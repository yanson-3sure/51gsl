<?php namespace Viky88\Sms\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Viky88\Sms\Facades\Sms;


class SmsController extends Controller
{
    /**
     * Show the application welcome screen to the user.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $rules = [
            'mobile'=>'required|regex:/^1[34578][0-9]{9}$/',
        ];
        $messages = [
            'mobile.required' => '手机号必填',
            'mobile.regex' => '手机号格式不正确',
            'mobile.unique' =>'手机号已经被注册'
        ];
        $this->validate($request,$rules,$messages);
        $mobile = Input::get('mobile');
        if(Sms::send($mobile)){
            return response()->json(['result'=>'success']);
        }else{
            return response(Sms::error(),501);
        }
    }
}