<?php
namespace App\Services;


use App\Models\Order;
use App\Services\MyTraits\Common;
use Carbon\Carbon;

class OrderService
{
    use Common;
    /*
     * 获取某位用户的订阅,分析师ids,返回数组
     */
    public function getUserIds($uid)
    {
        $now = Carbon::now();
        $models = Order::where('uid',$uid)
            ->whereIn('order_type',[1,2,3])
            ->where('start_at','<',$now)
            ->where('end_at','>',$now)
            ->where('status','paid')
            ->where('product_type','analyst')
            ->get();
        $result = [];
        foreach($models as $model){
            $result[] = $model->product_id;
        }
        return $result;
    }
    //获取用户,订阅某位老师的订单信息
    public function has($uid,$product_id,$product_type='analyst')
    {
        $now = Carbon::now();
        $order = Order::where('uid',$uid)
            ->whereIn('order_type',[1,2,3])
            ->where('start_at','<',$now)
            ->where('end_at','>',$now)
            ->where('status','paid')
            ->where('product_id',$product_id)
            ->where('product_type',$product_type)
            ->get();
        if(count($order)){
            return true;
        }
        return false;
    }
    public function orderWeiPayFaild($order_id)
    {
        $model = Order::find($order_id);
        if(!$model){
            return $this->result('订单不存在',501);
        }
        $model->status = 'paid_faild';
        if($model->save()){
            return $this->result($model);
        }
        return $this->result('失败',501);
    }
    public function orderWeiPaySuccess($order_id,$transaction_id)
    {
        $model = Order::find($order_id);
        if(!$model){
            return $this->result('订单不存在',501);
        }
        $model->paid_at = Carbon::now();
        $model->status = 'paid';
        $model->price_pay = $model->price;
        $model->transaction_id = $transaction_id;
        if($model->save()){
            return $this->result($model);
        }
        return $this->result('失败',501);
    }
    public function orderWeiPay($uid,$product_id,$product_type,$number,$month,$center_order_id,$order_type=2)
    {
        $userService = new UserService();
        $user = $userService->find($uid);
        if(!$user){
            return $this->result('用户不存在',501);
        }
        if(!$user['mobile']){
            return $this->result('请先绑定手机号',501);
        }
        //验证产品,是否存在
        if(!$this->chkProduct($product_id,$product_type)){
            return $this->result('产品不存在',501);
        }
        $openid = $userService->getOpenId($uid);
        if(!$openid){
            return $this->result('OPENID不存在',501);
        }
        $order_id = $this->createOrderId($product_id,$product_type);
        $now = Carbon::now();
        $model = new Order();
        $model->id = $order_id;
        $model->uid = $uid;
        $model->mobile = $user['mobile'];
        $model->product_id = $product_id;
        $model->product_type = $product_type;
        $model->number = $number;
        $model->price = $this->getProductPrice($product_id,$product_type) * $month;
        $model->price_pay = 0;
        $model->start_at = $now;
        $model->end_at = Carbon::now()->addMonth($month)->format('Y-m-d').' 23:59:59';
        $model->order_type = $order_type;
        $model->status = 'paying';
        $model->center_order_id = $center_order_id;
        $model->transaction_id = '';
        $model->openid = $openid;
        if($model->save()){
            $model->id = $order_id;
            return $this->result($model);
        }
        return $this->result('失败',501);
    }


    public function orderFree($uid,$product_id,$product_type='analyst',$month=1)
    {
        $userService = new UserService();
        $user = $userService->find($uid);
        if(!$user){
            return $this->result('用户不存在',501);
        }
        if(!$user['mobile']){
            return $this->result('请先绑定手机号',501);
        }
        //验证产品,是否存在
        if(!$this->chkProduct($product_id,$product_type)){
            return $this->result('产品不存在',501);
        }
        $free = $this->getFree($uid,$product_id,$product_type);
        if($free){
            return $this->result('已经试用过',501);
        }
        $order_id = $this->createOrderId($product_id,$product_type);
        $now = Carbon::now();
        $model = new Order();
        $model->id = $order_id;
        $model->uid = $uid;
        $model->mobile = $user['mobile'];
        $model->product_id = $product_id;
        $model->product_type = $product_type;
        $model->price = $this->getProductPrice($product_id,$product_type);
        $model->price_pay = 0;
        $model->paid_at = $now;
        $model->start_at = $now;
        $model->end_at = Carbon::now()->addMonth($month)->format('Y-m-d').' 23:59:59';
        $model->order_type = 1;
        $model->status = 'paid';
        $model->center_order_id = '';
        $model->transaction_id = '';
        if($model->save()){
            return $this->result($model);
        }
        return $this->result('失败',501);
    }

    public function getProductPrice($product_id,$product_type)
    {
        $price = 0;
        switch($product_type){
            case 'analyst':
                $key = 'base.product_price.'.$product_type.'.'.$product_id;
                $price = config($key);
                break;
        }
        return $price;
    }

    public function chkProduct($product_id,$product_type)
    {
        switch($product_type){
            case 'analyst':
                $userService = new UserService();
                return $userService->isAnalyst($product_id);
                break;
        }
        return false;
    }
    public function getProductTitle($product_id,$product_type)
    {
        switch($product_type){
            case 'analyst':
                $userService = new UserService();
                $user = $userService->get($product_id);
                return $user['name'] . '的VIP服务';
                break;
        }
        return false;
    }

    public function createOrderId($product_id,$product_type='analyst')
    {
        $prefix = strtoupper(substr( $product_type, 0, 1 ));
        return date_format(Carbon::now(),'YmdHisu').'R'.rand(10,99).$prefix.$product_id;
    }
    public function getFree($uid,$product_id,$product_type='analyst')
    {
        return Order::where('uid',$uid)
            ->where('product_id',$product_id)
            ->where('product_type',$product_type)
            ->first();
    }
    //个人用户是否有权限查看
    public function hasPermissions($uid,$model)
    {
        if(!$model){
            return false;
        }
        if(isAdmin($uid)){//管理员
            return true;
        }
        if(!$model->vip){//免费
            return true;
        }
        if($model->vip){//vip
            if($model->uid == $uid) { //对象拥有者
                return true;
            }
            //收费用户
            return in_array($uid,$this->getUserIds($uid));
        }
        return false;
    }
}