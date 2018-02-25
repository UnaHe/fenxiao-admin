<?php
/**
 * Created by PhpStorm.
 * User: yangtao
 * Date: 2017/10/18
 * Time: 15:51
 */
namespace App\Services;

use App\Helpers\QueryHelper;
use App\Models\Notice;
use Carbon\Carbon;

class NoticeService
{
    /**
     * 列表
     * @param \Illuminate\Http\Request $request
     */
    public function getList($request){

        $query = Notice::query();

        $query->where("is_delete", '=' , 0);
        $query->orderBy("id", "desc");

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
        $id = $request->post("id");
        if($id){
            $model = Notice::find($id);
        }else{
            $model = new Notice();
            $model['add_time'] = Carbon::now();
        }

        $model['start_time'] = $request->post('start_time');
        $model['end_time'] = $request->post('end_time');
        $model['title'] = $request->post('title');

        if(!$model->save()){
            throw new \Exception("保存失败");
        }

        return true;
    }

    /**
     * 删除banner
     * @param $id
     * @return bool
     * @throws \Exception
     */
    public function delete($id){
        $model = Notice::find($id);
        if(!$id){
            throw new \Exception("公告不存在");
        }

        $model['is_delete'] = 1;
        if(!$model->save()){
            throw new \Exception("删除失败");
        }
        return true;
    }

}
