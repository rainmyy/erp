<?php
//
class sign{
	# Global
	var $kclass;
	var $output;

	/*--------------------*/
	// Auto run function
	/*--------------------*/
	function autoRun(){
		switch($this->kclass->input['action']){
			case 'form':
				return $this->form();
				break;
			case 'list':
				return $this->form();
				break;
			case 'in':
				$this->in();
				break;
			case 'out':
				$this->out();
				break;
			case 'validate':
				$this->validate();
				break;

			default:
				$this->form();
		}
	}

	//
	function out(){
		if(preg_match("(sign|register|lostpwd|member|order)", $this->kclass->redirect)){
			$this->kclass->redirect = '/s.php';
		}
		// update session for member
		$this->kclass->sess->unloadUser();
		$this->kclass->sess->updateGuestSession();

		// dump sign success message
		$this->kclass->messager(array(
			'title' => '退出',
			'text' => '您已经成功安全退出。',
			'url' => '/s.php',
			'sec' => 3
		));
	}

	function in(){
		$e = '';
		// We must have submitted the form time to check some details.
//		if (strtolower($this->kclass->sess->session['validate']) != strtolower(trim($this->kclass->input['validate']))) {
//			$e = '<li>请输入验证码。</li>';
//		}else {
			$this->kclass->input['username'] = trim($this->kclass->input['username']);
			$this->kclass->input['username'] = str_replace( '|', '&#124;', $this->kclass->input['username'] );
			$this->kclass->input['password'] = trim($this->kclass->input['password']);

			if (empty($this->kclass->input['username'])) {
				$e .= '<li>请输入您的用户名。</li>';
			}else {
				$l = strlen($this->kclass->input['username']);
				if ($l < 3 OR $l > 16) {
					$e .= '<li>您输入的用户并无权限登录。</li>';
				}
			}
			if (empty($this->kclass->input['password'])) {
				$e .= '<li>请输入您的密码。</li>';
			}else{
				$l = strlen($this->kclass->input['password']);
				if ($l < 6 OR $l > 16) {
					$e .= '<li>您输入的密码不正确。</li>';
				}
			}
//		}

		if($e == ''){
			// Attempt to get the details from the DB
			$user = $this->kclass->DB->queryFirst("
				SELECT u.userid, u.groupid, u.username, u.password, u.lastVisit, u.lastActivity, u.counter, 
					g.zone, g.title AS `group`
				FROM `user` AS u
				LEFT JOIN `group` AS g ON (g.groupid=u.groupid)
				WHERE u.username='".$this->kclass->input['username']."'
				LIMIT 0,1 
			");
			if (!$user){
				$e .= "<li>您输入的用户名并无权限登录。</li>";
			}
			// Check password...
			$sign_key = md5($this->kclass->input['password']);
			if ( $sign_key !== $user['password'] ){
				$this->name = $this->kclass->input['username'];
				$e .= "<li>您输入的密码不正确。</li>";
			}
		}

		if($e != ''){
			$this->kclass->messager(array(
				'title' => '登录',
				'text' => '您的登录由于下面的原因，未登录成功：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}

		//All is good, rejoice as we set a session for this user
		$this->kclass->sess->user = $user;
		$this->kclass->sess->updateUserSession();

		//Redirect... dump sign success message
		$this->kclass->messager(array(
			'title' => '登录',
			'text' => '您已经使用用户名：<b>'.trim($this->kclass->input['username']).'</b> 登录。<br /><br />已确定您的权限。',
			'url' => $this->kclass->input['redirect'],
			'final' => 1,
			'sec' => 2
		));
	}

	//
	function form($message = '&nbsp;'){
		if($this->kclass->user['userid']>0){
			$this->kclass->boinkIt('/s.php');
		}

		if(preg_match("(sign|register|lostpwd)", URI)){
			$redirect = '/s.php';
		}else{
			$redirect = URI;
		}

		$copyright = COPYRIGHT.' <a href="http://www.wisdom.hk/copyright.html" target="_blank" class="small">版权所有</a>';
return <<<EOF
<!doctype html>
<html>
<head>
<meta http-equiv="content-type" content="text/html;charset=utf-8">
<link rel="stylesheet" href="/public/sign.css" /><script src="/public/jquery.js"></script>
<script>
var dc={};
dc.sign={
	check:function(e){
		if(e.username.value.length < 3){
			alert("请输入用户名。");
			e.username.focus();
			return false;
		}
		if(e.password.value.length < 3){
			alert("请输入密码。");
			e.password.focus();
			return false;
		}
		e.signin.disabled=true;
		e.signin.value = "　　正在登陆 . . .　　";
	},
	focus:function(){
		document.getElementById('signin').disabled=false;
		$("#signin").attr('disabled',false);
		var u = $('#username');
		if(u.attr('value')==undefined || u.attr('value')== ""){
			u.focus();
		}else{
			$('#password').focus();
		}
	}
}
$('document').ready(function(){dc.sign.focus();});
</script>
<title>{$this->kclass->vars['titleERP']} - 登录</title></head><body>
<div id="wrap">
	<div class="right" style="width:330px">
		<div class="center" style="margin:20px"><img src="/public/images/logomis.png" width="260" height="42" /></div>
		<div class="center">
			<form name="signform" action="/s.php?module=sign&action=in" method="post" onsubmit="return dc.sign.check(this);">
			<input type="hidden" name="redirect" value="{$redirect}" />
			<input type="hidden" name="module" value="sign" />
			<input type="hidden" name="action" value="in" />
			<div style="margin:8px 0"><span style="display:inline-block;width:60px">用户名：</span><input type="text" id="username" name="username" style="width:150px"></div>
			<div style="margin:8px 0"><span style="display:inline-block;width:60px">密　码：</span><input type="password" id="password" name="password" style="width:150px"></div>
			<div class="center" style="margin:10px"><input type="submit" id="signin" name="signin" value="　　登录　　" /></div>
			</form>
		</div>
		<div class="tiny gray">This program is protected by international copyright laws as described in the license agreement.</div>
		<div class="tiny gray">{$copyright}</div>
	</div>
</div>
</body></html>
EOF;
	}

	//
	function validate(){
		// no caching
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");             // Date in the past
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . "GMT"); // always modified
		header("Cache-Control: no-cache, must-revalidate");           // HTTP/1.1
		header("Pragma: no-cache");                                   // HTTP/1.0
		header("Content-type: image/png");

		//数字位数
		$number_bit = $this->kclass->krand(2,4);
		//定义允许出现的字母
		$letter_lower = array('a', 'b', 'c', 'd', 'e', 'f', 'h', 'j', 'k', 'm', 'n', 'p', 'r', 's', 't', 'w', 'x', 'y', 'z');
		$letter_upper = array('A', 'B', 'C', 'D', 'E', 'F', 'H', 'J', 'K', 'M', 'N', 'P', 'R', 'S', 'T', 'W', 'X', 'Y', 'Z');
		$k = 0;
		for ($i = 0; $i < 6; $i++) {
			if ($this->kclass->krand(0,1) AND $k < $number_bit) {//随机确定是否使用数字
				$text .= $this->kclass->krand(2,8);
				$k++;
			}else{
				$letter_num = $this->kclass->krand(0,18);
				if ($this->kclass->krand(0,1)) {//随机确定是否使用小写字母
					$text .= $letter_upper[$letter_num];
				}else {
					$text .= $letter_lower[$letter_num];
				}
			}
		}

		//记录随机码
		if (!empty($text)) {
			$this->kclass->DB->query("
				UPDATE sessions
				SET validate = '".$text."'
				WHERE id = '" . addslashes(trim($this->kclass->sess->session['id'])) . "'
			");
		}

		$image = array();

		$image['width'] = 180;
		$image['height'] = 40;
		//字符间距
		$space_min = 6;
		$space_max = 10;
		//字符位置
		$xpos = 10;
		$ypos_min = 26;
		$ypos_max = 30;
		//字体大小
		$font_size_min = 16;
		$font_size_max = 24;
		//字体
		$font_path = './private/fonts/';
		$font_staff = array('arbli.ttf', 'xsuni.ttf', 'serifth.ttf', 'pertili.ttf', 'vinet.ttf', 'swissko.ttf');

		$validate_image = imagecreate ($image['width'], $image['height']);
		$bgcolor = ImageColorAllocate ($validate_image, 255, 255, 255);
		$font_color = ImageColorAllocate ($validate_image, 0,0,0);

		for($i=0;substr($text, $i,1);$i++) {
			$image['space'] = $this->kclass->krand($space_min, $space_max);
			$image['ypos'] = $this->kclass->krand($ypos_min, $ypos_max);
			$image['font_size'] = $this->kclass->krand($font_size_min, $font_size_max);
			$image['font'] = $font_path.$font_staff[$this->kclass->krand(0,(count($font_staff) - 1))];
//			$xpos = _make_text(substr($text, $i,1), $xpos, $image['ypos'], $image['font'], $image['font_size'], $image['space'], $image_width, $image_height);
			$char = substr($text, $i,1);
			$image['font'] = realpath($image['font']);
			$text_box = imagettfbbox ($image['font_size'], 0, $image['font'], $char);
			imagettftext ($validate_image, $image['font_size'], 0, $xpos, $image['ypos'], $font_color, $image['font'], $char);
			$xpos += $text_box[4] + $image['space'];
		}

		Imagepng ($validate_image);
		ImageDestroy ($validate_image);
		exit;
	}
}
?>