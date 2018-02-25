<?php
//登录
$this->get('login', 'AuthController@showLoginForm')->name("login");
$this->post('login', 'AuthController@doLogin');

//后台管理
Route::group(['middleware' => ['auth'], 'as'=>"admin."], function(){
    $this->get('logout', 'AuthController@logout');
    //首页
    Route::get('/', ['as'=>'dashboard', 'uses'=>'HomeController@dashboard']);

    /*
     * ========================
     *  用户管理
     * ========================
     */
    //用户管理
    Route::get('/user', ['as'=>'user', 'uses'=>"UserController@show"]);
    //用户列表
    Route::get('/user/list', "UserController@getList");
    //用户详情
    Route::get('/user/detail', "UserController@detail");
    //保存用户信息
    Route::post('/user/save', "UserController@save");
    //用户关系
    Route::get('/user/tree', "UserController@tree");




    /*
     * ========================
     *  订单管理
     * ========================
     */
    //订单管理
    Route::get('/order', ['as'=>'orderlist', 'uses'=>"AlimamaOrderController@show"]);
    //订单列表
    Route::get('/order/list', ['as'=>'orderlist.list', 'uses'=>"AlimamaOrderController@getList"]);
    //订单详情
    Route::get('/order/detail',"AlimamaOrderController@detail");


    /*
     * ========================
     *  PID管理
     * ========================
     */
    //PID管理
    Route::get('/pid', ['as'=>'pid', 'uses'=>"PidController@show"]);
    //PID列表
    Route::get('/pid/list', ['as'=>'pid.list', 'uses'=>"PidController@getList"]);

    /*
     * ========================
     *  意见反馈
     * ========================
     */
    //意见反馈管理
    Route::get('/feedback', ['as'=>'feedback', 'uses'=>"FeedbackController@show"]);
    //意见反馈列表
    Route::get('/feedback/list', ['as'=>'feedback.list', 'uses'=>"FeedbackController@getList"]);


    /*
     * ========================
     *  系统授权管理
     * ========================
     */
    //系统授权管理
    Route::get('/taobaotoken', ['as'=>'taobaotoken', 'uses'=>"TaobaoTokenController@show"]);
    //系统授权列表
    Route::get('/taobaotoken/list', ['as'=>'taobaotoken.list', 'uses'=>"TaobaoTokenController@getList"]);
    //刷新授权
    Route::post('/taobaotoken/refresh', ['as'=>'taobaotoken.refresh', 'uses'=>"TaobaoTokenController@refreshToken"]);

    /*
     * ========================
     *  banner管理
     * ========================
     */
    //banner管理
    Route::get('/banner', ['as'=>'banner', 'uses'=>"BannerController@show"]);
    //banner列表
    Route::get('/banner/list', ['as'=>'banner.list', 'uses'=>"BannerController@getList"]);
    //banner保存
    Route::post('/banner/save', ['as'=>'banner.save', 'uses'=>"BannerController@save"]);
    //banner删除
    Route::post('/banner/del', ['as'=>'banner.del', 'uses'=>"BannerController@del"]);


    /*
     * ========================
     *  账单管理
     * ========================
     */
    //账单管理
    Route::get('/user_bill', ['as'=>'user_bill', 'uses'=>"UserBillController@show"]);
    //账单列表
    Route::get('/user_bill/list', ['as'=>'user_bill.list', 'uses'=>"UserBillController@getList"]);

    /*
     * ========================
     *  系统配置
     * ========================
     */
    //系统配置
    Route::get('/system_config', ['as'=>'system_config', 'uses'=>"SystemConfigController@show"]);
    //保存系统配置
    Route::post('/system_config/save', "SystemConfigController@saveConfig");

    /*
     * ========================
     *  域名管理
     * ========================
     */
    //域名管理
    Route::get('/wechat_domain', ['as'=>'wechat_domain', 'uses'=>"WechatDomainController@show"]);
    //域名列表
    Route::get('/wechat_domain/list', ['as'=>'wechat_domain.list', 'uses'=>"WechatDomainController@getList"]);
    //域名保存
    Route::post('/wechat_domain/save', ['as'=>'wechat_domain.save', 'uses'=>"WechatDomainController@save"]);
    //域名删除
    Route::post('/wechat_domain/del', ['as'=>'wechat_domain.del', 'uses'=>"WechatDomainController@del"]);


    /*
     * ========================
     *  失败任务管理
     * ========================
     */
    //失败任务管理
    Route::get('/failed_jobs', ['as'=>'failed_jobs', 'uses'=>"FailedJobsController@show"]);
    //失败任务列表
    Route::get('/failed_jobs/list', ['as'=>'failed_jobs.list', 'uses'=>"FailedJobsController@getList"]);
    //重试任务
    Route::post('/failed_jobs/retry', ['as'=>'failed_jobs.refresh', 'uses'=>"FailedJobsController@retry"]);

    /*
     * ========================
     *  等级配置管理
     * ========================
     */
    //等级配置管理
    Route::get('/grade', ['as'=>'grade', 'uses'=>"GradeController@show"]);
    //等级配置列表
    Route::get('/grade/list', ['as'=>'grade.list', 'uses'=>"GradeController@getList"]);
    //等级配置保存
    Route::post('/grade/save', ['as'=>'grade.save', 'uses'=>"GradeController@save"]);
    //等级配置删除
    Route::post('/grade/del', ['as'=>'grade.del', 'uses'=>"GradeController@del"]);


    /*
     * ========================
     *  公告管理
     * ========================
     */
    //公告管理
    Route::get('/notice', ['as'=>'notice', 'uses'=>"NoticeController@show"]);
    //公告列表
    Route::get('/notice/list', ['as'=>'notice.list', 'uses'=>"NoticeController@getList"]);
    //公告保存
    Route::post('/notice/save', ['as'=>'notice.save', 'uses'=>"NoticeController@save"]);
    //公告删除
    Route::post('/notice/del', ['as'=>'notice.del', 'uses'=>"NoticeController@del"]);


    /*
     * ========================
     *  提现申请
     * ========================
     */
    //提现申请管理
    Route::get('/withdraw', ['as'=>'withdraw', 'uses'=>"WithdrawController@show"]);
    //提现申请列表
    Route::get('/withdraw/list', "WithdrawController@getList");
    //提现详情
    Route::get('/withdraw/detail', "WithdrawController@detail");
    //同意提现申请
    Route::post('/withdraw/confirm', "WithdrawController@confirm");
    //拒绝提现申请
    Route::post('/withdraw/refuse', "WithdrawController@refuse");


    /*
     * ========================
     *  其他
     * ========================
     */
    //文件上传
    Route::post("/file/upload", ['as'=>'upload', 'uses'=>'FileController@upload']);




    //修改密码
    Route::get('/system/updatepwd', ['as'=>'system.updatepwd', 'uses'=>"SystemController@updatepwd"]);
    Route::post('/system/updatepwd', ['as'=>'system.updatepwd', 'uses'=>"SystemController@updatepwdSave"]);

});
