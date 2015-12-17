<?php
class SmartyView{
    public static $smarty=null;//静态属性，用于存放smarty对象
    public function __construct(){
        if(is_null(self::$smarty)){
            $smarty = new Smarty();
            $smarty->template_dir = APP_TPL_PATH . '/' . CONTROLLER . '/';
            $smarty->compile_dir = APP_COMPILE_PATH;
            $smarty->left_delimiter = C('SMARTY_LEFT_DELIMITER');
            $smarty->right_delimiter = C('SMARTY_RIGHT_DELIMITER');
            $smarty->cache_dir = APP_CACHE_PATH;
            $smarty->caching = C('SMARTY_CACHE_ON');
            $smarty->cache_lifetime = C('SMARTY_CACHE_LIFETIME');
            self::$smarty = $smarty;
        }
    }
    /**
     * [display 显示]
     * @param  [string] $tpl [模板文件]
     * @return null
     */
    protected function display($tpl){
        return self::$smarty->display($tpl,$_SERVER['REQUEST_URI']);
    }
    /**
     * [assign 分配变量]
     * @param  [string] $var   [变量]
     * @param  [type] $value [值]
     * @return null
     */
    protected function assign($var,$value=null){
        return self::$smarty->assign($var,$value);
    }
    /**
     * [is_cached 模板是否已缓存]
     * @param  [string]  $tpl [模板文件名]
     * @return boolean
     */
    protected function is_cached($tpl=null){
        C('SMARTY_ON') OR halt('请先开启smarty');
        $tpl = $this->get_tpl($tpl);
        return self::$smarty->isCached($tpl,$_SERVER['REQUEST_URI']);
    }
}
?>