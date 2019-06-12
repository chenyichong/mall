<?php
namespace app\admin\validate;
use think\Validate;

class Edit extends Validate
{
    protected $rule=[
        'name|用户名'     =>  "length:3,16",
        'password|密码'   =>  "alphaNum|length:3,16",
        'repassword|密码' =>  "confirm:password",
        'phone|手机'      =>  "number|length:11,11",
        'email|邮箱'      =>  "email",
    ];
    protected $message=[
        'name.length'  => '昵称长度必须在4-16个字符内',
        'password.length'  => '密码长度必须在6-16个字符内',
        'phone.length' => '手机格式不对',
    ];
}