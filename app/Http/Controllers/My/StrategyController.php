<?php

namespace App\Http\Controllers\My;

use App\Models\Strategy;
use App\Services\StrategyService;
use Illuminate\Http\Request;
use App\Services\OrderService;
use App\Services\UserService;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;

class StrategyController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->service = new StrategyService();
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('my.strategy.create',$this->data);
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
            'title'=>'required',
            'intro'=>'required',
            'content'=>'required',
            'risk'=>'required',
        ];
        $this->validate($request,$rules);
        $title = Input::get('title','');
        $intro = Input::get('intro','');
        $vip = Input::get('vip','');
        $content = Input::get('content','');
        $risk = Input::get('risk','');
        if($this->service->save($this->uid,$title,$vip,$intro,$content,$risk)){
            return ['result'=>'添加成功'];
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
