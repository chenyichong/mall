<?php
namespace app\admin\validate;

use think\Validate;
class Goods extends Validate
{
    protected $rule =[
        'name|商品名称'     =>  "require",

    ];


    protected $message=[

    ];

    protected $scene=[
        'name' => ['name'],
    ];


}

