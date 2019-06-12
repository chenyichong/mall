<?php
namespace app\admin\controller;
use app\admin\controller\Base;
use app\admin\model\Goods;

class Test extends Base
{
    public function test()
    {
      $result = Goods::select(function($query){
           $query->field('id,name,id')
               ->whereor('name','like','%1%')
               ->whereor('id','like','%1%')
              ->whereor('color','like','%1%');
       });

      foreach ($result as $result){
          dump ($result->getData());
      }


    }
}