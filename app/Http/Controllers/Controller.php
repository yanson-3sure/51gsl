<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\URL;
use App\Services\UserService;
use Jenssegers\Agent\Agent;


abstract class Controller extends BaseController
{
    use DispatchesJobs, ValidatesRequests;
    public $data = [];
    public $uid = 0;
    public $name = '';
    public $role = 0;
    public $avatar = '';
    public $user = [];
    public $service;
    public $isWechat = false;

    public function __construct()
    {
        if(Auth::check()){
            $userService = new UserService();
            $this->uid = Auth::user()->id;
            $this->user = $userService->get($this->uid);
            $this->data['user'] = $this->user;
            $this->role = $this->user['role'];
            $this->avatar = $this->user['avatar'];
            $this->name = $this->user['name'];
        }
        $this->data['uid'] = $this->uid;
        $this->data['name'] = $this->name;
        $this->data['role'] = $this->role;
        $this->data['avatar'] = $this->avatar;
        $this->data['isLogin'] = $this->uid > 0;
        $url = Request::getRequestUri();
        if(starts_with($url,'/my') or starts_with($url,'/auth')){
            $this->data['footer_isMy'] = true;
        }elseif(starts_with($url,'/train')){
            $this->data['footer_isTrain'] = true;
        }elseif(starts_with($url,'/strategy')){
            $this->data['footer_isStrategy'] = true;
        }else{
            $this->data['footer_isFirst'] = true;
        }

        $this->data['previous'] =  URL::previous();

        $agent = new Agent();
        $this->isWechat = $agent->match('.*MicroMessenger.*');
        $this->data['isWechat'] = $this->isWechat;

        //$jssdk = new JSSDK();
        //$this->data['jssdk_token'] = $jssdk->getSignPackage();
    }

    protected function debug($admin=true)
    {
        if(($admin && isAdmin($this->uid)) || !$admin) {
            if(Input::get(config('base.debug.name'))==config('base.debug.value')) {
                dd($this->data);
            }
        }
    }
}
