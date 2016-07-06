<?php
Route::controller('admin/test', 'Admin\TestController');
//登录相关
Route::group(['prefix' => 'auth', 'namespace' => 'Auth'], function () {
    Route::get('wechat', 'AuthController@redirectToProvider');
    Route::get('wechat/callback', 'AuthController@handleProviderCallback');
    Route::controller('','AuthController');
});

//ajax相关
Route::group(['prefix' => 'ajax', 'namespace' => 'Ajax'], function () {
    Route::Resource('qa','QAController');
    Route::Resource('status','StatusController');
    Route::Resource('strategy','StrategyController');
    Route::Resource('track','TrackController');
    Route::Resource('train','TrainController');
    Route::Resource('user','UserController');
});

//微信自动登录中间件
Route::group([ 'middleware' => 'oauth.wechat'], function () {
    Route::get('/', function () {
        return Redirect::to('home');
    });
    Route::Resource('status', 'StatusController');
    Route::Resource('strategy', 'StrategyController');

    Route::Resource('train', 'TrainController');


    Route::group(['prefix' => 'home', 'namespace' => 'Home'], function () {
        Route::get('/', 'IndexController@index');
        Route::controller('index', 'IndexController');
    });
    Route::get('my', 'My\IndexController@index');
    Route::Resource('user', 'UserController');
    Route::group(['prefix' => 'my', 'namespace' => 'My', 'middleware' => 'oauth'], function () {

        Route::get('info', 'InfoController@index');
        Route::controller('info', 'InfoController');
        Route::Resource('apply', 'ApplyController');
        Route::post('follow/focus', 'FollowController@postFocus');
        Route::post('follow/un-focus', 'FollowController@postUnFocus');
        Route::Resource('follow', 'FollowController');
        Route::get('message', 'MessageController@index');
        Route::controller('message', 'MessageController');
        Route::Resource('praise', 'PraiseController');
        Route::Resource('comment', 'CommentController');
        Route::Resource('question', 'QuestionController');

        Route::Resource('qa', 'QAController');


        Route::get('video/list', 'VideoController@getList');

        //ajax相关
        Route::group(['prefix' => 'ajax', 'namespace' => 'Ajax'], function () {
            Route::Resource('qa','QAController');
            Route::Resource('train','TrainController');
            Route::Resource('strategy','StrategyController');
        });

    });
    Route::group(['prefix' => 'my', 'namespace' => 'My', 'middleware' => 'oauth:1'], function () {
        Route::Resource('strategy', 'StrategyController');
        Route::Resource('status', 'StatusController');
        Route::Resource('track', 'TrackController');
        Route::Resource('vip/provided', 'Vip\ProvidedController');
        Route::Resource('vip', 'VipController');

        Route::Resource('answer', 'AnswerController');
    });
    Route::group(['prefix' => 'my', 'namespace' => 'My', 'middleware' => 'oauth:1'], function () {
        Route::Resource('train', 'TrainController');
    });
});
Route::controller('admin/export', 'Admin\ExportController');
Route::group(['prefix' => 'admin', 'namespace' => 'Admin', 'middleware' => 'oauth.admin'], function () {
    //Route::controller('test', 'TestController');
    Route::controller('index', 'IndexController');
});
//微信自动登录
Route::group(['prefix' => 'wechat', 'namespace' => 'Wechat'], function () {
    Route::get('oauthcallback', ['uses' => 'OauthCallbackController@index']);
    Route::any('oauthcallback/callback', ['uses' => 'OauthCallbackController@callback']);
    Route::controller('oauthcallback', 'OauthCallbackController');
});

