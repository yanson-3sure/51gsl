<?php
namespace App\Services;

use App\Models\Answer;
use App\Models\Question;
use App\Services\MyTraits\Common;

class QAService
{
    use Common;

    public function saveQuestion($object_type,$object_id,$content,$uid)
    {
        $model = new Question();
        $model->object_type = $object_type;
        $model->object_id = $object_id;
        $model->content = $content;
        $model->uid = $uid;
        $model->to_uid = $this->getObjectUid($object_type,$object_id);
        return $model->save();
    }
    public function questionAdd($id)
    {
        $model = Question::find($id);
        if($model){
            $model->answers = $model->answers+1;
            $model->save();
            return true;
        }
        return false;
    }

    public function saveAnswer($uid,$question_id,$content,$image=null)
    {
        $model = new Answer();
        $model->uid = $uid;
        $model->question_id = $question_id;
        $model->content = $content;
        if($image){
            $model->image = $image;
        }
        if($model->save()){
            $this->questionAdd($question_id);
            return true;
        }
        return false;
    }

    public function getForwardAnswer($ids)
    {
        if(!$ids) return [];
        $temp_models = Answer::whereIn('id',$ids)->get();
        $all_uid = [];
        $models = [];
        foreach ($temp_models as $k => $v) {
            if($v) {
                $all_uid[] = $v['uid'];
                $models[$v->id] = $v->toArray();
            }
        }
        //获取所有相关用户
        $userService = new UserService();
        $all_user = $userService->getBases($all_uid);//获取所有用户
        foreach ($models as $k => $v) {
            if($v) {
                $models[$k]['user'] = $all_user[$v['uid']];
            }
        }
        return $models;
    }
}