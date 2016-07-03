<?php

namespace App\Http\Controllers\My;

use App\Models\Train;
use App\Models\Video;
use App\Services\TrainService;
use App\Services\VideoService;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;

class VideoController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->service = new VideoService();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
    }

    public function getList()
    {
        $train_id = Input::get('id');
        $train = Train::find($train_id);
        if($train) {
            //检查权限
            $webcastid = $train->webcastid;
            $this->data['models'] = Video::where('webcastid', $webcastid)
                ->orderBy('createdTime', 'desc')
                ->take(20)->get();
            //dd($this->data);
            return view('my.video.list', $this->data);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //检查webcastid,是否是当前用户的,或者当前操作者是管理员
        $role = isAdmin($this->uid);
        if(!$role){
            $train = Train::where('webcastid',$id)->first();
            if($train){
                $role = $train->uid == $this->uid;
            }
        }
        if(!$role)return '无权限';
        $this->service->updateVodList();
        return '更新成功';
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
