<?php
error_reporting(E_ALL ^ E_NOTICE);
$vars['area'] = 's';//s:system, a:admin, w:web
require 'private/global.php';

if ($kclass->input['module'] == '') $kclass->input['module'] = 'excel';
if($kclass->input['action'] == '') $kclass->input['action'] = '';
// guest must login
if($kclass->user['userid'] == 0 AND $kclass->input['module'] != 'sign'){
	$kclass->boinkIt('/s.php');
}else{
	if(!$kclass->purviews(array('final'=>1))){
		$kclass->boinkIt('/s.php');
	}
}
require ('private/module/system/excel.php');
$x = new $kclass->input['module'];
$x->kclass = &$kclass;
$x->autoRun();
unset($x);
unset($kclass);
exit;
?>