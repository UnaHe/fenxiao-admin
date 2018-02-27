# 朋友淘分销版后台管理

* 测试地址 http://pytaoadmintest.tuidanke.com



# 部署
1. 开启足够数量的队列处理进程，并使用进程监控软件监控进程数量

    > php artisan queue:work --tries=5

2. 开启定时任务

    > \* * * * * php artisan schedule:run >> /dev/null 2>&1