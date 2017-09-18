<?php
// 
class flow{
	# Global
	var $kclass;
	var $cache=array();
	var $icache=array();

	/*--------------------*/
	// Auto run function
	/*--------------------*/
	function autoRun(){
		$this->kclass->page['title'] = '操作流程';
		$this->baseurl = '<a href="/s.php?module=home">首页</a> - <a href="/s.php?module=flow">操作流程</a>';
		$this->right = '<a href="/s.php?module=flow&action=add">新建操作流程</a>';

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
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			for($m=1;$m<9;$m++){
				if($this->kclass->input['itemModule'][$m]!=''){
					if(!$this->kclass->input['itemTitle'][$m]){
						$e.='<li>请填写[ID:'.$m.']的 操作流程名称</li>';
					}
					if(!$this->kclass->input['itemStatus'][$m]){
						$e.='<li>请填写[ID:'.$m.']的 状态码</li>';
					}
					if(!$this->kclass->input['itemOrdering'][$m]){
						$e.='<li>请填写[ID:'.$m.']的 排序</li>';
					}
				}
			}
		}
		if($e){
			$this->kclass->messager(array(
					'title' => '新建操作流程',
					'text' => '您在新建操作流程的过程中有以下错误: <ul>'.$e.'</ul>',
					'url' => 'javascript:history.back()',
					'final' => 1,
					'sec' => 3
				)
			);
		}
		for($m=1;$m<9;$m++){
			if($this->kclass->input['itemModule'][$m]!=''){
				$this->kclass->DB->query("
					INSERT INTO `flow` 
						(`title`,`statusid`, `module`,`remark`,`ordering`,`creator`,`created`) 
					VALUES 
						('".$this->kclass->input['itemTitle'][$m]."','".$this->kclass->input['itemStatus'][$m]."','".$this->kclass->input['itemModule'][$m]."','".$this->kclass->input['itemRemark'][$m]."','".$this->kclass->input['itemOrdering'][$m]."','".$this->kclass->user['userid']."','".TIMENOW."')
				");
			}
		}
		$this->kclass->messager(array(
				'title' => '新建操作流程',
				'text' => '操作流程 <b>'.$this->kclass->input['title'].'</b> 已新建成功!',
				'url' => '/s.php?module=flow',
				'final' => 1,
				'sec' => 3
			)
		);
	}

	// 
	function add(){
		for($i=1;$i<9;$i++){
			$itemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').'">
				<td>'.$i.'</td>
				<td><input type="text" name="itemModule['.$i.']" size="20"></td>
				<td><input type="text" name="itemTitle['.$i.']" size="30"></td>
				<td><input type="text" name="itemStatus['.$i.']" size="8"></td>
				<td><input type="text" name="itemRemark['.$i.']" size="16"></td>
				<td><input type="text" name="itemOrdering['.$i.']" value="'.$i.'" size="5"></td></tr>';
		}

$body = <<<EOF
<form action="/s.php?module=flow&action=insert" name="flow" method="post">
<input type="hidden" name="module" value="flow">
<input type="hidden" name="action" value="insert">
<table width="60%">
<thead>
<tr>
	<th colspan="8">添加操作流程：</th>
</tr>
</thead>
<tbody>
<tr class="bold even"><td>ID</td><td>模块 <span class="bold red">*</span></td><td>操作流程名称 <span class="bold red">*</span></td><td>状态码 <span class="bold red">*</span></td><td>备注</td><td>排序</td></tr>
{$itemtr}
<tr class="even">
	<td colspan="8" class="gray small">注意事项：<br>①如果所列表格不够，那么在提交保存后再点击“修改”来增加操作流程；<br></td>
</tr>
<tr class="odd">
	<td colspan="8" align="center">
		<input type="submit" name="submit" value="   保存   " accesskey="s">
		<input type="reset" value="   重置   ">
	</td>
</tr>
</tbody>
</table>
</form>
EOF;
		$this->kclass->page['onload'] .= 'dc.tabhover();';
		$this->kclass->page['title'] = ' 新建操作流程';
		$this->kclass->page['main'] .= $this->kclass->skin->dbody(array('title' => $this->baseurl.' - 新建操作流程', 'right' => '<a href="/s.php?module=flow">返回列表</a>','body'=>$body));
	}

	// 
	function doupdate(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			$j=count($this->kclass->input['itemflowid']);
			for($m=1;$m<$j+1;$m++){
				if($this->kclass->input['itemModule'][$m]==''){
					$e.='<li>请填写[ID:'.$m.']的 模块。 </li>';
				}
				if($this->kclass->input['itemTitle'][$m]==''){
					$e.='<li>请填写[ID:'.$m.']的 流程名称。 </li>';
				}
				if($this->kclass->input['itemStatus'][$m]==''){
					$e.='<li>请填写[ID:'.$m.']的 状态码。 </li>';
				}
				if($this->kclass->input['itemOrdering'][$m]==''){
					$e.='<li>请填写[ID:'.$m.']的 排序。 </li>';
				}
			}
		}

		if($e){
			$this->kclass->messager(array(
					'title' => '修改操作流程',
					'text' => '您在修改操作流程的过程中有以下错误：<ul>'.$e.'</ul>',
					'url' => 'javascript:history.back()',
					'final' => 1,
					'sec' => 3
				));
		}
		$j=count($this->kclass->input['itemflowid']);
		for($m=1;$m<$j+1;$m++){
			if($this->kclass->input['itemKill'][$m]!=''){
				$this->kclass->DB->query("UPDATE `flow` SET killed='".TIMENOW."',killer='".$this->kclass->user['userid']."' WHERE flowid='".$this->kclass->input['itemKill'][$m]."'");
			}else{
				$this->kclass->DB->query("
					UPDATE `flow` SET 
						`module`='".$this->kclass->input['itemModule'][$m]."', 
						`title`='".$this->kclass->input['itemTitle'][$m]."', 
						`statusid` = '".$this->kclass->input['itemStatus'][$m]."', 
						`ordering` = '".$this->kclass->input['itemOrdering'][$m]."', 
						`remark` = '".$this->kclass->input['itemRemark'][$m]."', 
						modifier = '".$this->kclass->user['userid']."', 
						modified = '".TIMENOW."' 
					WHERE flowid='".$this->kclass->input['itemflowid'][$m]."'
				");
			}
		}
		$this->kclass->messager(array(
				'title' => '修改操作流程',
				'text' => '操作流程 <b>'.$this->kclass->input['title'].'</b> 已成功被修改!',
				'url' => '/s.php?module=flow',
				'final' => 1,
				'sec' => 3
		));
	}

	// 
	function update(){
		$flows=$this->kclass->DB->query("
			SELECT f.* 
			FROM `flow` AS f
			WHERE f.killed=0 
			ORDER BY f.module ASC,f.ordering ASC
		");
		if($this->kclass->DB->numRows()){
			$i=1;
			while($flow=$this->kclass->DB->fetchArray($flows)){
				$itemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').'">
				<td>'.$i.'<input type="hidden" name="itemflowid['.$i.']" value="'.$flow['flowid'].'"></td>
				<td><input type="text" name="itemModule['.$i.']" size="20" value="'.$flow['module'].'"></td>
				<td><input type="text" name="itemTitle['.$i.']" size="30" value="'.$flow['title'].'"></td>
				<td><input type="text" name="itemStatus['.$i.']" size="10" value="'.$flow['statusid'].'"></td>
				<td><input type="text" name="itemRemark['.$i.']" size="16" value="'.$flow['remark'].'"></td>
				<td><input type="text" name="itemOrdering['.$i.']" size="5" value="'.$flow['ordering'].'"></td>
				<td><input type="checkbox" name="itemKill['.$i.']" value="'.$flow['flowid'].'"></td>
				</tr>';
				$i++;
			}
		}
	
		
$body = <<<EOF
<form action="/s.php?module=flow&action=doupdate" name="name" method="post">
<input type="hidden" name="module" value="flow">
<input type="hidden" name="action" value="doupdate">
<input type="hidden" name="flowid" value="{$this->kclass->input['flowid']}">
<table width="60%">
<thead>
<tr>
	<th colspan="8">修改操作流程: </th>
</tr>
</thead>
<tbody>
<tr class="bold even"><td>ID</td><td>模块 <span class="bold red">*</span></td><td>操作流程名称 <span class="bold red">*</span></td><td>状态码 <span class="bold red">*</span></td><td>备注</td><td>排序</td><td>删</td></tr>
{$itemtr}
<tr class="even">
	<td colspan="8" class="gray small">注意事项：<br>①如果所列表格不够，那么在提交保存后再点击“修改”，来增加操作流程；<br></td>
</tr>
<tr class="odd">
	<td colspan="8" align="center">
		<input type="submit" name="submit" value="   保存   " accesskey="s">
		<input type="reset" value="   重置   ">
	</td>
</tr>
</tbody>
</table>
</form>
EOF;
		$this->kclass->page['onload'] .= 'dc.tabhover();';
		$this->kclass->page['title'] = '修改操作流程';
		$this->kclass->page['main'] .= $this->kclass->skin->dbody(array('title' => $this->baseurl.' - 修改操作流程', 'right' => $this->right, 'body'=>$body));
	}

	// 
	function kill(){
		if($this->kclass->input['confirm'] == 0){
			$this->kclass->boinkIt('/s.php?module=flow');
		}
		if($this->kclass->input['flowid'] < 0 OR $this->kclass->input['flowid']<0){
			$this->kclass->boinkIt('/s.php?module=flow');
		}
		if($this->kclass->input['requestMethod'] != 'post'){
			$e='<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}
		if($e){
			$this->kclass->messager(array(
					'title' => '删除操作流程',
					'text' => '您在删除操作流程的过程中有以下错误：'.$error,
					'url' => 'javascript:history.back()',
					'final' => 1,
					'sec' => 3
				)
			);
		}
		if($this->kclass->input['flowid']){
			$condition='flowid='.$this->kclass->input['flowid'];
			$this->kclass->DB->query("
				UPDATE `flow` SET 
					killer='".$this->kclass->user['userid']."',
					killed='".TIMENOW."' 
				WHERE flowid='".$this->kclass->input['flowid']."'
			");
		}elseif($this->kclass->input['flowid']){
			$condition='flowid='.$this->kclass->input['flowid'];
			$this->kclass->DB->query("
				UPDATE `flow` SET 
					killer='".$this->kclass->user['userid']."',
					killed='".TIMENOW."' 
				WHERE flowid='".$flow['flowid']."'
			");
		}
		$this->kclass->messager(array(
				'title' => '删除操作流程',
				'text' => '物料 <b>'.$flow['materialno'].'</b> 的操作流程已成功被删除!',
				'url' => '/s.php?module=flow',
				'final' => 1,
				'sec' => 3
			)
		);
	}

	// 
	function remove(){
		if($this->kclass->input['flowid']){
			$condition='flowid='.$this->kclass->input['flowid'];
		}
		if($this->kclass->input['flowid']){
			$condition='flowid='.$this->kclass->input['flowid'];
		}
		$flow = $this->kclass->DB->queryFirst("
			SELECT `flow`.*,
				`material`.materialno
			FROM `flow` 
			LEFT JOIN `material` ON (`material`.materialid=`flow`.flowid)
			WHERE $condition
		");

$body = <<<EOF
<form action="/s.php?module=flow&action=kill" name="name" method="post">
<input type="hidden" name="module" value="flow">
<input type="hidden" name="action" value="kill">
<input type="hidden" name="flowid" value="{$this->kclass->input['flowid']}">
<input type="hidden" name="flowid" value="{$this->kclass->input['flowid']}">
<table>
<thead>
<tr>
	<th colspan="2">删除操作流程: </th>
</tr>
</thead>
<tbody>
<tr class="even">
	<td colspan="2">你确定要删除物资: <b>{$flow['materialno']}</b> 的操作流程吗?<br />(注意：此列项下包含的所有子类也会同时被删除！)</td>
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
		$this->kclass->page['title'] = ' - 删除操作流程';
		$this->kclass->page['main'] .= $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 删除操作流程', 'right' => $this->right, 'body'=>$body));
	}

	// 
	function doorder(){
		foreach($this->kclass->input['order'] as $k => $v){
			$this->kclass->DB->query("
				UPDATE `flow`
				SET `ordering`='".$v."'
				WHERE `flowid`='".$k."'
			");
		}
		
		$this->kclass->messager(array(
				'title' => '操作流程排序',
				'text' => '操作流程 重新排序 成功!',
				'url' => '/s.php?module=flow',
				'final' => 1,
				'sec' => 1
			)
		);
	}

	// 
	function mmlist(){
		$flows=$this->kclass->DB->query("
			SELECT f.* 
			FROM `flow` AS f
			WHERE f.killed=0 
			ORDER BY f.module ASC,f.ordering ASC
		");
		if($this->kclass->DB->numRows()){
			while($flow=$this->kclass->DB->fetchArray($flows)){
				$itemtr.='<tr class="'.$this->kclass->rotateLine().' small">
					<td>'.date('Y-m-d',$flow['created']).'</td>
					<td><input type="text" name="order['.$flow['flowid'].']" size="1" value="'.$flow['ordering'].'"></td>
					<td>'.$flow['module'].'</td>
					<td class="bold">'.$flow['title'].'</td>
					<td>'.$flow['statusid'].'</td>
					<td>'.$flow['remark'].'</td>
					<td>'.$this->kclass->iif($flow['modified']==0,'',date('Y-m-d',$flow['modified'])).'</td>
					</tr>'.$flowItemtr;
			}
		}

$body = <<<EOF
<form action="/s.php?module=flow&action=doorder" name="name" method="post">
<input type="hidden" name="module" value="flow">
<input type="hidden" name="action" value="doorder">
<table>
EOF;

if($itemtr){
$body .= <<<EOF
<thead>
<tr>
	<th width="70">建立日期</th>
	<th width="40">排序</th>
	<th width="150">模块</th>
	<th width="220">操作流程</th>
	<th width="70">状态码</th>
	<th width="100">备注</th>
	<th width="60">修改日期</th>
</tr>
</thead>
<tbody>
{$itemtr}
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
	<td>暂无操作流程.</td>
</tr>
</form>
</table>
EOF;
}
		$this->kclass->page['onload'] .= 'dc.tabhover();';
		$this->kclass->page['title'] = '操作流程 - 列表';
		$this->kclass->page['main'] .= $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 列表', 'right' => $this->right.'　|　<a href="/s.php?module=flow&action=update">编辑操作流程</a>', 'body'=>$body));
	}

}
?>