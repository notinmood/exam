<?php
/**
 * Created by PhpStorm.
 * User: xiedali
 * Date: 2018/11/3
 * Time: 8:58
 */

namespace Game\Controller;


use Think\Controller;
use Vendor\Hiland\Biz\Tencent\MiniProgramHelper;
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
        MiniProgramHelper::getOpenID($code);
        //echo $result;
    }
}