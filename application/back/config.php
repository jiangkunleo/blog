<?php
//配置文件
return [
	//定义目录常量
	'view_replace_str' => [
		//后台目录常量
	    '__back__' => '/back',
	    '__editor__' => '/editor',
	],

	//时间自动转换
	'datetime_format'=>true,
	'template'  =>  [
	    'layout_on'     =>  true,
	    'layout_name'   =>  'layout',
	],
	//后台页面跳转页面用自己的配置页面
    'dispatch_success_tmpl'  => APP_PATH .'back' . DS.'view'. DS .'page' . DS . 'ersu.html',
    'dispatch_error_tmpl'    => APP_PATH .'back' . DS.'view'. DS .'page' . DS . 'ersu.html',

];