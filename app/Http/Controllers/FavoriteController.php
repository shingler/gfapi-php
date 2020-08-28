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

    /**
     * 我的收藏列表
     * @param Request $request
     * @return \App\Http\Response\Json格式的输出
     */
    public function my(Request $request) {
        $user_id = $request->session()->get("user_id");
        $list = Favorite::with("shelf")->where('user_id', $user_id)->where('state', 1)->orderByDesc('updated')->get();
        foreach ($list as $k => $item) {
            $list[$k]["shelf"] = $list[$k]["shelf"]->loadCoverUrl();
        }
        return $this->listJson($list);
    }

    /**
     * 检查用户是否收藏过某游戏
     * @param Request $request
     * @param int $game_id
     * @return JsonResponse
     */
    public function check(Request $request, int $game_id) {
        $user_id = $request->session()->get("user_id");
        $res = Favorite::where("user_id", $user_id)->where("shelf_id", $game_id)->first();
        $state = $res?$res->state:0;
        return $this->success("ok", ["state"=>$state]);
    }

    /**
     * 执行收藏
     * @param Request $request
     * @param int $game_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function do(Request $request, int $game_id) {
        $user_id = $request->session()->get("user_id");

        $fav_obj = Favorite::where('user_id', $user_id)->where('shelf_id', $game_id)->get()->first();
        if ($fav_obj and $fav_obj->state == 1) {
            return $this->error("已经收藏过了");
        }
        $res = [];
        if ($fav_obj) {
            $fav_obj->state = 1;
            $fav_obj->updated = now()->getTimestamp();
            $fav_obj->save();
            $res["state"] = $fav_obj->state;
        } else {
            $data = [
                "user_id" => $user_id,
                "shelf_id" => $game_id,
                "state" => 1,
                "created" => now()->getTimestamp(),
                "updated" => now()->getTimestamp()
            ];
            Favorite::create($data);
            $res["state"] = 1;
        }
        return $this->success("收藏成功", $res);
    }

    /**
     * 取消收藏
     * @param Request $request
     * @param int $game_id
     * @return \Illuminate\Http\JsonResponse
     */
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
