<?php
namespace Viky88\Sms;

use Exception;
use Illuminate\Config\Repository;
use Illuminate\Hashing\BcryptHasher as Hasher;
use Illuminate\Support\Str;
use Illuminate\Session\Store as Session;
use Ixudra\Curl\Facades\Curl;

class Sms
{
    /**
     * @var Session
     */
    protected $session;

    /**
     * @var Str
     */
    protected $str;

    /**
     * @var int
     */
    protected $length = 5;

    /**
     * @var string
     */
    protected $characters = '12346789abcdefghjmnpqrtuxyzABCDEFGHJMNPQRTUXYZ';

    /**
     * @var Hasher
     */
    protected $hasher;

    protected $send_url;


    protected $debug = false;
    protected $debug_code = '123456';

    protected $max_times = 3;

    protected $error = '';


    /**
     * @var bool
     */
    protected $sensitive = false;

    public function __construct(
        Repository $config,
        Session $session,
        Hasher $hasher,
        Str $str
    )
    {
        $this->config = $config;
        $this->session = $session;
        $this->hasher = $hasher;
        $this->str = $str;
    }

    /**
     * @param string $config
     * @return void
     */
    protected function configure($config)
    {
        if ($this->config->has('sms.' . $config))
        {
            foreach($this->config->get('sms.' . $config) as $key => $val)
            {
                $this->{$key} = $val;
            }
        }
    }

    /**
     * Generate captcha text
     *
     * @return string
     */
    protected function generate()
    {
        $characters = str_split($this->characters);

        $bag = '';
        for($i = 0; $i < $this->length; $i++)
        {
            $bag .= $characters[rand(0, count($characters) - 1)];
        }
        return $bag;
    }

    protected function sms_count($phone)
    {
        $key = 'sms'.$phone.'count';
        $count = 0;
        if ( $this->session->has($key))
        {
            $count = $this->session->get($key);
        }
        $this->session->put('sms'.$phone.'count',$count+1);
    }
    protected function get_count($phone)
    {
        $key = 'sms'.$phone.'count';
        $count = 0;
        if ( $this->session->has($key))
        {
            $count = $this->session->get($key);
        }
        return $count;
    }

    public function send($phone,$config='default')
    {
        $this->configure($config);
        if($this->get_count($phone)>= $this->max_times){
            $this->error = '最多只能发送'.$this->max_times.'次' ;
            return false;
        }
        if($this->debug){
            $this->session->put('sms', [
                'sensitive' => $this->sensitive,
                'key'       => $this->hasher->make($this->sensitive ? $this->debug_code : $this->str->lower($this->debug_code))
            ]);
            $this->sms_count($phone);

            return true;
        }
        $bag = $this->generate();
        $url = $this->send_url.'&m='.$phone.'&g='.$bag;
        //$result = Curl::to($this->send_url)->withData(['m'=>$phone,'g'=>$bag])->get();
        $result = Curl::to($this->send_url.'&m='.$phone.'&g='.$bag)->get();
        if($result==1){
            $this->session->put('sms', [
                'sensitive' => $this->sensitive,
                'key'       => $this->hasher->make($this->sensitive ? $bag : $this->str->lower($bag))
            ]);
            $this->sms_count($phone);
            return true;
        }
        $this->error = '发送失败,请稍候重试-'.$result.'-'.$url;
        return false;
    }
    /**
     * Captcha check
     *
     * @param $value
     * @return bool
     */
    public function check($value)
    {
        if ( ! $this->session->has('sms'))
        {
            return false;
        }

        $key = $this->session->get('sms.key');

        if ( ! $this->session->get('sms.sensitive'))
        {
            $value = $this->str->lower($value);
        }

        //$this->session->remove('sms');

        return $this->hasher->check($value, $key);
    }

    public function error()
    {
        return $this->error;
    }

}