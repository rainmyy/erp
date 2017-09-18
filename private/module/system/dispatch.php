<?php
class dispatch{

	var $wrongType=array(array('id'=>1,'title'=>'A: 人为操作问题'),array('id'=>2,'title'=>'B: 物料问题'));
	var $repairResult=array(array('id'=>1,'title'=>'A: 合格'),array('id'=>2,'title'=>'B: 报废'));

	function autoRun(){
		$this->kclass->page['title']='派工单';
		$this->baseurl='<a href="/s.php">首页</a> - <a href="/s.php?module=dispatch">派工单</a>';
		$this->right='<a href="/s.php?module=dispatch">列表</a> <a href="/s.php?module=dispatch&action=add">新建</a>';
		switch($this->kclass->input['action']){
			case 'list':
				$this->mmlist();
			break;
			case 'find':
				$this->find();
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
			case 'viewRecord':
				$this->viewRecord();
			break;
			case 'viewRecorditem':
				$this->viewRecorditem();
			break;
			case 'viewProducecounter':
				$this->viewProducecounter();
			break;
			case 'lock':
				$this->lock();
			break;
			case 'dolock':
				$this->dolock();
			break;
			case 'verify':
				$this->verify();
			break;
			case 'doverify':
				$this->doverify();
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
			case 'addQuantity':
				return $this->addQuantity();
				break;
			case 'insertQuantity':
				return $this->insertQuantity();
				break;
			case 'addPayment':
				return $this->addPayment();
				break;
			case 'insertPayment':
				return $this->insertPayment();
				break;
			case 'updateQuantity':
				return $this->updateQuantity();
				break;
			case 'doupdateQuantity':
				return $this->doupdateQuantity();
				break;
			case 'changeStatus':
				return $this->changeStatus();
				break;
			case 'dochangeStatus':
				return $this->dochangeStatus();
				break;
			case 'viewrepairRecord':
				return $this->viewrepairRecord();
				break;
			case 'addrepairRecord':
				return $this->addrepairRecord();
				break;
			case 'insertrepairRecord':
				return $this->insertrepairRecord();
				break;
			case 'removerepairRecord':
				return $this->removerepairRecord();
				break;
			case 'killrepairRecord':
				return $this->killrepairRecord();
				break;
			case 'removeQuantity':
				return $this->removeQuantity();
				break;
			case 'killQuantity':
				return $this->killQuantity();
				break;
			case 'revivalQuantity':
				return $this->revivalQuantity();
				break;
			case 'restoreQuantity':
				return $this->restoreQuantity();
				break;
			default:
				$this->mmlist();
		}
	}
	// 
	function mmlist(){
		if($this->kclass->input['show']=='all'){
			$condition='`dispatch`.killed>=0';
		}elseif($this->kclass->input['show']=='unlock'){
			$condition='`dispatch`.statusid=1 AND `dispatch`.killed=0';
		}elseif($this->kclass->input['show']=='locked'){
			$condition='`dispatch`.statusid=2 AND `dispatch`.killed=0';
		}elseif($this->kclass->input['show']=='inProduction'){
			$condition='`dispatch`.statusid=3 AND `dispatch`.killed=0';
		}elseif($this->kclass->input['show']=='Done'){
			$condition='`dispatch`.statusid=4 AND `dispatch`.killed=0';
		}else{
			$condition='`dispatch`.killed=0';
		}
		$find=$this->kclass->findDispatch(array('created'=>$this->kclass->input['created'],'quantity'=>$this->kclass->input['quantity'],'finishquantity'=>$this->kclass->input['finishquantity'],'workdate'=>$this->kclass->input['workdate'],'finishdate'=>$this->kclass->input['finishdate'],'workcenterid'=>$this->kclass->input['workcenterid'],'materialno'=>$this->kclass->input['materialno'],'materialtitle'=>$this->kclass->input['materialtitle'],'dispatchno'=>$this->kclass->input['dispatchno']));
		//查找
		if($this->kclass->input['dispatchno']!='')
		$body.='　派工单编号: "<span class="red">'.$this->kclass->input['dispatchno'].'</span>"　';
		if($this->kclass->input['quantity']!='')
		$body.='　派工数量: "<span class="red">'.$this->kclass->input['quantity'].'</span>"　';
		if($this->kclass->input['finishquantity']!='')
		$body.='　完工数量: "<span class="red">'.$this->kclass->input['finishquantity'].'</span>"　';
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
		$body.='　申请时间: "<span class="red">'.$this->kclass->input['created'].'</span>"　';
		if($body)$body='<div class="toptip"><span class="bold">在派工单中查找：</span>'.$body.'</div>';
		//
		if($this->kclass->input['orderby']!='')$query['orderby']=$this->kclass->input['orderby'];
		if($this->kclass->input['direction']!='')$query['direction']=$this->kclass->input['direction'];
		if($this->kclass->input['show']!='')$query['show']=$this->kclass->input['show'];
		if($this->kclass->input['layout']!='')$query['layout']=$this->kclass->input['layout'];
		$orderby=$this->kclass->orderby(array('module'=>'dispatch','direction'=>'desc','orderby'=>'`dispatch`.created', 'default'=>'created', 'serial'=>array(array('title'=>'编号', 'field'=>'dispatchno'), array('title'=>'ID', 'field'=>'dispatchid'), array('title'=>'修改时间', 'field'=>'modified'), array('title'=>'建立时间', 'field'=>'created')),'appendUrl'=>$query));
		if(is_array($query) AND count($query)>0)$queryPart='&'.http_build_query($query);
		$departmentid=$this->kclass->user['departmentid'];$departments=array(8,9,10);
		$purviews=' AND wc.departmentid='.$departmentid;
		if(in_array($departmentid,$departments))$purviews='';
		//分页
		$perpage=20;
		$dispatchList=$this->kclass->listDispatch(array('prepage'=>$perpage,'find'=>$find,'orderby'=>$query['orderby'],'direction'=>$query['direction'],'show'=>$query['show'],'layout'=>$query['layout']));
		if($this->kclass->input['layout']=='grid'){
				$body.='<ul id="tbody" class="mmlist clear">';
			}else{
				$body.='<table class="hundred mytable tablesorter"><thead><th width="20">ID</th><th width="100">编号</th><th width="50">申请人</th><th width="70">申请时间</th><th width="70">工作中心</th><th width="70">状态</th><th width="80">选项</th></thead><tbody id="tbody">';
			}
		$body.=$dispatchList['body'];
		if($dispatchList['counter']>0){
			if($dispatchList['count']>$dispatchList['counter']){
				$body.='<div class="page" id="dispatchpage"><a href="javascript:dc.tableItem.page('.$perpage.')">点击查看更多</a></div>';
			}
		}
		$body.='<div class="clear"><span class="gray small left">(共<span class="darkred">'.$dispatchList['count'].'</span>条记录)</span></div>';
		//
		$workcenter=$this->kclass->chooserWorkcenter(array('name'=>'workcenterid','width'=>280,'hasBlank'=>1,'topname'=>'所有'));
		$body.='<div class="search_menu" id="search_menu" style="display:none;">
	<form action="/s.php?module=dispatch&action=list" method="get">
<input type="hidden" name="module" value="dispatch">
<input type="hidden" name="action" value="list">
<table style="width:450px">
<thead><tr><th colspan=2>根据条件查找派工单<span class=right>[<a href="#" class="switchsearch">关闭</a>]</span></th></tr></thead>
<tbody>
<tr class="even">
<td width="100">派工单编号：</td><td><input type="text" name="dispatchno"></td>
</tr>
<tr class="odd">
<td width="100">派工数量：</td><td><input type="text" name="quantity"></td>
</tr>
<tr class="even">
<td width="100">完工数量：</td><td><input type="text" name="finishquantity"></td>
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
<td width="100">申请时间：</td><td><input type="text" name="created" id="created"></td>
</tr>
<tr class="odd">
<td class="small gray" colspan="9">
①派工单可以通过输入“派工单编号”，“日期”和“物料编号”进行查找<br>
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
				$layoutLink='<a href="/s.php?module=dispatch&action=list&layout=list'.str_replace('&layout=grid', '', $queryPart).'">列表</a> 格子';
			}else{
				$layoutLink='列表 <a href="/s.php?module=dispatch&action=list&layout=grid'.str_replace('&layout=list', '', $queryPart).'">格子</a>';
			}
		$showLink='<a href="/s.php?module=dispatch&action=list">默认</a> <a href="/s.php?module=dispatch&action=list&show=all">所有<span class="small gray">(包括删除)</span></a> <a href="/s.php?module=dispatch&action=list&show=unlock">未锁定</a>  <a href="/s.php?module=dispatch&action=list&show=locked">已锁定</a>  <a href="/s.php?module=dispatch&action=list&show=inProduction">生产中</a>  <a href="/s.php?module=dispatch&action=list&show=Done">生产完成</a>';
		$this->kclass->page['onload']='dc.tabhover();$(\'.mytable\').fixedtableheader();dc.switchsearch();$(\'#date,'.$id.'\').datepicker();';
		$this->kclass->page['title'].=' - 列表';
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl . ' - 列表','right'=> '<a href="#" class="switchsearch">查找</a>　 | 　<span class="small">排序：</span>'.$orderby['link'].'<span class="small">显示方式：</span>'.$showLink.'　|　<span class="small">布局：</span>'.$layoutLink.'　|　<a href="/s.php?module=dispatch&action=add">新建</a>','body'=>$body));
	}
	//
	//find页面需要完善，可作为高级搜索，设置更多的查询条件 --gaowenfei 2013/11/25 8:45
	function find(){
		if($this->kclass->input['requestMethod']!='post'){
			$workcenter=$this->kclass->chooserWorkcenter(array('name'=>'workcenterid','width'=>280,'hasBlank'=>1,'topname'=>'所有'));
			$body=<<<EOF
<form action="/s.php?module=dispatch&action=list" method="get">
<input type="hidden" name="module" value="dispatch">
<input type="hidden" name="action" value="list">
<table style="width:450px">
<thead><tr><th colspan=2>根据条件查找派工单</th></tr></thead>
<tbody>
<tr class="even">
<td width="100">派工单编号：</td><td><input type="text" name="dispatchno"></td>
</tr>
<tr class="odd">
<td width="100">派工数量：</td><td><input type="text" name="quantity"></td>
</tr>
<tr class="even">
<td width="100">完工数量：</td><td><input type="text" name="finishquantity"></td>
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
<td width="100">申请时间：</td><td><input type="text" name="created" id="created"></td>
</tr>
<tr class="odd">
<td class="small gray" colspan="9">
①派工单可以通过输入“派工单编号”，“日期”和“物料编号”进行查找<br>
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

			$this->kclass->page['title'].='查找生产派工单';
			$this->kclass->page['onload'].='dc.listhover();$(\'#date,'.$id.'\').datepicker();';
			$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - 查找生产派工单','right' => '<a href="/s.php?module=dispatch">查看列表</a>','body'=>$body));
		}
	}
	// 
	function view(){
		$dispatch=$this->kclass->DB->queryFirst("
			SELECT d.*,
				ms.title AS status,
				wc.title AS workcenter,
			u.username AS creator,
			us.username AS modifier,
			ur.username AS verifier
			FROM dispatch AS d
			LEFT JOIN user AS u ON (u.userid=d.creator)
			LEFT JOIN user AS us ON (us.userid=d.modifier)
			LEFT JOIN user AS ur ON (ur.userid=d.verifier)
			LEFT JOIN mftstatus AS ms ON (ms.statusid=d.statusid)
			LEFT JOIN workcenter AS wc ON (wc.workcenterid=d.workcenterid)
			WHERE d.dispatchid='".$this->kclass->input['dispatchid']."'
			LIMIT 0,1
		");
		if($dispatch){
			$dispatch['created']=date('Y-m-d H:i:s',$dispatch['created']);
			$verify=$this->kclass->verify($dispatch['ifVerify']);
			$verifyRemark='<td></td><td></td>';
			if($dispatch['modified']!=0)$modify='，由 '.$dispatch['modifier'].' 于 '.date('Y-m-d H:i:s',$dispatch['modified']).' 修改';
			if($dispatch['verified']!=0){
				$pverify='，由 '.$dispatch['verifier'].' 于 '.date('Y-n-d H:i:s',$dispatch['verified']).' 审核';
				$verifyRemark='<td>审核备注：</td><td>'.$dispatch['verifyRemark'].'</td>';
			}
			if($dispatch['ifVerify']==0){
				$statusid=2;
				$updatedp='<span class="right"><a href="/s.php?module=dispatch&action=update&dispatchid='.$dispatch['dispatchid'].'">修改派工单明细</a></span>';
				$verifyOperate='|　<a href="/s.php?module=dispatch&action=update&dispatchid='.$dispatch['dispatchid'].'">修改</a>　|　<a href="/s.php?module=dispatch&action=verify&dispatchid='.$dispatch['dispatchid'].'">审核</a>';
			}elseif($dispatch['ifVerify']==-1){
				$statusid=2;
				$updatedp='<span class="right"><a href="/s.php?module=dispatch&action=update&dispatchid='.$dispatch['dispatchid'].'">修改派工单明细</a></span>';
				$verifyOperate='|　<a href="/s.php?module=dispatch&action=update&dispatchid='.$dispatch['dispatchid'].'">修改</a> ';
			}elseif($dispatch['ifVerify']==1 AND $dispatch['statusid']<>4){
				$statusid=3;
				$verifyOperate='';
				$updatedp='<span class="right"><a href="/s.php?module=dispatch&action=addQuantity&dispatchid='.$dispatch['dispatchid'].'">新增生产记录</a></span>';
			}elseif($dispatch['statusid']==4){
				$statusid=4;
				$verifyOperate='';
				$updatedp='';
			}
			//申请加工的物资
			$items=$this->kclass->DB->query("
				SELECT di.*,
					mv.title AS version,
					t.taskno,
					ti.quantity AS taskquantity,ti.finishquantity AS taskfinishquantity,
					r.title AS routing,
					ms.title AS status,
					wc.title AS workcenter,
					u.username AS creator,
				us.username AS modifier
				FROM `dispatchitem` AS di
				LEFT JOIN user AS u ON (u.userid=di.creator)
				LEFT JOIN user AS us ON (us.userid=di.modifier)
				LEFT JOIN `materialversion` AS mv ON (mv.versionid=di.versionid)
				LEFT JOIN mftstatus AS ms ON (ms.statusid=di.statusid)
				LEFT JOIN task AS t ON (t.taskid=di.taskid)
				LEFT JOIN taskitem AS ti ON (ti.itemid=di.taskitemid)
				LEFT JOIN routing AS r ON (r.routingid=ti.routingid)
				LEFT JOIN workcenter AS wc ON (wc.workcenterid=di.workcenterid)
				WHERE di.killed=0 AND di.dispatchid='".$dispatch['dispatchid']."'
				ORDER BY di.itemid ASC
			");
			if($this->kclass->DB->numRows()){
				$i=1;$m=1;$k=1;$j=1;
				while($item=$this->kclass->DB->fetchArray($items)){
					$item['workdate']=date('Y-m-d',$item['workdate']);
					$unfinished=$item['taskquantity']-$item['taskfinishquantity'];
					if($unfinished<0)$unfinished=0;
					$materialInfo=$this->kclass->getMaterial(array('materialid'=>$item['materialid'],'itemid'=>$item['materialitemid']));
					$itemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').' small"  align="left">
						<td>'.$i.'</td>
						<td><a href="/s.php?module=task&action=view&taskid='.$item['taskid'].'">'.$item['taskno'].'</a></td>
						<td><a href="/s.php?module=workcenter&action=view&workcenterid='.$item['workcenterid'].'">'.$item['workcenter'].'</a></td>
						<td width="400"><a href="'.$materialInfo['url'].'">'.$materialInfo['no'].'</a> '.$materialInfo['material'].' '.$materialInfo['standard'].'</td>
						<td>'.$item['version'].'</td>
						<td width="210"><a href="/s.php?module=routing&action=list">'.$item['routing'].'</a></td>
						<td><span class="bold">'.number_format($item['taskquantity']).'</span> '.$materialInfo['unit'].'</td>
						<td><span class="bold">'.number_format($item['taskfinishquantity']).'</span> '.$materialInfo['unit'].'</td>
						<td><span class="bold">'.number_format($unfinished).'</span> '.$materialInfo['unit'].'</td>
						<td align="right"><span class="bold darkred">'.$item['quantity'].'</span> '.$materialInfo['unit'].'</td>
						<td align="right"><span class="bold darkred">'.$item['workdate'].'</span></td>
						<td><span class="bold darkred">'.date('Y-m-d',$item['batchTime']).'</span></td>
						<td align="right"><span class="bold darkred">'.$item['dispatchnumber'].'</span> 人</td>
						<td align="right"><span class="bold darkred">'.$item['workhours'].'</span> 时</td>
						<td>'.$item['remark'].'</td></tr>';
					//工序
					$dcs=$this->kclass->DB->query("
						SELECT dc.*,
							d.dispatchid,d.dispatchno,
							s.title AS status,
							wc.workcenterid,wc.title AS workcenter,
							r.title AS routing,
							p.title AS proce,p.step
						FROM `dispatchrecord` AS dc
						LEFT JOIN mftstatus AS s ON (s.statusid=dc.statusid)
						LEFT JOIN dispatchitem AS di ON (di.itemid=dc.dispatchitemid)
						LEFT JOIN dispatch AS d ON (d.dispatchid=di.dispatchid)
						LEFT JOIN workcenter AS wc ON (wc.workcenterid=di.workcenterid)
						LEFT JOIN routing AS r ON (r.routingid=dc.routingid)
						LEFT JOIN `procedure` AS p ON (p.procedureid=dc.procedureid)
						WHERE dc.killed=0 AND dc.finishquantity<>0 AND dc.dispatchitemid=".$item['itemid']."
						ORDER BY dc.finishdate ASC,dc.ordering ASC,dc.modified DESC, dc.created DESC
					");
					if($this->kclass->DB->numRows()){
						while($dc=$this->kclass->DB->fetchArray($dcs)){
							$record.='<tr class="'.$this->kclass->rotateLine(). ' small">
								<td>'.$k.'</td>
								<td><a href="'.$materialInfo['url'].'">'.$materialInfo['no'].'</a> '.$materialInfo['material'].' '.$materialInfo['standard'].'</td>
								<td><span class="small"><a href="/s.php?module=routing&action=list">'.$dc['routing'].'</a>
								</span></td>
								<td>'.$dc['proce'].'</td>
								<td>'.$dc['ordering'].'</td>
								<td><span class="bold darkred">'.number_format($dc['finishquantity']).'</span></td>
								<td><span class="bold">'.number_format($dc['badquantity']).'</span></td>
								<td><span class="bold">'.number_format($dc['discardquantity']).'</span></td>
								<td><span class="bold darkred">'.$dc['finishhours'].'</span> 时</td>
								<td><span class="bold darkred">'.date('Y-m-d H:i:s',$dc['finishdate']).'</span></td>
								<td><span class="bold">'.$dc['status'].'</span></td>
								<td><a href="/s.php?module=dispatch&action=changeStatus&recordid='.$dc['recordid'].'">'.$this->kclass->iif($dc['statusid']==4,'继续生产','结束生产').'</a></td>
							</tr>';
							$k++;
						}
					}
					//工序明细
					$dcitems=$this->kclass->DB->query("
						SELECT dci.*,
							d.dispatchid,d.dispatchno,
							di.materialid,di.materialitemid,di.versionid,di.taskitemid,
							wc.workcenterid,wc.title AS workcenter,
							r.title AS routing,
							p.title AS proce,p.step,p.type
						FROM `dispatchrecorditem` AS dci
						LEFT JOIN dispatchitem AS di ON (di.itemid=dci.dispatchitemid)
						LEFT JOIN dispatch AS d ON (d.dispatchid=di.dispatchid)
						LEFT JOIN workcenter AS wc ON (wc.workcenterid=di.workcenterid)
						LEFT JOIN routing AS r ON (r.routingid=dci.routingid)
						LEFT JOIN `procedure` AS p ON (p.procedureid=dci.procedureid)
						WHERE dci.killed=0 AND dci.finishquantity<>0 AND dci.dispatchitemid=".$item['itemid']."
						ORDER BY dci.finishdate ASC,dci.ordering ASC,dci.modified DESC, dci.created DESC
					");
					if($this->kclass->DB->numRows()){
						while($dcitem=$this->kclass->DB->fetchArray($dcitems)){
							$op='';
							if($dcitem['type']==1){
								$taskitem=$this->kclass->DB->queryFirst("SELECT batchid,barcodeid FROM taskitem WHERE itemid={$dcitem['taskitemid']} LIMIT 0,1");
								$startno=$this->kclass->DB->queryFirst("
									SELECT number
									FROM childbarcode 
									WHERE versionid={$dcitem['versionid']} AND barcodeid={$taskitem['barcodeid']} 
									ORDER BY number ASC 
									LIMIT 0,1
								");
								$endno=$this->kclass->DB->queryFirst("
									SELECT number
									FROM childbarcode 
									WHERE versionid={$dcitem['versionid']} AND recorditemid={$dcitem['itemid']} 
									ORDER BY number DESC 
									LIMIT 0,1
								");
								$childBarcode=$this->kclass->DB->queryFirst("SELECT COUNT(*) AS count FROM childbarcode WHERE versionid={$item['versionid']} AND barcodeid={$taskitem['barcodeid']} LIMIT 0,1");
								$info='共<b>'.$this->kclass->iif($childBarcode['count'],$childBarcode['count'],0).'</b>条';
								if($childBarcode['count']!=$dcitem['finishquantity']){
									$quantity=$dcitem['finishquantity']-$childBarcode['count'];
									//$param="{'id':'item{$j}','q':'{$quantity}'}";
									$status='<a href="javascript:dc.barcode.addBarcode(\'item'.$j.'\')">生成流水条码</a>';
								}else{
									$status='生成完成';
								}

								$op='<input type="hidden" value="'.$quantity.'" name="quantity" >
										<input type="hidden" value="'.$dcitem['itemid'].'" name="recorditemid">
										<input type="hidden" value="'.$taskitem['barcodeid'].'" name="barcodeid">
										<input type="hidden" value="'.$dcitem['versionid'].'" name="versionid">
										<input type="hidden" value="'.$childBarcode['count'].'" name="count" >
										序列号：起始号：<input type="text" name="startno" value="'.$this->kclass->iif($startno['number'],str_pad($startno['number'],4,'0',STR_PAD_LEFT),'0000').'" size="7" /> — 结束号：<span class="endno">'.$this->kclass->iif($endno['number'],str_pad($endno['number'],4,'0',STR_PAD_LEFT),'0000').'</span>
										<span class="info">'.$info.'</span><br />
										状态：<span class="status">'.$status.'</span>';
							}elseif($dcitem['type']==2){
								$serviceno=$this->kclass->DB->queryFirst("SELECT COUNT(*) AS count FROM serviceno WHERE recorditemid='".$dcitem['itemid']."' AND versionid='".$dcitem['versionid']."' LIMIT 0,1");
								$startno=$this->kclass->DB->queryFirst("SELECT serviceno FROM serviceno WHERE recorditemid='".$dcitem['itemid']."' AND versionid='".$dcitem['versionid']."' ORDER BY serviceno ASC LIMIT 0,1");
								$endno=$this->kclass->DB->queryFirst("SELECT serviceno FROM serviceno WHERE recorditemid='".$dcitem['itemid']."' AND versionid='".$dcitem['versionid']."' ORDER BY serviceno DESC LIMIT 0,1");
								$status=$info='';
								if($serviceno['count']==$dcitem['finishquantity']){
									$status='生成完成';
								}elseif($serviceno['count']<$dcitem['finishquantity'] AND $serviceno['count']!=0){
									$status='<a href="javascript:dc.barcode.addServiceNo(\'item'.$j.'\')">继续生成快速服务代码</a>';
								}else{
									$status='<a href="javascript:dc.barcode.addServiceNo(\'item'.$j.'\')">生成快速服务代码</a>';
								}
								$info='共<b>'.$this->kclass->iif($serviceno['count'],$serviceno['count'],0).'</b>条';
								$op='<input type="hidden" value="'.$dcitem['finishquantity'].'" name="quantity" >
										<input type="hidden" value="'.$dcitem['itemid'].'" name="recorditemid">
										<input type="hidden" value="'.$dcitem['versionid'].'" name="versionid">
										<input type="hidden" value="'.$serviceno['count'].'" name="count" >
										序列号：起始号：<input type="text" name="startno" value="'.$startno['serviceno'].'" size="7" /> — 结束号：<span class="endno">'.$this->kclass->iif($endno['serviceno'],$endno['serviceno'],0).'</span>
										<span class="info">'.$info.'</span><br />
										状态：<span class="status">'.$status.'</span>';
							}
							$recorditem.='<tr class="'.$this->kclass->rotateLine(). ' small" id="item'.$j.'">
								<td>'.$j.'</td>
								<td><a href="'.$materialInfo['url'].'">'.$materialInfo['no'].'</a></td>
								<td>'.$dcitem['proce'].'</td>
								<td>'.$dcitem['ordering'].'</td>
								<td><span class="bold darkred">'.number_format($dcitem['finishquantity']).'</span></td>
								<td><span class="bold">'.number_format($dcitem['badquantity']).'</span></td>
								<td><span class="bold">'.number_format($dcitem['discardquantity']).'</span></td>
								<td><span class="bold darkred">'.$dcitem['finishhours'].'</span> 时</td>
								<td><span class="bold darkred">'.date('Y-m-d H:i:s',$dcitem['finishdate']).'</span></td>
								<td>'.$dcitem['workersnumber'].' 人</td>
								<td>'.$dcitem['workers'].'</td>
								<td>'.$dcitem['finishremark'].'</td>
								<td>'.$op.'</td>
								<td><a href="/s.php?module=dispatch&action=updateQuantity&dispatchid='.$item['dispatchid'].'">修改</a>　'.$this->kclass->iif($dcitem['badquantity']>0,'<a href="/s.php?module=dispatch&action=viewrepairRecord&recorditemid='.$dcitem['itemid'].'"> 维修记录</a>','').'</td>
							</tr>';
							$j++;
						}
					}
					if($item['finishquantity']<>0){
						$finishitem.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').' small">
						<td>'.$m.'</td>
						<td width="150">'.$item['batch'].'</td>
							<td width="400"><a href="'.$materialInfo['url'].'">'.$materialInfo['no'].'</a> '.$materialInfo['material'].' '.$materialInfo['standard'].'</td>
						<td>'.$item['version'].'</td>
						<td><span class="bold">'.$item['quantity'].'</span> '.$item['unit'].'</td>
						<td><span class="bold darkred">'.$item['finishquantity'].'</span> '.$item['unit'].'</td>
						<td><span class="bold darkred">'.date('Y-m-d H:i:s',$item['finishdate']).'</span></td>
						<td><span class="bold darkred">'.$item['finishhours'].'</span> 时</td>
						<td>'.$this->kclass->iif($item['ifChooser']==1,'完成','<a href="/s.php?module=task&action=addInbound&workcenterid='.$item['workcenterid'].'">入库</a>').'</td></tr>';
						$m++;
					}
					$i++;
				}
				if($finishitem==''){
					$finishitemtr='';
					$finishitem.='<tr><td colspan="14" class="darkred center">暂无相关记录！</td></tr>';
				}else{
					$finishitemtr='<tr class="even" align="left"><td>ID</td><td>生产批次</td><td>生产物资</td><td>版本</td><td>派工生产数</td><td>完工数量</td><td>完工时间</td><td>完工总用时</td><td width="120">操作</td></tr>';
				}
				if($record==''){
					$recordtr='';
					$record.='<tr><td colspan="16" class="darkred center">暂无相关记录！</td></tr>';
				}else{
					$recordtr='<tr class="even" align="left"><td>ID</td><td>生产物资</td><td>工艺流程</td><td>工序</td><td>排序</td><td>总完工数量</td><td>总不良数量</td><td>总报废数量</td><td>总完工用时</td><td>完工时间</td><td>状态</td><td width="120">操作</td></tr>';
				}
				if($recorditem==''){
					$recorditemtr='';
					$recorditem.='<tr><td colspan="16" class="darkred center">暂无相关记录！</td></tr>';
				}else{
					$recorditemtr='<tr class="even" align="left"><td>ID</td><td>生产物资</td><td>工序</td><td>排序</td><td>完工数量</td><td>不良数量</td><td>报废数量</td><td>完工用时</td><td>完工时间</td><td>作业人数</td><td>作业人员</td><td width="100">完工备注</td><td width="340">其他</td><td width="80">操作</td></tr>';
				}
			}
			$dispatchrepairrecord = $this->kclass->releatedRepairrecord(array('dispatchid'=>$this->kclass->input['dispatchid']));//派工单相关维修单
			$flow=$this->kclass->listFlow(array('module'=>'dispatch','statusid'=>$statusid));
$body=<<<EOF
<div class="title"><span class="right small gray">由 {$dispatch['creator']} 于 {$dispatch['created']} 建立 {$modify}{$pverify}。</span>{$dispatch['dispatchno']}</div>
<dl id="dispatch" class="tabs" style="display: block;">
	<dt tabid="0">基本信息</dt>
	<dt title="与此物资生产任务相关的生产派工申请"{$taskdispatchlist['off']}>生产记录{$taskdispatchlist['count']}</dt>
	<dt title="与此物资生产任务相关的出库单"{$taskoutboundlist['off']}>工序记录{$taskoutboundlist['count']}</dt>
	<dt title="与此物资生产任务相关的入库单"{$dispatchrepairrecord['off']}>维修记录{$dispatchrepairrecord['count']}</dt>
<dd>
<div><span class="right">{$flow}</span></div>
<table class="hundred">
<thead><tr><th colspan="4">生产派工申请单</th></tr></thead>
<tbody>
<tr class="odd">
<td width="100">编　　号：</td><td width="475">{$dispatch['dispatchno']}　（{$verify}）</td>
<td width="100">工作中心：</td><td>{$dispatch['workcenter']}</td>
</tr>
<tr class="even">
<td>申 请 人：</td><td>{$dispatch['applicant']}</td>
<td>申请时间：</td><td>{$dispatch['created']}</td>
</tr>
<tr class="odd">
<td>状    态：</td><td>{$dispatch['status']}</td>
<td>备　　注：</td><td>{$dispatch['remark']}</td>
</tr>
<tr>
{$verifyRemark}
<td></td><td></td>
</tr>
</tbody>
</table>
<table class="hundred">
<thead><tr><th colspan="15">派工生产物资明细{$updatedp}</th></tr></thead>
<tbody>
<tr class="even" align="left">
<td>ID</td><td>任务单</td><td>工作中心</td><td>生产物资</td><td>版本</td><td>工艺流程</td><td>需求数量</td><td>已完成数</td><td>未完成数</td><td align="right">派工生产数</td><td align="right" width="70">投产日期</td><td align="right" width="70">批次日期</td><td align="right">派工人数</td><td align="right">生产工时</td><td width="100">备注</td>
</tr>
{$itemtr}
</tbody>
</table>
<table class="hundred">
<thead><tr><th colspan="14">派工生产记录</th></tr></thead>
<tbody>
{$finishitemtr}
{$finishitem}
</tbody>
</table>
<table class="hundred">
<thead><tr><th colspan="16">派工生产工序记录</th></tr></thead>
<tbody>
{$recordtr}
{$record}
</tbody>
</table>
<table class="hundred">
<thead><tr><th colspan="16">派工生产工序记录明细</th></tr></thead>
<tbody>
{$recorditemtr}
{$recorditem}
</tbody>
</table>
</dd>
<dd>{$taskdispatchlist['panel']}</dd>
<dd>{$taskoutboundlist['panel']}</dd>
<dd>{$dispatchrepairrecord['panel']}</dd>
EOF;
		}else{
			$body='数据错误，很抱歉！';
		}

		$this->kclass->page['title'].=' - '.$dispatch['dispatchno'];
		$this->kclass->page['onload'].='dc.tabhover();dc.tips();dc.tabs({\'id\':\'dispatch\'});dc.show()';
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - 生产派工申请单','right' => '<span class = "small">操作：</span> '.$this->kclass->iif($dispatch['killed']==0,'<a href="/s.php?module=dispatch&action=add">新建</a>　<a href="/s.php?module=dispatch&action=remove&dispatchid='.$dispatch['dispatchid'].'&rt=view">删除</a> &nbsp; '.$verifyOperate,'<a href="/s.php?module=dispatch&action=restore&dispatchid='.$this->kclass->input['dispatchid'].'&rt=view">恢复</a>'),'body'=>$body));
	}
	//
	function add(){
		if($this->kclass->input['workcenterid']==''){
		$departmentid=$this->kclass->user['departmentid'];$departments=array(8,9,10);
		$purviews=' AND departmentid='.$departmentid;
		if(in_array($departmentid,$departments))$purviews='AND killed<>0';
		$wc=$this->kclass->DB->queryFirst("
			SELECT workcenterid
			FROM workcenter
			WHERE killed=0 ".$purviews."
		");
		if($wc['workcenterid']>0)$this->kclass->boinkIt('/s.php?module=dispatch&action=add&workcenterid='.$wc['workcenterid']);
		$workcenter=$this->kclass->chooserWorkcenter(array('name'=>'workcenterid','width'=>300,'hasBlank'=>0,'selectedid'=>1));
		$body=<<<EOF
<form action="/s.php?module=dispatch&action=add" method="get">
<input type="hidden" name="module" value="dispatch">
<input type="hidden" name="action" value="add">
<table style="width:450px;">
<thead><tr><th colspan=2>新建派工申请单：选择工作中心</th></tr></thead>
<tbody>
<tr><td>选择工作中心</td><td>{$workcenter}</td></tr>
<tr class="center"><td colspan=2><input type="submit" value=" 提交 "><input type="reset" value=" 重置 "></td></tr>
</tbody>
</table>
EOF;
		}else{
			$start=strtotime(date('Y-m-d',TIMENOW));
			$end=strtotime(date('Y-m-d',$start))+86400;
			$counter=$this->kclass->DB->queryFirst("SELECT COUNT(*) AS count FROM `dispatch` WHERE created>='".$start."' AND created<='".$end."'");
			$dispatchno=$this->kclass->id(array('dispatch'=>TIMENOW,'number'=>$counter['count']));
			$workcenterid=$this->kclass->input['workcenterid'];
			$wc=$this->kclass->DB->queryFirst("
				SELECT wc.*
					FROM `workcenter` AS wc
					WHERE wc.killed=0 AND wc.workcenterid='".$workcenterid."'
			");
			if(!$wc){
				$e='<li>数据错误，很抱歉</li>';
			}
			if($e){
				$this->kclass->messager(array(
					'title' => '新增派工申请单',
					'text' => '您在新增派工申请单的过程中有以下错误：<ul>'.$e.'</ul>',
					'url' => 'javascript:history.back()',
					'sec' => 3
				));
			}
			if($this->kclass->input['taskitemid']==''){
				$orgItemtr='';
				$taskitemid=0;
			}else{
				$taskitemid=$this->kclass->input['taskitemid'];
				$taskitem=$this->kclass->DB->queryFirst("
					SELECT ti.*,
						mv.title AS version,
						t.taskno,t.tasker,t.remark AS taskremark,t.created AS taskcreated
					FROM `taskitem` AS ti
					LEFT JOIN `materialversion` AS mv ON (mv.versionid=ti.versionid)
					LEFT JOIN task AS t ON (t.taskid=ti.taskid)
					WHERE ti.killed=0 AND ti.statusid<4 AND ti.itemid='".$taskitemid."'
				");
				$outboundItem=$this->kclass->DB->queryFirst("SELECT DISTINCT mid FROM preoutbounditem WHERE killed=0 AND module='outbound' AND taskitemid='".$taskitemid."' LIMIT 0,1");
				$outbound=$this->kclass->DB->queryFirst("SELECT ifComplete FROM outbound WHERE outboundid='".$outboundItem['mid']."'");
				if($outbound['ifComplete']==2 OR $wc['outboundtypeid']==2){
					$unfinished=$taskitem['quantity']-$taskitem['finishquantity'];
					if($unfinished<0)$unfinished=0;
					$taskitem['workdate']=date('Y-m-d',$taskitem['workdate']);
					$taskitem['finishdate']=date('Y-m-d',$taskitem['finishdate']);
					$taskitem['taskcreated']=date('Y-m-d',$taskitem['taskcreated']);
					$materialInfo=$this->kclass->getMaterial(array('materialid'=>$taskitem['materialid'],'itemid'=>$taskitem['materialitemid']));
					$orgItemtr.='<table class="hundred"><thead><th colspan="20">当前派工生产明细</th></thead><tbody><tr><td colspan="20" class="darkred">当前选择的任务单下的条目明细</td></tr><tr><td>ID</td><td>任务单</td><td>物资</td><td>版本</td><td>需求数</td><td>已完成</td><td>未完成</td><td>开工日</td><td>完工日</td><td>需求人数</td><td>需求工时</td><td>生产数量 <span class="red bold">*</span></td><td>批次日期 <span class="red bold">*</span></td><td>投产日期<span class="red bold">*</span></td><td>派工人数<span class="red bold">*</span></td><td>生产工时<span class="red bold">*</span></td><td>备注</td></tr>
					<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').' small">
					<td><input type="hidden" name="sldItemid" value="'.$taskitem['itemid'].'">
					<input type="hidden" name="sldMaterialid" value="'.$taskitem['materialid'].'">
					<input type="hidden" name="sldVersionid" value="'.$taskitem['versionid'].'">
					<input type="hidden" name="sldMaterialitemid" value="'.$taskitem['materialitemid'].'">
					<input type="hidden" name="sldTaskid" value="'.$taskitem['taskid'].'">1</td>
					<td><a href="/s.php?module=task&action=view&taskid='.$taskitem['taskid'].'">'.$taskitem['taskno'].'</a><input type="hidden" name="sldRoutingid" value="'.$taskitem['routingid'].'"></td>
					<td width="350"><a href="'.$materialInfo['url'].'">'.$materialInfo['no'].'</a>　'.$materialInfo['material'].'　'.$materialInfo['standard'].'</td>
					<td>'.$taskitem['version'].'</td>
					<td><span class="bold">'.number_format($taskitem['quantity']).'</span> '.$materialInfo['unit'].'</td>
					<td><span class="bold">'.number_format($taskitem['finishquantity']).'</span> '.$materialInfo['unit'].'</td>
					<td><span class="bold">'.number_format($unfinished).'</span> '.$materialInfo['unit'].'</td>
					<td><span class="bold darkred">'.$taskitem['workdate'].'</span></td>
					<td><span class="bold darkred">'.$taskitem['finishdate'].'</span></td>
					<td><span class="bold darkred">'.$taskitem['dispatchnumber'].'</span> 人</td>
					<td><span class="bold darkred">'.$taskitem['workhours'].'</span> 时</td>
					<td><input type="text" name="sldQuantity" size="6" '.$this->kclass->iif($workcenterid==5,'value="'.$taskitem['quantity'].'"','').'></td>
					<td><input type="text" name="sldBatchTime" size="10" id="batchTime"></td>
					<td><input type="text" name="sldWorkdate" id="workdate" size="10" '.$this->kclass->iif($workcenterid==5,'value="'.$taskitem['workdate'].'"','').'></td>
					<td><input type="text" name="sldDispatchnumber" size="6" '.$this->kclass->iif($workcenterid==5,'value="'.$taskitem['dispatchnumber'].'"','').'><span class="gray small"> 人</span></td>
					<td><input type="text" name="sldWorkhours" size="6" '.$this->kclass->iif($workcenterid==5,'value="'.$taskitem['workhours'].'"','').'><span class="gray small"> 时</span></td>
					<td><input type="text" name="sldRemark" style="width:100px;"></td>
					</tr>';
					$orgItemtr.='</tbody></table>';
					$id.='#workdate,#batchTime,';
				}
			}
			$items=$this->kclass->DB->query("
				SELECT ti.*,
					t.taskno,wc.title AS workcenter,
					s.title AS status,
					mv.title AS version,
					u.username AS creator,
					us.username AS modifier
				FROM `taskitem` AS ti
				LEFT JOIN task AS t ON (t.taskid=ti.taskid)
				LEFT JOIN mftstatus AS s ON (s.statusid=t.statusid)
				LEFT JOIN `materialversion` AS mv ON (mv.versionid=ti.versionid)
				LEFT JOIN user AS u ON (u.userid=ti.creator)
				LEFT JOIN user AS us ON (us.userid=ti.modifier)
				LEFT JOIN workcenter AS wc ON (wc.workcenterid=ti.workcenterid)
				WHERE ti.killed=0 AND ti.statusid<4 AND ti.workcenterid=".$workcenterid." AND ti.itemid<>".$taskitemid."
				ORDER BY ti.itemid DESC, ti.modified DESC, ti.created DESC
			");
			if($counter=$this->kclass->DB->numRows()){
				$i=1;
				$itemtr.='<tr class="even"><td>ID</td><td>任务单</td><td>物资</td><td>版本</td><td>需求数</td><td>已完成</td><td>未完成</td><td>开工日</td><td>完工日</td><td>需求人数</td><td>需求工时</td><td>生产数量 <span class="red bold">*</span></td><td>批次日期 <span class="red bold">*</span></td><td>投产日期 <span class="red bold">*</span></td><td>派工人数<span class="red bold">*</span></td><td>生产工时<span class="red bold">*</span></td><td>备注</td><td>选</td></tr>';
				while($item=$this->kclass->DB->fetchArray($items)){
					$outboundItem=$this->kclass->DB->queryFirst("SELECT DISTINCT mid FROM preoutbounditem WHERE killed=0 AND module='outbound' AND taskitemid='".$item['itemid']."' LIMIT 0,1");
					$outbound=$this->kclass->DB->queryFirst("SELECT ifComplete FROM outbound WHERE outboundid='".$outboundItem['mid']."'");
					if($outbound['ifComplete']==2 OR $wc['outboundtypeid']==2){
						$unfinished=$item['quantity']-$item['finishquantity'];
						if($unfinished<0)$unfinished=0;
						$item['workdate']=date('Y-m-d',$item['workdate']);
						$item['finishdate']=date('Y-m-d',$item['finishdate']);
						$materialInfo=$this->kclass->getMaterial(array('materialid'=>$item['materialid'],'itemid'=>$item['materialitemid']));
						$itemtr.='<tr class="odd small">
							<td><input type="hidden" name="itemId['.$i.']" value="'.$item['itemid'].'" />'.$i.'</td>
							<td><a href="/s.php?module=task&action=view&taskid='.$item['taskid'].'">'.$item['taskno'].'</a><input type="hidden" name="itemRoutingid['.$i.']" value="'.$item['routingid'].'"></td>
							<td width="350"><span class="small"><a href="'.$materialInfo['url'].'">'.$materialInfo['no'].'</a>　'.$materialInfo['material'].' '.$materialInfo['standard'].'</span></td>
							<td>'.$item['version'].'</td>
							<td><span class="bold">'.number_format($item['quantity']).'</span> '.$materialInfo['unit'].'</td>
							<td><span class="bold">'.number_format($item['finishquantity']).'</span> '.$materialInfo['unit'].'</td>
							<td><span class="bold">'.number_format($unfinished).'</span> '.$materialInfo['unit'].'</td>
							<td width="80"><span class="bold darkred">'.$item['workdate'].'</span></td>
							<td width="80"><span class="bold darkred">'.$item['finishdate'].'</span></td>
							<td><span class="bold darkred">'.$item['dispatchnumber'].'</span> 人</td>
							<td><span class="bold darkred">'.$item['workhours'].'</span> 时</td>
							<td><input type="text" name="itemQuantity['.$i.']" size="7" '.$this->kclass->iif($workcenterid==5,'value="'.$item['quantity'].'"','').'></td>
							<td><input type="text" name="batchTime['.$i.']" size="10" id="batchTime'.$i.'"></td>
							<td><input type="text" name="itemWorkdate['.$i.']" id="workdate'.$i.'" size="10" '.$this->kclass->iif($workcenterid==5,'value="'.$item['workdate'].'"','').'></td>
							<td><input type="text" name="itemDispatchnumber['.$i.']" size="7" '.$this->kclass->iif($workcenterid==5,'value="'.$item['dispatchnumber'].'"','').'><span class="gray small"> 人</span></td>
							<td><input type="text" name="itemWorkhours['.$i.']" size="7" '.$this->kclass->iif($workcenterid==5,'value="'.$item['workhours'].'"','').'><span class="gray small"> 时</span></td>
							<td><input type="text" name="itemRemark['.$i.']" style="width:100px;"></td>
							<td><input type="checkbox" name="itemSelect['.$i.']" value="'.$item['itemid'].'"></td>
						</tr>';
						if($i<$counter){
							$id.='#batchTime'.$i.',#workdate'.$i.',';
						}else{
							$id.=',#workdate'.$i;
						}
						$i++;
					}
				}
			}else{
				$itemtr.='<tr><td colspan="20" class="darkred center">暂无相关记录！</td></tr>';
			}
			$flow=$this->kclass->listFlow(array('module'=>'dispatch','statusid'=>1));
$body=<<<EOF
<form action="/s.php?module=dispatch&action=insert" name="dispatch" method="post">
<input type="hidden" name="module" value="dispatch" />
<input type="hidden" name="action" value="insert" />
<input type="hidden" name="dispatchno" value="{$dispatchno}" />
<input type="hidden" name="workcenterid" value="{$workcenterid}" />
<div><span class="right">{$flow}</span></div>
<table class="hundred">
<thead><tr><th colspan="6">基本信息</th></tr></thead>
<tbody>
<tr class="odd">
<td width="100">派工单编号：<span class="red bold">*</span></td><td width="450"><span class="middle bold darkred">{$dispatchno}</span> <span class="gray small">此为预估编号，保存后才可确定</span></td>
<td width="100">工作中心：</td><td width="450">{$wc['title']}</td>
<td width="100">位　　置：</td><td>{$wc['position']}</td>
</tr>
<tr class="even">
<td>人　　数：</td><td>{$wc['quantity']}</td>
<td>工　　时：</td><td>{$wc['workhours']}</td>
<td>效　　率：</td><td>{$wc['efficiency']}</td>
</tr>
<tr class="odd">
<td>负　　荷：</td><td>{$wc['burthen']}</td>
<td>描　　述：</td><td>{$wc['remark']}</td>
<td></td><td></td>
</tr>
</tbody>
<thead></tr><th colspan="6">申请信息</th></tr></thead>
<tbody>
<tr class="odd">
<td>申 请 人：<span class="red bold">*</span></td><td><input type="text" name="applicant" value="{$this->kclass->user['realname']}"></td>
<td>备　　注：</td><td><textarea name="remark" style="width:444px;height:71px"></textarea></td><td></td><td></td>
</tr>
</tbody>
</table>
{$orgItemtr}
<table class="hundred">
<thead></tr><th colspan="20">选择派工生产明细</th></tr></thead>
<tbody>
<tr>
<td colspan="20" class="darkred">工作中心 <span class="bold darkred">{$wc['title']}</span> 的全部任务单下的条目明细 <span class="small gray">(请勾选提交，已完成明细内容的任务单不会显示)</span></td>
</tr>
{$itemtr}
<tr class="even">
<td class="small gray" colspan="20">
注意事项：<br>
　　①编号咱未确定编号规则，所以默认为OM+时间戳<br>
　　②如果所列表格不够，那么在提交保存后再点击“修改”，来增加订单物资；<br>
　　③需要加工的物资从“物资”中选择；<br>
　　④“加工交期 ”的格式为2012-02-06，中间用半角短横杠隔开；<br>
</td>
</tr>
</tbody>
</table>
{$upload}
<table class="tlist tsuite" align="center" width="100%">
<tbody>
<tr class="odd" nohover>
<td align="center" colspan=4><input type="submit" id="submitButton" value="  提交  " accesskey="s"><input type="reset" value="  复位  "/></td>
</tr>
</tbody>
</table>
</form>
EOF;
		}
		$this->kclass->page['onload']='dc.tabhover();dc.tips();$(\'#date,'.$id.'\').datepicker()';
		$this->kclass->page['title'].=' - 新建生产派工申请单';
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl . ' - 新建生产派工申请单','right'=>'<a href="/s.php?module=dispatch">返回列表</a>','body'=>$body));
	}
	//
	function insert(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e.='<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			if($this->kclass->input['applicant'] == ''){
				$e.='<li>请填写派工申请单的 申请人。</li>';
			}
			$ifitem=0;
			if($this->kclass->input['sldItemid']!=''){
				$ifitem=1;
				if($this->kclass->input['sldQuantity'] == ''){
					$e.='<li>请填写派工申请单[当前]派工明细的 生产数量。</li>';
				}
				if($this->kclass->input['sldWorkdate'] == ''){
					$e.='<li>请填写派工申请单[当前]派工明细的 生产日。</li>';
				}
				if($this->kclass->input['sldDispatchnumber'] == ''){
					$e.='<li>请填写派工申请单[当前]派工明细的 派工人数。</li>';
				}
				if($this->kclass->input['sldWorkhours'] == ''){
					$e.='<li>请填写派工申请单[当前]派工明细的 生产工时。</li>';
				}
				if($this->kclass->input['sldBatchTime'] == ''){
					$e.='<li>请填写派工申请单[当前]派工明细的 批次日期。</li>';
				}
			}
			$count=count($this->kclass->input['itemId']);
			for($m=1;$m<=$count;$m++){
				if($this->kclass->input['itemSelect'][$m]!=''){
					$ifitem=1;
					if($this->kclass->input['itemQuantity'][$m]==''){
						$e.='<li>请填写派工申请单[新增]派工明细[ID:'.$m.']的 生产数量</li>';
					}
					if($this->kclass->input['itemWorkdate'][$m]==''){
						$e.='<li>请填写派工申请单[新增]派工明细[ID:'.$m.']的 生产日</li>';
					}
					if($this->kclass->input['itemDispatchnumber'][$m]==''){
						$e.='<li>请填写派工申请单[新增]派工明细[ID:'.$m.']的 派工人数</li>';
					}
					if($this->kclass->input['itemWorkhours'][$m]==''){
						$e.='<li>请填写派工申请单[新增]派工明细[ID:'.$m.']的 生产工时</li>';
					}
					if($this->kclass->input['batchTime'][$m]==''){
						$e.='<li>请填写派工申请单[新增]派工明细[ID:'.$m.']的 批次日期</li>';
					}
				}
			}
			if($ifitem==0){
				$e.='<li>请至少填写一条派工申请单中的 派工明细</li>';
			}
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title'=>'新建生产派工申请单',
				'text'=>'您在新建生产派工申请单的过程中有以下错误：<ul>'.$e.'</ul>',
				'url'=>'javascript:history.back()',
				'sec'=>3
			));
		}
		$this->kclass->DB->query("
			INSERT INTO `dispatch` (`dispatchno`,`applicant`,`statusid`,`workcenterid`,`ifVerify`,`ordering`,`remark`,`creator`,`created`)
			VALUES ('".$this->kclass->input['dispatchno']."','".$this->kclass->input['applicant']."',1,".$this->kclass->input['workcenterid'].",0,0,'".$this->kclass->input['remark']."','".$this->kclass->user['userid']."','".TIMENOW."')
		");
		$dispatchid=$this->kclass->DB->insertID();
		if($this->kclass->input['sldQuantity']!='' AND $this->kclass->input['sldWorkdate']!='' AND $this->kclass->input['sldDispatchnumber']!='' AND $this->kclass->input['sldWorkhours']!='' AND $this->kclass->input['sldBatchTime']!=''){
			$sd=explode('-',$this->kclass->input['sldWorkdate']);
			$sldWorkdate=mktime(0,0,0,$sd[1],$sd[2],$sd[0]);
			if($sldWorkdate=='')$sldWorkdate=0;
			$sd1=explode('-',$this->kclass->input['sldBatchTime']);
			$batchTime=mktime(0,0,0,$sd1[1],$sd1[2],$sd1[0]);
			$this->kclass->DB->query("
				INSERT INTO `dispatchitem` (dispatchid,workcenterid,taskid,taskitemid,workdate,dispatchnumber,workhours,materialid,versionid,materialitemid,statusid,quantity,batchTime,remark,ordering,created,creator) 
				VALUES ('".$dispatchid."','".$this->kclass->input['workcenterid']."','".$this->kclass->input['sldTaskid']."','".$this->kclass->input['sldItemid']."','".$sldWorkdate."','".$this->kclass->input['sldDispatchnumber']."','".$this->kclass->input['sldWorkhours']."','".$this->kclass->input['sldMaterialid']."','".$this->kclass->input['sldVersionid']."','".$this->kclass->input['sldMaterialitemid']."',1,'".$this->kclass->input['sldQuantity']."','".$batchTime."','".$this->kclass->input['sldRemark']."',0,'".TIMENOW."','".$this->kclass->user['userid']."')
			");
			$ditemid=$this->kclass->DB->insertID();
			$procedures=$this->kclass->DB->query("
				SELECT *
				FROM `procedure`  
				WHERE killed=0 AND routingid='".$this->kclass->input['sldRoutingid']."' 
				ORDER BY ordering ASC 
			");
			if($this->kclass->DB->numRows()){
				while($procedure=$this->kclass->DB->fetchArray($procedures)){
					$this->kclass->DB->query("
						INSERT INTO `dispatchrecord` (`dispatchitemid`, `routingid`, `procedureid`, `forecastworkdate`, `forecastworkhours`, `statusid`, `ordering`,`created`, `creator`) 
						VALUES ('".$ditemid."','".$this->kclass->input['sldRoutingid']."','".$procedure['procedureid']."',0,0,1,'".$procedure['ordering']."','".TIMENOW."','".$this->kclass->user['userid']."')
					");
				}
			}
		}
		$count=count($this->kclass->input['itemId']);
		for($m=1;$m<=$count;$m++){
			if($this->kclass->input['itemSelect'][$m]!='' AND $this->kclass->input['itemQuantity'][$m]!='' AND $this->kclass->input['itemWorkdate'][$m]!='' AND $this->kclass->input['itemDispatchnumber'][$m]!='' AND $this->kclass->input['itemWorkhours'][$m]!='' AND $this->kclass->input['batchTime'][$m]!=''){
				if($this->kclass->input['itemWorkdate'][$m]){
					$sd=explode('-',$this->kclass->input['itemWorkdate'][$m]);
					$workdate=mktime(0,0,0,$sd[1],$sd[2],$sd[0]);
				}else{
					$workdate=0;
				}
				$sd1=explode('-',$this->kclass->input['batchTime'][$m]);
				$batchTime=mktime(0,0,0,$sd1[1],$sd1[2],$sd1[0]);
				$item=$this->kclass->DB->queryFirst("
					SELECT ti.itemid,ti.taskid,ti.workcenterid,ti.materialid,ti.versionid,ti.materialitemid,ti.workdate,ti.finishdate,ti.workhours,ti.quantity,ti.remark,
						mv.title AS version
					FROM `taskitem` AS ti
					LEFT JOIN materialversion AS mv ON (mv.versionid=mv.versionid)
					WHERE ti.killed=0 AND ti.itemid='".$this->kclass->input['itemSelect'][$m]."'
				");
				$this->kclass->DB->query("
					INSERT INTO `dispatchitem` (dispatchid,workcenterid,taskid,taskitemid,materialid,versionid,materialitemid,workdate,dispatchnumber,workhours,statusid,quantity,batchTime,remark,ordering,created,creator) 
					VALUES ('".$dispatchid."','".$item['workcenterid']."','".$item['taskid']."','".$this->kclass->input['itemId'][$m]."','".$item['materialid']."','".$item['versionid']."','".$item['materialitemid']."','".$workdate."','".$this->kclass->input['itemDispatchnumber'][$m]."','".$this->kclass->input['itemWorkhours'][$m]."',1,'".$this->kclass->input['itemQuantity'][$m]."','".$batchTime."','".$this->kclass->input['itemRemark'][$m]."',0,'".TIMENOW."','".$this->kclass->user['userid']."')
				");
				$itemid=$this->kclass->DB->insertID();
				$ps=$this->kclass->DB->query("
					SELECT *
					FROM `procedure`  
					WHERE killed=0 AND routingid='".$this->kclass->input['itemRoutingid'][$m]."' 
					ORDER BY ordering ASC 
				");
				if($this->kclass->DB->numRows()){
					while($p=$this->kclass->DB->fetchArray($ps)){
						$this->kclass->DB->query("
							INSERT INTO `dispatchrecord` (`dispatchitemid`, `routingid`, `procedureid`, `forecastworkdate`, `forecastworkhours`, `statusid`, `ordering`,`created`, `creator`) 
							VALUES ('".$itemid."','".$this->kclass->input['itemRoutingid'][$m]."','".$p['procedureid']."',0,0,1,'".$p['ordering']."','".TIMENOW."','".$this->kclass->user['userid']."')
						");
					}
				}
			}
		}
		//$this->kclass->updateAttachs(array('module'=>'dispatch', 'mid'=>$dispatchid));
		$this->kclass->messager(array(
			'title'=>'新建生产派工申请单',
			'text'=>'生产派工申请单 <b>'.$this->kclass->input['dispatchno'].'</b> 已新建成功!',
			'url'=>'/s.php?module=dispatch&action=view&dispatchid='.$dispatchid,
			'sec'=>2
		));
	}
	//
	function update(){
		if($this->kclass->input['dispatchid']<=0 OR !$dispatch=$this->kclass->DB->queryFirst("
			SELECT d.*,
			wc.title AS workcenter
			FROM `dispatch` AS d
			LEFT JOIN workcenter AS wc ON (wc.workcenterid=d.workcenterid)
			WHERE d.killed=0 AND d.dispatchid='".$this->kclass->input['dispatchid']."'
		")){
			$this->kclass->boinkIt('/s.php?module=dispatch');
		}
		$items=$this->kclass->DB->query("
			SELECT di.*,
			t.taskno,wc.title AS workcenter,
			ti.quantity AS taskquantity,ti.finishquantity AS taskfinishquantity,
			mv.title AS version
			FROM `dispatchitem` AS di
			LEFT JOIN task AS t ON (t.taskid=di.taskid)
			LEFT JOIN `materialversion` AS mv ON (mv.versionid=di.versionid)
			LEFT JOIN workcenter AS wc ON (wc.workcenterid=di.workcenterid)
			LEFT JOIN taskitem AS ti ON (ti.itemid=di.taskitemid)
			WHERE di.killed=0 AND di.dispatchid='".$dispatch['dispatchid']."' 
			ORDER BY di.itemid ASC 
		");
		$k=1;
		if($this->kclass->DB->numRows()){
			while($item=$this->kclass->DB->fetchArray($items)){
				$it[]=$item['taskitemid'];
				$unfinished=$item['taskquantity']-$item['taskfinishquantity'];
				if($unfinished<0)$unfinished=0;
				$materialInfo=$this->kclass->getMaterial(array('materialid'=>$item['materialid'],'itemid'=>$item['materialitemid']));
				$itemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').' small">
					<td>'.$k.'<input type="hidden" name="itemId['.$k.']" value="'.$item['itemid'].'"></td>
					<td><a href="/s.php?module=task&action=view&taskid='.$item['taskid'].'">'.$item['taskno'].'</a></td>
					<td>'.$item['workcenter'].'</td>
					<td><span class="small"><a href="'.$materialInfo['url'].'">'.$materialInfo['no'].'</a>　'.$materialInfo['material'].'　'.$materialInfo['standard'].'</span></td>
					<td>'.$item['version'].'</td>
					<td><span class="bold">'.number_format($item['taskquantity']).'</span> '.$materialInfo['unit'].'</td>
					<td><span class="bold">'.number_format($item['taskfinishquantity']).'</span> '.$materialInfo['unit'].'</td>
					<td><span class="bold">'.number_format($unfinished).'</span> '.$materialInfo['unit'].'</td>
					<td><input type="text" name="itemQuantity['.$k.']" value="'.$item['quantity'].'"> '.$materialInfo['unit'].'</td>
					<td><input type="text" name="batchTime['.$k.']" size="10" id="batchTime'.$k.'" value="'.date('Y-m-d',$item['batchTime']).'"></td>
					<td><input type="text" name="itemWorkdate['.$k.']" id="workdate'.$k.'" " value="'.date('Y-m-d',$item['workdate']).'"></td>
					<td><input type="text" name="itemDispatchnumber['.$k.']" value="'.$item['dispatchnumber'].'"> 人</td>
					<td><input type="text" name="itemWorkhours['.$k.']" value="'.$item['workhours'].'"> 时</td>
					<td><input type="text" name="itemRemark['.$k.']" value="'.$item['remark'].'"></td>
					<td><input type="checkbox" name="itemKill['.$k.']" value="'.$item['itemid'].'"></td>
					</tr>';
					$id.='#workdate'.$k.',#batchTime'.$k.',';
					$k++;
				}
			}else{
				$it=array(0);
				$itemtr.='<td colspan="14" class="center darkred">当前派工单明细为空！</td>';
			}
			$itemtr.='<thead></tr><th colspan="15">新建派工生产明细</th></tr></thead><tbody><tr><td colspan="15" class="darkred">全部任务单下的条目明细</td></tr><tr class="center"><td>ID</td><td>任务单</td><td>工作中心</td><td>物资</td><td>版本</td><td>需求数量</td><td>已完成数</td><td>未完成数</td><td>生产数量 <span class="red bold">*</span></td><td>批次日期 <span class="red bold">*</span></td><td>投产日期<span class="red bold">*</span></td><td>派工人数<span class="red bold">*</span></td><td>生产工时<span class="red bold">*</span></td><td>备注</td><td>选</td></tr>';

		$additems=$this->kclass->DB->query("
			SELECT ti.*,
				t.taskno,wc.title AS workcenter,
				mv.title AS version
			FROM `taskitem` AS ti
			LEFT JOIN task AS t ON (t.taskid=ti.taskid)
			LEFT JOIN workcenter AS wc ON (wc.workcenterid=ti.workcenterid)
			LEFT JOIN `materialversion` AS mv ON (mv.versionid=ti.versionid)
			WHERE ti.killed=0 AND ti.routingid<>0 AND ti.statusid<>4 AND ti.itemid NOT IN (".implode(',', $it).") AND ti.workcenterid=".$dispatch['workcenterid']."
			ORDER BY ti.itemid ASC, ti.modified DESC, ti.created DESC
		");
		if($this->kclass->DB->numRows()){
			$i=$k;
			while($additem=$this->kclass->DB->fetchArray($additems)){
				$unfinished=$additem['quantity']-$additem['finishquantity'];
				if($unfinished<0)$unfinished=0;
				$materialInfo=$this->kclass->getMaterial(array('materialid'=>$additem['materialid'],'itemid'=>$additem['materialitemid']));
				$itemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').' small">
					<td><input type="hidden" name="workcenterid['.$i.']" value="'.$additem['workcenterid'].'">
						<input type="hidden" name="taskid['.$i.']" value="'.$additem['taskid'].'">
						<input type="hidden" name="materialid['.$i.']" value="'.$additem['materialid'].'">
						<input type="hidden" name="versionid['.$i.']" value="'.$additem['versionid'].'">
						<input type="hidden" name="materialitemid['.$i.']" value="'.$additem['materialitemid'].'">
						<input type="hidden" name="additemId['.$i.']" value="'.$additem['itemid'].'">
						<input type="hidden" name="itemRoutingid['.$i.']" value="'.$additem['routingid'].'">'.$i.'</td>
					<td><a href="/s.php?module=task&action=view&taskid='.$additem['taskid'].'">'.$additem['taskno'].'</a></td>
					<td>'.$additem['workcenter'].'</td>
					<td><span class="small"><a href="'.$materialInfo['url'].'">'.$materialInfo['no'].'</a>　'.$materialInfo['material'].'　'.$materialInfo['standard'].'</span></td>
					<td>'.$additem['version'].'</td>
					<td><span class="bold">'.number_format($additem['quantity']).'</span> '.$materialInfo['unit'].'</td>
					<td><span class="bold">'.number_format($additem['finishquantity']).'</span> '.$materialInfo['unit'].'</td>
					<td><span class="bold">'.number_format($unfinished).'</span> '.$materialInfo['unit'].'</td>
					<td><input type="text" name="itemQuantity['.$i.']"> '.$materialInfo['unit'].'</td>
					<td><input type="text" name="batchTime['.$i.']" size="10" id="batchTime'.$i.'"></td>
					<td><input type="text" name="itemWorkdate['.$i.']" id="addworkdate'.$i.'"></td>
					<td><input type="text" name="itemDispatchnumber['.$i.']"></td>
					<td><input type="text" name="itemWorkhours['.$i.']"></td>
					<td><input type="text" name="itemRemark['.$i.']"></td>
					<td><input type="checkbox" name="itemSelect['.$i.']"></td>
				</tr>';
				$id.='#addworkdate'.$i.',#batchTime'.$i.',';
				$i++;
			}
		}
$body=<<<EOF
<form action="/s.php?module=dispatch&action=doupdate" name="dispatch" method="post">
<input type="hidden" name="module" value="dispatch" />
<input type="hidden" name="action" value="doupdate" />
<input type="hidden" name="dispatchid" value="{$dispatch['dispatchid']}" />
<table class="hundred">
<thead><tr><th colspan="4">基本信息</th></tr></thead>
<tbody>
<tr class="odd">
<td>生产派工编号：<span class="red bold">*</span></td><td><span class="middle bold darkred">{$dispatch['dispatchno']}</span> <span class="gray small">编号不可修改</span></td>
<td>工作中心</td><td>{$dispatch['workcenter']}</td>
</tr>
<tr class="even">
<td>申 请 人：<span class="red bold">*</span></td><td><input type="text" name="applicant" value="{$dispatch['applicant']}"></td>
<td>备　　注：</td><td><textarea name="remark" style="width:344px;height:50px;">{$dispatch['remark']}</textarea></td>
</tr>
</tbody>
</table>
<table class="hundred">
<thead><tr><th colspan="15">当前派工生产明细</th></tr></thead><tbody><tr><td colspan="15" class="darkred">当前选择的派工单下的条目明细</td></tr><tr class="center"><td>ID</td><td>任务单</td><td>工作中心</td><td>物资</td><td>版本</td><td>需求数量</td><td>已完成数</td><td>未完成数</td><td>生产数量 <span class="red bold">*</span></td><td>批次日期 <span class="red bold">*</span></td><td>投产日期<span class="red bold">*</span></td><td>派工人数<span class="red bold">*</span></td><td>生产工时<span class="red bold">*</span></td><td>备注</td><td>删</td></tr>
{$itemtr}
<tr class="even">
<td class="small gray" colspan="15">
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
		$this->kclass->page['onload']='dc.tabhover();dc.tips();$(\'#date,'.$id.'\').datepicker()';
		$this->kclass->page['title'].=' - 修改生产派工申请单 - '.$dispatch['dispatchno'];
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl . ' - 修改生产派工申请单 - '.$dispatch['dispatchno'],'right'=>'<a href="/s.php?module=dispatch">返回列表</a>','body'=>$body));
	}
	//
	function doupdate(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e='<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			$dispatch=$this->kclass->DB->queryFirst("SELECT dispatchid,dispatchno,statusid FROM dispatch WHERE dispatchid='".$this->kclass->input['dispatchid']."'");
			if(!$dispatch){
				$e .= '<li>您要编辑的派工申请单并不存在，请返回列表刷新后再操作。</li>';
			}
			if($dispatch['statusid']>1){
				$e .= '<li>您要编辑的派工申请单已经[锁定]，无法进行修改。</li>';
			}
			if($this->kclass->input['applicant'] == ''){
				$e='<li>请填写生产派工申请单的 申请人。</li>';
			}
			$count=count($this->kclass->input['itemId']);
			for($m=1;$m<=$count;$m++){
				if($this->kclass->input['itemKill'][$m]==''){
					if($this->kclass->input['itemQuantity'][$m]==''){
						$e.='<li>请填写派工申请单[当前]派工明细[ID:'.$m.']的 生产数量</li>';
					}
					if($this->kclass->input['itemWorkdate'][$m]==''){
						$e.='<li>请填写派工申请单[当前]派工明细[ID:'.$m.']的 生产日</li>';
					}
					if($this->kclass->input['itemDispatchnumber'][$m]==''){
						$e.='<li>请填写派工申请单[当前]派工明细[ID:'.$m.']的 派工人数</li>';
					}
					if($this->kclass->input['itemWorkhours'][$m]==''){
						$e.='<li>请填写派工申请单[当前]派工明细[ID:'.$m.']的 生产工时</li>';
					}
					if($this->kclass->input['batchTime'][$m]==''){
						$e.='<li>请填写派工申请单[当前]派工明细[ID:'.$m.']的 批次日期</li>';
					}
				}
			}
			$addcount=count($this->kclass->input['additemId']);
			for($k=$m;$k<$m+$addcount;$k++){
				if($this->kclass->input['itemSelect'][$k]!=''){
					if($this->kclass->input['itemQuantity'][$k]==''){
						$e.='<li>请填写派工申请单[新增]派工明细[ID:'.$k.']的 生产数量</li>';
					}
					if($this->kclass->input['itemWorkdate'][$k]==''){
						$e.='<li>请填写派工申请单[新增]派工明细[ID:'.$k.']的 生产日</li>';
					}
					if($this->kclass->input['itemDispatchnumber'][$k]==''){
						$e.='<li>请填写派工申请单[新增]派工明细[ID:'.$k.']的 派工人数</li>';
					}
					if($this->kclass->input['itemWorkhours'][$k]==''){
						$e.='<li>请填写派工申请单[新增]派工明细[ID:'.$k.']的 生产工时</li>';
					}
					if($this->kclass->input['batchTime'][$k]==''){
						$e.='<li>请填写派工申请单[新增]派工明细[ID:'.$k.']的 批次日期</li>';
					}
				}
			}
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title'=>'修改生产派工申请单',
				'text'=>'您在修改生产派工申请单的过程中有以下错误：<ul>'.$e.'</ul>',
				'url'=>'javascript:history.back()',
				'sec'=>3
			));
		}
		$this->kclass->DB->query("
			UPDATE `dispatch` SET 
				`applicant`='".$this->kclass->input['applicant']."',
				`remark`='".$this->kclass->input['remark']."',
				`modified`='".TIMENOW."',
				`modifier`='".$this->kclass->user['userid']."',
				`ifVerify`=0
			WHERE dispatchid='".$this->kclass->input['dispatchid']."'
		");
		$count=count($this->kclass->input['itemId']);
		for($m=1;$m<=$count;$m++){
			if($this->kclass->input['itemWorkdate'][$m]!=''){
				$sd=explode('-',$this->kclass->input['itemWorkdate'][$m]);
				$workdate=mktime(0,0,0,$sd[1],$sd[2],$sd[0]);
			}else{
				$workdate=0;
			}
			if($this->kclass->input['batchTime'][$m]!=''){
				$sd=explode('-',$this->kclass->input['batchTime'][$m]);
				$batchTime=mktime(0,0,0,$sd[1],$sd[2],$sd[0]);
			}else{
				$batchTime=0;
			}
			if($this->kclass->input['itemKill']!=''){
				$this->kclass->DB->query("UPDATE `dispatchitem` SET killed=".TIMENOW.",killer='".$this->kclass->user['userid']."'  WHERE itemid='".$this->kclass->input['itemKill'][$m]."'");
			}else{
				$this->kclass->DB->query("
					UPDATE `dispatchitem` SET 
						`quantity`='".$this->kclass->input['itemQuantity'][$m]."',
						`workdate`='".$workdate."',
						`dispatchnumber`='".$this->kclass->input['itemDispatchnumber'][$m]."',
						`workhours`='".$this->kclass->input['itemWorkhours'][$m]."',
						`batchTime`='".$batchTime."',
						`remark`='".$this->kclass->input['itemRemark'][$m]."',
						`modified`='".TIMENOW."',
						`modifier`='".$this->kclass->user['userid']."'
					WHERE itemid='".$this->kclass->input['itemId'][$m]."'
				");
			}
		}
		$addcount=count($this->kclass->input['additemId']);
		for($k=$m;$k<$m+$addcount;$k++){
			if($this->kclass->input['itemWorkdate'][$k]!=''){
				$sd=explode('-',$this->kclass->input['itemWorkdate'][$k]);
				$workdate=mktime(0,0,0,$sd[1],$sd[2],$sd[0]);
			}else{
				$workdate=0;
			}
			if($this->kclass->input['batchTime'][$k]!=''){
				$sd=explode('-',$this->kclass->input['batchTime'][$k]);
				$batchTime=mktime(0,0,0,$sd[1],$sd[2],$sd[0]);
			}else{
				$batchTime=0;
			}
			if($this->kclass->input['itemSelect'][$k]!=''){
				$this->kclass->DB->query("
					INSERT INTO `dispatchitem` (dispatchid,workcenterid,taskid,taskitemid,materialid,versionid,materialitemid,workdate,dispatchnumber,workhours,statusid,quantity,batchTime,remark,ordering,created,creator) 
					VALUES ('".$this->kclass->input['dispatchid']."','".$this->kclass->input['workcenterid'][$k]."','".$this->kclass->input['taskid'][$k]."','".$this->kclass->input['additemId'][$k]."','".$this->kclass->input['materialid'][$k]."','".$this->kclass->input['versionid'][$k]."','".$this->kclass->input['materialitemid'][$k]."','".$workdate."','".$this->kclass->input['itemDispatchnumber'][$k]."','".$this->kclass->input['itemWorkhours'][$k]."',1,'".$this->kclass->input['itemQuantity'][$k]."','".$batchTime."','".$this->kclass->input['itemRemark'][$k]."',0,'".TIMENOW."','".$this->kclass->user['userid']."')
				");
				$itemid=$this->kclass->DB->insertID();
					$ps=$this->kclass->DB->query("
					SELECT *
					FROM `procedure`  
					WHERE killed=0 AND routingid='".$this->kclass->input['itemRoutingid'][$k]."' 
					ORDER BY ordering ASC 
				");
				if($this->kclass->DB->numRows()){
					while($p=$this->kclass->DB->fetchArray($ps)){
						$this->kclass->DB->query("
							INSERT INTO `dispatchrecord` 
								(`dispatchitemid`, `routingid`, `procedureid`, `forecastworkdate`, `forecastworkhours`, `statusid`, `ordering`,`created`, `creator`) 
							VALUES
								('".$itemid."','".$this->kclass->input['itemRoutingid'][$k]."','".$p['procedureid']."',0,0,1,'".$p['ordering']."','".TIMENOW."','".$this->kclass->user['userid']."')
						");
					}
				}
			}
		}

		//$this->kclass->updateAttachs(array('module'=>'dispatch', 'mid'=>$this->kclass->input['dispatchid']));
		$this->kclass->messager(array(
			'title'=>'修改生产派工申请单',
			'text'=>'生产派工申请单 <b>'.$dispatch['dispatchno'].'</b> 的信息已成功修改!',
			'url'=>'/s.php?module=dispatch&action=view&dispatchid='.$this->kclass->input['dispatchid'],
			'sec'=>2
		));
	}
	//查看生产记录
		function viewRecord(){
		if($this->kclass->input['start']==''){
			$b['start']=0;
		}else{
			$b['start']=$this->kclass->input['start'];
		}
		if($this->kclass->input['perpage']=='')$b['perpage']=20;
		if($this->kclass->input['orderby']!='')$query['orderby']=$this->kclass->input['orderby'];
		if($this->kclass->input['direction']!='')$query['direction']=$this->kclass->input['direction'];
		$orderby=$this->kclass->orderby(array('module'=>'dispatch','action'=>'viewRecord','direction'=>'desc','orderby'=>'`dispatch`.modified', 'default'=>'dispatchid', 'serial'=>array(array('title'=>'编号', 'field'=>'dispatchno'), array('title'=>'ID', 'field'=>'dispatchid'), array('title'=>'修改时间', 'field'=>'modified'), array('title'=>'建立时间', 'field'=>'created')),'appendUrl'=>$query));
		$departmentid=$this->kclass->user['departmentid'];$departments=array(8,9,10);
		$purviews=' AND wc.departmentid='.$departmentid;
		if(in_array($departmentid,$departments))$purviews='';
		$dispatchs=$this->kclass->DB->query("
			SELECT `dispatch`.*,
					wc.title AS workcenter,
					ms.title AS status,
				u.username AS creator,
				us.username AS modifier
			FROM dispatch 
			LEFT JOIN user AS u ON (u.userid=`dispatch`.creator)
			LEFT JOIN user AS us ON (us.userid=`dispatch`.modifier)
			LEFT JOIN mftstatus AS ms ON (ms.statusid=`dispatch`.statusid)
			LEFT JOIN workcenter AS wc ON (wc.workcenterid=`dispatch`.workcenterid)
			WHERE `dispatch`.killed=0 AND `dispatch`.ifVerify=1 ".$purviews."
			ORDER BY ".$orderby['sql']."
			LIMIT ".$b['start'].",".$b['perpage']."
		");
		if($this->kclass->DB->numRows()){
			$i=1;
			while($dispatch=$this->kclass->DB->fetchArray($dispatchs)){
				$created=date('Y-m-d', $dispatch['created']);
				$items=$this->kclass->DB->query("
					SELECT di.*,
					t.taskno,
					wc.title AS workcenter,
					s.title AS status,
					mv.title AS version,
					u.username AS creator,
					us.username AS modifier
				FROM `dispatchitem` AS di
				LEFT JOIN task AS t ON (t.taskid=di.taskid)
				LEFT JOIN workcenter AS wc ON (wc.workcenterid=di.workcenterid)
				LEFT JOIN mftstatus AS s ON (s.statusid=t.statusid)
				LEFT JOIN `materialversion` AS mv ON (mv.versionid=di.versionid)
				LEFT JOIN user AS u ON (u.userid=di.creator)
				LEFT JOIN user AS us ON (us.userid=di.modifier)
				WHERE di.killed=0 AND di.finishquantity<>0 AND di.dispatchid=".$dispatch['dispatchid']."
				ORDER BY di.finishdate ASC, di.modified DESC, di.created DESC
				");
				if($this->kclass->DB->numRows()){
					while($item=$this->kclass->DB->fetchArray($items)){
						$materialInfo=$this->kclass->getMaterial(array('materialid'=>$item['materialid'],'itemid'=>$item['materialitemid']));
						$itemtr.='<tr class="'.$this->kclass->rotateLine(). ' small">
							<td>'.$i.'</td>
							<td>'.$item['batch'].'</td>
							<td><span class="small"><a href="/s.php?module=workcenter&action=view&workcenterid='.$item['workcenterid'].'">'.$item['workcenter'].'</a>
							</span></td>
							<td><span class="small"><a href="/s.php?module=dispatch&action=view&dispatchid='.$dispatch['dispatchid'].'">'.$dispatch['dispatchno'].'</a>
							</span></td>
							<td><span class="small"><a href="'.$materialInfo['url'].'">'.$materialInfo['no'].'</a> '.$materialInfo['material'].' '.$materialInfo['standard'].'</span></td>
							<td>'.$item['version'].'</td>
							<td><span class="bold">'.$item['quantity'].'</span> </td>
							<td><span class="bold">'.date('Y-m-d',$item['workdate']).'</span></td>
							<td><span class="bold darkred">'.$item['finishquantity'].'</span> </td>
							<td><span class="bold darkred">'.date('Y-m-d H:i:s',$item['finishdate']).'</span></td>
							<td><span class="bold darkred">'.$item['finishhours'].'</span> </td>
							<td><a href="/s.php?module=dispatch&action=updateQuantity&dispatchid='.$dispatch['dispatchid'].'">修改</a></td>
						</tr>';
								$i++;
					}
				}
			}
		}
		if(!$itemtr){
			$itemtr='<tr><td colspan=14 class="center"><span class="darkred">暂无相关记录</span></td></tr>';
		}
		$body.=<<<EOF
<table class="hundred tablesorter mytable">
<thead>
<tr><th width="35">ID</th><th>生产批次</th><th>工作中心</th><th>派工单</th><th width="550">物资</th><th>版本</th><th>派工数量</th><th>投产日期</th><th>完工数量</th><th>完工时间</th><th width="100">完工总用时</th><th>操作</th></tr>
</thead>
<tbody>
{$itemtr}
</tbody>
</table>
EOF;
		
		$pcount=$this->kclass->DB->queryFirst("SELECT COUNT(dispatchid) AS count FROM `dispatch` LEFT JOIN workcenter AS wc ON (wc.workcenterid=`dispatch`.workcenterid) WHERE `dispatch`.killed=0 AND `dispatch`.ifVerify=1 ".$purviews." ");
		$body.='<div class="clear"><span class="gray small left">(共<span class="darkred">'.$pcount['count'].'</span>组记录)</span></div>';
		$prestart=$b['start']-$b['perpage'];
		if($prestart>=0)$pre='<a href="/s.php?module=dispatch&action=viewRecord&orderby='.$query['orderby'].'&direction='.$query['direction'].'&start='.$prestart.'"><-前一页</a>　';
		$aftstart=$b['start']+$b['perpage'];
		if($aftstart<$pcount['count'])$aft='　<a href="/s.php?module=dispatch&action=viewRecord&orderby='.$query['orderby'].'&direction='.$query['direction'].'&start='.$aftstart.'">后一页-></a>';
		$body.='<div class="clear center"><span class=" middle bold">'.$pre.$aft.'</span></div>';
		//
		$this->kclass->page['onload']="dc.tabhover();dc.tips();$('.tablesorter').tablesorter( {sortList: [[0,0]], headers: { 11: {sorter: false} }} );$('.mytable').fixedtableheader();";
		$this->kclass->page['title'].=' - 生产记录';
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl . ' - 生产记录','right'=> '<span class="small">排序：</span>'.$orderby['link'].'  &nbsp;<span class="small">查看：</span><a href="/s.php?module=dispatch&action=viewRecord">列表</a> &nbsp;<a href="/s.php?module=dispatch&action=viewRecorditem">明细</a> &nbsp;<a href="/s.php?module=dispatch&action=viewProducecounter">统计</a>','body'=>$body));
		}
			//
	//查看生产记录明细
	function viewRecorditem(){
		if($this->kclass->input['start']==''){
			$b['start']=0;
		}else{
			$b['start']=$this->kclass->input['start'];
		}
		if($this->kclass->input['perpage']=='')$b['perpage']=20;
		//
		if($this->kclass->input['show']==''){
			$show='list';
		}else{
			$show=$this->kclass->input['show'];
		}
		$departmentid=$this->kclass->user['departmentid'];$departments=array(8,9,10);
		$purviews=' AND wc.departmentid='.$departmentid;
		if(in_array($departmentid,$departments))$purviews='';
		$dispatchs=$this->kclass->DB->query("
			SELECT di.*,
					wc.title AS workcenter,
					ms.title AS status,
				u.username AS creator,
				us.username AS modifier
			FROM dispatchitem AS di
			LEFT JOIN `dispatch` ON (`dispatch`.dispatchid=di.dispatchid)
			LEFT JOIN user AS u ON (u.userid=di.creator)
			LEFT JOIN user AS us ON (us.userid=di.modifier)
			LEFT JOIN mftstatus AS ms ON (ms.statusid=di.statusid)
			LEFT JOIN workcenter AS wc ON (wc.workcenterid=di.workcenterid)
			WHERE di.killed=0 ".$purviews."
			ORDER BY di.finishdate ASC,di.ordering ASC,di.modified DESC, di.created DESC
			LIMIT ".$b['start'].",".$b['perpage']."
		");
		if($this->kclass->DB->numRows()){
			$i=1;
			while($dispatch=$this->kclass->DB->fetchArray($dispatchs)){
				$created=date('Y-m-d', $dispatch['created']);
				if($show=='list'){
					$items=$this->kclass->DB->query("
						SELECT dc.*,
						d.dispatchid,d.dispatchno,
						wc.workcenterid,wc.title AS workcenter,
						r.title AS routing,
						p.title AS proce
					FROM `dispatchrecord` AS dc
					LEFT JOIN dispatchitem AS di ON (di.itemid=dc.dispatchitemid)
					LEFT JOIN dispatch AS d ON (d.dispatchid=di.dispatchid)
					LEFT JOIN workcenter AS wc ON (wc.workcenterid=di.workcenterid)
					LEFT JOIN routing AS r ON (r.routingid=dc.routingid)
					LEFT JOIN `procedure` AS p ON (p.procedureid=dc.procedureid)
					WHERE dc.killed=0 AND dc.finishquantity<>0 AND dc.dispatchitemid=".$dispatch['itemid']."
					ORDER BY dc.finishdate ASC,dc.ordering ASC,dc.modified DESC, dc.created DESC
					");
					$itemth='<thead><tr><th width="35">ID</th><th width="100">派工单</th><th width="80">工作中心</th><th width="260">工艺流程</th><th width="150">工序</th><th width="30">排序</th><th width="80">预计工作日</th><th width="80">预计工时</th><th width="100">总完工数量</th><th width="100">总不良数量</th><th width="100">总报废数量</th><th width="100">总完工用时</th><th>总完工时间</th><th width="30">操作</th></tr></thead><tbody>';
					if($this->kclass->DB->numRows()){
						while($item=$this->kclass->DB->fetchArray($items)){
							$itemtr.='<tr class="'.$this->kclass->rotateLine(). ' small">
								<td>'.$i.'</td>
								<td><span class="small"><a href="/s.php?module=dispatch&action=view&dispatchid='.$item['dispatchid'].'">'.$item['dispatchno'].'</a>
								</span></td>
								<td><span class="small"><a href="/s.php?module=workcenter&action=view&workcenterid='.$item['workcenterid'].'">'.$item['workcenter'].'</a>
								</span></td>
								<td><span class="small"><a href="/s.php?module=routing&action=list">'.$item['routing'].'</a>
								</span></td>
								<td>'.$item['proce'].'</td>
								<td>'.$item['ordering'].'</td>
								<td>'.$item['forecastworkdate'].'</td>
								<td>'.$item['forecastworkhours'].'</td>
								<td><span class="bold darkred">'.$item['finishquantity'].'</span></td>
								<td><span class="bold">'.$item['badquantity'].'</span></td>
								<td><span class="bold">'.$item['discardquantity'].'</span></td>
								<td><span class="bold darkred">'.$item['finishhours'].'</span> </td>
								<td><span class="bold darkred">'.date('Y-m-d H:i:s',$item['finishdate']).'</span></td>
								<td><a href="/s.php?module=dispatch&action=updateQuantity&dispatchid='.$dispatch['dispatchid'].'">修改</a></td>
							</tr>';
									$i++;
						}
					}
					$this->kclass->page['onload']="$('.tablesorter').tablesorter( {sortList: [[0,0]], headers: { 5: { sorter: false}, 6: {sorter: false}, 7: {sorter: false}, 13: { sorter: false} }} );";
				}elseif($show=='item'){
					$items=$this->kclass->DB->query("
						SELECT dc.*,
						d.dispatchid,d.dispatchno,
						wc.workcenterid,wc.title AS workcenter,
						r.title AS routing,
						p.title AS proce
					FROM `dispatchrecorditem` AS dc
					LEFT JOIN dispatchitem AS di ON (di.itemid=dc.dispatchitemid)
					LEFT JOIN dispatch AS d ON (d.dispatchid=di.dispatchid)
					LEFT JOIN workcenter AS wc ON (wc.workcenterid=di.workcenterid)
					LEFT JOIN routing AS r ON (r.routingid=dc.routingid)
					LEFT JOIN `procedure` AS p ON (p.procedureid=dc.procedureid)
					WHERE dc.killed=0 AND dc.finishquantity<>0 AND dc.dispatchitemid=".$dispatch['itemid']."
				ORDER BY dc.finishdate ASC,dc.ordering ASC,dc.modified DESC, dc.created DESC
					");
					$itemth='<thead><tr><th width="35">ID</th><th width="80">派工单</th><th width="80">工作中心</th><th width="240">工艺流程</th><th width="80">工序</th><th width="40">排序</th><th width="100">完工数量</th><th width="100">不良数量</th><th width="100">报废数量</th><th width="100">完工用时</th><th width="150">完工时间</th><th width="80">作业人数</th><th width="200">作业人员</th><th width="100">完工备注</th><th width="30">操作</th></tr></thead><tbody>';
					if($this->kclass->DB->numRows()){
						while($item=$this->kclass->DB->fetchArray($items)){
							$itemtr.='<tr class="'.$this->kclass->rotateLine(). ' small">
								<td>'.$i.'</td>
								<td><span class="small"><a href="/s.php?module=dispatch&action=view&dispatchid='.$item['dispatchid'].'">'.$item['dispatchno'].'</a>
								</span></td>
								<td><span class="small"><a href="/s.php?module=workcenter&action=view&workcenterid='.$item['workcenterid'].'">'.$item['workcenter'].'</a>
								</span></td>
								<td><span class="small"><a href="/s.php?module=routing&action=list">'.$item['routing'].'</a>
								</span></td>
								<td>'.$item['proce'].'</td>
								<td>'.$item['ordering'].'</td>
								<td><span class="bold darkred">'.$item['finishquantity'].'</span></td>
								<td><span class="bold">'.$item['badquantity'].'</span></td>
								<td><span class="bold">'.$item['discardquantity'].'</span></td>
								<td><span class="bold darkred">'.$item['finishhours'].'</span> </td>
								<td><span class="bold darkred">'.date('Y-m-d H:i:s',$item['finishdate']).'</span></td>
								<td>'.$item['workersnumber'].' 人</td>
								<td>'.$item['workers'].'</td>
								<td>'.$item['finishremark'].'</td>
								<td><a href="/s.php?module=dispatch&action=updateQuantity&dispatchid='.$dispatch['dispatchid'].'">修改</a></td>
							</tr>';
									$i++;
						}
					}
					$this->kclass->page['onload']="$('.tablesorter').tablesorter( {sortList: [[0,0]], headers: { 5: { sorter: false}, 14: { sorter: false} }} );";
				}
			}
		}else{
			$itemtr='<tr><td colspan=18 class="center"><span class="darkred">暂无相关记录</span></td></tr>';
		}
				$body.=<<<EOF
<table class="hundred tablesorter mytable">
{$itemth}
{$itemtr}
</tbody>
</table>
EOF;
		$pcount=$this->kclass->DB->queryFirst("SELECT COUNT(itemid) AS count FROM `dispatchitem` AS di LEFT JOIN workcenter AS wc ON (wc.workcenterid=di.workcenterid) WHERE di.killed=0 ".$purviews." ");
		$body.='<div class="clear"><span class="gray small left">(共<span class="darkred">'.$pcount['count'].'</span>组记录)</span></div>';
		$prestart=$b['start']-$b['perpage'];
		if($prestart>=0)$pre='<a href="/s.php?module=dispatch&action=viewRecorditem&show='.$show.'&start='.$prestart.'"><-前一页</a>　';
		$aftstart=$b['start']+$b['perpage'];
		if($aftstart<$pcount['count'])$aft='　<a href="/s.php?module=dispatch&action=viewRecorditem&show='.$show.'&start='.$aftstart.'">后一页-></a>';
		$body.='<div class="clear center"><span class=" middle bold">'.$pre.$aft.'</span></div>';
		//
		$this->kclass->page['onload'].="dc.tabhover();dc.tips();$('.mytable').fixedtableheader();";
		$this->kclass->page['title'].=' - 生产工序记录';
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl . ' - 生产工序记录','right'=> '<span class="small">显示方式：</span><a href="/s.php?module=dispatch&action=viewRecorditem&show=list">工序</a>&nbsp; <a href="/s.php?module=dispatch&action=viewRecorditem&show=item">工序明细</a> &nbsp;  &nbsp;<span class="small">查看：</span><a href="/s.php?module=dispatch&action=viewRecord">列表</a> &nbsp;<a href="/s.php?module=dispatch&action=viewRecorditem">明细</a> &nbsp;<a href="/s.php?module=dispatch&action=viewProducecounter">统计</a>','body'=>$body));
		}
			//
		//查看生产统计 目前只是简单实现 后续开发可进行数据挖掘 数据分析 图表展示 --gaowenfei 2013/11/25 10:03
		function viewProducecounter(){
			if($this->kclass->input['start']==''){
				$b['start']=0;
			}else{
				$b['start']=$this->kclass->input['start'];
			}
			if($this->kclass->input['perpage']=='')$b['perpage']=20;
			if($this->kclass->input['show']==''){
				$show='procedure';
			}else{
				$show=$this->kclass->input['show'];
			}
			$departmentid=$this->kclass->user['departmentid'];$departments=array(8,9,10);
			$purviews=' AND wc.departmentid='.$departmentid;
			if(in_array($departmentid,$departments))$purviews='';
			if($this->kclass->input['show']=='procedure' OR $this->kclass->input['show']==''){
			$records=$this->kclass->DB->query("
				SELECT DISTINCT d.procedureid
				FROM dispatchrecord AS d
				LEFT JOIN routing AS r ON (r.routingid=d.routingid)
				LEFT JOIN workcenter AS wc ON (wc.workcenterid=r.workcenterid)
				WHERE d.killed=0 AND d.finishquantity<>0 ".$purviews."
				LIMIT ".$b['start'].",".$b['perpage']."
			");
			if($this->kclass->DB->numRows()){
				$i=1;
				while($record=$this->kclass->DB->fetchArray($records)){
					$items=$this->kclass->DB->query("
						SELECT SUM( finishquantity ) AS sumquantity, SUM( finishhours ) AS sumhours
						FROM `dispatchrecord`
						WHERE killed=0 AND finishquantity<>0 AND procedureid=".$record['procedureid']."
					");
					if($this->kclass->DB->numRows()){
						while($item=$this->kclass->DB->fetchArray($items)){
							$p=$this->kclass->DB->queryFirst("
									SELECT p.*,
									wc.workcenterid,wc.title AS workcenter,
									r.title AS routing
									FROM `procedure` AS p
									LEFT JOIN routing AS r ON (r.routingid=p.routingid)
									LEFT JOIN workcenter AS wc ON (wc.workcenterid=r.workcenterid)
									WHERE p.killed=0 AND p.procedureid=".$record['procedureid']."
								");
							$avg=$item['sumquantity']/$item['sumhours'];
							$avg=sprintf("%.2f",$avg);
							$itemtr.='<tr class="'.$this->kclass->rotateLine(). ' small">
								<td>'.$i.'</td>
								<td><span class="small"><a href="/s.php?module=workcenter&action=view&workcenterid='.$p['workcenterid'].'">'.$p['workcenter'].'</a>
								</span></td>
								<td><span class="small"><a href="/s.php?module=routing&action=list">'.$p['routing'].'</a>
								</span></td>
								<td>'.$p['title'].'</td>
								<td>'.$p['ordering'].'</td>
								<td><span class="bold darkred">'.$item['sumquantity'].'</span></td>
								<td><span class="bold darkred">'.$item['sumhours'].'</span> </td>
								<td><span class="bold">'.$avg.'</span></td>
							</tr>';
									$i++;
						}
					}
				}
			}else{
				$itemtr='<tr><td colspan=14 class="center"><span class="darkred">暂无相关记录</span></td></tr>';
			}
			$body.=<<<EOF
<table class="hundred mytable tablesorter">
<thead>
<tr><th width="35">ID</th><th>工作中心</th><th>工艺流程</th><th>工序</th><th>排序</th><th>总完工数量</th><th>总完工用时</th><th>标准用时</th></tr>
</thead>
<tbody>
{$itemtr}
</tbody>
</table>
EOF;
			$pcount=$this->kclass->DB->queryFirst("SELECT COUNT(DISTINCT d.procedureid) AS count FROM `dispatchrecord` AS d LEFT JOIN routing AS r ON (r.routingid=d.routingid) LEFT JOIN workcenter AS wc ON (wc.workcenterid=r.workcenterid) WHERE d.killed=0 AND d.finishquantity<>0 ".$purviews." ");
			$body.='<div class="clear"><span class="gray small left">(共<span class="darkred">'.$pcount['count'].'</span>组记录)</span></div>';
			$prestart=$b['start']-$b['perpage'];
			if($prestart>=0)$pre='<a href="/s.php?module=dispatch&action=viewProducecounter&show='.$show.'&start='.$prestart.'"><-前一页</a>　';
			$aftstart=$b['start']+$b['perpage'];
			if($aftstart<$pcount['count'])$aft='　<a href="/s.php?module=dispatch&action=viewProducecounter&show='.$show.'&start='.$aftstart.'">后一页-></a>';
			$body.='<div class="clear center"><span class=" middle bold">'.$pre.$aft.'</span></div>';
			//
			$this->kclass->page['onload'].="$('.tablesorter').tablesorter( {sortList: [[0,0]], headers: { 4: {sorter: false} }} );";
			}elseif($this->kclass->input['show']=='material'){
			$ms=$this->kclass->DB->query("
			SELECT DISTINCT d.materialid,d.versionid,d.productitemid
			FROM dispatchitem AS d
			LEFT JOIN workcenter AS wc ON (wc.workcenterid=d.workcenterid)
			WHERE d.killed=0 AND d.finishquantity<>0 ".$purviews."
			LIMIT ".$b['start'].",".$b['perpage']."
		");
		if($this->kclass->DB->numRows()){
			$i=1;
			while($m=$this->kclass->DB->fetchArray($ms)){
				$items=$this->kclass->DB->query("
					SELECT SUM( finishquantity ) AS sumquantity, SUM( finishhours ) AS sumhours
					FROM dispatchitem
					WHERE killed=0 AND finishquantity<>0 AND materialid='".$m['materialid']."' AND versionid='".$m['versionid']."' AND productitemid='".$m['productitemid']."'
				");
				if($this->kclass->DB->numRows()){
					while($item=$this->kclass->DB->fetchArray($items)){
						$p=$this->kclass->DB->queryFirst("
							SELECT wc.*
								FROM workcenter AS wc
								LEFT JOIN workcenteritem AS wci ON (wci.workcenterid=wc.workcenterid)
								WHERE wc.killed=0 AND wci.materialid=".$m['materialid']."
							");
						$v=$this->kclass->DB->queryFirst("SELECT title AS version FROM materialversion WHERE versionid='".$m['versionid']."'");
						$materialInfo=$this->kclass->getMaterial(array('materialid'=>$p['materialid'],'itemid'=>$m['materialitemid']));
						$avg=$item['sumquantity']/$item['sumhours'];
						$avg=sprintf("%.2f",$avg);
						$itemtr.='<tr class="'.$this->kclass->rotateLine(). ' small">
							<td>'.$i.'</td>
							<td><span class="small"><a href="/s.php?module=workcenter&action=view&workcenterid='.$p['workcenterid'].'">'.$p['title'].'</a>
							</span></td>
							<td><span class="small"><a href="'.$materialInfo['url'].'">'.$materialInfo['no'].'</a> '.$materialInfo['material'].' '.$materialInfo['standard'].'</span></td>
							<td>'.$v['version'].'</td>
							<td><span class="bold darkred">'.$item['sumquantity'].'</span></td>
							<td><span class="bold darkred">'.$item['sumhours'].'</span> </td>
							<td><span class="bold">'.$avg.'</span></td>
						</tr>';
								$i++;
					}
				}
			}
		}else{
			$itemtr='<tr><td colspan=14 class="center"><span class="darkred">暂无相关记录</span></td></tr>';
		}
		$body.=<<<EOF
<table class="hundred mytable tablesorter">
<thead>
<tr><th width="35">ID</th><th>工作中心</th><th>物资</th><th>版本</th><th>总完工数量</th><th>总完工用时</th><th>标准用时</th></tr>
</thead>
<tbody>
{$itemtr}
</tbody>
</table>
EOF;
		$mcount=$this->kclass->DB->queryFirst("SELECT COUNT(DISTINCT d.materialid) AS count FROM dispatchitem AS d LEFT JOIN workcenter AS wc ON (wc.workcenterid=d.workcenterid) WHERE d.killed=0 AND d.finishquantity<>0 ".$purviews." ");
		$body.='<div class="clear"><span class="gray small left">(共<span class="darkred">'.$mcount['count'].'</span>组记录)</span></div>';
		$prestart=$b['start']-$b['perpage'];
		if($prestart>=0)$pre='<a href="/s.php?module=dispatch&action=viewProducecounter&show='.$show.'&start='.$prestart.'"><-前一页</a>　';
		$aftstart=$b['start']+$b['perpage'];
		if($aftstart<$mcount['count'])$aft='　<a href="/s.php?module=dispatch&action=viewProducecounter&show='.$show.'&start='.$aftstart.'">后一页-></a>';
		$body.='<div class="clear center"><span class=" middle bold">'.$pre.$aft.'</span></div>';
		//
		$this->kclass->page['onload'].="$('.tablesorter').tablesorter( {sortList: [[0,0]]} );";
		}
		$this->kclass->page['onload'].="dc.tabhover();dc.tips();$('.mytable').fixedtableheader();";
		$this->kclass->page['title'].=' - 生产记录统计';
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl . ' - 生产记录统计','right'=> '<span class="small">显示方式： </span><a href="/s.php?module=dispatch&action=viewProducecounter&show=procedure">工序</a>&nbsp; <a href="/s.php?module=dispatch&action=viewProducecounter&show=material">物资</a> &nbsp;&nbsp;<span class="small">查看：</span><a href="/s.php?module=dispatch&action=viewRecord">列表</a> &nbsp;<a href="/s.php?module=dispatch&action=viewRecorditem">明细</a> &nbsp;<a href="/s.php?module=dispatch&action=viewProducecounter">统计</a>','body'=>$body));
		}
			//已作废功能 看需求跟进
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
				$this->kclass->DB->query("
					UPDATE task SET
						statusid='".$this->kclass->input['statusid']."',
						locker='".$this->kclass->user['userid']."',
						locked='".TIMENOW."'
					WHERE taskid='".$this->kclass->input['taskid']."'
				");
				$lock='锁定成功';
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
	//审核派工单
	function verify(){
		$dispatch=$this->kclass->DB->queryFirst("
			SELECT d.*,
					ms.title AS status,
					wc.title AS workcenter,
				u.username AS creator,
				us.username AS modifier,
				ur.username AS verifier
			FROM dispatch AS d
			LEFT JOIN user AS u ON (u.userid=d.creator)
			LEFT JOIN user AS us ON (us.userid=d.modifier)
			LEFT JOIN user AS ur ON (ur.userid=d.verifier)
			LEFT JOIN mftstatus AS ms ON (ms.statusid=d.statusid)
			LEFT JOIN workcenter AS wc ON (wc.workcenterid=d.workcenterid)
			WHERE d.dispatchid='".$this->kclass->input['dispatchid']."'
			LIMIT 0,1
		");
		if(!$dispatch AND $dispatch['ifVerify']!=0){
			$e='<li>数据错误，很抱歉~</li>';
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '审核生产派工申请单',
				'text' => '审核生产派工申请单<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$dispatch['created']=date('Y-m-d',$dispatch['created']);
		$verify=$this->kclass->verify($dispatch['ifVerify']);
		if($dispatch['modified']!=0)$modify='，由'.$dispatch['modifier'].'于'.date('Y-n-d',$dispatch['modified']).'修改';		
		//申请加工的物资
		$items=$this->kclass->DB->query("
			SELECT di.*,
					mv.title AS version,
					t.taskno,
					ms.title AS status,
					wc.title AS workcenter,
					u.username AS creator,
				us.username AS modifier
				FROM `dispatchitem` AS di
				LEFT JOIN user AS u ON (u.userid=di.creator)
				LEFT JOIN user AS us ON (us.userid=di.modifier)
				LEFT JOIN `materialversion` AS mv ON (mv.versionid=di.versionid)
				LEFT JOIN mftstatus AS ms ON (ms.statusid=di.statusid)
				LEFT JOIN task AS t ON (t.taskid=di.taskid)
				LEFT JOIN workcenter AS wc ON (wc.workcenterid=di.workcenterid)
				WHERE di.killed=0 AND di.dispatchid='".$dispatch['dispatchid']."'
				ORDER BY di.itemid ASC
		");
		if($this->kclass->DB->numRows()){
			$i=1;
			while($item=$this->kclass->DB->fetchArray($items)){
				$item['workdate']=date('Y-m-d',$item['workdate']);
				$materialInfo=$this->kclass->getMaterial(array('materialid'=>$item['materialid'],'itemid'=>$item['materialitemid']));
				$itemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').' small">
						<td>'.$i.'</td>
						<td><a href="/s.php?module=task&action=view&taskid='.$item['taskid'].'">'.$item['taskno'].'</a></td>
						<td><a href="/s.php?module=workcenter&action=view&workcenterid='.$item['workcenterid'].'">'.$item['workcenter'].'</a></td>
						<td><a href="'.$materialInfo['url'].'">'.$materialInfo['no'].'</a></td>
						<td>'.$materialInfo['material'].'</td>
						<td>'.$materialInfo['standard'].'</td>
						<td>'.$item['quantity'].' '.$materialInfo['unit'].'</td>
						<td>'.$item['workdate'].'</td>
						<td>'.$item['dispatchnumber'].' 人</td>
						<td>'.$item['workhours'].' 时</td>
						<td>'.$item['remark'].'</td></tr>';
				$i++;
			}
		}

$body=<<<EOF
<div class="title"><span class="right small gray">由{$dispatch['creator']}于{$dispatch['created']}建立{$modify}{$pverify}。</span>{$dispatch['dispatchno']}</div>
<table class="hundred">
<thead><tr><th colspan="4">生产派工申请单</th></tr></thead>
<tbody>
<tr class="odd">
<td width="100">编　　号：</td><td width="475">{$dispatch['dispatchno']}　（{$verify}）</td>
<td width="100">工作中心：</td><td>{$dispatch['workcenter']}</td>
</tr>
<tr class="even">
<td>申 请 人：</td><td>{$dispatch['applicant']}</td>
<td>申请时间：</td><td>{$dispatch['created']}</td>
</tr>
<tr class="odd">
<td>状    态：</td><td>{$dispatch['status']}</td>
<td>备　　注：</td><td>{$dispatch['remark']}</td>
</tr>
</tbody>
</table>
<table class="hundred">
<thead><tr><th colspan="13">物资明细</th></tr></thead>
<tbody>
<tr class="center even">
<td>ID</td><td>任务单</td><td>工作中心</td><td>物资编号</td><td>物资名称</td><td>物资规格</td><td>生产数</td><td>投产日期</td><td>派工人数</td><td>生产工时</td><td>备注</td>
</tr>
{$itemtr}
</tbody>
</table>
<form method="post" action="/s.php?module=dispatch&action=doverify">
<input type="hidden" name="module" value="dispatch">
<input type="hidden" name="action" value="doverify">
<input type="hidden" name="dispatchid" value="{$this->kclass->input['dispatchid']}">
<table>
<thead><tr><th colspan="4">审核</th></tr></thead>
<tbody>
<tr class="even">
	<td>审　　核：<span class="bold red">*</span></td><td class="center"><input type="radio" name="ifVerify" value="1" checked/>通过　　　<input type="radio" name="ifVerify" value="-1" />不通过</td>
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
		$this->kclass->page['title'].='审核生产派工申请单';
		$this->kclass->page['onload'].='dc.tabhover();';
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - <a href="/s.php?module=dispatch&action=view&dispatchid='.$this->kclass->input['dispatchid'].'">查看详细</a> - 审核生产派工申请单', 'right'=>'<a href="/s.php?module=dispatch&action=list">返回列表</a>　|　<a href="/s.php?module=dispatch&action=view&dispatchid='.$this->kclass->input['dispatchid'].'">查看详细</a>','body'=>$body));
	}
	// 
	function doverify(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			$dispatch=$this->kclass->DB->queryFirst("SELECT dispatchno FROM dispatch WHERE dispatchid='".$this->kclass->input['dispatchid']."'");
			if($this->kclass->input['ifVerify']!=1 AND $this->kclass->input['ifVerify']!=-1){
				$e.='<li>请选择派工申请单中的 是否通过审核 按钮。</li>';
			}
		}
		if($e){
			$this->kclass->messager(array(
				'title' => '审核生产派工申请单',
				'text' => '您在审核生产派工申请单的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		if($this->kclass->input['ifVerify']==1){
			$verify='已通过';
			$statusid=2;
			
			$items=$this->kclass->DB->query("
				SELECT di.itemid,di.materialid,di.materialitemid,di.versionid,di.batchTime,di.taskitemid,
					mv.title AS version
				FROM dispatchitem AS di
				LEFT JOIN materialversion AS mv ON (di.versionid=mv.versionid)
				WHERE di.killed=0 AND di.dispatchid={$this->kclass->input['dispatchid']}
				ORDER BY di.itemid ASC 
			");
			if($this->kclass->DB->numRows()){
				while($item=$this->kclass->DB->fetchArray($items)){
					$materialInfo=$this->kclass->getMaterial(array('materialid'=>$item['materialid'],'itemid'=>$item['materialitemid']));
					// 生成批次和条码
					$batchno=$this->kclass->id(array('batch'=>$item['batchTime'],'materialno'=>$materialInfo['no']));
					$sd=explode('-',date('y-n-j',$item['batchTime']));
					$time36='';
					foreach($sd as $val){
						$time36.=strtoupper(base_convert($val,10,36));// 将日期转化为36位进制
					}
					$barcodeno='CN-'.$materialInfo['materialno'].$this->kclass->iif($item['materialitemid']>0,'-'.$materialInfo['no'],'').'-0000-'.$time36.'-'.$item['version'];

					// 获得批次id
					$batch=$this->kclass->DB->queryFirst("SELECT batchid FROM batch WHERE batchno='".$batchno."'");
					if($batch['batchid']>0){
						$batchid=$batch['batchid'];
					}else{
						$this->kclass->DB->query("
							INSERT INTO batch (batchno,materialid,materialitemid,versionid,dateline,created,creator)
							VALUES ('".$batchno."','".$item['materialid']."','".$item['materialitemid']."','".$item['versionid']."','".$item['batchTime']."','".TIMENOW."','".$this->kclass->user['userid']."')
						");
						$batchid=$this->kclass->DB->insertID();
					}
					// 获得条码id
					$barcode=$this->kclass->DB->queryFirst("SELECT barcodeid FROM barcode WHERE barcode='".$barcodeno."'");
					if($barcode['barcodeid']>0){
						$barcodeid=$barcode['barcodeid'];
					}else{
						$this->kclass->DB->query("
							INSERT INTO barcode (materialid,supplierid,versionid,batchid,barcode,country,materialno,supplierno,arrivalTime,version,created,creator)
							VALUES ('".$item['materialid']."','2','".$item['versionid']."','".$batchid."','".$barcodeno."','CN','".$materialInfo['materialno']."','0000','".$item['batchTime']."','".$item['version']."','".TIMENOW."','".$this->kclass->user['userid']."')
						");
						$barcodeid=$this->kclass->DB->insertID();
					}
					$this->kclass->DB->query("UPDATE taskitem SET batchid={$batchid},barcodeid={$barcodeid} WHERE itemid={$item['taskitemid']}");
				}
			}
		}elseif($this->kclass->input['ifVerify']==-1){
			$verify='未通过';
			$statusid=1;
		}
		$this->kclass->DB->query("
			UPDATE dispatch SET
				ifVerify='".$this->kclass->input['ifVerify']."',
				verifier='".$this->kclass->user['userid']."',
				verified='".TIMENOW."',
				statusid='".$statusid."',
				verifyRemark='".$this->kclass->input['verifyRemark']."'
			WHERE dispatchid='".$this->kclass->input['dispatchid']."'
		");
		$this->kclass->messager(array(
			'title' => '审核派工申请单',
			'text' => '派工申请单 【<b>'.$dispatch['dispatchno'].'</b>】 '.$verify.'审核!',
			'url' => '/s.php?module=dispatch&action=view&dispatchid='.$this->kclass->input['dispatchid'],
			'sec' => 2
		));
	}
//
	function kill(){
		if($this->kclass->input['dispatchid']<=0){
			$this->kclass->boinkIt('/s.php?module=dispatch');
		}
		if($this->kclass->input['rt']!='list' AND $this->kclass->input['confirm'] == 0){
			$this->kclass->boinkIt('/s.php?module=dispatch&action=view&dispatchid='.$this->kclass->input['dispatchid']);
		}
		if($this->kclass->input['dispatchid'] < 0){
			$e = '<li>请从正确的页面进行操作，如果此问题持续存在，请联系网站管理员。</li>';
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '删除派工申请单',
				'text' => '您在删除派工申请单的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		if($this->kclass->input['rt']=='view'){
			$rt = '&action=view&dispatchid='.$this->kclass->input['dispatchid'];
		}elseif($this->kclass->input['rt']=='list'){
			$rt = '#'.$this->kclass->input['dispatchid'];
		}
		$dispatch=$this->kclass->DB->queryFirst("
			SELECT dispatchno
			FROM dispatch
			WHERE dispatchid='".$this->kclass->input['dispatchid']."'
		");
		if($dispatch){
			$items=$this->kclass->DB->query("
				SELECT taskitemid
				FROM dispatchitem AS di
				WHERE dispatchid='".$this->kclass->input['dispatchid']."'
			");
			if($this->kclass->DB->numRows()){
				while($item=$this->kclass->DB->fetchArray($items)){
					$this->kclass->DB->query("UPDATE taskitem SET ifChooser=0 WHERE itemid={$item['taskitemid']}");
				}
			}
			$this->kclass->DB->query("
				UPDATE `dispatch`
				SET killed=".TIMENOW.",killer='".$this->kclass->user['userid']."'
				WHERE dispatchid='".$this->kclass->input['dispatchid']."'
			");
			
			$this->kclass->messager(array(
				'title' => '删除派工申请单成功',
				'text' => '派工申请单 <b>'.$dispatch['dispatchno'].'</b> 已成功被标记为删除!',
				'url' => '/s.php?module=dispatch'.$rt,
				'sec' => 2
			));
		}else{
			$this->kclass->messager(array(
				'title' => '删除派工申请单失败',
				'text' => '您要删除的派工申请单，不存在！',
				'url' => '/s.php?module=dispatch'.$rt,
				'sec' => 3
			));
		}
	}

	//
	function remove(){
		if($this->kclass->input['dispatchid']<=0){
			$this->kclass->boinkIt('/s.php?module=dispatch');
		}
		$dispatch = $this->kclass->DB->queryFirst("
			SELECT dispatchno
			FROM `dispatch`
			WHERE dispatchid='".$this->kclass->input['dispatchid']."'
		");
$body = <<<EOF
<form action="/s.php?module=dispatch&action=kill" name="dispatch" method="post">
<input type="hidden" name="module" value="dispatch">
<input type="hidden" name="action" value="kill">
<input type="hidden" name="dispatchid" value="{$this->kclass->input['dispatchid']}">
<input type="hidden" name="rt" value="{$this->kclass->input['rt']}">
<table><thead>
<thead>
<tr>
<th>删除派工申请单：{$dispatch['dispatchno']}</th>
</tr>
</thead>
<tbody>
<tr class="even">
<td class="middle">你确定要删除派工申请单: <a href="/s.php?module=dispatch&action=view&dispatchid={$this->kclass->input['dispatchid']}" class="big bold" target="_blank">{$dispatch['dispatchno']}</a> 吗?</td>
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
		$this->kclass->page['title'] .= ' - 删除 - '.$dispatch['dispatchno'];
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 删除派工申请单 - '.$dispatch['dispatchno'], 'right' => '<a href="/s.php?module=dispatch">返回列表</a>', 'body'=>$body));
	}

	//
	function revival(){
		if($this->kclass->input['dispatchid']<=0){
			$this->kclass->boinkIt('/s.php?module=dispatch');
		}
		if($this->kclass->input['rt']!='list' AND $this->kclass->input['confirm']==0){
			$this->kclass->boinkIt('/s.php?module=dispatch&action=view&dispatchid='.$this->kclass->input['dispatchid']);
		}
		if($this->kclass->input['dispatchid']<0){
			$e = '<li>请从正确的页面进行操作，如果此问题持续存在，请联系网站管理员。</li>';
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '恢复派工申请单',
				'text' => '您在恢复派工申请单的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$dispatch = $this->kclass->DB->queryFirst("
			SELECT dispatchno
			FROM `dispatch`
			WHERE dispatchid='".$this->kclass->input['dispatchid']."'
		");
		if($this->kclass->input['rt']=='view'){
			$rt = '&action=view&dispatchid='.$this->kclass->input['dispatchid'];
		}elseif($this->kclass->input['rt']=='list'){
			$rt = '#'.$this->kclass->input['dispatchid'];
		}
		if($dispatch){
			$this->kclass->DB->query("
				UPDATE `dispatch`
				SET killed=0,killer=0
				WHERE dispatchid='".$this->kclass->input['dispatchid']."'
			");
			$this->kclass->messager(array(
				'title' => '恢复派工申请单成功',
				'text' => '派工申请单 <b>'.$dispatch['dispatchno'].'</b> 已成功被恢复！',
				'url' => '/s.php?module=dispatch'.$rt,
				'sec' => 2
			));
		}else{
			$this->kclass->messager(array(
				'title' => '恢复派工申请单',
				'text' => '您要恢复的派工申请单不存在！',
				'url' => '/s.php?module=dispatch'.$rt,
				'sec' => 3
			));
		}
	}

	//
	function restore(){
		if($this->kclass->input['dispatchid']<=0){
			$this->kclass->boinkIt('/s.php?module=dispatch');
		}
		$dispatch = $this->kclass->DB->queryFirst("
			SELECT dispatchno
			FROM `dispatch`
			WHERE dispatchid='".$this->kclass->input['dispatchid']."'
		");
$body = <<<EOF
<form action="/s.php?module=dispatch&action=revival" name="dispatch" method="post">
<input type="hidden" name="module" value="dispatch">
<input type="hidden" name="action" value="revival">
<input type="hidden" name="dispatchid" value="{$this->kclass->input['dispatchid']}">
<input type="hidden" name="rt" value="{$this->kclass->input['rt']}">
<table><thead>
<thead>
<tr>
<th>恢复派工申请单：{$dispatch['dispatchno']}</th>
</tr>
</thead>
<tbody>
<tr class="even">
<td class="middle">你确定要恢复派工申请单: <a href="/s.php?module=dispatch&action=view&dispatchid={$this->kclass->input['dispatchid']}" class="big bold" target="_blank">{$dispatch['dispatchno']}</a> 吗?</td>
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
		$this->kclass->page['title'] .= ' - 恢复 - '.$dispatch['dispatchno'];
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 恢复派工申请单 - '.$dispatch['dispatchno'], 'right' => '<a href="/s.php?module=dispatch">返回列表</a>', 'body'=>$body));
	}
	//
		//增加生产记录 并联动修改任务单明细完成记录和状态id
		function addQuantity(){
			if($this->kclass->input['dispatchid']==''){
				$workcenterid=$this->kclass->input['workcenterid'];
				$dispatch=$this->kclass->chooserDispatch(array('name'=>'dispatchid','workcenterid'=>$workcenterid,'width'=>300,'hasBlank'=>0,'selectedid'=>1));
				if(!$dispatch){
					$this->kclass->messager(array(
						'title' => '新建生产记录',
						'text' => '您在新建生产记录的过程中有以下错误：<ul>当前工作中心没有可用派工单！</ul>',
						'url' => 'javascript:history.back()',
						'sec' => 3
					));
				}
				$body=<<<EOF
<form action="/s.php?module=dispatch&action=addQuantity" method="get">
<input type="hidden" name="module" value="dispatch">
<input type="hidden" name="action" value="addQuantity">
<table style="width:450px;">
<thead><tr><th colspan=2>新建生产记录：选择已完工派工单</th></tr></thead>
<tbody>
<tr><td>选择派工单</td><td>{$dispatch}</td></tr>
<tr class="center"><td colspan=2><input type="submit" value=" 提交 "><input type="reset" value=" 重置 "></td></tr>
</tbody>
</table>
EOF;
			}else{
				$dispatch=$this->kclass->DB->queryFirst("
					SELECT d.*,
						s.title AS status,
						u.username AS creator,
						us.username AS modifier
					FROM `dispatch` AS d
					LEFT JOIN mftstatus AS s ON (s.statusid=d.statusid)
					LEFT JOIN user AS u ON (u.userid=d.creator)
					LEFT JOIN user AS us ON (us.userid=d.modifier)
					WHERE d.killed=0 AND d.dispatchid=".$this->kclass->input['dispatchid']."
				");
				$dispatchcreated=date('Y-m-d', $dispatch['created']);
				$items=$this->kclass->DB->query("
					SELECT di.*,
						ti.routingid,
						r.title AS routing,
						s.title AS status,
						mv.title AS version
					FROM `dispatchitem` AS di
					LEFT JOIN taskitem AS ti ON (ti.itemid=di.taskitemid)
					LEFT JOIN routing AS r ON (r.routingid=ti.routingid)
					LEFT JOIN mftstatus AS s ON (s.statusid=di.statusid)
					LEFT JOIN `materialversion` AS mv ON (mv.versionid=di.versionid)
					WHERE di.killed=0 AND di.statusid<>4 AND di.dispatchid=".$dispatch['dispatchid']."
					ORDER BY di.itemid ASC, di.modified DESC, di.created DESC
				");
				if($this->kclass->DB->numRows()){
					$i=1;
					while($item=$this->kclass->DB->fetchArray($items)){
						$materialInfo=$this->kclass->getMaterial(array('materialid'=>$item['materialid'],'itemid'=>$item['materialitemid']));
						$batch=$this->kclass->id(array('materialno'=>$item['materialno'],'batch'=>TIMENOW));
						$itemtr.='<tbody id="item'.$i.'"><tr class="'.$this->kclass->rotateLine(). '">
							<td colspan=12><input type="hidden" name="taskId['.$i.']" value="'.$item['taskid'].'">
								<input type="hidden" name="itemId['.$i.']" value="'.$item['itemid'].'">
								<input type="hidden" name="itemBatch['.$i.']" value="'.$batch.'">
								<span class="bold">['.$i.']</span> 生产物资: <span class="small">[<a href="'.$materialInfo['url'].'">'.$materialInfo['no'].'</a>　'.$materialInfo['material'].'　'.$materialInfo['standard'].']</span> 版本: <span class="small">['.$item['version'].']</span>
								派工生产数: <span class="small bold darkred">'.number_format($item['quantity']).'　'.$materialInfo['unit'].'</span>　备注: <span class="small bold darkred">'.$item['remark'].'</span>　投产日: <span class="small bold darkred">'.date('Y-m-d',$item['workdate']).'</span>　工作时数: <span class="small bold darkred">'.$item['workhours'].' 时</span> <span class="right small">勾选<input type="checkbox" name="itemSelect['.$i.']" value="'.$item['itemid'].'"/></span></td></tr>';
						if($materialInfo['ifPerBaecode']==1){
							$condition=' AND p.type<=1';
						}
						if($materialInfo['attrid']==8){
							$condition=' AND p.type<=2';
						}
						//工艺路线
						$records=$this->kclass->DB->query("
							SELECT dc.*,
							p.title,p.step,p.type
							FROM `dispatchrecord` AS dc
							LEFT JOIN `procedure` AS p ON (p.procedureid=dc.procedureid)
							WHERE dc.killed=0 AND dc.dispatchitemid=".$item['itemid']." {$condition}
							ORDER BY dc.ordering ASC 
						");
						if($this->kclass->DB->numRows()){
							$k=1;
							$itemtr.='<tr><td width="30" align="right">ID</td><td width="200">工序</td><td>排序</td><td>完工数量<span class="red bold">*</span></td><td>不良数量<span class="red bold">*</span></td><td>报废数量<span class="red bold">*</span></td><td>完工用时<span class="red bold">*</span></td><td>作业人数<span class="red bold">*</span></td><td>作业人员<span class="red bold">*</span></td><td>完工备注</td><td>结束生产</td></tr>';
							while($record=$this->kclass->DB->fetchArray($records)){
								$itemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').'"><input type="hidden" name="recordId'.$i.'['.$k.']" value="'.$record['recordid'].'" />';	
								$recorditems=$this->kclass->DB->query("
									SELECT dc.*,
									p.title,p.step
									FROM `dispatchrecorditem` AS dc
									LEFT JOIN `procedure` AS p ON (p.procedureid=dc.procedureid)
									WHERE dc.killed=0 AND dc.dispatchitemid=".$item['itemid']." AND dc.routingid=".$record['routingid']." AND dc.procedureid=".$record['procedureid']."
									ORDER BY dc.ordering ASC 
								");
								if($this->kclass->DB->numRows()){
									while($recorditem=$this->kclass->DB->fetchArray($recorditems)){
										$itemtr.='<td align="right">'.$k.'</td>
											<td>'.$recorditem['title'].'</td>
											<td>'.$recorditem['ordering'].'</td>
											<td>'.$recorditem['finishquantity'].'</td>
											<td>'.$recorditem['badquantity'].'</td>
											<td>'.$recorditem['discardquantity'].'</td>
											<td>'.$recorditem['finishhours'].' 时</td>
											<td>'.$recorditem['workersnumber'].'</td>
											<td>'.$recorditem['workers'].'</td>
											<td width="250">'.$recorditem['finishremark'].'</td>
											<td><span class="darkred">完工日期:'.date('Y-m-d H:i',$recorditem['finishdate']).'</span></td>
										</tr>';
									}
								}
								if($record['statusid']<>4){
									if($record['type']==0){
										$itemtr.='<td align="right"><input type="hidden" name="type'.$i.'['.$k.']" value="0">'.$k.'</td>
											<td>'.$record['title'].'</td>
											<td>'.$record['ordering'].'</td>
											<td><input type="text" name="finishQuantity'.$i.'['.$k.']" size="10" '.$this->kclass->iif($item['workcenterid']==5,'value="'.$item['quantity'].'"','').'></td>
											<td><input type="text" name="badQuantity'.$i.'['.$k.']" size="10" value="0"></td>
											<td><input type="text" name="discardQuantity'.$i.'['.$k.']" size="10" value="0"></td>
											<td><input type="text" name="finishHours'.$i.'['.$k.']" size="5" '.$this->kclass->iif($item['workcenterid']==5,'value="'.$item['workhours'].'"','').'> 时</td>
											<td><input type="text" name="workersNumber'.$i.'['.$k.']" size="5" '.$this->kclass->iif($item['workcenterid']==5,'value="'.$item['dispatchnumber'].'"','').'></td>
											<td><input type="text" name="workers'.$i.'['.$k.']" size="15"></td>
											<td><input type="text" name="finishRemark'.$i.'['.$k.']" size="10"></td>
											<td><input type="radio" name="ifend'.$i.'['.$k.']" value="1" checked>是　<input type="radio" name="ifend'.$i.'['.$k.']" value="0">否</td>
										</tr>';
									}elseif($record['type']==1){
										$itemtr.='<td align="right"><input type="hidden" name="type'.$i.'['.$k.']" value="1">'.$k.'</td>
											<td>'.$record['title'].'</td>
											<td>'.$record['ordering'].'</td>
											<td><input type="text" name="finishQuantity'.$i.'['.$k.']" size="10" '.$this->kclass->iif($item['workcenterid']==5,'value="'.$item['quantity'].'"','').'></td>
											<td><input type="text" name="badQuantity'.$i.'['.$k.']" size="10" value="0"></td>
											<td><input type="text" name="discardQuantity'.$i.'['.$k.']" size="10" value="0"></td>
											<td><input type="text" name="finishHours'.$i.'['.$k.']" size="5" '.$this->kclass->iif($item['workcenterid']==5,'value="'.$item['workhours'].'"','').'> 时</td>
											<td><input type="text" name="workersNumber'.$i.'['.$k.']" size="5" '.$this->kclass->iif($item['workcenterid']==5,'value="'.$item['dispatchnumber'].'"','').'></td>
											<td><input type="text" name="workers'.$i.'['.$k.']" size="15"></td>
											<td><input type="text" name="finishRemark'.$i.'['.$k.']" size="10"></td>
											<td><input type="radio" name="ifend'.$i.'['.$k.']" value="1" checked>是　<input type="radio" name="ifend'.$i.'['.$k.']" value="0">否</td>
										</tr>';
									}elseif($record['type']==2){
										$itemtr.='<td align="right"><input type="hidden" name="type'.$i.'['.$k.']" value="2">'.$k.'</td>
											<td>'.$record['title'].'</td>
											<td>'.$record['ordering'].'</td>
											<td><input type="text" name="finishQuantity'.$i.'['.$k.']" size="10" '.$this->kclass->iif($item['workcenterid']==5,'value="'.$item['quantity'].'"','').'></td>
											<td><input type="text" name="badQuantity'.$i.'['.$k.']" size="10" value="0"></td>
											<td><input type="text" name="discardQuantity'.$i.'['.$k.']" size="10" value="0"></td>
											<td><input type="text" name="finishHours'.$i.'['.$k.']" size="5" '.$this->kclass->iif($item['workcenterid']==5,'value="'.$item['workhours'].'"','').'> 时</td>
											<td><input type="text" name="workersNumber'.$i.'['.$k.']" size="5" '.$this->kclass->iif($item['workcenterid']==5,'value="'.$item['dispatchnumber'].'"','').'></td>
											<td><input type="text" name="workers'.$i.'['.$k.']" size="15"></td>
											<td><input type="text" name="finishRemark'.$i.'['.$k.']" size="10"></td>
											<td><input type="radio" name="ifend'.$i.'['.$k.']" value="1" checked>是　<input type="radio" name="ifend'.$i.'['.$k.']" value="0">否</td>
										</tr>';
									}
								}
								$itemtr.='<tr><td colspan="20"></td></tr>';
								$k++;
							}
						}
						$i++;
					}
				}
				$body=<<<EOF
<form action="/s.php?module=dispatch&action=insertQuantity" name="dispatch" method="post">
<input type="hidden" name="module" value="dispatch">
<input type="hidden" name="action" value="insertQuantity">
<input type="hidden" name="dispatchid" value="{$dispatch['dispatchid']}">
<input type="hidden" name="dispatchno" value="{$dispatch['dispatchno']}">
<table class="hundred">
<thead><tr><th colspan=12>新建生产记录</th></tr></thead>
<tr><td colspan=12>派工单：<a href="/s.php?module=dispatch&action=view&dispatchid={$dispatch['dispatchid']}">{$dispatch['dispatchno']}</a> &nbsp;&nbsp;状态：{$dispatch['status']}　备注：{$dispatch['remark']}　创建人：{$dispatch['creator']}　创建时间：{$dispatchcreated}</td></tr>
{$itemtr}
<tbody>
<tr class="center"><td colspan=12><input type="submit" value=" 提交 "><input type="reset" value=" 重置 "></td></tr>
</tbody>
</table>
</form>
EOF;
			}
			$this->kclass->page['title'].='生产记录';
			$this->kclass->page['onload'].='dc.tabhover();dc.tips();$(\'#date\').datepicker()';
			$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - 新建生产记录', 'right'=>'<a href="/s.php?module=dispatch">返回列表</a>','body'=>$body));
		}
		// insert stock
		function insertQuantity(){
			if($this->kclass->input['requestMethod'] != 'post'){
				$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
			}else{
				if(count($this->kclass->input['itemSelect'])==0){
					$e.='<li>请勾选 生产记录 中的 生产物资 </li>';
				}
				$count=count($this->kclass->input['itemId']);
				for($m=1;$m<$count+1;$m++){
					if($this->kclass->input['itemSelect'][$m]!=''){
						$status=$this->kclass->DB->queryFirst("SELECT statusid FROM dispatchitem WHERE itemid='".$this->kclass->input['itemSelect'][$m]."'");
						if($status['statusid']==4){
							$e.='<li>生产物资['.$m.'] 中的物资明细 已经生产完成</li>';
						}
						$c=count($this->kclass->input['recordId'.$m]);	
						for($i=1;$i<$c+1;$i++){
							if($this->kclass->input['finishQuantity'.$m][$i]!=''){
								if($this->kclass->input['finishQuantity'.$m][$i]<=0){
									$e.='<li>请填写 生产物资['.$m.'] 中的ID:['.$i.']的 完工数量 必须大于0</li>';
								}
								if($this->kclass->input['badQuantity'.$m][$i]==''){
									$e.='<li>请填写 生产物资['.$m.'] 中的ID:['.$i.']的 不良数量 </li>';
								}
								if($this->kclass->input['discardQuantity'.$m][$i]==''){
									$e.='<li>请填写 生产物资['.$m.'] 中的ID:['.$i.']的 报废数量 </li>';
								}
								if($this->kclass->input['finishHours'.$m][$i]==''){
									$e.='<li>请填写 生产物资['.$m.'] 中的ID:['.$i.']的 完工用时 </li>';
								}
								if($this->kclass->input['finishHours'.$m][$i]<=0){
									$e.='<li>请填写 生产物资['.$m.'] 中的ID:['.$i.']的 完工用时 必须大于0</li>';
								}
								if($this->kclass->input['workersNumber'.$m][$i]==''){
									$e.='<li>请填写 生产物资['.$m.'] 中的ID:['.$i.']的 作业人数 </li>';
								}
								if($this->kclass->input['workersNumber'.$m][$i]<=0){
									$e.='<li>请填写 生产物资['.$m.'] 中的ID:['.$i.']的 作业人数 必须大于0</li>';
								}
								if($this->kclass->input['workers'.$m][$i]==''){
									$e.='<li>请填写 生产物资['.$m.'] 中的ID:['.$i.']的 作业人员 </li>';
								}
								if($this->kclass->input['ifend'.$m][$i]==''){
									$e.='<li>请选择 生产物资['.$m.'] 中的ID:['.$i.']的 是否结束生产 </li>';
								}
							}
						}
					}
				}
			}
			if(isset($e)){
				$this->kclass->messager(array(
					'title' => '新建生产记录',
					'text' => '您在新建生产记录的过程中有以下错误：<ul>'.$e.'</ul>',
					'url' => 'javascript:history.back()',
					'sec' => 3
				));
			}
			$count=count($this->kclass->input['itemId']);
			for($m=1;$m<$count+1;$m++){
				if($this->kclass->input['itemSelect'][$m]!=''){
					//record
					$c=count($this->kclass->input['recordId'.$m]);	
					for($i=1;$i<$c+1;$i++){
						if($this->kclass->input['finishQuantity'.$m][$i]!=''){
							$dc=$this->kclass->DB->queryFirst("
								SELECT recordid,dispatchitemid,routingid,procedureid,ordering
								FROM `dispatchrecord`
								WHERE recordid='".$this->kclass->input['recordId'.$m][$i]."'
							");
							$this->kclass->DB->query("
								INSERT INTO dispatchrecorditem (recordid,dispatchitemid,routingid,procedureid,ordering,statusid,finishquantity,badquantity,discardquantity,finishhours,workersnumber,workers,finishremark,finishdate)
								VALUES ('".$dc['recordid']."','".$dc['dispatchitemid']."','".$dc['routingid']."','".$dc['procedureid']."','".$dc['ordering']."',2,'".$this->kclass->input['finishQuantity'.$m][$i]."','".$this->kclass->input['badQuantity'.$m][$i]."','".$this->kclass->input['discardQuantity'.$m][$i]."','".$this->kclass->input['finishHours'.$m][$i]."','".$this->kclass->input['workersNumber'.$m][$i]."','".$this->kclass->input['workers'.$m][$i]."','".$this->kclass->input['finishRemark'.$m][$i]."','".TIMENOW."')
							");
							$statusid=$this->kclass->iif($this->kclass->input['ifend'.$m][$i]==1,4,3);
							$this->kclass->DB->query("
								UPDATE dispatchrecord SET 
									statusid='".$statusid."',
									finishquantity=finishquantity+'".$this->kclass->input['finishQuantity'.$m][$i]."',
									badquantity=badquantity+'".$this->kclass->input['badQuantity'.$m][$i]."',
									discardquantity=discardquantity+'".$this->kclass->input['discardQuantity'.$m][$i]."',
									finishhours=finishhours+'".$this->kclass->input['finishHours'.$m][$i]."',
									finishdate='".TIMENOW."'
								WHERE recordid='".$this->kclass->input['recordId'.$m][$i]."'
							");
							if($i==$c){//判断为提交最后一道工序生产记录时 更新完成信息和状态
								$this->kclass->DB->query("
									UPDATE dispatchitem SET 
										statusid='".$statusid."',
										finishquantity=finishquantity+'".$this->kclass->input['finishQuantity'.$m][$i]."',
										batch='".$this->kclass->input['itemBatch'][$m]."',
										finishhours=finishhours+'".$this->kclass->input['finishHours'.$m][$i]."',
										finishdate='".TIMENOW."'
									WHERE itemid='".$this->kclass->input['itemSelect'][$m]."'
								");
								//更新任务单明细完成信息和状态
								$tid=$this->kclass->DB->queryFirst("SELECT taskitemid FROM dispatchitem WHERE itemid='".$this->kclass->input['itemSelect'][$m]."'");
								$this->kclass->DB->query("
									UPDATE taskitem SET 
										finishquantity=finishquantity+'".$this->kclass->input['finishQuantity'.$m][$i]."',
										finishdis=finishdis+'".$this->kclass->input['badQuantity'.$m][$i]."'
									WHERE itemid='".$tid['taskitemid']."'
								");
								$ti=$this->kclass->DB->queryFirst("SELECT quantity,finishquantity FROM taskitem WHERE itemid='".$tid['taskitemid']."'");
								if($ti['quantity']<=$ti['finishquantity']){
									$this->kclass->DB->query("
									UPDATE taskitem SET 
										statusid=4
									WHERE itemid='".$tid['taskitemid']."'
								");
								}
								$sid=$this->kclass->DB->queryFirst("
									SELECT statusid 
									FROM  taskitem 
									WHERE killed=0 AND statusid=1 AND taskid=".$this->kclass->input['taskId'][$m]."
								");
								if($sid==''){
									$this->kclass->DB->query("
										UPDATE task SET 
											statusid=4
										WHERE taskid=".$this->kclass->input['taskId'][$m]."
									");
								}
							}
						}
					}
				}
			}

			//更新派工单完成状态
			$item=$this->kclass->DB->queryFirst("
				SELECT di.itemid,di.batchTime,di.versionid,di.materialid,di.materialitemid,di.taskitemid,
					mv.title AS version
				FROM  `dispatchitem` AS di
				LEFT JOIN materialversion AS mv ON (di.versionid=mv.versionid)
				WHERE di.dispatchid=".$this->kclass->input['dispatchid']." AND di.statusid<>4 AND di.killed=0
			");
			//
			if(empty($item)){
				$statusid=4;
			}else{
				$statusid=3;
			}
			$this->kclass->DB->query("UPDATE dispatch SET statusid='".$statusid."' WHERE dispatchid='".$this->kclass->input['dispatchid']."'");

			$this->kclass->messager(array(
				'title' => '新建生产记录',
				'text' => '派工单 <b>'.$this->kclass->input['dispatchno'].'</b> 生产记录 已新建成功!',
				'url' => '/s.php?module=dispatch&action=view&dispatchid='.$this->kclass->input['dispatchid'],
				'sec' => 2
			));
		}

		//修改生产记录 并更新完成信息和状态 存在待修复bug 需要修改记录不完善 --gaowenfei 2013/11/25 10:09
		function updateQuantity(){
			$dispatch=$this->kclass->DB->queryFirst("
				SELECT d.*,
					s.title AS status,
					u.username AS creator,
					us.username AS modifier
				FROM `dispatch` AS d
				LEFT JOIN mftstatus AS s ON (s.statusid=d.statusid)
				LEFT JOIN user AS u ON (u.userid=d.creator)
				LEFT JOIN user AS us ON (us.userid=d.modifier)
				WHERE d.killed=0 AND d.dispatchid=".$this->kclass->input['dispatchid']."
				");
			$dispatchcreated=date('Y-m-d', $dispatch['created']);
			if(!$dispatch){
				$e='数据错误，很抱歉！';
			}
			if(isset($e)){
				$this->kclass->messager(array(
					'title' => '修改生产记录',
					'text' => '您在修改生产记录的过程中有以下错误：<ul>'.$e.'</ul>',
					'url' => 'javascript:history.back()',
					'sec' => 3
				));
			}
			$items=$this->kclass->DB->query("
					SELECT di.*,
					s.title AS status,
					mv.title AS version,
					u.username AS creator,
					us.username AS modifier
				FROM `dispatchitem` AS di
				LEFT JOIN mftstatus AS s ON (s.statusid=di.statusid)
				LEFT JOIN `materialversion` AS mv ON (mv.versionid=di.versionid)
				LEFT JOIN user AS u ON (u.userid=di.creator)
				LEFT JOIN user AS us ON (us.userid=di.modifier)
				WHERE di.killed=0 AND di.dispatchid=".$dispatch['dispatchid']."
			ORDER BY di.itemid ASC, di.modified DESC, di.created DESC
				");
				$i=1;
				if($this->kclass->DB->numRows()){
					while($item=$this->kclass->DB->fetchArray($items)){
				$materialInfo=$this->kclass->getMaterial(array('materialid'=>$item['materialid'],'itemid'=>$item['materialitemid']));
						$batch=$this->kclass->id(array('materialno'=>$item['materialno'],'batch'=>TIMENOW));
						$itemtr.='<tr class="'.$this->kclass->rotateLine(). '">
					<td colspan=12><span class="small">['.$i.']</span><input type="hidden" name="taskId['.$i.']" value="'.$item['taskid'].'"><input type="hidden" name="itemId['.$i.']" value="'.$item['itemid'].'"><input type="hidden" name="itemBatch['.$i.']" value="'.$batch.'"><input type="hidden" name="orgitemQuantity['.$i.']" value="'.$item['finishquantity'].'"> 生产物资:  <span class="small">[ <a href="'.$materialInfo['url'].'">'.$materialInfo['no'].'</a>　'.$materialInfo['material'].'　'.$materialInfo['standard'].' ]</span> 版本: <span class="small">['.$item['version'].']</span>
					 派工生产数: <span class="small bold darkred">'.number_format($item['quantity']).' '.$materialInfo['unit'].'</span> 备注: <span class="small bold darkred">'.$item['remark'].'</span> 投产日期: <span class="small bold darkred">'.date('Y-m-d',$item['workdate']).'</span> 工作时数: <span class="small bold darkred">'.$item['workhours'].' 时</span> <span class="right small">勾选<input type="checkbox" name="itemSelect['.$i.']" value="'.$item['itemid'].'"/></span></td></tr>';
							//工艺路线
						$records=$this->kclass->DB->query("
							SELECT dci.*,
							p.title,p.step
							FROM `dispatchrecorditem` AS dci
							LEFT JOIN `procedure` AS p ON (p.procedureid=dci.procedureid)
							WHERE dci.killed=0 AND dci.dispatchitemid=".$item['itemid']."
							ORDER BY dci.ordering ASC 
							");
						if($this->kclass->DB->numRows()){
							$k=1;
							$itemtr.='<tr><td width="15">ID</td><td width="200">工序</td><td>完工数量<span class="red bold">*</span></td><td>不良数量<span class="red bold">*</span></td><td>报废数量<span class="red bold">*</span></td><td>完工用时<span class="red bold">*</span></td><td>作业人数<span class="red bold">*</span></td><td>作业人员<span class="red bold">*</span></td><td>完工备注</td><td>完工日期</td><td>删</td></tr>';
						while($record=$this->kclass->DB->fetchArray($records)){
							$dc=$this->kclass->DB->queryFirst("SELECT  MAX(ordering) AS maxordering FROM `dispatchrecord` WHERE recordid='".$record['recordid']."'");
							$ifLastrecord=$this->kclass->iif($dc['maxordering']==$record['ordering'],1,0);
							$itemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').'"><input type="hidden" name="recorditemId'.$i.'['.$k.']" value="'.$record['itemid'].'" /><input type="hidden" name="recordId'.$i.'['.$k.']" value="'.$record['recordid'].'" /><input type="hidden" name="ifLastrecord'.$i.'['.$k.']" value="'.$ifLastrecord.'" />';	
							$itemtr.='<td>'.$k.'</td>
									<td>'.$record['title'].'</td>
									<td><input type="text" name="finishQuantity'.$i.'['.$k.']" size="10" value="'.$record['finishquantity'].'"></td>
									<td><input type="text" name="badQuantity'.$i.'['.$k.']" size="10" value="'.$record['badquantity'].'"></td>
									<td><input type="text" name="discardQuantity'.$i.'['.$k.']" size="10" value="'.$record['discardquantity'].'"></td>
									<td><input type="text" name="finishHours'.$i.'['.$k.']" size="5" value="'.$record['finishhours'].'"> 时</td>
									<td><input type="text" name="workersNumber'.$i.'['.$k.']" size="5" value="'.$record['workersnumber'].'"></td>
									<td><input type="text" name="workers'.$i.'['.$k.']" size="15" value="'.$record['workers'].'"></td>
									<td width="120"><input type="text" name="finishRemark'.$i.'['.$k.']" size="10" value="'.$record['finishremark'].'"></td>
									<td><span class="darkred">完工日期:'.date('Y-m-d H:i',$record['finishdate']).'</span></td>
									<td><input type="checkbox" name="itemKill'.$i.'['.$k.']" value="'.$record['itemid'].'"></td>
									</tr>';
								$k++;
							}
						}
						$i++;
					}
				}

			$body=<<<EOF
<form action="/s.php?module=dispatch&action=doupdateQuantity" name="dispatch" method="post">
<input type="hidden" name="module" value="dispatch">
<input type="hidden" name="action" value="doupdateQuantity">
<input type="hidden" name="dispatchid" value="{$dispatch['dispatchid']}">
<input type="hidden" name="dispatchno" value="{$dispatch['dispatchno']}">
<table class="hundred">
<thead><tr><th colspan=16>修改生产记录</th></tr></thead>
<tr><td colspan=12>派工单：<a href="/s.php?module=dispatch&action=view&dispatchid={$dispatch['dispatchid']}">{$dispatch['dispatchno']}</a> &nbsp;&nbsp;状态：{$dispatch['status']}　备注：{$dispatch['remark']}　创建人：{$dispatch['creator']}　创建时间：{$dispatchcreated}</td></tr>
<tbody>
{$itemtr}
<tr class="center"><td colspan=16><input type="submit" value=" 提交 "><input type="reset" value=" 重置 "></td></tr>
</tbody>
</table>
</form>
EOF;
			$this->kclass->page['title'].='订单生产记录';
			$this->kclass->page['onload'].='dc.tabhover();dc.tips();$(\'#date\').datepicker()';
			$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - 修改生产记录', 'right'=>'<a href="/s.php?module=dispatch">返回列表</a>','body'=>$body));

		}
		//
		function doupdateQuantity(){
			if($this->kclass->input['requestMethod'] != 'post'){
				$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
			}else{
				$count=count($this->kclass->input['itemId']);
				for($m=1;$m<$count+1;$m++){
					if($this->kclass->input['itemSelect'][$m]!=''){
						$c=count($this->kclass->input['recordId'.$m]);	
						for($i=1;$i<$c+1;$i++){
							if($this->kclass->input['finishQuantity'.$m][$i]==''){
								$e.='<li>请填写 生产物资['.$m.'] 中的ID:['.$i.']的 完工数量 </li>';
							}
							if($this->kclass->input['finishQuantity'.$m][$i]<=0){
								$e.='<li>请填写 生产物资['.$m.'] 中的ID:['.$i.']的 完工数量 必须大于0</li>';
							}
							if($this->kclass->input['badQuantity'.$m][$i]==''){
								$e.='<li>请填写 生产物资['.$m.'] 中的ID:['.$i.']的 不良数量 </li>';
							}
							if($this->kclass->input['discardQuantity'.$m][$i]==''){
								$e.='<li>请填写 生产物资['.$m.'] 中的ID:['.$i.']的 报废数量 </li>';
							}
							if($this->kclass->input['finishHours'.$m][$i]==''){
								$e.='<li>请填写 生产物资['.$m.'] 中的ID:['.$i.']的 完工用时 </li>';
							}
							if($this->kclass->input['finishHours'.$m][$i]<=0){
								$e.='<li>请填写 生产物资['.$m.'] 中的ID:['.$i.']的 完工用时 必须大于0</li>';
							}
							if($this->kclass->input['workersNumber'.$m][$i]==''){
								$e.='<li>请填写 生产物资['.$m.'] 中的ID:['.$i.']的 作业人数 </li>';
							}
							if($this->kclass->input['workersNumber'.$m][$i]<=0){
								$e.='<li>请填写 生产物资['.$m.'] 中的ID:['.$i.']的 作业人数 必须大于0</li>';
							}
							if($this->kclass->input['workers'.$m][$i]==''){
								$e.='<li>请填写 生产物资['.$m.'] 中的ID:['.$i.']的 作业人员 </li>';
							}
						}
					}
				}
			}
			if(isset($e)){
				$this->kclass->messager(array(
					'title' => '新建生产记录',
					'text' => '您在新建生产记录的过程中有以下错误：<ul>'.$e.'</ul>',
					'url' => 'javascript:history.back()',
					'sec' => 3
				));
			}
			$count=count($this->kclass->input['itemId']);
			for($m=1;$m<$count+1;$m++){
				if($this->kclass->input['itemSelect'][$m]!=''){
				//record
					$c=count($this->kclass->input['recordId'.$m]);	
					for($i=1;$i<$c+1;$i++){
						$di=$this->kclass->DB->queryFirst("
								SELECT dci.finishquantity,dci.badquantity,dci.discardquantity,dci.finishhours,
									dc.statusid AS dcstatusid
								FROM dispatchrecorditem AS dci
								LEFT JOIN dispatchrecord AS dc ON (dc.recordid=dci.recordid)
								WHERE dci.itemid='".$this->kclass->input['recorditemId'.$m][$i]."'
							");
						if($this->kclass->input['itemKill'.$m][$i]!=''){
						$this->kclass->DB->query("UPDATE dispatchrecorditem SET	killed=".TIMENOW.",killer='".$this->kclass->user['userid']."' WHERE itemid='".$this->kclass->input['itemKill'.$m][$i]."'");
						// 去掉生产记录的数量
							$this->kclass->DB->query("
								UPDATE dispatchrecord SET 
											finishquantity=finishquantity-'".$di['finishquantity']."',
											badquantity=badquantity-'".$di['badquantity']."',
											discardquantity=discardquantity-'".$di['discardquantity']."',
											finishhours=finishhours-'".$di['finishhours']."',
											statusid=1,
											modified='".TIMENOW."',
											modifier='".$this->kclass->user['userid']."'
								WHERE recordid='".$this->kclass->input['recordId'.$m][$i]."'
							");
						$this->kclass->DB->query("UPDATE dispatchitem SET statusid=1 WHERE itemid='".$this->kclass->input['itemSelect'][$m]."'");
							$finishquantity=$di['finishquantity'];
							$finishhours=$di['finishhours'];
						}else{
						//去掉生产记录明细的数量
							$this->kclass->DB->query("
								UPDATE dispatchrecorditem SET 
									finishquantity='".$this->kclass->input['finishQuantity'.$m][$i]."',
									badquantity='".$this->kclass->input['badQuantity'.$m][$i]."',
									discardquantity='".$this->kclass->input['discardQuantity'.$m][$i]."',
									finishhours='".$this->kclass->input['finishHours'.$m][$i]."',
									workersnumber='".$this->kclass->input['workersNumber'.$m][$i]."',
									workers='".$this->kclass->input['workers'.$m][$i]."',
									finishremark='".$this->kclass->input['finishRemark'.$m][$i]."',
									modified='".TIMENOW."',
									modifier='".$this->kclass->user['userid']."'
								WHERE itemid='".$this->kclass->input['recorditemId'.$m][$i]."'
							");
							$this->kclass->DB->query("
								UPDATE dispatchrecord SET 
									finishquantity=finishquantity-'".$di['finishquantity']."'+'".$this->kclass->input['finishQuantity'.$m][$i]."',
									badquantity=badquantity-'".$di['badquantity']."'+'".$this->kclass->input['badQuantity'.$m][$i]."',
									discardquantity=discardquantity-'".$di['discardquantity']."'+'".$this->kclass->input['discardQuantity'.$m][$i]."',
									finishhours=finishhours-'".$di['finishhours']."'+'".$this->kclass->input['finishHours'.$m][$i]."',
									modified='".TIMENOW."',
									modifier='".$this->kclass->user['userid']."'
								WHERE recordid='".$this->kclass->input['recordId'.$m][$i]."'
							");
							$finishquantity=$di['finishquantity']-$this->kclass->input['finishQuantity'.$m][$i];
							$finishhours=$di['finishhours']-$this->kclass->input['finishHours'.$m][$i];
						}
						if($this->kclass->input['ifLastrecord'.$m][$i]==1){
							$this->kclass->DB->query("
								UPDATE dispatchitem SET 
									finishquantity=finishquantity-'".$finishquantity."',
									batch='".$this->kclass->input['itemBatch'][$m]."',
									finishhours=finishhours-'".$finishhours."'
								WHERE itemid='".$this->kclass->input['itemSelect'][$m]."'
							");
							$tid=$this->kclass->DB->queryFirst("SELECT taskitemid FROM dispatchitem WHERE itemid='".$this->kclass->input['itemId'][$m]."'");
							$this->kclass->DB->query("
								UPDATE taskitem SET 
									finishquantity=finishquantity-'".$finishquantity."',
									finishdis=finishdis-'".$di['badquantity']."'+'".$this->kclass->input['badQuantity'.$m][$i]."'
								WHERE itemid='".$tid['taskitemid']."'
							");
							$ti=$this->kclass->DB->queryFirst("SELECT quantity,finishquantity FROM taskitem WHERE itemid='".$tid['taskitemid']."'");
							$itemstatusid=$this->kclass->iif($ti['quantity']<=$ti['finishquantity'],'4','1');
							$this->kclass->DB->query("UPDATE taskitem SET statusid='".$itemstatusid."' WHERE itemid='".$tid['taskitemid']."'");
							$sid=$this->kclass->DB->queryFirst("SELECT statusid FROM  taskitem WHERE killed=0 AND statusid=1 AND taskid=".$this->kclass->input['taskId'][$m]." LIMIT 0,1");
							$taskstatusid=$this->kclass->iif($sid=='','4','2');
							$this->kclass->DB->query("UPDATE task SET statusid='".$taskstatusid."' WHERE taskid='".$this->kclass->input['taskId'][$m]."'");
						}
					}
				}
			}
			$dispatch=$this->kclass->input['dispatchid'];
		$item=$this->kclass->DB->queryFirst("SELECT itemid FROM  `dispatchitem` WHERE dispatchid=".$dispatch." AND statusid<>4 AND killed=0");
			if($item==''){
				$statusid=4;
				}else{
					$statusid=3;
			}
			$this->kclass->DB->query("UPDATE dispatch SET statusid='".$statusid."' WHERE dispatchid='".$dispatch."'");
			$this->kclass->messager(array(
				'title' => '修改生产记录',
				'text' => '派工单 <b>'.$this->kclass->input['dispatchno'].'</b> 生产记录 已修改成功!',
				'url' => '/s.php?module=dispatch&action=view&dispatchid='.$dispatch,
				'sec' => 2
			));
		}
		//修改工序生产状态
		function changeStatus(){
			$rd=$this->kclass->DB->queryFirst("
				SELECT dc.*,
					d.statusid AS dstatusid,
					s.title AS status,
					p.title,
					u.username AS creator,
					us.username AS modifier
				FROM `dispatchrecord` AS dc 
				LEFT JOIN mftstatus AS s ON (s.statusid=dc.statusid)
				LEFT JOIN `procedure` AS p ON (p.procedureid=dc.procedureid)
				LEFT JOIN dispatchitem AS di ON (di.itemid=dc.dispatchitemid)
				LEFT JOIN dispatch AS d ON (d.dispatchid=di.dispatchid)
				LEFT JOIN user AS u ON (u.userid=dc.creator)
				LEFT JOIN user AS us ON (u.userid=dc.modifier)
				WHERE dc.killed=0 AND dc.recordid='".$this->kclass->input['recordid']."'
				LIMIT 0,1
			");
			if(!$rd){
				$e='<li>数据错误，很抱歉~</li>';
			}
			if(isset($e)){
				$this->kclass->messager(array(
					'title' => '修改工序生产状态',
					'text' => '修改工序生产状态<ul>'.$e.'</ul>',
					'url' => 'javascript:history.back()',
					'sec' => 3
				));
			}
$body=<<<EOF
<form method="post" action="/s.php?module=dispatch&action=dochangeStatus">
<input type="hidden" name="module" value="dispatch">
<input type="hidden" name="action" value="dochangeStatus">
<input type="hidden" name="recordid" value="{$this->kclass->input['recordid']}">
<table>
<thead><tr><th colspan="6">修改工序 [<span class="bold darkred">{$rd['title']}</span>] 的生产状态</th></tr></thead>
<tbody>
<tr class="even">
	<td>状态：<span class="bold red">*</span></td><td class="center"><input type="radio" name="statusid" value="3" checked="checked" />继续生产　　　<input type="radio" name="statusid" value="4" />生产完成　　　</td>
</tr>
<tr class="odd">
	<td colspan=6 class="center"><input type="submit" value=" 提交 " /><input type="reset" value="重置" /></td>
</tr>
</tbody>
</table>
</form>
EOF;
			$this->kclass->tbline+=4;
			$this->kclass->page['title'].='修改工序生产状态';
			$this->kclass->page['onload'].='dc.tabhover();';
			$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - 修改工序生产状态', 'right'=>'<a href="/s.php?module=dispatch&action=list">返回列表</a>','body'=>$body));
		}
		//
		function dochangeStatus(){
			if($this->kclass->input['requestMethod'] != 'post'){
				$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
			}else{
				if($this->kclass->input['statusid']==''){
					$e.='<li>请选择要修改的工序的生产状态。</li>';
				}
			}
			if($e){
				$this->kclass->messager(array(
					'title' => '修改工序生产状态',
					'text' => '您在修改工序生产状态的过程中有以下错误：<ul>'.$e.'</ul>',
					'url' => 'javascript:history.back()',
					'sec' => 3
				));
			}
			$rd=$this->kclass->DB->queryFirst("
				SELECT dc.*,
					s.title AS status,
					p.title,
					di.dispatchid,di.taskid,
					u.username AS creator,
					us.username AS modifier
				FROM `dispatchrecord` AS dc
				LEFT JOIN mftstatus AS s ON (s.statusid=dc.statusid)
				LEFT JOIN dispatchitem AS di ON (di.itemid=dc.dispatchitemid)
				LEFT JOIN `procedure` AS p ON (p.procedureid=dc.procedureid)
				LEFT JOIN user AS u ON (u.userid=dc.creator)
				LEFT JOIN user AS us ON (u.userid=dc.modifier)
				WHERE dc.killed=0 AND dc.recordid='".$this->kclass->input['recordid']."'
				LIMIT 0,1
			");
			$this->kclass->DB->query("
				UPDATE `dispatchrecord` SET
					`statusid`='".$this->kclass->input['statusid']."',
					`modified`='".TIMENOW."',
					`modifier`='".$this->kclass->user['userid']."'
				WHERE recordid='".$this->kclass->input['recordid']."'
			");
			if($this->kclass->input['statusid']==3){
				$this->kclass->DB->query("
					UPDATE `dispatch` SET
						`statusid`='".$this->kclass->input['statusid']."',
						`modified`='".TIMENOW."',
						`modifier`='".$this->kclass->user['userid']."'
					WHERE dispatchid='".$rd['dispatchid']."'
				");
				$this->kclass->DB->query("
					UPDATE `task` SET
						`statusid`='".$this->kclass->input['statusid']."',
						`modified`='".TIMENOW."',
						`modifier`='".$this->kclass->user['userid']."'
					WHERE taskid='".$rd['taskid']."'
				");
			}
			$this->kclass->messager(array(
				'title' => '修改工序生产状态',
				'text' => '工序 <b>'.$rd['title'].'</b> 生产状态修改成功！',
				'url' => '/s.php?module=dispatch&action=view&dispatchid='.$rd['dispatchid'],
				'sec' => 2
			));
		}
		//
		//查看维修记录
		function viewrepairRecord(){
			$dc=$this->kclass->DB->queryFirst("
				SELECT dc.*,
					mv.title AS version,
					d.dispatchid,d.dispatchno,
					wc.workcenterid,wc.title AS workcenter,
					r.title AS routing,
					p.title AS proce,p.step
				FROM `dispatchrecorditem` AS dc
				LEFT JOIN dispatchitem AS di ON (di.itemid=dc.dispatchitemid)
				LEFT JOIN `materialversion` AS mv ON (mv.versionid=di.versionid)
				LEFT JOIN dispatch AS d ON (d.dispatchid=di.dispatchid)
				LEFT JOIN workcenter AS wc ON (wc.workcenterid=di.workcenterid)
				LEFT JOIN routing AS r ON (r.routingid=dc.routingid)
				LEFT JOIN `procedure` AS p ON (p.procedureid=dc.procedureid)
				WHERE dc.killed=0 AND dc.itemid=".$this->kclass->input['recorditemid']."
				LIMIT 0,1
			");
		if($dc){
			$dc['created']=date('Y-m-d',$dc['created']);
			$materialInfo=$this->kclass->getMaterial(array('materialid'=>$dc['materialid'],'itemid'=>$dc['materialitemid']));
			$dcbody='<tbody>
<tr class="even">
<td width="100">派工单编号：</td><td width="300"><a href="/s.php?module=dispatch&action=view&dispatchid='.$dc['dispatchid'].'">'.$dc['dispatchno'].'</a></td>
<td width="100">工作中心：</td><td width="300">'.$dc['workcenter'].'</td>
<td width="100">生产物资：</td><td width="400"><a href="'.$materialInfo['url'].'">'.$materialInfo['murl'].'</a>　'.$materialInfo['material'].'　'.$materialInfo['standard'].'　(版本：'.$dc['version'].')</td>
</tr>
<tr class="odd">
<td>工艺流程：</td><td>'.$dc['routing'].'</td>
<td>工序：</td><td>'.$dc['proce'].'</td>
<td>排序：</td><td>'.$dc['ordering'].'</td>
</tr>
<tr class="even">
<td>作业人数：</td><td>'.$dc['workersnumber'].' 人</td>
<td>作业人员：</td><td>'.$dc['workers'].'</td>
<td>完工备注：</td><td>'.$dc['finishremark'].'</td>
</tr>
<tr class="odd">
<td>完工用时：</td><td><span class="bold">'.$dc['finishhours'].'</span> 时</td>
<td>完工时间：</td><td><span class="bold">'.date('Y-m-d H:i:s',$dc['finishdate']).'</span></td>
<td></td><td></td>
</tr>
<tr class="even">
<td>完工数量：</td><td><span class="bold">'.number_format($dc['finishquantity']).'</span></td>
<td>不良品数量：</td><td><span class="bold darkred">'.number_format($dc['badquantity']).'</span></td>
<td>报废数量：</td><td><span class="bold">'.number_format($dc['discardquantity']).'</span></td>
</tr>
</tbody>';
			//维修记录
			$items=$this->kclass->DB->query("
				SELECT rr.*,
					u.username AS creator,
					us.username AS modifier
				FROM `repairrecord` AS rr
				LEFT JOIN user AS u ON (u.userid=rr.creator)
				LEFT JOIN user AS us ON (us.userid=rr.modifier)
				WHERE rr.killed=0 AND rr.recorditemid='".$dc['itemid']."'
				ORDER BY rr.repairid ASC,rr.ordering ASC,rr.modified DESC, rr.created DESC
			");
			if($this->kclass->DB->numRows()){
				$i=1;
				while($item=$this->kclass->DB->fetchArray($items)){
					$type=$this->kclass->iif($item['wrongtype']==1,'A: 人为操作问题','B: 物料问题');
					$result=$this->kclass->iif($item['repairresult']==1,'A: 合格','B: 报废');
					$itemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').'"  align="left">
						<td>'.$i.'</td>
						<td><span class="bold darkred">'.$type.'</span></td>
						<td>'.$item['describe'].'</td>
						<td>'.$item['analysis'].'</td>
						<td>'.$item['measure'].'</td>
						<td>'.$item['conclusion'].'</td>
						<td>'.$item['remark'].'</td>
						<td>'.$item['quantity'].'</td>
						<td>'.date('Y-m-d',$item['repairdate']).'</td>
						<td>'.date('Y-m-d',$item['finishdate']).'</td>
						<td><span class="bold darkred">'.$result.'</span></td>
						<td>由'.$item['creator'].'创建于'.date('Y-m-d',$item['created']).'</td>
						<td><a href="/s.php?module=dispatch&action=removerepairRecord&repairid='.$item['repairid'].'">删除</a></td>
						</tr>';
					$i++;
				}
			}else{
				$itemtr.='<td colspan="14" class="darkred center">暂无相关记录！</td>';
			}
$body=<<<EOF
<div class="title">生产不良品维修报表</div>
<table class="hundred">
<thead><tr><th colspan="8">生产记录信息</th></tr></thead>
{$dcbody}
</table>
<table class="hundred">
<thead><tr><th colspan="14">维修记录列表<span class="right"><a href="/s.php?module=dispatch&action=addrepairRecord&recorditemid={$this->kclass->input['recorditemid']}">增加维修记录</a></span></th></tr></thead>
<tbody>
<tr class="even bold" align="left">
<td>ID</td><td>故障类型</td><td>故障现象描述</td><td>故障原因分析</td><td>维修措施</td><td>结论</td><td>备注</td><td>数量</td><td>送修时间</td><td>完工时间</td><td>处理结果</td><td>创建信息</td><td>操作</td>
</tr>
{$itemtr}
</tbody>
</table>
EOF;
		}else{
			$body='数据错误，很抱歉！';
		}

		$this->kclass->page['title'].=' - 新增维修记录';
		$this->kclass->page['onload'].='dc.tabhover();dc.tips();';
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - 生产派工维修记录','right' => '<span class = "small">操作：</span> <a href="/s.php?module=dispatch&action=list">返回列表</a>','body'=>$body));
		}
		//
		//新增维修记录
		function addrepairRecord(){
			if($this->kclass->input['recorditemid']==''){
				$departmentid=$this->kclass->user['departmentid'];$departments=array(8,9,10);
				$purviews=' AND wc.departmentid='.$departmentid;
				if(in_array($departmentid,$departments))$purviews='';
				$items=$this->kclass->DB->query("
						SELECT dc.*,
						d.dispatchid,d.dispatchno,
						wc.workcenterid,wc.title AS workcenter,
						r.title AS routing,
						p.title AS proce
					FROM `dispatchrecorditem` AS dc
					LEFT JOIN dispatchitem AS di ON (di.itemid=dc.dispatchitemid)
					LEFT JOIN dispatch AS d ON (d.dispatchid=di.dispatchid)
					LEFT JOIN workcenter AS wc ON (wc.workcenterid=di.workcenterid)
					LEFT JOIN routing AS r ON (r.routingid=dc.routingid)
					LEFT JOIN `procedure` AS p ON (p.procedureid=dc.procedureid)
					WHERE dc.killed=0 AND dc.badquantity<>0 ".$purviews."
					ORDER BY dc.finishdate ASC,dc.ordering ASC,dc.modified DESC, dc.created DESC
					");
					$itemth='<tr><th width="35">ID</th><th width="80">派工单</th><th width="80">工作中心</th><th width="240">工艺流程</th><th width="80">工序</th><th width="40">排序</th><th width="100">完工数量</th><th width="100">不良数量</th><th width="100">报废数量</th><th width="100">完工用时</th><th width="150">完工时间</th><th width="80">作业人数</th><th width="100">作业人员</th><th width="100">完工备注</th><th width="100">操作</th></tr>';
					$i=1;
					if($this->kclass->DB->numRows()){
						while($item=$this->kclass->DB->fetchArray($items)){
							$itemtr.='<tr class="'.$this->kclass->rotateLine(). ' small">
								<td>'.$i.'</td>
								<td><span class="small"><a href="/s.php?module=dispatch&action=view&dispatchid='.$item['dispatchid'].'">'.$item['dispatchno'].'</a>
								</span></td>
								<td><span class="small"><a href="/s.php?module=workcenter&action=view&workcenterid='.$item['workcenterid'].'">'.$item['workcenter'].'</a>
								</span></td>
								<td><span class="small"><a href="/s.php?module=routing&action=list">'.$item['routing'].'</a>
								</span></td>
								<td>'.$item['proce'].'</td>
								<td>'.$item['ordering'].'</td>
								<td><span class="bold darkred">'.number_format($item['finishquantity']).'</span></td>
								<td><span class="bold">'.number_format($item['badquantity']).'</span></td>
								<td><span class="bold">'.number_format($item['discardquantity']).'</span></td>
								<td><span class="bold darkred">'.$item['finishhours'].'</span> 时</td>
								<td><span class="bold darkred">'.date('Y-m-d H:i:s',$item['finishdate']).'</span></td>
								<td>'.$item['workersnumber'].' 人</td>
								<td>'.$item['workers'].'</td>
								<td>'.$item['finishremark'].'</td>
								<td><a href="/s.php?module=dispatch&action=viewrepairRecord&recorditemid='.$item['itemid'].'">查看</a>　<a href="/s.php?module=dispatch&action=addrepairRecord&recorditemid='.$item['itemid'].'">新增</a></td>
							</tr>';
									$i++;
						}
					}else{
						$itemtr='<tr><td colspan=18 class="center"><span class="darkred">暂无相关记录</span></td></tr>';
					}
				$body=<<<EOF
<table class="hundred">
<thead><tr><th colspan=18>不良品生产记录</th></tr></thead>
<tbody>
{$itemth}
{$itemtr}
</tbody>
</table>
EOF;
			}else{
			$dc=$this->kclass->DB->queryFirst("
				SELECT dc.*,
					mv.title AS version,
					d.dispatchid,d.dispatchno,
					wc.workcenterid,wc.title AS workcenter,
					r.title AS routing,
					p.title AS proce,p.step
				FROM `dispatchrecorditem` AS dc
				LEFT JOIN dispatchitem AS di ON (di.itemid=dc.dispatchitemid)
				LEFT JOIN `materialversion` AS mv ON (mv.versionid=di.versionid)
				LEFT JOIN dispatch AS d ON (d.dispatchid=di.dispatchid)
				LEFT JOIN workcenter AS wc ON (wc.workcenterid=di.workcenterid)
				LEFT JOIN routing AS r ON (r.routingid=dc.routingid)
				LEFT JOIN `procedure` AS p ON (p.procedureid=dc.procedureid)
				WHERE dc.killed=0 AND dc.itemid=".$this->kclass->input['recorditemid']."
				LIMIT 0,1
			");
			if($dc){
			$dc['created']=date('Y-m-d',$dc['created']);
				$materialInfo=$this->kclass->getMaterial(array('materialid'=>$dc['materialid'],'itemid'=>$dc['materialitemid']));
			$title=$dc['title'];
			$standard=$dc['standard'];
				$record='<tr><td colspan="14" class="">生产记录信息：　[ 维修物资：<a href="'.$materialInfo['url'].'">'.$materialInfo['murl'].'</a> 　'.$materialInfo['material'].' 　'.$materialInfo['standard'].'　(版本：'.$dc['version'].') 　工序：'.$dc['proce'].'　完工数量：<span class="bold">'.number_format($dc['finishquantity']).'</span>　不良品数量：<span class="bold darkred">'.number_format($dc['badquantity']).'</span>　报废数量：<span class="bold">'.number_format($dc['discardquantity']).'</span> ]</td></tr>';
			//维修记录
			for($i = 1; $i < 6; $i++){
				$type='<input type="radio" name="wrongtype['.$i.']" value="1" checked>A: 人为操作问题<br><br><input type="radio" name="wrongtype['.$i.']" value="2">B: 物料问题<br><br></td>';
				$result='<input type="radio" name="repairresult['.$i.']" value="1" checked>A: 合格<br><br><input type="radio" name="repairresult['.$i.']" value="2">B: 报废<br><br></td>';
				$itemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').' small"  align="left">
					<td>'.$i.'</td>
					<td>'.$type.'</td>
					<td><textarea name="describe['.$i.']" style="width:180px;height:70px;"></textarea></td>
					<td><textarea name="analysis['.$i.']" style="width:180px;height:70px;"></textarea></td>
					<td><textarea name="measure['.$i.']" style="width:180px;height:70px;"></textarea></td>
					<td><textarea name="conclusion['.$i.']" style="width:180px;height:70px;">空</textarea></td>
					<td><textarea name="remark['.$i.']" style="width:180px;height:70px;"></textarea></td>
					<td><input type="text" name="quantity['.$i.']" size="10"></td>
					<td><input type="text" id="repairdate'.$i.'" name="repairdate['.$i.']" size="15" /></td>
					<td><input type="text" id="finishdate'.$i.'" name="finishdate['.$i.']" size="15" value="'.date('Y-m-d',TIMENOW).'"/></td>
					<td>'.$result.'</td>
					</tr>';
					$id.='#repairdate'.$i.',';
					$id.='#finishdate'.$i.',';
				}
$body=<<<EOF
<div class="title">生产不良品维修报表</div>
<form action="/s.php?module=dispatch&action=insertrepairRecord" name="dispatch" method="post">
<input type="hidden" name="module" value="dispatch" />
<input type="hidden" name="action" value="insertrepairRecord" />
<input type="hidden" name="recorditemid" value="{$this->kclass->input['recorditemid']}" />
<table class="hundred">
<thead><tr><th colspan="14">新增维修记录</th></tr></thead>
<tbody>
{$record}
<tr class="even" align="left">
<td>ID</td><td>故障类型 <span class="red bold">*</span></td><td>故障现象描述 <span class="red bold">*</span></td><td>故障原因分析 <span class="red bold">*</span></td><td>维修措施 <span class="red bold">*</span></td><td>结论 <span class="red bold">*</span></td><td>备注</td><td>数量 <span class="red bold">*</span></td><td>送修时间 <span class="red bold">*</span></td><td>完工时间 <span class="red bold">*</span></td><td>处理结果 <span class="red bold">*</span></td>
</tr>
{$itemtr}
<tr class="even">
<td class="small gray" colspan="12">
注意事项：<br>
①如果所列表格不够，那么在提交保存后再点击“修改”，来添加所要生产的物资；<br>
②时间的格式为2012-02-06，中间用半角短横杠隔开；<br>
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
			}else{
				$body='数据错误，很抱歉！';
			}
		}

		$this->kclass->page['title'].=' - 新增维修记录';
		$this->kclass->page['onload'].='var dates=$(\''.$id.'\').datepicker({onSelect:function(selectedDate){instance=$(this).data(\'datepicker\'),date=$.datepicker.parseDate(instance.settings.dateFormat || $.datepicker._defaults.dateFormat,selectedDate,instance.settings);}});dc.tabhover();dc.tips();';
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - 新增维修记录','right' => '<span class = "small">操作：</span> <a href="/s.php?module=dispatch&action=list">返回列表</a>','body'=>$body));
		}
		//
		//
		function insertrepairRecord(){
			if($this->kclass->input['requestMethod'] != 'post'){
				$e='<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
			}else{
				for($i=1;$i<6;$i++){
					if($this->kclass->input['describe'][$i]!=''){
						if($this->kclass->input['analysis'][$i]==''){
							$e.='<li>请填写维修记录[ID:'.$i.']中的 故障原因分析</li>';
						}
						if($this->kclass->input['measure'][$i]==''){
							$e.='<li>请填写维修记录[ID:'.$i.']中的 故障维修措施</li>';
						}
						if($this->kclass->input['conclusion'][$i]==''){
							$e.='<li>请填写维修记录[ID:'.$i.']中的 结论</li>';
						}
						if($this->kclass->input['wrongtype'][$i]==''){
							$e.='<li>请填写维修记录[ID:'.$i.']中的 故障类型</li>';
						}
						if($this->kclass->input['repairdate'][$i]==''){
							$e.='<li>请填写维修记录[ID:'.$i.']中的 送修时间</li>';
						}
						if($this->kclass->input['finishdate'][$i]==''){
							$e.='<li>请填写维修记录[ID:'.$i.']中的 完工时间</li>';
						}
						if($this->kclass->input['repairresult'][$i]==''){
							$e.='<li>请填写维修记录[ID:'.$i.']中的 故障处理结果</li>';
						}
						if($this->kclass->input['quantity'][$i]==''){
							$e.='<li>请填写维修记录[ID:'.$i.']中的 维修数量</li>';
						}
					}
				}
			}
			if($e){
				$this->kclass->messager(array(
					'title' => '新建维修记录',
					'text' => '您在新建维修记录的过程中有以下错误：<ul>'.$e.'</ul>',
					'url' => 'javascript:history.back()',
					'sec' => 3
				));
			}
			for($i=1;$i<6;$i++){
				if($this->kclass->input['describe'][$i]!=''){
					if($this->kclass->input['repairdate'][$i]){
						$sd=explode('-',$this->kclass->input['repairdate'][$i]);
						$repairdate=mktime(0,0,0,$sd[1],$sd[2],$sd[0]);
					}else{
						$repairdate=0;
					}
					if($this->kclass->input['finishdate'][$i]){
						$sd1=explode('-',$this->kclass->input['finishdate'][$i]);
						$finishdate=mktime(0,0,0,$sd1[1],$sd1[2],$sd1[0]);
					}else{
						$finishdate=0;
					}
					$this->kclass->DB->query("
						INSERT INTO `repairrecord`
							(`recorditemid`,`describe`,`analysis`,`measure`,`conclusion`,`wrongtype`,`repairdate`,`finishdate`,`repairresult`,`quantity`,`remark`,`created`,`creator`)
						VALUES
							('".$this->kclass->input['recorditemid']."','".$this->kclass->input['describe'][$i]."','".$this->kclass->input['analysis'][$i]."','".$this->kclass->input['measure'][$i]."','".$this->kclass->input['conclusion'][$i]."','".$this->kclass->input['wrongtype'][$i]."','".$repairdate."','".$finishdate."','".$this->kclass->input['repairresult'][$i]."','".$this->kclass->input['quantity'][$i]."','".$this->kclass->input['remark'][$i]."','".TIMENOW."','".$this->kclass->user['userid']."')
					");
				}
			}
			$this->kclass->messager(array(
					'title' => '新建维修记录',
					'text' => '维修记录已新建成功!',
					'url' => '/s.php?module=dispatch&action=viewrepairRecord&recorditemid='.$this->kclass->input['recorditemid'],
					'sec' => 2
				));
		}
		//
		//删除维修记录
		function killrepairRecord(){
			if($this->kclass->input['repairid'] < 0){
				$e = '<li>请从正确的页面进行操作，如果此问题持续存在，请联系网站管理员。</li>';
			}
			$repair=$this->kclass->DB->queryFirst("
				SELECT *
				FROM repairrecord
				WHERE killed=0 AND repairid='".$this->kclass->input['repairid']."'
			");
			if($this->kclass->input['confirm'] == 0){
				$this->kclass->boinkIt('/s.php?module=dispatch&action=viewrepairRecord&recorditemid='.$repair['recorditemid']);
			}

			if(isset($e)){
				$this->kclass->messager(array(
					'title' => '删除维修记录',
					'text' => '您在删除维修记录的过程中有以下错误：<ul>'.$e.'</ul>',
					'url' => 'javascript:history.back()',
					'sec' => 3
				));
			}
			if($repair){
				$this->kclass->DB->query("
					UPDATE `repairrecord`
					SET killed=".TIMENOW.",killer='".$this->kclass->user['userid']."'
					WHERE repairid='".$this->kclass->input['repairid']."'
				");
				$this->kclass->messager(array(
					'title' => '删除维修记录成功',
					'text' => '该条维修记录 已成功被标记为删除!',
					'url' => '/s.php?module=dispatch&action=viewrepairRecord&recorditemid='.$repair['recorditemid'],
					'sec' => 2
				));
			}else{
				$this->kclass->messager(array(
					'title' => '删除维修记录失败',
					'text' => '您要删除的维修记录，不存在！',
					'url' => '/s.php?module=dispatch&action=viewrepairRecord&recorditemid='.$repair['recorditemid'],
					'sec' => 3
				));
			}
		}
		//
			//
		function removerepairRecord(){
			if($this->kclass->input['repairid']<=0){
				$this->kclass->boinkIt('/s.php?module=dispatch');
			}
			$repair=$this->kclass->DB->queryFirst("
				SELECT *
				FROM repairrecord
				WHERE killed=0 AND repairid='".$this->kclass->input['repairid']."'
			");
			if(!$repair){
				$e='<li>数据错误，很抱歉</li>';
			}
			if($e){
				$this->kclass->messager(array(
					'title' => '删除维修记录',
					'text' => '您在删除维修记录的过程中有以下错误：<ul>'.$e.'</ul>',
					'url' => 'javascript:history.back()',
					'sec' => 3
				));
			}
$body = <<<EOF
<form action="/s.php?module=dispatch&action=killrepairRecord" name="dispatch" method="post">
<input type="hidden" name="module" value="dispatch">
<input type="hidden" name="action" value="killrepairRecord">
<input type="hidden" name="repairid" value="{$this->kclass->input['repairid']}">
<table><thead>
<thead>
<tr>
	<th>删除维修记录</th>
</tr>
</thead>
<tbody>
<tr class="even">
	<td class="middle">你确定要删除这条维修记录吗?</td>
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
			$this->kclass->page['title'] .= ' - 删除维修记录 ';
			$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 删除维修记录 ', 'right' => '<a href="/s.php?module=dispatchmodule=dispatch&action=list">返回列表</a>', 'body'=>$body));
		}
		//未开发完成 缺少恢复数量 更改状态 目前用修改生产记录完成这些功能 --gaowenfei 2013/11/25 10:15
		function killQuantity(){
			if($this->kclass->input['trackingid']<=0){
				$this->kclass->boinkIt('/s.php?module=dispatch');
			}
			if($this->kclass->input['trackingid'] < 0){
				$e = '<li>请从正确的页面进行操作，如果此问题持续存在，请联系网站管理员。</li>';
			}
			$tracking=$this->kclass->DB->queryFirst("
				SELECT trackingid,itemid
				FROM orderTracking
				WHERE trackingid='".$this->kclass->input['trackingid']."'
			");
			if($this->kclass->input['confirm'] == 0){
				$this->kclass->boinkIt('/s.php?module=dispatch&action=view&itemid='.$tracking['itemid']);
			}

			if(isset($e)){
				$this->kclass->messager(array(
					'title' => '删除生产记录',
					'text' => '您在删除生产记录的过程中有以下错误：<ul>'.$e.'</ul>',
					'url' => 'javascript:history.back()',
					'sec' => 3
				));
			}

			if($tracking){
				$this->kclass->DB->query("
					UPDATE `orderTracking`
					SET killed=".TIMENOW.",killer='".$this->kclass->user['userid']."'
					WHERE trackingid='".$this->kclass->input['trackingid']."'
				");
				$this->kclass->messager(array(
					'title' => '删除生产记录成功',
					'text' => '该条生产记录 已成功被标记为删除!',
					'url' => '/s.php?module=dispatch&action=view&itemid='.$tracking['itemid'],
					'sec' => 2
				));
			}else{
				$this->kclass->messager(array(
					'title' => '删除生产记录失败',
					'text' => '您要删除的生产记录，不存在！',
					'url' => '/s.php?module=dispatch&action=view&itemid='.$tracking['itemid'],
					'sec' => 3
				));
			}
		}

		//
		function removeQuantity(){
			if($this->kclass->input['trackingid']<=0){
				$this->kclass->boinkIt('/s.php?module=dispatch');
			}
			$tracking=$this->kclass->DB->queryFirst("
				SELECT trackingid,itemid
				FROM orderTracking
				WHERE trackingid='".$this->kclass->input['trackingid']."'
			");
$body = <<<EOF
<form action="/s.php?module=dispatch&action=killQuantity" name="dispatch" method="post">
<input type="hidden" name="module" value="dispatch">
<input type="hidden" name="action" value="killQuantity">
<input type="hidden" name="trackingid" value="{$this->kclass->input['trackingid']}">
<table><thead>
<thead>
<tr>
	<th>删除生产记录</th>
</tr>
</thead>
<tbody>
<tr class="even">
	<td class="middle">你确定要删除这条生产记录吗?</td>
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
			$this->kclass->page['title'] .= ' - 删除生产记录 ';
			$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 删除生产记录 ', 'right' => '<a href="/s.php?module=dispatchmodule=dispatch&action=view&itemid='.$tracking['itemid'].'">返回查看生产记录</a>', 'body'=>$body));
		}

		//
		function revivalQuantity(){
			if($this->kclass->input['trackingid']<=0){
				$this->kclass->boinkIt('/s.php?module=dispatch');
			}
			if($this->kclass->input['trackingid'] < 0){
				$e = '<li>请从正确的页面进行操作，如果此问题持续存在，请联系网站管理员。</li>';
			}
			$tracking=$this->kclass->DB->queryFirst("
				SELECT trackingid,itemid
				FROM orderTracking
				WHERE trackingid='".$this->kclass->input['trackingid']."'
			");
			if($this->kclass->input['confirm'] == 0){
				$this->kclass->boinkIt('/s.php?module=dispatch&action=view&itemid='.$tracking['itemid']);
			}

			if(isset($e)){
				$this->kclass->messager(array(
					'title' => '恢复生产记录',
					'text' => '您在恢复生产记录的过程中有以下错误：<ul>'.$e.'</ul>',
					'url' => 'javascript:history.back()',
					'sec' => 3
				));
			}

			if($tracking){
				$this->kclass->DB->query("
					UPDATE `orderTracking`
					SET killed=0,killer=0
					WHERE trackingid='".$this->kclass->input['trackingid']."'
				");
				$this->kclass->messager(array(
					'title' => '恢复生产记录成功',
					'text' => '该条 生产记录 已成功被恢复！',
					'url' => '/s.php?module=dispatch&action=view&itemid='.$tracking['itemid'],
					'sec' => 2
				));
			}else{
				$this->kclass->messager(array(
					'title' => '恢复生产记录失败',
					'text' => '您要恢复的生产记录不存在！',
					'url' => '/s.php?module=dispatch&action=view&itemid='.$tracking['itemid'],
					'sec' => 3
				));
			}
		}

		//
		function restoreQuantity(){
			if($this->kclass->input['stockid']<=0){
				$this->kclass->boinkIt('/s.php?module=dispatch');
			}
			$tracking=$this->kclass->DB->queryFirst("
				SELECT trackingid,itemid
				FROM orderTracking
				WHERE trackingid='".$this->kclass->input['trackingid']."'
			");
$body = <<<EOF
<form action="/s.php?module=dispatch&action=revival" name="dispatch" method="post">
<input type="hidden" name="module" value="dispatch">
<input type="hidden" name="action" value="revivalQuantity">
<input type="hidden" name="trackingid" value="{$this->kclass->input['trackingid']}">
<table><thead>
<thead>
<tr>
	<th>恢复生产记录</th>
</tr>
</thead>
<tbody>
<tr class="even">
	<td class="middle">你确定要恢复这条生产记录吗?</td>
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
			$this->kclass->page['title'] .= ' - 恢复生产记录';
			$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 恢复生产记录 ', 'right' => '<a href="/s.php?module=dispatch">返回列表</a>', 'body'=>$body));
		}
	//
}
?>