<?php
namespace app\index\controller;


class Test
{
    public function index()
    {
        $image = \think\Image::open('./image.jpg');
        $image->crop(294, 400,53,0)->save('./crop.png');
    }
}