<?php
Route::controller('auth','Auth\AuthController');
Route::get('/',  function()
{
    return Redirect::to('home');
});
Route::group(['prefix' => 'home', 'namespace'=>'Home'], function () {
    Route::get('/', 'IndexController@index');
    Route::controller('index', 'IndexController');
});
Route::get('my', 'My\IndexController@index');
Route::Resource('user','UserController');
Route::group(['prefix' => 'my', 'namespace'=>'My', 'middleware' => 'oauth'], function () {
    //Route::controller('Status', 'StatusController');
    Route::get('info','InfoController@index');
    Route::controller('info','InfoController');
    Route::Resource('apply','ApplyController');
    Route::Resource('status','StatusController');
    Route::controller('follow','FollowController');
});
Route::group(['prefix' => 'admin', 'namespace'=>'Admin','middleware'=>'oauth.admin'], function () {
    Route::controller('test', 'TestController');
    Route::controller('index', 'IndexController');
});