<?php

namespace Game\Controller;

use Game\Model\GameBiz;
use Think\Controller;
use Vendor\Hiland\Biz\Tencent\WechatHelper;
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

    public function mbti()
    {
        $this->display();
    }

    public function mbtiinput()
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
     */
    public function  getTopic($topicNumber=1){
        $mate4topic = new  ModelMate('exam_topic');
        $mate4choice = new ModelMate('exam_topic_choice');

        $conditon4topic= array();
        $conditon4topic['topicnumber']= $topicNumber;
        $topic= $mate4topic->find($conditon4topic);

        $topic['choices']= $mate4choice->select($conditon4topic);

        echo json_encode($topic);
    }

    public function saveAnswer($userGuid,$topicNumber,$topicAnswer){
        $mate = new  ModelMate('exam_topic_answer');
        $data= array();
        $data["userguid"]= $userGuid;
        $data["topicnumber"]= $topicNumber;
        $data["choicevalue"]= $topicAnswer;
        //$data["parentuserguid"]= $userGuid;
        $mate->interact($data);
    }
}