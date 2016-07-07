<?php
namespace App\Services\MyTraits;

use App\Models\Answer;
use App\Models\Strategy;
use App\Models\Train;
use App\Services\StatusService;

trait Common{
    /*
     * 获取对象所属用户
     */
    public function getObjectUid($object_type,$object_id)
    {
        $object_uid = 0;
        switch($object_type) {
            case 'status':
                $statusService = new StatusService();
                $object = $statusService->get($object_id);
                if(!$object){
                    return false;
                }
                $object_uid = $object['uid'];
                break;
            case 'answer':
                $object = Answer::find($object_id);
                if(!$object){
                    return false;
                }
                $object_uid = $object['uid'];
                break;
            case 'train':
                $object = Train::find($object_id);
                if(!$object){
                    return false;
                }
                $object_uid = $object['uid'];
                break;
            case 'strategy':
                $object = Strategy::find($object_id);
                if(!$object){
                    return false;
                }
                $object_uid = $object['uid'];
                break;
        }
        return $object_uid;
    }

    public function result($content='成功',$status=200)
    {
        return collect(['status' => $status, 'content' => $content]);
    }
}