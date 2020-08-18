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
        $user_id = $request->session()->get("user_id");

        $user_obj = User::select('id', 'username')->find($user_id);
        $user_obj->userprofile;
//        dd($user_obj);

        return $this->infoJson($user_obj);

    }
}
