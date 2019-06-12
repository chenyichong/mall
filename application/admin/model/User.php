<?php
namespace app\admin\model;
use think\Model;
use traits\model\SoftDelete;
class User extends Model
{
    //过滤非数据库字段
    protected $field = true;

    //软删除
    use SoftDelete;
    protected $deleteTime="delete_time";

    protected function getRoleAttr($value)
    {
        $role = [0=>'超管',1=>'管理员',2=>'客服',3=>'物流'];
        return $role[$value];
    }


}