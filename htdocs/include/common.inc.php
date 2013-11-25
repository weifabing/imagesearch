<?php
/**
 * 启动文件
 * 系统启动文件，负责初始化数据等资源
 *
 * $id$
 * $Author: fabing.wei $
 * $Revision: 18408 $ 
 * $LastChangedDate: 2012-06-12 14:29:36 +0800 (周二, 12 六月 2012) $ 
 *
 */

//统计运行时间
set_magic_quotes_runtime(0);
$mtime = explode(' ', microtime());
$discuz_starttime = $mtime[1] + $mtime[0];

//防止非法访问
!defined('START') && die('Forbidden');
//初始化数据库
require_once DIR_ROOT . './include/db_mysql.class.php';
$db = new dbstuff;
$db->connect(DBHOST, DBUSER, DBPW, DBNAME, 0, false, DBCHARSET);
$db->select_db(DBNAME);

//载入函数库
require_once DIR_ROOT . './include/functions.php';

//脚本运行结束时间
$mtime = explode(' ', microtime());
$discuz_endtime = $mtime[1] + $mtime[0];

?>