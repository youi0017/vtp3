<!DOCTYPE html>
<html lang="zh">
<head>
<meta charset="utf-8">
<title><?=$title;?></title>
<meta name="viewport" content="width=device-width,minimum-scale=1,initial-scale=1">
<link type="text/css" href="" rel="stylesheet" />
<script type="text/javascript" src=""></script>
<style type="text/css">
html{background-color:#EFEFF4;}
body{margin:10px auto; padding:0 15px 15px; max-width:720px; background-color:#FFF;}


img{display:block; max-width:200px; max-height:200px; margin:20px;}

</style>
</head>
<body>
<header>
<?php include '_header.tpl';?>
</header>

<nav>

</nav>

<section>
<button id="btn" type="button">显示或隐藏我的图片</button>
<br/>
<img id="pic" src="" alt="此图正常，将解析为画出的一个图片">

</section>

<footer>
<?php include '_urlQrcode.tpl';?>
</footer>

	
<script type="text/javascript">

btn.onclick=function()
{
	var src = pic.getAttribute("src");
	//console.log(src);
	pic.setAttribute("src", (src ? "" : "<?=$src;?>"));
}


</script>
</body>
</html>
