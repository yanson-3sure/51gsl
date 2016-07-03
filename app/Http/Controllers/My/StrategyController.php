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
        $page = 10;
        //两类,一类,分析师自己的,另一类,我订阅的,所有的
        $type = Input::get('type');
        $max = Input::get('max',0);
        //分析师自己的
        if($type==1){
            $select = Strategy::where('uid',$this->uid);
        }else{
            $orderServcie = new OrderService();
            $this->data['orderUserIds'] = $orderServcie->getUserIds($this->uid);
            if(!$this->data['orderUserIds']){
                if(ajax()) {
                    return ['max' => 0, 'isMore' => false, 'content' => ''];
                }
                return '';
            }
            $select = Strategy::whereIn('uid',$this->data['orderUserIds']);
        }
        if($max){
            $select->where('created_at','<',date('Y-m-d H:i:s',$max));
        }
        $select->orderBy('id','desc');
        $models = $select->take($page+1)->get();
        if(count($models)==$page+1){
            $this->data['isMore'] = true;
            $models->pop();
        }else{
            $this->data['isMore'] = false;
        }
        $this->data['max'] = strtotime(last($models->all())['created_at']);
        $this->data['models'] = $models;
        $all_uid = [];
        foreach($models as $model){
            $all_uid[] = $model->uid;
        }
        $userService = new UserService();
        $this->data['users'] = $userService->getAvatarAndName($all_uid);
        if(ajax()) {
            return ['max' => $this->data['max'], 'isMore' => $this->data['isMore'], 'content' => view('my.strategy.common.list', $this->data)->render()];
        }
        return dd($this->data);
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
