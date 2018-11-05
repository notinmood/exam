<?php
/**
 * Created by PhpStorm.
 * User: xiedali
 * Date: 2018/11/3
 * Time: 8:58
 */

namespace Game\Controller;


use Think\Controller;
use Vendor\Hiland\Utils\Web\NetHelper;

class ApiController extends Controller
{
    public function index(){
        echo json_encode("hello world!");
    }

    public function echostring(){
        echo "hello string;";
    }

    public function echojson(){
        echo "hello json";
    }

    public function postdata(){
        $id= I('id');
        echo "编号为" .$id;
    }

    public function getopenid($code){
        $APPID= "wxa37839e8d0954603";
        $SECRET= "96acf4487c365efb37edd16f5bf1b496";
        $url= "https://api.weixin.qq.com/sns/jscode2session?appid=$APPID&secret=$SECRET&js_code=$code&grant_type=authorization_code";
        $result= NetHelper::request($url);
        echo $result;
    }
}