<?php

namespace App\Http\Middleware;

use Closure;

class CheckLogin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // 获取上一次访问的地址
        $httpReferer = $_SERVER['HTTP_REFERER'];
        $member = $request->session()->get('member','');
        if($member == '') {
            return redirect('/login?return_url='.urlencode($httpReferer));
        }
        return $next($request);
    }
}
