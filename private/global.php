<?php
// Get run time
$timer = explode(" ",microtime());
define('TIMENOW', $timer[1]);
define('MICRO_TIME', $timer[0]);
unset($timer);
define('COPYRIGHT', '&copy;2011 New Wisdom Investment Limited');
// Set some constant
if(@$_SERVER['HTTP_CLIENT_IP']){
	define('IP', $_SERVER['HTTP_CLIENT_IP']);
}elseif(@$_SERVER['HTTP_X_FORWARDED_FOR'] AND preg_match('#\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}#s', @$_SERVER['HTTP_X_FORWARDED_FOR'], $matches)){
	define('IP', $matches[0]);
}elseif(@$_SERVER['HTTP_FROM']){
	define('IP', @$_SERVER['HTTP_FROM']);
}else{
	define('IP', $_SERVER['REMOTE_ADDR']);
}
define('URI', $_SERVER["REQUEST_URI"] ? $_SERVER['REQUEST_URI'] : $_SERVER["PHP_SELF"]);
define('AGENT', substr($_SERVER['HTTP_USER_AGENT'], 0, 255));
define('REFERER', @$_SERVER['HTTP_REFERER']);
define('ENCODING',$_SERVER['HTTP_ACCEPT_ENCODING']);
define('ROOT',$_SERVER['DOCUMENT_ROOT']);

require "private/config.php";
require "private/mysql.php";
require "private/kclass.php";
require "private/session.php";
require "private/skin/global.php";
# Initiate super-class
if($vars['area']=='w'){
	$kclass = new kclass();
}else{
	require 'private/'.$vars['area'].'kclass.php';
	$kclass = new fkclass();
}
$kclass->vars = $vars;
$kclass->parseIncoming();
$kclass->initDB();
$kclass->sess = new session();
$kclass->sess->kclass =&$kclass;
$kclass->user = $kclass->sess->authorise();

//echo '<pre>'.print_r($kclass->user, 1).'</pre>';
//echo '<pre>'.print_r($kclass->session, 1).'</pre>';exit;
?>