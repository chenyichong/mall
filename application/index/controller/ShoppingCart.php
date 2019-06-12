<?php
namespace app\index\controller;
use app\index\controller\Base;
use app\index\model\Cart;
use app\index\model\Goods;
use app\index\model\Member as M;
use app\index\model\Address;
use think\Request;

class ShoppingCart extends Base
{
    public function index()
    {
        $this->isLogin ();

        //购物车
        $cart =Cart::all(function($query){
            $query->where("member_id",MEM_ID)->where('order_number',0);
        });
        $this->assign('cart',$cart);

        //地址
        $address_id = M::find(MEM_ID);
        $address_id = $address_id['address_id'];
        $address = Address::find(['id'=>$address_id]);
        $this->assign('address',$address);

        //确认订单
        $order =Cart::all(function($query){
            $query->where("member_id",MEM_ID)->where('order_number',0);
        });
        $this->assign('order',$order);

        return $this->fetch();
    }



    public function add( ){
        //判断是否登录
        $this->isLogin ();
        //购物车是否存在同样的商品
        $first = Cart::where('goods_id','=',$_POST['goods_id'])
            ->where ('order_number',0)
            ->where('color',input ('color'))
            ->where('size',input('size'))
            ->select();
        if($first == null){
            Cart::create (['name' => $_POST['name'], 'price' => $_POST['price'], 'color' => $_POST['color'], 'size' => $_POST['size'], 'image' => $_POST['image'], 'member_id' => MEM_ID, "goods_id" => $_POST['goods_id']]);
        }else {
            $count = $first[0]['count'] + 1;
            Cart::where('goods_id',$_POST["goods_id"])->update(['count'=>$count]);
        }

        $this->redirect ('goods/single',['id'=>$_POST['goods_id']]);
    }
    //删除
    public function delete(){
        $id=input ('id');
        $color=input ('color');
        $size=input ('size');
        Cart::destroy (function ($query)use($id,$color,$size){
            $query->where('goods_id',$id)
                ->where('size',$size)
                ->where('color',$color)
                ->where('order_number',0)
                ->where('member_id',MEM_ID);
        });
        $this->redirect ('shoppingCart/index');
    }
    //增加数量
    public function increase()
    {
        $goods_id = input ('goods_id');
        //找到数据
        $result = Cart::where('goods_id',$goods_id)
            ->where('member_id',MEM_ID)
            ->where('order_number',0)
            ->where ('color',input ('color'))
            ->where ('size',input ('size'))
            ->find();
        //数量+1
        $new = $result['count'] +1 ;
        Cart::where('goods_id',$goods_id)
            ->where('member_id',MEM_ID)
            ->where('order_number',0)
            ->where ('color',input ('color'))
            ->where ('size',input ('size'))
            ->update(['count'=>$new]);
        $this->redirect ('shoppingCart/index');
    }
    //减少数量
    public function decrease()
    {
        $goods_id = input ('goods_id');
        //找到数据
        $result = Cart::where('goods_id',$goods_id)
            ->where('member_id',MEM_ID)
            ->where('order_number',0)
            ->where ('color',input ('color'))
            ->where ('size',input ('size'))
            ->find();
        //数量-1
        if($result['count'] > 1) {
            $new = $result['count'] - 1;
            Cart::where ('goods_id', $goods_id)
                ->where ('member_id', MEM_ID)
                ->where('order_number',0)
                ->where ('color',input ('color'))
                ->where ('size',input ('size'))
                ->update (['count' => $new]);
        }
        $this->redirect ('shoppingCart/index');

    }


    public function placeOrder()
    {
        $cart = Cart::all(function($query){
            $query->where('member_id',MEM_ID)->where('order_number',0);
        });
        $member = M::where('id',MEM_ID)->find ();
        $address_id=$member['address_id'];
        $time = time ();
        $map=[
            'order_number'        =>    $time,
            'leaving_a_message'   =>    $_POST['leaving_a_message'],
            'status'              =>    '正在派送中',
            'address_id'          =>    $address_id,
//            'address_id'          =>    $,

        ];
        foreach( $cart as $value)
        {
           Cart::where('id',$value['id'])->update ($map);

        }

    }
}