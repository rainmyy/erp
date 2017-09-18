<?php
// 
class home{
	var $kclass;# Global
	// Auto run function
	function autoRun(){
		switch($this->kclass->input['do']){
			case 'list':
				$this->index();
				break;
			case 'get':
				$this->get();
				break;

			default:
				$this->index();
		}
	}

	// 
	function index(){
		$this->kclass->page['title'] = '首页';
		$dbs = $this->kclass->DB->query("SHOW TABLE STATUS");
		while($db = $this->kclass->DB->fetchArray($dbs)){
			if($db['Name'] == 'log'){
				$sizeLog = $db['Data_length'] + $db['Index_length'];
			}
			$sizeDB += $db['Data_length'] + $db['Index_length'];
		}

//, 附件图片: '.$this->kclass->size_format($this->kclass->folder_size($this->kclass->vars['user_file_path']))

$tabFinduser = <<<EOF
<table cellpadding="2" align="center">
<tr>
	<td><b>用户</b>：</td>
	<td><form method="post" action="/a.php?module=user" onsubmit="return dc.home.user(this);">
	<input type="hidden" name="module" value="user">
	<input type="hidden" name="action" value="list">
	<input type="text" name="username" value="用户名称" onmouseover="if(this.value=='用户名称'){this.value='';}this.focus()" onmouseout="if(this.value==''){this.value='用户名称';}"><input type="submit" value="Go">
	</form>
	</td>
</tr>
</table>
EOF;
		$body = $this->kclass->skin->fieldset(array('title' => '快速查找', 'content' => $tabFinduser));
		// user
		$users = $this->kclass->DB->query("
			SELECT COUNT(DISTINCT `user`.userid) AS counter, `user`.joindate, 
				`user`.userid, `user`.username, `user`.groupid, 
				`group`.title AS `group`
			FROM `user`
			LEFT JOIN `group` ON (`group`.groupid = `user`.groupid)
			GROUP BY `group`
			ORDER BY `group`.groupid ASC
		");
		if($this->kclass->DB->numRows()){
			$tabUser = '<table cellspacing="10"><tr class="solid"><td>用户组</td><td>最新成员</td><td align="center">新员日期</td><td align="right">人数</td></tr>';
			while($user = $this->kclass->DB->fetchArray($users)){
				$tabUser .= '<tr class="dotted"><td><a href="/a.php?module=group&action=view&groupid='.$user['groupid'].'">'.$user['group'].'</a></td><td><a href="/a.php?module=user&action=view&userid='.$user['userid'].'">' . $user['username'] . '</a></td><td align="center" class="small">'.date('Y-m-d H:i:s', $user['joindate']).'</td><td align="right">'.$user['counter'].'</td></tr>';
				$userTotal += $user['counter'];
			}
			$tabUser .= '</table>';
			$body .= $this->kclass->skin->fieldset(array('title' => '用户(总数: '.$userTotal.' 人)', 'content' => $tabUser.$tabGroup));
		}
		$this->kclass->page['main'] .= $this->kclass->skin->dbody(array('title' => '首页', 'right' => '数据库: '.$this->kclass->sizeFormat($sizeDB).', 操作日志: '.$this->kclass->sizeFormat($sizeLog).'.', 'body'=>$body));
	}
}


?>