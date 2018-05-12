<?php
namespace app\back\controller;

use think\Request;
use think\Session;
use app\back\controller\Common;

/**
 *  后台首页
 */
class Manage extends Common{

	//后台首页控制器
	public function index() {
		$this->getPublicDate();	
		return $this->fetch();
	}

}