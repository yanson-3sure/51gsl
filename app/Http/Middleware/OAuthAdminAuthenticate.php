<?php

namespace App\Http\Middleware;

use App\Services\UserService;
use EasyWeChat;
use Closure;
use Jenssegers\Agent\Agent;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cookie;


/**
 * Class OAuthAuthenticate
 */
class OAuthAdminAuthenticate
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
        if (!Auth::check()) {//非登录的情况下
            if ($request->ajax()) {
                return response('Unauthorized.', 401);
            } else {
                return redirect()->guest('auth/login');
            }
        }
        if(!isAdmin(Auth::user()->id)){
            if ($request->ajax()) {
                return response('Unauthorized.', 403);//权限不足
            }else{
                return redirect()->to('/');
            }
        }

        return $next($request);
    }
}
