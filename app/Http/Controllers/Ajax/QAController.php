<?php

namespace App\Http\Controllers\Ajax;

use App\Models\Answer;
use App\Models\Question;
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
        $page = config('base.page_size');
        $object_type=Input::get('object_type');
        $object_id = Input::get('object_id');
        $max = Input::get('max',0);
        $select = Question::where('object_type',$object_type)
            ->where('object_id',$object_id)
            ->where('answers','>',0);
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

            $answers = Answer::whereIn('question_id', $question_ids)->get();
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
            $this->data['questions'] = $questions;
        }else{
            $this->data['questions'] = [];
        }

        return ajaxView('ajax.qa.list',$this->data) ;
    }
}
