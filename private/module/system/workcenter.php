<?php
//
class workcenter{
	var $kclass;
	var $outboundType=array(array('id'=>1,'title'=>'按任务单领料'),array('id'=>2,'title'=>'按需领料'));
	//
	function autorun(){
		$this->baseurl='<a href="/s.php?">首页</a> ';
		switch($this->kclass->input['action']){
			case 'list':
				return $this->mmlist();
				break;
			case 'find':
				return $this->find();
				break;
			case 'view':
				return $this->view();
				break;
			case 'add':
				return $this->add();
				break;
			case 'insert':
				return $this->insert();
				break;
			case 'update':
				return $this->update();
				break;
			case 'doupdate':
				return $this->doupdate();
				break;
			case 'kill':
				return $this->kill();
				break;
			case 'remove':
				return $this->remove();
				break;
			case 'revival':
				return $this->revival();
				break;
			case 'restore':
				return $this->restore();
				break;
			case 'addMaterial':
				return $this->addMaterial();
				break;
			case 'insertMaterial':
				return $this->insertMaterial();
				break;
			case 'removeMaterial':
				return $this->removeMaterial();
				break;
			case 'evaluate':
				return $this->evaluate();
				break;
			case 'doevaluate':
				return $this->doevaluate();
				break;
			case 'inquiry':
				return $this->inquiry();
				break;
			case 'addInquiry':
				return $this->addInquiry();
				break;
			case 'insertInquiry':
				return $this->insertInquiry();
				break;
			case 'updateInquiry':
				return $this->updateInquiry();
				break;
			case 'doupdateInquiry':
				return $this->doupdateInquiry();
				break;
			case 'killInquiry':
				return $this->killInquiry();
				break;
			case 'removeInquiry':
				return $this->removeInquiry();
				break;
			case 'revivalInquiry':
				return $this->revivalInquiry();
				break;
			case 'restoreInquiry':
				return $this->restoreInquiry();
				break;
			case 'quoting':
				return $this->quoting();
				break;
			case 'addQuoting':
				return $this->addQuoting();
				break;
			case 'insertQuoting':
				return $this->insertQuoting();
				break;
			case 'updateQuoting':
				return $this->updateQuoting();
				break;
			case 'doupdateQuoting':
				return $this->doupdateQuoting();
				break;
			case 'killQuoting':
				return $this->killQuoting();
				break;
			case 'removeQuoting':
				return $this->removeQuoting();
				break;
			case 'revivalQuoting':
				return $this->revivalQuoting();
				break;
			case 'restoreQuoting':
				return $this->restoreQuoting();
				break;
			case 'addVisit':
				$this->addVisit();
			break;
			case 'insertVisit':
				$this->insertVisit();
			break;
			case 'updateVisit':
				$this->updateVisit();
			break;
			case 'doupdateVisit':
				$this->doupdateVisit();
			break;
			case 'removeVisit':
				$this->removeVisit();
			break;
			case 'killVisit':
				$this->killVisit();
			break;
			case 'revivalVisit':
				$this->revivalVisit();
			break;
			case 'restoreVisit':
				$this->restoreVisit();
			break;
			case 'updateBankDetail':
				$this->updateBankDetail();
			break;
			case 'doupdateBankDetail':
				$this->doupdateBankDetail();
			break;
			case 'removeBankDetail':
				$this->removeBankDetail();
			break;
			case 'killBankDetail':
				$this->killBankDetail();
			break;
			case 'revivalBankDetail':
				$this->revivalBankDetail();
			break;
			case 'restoreBankDetail':
				$this->restoreBankDetail();
			break;
			case 'import':// 批量导入
				$this->import();
			break;
			case 'doimport':
				$this->doimport();
			break;
			case 'listOriginal':// 原始数据列表
				$this->listOriginal();
			break;
			case 'exchange':// 原始工作中心数据转换成标准结构的工作中心
				$this->exchange();
			break;
			default:
				return $this->mmlist();
		}
	}
	//
	function mmlist(){
		$condition=$this->kclass->iif($this->kclass->input['show']=='all','1=1','`workcenter`.killed=0');
		$keywords=$this->kclass->iif($this->kclass->input['keywords']=='',' AND 1=1',' AND (`workcenter`.title like \'%'.$this->kclass->input['keywords'].'%\' OR `workcenter`.remark like \'%'.$this->kclass->input['keywords'].'%\')');
		$departmentid=$this->kclass->user['departmentid'];$departments=array(8,9,10);
		$purviews=' AND `workcenter`.departmentid='.$departmentid;
		if(in_array($departmentid,$departments))$purviews='';
		if($this->kclass->input['orderby']!='')$query['orderby']=$this->kclass->input['orderby'];
		if($this->kclass->input['direction']!='')$query['direction']=$this->kclass->input['direction'];
		if($this->kclass->input['show']!='')$query['show']=$this->kclass->input['show'];
		if($this->kclass->input['layout']!='')$query['layout']=$this->kclass->input['layout'];
		$orderby=$this->kclass->orderby(array('module'=>'workcenter','direction'=>'asc','orderby'=>'`workcenter`.modified', 'default'=>'id', 'serial'=>array(array('title'=>'名称', 'field'=>'title','word'=>'name'), array('title'=>'ID', 'field'=>'workcenterid','word'=>'id'), array('title'=>'修改时间', 'field'=>'modified'), array('title'=>'建立时间', 'field'=>'created')),'appendUrl'=>$query));
		if(is_array($query) AND count($query)>0)$queryPart='&'.http_build_query($query);
		$workcenters=$this->kclass->DB->query("
			SELECT `workcenter`.workcenterid,`workcenter`.title,`workcenter`.position,`workcenter`.workhours,
				`workcenter`.quantity,`workcenter`.efficiency,`workcenter`.burthen,`workcenter`.remark,`workcenter`.modified,`workcenter`.killed,`workcenter`.created,
				d.title AS department,
				m.username AS modifier,
				c.username AS creator
			FROM `workcenter`
			LEFT JOIN `user` AS m ON (m.userid=`workcenter`.modifier)
			LEFT JOIN `user` AS c ON (c.userid=`workcenter`.creator)
			LEFT JOIN `department` AS d ON (d.departmentid=`workcenter`.departmentid)
			WHERE ".$condition.$keywords.$purviews."
			ORDER BY ".$orderby['sql']."
		");
		if($this->kclass->DB->numRows()){
			if($this->kclass->input['layout']=='grid'){
				$body='<ul id="mmlist" class="mmlist clear">';
			}else{
				$body='<table class="hundred"><thead><th>ID</th><th>名称</th><th>部门</th><th>描述</th><th>人数</th><th>工时</th><th width="30">选项</th></thead><tbody>';
			}
			$i=1;
			while($workcenter=$this->kclass->DB->fetchArray($workcenters)){
				$workcenter['title']=$this->kclass->parseConvertValue($workcenter['title']);
				$title=$this->kclass->iif(strlen($workcenter['title'])>30, mb_substr($workcenter['title'], 0, 30, 'UTF-8').'...', $workcenter['title']);
				if($this->kclass->input['title']!=''){
					$title=$this->kclass->highlight($workcenter['title'], $this->kclass->input['title']);
				}
				$remark=$workcenter['remark'];
				if($this->kclass->input['remark']!=''){
					$remark=$this->kclass->highlight($workcenter['remark'], $this->kclass->input['remark']);
				}
				$quantity=$workcenter['quantity'];
				if($this->kclass->input['quantity']!=''){
					$quantity=$this->kclass->highlight($quantity, $this->kclass->input['quantity']);
				}
				$efficiency=$workcenter['efficiency'];
				if($this->kclass->input['efficiency']!=''){
					$efficiency=$this->kclass->highlight($efficiency, $this->kclass->input['efficiency']);
				}
				$workhours=$workcenter['workhours'];
				if($this->kclass->input['workhours']!=''){
					$workhours=$this->kclass->highlight($workhours, $this->kclass->input['workhours']);
				}
				$department=$workcenter['department'];
				if($this->kclass->input['department']!=''){
					$department=$this->kclass->highlight($department, $this->kclass->input['department']);
				}
				$workcenter['created']=date('Y-m-d',$workcenter['created']);
				$workcenter['title']=$this->kclass->iif(strlen($workcenter['title'])>20,mb_substr($workcenter['title'],0,10,'UTF-8').'...',$workcenter['title']);
				if($workcenter['killed']>0){
					$link='<a href="/s.php?module=workcenter&action=restore&workcenterid='.$workcenter['workcenterid'].'&rt=list">恢复</a>';
				}else{
					$link='<a href="/s.php?module=workcenter&action=update&workcenterid='.$workcenter['workcenterid'].'&rt=list">改</a><br><a href="/p.php?action=workcenter&workcenterid='.$workcenter['workcenterid'].'" target="_blank">印</a>';
				}
				if($this->kclass->input['layout']=='grid'){//列表
					$body.='<li title="由 '.$workcenter['creator'].' 建于 '.$workcenter['created'].$this->kclass->iif($workcenter['modifier']!='', '，'.$workcenter['modifier'].' 改于 '.date('Y-m-d H:i',$workcenter['modified']), '').'"'.$this->kclass->iif($i%4==0, ' class="end"', '').'>
					<div class="mmlistt">
						<span class="right normal">';
					if($workcenter['killed']>0){
						$body.='<a href="/s.php?module=workcenter&action=revival&workcenterid='.$workcenter['workcenterid'].'&rt=list" onclick="return confirm(\'你确定要恢复这个工作中心 '.$workcenter['title'].' 吗？\');">恢复</a>';
					}else{
						$body.='<a href="/s.php?module=workcenter&action=kill&workcenterid='.$workcenter['workcenterid'].'&rt=list" onclick="return confirm(\'你确定要删除这个工作中心 '.$workcenter['title'].' 吗？\');">删</a> <a href="/s.php?module=workcenter&action=update&workcenterid='.$workcenter['workcenterid'].'&rt=list">改</a>';
					}
					$body.='</span>
					<span class="small">'.$workcenter['workcenterid'].'</span> <a href="/s.php?module=workcenter&action=view&workcenterid='.$workcenter['workcenterid'].'">'.$workcenter['title'].'</a><br><span class="small gray right">'.$workcenter['created'].'</span></div>
				<div class="mmlistb">
					<div title="'.$workcenter['department'].'"><span class="small gray">部门：'.$workcenter['department'].'</span></div>
					<div title="'.$workcenter['quantity'].'"><span class="small gray">人数：'.$workcenter['quantity'].'</span></div>
					<div><span class="small gray">工时：'.$workhours.'</span></div>
					<div><span class="small gray">效率：'.$efficiency.'%</span></div>
					<div><span class="small gray">位置：'.$workcenter['position'].'</span></div>
					<div title="'.$workcenter['remark'].'"><span class="small gray">描述：'.$this->kclass->iif(strlen($workcenter['remark'])>30, mb_substr($workcenter['remark'], 0, 30, 'UTF-8').'...', $workcenter['remark']).'</span></div>
					<div class="small clear"><span class=right title="由 '.$workcenter['creator'].' 建于 '.$workcenter['created'].'">由 '.$workcenter['creator'].' 建于 '.$workcenter['created'].'</span></div></div></li>';
				}else{
					$body .='<tr '.$this->kclass->rotateLine().'>
						<td>'.$workcenter['workcenterid'].'</td>
						<td><a href="/s.php?module=workcenter&action=view&workcenterid='.$workcenter['workcenterid'].'"><span class="normal" title="'.$workcenter['title'].'">'.$title.'</span></a></td>
						<td>'.$department.'</td>
						<td>'.$remark.'</td>
						<td>'.$quantity.'</td>
						<td>'.$workhours.'</td>
						<td align="center">'.$link.'</td>
					</tr>';
				}
				$i++;
			}
			if($this->kclass->input['layout']=='grid'){
				$body .= '</ul>';
			}else{
				$body.='</tbody></table>';
			}
		}else{
			$body='暂无相关记录';
		}
		if($this->kclass->input['layout']=='grid'){
			$layoutLink='<a href="/s.php?module=workcenter&action=list&layout=list'.str_replace('&layout=grid', '', $queryPart).'">列表</a> 格子';
		}else{
			$layoutLink='列表 <a href="/s.php?module=workcenter&action=list&layout=grid'.str_replace('&layout=list', '', $queryPart).'">格子</a>';
		}
		if($this->kclass->input['show']=='all'){
			$showLink='<a href="/s.php?module=workcenter&action=list'.str_replace('&show=all', '', $queryPart).'">默认</a> 所有<span class="small gray">(包括删除)</span>';
		}else{
			$showLink='默认 <a href="/s.php?module=workcenter&action=list&show=all'.str_replace('&show=all', '', $queryPart).'">所有<span class="small gray">(包括删除)</span></a>';
		}
		$this->kclass->page['onload']='dc.tabhover();';
		$this->kclass->page['title']='工作中心列表';
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 列表', 'right' => '<span class="small">排序：</span>'.$orderby['link'].'|　<span class="small">显示方式：</span>'.$showLink.'　|　<span class="small">布局：</span>'.$layoutLink.'　|　<a href="/s.php?module=workcenter&action=add">新建</a>　|', 'body'=>$body));
	}
	//
	function find(){
		if($this->kclass->input['keywords']==''){
$body=<<<EOF
<form method="get" action="/s.php?module=workcenter&action=list">
<input type="hidden" name="module" value="workcenter">
<input type="hidden" name="action" value="list">
<table>
<thead><tr><th colspan=2>查找工作中心：</th></tr></thead>
<tbody>
<tr><td>请输入查找关键词：</td><td><input type="text" name="keywords" style="width:350px"></td></tr>
<tr><td colspan=2 class="small gray">注意：请输入要查找工作中心的编号或工作中心名称</td></tr>
<tr class="center"><td colspan=2><input type="submit" value=" 提交 "><input type="reset" value=" 重置 "></td></tr>
</tbody>
</table>
</form>
EOF;
		}
		$this->kclass->page['title'].='工作中心查找';
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - 工作中心查找','right' => '<a href="/s.php?module=workcenter">返回列表</a>','body'=>$body));
	}
	//
	function view(){
		$workcenterid=$this->kclass->input['workcenterid'];
		$workcenter=$this->kclass->DB->queryFirst("
			SELECT `workcenter`.*,
				d.title AS department,
				m.username AS modifier,
				c.username AS creator
			FROM `workcenter`
			LEFT JOIN `user` AS m ON (m.userid=`workcenter`.modifier)
			LEFT JOIN `user` AS c ON (c.userid=`workcenter`.creator)
			LEFT JOIN `department` AS d ON (d.departmentid=`workcenter`.departmentid)
			WHERE `workcenter`.workcenterid ='".$workcenterid."'
			ORDER BY `workcenter`.workcenterid ASC,`workcenter`.modified DESC,`workcenter`.created DESC
			LIMIT 0,1
		");
		foreach($this->outboundType as $val){
			if($workcenter['outboundtypeid']==$val['id']){
				$outboundtype=$val['title'];
			}
		}
		if($workcenter){
			$workcenter['created']=date('Y-n-d H:i:s',$workcenter['created']);
			if($workcenter['modified']!=0)$modify='，由'.$workcenter['modifier'].'于'.date('Y-n-d H:i:s',$workcenter['modified']).'修改';
			$workcenterItems = $this->kclass->releatedWorkcenterMaterial(array('workcenterid'=>$workcenterid));//工作中心供应的物资
			$pcount=$workcenterItems['counter'];
			if($pcount>0){
				$wclist='<table class="hundred"><thead><tr><th width="15">ID</th><th>物资</th><th width="50">版本</th><th width="100">工作中心</th><th width="100">当前/待入/待出</th><th width="80">计划可用</th><th width="180">操作</th></tr></thead><tbody id="workcenteritem">'.$workcenterItems['panel'].'</tbody></table>';
			}
			$perpage=10;
			if($workcenterItems['num']>0){
				if($pcount>$workcenterItems['num']){
					$morewclist.='<div class="page" id="workcenteritempage"><a href="javascript:dc.listItem.page('.$perpage.',\'workcenteritem\',\'workcenter\',\''.$workcenterid.'\')">点击查看更多</a></div>';
				}
			}
			$morewclist.='<div class="clear"><span class="gray small left">(共<span class="darkred">'.$pcount.'</span>条记录)</span></div>';
			//
			$wctasklist = $this->kclass->releatedTasklist(array('workcenterid'=>$workcenterid));//工作中心相关生产任务单
			$moretasklist.='<div id="taskitem"></div>';
			$pcount=$wctasklist['counter'];
			$perpage=10;
			if($wctasklist['num']>0){
				if($pcount>$wctasklist['num']){
					$moretasklist.='<div class="page" id="taskitempage"><a href="javascript:dc.listItem.page('.$perpage.',\'taskitem\',\'workcenter\',\''.$workcenterid.'\')">点击查看更多</a></div>';
				}
			}
			$moretasklist.='<div class="clear"><span class="gray small left">(共<span class="darkred">'.$pcount.'</span>条记录)</span></div>';
			//
			$wcdispatchlist = $this->kclass->releatedDispatchlist(array('workcenterid'=>$workcenterid));//工作中心相关生产派工单
			$moredispatchlist.='<div id="dispatchitem"></div>';
			$pcount=$wcdispatchlist['counter'];
			$perpage=10;
			if($wcdispatchlist['num']>0){
				if($pcount>$wcdispatchlist['num']){
					$moredispatchlist.='<div class="page" id="dispatchitempage"><a href="javascript:dc.listItem.page('.$perpage.',\'dispatchitem\',\'workcenter\',\''.$workcenterid.'\')">点击查看更多</a></div>';
				}
			}
			$moredispatchlist.='<div class="clear"><span class="gray small left">(共<span class="darkred">'.$pcount.'</span>条记录)</span></div>';
			//
			$wcproductionrecord = $this->kclass->releatedProductionrecord(array('workcenterid'=>$workcenterid));//工作中心相关生产记录
			$pcount=$wcproductionrecord['counter'];
			if($pcount>0){
				$wcproductionrecordlist='<table class="hundred"><thead><tr><th width="10">ID</th><th>生产批次</th><th>派工单</th><th>物资</th><th>版本</th><th>需求数量</th><th>需求时间</th><th>完工数量</th><th>完工时间</th><th width="80">完工总用时</th></tr></thead><tbody id="productionrecord">'.$wcproductionrecord['panel'].'</tbody></table>';
			}
			$perpage=10;
			if($wcproductionrecord['num']>0){
				if($pcount>$wcproductionrecord['num']){
					$morerecordlist.='<div class="page" id="productionrecordpage"><a href="javascript:dc.listItem.page('.$perpage.',\'productionrecord\',\'workcenter\',\''.$workcenterid.'\')">点击查看更多</a></div>';
				}
			}
			$morerecordlist.='<div class="clear"><span class="gray small left">(共<span class="darkred">'.$pcount.'</span>条记录)</span></div>';
			//
			$wcrepairrecord = $this->kclass->releatedRepairrecord(array('workcenterid'=>$workcenterid));//工作中心相关维修单
			$morerepairrecord.='<div id="repairrecord"></div>';
			$pcount=$wcrepairrecord['counter'];
			$perpage=3;
			if($wcrepairrecord['num']>0){
				if($pcount>$wcrepairrecord['num']){
					$morerepairrecord.='<div class="page" id="repairrecordpage"><a href="javascript:dc.listItem.page('.$perpage.',\'repairrecord\',\'workcenter\',\''.$workcenterid.'\')">点击查看更多</a></div>';
				}
			}
			$morerepairrecord.='<div class="clear"><span class="gray small left">(共<span class="darkred">'.$pcount.'</span>条记录)</span></div>';
			$order=$this->kclass->relatedOrderList(array('workcenterid'=>$workcenterid,'type'=>'PO','panel'=>1));
			$sample=$this->kclass->relatedSampleList(array('workcenterid'=>$workcenterid,'panel'=>1));
			$packinglist=$this->_relatedPackinglist(array());
$body=<<<EOF
<div class="title"><span class="right small gray">由{$workcenter['creator']}于{$workcenter['created']}建立{$modify}。</span>{$workcenter['title']}</div>
<dl id="workcenter" class="tabs" style="display: block;">
<dt>资料</dt>
<dt title="工作中心的各类产出品"{$workcenterItems['off']}>产出品{$workcenterItems['count']}</dt>
<dt title="与工作中心相关的设备&人员"{$invoice['off']}>设备&人员{$invoice['count']}</dt>
<dt title="与工作中心相关的工厂日历"{$guarantee['off']}>工厂日历{$guarantee['count']}</dt>
<dt title="与工作中心相关的任务单"{$wctasklist['off']}>任务单{$wctasklist['count']}</dt>
<dt title="与工作中心相关的派工单"{$wcdispatchlist['off']}>派工单{$wcdispatchlist['count']}</dt>
<dt title="与工作中心相关的生产记录"{$wcproductionrecord['off']}>生产记录{$wcproductionrecord['count']}</dt>
<dt title="与工作中心相关的维修记录"{$wcrepairrecord['off']}>维修记录{$wcrepairrecord['count']}</dt>
<dt title="包装组可查看订单状态"{$order['off']}>销售PO订单{$order['count']}</dt>
<dt title="包装组可查看订单状态"{$sample['off']}>样品单{$sample['count']}</dt>
<dt title="包装组可查看订单状态"{$packinglist['off']}>包装清单{$packinglist['count']}</dt>
<dd>
<div class="gray small"><span class="right normal"><span class="small">新建：</span><a href="/s.php?module=workcenter&action=add&workcenterid={$workcenterid}">工作中心</a>　|　<a href="/s.php?module=workcenter&action=update&workcenterid={$workcenterid}">修改产出品</a>　|</span>由{$workcenter['creator']}于{$workcenter['created']}建立{$modify}。</div>
<table class="hundred">
<thead><tr><th colspan="4">工作中心信息 </th></tr></thead>
<tr class="odd">
	<td width="100">名　　称：</td><td>{$workcenter['title']} </td>
	<td width="80">部    门：</td><td>{$workcenter['department']}</td>
</tr>
<tr class="even">
	<td width="100">领料类型：</td><td>{$outboundtype} </td>
	<td width="80"></td><td></td>
</tr>
<tr class="odd">
	<td width="80">人　　数：</td><td>{$workcenter['quantity']}</td>
	<td>工　　时：</td><td>{$workcenter['workhours']}</td>
</tr>
<tr class="odd">
	<td>效　　率：</td><td>{$workcenter['efficiency']}</td>
	<td>负　　荷：</td><td>{$workcenter['burthen']}</td>
</tr>
<tr class="even">
	<td>位　　置：</td><td>{$workcenter['position']}</td>
	<td>描　　述：</td><td>{$workcenter['remark']}</td>
</tr>
</table>
</dd>
<dd>
<div class="small"><span class="right normal">{$addParking}</span></div>
{$wclist}{$morewclist}</dd>
<dd>{$visit['panel']}</dd>
<dd>{$quoting['panel']}</dd>
<dd>{$wctasklist['panel']}{$moretasklist}</dd>
<dd>{$wcdispatchlist['panel']}{$moredispatchlist}</dd>
<dd>{$wcproductionrecordlist}{$morerecordlist}</dd>
<dd>{$wcrepairrecord['panel']}{$morerepairrecord}</dd>
<dd>{$order['panel']}</dd>
<dd>{$sample['panel']}</dd>
<dd>{$packinglist['panel']}</dd>
EOF;

		}else{
				$body='系统数据出错，很抱歉！';
		}
		$this->kclass->page['title'].='查看工作中心';
		$this->kclass->page['onload'].='dc.tabhover();dc.tips();dc.tabs({\'id\':\'workcenter\'});dc.show()';
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title' => $this->baseurl . ' - <a href="/s.php?module=workcenter&action=list">工作中心列表</a> - 查看工作中心', 'right' =>'　<span class="small">新建：</span> <a href="/s.php?module=task&action=addOutbound&workcenterid='.$workcenterid.'" class="hand">出库单</a>  &nbsp;&nbsp;<a href="/s.php?module=task&action=addInbound&workcenterid='.$workcenterid.'" class="hand">入库单</a>　<a href="/s.php?module=dispatch&action=add&workcenterid='.$workcenterid.'">派工单</a>　<a href="/s.php?module=dispatch&action=addQuantity&workcenterid='.$workcenterid.'">生产记录</a>　<a href="/s.php?module=dispatch&action=addrepairRecord&workcenterid='.$workcenterid.'">维修记录</a>','body' => $body));
	}
	// insert
	function add(){
		for($i=1;$i<=6;$i++){
			$itemList.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').' small">
				<td>'.$i.'</td>
				<td>'.$this->kclass->chooserMaterial(array('hName'=>'itemMaterialid['.$i.']','hId'=>'itemMaterialid'.$i,'name'=>'itemMaterial['.$i.']','id'=>'itemMaterial'.$i,'width'=>400,'line'=>$i,'showVersion'=>1)).'</td>
				<td><select name="itemVersionid['.$i.']" id="itemVersionid'.$i.'" style="width:80px;"></select></td>
			</tr>';
		}
		foreach($this->outboundType as $key => $val){
			$typeSelect.='<option value="'.$val['id'].'">'.$val['title'].'</option>';
		}
		$department=$this->kclass->chooserDepartment(array('name'=>'departmentid','hasBlank'=>1,'width'=>400	,'topname'=>'请选择'));
$body=<<<EOF
<form action="/s.php?module=workcenter&action=insert" name="workcenter" method="post">
<input type="hidden" name="module" value="workcenter" />
<input type="hidden" name="action" value="insert" />
<table class="hundred">
<thead><tr><th colspan="4">新建工作中心</th></tr></thead>
<tbody>
<tr class="odd">
<td>名称：<span class="red bold">*</span></td>
<td><input type="text" style="width:450px" name="title" />　领料类型：<select name="outboundtypeid" style="width:150px">{$typeSelect}</select> <span class="red bold">*</span></td>
<td>部门：<span class="red bold">*</span></td>
<td>{$department}</td>
</tr>
<tr class="even">
<td>人数：<span class="red bold">*</span></td>
<td><input type="text" style="width:450px" name="quantity" /></td>
<td>工时：<span class="red bold">*</span></td>
<td><input type="text" style="width:450px" name="workhours" /></td>
</tr>
<tr class="odd">
<td>效率：<span class="red bold">*</span></td>
<td><input type="text" style="width:450px" name="efficiency" /></td>
<td>负荷：<span class="red bold">*</span></td>
<td><input type="text" style="width:450px" name="burthen" /></td>
</tr>
<tr class="even">
<td>位置：</td>
<td><input type="text" style="width:450px" name="position" /></td>
<td>描述：</td>
<td><textarea style="width:450px;height:100px" name="remark"></textarea></td>
</tr>
</tbody>
</table>
<table class="hundred">
<thead></tr><th colspan="7">选择产出品</th></tr></thead>
<tbody>
<tr class="even"><td width="30">ID</td><td width="400"><span class="left">选择物资</span></td><td><span class="left">版本</span></td></tr>
	{$itemList}
<tr class="even">
	<td class="small gray" colspan="8">
		注意事项：
		如果所列表格不够，那么在提交保存后再进行修改工作中心</td>
</tr>
</tbody>
</table>
<table class="tlist tsuite" align="center" width="100%">
<tbody>
<tr class="odd center" nohover>
<td colspan=4><input type="submit" value="  提交  " accesskey="s" onclick="dc.workcenter.check(document.workcenter)"><input type="reset" value="  复位  "/></td>
</tr>
</tbody>
</table>
</form>
EOF;
		$this->kclass->tbline+=4;
		$this->kclass->page['title'].='新建工作中心';
		$this->kclass->page['onload'].='dc.tabhover();';
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - 新建工作中心', 'right'=>'<a href="/s.php?module=workcenter">返回列表</a>','body'=>$body));
	}
	//
	function insert(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e.='<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			if($this->kclass->input['title'] == ''){
				$e.='<li>请填写工作中心的 名称。</li>';
			}else{
				if($this->kclass->DB->queryFirst("SELECT workcenterid FROM `workcenter` WHERE `title`='".$this->kclass->input['title']."'")){
					$e.='<li>您要新建的工作中心 【'.$this->kclass->input['title'].'】 已经存在。</li>';
				}
			}
			if($this->kclass->input['departmentid'] == ''){
				$e.='<li>请填写工作中心的 部门。</li>';
			}
			if($this->kclass->input['quantity'] == ''){
				$e.='<li>请填写工作中心的 人数。</li>';
			}
			if($this->kclass->input['workhours'] == ''){
				$e.='<li>请填写工作中心的 工时。</li>';
			}
			if($this->kclass->input['efficiency'] == ''){
				$e.='<li>请填写工作中心的 效率。</li>';
			}
			if($this->kclass->input['burthen'] == ''){
				$e.='<li>请填写工作中心的 负荷。</li>';
			}
			$count=count($this->kclass->input['itemMaterialid']);	
			for($i=1;$i<$count+1;$i++){
				if($this->kclass->input['itemMaterialid'][$i]>0){
					if($this->kclass->input['itemVersionid'][$i]==''){
						$e.='<li>请选择物资[ID:'.$i.']的 版本。</li>';
					}
					$m=$this->kclass->DB->queryFirst("
						SELECT wci.materialid,
						m.materialno,m.title AS material,m.standard AS standard
						FROM `workcenteritem` AS wci
						LEFT JOIN `material` AS m ON (m.materialid=wci.materialid)
						WHERE wci.killed=0 AND wci.materialid='".$this->kclass->input['itemMaterialid'][$i]."' AND wci.versionid='".$this->kclass->input['itemVersionid'][$i]."' AND wci.materialitemid='".$this->kclass->input['materialItemid'][$i]."'
					");
					if($m['materialid']!=''){
						$e.='<li>您要关联的物资 【'.$m['materialno'].'】 已经存在。</li>';
					}
				}
			}
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '新建工作中心',
				'text' => '您在新建工作中心的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}

		$this->kclass->DB->query("
			INSERT INTO `workcenter` (`title`,`departmentid`,`outboundtypeid`,`position`,`workhours`,`quantity`,`efficiency`,`burthen`,`remark`,`creator`,`created`) VALUES ('".$this->kclass->input['title']."','".$this->kclass->input['departmentid']."','".$this->kclass->input['outboundtypeid']."','".$this->kclass->input['position']."','".$this->kclass->input['workhours']."','".$this->kclass->input['quantity']."','".$this->kclass->input['efficiency']."','".$this->kclass->input['burthen']."','".$this->kclass->input['remark']."','".$this->kclass->user['userid']."','".TIMENOW."')
		");
		$workcenterid=$this->kclass->DB->insertID();
		$count=count($this->kclass->input['itemMaterialid']);
		for($i=1;$i<$count+1;$i++){
			//insert workcenter material item
			if($this->kclass->input['itemMaterialid'][$i]>0){
				$this->kclass->DB->query("
					INSERT INTO workcenteritem (materialid,versionid,materialitemid,workcenterid,created,creator)
					VALUES ('".$this->kclass->input['itemMaterialid'][$i]."','".$this->kclass->input['itemVersionid'][$i]."','".$this->kclass->input['materialItemid'][$i]."','".$workcenterid."','".TIMENOW."','".$this->kclass->user['userid']."')
				");
			}
		}

		$this->kclass->messager(array(
			'title' => '新建工作中心',
			'text' => '工作中心 <b>'.$this->kclass->input['title'].'</b> 已新建成功!',
			'url' => '/s.php?module=workcenter&action=view&workcenterid='.$workcenterid,
			'sec' => 2
		));
	}
	// update workcenter
	function update(){
		if($this->kclass->input['workcenterid']>0){
			$workcenter=$this->kclass->DB->queryFirst("
				SELECT `workcenter`.*,
					m.username AS modifier,
					c.username AS creator
				FROM `workcenter`
				LEFT JOIN `user` AS m ON (m.userid=workcenter.modifier)
				LEFT JOIN `user` AS c ON (c.userid=workcenter.creator)
				WHERE `workcenter`.killed=0 AND `workcenter`.workcenterid ='".$this->kclass->input['workcenterid']."'
				ORDER BY `workcenter`.workcenterid ASC,`workcenter`.modified DESC,`workcenter`.created DESC
				LIMIT 0,1
			");
			if($workcenter){
				//$upload=$this->kclass->upload(array('module'=>'workcenter', 'mid'=>$workcenter['workcenterid']));
				$items=$this->kclass->DB->query("
					SELECT wci.*, 
						mv.title AS version
					FROM `workcenteritem` AS wci
					LEFT JOIN `materialversion` AS mv ON (mv.versionid=wci.versionid)
					WHERE wci.killed=0 AND wci.workcenterid='".$workcenter['workcenterid']."'
					ORDER BY wci.materialid ASC
				");
				$k=1;
				$ifexist='<tr><td colspan="8" class="center darkred">当前生产物资为空</td></tr>';
				if($this->kclass->DB->numRows()){
					$ifexist='';
					$itemList.='<tr><td>ID</td><td>物资编号</td><td>物资名称</td><td>物资规格</td><td>版本</td><td>删</td></tr>';
					while($item=$this->kclass->DB->fetchArray($items)){
						$materialInfo=$this->kclass->getMaterial(array('materialid'=>$item['materialid'],'itemid'=>$item['materialitemid']));
						$itemList.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').' small">
							<td width="30"><input type="hidden" name="orgitemId['.$k.']" value="'.$item['itemid'].'">'.$k.'</td>
							<td><a href="'.$materialInfo['url'].'">'.$materialInfo['no'].'</a></td>
							<td>'.$materialInfo['material'].'</td>
							<td>'.$materialInfo['standard'].'</td>
							<td>'.$item['version'].'</td>
							<td><input type="checkbox" name="itemKill['.$k.']" value="'.$item['itemid'].'"></td>
						</tr>';
						$k++;
					}
				}
				$itemList.='</tbody></table><table class="hundred"><thead><tr><th colspan="8">新增生产物资</th></tr></thead><tbody>';
				$itemList.='<tr class="center"><td width="30">ID</td><td width="400"><span class="left">选择物资</span></td><td><span class="left">版本</span></td></tr>';
				for($i=$k;$i<$k+6;$i++){
					$itemList.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').' small">
						<td>'.$i.'</td>
						<td>'.$this->kclass->chooserMaterial(array('hName'=>'itemMaterialid['.$i.']','hId'=>'itemMaterialid'.$i,'name'=>'itemMaterial['.$i.']','id'=>'itemMaterial'.$i,'width'=>400,'line'=>$i,'showVersion'=>1)).'</td>
						<td><select name="itemVersionid['.$i.']" id="itemVersionid'.$i.'" style="width:80px;"></select></td>
					</tr>';
				}
				foreach($this->outboundType as $key => $val){
					if($workcenter['outboundtypeid']==$val['id']){
						$typeSelect.='<option value="'.$val['id'].'" selected>'.$val['title'].'</option>';
						}else{
							$typeSelect.='<option value="'.$val['id'].'">'.$val['title'].'</option>';
						}
				}
				$department=$this->kclass->chooserDepartment(array('name'=>'departmentid','hasBlank'=>1,'width'=>400	,'selectedid'=>$workcenter['departmentid']				));
$body=<<<EOF
<form action="/s.php?module=workcenter&action=doupdate" name="workcenter" method="post">
<input type="hidden" name="module" value="workcenter" />
<input type="hidden" name="action" value="doupdate" />
<input type="hidden" name="workcenterid" value="{$workcenter['workcenterid']}" />
<table class="hundred">
<thead><tr><th colspan="4">修改工作中心</th></tr></thead>
<tbody>
<tr class="odd">
<td>名称：<span class="red bold">*</span></td>
<td><input type="text" style="width:450px" name="title" value="{$workcenter['title']}" />　领料类型：<select name="outboundtypeid" style="width:150px">{$typeSelect}</select> <span class="red bold">*</span></td>
<td>部门：<span class="red bold">*</span></td>
<td>{$department}</td>
</tr>
<tr class="even">
<td>人数：<span class="red bold">*</span></td>
<td><input type="text" style="width:450px" name="quantity" value="{$workcenter['quantity']}"/></td>
<td>工时：<span class="red bold">*</span></td>
<td><input type="text" style="width:450px" name="workhours" value="{$workcenter['workhours']}"/></td>
</tr>
<tr class="odd">
<td>效率：<span class="red bold">*</span></td>
<td><input type="text" style="width:450px" name="efficiency" value="{$workcenter['efficiency']}"/></td>
<td>负荷：<span class="red bold">*</span></td>
<td><input type="text" style="width:450px" name="burthen" value="{$workcenter['burthen']}"/></td>
</tr>
<tr class="even">
<td>位置：</td>
<td><input type="text" style="width:450px" name="position" value="{$workcenter['position']}"/></td>
<td>描述：</td>
<td><textarea style="width:450px;height:100px" name="remark">{$workcenter['remark']}</textarea></td>
</tr>
</tbody>
</table>

<table class="hundred">
<thead></tr><th colspan="7">修改当前产出品</th></tr></thead>
<tbody>
{$ifexist}
{$itemList}
<tr class="even">
	<td class="small gray" colspan="8">
		注意事项：
		如果所列表格不够，那么在提交保存后再进行修改工作中心</td>
</tr>
</tbody>
</table>

<table class="tlist tsuite" align="center" width="100%">
<tbody>
<tr class="odd center" nohover>
<td colspan=4><input type="submit" value="  提交  " accesskey="s" onclick="dc.workcenter.check(document.workcenter)"><input type="reset" value="  复位  "/></td>
</tr>
</tbody>
</table>
</form>
EOF;
		}else{
			$body='系统数据出错，很抱歉！';
		}

		$this->kclass->tbline+=4;
		$this->kclass->page['title'].='修改工作中心';
		$this->kclass->page['onload'].='dc.tabhover()';
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - <a href="/s.php?module=workcenter&action=view&workcenterid='.$workcenter['workcenterid'].'">查看工作中心</a> - 修改工作中心', 'right'=>'<a href="/s.php?module=workcenter">返回列表</a>','body'=>$body));
	}
}
//
function doupdate(){
	if($this->kclass->input['requestMethod'] != 'post'){
		$e='<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
	}else{
		if($this->kclass->input['title'] == ''){
			$e='<li>请填写工作中心的 中文名称。</li>';
		}else{
			if($this->kclass->DB->queryFirst("SELECT workcenterid FROM `workcenter` WHERE `title`='".$this->kclass->input['title']."' AND workcenterid<>'".$this->kclass->input['workcenterid']."'")){
				$e.='<li>您要修改的工作中心 【'.$this->kclass->input['title'].'】 已经存在。</li>';
			}
		}
		if($this->kclass->input['departmentid'] == ''){
			$e.='<li>请填写工作中心的 部门。</li>';
		}
		if($this->kclass->input['quantity'] == ''){
			$e.='<li>请填写工作中心的 人数。</li>';
		}
		if($this->kclass->input['workhours'] == ''){
			$e.='<li>请填写工作中心的 工时。</li>';
		}
		if($this->kclass->input['efficiency'] == ''){
			$e.='<li>请填写工作中心的 效率。</li>';
		}
		if($this->kclass->input['burthen'] == ''){
			$e.='<li>请填写工作中心的 负荷。</li>';
		}
		$c=count($this->kclass->input['orgitemId']);
		for($i=$c+1;$i<$c+6;$i++){
			if($this->kclass->input['itemMaterialid'][$i]>0){
				if($this->kclass->input['itemVersionid'][$i]==''){
					$e.='<li>请选择物资[ID:'.$i.']的 版本。</li>';
				}
				$m=$this->kclass->DB->queryFirst("
					SELECT wci.materialid,wci.versionid,wci.productitemid,
						m.materialno,m.title AS material,m.standard AS standard
					FROM `workcenteritem` AS wci
					LEFT JOIN `material` AS m ON (m.materialid=wci.materialid)
					WHERE wci.killed=0 AND wci.materialid='".$this->kclass->input['itemMaterialid'][$i]."' AND wci.versionid='".$this->kclass->input['itemVersionid'][$i]."' AND wci.materialitemid='".$this->kclass->input['materialItemid'][$i]."'
				");
				if($m['materialid']!=''){
					$e.='<li>您要关联的物资 【'.$m['materialno'].'】 已经存在。</li>';
				}
			}
		}
	}
	if(isset($e)){
		$this->kclass->messager(array(
			'title' => '修改工作中心',
			'text' => '您在修改工作中心的过程中有以下错误：<ul>'.$e.'</ul>',
			'url' => 'javascript:history.back()',
			'sec' => 3
		));
	}
	$this->kclass->DB->query("
		UPDATE `workcenter` SET
			`title`='".$this->kclass->input['title']."',
			`departmentid`='".$this->kclass->input['departmentid']."',
			`outboundtypeid`='".$this->kclass->input['outboundtypeid']."',
			`quantity`='".$this->kclass->input['quantity']."',
			`workhours`='".$this->kclass->input['workhours']."',
			`efficiency`='".$this->kclass->input['efficiency']."',
			`burthen`='".$this->kclass->input['burthen']."',
			`position`='".$this->kclass->input['position']."',
			`remark`='".$this->kclass->input['remark']."'
		WHERE workcenterid='".$this->kclass->input['workcenterid']."'
	");
	//$this->kclass->updateAttachs(array('module'=>'workcenter', 'mid'=>$this->kclass->input['workcenterid']));
	$count=count($this->kclass->input['orgitemId']);
	for($m=1;$m<$count+1;$m++){
		if($this->kclass->input['itemKill'][$m]!=''){
			$this->kclass->DB->query("UPDATE `workcenteritem` SET killed=".TIMENOW.",killer='".$this->kclass->user['userid']."'  WHERE itemid='".$this->kclass->input['orgitemId'][$m]."'");
		}
	}
	for($j=$m;$j<$m+6;$j++){
		if($this->kclass->input['itemMaterialid'][$j]>0 AND $this->kclass->input['itemVersionid'][$j]>0){
			$materialitemid=$this->kclass->iif($this->kclass->input['materialItemid'][$j]>0,$this->kclass->input['materialItemid'][$j],0);
			$this->kclass->DB->query("
				INSERT INTO `workcenteritem` (materialid,versionid,materialitemid,workcenterid,created,creator)
				VALUES('".$this->kclass->input['itemMaterialid'][$j]."','".$this->kclass->input['itemVersionid'][$j]."','".$materialitemid."','".$this->kclass->input['workcenterid']."','".TIMENOW."','".$this->kclass->user['userid']."')
			");
		}
	}
	$this->kclass->messager(array(
		'title' => '修改工作中心',
		'text' => '修改工作中心 <b>'.$this->kclass->input['workcenterno'].' '.$this->kclass->input['title'].'</b> 已修改成功!',
		'url' => '/s.php?module=workcenter&action=view&workcenterid='.$this->kclass->input['workcenterid'],
		'sec' => 2
	));
}
//
function kill(){
	if($this->kclass->input['workcenterid']<=0){
		$this->kclass->boinkIt('/s.php?module=workcenterid');
	}
	if($this->kclass->input['rt']!='list' AND  $this->kclass->input['confirm'] == 0){
		$this->kclass->boinkIt('/s.php?module=workcenter&action=view&workcenterid='.$this->kclass->input['workcenterid']);
	}
	if($this->kclass->input['workcenterid'] < 0){
		$e='<li>请从正确的页面进行操作，如果此问题持续存在，请联系网站管理员。</li>';
	}
	if(isset($e)){
		$this->kclass->messager(array(
			'title' => '删除工作中心',
			'text' => '您在删除工作中心的过程中有以下错误：<ul>'.$e.'</ul>',
			'url' => 'javascript:history.back()',
			'sec' => 3
		));
	}
	if($this->kclass->input['rt']=='view'){
		$rt='&action=view&workcenterid='.$this->kclass->input['workcenterid'];
	}elseif($this->kclass->input['rt']=='list'){
		$rt='#'.$this->kclass->input['workcenterid'];
	}
	$workcenter=$this->kclass->DB->queryFirst("
		SELECT workcenterno
		FROM workcenter
		WHERE workcenterid='".$this->kclass->input['workcenterid']."'
	");
	if($workcenter){
		$this->kclass->DB->query("
			UPDATE `workcenter`
			SET killed=".TIMENOW.",killer='".$this->kclass->user['userid']."'
			WHERE workcenterid='".$this->kclass->input['workcenterid']."'
		");
		$this->kclass->messager(array(
			'title' => '删除工作中心成功',
			'text' => '工作中心 <b>'.$workcenter['workcenterno'].'</b> 已成功被标记为删除!',
			'url' => '/s.php?module=workcenter&'.$rt,
			'sec' => 2
		));
	}else{
		$this->kclass->messager(array(
			'title' => '删除工作中心失败',
			'text' => '您要删除的工作中心，不存在！',
			'url' => '/s.php?module=workcenter&action='.$rt,
			'sec' => 3
		));
	}
}

	//
	function remove(){
		if($this->kclass->input['workcenterid']<=0){
			$this->kclass->boinkIt('/s.php?module=workcenter');
		}
		$workcenter=$this->kclass->DB->queryFirst("
			SELECT workcenterno
			FROM `workcenter`
			WHERE workcenterid='".$this->kclass->input['workcenterid']."'
		");
$body=<<<EOF
<form action="/s.php?module=workcenter&action=kill" name="workcenter" method="post">
<input type="hidden" name="module" value="workcenter">
<input type="hidden" name="action" value="kill">
<input type="hidden" name="rt" value="{$this->kclass->input['rt']}">
<input type="hidden" name="workcenterid" value="{$this->kclass->input['workcenterid']}">
<table><thead>
<thead>
<tr>
<th>删除工作中心：{$workcenter['workcenterno']}</th>
</tr>
</thead>
<tbody>
<tr class="even">
<td class="middle">你确定要删除工作中心: <a href="/s.php?module=workcenter&action=view&workcenterid={$this->kclass->input['workcenterid']}" class="big bold" target="_blank">{$workcenter['workcenterno']}</a> 吗?</td>
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
		$this->kclass->page['title'] .= ' - 删除 - '.$workcenter['workcenterno'];
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 删除工作中心 - '.$workcenter['workcenterno'], 'right' => '<a href="/s.php?module=workcenter">返回列表</a>', 'body'=>$body));
	}

	//
	function revival(){
		if($this->kclass->input['workcenterid']<=0){
			$this->kclass->boinkIt('/s.php?module=workcenter');
		}
		if($this->kclass->input['rt']!='list' AND $this->kclass->input['confirm'] == 0){
			$this->kclass->boinkIt('/s.php?module=workcenter&action=view&workcenterid='.$this->kclass->input['workcenterid']);
		}
		if($this->kclass->input['workcenterid'] < 0){
			$e='<li>请从正确的页面进行操作，如果此问题持续存在，请联系网站管理员。</li>';
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '恢复工作中心',
				'text' => '您在恢复工作中心的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$workcenter=$this->kclass->DB->queryFirst("
			SELECT workcenterno
			FROM `workcenter`
			WHERE workcenterid='".$this->kclass->input['workcenterid']."'
		");
		if($this->kclass->input['rt']=='view'){
			$rt='&action=view&workcenterid='.$this->kclass->input['workcenterid'];
		}elseif($this->kclass->input['rt']=='list'){
			$rt='#'.$this->kclass->input['workcenterid'];
		}
		if($workcenter){
			$this->kclass->DB->query("
				UPDATE `workcenter`
				SET killed=0,killer=0
				WHERE workcenterid='".$this->kclass->input['workcenterid']."'
			");
			$this->kclass->messager(array(
				'title' => '恢复工作中心成功',
				'text' => '工作中心 <b>'.$workcenter['workcenterno'].'</b> 已成功被恢复！',
				'url' => '/s.php?module=workcenter'.$rt,
				'sec' => 2
			));
		}else{
			$this->kclass->messager(array(
				'title' => '恢复工作中心失败',
				'text' => '您要恢复的工作中心不存在！',
				'url' => '/s.php?module=workcenter'.$rt,
				'sec' => 3
			));
		}
	}

	//
	function restore(){
		if($this->kclass->input['workcenterid']<=0){
			$this->kclass->boinkIt('/s.php?module=workcenter');
		}
		$workcenter=$this->kclass->DB->queryFirst("
			SELECT workcenterno
			FROM `workcenter`
			WHERE workcenterid='".$this->kclass->input['workcenterid']."'
		");
$body=<<<EOF
<form action="/s.php?module=workcenter&action=revival" name="workcenter" method="post">
<input type="hidden" name="module" value="workcenter">
<input type="hidden" name="action" value="revival">
<input type="hidden" name="rt" value="{$this->kclass->input['rt']}">
<input type="hidden" name="workcenterid" value="{$this->kclass->input['workcenterid']}">
<table><thead>
<thead>
<tr>
<th>恢复工作中心：{$workcenter['workcenterno']}</th>
</tr>
</thead>
<tbody>
<tr class="even">
<td class="middle">你确定要恢复工作中心: <a href="/s.php?module=workcenter&action=view&workcenterid={$this->kclass->input['workcenterid']}" class="big bold" target="_blank">{$workcenter['workcenterno']}</a> 吗?</td>
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
		$this->kclass->page['title'] .= ' - 恢复 - '.$workcenter['workcenterno'];
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 恢复工作中心 - '.$workcenter['workcenterno'], 'right' => '<a href="/s.php?module=workcenter">返回列表</a>', 'body'=>$body));
	}
	// 工作中心物资 add
	function addMaterial(){
		$workcenterid=$this->kclass->input['workcenterid'];
		$workcenter=$this->kclass->DB->queryFirst("
			SELECT `workcenter`.*,
				m.username AS modifier,
				c.username AS creator,
				b.title AS businesstype,
				r.country,r.state,r.city
			FROM `workcenter`
			LEFT JOIN `user` AS m ON (m.userid=workcenter.modifier)
			LEFT JOIN `user` AS c ON (c.userid=workcenter.creator)
			LEFT JOIN `businesstype` AS b ON (b.businesstypeid=workcenter.businesstypeid)
			LEFT JOIN `region` AS r ON (r.regionid=workcenter.regionid)
			WHERE `workcenter`.killed=0 AND `workcenter`.workcenterid ='".$workcenterid."'
			ORDER BY `workcenter`.workcenterid ASC,`workcenter`.modified DESC,`workcenter`.created DESC
			LIMIT 0,1
		");
		if($workcenter){
			$workcenter['country']=$this->kclass->iif($workcenter['country'],$workcenter['country'],'');
			$workcenter['state']=$this->kclass->iif($workcenter['state'],$workcenter['state'],'');
			$workcenter['city']=$this->kclass->iif($workcenter['city'],$workcenter['city'],'');
			$workcenter['created']=date('Y-n-d',$workcenter['created']);
			if($workcenter['modified']!=0)$modify='，由'.$workcenter['modifier'].'于'.date('Y-n-d',$workcenter['modified']).'修改';
			//获得工作中心的供应物资
			$workcenterMaterials=$this->kclass->DB->query("
				SELECT s.workcenterMaterialid,s.materialid,s.articleno,s.title,s.standard,s.remark,
					m.materialno,m.title AS materialTitle,m.standard AS materialStandard,
					b.title AS brand,
					u.title AS unit
				FROM workcentermaterial AS s
				LEFT JOIN material AS m ON (m.materialid=s.materialid)
				LEFT JOIN brand AS b ON (b.brandid=s.brandid)
				LEFT JOIN unit AS u ON (u.unitid=s.unitid)
				WHERE s.killed=0 AND s.workcenterid='".$workcenter['workcenterid']."'
				ORDER BY workcentermaterialid ASC
			");
			if($this->kclass->DB->numRows()){
				$workcenterMaterialList='<table class="hundred small"><thead><tr><th colspan="7">工作中心物资</th></tr></thead>
	<tbody><tr class="center" nohover><td>物资</td><td>工作中心所用编号</td><td>工作中心所用名称</td><td>工作中心所用规格</td><td>品牌</td><td>单位</td><td>备注</td></tr>';
				while($workcenterMaterial=$this->kclass->DB->fetchArray($workcenterMaterials)){
					$workcenterMaterialList.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').'">
						<td>'.$workcenterMaterial['materialno'].'<a href="/s.php?module=material&action=view&materialid='.$material['materialid'].'">　'.$workcenterMaterial['materialTitle'].'　'.$workcenterMaterial['materialStandard'].'</a></td>
						<td>'.$workcenterMaterial['articleno'].'</td>
						<td>'.$workcenterMaterial['title'].'</td>
						<td>'.$workcenterMaterial['standard'].'</td>
						<td>'.$workcenterMaterial['brand'].'</td>
						<td>'.$workcenterMaterial['unit'].'</td>
						<td>'.$workcenterMaterial['remark'].'</td></tr>';
				}
				$workcenterMaterialList.='</tbody></table>';
			}

			for($i=1;$i<=5;$i++){
				$unit=$this->kclass->chooserUnit(array('name'=>'unitid['.$i.']','hasBlank'=>1,'topname'=>'请选择','selectedid'=>5));
				$brand=$this->kclass->chooserBrand(array('name'=>'brandid['.$i.']','hasBlank'=>1,'topname'=>'请选择'));
				$material=$this->kclass->chooserMaterial(array('hName'=>'itemMaterialid['.$i.']','hId'=>'itemMaterialid'.$i,'name'=>'itemMaterial['.$i.']','id'=>'itemMaterial'.$i,'width'=>400,'line'=>$i));
				$class=$this->kclass->iif($i%2==0,'class="even center"','class="odd center"');
				$itemList.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').'">
					<td>'.$material.'</td>
					<td><input type="text" name="articleno['.$i.']"></td>
					<td><input type="text" name="title['.$i.']"></td>
					<td><input type="text" name="standard['.$i.']"></td>
					<td>'.$brand.'</td>
					<td>'.$unit.'</td>
					<td><input type="text" name="remark['.$i.']" style="width:150px;"></td>
				</tr>';
			}

$body=<<<EOF
<div class="title"><span class="right small gray">由{$workcenter['creator']}于{$workcenter['created']}建立{$modify}。</span>{$workcenter['title']}</div>
{$workcenterMaterialList}
<form action="/s.php?module=workcenter&action=insertMaterial" name="workcenter" method="post">
<input type="hidden" name="module" value="workcenter" />
<input type="hidden" name="action" value="insertMaterial" />
<input type="hidden" name="workcenterid" value="{$workcenterid}" />
<table class="hundred small">
<thead></tr><th colspan="7">供应物资信息</th></tr></thead>
<tbody>
<tr class="center even" nohover><td>物资</td><td>工作中心所用编号</td><td>工作中心所用名称</td><td>工作中心所用规格</td><td>品牌</td><td>单位</td><td>备注</td></tr>
	{$itemList}
<tr class="even">
	<td class="small gray" colspan="8">
		注意事项：
		如果所列表格不够，那么在提交保存后再添加物资</td>
</tr>
</tbody>
</table>
<table class="tlist tsuite" align="center" width="100%">
<tbody>
<tr class="odd" nohover>
<td class="center"><input type="submit" value="  提交  " accesskey="s""><input type="reset" value="  复位  "/></td>
</tr>
</tbody>
</table>
</form>
EOF;
		}
		$this->kclass->tbline+=4;
		$this->kclass->page['title'].='新建工作中心供应的物资';
		$this->kclass->page['onload'].='dc.tabhover();';
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - <a href="/s.php?module=workcenter&action=view&workcenterid='.$workcenterid.'">查看工作中心</a> - 新建工作中心供应的物资', 'right'=>'<a href="/s.php?module=workcenter&action=view&workcenterid='.$workcenterid.'">返回查看工作中心</a>','body'=>$body));
	}
	// insert
	function insertMaterial(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e='<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '新建工作中心供应的物资',
				'text' => '您在新建工作中心供应的物资的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}

		$count=count($this->kclass->input['materialid']);
		for($i=1;$i<$count+1;$i++){
			if($this->kclass->input['materialid'][$i]>0){
				//insert workcenter material item
				$this->kclass->DB->query("
					INSERT INTO workcentermaterial (materialid,workcenterid,articleno,title,standard,brandid,unitid,remark,created,creator)
					VALUES ('".$this->kclass->input['materialid'][$i]."','".$this->kclass->input['workcenterid']."','".$this->kclass->input['articleno'][$i]."','".$this->kclass->input['title'][$i]."','".$this->kclass->input['standard'][$i]."','".$this->kclass->input['brandid'][$i]."','".$this->kclass->input['unitid'][$i]."','".$this->kclass->input['remark'][$i]."','".TIMENOW."','".$this->kclass->user['userid']."')
				");
			}
		}

		$this->kclass->messager(array(
			'title' => '新增工作中心供应的物资',
			'text' => '工作中心供应的物资  已新建成功!',
			'url' => '/s.php?module=workcenter&action=view&workcenterid='.$this->kclass->input['workcenterid'],
			'sec' => 2
		));
	}
	//
	function removeMaterial(){
		if($this->kclass->input['workcenterMaterialid']<=0){
			$e='<li>系统数据错误，很抱歉！</li>';
		}
		$material=$this->kclass->DB->queryFirst("SELECT material.materialno,workcentermaterial.workcenterid FROM workcentermaterial LEFT JOIN material ON (material.materialid=workcentermaterial.materialid) WHERE workcenterMaterialid='".$this->kclass->input['workcenterMaterialid']."' LIMIT 0,1");
		if(!$material){
			$e.='<li>系统数据错误，很抱歉！</li>';
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '删除工作中心供应的物资',
				'text' => '您在删除工作中心供应的物资的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}

		$this->kclass->DB->query("UPDATE workcentermaterial SET killed='".TIMENOW."',killer='".$this->kclass->user['userid']."' WHERE workcenterMaterialid='".$this->kclass->input['workcenterMaterialid']."'");

		$this->kclass->messager(array(
			'title' => '删除工作中心供应的物资',
			'text' => '工作中心供应的物资 <b>'.$material['materialno'].'</b> 已删除成功!',
			'url' => '/s.php?module=workcenter&action=view&workcenterid='.$material['workcenterid'],
			'sec' => 2
		));
	}

/***********************private function***************************************/
	//
	function _chooserworkcenterMaterial($b){
		if($b['orderby'] == ''){
			$b['orderby']='ordering';
		}
		if($b['direction'] == ''){
			$b['direction']='ASC';
		}
		if($b['width'] > 0){
			$b['width']=' style="width: '.$b['width'].'px;"';
		}

		$workcenterMaterials=$this->kclass->DB->query("
			SELECT `workcentermaterial`.*,
				`material`.materialno,`material`.title AS materialTitle,`material`.standard AS materialStandard 
			FROM `workcentermaterial`
			LEFT JOIN `material` ON (`material`.materialid=`workcentermaterial`.materialid)
			WHERE `workcentermaterial`.killed=0 AND `workcentermaterial`.workcenterid='".$b['workcenterid']."'
			ORDER BY ".$b['orderby']." ".$b['direction']."
		");
		$r='<select id="'.$b['name'].'" name="'.$b['name'].'"'.$b['width'].'>';
		if($this->kclass->DB->numRows()){
			if($b['hasBlank']){
				$r.='<option value="0">'.$b['topname'].'</option>';
			}
			while($workcenterMaterial=$this->kclass->DB->fetchArray($workcenterMaterials)){
				$r.='<option value="'.$workcenterMaterial['materialid'].'" ';
				if($b['selectedid']==$workcenterMaterial['materialid']){
					$r.='selected';
				}
				$r.='>'.$workcenterMaterial['materialno'].'　'.$workcenterMaterial['materialTitle'].'　'.$workcenterMaterial['materialStandard'].'</option>';
			}
		}
		$r .= '</select>';

		return $r;
	}
	// 包装组可以看到相关的包装清单
	function _relatedPackinglist($b){
		$packinglists=$this->kclass->DB->query("
			SELECT pl.packinglistid,pl.packinglistno,pl.type,pl.module,pl.mid,pl.cartonCount,pl.modified,pl.created,
				c.customerid,c.title AS customer,
				m.realname AS modifier,mem.realname AS creator
			FROM `packinglist` AS pl
			LEFT JOIN `customer` AS c ON (c.customerid=pl.customerid)
			LEFT JOIN `member` AS m ON (m.userid=pl.modifier)
			LEFT JOIN `member` AS mem ON (mem.userid=pl.creator)
			WHERE pl.type='produce'
			ORDER BY created DESC
		");
		if($this->kclass->DB->numRows()){
			$i=1;
			$r['panel']='<table class="hundred"><thead><tr><th width="15">ID</th><th>包装清单号</th><th>类型</th><th>订单/样品单号</th><th>客户</th><th>总箱数</th><th>建立时间</th></tr></thead><tbody>';
			while($packinglist=$this->kclass->DB->fetchArray($packinglists)){
				if($packinglist['module']=='order'){
					$m='订单';
					$order=$this->kclass->DB->queryFirst("SELECT orderno FROM `order` WHERE orderid={$packinglist['mid']}");
					$no='<a href="/s.php?module=order&action=view&orderid='.$packinglist['mid'].'">'.$order['orderno'].'</a>';
				}elseif($packinglist['module']=='sample'){
					$m='样品单';
					$sample=$this->kclass->DB->queryFirst("SELECT sampleno FROM `sample` WHERE sampleid={$packinglist['mid']}");
					$no='<a href="/s.php?module=sample&action=view&sampleid='.$packinglist['mid'].'">'.$sample['sampleno'].'</a>';
				}
				$r['panel'].='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').'">
					<td>'.$i.'</td>
					<td><a href="/s.php?module=packinglist&action=view&packinglistid='.$packinglist['packinglistid'].'">'.$packinglist['packinglistno'].'</a></td>
					<td>'.$m.'</td>
					<td>'.$no.'</td>
					<td><a herf="/s.php?module=customer&action=view&customerid='.$packinglist['customerid'].'">'.$packinglist['customer'].'</a></td>
					<td>'.$packinglist['cartonCount'].'</td>
					<td>'.date('Y-m-d',$packinglist['created']).'</td>
				</tr>';
				$i++;
			}
			$r['panel'].='</tbody></table>';
		}
		
		return $r;
	}
}
?>