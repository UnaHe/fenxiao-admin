<?php
/**
 * Created by PhpStorm.
 * User: yangtao
 * Date: 2017/10/24
 * Time: 10:51
 */
namespace App\Services;

use App\Models\Feedback;

class FeedbackService
{
    /**
     * 添加意见反馈
     * @param $userId
     * @param $content
     * @return bool
     */
    public function addFeedback($userId, $content){
        $isSuccess = Feedback::create([
            'user_id' => $userId,
            'content' => $content,
            'create_time' => date('Y-m-d H:i:s'),
        ]);

        return $isSuccess ? true : false;
    }
}
