<?php

namespace App\Http\Controllers\My;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class IndexController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (Auth::check()) {
            $this->data['noreadcount'] = 0;
            //$messageService = new MessageService();
            //$this->data['noreadcount'] = $messageService->getNoreadCount($this->userid);
            //var_dump($this->data);

        } else {
            $this->data['noreadcount'] = 0;
        }
        return view('my.index', $this->data);
    }
}
