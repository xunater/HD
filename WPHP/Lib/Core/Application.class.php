<?php
/**
 * 应用类
 */
class Application{
	public static function run(){
		self::_init();
		set_error_handler(array(__CLASS__,'error'));
		register_shutdown_function(array(__CLASS__,'fatal_error'));
		self::_user_import();
		self::_set_url();
		spl_autoload_register(array(__CLASS__,'_autoload'));
		self::_create_demo();
		self::_app_run();
	}
	/**
	 * [_init 初始化应用类]
	 * 载入配置项，设置默认时区，并开启session
	 * @return [type] [description]
	 */
	private static function _init(){
		//载入系统配置项
		C(require_once CONFIG_PATH . '/config.php');
		//初始化空配制项字符串
		$str = <<<str
<?php
return array(
	//配置项=>值
);
?>
str;
		//载入公共配置项
		$commonPath = COMMON_CONFIG_PATH . '/config.php';
		is_file($commonPath) OR (file_put_contents($commonPath, $str) && chmod($commonPath, 0666));
		C(require_once $commonPath);
		//载入用户配置项
		$userPath = APP_CONFIG_PATH . '/config.php';
		is_file($userPath) OR (file_put_contents($userPath, $str) && chmod($userPath, 0666));
		C(require_once $userPath);
		//设置默认时区
		date_default_timezone_set(C('DEFAULT_TIME_ZONE'));
		// 开启session
		C('SESSION_AUTO_START') && session_start();
	}
	/**
	 * [_error 错误处理函数]
	 * @param  [int] $errno [description]
	 * @param  [string] $error [description]
	 * @param  [string] $file  [description]
	 * @param  [int] $line  [description]
	 * @return [type]        [description]
	 */
	public static function error($errno,$error,$file,$line){
		switch ($errno) {
			case E_ERROR:
			case E_PARSE;
			case E_USER_ERROR:
			case E_CORE_ERROR:
			case E_COMPILE_ERROR:
				$error = $error . "<br/>[FILE:". $file . "] [LINE:{$line}]";
				halt($error);
				break;
			case E_USER_WARNING:
			case E_USER_NOTICE:
			case E_STRICT:
			default:
				if(DEBUG){
					include TPL_PATH . '/notice.html';
				}
				break;
		}
	}
	/**
	 * [fatal_error 致命错误处理]
	 * @return [type] [description]
	 */
	public static function fatal_error(){
		if($e = error_get_last()){
			// p($e);
			self::error($e['type'], $e['message'], $e['file'], $e['line']);
		}
	}
	/**
	 * [_user_import 用户自定义扩展文件的自动载入(根据配置文件)]
	 * @return [type] [description]
	 */
	private static function _user_import(){
		$fileArr = C('USER_IMPORT_FILE');
		foreach ($fileArr as $v) {
			$path = COMMON_LIB_PATH . '/' .$v;
			is_file($path) OR halt($path . '库文件不存在'); 
			require_once $path;
		}
	}
	/**
	 * [_set_url 设置外部路径]
	 */
	private static function _set_url(){
		// p($_SERVER);
		$path = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'];
		define('__ROOT__', dirname($path));
		define('__APP__', __ROOT__ . '/' . APP_NAME);
		define('__TPL__', __APP__ . '/Tpl');
		define('__PUBLIC__', __TPL__ . '/Public');
	}
	/**
	 * [_autoload 自动载入]
	 * @param  [type] $className [description]
	 * @return [type]            [description]
	 */
	private static function _autoload($className){
		// echo APP_CONTROLLER_PATH . '/' . $className .'.class.php';
		switch (true) {
			case strlen($className) > 10 && substr($className, -10) == 'Controller':
				$path = APP_CONTROLLER_PATH . '/' . $className .'.class.php';
				if(!is_file($path)){
					$emptyPath = APP_CONTROLLER_PATH . '/EmptyController.class.php';
					if(is_file($emptyPath)){
						require_once $emptyPath; return;
					}else{
						halt($path . '控制器未找到');
					}
				}else{
					require_once $path; return;
				}
				break;
			case strlen($className) > 5 && substr($className, -5) == 'Model':
				$path = COMMON_MODEL_PATH . '/' . $className . '.class.php';
				is_file($path) OR halt($path . '未找到');
				include_once $path; return;
				break;
			default:
				$path = TOOL_PATH . '/' . $className .'.class.php';
				is_file($path) OR halt($path . '类未找到');
				require_once $path; return;
				break;
		}
		/*
		if(strlen($className) > 10 && substr($className, -10) == 'Controller'){
			$path = APP_CONTROLLER_PATH . '/' . $className .'.class.php';
			if(!is_file($path)){
				$emptyPath = APP_CONTROLLER_PATH . '/EmptyController.class.php';
				if(is_file($emptyPath)){
					require_once $emptyPath; return;
				}else{
					halt($path . '控制器未找到');
				}
			}else{
				require_once $path; return;
			}
		}else{
			$path = TOOL_PATH . '/' . $className .'.class.php';
			is_file($path) OR halt($path . '类未找到');
			require_once $path; return;
		}
		*/
	}
	/**
	 * [_create_demo 创建demo]
	 * @return [type] [description]
	 */
	private static function _create_demo(){
		$path = APP_CONTROLLER_PATH . '/IndexController.class.php';
		$str = <<<str
<?php
class IndexController extends Controller{
	public function index(){
		p('<h1>:)</h1><p>欢迎使用WPHP框架！</p>');
	}
}
?>
str;
		is_file($path) OR file_put_contents($path, $str) && chmod($path, 0666);

	}
	/**
	 * [_app_run 根据url参数,运行相应的控制器类的方法]
	 * @return [type] [description]
	 */
	private static function _app_run(){
		$c = isset($_GET[C('CONTROLLER_VAR')]) ? $_GET[C('CONTROLLER_VAR')] : 'Index';
		$a = isset($_GET[C('ACTION_VAR')]) ? $_GET[C('ACTION_VAR')] : 'index';
		define('CONTROLLER',$c);
		define('ACTION',$a);
		$c .= 'Controller';
		if(class_exists($c)){
			$obj = new $c();
			if(method_exists($obj, $a)){
				$obj->$a();
			}else{
				if(method_exists($obj, '__empty')){
					$obj->__empty();
				}else{
					halt($a . '方法未定义');
				}
			}
		}else{
			$obj = new EmptyController();
			// $obj->index();
		}
		
	}
}
 ?>