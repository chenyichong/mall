<?php
namespace app\index\validate;
use think\Validate;

class Address extends Validate
{
    protected $rule =[
        'name|收件人'     =>  "require",
        'phone|手机'      =>  "require|number",
        'address|收货地址'=>  "require",
    ];
}