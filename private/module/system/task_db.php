<?php
//
class task{
	var $kclass;
	var $inboundType=array(array('id'=>1,'title'=>'进仓'),array('id'=>2,'title'=>'良品退料(非BOM)'),array('id'=>3,'title'=>'订单入库'),array('id'=>5,'title'=>'良品退料(BOM)'),array('id'=>6,'title'=>'样品入库'),array('id'=>7,'title'=>'不良品退料'));
	var $outboundType=array(array('id'=>1,'title'=>'领料(BOM)'),array('id'=>2,'title'=>'领料(非BOM)'),array('id'=>6,'title'=>'补料'),array('id'=>7,'title'=>'补料(BOM)'));
	//
	function autorun(){
		$this->baseurl='<a href="/s.php?">首页</a>';
		switch($this->kclass->input['action']){
			case 'list':
				return $this->mmlist();
				break;
			case 'view':
				return $this->view();
				break;
			case 'find':
				return $this->find();
				break;
			case 'add':
				return $this->add();
				break;
			case 'insert':
				return $this->insert();
				break;
			case 'view':
				return $this->view();
				break;
			case 'update':
				return $this->update();
				break;
			case 'doupdate':
				return $this->doupdate();
				break;
			case 'appointComplete':
				return $this->appointComplete();
				break;
			case 'doappointComplete':
				return $this->doappointComplete();
				break;
			case 'viewInboundRecord':
				return $this->viewInboundRecord();
				break;
			case 'viewOutboundRecord':
				return $this->viewOutboundRecord();
				break;
			case 'addOutboundRecord':
				return $this->addOutboundRecord();
				break;
			case 'insertOutboundRecord':
				return $this->insertOutboundRecord();
				break;
			case 'addInboundRecord':
				return $this->addInboundRecord();
				break;
			case 'insertInboundRecord':
				return $this->insertInboundRecord();
				break;
			case 'addInbound':
				return $this->addInbound();
				break;
			case 'addOutbound':
				return $this->addOutbound();
				break;
			case 'addWorkcenter':
				return $this->addWorkcenter();
				break;
			case 'insertWorkcenter':
				return $this->insertWorkcenter();
				break;
			case 'addRouting':
				return $this->addRouting();
				break;
			case 'insertRouting':
				return $this->insertRouting();
				break;
			case 'updateRouting':
				return $this->updateRouting();
				break;
			case 'doupdateRouting':
				return $this->doupdateRouting();
				break;
			case 'remove':
				return $this->remove();
				break;
			case 'kill':
				return $this->kill();
				break;
			case 'revival':
				return $this->revival();
				break;
			case 'restore':
				return $this->restore();
				break;
			case 'lock':
				return $this->lock();
				break;
			case 'dolock':
				return $this->dolock();
				break;
			case 'verify':
				return $this->verify();
				break;
			case 'doverify':
				return $this->doverify();
				break;
			default:
				return $this->mmlist();

		}
	}
	// main page,shows all the task info list
	function mmlist(){
		if($this->kclass->input['show']=='all'){
			$condition='`task`.killed>=0';
		}elseif($this->kclass->input['show']=='unlock'){
			$condition='`task`.statusid=1 AND `task`.killed=0';
		}elseif($this->kclass->input['show']=='locked'){
			$condition='`task`.statusid=2 AND `task`.killed=0';
		}elseif($this->kclass->input['show']=='inProduction'){
			$condition='`task`.statusid=3 AND `task`.killed=0';
		}elseif($this->kclass->input['show']=='Done'){
			$condition='`task`.statusid=4 AND `task`.killed=0';
		}else{
			$condition='`task`.killed=0';
		}
		$find=$this->kclass->findTask(array('created'=>$this->kclass->input['created'],'quantity'=>$this->kclass->input['quantity'],'finishquantity'=>$this->kclass->input['finishquantity'],'workdate'=>$this->kclass->input['workdate'],'finishdate'=>$this->kclass->input['finishdate'],'workcenterid'=>$this->kclass->input['workcenterid'],'materialno'=>$this->kclass->input['materialno'],'materialtitle'=>$this->kclass->input['materialtitle'],'taskno'=>$this->kclass->input['taskno']));
		//查找
		if($this->kclass->input['taskno']!='')
		$body.='　任务单编号: "<span class="red">'.$this->kclass->input['taskno'].'</span>"　';
		if($this->kclass->input['quantity']!='')
		$body.='　净需求数量: "<span class="red">'.$this->kclass->input['quantity'].'</span>"　';
		if($this->kclass->input['finishquantity']!='')
		$body.='　已完工数量: "<span class="red">'.$this->kclass->input['finishquantity'].'</span>"　';
		if($this->kclass->input['workdate']!='')
		$body.='　投产日期: "<span class="red">'.$this->kclass->input['workdate'].'</span>"　';
		if($this->kclass->input['finishdate']!='')
		$body.='　完工日期: "<span class="red">'.$this->kclass->input['finishdate'].'</span>"　';
		if($this->kclass->input['workcenterid']!=''){
		$wc=$this->kclass->DB->queryFirst("SELECT title FROM workcenter WHERE workcenterid='".$this->kclass->input['workcenterid']."'");
		$body.='　工作中心: "<span class="red">'.$wc['title'].'</span>"　';
		}
		if($this->kclass->input['materialno']!='')
		$body.='　物料编号: "<span class="red">'.$this->kclass->input['materialno'].'</span>"　';
		if($this->kclass->input['materialtitle']!='')
		$body.='　物料名称: "<span class="red">'.$this->kclass->input['materialtitle'].'</span>"　';
		if($this->kclass->input['created']!='')
		$body.='　任务时间: "<span class="red">'.$this->kclass->input['created'].'</span>"　';
		if($body)$body='<div class="toptip"><span class="bold">在任务单中查找：</span>'.$body.'</div>';
		//
		if($this->kclass->input['orderby']!='')$query['orderby']=$this->kclass->input['orderby'];
		if($this->kclass->input['direction']!='')$query['direction']=$this->kclass->input['direction'];
		if($this->kclass->input['show']!='')$query['show']=$this->kclass->input['show'];
		if($this->kclass->input['layout']!='')$query['layout']=$this->kclass->input['layout'];
		$orderby=$this->kclass->orderby(array('module'=>'task','direction'=>'desc','orderby'=>'created', 'default'=>'created', 'serial'=>array( array('title'=>'编号', 'field'=>'taskno','word'=>'no'), array('title'=>'修改时间', 'field'=>'modified'), array('title'=>'建立时间', 'field'=>'created')),'appendUrl'=>$query));
		if(is_array($query) AND count($query)>0)$queryPart='&'.http_build_query($query);
		//分页
		$perpage=20;
		$taskList=$this->kclass->listTask(array('prepage'=>$perpage,'find'=>$find,'orderby'=>$query['orderby'],'direction'=>$query['direction'],'show'=>$query['show'],'layout'=>$query['layout']));
		if($this->kclass->input['layout']=='grid'){
				$body.='<ul id="tbody" class="mmlist clear">';
			}else{
				$body.='<table class="hundred mytable tablesorter"><thead><th width="20">ID</th><th width="100">编号</th><th width="50">任务员</th><th width="70">任务时间</th><th width="70">备注</th><th width="70">状态</th><th width="70">录入类型</th><th width="80">选项</th></thead><tbody id="tbody">';
			}
		$body.=$taskList['body'];
		if($taskList['counter']>0){
			if($taskList['count']>$taskList['counter']){
				$body.='<div class="page" id="taskpage"><a href="javascript:dc.tableItem.page('.$perpage.')">点击查看更多</a></div>';
			}
		}
		$body.='<div class="clear"><span class="gray small left">(共<span class="darkred">'.$taskList['count'].'</span>条记录)</span></div>';
		//
		$workcenter=$this->kclass->chooserWorkcenter(array('name'=>'workcenterid','width'=>280,'hasBlank'=>1,'topname'=>'所有'));
		$body.='<div class="search_menu" id="search_menu" style="display:none;">
<form action="/s.php?module=task&action=list" method="get">
<input type="hidden" name="module" value="task">
<input type="hidden" name="action" value="list">
<table style="width:450px">
<thead><tr><th colspan=2>根据条件查找任务单<span class=right>[<a href="#" class="switchsearch">关闭</a>]</span></th></tr></thead>
<tbody>
<tr class="even">
<td width="100">任务单编号：</td><td><input type="text" name="taskno"></td>
</tr>
<tr class="odd">
<td width="100">净需求量：</td><td><input type="text" name="quantity"></td>
</tr>
<tr class="even">
<td width="100">已完工量：</td><td><input type="text" name="finishquantity"></td>
</tr>
<tr class="odd">
<td width="100">投产日期：</td><td><input type="text" name="workdate" id="workdate"></td>
</tr>
<tr class="even">
<td width="100">完工日期：</td><td><input type="text" name="finishdate" id="finishdate"></td>
</tr>
<tr class="odd">
<td width="100">工作中心：</td><td>'.$workcenter.'</td>
</tr>
<tr class="even">
<td width="100">物料编号：</td><td><input type="text" name="materialno" style="width:300px"></td>
</tr>
<tr class="odd">
<td width="100">物料名称：</td><td><input type="text" name="materialtitle" style="width:300px"></td>
</tr>
<tr class="even">
<td width="100">任务时间：</td><td><input type="text" name="created" id="created"></td>
</tr>
<tr class="odd">
<td class="small gray" colspan="9">
①任务单可以通过输入“任务单编号”，“日期”和“物料编号”进行查找<br>
②请保证输入的值无误</td>
</tr>
<tr class="even" nohover>
<td class="center" colspan=2><input type="submit" id="submitButton" value="  提交  "><input type="reset" value="  复位  "/></td>
</tr>
</tbody>
</table>
</form>
</div>';
	$id.=',#created';
	$id.=',#workdate';
	$id.=',#finishdate';
		//
		if($this->kclass->input['layout']=='grid'){
			$layoutLink='<a href="/s.php?module=task&action=list&layout=list'.str_replace('&layout=grid', '', $queryPart).'">列表</a> 格子';
		}else{
			$layoutLink='列表 <a href="/s.php?module=task&action=list&layout=grid'.str_replace('&layout=list', '', $queryPart).'">格子</a>';
		}
		$showLink='<a href="/s.php?module=task&action=list">默认</a> <a href="/s.php?module=task&action=list&show=all">所有<span class="small gray">(包括删除)</span></a> <a href="/s.php?module=task&action=list&show=unlock">未锁定</a>  <a href="/s.php?module=task&action=list&show=locked">已锁定</a>  <a href="/s.php?module=task&action=list&show=inProduction">生产中</a>  <a href="/s.php?module=task&action=list&show=Done">生产完成</a>';
		$this->kclass->page['title'].='生产任务单列表';
		$this->kclass->page['onload'].='dc.listhover();dc.switchsearch();$(\'.mytable\').fixedtableheader();$(\'#date,'.$id.'\').datepicker();';
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - 生产任务单列表','right' => '<a href="#" class="switchsearch">查找</a>　 | 　<span class="small">排序：</span>'.$orderby['link'].'|　<span class="small">显示方式：</span>'.$showLink.'　|　<span class="small">布局：</span>'.$layoutLink.'　|　<a href="/s.php?module=task&action=add">新建</a>','body'=>$body));
	}
	//find页面需要完善，可作为高级搜索，设置更多的查询条件 --gaowenfei 2013/11/25 8:45
	function find(){
		if($this->kclass->input['requestMethod']!='post'){
			$workcenter=$this->kclass->chooserWorkcenter(array('name'=>'workcenterid','width'=>280,'hasBlank'=>1,'topname'=>'所有'));
			$body=<<<EOF
<form action="/s.php?module=task&action=list" method="get">
<input type="hidden" name="module" value="task">
<input type="hidden" name="action" value="list">
<table style="width:450px">
<thead><tr><th colspan=2>根据条件查找任务单</th></tr></thead>
<tbody>
<tr class="even">
<td width="100">任务单编号：</td><td><input type="text" name="taskno"></td>
</tr>
<tr class="odd">
<td width="100">净需求量：</td><td><input type="text" name="quantity"></td>
</tr>
<tr class="even">
<td width="100">已完工量：</td><td><input type="text" name="finishquantity"></td>
</tr>
<tr class="odd">
<td width="100">投产日期：</td><td><input type="text" name="workdate" id="workdate"></td>
</tr>
<tr class="even">
<td width="100">完工日期：</td><td><input type="text" name="finishdate" id="finishdate"></td>
</tr>
<tr class="odd">
<td width="100">工作中心：</td><td>{$workcenter}</td>
</tr>
<tr class="even">
<td width="100">物料编号：</td><td><input type="text" name="materialno" style="width:300px"></td>
</tr>
<tr class="odd">
<td width="100">物料名称：</td><td><input type="text" name="materialtitle" style="width:300px"></td>
</tr>
<tr class="even">
<td width="100">任务时间：</td><td><input type="text" name="created" id="created"></td>
</tr>
<tr class="odd">
<td class="small gray" colspan="9">
①任务单可以通过输入“任务单编号”，“日期”和“物料编号”进行查找<br>
②请保证输入的值无误</td>
</tr>
<tr class="even" nohover>
<td class="center" colspan=2><input type="submit" id="submitButton" value="  提交  "><input type="reset" value="  复位  "/></td>
</tr>
</tbody>
</table>
</form>
EOF;
			$id.=',#created';
			$id.=',#workdate';
			$id.=',#finishdate';

			$this->kclass->page['title'].='查找生产任务单';
			$this->kclass->page['onload'].='dc.listhover();$(\'#date,'.$id.'\').datepicker();';
			$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - 查找生产任务单','right' => '<a href="/s.php?module=task">查看列表</a>','body'=>$body));
		}else{
			if($this->kclass->input['taskno']!=''){
				$taskno=trim($this->kclass->input['taskno']);
				$task=$this->kclass->DB->queryFirst("SELECT taskid FROM task WHERE taskno='".$taskno."' LIMIT 0,1");
			}else{
				if($this->kclass->input['dateline']!=''){
					$sd=explode('-',$this->kclass->input['dateline']);
					$start=mktime(0,0,0,$sd[1],$sd[2],$sd[0]);
					$end=$start+86400;
					$condition.=' AND (p.created>'.$start.' OR p.created<'.$end.')';
				}
				if($this->kclass->input['materialno']!=''){
					$materialno=trim($this->kclass->input['materialno']);
					$condition=' AND m.materialno="'.$materialno.'"';
				}
				if($this->kclass->input['title']!=''){
					$title=trim($this->kclass->input['title']);
					$condition.=' AND (m.title="'.$this->kclass->input['title'].'" OR p.title="'.$title.'" OR pi.title="'.$title.'")';
				}
				$task=$this->kclass->DB->queryFirst("
					SELECT DISTINCT p.taskid
					FROM taskitem AS p
					LEFT JOIN material AS m ON (m.materialid=p.materialid)
					LEFT JOIN preinbounditem AS pi ON (pi.itemid=p.applyItemid)
					WHERE p.killed=0 ".$condition."
					ORDER BY p.created DESC
					LIMIT 0,1
				");
			}
			$this->kclass->boinkIt('/s.php?module=task&action=view&taskid='.$task['taskid']);
		}
	}
	//
	function view(){
		$task=$this->kclass->DB->queryFirst("
			SELECT t.*,
				s.title AS status,
				ul.username AS locker,
				u.username AS creator,
				us.username AS modifier
			FROM task AS t
			LEFT JOIN mftstatus AS s ON (s.statusid=t.statusid)
			LEFT JOIN user AS ul ON (ul.userid=t.locker)
			LEFT JOIN user AS u ON (u.userid=t.creator)
			LEFT JOIN user AS us ON (us.userid=t.modifier)
			WHERE t.taskid='".$this->kclass->input['taskid']."'
			LIMIT 0,1
		");
		if(!$task){
			$e='<li>数据错误，很抱歉</li>';
		}
		if($e){
			$this->kclass->messager(array(
				'title' => '查看生产任务单',
				'text' => '您在查看生产任务单的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$addtype=$this->kclass->iif($task['addtype']==1,'手工方式','生产计划');
		$task['created']=date('Y-m-d H:i:s',$task['created']);
		if($task['modified']!=0)$modify='，由 '.$task['modifier'].' 于 '.date('Y-m-d H:i:s',$task['modified']).' 修改';
		$statusRemark='<td></td><td></td>';
		if($task['statusid']==2){
			$pstatus='，由 '.$task['locker'].' 于 '.date('Y-m-d H:i:s',$task['locked']).' 锁定';
			$statusRemark='<td>审核备注：</td><td>'.$task['status'].'</td>';
		}
		if($task['statusid']==1){
			$changeStatus='<a href="/s.php?module=task&action=remove&taskid='.$task['taskid'].'&rt=view">删除</a>　|　<a href="/s.php?module=task&action=lock&taskid='.$task['taskid'].'">锁定</a>';
		}elseif($task['statusid']!=1){
			$changeStatus='';
		}
		$updateItem='<span class="right"><a href="/s.php?module=task&action=update&taskid='.$task['taskid'].'&step=item">修改任务单明细</a></span>';
		//任务的物资
		$items=$this->kclass->DB->query("
			SELECT ti.*,
				rt.requirementno,
				r.title AS routing,
				mv.title AS version
			FROM taskitem AS ti
			LEFT JOIN requirement AS rt ON (rt.requirementid=ti.requirementid)
			LEFT JOIN routing AS r ON (r.routingid=ti.routingid)
			LEFT JOIN `materialversion` AS mv ON (mv.versionid=ti.versionid)
			WHERE ti.killed=0 AND ti.taskid='".$task['taskid']."'
			ORDER BY ti.ordering ASC
		");
		if($this->kclass->DB->numRows()){
			$i=1;$j=1;
			while($item=$this->kclass->DB->fetchArray($items)){
				$wc=$this->kclass->DB->queryFirst("
					SELECT wci.workcenterid,wc.title AS workcenter,wc.burthen
					FROM workcenteritem AS wci
					LEFT JOIN workcenter AS wc ON (wc.workcenterid=wci.workcenterid)
					WHERE wci.killed=0 AND wc.workcenterid='".$item['workcenterid']."' AND wci.versionid='".$item['versionid']."'
				");
				if($wc['workcenterid']=="")$haswc=1;
				if($item['routingid']==0)$hasrouting=1;
				$materialInfo=$this->kclass->getMaterial(array('materialid'=>$item['materialid'],'itemid'=>$item['materialitemid']));
				$item['workdate']=date('Y-m-d',$item['workdate']);
				$item['finishdate']=date('Y-m-d',$item['finishdate']);
				$requirementno='<a href="/s.php?module=requirement&action=view&requirementid='.$item['requirementid'].'">'.$item['requirementno'].'</a>';
				$workcenter='<a href="/s.php?module=workcenter&action=view&workcenterid='.$wc['workcenterid'].'">'.$wc['workcenter'].'</a>';
				$routing='<a href="/s.php?module=routing&action=list">'.$item['routing'].'</a>'.$this->kclass->iif($task['statusid']==1,'<a href="/s.php?module=task&action=updateRouting&itemid='.$item['itemid'].'&workcenterid='.$item['workcenterid'].'"><span class="darkred normal right">  (改)</span></a>','');
				$itemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').' '.$this->kclass->iif($wc['workcenterid']=="" OR $item['routingid']==0,'bgYellow','').' small"><td>'.$i.'</td>
					<td>'.$this->kclass->iif($item['requirementno']=="",'无',$requirementno).'</td>
					<td>'.$this->kclass->iif($wc['workcenterid']=="",'<a href="/s.php?module=task&action=addWorkcenter&itemid='.$item['itemid'].'"><span class="red normal">添加工作中心</span></a>',$workcenter.'　<a href="/s.php?module=task&action=addWorkcenter&itemid='.$item['itemid'].'">切换工作中心</a>').'</td>
					<td>'.$this->kclass->iif($item['routingid']==0,'<a href="/s.php?module=task&action=addRouting&itemid='.$item['itemid'].'&workcenterid='.$item['workcenterid'].'"><span class="red normal">选择工艺流程</span></a>',$routing).'</td>
					<td><a href="'.$materialInfo['url'].'">'.$materialInfo['no'].'</a>　'.$materialInfo['material'].'　'.$materialInfo['standard'].' </td>
					<td>'.$item['version'].'</td>
					<td align="right"><span class="bold">'.$item['quantity'].'</span> '.$item['unit'].'</td>
					<td align="right"><span class="bold">'.$item['finishquantity'].'</span> '.$item['unit'].'</td>
					<td align="right"><span class="bold darkred">'.$this->kclass->iif($item['ifChooser']>0,'是','否').'</span></td>
					<td align="right"><span class="bold darkred">'.$item['workdate'].'</span></td>
					<td align="right"><span class="bold darkred">'.$item['finishdate'].'</span></td>
					<td align="right">'.$item['dispatchnumber'].' 人</td>
					<td align="right">'.$item['workhours'].' 时</td>
					<td>'.$item['remark'].'</td>
					<td>'.$this->kclass->iif($item['statusid']!=4,'<a href="/s.php?module=task&action=appointComplete&taskitemid='.$item['itemid'].'">指定完工</a>','').'</td>
				</tr>';
				$i++;
				//生产变更记录
				$logs=$this->kclass->DB->query("
					SELECT *
					FROM producemodifylog
					WHERE module='task' AND mid='".$item['itemid']."'
				");
				if($this->kclass->DB->numRows()){
					while($log=$this->kclass->DB->fetchArray($logs)){
						$logtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').' small">
						<td>'.$j.'</td>
						<td><a href="'.$materialInfo['url'].'">'.$materialInfo['no'].'</a>'.$materialInfo['material'].' '.$materialInfo['standard'].'</td>
						<td>'.$item['version'].'</td>
						<td align="right"><span class="bold">'.$log['oldquantity'].'</span> '.$materialInfo['unit'].'</td>
						<td align="right"><span class="bold">'.$log['newquantity'].'</span> '.$materialInfo['unit'].'</td>
						<td>'.date('Y-m-d H:i:s',$log['created']).'</td>
						<td>'.$log['remark'].'</td>
						</tr>';
						$j++;
					}
				}
				//领料bom
				$bomtr.='<tr><td colspan="16" class="bold"><a href="'.$materialInfo['url'].'">'.$materialInfo['no'].'</a>'.$materialInfo['material'].' '.$materialInfo['standard'].'　 (版本：'.$item['version'].')</td></tr>';
				$child=$this->kclass->bomChildList(array('parentid'=>$item['materialid'],'materialitemid'=>$item['materialitemid'],'versionid'=>$item['versionid'],'quantity'=>$item['quantity'],'loopNum'=>1));
				if(!empty($child['bom'])){
					$k=1;
					foreach($child['bom'] as $val){
						$version=$this->kclass->DB->queryFirst("SELECT mv.qualified,mv.planInbound,mv.planOutbound,m.min FROM materialversion AS mv LEFT JOIN material AS m ON (m.materialid=mv.materialid) WHERE mv.killed=0 AND mv.versionid='".$val['versionid']."'");
						$usable=$version['qualified']+$version['planInbound']-$version['planOutbound'];
						$bomtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').' small '.$this->kclass->iif($val['ifGeneral']==0,$this->kclass->iif($val['quantity']>$version['qualified'],'bgYellow',''),'').'">
						<td>'.$k.'</td>
						<td><a href="'.$val['url'].'">'.$val['no'].'</a>　'.$val['title'].'　'.$val['standard'].'</td>
						<td>'.$val['version'].'</td>
						<td>'.$val['origin'].'</td>
						<td>'.$this->kclass->iif($val['ifGeneral']==0,$val['quantity'],'通用辅料').'</td>
						<td>'.$version['qualified'].'</td>
						<td>'.$version['planInbound'].'</td>
						<td>'.$version['planOutbound'].'</td>
						<td>'.$version['min'].'</td>
						<td>'.$usable.'</td>
						</tr>';
						$k++;
					}
				}else{
					$bomtr.='<tr><td colspan="16" class="darkred">物资 ['.$title.'] BOM(子) 不存在！</td></tr>';
				}
				unset($child);
			}
		}else{
			$itemtr.='<tr><td colspan="16" class="darkred center">暂无相关记录！</td></tr>';
	}
	if($logtr){
		$logth='<table class="hundred"><thead><tr><th colspan="15">生产变更记录</th></tr></thead><tbody><tr class=""><td width="30">ID</td><td width="600">物资</td><td>版本</td><td>原净需求量</td><td>现净需求量</td><td>变更时间</td><td>变更原因</td></tr>';
		$logtr=$logth.$logtr.'</tbody></table>';
	}
	$taskdispatchlist = $this->kclass->releatedDispatchlist(array('taskid'=>$this->kclass->input['taskid'],'perpage'=>4));//工作中心相关生产派工单
	$moredispatchlist.='<div id="dispatchitem"></div>';
	$pcount=$taskdispatchlist['counter'];
	$perpage=4;
	if($taskdispatchlist['num']>0){
		if($pcount>$taskdispatchlist['num']){
			$moredispatchlist.='<div class="page" id="dispatchitempage"><a href="javascript:dc.listItem.page('.$perpage.',\'dispatchitem\',\'task\',\''.$this->kclass->input['taskid'].'\')">点击查看更多</a></div>';
		}
	}
	//
	$taskinboundlist = $this->kclass->relatedInbound(array('taskid'=>$this->kclass->input['taskid']));//工作中心相关入库单
	$taskoutboundlist = $this->kclass->relatedOutbound(array('taskid'=>$this->kclass->input['taskid']));//工作中心相关出库单
	$statusid=4;
	if($hasrouting==1)$statusid=3;
	if($haswc==1)$statusid=2;
	if($task['statusid']>1)$statusid=5;		
	$flow=$this->kclass->listFlow(array('module'=>'task','statusid'=>$statusid));
$body=<<<EOF
<div class="title"><span class="right small gray">由 {$task['creator']} 于 {$task['created']} 建立 {$modify}{$pstatus}。</span>{$task['taskno']}</div>
<dl id="task" class="tabs" style="display: block;">
<dt tabid="0">基本信息</dt>
<dt title="与此物资生产任务相关的生产派工申请"{$taskdispatchlist['off']}>生产派工单{$taskdispatchlist['count']}</dt>
<dt title="与此物资生产任务相关的出库单"{$taskoutboundlist['off']}>出库单{$taskoutboundlist['count']}</dt>
<dt title="与此物资生产任务相关的入库单"{$taskinboundlist['off']}>入库单{$taskinboundlist['count']}</dt>
<dd>
<div><span class="right">{$flow}</span></div>
<table class="hundred">
<thead><tr><th colspan="4">生产任务单</th></tr></thead>
<tbody>
<tr class="odd">
<td width="100">编号：</td><td width="475">{$task['taskno']}　（{$task['status']}）</td>
<td width="100">录入类型：</td><td>{$addtype}</td>
</tr>
<tr class="even">
<td width="100">任务员：</td><td>{$task['tasker']}</td>
<td>任务时间：</td><td>{$task['created']}</td>
</tr>
<tr class="odd">
<td>备注：</td><td>{$task['remark']}</td>
<td></td><td></td>
</tr>
</tbody>
</table>
<table class="hundred">
<thead><tr><th colspan="15">生产任务明细{$updateItem}</th></tr></thead>
<tbody>
<tr class="even center"><td width="15">ID</td><td width="60">计划单</td><td width="140">工作中心</td><td width="280">工艺流程</td><td width="480">物资</td><td width="60">版本</td><td width="90">净需求量</td><td width="100">已完工量</td><td width="90">申请领料</td><td width="90">投产日期</td><td width="90">完工日期</td><td width="100">派工人数</td><td width="100">工作时数</td><td width="120">备注</td><td width="100">操作</td></tr>
{$itemtr}
</tbody>
</table>
{$logtr}
<table class="hundred">
<thead><tr><th colspan="15">物料需求明细</th></tr></thead>
<tbody><tr class=""><td width="30">ID</td><td>物资</td><td>版本</td><td>来源</td><td>需求数量</td><td>良品库存</td><td>待入库存</td><td>待出库存</td><td>安全库存</td><td>计划可用</td></tr>
{$bomtr}
<tr class="even">
<td class="small gray" colspan="15">
注意事项：<br>
①如果所列表格行为黄色,说明物资没有关联到工作中心,请点击选择添加关联到工作中心；<br>
②如果不从“生产任务单中”选择物资，而是生产新物资，那么就填写“物资编号”、“物资名称”、“物资规格”，或者在物资列表中选择；<br>
③“生产任务单及其物资”中 灰色的物资表示已经被选中过，不可以继续选择；<br>
④时间的格式为2012-02-06，中间用半角短横杠隔开；<br>
⑤从库中物资选择时，“单位”不需要选，但是如果生产物资不存在库中时，就必须要选择“单位”
</td>
</tr>
</tbody>
</table>
</dd>
<dd>{$taskdispatchlist['panel']}{$moredispatchlist}</dd>
<dd>{$taskoutboundlist['panel']}</dd>
<dd>{$taskinboundlist['panel']}</dd>
EOF;
		$this->kclass->page['title'].='生产任务单';
		$this->kclass->page['onload'].='dc.tabhover();dc.tips();dc.tabs({\'id\':\'task\'});dc.show()';
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - <a href="/s.php?module=task&action=list">生产任务单列表</a> - 生产任务单','right' => '<span class = "small">操作：</span> '.$this->kclass->iif($task['killed']==0,'<a href="/s.php?module=task&action=add">新建</a>　|　<a href="/s.php?module=task&action=update&taskid='.$task['taskid'].'">修改</a>　'.$changeStatus,'<a href="/s.php?module=task&action=restore&taskid='.$this->kclass->input['taskid'].'&rt=view">恢复</a>'),'body'=>$body));
	}
	//新增任务单 生成方式必须选择按需求计划 按库存新增已经作废 后续开发可将按库存修改为返工需求 返工模块需重新开发 --gaowenfei 2013/11/25 9:39
	function add(){
		if($this->kclass->input['step']==''){
			if($this->kclass->input['addtype']==''){
	$body=<<<EOF
<form action="/s.php?module=task&action=add" method="get">
<input type="hidden" name="module" value="task">
<input type="hidden" name="action" value="add">
<table style="width:450px;">
<thead><tr><th colspan=2>新建任务单：选择生成方式</th></tr></thead>
<tbody>
<tr><td width="90">选择生成方式</td><td><input type="radio" name="addtype" value="plan" checked>生产计划生成<br><br><input type="radio" name="addtype" value="hand">手工方式录入</td></tr>
<tr class="center"><td colspan=2><input type="submit" value=" 提交 "><input type="reset" value=" 重置 "></td></tr>
</tbody>
</table>
EOF;
		}elseif($this->kclass->input['addtype']=='plan'){
			$requirements=$this->kclass->DB->query("
				SELECT r.*,
				u.username AS creator
				FROM `requirement` AS r
				LEFT JOIN user AS u ON (u.userid=r.creator)
				WHERE r.killed=0 AND r.statusid=2
			");
			if($this->kclass->DB->numRows()){
				$i=1;
				while($requirement=$this->kclass->DB->fetchArray($requirements)){
					//申请制造的物资
					$items=$this->kclass->DB->query("
						SELECT mp.*,
							mv.title AS version
						FROM `mrp` AS mp
						LEFT JOIN `materialversion` AS mv ON (mv.versionid=mp.versionid)
						LEFT JOIN `material` AS m ON (m.materialid=mp.materialid)
						WHERE mp.killed=0 AND m.originid=1 AND mp.ifChooser=0 AND mp.requirementid='".$requirement['requirementid']."'
						ORDER BY mp.ordering ASC
					");
					if($this->kclass->DB->numRows()){
						$k=1;
						$requirementno='<a href="/s.php?module=requirement&action=view&requirementid='.$requirement['requirementid'].'">'.$requirement['requirementno'].'</a>';
						$itemtr.='<tbody><tr class="bold "><td colspan=9>需求计划单编号：<span class="middle bold">'.$requirementno.'</span>  　计划员：'.$requirement['creator'].'　计划时间：'.date('Y-m-d',$requirement['created']).'</td></tr><tr class="center even" nohover><td width="30">ID</td><td>物资编号</td><td>物资名称</td><td>物资规格</td><td>版本</td><td>需求数量</td><td>需求日期</td><td>备注</td><td>选</td></tr>';
						while($item=$this->kclass->DB->fetchArray($items)){
							$material=$this->kclass->getMaterial(array('materialid'=>$item['materialid'],'itemid'=>$item['materialitemid']));
							$itemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').'">
								<td><input type="hidden" name="itemId['.$i.']" value="'.$item['mrpid'].'" />'.$k.'</td>
								<td><a href="'.$material['url'].'">'.$material['no'].'</a></td>
								<td>'.$material['material'].'</td>
								<td>'.$material['standard'].'</td>
								<td>'.$item['version'].'</td>
								<td>'.$item['netQuantity'].'</td><td>'.date('Y-m-d',$item['requiredate']).'</td><td>'.$item['remark'].'</td>
								<td><input type="checkbox" name="itemSelect['.$i.']" value="'.$item['mrpid'].'"/></td>
							</tr>';
							$k++;$i++;
						}
					}
				}
				$itemtr.='</tbody>';
			}
			if(!$itemtr)$itemtr.='<tr class="darkred center"><td colspan="10">　暂无可用的物料需求记录！　</td></tr>';
			$body=<<<EOF
<form action="/s.php?module=task&action=add" method="post">
<input type="hidden" name="module" value="task" />
<input type="hidden" name="action" value="add" />
<input type="hidden" name="addtype" value="plan" />
<input type="hidden" name="step" value="2" />
<table><thead><tr><th colspan=9>选择计划单物料需求明细</th></tr></thead>
{$itemtr}
<tr class="center">
<td colspan=9><input type="submit" value="提交"><input type="reset" value="重置"></td>
</tr>
</tbody></table>
</form>
EOF;
		}elseif($this->kclass->input['addtype']=='hand'){//hand方式已作废
			$start=strtotime(date('Y-m-d',TIMENOW));
			$end=strtotime(date('Y-m-d',$start))+86400;
			$counter=$this->kclass->DB->queryFirst("SELECT COUNT(*) AS count FROM task WHERE created>='".$start."' AND created<='".$end."'");
			$taskno=$this->kclass->id(array('task'=>TIMENOW,'number'=>$counter['count']));
				for($i = 1; $i <= 6; $i++){
					$material=$this->kclass->chooserMaterial(array('hName'=>'itemMaterialid['.$i.']','hId'=>'itemMaterialid'.$i,'name'=>'itemMaterial['.$i.']','id'=>'itemMaterial'.$i,'width'=>400,'line'=>$i,'showVersion'=>1));
					$itemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').'">
						<input type="hidden" name="itemId['.$i.']" value="'.$i.'">
						<td>'.$i.'</td>
						<td>'.$material.'</td>
						<td><select name="itemVersionid['.$i.']" id="itemVersionid'.$i.'" style="width:80px;"></select></td>
						<td><input type="text" id="quantity'.$i.'" name="itemQuantity['.$i.']" size="15" /></td>
						<td><input type="text" id="workdate'.$i.'" name="itemWorkdate['.$i.']" size="15" /></td>
						<td><input type="text" id="finishdate'.$i.'" name="itemFinishdate['.$i.']" size="15"/></td>
						<td><input type="text" id="dispatchnumber'.$i.'" name="itemDispatchnumber['.$i.']" size="10" /> 人</td>
						<td><input type="text" id="workhours'.$i.'" name="itemWorkhours['.$i.']" size="10" /> 时</td>
						<td><input type="text" id="remark'.$i.'" name="itemRemark['.$i.']" size="25" /></td>
					</tr>';
					if($i<=6){
					$id.='#workdate'.$i.',';
					$id.='#finishdate'.$i.',';
				}else{
					$id.='#workdate'.$i;
					$id.='#finishdate'.$i;
				}
			}
			$flow=$this->kclass->listFlow(array('module'=>'task','statusid'=>'1'));
			$body=<<<EOF
<form action="/s.php?module=task&action=insert" name="task" method="post">
<input type="hidden" name="module" value="task" />
<input type="hidden" name="action" value="insert" />
<input type="hidden" name="addtype" value="hand" />
<input type="hidden" name="taskno" value="{$taskno}" />
<div><span class="right">{$flow}</span></div>
<table class="hundred">
<thead><tr><th colspan="6">新建生产任务单</th></tr></thead>
<tbody>
<tr class="odd">
<td width="120">任务单编号：<span class="red bold">*</span></td><td width="450"><span class="middle bold darkred">{$taskno}</span> <span class="gray small">此为预估编号，保存后才可确定</span></td>
<td width="120">任 务 员：<span class="red bold">*</span></td><td width="450"><input type="text" name="tasker" value="{$this->kclass->user['realname']}"></td>
<td width="120">备　　注：</td><td width="450"><textarea type="text" name="remark" style="width:250px;height:100px"></textarea>
</tr>
</tbody>
</table>
<table class="hundred">
<thead><tr><th colspan="12">工作中心任务明细</th></tr></thead>
<tbody>
<tr class="even center"><td width="15">ID</td><td>选择物资<span class="red bold">*</span></td><td>版本 <span class="bold red">*</span></td><td width="150">净需求数量 <span class="red bold">*</span></td><td width="120">投产日期<span class="red bold">*</span></td><td width="120">完工日期<span class="red bold">*</span></td><td width="100">派工人数<span class="red bold">*</span></td><td width="100">工作时数<span class="red bold">*</span></td><td width="200">备注</td></tr>
</tbody><tbody class="small">
{$itemtr}
<tr class="even">
<td class="small gray" colspan="12">
注意事项：<br>
①如果所列表格不够，那么在提交保存后再点击“修改”，来添加所要生产的物资；<br>
②如果不从“生产任务单中”选择物资，而是生产新物资，那么就填写“物资编号”、“物资名称”、“物资规格”，或者在物资列表中选择；<br>
③“生产任务单及其物资”中 灰色的物资表示已经被选中过，不可以继续选择；<br>
④时间的格式为2012-02-06，中间用半角短横杠隔开；<br>
⑤从库中物资选择时，“单位”不需要选，但是如果生产物资不存在库中时，就必须要选择“单位”
</td>
</tr>
</tbody>
</table>
<table class="tlist tsuite" align="center" width="100%">
<tbody>
<tr class="odd small" nohover>
<td class="center"><input type="submit" id="submitButton" value="  提交  " accesskey="s"><input type="reset" value="  复位  "/></td>
</tr>
</tbody>
</table>
</form>
EOF;
		}
		$this->kclass->tbline+=4;
		$this->kclass->page['title'].='选择生产任务单明细';
		$this->kclass->page['onload'].='var dates=$(\''.$id.'\').datepicker({onSelect:function(selectedDate){instance=$(this).data(\'datepicker\'),date=$.datepicker.parseDate(instance.settings.dateFormat || $.datepicker._defaults.dateFormat,selectedDate,instance.settings);}});dc.tabhover();dc.tips();';
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - <a href="/s.php?module=task&action=list">生产任务单列表</a> - 新建生产任务单', 'right'=>'<a href="/s.php?module=task&action=list">返回列表</a>','body'=>$body));
	}elseif($this->kclass->input['step']==2){
		if($this->kclass->input['itemSelect']==''){
			$e.='<li>请选择订单明细</li>';
			$this->kclass->messager(array(
				'title' => '新建生产任务单',
				'text' => '您在新建生产任务单的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$start=strtotime(date('Y-m-d',TIMENOW));
		$end=strtotime(date('Y-m-d',$start))+86400;
		$counter=$this->kclass->DB->queryFirst("SELECT COUNT(*) AS count FROM task WHERE created>='".$start."' AND created<='".$end."'");
		$taskno=$this->kclass->id(array('task'=>TIMENOW,'number'=>$counter['count']));
		$count=count($this->kclass->input['itemId']);
		for($i=$k=1;$k<$count+1;$k++){
			if($this->kclass->input['itemSelect'][$k]!=''){
				$item=$this->kclass->DB->queryFirst("
					SELECT mp.*,
						r.requirementid,r.requirementno,r.remark AS requirementremark,
						mv.title AS version
					FROM `mrp` AS mp
					LEFT JOIN `materialversion` AS mv ON (mv.versionid=mp.versionid)
					LEFT JOIN requirement AS r ON (r.requirementid=mp.requirementid)
					WHERE mp.killed=0 AND mp.ifChooser=0 AND mp.mrpid='".$this->kclass->input['itemSelect'][$k]."'
					LIMIT 0,1
				");
				$wc=$this->kclass->DB->queryFirst("
					SELECT wci.workcenterid,wc.title AS workcenter,wc.burthen
					FROM `workcenteritem` AS wci
					LEFT JOIN workcenter AS wc ON (wc.workcenterid=wci.workcenterid)
					WHERE wci.killed=0 AND wci.materialid='".$item['materialid']."'
				");
			//	print_r($wc);
				$materialInfo=$this->kclass->getMaterial(array('materialid'=>$item['materialid'],'itemid'=>$item['materialitemid']));
				$workcenter=$this->kclass->chooserWorkcenter(array('name'=>'workcenterid['.$i.']','width'=>150,'hasBlank'=>1,'selectedid'=>$wc['workcenterid']));
				$requirementno='<a href="/s.php?module=requirement&action=view&requirementid='.$item['requirementid'].'">'.$item['requirementno'].'</a>';
				$itemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').' small">
					<input type="hidden" name="itemId['.$i.']" value="'.$item['mrpid'].'" />
					<td>'.$i.'</td>
					<td>'.$requirementno.'<input type="hidden" name="itemMftid['.$i.']" value="'.$item['requirementid'].'"></td>
					<td><a href="'.$materialInfo['url'].'">'.$materialInfo['no'].'</a>　'.$materialInfo['material'].''.$materialInfo['standard'].'</td>
					<td>'.$item['version'].'</td>
					<td>'.$item['netQuantity'].'</td>
					<td>'.$workcenter.'</td>
					<td>'.$wc['burthen'].'</td>
					<td>'.$item['requirementremark'].'</td>
					<td><input type="text" id="workdate'.$i.'" name="itemWorkdate['.$i.']" size="15" /></td>
					<td><input type="text" id="finishdate'.$i.'" name="itemFinishdate['.$i.']" size="15" value="'.date('Y-m-d',$item['requiredate']).'" /></td>
					<td><input type="text" id="dispatchnumber'.$i.'" name="itemDispatchnumber['.$i.']" size="10" /> 人</td>
					<td><input type="text" id="workhours'.$i.'" name="itemWorkhours['.$i.']" size="15" /> 时</td>
					<td><input type="text" id="remark'.$i.'" name="itemRemark['.$i.']" size="15" /></td>
				</tr>';
				//领料bom
				$bomtr.='<tr><td colspan="16" class="bold"><a href="'.$materialInfo['url'].'">'.$materialInfo['no'].'</a>　'.$materialInfo['material'].''.$materialInfo['standard'].'　 (版本：'.$item['version'].')</td></tr>';
				$child=$this->kclass->bomChildList(array('parentid'=>$item['materialid'],'materialitemid'=>$item['materialitemid'],'versionid'=>$item['versionid'],'quantity'=>$item['netQuantity'],'loopNum'=>1));
				if(!empty($child['bom'])){
					$j=1;
					foreach($child['bom'] as $val){
						$version=$this->kclass->DB->queryFirst("
							SELECT mv.qualified,mv.planInbound,mv.planOutbound,m.min
							FROM materialversion AS mv
							LEFT JOIN material AS m ON (mv.materialid=m.materialid)
							WHERE mv.killed=0 AND mv.versionid='".$val['versionid']."'
						");
						$usable=$version['qualified']+$version['planInbound']-$version['planOutbound'];
						$bomtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').' small '.$this->kclass->iif($val['ifGeneral']==0,$this->kclass->iif($val['quantity']>$version['qualified'],'bgYellow',''),'').'">
							<td>'.$j.'</td>
							<td><a href="'.$val['url'].'">'.$val['no'].'</a></td>
							<td>'.$val['title'].'　'.$val['standard'].'</td>
							<td>'.$val['version'].'</td>
							<td>'.$val['origin'].'</td>
							<td>'.$this->kclass->iif($val['ifGeneral']==0,$val['quantity'],'通用辅料').'</td>
							<td>'.$version['qualified'].'</td>
							<td>'.$version['planInbound'].'</td>
							<td>'.$version['planOutbound'].'</td>
							<td>'.$version['min'].'</td>
							<td>'.$usable.'</td>
							</tr>';
							$j++;
						}
					}else{
						$bomtr.='<tr><td colspan="16" class="darkred">物资 ['.$title.'] BOM(子) 不存在！</td></tr>';
					}
					unset($child);
					//
					if($i<=$counter){
					$id.='#workdate'.$i.',';
					$id.='#finishdate'.$i.',';
				}else{
					$id.='#workdate'.$i;
					$id.='#finishdate'.$i;
				}
				$i++;
			}
		}
		$flow=$this->kclass->listFlow(array('module'=>'task','statusid'=>'1'));
		$body=<<<EOF
<form action="/s.php?module=task&action=insert" name="task" method="post">
<input type="hidden" name="module" value="task" />
<input type="hidden" name="action" value="insert" />
<input type="hidden" name="addtype" value="plan" />
<input type="hidden" name="taskno" value="{$taskno}" />
<div><span class="right">{$flow}</span></div>
<table class="hundred">
<thead><tr><th colspan="6">新建生产任务单</th></tr></thead>
<tbody>
<tr class="odd">
<td width="100">任务单编号：<span class="red bold">*</span></td><td width="450"><span class="middle bold darkred">{$taskno}</span> <span class="gray small">此为预估编号，保存后才可确定</span></td>
<td width="100">任 务 员：<span class="red bold">*</span></td><td width="450"><input type="text" name="tasker" value="{$this->kclass->user['realname']}"></td>
<td width="100">备　　注：</td>
<td><textarea type="text" name="remark" style="width:250px;height:100px"></textarea>
</tr>
</tbody>
</table>
<table class="hundred">
<thead><tr><th colspan="16">工作中心任务明细</th></tr></thead>
<tbody>
<tr class="even center"><td width="15">ID</td><td width="60">计划单</td><td>物资</td><td>版本</td><td width="100">净需求数量</td><td width="100">工作中心</td><td width="50">负荷</td><td width="50">备注</td><td width="120">投产日期 <span class="red bold">*</span></td><td width="120">完工日期 <span class="red bold">*</span></td><td width="120">派工人数 <span class="red bold">*</span></td><td width="140">工作时数 <span class="red bold">*</span></td><td width="120">备注</td></tr>
{$itemtr}
</tbody>
</table>
<table class="hundred">
<thead><tr><th colspan="15">物料需求明细</th></tr></thead>
<tbody><tr class=""><td width="30">ID</td><td>物资编码</td><td>物资详情</td><td>版本</td><td>来源</td><td>需求数量</td><td>良品库存</td><td>待入库存</td><td>待出库存</td><td>安全库存</td><td>计划可用</td></tr>
{$bomtr}
<tr class="even">
<td class="small gray" colspan="16">
注意事项：<br>
①如果所列表格不够，那么在提交保存后再点击“修改”，来添加所要生产的物资；<br>
②如果不从“生产任务单中”选择物资，而是生产新物资，那么就填写“物资编号”、“物资名称”、“物资规格”，或者在物资列表中选择；<br>
③“生产任务单及其物资”中 灰色的物资表示已经被选中过，不可以继续选择；<br>
④时间的格式为2012-02-06，中间用半角短横杠隔开；<br>
⑤从库中物资选择时，“单位”不需要选，但是如果生产物资不存在库中时，就必须要选择“单位”
</td>
</tr>
</tbody>
</table>
<table class="tlist tsuite" align="center" width="100%">
<tbody>
<tr class="odd small" nohover>
<td class="center"><input type="submit" id="submitButton" value="  提交  " accesskey="s"><input type="reset" value="  复位  "/></td>
</tr>
</tbody>
</table>
</form>
EOF;

		$this->kclass->tbline+=4;
		$this->kclass->page['title'].='新建生产任务单';
		$this->kclass->page['onload'].='var dates=$(\''.$id.'\').datepicker({onSelect:function(selectedDate){instance=$(this).data(\'datepicker\'),date=$.datepicker.parseDate(instance.settings.dateFormat || $.datepicker._defaults.dateFormat,selectedDate,instance.settings);}});dc.tabhover();';
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - <a href="/s.php?module=task&action=list">生产任务单列表</a> - 新建生产任务单', 'right'=>'<a href="/s.php?module=task&action=list">返回列表</a>','body'=>$body));
	}
}
// insert task
function insert(){
	//print_r($this->kclass->input);
	if($this->kclass->input['requestMethod'] != 'post'){
		$e='<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
	}else{
		if($this->kclass->input['addtype']=="hand"){
			if($this->kclass->input['tasker']==''){
				$e.='<li>请填写生产任务单中的 任务员.</li>';
			}
			$key=array();
			for($i=1;$i<=6;$i++){//获得输入框行的编号
				if($this->kclass->input['itemMaterialid'][$i]!=0){
					$key[]=$i;
				}
			}
			$count=count($key);
			if($count==0){
				$e.='<li>请填写生产任务单中的 任务明细.</li>';
			}
			for($j=0;$j<$count;$j++){
				$k=$j+1;
				if($this->kclass->input['itemVersionid'][$key[$j]]==''){
					$e.='<li>请填写生产任务单明细[ID:'.$k.']中物资的版本.</li>';
				}
				if($this->kclass->input['itemQuantity'][$key[$j]]==''){
					$e.='<li>请填写生产任务单明细[ID:'.$k.']中物资的需求数量.</li>';
				}
				if($this->kclass->input['itemWorkdate'][$key[$j]]==''){
					$e.='<li>请填写生产任务单明细[ID:'.$k.']中物资的投产日期.</li>';
				}
				if($this->kclass->input['itemFinishdate'][$key[$j]]==''){
					$e.='<li>请填写生产任务单明细[ID:'.$k.']中物资的完工日期.</li>';
				}
				if($this->kclass->input['itemDispatchnumber'][$key[$j]]==''){
					$e.='<li>请填写生产任务单明细[ID:'.$k.']中物资的派工人数.</li>';
				}
				if($this->kclass->input['itemWorkhours'][$key[$j]]==''){
					$e.='<li>请填写生产任务单明细[ID:'.$k.']中物资的工时.</li>';
				}
			}
		}elseif($this->kclass->input['addtype']=="plan"){
			if($this->kclass->input['tasker']==''){
				$e.='<li>请填写生产任务单中的 任务员.</li>';
			}
			$count=count($this->kclass->input['itemId']);
			for($m=1;$m<=$count;$m++){
				if($this->kclass->input['workcenterid'][$m]==''){
					$e.='<li>请填写生产任务单中生产明细[ID:'.$m.']的工作中心</li>';
				}
				if($this->kclass->input['itemWorkdate'][$m]==''){
					$e.='<li>请填写生产任务单中生产明细[ID:'.$m.']的 投产日期</li>';
				}
				if($this->kclass->input['itemFinishdate'][$m]==''){
					$e.='<li>请填写生产任务单中生产明细[ID:'.$m.']的 完工日期</li>';
				}
				if($this->kclass->input['itemDispatchnumber'][$m]==''){
					$e.='<li>请填写生产任务单中生产明细[ID:'.$m.']的 派工人数</li>';
				}
				if($this->kclass->input['itemWorkhours'][$m]==''){
					$e.='<li>请填写生产任务单中生产明细[ID:'.$m.']的 工作时数</li>';
				}
			}
		}
	}
	if($e){
		$this->kclass->messager(array(
			'title' => '新建生产任务单',
			'text' => '您在新建生产任务单的过程中有以下错误：<ul>'.$e.'</ul>',
			'url' => 'javascript:history.back()',
			'sec' => 3
		));
	}
	if($this->kclass->input['addtype']=="hand"){//hand方式已作废
		$this->kclass->DB->query("
			INSERT INTO task (taskno,addtype,statusid,tasker,remark,ordering,created,creator)
			VALUES ('".$this->kclass->input['taskno']."',1,1,'".$this->kclass->input['tasker']."','".$this->kclass->input['remark']."',0,'".TIMENOW."','".$this->kclass->user['userid']."')
		");
		$taskid=$this->kclass->DB->insertID();
		for($m=0;$m<$count;$m++){
			//insert item
			if($this->kclass->input['itemWorkdate'][$key[$m]]){
				$sd=explode('-', $this->kclass->input['itemWorkdate'][$key[$m]]);
				$workdate=mktime(0,0,0,$sd[1],$sd[2],$sd[0]);
			}else{
				$workdate=0;
			}
			if($this->kclass->input['itemFinishdate'][$key[$m]]){
				$sd=explode('-', $this->kclass->input['itemFinishdate'][$key[$m]]);
				$finishdate=mktime(0,0,0,$sd[1],$sd[2],$sd[0]);
			}else{
				$finishdate=0;
			}
			/*修改前的增加工作中心
			*$wc=$this->kclass->DB->queryFirst("
			*	SELECT workcenterid
			*	FROM `workcenteritem`
			*	WHERE killed=0 AND materialid='".$this->kclass->input['itemMaterialid'][$key[$m]]."'
			*");
			*/
			
			if($wc['workcenterid']=='')$wc['workcenterid']=0;
			$materialitemid=$this->kclass->iif($this->kclass->input['materialItemid'][$key[$m]]>0,$this->kclass->input['materialItemid'][$key[$m]],0); 
			$this->kclass->DB->query("
				INSERT INTO taskitem (materialid,versionid,materialitemid,taskid,requirementid,workcenterid,statusid,workdate,finishdate,dispatchnumber,workhours,quantity,remark,created,creator)
				VALUES ('".$this->kclass->input['itemMaterialid'][$key[$m]]."','".$this->kclass->input['itemVersionid'][$key[$m]]."','".$materialitemid."','".$taskid."',0,'".$wc['workcenterid']."',1,'".$workdate."','".$finishdate."','".$this->kclass->input['itemDispatchnumber'][$key[$m]]."','".$this->kclass->input['itemWorkhours'][$key[$m]]."','".$this->kclass->input['itemQuantity'][$key[$m]]."','".$this->kclass->input['itemRemark'][$key[$m]]."','".TIMENOW."','".$this->kclass->user['userid']."')
			");
		}
	}elseif($this->kclass->input['addtype']=='plan'){
		$this->kclass->DB->query("
			INSERT INTO task (taskno,addtype,statusid,tasker,remark,ordering,created,creator)
			VALUES ('".$this->kclass->input['taskno']."',2,1,'".$this->kclass->input['tasker']."','".$this->kclass->input['remark']."',0,'".TIMENOW."','".$this->kclass->user['userid']."')
		");
		$taskid=$this->kclass->DB->insertID();
		$count=count($this->kclass->input['itemId']);
		for($m=1;$m<$count+1;$m++){
			if($this->kclass->input['itemWorkdate'][$m]!=''){
				if($this->kclass->input['itemWorkdate'][$m]){
					$sd=explode('-',$this->kclass->input['itemWorkdate'][$m]);
					$workdate=mktime(0,0,0,$sd[1],$sd[2],$sd[0]);
				}else{
					$workdate=0;
				}
				if($this->kclass->input['itemFinishdate'][$m]){
					$sd1=explode('-',$this->kclass->input['itemFinishdate'][$m]);
					$finishdate=mktime(0,0,0,$sd1[1],$sd1[2],$sd1[0]);
				}else{
					$finishdate=0;
				}
				$item=$this->kclass->DB->queryFirst("
					SELECT *
					FROM `mrp`
					WHERE killed=0 AND ifChooser=0 AND mrpid='".$this->kclass->input['itemId'][$m]."'
				");
			/*修改前的增加工作中心
			*	$wc=$this->kclass->DB->queryFirst("
			*		SELECT workcenterid
			*		FROM `workcenteritem`
			*		WHERE killed=0 AND materialid='".$item['materialid']."'
			*	");
				*/
				$wc['workcenterid'][$m]=$this->kclass->input['workcenterid'][$m];
				if($wc['workcenterid'][$m]=='') $wc['workcenterid'][$m]=0;
				$this->kclass->DB->query("
					INSERT INTO taskitem (materialid,versionid,materialitemid,taskid,requirementid,mrpid,workcenterid,statusid,workdate,finishdate,dispatchnumber,workhours,quantity,remark,created,creator)
					VALUES ('".$item['materialid']."','".$item['versionid']."','".$item['materialitemid']."','".$taskid."','".$item['requirementid']."','".$item['mrpid']."','".$this->kclass->iif($this->kclass->input['workcenterid'][$m]!='',$this->kclass->input['workcenterid'][$m],'0')."',1,'".$workdate."','".$finishdate."','".$this->kclass->input['itemDispatchnumber'][$m]."','".$this->kclass->input['itemWorkhours'][$m]."','".$item['netQuantity']."','".$this->kclass->input['itemRemark'][$m]."','".TIMENOW."','".$this->kclass->user['userid']."')
				");
				$this->kclass->DB->query("
		                 INSERT INTO workcenteritem (workcenterid,materialid,versionid,materialitemid,creator,created)
		                     VALUES ('".$this->kclass->iif($this->kclass->input['workcenterid'][$m]!='',$this->kclass->input['workcenterid'][$m],'0')."','".$item['materialid']."','".$item['versionid']."','".$item['materialitemid']."','".$this->kclass->user['userid']."','".TIMENOW."')
	                                 ");
				$this->kclass->DB->query("UPDATE `mrp` SET ifChooser=1 WHERE mrpid='".$this->kclass->input['itemId'][$m]."'");
			}
		}
	}
	$this->kclass->messager(array(
		'title' => '新建生产任务单',
		'text' => '生产任务单 【<b>'.$this->kclass->input['taskno'].'</b>】 已新建成功!',
		'url' => '/s.php?module=task&action=view&taskid='.$taskid,
		'sec' => 2
	));
}
//
function update(){
	$task=$this->kclass->DB->queryFirst("
		SELECT taskid,taskno,addtype,statusid,tasker,remark,created
		FROM task
		WHERE killed=0 AND taskid='".$this->kclass->input['taskid']."'
		LIMIT 0,1
	");
	if(!$task){
		$this->kclass->messager(array(
			'title' => '修改生产',
			'text' => '选择修改的生产任务单不存在！',
			'url' => '/s.php?module=task&action=list',
			'sec' => 2
		));
	}
	if($this->kclass->input['step']=='item'){
		if($task['addtype']==1){
			//任务的物资
			$items=$this->kclass->DB->query("
				SELECT ti.*,
					wc.title AS workcenter,
					r.requirementno,
					mv.title AS version
				FROM taskitem AS ti
				LEFT JOIN requirement AS r ON (r.requirementid=ti.requirementid)
				LEFT JOIN workcenter AS wc ON (wc.workcenterid=ti.workcenterid)
				LEFT JOIN `materialversion` AS mv ON (mv.versionid=ti.versionid)
				WHERE ti.killed=0 AND ti.taskid='".$task['taskid']."'
				ORDER BY ti.ordering ASC
			");
			if($this->kclass->DB->numRows()){
				$k=1;
				while($item=$this->kclass->DB->fetchArray($items)){
					$materialInfo=$this->kclass->getMaterial(array('materialid'=>$item['materialid'],'itemid'=>$item['materialitemid']));
					$itemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').' small">
						<td>'.$k.'<input type="hidden" name="itemId['.$k.']" value="'.$item['itemid'].'"></td>
						<td>'.$this->kclass->iif($task['addtype']==1,'手工方式','生产计划').'</td>
						<td>'.$this->kclass->iif($task['workcenter']=="",'无',$task['workcenter']).'</td>
						<td><span class="small"><a href="'.$materialInfo['url'].'">'.$materialInfo['no'].'</a> '.$materialInfo['material'].' '.$materialInfo['standard'].'</span></td>
						<td>'.$item['version'].'</td>
						<td><input type="text" name="itemQuantity['.$k.']" value="'.$item['quantity'].'"> '.$item['unit'].'</td>
						<td><input type="text" name="itemWorkdate['.$k.']" id="workdate'.$k.'"  value="'.date('Y-m-d',$item['workdate']).'"></td>
						<td><input type="text" name="itemFinishdate['.$k.']" id="finishdate'.$k.'" value="'.date('Y-m-d',$item['finishdate']).'"></td>
						<td><input type="text" name="itemDispatchnumber['.$k.']" value="'.$item['dispatchnumber'].'"></td>
						<td><input type="text" name="itemWorkhours['.$k.']" value="'.$item['workhours'].'"></td>
						<td><input type="text" name="itemRemark['.$k.']" value="'.$item['remark'].'"></td>
						<td>'.$this->kclass->iif($task['statusid']==1,'<input type="checkbox" name="itemKill['.$k.']" value="'.$item['itemid'].'">','').'</td>
						</tr>';
					$id.='#workdate'.$k.',';
					$id.='#finishdate'.$k.',';
					$k++;
				}
				$itemtr.='</tbody></table><table class="hundred"><thead><tr><th colspan="14">新建任务生产明细</th></tr></thead><tbody><tr><td colspan="14" class="darkred">全部任务单下的条目明细</td></tr><tr class="even center"><td width="15">ID</td><td>选择物资<span class="red bold">*</span></td><td>版本 <span class="bold red">*</span></td><td width="180">净需求数量 <span class="red bold">*</span></td><td width="180">投产日期<span class="red bold">*</span></td><td width="180">完工日期<span class="red bold">*</span></td><td width="180">派工人数<span class="red bold">*</span></td><td width="180">工作时数<span class="red bold">*</span></td><td width="200">备注</td></tr>';
			}
			for($i = $k; $i <= $k+6; $i++){
				$material=$this->kclass->chooserMaterial(array('hName'=>'itemMaterialid['.$i.']','hId'=>'itemMaterialid'.$i,'name'=>'itemMaterial['.$i.']','id'=>'itemMaterial'.$i,'width'=>400,'line'=>$i,'showVersion'=>1));
				$itemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').'">
					<td>'.$i.'</td>
					<td>'.$material.'</td>
					<td><select name="itemVersionid['.$i.']" id="itemVersionid'.$i.'" style="width:80px;"></select></td>
					<td><input type="text" id="quantity'.$i.'" name="itemQuantity['.$i.']" size="15" /></td>
					<td><input type="text" id="workdate'.$i.'" name="itemWorkdate['.$i.']" size="15" /></td>
					<td><input type="text" id="finishdate'.$i.'" name="itemFinishdate['.$i.']" size="15"/></td>
					<td><input type="text" id="dispatchnumber'.$i.'" name="itemDispatchnumber['.$i.']" size="10" /> 人</td>
					<td><input type="text" id="workhours'.$i.'" name="itemWorkhours['.$i.']" size="10" /> 时</td>
					<td><input type="text" id="remark'.$i.'" name="itemRemark['.$i.']" size="25" /></td>
				</tr>';
				if($i<=$k+6){
					$id.='#workdate'.$i.',';
					$id.='#finishdate'.$i.',';
				}else{
					$id.='#workdate'.$i;
					$id.='#finishdate'.$i;
				}
			}
			$body=<<<EOF
<form action="/s.php?module=task&action=doupdate" name="task" method="post">
<input type="hidden" name="module" value="task" />
<input type="hidden" name="action" value="doupdate" />
<input type="hidden" name="taskid" value="{$task['taskid']}" />
<input type="hidden" name="taskno" value="{$task['taskno']}" />
<input type="hidden" name="addtype" value="1" />
<input type="hidden" name="step" value="item" />
<table class="hundred">
<thead><tr><th colspan="14">当前任务生产明细</th></tr></thead><tbody><tr><td colspan="14" class="darkred">当前选择的任务单下的条目明细</td></tr><tr class="center"><td>ID</td><td>录入类型</td><td>工作中心</td><td>物资</td><td>版本</td><td>生产数量 <span class="red bold">*</span></td><td>生产日<span class="red bold">*</span></td><td>完工日<span class="red bold">*</span></td><td>派工人数<span class="red bold">*</span></td><td>生产工时<span class="red bold">*</span></td><td>备注</td><td>删</td></tr>
{$itemtr}
<tr class="even">
<td class="small gray" colspan="14">
注意事项：<br>
	①编号咱未确定编号规则，所以默认为OM+时间戳<br>
	②如果所列表格不够，那么在提交保存后再点击“修改”，来增加订单物资；<br>
	③需要加工的物资从“物资”中选择；<br>
	④“加工交期 ”的格式为2012-02-06，中间用半角短横杠隔开；<br>
</td>
</tr>
</tbody>
</table>
<table class="tlist tsuite" align="center" width="100%">
<tbody>
<tr class="odd" nohover>
<td align="center" colspan=4><input type="submit" id="submitButton" value="  提交  " accesskey="s"><input type="reset" value="  复位  "/></td>
</tr>
</tbody>
</table>
</form>
EOF;
		}else{
			$taskItems=$this->kclass->DB->query("
				SELECT ti.*,
					mv.title AS version,
					r.requirementno,r.statusid AS rtstatusid
				FROM taskitem AS ti
				LEFT JOIN `materialversion` AS mv ON (mv.versionid=ti.versionid)
				LEFT JOIN requirement AS r ON (r.requirementid=ti.requirementid)
				LEFT JOIN mrp AS mp ON (mp.mrpid=ti.mrpid)
				WHERE ti.killed=0 AND ti.taskid='".$this->kclass->input['taskid']."'
				ORDER BY ti.itemid ASC
			");
			if($this->kclass->DB->numRows()){
				$m=1;
				while($taskItem=$this->kclass->DB->fetchArray($taskItems)){
					$materialInfo=$this->kclass->getMaterial(array('materialid'=>$taskItem['materialid'],'itemid'=>$taskItem['materialitemid'],'materialno'=>$taskItem['materialno']));
					//是否有审核后的派工单
					$dispatch=$this->kclass->DB->queryFirst("
						SELECT d.statusid
						FROM dispatchitem AS di
						LEFT JOIN dispatch AS d ON (d.dispatchid=di.dispatchid)
						WHERE di.taskitemid='".$taskItem['itemid']."' AND d.statusid>1
					");
					$orgItemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').' small">
						<td><input type="hidden" name="taskItemId['.$m.']" value="'.$taskItem['itemid'].'" />
							<input type="hidden" name="mrpid['.$m.']" value="'.$taskItem['mrpid'].'" />'.$m.'</td>
						<td><a href="/s.php?module=requirement&action=view&requirementid='.$taskItem['requirementid'].'">'.$taskItem['requirementno'].'</a></td>
						<td><a href="'.$materialInfo['url'].'">'.$materialInfo['no'].'</a></td>
						<td>'.$materialInfo['material'].'</td>
						<td>'.$materialInfo['standard'].'</td>
						<td>'.$taskItem['version'].' </td>
						<td>'.$taskItem['quantity'].' </td>
						<td><input type="text" id="olworkdate'.$m.'" name="olworkdate['.$m.']" size="10" value="'.date('Y-m-d',$taskItem['workdate']).'" /></td>
						<td><input type="text" id="olfinishdate'.$m.'" name="olfinishdate['.$m.']" size="10" value="'.date('Y-m-d',$taskItem['finishdate']).'" /></td>
						<td><input type="text" name="oldispatchnumber['.$m.']" value="'.$taskItem['dispatchnumber'].'" size="10"/> 人</td>
						<td><input type="text" name="olworkhours['.$m.']" value="'.$taskItem['workhours'].'" size="10"/> 时</td>
						<td><input type="text" name="olremark['.$m.']" value="'.$taskItem['remark'].'" size="12" /></td>
						<td>'.$this->kclass->iif($taskItem['rtstatusid']==2 AND $taskItem['statusid']==1 AND $dispatch=='' AND $taskItem['ifChooser']==0,'<input type="checkbox" name="itemKill['.$m.']" value="'.$taskItem['itemid'].'"/>','已投产').'</td>
					</tr>';//需求计划单是否被解锁
					$id.='#olworkdate'.$m.',';
					$id.='#olfinishdate'.$m.',';
					$m++;
				}
			}
			$requirements=$this->kclass->DB->query("
				SELECT r.*,
				u.username AS creator,
				us.username AS modifier
				FROM `requirement` AS r
				LEFT JOIN `user` AS u ON (u.userid=r.creator)
				LEFT JOIN `user` AS us ON (us.userid=r.modifier)
				WHERE r.killed=0 AND r.statusid=2
			");
			if($this->kclass->DB->numRows()){
				$i=1;
				while($requirement=$this->kclass->DB->fetchArray($requirements)){
					//申请的物资
					$items=$this->kclass->DB->query("
						SELECT mp.*,
							mv.title AS version
						FROM mrp AS mp
						LEFT JOIN material AS m ON (m.materialid=mp.materialid)
						LEFT JOIN `materialversion` AS mv ON (mv.versionid=mp.versionid)
						WHERE mp.killed=0 AND m.originid=1 AND mp.ifChooser=0 AND mp.requirementid='".$requirement['requirementid']."'
						ORDER BY mp.ordering ASC
					");
					if($this->kclass->DB->numRows()){
						$k=1;
						$itemtr.='<tbody><tr class="bold "><td colspan=13>需求计划单编号：<span class="middle bold"><a href="/s.php?module=requirement&action=view&requirementid='.$requirement['requirementid'].'">'.$requirement['requirementno'].'</a></span>　申请人：'.$requirement['creator'].'　申请时间：'.date('Y-m-d',$requirement['created']).'</td></tr><tr class="center even" nohover><td width="15">ID</td><td>物资编号</td><td>物资名称</td><td>物资规格</td><td>需求数量</td><td>需求日期</td><td width="100">投产日期</td><td width="100">完工日期</td><td width="120">派工人数</td><td width="120">工作时数</td><td>备注</td><td>选</td></tr>';
						while($item=$this->kclass->DB->fetchArray($items)){
							$materialInfo=$this->kclass->getMaterial(array('materialid'=>$item['materialid'],'itemid'=>$item['materialitemid']));
							$itemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').' small"><input type="hidden" name="itemId['.$i.']" value="'.$item['itemid'].'" />
								<td>'.$k.'</td>
								<td><a href="'.$materialInfo['url'].'">'.$materialInfo['no'].'</a></td>
								<td>'.$materialInfo['material'].'</td>
								<td>'.$materialInfo['standard'].'</td>
								<td>'.$item['netQuantity'].'</td>
								<td>'.date('Y-m-d',$item['requiredate']).'</td>
								<td><input type="text" id="itemworkdate'.$i.'" name="itemworkdate['.$i.']" size="10" /></td>
								<td><input type="text" id="itemfinishdate'.$i.'" name="itemfinishdate['.$i.']" size="10" value="'.date('Y-m-d',$item['requiredate']).'" /></td>
								<td><input type="text" name="itemdispatchnumber['.$i.']"  size="10"/> 人</td>
								<td><input type="text" name="itemworkhours['.$i.']"  size="10"/> 时</td>
								<td><input type="text" id="remark'.$i.'" name="itemremark['.$i.']" size="12" /></td>
								<td><input type="checkbox" name="itemSelect['.$i.']" value="'.$item['mrpid'].'"/></td></tr>';
							$id.='#itemworkdate'.$i.',';
							$id.='#itemfinishdate'.$i.',';
							$k++;$i++;
						}
					}
				}
				$itemtr.='';
			}
$body=<<<EOF
<form action="/s.php?module=task&action=doupdate" method="post">
<input type="hidden" name="module" value="task" />
<input type="hidden" name="action" value="doupdate" />
<input type="hidden" name="taskid" value="{$this->kclass->input['taskid']}" />
<input type="hidden" name="taskno" value="{$task['taskno']}" />
<input type="hidden" name="step" value="item" />
<table class="hundred"><thead><tr><th colspan=14>当前生产任务单物料需求明细</th></tr></thead>
<tbody>
<tr class="center even" nohover><td width="15">ID</td><td width="50">计划单编号</td><td>物资编号</td><td>物资名称</td><td>物资规格</td><td>版本</td><td>需求数量</td><td width="100">投产日期</td><td width="100">完工日期</td><td width="120">派工人数</td><td width="120">工作时数</td><td>备注</td><td>删</td></tr>
{$orgItemtr}
</tbody>
</table>
<table class="hundred"><thead><tr><th colspan=13>选择计划单物料需求明细</th></tr></thead>
{$itemtr}
<tr class="center">
<td colspan=13><input type="submit" value="提交"><input type="reset" value="重置"></td>
</tr>
</tbody></table>
</form>
EOF;
		}
		$this->kclass->tbline+=4;
		$this->kclass->page['title'].='选择生产任务单明细';
		$this->kclass->page['onload'].='var dates=$(\''.$id.'\').datepicker({onSelect:function(selectedDate){instance=$(this).data(\'datepicker\'),date=$.datepicker.parseDate(instance.settings.dateFormat || $.datepicker._defaults.dateFormat,selectedDate,instance.settings);}});dc.tabhover()';
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - <a href="/s.php?module=task&action=list">生产任务单列表</a> - 修改生产任务单', 'right'=>'<a href="/s.php?module=task&action=list">返回列表</a>','body'=>$body));
	}else{
		$body=<<<EOF
<form action="/s.php?module=task&action=doupdate" name="task" method="post">
<input type="hidden" name="module" value="task" />
<input type="hidden" name="action" value="doupdate" />
<input type="hidden" name="taskid" value="{$this->kclass->input['taskid']}" />
<input type="hidden" name="taskno" value="{$task['taskno']}" />
<table class="hundred">
<thead><tr><th colspan="6">新建生产任务单</th></tr></thead>
<tbody>
<tr class="odd">
<td width="120">任务单编号：<span class="red bold">*</span></td><td width="450">{$task['taskno']}<span class="small gray">(编号不会被修改)</span></td>
<td width="100">任 务 员：<span class="red bold">*</span></td><td width="450"><input type="text" name="tasker" value="{$task['tasker']}"></td>
<td width="100">备　　注：</td><td><textarea type="text" name="remark" style="width:444px;height:111px;">{$task['remark']}</textarea></td>
</tr>
</tbody>
</table>
<table class="tlist tsuite" align="center" width="100%">
<tbody>
<tr class="odd" colspan=4 nohover>
<td class="center"><input type="submit" id="submitButton" value="  提交  " accesskey="s"><input type="reset" value="  复位  "/></td>
</tr>
</tbody>
</table>
</form>
EOF;
		$this->kclass->tbline+=4;
		$this->kclass->page['title'].='修改生产任务单';
		$this->kclass->page['onload'].='dc.tabhover();dc.tips();';
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - <a href="/s.php?module=task&action=view&taskid='.$this->kclass->input['taskid'].'">查看生产任务单</a> - 修改生产任务单', 'right'=>'<a href="/s.php?module=task&action=update">返回列表</a>　|　<a href="/s.php?module=task&action=view&taskid='.$this->kclass->input['taskid'].'">查看生产任务单</a>　','body'=>$body));
	}
}
//
function doupdate(){
	if($this->kclass->input['requestMethod'] != 'post'){
		$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
	}else{
		$task=$this->kclass->DB->queryFirst("SELECT taskid,taskno,statusid FROM `task` WHERE taskid='".$this->kclass->input['taskid']."'");
		if(!$task){
			$e .= '<li>您要编辑的生产任务单并不存在，请返回列表刷新后再操作。</li>';
		}
		if($this->kclass->input['step']=='item'){
			if($this->kclass->input['addtype']=='1'){
				$count=count($this->kclass->input['itemId']);
				for($m=1;$m<$count+1;$m++){
					if($this->kclass->input['itemQuantity'][$m]==''){
						$e.='<li>请填写生产任务单[原]生产明细[ID:'.$m.']的 生产数量</li>';
					}
					if($this->kclass->input['itemWorkdate'][$m]==''){
						$e.='<li>请填写生产任务单[原]生产明细[ID:'.$m.']的 生产日</li>';
					}
					if($this->kclass->input['itemFinishdate'][$m]==''){
						$e.='<li>请填写生产任务单[原]生产明细[ID:'.$m.']的 完工日</li>';
					}
					if($this->kclass->input['itemDispatchnumber'][$m]==''){
						$e.='<li>请填写生产任务单[原]生产明细[ID:'.$m.']的 派工人数</li>';
					}
					if($this->kclass->input['itemWorkhours'][$m]==''){
						$e.='<li>请填写生产任务单[原]生产明细[ID:'.$m.']的 生产工时</li>';
					}
				}
				for($j=$m;$j<$m+7;$j++){
					if($this->kclass->input['itemMaterialid'][$j]>0){
					if($this->kclass->input['itemVersionid'][$j]==''){
						$e.='<li>请填写生产任务单中生产明细[ID:'.$j.']的 版本</li>';
					}
					if($this->kclass->input['itemQuantity'][$j]==''){
						$e.='<li>请填写生产任务单中生产明细[ID:'.$j.']的 需求数量</li>';
					}
					if($this->kclass->input['itemWorkdate'][$j]==''){
						$e.='<li>请填写生产任务单中生产明细[ID:'.$j.']的 生产日</li>';
					}
					if($this->kclass->input['itemFinishdate'][$j]==''){
						$e.='<li>请填写生产任务单中生产明细[ID:'.$j.']的 完工日</li>';
					}
					if($this->kclass->input['itemDispatchnumber'][$j]==''){
						$e.='<li>请填写生产任务单中生产明细[ID:'.$j.']的 派工人数</li>';
					}
					if($this->kclass->input['itemWorkhours'][$j]==''){
						$e.='<li>请填写生产任务单中生产明细[ID:'.$j.']的 工作时数</li>';
					}
				}
			}
		}else{
			$count=count($this->kclass->input['taskItemId']);
			for($m=1;$m<$count+1;$m++){
				if($this->kclass->input['olworkdate'][$m]==''){
					$e.='<li>请填写生产任务单[原]生产明细[ID:'.$m.']的 生产日</li>';
				}
				if($this->kclass->input['olfinishdate'][$m]==''){
					$e.='<li>请填写生产任务单[原]生产明细[ID:'.$m.']的 完工日</li>';
				}
				if($this->kclass->input['oldispatchnumber'][$m]==''){
					$e.='<li>请填写生产任务单[原]生产明细[ID:'.$m.']的 派工人数</li>';
				}
				if($this->kclass->input['olworkhours'][$m]==''){
					$e.='<li>请填写生产任务单[原]生产明细[ID:'.$m.']的 生产工时</li>';
				}
			}
			$counter=count($this->kclass->input['itemId']);
			for($i=1;$i<$counter+1;$i++){
				if($this->kclass->input['itemSelect'][$i]!=''){
					if($this->kclass->input['itemworkdate'][$i]==''){
						$e.='<li>请填写生产任务单[新]生产明细[ID:'.$i.']的 生产日</li>';
					}
					if($this->kclass->input['itemfinishdate'][$i]==''){
						$e.='<li>请填写生产任务单[新]生产明细[ID:'.$i.']的 完工日</li>';
					}
					if($this->kclass->input['itemdispatchnumber'][$i]==''){
						$e.='<li>请填写生产任务单[新]生产明细[ID:'.$i.']的 派工人数</li>';
					}
					if($this->kclass->input['itemworkhours'][$i]==''){
						$e.='<li>请填写生产任务单[新]生产明细[ID:'.$i.']的 工作时数</li>';
					}
				}
			}
		}
	}else{
		if($this->kclass->input['tasker']==''){
			$e.='<li>请填写生产任务单中的 任务员</li>';
		}
	}
}
if($e){
	$this->kclass->messager(array(
		'title' => '修改生产任务单',
		'text' => '您在修改生产任务单的过程中有以下错误：<ul>'.$e.'</ul>',
		'url' => 'javascript:history.back()',
		'sec' => 3
	));
}
if($this->kclass->input['step']=='item'){
	if($this->kclass->input['addtype']=='1'){
		$count=count($this->kclass->input['itemId']);
		for($m=1;$m<$count+1;$m++){
			if($this->kclass->input['itemWorkdate'][$m]!=''){
				$sd=explode('-',$this->kclass->input['itemWorkdate'][$m]);
				$workdate=mktime(0,0,0,$sd[1],$sd[2],$sd[0]);
			}else{
				$workdate=0;
			}
			if($this->kclass->input['itemFinishdate'][$m]!=''){
				$sd=explode('-',$this->kclass->input['itemFinishdate'][$m]);
				$finishdate=mktime(0,0,0,$sd[1],$sd[2],$sd[0]);
			}else{
				$finishdate=0;
			}
			if($this->kclass->input['itemKill'][$m]!=''){
				$this->kclass->DB->query("UPDATE `taskitem` SET killed=".TIMENOW.",killer='".$this->kclass->user['userid']."'  WHERE itemid='".$this->kclass->input['itemId'][$m]."'");
			}else{
				$this->kclass->DB->query("
					UPDATE `taskitem` SET
						`quantity`='".$this->kclass->input['itemQuantity'][$m]."',
						`workdate`='".$workdate."',
						`finishdate`='".$finishdate."',
						`dispatchnumber`='".$this->kclass->input['itemDispatchnumber'][$m]."',
						`workhours`='".$this->kclass->input['itemWorkhours'][$m]."',
						`remark`='".$this->kclass->input['itemRemark'][$m]."',
						`modified`='".TIMENOW."',
						`modifier`='".$this->kclass->user['userid']."'
					WHERE itemid='".$this->kclass->input['itemId'][$m]."'
				");
			}
		}
		for($j=$m;$j<$m+7;$j++){
			if($this->kclass->input['itemMaterialid'][$j]!=0){
				//insert item
				if($this->kclass->input['itemWorkdate'][$j]){
					$sd=explode('-', $this->kclass->input['itemWorkdate'][$j]);
					$workdate=mktime(0,0,0,$sd[1],$sd[2],$sd[0]);
				}else{
					$workdate=0;
				}
				if($this->kclass->input['itemFinishdate'][$j]){
					$sd=explode('-', $this->kclass->input['itemFinishdate'][$j]);
					$finishdate=mktime(0,0,0,$sd[1],$sd[2],$sd[0]);
				}else{
					$finishdate=0;
				}
				$wc=$this->kclass->DB->queryFirst("
					SELECT workcenterid
					FROM `workcenteritem`
					WHERE killed=0 AND materialid='".$this->kclass->input['itemMaterialid'][$j]."'
				");
				if($wc['workcenterid']=='')$wc['workcenterid']=0;
				$materialitemid=$this->kclass->iif($this->kclass->input['materialItemid'][$j]>0,$this->kclass->input['materialItemid'][$j],0);
				$this->kclass->DB->query("
					INSERT INTO taskitem (materialid,versionid,materialitemid,taskid,requirementid,workcenterid,statusid,workdate,finishdate,dispatchnumber,workhours,quantity,remark,created,creator)
					VALUES ('".$this->kclass->input['itemMaterialid'][$j]."','".$this->kclass->input['itemVersionid'][$j]."','".$materialitemid."','".$this->kclass->input['taskid']."',0,'".$wc['workcenterid']."',1,'".$workdate."','".$finishdate."','".$this->kclass->input['itemDispatchnumber'][$j]."','".$this->kclass->input['itemWorkhours'][$j]."','".$this->kclass->input['itemQuantity'][$j]."','".$this->kclass->input['itemRemark'][$j]."','".TIMENOW."','".$this->kclass->user['userid']."')
				");
			}
		}
	}else{
		$count=count($this->kclass->input['taskItemId']);
			for($m=1;$m<$count+1;$m++){
				if($this->kclass->input['itemKill'][$m]!='' AND $this->kclass->input['itemKill'][$m]=$this->kclass->input['taskItemId'][$m]){
					$this->kclass->DB->query("UPDATE `taskitem` SET killed=".TIMENOW.",killer='".$this->kclass->user['userid']."'  WHERE itemid='".$this->kclass->input['taskItemId'][$m]."'");
					$this->kclass->DB->query("UPDATE `mrp` SET ifChooser=0 WHERE mrpid='".$this->kclass->input['mrpid'][$m]."'");
				}else{
					if($this->kclass->input['olworkdate'][$m]){
						$sd=explode('-',$this->kclass->input['olworkdate'][$m]);
						$workdate=mktime(0,0,0,$sd[1],$sd[2],$sd[0]);
					}else{
						$workdate=0;
					}
					if($this->kclass->input['olfinishdate'][$m]){
						$sd=explode('-',$this->kclass->input['olfinishdate'][$m]);
						$finishdate=mktime(0,0,0,$sd[1],$sd[2],$sd[0]);
					}else{
						$finishdate=0;
					}
					$this->kclass->DB->query("
						UPDATE taskitem SET
							dispatchnumber='".$this->kclass->input['oldispatchnumber'][$m]."',
							workhours='".$this->kclass->input['olworkhours'][$m]."',
							workdate='".$workdate."',
							finishdate='".$finishdate."',
							remark='".$this->kclass->input['olremark'][$m]."'
						WHERE itemid='".$this->kclass->input['taskItemId'][$m]."'
					");
				}
			}
			$counter=count($this->kclass->input['itemId']);
			for($i=1;$i<$counter+1;$i++){
				if($this->kclass->input['itemSelect'][$i]!=''){
					if($this->kclass->input['itemworkdate'][$i]){
						$sd=explode('-',$this->kclass->input['itemworkdate'][$i]);
						$workdate=mktime(0,0,0,$sd[1],$sd[2],$sd[0]);
					}else{
						$workdate=0;
					}
					if($this->kclass->input['itemfinishdate'][$i]){
						$sd=explode('-',$this->kclass->input['itemfinishdate'][$i]);
						$finishdate=mktime(0,0,0,$sd[1],$sd[2],$sd[0]);
					}else{
						$finishdate=0;
					}
					$item=$this->kclass->DB->queryFirst("SELECT * FROM `mrp` WHERE killed=0 AND mrpid='".$this->kclass->input['itemSelect'][$i]."' LIMIT 0,1 ");
					$wc=$this->kclass->DB->queryFirst("SELECT workcenterid FROM `workcenteritem` WHERE killed=0 AND materialid='".$item['materialid']."'");
					if($wc['workcenterid']=='')$wc['workcenterid']=0;
					$this->kclass->DB->query("
						INSERT INTO taskitem
							(materialid,versionid,materialitemid,requirementid,workcenterid,mrpid,taskid,workdate,finishdate,dispatchnumber,workhours,quantity,remark,created,creator)
						VALUES
							('".$item['materialid']."','".$item['versionid']."','".$item['materialitemid']."','".$item['requirementid']."','".$wc['workcenterid']."','".$item['mrpid']."','".$this->kclass->input['taskid']."','".$workdate."','".$finishdate."','".$this->kclass->input['itemdispatchnumber'][$i]."','".$this->kclass->input['itemworkhours'][$i]."','".$item['netQuantity']."','".$this->kclass->input['itemremark'][$i]."','".TIMENOW."','".$this->kclass->user['userid']."')
					");
					$this->kclass->DB->query("UPDATE `mrp` SET ifChooser=1 WHERE mrpid='".$this->kclass->input['itemSelect'][$i]."'");
				}
			}
		}
		$this->kclass->messager(array(
			'title' => '修改生产任务申请单明细',
			'text' => '生产任务单 【<b>'.$task['taskno'].'</b>】 的明细已修改成功!返回新建生产任务申请单',
			'url' => '/s.php?module=task&action=view&taskid='.$this->kclass->input['taskid'],
			'sec' => 2
		));
	}else{
		// update task
		$this->kclass->DB->query("
			UPDATE task SET
				tasker='".$this->kclass->input['tasker']."',
				remark='".$this->kclass->input['remark']."',
				modified='".TIMENOW."',
				modifier='".$this->kclass->user['userid']."'
			WHERE taskid='".$this->kclass->input['taskid']."'
		");
		$this->kclass->messager(array(
			'title' => '修改生产任务单',
			'text' => '生产任务单 【<b>'.$this->kclass->input['taskno'].'</b>】 已修改成功!返回修改生产任务单',
			'url' => '/s.php?module=task&action=view&taskid='.$this->kclass->input['taskid'],
			'sec' => 2
		));
	}
}
		//指定完工任务单 更新待入待出
function appointComplete(){
	$item=$this->kclass->DB->queryFirst("
		SELECT ti.*,
			rt.requirementno,
			t.taskno,t.tasker,t.addtype,
			r.title AS routing,
			wc.title AS workcenter,
			mv.title AS version
		FROM taskitem AS ti
		LEFT JOIN task AS t ON (t.taskid=ti.taskid)
		LEFT JOIN requirement AS rt ON (rt.requirementid=ti.requirementid)
		LEFT JOIN routing AS r ON (r.routingid=ti.routingid)
		LEFT JOIN workcenter AS wc ON (wc.workcenterid=ti.workcenterid)
		LEFT JOIN `materialversion` AS mv ON (mv.versionid=ti.versionid)
		WHERE ti.killed=0 AND ti.statusid=1 AND ti.itemid='".$this->kclass->input['taskitemid']."'
		LIMIT 0,1
	");
	if(!$item){
		$e='<li>数据错误，很抱歉~</li>';
	}
	if(isset($e)){
		$this->kclass->messager(array(
			'title' => '指定完工生产任务明细',
			'text' => '指定完工生产任务明细时发生以下错误:<ul>'.$e.'</ul>',
			'url' => 'javascript:history.back()',
			'sec' => 3
		));
	}
	$materialInfo=$this->kclass->getMaterial(array('materialid'=>$item['materialid'],'itemid'=>$item['materialitemid']));
	$material='<a href="'.$materialInfo['url'].'">'.$materialInfo['no'].'</a>　'.$materialInfo['material'].'　'.$materialInfo['standard'];
	$addtype=$this->kclass->iif($item['addtype']==1,'生产计划','手工录入');
	$workdate=date('Y-m-d',$item['workdate']);
	$finishdate=date('Y-m-d',$item['finishdate']);
	$ifChooser=$this->kclass->iif($item['ifChooser']==1,'是','否');
	$quantity=number_format($item['quantity']);
	$finishquantity=number_format($item['finishquantity']);
	$itemtr='<tr class="even">
		<td width="150">变更净需求量：<span class="red bold">*</span></td><td><input type="text" name="itemQuantity" size="30"> 　<span class="small darkred">(请填写大于或等于 '.$item['finishquantity'].' 的数值)</span></td>
		<td width="100">变更原因：</td><td><input type="text" name="itemRemark" size="30"></td>
		</tr>';
$body=<<<EOF
<form method="post" action="/s.php?module=task&action=doappointComplete">
<input type="hidden" name="module" value="task">
<input type="hidden" name="action" value="doappointComplete">
<input type="hidden" name="taskitemid" value="{$this->kclass->input['taskitemid']}">
<table class="hundred">
<thead><tr><th colspan="10">生产任务明细</th></tr></thead>
<tbody>
<tr class="even">
<td width="100">需求计划编号：</td><td width="475"><a href="/s.php?module=requirement&action=view&requirementid={$item['requirementid']}">{$item['requirementno']}</a></td>
<td width="100">生产任务编号：</td><td width="150"><a href="/s.php?module=task&action=view&taskid={$item['taskid']}">{$item['taskno']}</a></td>
<td width="100">任务员：</td><td>{$item['tasker']}</td>
<td width="100">录入类型：</td><td>{$addtype}</td>
</tr>
<tr class="odd">
<td width="100">生产任务物资：</td><td>{$material}</td>
<td width="100">版本：</td><td>{$item['version']}</td>
<td width="100">工作中心：</td><td>{$item['workcenter']}</td>
<td width="100">工艺流程：</td><td>{$item['routing']}</td>
</tr>
<tr class="even">
<td width="100">净需求量：</td><td><span class="bold darkred">{$quantity}</span></td>
<td width="100">已完工量：</td><td><span class="bold darkred">{$finishquantity}</span></td>
<td width="100">投产日期：</td><td><span class="bold">{$workdate}</span></td>
<td width="100">完工日期：</td><td><span class="bold">{$finishdate}</span></td>
</tr>
<tr class="odd">
<td width="100">是否申请领料：</td><td><span class="bold darkred">{$ifChooser}</span></td>
<td width="100">派工人数：</td><td>{$item['dispatchnumber']}</td>
<td width="100">工作时数：</td><td>{$item['workhours']}</td>
<td width="100">备注：</td><td>{$item['remark']}</td>
</tr>
</tbody></table>
<table class="hundred">
<thead><tr><th colspan="10">指定完工</th></tr></thead>
<tbody>
{$itemtr}
<tr class="odd">
<td colspan=10 class="center"><input type="submit" value=" 提交 " /><input type="reset" value="重置" /></td>
</tr>
</tbody>
</table>
</form>
EOF;
		$this->kclass->tbline+=4;
		$this->kclass->page['title'].='指定完工生产任务明细';
		$this->kclass->page['onload'].='dc.tabhover();dc.tips();';
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - <a href="/s.php?module=task&action=view&taskid='.$item['taskid'].'">查看生产任务单</a> - 指定完工生产任务明细', 'right'=>'<a href="/s.php?module=task&action=list">返回列表</a>　|　<a href="/s.php?module=task&action=view&taskid='.$item['taskid'].'">查看生产任务单</a>','body'=>$body));
}
//
//
function doappointComplete(){
	if($this->kclass->input['requestMethod'] != 'post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			$ti=$this->kclass->DB->queryFirst("
				SELECT ti.*
				FROM taskitem AS ti
				WHERE ti.itemid='".$this->kclass->input['taskitemid']."'
				");
			$quantity=$this->kclass->input['itemQuantity'];
			if($quantity==''){
				$e.='<li>请填写变更单中的 变更数量。</li>';
			}
			if($quantity<$ti['finishquantity'] ){
				$e.='<li>变更单中的 变更数量不在规定范围之内。</li>';
			}
			if($ti['statusid']==4){
				$e.='<li>该生产任务明细已经生产完成。</li>';
			}
		}
		if($e){
			$this->kclass->messager(array(
				'title' => '指定完工生产任务明细',
				'text' => '您在指定完工生产任务明细的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
	if($quantity!=''){
		$decrease=$ti['quantity']-$quantity;
		$this->kclass->DB->query("
			INSERT INTO `producemodifylog`
				(`module`,`mid`,`oldquantity`,`newquantity`,`remark`,`created`,`creator`)
			VALUES
				('task','".$this->kclass->input['taskitemid']."','".$ti['quantity']."','".$quantity."','".$this->kclass->input['itemRemark']."','".TIMENOW."','".$this->kclass->user['userid']."')
		");
		//修改数量、待入
		$this->kclass->DB->query("
			UPDATE `taskitem` SET
				quantity='".$quantity."',
				modified='".TIMENOW."',
				modifier='".$this->kclass->user['userid']."'
			WHERE itemid='".$this->kclass->input['taskitemid']."'
		");
		$this->kclass->DB->query("
			UPDATE materialversion SET
				planInbound=planInbound-'".$decrease."'
			WHERE versionid='".$ti['versionid']."'
		");
		if($ti['ifChooser']==0){//未领料的BOM待出
			$child=$this->kclass->bomChildList(array('parentid'=>$ti['materialid'],'materialitemid'=>$ti['materialitemid'],'versionid'=>$ti['versionid'],'quantity'=>$decrease,'loopNum'=>1));
			//BOM预出
			if(count($child['bom'])>0){
				foreach($child['bom'] as $key => $val){
					if($val['originid']>1 AND $val['ifGeneral']==0){
						$this->kclass->DB->query("
							UPDATE materialversion SET
								planOutbound=planOutbound-'".$val['quantity']."'
							WHERE versionid='".$val['versionid']."'
						");
						}
					}
				}
			}
		}
		if($quantity==$ti['finishquantity']){
			$this->kclass->DB->query("
				UPDATE taskitem SET
					statusid=5
				WHERE itemid='".$ti['itemid']."'
			");
			$sid=$this->kclass->DB->queryFirst("
				SELECT statusid
				FROM  taskitem
				WHERE statusid=1 AND taskid=".$ti['taskid']."
			");
			if($sid==''){
				$this->kclass->DB->query("
					UPDATE task SET
						statusid=5
					WHERE taskid=".$ti['taskid']."
			");
			}
		}
		$this->kclass->messager(array(
			'title' => '指定完工生产任务明细',
			'text' => '指定完工生产任务明细成功！<br>已更新物资待入数量和未领料物资的BOM的待出数量！',
			'url' => '/s.php?module=task&action=view&taskid='.$ti['taskid'],
			'sec' => 2
		));
	}
	//查看任务单明细相关入库记录
	function viewInboundRecord(){
		$ti=$this->kclass->DB->queryFirst("
			SELECT ti.*,
				t.taskno,t.addtype,
				s.title AS status,
				wc.title AS workcenter,
				r.title AS routing,
				mv.title AS version,
				u.username AS creator,
				us.username AS modifier
			FROM `taskitem` AS ti
			LEFT JOIN task AS t ON (t.taskid=ti.taskid)
			LEFT JOIN workcenter AS wc ON (wc.workcenterid=ti.workcenterid)
			LEFT JOIN routing AS r ON (r.routingid=ti.routingid)
			LEFT JOIN mftstatus AS s ON (s.statusid=t.statusid)
			LEFT JOIN `materialversion` AS mv ON (mv.versionid=ti.versionid)
			LEFT JOIN user AS u ON (u.userid=ti.creator)
			LEFT JOIN user AS us ON (us.userid=ti.modifier)
			WHERE ti.itemid='".$this->kclass->input['taskitemid']."'
			LIMIT 0,1
		");
		if(!$ti){
			$e='<li>数据错误，很抱歉</li>';
		}
		if($e){
			$this->kclass->messager(array(
				'title' => '查看入库记录',
				'text' => '您在查看入库记录的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$ti['created']=date('Y-m-d',$ti['created']);
		$addtype=$this->kclass->iif($ti['addtype']==1,'手工方式','生产计划');
		$unfinished=$ti['quantity']-$ti['finishquantity'];
		if($unfinished<0)$unfinished=0;
		$taskMaterial=$this->kclass->getMaterial(array('materialid'=>$ti['materialid'],'itemid'=>$ti['materialitemid']));
		$tibody='<tbody>
<tr class="even">
<td width="100">任务单编号：</td><td width="200"><a href="/s.php?module=task&action=view&taskid='.$ti['taskid'].'">'.$ti['taskno'].'</a>　('.$ti['status'].')</td>
<td width="100">录入类型：</td><td width="200">'.$addtype.'</td>
<td width="100">生产物资：</td><td width="350"><a href="'.$taskMaterial['url'].'">'.$taskMaterial['no'].'　'.$taskMaterial['material'].'　'.$taskMaterial['standard'].' 　(版本：'.$ti['version'].')</td>
<td width="100">工艺流程：</td><td>'.$ti['routing'].'</td>
</tr>
<tr class="odd">
<td>工作中心：</td><td>'.$ti['workcenter'].'</td>
<td>需求数量：</td><td><span class="bold darkred">'.number_format($ti['quantity']).'</span></td>
<td>投产日期：</td><td><span class="bold">'.date('Y-m-d',$ti['workdate']).'</span></td>
<td>完工日期：</td><td><span class="bold">'.date('Y-m-d',$ti['finishdate']).'</span></td>
</tr>
<tr class="even">
<td>派工人数：</td><td>'.$ti['dispatchnumber'].' 人</td>
<td>工作时数：</td><td>'.$ti['workhours'].' 时</td>
<td>申请领料：</td><td>'.$this->kclass->iif($ti['ifChooser']>0,'是','否').'</td>
<td>备注：</td><td>'.$ti['remark'].'</td>
</tr>
<tr class="odd">
<td>已完工数：</td><td><span class="bold">'.number_format($ti['finishquantity']).'</span></td>
<td>未完工数：</td><td><span class="bold">'.number_format($unfinished).'</span></td>
<td></td><td></td>
<td></td><td></td>
</tr>
</tbody>';
		//入库记录
		$items=$this->kclass->DB->query("
			SELECT pi.*,
				i.inboundid,i.inboundno,
				t.title AS type,
				ii.itemid AS initemid,ii.dateTime,ii.batchid,ii.barcodeid,
				ba.batchno,ba.dateline,
				bc.barcode,
				mv.title AS version
			FROM preinbounditem AS pi
			LEFT JOIN `inbound` AS i ON (i.inboundid=pi.mid)
			LEFT JOIN `inorouttype` AS t ON (i.typeid=t.typeid)
			LEFT JOIN inbounditem AS ii ON (pi.itemid=ii.preitemid)
			LEFT JOIN batch AS ba ON (ba.batchid=ii.batchid)
			LEFT JOIN barcode AS bc ON (bc.barcodeid=ii.barcodeid)
			LEFT JOIN `materialversion` AS mv ON (mv.versionid=pi.versionid)
			WHERE pi.killed=0 AND i.killed=0 AND pi.module='inbound' AND pi.taskitemid='".$this->kclass->input['taskitemid']."'
			ORDER BY pi.created
		");
		if($this->kclass->DB->numRows()){
			$i=1;
			$itemList='<tr class=""><td width="25">ID</td><td width="80">入库单</td><td width="120">入库类型</td><td width="450">物资</td><td width="30">版本</td><td width="60">良品数</td><td width="60">不良品数</td><td width="80">批号时间</td><td width="90">备注</td><td width="80">已入库数量</td><td width="100">到货时间</td><td>批次</td><td>条码</td></tr>';
			while($item=$this->kclass->DB->fetchArray($items)){
				$item['dateline']=date('Y-m-d',$item['dateline']);
				if($item['arrivalTime'])$item['arrivalTime']=date('Y-m-d',$item['arrivalTime']);
				$in=$this->kclass->DB->queryFirst("SELECT SUM(quantity) AS count FROM inbounditem WHERE killed=0 AND itemid='".$item['initemid']."'");
				$inMaterial=$this->kclass->getMaterial(array('materialid'=>$item['materialid'],'itemid'=>$item['materialitemid']));

				$itemList.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').' small">
					<td>'.$i.'</td>
					<td><a href="/s.php?module=inbound&action=view&inboundid='.$item['inboundid'].'">'.$item['inboundno'].'</a></td>
					<td>'.$item['type'].'</td>
					<td><a href="'.$inMaterial['url'].'">'.$inMaterial['no'].'</a>　'.$inMaterial['material'].'　'.$inMaterial['standard'].'</td>
					<td>'.$item['version'].'</td>
					<td>'.$item['qualified'].$inMaterial['unit'].'</td>
					<td>'.$item['disqualified'].$inMaterial['unit'].'</td>
					<td>'.$item['dateline'].'</td>
					<td>'.$item['remark'].'</td>
					<td>'.$this->kclass->iif($in['count'],$in['count'],0).'</td>
					<td>'.$item['arrivalTime'].'</td>
					<td>'.$this->kclass->iif($item['batchno'],'<a href="/s.php?module=batch&action=view&batchid='.$item['batchid'].'">'.$item['batchno'].'</a>','').'</td>
					<td>'.$this->kclass->iif($item['barcode'],'<a href="/s.php?module=barcode&action=view&barcodeid='.$item['barcodeid'].'">'.$item['barcode'].'</a>','').'</td>
					</tr>';
				$i++;
			}
		}
		if(!$itemList)$itemList='<tr><td colspan="16" class="darkred center">暂无相关记录！</td></tr>';
$body=<<<EOF
<div class="title">生产任务明细入库记录</div>
<table class="hundred">
<thead><tr><th colspan="8">生产任务单明细</th></tr></thead>
{$tibody}
</table>
<table class="hundred">
<thead><tr><th colspan="14">入库申请记录<span class="right"><a href="/s.php?module=task&action=addInboundRecord&taskitemid={$ti['itemid']}">新增出库记录</a>　<a href="/s.php?module=task&action=addInbound&workcenterid={$ti['workcenterid']}">新增出库单</a></span></th></tr></thead>
<tbody>
{$itemList}
</tbody>
</table>
EOF;
		$this->kclass->tbline+=4;
		$this->kclass->page['title'].='查看入库记录';
		$this->kclass->page['onload'].='dc.tabhover();dc.tips();';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - <a href="/s.php?module=task&action=list">任务单列表</a> - 查看入库记录', 'right' => '<span class = "small">操作：</span> <a href="/s.php?module=workcenter&action=list">返回工作中心</a>　','body' => $body));

	}
	//
	//查看任务单明细相关出库记录
	function viewOutboundRecord(){
		$ti=$this->kclass->DB->queryFirst("
			SELECT ti.*,
				t.taskno,t.addtype,
				s.title AS status,
				wc.title AS workcenter,
				r.title AS routing,
				mv.title AS version,
				u.username AS creator,
				us.username AS modifier
			FROM `taskitem` AS ti
			LEFT JOIN task AS t ON (t.taskid=ti.taskid)
			LEFT JOIN workcenter AS wc ON (wc.workcenterid=ti.workcenterid)
			LEFT JOIN routing AS r ON (r.routingid=ti.routingid)
			LEFT JOIN mftstatus AS s ON (s.statusid=t.statusid)
			LEFT JOIN `materialversion` AS mv ON (mv.versionid=ti.versionid)
			LEFT JOIN user AS u ON (u.userid=ti.creator)
			LEFT JOIN user AS us ON (us.userid=ti.modifier)
			WHERE ti.itemid='".$this->kclass->input['taskitemid']."'
			LIMIT 0,1
		");
		if(!$ti){
			$e='<li>数据错误，很抱歉</li>';
		}
		if($e){
			$this->kclass->messager(array(
				'title' => '查看出库记录',
				'text' => '您在查看出库记录的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$ti['created']=date('Y-m-d',$ti['created']);
		$addtype=$this->kclass->iif($ti['addtype']==1,'手工方式','生产计划');
		$unfinished=$ti['quantity']-$ti['finishquantity'];
		if($unfinished<0)$unfinished=0;
		$taskMaterial=$this->kclass->getMaterial(array('materialid'=>$ti['materialid'],'itemid'=>$ti['materialitemid']));
		$tibody='<tbody>
<tr class="even">
<td width="100">任务单编号：</td><td width="200"><a href="/s.php?module=task&action=view&taskid='.$ti['taskid'].'">'.$ti['taskno'].'</a>　('.$ti['status'].')</td>
<td width="100">录入类型：</td><td width="200">'.$addtype.'</td>
<td width="100">生产物资：</td><td width="350"><a href="'.$taskMaterial['url'].'">'.$taskMaterial['no'].'</a>　'.$taskMaterial['material'].'　'.$taskMaterial['standard'].'　(版本：'.$ti['version'].')</td>
<td width="100">工艺流程：</td><td>'.$ti['routing'].'</td>
</tr>
<tr class="odd">
<td>工作中心：</td><td>'.$ti['workcenter'].'</td>
<td>需求数量：</td><td><span class="bold darkred">'.number_format($ti['quantity']).'</span></td>
<td>投产日期：</td><td><span class="bold">'.date('Y-m-d',$ti['workdate']).'</span></td>
<td>完工日期：</td><td><span class="bold">'.date('Y-m-d',$ti['finishdate']).'</span></td>
</tr>
<tr class="even">
<td>派工人数：</td><td>'.$ti['dispatchnumber'].' 人</td>
<td>工作时数：</td><td>'.$ti['workhours'].' 时</td>
<td>申请领料：</td><td>'.$this->kclass->iif($ti['ifChooser']>0,'是','否').'</td>
<td>备注：</td><td>'.$ti['remark'].'</td>
</tr>
<tr class="odd">
<td>已完工数：</td><td><span class="bold">'.number_format($ti['finishquantity']).'</span></td>
<td>未完工数：</td><td><span class="bold">'.number_format($unfinished).'</span></td>
<td></td><td></td>
<td></td><td></td>
</tr>
</tbody>';
		//领料bom
		$child=$this->kclass->bomChildList(array('parentid'=>$ti['materialid'],'materialitemid'=>$ti['materialitemid'],'versionid'=>$ti['versionid'],'loopNum'=>1));
		if(empty($child['bom'])){
			$k=1;
			foreach($child['bom'] as $$val){
				$bomtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').' small '.$this->kclass->iif($val['ifGeneral']==0,$this->kclass->iif($val['quantity']>$val['qualified'],'bgYellow',''),'').'">
					<td>'.$k.'</td>
					<td><a href="'.$val['url'].'">'.$val['no'].'</a>　'.$val['material'].'　'.$val['standard'].'</td>
					<td>'.$val['version'].'</td>
					<td>'.$val['origin'].'</td>
					<td>'.$this->kclass->iif($val['ifGeneral']==0,$val['quantity'],'通用辅料').'</td>
					<td>'.$val['qualified'].'</td><td>'.$val['planInbound'].'</td><td>'.$val['planOutbound'].'</td>
					<td>'.$val['min'].'</td>
				</tr>';
				$k++;
			}
		}else{
			$bomtr.='<tr><td colspan="16" class="darkred center">物资 ['.$ti['material'].'] BOM(子) 不存在！</td></tr>';
		}
		unset($child);
	//
	//出库记录
	$items=$this->kclass->DB->query("
		SELECT pi.*,
			o.outboundid,o.outboundno,
			t.title AS type,
			oi.actualTime,oi.batchid,oi.barcodeid,
			ba.batchno,
			bc.barcode,
			mv.title AS version,mv.qualified AS store
		FROM preoutbounditem AS pi
		LEFT JOIN `outbound` AS o ON (o.outboundid=pi.mid)
		LEFT JOIN `inorouttype` AS t ON (t.typeid=o.typeid)
		LEFT JOIN outbounditem AS oi ON (pi.itemid=oi.preitemid)
		LEFT JOIN batch AS ba ON (ba.batchid=oi.batchid)
		LEFT JOIN barcode AS bc ON (bc.barcodeid=oi.barcodeid)
		LEFT JOIN `materialversion` AS mv ON (mv.versionid=pi.versionid)
		WHERE pi.killed=0 AND o.killed=0 AND pi.module='outbound' AND pi.taskitemid='".$this->kclass->input['taskitemid']."'
		ORDER BY pi.created
	");
	if($this->kclass->DB->numRows()){
		$i=1;
		$itemList='<tr class=""><td width="25">ID</td><td width="80">出库单</td><td width="120">出库类型</td><td width="450">物资</td><td width="30">版本</td><td width="60">库存数量</td><td width="80">数量/单位</td><td width="90">申请出库时间</td><td width="80">已出库数量</td><td width="100">备注</td><td width="100">实际出库时间</td><td>批次</td><td>条码</td></tr>';
		while($item=$this->kclass->DB->fetchArray($items)){
			$item['outTime']=date('Y-m-d',$item['outTime']);
			if($item['actualTime'])$item['actualTime']=date('Y-m-d',$item['actualTime']);
			if($item['ifPerBarcode']==1){
				$counter=$this->kclass->DB->queryFirst("SELECT COUNT(*) AS count FROM outbounditem WHERE materialid='".$item['materialid']."' AND outboundid='".$this->kclass->input['mid']."'");
			}elseif($item['ifPerBarcode']==0){
				$counter=$this->kclass->DB->queryFirst("SELECT outQuantity AS count FROM outbounditem WHERE materialid='".$item['materialid']."' AND outboundid='".$this->kclass->input['mid']."' AND preitemid='".$item['itemid']."'");
			}
			$materialInfo=$this->kclass->getMaterial(array('materialid'=>$item['materialid'],'itemid'=>$item['materialitemid']));

			$itemList.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').' small">
				<td>'.$i.'</td>
				<td><a href="/s.php?module=outbound&action=view&outboundid='.$item['outboundid'].'">'.$item['outboundno'].'</a></td>
				<td>'.$item['type'].'</td>
				<td><a herf="'.$materialInfo['url'].'">'.$materialInfo['no'].'　'.$materialInfo['material'].'　'.$materialInfo['standard'].'</td>
				<td>'.$item['version'].'</td>
				<td>'.$item['store'].$materialInfo['unit'].'</td>
				<td>'.$item['quantity'].$materialInfo['unit'].'</td>
				<td>'.$item['outTime'].'</td>
				<td>'.$this->kclass->iif($counter['count'],$counter['count'],0).'</td>
				<td>'.$item['remark'].'</td>
				<td>'.$item['actualTime'].'</td>
				<td>'.$this->kclass->iif($item['batchno'],'<a href="/s.php?module=batch&action=view&batchid='.$item['batchid'].'">'.$item['batchno'].'</a>','').'</td>
				<td>'.$this->kclass->iif($item['barcode'],'<a href="/s.php?module=barcode&action=view&barcodeid='.$item['barcodeid'].'">'.$item['barcode'].'</a>','').'</td>
				</tr>';
			$i++;
		}
	}
	if(!$itemList)$itemList='<tr><td colspan="16" class="darkred center">暂无相关记录！</td></tr>';
$body=<<<EOF
<div class="title">生产任务明细出库记录</div>
<table class="hundred">
<thead><tr><th colspan="8">生产任务单明细</th></tr></thead>
{$tibody}
</table>
<table class="hundred">
<thead><tr><th colspan="12">物料需求明细<span class="right"><a href="/s.php?module=task&action=addOutboundRecord&taskitemid={$ti['itemid']}">新增出库记录</a>　<a href="/s.php?module=task&action=addOutbound&workcenterid={$ti['workcenterid']}">新增出库单</a></span></th></tr></thead>
<tbody><tr class=""><td width="30">ID</td><td>物资</td><td>版本</td><td>来源</td><td>需求数量</td><td>良品库存</td><td>待入库存</td><td>待出库存</td><td>安全库存</td></tr>
{$bomtr}
</tbody>
</table>
<table class="hundred">
<thead><tr><th colspan="14">出库申请记录</th></tr></thead>
<tbody>
{$itemList}
</tbody>
</table>
EOF;
	$this->kclass->tbline+=4;
	$this->kclass->page['title'].='查看出库记录';
	$this->kclass->page['onload'].='dc.tabhover();dc.tips();';
	$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - <a href="/s.php?module=task&action=list">任务单列表</a> - 查看出库记录', 'right' => '<span class = "small">操作：</span> <a href="/s.php?module=workcenter&action=list">返回工作中心</a>　','body' => $body));

	}
	//
	//已作废 按需求变动保留开发
	function addOutboundRecord(){
	}
	//
	function insertOutboundRecord(){
	}
	//
	function addInboundRecord(){
	}
	//
	function insertInboundRecord(){
	}
	//对接入库单接口 提交数据到入库模块
	function addInbound(){
		if($this->kclass->input['step']==''){
			$workcenterid=$this->kclass->input['workcenterid'];
			if(!$workcenterid){
				$this->kclass->messager(array(
					'title' => '新建入库申请单',
					'text' => '您在新建入库申请单的过程中有以下错误：<ul>请从正确的页面提交！</ul>',
					'url' => 'javascript:history.back()',
					'sec' => 3
				));
			}
			$type=$this->kclass->chooserInorouttype(array('name'=>'typeid','width'=>200,'module'=>'inbound','condition'=>' AND ifTask=1'));
			$before5day = TIMENOW - 432000;
			$itemtr='<tr class="odd"><td colspan="6">选择从 <input type="text" id="taskTime" name="taskTime" size="10" value="'.date('Y-m-d',$before5day).'"> 起到 <span class="darkred">今天</span> 的任务单 <span class="gray">(默认为5天前)</span></td></tr>';
			$id.='#taskTime';
			$body=<<<EOF
<form action="/s.php?module=task&action=addInbound&step=1" method="post">
<input type="hidden" name="module" value="task" />
<input type="hidden" name="action" value="addInbound" />
<input type="hidden" name="workcenterid" value="{$workcenterid}" />
<input type="hidden" name="step" value="1" />
<table>
<thead>
<tr>
<th colspan="6">选择入库类型:</th>
</tr>
</thead>
<tbody>
<tr class="odd">
<td colspan="6">选择入库类型：<span class="red bold">*</span>{$type}</td>
</tr>
</tbody>
<thead>
<tr>
<th colspan="6">选择任务时间:</th>
</tr>
</thead>
<tbody>
{$itemtr}
<tr class="odd" nohover>
<td colspan="6" class="center" nohover>
<input type="submit" id="submitButton" value="  下一步  " accesskey="s">
<input type="reset" value="  复位  ">
</td>
</tr>
</tbody>
</table>
</form>
EOF;
			$this->kclass->tbline+=4;
			$this->kclass->page['title'].='选择入库类型';
			$this->kclass->page['onload'].='dc.tabhover();$(\'#date,'.$id.'\').datepicker();';
			$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - 新建入库申请单', 'right'=>'<a href="/s.php?module=task&action=list">返回列表</a>','body'=>$body));
		}elseif($this->kclass->input['step']==1){
			if($this->kclass->input['taskTime']==''){
				$e.='<li>请选择任务时间</li>';
				$this->kclass->messager(array(
					'title' => '新建入库申请单',
					'text' => '您在新建入库申请单的过程中有以下错误：<ul>'.$e.'</ul>',
					'url' => 'javascript:history.back()',
					'sec' => 3
				));
			}
			$sd=explode('-',$this->kclass->input['taskTime']);
			$taskTime=mktime(0,0,0,$sd[1],$sd[2],$sd[0]);
			$typeid=$this->kclass->input['typeid'];
			$workcenterid=$this->kclass->input['workcenterid'];
			$items=$this->kclass->DB->query("
				SELECT ti.*,
					t.taskno,
					s.title AS status,
					mv.title AS version,
					wc.title AS workcenter,
					u.username AS creator,
					us.username AS modifier
				FROM `taskitem` AS ti
				LEFT JOIN mftstatus AS s ON (s.statusid=ti.statusid)
				LEFT JOIN `materialversion` AS mv ON (mv.versionid=ti.versionid)
				LEFT JOIN workcenter AS wc ON (wc.workcenterid=ti.workcenterid)
				LEFT JOIN task AS t ON (t.taskid=ti.taskid)
				LEFT JOIN user AS u ON (u.userid=ti.creator)
				LEFT JOIN user AS us ON (us.userid=ti.modifier)
				WHERE ti.killed=0 AND finishquantity>0 AND ti.workcenterid=".$workcenterid." AND t.created>".$taskTime."
				ORDER BY ti.itemid DESC, ti.modified DESC, ti.created DESC
			");
			if($this->kclass->DB->numRows()){
				$i=1;
				$itemtr.='<tr><td width="15">ID</td><td>任务单</td><td>工作中心</td><td width="200">物资</td><td>版本</td><td width="80">投产日期</td><td>总需求数量</td><td>已完工数量</td><td>选</td></tr>';
				while($item=$this->kclass->DB->fetchArray($items)){
					$materialInfo=$this->kclass->getMaterial(array('materialid'=>$item['materialid'],'itemid'=>$item['materialitemid']));
					$itemtr.='<tr class="'.$this->kclass->rotateLine(). ' small">
						<td><input type="hidden" name="itemId['.$i.']" value="'.$item['itemid'].'">'.$i.'</td>
						<td><a href="/s.php?module=task&action=view&taskid='.$item['taskid'].'">'.$item['taskno'].'</a></td>
						<td>'.$item['workcenter'].'</td>
						<td width="350"><span class="small"><a href="'.$materialInfo['url'].'">'.$materialInfo['no'].'</a>　'.$materialInfo['material'].'　'.$materialInfo['standard'].'</span></td>
						<td>'.$item['version'].'</td>
						<td>'.date('Y-m-d',$item['workdate']).'</td>
						<td>'.number_format($item['quantity']).' '.$item['unit'].'</td>
						<td>'.$item['finishquantity'].' '.$item['unit'].'</td>
						<td>'.$this->kclass->iif($typeid==4 OR $typeid==6,'<input type="radio" name="itemSelect" value="'.$item['itemid'].'"/>','<input type="checkbox" name="itemSelect['.$i.']" value="'.$item['itemid'].'"/>').'</td>
					</tr>';
					$i++;
				}
			}else{
				$itemtr='<tr><td class="red">当前生产记录明细为空！</td></tr>';
			}
			$body=<<<EOF
<form action="/s.php?module=inbound&action=add" name="inbound" method="post">
<input type="hidden" name="module" value="inbound" />
<input type="hidden" name="action" value="add" />
<input type="hidden" name="workcenterid" value="{$workcenterid}" />
<input type="hidden" name="typeid" value="{$typeid}" />
<input type="hidden" name="step" value="2" />
<table><thead><tr><th colspan=16>选择生产记录明细</th></tr></thead>
{$itemtr}
<tr class="center">
<td colspan=16><input type="submit" value="提交"><input type="reset" value="重置"></td>
</tr>
</tbody></table>
</form>
EOF;
			$this->kclass->tbline+=4;
			$this->kclass->page['title'].='- 选择生产记录明细';
			$this->kclass->page['onload'].='dc.tabhover();dc.tips();';
			$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - 新建入库申请单', 'right'=>'<a href="/s.php?module=task&action=list">返回列表</a>','body'=>$body));
		}
	}
//对接出库单接口 提交数据到出库模块
	function addOutbound(){
		if($this->kclass->input['step']==''){
			$workcenterid=$this->kclass->input['workcenterid'];
			if(!$workcenterid){
				$this->kclass->messager(array(
					'title' => '新建出库申请单',
					'text' => '您在新建出库申请单的过程中有以下错误：<ul>请从正确的页面提交！</ul>',
					'url' => 'javascript:history.back()',
					'sec' => 3
				));
			}
			$type=$this->kclass->chooserInorouttype(array('name'=>'typeid','width'=>200,'module'=>'outbound','condition'=>' AND ifTask=1'));
			$body=<<<EOF
<form action="/s.php?module=task&action=addOutbound&step=1" method="post">
<input type="hidden" name="module" value="task" />
<input type="hidden" name="action" value="addOutbound" />
<input type="hidden" name="workcenterid" value="{$workcenterid}" />
<input type="hidden" name="step" value="1" />
<table>
<thead>
<tr>
<th colspan="2">新建出库申请：请选择出库类型</th>
</tr>
</thead>
<tbody>
<tr class="odd">
<td>选择出库类型：<span class="red bold">*</span></td>
<td>{$type}</td>
</tr>
<tr class="odd" nohover>
<td colspan="2" class="center" nohover>
<input type="submit" id="submitButton" value="  下一步  " accesskey="s">
<input type="reset" value="  复位  ">
</td>
</tr>
</tbody>
</table>
</form>
EOF;
			$this->kclass->tbline+=4;
			$this->kclass->page['title'].='选择出库类型';
			$this->kclass->page['onload'].='dc.tabhover();';
			$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - 新建出库申请单', 'right'=>'<a href="/s.php?module=task&action=list">返回列表</a>','body'=>$body));
		}elseif($this->kclass->input['step']==1){
			$typeid=$this->kclass->input['typeid'];
			$con=$this->kclass->iif($typeid==11 OR $typeid==12,'AND ti.ifChooser=1','AND ti.ifChooser=0');
			$workcenterid=$this->kclass->input['workcenterid'];
			$items=$this->kclass->DB->query("
				SELECT ti.*,
					t.taskno,
					s.title AS status,
					mv.title AS version,
					wc.title AS workcenter,
					u.username AS creator,
					us.username AS modifier
				FROM `taskitem` AS ti
				LEFT JOIN mftstatus AS s ON (s.statusid=ti.statusid)
				LEFT JOIN `materialversion` AS mv ON (mv.versionid=ti.versionid)
				LEFT JOIN workcenter AS wc ON (wc.workcenterid=ti.workcenterid)
				LEFT JOIN task AS t ON (t.taskid=ti.taskid)
				LEFT JOIN user AS u ON (u.userid=ti.creator)
				LEFT JOIN user AS us ON (us.userid=ti.modifier)
				WHERE ti.killed=0 ".$con." AND t.statusid=2 AND ti.workcenterid=".$workcenterid."
				ORDER BY ti.modified DESC,ti.created DESC,ti.itemid ASC
			");
			if($this->kclass->DB->numRows()){
				$i=1;
				$itemtr.='<tr><td width="15">ID</td><td>生产批次</td><td>任务单</td><td>工作中心</td><td width="200">物资</td><td>版本</td><td width="80">投产日期</td><td>总需求数量</td><td>已完工数量</td><td>选</td></tr>';
				while($item=$this->kclass->DB->fetchArray($items)){
					$materialInfo=$this->kclass->getMaterial(array('materialid'=>$item['materialid'],'itemid'=>$item['materialitemid']));
					$itemtr.='<tr class="'.$this->kclass->rotateLine(). ' small">
						<td><input type="hidden" name="itemId['.$i.']" value="'.$item['itemid'].'">'.$i.'</td>
						<td>'.$item['batch'].'</td>
						<td><a href="/s.php?module=task&action=view&taskid='.$item['taskid'].'">'.$item['taskno'].'</a></td>
						<td>'.$item['workcenter'].'</td>
						<td width="350"><span class="small"><a href="'.$materialInfo['url'].'">'.$materialInfo['no'].'</a> '.$materialInfo['material'].' '.$materialInfo['standard'].'</span></td>
						<td>'.$item['version'].'</td>
						<td>'.date('Y-m-d',$item['workdate']).'</td>
						<td>'.number_format($item['quantity']).' '.$materialInfo['unit'].'</td>
						<td>'.$item['finishquantity'].' '.$materialInfo['unit'].'</td>
						<td>'.$this->kclass->iif($typeid==9 OR $typeid==11,'<input type="checkbox" name="itemSelect['.$i.']" value="'.$item['itemid'].'"/>','<input type="radio" name="itemSelect" value="'.$item['itemid'].'"/>').'</td>
					</tr>';
					$i++;
				}
			}else{
				$itemtr='<tr><td class="red">当前可申请出库的生产任务单明细为空！</td></tr>';
			}
			$body=<<<EOF
<form action="/s.php?module=outbound&action=add" name="outbound" method="post">
<input type="hidden" name="module" value="outbound" />
<input type="hidden" name="action" value="add" />
<input type="hidden" name="workcenterid" value="{$workcenterid}" />
<input type="hidden" name="typeid" value="{$typeid}" />
<table><thead><tr><th colspan=16>请选择生产任务单明细</th></tr></thead>
{$itemtr}
<tr class="center">
<td colspan=16><input type="submit" value="提交"><input type="reset" value="重置"></td>
</tr>
</tbody></table>
</form>
EOF;
			$this->kclass->tbline+=4;
			$this->kclass->page['title'].='新建出库申请单';
			$this->kclass->page['onload'].='dc.tabhover();dc.tips();';
			$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - 新建出库申请单', 'right'=>'<a href="/s.php?module=task&action=list">返回列表</a>','body'=>$body));
		}
	}
//
//物资关联工作中心
function addWorkcenter(){
	$itemid=$this->kclass->input['itemid'];
	$item=$this->kclass->DB->queryFirst("
		SELECT ti.materialid,ti.versionid,ti.materialitemid,ti.taskid,
			mv.title AS version
		FROM taskitem AS ti
		LEFT JOIN `materialversion` AS mv ON (mv.versionid=ti.versionid)
		WHERE ti.killed=0 AND ti.itemid='".$itemid."'
		LIMIT 0,1
	");
	$materialInfo=$this->kclass->getMaterial(array('materialid'=>$item['materialid'],'itemid'=>$item['materialitemid']));
	$workcenter=$this->kclass->chooserWorkcenter(array('name'=>'workcenterid','width'=>300,'hasBlank'=>0,'selectedid'=>1));
	$body=<<<EOF
<form action="/s.php?module=task&action=insertWorkcenter" method="post">
<input type="hidden" name="module" value="task">
<input type="hidden" name="action" value="insertWorkcenter">
<input type="hidden" name="itemid" value="{$itemid}">
<input type="hidden" name="taskid" value="{$item['taskid']}">
<input type="hidden" name="materialid" value="{$item['materialid']}">
<input type="hidden" name="versionid" value="{$item['versionid']}">
<input type="hidden" name="materialitemid" value="{$item['materialitemid']}">
<input type="hidden" name="materialno" value="{$materialInfo['no']}">
<table>
<thead><tr><th colspan=2>物资关联工作中心</th></tr></thead>
<tbody>
<tr><td>物资</td><td><a href="{$materialInfo['url']}">{$materialInfo['no']}</a>　{$materialInfo['material']}　{$materialInfo['standard']}</td></tr>
<tr><td>选择工作中心</td><td>{$workcenter}</td></tr>
<tr class="center"><td colspan=2><input type="submit" value=" 提交 "><input type="reset" value=" 重置 "></td></tr>
</tbody>
</table>
EOF;
	$this->kclass->page['onload']='dc.tabhover();dc.tips();$(\'#date,'.$id.'\').datepicker()';
	$this->kclass->page['title'].=' - 关联工作中心';
	$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl . ' - 关联工作中心','right'=>'<a href="/s.php?module=task">返回列表</a>','body'=>$body));
}
//
function insertWorkcenter(){
	if($this->kclass->input['requestMethod'] != 'post'){
		$e='<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
	}else{
		if($this->kclass->input['workcenterid']==''){
			$e.='<li>请选择工作中心</li>';
		}
	}
	if($e){
		$this->kclass->messager(array(
			'title' => '关联工作中心',
			'text' => '您在关联工作中心的过程中有以下错误：<ul>'.$e.'</ul>',
			'url' => 'javascript:history.back()',
			'sec' => 3
		));
	}

	// update task
	$this->kclass->DB->query("
		UPDATE taskitem SET
			workcenterid='".$this->kclass->input['workcenterid']."'
		WHERE killed=0 AND versionid='".$this->kclass->input['versionid']."'
	");
	// insert workcenter
	$this->kclass->DB->query("
		INSERT INTO workcenteritem (workcenterid,materialid,versionid,materialitemid,creator,created)
		VALUES ('".$this->kclass->input['workcenterid']."','".$this->kclass->input['materialid']."','".$this->kclass->input['versionid']."','".$this->kclass->input['materialitemid']."','".$this->kclass->user['userid']."','".TIMENOW."')
	");
	$this->kclass->messager(array(
		'title' => '关联工作中心',
		'text' => '物资 [<b>'.$this->kclass->input['materialno'].'</b>] 关联工作中心成功!',
		'url' => '/s.php?module=task&action=view&taskid='.$this->kclass->input['taskid'],
		'sec' => 2
	));
}
	//生产任务增加工艺路线 后续开发可将工艺路线直接关联相应物资 减少这一步操作 --gaowenfei 2013/11/25 9:46
function addRouting(){
	$workcenterid=$this->kclass->input['workcenterid'];
	if($workcenterid==0){
		$e.='<li>请先将物资关联工作中心。</li>';
	}
	if($e){
		$this->kclass->messager(array(
			'title' => '选择工艺流程',
			'text' => '您在选择工艺流程的过程中有以下错误：<ul>'.$e.'</ul>',
			'url' => 'javascript:history.back()',
			'sec' => 3
		));
	}
	$itemid=$this->kclass->input['itemid'];
	$wc=$this->kclass->DB->queryFirst("
		SELECT title
		FROM workcenter
		WHERE killed=0 AND workcenterid='".$workcenterid."'
		LIMIT 0,1
	");
	$routing=$this->kclass->chooserRouting(array('name'=>'routingid','workcenterid'=>$workcenterid,'width'=>300,'hasBlank'=>0,'selectedid'=>1));
	$body=<<<EOF
<form action="/s.php?module=task&action=insertRouting" method="post">
<input type="hidden" name="module" value="task">
<input type="hidden" name="action" value="insertRouting">
<input type="hidden" name="workcenterid" value="{$workcenterid}">
<input type="hidden" name="itemid" value="{$itemid}">
<table>
<thead><tr><th colspan=2>选择工作中心工艺流程</th></tr></thead>
<tbody>
<tr><td>工作中心</td><td>{$wc['title']}</td></tr>
<tr><td>选择工艺流程</td><td>{$routing}</td></tr>
<tr class="center"><td colspan=2><input type="submit" value=" 提交 "><input type="reset" value=" 重置 "></td></tr>
</tbody>
</table>
EOF;
	$this->kclass->page['onload']='dc.tabhover();$(\'#date,'.$id.'\').datepicker()';
	$this->kclass->page['title'].=' - 选择工艺流程';
	$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl . ' - 选择工艺流程','right'=>'<a href="/s.php?module=task">返回列表</a>','body'=>$body));
}
//
//
function insertRouting(){
	if($this->kclass->input['requestMethod'] != 'post'){
		$e='<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
	}else{
		if($this->kclass->input['routingid']==''){
			$e.='<li>请选择工艺流程</li>';
		}
	}
	if($e){
		$this->kclass->messager(array(
			'title' => '选择工艺流程',
			'text' => '您在选择工艺流程的过程中有以下错误：<ul>'.$e.'</ul>',
			'url' => 'javascript:history.back()',
			'sec' => 3
		));
	}
	$itemid=$this->kclass->input['itemid'];
	$workcenterid=$this->kclass->input['workcenterid'];
	$wc=$this->kclass->DB->queryFirst("
		SELECT title
		FROM workcenter
		WHERE killed=0 AND workcenterid='".$workcenterid."'
		LIMIT 0,1
	");
	$task=$this->kclass->DB->queryFirst("
		SELECT taskid
		FROM taskitem
		WHERE killed=0 AND itemid='".$itemid."'
		LIMIT 0,1
	");
	// update task
	$this->kclass->DB->query("
		UPDATE taskitem SET
			routingid='".$this->kclass->input['routingid']."'
		WHERE itemid='".$this->kclass->input['itemid']."'
	");
	$this->kclass->messager(array(
		'title' => '选择工艺流程',
		'text' => '工作中心【<b>'.$wc['title'].'</b>】 成功选择工艺流程!',
		'url' => '/s.php?module=task&action=view&taskid='.$task['taskid'],
		'sec' => 2
	));
}
//修改工艺路线
function updateRouting(){
	$workcenterid=$this->kclass->input['workcenterid'];
	if($workcenterid==0){
		$e.='<li>请先将物资关联工作中心。</li>';
	}
	if($e){
		$this->kclass->messager(array(
			'title' => '修改工艺流程',
			'text' => '您在选择工艺流程的过程中有以下错误：<ul>'.$e.'</ul>',
			'url' => 'javascript:history.back()',
			'sec' => 3
		));
	}
	$itemid=$this->kclass->input['itemid'];
	$wc=$this->kclass->DB->queryFirst("
		SELECT title
		FROM workcenter
		WHERE killed=0 AND workcenterid='".$workcenterid."'
		LIMIT 0,1
	");
	$item=$this->kclass->DB->queryFirst("
		SELECT routingid
		FROM taskitem
		WHERE killed=0 AND itemid='".$itemid."'
		LIMIT 0,1
	");
	$routing=$this->kclass->chooserRouting(array('name'=>'routingid','workcenterid'=>$workcenterid,'width'=>300,'hasBlank'=>0,'selectedid'=>$item['routingid']));
	$body=<<<EOF
<form action="/s.php?module=task&action=doupdateRouting" method="post">
<input type="hidden" name="module" value="task">
<input type="hidden" name="action" value="doupdateRouting">
<input type="hidden" name="workcenterid" value="{$workcenterid}">
<input type="hidden" name="itemid" value="{$itemid}">
<table>
<thead><tr><th colspan=2>选择工作中心工艺流程</th></tr></thead>
<tbody>
<tr><td>工作中心</td><td>{$wc['title']}</td></tr>
<tr><td>选择工艺流程</td><td>{$routing}</td></tr>
<tr class="center"><td colspan=2><input type="submit" value=" 提交 "><input type="reset" value=" 重置 "></td></tr>
</tbody>
</table>
EOF;
	$this->kclass->page['onload']='dc.tabhover();$(\'#date,'.$id.'\').datepicker()';
	$this->kclass->page['title'].=' - 修改工艺流程';
	$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl . ' - 修改工艺流程','right'=>'<a href="/s.php?module=task">返回列表</a>','body'=>$body));
}
//
function doupdateRouting(){
	if($this->kclass->input['requestMethod'] != 'post'){
		$e='<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
	}else{
		if($this->kclass->input['routingid']==''){
			$e.='<li>请选择工艺流程</li>';
		}
	}
	if($e){
		$this->kclass->messager(array(
			'title' => '修改工艺流程',
			'text' => '您在选择工艺流程的过程中有以下错误：<ul>'.$e.'</ul>',
			'url' => 'javascript:history.back()',
			'sec' => 3
		));
	}
	$itemid=$this->kclass->input['itemid'];
	$workcenterid=$this->kclass->input['workcenterid'];
	$wc=$this->kclass->DB->queryFirst("
		SELECT title
		FROM workcenter
		WHERE killed=0 AND workcenterid='".$workcenterid."'
		LIMIT 0,1
	");
	$task=$this->kclass->DB->queryFirst("
		SELECT taskid
		FROM taskitem
		WHERE killed=0 AND itemid='".$itemid."'
		LIMIT 0,1
	");
	// update task
	$this->kclass->DB->query("
		UPDATE taskitem SET
			routingid='".$this->kclass->input['routingid']."'
		WHERE itemid='".$this->kclass->input['itemid']."'
	");
	$this->kclass->messager(array(
		'title' => '修改工艺流程',
		'text' => '工作中心【<b>'.$wc['title'].'</b>】 成功修改工艺流程!',
		'url' => '/s.php?module=task&action=view&taskid='.$task['taskid'],
		'sec' => 2
	));
}
//锁定任务单 下发到工作中心
function lock(){
	$task=$this->kclass->DB->queryFirst("
		SELECT t.*,
			s.title AS status,
			u.username AS creator,
			us.username AS modifier
		FROM task AS t
		LEFT JOIN mftstatus AS s ON (s.statusid=t.statusid)
		LEFT JOIN user AS u ON (u.userid=t.creator)
		LEFT JOIN user AS us ON (u.userid=t.modifier)
		WHERE t.killed=0 AND t.taskid='".$this->kclass->input['taskid']."'
		LIMIT 0,1
	");
	if(!$task AND $task['statusid']!=1){
		$e='<li>数据错误，很抱歉~</li>';
	}
	if(isset($e)){
		$this->kclass->messager(array(
			'title' => '锁定生产任务单',
			'text' => '锁定生产任务单<ul>'.$e.'</ul>',
			'url' => 'javascript:history.back()',
			'sec' => 3
		));
	}
$body=<<<EOF
<form method="post" action="/s.php?module=task&action=dolock">
<input type="hidden" name="module" value="task">
<input type="hidden" name="action" value="dolock">
<input type="hidden" name="taskid" value="{$this->kclass->input['taskid']}">
<table>
<thead><tr><th colspan="6">确定要锁定生产任务单 <span class="bold darkred">{$task['taskno']}</span> 吗？</th></tr></thead>
<tbody>
<tr class="even">
<td>锁定：<span class="bold red">*</span></td><td class="center"><input type="radio" name="statusid" value="2" checked="checked" />锁定　　　<input type="radio" name="statusid" value="1" />取消</td>
</tr>
<tr class="odd">
<td colspan=6 class="center"><input type="submit" value=" 提交 " /><input type="reset" value="重置" /></td>
</tr>
</tbody>
</table>
</form>
EOF;
	$this->kclass->tbline+=4;
	$this->kclass->page['title'].='锁定生产任务单';
	$this->kclass->page['onload'].='dc.tabhover();';
	$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - <a href="/s.php?module=task&action=view&taskid='.$this->kclass->input['taskid'].'">查看生产任务单</a> - 锁定生产任务单', 'right'=>'<a href="/s.php?module=task&action=list">返回列表</a>　|　<a href="/s.php?module=task&action=view&taskid='.$this->kclass->input['taskid'].'">查看生产任务单</a>','body'=>$body));
}
//
function dolock(){
	if($this->kclass->input['requestMethod'] != 'post'){
		$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
	}else{
		$task=$this->kclass->DB->queryFirst("SELECT taskno,statusid FROM task WHERE taskid='".$this->kclass->input['taskid']."'");
		if($this->kclass->input['statusid']==''){
			$e.='<li>请选择生产任务单中的 是否锁定 按钮。</li>';
		}
		if($task['statusid']==2){
			$e.='<li>生产任务单 '.$task['taskno'].' 已经锁定。</li>';
		}
	}
	if($e){
		$this->kclass->messager(array(
			'title' => '锁定生产任务单',
			'text' => '您在锁定生产任务单的过程中有以下错误：<ul>'.$e.'</ul>',
			'url' => 'javascript:history.back()',
			'sec' => 3
		));
	}
	if($this->kclass->input['statusid']==2){
		$lock='锁定成功';
		$this->kclass->DB->query("
		UPDATE task SET
			statusid='".$this->kclass->input['statusid']."',
			locker='".$this->kclass->user['userid']."',
			locked='".TIMENOW."'
		WHERE taskid='".$this->kclass->input['taskid']."'
	");
	}elseif($this->kclass->input['statusid']==1){
		$lock='锁定操作取消';
	}
	$this->kclass->messager(array(
		'title' => '锁定生产任务单',
		'text' => '生产任务单 <b>'.$task['taskno'].'</b> '.$lock.'！',
		'url' => '/s.php?module=task&action=view&taskid='.$this->kclass->input['taskid'],
		'sec' => 2
	));
}
	//
	//已作废功能 随需求变化更新 
	function verify(){
		$task=$this->kclass->DB->queryFirst("
			SELECT p.taskid,p.taskno,p.applicant,p.ifVerify,p.purpose,p.remark,p.created,p.modified,p.amount,
				c.title AS currency,
				u.username AS creator,
				us.username AS modifier
			FROM task AS p
			LEFT JOIN currency AS c ON (c.currencyid=p.currencyid)
			LEFT JOIN user AS u ON (u.userid=p.creator)
			LEFT JOIN user AS us ON (u.userid=p.modifier)
			WHERE p.killed=0 AND p.taskid='".$this->kclass->input['taskid']."'
			LIMIT 0,1
		");
		if(!$task AND $task['ifVerify']!=0){
			$e='<li>数据错误，很抱歉~</li>';
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '审核生产任务单',
				'text' => '审核生产任务单<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$task['created']=date('Y-n-d',$task['created']);
		$verify=$this->kclass->verify($task['ifVerify']);
		if($task['modified']!=0)$modify='，由'.$task['modifier'].'于'.date('Y-n-d',$task['modified']).'修改';
		if($task['ifVerify']==0){
			$verifyOperate='|　<a href="/s.php?module=task&action=update&taskid='.$task['taskid'].'">修改</a>　|　<span class="small">审核：</span><a href="/s.php?module=task&action=verify&taskid='.$task['taskid'].'" onclick="return confirm(\'你确定要对这个生产任务单 '.$task['taskno'].' 审核通过吗？\');">通过</a>　<a href="/s.php?module=task&action=unverify&taskid='.$task['taskid'].'" onclick="return confirm(\'你确定要对这个生产任务单 '.$task['taskno'].' 审核不通过吗？\');">不通过</a>';
		}elseif($task['ifVerify']==1){
			$verifyOperate='';
		}
		//申请的物资
		$items=$this->kclass->DB->query("
			SELECT p.*,
				m.materialno,m.title,m.standard,
				s.supplierid,s.title AS supplier,
				u.title AS unit,
				c.title AS currency,c.symbol
			FROM taskitem AS p
			LEFT JOIN material AS m ON (m.materialid=p.materialid)
			LEFT JOIN supplier AS s ON (s.supplierid=p.supplierid)
			LEFT JOIN unit AS u ON (u.unitid=p.unitid)
			LEFT JOIN currency AS c ON (c.currencyid=p.currencyid)
			WHERE p.killed=0 AND p.taskid='".$task['taskid']."'
			ORDER BY p.ordering ASC
		");
		if($this->kclass->DB->numRows()){
			$i=1;
			while($item=$this->kclass->DB->fetchArray($items)){
				$addMaterial='';
				if($task['ifVerify']==1 AND $item['materialid']==0){
					$addMaterial='<a target="_blank" href="/s.php?module=material&action=add&itemid='.$item['itemid'].'">新增该物资</a>';
				}

				$item['arrivalTime']=date('Y-m-d',$item['arrivalTime']);
				$class=$this->kclass->iif($i%2==0,'class="even"','class="odd"');
				if($item['module']=='taskApply'){
					$mitem=$this->kclass->DB->queryFirst("SELECT pi.*,pa.applyno,pa.taskApplyid FROM preinbounditem AS pi LEFT JOIN taskapply AS pa ON (pi.mid=pa.taskApplyid) WHERE pi.killed=0 AND pi.module='taskApply' AND pi.itemid='".$item['mitemid']."'");
					$type="生产任务";
					$typeno='<a href="/s.php?module=taskapply&action=view&taskApplyid='.$mitem['taskApplyid'].'">'.$mitem['applyno'].'</a>';
				}elseif($item['module']=='entrust'){
					$mitem=$this->kclass->DB->queryFirst("SELECT ei.*,e.entrustid,e.entrustno FROM entrustitem AS ei LEFT JOIN entrust AS e ON (e.entrustid=ei.entrustid) WHERE ei.killed=0 AND ei.itemid='".$item['mitemid']."'");
					$type="外发加工";
					$typeno='<a href="/s.php?module=entrust&action=view&entrustid='.$mitem['entrustid'].'">'.$mitem['entrustno'].'</a>';
				}
				$materialno=$this->kclass->iif($item['materialid']==0,$mitem['materialno'],'<a href="/s.php?module=material&action=view&materialid='.$item['materialid'].'">'.$item['materialno'].'</a>');
				$title=$this->kclass->iif($item['materialid']==0,$mitem['title'],$item['title']);
				$standard=$this->kclass->iif($item['materialid']==0,$mitem['stanard'],$item['standard']);

				$itemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').' small"><td>'.$i.'</td>
					<td>'.$type.'</td>
					<td>'.$typeno.'</td>
					<td>'.$materialno.'</td>
					<td>'.$title.'</td>
					<td>'.$standard.'</td>
					<td><a href="/s.php?module=psupplier&action=view&supplierid='.$item['supplierid'].'">'.$item['supplier'].'</a></td>
					<td>'.$item['quantity'].' '.$item['unit'].'</td>
					<td>'.$item['currency'].'</td>
					<td>'.$item['price'].'</td>
					<td>'.$item['discount'].'%</td>
					<td>'.$item['total'].'</td>
					<td>'.$item['arrivalTime'].'</td>
					<td>'.$item['remark'].'</td></tr>';
				$i++;
			}
		}

$body=<<<EOF
<div class="title"><span class="right small gray">由{$task['creator']}于{$task['created']}建立{$modify}。</span>{$task['taskno']}</div>
<table class="hundred">
<thead><tr><th colspan="4">生产任务单</th></tr></thead>
<tbody>
<tr class="odd">
<td width="100">编　　号：</td><td width="475">{$task['taskno']}　（{$verify}）</td>
<td width="100">采 购 员：</td><td>{$task['applicant']}</td>
</tr>
<tr class="even">
<td>总　　价：</td><td>{$task['amount']}</td>
<td>申请时间：</td><td>{$task['created']}</td>
</tr>
<tr class="odd">
<td>目　　的：</td><td>{$task['purpose']}</td>
<td>备　　注：</td><td>{$task['remark']}</td>
</tr>
</tbody>
</table>
<table class="hundred">
<thead><tr><th colspan="14">生产物资</th></tr></thead>
<tbody>
<tr class="center even">
<td width="15">ID</td><td width="60">申请类型</td><td width="50">申请单编号</td><td>物资编号</td><td>物资名称</td><td>物资规格</td><td>供应商</td><td>数量/单位</td><td>币种</td><td>单价</td><td>折扣</td><td>总价</td><td>到货时间</td><td>备注</td>
</tr>
{$itemtr}
</tbody>
</table>
<form method="post" action="/s.php?module=task&action=doverify">
<input type="hidden" name="module" value="task">
<input type="hidden" name="action" value="doverify">
<input type="hidden" name="taskid" value="{$this->kclass->input['taskid']}">
<table>
<thead><tr><th colspan="4">审核</th></tr></thead>
<tbody>
<tr class="even">
<td>审　　核：<span class="bold red">*</span></td><td class="center"><input type="radio" name="ifVerify" value="1" />通过　　　<input type="radio" name="ifVerify" value="-1" checked />不通过</td>
</tr>
<tr class="odd">
<td>审核备注：</td><td><textarea name="verifyRemark" style="width:440px;height:110px;"></textarea></td>
</tr>
<tr class="odd">
<td colspan=2 class="center"><input type="submit" value=" 提交 " /><input type="reset" value="重置" /></td>
</tr>
</tbody>
</table>
</form>
EOF;
		$this->kclass->tbline+=4;
		$this->kclass->page['title'].='审核生产任务单';
		$this->kclass->page['onload'].='dc.tabhover();';
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - <a href="/s.php?module=task&action=view&taskid='.$this->kclass->input['taskid'].'">查看生产任务单</a> - 审核生产任务单', 'right'=>'<a href="/s.php?module=task&action=list">返回列表</a>　|　<a href="/s.php?module=task&action=view&taskid='.$this->kclass->input['taskid'].'">查看生产任务单</a>','body'=>$body));
	}
	//已作废功能 随需求变化更新
	function doverify(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			$task=$this->kclass->DB->queryFirst("SELECT taskno FROM task WHERE taskid='".$this->kclass->input['taskid']."'");
			if($this->kclass->input['ifVerify']!=1 AND $this->kclass->input['ifVerify']!=-1){
				$e.='<li>请选择生产任务单中的 是否通过审核 按钮。</li>';
			}
		}
		if($e){
			$this->kclass->messager(array(
				'title' => '审核生产任务单',
				'text' => '您在审核生产任务单的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$this->kclass->DB->query("
			UPDATE task SET
				ifVerify='".$this->kclass->input['ifVerify']."',
				verifier='".$this->kclass->user['userid']."',
				verified='".TIMENOW."',
				verifyRemark='".$this->kclass->input['verifyRemark']."'
			WHERE taskid='".$this->kclass->input['taskid']."'
		");
		if($this->kclass->input['ifVerify']==1){
			$verify='已通过';
		}elseif($this->kclass->input['ifVerify']==-1){
			$verify='未通过';
		}
		$this->kclass->messager(array(
			'title' => '审核生产任务单',
			'text' => '生产任务单 【<b>'.$task['taskno'].'</b>】 '.$verify.'审核!返回查看生产任务单',
			'url' => '/s.php?module=task&action=view&taskid='.$this->kclass->input['taskid'],
			'sec' => 2
		));
	}
	//删除任务单 需修改任务单明细为空
	function kill(){
		if($this->kclass->input['taskid']<=0){
			$this->kclass->boinkIt('/s.php?module=task');
		}
		if($this->kclass->input['rt']!='list' AND $this->kclass->input['confirm'] == 0){
			$this->kclass->boinkIt('/s.php?module=task&action=view&taskid='.$this->kclass->input['taskid']);
		}
		if($this->kclass->input['taskid'] < 0){
			$e = '<li>请从正确的页面进行操作，如果此问题持续存在，请联系网站管理员。</li>';
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '删除生产任务单',
				'text' => '您在删除生产任务单的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		if($this->kclass->input['rt']=='view'){
			$rt = '&action=view&taskid='.$this->kclass->input['taskid'];
		}elseif($this->kclass->input['rt']=='list'){
			$rt = '#'.$this->kclass->input['taskid'];
		}
		$task=$this->kclass->DB->queryFirst("
			SELECT taskno
			FROM task
			WHERE taskid='".$this->kclass->input['taskid']."'
		");
		if($task){
			$this->kclass->DB->query("
				UPDATE `task`
				SET killed=".TIMENOW.",killer='".$this->kclass->user['userid']."'
				WHERE taskid='".$this->kclass->input['taskid']."'
			");
			$this->kclass->messager(array(
				'title' => '删除生产任务单成功',
				'text' => '生产任务单 <b>'.$task['taskno'].'</b> 已成功被标记为删除!',
				'url' => '/s.php?module=task'.$rt,
				'sec' => 2
			));
		}else{
			$this->kclass->messager(array(
				'title' => '删除生产任务单失败',
				'text' => '您要删除的生产任务单，不存在！',
				'url' => '/s.php?module=task'.$rt,
				'sec' => 3
			));
		}
	}

	//
	function remove(){
		if($this->kclass->input['taskid']<=0){
			$this->kclass->boinkIt('/s.php?module=task');
		}
		$task = $this->kclass->DB->queryFirst("
			SELECT taskno
			FROM `task`
			WHERE taskid='".$this->kclass->input['taskid']."'
		");
		$ti=$this->kclass->DB->queryFirst("
		SELECT itemid
		FROM `taskitem`
		WHERE killed=0 AND taskid='".$this->kclass->input['taskid']."'
	");
	if($ti){
		$e.='<li>生产任务单中存在任务明细，请进入修改页面把相应明细删除再进行操作！</li>';
	}
	if(isset($e)){
		$this->kclass->messager(array(
			'title' => '删除生产任务单',
			'text' => '生产任务单无法删除！<ul>'.$e.'</ul>',
			'url' => 'javascript:history.back()',
			'sec' => 3
		));
	}
$body = <<<EOF
<form action="/s.php?module=task&action=kill" name="task" method="post">
<input type="hidden" name="module" value="task">
<input type="hidden" name="action" value="kill">
<input type="hidden" name="taskid" value="{$this->kclass->input['taskid']}">
<input type="hidden" name="rt" value="{$this->kclass->input['rt']}">
<table><thead>
<thead>
<tr>
<th>删除生产任务单：{$task['taskno']}</th>
</tr>
</thead>
<tbody>
<tr class="even">
<td class="middle">你确定要删除生产任务单: <a href="/s.php?module=task&action=view&taskid={$this->kclass->input['taskid']}" class="big bold" target="_blank">{$task['taskno']}</a> 吗?</td>
</tr>
<tr class="odd">
<td align="center">
	<label for="c1" class="red"><input type="radio" id="c1" name="confirm" value="1">是</label>
	<label for="c0" class="green"><input type="radio" id="c0" name="confirm" value="0" checked>否</label>
</td>
</tr>
<tr class="even" nohover>
<td align="center">
	<input type="submit" value="   保存   " accesskey="s">
	<input type="reset" value="   复位   ">
</td>
</tr>
</tbody>
</table>
</form>
EOF;
		$this->kclass->page['title'] .= '删除 - '.$task['taskno'];
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 删除生产任务单 - '.$task['taskno'], 'right' => '<a href="/s.php?module=task">返回列表</a>', 'body'=>$body));
	}

	//恢复已删除的任务单
	function revival(){
		if($this->kclass->input['taskid']<=0){
			$this->kclass->boinkIt('/s.php?module=task');
		}
		if($this->kclass->input['rt']!='list' AND $this->kclass->input['confirm']==0){
			$this->kclass->boinkIt('/s.php?module=task&action=view&taskid='.$this->kclass->input['taskid']);
		}
		if($this->kclass->input['taskid']<0){
			$e = '<li>请从正确的页面进行操作，如果此问题持续存在，请联系网站管理员。</li>';
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '恢复生产任务单',
				'text' => '您在恢复生产任务单的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$task = $this->kclass->DB->queryFirst("
			SELECT taskno
			FROM `task`
			WHERE taskid='".$this->kclass->input['taskid']."'
		");
		if($this->kclass->input['rt']=='view'){
			$rt = '&action=view&taskid='.$this->kclass->input['taskid'];
		}elseif($this->kclass->input['rt']=='list'){
			$rt = '#'.$this->kclass->input['taskid'];
		}
		if($task){
			$this->kclass->DB->query("
				UPDATE `task`
				SET killed=0,killer=0
				WHERE taskid='".$this->kclass->input['taskid']."'
			");
			$this->kclass->messager(array(
				'title' => '恢复生产任务单成功',
				'text' => '生产任务单 <b>'.$task['taskno'].'</b> 已成功被恢复！',
				'url' => '/s.php?module=task'.$rt,
				'sec' => 2
			));
		}else{
			$this->kclass->messager(array(
				'title' => '恢复生产任务单',
				'text' => '您要恢复的生产任务单不存在！',
				'url' => '/s.php?module=task'.$rt,
				'sec' => 3
			));
		}
	}

	//
	function restore(){
		if($this->kclass->input['taskid']<=0){
			$this->kclass->boinkIt('/s.php?module=task');
		}
		$task = $this->kclass->DB->queryFirst("
			SELECT taskno
			FROM `task`
			WHERE taskid='".$this->kclass->input['taskid']."'
		");
$body = <<<EOF
<form action="/s.php?module=task&action=revival" name="task" method="post">
<input type="hidden" name="module" value="task">
<input type="hidden" name="action" value="revival">
<input type="hidden" name="taskid" value="{$this->kclass->input['taskid']}">
<input type="hidden" name="rt" value="{$this->kclass->input['rt']}">
<table><thead>
<thead>
<tr>
	<th>恢复生产任务单：{$task['taskno']}</th>
</tr>
</thead>
<tbody>
<tr class="even">
	<td class="middle">你确定要恢复生产任务单: <a href="/s.php?module=task&action=view&taskid={$this->kclass->input['taskid']}" class="big bold" target="_blank">{$task['taskno']}</a> 吗?</td>
</tr>
<tr class="odd">
	<td align="center">
		<label for="c1" class="red"><input type="radio" id="c1" name="confirm" value="1">是</label>
		<label for="c0" class="green"><input type="radio" id="c0" name="confirm" value="0" checked>否</label>
	</td>
</tr>
<tr class="even" nohover>
	<td align="center">
		<input type="submit" value="   保存   " accesskey="s">
		<input type="reset" value="   复位   ">
	</td>
</tr>
</tbody>
</table>
</form>
EOF;
			$this->kclass->page['title'] .= ' - 恢复 - '.$task['taskno'];
			$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 恢复生产任务单 - '.$task['taskno'], 'right' => '<a href="/s.php?module=task">返回列表</a>', 'body'=>$body));
		}
	}
?>