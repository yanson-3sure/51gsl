<?php

namespace App\Http\Controllers\Home;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use App\Services\StatusService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class IndexController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $type = Input::get('type', '');
        $page_size = config('base.page_size');
        $this->data['type'] = $type;
        $start = 0;
        $statusService = new StatusService();
        if ($type == 'my') {
            if (Auth::check()) {
                $all_status = $statusService->getRevHome($this->uid, $start, $page_size);
            } else {
                return Redirect::to('/auth/login');
            }
        } else {
            $all_status = $statusService->getRevAllHome($start, $page_size);
        }
        //dd($all_status);
        if ($all_status) {
            $this->data['max'] = strtotime(last($all_status)['created_at']);
            $this->data['min'] = strtotime(head($all_status)['created_at']);
        } else {
            $this->data['max'] = 0;
            $this->data['min'] = 0;
        }
        //dd($this->data);
        $status = $statusService->getViewListInfo($all_status);
        $this->data['statuses'] = $status;
        if(Input::get('debug','')=='1') {
            dd($this->data);
        }
        return view('home.index', $this->data);
    }

    public function getLogin()
    {
        $uid = Input::get('uid');
        Auth::loginUsingId($uid);
    }
}
