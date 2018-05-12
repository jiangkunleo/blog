<?php
//截取中文字符串
function subtext($text,$length) {
	if(mb_strlen($text,'utf8') > $length) {
		return mb_substr($text,0,$length,'utf8').'....';
	}
	return $text;
}
