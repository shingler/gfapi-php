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

    public function __construct() {
        $accessKeyId = env("OSS_ACCESS_KEY_ID");
        $accessKeySecret = env("OSS_ACCESS_KEY_SECRET");
        $endpoint = env("OSS_END_POINT");
        try {
            $this->ossClient = new OssClient($accessKeyId, $accessKeySecret, $endpoint);
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
     * @param $bucket_name bucket数组的key名
     * @return bucket对应的节点域名
     */
    public function getDomain($bucket_name):string {
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
     * @return 缩略图地址
     */
    public function getUrl($key, $bucket_name, $style_name = ""):string {
        $origin_url = $this->getOriginUrl($key, $bucket_name);
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
     * @return 图片url
     */
    public function getOriginUrl($key, $bucket_name):string {
        return sprintf("//%s/%s", $this->getDomain($bucket_name), $key);
    }

}
