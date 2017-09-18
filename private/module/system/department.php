<?php
// 
class department{
	# Global
	var $kclass;
	var $output;

	/*--------------------*/
	// Auto run function
	/*--------------------*/
	function autoRun(){
		$this->kclass->page['title'] = '部门';
		$this->baseurl = '<a href="/s.php?module=home">首页</a> - <a href="/s.php?module=department">部门</a>';
		$this->right = '<a href="/s.php?module=department&action=add">新建部门</a>';

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
				$errors[] = '请填写部门标题。';
			}
			if($this->kclass->input['entitle'] == ''){
				$errors[] = '请填写部门 英文标题。';
			}
		}

		if(count($errors)){
			$error = implode("</li>\n<li>",$errors);
			$error = "<ul>\n<li>".$error."</li></ul>";
			$this->kclass->messager(array(
					'title' => '新建部门',
					'text' => '您在新建部门的过程中有以下错误: '.$error,
					'url' => 'javascript:history.back()',
					'final' => 1,
					'sec' => 3
				)
			);
		}

		$this->kclass->DB->query("
			INSERT INTO `department` 
				(`departmentno` ,`abbr` ,`title` , `parentid` , `parentlist` , `node` , `entitle` , `description` , `publish` , `ordering` , `display` , `creator` , `created`) 
			VALUES 
				('".$this->kclass->input['departmentno']."','".$this->kclass->input['abbr']."', '".$this->kclass->input['title']."', '".$this->kclass->input['parentid']."', '".$parentlist."', 0, '".$this->kclass->input['entitle']."', '".$this->kclass->input['description']."', '".$this->kclass->input['publish']."', '".$this->kclass->input['ordering']."', '".$this->kclass->input['display']."', '".$this->kclass->user['userid']."', '".TIMENOW."')
		");
		$departmentid=$this->kclass->DB->insertID();

		$parentlist = $this->_makeparentlist($this->kclass->input['parentid']);

		$this->kclass->DB->query("
			UPDATE `department` 
			SET parentlist='".addslashes("$departmentid,$parentlist")."'
			WHERE departmentid='".$departmentid."'
		");
		$this->kclass->DB->query("
			UPDATE `department` 
			SET node=node+1 
			WHERE departmentid IN ($parentlist)
		");

		$this->kclass->messager(array(
				'title' => '新建部门',
				'text' => '部门 <b>'.$this->kclass->input['title'].'</b> 已新建成功!',
				'url' => '/s.php?module=department',
				'final' => 1,
				'sec' => 3
			)
		);
	}

	// 
	function add(){
		$select = $this->kclass->chooserDepartment(array(
				'name' => 'parentid', 
				'selectedid' => $this->kclass->input['parentid'],
				'departmentid' => -1, 
				'displaytop' => 1, 
				'hasBlank' => 1, 
				'topname' => '根',
				'showall' => 1,
				'width' =>306
			)	
		);

$body = <<<EOF
<form action="/s.php?module=department&action=insert" name="name" method="post">
<input type="hidden" name="module" value="department">
<input type="hidden" name="action" value="insert">
<table>
<thead>
<tr>
	<th colspan="2">添加部门：</th>
</tr>
</thead>
<tbody>
<tr class="even">
	<td>上级列项：</td>
	<td>{$select}</td>
</tr>
<tr class="even">
	<td>部门编号：<font color="red">*</font></td>
	<td><input type="text" style="width: 300px;" name="departmentno" value=""></td>
</tr>
<tr class="odd">
	<td>标　　题：<font color="red">*</font></td>
	<td><input type="text" style="width: 300px;" name="title" value=""></td>
</tr>
<tr class="even">
	<td>英文标题：<font color="red">*</font></td>
	<td><input type="text" style="width: 300px;" name="entitle" value=""></td>
</tr>
<tr class="even">
	<td>部门代号：</td>
	<td><input type="text" style="width: 300px;" name="abbr" value=""></td>
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
	<td colspan="2" class="gray small">如果选择不显示，那么网站前台所有该部门下的产品页将不显示，即使部门下的产品是“发布”状态。</td>
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
		$this->kclass->page['title'] = ' - 新建部门';
		$this->kclass->page['main'] .= $this->kclass->skin->dbody(array('title' => $this->baseurl.' - 新建部门', 'right' => '<a href="/s.php?module=department">返回列表</a>','body'=>$body));
	}

	// 
	function doupdate(){
		if($this->kclass->input['parentid'] == $this->kclass->input['departmentid']){
			$errors[] = '你不能将一个部门自身选做它的父部门！';
		}else{
			$departmentinfo = $this->kclass->DB->queryFirst("
				SELECT departmentid,title,parentlist 
				FROM `department` 
				WHERE departmentid='".$parentid."'
			");
			$parents = explode(",", $departmentinfo['parentlist']);
			$touchon = 0;
			while (list(,$val) = each($parents)) {
				if ($val==$this->kclass->input['departmentid']) {
					$touchon = 1;
				}
			}

			if($touchon){
				$errors[] = '你不能将一个列项设置为它的子列项的列项！';
			}
			if($this->kclass->input['title'] == ''){
				$errors[] = '请填写部门标题。';
			}
			if($this->kclass->input['entitle'] == ''){
				$errors[] = '请填写部门 英文标题 标题。';
			}
		}

		if(count($errors)){
			$error = implode("</li>\n<li>",$errors);
			$error = "<ul>\n<li>".$error."</li></ul>";
			$this->kclass->messager(array(
					'title' => '修改部门',
					'text' => '您在修改部门的过程中有以下错误：'.$error,
					'url' => 'javascript:history.back()',
					'final' => 1,
					'sec' => 3
				)
			);
		}

		$parentlist = $this->kclass->input['departmentid'].",".$this->_makeparentlist($this->kclass->input['parentid']);
		$this->kclass->DB->query("
			UPDATE `department` 
			SET title = '".$this->kclass->input['title']."', 
				entitle = '".$this->kclass->input['entitle']."', 
				departmentno = '".$this->kclass->input['departmentno']."',
				abbr = '".$this->kclass->input['abbr']."',
				description = '".$this->kclass->input['description']."', 
				ordering = '".$this->kclass->input['ordering']."', 
				parentid = '".$this->kclass->input['parentid']."', 
				display = '".$this->kclass->input['display']."', 
				publish = '".$this->kclass->input['publish']."', 
				parentlist = '".$parentlist."', 
				modifier = '".$this->kclass->user['userid']."', 
				modified = '".TIMENOW."' 
			WHERE departmentid='".$this->kclass->input['departmentid']."'
		");

		$this->kclass->messager(array(
				'title' => '修改部门',
				'text' => '部门 <b>'.$this->kclass->input['title'].'</b> 已成功被修改!',
				'url' => '/s.php?module=department',
				'final' => 1,
				'sec' => 3
			)
		);
	}

	// 
	function update(){
		if($this->kclass->input['departmentid']<=0){
			$this->kclass->boink_it('/s.php?module=department.html');
		}
		$department = $this->kclass->DB->queryFirst("
			SELECT *
			FROM department
			WHERE departmentid='".$this->kclass->input['departmentid']."'
		");
		if($department['display'] == 1){
			$display_1 = ' checked';
		}else{
			$display_0 = ' checked';
		}
		if($department['publish'] == 1){
			$publish_1 = ' checked';
		}else{
			$publish_0 = ' checked';
		}
		$select = $this->kclass->chooserdepartment(array(
				'name' => 'parentid', 
				'selectedid' => $department['parentid'],
				'departmentid' => 0, 
				'displaytop' => 1, 
				'hasBlank' => 1, 
				'topname' => '根',
				'showall' => 1,
				'width'=>306
			)	
		);
$body = <<<EOF
<form action="/s.php?module=department&action=doupdate" name="name" method="post">
<input type="hidden" name="module" value="department">
<input type="hidden" name="action" value="doupdate">
<input type="hidden" name="departmentid" value="{$this->kclass->input['departmentid']}">
<table>
<thead>
<tr>
	<th colspan="2">修改部门: </th>
</tr>
</thead>
<tbody>
<tr class="even">
	<td>上级列项：</td>
	<td>{$select}</td>
</tr>
<tr class="even">
	<td>部门编号：<font color="red">*</font></td>
	<td><input type="text" style="width: 300px;" name="departmentno" value="{$department['departmentno']}"></td>
</tr>
<tr class="odd">
	<td>标　　题：<font color="red">*</font></td>
	<td><input type="text" style="width: 300px;" name="title" value="{$department['title']}"></td>
</tr>
<tr class="even">
	<td>英文标题：<font color="red">*</font></td>
	<td><input type="text" style="width: 300px;" name="entitle" value="{$department['entitle']}"></td>
</tr>
<tr class="even">
	<td>部门代号：</td>
	<td><input type="text" style="width: 300px;" name="abbr" value="{$department['abbr']}"></td>
</tr>
<tr class="odd">
	<td valign="top">描　　述：</td>
	<td><textarea name="description" style="width: 300px;height: 50px;">{$department['description']}</textarea></td>
</tr>
<tr class="even">
	<td>顺　　序：</td>
	<td><input type="text" style="width: 300px;" name="ordering" value="{$department['ordering']}"></td>
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
	<td colspan="2" class="gray small">如果选择不显示，那么网站前台所有该部门下的产品页将不显示，即使部门下的产品是“发布”状态。</td>
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
		$this->kclass->page['title'] = ' - 修改部门';
		$this->kclass->page['main'] .= $this->kclass->skin->dbody(array('title' => $this->baseurl.' - 修改部门', 'right' => $this->right, 'body'=>$body));
	}

	// 
	function kill(){
		if($this->kclass->input['confirm'] == 0){
			$this->kclass->boink_it('/s.php?module=department.html');
		}
		if($this->kclass->input['departmentid'] < 0){
			$errors[] = '请从正确的页面进行操作，如果此问题持续存在，请联系网站管理员。';
		}
		if(count($errors)){
			$error = implode("</li>\n<li>",$errors);
			$error = "<ul>\n<li>".$error."</li></ul>";
			$this->kclass->messager(array(
					'title' => '删除部门',
					'text' => '您在删除部门的过程中有以下错误：'.$error,
					'url' => 'javascript:history.back()',
					'final' => 1,
					'sec' => 3
				)
			);
		}

		$department = $this->kclass->DB->queryFirst("
			SELECT title, parentid 
			FROM `department` 
			WHERE departmentid = '".$this->kclass->input['departmentid']."'
		");
		$departments=$this->kclass->DB->query("
			SELECT departmentid 
			FROM `department` 
			WHERE INSTR(CONCAT(',',parentlist,','), ',".$this->kclass->input['departmentid'].",')>0
		");
		$departmentlist = $this->kclass->input['departmentid'];
		if($this->kclass->DB->numRows()){
			while($thisdepartment=$this->kclass->DB->fetchArray($departments)) {
				$departmentlist .= ",$thisdepartment[departmentid]";
			}
		}
		$this->kclass->DB->query("
			DELETE FROM `department` 
			WHERE departmentid IN (".$departmentlist.")
		");
		$this->kclass->DB->query("
			UPDATE `department` 
			SET node = (node - 1) 
			WHERE departmentid IN (".$departmentlist.")
		");
		$this->kclass->DB->query("
			UPDATE `department` 
			SET node = (node - 1) 
			WHERE departmentid = '".$department['parentid']."'
		");
		$this->kclass->messager(array(
				'title' => '删除部门',
				'text' => '部门 <b>'.$department['title'].'</b> 已成功被删除!',
				'url' => '/s.php?module=department',
				'final' => 1,
				'sec' => 3
			)
		);
	}

	// 
	function remove(){
		$department = $this->kclass->DB->queryFirst("
			SELECT title
			FROM department
			WHERE departmentid='".$this->kclass->input['departmentid']."'
		");
$body = <<<EOF
<form action="/s.php?module=department&action=kill" name="name" method="post">
<input type="hidden" name="module" value="department">
<input type="hidden" name="action" value="kill">
<input type="hidden" name="departmentid" value="{$this->kclass->input['departmentid']}">
<table>
<thead>
<tr>
	<th colspan="2">删除部门: </th>
</tr>
</thead>
<tbody>
<tr class="even">
	<td colspan="2">你确定要删除部门: <b>{$department['title']}</b> 吗?<br />(注意：此列项下包含的所有子类也会同时被删除！)</td>
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
		$this->kclass->page['title'] = ' - 删除部门';
		$this->kclass->page['main'] .= $this->kclass->skin->title(array('title' => $this->baseurl.' - 删除部门', 'right' => $this->right));
		$this->kclass->page['main'] .= $this->kclass->skin->body($body);
	}

	// 
	function doorder(){
		foreach ($this->kclass->input['order'] as $k => $v) {
			$this->kclass->DB->query("
				UPDATE department
				SET ordering='".$v."'
				WHERE departmentid='".$k."'
			");
		}
		
		$this->kclass->messager(array(
				'title' => '部门排序',
				'text' => '部门 重新排序 成功!',
				'url' => '/s.php?module=department',
				'final' => 1,
				'sec' => 3
			)
		);
	}

	// 
	function mmlist(){
		$department = $this->_departmentlist(-1);
$body = <<<EOF
<form action="/s.php?module=department&action=doorder" name="name" method="post">
<input type="hidden" name="module" value="department">
<input type="hidden" name="action" value="doorder">
<table>
EOF;

if($department){
$body .= <<<EOF
<thead>
<tr>
	<th>网站发布</th>
	<th>中文</th>
	<th>英文</th>
	<th>编号</th>
	<th>代号</th>
	<th>排序</th>
	<th>选项</th>
</tr>
</thead>
<tbody>
{$department}
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
	<td>暂无部门.</td>
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
	function _departmentlist($parentid = -1){
		$departments=$this->kclass->DB->query("
			SELECT * 
			FROM `department` 
			WHERE parentid='".$parentid."'
			ORDER BY ordering
		");

		if($this->kclass->DB->numRows()){
			while($department = $this->kclass->DB->fetchArray($departments)) {
				$r .= '<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').'"><td align="center">'.$this->kclass->iif($department['publish'] == 1, '是', '否').'</td><td class="middle bold">' . $this->kclass->iif($parentid > 0, '　　　', '') . '<a href="/s.php?module=product&action=list&departmentid='.$department['departmentid'].'">'.$department['title'].'</a></td><td>'.$department['entitle'].'</td><td>'.$department['departmentno'].'</td><td>'.$department['abbr'].'</td><td align="right"><input type="text" name="order['.$department['departmentid'].']" size="1" value="'.$department['ordering'].'"></td><td><a href="/s.php?module=department&action=remove&departmentid='.$department['departmentid'].'" class="small">删除</a> <a href="/s.php?module=department&action=update&departmentid='.$department['departmentid'].'">编辑</a>' .$this->kclass->iif($parentid==-1, ' <a href="/s.php?module=department&action=add&parentid='.$department['departmentid'].'"><span class="small">添加</span>子类</a>', '').'</td></tr>';
				if($temp = $this->_departmentlist($department['departmentid'])){
					$r .= $temp;
				}
//				$r .= '</li>';
			}
		}
		return $r;
	}

	// 
	function _chooser($c){
		if ($c['departmentid']==0) {
			$r .= '<select name="'.$c['name'].'" style="width: 300">';
			if ($c['displaytop']==1) {
				$r .= '<option value="0"'.$this->kclass->iif($c['selectedid']==$c['departmentid'], ' selected', '').'>'.$c['depth'].$c['topname'].'</option>';
			}
		} else {
			$condition = "departmentid='".$c['departmentid']."'";
			$department = $this->kclass->DB->queryFirst("
				SELECT departmentid,title
				FROM `department`
				WHERE $condition
			");
			$r .= '<option value="'.$department['departmentid'].'"'.$this->kclass->iif($c['selectedid']==$c['departmentid'], ' selected', '').'>'.$c['depth'].$department['title'].'</option>';
		}

		$c['depth'] .= "--";

		$ncondition .= "parentid = '".$c['departmentid']."'";

		$departments=$this->kclass->DB->query("
			SELECT departmentid 
			FROM `department` 
			WHERE $ncondition
			ORDER BY ordering
		");
		if($this->kclass->DB->numRows()){
			while ($department = $this->kclass->DB->fetchArray($departments)) {
				$r .= $this->kclass->chooserdepartment(array(
					'name' => 'departmentid', 
					'selectedid' => $c['selectedid'], 
					'departmentid' => $department['departmentid'], 
					'depth' => $c['depth'], 
					'topname' => $c['topname'], 
					'displaytop' => 1, 
					'displayid' => $c['displayid'], 
					'userid' => $c['userid']
				));
			}
		}
		if ($c['departmentid'] == 0) {
			$r .= '</select>';
		}

		return $r;
	}

	// 
	function _makeparentlist($departmentid){
		$departmentinfo = $this->kclass->DB->queryFirst("
			SELECT parentid 
			FROM `department` 
			WHERE departmentid = '".$departmentid."'
		");

		$departmentarray = $departmentid;

		if ($departmentinfo['parentid'] != -1) {
		  $parentlist = $this->kclass->DB->queryFirst("
				SELECT parentlist 
				FROM `department` 
				WHERE departmentid='".$departmentinfo['parentid']."'
			");

			$departmentarray .= ",".$parentlist['parentlist'];
		}

		if (substr($departmentarray, -2)!="-1") {
			$departmentarray.=",-1";
		}
		return $departmentarray;
	}
}
?>