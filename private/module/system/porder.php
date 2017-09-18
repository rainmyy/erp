<?php
//
class porder{
	var $kclass;
	var $purchaseType=array(array('entitle'=>'normal','title'=>'普通采购'),array('entitle'=>'entrust','title'=>'加工采购'));
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
			case 'addTracking':
				return $this->addTracking();
			break;
			case 'insertTracking':
				return $this->insertTracking();
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
			case 'verify':
				$this->verify();
			break;
			case 'doverify':
				$this->doverify();
			case 'approve':
				$this->approve();
			break;
			case 'doapprove':
				$this->doapprove();
			case 'orderStatus':
				$this->orderStatus();
			break;
			case 'doorderStatus':
				$this->doorderStatus();
			break;
			case 'revise':
				$this->revise();
			break;
			case 'addOld':
				$this->addOld();
			break;
			case 'insertOld':
				$this->insertOld();
			break;
			case 'updateOld':
				$this->updateOld();
			break;
			case 'doupdateOld':
				$this->doupdateOld();
			default:
				return $this->mmlist();
		}
	}
	//
	function mmlist(){
		if($this->kclass->input['show']=='all'){
			$condition='1=1';
		}elseif($this->kclass->input['show']=='verify'){
			$condition='`porder`.ifVerify=1';
		}elseif($this->kclass->input['show']=='unverify'){
			$condition='`porder`.ifVerify=-1';
		}elseif($this->kclass->input['show']=='inverify'){
			$condition='`porder`.ifVerify=0';
		}else{
			$condition='`porder`.killed=0 ';
			//$condition='`porder`.killed=0 AND (`porder`.payStatus<>2 OR `porder`.arrivalStatus<>2) AND `porder`.orderStatus=0';
		}
		$keywords=$this->kclass->iif($this->kclass->input['keywords']=='','',' AND 1=1',' AND `porder`.orderno like \'%'.$this->kclass->input['keywords'].'%\' OR `supplier`.title like \'%'.$this->kclass->input['keywords'].'%\'');
		if($this->kclass->input['orderby']!='')$query['orderby']=$this->kclass->input['orderby'];
		if($this->kclass->input['direction']!='')$query['direction']=$this->kclass->input['direction'];
		if($this->kclass->input['show']!='')$query['show']=$this->kclass->input['show'];
		if($this->kclass->input['layout']!='')$query['layout']=$this->kclass->input['layout'];
		$orderby=$this->kclass->orderby(array('module'=>'porder','direction'=>'DESC','orderby'=>'created', 'default'=>'created', 'serial'=>array(array('title'=>'编号', 'field'=>'orderno','word'=>'no'), array('title'=>'修改时间', 'field'=>'modified'), array('title'=>'建立时间', 'field'=>'created')),'appendUrl'=>$query));

		$filterItem=$this->kclass->filterItem(array('module'=>'porder'));
		// 筛选
		$filter=$this->kclass->filter(
			array('module'=>'porder','action'=>'list','table'=>'porder','url'=>$queryPart,'serial'=>array(
				array('name'=>'ifVerify','dname'=>'审核状态','status'=>$filterItem['verify']),
				array('name'=>'ifApprove','dname'=>'审批状态','status'=>$filterItem['approve']),
				array('name'=>'payStatus','dname'=>'付款状态','status'=>array(
					array('value'=>-100,'title'=>'全部'),
					array('value'=>0,'title'=>'未付款'),
					array('value'=>1,'title'=>'部分付款'),
					array('value'=>2,'title'=>'已付款'),)),
				array('name'=>'arrivalStatus','dname'=>'到货状态','status'=>array(
					array('value'=>-100,'title'=>'全部'),
					array('value'=>0,'title'=>'未到货'),
					array('value'=>1,'title'=>'部分到货'),
					array('value'=>2,'title'=>'已到货'),)),
				array('name'=>'orderStatus','dname'=>'订单状态','status'=>array(
					array('value'=>-100,'title'=>'全部'),
					array('value'=>0,'title'=>'未完成'),
					array('value'=>1,'title'=>'已完成'),
					array('value'=>-1,'title'=>'已取消'),)),
				array('name'=>'month','dname'=>'月份','status'=>$filterItem['month'])
		)));
		//规定数据查询起始数$startNum
		if($this->kclass->input['startno']!=""){
			if($this->kclass->input['startno']>=0){
				$startNum = $this->kclass->input['startno'];
			}else{
				$startNum = 0;
			}
		}else{
			$startNum = 0;
		}
		$orders=$this->kclass->DB->query("
			SELECT `porder`.porderid,`porder`.orderno,`porder`.type,`porder`.taxRate,`porder`.purchaseType,`porder`.ifVerify,`porder`.ifApprove,
					`porder`.orderStatus,`porder`.shipmentDate,`porder`.paymentDate,`porder`.payStatus,`porder`.arrivalStatus,
					`porder`.created,`porder`.modified,`porder`.attachs,`porder`.images,`porder`.killed,
				`orderroute`.title AS orderRoute,
				`currency`.title AS currency,
				`supplier`.title AS supplier,`supplier`.supplierid,`supplier`.abbr,
				`pinvoicetype`.pinvoiceTypeid,`pinvoicetype`.title AS pinvoiceType,
				u.username AS creator,
				us.username AS modifier
			FROM `porder`
			LEFT JOIN `orderroute` ON (`orderroute`.routeid=`porder`.routeid)
			LEFT JOIN `supplier` ON (`supplier`.supplierid=`porder`.supplierid)
			LEFT JOIN `currency` ON (`currency`.currencyid=`porder`.currencyid)
			LEFT JOIN `pinvoicetype` ON (`pinvoicetype`.pinvoiceTypeid=`porder`.pinvoiceTypeid)
			LEFT JOIN `user` AS u ON (u.userid=`porder`.creator)
			LEFT JOIN `user` AS us ON (us.userid=`porder`.modifier)
			WHERE ".$condition.$keywords.$filter['link']."
			ORDER BY ".$orderby['sql']." LIMIT ".$startNum.",100
		");
		if($this->kclass->DB->numRows()){
			if($this->kclass->input['layout']=='grid'){
				$body='<ul id="mmlist" class="mmlist clear">';
			}else{
				$body='<table class="hundred"><thead><th width="15">ID</th><th width="90">订单号</th><td width="70">采购类型</td><th width="100">供应商</th><th>订单物资</th><th width="30">币种</th><th width="60">总额</th><th width="60">应付</th><th width="60">已付</th><th width="60">申请时间</th><th width="120">订单状态</th><th width="100">到货及付款状态</th><th width="180"></th></thead><tbody>';
			}
			$i=1;
			while($order=$this->kclass->DB->fetchArray($orders)){
				$alarm='';
				$shipmentDate=date('Y-m-d',$order['shipmentDate']);
				$paymentDate=date('Y-m-d',$order['paymentDate']);
				$order['created']=date('Y-m-d',$order['created']);
				$verify=$this->kclass->verify($order['ifVerify']);
				$approve=$this->kclass->approve($order['ifApprove']);
				if($order['ifVerify']==0){
					$alarm='bgYellow';
				}elseif($order['ifVerify']==-1){
					$alarm='bgRed';
				}
				if($order['arrivalStatus']==0){
					$arrivalStatus='未到货';
				}elseif($order['arrivalStatus']==1){
					$arrivalStatus='部分到货';
				}if($order['arrivalStatus']==2){
					$arrivalStatus='已到货';
				}
				if($order['payStatus']==0){
					$payStatus='未付款';
				}elseif($order['payStatus']==1){
					$payStatus='部分付款';
				}if($order['payStatus']==2){
					$payStatus='已付款';
				}

				foreach($this->purchaseType AS $key=>$val){
					if($order['purchaseType']==$val['entitle']){
						$purchaseType=$val['title'];
					}
				}
				$link='';
				if($order['killed']>0){
					$link='<a href="/s.php?module=porder&action=restore&porderid='.$order['porderid'].'&rt=list">恢复</a>';
				}else{
					if($order['ifApprove']==1){
						$link=$this->kclass->iif($order['orderStatus']==0,' <a href="/s.php?module=porder&action=orderStatus&porderid='.$order['porderid'].'">完成/取消</a>','').' <a href="/p.php?action=porder&porderid='.$order['porderid'].'" target="_blank">印</a> <a href="/s.php?module=accounting&action=addPayment&supplierid='.$order['supplierid'].'" target="_blank"> 付 </a>';
					}elseif($order['ifApprove']==0){
						if($order['ifVerify']==1){
							$link='<a href="/s.php?module=porder&action=approve&porderid='.$order['porderid'].'">批</a>';
						}elseif($order['ifVerify']==0){
							$link='<a href="/s.php?module=porder&action=verify&porderid='.$order['porderid'].'">审</a> <a href="/s.php?module=porder&action=kill&porderid='.$order['porderid'].'&rt=list" onclick="return confirm(\'你确定要删除这个订单 '.$order['orderno'].' 吗？\');">删</a>';
						}elseif($order['ifVerify']==-1){
							$link='<a href="/s.php?module=porder&action=kill&porderid='.$order['porderid'].'&rt=list" onclick="return confirm(\'你确定要删除这个订单 '.$order['orderno'].' 吗？\');">删</a>';
						}
					}
				}
				$link.=$this->kclass->iif($order['payStatus']==0 AND $order['arrivalStatus']==0 AND $order['orderStatus']==0,' <a href="/s.php?module=porder&action=update&porderid='.$order['porderid'].'&rt=list">改','');
				// 订单物资
				$orderItem='';
				$items=$this->kclass->DB->query("SELECT m.title FROM `item` AS i LEFT JOIN material AS m ON (m.materialid=i.materialid) WHERE module='porder' AND mid='".$order['porderid']."'");
				if($this->kclass->DB->numRows()){
					while($item=$this->kclass->DB->fetchArray($items)){
						$orderItem.=$item['title'].' ；';
					}
				}
				$total=$this->kclass->DB->queryFirst("SELECT SUM(amount) AS amount FROM item WHERE killed=0 AND module='porder' AND mid='".$order['porderid']."' LIMIT 0,1");// 总金额
				$payment=$this->kclass->DB->queryFirst("SELECT SUM(ordertracking.amount) AS amount FROM ordertracking LEFT JOIN payment ON (ordertracking.paymentid=payment.paymentid) WHERE ordertracking.killed=0 AND payment.killed=0 AND ordertracking.paymentid<>0 AND ordertracking.module='porder' AND ordertracking.mid='".$order['porderid']."' LIMIT 0,1");// 已付
				$debitnote=$total['amount']-$payment['amount'];// 应付
				if($this->kclass->input['layout']=='grid'){//表格
					$body .= '<li title="由 '.$order['creator'].' 建于 '.$order['created'].$this->kclass->iif($order['modifier']!='', '，'.$order['modifier'].' 改于 '.date('Y-m-d',$order['modified']), '').'"'.$this->kclass->iif($i%4==0, ' class="end"', '').'>
					<div class="mmlistt">
						<span class="right normal">';
					if($order['killed']>0){
						$body.='<a href="/s.php?module=porder&action=revival&porderid='.$order['porderid'].'&rt=list" onclick="return confirm(\'你确定要恢复这个订单 '.$order['orderno'].' 吗？\');">恢复</a>';
					}else{
						$body.='<a href="/s.php?module=porder&action=kill&porderid='.$order['porderid'].'&rt=list" onclick="return confirm(\'你确定要删除这个订单 '.$order['orderno'].' 吗？\');">删</a> '.$this->kclass->iif($order['ifVerify']==0,'<a href="/s.php?module=porder&action=update&porderid='.$order['porderid'].'&rt=list">改</a>','');
					}
					$body.='</span>
						<span class="small">'.$order['attr'].'</span> <a href="/s.php?module=porder&action=view&porderid='.$order['porderid'].'">'.$order['orderno'].'</a><span class="small">('.$verify.')</span><br><span class="small gray right">'.$order['created'].'</span>'.$this->kclass->iif($order['attachs']>0, ' <span class="attachFile" title="有'.$order['attachs'].'个附件。"></span> ', '').$this->kclass->iif($order['images']>0, ' <span class="attachImage" title="有'.$order['images'].'个图片。"></span> ', '').'<span class="small gray">'.$this->kclass->iif($order['pinvoiceTypeid']>0,$order['pinvoiceType'],'无发票').'</span>('.($order['taxRate']).'%)</div>
					<div class="mmlistb">
						<div title="'.$order['orderRoute'].'"><span class="small gray">类型：'.$order['orderRoute'].'</span></div>
						<div title="'.$order['orderRoute'].'"><span class="small gray">采购类型：'.$purchaseType.'</span></div>';
					if($order['purchaseType']=='entrust'){
						$body.='<div title="'.$order['orderRoute'].'"><span class="small gray">给付日期：'.$paymentDate.'</span></div>';
					}
					$body.='<div title="'.$order['supplier'].'"><span class="small gray">供应商：<a href="/s.php?module=psupplier&action=view&supplierid='.$order['supplierid'].'">'.$order['supplier'].'</span></a></div>
						<div class="small clear"><span class=right title="由 '.$order['creator'].' 建于 '.$order['created'].'">由 '.$order['creator'].' 建于 '.$order['created'].'</span></div></div></li>';
				}else{
					$body .='<tr class="'.$this->kclass->rotateLine().' '.$alarm.' small">
						<td>'.$i.'</td>
						<td><a href="/s.php?module=porder&action=view&porderid='.$order['porderid'].'" class="normal">'.$order['orderno'].$this->kclass->iif($order['attachs']>0, ' <span class="attachFile" title="有'.$order['attachs'].'个附件。"></span> ', '').$this->kclass->iif($order['images']>0, ' <span class="attachImage" title="有'.$order['images'].'个图片。"></span> ', '').'</a></td>
						<td>'.$purchaseType.'</td>
						<td title="'.$order['supplier'].'"><a  class="small" href="/s.php?module=psupplier&action=view&supplierid='.$order['supplierid'].'">'.$order['abbr'].'</a></td>
						<td>'.$orderItem.'</td>
						<td align="right">'.$order['currency'].'</td>
						<td align="right">'.$total['amount'].'</td>
						<td align="right">'.$this->kclass->iif($debitnote<0,0,$debitnote).'</td>
						<td align="right">'.$this->kclass->iif($payment['amount'],$payment['amount'],0).'</td>
						<td>'.$order['created'].'</td>
						<td><span class="small">'.$verify.' , '.$approve.$this->kclass->iif($order['orderStatus']==1,' , 已完成','').$this->kclass->iif($order['orderStatus']==-1,' , 已取消','').'</span></td>
						<td>'.$arrivalStatus.' , '.$payStatus.'</td>
						<td align="center" class="small">'.$link.'</td>
					</tr>';
				}
				$i++;
			}
			if($this->kclass->input['layout']=='grid'){
				$body .= '</ul>';
				//列表分页
				$uriParams = explode("list",URI)[1];
				$counter = $this->kclass->DB->queryFirst("SELECT COUNT(porderid) FROM `porder` WHERE ".$condition.$keywords.$filter['link']."
			ORDER BY ".$orderby['sql']."");
				$count = $counter['COUNT(porderid)'];
				$pagesize = 10;
				$topage=$this->kclass->commonPaging(array('module'=>'porder','action'=>'list','count'=>$count,'pagesize'=>$pagesize,'startNum'=>$startNum,'uriParams'=>$uriParams));
				$body .= '<table width="100%"><tr><td align="center">'.$topage.'</td></tr></table>';
			}else{
			$data = $body;
			$data = htmlspecialchars($data);
			//var_dump($data);
				$body.='<tr><th colspan="13" align="left">导出EXCEL</th></tr>
				<tr><td colspan="13" align="right">
						<form action="/e.php?module=excel&action=porder" method="post">
							<input type="hidden" name="title" value="采购列表" />
							<input type="hidden" name="condition" value="'.$condition.'" />
							<input type="hidden" name="keywords" value="'.$keywords.'" />
							<input type="hidden" name="filter" value="'.$filter['link'].'" />
							<input type="hidden" name="orderby" value="'.$orderby['sql'].'" />
							<input type="hidden" name="startNum" value="'.$startNum.'" />
							<select name="exportType">
								<option value="">选择导出类型</option>
								<option value="a">订单列表</option>
								<option value="b">订单列表详细</option>
							</select>
							<input type="submit" value="导出EXCEL" />
						</form>
					</td></tr></tbody></table>';
				//列表分页
				$uriParams = str_replace("&startno","",explode("&startno",explode("list",URI)[1])[0]);//获取uri动态参数部分并去除分页查询起始参数
				$counter = $this->kclass->DB->queryFirst("SELECT COUNT(porderid) FROM `porder` WHERE ".$condition.$keywords.$filter['link']."
			ORDER BY ".$orderby['sql']."");
				$count = $counter['COUNT(porderid)'];
				$pagesize = 100;
				$topage=$this->kclass->commonPaging(array('module'=>'porder','action'=>'list','count'=>$count,'pagesize'=>$pagesize,'startNum'=>$startNum,'uriParams'=>$uriParams));
				$body .= '<table width="100%"><tr><td align="center">'.$topage.'</td></tr></table>';
			}
		}else{
			$body='暂无相关记录';
		}
		if($this->kclass->input['layout']=='grid'){
			$layoutLink='<a href="/s.php?module=porder&action=list&layout=list'.str_replace('&layout=grid', '', $queryPart).'">列表</a> 格子';
		}else{
			$layoutLink='列表 <a href="/s.php?module=porder&action=list&layout=grid'.str_replace('&layout=list', '', $queryPart).'">格子</a>';
		}
		if($this->kclass->input['show']=='all'){
			$showLink='<a href="/s.php?module=porder&action=list'.str_replace('&show=all', '', $queryPart).'">默认</a> 所有<span class="small gray">(包括删除)</span> <a href="/s.php?module=porder&action=list&show=verify'.str_replace('&show=all', '', $queryPart).'">已通过</a>  <a href="/s.php?module=porder&action=list&show=inverify'.str_replace('&show=all', '', $queryPart).'">待审核</a>  <a href="/s.php?module=porder&action=list&show=unverify'.str_replace('&show=all', '', $queryPart).'">未通过</a>';
		}elseif($this->kclass->input['show']=='verify'){
			$showLink='<a href="/s.php?module=porder&action=list'.str_replace('&show=verify', '', $queryPart).'">默认</a> <a href="/s.php?module=porder&action=list&show=all'.str_replace('&show=verify', '', $queryPart).'">所有<span class="small gray">(包括删除)</span></a> 已通过 <a href="/s.php?module=porder&action=list&show=inverify'.str_replace('&show=verify', '', $queryPart).'">待审核</a>  <a href="/s.php?module=porder&action=list&show=unverify'.str_replace('&show=verify', '', $queryPart).'">未通过</a>';
		}elseif($this->kclass->input['show']=='inverify'){
			$showLink='<a href="/s.php?module=porder&action=list'.str_replace('&show=inverify', '', $queryPart).'">默认</a> <a href="/s.php?module=porder&action=list&show=all'.str_replace('&show=inverify', '', $queryPart).'">所有<span class="small gray">(包括删除)</span></a> <a href="/s.php?module=porder&action=list&show=verify'.str_replace('&show=inverify', '', $queryPart).'">已通过</a> 待审核 <a href="/s.php?module=porder&action=list&show=unverify'.str_replace('&show=inverify', '', $queryPart).'">未通过</a>';
		}elseif($this->kclass->input['show']=='unverify'){
			$showLink='<a href="/s.php?module=porder&action=list'.str_replace('&show=unverify', '', $queryPart).'">默认</a> <a href="/s.php?module=porder&action=list&show=all'.str_replace('&show=unverify', '', $queryPart).'">所有<span class="small gray">(包括删除)</span></a> <a href="/s.php?module=porder&action=list&show=inverify'.str_replace('&show=unverify', '', $queryPart).'">已通过</a> <a href="/s.php?module=porder&action=list&show=inverify'.str_replace('&show=unverify', '', $queryPart).'">待审核</a> 未通过';
		}else{
			$showLink='默认 <a href="/s.php?module=porder&action=list&show=all'.str_replace('&show=all', '', $queryPart).'">所有<span class="small gray">(包括删除)</span></a> <a href="/s.php?module=porder&action=list&show=verify'.str_replace('&show=all', '', $queryPart).'">已通过</a>  <a href="/s.php?module=porder&action=list&show=inverify'.str_replace('&show=all', '', $queryPart).'">待审核</a>  <a href="/s.php?module=porder&action=list&show=unverify'.str_replace('&show=all', '', $queryPart).'">未通过</a>';
		}
		$this->kclass->page['title'].='采购订单列表';
		$this->kclass->page['onload'].='dc.listhover();dc.tabhover();';
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - 采购订单列表','right' => '<span class="small">排序：</span>'.$orderby['link'].'|　<span class="small">显示方式：</span>'.$showLink.'　|　<span class="small">布局：</span>'.$layoutLink.'　|　<a href="/s.php?module=porder&action=add">新建</a>　|　<a href="/s.php?module=porder&action=addOld">新增旧订单</a>','body'=>$filter['panel'].$body));
	}
		//
	function find(){
		if($this->kclass->input['keywords']==''){
$body=<<<EOF
<form method="get" action="/s.php?module=porder&action=list">
<input type="hidden" name="module" value="porder">
<input type="hidden" name="action" value="list">
<table>
<thead><tr><th colspan=2>查找订单：</th></tr></thead>
<tbody>
<tr><td>请输入查找关键词：</td><td><input type="text" name="keywords" style="width:350px"></td></tr>
<tr><td colspan=2 class="small gray">注意：请输入要查找订单的编号</td></tr>
<tr class="center"><td colspan=2><input type="submit" value=" 提交 "><input type="reset" value=" 重置 "></td></tr>
</tbody>
</table>
</form>
EOF;
		}
		$this->kclass->page['title'].='订单查找';
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - 订单查找','right' => '<a href="/s.php?module=porder">返回列表</a>','body'=>$body));
	}
	//
	function view(){
		$order=$this->kclass->DB->queryFirst("
			SELECT o.porderid,o.supplierid,o.customerNo,o.supplierNo,o.orderno,o.type,o.purchaseType,o.shipmentDate,o.paymentDate,o.delivery,o.orderStatus,
					o.taxRate,o.consignee,o.consigneeTel,o.special,o.spare,o.verifier,o.ifVerify,o.verified,o.verifyRemark,o.approver,o.ifApprove,o.approved,
					o.approvalRemark,o.remark,o.modified,o.created,o.killed,o.payStatus,o.arrivalStatus,
				od.routeid,od.title AS route,od.caption,
				it.title AS invoiceType,
				s.title AS supplier,s.supplierno,s.address AS supplierAddress,s.linkman AS supplierLinkman,s.mobile AS supplierMobile,
					s.telephone AS supplierTelephone,s.email AS supplierEmail,s.fax AS supplierFax,
				cu.cntitle AS customer,cu.customerno,cu.cnaddress AS customerAddress,cu.linkman AS customerLinkman,cu.mobile AS customerMobile,
					cu.telephone AS customerTelephone,cu.email AS customerEmail,cu.fax AS customerFax,
				c.title AS currency,c.symbol AS encurrency,
				p.title AS paymentterm,p.entitle AS enpaymentterm,
				d.title AS deliveryterm,d.entitle AS endeliveryterm,
				u.username AS creator,
				us.username AS modifier,
				ur.username AS overifier,
				usr.username AS oapprover
			FROM `porder` AS o
			LEFT JOIN `orderroute` AS od ON (od.routeid=o.routeid)
			LEFT JOIN `pinvoicetype` AS it ON (it.pinvoiceTypeid=o.pinvoiceTypeid)
			LEFT JOIN `supplier` AS s ON (s.supplierid=o.supplierid)
			LEFT JOIN `customer` AS cu ON (cu.customerid=o.customerid)
			LEFT JOIN `currency` AS c ON (c.currencyid=o.currencyid)
			LEFT JOIN `paymentterm` AS p ON (p.paymenttermid=o.paymenttermid)
			LEFT JOIN `deliveryterm` AS d ON (d.deliverytermid=o.deliverytermid)
			LEFT JOIN `user` AS u ON (u.userid=o.creator)
			LEFT JOIN `user` AS us ON (us.userid=o.modifier)
			LEFT JOIN `user` AS ur ON (ur.userid=o.verifier)
			LEFT JOIN `user` AS usr ON (usr.userid=o.approver)
			WHERE o.routeid=4 AND o.type='PO' AND o.porderid='".$this->kclass->input['porderid']."'
			LIMIT 0,1
		");
		if(!$order){
			$e.='<li>数据错误，很抱歉！</li>';
		}

		if($order['killed']>0){
			$link='<a href="/s.php?module=porder&action=restore&porderid='.$order['porderid'].'&rt=list">恢复</a>';
		}else{
			if($order['orderStatus']==0){// 已到货已付款更新为订单完成状态
				if($order['payStatus']==2 AND $order['arrivalStatus']==2){
					$this->kclass->DB->query("UPDATE porder SET orderStatus=1,operater='".$this->kclass->user['userid']."',operated='".TIMENOW."' WHERE porderid='".$order['porderid']."'");
				}
			}
			if($order['ifApprove']==1){
				if($order['orderStatus']==0){
					$link.='<span class="small">打印：</span>
						<a href="/p.php?action=porder&porderid='.$order['porderid'].'" target="_blank">订单</a> <a href="/p.php?action=quoting&porderid='.$order['porderid'].'" target="_blank">报价表</a>　|　<span class="small">新建：</span>
						<a href="/s.php?module=porder&action=add">订单</a>
						<a href="/s.php?module=inbound&action=add&typeid=4&porderid='.$order['porderid'].'">入库单</a>
						<a href="/s.php?module=pinvoice&action=add&porderid='.$order['porderid'].'&step=2&supplierid='.$order['supplierid'].'">发票</a>
						<a href="/s.php?module=accounting&action=addPayment&supplierid='.$order['supplierid'].'">已付</a>
						<a href="/s.php?module=service&action=addClaim&porderid='.$order['porderid'].'">索赔</a>
						<a href="/s.php?module=service&action=addReturn&porderid='.$order['porderid'].'">退货</a>　|　<span class="small">操作：</span> <a href="/s.php?module=porder&action=orderStatus&porderid='.$order['porderid'].'">完成/取消订单';
				}
			}elseif($order['ifApprove']==0){
				if($order['ifVerify']==1){
					$link.='<span class="small">操作：</span><a href="/s.php?module=porder&action=approve&porderid='.$order['porderid'].'">审批</a>';
				}elseif($order['ifVerify']==0){
					$link.='<span class="small">操作：</span><a href="/s.php?module=porder&action=verify&porderid='.$order['porderid'].'">审核</a> <a href="/s.php?module=porder&action=kill&porderid='.$order['porderid'].'&rt=view">删除</a>';
				}elseif($order['ifVerify']==-1){
					$link.='<span class="small">操作：</span><a href="/s.php?module=porder&action=kill&porderid='.$order['porderid'].'&rt=list">删除</a>';
				}
			}
		}
		$link.=$this->kclass->iif($order['payStatus']==2 AND $order['arrivalStatus']==2 AND $order['orderStatus']==2,'　|　<a href="/s.php?module=porder&action=update&porderid='.$order['porderid'].'&rt=list">修改</a>','');
		$view=$this->_view($this->kclass->input['porderid']);
		$created=date('Y-m-d H:i:s',$order['created']);
		$verify=$this->kclass->verify($order['ifVerify']);
		$approve=$this->kclass->approve($order['ifApprove']);
		$tracking=$this->kclass->relatedOrderTracking(array('porderid'=>$order['porderid']));
		$invoice=$this->kclass->relatedPInvoice(array('porderid'=>$order['porderid']));
		$debitnote=$this->kclass->relatedDebitnote(array('module'=>'porder','mid'=>$order['porderid']));
		$payment=$this->kclass->relatedPayment(array('module'=>'porder','mid'=>$order['porderid']));
		$claim=$this->kclass->relatedClaim(array('module'=>'porder','mid'=>$order['porderid']));
		$return=$this->kclass->relatedReturn(array('module'=>'porder','mid'=>$order['porderid']));

		$body=<<<EOF
<div class="title"><span class="right small gray">由{$order['creator']}于{$created}建立{$modify}{$overify}。</span>{$order['orderno']}</div>
<dl id="order" class="tabs" style="display: block;">
<dt tabid="0">PO (采购订单)</dt>
<dt title="此订票的物资跟踪情况">物资跟踪</dt>
<dt title="与此订单相关的发票"{$invoice['off']}>发票{$invoice['count']}</dt>
<dt title="销售佣金记录" disabled>佣金</dt>
<dt title="与订单相关的定金记录" disabled>定金</dt>
<dt title="与订单相关的保证金记录" disabled>保证金</dt>
<dt title="与订单相关的应收款项" disabled>应收</dt>
<dt title="与订单相关的已经收到的款项" disabled>已收</dt>
<dt title="与订单相关的应该付款的款项"{$debitnote['off']}>应付{$debitnote['count']}</dt>
<dt title="与订单相关的已经付款的款项"{$payment['off']}>已付{$payment['count']}</dt>
<dt title="与订单相关的订单服务信息" disabled>客服</dt>
<dt title="与订单相关的索赔记录"{$claim['off']}>索赔{$claim['count']}</dt>
<dt title="订单的退货记录"{$return['off']}>退货{$return['count']}</dt>
<dt title="订单的换货记录" disabled>换货</dt>
<dd>{$view['html']}</dd>
<dd>{$tracking}</dd>
<dd>{$invoice['panel']}</dd>
<dd></dd>
<dd></dd>
<dd></dd>
<dd>
<div class="gray block"><span class="right normal"><span class="small">新建：</span><a href="/s.php?module=accounting&action=add&porderid={$order['porderid']}">应收</a>　|　<a href="/s.php?module=porder&action=update&porderid={$order['porderid']}">修改</a></span></div>
</dd>
<dd>
<div class="gray block"><span class="right normal"><span class="small">新建：</span><a href="/s.php?module=accounting&action=add&porderid={$order['porderid']}">已收</a>　|　<a href="/s.php?module=porder&action=update&porderid={$order['porderid']}">修改</a></span></div>
</dd>
<dd>{$debitnote['panel']}</dd>
<dd>{$payment['panel']}</dd>
<dd>
</dd>
<dd>{$claim['panel']}</dd>
<dd>{$return['panel']}</dd>
<dd>
</dd>
EOF;
		$this->kclass->page['title'].='采购订单';
		$this->kclass->page['onload'].='dc.tabhover();dc.tabs({\'id\':\'order\'});dc.show()';
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - <a href="/s.php?module=porder&action=list">订单列表</a> - 查看订单','right'=>$link,'body'=>$body));
	}
	//
	function add(){
		$orderno=$this->kclass->id(array('porder'=>TIMENOW));
		if($this->kclass->input['step']==''){
			if($this->kclass->input['purchaseType']==''){
				foreach($this->purchaseType as $key => $val){
					$option.='<input type="radio" name="purchaseType" value="'.$val['entitle'].'">'.$val['title'];
				}
				$body=<<<EOF
<form action="/s.php?module=porder&action=add" method="get">
<input type="hidden" name="module" value="porder">
<input type="hidden" name="action" value="add">
<table style="width:450px;">
<thead><tr><th colspan=2>新建订单：选择采购类型 <span class="small red">(由采购部填写)</span></th></tr></thead>
<tbody>
<tr><td>选择采购类型</td><td>{$option}</td></tr>
<tr class="center"><td colspan=2><input type="submit" value=" 提交 "><input type="reset" value=" 重置 "></td></tr>
</tbody>
</table>
EOF;
			}else{
				if($this->kclass->input['purchaseType']=='normal'){
					$purchases=$this->kclass->DB->query("SELECT * FROM `purchase` WHERE killed=0 AND ifVerify=1 ORDER BY created DESC");
					if($this->kclass->DB->numRows()){
						$i=1;
						while($purchase=$this->kclass->DB->fetchArray($purchases)){
						//计划的物资
							$items=$this->kclass->DB->query("
								SELECT pi.itemid,pi.module,pi.mid,pi.mitemid,pi.materialid,pi.quantity,pi.unitid,pi.supplierid,pi.price,pi.discount,pi.arrivalTime,pi.remark,
									m.materialno,m.title,m.standard,
									mv.title AS version,
									pii.materialno AS newMaterialno,pii.title AS newTitle,pii.standard AS newStandard,
									s.title AS supplier,
									b.title AS brand,
									u.title AS unit
									FROM purchaseitem AS pi
								LEFT JOIN material AS m ON (m.materialid=pi.materialid)
								LEFT JOIN materialversion AS mv ON (mv.versionid=pi.versionid)
								LEFT JOIN preinbounditem AS pii ON (pii.itemid=pi.mitemid)
								LEFT JOIN supplier AS s ON (s.supplierid=pi.supplierid)
								LEFT JOIN brand AS b ON (b.brandid=pi.brandid)
								LEFT JOIN `unit` AS u ON (pi.unitid=u.unitid)
								WHERE pi.killed=0 AND pi.ifChooser=0 AND pi.module='purchaseApply' AND pi.purchaseid='".$purchase['purchaseid']."'
								ORDER BY pi.itemid ASC
							");
							if($this->kclass->DB->numRows()){
								$k=1;
								$itemtr.='<tbody><tr class="bold "><input type="hidden" name="purchaseId['.$k.']" value="'.$purchase['purchaseid'].'"><td colspan=15>采购计划单编号：<a class="middle bold" href="/s.php?module=purchase&action=view&purchaseid='.$purchase['purchaseid'].'">'.$purchase['purchaseno'].'</a>　申请人：'.$purchase['applicant'].'　申请时间：'.date('Y-m-d',$purchase['created']).'</td></tr><tr class="center even" nohover><td width="15">ID</td><td>物资</td><td width="30">版本</td><td>供应商</td><td>品牌</td><td>数量 <span class="red bold">*</span></td><td>单价 <span class="red bold">*</span></td><td>折扣</td><td width="70">到货日期 <span class="red bold">*</span></td><td>备注</td><td>选</td></tr>';
								while($item=$this->kclass->DB->fetchArray($items)){
									$materialno=$this->kclass->iif($item['materialid']==0,$item['newMaterialno'],'<a href="/s.php?module=material&action=view&materialid='.$item['materialid'].'">'.$item['materialno'].'</a>');
									$title=$this->kclass->iif($item['materialid']==0,$item['newTitle'],$item['title']);
									$standard=$this->kclass->iif($item['materialid']==0,$item['newStandard'],$item['standard']);
									$itemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').' small">
										<input type="hidden" name="itemId['.$i.']" value="'.$item['itemid'].'">
										<td>'.$k.'</td>
										<td>'.$materialno.'　'.$title.'　'.$standard.'</td>
										<td>'.$item['version'].'</td>
										<td><a href="/s.php?module=psupplier&action=view&supplierid='.$item['supplierid'].'">'.$item['supplier'].'</a></td>
										<td>'.$item['brand'].'</td>
										<td>'.$item['quantity'].' '.$item['unit'].'</td>
										<td>'.$item['price'].'</td>
										<td>'.$item['discount'].'%</td>
										<td>'.date('Y-m-d',$item['arrivalTime']).'</td>
										<td>'.$item['remark'].'</td>
										<td><input type="checkbox" name="itemSelect['.$i.']" value="'.$item['itemid'].'"></td></tr>';
									$k++;$i++;
								}
							}
						}
					}
					$itemtr.='';
				}elseif($this->kclass->input['purchaseType']=='entrust'){
					$approvePurchases=$this->kclass->DB->query("
						SELECT `purchase`.*,
							c.title AS currency,
							u.username AS creator,
							us.username AS modifier
						FROM `purchase` 
						LEFT JOIN currency AS c ON (c.currencyid=`purchase`.currencyid)
						LEFT JOIN user AS u ON (u.userid=`purchase`.creator)
						LEFT JOIN user AS us ON (us.userid=`purchase`.modifier)
						WHERE `purchase`.killed=0 AND `purchase`.ifVerify=1 AND `purchase`.ifApprove=1
						ORDER BY purchaseno DESC
					");
					if($this->kclass->DB->numRows()){
						$approveCounter=0;
						$i=1;$k=1;
						while($approvePurchase=$this->kclass->DB->fetchArray($approvePurchases)){
							$items=$this->kclass->DB->query("
								SELECT pi.itemid,pi.module,pi.mid,pi.mitemid,pi.materialid,pi.quantity,pi.unitid,pi.supplierid,pi.price,pi.discount,pi.arrivalTime,pi.remark,
									m.entrustMaterialid,
									pii.materialno AS newMaterialno,pii.title AS newTitle,pii.standard AS newStandard,
									s.title AS supplier,
									b.title AS brand,
									u.title AS unit
								FROM purchaseitem AS pi
								LEFT JOIN material AS m ON (m.materialid=pi.materialid)
								LEFT JOIN preinbounditem AS pii ON (pii.itemid=pi.mitemid)
								LEFT JOIN supplier AS s ON (s.supplierid=pi.supplierid)
								LEFT JOIN brand AS b ON (b.brandid=pi.brandid)
								LEFT JOIN `unit` AS u ON (pi.unitid=u.unitid)
								WHERE pi.killed=0 AND pi.ifChooser=0 AND pi.purchaseid='{$approvePurchase['purchaseid']}' AND pi.module='entrust'
								ORDER BY pi.itemid ASC
							");
							$approveItemCounter+=$this->kclass->DB->numRows($items);
							if($this->kclass->DB->numRows($items)){
								$approveCounter++;
								$m=1;
								$itemtr.='<tbody>
								<tr class="bold ">
								<th colspan=15>采购计划单编号：<a class="middle bold" href="/s.php?module=purchase&action=view&purchaseid='.$approvePurchase['purchaseid'].'">'.$approvePurchase['purchaseno'].'</a>　申请人：'.$approvePurchase['applicant'].'　申请时间：'.date('Y-m-d',$approvePurchase['created']).'</th></tr></thead>
								<tr class="center even"><td width="15">ID</td><td width="60">申请类型</td><td width="50">申请单编号</td><td>物资编号</td><td>物资名称</td><td>物资规格</td><td>供应商</td><td>数量/单位</td><td>币种</td><td>单价</td><td>折扣</td><td>总价</td><td>到货时间</td><td>备注</td><td>选</td></tr>';
								while($item=$this->kclass->DB->fetchArray($items)){
									$item['arrivalTime']=date('Y-m-d',$item['arrivalTime']);
									$class=$this->kclass->iif($i%2==0,'class="even"','class="odd"');
									$mitem=$this->kclass->DB->queryFirst("SELECT ei.*,e.entrustid,e.entrustno FROM entrustitem AS ei LEFT JOIN entrust AS e ON (e.entrustid=ei.entrustid) WHERE ei.killed=0 AND ei.itemid='".$item['mitemid']."'");
									$type="外发加工";
									$typeno='<a href="/s.php?module=entrust&action=view&entrustid='.$mitem['entrustid'].'">'.$mitem['entrustno'].'</a>';
									$materialid = $item['entrustMaterialid'];
									if($materialid!=""){
										$query = $this->kclass->DB->queryFirst("SELECT m.materialno,m.standard,m.title,m.unitid,u.title as utitle
																				FROM `material` AS m
																				LEFT JOIN `unit` AS u ON (u.unitid=m.unitid)
																				WHERE materialid='{$materialid}'");
										$materialno = $query['materialno'];
										$standard = $query['standard'];	
										$title = $query['title'];
										$unit = $query['utitle'];
										$unitid = $query['unitid'];
									}
									$itemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').' small">
										<input type="hidden" name="itemId['.$i.']" value="'.$item['itemid'].'">
										<td>'.$m.'</td>
										<td>'.$type.'</td>
										<td>'.$typeno.'</td>
										<td><a href="/s.php?module=material&action=view&materialid='.$materialid.'">'.$materialno.'</a></td>
										<td>'.$title.'</td>
										<td>'.$standard.'</td>
										<td><a href="/s.php?module=psupplier&action=view&supplierid='.$item['supplierid'].'">'.$item['supplier'].'</a></td>
										<td>'.$item['quantity'].' '.$unit.'</td>
										<td>'.$item['currency'].'</td>
										<td>'.$item['price'].'</td>
										<td>'.$item['discount'].'%</td>
										<td>'.$item['total'].'</td>
										<td>'.$item['arrivalTime'].'</td>
										<td>'.$item['remark'].'</td>
										<td><input type="checkbox" name="itemSelect['.$i.']" value="'.$item['itemid'].'"></td></tr>';
									$m++;$i++;
								}
								$itemtr.='</tbody>';
								$k++;
							}
						}

						$approveBody['count']='('.$approveCounter.')';
					}
					if($approveItemCounter==0){
						$approveBody['off']=' disabled';
					}

				}
					
					$title=$this->kclass->iif($this->kclass->input['purchaseType']=='entrust','选择外发加工单明细','选择采购计划单明细');
				$body=<<<EOF
<form action="/s.php?module=porder&action=add" method="post">
<input type="hidden" name="module" value="porder" />
<input type="hidden" name="action" value="add" />
<input type="hidden" name="orderno" value="{$orderno}" />
<input type="hidden" name="step" value="2" />
<input type="hidden" name="purchaseType" value="{$this->kclass->input['purchaseType']}" />
<table class="hundred"><thead><tr><th colspan=15>{$title}</th></tr></thead>
{$itemtr}

<tr class="center">
<td colspan=15><input type="submit" value="提交"><input type="reset" value="重置"></td>
</tr>
</tbody></table>
</form>
EOF;
			}
		}elseif($this->kclass->input['step']==2){
			$supplier=$this->kclass->chooserSupplier(array('hName'=>'supplierid','name'=>'supplier','width'=>400));
			$selectCustomer=$this->kclass->DB->queryFirst("SELECT cntitle FROM customer WHERE killed=0 AND customerid=2 LIMIT 0,1");
			$customer=$this->kclass->chooserCustomer(array('hName'=>'customerid','name'=>'customer','width'=>450,'selectedid'=>2,'value'=>$selectCustomer['cntitle']));
			$deliveryterm=$this->kclass->chooserDeliveryterm(array('name'=>'deliverytermid','width'=>200,'hasBlank'=>1,'topname'=>'请选择','selectedid'=>8));
			$paymentterm=$this->kclass->chooserPaymentterm(array('name'=>'paymenttermid','width'=>200,'hasBlank'=>1,'topname'=>'请选择','module'=>'produce'));
			$currency=$this->kclass->chooserCurrency(array('name'=>'currencyid','width'=>100,'hasBlank'=>1,'topname'=>'请选择','selectedid'=>3));
			$pinvoiceType=$this->kclass->chooserPInvoiceType(array('name'=>'pinvoiceTypeid','hasBlank'=>1,'topname'=>'无发票','selectedid'=>2));
			$taxRate=$this->kclass->chooserTaxRate(array('name'=>'taxRate','selectedid'=>17));
			$fee=$this->kclass->formFee(array('module'=>'porder'));
			//$order=$this->kclass->DB->queryFirst("SELECT porderid,orderno,purchaseType,delivery,remark FROM `porder` WHERE killed=0 AND porderid='".$this->kclass->input['porderid']."'");
			$upload=$this->kclass->upload(array('title'=>'相关附件：'));
			$j=count($this->kclass->input['itemId']);
			if($this->kclass->input['purchaseType']=='normal'){
				$itemtr='<table class="hundred"><thead></tr><th colspan="13">订单明细表</th></tr></thead><tbody><tr class="center even"><td width="15">ID</td><td width="50">申请单编号</td><td>物资</td><td width="30">版本</td><td>品牌</td><td>数量 <span class="red bold">*</span></td><td>包装方式</td><td>单价 <span class="red bold">*</span></td><td>折扣</td><td>到货日期 <span class="red bold">*</span></td><td>备注</td></tr>';
			}elseif($this->kclass->input['purchaseType']=='entrust'){
				$itemtr='<table class="hundred"><thead></tr><th colspan="12">订单明细表</th></tr></thead><tbody><tr class="center even"><td width="15">ID</td><td>申请单编号</td><td>物资</td><td width="30">版本</td><td>品牌</td><td>数量 <span class="red bold">*</span></td><td>包装方式</td><td>单价 <span class="red bold">*</span></td><td>折扣 %</td><td>到货日期 <span class="red bold">*</span></td><td>加工要求</td><td>备注</td></tr>';
			}
			$i=1;
			$counter=count($this->kclass->input['itemSelect']);
			for($m=1;$m<$j+1;$m++){
				if($this->kclass->input['itemSelect'][$m]>0){
					//if($this->kclass->input['purchaseType']=='normal'){
						$item=$this->kclass->DB->queryFirst("
							SELECT pi.itemid,pi.module,pi.mid,pi.mitemid,pi.materialid,pi.quantity,pi.unitid,pi.supplierid,pi.versionid,
								pi.price,pi.discount,requirement,pi.arrivalTime,pi.remark,
							m.materialid,m.materialno,m.entrustMaterialid,
							mv.title AS version,mv.price,mv.price1,
							p.purchaseno,
							e.entrustno,e.entrustid,
							pii.materialno AS newMaterialno,pii.title AS newTitle,pii.standard AS newStandard,
							s.title AS supplier,
							b.title AS brand,
							u.title AS unit
							FROM purchaseitem AS pi
							LEFT JOIN material AS m ON (m.materialid=pi.materialid)
							LEFT JOIN materialversion AS mv ON (mv.versionid=pi.versionid)
							LEFT JOIN purchase AS p ON (p.purchaseid=pi.purchaseid)
							LEFT JOIN entrust AS e ON (e.entrustid=pi.mid)
							LEFT JOIN preinbounditem AS pii ON (pii.itemid=pi.mitemid)
							LEFT JOIN supplier AS s ON (s.supplierid=pi.supplierid)
							LEFT JOIN brand AS b ON (b.brandid=pi.brandid)
							LEFT JOIN `unit` AS u ON (pi.unitid=u.unitid)
							WHERE pi.killed=0 AND pi.itemid='".$this->kclass->input['itemSelect'][$m]."'
							ORDER BY pi.itemid ASC
						");
					/*}elseif($this->kclass->input['purchaseType']=='entrust'){
						$item=$this->kclass->DB->queryFirst("
							SELECT ei.itemid,ei.entrustid,ei.materialid,ei.versionid,ei.quantity,ei.unitid,ei.dateline,ei.requirement,ei.ifChooser,
								m.materialno,m.title,m.standard,m.entrustMaterialid,
								mv.title AS version,
								e.entrustno ,
								u.title AS unit
							FROM `entrustitem`  AS ei
							LEFT JOIN purchase AS p ON (p.purchaseid=pi.purchaseid)
							LEFT JOIN preinbounditem AS pii ON (pii.itemid=pi.mitemid)
							LEFT JOIN entrust AS e ON (e.entrustid=ei.entrustid)
							LEFT JOIN material AS m ON (m.materialid=ei.materialid)
							LEFT JOIN `unit` AS u ON (ei.unitid=u.unitid)
							LEFT JOIN materialversion AS mv ON (mv.versionid=ei.versionid)
							WHERE ei.killed=0 AND ei.ifChooser=0 AND  ei.itemid='".$this->kclass->input['itemSelect'][$m]."'
							ORDER BY ei.itemid ASC	
						");	
					}*/
					$price='';
					if($this->kclass->input['purchaseType']=='normal'){
						$materialitem='<a href="/s.php?module=material&action=view&materialid='.$item['materialid'].'">'.$item['materialno'].'</a>　'.$item['title'].'　'.$item['standard'].'';
						$price='<input type="text" name="itemPrice['.$i.']" size="5" value="'.$item['price'].'">';
						$link='<a href="/s.php?module=purchase&action=view&purchaseid='.$item['purchaseid'].'">'.$item['purchaseno'].'</a>';
						$unit = $item['unit'];
						$unitid = $item['unitid'];
					}elseif($this->kclass->input['purchaseType']=='entrust'){
						$price='<span class="gray">加工：</span><input type="text" name="itemPrice['.$i.']" size="5" value="'.$item['price1'].'">';
						$link='<a href="/s.php?module=entrust&action=view&entrustid='.$item['entrustid'].'">'.$item['entrustno'].'</a>';
						$materialid = $item['entrustMaterialid'];
						if($materialid!=""){
							$query = $this->kclass->DB->queryFirst("SELECT m.materialno,m.standard,m.title,m.unitid,u.title as utitle
																	FROM `material` AS m
																	LEFT JOIN `unit` AS u ON (u.unitid=m.unitid)
																	WHERE materialid='{$materialid}'");
							$materialno = $query['materialno'];
							$standard = $query['standard'];	
							$materialtitle = $query['title'];
							$unit = $query['utitle'];
							$unitid = $query['unitid'];
						}				
						$materialitem='<a href="/s.php?module=material&action=view&materialid='.$materialid.'">'.$materialno.'</a>　'.$materialtitle.'　'.$standard.'';
						$inputentrusid = '<input type="hidden" name="entrusid['.$i.']" value="'.$item['mid'].'">';
						$inputmaterialid = '<input type="hidden" name="materialid['.$i.']" value="'.$materialid.'">';
						$inputversionid = '<input type="hidden" name="versionid['.$i.']" value="'.$item['versionid'].'">';
					}					
					$itemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').' small">
						<input type="hidden" name="itemId['.$i.']" value="'.$item['itemid'].'">
						'.$inputentrusid.'
						'.$inputmaterialid.'
						'.$inputversionid.'
						<input type="hidden" name="itemUnitid['.$i.']" value="'.$unitid.'">
						<td>'.$i.'</td>
						<td>'.$link.'</td>
						<td>'.$materialitem.'</td>
						<td>'.$item['version'].'</td>
						<td>'.$this->kclass->chooserBrand(array('name'=>'itemBrandid['.$i.']','hasBlank'=>1,'topname'=>'请选择','width'=>'100')).'</td>
						<td><input type="text" name="itemQuantity['.$i.']" size="5" value="'.$item['quantity'].'"> '.$unit.'</td>
						<td><input type="text" name="itemPackage['.$i.']" style="width:80px;"></td>
						<td>'.$price.'</td>
						<td><input type="text" name="itemDiscount['.$i.']" value="100" size="3" value="'.$item['discount'].'"> %</td>
						<td><input type="text" name="itemDateline['.$i.']" id="dateline'.$i.'" size="10" value="'.date('Y-m-d',$item['arrivalTime']).'"></td>
						'.$this->kclass->iif($this->kclass->input['purchaseType']=='entrust','<td><input type="text" name="itemRequirement['.$i.']" style="width:100px;"  value="'.$item['requirement'].'"></td>','').'
						<td><input type="text" name="itemRemark['.$i.']" style="width:80px;" value="'.$item['remark'].'"></td>
					</tr>';
					if($i<$counter){
						$id.='#dateline'.$i.',';
					}else{
						$id.='#dateline'.$i;
					}
					$i++;
				}
			}
			/*if($this->kclass->input['purchaseType']=='entrust'){
				$itemtr.='</tbody></table><table class="hundred"><thead></tr><th colspan="11">物资提供明细表</th></tr></thead><tbody><tr class="center"><td>需要提供的物资 <span class="red bold">*</span></td><td>版本 <span class="red bold">*</span></td><td>数量 <span class="red bold">*</span></td><td>包装方式</td><td>备注</td></tr>';
				for($i=1;$i<6;$i++){
					$itemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').' small">
						<td>'.$this->kclass->chooserMaterial(array('hName'=>'itemEntrustMaterialid['.$i.']','hId'=>'itemEntrustMaterialid'.$i,'name'=>'itemEntrustMaterial['.$i.']','id'=>'itemEntrustMaterial'.$i,'width'=>400,'line'=>$i,'showVersion'=>1)).'</td>
						<td><select name="itemVersionid['.$i.']" id="itemVersionid'.$i.'" style="width:80px;"></select></td>
						<td><input type="text" name="itemEntrustQuantity['.$i.']" size="5"></td>
						<td><input type="text" name="itemEntrustPackage['.$i.']" style="width:100px;"></td>
						<td><input type="text" name="itemEntrustRemark['.$i.']" style="width:150px;"></td>
					</tr>';
				}
			}*/
			$itemtr.='<tr class="even"><td class="small gray" colspan="13">注意事项：<br>①如果所列表格不够，那么在提交保存后再点击“修改”，来增加订单物资；<br>②订单明细的物资都从“已通过审核的采购单及物资”中选择物资；<br>③“已通过审核的采购单及物资”中 灰色的物资表示已经被选中过，不可以继续选择；<br>④“到货日期”的格式为2012-02-06，中间用半角短横杠隔开；<br></tr></tbody></table>';

$body=<<<EOF
<form action="/s.php?module=porder&action=insert" name="porder" method="post">
<input type="hidden" name="module" value="porder" />
<input type="hidden" name="action" value="insert" />
<input type="hidden" name="purchaseType" value="{$this->kclass->input['purchaseType']}" />
<input type="hidden" name="step" value="2" />
<table class="hundred">
<thead></tr><th colspan="4">基本信息</th></tr></thead>
<tbody>
<tr class="even">
<td width="100">订单编号：</td><td><input type="text" name="orderno" value="{$orderno}" class="middle bold darkred" />　　编号生成方式：<input type="radio" name="noType" value="0" >自动<input type="radio" name="noType" value="1" checked>手动</td>
<td width="100">发票类型：<span class="red bold">*</span></td><td>{$pinvoiceType}</td>
</tr>
<tr class="odd">
<td>税　　率：<span class="red bold">*</span></td><td>{$taxRate}</td>
<td>订货单位：<span class="red bold">*</span></td><td>{$customer}</td>
</tr>
<tr class="even">
<td>付款方式：<span class="red bold">*</span></td><td>{$paymentterm} 　　币种：<span class="red bold">*</span>{$currency}</td>
<td>供 货 商：<span class="red bold">*</span></td><td>{$supplier}</td>
</tr>
<tr class="odd">
<td>交货方式：</td><td>{$deliveryterm}</td>
EOF;
if($order['purchaseType']=='entrust'){
$body.='<td>给付日期：<span class="red bold">*</span></td><td><input type="text" size="12" name="paymentDate" id="paymentDate"/><span class="small gray"> 时间格式如：2012-02-05</span></td>';
}else{
$body.='<td></td><td></td>';
}
$body.=<<<EOF
</tr>
<tr class="odd">
<td>收货联系人：<span class="red bold">*</span></td><td><input type="text" name="consignee" value="张小姐" />　电话：<span class="red bold">*</span> <input type="text" name="consigneeTel" value="0755-28990261" /></td>
<td>提供备品率：<span class="red bold">*</span></td><td><input type="text" name="spare" value="1" size="4" /> %</td>
</tr>
<tr class="even">
<td>产品规格及发货补充说明：</td><td><textarea name="delivery" style="width:444px;height:111px">
1、所订产品应为乙方原厂包装。
2、请严格按甲方要求日期交货，送货单上需注明订购单号、工作单号、材料编号及供应商编号，收货地址同甲方公司地址，收货联系人：{CONSIGNEE}。 若乙方未按期交货，每延迟一天，按本订单总货款金额的5%处以罚金，罚金直接在乙方货款中扣除。
3、甲方仓库当天收料截止时间为17：00，迟于该点视作后一天收料。另物料必须是品质合格，仓库方可开具收料单。
4、乙方对甲方合同如有异议必须在两天内给予答复，逾期未回传视同默认本合同。
</textarea></td>
<td>其他条款：</td><td><textarea name="remark" style="width:444px;height:111px">
1.	付款方式：{PAYMENT}
2.	供方经检验不合格产品需在收到甲方品管部检验报告或退料通知单后3日内取回，逾期甲方不负责保管。
3.	供方须无偿提供 {SQARE}左右备品。
4.	本订单签约不得涂改，经甲、乙双方签字、盖章有效。
5.	请在收到订单两天内签字、盖章回传，否则我公司财务将拒绝办理与此订单相关的付款手续！
6.	本订单传真件有效
</textarea></td>
</tr>
<tr class="even">
<td>产品要求：</td><td><textarea name="special" style="width:444px;height:111px"></textarea></td>
<td></td><td></td>
</tr>
</table>
{$itemtr}
{$fee}
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
		$this->kclass->page['title'].='新建订单';
		$this->kclass->page['onload'].='dc.tabhover();$(\''.$id.',#paymentDate\').datepicker();dc.listhover();dc.tabhover();dc.tabs({\'id\':\'purchase\'});dc.show()';
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - <a href="/s.php?module=porder">订单列表</a> - 新建订单', 'right'=>'<a href="/s.php?module=porder&action=list">返回列表</a>','body'=>$body));
	}
	//
	function insert(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			if($this->kclass->input['pinvoiceTypeid'] <= 0){
				$e .= '<li>请选择订单中的 发票类型。</li>';
			}
			if($this->kclass->input['currencyid'] <= 0){
				$e .= '<li>请选择订单中的 币种。</li>';
			}
			if($this->kclass->input['paymenttermid'] <= 0){
				$e .= '<li>请选择订单中的 付款方式。</li>';
			}
			if($this->kclass->input['supplierid'] <= 0){
				$e .= '<li>请选择订单中的 供应商。</li>';
			}
			if($this->kclass->input['consignee']==''){
				$e .= '<li>请填写订单中的 收货联系人。</li>';
			}
			if($this->kclass->input['consigneeTel']==''){
				$e .= '<li>请填写订单中的 收货联系人电话。</li>';
			}
			if($this->kclass->input['spare']==''){
				$e .= '<li>请填写订单中的 备品率。</li>';
			}
			if($this->kclass->input['purchaseType']=='entrust'){
				$m=count($this->kclass->input['itemId']);
				$hasItem=0;
				for($n=1;$n<$m+1;$n++){
					if($this->kclass->input['itemPrice'][$n]<=0 OR $this->kclass->input['itemQuantity'][$n]<=0){
						$hasItem=1;
					}
				}
				if($hasItem==1){
					$e.='<li>需要填写至少有一条订单明细，才能建立订单。</li>';
				}
				/*$j=count($this->kclass->input['itemEntrustMaterialid']);
				$hasEntrustItem=0;
				for($i=1; $i<$j+1; $i++){
					if($this->kclass->input['itemEntrustMaterialid'][$i]>0 AND $this->kclass->input['itemEntrustQuantity'][$i]>0){
						$hasEntrustItem=1;
					}
				}
				if($hasEntrustItem==0){
					$e.='<li>需要填写完整付物资明细，才能建立订单。</li>';
				}*/
			}elseif($this->kclass->input['purchaseType']=='normal'){
				$j=count($this->kclass->input['itemId']);
				$hasItem=0;
				for($i=1;$i<$j+1;$i++){
					if(intval($this->kclass->input['itemQuantity'][$i])>0 AND floatval($this->kclass->input['itemPrice'][$i]>=0) AND $this->kclass->input['itemDateline'][$i]>0 AND $this->kclass->input['itemDiscount'][$i]<=100 AND $this->kclass->input['itemDiscount'][$i]>0){
						$hasItem=1;
					}
				}
				if($hasItem==0){
					$e .= '<li>需要填写至少有一条订单明细，才能建立订单。</li>';
				}
			}
		}
		$ifexist = $this->kclass->DB->queryFirst("SELECT COUNT(orderno) FROM `porder` WHERE orderno='".$this->kclass->input['orderno']."' AND killed = 0");
		if($ifexist['COUNT(orderno)']>0){
			$e.='该订单已存在，请勿重复提交!';
		}
		if($e){
			$this->kclass->messager(array(
				'title' => '新建订单',
				'text' => '您在新建订单的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		if($this->kclass->input['noType']==0){
			$orderno=$this->kclass->id(array('porder'=>TIMENOW));
		}elseif($this->kclass->input['noType']==1){
			$orderno=$this->kclass->input['orderno'];
		}

		if($this->kclass->input['step']==2){
			$this->kclass->input['delivery']=$this->kclass->parseCleanValue($this->kclass->input['delivery']);
			$this->kclass->input['remark']=$this->kclass->parseCleanValue($this->kclass->input['remark']);
			$this->kclass->input['special']=$this->kclass->parseCleanValue($this->kclass->input['special']);
			if($this->kclass->input['paymentDate']!=0){
				$sd1=explode('-', $this->kclass->input['paymentDate']);
				$paymentDate=mktime(0,0,0,$sd1[1],$sd1[2],$sd1[0]);
			}else{
				$paymentDate=0;
			}
			$this->kclass->DB->query("
				INSERT INTO `porder`					(`orderno`,`noType`,`type`,`routeid`,`purchaseType`,`pinvoiceTypeid`,`supplierid`,`customerid`,`currencyid`,`deliverytermid`,`paymenttermid`,`paymentDate`,`taxRate`,`special`,`consignee`,`consigneeTel`,`spare`,`delivery`,`remark`,`created`,`creator`)
				VALUES
					('".$orderno."','".$this->kclass->input['noType']."','PO',4,'".$this->kclass->input['purchaseType']."','".$this->kclass->input['pinvoiceTypeid']."','".$this->kclass->input['supplierid']."','".$this->kclass->input['customerid']."','".$this->kclass->input['currencyid']."','".$this->kclass->input['deliverytermid']."','".$this->kclass->input['paymenttermid']."','".$paymentDate."','".$this->kclass->input['taxRate']."','".$this->kclass->input['special']."','".$this->kclass->input['consignee']."','".$this->kclass->input['consigneeTel']."','".$this->kclass->input['spare']."','".$this->kclass->input['delivery']."','".$this->kclass->input['remark']."','".TIMENOW."','".$this->kclass->user['userid']."')
			");
			$porderid=$this->kclass->DB->insertID();
			$counter=count($this->kclass->input['itemId']);
			for($i=1;$i<$counter+1;$i++){
				if($this->kclass->input['itemQuantity'][$i]!='' AND $this->kclass->input['itemPrice'][$i]!='' AND $this->kclass->input['itemDateline'][$i]!=''){
					$this->kclass->input['itemQuantity'][$i]=floatval($this->kclass->input['itemQuantity'][$i]);
					$this->kclass->input['itemPrice'][$i]=floatval($this->kclass->input['itemPrice'][$i]);
					$this->kclass->input['itemDiscount'][$i]=intVal($this->kclass->input['itemDiscount'][$i]);
					if($this->kclass->input['itemDiscount'][$i]<1 OR $this->kclass->input['itemDiscount'][$i]>100){
						$this->kclass->input['itemDiscount'][$i]=100;
					}
					$price='';

					$amount=$this->kclass->input['itemQuantity'][$i]*$this->kclass->input['itemPrice'][$i];

					if($this->kclass->input['itemDiscount'][$i]<100){
						$amount=$amount*$this->kclass->input['itemDiscount'][$i]/100;
					}
					$amount=number_format($amount,2,'.','');

					if($this->kclass->input['itemDateline'][$i]!=0){
						$sd2=explode('-', $this->kclass->input['itemDateline'][$i]);
						$dateline=mktime(0,0,0,$sd2[1],$sd2[2],$sd2[0]);
					}else{
						$dateline=0;
					}
					if($this->kclass->input['purchaseType']=='normal'){
						$purchaseItem=$this->kclass->DB->queryFirst("SELECT purchaseid,materialid,versionid,unitid FROM `purchaseitem` WHERE killed=0 AND itemid='".$this->kclass->input['itemId'][$i]."'");
						$this->kclass->DB->query("
							INSERT INTO item
								(`module`,`mid`,`purchaseid`,`materialid`,`versionid`,`brandid`,`quantity`,`unitid`,`package`,`price`,`discount`,`amount`,`dateline`,`requirement`,`remark`,`created`,`creator`)
							VALUES
								('porder','".$porderid."','".$purchaseItem['purchaseid']."','".$purchaseItem['materialid']."','".$purchaseItem['versionid']."','".$this->kclass->input['itemBrandid'][$i]."','".$this->kclass->input['itemQuantity'][$i]."','".$purchaseItem['unitid']."','".$this->kclass->input['itemPackage'][$i]."','".$this->kclass->input['itemPrice'][$i]."','".$this->kclass->input['itemDiscount'][$i]."','".$amount."','".$dateline."','".$this->kclass->input['itemRequirement'][$i]."','".$this->kclass->input['itemRemark'][$i]."','".TIMENOW."','".$this->kclass->user['userid']."')
						");
						$this->kclass->DB->query("UPDATE purchaseitem SET ifchooser=1 WHERE itemid='".$this->kclass->input['itemId'][$i]."'");
					}elseif($this->kclass->input['purchaseType']=='entrust'){
						//$entrustItem=$this->kclass->DB->queryFirst("SELECT entrustid,materialid,versionid,unitid FROM `entrustitem` WHERE killed=0 AND itemid='".$this->kclass->input['itemId'][$i]."'");
						//$entrustmaterial=$this->kclass->DB->queryFirst("SELECT entrustMaterialid FROM `material` WHERE killed=0 AND materialid='".$entrustItem['materialid']."'");
						$material=$this->kclass->DB->queryFirst("SELECT * ,
							mv.versionid
							FROM `material` AS m
							LEFT JOIN `materialversion` AS mv on (m.materialid=mv.materialid)
							WHERE m.killed=0 AND m.materialid='".$this->kclass->input['materialid'][$i]."'");
						$this->kclass->DB->query("
							INSERT INTO item
								(`module`,`mid`,`purchaseid`,`materialid`,`versionid`,`brandid`,`quantity`,`unitid`,`package`,`price`,`discount`,`amount`,`dateline`,`requirement`,`remark`,`created`,`creator`)
							VALUES
								('porder','".$porderid."','0','".$this->kclass->input['materialid'][$i]."','".$material['versionid']."','".$this->kclass->input['itemBrandid'][$i]."','".$this->kclass->input['itemQuantity'][$i]."','".$material['unitid']."','".$this->kclass->input['itemPackage'][$i]."','".$this->kclass->input['itemPrice'][$i]."','".$this->kclass->input['itemDiscount'][$i]."','".$amount."','".$dateline."','".$this->kclass->input['itemRequirement'][$i]."','".$this->kclass->input['itemRemark'][$i]."','".TIMENOW."','".$this->kclass->user['userid']."')
						");
						$this->kclass->DB->query("UPDATE entrustitem SET ifchooser=1 WHERE itemid='".$this->kclass->input['itemId'][$i]."'");	
					}
				}
			}
			/*// insert item
			if($this->kclass->input['purchaseType']=='entrust'){
				$count=count($this->kclass->input['itemEntrustMaterialid']);
				for($n=1;$n<$count+1;$n++){
					$this->kclass->input['itemEntrustQuantity'][$n]=intVal($this->kclass->input['itemEntrustQuantity'][$n]);
					if($this->kclass->input['itemEntrustMaterialid'][$n]>0 AND $this->kclass->input['itemEntrustQuantity'][$n]>0){
						$material=$this->kclass->DB->queryFirst("SELECT materialid,unitid FROM material WHERE materialid='".$this->kclass->input['itemEntrustMaterialid'][$n]."'");
						$this->kclass->DB->query("
							INSERT INTO item (module,mid,materialid,versionid,quantity,unitid,package,remark,created,creator)
							VALUES ('entrust','".$porderid ."','".$this->kclass->input['itemEntrustMaterialid'][$n]."','".$this->kclass->input['itemVersionid'][$n]."','".$this->kclass->input['itemQuantity'][$n]."','".$material['unitid']."','".$this->kclass->input['itemEntrustPackage'][$n]."','".$this->kclass->input['itemEntrustRemark'][$n]."','".TIMENOW."','".$this->kclass->user['userid']."')
						");
					}
				}
			}*/
			$this->kclass->updateFee(array('module'=>'porder','mid'=>$porderid));
			$this->kclass->updateAttachs(array('module'=>'porder', 'mid'=>$porderid));
			$this->kclass->messager(array(
				'title' => '新建订单',
				'text' => '订单 <b>'.$orderno.'</b> 已新建成功!返回订单详情页',
				'url' => '/s.php?module=porder&action=view&porderid='.$porderid,
				'sec' => 2
			));
		}
	}

	//
	function update(){
		$order=$this->kclass->DB->queryFirst("
			SELECT o.porderid,o.customerid,o.supplierid,o.currencyid,o.purchaseType,o.loading,o.discharge,
					o.shipmethodid,o.paymenttermid,o.deliverytermid,o.orderno,o.type,o.taxRate,o.pinvoiceTypeid,o.ifVerify,o.ifApprove,
					o.customerNo,o.supplierNo,o.shipmentDate,o.paymentDate,o.delivery,o.consignee,o.consigneeTel,o.spare,o.remark,o.created,
				od.routeid,od.title AS route,od.caption,
				s.title AS supplier,s.supplierno,
				u.username AS creator,
				us.username AS modifier
			FROM `porder` AS o
			LEFT JOIN `orderroute` AS od ON (od.routeid=o.routeid)
			LEFT JOIN `supplier` AS s ON (s.supplierid=o.supplierid)
			LEFT JOIN `user` AS u ON (u.userid=o.creator)
			LEFT JOIN `user` AS us ON (us.userid=o.modifier)
			WHERE o.killed=0 AND o.porderid='".$this->kclass->input['porderid']."'
			LIMIT 0,1
		");
		if(!$order){
			$e='<li>系统数据出错，很抱歉！</li>';
		}
		if($e){
			$this->kclass->messager(array(
				'title' => '修改订单',
				'text' => '您在修改订单的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		/*if($this->kclass->input['step']=='item'){

			$body=<<<EOF
<form action="/s.php?module=porder&action=doupdate" name="porder" method="post">
<input type="hidden" name="module" value="porder">
<input type="hidden" name="action" value="doupdate">
<input type="hidden" name="step" value="item">
<input type="hidden" name="purchaseType" value="{$order['purchaseType']}">
<input type="hidden" name="porderid" value="{$order['porderid']}">
<input type="hidden" name="supplierid" value="{$order['supplierid']}">
{$itemtr}
<table class="tlist tsuite" align="center" width="100%">
<tbody>
<tr class="odd" nohover>
<td align="center" colspan=4><input type="submit" id="submitButton" value="  提交  " accesskey="s"><input type="reset" value="  复位  "/></td>
</tr>
</tbody>
</table>
</form>
EOF;

		}else{*/
			$shipmentDate=date('Y-m-d',$order['shipmentDate']);
			$paymentDate=date('Y-m-d',$order['paymentDate']);
			foreach($this->purchaseType AS $key=>$val){
				if($order['purchaseType']==$val['entitle']){
					$purchaseType=$val['title'];
				}
			}
			if($order['modified']!=0)$modify='，由'.$order['modifier'].'于'.$order['modified'].'修改';
			$this->kclass->br2nl=true;
			$order['delivery']=$this->kclass->parseConvertValue($order['delivery']);
			$order['packing']=$this->kclass->parseConvertValue($order['packing']);
			$order['special']=$this->kclass->parseConvertValue($order['special']);
			$order['remark']=$this->kclass->parseConvertValue($order['remark']);
			$supplier=$this->kclass->chooserSupplier(array('hName'=>'supplierid','name'=>'supplier','width'=>400,'selectedid'=>$order['supplierid'],'value'=>$order['supplier'].' ('.$order['supplierno'].')'));
			$selectCustomer=$this->kclass->DB->queryFirst("SELECT cntitle FROM customer WHERE killed=0 AND customerid=2 LIMIT 0,1");
		$customer=$this->kclass->chooserCustomer(array('hName'=>'customerid','name'=>'customer','width'=>450,'selectedid'=>2,'value'=>$selectCustomer['cntitle']));
			$deliveryterm=$this->kclass->chooserDeliveryterm(array('name'=>'deliverytermid','width'=>200,'hasBlank'=>1,'topname'=>'请选择','selectedid'=>$order['deliverytermid']));
			$paymentterm=$this->kclass->chooserPaymentterm(array('name'=>'paymenttermid','width'=>200,'hasBlank'=>1,'topname'=>'请选择','module'=>'produce','selectedid'=>$order['paymenttermid']));
			$currency=$this->kclass->chooserCurrency(array('name'=>'currencyid','width'=>200,'hasBlank'=>1,'topname'=>'请选择','selectedid'=>$order['currencyid']));
			$route=$this->kclass->chooserOrderRoute(array('name'=>'routeid','width'=>200,'hasBlank'=>1,'topname'=>'请选择','selectedid'=>$order['routeid']));
			$invoiceType=$this->kclass->chooserPInvoiceType(array('name'=>'pinvoiceTypeid','hasBlank'=>1,'topname'=>'请选择','selectedid'=>$order['pinvoiceTypeid']));
			$taxRate=$this->kclass->chooserTaxRate(array('name'=>'taxRate','selectedid'=>$order['taxRate']));
			$upload=$this->kclass->upload(array('title'=>'相关附件：','module'=>'porder','mid'=>$order['porderid']));
			$fee=$this->kclass->formFee(array('module'=>'porder','mid'=>$order['porderid']));
			$consignee=$this->kclass->iif($order['consignee'],$order['consignee'],'张小姐');
			$consigneeTel=$this->kclass->iif($order['consigneeTel'],$order['consigneeTel'],'0755-28990261');
			$spare=$this->kclass->iif($order['spare'],$order['spare'],'1.0');
			$consigen=',o.consignee,o.consigneeTel,o.spare';
			if($order['ifVerify']!=0 OR $order['ifApprove']!=0){
				$updateReason='<tr class="odd"><td class="red bold">修改原因：</td><td><textarea name="reviseRemark" style="width:444px;height:100px"></textarea></td><td></td><td></td></tr>';
			}
			// item
			if($order['purchaseType']=='normal'){
				$module='porder';
				$itemtr='<table class="hundred small"><thead><tr><th colspan="13">订单明细列表</th></tr></thead><tbody><tr class="center"><td width="15">ID</td><td width="60">采购计划单</td><td width="60">物资编号</td><td>名称</td><td>规格</td><td width="85">品牌</td><td width="80">数量 <span class="red bold">*</span></td><td width="75">包装方式</td><td width="50">单价 <span class="red bold">*</span></td><td width="35">折扣</td><td width="80">到货时间 <span class="red bold">*</span></td><td width="50">备注</td><td width="30">删</td></tr>';
				$items=$this->kclass->DB->query("
				SELECT i.itemid,i.brandid,i.quantity,i.purchaseid,i.materialid,i.unitid,i.package,i.price,i.discount,i.dateline,i.requirement,i.remark,
					pu.purchaseno,
					b.brandid,b.title AS brand,
					m.materialno,m.title AS material,m.standard,
					u.title AS unit
				FROM item AS i
				LEFT JOIN purchase AS pu ON (pu.purchaseid=i.purchaseid)
				LEFT JOIN `brand` AS b ON (b.brandid=i.brandid)
				LEFT JOIN `material` AS m ON (m.materialid=i.materialid)
				LEFT JOIN `unit` AS u ON (u.unitid=i.unitid)
				WHERE i.killed=0 AND i.module='porder' AND i.mid='".$order['porderid']."'
				ORDER BY itemid ASC
			");
			}elseif($order['purchaseType']=='entrust'){
				$module='entrust';
				$itemtr.='<table class="hundred small"><thead><tr><th colspan="13">给付物资明细</th></tr></thead><tbody><tr class="center"><td width="15">ID</td><td width="60">采购计划单</td><td width="60">物资编号</td><td>名称</td><td>规格</td><td width="80">数量/单位 <span class="red bold">*</span></td><td width="75">包装方式</td><td width="50">单价</td><td width="35">折扣</td><td width="80">到货时间 <span class="red bold">*</span></td><td>加工要求</td><td>备注</td><td width="30">删</td></tr>';
				$items=$this->kclass->DB->query("
				SELECT i.itemid,i.brandid,i.quantity,i.entrustid,i.materialid,i.unitid,i.package,i.price,i.discount,i.dateline,i.requirement,i.remark,
					e.entrustid,e.entrustno,
					b.brandid,b.title AS brand,
					m.materialno,m.title AS material,m.standard,
					u.title AS unit
				FROM item AS i
				LEFT JOIN `entrust` AS e ON (e.entrustid=i.entrustid)
				LEFT JOIN `brand` AS b ON (b.brandid=i.brandid)
				LEFT JOIN `material` AS m ON (m.materialid=i.materialid)
				LEFT JOIN `unit` AS u ON (u.unitid=i.unitid)
				WHERE i.killed=0 AND i.module='porder' AND i.mid='".$order['porderid']."'
				ORDER BY itemid ASC
			");
			}
			
			$k=1;
			if($this->kclass->DB->numRows()){
				$this->kclass->tbline+=1;
				while($item=$this->kclass->DB->fetchArray($items)){
					if($order['purchaseType']=='entrust'){
						$link='<a href="/s.php?module=entrust&action=view&entrustid='.$item['entrustid'].'">'.$item['entrustno'].'</a>';
					}else{
						$link='<a href="/s.php?module=purchase&action=view&purchaseid='.$item['purchaseid'].'">'.$item['purchaseno'].'</a>';
					}
					$purchaseitem=$this->kclass->DB->queryFirst("SELECT itemid FROM purchaseitem WHERE killed=0 AND purchaseid='".$item['purchaseid']."' AND materialid='".$item['materialid']."'");
					$itemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').' small"><input type="hidden" name="olitemId['.$k.']" value="'.$item['itemid'].'">
						<td>'.$k.'</td>
						<td>'.$link.'</a></td>
						<td><a href="/s.php?module=material&action=view&materialid='.$item['materialid'].'">'.$item['materialno'].'</a></td>
						<td>'.$item['material'].'</td>
						<td>'.$item['standard'].'</td>
						'.$this->kclass->iif($order['purchaseType']=='normal','<td>'.$this->kclass->chooserBrand(array('name'=>'olBrandid['.$k.']','hasBlank'=>1,'topname'=>'请选择','selectedid'=>$item['brandid'])).'</td>','').'
						<td><input type="hidden" name="olUnitid['.$k.']" value="'.$item['unitid'].'"><input type="text" name="olQuantity['.$k.']" size="6" value="'.$item['quantity'].'" /> '.$item['unit'].'</td>
						<td><input type="text" name="olPackage['.$k.']" value="'.$item['package'].'"  style="width:70px;" /></td>
						<td><input type="text" name="olPrice['.$k.']" size="5" value="'.$item['price'].'" /></td>
						<td><input type="text" name="olDiscount['.$k.']" value="'.$item['discount'].'" size="3"></td>
						<td><input type="text" name="olDateline['.$k.']" id="olDateline'.$k.'" value="'.date('Y-m-d',$item['dateline']).'" size="10"></td>
						'.$this->kclass->iif($order['purchaseType']=='entrust','<td><input type="text"  name="olRequirement['.$k.']" value="'.$item['requirement'].'" style="width:80px;"/></td>','').'
						<td><input type="text" name="olRemark['.$k.']" style="width:80px;" value="'.$item['remark'].'"></td>
						<td><input type="checkbox" name="olKill['.$k.']" value="'.$item['itemid'].'"/></td>
					</tr>';
					$id.='#olDateline'.$k.',';
					$k+=1;
				}
				$itemtr.='<tr><td colspan="13" class="bold">新建明细</td></tr>';
			}
			if($order['purchaseType']=='normal'){
				$orgitems=$this->kclass->DB->query("
					SELECT pi.itemid,pi.module,pi.mid,pi.mitemid,pi.materialid,pi.quantity,pi.unitid,pi.supplierid,pi.price,pi.discount,pi.arrivalTime,pi.remark,
						m.materialno,m.title,m.standard,
						pii.materialno AS newMaterialno,pii.title AS newTitle,pii.standard AS newStandard,
						p.purchaseid,p.purchaseno,
						u.title AS unit
					FROM `purchaseitem` AS pi
					LEFT JOIN `material` AS m ON (m.materialid=pi.materialid)
					LEFT JOIN `preinbounditem` AS pii ON (pii.itemid=pi.mitemid)
					LEFT JOIN `purchase` AS p ON (p.purchaseid=pi.purchaseid)
					LEFT JOIN `unit` AS u ON (pi.unitid=u.unitid)
					WHERE pi.killed=0 AND pi.module='purchaseApply' AND pi.ifChooser=0 AND pi.materialid<>0
					ORDER BY pi.itemid ASC
				");
				$itemtr.='<tbody><tr class="center"><td width="15">ID</td><td>采购计划单</td><td>物资编号</td><td>名称</td><td width="200">规格</td><td>品牌</td><td align="right">数量 <span class="red bold">*</span></td><td>包装方式</td><td>单价 <span class="red bold">*</span></td><td>折扣</td><td>到货时间 <span class="red bold">*</span></td><td>备注</td><td>选</td></tr>';
			}elseif($order['purchaseType']=='entrust'){
				$orgitems=$this->kclass->DB->query("
					SELECT pi.itemid,pi.module,pi.mid,pi.mitemid,pi.materialid,pi.quantity,pi.unitid,pi.supplierid,pi.price,pi.discount,
							pi.requirement,pi.arrivalTime,pi.remark,
						m.materialno,m.title,m.standard,
						p.purchaseid,p.purchaseno,
						u.title AS unit
					FROM `purchaseitem` AS pi
					LEFT JOIN `material` AS m ON (m.materialid=pi.materialid)
					LEFT JOIN `purchase` AS p ON (p.purchaseid=pi.purchaseid)
					LEFT JOIN `unit` AS u ON (pi.unitid=u.unitid)
					WHERE pi.killed=0 AND pi.module='entrust' AND pi.ifChooser=0 AND pi.materialid<>0
					ORDER BY pi.itemid ASC
				");
				$itemtr.='<tbody><tr class="center"><td width="15">ID</td><td>采购计划单</td><td>物资编号</td><td>名称</td><td>规格</td><td align="right">数量/单位 <span class="red bold">*</span></td><td>包装方式</td><td width="30">单价<span class="red bold">*</span></td><td>折扣</td><td>到货时间 <span class="red bold">*</span></td><td>加工要求</td><td>备注</td><td>选</td></tr>';
			}
			if($this->kclass->DB->numRows()){
				$i=1;
				while($orgitem=$this->kclass->DB->fetchArray($orgitems)){
					$materialno=$this->kclass->iif($orgitem['materialid']==0,$orgitem['newMaterialno'],'<a href="/s.php?module=material&action=view&materialid='.$orgitem['materialid'].'">'.$orgitem['materialno'].'</a>');
					$title=$this->kclass->iif($orgitem['materialid']==0,$orgitem['newTitle'],$orgitem['title']);
					$standard=$this->kclass->iif($orgitem['materialid']==0,$orgitem['newStandard'],$orgitem['standard']);
					$itemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').' small"><input type="hidden" name="itemId['.$i.']" value="'.$orgitem['itemid'].'">
						<td>'.$i.'</td>
						<td><a href="/s.php?module=purchase&action=view&purchaseid='.$orgitem['purchaseid'].'">'.$orgitem['purchaseno'].'</a></td>
						<td>'.$materialno.'</td>
						<td>'.$title.'</td>
						<td>'.$standard.'</td>
						'.$this->kclass->iif($order['purchaseType']=='normal','<td>'.$this->kclass->chooserBrand(array('name'=>'itemBrandid['.$i.']','hasBlank'=>1,'topname'=>'请选择','width'=>'80')).'</td>','').'
						<td><input type="hidden" name="itemUnitid['.$i.']" value="'.$orgitem['unitid'].'"><input type="text" name="itemQuantity['.$i.']" size="6" value="'.$orgitem['quantity'].'"> '.$orgitem['unit'].'</td>
						<td><input type="text" name="itemPackage['.$i.']" style="width:70px;"></td>
						<td><input type="text" name="itemPrice['.$i.']" size="5" value="'.$orgitem['price'].'"></td>
						<td><input type="text" name="itemDiscount['.$i.']" size="3" value="'.$orgitem['discount'].'"></td>
						<td><input type="text" name="itemDateline['.$i.']" id="dateline'.$i.'" size="10" value="'.date('Y-m-d',$orgitem['arrivalTime']).'"></td>
						'.$this->kclass->iif($order['purchaseType']=='entrust','<td><input type="text" name="itemRequirement['.$i.']" value="'.$item['requirement'].'" style="width:80px;"/></td>','').'
						<td><input type="text" name="itemRemark['.$i.']" style="width:80px;" value="'.$orgitem['remark'].'"></td>
						<td><input type="checkbox" name="itemSelect['.$i.']" value="'.$orgitem['itemid'].'"></td>
					</tr>';
					$id.='#dateline'.$i.',';
					$i++;
				}
			}
			if($order['purchaseType']=='entrust'){
				$orderItems=$this->kclass->DB->query("
					SELECT i.*,
						u.unitid,u.title AS unit
					FROM `item` AS i
					LEFT JOIN `unit` AS u ON (u.unitid=i.unitid)
					WHERE i.killed=0 AND i.module='porder' AND i.mid='".$this->kclass->input['porderid']."'
					ORDER BY itemid ASC
				");
				if($this->kclass->DB->numRows()){
					$itemtr.='<table class="hundred"><thead></tr><th colspan="13">返还物资明细</th></tr></thead><tbody><tr class="center"><td width="30">ID</td><td>需要返还的物资 <span class="red bold">*</span></td><td>供应商产品编号</td><td>供应商使用的名称</td><td>数量 <span class="red bold">*</span></td><td>包装方式</td><td>到货日期 <span class="red bold">*</span></td><td>备注</td><td>删</td></tr>';
					$k=1;
					while($orderItem=$this->kclass->DB->fetchArray($orderItems)){
						$itemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').' small">
							<input type="hidden" name="orderItemId['.$k.']" value="'.$orderItem['itemid'].'">
							<td width="30">'.$k.'</td>
							<td>'.$this->kclass->chooserMaterial(array('hName'=>'itemMaterialid['.$k.']','hId'=>'itemMaterialid'.$k,'name'=>'itemMaterial['.$k.']','id'=>'itemMaterial'.$k,'width'=>400,'line'=>$k,'selectedid'=>$item['materialid'],'value'=>$material)).'</td>
							<td><input type="text" name="orderItemArticleNo['.$k.']" size="12" value="'.$orderItem['articleNo'].'"></td>
							<td><input type="text" name="orderItemTitle['.$k.']" size="25" value="'.$orderItem['title'].'"></td>
							<td><input type="hidden" name="orderItemUnitid" value="'.$orderItem['unitid'].'"><input type="text" name="orderItemQuantity['.$k.']" size="5"  value="'.$orderItem['quantity'].'"> '.$orderItem['unit'].'</td>
							<td><input type="text" name="orderItemPackage['.$k.']" style="width:100px;" value="'.$orderItem['package'].'"></td>
							<td><input type="text" name="orderItemDateline['.$k.']" id="orderItemDateline'.$k.'" size="10" value="'.date("Y-m-d",$orderItem['dateline']).'"></td>
							<td><input type="text" name="orderItemRemark['.$k.']" style="width:80px;" value="'.$orderItem['remark'].'">
							<td><input type="checkbox" name="orderItemKill['.$k.']" value="'.$orderItem['itemid'].'"></td></tr>';
						$id.='#orderItemDateline'.$k.',';
						$k++;
					}
					$itemtr.='<tr><td colspan=13>新增明细</td></tr>';
				}
				for($i=$k;$i<$k+5;$i++){
					$itemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').' small">
						<td width="30">'.$i.'</td>
						<td>'.$this->kclass->chooserMaterial(array('hName'=>'itemMaterialid['.$i.']','hId'=>'itemMaterialid'.$i,'name'=>'itemMaterial['.$i.']','id'=>'itemMaterial'.$i,'width'=>400,'line'=>$i)).'</td>
						<td><input type="text" name="orderItemArticleNo['.$i.']" size="12" value="'.$orderItem['articleNo'].'"></td>
						<td><input type="text" name="orderItemTitle['.$i.']" size="25" value="'.$orderItem['title'].'"></td>
						<td><input type="hidden" name="orderItemUnitid" value="'.$orderItem['unitid'].'"><input type="text" name="orderItemQuantity['.$i.']" size="5"  value="'.$orderItem['quantity'].'"> '.$orderItem['unit'].'</td>
						<td><input type="text" name="orderItemPackage['.$i.']" style="width:100px;" value="'.$orderItem['package'].'"></td>
						<td><input type="text" name="orderItemDateline['.$i.']" id="orderItemDateline'.$i.'" size="10" ></td>
						<td><input type="text" name="orderItemRemark['.$i.']" style="width:80px;" value="'.$orderItem['remark'].'">
						<td></td></tr>';
					if($i<$k+4){
						$id.='#orderItemDateline'.$i.',';
					}else{
						$id.='#orderItemDateline'.$i;
					}
				}
			}
			$itemtr.='<tr class="even"><td class="small gray" colspan="15">注意事项：<br>“到货日期”的格式为2012-02-06，中间用半角短横杠隔开；<br></tr></tbody></table>';
			$body=<<<EOF
<form action="/s.php?module=porder&action=doupdate" name="porder" method="post">
<input type="hidden" name="module" value="porder" />
<input type="hidden" name="action" value="doupdate" />
<input type="hidden" name="porderid" value="{$order['porderid']}" />
<input type="hidden" name="purchaseType" value="{$order['purchaseType']}" />
<table class="hundred">
<thead></tr><th colspan="4">基本信息</th></tr></thead>
<tbody>
<tr class="odd">
<td width="100">订单编号：<span class="red bold">*</span></td><td width="650"><span class="title">{$order['orderno']}</span> <span class="gray small">订单编号不可更改</span>　　币种：<span class="red bold">*</span>{$currency}</td>
<td width="100">发票类型：<span class="red bold">*</span></td><td>{$invoiceType}　　</td>
</tr>
<tr class="odd">
<td>税　　率<span class="red bold">*</span></td><td>{$taxRate}</td>
<td>订货单位：<span class="red bold">*</span></td><td>{$customer}</td>
</tr>
<tr class="even">
<td>付款方式：<span class="red bold">*</span></td><td>{$paymentterm}</td>
<td>供 货 商：<span class="red bold">*</span></td><td>{$supplier}</td>
</tr>
<tr class="odd">
<td>交货方式：</td><td>{$deliveryterm}</td>
EOF;
if($this->kclass->input['purchaseType']=='entrust'){
$body.='<td>给付日期：<span class="red bold">*</span></td><td><input type="text" size="12" name="paymentDate" id="paymentDate"/><span class="small gray"> 时间格式如：2012-02-05</span></td>';
}else{
$body.='<td></td><td></td>';
}
$body.=<<<EOF
</tr>
<tr class="odd">
<td>收货联系人：<span class="red bold">*</span></td><td><input type="text" name="consignee" value="{$consignee}" />　电话：<span class="red bold">*</span> <input type="text" name="consigneeTel" value="{$consigneeTel}" /></td>
<td>提供备品率：<span class="red bold">*</span></td><td><input type="text" name="spare" value="{$spare}" size="4" /> %</td>
</tr>
<tr class="even">
<td>产品规格及发货补充说明：</td><td><textarea name="delivery" style="width:444px;height:111px">{$order['delivery']}</textarea></td>
<td>其他条款：</td><td><textarea name="remark" style="width:444px;height:111px">{$order['remark']}</textarea></td>
</tr>
<tr class="even">
<td>产品要求：</td><td><textarea name="special" style="width:444px;height:111px">{$order['special']}</textarea></td>
<td></td><td></td>
</tr>
{$updateReason}
</table>
{$itemtr}
{$fee}
{$upload}
<table class="tlist tsuite" align="center" width="100%">
<tbody>
<tr class="odd" nohover>
<td colspan="4" align="center">
	<input type="submit" id="submitButton" value="  保存  " accesskey="s">
	<input type="reset" value="  复位  ">
</td>
</tr>
</tbody>
</table>
</form>
EOF;

		$this->kclass->page['title'] .= $order['orderno'].' - 修改订单';
		$this->kclass->page['onload'].='dc.tabhover();$(\''.$id.',#paymentDate\').datepicker()';
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - <a href="/s.php?module=porder&action=view&porderid='.$order['porderid'].'">查看订单</a> - 修改订单', 'right'=>'<a href="/s.php?module=porder&action=view&porderid='.$order['porderid'].'">查看订单</a>　｜　<a href="/s.php?module=porder&action=list">返回列表</a>　','body'=>$body));
	}

	//
	function doupdate(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			if($this->kclass->input['step']=='item'){

			}else{
				if($this->kclass->input['currencyid'] <= 0){
					$e .= '<li>请选择订单中的 币种。</li>';
				}
				if($this->kclass->input['paymenttermid'] <= 0){
					$e .= '<li>请选择订单中的 付款方式。</li>';
				}
				if($this->kclass->input['supplierid'] <= 0){
					$e .= '<li>请选择订单中的 供应商。</li>';
				}
			}
		}
		if($e){
			$this->kclass->messager(array(
				'title' => '修改订单',
				'text' => '您在修改订单的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$order=$this->kclass->DB->queryFirst("
			SELECT  o.porderid,o.supplierid,o.customerNo,o.supplierNo,o.orderno,o.type,o.purchaseType,o.paymentDate,o.delivery,
					o.taxRate,o.consignee,o.consigneeTel,o.spare,o.ifVerify,o.verified,o.verifyRemark,o.remark,o.modified,o.created,o.killed,
				od.routeid,od.title AS route,od.caption,
				it.title AS invoiceType,
				s.title AS supplier,s.supplierno,s.address AS supplierAddress,s.linkman AS supplierLinkman,s.mobile AS supplierMobile,
					s.telephone AS supplierTelephone,s.email AS supplierEmail,s.fax AS supplierFax,
				cu.title AS customer,cu.customerno,cu.address AS customerAddress,cu.linkman AS customerLinkman,cu.mobile AS customerMobile,
					cu.telephone AS customerTelephone,cu.email AS customerEmail,cu.fax AS customerFax,
				c.title AS currency,c.symbol AS encurrency,
				p.title AS paymentterm,p.entitle AS enpaymentterm,
				d.title AS deliveryterm,d.entitle AS endeliveryterm,
				u.username AS creator,
				us.username AS modifier,
				ur.username AS verifier
			FROM `porder` AS o
			LEFT JOIN `orderroute` AS od ON (od.routeid=o.routeid)
			LEFT JOIN `pinvoicetype` AS it ON (it.pinvoiceTypeid=o.pinvoiceTypeid)
			LEFT JOIN `supplier` AS s ON (s.supplierid=o.supplierid)
			LEFT JOIN `customer` AS cu ON (cu.customerid=o.customerid)
			LEFT JOIN `currency` AS c ON (c.currencyid=o.currencyid)
			LEFT JOIN `paymentterm` AS p ON (p.paymenttermid=o.paymenttermid)
			LEFT JOIN `deliveryterm` AS d ON (d.deliverytermid=o.deliverytermid)
			LEFT JOIN `user` AS u ON (u.userid=o.creator)
			LEFT JOIN `user` AS us ON (us.userid=o.modifier)
			LEFT JOIN `user` AS ur ON (ur.userid=o.verifier)
			WHERE o.routeid=4 AND o.type='PO' AND o.porderid='".$this->kclass->input['porderid']."'
			LIMIT 0,1
		");

		//获取历史版本信息
		$orderHtml = '';
		if($order['ifVerify']!=0 OR $order['ifApprove']!=0){
			$revise = $this->_view($this->kclass->input['porderid']);
			$this->kclass->insertReviseInfo(array('module'=>'porder', 'mid'=>$order['porderid'],'revise'=> $revise,'remark'=>$this->kclass->input['reviseRemark']));
			$this->kclass->DB->query("UPDATE `porder` SET ifVerify=0,verified=0,verifier=0,verifyRemark='',ifApprove=0,approved=0,approver=0,approvalRemark='' WHERE porderid='".$order['porderid']."'");
		}

		if($this->kclass->input['shipmentDate']!=''){
			$sd=explode('-', $this->kclass->input['shipmentDate']);
			$shipmentDate=mktime(0,0,0,$sd[1],$sd[2],$sd[0]);
		}else{
			$shipmentDate=0;
		}
		if($this->kclass->input['paymentDate']!=''){
			$sd=explode('-', $this->kclass->input['paymentDate']);
			$paymentDate=mktime(0,0,0,$sd[1],$sd[2],$sd[0]);
		}else{
			$paymentDate=0;
		}
		$this->kclass->input['delivery']=$this->kclass->parseCleanValue($this->kclass->input['delivery']);
		$this->kclass->input['remark']=$this->kclass->parseCleanValue($this->kclass->input['remark']);
		$this->kclass->updateFee(array('module'=>'porder','mid'=>$order['porderid']));
		// insert item
		$olCounter=count($this->kclass->input['olitemId']);
		for($m=1;$m<$olCounter+1;$m++){
			if($this->kclass->input['olQuantity'][$m]>0 AND $this->kclass->input['olPrice'][$m]>=0 AND $this->kclass->input['olDateline'][$m]!=''){
				$this->kclass->input['olQuantity'][$m]=floatVal($this->kclass->input['olQuantity'][$m]);
				$this->kclass->input['olPrice'][$m]=floatVal($this->kclass->input['olPrice'][$m]);
				$this->kclass->input['olDiscount'][$m]=intVal($this->kclass->input['olDiscount'][$m]);
				if($this->kclass->input['olDiscount'][$m]<1 OR $this->kclass->input['olDiscount'][$m]>100){
					$this->kclass->input['olDiscount'][$m]=100;
				}
				$amount=$this->kclass->input['olQuantity'][$m] * $this->kclass->input['olPrice'][$m];
				if($this->kclass->input['olDiscount'][$m]<100){
					$amount=$amount * $this->kclass->input['olDiscount'][$m]/100;
				}
				$amount=number_format($amount,2,'.','');
				if($this->kclass->input['olDateline'][$m]!=0){
					$sd2=explode('-', $this->kclass->input['olDateline'][$m]);
					$dateline=mktime(0,0,0,$sd2[1],$sd2[2],$sd2[0]);
				}else{
					$dateline=0;
				}
				$item=$this->kclass->DB->queryFirst("SELECT materialid,purchaseid FROM item WHERE killed=0 AND itemid='".$this->kclass->input['olitemId'][$m]."'");
				$olPurchaseitem=$this->kclass->DB->queryFirst("SELECT itemid FROM purchaseitem WHERE killed=0 AND purchaseid='".$item['purchaseid']."' AND materialid='".$item['materialid']."'");
				if($this->kclass->input['olKill'][$m]>0 AND $this->kclass->input['olKill'][$m]==$this->kclass->input['olitemId'][$m]){
					$this->kclass->DB->query("UPDATE `item` SET killed='".TIMENOW."',killer='".$this->kclass->user['userid']."' WHERE itemid='".$this->kclass->input['olitemId'][$m]."'");
					$this->kclass->DB->query("UPDATE purchaseitem SET ifChooser=0 WHERE itemid='".$olPurchaseitem['itemid']."'");
				}else{
					$this->kclass->DB->query("
						UPDATE item SET
							`quantity`='".$this->kclass->input['olQuantity'][$m]."',
							`brandid`='".$this->kclass->iif($this->kclass->input['olBrandid'][$m],$this->kclass->input['olBrandid'][$m],0)."',
							`package`='".$this->kclass->input['olPackage'][$m]."',
							`price`='".$this->kclass->input['olPrice'][$m]."',
							`discount`='".$this->kclass->input['olDiscount'][$m]."',
							`amount`='".$amount."',
							`dateline`='".$dateline."',
							`requirement`='".$this->kclass->input['olRequirement'][$m]."',
							`remark`='".$this->kclass->input['olRemark'][$m]."',
							`modified` = '".TIMENOW."',
							`modifier` = '".$this->kclass->user['userid']."'
						WHERE itemid='".$this->kclass->input['olitemId'][$m]."'
					");
				}
				$quantity+=$this->kclass->input['olQuantity'][$m];
			}
		}
		$orgCounter=count($this->kclass->input['itemId']);
		for($n=1;$n<$orgCounter+1;$n++){
			if($this->kclass->input['itemSelect'][$n]>0 AND $this->kclass->input['itemQuantity'][$n]>0 AND $this->kclass->input['itemPrice'][$n]>=0 AND $this->kclass->input['itemDateline'][$n]!=''){
				$this->kclass->input['itemQuantity'][$n]=floatval($this->kclass->input['itemQuantity'][$n]);
				$this->kclass->input['itemPrice'][$n]=floatval($this->kclass->input['itemPrice'][$n]);
				$this->kclass->input['itemDiscount'][$n]=intVal($this->kclass->input['itemDiscount'][$n]);
				if($this->kclass->input['itemDiscount'][$n]<1 OR $this->kclass->input['itemDiscount'][$n]>100){
					$this->kclass->input['itemDiscount'][$n]=100;
				}
				$amount=$this->kclass->input['itemQuantity'][$n] * $this->kclass->input['itemPrice'][$n];
				if($this->kclass->input['itemDiscount'][$n]<100){
					$amount=$amount * $this->kclass->input['itemDiscount'][$n]/100;
				}
				$amount=number_format($amount,2,'.','');
				if($this->kclass->input['itemDateline'][$n]!=0){
					$sd2=explode('-', $this->kclass->input['itemDateline'][$n]);
					$dateline=mktime(0,0,0,$sd2[1],$sd2[2],$sd2[0]);
				}else{
					$dateline=0;
				}
				$purchaseItem=$this->kclass->DB->queryFirst("
					SELECT p.itemid,p.materialid,m.materialno,m.title,m.standard,p.purchaseid,
						m.materialno,m.title,m.standard
					FROM purchaseitem AS p
					LEFT JOIN material AS m ON (m.materialid=p.materialid)
					WHERE p.itemid='".$this->kclass->input['itemId'][$n]."'
					LIMIT 0,1
				");
				if($this->kclass->input['purchaseType']=='normal'){
					$module='porder';
				}elseif($this->kclass->input['purchaseType']=='entrust'){
					$module='entrust';
				}

				$this->kclass->DB->query("
					INSERT INTO item (`module`,`mid`,`materialid`,`purchaseid`,`quantity`,`brandid`,`unitid`,`package`,`price`,`discount`,`amount`,`dateline`,`remark`,`requirement`,`created`,`creator`)
					VALUES ('".$module."','".$order['porderid']."','".$purchaseItem['materialid']."','".$purchaseItem['purchaseid']."','".$this->kclass->input['itemQuantity'][$n]."','".$this->kclass->iif($this->kclass->input['itemBrandid'][$m],$this->kclass->input['itemBrandid'][$m],0)."','".$this->kclass->input['itemUnitid'][$n]."','".$this->kclass->input['itemPackage'][$n]."','".$this->kclass->input['itemPrice'][$n]."','".$this->kclass->input['itemDiscount'][$n]."','".$amount."','".$dateline."','".$this->kclass->input['itemRemark'][$n]."','".$this->kclass->input['itemRequirement'][$n]."','".TIMENOW."','".$this->kclass->user['userid']."')
				");
				$this->kclass->DB->query("UPDATE purchaseitem SET ifChooser=1 WHERE itemid='".$purchaseItem['itemid']."'");
				$quantity+=$this->kclass->input['itemQuantity'][$n];
			}
		}
		if($this->kclass->input['purchaseType']=='entrust'){
			$orderCounter=count($this->kclass->input['orderItemMaterialid']);
			for($i=1;$i<$orderCounter;$i++){
				if($this->kclass->input['orderItemDateline'][$i]!=0){
					$sd2=explode('-', $this->kclass->input['orderItemDateline'][$i]);
					$dateline=mktime(0,0,0,$sd2[1],$sd2[2],$sd2[0]);
				}else{
					$dateline=0;
				}
				if($this->kclass->input['orderItemId'][$i]>0){
					if($this->kclass->input['orderItemKill'][$i]>0 AND $this->kclass->input['orderItemKill'][$i]==$this->kclass->input['orderItemId'][$i]){
						$this->kclass->DB->query("UPDATE `item` SET killed='".TIMENOW."',killer='".$this->kclass->user['userid']."' WHERE itemid='".$this->kclass->input['orderItemId'][$i]."'");
					}else{
						if($this->kclass->input['orderItemMaterialid'][$i]>0 AND $this->kclass->input['orderItemQuantity'][$i]>0 AND $this->kclass->input['orderItemDateline'][$i]!=''){
							$material=$this->kclass->DB->queryFirst("SELECT materialid,unitid FROM material WHERE materialid='".$this->kclass->input['orderItemMaterialid'][$i]."'");
							$this->kclass->DB->query("
								UPDATE `item` SET
									`materialid`='".$this->kclass->input['orderItemMaterialid'][$i]."',
									`articleNo`='".$this->kclass->input['orderItemArticleNo'][$i]."',
									`title`='".$this->kclass->input['orderItemTitle'][$i]."',
									`quantity`='".$this->kclass->input['orderItemQuantity'][$i]."',
									`unitid`='".$material['unitid']."',
									`package`='".$this->kclass->input['orderItemPackage'][$i]."',
									`dateline`='".$dateline."',
									`remark`='".$this->kclass->input['orderItemRemark'][$i]."',
									`modified`='".TIMENOW."',
									`modifier`='".$this->kclass->user['userid']."'
								WHERE itemid='".$this->kclass->input['orderItemId'][$i]."'
							");
						}
					}
				}else{
					if($this->kclass->input['orderItemMaterialid'][$i]>0 AND $this->kclass->input['orderItemQuantity'][$i]>0 AND $this->kclass->input['orderItemDateline'][$i]!=''){
						$material=$this->kclass->DB->queryFirst("SELECT materialid,unitid FROM material WHERE materialid='".$this->kclass->input['orderItemMaterialid'][$i]."'");
						$this->kclass->DB->query("
							INSERT INTO item (module,mid,materialid,articleNo,title,quantity,unitid,package,dateline,remark,created,creator)
							VALUES ('porder','".$this->kclass->input['porderid']."','".$this->kclass->input['orderItemMaterialid'][$i]."','".$this->kclass->input['orderItemArticleNo'][$i]."','".$this->kclass->input['orderItemTitle'][$i]."','".$this->kclass->input['orderItemQuantity'][$i]."','".$material['unitid']."','".$this->kclass->input['orderItemPackage'][$i]."','".$dateline."','".$this->kclass->input['orderItemRemark'][$i]."','".TIMENOW."','".$this->kclass->user['userid']."')
						");
					}
				}
			}
		}
		$this->kclass->DB->query("
			UPDATE `porder` SET
				`customerid` = '".$this->kclass->input['customerid']."',
				`supplierid` = '".$this->kclass->input['supplierid']."',
				`currencyid`='".$this->kclass->input['currencyid']."',
				`pinvoiceTypeid`='".$this->kclass->input['pinvoiceTypeid']."',
				`taxRate`='".$this->kclass->input['taxRate']."',
				`deliverytermid`='".$this->kclass->input['deliverytermid']."',
				`paymenttermid`='".$this->kclass->input['paymenttermid']."',
				`paymentDate`='".$paymentDate."',
				`consignee`='".$this->kclass->input['consignee']."',
				`consigneeTel`='".$this->kclass->input['consigneeTel']."',
				`spare`='".$this->kclass->input['spare']."',
				`special`='".$this->kclass->input['special']."',
				`delivery`='".$this->kclass->input['delivery']."',
				`remark`='".$this->kclass->input['remark']."',
				`modified` = '".TIMENOW."',
				`modifier` = '".$this->kclass->user['userid']."'
			WHERE porderid='".$order['porderid']."'
		");
		$this->kclass->updateAttachs(array('module'=>'porder', 'mid'=>$order['porderid']));
		$this->kclass->messager(array(
			'title' => '修改订单',
			'text' => '订单 <b>'.$order['orderno'].'</b> 已修改成功!返回订单详情页',
			'url' => '/s.php?module=porder&action=view&porderid='.$order['porderid'],
			'sec' => 2
		));
	}
	//
	function kill(){
		if($this->kclass->input['porderid']<=0){
			$this->kclass->boinkIt('/s.php?module=porder');
		}
		if($this->kclass->input['rt']!='list' AND $this->kclass->input['confirm'] == 0){
			$this->kclass->boinkIt('/s.php?module=porder&action=view&porderid='.$this->kclass->input['porderid']);
		}
		if($this->kclass->input['porderid'] < 0){
			$e = '<li>请从正确的页面进行操作，如果此问题持续存在，请联系网站管理员。</li>';
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '删除订单',
				'text' => '您在删除订单的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		if($this->kclass->input['rt']=='view'){
			$rt = '&action=view&porderid='.$this->kclass->input['porderid'];
		}elseif($this->kclass->input['rt']=='list'){
			$rt = '#'.$this->kclass->input['porderid'];
		}
		$order=$this->kclass->DB->queryFirst("
			SELECT orderno
			FROM `porder`
			WHERE porderid='".$this->kclass->input['porderid']."'
		");
		if($order){
			$this->kclass->DB->query("
				UPDATE `porder`
				SET killed=".TIMENOW.",killer='".$this->kclass->user['userid']."'
				WHERE porderid='".$this->kclass->input['porderid']."'
			");
			$items=$this->kclass->DB->query("
				SELECT module,itemid
				FROM item
				WHERE module='porder' AND mid='".$this->kclass->input['porderid']."'
			");
			if($this->kclass->DB->numRows()){
				while($item=$this->kclass->DB->fetchArray($items)){
					if($item['module']=='purchaseApply'){
						$this->kclass->DB->query("UPDATE preinbounditem SET ifChooser=0 WHERE module='purchaseApply' AND itemid='".$item['itemid']."'");
					}elseif($item['module']=='entrust'){
						$this->kclass->DB->query("UPDATE entrustitem SET ifChooser=0 WHERE itemid='".$item['itemid']."'");
					}
				}
			}
			$this->kclass->messager(array(
				'title' => '删除订单成功',
				'text' => '订单 <b>'.$order['orderno'].'</b> 已成功被标记为删除!',
				'url' => '/s.php?module=porder'.$rt,
				'sec' => 2
			));
		}else{
			$this->kclass->messager(array(
				'title' => '删除订单失败',
				'text' => '您要删除的订单，不存在！',
				'url' => '/s.php?module=porder'.$rt,
				'sec' => 3
			));
		}
	}

	//
	function remove(){
		if($this->kclass->input['porderid']<=0){
			$this->kclass->boinkIt('/s.php?module=porder');
		}
		$order = $this->kclass->DB->queryFirst("
			SELECT orderno
			FROM `porder`
			WHERE porderid='".$this->kclass->input['porderid']."'
		");
$body = <<<EOF
<form action="/s.php?module=porder&action=kill" name="order" method="post">
<input type="hidden" name="module" value="porder">
<input type="hidden" name="action" value="kill">
<input type="hidden" name="porderid" value="{$this->kclass->input['porderid']}">
<input type="hidden" name="rt" value="{$this->kclass->input['rt']}">
<table><thead>
<thead>
<tr>
	<th>删除订单：{$order['orderno']}</th>
</tr>
</thead>
<tbody>
<tr class="even">
	<td class="middle">你确定要删除订单: <a href="/s.php?module=porder&action=view&porderid={$this->kclass->input['porderid']}" class="big bold" target="_blank">{$order['orderno']}</a> 吗?</td>
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
		$this->kclass->page['title'] .= ' - 删除 - '.$order['orderno'];
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 删除订单 - '.$order['orderno'], 'right' => '<a href="/s.php?module=porder">返回列表</a>', 'body'=>$body));
	}

	//
	function revival(){
		if($this->kclass->input['porderid']<=0){
			$this->kclass->boinkIt('/s.php?module=porder');
		}
		if($this->kclass->input['rt']!='list' AND $this->kclass->input['confirm'] == 0){
			$this->kclass->boinkIt('/s.php?module=porder&action=view&porderid='.$this->kclass->input['porderid']);
		}
		if($this->kclass->input['porderid'] < 0){
			$e = '<li>请从正确的页面进行操作，如果此问题持续存在，请联系网站管理员。</li>';
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '恢复订单',
				'text' => '您在恢复订单的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$order = $this->kclass->DB->queryFirst("
			SELECT orderno
			FROM `porder`
			WHERE porderid='".$this->kclass->input['porderid']."'
		");
		if($this->kclass->input['rt']=='view'){
			$rt = '&action=view&porderid='.$this->kclass->input['porderid'];
		}elseif($this->kclass->input['rt']=='list'){
			$rt = '#'.$this->kclass->input['porderid'];
		}
		if($order){
			$this->kclass->DB->query("
				UPDATE `porder`
				SET killed=0,killer=0
				WHERE porderid='".$this->kclass->input['porderid']."'
			");
			$this->kclass->messager(array(
				'title' => '恢复订单成功',
				'text' => '订单 <b>'.$order['orderno'].'</b> 已成功被恢复！',
				'url' => '/s.php?module=porder'.$rt,
				'sec' => 2
			));
		}else{
			$this->kclass->messager(array(
				'title' => '恢复订单失败',
				'text' => '您要恢复的订单不存在！',
				'url' => '/s.php?module=porder'.$rt,
				'sec' => 3
			));
		}
	}

	//
	function restore(){
		if($this->kclass->input['porderid']<=0){
			$this->kclass->boinkIt('/s.php?module=porder');
		}
		$order = $this->kclass->DB->queryFirst("
			SELECT orderno
			FROM `porder`
			WHERE porderid='".$this->kclass->input['porderid']."'
		");
$body = <<<EOF
<form action="/s.php?module=porder&action=revival" name="porder" method="post">
<input type="hidden" name="module" value="porder">
<input type="hidden" name="action" value="revival">
<input type="hidden" name="porderid" value="{$this->kclass->input['porderid']}">
<input type="hidden" name="rt" value="{$this->kclass->input['rt']}">
<table><thead>
<thead>
<tr>
	<th>恢复订单：{$order['orderno']}</th>
</tr>
</thead>
<tbody>
<tr class="even">
	<td class="middle">你确定要恢复订单: <a href="/s.php?module=porder&action=view&porderid={$this->kclass->input['porderid']}" class="big bold" target="_blank">{$order['orderno']}</a> 吗?</td>
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
		$this->kclass->page['title'] .= ' - 恢复 - '.$order['orderno'];
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 恢复订单 - '.$order['orderno'], 'right' => '<a href="/s.php?module=porder">返回列表</a>', 'body'=>$body));
	}
	//
	function verify(){
		$order=$this->kclass->DB->queryFirst("
			SELECT o.porderid,o.supplierid,o.customerNo,o.supplierNo,o.orderno,o.type,o.purchaseType,o.paymentDate,o.delivery,
					o.taxRate,o.consignee,o.consigneeTel,o.special,o.spare,o.ifVerify,o.verified,o.verifyRemark,o.remark,o.modified,o.created,o.killed,
				it.title AS invoiceType,
				s.title AS supplier,s.supplierno,
				c.title AS currency,c.symbol AS encurrency,
				p.title AS paymentterm,p.entitle AS enpaymentterm,
				d.title AS deliveryterm,d.entitle AS endeliveryterm
			FROM `porder` AS o
			LEFT JOIN `orderroute` AS od ON (od.routeid=o.routeid)
			LEFT JOIN `pinvoicetype` AS it ON (it.pinvoiceTypeid=o.pinvoiceTypeid)
			LEFT JOIN `supplier` AS s ON (s.supplierid=o.supplierid)
			LEFT JOIN `customer` AS cu ON (cu.customerid=o.customerid)
			LEFT JOIN `currency` AS c ON (c.currencyid=o.currencyid)
			LEFT JOIN `paymentterm` AS p ON (p.paymenttermid=o.paymenttermid)
			LEFT JOIN `deliveryterm` AS d ON (d.deliverytermid=o.deliverytermid)
			WHERE o.routeid=4 AND o.type='PO' AND o.porderid='".$this->kclass->input['porderid']."'
			LIMIT 0,1
		");
		if(!$order AND $order['ifVerify']!=0){
			$e='<li>数据错误，很抱歉~</li>';
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '审核订单',
				'text' => '审核订单<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$order['taxRate']=$order['taxRate'].'%';
		$order['paymentDate']=date('Y-m-d',$order['paymentDate']);
		foreach($this->purchaseType AS $key=>$val){
			if($order['purchaseType']==$val['entitle']){
				$purchaseType=$val['title'];
			}
		}
		// 订单明细
		$items=$this->kclass->DB->query("
			SELECT i.itemid,i.mid AS porderid,i.materialid,i.title,i.quantity,i.package,i.price,i.amount,i.discount,i.requirement,i.dateline,i.remark,
				m.materialno,m.title AS material,m.standard,
				u.title AS unit,
				br.title AS brand,
				p.purchaseid,p.purchaseno
			FROM `item` AS i
			LEFT JOIN `purchase` AS p ON (p.purchaseid=i.purchaseid)
			LEFT JOIN `material` AS m ON (m.materialid=i.materialid)
			LEFT JOIN `unit` AS u ON (u.unitid=i.unitid)
			LEFT JOIN `brand` AS br ON (br.brandid=i.brandid)
			WHERE i.killed=0 AND i.module='porder' AND i.mid='".$order['porderid']."'
			ORDER BY itemid ASC
		");
		if($this->kclass->DB->numRows()){
			$i=1;
			if($order['purchaseType']=='normal'){
				$orderItem.='<table class="hundred small"><thead></tr><th colspan="13">订单明细</th></tr></thead>
				<tbody><tr class="center"><td width="15">ID</td><td width="70">采购单编号</td><td>物资编号</td><td>物资名称</td><td>规格</td><td>品牌</td><td>包装方式</td><td align="right">数量/单位</td><td>折扣</td><td>单价</td><td>金额</td><td>到货时间</td><td>备注</td></tr>';
			}elseif($order['purchaseType']=='entrust'){
				$orderItem.='<table class="hundred small"><thead></tr><th colspan="13">订单明细<span class="right">'.$updateItem.'</span></th></tr></thead><tbody><tr class="center"><td width="15">ID</td><td width="70">采购单编号</td><td>物资编号</td><td>物资名称</td><td>规格</td><td>品牌</td><td>包装方式</td><td align="right">数量/单位</td><td>折扣</td><td>单价</td><td>金额</td><td>加工要求</td><td>备注</td></tr>';
			}
			while($item=$this->kclass->DB->fetchArray($items)){
				$total+=$item['amount'];
				$orderItem.='<tr class="'.$this->kclass->rotateLine().'">
					<td>'.$i.'</td>
					<td><a href="/s.php?module=purchase&action=view&purchaseid='.$item['purchaseid'].'">'.$item['purchaseno'].'</a></td>
					<td><a href="/s.php?module=material&action=view&materialid='.$item['materialid'].'">'.$item['materialno'].'</a></td>
					<td>'.$item['material'].'</td>
					<td>'.$item['standard'].'</td>
					<td>'.$item['brand'].'</td>
					<td>'.$item['package'].'</td>
					<td align="right">'.number_format($item['quantity']).' '.$item['unit'].'</td>
					<td align="right">'.$this->kclass->iif($item['discount']>0, $item['discount'].'%', '').'</td>
					<td>'.number_format($item['price'],2).'</td>
					<td>'.number_format($item['amount'],2).'</td>
					<td>'.$this->kclass->iif($o['purchaseType']=='entrust',$item['requirement'],date('Y-m-d',$item['dateline'])).'</td>
					<td>'.$item['remark'].'</td></tr>';
				$i++;
			}
			$total=number_format($total, 2);
			$cntotal=$this->kclass->numberToCNAccount($total);
			$orderItem.='<tr class="even"><td colspan="3" align="center">合　　计</td><td colspan="7">人　民　币：　 <span class="bold middle">'.$cntotal.'</span></td><td colspan="3">小写： <span class="bold middle">'.$total.'</span></td></tr>
			<tr class="odd"><td class="small gray" colspan="13">注意事项：可以点击“修改订单”，继续添加所需要购买的物资</td></tr></tbody></table>';
			if($order['purchaseType']=='entrust'){
				$items=$this->kclass->DB->query("
					SELECT i.itemid,i.mid AS porderid,i.materialid,i.title,i.quantity,i.package,i.price,i.amount,i.discount,i.requirement,i.dateline,i.remark,
						m.materialno,m.title AS material,m.standard,
						u.title AS unit
					FROM `item` AS i
					LEFT JOIN `material` AS m ON (m.materialid=i.materialid)
					LEFT JOIN `unit` AS u ON (u.unitid=i.unitid)
					WHERE i.killed=0 AND i.module='porder' AND i.mid='".$order['porderid']."'
					ORDER BY itemid ASC
				");
				if($this->kclass->DB->numRows()){
					$orderItem.='<table class="hundred small"><thead></tr><th colspan="12">物资提供明细</th></tr></thead><tbody><tr class="center"><td width="30">ID</td><td>物资编号</td><td>物资名称</td><td>规格</td><td>包装方式</td><td align="right">数量/单位</td><td>到货时间</td><td>备注</td></tr>';
					$k=1;
					while($item=$this->kclass->DB->fetchArray($items)){
						$total+=$item['amount'];
						$orderItem.='<tr class="'.$this->kclass->rotateLine().'">
							<td width="30">'.$k.'</td>
							<td><a href="/s.php?module=material&action=view&materialid='.$item['materialid'].'">'.$item['materialno'].'</a></td>
							<td>'.$item['material'].'</td>
							<td>'.$item['standard'].'</td>
							<td>'.$item['package'].'</td>
							<td align="right">'.number_format($item['quantity']).' '.$item['unit'].'</td>
							<td>'.date('Y-m-d',$item['dateline']).'</td>
							<td>'.$item['remark'].'</td></tr>';
						$k++;
					}
					$orderItem.='	<tr class="odd"><td class="small gray" colspan="12">注意事项：可以点击“修改订单”，继续添加所需要购买的物资</td></tr></tbody></table>';
				}
			}
		}

$body=<<<EOF
<table class="hundred">
<thead><tr><th colspan="4">订单</th></tr></thead>
<tbody>
<tr class="odd">
	<td width="100">订单编号：</td><td width="440">{$order['orderno']}</td>
	<td width="100">采购类型：</td><td>{$purchaseType}</td>
</tr>
<tr class="even">
	<td>发票类型：</td><td>{$order['invoiceType']}</td>
	<td>税　　率：</td><td>{$order['taxRate']}</td>
</tr>
<tr class="even">
<td>供 应 商：</td><td><a href="/s.php?module=psupplier&action=view&supplierid={$order['supplierid']}">{$order['supplier']}</a></td>
<td>发票类型：</td><td>{$order['invoiceType']} （{$order['taxRate']}）</td>
</tr>
<tr class="odd">
	<td width="100">交货方式：</td><td width="450">{$order['deliveryterm']} </td>
	<td width="100">付款方式：</td><td>{$order['paymentterm']}</td>
</tr>
<tr class="even">
	<td>币　　种：</td><td>{$order['currency']} <span class="gray">{$order['symbol']}</span></td>
EOF;
	$body.=$this->kclass->iif($order['purchaseType']=='entrust','<td></td><td></td></tr>','<td></td><td></td>');
$body.=<<<EOF
</tr>
<tr class="even">
<td>收货联系人：</td><td>{$order['consignee']}　电话：{$order['consigneeTel']}</td>
<td>提供备品率：</td><td>{$order['spare']} %</td>
</tr>
<tr class="odd">
	<td>产品规格及发货补充说明：</td><td>{$order['delivery']}</td>
	<td>备　　注：</td><td>{$order['remark']}</td>
</tr>
<tr class="odd">
	<td>产品要求：</td><td>{$order['special']}</td>
	<td></td><td></td>
</tr>
{$orderItem}
<form method="post" action="/s.php?module=porder&action=doverify">
<input type="hidden" name="module" value="porder">
<input type="hidden" name="action" value="doverify">
<input type="hidden" name="porderid" value="{$this->kclass->input['porderid']}">
<table>
<thead><tr><th colspan="4">审核 <span class="small red">(订单评审)</span></th></tr></thead>
<tbody>
<tr class="even">
<td>审　　核：<span class="bold red">*</span></td><td class="center"><input type="radio" name="ifVerify" value="1" checked />通过　　　<input type="radio" name="ifVerify" value="-1" />不通过</td>
</tr>
<tr class="odd">
<td>审核备注：</td><td><textarea name="verifyRemark" style="width:440px;height:110px;"></textarea></td>
</tr>
<tr class="even">
<td colspan=2 class="center"><input type="submit" value=" 提交 " /><input type="reset" value="重置" /></td>
</tr>
</tbody>
</table>
</form>
EOF;
		$this->kclass->tbline+=4;
		$this->kclass->page['title'].='审核订单 - '.$order['orderno'];
		$this->kclass->page['onload'].='dc.tabhover();';
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - <a href="/s.php?module=porder&action=view&porderid='.$this->kclass->input['porderid'].'">查看订单</a> - 审核订单 - '.$order['orderno'], 'right'=>'<a href="/s.php?module=porder&action=list">返回列表</a>　|　<a href="/s.php?module=porder&action=view&porderid='.$this->kclass->input['porderid'].'">查看订单</a>','body'=>$body));
	}
	//
	function doverify(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			$porder=$this->kclass->DB->queryFirst("SELECT orderno FROM porder WHERE porderid='".$this->kclass->input['porderid']."'");
			if($this->kclass->input['ifVerify']!=1 AND $this->kclass->input['ifVerify']!=-1){
				$e.='<li>请选择采购订单中的 是否通过审核 按钮。</li>';
			}
		}
		if($e){
			$this->kclass->messager(array(
				'title' => '审核采购计划单',
				'text' => '您在审核采购订单的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$this->kclass->DB->query("
			UPDATE porder SET
				ifVerify='".$this->kclass->input['ifVerify']."',
				verifier='".$this->kclass->user['userid']."',
				verified='".TIMENOW."',
				verifyRemark='".$this->kclass->input['verifyRemark']."'
			WHERE porderid='".$this->kclass->input['porderid']."'
		");
		if($this->kclass->input['ifVerify']==1){
			$verify='已通过';
		}elseif($this->kclass->input['ifVerify']==-1){
			$verify='未通过';
		}
		$this->kclass->messager(array(
			'title' => '审核采购订单',
			'text' => '采购订单 【<b>'.$porder['orderno'].'</b>】 '.$verify.'审核!返回查看采购订单',
			'url' => '/s.php?module=porder&action=list',
			'sec' => 2
		));
	}
	//
	function approve(){
		$order=$this->kclass->DB->queryFirst("
			SELECT o.porderid,o.supplierid,o.customerNo,o.supplierNo,o.orderno,o.type,o.purchaseType,o.paymentDate,o.delivery,
					o.taxRate,o.consignee,o.consigneeTel,o.special,o.spare,o.ifVerify,o.verified,o.verifyRemark,o.remark,o.modified,o.created,o.killed,
				it.title AS invoiceType,
				s.title AS supplier,s.supplierno,
				c.title AS currency,c.symbol AS encurrency,
				p.title AS paymentterm,p.entitle AS enpaymentterm,
				d.title AS deliveryterm,d.entitle AS endeliveryterm
			FROM `porder` AS o
			LEFT JOIN `orderroute` AS od ON (od.routeid=o.routeid)
			LEFT JOIN `pinvoicetype` AS it ON (it.pinvoiceTypeid=o.pinvoiceTypeid)
			LEFT JOIN `supplier` AS s ON (s.supplierid=o.supplierid)
			LEFT JOIN `customer` AS cu ON (cu.customerid=o.customerid)
			LEFT JOIN `currency` AS c ON (c.currencyid=o.currencyid)
			LEFT JOIN `paymentterm` AS p ON (p.paymenttermid=o.paymenttermid)
			LEFT JOIN `deliveryterm` AS d ON (d.deliverytermid=o.deliverytermid)
			WHERE o.routeid=4 AND o.type='PO' AND o.porderid='".$this->kclass->input['porderid']."'
			LIMIT 0,1
		");
		if(!$order AND $order['ifVerify']!=0){
			$e='<li>数据错误，很抱歉~</li>';
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '审批订单',
				'text' => '审批订单<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$order['taxRate']=$order['taxRate'].'%';
		$order['paymentDate']=date('Y-m-d',$order['paymentDate']);
		foreach($this->purchaseType AS $key=>$val){
			if($order['purchaseType']==$val['entitle']){
				$purchaseType=$val['title'];
			}
		}
		// 订单明细
		$items=$this->kclass->DB->query("
			SELECT i.itemid,i.mid AS porderid,i.materialid,i.title,i.quantity,i.package,i.price,i.amount,i.discount,i.requirement,i.dateline,i.remark,
				m.materialno,m.title AS material,m.standard,
				u.title AS unit,
				br.title AS brand,
				p.purchaseid,p.purchaseno
			FROM `item` AS i
			LEFT JOIN `purchase` AS p ON (p.purchaseid=i.purchaseid)
			LEFT JOIN `material` AS m ON (m.materialid=i.materialid)
			LEFT JOIN `unit` AS u ON (u.unitid=i.unitid)
			LEFT JOIN `brand` AS br ON (br.brandid=i.brandid)
			WHERE i.killed=0 AND i.module='porder' AND i.mid='".$order['porderid']."'
			ORDER BY itemid ASC
		");
		if($this->kclass->DB->numRows()){
			$i=1;
			if($order['purchaseType']=='normal'){
				$orderItem.='<table class="hundred small"><thead></tr><th colspan="13">订单明细</th></tr></thead>
				<tbody><tr class="center"><td width="15">ID</td><td width="70">采购单编号</td><td>物资编号</td><td>物资名称</td><td>规格</td><td>品牌</td><td>包装方式</td><td align="right">数量/单位</td><td>折扣</td><td>单价</td><td>金额</td><td>到货时间</td><td>备注</td></tr>';
			}elseif($order['purchaseType']=='entrust'){
				$orderItem.='<table class="hundred small"><thead></tr><th colspan="13">订单明细<span class="right">'.$updateItem.'</span></th></tr></thead><tbody><tr class="center"><td width="15">ID</td><td width="70">采购单编号</td><td>物资编号</td><td>物资名称</td><td>规格</td><td>品牌</td><td>包装方式</td><td align="right">数量/单位</td><td>折扣</td><td>单价</td><td>金额</td><td>加工要求</td><td>备注</td></tr>';
			}
			while($item=$this->kclass->DB->fetchArray($items)){
				$total+=$item['amount'];
				$orderItem.='<tr class="'.$this->kclass->rotateLine().'">
					<td>'.$i.'</td>
					<td><a href="/s.php?module=purchase&action=view&purchaseid='.$item['purchaseid'].'">'.$item['purchaseno'].'</a></td>
					<td><a href="/s.php?module=material&action=view&materialid='.$item['materialid'].'">'.$item['materialno'].'</a></td>
					<td>'.$item['material'].'</td>
					<td>'.$item['standard'].'</td>
					<td>'.$item['brand'].'</td>
					<td>'.$item['package'].'</td>
					<td align="right">'.number_format($item['quantity']).' '.$item['unit'].'</td>
					<td align="right">'.$this->kclass->iif($item['discount']>0, $item['discount'].'%', '').'</td>
					<td>'.number_format($item['price'],2).'</td>
					<td>'.number_format($item['amount'],2).'</td>
					<td>'.$this->kclass->iif($o['purchaseType']=='entrust',$item['requirement'],date('Y-m-d',$item['dateline'])).'</td>
					<td>'.$item['remark'].'</td></tr>';
				$i++;
			}
			$total=number_format($total, 2);
			$cntotal=$this->kclass->numberToCNAccount($total);
			$orderItem.='<tr class="even"><td colspan="3" align="center">合　　计</td><td colspan="7">人　民　币：　 <span class="bold middle">'.$cntotal.'</span></td><td colspan="3">小写： <span class="bold middle">'.$total.'</span></td></tr>
			<tr class="odd"><td class="small gray" colspan="13">注意事项：可以点击“修改订单”，继续添加所需要购买的物资</td></tr></tbody></table>';
			if($order['purchaseType']=='entrust'){
				$items=$this->kclass->DB->query("
					SELECT i.itemid,i.mid AS porderid,i.materialid,i.title,i.quantity,i.package,i.price,i.amount,i.discount,i.requirement,i.dateline,i.remark,
						m.materialno,m.title AS material,m.standard,
						u.title AS unit
					FROM `item` AS i
					LEFT JOIN `material` AS m ON (m.materialid=i.materialid)
					LEFT JOIN `unit` AS u ON (u.unitid=i.unitid)
					WHERE i.killed=0 AND i.module='porder' AND i.mid='".$order['porderid']."'
					ORDER BY itemid ASC
				");
				if($this->kclass->DB->numRows()){
					$orderItem.='<table class="hundred small"><thead></tr><th colspan="12">物资提供明细</th></tr></thead><tbody><tr class="center"><td width="30">ID</td><td>物资编号</td><td>物资名称</td><td>规格</td><td>包装方式</td><td align="right">数量/单位</td><td>到货时间</td><td>备注</td></tr>';
					$k=1;
					while($item=$this->kclass->DB->fetchArray($items)){
						$total+=$item['amount'];
						$orderItem.='<tr class="'.$this->kclass->rotateLine().'">
							<td width="30">'.$k.'</td>
							<td><a href="/s.php?module=material&action=view&materialid='.$item['materialid'].'">'.$item['materialno'].'</a></td>
							<td>'.$item['material'].'</td>
							<td>'.$item['standard'].'</td>
							<td>'.$item['package'].'</td>
							<td align="right">'.number_format($item['quantity']).' '.$item['unit'].'</td>
							<td>'.date('Y-m-d',$item['dateline']).'</td>
							<td>'.$item['remark'].'</td></tr>';
						$k++;
					}
					$orderItem.='	<tr class="odd"><td class="small gray" colspan="12">注意事项：可以点击“修改订单”，继续添加所需要购买的物资</td></tr></tbody></table>';
				}
			}
		}

$body=<<<EOF
<table class="hundred">
<thead><tr><th colspan="4">订单</th></tr></thead>
<tbody>
<tr class="odd">
	<td width="100">订单编号：</td><td width="440">{$order['orderno']}</td>
	<td width="100">采购类型：</td><td>{$purchaseType}</td>
</tr>
<tr class="even">
	<td>发票类型：</td><td>{$order['invoiceType']}</td>
	<td>税　　率：</td><td>{$order['taxRate']}</td>
</tr>
<tr class="even">
<td>供 应 商：</td><td><a href="/s.php?module=psupplier&action=view&supplierid={$order['supplierid']}">{$order['supplier']}</a></td>
<td>发票类型：</td><td>{$order['invoiceType']} （{$order['taxRate']}）</td>
</tr>
<tr class="odd">
	<td width="100">交货方式：</td><td width="450">{$order['deliveryterm']} </td>
	<td width="100">付款方式：</td><td>{$order['paymentterm']}</td>
</tr>
<tr class="even">
	<td>币　　种：</td><td>{$order['currency']} <span class="gray">{$order['symbol']}</span></td>
EOF;
	$body.=$this->kclass->iif($order['purchaseType']=='entrust','<td>给付日期：:</td><td>'.$order['paymentDate'].'</td></tr>','<td></td><td></td>');
$body.=<<<EOF
</tr>
<tr class="even">
<td>收货联系人：</td><td>{$order['consignee']}　电话：{$order['consigneeTel']}</td>
<td>提供备品率：</td><td>{$order['spare']} %</td>
</tr>
<tr class="odd">
	<td>产品规格及发货补充说明：</td><td>{$order['delivery']}</td>
	<td>备　　注：</td><td>{$order['remark']}</td>
</tr>
<tr class="odd">
	<td>产品要求：</td><td>{$order['special']}</td>
	<td></td><td></td>
</tr>
{$orderItem}
<form method="post" action="/s.php?module=porder&action=doapprove">
<input type="hidden" name="module" value="porder">
<input type="hidden" name="action" value="doapprove">
<input type="hidden" name="porderid" value="{$this->kclass->input['porderid']}">
<table>
<thead><tr><th colspan="4">审批 <span class="small red">(订单评审)</span></th></tr></thead>
<tbody>
<tr class="even">
<td>审　　批：<span class="bold red">*</span></td><td class="center"><input type="radio" name="ifApprove" value="1" checked />通过　　　<input type="radio" name="ifApprove" value="-1" />不通过</td>
</tr>
<tr class="odd">
<td>审批备注：</td><td><textarea name="approvalRemark" style="width:440px;height:110px;"></textarea></td>
</tr>
<tr class="even">
<td colspan=2 class="center"><input type="submit" value=" 提交 " /><input type="reset" value="重置" /></td>
</tr>
</tbody>
</table>
</form>
EOF;
		$this->kclass->tbline+=4;
		$this->kclass->page['title'].='审批订单 - '.$order['orderno'];
		$this->kclass->page['onload'].='dc.tabhover();';
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - <a href="/s.php?module=porder&action=view&porderid='.$this->kclass->input['porderid'].'">查看订单</a> - 审批订单 - '.$order['orderno'], 'right'=>'<a href="/s.php?modhttp://wisdom.hk/a.php?module=setting&action=moduleule=porder&action=list">返回列表</a>　|　<a href="/s.php?module=porder&action=view&porderid='.$this->kclass->input['porderid'].'">查看订单</a>','body'=>$body));
	}
	//
	function doapprove(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			$porder=$this->kclass->DB->queryFirst("SELECT orderno FROM porder WHERE porderid='".$this->kclass->input['porderid']."'");
			if($this->kclass->input['ifApprove']!=1 AND $this->kclass->input['ifApprove']!=-1){
				$e.='<li>请选择采购订单中的 是否通过审核 按钮。</li>';
			}
		}
		if($e){
			$this->kclass->messager(array(
				'title' => '审批采购订单',
				'text' => '您在审批采购订单的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$this->kclass->DB->query("
			UPDATE porder SET
				ifApprove='".$this->kclass->input['ifApprove']."',
				approver='".$this->kclass->user['userid']."',
				approved='".TIMENOW."',
				approvalRemark='".$this->kclass->input['approvalRemark']."'
			WHERE porderid='".$this->kclass->input['porderid']."'
		");
		$items=$this->kclass->DB->query("SELECT itemid,versionid,quantity FROM `item` WHERE killed=0 AND module='porder' AND mid='".$this->kclass->input['porderid']."' ORDER BY itemid ASC");
		if($this->kclass->DB->numRows()){
			while($item=$this->kclass->DB->fetchArray($items)){
				$this->kclass->DB->query("UPDATE materialversion SET planInbound=planInbound+'".$item['quantity']."' WHERE versionid='".$item['versionid']."'");
			}
		}
		if($this->kclass->input['ifApprove']==1){
			$verify='已通过';
		}elseif($this->kclass->input['ifApprove']==-1){
			$verify='未通过';
		}
		$this->kclass->messager(array(
			'title' => '审批采购订单',
			'text' => '采购订单 【<b>'.$porder['orderno'].'</b>】 '.$verify.'审批!返回查看采购订单',
			'url' => '/s.php?module=porder&action=list',
			'sec' => 2
		));
	}
	//
	function orderStatus(){
		$order=$this->kclass->DB->queryFirst("
			SELECT porderid,orderno,ifVerify,ifApprove,orderStatus
			FROM `porder`
			WHERE killed=0 AND porderid='".$this->kclass->input['porderid']."'
			LIMIT 0,1
		");
		if(!$order){
			$e.='<li>数据错误，很抱歉~</li>';
		}
		if($order['orderStatus']!=0){
			$e.='<li>该订单已经完成或者取消</li>';
		}
		if($order['ifVerify']==0){
			$e.='<li>未审核订单不可修改状态</li>';
		}
		if($order['ifApprove']==0){
			$e.='<li>未审批订单不可修改状态</li>';
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '手动修改订单状态',
				'text' => '您在手动修改订单状态的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}

$body=<<<EOF
<form method="post" action="/s.php?module=porder&action=doorderStatus">
<input type="hidden" name="module" value="porder">
<input type="hidden" name="action" value="doorderStatus">
<input type="hidden" name="porderid" value="{$this->kclass->input['porderid']}">
<table>
<thead><tr><th colspan="4">手动修改订单状态</th></tr></thead>
<tbody>
<tr class="even">
<td>订单状态：<span class="bold red">*</span></td>
<td class="center"><input type="radio" name="orderStatus" value="1" />完成订单　<input type="radio" name="orderStatus" value="-1" />取消订单
</tr>
<tr class="odd">
<td>修改原因：<span class="bold red">*</span></td><td><textarea name="operateRemark" style="width:440px;height:110px;"></textarea></td>
</tr>
<tr class="even">
<td colspan=2 class="center"><input type="submit" value=" 提交 " /><input type="reset" value="重置" /></td>
</tr>
</tbody>
</table>
</form>
EOF;
		$this->kclass->tbline+=4;
		$this->kclass->page['title'].='手动修改订单状态 - '.$order['orderno'];
		$this->kclass->page['onload'].='dc.tabhover();';
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - <a href="/s.php?module=porder&action=view&porderid='.$this->kclass->input['porderid'].'">查看订单</a> - 手动修改订单状态 - '.$order['orderno'], 'right'=>'<a href="/s.php?modhttp://wisdom.hk/a.php?module=setting&action=moduleule=porder&action=list">返回列表</a>　|　<a href="/s.php?module=porder&action=view&porderid='.$this->kclass->input['porderid'].'">查看订单</a>','body'=>$body));
	}
	//
	function doorderStatus(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			if($this->kclass->input['orderStatus']!=1 AND $this->kclass->input['orderStatus']!=-1){
				$e.='<li>请选择正确的订单状态</li>';
			}
			if($this->kclass->input['operateRemark']==''){
				$e.='<li>请填写修改原因</li>';
			}
		}
		$porder=$this->kclass->DB->queryFirst("SELECT porderid,orderno FROM porder WHERE killed=0 AND ifVerify<>0 AND ifApprove<>0 AND porderid='".$this->kclass->input['porderid']."'");
		if(!$porder){
			$e.='<li>数据错误，很抱歉！</li>';
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '手动修改订单状态',
				'text' => '提交修改订单状态的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
			$this->kclass->DB->query("
				UPDATE porder SET
					orderStatus='".$this->kclass->input['orderStatus']."',
					operater='".$this->kclass->user['userid']."',
					operated='".TIMENOW."',
					operateRemark='".$this->kclass->input['operateRemark']."'
				WHERE porderid='".$porder['porderid']."'
			");
			if($this->kclass->input['orderStatus']==1){
				$status='确定';
			}elseif($this->kclass->input['orderStatus']==-1){
				$status='取消';
			}

		$this->kclass->messager(array(
			'title' => '审批采购订单',
			'text' => '采购订单 【<b>'.$porder['orderno'].'</b>】 已成功'.$status.'!返回查看采购订单',
			'url' => '/s.php?module=porder&action=list',
			'sec' => 2
		));
	}
	//
	function revise(){
		if($this->kclass->input['reviseid']>0){
			$revise = $this->kclass->DB->queryFirst("
				SELECT `revise`.ptext,`revise`.version,`revise`.remark
				FROM `revise`
				WHERE reviseid='".$this->kclass->input['reviseid']."'
			");
			if($revise){
				$this->kclass->page['onload'] .= "dc.tabs({'id':'porder'});dc.tabhover()";
				$this->kclass->page['title'] .= ' - 订单版本R'.$revise['version'];
				$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl.' - 订单版本信息　版本R'.$revise['version'], 'right' => '', 'body'=>$revise['ptext']));
			}else{
				$this->kclass->messager(array(
						'title' => '查看订单历史版本',
						'text' => '没有此历史版本!',
						'url' => '/s.php?module=porder&action=view&porderid='.$this->kclass->input['porderid'],
						'sec' => 5
				));
			}
		}
	}
	//
	function addOld(){
		$supplier=$this->kclass->chooserSupplier(array('hName'=>'supplierid','name'=>'supplier','width'=>400));
		$selectCustomer=$this->kclass->DB->queryFirst("SELECT cntitle FROM customer WHERE killed=0 AND customerid=2 LIMIT 0,1");
		$customer=$this->kclass->chooserCustomer(array('hName'=>'customerid','name'=>'customer','width'=>450,'selectedid'=>2,'value'=>$selectCustomer['cntitle']));
		$loading = $this->kclass->chooserPort(array('name'=>'loading', 'hasBlank'=>1, 'width'=>450, 'selectedid'=>$order['loading']));
		$discharge = $this->kclass->chooserPort(array('name'=>'discharge', 'hasBlank'=>1, 'width'=>450, 'selectedid'=>$order['discharge']));
		$deliveryterm=$this->kclass->chooserDeliveryterm(array('name'=>'deliverytermid','width'=>200,'hasBlank'=>1,'topname'=>'请选择'));
		$paymentterm=$this->kclass->chooserPaymentterm(array('name'=>'paymenttermid','width'=>200,'hasBlank'=>1,'topname'=>'请选择','module'=>'produce'));
		$shipmethod=$this->kclass->chooserShipmethod(array('name'=>'shipmethodid','width'=>200,'hasBlank'=>1,'topname'=>'请选择'));
		$currency=$this->kclass->chooserCurrency(array('name'=>'currencyid','width'=>100,'hasBlank'=>1,'topname'=>'请选择','selectedid'=>3));
		$pinvoiceType=$this->kclass->chooserPInvoiceType(array('name'=>'pinvoiceTypeid','hasBlank'=>1,'topname'=>'无发票','selectedid'=>2));
		$taxRate=$this->kclass->chooserTaxRate(array('name'=>'taxRate','selectedid'=>17));
		$fee=$this->kclass->formFee(array('module'=>'porder'));
		$upload=$this->kclass->upload(array('title'=>'相关附件：'));
		$itemtr='<table class="hundred"><thead></tr><th colspan="10">订单明细表</th></tr></thead><tbody><tr class="center"><td width="15">ID</td><td>选择物资 <span class="red bold">*</span><span class="small gray">（输入物资编号自动出现列表）</span></td><td>品牌</td><td>数量 <span class="red bold">*</span></td><td>包装方式</td><td>单价 <span class="red bold">*</span></td><td>折扣</td><td>到货日期 <span class="red bold">*</span></td><td>备注</td></tr>';
		for($i=1;$i<6;$i++){
			$itemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').' small">
				<td>'.$i.'</td>
				<td>'.$this->kclass->chooserMaterial(array('hName'=>'itemMaterialid['.$i.']','hId'=>'itemMaterialid'.$i,'name'=>'itemMaterial['.$i.']','id'=>'itemMaterial'.$i,'width'=>400,'line'=>$i)).'</td>
				<td>'.$this->kclass->chooserBrand(array('name'=>'itemBrandid['.$i.']','hasBlank'=>1,'topname'=>'请选择','width'=>'100')).'</td>
				<td><input type="text" name="itemQuantity['.$i.']" size="10"></td>
				<td><input type="text" name="itemPackage['.$i.']" style="width:100px;"></td>
				<td><input type="text" name="itemPrice['.$i.']" size="5"></td>
				<td><input type="text" name="itemDiscount['.$i.']" value="100" size="3"></td>
				<td><input type="text" name="itemDateline['.$i.']" id="dateline'.$i.'" size="10"></td>
				<td><input type="text" name="itemRemark['.$i.']" style="width:130px;"></td>
			</tr>';
			if($i<5){
				$id.='#dateline'.$i.',';
			}else{
				$id.='#dateline'.$i;
			}
		}
		$itemtr.='<tr class="even"><td class="small gray" colspan="10">注意事项：<br>①如果所列表格不够，那么在提交保存后再点击“修改”，来增加订单物资；<br>②订单明细的物资都从“已通过审核的采购单及物资”中选择物资；<br>③“已通过审核的采购单及物资”中 灰色的物资表示已经被选中过，不可以继续选择；<br>④“到货日期”的格式为2012-02-06，中间用半角短横杠隔开；<br></tr></tbody></table>';

$body=<<<EOF
<form action="/s.php?module=porder&action=insertOld" name="porder" method="post">
<input type="hidden" name="module" value="porder" />
<input type="hidden" name="action" value="insertOld" />
<input type="hidden" name="routeid" value="4" />
<input type="hidden" name="purchaseType" value="{$this->kclass->input['purchaseType']}" />
<input type="hidden" name="orderno" value="{$orderno}" />
<table class="hundred">
<thead></tr><th colspan="4">基本信息</th></tr></thead>
<tbody>
<tr class="even">
<td>订单编号：<span class="red bold">*</span></td><td><input type="text" size="20" name="orderno" /></span>　币种：<span class="red bold">*</span>{$currency}</td>
<td>订单类型：<span class="red bold">*</span></td><td>PO</td>
</tr>
<tr class="odd">
<td>发票类型：<span class="red bold">*</span></td><td>{$pinvoiceType}</td>
<td>税　　率：<span class="red bold">*</span></td><td>{$taxRate}</td>
</tr>
<tr class="even">
<td>订货单位：<span class="red bold">*</span></td><td>{$customer}</td>
<td>供 货 商：<span class="red bold">*</span></td><td>{$supplier}</td>
</tr>
<tr class="odd">
<td title="Customer Order No">客户编号：</td>
<td><input type="text" name="customerNo" /> <span class="gray small">客户使用的订单号</span></td>
<td title="Supplier Order No">供应商编号：</td>
<td><input type="text" name="supplierNo" /> <span class="gray small">供应商使用的订单号</span></td>
</tr>
<tr class="odd">
<td>交货方式：</td><td>{$deliveryterm}</td>
<td>付款方式：</td><td>{$paymentterm}</td>
</tr>
<tr class="odd">
<td>交货地点：</td><td>{$loading}</td>
<td>卸货地点：</td><td>{$discharge}</td>
</tr>
<tr class="even">
<td>运输方式：</td><td>{$shipmethod}</td>
<td></td><td></td>
</tr>
<tr class="odd">
<td>收货联系人：<span class="red bold">*</span></td><td><input type="text" name="consignee" value="张小姐" />　电话：<span class="red bold">*</span> <input type="text" name="consigneeTel" value="0755-28990261" /></td>
<td>提供备品率：<span class="red bold">*</span></td><td><input type="text" name="spare" value="1" size="4" /> %</td>
</tr>
<tr class="even">
<td>产品规格及发货补充说明：</td><td><textarea name="delivery" style="width:444px;height:111px">
{$order['delivery']}
</textarea></td>
<td>其他条款：</td><td><textarea name="remark" style="width:444px;height:111px">
{$order['remark']}
</textarea></td>
</tr>
<tr class="even">
<td>产品要求：</td><td><textarea name="special" style="width:444px;height:111px"></textarea></td>
<td></td><td></td>
</tr>
</table>
{$itemtr}
{$fee}
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
		$this->kclass->page['title'].='新建订单';
		$this->kclass->page['onload'].='dc.tabhover();$(\''.$id.'\').datepicker()';
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - <a href="/s.php?module=porder">订单列表</a> - 新建订单', 'right'=>'<a href="/s.php?module=porder&action=list">返回列表</a>','body'=>$body));
	}
	//
	function insertOld(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			if($this->kclass->input['orderno'] ==''){
				$e .= '<li>请输入订单中的 订单编号。</li>';
			}
			if($this->kclass->input['currencyid'] <= 0){
				$e .= '<li>请选择订单中的 币种。</li>';
			}
			if($this->kclass->input['supplierid'] <= 0){
				$e .= '<li>请选择订单中的 供应商。</li>';
			}
			$j=count($this->kclass->input['itemMaterialid']);
			$hasItem=0;
			for($i=1;$i<$j+1;$i++){
				if($this->kclass->input['itemMaterialid'][$i]>0 AND intval($this->kclass->input['itemQuantity'][$i])>=0 AND $this->kclass->input['itemPrice'][$i]>=0 AND $this->kclass->input['itemDateline'][$i]!=''){
					$hasItem=1;
				}
			}
			if($hasItem==0){
				$e .= '<li>需要填写至少有一条订单明细，才能建立订单。</li>';
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
		// insert order
		$this->kclass->DB->query("
			INSERT INTO `porder`
				(`routeid`,`type`,`orderno`,`pinvoiceTypeid`,`supplierid`,`customerid`,`supplierNo`,`customerNo`,`currencyid`,`loading`,`discharge`,`deliverytermid`,`paymenttermid`,`shipmethodid`,`taxRate`,`special`,`consignee`,`consigneeTel`,`spare`,`delivery`,`remark`,`created`,`creator`)
			VALUES (4,'PO','".$this->kclass->input['orderno']."','".$this->kclass->input['pinvoiceTypeid']."','".$this->kclass->input['supplierid']."','".$this->kclass->input['customerid']."','".$this->kclass->input['supplierNo']."','".$this->kclass->input['orderno']."','".$this->kclass->input['currencyid']."','".$this->kclass->input['loading']."','".$this->kclass->input['discharge']."','".$this->kclass->input['deliverytermid']."','".$this->kclass->input['paymenttermid']."','".$this->kclass->input['shipmethodid']."','".$this->kclass->input['taxRate']."','".$this->kclass->input['special']."','".$this->kclass->input['consignee']."','".$this->kclass->input['consigneeTel']."','".$this->kclass->input['spare']."','".$this->kclass->input['delivery']."','".$this->kclass->input['remark']."','".TIMENOW."','".$this->kclass->user['userid']."')
		");

		$porderid=$this->kclass->DB->insertID();
		// insert item
		$count=count($key);
		for($m=1;$m<$j+1;$m++){
			if($this->kclass->input['itemMaterialid'][$m]>0 AND $this->kclass->input['itemQuantity'][$m]!='' AND $this->kclass->input['itemPrice'][$m]!='' AND $this->kclass->input['itemDateline'][$m]!=''){
				$this->kclass->input['itemQuantity'][$m]=intVal($this->kclass->input['itemQuantity'][$m]);
				$this->kclass->input['itemPrice'][$m]=floatVal($this->kclass->input['itemPrice'][$m]);
				$this->kclass->input['itemDiscount'][$m]=intVal($this->kclass->input['itemDiscount'][$m]);
				if($this->kclass->input['itemDiscount'][$m]<1 OR $this->kclass->input['itemDiscount'][$m]>100){
					$this->kclass->input['itemDiscount'][$m]=100;
				}
				$amount=$this->kclass->input['itemQuantity'][$m]*$this->kclass->input['itemPrice'][$m];
				if($this->kclass->input['itemDiscount'][$m]<100){
					$amount=$amount*$this->kclass->input['itemDiscount'][$m]/100;
				}
				$amount=number_format($amount,4,'.','');
				$this->kclass->input['itemBrandid'][$m]=$this->kclass->iif($this->kclass->input['itemBrandid'][$m]>0,$this->kclass->input['itemBrandid'][$m],0);
				$material=$this->kclass->DB->queryFirst("SELECT unitid FROM material WHERE killed=0 AND materialid='".$this->kclass->input['itemMaterialid'][$m]."'");
				if($this->kclass->input['itemDateline'][$m]){
					$sd=explode('-',$this->kclass->input['itemDateline'][$m]);
					$dateline=mktime(0,0,0,$sd[1],$sd[2],$sd[0]);
				}else{
					$dateline=0;
				}
				//insert item
				$this->kclass->DB->query("
					INSERT INTO item (module,mid,materialid,quantity,brandid,unitid,package,price,amount,dateline,remark,created,creator)
					VALUES ('porder','".$porderid."','".$this->kclass->input['itemMaterialid'][$m]."','".$this->kclass->input['itemQuantity'][$m]."','".$this->kclass->input['itemBrandid'][$m]."','".$material['unitid']."','".$this->kclass->input['itemPackage'][$m]."','".$this->kclass->input['itemPrice'][$m]."','".$amount."','".$dateline."','".$this->kclass->input['itemRemark'][$m]."','".TIMENOW."','".$this->kclass->user['userid']."')
				");
				$this->kclass->updateFee(array('module'=>'porder','mid'=>$porderid));
			}
		}
		$this->kclass->updateAttachs(array('module'=>'porder', 'mid'=>$porderid));
		$this->kclass->messager(array(
			'title' => '新建订单',
			'text' => '订单 <b>'.$orderno.'</b> 已新建成功!返回订单详情页',
			'url' => '/s.php?module=porder&action=view&porderid='.$porderid,
			'sec' => 2
		));
	}
	//
	function updateOld(){
		$order=$this->kclass->DB->queryFirst("
			SELECT o.porderid,o.customerid,o.supplierid,o.currencyid,o.purchaseType,o.loading,o.discharge,
					o.shipmethodid,o.paymenttermid,o.deliverytermid,o.orderno,o.type,o.taxRate,o.pinvoiceTypeid,
					o.customerNo,o.supplierNo,o.shipmentDate,o.paymentDate,o.delivery,o.consignee,o.consigneeTel,o.spare,o.remark,o.created,
				od.routeid,od.title AS route,od.caption,
				s.title AS supplier,s.supplierno,
				u.username AS creator,
				us.username AS modifier
			FROM `porder` AS o
			LEFT JOIN `orderroute` AS od ON (od.routeid=o.routeid)
			LEFT JOIN `supplier` AS s ON (s.supplierid=o.supplierid)
			LEFT JOIN `user` AS u ON (u.userid=o.creator)
			LEFT JOIN `user` AS us ON (us.userid=o.modifier)
			WHERE o.killed=0 AND o.routeid=4 AND o.type='PO' AND o.porderid='".$this->kclass->input['porderid']."'
			LIMIT 0,1
		");
		if($order){
			if($this->kclass->input['step']=='item'){
				$module='porder';
				$itemtr='<table class="hundred small"><thead><tr><th colspan="13">订单明细列表</th></tr></thead><tbody><tr class="center"><td width="15">ID</td><td>物资</td><td>品牌</td><td align="right">数量 <span class="red bold">*</span></td><td>包装方式</td><td>单价 <span class="red bold">*</span></td><td>折扣</td><td>到货时间 <span class="red bold">*</span></td><td>备注</td><td>删</td></tr>';
				$items=$this->kclass->DB->query("
					SELECT i.itemid,i.brandid,i.quantity,i.purchaseid,i.materialid,i.unitid,i.package,i.price,i.discount,i.dateline,i.requirement,i.remark,
						pu.purchaseno,
						b.brandid,b.title AS brand,
						m.materialno,m.title AS material,m.standard,
						u.title AS unit
					FROM item AS i
					LEFT JOIN purchase AS pu ON (pu.purchaseid=i.purchaseid)
					LEFT JOIN `brand` AS b ON (b.brandid=i.brandid)
					LEFT JOIN `material` AS m ON (m.materialid=i.materialid)
					LEFT JOIN `unit` AS u ON (u.unitid=i.unitid)
					WHERE i.killed=0 AND i.module='porder' AND i.mid='".$order['porderid']."'
					ORDER BY itemid ASC
				");
				$k=1;
				if($this->kclass->DB->numRows()){
					$this->kclass->tbline+=1;
					while($item=$this->kclass->DB->fetchArray($items)){
						$itemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').' small"><input type="hidden" name="itemId['.$k.']" value="'.$item['itemid'].'">
							<td>'.$k.'</td>
							<td><input type="hidden" name="itemMaterialid['.$k.']" value="'.$item['materialid'].'"><a href="/s.php?module=material&action=view&materialid='.$item['materialid'].'">'.$item['materialno'].'　'.$item['material'].'　'.$item['standard'].'</a></td>
							<td>'.$this->kclass->chooserBrand(array('name'=>'itemBrandid['.$k.']','hasBlank'=>1,'topname'=>'请选择','selectedid'=>$item['brandid'],'width'=>'100')).'</td>
							<td><input type="text" name="itemQuantity['.$k.']" size="6" value="'.$item['quantity'].'" /> '.$item['unit'].'</td>
							<td><input type="text" name="itemPackage['.$k.']" value="'.$item['package'].'"  style="width:100px;" /></td>
							<td><input type="text" name="itemPrice['.$k.']" size="5" value="'.$item['price'].'" /></td>
							<td><input type="text" name="itemDiscount['.$k.']" value="'.$item['discount'].'" size="3"></td>
							<td><input type="text" name="itemDateline['.$k.']" id="itemDateline'.$k.'" value="'.date('Y-m-d',$item['dateline']).'" size="10"></td>
							<td><input type="text" name="itemRemark['.$k.']" style="width:130px" value="'.$item['remark'].'"></td>
							<td><input type="checkbox" name="itemKill['.$k.']" value="'.$item['itemid'].'"/></td>
						</tr>';
						$id.='#itemDateline'.$k.',';
						$k+=1;
					}
					$itemtr.='<tr><td colspan="13" class="bold">新建明细</td></tr>';
				}
				$itemtr.='<tbody><tr class="center"><td width="15">ID</td><td>选择物资 <span class="red bold">*</span><span class="small gray">（输入物资编号自动出现列表）</span></td><td>品牌</td><td>数量 <span class="red bold">*</span></td><td>包装方式</td><td>单价 <span class="red bold">*</span></td><td>折扣</td><td>到货日期 <span class="red bold">*</span></td><td>备注</td><td>选</td></tr>';
				for($i=$k;$i<$k+5;$i++){
					$itemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').' small">
						<td>'.$i.'</td>
						<td>'.$this->kclass->chooserMaterial(array('hName'=>'itemMaterialid['.$i.']','hId'=>'itemMaterialid'.$i,'name'=>'itemMaterial['.$i.']','id'=>'itemMaterial'.$i,'width'=>400,'line'=>$i)).'</td>
						<td>'.$this->kclass->chooserBrand(array('name'=>'itemBrandid['.$i.']','hasBlank'=>1,'topname'=>'请选择','width'=>'100')).'</td>
						<td><input type="text" name="itemQuantity['.$i.']" size="10"></td>
						<td><input type="text" name="itemPackage['.$i.']" style="width:100px;"></td>
						<td><input type="text" name="itemPrice['.$i.']" size="5"></td>
						<td><input type="text" name="itemDiscount['.$i.']" value="100" size="3"></td>
						<td><input type="text" name="itemDateline['.$i.']" id="dateline'.$i.'" size="10"></td>
						<td><input type="text" name="itemRemark['.$i.']" style="width:130px"></td>
						<td><input type="checkbox" name="itemSelect['.$i.']"/>
					</tr>';
					if($i<5){
						$id.='#dateline'.$i.',';
					}else{
						$id.='#dateline'.$i;
					}
				}
				$itemtr.='<tr class="even"><td class="small gray" colspan="15">注意事项：<br>“到货日期”的格式为2012-02-06，中间用半角短横杠隔开；<br></tr></tbody></table>';
				$body=<<<EOF
<form action="/s.php?module=porder&action=doupdateOld" name="porder" method="post">
<input type="hidden" name="module" value="porder">
<input type="hidden" name="action" value="doupdateOld">
<input type="hidden" name="step" value="item">
<input type="hidden" name="purchaseType" value="{$order['purchaseType']}">
<input type="hidden" name="porderid" value="{$order['porderid']}">
<input type="hidden" name="supplierid" value="{$order['supplierid']}">
{$itemtr}
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
		}else{
			$body = '系统数据出错，很抱歉！';
		}
		$this->kclass->page['title'] .= $order['orderno'].' - 修改订单';
		$this->kclass->page['onload'].='dc.tabhover();$(\''.$id.',#paymentDate\').datepicker()';
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - <a href="/s.php?module=porder&action=view&porderid='.$order['porderid'].'">查看订单</a> - 修改订单', 'right'=>'<a href="/s.php?module=porder&action=view&porderid='.$order['porderid'].'">查看订单</a>　｜　<a href="/s.php?module=porder&action=list">返回列表</a>　','body'=>$body));
	}
	//
	function doupdateOld(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e='<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			if($this->kclass->input['step']=='item'){
				$j=count($this->kclass->input['itemId']);
				$hasItem=0;
				for($i=1;$i<$j+1;$i++){
					if($this->kclass->input['itemKill'][$i]=='' AND $this->kclass->input['itemQuantity'][$i]!='' AND $this->kclass->input['itemPrice'][$i]!='' AND $this->kclass->input['itemDateline'][$i]!=''){
						$hasItem=1;
					}
				}
				if($hasItem==0){
					$e.='<li>请填写完整至少一个订单明细</li>';
				}
			}
		}
		if($e){
			$this->kclass->messager(array(
				'title' => '修改订单',
				'text' => '您在修改订单的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$porder=$this->kclass->DB->queryFirst("SELECT ifVerify FROM porder WHERE killed=0 AND porderid='".$this->kclass->input['porderid']."'");
		$count=count($this->kclass->input['itemMaterialid']);
		for($m=1;$m<$count+1;$m++){
			if($this->kclass->input['itemDateline'][$m]!=''){
				$sd=explode('-',$this->kclass->input['itemDateline'][$m]);
				$dateline=mktime(0,0,0,$sd[1],$sd[2],$sd[0]);
			}else{
				$dateline=0;
			}
			if($this->kclass->input['itemId'][$m]>0){
				if($this->kclass->input['itemKill'][$m]!='' AND $this->kclass->input['itemKill'][$m]==$this->kclass->input['itemId'][$m]){
					$this->kclass->DB->query("UPDATE item SET killed='".TIMENOW."',killer='".$this->kclass->user['userid']."' WHERE itemid='".$this->kclass->input['itemId']."'");
				}else{
					if($this->kclass->input['itemQuantity'][$m]!='' AND $this->kclass->input['itemPrice'][$m]!='' AND $this->kclass->input['itemDateline'][$m]!=''){
						$this->kclass->input['itemQuantity'][$m]=intVal($this->kclass->input['itemQuantity'][$m]);
						$this->kclass->input['itemPrice'][$m]=floatVal($this->kclass->input['itemPrice'][$m]);
						$this->kclass->input['itemDiscount'][$m]=intVal($this->kclass->input['itemDiscount'][$m]);
						if($this->kclass->input['itemDiscount'][$m]<1 OR $this->kclass->input['itemDiscount'][$m]>100){
							$this->kclass->input['itemDiscount'][$m]=100;
						}
						$amount=$this->kclass->input['itemQuantity'][$m]*$this->kclass->input['itemPrice'][$m];
						if($this->kclass->input['itemDiscount'][$m]<100){
							$amount=$amount*$this->kclass->input['itemDiscount'][$m]/100;
						}
						$amount=number_format($amount,2,'.','');
						$this->kclass->DB->query("
							UPDATE `item` SET
								brandid='".$this->kclass->input['itemBrandid'][$m]."',
								quantity='".$this->kclass->input['itemQuantity'][$m]."',
								package='".$this->kclass->input['itemPackage'][$m]."',
								price='".$this->kclass->input['itemPrice'][$m]."',
								discount='".$this->kclass->input['itemDiscount'][$m]."',
								dateline='".$dateline."',
								amount='".$amount."',
								remark='".$this->kclass->input['itemRemark'][$m]."',
								modified='".TIMENOW."',
								modifier='".$this->kclass->user['userid']."'
							WHERE killed=0 AND itemid='".$this->kclass->input['itemId'][$m]."'
						");
					}
				}
			}else{
				if($this->kclass->input['itemMaterialid'][$m]>0 AND $this->kclass->input['itemSelect'][$m]!='' AND $this->kclass->input['itemQuantity'][$m]!='' AND $this->kclass->input['itemPrice'][$m]!='' AND $this->kclass->input['itemDateline'][$m]!=''){
					$material=$this->kclass->DB->queryFirst("SELECT unitid FROM material WHERE materialid='".$this->kclass->input['itemMaterialid'][$m]."'");
					$this->kclass->input['itemQuantity'][$m]=intVal($this->kclass->input['itemQuantity'][$m]);
					$this->kclass->input['itemPrice'][$m]=floatVal($this->kclass->input['itemPrice'][$m]);
					$this->kclass->input['itemDiscount'][$m]=intVal($this->kclass->input['itemDiscount'][$m]);
					if($this->kclass->input['itemDiscount'][$m]<1 OR $this->kclass->input['itemDiscount'][$m]>100){
						$this->kclass->input['itemDiscount'][$m]=100;
					}
					$amount=$this->kclass->input['itemQuantity'][$m]*$this->kclass->input['itemPrice'][$m];
					if($this->kclass->input['itemDiscount'][$m]<100){
						$amount=$amount*$this->kclass->input['itemDiscount'][$m]/100;
					}
					$amount=number_format($amount,4,'.','');
					$this->kclass->DB->query("
						INSERT INTO `item`
							(module,mid,materialid,quantity,brandid,unitid,package,price,discount,amount,dateline,remark,created,creator)
						VALUES
							('porder','".$this->kclass->input['porderid']."','".$this->kclass->input['itemMaterialid'][$m]."','".$this->kclass->input['itemQuantity'][$m]."','".$this->kclass->input['itemBrandid'][$m]."','".$material['unitid']."','".$this->kclass->input['itemPackage'][$m]."','".$this->kclass->input['itemPrice'][$m]."','".$this->kclass->input['itemDiscount'][$m]."','".$amount."','".$dateline."','".$this->kclass->input['itemRemark'][$m]."','".TIMENOW."','".$this->kclass->user['userid']."')
					");
				}
			}
		}
		if($porder['ifVerify']==-1){
			$this->kclass->DB->query("
				UPDATE porder SET
					ifVerify=0,
					verifier=0,
					verified=0,
					verifyRemark=''
				WHERE porderid='".$this->kclass->input['porderid']."'
			");
		}
		$this->kclass->messager(array(
			'title' => '修改订单明细',
			'text' => '订单 <b>'.$orderno.'</b> 明细已修改成功!返回订单详情页',
			'url' => '/s.php?module=porder&action=view&porderid='.$this->kclass->input['porderid'],
			'sec' => 2
		));
	}
/**********************************private function*****************************************************/
	// view body of the order
	function _view($orderid){
		$order=$this->kclass->DB->queryFirst("
			SELECT o.porderid,o.supplierid,o.customerNo,o.supplierNo,o.orderno,o.type,o.purchaseType,o.shipmentDate,o.paymentDate,o.delivery,o.orderStatus,
					o.taxRate,o.consignee,o.consigneeTel,o.special,o.spare,o.verifier,o.ifVerify,o.verified,o.verifyRemark,o.approver,o.ifApprove,o.approved,
					o.approvalRemark,o.remark,o.modified,o.created,o.killed,o.payStatus,o.arrivalStatus,
				od.routeid,od.title AS route,od.caption,
				it.title AS invoiceType,
				s.title AS supplier,s.supplierno,s.address AS supplierAddress,s.linkman AS supplierLinkman,s.mobile AS supplierMobile,
					s.telephone AS supplierTelephone,s.email AS supplierEmail,s.fax AS supplierFax,
				cu.cntitle AS customer,cu.customerno,cu.cnaddress AS customerAddress,cu.linkman AS customerLinkman,cu.mobile AS customerMobile,
					cu.telephone AS customerTelephone,cu.email AS customerEmail,cu.fax AS customerFax,
				c.title AS currency,c.symbol AS encurrency,
				p.title AS paymentterm,p.entitle AS enpaymentterm,
				d.title AS deliveryterm,d.entitle AS endeliveryterm,
				u.username AS creator,
				us.username AS modifier,
				ur.username AS overifier,
				usr.username AS oapprover
			FROM `porder` AS o
			LEFT JOIN `orderroute` AS od ON (od.routeid=o.routeid)
			LEFT JOIN `pinvoicetype` AS it ON (it.pinvoiceTypeid=o.pinvoiceTypeid)
			LEFT JOIN `supplier` AS s ON (s.supplierid=o.supplierid)
			LEFT JOIN `customer` AS cu ON (cu.customerid=o.customerid)
			LEFT JOIN `currency` AS c ON (c.currencyid=o.currencyid)
			LEFT JOIN `paymentterm` AS p ON (p.paymenttermid=o.paymenttermid)
			LEFT JOIN `deliveryterm` AS d ON (d.deliverytermid=o.deliverytermid)
			LEFT JOIN `user` AS u ON (u.userid=o.creator)
			LEFT JOIN `user` AS us ON (us.userid=o.modifier)
			LEFT JOIN `user` AS ur ON (ur.userid=o.verifier)
			LEFT JOIN `user` AS usr ON (usr.userid=o.approver)
			WHERE o.routeid=4 AND o.type='PO' AND o.porderid='".$orderid."'
			LIMIT 0,1
		");
		$order['created']=date('Y-m-d H:i',$order['created']);
		if($order['modified']!=0)$modify='，由'.$order['modifier'].'于'.date('Y-m-d H:i',$order['modified']).'修改';
		if($order['verified']!=0){
			$overify.='，由'.$order['overifier'].'于'.date('Y-m-d H:i:s',$order['verified']).'审核';
			$remark.='<tr class="even"><td>审核备注：</td><td>'.$order['verifyRemark'].'</td><td></td><td></td></tr>';
		}
		if($order['approved']!=0){
			$overify.='，由'.$order['oapprover'].'于'.date('Y-m-d H:i:s',$order['approved']).'审批';
			$remark.='<tr class="even"><td>审批备注：</td><td>'.$order['approvalRemark'].'</td><td></td><td></td></tr>';
		}

		$order['shipmentDate']=date('Y-m-d',$order['shipmentDate']);
		$order['paymentDate']=date('Y-m-d',$order['paymentDate']);
		foreach($this->purchaseType AS $key=>$val){
			if($order['purchaseType']==$val['entitle']){
				$purchaseType=$val['title'];
			}
		}
		$verifier=$this->kclass->getRealname($order['verifier']);
		$approver=$this->kclass->getRealname($order['approver']);
		if($order['verified']!=0){
			$remark='<tr class="even"><td>审 核 人：</td><td>'.$verifier.'</td><td>审核备注：</td><td>'.$order['verifyRemark'].'</td>';
		}
		if($order['approved']!=0){
			$remark.='<tr class="odd"><td>审 批 人：</td><td>'.$approver.'</td><td>审批备注：</td><td>'.$order['approvalRemark'].'</td></tr>';
		}
		$verify=$this->kclass->verify($order['ifVerify']);
		$approve=$this->kclass->approve($order['ifApprove']);
		if($order['arrivalStatus']==0){
			$arrivalStatus='未到货';
		}elseif($order['arrivalStatus']==1){
			$arrivalStatus='部分到货';
		}if($order['arrivalStatus']==2){
			$arrivalStatus='已到货';
		}
		if($order['payStatus']==0){
			$payStatus='未付款';
		}elseif($order['payStatus']==1){
			$payStatus='部分付款';
		}if($order['payStatus']==2){
			$payStatus='已付款';
		}
		$orderStatus=$this->kclass->iif($order['orderStatus']==1,' , 已完成','').$this->kclass->iif($order['orderStatus']==-1,' , 已取消','');
		$order['delivery']=str_replace('{CONSIGNEE}','<span class="underline">'.$order['consignee'].'　电话： '.$order['consigneeTel'].'</span>',$order['delivery']);
		$order['remark']=str_replace('{PAYMENT}','<span class="undeline">'.$order['paymentterm'].'</span>',$order['remark']);
		$order['remark']=str_replace('{SQARE}','<span class="undeline">'.$order['spare'].' %</span>',$order['remark']);
		$order['taxRate']=$order['taxRate'].'%';
		$item=$this->kclass->relatedPOrderItem(array('porderid'=>$order['porderid'],'purchaseType'=>$order['purchaseType']));
		$revise=$this->kclass->relatedRevise(array('module'=>'porder','mid'=>$order['porderid']));
		$fee=$this->kclass->relatedFee(array('porderid'=>$order['porderid']));
		$attach=$this->kclass->getAttachs(array('module'=>'porder','mid'=>$order['porderid']));
		if($attach!= false){
			$attachs = '<div class="clear">'.$attach.'</div>';
		}
		$order['remark']=$this->kclass->parseConvertValue($order['remark']);
		$order['delivery']=$this->kclass->parseConvertValue($order['delivery']);
		// 订单数据序列号
		$r['arr']=serialize(array_merge($order,$item['array'],$fee['array']));
		if($order['killed']>0){
			$link='<a href="/s.php?module=porder&action=restore&porderid='.$order['porderid'].'&rt=list">恢复</a>';
		}else{
			if($order['ifApprove']==1){
				if($order['orderStatus']==0){
					$link='<span class="small">打印：</span>
						<a href="/p.php?action=porder&porderid='.$order['porderid'].'" target="_blank">订单</a> <a href="/p.php?action=quoting&porderid='.$order['porderid'].'" target="_blank">报价表</a>　|　<span class="small">新建：</span>
						<a href="/s.php?module=porder&action=add">订单</a>
						<a href="/s.php?module=inbound&action=add&typeid=4&porderid='.$order['porderid'].'">入库单</a>
						<a href="/s.php?module=pinvoice&action=add&porderid='.$order['porderid'].'&step=2&supplierid='.$order['supplierid'].'">发票</a>　|　<span class="small">操作：</span> <a href="/s.php?module=porder&action=orderStatus&porderid='.$order['porderid'].'">完成/取消</a>';
				}
			}elseif($order['ifApprove']==0){
				if($order['ifVerify']==1){
					$link='<span class="small">操作：</span><a href="/s.php?module=porder&action=approve&porderid='.$order['porderid'].'">审批</a>';
				}elseif($order['ifVerify']==0){
					$link='<span class="small">操作：</span><a href="/s.php?module=porder&action=verify&porderid='.$order['porderid'].'">审核</a> <a href="/s.php?module=porder&action=kill&porderid='.$order['porderid'].'&rt=view">删除</a>';
				}elseif($order['ifVerify']==-1){
					$link='<span class="small">操作：</span><a href="/s.php?module=porder&action=kill&porderid='.$order['porderid'].'&rt=list">删除</a>';
				}
			}
			$link.=$this->kclass->iif($order['payStatus']==2 AND $order['arrivalStatus']==2 AND $order['orderStatus']==2,'','　|　<a href="/s.php?module=porder&action=update&porderid='.$order['porderid'].'&rt=list">修改</a>');
		}
$r['html']=<<<EOF
<div><span class="small gray">当前订单状态：{$verify} , {$approve} , {$arrivalStatus} , {$payStatus}<span class="red">{$orderStatus}</span>　　　　<span class="right normal">{$link}</div>
<div class="bold darkred big">{$order['orderno']}{$revise['currencyRevise']}<span class="gray small right">由 {$order['creator']} 于 {$order['created']} 建立{$modify}{$overify}</span></div>
<table class="hundred">
	<thead><tr><th colspan="4">基本信息</th></tr></thead>
	<tbody>
<tr class="odd">
	<td width="100">订单编号：</td><td width="650">{$order['orderno']}</td>
	<td width="100">采购类型：</td><td>{$purchaseType}</td>
</tr>
{$remark}
<tr><td colspan=2 class="center bold">订货商信息（甲方）</td><td colspan=2 class="center bold">供应商信息（乙方）</td></tr>
<tr class="odd">
	<td>订货单位：</td><td>{$order['customer']} ( {$order['customerno']} )</td>
	<td>供货单位：</td><td><a href="/s.php?module=psupplier&action=view&supplierid={$order['supplierid']}">{$order['supplier']}</a> ( {$order['supplierno']} )</td>
</tr>
<tr class="even">
	<td>联 系 人：</td><td>{$order['customerLinkman']}</td>
	<td>联 系 人：</td><td>{$order['supplierLinkman']}</td>
</tr>
<tr class="odd">
	<td>公司地址：</td><td>{$order['customerAddress']}</td>
	<td>公司地址：</td><td>{$order['supplierAddress']}</td>
</tr>
<tr class="even">
	<td>手　　机：</td><td>{$order['customerMobile']}</td>
	<td>手　　机：</td><td>{$order['supplierMobile']}</td>
</tr>
<tr class="odd">
	<td>电　　话：</td><td>{$order['customerTelephone']}</td>
	<td>电　　话：</td><td>{$order['supplierTelephone']}</td>
</tr>
<tr class="even">
	<td>邮　　箱：</td><td>{$order['customerEmail']}</td>
	<td>邮　　箱：</td><td>{$order['supplierEmail']}</td>
</tr>
<tr class="odd">
	<td>传　　真：</td><td>{$order['customerFax']}</td>
	<td>传　　真：</td><td>{$order['supplierFax']}</td>
</tr>
</tbody>
</table>
<table class="hundred">
<thead></tr><th colspan="4">条款</th></tr></thead>
<tbody>
<tr class="even">
	<td>发票类型：</td><td>{$order['invoiceType']}</td>
	<td>税　　率：</td><td>{$order['taxRate']}</td>
</tr>
<tr class="odd">
	<td width="100">交货方式：</td><td width="450">{$order['deliveryterm']} </td>
	<td width="100">付款方式：</td><td>{$order['paymentterm']}</td>
</tr>
<tr class="even">
	<td>币　　种：</td><td>{$order['currency']} <span class="gray">{$order['symbol']}</span></td>
EOF;
	$r['html'].=$this->kclass->iif($order['purchaseType']=='entrust','<td></td><td></td></tr>','<td></td><td></td>');
$r['html'].=<<<EOF
</tr>
<tr class="odd">
<td>收货联系人：</td><td>{$order['consignee']}　电话：{$order['consigneeTel']}</td>
<td>提供备品率：</td><td>{$order['spare']} %</td>
</tr>
<tr class="even">
	<td>产品规格及发货补充说明：</td><td>{$order['delivery']}</td>
	<td>备　　注：</td><td>{$order['remark']}</td>
</tr>
<tr class="odd">
	<td>产品要求：</td><td>{$order['special']}</td>
	<td></td><td></td>
</tr>
</table>
{$item['tr']}
<table class="hundred">
<thead>
<tr>
	<th colspan="12">费用项目明细 (Fee Information)</th>
</tr>
</thead>
<tbody>
{$fee['tr']}
</tbody>
</table>
{$revise['panel']}
{$attachs}
EOF;
		return $r;
	}
}

?>