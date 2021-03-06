<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title><?=$message;?>-VtpErrorCatched</title>
<base href="/static/" target="_self">
<link href="css/bootstrap.min.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" type="text/css" href="js/prism/prism-night.css"/>
<style type="text/css">
/*body{max-width:900px; padding:0 10px 10px; margin:0 auto; background-color:#FFF;}*/
#header{margin:10px 0; padding:10px 0; border-bottom: 1px solid #DDD; }
#info{list-style-type:none;}
#info li{line-height:2;}
.msg{margin:20px 0;}


/*书签样式 20190616*/
.mark{
    padding: 10px 10px 10px 20px;
    color: #777;
    border-left: 4px solid #ddd;
    background-color: #f5f5f5;
}

/**/
.code{
	/*
	background-color: #DDD;
    border: 1px solid #CCC;
    margin: 0px 5px;
    padding: 1px 6px;
*/
    background-color: #f9fafa;
    border: 1px solid #ded9d9;
    border-radius: 3px;
    margin: 0px 5px;
    padding: 1px 6px;
    color:#525252;
}

.danger{
	color: #d9534f;
    background-color: #fdf7f7;
    border-color: #d9534f;
}

.warning {
    color: #856404;
    background-color: #fff3cd;
    border-color: #ffeeba;
}

.success {
    color: #155724;
    background-color: #d4edda;
    border-color: #c3e6cb;
}

.f_r{float:right;}
</style>
</head>
<body class="container">
<!--  header -->
<div id="header">
	<span class="f_r"><?=date('Y-m-d H:i:s');?></span>
	<span>Vtp调试模式：错误/异常页面输出</span>
</div>

<!--  main message -->
<div class="msg h2"><?=$message;?></div>


<!--  info message -->
<ul id="info" class="mark danger">
	<li>
		<b>错误类型: </b> <?=$etype;?>
	</li>
	<li>
		<b>所在行数: </b> 第 <span class="code"><?=$line;?></span> 行
	</li>
	<li>
		<b>所在文件: </b> <span class="code"><?=$file;?></span><br/>
	</li>
	<li>
		<b>当前页面: </b> 控制器：<span class="code"><?=CTL;?></span> ，执行器：<span class="code"><?=ACT;?></span>
	</li>

</ul>
<pre class="line-numbers" data-start='<?=$line0;?>' data-line="<?=$line;?>" style="white-space:pre-wrap" >
<code class="language-php"><?php foreach($lines as $li){echo htmlentities($li);}?></code>
</pre>

<br/>
<h5>栈调用信息</h5>
<pre class="line-numbers" style="white-space:pre-wrap" >
<code class="language-php"><?=$stack;?></code>
</pre>

<br/>
<h5>页面全量信息</h5>
<pre class="line-numbers" style="white-space:pre-wrap" >
<code class="language-php"><?php var_dump($GLOBALS);?></code>
</pre>

<script type="text/javascript" src="js/prism/prism-Highlight-Numbers.js"></script>
<script type="text/javascript">


</script>
</body>
</html>