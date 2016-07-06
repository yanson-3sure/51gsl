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
            if (\Illuminate\Support\Facades\Request::ajax()) {
                return $model;
            }
            $model->views = $model->views + 1;
            $model->save();
            $useService = new UserService();
            $user = $useService->get($model->uid);
            $model['user'] = $user;
            $this->data['model'] = $model;
            return view('strategy.show', $this->data);
        }
    }

}
