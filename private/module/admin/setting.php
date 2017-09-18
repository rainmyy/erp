<?php
//
class setting{
	//
	function autoRun(){
		$this->kclass->page['title'] = '系统配置';
		$this->baseurl = '<a href="/a.php">首页</a> - <a href="/a.php?module=user">系统配置</a>';
		$this->right = '<a href="/a.php?module=setting&action=options">全局设置</a>　<a href="/a.php?module=setting&action=dft">默认值设置</a>　<a href="/a.php?module=setting&action=attach">附件管理</a>　<a href="/a.php?module=setting&action=module">模块管理</a>　<a href="/a.php?module=setting&action=menu">菜单管理</a>';
		switch($this->kclass->input['action']){
			case 'options':
				$this->options();
			break;
			case 'dft':
				$this->dft();
			break;
			case 'attach':
				$this->attach();
			break;
			case 'module':
				$this->module();
			break;
			case 'mOrdering':
				$this->mOrdering();
			break;
			case 'mUpdate':
				$this->mUpdate();
			break;
			case 'mDoupdate':
				$this->mDoupdate();
			break;
			case 'mAdd':
				$this->mAdd();
			break;
			case 'mInsert':
				$this->mInsert();
			break;
			case 'mCopyAll':
				$this->mCopyAll();
			break;
			case 'mDoCopyAll':
				$this->mDoCopyAll();
			break;
			case 'mRemove':
				$this->mRemove();
			break;
			case 'mKill':
				$this->mKill();
			break;
			case 'mRevival':
				$this->mRevival();
			break;
			case 'mRestore':
				$this->mRestore();
			break;
			case 'menu':
				$this->menu();
			break;
			case 'mnOrdering':
				$this->mnOrdering();
			break;
			case 'mnUpdate':
				$this->mnUpdate();
			break;
			case 'mnDoupdate':
				$this->mnDoupdate();
			break;
			case 'mnAdd':
				$this->mnAdd();
			break;
			case 'mnInsert':
				$this->mnInsert();
			break;
			case 'mnRemove':
				$this->mnRemove();
			break;
			case 'mnKill':
				$this->mnKill();
			break;
			default:
				$this->options();
		}
	}
	//
	function options(){

	}
	//
	function dft(){

	}
	//
	function attach(){

	}
	//
	function mInsert(){
		if($this->kclass->input['title'][1] == ''){
			$e = '<li>请输入模块名称。</li>';
		}
		if($this->kclass->input['mdl'][1] == ''){
			$e .= '<li>请输入模块。</li>';
		}
		if($e != ''){
			$this->kclass->messager(array(
				'title' => '添加新模块',
				'text' => '您在修改用户信息过程中有以下错误: <ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}

		foreach($this->kclass->input['title'] as $k => $v){
			if($v != '' AND $this->kclass->input['mdl'][$k] != ''){
				$this->kclass->DB->query("
					INSERT INTO `module` (`parentid`, `title`, `module`, `action`, `remark` , `ordering`, `modified`, `created`, `creator`)
					VALUES (
						'".$this->kclass->input['parentid']."', '".$v."', '".$this->kclass->input['mdl'][$k]."', '".$this->kclass->input['atn'][$k]."', '".$this->kclass->input['remark'][$k]."', '".$this->kclass->input['ordering'][$k]."', '".TIMENOW."', '".TIMENOW."' , '".$this->kclass->user['userid']."'
					)
				");
			}
		}
		$this->kclass->messager(array(
			'title' => '添加新模块',
			'text' => '新模块已经添加。',
			'url' => '/a.php?module=setting&action=module'.$this->kclass->iif($this->kclass->input['parentid']>0, '#'.$this->kclass->input['parentid'], ''),
			'sec' => 1
		));
	}
	//
	function mAdd(){
		if($this->kclass->input['parentid']>0 AND $p = $this->kclass->DB->queryFirst("SELECT moduleid, title, module FROM `module` WHERE moduleid='".$this->kclass->input['parentid']."'")){
			$t .= '<thead><tr><th>上级名称</th><th>模块名称<span class="red bold">*</span></th><th>模块<span class="red bold">*</span></th><th>动作</th><th>备注</th><th>排序</th></tr></thead><tbody>';
			for($i = 1; $i < 12; $i++){
				$t .= '<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').'"><td class="middle">'.$p['title'].'</td><td><input type="text" name="title['.$i.']" size="10"></td><td><input type="text" name="mdl['.$i.']" value="'.$p['module'].'" size="10"></td><td><input type="text" name="atn['.$i.']" size="10"></td><td><input type="text" name="remark['.$i.']" size="30"></td><td><input type="text" name="ordering['.$i.']" value="1" size="1" class="small"></td></tr>';
			}
		}else{
			$m = $this->kclass->DB->queryFirst("SELECT MAX(ordering) AS n FROM `module` WHERE `parentid`=0");
			$t = '<thead><tr><th colspan="2">添加新模块</th></tr></thead><tbody><tr class="odd"><td>名称：<span class="red bold">*</span></td><td><input type="text" name="title[1]" size="30" /></td></tr><tr class="even"><td>模块：<span class="red bold">*</span></td><td><input type="text" name="mdl[1]" size="30" /></td></tr><tr class="odd"><td>动作：</td><td><input type="text" name="atn[1]" size="30" /></td></tr><tr class="even"><td>备注：</td><td><input type="text" name="remark[1]" size="30" /></td></tr><tr><td>排序：</td><td><input type="text" name="ordering[1]" value="'.($m['n']+1).'" size="30"></td></tr>';
		}
$body = <<<EOF
<form name="setting" method="post" action="/a.php?module=setting&action=mInsert">
<input type="hidden" name="module" value="setting">
<input type="hidden" name="action" value="mInsert">
<input type="hidden" name="parentid" value="{$this->kclass->input['parentid']}">
<table>
{$t}
<tr class="even">
	<td align="center" colspan="8">
		<input type="submit" value="    保存    " accesskey="g" tabindex="7">
		<input type="reset" value="    重置    " accesskey="r" tabindex="8">
	</td>
</tr>
</tbody>
</table>
</form>
EOF;
		$this->kclass->page['onload'] = 'dc.tabhover()';
		$this->kclass->page['title'] .= ' - 模块管理 - 添加新模块';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title'=>$this->baseurl.' - <a href="/a.php?module=setting&action=module">模块管理</a> - 添加新模块', 'right' => $this->right, 'body'=>$body));
	}
	//
	function mDoCopyAll(){
		if($this->kclass->input['fromid']<=0){
			$e = '<li>要复制的模块ID缺失，请返回列表刷新继续操作。</li>';
		}elseif(!$p = $this->kclass->DB->queryFirst("SELECT moduleid, title FROM `module` WHERE killed=0 AND parentid=0 AND moduleid='".$this->kclass->input['fromid']."'")){
			$e = '<li>要复制的模块不存在或已被删除，请返回列表刷新继续操作。</li>';
		}elseif($this->kclass->input['toid']<=0){
			$e .= '<li>请选择要复制到的模块。</li>';
		}elseif(!$t = $this->kclass->DB->queryFirst("SELECT moduleid, title, module FROM `module` WHERE killed=0 AND parentid=0 AND moduleid='".$this->kclass->input['toid']."'")){
			$e .= '<li>选择要复制到的模块并不存在，请返回列表刷新后再操作。</li>';
		}else{
			$froms = $this->kclass->DB->query("SELECT * FROM `module` WHERE killed=0 AND parentid<>0 AND parentid='".$this->kclass->input['fromid']."'");
			if($i = $this->kclass->DB->numRows()==0){
				$e = '<li>被复制的模块下并没有任何子项目。</li>';
			}
		}
		if($e != ''){
			$this->kclass->messager(array(
				'title' => '复制模块子项目',
				'text' => '您在复制模块子项目过程中有以下错误: <ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}else{
			while($from = $this->kclass->DB->fetchArray($froms)){
				$this->kclass->DB->query("
					INSERT INTO `module` (`parentid`, `title`, `module`, `action`, `remark` , `ordering`, `modified`, `created`, `creator`)
					VALUES (
						'".$this->kclass->input['toid']."', '".$from['title']."', '".$t['module']."', '".$from['action']."', '".$from['remark']."', '".$from['ordering']."', '".TIMENOW."', '".TIMENOW."' , '".$this->kclass->user['userid']."'
					)
				");
			}
			$this->kclass->messager(array(
				'title' => '复制模块子项目',
				'text' => '共成功复制了<b>'.$i.'</b>个项目。',
				'url' => '/a.php?module=setting&action=module#'.$this->kclass->iif($this->kclass->input['toid']>0, $this->kclass->input['toid'], $this->kclass->input['fromid']),
				'sec' => 1
			));
		}
	}
	//
	function mCopyAll(){
		$e='';
		if($this->kclass->input['fromid']<=0){
			$e = '<li>要复制的模块ID缺失，请返回列表刷新继续操作。</li>';
		}elseif(!$p = $this->kclass->DB->queryFirst("SELECT moduleid, title FROM `module` WHERE killed=0 AND parentid=0 AND moduleid='".$this->kclass->input['fromid']."'")){
			$e = '<li>要复制的模块不存在或已被删除，请返回列表刷新继续操作。</li>';
		}elseif($c = $this->kclass->DB->queryFirst("SELECT count(moduleid) AS i FROM `module` WHERE parentid='".$this->kclass->input['fromid']."'") == 0){
			$e = '<li>选择的模块下并未有子项目，请重新选择。</li>';
		}else{
			$tos = $this->kclass->DB->query("SELECT moduleid, title, module FROM `module` WHERE killed=0 AND parentid=0 AND moduleid<>'".$this->kclass->input['fromid']."' ORDER BY modified DESC, created DESC");
			if($this->kclass->DB->numRows()){
				$ts = '<select name="toid"><option value="-1"> </option>';
				while($to = $this->kclass->DB->fetchArray($tos)){
					$ts .= '<option value="'.$to['moduleid'].'">'.$to['title'].'</option>';
				}
				$ts .= '</select>';
			}else{
				$e = '<li>您在复制前需要先建立一个新的模块，然后才能复制子项到新建的模块中。</li>';
			}
		}

		if($e!=''){
			$this->kclass->messager(array(
				'title' => '复制模块子项目',
				'text' => $e,
				'url' => '/a.php?module=setting&action=module',
				'sec' => 2
			));
		}else{
$body = <<<EOF
<form name="setting" method="post" action="/a.php?module=setting&action=mDoCopyAll">
<input type="hidden" name="module" value="setting">
<input type="hidden" name="action" value="mDoCopyAll">
<input type="hidden" name="fromid" value="{$this->kclass->input['fromid']}">
<table><thead>
<tr><th colspan="2">复制模块子项</th></tr>
</thead>
<tbody>
<tr class="odd">
	<td>复制自：</td>
	<td class="bold darkred">{$p['title']}</td>
</tr>
<tr class="even">
	<td>复制到：</td>
	<td>{$ts}</td>
</tr>
<tr class="odd">
	<td align="center" colspan="8">
		<input type="submit" name="submit" value="    保存    " accesskey="g" tabindex="7">
		<input type="reset" value="    重置    " accesskey="r" tabindex="8">
	</td>
</tr>
</tbody>
</table>
</form>
EOF;
		$this->kclass->page['onload'] = 'dc.tabhover()';
			$this->kclass->page['title'] .= ' - 模块管理 - 复制模块子项目';
			$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title'=>$this->baseurl.' - <a href="/a.php?module=setting&action=module">模块管理</a> - 复制模块子项目', 'right' => $this->right, 'body'=>$body));
		}
	}
	//
	function mDoupdate(){
		if($this->kclass->input['title'] == ''){
			$e = '<li>请输入模块名称。</li>';
		}
		if($this->kclass->input['mdl'] == ''){
			$e .= '<li>请输入模块。</li>';
		}
		if($e != ''){
			$this->kclass->messager(array(
				'title' => '修改模块',
				'text' => '您在修改模块过程中有以下错误: <ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}

		$this->kclass->DB->query("
			UPDATE `module`
			SET `title`='".$this->kclass->input['title']."',
				`module`='".$this->kclass->input['mdl']."',
				`action`='".$this->kclass->input['atn']."',
				`remark`='".$this->kclass->input['remark']."',
				`ordering`='".$this->kclass->input['ordering']."',
				`modified` = '".TIMENOW."',
				`modifier` = '".$this->kclass->user['userid']."'
			WHERE `moduleid`='".$this->kclass->input['moduleid']."'
		");
		$this->kclass->messager(array(
			'title' => '修改模块',
			'text' => '模块已经修改。',
			'url' => '/a.php?module=setting&action=module'.$this->kclass->iif($this->kclass->input['parentid']>0, '#'.$this->kclass->input['parentid'], ''),
			'sec' => 0
		));
	}
	//
	function mUpdate(){
		$m = $this->kclass->DB->queryFirst("SELECT * FROM `module` WHERE `module`.`moduleid`='".$this->kclass->input['moduleid']."'");
$body = <<<EOF
<form name="setting" method="post" action="/a.php?module=setting&action=mDoupdate">
<input type="hidden" name="module" value="setting">
<input type="hidden" name="action" value="mDoupdate">
<input type="hidden" name="moduleid" value="{$m['moduleid']}">
<input type="hidden" name="parentid" value="{$m['parentid']}">
<table><thead>
<tr><th colspan="2">修改模块</th></tr>
</thead>
<tbody>
<tr class="odd">
	<td>名称：<span class="red bold">*</span></td>
	<td><input type="text" name="title" size="30" value="{$m['title']}" /></td>
</tr>
<tr class="even">
	<td>模块：<span class="red bold">*</span></td>
	<td><input type="text" name="mdl" size="30" value="{$m['module']}" /></td>
</tr>
<tr class="odd">
	<td>动作：</td>
	<td><input type="text" name="atn" size="30" value="{$m['action']}" /></td>
</tr>
<tr class="even">
	<td>备注：</td><td><input type="text" name="remark" size="30" value="{$m['remark']}" /></td>
</tr>
<tr class="odd">
	<td>排序：</td><td><input type="text" name="ordering" value="{$m['ordering']}" size="30"></td>
</tr>
<tr class="even">
	<td align="center" colspan="8">
		<input type="submit" name="submit" value="    保存    " accesskey="g" tabindex="7">
		<input type="reset" value="    重置    " accesskey="r" tabindex="8">
	</td>
</tr>
</tbody>
</table>
</form>
EOF;
		$this->kclass->page['onload'] = 'dc.tabhover()';
		$this->kclass->page['title'] .= ' - 模块管理 - 修改模块：'.$m['title'];
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title'=>$this->baseurl.' - <a href="/a.php?module=setting&action=module">模块管理</a> - 修改模块：'.$m['title'], 'right' => $this->right, 'body'=>$body));
	}
	//
	function mKill(){
		if($this->kclass->input['moduleid']<=0){
			$this->kclass->boinkIt('/a.php?module=setting&action=module');
		}
		if($this->kclass->input['confirm'] == 0){
			$this->kclass->boinkIt('/a.php?module=setting&action=module');
		}
		if($this->kclass->input['moduleid'] < 0){
			$e = '<li>请从正确的页面进行操作，如果此问题持续存在，请联系网站管理员。';
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '删除模块',
				'text' => '您在删除模块的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$m = $this->kclass->DB->queryFirst("
			SELECT `moduleid`, `title`, `parentid`
			FROM `module`
			WHERE moduleid='".$this->kclass->input['moduleid']."'
		");
		if($m){
			$this->kclass->DB->query("
				UPDATE `module`
				SET killed=1
				WHERE moduleid='".$this->kclass->input['moduleid']."'
			");
			$this->kclass->messager(array(
				'title' => '删除模块成功',
				'text' => '模块 <b>'.$m['title'].'</b> 已成功被标记为删除!',
				'url' => '/a.php?module=setting&action=module',
				'sec' => 3
			));
		}else{
			$this->kclass->boinkIt('/a.php?module=setting&action=module#'.$m['parentid']);
		}
	}

	//
	function mRemove(){
		if($this->kclass->input['moduleid']<=0){
			$this->kclass->boinkIt('/a.php?module=setting&action=module');
		}
		$m = $this->kclass->DB->queryFirst("
			SELECT `moduleid`, `title`, `remark`
			FROM `module`
			WHERE moduleid='".$this->kclass->input['moduleid']."'
		");
$body = <<<EOF
<form action="/a.php?module=setting&action=mKill" name="user" method="post">
<input type="hidden" name="module" value="setting">
<input type="hidden" name="action" value="mKill">
<input type="hidden" name="moduleid" value="{$this->kclass->input['moduleid']}">
<table><thead>
<tr><th>删除模块</th></tr>
</thead>
<tbody>
<tr class="odd">
	<td class="middle">你确定要<span class="darkred bold">删除</span>模块: <b><a href="/a.php?module=setting&action=module&moduleid={$m['moduleid']}" class="big" target="_blank">{$m['title']}</a> <span class="small gray">{$m['remark']}</span></b> 吗?</td>
</tr>
<tr>
	<td align="even">
		<label for="c1" class="red"><input type="radio" id="c1" name="confirm" value="1">是</label>　　
		<label for="c0" class="green"><input type="radio" id="c0" name="confirm" value="0" checked>否</label>
	</td>
</tr>
<tr>
	<td align="odd">
		<input type="submit" value="   保存   " accesskey="s">
		<input type="reset" value="   复位   ">
	</td>
</tr>
</tbody>
</table>
</form>
EOF;
		$this->kclass->page['onload'] = 'dc.tabhover()';
		$this->kclass->page['title'] .= ' - 删除模块 - '.$m['title'];
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 删除模块 - '.$m['title'], 'right' => $this->right . '　|　<a href="/a.php?module=setting&action=module">返回列表模块列表</a>', 'body'=>$body));
	}
	//
	function mRevival(){
		if($this->kclass->input['moduleid']<=0){
			$this->kclass->boinkIt('/a.php?module=setting&action=module');
		}
		if($this->kclass->input['confirm'] == 0){
			$this->kclass->boinkIt('/a.php?module=setting&action=module');
		}
		if($this->kclass->input['moduleid'] < 0){
			$e = '<li>请从正确的页面进行操作，如果此问题持续存在，请联系网站管理员。';
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '恢复模块',
				'text' => '您在恢复模块的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$m = $this->kclass->DB->queryFirst("
			SELECT `moduleid`, `title`, `parentid`
			FROM `module`
			WHERE moduleid='".$this->kclass->input['moduleid']."'
		");
		if($m){
			$this->kclass->DB->query("
				UPDATE `module`
				SET killed=0
				WHERE moduleid='".$this->kclass->input['moduleid']."'
			");
			$this->kclass->messager(array(
				'title' => '恢复模块成功',
				'text' => '模块 <b>'.$m['title'].'</b> 已成功被恢复!',
				'url' => '/a.php?module=setting&action=module',
				'sec' => 3
			));
		}else{
			$this->kclass->boinkIt('/a.php?module=setting&action=module#'.$m['parentid']);
		}
	}

	//
	function mRestore(){
		if($this->kclass->input['moduleid']<=0){
			$this->kclass->boinkIt('/a.php?module=setting&action=module');
		}
		$m = $this->kclass->DB->queryFirst("
			SELECT `moduleid`, `title`, `remark`
			FROM `module`
			WHERE moduleid='".$this->kclass->input['moduleid']."'
		");
$body = <<<EOF
<form action="/a.php?module=setting&action=mRevival" name="user" method="post">
<input type="hidden" name="module" value="setting">
<input type="hidden" name="action" value="mRevival">
<input type="hidden" name="moduleid" value="{$this->kclass->input['moduleid']}">
<table><thead>
<tr><th>恢复模块</th></tr>
</thead>
<tbody>
<tr class="odd">
	<td class="middle">你确定要<span class="darkred bold">恢复</span>模块: <b><a href="/a.php?module=setting&action=module&moduleid={$m['moduleid']}" class="big" target="_blank">{$m['title']}</a> <span class="small gray">{$m['remark']}</span></b> 吗?</td>
</tr>
<tr>
	<td align="even">
		<label for="c1" class="red"><input type="radio" id="c1" name="confirm" value="1">是</label>　　
		<label for="c0" class="green"><input type="radio" id="c0" name="confirm" value="0" checked>否</label>
	</td>
</tr>
<tr>
	<td align="odd">
		<input type="submit" value="   保存   " accesskey="s">
		<input type="reset" value="   复位   ">
	</td>
</tr>
</tbody>
</table>
</form>
EOF;
		$this->kclass->page['onload'] = 'dc.tabhover()';
		$this->kclass->page['title'] .= ' - 恢复模块 - '.$m['title'];
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 恢复模块 - '.$m['title'], 'right' => $this->right . '　|　<a href="/a.php?module=setting&action=module">返回列表模块列表</a>', 'body'=>$body));
	}
	//
	function mOrdering(){
		if(count($this->kclass->input['ordering']) > 0){
			foreach($this->kclass->input['ordering'] as $k => $v){
				$this->kclass->DB->query("UPDATE `module` SET `ordering`='".$v."' WHERE `moduleid`='".$k."'");
			}
		}
		$this->kclass->messager(array(
			'title' => '模块排序',
			'text' => '模块排序设置已保存。',
			'url' => '/a.php?module=setting&action=module',
			'sec' => 1
		));
	}
	//
	function module(){
		$ms = $this->kclass->DB->query("
			SELECT m1.moduleid AS m1id, m1.title AS parent, m1.remark AS m1remark, m1.created AS m1created, m1.modified AS m1modified, m1.ordering AS m1ordering, m1.killed AS m1killed, 
				m2.*
			FROM `module` AS m1
			LEFT JOIN `module` AS m2 ON (m2.parentid=m1.moduleid)
			WHERE m1.parentid=0
			ORDER BY m1.ordering ASC, m2.ordering ASC, m2.moduleid ASC, m1.moduleid ASC
		");
		if($this->kclass->DB->numRows()){
			while($m = $this->kclass->DB->fetchArray($ms)){
				if($parent != $m['parent']){
					$parent = $m['parent'];
					$kill=0;
					$t .= '<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even');
					if($m['m1killed'] == 1){
						$t .= ' through';
						$kill=1;
						$l = '<a href="/a.php?module=setting&action=mRestore&moduleid='.$m['m1id'].'">恢</a>';
					}else{
						$l = '<a href="/a.php?module=setting&action=mCopyAll&fromid='.$m['m1id'].'">子项复制</a>　|　<a href="/a.php?module=setting&action=mRemove&moduleid='.$m['m1id'].'">删</a> <a href="/a.php?module=setting&action=mUpdate&moduleid='.$m['m1id'].'">改</a> <a href="/a.php?module=setting&action=mAdd&parentid='.$m['m1id'].'">添加</a>';
					}
					$t .= '" onmouseover="this.style.backgroundColor=\'#FEEBD1\';" onmouseout="this.style.backgroundColor=\'\';"><td colspan="2" class="bold middle"><a name="'.$m['m1id'].'"></a>'.$m['parent'].'</td><td class="gray">'.$m['m1remark'].'</td><td class="gray">'.date('y-m-d H:i', $m['m1created']).'</td><td><input type="text" name="ordering['.$m['m1id'].']" class="small" size="1" value="'.$m['m1ordering'].'"></td><td>'.$l.'</td></tr>';
				}
				if($m['moduleid'] > 0){
					$t .= '<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even');
					if($m['killed'] == 1 OR $kill==1){
						$t .= ' through';
						if($kill == 1){
							$l = '';
						}else{
							$l = '<a href="/a.php?module=setting&action=mRestore&moduleid='.$m['moduleid'].'">恢</a>';
						}
					}else{
						$l = '<a href="/a.php?module=setting&action=mRemove&moduleid='.$m['moduleid'].'">删</a> <a href="/a.php?module=setting&action=mUpdate&moduleid='.$m['moduleid'].'">改</a>';
					}
					$t .= '" onmouseover="this.style.backgroundColor=\'#FEEBD1\';" onmouseout="this.style.backgroundColor=\'\';"><td>'.$m['moduleid'].'.</td><td>'.$m['title'].'<span class="gray small" title="'.$m['action'].'">('.$this->kclass->iif(strlen($m['action'])>30, substr($m['action'], 0, 30).'...', $m['action']).')</span></td><td class="gray">'.$m['remark'].'</td><td class="gray">'.date('y-m-d H:i', $m['m1created']).'</td><td><input type="text" name="ordering['.$m['moduleid'].']" class="small" size="1" value="'.$m['ordering'].'"></td><td align="right">'.$l.'</td></tr>';
				}
			}
		}
$body = <<<EOF
<form name="setting" method="post" action="/a.php?module=setting&action=mOrdering">
<input type="hidden" name="module" value="setting">
<input type="hidden" name="action" value="mOrdering">
<table><thead>
<tr>
	<th colspan="2">模块名称</th>
	<th>说明</th>
	<th>修改日期</th>
	<th>排序</th>
	<th align="right">选项</th>
</tr>
</thead>
<tbody>
{$t}
<tr>
	<td colspan="6"></td>
</tr>
<tr>
	<td align="center" colspan="6"><input type="submit" value="    保存排序    "></td>
</tr>
</tbody>
</table>
</form>
EOF;
		$this->kclass->page['onload'] = 'dc.tabhover()';
		$this->kclass->page['title'] .= ' - 模块管理';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title'=>$this->baseurl.' - 模块管理', 'right' => $this->right.'　|　<a href="/a.php?module=setting&action=mAdd&parentid=0">新建模块</a>', 'body'=>$body));
	}
	//
	function mnInsert(){
		if($this->kclass->input['parentid'] > 0 AND $this->kclass->input['title'] != '' AND $this->kclass->input['moduleid'] == 0){
			$e = '<li>必需与模块关联。</li>';
		}
		if($this->kclass->input['title'] == '' AND $this->kclass->kclass->input['parentid'] == 0){
			$e = '<li>根菜单中的项目不能留空。</li>';
		}
		if($e != ''){
			$this->kclass->messager(array(
				'title' => '添加新菜单',
				'text' => '您在修改用户信息过程中有以下错误: <ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}

		$this->kclass->DB->query("
			INSERT INTO `menu` (`parentid`, `moduleid`, `title`, `icon`, `module`, `action`, `addition`, `remark` , `ordering`, `modified`, `created`, `creator`)
			VALUES (
				'".$this->kclass->input['parentid']."', '".$this->kclass->input['moduleid']."', '".$this->kclass->input['title']."', '".$this->kclass->input['icon']."', '".$this->kclass->input['mdl']."', '".$this->kclass->input['atn']."', '".$this->kclass->input['addition']."', '".$this->kclass->input['remark']."', '".$this->kclass->input['ordering']."', '".TIMENOW."', '".TIMENOW."' , '".$this->kclass->user['userid']."'
			)
		");
		$this->kclass->messager(array(
			'title' => '添加新菜单',
			'text' => '新菜单已经添加。',
			'url' => '/a.php?module=setting&action=menu'.$this->kclass->iif($this->kclass->input['parentid']>0, '#'.$this->kclass->input['parentid'], ''),
			'sec' => 1
		));
	}
	//
	function mnAdd(){
		$parent = $this->_menuChoose(array(
			'name' => 'parentid', 
			'selectedid' => $this->kclass->input['parentid'],
			'menuid' => 0, 
			'top' => 1, 
			'topname' => '根(一级菜单)',
			'showall' => 1,
			'width' =>226
		));
		$modules = $this->_moduleChoose(0);
$body = <<<EOF
<form name="setting" method="post" action="/a.php?module=setting&action=mnInsert">
<input type="hidden" name="menu" value="setting">
<input type="hidden" name="action" value="mnInsert">
<table><thead>
<tr>
	<th colspan="2">新建菜单</th>
</tr>
</thead>
<tbody>
<tr class="odd">
	<td>关联模块：<span class="red bold">*</span></td>
	<td>{$modules} <span class="small gray">如果是新内容，请先添加相应 <a href="/a.php?module=setting&action=module" target="_blank">模块</a> 后再添加菜单项目。</span></td>
</tr>
<tr class="even">
	<td>上　　级：<span class="red bold">*</span></td>
	<td>{$parent} <span class="small gray">建议不要超过2级，否则效率低。</span></td>
</tr>
<tr class="odd">
	<td>名　　称：</td>
	<td><input type="text" name="title" size="25" style="width:350px" value="{$m['title']}" /> <span class="small gray">一般不要超过5个中文字，除了根菜单，留空表示<span class="bold">分割线</span>。</span></td>
</tr>
<tr class="even">
	<td>图　　标：</td>
	<td><input type="text" name="icon" size="25" style="width:350px" value="{$m['icon']}" /> <span class="small gray">图标尺寸必需<span class="bold">16x16</span>，文件位置<span class="bold">/public/menu</span>下。</span></td>
</tr>
<tr class="odd">
	<td>URL模块：</td>
	<td><input type="text" name="mdl" size="25" style="width:350px" value="{$m['mdl']}" /> <span class="small gray">仅填写“<span class="bold">module=</span>”的值。</span></td>
</tr>
<tr class="even">
	<td>URL动作：</td>
	<td><input type="text" name="atn" size="25" style="width:350px" value="{$m['atn']}" /> <span class="small gray">仅填写“<span class="bold">action=</span>”的值。</span></td>
</tr>
<tr class="odd">
	<td>URL附加：</td>
	<td><input type="text" name="addition" size="25" style="width:350px" value="{$m['addition']}" /> <span class="small gray">前面不要添加<span class="bold">&</span>符号。</span></td>
</tr>
<tr class="even">
	<td>备　　注：</td>
	<td><input type="text" name="remark" size="25" style="width:350px" value="{$m['remark']}" /> <span class="small gray">简要说明。</span></td>
</tr>
<tr class="odd">
	<td>排　　序：</td>
	<td><input type="text" name="ordering" size="25" style="width:350px" value="1" /> <span class="small gray">数字越小排序越靠前。</span></td>
</tr>
<tr class="even">
	<td align="center" colspan="8">
		<input type="submit" name="submit" value="    保存    " accesskey="g" tabindex="7">
		<input type="reset" value="    重置    " accesskey="r" tabindex="8">
	</td>
</tr>
</tbody>
</table>
</form>
EOF;
		$this->kclass->page['onload'] .= 'dc.tabhover();';
		$this->kclass->page['title'] .= ' - 菜单管理 - 添加新菜单';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title'=>$this->baseurl.' - <a href="/a.php?module=setting&action=menu">菜单管理</a> - 添加新菜单', 'right' => $this->right, 'body'=>$body));
	}
	//
	function mnDoupdate(){
		if($this->kclass->input['parentid'] > 0 AND $this->kclass->input['title'] != '' AND $this->kclass->input['moduleid'] == 0){
			$e = '<li>必需与模块关联。</li>';
		}
		if($this->kclass->input['title'] == '' AND $this->kclass->kclass->input['parentid'] == 0){
			$e = '<li>根菜单中的项目不能留空。</li>';
		}
		if($e != ''){
			$this->kclass->messager(array(
				'title' => '修改菜单',
				'text' => '您在修改用户信息过程中有以下错误: <ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}

		$this->kclass->DB->query("
			UPDATE `menu`
			SET `parentid`='".$this->kclass->input['parentid']."', 
				`moduleid`='".$this->kclass->input['moduleid']."', 
				`title`='".$this->kclass->input['title']."', 
				`module`='".$this->kclass->input['mdl']."',
				`action`='".$this->kclass->input['atn']."',
				`addition`='".$this->kclass->input['addition']."',
				`icon`='".$this->kclass->input['icon']."',
				`remark`='".$this->kclass->input['remark']."',
				`ordering`='".$this->kclass->input['ordering']."',
				`modified` = '".TIMENOW."',
				`modifier` = '".$this->kclass->user['userid']."'
			WHERE `menuid`='".$this->kclass->input['menuid']."'
		");
		$this->kclass->messager(array(
			'title' => '修改菜单',
			'text' => '菜单已经修改。',
			'url' => '/a.php?module=setting&action=menu'.$this->kclass->iif($this->kclass->input['parentid']>0, '#'.$this->kclass->input['parentid'], ''),
			'sec' => 1
		));
	}
	//
	function mnUpdate(){
		$m = $this->kclass->DB->queryFirst("SELECT * FROM `menu` WHERE menuid='".$this->kclass->input['menuid']."'");
		$parent = $this->_menuChoose(array(
			'name' => 'parentid', 
			'selectedid' => $m['parentid'],
			'menuid' => 0, 
			'top' => 1, 
			'topname' => '根(一级菜单)',
			'showall' => 1,
			'width' =>226
		));
		$modules = $this->_moduleChoose($m['moduleid']);
$body = <<<EOF
<form name="setting" method="post" action="/a.php?module=setting&action=mnDoupdate">
<input type="hidden" name="menu" value="setting">
<input type="hidden" name="action" value="mnDoupdate">
<input type="hidden" name="menuid" value="{$this->kclass->input['menuid']}">
<table><thead>
<tr>
	<th colspan="2">修改菜单</th>
</tr>
</thead>
<tbody>
<tr>
	<td>关联模块：<span class="red bold">*</span></td>
	<td>{$modules} <span class="small gray">如果是新内容，请先添加相应 <a href="/a.php?module=setting&action=module" target="_blank">模块</a> 后再添加菜单项目。</span></td>
</tr>
<tr class="odd">
	<td>上　　级：<span class="red bold">*</span></td>
	<td>{$parent} <span class="small gray">建议不要超过2级，否则效率低。</span></td>
</tr>
<tr class="even">
	<td>名　　称：</td>
	<td><input type="text" name="title" size="25" style="width:350px" value="{$m['title']}" /> <span class="small gray">一般不要超过5个中文字，除了根菜单，留空表示<span class="bold">分割线</span>。</span></td>
</tr>
<tr class="odd">
	<td>图　　标：</td>
	<td><input type="text" name="icon" size="25" style="width:350px" value="{$m['icon']}" /> <span class="small gray">图标尺寸必需<span class="bold">16x16</span>，文件位置<span class="bold">/public/menu</span>下。</span></td>
</tr>
<tr class="even">
	<td>URL模块：</td>
	<td><input type="text" name="mdl" size="25" style="width:350px" value="{$m['module']}" /> <span class="small gray">仅填写“<span class="bold">module=</span>”的值。</span></td>
</tr>
<tr class="odd">
	<td>URL动作：</td>
	<td><input type="text" name="atn" size="25" style="width:350px" value="{$m['action']}" /> <span class="small gray">仅填写“<span class="bold">action=</span>”的值。</span></td>
</tr>
<tr class="even">
	<td>URL附加：</td>
	<td><input type="text" name="addition" size="25" style="width:350px" value="{$m['addition']}" /> <span class="small gray">前面不要添加<span class="bold">&</span>符号。</span></td>
</tr>
<tr class="odd">
	<td>备　　注：</td>
	<td><input type="text" name="remark" size="25" style="width:350px" value="{$m['remark']}" /> <span class="small gray">简要说明。</span></td>
</tr>
<tr class="even">
	<td>排　　序：</td>
	<td><input type="text" name="ordering" size="25" style="width:350px" value="{$m['ordering']}" /> <span class="small gray">数字越小排序越靠前。</span></td>
</tr>
<tr class="odd">
	<td align="center" colspan="8">
		<input type="submit" name="submit" value="    保存    " accesskey="g" tabindex="7">
		<input type="reset" value="    重置    " accesskey="r" tabindex="8">
	</td>
</tr>
</tbody>
</table>
</form>
EOF;
		$this->kclass->page['onload'] .= 'dc.tabhover();';
		$this->kclass->page['title'] .= ' - 菜单管理 - 修改菜单';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title'=>$this->baseurl.' - <a href="/a.php?module=setting&action=menu">菜单管理</a> - 修改菜单', 'right' => $this->right, 'body'=>$body));
	}
	//
	function mnKill(){
		if($this->kclass->input['menuid']<=0){
			$this->kclass->boinkIt('/a.php?module=setting&action=menu');
		}
		if($this->kclass->input['confirm'] == 0){
			$this->kclass->boinkIt('/a.php?module=setting&action=menu');
		}
		if($this->kclass->input['menuid'] < 0){
			$e = '<li>请从正确的页面进行操作，如果此问题持续存在，请联系网站管理员。';
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '删除菜单',
				'text' => '您在删除菜单的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$m = $this->kclass->DB->queryFirst("
			SELECT `menuid`, `title`, `parentid`
			FROM `menu`
			WHERE menuid='".$this->kclass->input['menuid']."'
		");
		if($m){
			$this->kclass->DB->query("
				DELETE FROM `menu`
				WHERE menuid='".$this->kclass->input['menuid']."'
			");
			$this->kclass->messager(array(
				'title' => '删除菜单成功',
				'text' => '菜单 <b>'.$m['title'].'</b> 已成功被删除!',
				'url' => '/a.php?module=setting&action=menu',
				'sec' => 3
			));
		}else{
			$this->kclass->boinkIt('/a.php?module=setting&action=menu#'.$m['parentid']);
		}
	}

	//
	function mnRemove(){
		if($this->kclass->input['menuid']<=0){
			$this->kclass->boinkIt('/a.php?module=setting&action=menu');
		}
		$m = $this->kclass->DB->queryFirst("
			SELECT `menuid`, `title`, `remark`
			FROM `menu`
			WHERE menuid='".$this->kclass->input['menuid']."'
		");
$body = <<<EOF
<form action="/a.php?module=setting&action=mnKill" name="user" method="post">
<input type="hidden" name="menu" value="setting">
<input type="hidden" name="action" value="mnKill">
<input type="hidden" name="menuid" value="{$this->kclass->input['menuid']}">
<table><thead>
<tr class="odd">
	<th class="middle">你确定要 <span class="darkred bold">删除 <span class="small">(物理删除)</span></span> 菜单: <span class="big bold">{$m['title']}</span> <span class="small gray">{$m['remark']}</span></b> 吗?</th>
</tr>
</thead>
<tbody>
<tr>
	<td align="center">
		<label for="c1" class="red"><input type="radio" id="c1" name="confirm" value="1">是</label>　　
		<label for="c0" class="green"><input type="radio" id="c0" name="confirm" value="0" checked>否</label>
	</td>
</tr>
<tr>
	<td align="center">
		<input type="submit" value="   保存   " accesskey="s">
		<input type="reset" value="   复位   ">
	</td>
</tr>
</tbody>
</table>
</form>
EOF;
		$this->kclass->page['title'] .= ' - 删除菜单 - '.$m['title'];
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 删除菜单 - '.$m['title'], 'right' => $this->right . '　|　<a href="/a.php?module=setting&action=menu">返回列表菜单列表</a>', 'body'=>$body));
	}
	//
	function mnOrdering(){
		if(count($this->kclass->input['ordering']) > 0){
			foreach($this->kclass->input['ordering'] as $k => $v){
				$this->kclass->DB->query("UPDATE `menu` SET `ordering`='".$v."' WHERE `menuid`='".$k."'");
			}
		}
		$this->kclass->messager(array(
			'title' => '菜单排序',
			'text' => '菜单排序设置已保存。',
			'url' => '/a.php?module=setting&action=menu',
			'sec' => 1
		));
	}
	//
	function menu(){
		$m = $this->_menu();
$body = <<<EOF
<form name="setting" method="post" action="/a.php?module=setting&action=mnOrdering">
<input type="hidden" name="menu" value="setting">
<input type="hidden" name="action" value="mnOrdering">
<table cellspacing="5" align="center">
<thead>
<tr>
	<th align="center">建立日期</th>
	<th>排序</th>
	<th>菜单名称</th>
	<th>关联</th>
	<th>说明</th>
	<th align="center">修改日期</th>
	<th align="center">选项</th>
</tr>
</thead>
<tbody>
{$m['r']}
<tr>
	<td align="center" colspan="7"><input type="submit" value="    保存排序    "></td>
</tr>
</tbody>
</table>
</form>
EOF;
		$this->kclass->page['title'] .= ' - 菜单管理';
		$this->kclass->page['onload'] .= 'dc.tabhover();';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title'=>$this->baseurl.' - 菜单管理', 'right' => $this->right.'　|　<a href="/a.php?module=setting&action=mnAdd&parentid=0">新建菜单</a>', 'body'=>$body));
	}
	//
	function _menuChoose($c){
		if($c['menuid']==0){
			$r .= '<select name="'.$c['name'].'" style="width:350px">';
			if($c['top']==1){
				$r .= '<option value="0"'.$this->kclass->iif($c['selectedid']==$c['menuid'], ' selected', '').'>'.$c['depth'].$c['topname'].'</option>';
			}
		}else{
			$condition = "menuid='".$c['menuid']."'";
			$menu = $this->kclass->DB->queryFirst("
				SELECT menuid,title
				FROM `menu`
				WHERE $condition
			");
			$r .= '<option value="'.$menu['menuid'].'"'.$this->kclass->iif($c['selectedid']==$c['menuid'], ' selected', '').'>'.$c['depth'].$menu['title'].'</option>';
		}

		$c['depth'] .= "　";
		$ncondition .= "parentid = '".$c['menuid']."'";
		$menus=$this->kclass->DB->query("
			SELECT menuid 
			FROM `menu` 
			WHERE $ncondition
			ORDER BY ordering
		");
		if($this->kclass->DB->numRows()){
			while ($menu = $this->kclass->DB->fetchArray($menus)){
				$r .= $this->_menuChoose(array(
					'name' => 'menuid', 
					'selectedid' => $c['selectedid'], 
					'menuid' => $menu['menuid'], 
					'depth' => $c['depth'], 
					'topname' => $c['topname'], 
					'top' => 1, 
					'displayid' => $c['displayid'], 
					'userid' => $c['userid']
				));
			}
		}
		if($c['menuid'] == 0){
			$r .= '</select>';
		}
		return $r;
	}
	// 
	function _menu($pid=0){
		$ms=$this->kclass->DB->query("
			SELECT m.*, 
				d.`module` AS mdl, d.`action` AS atn
			FROM `menu` AS m 
			LEFT JOIN `module` AS d ON (d.moduleid=m.moduleid) 
			WHERE m.parentid='".$pid."' 
			ORDER BY m.ordering ASC, m.menuid ASC
		");
		if($this->kclass->DB->numRows()){
			while($m = $this->kclass->DB->fetchArray($ms)){
				if($t = $this->_menu($m['menuid']) AND $m['parentid']>0)$d = '　　'.$t['d'];

				$r .= '<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').'"><td class="small gray">'.date('y-m-d H:i', $m['created']).'</td><td align="right"><input type="text" name="ordering['.$m['menuid'].']" size="3" value="'.$m['ordering'].'" class="tiny"></td><td>'.$d.$this->kclass->iif($m['icon']!='', '<img src="" width="16" height="16" /> ', '').$m['title'].$this->kclass->iif($m['module']!='', ' <span class="small gray">(module=<span class="bold">'.$m['module'].'</span>'.$this->kclass->iif($m['action']!='', '&action=<span class="bold">'.$m['action'].'</span>', '').$this->kclass->iif($m['addition']!='', '&<span class="bold">'.$m['addition'].'</span>', '').')</span>', '').'</td><td>'.$m['mdl'].'</td><td class="gray">'.$m['remark'].'</td><td class="gray">'.date('y-m-d H:i', $m['modified']).'</td><td><a href="/a.php?module=setting&action=mnRemove&menuid='.$m['menuid'].'">删</a> <a href="/a.php?module=setting&action=mnAdd&parentid='.$m['menuid'].'" class="tiny">添加下级</a> <a href="/a.php?module=setting&action=mnUpdate&menuid='.$m['menuid'].'">改</a></td></tr>'."\n";

				if($t)$r .= $t['r'];
			}
		}
		return array('r'=>$r, 'd'=>$d);
	}
	//
	function _moduleChoose($id=0){
		$ms = $this->kclass->DB->query("
			SELECT m1.title AS parent, 
				m2.moduleid, m2.title, m2.remark, m2.module, m2.action
			FROM `module` AS m1
			LEFT JOIN `module` AS m2 ON (m2.parentid=m1.moduleid)
			WHERE m1.killed=0 AND m2.killed=0 AND m1.parentid=0
			ORDER BY m1.ordering ASC, m2.ordering ASC, m2.moduleid ASC, m1.moduleid ASC
		");
		if($this->kclass->DB->numRows()){
			$parent='';
			$s = '<select name="moduleid" style="width:350px"><option value="0"'.$this->kclass->iif($id==0, ' selected', '').'> </option>';
			while($m = $this->kclass->DB->fetchArray($ms)){
				if($parent != $m['parent']){
					if($parent==''){
						$s .= '</optgroup>';
					}
					$s .= '<optgroup label="'.$m['parent'].'">';
					$parent = $m['parent'];
				}
				if($m['moduleid'] > 0){
					$s .= '<option value="'.$m['moduleid'].'" title="'.$m['remark'].'"'.$this->kclass->iif($m['moduleid']==$id, ' selected', '').'>'.$m['title'].'('.$m['module'].'|'.$m['action'].')</option>';
				}
			}
			$s .= '</optgroup></select>';
		}
		return $s;
	}
}
?>