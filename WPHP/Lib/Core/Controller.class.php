<?php
class Controller{
	/**
	 * [__construct 扩展构造函数]
	 */
	public function __construct(){
		if(method_exists($this, '__auto')){
			$this->__auto();
		}
		if(method_exists($this, '__init')){
			$this->__init();
		}
	}

	public function success($msg='操作成功',$url='',$time=1){
		$url = $url ? "window.location.href='".$url."'" : "window.history.go(-1)";
		include TPL_PATH . '/success.html';
	}

	public function error($msg='操作失败',$url='',$time=3){
		$url = $url ? "window.location.href='".$url."'" : "window.history.go(-1)";
		include TPL_PATH . '/error.html';
	}

}
?>