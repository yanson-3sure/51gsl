<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/4/14
 * Time: 19:32
 */

namespace App\Services;

use App\Models\Follow;
use Illuminate\Support\Facades\Redis;
use App\Models\Following;
use Carbon\Carbon;


class FollowService
{
    private $prefix_er = 'followers:';
    private $prefix_ing = 'following:';

    public function getKey1($uid)
    {
        return $this->prefix_ing . $uid;
    }

    public function getKey2($uid)
    {
        return $this->prefix_er . $uid;
    }

    public function getFollowers($uid)
    {

    }
    public function getFollowingIds($uid)
    {
        return $this->zrevrangebyscore($this->getKey1($uid),0,-1,0);
    }
    public function getFollowing($uid,$length,$max=0)
    {
        $all_uid = $this->zrevrangebyscore($this->getKey1($uid),0,$length,$max);
        if(!$all_uid){
            return [];
        }
        $analystService = new AnalystService();
        $users = $analystService->getList($all_uid);
        return $users;
    }
    protected function zrevrangebyscore($key,$start,$length,$max=0,$isEqual=false,$WITHSCORES=false)
    {
        if(!$max) {
            $max = '+inf';
        }
        if(!$isEqual && $max!='+inf'){
            $max = '(' . $max;
        }
        $arguments = ['limit' => [$start, $length]];
        if($WITHSCORES){
            $arguments['WITHSCORES'] = true;
        }
        return Redis::ZREVRANGEBYSCORE($key,$max,'-inf',$arguments);
    }

    public function isFollowing($uid, $f_uid)
    {
        return Follow::where('uid', $uid)->where('f_uid', $f_uid)->count();
    }

    /*
     * 关注用户
     */
    public function follow_user($uid, $f_uid)
    {
        $key1 = $this->getKey1($uid);
        $key2 = $this->getKey2($f_uid);

        if ($this->isFollowing($uid, $f_uid)) {//已经关注过
            return false;
        }
        $now1 = Carbon::now();
        $now = strtotime($now1);
        if (!$this->save($uid, $f_uid, $now1)) {
            return false;
        }
        $response = Redis::pipeline(function ($pipe) use ($key1, $key2, $uid, $f_uid, $now) {
            $pipe->zadd($key1, $now, $f_uid);
            $pipe->zadd($key2, $now, $uid);
            //从关注用户的个人时间里面获取HOME_TIMELINE_SIZE条最新的消息
            $pipe->zrevrange('profile:' . $f_uid, 0, config('base.home_timeline_size') - 1, 'WITHSCORES');
        });
        $following = $response[0];
        $followers = $response[1];
        $statues = $response[2];
        $response = Redis::pipeline(function ($pipe) use ($following, $followers, $statues, $uid, $f_uid) {
            $pipe->hincrby('user:' . $uid, 'following', $following);
            $pipe->hincrby('user:' . $f_uid, 'followers', $followers);

            //统计排行+1
            $pipe->zincrby('zanalyst:followers', 1, $f_uid);

            if ($statues) {
                $pipe->zadd('home:' . $uid, $statues);
            }
            $pipe->zremrangebyrank('home:' . $uid, 0, -config('base.home_timeline_size') - 1);
        });
        return true;
    }

    public function unfollow_user($uid, $f_uid)
    {
        $key1 = $this->getKey1($uid);
        $key2 = $this->getKey2($f_uid);

        if (!$this->isFollowing($uid, $f_uid)) {//未关注过
            return false;
        }

        Follow::where('uid', $uid)->where('f_uid', $f_uid)->delete();

        $response = Redis::pipeline(function ($pipe) use ($key1, $key2, $uid, $f_uid) {
            $pipe->zrem($key1, $f_uid);
            $pipe->zrem($key2, $uid);
            //从关注用户的个人时间里面获取HOME_TIMELINE_SIZE条最新的消息
            $pipe->zrevrange('profile:' . $f_uid, 0, config('base.home_timeline_size') - 1);
        });
        $following = $response[0];
        $followers = $response[1];
        $statues = $response[2];
        $response = Redis::pipeline(function ($pipe) use ($following, $followers, $statues, $uid, $f_uid) {
            $pipe->hincrby('user:' . $uid, 'following', -$following);
            $pipe->hincrby('user:' . $f_uid, 'followers', -$followers);

            //统计排行-1
            $pipe->zincrby('zanalyst:followers', -1, $f_uid);

            if ($statues) {
                $pipe->zrem('home:' . $uid, $statues);
            }
        });
        return true;
    }

    protected function save($uid, $f_uid, $created_at)
    {
        $model = new Follow();
        $model->uid = $uid;
        $model->f_uid = $f_uid;
        $model->created_at = $created_at;
        if ($model->save()) {
            return $model;
        }
        return false;
    }


}