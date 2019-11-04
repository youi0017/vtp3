<!DOCTYPE html>
<html lang="zh">
<head>
<meta charset="utf-8">
<title>欢迎来到VTP-PHP框架</title>
<link type="text/css" href="/static/css/bootstrap.min.css" rel="stylesheet" />
<script type="text/javascript" src=""></script>
<style type="text/css">
html{background-color:#EFEFF4;}
body{margin:10px auto; padding:0 15px 15px; max-width:720px; background-color:#FFF;}

p{margin:20px 0;text-indent:2em;}
.qrcode{text-align:right; }
.qrcode img{width:150px;}
</style>
</head>
<body>

<?php include '_header.tpl';?>

<h2>欢迎来到VTP-PHP框架</h2>
<hr/>
<p>
VTP-PHP框架就是通过提供一个开发Web程序的基本架构，PHP开发框架把PHPWeb程序开发摆到了流水线上。换句话说，PHP开发框架有助于促进快速软件开发（RAD），这节约了你的时间，有助于创建更为稳定的程序，并减少开发者的重复编写代码的劳动。这些框架还通过确保正确的数据库操作以及只在表现层编程的方式帮助初学者创建稳定的程序。PHP开发框架使得你可以花更多的时间去创造真正的Web程序，而不是编写重复性的代码。
</p>

<p>
VTP/PHP是一个简单快速的PHP VC框架，由 <?=$author;?> 先生于 <?=$creatAt;?> 创建，经过大量的应用，是一套非常实用的快速开发工具。<br/>
</p>
<p>
# 其特点是：安全、轻量、灵活。
</p>

<footer>
	<div class="qrcode">
		<?php include '_urlQrcode.tpl';?>
	</div>
</footer>

<script src="/static/js/axios.js"></script>

</body>
</html>
