<?php
/**
 * Created by PhpStorm.
 * User: yangtao
 * Date: 2017/10/24
 * Time: 10:51
 */
namespace App\Services;

use App\Helpers\QueryHelper;
use App\Models\Feedback;
use App\Models\User;

class FeedbackService
{
    /**
     * 列表
     * @param \Illuminate\Http\Request $request
     */
    public function feedbackList($request){

        $query = Feedback::query()->from((new Feedback())->getTable()." as feedback")
            ->leftJoin((new User())->getTable()." as user", "user.id", "=", "feedback.user_id")
            ->select(['feedback.*', 'user.mobile']);

        //搜索手机号
        if($mobile = trim($request->get('mobile'))){
            $query->where("user.mobile", '=' , $mobile);
        }

        $query->orderBy("id", "desc");

        //分页数据
        $data  = (new QueryHelper())->pagination($query);

        return $data;
    }

}
