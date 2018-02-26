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
use App\Models\User;
use App\Models\UserMessage;
use Carbon\Carbon;

class MessageService
{
    /**
     * 列表
     * @param \Illuminate\Http\Request $request
     */
    public function getList($request){
        $query = Message::query()->from((new Message())->getTable().' as msg')
        ->leftJoin((new User())->getTable().' as user', 'msg.to_user_id', '=', 'user.id')
        ->select(['msg.*', 'user.mobile']);

        $query->where('is_delete', 0);
        $query->orderBy("create_time", "desc");

        //分页数据
        $data  = (new QueryHelper())->pagination($query);

        return $data;
    }

    /**
     * 保存
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function save($request){
        $title = $request->post('title');
        if(!$title){
            throw new \Exception("通知内容必须填写");
        }

        //指定接收用户
        $toUserMobile = $request->post('mobile');
        $toUserId = 0;
        if($toUserMobile){
            $toUserId = User::where("mobile", trim($toUserMobile))->pluck("id")->first();
            if(!$toUserId){
                throw new \Exception("接收用户不存在");
            }
        }

        $id = $request->post("id");
        if($id){
            $model = Message::find($id);
        }else{
            $model = new Message();
            $model['create_time'] = Carbon::now();
            $model['to_user_id'] = $toUserId;
            if($toUserId){
                $model['type'] = Message::MSG_TYPE_PRIVATE;
            }else{
                $model['type'] = Message::MSG_TYPE_BROADCAST;
            }
        }

        $model['title'] = $title;
        $model['content'] = $request->post('content');

        if(!$model->save()){
            throw new \Exception("保存失败");
        }

        return true;
    }

    /**
     * 删除
     * @param $id
     * @return bool
     * @throws \Exception
     */
    public function delete($id){
        $model = Message::find($id);
        if(!$id){
            throw new \Exception("消息不存在");
        }

        $model['is_delete'] = 1;
        if(!$model->save()){
            throw new \Exception("删除失败");
        }
        return true;
    }

}
