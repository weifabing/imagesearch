<?php
/**
 * 下载文件
 * 图片文件默认使用浏览器打开现在改用php推送的方式
 * 另外可以设置不同的域名，通过服务器设置来实现下载的方式
 * 
 * $id$
 * $Author: fabing.wei $
 * $Revision: 18182 $ 
 * $LastChangedDate: 2012-05-24 17:47:30 +0800 (周四, 24 五月 2012) $ 
 *
 */
define('START', TRUE);
include_once('config.ini.php');
include_once(DIR_ROOT . './include/common.inc.php');
//---初始化完成---

//开始处理
$uuid	= isset($_GET['uuid'])?$_GET['uuid']:'';
$sql	= "select * from images where uuid='{$uuid}'";
$img	= $db->fetch_first($sql);
if(empty($img))
{
	die('文件不存在');
}

$filename = STOR_ROOT.$img['url'];
if(file_exists($filename))
{
	//更新下载次数
	$sql = "update images set click=click+1 where uuid='{$uuid}'";
	$db->query($sql);
	
	//推送文件
	header("Content-type: application/octet-stream");
	header("Content-Length: ".filesize($filename));
	header("Content-Disposition: attachment; filename={$img['name']}");
	readfile($filename);
}
else
{
	die('文件已删除');
}

//方式二
//$fp = fopen($filename, 'rb');  
//fpassthru($fp);  
//fclose($fp);  
?>