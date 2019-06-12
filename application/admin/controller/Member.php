<?php
namespace app\admin\controller;
use app\admin\controller\Base;
use app\admin\model\Member as M;
use think\Request;
class Member extends Base
{
    //渲染页面(会员列表)
    public function index()
    {
        $member  = M::all ();
        $count = count($member);
        $this->assign ("member",$member);
        $this->assign ("count",$count);
        return $this->fetch();
    }


    // 禁用 启用 (相关字段:status)
    public function changeStatus(Request $request)
    {
        $id = $request -> param('id');
        $result = M::get($id);
        if($result->getData('status')==0){
            M::update(['status'=>1],['id'=>$id]);
        }else{
            M::update(['status'=>0],['id'=>$id]);
        }
    }

    // 软删除
    public function delete()
    {
        $id = $_GET['id'];
        M::destroy ($id);
    }

    //渲染页面(恢复会员)
    public function restorePage()
    {

        $member  = M::onlyTrashed ()->select();
        $count = count($member);
        $this->assign ("member",$member);
        $this->assign ("count",$count);
        return $this->fetch ();
    }

    //恢复删除的会员
    public function restore()
    {
        $result=M::onlyTrashed()->find()->save(['delete_time'=>null]);
        if(null == $result){
            $this->error ("恢复失败","member/restorePage");
        }else{
            $this->redirect ("member/restorePage");
        }
    }

    //彻底删除delete_true
    public function delete_true()
    {
        $id = $_GET['id'];

        M::destroy ($id,true);
    }




    // 页面渲染 (编辑)
    public function editPage()
    {
        $id = input ("id");
        $member = M::get($id);
        $this->assign ('id',$member['id']);
        $this->assign ('name',$member['name']);
        $this->assign ('phone',$member['phone']);
        $this->assign ('email',$member['email']);
        $this->assign ('role',$member['role']);

        return $this->fetch ();
    }



    // 编辑
    public function edit(Request $request)
    {
        $param = $request -> param ();
        $id =input("id");
        $status = 0;

        //验证表单
        $result=$this->validate ($param,"Edit");

        //筛选出修改过的用户信息
        foreach ($param as $key => $value){
            if(!empty($value)){
                $data[$key]=$value;
            }
        }
            if($result === true){
                //修改数据库数据
                M::update($data,['id'=>$id]);

                $status = 1;
                $message = "修改成功";
            }else{
                $message = $result."(修改失败)";
            }

        //返回页面需要的信息
        return ['status'=>$status,'message'=>$message];
    }




    //页面渲染 (添加)
    public function addPage()
    {
        return $this->fetch ();
    }




    // 添加
    public function add(Request $request)
    {
        $data = $request->param();
        $status = 0;
        $message= "初始message";

        //验证
        $result = $this->validate ($data,'User.add');

        //查重
        if(M::where('name','eq',$data['name'])->select()){
            $message="昵称已存在";
        }elseif (M::where('phone','eq',$data['phone'])->select()){
            $message="手机号码已被注册";
        }else{

            //创建
            if($result===true){
                //repassword字段不需要存进数据库
                foreach($data as $key => $value){
                    if($key!== 'repassword'){
                        $data_2[$key] = $value;
                    }
                }

                $user = M::create($data_2);
                $message = "添加成功";
                $status = 1;
            }else{
                $message = $result;

                //当创建不通过时保留表单数据
                foreach ($data as $key => $value){
                    if(!empty($value)){
                        $this->assign ($key,$value);
                    }
                }
            }
        }
        return ['status'=>$status, 'message'=>$message];
    }

//    搜索页面
    public function search()
    {
        $key = $_GET['seach'];
        if($key=='已启用'){
            $status=1;
            $result = M::all (['status'=>$status]);
        }elseif ($key=='已禁用'){
            $status=0;
            $result = M::all (['status'=>$status]);
        }else{
            $result = M::all (function($query) use ($key){
                $query->whereor('name','like','%'.$key.'%')
                    ->whereor('id','like','%'.$key.'%')
                    ->whereor('phone','like','%'.$key.'%')
                    ->whereor('email','like','%'.$key.'%')
                    ->whereor('status','like','%'.$key.'%');
            });
        }
        $this->assign ('member',$result);
        $this->assign('count',count ($result));
        return $this->fetch ();
    }

}