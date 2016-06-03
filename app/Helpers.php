<?php
function debug_time($str=''){
    echo '当前时间：'. date('y-m-d h:i:s',time()) . '<br>';
    if($str){
        var_dump($str);
        echo '<br>';
    }
}
function previous()
{
    return URL::previous();
}
function getAvatar($url,$size=46){
    $realSize = getRealSize($size);
    if($url){
        return Config::get( 'avatar.url-pre').$url.'/'.$realSize.'.png';
    }
    return Config::get('avatar.size.'.$size)[1];
}

function getAvatarDefault($size=46){
    return Config::get('avatar.size.'.$size)[1];
}


function getRealSize($size)
{
    $sizes = Config::get('avatar.size');
    if(array_key_exists($size,$sizes)) {
        return $sizes[$size][0];
    }
    return dd('size error');
}

function getImageUrl($image,$size=''){
    if($image && is_object($image)) {
        if ($size) {
            return '/' . $image->path . '-' . $size . '.' . $image->ext;
        }
        return '/' . $image->path . '.' . $image->ext;
    }
    if($image && is_array($image)){
        if ($size) {
            return '/' . $image['path'] . '-' . $size . '.' . $image['ext'];
        }
        return '/' . $image['path'] . '.' . $image['ext'];
    }
    return '';
}
function getImageFullUrl($image,$size=''){
    $result = getImageUrl($image,$size);
    if($result){
        $result = URL::to('/') . $result;
    }
    return $result;
}
function getImageRealPath($image,$size=''){
    $result = getImageUrl($image,$size);
    if($result){
        $result = public_path(ltrim($result,'/'));
    }
    return $result;
}

function redis_unshift($key,$id)
{
    $ids = Redis::get($key);
    if($ids) {
        $ids = json_decode($ids);
        if(in_array($id,$ids)){
            return ;
        }
        array_unshift($ids,$id);
        Redis::set($key,json_encode($ids));
    }else{
        Redis::set($key,json_encode([$id]));
    }
}

function redis_unset($key,$id)
{
    $ids = Redis::get($key);
    if($ids) {
        $ids = json_decode($ids);
        if(in_array($id,$ids)){//包含
            unset($ids[array_search($id,$ids)]);
            Redis::set($key,json_encode(array_values($ids)));
            return true;
        }
    }
    return false;
}
function addPrefix($ids,$prefix){
    $result = [];
    foreach($ids as $k=>$v){
        $result[$k] = $prefix.$v;
    }
    return $result;
}
 function getFileFormat($ext) {
    if ( preg_match('/(jpg|jpeg|gif|png)/', $ext) )
    {
        return 'image';
    }
    elseif( preg_match( '/(mp3|wav|ogg)/', $ext) )
    {
        return 'audio';
    }
    elseif( preg_match( '/(mp4|wmv|flv)/', $ext) )
    {
        return 'video';
    }
    elseif( preg_match('/txt/', $ext) )
    {
        return 'text';
    }

    return 'other';
}
function smarty_modifier_time_ago($time) {
    $current_time = time();
    $time_deff = $current_time - $time;
    $return = '';
    if ($time_deff >= 3600) {
        $a_date = date('Y-m-d',$current_time);
        $b_date = date('Y-m-d',$time);
        if($a_date==$b_date) {
            $return = '今天 ' . date('H:i', $time);
        }else{
            $return = date('m-d H:i', $time);
        }
    } else if ($time_deff >= 60) {
        $minute = intval($time_deff / 60);
        $return = $minute . '分钟前';
    }else {
        $return = '刚刚';
    }
    return $return;
}

function getTime()
{
    $strTimeToString = "000111222334455556666667";
    $strWenhou = array('夜深了，','凌晨了，','早上好！','上午好！','中午好！','下午好！','晚上好！','夜深了，');
    return $strWenhou[(int)$strTimeToString[(int)date('G',time())]];
}
function hidePhone($phone)
{
    if(strlen($phone) != 11) return $phone;
    return substr($phone,0,3) . '*****' . substr($phone,-3,3);
}
function n2br($str)
{
    return str_replace(array("\r","\n",'\r','\n'),'<br>',$str);
}
function filterJs($html)
{
    $preg = "/<script\b[^>]*>(.*?)<\/script>/is";
    $html = preg_replace($preg,'',$html);
    return $html;
}
function filterCss($html)
{
    $preg = "/<style\b[^>]*>(.*?)<\/style>/is";
    $html = preg_replace($preg,'',$html);
    $preg = "/<link[^>]+\>/i";
    $html = preg_replace($preg,'',$html);
    return $html;
}
function filterImg($html)
{
    $preg = "/<img[^>]+\>/i";
    $html = preg_replace($preg,'',$html);
    return $html;
}
