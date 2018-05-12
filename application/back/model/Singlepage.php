<?php
namespace app\back\model;
use think\Model;
use think\Db;

/**
 *  后台单页面表模型
 */
class Singlepage extends Model{
	//设置模型对应的完整的数据表名
	protected $table = 'bg_singlepage';

	//获取所有单页面的信息
	public function getPages() {
		return $this->order('page_id desc')->select();
	}

	//单页面信息新增
	public function insertPage($pageInfo) {
		return $this->save($pageInfo);
	}

	//根据id获取单条单页信息
	public function getPageInfoById($page_id) {
		return $this->where(['page_id'=>$page_id])->find();
	}

	//更新一条单页信息
	public function updatePageInfo($pageInfo) {
		return $this->update($pageInfo);
	}

	//删除一条单页信息
	public function delPageInfoById($page_id) {
		return $this->where(['page_id'=>$page_id])->delete();
	}

}