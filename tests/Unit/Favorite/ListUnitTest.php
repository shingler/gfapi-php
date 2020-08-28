<?php

namespace Tests\Unit\Favorite;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ListUnitTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testExample()
    {
        $token = "0adce167543e66844d0d7d695826c2fd";

        $response = $this->json("get", route("favorite.list"), compact('token'));
        $response->assertStatus(200);
        //检查已收藏的是否出现在列表里
        $json_data = $response->json();
        $this->assertArrayNotHasKey("status", $json_data, "列表报错：".json_encode($json_data));
        $this->assertNotCount(0, $json_data, "数组为空");
        $has = false;
        $expect_id = 31;
        foreach ($json_data as $item) {
            if ($item["shelf_id"] == $expect_id) {
                $has = true;
            }
        }
        $this->assertEquals(true, $has, "已收藏的未找到");
    }
}
