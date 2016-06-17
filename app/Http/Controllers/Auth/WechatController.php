<?php

namespace App\Http\Controllers\Auth;


use App\Http\Controllers\Controller;
use App\Services\AvatarService;
use App\Services\UserService;
use App\Services\WechatService;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Input;

use App\User;
use App\Models\UserWechat;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

use Laravel\Socialite\Facades\Socialite;

class WechatController extends Controller
{
    public function callback()
    {
        $service = new WechatService();
        if($service->isWechat()) {
            if (Input::has('state') && Input::has('code')) {
                $app = $service->getApp();
                $tempuser = $app->oauth->user();
                $OPENID = $tempuser->id;
                $oauthUser = $app->user->get($OPENID);
                $UNIONID = isset($oauthUser['unionid']) ? $oauthUser['unionid'] : '';
                if(!$UNIONID) {
                    dd('请先将公众号/服务号加入到微信开放平台');
                }
                $users_wechat = UserWechat::where('unionid','=',$UNIONID)->first();
                if(!$users_wechat) {
                    $nickname = $oauthUser['nickname'] ? $oauthUser['nickname'] :$tempuser['nickname'] ;
                    $user = User::where('name',$nickname)->first();
                    if($user){
                        $nickname = $nickname.'_'.str_random(6);
                    }
                    $headimgurl = $oauthUser['headimgurl'] ? $oauthUser['headimgurl'] :$tempuser['avatar'];
                    $savedir = '';
                    if($headimgurl){
                        $avatarService = new AvatarService();
                        $savedir = $avatarService->saveWechat($headimgurl);
                    }

                    $service = new UserService();
                    $user = $service->save(null,null,$nickname,$savedir);

                    $user_wechat = new UserWechat();
                    $user_wechat->unionid = $UNIONID;
                    $user_wechat->uid =  $user['id'];
                    $user_wechat->create_at = Carbon::now();
                    $user_wechat->save();

                    Auth::loginUsingId($user['id'],config('base.auto_remember_me'));
                }  else {
                    $user = User::find($users_wechat->uid);
                    if(!$user){
                        Log::error('unionid='.$UNIONID . '获取用户失败');
                        dd('异常');
                    }
                    Auth::loginUsingId($users_wechat->uid,config('base.auto_remember_me'));
                }
                $targetUrl = empty($_SESSION['target_url']) ? '/' : $_SESSION['target_url'];
                return  Redirect::to($targetUrl);
            }
        }else{
            dd('Permission denied');
        }
    }
    public function anyWebLogin()
    {
        return Socialite::with('weixin')->redirect();
    }
    public function anyWebLoginCallback()
    {
        $oauthUser = Socialite::with('weixin')->user();

        var_dump($oauthUser->getId());
        var_dump($oauthUser->getNickname());
        var_dump($oauthUser->getName());
        var_dump($oauthUser->getEmail());
        var_dump($oauthUser->getAvatar());
    }
}
