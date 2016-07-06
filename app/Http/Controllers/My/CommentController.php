<?php

namespace App\Http\Controllers\My;

use App\Services\CommentService;
use App\Services\StatusService;
use App\Services\UserService;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;

class CommentController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->service = new CommentService();
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

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = [
            'comment_object_id'=>'required|integer|min:1',
            'comment_object_type'=>'required|object_type:comment',
            'comment_body' => 'required',
        ];
        $messages = [
            'comment_object_id.min' => '对象不存在',
            'comment_object_id.integer' => '对象不存在',
        ];
        $this->validate($request,$rules,$messages);
        $uid = $this->uid;
        $object_id = Input::get('comment_object_id');
        $object_type = Input::get('comment_object_type');
        $comment = Input::get('comment_body');
        $r_comment = Input::get('r_comment',false);
        $reply_comment_id  = Input::get('reply_comment_id',0);
        $reply_uid = 0;
        if($reply_comment_id>0) {
            $reply_comment = $this->service->get($reply_comment_id);
            if($reply_comment){
                $reply_uid = $reply_comment['uid'];
            }else{
                $reply_comment_id = 0;
            }
        }
        if($reply_uid == $uid){
            return response('自己不能回复自己',501);
        }
        $result = $this->service->save($uid,$comment,$object_id,$object_type,$reply_uid,$reply_comment_id);
        if($result && $result!=-1){
            if($r_comment) {//如果有转发
                $statusService = new StatusService();
                $statusService->post($uid,$comment,0,'comment',$result['id']);
            }
            $result = [
                'uid'=> $uid,
                'name'=> $this->name,
                'comment_id'=> $result['id'],
                'comment' => $comment,
            ];
            if($reply_uid>0){
                $userService = new UserService();
                $reply_user = $userService->get($reply_uid);
                if($reply_user){
                    $result['reply_name'] = $reply_user['name'];
                }
            }
            return $result;
        }elseif($result==-1){
            return response('评论对象已经删除',501);
        }else{
            return response('评论失败',501);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
