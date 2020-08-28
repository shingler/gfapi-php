<?php
/**
 * 定义json格式的响应返回格式
 */
namespace App\Http\Response;


trait JsonResponse {

    /**
     * 列表类型的返回格式
     * @param $data 数据的数组格式
     * @return Json格式的输出
     */
    public function listJson($data) {
        return response()->json($data);
    }

    /**
     * 带分页的列表类型的返回格式
     * @param $data 数据的数组格式
     * @param $count 数据的总量，默认为0
     * @param $next_page 下一页的页码。默认为false，当值为false，返回的next的值也是false
     * @param $previous_page 上一页的页码。默认为false，当值为false，返回的previeous的值也是false
     * @return Json格式的输出
     */
    public function paginateJson($data, $count=0, $next_page=false, $previous_page=false) {
        $res = [
            "count" => $count,
            "next"=> $next_page ?? false ,
            "previous" => $previous_page ?? false,
            "results"=> $data
        ];

        return response()->json($res);
    }

    /**
     * 单条数据的返回格式
     */
    public function infoJson($data) {
        return response()->json($data);
    }

    public function success($msg, $data=[]) {
        $res = [
            "status" => 1,
            "msg" => $msg,
            "data" => $data
        ];

        return response()->json($res);
    }

    public function error($msg, $data=[]) {
        $res = [
            "status" => 0,
            "msg" => $msg,
            "data" => $data
        ];

        return response()->json($res);
    }
}
