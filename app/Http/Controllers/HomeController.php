<?php

namespace App\Http\Controllers;

use App\Services\AlimamaOrderService;
use App\Services\ApplyGuajiService;
use App\Services\ApplyUpgradeService;
use App\Services\PidService;
use App\Services\UserService;
use App\Services\WithdrawService;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function dashboard()
    {
        $userStatistics = (new UserService())->userStatistics();
        $orderStatistics = (new AlimamaOrderService())->orderStatistics();
        $unDealWithdrawNum = (new WithdrawService())->unDealNum();
        $unDealApplyUpgradeNum = (new ApplyUpgradeService())->unDealNum();
        $unDealApplyGuajiNum = (new ApplyGuajiService())->unDealNum();
        $pidStatistics = (new PidService())->pidStatistics();


        return view('admin.dashboard',[
            'userStatistics' => $userStatistics,
            'orderStatistics' => $orderStatistics,
            'unDealWithdrawNum' => $unDealWithdrawNum,
            'unDealApplyUpgradeNum' => $unDealApplyUpgradeNum,
            'unDealApplyGuajiNum' => $unDealApplyGuajiNum,
            'pidStatistics' => $pidStatistics,
        ]);
    }
}
