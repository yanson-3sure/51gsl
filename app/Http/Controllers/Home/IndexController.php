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
        $type = Input::get('type', 'all_home');
        $this->data['type'] = $type;
        return view('home.index', $this->data);
    }

    public function getLogin()
    {
        $uid = Input::get('uid');
        Auth::loginUsingId($uid);
    }
}
