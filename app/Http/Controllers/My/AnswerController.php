<?php

namespace App\Http\Controllers\My;

use App\Models\Question;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Services\QAService;
use Illuminate\Support\Facades\Input;

class AnswerController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->service = new QAService();
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
        $question_id = Input::get('question_id');
        $question = Question::find($question_id);
        if($question) {
            $this->data['question'] = $question;
            return view('my.answer.create', $this->data);
        }
        return '没找到要回答的问题';
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
            'question_id'=>'required|min:1',
            'content'=>'required',
        ];
        $this->validate($request,$rules);
        $question_id = Input::get('question_id');
        $question = Question::find($question_id);
        if($question && $question->to_uid == $this->uid) {
            if ($this->service->saveAnswer($this->uid, $question_id, Input::get('content'), Input::get('image'))) {
                return ['result' => '添加成功'];
            }
            return response('添加失败', 501);
        }
        return response('没有权限', 501);
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
