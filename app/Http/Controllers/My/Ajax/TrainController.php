<?php

namespace App\Http\Controllers\My\Ajax;

use App\Models\Train;
use App\Services\AnalystService;
use App\Services\OrderService;
use App\Services\TrainService;
use App\Services\UserService;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;

class TrainController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->service = new TrainService();
    }

    public function index()
    {
        $page = config('base.page_size');
        //两类,一类,分析师自己的,另一类,我订阅的,所有的
        $type = Input::get('type');
        $max = Input::get('max',0);
        //分析师自己的
        if($type==1){
            $select = Train::where('uid',$this->uid);
        }else{
            $orderServcie = new OrderService();
            $this->data['orderUserIds'] = $orderServcie->getUserIds($this->uid);
            if(!$this->data['orderUserIds']){
                if(ajax()) {
                    return ['max' => 0, 'isMore' => false, 'content' => ''];
                }
                return '';
            }
            $select = Train::whereIn('uid',$this->data['orderUserIds']);
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

        return ajaxView('my.ajax.train.list',$this->data);
    }
}
