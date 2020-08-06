<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Response\JsonResponse;
use App\Auth\{Token, User};

class UserController extends Controller
{
    use JsonResponse;

    /**
     * 通过应用token获取用户信息
     */
    public function get(Request $request) {
        $app_token = $request->get("token", "");
        if (empty($app_token)) {
            return $this->error("无效的请求");
        }
        $token_obj = Token::where('token', $app_token)->first();
        if ($token_obj->expired < now()->getTimestamp()) {
            return $this->error("认证信息过期，请重新登录");
        }

        $user_obj = User::select('id', 'username')->find($token_obj->user_id);
        $user_obj->userprofile;
//        dd($user_obj);

        return $this->infoJson($user_obj);

    }
}
