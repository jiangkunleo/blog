<?php
namespace app\back\controller;

use think\Request;
use think\Session;
use app\back\controller\Common;
use app\back\model\Category as categoryModel;
use app\back\model\Article as articleModel;


/**
 *  文章控制器
 */
class Article extends Common{
	//显示文章首页
	public function index() {
		$this->clear_img(); //删除原图
		$this->clear_thumb_img(); //删除缩略图

		$this->getPublicDate(); //获取公共页面数据
		//获取所有文章
		$article_model = new articleModel();
		$artInfo = $article_model->getArticle();
		$page = $artInfo->render();
		$this->assign('page',$page);
		$this->assign('artInfo',$artInfo);
		return $this->fetch();
	}

	//显示添加文章的表单
	public function add() {
		$this->getPublicDate();
		$category_model = new categoryModel();
		$cateInfo = $category_model->getCategory();
		$this->assign('cateInfo',$cateInfo);
		return $this->fetch();
	}

	//文章添加动作
	public function dealadd() {
		//接收表单
		$art = array();
		$art['cate_id'] = input('cate_id');
		$art['title'] = $this->escapeDate(input('title'));
		$art['author'] = $this->escapeDate(input('author'));
		$art['art_desc'] = $this->escapeDate(input('art_desc'));
		$art['content'] = addslashes(input('content')); 
		//判断数据的合法性
		if(empty($art['content'])||empty($art['author'])||empty($art['art_desc'])||empty($art['title'])) {
			$this->error('填写的信息不完整！');die;
		}
		//判断是否有缩略图上传
		$thumb = 'thumb';
		$thumb_path = $this->upimg($thumb);
		$art['thumb'] = $thumb_path;

		//数据入库，调用模型
		$article_model = new articleModel();
		$result = $article_model->insertArt($art);
		if($result !== false) {
			$this->success('添加文章成功!','back/article/index');die;
		}else{
			$this->error('出现未知错误，添加失败！');die;
		}
	}

	//显示修改文章页面
	public function edit() {
		$this->getPublicDate();
		$art_id = input('art_id');
		$article_model = new articleModel();

		$art = $article_model->getArticleById($art_id);
		$this->assign('art',$art);
		//获取分类信息
		$category_model = new categoryModel();
		$cateInfo = $category_model->getCategory();
		$this->assign('cateInfo',$cateInfo);
		return $this->fetch();
	}

	//修改文章动作
	public function dealedit() {
		//接收表单
		$art = array();
		$art['cate_id'] = input('cate_id');
		$art['title'] = $this->escapeDate(input('title'));
		$art['author'] = $this->escapeDate(input('author'));
		$art['art_desc'] = $this->escapeDate(input('art_desc'));
		$art['content'] = $this->escapeDate(input('content'));
		$art['art_id'] = $this->escapeDate(input('art_id'));

		if(empty($art['title'])||empty($art['author'])||empty($art['art_desc'])||empty($art['content'])) {
			$this->error('填写的信息不完整！');die;
		}
		//判断是否有缩略图上传，暂时省略
		$file = request()->file('thumb');
		if(empty($file)) {
			//说明没有上传文件，用原来的图片
			$art['thumb'] = input('thumb_back');
		}else{
			//说明上传了文件，则需要删除原来的缩略图，更新现在的图片
			$thumb_logo_path = str_replace('/','\\','.'.input('thumb_back'));
			Session::set('thumb_logo_path',$thumb_logo_path);

			$thumb = 'thumb';
			$thumb_path = $this->upimg($thumb);
			$art['thumb'] = $thumb_path;
		}

		$article_model = new articleModel();
		$result = $article_model->updateArtById($art);
		if($result !== false) {
			$this->success('修改文章成功！','back/article/index');die;
		}else{
			$this->error('发生未知错误，修改失败！');die;
		}
	}

	//删除文章的动作
	public function del() {
		$art_id = input('art_id');
		//逻辑删除文章
		$article_model = new articleModel();
		$artInfo = $article_model->getArticleById($art_id);
		$thumb = $artInfo->thumb;
		$result = $article_model->delArticleById($art_id);
		if($result !== false) {
			$this->success('删除成功！','back/article/index');die;
		}else{
			$this->error('发生未知错误，删除失败！');die;
		}
	}

	//批量删除动作
	public function delall() {
		if(!isset($_POST['art_id'])) {
			$this->error('请选择需要删除的文章！');die;
		}
		$art_id = $_POST['art_id'];
		$article_model = new articleModel();
		$result = $article_model->delAllArticle($art_id);
		if($result !== false) {
			$this->success('批量删除成功！','back/article/index');die;
		}else{
			$this->error('发生未知错误，批量删除失败！');die;
		}
	}

	//显示回收站
	public function recycle() {
		$this->clear_thumb_img(); //跳转回
		$this->getPublicDate();
		$article_model = new articleModel();
		$artInfo = $article_model->getDeledArticle();
		$page = $artInfo->render();
		$this->assign('page',$page);
		$this->assign('artInfo',$artInfo);
		return $this->fetch();
	}

	//还原回收站的文章
	public function recover() {
		$art_id = input('art_id');
		$article_model = new articleModel();
		$result = $article_model->recoverArtById($art_id);
		if($result !== false) {
			$this->success('还原成功！','back/article/recycle');die;
		}else{
			$this->error('发生未知错误，还原失败！');die;
		}
	}

	//回收站中根据id号彻底删除一篇文章
	public function realdel() {
		$art_id = input('art_id');
		$article_model = new articleModel();
		$artInfo = $article_model->getArticleById($art_id);
		$thumb_logo_path = $artInfo->thumb;
		$result = $article_model->realDelArticleById($art_id);
		if($result !== false) {
			//文章删除了，缩略图需要删除
			$thumb_logo_path = str_replace('/','\\','.'.$thumb_logo_path);
			Session::set('thumb_logo_path',$thumb_logo_path);

			$this->success('彻底删除成功！','back/article/recycle');die;
		}else{
			$this->error('发生未知错误，删除失败！');die;
		}
	}

	//回收站彻底批量删除
	public function realdelall() {
		if(!isset($_POST['art_id'])) {
			$this->error('请先选择需要删除的文章！');die;
		}
		$art_id = $_POST['art_id'];//数组
		$this->clear_old_imgs($art_id); //删除原缩略图

		$article_model = new articleModel();
		$result = $article_model->realDelAllArticle($art_id);
		if($result !== false) {
			$this->success('彻底批量删除成功！','back/article/recycle');die;
		}else{
			$this->error('发生未知错误，批量删除失败！');die;
		}
	}

	//文章是否推荐
	public function ifrecommend() {
		$art_id = input('art_id');
		$is_recommend = input('is_recommend');
		$page = input('page');
		$article_model = new articleModel();
		$result = $article_model->updateRecomendById($art_id,$is_recommend);
		if($result!== false) {
			$this->success('推荐成功！','back/article/index',['page'=>$page]);die;
		}else{
			$this->error('发生未知错误，推荐失败！');die;
		}
	}


}