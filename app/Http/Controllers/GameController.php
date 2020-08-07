<?php

namespace App\Http\Controllers;

use App\Aliyunoss\Manager;
use App\Http\Response\JsonResponse;
use App\Game\Shelf;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;

class GameController extends Controller
{
    use JsonResponse;

    /** 游戏列表
     * @param $page
     * @param $show_detail
     * @return \App\Http\Response\Json格式的输出
     */
    function list() {
        $limit = 10;
//        $page = Input::get("page", 1);
        $db = new Shelf();
        $db = $db->where('show', '=', '1');
        $data = $db->orderBy('gameId', 'desc')->paginate($limit);

        $oss_manager = new Manager();
        // 补充数据
        foreach ($data as $key => $value) {
            // 获取游戏数据
            $value["subjects"] = $value->getSubject($value->officialGameIds);
            // 获取阿里云OSS远程地址
            $value->loadCoverUrl()->loadThumbUrl();
        }

        $count = $data->total();
        $next = $data->nextPageUrl();
        $previous = $data->previousPageUrl();

        return $this->paginateJson($data->toArray()["data"], $count, $next, $previous);

    }

    /**
     * 游戏详情数据
     */
    function info($id=0) {
        if (!$id) {
            return $this->error("无效的请求");
        }
        $db = new Shelf();
        $data = $db->where('gameId', '=', $id)->first();
        if (empty($data)) {
            return $this->error("数据不存在");
        }

        // 加载具体游戏sku信息
        $data["subjects"] = $data->getSubject($data->officialGameIds);

        // 处理图片地址转换
        $data->loadCoverUrl()->loadThumbUrl();
        $intro_data = json_decode($data->intro, true);
        // 尽量显示中文
        $data->intro = $intro_data["hk"] ?? $intro_data["trans"] ?? "";
        $data->intro = str_replace("<br>", PHP_EOL, $data->intro);

        // 总体评分美化
        $data->score = number_format($data->score, 1);

        return $this->infoJson($data);
    }
}
