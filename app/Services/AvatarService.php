<?php
namespace App\Services;



use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Redis;
use Ixudra\Curl\Facades\Curl;
use Illuminate\Support\Facades\Storage;

class AvatarService
{
    public function saveWechat($headimgurl)
    {
        if(!$headimgurl)return '';
        $basedir = Config::get('avatar.uploads.path');

        $type = 'avatar';
        $ext = 'png';
        $savedir = getMd5PathRandom('avatar');
        $filename = $savedir.'.'.$ext;
        $basedir .= $savedir;
        $destPath = public_path($basedir);
        if(!file_exists($destPath))
            mkdir($destPath,0755,true);

        $response = Curl::to($headimgurl)
            ->withContentType('image/png')
            ->download($destPath. '/0.png');

        $file_path = $type.'/'.$filename;
        $result = Storage::putFile($file_path,$destPath. '/0.png');
        if($result){
            return $filename;
        }
        return false;
    }
}