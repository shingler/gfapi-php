<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Response\JsonResponse;
use App\Auth\{Token, User, Userprofile};
use App\Aliyunoss\Manager;

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

    /**
     * 修改用户名。新用户名不能为空。新用户名不能存在
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function changename(Request $request) {
        $new_name = $request->input("new_username", "");
        if (empty($new_name)) {
            return $this->error("新的用户名不能为空");
        }
        $count = User::where("username", $new_name)->count();
        if ($count > 0) {
            return $this->error("该用户名已存在");
        }
        $user_id = $request->session()->get("user_id");
        $user_obj = User::find($user_id);
        $user_obj->username = $new_name;
        $user_obj->save();

        $user_info_obj = Userprofile::where("user_id", $user_id)->first();
        $user_info_obj->nickname = $new_name;
        $user_info_obj->save();

        return $this->success("修改成功");
    }

    public function avatar_sign(Request $request) {
        $ossManager = new Manager();
        $sign = $ossManager->getDirectUploadSign("avatar", "avatar");
        return $this->success("ok", $sign);
    }
}
