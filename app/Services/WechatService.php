<?php
namespace App\Services;

use EasyWeChat\Foundation\Application;
use Jenssegers\Agent\Agent;

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
}