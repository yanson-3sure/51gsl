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
            $this->setCacheModel($cacheModel,$model->uid);
            return $cacheModel;
        }
        return [];
    }

    public function getAllName()
    {
        $ids = Redis::ZREVRANGE('zanalyst:status',0,-1);
        if($ids){
            $userService = new UserService();
            return $userService->getNames($ids);
        }
        return [];
    }

    public function getList($ids,$follower_id = 0)
    {
        $userService = new UserService();
        $users = $userService->gets($ids);
        $feature = $this->hmgets($ids,['feature']);
        foreach($users as $k => $v){
            $users[$k]['feature'] = $feature[$k]['feature'];
        }
        if($follower_id){
            $followService = new FollowService();
            $ids = $followService->getFollowingIds($follower_id);
            foreach($users as $k => $v){
                if(in_array($v['id'],$ids)){
                    $users[$k]['following'] = true;
                }else{
                    $users[$k]['following'] = false;
                }
            }
        }
        return $users;
    }

    public function getRankByStatus($followerid=0)
    {
        $ids = Redis::ZREVRANGE('zanalyst:status',0,-1);
        if($ids)
            return $this->getList($ids,$followerid);
        return [];
    }
    public function getRankByFollower($followerid=0)
    {
        $ids = Redis::ZREVRANGE('zanalyst:followers',0,-1);
        if($ids)
            return $this->getList($ids,$followerid);
        return [];
    }
    public function getRankByComment($followerid=0)
    {
        $ids = Redis::ZREVRANGE('zanalyst:comment',0,-1);
        if($ids)
            return $this->getList($ids,$followerid);
        return [];
    }

    public function filter($models)
    {
        $filter_analyst_user_ids = array_unique(config('base.filter_analyst_user_ids'));
        $filter_analyst_user_ids = array_flip($filter_analyst_user_ids);
        return array_diff_key($models,$filter_analyst_user_ids);
    }

    public function initCache($uid)
    {
        Redis::pipeline(function ($pipe) use($uid) {
            $pipe->ZADD('zanalyst:comment', 0, $uid);
            $pipe->ZADD('zanalyst:status', 0, $uid); //直播排行列表
            $pipe->ZADD('zanalyst:followers', 0, $uid);
            $pipe->ZADD('zanalyst:praises', 0, $uid);
        });
    }
}