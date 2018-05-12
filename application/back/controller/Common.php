<?php
namespace app\back\controller;

use think\Controller;
use think\Request;
use think\Session;
use app\back\model\Admin as adminModel;
use app\back\model\Article as articleModel;
/**
 *  后台公共控制器
 */
class Common extends Controller{
	//调用构造方法（当Common类以下的子类实例化时，因为子类没有定义构造方法，则会默认直接执行此构造方法）
	public function __construct() {
		parent::__construct();
		$this->chLogin();
	}

	//检验用户是否已经登录
	protected function chLogin() {
		$request = Request::instance();
		//主要是检查两个方面，是否存在session，访问是否包含在免登录的页面
		$no_need = ['admin/login','admin/check'];
		$res = !in_array(strtolower($request->controller().'/'.$request->action()),$no_need);
		if($res && Session::get('is_login') != 1 ) {
			$this->error('请先登录！');die;
		}
	}

	//对外部数据进行过滤
	protected function escapeDate($data) {
		return addslashes(strip_tags(trim($data)));
	}

	//给公共模板分配变量
	protected function getPublicDate() {
		$admin_model = new adminModel();
		$res = $admin_model->getCurrentInfo();
		$this->assign('admin_id',$res->admin_id);
		$this->assign('admin_name',$res->admin_name);
		$this->assign('login_ip',$res->login_ip);
		$this->assign('login_nums',$res->login_nums);
		$this->assign('login_time',$res->login_time);		
	}

	//图片上传到项目文件夹并缩略
	protected function upimg($file_img) {
		$file = request()->file($file_img);
		if(empty($file)) {
			$this->error('请选择上传图片');die;
		}
		//上传文件移动到框架应用根目录/public/uploads目录下
		$info = $file->validate(['size'=>8388608,'ext'=>'jpg,png,gif,jpeg'])->move(ROOT_PATH . 'public' . DS . 'uploads');
		//判断文件是否上传到指定文件夹成功！
		if( $info!== false ){
			//调用image类静态方法打开图片
			$big_logo_path = './uploads/'.$info->getSaveName(); //获取源图片保存路径
	    	$image = \think\Image::open($big_logo_path); //打开图片
	    	//缩略图片路径
	    	$thumb_logo_path = './thumb/'.$info->getFileName();
	    	$res = $image->thumb(170,170,\think\Image::THUMB_CENTER)->save($thumb_logo_path );
		    	if($res!==false) {
				    $big_logo_path = str_replace('/','\\',$big_logo_path);
				    Session::set('big_logo_path',$big_logo_path);
		    		//图片缩略成功，将缩略图路径存放到数据库中
					return  '/thumb/'.$info->getFileName();
		    	}else{
		    		return $this->error('图片上传失败！'.$res->getError());die;         
		    	}

		}else{
			$this->error('文件上传失败！'.$file->getError());die;
		}
	}

	//清除上传图片（图片上传后进行缩略，原图不需要了，清除）
	protected function clear_img() {
		//删除原图（新上传的原图有则删，没有则跳过）
		$big_logo_path = @Session::get('big_logo_path');
	    if(file_exists($big_logo_path)&&chmod($big_logo_path,0777)) {	
	     	unlink($big_logo_path);
	     	Session::delete('big_logo_path');
	     	unset($big_logo_path);
	    }
	}

	protected function clear_thumb_img() {
		$thumb_logo_path = @Session::get('thumb_logo_path');
	    if(file_exists($thumb_logo_path)&&chmod($thumb_logo_path,0777)) {	
	     	unlink($thumb_logo_path);
	     	Session::delete('thumb_logo_path');
	     	unset($thumb_logo_path);
	    }
	}


	//删除原来的缩略图
	protected function clear_old_img($thumb_path) {
		if(file_exists($thumb_path)&&chmod($thumb_path,0777)) {
			unlink($thumb_path);
		}
	}

	//批量删除缩略图
	protected function clear_old_imgs($art_id) {
		//根据文章id查询出所有缩略图的路径
		$article_model = new articleModel();
		$thumbs = $article_model->getThumbsByIds($art_id);
		foreach($thumbs as $vo) {
			$path = '.'.$vo['thumb'];
			if(file_exists($path)&&chmod($path,0777)) {
				unlink($path);
			}
		}
	}

}