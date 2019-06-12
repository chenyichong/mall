<?php
namespace app\admin\controller;
use think\Controller;
use think\Session;
class Base extends Controller
{
    protected function _initialize ()
    {
        define("USER_ID",Session::has ('user_id')?Session::get ('user_id'):null);
        $this -> assign("user_name",Session::has ('user_info')?Session::get ('user_info["name"]'):null);
    }

    //判断用户是否登录 (index/index)
    protected function isLogin()
    {
        if (is_null (USER_ID)){
            $this->error("用户未登录",'user/login');
        }
    }

    //判断用户是否已经登录 防止重复登录 (user/login)
    protected function alreadyLogin()
    {
        if (USER_ID){
            $this->error("用户已登录",'index/index');
        }
    }


}