<?php
namespace app\home\controller;
use app\home\controller\Common;
use think\Request;
use think\Session;
use app\home\model\Article as articleModel;
use app\home\model\Category as categoryModel;
use app\home\model\Comment as commentModel;

/**
 *  前台文章管理控制器
 */
class Article extends Common{
	//对应栏目首页动作
	public function index() {
		//接收栏目id
		$cate_id = input('cate_id');
		$article_model = new articleModel();
		$artInfo = $article_model->getArtInfo($cate_id);
		$strpage = $artInfo->render();
		$count = $artInfo->count();
		$this->assign('artInfo',$artInfo);
		$this->assign('strpage',$strpage);
		$this->assign('count',$count);
		//调用公共动作
		$this->Public($cate_id);

		return $this->fetch();
	}

	//公共动作
	protected function Public($cate_id) {
		//获取右侧子类别信息
		$article_model = new articleModel();
		$category_model = new categoryModel();
		$subCate = $category_model->getSubCateById($cate_id);
		$this->assign('subCate',$subCate);
		//获取面包屑导航所有的父类列表
		$list = $category_model->getAllParentCateName($cate_id);
		$this->assign('list',$list);
		//获取点击排行文章
		$sortByHits = $article_model->getSortByHits($cate_id,'5');
		$this->assign('sortByHits',$sortByHits);
		//获取当前分类推荐文章
		$sortByRecommend = $article_model->getSortByRecommend($cate_id,'5');
		$this->assign('sortByRecommend',$sortByRecommend);
	}

	//显示文章内容动作
	public function show() {
		//接收文章id
		$art_id = input('art_id');
		$article_model = new articleModel();
		$row = $article_model->getArtInfoById($art_id);
		$this->assign('row',$row);
		$this->assign('art_id',$art_id);
	    //更新浏览次数
	    $article_model->updateHitsById($art_id);

		$cate_id = $row->cate_id;
		//调用公共动作
		$this->Public($cate_id);
		//获取文章的上一篇和下一篇的信息
		$prev = $article_model->getPrevArt($art_id);
		$next = $article_model->getNextArt($art_id);
		$this->assign('prev',$prev);
		$this->assign('next',$next);
		//提取文章的总评论数
		$comment_model = new commentModel();
		$cmtInfos = $comment_model->getCmtInfoByArtId($art_id);
		$count = $cmtInfos->count();
		$strpage = $cmtInfos->render();
		$this->assign('cmtInfos',$cmtInfos);
		$this->assign('count',$count);
		$this->assign('strpage',$strpage);

		return $this->fetch();
	}



	//处理评论动作
	public function comment() {
		if(!Session::has('user_name')) {
			$this->error('请先登录。如没有注册请先注册再登录！');die;
		}
		//接收数据
		$cmtInfo = array();
		$cmtInfo['art_id'] = input('art_id');
		$cmt_content = $this->escapeDate(input('content'));
		if(empty($cmt_content)) {
			$this->error('评论的内容不能为空');die;
		}
		$cmtInfo['cmt_content'] = $cmt_content;
		$cmtInfo['cmt_user'] = Session::get('user_name');
		$cmtInfo['cmt_time'] = time();

		$comment_model = new commentModel();
		$result = $comment_model->insertComment($cmtInfo);
		if($result !== false) {
			$this->redirect('home/article/show', ['art_id'=>$cmtInfo['art_id']]);die;
		}else{
			$this->error('发生未知错误，评论失败！');die;
		}
		
	}
}
