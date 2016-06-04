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
class OAuthAuthenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $role
     * @return mixed
     */
    public function handle($request, Closure $next, $role = null)
    {
        if (!Auth::check()) {//非登录的情况下
            if ($request->ajax()) {
                return response('Unauthorized.', 401);
            } else {
//                $agent = new Agent();
//                $isWechat = $agent->match('.*MicroMessenger.*');
//                if($isWechat) {
//                    $wechat = app('EasyWeChat\\Foundation\\Application');
//                    return $wechat->oauth->redirect();
//                    //return $wechat->oauth->scopes(['snsapi_userinfo'])->redirect(URL::to('/wechat/oauthcallback'));
//                }
                return redirect()->guest('auth/login');
            }
        }
        if($role) //登录并 设置规则的情况下
        {
            $userService = new UserService();
            $user = $userService->get(Auth::user()->id);
            if ($user && $user['role'] == $role) {
                return $next($request);
            } else {
                if ($request->ajax()) {
                    return response('Unauthorized.', 403);//权限不足
                }else{
                    return redirect()->to('/');
                }
            }
        }
        return $next($request);
    }

//    public  function set(){
//        $cookieName = Auth::getRecallerName();
//        if (Session::has('cookie_expiration') && Auth::check() && isset($_COOKIE[$cookieName])) {
//            // get the (current/new) cookie values
//            $cookieValue = Cookie::get($cookieName);
//            $expiration = Session::get('cookie_expiration');
//
//            // forget the session var
//            Session::forget('cookie_expiration');
//
//            // change the expiration time
//            $cookie = Cookie::make($cookieName, $cookieValue, $expiration);
//            return $response->withCookie($cookie);
//        }
//    }

    /**
     * Build the target business url.
     *
     * @param Request $request
     *
     * @return string
     */
    public function getTargetUrl($request)
    {
        $queries = array_except($request->query(), ['code', 'state']);

        return $request->url().(empty($queries) ? '' : '?'.http_build_query($queries));
    }
}
