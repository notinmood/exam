<?php

namespace Game\Controller;

use Game\Model\GameBiz;
use Think\App;
use Think\Controller;
use Vendor\Hiland\Biz\Tencent\WechatHelper;
use Vendor\Hiland\Utils\Data\DateHelper;
use Vendor\Hiland\Utils\Data\GuidHelper;
use Vendor\Hiland\Utils\Data\StringHelper;
use Vendor\Hiland\Utils\DataModel\ModelMate;
use Vendor\Hiland\Utils\Web\WebHelper;

/**
 * Created by PhpStorm.
 * User: devel
 * Date: 2016/4/2 0002
 * Time: 7:38
 */
class IndexController extends Controller
{
    public function index()
    {
        $openID = GuidHelper::newGuid();
        $result = GameBiz::generateCharactorGameSet($openID);

        dump($result);

        //dump(C('WEIXIN_OAUTH2_REDIRECTPAGE'));
        $redirecturl = 'http://' . WebHelper::getHostName() . C('WEIXIN_OAUTH2_REDIRECTPAGE');
        $redirectstate = 100;
        $oauth2url = WechatHelper::getOAuth2PageUrl($redirectstate, $redirecturl, '', 'snsapi_base');

        dump('aaaaaaaaaaaaaa:' . $oauth2url);
        dump($oauth2url);


        WebHelper::redirectUrl($oauth2url);
        $this->show("<a href='" . $oauth2url . "'>开始</a>");
    }

    public function character()
    {
        //WxJSAPIMate
//        //dump('ssssssssssssss');
//        $code= WechatHelper::getOAuth2Code();
//        dump($code);
//        $openID= WechatHelper::getOAuth2OpenID($code);
//
//        dump($openID);
    }

    public function about(){
        $this->display();
    }

    public function sample(){
        $this->display();
    }


    /**
     * 将cookie进行包装（cookie的path部分），使其默认值跟jquery中cookie的操作相同
     * @param $name
     * @param $value
     */
    private function setCookie($name, $value)
    {
        $option = array();
        $option['path'] = ROOT_PATH;

        cookie($name, $value, $option);
    }

    /**将cookie进行包装（cookie的path部分），使其默认值跟jquery中cookie的操作相同
     * @param $name
     * @return mixed
     */
    private function getCookie($name)
    {
        $option = array();
        $option['path'] = ROOT_PATH;
        return cookie($name, $option);
    }

    public function mbti()
    {
        //用户guid 入口(前后台的用户GUID都从这个地方唯一设置)
        $userGuid = '7e9c6abb-1d1b-4c90-b140-771e1cfebea7';
        setCookie('userGuid', $userGuid);

        $displayContinueButton = "false";
        $unfinishedAnswer = $this->getUnfinishedAnswer($userGuid);

        if ($unfinishedAnswer) {
            $lastAnswerGuid = $unfinishedAnswer["answerguid"];
            setCookie("lastAnswerGuid", $lastAnswerGuid);
            //获取上次没有完成的测试的最后一道题的number并保存为cookie
            $lastTopicNumberOfLastAnswer = $this->getLastTopicNumberOfAnswer($lastAnswerGuid);
            setCookie("lastTopicNumber", $lastTopicNumberOfLastAnswer);

            $displayContinueButton = "true";
        } else {
            $displayContinueButton = "false";
        }

        $this->assign("displayContinueButton", $displayContinueButton);
        $this->display();
    }

    /**
     * 获取某次考试所有答案的具体信息
     * @param $answerGuid
     * @return array
     */
    public function getAnswerDetails($answerGuid)
    {
        $condition = array();
        $condition['answerguid'] = $answerGuid;
        $mate = new ModelMate('exam_answer_details');
        return $mate->select($condition, "topicnumber");
    }

    /**获取某次考试所有答案的最后一道题信息
     * @param $answerGuid
     * @return mixed
     */
    public function getLastTopicOfAnswer($answerGuid)
    {
        $details = $this->getAnswerDetails($answerGuid);
        $count = count($details);
        //dump($details[$count-1]);
        return $details[$count - 1];
    }

    /**获取某次考试所有答案的最后一道题编号
     * @param $answerGuid
     * @return mixed
     */
    public function getLastTopicNumberOfAnswer($answerGuid)
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

    public function mbtiProgress()
    {
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
        $mate = new  ModelMate('exam_answer');
        $condition = array();
        $condition['userguid'] = $userGuid;
        $condition['isfinished'] = array("NEQ",1);
        $result = $mate->select($condition);
        if ($result) {
            $count = count($result);
            //dump($result[$count - 1]);
            return $result[$count - 1];
        } else {
            return null;
        }
    }

    public function mbtiInput()
    {
        if (IS_POST) {
            $topicinfo = I('topicinfo');
            $lines = explode("\r\n", $topicinfo);
            if (count($lines) < 3) {
                dump('error');
            } else {
                $line1 = $lines[0];
                $line2 = $lines[1];
                $line3 = $lines[2];
                $exist4line1 = StringHelper::isContains($line1, "．");
                $exist4line2 = StringHelper::isContains($line2, "．");
                $exist4line3 = StringHelper::isContains($line3, "．");
                if ($exist4line1 == false || $exist4line2 == false || $exist4line3 == false) {
                    dump('error');

                } else {
                    $mate4topic = new  ModelMate('exam_topic');
                    $topicnumber = StringHelper::getSeperatorBeforeString($line1, "．");
                    $topictitle = StringHelper::getSeperatorAfterString($line1, "．");

                    $data4topice = array();
                    $data4topice['topicnumber'] = $topicnumber;
                    $data4topice['topictitle'] = $topictitle;
                    $mate4topic->interact($data4topice);

                    $this->interactChoice($line2, $topicnumber);
                    $this->interactChoice($line3, $topicnumber);
                }
            }

            $this->display();
        } else {
            $this->display();
        }
    }

    /**
     * @param $line
     * @param $topicnumber
     */
    private function interactChoice($line, $topicnumber)
    {
        $choicenumber = StringHelper::getSeperatorBeforeString($line, "．");
        $choicecontent = StringHelper::getSeperatorAfterString($line, "．");

        $mate4choice = new ModelMate('exam_topic_choice');
        $data4choice = array();
        $data4choice['topicnumber'] = $topicnumber;
        $data4choice['choicenumber'] = $choicenumber;
        $data4choice['choicecontent'] = $choicecontent;
        $mate4choice->interact($data4choice);
    }

    /**
     * 获取单条测试题目
     * @param $topicNumber
     * @return json
     */
    public function getTopic($topicNumber = 1)
    {
        $mate4topic = new  ModelMate('exam_topic');
        $mate4choice = new ModelMate('exam_topic_choice');

        $conditon4topic = array();
        $conditon4topic['topicnumber'] = $topicNumber;
        $topic = $mate4topic->find($conditon4topic);

        $topic['choices'] = $mate4choice->select($conditon4topic);

        return json_encode($topic);
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

    public function saveAnswer($answerGuid, $topicNumber, $topicAnswer)
    {
        $mate = new  ModelMate('exam_answer_details');
        $data = array();
        $condition= array();
        $condition["answerguid"] = $answerGuid;
        $condition["topicnumber"] = $topicNumber;
        $data= $mate->find($condition);
        if(is_null($data) ){
            $data = array();
        }

        $data["answerguid"] = $answerGuid;
        $data["topicnumber"] = $topicNumber;
        $data["choicevalue"] = $topicAnswer;
        $mate->interact($data);
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

        $mate = new ModelMate('exam_answer');
        echo $mate->interact($data);
    }

    public function updateAnswer4Client($answerGuid)
    {
        $condition = array();
        $condition["answerguid"] = $answerGuid;

        $mate4AnswerDetail = new ModelMate("exam_answer_details");
        $datas4AnswerDetail = $mate4AnswerDetail->select($condition);

        $array4Topic = $this->getTopicTypeMatch();

        $array4AnserDetail = array();

        foreach ($datas4AnswerDetail as $data4AnswerDetail) {
            $topicNumber = $data4AnswerDetail["topicnumber"];
            $choiceValue = $data4AnswerDetail["choicevalue"];

            $topicType = $array4Topic[$topicNumber];
            $typeNameA = StringHelper::subString($topicType, 0, 1);
            $typeNameB = StringHelper::subString($topicType, 1, 1);

            $array4AnserDetail[$typeNameA] += ($choiceValue);
            $array4AnserDetail[$typeNameB] += (5 - ($choiceValue));
        }

        $mate4Answer = new ModelMate('exam_answer');
        $data4Answer = $mate4Answer->find($condition);
        if ($data4Answer == null) {
            $data4Answer = array();
        }

        $data4Answer['scoreE'] = $array4AnserDetail['E'];
        $data4Answer['scoreI'] = $array4AnserDetail['I'];
        $data4Answer['scoreS'] = $array4AnserDetail['S'];
        $data4Answer['scoreN'] = $array4AnserDetail['N'];
        $data4Answer['scoreT'] = $array4AnserDetail['T'];
        $data4Answer['scoreF'] = $array4AnserDetail['F'];
        $data4Answer['scoreJ'] = $array4AnserDetail['J'];
        $data4Answer['scoreP'] = $array4AnserDetail['P'];

        $answerResult = "";
        if ($data4Answer['scoreE'] > $data4Answer['scoreI']) {
            $answerResult .= "E";
        } else {
            $answerResult .= "I";
        }

        if ($data4Answer['scoreS'] > $data4Answer['scoreN']) {
            $answerResult .= "S";
        } else {
            $answerResult .= "N";
        }

        if ($data4Answer['scoreT'] > $data4Answer['scoreF']) {
            $answerResult .= "T";
        } else {
            $answerResult .= "F";
        }

        if ($data4Answer['scoreJ'] > $data4Answer['scoreP']) {
            $answerResult .= "J";
        } else {
            $answerResult .= "P";
        }

        $data4Answer['answerresult'] = $answerResult;
        $data4Answer['isfinished'] = 1;
        $result = $mate4Answer->interact($data4Answer);

        if ($result) {
            $this->setCookie("answerresult", $answerResult);
        }
        echo $result;
    }

    public function mbtiResult($answerGuid)
    {
        $answerResult = "";
        $scoreE = $scoreI = $scoreS = $scoreN = $scoreT = $scoreF = $scoreJ = $scoreP = 0;

        $mate4Answer = new ModelMate("exam_answer");
        $condition4Answer = array();
        $condition4Answer["answerguid"] = $answerGuid;
        $data4Answer = $mate4Answer->find($condition4Answer);

        if ($data4Answer) {
            $answerResult = $data4Answer["answerresult"];
            $scoreE = $data4Answer["scoreE"];
            $scoreI = $data4Answer["scoreI"];
            $scoreS = $data4Answer["scoreS"];
            $scoreN = $data4Answer["scoreN"];
            $scoreT = $data4Answer["scoreT"];
            $scoreF = $data4Answer["scoreF"];
            $scoreJ = $data4Answer["scoreJ"];
            $scoreP = $data4Answer["scoreP"];

            $mate4mbti = new ModelMate("exam_mbti_desc");
            $condition4mbti = array();
            $condition4mbti["name"] = $answerResult;
            $data4mbti = $mate4mbti->find($condition4mbti);

            $examTitle = $data4mbti["title"];
            $examSubTitle = $data4mbti["subtitle"];
            $examSummary = $data4mbti["summary"];

            $examSummary= str_replace(Chr(13),"<br/>",$examSummary);


            $this->assign("answerResult", $answerResult);

            $this->assign("scoreE", $scoreE);
            $this->assign("scoreI", $scoreI);

            $this->assign("scoreS", $scoreS);
            $this->assign("scoreN", $scoreN);

            $this->assign("scoreT", $scoreT);
            $this->assign("scoreF", $scoreF);

            $this->assign("scoreJ", $scoreJ);
            $this->assign("scoreP", $scoreP);

            $this->assign("examTitle", $examTitle);
            $this->assign("examSubTitle", $examSubTitle);
            $this->assign("examSummary", $examSummary);
        }

        $this->display();
    }

    /**从数据库的mbti信息里面获取某个数据项目，并对其进行格式化处理
     * @param $data
     * @param $itemName
     * @return string
     */
    private function getDescItemValueAndFormatAssign($data, $itemName)
    {
        $itemValue = $data[$itemName];
        $itemValue = "<p>" . str_replace(Chr(13), "</p><p>", $itemValue) . "</p>";
        $this->assign($itemName,$itemValue);
    }

    public function mbtiReport($answerGuid=''){
        $mate4Answer = new ModelMate("exam_answer");
        $condition4Answer = array();
        $condition4Answer["answerguid"] = $answerGuid;
        $data4Answer = $mate4Answer->find($condition4Answer);

        $answerType=$data4Answer['answerresult'];
        $this->assign("examName",$answerType);

        $answerUser= $data4Answer['username'];
        $this->assign("answerUser",$answerUser);

        $answerTime= $data4Answer["answerdate"];
        $this->assign("answerTime",$answerTime);

        $mate4desc= new ModelMate("exam_mbti_desc");
        $condion4desc= array();
        $condion4desc['name']= $answerType;
        $data4desc= $mate4desc->find($condion4desc);

        $examTitle = $data4desc["title"];
        $examSubTitle = $data4desc["subtitle"];
        $this->assign("examTitle", $examTitle);
        $this->assign("examSubTitle", $examSubTitle);

        $this->getDescItemValueAndFormatAssign($data4desc,"gxtz");
        $this->getDescItemValueAndFormatAssign($data4desc,"wtjj");
        $this->getDescItemValueAndFormatAssign($data4desc,"gnyy");
        $this->getDescItemValueAndFormatAssign($data4desc,"gzys");
        $this->getDescItemValueAndFormatAssign($data4desc,"gzls");
        $this->getDescItemValueAndFormatAssign($data4desc,"zzgx");
        $this->getDescItemValueAndFormatAssign($data4desc,"ldfg");
        $this->getDescItemValueAndFormatAssign($data4desc,"qzqx");
        $this->getDescItemValueAndFormatAssign($data4desc,"gzhj");

        $mate4descpro= new ModelMate("exam_mbti_desc_pro");
        $data4descpro = $mate4descpro->find($condion4desc);

        $this->getDescItemValueAndFormatAssign($data4descpro,"shgw");
        $this->getDescItemValueAndFormatAssign($data4descpro,"czmd");
        $this->getDescItemValueAndFormatAssign($data4descpro,"zylx");
        $this->getDescItemValueAndFormatAssign($data4descpro,"grfz");

        $this->display();
    }

    /**
     * 获取题目编号与题目类型对照表（数组形式）
     * @return array
     */
    private function getTopicTypeMatch()
    {
        $mate4Topic = new ModelMate("exam_topic");
        $datas4Topic = $mate4Topic->select();

        $array4Topic = array();
        foreach ($datas4Topic as $data4Topic) {
            $topiceNumber = $data4Topic["topicnumber"];
            $topicType = $data4Topic["topictype"];
            $array4Topic[$topiceNumber] = $topicType;
        }
        return $array4Topic;
    }
}