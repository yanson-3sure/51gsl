<?php

namespace App\Http\Controllers\My;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use App\Services\PraiseService;

class PraiseController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->service = new PraiseService();
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        $rules = [
            'object_id'=>'required|integer|min:1',
            'object_type'=>'required|object_type:praise'
        ];
        $messages = [
            'object_id.min' => '对象不存在',
            'object_id.integer' => '对象不存在',
        ];
        $this->validate($request,$rules,$messages);
        $object_id = Input::get('object_id',0);
        $object_type = Input::get('object_type','status');
        if($this->service->save($this->uid,$object_id,$object_type)){
            return ['result'=>'success'];
        }
        return response('已经赞过',501);
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $object_id = Input::get('object_id',0);
        $object_type = Input::get('object_type','status');
        if($object_id<1 || !validObjectType($object_type,'praise')) {
            response('对象或类型不正确');
        }
        if($this->service->delete($this->uid,$object_id,$object_type)){
            return ['result'=>'success'];
        }
        return response('已经取消赞',501);
    }
}
