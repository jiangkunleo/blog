<?php
namespace app\home\model;
use think\Model;

/**
 *  站长信息表模型
 */
class Master extends Model{
	//设置模型对应的完整的数据表名
	protected $table = 'bg_master';

	//查询站长信息
	public function getMasterInfo() {
		return $this->find();
	}

}