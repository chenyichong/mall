<?php
namespace app\admin\controller;
use app\admin\controller\Base;
use app\admin\model\Goods as G;
use app\admin\model\Comment;
use app\admin\model\Img;
use think\Request;
use think\Image;
use think\Validate;
class Goods extends Base
{
    public function index()
    {
        $goods  = G::all (function ($query){
            $query->order('sell_time','desc');
        });
        $count = count($goods);
        $this->assign ("goods",$goods);
        $this->assign ("count",$count);

        return $this->fetch();

    }


    //渲染页面 (添加)
    public function addPage()
    {
        return $this->fetch ();
    }

    //渲染页面 (商品详情)
    public function detailsPage()
    {
        //商品本信息
        $goods = G::where('id',input('id'))->find();
        $this->assign ('goods',$goods);
        //评价数量
        $comment = Comment::all(['goods_id'=>input ('id')]);
        $comment_num = count ($comment);
        $this->assign ('comment_num',$comment_num);
        //图片(弹出层表聚合失效)
        $img = Img::find(['goods'=>$goods['id']]);
        $this->assign ('img_1',$img['img_1']);
        $this->assign ('img_2',$img['img_2']);
        $this->assign ('img_3',$img['img_3']);
        $this->assign ('img_4',$img['img_4']);

        return $this->fetch ();
    }


    //验证 商品名称
    public function checkName(Request $request)
    {
        $name =  isset($_POST['name'])?$_POST['name']:"";
        $data = ['name'=>$name];
        $message = "初始信息";
        $status = 0;

        //验证注册信息
        if(G::get($data)){
            $message = "<span style='color:red'>商品已存在</span>";
        }else{
            $result = $this->validate ($data,"Goods.name");
            if($result === true){
                $message = "";
                $status = 1;

            }else{
                $message ="<span style='color:red'>$result</span>";
            }
        }
        return ['message'=>$message,'status'=>$status];
    }


    //添加
    public function add()
    {
        $name = $_POST['name'];
        $color = $_POST['color'];
        $color_2 = $_POST['color_2'];
        $color_3 = $_POST['color_3'];
        $size_s = isset($_POST['size_s'])?$_POST['size_s']:0;
        $size_m = isset($_POST['size_m'])?$_POST['size_m']:0;
        $size_l = isset($_POST['size_l'])?$_POST['size_l']:0;
        $size_xl = isset($_POST['size_xl'])?$_POST['size_xl']:0;
        $size_xxl = isset($_POST['size_xxl'])?$_POST['size_xxl']:0;
        $price = $_POST['price'];
        $type_1 = $_POST['type_1']?$_POST['type_1']:"其他";
        $type_2 = $_POST['type_2']?$_POST['type_2']:null;
        $sex = $_POST['sex'];
        $keyword = $_POST['keyword']?$_POST['keyword']:null;

       //商品预览图处理
        if($file_1= request()->file('image')){
            //存储图片
            $info = $file_1->move('static/images/img');
            $saveName = $info->getSaveName ();

            //图片路径
            //路径image
            $src_0 = "/static/images/img/" . $saveName;
            $src_1 = strtr ($src_0, '\\', '/');
            //路径ipreview_1
            $src_p_0 = "static/images/img/" . $saveName;
            $src_p = strtr ($src_p_0, '\\', '/');
            $src_preview_0 = "/static/images/preview/".time().".jpg";
            $src_preview = strtr ( $src_preview_0, '\\', '/');
        }else{
            $src_1 = null;
            $src_preview = null;
        }
        if( $file_2= request()->file('image_2')){
            //存储图片
            $info_2 = $file_2->move('static/images/img');
            $saveName_2 = $info_2->getSaveName ();
            //路径image_2
            $src_0 = "/static/images/img/" . $saveName_2;
            $src_2 = strtr ($src_0, '\\', '/');
        }else{
            $src_2 = null;
        }
        if( $file_3= request()->file('image_3')){
            //存储图片
           $info_3 = $file_3->move('static/images/img');
            $saveName_3 = $info_3->getSaveName ();

            //路径image_3
            $src_0 = "/static/images/img/" . $saveName_3;
            $src_3 = strtr ($src_0, '\\', '/');
        }else{
            $src_3 = null;
        }
        if($file_4= request()->file('image_4')){
            //存储图片
            $info_4 = $file_4->move('static/images/img');
            $saveName_4 = $info_4->getSaveName ();
            //路径image_4
            $src_0 = "/static/images/img/" . $saveName_4;
            $src_4 = strtr ($src_0, '\\', '/');
        }else{
            $src_4 = null;
        }

        //商品详细图片
        $files = request()->file('details');

        foreach($files as $file){
            $info = $file->move('static/images/details');
            if($info){
                $saveName = $info->getSaveName ();
                //路径image_2
                $src = "/static/images/details/" . $saveName;
                $src = strtr ($src, '\\', '/');
            }else{
                $src = null;
            }
            $srcArr[]=$src;
        }

        $map=[
            'name'=> $name,
            'preview_1'=>$src_preview,
            'img_1'=>$src_1,
            'img_2'=>$src_2,
            'img_3'=>$src_3,
            'img_4'=>$src_4,
            'color'=>$color,
            'color_2'=>$color_2,
            'color_3'=>$color_3,
            'size_s'=>$size_s,
            'size_m'=>$size_m,
            'size_l'=>$size_l,
            'size_xl'=>$size_xl,
            'size_xxl'=>$size_xxl,
            'price'=>$price,
            'type_1'=>$type_1,
            'type_2'=>$type_2,
            'sex'=>$sex,
            'keyword'=>$keyword,
            'sell_time'=>time (),
            'detail_1'=>empty($srcArr[0])?"":$srcArr[0],
            'detail_2'=>empty($srcArr[1])?"":$srcArr[1],
            'detail_3'=>empty($srcArr[2])?"":$srcArr[2],
            'detail_4'=>empty($srcArr[3])?"":$srcArr[3],
            'detail_5'=>empty($srcArr[4])?"":$srcArr[4],
            'detail_6'=>empty($srcArr[5])?"":$srcArr[5],
            'detail_7'=>empty($srcArr[6])?"":$srcArr[6],
            'detail_8'=>empty($srcArr[7])?"":$srcArr[7],
            'detail_9'=>empty($srcArr[8])?"":$srcArr[8],
            'detail_10'=>empty($srcArr[9])?"":$srcArr[9],
            'detail_11'=>empty($srcArr[10])?"":$srcArr[10],
            'detail_12'=>empty($srcArr[11])?"":$srcArr[11],
            'detail_13'=>empty($srcArr[12])?"":$srcArr[12],
            'detail_14'=>empty($srcArr[13])?"":$srcArr[13],
            'detail_15'=>empty($srcArr[14])?"":$srcArr[14],
        ];

        if(G::create($map)){
            //剪裁 preview_1
            $image = Image::open($src_p);
            $image->crop(294, 400,53,0)->save(".".$src_preview);
        }
        //跳转
        echo "<script>window.parent.location.reload();</script>";
    }

    //删除
    public function delete()
    {
        $id=input('id');
        G::destroy($id);
    }

    //修改
    public function modifyName()
    {
        G::where('id',input ('id'))->update(['name'=>input ('name')]);
        $this->redirect ('detailsPage',['id'=>input ('id')]);
    }
    public function modifyPrice()
    {
        G::where('id',input ('id'))->update(['price'=>input ('price'),'']);
        $this->redirect ('detailsPage',['id'=>input ('id')]);
    }
    public function modifySex()
    {
        G::where('id',input ('id'))->update(['sex'=>input ('sex')]);
        $this->redirect ('detailsPage',['id'=>input ('id')]);
    }
    public function modifyColor()
    {
        G::where('id',input ('id'))->update(['color'=>input ('color'),'color_2'=>input ('color_2'),'color_3'=>input ('color_3')]);
        $this->redirect ('detailsPage',['id'=>input ('id')]);
    }
    public function modifySize()
    {
        $map=[
            'size_s' => input ('size_s')?input ('size_s'):0,
            'size_m' => input ('size_m')?input ('size_m'):0,
            'size_l' => input ('size_l')?input ('size_l'):0,
            'size_xl' => input ('size_xl')?input ('size_xl'):0,
            'size_xxl' => input ('size_xxl')?input ('size_xxl'):0,
        ];
        G::where('id',input ('id'))->update($map);
        $this->redirect ('detailsPage',['id'=>input ('id')]);
    }
    public function modifyType()
    {
        $map=[
            'type_1'=>input ('type_1'),
            'type_2'=>input ('type_2'),
        ];
        G::where('id',input ('id'))->update($map);
        $this->redirect ('detailsPage',['id'=>input ('id')]);
    }
    public function modifyKeyword()
    {
        G::where('id',input ('id'))->update(['keyword'=>input('keyword')]);
        $this->redirect ('detailsPage',['id'=>input ('id')]);
    }
    public function modifyImg_1()
    {
        $file= request()->file('img_1');
        //存储图片
        $info = $file->move('static/images/img');
        $saveName = $info->getSaveName ();
        //路径image
        $src = "/static/images/img/" . $saveName;
        $src = strtr ($src, '\\', '/');

        Img::where('goods_id',input('id'))->update(['img_1'=>$src]);
        $this->redirect ('detailsPage',['id'=>input ('id')]);
    }
    public function modifyImg_2()
    {
        $file= request()->file('img_2');
        //存储图片
        $info = $file->move('static/images/img');
        $saveName = $info->getSaveName ();
        //路径image
        $src = "/static/images/img/" . $saveName;
        $src = strtr ($src, '\\', '/');

        Img::where('goods_id',input('id'))->update(['img_2'=>$src]);
        $this->redirect ('detailsPage',['id'=>input ('id')]);
    }
    public function modifyImg_3()
    {
        $file= request()->file('img_3');
        //存储图片
        $info = $file->move('static/images/img');
        $saveName = $info->getSaveName ();
        //路径image
        $src = "/static/images/img/" . $saveName;
        $src = strtr ($src, '\\', '/');

        Img::where('goods_id',input('id'))->update(['img_3'=>$src]);
        $this->redirect ('detailsPage',['id'=>input ('id')]);
    }
    public function modifyImg_4()
    {
        $file= request()->file('img_4');
        //存储图片
        $info = $file->move('static/images/img');
        $saveName = $info->getSaveName ();
        //路径image
        $src = "/static/images/img/" . $saveName;
        $src = strtr ($src, '\\', '/');

        Img::where('goods_id',input('id'))->update(['img_4'=>$src]);
        $this->redirect ('detailsPage',['id'=>input ('id')]);
    }

    //打折页面
    public function discountPage()
    {
        $this->assign ('id',input ('id'));
        return $this->fetch ();
    }
    //打折
    public function discount()
    {
        $goods=G::find(input ('id'));

        //是否已经打折
        if ($goods['original_price'] == 0){
            $price=$goods['price']*input ('discount');
            $map=[
                'original_price'=>$goods['price'],
                'price'=>$price,
            ];
            G::where('id',input ('id'))->update ($map);
        }else{
            $price=$goods['original_price']*input ('discount');
            $map=[
                'price'=>$price,
            ];
            G::where('id',input ('id'))->update ($map);
        }
    }

    //搜索
    public function search()
    {
        $key = $_GET['search'];
        $result = G::all (function($query) use ($key){
            $query->whereor('name','like','%'.$key.'%')
                ->whereor('Goods.id','like','%'.$key.'%')
                ->whereor('type_1','like','%'.$key.'%')
                ->whereor('type_2','like','%'.$key.'%')
                ->whereor('color','like','%'.$key.'%')
                ->whereor('color_2','like','%'.$key.'%')
                ->whereor('color_3','like','%'.$key.'%')
                ->whereor('keyword','like','%'.$key.'%')
                ->order('sell_time','desc')
            ;
        });
        $this->assign ('goods',$result);
        $this->assign('count',count ($result));
        return $this->fetch ();
    }
    
    //测试
    public function test()
    {
        echo time();
    }
}