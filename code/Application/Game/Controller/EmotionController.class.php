<?php
/**
 * Created by PhpStorm.
 * User: xiedali
 * Date: 2018/10/17
 * Time: 18:06
 */

namespace Game\Controller;

use Game\Model\GameBiz;
use Think\Controller;
use Vendor\Hiland\Utils\Data\DateHelper;
use Vendor\Hiland\Utils\Data\StringHelper;
use Vendor\Hiland\Utils\DataModel\ModelMate;

class EmotionController extends Controller
{
    public function index()
    {
        //用户guid 入口(前后台的用户GUID都从这个地方唯一设置)
        $userGuid = '7e9c6abb-1d1b-4c90-b140-771e1cfebea7';
        GameBiz::setCookie('userGuid', $userGuid);

        $displayContinueButton = "false";
        $unfinishedAnswer = $this->getUnfinishedAnswer($userGuid);

        if ($unfinishedAnswer) {
            $lastAnswerGuid = $unfinishedAnswer["answerguid"];
            GameBiz::setCookie("lastAnswerGuid4emotion", $lastAnswerGuid);
            //获取上次没有完成的测试的最后一道题的number并保存为cookie
            $lastTopicNumberOfLastAnswer = $this->getLastTopicNumberOfAnswer($lastAnswerGuid);
            GameBiz::setCookie("lastTopicNumber4emotion", $lastTopicNumberOfLastAnswer);

            $displayContinueButton = "true";
        } else {
            $displayContinueButton = "false";
        }

        $this->assign("displayContinueButton", $displayContinueButton);
        $this->display();
    }

    /** 获取某位用户的未完成的问卷
     * --如果没有的话，返回null
     * --如果有多条未完成问卷，则返回最后一条未完成的问卷
     * @param $userGuid
     * @return array
     */
    public function getUnfinishedAnswer($userGuid)
    {
        $mate = new  ModelMate('exam_emotion_answer');
        $condition = array();
        $condition['userguid'] = $userGuid;
        $condition['isfinished'] = array("NEQ", 1);
        $result = $mate->select($condition);
        if ($result) {
            $count = count($result);
            return $result[$count - 1];
        } else {
            return null;
        }
    }

    /**获取某次考试所有答案的最后一道题编号
     * @param $answerGuid
     * @return mixed
     */
    private function getLastTopicNumberOfAnswer($answerGuid)
    {
        $lastTopicOfAnswer = $this->getLastTopicOfAnswer($answerGuid);

        $result = 0;
        if ($lastTopicOfAnswer) {
            $result = $lastTopicOfAnswer['topicnumber'];
        } else {
            $result = 0;
        }

        return $result;
    }

    /**获取某次考试所有答案的最后一道题信息
     * @param $answerGuid
     * @return mixed
     */
    private function getLastTopicOfAnswer($answerGuid)
    {
        $details = $this->getAnswerDetails($answerGuid);
        $count = count($details);
        return $details[$count - 1];
    }

    /**
     * 获取某次考试所有答案的具体信息
     * @param $answerGuid
     * @return array
     */
    private function getAnswerDetails($answerGuid)
    {
        $condition = array();
        $condition['answerguid'] = $answerGuid;
        $mate = new ModelMate('exam_emotion_answer_details');
        return $mate->select($condition, "id");
    }

    /**开始答题（开始答题时只记录用户和时间信息，其他信息后面完善）
     * @param $answerGuid
     * @param $userGuid
     * @return bool|number
     */
    public function beginAnswer4Client($answerGuid, $userGuid)
    {
        $data = array();
        $data['userguid'] = $userGuid;
        $data['answerguid'] = $answerGuid;
        $data['answerdate'] = DateHelper::format();

        $mate = new ModelMate('exam_emotion_answer');
        echo $mate->interact($data);
    }

    /**
     * 获取单条测试题目(为Web前端使用)
     * @param $topicNumber
     */
    public function getTopic4Client($topicNumber = 1)
    {
        $result = $this->getTopic($topicNumber);
        echo json_encode($result);
    }

    /**
     * 获取单条测试题目
     * @param $topicNumber
     * @return json
     */
    public function getTopic($topicNumber = 1)
    {
        $mate4topic = new  ModelMate('exam_emotion_topic');

        $conditon4topic = array();
        $conditon4topic['topicnumber'] = $topicNumber;
        $topic = $mate4topic->find($conditon4topic);

        return json_encode($topic);
    }

    public function saveAnswer($answerGuid, $topicNumber, $topicAnswer, $topicType)
    {
        $mate = new  ModelMate('exam_emotion_answer_details');
        $data = array();
        $condition = array();
        $condition["answerguid"] = $answerGuid;
        $condition["topicnumber"] = $topicNumber;
        $data = $mate->find($condition);
        if (is_null($data)) {
            $data = array();
        }

        $data["answerguid"] = $answerGuid;
        $data["topicnumber"] = $topicNumber;
        $data["topicvalue"] = $topicAnswer;
        $data["topictype"] = $topicType;
        $mate->interact($data);
    }

    public function progress()
    {
        $this->display();
    }

    public function updateAnswer4Client($answerGuid)
    {
        $condition = array();
        $condition["answerguid"] = $answerGuid;

        $mate4AnswerDetail = new ModelMate("exam_emotion_answer_details");
        $datas4AnswerDetail = $mate4AnswerDetail->select($condition);

        $array4AnserDetail = array();

        foreach ($datas4AnswerDetail as $data4AnswerDetail) {
            $topicNumber = $data4AnswerDetail["topicnumber"];
            $topicValue = $data4AnswerDetail["topicvalue"];
            $topicType = $data4AnswerDetail["topictype"];

            $array4AnserDetail[$topicType] += ($topicValue);
        }

        $mate4Answer = new ModelMate('exam_emotion_answer');
        $data4Answer = $mate4Answer->find($condition);
        if ($data4Answer == null) {
            $data4Answer = array();
        }

        $AD = $data4Answer['scoreAD'] = $array4AnserDetail['AD'];
        $LA = $data4Answer['scoreLA'] = $array4AnserDetail['LA'];
        $SI = $data4Answer['scoreSI'] = $array4AnserDetail['SI'];
        $SA = $data4Answer['scoreSA'] = $array4AnserDetail['SA'];
        $SS = $data4Answer['scoreSS'] = $array4AnserDetail['SS'];
        $CC = $data4Answer['scoreCC'] = $array4AnserDetail['CC'];

        $answerResult = "AD:$AD|LA:$LA|SI:$SI|SA:$SA|SS:$SS|CC:$CC";

        $data4Answer['answerresult'] = $answerResult;
        $data4Answer['isfinished'] = 1;
        $result = $mate4Answer->interact($data4Answer);

        if ($result) {
            GameBiz:: setCookie("answerresult4emotion", $answerResult);
        }
        echo $result;
    }

    public function result($answerGuid){
        $answerResult = "";
        $scoreAD = $scoreLA = $scoreSI = $scoreSA = $scoreSS = $scoreCC = 0;

        $mate4Answer = new ModelMate("exam_emotion_answer");
        $condition4Answer = array();
        $condition4Answer["answerguid"] = $answerGuid;
        $data4Answer = $mate4Answer->find($condition4Answer);

        if ($data4Answer) {
            $answerResult = $data4Answer["answerresult"];
            $scoreAD = $data4Answer["scoreAD"];
            $scoreLA = $data4Answer["scoreLA"];
            $scoreSI = $data4Answer["scoreSI"];
            $scoreSA = $data4Answer["scoreSA"];
            $scoreSS = $data4Answer["scoreSS"];
            $scoreCC = $data4Answer["scoreCC"];

            $this->assign("scoreAD", $scoreAD);
            $this->assign("scoreLA", $scoreLA);
            $this->assign("scoreSI", $scoreSI);
            $this->assign("scoreSA", $scoreSA);
            $this->assign("scoreSS", $scoreSS);
            $this->assign("scoreCC", $scoreCC);

            $mate4desc= new ModelMate("exam_emotion_desc");
            $datas4desc= $mate4desc->select();
            //dump($datas4desc);
            $answerResult="";

            $answerResult.= $this->genEmotionResultContent($datas4desc,"AD",$scoreAD);
            $answerResult.= $this->genEmotionResultContent($datas4desc,"LA",$scoreLA);
            $answerResult.= $this->genEmotionResultContent($datas4desc,"SI",$scoreSI);
            $answerResult.= $this->genEmotionResultContent($datas4desc,"SA",$scoreSA);
            $answerResult.= $this->genEmotionResultContent($datas4desc,"SS",$scoreSS);
            $answerResult.= $this->genEmotionResultContent($datas4desc,"CC",$scoreCC);

            $this->assign("answerResult", $answerResult);
        }

        $this->display();
    }

    public function report($answerGuid = '')
    {
//        $mate4Answer = new ModelMate("exam_mbti_answer");
//        $condition4Answer = array();
//        $condition4Answer["answerguid"] = $answerGuid;
//        $data4Answer = $mate4Answer->find($condition4Answer);
//
//        $answerType = $data4Answer['answerresult'];
//        $this->assign("examName", $answerType);
//
//        $answerUser = $data4Answer['username'];
//        $this->assign("answerUser", $answerUser);
//
//        $answerTime = $data4Answer["answerdate"];
//        $this->assign("answerTime", $answerTime);
//
//        $mate4desc = new ModelMate("exam_mbti_desc");
//        $condion4desc = array();
//        $condion4desc['name'] = $answerType;
//        $data4desc = $mate4desc->find($condion4desc);
//
//        $examTitle = $data4desc["title"];
//        $examSubTitle = $data4desc["subtitle"];
//        $this->assign("examTitle", $examTitle);
//        $this->assign("examSubTitle", $examSubTitle);
//
//        $this->getDescItemValueAndFormatAssign($data4desc, "gxtz");
//        $this->getDescItemValueAndFormatAssign($data4desc, "wtjj");
//        $this->getDescItemValueAndFormatAssign($data4desc, "gnyy");
//        $this->getDescItemValueAndFormatAssign($data4desc, "gzys");
//        $this->getDescItemValueAndFormatAssign($data4desc, "gzls");
//        $this->getDescItemValueAndFormatAssign($data4desc, "zzgx");
//        $this->getDescItemValueAndFormatAssign($data4desc, "ldfg");
//        $this->getDescItemValueAndFormatAssign($data4desc, "qzqx");
//        $this->getDescItemValueAndFormatAssign($data4desc, "gzhj");
//
//        $mate4descpro = new ModelMate("exam_mbti_desc_pro");
//        $data4descpro = $mate4descpro->find($condion4desc);
//
//        $this->getDescItemValueAndFormatAssign($data4descpro, "shgw");
//        $this->getDescItemValueAndFormatAssign($data4descpro, "czmd");
//        $this->getDescItemValueAndFormatAssign($data4descpro, "zylx");
//        $this->getDescItemValueAndFormatAssign($data4descpro, "grfz");

        $this->display();
    }

    private function genEmotionResultContent($datas4desc,$emotionType,$score){
        $emotionNames= GameBiz::getEmotionNames();
        $emotionName= $emotionNames[$emotionType];

        $desc= $this->getEmotionDesc($datas4desc,$emotionType,$score);
        $result = "您的[$emotionName]能力得分:$score,".$desc['summary']."<br/>";
        return $result;
    }

    private function getEmotionDesc($all,$emotionType,$emotionValue){
        $result= null;
        foreach ($all as $item){
            $values= $item["emotionvalues"];
            if($item['emotiontype']==$emotionType && StringHelper::isContains($values,"[$emotionValue]")){
                $result= $item;
                break;
            }
        }

        return $result;
    }
}