<?php

namespace App\Http\Controllers;

use App\Models\Strategy;
use App\Services\UserService;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Services\OrderService;
use Illuminate\Support\Facades\Input;

class StrategyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('strategy.index', $this->data);
    }


    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $model = Strategy::find($id);
        if ($model) {
            $model->views = $model->views + 1;
            $model->save();
            $useService = new UserService();
            $user = $useService->get($model->uid);
            $model['user'] = $user;
            $this->data['model'] = $model;
            $orderService = new OrderService();

            if($model->vip && !$orderService->has($this->uid,$model->uid)){
                return view('strategy.show_vip', $this->data);
            }
            return view('strategy.show', $this->data);

        }
    }

}
