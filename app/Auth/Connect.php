<?php

namespace App\Auth;

use Illuminate\Database\Eloquent\Model;

class Connect extends Model
{
    protected $table = "connect";
    protected $primaryKey = "id";
    public $timestamps = false;

    public static $_SCOPE = ['wx', 'alipay'];
    public $guarded = ['id'];

    public static function access_token(string $auth_code, string $platform) {

    }

    /**
     * 通过第三方平台的oauth_token获取应用token
     * @param string $scope 第三方平台名
     * @param array $auth_data 第三方平台的token信息（微信使用openid）
     * @return Connect|bool
     * @throws \Exception 不支持的第三方平台
     */
    public static function authenticate(string $scope, array $auth_data) {
        if (!in_array($scope, self::$_SCOPE)) {
            throw new \Exception("不支持的第三方登录平台");
        }

        $create_data = [
            'oauth_key' => env('CONNECT_ALIPAY_APP_ID'),
            'oauth_token' => $auth_data["oauth_token"],
            'oauth_token_fresh' => $auth_data["oauth_token_fresh"]??"",
            'oauth_expire' => $auth_data["oauth_expire"]??"",
            'oauth_token_fresh_expire' => $auth_data["oauth_token_fresh_expire"]??"",
            'created' => now()->getTimestamp(),
            'oauth_data' => ""
        ];
        if ($scope == "wx") {
            $create_data["oauth_key"] = env("CONNECT_WX_APP_ID");
        }

        return self::firstOrNew([
            'oauth_platform' => $scope,
            'oauth_platform_user_id' => $auth_data["oauth_platform_user_id"]
        ], $create_data);
    }
}
