<?php
namespace App\Services;


use App\Models\Praise;
use Carbon\Carbon;
use Illuminate\Support\Facades\Redis;


class PraiseService extends AbstractService
{
    protected $prefix = 'praise:';
    protected $model = Praise::class;


   public function hmgets_count($ids,$object_type)
   {
       if(!$ids) return [];
       $ids = array_unique($ids);
       $this->prefix = $object_type.':';
       $keys = $this->getKeys($ids);
       $this->prefix = 'praise:';
       $result = Redis::pipeline(function ($pipe) use($keys){
           foreach($keys as $key){
               $pipe->HMGET($key,'praises');
           }
       });
       foreach($result as $k => $v){
           if($v) {
               $result[$k] = array_combine(['praises'], $v);
           }else{
               $result[$k] = ['praises'=>0];
           }
       }
       return array_combine($ids,$result);
   }

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
//    public function gets($ids,$unique=true)
//    {
//        return Praise::whereIn('id',$ids)
//            ->orderby('id','desc')
//            ->get();
//    }
//    public function getMessagePraises($ids)
//    {
//        if(!$ids) return [];
//        $models = $this->gets($ids);
//        //$all_uid = [];
//        $all_object_id = [];
//        $all_model = [];
//        foreach($models as $k => $v){
//            $all_model[$v->id] = $this->getCacheModel($v);
//            $model = $all_model[$v->id];
//            //$all_uid[] = $model['uid'];
//            $all_object_id[$model['object_type']][] = $model['object_id'];
//
//        }
//        //获取所有相关用户
//        //$userService = new UserService();
//        //$all_user = $userService->getNames($all_uid);
//
//    }

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
        if(!$object_uid)return -1;
        $userService = new UserService();
        $object_user = $userService->get($object_uid);
        $object_user_isAnalyst = $object_user['role']==1;
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
            Redis::pipeline(function ($pipe)use($key,$now,$uid,$object_uid,$object_type,$object_id,$object_user_isAnalyst) {
                //添加到对象赞列表
                $pipe->ZADD($key, $now, $uid);
                //对象的赞数量+1
                $pipe->HINCRBY($object_type . ':' . $object_id, 'praises', 1);
                //对象所属人,总赞数+1
                $pipe->HINCRBY('user:' . $object_uid, 'praises', 1);
                if($object_user_isAnalyst) {//分析师,排行+1
                    //总赞排行+1
                    $pipe->ZINCRBY('zanalyst:praises', 1, $object_uid);
                }
            });
            //添加到消息列表
            if($uid!=$object_uid) {//如果自己给自己点赞,不需要消息
                //如果赞 是 软删除,则,不发送消息
                if(!$isSoftDelete) {
                    $messageService = new MessageService();
                    $messageService->createByPraise($uid, $object_uid, $object_id,'praise:'.$object_type);
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
        $object_uid = $this->getObjectUid($object_type,$object_id);
        if(!$object_uid) return -1;
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
                //总赞排行-1
                $pipe->ZINCRBY('zanalyst:praises', -1, $uid);
            });
            return true;
        }
        return false;
    }
}