<?php
error_reporting(E_ALL ^ E_NOTICE);
$vars['area'] = 'm';//s:system, a:admin, w:web m:mobile
require 'private/global.php';

$button=$kclass->iif($kclass->input['action'] == '','','<a href="/m.php" data-role="button" data-icon="arrow-l" data-transition="slide">返回</a>');
if ($kclass->input['module'] == '') $kclass->input['module'] = 'mobile';
if($kclass->input['action'] == '') $kclass->input['action'] = 'list';

// guest must login
if($kclass->user['userid'] == 0 AND $kclass->input['module'] != 'sign'){
	$kclass->boinkIt('/s.php');
}

require ('private/module/system/mobile.php');
$r = new $kclass->input['module'];
$r->kclass = &$kclass;
$r->autoRun();

$w = <<<EOF
<!DOCTYPE html> 
<html> 
<head> 
	<title>{$kclass->page['title']}</title> 
		<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1"> 
	<link rel="stylesheet" href="http://code.jquery.com/mobile/1.3.2/jquery.mobile-1.3.2.min.css" />
	<script src="http://code.jquery.com/jquery-1.9.1.min.js"></script>
	<script src="http://code.jquery.com/mobile/1.3.2/jquery.mobile-1.3.2.min.js"></script>
</head> 
<body>

<div data-role="page">
	<div data-role="header" data-theme="b">
	{$button}
	<a href="/s.php?module=sign&action=out" class="ui-btn-right" data-role="button" data-icon="gear" data-transition="slide">注销</a>
		<h1>移动ERP系统</h1>
	</div><!-- /header -->
<div data-role="content">	
<ul data-role="listview" data-inset="true" data-filter="true" data-filter-reveal="true"  data-filter-placeholder="输入 0 1 2 3...">
    <li><a href="/m.php?action=order">01 PO单</a></li>
    <li><a href="/m.php?action=sample">02 样品单</a></li>
    <li><a href="/m.php?action=requirement">03 需求计划单</a></li>
    <li><a href="/m.php?action=task">04 任务单</a></li>
    <li><a href="/m.php?action=dispatch">05 派工单</a></li>
    <li><a href="/m.php?action=oubound">06 出库单</a></li>
    <li><a href="/m.php?action=inbound">07 入库单</a></li>
    <li><a href="/m.php?action=rank">08 操作排行</a></li>
    <li><a href="/m.php?action=online">09 在线列表</a></li>
</ul>
{$kclass->page['main']}
</div><!-- /content -->
<div data-role="footer" data-id="foo1" data-position="fixed" data-theme="b">
	<div data-role="navbar" data-iconpos="left">
		<ul>
			<li><a href="/m.php" data-transition="slide" data-icon="home">主页</a></li>
			<li><a href="/m.php?action=nav" data-transition="slide" data-icon="search">导航</a></li>
			<li><a href="/m.php?action=center" data-transition="slide" data-icon="star">个人中心</a></li>
		</ul>
	</div><!-- /navbar -->
</div><!-- /footer -->
</div><!-- /page -->

</body>
</html>
EOF;

unset($r);
unset($kclass);

print $w;
exit;
?>