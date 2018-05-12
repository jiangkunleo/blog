<?php
namespace app\back\model;
use think\Model;
use think\Db;
use think\Session;

/**
 *  后台管理员表
 */
class Admin extends Model{
	//设置模型对应的完整的数据表名
	protected $table = 'bg_admin';

	//验证管理员的合法性
	public function check($name,$pass) {
		$pass = md5($pass);
		return $this->where(['admin_name'=>$name,'admin_pass'=>$pass])->find();
	}

	//给登录用户添加附加信息
	public function updateAdminInfo($admin_id) {
		$login_ip = $_SERVER["REMOTE_ADDR"];
		$login_time = time();
		$sql = "update bg_admin set login_ip='$login_ip',login_time=$login_time,login_nums=login_nums+1 where admin_id=$admin_id";
		return Db::execute($sql);
	}

	//获取当前用户的信息
	public function getCurrentInfo() {
		$admin_id = Session::get('admin_id');
		return $this->where(['admin_id'=>$admin_id])->find();
	}
	

}