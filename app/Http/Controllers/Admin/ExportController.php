<?php

namespace App\Http\Controllers\Admin;

use App\Models\Analyst;
use App\Models\Application;
use App\Models\Comment;
use App\Models\Follow;
use App\Models\Image;
use App\Models\Message;
use App\Models\Praise;
use App\Models\Status;
use App\Models\UserWechat;
use App\Services\CommentService;
use App\Services\FollowService;
use App\Services\ImageService;
use App\Services\PraiseService;
use App\Services\StatusService;
use App\Services\UserService;
use App\Services\AnalystService;
use App\User;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class ExportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getDo()
    {
        if(false) {
            //处理用户
            $this->users();

            //处理微信UNIONID对应关系
            $this->user_wechat_ids();

            //处理申请表
            $this->applies();

            //处理分析师表
            $this->analysts();

            //处理直播
            $this->statuses();

            //处理 图片
            $this->images();

            //赞
            $this->praises();

            //评论
            $this->comments();

            //关注
            $this->follows();
        }
        //消息
        $this->messages();



    }

    protected function users()
    {
        $users = collect(DB::connection('mysql2')->select('select * from users'));
        $user_profiles = collect(DB::connection('mysql2')->select('select * from user_profiles'));
        //dd($user_profiles);
        //处理users
        $all_user = [];
        foreach($users as $k => $v){
            $user = new User();
            $user->id = $v->userid;
            $user->mobile = $v->phone;
            $user->password = $v->password;
            if($user_profiles->contains('userid',$v->userid)) {
                $user_profile = head($user_profiles->where('userid',$v->userid)->all());
                $user->name = $user_profile->nickname;
                $user->role = $user_profile->role;
                $user->avatar = $user_profile->avatar;
            }
            $user->remember_token = $v->remember_token;
            $user->created_at = $v->created_at;
            $user->updated_at = $v->updated_at;
            $user->save();
            $all_user[$v->userid] = $user;
        }
        $userService = new UserService();
        $userService->setCacheModels($all_user);
    }

    public function user_wechat_ids()
    {
        $user_wechat_ids = collect(DB::connection('mysql2')->select('select * from user_wechat_ids'));
        //dd($user_wechat_ids);
        foreach($user_wechat_ids as $k => $v){
            $user_wechat = new UserWechat();
            $user_wechat->uid = $v->userid;
            $user_wechat->unionid = $v->unionid;
            $user_wechat->created_at = $v->create_at;
            $user_wechat->save();
        }
    }

    public function applies()
    {
        $applies = collect(DB::connection('mysql2')->select('select * from applies'));
        foreach($applies as $k => $v)
        {
            $apply = new Application();
            $apply->id = $v->apply_id;

            $apply->uid = $v->userid;
            $apply->id_number = $v->id_number;
            $apply->securities_certificate = $v->securities_certificate;
            $apply->role_name = $v->role_name;
            $apply->feature = $v->feature;
            $apply->status = $v->status;

            $apply->audit_at = $v->audit_at;
            $apply->audit_name = $v->audit_name;
            $apply->audit_reason = $v->audit_reason;
            $apply->created_at = $v->created_at;
            $apply->updated_at = $v->updated_at;

            $apply->save();

        }
    }

    public function analysts()
    {
        $analysts = collect(DB::connection('mysql2')->select('select * from analyst_profiles'));
        $analystService = new AnalystService();
        foreach($analysts as $k => $v){
            $analyst = new Analyst();
            $analyst->uid = $v->userid;
            $analyst->status = $v->status;
            $analyst->role_name = $v->role_name;
            $analyst->feature = $v->feature;
            $analyst->audit_at = $v->audit_at;
            $analyst->application_id = $v->apply_id;
            $analyst->created_at = $v->created_at;
            $analyst->updated_at = $v->updated_at;
            $analyst->save();

            $analystService->loadCache($v->userid);
            $analystService->initCache($v->userid);
        }
    }

    public function statuses()
    {
        $statuses = collect(DB::connection('mysql2')->select('select * from status'));
        $forwards = collect(DB::connection('mysql2')->select('select * from forwards'));
        ///dd($forwards);
        $statusService = new StatusService();
        foreach($statuses as $k => $v)
        {
            $status = new Status();
            $status->id = $v->statusid;
            $status->uid = $v->userid;
            $status->message = $v->body;
            $status->image_id = $v->imageid;

            if($v->forwardid){
                $forward = head($forwards->where('forwardid',$v->forwardid)->all());
                if($forward->reply_commentid) {
                    $status->forward_id = $forward->reply_commentid;
                    $status->forward_type = 'comment';
                }else{
                    $status->forward_id = $forward->statusid;
                    $status->forward_type = 'status';
                }
            }
            $status->created_at = $v->created_at;
            $status->updated_at = $v->updated_at;
            $status->save();
            $uid = $status->uid;
            $cacheModel = $statusService->loadCache($status->id);
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
        }
    }

    public function images()
    {
        $images = collect(DB::connection('mysql2')->select('select * from images'));
        $all_image = [];
        foreach($images as $k => $v){
            $image = new Image();
            $image->id = $v->imageid;
            $image->path = $v->path;
            $image->ext = $v->ext;
            $image->url = $v->url;
            $image->uid = $v->userid;
            $image->valid = $v->valid;
            $image->type = 'status';
            $image->created_at = $v->created_at;
            $image->updated_at = $v->updated_at;
            $image->save();
            $all_image[$image->id] = $image;
        }
        $imageService = new ImageService();
        $imageService->setCacheModels($all_image);
    }

    public function praises()
    {
        $models = collect(DB::connection('mysql2')->select('select * from praises'));
        foreach($models as $k => $v){
            $model = new Praise();
            $model->id = $v->praiseid;
            $model->uid = $v->userid;
            $model->object_id = $v->statusid;
            $model->object_type = 'status';
            $model->created_at = $v->created_at;
            $model->save();
            $service = new PraiseService();
            $now = strtotime($model->created_at);
            $uid = $model->uid;
            $object_id = $model->object_id;
            $object_type = $model->object_type;
            $object_uid = $service->getObjectUid($object_type,$object_id);

            $key = $service->getKey($object_id,$object_type);
            Redis::pipeline(function ($pipe)use($key,$now,$uid,$object_uid,$object_type,$object_id) {
                //添加到对象赞列表
                $pipe->ZADD($key, $now, $uid);
                //对象的赞数量+1
                $pipe->HINCRBY($object_type . ':' . $object_id, 'praises', 1);
                //对象所属人,总赞数+1
                $pipe->HINCRBY('user:' . $object_uid, 'praises', 1);
                //总赞排行+1
                $pipe->ZINCRBY('zanalyst:praises', 1, $uid);
            });
        }
    }

    public function comments()
    {
        $models = collect(DB::connection('mysql2')->select('select * from comments'));
        foreach($models as $k => $v){
            $model = new Comment();
            $model->id = $v->commentid;
            $model->uid = $v->userid;
            $model->object_id = $v->statusid;
            $model->object_type = 'status';
            $model->comment = $v->body;
            $model->reply_uid = $v->reply_userid;
            $model->reply_comment_id = $v->reply_commentid;
            $model->created_at = $v->create_at;
            $model->save();

            $service = new CommentService();
            $comment_id = $model->id;
            $now = strtotime($model->created_at);
            $object_id = $model->object_id;
            $object_type = $model->object_type;
            $object_uid = $service->getObjectUid($object_type,$object_id);
            $key = $service->getKey($object_id,$object_type);

            $cacheModel = $service->getCacheModel($model);
            $service->setCacheModel($cacheModel,$comment_id);
            Redis::pipeline(function ($pipe)use($key,$now,$comment_id,$object_uid,$object_type,$object_id){
                //添加到对象评论列表
                $pipe->ZADD($key, $now, $comment_id);
                //对象的评论数量+1
                $pipe->HINCRBY($object_type . ':' . $object_id, 'comments', 1);
                //对象所属人,总评论数+1
                $pipe->HINCRBY('user:' . $object_uid, 'comments', 1);
            });
            $uid = $model->uid ;
            $userService = new UserService();
            $user = $userService->get($uid);
            $role = $user['role'];
            if ($role == 1) {//如果是分析师
                Redis::ZINCRBY('zanalyst:comment', 1, $uid);
            }
        }
    }

    public function follows()
    {
        $models = collect(DB::connection('mysql2')->select('select * from followings'));
        $service = new FollowService();
        foreach($models as $k => $v) {
//            $model = new Follow();
//            $model->id = $v->fid;
//            $model->uid = $v->userid;
//            $model->f_uid = $v->fuserid;
//            $model->created_at = $v->created_at;
//            $model->save();
            $service->follow_user($v->userid,$v->fuserid,$v->created_at);
        }
    }

    public function messages()
    {
        $models = collect(DB::connection('mysql2')->select('select * from messages'));
        foreach($models as $k => $v) {
            $model = new Message();
            $model->id = $v->message_id;
            $model->message = $v->title;
            $model->event_id = $v->event_id;
            if($v->event_type=='praise') {
                $model->event_type = 'praise:status';
            }else{
                $model->event_type = $v->event_type;
            }
            $model->from_uid = $v->from_userid;
            $model->to_uid = $v->to_userid;
            $model->created_at = $v->created_at;
            $model->updated_at = $v->updated_at;
            $model->save();
        }
    }

}
