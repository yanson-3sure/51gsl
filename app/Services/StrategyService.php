<?php
namespace App\Services;

use App\Models\Status;
use App\Models\Strategy;
use App\Services\MyTraits\Permissions;
use Carbon\Carbon;
use Illuminate\Support\Facades\Redis;

class StrategyService extends AbstractService
{
    use Permissions;
    protected $prefix = 'strategy:';
    protected $model = Status::class;
    protected $noCacheAttributes = ['content','risk'];

    public function save($uid,$title,$vip,$intro,$content,$risk)
    {
        $model = new Strategy();
        $model->uid = $uid;
        $model->title = $title;
        $model->vip = $vip;
        $model->intro = $intro;
        $model->content = $content;
        $model->risk = $risk;
        $now = Carbon::now();
        $model->created_at = $now;
        $model->updated_at = $now;
        if($model->save()){
            $statusService = new StatusService();
            $statusService->post($uid,'新增了一条策略','','strategy',$model->id);
            return $this->setCacheModel($model);
        }
        return [];
    }

    public function updateTime($id)
    {
        $model = Strategy::find($id);
        if($model) {
            $model->updated_at = Carbon::now();
            if ($model->save()){
                Redis::hset($this->getKey($id),'updated_at',$model->updated_at);
                return true;
            }
        }
        return false;
    }

    public function getForward($ids)
    {
        if(!$ids) return [];
        $models = $this->gets($ids,true);
        $all_uid = [];
        foreach ($models as $k => $v) {
            $all_uid[] = $v['uid'];
        }
        //获取所有相关用户
        $userService = new UserService();
        $all_user = $userService->getBases($all_uid);//获取所有用户
        foreach ($models as $k => $v) {
            $models[$k]['user'] = $all_user[$v['uid']];
        }
        return $models;
    }
}