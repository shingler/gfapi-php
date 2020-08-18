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
// 系列相关游戏
Route::get('/serial/{id}', "SerialController@games");
//Auth::routes();

//Route::get('/home', 'HomeController@index')->name('home');
// 登录认证
Route::post('/connect/authenticate', 'ConnectController@authenticate');
Route::get('/connect/token', 'ConnectController@token');
// 用户操作
Route::prefix('user')->middleware('token')->group(function (){
    Route::get('get', 'UserController@get');
});
// 收藏相关
Route::prefix('favorite')->middleware('token')->group(function (){
    Route::get('my', 'FavoriteController@my');
    Route::post('do/{game_id}', 'FavoriteController@do')->name("favorite.add")->middleware("gameId");
    Route::post('undo/{game_id}', 'FavoriteController@undo')->name("favorite.remove")->middleware("gameId");
});
