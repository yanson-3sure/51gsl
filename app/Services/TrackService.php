<?php
namespace App\Services;

use App\Models\Track;
use App\Services\StrategyService;
use App\Services\StatusService;

class TrackService
{
    public function save($strategy_id,$status,$content,$uid,$image)
    {
        $model = new Track();
        $model->strategy_id = $strategy_id;
        $model->status = $status;
        $model->content = $content;
        $model->uid = $uid;
        $model->image = $image;
        if($model->save()){
            $strategyService = new StrategyService();
            $strategyService->updateTime($strategy_id);
            $statusService = new StatusService();
            $statusService->post($model->uid,'更新了一条策略','','strategy',$strategy_id);
            return true;
        }
        return false;
    }
}