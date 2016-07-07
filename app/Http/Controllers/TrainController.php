<?php

namespace App\Http\Controllers;

use App\Models\Train;
use App\Services\UserService;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Gensee\Facades\Gensee;
use App\Services\OrderService;

class TrainController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $models = Train::take(100)->get();
        $this->data['models'] = $models;
        $all_uid = [];
        foreach($models as $model){
            $all_uid[] = $model->uid;
        }
        $userService = new UserService();
        $this->data['users'] = $userService->getAvatarAndName($all_uid);
        return view('train.index',$this->data);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $model = Train::find($id);
        if($model) {
            $model->views = $model->views + 1;
            $model->save();
            $useService = new UserService();
            $user = $useService->get($model->uid);
            $model['user'] = $user;
            $this->data['model'] = $model;
            $orderService = new OrderService();
            if ($orderService->has($this->uid, $model->uid)) {
                return view('train.show', $this->data);
            }
            return view('train.show_vip', $this->data);
        }
        abort(404);
    }

}
