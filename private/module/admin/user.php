<?php
//
class user{
	# Global
	var $kclass;
	var $output;

	/*--------------------*/
	// Auto run function
	/*--------------------*/
	function autoRun(){
		$this->kclass->page['title'] = '用户';
		$this->baseurl = '<a href="/a.php">首页</a> - <a href="/a.php?module=user">用户</a>';
		$this->right = '<a href="/a.php?module=user&action=group">用户组</a> | <a href="/a.php?module=user">用户</a> <a href="/a.php?module=user&action=find">查用户</a>';
		switch($this->kclass->input['action']){
			case 'list':
				$this->modify();
			break;
			case 'view':
				$this->view();
			break;
			case 'purview':
				$this->purview();
			break;
			case 'dopurview':
				$this->dopurview();
			break;
			case 'add':
				$this->add();
			break;
			case 'insert':
				$this->insert();
			break;
			case 'update':
				$this->update();
			break;
			case 'doupdate':
				$this->doupdate();
			break;
			case 'remove':
				$this->remove();
			break;
			case 'kill':
				$this->kill();
			break;
			case 'revival':
				$this->revival();
			break;
			case 'restore':
				$this->restore();
			break;
			case 'find':
				$this->find();
			break;
			case 'group':
				$this->group();
			break;
			case 'gView':
				$this->gView();
			break;
			case 'gAdd':
				$this->gAdd();
			break;
			case 'gInsert':
				$this->gInsert();
			break;
			case 'gUpdate':
				$this->gUpdate();
			break;
			case 'gDoupdate':
				$this->gDoupdate();
			break;
			case 'gRemove':
				$this->gRemove();
			break;
			case 'gKill':
				$this->gKill();
			break;

			default:
				$this->modify();
		}
	}

	//
	function view(){
		if($this->kclass->input['userid']<=0 OR !$user = $this->kclass->DB->queryFirst("SELECT user.*, `group`.title, `group`.purviews, `group`.pcount AS pcounts FROM user LEFT JOIN `group` ON (`group`.groupid=user.groupid) WHERE userid='".$this->kclass->input['userid']."'")){
			$this->kclass->boinkIt('/a.php?module=user');
		}
		$joindate = date('y-m-d H:i:s', $user['joindate']);
		$lastVisit = date('y-m-d H:i:s', $user['lastVisit']);
		$lastActivity = date('y-m-d H:i:s', $user['lastActivity']);
		$name = ucfirst($user['username']);
		if($user['groupid']==1){
			$purviews = '<div class="center middle bold" style="padding:150px 0"><span class="darkred">管理员</span> 没有操作业务系统的权限。</div>';
		}elseif($user['inherit'] == 0 AND $user['pcount']>0){
			$purviews = $this->_showPurviews($user['purview']);// follow self
			$menu = $this->_menu($user['purview']);
			$pcount = $user['pcount'];
		}else{
			$purviews = '<div class="middle center darkred">继承自用户组 <a class="bold" href="/a.php?module=user&action=gView&groupid='.$user['groupid'].'" target="_blank">'.$user['title'].'</a> 的权限。</div>'.$this->_showPurviews($user['purviews']);// follow group
			$menu = $this->_menu($user['purviews']);
			$pcount = $user['pcounts'];
		}
		$logs = $this->_log(array('userid'=>$user['userid']));
$body = <<<EOF
<div id="userinfo">
	<ul class="tabs">
		<li><a><span class="darkred">{$name}</span> 基本资料</a></li>
		<li><a>权限<span class="tiny">({$pcount})</span></a></li>
		<li><a>日志<span class="tiny">({$logs['total']})</span></a></li>
		<li><a>统计<span class="tiny">(0)</span></a></li>
	</ul>
	<div class="tabText">
		<table cellspacing="10" align="center">
		<tr>
			<td valign="top">
			<fieldset>
				<legend>用户名称</legend>
				<table cellspacing="10" width="100%">
				<tr class="dotted">
					<td>用户姓名：</td>
					<td class="bold">{$user['username']}</td>
					<td>姓　　名：</td>
					<td class="bold">{$user['realname']}</td>
				</tr>
				<tr class="dotted">
					<td>所属组：</td>
					<td class="bold">{$user['title']}</td>
					<td>加入时间：</td>
					<td class="small">{$joindate}</td>
				</tr>
				<tr class="dotted">
					<td>上次登录：</td>
					<td class="small">{$lastVisit}</td>
					<td>最后活动：</td>
					<td class="small">{$lastActivity}</td>
				</tr>
				</table>
			</fieldset>
			</td>
		</tr>
		<tr>
			<td valign="top">
			<fieldset>
				<legend>联系方式</legend>
				<table cellspacing="10">
				<tr class="dotted">
					<td>电子邮箱：</td>
					<td class="bold">{$user['email']}</td>
					<td>手机号码：</td>
					<td class="bold">{$user['mobile']}</td>
				</tr>
				<tr class="dotted">
					<td>固定电话：</td>
					<td class="bold">{$user['telephone']}</td>
					<td>邮政编码：</td>
					<td class="bold">{$user['postalcode']}</td>
				</tr>
				<tr>
					<td>地　　址：</td>
					<td class="bold" colspan="3">{$user['address']}</td>
				</tr>
				</table>
			</fieldset>
			</td>
		</tr>
		<tr>
			<td colspan="2">
			<fieldset>
				<legend>设置</legend>
			</fieldset>
			</td>
		</tr>
		</table>
	</div>
	<div class="tabText">
			<fieldset>
				<legend>权限</legend>
		{$purviews}
			</fieldset>
			<fieldset>
				<legend>菜单</legend>
		{$menu}
			</fieldset>
	</div>
	<div class="tabText">
		{$logs['tab']}
	</div>
	<div class="tabText">
		暂无。
	</div>
</div>
EOF;
		$this->kclass->page['onload'] = "dc.tabs.init({'id':'userinfo'});";
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl.' - 用户信息 - '.$name, 'right' => $this->right.' | '.$this->kclass->iif($user['groupid']==1, '<a href="/a.php?module=user&action=update&userid='.$user['userid'].'">修改</a>', '<a href="/a.php?module=user&action=remove&userid='.$user['userid'].'">删除</a> <a href="/a.php?module=user&action=update&userid='.$user['userid'].'">修改</a> <a href="/a.php?module=user&action=purview&userid='.$user['userid'].'">权限</a>'), 'body'=>$body));
	}

	//
	function insert(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			if($this->kclass->input['groupid'] <= 0){
				$e = '<li>请选择 所属组。</li>';
			}
			if($this->kclass->input['username'] == ''){
				$e .= '<li>请填写 用户名称。</li>';
			}elseif($user=$this->kclass->DB->queryFirst("SELECT `userid`, `username` FROM `user` WHERE username='".$this->kclass->input['username']."'")){
				$e .= '<li>此用户名称[ <a href="/a.php?module=user&action=view&userid='.$user['userid'].'" target="_blank"><font size="5">'.$user['username'].'</font></a> ]已经存在，请重新选择其它用户名称。';
			}
			if($this->kclass->input['password'] == ''){
				$e .= '<li>请填写 密码。</li>';
			}
			if(strlen($this->kclass->input['password']) < 6 OR strlen($this->kclass->input['password']) > 16 ){
				$e .= '<li>密码必需在6-16位数字或字母。</li>';
			}
			if ($this->kclass->input['email'] == '') {
				$e .= '<li>请输入 电子邮箱。</li>';
			}else {
				//check banemail
				if ($this->kclass->vars['enable_banning'] and $this->kclass->vars['ban_email']!="") {
					if (stristr(" ".$this->kclass->vars['ban_email']." "," ".$this->kclass->input['email']." ")!="") {
						$e .= '<li>输入的邮箱是网站禁止使用的，请使用其它邮箱。</li>';
					}
					if ($this->kclass->input['email_domain']=substr(strstr($this->kclass->input['email'],"@"),1)) {
						if (stristr(" ".$this->kclass->vars['ban_email']." "," ".$this->kclass->input['email_domain']." ")!="") {
							$e .= '<li>输入的邮箱域是网站禁止使用的，请使用其它邮箱。</li>';
						}
					}
				}else {
					if (!preg_match('/^[-!#$%&\'*+\\.\/0-9=?A-Z^_`{|}~]+@([-0-9A-Z]+\.)+([0-9A-Z]){2,4}$/i', $this->kclass->input['email'])) {
						$e .= '<li>输入的 电子邮箱 不正确，请检查。</li>';
					}
				}
			}
		}

		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '新建用户',
				'text' => '您在新建用户的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$joindate = strtotime($this->kclass->input['joindate']);
		$this->kclass->DB->query("
			INSERT INTO `user` ( `groupid` , `username` , `password` , `realname` , `telephone` , `mobile` , `email` , `address` , `postalcode` , `remark` , `inherit` , `joindate` , `creator` , `created`)
			VALUES (
			'".$this->kclass->input['groupid']."', '".$this->kclass->input['username']."', '".md5($this->kclass->input['password'])."', '".$this->kclass->input['realname']."', '".$this->kclass->input['telephone']."', '".$this->kclass->input['mobile']."', '".$this->kclass->input['email']."', '".$this->kclass->input['address']."', '".$this->kclass->input['postalcode']."', '".$this->kclass->input['remark']."', '".$this->kclass->input['inherit']."', '".$joindate."', '".$this->kclass->member['userid']."', '".TIMENOW."'
			)
		");
		$userid = $this->kclass->DB->insertID();

		switch($this->kclass->input['o']){
			case 1:
				$rurl = 'list#'.$userid;
				break;
			case 2:
				$rurl = 'view&userid='.$userid;
				break;
			case 3:
				$rurl = 'purviews&userid='.$userid;
				break;
		}
		$this->kclass->mySetcookie(array('name'=>'o4u', 'value'=>$this->kclass->input['o']));
		$this->kclass->messager(array(
			'title' => '新建用户',
			'text' => '用户 <b>'.$this->kclass->input['username'].'</b> 已新建成功!',
			'url' => '/a.php?module=user&action='.$rurl,
			'sec' => 3
		));
	}

	//
	function add(){
		$group = $this->_gChoose(array('id'=>$user['groupid']));
		$joindate = date('Y-m-d', TIMENOW);
		$c = ' checked';
		switch($this->kclass->myGetcookie('o4u')){
			case 1:
				$o1 = $c;
				break;
			case 2:
				$o2 = $c;
				break;
			case 3:
				$o3 = $c;
				break;
			default:
				$o3 = $c;
				break;
		}
		$this->kclass->page['header'] = '<script src="/public/cal.js"></script><link rel="stylesheet" type="text/css" href="/public/cal.css" />';

$body = <<<EOF
<table cellspacing="10" align="center">
<form action="/a.php?module=user&action=insert" name="user" method="post">
<input type="hidden" name="module" value="user">
<input type="hidden" name="action" value="insert">
<tr class="dotted">
	<td>用户组：<span class="red bold">*</span></td>
	<td>{$group}</td>
	<td>邮　　箱：<span class="red bold">*</span></td>
	<td><input type="text" style="width:200px" size="26" name="email" value="{$user['email']}"></td>
</tr>
<tr class="dotted">
	<td valign="top">用户名称：<span class="red bold">*</span></td>
	<td><input type="text" style="width:200px" size="26" name="username" value="{$user['username']}"> <div class="small gray">用户名称位数为3-16位。</div></td>
	<td valign="top">密　　码：<span class="red bold">*</span></td>
	<td><input type="password" style="width:200px" size="26" name="password"> <div class="small gray">6-32位；<span class="bold">如不修改密码，请留空。</span></div></td>
</tr>
<tr class="dotted">
	<td>姓　　名：</td>
	<td><input type="text" style="width:200px" size="26" name="realname" value="{$user['realname']}"></td>
	<td>手　　机：</td>
	<td><input type="text" style="width:200px" size="26" name="mobile" value="{$user['mobile']}"></td>
</tr>
<tr class="dotted">
	<td>电　　话：</td>
	<td><input type="text" style="width:200px" size="26" name="telephone" value="{$user['telephone']}"></td>
	<td>邮　　编：</td>
	<td><input type="text" style="width:200px" size="26" name="postalcode" value="{$user['postalcode']}"></td>
</tr>
<tr class="dotted">
	<td>地　　址：</td>
	<td colspan="3"><input type="text" style="width:490px" size="68" name="address" value="{$user['address']}"></td>
</tr>
<tr class="dotted">
	<td valign="top">备　　注：</td>
	<td colspan="3"><textarea name="remark" rows="2" cols="65" style="width:490px;height:60px">{$user['remark']}</textarea></td>
</tr>
<tr class="dotted">
	<td valign="top">加入日期：</td>
	<td><input type="text" size="10" id="j0" name="joindate" value="{$joindate}"><img id="j1" src="/public/images/cal.gif" width="15" height="12" class="hand" /> <div class="small gray">格式必须为：YYYY-MM-DD</td>
	<td valign="top">继承权限：</td>
	<td valign="top">
		<label for="i1" class="red"><input type="radio" id="i1" name="inherit" value="1"{$i1}>继承</label>　
		<label for="i0" class="green"><input type="radio" id="i0" name="inherit" value="0"{$i0}>不继承</label> 
	</td>
</tr>
<tr class="solid">
	<td valign="top">选　　项：</td>
	<td valign="top" colspan="3">
		保存后：
		<label for="o0"><input type="radio" id="o0" name="o" value="1"{$o1}>返回到<b>用户列表</b>页</label> 
		<label for="o1"><input type="radio" id="o1" name="o" value="2"{$o2}>返回到<b>用户信息</b>页</label> 
		<label for="o2"><input type="radio" id="o2" name="o" value="3"{$o3}>转到到<b>用户权限设置</b>页</label>
	</td>
</tr>
<tr>
	<td colspan="4" align='center'>
		<input type="submit" value="      保存      " accesskey="s">　　
		<input type="reset" value="      复位      ">
	</td>
</tr>
</form>
</table>
<script type="text/javascript">
var cal = Calendar.setup({onSelect:function(cal){cal.hide()}});
cal.manageFields("j1", "j0", "%Y-%m-%d");
</script>
EOF;
		$this->kclass->page['title'] .= ' - 新建用户';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 新建用户', 'right' => $this->right . ' | <a href="/a.php?module=user">返回列表</a>', 'body'=>$body));
}

	//
	function doupdate(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			$user=$this->kclass->DB->queryFirst("SELECT `groupid` FROM `user` WHERE userid='".$this->kclass->input['userid']."'");
			if($user['groupid'] != 1){
				$group = "`groupid` = '".$this->kclass->input['groupid']."', ";
			}
			if($this->kclass->input['username'] == ''){
				$e .= '<li>请填写 用户名称。</li>';
			}
			if($this->kclass->input['password'] != ''){
				if(strlen($this->kclass->input['password']) < 6 OR strlen($this->kclass->input['password']) > 16 ){
					$e .= '<li>密码必需在6-16位数字或字母。</li>';
				}
				$password = "`password` = '".md5($this->kclass->input['password'])."', ";
				$pcm = '(<b>包括密码</b>)';
			}
			if ($this->kclass->input['email'] == '') {
				$e .= '请输入 电子邮箱.';
			}else {
				//check banemail
				if ($this->kclass->vars['enable_banning'] and $this->kclass->vars['ban_email']!="") {
					if (stristr(" ".$this->kclass->vars['ban_email']." "," ".$this->kclass->input['email']." ")!="") {
						$e .= '输入的邮箱是网站禁止使用的, 请使用其它邮箱.';
					}
					if ($this->kclass->input['email_domain']=substr(strstr($this->kclass->input['email'],"@"),1)) {
						if (stristr(" ".$this->kclass->vars['ban_email']." "," ".$this->kclass->input['email_domain']." ")!="") {
							$e .= '输入的邮箱域是网站禁止使用的, 请使用其它邮箱.';
						}
					}
				}else {
					if (!preg_match('/^[-!#$%&\'*+\\.\/0-9=?A-Z^_`{|}~]+@([-0-9A-Z]+\.)+([0-9A-Z]){2,4}$/i', $this->kclass->input['email'])) {
						$e .= '输入的 电子邮箱 不正确, 请检查.';
					}
				}
			}
		}

		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '修改用户信息',
				'text' => '修改用户信息不成功：<ul>'.$e.'</li>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$joindate = strtotime($this->kclass->input['joindate']);
		$this->kclass->DB->query("
			UPDATE `user`
			SET `username` = '".$this->kclass->input['username']."',
				$password
				$group
				`realname` = '".$this->kclass->input['realname']."',
				`email` = '".$this->kclass->input['email']."',
				`telephone` = '".$this->kclass->input['telephone']."',
				`mobile` = '".$this->kclass->input['mobile']."',
				`postalcode` = '".$this->kclass->input['postalcode']."',
				`address` = '".$this->kclass->input['address']."', 
				`joindate` = '".$joindate."', 
				`remark` = '".$this->kclass->input['remark']."', 
				`inherit` = '".$this->kclass->input['inherit']."', 
				`modifier` = '".$this->kclass->user['userid']."', 
				`modified` = '".TIMENOW."'
			WHERE userid='".$this->kclass->input['userid']."'
		");

		switch($this->kclass->input['o']){
			case 1:
				$rurl = 'list#'.$this->kclass->input['userid'];
				break;
			case 2:
				$rurl = 'view&userid='.$this->kclass->input['userid'];
				break;
			case 3:
				$rurl = 'purviews&userid='.$this->kclass->input['userid'];
				break;
		}
		$this->kclass->mySetcookie(array('name'=>'o4u', 'value'=>$this->kclass->input['o']));
		$this->kclass->messager(array(
			'title' => '修改用户信息',
			'text' => '用户 <b>'.$this->kclass->input['username'].'</b> 的信息已成功修改!'.$pcm,
			'url' => '/a.php?module=user&action='.$rurl,
			'sec' => 3
		));
	}

	//
	function update(){
		if($this->kclass->input['userid']<=0 OR !$user = $this->kclass->DB->queryFirst("SELECT user.*, group.title FROM user LEFT JOIN `group` ON (`group`.groupid=user.groupid) WHERE userid='".$this->kclass->input['userid']."'")){
			$this->kclass->boinkIt('/a.php?module=user');
		}
		if($user['groupid'] == 1){
			$group = $user['title'];
		}else{
			$group = $this->_gChoose(array('id'=>$user['groupid']));
		}
		if($user['inherit'] == 1){
			$i1 = ' checked';
		}else{
			$i0 = ' checked';
		}
		$joindate = date('Y-m-d', $this->kclass->iif($user['joindate']==0, TIMENOW, $user['joindate']));
		$c = ' checked';
		switch($this->kclass->myGetcookie('o4u')){//options for user = o4u
			case 1:
				$o1 = $c;
				break;
			case 2:
				$o2 = $c;
				break;
			case 3:
				$o3 = $c;
				break;
			default:
				$o2 = $c;
				break;
		}
		$this->kclass->page['header'] = '<script src="/public/cal.js"></script><link rel="stylesheet" type="text/css" href="/public/cal.css" />';

$body = <<<EOF
<table cellspacing="10" align="center">
<form action="/a.php?module=user&action=doupdate" name="user" method="post">
<input type="hidden" name="module" value="user">
<input type="hidden" name="action" value="doupdate">
<input type="hidden" name="userid" value="{$this->kclass->input['userid']}">
<tr class="dotted">
	<td>用户组：<span class="red bold">*</span></td>
	<td>{$group}</td>
	<td>邮　　箱：<span class="red bold">*</span></td>
	<td><input type="text" style="width:200px" size="26" name="email" value="{$user['email']}"></td>
</tr>
<tr class="dotted">
	<td valign="top">用户名称：<span class="red bold">*</span></td>
	<td><input type="text" style="width:200px" size="26" name="username" value="{$user['username']}"> <div class="small gray">用户名称位数为3-16位。</div></td>
	<td valign="top">密　　码：<span class="red bold">*</span></td>
	<td><input type="password" style="width:200px" size="26" name="password"> <div class="small gray">6-32位；<span class="bold">如不修改密码，请留空。</span></div></td>
</tr>
<tr class="dotted">
	<td>姓　　名：</td>
	<td><input type="text" style="width:200px" size="26" name="realname" value="{$user['realname']}"></td>
	<td>手　　机：</td>
	<td><input type="text" style="width:200px" size="26" name="mobile" value="{$user['mobile']}"></td>
</tr>
<tr class="dotted">
	<td>电　　话：</td>
	<td><input type="text" style="width:200px" size="26" name="telephone" value="{$user['telephone']}"></td>
	<td>邮　　编：</td>
	<td><input type="text" style="width:200px" size="26" name="postalcode" value="{$user['postalcode']}"></td>
</tr>
<tr class="dotted">
	<td>地　　址：</td>
	<td colspan="3"><input type="text" style="width:490px" size="68" name="address" value="{$user['address']}"></td>
</tr>
<tr class="dotted">
	<td valign="top">备　　注：</td>
	<td colspan="3"><textarea name="remark" rows="2" cols="65" style="width:490px;height:60px">{$user['remark']}</textarea></td>
</tr>
<tr class="dotted">
	<td valign="top">加入日期：</td>
	<td><input type="text" size="10" id="j0" name="joindate" value="{$joindate}"><img id="j1" src="/public/images/cal.gif" width="15" height="12" class="hand" /><div class="small gray">格式必须为：YYYY-MM-DD</td>
	<td valign="top">继承权限：</td>
	<td valign="top">
		<label for="i1" class="red"><input type="radio" id="i1" name="inherit" value="1"{$i1}>继承</label>　
		<label for="i0" class="green"><input type="radio" id="i0" name="inherit" value="0"{$i0}>不继承</label> 
	</td>
</tr>
<tr class="solid">
	<td valign="top">选　　项：</td>
	<td valign="top" colspan="3">
		保存后：
		<label for="o0"><input type="radio" id="o0" name="o" value="1"{$o1}>返回到<b>用户列表</b>页</label> 
		<label for="o1"><input type="radio" id="o1" name="o" value="2"{$o2}>返回到<b>用户信息</b>页</label> 
		<label for="o2"><input type="radio" id="o2" name="o" value="3"{$o3}>转到到<b>用户权限设置</b>页</label>
	</td>
</tr>
<tr>
	<td colspan="4" align='center'>
		<input type="submit" value="      保存      " accesskey="s">　　
		<input type="reset" value="      复位      ">
	</td>
</tr>
</form>
</table>
<script type="text/javascript">
var cal = Calendar.setup({onSelect:function(cal){cal.hide()}});
cal.manageFields("j1", "j0", "%Y-%m-%d");
</script>
EOF;
		$this->kclass->page['title'] .= ' - 修改用户 - '.$user['username'];
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 修改用户 - '.$user['username'], 'right' => $this->right . ' | <a href="/a.php?module=user">返回列表</a>', 'body'=>$body));
	}
	// 
	function dopurview(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			$user=$this->kclass->DB->queryFirst("SELECT `groupid` FROM `user` WHERE userid='".$this->kclass->input['userid']."'");
			if($user['groupid'] == 1){
				$e = '<li>管理员无法访问业务系统，不允许设置权限。</li>';
			}
		}

		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '设置用户权限',
				'text' => '设置用户权限不成功：<ul>'.$e.'</li>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$i=0;
		foreach($this->kclass->input as $k => $v){
			if(substr_count($k, 'purviews') == 1){
				foreach($v as $val){
					$p .= $val.',';
					$i++;
				}
			}
		}
		if($p != ''){
			$p = trim($p, ',');
		}
		$this->kclass->DB->query("
			UPDATE `user`
			SET `purview` = '".$p."',
				`pcount` = '".$i."',
				`modifier` = '".$this->kclass->user['userid']."', 
				`modified` = '".TIMENOW."'
			WHERE userid='".$this->kclass->input['userid']."'
		");

		$this->kclass->messager(array(
			'title' => '设置用户权限',
			'text' => '用户 <b>'.$this->kclass->input['username'].'</b> 的权限已成功设置!',
			'url' => '/a.php?module=user&action=view&userid='.$this->kclass->input['userid'],
			'sec' => 3
		));
	}
	//
	function purview(){
		if($this->kclass->input['userid']<=0 OR !$user = $this->kclass->DB->queryFirst("SELECT user.*, group.title FROM user LEFT JOIN `group` ON (`group`.groupid=user.groupid) WHERE userid='".$this->kclass->input['userid']."'")){
			$this->kclass->boinkIt('/a.php?module=user');
		}
		if($user['inherit'] == 1){
			$purview = '由于用户 '.$user['username'].' 的权限继承设置为<span class="bold">继承组权限</span>，无法设置该用户的权限。如需设置，请首先修改该用户的权限继承设置。';
		}else{
			$purview = $this->_purviews($user['purview']);
		}

$body = <<<EOF
<table cellspacing="10" align="center">
<form action="/a.php?module=user&action=dopurview" name="user" method="post">
<input type="hidden" name="module" value="user">
<input type="hidden" name="action" value="dopurview">
<input type="hidden" name="userid" value="{$this->kclass->input['userid']}">
<tr class="dotted">
	<td>用户组：</td>
	<td><a href="/a.php?module=user&action=gView&groupid={$user['groupid']}">{$user['title']}</a></td>
	<td>用户名称：</td>
	<td><a href="/a.php?module=user&action=view&userid={$user['userid']}">{$user['username']}</a></td>
</tr>
<tr class="dotted">
	<td colspan="4">权限选择： <span class="small gray">只对未设置<span class="bold">不继承组权限</span>的用户。</span></td>
</tr>
<tr>
	<td colspan="4">{$purview}</td>
</tr>
<tr class="solid">
	<td colspan="4"></td>
</tr>
<tr>
	<td colspan="4" align='center'>
		<input type="submit" value="      保存      " accesskey="s">　　
		<input type="reset" value="      复位      ">
	</td>
</tr>
</form>
</table>
EOF;
		$this->kclass->page['title'] .= ' - 设置用户权限 - '.$user['username'];
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 设置用户权限 - '.$user['username'], 'right' => $this->right . ' | <a href="/a.php?module=user&action=view&userid='.$user['userid'].'">返回用户信息页</a>', 'body'=>$body));
	}

	//
	function kill(){
		if($this->kclass->input['userid']<=0){
			$this->kclass->boinkIt('/a.php?module=user');
		}
		if($this->kclass->input['confirm'] == 0){
			$this->kclass->boinkIt('/a.php?module=user');
		}
		if($this->kclass->input['userid'] < 0){
			$e = '<li>请从正确的页面进行操作，如果此问题持续存在，请联系网站管理员。';
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '删除用户',
				'text' => '您在删除用户的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$user = $this->kclass->DB->queryFirst("
			SELECT username
			FROM user
			WHERE userid='".$this->kclass->input['userid']."'
		");
		if($user){
			$this->kclass->DB->query("
				UPDATE `user`
				SET killed=1
				WHERE userid='".$this->kclass->input['userid']."'
			");
			$this->kclass->messager(array(
				'title' => '删除用户成功',
				'text' => '用户 <b>'.$user['username'].'</b> 已成功被标记为删除!',
				'url' => '/a.php?module=user',
				'sec' => 3
			));
		}else{
			$this->kclass->boinkIt('/a.php?module=user');
		}
	}

	//
	function remove(){
		if($this->kclass->input['userid']<=0){
			$this->kclass->boinkIt('/a.php?module=user');
		}
		$user = $this->kclass->DB->queryFirst("
			SELECT username
			FROM user
			WHERE userid='".$this->kclass->input['userid']."'
		");
$body = <<<EOF
<table cellspacing="20" align="center">
<form action="/a.php?module=user&action=kill" name="user" method="post">
<input type="hidden" name="module" value="user">
<input type="hidden" name="action" value="kill">
<input type="hidden" name="userid" value="{$this->kclass->input['userid']}">
<tr class="dotted">
	<td class="middle">你确定要删除用户: <b><a href="/a.php?module=user&action=view&userid={$this->kclass->input['userid']}" class="big" target="_blank">{$user['username']}</a></b> 吗?</td>
</tr>
<tr class="solid">
	<td align="center">
		<label for="c1"><input type="radio" id="c1" name="confirm" value="1">是</label>　　
		<label for="c0"><input type="radio" id="c0" name="confirm" value="0" checked>否</label>
	</td>
</tr>
<tr>
	<td align="center">
		<input type="submit" value="   保存   " accesskey="s">
		<input type="reset" value="   复位   ">
	</td>
</tr>
</form>
</table>
EOF;
		$this->kclass->page['title'] .= ' - 删除用户 - '.$user['username'];
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 删除用户 - '.$user['username'], 'right' => $this->right . ' | <a href="/a.php?module=user">返回</a>', 'body'=>$body));
	}

	//
	function revival(){
		if($this->kclass->input['userid']<=0){
			$this->kclass->boinkIt('/a.php?module=user');
		}
		if($this->kclass->input['confirm'] == 0){
			$this->kclass->boinkIt('/a.php?module=user');
		}
		if($this->kclass->input['userid'] < 0){
			$e = '<li>请从正确的页面进行操作，如果此问题持续存在，请联系网站管理员。';
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '恢复用户',
				'text' => '您在恢复用户的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$user = $this->kclass->DB->queryFirst("
			SELECT username
			FROM user
			WHERE userid='".$this->kclass->input['userid']."'
		");
		if($user){
			$this->kclass->DB->query("
				UPDATE `user`
				SET killed=0
				WHERE userid='".$this->kclass->input['userid']."'
			");
			$this->kclass->messager(array(
				'title' => '恢复用户成功',
				'text' => '用户 <b>'.$user['username'].'</b> 已成功被恢复!',
				'url' => '/a.php?module=user',
				'sec' => 3
			));
		}else{
			$this->kclass->boinkIt('/a.php?module=user');
		}
	}

	//
	function restore(){
		if($this->kclass->input['userid']<=0){
			$this->kclass->boinkIt('/a.php?module=user');
		}
		$user = $this->kclass->DB->queryFirst("
			SELECT username
			FROM user
			WHERE userid='".$this->kclass->input['userid']."'
		");
$body = <<<EOF
<table cellspacing="20" align="center">
<form action="/a.php?module=user&action=revival" name="user" method="post">
<input type="hidden" name="module" value="user">
<input type="hidden" name="action" value="revival">
<input type="hidden" name="userid" value="{$this->kclass->input['userid']}">
<tr class="dotted">
	<td class="middle">你确定要恢复用户: <b><a href="/a.php?module=user&action=view&userid={$this->kclass->input['userid']}" class="big" target="_blank">{$user['username']}</a></b> 吗?</td>
</tr>
<tr class="solid">
	<td align="center">
		<label for="c1"><input type="radio" id="c1" name="confirm" value="1">是</label>　　
		<label for="c0"><input type="radio" id="c0" name="confirm" value="0" checked>否</label>
	</td>
</tr>
<tr>
	<td align="center">
		<input type="submit" value="   保存   " accesskey="s">
		<input type="reset" value="   复位   ">
	</td>
</tr>
</form>
</table>
EOF;
		$this->kclass->page['title'] .= ' - 恢复用户 - '.$user['username'];
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 恢复用户 - '.$user['username'], 'right' => $this->right . ' | <a href="/a.php?module=user">返回</a>', 'body'=>$body));
	}

	//
	function find(){
		$group = $this->_gChoose(array());
$body = <<<EOF
<table cellspacing="10" cellspacing="0" border="0" align="center">
<form action="/a.php?module=user" name="user" method="post">
<input type="hidden" name="module" value="user">
<tr class="solid">
	<td>用户组 是</td>
	<td>{$group}</td>
</tr>
<tr class="solid">
	<td>用户名称 包含</td>
	<td><input type="text" name="username" value="" style="width:250px"></td>
</tr>
<tr>
	<td colspan='2' align='center'>
		<input type="submit" value="   查找   " accesskey="s">
		<input type="reset" value="   复位   ">
	</td>
</tr>
</form>
</table>
EOF;
		$this->kclass->page['title'] .= '查找';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 查找', 'right' => $this->right . ' | <a href="/a.php?module=user">返回</a>', 'body'=>$body));
	}
	//
	function modify(){
		$condition = "1=1";
		$cusername = array();
		if($this->kclass->input['groupid'] > 0){
			$condition .= " AND user.groupid = '".$this->kclass->input['groupid']."'";
			$group = $this->kclass->DB->queryFirst("SELECT title FROM `group` WHERE groupid = '".$this->kclass->input['groupid']."'");
			$cusername[] = '用户组是 <b><a href="group&action=view&groupid='.$this->kclass->input['groupid'].'">'.$group['title'].'</a></b>';
		}
		if($this->kclass->input['username'] != ''){
			$condition .= " AND INSTR(LCASE(user.username),'".(strtolower($this->kclass->input['username']))."')>0";
			$cusername[] = '名称包含 <b>'.$this->kclass->input['username'].'</b>';
		}

		$users = $this->kclass->DB->query("
			SELECT user.*,
				`group`.title AS `group`
			FROM user
			LEFT JOIN `group` ON (`group`.groupid = user.groupid)
			WHERE $condition
			ORDER BY userid DESC
		");
		if($this->kclass->DB->numRows()){
			$body = '<table cellspacing="10" align="center">';
			$body .= '<tr class="solid"><td>ID</td><td>用户名称</td><td>所属组</td><td>姓名</td><td>手机</td><td>电话</td><td align="center">注册日期</td><td align="right">上次访问</td><td align="center">最后活动</td><td align="center">操作</td></tr>';
			while($user = $this->kclass->DB->fetchArray($users)){
				$body .= '<tr onmouseover="this.style.backgroundColor=\'#FEEBD1\';" onmouseout="this.style.backgroundColor=\'\';" class="dotted'.$this->kclass->iif($user['killed']==1, ' through', '').'"><td><a name="'.$user['userid'].'"></a>'.$user['userid'].'</td><td><a href="/a.php?module=user&action=view&userid='.$user['userid'].'">'.$user['username'].'</a></td><td width="80"><a href="/a.php?module=user&groupid='.$user['groupid'].'">'.$user['group'].'</a></td><td>'.$user['realname'].'</td><td class="small" username="地址: '.$user['address']."\n".'邮编: '.$user['postalcode'].'">'.$user['mobile'].'</td><td class="small">'.$user['telephone'].'</td><td class="small">'.$this->kclass->iif($user['joindate']>0, date('y-m-d H:i', $user['joindate']), '').'</td><td class="small">'.$this->kclass->iif($user['lastVisit']>0, date('y-m-d H:i', $user['lastVisit']), '').'</td><td class="small">'.$this->kclass->iif($user['lastActivity']>0, date('y-m-d H:i', $user['lastActivity']), '').'</td>';
				if($user['groupid'] > 1){
					if($user['killed']==1){
						$body .= '<td><a href="/a.php?module=user&action=restore&userid='.$user['userid'].'">恢</a></td>';
					}else{
						$body .= '<td align="right"><a href="/a.php?module=user&action=remove&userid='.$user['userid'].'">删</a> <a href="/a.php?module=user&action=update&userid='.$user['userid'].'">改</a> <a href="/a.php?module=user&action=purview&userid='.$user['userid'].'">权</a></td></tr>';
					}
				}else{
					$body .= '<td align="right"><a href="/a.php?module=user&action=update&userid='.$user['userid'].'">改</a></td></tr>';
				}
			}
			$body .= '</table>';
		}else{
			if(count($cusername)>0){
				$body = '未查到满足条件的用户，可尝试更换查找条件重试。';
			}else{
				$body = '暂无。';
			}
		}

		$this->kclass->page['title'] .= '列表';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 列表'.$this->kclass->iif(count($cusername)>0, ' <span class="small">('.implode(', ', $cusername).')</span>', ''), 'right' => $this->right.' | <a href="/a.php?module=user&action=gAdd">新建组</a> <a href="/a.php?module=user&action=add">新建用户</a>', 'body'=>$body));
	}
	//
	function gView(){
		if($this->kclass->input['groupid']<=0 OR !$group = $this->kclass->DB->queryFirst("SELECT `group`.*, u1.username AS creator, u2.username AS modifier FROM `group` LEFT JOIN `user` AS u1 ON (u1.userid=`group`.creator) LEFT JOIN `user` AS u2 ON (u2.userid=`group`.modifier) WHERE `group`.groupid='".$this->kclass->input['groupid']."'")){
			$this->kclass->boinkIt('/a.php?module=user&action=group');
		}
		$modified = date('Y-m-d H:i:s', $group['modified']);
		$created = date('Y-m-d H:i:s', $group['created']);
		$purviews = $this->_showPurviews($group['purviews']);
		$menu = $this->_menu($group['purviews']);
$body = <<<EOF
<div id="userinfo">
	<ul class="tabs">
		<li><a><span class="darkred">{$group['title']}</span> 组资料</a></li>
		<li><a>统计<span class="tiny">(0)</span></a></li>
	</ul>
	<div class="tabText">
		<table cellspacing="10" align="center">
		<tr>
			<td valign="top">
			<fieldset>
				<legend>用户组</legend>
				<table cellspacing="10" width="100%">
				<tr class="dotted">
					<td>名称：</td>
					<td class="bold">{$group['title']}</td>
					<td>备注：</td>
					<td class="bold">{$group['remark']}</td>
				</tr>
				<tr class="dotted">
					<td>建立时间：</td>
					<td class="bold">{$created}</td>
					<td>修改时间：</td>
					<td class="small">{$modified}</td>
				</tr>
				<tr class="dotted">
					<td>建立者：</td>
					<td class="small">{$group['creator']}</td>
					<td>修改者：</td>
					<td class="small">{$group['modifier']}</td>
				</tr>
				</table>
			</fieldset>
			</td>
		</tr>
		<tr>
			<td valign="top">
			<fieldset>
				<legend>权限</legend>
		{$purviews}
			</fieldset>
			</td>
		</tr>
		<tr>
			<td valign="top">
			<fieldset>
				<legend>菜单</legend>
		{$menu}
			</fieldset>
			</td>
		</tr>
		</table>
	</div>
	<div class="tabText">
		暂无。
	</div>
</div>
EOF;
		$this->kclass->page['onload'] = "dc.tabs.init({'id':'userinfo'});";
		$this->kclass->page['title'] .= ' - 用户组信息 - '.$group['title'];
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl.' - 用户组信息 - '.$group['title'], 'right' => $this->right.' |  <a href="/a.php?module=user&action=gRemove&userid='.$user['userid'].'">删除</a> <a href="/a.php?module=user&action=gUpdate&groupid='.$group['groupid'].'">修改与权限</a>', 'body'=>$body));
	}
	//
	function gInsert(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			if($this->kclass->input['title'] == ''){
				$e = '<li>请输入用户组的名称。</li>';
			}
		}

		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '新建用户信息',
				'text' => '新建用户信息不成功：<ul>'.$e.'</li>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}

		$i=0;
		foreach($this->kclass->input as $k => $v){
			if(substr_count($k, 'purviews') == 1){
				foreach($v as $val){
					$p .= $val.',';
					$i++;
				}
			}
		}
		if($p != ''){
			$p = trim($p, ',');
		}

		$this->kclass->DB->query("
			INSERT INTO `group` (`parentid`, `title` , `remark` , `purviews` , `pcount` , `creator` , `created`)
			VALUES (
			'".$this->kclass->input['parentid']."', '".$this->kclass->input['title']."', '".$this->kclass->input['remark']."', '".$p."', '".$i."', '".$this->kclass->member['userid']."', '".TIMENOW."'
			)
		");
		$groupid = $this->kclass->DB->insertID();

		$this->kclass->messager(array(
			'title' => '新建用户组信息',
			'text' => '用户组 <b>'.$this->kclass->input['groupname'].'</b> 的信息已成功建立!'.$pcm,
			'url' => '/a.php?module=user&action=gView&groupid='.$this->kclass->input['groupid'],
			'sec' => 3
		));
	}

	//
	function gAdd(){
		if($this->kclass->input['groupid']>0 AND $group = $this->kclass->DB->queryFirst("SELECT `group`.*, p.title AS parent FROM `group` LEFT JOIN `group` AS p ON (p.groupid=`group`.parentid) WHERE `group`.groupid='".$this->kclass->input['parentid']."'")){
			$this->kclass->input['parentid'] = 0;
		}
		$parent = $this->_gChoose(array('name'=>'parentid', 'id'=>$this->kclass->input['parentid']));
		$purviews = $this->_purviews($group['purviews']);

$body = <<<EOF
<table cellspacing="10" align="center">
<form action="/a.php?module=user&action=gInsert" id="purviews" name="group" method="post">
<input type="hidden" name="module" value="user">
<input type="hidden" name="action" value="gInsert">
<tr class="dotted">
	<td>上级　组：</td>
	<td>{$parent} <span class="small gray">不选，则新建为最高级别用户组。</span></td>
</tr>
<tr class="dotted">
	<td>用户名称：<span class="red bold">*</span></td>
	<td><input type="text" style="width:250px" name="title" value="{$group['title']}"> <span class="small gray">组名称不能少于2个字。</span></td>
</tr>
<tr class="dotted">
	<td>备　　注：</td>
	<td><input type="text" style="width:250px" name="remark" value="{$group['remark']}"> <span class="small gray">简要说明，不超过255个字符。</span></td>
</tr>
<tr class="dotted">
	<td colspan="2">权限选择： <span class="small gray">针对本组中具体用户未设置<span class="bold">不继承组权限</span>的用户。</span></td>
</tr>
<tr>
	<td colspan="2">{$purviews}</td>
</tr>
<tr class="solid">
	<td colspan="2"></td>
</tr>
<tr>
	<td colspan='2' align='center'>
		<input type="submit" value="   保存   " accesskey="s">
		<input type="reset" value="   复位   ">
	</td>
</tr>
</form>
</table>
EOF;
		$this->kclass->page['title'] .= ' - 新建组';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 新建组', 'right' => $this->right . ' | <a href="/a.php?module=user&action=group">返回列表</a>', 'body'=>$body));
}

	//
	function gDoupdate(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			$group=$this->kclass->DB->queryFirst("SELECT `groupid` FROM `group` WHERE groupid='".$this->kclass->input['groupid']."'");
			if($group['groupid'] == 1){
				$e = '<li>您不可以编辑系统管理用户的权限（系统管理用户没有使用业务系统的权限）。</li>';
			}
			if($this->kclass->input['title'] == ''){
				$e .= '<li>请输入用户组的名称。</li>';
			}
		}

		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '修改用户信息',
				'text' => '修改用户信息不成功：<ul>'.$e.'</li>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}

		$i=0;
		foreach($this->kclass->input as $k => $v){
			if(substr_count($k, 'purviews') == 1){
				foreach($v as $val){
					$p .= $val.',';
					$i++;
				}
			}
		}
		if($p != ''){
			$p = trim($p, ',');
		}
		$this->kclass->DB->query("
			UPDATE `group`
			SET `title` = '".$this->kclass->input['title']."',
				`parentid` = '".$this->kclass->input['parentid']."',
				`remark` = '".$this->kclass->input['remark']."',
				`purviews` = '".$p."',
				`pcount` = '".$i."',
				`modified` = '".TIMENOW."',
				`modifier` = '".$this->kclass->user['userid']."'
			WHERE groupid='".$this->kclass->input['groupid']."'
		");

		$this->kclass->messager(array(
			'title' => '修改用户组信息',
			'text' => '用户组 <b>'.$this->kclass->input['groupname'].'</b> 的信息已成功修改!'.$pcm,
			'url' => '/a.php?module=user&action=gView&groupid='.$this->kclass->input['groupid'],
			'sec' => 3
		));
	}

	//
	function gUpdate(){
		if($this->kclass->input['groupid']<=0 OR !$group = $this->kclass->DB->queryFirst("SELECT `group`.*, p.title AS parent FROM `group` LEFT JOIN `group` AS p ON (p.groupid=`group`.parentid) WHERE `group`.groupid='".$this->kclass->input['groupid']."'")){
			$this->kclass->boinkIt('/a.php?module=user&action=group');
		}
		$parent = $this->_gChoose(array('name'=>'parentid', 'id'=>$group['parentid'], 'condition'=>" AND g1.`groupid`<>'".$group['groupid']."'"));
		$purviews = $this->_purviews($group['purviews']);

$body = <<<EOF
<table cellspacing="10" align="center">
<form action="/a.php?module=user&action=gDoupdate" id="purviews" name="group" method="post">
<input type="hidden" name="module" value="user">
<input type="hidden" name="action" value="gDoupdate">
<input type="hidden" name="groupid" value="{$this->kclass->input['groupid']}">
<tr class="dotted">
	<td>上级　组：</td>
	<td>{$parent} <span class="small gray">留空表示是最上级组。</span></td>
</tr>
<tr class="dotted">
	<td>用户名称：<span class="red bold">*</span></td>
	<td><input type="text" style="width:250px" name="title" value="{$group['title']}"> <span class="small gray">组名称不能少于2个字。</span></td>
</tr>
<tr class="dotted">
	<td>备　　注：</td>
	<td><input type="text" style="width:250px" name="remark" value="{$group['remark']}"> <span class="small gray">简要说明，不超过255个字符。</span></td>
</tr>
<tr class="dotted">
	<td colspan="2">权限选择： <span class="small gray">只对未设置<span class="bold">不继承组权限</span>的用户。</span></td>
</tr>
<tr>
	<td colspan="2">{$purviews}</td>
</tr>
<tr class="solid">
	<td colspan="2"></td>
</tr>
<tr>
	<td colspan='2' align='center'>
		<input type="submit" value="   保存   " accesskey="s">
		<input type="reset" value="   复位   ">
	</td>
</tr>
</form>
</table>
EOF;
		$this->kclass->page['title'] .= ' - 修改组 - '.$group['title'];
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 修改组 - '.$group['title'], 'right' => $this->right . ' | <a href="/a.php?module=user&action=group">返回列表</a>', 'body'=>$body));
	}

	//
	function gKill(){
		if($this->kclass->input['groupid']<=0){
			$this->kclass->boinkIt('/a.php?module=user&action=group');
		}
		if($this->kclass->input['confirm'] == 0){
			$this->kclass->boinkIt('/a.php?module=user&action=group');
		}
		if($this->kclass->input['groupid'] < 0){
			$e = '<li>请从正确的页面进行操作，如果此问题持续存在，请联系网站管理员。';
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '删除组',
				'text' => '您在删除组的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$group = $this->kclass->DB->queryFirst("
			SELECT groupname
			FROM group
			WHERE groupid='".$this->kclass->input['groupid']."'
		");
		if($group){
			$this->kclass->DB->query("
				UPDATE `group`
				SET killed=1
				WHERE groupid='".$this->kclass->input['groupid']."'
			");
			$this->kclass->messager(array(
				'title' => '删除组成功',
				'text' => '组 <b>'.$group['groupname'].'</b> 已成功被标记为删除!',
				'url' => '/a.php?module=user&action=group',
				'sec' => 3
			));
		}else{
			$this->kclass->boinkIt('/a.php?module=user&action=group');
		}
	}

	//
	function gRemove(){
		if($this->kclass->input['groupid']<=0){
			$this->kclass->boinkIt('/a.php?module=user&action=group');
		}
		$group = $this->kclass->DB->queryFirst("
			SELECT groupname
			FROM group
			WHERE groupid='".$this->kclass->input['groupid']."'
		");
$body = <<<EOF
<table cellspacing="20" align="center">
<form action="/a.php?module=user&action=gKill" name="group" method="post">
<input type="hidden" name="module" value="user">
<input type="hidden" name="action" value="gKill">
<input type="hidden" name="groupid" value="{$this->kclass->input['groupid']}">
<tr class="dotted">
	<td class="middle">你确定要删除组: <b><a href="/a.php?module=user&action=gView&groupid={$this->kclass->input['groupid']}" class="big" target="_blank">{$group['groupname']}</a></b> 吗?</td>
</tr>
<tr class="solid">
	<td align="center">
		<label for="c1"><input type="radio" id="c1" name="confirm" value="1">是</label>　　
		<label for="c0"><input type="radio" id="c0" name="confirm" value="0" checked>否</label>
	</td>
</tr>
<tr>
	<td align="center">
		<input type="submit" value="   保存   " accesskey="s">
		<input type="reset" value="   复位   ">
	</td>
</tr>
</form>
</table>
EOF;
		$this->kclass->page['title'] .= ' - 删除组 - '.$group['title'];
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 删除组 - '.$group['title'], 'right' => $this->right . ' | <a href="/a.php?module=user&action=group">返回</a>', 'body'=>$body));
	}

	//
	function gRevival(){
		if($this->kclass->input['groupid']<=0){
			$this->kclass->boinkIt('/a.php?module=user&action=group');
		}
		if($this->kclass->input['confirm'] == 0){
			$this->kclass->boinkIt('/a.php?module=user&action=group');
		}
		if($this->kclass->input['groupid'] < 0){
			$e = '<li>请从正确的页面进行操作，如果此问题持续存在，请联系网站管理员。';
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '恢复组',
				'text' => '您在恢复组的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$group = $this->kclass->DB->queryFirst("
			SELECT groupname
			FROM group
			WHERE groupid='".$this->kclass->input['groupid']."'
		");
		if($group){
			$this->kclass->DB->query("
				UPDATE `group`
				SET killed=0
				WHERE groupid='".$this->kclass->input['groupid']."'
			");
			$this->kclass->messager(array(
				'title' => '恢复组成功',
				'text' => '组 <b>'.$group['groupname'].'</b> 已成功被恢复!',
				'url' => '/a.php?module=user&action=group',
				'sec' => 3
			));
		}else{
			$this->kclass->boinkIt('/a.php?module=user&action=group');
		}
	}

	//
	function gRestore(){
		if($this->kclass->input['groupid']<=0){
			$this->kclass->boinkIt('/a.php?module=user&action=group');
		}
		$group = $this->kclass->DB->queryFirst("
			SELECT groupname
			FROM group
			WHERE groupid='".$this->kclass->input['groupid']."'
		");
$body = <<<EOF
<table cellspacing="20" align="center">
<form action="/a.php?module=user&action=revival" name="group" method="post">
<input type="hidden" name="module" value="user">
<input type="hidden" name="action" value="gRevival">
<input type="hidden" name="groupid" value="{$this->kclass->input['groupid']}">
<tr class="dotted">
	<td class="middle">你确定要恢复组: <b><a href="/a.php?module=user&action=gView&groupid={$this->kclass->input['groupid']}" class="big" target="_blank">{$group['groupname']}</a></b> 吗?</td>
</tr>
<tr class="solid">
	<td align="center">
		<label for="c1"><input type="radio" id="c1" name="confirm" value="1">是</label>　　
		<label for="c0"><input type="radio" id="c0" name="confirm" value="0" checked>否</label>
	</td>
</tr>
<tr>
	<td align="center">
		<input type="submit" value="   保存   " accesskey="s">
		<input type="reset" value="   复位   ">
	</td>
</tr>
</form>
</table>
EOF;
		$this->kclass->page['title'] .= ' - 恢复组 - '.$group['title'];
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 恢复组 - '.$group['title'], 'right' => $this->right . ' | <a href="/a.php?module=user&action=group">返回</a>', 'body'=>$body));
	}

	//
	function group(){
		$groups = $this->kclass->DB->query("
			SELECT g1.groupid AS g1id, g1.title AS parent, g1.created AS g1created, g1.modified AS g1modified, g1.remark AS g1remark,  g1.pcount AS g1pcount, 
				g2 . * 
			FROM `group` AS g1
			LEFT JOIN `group` AS g2 ON (g2.parentid=g1.groupid) 
			WHERE g1.parentid=0 AND g1.groupid>1
			ORDER BY g1.groupid ASC, g2.groupid ASC 
		");
		if($this->kclass->DB->numRows()){
			$body = '<table cellspacing="10" align="center">';
			$body .= '<tr class="solid"><td>组名称<span class="small gray">(权限数)</span></td><td>备注</td><td>建立日期</td><td>修改日期</td><td>成员数</td><td align="center">操作</td></tr>';
			while($group = $this->kclass->DB->fetchArray($groups)){
				if($parent != $group['parent']){
					$parent = $group['parent'];
					$body .= '<tr class="dotted" onmouseover="this.style.backgroundColor=\'#FEEBD1\';" onmouseout="this.style.backgroundColor=\'\';"><td class="bold"><a name="'.$group['g1id'].'"></a><a href="/a.php?module=user&action=gView&groupid='.$group['g1id'].'">'.$group['parent'].'</a><span class="small">('.$group['g1pcount'].')</span></td><td class="small">'.$group['g1remark'].'</td><td class="small">'.date('y-m-d H:i', $group['g1created']).'</td><td class="small">'.date('y-m-d H:i', $group['g1modified']).'</td><td>'.$group['counter'].'</td><td align="right"><a href="/a.php?module=user&action=gAdd&parentid='.$group['g1id'].'" class="small">建下级组</a> | <a href="/a.php?module=user&action=add&groupid='.$group['g1id'].'" class="small">添加用户</a> | <a href="/a.php?module=user&action=gUpdate&groupid='.$group['g1id'].'">修改与权限</a></td></tr>';
				}
				if($group['created'] > 0){
					$body .= '<tr class="dotted" onmouseover="this.style.backgroundColor=\'#FEEBD1\';" onmouseout="this.style.backgroundColor=\'\';"><td>　　<a name="'.$group['groupid'].'"></a><a href="/a.php?module=user&action=gView&groupid='.$group['groupid'].'">'.$group['title'].'</a><span class="small">('.$group['pcount'].')</span></td><td class="small">'.$group['remark'].'</td><td class="small">'.date('y-m-d H:i', $group['created']).'</td><td class="small">'.date('y-m-d H:i', $group['modified']).'</td><td>'.$group['counter'].'</td><td align="right"><a href="/a.php?module=user&action=add&groupid='.$group['groupid'].'" class="small">添加用户</a> | <a href="/a.php?module=user&action=gUpdate&groupid='.$group['groupid'].'">修改与权限</a></td></tr>';
				}
			}
			$body .= '</table>';
		}else{
			$body = '暂无。';
		}

		$this->kclass->page['title'] .= ' - 用户组';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - <a href="/a.php?module=user&action=group">用户组</a>', 'right' => $this->right.' | <a href="/a.php?module=user&action=add">新建用户</a> <a href="/a.php?module=user&action=gAdd">新建组</a>', 'body'=>$body));
	}
	// 
	function _gChoose($c){
		if($c['name'] == ''){
			$c['name'] = 'groupid';
		}
		if($c['width'] == ''){
			$c['width'] = 200;
		}
		if($c['first'] == ''){
			$c['first'] = '根';
		}
		if($c['firstid'] == ''){
			$c['firstid'] = 0;
		}
		if($c['id'] == ''){
			$c['id'] = 0;
		}

		$gs = $this->kclass->DB->query("
			SELECT g1.groupid AS g1id, g1.title AS parent, g1.created AS g1created, g1.modified AS g1modified, g1.remark AS g1remark, 
				g2 . * 
			FROM `group` AS g1
			LEFT JOIN `group` AS g2 ON (g2.parentid=g1.groupid) 
			WHERE g1.parentid=0 AND g1.groupid>1".$c['condition']."
			ORDER BY g1.groupid ASC, g2.groupid ASC 
		");
		if($this->kclass->DB->numRows()){
			$s = '<select name="'.$c['name'].'" style="width:'.$c['width'].'px"><option value="'.$c['firstid'].'">'.$c['first'].'</option>';
			while($g = $this->kclass->DB->fetchArray($gs)){
				if($parent != $g['parent']){
					$parent = $g['parent'];
					$s .= '<option value="'.$g['g1id'].'"'.$this->kclass->iif($g['g1id']==$c['id'], ' selected', '').'>'.$g['parent'].'</option>';
				}
				if($g['created'] > 0){
					$s .= '<option value="'.$g['groupid'].'"'.$this->kclass->iif($g['groupid']==$c['id'], ' selected', '').'>　　'.$g['title'].'</option>';
				}
			}
			$s .= '</select>';
		}
		return $s;
	}
	// 
	function _purviews($p){
		$ped = array();
		if($p != ''){
			$ped = explode(',', $p);
		}
		$ms = $this->kclass->DB->query("
			SELECT m1.moduleid AS m1id, m1.title AS parent, m1.remark AS m1remark, m1.created AS m1created, m1.modified AS m1modified, m1.ordering AS m1ordering, 
				m2 . * 
			FROM `module` AS m1
			LEFT JOIN `module` AS m2 ON (m2.parentid=m1.moduleid) 
			WHERE m1.parentid=0 AND m1.killed=0 AND m2.killed=0
			ORDER BY m1.ordering ASC, m2.ordering ASC, m2.moduleid ASC, m1.moduleid ASC 
		");
		if($this->kclass->DB->numRows()){
			while($m = $this->kclass->DB->fetchArray($ms)){
				if($parent != $m['parent']){
					$parent = $m['parent'];
					$t .= '<tr class="double" onmouseover="this.style.backgroundColor=\'#FEEBD1\';" onmouseout="this.style.backgroundColor=\'\';"><td colspan="2" class="bold middle"><a name="'.$m['m1id'].'"></a>'.$m['parent'].'</td><td class="tiny gray">'.$m['m1remark'].'</td><td class="small gray">'.date('y-m-d H:i', $m['m1created']).'</td><td><label for="m'.$m['m1id'].'" class="small gray"><input type="checkbox" id="m'.$m['m1id'].'" value="'.$m['m1id'].'" onclick="dc.user.groupSelect('.$m['m1id'].')" />反选</label></td></tr>';
				}
				if($m['moduleid'] > 0){
					$t .= '<tr class="dotted" onmouseover="this.style.backgroundColor=\'#FEEBD1\';" onmouseout="this.style.backgroundColor=\'\';"><td>'.$m['moduleid'].'.</td><td>'.$m['title'].'</td><td class="small gray">'.$m['remark'].'</td><td class="small gray">'.date('y-m-d H:i', $m['m1created']).'</td><td align="right"><input type="checkbox" name="purviews'.$m['m1id'].'[]" value="'.$m['moduleid'].'"'.$this->kclass->iif(in_array($m['moduleid'], $ped), ' checked', '').' /></td></tr>';
				}
			}
		}
		return '<table cellspacing="5" align="center">'.$t.'</table>';
	}
	// 
	function _showPurviews($p){
		if($p == '')return;
		$ms = $this->kclass->DB->query("
			SELECT m1.moduleid, m1.title, m1.remark, m1.parentid, m1.killed, 
				m2.title AS parent, m2.killed AS m2killed
			FROM `module` AS m1
			LEFT JOIN `module` AS m2 ON (m2.moduleid=m1.parentid) 
			WHERE m1.moduleid IN (".$p.")
			ORDER BY m1.ordering ASC, m2.ordering ASC, m2.moduleid ASC, m1.moduleid ASC 
		");
		if($this->kclass->DB->numRows()){
			$pid = 0;
			$t = '';
			while($m = $this->kclass->DB->fetchArray($ms)){
				if($pid != $m['parentid']){
					if($pid > 0)$t .= '</td></tr>';
					$t .= '<tr class="dotted'.$this->kclass->iif($m['m2killed']==1, ' through', '').'"><td class="bold">'.$m['parent'].'</td></tr><tr><td>';
					$pid = $m['parentid'];
				}
				$t .= '<div class="left'.$this->kclass->iif($m['m2killed']==1 OR $m['killed']==1, ' through', '').'" style="width:250px;height:20px">'.$m['title'].''.$this->kclass->iif($m['remark']!='', '<span class="gray tiny">('.$m['remark'].')</span>', '').'</div>';
			}
			return '<table cellspacing="10">'.$t.'</td></tr></table>';
		}else{
			return;
		}
	}
	// 
	function _log($l){
		if($l['userid'] == '')return;
		if($l['condition'] == '')$l['condition']="`log`.userid IN (".$l['userid'].")";
		if($l['limit'] == '')$l['limit']="50";
		$r = $this->kclass->DB->queryFirst("SELECT COUNT(userid) AS total, `log`.* FROM `log` WHERE userid IN (".$l['userid'].")");
		if($l['limit']>$r['total'])$l['limit'] = $r['total'];
		$gs = $this->kclass->DB->query("
			SELECT `log`.*, 
				user.username
			FROM `log`
			LEFT JOIN user ON (user.userid=`log`.userid) 
			WHERE ".$l['condition']."
			ORDER BY dateline DESC, `log`.logid ASC
			LIMIT 0,".$l['limit']."
		");
		if($this->kclass->DB->numRows()){
			$area = array('a'=>'后台管理', 's'=>'业务系统', 'w'=>'网站');
			$r['tab'] = '<div class="center small">最新'.$l['limit'].'条，查看全部转至 <a href="/a.php?module=logs">日志管理</a>。</div><table cellspacing="5" width="100%" align="center"><tr class="solid"><td>ID</td><td>标题</td><td>范围</td><td>模块</td><td>动作</td><td align="center">id</td><td align="center">IP</td><td align="center">时间</td><td align="center">用户</td></tr>';
			while($g = $this->kclass->DB->fetchArray($gs)){
				$r['tab'] .= '<tr class="dotted"><td class="small"><a href="/a.php?module=logs&logid='.$g['logid'].'">'.$g['logid'].'</a></td><td>'.$g['title'].'</td><td class="small">'.$area[$g['area']].'</td><td>'.$g['module'].'</td><td>'.$g['action'].'</td><td align="right">'.$g['id'].'</td><td class="small" align="center">'.$g['ip'].'</td><td class="small" align="center">'.date('Y-m-d H:i:s', $g['dateline']).'</td><td align="right"><a href="/a.php?module=user&action=view&userid='.$g['userid'].'">'.$g['username'].'</a></td></tr>';
			}
			$r['tab'] .= '</table>';
		}
		return $r;
	}
	// 
	function _menu($g){
		if($g=='')return;
		$ms = $this->kclass->DB->query("
			SELECT m1.menuid, m1.parentid, m1.moduleid, m1.title, m1.icon, m1.`module`, m1.`action`, m1.`addition`, 
				m2.title AS parent
			FROM `menu` AS m1
			LEFT JOIN `menu` AS m2 ON (m2.menuid = m1.parentid)
			WHERE m1.killed=0 AND m1.`moduleid` IN (".$g.")
			ORDER BY m2.ordering ASC , m1.ordering ASC
		");
		if($this->kclass->DB->numRows()){
			$pid = -1;
			$u = '<ul id="menu">';
			while($m = $this->kclass->DB->fetchArray($ms)){
				if($pid != $m['parentid']){
					if($pid>0)$u .= '</ul></li>';//if has sub add ul/li tail
					$u .= '<li>'.$this->kclass->iif($m['parentid']==0, $m['title'].'</li>', $m['parent'].'<ul>');
					$pid = $m['parentid'];
				}
				if($m['parentid'] > 0){
					$u .= '<li>';
					if($m['icon']!='')$u='<img src="/public/menu/'.$m['icon'].'" width="16" height="16" />';// if icon is true
					if($m['module'] != ''){//if url is true
						$u .= '<a href="/s.php?module='.$m['module'];
						if($m['action'] != '')$u.='&action='.$m['action'];
						if($m['addition']!='')$u.='&'.$m['addition'];
						$u.='">';
					}
					$u .= $m['title'];
					if($m['module'] != '')$u.='</a>';
					$u .= '</li>';
				}
			}
			if($pid > 0)$u .= '</ul></li>';
			$u .= '</ul>';
		}
		return $u;
	}
}


?>