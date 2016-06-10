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
        $length = config('base.page_size');
        $type = Input::get('type');
        $this->data['type'] = $type;
        $messages = [];
        if($type=='noread'){
            if($this->service->getNoreadBefore($this->uid)) { //获取前准备成功
                $noreadids = $this->service->getNoreadList($this->uid, 0, $length, 0, true);
                if ($noreadids) {
                    $this->data['maxscore'] = last($noreadids);
                    $messages = $this->service->gets(array_keys($noreadids));
                }
            }
        }else {
            $messages = $this->service->getList($this->uid, $length);
        }
        if (count($messages)>0) {
            $listDetail = $this->service->getListDetail($messages);
            dd($listDetail);
            $last = last($listDetail);
            if($type=='noread') {

            }else{
                $this->data['max'] = $last['id'];
            }
            $this->data['messages'] = $listDetail;
        } else {
            $this->data['max'] = 0;
            $this->data['messages'] = [];
        }
        return view('my.message',$this->data);
    }

    public function getNoreadcount()
    {
        $count = $this->service->getNoreadCount($this->uid);
        return ['count'=>$count];
    }
}
