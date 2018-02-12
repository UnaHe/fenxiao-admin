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
}