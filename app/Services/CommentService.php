<?php
namespace App\Services;

use App\Models\Comment;
use Carbon\Carbon;
use Illuminate\Support\Facades\Redis;
use Symfony\Component\Finder\Tests\Iterator\RealIteratorTestCase;

class CommentService extends AbstractService
{
    protected $prefix = 'comment:';
    protected $model = Comment::class;
    protected $onlyCacheTrueExcept = ['comment'];

    public function zgets($ids,$object_type='status',$length=10)
    {
        $this->object_type = $object_type;
        $result = $this->zrangebyscores($ids,$length);
        $all_uid = [];
        $all_reply_comment_id = [];
        $all_comment_id = [];
        foreach($result as $k => $v){
            $all_comment_id = array_merge($all_comment_id,$v);
        }
        $this->object_type = '';
        $models = $this->gets($all_comment_id);
        foreach($models as $k => $v){
            $all_uid[] = $v['uid'];
            if(isset($v['reply_uid']) && $v['reply_uid']>0){
                $all_uid[] = $v['reply_uid'];
                $all_reply_comment_id[] = $v['reply_comment_id'];
            }
        }
        //获取所有相关用户
        $userService = new UserService();
        $all_user = $userService->getNames($all_uid);
        //处理所有的reply
        $reply_comments = $this->gets($all_reply_comment_id);
        foreach($result as $k => $v){
            $comment_info = [];
            foreach($v as $comment_id) {
                $comment = $models[$comment_id];
                $comment_info[$comment_id] = $comment;
                $comment_info[$comment_id]['user'] = $all_user[$comment['uid']];
                if (isset($comment['reply_uid']) && $comment['reply_uid'] > 0) {
                    $comment_info[$comment_id]['reply_user'] = $all_user[$comment['reply_uid']];
                    $comment_info[$comment_id]['reply_comment'] = $reply_comments[$comment['reply_comment_id']];
                }
            }
            $result[$k] = $comment_info;
        }
        return $result;
    }

    public function getForwardComment($ids)
    {
        if(!$ids) return [];
        $models = $this->gets($ids,true);
        $all_uid = [];
        $all_reply_comment_id = [];
        $all_object_id = [];
        foreach($models as $k => $v){
            $all_uid[] = $v['uid'];
            if(isset($v['reply_uid']) && $v['reply_uid']>0){
                $all_uid[] = $v['reply_uid'];
                $all_reply_comment_id[] = $v['reply_comment_id'];
            }
            if(isset($v['object_id']) && $v['object_id']>0){
                $object_type = !isset($v['object_type']) ? 'status' : $v['object_type'];
                $all_object_id[$object_type][] = $v['object_id'];
            }
        }
        //获取所有相关用户
        $userService = new UserService();
        $all_user = $userService->getNames($all_uid);
        //处理所有的reply
        $reply_comments = $this->gets($all_reply_comment_id);
        //处理所有object_id
        $all_object = [];
        foreach($all_object_id as $k => $v){
            switch($k){
                case 'status':
                    $statusService = new StatusService();
                    $all_object[$k] = $statusService->getForwardStatus($v);
                    break;
            }
        }
        foreach($models as $k => $v){
            $models[$k]['user'] = $all_user[$v['uid']];
            if(isset($v['reply_uid']) && $v['reply_uid']>0) {
                $models[$k]['reply_comment'] = $reply_comments[$v['reply_comment_id']];
                $models[$k]['reply_comment']['user'] = $all_user[$v['reply_uid']];
            }
            $models[$k]['object'] = $all_object[$v['object_type']][$v['object_id']];
        }
        return $models;
    }

    public function save($uid, $comment, $object_id, $object_type = 'status', $reply_uid = 0, $reply_comment_id = 0)
    {
        $userService = new UserService();
        $user = $userService->get($uid);
        if(isBlackRole($user['role'])) {
            return false;
        }
        $object_uid = 0;
        if($object_type=='status'){//如果是直播,检查是否允许评论
            $statusService = new StatusService();
            $object = $statusService->get($object_id);
            if($object){
                if(isset($object['isComment']) && $object['isComment']==0){//不允许评论
                    return false;
                }
                $object_uid = $object['uid'];
            }
        }else{
            $object_uid = $this->getObjectUid($object_type,$object_id);
        }
        if(!$object_uid) return -1;
        $role = $user['role'];
        $now1 = Carbon::now();
        $now = strtotime($now1);
        $model = new Comment();
        $model->uid = $uid;
        $model->comment = $comment;
        $model->object_id = $object_id;
        $model->object_type = $object_type;
        $model->reply_uid = $reply_uid;
        $model->reply_comment_id = $reply_comment_id;
        $model->created_at = $now1;
        if(!$model->save()){
            return false;
        }
        $comment_id = $model->id;
        if(!$object_uid) {
            $object_uid = $this->getObjectUid($object_type, $object_id);
        }
        $cacheModel = $this->getCacheModel($model);
        $this->setCacheModel($cacheModel,$comment_id);

        $key = $this->getKey($object_id,$object_type);
        Redis::pipeline(function ($pipe)use($key,$now,$comment_id,$object_uid,$object_type,$object_id) {
            //添加到对象评论列表
            $pipe->ZADD($key, $now, $comment_id);
            //对象的评论数量+1
            $pipe->HINCRBY($object_type . ':' . $object_id, 'comments', 1);
            //对象所属人,总评论数+1
            $pipe->HINCRBY('user:' . $object_uid, 'comments', 1);
        });
        $messageService = new MessageService();
        if ($role == 1) {//如果是分析师
            Redis::ZINCRBY('zanalyst:comment', 1, $uid);
            if ($object_uid != $uid) {//如果评论的不是自己的
                $messageService->createByComment($uid, $object_uid, $comment_id);
            }
        } else {//普通用户
            $messageService->createByComment($uid, $object_uid, $comment_id);
        }
        if ($reply_uid) {//如果回复某人
            if ($reply_uid != $object_uid) {//被回复人,非,本条,老师
                $messageService->createByComment($uid, $reply_uid, $comment_id);
            }
        }
        return $cacheModel;
    }
}