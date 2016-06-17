<?php
//登录相关
Route::group(['prefix' => 'auth', 'namespace' => 'Auth'], function () {
    Route::get('wechat', 'AuthController@redirectToProvider');
    Route::get('wechat/callback', 'AuthController@handleProviderCallback');

    Route::controller('','AuthController');
});
//微信自动登录中间件
Route::group([ 'middleware' => 'oauth.wechat'], function () {
    Route::get('/', function () {
        return Redirect::to('home');
    });
    Route::get('status/list', 'StatusController@getList');
    Route::get('status/rev-list', 'StatusController@getRevList');
    Route::Resource('status', 'StatusController');
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
        Route::Resource('status', 'StatusController');
        Route::post('follow/focus', 'FollowController@postFocus');
        Route::post('follow/un-focus', 'FollowController@postUnFocus');
        Route::Resource('follow', 'FollowController');
        Route::get('message', 'MessageController@index');
        Route::controller('message', 'MessageController');
        Route::Resource('praise', 'PraiseController');
        Route::Resource('comment', 'CommentController');
    });
});
Route::controller('admin/export', 'Admin\ExportController');
Route::group(['prefix' => 'admin', 'namespace' => 'Admin', 'middleware' => 'oauth.admin'], function () {
    Route::controller('test', 'TestController');
    Route::controller('index', 'IndexController');
});
//微信自动登录
Route::group(['prefix' => 'wechat', 'namespace' => 'Wechat'], function () {
    Route::get('oauthcallback', ['uses' => 'OauthCallbackController@index']);
    Route::any('oauthcallback/callback', ['uses' => 'OauthCallbackController@callback']);
    Route::controller('oauthcallback', 'OauthCallbackController');
});

