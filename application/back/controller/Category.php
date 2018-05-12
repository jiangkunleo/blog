<?php
namespace app\back\controller;

use think\Request;
use think\Session;
use app\back\controller\Common;
use app\back\model\Category as categoryModel;

/**
 *  分类控制器
 */
class Category extends Common{
	//显示分类首页页面
	public function index() {
		$this->getPublicDate();	
		$category_model = new categoryModel();
		$cate_info = $category_model->getCategory();
		$this->assign('cate_info',$cate_info);
		return $this->fetch();
	}

	//显示添加页
	public function add() {
		$this->getPublicDate();
		$category_model = new categoryModel();
		$cate_info = $category_model->getCategory();
		$this->assign('cate_info',$cate_info);
		return $this->fetch();
	}

	//接收添加数据
	public function dealadd() {
		//接收数据
		$cate = array();
		$cate['cate_name'] = $this->escapeDate(input('cate_name'));
		$cate['cate_pid'] = input('cate_pid');
		$cate['cate_sort'] = $this->escapeDate(input('cate_sort'));
		$cate['cate_desc'] = $this->escapeDate(input('cate_desc'));
		if(empty($cate['cate_name']) || empty($cate['cate_desc'])|| empty($cate['cate_sort'])) {
			$this->error('信息不完整！');die;
		}
		if(!is_numeric($cate['cate_sort']) || (int)$cate['cate_sort'] != $cate['cate_sort'] ||$cate['cate_sort'] < 1) {
			$this->error('排序应该为1-50');
		}
		$category_model = new categoryModel();
		$result = $category_model->insertcate($cate);
		if($result !== false) {
			$this->success('添加成功','back/category/index');die;
		}else{
			$this->error('添加失败');die;
		}
	}

	//显示分类修改页
	public function edit() {
		$this->getPublicDate();

		$cate_id = input('cate_id');
		$category_model = new categoryModel();

		$cate = $category_model->getCategoryById($cate_id);
		$this->assign('cate',$cate);

		$cate_info = $category_model->getCategory();
		$this->assign('cate_info',$cate_info);

		return $this->fetch();
	}

	//提交修改分类信息
	public function dealedit() {
		//接收数据
		$cate = array();
		$cate['cate_name'] = $this->escapeDate(input('cate_name'));
		$cate['cate_pid'] = input('cate_pid');
		$cate['cate_sort'] = $this->escapeDate(input('cate_sort'));
		$cate['cate_desc'] = $this->escapeDate(input('cate_desc'));
		$cata['cate_id'] = input('cate_id');
		//halt($cate);
		if(empty($cate['cate_name']) || empty($cate['cate_desc'])|| empty($cate['cate_sort'])) {
			$this->error('信息不完整！');die;
		}
		if(!is_numeric($cate['cate_sort']) || (int)$cate['cate_sort'] != $cate['cate_sort'] ||$cate['cate_sort'] < 1) {
			$this->error('排序应该为1-50');die;
		}
		$category_model = new categoryModel();
		$result = $category_model->updateCateById($cate);
		if($result !== false) {
			$this->success('修改成功','back/category/index');die;
		}else{
			$this->error('修改失败');die;
		}		
	}

	//删除某条分类的动作
	public function del() {
		//接收id
		$cate_id = input('cate_id');
		$category_model = new categoryModel();
		//判断分类是否可以删除，如果有子分类则不可以删
		$sub_id = $category_model->getSubId($cate_id);
		if($sub_id) {
			$this->error('此分类存在子分类，不能删除');die;
		}
		//删除分类
		$result = $category_model->delCategoryById($cate_id);
		if($result !== false) {
			$this->success('删除分类成功','back/category/index');die;
		}else{
			$this->error('发生未知原因，删除失败！');die;
		}
	}

	//批量删除分类动作
	public function delall() {
		//先判断有没有用户勾选要删除的分类
		if(!isset($_POST['cate_id'])) {
			$this->error('请先选中要删除的分类');die;
		}
		//接收勾选的id号 数组
		$cate_id = $_POST['cate_id'];
		//在循环中判断数组中的各个分类是否存在子分类，如果存在子分类则不能删除
		$category_model = new categoryModel();
		foreach($cate_id as $id) {
			if($category_model->getSubId($id)) {
				$this->error('不能删除存在子分类的分类！');die;
			}
		}
		//最后批量删除操作
		$result = $category_model->delAllCategory($cate_id);
		if($result !== false) {
			$this->success('删除成功','back/category/index');die;
		}else{
			$this->error('发生未知错误，删除失败！');die;
		}

	}



}