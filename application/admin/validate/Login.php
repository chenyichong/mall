<?php
namespace app\admin\validate;
use think\Validate;

class Login extends Validate
{
    protected $rule = [

        'name|用户名'     =>  "require",
        'password|密码'   =>  "require",
        'captcha|验证码'  =>  "require|captcha",
    ];
}