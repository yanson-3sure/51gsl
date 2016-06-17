<?php
namespace App\Services;



use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Redis;
use Ixudra\Curl\Facades\Curl;
class AvatarService
{
    public function saveWechat($headimgurl)
    {
        if(!$headimgurl)return '';
        $sizes = Config::get('avatar.wechat-size');
        $basedir = Config::get('avatar.uploads.path');
        $md5 =  md5(Config::get('avatar.hash-key').$headimgurl);
        $savedir = substr($md5,0,2) . '/' . substr($md5,2,2) . '/' . substr($md5,4);
        $basedir .= $savedir;
        $destPath = public_path($basedir);
        if(!file_exists($destPath))
            mkdir($destPath,0755,true);
        foreach($sizes as $k => $v){
            $response = Curl::to(rtrim($headimgurl,'/0').'/'.$k)
                ->withContentType('image/png')
                ->download($destPath. '/'.$v.'.png');
        }
        return $savedir;
    }
}