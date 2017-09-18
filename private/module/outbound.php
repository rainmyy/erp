	<?php
//
class outbound{
	var $kclass;
	var $outboundType=array(array('id'=>1,'title'=>'领料(BOM)'),array('id'=>2,'title'=>'领料(非BOM)'),array('id'=>3,'title'=>'订单出库'),array('id'=>4,'title'=>'外发加工'),array('id'=>5,'title'=>'退货'),array('id'=>6,'title'=>'补料'),array('id'=>7,'title'=>'补料(BOM)'),array('id'=>8,'title'=>'样品出库'),array('id'=>9,'title'=>'出库（旧单）'));
	var $day=26;// 转换为下月1号的日期
	//
	function autorun(){
		$this->baseurl='<a href="/s.php?">首页</a>';
		switch($this->kclass->input['action']){
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
			case 'removeMaterial':
				return $this->removeMaterial();
				break;
			case 'out':
				return $this->out();
				break;
			case 'doout':
				return $this->doout();
				break;
			case 'perout':
				return $this->perout();
				break;
			case 'doperout':
				return $this->doperout();
				break;
			case 'outAll':
				return $this->outAll();
				break;
			case 'dooutAll':
				return $this->dooutAll();
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
			case 'cancelOut':
				return $this->cancelOut();
				break;
			case 'docancelOut':
				return $this->docancelOut();
				break;
			default:
				return $this->mmlist();

		}
	}
	// main page,shows all the outbound info list
	function mmlist(){
		if($this->kclass->input['show']=='all'){
			$condition='1=1';
		}elseif($this->kclass->input['show']=='verify'){
			$condition='`outbound`.ifVerify=1';
		}elseif($this->kclass->input['show']=='unverify'){
			$condition='`outbound`.ifVerify=-1';
		}else{
			$condition='`outbound`.killed=0';
		}
		// 列表筛选
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
			$condition.=' AND `outbound`.departmentid IN ('.$department.')';
		}else{
			$condition.=' AND (`outbound`.departmentid='.$this->kclass->user['departmentid'].' OR `outbound`.ifVerify=1)';
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
		$orderby=$this->kclass->orderby(array('module'=>'outbound','direction'=>'DESC','orderby'=>'created', 'default'=>'created', 'serial'=>array(array('title'=>'编号', 'field'=>'outboundno','word'=>'no'), array('title'=>'修改时间', 'field'=>'modified'), array('title'=>'建立时间', 'field'=>'created')),'appendUrl'=>$query));
		if(is_array($query) AND count($query)>0)$queryPart='&'.http_build_query($query);
		
		$filterItem=$this->kclass->filterItem(array('module'=>'outbound','outboundType'=>$this->outboundType));
		// 筛选
		$filter=$this->kclass->filter(
			array('module'=>'outbound','action'=>'list','table'=>'outbound','url'=>$queryPart,'serial'=>array(
				array('name'=>'ifVerify','dname'=>'审核状态','status'=>$filterItem['verify']),
				array('name'=>'ifComplete','dname'=>'出库状态','status'=>$filterItem['complete']),
				array('name'=>'typeid','dname'=>'出库类型','status'=>$filterItem['outboundType']),
				array('name'=>'departmentid','dname'=>'部门','status'=>$filterItem['department']),
				array('name'=>'month','dname'=>'月份','status'=>$filterItem['month'])
		)));

		$outboundList=$this->kclass->listOutbound(array('start'=>$start,'prepage'=>$perpage,'condition'=>$condition,'orderby'=>$query['orderby'],'filter'=>$filter['link']));
		$body.=$outboundList['body'];
		$prestart=$start-$perpage;
		$body.='<div class="clear"><span class="gray small left">(共<span class="darkred">'.$outboundList['counter'].'</span>组记录)</span></div>';
		if($prestart>=0)$prev='<a href="'.$filter['uri'].'&orderby='.$query['orderby'].'&direction='.$query['direction'].'&start='.$prestart.'"><-前一页</a>　';
		$aftstart=$start+$perpage;
		if($aftstart<$outboundList['counter'])$after='　<a href="'.$filter['uri'].'&orderby='.$query['orderby'].'&direction='.$query['direction'].'&start='.$aftstart.'">后一页-></a>';
		$body.='<div class="clear center"><span class=" middle bold">'.$prev.$after.'</span></div>';
		if($this->kclass->input['layout']=='grid'){
			$layoutLink='<a href="/s.php?module=outbound&action=list&layout=list'.str_replace('&layout=grid', '', $queryPart).'">列表</a> 格子';
		}else{
			$layoutLink='列表 <a href="/s.php?module=outbound&action=list&layout=grid'.str_replace('&layout=list', '', $queryPart).'">格子</a>';
		}
		if($this->kclass->input['show']=='all'){
			$showLink='<a href="/s.php?module=outbound&action=list'.str_replace('&show=all', '', $queryPart).'">默认</a> 所有<span class="small gray">(包括删除)</span> <a href="/s.php?module=outbound&action=list&show=verify'.str_replace('&show=all', '', $queryPart).'">已通过</a>  <a href="/s.php?module=outbound&action=list&show=inverify'.str_replace('&show=all', '', $queryPart).'">待审核</a>  <a href="/s.php?module=outbound&action=list&show=unverify'.str_replace('&show=all', '', $queryPart).'">未通过</a>';
		}elseif($this->kclass->input['show']=='verify'){
			$showLink='<a href="/s.php?module=outbound&action=list'.str_replace('&show=verify', '', $queryPart).'">默认</a> <a href="/s.php?module=outbound&action=list&show=all'.str_replace('&show=verify', '', $queryPart).'">所有<span class="small gray">(包括删除)</span></a> 已通过 <a href="/s.php?module=outbound&action=list&show=inverify'.str_replace('&show=verify', '', $queryPart).'">待审核</a>  <a href="/s.php?module=outbound&action=list&show=unverify'.str_replace('&show=verify', '', $queryPart).'">未通过</a>';
		}elseif($this->kclass->input['show']=='inverify'){
			$showLink='<a href="/s.php?module=outbound&action=list'.str_replace('&show=inverify', '', $queryPart).'">默认</a> <a href="/s.php?module=outbound&action=list&show=all'.str_replace('&show=inverify', '', $queryPart).'">所有<span class="small gray">(包括删除)</span></a> <a href="/s.php?module=outbound&action=list&show=verify'.str_replace('&show=inverify', '', $queryPart).'">已通过</a> 待审核 <a href="/s.php?module=outbound&action=list&show=unverify'.str_replace('&show=inverify', '', $queryPart).'">未通过</a>';
		}elseif($this->kclass->input['show']=='unverify'){
			$showLink='<a href="/s.php?module=outbound&action=list'.str_replace('&show=unverify', '', $queryPart).'">默认</a> <a href="/s.php?module=outbound&action=list&show=all'.str_replace('&show=unverify', '', $queryPart).'">所有<span class="small gray">(包括删除)</span></a> <a href="/s.php?module=outbound&action=list&show=inverify'.str_replace('&show=unverify', '', $queryPart).'">已通过</a> <a href="/s.php?module=outbound&action=list&show=inverify'.str_replace('&show=unverify', '', $queryPart).'">待审核</a> 未通过';
		}else{
			$showLink='默认 <a href="/s.php?module=outbound&action=list&show=all'.str_replace('&show=all', '', $queryPart).'">所有<span class="small gray">(包括删除)</span></a> <a href="/s.php?module=outbound&action=list&show=verify'.str_replace('&show=all', '', $queryPart).'">已通过</a>  <a href="/s.php?module=outbound&action=list&show=inverify'.str_replace('&show=all', '', $queryPart).'">待审核</a>  <a href="/s.php?module=outbound&action=list&show=unverify'.str_replace('&show=all', '', $queryPart).'">未通过</a>';
		}
		$this->kclass->page['title'].='出库单列表';
		$this->kclass->page['onload'].="dc.listhover();dc.tips();dc.tabhover();$('.tablesorter').tablesorter( {sortList: [[0,0]], headers: { 8: { sorter: false} }} );$('.mytable').fixedtableheader();";
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - 出库单列表','right' => '<span class="small">排序：</span>'.$orderby['link'].'<span class="small">显示方式：</span>'.$showLink.'　|　<span class="small">布局：</span>'.$layoutLink.'　|　<a href="/s.php?module=outbound&action=add">新建</a>','body'=>$filter['panel'].$body));
	}
			//
	function find(){
		if($this->kclass->input['departmentid']<=0 AND $this->kclass->input['applicant']=='' AND $this->kclass->input['date']=='' AND $this->kclass->input['month']<=0){
			$department=$this->kclass->chooserDepartment(array('name'=>'departmentid','width'=>250,'hasBlank'=>1,'topname'=>' '));
			$body=<<<EOF
<form action="/s.php?module=inbound&action=find" method="get">
<input type="hidden" name="module" value="outbound">
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
			$this->kclass->page['title'].='查找入库';
			$this->kclass->page['onload'].='$(\'#date\').datepicker()';
			$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - 查找物资','right' => '<a href="/s.php?module=inbound">查看列表</a>','body'=>$body));
		}else{
			if($this->kclass->input['departmentid']>0){
				$condition.=' AND departmentid='.$this->kclass->input['departmentid'];
			}
			if($this->kclass->input['applicant']!=''){
				$condition.=' AND applicant="'.$this->kclass->input['applicant'].'"';
			}
			if($this->kclass->input['date']!=''){
				$sd=explode('-',$this->kclass->input['date']);
				$dateline=mktime(0,0,0,$sd[1],$sd[2],$sd[0]);
				$condition.=' AND oi.created>'.$dateline.' AND oi.created<='.($dateline+86400);
			}
			if($this->kclass->input['month']>0){
				$year=date('Y',TIMENOW);
				$first=strtotime(date('Y-m-d', mktime(0,0,0,$this->kclass->input['month'],1,$year)));
				$last=strtotime(date('Y-m-t', mktime(0,0,0,$this->kclass->input['month'],1,$year)));
				$condition.=' AND oi.created>'.$first.' AND oi.created<='.$last;
			}
			$outbounds=$this->kclass->DB->query("
				SELECT outboundid,applicant,outboundno
				FROM outbound
				WHERE killed=0 $condition1
				ORDER BY created ASC
			");
			if($this->kclass->DB->numRows()){
				$quantity=0;
				$materialId=array();
				$i=0;
				while($outbound=$this->kclass->DB->fetchArray($outbounds)){
					$items=$this->kclass->DB->query("
						SELECT oi.*,
							m.materialno,m.title AS material,m.standard,m.unitid,m.ifPerBarcode,
							bc.barcode,
							bt.batchno
						FROM outbounditem AS oi
						LEFT JOIN material AS m ON (m.materialid=oi.materialid)
						LEFT JOIN barcode AS bc ON (bc.barcodeid=oi.barcodeid)
						LEFT JOIN batch AS bt ON (bt.batchid=oi.batchid)
						WHERE oi.killed=0 AND oi.outboundid='".$outbound['outboundid']."' $condition2
						ORDER BY outboundid ASC,itemid ASC
					");
					$id=array();
					if($this->kclass->DB->numRows()){
						while($item=$this->kclass->DB->fetchArray($items)){
							if(in_array($item['materialid'],$id)){
								$quantity+=$item['outQuantity'];
							}else{
								$unit=$this->kclass->DB->queryFirst("SELECT title FROM unit WHERE unitid='".$item['unitid']."'");
								$id[]=$item['materialid'];
								$quantity=$item['outQuantity'];
								$result[$item['materialid']]=$item;
								$result[$item['materialid']]['unit']=$unit['title'];
							}
							if(!in_array($item['materialid'],$materialId)){
								$materialId[]=$item['materialid'];
							}
						}
					}
					if(!empty($id)){
						foreach($id as $key=>$val){
							$i+=1;
							$itemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').' small">
							<td>'.$i.'</td>
							<td><a href="/s.php?module=outbound&action=view&outboundid='.$outbound['outboundid'].'">'.$outbound['outboundno'].'</a></td>
							<td><a href="/s.php?module=barcode&action=view&barcodeid='.$result[$val]['barcodeid'].'">'.$result[$val]['barcode'].'</a></td>
							<td><a href="/s.php?module=batch&action=view&batchid='.$result[$val]['batchid'].'">'.$result[$val]['batchno'].'</a></td>
							<td><a href="/s.php?module=material&action=view&materialid='.$result[$val]['materialid'].'">'.$result[$val]['materialno'].'</a>'.'　'.$result[$val]['material'].'　'.$result[$val]['standard'].'</td>
							<td>'.$result[$val]['applyQuantity'].' '.$result[$val]['unit'].'</td>
							<td>'.$quantity.' '.$result[$val]['unit'].'</td>
							<td>'.date('Y-m-d',$result[$val]['applyTime']).'</td>
							<td>'.date('Y-m-d',$result[$val]['actualTime']).'</td>
							<td>'.$outbound['applicant'].'</td>
							<td>'.$result[$val]['remark'].'</td>
							</tr>';
						}
					}
				}
			}
			$count=count($materialId);
$body=<<<EOF
<table class="hundred">
<thead><tr class="even center"><th width="15">ID</th><th width="50">出库单号</th><th width="170">条码</th><th width="50">批次编号</th><th width="250">物资</th><th width="70">申请数量</th><th width="60">实际数量</th><th width="90">申请出库时间</th><th width="90">实际出库时间</th><th width="40">申请人</th><th width="70">备注</th></tr></thead><tbody>
{$itemtr}
<tr class="middle"><td colspan=2>物资种类　总计</td><td class="bold" colspan=9>{$count}</td></tr>
</tbody>
</table>
EOF;
			$this->kclass->page['title'].='出库统计';
			$this->kclass->page['onload'].='$(\'#date\').datepicker()';
			$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - 查找物资','right' => '<a href="/p.php?module=printer&action=inbound&inboundid">打印报表</a>　<a href="/s.php?module=inbound">查看列表</a>','body'=>$body));
		}
	}
	//
	function view(){
		$outbound=$this->kclass->DB->queryFirst("
			SELECT ob.outboundid,ob.orderid,ob.sampleid,ob.orderno,ob.outboundno,ob.oldoutno,ob.departmentid,ob.applicant,ob.purpose,ob.typeid,ob.workcenterid,ob.remark,ob.verifyRemark,ob.ifVerify,ob.ifConfirm,ob.verified,ob.created,ob.ifComplete,ob.killed,
				d.title AS department,
				t.title AS type,
				m.realname AS creator,
				me.realname AS modifier,
				mem.realname AS verifier
			FROM outbound AS ob
			LEFT JOIN `department` AS d ON (d.departmentid=ob.departmentid)
			LEFT JOIN `inorouttype` AS t ON (t.typeid=ob.typeid)
			LEFT JOIN `member` AS m ON (m.userid=ob.creator)
			LEFT JOIN `member` AS me ON (me.userid=ob.modifier)
			LEFT JOIN `member` AS mem ON (mem.userid=ob.verifier)
			WHERE ob.outboundid='".$this->kclass->input['outboundid']."'
			LIMIT 0,1
		");
		if($outbound){
			// 获得即将出库明细数量累加值
			$preitems=$this->kclass->DB->query("
				SELECT SUM(quantity) AS count,SUM(wastage) AS count1,versionid
				FROM preoutbounditem 
				WHERE killed=0 AND module='outbound' AND mid='".$this->kclass->input['outboundid']."'
				GROUP BY versionid
				ORDER BY itemid ASC
			");
			if($this->kclass->DB->numRows()){
				while($preitem=$this->kclass->DB->fetchArray($preitems)){
					$totalCount[$preitem['versionid']]=$preitem['count']+$preitem['count1'];
				}
			}
			//即将出库
			$items=$this->kclass->DB->query("
				SELECT poi.*,
					mv.title AS version,mv.qualified AS store1,mv.disqualified AS store2
				FROM preoutbounditem AS poi
				LEFT JOIN `materialversion` AS mv ON (mv.versionid=poi.versionid)
				LEFT JOIN `material` AS m ON (m.materialid=poi.materialid)
				WHERE poi.killed=0 AND poi.module='outbound' AND poi.mid='".$this->kclass->input['outboundid']."'
				ORDER BY m.materialno ASC
			");
			if($this->kclass->DB->numRows()){
				$i=1;
				$itemList='<table class="hundred"><thead><tr><th colspan="14">物资列表 '.$this->kclass->iif($outbound['ifConfirm']==1 AND $outbound['ifVerify']==1,$this->kclass->iif($outbound['ifComplete']==2,'','<a class="right" href="/s.php?module=outbound&action=outAll&outboundid='.$this->kclass->input['outboundid'].'">选择明细出库</a>'),'').'</th></tr></thead><tbody><tr class="center"><td width="30">ID</td>'.$this->kclass->iif($outbound['workcenterid']>0,'<td width="100">任务单号</td>','').'<td>物资</td><td width="30">版本</td><td>批次数</td><td width="60">是否良品</td><td width="60">库存数量</td><td width="80">数量/单位</td><td width="80">损耗数/单位</td><td width="80">总数</td><td width="90">申请出库时间</td><td width="80">已出库数量</td><td width="100">备注</td><td width="150"></td></tr></tbody><tbody class="small">';
				while($item=$this->kclass->DB->fetchArray($items)){
					$item['outTime']=date('Y-m-d',$item['outTime']);
					/* 保留
					if($item['ifPerBarcode']==1){
						$counter=$this->kclass->DB->queryFirst("SELECT SUM(*) AS count FROM outbounditem WHERE materialid='".$item['materialid']."' AND outboundid='".$this->kclass->input['outboundid']."'");
						$action='perout';
						$s='单个出库';
					}elseif($item['ifPerBarcode']==0){*/
						$counter=$this->kclass->DB->queryFirst("SELECT SUM(BINARY outQuantity) AS count FROM outbounditem WHERE killed=0 AND outboundid='".$this->kclass->input['outboundid']."' AND preitemid='".$item['itemid']."'");
						$action='out';
						$s='出库';
					//}
					if($outbound['ifVerify']==1){
						if($counter['count']==$item['quantity']+$item['wastage']){
							$outOperate='出库完成';
						}elseif($counter['count']==0){
							$outOperate='<a href="/s.php?module=outbound&action='.$action.'&outboundid='.$outbound['outboundid'].'&itemid='.$item['itemid'].'">'.$s.'</a>';
						}else{
							$outOperate='<a href="/s.php?module=outbound&action='.$action.'&outboundid='.$outbound['outboundid'].'&itemid='.$item['itemid'].'">继续出库</a>';
						}
					}
					$store=0;
					if($outbound['typeid']==15 OR $outbound['typeid']==16){
						$d=$this->kclass->iif($outbound['typeid']==15,' AND i.orderid='.$this->kclass->iif($outbound['orderid']>0,$outbound['orderid'],0),' AND i.sampleid='.$this->kclass->iif($outbound['sampleid']>0,$outbound['sampleid'],0));
						$versionQuantity=$this->kclass->DB->queryFirst("
							SELECT SUM(ii.quantity) AS quantity 
							FROM inbounditem AS ii
							LEFT JOIN preinbounditem AS pii ON (pii.itemid=ii.preitemid)
							LEFT JOIN inbound AS i ON (i.inboundid=ii.inboundid)
							WHERE ii.killed=0 $d AND ii.materialid='".$item['materialid']."' AND pii.productitemid='".$item['productitemid']."' 
							LIMIT 0,1
						");
						$store=$versionQuantity['quantity'];
						$product=$this->kclass->getProduct(array('productid'=>$item['productid'],'itemid'=>$item['productitemid']));
						$material='<a href="/s.php?module=product&action=view&productid='.$item['productid'].'">'.$product['title'].'</a>　'.$product['standard'];
					}else{
						if($item['ifQualified']==1){
							$qualifiedType='良品';
							$store=$item['store1'];
						}elseif($item['ifQualified']==0){
							$qualifiedType='不良品';
							$store=$item['store2'];
						}
						$materialInfo=$this->kclass->getMaterial(array('materialid'=>$item['materialid'],'itemid'=>$item['materialitemid']));
						$material='<a href="'.$materialInfo['url'].'">'.$materialInfo['no'].'</a>　'.$item['itemno'].'</a>　'.$materialInfo['material'].' '.$materialInfo['standard'];
					}

					$taskitem=$this->kclass->DB->queryFirst("
						SELECT d.taskid,d.taskno
						FROM taskitem AS t
						LEFT JOIN task AS d ON (d.taskid=t.taskid)
						WHERE t.killed=0 AND d.killed=0 AND t.itemid='".$item['taskitemid']."'
					");

					$itemList.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').'">
						<td>'.$i.'</td>
						'.$this->kclass->iif($outbound['workcenterid']>0,'<td><a href="/s.php?module=task&action=view&taskid='.$taskitem['taskid'].'">'.$taskitem['taskno'].'</td>','').'
						<td>'.$material.'</td>
						<td>'.$item['version'].'</td>
						<td>'.$barcodeCount.'</td>
						<td>'.$qualifiedType.'</td>	
						<td>'.$store.$item['unit'].'</td>
						<td>'.$item['quantity'].$item['unit'].'</td>
						<td>'.$item['wastage'].$item['unit'].'</td>
						<td>'.$totalCount[$item['versionid']].$item['unit'].'</td>
						<td>'.$item['outTime'].'</td>
						<td>'.$this->kclass->iif($counter['count'],$counter['count'],0).'</td>
						<td>'.$item['remark'].'</td>
						<td>'.$outOperate.'</td></tr>';
					$i++;
				}
				$itemList.='</tbody></table>';
			}
			$quantity=0;
			$outboundItems=$this->kclass->DB->query("
				SELECT oi.*,
					ba.batchno,
					bc.barcode,
					mem.realname AS creator
				FROM outbounditem AS oi
				LEFT JOIN batch AS ba ON (ba.batchid=oi.batchid)
				LEFT JOIN barcode AS bc ON (bc.barcodeid=oi.barcodeid)
				LEFT JOIN member AS mem ON (mem.userid=oi.creator)
				WHERE oi.killed=0 AND oi.outboundid='".$this->kclass->input['outboundid']."'
				ORDER BY oi.preitemid ASC
			");
			if($this->kclass->DB->numRows()){
				$i=1;
				$batchList='<table class="hundred"><thead><tr><th colspan="12">出库批次列表 <a class="right" href="s.php?module=outbound&action=cancelOut&type=all&outboundid='.$this->kclass->input['outboundid'].'" onclick="return confirm(\'你确定要对该出库单 '.$outbound['outboundno'].' 进行取消出库吗？\');">全部取消出库</a></th></tr></thead><tbody><tr class="even center"><td width="30">ID</td><td width="120">批次编号</td><td width="170">条码</td><td width="60">物资编号</td><td width="70">申请数量</td><td width="60">实际数量</td><td width="70">出库人</td><td width="90">申请出库时间</td><td width="90">实际出库时间</td><td width="120"></td></tr></tbody><tbody class="small">';
				while($outbounditem=$this->kclass->DB->fetchArray($outboundItems)){
					$outbounditem['applyTime']=date('Y-m-d',$outbounditem['applyTime']);
					$outbounditem['actualTime']=date('Y-m-d',$outbounditem['actualTime']);
					$materialInfo=$this->kclass->getMaterial(array('materialid'=>$outbounditem['materialid'],'itemid'=>$outbounditem['materialitemid']));
					$batchList.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').'">
						<td>'.$i.'</td>
						<td><a href="/s.php?module=batch&action=view&batchid='.$outbounditem['batchid'].'">'.$outbounditem['batchno'].'</a></td>
						<td><a href="/s.php?module=barcode&action=view&barcodeid='.$outbounditem['barcodeid'].'">'.$outbounditem['barcode'].'</a></td>
						<td><a href="'.$materialInfo['url'].'">'.$materialInfo['no'].'</a></td>
						<td>'.$outbounditem['applyQuantity'].$outbounditem['unit'].'</td>
						<td>'.$outbounditem['outQuantity'].$outbounditem['unit'].'</td>
						<td>'.$outbounditem['creator'].'</td>
						<td>'.$outbounditem['applyTime'].'</td>
						<td>'.$outbounditem['actualTime'].'</td>
						<td><a href="/s.php?module=batch&action=view&batchid='.$outbounditem['batchid'].'">查看详情</a>　<a target="_blank" href="/s.php?module=outbound&action=cancelOut&type=item&itemid='.$outbounditem['itemid'].'" onclick="return confirm(\'你确定要对该条明细 '.$outbounditem['materialno'].' 进行取消出库吗？\');">取消出库</a></td></tr>';
						$quantity+=$outbounditem['applyQuantity'];
					$i++;
				}
				$batchList.='</tbody></table>';
			}
			$outbound['created']=date('Y-m-d H:i',$outbound['created']);
			if($outbound['orderid']>0){
				$order=$this->kclass->DB->queryFirst("SELECT orderid,orderno FROM `order` WHERE orderid=".$outbound['orderid']." LIMIT 0,1");
				$r='<tr class="even"><td>订单编号：</td><td><a href="/s.php?module=order&action=view&orderid='.$order['orderid'].'">'.$order['orderno'].'</a></td><td></td><td></td></tr>';
			}elseif($outbound['sampleid']>0){
				$sample=$this->kclass->DB->queryFirst("SELECT sampleid,sampleno FROM sample WHERE sampleid=".$outbound['sampleid']." LIMIT 0,1");
				$r='<tr class="even"><td>样品单编号：</td><td><a href="/s.php?module=sample&action=view&sampleid='.$sample['sampleid'].'">'.$sample['sampleno'].'</a></td><td></td><td></td></tr>';
			}elseif($outbound['typeid']==17){
				$r='<tr class="even"><td>订单/样品单编号：</td><td>'.$outbound['oldoutno'].'</td><td></td><td></td></tr>';
			}

			$confirm=$this->kclass->confirm($outbound['ifConfirm']);
			$verify=$this->kclass->verify($outbound['ifVerify']);
			$outStatus='';
			if($outbound['ifComplete']==1){
				$outStatus='部分出库';
			}elseif($outbound['ifComplete']==2){
				$outStatus='出库完成';
			}else{
				$outStatus='等待出库';
			}
			if($outbound['modified']!=0)$modify='，由'.$outbound['modifier'].'于'.date('Y-m-d H:i:s',$outbound['modified']).'修改';
			if($outbound['verified']!=0)$overify='，由'.$outbound['verifier'].'于'.date('Y-m-d H:i:s',$outbound['verified']).'审核';
			if($outbound['ifConfirm']==1){
				if($outbound['ifVerify']==0){
					$operate='<a href="/s.php?module=outbound&action=remove&outboundid='.$outbound['outboundid'].'&rt=view">删除</a>　<a href="/s.php?module=outbound&action=update&outboundid='.$outbound['outboundid'].'">修改</a>　|　<a href="/s.php?module=outbound&action=verify&outboundid='.$outbound['outboundid'].'">审核</a>';
				}else{
					$operate='<a href="/s.php?module=outbound&action=update&outboundid='.$outbound['outboundid'].'">修改</a>';
					$rv='<tr class="odd"><td>审核备注：</td><td>'.$outbound['verifyRemark'].'</td><td></td><td></td></tr>';
				}
			}else{
				$operate='<a href="/s.php?module=outbound&action=confirm&outboundid='.$outbound['outboundid'].'">确认</a>　|　<a href="/s.php?module=outbound&action=update&outboundid='.$outbound['outboundid'].'">修改</a>　';
			}
			$inbound=$this->kclass->relatedInbound(array('sampleid'=>$outbound['sampleid'],'orderid'=>$outbound['orderid']));
$body=<<<EOF
<div class="title">{$outbound['outboundno']}<span class="right small gray">由{$outbound['creator']}于{$outbound['created']}建立{$modify}{$overify}。</span></div>
<dl id="outbound" class="tabs" style="display: block;">
<dt tabid=0>出库信息</dt>
<dt title="此出库相关的入库单"{$inbound['off']}>入库单{$inbound['count']}</dt>
<dd>
<table class="hundred">
<thead><tr><th colspan="4">出库单</th></tr></thead>
<tbody>
<tr class="odd">
<td width="85">出库单编号：</td><td width="450">{$outbound['outboundno']}　（{$confirm}　{$verify}　{$outStatus}）</td>
<td width="85">申 请 人：</td><td>{$outbound['applicant']} （{$outbound['department']}）</td>
</tr>
<tr class="even">
<td width="85">出库类型：</td><td width="450">{$outbound['type']}</td><td></td><td></td>
</tr>
{$r}
<tr class="even">
<td>用　　途：</td><td>{$outbound['purpose']}</td>
<td>备　　注：</td><td>{$outbound['remark']}</td>
</tr>
{$rv}
</tbody>
</table>
{$itemList}
{$batchList}
</dd>
<dd>{$inbound['panel']}</dd>
EOF;

		}else{ 
				$body = '系统数据出错，很抱歉！';
		}
		$this->kclass->tbline+=4;
		$this->kclass->page['title'].='查看出库单';
		$this->kclass->page['onload'].='dc.tabhover();dc.tabs({\'id\':\'outbound\'});dc.show()';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - <a href="/s.php?module=outbound&action=list">出库单列表</a> - 查看出库单', 'right' => $this->kclass->iif($outbound['killed']==0,$this->kclass->iif($outbound['ifVerify']==1,'<span class = "small">打印：</span> <a target="_blank" href="/p.php?action=outboundApply&outboundid='.$outbound['outboundid'].'">出库单</a>　|　','').'<span class = "small">操作：</span> <a href="/s.php?module=outbound&action=add">新建</a>　'.$operate,'<a href="/s.php?module=outbound&action=restore&outboundid='.$outbound['outboundid'].'&rt=view">恢复</a>'),'body' => $body));
	}
	// add outbound
	function add(){
		if(!$this->kclass->input['typeid']){
			$type=$this->kclass->chooserInorouttype(array('name'=>'typeid','width'=>200,'module'=>'outbound'));
$body=<<<EOF
<form action="/s.php?module=outbound&action=add" method="get">
<input type="hidden" name="module" value="outbound">
<input type="hidden" name="action" value="add">
<input type="hidden" name="departmentid" value="{$this->kclass->user['departmentid']}">
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
			$this->kclass->page['title'].='新建出库单';
			$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - <a href="/s.php?module=outbound&action=list">出库列表</a> - 新建出库单', 'right'=>'<a href="/s.php?module=outbound&action=list">返回列表</a>','body'=>$body));
		}else{
			if($this->kclass->input['typeid']==15 AND $this->kclass->input['orderid']<=0){
				$order=$this->kclass->chooserOrder(array('name'=>'orderid','hasBlank'=>1,'topname'=>'请选择','condition'=>' AND o.ifOutbound=1','width'=>350,'route'=>'PO'));
				$body=<<<EOF
<form action="/s.php?module=outbound&action=add" method="get">
<input type="hidden" name="module" value="outbound">
<input type="hidden" name="action" value="add">
<input type="hidden" name="departmentid" value="{$this->kclass->input['departmentid']}">
<input type="hidden" name="typeid" value="{$this->kclass->input['typeid']}">
<table>
<thead>
<tr>
<th colspan="4">新建出库申请：请选择订单</th>
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
				$this->kclass->page['title'].='新建出库单';
				$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - <a href="/s.php?module=outbound&action=list">出库列表</a> - 新建出库单', 'right'=>'<a href="/s.php?module=outbound&action=list">返回列表</a>','body'=>$body));
			}elseif($this->kclass->input['typeid']==16 AND $this->kclass->input['sampleid']<=0){
				$sample=$this->kclass->chooserSample(array('name'=>'sampleid','hasBlank'=>1,'topname'=>'请选择','width'=>350));
				$body=<<<EOF
<form action="/s.php?module=outbound&action=add" method="get">
<input type="hidden" name="module" value="outbound">
<input type="hidden" name="action" value="add">
<input type="hidden" name="departmentid" value="{$this->kclass->input['departmentid']}">
<input type="hidden" name="typeid" value="{$this->kclass->input['typeid']}">
<table>
<thead>
<tr>
<th colspan="4">新建出库申请：请选择样品单</th>
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
				$this->kclass->page['title'].='新建出库单';
				$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - <a href="/s.php?module=outbound&action=list">出库列表</a> - 新建出库单', 'right'=>'<a href="/s.php?module=outbound&action=list">返回列表</a>','body'=>$body));
			}else{
				$departments=$this->kclass->DB->query("SELECT departmentno,departmentid,title FROM department WHERE parentid='".$this->kclass->user['departmentid']."'");
				if($this->kclass->DB->numRows()){
					$departmentSelect.='选择 <span class="bold middle">'.$this->kclass->user['department'].'</span> 的下级部门：<select name="childDepartmentid">';
					while($department=$this->kclass->DB->fetchArray($departments)){
						$departmentSelect.='<option value="'.$department['departmentid'].'">'.$department['title'].'</option>';
					}
					$departmentSelect.='</select>';
				}
				// 获得出库类型
				$inType=$this->kclass->DB->queryFirst("SELECT typeid,title,notetypeid FROM inorouttype WHERE killed=0 AND typeid='".$this->kclass->input['typeid']."' LIMIT 0,1");
				// 出库单号
				$nowtime=date('Y-m-d',TIMENOW);
				$outboundno=$this->kclass->id(array('outbound'=>TIMENOW,'typeid'=>$inType['notetypeid']));
				
				if($this->kclass->input['workcenterid']>0){ // 从生产任务中下单
					$workcenterid=$this->kclass->input['workcenterid'];
					$typeid=$this->kclass->input['typeid'];
					if($typeid==9 OR $typeid==11){ // 领料bom 或者 补料bom  出库
						$count=count($this->kclass->input['itemId']);
						$th.='<tr class="center even" nohover><td width="15">ID</td><td width="80">任务单号</td><td>物资</td><td width="30">版本</td><td width="120">是否按BOM出库</td><td width="80">数量</td><td width="80">损耗数</td></td><td width="100">出库时间 <span class="red bold">*</span></td><td width="200">备注</td><td width="30">类型</td><td width="15">选</td></tr>';
						$j=1;
						for($i=$m=1;$i<=$count;$i++){
							if($this->kclass->input['itemSelect'][$i]!=''){
								$item=$this->kclass->DB->queryFirst("
									SELECT ti.*,
										t.taskno,
										mv.title AS version
									FROM `taskitem` AS ti
									LEFT JOIN `materialversion` AS mv ON (mv.versionid=ti.versionid)
									LEFT JOIN task AS t ON (t.taskid=ti.taskid)
									WHERE ti.killed=0 AND ti.itemid='".$this->kclass->input['itemSelect'][$i]."'
									LIMIT 0,1
								");
								$child=$this->kclass->bomChildList(array('parentid'=>$item['materialid'],'materialitemid'=>$item['materialitemid'],'versionid'=>$item['versionid'],'quantity'=>$item['quantity'],'loopNum'=>1));
								if(!empty($child['bom'])){
									foreach($child['bom'] as $val){
										if($val['ifGeneral']==1){
											$selectItemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').' small">
												<td><input type="hidden" name="taskItemid['.$j.']" value="'.$item['itemid'].'" />
												<input type="hidden" name="materialItemid['.$j.']" value="'.$val['materialitemid'].'" />
												<input type="hidden" name="itemMaterialid['.$j.']" value="'.$val['materialid'].'" />'.$j.'</td>
												<td><a href="/s.php?module=task&action=view*taskid='.$item['taskid'].'">'.$item['taskno'].'</a></td>
												<td><a href="'.$val['url'].'">'.$val['no'].'</a>　'.$val['title'].'　'.$val['standard'].'</td>
												<td>'.$val['version'].'<input type="hidden" name="itemVersionid['.$j.']" id="itemVersionid'.$j.'" value="'.$val['versionid'].'" /></td>
												<td><input type="radio" name="ifBom['.$j.']" value=1 disabled />是 <input type="radio" name="ifBom['.$j.']" value=0 checked disabled />否</td>
												<td><input type="text" name="itemQuantity['.$j.']" size="10"> '.$val['unit'].'</td>
												<td><input type="text" name="itemWastage['.$j.']" size="10" value=0></td>
												<td><input type="text" id="outTime'.$j.'" name="itemOutTime['.$j.']" size="15" value="'.date('Y-m-d',TIMENOW).'"></td>
												<td><input type="text" name="itemRemark['.$j.']" style="width:200px"></td>
												<td></td>
												<td><input type="checkbox" name="itemSelect['.$j.']" value="'.$j.'"></td>
											</tr>';
										}else{
											if($val['type']==0){
												$type='安装';
											}elseif($val['type']==1){
												$type='配送';
											}
											$mainItemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').' small">
												<td><input type="hidden" name="taskItemid['.$j.']" value="'.$item['itemid'].'" />
												<input type="hidden" name="materialItemid['.$j.']" value="'.$val['materialitemid'].'" />
												<input type="hidden" name="itemMaterialid['.$j.']" value="'.$val['materialid'].'" />
												<input type="hidden" name="itemSelect['.$j.']" value="'.$j.'">'.$j.'</td>
												<td><a href="/s.php?module=task&action=view*taskid='.$item['taskid'].'">'.$item['taskno'].'</a></td>
												<td><a href="'.$val['url'].'">'.$val['no'].'</a>　'.$val['title'].'　'.$val['standard'].'</td>
												<td>'.$val['version'].'<input type="hidden" name="itemVersionid['.$j.']" id="itemVersionid'.$j.'" value="'.$val['versionid'].'" /></td>
												<td><input type="radio" name="ifBom['.$j.']" value=1 />是 <input type="radio" name="ifBom['.$j.']" value=0 checked />否</td>
												<td><input type="text" name="itemQuantity['.$j.']" size="10" value="'.$val['quantity'].'"> '.$val['unit'].'</td>
												<td><input type="text" name="itemWastage['.$j.']" size="10" value=0></td>
												<td><input type="text" id="outTime'.$j.'" name="itemOutTime['.$j.']" size="15" value="'.date('Y-m-d',TIMENOW).'"></td>
												<td><input type="text" name="itemRemark['.$j.']" style="width:200px"></td>
												<td class="bold">'.$type.'</td>
												<td></td>
											</tr>';
										}
										$id.='#outTime'.$j.',';
										$j++;
									}
								}
							}
						}

						if($selectItemtr)$selectItemtr='<tr><td colspan="14" class="darkred">选择添加通用辅料:</td></tr>'.$selectItemtr;
						$itemList.=$th.$selectItemtr.'<tr><td colspan=14></td></tr>'.$mainItemtr;
					}elseif($typeid==10 OR $typeid==12){ // 非bom 领料或补料出库
						$taskitemid=$this->kclass->input['itemSelect'];
						$itemList.='<tr class="center even" nohover><td width="15">ID</td><td width="80">任务单号</td><td>物资</td><td width="30">版本</td><td width="120">是否按BOM出库</td><td width="80">数量</td><td width="80">损耗数</td></td><td width="100">出库时间 <span class="red bold">*</span></td><td width="200">备注</td></tr>';
						$item=$this->kclass->DB->queryFirst("
							SELECT ti.*,
								t.taskno,
								mv.title AS version
							FROM `taskitem` AS ti
							LEFT JOIN `materialversion` AS mv ON (mv.versionid=ti.versionid)
							LEFT JOIN task AS t ON (t.taskid=ti.taskid)
							WHERE ti.killed=0 AND ti.itemid='".$taskitemid."'
							LIMIT 0,1
						");
						for($i=1;$i<13;$i++){
							$itemList.='<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').' small">
								<td><input type="hidden" name="taskItemid['.$i.']" value="'.$taskitemid.'" />'.$i.'</td>
								<td><a href="/s.php?module=task&action=view&taskid='.$item['taskid'].'">'.$item['taskno'].'</a></td>
								<td>'.$this->kclass->chooserMaterial(array('hName'=>'itemMaterialid['.$i.']','hId'=>'itemMaterialid'.$i,'name'=>'itemMaterial['.$i.']','id'=>'itemMaterial'.$i,'width'=>600,'line'=>$i,'showVersion'=>1)).'</td>
								<td><select name="itemVersionid['.$i.']" id="itemVersionid'.$i.'" style="width:200px;"></select></td>
								<td><input type="radio" name="ifBom['.$i.']" value=1 />是 <input type="radio" name="ifBom['.$i.']" value=0 checked />否</td>
								<td><input type="text" name="itemQuantity['.$i.']" size="10"></td>
								<td><input type="text" name="itemWastage['.$i.']" size="10" value=0></td>
								<td><input type="text" id="outTime'.$i.'" name="itemOutTime['.$i.']" size="20" value="'.date('Y-m-d',TIMENOW).'"></td>
								<td><input type="text" name="itemRemark['.$i.']" style="width:200px"></td>
							</tr>';
							$id.=$this->kclass->iif($i<13,'#outTime'.$i.',','#outTime'.$i);
						}
					}
				}else{// 非生产任务下单。
					if(($this->kclass->input['typeid']==15 AND $this->kclass->input['orderid']>0) OR ($this->kclass->input['typeid']==16 AND $this->kclass->input['sampleid']>0)){ // 订单或者样品出库
						if($this->kclass->input['orderid']>0){
							$order=$this->kclass->DB->queryFirst("SELECT orderid,orderno FROM `order` WHERE killed=0 AND orderid='".$this->kclass->input['orderid']."'");
							$r='<tr class="odd"><td>订单编号：</td><td><a href="/s.php?module=order&action=view&orderid='.$this->kclass->input['orderid'].'&type=PO">'.$order['orderno'].'</a></td><td></td><td></td></tr>';
							$module='order';$mid=$order['orderid'];
						}elseif($this->kclass->input['sampleid']>0){
							$sample=$this->kclass->DB->queryFirst("SELECT sampleid,sampleno FROM `sample` WHERE killed=0 AND sampleid='".$this->kclass->input['sampleid']."'");
							$r='<tr class="odd"><td>样品单编号：</td><td><a href="/s.php?module=sample&action=view&sampleid='.$this->kclass->input['sampleid'].'">'.$sample['sampleno'].'</a></td><td></td><td></td></tr>';
							$module='sample';$mid=$sample['sampleid'];
						}

						$itemList='<tr class="center even" nohover><td width="30">ID</td><td>选择物资 <span class="bold red">*</span></td><td width="50">数量 <span class="red bold">*</span></td><td width="50">出库时间 <span class="red bold">*</span></td><td width="100">备注</td></tr>';
						$items=$this->kclass->DB->query("
							SELECT i.itemid,i.quantity,i.productid,i.productitemid,i.productAttributes,
								pd.title,pd.attrid
							FROM `item` AS i
							LEFT JOIN `product` AS pd ON (pd.productid=i.productid)
							WHERE i.killed=0 AND i.module='".$module."' AND i.mid='".$mid."'
							ORDER BY i.itemid ASC
						");
						if($this->kclass->DB->numRows()){
							$i=1;
							while($item=$this->kclass->DB->fetchArray($items)){
								if($item['attrid']==5){ // 配件包
									$partitems=$this->kclass->DB->query("
										SELECT pi.quantity,pi.childid,p.title
										FROM partitem AS pi 
										LEFT JOIN product AS p ON (p.productid=pi.childid)
										WHERE pi.killed=0 AND pi.productid='".$item['productid']."'
										ORDER BY pi.ordering ASC
									");
									if($this->kclass->DB->numRows()){
										while($partitem=$this->kclass->DB->fetchArray($partitems)){
											$itemList.='<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').' small">
												<td><input type="hidden" name="itemId['.$i.']" value="'.$i.'" />'.$i.'</td>
												<td><input type="hidden" name="productId['.$i.']" value="'.$partitem['childid'].'" />
												<input type="hidden" name="productItemid['.$i.']" value="0" />
												<a href="/s.php?module=product&action=view&productid='.$partitem['childid'].'">'.$partitem['title'].'</a></td>
												<td><input type="text" name="itemQuantity['.$i.']" size="10" value="'.($item['quantity']*$partitem['quantity']).'"></td>
												<td><input type="text" id="outTime'.$i.'" name="itemOutTime['.$i.']" size="10" value="'.date('Y-m-d',TIMENOW).'"></td>
												<td><input type="text" name="itemRemark['.$i.']" style="width:150px"></td>
											</tr>';
											$i++;
										}
									}
								}else{
									$pitem=$this->kclass->getProduct(array('productid'=>$item['productid'],'itemid'=>$item['productitemid']));
									$itemList.='<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').' small">
										<td><input type="hidden" name="itemId['.$i.']" value="'.$i.'" />'.$i.'</td>
										<td><input type="hidden" name="productId['.$i.']" value="'.$item['productid'].'" />
										<input type="hidden" name="productItemid['.$i.']" value="'.$item['productitemid'].'" />
										<a href="/s.php?module=product&action=view&productid='.$item['productid'].'">'.$pitem['title'].'</a>　'.$pitem['standard'].'</td>
										<td><input type="text" name="itemQuantity['.$i.']" size="10" value="'.$item['quantity'].'"></td>
										<td><input type="text" id="outTime'.$i.'" name="itemOutTime['.$i.']" size="10" value="'.date('Y-m-d',TIMENOW).'"></td>
										<td><input type="text" name="itemRemark['.$i.']" style="width:150px"></td>
									</tr>';
									$i++;
								}
							}
						}
					}else{// 其他类型的出库
						if($this->kclass->input['typeid']==17){ // 旧单出库
							$r='<tr class="odd"><td>样品单/订单编号：</td><td><input type="text" name="oldoutno" style="width:250px"/></td><td></td><td></td></tr>';
						}
						$itemList='<tr class="center even" nohover><td width="30">ID</td><td>选择物资 <span class="bold red">*</span></td><td>版本 <span class="red bold">*</span></td><td>是否按BOM出库</td><td>是否良品</td><td width="50">数量 <span class="red bold">*</span></td><td>损耗数</td><td width="50">出库时间 <span class="red bold">*</span></td><td width="100">备注</td></tr>';
						for($i=1;$i<13;$i++){
							$itemList.='<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').' small">
								<td><input type="hidden" name="itemId['.$i.']" value="'.$i.'" />'.$i.'</td>
								<td>'.$this->kclass->chooserMaterial(array('hName'=>'itemMaterialid['.$i.']','hId'=>'itemMaterialid'.$i,'name'=>'itemMaterial['.$i.']','id'=>'itemMaterial'.$i,'width'=>400,'line'=>$i,'showVersion'=>1)).'</td>
								<td><select name="itemVersionid['.$i.']" id="itemVersionid'.$i.'" style="width:80px;"></select></td>
								<td><input type="radio" name="ifBom['.$i.']" value=1 />是 <input type="radio" name="ifBom['.$i.']" value=0 checked />否</td>
								<td><input type="radio" name="ifQualified['.$i.']" value=1 checked />良品 <input type="radio" name="ifQualified['.$i.']" value=0 />不良品</td>
								<td><input type="text" name="itemQuantity['.$i.']" size="10"></td>
								<td><input type="text" name="itemWastage['.$i.']" size="10" value="0"></td>
								<td><input type="text" id="outTime'.$i.'" name="itemOutTime['.$i.']" size="10" value="'.date('Y-m-d',TIMENOW).'"></td>
								<td><input type="text" name="itemRemark['.$i.']" style="width:150px"></td>
							</tr>';
							$id.=$this->kclass->iif($i<13,'#outTime'.$i.',','#outTime'.$i);
						}
					}
				}
$body=<<<EOF
<form action="/s.php?module=outbound&action=insert" name="outbound" method="post" onsubmit="dc.checkSubmit(this)">
<input type="hidden" name="module" value="outbound" />
<input type="hidden" name="action" value="insert" />
<input type="hidden" name="departmentid" value="{$this->kclass->user['departmentid']}" />
<input type="hidden" name="typeid" value="{$this->kclass->input['typeid']}" />
<input type="hidden" name="noteTypeid" value="{$inType['notetypeid']}" />
<input type="hidden" name="orderid" value="{$this->kclass->input['orderid']}" />
<input type="hidden" name="sampleid" value="{$this->kclass->input['sampleid']}" />
<input type="hidden" name="workcenterid" value="{$this->kclass->input['workcenterid']}" />
<table class="hundred">
<thead></tr><th colspan="4">基本信息</th></tr></thead>
<tbody>
<tr class="odd">
<td>出库单号：<span class="red bold">*</span></td><td width="700"><input type="text" name="outboundno" value="{$outboundno}" id="outboundno" class="middle bold darkred" size="14"></a>　　　编号生成方式：<input type="radio" name="noType" value="0" checked>自动<input type="radio" name="noType" value="1" >手动 {$departmentSelect}　　　转月时间：<input type="text" id="realtime" name="realtime" value="{$nowtime}" size=10></td>
<td>申 请 人：<span class="red bold">*</span></td><td><input type="text" name="applicant" value="{$this->kclass->user['realname']}" />　出库类型：{$inType['title']}</td>
</tr>
{$r}
<tr class="even">
<td>用　　途：</td><td><textarea name="purpose" style="width:444px;height:111px;" ></textarea></td>
<td>备　　注：</td><td><textarea name="remark" style="width:444px;height:111px;"></textarea></td>
</tr>
</tbody>
</table>
<table class="hundred">
<thead></tr><th colspan="11">出库申请明细</th></tr></thead>
<tbody>
{$itemList}
<tr class="even">
	<td class="small gray" colspan="11">
		注意事项：
		如果所列表格不够，那么在提交保存后再点击修改来增加物资；
		一张单最多只能填入10条数据</td>
</tr>
</tbody>
</table>
<table class="tlist tsuite" align="center" width="100%">
<tbody>
	<tr class="even" nohover>
		<td class="center"><input type="submit" id="submitButton" value="  提交  " accesskey="s"><input type="reset" value="  复位  "/></td>
	</tr>
</tbody>
</table>
EOF;
			}
			$this->kclass->tbline+=4;
			$this->kclass->page['title'].='新建出库单';
			$this->kclass->page['onload'].='var dates=$(\'#realtime,'.$id.'\').datepicker({onSelect:function(selectedDate){instance=$(this).data(\'datepicker\'),date=$.datepicker.parseDate(instance.settings.dateFormat || $.datepicker._defaults.dateFormat,selectedDate,instance.settings);dc.updateNo({\'id\':\'outboundno\',\'typeid\':'.$inType['notetypeid'].'});}});dc.tabhover()';
			$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - <a href="/s.php?module=outbound&action=list">出库列表</a> - 新建出库单', 'right'=>'<a href="/s.php?module=outbound&action=list">返回列表</a>','body'=>$body));
		}
	}

	//
	function insert(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			if($this->kclass->input['departmentid']<=0){
				$e .= '<li>请选择出库申请单中的 部门。</li>';
			}
			if($this->kclass->input['applicant']==''){
				$e .= '<li>请填写出库申请单的 申请人。</li>';
			}
			if($this->kclass->input['typeid']==''){
				$e .= '<li>请选择出库申请单的 出库类型。</li>';
			}
			$hasItem=$hasBom=0;
			if($this->kclass->input['workcenterid']>0){
				$j=count($this->kclass->input['taskItemid']);
				for($i=1;$i<=$j;$i++){
					if($this->kclass->input['itemMaterialid'][$i]>0 AND $this->kclass->input['itemVersionid'][$i]>0 AND $this->kclass->input['itemQuantity'][$i]>0 AND $this->kclass->input['itemOutTime'][$i]!=''){
						$material=$this->kclass->DB->queryFirst("SELECT materialid,materialno,qualified FROM material WHERE killed=0 AND materialid='".$this->kclass->input['itemMaterialid'][$i]."'");
						// 判断是否有明细
						if($this->kclass->input['typeid']==9 OR $this->kclass->input['typeid']==11){
							if($this->kclass->input['itemSelect'][$i]>0){
								$hasItem=1;
							}
						}else{
							$hasItem=1;
						}
						// 判断任务单是否已领料
						if($this->kclass->input['taskItemid'][$i]>0 AND $this->kclass->input['typeid']!=11 AND $this->kclass->input['typeid']!=12){
							$taskitem=$this->kclass->DB->queryFirst("SELECT ti.ifChooser,t.taskno FROM taskitem AS ti LEFT JOIN task AS t ON (ti.taskid=t.taskid) WHERE ti.itemid='".$this->kclass->input['taskItemid'][$i]."'");
							if($taskitem['ifChooser']==1){
								$e.='<li>任务单 '.$taskitem['taskno'].' 明细 '.$material['materialno'].' 已经领料。请勿重复操作.</li>';
							}
						}
						// 判断领料数量是否大于库存数量
						/*if($this->kclass->input['itemQuantity'][$i]>$material['qualified']){
							$e.='<li>物资 '.$material['materialno'].' 库存数只有 '.$material['qualified'].' 小于所要出库的数量 '.$this->kclass->input['itemQuantity'][$in].' </li>';
						}*/
					}
				}
			}else{
				$j=count($this->kclass->input['itemId']);
				for($i=1;$i<=$j;$i++){
					if(($this->kclass->input['itemMaterialid'][$i]>0 OR $this->kclass->input['productId'][$i]>0) AND $this->kclass->input['itemQuantity'][$i]!='' AND $this->kclass->input['itemOutTime'][$i]!=''){
						if($this->kclass->input['typeid']==15 OR $this->kclass->input['typeid']==16){
							$hasItem=1;
						}else{
							if($this->kclass->input['itemVersionid'][$i]>0){
								$hasItem=1;
							}
						}

						if($this->kclass->input['typeid']==9 OR $this->kclass->input['typeid']==11){
							$material=$this->kclass->DB->queryFirst("SELECT materialno FROM material WHERE materialid=".$this->kclass->input['itemMaterialid'][$i]." LIMIT 0,1");
							$child=$this->kclass->bomChildList(array('parentid'=>$this->kclass->input['itemMaterialid'][$i],'materialitemid'=>$this->kclass->input['materialItemid'][$i],'versionid'=>$this->kclass->input['itemVersionid'][$i],'loopNum'=>1));
							if(!empty($child)){
								foreach($child['bom'] as $key => $val){
									$materialQuantity=$this->kclass->DB->queryFirst("SELECT quantity FROM material WHERE materialid='".$val['materialid']."' LIMIT 0,1");
									if($val['ifGeneral']==0){
										/*if($val['quantity']>$materialQuantity['quantity']){
											$e.='<li>BOM单中物资'.$val['materialno'].'的数量小于所要求出库的数量</li>';
										}*/
									}
								}
							}else{
								$e.='<li>申请出库物资 <b>'.$material['materialno'].'</b> 有存在没有BOM单的物资。请查看</li>';
							}
						}else{
							if($this->kclass->input['typeid']!=15 OR $this->kclass->input['typeid']!=16){
								$materialQuantity=$this->kclass->DB->queryFirst("SELECT qualified FROM materialversion WHERE killed=0 AND versionid='".$this->kclass->input['itemVersionid'][$i]."' LIMIT 0,1");
							}

							/*if($this->kclass->input['itemQuantity'][$i]>$materialQuantity['quantity']){
								$e.='<li>物资'.$material['materialno'].'的数量小于所要求出库的数量</li>';
							}*/
						}
					}
				}
			}
			if($hasItem==0){
				$e.='<li>请填写出库申请单的完整明细</li>';
			}
			$sd=explode('-', $this->kclass->input['realtime']);
			$realtime=mktime(0,0,0,$sd[1],$sd[2],$sd[0]);
			if($this->kclass->input['noType']==1){
				$outboundno=$this->kclass->input['outboundno'];
			}elseif($this->kclass->input['noType']==0){
				$outboundno=$this->kclass->id(array('outbound'=>$realtime,'typeid'=>$this->kclass->input['noteTypeid']));
			}

			//
			$outbound=$this->kclass->DB->queryFirst("SELECT * FROM outbound WHERE killed=0 AND noteTypeid='".$this->kclass->input['noteTypeid']."' AND outboundno='".$outboundno."' LIMIT 0,1");
			if($outbound){
				$e.='<li>出库单号【'.$outboundno.'】已存在。</li>';
			}
		}
		if($e){
			$this->kclass->messager(array(
				'title' => '新建出库申请',
				'text' => '您在新建出库申请的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$this->kclass->input['orderid']=$this->kclass->iif($this->kclass->input['orderid']>0,$this->kclass->input['orderid'],0);
		$this->kclass->input['supplierid']=$this->kclass->iif($this->kclass->input['supplierid']>0,$this->kclass->input['supplierid'],0);
		$sampleid=$this->kclass->iif($this->kclass->input['sampleid']>0,$this->kclass->input['sampleid'],0);
		$workcenterid=$this->kclass->iif($this->kclass->input['workcenterid']>0,$this->kclass->input['workcenterid'],0);
		$this->kclass->DB->query("
			INSERT INTO outbound (outboundno,orderid,sampleid,workcenterid,realtime,oldoutno,typeid,noteTypeid,departmentid,supplierid,applicant,purpose,remark,created,creator)
			VALUES ('".$outboundno."','".$this->kclass->input['orderid']."','".$sampleid."','".$workcenterid."','".$realtime."','".$this->kclass->input['oldoutno']."','".$this->kclass->input['typeid']."','".$this->kclass->input['noteTypeid']."','".$this->kclass->user['departmentid']."','".$this->kclass->input['supplierid']."','".$this->kclass->input['applicant']."','".$this->kclass->input['purpose']."','".$this->kclass->input['remark']."','".TIMENOW."','".$this->kclass->user['userid']."')
		");
		$outboundid=$this->kclass->DB->insertID();
		if($this->kclass->input['workcenterid']>0){// 任务单出库
			$j=count($this->kclass->input['taskItemid']);
			for($i=1;$i<=$j;$i++){
				if($this->kclass->input['itemMaterialid'][$i]>0 AND $this->kclass->input['itemVersionid'][$i]>0 AND $this->kclass->input['itemQuantity'][$i]>0 AND $this->kclass->input['itemOutTime'][$i]!=''){
					$sd=explode('-', $this->kclass->input['itemOutTime'][$i]);
					$outTime=mktime(0,0,0,$sd[1],$sd[2],$sd[0]);
					$wastage=$this->kclass->iif(floatval($this->kclass->input['itemWastage'][$i])>0,floatval($this->kclass->input['itemWastage'][$i]),0);
					$materialid=$this->kclass->iif($this->kclass->input['itemMaterialid'][$i]>0,$this->kclass->input['itemMaterialid'][$i],0);
					$materialitemid=$this->kclass->iif($this->kclass->input['materialItemid'][$i]>0,$this->kclass->input['materialItemid'][$i],0);
					$ifQualified=$this->kclass->iif(isset($this->kclass->input['ifQualified'][$i]),$this->kclass->input['ifQualified'][$i],1);
					$ifBom=$this->kclass->iif(isset($this->kclass->input['ifBom'][$i]),$this->kclass->input['ifBom'][$i],0);
					$quantity=intval($this->kclass->input['itemQuantity'][$i]);
					if($materialid>0){
						$material=$this->kclass->DB->queryFirst("
							SELECT materialid,unitid,quantity,materialno
							FROM material 
							WHERE materialid='".$materialid."'
							LIMIT 0,1
						");
					}

					if($this->kclass->input['typeid']==9 OR $this->kclass->input['typeid']==11){
						if($this->kclass->input['itemSelect'][$i]>0){
							if($ifBom==1){
								// bom方式 暂时不加入 损耗数
								$child=$this->kclass->bomChildList(array('parentid'=>$materialid,'materialitemid'=>$materialitemid,'versionid'=>$this->kclass->input['itemVersionid'][$i],'quantity'=>$quantity,'loopNum'=>1));
								if(!empty($child)){
									foreach($child['bom'] as $val){
										if($val['ifGeneral']==0){
											$this->kclass->DB->query("
												INSERT preoutbounditem (module,mid,materialid,versionid,materialitemid,ifQualified,taskitemid,quantity,unitid,outTime,remark,created,creator)
												VALUES ('outbound','".$outboundid."','".$val['materialid']."','".$val['versionid']."','".$val['materialitemid']."',1,'".$this->kclass->input['taskItemid'][$i]."','".$val['quantity']."','".$val['unitid']."','".$outTime."','".$this->kclass->input['itemRemark'][$i]."','".TIMENOW."','".$this->kclass->user['userid']."')
											");
										}
									}
								}
							}else{
								$this->kclass->DB->query("
									INSERT preoutbounditem (module,mid,materialid,versionid,materialitemid,ifQualified,taskitemid,quantity,wastage,unitid,outTime,remark,created,creator)
									VALUES ('outbound','".$outboundid."','".$materialid."','".$this->kclass->input['itemVersionid'][$i]."','".$materialitemid."',1,'".$this->kclass->input['taskItemid'][$i]."','".$this->kclass->input['itemQuantity'][$i]."','".$this->kclass->input['itemWastage'][$i]."','".$material['unitid']."','".$outTime."','".$this->kclass->input['itemRemark'][$i]."','".TIMENOW."','".$this->kclass->user['userid']."')
								");
							}					
						}
					}else{
						if($ifBom==1){
							// bom方式 暂时不加入 损耗数
							$child=$this->kclass->bomChildList(array('parentid'=>$materialid,'materialitemid'=>$materialitemid,'versionid'=>$this->kclass->input['itemVersionid'][$i],'quantity'=>$quantity,'loopNum'=>1));
							if(!empty($child)){
								foreach($child['bom'] as $val){
									if($val['ifGeneral']==0){
										$this->kclass->DB->query("
											INSERT preoutbounditem (module,mid,materialid,versionid,materialitemid,ifQualified,taskitemid,quantity,unitid,outTime,remark,created,creator)
											VALUES ('outbound','".$outboundid."','".$val['materialid']."','".$val['versionid']."','".$val['materialitemid']."',1,'".$this->kclass->input['taskItemid'][$i]."','".$val['quantity']."','".$val['unitid']."','".$outTime."','".$this->kclass->input['itemRemark'][$i]."','".TIMENOW."','".$this->kclass->user['userid']."')
										");
									}
								}
							}
						}else{
							$this->kclass->DB->query("
								INSERT preoutbounditem (module,mid,materialid,versionid,materialitemid,ifQualified,taskitemid,quantity,wastage,unitid,outTime,remark,created,creator)
								VALUES ('outbound','".$outboundid."','".$materialid."','".$this->kclass->input['itemVersionid'][$i]."','".$materialitemid."',1,'".$this->kclass->input['taskItemid'][$i]."','".$this->kclass->input['itemQuantity'][$i]."','".$this->kclass->input['itemWastage'][$i]."','".$material['unitid']."','".$outTime."','".$this->kclass->input['itemRemark'][$i]."','".TIMENOW."','".$this->kclass->user['userid']."')
							");
						}
					}
					$this->kclass->DB->query("UPDATE taskitem SET ifChooser=1 WHERE itemid='".$this->kclass->input['taskItemid'][$i]."'");
				}
			}
		}else{ // 非任务单出库
			$j=count($this->kclass->input['itemId']);
			for($i=1;$i<=$j;$i++){
				if(($this->kclass->input['itemMaterialid'][$i]>0 OR $this->kclass->input['productId'][$i]>0) AND $this->kclass->input['itemQuantity'][$i]>0 AND $this->kclass->input['itemOutTime'][$i]!=''){
					$versionid=$this->kclass->iif($this->kclass->input['typeid']==15 OR $this->kclass->input['typeid']==16,0,$this->kclass->input['itemVersionid'][$i]);
					$sd=explode('-', $this->kclass->input['itemOutTime'][$i]);
					$outTime=mktime(0,0,0,$sd[1],$sd[2],$sd[0]);
					$wastage=$this->kclass->iif(floatval($this->kclass->input['itemWastage'][$i])>0,floatval($this->kclass->input['itemWastage'][$i]),0);
					$materialid=$this->kclass->iif($this->kclass->input['itemMaterialid'][$i]>0,$this->kclass->input['itemMaterialid'][$i],0);
					$productitemid=$this->kclass->iif($this->kclass->input['productItemid'][$i]>0,$this->kclass->input['productItemid'][$i],0);
					$productid=$this->kclass->iif($this->kclass->input['productId'][$i]>0,$this->kclass->input['productId'][$i],0);
					$materialitemid=$this->kclass->iif($this->kclass->input['materialItemid'][$i]>0,$this->kclass->input['materialItemid'][$i],0);
					$ifQualified=$this->kclass->iif(isset($this->kclass->input['ifQualified'][$i]),$this->kclass->input['ifQualified'][$i],1);
					$ifBom=$this->kclass->iif(isset($this->kclass->input['ifBom'][$i]),$this->kclass->input['ifBom'][$i],0);
					if($materialid>0){
						$material=$this->kclass->DB->queryFirst("
							SELECT materialid,unitid,quantity,materialno
							FROM material 
							WHERE materialid='".$materialid."'
							LIMIT 0,1
						");
					}
					if($this->kclass->input['typeid']==9 OR $this->kclass->input['typeid']==11){ //bom方式领料
						// bom方式 暂时不加入 损耗数
						$child=$this->kclass->bomChildList(array('parentid'=>$materialid,'materialitemid'=>$materialitemid,'versionid'=>$this->kclass->input['itemVersionid'][$i],'quantity'=>$this->kclass->input['itemQuantity'][$i],'loopNum'=>1));
						if(!empty($child)){
							foreach($child['bom'] as $val){
								if($val['ifGeneral']==0){
									$this->kclass->DB->query("
										INSERT preoutbounditem
											(module,mid,materialid,versionid,materialitemid,ifQualified,quantity,outTime,remark,created,creator)
										VALUES ('".$this->kclass->input['module']."','".$outboundid."','".$val['materialid']."','".$val['versionid']."','".$val['materialitemid']."',1,'".$val['quantity']."','".$outTime."','".$this->kclass->input['itemRemark'][$i]."','".TIMENOW."','".$this->kclass->user['userid']."')
									");
								}
							}
						}
					}else{ // 非bom方式领料
						if($ifBom==1){ // 非bom方式中选择按bom领料
							// bom方式 暂时不加入 损耗数
							$child=$this->kclass->bomChildList(array('parentid'=>$materialid,'materialitemid'=>$materialitemid,'versionid'=>$this->kclass->input['itemVersionid'][$i],'quantity'=>$this->kclass->input['itemQuantity'][$i],'loopNum'=>1));
							if(!empty($child)){
								foreach($child['bom'] as $key => $val){
									if($val['ifGeneral']==0){
										$this->kclass->DB->query("
											INSERT preoutbounditem
												(module,mid,materialid,versionid,materialitemid,ifQualified,quantity,outTime,remark,created,creator)
											VALUES ('".$this->kclass->input['module']."','".$outboundid."','".$val['materialid']."','".$val['versionid']."','".$val['materialitemid']."',1,'".$val['quantity']."','".$outTime."','".$this->kclass->input['itemRemark'][$i]."','".TIMENOW."','".$this->kclass->user['userid']."')
										");
									}
								}
							}
						}else{
							$this->kclass->DB->query("
								INSERT preoutbounditem (module,mid,materialid,versionid,materialitemid,productid,productitemid,ifQualified,quantity,wastage,unitid,outTime,remark,created,creator)
								VALUES ('".$this->kclass->input['module']."','".$outboundid."','".$materialid."','".$versionid."','".$materialitemid."','".$productid."','".$productitemid."','".$ifQualified."','".$this->kclass->input['itemQuantity'][$i]."','".$wastage."','".$this->kclass->iif($material['unitid']>0,$material['unitid'],0)."','".$outTime."','".$this->kclass->input['itemRemark'][$i]."','".TIMENOW."','".$this->kclass->user['userid']."')
							");
						}
					}
				}
			}
		}

		$this->kclass->messager(array(
			'title' => '新建出库单',
			'text' => '出库单 【<b>'.$outboundno.'</b>】 已新建成功!返回添加出库物资页面',
			'url' => '/s.php?module=outbound&action=view&outboundid='.$outboundid,
			'sec' => 2
		));
	}
	//
	function update(){
		$outbound=$this->kclass->DB->queryFirst("SELECT * FROM outbound WHERE killed=0 AND outboundid='".$this->kclass->input['outboundid']."' LIMIT 0,1");
		/*$outbounditem=$this->kclass->DB->queryFirst("SELECT COUNT(itemid) AS count FROM outbounditem WHERE killed=0 AND outboundid='".$this->kclass->input['outboundid']."' LIMIT 0,1");
		if($outbounditem['count']>0){
			$e='<li>请先等待 仓库 取消出库</li>';
		}*/
		if(empty($outbound)){
			$e='<li>数据错误，很抱歉</li>';
		}
		if($e){
			$this->kclass->messager(array(
				'title' => '修改出库申请',
				'text' => '您在修改出库申请的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		if(!$this->kclass->input['typeid']){
			$type=$this->kclass->chooserInorouttype(array('name'=>'typeid','width'=>200,'module'=>'outbound','selectedid'=>$outbound['typeid']));
$body=<<<EOF
<form action="/s.php?module=outbound&action=update" method="get">
<input type="hidden" name="module" value="outbound">
<input type="hidden" name="action" value="update">
<input type="hidden" name="outboundid" value="{$this->kclass->input['outboundid']}">
<input type="hidden" name="departmentid" value="{$this->kclass->user['departmentid']}">
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
	}else{
		if($this->kclass->input['typeid']==15 AND $this->kclass->input['orderid']<=0){
			$order=$this->kclass->chooserOrder(array('name'=>'orderid','hasBlank'=>1,'topname'=>'请选择','width'=>350,'route'=>'PO','selectedid'=>$outbound['orderid']));
			$body=<<<EOF
<form action="/s.php?module=outbound&action=update" method="get">
<input type="hidden" name="module" value="outbound">
<input type="hidden" name="action" value="update">
<input type="hidden" name="departmentid" value="{$this->kclass->input['departmentid']}">
<input type="hidden" name="outboundid" value="{$this->kclass->input['outboundid']}">
<input type="hidden" name="typeid" value="{$this->kclass->input['typeid']}">
<table>
<thead>
<tr>
<th colspan="4">新建出库申请：请选择订单</th>
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
		}elseif($this->kclass->input['typeid']==16 AND $this->kclass->input['sampleid']<=0){
			$sample=$this->kclass->chooserSample(array('name'=>'sampleid','hasBlank'=>1,'topname'=>'请选择','width'=>350,'selectedid'=>$outbound['sampleid']));
			$body=<<<EOF
<form action="/s.php?module=outbound&action=update" method="get">
<input type="hidden" name="module" value="outbound">
<input type="hidden" name="action" value="update">
<input type="hidden" name="departmentid" value="{$this->kclass->input['departmentid']}">
<input type="hidden" name="outboundid" value="{$this->kclass->input['outboundid']}">
<input type="hidden" name="typeid" value="{$this->kclass->input['typeid']}">
<table>
<thead>
<tr>
<th colspan="4">新建出库申请：请选择样品单</th>
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
		}else{
			$departments=$this->kclass->DB->query("SELECT departmentno,departmentid,title FROM department WHERE parentid='".$this->kclass->user['departmentid']."'");
			if($this->kclass->DB->numRows()){
				$departmentSelect.='选择 <span class="bold middle">'.$this->kclass->user['department'].'</span> 的下级部门：<select name="childDepartmentid">';
				while($department=$this->kclass->DB->fetchArray($departments)){
					$departmentSelect.='<option value="'.$department['departmentid'].'">'.$department['title'].'</option>';
				}
				$departmentSelect.='</select>';
			}
			// 获得出库类型
			$inType=$this->kclass->DB->queryFirst("SELECT typeid,title,notetypeid FROM inorouttype WHERE killed=0 AND typeid='".$this->kclass->input['typeid']."' LIMIT 0,1");
			// 获得已经申请出库的物资明细
			$orgItems=$this->kclass->DB->query("
				SELECT itemid,materialid,materialitemid,productid,productitemid,versionid,quantity,wastage,ifQualified,unitid,outTime,remark
				FROM preoutbounditem 
				WHERE killed=0 AND module='outbound' AND mid='".$outbound['outboundid']."'
			");
			$k=1;
			if($this->kclass->DB->numRows()){
				while($orgItem=$this->kclass->DB->fetchArray($orgItems)){
					if($this->kclass->input['typeid']!=15 AND $this->kclass->input['typeid']!=16){
						if($orgItem['ifQualified']==1){
							$checked1='checked';$checked2='';
						}elseif($orgItem['ifQualified']==0){
							$checked1='';$checked2='checked';
						}
						$materialInfo=$this->kclass->getMaterial(array('materialid'=>$orgItem['materialid'],'itemid'=>$orgItem['materialitemid']));
						$itemList.='<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').' small">
							<td><input type="hidden" name="itemId['.$k.']" value="'.$orgItem['itemid'].'" />
							<input type="hidden" name="itemMaterialid['.$k.']" value="'.$orgItem['materialid'].'" />
							<input type="hidden" name="materialItemid['.$k.']" value="'.$orgItem['materialitemid'].'" />'.$k.'</td>
							<td><a href="'.$materialInfo['url'].'">'.$materialInfo['no'].'</a>　'.$materialInfo['material'].'　'.$materialInfo['standard'].'</td>
							<td>'.$this->kclass->chooserMaterialVersion(array('name'=>'itemVersionid['.$k.']','selectedid'=>$orgItem['versionid'],'width' =>200,'id'=>'itemVersionid'.$k,'materialid'=>$orgItem['materialid'],'itemid'=>$orgItem['materialitemid'])).'</td>
							<td><input type="radio" name="ifBom['.$k.']" value=1 disabled />是 <input type="radio" name="ifBom['.$k.']" value=0 checked disabled />否</td>
							<td><input type="radio" name="ifQualified['.$k.']" value=1 '.$checked1.' />良品 <input type="radio" name="ifQualified['.$k.']" value=0 '.$checked2.' />不良品</td>
							<td><input type="text" name="itemQuantity['.$k.']" size="10" value="'.$orgItem['quantity'].'"></td>
							<td><input type="text" name="itemWastage['.$k.']" size="10" value="'.$orgItem['wastage'].'"></td>
							<td><input type="text" id="outTime'.$k.'" name="itemOutTime['.$k.']" size="10" value="'.date('Y-m-d',$orgItem['outTime']).'"></td>
							<td><input type="text" name="itemRemark['.$k.']" style="width:150px"></td>
							<td><input type="checkbox" name="itemKill['.$k.']" value="'.$orgItem['itemid'].'"/></td>
						</tr>';
					}
					$k++;
				}
			}
			if(($this->kclass->input['typeid']==15 AND $this->kclass->input['orderid']>0) OR ($this->kclass->input['typeid']==16 AND $this->kclass->input['sampleid']>0)){ // 订单或者样品出库
				if($this->kclass->input['orderid']>0){
					$order=$this->kclass->DB->queryFirst("SELECT orderid,orderno FROM `order` WHERE killed=0 AND orderid='".$this->kclass->input['orderid']."'");
					$r='<tr class="odd"><td>订单编号：</td><td><a href="/s.php?module=order&action=view&orderid='.$this->kclass->input['orderid'].'&type=PO">'.$order['orderno'].'</a></td><td></td><td></td></tr>';
					$module='order';$mid=$order['orderid'];
				}elseif($this->kclass->input['sampleid']>0){
					$sample=$this->kclass->DB->queryFirst("SELECT sampleid,sampleno FROM `sample` WHERE killed=0 AND sampleid='".$this->kclass->input['sampleid']."'");
					$r='<tr class="odd"><td>样品单编号：</td><td><a href="/s.php?module=sample&action=view&sampleid='.$this->kclass->input['sampleid'].'">'.$sample['sampleno'].'</a></td><td></td><td></td></tr>';
					$module='sample';$mid=$sample['sampleid'];
				}
				$tr='<tr class="center even" nohover><td width="30">ID</td><td>选择物资 <span class="bold red">*</span></td><td width="50">数量 <span class="red bold">*</span></td><td width="50">出库时间 <span class="red bold">*</span></td><td width="100">备注</td></tr>';
				$items=$this->kclass->DB->query("
					SELECT i.itemid,i.quantity,i.productid,i.productitemid,i.productAttributes,
						pd.title,pd.attrid
					FROM `item` AS i
					LEFT JOIN `product` AS pd ON (pd.productid=i.productid)
					WHERE i.killed=0 AND i.module='".$module."' AND i.mid='".$mid."'
					ORDER BY i.itemid ASC
				");
				if($this->kclass->DB->numRows()){
					$i=1;
					while($item=$this->kclass->DB->fetchArray($items)){
						if($item['attrid']==5){ // 配件包
							$partitems=$this->kclass->DB->query("
								SELECT pi.quantity,pi.childid,p.title
								FROM partitem AS pi 
								LEFT JOIN product AS p ON (p.productid=pi.childid)
								WHERE pi.killed=0 AND pi.productid='".$item['productid']."'
								ORDER BY pi.ordering ASC
							");
							if($this->kclass->DB->numRows()){
								while($partitem=$this->kclass->DB->fetchArray($partitems)){
									$itemList.='<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').' small">
										<td><input type="hidden" name="itemId['.$i.']" value="'.$i.'" />'.$i.'</td>
										<td><input type="hidden" name="productId['.$i.']" value="'.$partitem['childid'].'" />
										<input type="hidden" name="productItemid['.$i.']" value="0" />
										<a href="/s.php?module=product&action=view&productid='.$partitem['childid'].'">'.$partitem['title'].'</a></td>
										<td><input type="text" name="itemQuantity['.$i.']" size="10" value="'.($item['quantity']*$partitem['quantity']).'"></td>
										<td><input type="text" id="outTime'.$i.'" name="itemOutTime['.$i.']" size="10" value="'.date('Y-m-d',TIMENOW).'"></td>
										<td><input type="text" name="itemRemark['.$i.']" style="width:150px"></td>
									</tr>';
									$i++;
								}
							}
						}else{
							$pitem=$this->kclass->getProduct(array('productid'=>$item['productid'],'itemid'=>$item['productitemid']));
							$itemList.='<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').' small">
								<td><input type="hidden" name="itemId['.$i.']" value="'.$i.'" />'.$i.'</td>
								<td><input type="hidden" name="productId['.$i.']" value="'.$item['productid'].'" />
								<input type="hidden" name="productItemid['.$i.']" value="'.$item['productitemid'].'" />
								<a href="/s.php?module=product&action=view&productid='.$item['productid'].'">'.$pitem['title'].'</a>　'.$pitem['standard'].'</td>
								<td><input type="text" name="itemQuantity['.$i.']" size="10" value="'.$item['quantity'].'"></td>
								<td><input type="text" id="outTime'.$i.'" name="itemOutTime['.$i.']" size="10" value="'.date('Y-m-d',TIMENOW).'"></td>
								<td><input type="text" name="itemRemark['.$i.']" style="width:150px"></td>
							</tr>';
							$i++;
						}
					}
				}
			}else{// 其他类型的出库
				$tr='<tr class="center even" nohover><td width="30">ID</td><td>选择物资 <span class="bold red">*</span></td><td width="200">版本 <span class="red bold">*</span></td><td width="110">是否按BOM出库</td><td width="110">是否良品</td><td width="50">数量 <span class="red bold">*</span></td><td>损耗数</td><td width="50">出库时间 <span class="red bold">*</span></td><td width="100">备注</td><td width="15">删</td></tr>';
				$itemList.='<tr><td colspan=10>新增明细</td></tr>';
				for($i=$k;$i<$k+5;$i++){
					$itemList.='<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').' small">
						<td>'.$i.'</td>
						<td>'.$this->kclass->chooserMaterial(array('hName'=>'itemMaterialid['.$i.']','hId'=>'itemMaterialid'.$i,'name'=>'itemMaterial['.$i.']','id'=>'itemMaterial'.$i,'width'=>400,'line'=>$i,'showVersion'=>1)).'</td>
						<td><select name="itemVersionid['.$i.']" id="itemVersionid'.$i.'" style="width:200px;"></select></td>
						<td><input type="radio" name="ifBom['.$i.']" value=1 />是 <input type="radio" name="ifBom['.$i.']" value=0 checked />否</td>
						<td><input type="radio" name="ifQualified['.$i.']" value=1 checked />良品 <input type="radio" name="ifQualified['.$i.']" value=0 />不良品</td>
						<td><input type="text" name="itemQuantity['.$i.']" size="10"></td>
						<td><input type="text" name="itemWastage['.$i.']" size="10" value="0"></td>
						<td><input type="text" id="outTime'.$i.'" name="itemOutTime['.$i.']" size="10" value="'.date('Y-m-d',TIMENOW).'"></td>
						<td><input type="text" name="itemRemark['.$i.']" style="width:150px"></td>
						<td></td>
					</tr>';
					$id.=$this->kclass->iif($i<13,'#outTime'.$i.',','#outTime'.$i);
				}
			}
			$outbound['realtime']=date('Y-m-d',$outbound['realtime']);
			$body=<<<EOF
<form action="/s.php?module=outbound&action=doupdate" name="outbound" method="post" onsubmit="dc.checkSubmit(this)">
<input type="hidden" name="module" value="outbound" />
<input type="hidden" name="action" value="doupdate" />
<input type="hidden" name="outboundid" value="{$this->kclass->input['outboundid']}" />
<input type="hidden" name="departmentid" value="{$this->kclass->input['departmentid']}" />
<input type="hidden" name="typeid" value="{$this->kclass->input['typeid']}">
<input type="hidden" name="noteTypeid" value="{$inType['notetypeid']}">
<input type="hidden" name="orderid" value="{$this->kclass->input['orderid']}" />
<input type="hidden" name="sampleid" value="{$this->kclass->input['sampleid']}" />
<input type="hidden" name="workcenterid" value="{$outbound['workcenterid']}" />
<table class="hundred">
<thead></tr><th colspan="4">基本信息</th></tr></thead>
<tbody>
<tr class="odd">
<td>出库单号：<span class="red bold">*</span></td><td width="700"><input type="text" name="outboundno" value="{$outbound['outboundno']}" id="outboundno" class="middle bold darkred" size="14"></a>　　　　编号生成方式：<input type="radio" name="noType" value="0" >自动<input type="radio" name="noType" value="1" checked>手动 {$departmentSelect}　　　 转月时间：<input type="text" id="realtime" name="realtime" value="{$outbound['realtime']}" size=10></td>
<td>申 请 人：<span class="red bold">*</span></td><td><input type="text" name="applicant" value="{$outbound['applicant']}" />　出库类型：{$inType['title']}</td>
</tr>
{$r}
<tr class="even">
<td>用　　途：</td><td><textarea name="purpose" style="width:444px;height:111px;" >{$outbound['purpose']}</textarea></td>
<td>备　　注：</td><td><textarea name="remark" style="width:444px;height:111px;">{$outbound['remark']}</textarea></td>
</tr>
<tr class="odd">
<td class="small gray" colspan="9">
注意事项：
请保存后，再添加出库单物资批次
</td>
</tr>
</tbody>
</table>
<table class="hundred">
<thead></tr><th colspan="10">出库申请明细</th></tr></thead>
<tbody>
{$tr}
{$orgItemtr}
{$itemList}
<tr class="odd">
<td class="small gray" colspan="10">
注意事项：
如果所列表格不够，那么在提交保存后再点击修改订单来增加物资</td>
</tr>
</tbody>
</table>
<table class="tlist tsuite" align="center" width="100%">
<tbody>
<tr class="even" nohover>
<td class="center"><input type="submit" id="submitButton" value="  提交  " accesskey="s"><input type="reset" value="  复位  "/></td>
</tr>
</tbody>
</table>
EOF;
			}
		}

		$this->kclass->page['title']=$outbound['outboundno'].' - 修改出库单';
		$this->kclass->page['onload']='var dates=$(\'#realtime,'.$id.'\').datepicker({onSelect:function(selectedDate){instance=$(this).data(\'datepicker\'),date=$.datepicker.parseDate(instance.settings.dateFormat || $.datepicker._defaults.dateFormat,selectedDate,instance.settings);dc.updateNo({\'id\':\'outboundno\',\'typeid\':'.$inType['notetypeid'].'});}});dc.tabhover();';
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - <a href="/s.php?module=outbound&action=list">出库列表</a> - <a href="/s.php?module=outbound&action=view&outboundid='.$this->kclass->input['outboundid'].'">查看出库单</a> - 修改出库单', 'right'=>'<a href="/s.php?module=outbound&action=list">返回列表</a>','body'=>$body));
	}
	//
	function doupdate(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			if($this->kclass->input['departmentid']<= 0){
				$e .= '<li>请选择出库申请单中的 部门。</li>';
			}
			if($this->kclass->input['applicant']=='' ){
				$e .= '<li>请填写出库申请单的 申请人。</li>';
			}
			if($this->kclass->input['typeid']=='' ){
				$e .= '<li>请选择出库申请单的 出库类型。</li>';
			}
			$hasItem=0;
			if($this->kclass->input['typeid']==15 OR $this->kclass->input['typeid']==16){ // 订单或者样品单
				$m=count($this->kclass->input['itemId']);
				for($n=1;$n<=$m;$n++){
					if($this->kclass->input['itemQuantity'][$n]>0 AND $this->kclass->input['itemOutTime'][$n]!=''){
						$hasItem=1;
					}
				}
			}else{
				$j=count($this->kclass->input['itemMaterialid']);
				for($i=1;$i<=$j;$i++){
					if($this->kclass->input['itemKill'][$i]=='' AND $this->kclass->input['itemMaterialid'][$i]>0 AND $this->kclass->input['itemVersionid'][$i]>0 AND $this->kclass->input['itemQuantity'][$i]>0 AND $this->kclass->input['itemOutTime'][$i]!=''){
						$hasItem=1;
						$materialInfo=$this->kclass->getMaterial(array('materialid'=>$this->kclass->input['itemMaterialid'][$i],'itemid'=>$this->kclass->input['materialItemid'][$i]));
						if($this->kclass->input['typeid']==9 OR $this->kclass->input['typeid']==11){
							if(!$this->kclass->input['itemId'][$i]){
								$child=$this->kclass->bomChildList(array('parentid'=>$this->kclass->input['itemMaterialid'][$i],'materialitemid'=>$this->kclass->input['materialItemid'][$i],'versionid'=>$this->kclass->input['itemVersionid'][$i],'loopNum'=>1));
								if(!empty($child)){
									foreach($child['bom'] as $key => $val){
										$materialQuantity=$this->kclass->DB->queryFirst("SELECT quantity FROM materialversion WHERE versionid='".$this->kclass->input['itemVersionid'][$i]."' LIMIT 0,1");
										if($val['ifGeneral']==0){
											/*if($val['quantity']>$materialQuantity['quantity']){
												$e.='<li>BOM单中物资'.$val['materialno'].'的数量小于所要求出库的数量</li>';
											}*/
										}
									}
								}else{
									$e.='<li>申请出库物资 <b>'.$materialInfo['no'].'</b> 不存在BOM。请查看</li>';
								}
							}
						}else{
							$materialQuantity=$this->kclass->DB->queryFirst("SELECT quantity FROM materialversion WHERE versionid='".$this->kclass->input['itemVersionid'][$i]."' LIMIT 0,1");
							if($this->kclass->input['ifBom'][$i]){
								$child=$this->kclass->bomChildList(array('parentid'=>$this->kclass->input['itemMaterialid'][$i],'materialitemid'=>$this->kclass->input['materialItemid'][$i],'versionid'=>$this->kclass->input['itemVersionid'][$i],'loopNum'=>1));
								if(!empty($child)){
									foreach($child['bom'] as $key => $val){
										$materialQuantity=$this->kclass->DB->queryFirst("SELECT quantity FROM materialversion WHERE versionid='".$this->kclass->input['itemVersionid'][$i]."' LIMIT 0,1");
										if($val['ifGeneral']==0){
											/*if($val['quantity']>$materialQuantity['quantity']){
												$e.='<li>BOM单中物资'.$val['materialno'].'的数量小于所要求出库的数量</li>';
											}*/
										}
									}
								}else{
									$e.='<li>申请出库物资 <b>'.$materialInfo['no'].'</b> 不存在BOM。请查看</li>';
								}
							}
							/*if($this->kclass->input['itemQuantity'][$i]>$materialQuantity['quantity']){
								$e.='<li>物资'.$material['materialno'].'的数量小于所要求出库的数量</li>';
							}*/
						}
					}
				}
			}
			if($hasItem==0){
				$e.='<li>需要填写至少有一条出库明细，才能建立入库申请单。</li>';
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
		$orderid=$this->kclass->iif($this->kclass->input['orderid'],$this->kclass->input['orderid'],0);
		$supplierid=$this->kclass->iif($this->kclass->input['supplierid'],$this->kclass->input['supplierid'],0);
		$sampleid=$this->kclass->iif($this->kclass->input['sampleid']>0,$this->kclass->input['sampleid']>0,0);
		$outbound=$this->kclass->DB->queryFirst("SELECT * FROM outbound WHERE outboundid='".$this->kclass->input['outboundid']."'");
		$sd=explode('-', $this->kclass->input['realtime']);
		$realtime=mktime(0,0,0,$sd[1],$sd[2],$sd[0]);
		if($this->kclass->input['noType']==1){
			$outboundno=$this->kclass->input['outboundno'];
		}elseif($this->kclass->input['noType']==0){
			$outboundno=$this->kclass->id(array('outbound'=>$realtime,'typeid'=>$this->kclass->input['noteTypeid']));
		}
		$this->kclass->DB->query("
			UPDATE outbound SET 
				outboundno='{$outboundno}',
				typeid='".$this->kclass->input['typeid']."',
				noteTypeid='".$this->kclass->input['noteTypeid']."',
				orderid='".$orderid."',
				sampleid='".$sampleid."',
				supplierid='".$supplierid."',
				oldoutno='".$this->kclass->input['oldoutno']."',
				applicant='".$this->kclass->input['applicant']."',
				purpose='".$this->kclass->input['purpose']."',
				remark='".$this->kclass->input['remark']."',
				ifConfirm=0,confirmer=0,confirmed=0,
				ifVerify=0,verified=0,verifier=0,verifyRemark='',
				ifComplete=1,
				modified='".TIMENOW."',
				modifier='".$this->kclass->user['userid']."'
			WHERE outboundid='".$this->kclass->input['outboundid']."'
		");
		$j=count($this->kclass->input['itemMaterialid']);
		for($i=1;$i<=$j;$i++){
			if(($this->kclass->input['itemMaterialid'][$i]>0 OR $this->kclass->input['productItemid'][$i]>0) AND $this->kclass->input['itemVersionid'][$i]>0 AND $this->kclass->input['itemQuantity'][$i]>0 AND $this->kclass->input['itemOutTime'][$i]!=''){
				$sd=explode('-', $this->kclass->input['itemOutTime'][$i]);
				$outTime=mktime(0,0,0,$sd[1],$sd[2],$sd[0]);
				$wastage=$this->kclass->iif(floatval($this->kclass->input['itemWastage'][$i])>0,floatval($this->kclass->input['itemWastage'][$i]),0);
				$materialid=$this->kclass->iif($this->kclass->input['itemMaterialid'][$i]>0,$this->kclass->input['itemMaterialid'][$i],0);
				$materialitemid=$this->kclass->iif($this->kclass->input['materialItemid'][$i]>0,$this->kclass->input['materialItemid'][$i],0);
				$ifQualified=$this->kclass->iif(isset($this->kclass->input['ifQualified'][$i]),$this->kclass->input['ifQualified'][$i],1);
				$ifBom=$this->kclass->iif(isset($this->kclass->input['ifBom'][$i]),$this->kclass->input['ifBom'][$i],0);
				if($materialid>0){
					$material=$this->kclass->DB->queryFirst("
						SELECT materialid,unitid,quantity,materialno
						FROM material 
						WHERE materialid='".$materialid."'
						LIMIT 0,1
					");
				}
				if($this->kclass->input['itemId'][$i]>0){
					if($this->kclass->input['itemKill'][$i]>0 AND $this->kclass->input['itemKill'][$i]==$this->kclass->input['itemId'][$i]){
						$this->kclass->DB->query("UPDATE preoutbounditem SET killed='".TIMENOW."',killer='".$this->kclass->user['userid']."' WHERE itemid='".$this->kclass->input['itemKill'][$i]."'");
					}else{
						$this->kclass->DB->query("
							UPDATE preoutbounditem SET
								materialid='".$materialid."',
								versionid='".$this->kclass->input['itemVersionid'][$i]."',
								materialitemid='".$materialitemid."',
								ifQualified='".$ifQualified."',
								quantity='".$this->kclass->input['itemQuantity'][$i]."',
								wastage='".$wastage."',
								unitid='".$material['unitid']."',
								outTime='".$outTime."',
								remark='".$this->kclass->input['itemRemark'][$i]."',
								modified='".TIMENOW."',
								modifier='".$this->kclass->user['userid']."'
							WHERE itemid='".$this->kclass->input['itemId'][$i]."'
						");
					}
				}else{
					if($this->kclass->input['typeid']==9 OR $this->kclass->input['typeid']==11){
						$child=$this->kclass->bomChildList(array('parentid'=>$materialid,'materialitemid'=>$materialitemid,'versionid'=>$this->kclass->input['itemVersionid'][$i],'quantity'=>$this->kclass->input['itemQuantity'][$i],'loopNum'=>1));
						if(!empty($child)){
							foreach($child['bom'] as $key => $val){
								$this->kclass->DB->query("
									INSERT preoutbounditem
										(module,mid,materialid,versionid,materialitemid,ifQualified,quantity,outTime,remark,created,creator)
									VALUES ('outbound','".$this->kclass->input['outboundid']."','".$val['materialid']."','".$val['versionid']."','".$val['materialitemid']."',1,'".$val['quantity']."','".$outTime."','".$this->kclass->input['itemRemark'][$i]."','".TIMENOW."','".$this->kclass->user['userid']."')
								");
							}
						}
					}else{
						if($ifBom==1){
							// bom方式 暂时不加入 损耗数
							$child=$this->kclass->bomChildList(array('parentid'=>$materialid,'materialitemid'=>$materialitemid,'versionid'=>$this->kclass->input['itemVersionid'][$i],'quantity'=>$this->kclass->input['itemQuantity'][$i],'loopNum'=>1));
							if(!empty($child)){
								foreach($child['bom'] as $val){
									if($val['ifGeneral']==0){
										$this->kclass->DB->query("
											INSERT preoutbounditem
												(module,mid,materialid,versionid,materialitemid,ifQualified,quantity,outTime,remark,created,creator)
											VALUES ('outbound','".$this->kclass->input['outboundid']."','".$val['materialid']."','".$val['versionid']."','".$val['materialitemid']."',1,'".$val['quantity']."','".$outTime."','".$this->kclass->input['itemRemark'][$i]."','".TIMENOW."','".$this->kclass->user['userid']."')
										");
									}
								}
							}
						}else{
							$this->kclass->DB->query("
								INSERT preoutbounditem (module,mid,materialid,versionid,materialitemid,ifQualified,quantity,wastage,unitid,outTime,remark,created,creator)
								VALUES ('outbound','".$this->kclass->input['outboundid']."','".$materialid."','".$this->kclass->input['itemVersionid'][$i]."','".$materialitemid."','".$ifQualified."','".$this->kclass->input['itemQuantity'][$i]."','".$wastage."','".$this->kclass->iif($material['unitid']>0,$material['unitid'],0)."','".$outTime."','".$this->kclass->input['itemRemark'][$i]."','".TIMENOW."','".$this->kclass->user['userid']."')
							");
						}
					}
				}
			}
		}

		$this->kclass->messager(array(
			'title' => '修改出库单',
			'text' => '出库单 【<b>'.$outboundno.'</b>】 已修改成功!返回添加出库物资页面',
			'url' => '/s.php?module=outbound&action=view&outboundid='.$this->kclass->input['outboundid'],
			'sec' => 2
		));
	}
	//
	function out(){
		$outbound=$this->kclass->DB->queryFirst("SELECT outboundid,outboundno,orderid,sampleid,supplierid,typeid FROM outbound WHERE killed=0 AND ifComplete<>2 AND outboundid='".$this->kclass->input['outboundid']."' LIMIT 0,1");
		if(!$outbound){
			$e='<li>数据错误，很抱歉~</li>';
		}else{
			$item=$this->kclass->DB->queryFirst("
				SELECT pi.itemid,pi.materialid,pi.materialitemid,pi.productid,pi.productitemid,pi.versionid,pi.ifQualified,pi.quantity,pi.wastage,pi.outTime,pi.remark,
					mv.title AS version,mv.qualified AS store1,mv.disqualified AS store2,mv.`change`
				FROM preoutbounditem AS pi
				LEFT JOIN materialversion AS mv ON (mv.versionid=pi.versionid)
				WHERE pi.killed=0 AND pi.module='outbound' AND pi.mid='".$outbound['outboundid']."' AND pi.itemid='".$this->kclass->input['itemid']."'
				LIMIT 0,1
			");
			$materialInfo=$this->kclass->getMaterial(array('materialid'=>$item['materialid'],'itemid'=>$item['materialitemid']));
			if($outbound['typeid']!=15 AND $outbound['typeid']!=16){
				$barcode=$this->kclass->DB->queryFirst("SELECT COUNT(*) AS count FROM barcode WHERE scraped=0 AND materialid='".$item['materialid']."' AND versionid=".$item['versionid']." LIMIT 0,1");
				if($barcode['count']==0){
					$e.='<li>该物资暂无批次</li>';
				}
			}else{
				if($outbound['orderid']>0){
					$inbound=$this->kclass->DB->queryFirst("SELECT ifComplete FROM inbound WHERE killed=0 AND orderid='".$outbound['orderid']."' ORDER BY created DESC LIMIT 0,1");
					if($inbound['ifComplete']!=2){
						$e.='<li>该订单还未入库</li>';
					}
				}elseif($outbound['sampleid']>0){
					$inbound=$this->kclass->DB->queryFirst("SELECT ifComplete FROM inbound WHERE killed=0 AND sampleid='".$outbound['sampleid']."' ORDER BY created DESC LIMIT 0,1");
					if($inbound['ifComplete']!=2){
						$e.='<li>该样品单还未入库</li>';
					}
				}
			}

			$preQuantity=$item['quantity']+$item['wastage'];
			// 已经出库数量
			$outItem=$this->kclass->DB->queryFirst("SELECT SUM(outQuantity) AS quantity FROM outbounditem WHERE killed=0 AND preitemid='".$item['itemid']."' LIMIT 0,1");
			if($outItem['quantity']>=$preQuantity){
				$e.='<li>该物资已经出库</li>';
			}
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '新建出库的物资',
				'text' => '新建出库的物资<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$quantity=$this->kclass->iif($outItem['quantity']>0,$preQuantity-$outItem['quantity'],$preQuantity);
		$barcodeSelect=$condition='';
		if($outbound['typeid']==15){ //订单出库
			$store=0;
			$inItems=$this->kclass->DB->query("
				SELECT ii.quantity,ii.barcodeid,b.barcode,b.arrivalTime
				FROM inbounditem AS ii
				LEFT JOIN barcode AS b ON (ii.barcodeid=b.barcodeid)
				LEFT JOIN preinbounditem AS pi ON (ii.preitemid=pi.itemid) 
				LEFT JOIN inbound AS i ON (i.inboundid=ii.inboundid)
				WHERE i.killed=0 AND pi.killed=0 AND pi.module='inbound' AND i.ifVerify=1 AND  ii.orderid=".$outbound['orderid']."  AND pi.productid='".$item['productid']."' AND pi.productitemid='".$item['productitemid']."'
				ORDER BY ii.created DESC
			");
			$barcodeSelect.='<select id="barcodeid" name="barcodeid" style="width:550px"><option value=0></option>';
			if($this->kclass->DB->numRows()){
				while($inItem=$this->kclass->DB->fetchArray($inItems)){
					$barcodeSelect.='<option value="'.$inItem['barcodeid'].'" selected>'.$inItem['barcode'].' ( 批次时间：'.date('Y-m-d',$inItem['arrivalTime']).' ) 库存：'.$inItem['quantity'].'</option>';
					$store+=$inItem['quantity'];
				}
			}
			$barcodeSelect.='</select>';
			$product=$this->kclass->getProduct(array('productid'=>$item['productid'],'itemid'=>$item['productitemid']));
			$material='<a href="/s.php?module=product&cation=view&productid='.$item['productid'].'">'.$product['title'].'</a>　'.$product['standard'];
		}elseif($outbound['typeid']==16){ //样品单出库
			$store=0;
			$preInitems=$this->kclass->DB->query("
				SELECT pi.itemid
				FROM preinbounditem AS pi
				LEFT JOIN inbound AS i ON (pi.mid=i.inboundid)
				WHERE i.killed=0 AND pi.killed=0 AND pi.module='inbound' AND i.ifVerify=1 AND i.sampleid='".$outbound['sampleid']."' AND productid='".$item['productid']."' AND productitemid='".$item['productitemid']."'
				ORDER BY pi.created ASC 
			");
			if($this->kclass->DB->numRows()){
				$barcodeSelect.='<select id="barcodeid" name="barcodeid" style="width:550px"><option value=0></option>';
				while($preInitem=$this->kclass->DB->fetchArray($preInitems)){
					$inItem=$this->kclass->DB->queryFirst("
						SELECT ii.quantity,ii.barcodeid,b.barcode,b.arrivalTime
						FROM inbounditem AS ii
						LEFT JOIN barcode AS b ON (ii.barcodeid=b.barcodeid)
						WHERE killed=0 AND preitemid=".$preInitem['itemid']." 
						ORDER BY ii.created DESC
						LIMIT 0,1
					");
					$barcodeSelect.='<option value="'.$inItem['barcodeid'].'" selected>'.$inItem['barcode'].' ( 批次时间：'.date('Y-m-d',$inItem['arrivalTime']).' ) 库存：'.$inItem['quantity'].'</option>';
					$store+=$inItem['quantity'];
				}
				$barcodeSelect.='</select>';
			}
			$product=$this->kclass->getProduct(array('productid'=>$item['productid'],'itemid'=>$item['productitemid']));
			$material='<a href="/s.php?module=product&cation=view&productid='.$item['productid'].'">'.$product['title'].'</a>　'.$product['standard'];
		}else{ // 其他类型出库
			$material='<a href="'.$materialInfo['url'].'">'.$materialInfo['no'].'</a>　'.$materialInfo['material'].'　'.$materialInfo['standard'].'　　版本：'.$item['version'];
			$barcodeSelect=$this->kclass->chooserBarcode(array('name'=>'barcodeid','condition'=>'materialid='.$item['materialid'].' ','versionid'=>$item['versionid'],'width'=>550,'ifQualified'=>$item['ifQualified'],'ifMinus'=>1));
			if($item['ifQualified']==1){
				$qualifiedType='良品';
				$store=$item['store1'];
			}elseif($item['ifQualified']==0){
				$qualifiedType='不良品';
				$store=$item['store2'];
			}
		}

		$item['outTime']=date('Y-m-d',$item['outTime']);
		$nowTime=date('Y-m-d',TIMENOW);

		/* 暂时保留
		for($i=1;$i<=5;$i++){
			$stocks=$this->kclass->DB->query("
				SELECT si.stockid,si.quantity,
					s.stockno,s.ifEmpty
				FROM stockitem AS si
				LEFT JOIN stock AS s ON (s.stockid=si.stockid)
				WHERE si.killed=0 AND quantity<>0 AND si.materialid='".$this->kclass->input['materialid']."'
				ORDER BY si.created ASC
			");
			if($this->kclass->DB->numRows()){
				$stockList='<select name="stockid['.$i.']"><option value=0>请选择</option>';
				while($stock=$this->kclass->DB->fetchArray($stocks)){
					$ifEmpty=$this->kclass->iif($stock['ifEmpty']==1,'（已满）','');
					$ifEmptyColor=$this->kclass->iif($stock['ifEmpty']==1,'class="gray"','');
					$stockList.='<option value="'.$stock['stockid'].'" '.$ifEmptyColor.'>'.$stock['stockno'].'（剩余数量：'.$stock['quantity'].'）</option>';
				}
				$stockList.='</select>';
			}
			$stocktr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').' center">
				<td>'.$stockList.'</td>
				<td><input type="text" name="quantity['.$i.']"></td>
				<td><input type="radio" name="ifEmpty['.$i.']" value="0" checked />空闲 <input type="radio" name="ifEmpty['.$i.']" value="1" />不空闲</td>
			</tr>';
		}
		<table>
<thead></tr><th colspan="4">添加出库明细</th></tr></thead>
<tbody>
<tr class="odd">
<td width="200" class="bold center">选择库位<span class="red bold">*</span></td><td>出库数量<span class="red bold">*</span></td><td>是否空闲：<span class="red bold">*</span></td>
</tr>
{$stocktr}
</tbody>
</table>
		*/
$body=<<<EOF
<form action="/s.php?module=outbound&action=doout" name="outbound" method="post" onsubmit="dc.checkSubmit(this)">
<input type="hidden" name="module" value="outbound" />
<input type="hidden" name="action" value="doout" />
<input type="hidden" name="outboundid" value="{$this->kclass->input['outboundid']}" />
<input type="hidden" name="materialid" value="{$item['materialid']}" />
<input type="hidden" name="ifQualified" value="{$item['ifQualified']}" />
<input type="hidden" name="itemid" value="{$this->kclass->input['itemid']}" />
<table class="hundred">
<thead></tr><th colspan="4">出库信息</th></tr></thead>
<tbody>
<tr class="even">
<td>物　　资：</td><td>{$material}</td><td>库 存 数：</td><td>{$store}</td>
</tr>
<tr class="odd">
<td width="120">批次条码：<span class="red bold">*</span></td><td>{$barcodeSelect}</td>
<td width="120"></td><td></td>
</tr>
<tr class="even">
<td>申请出库时间：<span class="red bold">*</span></td><td><input type="text" name="applyTime" value="{$item['outTime']}" readonly /></td>
<td>实际出库时间：<span class="red bold">*</span></td><td><input type="text" name="actualTime" value="{$nowTime}"  id="date" /></td>
</tr>
<tr class="odd">
<td>申请数量：<span class="red bold">*</span></td><td><input type="text" name="applyQuantity" value="{$preQuantity}" readonly/></td>
<td>实际数量：<span class="red bold">*</span></td><td><input type="text" name="outQuantity" value="{$quantity}" /></td>
</tr>
<tr class="even">
<td>备　　注：</td><td width="40%"><textarea type="text" name="remark" value="" style="width:444px;height:111px;" >{$item['remark']}</textarea></td>
<td></td><td></td>
</tr>
<tr class="odd">
<td class="small gray" colspan="4">注意事项：确认信息正确无误</td>
</tr>
</tbody>
</table>
<table class="tlist tsuite" align="center" width="100%">
<tbody>
<tr class="odd" nohover>
<td class="center"><input type="submit" id="submitButton" value="  提交  " accesskey="s"><input type="reset" value="  复位  "/></td>
</tr>
</tbody>
</table>
</form>
EOF;
		$this->kclass->tbline+=4;
		$this->kclass->page['title'].='出库';
		$this->kclass->page['onload'].='dc.tabhover();$(\'#date\').datepicker()';
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - <a href="/s.php?module=outbound&action=view&outboundid='.$this->kclass->input['outboundid'].'">查看出库单</a> - 出库', 'right'=>'<a href="/s.php?module=outbound&action=view&outboundid='.$this->kclass->input['outboundid'].'">查看出库单</a>','body'=>$body));
	}
	//
	function doout(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			if($this->kclass->input['barcodeid']<=0){
				$e.='<li>请选择出库物资的 条码。</li>';
			}
			if($this->kclass->input['actualTime']==''){
				$e.='<li>请输入出库物资的 实际出库时间。</li>';
			}
			if($this->kclass->input['applyQuantity']==''){
				$e.='<li>请输入出库物资的 申请出库数量。</li>';
			}
			if($this->kclass->input['outQuantity']==''){
				$e.='<li>请输入出库物资的 实际出库数量。</li>';
			}
			$item=$this->kclass->DB->queryFirst("
				SELECT pi.itemid,pi.materialid,pi.productitemid,pi.versionid,pi.quantity,pi.wastage,pi.unitid,pi.outTime
				FROM preoutbounditem  AS pi
				WHERE pi.killed=0 AND pi.module='outbound' AND pi.mid='".$this->kclass->input['outboundid']."' AND pi.itemid='".$this->kclass->input['itemid']."'
				LIMIT 0,1
			");
			$barcode=$this->kclass->DB->queryFirst("SELECT barcodeid,materialid,batchid,quantity,qualified,disqualified,versionid FROM barcode WHERE barcodeid='".$this->kclass->input['barcodeid']."' LIMIT 0,1");
			if($this->kclass->input['ifQualified']==1){
				$store=$barcode['qualified'];
			}elseif($this->kclass->input['ifQualified']==0){
				$store=$barcode['disqualified'];
			}

		//	if($this->kclass->input['outQuantity']>$store){
			//	$e.='<li>出库数量大于该批次库存数。</li>';
			//}
			// 已经出库数量
			$outItem=$this->kclass->DB->queryFirst("SELECT SUM(outQuantity) AS quantity FROM outbounditem WHERE killed=0 AND preitemid='".$item['itemid']."' LIMIT 0,1");
			if($outItem['quantity']>=($item['quantity']+$item['wastage'])){
				$e.='<li>该物资已经出库</li>';
			}
			/* 选择库位验证。暂时保留
			$hasItem=0;
			$j=count($this->kclass->input['stockid']);
			for($i=1;$i<$j+1;$i++){
				if($this->kclass->input['stockid'][$i]>0 AND $this->kclass->input['quantity'][$i]!=''){
					$hasItem=1;
				}
			}
			if($hasItem==0){
				$e .= '<li>需要选择至少一个库位及出库数量。</li>';
			}*/
		}
		if($e){
			$this->kclass->messager(array(
				'title' => '新建订单',
				'text' => '您在新建订单的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$outbound=$this->kclass->DB->queryFirst("SELECT outboundid,outboundno,orderid,sampleid FROM outbound WHERE outboundid='".$this->kclass->input['outboundid']."'");
		$this->kclass->input['applyTime']=strtotime($this->kclass->input['applyTime']);
		$sd=explode('-', $this->kclass->input['actualTime']);
		$actualTime=mktime(0,0,0,$sd[1],$sd[2],$sd[0]);
		/* 库位出库，暂时保留
		for($m=1;$m<$j+1;$m++){
			if($this->kclass->input['stockid'][$m]>0 AND $this->kclass->input['quantity'][$m]!=''){
				$this->kclass->DB->query("UPDATE stockitem SET quantity=quantity-'".$this->kclass->input['quantity'][$m]."' WHERE batchid='".$this->kclass->input['batchid']."' AND stockid='".$this->kclass->input['stockid'][$m]."'");
				$this->kclass->DB->query("UPDATE stock SET ifEmpty='".$this->kclass->input['ifEmpty'][$m]."' WHERE killed=0 AND stockid='".$this->kclass->input['stockid'][$m]."'");
				$stock[]=array('stockid'=>$this->kclass->input['stockid'][$m],'quantity'=>$this->kclass->input['quantity'][$m]);
			}
		}
		$stocks=serialize($stock);
		*/
		$version=$this->kclass->DB->queryFirst("SELECT itemid,quantity,qualified,disqualified FROM materialversion WHERE killed=0 AND versionid='".$barcode['versionid']."'");
		$stocks='';
		//出库
		$this->kclass->DB->query("
			INSERT INTO outbounditem (batchid,barcodeid,outboundid,materialid,materialitemid,versionid,preitemid,applyQuantity,outQuantity,applyTime,actualTime,stocks,remark,created,creator)
			VALUES ('".$barcode['batchid']."','".$this->kclass->input['barcodeid']."','".$this->kclass->input['outboundid']."','".$barcode['materialid']."','".$version['itemid']."','".$barcode['versionid']."','".$this->kclass->input['itemid']."','".$this->kclass->input['applyQuantity']."','".$this->kclass->input['outQuantity']."','".$item['outTime']."','".$actualTime."','".$stocks."','".$this->kclass->input['remark']."','".TIMENOW."','".$this->kclass->user['userid']."')
		");
		$itemid=$this->kclass->DB->insertID();
		//减去该物资总数及批次的数量
		if($this->kclass->input['ifQualified']==1){
			$outQualified=$this->kclass->input['outQuantity'];$outDisqualified=0;
			$changeQuantity='qualified=qualified-'.$this->kclass->input['outQuantity'];
		}elseif($this->kclass->input['ifQualified']==0){
			$outQualified=0;$outDisqualified=$this->kclass->input['outQuantity'];
			$changeQuantity='disqualified=disqualified-'.$this->kclass->input['outQuantity'];
		}

		//批次数量
		$this->kclass->DB->query("UPDATE batch SET quantity=quantity-'".$this->kclass->input['outQuantity']."',".$changeQuantity." WHERE batchid='".$barcode['batchid']."'");
		//条码数量
		$this->kclass->DB->query("UPDATE barcode SET quantity=quantity-'".$this->kclass->input['outQuantity']."',".$changeQuantity." WHERE barcodeid='".$this->kclass->input['barcodeid']."'");
		//版本数量
		$this->kclass->DB->query("UPDATE materialversion SET quantity=quantity-'".$this->kclass->input['outQuantity']."',".$changeQuantity." WHERE versionid='".$barcode['versionid']."'");
		//物资数量
		$this->kclass->DB->query("UPDATE material SET quantity=quantity-'".$this->kclass->input['outQuantity']."',".$changeQuantity." WHERE materialid='".$barcode['materialid']."'");
		/*// 出库后，清查批次数量
		$afterOutBatch=$this->kclass->DB->queryFirst("SELECT quantity FROM batch WHERE batchid='".$barcode['batchid']."'");
		if($afterOutBatch['quantity']==0){ // 批次数量为0的时候，删除
			$this->kclass->DB->query("UPDATE batch SET killed='".TIMENOW."',killer='".$this->kclass->user['userid']."' WHERE batchid='".$barcode['batchid']."'");
		}
		$afterOutBarcode=$this->kclass->DB->queryFirst("SELECT quantity FROM barcode WHERE barcodeid='".$this->kclass->input['barcodeid']."'");
		if($afterOutBarcode['quantity']==0){
			$this->kclass->DB->query("UPDATE barcode SET scraped='".TIMENOW."',scraper='".$this->kclass->user['userid']."' WHERE barcodeid='".$this->kclass->input['barcodeid']."'");
		}*/
		// 产品明细数量
		if($version['itemid']>0){
			$this->kclass->DB->query("UPDATE materialitem SET quantity=quantity-'".$this->kclass->input['outQuantity']."',".$changeQuantity." WHERE itemid='".$version['itemid']."'");
		}

		// 物料收发卡(出)
		$this->kclass->DB->query("
			INSERT inoutrecord (`module`,`mid`,`materialid`,`materialitemid`,`versionid`,`itemid`,`addition`,`qualified`,`balance`,`disqualified`,`disbalance`,`dateline`,`created`,`creator`)
			VALUE ('outbound','".$this->kclass->input['outboundid']."','".$barcode['materialid']."','".$version['itemid']."','".$barcode['versionid']."','".$itemid."','-','".$outQualified."','".($version['qualified']-$outQualified)."','".$outDisqualified."','".($version['disqualified']-$outDisqualified)."','".TIMENOW."','".TIMENOW."','".$this->kclass->user['userid']."')
		");// 判断出库单出库的状态
		$inStatus='';
		$preout=$this->kclass->DB->queryFirst("
			SELECT SUM(quantity) AS count,SUM(wastage) AS wastage
			FROM preoutbounditem
			WHERE killed=0 AND module='outbound' AND mid='".$this->kclass->input['outboundid']."'
			LIMIT 0,1
		");
		$preQuantity=$preout['count']+$preout['wastage'];
		$out=$this->kclass->DB->queryFirst("
			SELECT SUM(outQuantity) AS count
			FROM outbounditem
			WHERE killed=0 AND outboundid='".$this->kclass->input['outboundid']."'
			LIMIT 0,1
		");
		if($preQuantity>$out['count'] AND $out['count']>0){
			$inStatus='部分出库';
			$inShow=1;
			$this->kclass->DB->query("UPDATE outbound SET ifComplete=1 WHERE outboundid='".$this->kclass->input['outboundid']."'");
		}elseif($preQuantity==$out['count']){
			$inStatus='出库完成';
			$inShow=2;
			$items=$this->kclass->DB->query("
				SELECT i.outQuantity,
					pi.versionid,pi.taskitemid,
					pm.ifGeneral
				FROM outbounditem AS i
				LEFT JOIN preoutbounditem AS pi ON (pi.itemid=i.preitemid)
				LEFT JOIN material AS m ON (m.materialid=i.materialid)
				LEFT JOIN pmaterial AS pm ON (pm.materialid=m.materialid)
				WHERE i.killed=0 AND i.outboundid='".$this->kclass->input['outboundid']."'
			");
			if($this->kclass->DB->numRows()){
				while($item=$this->kclass->DB->fetchArray($items)){
					if($item['ifGeneral']==0 AND $item['taskitemid']>0){
						$itemVersion=$this->kclass->DB->queryFirst("SELECT planOutbound FROM materialversion WHERE versionid='".$item['versionid']."'");
						if($itemVersion['planOutbound']-$item['outQuantity']<0){
							$planOutbound=0;
						}else{
							$planOutbound=$itemVersion['planOutbound']-$item['outQuantity'];
						}
						$this->kclass->DB->query("UPDATE materialversion SET planOutbound='".$planOutbound."' WHERE versionid='".$item['versionid']."'");
					}
				}
			}
			$this->kclass->DB->query("UPDATE outbound SET ifComplete=2 WHERE outboundid='".$this->kclass->input['outboundid']."'");
			
			if($outbound['orderid']>0){  // 如果是订单出库，更改状态
				$this->kclass->DB->query("UPDATE `order` SET ifOutbound=2 WHERE orderid='".$outbound['orderid']."'");
			}elseif($outbound['sampleid']>0){ // 如果是样品出库，更改状态
				$this->kclass->DB->query("UPDATE `sample` SET ifOutbound=2 WHERE sampleid='".$outbound['sampleid']."'");
			}
		}elseif(!$out['count']){
			$inStatus='等待出库';
			$inShow=0;
		}

		$this->kclass->messager(array(
			'title' => '添加出库物资批次',
			'text' => '出库物资批次 已添加成功!返回添加查看出库单',
			'url' => '/s.php?module=outbound&action=view&outboundid='.$this->kclass->input['outboundid'],
			'sec' => 2
		));
	}
	// 单个出库
	function perout(){
		$outbound=$this->kclass->DB->queryFirst("SELECT outboundid,typeid,outboundno,orderid,supplierid FROM outbound WHERE killed=0 AND outboundid='".$this->kclass->input['outboundid']."' LIMIT 0,1");
		if(!$outbound){
			$e='<li>数据错误，很抱歉~</li>';
		}else{
			$item=$this->kclass->DB->queryFirst("
				SELECT pi.itemid,pi.materialid,pi.version,pi.quantity,pi.unitid,pi.outTime,pi.remark,
					m.materialno,m.title,m.standard,m.ifPerBarcode
				FROM preoutbounditem AS pi
				LEFT JOIN material AS m ON (m.materialid=pi.materialid)
				WHERE pi.killed=0 AND pi.module='outbound' AND pi.mid='".$outbound['outboundid']."' AND pi.itemid='".$this->kclass->input['itemid']."'
				LIMIT 0,1
			");
			if(!$item){
				$e='<li>数据错误，很抱歉~</li>';
			}
			$batch=$this->kclass->DB->queryFirst("SELECT COUNT(*) AS count FROM batch WHERE killed=0 AND materialid='".$item['materialid']."'");
			if($batch['count']==0){
				$e.='<li>该物资暂无批次</li>';
			}
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '新建出库的物资',
				'text' => '新建出库的物资<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}

		$material=$this->kclass->chooserMaterial(array('name'=>'materialid','hasBlank'=>1,'width'=>200,'topname'=>'新物品','selectedid'=>$item['materialid']));
		$order=$this->kclass->chooserOrder(array('name'=>'orderid','module'=>'order','hasBlank'=>1,'width'=>200,'topname'=>'','selectedid'=>$outbound['orderid']));
		$supplier=$this->kclass->chooserSupplier(array('name'=>'supplierid','hasBlank'=>1,'width'=>200,'topname'=>'','selectedid'=>$outbound['supplierid']));
		$unit=$this->kclass->chooserUnit(array('name'=>'unitid','hasBlank'=>1,'width'=>200,'topname'=>'','selectedid'=>$item['unitid']));
		$batch=$this->kclass->chooserBatch(array('name'=>'batchid','hasBlank'=>1,'topname'=>'','materialid'=>$item['materialid']));
		$barcode=$this->kclass->chooserBarcode(array('name'=>'barcodeid','hasBlank'=>1,'ifPerBarcode'=>$item['ifPerBarcode'],'topname'=>'请选择','status'=>'statusid=2','condition'=>'materialid='.$item['materialid'],'width'=>450));
		$item['outTime']=date('Y-n-d',$item['outTime']);
		for($i=1;$i<=5;$i++){
			$stocks=$this->kclass->DB->query("
				SELECT si.stockid,si.quantity,si.barcodeid,
					s.stockno
				FROM stockitem AS si
				LEFT JOIN stock AS s ON (s.stockid=si.stockid)
				WHERE si.killed=0 AND quantity<>0 AND si.materialid='".$item['materialid']."'
				ORDER BY si.created ASC
			");
			$stockList='<select name="stockid['.$i.']"><option value=0>请选择</option>';
			if($this->kclass->DB->numRows()){
				while($stock=$this->kclass->DB->fetchArray($stocks)){
					$stockList.='<option value="'.$stock['stockid'].'">'.$stock['stockno'].'（剩余数量：'.$stock['quantity'].'）</option>';
					if($item['ifPerBarcode']==1){
						$stockBarcodes=$this->kclass->DB->query("
							SELECT barcodeid,barcode
							FROM barcode
							WHERE scraped=0 AND barcodeid='".$stock['barcodeid']."'
							ORDER BY barcodeid ASC
						");
						if($this->kclass->DB->numRows()){
							while($stockBarcode=$this->kclass->DB->fetchArray($stockBarcodes)){
								$stockList.='<option value="'.$stock['stockid'].'" class="gray">　　'.$stockBarcode['barcode'].'</option>';
							}
						}
					}

				}
			}else{
				$stockList.='<option>库中暂无此物资</option>';
			}
			$stockList.='</select>';
			$stocktr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').' center">
				<td>'.$stockList.'</td>
				<td><input type="text" name="quantity['.$i.']"></td>
			</tr>';
		}

$body=<<<EOF
<form action="/s.php?module=outbound&action=doperout" name="outbound" method="post" onsubmit="dc.checkSubmit(this)">
<input type="hidden" name="module" value="outbound" />
<input type="hidden" name="action" value="doperout" />
<input type="hidden" name="outboundid" value="{$this->kclass->input['outboundid']}" />
<input type="hidden" name="materialid" value="{$item['materialid']}" />
<input type="hidden" name="typeid" value="{$outbound['typeid']}" />
<table class="hundred">
<thead></tr><th colspan="4">添加批次信息</th></tr></thead>
<tbody>
<tr class="odd">
<td width="120">选择批次：<span class="red bold">*</span></td><td>{$batch}</td>
<td width="120">批次条码：<span class="red bold">*</span></td><td>{$barcode}</td>
</tr>
<tr class="even">
<td>物资编号：</td><td><a href="/s.php?module=material&action=view&materialid={$item['materialid']}">{$item['materialno']}</a></td>
<td>物资名称：</td><td>{$item['title']}</td>
</tr>
<tr class="odd">
<td>物资规格：</td><td>{$item['standard']}</td>
<td>物资版本号：<span class="red bold">*</span></td><td><input type="text" name="version" value="A01" /><span class="small gray">（没有版本则默认为A01）</span></td>
</tr>
<tr class="even">
<td>申请出库时间：<span class="red bold">*</span></td><td><input type="text" name="applyTime" value="{$item['outTime']}" readonly /></td>
<td>实际出库时间：<span class="red bold">*</span></td><td><input type="text" name="actualTime" value=""  id="date" /></td>
</tr>
<tr class="odd">
<td>申请数量：<span class="red bold">*</span></td><td><input type="text" name="applyQuantity" value="{$item['quantity']}" /></td>
<td>实际数量：<span class="red bold">*</span></td><td><input type="text" name="outQuantity" value="1" /></td>
</tr>
<tr class="even">
<td>单　　位：<span class="red bold">*</span></td><td>{$unit}</td>
<td>备　　注：</td><td width="40%"><textarea type="text" name="remark" value="" style="width:350px;height:60px;" ></textarea></td>
</tr>
<tr class="odd">
<td class="small gray" colspan="4">注意事项：确认信息正确无误</td>
</tr>
</tbody>
</table>
<table>
<thead></tr><th colspan="4">添加出库明细</th></tr></thead>
<tbody>
<tr class="odd">
<th width="200">选择库位</th><th>出库数量</th>
</tr>
<tr class="even">
{$stocktr}
</tr>
</tbody>
</table>
<table class="tlist tsuite" align="center" width="100%">
<tbody>
<tr class="odd" nohover>
<td class="center"><input type="submit" id="submitButton" value="  提交  " accesskey="s"><input type="reset" value="  复位  "/></td>
</tr>
</tbody>
</table>
</form>
EOF;

		$this->kclass->tbline+=4;
		$this->kclass->page['title'].='添加出库的批次';
		$this->kclass->page['onload'].='dc.tabhover();$(\'#date\').datepicker()';
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - <a href="/s.php?module=outbound&action=view&outboundid='.$this->kclass->input['outboundid'].'">查看出库单</a> - 添加出库物资', 'right'=>'<a href="/s.php?module=outbound&action=view&outboundid='.$this->kclass->input['outboundid'].'">返回查看出库单</a>','body'=>$body));
	}
	//
	function doperout(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			if($this->kclass->input['batchid']<=0){
				$e.='<li>请选择出库物资的 批次。</li>';
			}
			if($this->kclass->input['actualTime']==''){
				$e.='<li>请输入出库物资的 实际出库时间。</li>';
			}
			if($this->kclass->input['applyQuantity']==''){
				$e.='<li>请输入出库物资的 申请出库数量。</li>';
			}
			if($this->kclass->input['outQuantity']==''){
				$e.='<li>请输入出库物资的 实际出库数量。</li>';
			}
			$hasItem=0;
			for($i=1;$i<=5;$i++){
				if($this->kclass->input['stockid'][$i]>0 AND $this->kclass->input['quantity'][$i]!=''){
					$hasItem=1;
				}
			}
			if($hasItem==0){
				$e .= '<li>需要选择至少一个库位及出库数量。</li>';
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

		$this->kclass->input['applyTime']=strtotime($this->kclass->input['applyTime']);
		$sd=explode('-', $this->kclass->input['actualTime']);
		$actualTime=mktime(0,0,0,$sd[1],$sd[2],$sd[0]);
		$key=array();
		for($i=1;$i<=5;$i++){//获得输入框行的编号
			if($this->kclass->input['stockid'][$i]!=0)
			$key[]=$i;
		}
		$count=count($key);
		for($m=0;$m<$count;$m++){
			$this->kclass->DB->query("UPDATE stockitem SET quantity=quantity-'".$this->kclass->input['quantity'][$key[$m]]."' WHERE batchid='".$this->kclass->input['batchid']."' AND stockid='".$this->kclass->input['stockid'][$key[$m]]."'");
			$stock[]=array('stockid'=>$this->kclass->input['stockid'][$key[$m]],'quantity'=>$this->kclass->input['quantity'][$key[$m]]);
		}
		$stocks=serialize($stock);
		//出库
		$this->kclass->DB->query("
			INSERT INTO outbounditem
				(batchid,barcodeid,outboundid,materialid,materialno,materialTitle,standard,version,applyQuantity,outQuantity,unitid,applyTime,actualTime,stocks,remark,created,creator)
			VALUES
				('".$this->kclass->input['batchid']."','".$this->kclass->input['barcodeid']."','".$this->kclass->input['outboundid']."','".$this->kclass->input['materialid']."','".$this->kclass->input['materialno']."','".$this->kclass->input['materialTitle']."','".$this->kclass->input['standard']."','".$this->kclass->input['version']."','".$this->kclass->input['applyQuantity']."','".$this->kclass->input['outQuantity']."','".$this->kclass->input['unitid']."','".$this->kclass->input['applyTime']."','".$actualTime."','".$stocks."','".$this->kclass->input['remark']."','".TIMENOW."','".$this->kclass->user['userid']."')
		");
		if($this->kclass->input['typeid']==7 OR $this->kclass->input['typeid']==8){
			$condition="disqualified=disqualified-'".$this->kclass->input['outQuantity']."'";
		}else{
			$condition="qualified=qualified-'".$this->kclass->input['outQuantity']."'";
		}
		//减去该物资总数及批次的数量
		$this->kclass->DB->query("UPDATE material SET quantity=quantity-'".$this->kclass->input['outQuantity']."',".$condition." WHERE materialid='".$this->kclass->input['materialid']."'");
		$this->kclass->DB->query("UPDATE batch SET quantity=quantity-'".$this->kclass->input['outQuantity']."',".$condition." WHERE batchid='".$this->kclass->input['batchid']."'");

		$this->kclass->messager(array(
			'title' => '添加出库物资批次',
			'text' => '出库物资批次 已添加成功!返回添加查看出库单',
			'url' => '/s.php?module=outbound&action=view&outboundid='.$this->kclass->input['outboundid'],
			'sec' => 2
		));
	}
	//
	function outAll(){
		$outbound=$this->kclass->DB->queryFirst("SELECT * FROM outbound WHERE killed=0 AND ifComplete<>2 AND outboundid='".$this->kclass->input['outboundid']."' LIMIT 0,1");
		if(!$outbound){
			$e='<li>数据错误，很抱歉~</li>';
		}else{
			$items=$this->kclass->DB->query("
				SELECT pi.itemid,pi.materialid,pi.ifQualified,pi.materialitemid,pi.versionid,pi.quantity,pi.unitid,pi.outTime,pi.remark,
					mv.title AS version,mv.qualified,mv.`change`
				FROM preoutbounditem AS pi
				LEFT JOIN materialversion AS mv ON (mv.versionid=pi.versionid)
				LEFT JOIN material AS m ON (m.materialid=pi.materialid)
				WHERE pi.killed=0 AND pi.module='outbound' AND pi.mid='".$outbound['outboundid']."'
				ORDER BY m.materialno DESC
			");
			if($this->kclass->DB->numRows()){
				while($item=$this->kclass->DB->fetchArray($items)){
					$materialInfo=$this->kclass->getMaterial(array('materialid'=>$item['materialid'],'itemid'=>$item['materialitemid']));
					if($outbound['typeid']!=15 AND $outbound['typeid']!=16){
						$barcode=$this->kclass->DB->queryFirst("SELECT COUNT(*) AS count FROM barcode WHERE scraped=0 AND materialid='".$item['materialid']."' AND versionid=".$item['versionid']." LIMIT 0,1");
						if($barcode['count']==0){
							$e.='<li>物资 '.$materialInfo['no'].'　'.$materialInfo['material'].'　'.$materialInfo['sandard'].'暂无批次</li>';
						}
					}else{
						if($outbound['orderid']>0){
							$inbound=$this->kclass->DB->queryFirst("SELECT ifComplete FROM inbound WHERE killed=0 AND orderid='".$outbound['orderid']."' ORDER BY created DESC LIMIT 0,1");
							if($inbound['ifComplete']!=2){
								$e.='<li>该订单还未入库</li>';
							}
						}elseif($outbound['sampleid']>0){
							$inbound=$this->kclass->DB->queryFirst("SELECT ifComplete FROM inbound WHERE killed=0 AND sampleid='".$outbound['sampleid']."' ORDER BY created DESC LIMIT 0,1");
							if($inbound['ifComplete']!=2){
								$e.='<li>该样品单还未入库</li>';
							}
						}
					}
				}
			}
			// 即将出库数量
			$perItem=$this->kclass->DB->queryFirst("SELECT SUM(quantity) AS quantity,SUM(wastage) AS wastage FROM preoutbounditem WHERE killed=0 AND module='outbound' AND mid='".$outbound['outboundid']."'");
			// 已经出库数量
			$outItem=$this->kclass->DB->queryFirst("SELECT SUM(outQuantity) AS quantity FROM outbounditem WHERE killed=0 AND outboundid='".$outbound['outboundid']."' LIMIT 0,1");
			if($outItem['quantity']>=($perItem['quantity']+$perItem['wastage'])){
				$e.='<li>该出库单已全部出库</li>';
			}
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '新建出库的物资',
				'text' => '新建出库的物资<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$id=array();
		$outItems=$this->kclass->DB->query("SELECT DISTINCT preitemid FROM outbounditem WHERE killed=0 AND outboundid='".$this->kclass->input['outboundid']."'");
		if($this->kclass->DB->numRows($outItems)){
			while($outItem=$this->kclass->DB->fetchArray($outItems)){
				$id[]=$outItem['preitemid'];
			}
		}
		// 获得即将出库明细数量累加值
		$preitems=$this->kclass->DB->query("
			SELECT SUM(quantity) AS count,SUM(wastage) AS count1,versionid
			FROM preoutbounditem 
			WHERE killed=0 AND module='outbound' AND mid='".$outbound['outboundid']."'
			GROUP BY versionid
			ORDER BY itemid ASC
		");
		if($this->kclass->DB->numRows()){
			while($preitem=$this->kclass->DB->fetchArray($preitems)){
				$totalCount[$preitem['versionid']]=$preitem['count']+$preitem['count1'];
			}
		}
		$items=$this->kclass->DB->query("
			SELECT pi.itemid,pi.materialid,pi.materialitemid,pi.versionid,pi.productid,pi.productitemid,pi.ifQualified,pi.quantity,pi.wastage,pi.unitid,pi.outTime,pi.remark,
				mv.title AS version,mv.qualified,mv.`change`,mv.qualified AS store1,mv.disqualified AS store2
			FROM preoutbounditem AS pi
			LEFT JOIN materialversion AS mv ON (mv.versionid=pi.versionid)
			LEFT JOIN material AS m ON (m.materialid=pi.materialid)
			WHERE pi.killed=0 AND pi.module='outbound' AND pi.mid='".$outbound['outboundid']."'
			ORDER BY m.materialno DESC
		");
		if($this->kclass->DB->numRows()){
			$i=$counter=1;
			while($item=$this->kclass->DB->fetchArray($items)){
				if(!in_array($item['itemid'],$id)){
					$materialInfo=$this->kclass->getMaterial(array('materialid'=>$item['materialid'],'itemid'=>$item['materialitemid']));
					$barcodeSelect=$d='';
					$counter++;
					if($outbound['typeid']==15){ //订单出库
						$store=0;
						/*$preInitems=$this->kclass->DB->query("
							SELECT pi.itemid
							FROM preinbounditem AS pi
							LEFT JOIN inbound AS i ON (pi.mid=i.inboundid)
							WHERE i.killed=0 AND pi.killed=0 AND pi.module='inbound' AND i.ifVerify=1 AND i.orderid='".$outbound['orderid']."' AND productid='".$item['productid']."' AND productitemid='".$item['productitemid']."'
							ORDER BY pi.created DESC 
						");
						if($this->kclass->DB->numRows()){
							$barcodeSelect.='<select id="barcodeid'.$i.'" name="barcodeid['.$i.']" style="width:550px"><option value=0></option>';
							while($preInitem=$this->kclass->DB->fetchArray($preInitems)){
								$inItem=$this->kclass->DB->queryFirst("
									SELECT ii.quantity,ii.barcodeid,b.barcode,b.arrivalTime
									FROM inbounditem AS ii
									LEFT JOIN barcode AS b ON (ii.barcodeid=b.barcodeid)
									WHERE killed=0 AND preitemid=".$preInitem['itemid']." 
									ORDER BY ii.created DESC
									LIMIT 0,1
								");
								$barcodeSelect.='<option value="'.$inItem['barcodeid'].'" selected>'.$inItem['barcode'].' ( 批次时间：'.date('Y-m-d',$inItem['arrivalTime']).' ) 库存：'.$inItem['quantity'].'</option>';
								$store+=$inItem['quantity'];
							}
							$barcodeSelect.='</select>';
						}*/
						$inItems=$this->kclass->DB->query("
							SELECT ii.quantity,ii.barcodeid,b.barcode,b.arrivalTime
							FROM inbounditem AS ii
							LEFT JOIN barcode AS b ON (ii.barcodeid=b.barcodeid)
							LEFT JOIN preinbounditem AS pi ON (ii.preitemid=pi.itemid) 
							LEFT JOIN inbound AS i ON (i.inboundid=ii.inboundid)
							WHERE i.killed=0 AND pi.killed=0 AND pi.module='inbound' AND i.ifVerify=1 AND ii.orderid=".$outbound['orderid']."  AND pi.productid='".$item['productid']."' AND pi.productitemid='".$item['productitemid']."'
							ORDER BY ii.created DESC
						");
						$barcodeSelect.='<select id="barcodeid'.$i.'" name="barcodeid['.$i.']" style="width:550px"><option value=0></option>';
						if($this->kclass->DB->numRows()){
							while($inItem=$this->kclass->DB->fetchArray($inItems)){
								$barcodeSelect.='<option value="'.$inItem['barcodeid'].'" selected>'.$inItem['barcode'].' ( 批次时间：'.date('Y-m-d',$inItem['arrivalTime']).' ) 库存：'.$inItem['quantity'].'</option>';
								$store+=$inItem['quantity'];
							}
						}
						$barcodeSelect.='</select>';
						$product=$this->kclass->getProduct(array('productid'=>$item['productid'],'itemid'=>$item['productitemid']));
						$material='<a href="/s.php?module=product&cation=view&productid='.$item['productid'].'">'.$product['title'].'</a>　'.$product['standard'];
					}elseif($outbound['typeid']==16){
						$store=0;
						$preInitems=$this->kclass->DB->query("
							SELECT pi.itemid
							FROM preinbounditem AS pi
							LEFT JOIN inbound AS i ON (pi.mid=i.inboundid)
							WHERE i.killed=0 AND pi.killed=0 AND pi.module='inbound' AND i.ifVerify=1 AND i.sampleid='".$outbound['sampleid']."' AND productid='".$item['productid']."' AND productitemid='".$item['productitemid']."'
							ORDER BY pi.created ASC 
						");
						if($this->kclass->DB->numRows()){
							$barcodeSelect.='<select id="barcodeid'.$i.'" name="barcodeid['.$i.']" style="width:550px"><option value=0></option>';
							while($preInitem=$this->kclass->DB->fetchArray($preInitems)){
								$inItem=$this->kclass->DB->queryFirst("
									SELECT ii.quantity,ii.barcodeid,b.barcode,b.arrivalTime
									FROM inbounditem AS ii
									LEFT JOIN barcode AS b ON (ii.barcodeid=b.barcodeid)
									WHERE killed=0 AND preitemid=".$preInitem['itemid']." 
									ORDER BY ii.created DESC
									LIMIT 0,1
								");
								$barcodeSelect.='<option value="'.$inItem['barcodeid'].'" selected>'.$inItem['barcode'].' ( 批次时间：'.date('Y-m-d',$inItem['arrivalTime']).' ) 库存：'.$inItem['quantity'].'</option>';
								$store+=$inItem['quantity'];
							}
							$barcodeSelect.='</select>';
						}
						$product=$this->kclass->getProduct(array('productid'=>$item['productid'],'itemid'=>$item['productitemid']));
						$material='<a href="/s.php?module=product&cation=view&productid='.$item['productid'].'">'.$product['title'].'</a>　'.$product['standard'];
					}else{
						$material='<a href="'.$materialInfo['url'].'">'.$materialInfo['no'].'</a>　'.$materialInfo['material'].'　'.$materialInfo['standard'];
						$version='<a href="#" class="tip" title="'.$materialInfo['no'].$item['change'].'">'.$item['version'].'</a>';
						if($item['ifQualified']==1){
							$qualifiedType='良品';
							$store=$item['store1'];
						}elseif($item['ifQualified']==0){
							$qualifiedType='不良品';
							$store=$item['store2'];
						}
						$barcodeSelect=$this->kclass->chooserBarcode(array('name'=>'barcodeid['.$i.']','condition'=>'materialid='.$item['materialid'],'versionid'=>$item['versionid'],'width'=>250,'ifQualified'=>$item['ifQualified'],'ifMinus'=>1));
					}
					$itemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').' small">
						<td><input type="hidden" name="itemId['.$i.']" value="'.$item['itemid'].'">
						<input type="hidden" name="ifQualified['.$i.']" value="'.$item['ifQualified'].'">'.$i.'</td>
						<td>'.$material.'</td>
						<td>'.$version.'</td>
						<td>'.$store.'</td>
						<td>'.date('Y-m-d',$item['outTime']).'</td>
						<td>'.($item['quantity']+$item['wastage']).'</td>
						<td><input type="text" name="outQuantity['.$i.']" value="'.($item['quantity']+$item['wastage']).'" size="7"  /></td>
						<td>'.$totalCount[$item['versionid']].'</td>
						<td>'.$barcodeSelect.'</td>
						<td><input type="text" name="actualTime['.$i.']" value="'.date('Y-m-d',TIMENOW).'" id="dateline'.$i.'" size="10" /></td>
						<td><input type="text" name="itemRemark['.$i.']" value="'.$item['remark'].'" size="20" /></td>
						<td><input class="select" type="checkbox" name="itemSelect['.$i.']" value="'.$item['itemid'].'"></td>
					</tr>';
					$dateId.=$this->kclass->iif($i<$counter,'#dateline'.$i.',','#dateline'.$i);
					$i++;
				}
			}
		}
		$body=<<<EOF
<form action="/s.php?module=outbound&action=dooutAll" name="outbound" method="post" onsubmit="dc.checkSubmit(this)">
<input type="hidden" name="module" value="outbound" />
<input type="hidden" name="action" value="dooutAll" />
<input type="hidden" name="outboundid" value="{$this->kclass->input['outboundid']}" />
<table class="hundred"><thead><tr><th colspan=12>选择明细出库 <span class="right">全选　<input type="checkbox" name"itemAll" id="selectAll" value="1"></span></th></tr></thead><tbody>
<tr class="center bold"><td width=30>ID</td><td>物资</td><td>版本</td><td>库存</td><td>申请时间</td><td width="50">申请数</td><td>实际数 <span class="red bold">*</span></td><td width="50">总数</td><td>条码批次 <span class="red bold">*</span></td><td>实出时间 <span class="red bold">*</span></td><td>备注</td><td>选</td></tr>
{$itemtr}
<tr class="odd" nohover>
<td class="center" colspan=12><input type="submit" id="submitButton" value="  提交  " accesskey="s"><input type="reset" value="  复位  "/></td>
</tr>
</tbody></table>
</form>
EOF;
		$this->kclass->tbline+=4;
		$this->kclass->page['title'].='全部出库';
		$this->kclass->page['onload'].='dc.tabhover();$(\''.$dateId.'\').datepicker();dc.selectAll();dc.tips()';
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - <a href="/s.php?module=outbound&action=view&outboundid='.$this->kclass->input['outboundid'].'">查看出库单</a> - 全部出库', 'right'=>'<a href="/s.php?module=outbound&action=view&outboundid='.$this->kclass->input['outboundid'].'">查看出库单</a>','body'=>$body));
	}
	//
	function dooutAll(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			$outbound=$this->kclass->DB->queryFirst("SELECT outboundid,outboundno,typeid,orderid,sampleid FROM outbound WHERE outboundid='".$this->kclass->input['outboundid']."' LIMIT 0,1");
			$hasItem=0;
			$j=count($this->kclass->input['itemId']);
			for($i=1;$i<=$j;$i++){
				if($this->kclass->input['itemSelect'][$i]>0 AND $this->kclass->input['barcodeid'][$i]>0 AND $this->kclass->input['actualTime'][$i]!='' AND $this->kclass->input['outQuantity'][$i]>0){
					$item=$this->kclass->DB->queryFirst("
						SELECT itemid,materialid,materialitemid,versionid,quantity,unitid,outTime
						FROM preoutbounditem 
						WHERE killed=0 AND module='outbound' AND mid='".$this->kclass->input['outboundid']."' AND itemid='".$this->kclass->input['itemId'][$i]."'
						LIMIT 0,1
					");
					$materialInfo=$this->kclass->getMaterial(array('materialid'=>$item['materialid'],'itemid'=>$item['materialitemid']));
					$barcode=$this->kclass->DB->queryFirst("
						SELECT barcodeid,versionid,quantity,qualified,disqualified
						FROM barcode
						WHERE barcodeid='".$this->kclass->input['barcodeid'][$i]."' 
						LIMIT 0,1
					");
					if($this->kclass->input['ifQualified'][$i]==1){
						$store=$barcode['qualified'];
					}elseif($this->kclass->input['ifQualified'][$i]==0){
						$store=$barcode['disqualified'];
					}
					if($this->kclass->input['outQuantity'][$i]>$store){
						$e.='<li>ID：【'.$i.'】　物资<b>'.$materialInfo['no'].'</b> 出库数量大于该批次库存数。</li>';
					}
					$hasItem=1;
				}
			}
			if($hasItem=0){
				$e.='<li>请将出库信息填写完成</li>';
			}
			// 即将出库数量
			$perItem=$this->kclass->DB->queryFirst("SELECT SUM(quantity) AS quantity,SUM(wastage) AS wastage FROM preoutbounditem WHERE killed=0 AND module='outbound' AND mid='".$this->kclass->input['outboundid']."'");
			// 已经出库数量
			$outItem=$this->kclass->DB->queryFirst("SELECT SUM(outQuantity) AS quantity FROM outbounditem WHERE killed=0 AND outboundid='".$this->kclass->input['outboundid']."' LIMIT 0,1");
			if($outItem['quantity']>=($perItem['quantity']+$perItem['wastage'])){
				$e.='<li>该出库单已全部出库</li>';
			}
		}
		if($e){
			$this->kclass->messager(array(
				'title' => '物资出库',
				'text' => '您在物资出库的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		for($n=1;$n<$j+1;$n++){
			if($this->kclass->input['itemSelect'][$n]>0 AND $this->kclass->input['barcodeid'][$n]>0 AND $this->kclass->input['actualTime'][$n]>0 AND $this->kclass->input['outQuantity'][$n]>0){
				$sd=explode('-', $this->kclass->input['actualTime'][$n]);
				$actualTime=mktime(0,0,0,$sd[1],$sd[2],$sd[0]);
				$item=$this->kclass->DB->queryFirst("
					SELECT pi.itemid,pi.materialid,pi.materialitemid,pi.versionid,pi.productid,pi.productitemid,pi.ifQualified,pi.quantity,pi.wastage,pi.unitid,pi.outTime
					FROM preoutbounditem AS pi
					WHERE pi.killed=0 AND pi.module='outbound' AND pi.mid='".$this->kclass->input['outboundid']."' AND pi.itemid='".$this->kclass->input['itemId'][$n]."'
					LIMIT 0,1
				");

				/* 库位出库，暂时保留
				for($m=1;$m<$j+1;$m++){
					if($this->kclass->input['stockid'][$m]>0 AND $this->kclass->input['quantity'][$m]!=''){
						$this->kclass->DB->query("UPDATE stockitem SET quantity=quantity-'".$this->kclass->input['quantity'][$m]."' WHERE batchid='".$this->kclass->input['batchid']."' AND stockid='".$this->kclass->input['stockid'][$m]."'");
						$this->kclass->DB->query("UPDATE stock SET ifEmpty='".$this->kclass->input['ifEmpty'][$m]."' WHERE killed=0 AND stockid='".$this->kclass->input['stockid'][$m]."'");
						$stock[]=array('stockid'=>$this->kclass->input['stockid'][$m],'quantity'=>$this->kclass->input['quantity'][$m]);
					}
				}
				$stocks=serialize($stock);
				*/
				$stocks='';
				$applyQuantity=$item['quantity']+$item['wastage'];
				$barcode=$this->kclass->DB->queryFirst("
					SELECT b.barcodeid,b.materialid,b.batchid,b.quantity,b.qualified,b.disqualified,b.versionid, 
						mv.itemid,mv.quantity AS versionQuantity,mv.qualified AS versionQualified,mv.disqualified AS versionDisqualified
					FROM barcode AS b
					LEFT JOIN materialversion AS mv ON (b.versionid=mv.versionid)
					WHERE barcodeid='".$this->kclass->input['barcodeid'][$n]."' 
					LIMIT 0,1
				");
				$this->kclass->DB->query("
					INSERT INTO outbounditem (batchid,barcodeid,outboundid,materialid,materialitemid,versionid,preitemid,applyQuantity,outQuantity,applyTime,actualTime,stocks,remark,created,creator)
					VALUES ('".$barcode['batchid']."','".$this->kclass->input['barcodeid'][$n]."','".$this->kclass->input['outboundid']."','".$barcode['materialid']."','".$barcode['itemid']."','".$barcode['versionid']."','".$item['itemid']."','".$applyQuantity."','".$this->kclass->input['outQuantity'][$n]."','".$item['outTime']."','".$actualTime."','".$stocks."','".$this->kclass->input['itemRemark'][$n]."','".TIMENOW."','".$this->kclass->user['userid']."')
				");
				$itemid=$this->kclass->DB->insertID();

				//减去该物资总数及批次的数量
				if($item['ifQualified']==1){
					$outQualified=$this->kclass->input['outQuantity'][$n];$outDisqualified=0;
					$changeQuantity='qualified=qualified-'.$this->kclass->input['outQuantity'][$n];
				}elseif($item['ifQualified']==0){
					$outQualified=0;$outDisqualified=$this->kclass->input['outQuantity'][$n];
					$changeQuantity='disqualified=disqualified-'.$this->kclass->input['outQuantity'][$n];
				}

				//批次数量
				$this->kclass->DB->query("UPDATE batch SET quantity=quantity-'".$this->kclass->input['outQuantity'][$n]."',".$changeQuantity." WHERE batchid='".$barcode['batchid']."'");
				//条码数量
				$this->kclass->DB->query("UPDATE barcode SET quantity=quantity-'".$this->kclass->input['outQuantity'][$n]."',".$changeQuantity." WHERE barcodeid='".$this->kclass->input['barcodeid'][$n]."'");
				//版本数量
				$this->kclass->DB->query("UPDATE materialversion SET quantity=quantity-'".$this->kclass->input['outQuantity'][$n]."',".$changeQuantity." WHERE versionid='".$barcode['versionid']."'");
				//物资数量
				$this->kclass->DB->query("UPDATE material SET quantity=quantity-'".$this->kclass->input['outQuantity'][$n]."',".$changeQuantity." WHERE materialid='".$barcode['materialid']."'");
				/*// 出库后，清查批次数量
				$afterOutBatch=$this->kclass->DB->queryFirst("SELECT quantity FROM batch WHERE batchid='".$barcode['batchid']."'");
				if($afterOutBatch['quantity']==0){ // 批次数量为0的时候，删除
					$this->kclass->DB->query("UPDATE batch SET killed='".TIMENOW."',killer='".$this->kclass->user['userid']."' WHERE batchid='".$barcode['batchid']."'");
				}
				$afterOutBarcode=$this->kclass->DB->queryFirst("SELECT quantity FROM barcode WHERE barcodeid='".$this->kclass->input['barcodeid'][$n]."'");
				if($afterOutBarcode['quantity']==0){
					$this->kclass->DB->query("UPDATE barcode SET scraped='".TIMENOW."',scraper='".$this->kclass->user['userid']."' WHERE barcodeid='".$this->kclass->input['barcodeid'][$n]."'");
				}*/
				// 产品明细数量
				if($barcode['itemid']>0){
					$this->kclass->DB->query("UPDATE materialitem SET quantity=quantity-'".$this->kclass->input['outQuantity'][$n]."',".$changeQuantity." WHERE itemid='".$barcode['itemid']."'");
				}
				// 物料收发卡(出)
				$this->kclass->DB->query("
					INSERT inoutrecord (`module`,`mid`,`materialid`,`materialitemid`,`versionid`,`itemid`,`addition`,`qualified`,`balance`,`disqualified`,`disbalance`,`dateline`,`created`,`creator`)
					VALUE ('outbound','".$this->kclass->input['outboundid']."','".$barcode['materialid']."','".$barcode['itemid']."','".$barcode['versionid']."','".$itemid."','-','".$outQualified."','".($barcode['versionQualified']-$outQualified)."','".$outDisqualified."','".($barcode['versionDisqualified']-$outDisqualified)."','".TIMENOW."','".TIMENOW."','".$this->kclass->user['userid']."')
				");
			}
		}
		// 判断出库单出库的状态
		$inStatus='';
		$preout=$this->kclass->DB->queryFirst("
			SELECT SUM(quantity) AS count,SUM(wastage) AS wastage
			FROM preoutbounditem
			WHERE killed=0 AND module='outbound' AND mid='".$this->kclass->input['outboundid']."'
			LIMIT 0,1
		");
		$preQuantity=$preout['count']+$preout['wastage'];
		$out=$this->kclass->DB->queryFirst("
			SELECT SUM(outQuantity) AS count
			FROM outbounditem
			WHERE killed=0 AND outboundid='".$this->kclass->input['outboundid']."'
			LIMIT 0,1
		");
		if($preQuantity>$out['count'] AND $out['count']>0){
			$inStatus='部分出库';
			$inShow=1;
			$this->kclass->DB->query("UPDATE outbound SET ifComplete=1 WHERE outboundid='".$this->kclass->input['outboundid']."'");
		}elseif($preQuantity>=$out['count'] AND $out['count']>0){
			$inStatus='出库完成';
			$inShow=2;
			$items=$this->kclass->DB->query("
				SELECT i.outQuantity,
					pi.versionid,pi.taskitemid,
					pm.ifGeneral
				FROM outbounditem AS i
				LEFT JOIN preoutbounditem AS pi ON (pi.itemid=i.preitemid)
				LEFT JOIN material AS m ON (m.materialid=i.materialid)
				LEFT JOIN pmaterial AS pm ON (pm.materialid=m.materialid)
				WHERE i.killed=0 AND i.outboundid='".$this->kclass->input['outboundid']."'
			");
			if($this->kclass->DB->numRows()){
				while($item=$this->kclass->DB->fetchArray($items)){
					if($item['ifGeneral']==0 AND $item['taskitemid']>0){
						$version=$this->kclass->DB->queryFirst("SELECT planOutbound FROM materialversion WHERE versionid='".$item['versionid']."'");
						if($version['planOutbound']-$item['outQuantity']<0){
							$planOutbound=0;
						}else{
							$planOutbound=$version['planOutbound']-$item['outQuantity'];
						}
						$this->kclass->DB->query("UPDATE materialversion SET planOutbound='".$planOutbound."' WHERE versionid='".$item['versionid']."'");
					}
				}
			}
			$this->kclass->DB->query("UPDATE outbound SET ifComplete=2 WHERE outboundid='".$this->kclass->input['outboundid']."'");
			
			if($outbound['orderid']>0){  // 如果是订单出库，更改状态
				$this->kclass->DB->query("UPDATE `order` SET ifOutbound=2 WHERE orderid='".$outbound['orderid']."'");
			}elseif($outbound['sampleid']>0){ // 如果是样品出库，更改状态
				$this->kclass->DB->query("UPDATE `sample` SET ifOutbound=2 WHERE sampleid='".$outbound['sampleid']."'");
			}
		}elseif(!$out['count']){
			$inStatus='等待出库';
			$inShow=0;
		}

		$this->kclass->messager(array(
			'title' => '添加出库物资批次',
			'text' => '出库物资批次 已添加成功!返回添加查看出库单',
			'url' => '/s.php?module=outbound&action=view&outboundid='.$this->kclass->input['outboundid'],
			'sec' => 2
		));
	}
	//
	function kill(){
		if($this->kclass->input['outboundid']<=0){
			$this->kclass->boinkIt('/s.php?module=outbound');
		}
		if(!$this->kclass->input['rt'] AND $this->kclass->input['confirm'] == 0){
			$this->kclass->boinkIt('/s.php?module=outbound');
		}
		if($this->kclass->input['outboundid'] < 0){
			$e = '<li>请从正确的页面进行操作，如果此问题持续存在，请联系网站管理员。</li>';
		}
		$item=$this->kclass->DB->query("SELECT COUNT(*) AS count FROM outbounditem WHERE killed=0 AND outboundid='".$this->kclass->input['outboundid']."'");
		if($item['count']>0){
			$e.='<li>该出库单正在出库中，删除前请联系仓库取消出库</li>';
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '删除出库单',
				'text' => '您在删除出库单的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		if($this->kclass->input['rt']=='view'){
			$rt = '&action=view&outboundid='.$this->kclass->input['outboundid'];
		}elseif($this->kclass->input['rt']=='list'){
			$rt = '#'.$this->kclass->input['outboundid'];
		}
		$outbound = $this->kclass->DB->queryFirst("
			SELECT outboundno,orderid,sampleid
			FROM outbound
			WHERE outboundid='".$this->kclass->input['outboundid']."'
			LIMIT 0,1
		");
		if($outbound){
			$this->kclass->DB->query("
				UPDATE `outbound`
				SET killed=".TIMENOW.",killer='".$this->kclass->user['userid']."'
				WHERE outboundid='".$this->kclass->input['outboundid']."'
			");
			$items=$this->kclass->DB->query("
				SELECT DISTINCT taskitemid
				FROM preoutbounditem
				WHERE killed=0 AND module='outbound' AND mid='".$this->kclass->input['outboundid']."'
			");
			if($this->kclass->DB->numRows()){
				while($item=$this->kclass->DB->fetchArray($items)){
					$this->kclass->DB->query("UPDATE `taskitem` SET ifChooser=0 WHERE itemid='".$item['taskitemid']."'");
				}
			}
			if($outbound['orderid']>0){  // 如果是订单出库，更改状态
				$this->kclass->DB->query("UPDATE `order` SET ifOutbound=0 WHERE orderid='".$outbound['orderid']."'");
			}elseif($outbound['sampleid']>0){ // 如果是样品出库，更改状态
				$this->kclass->DB->query("UPDATE `sample` SET ifOutbound=0 WHERE sampleid='".$outbound['sampleid']."'");
			}
			$this->kclass->messager(array(
				'title' => '删除出库单成功',
				'text' => '出库单 <b>'.$outbound['outboundno'].'</b> 已成功被标记为删除!',
				'url' => '/s.php?module=outbound'.$rt,
				'sec' => 3
			));
		}else{
			$this->kclass->messager(array(
				'title' => '删除出库单失败',
				'text' => '您要删除的出库单，不存在！',
				'url' => '/s.php?module=outbound'.$rt,
				'sec' => 3
			));
		}
	}

	//
	function remove(){
		if($this->kclass->input['outboundid']<=0){
			$this->kclass->boinkIt('/s.php?module=outbound');
		}
		$item=$this->kclass->DB->query("SELECT COUNT(*) AS count FROM outbounditem WHERE killed=0 AND outboundid='".$this->kclass->input['outboundid']."'");
		if($item['count']>0){
			$e.='<li>该出库单正在出库中，删除前请联系仓库取消出库</li>';
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '删除出库单',
				'text' => '您在删除出库单的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$outbound = $this->kclass->DB->queryFirst("
			SELECT outboundno
			FROM outbound
			WHERE outboundid='".$this->kclass->input['outboundid']."'
		");
$body = <<<EOF
<form action="/s.php?module=outbound&action=kill" name="outbound" method="post">
<input type="hidden" name="module" value="outbound">
<input type="hidden" name="action" value="kill">
<input type="hidden" name="outboundid" value="{$this->kclass->input['outboundid']}">
<table><thead>
<thead>
<tr>
<td>删除出库单：{$outbound['outboundno']}</td>
</tr>
</thead>
<tbody>
<tr class="odd">
<td class="middle">你确定要删除出库单: <a href="/s.php?module=outbound&action=view&outboundid={$this->kclass->input['outboundid']}" class="big bold" target="_blank">{$outbound['outboundno']}</a> 吗?</td>
</tr>
<tr class="even">
<td align="center">
	<label for="c1" class="red"><input type="radio" id="c1" name="confirm" value="1">是</label>
	<label for="c0" class="green"><input type="radio" id="c0" name="confirm" value="0" checked>否</label>
</td>
</tr>
<tr class="odd" nohover>
<td align="center">
	<input type="submit" value="   保存   " accesskey="s">
	<input type="reset" value="   复位   ">
</td>
</tr>
</tbody>
</table>
</form>
EOF;
		$this->kclass->page['title'] .= ' - 删除 - '.$outbound['outboundno'];
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 删除出库单 - '.$outbound['title'], 'right' => '<a href="/s.php?module=outbound">返回列表</a>', 'body'=>$body));
	}

	//
	function revival(){
		if($this->kclass->input['outboundid']<=0){
			$this->kclass->boinkIt('/s.php?module=outbound');
		}
		if(!$this->kclass->input['rt'] AND $this->kclass->input['confirm'] == 0){
			$this->kclass->boinkIt('/s.php?module=outbound');
		}
		if($this->kclass->input['outboundid'] < 0){
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
		$outbound = $this->kclass->DB->queryFirst("
			SELECT outboundno
			FROM outbound
			WHERE outboundid='".$this->kclass->input['outboundid']."'
		");
		if($this->kclass->input['rt']=='view'){
			$rt = '&action=view&outboundid='.$this->kclass->input['outboundid'];
		}elseif($this->kclass->input['rt']=='list'){
			$rt = '#'.$this->kclass->input['outboundid'];
		}
		if($outbound){
			$this->kclass->DB->query("
				UPDATE `outbound`
				SET killed=0,killer=0
				WHERE outboundid='".$this->kclass->input['outboundid']."'
			");
			$this->kclass->messager(array(
				'title' => '恢复出库单成功',
				'text' => '出库单 <b>'.$outbound['outboundno'].'</b> 已成功被恢复！',
				'url' => '/s.php?module=outbound'.$rt,
				'sec' => 3
			));
		}else{
			$this->kclass->messager(array(
				'title' => '恢复出库单失败',
				'text' => '您要恢复的出库单不存在！',
				'url' => '/s.php?module=outbound'.$rt,
				'sec' => 3
			));
		}
	}

	//
	function restore(){
		if($this->kclass->input['outboundid']<=0){
			$this->kclass->boinkIt('/s.php?module=outbound');
		}
		$outbound = $this->kclass->DB->queryFirst("
			SELECT outboundno
			FROM outbound
			WHERE outboundid='".$this->kclass->input['outboundid']."'
		");
$body = <<<EOF
<form action="/s.php?module=outbound&action=revival" name="outbound" method="post">
<input type="hidden" name="module" value="outbound">
<input type="hidden" name="action" value="revival">
<input type="hidden" name="outboundid" value="{$this->kclass->input['outboundid']}">
<table><thead>
<thead>
<tr>
<td>恢复供应商：{$outbound['outboundno']}</td>
</tr>
</thead>
<tbody>
<tr class="odd">
<td class="middle">你确定要恢复出库单: <a href="/s.php?module=outbound&action=view&outboundid={$this->kclass->input['outboundid']}" class="big bold" target="_blank">{$outbound['outboundno']}</a> 吗?</td>
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
		$this->kclass->page['title'] .= ' - 恢复 - '.$outbound['outboundno'];
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 恢复出库单 - '.$outbound['outboundno'], 'right' => '<a href="/s.php?module=outbound">返回列表</a>', 'body'=>$body));
	}
	// 审核通过
	function verify(){
		$outbound=$this->kclass->DB->queryFirst("
			SELECT ob.outboundid,ob.orderid,ob.sampleid,ob.orderno,ob.outboundno,ob.oldoutno,ob.departmentid,ob.applicant,ob.purpose,ob.typeid,ob.workcenterid,ob.remark,ob.verifyRemark,ob.ifVerify,ob.ifConfirm,ob.verified,ob.created,ob.ifComplete,ob.killed,
				d.title AS department,
				t.title AS type,
				m.realname AS creator,
				me.realname AS modifier,
				mem.realname AS verifier
			FROM outbound AS ob
			LEFT JOIN `department` AS d ON (d.departmentid=ob.departmentid)
			LEFT JOIN `inorouttype` AS t ON (t.typeid=ob.typeid)
			LEFT JOIN `member` AS m ON (m.userid=ob.creator)
			LEFT JOIN `member` AS me ON (me.userid=ob.modifier)
			LEFT JOIN `member` AS mem ON (mem.userid=ob.verifier)
			WHERE ob.killed=0 AND ob.outboundid='".$this->kclass->input['outboundid']."'
			LIMIT 0,1
		");
		if($outbound){
			if($outbound['ifVerify']!=0){
				$this->kclass->boinkIt('/s.php?module=outbound');
			}
			//获得物资列表
			$items=$this->kclass->DB->query("
				SELECT poi.*,
					mv.title AS version,mv.qualified AS store1,mv.disqualified AS store2
				FROM preoutbounditem AS poi
				LEFT JOIN `materialversion` AS mv ON (mv.versionid=poi.versionid)
				WHERE poi.killed=0 AND poi.module='outbound' AND poi.mid='".$this->kclass->input['outboundid']."'
				ORDER BY poi.itemid ASC
			");
			if($this->kclass->DB->numRows()){
				$i=1;
				$itemList='<table class="hundred small"><thead><tr><th colspan="11">物资列表</th></tr></thead><tbody><tr class="even center"><td width="15">ID</td><td>物资</td><td width="60">物资版本</td><td width="80">是否良品</td><td width="60">库存数量</td><td width="80">数量/单位</td><td width="80">损耗数/单位</td><td width="100">出库时间</td><td width="100">备注</td></tr>';
				while($item=$this->kclass->DB->fetchArray($items)){
					if($outbound['typeid']==3 OR $outbound['typeid']==8){
						$versionQuantity=$this->kclass->DB->queryFirst("SELECT SUM(qualified) AS quantity FROM materialversion WHERE killed=0 AND productitemid='".$item['productitemid']."' LIMIT 0,1");
						$store=$versionQuantity['quantity'];
					}else{
						if($item['ifQualified']==1){
							$qualifiedType='良品';
							$store=$item['store1'];
						}elseif($item['ifQualified']==0){
							$qualifiedType='不良品';
							$store=$item['store2'];
						}
					}
					if($outbound['typeid']==15 OR $outbound['typeid']==16){
						$standard='';
						$product=$this->kclass->DB->queryFirst("SELECT title FROM product WHERE productid=".$item['productid']." LIMIT 0,1");
						if($item['productitemid']>0){
							$productitem=$this->kclass->DB->queryFirst("
								SELECT attributevalue
								FROM productitem 
								WHERE itemid='".$item['productitemid']."'
							");
							$valueid=explode(',',$productitem['attributevalue']);
							foreach($valueid as $val){
								$attribute=$this->kclass->DB->queryFirst("
									SELECT av.valueid,av.title AS value,
										an.nameid,an.title AS name
									FROM attributevalue AS av
									LEFT JOIN attributename AS an ON (an.nameid=av.nameid)
									WHERE av.valueid='".$val."'
									ORDER BY an.nameid ASC
								");
								if($attribute['name'] AND $attribute['value']){
									$standard .= '　'.$attribute['name'].'：'.'<span class="darkred">'.$attribute['value'].'</span>';
								}
							}
						}
						$material='<a href="/s.php?module=product&action=view&productid='.$item['productid'].'">'.$product['title'].'</a>　'.$standard;
					}else{
						$materialInfo=$this->kclass->getMaterial(array('materialid'=>$item['materialid'],'itemid'=>$item['materialitemid']));
						$material='<a href="'.$materialInfo['url'].'">'.$materialInfo['no'].'</a>　'.$item['itemno'].'</a>　'.$materialInfo['material'].' '.$materialInfo['standard'];
					}
					$item['outTime']=date('Y-m-d',$item['outTime']);
					$itemList.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').'">
						<td>'.$i.'</td>
						<td>'.$material.'</td>
						<td>'.$item['version'].'</td>
						<td>'.$qualifiedType.'</td>
						<td>'.$store.$item['unit'].'</td>
						<td>'.$item['quantity'].$materialInfo['unit'].'</td>
						<td>'.$item['wastage'].$materialInfo['unit'].'</td>
						<td>'.$item['outTime'].'</td>
						<td>'.$item['remark'].'</td>
					</tr>';
					$i++;
				}
				$itemList.='</tbody></table>';
			}
			$created=date('Y-m-d H:i:s',$outbound['created']);
			if($outbound['modified']!=0)$modify='，由'.$outbound['modifier'].'于'.date('Y-m-d H:i:s',$outbound['modified']).'修改';
$body=<<<EOF
<div class="title">{$outbound['outboundno']}<span class="right small gray">由{$outbound['creator']}于{$created}建立{$modify}。</span></div>
<table class="hundred">
<thead><tr><th colspan="4">出库单 <span class="right">{$addMaterial}</span></th></tr></thead>
<tbody>
<tr class="odd">
<td width="85">出库单编号：</td><td width="450">{$outbound['outboundno']}</td>
<td width="85">申请人：</td><td>{$outbound['applicant']} （{$outbound['department']}）</td>
</tr>
<tr class="even">
<td>用途：</td><td>{$outbound['purpose']}</td>
<td>备注：</td><td>{$outbound['remark']}</td>
</tr>
</tbody>
</table>
{$itemList}
<form method="post" action="/s.php?module=outbound&action=doverify">
<input type="hidden" name="module" value="outbound">
<input type="hidden" name="action" value="doverify">
<input type="hidden" name="outboundid" value="{$this->kclass->input['outboundid']}">
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
		}else{
			$body = '系统数据出错，很抱歉！';
		}
		$this->kclass->tbline+=4;
		$this->kclass->page['title'].='审核出库单';
		$this->kclass->page['onload'].='dc.tabhover();';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - <a href="/s.php?module=outbound&action=list">出库单列表</a> - <a href="/s.php?module=outbound&action=view&outboundid='.$outbound['outboundid'].'">查看出库单</a> - 审核出库单', 'right' => '<span class = "small">操作：</span> <a href="/s.php?module=outbound&action=add">新建</a>　<a href="/s.php?module=outbound&action=remove&outboundid='.$outbound['outboundid'].'&rt=view">删除</a>　'.$verifyOperate,'body' => $body));
	}
	// 审核不通过
	function doverify(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			$outbound=$this->kclass->DB->queryFirst("SELECT outboundid,outboundno,orderid,sampleid FROM outbound WHERE killed=0 AND ifVerify=0 AND outboundid='".$this->kclass->input['outboundid']."' LIMIT 0,1");
			if($this->kclass->input['ifVerify']!=1 AND $this->kclass->input['ifVerify']!=-1){
				$e.='<li>请选择出库申请单中的 是否通过审核 按钮。</li>';
			}
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '审核出库申请单',
				'text' => '审核出库申请单<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}

		$this->kclass->DB->query("
			UPDATE outbound SET
			ifVerify='".$this->kclass->input['ifVerify']."',
			verifyRemark='".$this->kclass->input['verifyRemark']."',
			verified='".TIMENOW."',
			verifier='".$this->kclass->user['userid']."'
			WHERE outboundid='".$this->kclass->input['outboundid']."'
		");
		if($this->kclass->input['ifVerify']==1){
			$verify='已通过';
			if($outbound['orderid']>0){  // 如果是订单出库，更改状态
				$this->kclass->DB->query("UPDATE `order` SET ifOutbound=1 WHERE orderid='".$outbound['orderid']."'");
			}elseif($outbound['sampleid']>0){ // 如果是样品出库，更改状态
				$this->kclass->DB->query("UPDATE `sample` SET ifOutbound=1 WHERE sampleid='".$outbound['sampleid']."'");
			}
		}elseif($this->kclass->input['ifVerify']==-1){
			$verify='未通过';
		}
		$this->kclass->messager(array(
			'title' => '审核出库申请',
			'text' => '出库申请单 【<b>'.$outbound['outboundno'].'</b>】 '.$verify.'审核!返回查看出库单',
			'url' => '/s.php?module=outbound&action=view&outboundid='.$this->kclass->input['outboundid'],
			'sec' => 2
		));
	}
	// 入库申请单确认
	function confirm(){
		$outbound=$this->kclass->DB->queryFirst("
			SELECT ob.outboundid,ob.orderid,ob.sampleid,ob.orderno,ob.outboundno,ob.oldoutno,ob.departmentid,ob.applicant,ob.purpose,ob.typeid,ob.workcenterid,ob.remark,ob.verifyRemark,ob.ifVerify,ob.ifConfirm,ob.verified,ob.created,ob.ifComplete,ob.killed,
				d.title AS department,
				t.title AS type,
				m.realname AS creator,
				me.realname AS modifier,
				mem.realname AS verifier
			FROM outbound AS ob
			LEFT JOIN `department` AS d ON (d.departmentid=ob.departmentid)
			LEFT JOIN `inorouttype` AS t ON (t.typeid=ob.typeid)
			LEFT JOIN `member` AS m ON (m.userid=ob.creator)
			LEFT JOIN `member` AS me ON (me.userid=ob.modifier)
			LEFT JOIN `member` AS mem ON (mem.userid=ob.verifier)
			WHERE ob.killed=0 AND ob.outboundid='".$this->kclass->input['outboundid']."'
			LIMIT 0,1
		");
		if($outbound){
			if($outbound['ifVerify']!=0){
				$this->kclass->boinkIt('/s.php?module=outbound');
			}
			//获得物资列表
			$items=$this->kclass->DB->query("
				SELECT poi.*,
					mv.title AS version,mv.qualified AS store1,mv.disqualified AS store2
				FROM preoutbounditem AS poi
				LEFT JOIN `materialversion` AS mv ON (mv.versionid=poi.versionid)
				WHERE poi.killed=0 AND poi.module='outbound' AND poi.mid='".$this->kclass->input['outboundid']."'
				ORDER BY poi.itemid ASC
			");
			if($this->kclass->DB->numRows()){
				$i=1;
				$itemList='<table class="hundred small"><thead><tr><th colspan="11">物资列表</th></tr></thead><tbody><tr class="even center"><td width="15">ID</td><td>物资</td><td width="60">物资版本</td><td width="80">是否良品</td><td width="60">库存数量</td><td width="80">数量/单位</td><td width="80">损耗数/单位</td><td width="100">出库时间</td><td width="100">备注</td></tr>';
				while($item=$this->kclass->DB->fetchArray($items)){
					if($outbound['typeid']==3 OR $outbound['typeid']==8){
						$versionQuantity=$this->kclass->DB->queryFirst("SELECT SUM(qualified) AS quantity FROM materialversion WHERE killed=0 AND productitemid='".$item['productitemid']."' LIMIT 0,1");
						$store=$versionQuantity['quantity'];
					}else{
						if($item['ifQualified']==1){
							$qualifiedType='良品';
							$store=$item['store1'];
						}elseif($item['ifQualified']==0){
							$qualifiedType='不良品';
							$store=$item['store2'];
						}
					}
					if($outbound['typeid']==15 OR $outbound['typeid']==16){
						$standard='';
						$product=$this->kclass->DB->queryFirst("SELECT title FROM product WHERE productid=".$item['productid']." LIMIT 0,1");
						if($item['productitemid']>0){
							$productitem=$this->kclass->DB->queryFirst("
								SELECT attributevalue
								FROM productitem 
								WHERE itemid='".$item['productitemid']."'
							");
							$valueid=explode(',',$productitem['attributevalue']);
							foreach($valueid as $val){
								$attribute=$this->kclass->DB->queryFirst("
									SELECT av.valueid,av.title AS value,
										an.nameid,an.title AS name
									FROM attributevalue AS av
									LEFT JOIN attributename AS an ON (an.nameid=av.nameid)
									WHERE av.valueid='".$val."'
									ORDER BY an.nameid ASC
								");
								if($attribute['name'] AND $attribute['value']){
									$standard .= '　'.$attribute['name'].'：'.'<span class="darkred">'.$attribute['value'].'</span>';
								}
							}
						}
						$material='<a href="/s.php?module=product&action=view&productid='.$item['productid'].'">'.$product['title'].'</a>　'.$standard;
					}else{
						$materialInfo=$this->kclass->getMaterial(array('materialid'=>$item['materialid'],'itemid'=>$item['materialitemid']));
						$material='<a href="'.$materialInfo['url'].'">'.$materialInfo['no'].'</a>　'.$item['itemno'].'</a>　'.$materialInfo['material'].' '.$materialInfo['standard'];
					}
					$item['outTime']=date('Y-m-d',$item['outTime']);
					$itemList.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').'">
						<td>'.$i.'</td>
						<td>'.$material.'</td>
						<td>'.$item['version'].'</td>
						<td>'.$qualifiedType.'</td>
						<td>'.$store.$item['unit'].'</td>
						<td>'.$item['quantity'].$materialInfo['unit'].'</td>
						<td>'.$item['wastage'].$materialInfo['unit'].'</td>
						<td>'.$item['outTime'].'</td>
						<td>'.$item['remark'].'</td>
					</tr>';
					$i++;
				}
				$itemList.='</tbody></table>';
			}
			$created=date('Y-m-d H:i:s',$outbound['created']);
			if($outbound['modified']!=0)$modify='，由'.$outbound['modifier'].'于'.date('Y-m-d H:i:s',$outbound['modified']).'修改';

$body=<<<EOF
<div class="title">{$outbound['outboundno']}<span class="right small gray">由{$outbound['creator']}于{$created}建立{$modify}。</span></div>
<table class="hundred">
<thead><tr><th colspan="4">出库单 <span class="right"></span></th></tr></thead>
<tbody>
<tr class="odd">
<td width="85">出库单编号：</td><td width="450">{$outbound['outboundno']}</td>
<td width="85">申请人：</td><td>{$outbound['applicant']} （{$outbound['department']}）</td>
</tr>
<tr class="even">
<td>用途：</td><td>{$outbound['purpose']}</td>
<td>备注：</td><td>{$outbound['remark']}</td>
</tr>
</tbody>
</table>
{$itemList}
<table class="hundred">
<form method="post" action="/s.php?module=outbound&action=doconfirm">
<input type="hidden" name="module" value="outbound">
<input type="hidden" name="action" value="doconfirm">
<input type="hidden" name="outboundid" value="{$this->kclass->input['outboundid']}">
<tbody>
<tr class="odd">
<td colspan=4 class="center"><input type="submit" value=" 确认" /></td>
</tr>
</tbody>
</form>
</table>
EOF;
		}

		$this->kclass->page['title'].='确认出库单';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - <a href="/s.php?module=outbound&action=list">出库单列表</a> - 确认出库单', 'right' => '<a href="/s.php?module=outbound&action=view&outboundid='.$this->kclass->input['outboundid'].'">返回查看出库申请单</a>　|　<span class="small gray">操作：</span><a href="/s.php?module=outbound&action=add">新建</a>　','body' => $body));
	}
	//
	function doconfirm(){
		$outbound=$this->kclass->DB->queryFirst("SELECT outboundid,outboundno FROM outbound WHERE killed=0 AND ifVerify=0 AND outboundid='".$this->kclass->input['outboundid']."' LIMIT 0,1");
		if(!$outbound){
			$e='<li>数据错误，很抱歉~</li>';
		}else{
			if($outbound['ifConfirm']==1){
				$e.='<li>该出库申请单 已被确认。</li>';
			}
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '确认出库申请单',
				'text' => '确认出库申请单<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}

		$this->kclass->DB->query("
			UPDATE outbound SET
				ifConfirm=1,
				confirmer='".$this->kclass->user['userid']."',
				confirmed='".TIMENOW."'
			WHERE outboundid='".$this->kclass->input['outboundid']."'
		");
		$this->kclass->messager(array(
			'title' => '确认出库申请单',
			'text' => '出库申请单 【<b>'.$outbound['outboundno'].'</b>】 已被确认!返回查看出库申请单',
			'url' => '/s.php?module=outbound&action=view&outboundid='.$this->kclass->input['outboundid'],
			'sec' => 2
		));
	}
	// 对已出库的物资进行取消出库
	function cancelOut(){
		if($this->kclass->input['type']=='item'){
			$item=$this->kclass->DB->queryFirst("
				SELECT oi.*,
					o.outboundno,o.ifComplete,
					m.materialno
				FROM outbounditem AS oi
				LEFT JOIN outbound AS o ON (oi.outboundid=o.outboundid)
				LEFT JOIN material AS m ON (m.materialid=oi.materialid)
				WHERE oi.itemid='".$this->kclass->input['itemid']."'
				LIMIT 0,1
			");
			if(!$item['itemid']){
				$e.='<li>数据错误</li>';
			}
			if(isset($e)){
				$this->kclass->messager(array(
					'title' => '对该出库单的该明细进行取消出库',
					'text' => '取消出库 <ul>'.$e.'</ul>',
					'url' => 'javascript:history.back()',
					'sec' => 3
				));
			}
			$body = <<<EOF
<form action="/s.php?module=outbound&action=docancelOut" name="outbound" method="post">
<input type="hidden" name="module" value="outbound">
<input type="hidden" name="action" value="docancelOut">
<input type="hidden" name="outboundid" value="{$item['outboundid']}">
<input type="hidden" name="itemid" value="{$item['itemid']}">
<input type="hidden" name="type" value="{$this->kclass->input['type']}">
<table><thead>
<thead>
<tr>
<td>删除出库单：{$item['outboundno']}</td>
</tr>
</thead>
<tbody>
<tr class="odd">
<td class="middle">你确定要对出库单: <a href="/s.php?module=outbound&action=view&outboundid={$item['outboundid']}" class="big bold" target="_blank">{$item['outboundno']}</a> 的明细<b>{$item['materialno']}</b>取消出库吗?</td>
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
			$outbound=$this->kclass->DB->queryFirst("SELECT outboundid,outboundno FROM outbound WHERE outboundid='".$this->kclass->input['outboundid']."'");
			if($outbound['outboundid']==''){
				$e.='<li>数据错误</li>';
			}
			if(isset($e)){
				$this->kclass->messager(array(
					'title' => '对该出库单的所有明细进行取消出库',
					'text' => '取消出库 <ul>'.$e.'</ul>',
					'url' => 'javascript:history.back()',
					'sec' => 3
				));
			}
			$body = <<<EOF
<form action="/s.php?module=outbound&action=docancelOut" name="outbound" method="post" onsubmit="dc.checkSubmit(this)">
<input type="hidden" name="module" value="outbound">
<input type="hidden" name="action" value="docancelOut">
<input type="hidden" name="outboundid" value="{$outbound['outboundid']}">
<input type="hidden" name="type" value="{$this->kclass->input['type']}">
<table><thead>
<thead>
<tr>
<td>删除出库单：{$outbound['outboundno']}</td>
</tr>
</thead>
<tbody>
<tr class="odd">
<td class="middle">你确定要对出库单: <a href="/s.php?module=outbound&action=view&outboundid={$outbound['outboundid']}" class="big bold" target="_blank">{$outbound['outboundno']}</a> 取消出库吗?</td>
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
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - <a href="/s.php?module=outbound&action=list">出库单列表</a> - 审核出库单', 'right' => '<a href="/s.php?module=outbound&action=view&outboundid='.$this->kclass->input['outboundid'].'">返回查看出库申请单</a>','body' => $body));
	}
	//
	function docancelOut(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			if($this->kclass->input['type']=='item'){
				$item=$this->kclass->DB->queryFirst("
					SELECT oi.*,
						o.outboundno,
						m.materialno
					FROM outbounditem AS oi
					LEFT JOIN outbound AS o ON (oi.outboundid=o.outboundid)
					LEFT JOIN material AS m ON (m.materialid=oi.materialid)
					WHERE oi.itemid='".$this->kclass->input['itemid']."'
					LIMIT 0,1
				");
				if(!$item['itemid']){
					$e.='<li>出库单 明细 数据错误</li>';
				}
			}elseif($this->kclass->input['type']=='all'){
				$outbound=$this->kclass->DB->queryFirst("SELECT outboundid,outboundno,orderid,sampleid FROM outbound WHERE outboundid='".$this->kclass->input['outboundid']."'");
				if(!$outbound['outboundid']){
					$e.='<li>出库单 数据错误</li>';
				}
			}
		}
		if($this->kclass->input['confirm'] == 0){
			$this->kclass->boinkIt('/s.php?module=outbound&action=view&outboundid='.$this->kclass->input['outboundid']);
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '取消出库',
				'text' => '取消出库<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		if($this->kclass->input['type']=='item'){
			$item=$this->kclass->DB->queryFirst("
				SELECT oi.*,
					o.outboundno,o.ifComplete,o.orderid,o.sampleid,
					m.materialno,
					poi.ifQualified
				FROM outbounditem AS oi
				LEFT JOIN outbound AS o ON (oi.outboundid=o.outboundid)
				LEFT JOIN material AS m ON (m.materialid=oi.materialid)
				LEFT JOIN preoutbounditem AS poi ON (poi.itemid=oi.preitemid)
				WHERE oi.killed=0 AND oi.itemid='".$this->kclass->input['itemid']."'
				LIMIT 0,1
			");
			//减去该物资总数及批次的数量
			if($item['ifQualified']==1){
				$qualified=$item['outQuantity'];$disqualified=0;
				$changeQuantity='qualified=qualified+'.$item['outQuantity'];
			}elseif($item['ifQualified']==0){
				$qualified=0;$disqualified=$item['outQuantity'];
				$changeQuantity='disqualified=disqualified+'.$item['outQuantity'];
			}
			// 物资总数 return
			$this->kclass->DB->query("UPDATE material SET quantity=quantity+'".$item['outQuantity']."',".$changeQuantity." WHERE materialid='".$item['materialid']."'");
			// 物资明细return
			if($item['materialitemid']>0){
				$this->kclass->DB->query("UPDATE materialitem SET quantity=quantity+'".$item['outQuantity']."',".$changeQuantity." WHERE itemid='".$item['materialitemid']."'");
			}
			// 条码总数 return
			$this->kclass->DB->query("UPDATE barcode SET quantity=quantity+'".$item['outQuantity']."',".$changeQuantity.",scraped=0,scraper=0 WHERE barcodeid='".$item['barcodeid']."'");
			// 批次总数 return
			$this->kclass->DB->query("UPDATE batch SET quantity=quantity+'".$item['outQuantity']."',".$changeQuantity.",killed=0,killer=0 WHERE batchid='".$item['batchid']."'");
			// 版本总数 return
			$this->kclass->DB->query("UPDATE materialversion SET quantity=quantity+'".$item['outQuantity']."',".$changeQuantity." WHERE versionid='".$item['versionid']."'");
			// 删除原明细
			$this->kclass->DB->query("UPDATE outbounditem SET killed='".TIMENOW."',killer='".$this->kclass->user['userid']."' WHERE itemid='".$item['itemid']."'");
			// 删除并return物料收发卡数量
			$record=$this->kclass->DB->queryFirst("SELECT recordid,materialid,versionid,qualified,balance,disqualified,disbalance FROM inoutrecord WHERE killed=0 AND module='outbound' AND itemid='".$item['itemid']."'");
			$this->kclass->DB->query("UPDATE inoutrecord SET killed='".TIMENOW."',killer='".$this->kclass->user['userid']."' WHERE recordid='".$record['recordid']."'");
			$this->kclass->DB->query("UPDATE inoutrecord SET balance=balance+'".$qualified."',disbalance=disbalance+'".$disqualified."' WHERE killed=0 AND materialid='".$record['materialid']."' AND versionid='".$record['versionid']."' AND recordid>'".$record['recordid']."'");
			// 出库单状态变化
			$outboundItem=$this->kclass->DB->queryFirst("SELECT COUNT(itemid) AS count FROM outbounditem WHERE killed=0 AND outboundid='".$item['outboundid']."'");
			if($item['ifComplete']!=1){
				$this->kclass->DB->query("UPDATE outbound SET ifComplete=1 WHERE killed=0 AND outboundid='".$item['outboundid']."'");
			}elseif($outboundItem['count']==0){
				$this->kclass->DB->query("UPDATE outbound SET ifComplete=0 WHERE killed=0 AND outboundid='".$item['outboundid']."'");
			}
			if($item['orderid']>0){  // 如果是订单出库，更改状态
				$this->kclass->DB->query("UPDATE `order` SET ifOutbound=1 WHERE orderid='".$item['orderid']."'");
			}elseif($item['sampleid']>0){ // 如果是样品出库，更改状态
				$this->kclass->DB->query("UPDATE `sample` SET ifOutbound=1 WHERE sampleid='".$item['sampleid']."'");
			}
			$this->kclass->messager(array(
				'title' => '取消出库',
				'text' => '出库单 【<b>'.$item['outboundno'].'</b>】 明细 <b>'.$item['materialno'].'</b> 被取消出库!返回查看出库申请单',
				'url' => '/s.php?module=outbound&action=view&outboundid='.$item['outboundid'],
				'sec' => 2
			));
		}elseif($this->kclass->input['type']=='all'){
			$items=$this->kclass->DB->query("
				SELECT oi.*,
					poi.ifQualified
				FROM outbounditem AS oi
				LEFT JOIN preoutbounditem AS poi ON (poi.itemid=oi.preitemid)
				WHERE oi.killed=0 AND oi.outboundid='".$outbound['outboundid']."'
			");
			if($this->kclass->DB->numRows()){
				while($item=$this->kclass->DB->fetchArray($items)){
					$changeQuantity='';
					// 物资总数 return
					if($item['ifQualified']==1){
						$qualified=$item['outQuantity'];$disqualified=0;
						$changeQuantity='qualified=qualified+'.$item['outQuantity'];
					}elseif($item['ifQualified']==0){
						$qualified=0;$disqualified=$item['outQuantity'];
						$changeQuantity='disqualified=disqualified+'.$item['outQuantity'];
					}
					//物资总数 return
					$this->kclass->DB->query("UPDATE material SET quantity=quantity+'".$item['outQuantity']."',".$changeQuantity." WHERE materialid='".$item['materialid']."'");
					// 物资明细return
					if($item['materialitemid']>0){
						$this->kclass->DB->query("UPDATE materialitem SET quantity=quantity+'".$item['outQuantity']."',".$changeQuantity." WHERE itemid='".$item['materialitemid']."'");
					}
					// 条码总数 return
					$this->kclass->DB->query("UPDATE barcode SET quantity=quantity+'".$item['outQuantity']."',".$changeQuantity." WHERE barcodeid='".$item['barcodeid']."'");
					// 批次总数 return
					$this->kclass->DB->query("UPDATE batch SET quantity=quantity+'".$item['outQuantity']."',".$changeQuantity." WHERE batchid='".$item['batchid']."'");
					// 版本总数 return
					$this->kclass->DB->query("UPDATE materialversion SET quantity=quantity+'".$item['outQuantity']."',".$changeQuantity." WHERE versionid='".$item['versionid']."'");
					// 删除并return物料收发卡数量
					$record=$this->kclass->DB->queryFirst("SELECT recordid,materialid,versionid,qualified,balance,disqualified,disbalance FROM inoutrecord WHERE killed=0 AND module='outbound' AND itemid='".$item['itemid']."'");
					$this->kclass->DB->query("UPDATE inoutrecord SET killed='".TIMENOW."',killer='".$this->kclass->user['userid']."' WHERE recordid='".$record['recordid']."'");
					$this->kclass->DB->query("UPDATE inoutrecord SET balance=balance+'".$qualified."',disbalance=disbalance+'".$disqualified."' WHERE killed=0 AND materialid='".$record['materialid']."' AND versionid='".$record['versionid']."' AND recordid>'".$record['recordid']."'");
					// 删除原明细
					$this->kclass->DB->query("UPDATE outbounditem SET killed='".TIMENOW."',killer='".$this->kclass->user['userid']."' WHERE itemid='".$item['itemid']."'");
				}
			}
			if($outbound['orderid']>0){  // 如果是订单出库，更改状态
				$this->kclass->DB->query("UPDATE `order` SET ifOutbound=1 WHERE orderid='".$outbound['orderid']."'");
			}elseif($outbound['sampleid']>0){ // 如果是样品出库，更改状态
				$this->kclass->DB->query("UPDATE `sample` SET ifOutbound=1 WHERE sampleid='".$outbound['sampleid']."'");
			}
			$this->kclass->DB->query("UPDATE outbound SET ifComplete=0 WHERE outboundid='".$outbound['outboundid']."'");
			$this->kclass->messager(array(
				'title' => '取消出库',
				'text' => '出库单 【<b>'.$outbound['outboundno'].'</b>】 已全部被取消出库!返回查看出库申请单',
				'url' => '/s.php?module=outbound&action=view&outboundid='.$this->kclass->input['outboundid'],
				'sec' => 2
			));
		}
	}
}
?>