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
}