<?php
namespace App\Services\API;

use App\Jobs\DyxcSyncJob;
use App\Services\ImageService;
use App\Services\StatusService;
use App\Services\UserService;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Ixudra\Curl\Facades\Curl;
use Illuminate\Foundation\Bus\DispatchesJobs;

class DyxcService
{
    use DispatchesJobs;

    protected $debug = true;
    protected $url;
    protected $image_uploadurl;
    protected $image_prefix;
    protected $key;
    protected $users;

    protected $config='base.dyxc';

    public function __construct()
    {
        if (Config::has($this->config))
        {
            foreach(config($this->config) as $key => $val)
            {
                $this->{$key} = $val;
            }
        }
    }
    protected function curl($filepath)
    {
        // Create a cURL handle
        $ch = curl_init($this->image_uploadurl);

        // Create a CURLFile object
        $cfile = curl_file_create($filepath);

        // Assign POST data
        $data = array('upfile' => $cfile);
        curl_setopt($ch, CURLOPT_POST,1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true) ;
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

        // Execute the handle
        return curl_exec($ch);
    }
    //同步第一现场
    public function send($uid,$status_id,$newsType)
    {
        if($this->debug){
            return true;
        }
        if(isset($this->users[$uid])){
            //推送到队列中
            $this->dispatch(new DyxcSyncJob($status_id,$newsType));
        }
    }

    public function sync($status_id,$newsType)
    {
        $statusService = new StatusService();
        $status = $statusService->get($status_id);
        if($status) {
            $content = nl2p($status['message']);
            $userid = $status['uid'];
            $username = $this->users[$userid];
            $token = md5($username.$this->key);
            if ($status['image_id']) {
                $imageService = new ImageService();
                $image = $imageService->get($status['image_id']);
                if ($image) {
                    //提交图片到第一现场,并获取返回地址;
                    $result = $this->curl(getImageRealPath($image));
                    if ($result) {
                        $imageUrl = json_decode($result)->url;
                        $content .= '<image src="' . $imageUrl . '">';
                    }
                }
            }

            $response = Curl::to($this->url)
                ->withData([
                    'fxsname' => $username,
                    'Content' => $content,
                    'token'=>$token,
                    'newsType' => $newsType,
                ])
                ->post();
            if($response){
                Log::info('同步第一现场成功(status_id:'.$status_id.')');
            }else{
                Log::error('同步第一现场失败(status_id:'.$status_id.',newsType:'.$newsType.')');
                //throw  new \Exception;
            }
        }
    }
}