<?php
class fee{
	function autoRun(){
		$this->kclass->page['title'] = '费用项目';
		$this->baseurl = '<a href="/s.php">首页</a> - <a href="/s.php?module=fee">费用项目</a>';
		$this->right = '<a href="/s.php?module=fee">列表</a> <a href="/s.php?module=fee&action=add">新建</a>';
		switch($this->kclass->input['action']){
			case 'list':
				$this->modify();
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
				$this->modify();
		}
	}
	//
	function insert(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			if($this->kclass->input['title'] == ''){
				$e = '<li>请填写费用项目的 名称。</li>';
			}else{
				if($this->kclass->DB->queryFirst("SELECT feeid FROM `fee` WHERE `title`='".$this->kclass->input['title']."'")){
					$e = '<li>您要新建的费用项目 【'.$this->kclass->input['title'].'】 已经存在。</li>';
				}
			}
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '新建费用项目',
				'text' => '您在新建费用项目的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$this->kclass->DB->query("
			INSERT INTO `fee` ( `title` ,   `entitle` , `remark` , `ordering` , `modified`, `creator` , `created`)
			VALUES (
			'".$this->kclass->input['title']."', '".$this->kclass->input['entitle']."', '".$this->kclass->input['remark']."', '".$this->kclass->input['ordering']."', '".TIMENOW."', '".$this->kclass->user['userid']."', '".TIMENOW."'
			)
		");

		$this->kclass->messager(array(
			'title' => '新建费用项目',
			'text' => '费用项目 <b>'.$this->kclass->input['title'].'</b> 已新建成功!',
			'url' => '/s.php?module=fee#'.$this->kclass->DB->insertID(),
			'sec' => 2
		));
	}

	//
	function add(){
$body = <<<EOF
<form action="/s.php?module=fee&action=insert" name="fee" method="post">
<input type="hidden" name="module" value="fee">
<input type="hidden" name="action" value="insert">
<table class="tlist" align="center">
<thead><tr>
	<th colspan="4">新建费用项目：</th>
</tr>
</thead>
<tbody>
<tr class="odd">
	<td>名称<span class="small">(中文)</span>：<span class="red bold">*</span></td>
	<td><input type="text" style="width:200px" size="26" name="title" value="{$fee['title']}"></td>
</tr>
<tr class="even">
	<td>名称<span class="small">(英文)</span>：</td>
	<td><input type="text" style="width:200px" size="26" name="entitle" value="{$fee['entitle']}"></td>
</tr>
<tr class="odd">
	<td>备　　注：</td>
	<td><input type="text" style="width:200px" size="26" name="remark" value="{$fee['remark']}"></td>
</tr>
<tr class="even">
	<td>排　　序：</td>
	<td><input type="text" style="width:200px" size="26" name="ordering" value="1"></td>
</tr>
<tr class="odd">
	<td align="center" colspan="4">
		<input type="submit" id="submitButton" value="      保存      " accesskey="s">　　
		<input type="reset" value="      复位      ">
	</td>
</tr>
</tbody>
</table>
</form>
EOF;
		$this->kclass->page['title'] .= ' - 新建费用项目';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 新建费用项目', 'right' => $this->right . ' | <a href="/s.php?module=fee">返回列表</a>', 'body'=>$body));
}

	//
	function doupdate(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			if($this->kclass->input['title'] == ''){
				$e = '<li>请填写费用项目的 名称。</li>';
			}else{
				if($fee = $this->kclass->DB->queryFirst("SELECT feeid FROM `fee` WHERE `feeid`<>'".$this->kclass->input['feeid']."' AND `title`='".$this->kclass->input['title']."'")){
					$e = '<li>您要修改的费用项目 【'.$this->kclass->input['title'].'】 已经存在。</li>';
				}
			}
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '修改费用项目',
				'text' => '您在修改费用项目的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$this->kclass->DB->query("
			UPDATE `fee`
			SET `title` = '".$this->kclass->input['title']."', 
				`entitle` = '".$this->kclass->input['entitle']."', 
				`remark` = '".$this->kclass->input['remark']."', 
				`ordering` = '".$this->kclass->input['ordering']."', 
				`modified` = '".TIMENOW."', 
				`modifier` = '".$this->kclass->user['userid']."'
			WHERE feeid='".$this->kclass->input['feeid']."'
		");
		$this->kclass->messager(array(
			'title' => '修改费用项目',
			'text' => '费用项目 <b>'.$this->kclass->input['title'].'</b> 的信息已成功修改!',
			'url' => '/s.php?module=fee#'.$this->kclass->input['feeid'],
			'sec' => 2
		));
	}

	//
	function update(){
		if($this->kclass->input['feeid']<=0 OR !$fee = $this->kclass->DB->queryFirst("SELECT `fee`.* FROM `fee` WHERE `feeid`='".$this->kclass->input['feeid']."'")){
			$this->kclass->boinkIt('/s.php?module=fee');
		}
$body = <<<EOF
<form action="/s.php?module=fee&action=doupdate" name="fee" method="post">
<input type="hidden" name="module" value="fee">
<input type="hidden" name="action" value="doupdate">
<input type="hidden" name="feeid" value="{$this->kclass->input['feeid']}">
<table class="tlist" align="center">
<thead><tr>
	<th colspan="2">费用项目修改：{$fee['title']}</th>
</tr>
</thead>
<tbody>
<tr class="odd">
	<td>名称<span class="small">(中文)</span>：<span class="red bold">*</span></td>
	<td><input type="text" style="width:200px" size="26" name="title" value="{$fee['title']}"></td>
</tr>
<tr class="even">
	<td>名称<span class="small">(英文)</span>：</td>
	<td><input type="text" style="width:200px" size="26" name="entitle" value="{$fee['entitle']}"></td>
</tr>
<tr class="odd">
	<td>备　　注：</td>
	<td><input type="text" style="width:200px" size="26" name="remark" value="{$fee['remark']}"></td>
</tr>
<tr class="even">
	<td>排　　序：</td>
	<td><input type="text" style="width:200px" size="26" name="ordering" value="{$fee['ordering']}"></td>
</tr>
<tr class="odd">
	<td align="center" colspan="2">
		<input type="submit" id="submitButton" value="      保存      " accesskey="s">　　
		<input type="reset" value="      复位      ">
	</td>
</tr>
</tbody>
</table>
</form>
EOF;
		$this->kclass->tbline+=2;
		$this->kclass->page['title'] .= ' - 修改费用项目 - '.$fee['title'];
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 修改费用项目 - '.$fee['title'], 'right' => $this->right . ' | <a href="/s.php?module=fee">返回列表</a>', 'body'=>$body));
	}
	//
	function doupdateAll(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			if(implode('', $this->kclass->input['title']) == ''){
				$e = '<li>请填写费用项目的 名称，不能留空。</li>';
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
				'title' => '修改费用项目',
				'text' => '您在修改费用项目的过程中有以下错误：<ul>'.$e.'</ul>',
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
					UPDATE `fee`
					SET ".$s." 
						`modified` = '".TIMENOW."', 
						`modifier` = '".$this->kclass->user['userid']."'
					WHERE feeid='".$k."'
				");
			}
		}
		$this->kclass->messager(array(
			'title' => '修改费用项目',
			'text' => '全部费用项目的信息已成功修改!',
			'url' => '/s.php?module=fee',
			'sec' => 2
		));
	}

	//
	function updateAll(){
		$gs = $this->kclass->DB->query("SELECT `fee`.* FROM `fee` WHERE `killed`=0");
		if($this->kclass->DB->numRows()){
			while($g = $this->kclass->DB->fetchArray($gs)){
				$t.='<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').'"><td><input type="hidden" name="orgTitle['.$g['feeid'].']" value="'.$g['title'].'"><input type="text" style="width:200px" size="26" name="title['.$g['feeid'].']" value="'.$g['title'].'" /></td><td><input type="hidden" name="orgEntitle['.$g['feeid'].']" value="'.$g['entitle'].'"><input type="text" style="width:200px" size="26" name="entitle['.$g['feeid'].']" value="'.$g['entitle'].'" /></td><td><input type="hidden" name="orgRemark['.$g['feeid'].']" value="'.$g['remark'].'"><input type="text" style="width:200px" size="26" name="remark['.$g['feeid'].']" value="'.$g['remark'].'" /></td><td><input type="hidden" name="orgOrdering['.$g['feeid'].']" value="'.$g['ordering'].'"><input type="text" style="width:200px" size="26" name="ordering['.$g['feeid'].']" value="'.$g['ordering'].'" /></td></tr>';
			}
		}else{
			$this->kclass->boinkIt('/s.php?module=fee');
		}
		$sline = $this->kclass->iif($this->kclass->rotate(), 'odd', 'even');
$body = <<<EOF
<form action="/s.php?module=fee&action=doupdateAll" name="fee" method="post">
<input type="hidden" name="module" value="fee">
<input type="hidden" name="action" value="doupdateAll">
<table class="tlist" align="center">
<thead>
<tr>
	<th>名称<span class="small">(中文)</span> <span class="red bold">*</span></th>
	<th>名称<span class="small">(英文)</span></th>
	<th>备注</th>
	<th>排序</th>
</tr>
</thead>
<tbody>
{$t}
<tr class="{$sline}">
	<td align="center" colspan="4">
		<input type="submit" id="submitButton" value="      保存      " accesskey="s">　　
		<input type="reset" value="      复位      ">
	</td>
</tr>
</tbody>
</table>
</form>
EOF;
		$this->kclass->tbline+=2;
		$this->kclass->page['title'] .= ' - 修改费用项目';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 修改全部费用项目', 'right' => $this->right . ' | <a href="/s.php?module=fee">返回列表</a>', 'body'=>$body));
	}
	//
	function kill(){
		if($this->kclass->input['feeid']<=0){
			$this->kclass->boinkIt('/s.php?module=fee');
		}
		if($this->kclass->input['confirm'] == 0){
			$this->kclass->boinkIt('/s.php?module=fee');
		}
		if($this->kclass->input['feeid'] < 0){
			$e = '<li>请从正确的页面进行操作，如果此问题持续存在，请联系网站管理员。';
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '删除费用项目',
				'text' => '您在删除费用项目的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$fee = $this->kclass->DB->queryFirst("
			SELECT title
			FROM fee
			WHERE feeid='".$this->kclass->input['feeid']."'
		");
		if($fee){
			$this->kclass->DB->query("
				UPDATE `fee`
				SET killed=1
				WHERE feeid='".$this->kclass->input['feeid']."'
			");
			$this->kclass->messager(array(
				'title' => '删除费用项目成功',
				'text' => '费用项目 <b>'.$fee['title'].'</b> 已成功被标记为删除!',
				'url' => '/s.php?module=fee',
				'sec' => 2
			));
		}else{
			$this->kclass->messager(array(
				'title' => '删除费用项目失败',
				'text' => '您要删除的费用项目，不存在！',
				'url' => '/s.php?module=fee#'.$this->kclass->input['feeid'],
				'sec' => 3
			));
		}
	}

	//
	function remove(){
		if($this->kclass->input['feeid']<=0){
			$this->kclass->boinkIt('/s.php?module=fee');
		}
		$fee = $this->kclass->DB->queryFirst("
			SELECT title, entitle
			FROM fee
			WHERE feeid='".$this->kclass->input['feeid']."'
		");
$body = <<<EOF
<form action="/s.php?module=fee&action=kill" name="fee" method="post">
<input type="hidden" name="module" value="fee">
<input type="hidden" name="action" value="kill">
<input type="hidden" name="feeid" value="{$this->kclass->input['feeid']}">
<table class="tlist" align="center">
<thead><tr>
	<th>删除费用项目：{$fee['title']}</th>
</tr>
</thead>
<tbody>
<tr class="odd">
	<td class="middle">你确定要删除费用项目: <a href="/s.php?module=fee&action=view&feeid={$this->kclass->input['feeid']}" class="big bold" target="_blank">{$fee['title']} ({$fee['entitle']})</a> 吗?</td>
</tr>
<tr class="even">
	<td align="center">
		<label for="c1" class="red"><input type="radio" id="c1" name="confirm" value="1">是</label>　　
		<label for="c0" class="green"><input type="radio" id="c0" name="confirm" value="0" checked>否</label>
	</td>
</tr>
<tr class="odd">
	<td align="center">
		<input type="submit" value="   保存   " accesskey="s">
		<input type="reset" value="   复位   ">
	</td>
</tr>
</tbody>
</table>
</form>
EOF;
		$this->kclass->page['title'] .= ' - 删除费用项目 - '.$fee['title'];
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 删除费用项目 - '.$fee['title'], 'right' => $this->right . ' | <a href="/s.php?module=fee">返回</a>', 'body'=>$body));
	}

	//
	function revival(){
		if($this->kclass->input['feeid']<=0){
			$this->kclass->boinkIt('/s.php?module=fee');
		}
		if($this->kclass->input['confirm'] == 0){
			$this->kclass->boinkIt('/s.php?module=fee');
		}
		if($this->kclass->input['feeid'] < 0){
			$e = '<li>请从正确的页面进行操作，如果此问题持续存在，请联系网站管理员。';
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '恢复费用项目',
				'text' => '您在恢复费用项目的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$fee = $this->kclass->DB->queryFirst("
			SELECT title
			FROM fee
			WHERE feeid='".$this->kclass->input['feeid']."'
		");
		if($fee){
			$this->kclass->DB->query("
				UPDATE `fee`
				SET killed=0
				WHERE feeid='".$this->kclass->input['feeid']."'
			");
			$this->kclass->messager(array(
				'title' => '恢复费用项目成功',
				'text' => '费用项目 <b>'.$fee['title'].'</b> 已成功被恢复！',
				'url' => '/s.php?module=fee',
				'sec' => 2
			));
		}else{
			$this->kclass->messager(array(
				'title' => '恢复费用项目失败',
				'text' => '您要恢复的费用项目不存在！',
				'url' => '/s.php?module=fee#'.$this->kclass->input['feeid'],
				'sec' => 3
			));
		}
	}

	//
	function restore(){
		if($this->kclass->input['feeid']<=0){
			$this->kclass->boinkIt('/s.php?module=fee');
		}
		$fee = $this->kclass->DB->queryFirst("
			SELECT title
			FROM fee
			WHERE feeid='".$this->kclass->input['feeid']."'
		");
$body = <<<EOF
<form action="/s.php?module=fee&action=revival" name="fee" method="post">
<input type="hidden" name="module" value="fee">
<input type="hidden" name="action" value="revival">
<input type="hidden" name="feeid" value="{$this->kclass->input['feeid']}">
<table class="tlist" align="center">
<thead><tr>
	<th>恢复费用项目：{$fee['title']}</th>
</tr>
</thead>
<tbody>
<tr class="even">
	<td class="middle">你确定要恢复费用项目: <a href="/s.php?module=fee&action=view&feeid={$this->kclass->input['feeid']}" class="big bold" target="_blank">{$fee['title']} ({$fee['entitle']})</a> 吗?</td>
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
		$this->kclass->page['title'] .= ' - 恢复费用项目 - '.$fee['title'];
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 恢复费用项目 - '.$fee['title'], 'right' => $this->right . ' | <a href="/s.php?module=fee">返回</a>', 'body'=>$body));
	}
	// 
	function modify(){
		$bs = $this->kclass->DB->query("
			SELECT *
			FROM `fee`
			ORDER BY ordering DESC, modified DESC
		");
		if($this->kclass->DB->numRows()){
			$body = '<table class="eightypercent mytable">';
			$body .= '<thead><tr><th>名称<span class="small">(中文)</span></th><th>名称<span class="small">(英文)</span></th><th>备注</th><th>排序</th><th align="center">修改日期</th><th align="center" class="small">操作</th></tr></thead><tbody>';
			while($b = $this->kclass->DB->fetchArray($bs)){
				$body .= '<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even');
				$body .= $this->kclass->iif($b['killed']==1, ' through', '').'"><td class="middle"><a name="'.$b['feeid'].'"></a>'.$b['title'].'</td><td>'.$b['entitle'].'</td><td>'.$b['remark'].'</td><td class="small">'.$b['ordering'].'</td><td class="small gray">'.date('Y-m-d H:i:s', $b['modified']).'</td>';
				if($b['killed']==1){
					$body .= '<td><a href="/s.php?module=fee&action=restore&feeid='.$b['feeid'].'">恢</a></td>';
				}else{
					$body .= '<td align="right" class="small"><a href="/s.php?module=fee&action=remove&feeid='.$b['feeid'].'">删</a> <a href="/s.php?module=fee&action=update&feeid='.$b['feeid'].'">改</a></td></tr>';
				}
			}
			$body .= '</tbody></table>';
		}else{
			$body = '暂无相关记录。';
		}

		$this->kclass->page['onload'] = 'dc.tabhover();$(\'.mytable\').fixedtableheader();';
		$this->kclass->page['title'] .= ' - 列表';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 列表', 'right' => $this->right.' <a href="/s.php?module=fee&action=updateAll">改全部</a>', 'body'=>$body));
	}
}
?>