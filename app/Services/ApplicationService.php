<?php
namespace App\Services;


use App\Models\Analyst;
use App\Models\Application;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;


class ApplicationService
{

    public function audit($uid,$audit_name)
    {
        $userService = new UserService();
        $user = $userService->find($uid);
        if($user && $user->role==0){//普通用户
            $application = Application::where('uid',$uid)->first();
            if($application && $application->status == 0){//申请状态
                $analystService = new AnalystService();
                $analyst = $analystService->find($uid);
                if(!$analyst){
                    $analyst = new Analyst();
                    $analyst->uid = $uid;
                }
                $analyst->status = 1;
                $analyst->role_name = $application->role_name;
                $analyst->feature = $application->feature;
                $analyst->application_id = $application->id;

                $user->role = 1;

                $application->status = 1;
                $application->audit_at = Carbon::now();
                $application->audit_name = $audit_name;


                DB::transaction(function ()use($user,$application,$analyst) {
                    $user->save();
                    $application->save();
                    $analyst->save();
                });
                $chkUser = $userService->find($uid);
                if($chkUser->role==1){//成功
                    $userService->loadCache($uid);
                    $analystService->loadCache($uid);
                    $analystService->initCache($uid);
                    return true;
                }else{
                    return -1;
                }
            }
            return -2;
        }
        return -3;
    }
}