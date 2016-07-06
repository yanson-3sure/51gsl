<?php

namespace App\Http\Controllers\Ajax;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use App\Services\StatusService;

class StatusController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->service = new StatusService();
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $order = Input::get('order',0);
        if($order==0){
            return $this->getList();
        }else{
            return $this->getRevList();
        }
    }

    protected function getRevList()
    {
        $type = Input::get('type');
        $max =  Input::get('max',0);
        $page   = config('base.page_size');

        if($type=='home') {
            $all_status = $this->service->getRevHome($this->uid,0,$page+1,$max);
        }elseif($type=='profile') {
            $uid = Input::get('uid',0);
            $all_status = $this->service->getRevProfile($uid,0,$page+1,$max);
        }else {
            $all_status = $this->service->getRevAllHome(0,$page+1,$max);
        }
        if (count($all_status) == $page + 1) {
            $this->data['isMore'] = true;
            array_pop($all_status);
        } else {
            $this->data['isMore'] = false;
        }
        $status = $this->service->getViewListInfo($all_status);
        $this->data['statuses'] = $status ;
        if($all_status) {
            $this->data['max'] = strtotime(last($all_status)['created_at']);
            $this->data['min'] = strtotime(head($all_status)['created_at']);
        }
        return ajaxView('status.common.list',$this->data);
    }
    protected function getList()
    {
        $type = Input::get('type');
        $min =  Input::get('min',0);
        $page   = config('base.page_size');

        if($type=='home') {
            $all_status = $this->service->getHome($this->uid,0,$page+1,$min);
        }elseif($type=='profile') {
            $uid = Input::get('uid',0);
            $all_status = $this->service->getProfile($uid,0,$page+1,$min);
        }else {
            $all_status = $this->service->getAllHome(0,$page+1,$min);
        }
        if (count($all_status) == $page + 1) {
            $this->data['isMore'] = true;
            array_shift($all_status);
        } else {
            $this->data['isMore'] = false;
        }
        $status = $this->service->getViewListInfo($all_status);
        $this->data['statuses'] = $status ;
        if($all_status) {
            $this->data['max'] = strtotime(last($all_status)['created_at']);
            $this->data['min'] = strtotime(head($all_status)['created_at']);
        }
        return ajaxView('status.common.list',$this->data);
    }
}
