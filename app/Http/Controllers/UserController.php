<?php

namespace App\Http\Controllers;

use App\Models\Train;
use App\Services\AnalystService;
use App\Services\FollowService;
use App\Services\UserService;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;

class UserController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->service = new UserService();
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $type = Input::get('type','');
        $this->data['type'] = $type;
        return view('user.index',$this->data);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if(!$id)abort(404);
        $cur_user = $this->service->get($id);
        if($cur_user && $cur_user['role']==1) {
            $type = Input::get('type');
            $this->data['cur_user'] = $cur_user;
            $analystServcie = new AnalystService();
            $this->data['analyst'] = $analystServcie->get($id);

            $followService = new FollowService();
            $this->data['isFollowing'] = $followService->isFollowing($this->uid,$id);
            if($type!='train') {
                $this->data['trains'] = Train::where('uid', $id)->count();
            }

            if($type=='train'){//培训
                $models = Train::where('uid',$id)->take(100)->get();
                $this->data['models'] = $models;
                if($models) {
                    $all_uid = [];
                    foreach ($models as $model) {
                        $all_uid[] = $model->uid;
                    }
                    $userService = new UserService();
                    $this->data['users'] = $userService->getAvatarAndName($all_uid);
                }
                return view('user.show_train',$this->data);
            }
            if($type=='strategy') {//策略
                return view('user.show_strategy',$this->data);
            }


            return view('user.show',$this->data);
        }
        return abort(404);
    }
}
