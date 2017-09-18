<?php
//
class inbound{
	var $kclass;
	var $inboundType=array(array('id'=>1,'title'=>'进仓'),array('id'=>2,'title'=>'良品退料(非BOM)'),array('id'=>3,'title'=>'订单入库'),array('id'=>4,'title'=>'收料'),array('id'=>5,'title'=>'良品退料(BOM)'),array('id'=>6,'title'=>'样品入库'),array('id'=>7,'title'=>'不良品退料'),array('id'=>8,'title'=>'收料（非生产）'));
	var $day=26;// 转换为下月1号的日期

	//
	function autorun(){
		$this->baseurl='<a href="/s.php?">首页</a>';
		switch($this->kclass->input['action']){
			case 'list':
				return $this->mmlist();
				break;
			case 'find':
				return $this->find();
				break;
			case 'dofind':
				return $this->dofind();
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
			case 'verify':
				return $this->verify();
				break;
			case 'doverify':
				return $this->doverify();
				break;
			case 'confirm':
				return $this->confirm();
				break;
			case 'doconfirm':
				return $this->doconfirm();
				break;
			case 'in':
				return $this->in();
				break;
			case 'doin':
				return $this->doin();
				break;
			case 'cancelIn':
				return $this->cancelIn();
				break;
			case 'docancelIn':
				return $this->docancelIn();
				break;
			case 'perBarcode':
				return $this->perBarcode();
				break;
			case 'perin':
				return $this->perin();
				break;
			case 'doperin':
				return $this->doperin();
				break;
			case 'inAll':
				return $this->inAll();
				break;
			case 'doinAll':
				return $this->doinAll();
				break;
			case 'evaluate':
				return $this->evaluate();
				break;
			default:
				return $this->mmlist();

		}
	}

	// main page,shows all the inbound info list
	function mmlist(){
		if($this->kclass->input['show']=='all'){
			$condition='1=1';
		}elseif($this->kclass->input['show']=='verify'){
			$condition='`inbound`.ifVerify=1';
		}elseif($this->kclass->input['show']=='unverify'){
			$condition='`inbound`.ifVerify=-1';
		}else{
			$condition='`inbound`.killed=0';
		}
		if($this->kclass->user['departmentid']!=9){
			$department=$this->kclass->user['departmentid'];
			$chlidDepartments=$this->kclass->DB->query("SELECT departmentid FROM department WHERE parentid='".$this->kclass->user['departmentid']."'");
			if($counter=$this->kclass->DB->numRows()){
				$i=1;
				$department.=',';
				while($chlidDepartment=$this->kclass->DB->fetchArray($chlidDepartments)){
					if($i<$counter){
						$department.=$chlidDepartment['departmentid'].',';
					}else{
						$department.=$chlidDepartment['departmentid'];
					}
					$i++;
				}
			}
			$condition.=' AND `inbound`.departmentid IN ('.$department.')';
		}else{
			$condition.=' AND (`inbound`.ifVerify=1 OR `inbound`.departmentid=9)';
		}

		if($this->kclass->input['start']!=''){
			$start=$this->kclass->input['start'];
			$query['start']=$this->kclass->input['start'];
		}else{
			$start=0;
		}
		$perpage=20;
		if($this->kclass->input['orderby']!='')$query['orderby']=$this->kclass->input['orderby'];
		if($this->kclass->input['direction']!='')$query['direction']=$this->kclass->input['direction'];
		if($this->kclass->input['show']!='')$query['show']=$this->kclass->input['show'];
		if($this->kclass->input['layout']!='')$query['layout']=$this->kclass->input['layout'];
		$orderby=$this->kclass->orderby(array('module'=>'inbound','direction'=>'desc','orderby'=>'created', 'default'=>'created', 'serial'=>array(array('title'=>'编号', 'field'=>'inboundno','word'=>'no'), array('title'=>'修改时间', 'field'=>'modified'), array('title'=>'建立时间', 'field'=>'created')),'appendUrl'=>$query));
		if(is_array($query) AND count($query)>0)$queryPart='&'.http_build_query($query);

		$filterItem=$this->kclass->filterItem(array('module'=>'inbound','inboundType'=>$this->inboundType));
		// 筛选
		$filter=$this->kclass->filter(
			array('module'=>'inbound','action'=>'list','table'=>'inbound','url'=>$queryPart,'serial'=>array(
				array('name'=>'ifVerify','dname'=>'审核状态','status'=>$filterItem['verify']),
				array('name'=>'ifComplete','dname'=>'入库状态','status'=>$filterItem['complete']),
				array('name'=>'typeid','dname'=>'入库类型','status'=>$filterItem['inboundType']),
				array('name'=>'departmentid','dname'=>'部门','status'=>$filterItem['department']),
				array('name'=>'month','dname'=>'月份','status'=>$filterItem['month'])
		)));

		$inboundList=$this->kclass->listInbound(array('start'=>$start,'prepage'=>$perpage,'condition'=>$condition,'orderby'=>$query['orderby'],'filter'=>$filter['link']));
		$body.=$inboundList['body'];
		$prestart=$start-$perpage;
		$body.='<div class="clear"><span class="gray small left">(共<span class="darkred">'.$inboundList['counter'].'</span>组记录)</span></div>';
		if($prestart>=0)$prev='<a href="'.$filter['uri'].'&orderby='.$query['orderby'].'&direction='.$query['direction'].'&start='.$prestart.'"><-前一页</a>　';
		$aftstart=$start+$perpage;
		if($aftstart<$inboundList['counter'])$after='　<a href="'.$filter['uri'].'&orderby='.$query['orderby'].'&direction='.$query['direction'].'&start='.$aftstart.'">后一页-></a>';
		$body.='<div class="clear center"><span class=" middle bold">'.$prev.$after.'</span></div>';

		if($this->kclass->input['layout']=='grid'){
			$layoutLink='<a href="/s.php?module=inbound&action=list&layout=list'.str_replace('&layout=grid', '', $queryPart).'">列表</a> 格子';
		}else{
			$layoutLink='列表 <a href="/s.php?module=inbound&action=list&layout=grid'.str_replace('&layout=list', '', $queryPart).'">格子</a>';
		}
		if($this->kclass->input['show']=='all'){
			$showLink='<a href="/s.php?module=inbound&action=list'.str_replace('&show=all', '', $queryPart).'">默认</a> 所有<span class="small gray">(包括删除)</span> <a href="/s.php?module=inbound&action=list&show=verify'.str_replace('&show=all', '', $queryPart).'">已通过</a>  <a href="/s.php?module=inbound&action=list&show=inverify'.str_replace('&show=all', '', $queryPart).'">待审核</a>  <a href="/s.php?module=inbound&action=list&show=unverify'.str_replace('&show=all', '', $queryPart).'">未通过</a>';
		}elseif($this->kclass->input['show']=='verify'){
			$showLink='<a href="/s.php?module=inbound&action=list'.str_replace('&show=verify', '', $queryPart).'">默认</a> <a href="/s.php?module=inbound&action=list&show=all'.str_replace('&show=verify', '', $queryPart).'">所有<span class="small gray">(包括删除)</span></a> 已通过 <a href="/s.php?module=inbound&action=list&show=inverify'.str_replace('&show=verify', '', $queryPart).'">待审核</a>  <a href="/s.php?module=inbound&action=list&show=unverify'.str_replace('&show=verify', '', $queryPart).'">未通过</a>';
		}elseif($this->kclass->input['show']=='inverify'){
			$showLink='<a href="/s.php?module=inbound&action=list'.str_replace('&show=inverify', '', $queryPart).'">默认</a> <a href="/s.php?module=inbound&action=list&show=all'.str_replace('&show=inverify', '', $queryPart).'">所有<span class="small gray">(包括删除)</span></a> <a href="/s.php?module=inbound&action=list&show=verify'.str_replace('&show=inverify', '', $queryPart).'">已通过</a> 待审核 <a href="/s.php?module=inbound&action=list&show=unverify'.str_replace('&show=inverify', '', $queryPart).'">未通过</a>';
		}elseif($this->kclass->input['show']=='unverify'){
			$showLink='<a href="/s.php?module=inbound&action=list'.str_replace('&show=unverify', '', $queryPart).'">默认</a> <a href="/s.php?module=inbound&action=list&show=all'.str_replace('&show=unverify', '', $queryPart).'">所有<span class="small gray">(包括删除)</span></a> <a href="/s.php?module=inbound&action=list&show=inverify'.str_replace('&show=unverify', '', $queryPart).'">已通过</a> <a href="/s.php?module=inbound&action=list&show=inverify'.str_replace('&show=unverify', '', $queryPart).'">待审核</a> 未通过';
		}else{
			$showLink='默认 <a href="/s.php?module=inbound&action=list&show=all'.str_replace('&show=all', '', $queryPart).'">所有<span class="small gray">(包括删除)</span></a> <a href="/s.php?module=inbound&action=list&show=verify'.str_replace('&show=all', '', $queryPart).'">已通过</a>  <a href="/s.php?module=inbound&action=list&show=inverify'.str_replace('&show=all', '', $queryPart).'">待审核</a>  <a href="/s.php?module=inbound&action=list&show=unverify'.str_replace('&show=all', '', $queryPart).'">未通过</a>';
		}
		$this->kclass->page['title'].='入库单列表';
		$this->kclass->page['onload'].="dc.listhover();dc.tips();dc.tabhover();$('.tablesorter').tablesorter( {sortList: [[0,0]], headers: { 8: { sorter: false} }} );$('.mytable').fixedtableheader();";
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - 入库单列表','right' => '<span class="small">排序：</span>'.$orderby['link'].'<span class="small">显示方式：</span>'.$showLink.'　|　<span class="small">布局：</span>'.$layoutLink.'　|　<a href="/s.php?module=inbound&action=add">新建</a>','body'=>$filter['panel'].$body));
	}
	//
	function find(){
		if($this->kclass->input['departmentid']<=0 AND $this->kclass->input['applicant']=='' AND $this->kclass->input['date']=='' AND $this->kclass->input['month']<=0){
			$department=$this->kclass->chooserDepartment(array('name'=>'departmentid','width'=>250,'hasBlank'=>1,'topname'=>' '));
			$body=<<<EOF
<form action="/s.php?module=inbound&action=find" method="get">
<input type="hidden" name="module" value="inbound">
<input type="hidden" name="action" value="find">
<table style="width:450px">
<thead><tr><th colspan=2>根据条件查找物资</th></tr></thead>
<tbody>
<tr class="even">
<td width="100">部　　门：</td><td>{$department}</td>
</tr>
<tr class="odd">
<td width="100">申 请 人：</td><td><input type="text" name="applicant" style="width:300px"></td>
</tr>
<tr class="even">
<td width="100">日　　期：</td><td><input type="text" name="date" id="date" style="width:300px"></td>
</tr>
<tr class="even">
<td width="100">月　　份：</td><td>
<select name="month" style="width:300px">
<option value="0">请选择</option>
<option value="1">一月</option>
<option value="2">二月</option>
<option value="3">三月</option>
<option value="4">四月</option>
<option value="5">五月</option>
<option value="6">六月</option>
<option value="7">七月</option>
<option value="8">八月</option>
<option value="9">九月</option>
<option value="10">十月</option>
<option value="11">十一月</option>
<option value="12">十二月</option>
</select></td>
</tr>
<tr class="even">
<td class="small gray" colspan="9">
注意事项：<br>
①查找采购计划单时，可以通过输入“计划单编号”直接查找，也可以通过输入“日期”和“物料编号”进行查找<br>
②请保证输入的值无误</td>
</tr>
<tr class="odd" nohover>
<td class="center" colspan=2><input type="submit" id="submitButton" value="  提交  "><input type="reset" value="  复位  "/></td>
</tr>
</tbody>
</table>
</form>
EOF;
			$this->kclass->page['title'].='查找出库';
			$this->kclass->page['onload'].='$(\'#date\').datepicker()';
			$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - 查找物资','right' => '<a href="/s.php?module=inbound">查看列表</a>','body'=>$body));
		}else{
			if($this->kclass->input['departmentid']>0){
				$condition1.=' AND departmentid='.$this->kclass->input['departmentid'];
			}
			if($this->kclass->input['applicant']!=''){
				$condition1.=' AND applicant="'.$this->kclass->input['applicant'].'"';
			}
			if($this->kclass->input['date']!=''){
				$sd=explode('-',$this->kclass->input['date']);
				$dateline=mktime(0,0,0,$sd[1],$sd[2],$sd[0]);
				$condition2.=' AND ii.created>'.$dateline.' AND ii.created<='.($dateline+86400);
			}
			if($this->kclass->input['month']>0){
				$year=date('Y',TIMENOW);
				$first=strtotime(date('Y-m-d', mktime(0,0,0,$this->kclass->input['month'],1,$year)));
				$last=strtotime(date('Y-m-t', mktime(0,0,0,$this->kclass->input['month'],1,$year)));
				$condition2.=' AND ii.created>'.$first.' AND ii.created<='.$last;
			}
			$inbounds=$this->kclass->DB->query("
				SELECT inboundid,applicant,inboundno
				FROM inbound
				WHERE killed=0 $condition1
				ORDER BY created ASC
			");
			if($this->kclass->DB->numRows()){
				$quantity=0;
				$materialId=array();
				$i=0;
				while($inbound=$this->kclass->DB->fetchArray($inbounds)){
					$items=$this->kclass->DB->query("
						SELECT ii.*,
							m.materialno,m.title AS material,m.standard,m.ifPerBarcode,
							mv.title AS version,
							bc.barcode,
							bt.batchno,
							u.title AS unit
						FROM inbounditem AS ii
						LEFT JOIN material AS m ON (m.materialid=ii.materialid)
						LEFT JOIN materialversion AS mv ON (mv.versionid=ii.versionid)
						LEFT JOIN barcode AS bc ON (bc.barcodeid=ii.barcodeid)
						LEFT JOIN batch AS bt ON (bt.batchid=ii.batchid)
						LEFT JOIN unit AS u ON (u.unitid=ii.unitid)
						WHERE ii.killed=0 AND ii.inboundid='".$inbound['inboundid']."' $condition2
						ORDER BY inboundid ASC,itemid ASC
					");
					$id=array();
					if($this->kclass->DB->numRows()){
						while($item=$this->kclass->DB->fetchArray($items)){
							if(in_array($item['materialid'],$id)){
								$quantity+=$item['quantity'];
							}else{
								$id[]=$item['materialid'];
								$quantity=$item['quantity'];
								$result[$item['materialid']]=$item;
							}
							if(!in_array($item['materialid'],$materialId)){
								$materialId[]=$item['materialid'];
							}
						}
					}
					foreach($id as $key=>$val){
						$i+=1;
						$itemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').' small">
						<td>'.$i.'</td>
						<td><a href="/s.php?module=inbound&action=view&inboundid='.$inbound['inboundid'].'">'.$inbound['inboundno'].'</a></td>
						<td><a href="/s.php?module=barcode&action=view&barcodeid='.$result[$val]['barcodeid'].'">'.$result[$val]['barcode'].'</a></td>
						<td><a href="/s.php?module=batch&action=view&batchid='.$result[$val]['batchid'].'">'.$result[$val]['batchno'].'</a></td>
						<td><a href="/s.php?module=material&action=view&materialid='.$result[$val]['materialid'].'">'.$result[$val]['materialno'].'</a>'.'　'.$result[$val]['material'].'　'.$result[$val]['standard'].'</td>
						<td>'.$result[$val]['version'].'</td>
						<td>'.$quantity.' '.$result[$val]['unit'].'</td>
						<td>'.$inbound['applicant'].'</td>
						<td>'.date('Y-m-d H:j:s',$result[$val]['created']).'</td>
						<td>'.$result[$val]['remark'].'</td>
						</tr>';
					}
				}
			}
			$count=count($materialId);
$body=<<<EOF
<table class="hundred">
<thead><tr class="even center"><th width="15">ID</th><th width="50">入库单号</th><th width="170">条码</th><th width="60">批次编号</th><th width="250">物资</th><th width="30">版本</th><th width="50">入库数量</th><th width="40">申请人</th><th width="100">入库时间</th><th width="70">备注</th></tr></thead><tbody>
{$itemtr}
<tr class="middle"><td colspan=2>物资种类　总计</td><td class="bold" colspan=8>{$count}</td></tr>
</tbody>
</table>
EOF;
			$this->kclass->page['title'].='入库统计';
			$this->kclass->page['onload'].='$(\'#date\').datepicker()';
			$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - 查找物资','right' => '<a href="/p.php?module=printer&action=inbound&inboundid">打印报表</a>　<a href="/s.php?module=inbound">查看列表</a>','body'=>$body));
		}
	}
	//
	function view(){
		$inbound=$this->kclass->DB->queryFirst("
			SELECT i.inboundid,i.inboundno,i.applicant,i.orderno,i.supplier,i.porderid,i.orderid,i.sampleid,i.arrivalno,i.purpose,i.arrivalTime,i.remark,i.typeid,i.departmentid,i.workcenterid,i.verifyRemark,i.ifVerify,i.verified,i.ifConfirm,i.modified,i.created,i.killed,i.ifComplete,
				d.title AS department,
				t.title AS type,
				wc.title AS workcenter,
				u.username AS creator,
				us.username AS modifier,
				ur.username AS verifier
			FROM inbound AS i
			LEFT JOIN `inorouttype` AS t ON (t.typeid=i.typeid)
			LEFT JOIN `department` AS d ON (d.departmentid=i.departmentid)
			LEFT JOIN `workcenter` AS wc ON (wc.workcenterid=i.workcenterid)
			LEFT JOIN `user` AS u ON (u.userid=i.creator)
			LEFT JOIN `user` AS us ON (us.userid=i.modifier)
			LEFT JOIN `user` AS ur ON (ur.userid=i.verifier)
			WHERE i.inboundid='".$this->kclass->input['inboundid']."'
			LIMIT 0,1
		");
		if($inbound){
			$inbound['created']=date('Y-m-d H:i:s',$inbound['created']);
			$inbound['arrivalTime']=date('Y-m-d',$inbound['arrivalTime']);
			$confirm=$this->kclass->confirm($inbound['ifConfirm']);
			$verify=$this->kclass->verify($inbound['ifVerify']);
			$inStatus='';
			if($inbound['ifComplete']==1){
				$inStatus='部分入库';
			}elseif($inbound['ifComplete']==2){
				$inStatus='入库完成';
			}else{
				$inStatus='等待入库';
			}
			if($inbound['modified']!=0)$modify='，由'.$inbound['modifier'].'于'.date('Y-m-d H:i:s',$inbound['modified']).'修改';
			if($inbound['verified']!=0)$iverify='，由'.$inbound['verifier'].'于'.date('Y-m-d H:i:s',$inbound['verified']).'审核';
			// 确定和审核状态的不同，操作的不同
			if($inbound['ifConfirm']==1){
				if($inbound['ifVerify']==0){
					$operate='<a href="/s.php?module=inbound&action=verify&inboundid='.$inbound['inboundid'].'">审核</a>　|　<a href="/s.php?module=inbound&action=update&inboundid='.$inbound['inboundid'].'">修改</a>　<a href="/s.php?module=inbound&action=remove&inboundid='.$inbound['inboundid'].'&rt=view">删除</a>';
				}else{
					$operate='<a href="/s.php?module=inbound&action=update&inboundid='.$inbound['inboundid'].'">修改</a>　';
					$rv='<tr class="odd"><td>审核备注：</td><td>'.$inbound['verifyRemark'].'</td><td></td><td></td></tr>';
				}
			}else{
				$operate='<a href="/s.php?module=inbound&action=confirm&inboundid='.$inbound['inboundid'].'">确认</a>　|　<a href="/s.php?module=inbound&action=update&inboundid='.$inbound['inboundid'].'">修改</a>　';
			}

			if($inbound['porderid']>0){
				$porder=$this->kclass->DB->queryFirst("SELECT o.porderid,o.orderno,o.supplierid,s.title AS supplier FROM porder AS o LEFT JOIN supplier AS s ON (s.supplierid=o.supplierid) WHERE o.porderid=".$inbound['porderid']." LIMIT 0,1");
				$r='<tr class="even"><td>订单编号：</td><td><a href="/s.php?module=porder&action=view&porderid='.$porder['porderid'].'">'.$porder['orderno'].'</a></td><td>供　应　商：</td><td><a href="/s.php?module=supplier&action=view&supplierid='.$porder['supplierid'].'">'.$porder['supplier'].'</a></td></tr>
						<tr class="odd"><td>送货单号：</td><td>'.$inbound['arrivalno'].'</td><td>到达时间：</td><td>'.$inbound['arrivalTime'].'</td></tr>';
			}elseif($inbound['orderid']>0){
				$order=$this->kclass->DB->queryFirst("SELECT orderid,orderno FROM `order` WHERE orderid=".$inbound['orderid']." LIMIT 0,1");
				$r='<tr class="even"><td>订单编号：</td><td><a href="/s.php?module=order&action=view&orderid='.$order['orderid'].'">'.$order['orderno'].'</a></td><td></td><td></td></tr>';
			}elseif($inbound['sampleid']>0){
				$sample=$this->kclass->DB->queryFirst("SELECT sampleid,sampleno FROM sample WHERE sampleid=".$inbound['sampleid']." LIMIT 0,1");
				$r='<tr class="even"><td>样品单编号：</td><td><a href="/s.php?module=sample&action=view&sampleid='.$sample['sampleid'].'">'.$sample['sampleno'].'</a></td><td></td><td></td></tr>';
			}elseif($inbound['typeid']==2){
				$r='<tr class="even"><td>订单编号：</td><td>'.$inbound['orderno'].'</td><td>供　应　商：</td><td>'.$inbound['supplier'].'</td></tr>
						<tr class="odd"><td>送货单号：</td><td>'.$inbound['arrivalno'].'</td><td>到达时间：</td><td>'.$inbound['arrivalTime'].'</td></tr>';
			}
			
			//已入库明细
			$inboundItems=$this->kclass->DB->query("
				SELECT ii.itemid,ii.batchid,ii.barcodeid,ii.materialid,ii.materialitemid,ii.versionid,ii.created,
					bt.batchno,
					bc.barcode,
					mv.title AS version,
					mem.realname AS creator
				FROM inbounditem AS ii
				LEFT JOIN batch AS bt ON (ii.batchid=bt.batchid)
				LEFT JOIN barcode AS bc ON (bc.barcodeid=ii.barcodeid)
				LEFT JOIN material AS m ON (m.materialid=ii.materialid)
				LEFT JOIN materialversion AS mv ON (mv.versionid=ii.versionid)
				LEFT JOIN preinbounditem AS pii ON (pii.itemid=ii.preitemid)
				LEFT JOIN member AS mem ON (mem.userid=ii.creator)
				WHERE ii.killed=0 AND ii.inboundid='".$this->kclass->input['inboundid']."'
				ORDER BY m.materialno ASC
			");
			if($this->kclass->DB->numRows()){
				$i=1;
				$inboundItemtr='<table class="hundred"><thead><tr><th colspan="17">批次列表 <a class="right" href="s.php?module=inbound&action=cancelIn&type=all&inboundid='.$this->kclass->input['inboundid'].'" onclick="return confirm(\'你确定要对该入库单 '.$inbound['inboundno'].' 进行取消入库吗？\');">全部取消入库</a></th></tr></thead><tbody class="small"><tr class="even center"><td width="15">ID</td><td width="190">条码</td><td width="80">批次编号</td><td>物资</td><td width="40">签收人</td><td width="60">批号时间</td><td width="40">已入数</td><td width="120">入库时间</td><td width="270"></td><td width="120"></td></tr>';
				while($inboundItem=$this->kclass->DB->fetchArray($inboundItems)){
					$in=$this->kclass->DB->queryFirst("SELECT SUM(quantity) AS count FROM inbounditem WHERE killed=0 AND itemid='".$inboundItem['itemid']."'");
					$batch=$this->kclass->DB->queryFirst("SELECT * FROM batch WHERE killed=0 AND batchid='".$inboundItem['batchid']."'");
					$materialInfo=$this->kclass->getMaterial(array('materialid'=>$inboundItem['materialid'],'itemid'=>$inboundItem['materialitemid']));
					if($materialInfo['ifPerBarcode']==1);{
						$status='';
						$startno=$this->kclass->DB->queryFirst("
							SELECT number
							FROM childBarcode 
							WHERE versionid={$inboundItem['versionid']} AND inboundid={$inbound['inboundid']} 
							ORDER BY title ASC 
							LIMIT 0,1
						");
						$endno=$this->kclass->DB->queryFirst("
							SELECT number
							FROM childBarcode 
							WHERE versionid={$inboundItem['versionid']} AND inboundid={$inbound['inboundid']} 
							ORDER BY title DESC 
							LIMIT 0,1
						");
						$childBarcode=$this->kclass->DB->queryFirst("SELECT COUNT(*) AS count FROM childBarcode WHERE versionid={$inboundItem['versionid']} AND inboundid={$inbound['inboundid']} AND barcodeid={$inboundItem['barcodeid']} LIMIT 0,1");
						$info='共<b>'.$this->kclass->iif($childBarcode['count'],$childBarcode['count'],0).'</b>条';
						if($childBarcode['count']!=$in['count']){
							$quantity=$in['count']-$childBarcode['count'];
							$status='<a href="javascript:dc.barcode.addBarcode(\'item'.$i.'\')">生成流水条码</a>';
						}else{
							$status='生成完成';
						}
					}
					$inboundItemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').'" id="item'.$i.'">
						<td><input type="hidden" name="itemid" value="'.$inboundItem['itemid'].'">
							<input type="hidden" name="versionid" value="'.$inboundItem['versionid'].'">
							<input type="hidden" name="quantity" value="'.$quantity.'">
							<input type="hidden" name="barcodeid" value="'.$inboundItem['barcodeid'].'">'.$i.'</td>
						<td><a href="/s.php?module=barcode&action=view&barcodeid='.$inboundItem['barcodeid'].'">'.$inboundItem['barcode'].'</a></td>
						<td><a href="/s.php?module=batch&action=view&batchid='.$inboundItem['batchid'].'">'.$inboundItem['batchno'].'</a></td>
						<td><a href="'.$materialInfo['url'].'">'.$materialInfo['no'].'</a>　'.$materialInfo['material'].'　'.$materialInfo['standard'].'</td>
						<td>'.$inboundItem['creator'].'</td>
						<td>'.date('Y-m-d',$batch['dateline']).'</td>
						<td>'.$this->kclass->iif($in['count'],$in['count'],0).'</td>
						<td>'.date('Y-m-d H:i:s',$inboundItem['created']).'</td>
						<td>范围：<em class="startno">'.$this->kclass->iif($startno['number'],str_pad($startno['number'],4,'0',STR_PAD_LEFT),'0000').'</em> — <em class="endno">'.$this->kclass->iif($endno['number'],str_pad($endno['number'],4,'0',STR_PAD_LEFT),'0000').'</em> <span class="info">'.$info.'</span>　<span class="status">'.$status.'</span></td>
						<td><a href="/s.php?module=batch&action=view&batchid='.$inboundItem['batchid'].'">查看详情</a>　<a target="_blank" href="/s.php?module=inbound&action=cancelIn&type=item&itemid='.$inboundItem['itemid'].'" onclick="return confirm(\'你确定要对该条明细 '.$inboundItem['materialno'].' 进行取消入库吗？\');">取消入库</a></td></tr>';
					$i++;
				}
				$inboundItemtr.='</tbody></table>';
			}
			// 即将入库明细
			$items=$this->kclass->DB->query("
				SELECT pii.*,
					mv.title AS version
				FROM preinbounditem AS pii
				LEFT JOIN material AS m ON (m.materialid=pii.materialid)
				LEFT JOIN `materialversion` AS mv ON (mv.versionid=pii.versionid)
				WHERE pii.killed=0 AND pii.module='inbound' AND pii.mid='".$this->kclass->input['inboundid']."'
				ORDER BY m.materialno ASC
			");
			if($this->kclass->DB->numRows()){
				$i=1;
				$itemList='<table class="hundred"><thead><tr><th colspan="11">物资列表'.$this->kclass->iif($inbound['ifConfirm']==1 AND $inbound['ifVerify']==1,$this->kclass->iif($inbound['ifComplete']==2,'','<a class="right" href="/s.php?module=inbound&action=inAll&inboundid='.$this->kclass->input['inboundid'].'">选择明细接收</a>'),'').'</th></tr></thead><tbody><tr class="center even"><td width="15">ID</td>'.$this->kclass->iif($inbound['workcenterid']>0,'<td width="100">任务单号</td>','').'<td width="140">物资编号</td><td>物资名称</td><td>物资规格</td><td width="60">物资版本</td><td width="90">良品数/单位</td><td width="90">不良品数/单位</td><td width="80">批号时间</td><td width="130">备注</td><td width="60"></td></tr></tbody><tbody class="small">';
				while($item=$this->kclass->DB->fetchArray($items)){
					$inboundItem=$this->kclass->DB->queryFirst("SELECT quantity FROM inbounditem WHERE killed=0 AND materialid='".$item['materialid']."' AND inboundid='".$this->kclass->input['inboundid']."' AND preitemid='".$item['itemid']."'");
					if($inbound['killed']==0 AND $inbound['ifConfirm']==1 AND $inbound['ifVerify']==1){
						if($inboundItem['quantity']==$item['qualified'] OR ($inboundItem['quantity']==$item['disqualified'] AND $item['disqualified']>0)){
							$inoperate='入库完成';
						}elseif($inboundItem['quantity'] AND ($inboundItem['quantity']<$item['qualified'] OR ($inboundItem['quantity']<$item['disqualified'] AND $item['disqualified']>0))){
							$inoperate='<a href="/s.php?module=inbound&action=in&inboundid='.$inbound['inboundid'].'&itemid='.$item['itemid'].'">继续接收</a>';
						}else{
							$inoperate='<a href="/s.php?module=inbound&action=in&inboundid='.$inbound['inboundid'].'&itemid='.$item['itemid'].'">接收</a>';
						}
					}
					$item['dateline']=date('Y-m-d',$item['dateline']);
					$materialInfo=$this->kclass->getMaterial(array('materialid'=>$item['materialid'],'itemid'=>$item['materialitemid']));
					$taskitem=$this->kclass->DB->queryFirst("
						SELECT d.taskid,d.taskno
						FROM taskitem AS t
						LEFT JOIN task AS d ON (d.taskid=t.taskid)
						WHERE t.killed=0 AND d.killed=0 AND t.itemid='".$item['taskitemid']."'
					");
					$itemList.='<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').'">
						<td>'.$i.'</td>
						'.$this->kclass->iif($inbound['workcenterid']>0,'<td><a href="/s.php?module=task&action=view&taskid='.$taskitem['taskid'].'">'.$taskitem['taskno'].'</td>','').'
						<td><a href="'.$materialInfo['url'].'">'.$materialInfo['no'].'</a></td>
						<td>'.$materialInfo['material'].'</td>
						<td>'.$materialInfo['standard'].'</td>
						<td>'.$item['version'].'</td>
						<td>'.$item['qualified'].$item['unit'].'</td>
						<td>'.$item['disqualified'].$item['unit'].'</td>
						<td>'.$item['dateline'].'</td>
						<td>'.$item['remark'].'</td>
						<td>'.$inoperate.'</td></tr>';
					$i++;
				}
				$itemList.='</tbody></table>';
			}
			$supplier = $this->kclass->relatedSupplier(array('id'=>$inbound['supplierid']));

$body=<<<EOF
<div class="title">{$inbound['inboundno']}<span class="right small gray">由{$inbound['creator']}于{$inbound['created']}建立{$modify}{$iverify}。</span></div>
<dl id="inbound" class="tabs" style="display: block;">
<dt tabid="0">基本信息</dt>
<dt title="与此入库单相关的供应商"{$supplier['off']}>供应商</dt>
<dd>
<input type="hidden" name="inboundid" value="{$inbound['inboundid']}">
<input type="hidden" name="orderid" value="{$inbound['orderid']}">
<input type="hidden" name="sampleid" value="{$inbound['sampleid']}">
<table class="hundred">
<thead><tr><th colspan="4">入库单</th></tr></thead>
<tbody>
<tr class="odd">
<td width="100">入库单编号：</td><td width="450">{$inbound['inboundno']}　（{$confirm}　{$verify}　{$inStatus}）</td>
<td width="100">申　请　人：</td><td>{$inbound['applicant']} （{$inbound['department']}）</td>
</tr>
<tr class="even">
<td>入库类型：</td><td>{$inbound['type']}</td><td></td>
<td></td>
</tr>
{$r}
<tr class="even">
<td>用　　途：</td><td>{$inbound['purpose']}</td>
<td>备　　注：</td><td>{$inbound['remark']}</td>
</tr>
{$rv}
</tbody>
</table>
{$itemList}
{$inboundItemtr}
</dd>
<dd>{$supplier['panel']}</dd>
EOF;
		}else{
				$body = '系统数据出错，很抱歉！';
		}

		$this->kclass->page['title'].='查看入库单';
		$this->kclass->page['onload']='dc.tabhover();dc.tabs({\'id\':\'inbound\'})';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - <a href="/s.php?module=inbound&action=list">入库单列表</a> - 查看入库单', 'right' => $this->kclass->iif($inbound['killed']==0,$this->kclass->iif($inbound['ifVerify']==1,'<span class = "small">打印：</span> <a href="/p.php?action=inboundApply&inboundid='.$inbound['inboundid'].'" target="_blank">入库单</a>　|　','').'<span class="small gray">操作：</span><a href="/s.php?module=inbound&action=add">新建</a>　|　'.$operate,'<a href="/s.php?module=inbound&action=restore&inboundid='.$inbound['inboundid'].'&rt=view">恢复</a>'),'body' => $body));
	}

	// add inbound
	function add(){
		if(!$this->kclass->input['typeid']){
			$type=$this->kclass->chooserInorouttype(array('name'=>'typeid','width'=>200,'module'=>'inbound'));
$body=<<<EOF
<form action="/s.php?module=inbound&action=add" method="get">
<input type="hidden" name="module" value="inbound">
<input type="hidden" name="action" value="add">
<input type="hidden" name="departmentid" value="{$this->kclass->user['departmentid']}">
<table>
<thead>
<tr>
<th colspan="2">新建入库申请：请选择入库类型</th>
</tr>
</thead>
<tbody>
<tr class="odd">
<td>选择入库类型：<span class="red bold">*</span></td>
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
			$this->kclass->page['title'].='新建入库申请单';
			$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - <a href="/s.php?module=inbound">查看入库单列表</a> - 新建入库申请单', 'right'=>'<a href="/s.php?module=inbound&action=list">返回列表</a>','body'=>$body));
		}else{
			if($this->kclass->input['typeid']==1 AND $this->kclass->input['porderid']<=0){
				$order=$this->kclass->chooserPOrder(array('name'=>'porderid','hasBlank'=>1,'width'=>450,'condition'=>' AND arrivalStatus<>2 AND ifComplete=0','orderby'=>'`porder`.orderno','direction'=>'DESC'));
$body=<<<EOF
<form action="/s.php?module=inbound&action=add" method="get">
<input type="hidden" name="module" value="inbound">
<input type="hidden" name="action" value="add">
<input type="hidden" name="departmentid" value="{$this->kclass->input['departmentid']}">
<input type="hidden" name="typeid" value="{$this->kclass->input['typeid']}">
<table>
<thead>
<tr>
<th colspan="4">新建入库申请：请选择订单</th>
</tr>
</thead>
<tbody>
<tr class="odd">
<td title="">请选择订单：<span class="red bold">*</span></td>
<td>{$order}</td>
</tr>
<tr class="odd" nohover>
<td colspan="4" class="center" nohover>
	<input type="submit" id="submitButton" value="  下一步  " accesskey="s">
	<input type="reset" value="  复位  ">
</td>
</tr>
</tbody>
</table>
</form>
EOF;
				$this->kclass->page['title'].='新建入库申请单';
				$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - <a href="/s.php?module=inbound">查看入库单列表</a> - 新建入库申请单', 'right'=>'<a href="/s.php?module=inbound&action=list">返回列表</a>','body'=>$body));
			}elseif($this->kclass->input['typeid']==7 AND $this->kclass->input['orderid']<=0){
				$order=$this->kclass->chooserOrder(array('name'=>'orderid','hasBlank'=>1,'width'=>450,'condition'=>' AND ifApprove=1 AND ifInbound<>1','orderby'=>'o.orderno','direction'=>'DESC'));
$body=<<<EOF
<form action="/s.php?module=inbound&action=add" method="get">
<input type="hidden" name="module" value="inbound">
<input type="hidden" name="action" value="add">
<input type="hidden" name="departmentid" value="{$this->kclass->input['departmentid']}">
<input type="hidden" name="typeid" value="{$this->kclass->input['typeid']}">
<table>
<thead>
<tr>
<th colspan="4">新建入库申请：请选择订单</th>
</tr>
</thead>
<tbody>
<tr class="odd">
<td title="">请选择订单：<span class="red bold">*</span></td>
<td>{$order}</td>
</tr>
<tr class="odd" nohover>
<td colspan="4" class="center" nohover>
	<input type="submit" id="submitButton" value="  下一步  " accesskey="s">
	<input type="reset" value="  复位  ">
</td>
</tr>
</tbody>
</table>
</form>
EOF;
				$this->kclass->page['title'].='新建入库申请单';
				$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - <a href="/s.php?module=inbound">查看入库单列表</a> - 新建入库申请单', 'right'=>'<a href="/s.php?module=inbound&action=list">返回列表</a>','body'=>$body));
			}elseif($this->kclass->input['typeid']==8 AND $this->kclass->input['sampleid']<=0){
				$sample=$this->kclass->chooserSample(array('name'=>'sampleid','hasBlank'=>1,'width'=>450,'condition'=>' AND ifApprove=1','orderby'=>'s.sampleno','direction'=>'DESC'));
$body=<<<EOF
<form action="/s.php?module=inbound&action=add" method="get">
<input type="hidden" name="module" value="inbound">
<input type="hidden" name="action" value="add">
<input type="hidden" name="departmentid" value="{$this->kclass->input['departmentid']}">
<input type="hidden" name="typeid" value="{$this->kclass->input['typeid']}">
<table>
<thead>
<tr>
<th colspan="4">新建入库申请：请选择样品单</th>
</tr>
</thead>
<tbody>
<tr class="odd">
<td title="">请选择样品单：<span class="red bold">*</span></td>
<td>{$sample}</td>
</tr>
<tr class="odd" nohover>
<td colspan="4" class="center" nohover>
	<input type="submit" id="submitButton" value="  下一步  " accesskey="s">
	<input type="reset" value="  复位  ">
</td>
</tr>
</tbody>
</table>
</form>
EOF;
				$this->kclass->page['title'].='新建入库申请单';
				$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - <a href="/s.php?module=inbound">查看入库单列表</a> - 新建入库申请单', 'right'=>'<a href="/s.php?module=inbound&action=list">返回列表</a>','body'=>$body));
			}else{
				$departments=$this->kclass->DB->query("SELECT departmentno,departmentid,title FROM department WHERE parentid='".$this->kclass->user['departmentid']."'");
				if($this->kclass->DB->numRows()){
					$departmentSelect.='选择 <span class="bold middle">'.$this->kclass->user['department'].'</span> 的下级部门：<select name="childDepartmentid">';
					while($department=$this->kclass->DB->fetchArray($departments)){
						$departmentSelect.='<option value="'.$department['departmentid'].'">'.$department['title'].'</option>';
					}
					$departmentSelect.='</select>';
				}
				
				// 获得入库类型
				$inType=$this->kclass->DB->queryFirst("SELECT typeid,title,notetypeid FROM inorouttype WHERE killed=0 AND typeid='".$this->kclass->input['typeid']."' LIMIT 0,1");
				// 获得入库单号
				$nowtime=date('Y-m-d',TIMENOW);
				$inboundno=$this->kclass->id(array('inbound'=>TIMENOW,'typeid'=>$inType['notetypeid']));
				
				if($this->kclass->input['workcenterid']>0){ // 任务单入库
					if($this->kclass->input['typeid']==4 OR $this->kclass->input['typeid']==6){
						if($this->kclass->input['typeid']==4){
							$readonly1='';
							$readonly2='readonly';
						}elseif($this->kclass->input['typeid']==6){
							$readonly1='readonly';
							$readonly2='';
						}

						$itemtr.='<tr class="center even" nohover><td width="15">ID</td><td width="750">物资 </td><td width="150">版本 <span class="red bold">*</span></td><td width="70">良品数量</td><td width="80">不良品数量</td><td width="80">批次时间 <span class="red bold">*</span></td><td width="150">备注</td></tr>';
						for($i=1;$i<13;$i++){
							if($i<13){
								$id.='#itemBatchTime'.$i.',';
							}else{
								$id.='#itemBatchTime'.$i;
							}
						}
					}elseif($this->kclass->input['typeid']==5){
						$count=count($this->kclass->input['itemId']);
						$itemtr.='<tr class="center even" nohover><td width="15">ID</td><td width="70">任务单号</td><td>物资 </td><td width="30">版本</td><td width="80">良品数量 <span class="red bold">*</span></td><td width="90">不良品数量 <span class="red bold">*</span></td><td width="110">批次时间 <span class="red bold">*</span></td><td width="150">备注</td><td width="15">选</td></tr>';
						for($i=$m=1;$i<$count+1;$i++){
							if($this->kclass->input['itemSelect'][$i]!=''){
								$item=$this->kclass->DB->queryFirst("
									SELECT ti.*,
										t.taskno,
										wc.title AS workcenter
									FROM `taskitem` AS ti
									LEFT JOIN workcenter AS wc ON (wc.workcenterid=ti.workcenterid)
									WHERE ti.killed=0 AND ti.itemid='".$this->kclass->input['itemSelect'][$i]."'
									LIMIT 0,1
								");
								$child=$this->kclass->bomChildList(array('parentid'=>$item['materialid'],'materialitemid'=>$item['materialitemid'],'versionid'=>$item['versionid'],'loopNum'=>1));
								if(!empty($child['bom'])){
									foreach($child['bom'] as $val){
										$itemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').' small">
											<td><input type="hidden" name="taskItemid['.$m.']" value="'.$item['itemid'].'" />
											<input type="hidden" name="materialItemid['.$m.']" value="'.$val['materialitemid'].'" />
											<input type="hidden" name="itemMaterialid['.$m.']" value="'.$val['materialid'].'" />'.$m.'</td>
											<td>'.$item['taskno'].'</td>
											<td><a href="'.$val['url'].'">'.$val['no'].'</a>　'.$val['material'].'　'.$val['standard'].'</td>
											<td>'.$val['version'].'<input type="hidden" name="itemVersionid['.$m.']" id="itemVersionid'.$m.'" value="'.$val['versionid'].'" /></td>
											<td><input type="text" name="itemQualified['.$m.']" size="10" value="0"> '.$val['unit'].'</td>
											<td><input type="text" name="itemDisqualified['.$m.']" size="10" value="0" readonly></td>
											<td><input type="text" id="itemBatchTime'.$m.'" name="itemDateline['.$m.']" size="15" value="'.date('Y-m-d',TIMENOW).'"></td>
											<td><input type="text" name="itemRemark['.$m.']" style="width:150px"></td>
											<td><input type="checkbox" name="itemSelect['.$m.']" value="'.$m.'"></td>
										</tr>';
										$id.='#itemBatchTime'.$m.',';
										$m++;
									}
								}
							}
						}
					}else{
						$itemtr.='<tr class="center even" nohover><td width="15">ID</td><td width="70">生产批次</td><td width="70">任务单号</td><td>物资</td><td width="30">版本</td><td width="80">良品数量</td><td width="90">不良品数量</td><td width="110">批次时间 <span class="red bold">*</span></td><td width="150">备注</td></tr>';
						$count=count($this->kclass->input['itemId']);
						for($i=$m=1;$i<$count+1;$i++){
							if($this->kclass->input['itemSelect'][$i]!=''){
							$item=$this->kclass->DB->queryFirst("
								SELECT ti.*,
									t.taskno,
									s.title AS status,
									mv.title AS version,
									wc.title AS workcenter
								FROM `taskitem` AS ti
								LEFT JOIN mftstatus AS s ON (s.statusid=ti.statusid)
								LEFT JOIN `materialversion` AS mv ON (mv.versionid=ti.versionid)
								LEFT JOIN workcenter AS wc ON (wc.workcenterid=ti.workcenterid)
								LEFT JOIN task AS t ON (t.taskid=ti.taskid)
								WHERE ti.killed=0 AND ti.itemid='".$this->kclass->input['itemSelect'][$i]."'
							");
							$materialInfo=$this->kclass->getMaterial(array('materialid'=>$item['materialid'],'itemid'=>$item['materialitemid']));
							if($this->kclass->input['typeid']==3){
								$batch=$this->kclass->DB->queryFirst("SELECT batchid,batchno FROM batch WHERE batchid={$item['batchid']}");
							}
							$itemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').' small">
								<td><input type="hidden" name="taskItemid['.$m.']" value="'.$item['itemid'].'" />
								<input type="hidden" name="itemMaterialid['.$m.']" value="'.$item['materialid'].'" />
								<input type="hidden" name="materialItemid['.$m.']" value="'.$item['materialitemid'].'" />'.$m.'</td>
								<td>'.$batch['batchno'].'</td>
								<td>'.$item['taskno'].'</td>
								<td><a href="'.$materialInfo['url'].'">'.$materialInfo['no'].'</a>　'.$materialInfo['material'].'　'.$materialInfo['standard'].'</td>
								<td>'.$item['version'].'<input type="hidden" name="itemVersionid['.$m.']" size="3" value="'.$item['versionid'].'"></td>
								<td><input type="text" name="itemQualified['.$m.']" value="'.$item['finishquantity'].'" size="12"></td>
								<td>0<input type="hidden" name="itemDisqualified['.$m.']" value="0" /></td>
								<td><input type="text" name="itemDateline['.$m.']" id="itemBatchTime'.$m.'" size="16" value="'.date('Y-m-d',TIMENOW).'"></td>
								<td><input type="text" name="itemRemark['.$m.']" style="width:150px;"></td>
							</tr>';
							if($m<$count+1){
								$id.='#itemBatchTime'.$m.',';
							}else{
								$id.='#itemBatchTime'.$m;
								}
							$m++;
							}
						}
					}
				}else{ // 非任务单入库
					$itemtr='<tr class="center even" nohover><td width="15">ID</td><td width="800">物资 <span class="red bold">*</span></td><td>版本 <span class="red bold">*</span></td><td>良品数量 <span class="red bold">*</span></td><td>不良品数量</td><td>批次时间 <span class="red bold">*</span></td><td>备注</td></tr>';
					if($this->kclass->input['typeid']==1 AND $this->kclass->input['porderid']>0){ // 收料时且有订单时显示
						$inbound['arrivalTime']=date('Y-m-d',$inbound['arrivalTime']);
						$order=$this->kclass->DB->queryFirst("
							SELECT `porder`.orderno,`porder`.porderid,`porder`.purchaseType,
								`supplier`.supplierid,`supplier`.title AS supplier,`supplier`.supplierno
							FROM `porder`
							LEFT JOIN `supplier` ON (`supplier`.supplierid=`porder`.supplierid)
							WHERE `porder`.porderid='".$this->kclass->input['porderid']."'
							LIMIT 0,1
						");
						$r='<tr class="even"><td>送货单号：</td><td><input type="text" name="arrivalno" value="'.$inbound['arrivalno'].'" /></td>
							<td>到货时间：<span class="red bold">*</span></td><td><input type="text" name="arrivalTime" id="arrivalTime"/></td></tr>
							<tr class="odd"><td>订单编号：<span class="red bold">*</span></td><td><a href="/s.php?module=porder&action=view&porderid='.$order['porderid'].'">'.$order['orderno'].'</td>
							<td>供应商：<span class="red bold">*</span></td><td><a href="/s.php?module=supplier&action=view&supplierid='.$order['supplierid'].'">'.$order['supplier'].' ( '.$order['supplierno'].' )</a> </td></tr>';
						$id.='#arrivalTime,';
						$item=$this->kclass->DB->queryFirst("SELECT COUNT(itemid) AS count FROM `item` WHERE killed=0 AND module='porder' AND mid='".$order ['porderid']."' LIMIT 0,1");
						$i=1;
						for($j=0;$j<3;$j++){
							if($order['purchaseType']=='entrust'){
								switch($j){
									case 0:
										$itemtr.='<tr class="bold middle"><td colspan=7>良品明细</td></tr>';
									break;
									case 1:
										$itemtr.='<tr class="bold middle" id="addtr1"><td colspan=7>加工不良品明细</td></tr>';
									break;
									case 2:
										$itemtr.='<tr class="bold middle" id="addtr2"><td colspan=7>材料不良明细</td></tr>';
									break;
								}
							}else{
							
								switch($j){
									case 0:
										$itemtr.='<tr class="bold middle"><td colspan=7>原始明细</td></tr>';
									break;
									case 1:
										$itemtr.='<tr class="bold middle"><td colspan=7>备品明细</td></tr>';
									break;
									case 2:
										$itemtr.='<tr class="bold middle"><td colspan=7>多送明细</td></tr>';
									break;
								}
							}
						
							for($m=1;$m<=$item['count'];$m++){
							if($order['purchaseType']=='entrust'){
									if($j<2){	
										$materials=$this->kclass->DB->query("
												SELECT i.materialid,i.versionid,
													m.materialno,m.title,m.standard
												FROM `item` AS i
												LEFT JOIN `material` AS m ON (m.materialid=i.materialid)
												WHERE i.killed=0 AND i.module='porder' AND i.mid='".$this->kclass->input['porderid']."'
												ORDER BY i.itemid ASC
											");
										if($this->kclass->DB->numRows()){
											$select='<select id="itemMaterialid'.$i.'" name="itemMaterialid['.$i.']" style="width:350px" onchange="dc.material.version(this.value,'.$i.',\'material\')">';
											$select.= '<option value="0">选择订单明细</option>';
											while($material=$this->kclass->DB->fetchArray($materials)){										
												$select.='<option value="'.$material['materialid'].'">'.$material['materialno'].'　'.$material['title'].'　'.$material['standard'].'</option>';
											}
											$select.='</select>';
										}
									}else{
										$ematerials=$this->kclass->DB->query("
												SELECT i.materialid
												FROM `item` AS i
												WHERE i.killed=0 AND i.module='porder' AND i.mid='".$this->kclass->input['porderid']."'
												ORDER BY i.itemid ASC
											");	
										if($this->kclass->DB->numRows()){	
										  $select='<select id="itemMaterialid'.$i.'" name="itemMaterialid['.$i.']" style="width:350px" onchange="dc.material.version(this.value,'.$i.',\'material\')">';
										  $select.= '<option value="0">选择订单明细</option>';							  
											while($ematerial=$this->kclass->DB->fetchArray($ematerials)){
												$materials=$this->kclass->DB->query("
														SELECT m.materialid,m.materialno,m.title,m.standard,
														mv.versionid
														FROM `material` AS m
														LEFT JOIN `materialversion` AS mv ON (mv.materialid=m.materialid)
														WHERE m.entrustMaterialid='".$ematerial['materialid']."'
														ORDER BY m.materialid ASC
													");
												if($this->kclass->DB->numRows()){										
													while($material=$this->kclass->DB->fetchArray($materials)){	
														//print_r($material).">>>><br />";
														$select.='<option value="'.$material['materialid'].'">'.$material['materialno'].'　'.$material['title'].'　'.$material['standard'].'</option>';
													}									
												}
											}
												$select.='</select>';
										}
									}
								}else{
									$materials=$this->kclass->DB->query("
											SELECT i.materialid,i.versionid,
												m.materialno,m.title,m.standard
											FROM `item` AS i
											LEFT JOIN `material` AS m ON (m.materialid=i.materialid)
											WHERE i.killed=0 AND i.module='porder' AND i.mid='".$this->kclass->input['porderid']."'
											ORDER BY i.itemid ASC
										");
									if($this->kclass->DB->numRows()){
										$select='<select id="itemMaterialid'.$i.'" name="itemMaterialid['.$i.']" style="width:350px" onchange="dc.material.version(this.value,'.$i.',\'material\')">';
										$select.= '<option value="0">选择订单明细</option>';
										while($material=$this->kclass->DB->fetchArray($materials)){										
											$select.='<option value="'.$material['materialid'].'">'.$material['materialno'].'　'.$material['title'].'　'.$material['standard'].'</option>';
										}
										$select.='</select>';
									}								
								}

								$itemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').' small">
									<td><input type="hidden" class="itemId" name="itemId['.$i.']" value="'.$i.'" />'.$i.'</td>
									<td>'.$select.'</td>
									<td><select name="itemVersionid['.$i.']" id="itemVersionid'.$i.'" style="width:80px;"></select></td>
									<td><input type="text" name="itemQualified['.$i.']" size="10" value="0" ></td>
									<td><input type="text" name="itemDisqualified['.$i.']" size="10" value="0" ></td>
									<td><input type="text" name="itemDateline['.$i.']" id="itemBatchTime'.$i.'" size="16"></td>
									<td><input type="text" name="itemRemark['.$i.']" style="width:150px;"></td>
								</tr>';
								$id.=$this->kclass->iif($i<$item['count']*3,'#itemBatchTime'.$i.',','#itemBatchTime'.$i);
								$i++;
							}															
						}
						if($order['purchaseType']=='entrust'){
							$itemtr .='<tr><th colspan="9">添加入库明细</th></tr>
									<tr><td></td><td colspan="8"><select id="chooseType">
									<option value="0">请选择添加类型</option>
									<option value="1">良品明细</option>
									<option value="2">加工不良明细</option>
									</select><span class="hand" id="hand" onclick="dc.addinboundLine()" style="border:2px solid gray;background:#FFE8EB">增加一行明细</span></td></tr>';	
						}
					}elseif(($this->kclass->input['typeid']==7 AND $this->kclass->input['orderid']>0) OR ($this->kclass->input['typeid']==8 AND $this->kclass->input['sampleid']>0)){
						if($this->kclass->input['orderid']>0){
							$order=$this->kclass->DB->queryFirst("SELECT orderno,orderid FROM `order` WHERE orderid='".$this->kclass->input['orderid']."' LIMIT 0,1");
							$r='<tr class="even"><td>订单编号：<span class="red bold">*</span></td><td><a href="/s.php?module=order&action=view&orderid='.$order['orderid'].'">'.$order['orderno'].'</td><td></td><td></td></tr>';
							$module='order';$mid=$order['orderid'];
						}elseif($this->kclass->input['sampleid']>0){
							$sample=$this->kclass->DB->queryFirst("SELECT sampleno,sampleid FROM `sample` WHERE sampleid='".$this->kclass->input['sampleid']."' LIMIT 0,1");
							$r='<tr class="even"><td>样品单编号：<span class="red bold">*</span></td><td><a href="/s.php?module=sample&action=view&sampleid='.$sample['sampleid'].'">'.$sample['sampleno'].'</td><td></td><td></td></tr>';
							$module='sample';$mid=$sample['sampleid'];
						}
						$items=$this->kclass->DB->query("
							SELECT ri.materialid,ri.versionid,ri.materialitemid,ri.productid,ri.productitemid,
								mv.title AS version
							FROM `requirementitem` AS ri
							LEFT JOIN requirement AS r ON (r.requirementid=ri.requirementid)
							LEFT JOIN `materialversion` AS mv ON (mv.versionid=ri.versionid)
							WHERE ri.killed=0 AND mv.killed=0 AND r.locked>0 AND ri.module='".$module."' AND ri.mid='".$mid."'
							ORDER BY ri.itemid ASC
						");
						$count=$this->kclass->DB->numRows();
						if($count){
							$i=1;
							while($item=$this->kclass->DB->fetchArray($items)){
								$materialInfo=$this->kclass->getMaterial(array('materialid'=>$item['materialid'],'itemid'=>$item['materialitemid']));
								$itemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').' small">
									<td><input type="hidden" name="itemId['.$i.']" value="'.$i.'" />
									<input type="hidden" name="itemMaterialid['.$i.']" value="'.$item['materialid'].'" />
									<input type="hidden" name="materialItemid['.$i.']" value="'.$item['materialitemid'].'" />
									<input type="hidden" name="productId['.$i.']" value="'.$item['productid'].'" />
									<input type="hidden" name="productItemid['.$i.']" value="'.$item['productitemid'].'" />'.$i.'</td>
									<td><a href="'.$materialInfo['url'].'">'.$materialInfo['no'].'</a>　'.$materialInfo['material'].'　'.$materialInfo['standard'].'</td>
									<td>'.$this->kclass->chooserMaterialVersion(array('name'=>'itemVersionid['.$i.']','selectedid'=>$item['versionid'],'width' =>150,'id'=>'itemVersionid'.$i,'materialid'=>$item['materialid'],'itemid'=>$item['materialitemid'])).'</td>
									<td><input type="text" name="itemQualified['.$i.']" size="10" value="0" ></td>
									<td><input type="text" name="itemDisqualified['.$i.']" size="10" value="0" readonly></td>
									<td><input type="text" name="itemDateline['.$i.']" id="itemBatchTime'.$i.'" size="16" value=""></td>
									<td><input type="text" name="itemRemark['.$i.']" style="width:150px;"></td>
								</tr>';
								if($i<$count){
									$id.='#itemBatchTime'.$i.',';
								}else{
									$id.='#itemBatchTime'.$i;
								}
								$i++;
							}
						}else{
							$itemtr.='<tr><td colspan=7>该订单还没有被添加入计划中。</td></tr>';
						}
					}else{
						if($this->kclass->input['typeid']==2){
							$r='<tr class="even"><td>送货单号：</td><td><input type="text" name="arrivalno" value="" /></td><td>到达时间：<span class="red bold">*</span></td><td><input type="text" name="arrivalTime" id="arrivalTime"/></td></tr><tr class="odd"><td>订单编号：</td><td><input type="text" name="orderno" value="" /></td><td>供应商：</td><td><input type="text" name="supplier" style="width:440px" /></td></tr>';
							$id.='#arrivalTime,';
						}
						if($this->kclass->input['typeid']==6){
							$quantityReadonly='readonly';
							$disquantityReadonly='';
						}else{
							$quantityReadonly='';
							$disquantityReadonly='';
						}
						$counter=$this->kclass->iif($item['count'],$item['count'],12);
						for($i=1;$i<=$counter;$i++){
							$itemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').' small">
								<td><input type="hidden" name="itemId['.$i.']" value="'.$i.'" />'.$i.'</td>
								<td>'.$this->kclass->chooserMaterial(array('hName'=>'itemMaterialid['.$i.']','hId'=>'itemMaterialid'.$i,'name'=>'itemMaterial['.$i.']','id'=>'itemMaterial'.$i,'line'=>$i,'showVersion'=>1,'width'=>550,'hasBlank'=>1,'topname'=>'')).'</td>
								<td><select name="itemVersionid['.$i.']" id="itemVersionid'.$i.'" style="width:150px;"></select></td>
								<td><input type="text" name="itemQualified['.$i.']" size="10" value="0" '.$quantityReadonly.'></td>
								<td><input type="text" name="itemDisqualified['.$i.']" size="10" value="0" '.$disquantityReadonly.'></td>
								<td><input type="text" name="itemDateline['.$i.']" id="itemBatchTime'.$i.'" size="16"></td>
								<td><input type="text" name="itemRemark['.$i.']" style="width:150px;"></td>
							</tr>';
							if($i<12){
								$id.='#itemBatchTime'.$i.',';
							}else{
								$id.='#itemBatchTime'.$i;
							}
						}
					}
				}

$body=<<<EOF
<form action="/s.php?module=inbound&action=insert" name="inbound" method="post" onsubmit="dc.checkSubmit(this)">
<input type="hidden" name="module" value="inbound" />
<input type="hidden" name="action" value="insert" />
<input type="hidden" name="typeid" value="{$this->kclass->input['typeid']}" />
<input type="hidden" name="noteTypeid" value="{$inType['notetypeid']}" />
<input type="hidden" name="departmentid" value="{$this->kclass->user['departmentid']}" />
<input type="hidden" name="porderid" value="{$this->kclass->input['porderid']}" />
<input type="hidden" name="orderid" value="{$this->kclass->input['orderid']}" />
<input type="hidden" name="sampleid" value="{$this->kclass->input['sampleid']}" />
<input type="hidden" name="workcenterid" value="{$this->kclass->input['workcenterid']}" />
<table class="hundred">
<thead></tr><th colspan="4">基本信息</th></tr></thead>
<tbody>
<tr class="even">
<td>入库单号：</td><td width="700"><input type="text" class="middle bold darkred" name="inboundno" value="{$inboundno}" id="inboundno" size="14">　　　　编号生成方式：<input type="radio" name="noType" value="0" checked>自动<input type="radio" name="noType" value="1" >手动  {$departmentSelect}　　　　转月时间：<input type="text" id="realtime" name="realtime" value="{$nowtime}" size=10></td>
<td>申 请 人：<span class="red bold">*</span></td><td><input type="text" name="applicant" value="{$this->kclass->user['realname']}" />　部门： {$this->kclass->user['department']} 　入库类型：{$inType['title']}</td>
</tr>
{$r}
<tr class="even">
<td>用　　途：<span class="red bold">*</span></td><td><textarea name="purpose" style="width:444px;height:111px;" >空</textarea></td>
<td>备　　注：</td><td><textarea name="remark" style="width:444px;height:111px;"></textarea></td>
</tr>
</tbody>
</table>
<table class="hundred">
<thead></tr><th colspan="9">入库申请明细</th></tr></thead>
<tbody>
{$itemtr}
<tr class="even">
<td class="small gray" colspan="9">
注意事项：<br>
①如果所列表格不够，那么在提交保存后再点击修改订单来增加物资；<br>
②时间的格式为2012-02-06，中间用半角短横杠隔开；</td>
</tr>
</tbody>
</table>
<table class="tlist tsuite" align="center" width="100%">
<tbody>
<tr colspan="4" class="even" nohover>
<td class="center"><input type="submit" id="submitButton" value="  提交  " accesskey="s"><input type="reset" value="  复位  "/></td>
</tr>
</tbody>
</table>
EOF;

				$this->kclass->tbline+=4;
				$this->kclass->page['title'].='新建入库申请单';
				$this->kclass->page['onload'].='var dates=$(\'#realtime,'.$id.'\').datepicker({onSelect:function(selectedDate){instance=$(this).data(\'datepicker\'),date=$.datepicker.parseDate(instance.settings.dateFormat || $.datepicker._defaults.dateFormat,selectedDate,instance.settings);dc.updateNo({\'id\':\'inboundno\',\'typeid\':'.$inType['notetypeid'].'});}});dc.tabhover();';
				$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - <a href="/s.php?module=inbound">查看入库单列表</a> - 新建入库申请单', 'right'=>'<a href="/s.php?module=inbound&action=list">返回列表</a>','body'=>$body));
			}
		}
	}

	//
	function insert(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			if($this->kclass->input['departmentid']<= 0){
				$e .= '<li>请选择入库申请单中的 部门。</li>';
			}
			if($this->kclass->input['typeid']=='' ){
				$e .= '<li>请选择入库申请单的 入库类型。</li>';
			}
			if($this->kclass->input['purpose']=='' ){
				$e .= '<li>请填写入库申请单的 用途。</li>';
			}
			$j=count($this->kclass->input['itemMaterialid']);
			$hasItem=0;
			if($this->kclass->input['workcenterid']>0){
				$j=count($this->kclass->input['taskItemid']);
				for($i=1;$i<=$j;$i++){
					if($this->kclass->input['itemMaterialid'][$i]>0 AND $this->kclass->input['itemVersionid'][$i]>0 AND ($this->kclass->input['itemQualified'][$i]>0 OR $this->kclass->input['itemDisqualified'][$i]>0) AND $this->kclass->input['itemDateline'][$i]>0){
						if($this->kclass->input['typeid']==5){
							if($this->kclass->input['itemSelect'][$i]>0){
								$hasItem=1;
							}
						}else{
							$hasItem=1;
						}
					}
				}
			}else{
				$j=count($this->kclass->input['itemId']);
				for($i=1;$i<=$j;$i++){
					if(($this->kclass->input['itemMaterialid'][$i]>0 OR $this->kclass->input['productId'][$i]>0) AND $this->kclass->input['itemVersionid'][$i]>0 AND ($this->kclass->input['itemQualified'][$i]>0 OR $this->kclass->input['itemDisqualified'][$i]>0) AND $this->kclass->input['itemDateline'][$i]!=''){
						if($this->kclass->input['typeid']==5){
							$material=$this->kclass->DB->queryFirst("SELECT materialno FROM material WHERE materialid=".$this->kclass->input['itemMaterialid'][$i]." LIMIT 0,1");
							$child=$this->kclass->bomChildList(array('parentid'=>$this->kclass->input['itemMaterialid'][$i],'materialitemid'=>$this->kclass->input['materialItemid'][$i],'versionid'=>$this->kclass->input['itemVersionid'][$i],'loopNum'=>1));
							if(empty($child)){
								$e.='<li>申请出库物资 <b>'.$material['materialno'].'</b> 有存在没有BOM单的物资。请查看</li>';
							}
						}
						$hasItem=1;
					}
				}
			}
			if($hasItem==0){
				$e.='<li>需要填写至少有一条入库明细，才能建立入库申请单。</li>';
			}

			$sd=explode('-', $this->kclass->input['realtime']);
			$realtime=mktime(0,0,0,$sd[1],$sd[2],$sd[0]);
			if($this->kclass->input['noType']==1){
				$inboundno=$this->kclass->input['inboundno'];
			}elseif($this->kclass->input['noType']==0){
				$inboundno=$this->kclass->id(array('inbound'=>$realtime,'typeid'=>$this->kclass->input['noteTypeid']));
			}
			//判断是否已建立入库单，防止重复入库
			$inbound=$this->kclass->DB->queryFirst("SELECT * FROM inbound WHERE killed=0 AND noteTypeid='".$this->kclass->input['noteTypeid']."' AND inboundno='".$inboundno."' LIMIT 0,1");
			if($inbound){
				$e.='<li>入库单号【'.$inboundno.'】已存在。</li>';
			}
		}
		if($e){
			$this->kclass->messager(array(
				'title' => '新建入库申请单',
				'text' => '您在入库申请单的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		if($this->kclass->input['arrivalTime']){
			$sd=explode('-', $this->kclass->input['arrivalTime']);
			$arrivalTime=mktime(0,0,0,$sd[1],$sd[2],$sd[0]);
		}else{
			$arrivalTime=0;
		}
		
		$this->kclass->input['porderid']=$this->kclass->iif($this->kclass->input['porderid']>0,$this->kclass->input['porderid'],0);
		$this->kclass->input['orderid']=$this->kclass->iif($this->kclass->input['orderid']>0,$this->kclass->input['orderid'],0);
		$workcenterid=$this->kclass->iif($this->kclass->input['workcenterid']>0,$this->kclass->input['workcenterid'],0);
		$sampleid=$this->kclass->iif($this->kclass->input['sampleid']>0,$this->kclass->input['sampleid'],0);
		$porder=$this->kclass->DB->queryFirst("SELECT porderid,supplierid FROM porder WHERE killed=0 AND porderid='".$this->kclass->input['porderid']."'");
		$supplierid=$this->kclass->iif($porder['supplierid']>0,$porder['supplierid'],0);
		$this->kclass->DB->query("
			INSERT INTO inbound  (inboundno,departmentid,typeid,noteTypeid,porderid,orderid,sampleid,workcenterid,realtime,supplierid,orderno,supplier,arrivalno,applicant,purpose,arrivalTime,remark,created,creator)
			VALUES ('".$inboundno."','".$this->kclass->user['departmentid']."','".$this->kclass->input['typeid']."','".$this->kclass->input['noteTypeid']."','".$this->kclass->input['porderid']."','".$this->kclass->input['orderid']."','".$sampleid."','".$workcenterid."','".$realtime."','".$supplierid."','".$this->kclass->input['orderno']."','".$this->kclass->input['supplier']."','".$this->kclass->input['arrivalno']."','".$this->kclass->input['applicant']."','".$this->kclass->input['purpose']."','".$arrivalTime."','".$this->kclass->input['remark']."','".TIMENOW."','".$this->kclass->user['userid']."')
		");
		$inboundid=$this->kclass->DB->insertID();

		if($this->kclass->input['workcenterid']>0){// 从生产系统传递的入库信息
			$j=count($this->kclass->input['taskItemid']);
			for($i=1;$i<=$j;$i++){
				if($this->kclass->input['itemMaterialid'][$i]>0 AND $this->kclass->input['itemVersionid'][$i]>0 AND ($this->kclass->input['itemQualified'][$i]>0 OR $this->kclass->input['itemDisqualified'][$i]>0) AND $this->kclass->input['itemDateline'][$i]>0){
					$sd=explode('-', $this->kclass->input['itemDateline'][$i]);
					$dateline=mktime(0,0,0,$sd[1],$sd[2],$sd[0]);
					$disqualified=$this->kclass->iif(floatval($this->kclass->input['itemDisqualified'][$i])>0,floatval($this->kclass->input['itemDisqualified'][$i]),0);
					$quantity=$this->kclass->iif($this->kclass->input['itemQualified'][$i]>0,$this->kclass->input['itemQualified'][$i],$disqualified);
					$materialid=$this->kclass->iif($this->kclass->input['itemMaterialid'][$i]>0,$this->kclass->input['itemMaterialid'][$i],0);
					$materialitemid=$this->kclass->iif($this->kclass->input['materialItemid'][$i]>0,$this->kclass->input['materialItemid'][$i],0);
					if($materialid>0){
						$material=$this->kclass->DB->queryFirst("
							SELECT materialid,unitid,quantity,materialno
							FROM material 
							WHERE materialid='".$materialid."'
							LIMIT 0,1
						");
					}
					if($this->kclass->input['typeid']==5){ // 入库BOM
						if($this->kclass->input['itemSelect'][$i]>0){
							$this->kclass->DB->query("
								INSERT INTO preinbounditem (`module`,`mid`,`materialid`,`versionid`,`materialitemid`,`taskitemid`,`quantity`,`qualified`,`disqualified`,`unitid`,`dateline`,`remark`,`created`,`creator`)
								VALUES ('inbound','".$inboundid."','".$materialid."','".$this->kclass->input['itemVersionid'][$i]."','".$materialitemid."','".$this->kclass->input['taskItemid'][$i]."','".$quantity."','".$this->kclass->input['itemQualified'][$i]."','".$disqualified."','".$material['unitid']."','".$dateline."','".$this->kclass->input['itemRemark'][$i]."','".TIMENOW."','".$this->kclass->user['userid']."')
							");
						}
					}else{
						$this->kclass->DB->query("
							INSERT INTO preinbounditem (`module`,`mid`,`materialid`,`versionid`,`materialitemid`,`taskitemid`,`quantity`,`qualified`,`disqualified`,`unitid`,`dateline`,`remark`,`created`,`creator`)
							VALUES ('inbound','".$inboundid."','".$materialid."','".$this->kclass->input['itemVersionid'][$i]."','".$materialitemid."','".$this->kclass->input['taskItemid'][$i]."','".$quantity."','".$this->kclass->input['itemQualified'][$i]."','".$disqualified."','".$material['unitid']."','".$dateline."','".$this->kclass->input['itemRemark'][$i]."','".TIMENOW."','".$this->kclass->user['userid']."')
						");
					}
				}
			}
		}else{ //非任务单入库
			$j=count($this->kclass->input['itemMaterialid']);
			for($i=1;$i<=$j;$i++){
				if(($this->kclass->input['itemMaterialid'][$i]>0 OR $this->kclass->input['productItemid'][$i]) AND $this->kclass->input['itemVersionid'][$i]>0 AND ($this->kclass->input['itemQualified'][$i]>0 OR $this->kclass->input['itemDisqualified'][$i]>0) AND $this->kclass->input['itemDateline'][$i]!=''){
					$sd=explode('-', $this->kclass->input['itemDateline'][$i]);
					$dateline=mktime(0,0,0,$sd[1],$sd[2],$sd[0]);
					$disqualified=$this->kclass->iif(floatval($this->kclass->input['itemDisqualified'][$i])>0,floatval($this->kclass->input['itemDisqualified'][$i]),0);
					$quantity=$this->kclass->iif($this->kclass->input['itemQualified'][$i]>0,$this->kclass->input['itemQualified'][$i],$disqualified);			
					$materialid=$this->kclass->iif($this->kclass->input['itemMaterialid'][$i]>0,$this->kclass->input['itemMaterialid'][$i],0);
					$productitemid=$this->kclass->iif($this->kclass->input['productItemid'][$i]>0,$this->kclass->input['productItemid'][$i],0);
					$productid=$this->kclass->iif($this->kclass->input['productId'][$i]>0,$this->kclass->input['productId'][$i],0);
					$materialitemid=$this->kclass->iif($this->kclass->input['materialItemid'][$i]>0,$this->kclass->input['materialItemid'][$i],0);
					if($materialid>0){
						$material=$this->kclass->DB->queryFirst("
							SELECT materialid,unitid,quantity,materialno
							FROM material 
							WHERE materialid='".$materialid."'
							LIMIT 0,1
						");
					}
					if($this->kclass->input['typeid']==5){// 提交后插入bom子集
						$child=$this->kclass->bomChildList(array('parentid'=>$this->kclass->input['itemMaterialid'][$i],'materialitemid'=>$this->kclass->input['materialItemid'][$i],'versionid'=>$this->kclass->input['itemVersionid'][$i],'quantity'=>$quantity,'loopNum'=>1));
						if(!empty($child)){
							foreach($child['bom'] as $val){
								if($val['ifGeneral']==0){
									$this->kclass->DB->query("
										INSERT INTO preinbounditem (`module`,`mid`,`materialid`,`versionid`,`materialitemid`,`quantity`,`qualified`,`disqualified`,`unitid`,`dateline`,`remark`,`created`,`creator`)
										VALUES ('inbound','".$inboundid."','".$val['materialid']."','".$val['versionid']."','".$materialitemid."','".$val['quantity']."','".$val['quantity']."','".$disqualified."','".$val['unitid']."','".$dateline."','".$this->kclass->input['itemRemark'][$i]."','".TIMENOW."','".$this->kclass->user['userid']."')
									");
								}
							}
						}
					}else{// 提交后直接插入
						$this->kclass->DB->query("
							INSERT INTO preinbounditem								(`module`,`mid`,`materialid`,`versionid`,`materialitemid`,`productid`,`productitemid`,`quantity`,`qualified`,`disqualified`,`unitid`,`dateline`,`remark`,`created`,`creator`)
							VALUES
								('inbound','".$inboundid."','".$material['materialid']."','".$this->kclass->input['itemVersionid'][$i]."','".$materialitemid."','".$productid."','".$productitemid."','".$quantity."','".$this->kclass->input['itemQualified'][$i]."','".$disqualified."','".$material['unitid']."','".$dateline."','".$this->kclass->input['itemRemark'][$i]."','".TIMENOW."','".$this->kclass->user['userid']."')
						");
					}
				}
			}
		}

		$this->kclass->messager(array(
			'title' => '新建入库申请单',
			'text' => '入库单 【<b>'.$inboundno.'</b>】 已新建成功!返回查看入库申请单',
			'url' => '/s.php?module=inbound&action=view&inboundid='.$inboundid,
			'sec' => 2
		));
	}
	//
	function update(){
		$inbound=$this->kclass->DB->queryFirst("
			SELECT i.*,
				d.title AS department
			FROM inbound AS i
			LEFT JOIN department AS d ON (i.departmentid=d.departmentid)
			WHERE i.killed=0 AND i.inboundid='".$this->kclass->input['inboundid']."'
			LIMIT 0,1
		");
		if(!$inbound){
			$e='<li>数据错误，很抱歉</li>';
		}
		if($e){
			$this->kclass->messager(array(
				'title' => '修改入库单',
				'text' => '您在修改入库单的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		if(!$this->kclass->input['typeid']){
			$type=$this->kclass->chooserInorouttype(array('name'=>'typeid','width'=>200,'module'=>'inbound','selectedid'=>$inbound['typeid']));
$body=<<<EOF
<form action="/s.php?module=inbound&action=update" method="get">
<input type="hidden" name="module" value="inbound">
<input type="hidden" name="action" value="update">
<input type="hidden" name="inboundid" value="{$this->kclass->input['inboundid']}">
<input type="hidden" name="departmentid" value="{$this->kclass->user['departmentid']}">
<table>
<thead>
<tr>
<th colspan="2">新建入库申请：请选择入库类型</th>
</tr>
</thead>
<tbody>
<tr class="odd">
<td>选择入库类型：<span class="red bold">*</span></td>
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
			$this->kclass->page['title'].='新建入库申请单';
			$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - <a href="/s.php?module=inbound">查看入库单列表</a> - 新建入库申请单', 'right'=>'<a href="/s.php?module=inbound&action=list">返回列表</a>','body'=>$body));
		}else{
			if($this->kclass->input['typeid']==1 AND $this->kclass->input['porderid']<=0){
				$order=$this->kclass->chooserPOrder(array('name'=>'porderid','hasBlank'=>1,'width'=>450,'condition'=>' AND arrivalStatus<>2 AND ifComplete=0','orderby'=>'`porder`.orderno','direction'=>'DESC','selectedid'=>$inbound['porderid']));
$body=<<<EOF
<form action="/s.php?module=inbound&action=update" method="get">
<input type="hidden" name="module" value="inbound">
<input type="hidden" name="action" value="update">
<input type="hidden" name="inboundid" value="{$this->kclass->input['inboundid']}">
<input type="hidden" name="departmentid" value="{$this->kclass->input['departmentid']}">
<input type="hidden" name="typeid" value="{$this->kclass->input['typeid']}">
<table>
<thead>
<tr>
<th colspan="4">修改入库申请：请选择订单</th>
</tr>
</thead>
<tbody>
<tr class="odd">
<td title="">请选择订单：<span class="red bold">*</span></td>
<td>{$order}</td>
</tr>
<tr class="odd">
<td colspan="4" class="center" nohover>
<input type="submit" id="submitButton" value="  下一步  " accesskey="s">
<input type="reset" value="  复位  ">
</td>
</tr>
</tbody>
</table>
</form>
EOF;
				$this->kclass->page['title'].='新建入库申请单';
				$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - <a href="/s.php?module=inbound">查看入库单列表</a> - 新建入库申请单', 'right'=>'<a href="/s.php?module=inbound&action=list">返回列表</a>','body'=>$body));
			}elseif($this->kclass->input['typeid']==7 AND $this->kclass->input['orderid']<=0){
				$order=$this->kclass->chooserOrder(array('name'=>'orderid','hasBlank'=>1,'width'=>450,'condition'=>' AND ifApprove=1','orderby'=>'o.orderno','direction'=>'DESC','selectedid'=>$inbound['orderid']));
$body=<<<EOF
<form action="/s.php?module=inbound&action=update" method="get">
<input type="hidden" name="module" value="inbound">
<input type="hidden" name="action" value="update">
<input type="hidden" name="inboundid" value="{$this->kclass->input['inboundid']}">
<input type="hidden" name="departmentid" value="{$this->kclass->input['departmentid']}">
<input type="hidden" name="typeid" value="{$this->kclass->input['typeid']}">
<table>
<thead>
<tr>
<th colspan="4">新建入库申请：请选择订单</th>
</tr>
</thead>
<tbody>
<tr class="odd">
<td title="">请选择订单：<span class="red bold">*</span></td>
<td>{$order}</td>
</tr>
<tr class="odd" nohover>
<td colspan="4" class="center" nohover>
<input type="submit" id="submitButton" value="  下一步  " accesskey="s">
<input type="reset" value="  复位  ">
</td>
</tr>
</tbody>
</table>
</form>
EOF;
			$this->kclass->page['title'].='新建入库申请单';
			$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - <a href="/s.php?module=inbound">查看入库单列表</a> - 新建入库申请单', 'right'=>'<a href="/s.php?module=inbound&action=list">返回列表</a>','body'=>$body));
		}elseif($this->kclass->input['typeid']==8 AND $this->kclass->input['sampleid']<=0){
			$sample=$this->kclass->chooserSample(array('name'=>'sampleid','hasBlank'=>1,'width'=>450,'condition'=>' AND ifApprove=1','orderby'=>'s.sampleno','direction'=>'DESC','selectedid'=>$inbound['sampleid']));
$body=<<<EOF
<form action="/s.php?module=inbound&action=update" method="get">
<input type="hidden" name="module" value="inbound">
<input type="hidden" name="action" value="update">
<input type="hidden" name="inboundid" value="{$this->kclass->input['inboundid']}">
<input type="hidden" name="departmentid" value="{$this->kclass->input['departmentid']}">
<input type="hidden" name="typeid" value="{$this->kclass->input['typeid']}">
<table>
<thead>
<tr>
<th colspan="4">新建入库申请：请选择样品单</th>
</tr>
</thead>
<tbody>
<tr class="odd">
<td title="">请选择样品单：<span class="red bold">*</span></td>
<td>{$sample}</td>
</tr>
<tr class="odd" nohover>
<td colspan="4" class="center" nohover>
<input type="submit" id="submitButton" value="  下一步  " accesskey="s">
<input type="reset" value="  复位  ">
</td>
</tr>
</tbody>
</table>
</form>
EOF;
			$this->kclass->page['title'].='新建入库申请单';
			$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - <a href="/s.php?module=inbound">查看入库单列表</a> - 新建入库申请单', 'right'=>'<a href="/s.php?module=inbound&action=list">返回列表</a>','body'=>$body));
		}else{
			$departments=$this->kclass->DB->query("SELECT departmentno,departmentid,title FROM department WHERE parentid='".$this->kclass->user['departmentid']."'");
			if($this->kclass->DB->numRows()){
				$departmentSelect.='选择 <span class="bold middle">'.$this->kclass->user['department'].'</span> 的下级部门：<select name="childDepartmentid">';
				while($department=$this->kclass->DB->fetchArray($departments)){
					$departmentSelect.='<option value="'.$department['departmentid'].'">'.$department['title'].'</option>';
				}
				$departmentSelect.='</select>';
			}

			// 获得入库类型
			$inType=$this->kclass->DB->queryFirst("SELECT typeid,title,notetypeid FROM inorouttype WHERE killed=0 AND typeid='".$this->kclass->input['typeid']."' LIMIT 0,1");
			
			if($inbound['typeid']==6){
				$quantityReadonly='';
				$disquantityReadonly='';
			}else{
				$quantityReadonly='';
				$disquantityReadonly='';
			}
			// 已加入入库申请的物资明细
			$items=$this->kclass->DB->query("
				SELECT pi.*,
					b.batchid,
					t.taskno,t.taskid
				FROM `preinbounditem` AS pi
				LEFT JOIN `batch` AS b ON (b.batchid=pi.batchid)
				LEFT JOIN `taskitem` AS ti ON (ti.itemid=pi.taskitemid)
				LEFT JOIN `task` AS t ON (t.taskid=ti.taskid)
				LEFT JOIN material AS m ON (m.materialid=pi.materialid)
				WHERE pi.killed=0 AND pi.module='inbound' AND pi.mid='".$this->kclass->input['inboundid']."'
				ORDER BY m.materialno ASC
			");
			$k=1;
			if($this->kclass->DB->numRows()){
				while($item=$this->kclass->DB->fetchArray($items)){
					if($inbound['workcenterid']>0){
						$itemtd='<td><input type="hidden" name="taskItemId['.$k.']" value="'.$item['taskitemid'].'"/><a href="/s.php?module=task&action=view&taskid='.$item['taskid'].'">'.$item['taskno'].'</a></td>';
						$taskth='<th width="80">任务单号</th>';
					}
					$item['dateline']=date('Y-m-d',$item['dateline']);
					$materialInfo=$this->kclass->getMaterial(array('materialid'=>$item['materialid'],'itemid'=>$item['materialitemid']));
					$itemList.='<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').' small">
						<td><input type="hidden" name="id['.$k.']" value="'.$k.'" />
						<input type="hidden" name="itemId['.$k.']" value="'.$item['itemid'].'" />
						<input type="hidden" name="itemMaterialid['.$k.']" value="'.$item['materialid'].'" />
						<input type="hidden" name="materialItemid['.$k.']" value="'.$item['materialitemid'].'" />
						<input type="hidden" name="productId['.$k.']" value="'.$item['productid'].'" />
						<input type="hidden" name="productItemid['.$k.']" value="'.$item['productitemid'].'" />'.$k.'</td>
						'.$itemtd.'
						<td><a href="'.$materialInfo['url'].'">'.$materialInfo['no'].'</a>　'.$materialInfo['material'].'　'.$materialInfo['standard'].'</td>
						<td>'.$this->kclass->chooserMaterialVersion(array('name'=>'itemVersionid['.$k.']','selectedid'=>$item['versionid'],'width' =>250,'id'=>'itemVersionid'.$k,'materialid'=>$item['materialid'],'productitemid'=>$item['productitemid'])).'</td>
						<td><input type="text" name="itemQualified['.$k.']" value="'.$item['qualified'].'" size="7" '.$quantityReadonly.'></td>
						<td><input type="text" name="itemDisqualified['.$k.']" value="'.$item['disqualified'].'" size="7" '.$disquantityReadonly.'></td>
						<td><input type="text" name="itemDateline['.$k.']" id="itemDateline'.$k.'" size="13" value="'.$item['dateline'].'"></td>
						<td><input type="text" name="itemRemark['.$k.']" style="width:200px;" value="'.$item['remark'].'"></td>
						<td><input type="checkbox" name="itemKill['.$k.']" value="'.$item['itemid'].'" ></td>
					</tr>';
					$id.='#itemDateline'.$k.',';
					$taskItemId[]=$item['taskitemid'];
					$versionId[]=$item['versionid'];
					$k++;
				}
				$itemList.='<tr><td colspan=13 class="bold">新建明细</td></tr>';
			}

			// 新增的明细
			if($this->kclass->input['typeid']==1 AND $this->kclass->input['porderid']>0){// 收料时且有订单时显示
				$inbound['arrivalTime']=date('Y-m-d',$inbound['arrivalTime']);
				$order=$this->kclass->DB->queryFirst("
					SELECT p.orderno,p.porderid,
						s.supplierid,s.title AS supplier,s.supplierno
					FROM `porder` AS p
					LEFT JOIN `supplier` AS s ON (s.supplierid=p.supplierid)
					WHERE p.porderid='".$this->kclass->input['porderid']."'
					LIMIT 0,1
				");
				$item=$this->kclass->DB->queryFirst("SELECT COUNT(*) AS count FROM item WHERE killed=0 AND module='porder' AND mid='".$this->kclass->input['porderid']."'");
				$itemCount=$item['count'];
				$r='<tr class="even"><td>送货单号：</td><td><input type="text" name="arrivalno" value="'.$inbound['arrivalno'].'" /></td><td>到达时间：<span class="red bold">*</span></td><td><input type="text" name="arrivalTime" id="arrivalTime" value="'.$inbound['arrivalTime'].'" /></td></tr><tr class="odd"><td>订单编号：<span class="red bold">*</span></td><td><a href="/s.php?module=porder&action=view&porderid='.$order['porderid'].'">'.$order['orderno'].'</td><td>供应商：<span class="red bold">*</span></td><td>'.$order['supplier'].' ( '.$order['supplierno'].' ) </td></tr>';
				$id.='#arrivalTime,';
				$itemCount=$this->kclass->iif($itemCount>0,$itemCount,5);
				for($i=$k;$i<$k+$itemCount;$i++){
					$materials=$this->kclass->DB->query("
						SELECT i.materialid,i.versionid,
							m.materialno,m.title,m.standard
						FROM `item` AS i
						LEFT JOIN `material` AS m ON (m.materialid=i.materialid)
						WHERE i.killed=0 AND i.module='porder' AND i.mid='".$this->kclass->input['porderid']."'
						ORDER BY i.itemid ASC
					");
					if($this->kclass->DB->numRows()){
						$select='<select id="itemMaterialid'.$i.'" name="itemMaterialid['.$i.']" style="width:350px" onchange="dc.material.version(this.value,'.$i.',\'material\')">';
						$select.= '<option value="0">选择订单明细</option>';
						while($material=$this->kclass->DB->fetchArray($materials)){
							$select.='<option value="'.$material['materialid'].'">'.$material['materialno'].'　'.$material['title'].'　'.$material['standard'].'</option>';
						}
						$select.='</select>';
					}

					$itemList.='<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').' small">
						<td><input type="hidden" name="id['.$i.']" value="'.$i.'" />'.$i.'</td>
						<td>'.$select.'</td>
						<td><select name="itemVersionid['.$i.']" id="itemVersionid'.$i.'" style="width:250px;"></select></td>
						<td><input type="text" name="itemQualified['.$i.']" size="7" value="0" '.$quantityReadonly.'></td>
						<td><input type="text" name="itemDisqualified['.$i.']" size="7" value="0" '.$disquantityReadonly.'></td>
						<td><input type="text" name="itemDateline['.$i.']" id="itemDateline'.$i.'" size="13" value="'.date('Y-m-d',TIMENOW).'"></td>
						<td><input type="text" name="itemRemark['.$i.']" style="width:150px;"></td>
						<td></td>
					</tr>';
					$id.=$this->kclass->iif($i<$k+4,'#itemDateline'.$i.',','#itemDateline'.$i);
				}
			}elseif(($this->kclass->input['typeid']==7 AND $this->kclass->input['orderid']>0) OR ($this->kclass->input['typeid']==8 AND $this->kclass->input['sampleid']>0)){ // 订单或者样品单入库
				if($this->kclass->input['orderid']>0){
					$order=$this->kclass->DB->queryFirst("SELECT orderno,orderid FROM `order` WHERE orderid='".$this->kclass->input['orderid']."' LIMIT 0,1");
					$r='<tr class="even"><td>订单编号：<span class="red bold">*</span></td><td><a href="/s.php?module=order&action=view&orderid='.$order['orderid'].'">'.$order['orderno'].'</td><td></td><td></td></tr>';
					$module='order';$mid=$order['orderid'];
				}elseif($this->kclass->input['sampleid']>0){
					$sample=$this->kclass->DB->queryFirst("SELECT sampleno,sampleid FROM `sample` WHERE sampleid='".$this->kclass->input['sampleid']."' LIMIT 0,1");
					$r='<tr class="even"><td>样品单编号：<span class="red bold">*</span></td><td><a href="/s.php?module=sample&action=view&sampleid='.$sample['sampleid'].'">'.$sample['sampleno'].'</td><td></td><td></td></tr>';
					$module='sample';$mid=$sample['sampleid'];
				}
				$items=$this->kclass->DB->query("
					SELECT ri.materialid,ri.versionid,ri.materialitemid,ri.productid,ri.productitemid,ri.quantity,
						mv.title AS version
					FROM `requirementitem` AS ri
					LEFT JOIN requirement AS r ON (r.requirementid=ri.requirementid)
					LEFT JOIN `materialversion` AS mv ON (mv.versionid=ri.versionid)
					WHERE ri.killed=0 AND mv.killed=0 AND r.locked>0 AND ri.module='".$module."' AND ri.mid='".$mid."' AND ri.versionid NOT IN (".implode(',',$versionId).")
					ORDER BY ri.itemid ASC
				");
				$count=$this->kclass->DB->numRows();
				if($count){
					$i=$k;
					while($item=$this->kclass->DB->fetchArray($items)){
						$materialInfo=$this->kclass->getMaterial(array('materialid'=>$item['materialid'],'itemid'=>$item['materialitemid']));
						$itemList.='<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').' small">
							<td><input type="hidden" name="id['.$i.']" value="'.$i.'" />
							<input type="hidden" name="itemMaterialid['.$i.']" value="'.$item['materialid'].'" />
							<input type="hidden" name="materialItemid['.$i.']" value="'.$item['materialitemid'].'" />
							<input type="hidden" name="productId['.$i.']" value="'.$item['productid'].'" />
							<input type="hidden" name="productItemid['.$i.']" value="'.$item['productitemid'].'" />'.$i.'</td>
							<td><a href="'.$materialInfo['url'].'">'.$materialInfo['no'].'</a>　'.$materialInfo['material'].'　'.$materialInfo['standard'].'</td>
							<td>'.$this->kclass->chooserMaterialVersion(array('name'=>'itemVersionid['.$i.']','selectedid'=>$item['versionid'],'width' =>250,'id'=>'itemVersionid'.$i,'materialid'=>$item['materialid'],'itemid'=>$item['materialitemid'])).'</td>
							<td><input type="text" name="itemQualified['.$i.']" size="7" value="0" ></td>
							<td><input type="text" name="itemDisqualified['.$i.']" size="7" value="0" readonly></td>
							<td><input type="text" name="itemDateline['.$i.']" id="itemBatchTime'.$i.'" size="13" value=""></td>
							<td><input type="text" name="itemRemark['.$i.']" style="width:200px;"></td>
							<td></td>
						</tr>';
						if($i<$k+$count){
							$id.='#itemBatchTime'.$i.',';
						}else{
							$id.='#itemBatchTime'.$i;
						}
						$i++;
					}
				}
			}else{ // 其他方式入库
				if($this->kclass->input['typeid']==2){ // 非生产收料入库
					$r='<tr class="even"><td>送货单号：</td><td><input type="text" name="arrivalno" value="'.$inbound['arrivalno'].'" /></td><td>到达时间：<span class="red bold">*</span></td><td><input type="text" name="arrivalTime" id="arrivalTime" value="'.date('Y-m-d',$inbound['arrivalTime']).'" /></td></tr><tr class="odd"><td>订单编号：</td><td><input type="text" name="orderno" value="'.$inbound['orderno'].'" /></td><td>供应商：</td><td><input type="text" name="supplier" style="width:440px" value="'.$inbound['supplier'].'" /></td></tr>';
					$id.='#arrivalTime,';
				}

				if($this->kclass->input['typeid']==6){
					$quantityReadonly='readonly';
					$disquantityReadonly='';
				}else{
					$quantityReadonly='';
					$disquantityReadonly='';
				}
				if($inbound['workcenterid']>0){
					if($this->kclass->input['typeid']==3){
						$taskItems=$this->kclass->DB->query("
							SELECT ti.*,
								t.taskno,
								s.title AS status,
								mv.title AS version,
								t.taskid,t.taskno
							FROM `taskitem` AS ti
							LEFT JOIN mftstatus AS s ON (s.statusid=ti.statusid)
							LEFT JOIN `materialversion` AS mv ON (mv.versionid=ti.versionid)
							LEFT JOIN `workcenter` AS wc ON (wc.workcenterid=ti.workcenterid)
							LEFT JOIN task AS t ON (t.taskid=ti.taskid)
							WHERE ti.killed=0 AND ti.ifChooser=1 AND finishquantity>0 AND ti.workcenterid=".$inbound['workcenterid']." AND ti.itemid NOT IN (".implode(',',array_unique($taskItemId)).")
							ORDER BY ti.itemid ASC, ti.modified DESC, ti.created DESC
						");
						if($taskitemCount=$this->kclass->DB->numRows()){
							$i=$k;
							$itemList.='<tr class="center even" nohover><td width="15">ID</td><td>任务单号</td><td>物资</td><td width="250">版本 <span class="red bold">*</span></td><td width="60">良品 <span class="red bold">*</span></td><td width="60">不良品 <span class="red bold">*</span></td><td width="90">批次时间 <span class="red bold">*</span></td><td width="200">备注</td><td width="25">选</td></tr>';
							while($taskItem=$this->kclass->DB->fetchArray($taskItems)){
								$materialInfo=$this->kclass->getMaterial(array('materialid'=>$taskItem['materialid'],'itemid'=>$taskItem['materialitemid']));
								$itemList.='<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').' small">
									<td><input type="hidden" name="taskItemId['.$i.']" value="'.$taskItem['itemid'].'">
									<input type="hidden" name="itemMaterialid['.$i.']" value="'.$taskItem['materialid'].'">
									<input type="hidden" name="materialItemid['.$i.']" value="'.$taskItem['materialitemid'].'">'.$i.'</td>
									<td><a href="/s.php?module=task&action=view&taskid='.$taskItem['taskid'].'">'.$taskItem['taskno'].'</a></td>
									<td width="350"><a href="'.$materialInfo['url'].'">'.$materialInfo['no'].'</a>　'.$materialInfo['material'].'　'.$materialInfo['standard'].'</td>
									<td>'.$this->kclass->chooserMaterialVersion(array('name'=>'itemVersionid['.$k.']','selectedid'=>$taskItem['versionid'],'width' =>250,'id'=>'itemVersionid'.$k,'materialid'=>$taskItem['materialid'],'itemid'=>$taskItem['materialitemid'])).'</td>
									<td><input type="text" name="itemQualified['.$i.']" size="7" value="'.$taskItem['finishquantity'].'" '.$quantityReadonly.'></td>
									<td><input type="text" name="itemDisqualified['.$i.']" size="7" value="0" /></td>
									<td><input type="text" name="itemDateline['.$i.']" id="itemDateline'.$i.'" size="13" value="'.date('Y-m-d',TIMENOW).'"></td>
									<td><input type="text" name="itemRemark['.$i.']" style="width:200px;"></td>
									<td><input type="checkbox" name="itemSelect['.$i.']" value="'.$taskItem['itemid'].'"/></td>
								</tr>';
								$i++;
								$id.=$this->kclass->iif($i<$taskitemCount+$k,'#itemDateline'.$i.',','#itemDateline'.$i);
							}
						}
					}else{
						$taskItem=$this->kclass->DB->queryFirst("SELECT DISTINCT taskitemid FROM preinbounditem WHERE module='inbound' AND mid='".$this->kclass->input['inboundid']."' LIMIT 0,1");
						for($i=$k;$i<=$k+12;$i++){
							$itemList.='<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').' small">
								<td><input type="hidden" name="id['.$i.']" value="'.$i.'" />'.$i.'</td>
								<td><input type="hidden" name="taskItemId['.$i.']" value="'.$taskItem['taskitemid'].'"></td>
								<td>'.$this->kclass->chooserMaterial(array('hName'=>'itemMaterialid['.$i.']','hId'=>'itemMaterialid'.$i,'name'=>'itemMaterial['.$i.']','id'=>'itemMaterial'.$i,'line'=>$i,'showVersion'=>1,'width'=>550,'hasBlank'=>1,'topname'=>'')).'</td>
								<td><select name="itemVersionid['.$i.']" id="itemVersionid'.$i.'" style="width:250px;"></select></td>
								<td><input type="text" name="itemQualified['.$i.']" size="7" value="0" '.$quantityReadonly.'></td>
								<td><input type="text" name="itemDisqualified['.$i.']" size="7" value="0" /></td>
								<td><input type="text" name="itemDateline['.$i.']" id="itemBatchTime'.$i.'" size="13"></td>
								<td><input type="text" name="itemRemark['.$i.']" style="width:200px"></td>
								<td></td>
							</tr>';
							if($i<12){
								$id.='#itemBatchTime'.$i.',';
							}else{
								$id.='#itemBatchTime'.$i;
							}
						}
					}
				}else{
					for($i=$k;$i<=$k+12;$i++){
						$itemList.='<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').' small">
							<td><input type="hidden" name="id['.$i.']" value="'.$i.'" />'.$i.'</td>
							<td>'.$this->kclass->chooserMaterial(array('hName'=>'itemMaterialid['.$i.']','hId'=>'itemMaterialid'.$i,'name'=>'itemMaterial['.$i.']','id'=>'itemMaterial'.$i,'line'=>$i,'showVersion'=>1,'width'=>550,'hasBlank'=>1,'topname'=>'')).'</td>
							<td><select name="itemVersionid['.$i.']" id="itemVersionid'.$i.'" style="width:250px;"></select></td>
							<td><input type="text" name="itemQualified['.$i.']" size="7" value="0" '.$quantityReadonly.'></td>
							<td><input type="text" name="itemDisqualified['.$i.']" size="7" value="0" /></td>
							<td><input type="text" name="itemDateline['.$i.']" id="itemBatchTime'.$i.'" size="13"></td>
							<td><input type="text" name="itemRemark['.$i.']" style="width:200px"></td>
							<td></td>
						</tr>';
						if($i<12){
							$id.='#itemBatchTime'.$i.',';
						}else{
							$id.='#itemBatchTime'.$i;
						}
					}
				}
			}
			$inbound['realtime']=date('Y-m-d',$inbound['realtime']);
$body=<<<EOF
<form action="/s.php?module=inbound&action=doupdate" name="inbound" method="post" onsubmit="dc.checkSubmit(this)>
<input type="hidden" name="module" value="inbound" />
<input type="hidden" name="action" value="doupdate" />
<input type="hidden" name="inboundid" value="{$this->kclass->input['inboundid']}">
<input type="hidden" name="departmentid" value="{$this->kclass->input['departmentid']}" />
<input type="hidden" name="porderid" value="{$this->kclass->input['porderid']}" />
<input type="hidden" name="orderid" value="{$this->kclass->input['orderid']}" />
<input type="hidden" name="sampleid" value="{$this->kclass->input['sampleid']}" />
<input type="hidden" name="typeid" value="{$this->kclass->input['typeid']}" />
<input type="hidden" name="noteTypeid" value="{$inType['notetypeid']}">
<input type="hidden" name="workcenterid" value="{$inbound['workcenterid']}" />
<table class="hundred">
<thead></tr><th colspan="4">基本信息</th></tr></thead>
<tbody>
<tr class="even">
<td>入库单号：<span class="red bold">*</span></td><td><input type="text" class="middle bold darkred" name="inboundno" value="{$inbound['inboundno']}" id="inboundno">　转月时间：<input type="text" id="realtime" name="realtime" value="{$inbound['realtime']}" size=10></td>
<td>申请人：<span class="red bold">*</span></td><td><input type="text" name="applicant" value="{$inbound['applicant']}" />　部门： {$inbound['department']} 　入库类型：{$inType['title']}</td>
</tr>
{$r}
<tr class="even">
<td>用　　途：<span class="red bold">*</span></td><td><textarea name="purpose" style="width:444px;height:111px;">{$inbound['purpose']}</textarea></td>
<td>备　　注：</td><td><textarea name="remark" style="width:444px;height:111px;">{$inbound['remark']}</textarea></td>
</tr>
</tbody>
</table>
<table class="hundred">
<thead></tr><th colspan="13">入库申请明细</th></tr></thead>
<tbody>
<tr class="center even" nohover><td width="15">ID</td>{$taskth}<td>物资</td><td width="250">版本 <span class="red bold">*</span></td><td width="60">良品 <span class="red bold">*</span></td><td width="60">不良品 <span class="red bold">*</span></td><td width="90">批次时间 <span class="red bold">*</span></td><td width="200">备注</td><td width="25">删</td></tr>
{$itemList}
</tbody>
<tbody class="small">
<tr class="even">
<td class="small gray" colspan="13">
注意事项：<br>
①如果所列表格不够，那么在提交保存后再点击修改订单来增加物资；<br>
②时间的格式为2012-02-06，中间用半角短横杠隔开；</td>
</tr>
</tbody>
</table>
<table class="tlist tsuite" align="center" width="100%">
<tbody>
	<tr colspan="4" class="even" nohover>
		<td class="center"><input type="submit" id="submitButton" value="  提交  " accesskey="s"><input type="reset" value="  复位  "/></td>
	</tr>
</tbody>
</table>
EOF;
				$this->kclass->tbline+=4;
				$this->kclass->page['title'].=$inbound['inboundno'].' - 修改入库申请单';
				$this->kclass->page['onload'].='var dates=$(\'#realtime,'.$id.'\').datepicker({onSelect:function(selectedDate){instance=$(this).data(\'datepicker\'),date=$.datepicker.parseDate(instance.settings.dateFormat || $.datepicker._defaults.dateFormat,selectedDate,instance.settings);dc.updateNo({\'id\':\'inboundno\',\'typeid\':'.$inType['notetypeid'].'});}});dc.tabhover();';
				$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - <a href="/s.php?module=inbound&action=view&inboundid='.$inbound['inboundid'].'">查看入库单</a> - 修改入库申请单', 'right'=>'<a href="/s.php?module=inbound&action=view&inboundid='.$inbound['inboundid'].'">查看入库单</a>　|　<a href="/s.php?module=inbound&action=list">返回列表</a>','body'=>$body));
			}
		}
	}
	//
	function doupdate(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			if($this->kclass->input['departmentid'] <= 0){
				$e .= '<li>请选择采购单中的 部门。</li>';
			}
			if($this->kclass->input['typeid']=='' ){
				$e .= '<li>请选择入库申请单的 入库类型。</li>';
			}
			if(!$this->kclass->input['applicant']){
				$e .= '<li>请选择采购单中的 申请人。</li>';
			}
			if($this->kclass->input['workcenterid']>0){
				$j=count($this->kclass->input['taskItemId']);
				for($i=1;$i<=$j;$i++){
					if($this->kclass->input['itemMaterialid'][$i]>0 AND $this->kclass->input['itemVersionid'][$i]>0 AND ($this->kclass->input['itemQualified'][$i]>0 OR $this->kclass->input['itemDisqualified'][$i]>0) AND $this->kclass->input['itemDateline'][$i]!=''){
						if($this->kclass->input['itemKill'][$i]>0){
							if($this->kclass->input['itemSelect'][$i]>0)$hasItem=1;
						}else{
							$hasItem=1;
						}
					}
				}
			}else{
				$j=count($this->kclass->input['id']);
				for($i=1;$i<=$j;$i++){
					if($this->kclass->input['itemKill'][$i]=='' AND ($this->kclass->input['itemMaterialid'][$i]>0 OR $this->kclass->input['materialItemid'][$i]>0) AND $this->kclass->input['itemVersionid'][$i]>0 AND ($this->kclass->input['itemQualified'][$i]>0 OR $this->kclass->input['itemDisqualified'][$i]>0) AND $this->kclass->input['itemDateline'][$i]!=''){
						$hasItem=1;
					}
				}
			}
			if($hasItem==0){
				$e.='<li>需要填写至少有一条入库明细，才能建立入库申请单。</li>';
			}
		}
		
		if($e){
			$this->kclass->messager(array(
				'title' => '修改入库申请单',
				'text' => '您在修改入库申请单的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$inbound=$this->kclass->DB->queryFirst("SELECT * FROM inbound WHERE killed=0 AND inboundid='".$this->kclass->input['inboundid']."'");
		$no=$inbound['inboundno'];
		if($this->kclass->input['arrivalTime']){
			$sd=explode('-', $this->kclass->input['arrivalTime']);
			$arrivalTime=mktime(0,0,0,$sd[1],$sd[2],$sd[0]);
		}else{
			$arrivalTime=0;
		}
		if($this->kclass->input['realtime']>0){
			$sd=explode('-', $this->kclass->input['realtime']);
			$realtime=mktime(0,0,0,$sd[1],$sd[2],$sd[0]);
			if($realtime!=$inbound['realtime']){
				$no=$this->kclass->id(array('inbound'=>$realtime,'typeid'=>$this->kclass->input['noteTypeid']));
				$inboundno=' inboundno="'.$no.'",realtime="'.$realtime.'",';
			}
		}

		$this->kclass->input['porderid']=$this->kclass->iif($this->kclass->input['porderid']<=0,0,$this->kclass->input['porderid']);
		$this->kclass->input['supplierid']=$this->kclass->iif($this->kclass->input['supplierid']>0,$this->kclass->input['supplierid'],0);
		$orderid=$this->kclass->iif($this->kclass->input['orderid']>0,$this->kclass->input['orderid'],0);
		$sampleid=$this->kclass->iif($this->kclass->input['sampleid']>0,$this->kclass->input['sampleid'],0);

		$this->kclass->DB->query("
			UPDATE inbound SET
				".$inboundno."
				typeid='".$this->kclass->input['typeid']."',
				noteTypeid='".$this->kclass->input['noteTypeid']."',
				porderid='".$this->kclass->input['porderid']."',
				orderid='".$orderid."',
				sampleid='".$sampleid."',
				supplierid='".$this->kclass->input['supplierid']."',
				orderno='".$this->kclass->input['orderno']."',
				supplier='".$this->kclass->input['supplier']."',
				arrivalno='".$this->kclass->input['arrivalno']."',
				applicant='".$this->kclass->input['applicant']."',
				purpose='".$this->kclass->input['purpose']."',
				arrivalTime='".$arrivalTime."',
				remark='".$this->kclass->input['remark']."',
				ifConfirm=0,confirmer=0,confirmed=0,
				ifVerify=0,verified=0,verifier=0,verifyRemark='',
				modified='".TIMENOW."',
				modifier='".$this->kclass->user['userid']."'
			WHERE inboundid='".$inbound['inboundid']."'
		");
		if($this->kclass->input['workcenterid']>0){
			$j=count($this->kclass->input['itemMaterialid']);
			for($i=1;$i<=$j;$i++){
				if($this->kclass->input['itemMaterialid'][$i]>0 AND $this->kclass->input['itemVersionid'][$i]!='' AND ($this->kclass->input['itemQualified'][$i]>0 OR $this->kclass->input['itemDisqualified'][$i]>0) AND $this->kclass->input['itemDateline'][$i]!=''){
					$sd=explode('-', $this->kclass->input['itemDateline'][$i]);
					$dateline=mktime(0,0,0,$sd[1],$sd[2],$sd[0]);
					$disqualified=$this->kclass->iif(floatval($this->kclass->input['itemDisqualified'][$i])>0,floatval($this->kclass->input['itemDisqualified'][$i]),0);
					$quantity=$this->kclass->iif($this->kclass->input['itemQualified'][$i]>0,$this->kclass->input['itemQualified'][$i],$disqualified);
					$materialid=$this->kclass->iif($this->kclass->input['itemMaterialid'][$i]>0,$this->kclass->input['itemMaterialid'][$i],0);
					$materialitemid=$this->kclass->iif($this->kclass->input['materialItemid'][$i]>0,$this->kclass->input['materialItemid'][$i],0);
					if($materialid>0){
						$material=$this->kclass->DB->queryFirst("SELECT unitid FROM material WHERE materialid='".$materialid."' LIMIT 0,1");
					}
					if($this->kclass->input['itemId'][$i]>0){
						$item=$this->kclass->DB->queryFirst("SELECT itemid,taskitemid FROM preinbounditem WHERE itemid='".$this->kclass->input['itemId'][$i]."' LIMIT 0,1");
						if($this->kclass->input['itemKill'][$i]>0 AND $this->kclass->input['itemKill'][$i]==$this->kclass->input['itemId'][$i]){
							$this->kclass->DB->query("UPDATE preinbounditem SET killed='".TIMENOW."',killer='".$this->kclass->user['userid']."' WHERE itemid='".$item['itemid']."'");
							$this->kclass->DB->query("UPDATE taskitem SET ifChooser=0 WHERE itemid='".$this->kclass->input['taskItemId'][$i]."'");
						}else{
							$this->kclass->DB->query("
								UPDATE preinbounditem SET
									`materialid`='".$materialid."',
									`versionid`='".$this->kclass->input['itemVersionid'][$i]."',
									`materialitemid`='".$materialitemid."',
									`quantity`='".$quantity."',
									`qualified`='".$this->kclass->input['itemQualified'][$i]."',
									`disqualified`='".$disqualified."',
									`dateline`='".$dateline."',
									`remark`='".$this->kclass->input['itemRemark'][$i]."',
									`modified`='".TIMENOW."',
									`modifier`='".$this->kclass->user['userid']."'
								WHERE itemid='".$this->kclass->input['itemId'][$i]."'
							");
						}
					}else{
						if($this->kclass->input['typeid']==3){
							if($this->kclass->input['itemSelect'][$i]>0 AND $this->kclass->input['itemSelect'][$i]==$this->kclass->input['taskItemId'][$i]){
								$this->kclass->DB->query("
									INSERT INTO preinbounditem (`module`,`mid`,`materialid`,`versionid`,`materialitemid`,`taskitemid`,`quantity`,`qualified`,`disqualified`,`unitid`,`dateline`,`remark`,`created`,`creator`)
									VALUES ('inbound','".$inbound['inboundid']."','".$materialid."','".$this->kclass->input['itemVersionid'][$i]."','".$materialitemid."','".$this->kclass->input['itemSelect'][$i]."','".$quantity."','".$this->kclass->input['itemQualified'][$i]."','".$disqualified."','".$material['unitid']."','".$dateline."','".$this->kclass->input['itemRemark'][$i]."','".TIMENOW."','".$this->kclass->user['userid']."')
								");
								$this->kclass->DB->query("UPDATE taskitem SET ifChooser=1 WHERE itemid='".$this->kclass->input['itemSelect'][$i]."'");
							}
						}else{
							if($this->kclass->input['typeid']==5){
								$child=$this->kclass->bomChildList(array('parentid'=>$materialid,'materialitemid'=>$materialitemid,'versionid'=>$this->kclass->input['itemVersionid'][$i],'quantity'=>$quantity,'loopNum'=>1));
								if(!empty($child)){
									foreach($child['bom'] as $val){
										if($val['ifGeneral']==0){
											$this->kclass->DB->query("
												INSERT INTO preinbounditem (`module`,`mid`,`materialid`,`versionid`,`materialitemid`,`taskitemid`,`quantity`,`qualified`,`unitid`,`dateline`,`remark`,`created`,`creator`)
												VALUES ('inbound','".$inbound['inboundid']."','".$val['materialid']."','".$val['versionid']."','".$val['materialitemid']."','".$this->kclass->input['taskItemId'][$i]."','".$val['quantity']."','".$val['quantity']."','".$val['unitid']."','".$dateline."','".$this->kclass->input['itemRemark'][$i]."','".TIMENOW."','".$this->kclass->user['userid']."')
											");
										}
									}
								}
							}else{
								$this->kclass->DB->query("
									INSERT INTO preinbounditem (`module`,`mid`,`materialid`,`versionid`,`materialitemid`,`taskitemid`,`quantity`,`qualified`,`disqualified`,`unitid`,`dateline`,`remark`,`created`,`creator`)
									VALUES ('inbound','".$inbound['inboundid']."','".$materialid."','".$this->kclass->input['itemVersionid'][$i]."','".$materialitemid."','".$this->kclass->input['taskItemId'][$i]."','".$quantity."','".$this->kclass->input['itemQualified'][$i]."','".$disqualified."','".$material['unitid']."','".$dateline."','".$this->kclass->input['itemRemark'][$i]."','".TIMENOW."','".$this->kclass->user['userid']."')
								");
							}
						}
					}
				}
			}
		}else{
			$j=count($this->kclass->input['id']);
			for($i=1;$i<=$j;$i++){
				if($this->kclass->input['itemMaterialid'][$i]>0 AND $this->kclass->input['itemVersionid'][$i]!='' AND ($this->kclass->input['itemQualified'][$i]>0 OR $this->kclass->input['itemDisqualified'][$i]>0) AND $this->kclass->input['itemDateline'][$i]!=''){
					$sd=explode('-', $this->kclass->input['itemDateline'][$i]);
					$dateline=mktime(0,0,0,$sd[1],$sd[2],$sd[0]);
					$disqualified=$this->kclass->iif(floatval($this->kclass->input['itemDisqualified'][$i])>0,floatval($this->kclass->input['itemDisqualified'][$i]),0);
					$quantity=$this->kclass->iif($this->kclass->input['itemQualified'][$i]>0,$this->kclass->input['itemQualified'][$i],$disqualified);
					$materialid=$this->kclass->iif($this->kclass->input['itemMaterialid'][$i]>0,$this->kclass->input['itemMaterialid'][$i],0);
					$productitemid=$this->kclass->iif($this->kclass->input['productItemid'][$i]>0,$this->kclass->input['productItemid'][$i],0);
					$productid=$this->kclass->iif($this->kclass->input['productId'][$i]>0,$this->kclass->input['productId'][$i],0);
					$materialitemid=$this->kclass->iif($this->kclass->input['materialItemid'][$i]>0,$this->kclass->input['materialItemid'][$i],0);
					if($materialid>0){
						$material=$this->kclass->DB->queryFirst("
							SELECT materialid,unitid,quantity,materialno
							FROM material 
							WHERE materialid='".$materialid."'
							LIMIT 0,1
						");
					}
					if($this->kclass->input['itemId'][$i]>0){
						$itemid=$this->kclass->input['itemId'][$i];
						if($this->kclass->input['itemKill'][$i]>0 AND $this->kclass->input['itemKill'][$i]==$itemid){
							$this->kclass->DB->query("UPDATE preinbounditem SET killed='".TIMENOW."',killer='".$this->kclass->user['userid']."' WHERE itemid='".$itemid."'");
						}else{
							$this->kclass->DB->query("
								UPDATE preinbounditem SET
									`materialid`='".$this->kclass->input['itemMaterialid'][$i]."',
									`versionid`='".$this->kclass->input['itemVersionid'][$i]."',
									`materialitemid`='".$materialitemid."',
									`productid`='".$productid."',
									`productitemid`='".$productitemid."',
									`quantity`='".$quantity."',
									`qualified`='".$this->kclass->input['itemQualified'][$i]."',
									`disqualified`='".$disqualified."',
									`dateline`='".$dateline."',
									`remark`='".$this->kclass->input['itemRemark'][$i]."',
									`modified`='".TIMENOW."',
									`modifier`='".$this->kclass->user['userid']."'
								WHERE itemid='".$itemid."'
							");
						}
					}else{
						if($this->kclass->input['typeid']==5){// 提交后插入bom子集
							$child=$this->kclass->bomChildList(array('parentid'=>$this->kclass->input['itemMaterialid'][$i],'materialitemid'=>$this->kclass->input['materialItemid'][$i],'versionid'=>$this->kclass->input['itemVersionid'][$i],'quantity'=>$quantity,'loopNum'=>1));
							if(!empty($child)){
								foreach($child['bom'] as $key => $val){
									if($val['ifGeneral']==0){
										$this->kclass->DB->query("
											INSERT INTO preinbounditem (`module`,`mid`,`materialid`,`versionid`,`materialitemid`,`quantity`,`qualified`,`disqualified`,`unitid`,`dateline`,`remark`,`created`,`creator`)
											VALUES ('inbound','".$inbound['inboundid']."','".$val['materialid']."','".$val['versionid']."','".$materialitemid."','".$val['quantity']."','".$val['quantity']."','".$disqualified."','".$val['unitid']."','".$dateline."','".$this->kclass->input['itemRemark'][$i]."','".TIMENOW."','".$this->kclass->user['userid']."')
										");
									}
								}
							}
						}else{
							$this->kclass->DB->query("
								INSERT INTO preinbounditem
									(`module`,`mid`,`materialid`,`versionid`,`materialitemid`,`productid`,`productitemid`,`quantity`,`qualified`,`disqualified`,`unitid`,`dateline`,`remark`,`created`,`creator`)
								VALUES
									('inbound','".$inbound['inboundid']."','".$material['materialid']."','".$this->kclass->input['itemVersionid'][$i]."','".$materialitemid."','".$productid."','".$productitemid."','".$quantity."','".$this->kclass->input['itemQualified'][$i]."','".$disqualified."','".$material['unitid']."','".$dateline."','".$this->kclass->input['itemRemark'][$i]."','".TIMENOW."','".$this->kclass->user['userid']."')
							");
						}
					}
				}
			}
		}

		$this->kclass->messager(array(
			'title' => '修改入库单',
			'text' => '入库单 【<b>'.$no.'</b>】 已修改成功!返回查看入库单',
			'url' => '/s.php?module=inbound&action=view&inboundid='.$this->kclass->input['inboundid'],
			'sec' => 2
		));
	}
	// 提交审核
	function verify(){
		$inbound=$this->kclass->DB->queryFirst("
			SELECT i.inboundid,i.inboundno,i.applicant,i.orderno,i.supplier,i.porderid,i.orderid,i.sampleid,i.arrivalno,i.purpose,i.arrivalTime,i.remark,i.typeid,i.departmentid,i.workcenterid,i.verifyRemark,i.ifVerify,i.verified,i.ifConfirm,i.modified,i.created,i.killed,i.ifComplete,
				d.title AS department,
				t.title AS type,
				wc.title AS workcenter,
				u.username AS creator,
				us.username AS modifier,
				ur.username AS verifier
			FROM inbound AS i
			LEFT JOIN `inorouttype` AS t ON (t.typeid=i.typeid)
			LEFT JOIN `department` AS d ON (d.departmentid=i.departmentid)
			LEFT JOIN `workcenter` AS wc ON (wc.workcenterid=i.workcenterid)
			LEFT JOIN `user` AS u ON (u.userid=i.creator)
			LEFT JOIN `user` AS us ON (us.userid=i.modifier)
			LEFT JOIN `user` AS ur ON (ur.userid=i.verifier)
			WHERE i.killed=0 AND i.inboundid='".$this->kclass->input['inboundid']."'
			LIMIT 0,1
		");
		if($inbound){
			if($inbound['ifVerify']!=0){
				$this->kclass->boinkIt('/s.php?module=inbound');
			}
			$inbound['created']=date('Y-m-d H:i:s',$inbound['created']);
			$inbound['arrivalTime']=date('Y-m-d',$inbound['arrivalTime']);
			$verify=$this->kclass->verify($inbound['ifVerify']);
			if($inbound['modified']!=0)$modify='，由'.$purchase['modifier'].'于'.$purchase['modified'].'修改';

			if($inbound['porderid']>0){
				$porder=$this->kclass->DB->queryFirst("SELECT o.porderid,o.orderno,o.supplierid,s.title AS supplier FROM porder AS o LEFT JOIN supplier AS s ON (s.supplierid=o.supplierid) WHERE o.porderid=".$inbound['porderid']." LIMIT 0,1");
				$r='<tr class="even"><td>订单编号：</td><td><a href="/s.php?module=porder&action=view&porderid='.$porder['porderid'].'">'.$porder['orderno'].'</a></td><td>供　应　商：</td><td><a href="/s.php?module=supplier&action=view&supplierid='.$porder['supplierid'].'">'.$porder['supplier'].'</a></td></tr><tr class="odd"><td>送货单号：</td><td>'.$inbound['arrivalno'].'</td><td>到达时间：</td><td>'.$inbound['arrivalTime'].'</td></tr>';
			}elseif($inbound['orderid']>0){
				$order=$this->kclass->DB->queryFirst("SELECT orderid,orderno FROM `order` WHERE orderid=".$inbound['orderid']." LIMIT 0,1");
				$r='<tr class="even"><td>订单编号：</td><td><a href="/s.php?module=order&action=view&orderid='.$order['orderid'].'">'.$order['orderno'].'</a></td><td></td><td></td></tr>';
			}elseif($inbound['sampleid']>0){
				$sample=$this->kclass->DB->queryFirst("SELECT sampleid,sampleno FROM sample WHERE sampleid=".$inbound['sampleid']." LIMIT 0,1");
				$r='<tr class="even"><td>样品单编号：</td><td><a href="/s.php?module=sample&action=view&sampleid='.$sample['sampleid'].'">'.$sample['sampleno'].'</a></td><td></td><td></td></tr>';
			}elseif($inbound['typeid']==8){
				$r='<tr class="even"><td>订单编号：</td><td>'.$inbound['orderno'].'</td><td>供　应　商：</td><td>'.$inbound['supplier'].'</td></tr>
						<tr class="odd"><td>送货单号：</td><td>'.$inbound['arrivalno'].'</td><td>到达时间：</td><td>'.$inbound['arrivalTime'].'</td></tr>';
			}

			$items=$this->kclass->DB->query("
				SELECT pi.*,
					mv.title AS version,
					b.batchid
				FROM preinbounditem AS pi
				LEFT JOIN `material` AS m ON (m.materialid=pi.materialid)
				LEFT JOIN `materialversion` AS mv ON (mv.versionid=pi.versionid)
				LEFT JOIN `batch` AS b ON (b.batchid=pi.batchid)
				WHERE pi.killed=0 AND pi.module='inbound' AND pi.mid='".$this->kclass->input['inboundid']."'
				ORDER BY m.materialno ASC
			");
			if($this->kclass->DB->numRows()){
				$i=1;
				$itemList='<table class="hundred small"><thead><tr><th colspan="11">物资列表</th></tr></thead><tbody><tr class="center even"><td width=30>ID</td>'.$this->kclass->iif($inbound['workcenterid']>0,'<td width="100">任务单号</td>','').'<td width="120">物资编号</td><td>物资名称</td><td>物资规格</td><td width="60">物资版本</td><td width="80">总数</td><td width="80">良品数</td><td width="80">不良品数</td><td width="70">批号时间</td><td width="70">备注</td></tr>';
				while($item=$this->kclass->DB->fetchArray($items)){
					$item['dateline']=date('Y-m-d',$item['dateline']);
					$materialInfo=$this->kclass->getMaterial(array('itemid'=>$item['materialitemid'],'materialid'=>$item['materialid']));
					$taskitem=$this->kclass->DB->queryFirst("
						SELECT d.taskid,d.taskno
						FROM taskitem AS t
						LEFT JOIN task AS d ON (d.taskid=t.taskid)
						WHERE t.killed=0 AND d.killed=0 AND t.itemid='".$item['taskitemid']."'
					");
					$itemList.='<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').'">
						<td>'.$i.'</td>
						'.$this->kclass->iif($inbound['workcenterid']>0,'<td><a href="/s.php?module=task&action=view&taskid='.$taskitem['taskid'].'">'.$taskitem['taskno'].'</td>','').'
						<td><a href="'.$materialInfo['url'].'">'.$materialInfo['no'].'</a></td>
						<td>'.$materialInfo['material'].'</td>
						<td>'.$materialInfo['standard'].'</td>
						<td>'.$item['version'].'</td>
						<td>'.$item['quantity'].$materialInfo['unit'].'</td>
						<td>'.$item['qualified'].$materialInfo['unit'].'</td>
						<td>'.$item['disqualified'].$materialInfo['unit'].'</td>
						<td>'.$item['dateline'].'</td>
						<td>'.$item['remark'].'</td></tr>';
					$i++;
				}
				$itemList.='</tbody></table>';
			}

$body=<<<EOF
<div class="title">{$inbound['inboundno']}<span class="right small gray">由{$inbound['creator']}于{$inbound['created']}建立{$modify}。</span></div>
<table class="hundred">
<thead><tr><th colspan="4">入库单 <span class="right">{$addMaterial}</span></th></tr></thead>
<tbody>
<tr class="even">
<td width="100">入库单编号：</td><td width="450">{$inbound['inboundno']}　（{$verify}）</td>
<td width="100">申　请　人：</td><td>{$inbound['applicant']} （{$inbound['department']}）</td>
</tr>
{$r}
<tr class="even">
<td>用　　途：</td><td>{$inbound['purpose']}</td>
<td>备　　注：</td><td>{$inbound['remark']}</td>
</tr>
</tbody>
</table>
{$itemList}
<form method="post" action="/s.php?module=inbound&action=doverify">
<input type="hidden" name="module" value="inbound">
<input type="hidden" name="action" value="doverify">
<input type="hidden" name="inboundid" value="{$this->kclass->input['inboundid']}">
<table>
<thead><tr><th colspan="4">审核</th></tr></thead>
<tbody>
<tr class="even">
<td>审　　核：<span class="bold red">*</span></td><td class="center"><input type="radio" name="ifVerify" value="1" checked />通过　　　<input type="radio" name="ifVerify" value="-1" />不通过</td>
</tr>
<tr class="odd">
<td>审核备注：</td><td><textarea name="verifyRemark" style="width:444px;height:111px;"></textarea></td>
</tr>
<tr class="odd">
<td colspan=2 class="center"><input type="submit" value=" 提交 " /><input type="reset" value="重置" /></td>
</tr>
</tbody>
</table>
</form>
EOF;
		}

		$this->kclass->page['title'].='查看入库单';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - <a href="/s.php?module=inbound&action=list">入库单列表</a> - 查看入库单', 'right' => '<a href="/s.php?module=inbound&action=view&inboundid='.$this->kclass->input['inboundid'].'">返回查看入库申请单</a>　|　<span class="small gray">操作：</span><a href="/s.php?module=inbound&action=add">新建</a>　','body' => $body));
	}
	// 执行审核
	function doverify(){
		$inbound=$this->kclass->DB->queryFirst("SELECT inboundid,inboundno,typeid,orderid,sampleid FROM inbound WHERE killed=0 AND inboundid='".$this->kclass->input['inboundid']."' LIMIT 0,1");
		if(!$inbound){
			$e='<li>数据错误，很抱歉~</li>';
		}else{
			if($this->kclass->input['ifVerify']!=1 AND $this->kclass->input['ifVerify']!=-1){
				$e.='<li>请选择入库申请单中的 是否通过审核 按钮。</li>';
			}
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '审核入库申请单',
				'text' => '审核入库申请单<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}

		$this->kclass->DB->query("
			UPDATE inbound SET
				ifVerify='".$this->kclass->input['ifVerify']."',
				verifier='".$this->kclass->user['userid']."',
				verified='".TIMENOW."',
				verifyRemark='".$this->kclass->input['verifyRemark']."'
			WHERE inboundid='".$this->kclass->input['inboundid']."'
		");
		if($this->kclass->input['ifVerify']==1){
			$verify='已通过';
			if($inbound['typeid']==2){
				$items=$this->kclass->DB->query("
					SELECT quantity,versionid
					FROM preinbounditem
					WHERE killed=0 AND module='inbound' AND mid='".$inbound['inboundid']."'
					ORDER BY created ASC
				");
				if($this->kclass->DB->numRows()){
					while($item=$this->kclass->DB->fetchArray($items)){
						$this->kclass->DB->query("UPDATE materialversion SET planInbound=planInbound+'".$item['quantity']."' WHERE versionid='".$item['versionid']."'");
					}
				}
			}
			if($inbound['orderid']>0){  // 如果是订单入库，更改状态
				$this->kclass->DB->query("UPDATE `order` SET ifInbound=1 WHERE orderid='".$inbound['orderid']."'");
			}elseif($inbound['sampleid']>0){ // 如果是样品入库，更改状态
				$this->kclass->DB->query("UPDATE `sample` SET ifInbound=1 WHERE sampleid='".$inbound['sampleid']."'");
			}
		}elseif($this->kclass->input['ifVerify']==-1){
			$verify='未通过';
		}
		$this->kclass->messager(array(
			'title' => '审核入库申请单',
			'text' => '入库申请单 【<b>'.$inbound['inboundno'].'</b>】 '.$verify.'审核!返回查看入库申请单',
			'url' => '/s.php?module=inbound&action=view&inboundid='.$this->kclass->input['inboundid'],
			'sec' => 2
		));
	}
	// 入库申请单确认
	function confirm(){
		$inbound=$this->kclass->DB->queryFirst("
			SELECT i.inboundid,i.inboundno,i.applicant,i.orderno,i.supplier,i.porderid,i.orderid,i.sampleid,i.arrivalno,i.purpose,i.arrivalTime,i.remark,i.typeid,i.departmentid,i.workcenterid,i.verifyRemark,i.ifVerify,i.verified,i.ifConfirm,i.modified,i.created,i.killed,i.ifComplete,
				d.title AS department,
				t.title AS type,
				wc.title AS workcenter,
				u.username AS creator,
				us.username AS modifier,
				ur.username AS verifier
			FROM inbound AS i
			LEFT JOIN `inorouttype` AS t ON (t.typeid=i.typeid)
			LEFT JOIN `department` AS d ON (d.departmentid=i.departmentid)
			LEFT JOIN `workcenter` AS wc ON (wc.workcenterid=i.workcenterid)
			LEFT JOIN `user` AS u ON (u.userid=i.creator)
			LEFT JOIN `user` AS us ON (us.userid=i.modifier)
			LEFT JOIN `user` AS ur ON (ur.userid=i.verifier)
			WHERE i.killed=0 AND i.inboundid='".$this->kclass->input['inboundid']."'
			LIMIT 0,1
		");
		if($inbound){
			if($inbound['ifVerify']!=0){
				$this->kclass->boinkIt('/s.php?module=inbound');
			}
			$inbound['created']=date('Y-m-d H:i:s',$inbound['created']);
			$inbound['arrivalTime']=date('Y-m-d',$inbound['arrivalTime']);
			$verify=$this->kclass->verify($inbound['ifVerify']);
			if($inbound['modified']!=0)$modify='，由'.$purchase['modifier'].'于'.$purchase['modified'].'修改';

			if($inbound['porderid']>0){
				$porder=$this->kclass->DB->queryFirst("SELECT o.porderid,o.orderno,o.supplierid,s.title AS supplier FROM porder AS o LEFT JOIN supplier AS s ON (s.supplierid=o.supplierid) WHERE o.porderid=".$inbound['porderid']." LIMIT 0,1");
				$r='<tr class="even"><td>订单编号：</td><td><a href="/s.php?module=porder&action=view&porderid='.$porder['porderid'].'">'.$porder['orderno'].'</a></td><td>供　应　商：</td><td><a href="/s.php?module=supplier&action=view&supplierid='.$porder['supplierid'].'">'.$porder['supplier'].'</a></td></tr><tr class="odd"><td>送货单号：</td><td>'.$inbound['arrivalno'].'</td><td>到达时间：</td><td>'.$inbound['arrivalTime'].'</td></tr>';
			}elseif($inbound['orderid']>0){
				$order=$this->kclass->DB->queryFirst("SELECT orderid,orderno FROM `order` WHERE orderid=".$inbound['orderid']." LIMIT 0,1");
				$r='<tr class="even"><td>订单编号：</td><td><a href="/s.php?module=order&action=view&orderid='.$order['orderid'].'">'.$order['orderno'].'</a></td><td></td><td></td></tr>';
			}elseif($inbound['sampleid']>0){
				$sample=$this->kclass->DB->queryFirst("SELECT sampleid,sampleno FROM sample WHERE sampleid=".$inbound['sampleid']." LIMIT 0,1");
				$r='<tr class="even"><td>样品单编号：</td><td><a href="/s.php?module=sample&action=view&sampleid='.$sample['sampleid'].'">'.$sample['sampleno'].'</a></td><td></td><td></td></tr>';
			}elseif($inbound['typeid']==8){
				$r='<tr class="even"><td>订单编号：</td><td>'.$inbound['orderno'].'</td><td>供　应　商：</td><td>'.$inbound['supplier'].'</td></tr>
						<tr class="odd"><td>送货单号：</td><td>'.$inbound['arrivalno'].'</td><td>到达时间：</td><td>'.$inbound['arrivalTime'].'</td></tr>';
			}

			$items=$this->kclass->DB->query("
				SELECT pi.*,
					mv.title AS version,
					b.batchid
				FROM preinbounditem AS pi
				LEFT JOIN `material` AS m ON (m.materialid=pi.materialid)
				LEFT JOIN `materialversion` AS mv ON (mv.versionid=pi.versionid)
				LEFT JOIN `batch` AS b ON (b.batchid=pi.batchid)
				WHERE pi.killed=0 AND pi.module='inbound' AND pi.mid='".$this->kclass->input['inboundid']."'
				ORDER BY m.materialno ASC
			");
			if($this->kclass->DB->numRows()){
				$i=1;
				$itemList='<table class="hundred small"><thead><tr><th colspan="11">物资列表</th></tr></thead><tbody><tr class="center even"><td width=30>ID</td>'.$this->kclass->iif($inbound['workcenterid']>0,'<td width="100">任务单号</td>','').'<td width="120">物资编号</td><td>物资名称</td><td>物资规格</td><td width="60">物资版本</td><td width="80">总数</td><td width="80">良品数</td><td width="80">不良品数</td><td width="70">批号时间</td><td width="70">备注</td></tr>';
				while($item=$this->kclass->DB->fetchArray($items)){
					$item['dateline']=date('Y-m-d',$item['dateline']);
					$materialInfo=$this->kclass->getMaterial(array('itemid'=>$item['materialitemid'],'materialid'=>$item['materialid']));
					$taskitem=$this->kclass->DB->queryFirst("
						SELECT d.taskid,d.taskno
						FROM taskitem AS t
						LEFT JOIN task AS d ON (d.taskid=t.taskid)
						WHERE t.killed=0 AND d.killed=0 AND t.itemid='".$item['taskitemid']."'
					");
					$itemList.='<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').'">
						<td>'.$i.'</td>
						'.$this->kclass->iif($inbound['workcenterid']>0,'<td><a href="/s.php?module=task&action=view&taskid='.$taskitem['taskid'].'">'.$taskitem['taskno'].'</td>','').'
						<td><a href="'.$materialInfo['url'].'">'.$materialInfo['no'].'</a></td>
						<td>'.$materialInfo['material'].'</td>
						<td>'.$materialInfo['standard'].'</td>
						<td>'.$item['version'].'</td>
						<td>'.$item['quantity'].$materialInfo['unit'].'</td>
						<td>'.$item['qualified'].$materialInfo['unit'].'</td>
						<td>'.$item['disqualified'].$materialInfo['unit'].'</td>
						<td>'.$item['dateline'].'</td>
						<td>'.$item['remark'].'</td></tr>';
					$i++;
				}
				$itemList.='</tbody></table>';
			}

$body=<<<EOF
<div class="title">{$inbound['inboundno']}<span class="right small gray">由{$inbound['creator']}于{$inbound['created']}建立{$modify}。</span></div>
<table class="hundred">
<thead><tr><th colspan="4">入库单 <span class="right">{$addMaterial}</span></th></tr></thead>
<tbody>
<tr class="even">
<td width="100">入库单编号：</td><td width="450">{$inbound['inboundno']}　（{$verify}）</td>
<td width="100">申　请　人：</td><td>{$inbound['applicant']} （{$inbound['department']}）</td>
</tr>
{$r}
<tr class="even">
<td>用　　途：</td><td>{$inbound['purpose']}</td>
<td>备　　注：</td><td>{$inbound['remark']}</td>
</tr>
</tbody>
</table>
{$itemList}
<table class="hundred">
<form method="post" action="/s.php?module=inbound&action=doconfirm">
<input type="hidden" name="module" value="inbound">
<input type="hidden" name="action" value="doconfirm">
<input type="hidden" name="inboundid" value="{$this->kclass->input['inboundid']}">
<tbody>
<tr class="odd">
<td colspan=4 class="center"><input type="submit" value=" 确认" /></td>
</tr>
</tbody>
</form>
</table>
EOF;
		}

		$this->kclass->page['title'].='查看入库单';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - <a href="/s.php?module=inbound&action=list">入库单列表</a> - 查看入库单', 'right' => '<a href="/s.php?module=inbound&action=view&inboundid='.$this->kclass->input['inboundid'].'">返回查看入库申请单</a>　|　<span class="small gray">操作：</span><a href="/s.php?module=inbound&action=add">新建</a>　','body' => $body));
	}
	//
	function doconfirm(){
		$inbound=$this->kclass->DB->queryFirst("SELECT inboundid,inboundno,ifConfirm FROM inbound WHERE killed=0 AND inboundid='".$this->kclass->input['inboundid']."' LIMIT 0,1");
		if(!$inbound){
			$e='<li>数据错误，很抱歉~</li>';
		}else{
			if($inbound['ifConfirm']==1){
				$e.='<li>该入库申请单 已被确认。</li>';
			}
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '确认入库申请单',
				'text' => '确认入库申请单<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}

		$this->kclass->DB->query("
			UPDATE inbound SET
				ifConfirm=1,
				confirmer='".$this->kclass->user['userid']."',
				confirmed='".TIMENOW."'
			WHERE inboundid='".$this->kclass->input['inboundid']."'
		");
		$this->kclass->messager(array(
			'title' => '确认入库申请单',
			'text' => '入库申请单 【<b>'.$inbound['inboundno'].'</b>】 已被确认!返回查看入库申请单',
			'url' => '/s.php?module=inbound&action=view&inboundid='.$this->kclass->input['inboundid'],
			'sec' => 2
		));
	}
	// 入库
	function in(){
		$inbound=$this->kclass->DB->queryFirst("
			SELECT i.inboundid,i.inboundno,i.porderid,i.supplierid,i.arrivalTime,
				s.title AS supplier,s.supplierno
			FROM inbound AS i
			LEFT JOIN supplier AS s ON (s.supplierid=i.supplierid)
			WHERE i.killed=0 AND i.inboundid='".$this->kclass->input['inboundid']."'
			LIMIT 0,1
		");
		if(!$inbound){
			$e='<li>数据错误，很抱歉~</li>';
		}else{
			$item=$this->kclass->DB->queryFirst("
				SELECT pi.itemid,pi.materialid,pi.materialitemid,pi.taskitemid,pi.versionid,pi.quantity,pi.qualified,pi.disqualified,pi.unitid,pi.dateline,pi.remark,
					mv.title AS version
				FROM preinbounditem AS pi
				LEFT JOIN materialversion AS mv ON (mv.versionid=pi.versionid)
				WHERE pi.killed=0 AND pi.module='inbound' AND pi.mid='".$inbound['inboundid']."' AND pi.itemid='".$this->kclass->input['itemid']."'
				LIMIT 0,1
			");
			
			$defaultSupplier=$this->kclass->DB->queryFirst("SELECT title,supplierno FROM supplier WHERE supplierid=2");
			if(!$item){
				$e='<li>数据错误，很抱歉~</li>';
			}
			// 即将入库明细数量
			$perItem=$this->kclass->DB->queryFirst("SELECT SUM(quantity) AS quantity FROM preinbounditem WHERE killed=0 AND itemid='".$this->kclass->input['itemid']."'");
			// 已经入库数量
			$inItem=$this->kclass->DB->queryFirst("SELECT SUM(quantity) AS quantity FROM inbounditem WHERE killed=0 AND preitemid='".$this->kclass->input['itemid']."' LIMIT 0,1");
			if($inItem['quantity']>=$perItem['quantity']){
				$e.='<li>该物资已经入库</li>';
			}
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '新建入库记录的批次编号',
				'text' => '新建入库记录的批次编号<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$materialInfo=$this->kclass->getMaterial(array('materialid'=>$item['materialid'],'itemid'=>$item['materialitemid']));
		if($inbound['porderid']>0){
			$order=$this->kclass->DB->queryFirst("SELECT porderid,orderno FROM `porder` WHERE killed=0 AND porderid='".$inbound['porderid']."'");
			$orderItem=$this->kclass->DB->queryFirst("SELECT quantity FROM item WHERE module='porder' AND mid='".$inbound['porderid']."' AND materialid='".$item['materialid']."' LIMIT 0,1");
			$r='<tr class="even"><td>选择订单：<span class="red bold">*</span></td><td><a href="/s.php?module=porder&action=view&porderid='.$order['porderid'].'">'.$order['orderno'].'</a></td><td>计划数量：<span class="red bold">*</span></td><td><input type="text" name="planQuantity" value="'.$orderItem['quantity'].'" /></td></tr>';
		}
		$supplier=$this->kclass->chooserSupplier(array('hName'=>'supplierid','name'=>'supplier','width'=>400,'selectedid'=>$this->kclass->iif($inbound['supplierid'],$inbound['supplierid'],2),'readonly'=>1,'value'=>$this->kclass->iif($inbound['supplierid'],$inbound['supplier'].' ('.$inbound['supplierno'].')',$defaultSupplier['title'].' ('.$defaultSupplier['supplierno'].')')));
		$batchno=$this->kclass->id(array('batch'=>$item['dateline'],'materialno'=>$materialInfo['no']));
		$version=$this->kclass->iif($item['version'],$item['version'],'01');
		$item['dateline']=date('Y-m-d',$item['dateline']);
		$quantity=$this->kclass->iif($item['qualified']==0,$item['disqualified'],$item['qualified']);
		/*// stock item    暂时保留
		for($i=1;$i<=5;$i++){
			$stock=$this->kclass->chooserStock(array('name'=>'stockid['.$i.']','hasBlank'=>1,'topname'=>'','width'=>350));
			$class=$this->kclass->iif($i%2==0,'class="even center"','class="odd center"');
			$itemList.='<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').'">
				<td>'.$stock.'</td>
				<td><input type="text" name="quantity['.$i.']"></td>
				<td><input type="radio" name="ifEmpty['.$i.']" value="0" checked />空闲 <input type="radio" name="ifEmpty['.$i.']" value="1" />不空闲</td></tr>';
		}
	<table class="hundred">
<thead><tr><th colspan="4">选择库位</th></tr></thead>
<tbody>
<tr class="odd even">
<td>选择库位：<span class="red bold">*</span></td>
<td>数量：<span class="red bold">*</span></td>
<td>是否空闲：<span class="red bold">*</span></td>
</tr>
{$itemList}
</tbody>
<tbody>
</tbody>
</table>
	*/
$body=<<<EOF
 <form action="/s.php?module=inbound&action=doin" name="inbound" method="post" onsubmit="dc.checkSubmit(this)">
<input type="hidden" name="module" value="inbound" />
<input type="hidden" name="action" value="doin" />
<input type="hidden" name="itemid" value="{$this->kclass->input['itemid']}" />
<input type="hidden" name="inboundid" value="{$this->kclass->input['inboundid']}" />
<input type="hidden" name="materialid" value="{$item['materialid']}" />
<input type="hidden" name="versionid" value="{$item['versionid']}" />
<input type="hidden" name="materialitemid" value="{$item['materialitemid']}" />
<table class="hundred">
<thead><tr><th colspan="6">批次信息</th></tr></thead>
<tbody>
<tr class="odd">
<td>物　　资：</td><td colspan=5><a href="{$materialInfo['url']}">{$materialInfo['no']}</a>　{$materialInfo['material']}　{$materialInfo['standard']}　　版本：{$version}</td>
</tr>
<tr class="even">
<td width="120">批次编号：<span class="red bold">*</span></td><td width="450"><input type="text" name="batchno" value="{$batchno}" /></td>
<td width="100">批次时间：<span class="red bold">*</span></td><td width="450"><input type="text" name="dateline" value="{$item['dateline']}" id="dateline" /> <span class="small gray">时间按2012-01-01格式输入</span></td>
<td width="100">签 收 人：<span class="red bold">*</span></td><td><input type="text" name="recipient" value="{$this->kclass->user['realname']}" /></td>
</tr>
<tr class="odd">
<td>申请入库数量：<span class="red bold">*</span></td><td><input type="text" name="arrivalQuantity" value="{$quantity}" /></td>
<td>实际数量：<span class="red bold">*</span></td><td><input type="text" name="actualQuantity" value="{$quantity}" /></td>
<td>良品数量：<span class="red bold">*</span></td><td><input type="text" name="qualified" value="{$item['qualified']}" /></td>
</tr>
<tr class="even">
<td>不良数量：<span class="red bold">*</span></td><td><input type="text" name="disqualified" value="{$item['disqualified']}" /></td>
<td>备　　注：</td><td colspan=4><input type="text" name="remark" value="" style="width:720px" /></td>
</tr>
<tr class="even center" nohover><td colspan=6><input type="submit" id="submitButton" value="  提交  " accesskey="s"><input type="reset" value="  复位  "/></td></td></tr>
</tbody>
</table>
</form>
EOF;

		$this->kclass->tbline+=4;
		$this->kclass->page['title'].='物资入库';
		$this->kclass->page['onload'].='dc.tabhover();$(\'#dateline\').datepicker()';
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - <a href="/s.php?module=inbound&action=view&inboundid='.$this->kclass->input['inboundid'].'">查看入库单</a> - 物资入库', 'right'=>'<a href="/s.php?module=inbound&action=view&inboundid='.$this->kclass->input['inboundid'].'">返回查看入库单</a>','body'=>$body));
	}
	// 入库
	function doin(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			if($this->kclass->input['batchno']==''){
				$e.='<li>请填写批次信息的  批次编号 </li>';
			}
			if($this->kclass->input['recipient']==''){
				$e.='<li>请填写批次信息的  签收人 </li>';
			}
			if($this->kclass->input['actualQuantity']==''){
				$e.='<li>请填写批次信息的  实际数量 </li>';
			}
			// 即将入库明细数量
			$perItem=$this->kclass->DB->queryFirst("SELECT SUM(quantity) AS quantity FROM preinbounditem WHERE killed=0 AND itemid='".$this->kclass->input['itemid']."'");
			// 已经入库数量
			$inItem=$this->kclass->DB->queryFirst("SELECT SUM(quantity) AS quantity FROM inbounditem WHERE killed=0 AND preitemid='".$this->kclass->input['itemid']."' LIMIT 0,1");
			if($inItem['quantity']>=$perItem['quantity']){
				$e.='<li>该物资已经入库</li>';
			}

			/* stock 暂时保留
			$hasItem=0;
			$j=count($this->kclass->input['stockid']);
			for($i=1;$i<$j+1;$i++){
				if($this->kclass->input['stockid'][$i]>0 AND intval($this->kclass->input['quantity'][$i])>0 AND ($this->kclass->input['ifEmpty'][$i]==1 OR $this->kclass->input['ifEmpty'][$i]==0)){
					$hasItem=1;
				}
			}
			if($hasItem==0){
				$e.='<li>请选择 库位明细</li>';
			}*/
		}
		if($e){
			$this->kclass->messager(array(
				'title' => '入库物资',
				'text' => '您在入库物资的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$inbound=$this->kclass->DB->queryFirst("SELECT inboundid,inboundno,arrivalno,orderid,sampleid,porderid,supplierid,typeid FROM inbound WHERE inboundid='".$this->kclass->input['inboundid']."'");
		if($this->kclass->input['dateline']!=''){
			$sd=explode('-',$this->kclass->input['dateline']);
			$dateline=mktime(0,0,0,$sd[1],$sd[2],$sd[0]);
		}else{
			$dateline=0;
		}
		$material=$this->kclass->getMaterial(array('materialid'=>$this->kclass->input['materialid'],'itemid'=>$this->kclass->input['materialitemid']));
		$version=$this->kclass->DB->queryFirst("SELECT versionid,title,quantity,qualified,disqualified,price,price1 FROM materialversion WHERE killed=0 AND versionid='".$this->kclass->input['versionid']."'");
		/*// 事务开启
		$mysql=array(); // 存储update或者insert之后的资源数组
		$this->kclass->DB->query("BEGIN");
		$this->kclass->DB->query("SAVEPOINT inbound");*/
		// 物资版本新增或者修改数量
		$mysql[1]=$this->kclass->DB->query("
			UPDATE materialversion SET
				quantity=quantity+'".$this->kclass->input['actualQuantity']."',
				qualified=qualified+'".$this->kclass->input['qualified']."',
				disqualified=disqualified+'".$this->kclass->input['disqualified']."'
			WHERE versionid='".$this->kclass->input['versionid']."'
		");
		$versionid=$this->kclass->input['versionid'];
		// 插入批次  判断是否是重复批次或者是新批次
		$batch=$this->kclass->DB->queryFirst("SELECT batchid FROM batch WHERE killed=0 AND batchno='".$this->kclass->input['batchno']."'");
		if($batch['batchid']){
			$mysql[2]=$this->kclass->DB->query("
				UPDATE batch SET
					versionid='".$versionid."',
					quantity=quantity+'".$this->kclass->input['actualQuantity']."',
					qualified=qualified+'".$this->kclass->input['qualified']."',
					disqualified=disqualified+'".$this->kclass->input['disqualified']."',
					modifier='".$this->kclass->user['userid']."',
					modified='".TIMENOW."'
				WHERE batchid='".$batch['batchid']."'
			");
			$batchid=$batch['batchid'];
		}else{
			$mysql[2]=$this->kclass->DB->query("
				INSERT INTO batch
					(batchno,materialid,materialitemid,versionid,dateline,quantity,qualified,disqualified,remark,created,creator)
				VALUES
					('".$this->kclass->input['batchno']."','".$this->kclass->input['materialid']."','".$this->kclass->input['materialitemid']."','".$versionid."','".$dateline."','".$this->kclass->input['actualQuantity']."','".$this->kclass->input['qualified']."','".$this->kclass->input['disqualified']."','".$this->kclass->input['remark']."','".TIMENOW."','".$this->kclass->user['userid']."')
			");
			$batchid=$this->kclass->DB->insertID();
		}
		//  生成条码
		$c='';
		if($inbound['porderid']>0)$c=' AND supplierid='.$inbound['supplierid'];
		$barcode=$this->kclass->DB->queryFirst("SELECT barcodeid FROM barcode WHERE scraped=0 AND batchid='".$batchid."' AND versionid='".$versionid."' {$c}");
		if($barcode['barcodeid']){
			$mysql[3]=$this->kclass->DB->query("
				UPDATE barcode SET
					quantity=quantity+'".$this->kclass->input['actualQuantity']."',
					qualified=qualified+'".$this->kclass->input['qualified']."',
					disqualified=disqualified+'".$this->kclass->input['disqualified']."',
					modifier='".$this->kclass->user['userid']."',
					modified='".TIMENOW."'
				WHERE barcodeid='".$barcode['barcodeid']."'
			");
			$barcodeid=$barcode['barcodeid'];
		}else{
			if($inbound['supplierid']){
				$supplier=$this->kclass->DB->queryFirst("
					SELECT s.supplierid,s.supplierno,s.regionid, 
						r.countryid,r.abbr
					FROM supplier AS s
					LEFT JOIN region AS r ON (r.regionid=s.regionid)
					WHERE supplierid='".$inbound['supplierid']."'
				");
				if($supplier['countryid']==0){
					$supplier['country']=$supplier['abbr'];
				}else{
					$region=$this->kclass->DB->queryFirst("
						SELECT r.regionid,re.abbr
						FROM region AS r
						LEFT JOIN region AS re ON (re.regionid=r.countryid)
						WHERE r.regionid='".$supplier['regionid']."'
					");
					$supplier['country']=$region['abbr'];
				}
			}else{
				$supplier['supplierno']='0000';
				$supplier['supplierid']='1';
				$supplier['country']='CN';
			}
			$sd=explode('-',date('y-n-j',$dateline));
			$time36='';
			foreach($sd as $val){
				$time36.=strtoupper(base_convert($val,10,36));// 将日期转化为36位进制
			}
			$barcode=$supplier['country'].'-'.$material['materialno'].$this->kclass->iif($this->kclass->input['materialitemid']>0,'-'.$material['no'],'').'-'.$supplier['supplierno'].'-'.$time36.'-'.$version['title'];
			$mysql[3]=$this->kclass->DB->query("
				INSERT INTO barcode (materialid,supplierid,versionid,batchid,barcode,country,materialno,supplierno,arrivalTime,version,quantity,qualified,disqualified,created,creator)
				VALUES ('".$this->kclass->input['materialid']."','".$inbound['supplierid']."','".$versionid."','".$batchid."','".$barcode."','".$supplier['country']."','".$material['materialno']."','".$supplier['supplierno']."','".$dateline."','".$version['title']."','".$this->kclass->input['actualQuantity']."','".$this->kclass->input['qualified']."','".$this->kclass->input['disqualified']."','".TIMENOW."','".$this->kclass->user['userid']."')
			");
			$barcodeid=$this->kclass->DB->insertID();
		}
		$mysql[4]=$this->kclass->DB->query("
			INSERT INTO inbounditem (inboundid,batchid,orderid,sampleid,barcodeid,materialid,materialitemid,preitemid,versionid,quantity,stockid,unitid,dateTime,created,creator)
			VALUES ('".$this->kclass->input['inboundid']."','".$batchid."','{$inbound['orderid']}','{$inbound['sampleid']}','".$barcodeid."','".$this->kclass->input['materialid']."','".$this->kclass->input['materialitemid']."','".$this->kclass->input['itemid']."','".$versionid."','".$this->kclass->input['actualQuantity']."','0','".$material['unitid']."','".$dateline."','".TIMENOW."','".$this->kclass->user['userid']."')
		");
		$initemid=$this->kclass->DB->insertID();
		//是否为外发加工原材料,若非则修改单价
		$hasmaterial = $this->kclass->DB->queryFirst("SELECT COUNT(itemid) FROM entrustitem WHERE materialid='".$this->kclass->input['materialid']."'");
		if($hasmaterial['COUNT(itemid)']==0){
			$this->__updatePrice(array('materialid'=>$this->kclass->input['materialid'],'materialitemid'=>$this->kclass->input['materialitemid'],'versionid'=>$version['versionid'],'price'=>$version['price'],'price1'=>$version['price1'],'quantity'=>$version['qualified'],'inQuantity'=>$this->kclass->input['actualQuantity'],'typeid'=>$inbound['typeid'],'porderid'=>$inbound['porderid'],'inboundid'=>$inbound['inboundid'],'initemid'=>$initemid));
		}
		$hasItem = $this->kclass->DB->queryFirst("SELECT COUNT(itemid) FROM item WHERE module='porder' and mid='".$inbound['porderid']."' and materialid='".$this->kclass->input['materialid']."'");
		// 新增到货记录
		if($inbound['porderid']>0){
			//若物料存在于item中,则查询出itemid,否则默认为0
			if($hasItem['COUNT(itemid)']>0){
					$item=$this->kclass->DB->queryFirst("
						SELECT itemid
						FROM item
						WHERE killed=0 AND module='porder' AND mid='".$inbound['porderid']."' AND materialid='".$this->kclass->input['materialid']."'
						LIMIT 0,1
					");
					$itemid = $item['itemid'];
			}else{
				$itemid = "0";
			}
			$this->kclass->DB->query("
				INSERT INTO ordertracking (`module`,`mid`,`itemid`,`initemid`,`materialid`,`inboundid`,`arrivalno`,`quantity`,`unitid`,`dateline`,`remark`,`created`,`creator`)
				VALUES ('porder','".$inbound['porderid']."','".$itemid."','".$initemid."','".$this->kclass->input['materialid']."','".$this->kclass->input['inboundid']."','".$inbound['arrivalno']."','".$this->kclass->input['actualQuantity']."','".$material['unitid']."','".$dateline."','".$this->kclass->input['itemRemark']."','".TIMENOW."','".$this->kclass->user['userid']."')
			");
		}
			/*  分配库位  暂时保留
			if($this->kclass->input['stockid'][$m]>0 AND intval($this->kclass->input['quantity'][$m])>0 AND ($this->kclass->input['ifEmpty'][$m]==1 OR $this->kclass->input['ifEmpty'][$m]==0)){
				$this->kclass->DB->query("
					INSERT INTO inbounditem
						(inboundid,batchid,barcodeid,materialid,versionid,quantity,stockid,unitid,dateTime,created,creator)
					VALUES
						('".$this->kclass->input['inboundid']."','".$batchid."','".$barcodeid."','".$this->kclass->input['materialid']."','".$versionid."','".$this->kclass->input['quantity'][$m]."','".$this->kclass->input['stockid'][$m]."','".$material['unitid']."','".$dateline."','".TIMENOW."','".$this->kclass->user['userid']."')
				");
				//
				$this->kclass->DB->query("
					INSERT INTO stockitem
						(stockid,materialid,quantity,batchid,barcodeid,created,creator)
					VALUES
						('".$this->kclass->input['stockid'][$m]."','".$this->kclass->input['materialid']."','".$this->kclass->input['quantity'][$m]."','".$batchid."','".$barcodeid."','".TIMENOW."','".$this->kclass->user['userid']."')
				");
				$this->kclass->DB->query("UPDATE stock SET ifEmpty='".$this->kclass->input['ifEmpty'][$m]."' WHERE stockid='".$this->kclass->input['stockid'][$m]."'");
			}
		}*/
		// 总数的变化
		$mysql[5]=$this->kclass->DB->query("UPDATE material SET quantity=quantity+'".$this->kclass->input['actualQuantity']."',qualified=qualified+'".$this->kclass->input['qualified']."',disqualified=disqualified+'".$this->kclass->input['disqualified']."' WHERE materialid='".$this->kclass->input['materialid']."'");
		if($this->kclass->input['materialitemid']>0){
			$this->kclass->DB->query("UPDATE materialitem SET quantity=quantity+'".$this->kclass->input['actualQuantity']."',qualified=qualified+'".$this->kclass->input['qualified']."',disqualified=disqualified+'".$this->kclass->input['disqualified']."' WHERE itemid='".$this->kclass->input['materialitemid']."'");
		}

		// 物料收发卡(收)
		$mysql[6]=$this->kclass->DB->query("
			INSERT inoutrecord (`module`,`mid`,`materialid`,`materialitemid`,`versionid`,`itemid`,`addition`,`qualified`,`balance`,`disqualified`,`disbalance`,`dateline`,`created`,`creator`)
			VALUE ('inbound','".$this->kclass->input['inboundid']."','".$this->kclass->input['materialid']."','".$this->kclass->input['materialitemid']."','".$versionid."','".$initemid."','+','".$this->kclass->input['qualified']."','".($version['qualified']+$this->kclass->input['qualified'])."','".$this->kclass->input['disqualified']."','".($version['disqualified']+$this->kclass->input['disqualified'])."','".TIMENOW."','".TIMENOW."','".$this->kclass->user['userid']."')
		");

		// 判断入库单入库的状态 并跳转
		$inStatus='';
		$prein=$this->kclass->DB->queryFirst("
			SELECT SUM(quantity) AS count
			FROM preinbounditem
			WHERE killed=0 AND module='inbound' AND mid='".$this->kclass->input['inboundid']."'
			LIMIT 0,1
		");
		$in=$this->kclass->DB->queryFirst("
			SELECT SUM(quantity) AS count
			FROM inbounditem
			WHERE killed=0 AND inboundid='".$this->kclass->input['inboundid']."'
			LIMIT 0,1
		");
		if($prein['count']>$in['count'] AND $in['count']>0){
			$inStatus='部分入库';
			$inShow=1;
			// 修改入库单状态 部分入库
			$this->kclass->DB->query("UPDATE inbound SET ifComplete=1 WHERE inboundid='".$this->kclass->input['inboundid']."'");
		}elseif($prein['count']==$in['count']){
			$inStatus='入库完成';
			$inShow=2;
			$items=$this->kclass->DB->query("
				SELECT quantity,versionid
				FROM inbounditem
				WHERE killed=0 AND inboundid='".$this->kclass->input['inboundid']."'
			");
			if($this->kclass->DB->numRows()){
				while($item=$this->kclass->DB->fetchArray($items)){
					$version=$this->kclass->DB->queryFirst("SELECT planInbound FROM materialversion WHERE versionid='".$item['versionid']."'");
					if($version['planInbound']-$item['quantity']<0){
						$planInbound=0;
					}else{
						$planInbound=$version['planInbound']-$item['quantity'];
					}
					$this->kclass->DB->query("UPDATE materialversion SET planInbound='".$planInbound."' WHERE versionid='".$item['versionid']."'");
				}
			}
			// 修改入库单状态 入库完成
			$this->kclass->DB->query("UPDATE inbound SET ifComplete=2 WHERE inboundid='".$this->kclass->input['inboundid']."'");

			if($inbound['porderid']>0){ // 修改采购订单到货情况
				$orderQuantity=$this->kclass->DB->queryFirst("SELECT SUM(quantity) AS count FROM item WHERE killed=0 AND module='porder' AND mid='".$inbound['porderid']."'");
				$trackingQuantity=$this->kclass->DB->queryFirst("SELECT SUM(quantity) AS count FROM ordertracking WHERE killed=0 AND module='porder' AND mid='".$inbound['porderid']."'");
				if(!$trackingQuantity['count']){ // 未到货
					$this->kclass->DB->query("UPDATE porder SET arrivalStatus=0 WHERE porderid='".$inbound['porderid']."'");
				}elseif($trackingQuantity['count']<$orderQuantity['count'] AND $trackingQuantity['count']>0){ // 部分到货
					$this->kclass->DB->query("UPDATE porder SET arrivalStatus=1 WHERE porderid='".$inbound['porderid']."'");
				}elseif($trackingQuantity['count']>=$orderQuantity['count']){// 已到货
					$this->kclass->DB->query("UPDATE porder SET arrivalStatus=2,orderStatus=1 WHERE porderid='".$inbound['porderid']."'");
				}
			}elseif($inbound['orderid']>0){  // 如果是销售订单入库，更改状态
				$this->kclass->DB->query("UPDATE `order` SET ifInbound=2 WHERE orderid='".$inbound['orderid']."'");
			}elseif($inbound['sampleid']>0){ // 如果是样品入库，更改状态
				$this->kclass->DB->query("UPDATE `sample` SET ifInbound=2 WHERE sampleid='".$inbound['sampleid']."'");
			}
		}elseif(!$in['count']){
			$inStatus='等待入库';
			$inShow=0;
		}
		
		/*// 事务回滚
		if(count($mysql)<6){
			$this->kclass->DB->query("ROLLBACK to inbound");
		}else{
			$this->kclass->DB->query("COMMIT");
		}*/
		$this->kclass->messager(array(
			'title' => '入库物资',
			'text' => '入库物资 入库成功!返回添加查看入库单',
			'url' => '/s.php?module=inbound&action=view&inboundid='.$this->kclass->input['inboundid'],
			'sec' => 2
		));
	}
	// 单个物资入库
	function perBarcode(){
		$barcode=$this->kclass->DB->queryFirst("SELECT COUNT(*) AS count FROM barcode WHERE barcode.scraped=0 AND parentid='".$this->kclass->input['barcodeid']."' LIMIT 0,1");
		if(!$barcode){
			$e='<li>数据错误，很抱歉~</li>';
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '流水号条码列表',
				'text' => '流水号条码列表<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$this->kclass->input['min']=$this->kclass->iif($this->kclass->input['min'],$this->kclass->input['min'],0);
		$perBarcodes=$this->kclass->DB->query("
			SELECT barcodeid,barcode,parentid,
			FROM barcode
			WHERE scraped=0 AND parentid='".$this->kclass->input['barcodeid']."'
			LIMIT ".$this->kclass->input['min'].",500
		");
		if($counter=$this->kclass->DB->numRows()){
			$index=1;
			while($perBarcode=$this->kclass->DB->fetchArray($perBarcodes)){
				$barcodetr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').'">
					<td>'.$index.'</td><td>'.$perBarcode['barcode'].'</td><td></td><td><a href="/s.php?module=inbound&action=perin&inboundid='.$this->kclass->input['inboundid'].'&barcodeid='.$perBarcode['barcodeid'].'">入库</a></td>';
				$index++;
			}
		}else{
			$barcodetr.='<tr class="center"><td colspan="4">等待检测...</td></tr>';
		}
		$number=$barcode['count']/500;
		for($i=0;$i<$number;$i++){
			$link.='<a href="/s.php?module=inbound&action=perBarcode&min='.($i*500).'&max='.(($i+1)*500).'">'.($i*500+1).'~'.(($i+1)*500).'</a>　';
		}

$body=<<<EOF
<div class="normal"><span class="gray">流水号条码范围：</span>{$link}</div>
<table>
<thead></tr><th colspan="4">流水号条码列表</th></tr></thead>
<tbody>
<tr class="even center"><td width="50">ID</td><td>流水号条码</td><td>状态</td><td>操作</td></tr>
{$barcodetr}
</tbody>
</table>
EOF;
		$this->kclass->page['title'].='添加入库的物资';
		$this->kclass->page['onload'].='dc.tabhover();dc.chooserDate()';
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - <a href="/s.php?module=inbound&action=view&inboundid='.$this->kclass->input['inboundid'].'">查看入库单</a> - 添加入库物资', 'right'=>'<a href="/s.php?module=inbound&action=view&inboundid='.$this->kclass->input['inboundid'].'">返回查看入库单</a>','body'=>$body));
	}
	//
	function perin(){
		$barcode=$this->kclass->DB->queryFirst("SELECT barcodeid,barcode,materialid FROM barcode WHERE barcode.scraped=0 AND barcodeid='".$this->kclass->input['barcodeid']."' LIMIT 0,1");
		if(!$barcode){
			$e='<li>数据错误，很抱歉~</li>';
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '流水号条码列表',
				'text' => '流水号条码列表<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$attributes=$this->kclass->DB->query("
			SELECT an.nameid,an.title AS name,
				av.title AS value
			FROM  attributename AS an
			LEFT JOIN attributevalue AS av ON (av.nameid=an.nameid)
			WHERE an.killed=0 AND an.module='barcode' AND an.mid='".$barcode['barcodeid']."'
			ORDER BY an.ordering ASC
		");
		$nameId=$title=$value=array();
		if($this->kclass->DB->numRows()){
			while($attribute=$this->kclass->DB->fetchArray($attributes)){
				if(in_array($attribute['nameid'],$nameId)){
					$title[$attribute['nameid']]=$attribute['name'];
					$value[$attribute['nameid']].=$attribute['value'].'<br>';
				}else{
					$nameId[]=$attribute['nameid'];
					$title[$attribute['nameid']]=$attribute['name'];
					$value[$attribute['nameid']]=$attribute['value'].'<br>';
				}
			}
			foreach($nameId as $key => $val){
				$attributetr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').'"><td>'.$title[$val].'</td><td>'.$value[$val].' '.$unit[$val].'</td></tr>';
			}
		}
		$stock=$this->kclass->chooserStock(array('name'=>'stockid','hasBlank'=>1,'topname'=>''));
$body=<<<EOF
<form action="/s.php?module=inbound&action=doperin" method="post">
<input type="hidden" name="module" value="inbound" />
<input type="hidden" name="action" value="doperin" />
<input type="hidden" name="inboundid" value="{$this->kclass->input['inboundid']}" />
<input type="hidden" name="barcodeid" value="{$this->kclass->input['barcodeid']}" />
<table class="tlist tsuite" align="center" width="40%">
<thead></tr><th colspan="2">流水号条码入库</th></tr></thead>
<tbody>
<tr><td class="bold">条码</td><td class="title">{$barcode['barcode']}</td></tr>
<tr class="even center"><td>属性</td><td>属性值</td></tr>
{$attributetr}
</tbody>
</table>
<table class="tlist tsuite" align="center" width="40%">
<thead></tr><th colspan="2">选择库位</th></tr></thead>
<tbody>
<tr><td>选择库位</td><td>{$stock} <input type="radio" name="ifEmpty" value="0" checked />空闲 <input type="radio" name="ifEmpty" value="1" />不空闲</td></td></tr>
<tr class="odd" nohover>
<td class="center" colspan="2"><input type="submit" value="  提交  " accesskey="s"><input type="reset" value="  复位  "/></td>
</tr>
</tbody>
</table>
</form>
EOF;
		$this->kclass->page['title'].='流水号条码入库';
		$this->kclass->page['onload'].='dc.tabhover();';
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - <a href="/s.php?module=inbound&action=view&inboundid='.$this->kclass->input['inboundid'].'">查看入库单</a> - 流水号条码入库', 'right'=>'<a href="/s.php?module=inbound&action=view&inboundid='.$this->kclass->input['inboundid'].'">返回查看入库单</a>','body'=>$body));
	}
	//
	function doperin(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}
		if($e){
			$this->kclass->messager(array(
				'title' => '流水号条码入库',
				'text' => '您在入库流水号条码的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$barcode=$this->kclass->DB->queryFirst("
			SELECT b.barcodeid,b.barcode,b.version,b.materialid,b.parentid,
				material.materialno,material.title,material.standard,material.unitid,
				ba.batchid
			FROM barcode AS b
			LEFT JOIN material ON (material.materialid=b.materialid)
			LEFT JOIN barcode AS ba ON (ba.barcodeid=b.parentid)
			WHERE b.scraped=0 AND b.barcodeid='".$this->kclass->input['barcodeid']."'
			LIMIT 0,1
		");
		$batch=$this->kclass->DB->queryFirst("SELECT batchid,versionid FROM batch WHERE killed=0 AND batchid='".$barcode['batchid']."'");
		$this->kclass->DB->query("
			INSERT INTO inbounditem
				(inboundid,batchid,barcodeid,materialid,versionid,quantity,stockid,unitid,dateTime,created,creator)
			VALUES
				('".$this->kclass->input['inboundid']."','".$barcode['batchid']."','".$barcode['barcodeid']."','".$barcode['materialid']."','".$batch['versionid']."','1','".$this->kclass->input['stockid']."','".$barcode['unitid']."','".TIMENOW."','".TIMENOW."','".$this->kclass->user['userid']."')
		");
		//存放至库位中
		$this->kclass->DB->query("
			INSERT INTO stockitem
				(stockid,materialid,quantity,batchid,barcodeid,created,creator)
			VALUE
				('".$this->kclass->input['stockid']."','".$barcode['materialid']."',1,'".$barcode['batchid']."','".$barcode['barcodeid']."','".TIMENOW."','".$this->kclass->user['userid']."')
		");
		if($this->kclass->input['ifEmpty']==1){
			$this->kclass->DB->query("UPDATE stock SET ifEmpty=1 WHERE killed=0 AND stockid='".$this->kclass->input['stockid']."'");
		}
		//物资数量增加
		$this->kclass->DB->query("UPDATE `material` SET `quantity`=`quantity`+1, `qualified`=`qualified`+1 WHERE	materialid='".$barcode['materialid']."'");
		$this->kclass->messager(array(
			'title' => '流水号条码入库',
			'text' => '流水号条码 <b>'.$barcode['barcode'].'</b> 入库成功!返回流水号条码入列表',
			'url' => '/s.php?module=inbound&action=perBarcode&inboundid='.$this->kclass->input['inboundid'].'&barcodeid='.$barcode['parentid'],
			'sec' => 2
		));
	}
	// 全部接收
	function inAll(){
		$inbound=$this->kclass->DB->queryFirst("SELECT * FROM inbound WHERE killed=0 AND ifComplete<>2 AND inboundid='".$this->kclass->input['inboundid']."' LIMIT 0,1");
		if(!$inbound){
			$e='<li>数据错误，很抱歉~</li>';
		}else{
			// 即将出库数量
			$perItem=$this->kclass->DB->queryFirst("SELECT SUM(quantity) AS quantity FROM preinbounditem WHERE killed=0 AND module='inbound' AND mid='".$inbound['inboundid']."'");
			// 已经出库数量
			$inItem=$this->kclass->DB->queryFirst("SELECT SUM(quantity) AS quantity FROM inbounditem WHERE killed=0 AND inboundid='".$inbound['inboundid']."' LIMIT 0,1");
			if($inItem['quantity']>=$perItem['quantity']){
				$e.='<li>该出库单已全部入库</li>';
			}
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '新建全部入库的物资',
				'text' => '新建全部入库的物资<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$id=array();
		$inItems=$this->kclass->DB->query("SELECT DISTINCT preitemid FROM inbounditem WHERE killed=0 AND inboundid='".$inbound['inboundid']."'");
		if($this->kclass->DB->numRows($inItems)){
			while($inItem=$this->kclass->DB->fetchArray($inItems)){
				$id[]=$inItem['preitemid'];
			}
		}
		$items=$this->kclass->DB->query("
			SELECT pi.itemid,pi.materialid,pi.materialitemid,pi.versionid,pi.quantity,pi.qualified,pi.disqualified,pi.unitid,pi.dateline,pi.remark,
				mv.title AS version,mv.`change`
			FROM preinbounditem AS pi
			LEFT JOIN materialversion AS mv ON (mv.versionid=pi.versionid)
			LEFT JOIN material AS m ON (m.materialid=pi.materialid)
			WHERE pi.killed=0 AND pi.module='inbound' AND pi.mid='".$inbound['inboundid']."'
			ORDER BY m.materialno ASC
		");
		if($counter=$this->kclass->DB->numRows()){
			$i=1;
			while($item=$this->kclass->DB->fetchArray($items)){
				if(!in_array($item['itemid'],$id)){
					$quantity=$this->kclass->iif($item['qualified']==0,$item['disqualified'],$item['qualified']);
					$materialInfo=$this->kclass->getMaterial(array('materialid'=>$item['materialid'],'itemid'=>$item['materialitemid']));
					$batchno=$this->kclass->id(array('batch'=>$item['dateline'],'materialno'=>$materialInfo['no']));
					$itemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').'">
						<td><input type="hidden" name="itemId['.$i.']" value="'.$item['itemid'].'">
							<input type="hidden" name="itemVersionId['.$i.']" value="'.$item['versionid'].'">'.$i.'</td>
						<td class="small"><a href="'.$materialInfo['url'].'">'.$materialInfo['no'].'</a>　'.$materialInfo['material'].'　'.$materialInfo['standard'].'</td>
						<td><a href="#" class="tip" title="'.$item['change'].'">'.$item['version'].'</a></td>
						<td><input type="text" name="itemBatchno['.$i.']" value="'.$batchno.'" size="15" /></td>
						<td><input type="text" name="itemDateline['.$i.']" value="'.date('Y-m-d',$item['dateline']).'" id="dateline" size="10" /></td>
						<td>'.$item['quantity'].'</td>
						<td><input type="text" name="itemQuantity['.$i.']" value="'.$quantity.'" size="7" /></td>
						<td><input type="text" name="itemQualified['.$i.']" value="'.$item['qualified'].'" size="7" /></td>
						<td><input type="text" name="itemDisqualified['.$i.']" value="'.$item['disqualified'].'" size="7" /></td>
						<td><input type="text" name="itemRemark['.$i.']" value="'.$item['remark'].'" size="20" /></td>
						<td><input class="select" type="checkbox" name="itemSelect['.$i.']" value="'.$item['itemid'].'"></td>
					</tr>';
					$dateId.=$this->kclass->iif($i<$counter,'#dateline'.$i.',','#dateline'.$i);
					$i++;
				}
			}
		}
		$body=<<<EOF
<form action="/s.php?module=inbound&action=doinAll" name="inbound" method="post" onsubmit="dc.checkSubmit(this)">
<input type="hidden" name="module" value="inbound" />
<input type="hidden" name="action" value="doinAll" />
<input type="hidden" name="inboundid" value="{$this->kclass->input['inboundid']}" />
<table class="hundred"><thead><tr><th colspan=12>选择明细接收 <span class="right">全选　<input type="checkbox" name="itemAll" id="selectAll" value="1"></span></th></tr></thead><tbody>
<tr class="center bold"><td width=30>ID</td><td>物资</td><td width="30">版本</td><td width="120">批次 <span class="red bold">*</span></td><td width="90">批次时间 <span class="red bold">*</span></td><td width="50">申请数</td><td width="70">实际数 <span class="red bold">*</span></td><td width="70">良品 <span class="red bold">*</span></td><td width="70">不良品 <span class="red bold">*</span></td><td width="150">备注</td><td>选</td></tr>
{$itemtr}
<tr class="odd" nohover>
<td class="center" colspan=12><input type="submit" id="submitButton" value="  提交  " accesskey="s"><input type="reset" value="  复位  "/></td>
</tr>
</tbody></table>
</form>
EOF;
		$this->kclass->tbline+=4;
		$this->kclass->page['title'].='选择明细接收';
		$this->kclass->page['onload'].='dc.tabhover();$(\''.$dateId.'\').datepicker();dc.selectAll();dc.tips()';
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - <a href="/s.php?module=inbound&action=view&inboundid='.$this->kclass->input['inboundid'].'">查看入库单</a> - 选择明细接收', 'right'=>'<a href="/s.php?module=inbound&action=view&inboundid='.$this->kclass->input['inboundid'].'">查看入库单</a>','body'=>$body));
	}
	// 
	function doinAll(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			$hasItem=0;
			$j=count($this->kclass->input['itemId']);
			for($i=1;$i<$j+1;$i++){
				if($this->kclass->input['itemSelect'][$i]>0 AND $this->kclass->input['itemBatchno'][$i]!='' AND $this->kclass->input['itemDateline'][$i]!='' AND $this->kclass->input['itemQuantity'][$i]>0 AND $this->kclass->input['itemQualified'][$i]>=0 AND $this->kclass->input['itemDisqualified'][$i]>=0){
					$hasItem=1;
					$item=$this->kclass->DB->queryFirst("
						SELECT pi.itemid,pi.materialid,pi.quantity,
							m.materialno
						FROM preinbounditem AS pi
						LEFT JOIN material AS m ON (m.materialid=pi.materialid)
						WHERE pi.killed=0 AND pi.module='inbound' AND pi.mid='".$this->kclass->input['inboundid']."' AND pi.itemid='".$this->kclass->input['itemId'][$j]."'
						LIMIT 0,1
					");
					$in=$this->kclass->DB->queryFirst("SELECT COUNT(quantity) AS quantity FROM inbounditem WHERE killed=0 AND preitemid='".$item['itemid']."' LIMIT 0,1");
					if($in['quantity']>=$item['quantity']){
						$e.='<li>物资'.$item['materialno'].'已经被接收</li>';
					}
				}
			}
			if($hasItem==0){
				$e.='<li>请选择并且将明细信息填写完整</li>';
			}
			// 即将入库数量
			$perItem=$this->kclass->DB->queryFirst("SELECT SUM(quantity) AS quantity FROM preinbounditem WHERE killed=0 AND module='inbound' AND mid='".$this->kclass->input['inboundid']."'");
			// 已经入库数量
			$inItem=$this->kclass->DB->queryFirst("SELECT SUM(quantity) AS quantity FROM inbounditem WHERE killed=0 AND inboundid='".$this->kclass->input['inboundid']."' LIMIT 0,1");
			if($outItem['quantity']>=$perItem['quantity']){
				$e.='<li>该出库单已全部出库</li>';
			}
		}
		if($e){
			$this->kclass->messager(array(
				'title' => '新建订单',
				'text' => '您在新建订单的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$inbound=$this->kclass->DB->queryFirst("SELECT inboundid,inboundno,typeid,porderid,orderid,sampleid,supplierid FROM inbound WHERE inboundid='".$this->kclass->input['inboundid']."' LIMIT 0,1");
		for($n=1;$n<=$j;$n++){
			if($this->kclass->input['itemSelect'][$n]>0 AND $this->kclass->input['itemBatchno'][$n]!='' AND $this->kclass->input['itemDateline'][$n]!='' AND $this->kclass->input['itemQuantity'][$n]>0 AND $this->kclass->input['itemQualified'][$n]>=0 AND $this->kclass->input['itemDisqualified'][$n]>=0){
				$item=$this->kclass->DB->queryFirst("
					SELECT itemid,materialid,materialitemid,quantity,versionid
					FROM preinbounditem 
					WHERE killed=0 AND module='inbound' AND mid='".$this->kclass->input['inboundid']."' AND itemid='".$this->kclass->input['itemId'][$n]."'
					LIMIT 0,1
				");
				if($this->kclass->input['itemDateline'][$n]!=''){
					$sd=explode('-',$this->kclass->input['itemDateline'][$n]);
					$dateline=mktime(0,0,0,$sd[1],$sd[2],$sd[0]);
				}else{
					$dateline=0;
				}
				$material=$this->kclass->getMaterial(array('materialid'=>$item['materialid'],'itemid'=>$item['materialitemid']));
				$version=$this->kclass->DB->queryFirst("SELECT title,quantity,qualified,disqualified,price,price1 FROM materialversion WHERE killed=0 AND versionid='".$item['versionid']."'");
				// 物资版本新增或者修改数量
				$this->kclass->DB->query("
					UPDATE materialversion SET
						quantity=quantity+'".$this->kclass->input['itemQuantity'][$n]."',
						qualified=qualified+'".$this->kclass->input['itemQualified'][$n]."',
						disqualified=disqualified+'".$this->kclass->input['itemDisqualified'][$n]."'
					WHERE versionid='".$item['versionid']."'
				");
				$versionid=$item['versionid'];

				// 插入批次  判断是否是重复批次或者是新批次
				$batch=$this->kclass->DB->queryFirst("SELECT batchid FROM batch WHERE killed=0 AND batchno='".$this->kclass->input['itemBatchno'][$n]."'");
				if($batch['batchid']){
					$this->kclass->DB->query("
						UPDATE batch SET
							versionid='".$versionid."',
							quantity=quantity+'".$this->kclass->input['itemQuantity'][$n]."',
							qualified=qualified+'".$this->kclass->input['itemQualified'][$n]."',
							disqualified=disqualified+'".$this->kclass->input['itemDisqualified'][$n]."',
							modifier='".$this->kclass->user['userid']."',
							modified='".TIMENOW."'
						WHERE batchid='".$batch['batchid']."'
					");
					$batchid=$batch['batchid'];
				}else{
					$this->kclass->DB->query("
						INSERT INTO batch
							(batchno,materialid,materialitemid,versionid,dateline,quantity,qualified,disqualified,created,creator)
						VALUES
							('".$this->kclass->input['itemBatchno'][$n]."','".$item['materialid']."','".$item['materialitemid']."','".$versionid."','".$dateline."','".$this->kclass->input['itemQuantity'][$n]."','".$this->kclass->input['itemQualified'][$n]."','".$this->kclass->input['itemDisqualified'][$n]."','".TIMENOW."','".$this->kclass->user['userid']."')
					");
					$batchid=$this->kclass->DB->insertID();
				}
				//  生成条码
				if($inbound['porderid']>0)$c=' AND supplierid='.$inbound['supplierid'];
				$barcode=$this->kclass->DB->queryFirst("SELECT barcodeid FROM barcode WHERE scraped=0 AND batchid='".$batchid."' AND versionid='".$versionid."' {$c}");
				if($barcode['barcodeid']){
					$this->kclass->DB->query("
						UPDATE barcode SET
							quantity=quantity+'".$this->kclass->input['itemQuantity'][$n]."',
							qualified=qualified+'".$this->kclass->input['itemQualified'][$n]."',
							disqualified=disqualified+'".$this->kclass->input['itemDisqualified'][$n]."',
							modifier='".$this->kclass->user['userid']."',
							modified='".TIMENOW."'
						WHERE barcodeid='".$barcode['barcodeid']."'
					");
					$barcodeid=$barcode['barcodeid'];
				}else{
					if($inbound['supplierid']>0){
						$supplier=$this->kclass->DB->queryFirst("
							SELECT s.supplierid,s.supplierno,s.regionid, 
								r.countryid,r.abbr
							FROM supplier AS s
							LEFT JOIN region AS r ON (r.regionid=s.regionid)
							WHERE supplierid='".$inbound['supplierid']."'
						");
						if($supplier['countryid']==0){
							$supplier['country']=$supplier['abbr'];
						}else{
							$region=$this->kclass->DB->queryFirst("
								SELECT r.regionid,re.abbr
								FROM region AS r
								LEFT JOIN region AS re ON (re.regionid=r.countryid)
								WHERE r.regionid='".$supplier['regionid']."'
							");
							$supplier['country']=$region['abbr'];
						}
					}else{
						$supplier['supplierno']='0000';
						$supplier['supplierid']='2';
						$supplier['country']='CN';
					}
					$sd=explode('-',date('y-n-j',$dateline));
					$time36='';
					foreach($sd as $val){
						$time36.=strtoupper(base_convert($val,10,36));// 将日期转化为36位进制
					}
					$barcode=$supplier['country'].'-'.$material['materialno'].$this->kclass->iif($item['materialitemid']>0,'-'.$material['no'],'').'-'.$supplier['supplierno'].'-'.$time36.'-'.$version['title'];
					$this->kclass->DB->query("
						INSERT INTO barcode (materialid,supplierid,versionid,batchid,barcode,country,materialno,supplierno,arrivalTime,version,quantity,qualified,disqualified,created,creator)
						VALUES ('".$item['materialid']."','".$supplier['supplierid']."','".$versionid."','".$batchid."','".$barcode."','".$supplier['country']."','".$material['materialno']."','".$supplier['supplierno']."','".$dateline."','".$version['title']."','".$this->kclass->input['itemQuantity'][$n]."','".$this->kclass->input['itemQualified'][$n]."','".$this->kclass->input['itemDisqualified'][$n]."','".TIMENOW."','".$this->kclass->user['userid']."')
					");
					$barcodeid=$this->kclass->DB->insertID();
				}
				$this->kclass->DB->query("
					INSERT INTO inbounditem (inboundid,orderid,sampleid,batchid,barcodeid,materialid,materialitemid,preitemid,versionid,quantity,stockid,unitid,dateTime,created,creator)
					VALUES ('".$this->kclass->input['inboundid']."','{$inbound['orderid']}','{$inbound['sampleid']}','".$batchid."','".$barcodeid."','".$item['materialid']."','".$item['materialitemid']."','".$this->kclass->input['itemId'][$n]."','".$versionid."','".$this->kclass->input['itemQuantity'][$n]."','0','".$material['unitid']."','".$dateline."','".TIMENOW."','".$this->kclass->user['userid']."')
				");
				$initemid=$this->kclass->DB->insertID();
				// 总数的变化
				$this->kclass->DB->query("UPDATE material SET quantity=quantity+'".$this->kclass->input['itemQuantity'][$n]."',qualified=qualified+'".$this->kclass->input['itemQualified'][$n]."',disqualified=disqualified+'".$this->kclass->input['itemDisqualified'][$n]."' WHERE materialid='".$item['materialid']."'");
				if($item['materialitemid']>0){
					$this->kclass->DB->query("UPDATE materialitem SET quantity=quantity+'".$this->kclass->input['itemQuantity'][$n]."',qualified=qualified+'".$this->kclass->input['itemQualified'][$n]."',disqualified=disqualified+'".$this->kclass->input['itemDisqualified'][$n]."' WHERE itemid='".$item['materialitemid']."'");
				}
				//若非外发加工原材料则修改单价
				$hasmaterial = $this->kclass->DB->queryFirst("SELECT COUNT(itemid) FROM entrustitem WHERE materialid='".$item['materialid']."'");
				if($hasmaterial['COUNT(itemid)']==0){
					$this->__updatePrice(array('materialid'=>$item['materialid'],'materialitemid'=>$item['materialitemid'],'versionid'=>$versionid,'price'=>$version['price'],'price1'=>$version['price1'],'quantity'=>$version['qualified'],'inQuantity'=>$this->kclass->input['itemQuantity'][$n],'typeid'=>$inbound['typeid'],'porderid'=>$inbound['porderid'],'inboundid'=>$inbound['inboundid'],'initemid'=>$initemid));
				}
				// 物料收发卡(收)
				$this->kclass->DB->query("
					INSERT inoutrecord  (`module`,`mid`,`materialid`,`materialitemid`,`versionid`,`itemid`,`addition`,`qualified`,`balance`,`disqualified`,`disbalance`,`dateline`,`created`,`creator`)
					VALUE  ('inbound','".$this->kclass->input['inboundid']."','".$item['materialid']."','".$item['materialitemid']."','".$versionid."','".$initemid."','+','".$this->kclass->input['itemQualified'][$n]."','".($version['qualified']+$this->kclass->input['itemQualified'][$n])."','".$this->kclass->input['itemDisqualified'][$n]."','".($version['disqualified']+$this->kclass->input['itemDisqualified'][$n])."','".TIMENOW."','".TIMENOW."','".$this->kclass->user['userid']."')
				");
				$hasItem = $this->kclass->DB->queryFirst("SELECT COUNT(itemid) FROM item WHERE module='porder' and mid='".$inbound['porderid']."' and materialid='".$item['materialid']."'");
				// 新增到货记录
				if($inbound['porderid']>0){	
					//若该物料存在于item中,则查询出itemid,否则默认为0
					if($hasItem['COUNT(itemid)']>0){
						$orderItem=$this->kclass->DB->queryFirst("
							SELECT itemid
							FROM item
							WHERE killed=0 AND module='porder' AND mid='".$inbound['porderid']."' AND materialid='".$item['materialid']."'
							LIMIT 0,1
						");
						$itemid = $orderItem['itemid'];
					}else{
						$itemid = "0";
					}
					$this->kclass->DB->query("
						INSERT INTO ordertracking (`module`,`mid`,`itemid`,`initemid`,`materialid`,`inboundid`,`arrivalno`,`quantity`,`unitid`,`dateline`,`remark`,`created`,`creator`)
						VALUES ('porder','".$inbound['porderid']."','".$itemid."','".$initemid."','".$item['materialid']."','".$this->kclass->input['inboundid']."','".$inbound['arrivalno']."','".$this->kclass->input['itemQuantity'][$n]."','".$material['unitid']."','".$dateline."','".$this->kclass->input['itemRemark'][$n]."','".TIMENOW."','".$this->kclass->user['userid']."')
					");
				}
			}
		}

		// 判断入库单入库的状态 并跳转
		$inStatus='';
		$prein=$this->kclass->DB->queryFirst("
			SELECT SUM(quantity) AS count
			FROM preinbounditem
			WHERE killed=0 AND module='inbound' AND mid='".$this->kclass->input['inboundid']."'
			LIMIT 0,1
		");
		$in=$this->kclass->DB->queryFirst("
			SELECT SUM(quantity) AS count
			FROM inbounditem
			WHERE killed=0 AND inboundid='".$this->kclass->input['inboundid']."'
			LIMIT 0,1
		");
		if($prein['count']>$in['count'] AND $in['count']>0){
			$inStatus='部分入库';
			$inShow=1;
			// 修改入库单状态 部分入库
			$this->kclass->DB->query("UPDATE inbound SET ifComplete=1 WHERE inboundid='".$this->kclass->input['inboundid']."'");
		}elseif($prein['count']==$in['count']){
			$inStatus='入库完成';
			$inShow=2;
			$items=$this->kclass->DB->query("
				SELECT quantity,versionid
				FROM inbounditem
				WHERE killed=0 AND inboundid='".$this->kclass->input['inboundid']."'
			");
			if($this->kclass->DB->numRows()){
				while($item=$this->kclass->DB->fetchArray($items)){
					$version=$this->kclass->DB->queryFirst("SELECT planInbound FROM materialversion WHERE versionid='".$item['versionid']."'");
					if($version['planInbound']-$item['quantity']<0){
						$planInbound=0;
					}else{
						$planInbound=$version['planInbound']-$item['quantity'];
					}
					$this->kclass->DB->query("UPDATE materialversion SET planInbound='".$planInbound."' WHERE versionid='".$item['versionid']."'");
				}
			}
			// 修改入库单状态 入库完成
			$this->kclass->DB->query("UPDATE inbound SET ifComplete=2 WHERE inboundid='".$this->kclass->input['inboundid']."'");
			// 修改订单到货情况
			if($inbound['porderid']>0){
				$orderQuantity=$this->kclass->DB->queryFirst("SELECT SUM(quantity) AS count FROM item WHERE killed=0 AND module='porder' AND mid='".$inbound['porderid']."'");
				$trackingQuantity=$this->kclass->DB->queryFirst("SELECT SUM(quantity) AS count FROM ordertracking WHERE killed=0 AND module='porder' AND mid='".$inbound['porderid']."'");
				if(!$trackingQuantity['count']){ // 未到货
					$this->kclass->DB->query("UPDATE porder SET arrivalStatus=0 WHERE porderid='".$inbound['porderid']."'");
				}elseif($trackingQuantity['count']<$orderQuantity['count'] AND $trackingQuantity['count']>0){ // 部分到货
					$this->kclass->DB->query("UPDATE porder SET arrivalStatus=1 WHERE porderid='".$inbound['porderid']."'");
				}elseif($trackingQuantity['count']>=$orderQuantity['count']){// 已到货
					$this->kclass->DB->query("UPDATE porder SET arrivalStatus=2 WHERE porderid='".$inbound['porderid']."'");
				}
			}elseif($inbound['orderid']>0){  // 如果是销售订单入库，更改状态
				$this->kclass->DB->query("UPDATE `order` SET ifInbound=2 WHERE orderid='".$inbound['orderid']."'");
			}elseif($inbound['sampleid']>0){ // 如果是样品入库，更改状态
				$this->kclass->DB->query("UPDATE `sample` SET ifInbound=2 WHERE sampleid='".$inbound['sampleid']."'");
			}
		}elseif(!$in['count']){
			$inStatus='等待入库';
			$inShow=0;
		}

		$this->kclass->messager(array(
			'title' => '添加入库物资批次',
			'text' => '入库物资批次 已添加成功!返回添加查看入库单',
			'url' => '/s.php?module=inbound&action=view&inboundid='.$this->kclass->input['inboundid'],
			'sec' => 2
		));
	}
	// 对已出库的物资进行取消入库
	function cancelIn(){
		if($this->kclass->input['type']=='item'){
			$item=$this->kclass->DB->queryFirst("
				SELECT ii.*,
					i.inboundno,i.ifComplete
				FROM inbounditem AS ii
				LEFT JOIN inbound AS i ON (ii.inboundid=i.inboundid)
				WHERE ii.itemid='".$this->kclass->input['itemid']."'
				LIMIT 0,1
			");
			if(!$item['itemid']){
				$e.='<li>数据错误</li>';
			}
			if(isset($e)){
				$this->kclass->messager(array(
					'title' => '对该入库单的该明细进行取消入库',
					'text' => '取消入库 <ul>'.$e.'</ul>',
					'url' => 'javascript:history.back()',
					'sec' => 3
				));
			}
			$body = <<<EOF
<form action="/s.php?module=inbound&action=docancelIn" name="inbound" method="post" onsubmit="dc.checkSubmit(this)">
<input type="hidden" name="module" value="inbound">
<input type="hidden" name="action" value="docancelIn">
<input type="hidden" name="inboundid" value="{$item['inboundid']}">
<input type="hidden" name="itemid" value="{$item['itemid']}">
<input type="hidden" name="type" value="{$this->kclass->input['type']}">
<table><thead>
<thead>
<tr>
<th>对入库单：{$item['inboundno']} 取消入库</th>
</tr>
</thead>
<tbody>
<tr class="odd">
<td class="middle">你确定要对入库单: <a href="/s.php?module=inbound&action=view&inboundid={$item['inboundid']}" class="big bold" target="_blank">{$item['inboundno']}</a> 的明细<b>{$item['materialno']}</b>取消入库吗?</td>
</tr>
<tr class="even">
<td align="center">
	<label for="c1" class="red"><input type="radio" id="c1" name="confirm" value="1">是</label>
	<label for="c0" class="green"><input type="radio" id="c0" name="confirm" value="0" checked>否</label>
</td>
</tr>
<tr class="odd" nohover>
<td align="center">
	<input type="submit" value="   保存   ">
	<input type="reset" value="   复位   ">
</td>
</tr>
</tbody>
</table>
</form>
EOF;
		}elseif($this->kclass->input['type']=='all'){
			$inbound=$this->kclass->DB->queryFirst("SELECT inboundid,inboundno FROM inbound WHERE inboundid='".$this->kclass->input['inboundid']."'");
			if($inbound['inboundid']==''){
				$e.='<li>数据错误</li>';
			}
			if(isset($e)){
				$this->kclass->messager(array(
					'title' => '对该入库单的所有明细进行取消人库',
					'text' => '取消出库 <ul>'.$e.'</ul>',
					'url' => 'javascript:history.back()',
					'sec' => 3
				));
			}
			$body = <<<EOF
<form action="/s.php?module=inbound&action=docancelIn" name="inbound" method="post" onsubmit="dc.checkSubmit(this)">
<input type="hidden" name="module" value="inbound">
<input type="hidden" name="action" value="docancelIn">
<input type="hidden" name="inboundid" value="{$inbound['inboundid']}">
<input type="hidden" name="type" value="{$this->kclass->input['type']}">
<table><thead>
<thead>
<tr>
<th>删除出库单：{$inbound['inboundno']}</th>
</tr>
</thead>
<tbody>
<tr class="odd">
<td class="middle">你确定要对入库单: <a href="/s.php?module=inbound&action=view&inboundid={$inbound['inboundid']}" class="big bold" target="_blank">{$inbound['inboundno']}</a> 取消入库吗?</td>
</tr>
<tr class="even">
<td align="center">
	<label for="c1" class="red"><input type="radio" id="c1" name="confirm" value="1">是</label>
	<label for="c0" class="green"><input type="radio" id="c0" name="confirm" value="0" checked>否</label>
</td>
</tr>
<tr class="odd" nohover>
<td align="center">
	<input type="submit" value="   保存   ">
	<input type="reset" value="   复位   ">
</td>
</tr>
</tbody>
</table>
</form>
EOF;
		}
		$this->kclass->page['title'].='取消出库';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - <a href="/s.php?module=inbound&action=list">入库单列表</a> - 审核出库单', 'right' => '<a href="/s.php?module=inbound&action=view&inboundid='.$this->kclass->input['inboundid'].'">返回查看入库申请单</a>','body' => $body));
	}
	//
	function docancelIn(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			if($this->kclass->input['type']=='item'){
				$item=$this->kclass->DB->queryFirst("
					SELECT itemid
					FROM inbounditem
					WHERE itemid='".$this->kclass->input['itemid']."'
					LIMIT 0,1
				");
				if(!$item['itemid']){
					$e.='<li>入库单 明细 数据错误</li>';
				}
			}elseif($this->kclass->input['type']=='all'){
				$inbound=$this->kclass->DB->queryFirst("SELECT inboundid,inboundno,porderid,orderid,sampleid FROM inbound WHERE inboundid='".$this->kclass->input['inboundid']."'");
				if(!$inbound['inboundid']){
					$e.='<li>入库单 数据错误</li>';
				}
			}
		}
		if($this->kclass->input['confirm'] == 0){
			$this->kclass->boinkIt('/s.php?module=inbound&action=view&inboundid='.$this->kclass->input['inboundid']);
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '取消入库',
				'text' => '取消入库<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		if($this->kclass->input['type']=='item'){
			$item=$this->kclass->DB->queryFirst("
				SELECT ii.*,
					i.inboundno,i.ifComplete,i.porderid,i.orderid,i.sampleid,
					m.materialno,m.ifPerBarcode,
					pii.versionid,pii.qualified,pii.disqualified
				FROM inbounditem AS ii
				LEFT JOIN inbound AS i ON (ii.inboundid=i.inboundid)
				LEFT JOIN material AS m ON (m.materialid=ii.materialid)
				LEFT JOIN preinbounditem AS pii ON (pii.itemid=ii.preitemid)
				WHERE ii.killed=0 AND ii.itemid='".$this->kclass->input['itemid']."'
				LIMIT 0,1
			");
			if($item['qualified']>0){
				$qualified=$item['quantity'];
				$disqualified=0;
			}elseif($item['disqualified']>0){
				$qualified=0;
				$disqualified=$item['quantity'];
			}
			// 物资总数 return
			$this->kclass->DB->query("UPDATE material SET quantity=quantity-'".$item['quantity']."',qualified=qualified-'".$qualified."',disqualified=disqualified-'".$disqualified."' WHERE materialid='".$item['materialid']."'");
			// 物资明细return
			if($item['materialitemid']>0){
				$this->kclass->DB->query("UPDATE materialitem SET quantity=quantity-'".$item['quantity']."',qualified=qualified-'".$qualified."',disqualified=disqualified-'".$disqualified."' WHERE itemid='".$item['materialitemid']."'");
			}
			// 条码总数 return
			$this->kclass->DB->query("UPDATE barcode SET quantity=quantity-'".$item['quantity']."',qualified=qualified-'".$qualified."' WHERE barcodeid='".$item['barcodeid']."'");
			// 批次总数 return
			$this->kclass->DB->query("UPDATE batch SET quantity=quantity-'".$item['quantity']."',qualified=qualified-'".$qualified."',disqualified=disqualified-'".$disqualified."' WHERE batchid='".$item['batchid']."'");
			// 版本总数 return
			$this->kclass->DB->query("UPDATE materialversion SET quantity=quantity-'".$item['quantity']."',qualified=qualified-'".$qualified."',disqualified=disqualified-'".$disqualified."' WHERE versionid='".$item['versionid']."'");
			// 删除原明细
			$this->kclass->DB->query("UPDATE inbounditem SET killed='".TIMENOW."',killer='".$this->kclass->user['userid']."' WHERE itemid='".$item['itemid']."'");
			// 删除并return物料收发卡数量
			$record=$this->kclass->DB->queryFirst("SELECT recordid,materialid,versionid,qualified,balance,disqualified,disbalance FROM inoutrecord WHERE module='inbound' AND itemid='".$item['itemid']."'");
			$this->kclass->DB->query("UPDATE inoutrecord SET killed='".TIMENOW."',killer='".$this->kclass->user['userid']."' WHERE recordid='".$record['recordid']."'");
			$this->kclass->DB->query("UPDATE inoutrecord SET balance=balance-'".$qualified."',disbalance=disbalance-'".$disqualified."' WHERE materialid='".$record['materialid']."' AND versionid='".$record['versionid']."' AND recordid>'".$record['recordid']."'");
			// 恢复原价格
			$price=$this->kclass->DB->queryFirst("SELECT recordid FROM pricerecord WHERE killed=0 AND initemid={$this->kclass->input['itemid']} ORDER BY created DESC LIMIT 0,1");// 获得当前明细对应的最新未删除价格
			if($price['recordid']>0){
				$this->kclass->DB->query("UPDATE pricerecord SET killed='".TIMENOW."',killer='".$this->kclass->user['userid']."' WHERE recordid={$price['recordid']}"); // 删除
				$lastPrice=$this->kclass->DB->queryFirst("SELECT price,price1 FROM pricerecord WHERE killed=0 AND materialid={$item['materialid']} AND versionid={$item['versionid']} ORDER BY created DESC LIMIT 0,1");
				$this->kclass->DB->query("UPDATE materialversion SET price={$lastPrice['price']},price1={$lastPrice['price1']} WHERE versionid={$item['versionid']}");
			}
			// 收料方式的时候，删除物料跟踪的物资
			if($item['porderid']>0){
				$this->kclass->DB->query("UPDATE ordertracking SET killed='".TIMENOW."',killer='".$this->kclass->user['userid']."' WHERE module='porder' AND mid='".$item['porderid']."' AND initemid='".$item['itemid']."'");
			}elseif($item['orderid']>0){  // 如果是销售订单入库，更改状态
				$this->kclass->DB->query("UPDATE `order` SET ifInbound=1 WHERE orderid='".$item['orderid']."'");
			}elseif($item['sampleid']>0){ // 如果是样品入库，更改状态
				$this->kclass->DB->query("UPDATE `sample` SET ifInbound=1 WHERE sampleid='".$item['sampleid']."'");
			}
			// 出库单状态变化
			$inboundItem=$this->kclass->DB->queryFirst("SELECT COUNT(itemid) AS count FROM inbounditem WHERE killed=0 AND inboundid='".$item['inboundid']."'");
			if($item['ifComplete']!=1){
				$this->kclass->DB->query("UPDATE inbound SET ifComplete=1 WHERE inboundid='".$item['inboundid']."'");
			}elseif($inboundItem['count']==0){
				$this->kclass->DB->query("UPDATE inbound SET ifComplete=0 WHERE inboundid='".$item['inboundid']."'");
			}
			$this->kclass->DB->query("DELETE FROM `childBarcode` WHERE initemid={$item['itemid']}"); // 删除对应的流水号条码
			$this->kclass->messager(array(
				'title' => '取消入库',
				'text' => '入库单 【<b>'.$item['inboundno'].'</b>】 明细 <b>'.$item['materialno'].'</b> 被取消入库!返回查看入库申请单',
				'url' => '/s.php?module=inbound&action=view&inboundid='.$item['inboundid'],
				'sec' => 2
			));
		}elseif($this->kclass->input['type']=='all'){
			$items=$this->kclass->DB->query("
				SELECT ii.*,
					pii.materialid,pii.versionid,pii.qualified,pii.disqualified
				FROM inbounditem AS ii
				LEFT JOIN preinbounditem AS pii ON (pii.itemid=ii.preitemid)
				WHERE ii.killed=0 AND ii.inboundid='".$inbound['inboundid']."'
			");
			if($this->kclass->DB->numRows()){
				while($item=$this->kclass->DB->fetchArray($items)){
					if($item['qualified']>0){
						$qualified=$item['quantity'];
						$disqualified=0;
					}elseif($item['disqualified']>0){
						$qualified=0;
						$disqualified=$item['quantity'];
					}

					// 物资总数 return
					$this->kclass->DB->query("UPDATE material SET quantity=quantity-'".$item['quantity']."',qualified=qualified-'".$qualified."',disqualified=disqualified-'".$disqualified."' WHERE materialid='".$item['materialid']."'");
					// 物资明细return
					if($item['materialitemid']>0){
						$this->kclass->DB->query("UPDATE materialitem SET quantity=quantity-'".$item['quantity']."',qualified=qualified-'".$qualified."',disqualified=disqualified-'".$disqualified."' WHERE itemid='".$item['materialitemid']."'");
					}
					// 条码总数 return
					$this->kclass->DB->query("UPDATE barcode SET quantity=quantity-'".$item['quantity']."',qualified=qualified-'".$qualified."',disqualified=disqualified-'".$disqualified."' WHERE barcodeid='".$item['barcodeid']."'");
					// 批次总数 return
					$this->kclass->DB->query("UPDATE batch SET quantity=quantity-'".$item['quantity']."',qualified=qualified-'".$qualified."',disqualified=disqualified-'".$disqualified."' WHERE batchid='".$item['batchid']."'");
					// 版本总数 return
					$this->kclass->DB->query("UPDATE materialversion SET quantity=quantity-'".$item['quantity']."',qualified=qualified-'".$qualified."',disqualified=disqualified-'".$disqualified."' WHERE versionid='".$item['versionid']."'");
					// 删除原明细
					$this->kclass->DB->query("UPDATE inbounditem SET killed='".TIMENOW."',killer='".$this->kclass->user['userid']."' WHERE itemid='".$item['itemid']."'");
					// 取消入库后，清查批次数量
					$afterBatch=$this->kclass->DB->queryFirst("SELECT quantity FROM batch WHERE batchid='".$item['batchid']."'");
					if($afterBatch['quantity']==0){ // 批次数量为0的时候，删除
						$this->kclass->DB->query("UPDATE batch SET killed='".TIMENOW."',killer='".$this->kclass->user['userid']."' WHERE batchid='".$item['batchid']."'");
					}
					$afterBarcode=$this->kclass->DB->queryFirst("SELECT quantity FROM barcode WHERE barcodeid='".$item['barcodeid']."'");
					if($afterBarcode['quantity']==0){
						$this->kclass->DB->query("UPDATE barcode SET scraped='".TIMENOW."',scraper='".$this->kclass->user['userid']."' WHERE barcodeid='".$item['barcodeid']."'");
					}
					// 删除并return物料收发卡数量
					$record=$this->kclass->DB->queryFirst("SELECT recordid,materialid,versionid,qualified,balance,disqualified,disbalance FROM inoutrecord WHERE killed=0 AND module='inbound' AND itemid='".$item['itemid']."'");
					$this->kclass->DB->query("UPDATE inoutrecord SET killed='".TIMENOW."',killer='".$this->kclass->user['userid']."' WHERE recordid='".$record['recordid']."'");
					$this->kclass->DB->query("UPDATE inoutrecord SET balance=balance-'".$qualified."',disbalance=disbalance-'".$disqualified."' WHERE materialid='".$record['materialid']."' AND versionid='".$record['versionid']."' AND recordid>'".$record['recordid']."'");
					// 恢复原价格
					$price=$this->kclass->DB->queryFirst("SELECT recordid FROM pricerecord WHERE killed=0 AND initemid={$item['itemid']} ORDER BY created DESC LIMIT 0,1");// 获得当前明细对应的最新未删除价格
					if($price['recordid']>0){
						$this->kclass->DB->query("UPDATE pricerecord SET killed='".TIMENOW."',killer='".$this->kclass->user['userid']."' WHERE recordid={$price['recordid']}"); // 删除
						$lastPrice=$this->kclass->DB->queryFirst("SELECT price,price1 FROM pricerecord WHERE killed=0 AND materialid={$item['materialid']} AND versionid={$item['versionid']} ORDER BY created DESC LIMIT 0,1");
						$this->kclass->DB->query("UPDATE materialversion SET price={$lastPrice['price']},price1={$lastPrice['price1']} WHERE versionid={$item['versionid']}");
					}
					// 收料方式的时候，删除物料跟踪的物资
					if($inbound['porderid']>0){
						$this->kclass->DB->query("UPDATE ordertracking SET killed='".TIMENOW."',killer='".$this->kclass->user['userid']."' WHERE module='porder' AND mid='".$inbound['porderid']."' AND inboundid='".$inbound['inboundid']."'");
					}elseif($inbound['orderid']>0){  // 如果是销售订单入库，更改状态
						$this->kclass->DB->query("UPDATE `order` SET ifInbound=1 WHERE orderid='".$inbound['orderid']."'");
					}elseif($inbound['sampleid']>0){ // 如果是样品入库，更改状态
						$this->kclass->DB->query("UPDATE `sample` SET ifInbound=1 WHERE sampleid='".$inbound['sampleid']."'");
					}
					$this->kclass->DB->query("DELETE FROM `childBarcode` WHERE initemid={$item['itemid']}"); // 删除对应的流水号条码
				}
			}
			$this->kclass->DB->query("UPDATE inbound SET ifComplete=0 WHERE killed=0 AND inboundid='".$inbound['inboundid']."'");

			$this->kclass->messager(array(
				'title' => '取消入库',
				'text' => '入库单 【<b>'.$inbound['inboundno'].'</b>】 已全部被取消入库!返回查看入库申请单',
				'url' => '/s.php?module=inbound&action=view&inboundid='.$this->kclass->input['inboundid'],
				'sec' => 2
			));
		}
	}
	//
	function kill(){
		if($this->kclass->input['inboundid']<=0){
			$this->kclass->boinkIt('/s.php?module=inbound');
		}
		if(!$this->kclass->input['rt'] AND $this->kclass->input['confirm'] == 0){
			$this->kclass->boinkIt('/s.php?module=inbound');
		}
		if($this->kclass->input['inboundid'] < 0){
			$e = '<li>请从正确的页面进行操作，如果此问题持续存在，请联系网站管理员。</li>';
		}
		$item=$this->kclass->DB->query("SELECT COUNT(*) AS count FROM inbounditem WHERE killed=0 AND inboundid='".$this->kclass->input['inboundid']."'");
		if($item['count']>0){
			$e.='<li>该入库单正在入库中，删除前请联系仓库取消入库</li>';
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '删除入库单',
				'text' => '您在删除入库单的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		if($this->kclass->input['rt']=='view'){
			$rt = '&action=view&inboundid='.$this->kclass->input['inboundid'];
		}elseif($this->kclass->input['rt']=='list'){
			$rt = '#'.$this->kclass->input['inboundid'];
		}
		$inbound = $this->kclass->DB->queryFirst("
			SELECT inboundno,orderid,sampleid
			FROM inbound
			WHERE inboundid='".$this->kclass->input['inboundid']."'
			LIMIT 0,1
		");
		if($inbound){
			$this->kclass->DB->query("
				UPDATE `inbound`
				SET killed=".TIMENOW.",killer='".$this->kclass->user['userid']."'
				WHERE inboundid='".$this->kclass->input['inboundid']."'
			");
			if($inbound['orderid']>0){  // 如果是订单入库，更改状态
				$this->kclass->DB->query("UPDATE `order` SET ifInbound=0 WHERE orderid='".$inbound['orderid']."'");
			}elseif($inbound['sampleid']>0){ // 如果是样品入库，更改状态
				$this->kclass->DB->query("UPDATE `sample` SET ifInbound=0 WHERE sampleid='".$inbound['sampleid']."'");
			}
			$this->kclass->messager(array(
				'title' => '删除出库单成功',
				'text' => '出库单 <b>'.$inbound['inboundno'].'</b> 已成功被标记为删除!',
				'url' => '/s.php?module=inbound'.$rt,
				'sec' => 3
			));
		}else{
			$this->kclass->messager(array(
				'title' => '删除出库单失败',
				'text' => '您要删除的出库单，不存在！',
				'url' => '/s.php?module=inbound'.$rt,
				'sec' => 3
			));
		}
	}

	//
	function remove(){
		if($this->kclass->input['inboundid']<=0){
			$this->kclass->boinkIt('/s.php?module=inbound');
		}
		$inbound = $this->kclass->DB->queryFirst("
			SELECT inboundno
			FROM inbound
			WHERE inboundid='".$this->kclass->input['inboundid']."'
		");
$body = <<<EOF
<form action="/s.php?module=inbound&action=kill" name="inbound" method="post">
<input type="hidden" name="module" value="inbound">
<input type="hidden" name="action" value="kill">
<input type="hidden" name="inboundid" value="{$this->kclass->input['inboundid']}">
<table><thead>
<thead>
<tr>
<td>删除出库单：{$inbound['inboundno']}</td>
</tr>
</thead>
<tbody>
<tr class="odd">
<td class="middle">你确定要删除出库单: <a href="/s.php?module=inbound&action=view&inboundid={$this->kclass->input['inboundid']}" class="big bold" target="_blank">{$inbound['inboundno']}</a> 吗?</td>
</tr>
<tr class="even">
<td align="center">
	<label for="c1" class="red"><input type="radio" id="c1" name="confirm" value="1">是</label>
	<label for="c0" class="green"><input type="radio" id="c0" name="confirm" value="0" checked>否</label>
</td>
</tr>
<tr class="odd">
<td align="center" nohover>
	<input type="submit" value="   保存   " accesskey="s">
	<input type="reset" value="   复位   ">
</td>
</tr>
</tbody>
</table>
</form>
EOF;
		$this->kclass->page['title'] .= ' - 删除 - '.$inbound['inboundno'];
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 删除出库单 - '.$inbound['title'], 'right' => '<a href="/s.php?module=inbound">返回列表</a>', 'body'=>$body));
	}

	//
	function revival(){
		if($this->kclass->input['inboundid']<=0){
			$this->kclass->boinkIt('/s.php?module=inbound');
		}
		if(!$this->kclass->input['rt'] AND $this->kclass->input['confirm'] == 0){
			$this->kclass->boinkIt('/s.php?module=inbound');
		}
		if($this->kclass->input['inboundid'] < 0){
			$e = '<li>请从正确的页面进行操作，如果此问题持续存在，请联系网站管理员。</li>';
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '恢复出库单',
				'text' => '您在恢复出库单的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$inbound = $this->kclass->DB->queryFirst("
			SELECT inboundno
			FROM inbound
			WHERE inboundid='".$this->kclass->input['inboundid']."'
		");
		if($this->kclass->input['rt']=='view'){
			$rt = '&action=view&inboundid='.$this->kclass->input['inboundid'];
		}elseif($this->kclass->input['rt']=='list'){
			$rt = '#'.$this->kclass->input['inboundid'];
		}
		if($inbound){
			$this->kclass->DB->query("
				UPDATE `inbound`
				SET killed=0,killer=0
				WHERE inboundid='".$this->kclass->input['inboundid']."'
			");
			$this->kclass->messager(array(
				'title' => '恢复出库单成功',
				'text' => '出库单 <b>'.$inbound['inboundno'].'</b> 已成功被恢复！',
				'url' => '/s.php?module=inbound'.$rt,
				'sec' => 3
			));
		}else{
			$this->kclass->messager(array(
				'title' => '恢复出库单失败',
				'text' => '您要恢复的出库单不存在！',
				'url' => '/s.php?module=inbound'.$rt,
				'sec' => 3
			));
		}
	}

	//
	function restore(){
		if($this->kclass->input['inboundid']<=0){
			$this->kclass->boinkIt('/s.php?module=inbound');
		}
		$inbound = $this->kclass->DB->queryFirst("
			SELECT inboundno
			FROM inbound
			WHERE inboundid='".$this->kclass->input['inboundid']."'
		");
$body = <<<EOF
<form action="/s.php?module=inbound&action=revival" name="inbound" method="post">
<input type="hidden" name="module" value="inbound">
<input type="hidden" name="action" value="revival">
<input type="hidden" name="inboundid" value="{$this->kclass->input['inboundid']}">
<table><thead>
<thead>
<tr>
<td>恢复供应商：{$inbound['inboundno']}</td>
</tr>
</thead>
<tbody>
<tr class="odd">
<td class="middle">你确定要恢复出库单: <a href="/s.php?module=inbound&action=view&inboundid={$this->kclass->input['inboundid']}" class="big bold" target="_blank">{$inbound['inboundno']}</a> 吗?</td>
</tr>
<tr class="even">
<td align="center">
	<label for="c1" class="red"><input type="radio" id="c1" name="confirm" value="1">是</label>
	<label for="c0" class="green"><input type="radio" id="c0" name="confirm" value="0" checked>否</label>
</td>
</tr>
<tr class="odd">
<td align="center" nohover>
	<input type="submit" value="   保存   " accesskey="s">
	<input type="reset" value="   复位   ">
</td>
</tr>
<tbody>
</table>
</form>
EOF;
		$this->kclass->page['title'] .= ' - 恢复 - '.$inbound['inboundno'];
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 恢复出库单 - '.$inbound['inboundno'], 'right' => '<a href="/s.php?module=inbound">返回列表</a>', 'body'=>$body));
	}

	/****************************** private function *****************************************/
	// 更新价格
	function __updatePrice($b){	
	//echo "Materialid:".$b['materialid']."<br />versionid:".$b['versionid']."<br /> price:".$b	
		$newPrice=0;
		if($b['typeid']!=1){
			return false;
		}

		$orderItem=$this->kclass->DB->queryFirst("
			SELECT i.price,po.purchaseType 
			FROM item AS i
			LEFT JOIN porder AS po ON (po.porderid=i.mid)
			WHERE module='porder' AND mid={$b['porderid']} AND versionid={$b['versionid']}
		");
		if($orderItem['purchaseType']=='normal'){
			$newPrice=$orderItem['price'];$newPrice1=0;
			$price=sprintf('%.4f',($b['price']*$b['quantity']+$newPrice*$b['inQuantity'])/($b['quantity']+$b['inQuantity']));
			$price1=0;
		}elseif($orderItem['purchaseType']=='entrust'){
			$newPrice=0;$newPrice1=$orderItem['price'];
			$children=$this->kclass->bomChildList(array('parentid'=>$b['materialid'],'loopNum'=>1,'versionid'=>$b['versionid'],'materialitemid'=>$b['materialitemid']));
			$price0=0;
			if($children['bom']){
				foreach($children['bom'] AS $child){
					$price0+=$child['price'];
				}
			}
			$price1=sprintf('%.4f',($b['price1']*$b['quantity']+$newPrice1*$b['inQuantity'])/($b['quantity']+$b['inQuantity']));
			$price=$price0+$price1;
		}
		

		
		if($price!=$b['price']){// insert price record
			$this->kclass->DB->query("
				INSERT INTO pricerecord (materialid,versionid,inboundid,initemid,price,price1,quantity,created,creator)
				VALUES ('{$b['materialid']}','{$b['versionid']}',{$b['inboundid']},{$b['initemid']},'{$price}','{$price1}','{$b['inQuantity']}','".TIMENOW."','{$this->kclass->user['userid']}')
			");
			// update the current price 
			$this->kclass->DB->query("UPDATE materialversion SET price={$price},price1={$price1} WHERE versionid={$b['versionid']}");
		}
	}
}
?>