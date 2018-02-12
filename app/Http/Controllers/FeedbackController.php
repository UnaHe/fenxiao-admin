<?php

namespace App\Http\Controllers;

use App\Services\FeedbackService;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Response;

class FeedbackController extends Controller
{
    /**
     * 显示管理
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(Request $request){
        return view("admin.feedback.feedback_list");
    }

    /**
     * 列表
     * @param Request $request
     * @return mixed
     */
    public function getList(Request $request){
        $data = (new FeedbackService())->feedbackList($request);
        return Response::json($data);
    }

}
