<?php
/**
 * Created by PhpStorm.
 * User: yangtao
 * Date: 2017/10/18
 * Time: 15:51
 */
namespace App\Services;

use App\Helpers\QueryHelper;
use App\Models\Message;
use App\Models\UserMessage;

class MessageService
{
    /**
     * 获取消息列表
     * @param $userId
     * @return array
     */
    public function getMessages($userId){
        //已删除的消息
        $userDeleteMsgIds = UserMessage::where(['user_id'=>$userId, 'is_delete'=>1])->pluck('message_id')->toArray();

        $query = Message::where(['is_delete'=>0])->whereNotIn('id', $userDeleteMsgIds);
        //查询消息基础信息
        $query->where(function($query) use($userId){
            //私信
            $query->where(function($query) use($userId){
                $query->where('type', Message::MSG_TYPE_PRIVATE);
                $query->where('to_user_id', $userId);
            });
            //广播类型
            $query->orWhere(function($query) use($userId){
                $query->where('type', Message::MSG_TYPE_BROADCAST);
            });
        });

        $list = (new QueryHelper())->pagination($query)->get();

        $messages = $list->toArray();
        $messageIds = $list->pluck('id')->toArray();

        //查询是否已读
        $readedIds = UserMessage::whereIn('message_id', $messageIds)->where(['is_read'=>1, 'user_id'=> $userId])->pluck('message_id')->toArray();

        foreach ($messages as &$item){
            unset($item['is_delete']);
            unset($item['update_time']);
            unset($item['type']);
            $item['is_read'] = 0;
            if(in_array($item['id'], $readedIds)){
                $item['is_read'] = 1;
            }
        }

        return $messages;
    }

    /**
     * 阅读消息详情
     * 查询消息内容，如果消息不存在或不属于当前用户，则统一返回消息不存在
     * @param $userId
     * @param $messageId
     * @return array
     */
    public function read($userId, $messageId){
        $message = $this->getUserMessage($userId, $messageId);
        $this->updateUserMessage($userId, $messageId, 1);
        return $message;
    }

    /**
     * 删除消息
     * @param $userId
     * @param $messageId
     * @return bool
     */
    public function delete($userId, $messageId){
        $message = $this->getUserMessage($userId, $messageId);
        $this->updateUserMessage($userId, $messageId, 0, 1);
        return true;
    }

    /**
     * 获取用户消息，并判断消息是否属于用户
     * @param $userId
     * @param $messageId
     * @return mixed
     * @throws \Exception
     */
    public function getUserMessage($userId, $messageId){
        $message = Message::where(['is_delete'=>0, 'id'=>$messageId])->first();
        if(!$message || ($message['type'] == Message::MSG_TYPE_PRIVATE && $message['to_user_id'] != $userId)){
            throw new \Exception("消息不存在");
        }
        return $message;
    }

    /**
     * 更新用户消息状态
     * @param $userId 用户id
     * @param $messageId 消息id
     * @param int $isRead 是否已读
     * @param int $isDelete 是否删除
     */
    public function updateUserMessage($userId, $messageId, $isRead=0, $isDelete = 0){
        $userMessage = UserMessage::where(['message_id'=>$messageId, 'user_id'=>$userId])->first();
        if(!$userMessage){
            $userMessage = new UserMessage();
        }

        $time = date('Y-m-d H:i:s');
        //是否需要保存
        $isSave = false;

        if($isDelete && $userMessage['is_delete'] == 0){
            $userMessage['is_delete'] = 1;
            $userMessage['delete_time'] = $time;
            $isSave = true;
        }

        if($isRead && $userMessage['is_read'] == 0){
            $userMessage['is_read'] = 1;
            $userMessage['read_time'] = $time;
            $isSave = true;
        }

        if($isSave){
            $userMessage['message_id'] = $messageId;
            $userMessage['user_id'] = $userId;
            return $userMessage->save();
        }

        return false;
    }

    /**
     * 未读消息数量
     * @param $userId
     */
    public function unReadNum($userId){
        //已读已删除的消息
        $userDeleteMsgIds = UserMessage::where(['user_id'=>$userId])->pluck('message_id')->toArray();

        $query = Message::where(['is_delete'=>0])->whereNotIn('id', $userDeleteMsgIds);
        //查询消息基础信息
        $query->where(function($query) use($userId){
            //私信
            $query->where(function($query) use($userId){
                $query->where('type', Message::MSG_TYPE_PRIVATE);
                $query->where('to_user_id', $userId);
            });
            //广播类型
            $query->orWhere(function($query) use($userId){
                $query->where('type', Message::MSG_TYPE_BROADCAST);
            });
        });

        return ['un_read'=> $query->count()];
    }
}
