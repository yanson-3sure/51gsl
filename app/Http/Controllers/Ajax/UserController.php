<?php

namespace App\Http\Controllers\Ajax;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use App\Services\AnalystService;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $type = Input::get('type','');
        $this->data['type'] = $type;
        $analystService = new AnalystService();
        switch($type){
            case 2:
                $this->data['users'] = $analystService->getRankByFollower($this->uid);
                break;
            case 3:
                $this->data['users'] = $analystService->getRankByComment($this->uid);
                break;
            default :
                $this->data['users'] = $analystService->getRankByStatus($this->uid);
                break;
        }
        $this->data['users'] = $analystService->filter($this->data['users']);
        $this->debug();
        return ajaxView('ajax.user.list',$this->data);
    }
}
