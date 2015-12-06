<?php
/**
 * 核心类
 */
final class WPHP{
	public static function run(){
		self::_set_const();
		if(DEBUG){
			self::_create_dir();
			self::_import_file();
		}else{
			require_once TEMP_PATH . '/~boot.php';
			error_reporting(0);
		}
		
		Application::run();
	}
	/**
	 * [_import_file 导入文件]
	 * @return [type] [description]
	 */
	private static function _import_file(){
		$arr = array(
			CORE_PATH . '/Log.class.php',
			FUNCTION_PATH . '/function.php',
			CORE_PATH . '/Application.class.php',
			CORE_PATH . '/Controller.class.php'
		);
		$str = '';
		foreach ($arr as $v) {
			$str .= substr(file_get_contents($v), 5,-2);
			require_once $v;
		}
		$str = "<?php\r\n" . preg_replace('/\/\*[\s\S]*?\*\//', '', $str) ."?>";
		file_put_contents(TEMP_PATH . '/~boot.php', $str);
	}
	/**
	 * 创建目录
	 */
	private static function _create_dir(){
		$arr = array(
			APP_PATH,
			APP_CONFIG_PATH,
			APP_CONTROLLER_PATH,
			APP_TPL_PATH,
			APP_PUBLIC_PATH,
			TEMP_PATH,
			LOG_PATH,
			COMMON_PATH,
			COMMON_CONFIG_PATH,
			COMMON_LIB_PATH,
			COMMON_MODEL_PATH
		);
		foreach ($arr as $v) {
			is_dir($v) OR (mkdir($v,0777,true) && chmod($v, 0777) );
		}
		$successTpl = APP_TPL_PATH . '/success.html';
		$errorTpl = APP_TPL_PATH . '/error.html';
		is_file($successTpl) OR (copy(TPL_PATH . '/success.html', $successTpl) && chmod($successTpl, 0777));
		is_file($errorTpl) OR (copy(TPL_PATH . '/error.html', $errorTpl) && chmod($errorTpl, 0777));
	}
	/**
	 * 设置常量
	 */
	private static function _set_const(){
		defined('DEBUG') OR define('DEBUG',false);
		$path = str_replace('\\', '/', __FILE__);
		define('WPHP_PATH', dirname($path));
		define('CONFIG_PATH', WPHP_PATH . '/Config');
		define('DATA_PATH', WPHP_PATH . '/Data');
		define('TPL_PATH', DATA_PATH . '/Tpl');
		define('LIB_PATH', WPHP_PATH . '/Lib');
		define('CORE_PATH', LIB_PATH . '/Core');
		define('FUNCTION_PATH', LIB_PATH . '/Function');
		//项目根目录
		define('ROOT_PATH', dirname(WPHP_PATH));
		//临时目录
		define('TEMP_PATH',ROOT_PATH . '/Temp');
		//日志目录
		define('LOG_PATH',TEMP_PATH . '/Log');
		// 框架扩展目录
		define('EXTENDS_PATH',WPHP_PATH . '/Extends');
		//框架扩展 第三方类库目录
		define('ORG_PATH',EXTENDS_PATH . '/Org');
		//框架扩展 工具类目录
		define('TOOL_PATH',EXTENDS_PATH . '/Tool');
		//公共目录
		define('COMMON_PATH',ROOT_PATH . '/Common');
		//公共配置目录
		define('COMMON_CONFIG_PATH',COMMON_PATH . '/Config');
		//公共库文件
		define('COMMON_LIB_PATH',COMMON_PATH . '/Lib');
		//公共模型目录
		define('COMMON_MODEL_PATH',COMMON_PATH . '/Model');
		//应用目录
		define('APP_PATH', ROOT_PATH . '/' . APP_NAME);
		define('APP_CONFIG_PATH', APP_PATH . '/Config');
		define('APP_CONTROLLER_PATH', APP_PATH . '/Controller');
		define('APP_TPL_PATH', APP_PATH . '/Tpl');
		define('APP_PUBLIC_PATH', APP_TPL_PATH . '/Public');
		//框架版本
		define('WPHP_VERSION','V1.0');
		//是否是post提交
		define('IS_POST',(isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == 'POST') ? true :false);
		// 是否为AJAX提交
		define('IS_AJAX',(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') ? true : false);

	}
}

WPHP::run();
?>