<?php
error_reporting(E_ALL ^ E_NOTICE);
$vars['area'] = 's';//s:system, a:admin, w:web
require 'private/global.php';

if ($kclass->input['module'] == '') $kclass->input['module'] = 'report';
if($kclass->input['action'] == '') $kclass->input['action'] = 'mmlist';
// guest must login
if($kclass->user['userid'] == 0 AND $kclass->input['module'] != 'sign'){
	$kclass->boinkIt('/s.php');
}else{
	if(!$kclass->purviews(array('final'=>1))){
		$kclass->boinkIt('/s.php');
	}
}
require ('private/module/system/report.php');
$r = new $kclass->input['module'];
$r->kclass = &$kclass;
$r->autoRun();

$w = <<<EOF
<!doctype html><html><head>
<meta http-equiv="content-type" content="text/html;charset=utf-8">
<link rel="stylesheet" type="text/css" href="/public/r.css" />
<link rel="stylesheet" type="text/css" href="/public/jqueryui.css" />
<script type="text/javascript" src="/public/jquery.js"></script>
<script type="text/javascript" src="/public/jqueryui.js"></script>
<script type="text/javascript" src="/public/s.js"></script>
<title>{$kclass->page['title']}</title>
</head>
<body><div id="wrap">
	<div id="header"></div>
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