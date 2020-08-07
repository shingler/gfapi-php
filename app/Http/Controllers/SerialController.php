<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Game\Serial;
use App\Http\Response\JsonResponse;

class SerialController extends Controller
{
    use JsonResponse;

    /**
     * 根据系列id获取游戏列表，可通过except排除某些游戏
     * @param Request $request ["except"=>游戏id的逗号连接字符串]
     * @param int $id 系列id
     * @return Json
     */
    public function games(Request $request, int $id) {
        $except = $request->get("except", "");
        $except = explode(',', $except);

        $serial_obj = Serial::select("id", "title")->find($id);
        $shelf_list = $serial_obj->getRelatedGames($except);
        $res = [
            "serial" => $serial_obj,
            "related_games" => $shelf_list
        ];

        return $this->infoJson($res);
    }
}
