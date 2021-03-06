<?php
class currency{
	function autoRun(){
		$this->kclass->page['title'] = '币种';
		$this->baseurl = '<a href="/s.php">首页</a> - <a href="/s.php?module=currency">币种</a>';
		$this->right = '<a href="/s.php?module=currency">列表</a> <a href="/s.php?module=currency&action=add">新建</a>';
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
				$e = '<li>请填写币种的 名称。</li>';
			}else{
				if($this->kclass->DB->queryFirst("SELECT currencyid FROM `currency` WHERE `title`='".$this->kclass->input['title']."'")){
					$e = '<li>您要新建的币种 【'.$this->kclass->input['title'].'】 已经存在。</li>';
				}
			}
			if($this->kclass->input['symbol']==''){
				$e .= '<li>请输入币种的 符号。</li>';
			}
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '新建币种',
				'text' => '您在新建币种的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$this->kclass->DB->query("
			INSERT INTO `currency` ( `title` ,   `symbol` , `remark` , `ordering` , `modified`, `creator` , `created`)
			VALUES (
			'".$this->kclass->input['title']."', '".$this->kclass->input['symbol']."', '".$this->kclass->input['remark']."', '".$this->kclass->input['ordering']."', '".TIMENOW."', '".$this->kclass->user['userid']."', '".TIMENOW."'
			)
		");

		$this->kclass->messager(array(
			'title' => '新建币种',
			'text' => '币种 <b>'.$this->kclass->input['title'].'</b> 已新建成功!',
			'url' => '/s.php?module=currency#'.$this->kclass->DB->insertID(),
			'sec' => 2
		));
	}

	//
	function add(){
$body = <<<EOF
<form action="/s.php?module=currency&action=insert" name="currency" method="post">
<input type="hidden" name="module" value="currency">
<input type="hidden" name="action" value="insert">
<table><thead>
<tr>
	<th colspan="4">新建币种：</th>
</tr>
</thead>
<tbody>
<tr class="even">
	<td>币种名称：<span class="red bold">*</span></td>
	<td><input type="text" style="width:350px" size="26" name="title" value="{$currency['title']}"></td>
</tr>
<tr class="odd">
	<td>币种符号：<span class="red bold">*</span></td>
	<td><input type="text" style="width:350px" size="26" name="symbol" value="{$currency['symbol']}"></td>
</tr>
<tr class="even">
	<td>备　　注：</td>
	<td><input type="text" style="width:350px" size="26" name="remark" value="{$currency['remark']}"></td>
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
		$this->kclass->page['title'] .= ' - 新建币种';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 新建币种', 'right' => '<a href="/s.php?module=currency">返回列表</a>', 'body'=>$body));
}

	//
	function doupdate(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			if($this->kclass->input['title'] == ''){
				$e = '<li>请填写币种的 名称。</li>';
			}else{
				if($currency = $this->kclass->DB->queryFirst("SELECT currencyid FROM `currency` WHERE `currencyid`<>'".$this->kclass->input['currencyid']."' AND `title`='".$this->kclass->input['title']."'")){
					$e = '<li>您要修改的币种 【'.$this->kclass->input['title'].'】 已经存在。</li>';
				}
			}
			if($this->kclass->input['symbol']==''){
				$e .= '<li>请输入币种的 符号。</li>';
			}
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '修改币种',
				'text' => '您在修改币种的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$this->kclass->DB->query("
			UPDATE `currency`
			SET `title` = '".$this->kclass->input['title']."', 
				`symbol` = '".$this->kclass->input['symbol']."', 
				`remark` = '".$this->kclass->input['remark']."', 
				`ordering` = '".$this->kclass->input['ordering']."', 
				`modified` = '".TIMENOW."', 
				`modifier` = '".$this->kclass->user['userid']."'
			WHERE currencyid='".$this->kclass->input['currencyid']."'
		");
		$this->kclass->messager(array(
			'title' => '修改币种',
			'text' => '币种 <b>'.$this->kclass->input['title'].'</b> 的信息已成功修改!',
			'url' => '/s.php?module=currency#'.$this->kclass->input['currencyid'],
			'sec' => 2
		));
	}

	//
	function update(){
		if($this->kclass->input['currencyid']<=0 OR !$currency = $this->kclass->DB->queryFirst("SELECT `currency`.* FROM `currency` WHERE `currencyid`='".$this->kclass->input['currencyid']."'")){
			$this->kclass->boinkIt('/s.php?module=currency');
		}
$body = <<<EOF
<form action="/s.php?module=currency&action=doupdate" name="currency" method="post">
<input type="hidden" name="module" value="currency">
<input type="hidden" name="action" value="doupdate">
<input type="hidden" name="currencyid" value="{$this->kclass->input['currencyid']}">
<table><thead>
<tr>
	<th colspan="2">币种修改：{$currency['title']}</th>
</tr>
</thead>
<tbody>
<tr class="even">
	<td>币种名称：<span class="red bold">*</span></td>
	<td><input type="text" style="width:350px" size="26" name="title" value="{$currency['title']}"></td>
</tr>
<tr class="odd">
	<td>币种符号：<span class="red bold">*</span></td>
	<td><input type="text" style="width:350px" size="26" name="symbol" value="{$currency['symbol']}"></td>
</tr>
<tr class="even">
	<td>备　　注：</td>
	<td><input type="text" style="width:350px" size="26" name="remark" value="{$currency['remark']}"></td>
</tr>
<tr class="odd">
	<td>排　　序：</td>
	<td><input type="text" style="width:100px" size="26" name="ordering" value="{$currency['ordering']}"> <span class="small gray">填入整数，数字越小排位越靠前。</span></td>
</tr>
<tr class="even">
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
		$this->kclass->page['title'] .= ' - 修改币种 - '.$currency['title'];
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 修改币种 - '.$currency['title'], 'right' => '<a href="/s.php?module=currency">返回列表</a>', 'body'=>$body));
	}
	//
	function doupdateAll(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			if(implode('', $this->kclass->input['title']) == ''){
				$e = '<li>请填写币种的 名称，不能留空。</li>';
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
				'title' => '修改币种',
				'text' => '您在修改币种的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		foreach($this->kclass->input['orgTitle'] as $k=>$v){
			$s='';
			if($this->kclass->input['orgTitle'][$k] != $this->kclass->input['title'][$k]){
				$s = "`title`='".$this->kclass->input['title'][$k]."', ";
			}
			if($this->kclass->input['orgEntitle'][$k] != $this->kclass->input['symbol'][$k]){
				$s .= "`symbol`='".$this->kclass->input['symbol'][$k]."', ";
			}
			if($this->kclass->input['orgRemark'][$k] != $this->kclass->input['remark'][$k]){
				$s .= "`remark`='".$this->kclass->input['remark'][$k]."', ";
			}
			if($this->kclass->input['orgOrdering'][$k] != $this->kclass->input['ordering'][$k]){
				$s .= "`ordering`='".$this->kclass->input['ordering'][$k]."', ";
			}
			if($s != ''){
				$this->kclass->DB->query("
					UPDATE `currency`
					SET ".$s." 
						`modified` = '".TIMENOW."', 
						`modifier` = '".$this->kclass->user['userid']."'
					WHERE currencyid='".$k."'
				");
			}
		}
		$this->kclass->messager(array(
			'title' => '修改币种',
			'text' => '全部币种的信息已成功修改!',
			'url' => '/s.php?module=currency',
			'sec' => 2
		));
	}

	//
	function updateAll(){
		$gs = $this->kclass->DB->query("SELECT `currency`.* FROM `currency` WHERE `killed`=0");
		if($this->kclass->DB->numRows()){
			while($g = $this->kclass->DB->fetchArray($gs)){
				$t.='<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').'"><td><input type="hidden" name="orgTitle['.$g['currencyid'].']" value="'.$g['title'].'"><input type="text" style="width:350px" size="26" name="title['.$g['currencyid'].']" value="'.$g['title'].'" /></td><td><input type="hidden" name="orgEntitle['.$g['currencyid'].']" value="'.$g['symbol'].'"><input type="text" style="width:350px" size="26" name="symbol['.$g['currencyid'].']" value="'.$g['symbol'].'" /></td><td><input type="hidden" name="orgRemark['.$g['currencyid'].']" value="'.$g['remark'].'"><input type="text" style="width:350px" size="26" name="remark['.$g['currencyid'].']" value="'.$g['remark'].'" /></td><td><input type="hidden" name="orgOrdering['.$g['currencyid'].']" value="'.$g['ordering'].'"><input type="text" style="width:20px" size="26" name="ordering['.$g['currencyid'].']" value="'.$g['ordering'].'" /></td></tr>';
			}
		}else{
			$this->kclass->boinkIt('/s.php?module=currency');
		}
		$sline = $this->kclass->iif($this->kclass->rotate(), 'odd', 'even');
$body = <<<EOF
<form action="/s.php?module=currency&action=doupdateAll" name="currency" method="post">
<input type="hidden" name="module" value="currency">
<input type="hidden" name="action" value="doupdateAll">
<table><thead>
<tr>
	<td colspan="4">修改全部：<span class="darkred small">请谨慎修改，防止误修改！</span></td>
</tr>
<tr>
	<td>币种币种名称 <span class="red bold">*</span></td>
	<td>币种符号</td>
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
</table>
</form>
EOF;
		$this->kclass->tbline+=2;
		$this->kclass->page['title'] .= ' - 修改币种 - '.$currency['title'];
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 修改币种 - '.$currency['title'], 'right' => '<a href="/s.php?module=currency">返回列表</a>', 'body'=>$body));
	}
	//
	function kill(){
		if($this->kclass->input['currencyid']<=0){
			$this->kclass->boinkIt('/s.php?module=currency');
		}
		if($this->kclass->input['confirm'] == 0){
			$this->kclass->boinkIt('/s.php?module=currency');
		}
		if($this->kclass->input['currencyid'] < 0){
			$e = '<li>请从正确的页面进行操作，如果此问题持续存在，请联系网站管理员。';
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '删除币种',
				'text' => '您在删除币种的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$currency = $this->kclass->DB->queryFirst("
			SELECT title
			FROM currency
			WHERE currencyid='".$this->kclass->input['currencyid']."'
		");
		if($currency){
			$this->kclass->DB->query("
				UPDATE `currency`
				SET killed=1
				WHERE currencyid='".$this->kclass->input['currencyid']."'
			");
			$this->kclass->messager(array(
				'title' => '删除币种成功',
				'text' => '币种 <b>'.$currency['title'].'</b> 已成功被标记为删除!',
				'url' => '/s.php?module=currency',
				'sec' => 2
			));
		}else{
			$this->kclass->messager(array(
				'title' => '删除币种失败',
				'text' => '您要删除的币种，不存在！',
				'url' => '/s.php?module=currency#'.$this->kclass->input['currencyid'],
				'sec' => 3
			));
		}
	}

	//
	function remove(){
		if($this->kclass->input['currencyid']<=0){
			$this->kclass->boinkIt('/s.php?module=currency');
		}
		$currency = $this->kclass->DB->queryFirst("
			SELECT title
			FROM currency
			WHERE currencyid='".$this->kclass->input['currencyid']."'
		");
$body = <<<EOF
<form action="/s.php?module=currency&action=kill" name="currency" method="post">
<input type="hidden" name="module" value="currency">
<input type="hidden" name="action" value="kill">
<input type="hidden" name="currencyid" value="{$this->kclass->input['currencyid']}">
<table><thead>
<tr>
	<th>删除币种：{$currency['title']}</th>
</tr>
</thead>
<tbody>
<tr class="even">
	<td class="middle">你确定要删除币种: <a href="/s.php?module=currency&action=view&currencyid={$this->kclass->input['currencyid']}" class="big bold" target="_blank">{$currency['title']}</a> 吗?</td>
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
		$this->kclass->page['title'] .= ' - 删除币种 - '.$currency['title'];
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 删除币种 - '.$currency['title'], 'right' => '<a href="/s.php?module=currency">返回列表</a>', 'body'=>$body));
	}

	//
	function revival(){
		if($this->kclass->input['currencyid']<=0){
			$this->kclass->boinkIt('/s.php?module=currency');
		}
		if($this->kclass->input['confirm'] == 0){
			$this->kclass->boinkIt('/s.php?module=currency');
		}
		if($this->kclass->input['currencyid'] < 0){
			$e = '<li>请从正确的页面进行操作，如果此问题持续存在，请联系网站管理员。';
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '恢复币种',
				'text' => '您在恢复币种的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$currency = $this->kclass->DB->queryFirst("
			SELECT title
			FROM currency
			WHERE currencyid='".$this->kclass->input['currencyid']."'
		");
		if($currency){
			$this->kclass->DB->query("
				UPDATE `currency`
				SET killed=0
				WHERE currencyid='".$this->kclass->input['currencyid']."'
			");
			$this->kclass->messager(array(
				'title' => '恢复币种成功',
				'text' => '币种 <b>'.$currency['title'].'</b> 已成功被恢复！',
				'url' => '/s.php?module=currency',
				'sec' => 2
			));
		}else{
			$this->kclass->messager(array(
				'title' => '恢复币种失败',
				'text' => '您要恢复的币种不存在！',
				'url' => '/s.php?module=currency#'.$this->kclass->input['currencyid'],
				'sec' => 3
			));
		}
	}

	//
	function restore(){
		if($this->kclass->input['currencyid']<=0){
			$this->kclass->boinkIt('/s.php?module=currency');
		}
		$currency = $this->kclass->DB->queryFirst("
			SELECT title
			FROM currency
			WHERE currencyid='".$this->kclass->input['currencyid']."'
		");
$body = <<<EOF
<form action="/s.php?module=currency&action=revival" name="currency" method="post">
<input type="hidden" name="module" value="currency">
<input type="hidden" name="action" value="revival">
<input type="hidden" name="currencyid" value="{$this->kclass->input['currencyid']}">
<table><thead>
<tr>
	<th>恢复币种：{$currency['title']}</th>
</tr>
</thead>
<tbody>
<tr class="even">
	<td class="middle">你确定要恢复币种: <a href="/s.php?module=currency&action=view&currencyid={$this->kclass->input['currencyid']}" class="big bold" target="_blank">{$currency['title']}</a> 吗?</td>
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
		$this->kclass->page['title'] .= ' - 恢复币种 - '.$currency['title'];
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 恢复币种 - '.$currency['title'], 'right' => '<a href="/s.php?module=currency">返回列表</a>', 'body'=>$body));
	}
	// 
	function mmlist(){
		if($this->kclass->input['show']=='all'){
			$condition='1=1';
		}else{
			$condition='`currency`.killed=0';
		}
		$bs = $this->kclass->DB->query("
			SELECT *
			FROM `currency`
			WHERE ".$condition."
			ORDER BY ordering DESC, modified DESC
		");
		if($this->kclass->DB->numRows()){
			$body = '<table class="eightypercent">';
			$body .= '<thead><tr><th>币种名称</th><th>币种符号</th><th>备注</th><th>排序</th><th align="center">修改日期</th><th align="center" class="small">操作</th></tr></thead><tbody>';
			while($b = $this->kclass->DB->fetchArray($bs)){
				$body .= '<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even');
				$body .= $this->kclass->iif($b['killed']==1, ' through', '').'"><td class="middle bold"><a name="'.$b['currencyid'].'"></a>'.$b['title'].'</td><td class="bold">'.$b['symbol'].'</td><td>'.$b['remark'].'</td><td class="small">'.$b['ordering'].'</td><td class="small gray">'.date('Y-m-d H:i:s', $b['modified']).'</td>';
				if($b['killed']==1){
					$body .= '<td><a href="/s.php?module=currency&action=restore&currencyid='.$b['currencyid'].'">恢</a></td>';
				}else{
					$body .= '<td><a href="/s.php?module=currency&action=remove&currencyid='.$b['currencyid'].'" class="small">删</a> <a href="/s.php?module=currency&action=update&currencyid='.$b['currencyid'].'">改</a></td></tr>';
				}
			}
			$body .= '</tbody></table>';
		}else{
			$body = '暂无相关记录。';
		}

		$this->kclass->page['onload'] = 'dc.tabhover()';
		$this->kclass->page['title'] .= ' - 列表';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 列表', 'right' => '<span class="small">显示方式：</span><a href="/s.php?module=currency">默认列表</a>　<a href="/s.php?module=currency&action=list&show=all">所有<span class="small gray">(包括删除)</span></a>　|　<a href="/s.php?module=currency&action=updateAll">修改全部</a>　|　<a href="/s.php?module=currency&action=add">新建</a>', 'body'=>$body));
	}
}
?>