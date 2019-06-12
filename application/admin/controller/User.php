<?php
namespace app\admin\controller;
use think\Request;
use app\admin\controller\Base;
use app\admin\model\User as UserModel;
use think\Session;
class User extends Base
{
    //渲染登录页面
   public function Login()
   {
       //判断是否已经登录
       $this->alreadyLogin ();
       return $this->fetch ();
   }

   //验证登录信息
   public function checkLogin(Request $request)
   {

       $status=0;
       $message="初始信息";
       $data= $request->param();

       //验证器验证
        $result = $this ->validate ($data,'Login');

       //验证数据库信息
       if (true!==$result) {
           //查询条件
          $message=$result;
       }else{
           //在数据库搜索账号密码
           $map = [
               'name' => $data['name'],
               'password' => md5 ($data['password']),
           ];
           $user = UserModel::get ($map);

           if ($user == null) {
               $message = "账号或密码错误";
           } else {
               $status = 1;
               $message = "登录成功";
               UserModel::where('name',$data['name'])->update (['login_count'=>$user['login_count']+1]);
               //生成session信息
               Session::set('user_id',$user->id);
               Session::set('user_info',$user->getData ());

               //更新数据库login_time
                $user->save(['login_time'=>time()]);
           }
       }
       return ['message' => $message, 'status' => $status];
   }

   //退出登录
    public function logout()
    {
        Session::delete ('user_id');
        Session::delete ('user_info');
        $this->success ('登录注销','user/login');
    }


   // 渲染管理员列表
    public function adminList()
    {
        $user=UserModel::all();
        $this->assign ('user',$user);
        $this->assign ('count',count ($user));

        return $this -> fetch ();
    }

    //改变账号状态status
    public function changeStatus(Request $request)
    {
        $id = $request -> param('id');
        $result = UserModel::get($id);
        if($result->getData('status')==0){
            UserModel::update(['status'=>1],['id'=>$id]);
        }else{
            UserModel::update(['status'=>0],['id'=>$id]);
        }
    }

    // 添加 页面的渲染
    public function adminAdd()
    {
        return $this->fetch ();
    }

    // 添加
    public function add(Request $request)
    {
        $data = $request->param();
        $status = 0;
        $message= "初始message";

        //验证data
        $result = $this->validate ($data,'User.add');

        if($result===true){
            $user = UserModel::create($data);

            $message = "添加成功";
            $status = 1;
        }else{
            $message = $result;
        }

        return ['status'=>$status, 'message'=>$message];
    }


    // 软删除
    public function delete()
    {
        $id = $_GET['id'];
        UserModel::destroy ($id);
    }

    // 恢复
    public function restore()
    {
        $id = input ('id');
        $result=UserModel::onlyTrashed()->select($id);
        if($result=null){
            $this->error("没有可以恢复的用户","user/adminList");
        }else{
            $this->success("恢复成功","user/adminList");
        }


    }

    // 渲染编辑页面
    public function adminEdit()
    {
        $id = input ("id");
        $user=UserModel::get($id);
        $this->assign ("id",$user["id"]);
        $this->assign ("name",$user["name"]);
        $this->assign ("password",$user["password"]);
        $this->assign ("phone",$user["phone"]);
        $this->assign ("email",$user["email"]);
        $this->assign ("role",$user["role"]);
        $this->assign ("sex",$user["sex"]);
        return $this -> fetch();
    }

    public function  edit(Request $request)
    {
        $param = $request -> param ();
        $id =input("id");
        $status = 0;

        //验证表单
        $result=$this->validate ($param,"Edit");

        //获取修改过的表单
        foreach ($param as $key => $value){
            if(!empty($value)){
                $data[$key]=$value;
            }
        }

        if($result === true){
            //修改数据库数据
            UserModel::update($data,['id'=>$id]);

            $status = 1;
            $message = "修改成功";

        }else{
            $message = $result."(修改失败)";
        }
        //返回页面需要的信息
        return ['status'=>$status,'message'=>$message];
    }











}