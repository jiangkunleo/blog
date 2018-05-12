<?php
namespace app\home\model;
use think\Model;
use think\Db;

/**
 *  前台评论表模型
 */
class Comment extends Model{
	//设置模型对应的完整的数据表名
	protected $table = 'bg_comment';

	//添加评论
	public function insertComment($cmtInfo) {
		return $this->save($cmtInfo);
	}

	//获取文章评论
	public function getCmtInfoByArtId($art_id) {
		return Db::table('bg_comment')->alias('a')->field('a.*,c.user_image')->join('bg_user c','a.cmt_user = c.user_name')->where(['art_id'=>$art_id])->order('a.cmt_time asc')->paginate(5);
	}

}