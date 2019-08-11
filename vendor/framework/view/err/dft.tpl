<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8" />
<title>错误: <?php echo $code.'-'.$msg;?></title>
<base href="/static/" target="_self">
<meta name="viewport" content="initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
<link href="css/bootstrap.min.css" rel="stylesheet" type="text/css" />
<style type="text/css">
html, body{height:100%;}
body{background-color:#F0F0F0;}
#content_box_outer{position:absolute; padding:0 20px 0 200px; margin-left:-350px; left:50%; top:25%; background:#FFFFFD url("pic/<?=$img;?>.jpg") 30px center no-repeat; border:1px #CCC solid; border-radius:3px; box-shadow:2px 2px 8px #999;}
#content_box_inner{ display:table-cell; min-width:500px; height:300px; vertical-align:middle;}/* text-align:center;*/
h2{font-size:50px; line-height:70px; letter-spacing:5px; }


</style>
</head>
<body>
<div id='content_box_outer'>
	<div id="content_box_inner">
		<h4 id='tit'><?=$msg;?></h4>
		<h2 id='code' class="color_red"><?=$code;?></h2>
	    <h5><?=$adv;?></h5>
   </div>
</div>

<script type='text/javascript'>
(function(){
	return;
	//put content_box_outer valign
	var o = document.getElementById('content_box_outer');
	var h = window.innerHeight ? window.innerHeight : document.documentElement.clientHeight;
	var str=o.style.height;
	var n1 = str.indexOf('px');
	var n = str.substr(0, n1);
	var r = (h-n)/2>0 ? (h-n)/2 : '25%';
	o.style.top=parseInt(r)+'px';
	
})()
</script>
</body>
</html>