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
	public static function write($msg,$level='ERROR',$type=3,$dest=NULL){
		if(!C('SAVE_LOG')) return;
		$dest = is_null($dest) ? LOG_PATH . '/' . date('Y-m-d') .'.log' : $dest;
		if(is_dir(LOG_PATH)){
			$error = '[TIME:'.date('Y-m-d H:i:s').']'.$level.':'.$msg."\r\n";
			error_log($error,$type,$dest);
		}	
	}
}
?>