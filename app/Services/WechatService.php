<?php
namespace App\Services;

use EasyWeChat\Foundation\Application;
use Jenssegers\Agent\Agent;
use EasyWeChat\Payment\Order;

class WechatService
{
    protected $_isWechat =false;
    protected $_domain = '';
    protected $_options = [];

    public function __construct()
    {
        $agent = new Agent();
        $this->_isWechat = $agent->match('.*MicroMessenger.*');
        $this->_domain = $_SERVER['HTTP_HOST'];
        $wechat_config = config('wechat');
        $this->_options = array_key_exists($this->_domain,$wechat_config) ? config($wechat_config[$this->_domain]) : [];
    }

    public function isWechat()
    {
        return $this->_isWechat;
    }
    public function domain()
    {
        return $this->_domain;
    }

    public function options()
    {
        return $this->options();
    }

    public function getApp()
    {
        return new Application($this->_options);
    }

    public function createOrder($body,$detail,$out_trade_no,$total_fee,$openid,$trade_type='JSAPI',$notify_url=null)
    {
        $attributes = [
            'trade_type'       => $trade_type, // JSAPI，NATIVE，APP...
            'body'             => $body,
            'detail'           => $detail,
            'out_trade_no'     => $out_trade_no,
            'total_fee'        => $total_fee,
            'openid'           => $openid,
        ];
        if($notify_url){
            $attributes['notify_url'] = $notify_url;
        }
        $order = new Order($attributes);
        $app = $this->getApp();
        $payment = $app->payment;
        $result = $payment->prepare($order);
        $prepayId = '';
        if ($result->return_code == 'SUCCESS' && $result->result_code == 'SUCCESS'){
            $prepayId = $result->prepay_id;
        }
        return $prepayId;
    }
}