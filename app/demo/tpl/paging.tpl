<!DOCTYPE html>
<html lang="zh">
<head>
<meta charset="utf-8">
<title>欢迎来到VTP-PHP框架</title>
<meta name="viewport" content="initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
<base href="/<?=W3;?>/" target="_self">
<link type="text/css" href="/static/css/bootstrap.min.css" rel="stylesheet" />
<script type="text/javascript" src=""></script>
<style type="text/css">
body{margin:15px auto;width:100%;max-width:960px;}
p{margin:20px 0;text-indent:2em;}

ul{padding:0;list-style-type:none;}
</style>
</head>
<body>
<ul>
<?php foreach( $rows as $obj ){ ?>
	<li><?=$obj->id;?>. <?=$obj->tit;?> <?=$obj->y.'-'.$obj->m.'-'.$obj->d;?></li> 
<?php  } ?>
<ul>

<br/>
<div>
<?=$paging->render();?>
</div>

</body>
</html>
