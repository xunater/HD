<?php
/**
 * 日志处理类
 */
final class Log{
	/**
	 * [write 写入日志]
	 * @param  [type]  $error [description]
	 * @param  string  $level [description]
	 * @param  integer $type  [description]
	 * @param  [type]  $dest  [description]
	 * @return [type]         [description]
	 */
	public static function write($error,$level='ERROR',$type=3,$dest=NULL){
		$dest = is_null($dest) ? LOG_PATH . '/' . date('Y-m-d') .'.log' : $dest;
		if(is_array($error)){
			error_log($error['message'],$type,$dest);
		}
		if (is_string($error)) {
			$error = '[TIME:'.date('Y-m-d H:i:s').']'.$level.':'.$error."\r\n";
			error_log($error,$type,$dest);
		}
	}
}