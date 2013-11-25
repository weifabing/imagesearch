<?php
/**
 * 搜索图片入口
 * 执行图片搜索任务,通过json 方式返回图片数据
 * $id$
 * $Author: fabing.wei $
 * $Revision: 19413 $ 
 * $LastChangedDate: 2012-09-18 15:57:40 +0800 (周二, 18 九月 2012) $ 
 *
 */
define('START', TRUE);
include_once('config.ini.php');
include_once(DIR_ROOT . './include/common.inc.php');

//---初始化完成---
$data = array();
$data['error']	= '0';
$data['msg']	= 'ok';

//处理关键字
$keyword = '';
if(isset($_GET['keyword']) && strlen(trim($_GET['keyword']))>0)
{
	//防止 sql 注入
	if(get_magic_quotes_gpc())
	{
		$keyword = addslashes(trim($_GET['keyword']));
	}
	else
	{
		$keyword = trim($_GET['keyword']);
	}
}

$page = isset($_REQUEST['page'])?intval($_REQUEST['page']):1;

//整理数据输出
$html = '<ul class="img_model">';

$where	= " name like '%{$keyword}%'";
$keys	= preg_split("/[\s,]+/i", $keyword);
if(count($keys)>1)
{
	foreach($keys as $v)
	{
		$where .= " or name like '%{$v}%' ";
	}
}

$orderby = 'order by cdate desc ';
$sql = "select count(1) from images where $where limit 1";
$query = $db->query($sql);
$item = $db->fetch_row($query);
$count = $item[0];
$showinfo  = '<div class="showinfo">共找到<em>'.$count.'</em>张照片</div>';

//------------分页计算------------
$page_size  = 15;	//每页大小
$page_count = ceil($count/$page_size);
$page = ($page>$page_count)?$page_count:$page;
$page = ($page<1)?1:$page;

$page_html = '<div class="page">';
//上一页代码
if($page>1)
{
    $page_html .= '<a class="n" href="javascript:go_page(\''.$keyword.'\',\''.($page-1).'\')">上一页</a>';
}
//中间导航
for($i=1; $i<=$page_count && $i<=20; $i++)
{
	if($page==$i)
	{
		$page_html .= '<strong>'.$i.'</strong>';
	}
	else
	{
		$page_html .= '<a href="javascript:go_page(\''.$keyword.'\',\''.$i.'\')">'.$i.'</a>';
	}
}
//下一页代码
if($page<$page_count)
{
	$page_html .= '<a class="n" href="javascript:go_page(\''.$keyword.'\',\''.($page+1).'\')">下一页</a>';
}
$page_html .= '</div>';
if($page_count<2)
{
	$page_html ='';
}
//------------分页结束 2012/5/21------------
$page_start = ($page-1)*$page_size;
$sql = "select * from images where $where $orderby limit {$page_start},{$page_size}";
$query = $db->query($sql);
while($image = $db->fetch_array($query))
{
	$html .= make_ul($image, $keyword);
}
$html .= '</ul>';

$data['html'] = $showinfo. $page_html . $html. $page_html;

//输出json格式数据
die(json_encode($data));

//格式化数据
function make_ul($image=null, $keyword='')
{
	$html  = '<li>';
	$url_name = $image['name'];
	if($keyword!='')
	{	
		$url_name	= preg_replace("/$keyword/i", '<em>'.$keyword.'</em>', $image['name']);
		$keys		= preg_split("/[\s,]+/i", $keyword);
		if(count($keys)>1)
		{
			foreach($keys as $v)
			{
				$url_name = preg_replace("/$v/i", '<em>'.$v.'</em>', $url_name);
			}
		}
	}
	$down_url = "down.php?uuid={$image['uuid']}";
	$thum_file = STOR_URL . str_replace($image['name'], 'thum/'.rawurlencode($image['name']), $image['url']);
	$thum_file = preg_replace('/.tif$/i','.jpg',$thum_file);
	$html .= '<a href="'.$down_url.'" target="_blank"><img src="' . $thum_file . '" /></a>';
	$html .= '<p>';	
	$html .= '<h3><a href="'.$down_url.'" target="_blank">'.$url_name.'</a></h3>';
	$html .= '<span>创建时间：' . $image['cdate'].'</span>';
	if($image['filesize'] < 1000000)
	{
		$file_size = intval($image['filesize']/1000) . ' K';
	}
	else
	{
		$file_size = round($image['filesize']/1000000, 2) . ' M';
	}
	$html .= '<span><br/> 文件大小: ' . $file_size .'</span>';
	$html .= '<span class="i"> '.$image['width'] . 'X' .$image['height'].'</span>';
	$html .= '<br/><span class="u">'.$image['url'].'</span>';
	$html .= '</p>';	
	$html .= '</li>';
	return $html;
}

?>