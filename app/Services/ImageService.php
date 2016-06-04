<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/4/14
 * Time: 16:23
 */

namespace App\Services;


use App\Models\Image;
use Illuminate\Support\Facades\Redis;


class ImageService extends AbstractService
{
    protected $prefix = 'image:';
    protected $model = Image::class;
    protected $noCacheAttributes = ['created_at','updated_at'];

    public function save($uid,$path,$ext,$type,$url='')
    {
        $model = new Image();
        $model->uid = $uid;
        $model->path = $path;
        $model->ext = $ext;
        $model->valid = 1;
        $model->url = $url;
        $model->type = $type;
        $model->save();
        if($model){
            $cacheModel = $this->getCacheModel($model);
            $this->setCacheModel($model->id,$cacheModel);
            return $cacheModel;
        }
        return false;
    }
}