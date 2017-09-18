<?php
class packing{
	function autoRun(){
		$this->kclass->page['title'] = '包装';
		$this->baseurl = '<a href="/s.php">首页</a> - <a href="/s.php?module=packing">包装</a>';
		$this->right = '<a href="/s.php?module=packing">列表</a> <a href="/s.php?module=packing&action=add">新建</a>';
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
				$e = '<li>请填写包装的 名称。</li>';
			}else{
				if($this->kclass->DB->queryFirst("SELECT packingid FROM `packing` WHERE `title`='".$this->kclass->input['title']."'")){
					$e = '<li>您要新建的包装 【'.$this->kclass->input['title'].'】 已经存在。</li>';
				}
			}
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '新建包装',
				'text' => '您在新建包装的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$this->kclass->DB->query("
			INSERT INTO `packing` ( `title` ,   `entitle` , `remark` , `ordering` , `modified`, `creator` , `created`)
			VALUES (
			'".$this->kclass->input['title']."', '".$this->kclass->input['entitle']."', '".$this->kclass->input['remark']."', '".$this->kclass->input['ordering']."', '".TIMENOW."', '".$this->kclass->user['userid']."', '".TIMENOW."'
			)
		");

		$this->kclass->messager(array(
			'title' => '新建包装',
			'text' => '包装 <b>'.$this->kclass->input['title'].'</b> 已新建成功!',
			'url' => '/s.php?module=packing#'.$this->kclass->DB->insertID(),
			'sec' => 2
		));
	}

	//
	function add(){
$body = <<<EOF
<form action="/s.php?module=packing&action=insert" name="packing" method="post">
<input type="hidden" name="module" value="packing">
<input type="hidden" name="action" value="insert">
<table><thead>
<tr>
	<th colspan="4">新建包装：</th>
</tr>
</thead>
<tbody>
<tr class="even">
	<td>名称<span class="small">(中文)</span>：<span class="red bold">*</span></td>
	<td><input type="text" style="width:350px" size="26" name="title" value="{$packing['title']}"></td>
</tr>
<tr class="odd">
	<td>名称<span class="small">(英文)</span>：</td>
	<td><input type="text" style="width:350px" size="26" name="entitle" value="{$packing['entitle']}"></td>
</tr>
<tr class="even">
	<td>备　　注：</td>
	<td><input type="text" style="width:350px" size="26" name="remark" value="{$packing['remark']}"></td>
</tr>
<tr class="odd">
	<td>排　　序：</td>
	<td><input type="text" style="width:100px" size="26" name="ordering" value="1"> <span class="small gray">填入整数，数字越小排位越靠前。</span></td>
</tr>
<tr class="even">
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
		$this->kclass->page['title'] .= ' - 新建包装';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 新建包装', 'right' => '<a href="/s.php?module=packing">返回列表</a>', 'body'=>$body));
}

	//
	function doupdate(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			if($this->kclass->input['title'] == ''){
				$e = '<li>请填写包装的 名称。</li>';
			}else{
				if($packing = $this->kclass->DB->queryFirst("SELECT packingid FROM `packing` WHERE `packingid`<>'".$this->kclass->input['packingid']."' AND `title`='".$this->kclass->input['title']."'")){
					$e = '<li>您要修改的包装 【'.$this->kclass->input['title'].'】 已经存在。</li>';
				}
			}
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '修改包装',
				'text' => '您在修改包装的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$this->kclass->DB->query("
			UPDATE `packing`
			SET `title` = '".$this->kclass->input['title']."', 
				`entitle` = '".$this->kclass->input['entitle']."', 
				`remark` = '".$this->kclass->input['remark']."', 
				`ordering` = '".$this->kclass->input['ordering']."', 
				`modified` = '".TIMENOW."', 
				`modifier` = '".$this->kclass->user['userid']."'
			WHERE packingid='".$this->kclass->input['packingid']."'
		");
		$this->kclass->messager(array(
			'title' => '修改包装',
			'text' => '包装 <b>'.$this->kclass->input['title'].'</b> 的信息已成功修改!',
			'url' => '/s.php?module=packing#'.$this->kclass->input['packingid'],
			'sec' => 2
		));
	}

	//
	function update(){
		if($this->kclass->input['packingid']<=0 OR !$packing = $this->kclass->DB->queryFirst("SELECT `packing`.* FROM `packing` WHERE `packingid`='".$this->kclass->input['packingid']."'")){
			$this->kclass->boinkIt('/s.php?module=packing');
		}
$body = <<<EOF
<form action="/s.php?module=packing&action=doupdate" name="packing" method="post">
<input type="hidden" name="module" value="packing">
<input type="hidden" name="action" value="doupdate">
<input type="hidden" name="packingid" value="{$this->kclass->input['packingid']}">
<table><thead>
<tr>
	<th colspan="2">包装修改：{$packing['title']}</th>
</tr>
</thead>
<tbody>
<tr class="even">
	<td>名称<span class="small">(中文)</span>：<span class="red bold">*</span></td>
	<td><input type="text" style="width:350px" size="26" name="title" value="{$packing['title']}"></td>
</tr>
<tr class="odd">
	<td>名称<span class="small">(英文)</span>：</td>
	<td><input type="text" style="width:350px" size="26" name="entitle" value="{$packing['entitle']}"></td>
</tr>
<tr class="even">
	<td>备　　注：</td>
	<td><input type="text" style="width:350px" size="26" name="remark" value="{$packing['remark']}"></td>
</tr>
<tr class="odd">
	<td>排　　序：</td>
	<td><input type="text" style="width:100px" size="26" name="ordering" value="{$packing['ordering']}"> <span class="small gray">填入整数，数字越小排位越靠前。</span></td>
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
		$this->kclass->page['title'] .= ' - 修改包装 - '.$packing['title'];
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 修改包装 - '.$packing['title'], 'right' => '<a href="/s.php?module=packing">返回列表</a>', 'body'=>$body));
	}
	//
	function doupdateAll(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			if(implode('', $this->kclass->input['title']) == ''){
				$e = '<li>请填写包装的 名称，不能留空。</li>';
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
				'title' => '修改全部包装',
				'text' => '您在修改包装的过程中有以下错误：<ul>'.$e.'</ul>',
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
					UPDATE `packing`
					SET ".$s." 
						`modified` = '".TIMENOW."', 
						`modifier` = '".$this->kclass->user['userid']."'
					WHERE packingid='".$k."'
				");
			}
		}
		$this->kclass->messager(array(
			'title' => '修改全部包装',
			'text' => '全部包装的信息已成功修改!',
			'url' => '/s.php?module=packing',
			'sec' => 2
		));
	}

	//
	function updateAll(){
		$gs = $this->kclass->DB->query("SELECT `packing`.* FROM `packing` WHERE `killed`=0");
		if($this->kclass->DB->numRows()){
			while($g = $this->kclass->DB->fetchArray($gs)){
				$t.='<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').'"><td><input type="hidden" name="orgTitle['.$g['packingid'].']" value="'.$g['title'].'"><input type="text" style="width:350px" size="26" name="title['.$g['packingid'].']" value="'.$g['title'].'" /></td><td><input type="hidden" name="orgEntitle['.$g['packingid'].']" value="'.$g['entitle'].'"><input type="text" style="width:350px" size="26" name="entitle['.$g['packingid'].']" value="'.$g['entitle'].'" /></td><td><input type="hidden" name="orgRemark['.$g['packingid'].']" value="'.$g['remark'].'"><input type="text" style="width:350px" size="26" name="remark['.$g['packingid'].']" value="'.$g['remark'].'" /></td><td><input type="hidden" name="orgOrdering['.$g['packingid'].']" value="'.$g['ordering'].'"><input type="text" style="width:20px" size="26" name="ordering['.$g['packingid'].']" value="'.$g['ordering'].'" /></td></tr>';
			}
		}else{
			$this->kclass->boinkIt('/s.php?module=packing');
		}
		$sline = $this->kclass->iif($this->kclass->rotate(), 'odd', 'even');
$body = <<<EOF
<form action="/s.php?module=packing&action=doupdateAll" name="packing" method="post">
<input type="hidden" name="module" value="packing">
<input type="hidden" name="action" value="doupdateAll">
<table><thead>
<tr>
	<th colspan="4">修改全部：<span class="darkred small">请谨慎修改，防止误修改！</span></th>
</tr>
</thead>
<tbody>
<tr>
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
		$this->kclass->page['title'] .= ' - 修改全部包装 - '.$packing['title'];
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 修改全部包装 - '.$packing['title'], 'right' => '<a href="/s.php?module=packing">返回列表</a>', 'body'=>$body));
	}
	//
	function kill(){
		if($this->kclass->input['packingid']<=0){
			$this->kclass->boinkIt('/s.php?module=packing');
		}
		if($this->kclass->input['confirm'] == 0){
			$this->kclass->boinkIt('/s.php?module=packing');
		}
		if($this->kclass->input['packingid'] < 0){
			$e = '<li>请从正确的页面进行操作，如果此问题持续存在，请联系网站管理员。';
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '删除包装',
				'text' => '您在删除包装的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$packing = $this->kclass->DB->queryFirst("
			SELECT title
			FROM packing
			WHERE packingid='".$this->kclass->input['packingid']."'
		");
		if($packing){
			$this->kclass->DB->query("
				UPDATE `packing`
				SET killed=1
				WHERE packingid='".$this->kclass->input['packingid']."'
			");
			$this->kclass->messager(array(
				'title' => '删除包装成功',
				'text' => '包装 <b>'.$packing['title'].'</b> 已成功被标记为删除!',
				'url' => '/s.php?module=packing',
				'sec' => 2
			));
		}else{
			$this->kclass->messager(array(
				'title' => '删除包装失败',
				'text' => '您要删除的包装，不存在！',
				'url' => '/s.php?module=packing#'.$this->kclass->input['packingid'],
				'sec' => 3
			));
		}
	}

	//
	function remove(){
		if($this->kclass->input['packingid']<=0){
			$this->kclass->boinkIt('/s.php?module=packing');
		}
		$packing = $this->kclass->DB->queryFirst("
			SELECT title
			FROM packing
			WHERE packingid='".$this->kclass->input['packingid']."'
		");
$body = <<<EOF
<form action="/s.php?module=packing&action=kill" name="packing" method="post">
<input type="hidden" name="module" value="packing">
<input type="hidden" name="action" value="kill">
<input type="hidden" name="packingid" value="{$this->kclass->input['packingid']}">
<table><thead>
<tr>
	<th>删除包装：{$packing['title']}</th>
</tr>
</thead>
<tbody>
<tr class="even">
	<td class="middle">你确定要删除包装: <a href="/s.php?module=packing&action=view&packingid={$this->kclass->input['packingid']}" class="big bold" target="_blank">{$packing['title']}</a> 吗?</td>
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
		$this->kclass->page['title'] .= ' - 删除包装 - '.$packing['title'];
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 删除包装 - '.$packing['title'], 'right' => '<a href="/s.php?module=packing">返回列表</a>', 'body'=>$body));
	}

	//
	function revival(){
		if($this->kclass->input['packingid']<=0){
			$this->kclass->boinkIt('/s.php?module=packing');
		}
		if($this->kclass->input['confirm'] == 0){
			$this->kclass->boinkIt('/s.php?module=packing');
		}
		if($this->kclass->input['packingid'] < 0){
			$e = '<li>请从正确的页面进行操作，如果此问题持续存在，请联系网站管理员。';
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '恢复包装',
				'text' => '您在恢复包装的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$packing = $this->kclass->DB->queryFirst("
			SELECT title
			FROM packing
			WHERE packingid='".$this->kclass->input['packingid']."'
		");
		if($packing){
			$this->kclass->DB->query("
				UPDATE `packing`
				SET killed=0
				WHERE packingid='".$this->kclass->input['packingid']."'
			");
			$this->kclass->messager(array(
				'title' => '恢复包装成功',
				'text' => '包装 <b>'.$packing['title'].'</b> 已成功被恢复！',
				'url' => '/s.php?module=packing',
				'sec' => 2
			));
		}else{
			$this->kclass->messager(array(
				'title' => '恢复包装失败',
				'text' => '您要恢复的包装不存在！',
				'url' => '/s.php?module=packing#'.$this->kclass->input['packingid'],
				'sec' => 3
			));
		}
	}

	//
	function restore(){
		if($this->kclass->input['packingid']<=0){
			$this->kclass->boinkIt('/s.php?module=packing');
		}
		$packing = $this->kclass->DB->queryFirst("
			SELECT title
			FROM packing
			WHERE packingid='".$this->kclass->input['packingid']."'
		");
$body = <<<EOF
<form action="/s.php?module=packing&action=revival" name="packing" method="post">
<input type="hidden" name="module" value="packing">
<input type="hidden" name="action" value="revival">
<input type="hidden" name="packingid" value="{$this->kclass->input['packingid']}">
<table><thead>
<tr>
	<th>恢复包装：{$packing['title']}</th>
</tr>
</thead>
<tbody>
<tr class="even">
	<td class="middle">你确定要恢复包装: <a href="/s.php?module=packing&action=view&packingid={$this->kclass->input['packingid']}" class="big bold" target="_blank">{$packing['title']}</a> 吗?</td>
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
		$this->kclass->page['title'] .= ' - 恢复包装 - '.$packing['title'];
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 恢复包装 - '.$packing['title'], 'right' => '<a href="/s.php?module=packing">返回列表</a>', 'body'=>$body));
	}
	// 
	function mmlist(){
		$bs = $this->kclass->DB->query("
			SELECT *
			FROM `packing`
			ORDER BY ordering DESC, modified DESC
		");
		if($this->kclass->DB->numRows()){
			$body = '<table class="eightypercent mytable"><thead>';
			$body .= '<tr><th>名称<span class="small">(中文)</span></th><th>名称<span class="small">(英文)</span></th><th>备注</th><th>排序</th><th align="center">修改日期</th><th align="center" class="small">操作</th></tr></thead><tbody>';
			while($b = $this->kclass->DB->fetchArray($bs)){
				$body .= '<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even');
				$body .= $this->kclass->iif($b['killed']==1, ' through', '').'"><td class="middle bold"><a name="'.$b['packingid'].'"></a>'.$b['title'].'</td><td class="bold">'.$b['entitle'].'</td><td>'.$b['remark'].'</td><td class="small">'.$b['ordering'].'</td><td class="small gray">'.date('Y-m-d H:i', $b['modified']).'</td>';
				if($b['killed']==1){
					$body .= '<td><a href="/s.php?module=packing&action=restore&packingid='.$b['packingid'].'">恢</a></td>';
				}else{
					$body .= '<td><a href="/s.php?module=packing&action=remove&packingid='.$b['packingid'].'" class="small">删</a> <a href="/s.php?module=packing&action=update&packingid='.$b['packingid'].'">改</a></td></tr>';
				}
			}
			$body .= '</tbody></table>';
		}else{
			$body = '暂无相关记录。';
		}

		$this->kclass->page['onload'] = 'dc.tabhover();$(\'.mytable\').fixedtableheader();';
		$this->kclass->page['title'] .= ' - 列表';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 列表', 'right' => '<span class="small">显示方式：</span><a href="/s.php?module=packing">默认列表</a>　<a href="/s.php?module=packing&action=list&show=all">所有<span class="small gray">(包括删除)</span></a>　|　<a href="/s.php?module=packing&action=updateAll">修改全部</a>　|　<a href="/s.php?module=packing&action=add">新建</a>', 'body'=>$body));
	}
}
?>