<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/31
 * Time: 17:36
 */

namespace App\Services;


use App\Helpers\QueryHelper;
use App\Models\User;
use App\Models\UserBill;

class UserBillService
{
    /**
     * 列表
     * @param \Illuminate\Http\Request $request
     */
    public function billList($request){

        $query = UserBill::query()->from((new UserBill())->getTable()." as bill")
            ->leftJoin((new User())->getTable()." as user", "user.id", "=", "bill.user_id")
            ->select([
                "bill.*",
                "user.mobile"
            ]);

        //手机号
        if($mobile = trim($request->get('mobile'))){
            $query->where("user.mobile", '=' , $mobile);
        }
        //类型
        $type = trim($request->get('type'));
        if($type !== ''){
            $query->where("bill.type", '=' , $type);
        }

        $query->orderBy("add_time", "desc");

        //分页数据
        $data  = (new QueryHelper())->pagination($query);

        return $data;
    }

}