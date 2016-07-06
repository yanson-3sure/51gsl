<?php
namespace App\Services;

use App\Models\Status;
use App\Services\ImageService;
use Illuminate\Support\Facades\Redis;
use App\Services\UserService;

class StatusService extends AbstractService
{
    protected $prefix = 'status:';
    protected $model = Status::class;
    protected $noCacheAttributes = ['updated_at'];
    /*
     * 直播消息列表
     * type:   all_home=>全部  home=>关注   profile=>个人主页
     */
    public function getRevStatus($type,$start,$length,$max,$uid=0)
    {
        switch($type){
            case 'all_home':
                return $this->getRevAllHome($start,$length,$max);
            case 'home':
                return $this->getRevHome($uid,$start,$length,$max);
            case 'profile':
                return $this->getRevProfile($uid,$start,$length,$max);
        }
        return [];
    }
    /*
     * 直播消息列表
     * type:   all_home=>全部  home=>关注   profile=>个人主页
     */
    public function getStatus($type,$start,$length,$min,$uid=0)
    {
        switch($type){
            case 'all_home':
                return $this->getAllHome($start,$length,$min);
            case 'home':
                return $this->getHome($uid,$start,$length,$min);
            case 'profile':
                return $this->getProfile($uid,$start,$length,$min);
        }
        return [];
    }

    public function getRevAllHome($start,$length,$max=0,$isEqual=false){
        $result = $this->zrevrangebyscore('all_home',$start,$length,$max,$isEqual);
        //dd($result);
        return $this->gets($result) ;
    }
    public function getAllHome($start,$length,$min=0,$isEqual=false)
    {
        $result = $this->zrangebyscore('all_home',$start,$length,$min,$isEqual);
        return $this->gets($result);
    }

    public function getRevHome($uid,$start,$length,$max=0)
    {
        $score = $max;
        //先获取当前用户所有关注用户ids;
        $followService = new FollowService();
        $ids = $followService->getFollowingIds($uid);
        $this->prefix = 'profile:';
        $result = $this->zrevrangebyscores($ids, config('base.status_home_follow_size'), true);
        $this->prefix = 'status:';
        //dd($result);
        if($result) {
            $all_id = [];
            foreach ($result as $k => $v) {
                foreach($v as $k2 => $v2) {
                    if(array_key_exists($k2,$all_id)){
                        continue;
                    }
                    $all_id[$k2] = $v2;
                }
            }
            arsort($all_id);
            if ($score > 0) {
                foreach($all_id as $k=>$v){
                    if ($all_id[$k] >= $score) {
                        unset($all_id[$k]);
                    } else {
                        break;
                    }
                }
            }
            //dd($all_id);
            $all_status = array_slice(array_keys($all_id), $start, $length);
            if ($all_status) {
                return $this->gets($all_status, true);
            }
        }
        return [];
//        $result = $this->zrevrangebyscore('home:'.$uid,$start,$length,$max,$isEqual);
//        return $this->gets($result) ;
    }

    public function getHome($uid,$start,$length,$min=0)
    {
        $score = $min;
        //先获取当前用户所有关注用户ids;
        $followService = new FollowService();
        $ids = $followService->getFollowingIds($uid);
        $this->prefix = 'profile:';
        $result = $this->zrangebyscores($ids, config('base.status_home_follow_size'), true);
        $this->prefix = 'status:';
        //dd($result);
        if($result) {
            $all_id = [];
            foreach ($result as $k => $v) {
                foreach($v as $k2 => $v2) {
                    if(array_key_exists($k2,$all_id)){
                        continue;
                    }
                    $all_id[$k2] = $v2;
                }
            }
            $all_ids = [];

            if ($score > 0) {
                arsort($all_id);
                foreach($all_id as $k=>$v){
                    if($all_id[$k]<=$score){
                        break;
                    }
                    if($all_id[$k]>$score){
                        $all_ids[$k] = $v;
                    }
                }
                $all_ids =array_reverse(array_keys($all_ids));
            }else{
                asort($all_id);
                $all_ids = array_keys($all_id);
            }
            //dd($all_id);
            $all_status = array_slice($all_ids, $start, $length);
            $all_status = array_reverse($all_status);
            if ($all_status) {
                return $this->gets($all_status, true);
            }
        }
        return [];
//        $result = $this->zrangebyscore('home:'.$uid,$start,$length,$min,$isEqual);
//        return $this->gets($result) ;
    }
    public function getRevProfile($uid,$start,$length,$max=0,$isEqual=false)
    {
        $result = $this->zrevrangebyscore('profile:'.$uid,$start,$length,$max,$isEqual);
        return $this->gets($result) ;
    }

    public function getProfile($uid,$start,$length,$min=0,$isEqual=false)
    {
        $result = $this->zrangebyscore('profile:'.$uid,$start,$length,$min,$isEqual);
        return $this->gets($result) ;
    }

    public function loadProfileCache($uid)
    {
        $statuses = Status::where('uid',$uid)->get();
        Redis::pipeline(function ($pipe) use($uid ,$statuses) {
            foreach ($statuses as $k => $v) {
                $pipe->zadd('profile:' . $uid, strtotime($v->created_at), $v->id);
                $pipe->zadd('all_home', strtotime($v->created_at), $v->id);
            }
            $pipe->hset('user:'.$uid,'posts',count($statuses));
            $pipe->zadd('zanalyst:status',count($statuses),$uid);
        });
    }

    protected function save($uid,$message,$image='',$forward_type=null,$forward_id=0)
    {
        $model = new Status();
        $model->uid = $uid;
        $model->message = $message;
        if($image) $model->image = $image;
        if($forward_type) $model->forward_type = $forward_type;
        if($forward_id) $model->forward_id = $forward_id;
        if($model->save()) {
            $cacheModel = $this->loadCache($model->id);
            return $cacheModel;
        }
        return [];
    }

    public function post($uid,$message,$image='',$forward_type=null,$forward_id=0)
    {
        $cacheModel = $this->save($uid,$message,$image,$forward_type,$forward_id);
        if(!$cacheModel)return false;
        $post = [$cacheModel['id']=>strtotime($cacheModel['created_at'])];
        Redis::pipeline(function ($pipe) use($uid ,$post) {
            //放到自己主页的时间线上
            $pipe->ZADD('profile:' . $uid, $post);
            //放到总时间线上
            $pipe->ZADD('all_home', $post);
            //统计排行+1
            $pipe->ZINCRBY('zanalyst:status', 1, $uid);
            //用户的posts+1
            $pipe->HINCRBY('user:' . $uid, 'posts', 1);
        });
        //将消息状态,推送给关注者
        //$this->syndicate_status($uid,$post);
        return $cacheModel;
    }
    public function syndicate_status($uid,$post,$start=0)
    {
        $posts_per_pass = config('base.posts_per_pass');
        $home_timeline_size = config('base.home_timeline_size');
        $followers = Redis::zrangebyscore('followers:'.$uid,$start,'+inf',array('limit' => array($start, $posts_per_pass)));
        Redis::pipeline(function ($pipe) use($followers ,$post,$home_timeline_size) {
            foreach($followers as $k => $v){
                $pipe->zadd('home:'.$v,$post);
                $pipe->zremrangebyrank('home:'.$v,0,-$home_timeline_size-1);
            }
        });
        if(count($followers)>=$posts_per_pass){//如果关注用户大于 posts_per_pass ,放到队列中处理...

        }
    }

    public function delete_status($uid,$id)
    {
        $key = $this->getKey($id);
        $status = $this->find($id);
        if($status){
            //非消息发布者本人,不能删除 .管理员除外
            if($status['uid'] != $uid && !isAdmin($uid)){
                return false;
            }
            if($status->delete()){//软删除
                Redis::pipeline(function ($pipe) use($uid,$id,$key) {
                    $pipe->DEL($key);
                    $pipe->ZREM('all_home',$id);//全部
                    $pipe->ZREM('home:'.$uid,$id);//关注
                    $pipe->ZREM('profile:'.$uid,$id);//个人主页
                    $pipe->ZINCRBY('zanalyst:status',-1,$uid);//排行
                    $pipe->HINCRBY('user:' . $uid, 'posts', -1);//用户直播总数量
                });
                return true;
            }
        }
        return false;
    }
    //恢复删除
    public function restore($uid,$id)
    {

    }
    //过滤删除的
//    public function filter()
//    {
//
//    }

    public function getViewListInfo($statuses)
    {
        if(!$statuses) return [];
        //收集各type数据
        $all_uid = [];
        $all_status_id = [];
        $all_image_id = [];
        $all_forward_id = [];

        foreach ($statuses as $k => $v) {
            $all_uid[] = $v['uid'];
            $all_status_id[] = $v['id'];
            if(isset($v['forward_id']) && $v['forward_id']>0){
                $forward_type = !isset($v['forward_type']) ? 'status' : $v['forward_type'];
                $all_forward_id[$forward_type][] = $v['forward_id'];
            }
        }
        //处理转发
        $commentService = new CommentService();
        $strategyService = new StrategyService();
        $all_forward = [];
        foreach($all_forward_id as $k => $v){
            switch($k){
                case 'status':
                    $all_forward[$k] = $this->getForwardStatus($v);
                    break;
                case 'comment':
                    $all_forward[$k] = $commentService->getForwardComment($v);
                    break;
                case 'strategy':
                    $all_forward[$k] = $strategyService->getForward($v);
                    break;
            }
        }
        //获取所有相关用户
        $userService = new UserService();
        $all_user = $userService->getBases($all_uid);
        //获取所有赞
        $praiseService = new PraiseService();
        $all_praise = $praiseService->zgets($all_status_id,'status',config('base.status_praise_size'));
        //获取所有评论
        $all_comment = $commentService->zgets($all_status_id,'status',config('base.status_comment_size'));

        //合并结果
        foreach($statuses as $k => $v){
            $statuses[$k]['praises_count']  = 0;
            if(isset($v['praises']) && $v['praises']>0){
                $statuses[$k]['praises_count'] = $v['praises'];
            }
            $statuses[$k]['comments_count']  = 0;
            if(isset($v['comments']) && $v['comments']>0){
                $statuses[$k]['comments_count'] = $v['comments'];
            }
            $statuses[$k]['user'] = $all_user[$v['uid']];
            if(isset($v['forward_id']) && $v['forward_id']>0){
                $statuses[$k]['forward'] = $all_forward[$v['forward_type']][$v['forward_id']];
            }
            $statuses[$k]['praises'] = $all_praise[$v['id']];
            $statuses[$k]['comments'] = $all_comment[$v['id']];
        }
        return $statuses;

    }
    public function getForwardStatus($ids){
        if(!$ids) return [];
        $models = $this->gets($ids,true);
        $all_uid = [];
        foreach ($models as $k => $v) {
            if($v) {
                $all_uid[] = $v['uid'];
            }
        }
        //获取所有相关用户
        $userService = new UserService();
        $all_user = $userService->getBases($all_uid);//获取所有用户
        foreach ($models as $k => $v) {
            if($v) {
                $models[$k]['user'] = $all_user[$v['uid']];
            }
        }
        return $models;
    }
}