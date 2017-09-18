<?php
//
class pinvoice{
	var $kclass;
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
			default:
				return $this->mmlist();
		}
	}
	//
	function mmlist(){
		$condition=$this->kclass->iif($this->kclass->input['show']=='all','1=1','`pinvoice`.killed=0');
		$keywords=$this->kclass->iif($this->kclass->input['keywords']=='',' AND 1=1',' AND (`pinvoice`.invoiceno like \'%'.$this->kclass->input['keywords'].'%\' OR `pinvoice`.title like \'%'.$this->kclass->input['keywords'].'%\' OR `supplier`.title like \'%'.$this->kclass->input['keywords'].'%\')');
		if($this->kclass->input['orderby']!='')$query['orderby']=$this->kclass->input['orderby'];
		if($this->kclass->input['direction']!='')$query['direction']=$this->kclass->input['direction'];
		if($this->kclass->input['show']!='')$query['show']=$this->kclass->input['show'];
		if($this->kclass->input['layout']!='')$query['layout']=$this->kclass->input['layout'];
		$orderby=$this->kclass->orderby(array('module'=>'pinvoice','direction'=>'asc','orderby'=>'modified', 'default'=>'modified', 'serial'=>array(array('title'=>'编号', 'field'=>'orderno','word'=>'no'), array('title'=>'修改时间', 'field'=>'modified'), array('title'=>'建立时间', 'field'=>'created')),'appendUrl'=>$query));
		$invoices=$this->kclass->DB->query("
			SELECT `pinvoice`.pinvoiceid,`pinvoice`.title,`pinvoice`.invoiceno,`pinvoice`.pinvoiceTypeid,`pinvoice`.taxRate,`pinvoice`.amount,
					`pinvoice`.dateOfArrival,`pinvoice`.created,`pinvoice`.modified,`pinvoice`.killed,
				`supplier`.supplierid,`supplier`.title AS supplier,
				`pinvoicetype`.title AS pinvoiceType,
				`currency`.title AS currency,
				u.username AS creator,
				us.username AS modifier
			FROM `pinvoice`
			LEFT JOIN `supplier` ON (`supplier`.supplierid=`pinvoice`.supplierid)
			LEFT JOIN `pinvoicetype` ON (`pinvoicetype`.pinvoiceTypeid=`pinvoice`.pinvoiceTypeid)
			LEFT JOIN `currency` ON (`currency`.currencyid=`pinvoice`.currencyid)
			LEFT JOIN `user` AS u ON (u.userid=`pinvoice`.creator)
			LEFT JOIN `user` AS us ON (us.userid=`pinvoice`.modifier)
			WHERE ".$condition.$keywords."
			ORDER BY ".$orderby['sql']."
		");
		if($this->kclass->DB->numRows()){
			if($this->kclass->input['layout']=='grid'){
				$body='<ul id="mmlist" class="mmlist clear">';
			}else{
				$body='<table class="hundred"><thead><th width="15">ID</th><th width="30">发票号</th><th width="200">供应商</th><th width="150">标题</th><th width="150">发票类型及税率</th><th>币种</th><th>总额</th><th>建立时间</th><th></th></thead><tbody>';
			}
			$i=1;
			while($invoice=$this->kclass->DB->fetchArray($invoices)){
				$invoice['dateOfArrival']=date('Y-m-d',$invoice['dateOfArrival']);
				$invoice['created']=date('Y-m-d',$invoice['created']);
				$invoice['supplier']=$this->kclass->iif(strlen($invoice['supplier'])>30,mb_substr($invoice['supplier'],0,30,'UTF-8').'...',$invoice['supplier']);
				if($invoice['killed']>0){
					$link='<a href="/s.php?module=pinvoice&action=restore&pinvoiceid='.$invoice['pinvoiceid'].'&rt=list">恢复</a>';
				}else{
					$link=$this->kclass->iif($order['ifVerify']==0,'<a href="/s.php?module=pinvoice&action=update&pinvoiceid='.$invoice['pinvoiceid'].'&rt=list">改</a>','').' <a href="/p.php?action=pinvoice&pinvoiceid='.$invoice['pinvoiceid'].'" target="_blank">印</a>'.' <a href="/s.php?module=pinvoice&action=remove&pinvoiceid='.$invoice['pinvoiceid'].'" target="_blank">删</a>';
				}
				if($this->kclass->input['layout']=='grid'){//表格
					$body.='<li title="由 '.$invoice['creator'].' 建于 '.$invoice['created'].$this->kclass->iif($invoice['modifier']!='','，'.$invoice['modifier'].' 改于 '.$modified, '').'"'.$this->kclass->iif($i%4==0,' class="end"','').'>
					<div class="mmlistt">
						<span class="right normal">';
					if($invoice['killed']>0){
						$body.='<a href="/s.php?module=pinvoice&action=revival&pinvoiceid='.$invoice['pinvoiceid'].'&rt=list" onclick="return confirm(\'你确定要恢复这个发票 '.$invoice['invoiceno'].' 吗？\');">恢复</a>';
					}else{
						$body.='<a href="/s.php?module=pinvoice&action=kill&pinvoiceid='.$invoice['pinvoiceid'].'&rt=list" onclick="return confirm(\'你确定要删除这个发票 '.$invoice['invoiceno'].' 吗？\');">删</a> <a href="/s.php?module=pinvoice&action=update&pinvoiceid='.$invoice['pinvoiceid'].'&rt=list">改</a>';
					}
					$body.='</span>
						<span class="small">'.$invoice['attr'].'</span> <a href="/s.php?module=pinvoice&action=view&pinvoiceid='.$invoice['pinvoiceid'].'">'.$invoice['invoiceno'].'</a><br><span class="small gray right">'.$invoice['created'].'</span>'.$this->kclass->iif($invoice['attachs']>0, ' <span class="attachFile" title="有'.$invoice['attachs'].'个附件。"></span> ', '').$this->kclass->iif($invoice['images']>0, ' <span class="attachImage" title="有'.$invoice['images'].'个图片。"></span> ', '').'<a href="/s.php?module=supplier&action=view&supplierid='.$invoice['supplierid'].'">'.$invoice['title'].'</a></div>
					<div class=mmlistb>
					<div title="'.$invoice['invoiceno'].'"><span class="small gray">供应商：<a href="/s.php?module=supplier&action=view&supplierid='.$invoice['supplierid'].'">'.$invoice['supplier'].'</a></span></div>
					<div><span class="small gray">到货日期：'.$invoice['dateOfArrival'].'</span></div>
					<div class="small clear"><span class=right title="由 '.$invoice['creator'].' 建于 '.$invoice['created'].'">由'.$invoice['creator'].'  建于 '.$invoice['created'].'</span></div></div></li>';
				}else{
					$body.='<tr class="'.$this->kclass->rotateLine().'">
						<td>'.$i.'</td>
						<td><a href="/s.php?module=pinvoice&action=view&pinvoiceid='.$invoice['pinvoiceid'].'">'.$invoice['invoiceno'].$this->kclass->iif($invoice['attachs']>0, ' <span class="attachFile" title="有'.$invoice['attachs'].'个附件。"></span> ', '').$this->kclass->iif($invoice['images']>0, ' <span class="attachImage" title="有'.$invoice['images'].'个图片。"></span> ', '').'</a></td>
						<td title="'.$invoice['supplier'].'"><a  class="small" href="/s.php?module=pinvoice&action=view&pinvoiceid='.$invoice['pinvoiceid'].'">'.$this->kclass->iif(strlen($invoice['supplier'])>30, mb_substr($invoice['supplier'], 0, 30, 'UTF-8').'...', $invoice['supplier']).'</a></td>
						<td title="'.$invoice['title'].'">'.$invoice['title'].'</td>
						<td>'.$this->kclass->iif($invoice['pinvoiceTypeid']>0,$invoice['pinvoiceType'],'无发票').'('.($invoice['taxRate']*100).'%)</td>
						<td title="'.$invoice['currency'].'">'.$invoice['currency'].'</td>
						<td align="right">'.$invoice['amount'].'</td>
						<td align="right">'.$invoice['created'].'</td>
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

		$this->kclass->page['title'].='发票列表';
		$this->kclass->page['onload'].='dc.listhover();';
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - 发票列表','right'=>'<span class="small">排序：</span>'.$orderby['link'].'|　<span class="small">显示方式：</span><a href="/s.php?module=pinvoice">默认列表</a>　<a href="/s.php?module=pinvoice&action=list&show=all">所有<span class="small gray">(包括删除)</span></a>　|　<a href="/s.php?module=pinvoice&action=add">新建</a>','body'=>$body));
	}
	//
	function find(){
		if($this->kclass->input['keywords']==''){
$body=<<<EOF
<form method="get" action="/s.php?module=pinvoice&action=list">
<input type="hidden" name="module" value="pinvoice">
<input type="hidden" name="action" value="list">
<table>
<thead><tr><th colspan=2>查找发票：</th></tr></thead>
<tbody>
<tr><td>请输入查找关键词：</td><td><input type="text" name="keywords" style="width:350px"></td></tr>
<tr><td colspan=2 class="small gray">注意：请输入要查找发票的编号、标题或供应商</td></tr>
<tr class="center"><td colspan=2><input type="submit" value=" 提交 "><input type="reset" value=" 重置 "></td></tr>
</tbody>
</table>
</form>
EOF;
		}
		$this->kclass->page['title'].='订单查找';
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - 发票查找','right' => '<a href="/s.php?module=pinvoice">返回列表</a>','body'=>$body));
	}
	//
	function view(){
		$invoice=$this->kclass->DB->queryFirst("
			SELECT i.pinvoiceid,i.supplierid,i.customerid,i.invoiceno,i.refno,i.title,i.invoiceCode,i.checkCode,i.dateOfArrival,
					i.finalDestination,i.amount,i.tax,i.businesstype,i.drawer,i.drawed,i.taxRate,i.recipient,i.remark,i.created,i.modified,
				s.title AS supplier,s.address AS supplierAddress,s.linkman AS supplierLinkman,s.mobile AS supplierMobile,s.taxpayerID AS supplierTaxpayerID,
					s.telephone AS supplierTelephone,s.email AS supplierEmail,s.fax AS supplierFax,
				cu.title AS customer,cu.address AS customerAddress,cu.linkman AS customerLinkman,cu.mobile AS customerMobile,cu.taxpayerID AS customerTaxpayerID,
					cu.telephone AS customerTelephone,cu.email AS customerEmail,cu.fax AS customerFax,
				it.title AS invoiceType,
				r.country,r.state,r.city,
				p.title AS paymentterm,p.entitle AS enpaymentterm,
				d.title AS deliveryterm,
				sm.title AS shipmethod,
				c.title AS currency,
				cbd.bank AS customerBank,cbd.bankAccountName AS customerBankAccountName,cbd.bankAccount AS customerBankAccount,
				sbd.bank AS supplierBank,sbd.bankAccountName AS supplierBankAccountName,sbd.bankAccount AS supplierBankAccount,
				u.username AS creator,
				us.username AS modifier
			FROM `pinvoice` AS i
			LEFT JOIN `supplier` AS s ON (s.supplierid=i.supplierid)
			LEFT JOIN `customer` AS cu ON (cu.customerid=i.customerid)
			LEFT JOIN `pinvoicetype` AS it ON (it.pinvoiceTypeid=i.pinvoiceTypeid)
			LEFT JOIN `region` AS r ON (r.regionid=i.regionid)
			LEFT JOIN `paymentterm` AS p ON (p.paymenttermid=i.paymenttermid)
			LEFT JOIN `deliveryterm` AS d ON (d.deliverytermid=i.deliverytermid)
			LEFT JOIN `shipmethod` AS sm ON (sm.shipmethodid=i.shipmethodid)
			LEFT JOIN `currency` AS c ON (c.currencyid=i.currencyid)
			LEFT JOIN `bankdetail` AS cbd ON (cbd.bankDetailid=i.customerBankDetailid)
			LEFT JOIN `bankdetail` AS sbd ON (sbd.bankDetailid=i.supplierBankDetailid)
			LEFT JOIN `user` AS u ON (u.userid=i.creator)
			LEFT JOIN `user` AS us ON (us.userid=i.modifier)
			WHERE i.killed=0 AND i.pinvoiceid='".$this->kclass->input['pinvoiceid']."'
			LIMIT 0,1
		");
		if($invoice){
			$invoice['created']=date('Y-m-d H:i',$invoice['created']);
			$invoice['drawed']=date('Y-m-d',$invoice['drawed']);
			$invoice['dateOfArrival']=date('Y-m-d',$invoice['dateOfArrival']);
			$invoice['taxRate']=$invoice['taxRate'].'%';
			$invoice['cnamount']=$this->kclass->iif($invoice['cnamount']=='','零圆整',$invoice['cnamount']);
			if($invoice['modified']!=0)$modify='，由'.$invoice['modifier'].'于'.date('Y-m-d H:i',$invoice['modified']).'修改';

			$items=$this->kclass->DB->query("
				SELECT i.itemid,i.materialid,i.discount,i.quantity,i.tax,i.price,i.amount,
					o.porderid,o.orderno,
					m.materialno,m.title,m.standard,
					u.title AS unit
				FROM item AS i
				LEFT JOIN `material` AS m ON (m.materialid=i.materialid)
				LEFT JOIN `porder` AS o ON (o.porderid=i.porderid)
				LEFT JOIN `unit` AS u ON (u.unitid=i.unitid)
				WHERE i.killed=0 AND i.module='pinvoice' AND i.mid='".$invoice['pinvoiceid']."'
				ORDER BY itemid
			");
			if($this->kclass->DB->numRows()){
					$i=1;
				while($item=$this->kclass->DB->fetchArray($items)){
					$itemList.='<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').'">
					<td>'.$i.'</td>
					<td><a href="/s.php?module=porder&action=view&porderid='.$item['porderid'].'" target="_blank">'.$item['orderno'].'</a></td>
					<td><a href="/s.php?module=material&action=view&materialid='.$item['materialid'].'" target="_blank">'.$item['materialno'].'</a>'.$item['title'].' '.$item['standard'].'</td>
					<td>'.$item['quantity'].'</td>
					<td>'.$item['unit'].'</td>
					<td>'.$item['price'].'</td>
					<td>'.$item['amount'].'</td></tr>';
					$i++;
				}
			}
			$cnamount=$this->kclass->numberToCNAccount($invoice['amount']);
			$order=$this->kclass->relatedPOrder(array('pinvoiceid'=>$this->kclass->input['pinvoiceid']));
			$supplier=$this->kclass->relatedSupplier(array('pinvoiceid'=>$this->kclass->input['pinvoiceid']));
$body=<<<EOF
<div class="title"><span class="right small gray">由{$invoice['creator']}于{$invoice['created']}建立{$modify}。 </span> {$invoice['invoiceno']}</div>
<dl id="invoice" class="tabs">
<dt tabid="0">采购发票</dt>
<dt title="与该发票有关的采购订单" {$order['off']}>采购订单{$order['count']}</dt>
<dd>
<div class="gray small"><span class="right normal"><a href="/s.php?module=pinvoice&action=add">新建发票</a>　|　<a href="/s.php?module=pinvoice&action=update&pinvoiceid={$invoice['pinvoiceid']}">修改</a>　|　<a href="" target="_blank">打印</a></span>由{$invoice['creator']}于{$invoice['created']}建立{$modify}。</div>
<table class="hundred">
<thead><tr><th colspan="4">基本信息</th></tr></thead>
<tbody>
<tr class="odd">
<td width="100">发票编号：</td><td width="700">{$invoice['invoiceno']}</td>
<td width="100">标　　题：</td><td>{$invoice['title']}</td>
</tr>
<tr class="even">
<td>发票代码：</td><td>{$invoice['invoiceCode']}</td>
<td>验 证 码：</td><td>{$invoice['checkCode']}</td>
</tr>
<tr class="odd">
<td>发票类型：</td><td>{$invoice['invoiceType']}</td>
<td>税　　率：</td><td>{$invoice['taxRate']}</td>
</tr>
<tr class="even">
<td>开票日期：</td><td>{$invoice['drawed']}</td>
<td>开 票 人：</td><td>{$invoice['drawer']}</td>
</tr>
<tr class="even">
<td>到票日期：</td><td>{$invoice['dateOfArrival']}</td>
<td>签 收 人：</td><td>{$invoice['recipient']}</td>
</tr>
<tr class="even">
<td>最终目的地：</td><td>{$invoice['finalDestination']}</td>
<td>行业分类：</td><td>{$invoice['businesstype']}</td>
</tr>
</tbody>
<thead>
<tr><th colspan=4 class="bold">客户及供应商信息</th></tr>
</thead>
<tr class="odd">
<td>客　　户：</td><td>{$invoice['customer']}</td>
<td>供 应 商：</td><td><a href="/s.php?module=supplier&action=view&supplierid={$invoice['supplierid']}">{$invoice['supplier']}</a></td>
</tr>
<tr class="even">
<td>公司地址：</td><td>{$invoice['customerAddress']}</td>
<td>公司地址：</td><td>{$invoice['supplierAddress']}</td>
</tr>
<tr class="odd">
<td>联 系 人：</td><td>{$invoice['customerLinkman']}</td>
<td>联 系 人：</td><td>{$invoice['supplierLinkman']}</td>
</tr>
<tr class="even">
<td>纳税人识别号：</td><td>{$invoice['customerTaxpayerID']}</td>
<td>纳税人识别号：</td><td>{$invoice['supplierTaxpayerID']}</td>
</tr>
<tr class="odd">
<td>手　　机：</td><td>{$invoice['customerMobile']}</td>
<td>手　　机：</td><td>{$invoice['supplierMobile']}</td>
</tr>
<tr class="even">
<td>电　　话：</td><td>{$invoice['customerTelephone']}</td>
<td>电　　话：</td><td>{$invoice['supplierTelephone']}</td>
</tr>
<tr class="odd">
<td>邮　　箱：</td><td>{$invoice['customerEmail']}</td>
<td>邮　　箱：</td><td>{$invoice['supplierEmail']}</td>
</tr>
<tr class="even">
<td>传　　真：</td><td>{$invoice['customerFax']}</span></td>
<td>传　　真：</td><td>{$invoice['supplierFax']}</td>
</tr>
<tr class="odd">
<td>户　　名：</td><td>{$invoice['customerBankAccountName']}</td>
<td>户　　名：</td><td>{$invoice['supplierBankAccountName']}</td>
</tr>
<tr class="even">
<td>银行账号：</td><td>{$invoice['customerBankAccount']}</td>
<td>银行账号：</td><td>{$invoice['supplierBankAccount']}</td>
</tr>
<tr class="odd">
<td>银行资料：</td><td>{$invoice['customerBank']}</td>
<td>银行资料：</td><td>{$invoice['supplierBank']}</td>
</tr>
</tbody>
<thead></tr><th colspan="4">条款</th></tr></thead>
<tbody>
<tr class="odd">
<td>交货方式：</td><td>{$invoice['deliveryterm']}</td>
<td>付款方式：</td><td>{$invoice['paymentterm']}({$invoice['enpaymentterm']})</td>
</tr>
<tr class="even">
<td>运输方式：</td><td>{$invoice['shipmethod']}</td>
<td>币　　种：</td><td>{$invoice['currency']}</td>
</tr>
<tr class="odd">
<td>地　　区：</td><td width="440">{$invoice['country']} {$invoice['state']} {$invoice['city']}</td>
<td></td><td></td>
</tr>
<tr class="even">
<td>备　　注：</td><td colspan=3>{$invoice['remark']}</td>
</tr>
</table>
<table class="hundred">
<thead><tr><th colspan="10"><span class="right"><a href="/s.php?module=pinvoice&action=update&pinvoiceid={$invoice['pinvoiceid']}&step=item">新增与修改订单条目</a></span>发票信息 (Order Information)</th></tr></thead>
<tbody>
<tr class="even center"><td width="20">ID</td><td width="50">订单号</td><td width="500">货物或应税劳务名称</td><td width="50">数量</td><td width="50">单位</td><td width="50">单价</td><td width="60">金额</td></tr>
</tbody>
<tbody class="small">
{$itemList}
<tr><td colspan=2 align="right">价税合计（大写）：</td><td colspan="2" class="middle bold">人民币：{$cnamount}</td><td>（小写）</td><td colspan="2" class="middle bold">{$invoice['amount']}</td></tr>
</tbody>
</table>
<dd>{$order['panel']}</dd>
EOF;
		}else{
			$body='数据错误，很抱歉';
		}

		$this->kclass->page['title'].='查看发票';
		$this->kclass->page['onload'].='dc.tabhover();dc.tabs({\'id\':\'invoice\'});dc.show()';
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - <a href="/s.php?module=pinvoice">发票列表</a> - 查看发票', 'right'=>'<a href="/s.php?module=pinvoice&action=list">返回列表</a>　|　<span class="small gray">操作：</span>'.$this->kclass->iif($b['killed']==1, '<a href="/s.php?module=pinvoice&action=restore&pinvoiceid='.$invoice['pinvoiceid'].'&rt=view">恢复发票</a>', '<a href="/s.php?module=pinvoice&action=remove&pinvoiceid='.$invoice['pinvoiceid'].'&rt=view">删除</a>　<a href="/s.php?module=pinvoice&action=update&pinvoiceid='.$invoice['pinvoiceid'].'&rt=view">修改</a>'),'body'=>$body));
	}
	//
	function add(){
		if($this->kclass->input['step'] == 2 AND !empty($this->kclass->input['porderid'])){
			$start=strtotime(date('Y-m-d',TIMENOW));
			$end=strtotime(date('Y-m-d',$start))+86400;
			$counter=$this->kclass->DB->queryFirst("SELECT COUNT(*) AS count FROM `pinvoice` WHERE created>='".$start."' AND created<='".$end."'");
			$invoiceno=$this->kclass->id(array('pinvoice'=>TIMENOW,'number'=>$counter['count']));
			foreach($this->kclass->input['porderid'] AS $k=>$v){
				$inputItem .= '<input type="hidden" name="porderid['.$k.']" value="'.$v.'" />';
				$order=$this->kclass->DB->queryFirst("
					SELECT o.porderid,o.routeid,o.supplierid,o.customerid,o.orderno,o.type,o.pinvoiceTypeid,o.shipmethodid,o.currencyid,o.paymenttermid,o.deliverytermid,o.taxRate,
							o.shipmentDate,o.delivery,o.taxRate,o.consignee,o.consigneeTel,o.special,o.spare,o.remark,o.created,
						s.title AS supplier,s.address AS supplierAddress,s.linkman AS supplierLinkman,s.mobile AS supplierMobile,
							s.telephone AS supplierTelephone,s.email AS supplierEmail,s.fax AS supplierFax,s.taxpayerID AS supplierTaxpayerID,
						c.title AS customer,c.address AS customerAddress,c.linkman AS customerLinkman,c.mobile AS customerMobile,
							c.telephone AS customerTelephone,c.email AS customerEmail,c.fax AS customerFax,c.taxpayerID AS customerTaxpayerID
					FROM `porder` AS o
					LEFT JOIN `supplier` AS s ON (s.supplierid=o.supplierid)
					LEFT JOIN `customer` AS c ON (c.customerid=o.customerid)
					WHERE o.killed=0 AND o.porderid='".$v."' 
					LIMIT 0,1
				");
				$this->kclass->br2nl+=2;
				$order['delivery']=$this->kclass->parseConvertValue($order['delivery']);
				$order['special']=$this->kclass->parseConvertValue($order['special']);
				$order['remark']=$this->kclass->parseConvertValue($order['remark']);
				$deliveryterm=$this->kclass->chooserDeliveryterm(array('name'=>'deliverytermid','width'=>200,'hasBlank'=>1,'topname'=>'请选择','selectedid'=>$order['deliverytermid']));
				$paymentterm=$this->kclass->chooserPaymentterm(array('name'=>'paymenttermid','width'=>200,'hasBlank'=>1,'topname'=>'请选择','selectedid'=>$order['paymenttermid'],'module'=>'produce'));
				$shipmethod=$this->kclass->chooserShipmethod(array('name'=>'shipmethodid','width'=>200,'hasBlank'=>1,'topname'=>'请选择','selectedid'=>$order['shipmethodid']));
				$currency=$this->kclass->chooserCurrency(array('name'=>'currencyid','width'=>100,'hasBlank'=>1,'topname'=>'请选择','selectedid'=>$order['currencyid']));
				$invoiceType=$this->kclass->chooserPInvoiceType(array('name'=>'pinvoiceTypeid','hasBlank'=>1,'topname'=>'请选择','selectedid'=>$order['pinvoiceTypeid']));
				$taxRate=$this->kclass->chooserTaxRate(array('name'=>'taxRate','selectedid'=>$order['taxRate']));
				$region=$this->kclass->chooserRegion(array('name'=>'regionid', 'hasBlank'=>1, 'width'=>200,));
				$order['shipmentDate']=date('Y-n-d',$order['shipmentDate']);

				//订单条目明细
				$items = $this->kclass->DB->query("
					SELECT `item`.*,
						material.materialno,material.title AS material,material.standard,
						brand.title AS brand,
						unit.title AS unit,
						`porder`.porderid,`porder`.orderno, `porder`.created AS orderCreated
					FROM `item`
					LEFT JOIN `brand` ON (`brand`.brandid=`item`.brandid)
					LEFT JOIN `material` ON (`material`.materialid=`item`.materialid)
					LEFT JOIN `unit` ON (`unit`.unitid=`item`.unitid)
					LEFT JOIN `porder` ON (`porder`.porderid=`item`.mid)
					WHERE `item`.killed=0 AND `item`.module='porder' AND `item`.mid='".$v."'
					ORDER BY `item`.ordering ASC, `item`.itemid ASC, `item`.modified ASC
				");
				if($this->kclass->DB->numRows()){
					$i=0;
					while($item = $this->kclass->DB->fetchArray($items)){
						$itemList .= '
						<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').'"><td>'.$i.'</td>
						<td title="创建时间：'.date('Y-m-d', $item['orderCreated']).'"><a href="/s.php?module=porder&action=view&porderid='.$item['porderid'].'" target="_blank">'.$item['orderno'].'</a></td>
						<td><input type="hidden" name="olItemid['.$k.']['.$i.']" value="'.$item['itemid'].'">'.$item['materialno'].' <a href="/s.php?module=material&action=view&materialid='.$item['materialid'].'" target="_blank">'.$item['material'].' '.$item['standard'].'</a></td>
						<td><input type="text" name="olQuantity['.$k.']['.$i.']" value="'.$item['quantity'].'" size="6"></td>
						<td>'.$this->kclass->chooserUnit(array('name'=>'olUnitid['.$k.']['.$i.']', 'hasBlank'=>1, 'width'=>100, 'selectedid'=>$item['unitid'])).'</td>
						<td><input type="text" name="olPrice['.$k.']['.$i.']" value="'.$item['price'].'" size="6"></td>
						<td><input type="text" name="olAmount['.$k.']['.$i.']" value="'.$item['amount'].'" size="10"></td>
						<td><input type="checkbox" name="olSelect['.$k.']['.$i.']" value="'.$item['itemid'].'" checked="true"></td>
						</tr>';
						$amount[$i] = $item['amount'];
						$i++;
					}
				}
				$totalAmount = array_sum($amount);
				$customerBankDetail=$this->kclass->chooserBankDetail(array('name'=>'customerBankDetailid','width'=>'440','customerid'=>$order['customerid']));
				$supplierBankDetail=$this->kclass->chooserBankDetail(array('name'=>'supplierBankDetailid','width'=>'440','supplierid'=>$order['supplierid']));
				$region=$this->kclass->chooserRegion(array('regionid'=>$customer['regionid'], 'hasBlank'=>1));
			}
			$upload=$this->kclass->upload(array('title'=>'相关附件：<span class="small darkred">请注意：发票里的附件不会出现在打印页面，仅在系统内部显示和存档。</span>'));

$body=<<<EOF
<form action="/s.php?module=pinvoice&action=insert" name="pinvoice" method="post">
<input type="hidden" name="module" value="pinvoice" />
<input type="hidden" name="action" value="insert" />
<input type="hidden" name="step" value="2" />
<input type="hidden" name="invoiceno" value="{$invoiceno}" />
{$inputItem}
<input type="hidden" name="supplierid" value="{$order['supplierid']}" />
<input type="hidden" name="customerid" value="{$order['customerid']}" />
<table class="hundred">
<thead></tr><th colspan="4">基本信息</th></tr></thead>
<tbody>
<tr class="odd">
<td width="100">发票编号：</td><td width="550"><span class="middle bold darkred">{$invoiceno}</span><span class="gray small">此为预估编号</span>　币种：<span class="red bold">*</span>{$currency}</td>
<td width="100">标　　题：</td><td><input type="text" name="title" style="width:450px;" /></td>
</tr>
<tr class="even">
<td>发票号码：<span class="red bold">*</span></td><td><input type="text" name="invoiceCode" style="width:450px;" /></td>
<td>发票金额：<span class="red bold">*</span></td><td><input type="text" name="amount" /><span class=""> (参考金额：{$totalAmount})</span></td>
</tr>
<tr class="odd">
<td>发票类型：<span class="red bold">*</span></td><td>{$invoiceType}</td>
<td>税　　率：<span class="red bold">*</span></td><td>{$taxRate}</td>
</tr>
<tr class="even">
<td>开票日期：<span class="red bold">*</span></td><td><input type="text" name="drawed" id="drawed" /><span class="small gray">日期格式如：2013-12-30</span></td>
<td>开 票 人：</td><td><input type="text" name="drawer" /></td>
</tr>
<tr class="odd">
<td>到票时间：</td><td><input type="text" name="dateOfArrival" id="dateOfArrival" /><span class="small gray">日期格式如：2013-12-30</span></td>
<td>签 收 人：</td><td><input type="text" name="recipient" /></td>
</tr>
<tr class="even">
<td>最终目的地：</td><td><input type="text" name="finalDestination" /></td>
<td>行业分类：</td><td><input type="text" name="businesstype" /></td>
</tr>
<tr>
</tbody>
<thead>
<tr>
<th colspan="4">供应商 <span class="bold darkred">{$order['supplier']}</span> 的信息：</th>
</tr>
</thead>
<tbody>
<tr class="even">
<td colspan=2 class="center bold">订货商信息</td>
<td colspan=2 class="center bold">供应商信息</td></tr>
<tr class="odd">
<td>客　　户：</td><td>{$order['customer']}</td>
<td>供 应 商：</td><td>{$order['supplier']}</td>
</tr>
<tr class="even">
<td>纳税人识别号：</td><td>{$order['customerTaxpayerID']}</td>
<td>纳税人识别号：</td><td>{$order['supplierTaxpayerID']}</td>
</tr>
<tr class="even">
<td>银行账户：</td><td>{$customerBankDetail}</td>
<td>银行账户：</td><td>{$supplierBankDetail}</td>
</tr>
</tbody>
<thead></tr><th colspan="4">条款</th></tr></thead>
<tbody>
<tr class="odd">
<td>交货方式：</td><td>{$deliveryterm}</td>
<td>付款方式：</td><td>{$paymentterm}</td>
</tr>
<tr class="even">
<td>运输方式：</td><td>{$shipmethod}</td>
<td>币　　种：</td><td>{$currency}</td>
</tr>
<tr class="odd">
<td>地　　区：</td><td width="440">{$region}</td>
<td></td><td></td>
</tr>
<tr class="even">
<td>产品规格及发货补充说明：</td><td><textarea name="delivery" style="width:444px;height:111px">{$order['delivery']}</textarea></td>
<td>其他条款：</td><td><textarea name="remark" style="width:444px;height:111px">{$order['remark']}</textarea></td>
</tr>
<tr class="even">
<td>产品要求：</td><td><textarea name="special" style="width:444px;height:111px">{$order['special']}</textarea></td>
<td></td><td></td>
</tr>
</table>
<table class="hundred">
<thead>
<tr>
	<th colspan="17">订单条目明细表（<span class="darkred small">如需要增加更多订单，可在保存后，使用“增加订单条目”以增加其它订单的条目。</span>）</th>
</tr>
</thead>
<tbody class="small">
<tr class="even">
	<td width="15">ID</td><td width="80">订单号</td><td>物资编号</td><td width="50">数量<span class="red bold">*</span></td><td width="50">单位<span class="red bold">*</span></td><td width="45">单价<span class="red bold">*</span></td><td width="60">金额<span class="red bold">*</span></td><td>选</td>
</tr>
{$itemList}
<tr>
	<td colspan="17" class="gray small">注：①条目如<b>选择</b>则至少有“数量”、“单位”、“单价”等四个项目，该条明细才会被保存。②折扣值范围“1-100”，不填默认为“100”。</td>
</tr>
</tbody>
</table>
{$upload}
<table class="tlist tsuite" align="center" width="100%">
<tbody>
<tr class="odd">
	<td>选　　项：</td>
	<td colspan="3">
		<label for="options1"><input type="radio" id="options1" name="options" value="1">保存后，继续执行，添加其它订单的条目。</label>
		<label for="options2"><input type="radio" id="options2" name="options" value="2" checked>保存后，转到新建立的发票详细页面。</label>
	</td>
</tr>
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
			$this->kclass->tbline+=4;
			$this->kclass->page['title'].='新建发票';
			$this->kclass->page['onload'].='var dates=$(\'#drawed,#dateOfArrival\').datepicker({onSelect:function(selectedDate){instance=$(this).data(\'datepicker\'),date=$.datepicker.parseDate(instance.settings.dateFormat || $.datepicker._defaults.dateFormat,selectedDate,instance.settings);}});dc.tabhover();';
			$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - 新建发票', 'right'=>'<a href="/s.php?module=pinvoice&action=list">返回列表</a>','body'=>$body));
		}else{
			if($this->kclass->input['porderid']){
				foreach($this->kclass->input['porderid'] as $k=>$v){
					$order=$this->kclass->DB->queryFirst("
						SELECT `porder`.porderid,`porder`.supplierid, 
							`supplier`.title AS supplier,`supplier`.supplierno
						FROM `porder` 
						LEFT JOIN `supplier` ON (`supplier`.supplierid=`porder`.supplierid)
						WHERE `porder`.porderid='".$v."' 
						LIMIT 0,1
					");
					if($order['supplierid']>0){
						$supplierid[$k] = $order['supplierid'];
					}else{
						$supplierid[$k] = $this->kclass->input['supplierid'];
					}
					$inputItem .='<input type="hidden" name="porderid['.$k.']" value="'.$v.'">';
				}
				$m = array_unique($supplierid);//剔除数组中相同值的元素
				if(count($m)!=1){
					$e = '<li>请选择同一供应商关联的订单！</li>';
					if(isset($e)){
						$this->kclass->messager(array(
							'title' => '新建发票：选择供应商',
							'text' => '您在新建发票的过程中有以下错误：<ul>'.$e.'</ul>',
							'url' => 'javascript:history.back()',
							'sec' => 3
						));
					}
				}else{
					$id = $supplierid[0];
					$supplier=$this->kclass->chooserSupplier(array('hName'=>'supplierid','name'=>'supplier','width'=>400,'selectedid'=>$id ,'value'=>$order['supplier'].' ('.$order['supplierno'].')'));				
				}

$body = <<<EOF
<form action="/s.php?module=pinvoice&action=add" name="pinvoice" method="post">
<input type="hidden" name="module" value="pinvoice">
<input type="hidden" name="action" value="add">
<input type="hidden" name="step" value="2">
{$inputItem}
<table>
<thead>
<tr>
	<th colspan="4">新建发票：选择供应商</th>
</tr>
</thead>
<tbody>
<tr class="odd">
	<td title="">选择供应商：<span class="red bold">*</span></td>
	<td>{$supplier}</td>
</tr>
<tr class="odd" nohover>
	<td colspan="4" align="center">
		<input type="submit" id="submitButton" value="  下一步  " accesskey="s">
		<input type="reset" value="  复位  ">
	</td>
</tr>
</tbody>
</table>
</form>
EOF;
			}else{
				$order=$this->kclass->chooserPOrder(array('module'=>'pinvoice','name'=>'porderid[1]','hasBlank'=>1,'width'=>450,'orderby'=>'`porder`.created','direction'=>'DESC','condition'=>'AND orderStatus=1 AND ifinvoiced=0'));
$body=<<<EOF
<form action="/s.php?module=pinvoice&action=add" method="post">
	<input type="hidden" name="module" value="pinvoice">
	<input type="hidden" name="action" value="add">
<table>
<thead>
<tr>
	<th colspan="4">新建发票：选择订单 <span class="small red">(由采购部填写)</span></th>
</tr>
</thead>
<tbody>
<tr class="odd pordertr" >
	<td>{$order}</td>
</tr>
<tr class="odd">
	<td><input type="button" id="addSelect" value="添加订单"></td>
</tr>
<tr class="odd" nohover>
	<td colspan="4" align="center">
		<input type="submit" id="submitButton" value="  下一步  " accesskey="s">
		<input type="reset" value="  复位  ">
	</td>
</tr>
</tbody>
</table>
</form>
EOF;
			}
			$this->kclass->page['title'].= ' - 新建发票';
			$this->kclass->page['onload'].='dc.tabhover();$(\'#date\').datepicker();dc.addPorder();';
			$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 新建发票', 'right' => '<a href="/s.php?module=pinvoice">返回列表</a>', 'body'=>$body));
		}
	}
	//
	function insert(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			if($this->kclass->input['invoiceCode']==''){
				$e.='<li>请填写 发票 的 发票号码/li>';
			}
			if($this->kclass->input['pinvoiceTypeid']<=0){
				$e.='<li>请填写 发票 的 发票类型/li>';
			}
			if($this->kclass->input['drawed']==''){
				$e.='<li>请填写 发票 的 开票日期/li>';
			}
			if($this->kclass->input['step']==2){
				$supplier=$this->kclass->DB->queryFirst("SELECT * FROM `supplier` WHERE supplierid='".$this->kclass->input['supplierid']."'");
				if(!$supplier){
					$e .= '<li>请选择返回列表刷新，重新进入新建页面，如此问题连续出现，请联系管理员。</li>';
				}
				if(count($this->kclass->input['olSelect']) == 0){
					$e .= '<li>请至少选择一个条目。</li>';
				}
			}else{
				$e .= '<li>请返回列表刷新后，再进行这些操作。如果此信息持续出现，请联系管理员。</li>';
			}
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '新建发票',
				'text' => '您在新建发票的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$start=strtotime(date('Y-m-d',TIMENOW));
		$end=strtotime(date('Y-m-d',$start))+86400;
		$counter=$this->kclass->DB->queryFirst("SELECT COUNT(*) AS count FROM `pinvoice` WHERE created>='".$start."' AND created<='".$end."'");
		$invoiceno=$this->kclass->id(array('pinvoice'=>TIMENOW,'number'=>$counter['count']));
		if($this->kclass->input['step']==2){
			if($this->kclass->input['onBoardDate'] != ''){
				$sd = explode('-', $this->kclass->input['onBoardDate']);
				$onBoardDate = mktime(0,0,0,$sd[1],$sd[2],$sd[0]);
			}else{
				$onBoardDate = 0;
			}
			if($this->kclass->input['dateOfArrival'] != ''){
				$dd = explode('-', $this->kclass->input['dateOfArrival']);
				$dateOfArrival = mktime(0,0,0,$dd[1],$dd[2],$dd[0]);
			}else{
				$dateOfArrival = 0;
			}
			$this->kclass->input['drawed']=strtotime($this->kclass->input['drawed']);
			$this->kclass->input['regionid']=$this->kclass->iif($this->kclass->input['regionid']==-1,0,$this->kclass->input['regionid']);
			$porderid = implode(",",$this->kclass->input['porderid']);
			//数据库新增发票记录
			$this->kclass->DB->query("
				INSERT INTO pinvoice				(invoiceno,title,porderid,invoiceCode,customerid,supplierid,customerBankDetailid,supplierBankDetailid,pinvoiceTypeid,amount,finalDestination,drawer,recipient,taxRate,drawed,businesstype,deliverytermid,paymenttermid,shipmethodid,currencyid,dateOfArrival,regionid,remark,created,creator)
				VALUES (
					'".$invoiceno."',
					'".$this->kclass->input['title']."',
					'".$porderid."',
					'".$this->kclass->input['invoiceCode']."',
					'".$this->kclass->input['customerid']."',
					'".$this->kclass->input['supplierid']."',
					'".$this->kclass->input['customerBankDetailid']."',
					'".$this->kclass->input['supplierBankDetailid']."',
					'".$this->kclass->input['pinvoiceTypeid']."',
					'".$this->kclass->input['amount']."',
					'".$this->kclass->input['finalDestination']."',
					'".$this->kclass->input['drawer']."',
					'".$this->kclass->input['recipient']."',
					'".$this->kclass->input['taxRate']."',
					'".$this->kclass->input['drawed']."',
					'".$this->kclass->input['businesstype']."',
					'".$this->kclass->input['deliverytermid']."',
					'".$this->kclass->input['paymenttermid']."',
					'".$this->kclass->input['shipmethodid']."',
					'".$this->kclass->input['currencyid']."',
					'".$dateOfArrival."',
					'".$this->kclass->input['regionid']."',
					'".$this->kclass->input['remark']."',
					'".TIMENOW."',
					'".$this->kclass->user['userid']."'
				)
			");
			$pinvoiceid=$this->kclass->DB->insertID();
			foreach($this->kclass->input['porderid'] as $k=>$v){
				for($i=0;$i<(count($this->kclass->input['olItemid'][$k])); $i++){
					$this->kclass->input['olQuantity'][$k][$i]=intVal($this->kclass->input['olQuantity'][$k][$i]);
					$this->kclass->input['olPrice'][$k][$i]=floatVal($this->kclass->input['olPrice'][$k][$i]);
					if($this->kclass->input['olSelect'][$k][$i]>0 AND $this->kclass->input['olSelect'][$k][$i] == $this->kclass->input['olItemid'][$k][$i] AND $this->kclass->input['olQuantity'][$k][$i]>0 AND $this->kclass->input['olPrice'][$k][$i] >=0 ){
						$item=$this->kclass->DB->queryFirst("SELECT * FROM `item` WHERE itemid='".$this->kclass->input['olItemid'][$k][$i]."' LIMIT 0,1");
						$this->kclass->DB->query("
							INSERT INTO item
								(module,mid,porderid,materialid,quantity,brandid,unitid,package,price,amount,modified,modifier,created,creator)
							VALUES (
								'pinvoice',
								'".$pinvoiceid."',
								'".$v."',
								'".$item['materialid']."',
								'".$this->kclass->input['olQuantity'][$k][$i]."',
								'".$item['brandid']."',
								'".$this->kclass->input['olUnitid'][$k][$i]."',
								'".$item['package']."',
								'".$this->kclass->input['olPrice'][$k][$i]."',
								'".$this->kclass->input['olAmount'][$k][$i]."',
								'".TIMENOW."',
								'".$this->kclass->user['userid']."',
								'".TIMENOW."',
								'".$this->kclass->user['userid']."'
							)
						");
					}
				}
				$this->kclass->DB->query("UPDATE porder SET ifinvoiced=1 WHERE porderid={$v}");
			}
		}
		$this->kclass->messager(array(
			'title' => '新建发票',
			'text' => '发票 <b>'.$invoiceno.'</b> 已新建成功! '.$this->kclass->iif($this->kclass->input['options']==1, '<span class="bold darkred">现在进入下一步，从该客户全部订单的明细中选择(此步骤也可以后续再操作)。</span>', ''),
			'url' => '/s.php?module=pinvoice&action='.$this->kclass->iif($this->kclass->input['options']==2, 'view', 'add&step=2&orderid='.$this->kclass->input['porderid']).'&pinvoiceid='.$pinvoiceid,
			'sec' => 2
		));
	}

	//
	function update(){
		$invoice=$this->kclass->DB->queryFirst("
			SELECT `pinvoice`.*,
				`supplier`.title AS supplier,
				`customer`.title AS customer
			FROM `pinvoice`
			LEFT JOIN `supplier` ON (`supplier`.supplierid=`pinvoice`.supplierid)
			LEFT JOIN `customer` ON (`customer`.customerid=`pinvoice`.customerid)
			WHERE `pinvoice`.killed=0 AND `pinvoice`.`pinvoiceid`='".$this->kclass->input['pinvoiceid']."'
		");
		if(!$invoice AND $this->kclass->input['pinvoiceid']<=0){
			$this->kclass->boinkIt('/s.php?module=pinvoice');
		}
		$itemId=array();
		if($this->kclass->input['step'] == 'item'){
			// org item
			$orgItems=$this->kclass->DB->query("
				SELECT item.*,
					`material`.title AS material,`material`.materialno,`material`.standard,
					`porder`.orderno,
					unit.title AS unit
				FROM `item`
				LEFT JOIN `material` ON (`material`.materialid=item.materialid)
				LEFT JOIN `porder` ON (`porder`.porderid=item.mid)
				LEFT JOIN unit ON (unit.unitid=item.unitid)
				WHERE `item`.killed=0 AND `item`.module='pinvoice' AND `item`.mid='".$invoice['pinvoiceid']."'
				ORDER BY modified ASC, itemid ASC
			");
			if($this->kclass->DB->numRows()){
				$i=1;
				while($orgItem=$this->kclass->DB->fetchArray($orgItems)){
					$orgItemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').'"><td>'.$i.'</td>
						<td title="创建时间：'.date('Y-m-d', $orgItem['orderCreated']).'"><a href="/s.php?module=order&action=view&porderid='.$orgItem['porderid'].'" target="_blank">'.$orgItem['orderno'].'</a></td>
						<td><input type="hidden" name="orgItemid['.$i.']" value="'.$orgItem['itemid'].'"><input type="hidden" name="orgOrderid['.$i.']" value="'.$orgItem['porderid'].'">'.$orgItem['materialno'].' <a href="/s.php?module=material&action=view&materialid='.$item['materialid'].'" target="_blank">'.$orgItem['title'].' '.$orgItem['standard'].'</a></td>
						<td><input type="text" name="orgQuantity['.$i.']" value="'.$orgItem['quantity'].'" size="6"></td>
						<td>'.$this->kclass->chooserUnit(array('name'=>'orgUnitid['.$i.']', 'hasBlank'=>1, 'width'=>100, 'selectedid'=>$orgItem['unitid'])).'</td>
						<td><input type="text" name="orgPrice['.$i.']" value="'.$orgItem['price'].'" size="6"></td>
						<td><input type="text" name="orgAmount['.$i.']" value="'.$orgItem['amount'].'" size="10"></td>
						<td><input type="checkbox" name="orgSelect['.$i.']" value="'.$orgItem['itemid'].'"></td>
						</tr>';
					$i++;
					$itemId[]=$orgItem['itemid'];
				}
			}
			// other item for supplier
			$orders=$this->kclass->DB->query("
				SELECT porderid,orderno,taxRate,created
				FROM `porder`
				WHERE killed=0 AND supplierid='".$invoice['supplierid']."'
			");
			if($counter=$this->kclass->DB->numRows()){
				$i=1;
				while($order=$this->kclass->DB->fetchArray($orders)){
					$taxRate=$this->kclass->chooserTaxRate(array('name'=>'taxRate','hasBlank'=>1,'topname'=>'请选择','start'=>1,'end'=>100,'selectedid'=>$order['taxRate']));
					$items=$this->kclass->DB->query("
						SELECT item.*,
							`material`.title AS material,`material`.materialno,`material`.standard,
							unit.title AS unit
						FROM `item`
						LEFT JOIN `material` ON (`material`.materialid=item.materialid)
						LEFT JOIN unit ON (unit.unitid=item.unitid)
						WHERE `item`.killed=0 AND `item`.module='porder' AND `item`.mid='".$order['porderid']."' 
						ORDER BY `item`.modified ASC, `item`.itemid ASC
					");
					if($this->kclass->DB->numRows()){
						while($item = $this->kclass->DB->fetchArray($items)){
							$itemList.='<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').'"><td>'.$i.'</td>
							<td title="创建时间：'.date('Y-m-d', $item['orderCreated']).'"><a href="/s.php?module=order&action=view&porderid='.$order['porderid'].'" target="_blank">'.$order['orderno'].'</a></td>
							<td><input type="hidden" name="olItemid['.$i.']" value="'.$item['itemid'].'"><input type="hidden" name="olOrderid['.$i.']" value="'.$order['porderid'].'">'.$item['materialno'].' <a href="/s.php?module=material&action=view&materialid='.$item['materialid'].'" target="_blank">'.$item['title'].' '.$item['standard'].'</a></td>
							<td><input type="text" name="olQuantity['.$i.']" value="'.$item['quantity'].'" size="6"></td>
							<td>'.$this->kclass->chooserUnit(array('name'=>'olUnitid['.$i.']', 'hasBlank'=>1, 'width'=>100, 'selectedid'=>$item['unitid'])).'</td>
							<td><input type="text" name="olPrice['.$i.']" value="'.$item['price'].'" size="6"></td>
							<td><input type="text" name="olAmount['.$i.']" value="'.$item['amount'].'" size="10"></td>
							<td><input type="checkbox" name="olSelect['.$i.']" value="'.$item['itemid'].'" ></td>
							</tr>';
							$i++;
						}
					}
				}
			}

$body = <<<EOF
<form action="/s.php?module=pinvoice&action=doupdate" name="pinvoice" method="post">
<input type="hidden" name="module" value="pinvoice">
<input type="hidden" name="action" value="doupdate">
<input type="hidden" name="step" value="item">
<input type="hidden" name="pinvoiceid" value="{$invoice['pinvoiceid']}">
<input type="hidden" name="supplierid" value="{$invoice['supplierid']}">
<table class="hundred">
<thead>
<tr><th>ID</th><th>订单号</th><th>物资</th><th>数量<span class="red bold">*</span></th><th>单位<span class="red bold">*</span></th><th>单价<span class="red bold">*</span></th><th>金额<span class="red bold">*</span></th><th class="darkred">删</th></tr>
</thead>
<tbody>
<tr>
	<td colspan="12" class="darkred">发票编号：<span class="bold">{$invoice['invoiceno']}</span> 已有条目</td>
</tr>
{$orgItemtr}
</tbody>
<thead>
<tr><th>ID</th><th>订单号</th><th>物资</th><th>数量<span class="red bold">*</span></th><th>单位<span class="red bold">*</span></th><th>单价<span class="red bold">*</span></th><th>金额<span class="red bold">*</span></th><th>选</th></tr>
</thead>
<tbody>
<tr class="odd">
	<td colspan="12" class="darkred">供应商 <span class="bold darkred">{$invoice['supplier']}</span> 的全部订单下的条目明细 <span class="small gray">(共{$counter}个，但没有明细内容的订单不会显示)</span></td>
</tr>
{$itemList}
<tr class="odd" nohover>
	<td align="center" colspan="12">
		<input type="submit" id="submitButton" value="  保存  " accesskey="s">
		<input type="reset" value="  复位  ">
	</td>
</tr>
</tbody>
</table>
</form>
EOF;
			$this->kclass->page['title'] .= ' 新增与修改发票订单条目 - '.$invoice['invocieno'];
			$this->kclass->page['onload'] .= 'dc.tabhover();';
			$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 新增与修改发票订单条目', 'right' => '<a href="/s.php?module=pinvoice&action=view&pinvoiceid='.$invoice['pinvoiceid'].'">返回发票'.$invoice['invocieno'].'</a>', 'body'=>$body));
		}else{
			if($invoice['drawed']>0){
				$invoice['drawed']=date('Y-m-d', $invoice['drawed']);
			}
			$dateOfArrival='';
			if($invoice['dateOfArrival']>0){
				$invoice['dateOfArrival']=date('Y-m-d', $invoice['dateOfArrival']);
			}

			$this->kclass->br2nl=true;
			$invoice['commodity']=$this->kclass->parseConvertValue($invoice['commodity']);
			$invoice['remark']=$this->kclass->parseConvertValue($invoice['remark']);

			$deliveryterm=$this->kclass->chooserDeliveryterm(array('name'=>'deliverytermid','width'=>200,'hasBlank'=>1,'topname'=>'请选择','selectedid'=>$invoice['deliverytermid']));
			$paymentterm=$this->kclass->chooserPaymentterm(array('name'=>'paymenttermid','width'=>200,'hasBlank'=>1,'topname'=>'请选择','selectedid'=>$invoice['paymenttermid'],'module'=>'produce'));
			$shipmethod=$this->kclass->chooserShipmethod(array('name'=>'shipmethodid','width'=>200,'hasBlank'=>1,'topname'=>'请选择','selectedid'=>$invoice['shipmethodid']));
			$currency=$this->kclass->chooserCurrency(array('name'=>'currencyid','width'=>200,'hasBlank'=>1,'topname'=>'请选择','selectedid'=>$invoice['currencyid']));
			$invoiceType=$this->kclass->chooserPInvoiceType(array('name'=>'pinvoiceTypeid','hasBlank'=>1,'topname'=>'请选择','selectedid'=>$invoice['pinvoiceTypeid']));
			$taxRate=$this->kclass->chooserTaxRate(array('name'=>'taxRate','selectedid'=>$order['taxRate']));
			$region=$this->kclass->chooserRegion(array('name'=>'regionid', 'hasBlank'=>1, 'width'=>200,));
			$upload = $this->kclass->upload(array('title'=>'相关附件：<span class="small darkred">请注意：发票里的附件不会出现在打印页面，仅在系统内部显示和存档。</span>'));
			$region = $this->kclass->chooserRegion(array('regionid'=>$customer['regionid'], 'hasBlank'=>1, 'regionid'=>$invoice['regionid']));

$body = <<<EOF
<form action="/s.php?module=pinvoice&action=doupdate" name="pinvoice" method="post">
<input type="hidden" name="module" value="pinvoice" />
<input type="hidden" name="action" value="doupdate" />
<input type="hidden" name="pinvoiceid" value="{$invoice['pinvoiceid']}" />
<table class="hundred">
<thead></tr><th colspan="4">修改发票-基本信息</th></tr></thead>
<tbody>
<tr class="odd">
<td width="100">发票编号：<span class="red bold">*</span></td><td width="550"><input type="text" name="invoiceno" value="{$invoice['invoiceno']}" /><span class="gray small">发票编号不可修改。</span></td>
<td width="100">标　　题：</td><td><input type="text" name="title" style="width:450px;" value="{$invoice['title']}" /></td>
</tr>
<tr class="even">
<td>发票号码：<span class="red bold">*</span></td><td><input type="text" name="invoiceCode" value="{$invoice['invoiceCode']}"  style="width:450px;" /></td>
<td>发票金额：<span class="red bold">*</span></td><td><input type="text" name="amount" style="width:250px;" value="{$invoice['amount']}" /></td>
</tr>
<tr class="odd">
<td>发票类型：<span class="red bold">*</span></td><td>{$invoiceType}</td>
<td>税　　率：<span class="red bold">*</span></td><td>{$taxRate}</td>
</tr>
<tr class="even">
<td>开票日期：<span class="red bold">*</span></td><td><input type="text" name="drawed" value="{$invoice['drawed']}" id="drawed" /><span class="small gray">日期格式如：2012-12-30</span></td>
<td>开 票 人：</td><td><input type="text" name="drawer" value="{$invoice['drawer']}" /></td>
</tr>
<tr class="odd">
<td>到票时间：</td><td><input type="text" name="dateOfArrival" id="dateOfArrival" value="{$invoice['dateOfArrival']}" /><span class="small gray">日期格式如：2013-12-30</span></td>
<td>签 收 人：</td><td><input type="text" name="recipient" value="{$invoice['recipient']}" /></td>
</tr>
<tr class="even">
<td>最终目的地：</td><td><input type="text" name="finalDestination" value="{$invoice['finalDestination']}" /></td>
<td>行业分类：</td><td><input type="text" name="businesstype" value="{$invoice['businesstype']}" /></td>
</tr>
<tr>
<td colspan=4 class="center bold">双方信息<span class="gray normal">供应商不能修改，如要修改供应商，则需要重新建立一张新的发票。</span></td></tr>
<tr class="odd">
<td>客　　户：</td><td>{$invoice['customer']}</td>
<td>供 应 商：</td><td>{$invoice['supplier']}</td>
</tr>
</tbody>
<thead></tr><th colspan="4">条款</th></tr></thead>
<tbody>
<tr class="odd">
<td>交货方式：</td><td>{$deliveryterm}</td>
<td>付款方式：</td><td>{$paymentterm}</td>
</tr>
<tr class="even">
<td>运输方式：</td><td>{$shipmethod}</td>
<td>币　　种：</td><td>{$currency}</td>
</tr>
<tr class="odd">
<td>地　　区：</td><td width="440">{$region}</td>
<td></td><td></td>
</tr>
<tr class="even">
<td>备　　注：</td><td colspan=3><textarea name="remark" style="width:430px;height:100px;" >{$invoice['remark']}</textarea></td>
</tr>
</table>
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
			$this->kclass->page['title'] .= ' 修改发票 - '.$invoice['invoiceno'];
			$this->kclass->page['onload'] .= 'dc.tabhover();var dates=$(\'#drawed,#dateOfArrival\').datepicker({onSelect:function(selectedDate){instance=$(this).data(\'datepicker\'),date=$.datepicker.parseDate(instance.settings.dateFormat || $.datepicker._defaults.dateFormat,selectedDate,instance.settings);}});';
			$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . '- <a href="/s.php?module=pinvoice&action=view&pinvoiceid='.$invoice['pinvoiceid'].'">'.$invoice['invoiceno'].'</a> - 修改发票', 'right' => '<a href="/s.php?module=pinvoice">返回列表</a>', 'body'=>$body));
		}
	}
	//
	function doupdate(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e='<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			$invoice=$this->kclass->DB->queryFirst("SELECT pinvoiceid,invoiceno FROM `pinvoice` WHERE killed=0 AND pinvoiceid='".$this->kclass->input['pinvoiceid']."'");
			if(!$invoice){
				$e .= '<li>您要编辑的发票并不存在，请返回列表刷新后再操作。</li>';
			}
			if($this->kclass->input['step']=='item'){

			}else{
				if($this->kclass->input['invoiceCode']==''){
					$e.='<li>请填写 发票 的 发票号码/li>';
				}
				if($this->kclass->input['pinvoiceTypeid']<=0){
					$e.='<li>请填写 发票 的 发票类型/li>';
				}
				if($this->kclass->input['drawed']==''){
					$e.='<li>请填写 发票 的 开票日期/li>';
				}
			}
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '修改发票',
				'text' => '您在修改发票的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}

		if($this->kclass->input['step']=='item'){
			$orderid=array();
			for($i = 1; $i < (count($this->kclass->input['orgItemid'])+1); $i++){
				if($this->kclass->input['orgSelect'][$i]>0 AND $this->kclass->input['orgSelect'][$i]==$this->kclass->input['orgItemid'][$i]){
					$this->kclass->DB->query("UPDATE `item` SET killed='".TIMENOW."',killer='".$this->kclass->user['userid']."' WHERE itemid='".$this->kclass->input['orgItemid'][$i]."'");
				}elseif($this->kclass->input['orgQuantity'][$i]>0 AND $this->kclass->input['orgPrice'][$i] >=0){
					$this->kclass->input['orgQuantity'][$i]=intVal($this->kclass->input['orgQuantity'][$i]);
					$this->kclass->input['orgPrice'][$i]=floatVal($this->kclass->input['orgPrice'][$i]);
					$this->kclass->input['orgAmount'][$i]=floatVal($this->kclass->input['orgAmount'][$i]);
					$amount+=$this->kclass->input['orgAmount'][$i];
					$amount=number_format($amount, 2, '.', '');
					$this->kclass->DB->query("
						UPDATE `item` SET
							quantity='".$this->kclass->input['orgQuantity'][$i]."',
							unitid='".$this->kclass->input['orgUnitid'][$i]."',
							price='".$this->kclass->input['orgPrice'][$i]."',
							amount='".$this->kclass->input['orgAmount'][$i]."'
						WHERE itemid='".$this->kclass->input['orgItemid'][$i]."'
					");
					if(!in_array($this->kclass->input['orgOrderid'][$i], $orderid)){
						$orderid[] = $this->kclass->input['orgOrderid'][$i];
					}
				}
			}
			for($i=1; $i< (count($this->kclass->input['olItemid'])+1); $i++){
				if($this->kclass->input['olSelect'][$i]>0 AND $this->kclass->input['olSelect'][$i] == $this->kclass->input['olItemid'][$i] AND $this->kclass->input['olQuantity'][$i]>0 AND $this->kclass->input['olPrice'][$i] >=0 ){
					$this->kclass->input['olQuantity'][$i]=intVal($this->kclass->input['olQuantity'][$i]);
					$this->kclass->input['olPrice'][$i]=floatVal($this->kclass->input['olPrice'][$i]);
					$this->kclass->input['olAmount'][$i]=floatVal($this->kclass->input['olAmount'][$i]);
					$amount+=$this->kclass->input['olAmount'][$i];
					$amount=number_format($amount, 2, '.', '');
					$item=$this->kclass->DB->queryFirst("SELECT * FROM `item` WHERE itemid='".$this->kclass->input['olItemid'][$i]."'");
					$this->kclass->DB->query("
						INSERT INTO item (module,mid,porderid,materialid,quantity,brandid,unitid,package,price,amount,modified,modifier,created,creator)
						VALUES ('pinvoice','".$this->kclass->input['pinvoiceid']."','".$this->kclass->input['olOrderid'][$i]."','".$item['materialid']."','".$this->kclass->input['olQuantity'][$i]."','".$item['brandid']."','".$this->kclass->input['olUnitid'][$i]."','".$item['package']."','".$this->kclass->input['olPrice'][$i]."','".$this->kclass->input['olAmount'][$i]."','".TIMENOW."','".$this->kclass->user['userid']."','".TIMENOW."','".$this->kclass->user['userid']."')
					");
					if(!in_array($this->kclass->input['olOrderid'][$i], $orderid)){
						$orderid[] = $this->kclass->input['olOrderid'][$i];
					}
				}
			}
			$tax=number_format($tax, 2, '.', '');
			$total=number_format($total, 2, '.', '');
			$this->kclass->DB->query("UPDATE `pinvoice` SET amount='".$amount."',tax='".$tax."' WHERE pinvoiceid='".$this->kclass->input['pinvoiceid']."'");
			if(count($orderid)>0){
				$this->kclass->DB->query("UPDATE `pinvoice` SET porderid='".implode(',', $orderid)."' WHERE pinvoiceid='".$this->kclass->input['pinvoiceid']."'");
			}

			$this->kclass->messager(array(
				'title' => '新增与修改发票订单条目',
				'text' => '发票 <b>'.$invoice['invoiceno'].'</b> 的明细内容已经更新! ',
				'url' => '/s.php?module=pinvoice&action=view&pinvoiceid='.$this->kclass->input['pinvoiceid'],
				'sec' => 2
			));
		}else{
			$dateOfArrival = 0;
			if($this->kclass->input['dateOfArrival'] != ''){
				$dd = explode('-', $this->kclass->input['dateOfArrival']);
				$dateOfArrival=mktime(0,0,0,$dd[1],$dd[2],$dd[0]);
			}
			$drawed=0;
			if($this->kclass->input['drawed'] != ''){
				$dd=explode('-', $this->kclass->input['drawed']);
				$drawed=mktime(0,0,0,$dd[1],$dd[2],$dd[0]);
			}
			if($this->kclass->input['cityid']>0){
				$regionid=$this->kclass->input['cityid'];
			}elseif($this->kclass->input['provinceid']>0){
				$regionid=$this->kclass->input['provinceid'];
			}else{
				$regionid=$this->kclass->input['countryid'];
			}
			$this->kclass->DB->query("
				UPDATE `pinvoice` SET
					`title`='".$this->kclass->input['title']."',
					`pinvoiceTypeid`='".$this->kclass->input['pinvoiceTypeid']."',
					`taxRate`='".$this->kclass->input['taxRate']."',
					`drawed`='".$drawed."',
					`drawer`='".$this->kclass->input['drawer']."',
					`recipient`='".$this->kclass->input['recipient']."',
					`finalDestination`='".$this->kclass->input['finalDestination']."',
					`businesstype`='".$this->kclass->input['businesstype']."',
					`regionid`='".$regionid."',
					`deliverytermid`='".$this->kclass->input['deliverytermid']."',
					`paymenttermid`='".$this->kclass->input['paymenttermid']."',
					`shipmethodid`='".$this->kclass->input['shipmethodid']."',
					`currencyid`='".$this->kclass->input['currencyid']."',
					`dateOfArrival`='".$dateOfArrival."',
					`remark` = '".$this->kclass->input['remark']."',
					`modified` = '".TIMENOW."',
					`modifier` = '".$this->kclass->user['userid']."'
				WHERE pinvoiceid='".$this->kclass->input['pinvoiceid']."'
			");
			$this->kclass->updateAttachs(array('module'=>'pinvoice', 'mid'=>$this->kclass->input['pinvoiceid']));
			$this->kclass->messager(array(
				'title' => '修改发票',
				'text' => '发票 <b>'.$invoice['invoiceno'].'</b> 的信息已成功修改!',
				'url' => '/s.php?module=pinvoice&action=view&pinvoiceid='.$invoice['pinvoiceid'],
				'sec' => 3
			));
		}
	}
	//
	function kill(){
		if($this->kclass->input['pinvoiceid']==''){
			$this->kclass->boinkIt('/s.php?module=pinvoice');
		}
		if(!$this->kclass->input['rt'] AND $this->kclass->input['confirm'] == 0){
			$this->kclass->boinkIt('/s.php?module=pinvoice&action=view&pinvoiceid='.$this->kclass->input['pinvoiceid']);
		}
		if($this->kclass->input['pinvoiceid'] < 0){
			$e = '<li>请从正确的页面进行操作，如果此问题持续存在，请联系网站管理员。</li>';
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '删除发票',
				'text' => '您在删除发票的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		if($this->kclass->input['rt']=='view'){
			$rt = '&action=view&pinvoiceid='.$this->kclass->input['pinvoiceid'];
		}elseif($this->kclass->input['rt']=='list'){
			$rt = '#'.$this->kclass->input['pinvoiceid'];
		}
		$invoice = $this->kclass->DB->queryFirst("
			SELECT invoiceno
			FROM pinvoice
			WHERE pinvoiceid='".$this->kclass->input['pinvoiceid']."'
		");
		if($invoice){
			$this->kclass->DB->query("
				UPDATE `pinvoice`
				SET killed=".TIMENOW.",killer='".$this->kclass->user['userid']."'
				WHERE pinvoiceid='".$this->kclass->input['pinvoiceid']."'
			");
			$this->kclass->messager(array(
				'title' => '删除发票成功',
				'text' => '发票 <b>'.$invoice['invoiceno'].'</b> 已成功被标记为删除!',
				'url' => '/s.php?module=pinvoice'.$rt,
				'sec' => 2
			));
		}else{
			$this->kclass->messager(array(
				'title' => '删除发票失败',
				'text' => '您要删除的发票，不存在！',
				'url' => '/s.php?module=pinvoice'.$rt,
				'sec' => 3
			));
		}
	}

	//
	function remove(){
		if($this->kclass->input['pinvoiceid']<=0){
			$this->kclass->boinkIt('/s.php?module=pinvoice');
		}
		$invoice = $this->kclass->DB->queryFirst("
			SELECT invoiceno
			FROM pinvoice
			WHERE pinvoiceid='".$this->kclass->input['pinvoiceid']."'
		");
$body = <<<EOF
<form action="/s.php?module=pinvoice&action=kill" name="pinvoice" method="post">
<input type="hidden" name="module" value="pinvoice">
<input type="hidden" name="action" value="kill">
<input type="hidden" name="pinvoiceid" value="{$this->kclass->input['pinvoiceid']}">
<table><thead>
<thead>
<tr>
	<th>删除发票：{$invoice['invoiceno']}</th>
</tr>
</thead>
<tbody>
<tr class="even">
	<td class="middle">你确定要删除发票: <a href="/s.php?module=pinvoice&action=view&pinvoiceid={$this->kclass->input['pinvoiceid']}" class="big bold" target="_blank">{$invoice['invoiceno']}</a> 吗?</td>
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
		$this->kclass->page['title'] .= ' - 删除 - '.$invoice['invoiceno'];
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 删除发票 - '.$invoice['invoiceno'], 'right' => '<a href="/s.php?module=pinvoice">返回列表</a>', 'body'=>$body));
	}

	//
	function revival(){
		if($this->kclass->input['pinvoiceid']<=0){
			$this->kclass->boinkIt('/s.php?module=pinvoice');
		}
		if(!$this->kclass->input['rt'] AND $this->kclass->input['confirm'] == 0){
			$this->kclass->boinkIt('/s.php?module=pinvoice&action=view&pinvoiceid='.$this->kclass->input['pinvoiceid']);
		}
		if($this->kclass->input['pinvoiceid'] < 0){
			$e = '<li>请从正确的页面进行操作，如果此问题持续存在，请联系网站管理员。</li>';
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '恢复发票',
				'text' => '您在恢复发票的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$invoice = $this->kclass->DB->queryFirst("
			SELECT invoiceno
			FROM invoice
			WHERE pinvoiceid='".$this->kclass->input['pinvoiceid']."'
		");
		if($this->kclass->input['rt']=='view'){
			$rt = '&action=view&pinvoiceid='.$this->kclass->input['pinvoiceid'];
		}elseif($this->kclass->input['rt']=='list'){
			$rt = '#'.$this->kclass->input['pinvoiceid'];
		}
		if($invoice){
			$this->kclass->DB->query("
				UPDATE `pinvoice`
				SET killed=0
				WHERE pinvoiceid='".$this->kclass->input['pinvoiceid']."'
			");
			$this->kclass->messager(array(
				'title' => '恢复发票成功',
				'text' => '发票 <b>'.$invoice['invoiceno'].'</b> 已成功被恢复！',
				'url' => '/s.php?module=pinvoice'.$rt,
				'sec' => 2
			));
		}else{
			$this->kclass->messager(array(
				'title' => '恢复发票失败',
				'text' => '您要恢复的发票不存在！',
				'url' => '/s.php?module=pinvoice'.$rt,
				'sec' => 3
			));
		}
	}

	//
	function restore(){
		if($this->kclass->input['pinvoiceid']<=0){
			$this->kclass->boinkIt('/s.php?module=pinvoice');
		}
		$invoice = $this->kclass->DB->queryFirst("
			SELECT invoiceno
			FROM invoice
			WHERE pinvoiceid='".$this->kclass->input['pinvoiceid']."'
		");
$body = <<<EOF
<form action="/s.php?module=pinvoice&action=revival" name="invoice" method="post">
<input type="hidden" name="module" value="invoice">
<input type="hidden" name="action" value="revival">
<input type="hidden" name="pinvoiceid" value="{$this->kclass->input['pinvoiceid']}">
<table><thead>
<thead>
<tr>
	<th>恢复发票：{$invoice['invoiceno']}</th>
</tr>
</thead>
<tbody>
<tr class="even">
	<td class="middle">你确定要恢复发票: <a href="/s.php?module=pinvoice&action=view&pinvoiceid={$this->kclass->input['pinvoiceid']}" class="big bold" target="_blank">{$invoice['invoiceno']}</a> 吗?</td>
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
		$this->kclass->page['title'] .= ' - 恢复 - '.$invoice['invoiceno'];
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 恢复发票 - '.$invoice['invoiceno'], 'right' => '<a href="/s.php?module=pinvoice">返回列表</a>', 'body'=>$body));
	}
}

?>