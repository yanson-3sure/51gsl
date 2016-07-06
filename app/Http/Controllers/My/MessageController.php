<?php

namespace App\Http\Controllers\My;

use App\Services\MessageService;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;

class MessageController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->service = new MessageService();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
//        $length = config('base.page_size');
//        $type = Input::get('type');
//        $max = Input::get('max',0);
        $this->data['type'] = Input::get('type');
//        $messages = [];
//        if($type=='noread'){
//            if($this->service->getNoreadBefore($this->uid)) { //获取前准备成功
//                $noreadids = $this->service->getNoreadList($this->uid, 0, $length, 0, true);
//                if ($noreadids) {
//                    $this->data['max'] = last($noreadids);
//                    $messages = $this->service->gets(array_keys($noreadids));
//                }
//            }
//        }else {
//            $messages = $this->service->getList($this->uid, $length,$max);
//        }
//        if (count($messages)>0) {
//            $listDetail = $this->service->getListDetail($messages);
//            $last = last($listDetail);
//            if($type=='noread') {
//
//            }else{
//                $this->data['max'] = $last['id'];
//            }
//            $this->data['messages'] = $listDetail;
//        } else {
//            $this->data['max'] = 0;
//            $this->data['messages'] = [];
//        }
//        if(Input::get('debug','')==1){
//            dd($this->data);
//        }
        return view('my.message.index', $this->data);
    }

    public function getRevList()
    {
        $length = config('base.page_size');
        $type = Input::get('type');
        $max_score = Input::get('max', 0);
        $max = 0;
        $messages = [];
        if ($type == 'noread') {
            $prepare = false;
            if ($max_score == 0) {
                $prepare = $this->service->getNoreadBefore($this->uid);
            }//获取前准备成功
            if(($max_score==0 && $prepare) || ($max_score>0)) {
                $noreadids = $this->service->getNoreadList($this->uid, 0, $length + 1, $max_score, true);
                if ($noreadids) {
                    $max = last($noreadids);
                    $messages = $this->service->gets(array_keys($noreadids));
                }
            }
        }else {
        $messages = $this->service->getList($this->uid, $length + 1, $max_score);
    }

        if (count($messages) == $length + 1) {
            $this->data['isMore'] = true;
            $messages->pop();
        } else {
            $this->data['isMore'] = false;
        }
        if (count($messages) > 0) {

            $listDetail = $this->service->getListDetail($messages);
            $last = last($listDetail);
            if ($type == 'noread') {
                $max = strtotime($last['created_at']);
            } else {
                $max = $last['id'];
            }
            $this->data['messages'] = $listDetail;
        } else {
            $this->data['messages'] = [];
        }
        if (Input::get('debug') == 1) {
            dd($this->data);
        }
        return ['max' => $max, 'isMore' => $this->data['isMore'], 'content' => view('my.message.common.list', $this->data)->render()];
    }

    public function getNoreadcount()
    {
        $count = $this->service->getNoreadCount($this->uid);
        return ['count' => $count];
    }
}
