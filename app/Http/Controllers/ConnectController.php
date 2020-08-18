<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

use App\Http\Response\JsonResponse;
use App\Auth\{Connect, User, Token};
use App\Auth\Open\Factory as OpenFactory;


class ConnectController extends Controller
{
    use JsonResponse;

    /**
     * 通过第三方oauth code返回应用token
     * @param Request $request => [platform, auth_code]
     * @return JsonResponse
     */
    public function authenticate(Request $request) {
        $platform = $request->get("platform", "alipay");
        $auth_code = $request->get("auth_code", "");
        if (empty($auth_code)) {
            return $this->error("auth_code为空");
        }

        // 根据auth_code获取第三方平台access_token
        try {
            $handler = OpenFactory::getOpenLoginHandler($platform);
        } catch (\Exception $ex) {
            return $this->error($ex->getMessage());
        }
        $token_result = $handler->getAccessToken($auth_code);
        $auth_data = [
            "platform" => $platform,
            "oauth_token" => $token_result->access_token,
            "oauth_token_fresh" => $token_result->refresh_token,
            "oauth_expire" => $token_result->expires_in,
            "oauth_token_fresh_expire" => $token_result->re_expires_in,
            "oauth_platform_user_id" => $token_result->user_id
        ];

        // 是否存在数据
        try {
            $conn = Connect::authenticate($platform, $auth_data);
        } catch (\Exception $ex) {
            dd($ex);
            return $this->error($ex->getMessage());
        }

        if (!$conn->user_id) {
            // 创建新用户
            $oauth_user_data = $handler->getUserData($auth_data["oauth_token"]);
            $user = User::createNew($oauth_user_data, $platform);
            $conn->user_id = $user->id;
            $conn->save();
        } elseif ($conn->oauth_expire < now()->getTimestamp()) {
            // 更新token信息
            $conn->oauth_token = $auth_data["oauth_token"];
            $conn->oauth_token_fresh = $auth_data["oauth_token_fresh"];
            $conn->oauth_expire = $auth_data["oauth_expire"];
            $conn->oauth_token_fresh_expire = $auth_data["oauth_token_fresh_expire"];
            $conn->save();
        }
        // 生成应用token
        $expire = strtotime("+1day");
        $app_token = sprintf("%s_%s_%d", $auth_data["platform"], $conn->user_id, $expire);
        $app_token = md5(base64_encode($app_token));

        Token::updateOrCreate([
                'user_id' => $conn->user_id,
                'scope' => $auth_data["platform"]
            ],
            [
                'expired' => $expire,
                'token' => $app_token
            ]);

        return $this->success("登录成功", ["token" => $app_token]);

    }
}
