<?php
/**
 * 系统配置文件
 *
 * $id$
 * $Author: fabing.wei $
 * $Revision: 20136 $ 
 * $LastChangedDate: 2012-11-26 14:40:17 +0800 (周一, 26 十一月 2012) $ 
 *
 */
!defined('START') && die('Forbidden');

define('DIR_ROOT', dirname(__FILE__).'/');

//数据库链接参数设置
define('DBHOST',	'localhost');	//数据库地址
define('DBUSER',	'admin');		//用户名
define('DBPW',		'12qwaszx');		//密码
define('DBNAME',	'images');		//数据库名称
define('DBCHARSET', 'utf8');		//数据库编码


define('STOR_ROOT', '/pic/images/');				//存储的基本路径
define('STOR_URL',  'http://pic.51diamond.com/');		//通过网络获取存储的基本路径

?>
