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

Route::get('/', function () {
    return view('welcome');
});
Route::get('/phpinfo', function() {
    phpinfo();
});

// 游戏列表
Route::get('/games', "GameController@list");
// 游戏详情
Route::get('/games/{id}', "GameController@info");
// 杂志列表
Route::get('/magzine', "ReviewController@list");
// 评测详情
Route::get('/magzine/{id}', "ReviewController@detail");