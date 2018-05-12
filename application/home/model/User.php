<?php
namespace app\home\model;
use think\Model;

/**
 *  前台会员表模型
 */
class User extends Model{
	//设置模型对应的完整的数据表名
	protected $table = 'bg_user';

	//查询用户是否存在
	public function if_name_exists($user_name) {
		return $this->where(['user_name'=>$user_name])->select();
	}

	//用户注册信息入库
	public function insertUser($userInfo) {
		return $this->save($userInfo);
	}

	//判断用户名是否正确
	public function check($name,$pass) {
		return $this->where(['user_name'=>$name,'user_pass'=>$pass])->select();
	}

}