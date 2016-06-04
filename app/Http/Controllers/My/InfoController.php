<?php

namespace App\Http\Controllers\My;

use App\Models\Application;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Services\UserService;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;

class InfoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $apply = Application::where('uid',$this->uid)->first();
        if($apply && ($apply->status==0 || $apply->status==1)){
            $this->data['auditing'] = true;
        }
        $userService = new UserService();
        $user = $userService->find($this->uid);
        $this->data['mobile'] = $user->mobile;
        return view('my.info.index',$this->data);
    }

    public function postChgName(Request $request)
    {
        $rules = [
            'name'=>'required|min:2|max:10|unique:users',
        ];
        $this->validate($request,$rules);
        $name = Input::get('name');
        $userService = new UserService();
        $user = $userService->chgNameById($this->uid,$name);
        if($user){
            return ['result'=>'success'];
        }elseif($user===false){
            return response('昵称跟之前相同',501);
        }else{
            return response('更新失败',501);
        }
    }


    public function getBindMobile()
    {
        return view('my.info.bind_mobile');
    }
    public function postBindMobile(Request $request)
    {
        $rules = [
            'mobile'=>'required|regex:/^1[34578][0-9]{9}$/|unique:users',
            'captcha'=>'required|sms',
        ];
        $this->validate($request,$rules);
        $mobile = Input::get('mobile');
        $userService = new UserService();
        if($userService->bindMobile($this->uid,$mobile)) {
            return ['result'=>'success','intended' => Redirect::intended('/')->getTargetUrl()];
        }else{
            return response('更新失败', 501);
        }
    }


    public function getModifyPwd()
    {
        $userService = new UserService();
        $user = $userService->find($this->uid);
        if(!$user->mobile){
            return redirect()->to('/my/bind-mobile');
        }
        return view('my.info.modify_pwd',['mobile'=>$user->mobile]);
    }
    public function postModifyPwd(Request $request)
    {
        $rules = [
            'captcha'=>'required|sms',
            'password'=>'required|min:6|confirmed',
            'password_confirmation'=>'required|min:6',
        ];
        $this->validate($request,$rules);

        $password = Input::get('password');
        $service = new UserService();
        $user = $service->chgPasswordById($this->uid,$password);
        if($user){
            return ['result'=>'success','intended' => Redirect::intended('/')->getTargetUrl()];
        }else{
            return response('更新失败', 501);
        }
    }


}
