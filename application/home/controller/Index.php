<?php
namespace app\home\controller;
use app\home\controller\Common;
use think\Request;
use think\Session;
use app\home\model\Article as articleModel;
use app\home\model\Category as categoryModel;
use app\home\model\Master as marsterModel;
/**
 *  前台公共控制器
 */
class Index extends Common{
	//显示前台首页
	public function index() {
		//获取推荐文章的信息5条
		$article_model = new articleModel();
		$recommendArt = $article_model->getRecommendArt(5);
		$this->assign('recommendArt',$recommendArt);
		//提出站长信息
		$master_model = new marsterModel();
		$masterInfo = $master_model->getMasterInfo();
		$this->assign('masterInfo',$masterInfo);
		//提取最新文章信息
		$newArt = $article_model->getNewArt(8);
		$this->assign('newArt',$newArt);
		//提取最热门文章信息
		$rmArtByHits = $article_model->getRmArtByHits(8);
		$this->assign('rmArtByHits',$rmArtByHits);

		return $this->fetch();
	}
}
