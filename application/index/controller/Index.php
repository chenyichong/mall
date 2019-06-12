<?php
namespace app\index\controller;
use app\index\controller\Base;
use app\index\model\Goods;
class Index extends Base
{
    public function index()
    {
        $goods = Goods::all(function($query){
            $query->limit(12);
        });
        $this->assign ('goods',$goods);
        return $this -> fetch();
    }
}
