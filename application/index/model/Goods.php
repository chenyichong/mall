<?php
namespace app\index\model;
use think\model\Merge;

class Goods extends Merge
{
    protected $relationModel=['Img'];
    protected $fk = 'goods_id';
    protected $mapFields=[
        'id' =>'Goods.id',
        'img_id'=>'Img.id',
    ];
}