<?php
namespace app\home\model;
use think\Model;
use think\Db;

/**
 *  前台文章表模型
 */
class Article extends Model{
	//设置模型对应的完整的数据表名
	protected $table = 'bg_article';

	//获取文章一级分类名称
	public function getRecommendArt($num) {
		return Db::table('bg_article')->alias('a')->field('a.*,c.cate_name')->join('bg_category c','a.cate_id = c.cate_id')->where(['is_del'=>'0','is_recommend'=>'1'])->order('a.add_time desc')->limit($num)->select();
	}

	//获取最新信息+
	public function getNewArt($num) {
		return $this->field('art_id,title')->where(['is_del'=>'0'])->order('add_time desc')->limit($num)->select();
	}

	//获取最热门信息
	public function getRmArtByHits($num) {
		return $this->field('art_id,title')->where(['is_del'=>'0','is_recommend'=>'1'])->order('hits desc')->limit($num)->select();
	}

	//根据栏目id获取器子类所有文章
	public function getArtInfo($cate_id) {
		//先获取去改分类下所有的后代分类的id号
		$ids = $this->getAllSubIds($cate_id);
		$ids .= $cate_id; //将栏目自己id也连接上
		$page = isset($_GET['page']) ? $_GET['page'] : 1;
		return Db::table('bg_article')->alias('a')->field('a.*,c.cate_name')->join('bg_category c','a.cate_id = c.cate_id')->where(['a.is_del'=>'0','a.is_recommend'=>'1',])->where("a.cate_id in($ids)")->order('a.add_time desc')->paginate(5);

	}

	//根据当前分类号获取其所有后代子分类的分类号
	protected function getAllSubIds($cate_id) {
		$id = Db::query("select cate_id from bg_category where cate_pid = $cate_id");
		static $ids = '';
		foreach($id as $row) {
			$ids .= $row['cate_id'].',';
			$this->getAllSubIds($row['cate_id']);
		}
		return $ids;
	}

	//获取当前分类及其所有子分类下的文章点击排行
	public function getSortByHits($cate_id,$num) {
		$ids = $this->getAllSubIds($cate_id);
		//再拼凑上当前分类的id
		$ids .= $cate_id;
		return $this->field('art_id,title')->where(['is_del'=>'0'])->where("cate_id in($ids)")->order('hits desc')->limit($num)->select();
	}

	//获取当前分类及其所有子分类的推荐文章列表
	public function getSortByRecommend($cate_id,$num) {
		//先获取该分类下的所有子分类id
		$ids = $this->getAllSubIds($cate_id);
		//再拼凑上当前分类的id
		$ids .= $cate_id;
		return $this->field('art_id,title')->where(['is_del'=>'0','is_recommend'=>'1'])->where("cate_id in($ids)")->order('add_time desc')->limit($num)->select();
	}

	//根据id获取一篇文章
	public function getArtInfoById($art_id) {
		return $this->where(['art_id'=>$art_id])->find();
	}

	//更新浏览次数
	public function updateHitsById($art_id) {
		return Db::query("update bg_article set  hits=hits+1 where art_id=$art_id");
	}

	//获取上一篇文章 
	public function getPrevArt($art_id) {
		return Db::query("select art_id,title from bg_article where is_del='0' and art_id<$art_id order by art_id desc limit 1");
	}

	//获取下一篇文章 
	public function getNextArt($art_id) {
		return Db::query("select art_id,title from bg_article where is_del='0' and art_id>$art_id order by art_id asc limit 1");
	}

}