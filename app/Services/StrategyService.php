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
            return $this->setCacheModel($model);
        }
        return [];
    }
}