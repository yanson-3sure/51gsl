<?php namespace Stevenyangecho\UEditor\Uploader;

use OSS\OssClient;
use OSS\Core\OssException;
/**
 *
 *
 * trait UploadOss
 *
 * OSS 上传 类
 *
 * @package Stevenyangecho\UEditor\Uploader
 */
trait UploadOss
{
    public function uploadOss($key, $content)
    {
        $ossClient = new OssClient(config('UEditorUpload.core.oss.access_id'), config('UEditorUpload.core.oss.access_key'), config('UEditorUpload.core.oss.endpoint'));
        $bucket = config('UEditorUpload.core.oss.bucket');

        try {
            $ossClient->putObject($bucket, $key, $content);
            $url=strtolower(config('UEditorUpload.core.oss.url'));
            $fullName = ltrim($this->fullName, '/');
            $this->fullName=$url.'/'.$fullName;
            $this->stateInfo = $this->stateMap[0];

        } catch (OssException $e) {
            $this->stateInfo =  $e->getMessage();
        }
        return true;
    }
}