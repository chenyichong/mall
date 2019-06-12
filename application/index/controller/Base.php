<?php
namespace app\index\controller;
use think\Controller;
use think\Session;
use app\index\model\Cart;
use app\index\model\Member as M;
class Base extends Controller
{
    protected function _initialize ()
    {
        define("MEM_ID",Session::has ('member_id')?Session::get ('member_id'):null);
        define("MEM_NAME",Session::has ('member_info')?Session::get ('member_info["name"]'):null);

        $member=M::where('id',MEM_ID)->find ();
        $this -> assign("member_name",$member['name']?$member['name']:null);
        $this -> assign("member_img",$member['img']?$member['img']:null);
        $this->assign ('member',$member);

        //购物车商品数量
        $data = Cart::where('member_id','=',MEM_ID)->where('order_number',0)->column('count');
        $sum=0;
        foreach ($data as &$value ){
            $sum=$sum+$value;
        }
        $this -> assign ("cart_num",$sum);

        //购物车商品总价
        $data1 = Cart::where('member_id','=',MEM_ID)->where('order_number',0)->column("price");
        $data2 = Cart::where('member_id','=',MEM_ID)->where('order_number',0)->column("count");
        $result=0;
        for ($i=0;$i<count ($data2);){
            foreach ($data1 as &$value1){
                $result = $value1*$data2[$i]+$result;
                $i++;
            }
        }
        $this -> assign ("cart_price",number_format("$result",2));

    }

    //判断用户是否已经登录 防止重复登录
    protected  function alreadyLogin()
    {
        if(MEM_ID){
            $this->error('用户已登录','index/index');
        }
    }

    protected function isLogin()
    {
        if(empty(MEM_ID)){
            $this->error('用户未登录','member/login');
        }
    }

 }