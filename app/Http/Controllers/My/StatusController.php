<?php

namespace App\Http\Controllers\My;

use App\Services\API\DyxcService;
use App\Services\StatusService;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;

class StatusController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->service = new StatusService();
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('my.status.create',$this->data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = [
            'message'=>'required',
        ];
        $this->validate($request,$rules);
        $message = Input::get('message','');
        $image = Input::get('image','');

        $message = $message;// filterImg(filterCss(filterJs($message)));
        $result = $this->service->post($this->uid,$message,$image);
        if($result){
            try {
                $dyxc = new DyxcService();
                $dyxc->send($this->uid,$result['id'],Input::get('NewsType'));
            }catch (\Exception $e){
                return ['result'=>'添加成功,同步第一现场失败'];
            }
            return ['result'=>'添加成功'];
        }
        return response('添加失败',501);
    }
}
