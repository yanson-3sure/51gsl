<?php

namespace App\Http\Controllers\Ajax;

use App\Models\Strategy;
use App\Services\UserService;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
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
        $page = config('base.page_size');
        //两类,一类,分析师自己的,另一类,所有的
        $max = Input::get('max', 0);
        $uid = Input::get('uid');
        //分析师自己的
        if ($uid > 0) {
            $select = Strategy::where('uid', $uid);
            $this->data['profile'] =true;

        } else {
            $select = Strategy::where('id', '>', '0');
            $this->data['profile'] =false;

        }
        if ($max) {
            $select->where('updated_at', '<', date('Y-m-d H:i:s', $max));
        }
        $select->orderBy('updated_at', 'desc');

        $models = $select->take($page + 1)->get();
        if (count($models) == $page + 1) {
            $this->data['isMore'] = true;
            $models->pop();
        } else {
            $this->data['isMore'] = false;
        }
        $this->data['max'] = strtotime(last($models->all())['created_at']);
        $this->data['models'] = $models;
        $all_uid = [];
        foreach ($models as $model) {
            $all_uid[] = $model->uid;
        }
        $userService = new UserService();
        $this->data['users'] = $userService->getAvatarAndName($all_uid);
        $this->debug(false);
        return ajaxView('ajax.strategy.list',$this->data);
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
        }
    }
}
