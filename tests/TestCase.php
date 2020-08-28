<?php

namespace Tests;

use App\Auth\Token;
use App\Auth\Userprofile;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

use App\Auth\User;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * 创建虚拟测试用户并生成访问token
     */
    public function createTestUserToken() {
        $test_user_data = [
            "nick_name" => "test_account",
            "avatar" => "https://tfs.alipayobjects.com/images/partner/TB1QZRuXPyX81Jjme6jXXXs3pXa"
        ];
        $scope = "alipay";
        $user = User::createNew($test_user_data, $scope);

        // 生成应用token
        $expire = strtotime("+1day");
        $app_token = sprintf("%s_%s_%d", $scope, $user->id, $expire);
        $app_token = md5(base64_encode($app_token));

        $token_obj = Token::updateOrCreate([
            'user_id' => $user->id,
            'scope' => $scope
        ],
            [
                'expired' => $expire,
                'token' => $app_token
            ]);
        return $token_obj;
    }

    /**
     * 删除虚拟测试用户
     * @param int $user_id
     */
    public function eraseTestUser(int $user_id) {
        Token::where("user_id", $user_id)->delete();
        Userprofile::where("user_id", $user_id)->delete();
        User::destroy($user_id);
    }

    public function randomUser():User {
        //从uid最小的前10个用户里随机取
        $list = User::with("userprofile")->where("id", "!=", 1)->orderBy("id")->take(10)->get();
        return $list[array_rand($list->toArray())];

    }
}
