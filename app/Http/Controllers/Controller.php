<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\URL;
use App\Services\UserService;
use Jenssegers\Agent\Agent;


abstract class Controller extends BaseController
{
    use DispatchesJobs, ValidatesRequests;
    public $data = [];
    public $uid = 0;
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
        }
        $this->data['uid'] = $this->uid;
        $this->data['isLogin'] = $this->uid > 0;
        $url = Request::getRequestUri();
        if(starts_with($url,'/my') or starts_with($url,'/auth')){
            $this->data['isMy'] = true;
        }elseif(starts_with($url,'/train')){
            $this->data['isTrain'] = true;
        }else{
            $this->data['isFirst'] = true;
        }

        $this->data['previous'] =  URL::previous();

        $agent = new Agent();
        $this->isWechat = $agent->match('.*MicroMessenger.*');

        //$jssdk = new JSSDK();
        //$this->data['jssdk_token'] = $jssdk->getSignPackage();
    }
}
