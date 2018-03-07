<?php
Route::group(['namespace'=> 'App\Http\Controllers\ToolApis', 'prefix'=>"toolApi"], function() {
    //订单统计信息
    Route::get('/order/statistics', 'OrderController@statistics');
    //同步订单数据
    Route::post('/order/save', 'OrderController@save');
    //增量同步时间点
    Route::get('/order/syncIncreaseTime', 'OrderController@syncIncreaseTime');
    //未结算订单时间点
    Route::get('/order/notSettleTime', 'OrderController@notSettleTime');


    //获取淘宝登录授权信息
    Route::get('/taobaotoken/getToken', 'TaobaoTokenController@getToken');
    //保存淘宝登录授权信息
    Route::post('/taobaotoken/save', 'TaobaoTokenController@save');

    //获取PID信息
    Route::get('/pid/getInfo', 'PidController@getInfo');
    //创建PID
    Route::post('/pid/save', 'PidController@save');


});