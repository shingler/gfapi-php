<?php
/**
 * 操作收藏
 */
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Game\Favorite;
use App\Http\Response\JsonResponse;

class FavoriteController extends Controller
{
    use JsonResponse;

    public function my(Request $request) {
        $user_id = $request->session()->get("user_id");
        $list = Favorite::with("shelf")->where('user_id', $user_id)->where('state', 1)->get();
        return $this->listJson($list);
    }

    public function do(Request $request, int $game_id) {
        $user_id = $request->session()->get("user_id");

        $fav_obj = Favorite::where('user_id', $user_id)->where('shelf_id', $game_id)->get()->first();
        if ($fav_obj and $fav_obj->state == 1) {
            return $this->error("已经收藏过了");
        }
        if ($fav_obj) {
            $fav_obj->state = 1;
            $fav_obj->updated = now()->getTimestamp();
            $fav_obj->save();
        } else {
            $data = [
                "user_id" => $user_id,
                "shelf_id" => $game_id,
                "state" => 1,
                "created" => now()->getTimestamp(),
                "updated" => now()->getTimestamp()
            ];
            Favorite::create($data);
        }
        return $this->success("收藏成功", $data);
    }

    public function undo(Request $request, int $game_id) {
        $user_id = $request->session()->get("user_id");

        $fav_obj = Favorite::where("user_id", $user_id)->where("shelf_id", $game_id)->get()->first();
        if (!$fav_obj) {
            return $this->error("还没有收藏这个游戏");
        }
        if ($fav_obj->state == 0) {
            return $this->error("这个游戏已经取消收藏了");
        }
        $fav_obj->state = 0;
        $fav_obj->updated = now()->getTimestamp();
        $fav_obj->save();

        return $this->success("取消成功", $fav_obj);
    }
}
