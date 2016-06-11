<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Services\StatusService;
use Illuminate\Support\Facades\Input;

class StatusController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->service = new StatusService();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if($id) {
            $status = $this->service->get($id);
            if($status) {
                $statuses = $this->service->getViewListInfo([$status]);
                $this->data['model'] = head($statuses);
                $this->data['detail'] = true;
                return view('status.show',$this->data);
            }
        }
        abort(404);
    }

    public function getRevList()
    {
        $type = Input::get('type');
        $max =  Input::get('max',0);
        $end   = config('base.page_size');

        if($type=='home') {
            $all_status = $this->service->getRevHome($this->uid,0,$end,$max);
        }elseif($type=='profile') {
            $uid = Input::get('uid',0);
            $all_status = $this->service->getRevProfile($uid,0,$end,$max);
        }else {
            $all_status = $this->service->getRevAllHome(0,$end,$max);
        }
        $status = $this->service->getViewListInfo($all_status);
        $this->data['statuses'] = $status ;
        $max = 0;
        $ids = [];
        if($all_status) {
            $max = strtotime(last($all_status)['created_at']);
            $ids = array_keys( $all_status);
        }
        return ['max'=>$max,'ids'=>$ids,'content'=>view('status.common.list',$this->data)->render()];
    }
    public function getList()
    {
        $type = Input::get('type');
        $min =  Input::get('min',0);
        $end   = config('base.page_size');

        if($type=='home') {
            $all_status = $this->service->getHome($this->uid,0,$end,$min);
        }elseif($type=='profile') {
            $uid = Input::get('uid',0);
            $all_status = $this->service->getProfile($uid,0,$end,$min);
        }else {
            $all_status = $this->service->getAllHome(0,$end,$min);
        }
        $status = $this->service->getViewListInfo($all_status);
        $this->data['statuses'] = $status ;
        $min = 0;
        $ids = [];
        if($all_status) {
            $min = strtotime(head($all_status)['created_at']);
            $ids = array_keys( $all_status);
        }
        return ['min'=>$min,'ids'=>$ids,'content'=>view('status.common.list',$this->data)->render()];
    }
}
