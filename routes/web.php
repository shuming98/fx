<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//微博授权登录
Route::any('wx','IndexController@index');
Route::get('weibo/login','IndexController@login');
Route::get('weibo/center','IndexController@center');

//微信授权登录
Route::get('wechat/center','UserController@center');
Route::get('wechat/login','UserController@login');
Route::get('wechat/logout','UserController@logout');

//商店页面
Route::get('/','GoodsController@index');
Route::get('add','GoodsController@add');
Route::get('goods/{gid}','GoodsController@goods');
Route::get('cartadd/{gid}','GoodsController@cartadd');
Route::get('cartshow','GoodsController@cartshow');
Route::get('cartclear','GoodsController@cartclear');
Route::post('done','GoodsController@done');
Route::post('pay','GoodsController@pay');