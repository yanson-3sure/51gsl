<?php
namespace App\Services;


use App\Models\Video;
use Gensee\Facades\Gensee;

class VideoService
{
    public function updateAllVodList()
    {
        $startTime='2000-01-01 00:00:00';
        $endTime='9999-01-01 00:00:00';
        $pageNo = 1;
        do {
            $result = $this->updateVodList($startTime, $endTime, $pageNo);
            if($result){
                $pageNo++;
            }
        }while($result);
    }
    public function updateVodList($startTime='2000-01-01 00:00:00',$endTime='9999-01-01 00:00:00',$pageNo=1)
    {
        $response = Gensee::WebcastJsonAPI()->getVodSync($startTime,$endTime,$pageNo);
        if(isset($response->list)){
            return $this->sync($response->list) ;
        }
        return 0;
    }
    public function sync($list)
    {
        $sync_num = 0;
        foreach($list as $k => $v){
            if(!Video::find($v->id)){//如果不存在,插入
                $model = new Video();
                $model->id = $v->id;
                $model->attendeeJoinUrl = $v->attendeeJoinUrl;
                $model->convertResult = $v->convertResult;
                $model->createdTime = date('Y-m-d H:i:s',$v->createdTime/1000);
                $model->creator = $v->creator;

                $model->description = $v->description;
                $model->grType = $v->grType;
                $model->number = $v->number;
                $model->password = $v->password;
                $model->recordEndTime = date('Y-m-d H:i:s',$v->recordEndTime/1000);

                $model->recordId = $v->recordId;
                $model->recordStartTime = date('Y-m-d H:i:s',$v->recordStartTime/1000);
                $model->subject = $v->subject;
                if(isset($v->webcastId)) {
                    $model->webcastId = $v->webcastId;
                }else{
                    $model->webcastId = '';
                }
                $model->save();
                $sync_num++;

            }else{
                break;
            }
        }
        return $sync_num;
    }
}