<?php
namespace app\back\model;
use think\Model;
use think\Db;

/**
 *  文章表模型
 */
class Article extends Model{
	//设置模型对应的完整的数据表名
	protected $table = 'bg_article';

	//添加一篇文章
	public function insertArt($art) {
		//完善其他数据
		//$art['thumb'] = 'default.jpg';
		$art['add_time'] = time();
		return $this->save($art);
	}

	//获取所有文章
	public function getArticle() {
		return Db::table('bg_article')->alias('a')->field('a.*,c.cate_name')->join('bg_category c','a.cate_id = c.cate_id')->where('is_del','0')->order('a.add_time desc')->paginate(2);
	}

	//根据id获取一条文章信息
	public function getArticleById($art_id) {
		return $this->where(['art_id'=>$art_id])->find();
	}

	//修改一篇文章信息
	public function updateArtById($art) {
		return $this->update($art);
	}

	//根据id逻辑删除该条文章的数据
	public function delArticleById($art_id) {
		return $this->where(['art_id'=>$art_id])->update(['is_del'=>1]);
	}

	//根据多个id批量逻辑删除
	public function delAllArticle($art_id) {
		return $this->where('art_id','in',$art_id)->update(['is_del'=>1]);
	}

	//获取所有逻辑删除的文章
	public function getDeledArticle() {
		return Db::table('bg_article')->alias('a')->field('a.*,c.cate_name')->join('bg_category c','a.cate_id = c.cate_id')->where('is_del','1')->order('a.add_time desc')->paginate(2);
	}

	//根据id还原逻辑删除的文章
	public function recoverArtById($art_id) {
		return $this->where(['art_id'=>$art_id])->update(['is_del'=>0]);
	}

	//根据id彻底删除回收站中的文章
	public function realDelArticleById($art_id) {
		return $this->where(['art_id'=>$art_id])->delete();
	}

	//根据id 彻底批量删除回收站中的文章
	public function realDelAllArticle($art_id) {
		return $this->where('art_id','in',$art_id)->delete();
	}

	//根据数据id 查找所有缩略图路径数组
	public function getThumbsByIds($art_id) {
		return $this->field('thumb')->where('art_id','in',$art_id)->select();
	}

	//根据id更改是否推荐
	public function updateRecomendById($art_id,$is_recommend) {
		if($is_recommend == 1) {
			$is_recommend = 0;
		}else{
			$is_recommend = 1;
		}
		return $this->where(['art_id'=>$art_id])->update(['is_recommend'=>$is_recommend]);
	}

}