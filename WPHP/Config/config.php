<?php 
return array(
	//验证码长度
	"CODE_LEN"					=>	4,
	//默认时区
	"DEFAULT_TIME_ZONE"			=>	'PRC',
	//是否默认开启session
	"SESSION_AUTO_START"		=>	true,
	//默认控制器参数
	"CONTROLLER_VAR"			=>	'c',
	//默认动作参数
	"ACTION_VAR"					=>	'a',
	//模板文件默认后缀名
	"TPL_SUFFIX"					=>	'.html',
	//是否写入日志
	"SAVE_LOG"					=>	true,
	//生产环境下错误跳转页面
	"ERROR_URL"					=>	'',
	//生产环境下错误提示信息
	"ERROR_MSG"					=>	'服务器君迷路了...',
	//Common/Lib目录下文件的自动载入
	"USER_IMPORT_FILE"			=>	array(),
	/**********************数据库配置***********************/
	"DB_CHARSET"					=>	'utf8',
	"DB_HOST"						=>	'127.0.0.1',
	"DB_PORT"						=>	3306,
	"DB_USER"						=>	'root',
	"DB_PWD"						=>	'',
	"DB_DATABASE"				    =>	'',
	"DB_PREFIX"					    =>	'wb_',
    /*********************Smarty相关**********************/
    "SMARTY_ON"                     =>  true,
    "SMARTY_LEFT_DELIMITER"         =>  "{sw ",
    "SMARTY_RIGHT_DELIMITER"        =>  "}",
    "SMARTY_CACHE_LIFETIME"         =>  3,
    "SMARTY_CACHE_ON"               =>  true
);
