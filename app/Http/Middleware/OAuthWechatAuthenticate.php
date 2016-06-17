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
use App\Services\WechatService;

/**
 * Class WechatOAuthAuthenticate
 */
class OAuthWechatAuthenticate
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
        $agent = new Agent();
        $isWechat = $agent->match('.*MicroMessenger.*');
        if($isWechat) {
            if(!Auth::check()) {
                session(['target_url'=>$this->getTargetUrl($request)]);
                $service = new WechatService();
                $app = $service->getApp();
                return $app->oauth->redirect();
            }
        }
        return $next($request);
    }
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
