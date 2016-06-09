<?php

namespace App\Http\Controllers\Admin;

use App\Services\StatusService;
use App\Services\UserService;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redis;

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
}
