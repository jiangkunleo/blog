<?php
namespace app\back\controller;

use think\Request;
use think\Session;
use app\back\controller\Common;
use app\back\model\Master as masterModel;
//站长控制器
class Master extends Common{
	//显示站长信息
	public function index() {
		$this->getPublicDate(); //获取公共页面数据
		$master_model = new masterModel();
		$masterInfo = $master_model->getMasterInfo();
		$this->assign('masterInfo',$masterInfo);
		return $this->fetch();
	}

	//修改站长信息动作
	public function edit() {
		//接收数据
		$masterInfo = array();
		$masterInfo['nickname'] = $this->escapeDate(input('nickname'));
		$masterInfo['job'] = $this->escapeDate(input('job'));
		$masterInfo['home'] = $this->escapeDate(input('home'));
		$masterInfo['email'] = $this->escapeDate(input('email'));
		$masterInfo['tel'] = $this->escapeDate(input('tel'));
		//验证数据
		if(empty($masterInfo['nickname']) || empty($masterInfo['job'])|| empty($masterInfo['home'])|| empty($masterInfo['email'])|| empty($masterInfo['tel'])) {
			$this->error('请填写完整的信息！');die;
		}
		//调用模型
		$master_model = new masterModel();
		$result = $master_model->updateMasterInfo($masterInfo);
		if($result !== false) {
			$this->success('更新成功！','back/master/index');die;
		}else{
			$this->error('发生未知错误！，更新失败！');die;
		}
	}

}
