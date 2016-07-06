<?php

namespace App\Http\Controllers\Ajax;

use App\Models\Strategy;
use App\Models\Track;
use App\Services\OrderService;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;

class TrackController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $page = config('base.page_size');
        $id = Input::get('id');
        $max = Input::get('max',0);
        $strategy = Strategy::find($id);
        $orderService = new OrderService();
        if(!$orderService->hasPermissions($this->uid,$strategy)){
            return '没有权限';
        }
        $select = Track::where('strategy_id',$id);
        if($max){
            $select->where('created_at','<',date('Y-m-d H:i:s',$max));
        }
        $select->orderBy('id','desc');
        $models = $select->take($page+1)->get();

        if(count($models)==$page+1){
            $this->data['isMore'] = true;
            $models->pop();
        }else{
            $this->data['isMore'] = false;
        }
        $this->data['max'] = strtotime(last($models->all())['created_at']);
        $this->data['tracks'] = $models;

        return ajaxView('ajax.track.list',$this->data);
    }
}
