<?php
// 
class skin extends gskin{

// 
function logo(){
	return '<a href="/s.php"><img src="/public/images/logomis.png" width="189" height="30"></a>';
}
function nav($u){
	return '<span class="right">'.$u.' | <a href="/s.php" target="_blank">访问系统</a> | <a href="/s.php?module=login&action=logout">退出</a></span><span class="big">控制面板</span>';
}

// 
function blink($t){
	return $t.'ms | ';
}

// 
function navSide(){
return <<<EOF
<div class="title"><span class="right normal"><a href="/a.php?module=home">首页</a></span>后台管理</div>
<div class="body">
<ul>
	<li>系统公告
		<ul>
			<li><a href="/a.php?module=billboard&action=add">新建</a></li>
			<li><a href="/a.php?module=billboard">全部</a></li>
		</ul>
	</li>
	<li>系统用户与权限管理
		<ul>
			<li><a href="/a.php?module=user">用户</a></li>
			<li><a href="/a.php?module=user&action=group">用户组</a></li>
			<li><a href="/a.php?module=position">职位与权限</a></li>
		</ul>
	</li>
	<li>通告与流程
		<ul>
			<li><a href="/a.php?module=oa&action=options">设置</a></li>
			<li><a href="/a.php?module=oa&action=notice">通告管理</a></li>
			<li><a href="/a.php?module=oa&action=flow">流程管理</a></li>
		</ul>
	</li>
	<li>邮件管理
		<ul>
			<li><a href="/a.php?module=mail&action=">统计</a></li>
			<li><a href="/a.php?module=mail&action=option">设置</a></li>
		</ul>
	</li>
	<li>系统配置
		<ul>
			<li><a href="/a.php?module=setting&action=options">全局设置</a></li>
			<li><a href="/a.php?module=setting&action=dft">默认值设置</a></li>
			<li><a href="/a.php?module=setting&action=attach">附件管理</a></li>
			<li><a href="/a.php?module=setting&action=module">模块管理</a></li>
			<li><a href="/a.php?module=setting&action=menu">菜单管理</a></li>
			<li><a href="/a.php?module=log">日志管理</a></li>
			<li><a href="/a.php?module=log&action=stats">系统统计</a></li>
		</ul>
	</li>
	<li>网站基础内容管理
		<ul>
			<li><a href="/a.php?module=other&action=update&mod=about">关于我们</a></li>
			<li><a href="/a.php?module=other&action=update&mod=contact">联系我们</a></li>
			<li><a href="/a.php?module=other&action=update&mod=official">代理商列表</a></li>
			<li><a href="/a.php?module=other&action=update&mod=privacy">隐私声明</a></li>
			<li><a href="/a.php?module=other&action=update&mod=rss">订阅管理</a></li>
		</ul>
	</li>
</div>
EOF;
}
	// 
	function initialize(){
$this->wrapper = <<<EOF
<!doctype html><html><head>
<meta http-equiv="content-type" content="text/html;charset=utf-8">
<script type="text/javascript" src="/public/jquery.js"></script>
<link rel="stylesheet" type="text/css" href="/public/a.css" />
<script type="text/javascript" src="/public/a.js"></script><%HEADER%>
<title><%TITLE%></title>
</head>
<body<%ONLOAD%>><div id="wrap">
	<div id="header">
		<div id="nav"><%NAV%></div>
		<div id="logo"><%LOGO%></div>
	</div>
	<div id="body" class="clear">
		<div id="main"><%MAIN%></div>
		<div id="left"><%LEFT%></div>
	</div>
	<div id="footer">
		<div class="right"><%RUNTIME%><a href="javascript:scroll(0,0)">顶部</a></div>
		<%COPYRIGHT%> <font color="#EEEEEE">Powered by Albert.</font>
	</div>
</div></body></html>
EOF;

$this->wrapMsg = '<!doctype html><html><head><meta http-equiv="content-type" content="text/xml;charset=utf-8" /><meta http-equiv="refresh" content="<%SEC%>;url=<%URL%>" /><link rel="stylesheet" type="text/css" href="/public/msg.css" /><title><%TITLE%> - NWIS</title></head><body><div id="msg"><div id="title"><%TITLE%></div><div id="body"><%TEXT%></div><div id="footer">请稍等, 正在转向中...<br />(<a href="<%URL%>">如果不想等待, 请点击这里</a>)</div></div></body></html>';
	}
}
?>