<?php
class gender{
	function autoRun(){
		$this->kclass->page['title'] = '性别';
		$this->baseurl = '<a href="/s.php">首页</a> - <a href="/s.php?module=gender">性别</a>';
		$this->right = '<li><a href="/s.php?module=gender">列表</a></li><li><a href="/s.php?module=gender&action=add">新建</a></li>';
		switch($this->kclass->input['action']){
			case 'list':
				$this->mmlist();
			break;
			case 'view':
				$this->view();
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
			case 'updateAll':
				$this->updateAll();
			break;
			case 'doupdateAll':
				$this->doupdateAll();
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
			default:
				$this->mmlist();
		}
	}
	//
	function insert(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			if($this->kclass->input['title'] == ''){
				$e = '<li>请填写性别的 名称。</li>';
			}else{
				if($this->kclass->DB->queryFirst("SELECT genderid FROM `gender` WHERE `title`='".$this->kclass->input['title']."'")){
					$e = '<li>您要新建的性别 【'.$this->kclass->input['title'].'】 已经存在。</li>';
				}
			}
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '新建性别',
				'text' => '您在新建性别的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$this->kclass->DB->query("
			INSERT INTO `gender` ( `title` ,   `entitle` , `remark` , `ordering` , `modified`, `creator` , `created`)
			VALUES (
			'".$this->kclass->input['title']."', '".$this->kclass->input['entitle']."', '".$this->kclass->input['remark']."', '".$this->kclass->input['ordering']."', '".TIMENOW."', '".$this->kclass->user['userid']."', '".TIMENOW."'
			)
		");

		$this->kclass->messager(array(
			'title' => '新建性别',
			'text' => '性别 <b>'.$this->kclass->input['title'].'</b> 已新建成功!',
			'url' => '/s.php?module=gender#'.$this->kclass->DB->insertID(),
			'sec' => 2
		));
	}

	//
	function add(){
$body = <<<EOF
<form action="/s.php?module=gender&action=insert" name="gender" method="post">
<input type="hidden" name="module" value="gender">
<input type="hidden" name="action" value="insert">
<table>
<thead>
<tr>
	<th colspan="4">新建性别：</th>
</tr>
</thead>
<tbody>
<tr class="odd">
	<td>名称<span class="small">(中文)</span>：<span class="red bold">*</span></td>
	<td><input type="text" style="width:200px" size="26" name="title" value="{$gender['title']}"></td>
</tr>
<tr class="even">
	<td>名称<span class="small">(英文)</span>：</td>
	<td><input type="text" style="width:200px" size="26" name="entitle" value="{$gender['entitle']}"></td>
</tr>
<tr class="odd">
	<td>备　　注：</td>
	<td><input type="text" style="width:200px" size="26" name="remark" value="{$gender['remark']}"></td>
</tr>
<tr class="even">
	<td>排　　序：</td>
	<td><input type="text" style="width:200px" size="26" name="ordering" value="1"></td>
</tr>
<tr class="odd">
	<td align="center" colspan="4">
		<input type="submit" id="submitButton" value="    保存    " accesskey="s">　　
		<input type="reset" value="    复位    ">
	</td>
</tr>
</tbody>
</table>
</form>
EOF;
		$this->kclass->page['onload'] = 'dc.tabhover()';
		$this->kclass->page['title'] .= ' - 新建性别';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 新建性别', 'right' => '<a href="/s.php?module=gender">返回列表</a>', 'body'=>$body));
}

	//
	function doupdate(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			if($this->kclass->input['title'] == ''){
				$e = '<li>请填写性别的 名称。</li>';
			}else{
				if($gender = $this->kclass->DB->queryFirst("SELECT genderid FROM `gender` WHERE `genderid`<>'".$this->kclass->input['genderid']."' AND `title`='".$this->kclass->input['title']."'")){
					$e = '<li>您要修改的性别 【'.$this->kclass->input['title'].'】 已经存在。</li>';
				}
			}
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '修改性别',
				'text' => '您在修改性别的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$this->kclass->DB->query("
			UPDATE `gender`
			SET `title` = '".$this->kclass->input['title']."', 
				`entitle` = '".$this->kclass->input['entitle']."', 
				`remark` = '".$this->kclass->input['remark']."', 
				`ordering` = '".$this->kclass->input['ordering']."', 
				`modified` = '".TIMENOW."', 
				`modifier` = '".$this->kclass->user['userid']."'
			WHERE genderid='".$this->kclass->input['genderid']."'
		");
		$this->kclass->messager(array(
			'title' => '修改性别',
			'text' => '性别 <b>'.$this->kclass->input['title'].'</b> 的信息已成功修改!',
			'url' => '/s.php?module=gender#'.$this->kclass->input['genderid'],
			'sec' => 2
		));
	}

	//
	function update(){
		if($this->kclass->input['genderid']<=0 OR !$gender = $this->kclass->DB->queryFirst("SELECT `gender`.* FROM `gender` WHERE `genderid`='".$this->kclass->input['genderid']."'")){
			$this->kclass->boinkIt('/s.php?module=gender');
		}
$body = <<<EOF
<form action="/s.php?module=gender&action=doupdate" name="gender" method="post">
<input type="hidden" name="module" value="gender">
<input type="hidden" name="action" value="doupdate">
<input type="hidden" name="genderid" value="{$this->kclass->input['genderid']}">
<table>
<thead>
<tr>
	<th colspan="2">性别修改：{$gender['title']}</th>
</tr>
</thead>
<tbody>
<tr class="odd">
	<td>名称<span class="small">(中文)</span>：<span class="red bold">*</span></td>
	<td><input type="text" style="width:200px" size="26" name="title" value="{$gender['title']}"></td>
</tr>
<tr class="even">
	<td>名称<span class="small">(英文)</span>：</td>
	<td><input type="text" style="width:200px" size="26" name="entitle" value="{$gender['entitle']}"></td>
</tr>
<tr class="odd">
	<td>备　　注：</td>
	<td><input type="text" style="width:200px" size="26" name="remark" value="{$gender['remark']}"></td>
</tr>
<tr class="even">
	<td>排　　序：</td>
	<td><input type="text" style="width:200px" size="26" name="ordering" value="{$gender['ordering']}"></td>
</tr>
<tr class="odd">
	<td align="center" colspan="2">
		<input type="submit" id="submitButton" value="    保存    " accesskey="s">　　
		<input type="reset" value="    复位    ">
	</td>
</tr>
</tbody>
</table>
</form>
EOF;
		$this->kclass->page['onload'] = 'dc.tabhover()';
		$this->kclass->page['title'] .= ' - 修改性别 - '.$gender['title'];
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 修改性别 - '.$gender['title'], 'right' => '<a href="/s.php?module=gender">返回列表</a>', 'body'=>$body));
	}
	//
	function doupdateAll(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			if(implode('', $this->kclass->input['title']) == ''){
				$e = '<li>请填写性别的 名称，不能留空。</li>';
			}else{
				$a = count($this->kclass->input['title']);
				$b = count(array_unique($this->kclass->input['title']));
				if($a != $b){
					$e = '<li>您修改后的 名称 有重复项目，不能有相同 名称。</li>';
				}
			}
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '修改性别',
				'text' => '您在修改性别的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		foreach($this->kclass->input['orgTitle'] as $k=>$v){
			$s='';
			if($this->kclass->input['orgTitle'][$k] != $this->kclass->input['title'][$k]){
				$s = "`title`='".$this->kclass->input['title'][$k]."', ";
			}
			if($this->kclass->input['orgEntitle'][$k] != $this->kclass->input['entitle'][$k]){
				$s = "`entitle`='".$this->kclass->input['entitle'][$k]."', ";
			}
			if($this->kclass->input['orgRemark'][$k] != $this->kclass->input['remark'][$k]){
				$s .= "`remark`='".$this->kclass->input['remark'][$k]."', ";
			}
			if($this->kclass->input['orgOrdering'][$k] != $this->kclass->input['ordering'][$k]){
				$s .= "`ordering`='".$this->kclass->input['ordering'][$k]."', ";
			}
			if($s != ''){
				$this->kclass->DB->query("
					UPDATE `gender`
					SET ".$s." 
						`modified` = '".TIMENOW."', 
						`modifier` = '".$this->kclass->user['userid']."'
					WHERE genderid='".$k."'
				");
			}
		}
		$this->kclass->messager(array(
			'title' => '修改性别',
			'text' => '全部性别的信息已成功修改!',
			'url' => '/s.php?module=gender',
			'sec' => 2
		));
	}

	//
	function updateAll(){
		$gs = $this->kclass->DB->query("SELECT `gender`.* FROM `gender` WHERE `killed`=0");
		if($this->kclass->DB->numRows()){
			while($g = $this->kclass->DB->fetchArray($gs)){
				$t.='<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').'"><td><input type="hidden" name="orgTitle['.$g['genderid'].']" value="'.$g['title'].'"><input type="text" style="width:200px" size="26" name="title['.$g['genderid'].']" value="'.$g['title'].'" /></td><td><input type="hidden" name="orgEntitle['.$g['genderid'].']" value="'.$g['entitle'].'"><input type="text" style="width:200px" size="26" name="entitle['.$g['genderid'].']" value="'.$g['entitle'].'" /></td><td><input type="hidden" name="orgRemark['.$g['genderid'].']" value="'.$g['remark'].'"><input type="text" style="width:200px" size="26" name="remark['.$g['genderid'].']" value="'.$g['remark'].'" /></td><td><input type="hidden" name="orgOrdering['.$g['genderid'].']" value="'.$g['ordering'].'"><input type="text" style="width:200px" size="26" name="ordering['.$g['genderid'].']" value="'.$g['ordering'].'" /></td></tr>';
			}
		}else{
			$this->kclass->boinkIt('/s.php?module=gender');
		}
		$sline = $this->kclass->iif($this->kclass->rotate(), 'odd', 'even');
$body = <<<EOF
<form action="/s.php?module=gender&action=doupdateAll" name="gender" method="post">
<input type="hidden" name="module" value="gender">
<input type="hidden" name="action" value="doupdateAll">
<table>
<thead>
<tr>
	<th colspan="4">修改全部：<span class="darkred small">请谨慎修改，防止误修改！</span></th>
</tr>
</thead>
<tbody>
<tr class="even">
	<td>名称<span class="small">(中文)</span> <span class="red bold">*</span></td>
	<td>名称<span class="small">(英文)</span></td>
	<td>备注</td>
	<td>排序</td>
</tr>
{$t}
<tr class="{$sline}">
	<td align="center" colspan="4">
		<input type="submit" id="submitButton" value="    保存    " accesskey="s">　　
		<input type="reset" value="    复位    ">
	</td>
</tr>
</tbody>
</table>
</form>
EOF;
		$this->kclass->page['onload'] = 'dc.tabhover()';
		$this->kclass->page['title'] .= ' - 修改全部性别';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 修改全部性别', 'right' => '<a href="/s.php?module=gender">返回列表</a>', 'body'=>$body));
	}
	//
	function kill(){
		if($this->kclass->input['genderid']<=0){
			$this->kclass->boinkIt('/s.php?module=gender');
		}
		if($this->kclass->input['confirm'] == 0){
			$this->kclass->boinkIt('/s.php?module=gender');
		}
		if($this->kclass->input['genderid'] < 0){
			$e = '<li>请从正确的页面进行操作，如果此问题持续存在，请联系网站管理员。';
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '删除性别',
				'text' => '您在删除性别的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$gender = $this->kclass->DB->queryFirst("
			SELECT title
			FROM gender
			WHERE genderid='".$this->kclass->input['genderid']."'
		");
		if($gender){
			$this->kclass->DB->query("
				UPDATE `gender`
				SET killed=".TIMENOW." 
				WHERE genderid='".$this->kclass->input['genderid']."'
			");
			$this->kclass->messager(array(
				'title' => '删除性别成功',
				'text' => '性别 <b>'.$gender['title'].'</b> 已成功被标记为删除!',
				'url' => '/s.php?module=gender',
				'sec' => 2
			));
		}else{
			$this->kclass->messager(array(
				'title' => '删除性别失败',
				'text' => '您要删除的性别，不存在！',
				'url' => '/s.php?module=gender#'.$this->kclass->input['genderid'],
				'sec' => 3
			));
		}
	}

	//
	function remove(){
		if($this->kclass->input['genderid']<=0){
			$this->kclass->boinkIt('/s.php?module=gender');
		}
		$gender = $this->kclass->DB->queryFirst("
			SELECT title
			FROM gender
			WHERE genderid='".$this->kclass->input['genderid']."'
		");
$body = <<<EOF
<form action="/s.php?module=gender&action=kill" name="gender" method="post">
<input type="hidden" name="module" value="gender">
<input type="hidden" name="action" value="kill">
<input type="hidden" name="genderid" value="{$this->kclass->input['genderid']}">
<table>
<thead>
<tr>
	<th>删除性别：{$gender['title']}</th>
</tr>
</thead>
<tbody>
<tr class="even">
	<td class="middle">你确定要删除性别: <a href="/s.php?module=gender&action=view&genderid={$this->kclass->input['genderid']}" class="big bold" target="_blank">{$gender['title']}</a> 吗?</td>
</tr>
<tr class="odd">
	<td align="center">
		<label for="c1" class="red"><input type="radio" id="c1" name="confirm" value="1">是</label>　　
		<label for="c0" class="green"><input type="radio" id="c0" name="confirm" value="0" checked>否</label>
	</td>
</tr>
<tr class="even">
	<td align="center">
		<input type="submit" value="   保存   " accesskey="s">
		<input type="reset" value="   复位   ">
	</td>
</tr>
</tbody>
</table>
</form>
EOF;
		$this->kclass->page['onload'] = 'dc.menu(\'filter\');';
		$this->kclass->page['title'] .= ' - 删除性别 - '.$gender['title'];
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 删除性别 - '.$gender['title'], 'right' => '<ul id="filter">'.$this->right . '<li><a href="/s.php?module=gender">返回</a></li></ul>', 'body'=>$body));
	}

	//
	function revival(){
		if($this->kclass->input['genderid']<=0){
			$this->kclass->boinkIt('/s.php?module=gender');
		}
		if($this->kclass->input['confirm'] == 0){
			$this->kclass->boinkIt('/s.php?module=gender');
		}
		if($this->kclass->input['genderid'] < 0){
			$e = '<li>请从正确的页面进行操作，如果此问题持续存在，请联系网站管理员。';
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '恢复性别',
				'text' => '您在恢复性别的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$gender = $this->kclass->DB->queryFirst("
			SELECT title
			FROM gender
			WHERE genderid='".$this->kclass->input['genderid']."'
		");
		if($gender){
			$this->kclass->DB->query("
				UPDATE `gender`
				SET killed=0
				WHERE genderid='".$this->kclass->input['genderid']."'
			");
			$this->kclass->messager(array(
				'title' => '恢复性别成功',
				'text' => '性别 <b>'.$gender['title'].'</b> 已成功被恢复！',
				'url' => '/s.php?module=gender',
				'sec' => 2
			));
		}else{
			$this->kclass->messager(array(
				'title' => '恢复性别失败',
				'text' => '您要恢复的性别不存在！',
				'url' => '/s.php?module=gender#'.$this->kclass->input['genderid'],
				'sec' => 3
			));
		}
	}

	//
	function restore(){
		if($this->kclass->input['genderid']<=0){
			$this->kclass->boinkIt('/s.php?module=gender');
		}
		$gender = $this->kclass->DB->queryFirst("
			SELECT title
			FROM gender
			WHERE genderid='".$this->kclass->input['genderid']."'
		");
$body = <<<EOF
<form action="/s.php?module=gender&action=revival" name="gender" method="post">
<input type="hidden" name="module" value="gender">
<input type="hidden" name="action" value="revival">
<input type="hidden" name="genderid" value="{$this->kclass->input['genderid']}">
<table>
<thead>
<tr>
	<th>恢复性别：{$gender['title']}</th>
</tr>
</thead>
<tbody>
<tr class="even">
	<td class="middle">你确定要恢复性别: <a href="/s.php?module=gender&action=view&genderid={$this->kclass->input['genderid']}" class="big bold" target="_blank">{$gender['title']}</a> 吗?</td>
</tr>
<tr class="odd">
	<td align="center">
		<label for="c1" class="red"><input type="radio" id="c1" name="confirm" value="1">是</label>　　
		<label for="c0" class="green"><input type="radio" id="c0" name="confirm" value="0" checked>否</label>
	</td>
</tr>
<tr class="even">
	<td align="center">
		<input type="submit" value="   保存   " accesskey="s">
		<input type="reset" value="   复位   ">
	</td>
</tr>
</tbody>
</table>
</form>
EOF;
		$this->kclass->page['onload'] = 'dc.menu(\'filter\');';
		$this->kclass->page['title'] .= ' - 恢复性别 - '.$gender['title'];
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 恢复性别 - '.$gender['title'], 'right' => '<ul id="filter">'.$this->right . '<li><a href="/s.php?module=gender">返回</a></li></ul>', 'body'=>$body));
	}
	// 
	function mmlist(){
		if($this->kclass->input['show']=='all'){
			$condition='1=1';
		}else{
			$condition='`gender`.killed=0';
		}
		$bs = $this->kclass->DB->query("
			SELECT *
			FROM `gender`
			WHERE ".$condition."
			ORDER BY ordering ASC, modified DESC, created DESC
		");
		if($this->kclass->DB->numRows()){
			$body = '<table class="eightypercent">';
			$body .= '<thead><tr><th>名称<span class="small">(中文)</span></th><th>名称<span class="small">(英文)</span></th><th>备注</th><th>排序</th><th align="center">修改日期</th><th>操作</th></tr></thead>';
			while($b = $this->kclass->DB->fetchArray($bs)){
				$body .= '<tbody><tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even');
				$body .= $this->kclass->iif($b['killed']==1, ' killed', '').'"><td class="middle bold"><a name="'.$b['genderid'].'"></a>'.$b['title'].'</td><td class="bold">'.$b['entitle'].'</td><td>'.$b['remark'].'</td><td align="right" class="small">'.$b['ordering'].'</td><td class="small gray">'.date('Y-m-d H:i:s', $b['modified']).'</td>';
				if($b['killed']==1){
					$body .= '<td><a href="/s.php?module=gender&action=restore&genderid='.$b['genderid'].'">恢</a></td>';
				}else{
					$body .= '<td align="right" class="small"><a href="/s.php?module=gender&action=remove&genderid='.$b['genderid'].'">删</a> <a href="/s.php?module=gender&action=update&genderid='.$b['genderid'].'">改</a></td></tr>';
				}
			}
			$body .= '</tbody></table>';
		}else{
			$body = '暂无相关记录。';
		}

		$this->kclass->page['onload'] = 'dc.tabhover()';
		$this->kclass->page['title'] .= ' - 列表';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 列表', 'right' => '<span class="small">显示方式：</span><a href="/s.php?module=gender">默认列表</a>　<a href="/s.php?module=gender&action=list&show=all">所有<span class="small gray">(包括删除)</span></a>　|　<a href="/s.php?module=gender&action=updateAll">修改全部</a>　|　<a href="/s.php?module=gender&action=add">新建</a>', 'body'=>$body));
	}
}
?>