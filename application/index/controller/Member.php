<?php
namespace app\index\controller;
use app\index\controller\Base;
use app\index\model\Cart;
use app\index\model\Member as M;
use app\index\model\Goods;
use think\Request;
use app\index\validate\Register;
use think\Session;
use think\Validate;
use app\index\model\Address;

class Member extends Base
{
    // 登录 页面渲染
    public function login()
    {
        $this->alreadyLogin ();
        return $this->fetch ();
    }

    // 登录 验证
    public function loginCheck()
    {
        $phone = $_POST['phone'];
        $password = md5($_POST['password']);

        $status = 0;

        //验证登录信息
        if(M::get (['phone' => $phone]) == null && M::get (['email' => $phone]) == null){
            $message = "用户不存在";
        }elseif (M::get(['phone'=>$phone,'password'=>$password]) == null && M::get(['email'=>$phone,'password'=>$password]) ==  null){
            $message = "密码错误";
        }elseif(M::get(['phone'=>$phone,'password'=>$password,'status'=>1]) == null && M::get(['email'=>$phone,'password'=>$password,'status'=>1]) ==  null){
            $message = "该用户已被禁用!!";
        }
        else{
            $status = 1 ;
            $message = "登录成功";

            //生成session
            $member=M::get(['phone'=>$phone,'password'=>$password])?M::get(['phone'=>$phone,'password'=>$password]):M::get(['email'=>$phone,'password'=>$password]);
            Session::set ("member_id",$member->id);
            Session::set('member_info',$member->getData ());

            //更新login_time
            $member->save (['login_time'=>time ()]);
        }

        return ['status' => $status , 'message' => $message];
        
    }

    //退出登录
    public function logout()
    {
        Session::delete ('member_id');
        Session::delete ('member_info');
        $this->success('登录注销','member/login');
    }

    //注册 页面渲染
    public function register()
    {
        return $this->fetch();
    }

    //注册 昵称验证
    public function registerCheckName(Request $request)
    {
        $name =  isset($_POST['name'])?$_POST['name']:"";
        $data = ['name'=>$name];
        $message = "初始信息";

        //验证注册信息
        if(M::get($data)){
            $message = "<span style='color:red'>昵称已注册</span>";
        }else{
            $result = $this->validate ($data,"Register.name");
            if($result === true){
                $message = "<span style='color:green'>昵称可用</span>";
            }else{
                $message ="<span style='color:red'>$result</span>";
            }
        }
        return ['message'=>$message];
    }

    //注册 手机验证
    public function registerCheckPhone(Request $request)
    {
        $phone =  isset($_POST['phone'])?$_POST['phone']:"";
        $data = ['phone'=>$phone];
        $message = "初始信息";

        //验证注册信息
        if(M::get($data)){
            $message = "<span style='color:red'>号码已注册</span>";
        }else{
            $result = $this->validate ($data,"Register.phone");
            if($result === true){
                $message = "<span style='color:green'>号码可用</span>";
            }else{
                $message ="<span style='color:red'>$result</span>";
            }
        }
        return ['message'=>$message];
    }

    //注册 昵称验证
    public function registerCheckEmail(Request $request)
    {
        $email =  isset($_POST['email'])?$_POST['email']:"";
        $data = ['email'=>$email];
        $message = "初始信息";

        //验证注册信息
        if(M::get($data)){
            $message = "<span style='color:red'>邮箱已注册</span>";
        }else{
            $result = $this->validate ($data,"Register.email");
            if($result === true){
                $message = "<span style='color:green'>邮箱可用</span>";
            }else{
                $message ="<span style='color:red'>$result</span>";
            }
        }
        return ['message'=>$message];
    }

    //注册 密码验证
    public function registerCheckPassword(Request $request)
    {
        $password =  isset($_POST['password'])?$_POST['password']:"";
        $data = ['password'=>$password];
        $message = "初始信息";

        //验证注册信息
        $result = $this->validate ($data,"Register.password");
        if($result === true){
            $message = "<span style='color:green'>密码可用</span>";
        }else{
            $message ="<span style='color:red'>$result</span>";
        }
        return ['message'=>$message];
    }

    //注册 确认密码验证
    public function registerCheckRepassword(Request $request)
    {
        $password =  isset($_POST['password'])?$_POST['password']:"";
        $repassword =  isset($_POST['repassword'])?$_POST['repassword']:"";

        if($password==$repassword){
            $message ="";
        }else{
            $message ="<span style='color:red'>两次密码不一致</span>";
        }

        return ['message'=>$message];
    }

    //注册
    public function registerMember(Request $request)
    {
        $param = $request->param ();
        $name = $request -> param('name');
        $phone = $request -> param('phone');
        $email = $request -> param('email');
        $password = $request->param ('password');
        $repassword = $request->param ('repassword');
        $status = 0;


        //验证表单
        $result=$this->validate ($param,'Register');
        if($result === true && $password==$repassword){
            if(M::get(['name'=>$name]) == null && M::get(['phone'=>$phone]) == null && M::get(['email'=>$email])==null){
                //验证通过 创建member
                $map=[
                    'name' => $request -> param('name'),
                    'password' => md5( $request -> param('password')),
                    'phone' => $request -> param('phone'),
                    'email' => $request -> param('email'),
                ];
                $member=M::create ($map);
                $message = "注册成功";
                $status = 1;

                //生成session
                Session::set ("member_id",$member->id);
                Session::set ("member_info",$member->getData());
            }else{
                $message = "注册失败";
            }
        }else{
            $message = $result.'(注册失败)';
        }
        return ['message'=>$message,'status'=>$status];

    }



    //渲染页面 修改密码
    public function changePasswordPage()
    {
        return $this->fetch ();
    }

    //修改密码
    public function changePassword()
    {
        $member=M::where('id',MEM_ID)->find();
        $status=0;
        $message="初始信息";
        if(md5($_POST['old_password'])==$member['password']){
            $data=[
                'password'  =>$_POST['password'],
                'repassword'=>$_POST['repassword'],
                ];
            $result=$this->validate ($data,'Register.changePassword');
            if($result === true){
                M::where('id',MEM_ID)->update (['password' => md5($_POST['password'])]);
                $message="修改成功";
            }else{
                $message=$result;
            }
        }else{
            $message="密码错误,修改失败!";
        }
        return['message'=>$message];
    }

    //个人信息
    public function information()
    {

        $member=M::where('id',MEM_ID)->find ();
        $this->assign ('member',$member);
        return $this->fetch ();
    }

    //修改个性签名
    public function updataSign()
    {
        $sign=$_POST['sign'];

        if(M::where('id',MEM_ID)->update (['sign'=>$sign])){
            $message="修改成功";
        }else{
            $message="修改失败";
        }
        return ['message'=>$message];
    }

    //修改昵称
    public function updataName()
    {
        $name=$_POST['name'];
        if(M::where('name',$name)->find ()){
            $message="昵称已存在";
            $status=2;
        }else{
            $data=[
                'name' => $name
            ];
            $result=$this->validate ($data,'Register.name');
            if($result !== true){
                $message=$result;
                $status= 0 ;
            }else{
               $member = M::where('id',MEM_ID)->update (['name'=>$name]);
                $message="修改成功";
                $status = 1;
            }
        }
        return['message'=>$message,'status'=>$status];
    }

    //修改性别
    public function updataSex( )
    {
        if(M::where('id',MEM_ID)->update(['sex'=>$_POST['sex']])){
            $status = 1;
            $message="修改成功!~";
        }else{
            $status = 0;
            $message="未改变~";
        }
        return ['message'=>$message,'status'=>$status];
    }

    //修改头像
    public function updataImg()
    {

        if ( $file = request() ->file('img')){
            $info = $file->validate (['ext'=>'jpg,png'])->move('static/images/touxiang');
            $saveName = $info->getSaveName ();
            //图片路径
            $src_0 = "/static/images/touxiang/" . $saveName;
            $src = strtr ($src_0, '\\', '/');
        }else{
            $src= "/static/images/touxiang/demo.jpg";
        }

        M::where('id',MEM_ID)->update(['img'=>$src]);

        $this->redirect ('information');

    }

    //添加地址页面
    public function addressPage()
    {
        $address = Address::all(["member_id"=>MEM_ID]);
        $this->assign ('address',$address);

        //默认checked
        $member = M::where('id',MEM_ID)->find();
        if($member['address_id'] !== null){
           $checked = Address::where('id',$member['address_id'])->find ();
           $this->assign ('checked',$checked);
        }


        return $this->fetch ();




    }

    //添加地址
    public function addressAdd()
    {
        $data = [
            'name' => $_POST['name'],
            'phone' => $_POST['phone'],
            'address' => $_POST['address'],
            'member_id'=>MEM_ID,
        ];
        $result=$this->validate ($data,'Address');

        if($result ===true){
            $address=Address::create($data);

            if(count (Address::all(['member_id'=>MEM_ID])) == 1)
            {
                M::where('id',MEM_ID)->update(['address_id'=>$address['id']]);
            }
            $status=1;
            $message="添加成功";
        }else{
            $status=0;
            $message=$result;
        }
        return ['message'=>$message,'status'=>$status];
    }

    //选择地址
    public function addressChoose()
    {
        M::where('id',MEM_ID)->update(['address_id'=>$_POST['address_id']]);
    }

    //删除地址
    public function  addressDelete()
    {
        Address::destroy ($_POST["id"]);
        $member=M::where('id',MEM_ID)->find ();
        if($_POST['id'] == $member['address_id']){
            $next = Address::where('member_id',MEM_ID)->find ();
            M::where('id',MEM_ID)->update(['address_id'=>$next['id']]);
        }
    }

    //修改地址

    public function  addressRevisePage()
    {
        $data = Address::where('id',$_POST['id'])->find ();

        return['id'=>$data['id'],'name'=>$data['name'],'phone'=>$data['phone'],'address'=>$data['address']];
    }

//    修改地址
    public function addressRevise()
    {
        $data=[
          'name' => $_POST['name'],
          'phone' => $_POST['phone'],
          'address' => $_POST['address'],
        ];

        $result = $this->validate ($data,'Address');
        if($result === true){
            Address::where('id',$_POST['id']) ->update ($data);
            $message='修改成功!~';
            $status=1;
        }else{
            $message=$result;
            $status=0;
        }
        return['message'=>$message,'status'=>$status];
    }

//    订单页面
    public function orderPage()
    {
        //所有订单
        $order = Cart::all(function($query){
            $query->where('member_id',MEM_ID)->where('order_number','neq','0')->order('id desc');
        });
       $this->assign ("order",$order);

        //地址(订单详情)
        return $this->fetch ();
    }

//    确定colspan的值
    public function countGoods()
    {
        $count = count (Cart::all(['order_number'=>$_POST['order_number']]));

        return['count'=>$count];

    }

//    订单详情
    public function orderDetails()
    {
        //地址
        $address_id = Cart::all(['order_number'=>input('code')]);
        $address = Address::where('id',$address_id[0]['address_id'])->find ();
        $this->assign ('address',$address);

        $order = Cart::all(['order_number'=>input('code')]);
        $this->assign ('order',$order);

        return $this->fetch ();
    }

//    确认收货
    public function takeDelivery(Request $request)
    {
        $num = $request->param('order_number');

        //获取评论权限
        Cart::where('order_number',$request->param('order_number'))
            ->update(['status' => '交易成功','comment_right' => 1]) ;
        //商品销量sold + 1

        //商品销量更新
        $where = function ($query) use ($num) {
            $query->field('goods_id,count')->where('order_number',$num);
        };
        $result = Cart::all($where);

        foreach ($result as  $key => $value){
            $goods = Goods::where('id',$value['goods_id'])->field('sold')->find();
            Goods::where('id',$value['goods_id'])->update (['sold'=> ($goods['sold'] + $value['count'])]);
        }
        return $this->redirect ('orderPage');
    }

//    测试
    public function test()
    {
        dump($_SESSION['think']['member_info']['name']);
    }


}