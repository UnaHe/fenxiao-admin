<?php

namespace App\Http\Controllers;

use App\Services\AlimamaOrderService;
use App\Services\UserService;
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

        return view('admin.dashboard',[
            'userStatistics' => $userStatistics,
            'orderStatistics' => $orderStatistics,
        ]);
    }
}
