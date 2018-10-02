<?php
/**
 * Created by PhpStorm.
 * User: xiedali
 * Date: 2018/10/1
 * Time: 17:53
 */

namespace Game\Controller;


use Think\Controller;
use Vendor\Hiland\Utils\Data\ArrayHelper;
use Vendor\Hiland\Utils\DataModel\ModelMate;

class ManageController extends Controller
{
    public function index()
    {
        $this->show("hello world");
    }

    public function dash($mbtitype = '')
    {
        $dataset = $this->getAnswersOfManage($mbtitype);

        $this->assign("dataset", $dataset);
        $this->display();
    }

    public function statistics()
    {
        $dataset = $this->getTypedCountAnswerOfManage();
        $this->assign("dataset", $dataset);
        $this->display();
    }

    public function analyse(){
        $dataset = $this->getTypedCountAnswerOfManage();

        $this->assign("dataset", $dataset);
        $this->display();
    }

    public function compare($quotaName='I'){
        $dataset= $this->getAnswersOfManage();
        $dataset= ArrayHelper::sortByColumn($dataset,'score'.$quotaName,SORT_DESC);

        $result= array();
        foreach($dataset as $item){
            $temp["username"]= $item['username'];
            $temp['scoreA']= $item['score'.$quotaName];
            $nameOfScoreB= '';
            switch ($quotaName){
                case "I":
                    $nameOfScoreB= "E";
                    break;
                case "S":
                    $nameOfScoreB= "N";
                    break;
                case "T":
                    $nameOfScoreB= "F";
                    break;
                default:
                    $nameOfScoreB= "P";
            }
            $temp['scoreB']= $item['score'.$nameOfScoreB];

            $result[]= $temp;
        }

        //dump($result);

        $this->assign("dataset",$result);

        $this->display();
    }

    /**
     * 获取某管理员下所有的测试问卷
     * @param $mbtitype
     * @return array
     */
    private function getAnswersOfManage($mbtitype)
    {
        //parentguid 入口
        $parentGuid = "0B64AF10-F1D0-6CD0-647F-160C50326F9D";
        $mate = new ModelMate("exam_answer");
        $condition = array();
        $condition["parentuserguid"] = $parentGuid;
        $condition["isfinished"] = 1;
        if ($mbtitype) {
            $condition['answerresult'] = $mbtitype;
        }
        $dataset = $mate->select($condition);
        return $dataset;
    }

    /**获取某管理员下分好类的测试问卷
     * @return array 这是一个二维数组，第一维度是mbti类型名称，第二维度是本类型下所有的测试问卷
     */
    private function getTypedAnswerOfManage()
    {
        $mbtitype = '';
        $temp = $this->getAnswersOfManage($mbtitype);
        $dataset = array();
        foreach ($temp as $item) {
            $answerresult = $item['answerresult'];
            $dataset[$answerresult][] = $item;
        }
        return $dataset;
    }

    /**获取某管理员下分好类的测试问卷,每个类别下试卷的数目
     * @return array
     */
    private function getTypedCountAnswerOfManage()
    {
        $dataset = $this->getTypedAnswerOfManage();
        $typedCountSet = array();
        foreach ($dataset as $k => $v) {
            $typedCountSet[$k] = count($v);
        }
        return $typedCountSet;
    }
}