<?php
/**
 * 打印函数
 * @param  [type]
 * @return [type]
 */
function p($var){
	if(is_null($var) OR is_bool($var)){
		var_dump($var);
	}else{
		echo '<pre style="padding:10px;border:1px dashed #ccc;background:#efefef;">' . print_r($var,true) . '</pre>';
	}
}
/**
 * [C 配置函数]
 * 1.载入配置项
 * 2.读取配置项
 * 3.动态设置配置项
 * 4.获取所有配置项
 * @param [type] $var   [description]
 * @param [type] $value [description]
 */
function C($var=NULL,$value=NULL){
	static $config = array();
	if(is_array($var)){
		$config = array_merge($config,array_change_key_case($var,CASE_UPPER));
		return;
	}
	if(is_string($var)){
		if(is_null($value)){
			return isset($config[$var]) ? $config[$var] : null;
		}
		$config[$var] = $value;
		return;
	}
	return $config;
}

function pconst(){
	$consts = get_defined_constants(true);
	p($consts['user']);
}
?>