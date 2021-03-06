<?php

namespace App\Http\Controllers\Admin;

use App\Services\AnalystService;
use App\Services\API\DyxcService;
use App\Services\AvatarService;
use App\Services\StatusService;
use App\Services\UserService;
use App\Services\WechatService;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;

class TestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getRegUser()
    {
        $mobile = '13141469450';
        $password = '123456';
        $name = 'viky50';
        $userService = new UserService();
        $result = $userService->save($mobile,$password,$name);
        dd($result);
    }

    public function getUser()
    {
        $uid = 4;
        $userService = new UserService();
        dd($userService->find($uid));
    }
    public function getUsers()
    {
        $userService = new UserService();
        dd($userService->gets([4,7]));
    }
    public function getTestCache()
    {
        $uid = 4;
        $userService = new UserService();
        $user = $userService->find($uid);
        dd($userService->getCacheModel($user));
    }

    public function getRedis()
    {
        $service = new StatusService();
        $service->loadProfileCache(9);
    }

    public function getGetStatus()
    {
        $service = new StatusService();
        //dd($service->getStatusMessage(7));

        dd($service->zrevrangebyscore('all_home',0,5));
    }
    public function getCacheModel()
    {
        $service = new StatusService();
        $model = $service->find(2);
        dd($service->getCacheModel($model));
    }

    public function getGets()
    {
        $service = new StatusService();
        dd($service->gets([100,1,2,4,3000]));
    }

    public function getUnique()
    {
        $arr = [1,2,3,4,5,1,2,6];
        dd(array_unique($arr));
    }
    public function getHmget()
    {
        $service = new UserService();
        dd($service->hmget(7,['id','avatar']));
    }
    public function getHmgets()
    {
        $service = new UserService();
        dd($service->hmgets([7,9],['id','avatar']));
    }

//    public function getHgets()
//    {
//        $service = new AnalystService();
//        dd($service->hgets([4,7],['feature','role_name']));
//    }

    public function getWechat()
    {
        $service = new WechatService();
        dd($service);
    }


    public function getStorage()
    {
        $type='status';
        $filename = $type.'/'.md5($type.date('U').str_random(10)).'.png';
        var_dump($filename);//Input::file('file')
        $path='/Applications/MAMP/htdocs/gusilu/master2/51gsl/public/uploads/status/2016/06/17/1466130271EHutPbGn93-66.png';
        $path = '/Users/viky/Pictures/281883ae48353724c0984fb12983d01c.jpg';
        dd(Storage::putFile($filename,$path));
    }

    public function getAvatar()
    {
        $headimgurl = 'http://wx.qlogo.cn/mmopen/zS5ibofBmCTyW2RUo81WCFLCnbMlAz2V5wGkicSFuVmsrmjruIetWGxk97micicrpDGHQ40NKoHJy1Q1EFpBpdORP2hj6tiaywWgG/0';
        $avatarService = new AvatarService();
        $result = $avatarService->saveWechat($headimgurl);
        return '<img src="'.getAvatar($result).'" >';
    }

    public function getDyxcOrder()
    {
        $service = new DyxcService();
        dd($service->getOrder('13141469448','13141469448'));
    }
}
