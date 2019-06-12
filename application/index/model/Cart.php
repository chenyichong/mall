<?php
namespace app\index\Model;

use think\Model;
class Cart extends Model

{
    protected function getSizeAttr($value)
    {
        $size = ['s'=>'S','m'=>'M','l'=>'L','xl'=>'XL','xxl'=>'XXL'];
        return $size[$value];
    }
}