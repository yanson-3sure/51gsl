<?php

namespace App\Http\Controllers\Admin;

use App\Services\UserService;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class TestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getRegUser()
    {
        $mobile = '13141469448';
        $password = '123456';
        $name = 'viky';
        $userService = new UserService();
        $result = $userService->save($mobile,$password,$name);
        dd($result);
    }

    public function getUser()
    {
        $uid = 1;
        $userService = new UserService();
        dd($userService->get($uid));
    }


}
