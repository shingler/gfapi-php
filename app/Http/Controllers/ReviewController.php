<?php

namespace App\Http\Controllers;
use App\Http\Response\JsonResponse;
use App\Game\Shelf;
use App\Game\Review;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class ReviewController extends Controller
{
    use JsonResponse;

    public function list() {
        $gameId = Input::get("gameId", 0);
        if (!$gameId) {
            return $this->error("无效的请求");
        }
        $shelf = new Shelf();
        if (!$shelf->where('gameId', '=', $gameId)->first()) {
            return $this->error("该游戏不存在");
        }

        $review = new Review();
        $review_data_list = $review->where('gameId', '=', $gameId)->orderBy('id', 'desc')->get();

        return $this->listJson($review_data_list);

    }

    public function detail($id=0) {
        if (!$id) {
            return $this->error("无效的请求");
        }
        $review = new Review();
        $review_data = $review->where('id', '=', $id)->first();
        if (empty($review_data)) {
            return $this->error("该评测不存在");
        }
        // 处理评测的游戏主体封图
        $review_data->shelf->loadCoverUrl()->toArray();
        // 评测内容译文做按句换行
        $review_data->comment_trans = str_replace("。", "。".PHP_EOL, $review_data->comment_trans);
        return $this->infoJson($review_data);
    }
}
