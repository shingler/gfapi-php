<?php
/**
 * 检查游戏id，字段兼容以下：game_id, shelf_id, gameId
 * 如果字段为空或无法查到则报错。
 */
namespace App\Http\Middleware;

use App\Game\Shelf;
use App\Http\Response\JsonResponse;
use Closure;
use phpDocumentor\Reflection\Types\Null_;

class GameId
{
    use JsonResponse;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $game_id = $request->game_id;

        if (empty($game_id)) {
            return $this->error("游戏ID不能为空");
        }
        $shelf_obj = Shelf::find($game_id);
        if (!$shelf_obj instanceof Shelf) {
            return $this->error("游戏信息无效");
        }

        return $next($request);
    }
}
