<?php
namespace App\Services;


class OrderService
{
    /*
     * 获取某位用户的订阅,分析师ids,返回数组
     */
    public function getUserIds($uid)
    {
        return [7];
    }
    //获取用户,订阅某位老师的订单信息
    public function get($uid,$order_uid)
    {

    }
    //个人用户是否有权限查看
    public function hasPermissions($uid,$model)
    {
        if(!$model){
            return false;
        }
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
            return in_array($uid,$this->getUserIds($uid));
        }
        return false;
    }
}