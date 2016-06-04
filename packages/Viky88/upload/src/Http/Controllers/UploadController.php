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

class UploadController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $type = Input::get('type');
        $chk = $this->chk($type);
        if($chk!==true){
            return $chk;
        }

        $config = Config::get('upload');

        $file  = \Input::file('file');
        // get file size in Bytes
        $file_size = $file->getSize();

        // get the extension
        $ext = strtolower( $file->getClientOriginalExtension() );
        //$ext = 'png';
        // checking file format
        $format = getFileFormat($ext);


        //$type = 'status';
        // saving file
        $filename = date('U').str_random(10);
        $dir = $config['dir'].'/'.$type .'/'. date('Y/m/d');
        $destPath = public_path($dir);
        if(!file_exists($destPath))
            mkdir($destPath,0755,true);
        $file->move($destPath, $filename.'.'.$ext);

        $file_path = $dir.'/'.$filename.'.'.$ext;

        if ( $format == 'image' && isset($config['types'][$type]['image']) && count($config['types'][$type]['image']) )
        {
            $img = Image::make(public_path().'/'.$file_path);

            foreach($config['types'][$type]['image'] as $task => $params)
            {
                switch($task) {
                    case 'resize':
                        $img->resize($params[0], $params[1]);
                        break;
                    case 'fit':
                        $img->fit($params[0], $params[1]);
                        break;
                    case 'crop':
                        $img->crop($params[0], $params[1]);
                        break;
                    case 'thumbs':
                       // $img->save();

                        foreach($params as $name => $sizes) {

                            $img->backup();

                            $thumb_path = $destPath.'/'.$filename.'-'.$name.'.'.$ext;

                            $img->fit($sizes[0], $sizes[1])->save($thumb_path);

                            $img->reset();
                        }
                        break;
                }
            }

            //$img->save();
        }
        $userid = Auth::user()->id;
        $imageService = new ImageService();
        $image = $imageService->save($userid,$dir.'/'.$filename,$ext,$type);
        if($image) {
            return [
                'original' => [
                    'name' => $file->getClientOriginalName(),
                    'size' => $file_size,
                ],
                'ext' => $ext,
                'format' => $format,
                // 'image' => [
                //     'size' =>$img->getSize(),
                // ],
                'image_id' => $image['id'],
                'name' => $filename,
                'path' => $file_path,
            ];
        }else{
            return [ 'error' => '保存失败' ];
        }
    }

    public function postAvatar()
    {
        $chk = $this->chk('status');
        if($chk!==true){
            return $chk;
        }

        $userid = Auth::user()->id;
        $md5 =  md5(config('avatar.hash-key').$userid);
        $savedir = substr($md5,0,2) . '/' . substr($md5,2,2) . '/' . substr($md5,4);
        $basedir = Config::get('avatar.uploads.path');
        $basedir .= $savedir;
        $destPath = public_path($basedir);
        if(!file_exists($destPath))
            mkdir($destPath,0755,true);
        $sizes = Config::get('avatar.size');
        $img = Image::make(Input::file('file'));
        foreach($sizes as $k => $v){
            $img->backup();

            $img->fit($v[0], $v[0])->save($destPath.'/'.$v[0].'.png');

            $img->reset();
        }
        if(!$this->user['avatar']){
            $userService = new UserService();
            $userService->chgAvatar($this->uid,$savedir);
        }
        return ['path'=>$basedir];
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
