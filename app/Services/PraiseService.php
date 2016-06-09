<?php
namespace App\Services;


use App\Models\Praise;
use Carbon\Carbon;
use Illuminate\Support\Facades\Redis;


class PraiseService extends AbstractService
{
    protected $prefix = 'praise:';
    protected $model = Praise::class;



    public function zgets($ids,$object_type='status',$length=10)
    {
        $this->prefix = $this->prefix.$object_type.':';
        $result = $this->zrangebyscores($ids,$length);
        $all_user_id = [];
        foreach($result as $k => $v){
            $all_user_id = array_merge($all_user_id,$v);
        }
        $userService = new UserService();
        $all_user = $userService->getAvatars($all_user_id);
        foreach($result as $k => $v){
            $praise_user = [];
            foreach($v as $uid){
                $praise_user[$uid] = $all_user[$uid];
            }
            $result[$k] = $praise_user;
        }
        return $result;
    }

    /*
     * object_type : status
     */
    public function save($uid,$object_id,$object_type='status')
    {
        $key = $this->getKey($object_id,$object_type);
        if($this->zexist($key,$uid)){//已经存在
            return false;
        }

        $object_uid = $this->getObjectUid($object_type,$object_id);
        $now1 = Carbon::now();
        $now = strtotime($now1);
        $praise = Praise::withTrashed()
            ->where('uid',$uid)
            ->where('object_id',$object_id)
            ->where('object_type',$object_type)
            ->first();
        $isSoftDelete = false;
        if($praise){//如果存在
            $isSoftDelete = true;
            $result = $praise->restore();
        }else{
            $result = $this->add($uid,$object_id,$object_type,$now1);
        }
        if($result){//如果添加成功
            Redis::pipeline(function ($pipe)use($key,$now,$uid,$object_uid,$object_type,$object_id) {
                //添加到对象赞列表
                $pipe->ZADD($key, $now, $uid);
                //对象的赞数量+1
                $pipe->HINCRBY($object_type . ':' . $object_id, 'praises', 1);
                //对象所属人,总赞数+1
                $pipe->HINCRBY('user:' . $object_uid, 'praises', 1);
            });
            //添加到消息列表
            if($uid!=$object_uid) {//如果自己给自己点赞,不需要消息
                //如果赞 是 软删除,则,不发送消息
                if(!$isSoftDelete) {
                    $messageService = new MessageService();
                    $messageService->createByPraise($uid, $object_uid, $object_id);
                }
            }
            return true;
        }
        return false;
    }

    protected function add($uid,$object_id,$object_type,$created_at)
    {
        $model = new  Praise();
        $model->uid = $uid;
        $model->object_id = $object_id;
        $model->object_type = $object_type;
        $model->created_at = $created_at;
        return $model->save();
    }

    public function delete($uid,$object_id,$object_type='status')
    {
        $key = $this->getKey($object_id,$object_type);
        if(!$this->zexist($key,$uid)){//已经不存在
            return true;
        }
        $object_uid = 0;
        switch($object_type) {
            case 'status':
                $statusService = new StatusService();
                $object = $statusService->get($object_id);
                if(!$object){
                    return false;
                }
                $object_uid = $object['uid'];
                break;
        }
        $result = Praise::withTrashed()
            ->where('uid',$uid)
            ->where('object_id',$object_id)
            ->where('object_type',$object_type)
            ->delete();
        if($result){
            Redis::pipeline(function ($pipe)use($key,$uid,$object_uid,$object_type,$object_id) {
                //删除赞列表中对象
                $pipe->ZREM($key, $uid);
                //对象的赞数量-1
                $pipe->HINCRBY($object_type . ':' . $object_id, 'praises', -1);
                //对象所属人,总赞数-1
                $pipe->HINCRBY('user:' . $object_uid, 'praises', -1);
            });
        }
        return true;
    }
}