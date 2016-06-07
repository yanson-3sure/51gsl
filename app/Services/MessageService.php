<?php

namespace App\Services;


use Illuminate\Support\Facades\Redis;
use App\Models\Message;
use Carbon\Carbon;

/*
 * 消息
 * 事件类型 praise comment
 */
class MessageService extends AbstractService
{
    protected $prefix_message_noread = 'message_noread';
    protected $prefix_zmessage_noread = 'zmessage:noread:';
    protected $prefix_zmessage_noread_old = 'zmessage:noread:old:';

    protected $model = Message::class;
    protected $onlyCacheTrueExcept = 'message';


    /*
     * 赞消息  如果赞过,消息不变
     */
    public function createByPraise($from_uid,$to_uid,$event_id)
    {
        $event_type = 'praise';
        return $this->create($from_uid,$to_uid,$event_id,$event_type);
    }
    public function createByComment($from_uid,$to_uid,$event_id)
    {
        $event_type = 'comment';
        return $this->create($from_uid,$to_uid,$event_id,$event_type);
    }
    protected function create($from_uid,$to_uid,$event_id,$event_type,$msg=null)
    {
        $message = new Message();
        $message->from_uid = $from_uid;
        $message->to_uid = $to_uid;
        $message->event_id = $event_id;
        $message->event_type = $event_type;
        if($msg)
            $message->message = $msg;
        if($message->save()){
            $this->noread_incr($message);
            return true;
        }
        return false;
    }
    /*
     * 取消赞 ,只将未读数量-1,消息不变
     */
//    public function cancelByPraise($to_userid)
//    {
//        $this->noread_decr($to_userid);
//    }
    /*
     * 未读+1
     */
    protected function noread_incr($message)
    {
        //$increment = 1;
        $prefix_message_noread = $this->prefix_message_noread;
        $prefix_zmessage_noread = $this->prefix_zmessage_noread;
        Redis::pipeline(function($pipe) use($message,$prefix_message_noread,$prefix_zmessage_noread){
            $pipe->hincrby('user:'.$message->to_uid, $prefix_message_noread ,1);
            $pipe->zadd($prefix_zmessage_noread . $message->to_uid, strtotime($message->created_at), $message->id);
        });
    }
//    protected function noread_decr($to_uid)
//    {
//        Redis::HINCRBY('user:'. $to_uid,$this->prefix_message_noread,-1);
//    }

    public function getNoread($uid)
    {
        $count = Redis::hget('user:'. $uid ,$this->prefix_message_noread);
        if($count && $count>0){
            return $count;
        }
        return 0;
    }

    public function getNoreadBefore($uid)
    {
        $count = $this->getNoreadCount($uid);
        if($count>0) {
            // 统计数=0
            Redis::hset('user:'. $uid ,$this->prefix_message_noread,0);
            //将 原key重命名
            Redis::rename($this->prefix_zmessage_noread. $uid, $this->prefix_zmessage_noread_old . $uid);
        }
        return $count;
    }
    public function getNoreadList($uid,$start,$length,$max=0,$WITHSCORES=false)
    {
        return $this->zrevrangebyscore($this->prefix_zmessage_noread_old.$uid,$start,$length,$max,false,$WITHSCORES);
    }

    public function getList($to_uid,$length,$maxid=0)
    {
        if($maxid){
            return Message::where('to_userid',$to_uid)
                ->where('message_id','<',$maxid)
                ->orderby('message_id','desc')
                ->take($length)
                ->get();
        }else{
            return Message::where('to_userid',$to_uid)
                ->orderby('message_id','desc')
                ->take($length)
                ->get();
        }
    }

    public function gets($ids)
    {
        return Message::whereIn('message_id',$ids)
            ->orderby('message_id','desc')
            ->get();
    }

    public function getListDetail($messages)
    {
        //收集各type数据
        $all_user_ids = [];
        $all_status_ids = [];
        $all_comment_ids = [];
        $all_image_ids = [];

        $all_message = [];
        //dd($messages);
        foreach($messages as $message)
        {
            $all_message[$message->message_id] = $this->getCacheModel($message);
            $all_user_ids[] = $message->from_userid;
            $all_user_ids[] = $message->to_userid;
            switch($message->event_type)
            {
                case 'praise':
                    $all_status_ids[] = $message->event_id;
                    break;
                case 'comment':
                    $all_comment_ids[] = $message->event_id;
                    break;
                default:
                    break;
            }
        }
        //处理comment
        $commentService = new CommentService();
        $all_comment = $commentService->gets($all_comment_ids,true);
        //遍历所有评论取出用户id,方便拿用户 昵称  及 statusid
//        $comment_user_ids = $commentService->getCommentUserIds($all_comment);
//        if($comment_user_ids)
//            $all_user_ids = array_merge($all_user_ids, $comment_user_ids);
        $all_reply_comment_ids = [];
        foreach ($all_comment as $comment) {
            $all_user_ids[] = $comment->userid;
            $all_status_ids[] = $comment->statusid;
            if ($comment->reply_userid && $comment->reply_userid>0) {
                $all_user_ids[] = $comment->reply_userid;
            }
            if(isset($comment->reply_commentid)){
                if(!in_array($comment->reply_commentid,$all_comment_ids)){
                    $all_reply_comment_ids[] = $comment->reply_commentid;
                }
            }
        }
        //处理回复评论
        if($all_reply_comment_ids) {
            $all_reply_comment_ids = array_unique($all_reply_comment_ids);
            sort($all_reply_comment_ids);
            $all_reply_comment = $commentService->gets($all_reply_comment_ids,true);
            $all_comment = $all_comment + $all_reply_comment;
        }

        //不考虑转发
        //处理 直播
        $all_status_ids = array_unique($all_status_ids);
        sort($all_status_ids);

        $statusService = new StatusService();
        $all_status = $statusService->gets($all_status_ids,true);
        //遍历直播,拿image_ids   user_id
        foreach($all_status as $status) {
            $all_image_ids[] = $status->imageid;
            $all_user_ids[] = $status->userid;
        }

        //获取所有图片
        $imageSerive = new ImageService();
        $all_images = $imageSerive->gets($all_image_ids,true);
        //dd($all_images);
        //获取所有用户信息
        $all_user_ids = array_unique($all_user_ids);
        sort($all_user_ids);
        $userService = new UserService();
        $all_user = $userService->getProfiles($all_user_ids,true);


        foreach($all_message as $k => $v){
            $all_message[$k]['from_user'] = $userService->getCacheModel($all_user[$v['from_userid']]);
            $all_message[$k]['to_user'] = $userService->getCacheModel($all_user[$v['to_userid']]);
            $status_id = 0;
            switch($v['event_type']){
                case 'praise':
                    $all_message[$k]['body'] = [

                    ];
                    $status_id = $v['event_id'];
                    break;
                case 'comment':
                    $all_message[$k]['body'] = $commentService->getCacheModel($all_comment[$v['event_id']]);
                    $all_message[$k]['body']['user'] = $userService->getCacheModel($all_user[$all_message[$k]['body']['userid']]);
                    $reply_userid = $all_message[$k]['body']['reply_userid'];
                    if($reply_userid) {
                        $all_message[$k]['body']['reply_user'] = $userService->getCacheModel($all_user[$reply_userid]);
                    }
                    $reply_commentid = $all_message[$k]['body']['reply_commentid'];
                    if($reply_commentid) {
                        $all_message[$k]['body']['reply_comment'] = $commentService->getCacheModel($all_comment[$reply_commentid]);
                    }
                    $status_id = $all_comment[$v['event_id']]->statusid;
                    break;
                default:
                    break;
            }
            if($status_id) {
                $all_message[$k]['status'] = $statusService->getCacheModel($all_status[$status_id]);
                if ($all_message[$k]['status'] && $all_message[$k]['status']['imageid'] > 0) {
                    $all_message[$k]['status']['image'] = $imageSerive->getCacheModel($all_images[$all_message[$k]['status']['imageid']]);
                }
            }
        }
        //dd($all_message);
        return $all_message;

    }

}