<?php

namespace Tests\Unit\Favorite;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UndoUnitTest extends TestCase
{
    /**
     * 测试取消登录用户的收藏夹里的特定游戏.
     * 预期：提供token和游戏的gameId。
     * 如果token无效则报错。
     * 如果gameId无效则报错。
     * 如果favorite表里没有对应组合的数据则返回失败。
     * 如果已存在且state=0则返回失败。
     * 如果已存在但state=1，则更新并返回成功。
     *
     * @return void
     */
    public function testFavoriteUndo()
    {
        $token = "d6806ff77ed86c88c622042cec0fae6f";
        $game_id = 31;

        $response = $this->json("post", route("favorite.remove", compact("game_id")), compact("token"));
        $response->assertStatus(200);
//        dd($response);
        $this->assertArrayHasKey('status', $response->json());
        $this->assertEquals(1, $response->json()["status"], $response->json()["msg"]);

    }
}
