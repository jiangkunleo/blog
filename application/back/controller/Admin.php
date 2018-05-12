<?php
namespace app\back\controller;

use think\Request;
use think\Session;
use app\back\model\Admin as adminModel;
use app\back\controller\Common;

/**
 *  后台登录
 */
class Admin extends Common{

	public function login() {

		return $this->fetch();
	}

	//验证管理员的合法性
	public function check(Request $request) {
		if($request->isPost()) {
			//检查验证码是否正确
			$passcode = $this->escapeDate(input('passcode'));
	        if(!captcha_check($passcode)) {
	          $this->error('验证码错误，请重新输入！');
	        }

			$admin_name = $this->escapeDate(input('admin_name'));
			$admin_pass = $this->escapeDate(input('admin_pass'));
			//实例化Admin模型对象
			$admin_model = new adminModel();
			$result = $admin_model->check($admin_name,$admin_pass);
			if($result) {
				//合法将管理员信息存到session中
				Session::set('admin_id',$result->admin_id);
				Session::set('admin_name',$result->admin_name);
				Session::set('is_login',1);
				$admin_model->updateAdminInfo($result->admin_id);

				$this->success('登录成功','back/manage/index');die;
			}else{
				//非法
				$this->error('用户名或密码错误！');die;
			}
		}
	}

	//退出登录
	public function loginOut() {
		Session::clear();
		$this->redirect('back/admin/login');die;
	}
}