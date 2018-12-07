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

Route::any('/google',function (){
    return view('google');
});
Route::any('/disclaimer','DisclaimerController@index');
Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');


Route::any('/article','ArticleController@index');
Route::any('/test','RedisTestController@test');