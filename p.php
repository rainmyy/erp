<?php
error_reporting(E_ALL ^ E_NOTICE);
$vars['area'] = 's';//s:system, a:admin, w:web
require 'private/global.php';

if ($kclass->input['module'] == '') $kclass->input['module'] = 'printer';
if($kclass->input['action'] == '') $kclass->input['action'] = 'mmlist';
if($kclass->input['action'] == 'orderstatistics'){
	$divwrap = '<div id="wrap-s">';//订单统计页面容器
}else{
	$divwrap = '<div id="wrap">';//打印页面容器
}
// guest must login
if($kclass->user['userid'] == 0 AND $kclass->input['module'] != 'sign'){
	$kclass->boinkIt('/s.php');
}else{
	if(!$kclass->purviews(array('final'=>1))){
		$kclass->boinkIt('/s.php');
	}
}
require ('private/module/system/printer.php');
$r = new $kclass->input['module'];
$r->kclass = &$kclass;
$r->autoRun();

$w = <<<EOF
<!doctype html><html><head>
<meta http-equiv="content-type" content="text/html;charset=utf-8">
<link rel="stylesheet" type="text/css" href="/public/p.css" />
<link rel="stylesheet" type="text/css" href="/public/jqueryui.css" />
<script type="text/javascript" src="/public/jquery.js"></script>
<script type="text/javascript" src="/public/jqueryui.js"></script>
<script type="text/javascript" src="/public/s.js"></script>
<title>{$kclass->page['title']}</title>
</head>
<body>{$divwrap}
	<div id="header" class="clear">
		<div id="subject">
			<div id="title">{$kclass->page['title']}</div>
			<div id="caption">{$kclass->page['caption']}</div>
		</div>
		<div id="logo"><img src="/public/images/logo.jpg" width="220" height="41" /></div>
		<div id="contact">
		公司地址：深圳市龙岗区宝荷路嶂背大道9号1楼<br>
		Tel: 86-755-83911841  Fax: 86-755-83906115 
		</div>
	</div>
	<div id="body">
	{$kclass->page['main']}
	</div>
	<div id="footer">
	{$kclass->page['footer']}
	</div>
</div></body></html>
EOF;

unset($r);
unset($kclass);

header("HTTP/1.0 200 OK");
header("HTTP/1.1 200 OK");
header('Content-type:text/html;charset=utf-8');
print $w;
exit;
?>