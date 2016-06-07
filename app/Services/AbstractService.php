<?php
namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Redis;

abstract class AbstractService
{

    protected $prefix;
    protected $model;
    protected $noCacheAttributes = [];
    protected $onlyCacheTrue = true;
    protected $onlyCacheTrueExcept = [];

    public function __construct()
    {
        $this->model = $this->createModel();
    }

    protected function createModel()
    {
        $class = '\\'.ltrim($this->model, '\\');

        return new $class;
    }
    /*
     * 获取对象所属用户
     */
    public function getObjectUid($object_type,$object_id)
    {
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
        return $object_uid;
    }
    public function isBlackRole($uid)
    {
        $userService = new UserService();
        $user = $userService->get($uid);
        return isBlackRole($user['role']);
    }
    public function find($id)
    {
        return $this->model->find($id);
    }

    public function get($id){
        $model = Redis::HGETALL($this->getKey($id));
        if(!$model){
            return $this->loadCache($id);
        }else{
            return $model;
        }
    }
    public function gets(array $ids,$unique=true){
        if($ids) {
            if($unique){
                $ids = array_unique($ids);
                sort($ids);
            }
            $prefix = $this->prefix;
            $result = Redis::pipeline(function ($pipe) use($ids,$prefix){
                foreach($ids as $k => $v){
                    $pipe->HGETALL($prefix.$v);
                }
            });
            return array_combine($ids,$result);
        }
        return [];
    }

    public function hget($key,$field)
    {
        return Redis::hget($key,$field);
    }
    public function hgetall($key)
    {
        return Redis::HGETALL($key);
    }

    public function zadd($key,$score,$member=null)
    {
        if($member){
            return Redis::zadd($key,$score,$member);
        }
        return Redis::zadd($key,$score);
    }

    public function zexist($key,$member)
    {
        return $this->zscroe($key,$member);
    }
    public function zscroe($key,$member)
    {
        return Redis::zscroe($key,$member);
    }

    public function zrevranges($ids,$length=0,$WITHSCORES=false)
    {
        $result = Redis::pipeline(function($pipe)use($ids,$length,$WITHSCORES){
            foreach($ids as $id){
                $key = $this->prefix . $id;
                if($length==0){
                    $length = 1000;
                }
                if($WITHSCORES){
                    $pipe->zrevrange($key,0,$length-1,'WITHSCORES');
                }else{
                    $pipe->revrange($key,0,$length-1);
                }
            }
        });
        return array_combine($ids,$result);
    }
    public function zrevrange($key,$page=1,$count=10,$WITHSCORES=false)
    {
        $start = ($page-1)*$count;
        $stop = $page*$count-1;
        if($WITHSCORES){
            $result = Redis::zrevrange($key,$start,$stop,'WITHSCORES');
        }else{
            $result = Redis::zrevrange($key,$start,$stop);
        }
        return $result;
    }
    public function zrange($key,$page=1,$count=10,$WITHSCORES=false,$isGets=true)
    {
        $start = ($page-1)*$count;
        $stop = $page*$count-1;
        if($WITHSCORES){
            $result = Redis::zrange($key,$start,$stop,'WITHSCORES');
        }else{
            $result = Redis::zrange($key,$start,$stop);
        }
        if($isGets)
        {
            return $this->gets($result);
        }
        return $result;
    }

    public function zrevrangebyscore($key,$start,$length,$max=0,$isEqual=false,$WITHSCORES=false)
    {
        if(!$max) {
            $max = '+inf';
        }
        if(!$isEqual){
            $max = '(' . $max;
        }
        $arguments = ['limit' => [$start, $length]];
        if($WITHSCORES){
            $arguments = ['WITHSCORES'=>true];
        }
        return Redis::ZREVRANGEBYSCORE($key,$max,'-inf',$arguments);
    }
    public function zrangebyscore($key,$start,$length,$min='-inf',$isEqual=false,$WITHSCORES=false)
    {
        if(!$isEqual){
            $min = '(' . $min;
        }
        $arguments = ['limit' => [$start, $length]];
        if($WITHSCORES){
            $arguments = ['WITHSCORES'=>true];
        }
        return Redis::ZRANGEBYSCORE($key,$min,'+inf',$arguments);
    }

    public function loadCache($id)
    {
        $model = $this->find($id);
        if($model) {
            $cacheModel = $this->getCacheModel($model);
            $this->setCacheModel($id, $model);
            return $cacheModel;
        }
        return [];
    }
    //加载models入cache
    public function loadCaches()
    {

    }

    public function getPrefix()
    {
        return $this->prefix;
    }
    public function getModel()
    {
        return $this->model;
    }
    public function getNoCacheAttributes()
    {
        return $this->noCacheAttributes;
    }
    public function getCacheModel(Model $model)
    {
        $result = array_diff_key($model->getAttributes(),array_flip($this->noCacheAttributes));

        if($this->onlyCacheTrue){
            $delKey = [];
            foreach($result as $k => $v){
                if(!$v){
                    if(!in_array($k,$this->onlyCacheTrueExcept)){
                        $delKey[$k] = '';
                    }
                }
            }
            $result = array_diff_key($result,$delKey);
        }
        return $result;
    }
    public function getKey($id,$object_type=null)
    {
        if($object_type){
            return $this->prefix.$object_type.':'.$id;
        }
        return $this->prefix.$id;
    }
    public function getKeys($ids,$unique = false)
    {
        if(!is_array($ids)) return [];
        $result = [];
        //删除重复值
        if($unique) {
            $ids = array_unique($ids);
            sort($ids);
        }
        foreach($ids as $k=>$v){
            $result[$v] = $this->getKey($v);
        }
        return $result;
    }
    public function setCacheModel($id,$model)
    {
        if(is_array($model)){
            $cacheModel = $model;
        }else{
            $cacheModel = $this->getCacheModel($model);
        }
        Redis::HMSET($this->getKey($id),$cacheModel);
    }
    public function setCache($id,$key,$value)
    {
        Redis::HSET($this->getKey($id),$key,$value);
    }
}