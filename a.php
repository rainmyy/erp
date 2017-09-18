<?php
$vars['area'] = 'a';//s:system, a:admin, w:web
require ('private/global.php');
require_once('private/akclass.php');
require_once('private/skin/admin.php');
$kclass->skin = new skin;
$kclass->skin->initialize();
// parse url
if ($kclass->input['module'] == '') $kclass->input['module'] = 'home';
if($kclass->input['action'] == '') $kclass->input['action'] = 'list';
// guest must login
if($kclass->user['userid']!= 2 AND $kclass->user['groupid'] != 1 AND $kclass->input['module'] != 'login'){
	$kclass->input['module'] = 'sign';
	$kclass->input['action'] = 'form';
	if($kclass->user['userid'] > 0 ){
		$kclass->session['msg'] = '您暂无权限查看这个页面。';
	}
	$m = 'private/module/system/'.$kclass->input['module'].'.php';
}else{
	$m = 'private/module/admin/'.$kclass->input['module'].'.php';
}

if(!is_file($m)){
	$kclass->fatalError('The class did not exist!', 'Please contact our Technical Staff. Thanks.');
}else{
	require ($m);
	$r = new $kclass->input['module'];
	$r->kclass = &$kclass;
	$kclass->export($r->autoRun());
	unset($r);
}
unset($kclass);
exit;
?>