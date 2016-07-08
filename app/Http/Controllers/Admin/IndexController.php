<?php

namespace App\Http\Controllers\Admin;

use App\Services\ApplicationService;
use App\Services\UserService;
use App\Services\VideoService;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;

class IndexController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    public function getAudit(Request $request)
    {
        $rules = [
            'uid'=>'required',
        ];
        $this->validate($request,$rules);
        $uid = Input::get('uid');
        $aService = new ApplicationService();
        if($aService->audit($uid,$this->user['name'])){
            return '成功';
        }else{
            return '失败';
        }
    }

    public function getUpdateAllVodList()
    {
        $page = Input::get('page',1);
        $startTime='2000-01-01 00:00:00';
        $endTime='9999-01-01 00:00:00';
        $service = new VideoService();
        $service->updateAllVodList();
        //$service->updateVodList($startTime,$endTime,$page);
    }

    public function getPingbi()
    {
        $uid = Input::get('uid');
        $service = new UserService();
        $service->chgRoleById($uid,-1);
    }
}
