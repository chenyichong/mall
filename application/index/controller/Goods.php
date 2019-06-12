<?php
namespace app\index\controller;
use app\index\controller\Base;
use app\index\model\Goods as G;
use app\index\model\Member as M;
use app\index\model\Cart;
use app\index\model\Comment;
use think\Request;

class Goods extends Base
{
    //渲染 (商品列表)
    public function index()
    {
        //排序
        if(input ('order') == "最新" || input('order')==null){
            $goods = G::order('sell_time desc')->paginate(12);
        }elseif (input ('order') == "人气"){
            $goods = G::order('sold desc')->paginate(12);
        }elseif(input ('order') == "价格"){
            $goods = G::order('price asc')->paginate(12);
        }
        //分页
        $page = $goods->render();
        $num = count($goods);
        $this->assign('page', $page);
        $this->assign ('goods',$goods);
        $this->assign ('num',$num);
        $this->assign('order',input ('order'));
        return $this->fetch();
    }

    //商品页面
    public function single()
    {
        $id=input ('id');

        //商品信息
        $goods = G::get ($id);
        $this->assign ('goods',$goods);
        //平敬爱信息
        $comment = Comment::all(['goods_id'=>$id]);
        $this->assign('comment',$comment);
        //评价数量
        //好评数量
        $good = Comment::all(['goods_id'=>$id,'evaluation'=>'好评']);
        $count_good = count ($good);
        $this->assign ('count_good',$count_good);
        //中评数量
        $soso = Comment::all(['goods_id'=>$id,'evaluation'=>'中评']);
        $count_soso = count ($soso);
        $this->assign ('count_soso',$count_soso);
        //差评数量
        $bad = Comment::all(['goods_id'=>$id,'evaluation'=>'差评']);
        $count_bad = count ($bad);
        $this->assign ('count_bad',$count_bad);
        //有图评价数量
        $has_and_no_img = Comment::all(['goods_id'=>$id]);
        $no_img = Comment::all(['goods_id'=>$id,'image_1'=>'']);
        $count_has_and_no_img = count ($has_and_no_img);
        $count_no_img = count ($no_img);
        $count_has_img =  $count_has_and_no_img - $count_no_img;
        $this->assign ('count_has_img',$count_has_img);

        return $this->fetch ();
    }

    //商品页面(自动定位到评论区域)
    public function singleComment()
    {
        $id=input ('id');

        //商品信息
        $goods = G::get ($id);
        $this->assign ('goods',$goods);

        //评论信息 全部 好评 中评 差评 有图
        if(empty(input ('pj'))){
            $comment = Comment::all(function($query)use($id){
                $query->where('goods_id',$id)->order('time','desc');
            });
            $this->assign ('comment',$comment);
            $this->assign ('pj_status',100);
        }elseif (input ('pj') == "全部"){
            $comment = Comment::all(function($query)use($id){
                $query->where('goods_id',$id)->order('time','desc');
            });
            $this->assign ('comment',$comment);
            $this->assign ('pj_status',"全部");
        }elseif (input ('pj') == "好评"){
            $comment = Comment::all(function($query)use($id){
                $query->where('goods_id',$id)->where('evaluation','好评')->order('time','desc');
            });
            $this->assign ('comment',$comment);
            $this->assign ('pj_status',"好评");
        }elseif (input ('pj') == "中评"){
            $comment = Comment::all(function($query)use($id){
                $query->where('goods_id',$id)->where('evaluation','中评')->order('time','desc');
            });
            $this->assign ('comment',$comment);
            $this->assign ('pj_status',"中评");
            $comment = Comment::all(function($query)use($id){
                $query->where('goods_id',$id)->where('evaluation','差评')->order('time','desc');
            });
            $this->assign ('comment',$comment);
            $this->assign ('pj_status',"差评");
        }elseif (input ('pj') == "有图"){
            $comment = Comment::all(function($query)use($id){
                $query->where('goods_id',$id)->where('image_1','neq','')->order('time','desc');
            });
            $this->assign ('comment',$comment);
            $this->assign ('pj_status',"有图");
        }

        //评价数量
        //好评数量
        $good = Comment::all(['goods_id'=>$id,'evaluation'=>'好评']);
        $count_good = count ($good);
        $this->assign ('count_good',$count_good);
        //中评数量
        $soso = Comment::all(['goods_id'=>$id,'evaluation'=>'中评']);
        $count_soso = count ($soso);
        $this->assign ('count_soso',$count_soso);
        //差评数量
        $bad = Comment::all(['goods_id'=>$id,'evaluation'=>'差评']);
        $count_bad = count ($bad);
        $this->assign ('count_bad',$count_bad);
        //有图评价数量
        $has_and_no_img = Comment::all(['goods_id'=>$id]);
        $no_img = Comment::all(['goods_id'=>$id,'image_1'=>'']);
        $count_has_and_no_img = count ($has_and_no_img);
        $count_no_img = count ($no_img);
        $count_has_img =  $count_has_and_no_img - $count_no_img;
        $this->assign ('count_has_img',$count_has_img);

        return $this->fetch ();
    }

    //检查评论权限
    public function commentRight()
    {
        $cart = Cart::where('member_id',MEM_ID)
            ->where('goods_id',input ('goods_id'))
            ->where('comment_right',1)
            ->find ();

        if($cart == null){
            $status=0;
        }else{
            $status=1;
        }

        return['status'=>$status,'cart_id'=>$cart['id']];
    }

    //评论页面
    public function commentPage()
    {
        $this->assign ('goods_id',input ('goods_id'));
        return $this->fetch ();
    }

    //添加评论
    public function commentAdd(Request $request)
    {
        //评论图片
            $files = $request->file('image');

            foreach($files as $file){
                $info = $file->move('static/images/comment');
                if($info){
                    $saveName = $info->getSaveName ();
                    //路径image_2
                    $src = "/static/images/comment/" . $saveName;
                    $src = strtr ($src, '\\', '/');
                }else{
                    $src = null;
                }
                $srcArr[]=$src;
        }

        //获取cart_id member_id member_touxiang goods_size goods_color
        $cart = Cart::where('member_id',MEM_ID)
            ->where('goods_id',$_POST['goods_id'])
            ->where('comment_right','1')
            ->find ();
        $cart_id    = $cart['id'];
        $cart_color = $cart['color'];
        $cart_size  = $cart['size'];

        $member = M::where('id',MEM_ID)->find();
        $member_name     = $member['name'];
        $member_touxiang = $member['img'];

        //数据
        $map=[
            'goods_id'        =>  $_POST['goods_id'],
            'goods_color'     =>  $cart_color ,
            'goods_size'      =>  $cart_size,
            'member_name'     =>  $member_name,
            'member_touxiang' =>  $member_touxiang,
            'cart_id'         =>  $cart_id,
            'evaluation'      =>  $_POST['evaluation'],
            'content'         =>  htmlspecialchars ($_POST['content'])?htmlspecialchars ($_POST['content']):"(用户很懒,什么都没写~)",
            'time'            =>  time(),
            'image_1'         =>  empty($srcArr[0])?"":$srcArr[0],
            'image_2'         =>  empty($srcArr[1])?"":$srcArr[1],
            'image_3'         =>  empty($srcArr[2])?"":$srcArr[2],
            'image_4'         =>  empty($srcArr[3])?"":$srcArr[3],
        ];

        //录入数据库
        Comment::create ($map);

        //修改cart表的comment_right
        Cart::where('member_id',MEM_ID)
        ->where('goods_id',$_POST['goods_id'])
        ->where('comment_right','1')
        ->update(['comment_right'=>0]);

        //跳转
        echo "<script>window.parent.location.reload();</script>";
    }

    public function search()
    {
        $keyword = input ('search');

        //分割搜索关键字，空格处切割
        $str = explode(" ",$keyword);
        strtr ($keyword," ","");

        if(count($str)==1){
            $goods = G::whereor('keyword|name|type_1|type_2|color|color_2|color_3|sex','like','%'.$keyword.'%')
                ->paginate(12);
        }elseif(count($str)==2){
            $goods = G::whereor('keyword|name|type_1|type_2|color|color_2|color_3|sex','like','%'.$str[0].'%')
                ->where ('keyword|name|type_1|type_2|color|color_2|color_3|sex','like','%'.$str[1].'%')
                ->paginate(12);
        }else{
            $goods = G::whereor('keyword|name|type_1|type_2|color|color_2|color_3|sex','like','%'.$str[0].'%')
                ->where ('keyword','like','%'.$str[1].'%')
                ->where ('name|type_1|type_2|color|color_2|color_3|sex','like','%'.$str[2].'%')
                ->paginate(12);
        }
        //页数
        $page = $goods->render();

        $this->assign ('goods',$goods);
        $this->assign('page', $page);
        $this->assign ('order','最新');

        return $this->fetch ();
    }

    //分割测试
    public function test(){
        $s='黑龙江   黑河县';
        $str = explode(" ",$s);
        foreach ($str as $key=>$val){
            if($val !== ''){
                echo $val;
                echo "<br>";
            }
        }
//        print_r($str);//输出Array ( [0] => 黑龙江 [1] => [2] => 黑河县 [3] =>
    }

}