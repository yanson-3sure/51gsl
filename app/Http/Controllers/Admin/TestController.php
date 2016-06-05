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


}
