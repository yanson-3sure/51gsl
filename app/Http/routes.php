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

Route::group(['prefix' => 'admin', 'namespace'=>'Admin'], function () {
    Route::controller('test', 'TestController');
});