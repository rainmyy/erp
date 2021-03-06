<?php
class skin extends gskin{
// 
function logo(){
	return '<a href="/s.php"><img src="/public/images/logomis.png" width="189" height="30"></a>';
}

function nav($myself, $today){
return <<<EOF
<ul id="menu" style="display:none">
  <li><a href="/s.php">首页</a></li>
  <li></li>
  <li>产品
		<ul>
      <li><a href="/s.php?module=product&action=add">新建</a></li>
      <li><a href="/s.php?module=product&action=find">查找</a></li>
      <li><a href="/s.php?module=product&action=list">列表</a></li>
			<li></li>
      <li><a href="/s.php?module=comment">评论</a></li>
		</ul>
	</li>
  <li></li>
  <li>订单
		<ul>
      <li><a href="/s.php?module=order&action=find">查找</a></li>
      <li><a href="/s.php?module=order&action=list">列表</a></li>
		</ul>
	</li>
  <li></li>
  <li>会员
		<ul>
      <li><a href="/s.php?module=member&action=find">查找</a></li>
      <li><a href="/s.php?module=member&action=list">列表</a></li>
		</ul>
	</li>
  <li></li>
	<li>新闻
		<ul>
			<li><a href="/s.php?module=news&action=add">新建</a></li>
			<li><a href="/s.php?module=news&action=find">查找</a></li>
			<li><a href="/s.php?module=news&action=list">列表</a></li>
		</ul>
	</li>
  <li></li>
  <li>网站管理
		<ul>
			<li><a href="/s.php?module=slideshow">幻灯展示</a></li>
			<li></li>
			<li><a href="/s.php?module=bulletin">滚动公告</a></li>
			<li></li>
			<li><a href="/s.php?module=other&action=update&m=about">关于我们</a></li>
			<li><a href="/s.php?module=other&action=update&m=contact">联系我们</a></li>
			<li><a href="/s.php?module=other&action=update&m=official">官方经销商</a></li>
			<li><a href="/s.php?module=other&action=update&m=privacy">隐私声明</a></li>
			<li><a href="/s.php?module=other&action=update&m=policy">退货政策</a></li>
			<li><a href="/s.php?module=other&action=update&m=shipping">发货与递送</a></li>
			<li><a href="/s.php?module=other&action=update&m=terms">条款与条件</a></li>
		</ul>
	</li>
  <li></li>
  <li>普通
		<ul>
			<li><a href="/s.php?module=category&action=list">产品类别</a></li>
			<li><a href="/s.php?module=brand&action=list">品牌</a></li>
			<li>供应商
				<ul>
					<li><a href="/s.php?module=supplier&action=find">查找</a></li>
					<li><a href="/s.php?module=supplier&action=list">列表</a></li>
					<li><a href="/s.php?module=supplier&action=add">新建</a></li>
				</ul>
			</li>
			</ul>
	</li>
  <li></li>
  <li>{$myself}
    <ul>
      <li><a href="/s.php?module=user&action=view">个人面板</a></li>
      <li><a href="/s.php?module=user&action=update">编辑资料</a></li>
      <li><a href="/s.php?module=user&action=password">修改密码</a></li>
      <li><a href="/s.php?module=user&action=setting">帐户设置</a></li>
		  <li><a href="/s.php?module=login&action=logout">退出</a></li>
		</ul>
	</li>
  <li></li>
	<li class="disabled">{$today}</li>
</ul>
EOF;
}
	// 
	function blink($run_time){
		return $run_time.'ms | ';
	}
	// 
	function copyright(){
		return 'COPYRIGHT&copy;2000-'.date('Y', TIMENOW).' NWI.';
	}
	// 
	function initialize(){
$this->wrapper = <<<EOF
<!doctype html><html><head>
<meta http-equiv="content-type" content="text/html;charset=utf-8">
<link rel="stylesheet" type="text/css" href="/public/s.css" />
<link rel="stylesheet" type="text/css" href="/public/jqueryui.css" />
<script type="text/javascript" src="/public/jquery.js"></script>
<script type="text/javascript" src="/public/jqueryui.js"></script>
<script type="text/javascript" src="/public/layer.min.js"></script>
<script type="text/javascript" src="/public/skin/layer.css"></script>
<script type="text/javascript" src="/public/s.js"></script><%HEADER%>
<title><%TITLE%></title>
</head>
<body<%ONLOAD%>><div id="wrap">
	<div id="header" class="clear">
		<div id="board">
			<div class="normal hand" id="notice"><span class="notice"></span>
			</div>
			<ul class="noticeBoard" id="noticeBoard"></ul>
		</div>
		<div id="logo"><%LOGO%></div>
		<div id="nav"><%NAV%></div>
	</div>
	<div id="body">
		<%MAIN%>
	</div>
	<div id="footer">
		<div class="right"><%RUNTIME%><a href="javascript:scroll(0,0)">顶部</a></div>
		<%COPYRIGHT%> <font color="#F6F8FB">Powered by Albert.</font>
	</div>
</div></body></html>
EOF;

$this->wrapMsg = '<!doctype html><html><head><meta http-equiv="content-type" content="text/xml;charset=utf-8" /><meta http-equiv="refresh" content="<%SEC%>;url=<%URL%>" /><link rel="stylesheet" type="text/css" href="/public/msg.css" /><title><%TITLE%></title></head><body><div id="msg"><div id="title"><%TITLE%></div><div id="body"><%TEXT%></div><div id="footer">请稍等, 正在转向中...<br />(<a href="<%URL%>">如果不想等待, 请点击这里</a>)</div></div></body></html>';
	}
}
?>