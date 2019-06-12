<?php
namespace app\index\validate;
use think\Validate;
class Register extends Validate
{
    protected $rule =[
        'name|昵称'     =>  "require|length:4,16",
        'phone|手机'      =>  "require|number|length:11,11",
        'email|邮箱'      =>  "require|email",
        'password|密码'   =>  "require|length:6,12",
        'repassword'   =>  "require|confirm:password",
    ];




    protected $message=[
        'name.length'  => '昵称长度必须在4-16个字符内',
        'password.length'  => '密码长度必须在6-12个字符内',
        'phone.length' => '手机格式不对',
        'repassword.require'   =>  "请再次输入密码",
        'repassword.confirm' => '两次密码不一致',
    ];

    protected $scene=[
        'name' => ['name'],
        'phone' => ['phone'],
        'email' => ['email'],
        'password' => ['password'],
        'changePassword' =>['password','repassword'],
    ];

}