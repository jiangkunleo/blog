<?php
namespace app\home\model;
use think\Model;

/**
 *  前台文章分类表模型
 */
class Category extends Model{
	//设置模型对应的完整的数据表名
	protected $table = 'bg_category';

	//获取文章一级分类名称
	public function getFirstCate() {
		return $this->where(['cate_pid'=>0])->select();
	}

	//获取下一级子类
	public function getSubCateById($cate_id) {
		return $this->field('cate_id,cate_name')->where(['cate_pid'=>$cate_id,])->select();
	}

	//获取面包屑导航所有父分类的列表
	public function getAllParentCateName($cate_id) {
		//根据当前分类的id查找出它的分类名称和pid，pid用于判断它是否还有父级，当前id为下标name为值存于静态 数组中！
		$cate = $this->field('cate_name,cate_pid')->where(['cate_id'=>$cate_id])->find();
		$cate_name = $cate->cate_name;
		$cate_pid = $cate->cate_pid;
		static $list = array();
		$list[$cate_id] = $cate_name;
		if($cate_pid != 0) {
			//父类如果不是0则说明此分类不是顶级分类，需要递归一直向上查到父级为止！
			$this->getAllParentCateName($cate_pid);
		}
		//将数据 将数组排序反转过来array_reverse
		return array_reverse($list,true);
	}

}