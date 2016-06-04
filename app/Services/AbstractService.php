<?php
namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Redis;

abstract class AbstractService
{

    protected $prefix;
    protected $model;
    protected $noCacheAttributes;

    public function __construct()
    {
        $this->model = $this->createModel();
    }

    protected function createModel()
    {
        $class = '\\'.ltrim($this->model, '\\');

        return new $class;
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
    public function gets(array $ids){
        if($ids) {
            $result = [];
            $prefix = $this->prefix;
            Redis::pipeline(function ($pipe) use($ids,$prefix,&$result){
                foreach($ids as $k => $v){
                    $result[$v] = Redis::HGETALL($prefix.$v);
                }
            });
            return $result;
        }
        return [];
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
        return array_diff_key($model->getAttributes(),array_flip($this->noCacheAttributes));
    }
    public function getKey($id)
    {
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