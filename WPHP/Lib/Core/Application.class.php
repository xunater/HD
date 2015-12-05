<?php
/**
 * 应用类
 */
class Application{
	public static function run(){
		self::_init();
		self::_set_url();
		spl_autoload_register(array(__CLASS__,'_autoload'));
		self::_create_demo();
		self::_app_run();
	}
	private static function _autoload($className){
		// echo APP_CONTROLLER_PATH . '/' . $className .'.class.php';
		require_once APP_CONTROLLER_PATH . '/' . $className .'.class.php';
	}
	private static function _app_run(){
		$c = isset($_GET[C('CONTROLLER_VAR')]) ? $_GET[C('CONTROLLER_VAR')] : 'Index';
		$a = isset($_GET[C('ACTION_VAR')]) ? $_GET[C('ACTION_VAR')] : 'index';

		$c .= 'Controller';
		$obj = new $c();
		$obj->$a();
	}
	/**
	 * [_init 初始化框架]
	 * 载入配置项，设置默认时区，并开启session
	 * @return [type] [description]
	 */
	private static function _init(){
		//载入系统配置项
		C(require_once CONFIG_PATH . '/config.php');
		//TODO 载入公共配置项
		//
		//载入用户配置项
		$str = <<<str
<?php
return array(
	//配置项=>值
);
?>
str;
		$path = APP_CONFIG_PATH . '/config.php';
		is_file($path) OR (file_put_contents($path, $str) && chmod($path, 0666));
		C(require_once $path);
		//设置默认时区
		date_default_timezone_set(C('DEFAULT_TIME_ZONE'));
		// 开启session
		C('SESSION_AUTO_START') && session_start();
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
}
 ?>