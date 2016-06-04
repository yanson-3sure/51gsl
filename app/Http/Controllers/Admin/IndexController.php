<?php

namespace App\Http\Controllers\Admin;

use App\Services\ApplicationService;
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
}
