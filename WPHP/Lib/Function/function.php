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
/**
 * [pconst 打印用户自定义的所有常量]
 * 非DEBUG模式不执行
 * @return [type] [description]
 */
function pconst(){
	if(!DEBUG){echo ':(';return;}
	$consts = get_defined_constants(true);
	p($consts['user']);
}
/**
 * [go 跳转函数]
 * headers如果未发送,使用header跳转,否则使用meta标签跳转
 * @param  [type]  $url  [description]
 * @param  integer $time [description]
 * @param  string  $msg  [description]
 * @return [type]        [description]
 */
function go($url,$time=0,$msg=''){
	if(!headers_sent()){
		$time == 0 ? header("Location:{$url}") : header("refresh:{$time},url={$url}");
		die($msg);
	}else{
		echo "<meta http-equiv='Refresh' content='{$time};url={$url}' />";
		if($time) die($msg);
	}
}
/**
 * [halt 错误中断函数]
 * 写入日志,并根据是否开启DEBUG模式,显示页面trace或提示信息。
 * @param  [type]  $error [description]
 * @param  string  $level [description]
 * @param  integer $type  [description]
 * @param  [type]  $dest  [description]
 * @return [type]         [description]
 */
function halt($error,$level='ERROR',$type=3,$dest=NULL){
	if(is_array($error)){
		Log::write($error['message'],$level,$type,$dest);
	}else{
		Log::write($error,$level,$type,$dest);
	}
	$e = array();
	if(DEBUG){
		if(!is_array($error)){
			$e['message'] = $error;
			$trace = debug_backtrace();
			$e['file'] = $trace[0]['file'];
			$e['line'] = $trace[0]['line'];
			ob_start();
			debug_print_backtrace();
			$e['trace'] = htmlspecialchars(ob_get_clean());
		}else{
			$e = $error;
		}	
	}else{
		if($url = C('ERROR_URL')){
			go($url);
		}else{
			$e['message'] = C('ERROR_MSG');
		}
	}

	require TPL_PATH . '/halt.html';
	die;
}
function M($table=null){
	return new Model($table);
}
function K($model){
	$model .= 'Model';
	return new $model();
}
?>