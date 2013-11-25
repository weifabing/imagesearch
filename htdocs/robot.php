<?php
/**
 * 文件爬虫
 * 文件爬虫可以检索文件到数据库,主要收集文件的名称,大小,创建时间
 *
 * $id$
 * $Author: fabing.wei $
 * $Revision: 19613 $
 * $LastChangedDate: 2012-10-08 14:30:12 +0800 (周一, 08 十月 2012) $
 *
 */
if(isset($_SERVER['REMOTE_ADDR']))
{
	header("location:index.php");
}
define('START', TRUE);
include_once('config.ini.php');
include_once(DIR_ROOT . './include/common.inc.php');
//---初始化完成---

//开始处理
$target_dir = isset($argv[1])?$argv[1]:date('Y/m/d',strtotime("-1 day"));	//目录目录
$update = isset($argv[2])?$argv[2]:'';				//更新索引
define('UPDATE', $update);

if(!is_dir(STOR_ROOT . $target_dir))
{
	die(STOR_ROOT . $target_dir ."----not exits\n");
}
$scan_dir = STOR_ROOT . $target_dir;

getfiles($scan_dir);

//遍历文件夹
function getfiles($path)
{
	if(!is_dir($path)) return;
	$handle  = opendir($path);
	while( false !== ($file = readdir($handle)))
	{
		//添加自动转换程序
		if(!is_utf8($file))
		{
			$old_file = $file;
			$file = auto_charset($file, 'gbk', 'utf-8');
			$falg = @rename($path.'/'.$old_file, $path.'/'.$file);
			if(!$falg)
			{
				continue;
			}
		}
		if($file != '.'  &&  $file!='..' && $file!='thum')
		{
			$path2= $path.'/'.$file;
			if(is_dir($path2))
			{
				//echo 'DIR:--'.$file."\n";
				getfiles($path2);
			}
			else
			{
				$file_path = realpath($path2);
				//echo 'FILE:--'.$file_path."\n";
				echo date('Ymd H:i:s'),'--',$file_path,"\t\t";
				save_file_db($file_path, UPDATE);
			}
		}
	}
}

//采集一个文件到数据库
function save_file_db($file='', $update='')
{
	if(!file_exists($file))
	{
		echo '--NOT FILE',"\n";
		return 'NOT FILE';
	}
	if(!in_array(strtolower(strrchr($file, ".")), array('.jpg','.png','.gif','.png','.tif')))
	{
		echo '--NOT IMAGE',"\n";
		return 'NOT IMAGE';
	}
	global $db;
	//索引文件保存以下属性
	$path_parts = pathinfo(realpath($file));	//解析文件路径
	$thum_dir = $path_parts['dirname'].'/thum';
	if(!file_exists($thumdir))
	{
		@mkdir($thum_dir, 0755);
	}
	if(!is_dir($thum_dir))
	{
		die('目录创建失败,请检查权限设置'.$thum_dir);
	}
	$name		= $path_parts['basename'];	//文件名称
	if(!preg_match('/^[a-zA-z0-9_]/',$name))
	{
		echo '--NOT NORMAL FILE',"\n";
		return 'NOT IMAGE';
	}
	$thum_name	= $thum_dir.'/'.$name;		//缩略图名称
	$thum_name = preg_replace('/.tif$/i','.jpg',$thum_name);
	$url		= str_replace(STOR_ROOT, '', $file);			//文件路径-只保留采集路径
	if(DIRECTORY_SEPARATOR == '\\')
	{
		$url	= str_replace('\\', '/', $url);
	}
	$cdate		= date("Y-m-d H:i:s", filectime($file));		//文件创建时间
	$filesize	= filesize($file);			//文件大小
	//图片压缩处理--star 2012/5/23

	$towidth	= '400';
	$toheight	= '300';
	if(!file_exists($thum_name))
	{
		$imgObject	= new Imagick("{$file}");
		$height		= $imgObject->getImageHeight();	//图片高度
		$width		= $imgObject->getImageWidth();	//图片宽度

		$imgObject->stripImage();					//删除照相机信息
		$imgObject->setImageCompressionQuality(80);	//设置jpg压缩质量，1 - 100
		@$imgObject->resizeImage($towidth, $toheight, Imagick::FILTER_LANCZOS, 1, true);
		$imgObject->writeImage($thum_name);
		$imgObject->destroy();
	}

	//end
	$click		= 0;						//文件下载次数
	$grade		= 0;						//文件评分
	$memo		= '';						//文件备注
	$tag		= '';						//文件标签
	$file_md5	= md5_file($file);			//文件md5
	$uuid		= "replace(uuid(),'-','')";	// uuid

	//插入数据库
	$sql  = "insert into images set ";
	$txt = "insert ";
	if($update=='update')
	{
		$sql  = "replace into images set ";
		$txt = "update";
	}
	$sql .= "uuid={$uuid},name='{$name}',url='{$url}',cdate='{$cdate}',filesize='{$filesize}'";
	$sql .= ",height='{$height}',width='{$width}'";
	$sql .= ",file_md5='{$file_md5}',click='{$click}', grade='{$grade}', memo='{$memo}', tag='{$tag}'";
	echo '--',$txt,'--ok',"\n";
	return $db->query($sql);
}

//生成报告
$today = date('Y-m-d');
$query = $db->query("select * from images where cdate>'{$today}'");
echo '今日采集：', $db->num_rows($query);

?>
