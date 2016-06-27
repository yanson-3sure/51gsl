<?php

namespace Viky88\upload\Http\Controllers;

use App\Services\ImageService;
use App\Services\UserService;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;

class UploadController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function postImage()
    {
        $type = Input::get('type');
        $chk = $this->chk($type);
        if($chk!==true){
            return $chk;
        }
        $filename = $this->putFile($type,Input::file('file'));
        if($filename) {
            return [
                'type' => $type,
                'name' => $filename,
                'path' => getImageUrl($type,$filename),
            ];
        }else{
            return response('上传图片失败',501);
        }
    }
    public function postAvatar()
    {
        $type = 'avatar';
        $chk = $this->chk($type);
        if($chk!==true){
            return $chk;
        }

        $filename = $this->putFile($type,Input::file('file'));
        if($filename){
            $userService = new UserService();
            $userService->chgAvatar($this->uid,$filename);
            return ['path'=>getAvatar($filename)];
        }
        return response('上传头像失败',501);
    }

    protected function putFile($type,$file)
    {
        $ext = strtolower($file->getClientOriginalExtension());
        $filename = getMd5PathRandom($type).'.'.$ext;
        $file_path = $type.'/'.$filename;
        $result = Storage::putFile($file_path,$file);
        if($result){
            return $filename;
        }
        return false;
    }
    protected function chk($type)
    {
        $types = Config::get('upload.types');//获取所有types
        $config = Config::get('upload');
        $configType = isset($types[$type]) ? $types[$type] :'';

        if ( ! $configType )
        {
            return [ 'error' => 'type-not-found' ];
        }

        // Check if file is uploaded
        if ( ! \Input::hasFile('file') )
        {
            return [ 'error' => 'file-not-found' ];
        }

        $file  = \Input::file('file');

        // get file size in Bytes
        $file_size = $file->getSize();

        // Check the file size
        if ( $file_size > $config['max_size'] * 1024 || ( isset($configType['max_size']) && $file_size > $configType['max_size'] * 1024 ) )
        {
            return [ 'error' => 'limit-size' ];
        }

        // get the extension
        $ext = strtolower( $file->getClientOriginalExtension() );

        // checking file format
        $format = getFileFormat($ext);

        // TODO: check file format
        if( isset($configType['format']) && ! in_array($format, explode('|', $configType['format'])) )
        {
            return [ 'error' => 'invalid-format' ];
        }
        return true;
    }


}
