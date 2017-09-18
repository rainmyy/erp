<?php
class port{
	function autoRun(){
		$this->kclass->page['title'] = '港口';
		$this->baseurl = '<a href="/s.php">首页</a> - <a href="/s.php?module=port">港口</a>';
		$this->right = '<a href="/s.php?module=port">列表</a> <a href="/s.php?module=port&action=add">新建</a>';
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
				$e = '<li>请填写港口的 名称。</li>';
			}else{
				if($this->kclass->DB->queryFirst("SELECT portid FROM `port` WHERE `title`='".$this->kclass->input['title']."'")){
					$e = '<li>您要新建的港口 【'.$this->kclass->input['title'].'】 已经存在。</li>';
				}
			}
			if($this->kclass->input['abbr'] == ''){
				$e .= '<li>请填写港口的 英文简称。</li>';
			}elseif(strlen($this->kclass->input['abbr'])>10){
				$e .= '<li>请填写港口的英文简称不能超过 10 个英文字符。</li>';
			}
			if(!$this->kclass->input['regionid'] > 0){
				$e .= '<li>请填写港口的所属 国家。</li>';
			}
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '新建港口',
				'text' => '您在新建港口的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$this->kclass->DB->query("
			INSERT INTO `port` ( `title` , `entitle` , `abbr` , `regionid` , `remark` , `ordering` , `modified`, `creator` , `created`)
			VALUES (
			'".$this->kclass->input['title']."', '".$this->kclass->input['entitle']."', '".$this->kclass->input['abbr']."', '".$this->kclass->input['regionid']."', '".$this->kclass->input['remark']."', '".$this->kclass->input['ordering']."', '".TIMENOW."', '".$this->kclass->user['userid']."', '".TIMENOW."'
			)
		");

		$this->kclass->messager(array(
			'title' => '新建港口',
			'text' => '港口 <b>'.$this->kclass->input['title'].'</b> 已新建成功!',
			'url' => '/s.php?module=port#'.$this->kclass->DB->insertID(),
			'sec' => 2
		));
	}

	//
	function add(){
		$country = $this->kclass->chooserCountry(array('name'=>'regionid', 'width'=>356,'topname'=>'', 'hasBlank'=>1, 'displaytop'=>1));
$body = <<<EOF
<form action="/s.php?module=port&action=insert" name="port" method="post">
<input type="hidden" name="module" value="port">
<input type="hidden" name="action" value="insert">
<table><thead>
<tr>
	<th colspan="4">新建港口：</th>
</tr>
</thead>
<tbody>
<tr class="even">
	<td>名称<span class="small">(中文)</span>：<span class="red bold">*</span></td>
	<td><input type="text" style="width:350px" size="26" name="title" value="{$port['title']}"></td>
</tr>
<tr class="odd">
	<td>名称<span class="small">(英文)</span>：</td>
	<td><input type="text" style="width:350px" size="26" name="entitle" value="{$port['entitle']}"></td>
</tr>
<tr class="even">
	<td>简称<span class="small">(英文)</span>：<span class="red bold">*</span></td>
	<td><input type="text" style="width:350px" size="26" name="abbr" value="{$port['abbr']}"></td>
</tr>
<tr class="odd">
	<td valign="top">国　　家：<span class="red bold">*</span></td>
	<td>{$country}</td>
</tr>
<tr class="even">
	<td>备　　注：</td>
	<td><input type="text" style="width:350px" size="26" name="remark" value="{$port['remark']}"></td>
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
		$this->kclass->page['title'] .= ' - 新建港口';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 新建港口', 'right' => '<a href="/s.php?module=port">返回列表</a>', 'body'=>$body));
}

	//
	function doupdate(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			if($this->kclass->input['title'] == ''){
				$e = '<li>请填写港口的 名称。</li>';
			}else{
				if($port = $this->kclass->DB->queryFirst("SELECT portid FROM `port` WHERE `portid`<>'".$this->kclass->input['portid']."' AND `title`='".$this->kclass->input['title']."'")){
					$e = '<li>您要修改的港口 【'.$this->kclass->input['title'].'】 已经存在。</li>';
				}
			}
			if($this->kclass->input['abbr'] == ''){
				$e .= '<li>请填写港口的 英文简称。</li>';
			}elseif(strlen($this->kclass->input['abbr'])>10){
				$e .= '<li>请填写港口的英文简称不能超过 10 个英文字符。</li>';
			}
			
			if(!$this->kclass->input['regionid'] > 0){
				$e .= '<li>请填写港口的所属 国家。</li>';
			}
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '修改港口',
				'text' => '您在修改港口的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$this->kclass->DB->query("
			UPDATE `port`
			SET `title` = '".$this->kclass->input['title']."', 
				`entitle` = '".$this->kclass->input['entitle']."', 
				`abbr` = '".$this->kclass->input['abbr']."', 
				`regionid` = '".$this->kclass->input['regionid']."', 
				`remark` = '".$this->kclass->input['remark']."', 
				`ordering` = '".$this->kclass->input['ordering']."', 
				`modified` = '".TIMENOW."', 
				`modifier` = '".$this->kclass->user['userid']."'
			WHERE portid='".$this->kclass->input['portid']."'
		");
		$this->kclass->messager(array(
			'title' => '修改港口',
			'text' => '港口 <b>'.$this->kclass->input['title'].'</b> 的信息已成功修改!',
			'url' => '/s.php?module=port#'.$this->kclass->input['portid'],
			'sec' => 2
		));
	}

	//
	function update(){
		if($this->kclass->input['portid']<=0 OR !$port = $this->kclass->DB->queryFirst("SELECT `port`.* FROM `port` WHERE `portid`='".$this->kclass->input['portid']."'")){
			$this->kclass->boinkIt('/s.php?module=port');
		}
		$country = $this->kclass->chooserCountry(array('name'=>'regionid', 'width'=>356,'topname'=>'', 'displaytop'=>1, 'regionid'=>$port['regionid']));
$body = <<<EOF
<form action="/s.php?module=port&action=doupdate" name="port" method="post">
<input type="hidden" name="module" value="port">
<input type="hidden" name="action" value="doupdate">
<input type="hidden" name="portid" value="{$this->kclass->input['portid']}">
<table><thead>
<tr>
	<th colspan="2">港口修改：{$port['title']}</th>
</tr>
</thead>
<tbody>
<tr class="even">
	<td>名称<span class="small">(中文)</span>：<span class="red bold">*</span></td>
	<td><input type="text" style="width:350px" size="26" name="title" value="{$port['title']}"></td>
</tr>
<tr class="odd">
	<td>名称<span class="small">(英文)</span>：</td>
	<td><input type="text" style="width:350px" size="26" name="entitle" value="{$port['entitle']}"></td>
</tr>
<tr class="even">
	<td>简称<span class="small">(英文)</span>：<span class="red bold">*</span></td>
	<td><input type="text" style="width:350px" size="26" name="abbr" value="{$port['abbr']}"></td>
</tr>
<tr class="odd">
	<td valign="top">国　　家：<span class="red bold">*</span></td>
	<td>{$country}</td>
</tr>
<tr class="even">
	<td>备　　注：</td>
	<td><input type="text" style="width:350px" size="26" name="remark" value="{$port['remark']}"></td>
</tr>
<tr class="odd">
	<td>排　　序：</td>
	<td><input type="text" style="width:100px" size="26" name="ordering" value="{$port['ordering']}"> <span class="small gray">填入整数，数字越小排位越靠前。</span></td>
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
		$this->kclass->page['title'] .= ' - 修改港口 - '.$port['title'];
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 修改港口 - '.$port['title'], 'right' => '<a href="/s.php?module=port">返回列表</a>', 'body'=>$body));
	}
	//
	function kill(){
		if($this->kclass->input['portid']<=0){
			$this->kclass->boinkIt('/s.php?module=port');
		}
		if($this->kclass->input['confirm'] == 0){
			$this->kclass->boinkIt('/s.php?module=port');
		}
		if($this->kclass->input['portid'] < 0){
			$e = '<li>请从正确的页面进行操作，如果此问题持续存在，请联系网站管理员。';
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '删除港口',
				'text' => '您在删除港口的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$port = $this->kclass->DB->queryFirst("
			SELECT title
			FROM port
			WHERE portid='".$this->kclass->input['portid']."'
		");
		if($port){
			$this->kclass->DB->query("
				UPDATE `port`
				SET killed=1
				WHERE portid='".$this->kclass->input['portid']."'
			");
			$this->kclass->messager(array(
				'title' => '删除港口成功',
				'text' => '港口 <b>'.$port['title'].'</b> 已成功被标记为删除!',
				'url' => '/s.php?module=port',
				'sec' => 2
			));
		}else{
			$this->kclass->messager(array(
				'title' => '删除港口失败',
				'text' => '您要删除的港口，不存在！',
				'url' => '/s.php?module=port#'.$this->kclass->input['portid'],
				'sec' => 3
			));
		}
	}

	//
	function remove(){
		if($this->kclass->input['portid']<=0){
			$this->kclass->boinkIt('/s.php?module=port');
		}
		$port = $this->kclass->DB->queryFirst("
			SELECT title
			FROM port
			WHERE portid='".$this->kclass->input['portid']."'
		");
$body = <<<EOF
<form action="/s.php?module=port&action=kill" name="port" method="post">
<input type="hidden" name="module" value="port">
<input type="hidden" name="action" value="kill">
<input type="hidden" name="portid" value="{$this->kclass->input['portid']}">
<table><thead>
<tr>
	<td>删除港口：{$port['title']}</td>
</tr>
<tr class="even">
	<td class="middle">你确定要删除港口: <a href="/s.php?module=port&action=view&portid={$this->kclass->input['portid']}" class="big bold" target="_blank">{$port['title']}</a> 吗?</td>
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
</table>
</form>
EOF;
		$this->kclass->page['title'] .= ' - 删除港口 - '.$port['title'];
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 删除港口 - '.$port['title'], 'right' => '<a href="/s.php?module=port">返回列表</a>', 'body'=>$body));
	}

	//
	function revival(){
		if($this->kclass->input['portid']<=0){
			$this->kclass->boinkIt('/s.php?module=port');
		}
		if($this->kclass->input['confirm'] == 0){
			$this->kclass->boinkIt('/s.php?module=port');
		}
		if($this->kclass->input['portid'] < 0){
			$e = '<li>请从正确的页面进行操作，如果此问题持续存在，请联系网站管理员。';
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '恢复港口',
				'text' => '您在恢复港口的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$port = $this->kclass->DB->queryFirst("
			SELECT title
			FROM port
			WHERE portid='".$this->kclass->input['portid']."'
		");
		if($port){
			$this->kclass->DB->query("
				UPDATE `port`
				SET killed=0
				WHERE portid='".$this->kclass->input['portid']."'
			");
			$this->kclass->messager(array(
				'title' => '恢复港口成功',
				'text' => '港口 <b>'.$port['title'].'</b> 已成功被恢复！',
				'url' => '/s.php?module=port',
				'sec' => 2
			));
		}else{
			$this->kclass->messager(array(
				'title' => '恢复港口失败',
				'text' => '您要恢复的港口不存在！',
				'url' => '/s.php?module=port#'.$this->kclass->input['portid'],
				'sec' => 3
			));
		}
	}

	//
	function restore(){
		if($this->kclass->input['portid']<=0){
			$this->kclass->boinkIt('/s.php?module=port');
		}
		$port = $this->kclass->DB->queryFirst("
			SELECT title
			FROM port
			WHERE portid='".$this->kclass->input['portid']."'
		");
$body = <<<EOF
<form action="/s.php?module=port&action=revival" name="port" method="post">
<input type="hidden" name="module" value="port">
<input type="hidden" name="action" value="revival">
<input type="hidden" name="portid" value="{$this->kclass->input['portid']}">
<table><thead>
<tr>
	<td>恢复港口：{$port['title']}</td>
</tr>
<tr class="even">
	<td class="middle">你确定要恢复港口: <a href="/s.php?module=port&action=view&portid={$this->kclass->input['portid']}" class="big bold" target="_blank">{$port['title']}</a> 吗?</td>
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
</table>
</form>
EOF;
		$this->kclass->page['title'] .= ' - 恢复港口 - '.$port['title'];
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 恢复港口 - '.$port['title'], 'right' => '<a href="/s.php?module=port">返回列表</a>', 'body'=>$body));
	}
	// 
	function mmlist(){
		if($this->kclass->input['show']=='all'){
			$condition='1=1';
		}else{
			$condition='`region`.killed=0';
		}
		$bs = $this->kclass->DB->query("
			SELECT `port`.*, 
				region.country, region.encountry
			FROM `port`
			LEFT JOIN `region` ON (region.regionid=`port`.regionid)
			WHERE ".$condition."
			ORDER BY region.country ASC, `port`.entitle ASC, `port`.ordering DESC, `port`.modified DESC
		");
		if($this->kclass->DB->numRows()){
			$body = '<table class="eightypercent mytable"><thead>';
			$body .= '<tr><th>名称<span class="small">(中文)</span></th><th>名称<span class="small">(英文)</span></th><th>简称</th><th>备注</th><th>排序</th><th align="center" class="small">操作</th></tr></thead><tbody>';
			$country='';
			while($b = $this->kclass->DB->fetchArray($bs)){
				if($country != $b['country']){
					$body .= '<tr><td colspan="10" class="big bold"><a href="/s.php?module=region&action=view&regionid='.$b['regionid'].'" target="_blank">'.$b['country'].'</a> <span class="small">（英文：<span class="middle bold">'.$b['encountry'].'</span>）</span></td></tr>';
					$country = $b['country'];
				}
				$body .= '<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even');
				$body .= $this->kclass->iif($b['killed']==1, ' through', '').'"><td class="middle bold"><a name="'.$b['portid'].'"></a>　　'.$b['title'].'</td><td class="bold">'.$b['entitle'].'</td><td>'.$b['abbr'].'</td><td>'.$b['remark'].'</td><td align="right" class="small">'.$b['ordering'].'</td>';
				if($b['killed']==1){
					$body .= '<td><a href="/s.php?module=port&action=restore&portid='.$b['portid'].'">恢</a></td>';
				}else{
					$body .= '<td align="right"><a href="/s.php?module=port&action=remove&portid='.$b['portid'].'" class="small">删</a> <a href="/s.php?module=port&action=update&portid='.$b['portid'].'">改</a></td></tr>';
				}
			}
			$body .= '</tbody></table>';
		}else{
			$body = '暂无相关记录。';
		}

		$this->kclass->page['onload'] = 'dc.tabhover();$(\'.mytable\').fixedtableheader();';
		$this->kclass->page['title'] .= ' - 列表';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 列表', 'right' => '<span class="small">显示方式：</span><a href="/s.php?module=port">默认列表</a>　<a href="/s.php?module=port&action=list&show=all">所有<span class="small gray">(包括删除)</span></a>　|　<a href="/s.php?module=port&action=add">新建</a>', 'body'=>$body));
	}
}
?>