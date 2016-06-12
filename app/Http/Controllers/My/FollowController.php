<?php

namespace App\Http\Controllers\My;

use App\Services\FollowService;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use App\Services\UserService;

class FollowController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->service = new FollowService();
    }
    public function index()
    {
        $result = $this->service->getFollowing($this->uid,100);
        $this->data['following_users'] = $result;
        //dd($this->data);
        return view('my.follow.index',$this->data);
    }
    public function postFocus()
    {
        $fuserid = Input::get('fuserid',0);
        if($fuserid && $fuserid >0){
            //检查 是否是分析师
            $userService = new UserService();
            $fuser = $userService->get($fuserid);
            if($fuser && $fuser['role']==1){
                if($this->service->follow_user($this->uid,$fuserid)) {
                    return ['result'=>'success'];
                }
                return ['error'=>'已经关注'];
            }else{
                return ['error'=>'只能关注分析师哦'];
            }
        }else{
            return ['error'=>'请选择关注的分析师'];
        }
    }
    public function postUnFocus()
    {
        $fuserid = Input::get('fuserid',0);
        if($fuserid && $fuserid >0){
            //检查 是否是分析师
            $userService = new UserService();
            $fuser = $userService->get($fuserid);
            if($fuser && $fuser['role']==1){
                if($this->service->unfollow_user($this->uid,$fuserid)) {
                    return ['result'=>'success'];
                }
                return ['error'=>'没有关注'];
            }else{
                return ['error'=>'只能不关注分析师'];
            }
        }else{
            return ['error'=>'请选择不关注的分析师'];
        }
    }
}
