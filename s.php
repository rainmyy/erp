<?php
error_reporting(E_ALL ^ E_NOTICE);
$vars['area'] = 's';//s:system, a:admin, w:web
require ('private/global.php');
require('private/skin/system.php');
$kclass->skin = new skin;
$kclass->skin->initialize();

// parse url
if ($kclass->input['module'] == '') $kclass->input['module'] = 'home';
if($kclass->input['action'] == '') $kclass->input['action'] = 'list';
// guest must login
if($kclass->user['userid'] == 0 AND $kclass->input['module'] != 'sign'){
	$kclass->input['module'] = 'sign';
	$kclass->input['action'] = 'form';
}elseif($kclass->input['module'] != 'sign' AND $kclass->input['module'] != 'ajax' AND ($kclass->input['action'] != 'out' OR $kclass->input['action'] != 'in' OR $kclass->input['action'] != 'validate')){
	$kclass->purviews(array());
}
$m = 'private/module/system/'.$kclass->input['module'].'.php';
if(!is_file($m)){
	$kclass->fatalError('The class did not exist!', 'Please contact our Technical Staff. Thanks.');
}else{
	require ($m);
	$r = new $kclass->input['module'];
	$r->kclass = &$kclass;
	$kclass->export($r->autoRun());
	unset($r);
}
//echo '<pre>'.print_r($kclass->user, 1).'</pre>';
//echo '<pre>'.print_r($kclass->session, 1).'</pre>';exit;
unset($kclass);
exit;
?>