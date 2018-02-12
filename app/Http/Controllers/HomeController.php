<?php

namespace App\Http\Controllers;

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
        return view('admin.dashboard',[
            'userStatistics' => $userStatistics,
        ]);
    }
}
