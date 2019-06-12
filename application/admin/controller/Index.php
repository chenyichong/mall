<?php
namespace app\admin\controller;
use app\admin\controller\Base;
use app\admin\model\User;
use think\Request;

class Index extends Base
{
    public function index(Request $request)
    {
        $this->isLogin ();

        $user = User::find(['id'=>USER_ID]);
        $this->assign ('user',$user);
        //基础属性
        $this->assign ('ip',$request->ip ());
//        dump ( $request->url(true));
        return $this->fetch();
    }



}