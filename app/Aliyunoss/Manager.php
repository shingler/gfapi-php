<?php
/**
 * 阿里云OSS操作类
 */

namespace App\Aliyunoss;

require_once env("APP_ROOT")."/vendor/aliyuncs/oss-sdk-php/autoload.php";

use OSS\OssClient;
use OSS\Core\OssException;

use App\Aliyunoss\OssException as App_OssException;
use Config;


class Manager
{
    protected $ossClient = null;
    private $accessKeyId;
    private $accessKeySecret;

    public function __construct() {
        $this->accessKeyId = env("OSS_ACCESS_KEY_ID");
        $this->accessKeySecret = env("OSS_ACCESS_KEY_SECRET");
        $endpoint = env("OSS_END_POINT");
        try {
            $this->ossClient = new OssClient($this->accessKeyId, $this->accessKeySecret, $endpoint);
        } catch (OssException $ex) {
            printf($ex->getMessage());
        }
    }

    /**
     * 根据用途获取bucket
     * @param $bucket_name bucket数组的key名
     * @return bucket对应的oss里的bucket名称
     **/
    public function getBucket($bucket_name = 'cover'):string {
        if (array_key_exists($bucket_name, OSS_BUCKETNAME)) {
            return Config::get("aliyunoss.OSS_BUCKETNAME")[$bucket_name]["bucket"];
        } else {
            return "";
        }
    }

    /**
     * 根据bucket名获取节点域名
     * @param string $bucket_name bucket数组的key名
     * @return string bucket对应的节点域名
     */
    public function getDomain(string $bucket_name):string {
        if (array_key_exists($bucket_name, Config::get("aliyunoss.OSS_BUCKETNAME"))) {
            return Config::get("aliyunoss.OSS_BUCKETNAME")[$bucket_name]["domain"];
        } else {
            return "";
        }
    }

    /**
     * 根据bucket名读取样式
     * @param $bucket_name bucket数组的key名
     * @return 样式数组
     */
    public function getStyle($bucket_name):array {
        if (array_key_exists($bucket_name, Config::get("aliyunoss.OSS_BUCKETNAME"))) {
            return Config::get("aliyunoss.OSS_BUCKETNAME")[$bucket_name]["style"];
        } else {
            return [];
        }
    }

    /**
     * 由id和src生成远端代码名
     * 格式：/shelf_id/md5(不带扩展名的src路径)[0:8].扩展名
     * @param $shelf_id 游戏仓库id，用来做路径前缀
     * @param $src 图片相对路径
     * @return 生成远程key返回
     */
    public function getRemoteKey($shelf_id, $src):string {
        $path_info = pathinfo($src);
        $fname = $path_info["filename"];
        $extname = $path_info["extension"];
        $str_md5 = md5($fname);

        $dest = sprintf("%s/%s%s", $shelf_id, substr($str_md5, 0, 8), $extname);
        return $dest;
    }

    /**
     * 指定key是否已存在
     * @param $bucket_name bucket数组的key名
     * @param $key 远程文件名
     * @return 布尔值
     * @throws 不存在的bucket异常
     */
    public function isExist($bucket_name, $key):bool {
        $bucket = $this->getBucket($bucket_name);
        if (empty($bucket)) {
            throw new App_OssException("不存在的bucket");
        }

        try {
            $exist = $this->ossClient->doesObjectExist($bucket, $key);
        } catch (OssException $ex) {
            printf($ex->getMessage());
            $exist = false;
        }

        return $exist;
    }

    /**
     * 获取缩略图地址
     * @param $key 远程文件名
     * @param $bucket_name bucket数组的key名
     * @param $style_name 样式名称
     * @param $protocal 访问协议。默认为空，自适应
     * @return 缩略图地址
     */
    public function getUrl($key, $bucket_name, $style_name = "", $protocal = ""):string {
        $origin_url = $this->getOriginUrl($key, $bucket_name, $protocal);
        if (in_array($style_name,$this->getStyle($bucket_name))) {
            return sprintf("%s?x-oss-process=style/%s", $origin_url, $style_name);
        } else {
            return $origin_url;
        }
    }

    /**
     * 获取原图地址
     * @param $key 远程文件名
     * @param $bucket_name bucket数组的key名
     * @param $protocal 访问协议。默认为空，自适应
     * @return 图片url
     */
    public function getOriginUrl($key, $bucket_name, $protocal):string {
        if (strpos($key, "http") !== false) {
            return $key;
        }
        if (in_array($protocal, ["http", "https"])) {
            $protocal .= ":";
        }
        return sprintf("%s//%s/%s", $protocal, $this->getDomain($bucket_name), $key);
    }

    /**
     * 获取直传签名
     * @param string $bucket_name bucket数组的key
     * @param string $prefix 上传的目录名
     * @return array
     */
    public function getDirectUploadSign(string $bucket_name, string $prefix=""):array {
        $dir = $prefix;
        $id = env("OSS_ACCESS_KEY_ID");
        $key = env("OSS_ACCESS_KEY_SECRET");
        $host = $this->getDomain($bucket_name);

        $now = time();
        $expire = 30;  //设置该policy超时时间是10s. 即这个policy过了这个有效时间，将不能访问。
        if (env("APP_ENV") == "local") {
            //docker环境时间差别很大，放大到7天
            $expire = 86400*7;
        }
        $end = $now + $expire;
        $expiration = self::gmt_iso8601($end);

        $start = array(0=>'starts-with', 1=>'$key', 2=>$dir);
        $conditions[] = $start;


        $arr = array('expiration'=>$expiration,'conditions'=>$conditions);
        $policy = json_encode($arr);
        $base64_policy = base64_encode($policy);
        $string_to_sign = $base64_policy;
        $signature = base64_encode(hash_hmac('sha1', $string_to_sign, $key, true));

        $response = array();
        $response['accessid'] = $id;
        $response['host'] = "https://".$host;
        $response['policy'] = $base64_policy;
        $response['signature'] = $signature;
        $response['expire'] = $end;
        $response['dir'] = $dir;  // 这个参数是设置用户上传文件时指定的前缀。
        return $response;
    }

    public static function gmt_iso8601($time) {
        $dtStr = date("c", $time);
        $mydatetime = new \DateTime($dtStr);
        $expiration = $mydatetime->format(\DateTime::ISO8601);
        $pos = strpos($expiration, '+');
        $expiration = substr($expiration, 0, $pos);
        return $expiration."Z";
    }

}
