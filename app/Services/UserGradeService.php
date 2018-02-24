<?php
/**
 * Created by PhpStorm.
 * User: yangtao
 * Date: 2018/1/31
 * Time: 19:55
 */

namespace App\Services;
use App\Models\Grade;


/**
 * 用户等级相关
 * Class UserGradeService
 * @package App\Services
 */
class UserGradeService
{
    /**
     * 用户等级
     * @var
     */
    private $grades;

    public function __construct(){
        $gradeModels = Grade::orderBy("sort", "desc")->get()->toArray();
        $grades = [];
        foreach ($gradeModels as $gradeModel){
            $grades[$gradeModel['grade']] = $gradeModel;
        }
        $this->grades = $grades;
    }

    /**
     * 获取所有等级信息
     * @return array
     */
    public function getGrades(){
        return $this->grades;
    }

    /**
     * 等级最高级别
     */
    public function getTopGrade(){
       return array_first($this->grades);
    }


    /**
     * 获取等级信息
     * @param $grade
     * @return mixed
     */
    public function getGrade($grade){
        return $this->grades[$grade];
    }

    /**
     * 获取自身返利比例
     * @param int $grade 用户等级
     * @param int $level 返利层级
     */
    public function getSelfRate($grade, $level){
        return $this->getRate($grade, "rate", $level);
    }

    /**
     * 获取平行返利比例
     * @param int $grade 用户等级
     * @param int $level 返利层级
     */
    public function getSameRate($grade, $level){
        return $this->getRate($grade, "same_rate", $level);
    }


    /**
     * 查询返利比例
     * @param int $grade 用户等级
     * @param string $rateName 返利类型名称
     * @param int $level 返利层级
     * @return mixed
     */
    public function getRate($grade, $rateName, $level){
        $gradeModel = $this->getGrade($grade);
        $rateInfo = $gradeModel[$rateName];
        $rateArray = explode(";", $rateInfo);

        $userRate = [];
        foreach ($rateArray as $rate){
            $rateInfo = explode(":", $rate);
            if(count($rateInfo)==2){
                $userRate[$rateInfo[0]] = $rateInfo[1];
            }
        }

        return isset($userRate[$level]) ? $userRate[$level] : 0;
    }

    /**
     * 获取等级列表
     */
    public function getGradeList(){
        $grades = $this->grades;
        foreach ($grades as &$grade){
            if($grade['child_grade']){
                $grade['child_grade_name'] = $this->getGrade($grade['child_grade'])['grade_name'];
            }else{
                $grade['child_grade_name'] = "";
            }
        }

        $total = count($grades);
        return [
            'data'=> array_values($grades),
            'recordsFiltered' => $total,
            'recordsTotal' => $total,
        ];
    }

    /**
     * 保存
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function save($request){
        $id = $request->post("id");
        if($id){
            $model = Grade::find($id);
        }else{
            $model = new Grade();
        }

        //直推返利存储格式
        $rates = $request->post('rate');
        $rateStr = "";
        if($rates){
            foreach ($rates as $key=>&$value){
                $value = $key.":".$value;
            }
            $rateStr = implode(";", $rates);
        }

        //平行返利存储格式
        $sameRates = $request->post('same_rate');
        $sameRateStr = "";
        if($sameRates){
            foreach ($sameRates as $key=>&$value){
                $value = $key.":".$value;
            }
            $sameRateStr = implode(";", $sameRates);
        }

        $model['grade_name'] = $request->post('grade_name');
        $model['child_grade'] = $request->post('child_grade');
        $model['child_grade_num'] = $request->post('child_grade_num');
        $model['sort'] = $request->post('sort');
        $model['rate'] = $rateStr;
        $model['same_rate'] = $sameRateStr;
        $model['find_parent_level'] = $request->post('find_parent_level');
        $model['find_same_level'] = $request->post('find_same_level');

        if(!$model->save()){
            throw new \Exception("保存失败");
        }

        if(!$id){
            $model['grade'] = $model['id'];
            $model->save();
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
        $model = Grade::find($id);
        if(!$id){
            throw new \Exception("等级不存在");
        }

        if(!$model->delete()){
            throw new \Exception("删除失败");
        }
        return true;
    }

}