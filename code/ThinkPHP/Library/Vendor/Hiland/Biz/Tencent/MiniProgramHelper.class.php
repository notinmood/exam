<?php
/**
 * Created by PhpStorm.
 * User: xiedali
 * Date: 2018/11/5
 * Time: 19:31
 */

namespace Vendor\Hiland\Biz\Tencent;


use Vendor\Hiland\Biz\Tencent\Common\MiniProgramConfig;
use Vendor\Hiland\Utils\Web\NetHelper;

class MiniProgramHelper
{
    /** 根据微信小程序wx.login中success中返回的code获取 用户session信息
     * @param $code
     * @return json
     */
    private static function getUserSession($code){
        $APPID= MiniProgramConfig::APPID;
        $SECRET= MiniProgramConfig::SECRET;
        $url= "https://api.weixin.qq.com/sns/jscode2session?appid=$APPID&secret=$SECRET&js_code=$code&grant_type=authorization_code";
        $result= NetHelper::request($url);
        return $result;
    }
    public static function getOpenID($code){
        $sessionJSON= self::getUserSession($code);
        $sessionObject= json_decode($sessionJSON);
        return $sessionObject->openid;
    }
}