<?php

namespace App\Http\Controllers;

use App\Models\Strategy;
use App\Services\UserService;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class StrategyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $models = Strategy::simplePaginate(10);
        $this->data['models'] = $models;
        $all_uid = [];
        foreach($models as $model){
            $all_uid[] = $model->uid;
        }
        $userService = new UserService();
        $this->data['users'] = $userService->getAvatarAndName($all_uid);
        return view('strategy.index',$this->data);
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
        $model = Strategy::find($id);
        if($model) {
            if(\Illuminate\Support\Facades\Request::ajax()){
                return $model;
            }
            $model->views = $model->views + 1;
            $model->save();
            $useService = new UserService();
            $user = $useService->get($model->uid);
            $model['user'] = $user;
            $this->data['model'] = $model;
            return view('strategy.show',$this->data);
        }
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
        //
    }
}
