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
        return $this->gets($result) ;
    }
    public function getAllHome($start,$length,$min=0,$isEqual=false)
    {
        $result = $this->zrangebyscore('all_home',$start,$length,$min,$isEqual);
        return $this->gets($result);
    }

    public function getRevHome($uid,$start,$length,$max=0,$isEqual=false)
    {
        $result = $this->zrevrangebyscore('home:'.$uid,$start,$length,$max,$isEqual);
        return $this->gets($result) ;
    }

    public function getHome($uid,$start,$length,$min=0,$isEqual=false)
    {
        $result = $this->zrangebyscore('home:'.$uid,$start,$length,$min,$isEqual);
        return $this->gets($result) ;
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

    protected function save($uid,$message,$image_id=0,$forward_type=null,$forward_id=0)
    {
        $model = new Status();
        $model->uid = $uid;
        $model->message = $message;
        if($image_id) $model->image_id = $image_id;
        if($forward_type) $model->forward_type = $forward_type;
        if($forward_id) $model->forward_id = $forward_id;
        if($model->save()) {
            $cacheModel = $this->loadCache($model->id);
            return $cacheModel;
        }
        return [];
    }

    public function post($uid,$message,$image_id=0,$forward_type=null,$forward_id=0)
    {
        $cacheModel = $this->save($uid,$message,$image_id,$forward_id,$forward_type);
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
        $this->syndicate_status($uid,$post);
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
        $status = $this->get($id);
        if($status){
            //非消息发布者本人,不能删除 .管理员除外
            if($status->uid != $uid && !isAdmin($uid)){
                return false;
            }
            if($status->delete()){//软删除
                $prefix = $this->prefix;
                Redis::pipeline(function ($pipe) use($uid,$id,$key) {
                    $pipe->DEL($key);
                    $pipe->ZREM('all_home',$id);//全部
                    $pipe->ZREM('home:'.$uid,$id);//关注
                    $pipe->ZREM('profile:'.$uid,$id);//个人主页
                    $pipe->ZINCRBY('zanalyst:status',-1,$uid);//排行
                    $pipe->HINCRBY('user:' . $uid, 'posts', -1);//用户直播总数量
                });
            }
        }
    }
    //恢复删除的评论
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
            if(isset($v['image_id']) && $v['image_id']>0){
                $all_image_id[] = $v['image_id'];
            }
            if(isset($v['forward_id']) && $v['forward_id']>0){
                $forward_type = !isset($v['forward_type']) ? 'status' : $v['forward_type'];
                $all_forward_id[$forward_type][] = $v['forward_id'];
            }
        }
        //获取所有图片
        $imageService = new ImageService();
        $all_images = $imageService->gets($all_image_id,true);

        //处理转发
        $commentService = new CommentService();
        $all_forward = [];
        foreach($all_forward_id as $k => $v){
            switch($k){
                case 'status':
                    $all_forward[$k] = $this->getForwardStatus($v);
                    break;
                case 'comment':
                    $all_forward[$k] = $commentService->getForwardComment($v);
                    break;
            }
        }
        //获取所有相关用户
        $userService = new UserService();
        $all_user = $userService->gets($all_uid,true);
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
            if(isset($v['image_id']) && $v['image_id']>0){
                $statuses[$k]['image'] = $all_images[$v['image_id']];
            }
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
        $all_image_id = [];
        foreach ($models as $k => $v) {
            $all_uid[] = $v['uid'];
            if(isset($v['image_id']) && $v['image_id']>0){
                $all_image_id[] = $v['image_id'];
            }
        }
        //获取所有图片
        $imageService = new ImageService();
        $all_images = $imageService->gets($all_image_id,true);
        //获取所有相关用户
        $userService = new UserService();
        $all_user = $userService->gets($all_uid,true);//获取所有用户
        foreach ($models as $k => $v) {
            $models[$k]['user'] = $all_user[$v['uid']];
            $models[$k]['image'] = $all_images[$v['image_id']];
        }
        return $models;
    }

    public function getViewListInfo1($statuses)
    {
        if(!$statuses) return [];
        //收集各type数据
        $all_user_ids = [];
        $all_status_ids = [];
        $all_comment_ids = [];
        $all_image_ids = [];
        $all_forward_ids = [];

        $all_status = [];

        foreach ($statuses as $status) {
            if(is_string($status))
                $status = json_decode($status);
            $all_status_ids[] = $status->statusid;;
            $all_user_ids[] = $status->userid;
            if(isset($status->imageid) && $status->imageid>0){
               $all_image_ids[] = $status->imageid;
            }
            if(isset($status->forwardid)){
                $all_forward_ids[] = $status->forwardid;
            }
            $all_status[$status->statusid] = $this->getCacheModel($status);
        }

        //处理赞
        $praiseService = new PraiseService();
        $praise_user_ids = $praiseService->gets($all_status_ids,true);
        foreach ($praise_user_ids as $k => $v) {
            if ($v) {
                $all_user_ids = array_merge($all_user_ids, $v);
            }
        }

        //获取 评论ids
        $commentService = new CommentService();
        $comment_ids = $commentService->getids($all_status_ids,true);
        foreach ($comment_ids as $k => $v) {
            if ($v) {
                $all_comment_ids = array_merge($all_comment_ids, $v);
            }
        }

        //处理转发
        $all_forward = [];
        $all_forward_status_ids = [];
        if($all_forward_ids) {
            $all_forward_ids = array_unique($all_forward_ids);
            sort($all_forward_ids);
            $forwardService = new ForwardService();
            $all_forward = $forwardService->gets($all_forward_ids,true);
            foreach($all_forward as $forward ){
                $all_forward_status_ids[] = $forward->statusid;
                if(isset( $forward->reply_commentid)){
                    $all_comment_ids[] = $forward->reply_commentid;
                    $all_user_ids[] = $forward->reply_userid;
                }
            }
        }
        //处理评论
        $all_comments = [];
        if($all_comment_ids) {
            $all_comment_ids = array_unique($all_comment_ids);
            sort($all_comment_ids);
            $all_comments = $commentService->gets($all_comment_ids,true);
            if ($all_comments) {
                foreach ($all_comments as $comment) {
                    $all_user_ids[] = $comment->userid;
                    if ($comment->reply_userid && $comment->reply_userid>0) {
                        $all_user_ids[] = $comment->reply_userid;
                    }
                }
            }
        }

        //处理转发直播
        $all_forward_status = [];
        if($all_forward_status_ids){
            $all_forward_status_ids = array_unique($all_forward_status_ids);
            sort($all_forward_status_ids);
            $all_forward_status = $this->gets($all_forward_status_ids,true);
            if($all_forward_status){
                foreach ($all_forward_status as $status) {
                    $all_user_ids[] = $status->userid;
                }
            }
        }
        //dd($all_forward_status);

        $all_user_ids = array_unique($all_user_ids);
        sort($all_user_ids);
        $userService = new UserService();
        $all_user = $userService->getProfiles($all_user_ids,true);

        //获取所有图片
        $imageSerive = new ImageService();
        $all_images = $imageSerive->gets($all_image_ids,true);

        foreach($all_status as $k => $v){
            $all_status[$k]['user'] = $userService->getCacheModel($all_user[$v['userid']]);
            if(isset($v['imageid']) && $v['imageid']>0){
                $all_status[$k]['image'] = $imageSerive->getCacheModel($all_images[$v['imageid']]);
            }
            //处理转发
            if(isset($v['forwardid']) && $v['forwardid']>0){
                $forward = $all_forward[$v['forwardid']];
                //dd($forward);
                if($all_forward_status && array_key_exists($forward->statusid,$all_forward_status)){
                    $forward_status = $this->getCacheModel($all_forward_status[$forward->statusid]);
                    $forward_status['user'] = $userService->getCacheModel($all_user[$forward_status['userid']]);
                    $all_status[$k]['forward'] = $forward_status;
                    if(isset($forward->reply_commentid) && $forward->reply_commentid>0){
                        $all_status[$k]['forward']['reply_comment'] = $commentService->getCacheModel( $all_comments[$forward->reply_commentid]);
                        $all_status[$k]['forward']['reply_comment']['user'] = $userService->getCacheModel( $all_user[$all_status[$k]['forward']['reply_comment']['userid']]);
                    }
                }
            }
            //处理赞
            $all_status[$k]['praiseCount'] = 0;
            if($praise_user_ids[$k]){
                $praises = array_reverse($praise_user_ids[$k]);
                $all_status[$k]['praiseCount'] = count($praises);
                $all_status[$k]['praises'] = $praises;
                foreach ($praises as $praise) {
                    $profile = $all_user[$praise];
                    if ($profile) {
                        $all_status[$k]['praises_avatar'][$praise] =$profile->avatar;
                    }
                }
            }

            //处理评论
            $all_status[$k]['commentCount'] = 0;
            if ($comment_ids[$k]) {//如果有评论
                $comments = $comment_ids[$k];
                $all_status[$k]['commentCount'] = count($comments);
                $comments = array_reverse($comments);
                foreach ($comments as $comment_id) {
                    $comment = $all_comments[$comment_id];
                    if ($comment) {
                        $all_status[$k]['comments'][$comment_id] =  $commentService->getCacheModel($comment);
                        if (array_key_exists($comment->userid, $all_user)) {
                            $all_status[$k]['comments'][$comment_id]['user'] = $userService->getCacheModel( $all_user[$comment->userid]);
                        }
                        if ($comment->reply_userid && array_key_exists($comment->reply_userid, $all_user)) {
                            $all_status[$k]['comments'][$comment_id]['reply_user'] = $userService->getCacheModel( $all_user[$comment->reply_userid]);
                        }
                    }
                }
            }
        }
        //dd($all_status);
        return $all_status;
    }
}