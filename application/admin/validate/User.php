<?php
namespace app\admin\validate;
use think\Validate;

class User extends Validate
{
    protected $rule =[
        'name|用户名'     =>  "require|length:3,16",
        'password|密码'   =>  "require|alphaNum|length:6,16|confirm:password",
        'repassword|密码' =>  "require|confirm:password",
        'captcha|验证码'  =>  "require|captcha",
        'phone|手机'      =>  "require|number|length:11,11",
        'email|邮箱'      =>  "require|email",
    ];

    protected $message=[
        'name.length'  => '昵称长度必须在4-16个字符内',
        'password.length'  => '密码长度必须在6-16个字符内',
        'phone.length' => '手机格式不对',
    ];

    protected $scene = [
        'add'   => ['name','password','repassword','phone','email'],
        'test'  => ['name'],

    ];
}