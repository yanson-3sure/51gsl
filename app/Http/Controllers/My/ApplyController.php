<?php

namespace App\Http\Controllers\My;

use App\Models\Application;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;

class ApplyController extends Controller
{
    public function index()
    {
        if(!$this->user['avatar']){
            $this->data['error'] = '请先设置头像';
            $this->data['error_url'] = '/my/info';
        }elseif($this->user['role']!=0){
            $this->data['error'] = '您无权限';
            $this->data['error_url'] = '/my';
        }else{
            $applyication= Application::where('uid',$this->uid)->first();
            if($applyication){
                $this->data['applyication'] = $applyication;
                return view("my.apply.show",$this->data);
            }
        }
        return view('my.apply.create',$this->data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = [
            'id_number'=>['required','regex:/(^\d{15}$)|(^\d{17}([0-9]|X)$)/i'],
            'securities_certificate'=>'required|min:4',
            'role_name'=>'required|min:5',
            'feature'=>'required|min:4',
        ];
        $this->validate($request,$rules);
        //检查 之前 是否提交 过申请
        if($this->user['role']!=0){
            return response('您没有权限申请',501);
        }
        $apply = new Application();
        $apply->uid = $this->uid;
        $apply->status = 0;
        $apply->id_number = Input::get('id_number');
        $apply->securities_certificate = Input::get('securities_certificate');
        $apply->role_name = Input::get('role_name');
        $apply->feature = Input::get('feature');

        if($apply->save()) {
            return ['result' => 'success'];
        }
        return response('更新失败',501);
    }
}
