<?php
namespace Gensee\Webcast;

use Gensee\Core\AbstractAPI;
use Ixudra\Curl\Facades\Curl;

class WebcastUrlAPI extends AbstractAPI
{
    /*
     * 5.8 获取直播配置信息
     */
    public function getSettingInfo($webcastId ,$sec = false)
    {

    }

    /*
     * 5.10 获取直播录制的点播列表
     */
    public function getRecordInfo($webcastId)
    {
        return $this->get('/webcast/record/info',['webcastId'=>$webcastId]);
    }

    public function getPrefix($suffix)
    {
        return $this->user->getSite().'integration/site'.$suffix;
    }

    public function get($suffix,$params)
    {
        $data = array_merge($this->getUserData(),$params);
        $url = $this->getPrefix($suffix);
        $response = Curl::to($url)
            ->withData($data)
            ->post();
        if($response){
            return json_decode($response);
        }else{
            return false;
        }
    }
}