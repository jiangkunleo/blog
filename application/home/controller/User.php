<?php
namespace app\home\controller;
use app\home\controller\Common;
use think\Request;
use think\Session;

use app\home\model\Master as marsterModel;
use app\home\model\Article as articleModel;
use app\home\model\Category as categoryModel;
use app\home\model\User as userModel;

/**
 *  前台会员管理控制器
 */
class User extends Common{
	//会员注册页动作
	public function register() {
		//提出站长信息
		$master_model = new marsterModel();
		$masterInfo = $master_model->getMasterInfo();
		$this->assign('masterInfo',$masterInfo);
		//提取最新文章信息
		$article_model = new articleModel();
		$newArt = $article_model->getNewArt(8);
		$this->assign('newArt',$newArt);
		//提取最热门文章信息
		$rmArtByHits = $article_model->getRmArtByHits(8);
		$this->assign('rmArtByHits',$rmArtByHits);

		return $this->fetch();
	}

	//会员注册提交动作 
	public function dealregister() {
		//接收数据
		$userInfo = array();
		$user_name = $this->escapeDate(input('user_name'));
		//判断用户名是否为空或已经存在
		if(empty($user_name)) {
			$this->error('用户名不能为空');die;
		}
		$user_model = new userModel();
		if( !empty($user_model->if_name_exists($user_name)) ) {
			//用户名已经存在
			$this->error('用户名已经存在，请重新填写');die;
		}
		$userInfo['user_name'] = $user_name;
		$user_pass1 = $this->escapeDate(input('pass1'));
		$user_pass2 = $this->escapeDate(input('pass2'));
		if(empty($user_pass1)||empty($user_pass1)) {
			$this->error('密码不能为空');die;
		}
		if($user_pass1 !== $user_pass2) {
			$this->error('两次密码不一致');die;
		}
		$userInfo['user_pass'] = md5($user_pass1);
		//判断是否有缩略图上传
		$user_image = 'user_image';
		$thumb_path = $this->upimg($user_image);
		$userInfo['user_image'] = $thumb_path;

		$result = $user_model->insertUser($userInfo);
		if($result!==false) {
			$this->success('注册成功！','home/user/login');die;
		}else{
			$this->error('发生未知错误，重新注册！');die;
		}
	}

	//会员登录页面
	public function login() {
		$this->clear_img();
		//提出站长信息
		$master_model = new marsterModel();
		$masterInfo = $master_model->getMasterInfo();
		$this->assign('masterInfo',$masterInfo);
		//提取最新文章信息
		$article_model = new articleModel();
		$newArt = $article_model->getNewArt(8);
		$this->assign('newArt',$newArt);
		//提取最热门文章信息
		$rmArtByHits = $article_model->getRmArtByHits(8);
		$this->assign('rmArtByHits',$rmArtByHits);

		return $this->fetch();
	}

	//会员登录动作
	public function deallogin() {
		//接收数据
		$user_name = $this->escapeDate(input('user_name'));
		$user_pass = $this->escapeDate(input('pass'));
		if(empty($user_name)||empty($user_pass)) {
			$this->error('用户名、密码不能为空');die;
		}
		$user_model = new userModel();
		$result = $user_model->check($user_name,md5($user_pass));
		if(!empty($result)) {
			//设置session
			Session::set('user_name',$user_name);
			Session::set('user_id',$result[0]['user_id']);
			$this->success('登录成功！','home/index/index');die;
		}else{
			$this->error('用户名或密不正确！');die;
		}
	}

	//会员退出登录
	public function logout() {
		Session::delete('user_name');
		Session::delete('user_id');
		$this->redirect('home/index/index');
	}


}