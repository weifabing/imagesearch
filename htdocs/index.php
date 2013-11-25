<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>钻石小鸟照片管理系统</title>
<link rel="stylesheet" type="text/css" href="./static/css/reset.css" />
<link rel="stylesheet" type="text/css" href="./static/css/main.css?v=1" />
<script type="text/javascript" src="./static/js/jquery.min.js" ></script> 
</head>
<body>
<div id="wraper">
	<!--/*头部*/-->
	<div id="header"></div>
	<!--/*搜索设置*/-->
	<div id="search">
		<input type="hidden" id="page" value="1">
		<div class="title">钻石小鸟图片搜索</div>
		<label id="lab_keyword" for="keyword"></label>
		<input type="text" name="keyword" id="keyword">
		<input type="button" value="搜索" id="star_search">
	</div>
	<!--/*搜索结果*/-->
	<div id="search_result"></div>
	<!--/*页脚*/-->
	<div id="footer"></div>
</div>
<script type="text/javascript">
<!--
$(document).ready( function() {
	$('#keyword').focus();
	$('#keyword').keydown(function(event){
		if(event.keyCode==13)
		{
			$('#page').val('1');
			star_search();
		}
		if(event.keyCode==46)
		{
			$('#keyword').val('');
		}
	})
	$('#star_search').click(function(){
		$('#page').val('1');
		star_search();
	})
});

function star_search()
{
	var page = $('#page').val();
	var keyword = $('#keyword').val();
	$.ajax({
		url: "search.php",
		data: {"keyword":keyword,"page":page},
		dataType: "json",
		cache: false,
		success: function(json){
			if(json.error==0){
				$('#search_result').html(json.html);
				$('#header').slideUp();
			}else{
				alert('发生错误');
			}
		}
	})
}

function go_page(keyword,page)
{
	$('#page').val(page);
	star_search();
}
//-->
</script>
</body>
</html>
