<?php
/**
 * 检查token是否存在，并返回user_id
 * 注意：middleware不支持json_encode，必须使用response()->json($data)
 */
namespace App\Http\Middleware;

use Closure;
use App\Http\Response\JsonResponse;

class Token
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
        $token = $request->input("token", "");
        if (empty($token)) {
            return $this->error("token不能为空");
        }
        $token_obj = \App\Auth\Token::where('token', $token)->first();
        if (!$token_obj) {
            return $this->error("token已失效");
        }
        if ($token_obj->expired < now()->getTimestamp()) {
            return $this->error("认证信息过期，请重新登录");
        }
        $request->session()->flash("user_id", $token_obj->user_id);
        return $next($request);
    }
}
