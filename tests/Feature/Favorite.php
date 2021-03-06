<?php
/**
 * 测试收藏相关功能
 * 1.创建虚拟测试用户并生成访问token
 * 2.从数据库随机读取一个游戏id
 * 3.访问接口1，判断是否已收藏该游戏
 * 4.访问接口2，收藏该游戏
 * 5.访问接口1，判断是否已收藏该游戏
 * 6.访问接口4，查看收藏列表里是否存在该游戏
 * 7.访问接口3，取消收藏该游戏
 * 8.访问接口1，判断是否已取消收藏该游戏
 * 9.删除虚拟测试用户
 */
namespace Tests\Feature;

use App\Game\Shelf;
use App\Game\Favorite as FavoriteModel;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class Favorite extends TestCase
{
    private $token_obj;

    // 1.每次创建一个新的测试账户，进行操作。操作之后清理数据，保证隔离性。
    public function setUp() {
        echo PHP_EOL.PHP_EOL."starting set up...".PHP_EOL;
        parent::setUp(); // TODO: Change the autogenerated stub
        $this->token_obj = parent::createTestUserToken();
        echo PHP_EOL.PHP_EOL."set up finish".PHP_EOL;
    }

    // 9.删除虚拟测试用户
    public function tearDown() {
        echo PHP_EOL.PHP_EOL."starting tear down...".PHP_EOL;
        $this->eraseFavorite($this->token_obj->user_id);
        $this->eraseTestUser($this->token_obj->user_id);
        parent::tearDown(); // TODO: Change the autogenerated stub
        echo PHP_EOL.PHP_EOL."tear down finish.".PHP_EOL;
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testFavorite()
    {
        // 2.获取随机游戏id
        $game_id = $this->getRandomGameId();
        $this->assertNotEmpty($game_id, "获取随机游戏失败");

        $token_obj = $this->token_obj;

        // 3.新注册的用户应该不会收藏该游戏
        $this->assertFalse($this->checkFavorited($game_id, $token_obj->token));
        // 4.点击收藏
        $this->assertTrue($this->doFavorite($game_id, $token_obj->token));
        //5. 判断是否已收藏该游戏
        $this->assertTrue($this->checkFavorited($game_id, $token_obj->token));
        //6. 查看收藏列表里是否存在该游戏
        $this->assertContains($game_id, $this->getFavoriteList($token_obj->token));
        //7. 取消收藏该游戏
        $this->assertFalse($this->undoFavorite($game_id, $token_obj->token));
        //8.判断是否已取消收藏该游戏
        $this->assertFalse($this->checkFavorited($game_id, $token_obj->token));

    }


    public function getRandomGameId() {
        $list = Shelf::select("gameId")->where("show", 1)->orderByDesc("gameId")->get()->toArray();
        $rand_game = array_random($list);
        $rand_game_id = $rand_game["gameId"];
        return $rand_game_id;
    }

    public function checkFavorited($game_id, $token) {
        $response = $this->json("get", route("favorite.check", compact('game_id')), compact('token'));
        $response->assertStatus(200);
        $result = $response->json();
        $this->assertEquals(1, $result["status"], $result["msg"]);
        return boolval($result["data"]["state"]);

    }

    public function doFavorite($game_id, $token) {
        $response = $this->json("post", route("favorite.add", compact('game_id')), compact("token"));
        $response->assertStatus(200);
        $result = $response->json();
        $this->assertEquals(1, $result["status"], $result["msg"]);
        return boolval($result["data"]["state"]);
    }

    public function undoFavorite($game_id, $token) {
        $response = $this->json("post", route("favorite.remove", compact('game_id')), compact("token"));
        $response->assertStatus(200);
        $result = $response->json();
        $this->assertEquals(1, $result["status"], $result["msg"]);
        return boolval($result["data"]["state"]);
    }

    public function getFavoriteList($token) {
        $response = $this->json("get", route("favorite.list"), compact('token'));
        $response->assertStatus(200);
        $result = $response->json();
        $this->assertArrayNotHasKey('status', $result, json_encode($result));
        $game_ids = array_map(function($item){
            return $item["shelf_id"];
        }, $result);
//        var_dump($game_ids);
        return $game_ids;
    }

    /**
     * 清理测试账户在功能测试过程中的残留数据
     * @param $user_id
     */
    public function eraseFavorite(int $user_id) {
        FavoriteModel::where("user_id", $user_id)->delete();
    }

}
