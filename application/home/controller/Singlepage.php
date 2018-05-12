<?php
namespace app\home\controller;
use app\home\controller\Common;
use think\Request;
use think\Session;
use app\home\model\Master as marsterModel;
use app\home\model\Singlepage as singlepageModel;

/**
 *  前台单页面管理控制器
 */
class Singlepage extends Common{
	//单页面显示 
	public function index() {
		$page_id = input('page_id');
		$singlepage_model = new singlepageModel();
		$pageInfo = $singlepage_model->getSinglepageById($page_id);
		$this->assign('pageInfo',$pageInfo);

		$master_model = new marsterModel();
		$masterInfo = $master_model->getMasterInfo();
		$this->assign('masterInfo',$masterInfo);

		return $this->fetch();
	}
}