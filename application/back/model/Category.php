<?php
namespace app\back\model;
use think\Model;

/**
 *  文章分类表模型
 */
class Category extends Model{
	//设置模型对应的完整的数据表名
	protected $table = 'bg_category';

	//获取所有分类信息，并树状整理
	public function getCategory() {
		$list = $this->order('cate_sort asc')->select();
		return $this->getCateTree($list);
	}

	//添加一条分类信息
	public function insertcate($cate) {
		return $this->save($cate);
	}

	//修改一条分类信息
	public function updateCateById($cate) {
		return $this->save($cate);
	}

	//根据id号获取单个分类的信息
	public function getCategoryById($cate_id) {
		return $info = $this->where(['cate_id'=>$cate_id])->find();
	}

	//对分类信息进行树状排序整理
	private function getCateTree($list,$pid=0,$level=0) {
		static $cate_list = array();
		foreach($list as $row) {
			if($row['cate_pid'] == $pid) {
				$row['level'] = $level;
				$cate_list[] = $row;
				$this->getCateTree($list,$row['cate_id'],$level+1);
			}
		}
		return $cate_list;
	}

	//判断分类是否可以删除
	public function getSubId($cate_id) {
		return $this->where(['cate_pid'=>$cate_id])->select();
	}

	//删除某条分类信息
	public function delCategoryById($cate_id) {
		return $this->where(['cate_id'=>$cate_id])->delete();
	}

	//批量删除分类
	public function delAllCategory($cate_id) {
		return $this->destroy($cate_id);
	}
}