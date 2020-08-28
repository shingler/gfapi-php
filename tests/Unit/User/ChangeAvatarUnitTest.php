<?php

namespace Tests\Unit\User;

use App\Aliyunoss\Manager;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ChangeAvatarUnitTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testChangeAvatar()
    {
        $prefix = "avatar";
        // 随机一个用户
        $user = parent::randomUser();
        // 更新头像
        $new_avatar = $user->updateAvatar();
        $this->assertNotEmpty($new_avatar);
        $this->assertContains(strval(now()->getTimestamp()), $new_avatar, "新的头像key不包含当前时间戳");
        // 新头像能访问
        var_dump($new_avatar);
        $response = $this->withHeaders(["Content-type: image/jpg"])->get($new_avatar);
        $response->assertStatus(200, "文件不存在");

    }
}
