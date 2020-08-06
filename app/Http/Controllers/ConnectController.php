<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

use App\Http\Response\JsonResponse;
use App\Auth\{Connect, User, Token};


class ConnectController extends Controller
{
    use JsonResponse;

    /**
     * 通过第三方认证信息返回应用token
     * @param Request $request => [platform, oauth_token, oauth_token_fresh,
     * oauth_expire, oauth_token_fresh_expire, oauth_data]
     * @return JsonResponse
     */
    public function authenticate(Request $request) {
        $auth_data = $request->all();

        try {
            $conn = Connect::authenticate($auth_data["platform"], $auth_data);
        } catch (\Exception $ex) {
            dd($ex);
            return $this->error($ex->getMessage());
        }

        if (!$conn->user_id) {
            // 创建新用户
            $user = User::createNew(json_decode($auth_data["oauth_data"], true), $auth_data["platform"]);
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
