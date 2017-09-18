<?php
// 
class routing{
	# Global
	var $kclass;
	var $cache=array();
	var $icache=array();

	/*--------------------*/
	// Auto run function
	/*--------------------*/
	function autoRun(){
		$this->kclass->page['title'] = '工艺路线';
		$this->baseurl = '<a href="/s.php?module=home">首页</a> - <a href="/s.php?module=routing">工艺路线</a>';
		$this->right = '<a href="/s.php?module=routing&action=add">新建工艺路线</a>';

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
			if($this->kclass->input['title']==''){
				$e.='<li>请填写工艺名称。 </li>';
			}
		}
		if($e){
			$this->kclass->messager(array(
					'title' => '新建工艺路线',
					'text' => '您在新建工艺路线的过程中有以下错误: <ul>'.$e.'</ul>',
					'url' => 'javascript:history.back()',
					'final' => 1,
					'sec' => 3
				)
			);
		}
		$this->kclass->DB->query("
			INSERT INTO `routing` (`title`,`workcenterid`,`remark`,`ordering`,`created`,`creator`)
				VALUES ('".$this->kclass->input['title']."','".$this->kclass->input['workcenterid']."','".$this->kclass->input['remark']."','".$this->kclass->input['ordering']."','".TIMENOW."','".$this->kclass->user['userid']."')
		");
		$routingid=$this->kclass->DB->insertID();
		for($m=1;$m<9;$m++){
			if($this->kclass->input['itemTitle'][$m]!=''){
				$this->kclass->DB->query("
					INSERT INTO `procedure` 
						(`routingid`,`title`,`step`,`remark`,`ordering`,`creator`,`created`) 
					VALUES 
						('".$routingid."','".$this->kclass->input['itemTitle'][$m]."','".$this->kclass->input['itemStep'][$m]."','".$this->kclass->input['itemRemark'][$m]."','".$this->kclass->input['itemOrdering'][$m]."','".$this->kclass->user['userid']."','".TIMENOW."')
				");
			}
		}
		$this->kclass->messager(array(
				'title' => '新建工艺路线',
				'text' => '工艺路线 <b>'.$this->kclass->input['title'].'</b> 已新建成功!',
				'url' => '/s.php?module=routing',
				'final' => 1,
				'sec' => 3
			)
		);
	}

	// 
	function add(){
		$select=$this->kclass->chooserWorkcenter(array('name'=>'workcenterid','width'=>200,'hasBlank'=>0,'selectedid'=>1));
	
		for($i=1;$i<9;$i++){
			$itemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').'">
				<td>'.$i.'</td>
				<td><input type="text" name="itemTitle['.$i.']" size="30"></td>
				<td><input type="text" name="itemStep['.$i.']" size="30"></td>
				<td><input type="text" name="itemRemark['.$i.']" size="16"></td>
				<td><input type="text" name="itemOrdering['.$i.']" value="'.$i.'" size="5"></td></tr>';
		}

$body = <<<EOF
<form action="/s.php?module=routing&action=insert" name="routing" method="post">
<input type="hidden" name="module" value="routing">
<input type="hidden" name="action" value="insert">
<table width="60%">
<thead>
<tr>
	<th colspan="8">添加工艺路线：</th>
</tr>
</thead>
<tbody>
<tr class="even">
	<td>工艺名称：<span class="bold red">*</span></td>
	<td><input type="text" name="title" size="35"></td>
	<td>选择工作中心：<span class="bold red">*</span></td>
	<td>{$select}</td>
</tr>
<tr class="odd">
	<td>备注：<span class="bold red">*</span></td>
	<td width="300"><input type="text" name="remark" size="35"></td>
	<td>排序：<span class="bold red">*</span></td>
	<td><input type="text" name="ordering" size="5" value="0"></td>
</tr>
</tbody>
</table>
<table width="60%">
<thead>
<tr>
	<th colspan="8">添加工序：</th>
</tr>
</thead>
<tbody>
<tr class="bold even"><td>ID</td><td>工序名称 <span class="bold red">*</span></td><td>操作步骤 <span class="bold red">*</span></td><td>备注 <span class="bold red">*</span></td><td>排序</td></tr>
{$itemtr}
<tr class="even">
	<td colspan="8" class="gray small">注意事项：<br>①如果所列表格不够，那么在提交保存后再点击“修改”来增加工序；<br></td>
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
		$this->kclass->page['title'] = ' 新建工艺路线';
		$this->kclass->page['main'] .= $this->kclass->skin->dbody(array('title' => $this->baseurl.' - 新建工艺路线', 'right' => '<a href="/s.php?module=routing">返回列表</a>','body'=>$body));
	}

	// 
	function doupdate(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			if($this->kclass->input['title']==''){
				$e.='<li>请填写工艺名称。 </li>';
			}
		}

		if($e){
			$this->kclass->messager(array(
					'title' => '修改工艺路线',
					'text' => '您在修改工艺路线的过程中有以下错误：<ul>'.$e.'</ul>',
					'url' => 'javascript:history.back()',
					'final' => 1,
					'sec' => 3
				)
			);
		}
		$this->kclass->DB->query("
						UPDATE `routing` SET 
							`workcenterid`='".$this->kclass->input['workcenterid']."', 
							`title` = '".$this->kclass->input['title']."', 
							`ordering` = '".$this->kclass->input['ordering']."', 
							`remark` = '".$this->kclass->input['remark']."', 
							modifier = '".$this->kclass->user['userid']."', 
							modified = '".TIMENOW."' 
						WHERE routingid='".$this->kclass->input['routingid']."'
					");
		$j=count($this->kclass->input['itemProcedureid']);
		for($m=1;$m<$j+1;$m++){
				if($this->kclass->input['itemKill'][$m]!=''){
					$this->kclass->DB->query("UPDATE `procedure` SET killed='".TIMENOW."',killer='".$this->kclass->user['userid']."' WHERE procedureid='".$this->kclass->input['itemKill'][$m]."'");
				}else{
					$this->kclass->DB->query("
						UPDATE `procedure` SET 
							`title`='".$this->kclass->input['itemTitle'][$m]."', 
							`step` = '".$this->kclass->input['itemStep'][$m]."', 
							`ordering` = '".$this->kclass->input['itemOrdering'][$m]."', 
							`remark` = '".$this->kclass->input['itemRemark'][$m]."', 
							modifier = '".$this->kclass->user['userid']."', 
							modified = '".TIMENOW."' 
						WHERE procedureid='".$this->kclass->input['itemProcedureid'][$m]."'
					");
				}
		}
		for($i=$m;$i<$m+5;$i++){
				if($this->kclass->input['itemTitle'][$i]!=''){
					$this->kclass->DB->query("
						INSERT INTO `procedure` 
							(`routingid`,`title`,`step`,`remark`,`ordering`,`creator`,`created`) 
						VALUES 
							('".$this->kclass->input['routingid']."','".$this->kclass->input['itemTitle'][$i]."','".$this->kclass->input['itemStep'][$i]."','".$this->kclass->input['itemRemark'][$i]."','".$this->kclass->input['itemOrdering'][$i]."','".$this->kclass->user['userid']."','".TIMENOW."')
					");
				}
			}

		$this->kclass->messager(array(
				'title' => '修改工艺路线',
				'text' => '工艺路线 <b>'.$this->kclass->input['title'].'</b> 已成功被修改!',
				'url' => '/s.php?module=routing',
				'final' => 1,
				'sec' => 3
		));
	}

	// 
	function update(){
		if($this->kclass->input['routingid']<=0){
			$this->kclass->boinkIt('/s.php?module=routing');
		}
		$routing=$this->kclass->DB->queryFirst("SELECT * FROM `routing` WHERE routingid='".$this->kclass->input['routingid']."' LIMIT 0,1");
		$select=$this->kclass->chooserWorkcenter(array('name'=>'workcenterid','width'=>200,'hasBlank'=>0,'selectedid'=>$routing['workcenterid']));
		$procedures=$this->kclass->DB->query("
			SELECT * 
			FROM `procedure`
			WHERE killed=0 AND routingid='".$this->kclass->input['routingid']."' 
			ORDER BY ordering ASC
		");
		if($this->kclass->DB->numRows()){
			$i=1;
			while($procedure=$this->kclass->DB->fetchArray($procedures)){
				$itemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').'">
				<td>'.$i.'<input type="hidden" name="itemProcedureid['.$i.']" value="'.$procedure['procedureid'].'"></td>
				<td><input type="text" name="itemTitle['.$i.']" size="30" value="'.$procedure['title'].'"></td>
				<td><input type="text" name="itemStep['.$i.']" size="30" value="'.$procedure['step'].'"></td>
				<td><input type="text" name="itemRemark['.$i.']" size="16" value="'.$procedure['remark'].'"></td>
				<td><input type="text" name="itemOrdering['.$i.']" size="5" value="'.$procedure['ordering'].'"></td>
				<td><input type="checkbox" name="itemKill['.$i.']" value="'.$procedure['procedureid'].'"></td>
				</tr>';
				$i++;
			}
			$itemtr.='<tr><th colspan="8">新增工序: </th></tr>';
		}
		for($k=$i;$k<$i+5;$k++){
			$itemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').'">
				<td>'.$k.'</td>
				<td><input type="text" name="itemTitle['.$k.']" size="30"></td>
				<td><input type="text" name="itemStep['.$k.']" size="30"></td>
				<td><input type="text" name="itemRemark['.$k.']" size="16"></td>
				<td><input type="text" name="itemOrdering['.$k.']" value="'.$k.'" size="5"></td>
				<td></td></tr>';
		}
$body = <<<EOF
<form action="/s.php?module=routing&action=doupdate" name="name" method="post">
<input type="hidden" name="module" value="routing">
<input type="hidden" name="action" value="doupdate">
<input type="hidden" name="routingid" value="{$this->kclass->input['routingid']}">
<table width="60%">
<thead>
<tr>
	<th colspan="8">修改工艺路线: </th>
</tr>
</thead>
<tbody>
<tr class="even">
	<td>工艺名称：<span class="bold red">*</span></td>
	<td><input type="text" name="title" size="35" value="{$routing['title']}"></td>
	<td>选择工作中心：<span class="bold red">*</span></td>
	<td>{$select}</td>
</tr>
<tr class="odd">
	<td>备注：<span class="bold red">*</span></td>
	<td width="300"><input type="text" name="remark" size="35" value="{$routing['remark']}"></td>
	<td>排序：<span class="bold red">*</span></td>
	<td><input type="text" name="ordering" size="5" value="{$routing['ordering']}"></td>
</tr>
</tbody>
</table>
<table width="60%">
<thead>
<tr>
	<th colspan="8">当前工序: </th>
</tr>
</thead>
<tbody>
<tr class="bold even center"><td>ID</td><td>工序名称 <span class="bold red">*</span></td><td>操作步骤 <span class="bold red">*</span></td><td>备注 <span class="bold red">*</span></td><td>排序</td><td>删</td></tr>
{$itemtr}
<tr class="even">
	<td colspan="8" class="gray small">注意事项：<br>①如果所列表格不够，那么在提交保存后再点击“修改”，来增加工艺路线；<br></td>
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
		$this->kclass->page['title'] = ' - 修改工艺路线';
		$this->kclass->page['main'] .= $this->kclass->skin->dbody(array('title' => $this->baseurl.' - 修改工艺路线', 'right' => $this->right, 'body'=>$body));
	}

	// 
	function kill(){
		if($this->kclass->input['confirm'] == 0){
			$this->kclass->boinkIt('/s.php?module=routing');
		}
		if($this->kclass->input['routingid'] < 0 OR $this->kclass->input['routingid']<0){
			$this->kclass->boinkIt('/s.php?module=routing');
		}
		if($this->kclass->input['requestMethod'] != 'post'){
			$e='<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}
		if($e){
			$this->kclass->messager(array(
					'title' => '删除工艺路线',
					'text' => '您在删除工艺路线的过程中有以下错误：'.$error,
					'url' => 'javascript:history.back()',
					'final' => 1,
					'sec' => 3
				)
			);
		}
		if($this->kclass->input['routingid']){
			$condition='routingid='.$this->kclass->input['routingid'];
			$this->kclass->DB->query("
				UPDATE `routing` SET 
					killer='".$this->kclass->user['userid']."',
					killed='".TIMENOW."' 
				WHERE routingid='".$this->kclass->input['routingid']."'
			");
		}elseif($this->kclass->input['routingid']){
			$condition='routingid='.$this->kclass->input['routingid'];
			$this->kclass->DB->query("
				UPDATE `routing` SET 
					killer='".$this->kclass->user['userid']."',
					killed='".TIMENOW."' 
				WHERE routingid='".$routing['procedureid']."'
			");
		}
		$this->kclass->messager(array(
				'title' => '删除工艺路线',
				'text' => '物料 <b>'.$routing['materialno'].'</b> 的工艺路线已成功被删除!',
				'url' => '/s.php?module=routing',
				'final' => 1,
				'sec' => 3
			)
		);
	}

	// 
	function remove(){
		if($this->kclass->input['routingid']){
			$condition='routingid='.$this->kclass->input['routingid'];
		}
		if($this->kclass->input['routingid']){
			$condition='routingid='.$this->kclass->input['routingid'];
		}
		$routing = $this->kclass->DB->queryFirst("
			SELECT `routing`.*,
				`material`.materialno
			FROM `routing` 
			LEFT JOIN `material` ON (`material`.materialid=`routing`.routingid)
			WHERE $condition
		");

$body = <<<EOF
<form action="/s.php?module=routing&action=kill" name="name" method="post">
<input type="hidden" name="module" value="routing">
<input type="hidden" name="action" value="kill">
<input type="hidden" name="routingid" value="{$this->kclass->input['routingid']}">
<input type="hidden" name="routingid" value="{$this->kclass->input['routingid']}">
<table>
<thead>
<tr>
	<th colspan="2">删除工艺路线: </th>
</tr>
</thead>
<tbody>
<tr class="even">
	<td colspan="2">你确定要删除物资: <b>{$routing['materialno']}</b> 的工艺路线吗?<br />(注意：此列项下包含的所有子类也会同时被删除！)</td>
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
		$this->kclass->page['title'] = ' - 删除工艺路线';
		$this->kclass->page['main'] .= $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 删除工艺路线', 'right' => $this->right, 'body'=>$body));
	}

	// 
	function doorder(){
		foreach($this->kclass->input['order'] as $k => $v){
			$this->kclass->DB->query("
				UPDATE `routing`
				SET `ordering`='".$v."'
				WHERE `routingid`='".$k."'
			");
		}
		foreach($this->kclass->input['cdorder'] as $ke => $ve){
			$this->kclass->DB->query("
				UPDATE `procedure`
				SET `ordering`='".$ve."'
				WHERE `procedureid`='".$ke."'
			");
		}
		
		$this->kclass->messager(array(
				'title' => '工艺路线排序',
				'text' => '工艺路线 重新排序 成功!',
				'url' => '/s.php?module=routing',
				'final' => 1,
				'sec' => 1
			)
		);
	}

	// 
	function mmlist(){
		$routings=$this->kclass->DB->query("
			SELECT r.* ,
			wc.title AS workcenter
			FROM `routing` AS r
			LEFT JOIN workcenter AS wc ON (wc.workcenterid=r.workcenterid)
			WHERE r.killed=0 
			ORDER BY r.ordering ASC, r.routingid ASC	
		");
		if($this->kclass->DB->numRows()){
			while($routing=$this->kclass->DB->fetchArray($routings)){
				$procedures=$this->kclass->DB->query("
					SELECT *
					FROM `procedure`  
					WHERE killed=0 AND routingid='".$routing['routingid']."' 
					ORDER BY ordering ASC 
				");
				if($this->kclass->DB->numRows()){
					$i=1;
					$procedureItemtr='';
					while($procedure=$this->kclass->DB->fetchArray($procedures)){
						$procedureItemtr.='<tr class="'.$this->kclass->rotateLine().' small">
							<td>'.date('Y-m-d',$procedure['created']).'</td>
							<td align="right"><input type="text" name="cdorder['.$procedure['procedureid'].']" size="1" value="'.$procedure['ordering'].'"></td>
							<td>&nbsp;&nbsp;&nbsp;'.$procedure['title'].'</td>
							<td></td>
							<td>'.$procedure['step'].'</td>
							<td>'.$procedure['remark'].'</td>
							<td>'.$this->kclass->iif($procedure['modified']==0,'',date('Y-m-d',$procedure['modified'])).'</td>
							<td><a href="/s.php?module=routing&action=remove&routingid='.$procedure['routingid'].'" class="small">删除</a></td></tr>';
						$i++;
					}
				}
				$material=$this->kclass->DB->queryFirst("SELECT materialid,title,materialno,standard FROM material WHERE materialid='".$routing['routingid']."' LIMIT 0,1");
				$itemtr.='<tr class="'.$this->kclass->rotateLine().' small">
					<td>'.date('Y-m-d',$routing['created']).'</td>
					<td><input type="text" name="order['.$routing['routingid'].']" size="1" value="'.$routing['ordering'].'"></td>
					<td class="bold">'.$routing['title'].'</td>
					<td><a href="/s.php?module=workcenter&action=view&workcenterid='.$routing['workcenterid'].'">'.$routing['workcenter'].'</a></td>
					<td></td>
					<td>'.$routing['remark'].'</td>
					<td>'.$this->kclass->iif($routing['modified']==0,'',date('Y-m-d',$routing['modified'])).'</td>
					<td><a href="/s.php?module=routing&action=remove&routingid='.$routing['routingid'].'" class="small">删除</a> <a href="/s.php?module=routing&action=update&routingid='.$routing['routingid'].'">编辑</td></tr>'.$procedureItemtr;
			}
		}

$body = <<<EOF
<form action="/s.php?module=routing&action=doorder" name="name" method="post">
<input type="hidden" name="module" value="routing">
<input type="hidden" name="action" value="doorder">
<table>
EOF;

if($itemtr){
$body .= <<<EOF
<thead>
<tr>
	<th width="70">建立日期</th>
	<th width="40">排序</th>
	<th width="220">工艺流程</th>
	<th width="70">工作中心</th>
	<th width="250">操作步骤</th>
	<th width="100">备注</th>
	<th width="60">修改日期</th>
	<th width="70">选项</th>
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
	<td>暂无工艺路线.</td>
</tr>
</form>
</table>
EOF;
}
		$this->kclass->page['onload'] .= 'dc.tabhover();';
		$this->kclass->page['title'] = '工艺路线 - 列表';
		$this->kclass->page['main'] .= $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 列表', 'right' => $this->right, 'body'=>$body));
	}

}
?>