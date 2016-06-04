<?php
namespace App\Services;

use App\Models\Analyst;
use Carbon\Carbon;
use Illuminate\Support\Facades\Redis;


class AnalystService extends AbstractService
{
    protected $prefix = 'analyst:';
    protected $model = Analyst::class;
    protected $noCacheAttributes = ['created_at','updated_at'];

    public function save($uid,$role_name,$feature,$application_id,$status=1)
    {
        $model = new Analyst();
        $model->uid = $uid;
        $model->role_name = $role_name;
        $model->feature = $feature;
        $model->application_id = $application_id;
        $model->status = $status;
        $model->audit_at = Carbon::now();
        if($model->save()){
            $cacheModel = $this->getCacheModel($model);
            $this->setCacheModel($model->uid,$cacheModel);
            return $cacheModel;
        }
        return [];
    }

    public function getsByStatusCount($followerid=0)
    {
        //$ids = Redis::ZREVRANGE('zstatus:count',0,-1,'WITHSCORES');
        $ids = Redis::ZREVRANGE('zstatus:count',0,-1);
        if($ids)
            return $this->gets($ids,$followerid);
        return [];
    }
    public function getsByFollowerCount($followerid=0)
    {
        //$ids = Redis::ZREVRANGE('zstatus:count',0,-1,'WITHSCORES');
        $ids = Redis::ZREVRANGE('zfollowers:count',0,-1);
        if($ids)
            return $this->gets($ids,$followerid);
        return [];
    }
    public function getsByReplyCommentCount($followerid=0)
    {
        //$ids = Redis::ZREVRANGE('zstatus:count',0,-1,'WITHSCORES');
        $ids = Redis::ZREVRANGE('zanalyst:comment:count',0,-1);
        if($ids)
            return $this->gets($ids,$followerid);
        return [];
    }

    public function initCache($uid)
    {
        Redis::ZADD('zanalyst:comment:count', 0, $uid);
        Redis::ZADD('zstatus:count', 0, $uid); //直播排行列表
        Redis::ZADD('zfollowers:count', 0, $uid);

    }
}