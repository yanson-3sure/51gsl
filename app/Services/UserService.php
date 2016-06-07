<?php
namespace App\Services;

use App\User;
use Illuminate\Support\Facades\Redis;

class UserService extends AbstractService
{
    protected $prefix = 'user:';
    protected $model = User::class;
    protected $noCacheAttributes = ['email','mobile','password','remember_token','created_at','updated_at'];
    protected $onlyCacheTrueExcept = ['name','avatar','role'];

    public function save($mobile,$password,$name)
    {
        $user = new User();
        $user->mobile = $mobile;
        $user->password = bcrypt($password);
        $user->name = $name;
        $user->avatar = '';
        $user->role = 0;
        if($user->save()){
            $cacheModel = $this->getCacheModel($user);
            $this->setCacheModel($user->id,$cacheModel);
            return $cacheModel;
        }
        return [];
    }

    public function getMobile($uid)
    {
        $user = $this->find($uid);
        if($user){
            return $user->mobile;
        }
        return '';
    }

    public function bindMobile($uid,$mobile)
    {
        $user = $this->find($uid);
        if($user){
            $user->mobile = $mobile;
            return $user->save();
        }
        return false;
    }

    public function hasMobile($mobile,$uid=0)
    {
        if(!$mobile) return true;
        $user = User::where('mobile',$mobile)->first();
        if($user){
            if($uid>0) {
                if ($user->id == $uid) {
                    return false;
                }
            }
            return true;
        }
        return false;
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
        $user =  $this->find($userid);
        if($user){
            $password = bcrypt($password);
            $user->password = $password;
            if($user->save())
                return $user;
        }
        return null;
    }

    public function chgNameById($uid,$name)
    {
        $model = $this->find($uid);
        if($model) {
            if($name==$model->name){
                return false;
            }
            $model->name = $name;
            if($model->save()) {
                $this->setCache($uid,'name',$name);
                return $model;
            }
        }
        return null;
    }

    public function chgAvatar($uid,$avatar)
    {
        $model = $this->find($uid);
        if($model) {
            if($avatar==$model->avatar){
                return false;
            }
            $model->avatar = $avatar;
            if($model->save()) {
                $this->setCache($uid,'avatar',$avatar);
                return $model;
            }
        }
        return null;
    }
}