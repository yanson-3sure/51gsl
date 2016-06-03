<?php
namespace App\Services;

use App\User;
use Illuminate\Support\Facades\Redis;

class UserService
{
    protected $prefix = 'user:';
    public function find($id)
    {
        return User::find($id);
    }
    public function get($id)
    {
        $model = Redis::HGETALL($this->prefix.$id);
        if(!$model){
            $model = User::find($id);
            $cacheModel = $this->getCacheModel($model);
            $this->setCacheModel($id,$cacheModel);
            return $cacheModel;
        }else{
            return $model;
        }
    }
    public function save($mobile,$password,$name)
    {
        $user = new User();
        $user->mobile = $mobile;
        $user->password = bcrypt($password);
        $user->name = $name;
        $user->role = 0;
        if($user->save()){
            $cacheModel = $this->getCacheModel($user);
            $this->setCacheModel($user->id,$cacheModel);
            return $cacheModel;
        }
        return [];
    }

    public function chgPassword($mobile,$password)
    {
        $user =  User::where('mobile',$mobile)->first();
        if($user){
            $password = bcrypt($password);
            $user->password = $password;
            if($user->save())
                return $user;
        }
        return null;
    }
    public function chgPasswordById($userid,$password)
    {
        $user =  $this->get($userid);
        if($user){
            $password = bcrypt($password);
            $user->password = $password;
            if($user->save())
                return $user;
        }
        return null;
    }

    public function getCacheModel($model)
    {
        if(is_string($model)){
            $model = json_decode($model);
        }
        if($model){
            return [
                'id'    => $model->id,
                'name'  => $model->name,
                'avatar'    => $model->avatar,
                'role'      => $model->role,
            ];
        }
        return [];
    }

    public function setCacheModel($id,$cacheModel)
    {
        Redis::HMSET($this->prefix.$id,$cacheModel);
    }
}