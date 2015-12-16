<?php
class SmartyView extends Smarty{
    public static $smarty=null;
    public function __construct(){
        if(is_null(self::$smarty)){
            $smarty = new Smarty();
            $smarty->template_dir = C('SMARTY_TEMPLATE_DIR');
            $smarty->compile_dir = C('SMARTY_COMPILE_DIR');
            $smarty->left_delimiter = C('SMARTY_LEFT_DELIMITER');
            $smarty->right_delimiter = C('SMARTY_RIGHT_DELIMITER');
            $smarty->cache_dir = C('SMARTY_CACHE_DIR');
            $smarty->cache_lifetime = C('SMARTY_CACHE_LIEFTIME');
            self::$smarty = $smarty;
        }

    }

    /*public function display($tpl){
        return self::$smarty->display($tpl);
    }

    public function assign($var,$value){
        return self::$smarty->assign($var,$value);
    }*/
}
?>