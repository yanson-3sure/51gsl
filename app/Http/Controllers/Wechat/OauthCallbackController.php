<?php

namespace App\Http\Controllers\Wechat;
//
//use App\Facades\Avatar;
use App\Http\Controllers\Controller;
use App\Services\AvatarService;
use App\Services\UserService;
use App\Services\WechatService;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Redirect;
use Jenssegers\Agent\Agent;
use Illuminate\Support\Facades\Input;

use App\User;
use App\Models\UserWechat;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

//
//
class OauthCallbackController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->service = new WechatService();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if($this->service->isWechat()) {
            if (Input::has('state') && Input::has('code')) {
                $app = $this->service->getApp();
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
                        if(!$savedir) $savedir = '';
                    }

                    $service = new UserService();
                    $user = $service->save(null,null,$nickname,$savedir);

                    $user_wechat = new UserWechat();
                    $user_wechat->unionid = $UNIONID;
                    $user_wechat->openid = $OPENID;
                    $user_wechat->uid =  $user['id'];
                    $user_wechat->create_at = Carbon::now();
                    $user_wechat->save();

                    Auth::loginUsingId($user['id'],config('base.auto_remember_me'));
                }  else {
                    $user_wechat = UserWechat::where('unionid',$UNIONID)->where('openid',$OPENID)->first();
                    if(!$user_wechat){
                        $user_wechat = new UserWechat();
                        $user_wechat->unionid = $UNIONID;
                        $user_wechat->openid = $OPENID;
                        $user_wechat->uid =  $users_wechat->uid;
                        $user_wechat->create_at = Carbon::now();
                        $user_wechat->save();
                    }
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
    public function callback()
    {
        $app = $this->service->getApp();
        // 从项目实例中得到服务端应用实例。
        $server = $app->server;
        $server->setMessageHandler(function ($message) {
//            switch ($message->MsgType) {
//                case 'event':
//                    # 事件消息...
//                    break;
//                case 'text':
//                    return '你好';
//                    break;
//                case 'image':
//                    # 图片消息...
//                    break;
//                case 'voice':
//                    # 语音消息...
//                    break;
//                case 'video':
//                    # 视频消息...
//                    break;
//                case 'location':
//                    # 坐标消息...
//                    break;
//                case 'link':
//                    # 链接消息...
//                    break;
//                // ... 其它消息
//                default:
//                    return '欢迎关注股思录';
//                    break;
//            }
            return '欢迎关注股思录';
            // ...
        });
        $server->serve()->send();
    }
}