<?php
namespace app\back\controller;

use think\Request;
use think\Session;
use app\back\controller\Common;
use app\back\model\Singlepage as singlepageModel;

/**
 *  单页面控制器
 */
class Singlepage extends Common{

	//显示单页管理控制器
	public function index() {
		$this->getPublicDate(); //获取公共页面数据
		$singlepage_model = new singlepageModel();
		$pageInfo = $singlepage_model->getPages();
		$this->assign('pageInfo',$pageInfo);
		return $this->fetch();
	}

	//显示添加单页
	public function add() {
		$this->getPublicDate(); //获取公共页面数据

		return $this->fetch();
	}


	//添加单页信息动作
	public function dealadd() {
		//接收表单
		$pageInfo = array();
		$pageInfo['title'] = $this->escapeDate(input('title'));
		$pageInfo['content'] = addslashes(input('content'));
		//判断数据的合法性
		if(empty($pageInfo['title']) || empty($pageInfo['content']) )  {
			$this->error('请填写完整的信息！');die;
		}
		$singlepage_model = new singlepageModel();
		$result = $singlepage_model->insertPage($pageInfo);
		if($result !== false) {
			$this->success('添加单页信息成功！','back/singlepage/index');die;
		}else{
			$this->error('发生未知错误，添加失败！');die;
		}

	}

	//显示修改单页信息
	public function edit() {
		$this->getPublicDate(); //获取公共页面数据
		//接收id
		$page_id = input('page_id');
		$singlepage_model = new singlepageModel();
		$pageInfo = $singlepage_model->getPageInfoById($page_id);
		$this->assign('pageInfo',$pageInfo);
		
		return $this->fetch();
	}

	//提交修改单页信息动作
	public function dealedit() {
		//接收表单
		$pageInfo = array();
		$pageInfo['title'] = $this->escapeDate(input('title'));
		$pageInfo['content'] = addslashes(input('content'));
		$pageInfo['page_id'] = input('page_id');
		//判断数据的合法性
		if(empty($pageInfo['title']) || empty($pageInfo['content']) )  {
			$this->error('请填写完整的信息！');die;
		}
		$singlepage_model = new singlepageModel();
		$result = $singlepage_model->updatePageInfo($pageInfo);
		if($result !== false) {
			$this->success('修改单页信息成功！','back/singlepage/index');die;
		}else{
			$this->error('发生未知错误，修改失败！');die;	
		}

	}

	//删除单页信息动作
	public function del() {
		//获取id
		$page_id = input('page_id');
		$singlepage_model = new singlepageModel();
		$result = $singlepage_model->delPageInfoById($page_id);
		if($result !== false) {
			$this->success('删除单页信息成功！','back/singlepage/index');die;
		}else{
			$this->error('发生未知错误，删除失败！');die;	
		}
	}

}