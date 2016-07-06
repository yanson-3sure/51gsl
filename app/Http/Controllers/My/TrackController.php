<?php

namespace App\Http\Controllers\My;

use App\Models\Strategy;
use App\Models\Track;
use App\Services\TrackService;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;

class TrackController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->service = new TrackService();
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $id = Input::get('id');
        $strategy = Strategy::find($id);
        //是否是当前用户的,或者当前操作者是管理员
        if($strategy) {
            $role = isAdmin($this->uid);
            if (!$role) {
                $role = $strategy->uid == $this->uid;
            }
            if(!$role) {
                return response('无权限',501);
            }
            $this->data['strategy'] = $strategy;
            return view('my.track.create',$this->data);
        }
        return response('没有此对应的策略',501);
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
            'content'=>'required',
        ];
        $this->validate($request,$rules);
        $strategy_id = Input::get('strategy_id');
        $strategy = Strategy::find($strategy_id);
        //是否是当前用户的,或者当前操作者是管理员
        if($strategy) {
            $role = isAdmin($this->uid);
            if (!$role) {
                $role = $strategy->uid == $this->uid;
            }
            if(!$role) {
                return response('无权限',501);
            }
            if($this->service->save($strategy_id,Input::get('status'),Input::get('content'),$this->uid,Input::get('image'))){
                return ['result'=>'添加成功'];
            }
        }
        return response('添加失败',501);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
