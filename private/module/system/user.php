<?php
// 
class user {
	var $baseurl;
	var $right;
	// 
	function autoRun(){
		if($this->kclass->user['userid'] <= 0){
			$this->kclass->invalid();
		}

		$this->kclass->page['title'] = '个人面板';
		$this->baseurl = '<a href="/s.php">首页</a> - <a href="/s.php?module=user">个人面板</a>';
		$this->right = '<a href="/s.php?module=user&action=update">修改资料</a>　<a href="/s.php?module=user&action=password">修改密码</a>　<a href="/s.php?module=user&action=setting">设置</a>';

		switch($this->kclass->input['action']){
			case 'list':
				$this->view();
				break;
			case 'update':
				$this->update();
				break;
			case 'doupdate':
				$this->doupdate();
				break;
			case 'password':
				$this->password();
				break;
			case 'dopassword':
				$this->dopassword();
				break;
			case 'setting':
				$this->setting();
				break;
			case 'dosetting':
				$this->dosetting();
				break;
		
			default:
				$this->view();
		}
	}

	// 
	function view(){
		$joindate = date('Y-m-d H:i:s', $this->kclass->user['joindate']);
		$lastVisit = date('Y-m-d H:i:s', $this->kclass->user['lastVisit']);
		$lastActivity = date('Y-m-d H:i:s', $this->kclass->user['lastActivity']);
$body = <<<EOF
<div class="title">{$this->kclass->user['username']} <span class="small">({$joindate})</span></div>
<dl id="userinfo" class="tabs">
	<dt>基本资料</dt>
	<dt>考勤</dt>
	<dt>办公用品</dt>
	<dt>报销</dt>
	<dt>会议</dt>
	<dt>差旅</dt>
	<dt>短消息</dt>
	<dt>工作日历</a></li>
	<dt>通告</dt>
	<dt>工作流程</dt>
<dd>
<table>
<thead>
<tr>
	<th colspan="4">用户信息</th>
</tr>
</thead>
<tbody>
<tr>
	<td>用户姓名：</td>
	<td class="bold">{$this->kclass->user['username']}</td>
	<td>姓　　名：</td>
	<td class="bold">{$this->kclass->user['realname']}</td>
</tr>
<tr class="even">
	<td>所属组别：</td>
	<td class="bold">{$this->kclass->user['title']}</td>
	<td>加入时间：</td>
	<td class="small">{$joindate}</td>
</tr>
<tr>
	<td>上次登录：</td>
	<td class="small">{$lastVisit}</td>
	<td>最后活动：</td>
	<td class="small">{$lastActivity}</td>
</tr>
</tbody>
<thead>
<tr>
	<th colspan="4">联系方式</th>
</tr>
</thead>
<tbody>
<tr>
	<td>电子邮箱：</td>
	<td class="bold">{$this->kclass->user['email']}</td>
	<td>手机号码：</td>
	<td class="bold">{$this->kclass->user['mobilephone']}</td>
</tr>
<tr class="even">
	<td>固定电话：</td>
	<td class="bold">{$this->kclass->user['telephone']}</td>
	<td>传　　真：</td>
	<td class="bold">{$this->kclass->user['fax']}</td>
</tr>
<tr>
	<td>地　　址：</td>
	<td class="bold">{$this->kclass->user['address']}</td>
	<td>邮政编码：</td>
	<td class="bold">{$this->kclass->user['postalcode']}</td>
</tr>
</tbody>
<thead>
<tr>
	<th colspan="4">设置</th>
</tr>
</thead>
<tbody>
<tr>
	<td colspan="4"></td>
</tr>
</tbody>
</table>
	</dd>
	<dd>{$eventtbl}</dd>
	<dd>{$product['panel']}</dd>
	<dd>{$customer['panel']}</dd>
	<dd>{$supplier['panel']}</dd>
	<dd>{$supplier['panel']}</dd>
	<dd>{$supplier['panel']}</dd>
	<dd>{$supplier['panel']}</dd>
	<dd>{$supplier['panel']}</dd>
	<dd>{$supplier['panel']}</dd>
</dl>
EOF;
		$this->kclass->page['onload'] .= "dc.tabs({'id':'userinfo'});dc.tabhover()";
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl, 'right' => $this->right, 'body'=>$body));
	}

	// 
	function dopassword(){
		$this->kclass->input['oldpassword'] = trim($this->kclass->input['oldpassword']);
		$this->kclass->input['password'] = trim($this->kclass->input['password']);
		$this->kclass->input['repassword'] = trim($this->kclass->input['repassword']);

		if(strlen($this->kclass->input['oldpassword']) < 6 OR strlen($this->kclass->input['oldpassword']) > 16){
			$errors .= '<li>请输入您正确的 正在使用的密码！</li>';
		}elseif(md5($this->kclass->input['oldpassword']) != $this->kclass->user['password']){
			$errors .= '<li>您输入的正在使用的密码不正确，请重新输入！</li>';
		}
		if (strlen($this->kclass->input['password']) < 6 OR strlen($this->kclass->input['password']) > 16 OR strlen($this->kclass->input['repassword']) < 6 OR strlen($this->kclass->input['repassword']) > 16) {
			$errors .= '<li>请输入您欲新设定的密码！</li>';
		}
		if ($this->kclass->input['password'] != $this->kclass->input['repassword']) {
			$errors .= '<li>您两次输入的密码不相同！</li>';
		}

		if ($errors !='') {
			$this->kclass->messager(array(
				'title' => '修改密码错误',
				'text' => '您在修改密码的过程中出现以下错误: <ul>'.$error.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}

		$this->kclass->DB->query("
			UPDATE user
			SET password = '".md5($this->kclass->input['password'])."'
			WHERE userid = '".$this->kclass->user['userid']."'
		");

		$this->kclass->messager(array(
			'title' => '修改密码成功',
			'text' => '恭喜，您已经修改了密码，请牢记您修改后的<b>新密码</b>。',
			'url' => '/s.php?module=user',
			'final' => 1,
			'sec' => 3
		));
	}

	// 
	function password(){
$body = <<<EOF
<form name="personal" method="post" action="/s.php?module=user&action=dopassword" onsubmit="return dc.user.password(this);">
<input type="hidden" name="module" value="user">
<input type="hidden" name="action" value="dopassword">
<table>
<thead>
<tr>
	<th colspan="2">重置 密码</th>
</tr>
</thead>
<tbody>
<tr>
	<td align="right"><b>正在使用的密码</b>：<span class="red bold">*</span></td>
	<td><input type="password" name="oldpassword" size="33" tabindex="1"></td>
</tr>
<tr class="even">
	<td align="right"><b>新设定的密码</b>：<span class="red bold">*</span></td>
	<td><input type="password" name="password" size="33" tabindex="2"></td>
</tr>
<tr>
	<td align="right"><b>重输新设定的密码：<span class="red bold">*</span></td>
	<td><input type="password" name="repassword" size="33" tabindex="3"></td>
</tr>
</tbody>
<thead>
<tr>
	<th colspan="2">验证码</th>
</tr>
</thead>
<tbody>
<tr>
	<td colspan="2" class="small gray">请输入图片中的字符用以验证。不区分大小写，共6位字符。</td>
</tr>
<tr class="even">
	<td align="right"><b>验证码：<span class="red bold">*</span></td>
	<td><input type="text" name="validate" size="20" tabindex="4" /></td>
</tr>
<tr>
	<td colspan="2" align="center"><img src="/s.php?module=sign&action=validate" onsubmit="this.submit.disabled=true;"></td>
</tr>
<tr class="even">
	<td align="center" colspan="2"><input type="submit" name="submit" value="    保存    " accesskey="m" tabindex="5">　　<input type="reset" value="    重置    " accesskey="r" tabindex="6"></td>
<tr>
</tbody>
</table>
</form>
EOF;
		$this->kclass->page['title'] .= ' - 修改密码';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 修改密码', 'right' => $this->right, 'body'=>$body));
	}

	// 
	function doupdate(){
		$this->kclass->input['email'] = trim($this->kclass->input['email']);
		if ($this->kclass->input['email'] == '') {
			$errors .= '<li>您没有输入 电子邮箱！</li>';
		}else {
			//check banemail
			if ($this->kclass->vars['ban'] and $this->kclass->vars['banEmail']!="") {
				if (stristr(" ".$this->kclass->vars['banEmail']." "," ".$this->kclass->input['email']." ")!="") {
					$errors .= '<li>您设定的邮箱地址属于禁用 邮箱地址，请返回列表设定一个其它的邮箱地址！</li>';
				}
				if ($this->kclass->input['banEmailDomain']=substr(strstr($this->kclass->input['email'],"@"),1)) {
					if (stristr(" ".$this->kclass->vars['banEmailDomain']." "," ".$this->kclass->input['email']." ")!="") {
						$errors .= '<li>您设定的邮箱地址的 域 属于禁用域，请返回列表设定一个其它的邮箱地址！</li>';
					}
				}
			}else {
				if (!preg_match('/^[-!#$%&\'*+\\.\/0-9=?A-Z^_`{|}~]+@([-0-9A-Z]+\.)+([0-9A-Z]){2,4}$/i', $this->kclass->input['email'])) {
					$errors .= '<li>您设定的邮箱地址格式有错误，请返回列表设定一个正确的邮箱地址！</li>';
				}
			}
		}
		if($this->kclass->input['mobile'] == ''){
			$errors .= '<li>您没有输入 手机！</li>';
		}
		if($this->kclass->input['address'] == ''){
			$errors .= '<li>您没有输入 地址！</li>';
		}
		if(strlen($this->kclass->input['postalcode']) != 6){
			$errors .= '<li>您没有输入 邮编！</li>';
		}

		if ($errors == '') {
			if ($this->kclass->vars['emailUnique'] AND $this->kclass->DB->queryFirst("SELECT username,email FROM user WHERE email='".addslashes($this->kclass->input['email'])."' AND userid<>'".$this->kclass->user['userid']."'")) {
				$errors .= '<li>您输入的这个邮件地址已经注册过，如果您是这个用户且您忘记了密码，您可以使用本站提供的 找回密码 功能，重新找回您忘记的密码。</li>';
			}
		}
		
		if ($errors!='') {
			$this->kclass->messager(array(
				'title' => '修改用户信息',
				'text' => '您在修改用户信息过程中有以下错误：<ul>'.$errors.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}

		$this->kclass->DB->query("
			UPDATE user
			SET `telephone` = '".$this->kclass->input['telephone']."' , 
				`mobilephone` = '".$this->kclass->input['mobile']."' , 
				`fax` = '".$this->kclass->input['fax']."' , 
				`email` = '".$this->kclass->input['email']."' , 
				`address` = '".$this->kclass->input['address']."' , 
				`postalcode` = '".$this->kclass->input['postalcode']."'
			WHERE userid = '".$this->kclass->user['userid']."'
		");

		$this->kclass->messager(array(
			'title' => '用户信息修改成功',
			'text' => '恭喜您已经成功修改用户信息。',
			'url' => '/s.php?module=user',
			'sec' => 3
		));
	}

	// 
	function update(){
		$joindate = date('Y-m-d H:i:s', $this->kclass->user['joindate']);
		$lastVisit = date('Y-m-d H:i:s', $this->kclass->user['lastVisit']);
		$lastActivity = date('Y-m-d H:i:s', $this->kclass->user['lastActivity']);
$body = <<<EOF
<form name="personal" method="post" action="/s.php?module=user&action=doupdate" onsubmit="return dc.user.update(this);">
<input type="hidden" name="module" value="user">
<input type="hidden" name="action" value="doupdate">
<table>
<thead>
<tr>
	<th colspan="4">用户信息</th>
</tr>
</thead>
<tbody>
<tr>
	<td>用户姓名：</td>
	<td class="bold">{$this->kclass->user['username']}</td>
	<td>姓　　名：</td>
	<td class="bold">{$this->kclass->user['realname']}</td>
</tr>
<tr class="even">
	<td>所属组别：</td>
	<td class="bold">{$this->kclass->user['title']}</td>
	<td>加入时间：</td>
	<td class="small">{$joindate}</td>
</tr>
<tr>
	<td>上次登录：</td>
	<td class="small">{$lastVisit}</td>
	<td>最后活动：</td>
	<td class="small">{$lastActivity}</td>
</tr>
</tbody>
<thead>
<tr>
	<th colspan="4">联系方式</th>
</tr>
</thead>
<tbody>
<tr>
	<td colspan="4" class="gray small">为了便于同事间相互联系，如您个人联系信息有变动，请及时更新。</td>
</tr>
<tr>
	<td>电子邮箱：<span class="red bold">*</span></td>
	<td><input type="text" name="email" size="35" tabindex="1" value="{$this->kclass->user['email']}"></td>
	<td>固定电话：</td>
	<td><input type="text" name="telephone" size="35" tabindex="2" value="{$this->kclass->user['telephone']}"></td>
</tr>
<tr>
	<td>手机号码：<span class="red bold">*</span></td>
	<td><input type="text" name="mobile" size="35" tabindex="3" value="{$this->kclass->user['mobilephone']}"></td>
	<td>传　　真：</td>
	<td><input type="text" name="fax" size="35" tabindex="4" value="{$this->kclass->user['fax']}"></td>
</tr>
<tr>
	<td>地　　址：<span class="red bold">*</span></td>
	<td><input type="text" name="address" size="35" tabindex="5" value="{$this->kclass->user['address']}"></td>
	<td>邮政编码：<span class="red bold">*</span></td>
	<td><input type="text" name="postalcode" size="35" tabindex="6" value="{$this->kclass->user['postalcode']}"></td>
</tr>
<tr>
	<td align="center" colspan="4">
		<input type="submit" name="submit" value="    保存    " accesskey="g" tabindex="7">　　
		<input type="reset" value="    重置    " accesskey="r" tabindex="8">
	</td>
</tr>
<tr>
	<td class="small gray" colspan="4">提示: 以下 <span class="red bold">*</span> 的内容必须填写。</td>
</tr>
</tbody>
</table>
</form>
EOF;
		$this->kclass->page['title'] .= ' - 修改资料';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 修改资料', 'right' => $this->right, 'body'=>$body));
	}

	// 
	function dosetting(){
		
	}

	// 
	function setting(){
$body = <<<EOF
<form name="personal" method="post" action="/s.php?module=user&action=dosetting" onsubmit="this.submit.disabled=true;">
<input type="hidden" name="module" value="user">
<input type="hidden" name="action" value="dosetting">
<table>
<tbody>
<tr>
	<td>当前无可用设置。</td>
</tr>
</tbody>
</table>
</form>
EOF;

		$this->kclass->page['title'] .= ' - 帐户设置';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 帐户设置', 'right' => $this->right, 'body'=>$body));
	}
}
?>