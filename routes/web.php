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


//用户登录注册
Auth::routes();

Route::get('/task/test', function () {
    $task = new \App\Jobs\TestTask('测试异步任务');
    $success = \Hhxsv5\LaravelS\Swoole\Task\Task::deliver($task);  // 异步投递任务，触发调用任务类的 handle 方法
    var_dump($success);
});

Route::get('/event/test', function () {
    $event = new \App\Events\TestEvent('测试异步事件监听及处理');
    $success = \Hhxsv5\LaravelS\Swoole\Task\Event::fire($event);
    var_dump($success);
});


Route::get('/danmu', function() {
    return view('danmu');
});
Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
