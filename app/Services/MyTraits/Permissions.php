<?php
namespace App\Services\MyTraits;

trait Permissions{
    //个人用户是否有权限查看
    public function hasPermissions($uid,$model)
    {
        if(isAdmin($uid)){//管理员
            return true;
        }
        if(!$model->vip){//免费
            return true;
        }
        if($model->vip){//vip
            if($model->uid == $uid) { //对象拥有者
                return true;
            }
            //收费用户
        }
        return false;
    }
}