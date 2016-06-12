<?php
Route::controller('auth','Auth\AuthController');
Route::get('/',  function()
{
    return Redirect::to('home');
});
Route::get('status/list','StatusController@getList');
Route::get('status/rev-list','StatusController@getRevList');
Route::Resource('status','StatusController');
Route::group(['prefix' => 'home', 'namespace'=>'Home'], function () {
    Route::get('/', 'IndexController@index');
    Route::controller('index', 'IndexController');
});
Route::get('my', 'My\IndexController@index');
Route::Resource('user','UserController');
Route::group(['prefix' => 'my', 'namespace'=>'My', 'middleware' => 'oauth'], function () {
    Route::get('info','InfoController@index');
    Route::controller('info','InfoController');
    Route::Resource('apply','ApplyController');
    Route::Resource('status','StatusController');
    Route::post('follow/focus','FollowController@postFocus');
    Route::post('follow/un-focus','FollowController@postUnFocus');
    Route::Resource('follow','FollowController');
    Route::get('message','MessageController@index');
    Route::controller('message','MessageController');
    Route::Resource('praise','PraiseController');
    Route::Resource('comment','CommentController');
});
Route::group(['prefix' => 'admin', 'namespace'=>'Admin','middleware'=>'oauth.admin'], function () {
    Route::controller('test', 'TestController');
    Route::controller('index', 'IndexController');
});