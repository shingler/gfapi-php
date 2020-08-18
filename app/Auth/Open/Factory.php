<?php
namespace App\Auth\Open;

class Factory
{
    public static $SUPPORT_PLATFORM = ["alipay", "wx"];

    public static function getOpenLoginHandler(string $platform) {
        if (!in_array($platform, self::$SUPPORT_PLATFORM)) {
            throw new \Exception("不支持的第三方登录平台");
        }
        switch ($platform) {
            case "alipay":
                return new Alipay();
        }
    }
}
