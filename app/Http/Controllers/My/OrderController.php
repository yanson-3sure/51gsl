<?php

namespace App\Http\Controllers\My;

use App\Models\Order;
use App\Services\OrderService;
use App\Services\UserService;
use App\Services\WechatService;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Carbon\Carbon;

class OrderController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->service = new OrderService();
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
        $product_id = Input::get('product_id');
        $product_type = Input::get('product_type','analyst');
        if($product_type=='analyst') {
            $userService = new UserService();
            $this->data['mobile'] = $userService->getMobile($this->uid);
            $this->data['analyst'] = $userService->get($product_id);
            $orderService = new OrderService();
            $this->data['price'] = $orderService->getProductPrice($product_id,$product_type);
            $this->data['now'] = Carbon::now();
            $this->data['end'] = Carbon::now()->addMonth(1);
            $wechatService = new WechatService();
            $app = $wechatService->getApp();
            $this->data['js'] = $app->js;
            return view('my.order.create', $this->data);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $type = Input::get('type','free');
        $product_id = Input::get('product_id');
        $product_type = Input::get('product_type','analyst');
        if($type=='free'){
            $result = $this->service->orderFree($this->uid,$product_id,$product_type);
            if($result['status']==200){
                return ['result'=>'成功'];
            }
            return response($result['content'],$result['status']);
        }
        if($type=='weipay'){
            $month = Input::get('month',1);
            $center_order_id = '';
            $result = $this->service->orderWeiPay($this->uid,$product_id,$product_type,$month,$month,$center_order_id);
            if($result['status']==200){
                $wechatService = new WechatService();
                $order = $result['content'];
                $title = $this->service->getProductTitle($product_id,$product_type);
                $prepayId = $wechatService->createOrder($title,$title,$order->id,$order->price,$order->openid);
                if($prepayId) {
                    $app = $wechatService->getApp();
                    $payment = $app->payment;
                    $config = $payment->configForJSSDKPayment($prepayId);
                    $config['order_id'] = $order->id;
                    $this->data['config'] = $config;
                    return $config;
                }
                return response('生成微信订单失败',501);
            }
            if($result['status']==400){
                return response(['result'=>'no_mobile']);
            }
            return response($result['content'],$result['status']);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $model = Order::find($id);
        if($model && ($model->uid == $this->uid || isAdmin($this->uid))) {
            $this->data['model'] = $model;
            $userService = new UserService();
            $this->data['user'] = $userService->get($model->product_id);
            return view('my.order.show', $this->data);
        }
        return '没有相关订单信息';
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

}
