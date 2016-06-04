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

    public function isFollowing($uid,$f_uid)
    {
        return Follow::where('uid',$uid)->where('f_uid',$f_uid)->count();
    }

    /*
     * 关注用户
     */
    public function follow_user($uid,$f_uid)
    {
        $key1 = $this->getKey1($uid);
        $key2 = $this->getKey2($f_uid);

        if($this->isFollowing($uid,$f_uid)){//已经关注过
            return false;
        }
        $now = time();
        if(!$this->save($uid,$f_uid,$now)) {
            return false;
        }
        Redis::pipeline(function ($pipe) use($key1,$key2,$uid ,$f_uid,$now) {
            $following = Redis::zadd($key1, $now, $f_uid);
            $followers = Redis::zadd($key2, $now, $uid);
            //从关注用户的个人时间里面获取HOME_TIMELINE_SIZE条最新的消息
            $statues = Redis::zrevrange('profile:' . $f_uid, 0, config('base.home_timeline_size') - 1, 'WITHSCORES');
            Redis::hincrby('user:' . $uid, 'following', $following);
            Redis::hincrby('user:' . $f_uid, 'followers', $followers);

            if ($statues) {
                Redis::zadd('home:' . $uid, $statues);
            }
            Redis::zremrangebyrank('home:' . $uid, 0, -config('base.home_timeline_size') - 1);
        });
        return true;
    }

    public function unfollow_user($uid,$f_uid)
    {
        $key1 = $this->getKey1($uid);
        $key2 = $this->getKey2($f_uid);

        if(!$this->isFollowing($uid,$f_uid)){//未关注过
            return false;
        }

        Follow::where('uid',$uid)->where('f_uid',$f_uid)->delete();

        Redis::pipeline(function ($pipe) use($key1,$key2,$uid ,$f_uid) {
            $following = Redis::zrem($key1, $f_uid);
            $followers = Redis::zrem($key2, $uid);
            //从关注用户的个人时间里面获取HOME_TIMELINE_SIZE条最新的消息
            $statues = Redis::zrevrange('profile:' . $f_uid, 0, config('base.home_timeline_size') - 1);
            Redis::hincrby('user:' . $uid, 'following', -$following);
            Redis::hincrby('user:' . $f_uid, 'followers', -$followers);

            if ($statues) {
                Redis::zrem('home:' . $uid, $statues);
            }
        });
        return true;
    }

    protected function save($uid,$f_uid,$created_at){
        $model = new Follow();
        $model->uid = $uid;
        $model->f_uid = $f_uid;
        $model->created_at = $created_at;
        if($model->save()){
            return $model;
        }
        return false;
    }







}