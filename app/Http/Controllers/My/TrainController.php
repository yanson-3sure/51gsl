<?php

namespace App\Http\Controllers\My;

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
        $page = 10;
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
        if(ajax()) {
            return ['max' => $this->data['max'], 'isMore' => $this->data['isMore'], 'content' => view('my.train.common.list', $this->data)->render()];
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
        $analystService = new AnalystService();
        $this->data['analysts'] = $analystService->getAllName();
        return view('my.train.create',$this->data);
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
            'uid'=>'required|min:1',
            'time'=>'required',
            'price'=>'required',
            'image'=>'required',
            'content'=>'required',
            'webcastid'=>'required',
        ];
        $messages = [
            'uid.required'=>'请选择分析师',
            'time.required'=>'请填写时间',
            'price.required'=>'请填写价格',
            'image.required'=>'先选择图片',
            'webcastid.required'=>'请填写展示互动直播ID',
        ];
        $this->validate($request,$rules,$messages);
        $model = new Train();
        $model->title = Input::get('title');
        $model->uid = Input::get('uid');
        $model->price = Input::get('price');
        $model->time = Input::get('time');
        $model->image = Input::get('image');
        $model->content = Input::get('content');
        $model->webcastid = Input::get('webcastid');
        $model->vip = Input::get('vip');
        if($model->save()){
            return ['result'=>'添加成功'];
        }
        return response('添加失败',501);
    }
}
