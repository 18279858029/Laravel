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

Route::get('/','StaticPagesController@home')->name('home');
//帮助页
Route::get('/help','StaticPagesController@help')->name('help');
//关于页
Route::get('/about','StaticPagesController@about')->name('about');
//注册页
Route::get('signup','UsersController@create')->name('signup');
//严格按照了 RESTful 架构对路由进行设计
Route::resource('users','UsersController');
//显示登录页面
Route::get('login', 'SessionsController@create')->name('login');
//创建新会话（登录）
Route::post('login', 'SessionsController@store')->name('login');
//销毁会话（退出登录）        
Route::delete('logout', 'SessionsController@destroy')->name('logout');
//用户信息的修改
Route::get('/users/{user}/edit','UsersController@edit')->name('users.edit');
