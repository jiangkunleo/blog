<?php
namespace app\home\model;
use think\Model;

/**
 *  站长信息表模型
 */
class Singlepage extends Model{
	//设置模型对应的完整的数据表名
	protected $table = 'bg_singlepage';

	//查询站长信息
	public function getSinglepageById($page_id) {
		return $this->where(['page_id'=>$page_id])->find();
	}

}