<?php

namespace App\Http\Controllers\My;

use App\Services\MessageService;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class MessageController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->service = new MessageService();
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

    public function getNoreadcount()
    {
        $count = $this->service->getNoreadCount($this->uid);
        return ['count'=>$count];
    }
}
