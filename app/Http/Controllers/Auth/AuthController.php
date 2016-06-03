<?php

namespace App\Http\Controllers\Auth;

use App\User;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use App\Services\UserService;

class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Registration & Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users, as well as the
    | authentication of existing users. By default, this controller uses
    | a simple trait to add these behaviors. Why don't you explore it?
    |
    */

    use AuthenticatesAndRegistersUsers;

    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => 'getLogout']);
    }

    protected $username = 'mobile';

    protected $redirectPath = '/?type=my';

    protected $loginPath = '/auth/login';

    public function getReg1()
    {
        return view('auth.reg1');
    }
    public function postReg1(Request $request)
    {
        $rules = [
            'mobile'=>'required|regex:/^1[34578][0-9]{9}$/|unique:users',
            'captcha'=>'required|sms',
        ];
        $this->validate($request,$rules);
        $mobile = Input::get('mobile');
        Session::put('reg1_mobile',$mobile);
        return ;
    }

    public function getReg2()
    {
        if(Session::has('reg1_mobile')) {
            $data['mobile'] = Session::get('reg1_mobile');
            return view('auth.reg2', $data);
        }
        return response()->redirectTo('auth/reg1');
    }

    protected function validator_reg2(Request $request)
    {
        $rules = [
            'mobile'=>'required|regex:/^1[34578][0-9]{9}$/|unique:users',
            'name'=>'required|min:2|max:10|unique:users',
            'password'=>'required|min:6|confirmed',
            'password_confirmation'=>'required|min:6',
        ];
        $this->validate($request,$rules);
    }

    public function postReg2(Request $request)
    {
        $this->validator_reg2($request);
        $mobile = Input::get('mobile');
        $name = Input::get('name');
        $password = Input::get('password');
        $tempUser = User::where('mobile','=',$mobile)->first();
        if($tempUser){
            return response('手机号已经注册',501);
        }
        $userService = new UserService();
        $user = $userService->save($mobile,$password,$name);
        if($user) {
//            $service = new UserService();
//            $agent = new Agent();
//            $isWechat = $agent->match('.*MicroMessenger.*');
//            if($isWechat){
//                //将微信 openid绑定
//                $app = app('EasyWeChat\\Foundation\\Application');
//                $oauthUser = $app->oauth->user();
//                $OPENID = $oauthUser->id;
////                $tempUser = $app->user->get($OPENID);
////                $UNIONID = $tempUser['unionid'];
//
//                $user_wechat_id = new UserWechatId();
//                $user_wechat_id->openid = $OPENID;
//                $user_wechat_id->unionid = '';
//                $user_wechat_id->wechatid = 0;
//                $user_wechat_id->userid =  $user->userid;
//                $user_wechat_id->create_at = Carbon::now();
//                $user_wechat_id->save();
//            }
            Auth::loginUsingId($user['id'],config('base.auto_remember_me'));
        }
    }

    public function getLogin()
    {
        return view('auth.login');
    }

    protected function validator_login(Request $request)
    {
        $rules = [
            'mobile'=>'required|regex:/^1[34578][0-9]{9}$/',
            'password'=>'required|min:6',
        ];
        $this->validate($request,$rules);
    }

    public function postLogin(Request $request)
    {
        $this->validator_login($request);
        $auth = false;
        $credentials = $request->only('mobile', 'password');

        if (Auth::attempt($credentials, $request->has('remember'))) {
            $auth = true; // Success
        }

        if ($request->ajax()) {
            return response()->json([
                'auth' => $auth,
                'intended' => Redirect::intended('/')->getTargetUrl(), //URL::previous()
            ]);
        } else {
            return redirect()->intended('/');
        }
    }

    public function getFindPwd()
    {
        return view('auth.findpwd');
    }

    protected function validator_findpwd(Request $request)
    {
        $rules = [
            'mobile'=>'required|regex:/^1[34578][0-9]{9}$/|exists:users',
            'captcha'=>'required|sms',
            'password'=>'required|min:6|confirmed',
            'password_confirmation'=>'required|min:6',
        ];
        $this->validate($request,$rules);
    }

    public function postFindPwd(Request $request)
    {
        $this->validator_findpwd($request);
        $mobile = Input::get('mobile');
        $password = Input::get('password');
        $service = new UserService();
        $user = $service->chgPassword($mobile,$password);
        if($user){
            Auth::loginUsingId($user->id,config('base.auto_remember_me'));
            return ['result'=>'success','intended' => Redirect::intended('/')->getTargetUrl()];
        }else{
            return response('更新失败', 501);
        }
    }
}
