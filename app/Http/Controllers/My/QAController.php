<?php

namespace App\Http\Controllers\My;

use App\Models\Answer;
use App\Models\Question;
use App\Services\OrderService;
use App\Services\PraiseService;
use App\Services\UserService;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;

class QAController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $page = 10;
        $max = Input::get('max',0);
        $role = Input::get('role',0);
        if($role==0 || $this->role==0){//普通用户,问答
            $select = Question::where('uid',$this->uid);
        }else{//老师获取跟自己相关的问答
            $select = Question::where('to_uid',$this->uid);
        }
        $type = Input::get('type',1); //0:全部  1:已回复(默认)  2:未回复
        switch($type){
            case 1:
                $select->where('answers','>',0);
                break;
            case 2:
                $select->where('answers','=',0);
                break;
        }
        if($max){
            $select->where('created_at','<',date('Y-m-d H:i:s',$max));
        }
        $select->orderBy('id','desc');
        $questions = $select->take($page+1)->get();
        if(count($questions)==$page+1){
            $this->data['isMore'] = true;
            $questions->pop();
        }else{
            $this->data['isMore'] = false;
        }
        $this->data['max'] = strtotime(last($questions->all())['created_at']);
        $question_ids = [];
        $all_user_id = [];
        if($questions) {
            foreach ($questions as $question) {
                $question_ids[] = $question->id;
                $all_user_id[] = $question->uid;
            }
            if($type==2){//如果是未回复的,就不需要再查询
                $answers = collect([]);
            }else {
                $answers = Answer::whereIn('question_id', $question_ids)->get();
            }
            $all_answer_id = [];
            foreach($answers as $answer){
                $all_user_id[] = $answer->uid;
                $all_answer_id[] = $answer->id;
            }

            //获取点赞数
            $praiseService = new PraiseService();
            $praise_counts= $praiseService->hmgets_count($all_answer_id,'answer');

            $is_praises = $praiseService->zscroes($all_answer_id,$this->uid,'answer');
            $userService = new UserService();
            $users = $userService->getBases($all_user_id);
            foreach ($questions as $k=>$question) {
                if($answers->contains('question_id',$question->id)) {
                    $questions[$k]['answer'] = head($answers->where('question_id',$question->id)->all());
                    $questions[$k]['answer']['user'] = $users[$questions[$k]['answer']['uid']];
                    $questions[$k]['answer']['praises'] = $praise_counts[$questions[$k]['answer']['id']]['praises'];
                    $questions[$k]['answer']['is_praise'] = $is_praises[$questions[$k]['answer']['id']];
                }
                $questions[$k]['user'] = $users[$question->uid];
            }
            $this->data['models'] = $questions;
        }else{
            $this->data['models'] = [];
        }
        if(ajax()) {
            return ['max' => $this->data['max'], 'isMore' => $this->data['isMore'], 'content' => view('my.qa.common.list', $this->data)->render()];
        }
        dd($this->data);

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
        //
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
