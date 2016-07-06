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
