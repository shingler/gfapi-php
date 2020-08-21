<?php

namespace Tests\Unit\User;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ChangeUsernameUnitTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testExample()
    {
        $token = "0adce167543e66844d0d7d695826c2fd";
        $new_username = "测试账号112";

        // 修改用户名
        $response = $this->json("post", route("user.changename"), compact('token', 'new_username'));
        $response->assertStatus(200, "状态码报错啦");
        $this->assertEquals(1, $response->json()["status"], "修改不成功：".$response->json()["msg"]);

        // 检查用户名
        $response = $this->json("get", route("user.get"), compact('token'));
        $response->assertStatus(200);
        $this->assertEquals($new_username, $response->json()["username"], "用户名修改不成功");
        $this->assertEquals($new_username, $response->json()["userprofile"]["nickname"], "profile表昵称修改不成功");
    }
}
