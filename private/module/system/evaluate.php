<?php
// 
class evaluate{
	# Global
	var $kclass;
	var $output;

	/*--------------------*/
	// Auto run function
	/*--------------------*/
	function autoRun(){
		$this->kclass->page['title'] = '供应商评估';
		$this->baseurl = '<a href="/?module=home">首页</a> - <a href="/?module=evaluate">供应商评估</a>';
		$this->right = '<a href="/?module=evaluate&action=add">新建供应商评估</a>';

		switch($this->kclass->input['action']){
			case 'list':
				$this->mmlist();
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
			case 'doorder':
				$this->doorder();
				break;
			default:
				$this->mmlist();
		}
	}

	// 
	function insert(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$errors[] = '请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。';
		}else{
			if($this->kclass->input['title'] == ''){
				$errors[] = '请填写供应商评估标题。';
			}
			if($this->kclass->input['entitle'] == ''){
				$errors[] = '请填写供应商评估 英文标题。';
			}
		}

		if(count($errors)){
			$error = implode("</li>\n<li>",$errors);
			$error = "<ul>\n<li>".$error."</li></ul>";
			$this->kclass->messager(array(
					'title' => '新建供应商评估',
					'text' => '您在新建供应商评估的过程中有以下错误: '.$error,
					'url' => 'javascript:history.back()',
					'final' => 1,
					'sec' => 3
				)
			);
		}

		$this->kclass->DB->query("
			INSERT INTO `evaluate` 
				(`title` , `parentid` , `parentlist` , `node` , `entitle` , `description` , `publish` , `ordering` , `display` , `creator` , `created`) 
			VALUES 
				( '".$this->kclass->input['title']."', '".$this->kclass->input['parentid']."', '".$parentlist."', 0, '".$this->kclass->input['entitle']."', '".$this->kclass->input['description']."', '".$this->kclass->input['publish']."', '".$this->kclass->input['ordering']."', '".$this->kclass->input['display']."', '".$this->kclass->member['userid']."', '".TIMENOW."')
		");
		$evaluateid=$this->kclass->DB->insertID();

		$parentlist = $this->_makeparentlist($this->kclass->input['parentid']);

		$this->kclass->DB->query("
			UPDATE `evaluate` 
			SET parentlist='".addslashes("$evaluateid,$parentlist")."'
			WHERE evaluateid='".$evaluateid."'
		");
		$this->kclass->DB->query("
			UPDATE `evaluate` 
			SET node=node+1 
			WHERE evaluateid IN ($parentlist)
		");

		$this->kclass->messager(array(
				'title' => '新建供应商评估',
				'text' => '供应商评估 <b>'.$this->kclass->input['title'].'</b> 已新建成功!',
				'url' => '/?module=evaluate',
				'final' => 1,
				'sec' => 3
			)
		);
	}

	// 
	function add(){
		$select = $this->kclass->chooserevaluate(array(
				'name' => 'parentid', 
				'selectedid' => $this->kclass->input['parentid'],
				'evaluateid' => 0, 
				'displaytop' => 1, 
				'hasBlank' => 1, 
				'topname' => '根',
				'showall' => 1,
				'width' =>306
			)	
		);

$body = <<<EOF
<form action="/?module=evaluate&action=insert" name="name" method="post">
<input type="hidden" name="module" value="evaluate">
<input type="hidden" name="action" value="insert">
<table>
<thead>
<tr>
	<th colspan="2">添加供应商评估：</th>
</tr>
</thead>
<tbody>
<tr class="even">
	<td>上级列项：</td>
	<td>{$select}</td>
</tr>
<tr class="odd">
	<td>标　　题：<font color="red">*</font></td>
	<td><input type="text" style="width: 300px;" name="title" value=""></td>
</tr>
<tr class="even">
	<td>英文标题：<font color="red">*</font></td>
	<td><input type="text" style="width: 300px;" name="entitle" value=""></td>
</tr>
<tr class="odd">
	<td valign="top">描　　述：</td>
	<td><textarea name="description" style="width: 300px;height: 50px;"></textarea></td>
</tr>
<tr class="even">
	<td>顺　　序：</td>
	<td><input type="text" style="width: 300px;" name="ordering" value="1"></td>
</tr>
<tr class="odd">
	<td>系统显示：</td>
	<td>
		<label for="display_1"><input type="radio" id="display_1" name="display" value="1" class="nostyle" checked>显示</label> 
		<label for="display_0"><input type="radio" id="display_0" name="display" value="0" class="nostyle">不显示</label> 
	</td>
</tr>
<tr class="even">
	<td>网站显示：</td>
	<td>
		<label for="publish_1"><input type="radio" id="publish_1" name="publish" value="1" class="nostyle">显示</label> 
		<label for="publish_0"><input type="radio" id="publish_0" name="publish" value="0" class="nostyle" checked>不显示</label> 
	</td>
</tr>
<tr class="odd">
	<td colspan="2" class="gray small">如果选择不显示，那么网站前台所有该供应商评估下的产品页将不显示，即使供应商评估下的产品是“发布”状态。</td>
</tr>
<tr class="even">
	<td colspan="2" align="center">
		<input type="submit" name="submit" value="   保存   " accesskey="s">
		<input type="reset" value="   重置   ">
	</td>
</tr>
</tbody>
</table>
</form>
EOF;
		$this->kclass->page['onload'] .= 'dc.tabhover();';
		$this->kclass->page['title'] = ' - 新建供应商评估';
		$this->kclass->page['main'] .= $this->kclass->skin->dbody(array('title' => $this->baseurl.' - 新建供应商评估', 'right' => '<a href="/?module=evaluate">返回列表</a>','body'=>$body));
	}

	// 
	function doupdate(){
		if($this->kclass->input['parentid'] == $this->kclass->input['evaluateid']){
			$errors[] = '你不能将一个供应商评估自身选做它的父供应商评估！';
		}else{
			$evaluateinfo = $this->kclass->DB->queryFirst("
				SELECT evaluateid,title,parentlist 
				FROM `evaluate` 
				WHERE evaluateid='".$parentid."'
			");
			$parents = explode(",", $evaluateinfo['parentlist']);
			$touchon = 0;
			while (list(,$val) = each($parents)) {
				if ($val==$this->kclass->input['evaluateid']) {
					$touchon = 1;
				}
			}

			if($touchon){
				$errors[] = '你不能将一个列项设置为它的子列项的列项！';
			}
			if($this->kclass->input['title'] == ''){
				$errors[] = '请填写供应商评估标题。';
			}
			if($this->kclass->input['entitle'] == ''){
				$errors[] = '请填写供应商评估 英文标题 标题。';
			}
		}

		if(count($errors)){
			$error = implode("</li>\n<li>",$errors);
			$error = "<ul>\n<li>".$error."</li></ul>";
			$this->kclass->messager(array(
					'title' => '修改供应商评估',
					'text' => '您在修改供应商评估的过程中有以下错误：'.$error,
					'url' => 'javascript:history.back()',
					'final' => 1,
					'sec' => 3
				)
			);
		}

		$parentlist = $evaluateid.",".$this->_makeparentlist($this->kclass->input['parentid']);
		$this->kclass->DB->query("
			UPDATE `evaluate` 
			SET title = '".$this->kclass->input['title']."', 
				entitle = '".$this->kclass->input['entitle']."', 
				description = '".$this->kclass->input['description']."', 
				ordering = '".$this->kclass->input['ordering']."', 
				parentid = '".$this->kclass->input['parentid']."', 
				display = '".$this->kclass->input['display']."', 
				publish = '".$this->kclass->input['publish']."', 
				parentlist = '".$parentlist."', 
				modifier = '".$this->kclass->member['userid']."', 
				modified = '".TIMENOW."' 
			WHERE evaluateid='".$this->kclass->input['evaluateid']."'
		");

		$this->kclass->messager(array(
				'title' => '修改供应商评估',
				'text' => '供应商评估 <b>'.$this->kclass->input['title'].'</b> 已成功被修改!',
				'url' => '/?module=evaluate',
				'final' => 1,
				'sec' => 3
			)
		);
	}

	// 
	function update(){
		if($this->kclass->input['evaluateid']<=0){
			$this->kclass->boink_it('/?module=evaluate.html');
		}
		$evaluate = $this->kclass->DB->queryFirst("
			SELECT *
			FROM evaluate
			WHERE evaluateid='".$this->kclass->input['evaluateid']."'
		");
		if($evaluate['display'] == 1){
			$display_1 = ' checked';
		}else{
			$display_0 = ' checked';
		}
		if($evaluate['publish'] == 1){
			$publish_1 = ' checked';
		}else{
			$publish_0 = ' checked';
		}
		$select = $this->kclass->chooserevaluate(array(
				'name' => 'parentid', 
				'selectedid' => $evaluate['parentid'],
				'evaluateid' => 0, 
				'displaytop' => 1, 
				'hasBlank' => 1, 
				'topname' => '根',
				'showall' => 1,
				'width'=>306
			)	
		);
$body = <<<EOF
<form action="/?module=evaluate&action=doupdate" name="name" method="post">
<input type="hidden" name="module" value="evaluate">
<input type="hidden" name="action" value="doupdate">
<input type="hidden" name="evaluateid" value="{$this->kclass->input['evaluateid']}">
<table>
<thead>
<tr>
	<th colspan="2">修改供应商评估: </th>
</tr>
</thead>
<tbody>
<tr class="even">
	<td>上级列项：</td>
	<td>{$select}</td>
</tr>
<tr class="odd">
	<td>标　　题：<font color="red">*</font></td>
	<td><input type="text" style="width: 300px;" name="title" value="{$evaluate['title']}"></td>
</tr>
<tr class="even">
	<td>英文标题：<font color="red">*</font></td>
	<td><input type="text" style="width: 300px;" name="entitle" value="{$evaluate['entitle']}"></td>
</tr>
<tr class="odd">
	<td valign="top">描　　述：</td>
	<td><textarea name="description" style="width: 300px;height: 50px;">{$evaluate['description']}</textarea></td>
</tr>
<tr class="even">
	<td>顺　　序：</td>
	<td><input type="text" style="width: 300px;" name="ordering" value="{$evaluate['ordering']}"></td>
</tr>
<tr class="odd">
	<td>系统显示：</td>
	<td>
		<label for="display_1"><input type="radio" id="display_1" name="display" value="1" class="nostyle"{$display_1}>显示</label> 
		<label for="display_0"><input type="radio" id="display_0" name="display" value="0" class="nostyle"{$display_0}>不显示</label> 
	</td>
</tr>
<tr class="even">
	<td>网站显示：</td>
	<td>
		<label for="publish_1"><input type="radio" id="publish_1" name="publish" value="1" class="nostyle"{$publish_1}>显示</label> 
		<label for="publish_0"><input type="radio" id="publish_0" name="publish" value="0" class="nostyle"{$publish_0}>不显示</label> 
	</td>
</tr>
<tr class="odd">
	<td colspan="2" class="gray small">如果选择不显示，那么网站前台所有该供应商评估下的产品页将不显示，即使供应商评估下的产品是“发布”状态。</td>
</tr>
<tr class="even">
	<td colspan="2" align="center">
		<input type="submit" name="submit" value="   保存   " accesskey="s">
		<input type="reset" value="   重置   ">
	</td>
</tr>
</tbody>
</table>
</form>
EOF;
		$this->kclass->page['onload'] .= 'dc.tabhover();';
		$this->kclass->page['title'] = ' - 修改供应商评估';
		$this->kclass->page['main'] .= $this->kclass->skin->dbody(array('title' => $this->baseurl.' - 修改供应商评估', 'right' => $this->right, 'body'=>$body));
	}

	// 
	function kill(){
		if($this->kclass->input['confirm'] == 0){
			$this->kclass->boink_it('/?module=evaluate.html');
		}
		if($this->kclass->input['evaluateid'] < 0){
			$errors[] = '请从正确的页面进行操作，如果此问题持续存在，请联系网站管理员。';
		}
		if(count($errors)){
			$error = implode("</li>\n<li>",$errors);
			$error = "<ul>\n<li>".$error."</li></ul>";
			$this->kclass->messager(array(
					'title' => '删除供应商评估',
					'text' => '您在删除供应商评估的过程中有以下错误：'.$error,
					'url' => 'javascript:history.back()',
					'final' => 1,
					'sec' => 3
				)
			);
		}

		$evaluate = $this->kclass->DB->queryFirst("
			SELECT title, parentid 
			FROM `evaluate` 
			WHERE evaluateid = '".$this->kclass->input['evaluateid']."'
		");
		$evaluates=$this->kclass->DB->query("
			SELECT evaluateid 
			FROM `evaluate` 
			WHERE INSTR(CONCAT(',',parentlist,','), ',".$this->kclass->input['evaluateid'].",')>0
		");
		$evaluatelist = $this->kclass->input['evaluateid'];
		if($this->kclass->DB->numRows()){
			while($thisevaluate=$this->kclass->DB->fetchArray($evaluates)) {
				$evaluatelist .= ",$thisevaluate[evaluateid]";
			}
		}
		$this->kclass->DB->query("
			DELETE FROM `evaluate` 
			WHERE evaluateid IN (".$evaluatelist.")
		");
		$this->kclass->DB->query("
			UPDATE `evaluate` 
			SET node = (node - 1) 
			WHERE evaluateid IN (".$evaluatelist.")
		");
		$this->kclass->DB->query("
			UPDATE `evaluate` 
			SET node = (node - 1) 
			WHERE evaluateid = '".$evaluate['parentid']."'
		");
		$this->kclass->messager(array(
				'title' => '删除供应商评估',
				'text' => '供应商评估 <b>'.$evaluate['title'].'</b> 已成功被删除!',
				'url' => '/?module=evaluate',
				'final' => 1,
				'sec' => 3
			)
		);
	}

	// 
	function remove(){
		$evaluate = $this->kclass->DB->queryFirst("
			SELECT title
			FROM evaluate
			WHERE evaluateid='".$this->kclass->input['evaluateid']."'
		");
$body = <<<EOF
<form action="/?module=evaluate&action=kill" name="name" method="post">
<input type="hidden" name="module" value="evaluate">
<input type="hidden" name="action" value="kill">
<input type="hidden" name="evaluateid" value="{$this->kclass->input['evaluateid']}">
<table>
<thead>
<tr>
	<th colspan="2">删除供应商评估: </th>
</tr>
</thead>
<tbody>
<tr class="even">
	<td colspan="2">你确定要删除供应商评估: <b>{$evaluate['title']}</b> 吗?<br />(注意：此列项下包含的所有子类也会同时被删除！)</td>
</tr>
<tr class="odd">
	<td colspan="2" align="center">
		<label for="confirm_1"><input type="radio" id="confirm_1" name="confirm" value="1" class="nostyle">是</label> 
		<label for="confirm_0"><input type="radio" id="confirm_0" name="confirm" value="0" class="nostyle" checked>否</label> 
	</td>
</tr>
<tr class="even">
	<td colspan="2" align="center">
		<input type="submit" name="submit" value="   保存   " accesskey="s">
		<input type="reset" value="   重置   ">
	</td>
</tr>
</tbody>
</table>
</form>
EOF;
		$this->kclass->page['title'] = ' - 删除供应商评估';
		$this->kclass->page['main'] .= $this->kclass->skin->title(array('title' => $this->baseurl.' - 删除供应商评估', 'right' => $this->right));
		$this->kclass->page['main'] .= $this->kclass->skin->body($body);
	}

	// 
	function doorder(){
		foreach ($this->kclass->input['order'] as $k => $v) {
			$this->kclass->DB->query("
				UPDATE evaluate
				SET ordering='".$v."'
				WHERE evaluateid='".$k."'
			");
		}
		
		$this->kclass->messager(array(
				'title' => '供应商评估排序',
				'text' => '供应商评估 重新排序 成功!',
				'url' => '/?module=evaluate',
				'final' => 1,
				'sec' => 3
			)
		);
	}

	// 
	function mmlist(){
		$evaluate = $this->_evaluatelist(0, 0);
$body = <<<EOF
<form action="/?module=evaluate&action=doorder" name="name" method="post">
<input type="hidden" name="module" value="evaluate">
<input type="hidden" name="action" value="doorder">
<table>
EOF;

if($evaluate){
$body .= <<<EOF
<thead>
<tr>
	<th>新建产品</th>
	<th>网站发布</th>
	<th>中文</th>
	<th>英文</th>
	<th>排序</th>
	<th>选项</th>
</tr>
</thead>
<tbody>
{$evaluate}
</tr>
<tr>
	<td colspan="10" align="center">
		<input type="submit" name="submit" value="   保存   " accesskey="s">
		<input type="reset" value="   重置   ">
	</td>
</tr>
</tbody>
</table>
</form>
EOF;
}else{
$body .= <<<EOF
<tr>
	<td>暂无供应商评估.</td>
</tr>
</form>
</table>
EOF;
}
		$this->kclass->page['title'] = ' - 列表';
		$this->kclass->page['main'] .= $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 列表', 'right' => $this->right, 'body'=>$body));
	}


/********************* Private Function **********************/
	// 
	function _evaluatelist($parentid = 0){
		$evaluates=$this->kclass->DB->query("
			SELECT * 
			FROM `evaluate` 
			WHERE parentid='".$parentid."'
			ORDER BY ordering
		");

		if($this->kclass->DB->numRows()){
			while($evaluate = $this->kclass->DB->fetchArray($evaluates)) {
				$r .= '<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').'"><td><a href="/?module=product&action=addform&evaluateid='.$evaluate['evaluateid'].'">新建产品</a></td><td align="center">'.$this->kclass->iif($evaluate['publish'] == 1, '是', '否').'</td><td class="middle bold">' . $this->kclass->iif($parentid > 0, '　　　', '') . '<a href="/?module=product&action=list&evaluateid='.$evaluate['evaluateid'].'">'.$evaluate['title'].'</a></td><td>'.$evaluate['entitle'].'</td><td align="right"><input type="text" name="order['.$evaluate['evaluateid'].']" size="1" value="'.$evaluate['ordering'].'"></td><td><a href="/?module=evaluate&action=remove&evaluateid='.$evaluate['evaluateid'].'" class="small">删除</a> <a href="/?module=evaluate&action=update&evaluateid='.$evaluate['evaluateid'].'">编辑</a>' .$this->kclass->iif($parentid==0, ' <a href="/?module=evaluate&action=add&parentid='.$evaluate['evaluateid'].'"><span class="small">添加</span>子类</a>', '').'</td></tr>';
				if($temp = $this->_evaluatelist($evaluate['evaluateid'])){
					$r .= $temp;
				}
//				$r .= '</li>';
			}
		}
		return $r;
	}

	// 
	function _chooser($c){
		if ($c['evaluateid']==0) {
			$r .= '<select name="'.$c['name'].'" style="width: 300">';
			if ($c['displaytop']==1) {
				$r .= '<option value="0"'.$this->kclass->iif($c['selectedid']==$c['evaluateid'], ' selected', '').'>'.$c['depth'].$c['topname'].'</option>';
			}
		} else {
			$condition = "evaluateid='".$c['evaluateid']."'";
			$evaluate = $this->kclass->DB->queryFirst("
				SELECT evaluateid,title
				FROM `evaluate`
				WHERE $condition
			");
			$r .= '<option value="'.$evaluate['evaluateid'].'"'.$this->kclass->iif($c['selectedid']==$c['evaluateid'], ' selected', '').'>'.$c['depth'].$evaluate['title'].'</option>';
		}

		$c['depth'] .= "--";

		$ncondition .= "parentid = '".$c['evaluateid']."'";

		$evaluates=$this->kclass->DB->query("
			SELECT evaluateid 
			FROM `evaluate` 
			WHERE $ncondition
			ORDER BY ordering
		");
		if($this->kclass->DB->numRows()){
			while ($evaluate = $this->kclass->DB->fetchArray($evaluates)) {
				$r .= $this->kclass->chooserevaluate(array(
					'name' => 'evaluateid', 
					'selectedid' => $c['selectedid'], 
					'evaluateid' => $evaluate['evaluateid'], 
					'depth' => $c['depth'], 
					'topname' => $c['topname'], 
					'displaytop' => 1, 
					'displayid' => $c['displayid'], 
					'userid' => $c['userid']
				));
			}
		}
		if ($c['evaluateid'] == 0) {
			$r .= '</select>';
		}

		return $r;
	}

	// 
	function _makeparentlist($evaluateid){
		$evaluateinfo = $this->kclass->DB->queryFirst("
			SELECT parentid 
			FROM `evaluate` 
			WHERE evaluateid = '".$evaluateid."'
		");

		$evaluatearray = $evaluateid;

		if ($evaluateinfo['parentid'] != 0) {
		  $parentlist = $this->kclass->DB->queryFirst("
				SELECT parentlist 
				FROM `evaluate` 
				WHERE evaluateid='".$evaluateinfo['parentid']."'
			");

			$evaluatearray .= ",".$parentlist['parentlist'];
		}

		if (substr($evaluatearray, -2)!="-1") {
			$evaluatearray.=",-1";
		}
		return $evaluatearray;
	}
}
?>