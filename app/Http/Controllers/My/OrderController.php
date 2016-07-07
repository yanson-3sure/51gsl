<?php

namespace App\Http\Controllers\My;

use App\Services\OrderService;
use App\Services\UserService;
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

}
