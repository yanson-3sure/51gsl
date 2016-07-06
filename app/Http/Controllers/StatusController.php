<?php

namespace App\Http\Controllers;

use App\Models\Status;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Services\StatusService;
use Illuminate\Support\Facades\Input;

class StatusController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->service = new StatusService();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if($id) {
            $status = $this->service->get($id);
            if($status) {
                $statuses = $this->service->getViewListInfo([$status]);
                $this->data['model'] = head($statuses);
                $this->debug(false);
                return view('status.show',$this->data);
            }
        }
        abort(404);
    }



    public function destroy($id)
    {
        if($this->service->delete_status($this->uid,$id)){
            return ['success'=>'删除成功'];
        }
        return response('删除失败',501);
    }
}
