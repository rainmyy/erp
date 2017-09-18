<?php
class deliveryterm{
	function autoRun(){
		$this->kclass->page['title'] = '配送条款';
		$this->baseurl = '<a href="/s.php">首页</a> - <a href="/s.php?module=deliveryterm">配送条款</a>';
		$this->right = '<a href="/s.php?module=deliveryterm">列表</a> <a href="/s.php?module=deliveryterm&action=add">新建</a>';
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
				$e = '<li>请填写配送条款的 名称。</li>';
			}else{
				if($this->kclass->DB->queryFirst("SELECT deliverytermid FROM `deliveryterm` WHERE `title`='".$this->kclass->input['title']."'")){
					$e = '<li>您要新建的配送条款 【'.$this->kclass->input['title'].'】 已经存在。</li>';
				}
			}
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '新建配送条款',
				'text' => '您在新建配送条款的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$this->kclass->DB->query("
			INSERT INTO `deliveryterm` ( `title` ,   `entitle` , `remark` , `ordering` , `modified`, `creator` , `created`)
			VALUES (
			'".$this->kclass->input['title']."', '".$this->kclass->input['entitle']."', '".$this->kclass->input['remark']."', '".$this->kclass->input['ordering']."', '".TIMENOW."', '".$this->kclass->user['userid']."', '".TIMENOW."'
			)
		");

		$this->kclass->messager(array(
			'title' => '新建配送条款',
			'text' => '配送条款 <b>'.$this->kclass->input['title'].'</b> 已新建成功!',
			'url' => '/s.php?module=deliveryterm#'.$this->kclass->DB->insertID(),
			'sec' => 2
		));
	}

	//
	function add(){
$body = <<<EOF
<form action="/s.php?module=deliveryterm&action=insert" name="deliveryterm" method="post">
<input type="hidden" name="module" value="deliveryterm">
<input type="hidden" name="action" value="insert">
<table><thead>
<tr>
	<th colspan="4">新建配送条款：</th>
</tr>
</thead>
<tbody>
<tr class="odd">
	<td>名称<span class="small">(中文)</span>：<span class="red bold">*</span></td>
	<td><input type="text" style="width:350px" size="26" name="title" value="{$deliveryterm['title']}"></td>
</tr>
<tr class="even">
	<td>名称<span class="small">(英文)</span>：</td>
	<td><input type="text" style="width:350px" size="26" name="entitle" value="{$deliveryterm['entitle']}"></td>
</tr>
<tr class="odd">
	<td>备　　注：</td>
	<td><input type="text" style="width:350px" size="26" name="remark" value="{$deliveryterm['remark']}"></td>
</tr>
<tr class="even">
	<td>排　　序：</td>
	<td><input type="text" style="width:200px" size="26" name="ordering" value="1"> <span class="small gray">填入整数，数字越小排位越靠前。</span></td>
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
		$this->kclass->page['onload'] = 'dc.tabhover()';
		$this->kclass->page['title'] .= ' - 新建配送条款';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 新建配送条款', 'right' => '<a href="/s.php?module=deliveryterm">返回列表</a>', 'body'=>$body));
}

	//
	function doupdate(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			if($this->kclass->input['title'] == ''){
				$e = '<li>请填写配送条款的 名称。</li>';
			}else{
				if($deliveryterm = $this->kclass->DB->queryFirst("SELECT deliverytermid FROM `deliveryterm` WHERE `deliverytermid`<>'".$this->kclass->input['deliverytermid']."' AND `title`='".$this->kclass->input['title']."'")){
					$e = '<li>您要修改的配送条款 【'.$this->kclass->input['title'].'】 已经存在。</li>';
				}
			}
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '修改配送条款',
				'text' => '您在修改配送条款的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$this->kclass->DB->query("
			UPDATE `deliveryterm`
			SET `title` = '".$this->kclass->input['title']."', 
				`entitle` = '".$this->kclass->input['entitle']."', 
				`remark` = '".$this->kclass->input['remark']."', 
				`ordering` = '".$this->kclass->input['ordering']."', 
				`modified` = '".TIMENOW."', 
				`modifier` = '".$this->kclass->user['userid']."'
			WHERE deliverytermid='".$this->kclass->input['deliverytermid']."'
		");
		$this->kclass->messager(array(
			'title' => '修改配送条款',
			'text' => '配送条款 <b>'.$this->kclass->input['title'].'</b> 的信息已成功修改!',
			'url' => '/s.php?module=deliveryterm#'.$this->kclass->input['deliverytermid'],
			'sec' => 2
		));
	}

	//
	function update(){
		if($this->kclass->input['deliverytermid']<=0 OR !$deliveryterm = $this->kclass->DB->queryFirst("SELECT `deliveryterm`.* FROM `deliveryterm` WHERE `deliverytermid`='".$this->kclass->input['deliverytermid']."'")){
			$this->kclass->boinkIt('/s.php?module=deliveryterm');
		}
$body = <<<EOF
<form action="/s.php?module=deliveryterm&action=doupdate" name="deliveryterm" method="post">
<input type="hidden" name="module" value="deliveryterm">
<input type="hidden" name="action" value="doupdate">
<input type="hidden" name="deliverytermid" value="{$this->kclass->input['deliverytermid']}">
<table><thead>
<tr>
	<th colspan="2">配送条款修改：{$deliveryterm['title']}</th>
</tr>
</thead>
<tbody>
<tr class="odd">
	<td>名称<span class="small">(中文)</span>：<span class="red bold">*</span></td>
	<td><input type="text" style="width:350px" size="26" name="title" value="{$deliveryterm['title']}"></td>
</tr>
<tr class="even">
	<td>名称<span class="small">(英文)</span>：</td>
	<td><input type="text" style="width:350px" size="26" name="entitle" value="{$deliveryterm['entitle']}"></td>
</tr>
<tr class="odd">
	<td>备　　注：</td>
	<td><input type="text" style="width:350px" size="26" name="remark" value="{$deliveryterm['remark']}"></td>
</tr>
<tr class="even">
	<td>排　　序：</td>
	<td><input type="text" style="width:100px" size="26" name="ordering" value="{$deliveryterm['ordering']}"> <span class="small gray">填入整数，数字越小排位越靠前。</span></td>
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
		$this->kclass->page['onload'] = 'dc.tabhover()';
		$this->kclass->page['title'] .= ' - 修改配送条款 - '.$deliveryterm['title'];
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 修改配送条款 - '.$deliveryterm['title'], 'right' => '<a href="/s.php?module=deliveryterm">返回列表</a>', 'body'=>$body));
	}
	//
	function doupdateAll(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			if(implode('', $this->kclass->input['title']) == ''){
				$e = '<li>请填写配送条款的 名称，不能留空。</li>';
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
				'title' => '修改全部配送条款',
				'text' => '您在修改配送条款的过程中有以下错误：<ul>'.$e.'</ul>',
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
					UPDATE `deliveryterm`
					SET ".$s." 
						`modified` = '".TIMENOW."', 
						`modifier` = '".$this->kclass->user['userid']."'
					WHERE deliverytermid='".$k."'
				");
			}
		}
		$this->kclass->messager(array(
			'title' => '修改全部配送条款',
			'text' => '全部配送条款的信息已成功修改!',
			'url' => '/s.php?module=deliveryterm',
			'sec' => 2
		));
	}

	//
	function updateAll(){
		$gs = $this->kclass->DB->query("SELECT `deliveryterm`.* FROM `deliveryterm` WHERE `killed`=0");
		if($this->kclass->DB->numRows()){
			while($g = $this->kclass->DB->fetchArray($gs)){
				$t.='<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').'"><td><input type="hidden" name="orgTitle['.$g['deliverytermid'].']" value="'.$g['title'].'"><input type="text" style="width:300px" size="26" name="title['.$g['deliverytermid'].']" value="'.$g['title'].'" /></td><td><input type="hidden" name="orgEntitle['.$g['deliverytermid'].']" value="'.$g['entitle'].'"><input type="text" style="width:300px" size="26" name="entitle['.$g['deliverytermid'].']" value="'.$g['entitle'].'" /></td><td><input type="hidden" name="orgRemark['.$g['deliverytermid'].']" value="'.$g['remark'].'"><input type="text" style="width:350px" size="26" name="remark['.$g['deliverytermid'].']" value="'.$g['remark'].'" /></td><td><input type="hidden" name="orgOrdering['.$g['deliverytermid'].']" value="'.$g['ordering'].'"><input type="text" style="width:20px" size="26" name="ordering['.$g['deliverytermid'].']" value="'.$g['ordering'].'" /></td></tr>';
			}
		}else{
			$this->kclass->boinkIt('/s.php?module=deliveryterm');
		}
		$sline = $this->kclass->iif($this->kclass->rotate(), 'odd', 'even');
$body = <<<EOF
<form action="/s.php?module=deliveryterm&action=doupdateAll" name="deliveryterm" method="post">
<input type="hidden" name="module" value="deliveryterm">
<input type="hidden" name="action" value="doupdateAll">
<table><thead>
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
		<input type="submit" id="submitButton" value="      保存      " accesskey="s">　　
		<input type="reset" value="      复位      ">
	</td>
</tr>
</tbody>
</table>
</form>
EOF;
		$this->kclass->page['onload'] = 'dc.tabhover()';
		$this->kclass->page['title'] .= ' - 修改全部配送条款';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 修改全部配送条款', 'right' => '<a href="/s.php?module=deliveryterm">返回列表</a>', 'body'=>$body));
	}
	//
	function kill(){
		if($this->kclass->input['deliverytermid']<=0){
			$this->kclass->boinkIt('/s.php?module=deliveryterm');
		}
		if($this->kclass->input['confirm'] == 0){
			$this->kclass->boinkIt('/s.php?module=deliveryterm');
		}
		if($this->kclass->input['deliverytermid'] < 0){
			$e = '<li>请从正确的页面进行操作，如果此问题持续存在，请联系网站管理员。';
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '删除配送条款',
				'text' => '您在删除配送条款的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$deliveryterm = $this->kclass->DB->queryFirst("
			SELECT title
			FROM deliveryterm
			WHERE deliverytermid='".$this->kclass->input['deliverytermid']."'
		");
		if($deliveryterm){
			$this->kclass->DB->query("
				UPDATE `deliveryterm`
				SET killed=1
				WHERE deliverytermid='".$this->kclass->input['deliverytermid']."'
			");
			$this->kclass->messager(array(
				'title' => '删除配送条款成功',
				'text' => '配送条款 <b>'.$deliveryterm['title'].'</b> 已成功被标记为删除!',
				'url' => '/s.php?module=deliveryterm',
				'sec' => 2
			));
		}else{
			$this->kclass->messager(array(
				'title' => '删除配送条款失败',
				'text' => '您要删除的配送条款，不存在！',
				'url' => '/s.php?module=deliveryterm#'.$this->kclass->input['deliverytermid'],
				'sec' => 3
			));
		}
	}

	//
	function remove(){
		if($this->kclass->input['deliverytermid']<=0){
			$this->kclass->boinkIt('/s.php?module=deliveryterm');
		}
		$deliveryterm = $this->kclass->DB->queryFirst("
			SELECT title
			FROM deliveryterm
			WHERE deliverytermid='".$this->kclass->input['deliverytermid']."'
		");
$body = <<<EOF
<form action="/s.php?module=deliveryterm&action=kill" name="deliveryterm" method="post">
<input type="hidden" name="module" value="deliveryterm">
<input type="hidden" name="action" value="kill">
<input type="hidden" name="deliverytermid" value="{$this->kclass->input['deliverytermid']}">
<table><thead>
<tr>
	<th>删除配送条款：{$deliveryterm['title']}</th>
</tr>
</thead>
<tbody>
<tr class="even">
	<td class="middle">你确定要删除配送条款: <a href="/s.php?module=deliveryterm&action=view&deliverytermid={$this->kclass->input['deliverytermid']}" class="big bold" target="_blank">{$deliveryterm['title']}</a> 吗?</td>
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
		$this->kclass->page['title'] .= ' - 删除配送条款 - '.$deliveryterm['title'];
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 删除配送条款 - '.$deliveryterm['title'], 'right' => '<a href="/s.php?module=deliveryterm">返回列表</a>', 'body'=>$body));
	}

	//
	function revival(){
		if($this->kclass->input['deliverytermid']<=0){
			$this->kclass->boinkIt('/s.php?module=deliveryterm');
		}
		if($this->kclass->input['confirm'] == 0){
			$this->kclass->boinkIt('/s.php?module=deliveryterm');
		}
		if($this->kclass->input['deliverytermid'] < 0){
			$e = '<li>请从正确的页面进行操作，如果此问题持续存在，请联系网站管理员。';
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '恢复配送条款',
				'text' => '您在恢复配送条款的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$deliveryterm = $this->kclass->DB->queryFirst("
			SELECT title
			FROM deliveryterm
			WHERE deliverytermid='".$this->kclass->input['deliverytermid']."'
		");
		if($deliveryterm){
			$this->kclass->DB->query("
				UPDATE `deliveryterm`
				SET killed=0
				WHERE deliverytermid='".$this->kclass->input['deliverytermid']."'
			");
			$this->kclass->messager(array(
				'title' => '恢复配送条款成功',
				'text' => '配送条款 <b>'.$deliveryterm['title'].'</b> 已成功被恢复！',
				'url' => '/s.php?module=deliveryterm',
				'sec' => 2
			));
		}else{
			$this->kclass->messager(array(
				'title' => '恢复配送条款失败',
				'text' => '您要恢复的配送条款不存在！',
				'url' => '/s.php?module=deliveryterm#'.$this->kclass->input['deliverytermid'],
				'sec' => 3
			));
		}
	}

	//
	function restore(){
		if($this->kclass->input['deliverytermid']<=0){
			$this->kclass->boinkIt('/s.php?module=deliveryterm');
		}
		$deliveryterm = $this->kclass->DB->queryFirst("
			SELECT title
			FROM deliveryterm
			WHERE deliverytermid='".$this->kclass->input['deliverytermid']."'
		");
$body = <<<EOF
<form action="/s.php?module=deliveryterm&action=revival" name="deliveryterm" method="post">
<input type="hidden" name="module" value="deliveryterm">
<input type="hidden" name="action" value="revival">
<input type="hidden" name="deliverytermid" value="{$this->kclass->input['deliverytermid']}">
<table><thead>
<tr>
	<th>恢复配送条款：{$deliveryterm['title']}</th>
</tr>
</thead>
<tbody>
<tr class="even">
	<td class="middle">你确定要恢复配送条款: <a href="/s.php?module=deliveryterm&action=view&deliverytermid={$this->kclass->input['deliverytermid']}" class="big bold" target="_blank">{$deliveryterm['title']}</a> 吗?</td>
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
		$this->kclass->page['title'] .= ' - 恢复配送条款 - '.$deliveryterm['title'];
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 恢复配送条款 - '.$deliveryterm['title'], 'right' => '<a href="/s.php?module=deliveryterm">返回列表</a>', 'body'=>$body));
	}
	// 
	function mmlist(){
		if($this->kclass->input['show']=='all'){
			$condition='1=1';
		}else{
			$condition='`deliveryterm`.killed=0';
		}
		$bs = $this->kclass->DB->query("
			SELECT *
			FROM `deliveryterm`
			WHERE ".$condition."
			ORDER BY ordering DESC, modified DESC
		");
		if($this->kclass->DB->numRows()){
			$body = '<table class="eightypercent"><thead>';
			$body .= '<tr><th>名称<span class="small">(中文)</span></th><th>名称<span class="small">(英文)</span></th><th>备注</th><th>排序</th><th align="center">修改日期</th><th align="center" class="small">操作</th></tr></thead><tbody>';
			while($b = $this->kclass->DB->fetchArray($bs)){
				$body .= '<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even');
				$body .= $this->kclass->iif($b['killed']==1, ' through', '').'"><td class="middle"><a name="'.$b['deliverytermid'].'"></a>'.$b['title'].'</td><td>'.$b['entitle'].'</td><td>'.$b['remark'].'</td><td class="small">'.$b['ordering'].'</td><td class="small gray">'.date('Y-m-d H:i:s', $b['modified']).'</td>';
				if($b['killed']==1){
					$body .= '<td><a href="/s.php?module=deliveryterm&action=restore&deliverytermid='.$b['deliverytermid'].'">恢</a></td>';
				}else{
					$body .= '<td align="right"><a href="/s.php?module=deliveryterm&action=remove&deliverytermid='.$b['deliverytermid'].'" class="small">删</a> <a href="/s.php?module=deliveryterm&action=update&deliverytermid='.$b['deliverytermid'].'">改</a></td></tr>';
				}
			}
			$body .= '</tbody></table>';
		}else{
			$body = '暂无相关记录。';
		}

		$this->kclass->page['onload'] = 'dc.tabhover()';
		$this->kclass->page['title'] .= ' - 列表';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 列表', 'right' => '<span class="small">显示方式：</span><a href="/s.php?module=deliveryterm">默认列表</a>　<a href="/s.php?module=deliveryterm&action=list&show=all">所有<span class="small gray">(包括删除)</span></a>　|　<a href="/s.php?module=deliveryterm&action=updateAll">修改全部</a>　|　<a href="/s.php?module=deliveryterm&action=add">新建</a>', 'body'=>$body));
	}
}
?>