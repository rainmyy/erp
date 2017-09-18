<?php
class invoice{
	function autoRun(){
		$this->kclass->page['title'] = '发票';
		$this->baseurl = '<a href="/s.php">首页</a> - <a href="/s.php?module=invoice">发票</a>';
		$this->right = '<span class="small"><a href="/s.php?module=invoice&action=find">查找</a>：<form style="display:inline-block" action="/s.php?module=invoice&action=list" name="invoice" method="post"><input type="text" style="width:50px;height:15px;margin-top:-3px" name="title" value="'.$this->kclass->input['title'].'"><input type="submit" value="找" accesskey="s" style="height:23px"></form></span>';
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
			case 'addPacking':
				$this->addPacking();
			break;
			case 'insertPacking':
				$this->insertPacking();
			break;
			case 'updatePacking':
				$this->updatePacking();
			break;
			case 'doupdatePacking':
				$this->doupdatePacking();
			break;
			case 'removePacking':
				$this->removePacking();
			break;
			case 'killPacking':
				$this->killPacking();
			break;
			case 'revivalPacking':
				$this->revivalPacking();
			break;
			case 'restorePacking':
				$this->restorePacking();
			break;
			case 'addQuoting':
				$this->addQuoting();
			break;
			case 'insertQuoting':
				$this->insertQuoting();
			break;
			case 'updateQuoting':
				$this->updateQuoting();
			break;
			case 'doupdateQuoting':
				$this->doupdateQuoting();
			break;
			case 'removeQuoting':
				$this->removeQuoting();
			break;
			case 'killQuoting':
				$this->killQuoting();
			break;
			case 'audit':
				$this->audit();
			break;
			case 'auditForm':
				$this->auditForm();
			break;
			case 'approvalForm':
				$this->approvalForm();
			break;
			case 'approval':
				$this->approval();
			break;
			case 'viewOldVersion':
				$this->viewOldVersion();
			break;
			case 'returnInvoiceHtml':
				$this->returnInvoiceHtml();
			break;
			default:
				$this->mmlist();
		}
	}
	
	
	/**
	 * 查看发票的修改版本信息
	 * @author creator mayinghao 2013-07-17 16:15
	 * @access public
	 * @param none 没有参数
	 * @return void 没有返回信息
	 * @throws none 没有异常
	 */
	function viewOldVersion(){
		if($this->kclass->input['reviseid']>0){
			$revise = $this->kclass->DB->queryFirst("
				SELECT `revise`.ptext,`revise`.version
				FROM `revise`
				WHERE reviseid='".$this->kclass->input['reviseid']."'
			");
			if($revise){
				$this->kclass->page['onload'] .= "dc.tabs({'id':'order'});dc.tabhover()";
				$this->kclass->page['title'] .= ' - 发票版本R'.$revise['version'];
				$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl.' - 发票版本信息　版本R'.$revise['version'], 'right' => '', 'body'=>$revise['ptext']));
			}else{
				$this->kclass->messager(array(
						'title' => '查看发票历史版本',
						'text' => '没有此历史版本!',
						'url' => '/s.php?module=order&action=view&orderid='.$this->kclass->input['orderid'],
						'sec' => 5
				));
			}
		}
	}
	
	/**
	 * 返回发票的HTML信息
	 * @author creator mayinghao 2013-07-17 16:15
	 * @access public
	 * @param none 没有参数
	 * @return string 返回HTML字符串
	 * @throws none 没有异常
	 */
	function returnInvoiceHtml(){
		if(!$this->kclass->input['invoiceid'] > 0)$this->kclass->boinkIt('/s.php?module=invoice');
		$invoice = $this->kclass->DB->queryFirst("
			SELECT `invoice`.*, 
				region.encountry AS regionEncountry, region.country AS regionCountry, region.enstate AS regionEnstate, region.state AS regionState, region.encity AS regionEncity, region.city AS regionCity, 
				customer.title AS customerTitle, customer.address AS customerAddress, customer.linkman AS customerLinkman, customer.position AS customerPosition, customer.telephone AS customerTelephone, customer.fax AS customerFax, 
				shipmethod.title AS shipmethodTitle, shipmethod.entitle AS shipmethodEntitle, 
				loading.title AS loadingTitle, loading.entitle AS loadingEntitle, 
				loadingCountry.country AS loadingCountryTitle, loadingCountry.encountry AS loadingCountryEntitle, 
				discharge.title AS dischargeTitle, discharge.entitle AS dischargeEntitle, 
				dischargeCountry.country AS dischargeCountryTitle, dischargeCountry.encountry AS dischargeCountryEntitle, 
				paymentterm.title AS paymenttermTitle, paymentterm.entitle AS paymenttermEntitle, 
				deliveryterm.title AS deliverytermTitle, deliveryterm.entitle AS deliverytermEntitle, 
				currency.title AS currencyTitle, currency.symbol AS currencySymbol, currency.remark AS currencyRemark, 
				unit.title AS unitTitle, unit.entitle AS unitEntitle, unit.remark AS unitRemark, 
				m.username AS mname, c.username AS cname ,t.username AS tname,d.username AS dname
			FROM `invoice` 
			LEFT JOIN region ON (region.regionid=`invoice`.regionid) 
			LEFT JOIN customer ON (customer.customerid=`invoice`.customerid) 
			LEFT JOIN currency ON (currency.currencyid=`invoice`.currencyid) 
			LEFT JOIN unit ON (unit.unitid=`invoice`.unitid) 
			LEFT JOIN shipmethod ON (shipmethod.shipmethodid=`invoice`.shipmethodid) 
			LEFT JOIN port AS loading ON (loading.portid=`invoice`.loading) 
			LEFT JOIN `region` AS loadingCountry ON (loadingCountry.regionid=loading.regionid)
			LEFT JOIN port AS discharge ON (discharge.portid=`invoice`.discharge) 
			LEFT JOIN `region` AS dischargeCountry ON (dischargeCountry.regionid=discharge.regionid)
			LEFT JOIN paymentterm ON (paymentterm.paymenttermid=`invoice`.paymenttermid) 
			LEFT JOIN deliveryterm ON (deliveryterm.deliverytermid=`invoice`.deliverytermid) 
			LEFT JOIN `user` AS m ON (m.userid=invoice.modifier) 
			LEFT JOIN `user` AS c ON (c.userid=invoice.creator) 
			LEFT JOIN `user` AS t ON (t.userid=invoice.tracker)
			LEFT JOIN `user` AS d ON (d.userid=invoice.director)
			WHERE invoiceid='".$this->kclass->input['invoiceid']."'
		");
		if(!$invoice)	$this->kclass->boinkIt('/s.php?module=invoice&action=find');
		if($invoice['modifier']>0){
			$modified = '，'.$invoice['mname'].' 于 '.date('y-m-d H:i:s', $invoice['modified']).' 最后修改';
		}
		$created = date('y-m-d H:i:s', $invoice['created']);
		$attach = $this->kclass->getAttachs(array('module'=>'invoice', 'mid'=>$invoice['invoiceid']));
		if($attach!= false){
			$attachs = '<div class="clear">'.$attach.'</div>';
		}
		if($invoice['onBoardDate']>0){
			$onBoardDate=date('Y-m-d', $invoice['onBoardDate']);
		}else{
			$onBoardDate='--';
		}
		if($invoice['dateOfArrival']>0){
			$dateOfArrival=date('Y-m-d', $invoice['dateOfArrival']);
		}else{
			$dateOfArrival='--';
		}
		if($invoice['regionid']>0){
			if($invoice['regionEncity']!=''){
				$region = $invoice['regionEncity'].', ';
			}
			if($invoice['regionEnstate']!=''){
				$region .= $invoice['regionEnstate'].', <br>';
			}
			if($invoice['regionEncountry']!=''){
				$region .= $invoice['regionEncountry'];
			}
		}
		$total=0;
		$item = $this->kclass->relatedInvoiceItem(array('invoiceid'=>$invoice['invoiceid']));
		$total = $item['total'];
		$feeitem = $this->kclass->relatedFee(array('invoiceid'=>$invoice['invoiceid']));
		$total += $feeitem['total'];
		$discounts = $this->kclass->DB->query("SELECT * FROM `discount` WHERE killed=0 AND module='invoice' AND mid='".$invoice['invoiceid']."'");
		if($this->kclass->DB->numRows()){
			while($discount=$this->kclass->DB->fetchArray($discounts)){
				if($item['total']>0){
					$amount = 0;
					if($discount['sign']=='%'){
						$discount['discount'] = intVal($discount['discount']);
						$amount = ($item['total'] * $discount['discount'] / 100);
					}else{
						$amount = $discount['discount'];
					}
					if($discount['addition']=='+'){
						$total += $amount;
					}else{
						$total -= $amount;
					}
				}
				$discounttr .= '<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'even', 'odd').'">
				<td>'.$discount['reason'].'</td>
				<td align="right">'.$discount['addition'].''.$discount['discount'].''.$this->kclass->iif($discount['sign']=='%', $discount['sign'], '').'</td>
				<td align="right">'.$discount['addition'].number_format($amount, 2).'</td>
				<td align="right">'.$invoice['currencySymbol'].number_format($total, 2).'</td>
				</tr>';
			}
			$discounttr = '<tr><td title="Reason">折扣说明(理由)</td><td title="Discount" align="right">折扣</td><td title="Amount" align="right">计算折扣数</td><td title="Amount" align="right">折扣后总数</td></tr>'.$discounttr.'<tr><td colspan="12" align="right">折后计：<span class="bold">'.$invoice['currencyTitle'].' '.$invoice['currencySymbol'].number_format($total, 2).'</span></td></tr>';
		}else{
			$discounttr = '<tr><td class="gray"><i>无折扣信息</i></td></tr>';
		}
		$total = number_format($total, 2);
		if($invoice['refno']==''){
			$invoice['refno'] = '<i class="gray">None</i>';
		}
		
		//$related=$this->kclass->relatedPackinglist(array('invoiceid'=>$invoice['invoiceid']));
//		$inquiry=$this->kclass->relatedPacking(array('productid'=>$invoice['productid'], 'options'=>1));
//		$quoting=$this->kclass->relatedQuoting(array('productid'=>$invoice['productid'], 'options'=>1));
return <<<EOF
<div class="bold darkred big" style="clear:both;"><span class="right small gray">由 {$invoice['cname']} 于 {$created} 建立{$modified}。</span>{$invoice['invoiceno']}<span class="gray small">　　　业务人员：<span class="darkred bold">{$invoice['dname']}</span></span>　　　<span class="gray small">跟单人员：<span class="darkred bold">{$invoice['tname']}</span></span></div>
<table class="hundred">
<thead>
<tr>
	<th colspan="4"><span class="right"><span class="normal">货物数量：</span>{$invoice['quantity']} {$invoice['unitTitle']} <span class="normal">总计(Total Amount)：</span><span class="darkred">{$invoice['currencyTitle']} {$invoice['currencySymbol']}{$total}</span></span>客户信息 (Customer Information)</th>
</tr>
</thead>
<tbody>
<tr class="odd">
	<td width="100">客户名称：<br>发票抬头：</td>
	<td><a href="/s.php?module=customer&action=view&customerid={$invoice['customerid']}" target="_blank">{$invoice['customerTitle']}</a><br>{$invoice['title']}</td>
	<td width="100">抬头地址：</td>
	<td>{$invoice['address']}<br>{$region}</td>
</tr>
</tbody>
</table>

<table class="hundred">
<thead>
<tr>
	<th colspan="4"><span class="right"><span class="normal" title="Currency">币种：</span>{$invoice['currencyTitle']} {$invoice['currencySymbol']} <span class="gray">{$invoice['currencyRemark']}</span></span>一般信息 (General Information)</th>
</tr>
</thead>
<tbody>
<tr class="odd">
	<td width="100" title="Shipment Method">运输方式：</td>
	<td class="middle">{$invoice['shipmethodTitle']} <span class="gray">{$invoice['shipmethodEntitle']}</span></td>
	<td width="100" title="Delivery Term">交货条款：</td>
	<td class="middle">{$invoice['deliverytermTitle']} <span class="gray">{$invoice['deliverytermEntitle']}</span></td>
</tr>
<tr class="even">
	<td title="Port of Loading">装货地点：</td>
	<td class="middle">{$invoice['loadingCountryTitle']} {$invoice['loadingTitle']} <span class="gray">{$invoice['loadingCountryTitle']} {$invoice['loadingTitle']}</span></td>
	<td title="Shipment Date">交货日期：</td>
	<td class="middle"><span class="bold darkred">{$onBoardDate}</span></td>
</tr>
<tr class="odd">
	<td title="Port of Discharge">卸货地点：</td>
	<td class="middle">{$invoice['dischargeCountryTitle']} {$invoice['dischargeTitle']} <span class="gray">{$invoice['dischargeCountryEntitle']} {$invoice['dischargeEntitle']}</span></td>
	<td title="Date of Arrival">到达日期：</td>
	<td class="middle"><span class="bold darkred">{$dateOfArrival}</span></td>
</tr>
<tr class="odd">
	<td title="Final Destination">最终目的地：</td>
	<td class="middle">{$invoice['finalDestination']}</td>
	<td title="1st Carrier">1<sup>st</sup>货运公司：</td>
	<td class="middle">{$invoice['firstCarrier']}</td>
</tr>
<tr class="odd">
	<td title="Payment Term">支付条款：</td>
	<td class="middle">{$invoice['paymenttermTitle']} <span class="gray">{$invoice['paymenttermEntitle']}</span></td>
	<td title="2nd Carrier">2<sup>nd</sup>货运公司：</td>
	<td class="middle">{$invoice['secondCarrier']}</td>
</tr>
<tr class="even">
	<td valign="top" title="Commodity">报关商品：</td>
	<td>{$invoice['commodity']}</td>
	<td valign="top" title="Remark">备　　注：</td>
	<td>{$invoice['remark']}</td>
</tr>
</tbody>
</table>

<table class="hundred">
<thead>
<tr>
	<th colspan="12"><span class="right"><a href="/s.php?module=invoice&action=update&invoiceid={$invoice['invoiceid']}&step=item">新增与修改订单条目</a></span>订单信息 (Order Information)</th>
</tr>
</thead>
<tbody>
{$item['tr']}
</tbody>
</table>
<table class="hundred">
<thead>
<tr>
	<th colspan="8">费用信息 (Fee Information)</th>
</tr>
</thead>
<tbody>
{$feeitem['tr']}
</tbody>
</table>

<table class="hundred">
<thead>
<tr>
	<th colspan="4">折扣信息 (Discount Information)</th>
</tr>
</thead>
<tbody>
{$discounttr}
</tbody>
</table>
<table class="hundred">
<tbody>
<tr><td align="right">总计(Total Amount)：<span class="middle bold darkred">{$invoice['currencyTitle']} {$invoice['currencySymbol']}{$total}</span></td></tr>
</tbody>
</table>
EOF;
	}
	
	
	// 
	function view(){
		if(!$this->kclass->input['invoiceid'] > 0)$this->kclass->boinkIt('/s.php?module=invoice');
		$invoice = $this->kclass->DB->queryFirst("
			SELECT `invoice`.*, 
				region.encountry AS regionEncountry, region.country AS regionCountry, region.enstate AS regionEnstate, region.state AS regionState, region.encity AS regionEncity, region.city AS regionCity, 
				customer.title AS customerTitle, customer.address AS customerAddress, customer.linkman AS customerLinkman, customer.position AS customerPosition, customer.telephone AS customerTelephone, customer.fax AS customerFax, 
				shipmethod.title AS shipmethodTitle, shipmethod.entitle AS shipmethodEntitle, 
				loading.title AS loadingTitle, loading.entitle AS loadingEntitle, 
				loadingCountry.country AS loadingCountryTitle, loadingCountry.encountry AS loadingCountryEntitle, 
				discharge.title AS dischargeTitle, discharge.entitle AS dischargeEntitle, 
				dischargeCountry.country AS dischargeCountryTitle, dischargeCountry.encountry AS dischargeCountryEntitle, 
				paymentterm.title AS paymenttermTitle, paymentterm.entitle AS paymenttermEntitle, 
				deliveryterm.title AS deliverytermTitle, deliveryterm.entitle AS deliverytermEntitle, 
				currency.title AS currencyTitle, currency.symbol AS currencySymbol, currency.remark AS currencyRemark, 
				unit.title AS unitTitle, unit.entitle AS unitEntitle, unit.remark AS unitRemark, 
				m.username AS mname, c.username AS cname ,t.username AS tname,d.username AS dname
			FROM `invoice` 
			LEFT JOIN region ON (region.regionid=`invoice`.regionid) 
			LEFT JOIN customer ON (customer.customerid=`invoice`.customerid) 
			LEFT JOIN currency ON (currency.currencyid=`invoice`.currencyid) 
			LEFT JOIN unit ON (unit.unitid=`invoice`.unitid) 
			LEFT JOIN shipmethod ON (shipmethod.shipmethodid=`invoice`.shipmethodid) 
			LEFT JOIN port AS loading ON (loading.portid=`invoice`.loading) 
			LEFT JOIN `region` AS loadingCountry ON (loadingCountry.regionid=loading.regionid)
			LEFT JOIN port AS discharge ON (discharge.portid=`invoice`.discharge) 
			LEFT JOIN `region` AS dischargeCountry ON (dischargeCountry.regionid=discharge.regionid)
			LEFT JOIN paymentterm ON (paymentterm.paymenttermid=`invoice`.paymenttermid) 
			LEFT JOIN deliveryterm ON (deliveryterm.deliverytermid=`invoice`.deliverytermid) 
			LEFT JOIN `user` AS m ON (m.userid=invoice.modifier) 
			LEFT JOIN `user` AS c ON (c.userid=invoice.creator) 
			LEFT JOIN `user` AS t ON (t.userid=invoice.tracker)
			LEFT JOIN `user` AS d ON (d.userid=invoice.director)
			WHERE invoiceid='".$this->kclass->input['invoiceid']."'
		");
		if(!$invoice)	$this->kclass->boinkIt('/s.php?module=invoice&action=find');
		if($invoice['modifier']>0){
			$modified = '，'.$invoice['mname'].' 于 '.date('y-m-d H:i:s', $invoice['modified']).' 最后修改';
		}
		$created = date('y-m-d H:i:s', $invoice['created']);
		$attach = $this->kclass->getAttachs(array('module'=>'invoice', 'mid'=>$invoice['invoiceid']));
		if($attach!= false){
			$attachs = '<div class="clear">'.$attach.'</div>';
		}
		if($invoice['onBoardDate']>0){
			$onBoardDate=date('Y-m-d', $invoice['onBoardDate']);
		}else{
			$onBoardDate='--';
		}
		if($invoice['dateOfArrival']>0){
			$dateOfArrival=date('Y-m-d', $invoice['dateOfArrival']);
		}else{
			$dateOfArrival='--';
		}
		if($invoice['regionid']>0){
			if($invoice['regionEncity']!=''){
				$region = $invoice['regionEncity'].', ';
			}
			if($invoice['regionEnstate']!=''){
				$region .= $invoice['regionEnstate'].', <br>';
			}
			if($invoice['regionEncountry']!=''){
				$region .= $invoice['regionEncountry'];
			}
		}
		$total=0;
		$item = $this->kclass->relatedInvoiceItem(array('invoiceid'=>$invoice['invoiceid']));
		$total = $item['total'];
		$feeitem = $this->kclass->relatedFee(array('invoiceid'=>$invoice['invoiceid']));
		$total += $feeitem['total'];
		$discounts = $this->kclass->DB->query("SELECT * FROM `discount` WHERE killed=0 AND module='invoice' AND mid='".$invoice['invoiceid']."'");
		if($this->kclass->DB->numRows()){
			while($discount=$this->kclass->DB->fetchArray($discounts)){
				if($item['total']>0){
					$amount = 0;
					if($discount['sign']=='%'){
						$discount['discount'] = intVal($discount['discount']);
						$amount = ($item['total'] * $discount['discount'] / 100);
					}else{
						$amount = $discount['discount'];
					}
					if($discount['addition']=='+'){
						$total += $amount;
					}else{
						$total -= $amount;
					}
				}
				$discounttr .= '<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'even', 'odd').'">
				<td>'.$discount['reason'].'</td>
				<td align="right">'.$discount['addition'].''.$discount['discount'].''.$this->kclass->iif($discount['sign']=='%', $discount['sign'], '').'</td>
				<td align="right">'.$discount['addition'].number_format($amount, 2).'</td>
				<td align="right">'.$invoice['currencySymbol'].number_format($total, 2).'</td>
				</tr>';
			}
			$discounttr = '<tr><td title="Reason">折扣说明(理由)</td><td title="Discount" align="right">折扣</td><td title="Amount" align="right">计算折扣数</td><td title="Amount" align="right">折扣后总数</td></tr>'.$discounttr.'<tr><td colspan="12" align="right">折后计：<span class="bold">'.$invoice['currencyTitle'].' '.$invoice['currencySymbol'].number_format($total, 2).'</span></td></tr>';
		}else{
			$discounttr = '<tr><td class="gray"><i>无折扣信息</i></td></tr>';
		}
		$total = number_format($total, 2);
		if($invoice['refno']==''){
			$invoice['refno'] = '<i class="gray">None</i>';
		}
		
		$status = '';
		if(0==$invoice['status']){
			$status = '<span class="darkred">未审核　未审批</span>　（<a href="/s.php?module=invoice&action=auditForm&invoiceid='.$invoice['invoiceid'].'">审核</a>）';
		}else if(1==$invoice['status']){
			$status = '<span class="green">通过审核</span>　<span class="darkred">未审批</span>　（<a href="/s.php?module=invoice&action=approvalForm&invoiceid='.$invoice['invoiceid'].'">审批</a>）';
		}else{
			$status = '<span class="green">通过审核　通过审批</span>';
		}
		$reviseInfo = $this->kclass->returnReviseInfo(array('module'=>'invoice','mid'=>$invoice['invoiceid']));
		
		//$related=$this->kclass->relatedPackinglist(array('invoiceid'=>$invoice['invoiceid']));
//		$inquiry=$this->kclass->relatedPacking(array('productid'=>$invoice['productid'], 'options'=>1));
//		$quoting=$this->kclass->relatedQuoting(array('productid'=>$invoice['productid'], 'options'=>1));
$body = <<<EOF
<dl id="invoice" class="tabs">
	<dt>CI (商业发票)</dt>
	{$related['tabs']}
	<dd>
<div><span class="small darkred">{$reviseInfo['now']}{$reviseInfo['old']}</span><span class="small gray">当前CI状态：{$status}</span><span class="right normal"><a href="/s.php?module=invoice&action=update&invoiceid={$invoice['invoiceid']}">修改发票</a>　<a href="/s.php?module=invoice&action=remove&invoiceid={$invoice['invoiceid']}&rt=view">删除发票</a>　<a href="/p.php?module=printer&action=invoice&invoiceid={$invoice['invoiceid']}" target="_blank">打印CI</a></span></div>
<div class="bold darkred big" style="clear:both;"><span class="right small gray">由 {$invoice['cname']} 于 {$created} 建立{$modified}。</span>{$invoice['invoiceno']}<span class="gray small">　　　业务人员：<span class="darkred bold">{$invoice['dname']}</span></span>　　　<span class="gray small">跟单人员：<span class="darkred bold">{$invoice['tname']}</span></span></div>
<table class="hundred">
<thead>
<tr>
	<th colspan="4"><span class="right"><span class="normal">货物数量：</span>{$invoice['quantity']} {$invoice['unitTitle']} <span class="normal">总计(Total Amount)：</span><span class="darkred">{$invoice['currencyTitle']} {$invoice['currencySymbol']}{$total}</span></span>客户信息 (Customer Information)</th>
</tr>
</thead>
<tbody>
<tr class="odd">
	<td width="100">客户名称：<br>发票抬头：</td>
	<td><a href="/s.php?module=customer&action=view&customerid={$invoice['customerid']}" target="_blank">{$invoice['customerTitle']}</a><br>{$invoice['title']}</td>
	<td width="100">抬头地址：</td>
	<td>{$invoice['address']}<br>{$region}</td>
</tr>
</tbody>
</table>

<table class="hundred">
<thead>
<tr>
	<th colspan="4"><span class="right"><span class="normal" title="Currency">币种：</span>{$invoice['currencyTitle']} {$invoice['currencySymbol']} <span class="gray">{$invoice['currencyRemark']}</span></span>一般信息 (General Information)</th>
</tr>
</thead>
<tbody>
<tr class="odd">
	<td width="100" title="Shipment Method">运输方式：</td>
	<td class="middle">{$invoice['shipmethodTitle']} <span class="gray">{$invoice['shipmethodEntitle']}</span></td>
	<td width="100" title="Delivery Term">交货条款：</td>
	<td class="middle">{$invoice['deliverytermTitle']} <span class="gray">{$invoice['deliverytermEntitle']}</span></td>
</tr>
<tr class="even">
	<td title="Port of Loading">装货地点：</td>
	<td class="middle">{$invoice['loadingCountryTitle']} {$invoice['loadingTitle']} <span class="gray">{$invoice['loadingCountryTitle']} {$invoice['loadingTitle']}</span></td>
	<td title="Shipment Date">交货日期：</td>
	<td class="middle"><span class="bold darkred">{$onBoardDate}</span></td>
</tr>
<tr class="odd">
	<td title="Port of Discharge">卸货地点：</td>
	<td class="middle">{$invoice['dischargeCountryTitle']} {$invoice['dischargeTitle']} <span class="gray">{$invoice['dischargeCountryEntitle']} {$invoice['dischargeEntitle']}</span></td>
	<td title="Date of Arrival">到达日期：</td>
	<td class="middle"><span class="bold darkred">{$dateOfArrival}</span></td>
</tr>
<tr class="odd">
	<td title="Final Destination">最终目的地：</td>
	<td class="middle">{$invoice['finalDestination']}</td>
	<td title="1st Carrier">1<sup>st</sup>货运公司：</td>
	<td class="middle">{$invoice['firstCarrier']}</td>
</tr>
<tr class="odd">
	<td title="Payment Term">支付条款：</td>
	<td class="middle">{$invoice['paymenttermTitle']} <span class="gray">{$invoice['paymenttermEntitle']}</span></td>
	<td title="2nd Carrier">2<sup>nd</sup>货运公司：</td>
	<td class="middle">{$invoice['secondCarrier']}</td>
</tr>
<tr class="even">
	<td valign="top" title="Commodity">报关商品：</td>
	<td>{$invoice['commodity']}</td>
	<td valign="top" title="Remark">备　　注：</td>
	<td>{$invoice['remark']}</td>
</tr>
</tbody>
</table>

<table class="hundred">
<thead>
<tr>
	<th colspan="12"><span class="right"><a href="/s.php?module=invoice&action=update&invoiceid={$invoice['invoiceid']}&step=item">新增与修改订单条目</a></span>订单信息 (Order Information)</th>
</tr>
</thead>
<tbody>
{$item['tr']}
</tbody>
</table>
<table class="hundred">
<thead>
<tr>
	<th colspan="8">费用信息 (Fee Information)</th>
</tr>
</thead>
<tbody>
{$feeitem['tr']}
</tbody>
</table>

<table class="hundred">
<thead>
<tr>
	<th colspan="4">折扣信息 (Discount Information)</th>
</tr>
</thead>
<tbody>
{$discounttr}
</tbody>
</table>
<table class="hundred">
<tbody>
<tr><td align="right">总计(Total Amount)：<span class="middle bold darkred">{$invoice['currencyTitle']} {$invoice['currencySymbol']}{$total}</span></td></tr>
</tbody>
</table>
	</dd>
	{$related['panel']}
</dl>
{$attachs}
EOF;
		$this->kclass->page['onload'] .= "dc.tabs({'id':'invoice'});dc.tabhover()";
		$this->kclass->page['title'] .= ' - '.$invoice['invoiceno'];
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl.' - 发票信息', 'right' => '', 'body'=>$body));
	}
	//
	function insert(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			if($this->kclass->input['step']==2){
				$customer = $this->kclass->DB->queryFirst("SELECT * FROM `customer` WHERE customerid='".$this->kclass->input['customerid']."'");
				if(!$customer){
					$e .= '<li>请选择返回列表刷新，重新进入新建页面，如此问题连续出现，请联系管理员。</li>';
				}
				if(strlen($this->kclass->input['title']) < 3){
					$e .= '<li>请选择发票中的 客户名称 抬头。</li>';
				}
				if(strlen($this->kclass->input['address']) < 3){
					$e .= '<li>请选择发票中的 客户地址。</li>';
				}
				if($this->kclass->input['countryid'] <= 0 AND $this->kclass->input['provinceid'] <= 0 AND $this->kclass->input['cityid'] <= 0){
					$e .= '<li>请填写发票中客户的所属 国家地区。</li>';
				}
				$this->kclass->input['quantity'] = intval($this->kclass->input['quantity']);
				if($this->kclass->input['tracker'] <= 0 ){
					$e .= '<li>请选择跟单人员。</li>';
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
		if($this->kclass->input['step']==2){
			$invoiceno = $this->kclass->input['invoiceno'];
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
			if($this->kclass->input['cityid']>0){
				$regionid=$this->kclass->input['cityid'];
			}elseif($this->kclass->input['provinceid']>0){
				$regionid=$this->kclass->input['provinceid'];
			}else{
				$regionid=$this->kclass->input['countryid'];
			}

			// invoice
			$this->kclass->DB->query("
				INSERT INTO `invoice` (`customerid`, `orderid`, `invoiceno`, `title`, `address`, `regionid`, `loading`, `discharge`, `currencyid`, `deliverytermid`, `paymenttermid`, `shipmethodid`, `onBoardDate`, `dateOfArrival`, `finalDestination`, `firstCarrier`, `secondCarrier`, `quantity`, `unitid`, `commodity`, `remark`, `modified`, `director`,`tracker`, `creator`, `created`) 
				VALUES ('".$customer['customerid']."', '".$this->kclass->input['orderid']."', '".$invoiceno."', '".$this->kclass->input['title']."', '".$this->kclass->input['address']."', '".$regionid."', '".$this->kclass->input['loading']."', '".$this->kclass->input['discharge']."', '".$this->kclass->input['currencyid']."', '".$this->kclass->input['deliverytermid']."', '".$this->kclass->input['paymenttermid']."', '".$this->kclass->input['shipmethodid']."', '".$onBoardDate."', '".$dateOfArrival."', '".$this->kclass->input['finalDestination']."', '".$this->kclass->input['firstCarrier']."', '".$this->kclass->input['secondCarrier']."', '".$this->kclass->input['quantity']."', '".$this->kclass->input['unitid']."', '".$this->kclass->input['commodity']."', '".$this->kclass->input['remark']."', '".TIMENOW."', '".$this->kclass->input['director']."','".$this->kclass->input['tracker']."', '".$this->kclass->user['userid']."', '".TIMENOW."')
			");
			$invoiceid = $this->kclass->DB->insertID();
			// 订单明细
			for($i = 1; $i < (count($this->kclass->input['olItemid'])+1); $i++){
				$this->kclass->input['olQuantity'][$i]=intVal($this->kclass->input['olQuantity'][$i]);
				$this->kclass->input['olPrice'][$i]=floatVal($this->kclass->input['olPrice'][$i]);
				$this->kclass->input['olDiscount'][$i]=intVal($this->kclass->input['olDiscount'][$i]);
				if($this->kclass->input['olSelect'][$i]>0 AND $this->kclass->input['olSelect'][$i] == $this->kclass->input['olItemid'][$i] AND $this->kclass->input['olQuantity'][$i]>0 AND $this->kclass->input['olPrice'][$i] >=0 ){
					if($this->kclass->input['olDiscount'][$i]<1 OR $this->kclass->input['olDiscount'][$i]>100){
						$this->kclass->input['olDiscount'][$i]=100;
					}
					$amount = $this->kclass->input['olQuantity'][$i] * $this->kclass->input['olPrice'][$i];
					if($this->kclass->input['olDiscount'][$i]<100){
						$amount = $amount * $this->kclass->input['olDiscount'][$i] / 100;
					}
					$amount = number_format($amount, 2, '.', '');

					$item = $this->kclass->DB->queryFirst("SELECT * FROM `item` WHERE itemid='".$this->kclass->input['olItemid'][$i]."'");
					$this->kclass->DB->query("
						INSERT INTO `item` (`module`, `mid`, `orderid`,`productid`, `productitemid`,`caption`,`customerOrderNo`,`articleNo`,`brandid`,`packingid`,`quantity`,`discount`,`unitid`,`price`,`amount`,`modified`,`creator`,`created`)
						VALUES (
						'invoice', '".$invoiceid."', '".$this->kclass->input['orderid']."', '".$item['productid']."','".$item['productitemid']."','".$item['caption']."', '".$this->kclass->input['olCustomerOrderNo'][$i]."',  '".$this->kclass->input['olArticleNo'][$i]."', '".$item['brandid']."', '".$item['packingid']."', '".$this->kclass->input['olQuantity'][$i]."', '".$this->kclass->input['olDiscount'][$i]."', '".$this->kclass->input['olUnitid'][$i]."', '".$this->kclass->input['olPrice'][$i]."', '".$amount."', '".TIMENOW."', '".$this->kclass->user['userid']."', '".TIMENOW."')
					");
				}
			}
			// fee & discount
			$this->kclass->updateFee(array('module'=>'invoice','mid'=>$invoiceid));
			$this->kclass->updateDiscount(array('module'=>'invoice','mid'=>$invoiceid));
			// insert addtachs
			$this->kclass->updateAttachs(array('module'=>'invoice', 'mid'=>$invoiceid));

			$this->kclass->messager(array(
				'title' => '新建发票',
				'text' => '发票 <b>'.$invoiceno.'</b> 已新建成功! '.$this->kclass->iif($this->kclass->input['options']==1, '<span class="bold darkred">现在进入下一步，从该客户全部订单的明细中选择(此步骤也可以后续再操作)。</span>', ''),
				'url' => '/s.php?module=invoice&action='.$this->kclass->iif($this->kclass->input['options']==2, 'view', 'add&step=3&orderid='.$this->kclass->input['orderid']).'&invoiceid='.$invoiceid,
				'sec' => 2
			));
		}
	}

	//
	function add(){
		$dateStr = date("Ymd");
		$year=((int)substr($dateStr,0,4));//取得年份
		$month=((int)substr($dateStr,4,2));//取得月份
		$day=((int)substr($dateStr,6,2));//取得几号
		$dateStamp = mktime(0,0,0,$month,$day,$year);
		
		$orderNum = $this->kclass->DB->queryFirst("SELECT COUNT(invoiceid) AS num FROM `invoice` WHERE created > ".$dateStamp);
		$noStr = '';
		$orderNum['num']++;
		if(strlen($orderNum['num']) < 2 AND strlen($orderNum['num']) > 0){
			$noStr = "0".$orderNum['num'];
		}elseif(strlen($orderNum['num']) == 2){
			$noStr = $orderNum['num'];
		}else{
			exit("订单数量有误！");
		}
		$yearStr = $dateStr.$noStr;
		$CIStr = 'CI'.$yearStr;
		
		if($this->kclass->input['step'] == 2 AND $this->kclass->input['customerid']>0 AND $this->kclass->input['orderid']>0){
			$order=$this->kclass->DB->queryFirst("SELECT * FROM `order` WHERE orderid='".$this->kclass->input['orderid']."'");
			if($order['status']!=2){
				$this->kclass->messager(array(
					'title' => '新建发票',
					'text' => '此订单还未通过审批',
					'url' => REFERER,
					'sec' => 3
				));
			}
			$customer = $this->kclass->DB->queryFirst("
					SELECT `customer`.customerid, `customer`.title, `customer`.cntitle, `customer`.address, `customer`.regionid, `customer`.linkman, `customer`.position, `customer`.telephone, `customer`.fax 
					FROM `order` 
					LEFT JOIN `customer` 
					ON (`customer`.customerid=`order`.customerid) 
					WHERE orderid='".$this->kclass->input['orderid']."'");
			
			$director = $this->kclass->chooserTracker(array('name'=>'director','condition'=>'positionid=7','width'=>80,'selectedid'=>$order['director']));
			$tracker = $this->kclass->chooserTracker(array('name'=>'tracker','condition'=>'positionid=7','width'=>80,'selectedid'=>$order['tracker']));
			$loading = $this->kclass->chooserPort(array('name'=>'loading', 'hasBlank'=>1, 'width'=>450, 'selectedid'=>$order['loading']));
			$discharge = $this->kclass->chooserPort(array('name'=>'discharge', 'hasBlank'=>1, 'width'=>450, 'selectedid'=>$order['discharge']));
			$paymentterm = $this->kclass->chooserPaymentterm(array('name'=>'paymenttermid', 'hasBlank'=>1, 'width'=>450, 'selectedid'=>$order['paymenttermid']));
			$deliveryterm = $this->kclass->chooserDeliveryterm(array('name'=>'deliverytermid', 'hasBlank'=>1, 'width'=>450, 'selectedid'=>$order['deliverytermid']));
			$shipmethod = $this->kclass->chooserShipmethod(array('name'=>'shipmethodid', 'hasBlank'=>1, 'width'=>450, 'selectedid'=>$order['shipmethodid']));
			$currency = $this->kclass->chooserCurrency(array('name'=>'currencyid', 'hasBlank'=>1, 'width'=>100, 'selectedid'=>$order['currencyid']));
			$unit = $this->kclass->chooserUnit(array('name'=>'unitid', 'hasBlank'=>1, 'width'=>150, 'selectedid'=>2));
			
			//订单条目明细
			$i = 1;
			$items = $this->kclass->DB->query("
				SELECT item.*,
					productitem.attributevalue,
					productitem.mid AS productId,
					brand.title AS brand,
					packing.title AS packing,
					unit.title AS unit
				FROM `item`
				LEFT JOIN `productitem` ON (productitem.productitemid=item.productitemid)
				LEFT JOIN packing ON (packing.packingid=item.packingid)
				LEFT JOIN brand ON (brand.brandid=item.brandid)
				LEFT JOIN unit ON (unit.unitid=item.unitid)
				WHERE `item`.killed=0 AND `item`.module='order' AND `item`.mid='".$this->kclass->input['orderid']."'
				ORDER BY itemid ASC, modified ASC
			");
			if($this->kclass->DB->numRows()){
				$k=1;
				while($item = $this->kclass->DB->fetchArray($items)){
					$selectStr = '';
					if(!empty($item['attributevalue'])){
						$valueStr = explode(",",$item['attributevalue']);
						
						$product=$this->kclass->DB->queryFirst("
							SELECT `product`.title,`product`.entitle
							FROM  `product`
							WHERE productid='".$item['productId']."'
						");
						
						$order=$this->kclass->DB->queryFirst("
							SELECT `order`.*
							FROM  `order`
							WHERE orderid='".$item['mid']."'
						");
						
						$attributes=$this->kclass->DB->query("
							SELECT an.nameid,an.title AS name,
								av.valueid,av.title AS value
							FROM  attributename AS an
							LEFT JOIN attributevalue AS av ON (av.nameid=an.nameid)
							WHERE an.killed=0 AND av.killed=0 AND an.module='product' AND an.mid='".$item['productId']."'
							ORDER BY an.nameid ASC
						");
						
						$nameId=$title=$value=$attributeCount=array();
						if($this->kclass->DB->numRows()){
							while($attribute=$this->kclass->DB->fetchArray($attributes)){
								if(in_array($attribute['nameid'],$nameId)){
									$title[$attribute['nameid']]=$attribute['name'];
									$value[$attribute['nameid']][]=$attribute['value'];
									$valueId[$attribute['nameid']][]=$attribute['valueid'];
									$attributeCount[$attribute['nameid']]++;
								}else{
									$nameId[]=$attribute['nameid'];
									$title[$attribute['nameid']]=$attribute['name'];
									$value[$attribute['nameid']][]=$attribute['value'];
									$valueId[$attribute['nameid']][]=$attribute['valueid'];
									$attributeCount[$attribute['nameid']]=1;
								}
							}
						}
						foreach ($nameId as $key=>$val){
							$selectStr .= ''.$title[$val].'：';
							for($n=0;$n<count($value[$val]);$n++){
								if($valueId[$val][$n]==$valueStr[$key]){
									$selectStr .= '<span class="darkred">'.$value[$val][$n].'</span>';
								}
							}
							$selectStr .= '　';
						}
					}else{
						$product=$this->kclass->DB->queryFirst("
							SELECT `product`.title,`product`.entitle,`product`.materialid
							FROM  `product`
							WHERE productid='".$item['productid']."'
						");
						$item['productId'] = $item['productid'];
						$material = $this->kclass->DB->queryFirst("
							SELECT `material`.materialid,`material`.standard
							FROM  `material`
							WHERE materialid='".$product['materialid']."'
						");
						$selectStr = $material['standard'];
					}
					
					$orderList .= '<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').'"><td>'.$i.'</td><td title="创建时间：'.date('Y-m-d', $item['orderCreated']).'"><a href="/s.php?module=order&action=view&orderid='.$order['orderid'].'" target="_blank">'.$order['orderno'].'</a></td> 
					<td><input type="hidden" name="olItemid['.$i.']" value="'.$item['itemid'].'"><a href="/s.php?module=product&action=view&productid='.$item['productId'].'" target="_blank">'.$product['title'].'</a></td>
					</td><td colspan="2">'.$selectStr.'</td>
					<td><input type="text" name="olDiscount['.$i.']" value="'.$item['discount'].'" size="3"></td>
					<td><input type="text" name="olQuantity['.$i.']" value="'.$item['quantity'].'" size="6"></td>
					<td>'.$this->kclass->chooserUnit(array('name'=>'olUnitid['.$i.']', 'hasBlank'=>1, 'width'=>100, 'selectedid'=>$item['unitid'])).'</td>
					<td><input type="text" name="olPrice['.$i.']" value="'.$item['price'].'" size="6"></td>
					<td><input type="text" name="olOrdering['.$i.']" value="'.$item['ordering'].'" size="1"></td>
					<td><input type="checkbox" name="olSelect['.$i.']" value="'.$item['itemid'].'" checked="true"></td>
					</tr>';
					$i++;
				}
			}
			$fee=$this->kclass->formFee(array('module'=>'invoice'));
			$discount=$this->kclass->formDiscount(array('module'=>'invoice'));
			$upload = $this->kclass->upload(array('title'=>'相关附件：<span class="small darkred">请注意：发票里的附件不会出现在打印页面，仅在系统内部显示和存档。</span>'));
			$region = $this->kclass->chooserRegion(array('regionid'=>$customer['regionid'], 'hasBlank'=>1));
$body = <<<EOF
<form action="/s.php?module=invoice&action=insert" name="invoice" method="post">
<input type="hidden" name="module" value="invoice">
<input type="hidden" name="action" value="insert">
<input type="hidden" name="step" value="2">
<input type="hidden" name="orderid" value="{$this->kclass->input['orderid']}">
<input type="hidden" name="customerid" value="{$customer['customerid']}">
<table class="hundred">
<thead>
<tr>
	<th colspan="4">新建发票：发票信息</th>
</tr>
</thead>
<tbody>
<tr class="odd">
	<td title="Invoice No">发票编号：</td>
	<td><!--<span class="middle bold darkred">{$invoiceno}</span> <span class="gray small">此为预估编号，保存后才可确定</span>--><input type="text" style="width:450px;" name="invoiceno" value="{$CIStr}"></td>
	<td>币　　种：<span class="red bold">*</span></td>
	<td><input type="hidden" name="refno" value="">{$currency}　　业务人员：<span class="red bold">*</span> {$director}　　跟单人员：<span class="red bold">*</span> {$tracker}</td></td>
</tr>
</tbody>
<thead>
<tr>
	<th colspan="4">客户 <span class="bold darkred">{$customer['title']}</span> 的信息：</th>
</tr>
</thead>
<tbody>
<tr class="odd">
	<td title="Customer Title">客户名称：<span class="red bold">*</span></td>
	<td><input type="text" style="width:340px" name="title" value="{$customer['title']}"> <span class="gray small">客户需要的发票抬头</span></td>
	<td title="Customer Address">客户地址：<span class="red bold">*</span></td>
	<td><input type="text" style="width:330px" name="address" value="{$customer['address']}"> <span class="gray small">国家/省 在下面选择</span></td>
</tr>
<tr class="even">
	<td title="Payment Term">支付条款：</td>
	<td>{$paymentterm}</td>
	<td>国家地区：<span class="red bold">*</span></td>
	<td>{$region}</td>
</tr>
<tr class="odd">
	<td title="Shipment Method">运送方式：</td>
	<td>{$shipmethod}</td>
	<td title="Delivery Term">交货条款：</td>
	<td>{$deliveryterm}</td>
</tr>
<tr class="even">
	<td title="Port or Loading">装货地点：</td>
	<td>{$loading}</td>
	<td title="On Board Date">装船日期：</td>
	<td><input type="text" style="width:150px" id="onBoardDate" name="onBoardDate" value="{$invoice['onBoardDate']}"> <span class="gray small">装船/车/飞机的日期</span></td>
</tr>
<tr class="odd">
	<td title="Port of Discharge">卸货地点：</td>
	<td>{$discharge}</td>
	<td title="Date of Arrival">到达日期：</td>
	<td><input type="text" style="width:150px" id="dateOfArrival" name="dateOfArrival" value="{$invoice['dateOfArrival']}"> <span class="gray small">抵达卸货地点的日期</span></td>
</tr>
<tr class="even">
	<td title="Final Destination">最终目的地：</td>
	<td><input type="text" style="width:150px" name="finalDestination" value="{$invoice['finalDestination']}"> <span class="gray small">如果卸货地点不是最终目的地城市，则需填写</span></td>
	<td title="1st. Carrier">1<sup>st</sup>货运公司：</td>
	<td><input type="text" style="width:150px" name="firstCarrier" value="{$invoice['firstCarrier']}"> <span class="gray small">第一阶段负责运输的公司，如果只有一个，此处留空</span></td>
</tr>
<tr class="odd">
	<td valign="top" title="Labeling">包装箱数：</td>
	<td><input type="text" style="width:70px" name="quantity" value="{$invoice['quantity']}">{$unit}<span class="gray small">表示运输时的单体数量，如多少箱</span></td>
	<td title="2nd. Carrier">2<sup>nd</sup>货运公司：</td>
	<td><input type="text" style="width:150px" name="secondCarrier" value="{$invoice['secondCarrier']}"> <span class="gray small">第二阶段负责运输的公司</span></td>
</tr>
<tr class="even">
	<td title="Commodity">报关商品：<div class="gray small" style="width:80px">报关所使用的商品名称</div></td>
	<td><textarea name="commodity" style="width:444px;height:100px">{$invoice['commodity']}</textarea></td>
	<td valign="top" title="Remark">备　　注：</td>
	<td><textarea name="remark" style="width:444px;height:100px">{$invoice['remark']}</textarea></td>
</tr>
</tbody>
</table>
<table class="hundred">
<thead>
<tr>
	<th colspan="12">订单条目明细表（<span class="darkred small">如需要增加更多订单，可在保存后，使用“增加订单条目”以增加其它订单的条目。</span>）</th>
</tr>
</thead>
<tbody class="small">
<tr class="even">
	<td>ID</td><td>订单号</td><td>产品或配件编号/名称</td><td colspan="2">产品或配件特有属性</td><td>折扣%</td><td>数量<span class="red bold">*</span></td><td>单位<span class="red bold">*</span></td><td>单价<span class="red bold">*</span></td><td>排序</td><td>选</td>
</tr>
{$orderList}
<tr>
	<td colspan="12" class="gray small">注：①条目如<b>选择</b>则至少有“数量”、“单位”、“单价”等四个项目，该条明细才会被保存。③折扣值范围“1-100”，不填默认为“100”。</td>
</tr>
</tbody>
</table>
{$fee}
{$discount}
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
<tr class="odd">
	<td colspan="4" align="center">
		<input type="submit" id="submitButton" value="  保存  " accesskey="s">　　
		<input type="reset" value="  复位  ">
	</td>
</tr>
</tbody>
</table>
</form>
EOF;
			$this->kclass->page['title'] .= ' - 新建发票';
			$this->kclass->page['onload'] .= 'dc.tabhover();var dates=$(\'#onBoardDate,#dateOfArrival\').datepicker({onSelect:function(selectedDate){var option=this.id==\'onBoardDate\'?\'minDate\':\'maxDate\',instance=$(this).data(\'datepicker\'),date=$.datepicker.parseDate(instance.settings.dateFormat || $.datepicker._defaults.dateFormat,selectedDate,instance.settings);dates.not(this).datepicker(\'option\',option,date);}});';
			$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 新建发票', 'right' => '<a href="/s.php?module=invoice">返回列表</a>', 'body'=>$body));
		}else if(($this->kclass->input['step'] == 1 OR $this->kclass->input['step'] == 2) AND $this->kclass->input['customerid']>0 ){
			$order=$this->kclass->chooserOrder(array('module'=>'order','name'=>'orderid','hasBlank'=>1,'topname'=>'请选择此客户的订单','type'=>'PI','customerid'=>$this->kclass->input['customerid'],'width'=>350));
			$body = <<<EOF
<form action="/s.php?module=invoice&action=add" name="invoice" method="get">
<input type="hidden" name="module" value="invoice">
<input type="hidden" name="action" value="add">
<input type="hidden" name="step" value="2">
<input type="hidden" name="customerid" value="{$this->kclass->input['customerid']}">
<table>
<thead>
<tr>
	<th colspan="4">新建发票：选择客户的订单</th>
</tr>
</thead>
<tbody>
<tr class="odd">
	<td title="">选择客户的订单：<span class="red bold">*</span></td>
	<td>{$order}</td>
</tr>
<tr class="odd">
	<td colspan="4" align="center">
		<input type="submit" id="submitButton" value="  下一步  " accesskey="s">　　
		<input type="reset" value="  复位  ">
	</td>
</tr>
</tbody>
</table>
</form>			
EOF;
			$this->kclass->page['title'] .= ' - 新建发票';
			$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 新建发票', 'right' => '<a href="/s.php?module=invoice">返回列表</a>', 'body'=>$body));
		}else{
			if($this->kclass->input['orderid']>0){
				$order=$this->kclass->DB->queryFirst("SELECT orderid, customerid FROM `order` WHERE orderid='".$this->kclass->input['orderid']."'");
			}
			if($order['customerid']>0){
				$customerid = $order['customerid'];
			}else{
				$customerid = $this->kclass->input['customerid'];
			}
			$customer = $this->kclass->chooserCustomer(array('hName'=>'customerid','name'=>'customer','width'=>350));
			$body = <<<EOF
<form action="/s.php?module=invoice&action=add" name="invoice" method="get">
<input type="hidden" name="module" value="invoice">
<input type="hidden" name="action" value="add">
<input type="hidden" name="step" value="1">
<table>
<thead>
<tr>
	<th colspan="4">新建发票：选择客户</th>
</tr>
</thead>
<tbody>
<tr class="odd">
	<td title="">选择客户：<span class="red bold">*</span></td>
	<td>{$customer}</td>
</tr>
<tr class="odd">
	<td colspan="4" align="center">
		<input type="submit" id="submitButton" value="  下一步  " accesskey="s">　　
		<input type="reset" value="  复位  ">
	</td>
</tr>
</tbody>
</table>
</form>
EOF;
			$this->kclass->page['title'] .= ' - 新建发票';
			$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 新建发票', 'right' => '<a href="/s.php?module=invoice">返回列表</a>', 'body'=>$body));
		}

	}

	//
	function doupdate(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			$invoice = $this->kclass->DB->queryFirst("SELECT invoiceid, invoiceno FROM `invoice` WHERE killed=0 AND invoiceid='".$this->kclass->input['invoiceid']."'");
			if(!$invoice){
				$e .= '<li>您要编辑的发票并不存在，请返回列表刷新后再操作。</li>';
			}
			if($this->kclass->input['step']=='item'){

			}else{
				if(strlen($this->kclass->input['title']) < 3){
					$e .= '<li>请选择发票中的 客户名称 抬头。</li>';
				}
				if(strlen($this->kclass->input['address']) < 3){
					$e .= '<li>请选择发票中的 客户地址。</li>';
				}
				if($this->kclass->input['countryid'] <= 0 AND $this->kclass->input['provinceid'] <= 0 AND $this->kclass->input['cityid'] <= 0){
					$e .= '<li>请填写发票中客户的所属 国家地区。</li>';
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
		
		//获取历史版本信息
		$invoiceHtml = '';
		if(1==$this->kclass->input['history']){
			$invoiceHtml = $this->returnInvoiceHtml();
		}
		if($this->kclass->input['step']=='item'){
			$orderid=array();
			for($i = 1; $i < (count($this->kclass->input['orgItemid'])+1); $i++){
				if($this->kclass->input['orgSelect'][$i]>0 AND $this->kclass->input['orgSelect'][$i]==$this->kclass->input['orgItemid'][$i]){
					$this->kclass->DB->query("UPDATE `item` SET killed='".TIMENOW."' WHERE itemid='".$this->kclass->input['orgItemid'][$i]."'");
				}elseif($this->kclass->input['orgQuantity'][$i]>0 AND $this->kclass->input['orgPrice'][$i] >=0){
					$this->kclass->input['orgQuantity'][$i]=intVal($this->kclass->input['orgQuantity'][$i]);
					$this->kclass->input['orgPrice'][$i]=floatVal($this->kclass->input['orgPrice'][$i]);
					$this->kclass->input['orgDiscount'][$i]=intVal($this->kclass->input['orgDiscount'][$i]);
					if($this->kclass->input['orgDiscount'][$i]<1 OR $this->kclass->input['orgDiscount'][$i]>100){
						$this->kclass->input['orgDiscount'][$i]=100;
					}
					$amount = $this->kclass->input['orgQuantity'][$i] * $this->kclass->input['orgPrice'][$i];
					if($this->kclass->input['orgDiscount'][$i]<100){
						$amount = $amount * $this->kclass->input['orgDiscount'][$i] / 100;
					}
					$amount = number_format($amount, 2, '.', '');
					$this->kclass->DB->query("
						UPDATE `item` 
						SET customerOrderNo='".$this->kclass->input['orgCustomerOrderNo'][$i]."', 
							articleNo='".$this->kclass->input['orgArticleNo'][$i]."', 
							discount='".$this->kclass->input['orgDiscount'][$i]."', 
							quantity='".$this->kclass->input['orgQuantity'][$i]."', 
							unitid='".$this->kclass->input['orgUnitid'][$i]."', 
							price='".$this->kclass->input['orgPrice'][$i]."', 
							amount='".$amount."', 
							ordering='".$this->kclass->input['orgOrdering'][$i]."'
						WHERE itemid='".$this->kclass->input['orgItemid'][$i]."'
					");
					if(!in_array($this->kclass->input['orgOrderid'][$i], $orderid)){
						$orderid[] = $this->kclass->input['orgOrderid'][$i];
					}
				}
			}
			for($i = 1; $i < (count($this->kclass->input['olItemid'])+1); $i++){
				$this->kclass->input['olQuantity'][$i]=intVal($this->kclass->input['olQuantity'][$i]);
				$this->kclass->input['olPrice'][$i]=floatVal($this->kclass->input['olPrice'][$i]);
				$this->kclass->input['olDiscount'][$i]=intVal($this->kclass->input['olDiscount'][$i]);
				if($this->kclass->input['olSelect'][$i]>0 AND $this->kclass->input['olSelect'][$i] == $this->kclass->input['olItemid'][$i] AND $this->kclass->input['olQuantity'][$i]>0 AND $this->kclass->input['olPrice'][$i] >=0 ){
					if($this->kclass->input['olDiscount'][$i]<1 OR $this->kclass->input['olDiscount'][$i]>100){
						$this->kclass->input['olDiscount'][$i]=100;
					}
					$amount = $this->kclass->input['olQuantity'][$i] * $this->kclass->input['olPrice'][$i];
					if($this->kclass->input['olDiscount'][$i]<100){
						$amount = $amount * $this->kclass->input['olDiscount'][$i] / 100;
					}
					$amount = number_format($amount, 2, '.', '');

					$item = $this->kclass->DB->queryFirst("SELECT * FROM `item` WHERE itemid='".$this->kclass->input['olItemid'][$i]."'");
					$this->kclass->DB->query("
						INSERT INTO `item` (`module`, `mid`, `productid`,`productitemid`,`orderid`,`title`,`model`,`caption`,`customerOrderNo`,`articleNo`,`brandid`,`packingid`,`quantity`,`discount`,`unitid`,`price`,`amount`,`modified`,`creator`,`created`)
						VALUES (
						'invoice', '".$this->kclass->input['invoiceid']."', '".$item['productid']."', '".$item['productitemid']."','".$this->kclass->input['olOrderid'][$i]."', '".$item['title']."', '".$item['model']."', '".$item['caption']."',  '".$this->kclass->input['olCustomerOrderNo'][$i]."',  '".$this->kclass->input['olArticleNo'][$i]."', '".$item['brandid']."', '".$item['packingid']."', '".$this->kclass->input['olQuantity'][$i]."', '".$this->kclass->input['olDiscount'][$i]."', '".$this->kclass->input['olUnitid'][$i]."', '".$this->kclass->input['olPrice'][$i]."', '".$amount."', '".TIMENOW."', '".$this->kclass->user['userid']."', '".TIMENOW."')
					");
					if(!in_array($this->kclass->input['olOrderid'][$i], $orderid)){
						$orderid[] = $this->kclass->input['olOrderid'][$i];
					}
				}
			}
			if(count($orderid)>0){
				$this->kclass->DB->query("UPDATE `invoice` SET orderid='".implode(',', $orderid)."' WHERE invoiceid='".$this->kclass->input['invoiceid']."'");
			}
			$this->kclass->messager(array(
				'title' => '新增与修改发票订单条目',
				'text' => '发票 <b>'.$invoiceno.'</b> 的明细内容已经更新! ',
				'url' => '/s.php?module=invoice&action=view&invoiceid='.$this->kclass->input['invoiceid'],
				'sec' => 2
			));
		}else{
			$onBoardDate = 0;
			if($this->kclass->input['onBoardDate'] != ''){
				$sd = explode('-', $this->kclass->input['onBoardDate']);
				$onBoardDate = mktime(0,0,0,$sd[1],$sd[2],$sd[0]);
			}
			$dateOfArrival = 0;
			if($this->kclass->input['dateOfArrival'] != ''){
				$dd = explode('-', $this->kclass->input['dateOfArrival']);
				$dateOfArrival = mktime(0,0,0,$dd[1],$dd[2],$dd[0]);
			}
			if($this->kclass->input['cityid']>0){
				$regionid=$this->kclass->input['cityid'];
			}elseif($this->kclass->input['provinceid']>0){
				$regionid=$this->kclass->input['provinceid'];
			}else{
				$regionid=$this->kclass->input['countryid'];
			}

			$this->kclass->DB->query("
				UPDATE `invoice`
				SET `refno`='".$this->kclass->input['refno']."', 
					`title`='".$this->kclass->input['title']."', 
					`address`='".$this->kclass->input['address']."', 
					`regionid`='".$regionid."', 
					`loading`='".$this->kclass->input['loading']."', 
					`discharge`='".$this->kclass->input['discharge']."', 
					`currencyid`='".$this->kclass->input['currencyid']."', 
					`deliverytermid`='".$this->kclass->input['deliverytermid']."', 
					`paymenttermid`='".$this->kclass->input['paymenttermid']."', 
					`shipmethodid`='".$this->kclass->input['shipmethodid']."', 
					`onBoardDate`='".$onBoardDate."', 
					`dateOfArrival`='".$dateOfArrival."', 
					`finalDestination`='".$this->kclass->input['finalDestination']."', 
					`firstCarrier`='".$this->kclass->input['firstCarrier']."', 
					`secondCarrier`='".$this->kclass->input['secondCarrier']."', 
					`quantity`='".$this->kclass->input['quantity']."', 
					`unitid`='".$this->kclass->input['unitid']."', 
					`commodity`='".$this->kclass->input['commodity']."', 
					`remark` = '".$this->kclass->input['remark']."', 
					`status` = '0',
					`modified` = '".TIMENOW."', 
					`modifier` = '".$this->kclass->user['userid']."',
					`director` = '".$this->kclass->input['director']."',
					`tracker` = '".$this->kclass->input['tracker']."'
				WHERE invoiceid='".$invoice['invoiceid']."'
			");
			// fee & discount
			$this->kclass->updateFee(array('module'=>'invoice','mid'=>$invoice['invoiceid']));
			$this->kclass->updateDiscount(array('module'=>'invoice','mid'=>$invoice['invoiceid']));
			if(1==$this->kclass->input['history']){
				$this->kclass->insertReviseInfo(array('module'=>'invoice', 'mid'=>$invoice['invoiceid'],'html'=> $invoiceHtml));
			}
			$this->kclass->updateAttachs(array('module'=>'invoice', 'mid'=>$invoice['invoiceid']));
			$this->kclass->messager(array(
				'title' => '修改发票',
				'text' => '发票 <b>'.$invoice['invoiceno'].'</b> 的信息已成功修改!',
				'url' => '/s.php?module=invoice&action=view&invoiceid='.$invoice['invoiceid'],
				'sec' => 3
			));
		}
	}

	//
	function update(){
		if($this->kclass->input['invoiceid']<=0 OR !$invoice = $this->kclass->DB->queryFirst("SELECT `invoice`.* FROM `invoice` WHERE `invoiceid`='".$this->kclass->input['invoiceid']."'")){
			$this->kclass->boinkIt('/s.php?module=invoice');
		}
		$url = REFERER;
		if(2==$invoice['status']){
			$this->kclass->messager(array(
					'title' => '修改发票',
					'text' => '发票 <b>'.$invoice['invocieno'].'</b> 的信息已经通过审批，不能再次修改!',
					'url' => REFERER,
					'sec' => 3
			));
		}
		if($this->kclass->input['step'] == 'item'){
			$invoice = $this->kclass->DB->queryFirst("
				SELECT `invoice`.invoiceid, `invoice`.invoiceno, `invoice`.customerid, 
					`customer`.title AS title
				FROM `invoice` 
				LEFT JOIN `customer` ON (`customer`.customerid=`invoice`.customerid)
				WHERE invoiceid='".$this->kclass->input['invoiceid']."'
			");
			// all item for customer
			$orders = $this->kclass->DB->query("
				SELECT orderid, orderno, customerNo, created
				FROM `order`
				WHERE killed=0 AND `type`='PI' AND customerid='".$invoice['customerid']."'
			");
			if($counter=$this->kclass->DB->numRows()){
				$i=1;
				while($order = $this->kclass->DB->fetchArray($orders)){
					$items = $this->kclass->DB->query("
						SELECT item.*,
							productitem.attributevalue,
							productitem.mid AS productId,
							brand.title AS brand,
							packing.title AS packing,
							unit.title AS unit
						FROM `item`
						LEFT JOIN `productitem` ON (productitem.productitemid=item.productitemid)
						LEFT JOIN packing ON (packing.packingid=item.packingid)
						LEFT JOIN brand ON (brand.brandid=item.brandid)
						LEFT JOIN unit ON (unit.unitid=item.unitid)
						WHERE `item`.killed=0 AND `item`.module='order' AND `item`.mid='".$order['orderid']."'
						ORDER BY itemid ASC, modified ASC
					");
					if($this->kclass->DB->numRows()){
						$k=1;
						while($item = $this->kclass->DB->fetchArray($items)){
							$selectStr = '';
							if(!empty($item['attributevalue'])){
								$valueStr = explode(",",$item['attributevalue']);
								
								$product=$this->kclass->DB->queryFirst("
									SELECT `product`.title,`product`.entitle
									FROM  `product`
									WHERE productid='".$item['productId']."'
								");
								
								$attributes=$this->kclass->DB->query("
									SELECT an.nameid,an.title AS name,
										av.valueid,av.title AS value
									FROM  attributename AS an
									LEFT JOIN attributevalue AS av ON (av.nameid=an.nameid)
									WHERE an.killed=0 AND av.killed=0 AND an.module='product' AND an.mid='".$item['productId']."'
									ORDER BY an.nameid ASC
								");
								
								$nameId=$title=$value=$attributeCount=array();
								if($this->kclass->DB->numRows()){
									while($attribute=$this->kclass->DB->fetchArray($attributes)){
										if(in_array($attribute['nameid'],$nameId)){
											$title[$attribute['nameid']]=$attribute['name'];
											$value[$attribute['nameid']][]=$attribute['value'];
											$valueId[$attribute['nameid']][]=$attribute['valueid'];
											$attributeCount[$attribute['nameid']]++;
										}else{
											$nameId[]=$attribute['nameid'];
											$title[$attribute['nameid']]=$attribute['name'];
											$value[$attribute['nameid']][]=$attribute['value'];
											$valueId[$attribute['nameid']][]=$attribute['valueid'];
											$attributeCount[$attribute['nameid']]=1;
										}
									}
								}
								foreach ($nameId as $key=>$val){
									$selectStr .= ''.$title[$val].'：';
									for($n=0;$n<count($value[$val]);$n++){
										if($valueId[$val][$n]==$valueStr[$key]){
											$selectStr .= '<span class="darkred">'.$value[$val][$n].'</span>';
										}
									}
									$selectStr .= '　';
								}
							}else{
								$product=$this->kclass->DB->queryFirst("
									SELECT `product`.title,`product`.entitle,`product`.materialid
									FROM  `product`
									WHERE productid='".$item['productid']."'
								");
								$item['productId'] = $item['productid'];
								$material = $this->kclass->DB->queryFirst("
									SELECT `material`.materialid,`material`.standard
									FROM  `material`
									WHERE materialid='".$product['materialid']."'
								");
								$selectStr = $material['standard'];
							}
							
							$orderList .= '<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').'"><td>'.$i.'</td><td><a href="/s.php?module=order&action=view&orderid='.$order['orderid'].'" target="_blank">'.$order['orderno'].'</a></td> 
							<td><input type="hidden" name="olItemid['.$i.']" value="'.$item['itemid'].'"><input type="hidden" name="olOrderid['.$i.']" value="'.$order['orderid'].'"><a href="/s.php?module=product&action=view&productid='.$item['productId'].'" target="_blank">'.$product['title'].'</a></td>
							<td width="350">'.$selectStr.'</td>
							<td><input type="text" name="olCustomerOrderNo['.$i.']" value="'.$order['customerNo'].'" size="12"></td>
							<td><input type="text" name="olArticleNo['.$i.']" value="'.$item['articleNo'].'" size="12"></td>
							<td><input type="text" name="olDiscount['.$i.']" value="'.$item['discount'].'" size="3"></td>
							<td><input type="text" name="olQuantity['.$i.']" value="'.$item['quantity'].'" size="6"></td>
							<td>'.$this->kclass->chooserUnit(array('name'=>'olUnitid['.$i.']', 'hasBlank'=>1, 'width'=>100, 'selectedid'=>$item['unitid'])).'</td>
							<td><input type="text" name="olPrice['.$i.']" value="'.$item['price'].'" size="6"></td>
							<td><input type="text" name="olOrdering['.$i.']" value="'.$item['ordering'].'" size="1"></td>
							<td><input type="checkbox" name="olSelect['.$i.']" value="'.$item['itemid'].'"></td>
							</tr>';
							$i++;
						}
					}
				}
			}
			// org item
			$orgItems = $this->kclass->DB->query("
				SELECT item.*,
					productitem.attributevalue,
					productitem.mid AS productId,
					brand.title AS brand,
					packing.title AS packing,
					unit.title AS unit
				FROM `item`
				LEFT JOIN `productitem` ON (productitem.productitemid=item.productitemid)
				LEFT JOIN packing ON (packing.packingid=item.packingid)
				LEFT JOIN brand ON (brand.brandid=item.brandid)
				LEFT JOIN unit ON (unit.unitid=item.unitid)
				WHERE `item`.killed=0 AND `item`.module='invoice' AND  `item`.mid='".$invoice['invoiceid']."'
				ORDER BY itemid ASC, modified ASC
			");
			if($this->kclass->DB->numRows()){
				$i=1;
				while($orgItem = $this->kclass->DB->fetchArray($orgItems)){
					$selectStr = '';
					if(!empty($orgItem['attributevalue'])){
						$valueStr = explode(",",$orgItem['attributevalue']);
						
						$product=$this->kclass->DB->queryFirst("
							SELECT `product`.title,`product`.entitle
							FROM  `product`
							WHERE productid='".$orgItem['productId']."'
						");
						$order=$this->kclass->DB->queryFirst("
								SELECT `order`.*
								FROM  `order`
								WHERE orderid='".$orgItem['orderid']."'
							");
						
						$attributes=$this->kclass->DB->query("
							SELECT an.nameid,an.title AS name,
								av.valueid,av.title AS value
							FROM  attributename AS an
							LEFT JOIN attributevalue AS av ON (av.nameid=an.nameid)
							WHERE an.killed=0 AND av.killed=0 AND an.module='product' AND an.mid='".$orgItem['productId']."'
							ORDER BY an.nameid ASC
						");
						
						$nameId=$title=$value=$attributeCount=array();
						if($this->kclass->DB->numRows()){
							while($attribute=$this->kclass->DB->fetchArray($attributes)){
								if(in_array($attribute['nameid'],$nameId)){
									$title[$attribute['nameid']]=$attribute['name'];
									$value[$attribute['nameid']][]=$attribute['value'];
									$valueId[$attribute['nameid']][]=$attribute['valueid'];
									$attributeCount[$attribute['nameid']]++;
								}else{
									$nameId[]=$attribute['nameid'];
									$title[$attribute['nameid']]=$attribute['name'];
									$value[$attribute['nameid']][]=$attribute['value'];
									$valueId[$attribute['nameid']][]=$attribute['valueid'];
									$attributeCount[$attribute['nameid']]=1;
								}
							}
						}
						foreach ($nameId as $key=>$val){
							$selectStr .= ''.$title[$val].'：';
							for($n=0;$n<count($value[$val]);$n++){
								if($valueId[$val][$n]==$valueStr[$key]){
									$selectStr .= '<span class="darkred">'.$value[$val][$n].'</span>';
								}
							}
							$selectStr .= '　';
						}
					}else{
						$order=$this->kclass->DB->queryFirst("
							SELECT `order`.*
							FROM  `order`
							WHERE orderid='".$orgItem['orderid']."'
						");
						$product=$this->kclass->DB->queryFirst("
							SELECT `product`.title,`product`.entitle,`product`.materialid
							FROM  `product`
							WHERE productid='".$orgItem['productid']."'
						");
						$orgItem['productId'] = $orgItem['productid'];
						$material = $this->kclass->DB->queryFirst("
							SELECT `material`.materialid,`material`.standard
							FROM  `material`
							WHERE materialid='".$product['materialid']."'
						");
						$selectStr = $material['standard'];
					}
					
					
					$orgItemtr .= '<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').'"><td>'.$i.'</td><td><a href="/s.php?module=order&action=view&orderid='.$orgItem['orderid'].'" target="_blank">'.$order['orderno'].'</a></td> 
					<td><input type="hidden" name="orgItemid['.$i.']" value="'.$orgItem['itemid'].'"><input type="hidden" name="orgOrderid['.$i.']" value="'.$orgItem['orderid'].'"><a href="/s.php?module=product&action=view&productid='.$orgItem['productId'].'" target="_blank">'.$product['title'].'</a></td>
					<td width="350">'.$selectStr.'</td>
					<td><input type="text" name="orgCustomerOrderNo['.$i.']" value="'.$orgItem['customerOrderNo'].'" size="12"></td>
					<td><input type="text" name="orgArticleNo['.$i.']" value="'.$orgItem['articleNo'].'" size="12"></td>
					<td><input type="text" name="orgDiscount['.$i.']" value="'.$orgItem['discount'].'" size="3"></td>
					<td><input type="text" name="orgQuantity['.$i.']" value="'.$orgItem['quantity'].'" size="6"></td>
					<td>'.$this->kclass->chooserUnit(array('name'=>'orgUnitid['.$i.']', 'hasBlank'=>1, 'width'=>100, 'selectedid'=>$orgItem['unitid'])).'</td>
					<td><input type="text" name="orgPrice['.$i.']" value="'.$orgItem['price'].'" size="6"></td>
					<td><input type="text" name="orgOrdering['.$i.']" value="'.$orgItem['ordering'].'" size="1"></td>
					<td><input type="checkbox" name="orgSelect['.$i.']" value="'.$orgItem['itemid'].'"></td>
					</tr>';
					$i++;
				}
			}

$body = <<<EOF
<form action="/s.php?module=invoice&action=doupdate" name="invoice" method="post">
<input type="hidden" name="module" value="invoice">
<input type="hidden" name="action" value="doupdate">
<input type="hidden" name="step" value="item">
<input type="hidden" name="invoiceid" value="{$invoice['invoiceid']}">
<input type="hidden" name="customerid" value="{$invoice['customerid']}">
<table class="hundred">
<thead>
<tr>
	<th>ID</th><th>订单号</th><th>产品或配件名称</th><th>产品或配件特有属性</th><th>客户订单号</th><th>客户产品编号</th><th align="right">折扣</th><th align="right">数量</th><th>单位</th><th align="right">单价</th><th align="right">排序</th><th class="darkred">删</th>
</tr>
</thead>
<tbody class="small">
<tr>
	<td colspan="12" class="darkred">发票编号：<span class="bold">{$invoice['invoiceno']}</span> 已有条目</td>
</tr>
{$orgItemtr}
</tbody>
<thead>
<tr><th>ID</th><th>订单号</th><th width="120">产品或配件名称</th><th>产品或配件特有属性</th><th>客户的订单号</th><th>客户的产品号</th><th align="right">折扣%</th><th align="right">数量</th><th align="right">单位</th><th align="right">单价</th><th align="right">排序</th><th class="green">选</th></tr>
</thead>
<tbody class="small">
<tr class="odd">
	<td colspan="12" class="darkred">客户 <span class="bold darkred">{$invoice['title']}</span> 的全部订单下的条目明细 <span class="small gray">(共{$counter}个，但没有明细内容的订单不会显示)</span></td>
</tr>
{$orderList}
<tr class="odd">
	<td align="center" colspan="12">
		<input type="submit" id="submitButton" value="  保存  " accesskey="s">　　
		<input type="reset" value="  复位  ">
	</td>
</tr>
</tbody>
</table>
</form>
EOF;
			$this->kclass->page['title'] .= ' - 新增与修改发票订单条目 - '.$invoice['invocieno'];
			$this->kclass->page['onload'] .= 'dc.tabhover();';
			$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 新增与修改发票订单条目', 'right' => '<a href="/s.php?module=invoice&action=view&invoiceid='.$invoice['invoiceid'].'">返回发票'.$invoice['invocieno'].'</a>', 'body'=>$body));
		}else{
			$onBoardDate='';
			if($invoice['onBoardDate']>0){
				$onBoardDate=date('Y-m-d', $invoice['onBoardDate']);
			}
			$dateOfArrival='';
			if($invoice['dateOfArrival']>0){
				$dateOfArrival=date('Y-m-d', $invoice['dateOfArrival']);
			}
			$this->kclass->br2nl=true;
			$invoice['commodity']=$this->kclass->parseConvertValue($invoice['commodity']);
			$invoice['remark']=$this->kclass->parseConvertValue($invoice['remark']);
			
			$director = $this->kclass->chooserTracker(array('name'=>'director','condition'=>'positionid=7','width'=>80,'selectedid'=>$invoice['director']));
			$tracker = $this->kclass->chooserTracker(array('name'=>'tracker','condition'=>'positionid=7','width'=>80,'selectedid'=>$invoice['tracker']));
			$loading = $this->kclass->chooserPort(array('name'=>'loading', 'hasBlank'=>1, 'width'=>450, 'selectedid'=>$invoice['loading']));
			$discharge = $this->kclass->chooserPort(array('name'=>'discharge', 'hasBlank'=>1, 'width'=>450, 'selectedid'=>$invoice['discharge']));
			$paymentterm = $this->kclass->chooserPaymentterm(array('name'=>'paymenttermid', 'hasBlank'=>1, 'width'=>450, 'selectedid'=>$invoice['paymenttermid']));
			$deliveryterm = $this->kclass->chooserDeliveryterm(array('name'=>'deliverytermid', 'hasBlank'=>1, 'width'=>450, 'selectedid'=>$invoice['deliverytermid']));
			$shipmethod = $this->kclass->chooserShipmethod(array('name'=>'shipmethodid', 'hasBlank'=>1, 'width'=>450, 'selectedid'=>$invoice['shipmethodid']));
			$currency = $this->kclass->chooserCurrency(array('name'=>'currencyid', 'hasBlank'=>1, 'width'=>100, 'selectedid'=>$invoice['currencyid']));
			$unit = $this->kclass->chooserUnit(array('name'=>'unitid', 'hasBlank'=>1, 'width'=>150, 'selectedid'=>$invoice['unitid']));
			$upload = $this->kclass->upload(array('title'=>'相关附件：<span class="small darkred">请注意：发票里的附件不会出现在打印页面，仅在系统内部显示和存档。</span>'));
			$region = $this->kclass->chooserRegion(array('regionid'=>$customer['regionid'], 'hasBlank'=>1, 'regionid'=>$invoice['regionid']));
			//fee & discount
			$fee=$this->kclass->formFee(array('module'=>'invoice','mid'=>$invoice['invoiceid']));
			$discount=$this->kclass->formDiscount(array('module'=>'invoice','mid'=>$invoice['invoiceid']));
$body = <<<EOF
<form action="/s.php?module=invoice&action=doupdate" name="invoice" method="post">
<input type="hidden" name="module" value="invoice">
<input type="hidden" name="action" value="doupdate">
<input type="hidden" name="invoiceid" value="{$this->kclass->input['invoiceid']}">
<table class="hundred">
<thead>
<tr>
	<th colspan="4">修改发票：{$invoice['invoiceno']}</th>
</tr>
</thead>
<tbody>
<tr class="odd">
	<td title="Invoice No">发票编号：</td>
	<td><span class="middle bold darkred">{$invoice['invoiceno']}</span> <input type="hidden" name="invoiceno" value="{$invoice['invoiceno']}"></td>
	<td>币　　种：<span class="red bold">*</span></td>
	<td><input type="hidden" name="refno" value="">{$currency}　　业务人员：<span class="red bold">*</span> {$director}　　跟单人员：<span class="red bold">*</span> {$tracker}</td></td>
</tr>
</tbody>
<thead>
<tr>
	<th colspan="4">客户 <span class="bold darkred">{$invoice['title']}</span> 的信息：<span class="gray normal">客户不能修改，如要修改客户，则需要重新建立一张新的发票。</span></th>
</tr>
</thead>
<tbody>
<tr class="odd">
	<td title="Customer Title">客户名称：<span class="red bold">*</span></td>
	<td><input type="text" style="width:340px" name="title" value="{$invoice['title']}"> <span class="gray small">客户需要的发票抬头</span></td>
	<td title="Customer Address">客户地址：<span class="red bold">*</span></td>
	<td><input type="text" style="width:330px" name="address" value="{$invoice['address']}"> <span class="gray small">国家/省 在下面选择</span></td>
</tr>
<tr class="even">
	<td title="Payment Term">支付条款：</td>
	<td>{$paymentterm}</td>
	<td>国家地区：<span class="red bold">*</span></td>
	<td>{$region}</td>
</tr>
<tr class="odd">
	<td title="Shipment Method">运送方式：</td>
	<td>{$shipmethod}</td>
	<td title="Delivery Term">交货条款：</td>
	<td>{$deliveryterm}</td>
</tr>
<tr class="even">
	<td title="Port or Loading">装货地点：</td>
	<td>{$loading}</td>
	<td title="On Board Date">装船日期：</td>
	<td><input type="text" style="width:150px" id="onBoardDate" name="onBoardDate" value="{$onBoardDate}"> <span class="gray small">装船/车/飞机的日期</span></td>
</tr>
<tr class="odd">
	<td title="Port of Discharge">卸货地点：</td>
	<td>{$discharge}</td>
	<td title="Date of Arrival">到达日期：</td>
	<td><input type="text" style="width:150px" id="dateOfArrival" name="dateOfArrival" value="{$dateOfArrival}"> <span class="gray small">抵达卸货地点的日期</span></td>
</tr>
<tr class="even">
	<td title="Final Destination">最终目的地：</td>
	<td><input type="text" style="width:150px" name="finalDestination" value="{$invoice['finalDestination']}"> <span class="gray small">如果卸货地点不是最终目的地城市，则需填写</span></td>
	<td title="1st. Carrier">1<sup>st</sup>货运公司：</td>
	<td><input type="text" style="width:150px" name="firstCarrier" value="{$invoice['firstCarrier']}"> <span class="gray small">第一阶段负责运输的公司，如果只有一个，此处留空</span></td>
</tr>
<tr class="odd">
	<td valign="top" title="Labeling">包装箱数：<span class="red bold">*</span></td>
	<td><input type="text" style="width:70px" name="quantity" value="{$invoice['quantity']}">{$unit}<span class="gray small">表示运输时的单体数量，如多少箱</span></td>
	<td title="2nd. Carrier">2<sup>nd</sup>货运公司：</td>
	<td><input type="text" style="width:150px" name="secondCarrier" value="{$invoice['secondCarrier']}"> <span class="gray small">第二阶段负责运输的公司</span></td>
</tr>
<tr class="even">
	<td title="Commodity">报关商品：<div class="gray small" style="width:80px">报关所使用的商品名称</div></td>
	<td><textarea name="commodity" style="width:444px;height:100px">{$invoice['commodity']}</textarea></td>
	<td valign="top" title="Remark">备　　注：</td>
	<td><textarea name="remark" style="width:444px;height:100px">{$invoice['remark']}</textarea></td>
</tr>
</tbody>
</table>
{$fee}
{$discount}
{$upload}
<table class="tlist tsuite" align="center" width="100%">
<tbody>
<tr class="even">
	<td colspan="4" align="center">
		不生成历史版本：<input type="radio" name="history" value="0" >　　　生成历史版本：<input type="radio" name="history" value="1" checked>　
	</td>
</tr>
<tr class="odd">
	<td colspan="4" align="center">
		<input type="submit" id="submitButton" value="  保存  " accesskey="s">　　
		<input type="reset" value="  复位  ">
	</td>
</tr>
</tbody>
</table>
</form>
EOF;
			$this->kclass->page['title'] .= ' - 修改发票 - '.$invoice['invocieno'];
			$this->kclass->page['onload'] .= 'dc.tabhover();var dates=$(\'#onBoardDate,#dateOfArrival\').datepicker({onSelect:function(selectedDate){var option=this.id==\'onBoardDate\'?\'minDate\':\'maxDate\',instance=$(this).data(\'datepicker\'),date=$.datepicker.parseDate(instance.settings.dateFormat || $.datepicker._defaults.dateFormat,selectedDate,instance.settings);dates.not(this).datepicker(\'option\',option,date);}});';
			$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . '- <a href="/s.php?module=invoice&action=view&invoiceid='.$invoice['invoiceid'].'">'.$invoice['invoiceno'].'</a> - 修改发票', 'right' => '<a href="/s.php?module=invoice">返回列表</a>', 'body'=>$body));
		}
	}
	//
	function kill(){
		if($this->kclass->input['invoiceid']<=0){
			$this->kclass->boinkIt('/s.php?module=invoice');
		}
		if($this->kclass->input['confirm'] == 0){
			$this->kclass->boinkIt('/s.php?module=invoice&action=view&invoiceid='.$this->kclass->input['invoiceid']);
		}
		if($this->kclass->input['rt']=='view'){
			$rt = '&action=view&invoiceid='.$this->kclass->input['invoiceid'];
		}elseif($this->kclass->input['rt']=='list'){
			$rt = '#'.$this->kclass->input['invoiceid'];
		}
		$invoice = $this->kclass->DB->queryFirst("
			SELECT invoiceno,title
			FROM invoice
			WHERE invoiceid='".$this->kclass->input['invoiceid']."'
		");
		if($invoice){
			$this->kclass->DB->query("
				UPDATE `invoice`
				SET killed=".TIMENOW." 
				WHERE invoiceid='".$this->kclass->input['invoiceid']."'
			");
			$this->kclass->messager(array(
				'title' => '删除发票成功',
				'text' => '发票 <b>'.$invoice['invoiceno'].'</b> 已成功被标记为删除!',
				'url' => '/s.php?module=invoice'.$rt,
				'sec' => 2
			));
		}else{
			$this->kclass->messager(array(
				'title' => '删除发票失败',
				'text' => '您要删除的发票，不存在！',
				'url' => '/s.php?module=invoice'.$rt,
				'sec' => 3
			));
		}
	}

	//
	function remove(){
		if($this->kclass->input['invoiceid']<=0){
			$this->kclass->boinkIt('/s.php?module=invoice');
		}
		$invoice = $this->kclass->DB->queryFirst("
			SELECT invoiceno
			FROM invoice
			WHERE invoiceid='".$this->kclass->input['invoiceid']."'
		");
$body = <<<EOF
<form action="/s.php?module=invoice&action=kill" name="invoice" method="post">
<input type="hidden" name="module" value="invoice">
<input type="hidden" name="action" value="kill">
<input type="hidden" name="invoiceid" value="{$this->kclass->input['invoiceid']}">
<table><thead>
<thead>
<tr>
	<th>删除发票：{$invoice['invoiceno']}</th>
</tr>
</thead>
<tbody>
<tr class="even">
	<td class="middle">你确定要删除发票: <a href="/s.php?module=invoice&action=view&invoiceid={$this->kclass->input['invoiceid']}" class="big bold" target="_blank">{$invoice['invoiceno']}</a> 吗?</td>
</tr>
<tr class="odd">
	<td align="center">
		<label for="c1" class="red"><input type="radio" id="c1" name="confirm" value="1">是</label>　　
		<label for="c0" class="green"><input type="radio" id="c0" name="confirm" value="0" checked>否</label>
	</td>
</tr>
<tr class="even">
	<td align="center">
		<input type="submit" value="   保存   " accesskey="s">
		<input type="reset" value="   复位   ">
	</td>
</tr>
</tbody>
</table>
</form>
EOF;
		$this->kclass->page['title'] .= ' - 删除 - '.$invoice['title'];
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 删除发票 - '.$invoice['title'], 'right' => '<a href="/s.php?module=invoice">返回列表</a>', 'body'=>$body));
	}

	//
	function revival(){
		if($this->kclass->input['productid']<=0){
			$this->kclass->boinkIt('/s.php?module=invoice');
		}
		if($this->kclass->input['confirm'] == 0){
			$this->kclass->boinkIt('/s.php?module=invoice&action=view&productid='.$this->kclass->input['productid']);
		}
		if($this->kclass->input['productid'] < 0){
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
			SELECT title
			FROM invoice
			WHERE productid='".$this->kclass->input['productid']."'
		");
		if($this->kclass->input['rt']=='view'){
			$rt = '&action=view&productid='.$this->kclass->input['productid'];
		}elseif($this->kclass->input['rt']=='list'){
			$rt = '#'.$this->kclass->input['productid'];
		}
		if($invoice){
			$this->kclass->DB->query("
				UPDATE `invoice`
				SET killed=0
				WHERE productid='".$this->kclass->input['productid']."'
			");
			$this->kclass->messager(array(
				'title' => '恢复发票成功',
				'text' => '发票 <b>'.$invoice['title'].'</b> 已成功被恢复！',
				'url' => '/s.php?module=invoice'.$rt,
				'sec' => 2
			));
		}else{
			$this->kclass->messager(array(
				'title' => '恢复发票失败',
				'text' => '您要恢复的发票不存在！',
				'url' => '/s.php?module=invoice'.$rt,
				'sec' => 3
			));
		}
	}

	//
	function restore(){
		if($this->kclass->input['productid']<=0){
			$this->kclass->boinkIt('/s.php?module=invoice');
		}
		$invoice = $this->kclass->DB->queryFirst("
			SELECT title
			FROM invoice
			WHERE productid='".$this->kclass->input['productid']."'
		");
$body = <<<EOF
<form action="/s.php?module=invoice&action=revival" name="invoice" method="post">
<input type="hidden" name="module" value="invoice">
<input type="hidden" name="action" value="revival">
<input type="hidden" name="productid" value="{$this->kclass->input['productid']}">
<table><thead>
<thead>
<tr>
	<th>恢复发票：{$invoice['title']}</th>
</tr>
</thead>
<tbody>
<tr class="even">
	<td class="middle">你确定要恢复发票: <a href="/s.php?module=invoice&action=view&productid={$this->kclass->input['productid']}" class="big bold" target="_blank">{$invoice['title']}</a> 吗?</td>
</tr>
<tr class="odd">
	<td align="center">
		<label for="c1" class="red"><input type="radio" id="c1" name="confirm" value="1">是</label>　　
		<label for="c0" class="green"><input type="radio" id="c0" name="confirm" value="0" checked>否</label>
	</td>
</tr>
<tr class="even">
	<td align="center">
		<input type="submit" value="   保存   " accesskey="s">
		<input type="reset" value="   复位   ">
	</td>
</tr>
</tbody>
</table>
</form>
EOF;
		$this->kclass->page['title'] .= ' - 恢复 - '.$invoice['title'];
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 恢复发票 - '.$invoice['title'], 'right' => '<a href="/s.php?module=invoice">返回列表</a>', 'body'=>$body));
	}
	
	/**
	 * 审核发票表单
	 * @author creator mayinghao 2013-07-17 8:15
	 * @access public
	 * @param none 没有参数
	 * @return void 没有返回信息
	 * @throws none 没有异常
	 */
	function auditForm(){
		$url = REFERER;
		$body = <<<EOF
<form action="/s.php?module=invoice&action=audit" name="order" method="post">
<input type="hidden" name="module" value="invoice">
<input type="hidden" name="action" value="audit">
<input type="hidden" name="invoiceid" value="{$this->kclass->input['invoiceid']}">
<input type="hidden" name="url" value="{$url}">
<table class="hundred">
<thead>
<tr>
<th colspan="4">审核发票：</th>
</tr>
</thead>
<tbody>
<tr class="odd">
<td colspan="4" align="center">审核通过：<input type="radio" name="audit" value="1" checked>　　　审核不通过：<input type="radio" name="audit" value="0"></td>
</tr>
</tbody>
</table>
<table class="tlist tsuite" align="center" width="100%">
<tbody>
<tr class="odd">
<td colspan="4" align="center">
<input type="submit" id="submitButton" value="  提交  " accesskey="s">　　
</td>
</tr>
</tbody>
</table>
</form>
EOF;
		$this->kclass->page['title'] .= ' - 审核发票';
		$this->kclass->page['onload'] .= 'dc.tabhover();$(\'#shipmentDate\').datepicker();';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 审核发票', 'right' => '<a href="'.REFERER.'">返回</a>', 'body'=>$body));
	}
	
	/**
	 * 审核发票
	 * @author creator mayinghao 2013-07-17 8:15
	 * @access public
	 * @param none 没有参数
	 * @return void 没有返回信息
	 * @throws none 没有异常
	 */
	function audit(){
		$url = $this->kclass->input['url'];
		if($this->kclass->input['invoiceid']>0){
			$invoice = $this->kclass->DB->queryFirst("
				SELECT `invoice`.status
				FROM `invoice`
				WHERE invoiceid='".$this->kclass->input['invoiceid']."'
			");
			if($invoice){
				if(1==$this->kclass->input['audit']){
					if(0==$invoice['status']){
						$this->kclass->DB->query("
							UPDATE `invoice` SET status='1' WHERE invoiceid='".$this->kclass->input['invoiceid']."'
						");
						$this->kclass->messager(array(
								'title' => '审核发票',
								'text' => '发票审核成功!',
								'url' => $url,
								'sec' => 3
						));
					}else{
						$this->kclass->messager(array(
								'title' => '审核发票',
								'text' => '此发票已经通过审核!',
								'url' => $url,
								'sec' => 3
						));
					}
				}else{
					if(0<$invoice['status']){
						$this->kclass->DB->query("
							UPDATE `invoice` SET status='0' WHERE invoiceid='".$this->kclass->input['invoiceid']."'
						");
						$this->kclass->messager(array(
								'title' => '审核发票',
								'text' => '操作成功,发票审核状态为不通过!',
								'url' => $url,
								'sec' => 3
						));
					}else{
						$this->kclass->messager(array(
								'title' => '审核发票',
								'text' => '发票审核状态已经为不通过!',
								'url' => $url,
								'sec' => 3
						));
					}
				}
			}else{
				$this->kclass->messager(array(
						'title' => '审核发票',
						'text' => '没有此发票!',
						'url' => $url,
						'sec' => 3
				));
			}
		}else{
			$this->kclass->messager(array(
					'title' => '审核发票',
					'text' => '发票参数不正确!',
					'url' => 'javascript:history.back()',
					'sec' => 3
			));
		}
	}
	
	
	/**
	 * 审批发票表单
	 * @author creator mayinghao 2013-07-17 8:15
	 * @access public
	 * @param none 没有参数
	 * @return void 没有返回信息
	 * @throws none 没有异常
	 */
	function approvalForm(){
		$url = REFERER;
		$body = <<<EOF
<form action="/s.php?module=invoice&action=audit" name="order" method="post">
<input type="hidden" name="module" value="invoice">
<input type="hidden" name="action" value="approval">
<input type="hidden" name="invoiceid" value="{$this->kclass->input['invoiceid']}">
<input type="hidden" name="url" value="{$url}">
<table class="hundred">
<thead>
<tr>
<th colspan="4">审批发票：</th>
</tr>
</thead>
<tbody>
<tr class="odd">
<td colspan="4" align="center">审批通过：<input type="radio" name="audit" value="1" checked>　　　审批不通过：<input type="radio" name="audit" value="0"></td>
</tr>
</tbody>
</table>
<table class="tlist tsuite" align="center" width="100%">
<tbody>
<tr class="odd">
<td colspan="4" align="center">
<input type="submit" id="submitButton" value="  提交  " accesskey="s">　　
</td>
</tr>
</tbody>
</table>
</form>
EOF;
		$this->kclass->page['title'] .= ' - 审批发票';
		$this->kclass->page['onload'] .= 'dc.tabhover();$(\'#shipmentDate\').datepicker();';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 审批发票', 'right' => '<a href="'.REFERER.'">返回</a>', 'body'=>$body));
	}
	
	/**
	 * 审批发票
	 * @author creator mayinghao 2013-07-17 8:30
	 * @access public
	 * @param none 没有参数
	 * @return void 没有返回信息
	 * @throws none 没有异常
	 */
	function approval(){
		$url = $this->kclass->input['url'];
		if($this->kclass->input['invoiceid']>0){
			$invoice = $this->kclass->DB->queryFirst("
				SELECT `invoice`.status
				FROM `invoice`
				WHERE invoiceid='".$this->kclass->input['invoiceid']."'
			");
			if($invoice){
				if(1==$this->kclass->input['audit']){
					if(1==$invoice['status']){
						$this->kclass->DB->query("
							UPDATE `invoice` SET status='2' WHERE invoiceid='".$this->kclass->input['invoiceid']."'
						");
						$this->kclass->messager(array(
								'title' => '审批发票',
								'text' => '发票审批成功!',
								'url' => $url,
								'sec' => 3
						));
					}elseif(0==$invoice['status']){
						$this->kclass->messager(array(
								'title' => '审批发票',
								'text' => '发票还未审核,请先审核后再审批!',
								'url' => $url,
								'sec' => 3
						));
					}else{
						$this->kclass->messager(array(
								'title' => '审批发票',
								'text' => '此发票已经通过审批!',
								'url' => $url,
								'sec' => 3
						));
					}
				}else{
					$this->kclass->DB->query("
							UPDATE `invoice` SET status='0' WHERE invoiceid='".$this->kclass->input['invoiceid']."'
						");
					$this->kclass->messager(array(
							'title' => '审批发票',
							'text' => '操作成功,发票审批状态为不通过!',
							'url' => $url,
							'sec' => 3
					));
				}
			}else{
				$this->kclass->messager(array(
						'title' => '审批发票',
						'text' => '没有此发票!',
						'url' => $url,
						'sec' => 3
				));
			}
		}else{
			$this->kclass->messager(array(
					'title' => '审批发票',
					'text' => '订单参数不正确!',
					'url' => 'javascript:history.back()',
					'sec' => 3
			));
		}
	}
	
	function find(){
		$region = $this->kclass->chooserRegion(array('regionid'=>0, 'hasBlank'=>1));
		$loading = $this->kclass->chooserPort(array('name'=>'loading', 'hasBlank'=>1, 'width'=>450, 'selectedid'=>$order['loading']));
		$discharge = $this->kclass->chooserPort(array('name'=>'discharge', 'hasBlank'=>1, 'width'=>450, 'selectedid'=>$order['discharge']));
		$paymentterm = $this->kclass->chooserPaymentterm(array('name'=>'paymenttermid', 'hasBlank'=>1, 'width'=>450, 'selectedid'=>$order['paymenttermid']));
		$deliveryterm = $this->kclass->chooserDeliveryterm(array('name'=>'deliverytermid', 'hasBlank'=>1, 'width'=>450, 'selectedid'=>$order['deliverytermid']));
		$shipmethod = $this->kclass->chooserShipmethod(array('name'=>'shipmethodid', 'hasBlank'=>1, 'width'=>450, 'selectedid'=>$order['shipmethodid']));
$body = <<<EOF
<form action="/s.php?module=order&action=list" name="order" method="post">
<input type="hidden" name="module" value="order">
<input type="hidden" name="action" value="list">
<table>
<thead>
<tr>
	<th colspan="2">查找发票</th>
</tr>
</thead>
<tbody>
<tr class="even">
	<td>发票编号 包含：</td>
	<td><input type="text" style="width:150px" name="invoiceno" value=""></td>
</tr>
<tr class="even">
	<td>发票客户名称 包含：</td>
	<td><input type="text" style="width:150px" name="customer" value=""> <span class="small gray">可使用发票中客户名称中的关键字查找，大于3个字符</span></td>
</tr>
<tr class="odd">
	<td>且 运输方式 是：</td>
	<td title="Shipping Method">{$shipmethod}</td>
</tr>
<tr class="even">
	<td>且 装货地点 是：</td>
	<td title="Port of Loading">{$loading}</td>
</tr>
<tr class="odd">
	<td>且 卸货地点 是：</td>
	<td title="Port of Discharge">{$discharge}</td>
</tr>
<tr class="even">
	<td title="Payment Term">且 支付条款 是：</td>
	<td>{$paymentterm}</td>
</tr>
<tr class="odd">
	<td title="Delivery Term">且 交货条款 是：</td>
	<td>{$deliveryterm}</td>
</tr>
<tr class="even">
	<td title="Shipment Date">且 到达日期：</td>
	<td>
		<label for="s0"><input type="radio" name="shipDateline" value="before" id="s0" checked>早于</label> 
		<label for="s1"><input type="radio" name="shipDateline" value="equal" id="s1">等于</label> 
		<label for="s2"><input type="radio" name="shipDateline" value="after" id="s2">晚于</label> 
		<input type="text" style="width:150px" id="shipmentDate" name="shipmentDate" value="{$shipmentDate}">
	</td>
</tr>
<tr class="odd">
	<td >且 修改日期 ：</td>
	<td>
		开始：
		<input type="text" id="startDate" name="startModDate" size="12" value="{$startDate}">
		结束：
		<input type="text" id="endDate" name="endModDate" size="12" value="{$endDate}">
	</td>
</tr>
<tr class="even">
	<td>且 创建时间：</td>
	<td>
		开始：
		<input type="text" id="startCreateDate" name="startCreateDate" size="12" value="{$startDate}">
		结束：
		<input type="text" id="endCreateDate" name="endCreateDate" size="12" value="{$endDate}">
	</td>
</tr>
</tbody>
<thead>
<tr>
	<th colspan="2">选项</th>
</tr>
</thead>
<tbody>
<tr class="even">
	<td>排序方式：</td>
	<td>
		<label for="o0"><input type="radio" name="orderby" value="orderno" id="o0" checked>订单编号</label> 
		<label for="o1"><input type="radio" name="orderby" value="orderid" id="o1">ID</label> 
		<label for="o2"><input type="radio" name="orderby" value="modified" id="o2">修改时间</label> 
		<label for="o3"><input type="radio" name="orderby" value="created" id="o3">建立时间</label> 
	</td>
</tr>
<tr class="odd">
	<td>排列方向：</td>
	<td>
		<label for="d0"><input type="radio" id="d0" name="direction" value="ASC" checked>升序(A-Z)</label> 
		<label for="d1"><input type="radio" id="d1" name="direction" value="DESC">降序(Z-A)</label> 
	</td>
</tr>
<tr class="even">
	<td>显示选项：</td>
	<td>
		<label for="s0"><input type="radio" id="s0" name="show" value="all" checked>默认列表</label> 
		<label for="s1"><input type="radio" id="s1" name="show" value="">所有<span class="small gray">(包括删除)</span></label> 
	</td>
</tr>
<tr class="odd">
	<td>列表方式：</td>
	<td disabled>
		<label for="t0"><input type="radio" id="t0" name="layout" value="grid" checked>格子</label> 
		<label for="t1"><input type="radio" id="t1" name="layout" value="list">列表</label> 
	</td>
</tr>
<tr class="even">
	<td align="center" colspan="2">
		<input type="submit" value="   查找   " accesskey="s">
		<input type="reset" value="   复位   ">
	</td>
</tr>
</tbody>
</table>
</form>
EOF;
		$this->kclass->page['onload'] .= 'dc.tabhover();';
		$this->kclass->page['title'] .= ' - 查找发票';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 查找发票', 'right' => '<a href="/s.php?module=invoice">返回列表</a>', 'body'=>$body));
	}	// 
	function mmlist(){
		if($this->kclass->input['show']=='all'){
			$condition='`invoice`.killed=0';
		}else{
			$condition='1=1';
		}
		
		
		
		$orderby=$this->kclass->orderby(array('module'=>'invoice','direction'=>'asc','orderby'=>'invoice.modified', 'default'=>'title', 'serial'=>array(array('title'=>'名称', 'field'=>'title','word'=>'name'), array('title'=>'ID', 'field'=>'invoiceid','word'=>'id'), array('title'=>'修改时间', 'field'=>'modified'), array('title'=>'建立时间', 'field'=>'created'), array('title'=>'国家', 'field'=>'regionid')),'appendUrl'=>$query));
		
		$invoices = $this->kclass->DB->query("
			SELECT `invoice`.*, 
				region.encountry AS regionEncountry, region.country AS regionCountry, region.enstate AS regionEnstate, region.state AS regionState, region.encity AS regionEncity, region.city AS regionCity, 
				customer.title AS customerTitle, customer.address AS customerAddress, customer.linkman AS customerLinkman, customer.position AS customerPosition, customer.telephone AS customerTelephone, customer.fax AS customerFax, 
				shipmethod.title AS shipmethodTitle, shipmethod.entitle AS shipmethodEntitle, 
				loading.title AS loadingTitle, loading.entitle AS loadingEntitle, 
				loadingCountry.country AS loadingCountryTitle, loadingCountry.encountry AS loadingCountryEntitle, 
				discharge.title AS dischargeTitle, discharge.entitle AS dischargeEntitle, 
				dischargeCountry.country AS dischargeCountryTitle, dischargeCountry.encountry AS dischargeCountryEntitle, 
				paymentterm.title AS paymenttermTitle, paymentterm.entitle AS paymenttermEntitle, 
				deliveryterm.title AS deliverytermTitle, deliveryterm.entitle AS deliverytermEntitle, 
				currency.title AS currencyTitle, currency.symbol AS currencySymbol, currency.remark AS currencyRemark, 
				unit.title AS unitTitle, unit.entitle AS unitEntitle, unit.remark AS unitRemark, 
				m.username AS modifier, c.username AS creator, t.username AS tracker,d.username AS director
			FROM `invoice` 
			LEFT JOIN region ON (region.regionid=`invoice`.regionid) 
			LEFT JOIN customer ON (customer.customerid=`invoice`.customerid) 
			LEFT JOIN currency ON (currency.currencyid=`invoice`.currencyid) 
			LEFT JOIN unit ON (unit.unitid=`invoice`.unitid) 
			LEFT JOIN shipmethod ON (shipmethod.shipmethodid=`invoice`.shipmethodid) 
			LEFT JOIN port AS loading ON (loading.portid=`invoice`.loading) 
			LEFT JOIN `region` AS loadingCountry ON (loadingCountry.regionid=loading.regionid)
			LEFT JOIN port AS discharge ON (discharge.portid=`invoice`.discharge) 
			LEFT JOIN `region` AS dischargeCountry ON (dischargeCountry.regionid=discharge.regionid)
			LEFT JOIN paymentterm ON (paymentterm.paymenttermid=`invoice`.paymenttermid) 
			LEFT JOIN deliveryterm ON (deliveryterm.deliverytermid=`invoice`.deliverytermid) 
			LEFT JOIN `user` AS m ON (m.userid=invoice.modifier) 
			LEFT JOIN `user` AS c ON (c.userid=invoice.creator) 
			LEFT JOIN `user` AS d ON (d.userid=invoice.director) 
			LEFT JOIN `user` AS t ON (t.userid=invoice.tracker) 
			WHERE ".$condition."
			ORDER BY ".$orderby['sql']."
		");
		if($sum=$this->kclass->DB->numRows()){
			$body = '<table class="hundred tablesorter mytable"><thead><th width="60">发票ID</th><th width="100">发票编号</th><th width="180">发票状态</th><th>客户</th><th width="80">业务人员</th><th width="80">跟单人员</th><th width="80">运送方式</th><th width="80">交货日期</th><th width="80">到达日期</th><th width="120">创建日期</th><th width="120">修改日期</th><th width="60">创建者</th><th width="60">修改者</th><th width="36">操作</th></thead><tbody>';
			$i=1;
			while($invoice = $this->kclass->DB->fetchArray($invoices)){
				$created = date('Y-m-d H:m', $invoice['created']);
				$modified = date('Y-m-d H:m', $invoice['modified']);
				if($invoice['onBoardDate']>0){
					$onBoardDate=date('Y-m-d', $invoice['onBoardDate']);
				}else{
					$onBoardDate='--';
				}
				if($invoice['dateOfArrival']>0){
					$dateOfArrival=date('Y-m-d', $invoice['dateOfArrival']);
				}else{
					$dateOfArrival='--';
				}
				if($invoice['regionid']>0){
					if($invoice['regionEncity']!=''){
						$region = $invoice['regionEncity'].', ';
					}
					if($invoice['regionEnstate']!=''){
						$region .= $invoice['regionEnstate'].', <br>';
					}
					if($invoice['regionEncountry']!=''){
						$region .= $invoice['regionEncountry'];
					}
				}
				$status = '';
				if(0==$invoice['status']){
					$status = '<span class="darkred">未审核　未审批</span>　[<a href="/s.php?module=order&action=audit&orderid='.$order['orderid'].'">审核</a>]';
				}else if(1==$invoice['status']){
					$status = '<span class="green">已审核</span>　<span class="darkred">未审批</span>　[<a href="/s.php?module=order&action=approval&orderid='.$order['orderid'].'">审批</a>]';
				}else{
					$status = '<span class="green">已审核　已审批</span>';
				}
				$body .= '<tr class="'.$this->kclass->rotateLine().'">
					<td>'.$invoice['invoiceid'].'</td>
					<td><a href="/s.php?module=invoice&action=view&invoiceid='.$invoice['invoiceid'].'">'.$invoice['invoiceno'].'</a></td>
					<td>'.$status.'</td>
					<td><a href="/s.php?module=customer&action=view&customerid='.$invoice['customerid'].'">'.$invoice['customerTitle'].'</a></td>
					<td>'.$invoice['director'].'</td>
					<td>'.$invoice['tracker'].'</td>
					<td>'.$invoice['shipmethodTitle'].'</td>
					<td>'.$onBoardDate.'</td>
					<td>'.$dateOfArrival.'</td>
					<td>'.$created.'</td>
					<td>'.$modified.'</td>
					<td>'.$invoice['creator'].'</td>
					<td>'.$invoice['modifier'].'</td>
					<td>'.$this->kclass->iif($invoice['killed']>0,'<a href="/s.php?module=invoice&action=restore&invoiceid='.$invoice['invoiceid'].'&rt=list">恢复</a>','<a href="/s.php?module=invoice&action=kill&invoiceid='.$invoice['invoiceid'].'&rt=list" onclick="return confirm(\'你确定要删除这个样品 '.$invoice['title'].' 吗？\');">删</a> <a href="/s.php?module=invoice&action=update&invoiceid='.$invoice['invoiceid'].'&rt=list">改</a>').'</td>
				';
				/*
				$body .= '<li title="由 '.$invoice['creator'].' 建于 '.$created.$this->kclass->iif($invoice['modifier']!='', '，'.$invoice['modifier'].' 改于 '.$modified, '').'"'.$this->kclass->iif($i%4==0, ' class="end"', '').'>
				<div class="mmlistt">
					<span class="right normal">';
				if($invoice['killed']>0){
					$body .= '<a href="/s.php?module=invoice&action=restore&invoiceid='.$invoice['invoiceid'].'&rt=list">恢复</a>';
				}else{
					$body .= '<a href="/s.php?module=invoice&action=kill&invoiceid='.$invoice['invoiceid'].'&rt=list" onclick="return confirm(\'你确定要删除这个样品 '.$invoice['title'].' 吗？\');">删</a> <a href="/s.php?module=invoice&action=update&invoiceid='.$invoice['invoiceid'].'&rt=list">改</a>';
				}
				$body .= '</span>
					<a href="/s.php?module=invoice&action=view&invoiceid='.$invoice['invoiceid'].'">'.$invoice['invoiceno'].'</a><br>'.$this->kclass->iif($invoice['attachs']>0, ' <span class="attachFile" title="有'.$invoice['attachs'].'个附件。"></span> ', '').$this->kclass->iif($invoice['images']>0, ' <span class="attachImage" title="有'.$invoice['images'].'个图片。"></span> ', '').'<span class="normal">'.$this->kclass->iif(strlen($invoice['customerTitle'])>32, mb_substr($invoice['customerTitle'], 0, 32, 'UTF-8').'...', $invoice['customerTitle']).'</span></div>
				<div class="mmlistb">
					'.$this->kclass->iif($invoice['cover']!='', '<span class="right"><img src="'.$invoice['cover'].'"></span>', '').'
					<div><span class="small gray">交货日期：</span>'.$onBoardDate.'</div>
					<div title="'.$invoice['loadingEntitle'].'"><span class="small gray">装货地点：</span>'.$invoice['loadingTitle'].'</div>
					<div><span class="small gray">到达日期：</span>'.$dateOfArrival.'</div>
					<div title="'.$invoice['dischargeEntitle'].'"><span class="small gray">卸货地点：</span>'.$invoice['dischargeTitle'].'</div>
					<div class="small clear"><span class="gray small">业务人员：<span class="darkred">'.$invoice['creator'].'</span></span>&nbsp;&nbsp;&nbsp;<span class="gray small">跟单人员：<span class="darkred">'.$invoice['tracker'].'</span></span></div>		
					<div class="small clear"><span class="right" title="由 '.$invoice['creator'].' 建于 '.$created.'">'.$this->kclass->iif($invoice['modifier']!='', '由 '.$invoice['modifier'].' 改于 ', '建于 ').$modified.'</span></div>
				</div>
				</tr>';
				*/
				$i++;
			}
			$body .= '</tbody></table>';
		}else{
			$body = '暂无相关记录。';
		}

		$this->kclass->page['onload'] .= "dc.listhover();dc.tabhover();$('.mytable').fixedtableheader();$('.tablesorter').tablesorter( {sortList: [[0,1]], headers: { 6: { sorter: false},13: { sorter: false} } } );";
		$this->kclass->page['title'] .= ' - 列表';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 列表', 'right' => '<span class="small">显示方式：</span><a href="/s.php?module=invoice">默认列表</a>　<a href="/s.php?module=invoice&action=list&show=all">所有<span class="small gray">(包括删除)</span></a>　|　<a href="/s.php?module=invoice&action=add">新建</a>', 'body'=>$body));
	}

	//
	function insertPacking(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			$invoice = $this->kclass->DB->queryFirst("SELECT * FROM `invoice` WHERE invoiceid='".$this->kclass->input['invoiceid']."'");
			if(!$invoice){
				$e .= '<li>请选择返回列表刷新，重新进入新建页面，如此问题连续出现，请联系管理员。</li>';
			}
			if(strlen($this->kclass->input['title']) < 3){
				$e .= '<li>请选择发票中的 客户名称 抬头。</li>';
			}
			if(strlen($this->kclass->input['address']) < 3){
				$e .= '<li>请选择发票中的 客户地址。</li>';
			}
			if($this->kclass->input['countryid'] <= 0 AND $this->kclass->input['provinceid'] <= 0 AND $this->kclass->input['cityid'] <= 0){
				$e .= '<li>请填写发票中客户的所属 国家地区。</li>';
			}
			$t=0;
			/*
			for($i = 1; $i < count($this->kclass->input['itemid'])+1; $i++){
				if($this->kclass->input['perCarton'][$i]<=0 OR $this->kclass->input['cartonNo'][$i]=='' OR $this->kclass->input['cartons'][$i]=='' OR $this->kclass->input['grossWeight'][$i]=='' OR $this->kclass->input['netWeight'][$i]=='' OR $this->kclass->input['length'][$i]=='' OR $this->kclass->input['width'][$i]=='' OR $this->kclass->input['height'][$i]==''){
					$t=1;
				}
			}
			if($t==1){
				$e .= '<li>所有的明细项目都需要填写。</li>';
			}
			*/
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '新建包装清单',
				'text' => '您在新建包装清单的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		if($this->kclass->input['cityid']>0){
			$regionid=$this->kclass->input['cityid'];
		}elseif($this->kclass->input['provinceid']>0){
			$regionid=$this->kclass->input['provinceid'];
		}else{
			$regionid=$this->kclass->input['countryid'];
		}
		// packing list
		$this->kclass->DB->query("
			INSERT INTO `packinglist` (`packinglistno`,`invoiceno` ,`invoiceid` ,`customerid` ,`paymenttermid` ,`deliverytermid` ,`shipmethodid` ,`loading` ,`discharge` ,`onBoardDate` ,`dateOfArrival` ,`finalDestination` ,`currencyid` ,`firstCarrier` ,`secondCarrier` ,`quantity` ,`unitid` ,`commodity` ,`title` ,`address` ,`regionid` ,`invoiceRemark` ,`remark` ,`modified` ,`creator` ,`created`) 
			VALUES (
			'".$this->kclass->input['packinglistno']."','".$invoice['invoiceno']."', '".$invoice['invoiceid']."', '".$invoice['customerid']."', '".$invoice['paymenttermid']."', '".$invoice['deliverytermid']."', '".$invoice['shipmethodid']."', '".$invoice['loading']."', '".$invoice['discharge']."', '".$invoice['onBoardDate']."', '".$invoice['dateOfArrival']."', '".$invoice['finalDestination']."', '".$invoice['currencyid']."', '".$invoice['firstCarrier']."', '".$invoice['secondCarrier']."' , '".$invoice['quantity']."', '".$invoice['unitid']."', '".$invoice['commodity']."', '".$this->kclass->input['title']."', '".$this->kclass->input['address']."', '".$regionid."', '".$invoice['remark']."', '".$this->kclass->input['remark']."', '".TIMENOW."', '".$this->kclass->user['userid']."', '".TIMENOW."')
		");
		$packinglistid = $this->kclass->DB->insertID();
		// invoice discount
		for($i = 1; $i < (count($this->kclass->input['itemid'])+1); $i++){
			$this->kclass->input['perCarton'][$i]=intVal($this->kclass->input['perCarton'][$i]);
			
			$this->kclass->input['length'][$i]=intVal($this->kclass->input['length'][$i]);
			$this->kclass->input['width'][$i]=intVal($this->kclass->input['width'][$i]);
			$this->kclass->input['height'][$i]=intVal($this->kclass->input['height'][$i]);
			
			$this->kclass->input['cartons'][$i]=intVal($this->kclass->input['cartons'][$i]);
			$this->kclass->input['grossWeight'][$i]=floatVal($this->kclass->input['grossWeight'][$i]);
			$this->kclass->input['netWeight'][$i]=floatVal($this->kclass->input['netWeight'][$i]);

			$item = $this->kclass->DB->queryFirst("SELECT * FROM `item` WHERE killed=0 AND itemid='".$this->kclass->input['itemid'][$i]."'");
			$this->kclass->DB->query("
				INSERT INTO `item` (`module`, `mid`, `orderid`, `productid`,`title`,`model`,`caption`,`articleNo`,`brandid`,`packingid`,`quantity`,`discount`,`unitid`,`price`,`amount`,
				`perCarton`,`length`,`width`,`height`,`cartonNo`,`perNetWeight`,`perGrossWeight`,`netWeight`,`grossWeight`,`perVolume`,`totalCartons`,`totalNetWeight`,`totalWeight`,`totalVolume`,`modified`,`creator`,`created`)
				VALUES (
				'packinglist', 
				'".$packinglistid."', 
				'".$item['orderid']."', 
				'".$item['productid']."', 
				'".$item['title']."', 
				'".$item['model']."', 
				'".$item['caption']."', 
				'".$item['articleNo']."', 
				'".$item['brandid']."', 
				'".$item['packingid']."', 
				'".$item['quantity']."', 
				'".$item['discount']."', 
				'".$item['unitid']."', 
				'".$item['price']."', 
				'".$item['amount']."', 
				'".$this->kclass->input['perCarton'][$i]."',
				'".$this->kclass->input['length'][$i]."', 
				'".$this->kclass->input['width'][$i]."', 
				'".$this->kclass->input['height'][$i]."', 
				'".$this->kclass->input['cartonNo'][$i]."', 
				'".$this->kclass->input['perNetWeight'][$i]."',  
				'".$this->kclass->input['perGrossWeight'][$i]."', 
				'".$this->kclass->input['netWeight'][$i]."',  
				'".$this->kclass->input['grossWeight'][$i]."', 
				'".$this->kclass->input['perVolume'][$i]."', 
				'".$this->kclass->input['totalCartons'][$i]."', 
				'".$this->kclass->input['totalNetWeight'][$i]."', 
				'".$this->kclass->input['totalWeight'][$i]."', 
				'".$this->kclass->input['totalVolume'][$i]."', 
				'".TIMENOW."', 
				'".$this->kclass->user['userid']."', 
				'".TIMENOW."')
			");
		}
		$this->kclass->updateAttachs(array('module'=>'packinglist', 'mid'=>$packinglistid));

		$this->kclass->messager(array(
			'title' => '新建包装清单',
			'text' => '与发票 <b>'.$invoice['invoiceno'].'</b> 关联的 包装清单 已新建成功! ',
			'url' => '/s.php?module=invoice&action=view&invoiceid='.$invoice['invoiceid'],
			'sec' => 3
		));
	}

	//
	function addPacking(){
		if($this->kclass->input['invoiceid']<=0 OR !$invoice = $this->kclass->DB->queryFirst("
			SELECT `invoice`.*, 
				region.encountry AS regionEncountry, region.country AS regionCountry, region.enstate AS regionEnstate, region.state AS regionState, region.encity AS regionEncity, region.city AS regionCity, 
				customer.title AS customerTitle, customer.address AS customerAddress, customer.linkman AS customerLinkman, customer.position AS customerPosition, customer.telephone AS customerTelephone, customer.fax AS customerFax, 
				shipmethod.title AS shipmethodTitle, shipmethod.entitle AS shipmethodEntitle, 
				loading.title AS loadingTitle, loading.entitle AS loadingEntitle, 
				loadingCountry.country AS loadingCountryTitle, loadingCountry.encountry AS loadingCountryEntitle, 
				discharge.title AS dischargeTitle, discharge.entitle AS dischargeEntitle, 
				dischargeCountry.country AS dischargeCountryTitle, dischargeCountry.encountry AS dischargeCountryEntitle, 
				paymentterm.title AS paymenttermTitle, paymentterm.entitle AS paymenttermEntitle, 
				deliveryterm.title AS deliverytermTitle, deliveryterm.entitle AS deliverytermEntitle, 
				currency.title AS currencyTitle, currency.symbol AS currencySymbol, currency.remark AS currencyRemark, 
				unit.title AS unitTitle, unit.entitle AS unitEntitle, unit.remark AS unitRemark, 
				m.username AS mname, c.username AS cname
			FROM `invoice` 
			LEFT JOIN region ON (region.regionid=`invoice`.regionid) 
			LEFT JOIN customer ON (customer.customerid=`invoice`.customerid) 
			LEFT JOIN currency ON (currency.currencyid=`invoice`.currencyid) 
			LEFT JOIN unit ON (unit.unitid=`invoice`.unitid) 
			LEFT JOIN shipmethod ON (shipmethod.shipmethodid=`invoice`.shipmethodid) 
			LEFT JOIN port AS loading ON (loading.portid=`invoice`.loading) 
			LEFT JOIN `region` AS loadingCountry ON (loadingCountry.regionid=loading.regionid)
			LEFT JOIN port AS discharge ON (discharge.portid=`invoice`.discharge) 
			LEFT JOIN `region` AS dischargeCountry ON (dischargeCountry.regionid=discharge.regionid)
			LEFT JOIN paymentterm ON (paymentterm.paymenttermid=`invoice`.paymenttermid) 
			LEFT JOIN deliveryterm ON (deliveryterm.deliverytermid=`invoice`.deliverytermid) 
			LEFT JOIN `user` AS m ON (m.userid=invoice.modifier) 
			LEFT JOIN `user` AS c ON (c.userid=invoice.creator) 
			WHERE invoiceid='".$this->kclass->input['invoiceid']."'
		")){
			$this->kclass->boinkIt('/s.php?module=invoice');
		}
		$items = $this->kclass->DB->query("
			SELECT `item`.*, 
				`unit`.title AS unitTitle, 
				`order`.orderno, `order`.created, 
				`product`.productno, `product`.title AS productTitle 
			FROM `item` 
			LEFT JOIN `order` ON (`order`.orderid=`item`.orderid) 
			LEFT JOIN `unit` ON (`unit`.unitid=`item`.unitid) 
			LEFT JOIN `product` ON (`product`.productid=`item`.productid) 
			WHERE `item`.killed=0 AND `item`.module='invoice' AND `item`.mid='".$invoice['invoiceid']."' 
			ORDER BY modified ASC, itemid ASC
		");
		if($this->kclass->DB->numRows()){
			$i=1;
			while($item = $this->kclass->DB->fetchArray($items)){
				$orderList .= '<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').'"><td title="创建时间：'.date('Y-m-d', $item['created']).'"><a href="/s.php?module=order&action=view&orderid='.$item['orderid'].'" target="_blank">'.$item['orderno'].'</a><input type="hidden" name="itemid['.$i.']" value="'.$item['itemid'].'"></td>
				<td>'.$item['productno'].' <a href="/s.php?module=product&action=view&productid='.$item['productid'].'" target="_blank">'.$item['productTitle'].'</a></td>
				<td></td>
				<td align="right">'.$item['quantity'].''.$item['unitTitle'].'<input type="hidden" id="quantity'.$i.'" value="'.$item['quantity'].'"></td>
				<td><input type="text" name="perCarton['.$i.']" value="" size="8" id="perCarton'.$i.'" onblur="dc.packingCalculator('.$i.',1)"></td>
				<td><input type="text" name="length['.$i.']" value="" title="长" id="length'.$i.'" size="2">X<input type="text" name="width['.$i.']" title="宽" id="width'.$i.'" value="" size="2">X<input type="text" title="高" name="height['.$i.']" id="height'.$i.'" value="" size="2" onblur="dc.packingCalculator('.$i.',2)"></td>
				<td><input type="text" name="cartonNo['.$i.']" value="" size="8"></td>
				<td><input type="text" name="perNetWeight['.$i.']" value="" size="8" id="perNetWeight'.$i.'" onblur="dc.packingCalculator('.$i.',3)"></td>
				<td><input type="text" name="perGrossWeight['.$i.']" value="" size="8" id="perGrossWeight'.$i.'" onblur="dc.packingCalculator('.$i.',4)"></td>
				<td><input type="text" name="netWeight['.$i.']" value="" size="8"></td>
				<td><input type="text" name="grossWeight['.$i.']" value="" size="8"></td>
				<td><input type="text" name="perVolume['.$i.']" id="perVolume'.$i.'" value="" size="8"></td>
				<td><input type="text" name="totalCartons['.$i.']" id="totalCartons'.$i.'" value="" size="8"></td>
				<td><input type="text" name="totalNetWeight['.$i.']" id="totalNetWeight'.$i.'" value="" size="8"></td>
				<td><input type="text" name="totalWeight['.$i.']" id="totalWeight'.$i.'" value="" size="8"></td>
				<td><input type="text" name="totalVolume['.$i.']" value="" id="totalVolume'.$i.'" size="8"></td>
				</tr>';
				$i++;
			}
		}
		if($invoice['onBoardDate']>0){
			$onBoardDate=date('Y-m-d', $invoice['onBoardDate']);
		}else{
			$onBoardDate='--';
		}
		if($invoice['dateOfArrival']>0){
			$dateOfArrival=date('Y-m-d', $invoice['dateOfArrival']);
		}else{
			$dateOfArrival='--';
		}
		$this->kclass->br2nl=true;
		$invoice['commodity']=$this->kclass->parseConvertValue($invoice['commodity']);
		$invoice['remark']=$this->kclass->parseConvertValue($invoice['remark']);
		
		$dateStr = date("Ymd");
		$year=((int)substr($dateStr,0,4));//取得年份
		$month=((int)substr($dateStr,4,2));//取得月份
		$day=((int)substr($dateStr,6,2));//取得几号
		$dateStamp = mktime(0,0,0,$month,$day,$year);
		
		$orderNum = $this->kclass->DB->queryFirst("SELECT COUNT(packinglistid) AS num FROM `packinglist` WHERE created > ".$dateStamp);
		$noStr = '';
		$orderNum['num']++;
		if(strlen($orderNum['num']) < 2 AND strlen($orderNum['num']) > 0){
			$noStr = "0".$orderNum['num'];
		}elseif(strlen($orderNum['num']) == 2){
			$noStr = $orderNum['num'];
		}else{
			exit("包装清单数量有误！");
		}
		$yearStr = $dateStr.$noStr;
		$PLStr = 'PL'.$yearStr;
		
		$director = $this->kclass->chooserTracker(array('name'=>'director','condition'=>'positionid=7','width'=>80,'selectedid'=>$invoice['director']));
		$tracker = $this->kclass->chooserTracker(array('name'=>'tracker','condition'=>'positionid=7','width'=>80,'selectedid'=>$invoice['tracker']));
		$currency = $this->kclass->chooserCurrency(array('name'=>'currencyid', 'hasBlank'=>1, 'width'=>100, 'selectedid'=>$invoice['currencyid']));
			
		$upload = $this->kclass->upload(array('title'=>'相关附件：<span class="small darkred">请注意：包装清单里的附件不会出现在打印页面，仅在系统内部显示和存档，发货前拍照后此处上传存档。</span>'));
		$region = $this->kclass->chooserRegion(array('regionid'=>$customer['regionid'], 'hasBlank'=>1, 'regionid'=>$invoice['regionid']));
$body = <<<EOF
<form action="/s.php?module=invoice&action=insertPacking" name="invoice" method="post">
<input type="hidden" name="module" value="invoice">
<input type="hidden" name="action" value="insertPacking">
<input type="hidden" name="invoiceid" value="{$this->kclass->input['invoiceid']}">
<table class="hundred">
<thead>
<tr>
	<th colspan="4">为发票 {$invoice['invoiceno']} 新建包装清单,包装编号为：<span class="darkred">{$PLStr}</span></th>
</tr>
</thead>
<tbody>
<tr class="odd">
	<td title="Invoice No">包装编号：</td>
	<td><input type="text" name="packinglistno" style="width:450px;" value="{$PLStr}"></td>
	<td>币　　种：<span class="red bold">*</span></td>
	<td><input type="hidden" name="refno" value="">{$currency}　　业务人员：<span class="red bold">*</span> {$director}　　跟单人员：<span class="red bold">*</span> {$tracker}</td></td>

</tr>
</tbody>
<thead>
<tr>
	<th colspan="4">客户 <span class="bold darkred">{$invoice['title']}</span> 的信息：<span class="gray normal">客户不能修改，如要修改客户，则需要重新建立一张新的发票。</span></th>
</tr>
</thead>
<tbody>
<tr class="odd">
	<td title="Customer Title">客户名称：<span class="red bold">*</span></td>
	<td><input type="text" style="width:340px" name="title" value="{$invoice['title']}"> <span class="gray small">客户需要的发票抬头</span></td>
	<td title="Customer Address">客户地址：<span class="red bold">*</span></td>
	<td><input type="text" style="width:330px" name="address" value="{$invoice['address']}"> <span class="gray small">国家/省 在下面选择</span></td>
</tr>
<tr class="even">
	<td title="Remark">包装备注：</td>
	<td><textarea name="remark" style="width:444px;height:50px"></textarea></td>
	<td>国家地区：<span class="red bold">*</span></td>
	<td>{$region}</td>
</tr>
</tbody>
<thead>
<tr>
	<th colspan="4"><span class="right"><span class="normal" title="Currency">币种：</span>{$invoice['currencyTitle']} {$invoice['currencySymbol']} <span class="gray">{$invoice['currencyRemark']}</span></span>发票信息</th>
</tr>
</thead>
<tbody>
<tr class="odd">
	<td width="100" title="Shipment Method">运输方式：</td>
	<td class="middle">{$invoice['shipmethodTitle']} <span class="gray">{$invoice['shipmethodEntitle']}</span></td>
	<td width="100" title="Delivery Term">交货条款：</td>
	<td class="middle">{$invoice['deliverytermTitle']} <span class="gray">{$invoice['deliverytermEntitle']}</span></td>
</tr>
<tr class="even">
	<td title="Port of Loading">装货地点：</td>
	<td class="middle">{$invoice['loadingCountryTitle']} {$invoice['loadingTitle']} <span class="gray">{$invoice['loadingCountryTitle']} {$invoice['loadingTitle']}</span></td>
	<td title="Shipment Date">交货日期：</td>
	<td class="middle"><span class="bold darkred">{$onBoardDate}</span></td>
</tr>
<tr class="odd">
	<td title="Port of Discharge">卸货地点：</td>
	<td class="middle">{$invoice['dischargeCountryTitle']} {$invoice['dischargeTitle']} <span class="gray">{$invoice['dischargeCountryEntitle']} {$invoice['dischargeEntitle']}</span></td>
	<td title="Date of Arrival">到达日期：</td>
	<td class="middle"><span class="bold darkred">{$dateOfArrival}</span></td>
</tr>
<tr class="odd">
	<td title="Final Destination">最终目的地：</td>
	<td class="middle">{$invoice['finalDestination']}</td>
	<td title="1st Carrier">1<sup>st</sup>货运公司：</td>
	<td class="middle">{$invoice['firstCarrier']}</td>
</tr>
<tr class="odd">
	<td title="Payment Term">支付条款：</td>
	<td class="middle">{$invoice['paymenttermTitle']} <span class="gray">{$invoice['paymenttermEntitle']}</span></td>
	<td title="2nd Carrier">2<sup>nd</sup>货运公司：</td>
	<td class="middle">{$invoice['secondCarrier']}</td>
</tr>
<tr class="even">
	<td valign="top" title="Commodity">报关商品：</td>
	<td>{$invoice['commodity']}</td>
	<td valign="top" title="Remark">备　　注：</td>
	<td>{$invoice['remark']}</td>
</tr>
</tbody>
</table>

<table class="tlist tsuite" align="center" width="100%">
<thead>
<tr><th colspan="19">包装明细</th></tr>
</thead>
<tbody class="small">
<tr>
	<td width="90" >订单号</td>
	<td width="120">产品编号/名称</td>
	<td >产品特有属性</td>
	<td align="right" width="50">总数量<span class="tiny"></span></td>
	<td width="85">每箱数量<span class="tiny">/PCS</span><span class="red bold">*</span></td>
	<td width="110">外箱规格<span class="tiny">/CM</span><span class="red bold">*</span></td>
	<td width="60">箱编号<span class="red bold">*</span></td>
	<td width="80">每个净重<span class="tiny">/KG</span><span class="red bold">*</span></td>
	<td width="80">每个毛重<span class="tiny">/KG</span></td>
	<td width="80">每箱净重<span class="tiny">/KG</span></td>
	<td width="80">每箱毛重<span class="tiny">/KG</span></td>
	<td width="90">每箱体积<span class="tiny">/CBM</span><span class="red bold">*</span></td>
	<td width="75">总箱数<span class="tiny">/CTN</span><span class="red bold">*</span></td>
	<td width="70">总净重<span class="tiny">/KG</span><span class="red bold">*</span></td>
	<td width="70">总重量<span class="tiny">/KG</span><span class="red bold">*</span></td>
	<td width="75">总体积<span class="tiny">/CBM</span><span class="red bold">*</span></td>
</tr>
{$orderList}
</tbody>
</table>
{$upload}
<table class="tlist tsuite" align="center" width="100%">
<tbody>
<tr class="odd">
	<td colspan="4" align="center">
		<input type="submit" id="submitButton" value="  保存  " accesskey="s">　　
		<input type="reset" value="  复位  ">
	</td>
</tr>
</tbody>
</table>
</form>
EOF;
		$this->kclass->page['title'] .= ' - 为发票'.$invoice['invocieno'].'新建包装清单';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . '- <a href="/s.php?module=invoice&action=view&invoiceid='.$invoice['invoiceid'].'">'.$invoice['invoiceno'].'</a> - 新建包装清单', 'right' => '<a href="/s.php?module=invoice">返回列表</a>', 'body'=>$body));
	}

	//
	function doupdatePacking(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			$packinglist = $this->kclass->DB->queryFirst("SELECT * FROM `packinglist` WHERE packinglistid='".$this->kclass->input['packinglistid']."'");
			if(!$packinglist){
				$e .= '<li>请选择返回列表刷新，重新进入新建页面，如此问题连续出现，请联系管理员。</li>';
			}
			if(strlen($this->kclass->input['title']) < 3){
				$e .= '<li>请选择发票中的 客户名称 抬头。</li>';
			}
			if(strlen($this->kclass->input['address']) < 3){
				$e .= '<li>请选择发票中的 客户地址。</li>';
			}
			if($this->kclass->input['countryid'] <= 0 AND $this->kclass->input['provinceid'] <= 0 AND $this->kclass->input['cityid'] <= 0){
				$e .= '<li>请填写发票中客户的所属 国家地区。</li>';
			}
			$t=0;
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '修改包装清单',
				'text' => '您在修改包装清单的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		if($this->kclass->input['cityid']>0){
			$regionid=$this->kclass->input['cityid'];
		}elseif($this->kclass->input['provinceid']>0){
			$regionid=$this->kclass->input['provinceid'];
		}else{
			$regionid=$this->kclass->input['countryid'];
		}
		// packing list
		$this->kclass->DB->query("
			UPDATE `packinglist` 
			SET
				`title`='".$this->kclass->input['title']."' ,
				`address`='".$this->kclass->input['address']."' ,
				`regionid`='".$regionid."' ,
				`remark`='".$this->kclass->input['remark']."' ,
				`modified` = '".TIMENOW."', 
				`modifier` = '".$this->kclass->user['userid']."'
			WHERE packinglistid='".$packinglist['packinglistid']."'
		");
		// packing list item
		for($i = 1; $i < (count($this->kclass->input['itemid'])+1); $i++){
			$this->kclass->input['perCarton'][$i]=intVal($this->kclass->input['perCarton'][$i]);
			$this->kclass->input['cartons'][$i]=intVal($this->kclass->input['cartons'][$i]);
			$this->kclass->input['grossWeight'][$i]=floatVal($this->kclass->input['grossWeight'][$i]);
			$this->kclass->input['netWeight'][$i]=floatVal($this->kclass->input['netWeight'][$i]);
			$this->kclass->input['length'][$i]=intVal($this->kclass->input['length'][$i]);
			$this->kclass->input['width'][$i]=intVal($this->kclass->input['width'][$i]);
			$this->kclass->input['height'][$i]=intVal($this->kclass->input['height'][$i]);
			if($this->kclass->input['perCarton'][$i]>0 OR $this->kclass->input['cartonNo'][$i]=='' OR $this->kclass->input['cartons'][$i]=='' OR $this->kclass->input['grossWeight'][$i]=='' OR $this->kclass->input['netWeight'][$i]=='' OR $this->kclass->input['length'][$i]=='' OR $this->kclass->input['width'][$i]=='' OR $this->kclass->input['height'][$i]==''){
				$this->kclass->DB->query("
					UPDATE `item`
					SET `perCarton` = '".$this->kclass->input['perCarton'][$i]."',
						`length` = '".$this->kclass->input['length'][$i]."',
						`width` = '".$this->kclass->input['width'][$i]."',
						`height` = '".$this->kclass->input['height'][$i]."',
						`cartonNo` = '".$this->kclass->input['cartonNo'][$i]."',
						`perNetWeight` = '".$this->kclass->input['perNetWeight'][$i]."',
						`perGrossWeight` = '".$this->kclass->input['perGrossWeight'][$i]."',
						`netWeight` = '".$this->kclass->input['netWeight'][$i]."',
						`grossWeight` = '".$this->kclass->input['grossWeight'][$i]."',
						`perVolume` = '".$this->kclass->input['perVolume'][$i]."',
						`totalCartons` = '".$this->kclass->input['totalCartons'][$i]."',
						`totalNetWeight` = '".$this->kclass->input['totalNetWeight'][$i]."',
						`totalWeight` = '".$this->kclass->input['totalWeight'][$i]."',
						`totalVolume` = '".$this->kclass->input['totalVolume'][$i]."',
						`modified` = '".TIMENOW."', 
						`modifier` = '".$this->kclass->user['userid']."'
					WHERE itemid='".$this->kclass->input['itemid'][$i]."'
				");
			}
		}
		$this->kclass->updateAttachs(array('module'=>'packinglist', 'mid'=>$packinglist['packinglistid']));

		$this->kclass->messager(array(
			'title' => '修改包装清单',
			'text' => '包装清单 已修改成功! ',
			'url' => '/s.php?module=invoice&action=view&invoiceid='.$packinglist['invoiceid'],
			'sec' => 2
		));
	}

	//
	function updatePacking(){
		if($this->kclass->input['packinglistid']<=0 OR !$packinglist = $this->kclass->DB->queryFirst("
			SELECT `packinglist`.*, 
				region.encountry AS regionEncountry, region.country AS regionCountry, region.enstate AS regionEnstate, region.state AS regionState, region.encity AS regionEncity, region.city AS regionCity, 
				customer.title AS customerTitle, customer.address AS customerAddress, customer.linkman AS customerLinkman, customer.position AS customerPosition, customer.telephone AS customerTelephone, customer.fax AS customerFax, 
				shipmethod.title AS shipmethodTitle, shipmethod.entitle AS shipmethodEntitle, 
				loading.title AS loadingTitle, loading.entitle AS loadingEntitle, 
				loadingCountry.country AS loadingCountryTitle, loadingCountry.encountry AS loadingCountryEntitle, 
				discharge.title AS dischargeTitle, discharge.entitle AS dischargeEntitle, 
				dischargeCountry.country AS dischargeCountryTitle, dischargeCountry.encountry AS dischargeCountryEntitle, 
				paymentterm.title AS paymenttermTitle, paymentterm.entitle AS paymenttermEntitle, 
				deliveryterm.title AS deliverytermTitle, deliveryterm.entitle AS deliverytermEntitle, 
				currency.title AS currencyTitle, currency.symbol AS currencySymbol, currency.remark AS currencyRemark, 
				unit.title AS unitTitle, unit.entitle AS unitEntitle, unit.remark AS unitRemark, 
				m.username AS mname, c.username AS cname
			FROM `packinglist` 
			LEFT JOIN region ON (region.regionid=`packinglist`.regionid) 
			LEFT JOIN customer ON (customer.customerid=`packinglist`.customerid) 
			LEFT JOIN currency ON (currency.currencyid=`packinglist`.currencyid) 
			LEFT JOIN unit ON (unit.unitid=`packinglist`.unitid) 
			LEFT JOIN shipmethod ON (shipmethod.shipmethodid=`packinglist`.shipmethodid) 
			LEFT JOIN port AS loading ON (loading.portid=`packinglist`.loading) 
			LEFT JOIN `region` AS loadingCountry ON (loadingCountry.regionid=loading.regionid)
			LEFT JOIN port AS discharge ON (discharge.portid=`packinglist`.discharge) 
			LEFT JOIN `region` AS dischargeCountry ON (dischargeCountry.regionid=discharge.regionid)
			LEFT JOIN paymentterm ON (paymentterm.paymenttermid=`packinglist`.paymenttermid) 
			LEFT JOIN deliveryterm ON (deliveryterm.deliverytermid=`packinglist`.deliverytermid) 
			LEFT JOIN `user` AS m ON (m.userid=packinglist.modifier) 
			LEFT JOIN `user` AS c ON (c.userid=packinglist.creator) 
			WHERE packinglistid='".$this->kclass->input['packinglistid']."'
		")){
			$this->kclass->boinkIt('/s.php?module=invoice');
		}
		$items = $this->kclass->DB->query("
			SELECT `item`.*, 
				`unit`.title AS unitTitle, 
				`order`.orderno, `order`.created, 
				`product`.productno, `product`.title AS productTitle 
			FROM `item` 
			LEFT JOIN `order` ON (`order`.orderid=`item`.orderid) 
			LEFT JOIN `unit` ON (`unit`.unitid=`item`.unitid) 
			LEFT JOIN `product` ON (`product`.productid=`item`.productid) 
			WHERE `item`.killed=0 AND `item`.module='packinglist' AND `item`.mid='".$packinglist['packinglistid']."' 
			ORDER BY modified ASC, itemid ASC
		");
		if($this->kclass->DB->numRows()){
			$i=1;
			while($item = $this->kclass->DB->fetchArray($items)){
				$orderList .= '<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').'"><td title="创建时间：'.date('Y-m-d', $item['created']).'"><a href="/s.php?module=order&action=view&orderid='.$item['orderid'].'" target="_blank">'.$item['orderno'].'</a><input type="hidden" name="itemid['.$i.']" value="'.$item['itemid'].'"></td>
				<td>'.$item['productno'].' <a href="/s.php?module=product&action=view&productid='.$item['productid'].'" target="_blank">'.$item['productTitle'].'</a></td>
				<td></td>
				<td align="right">'.$item['quantity'].''.$item['unitTitle'].'<input type="hidden" id="quantity'.$i.'" value="'.$item['quantity'].'"></td>
				<td><input type="text" name="perCarton['.$i.']" value="'.$item['perCarton'].'" size="8" id="perCarton'.$i.'" onblur="dc.packingCalculator('.$i.',1)"></td>
				<td><input type="text" name="length['.$i.']" value="'.$item['length'].'" title="长" id="length'.$i.'" size="2">X<input type="text" name="width['.$i.']" title="宽" id="width'.$i.'" value="'.$item['width'].'" size="2">X<input type="text" title="高" name="height['.$i.']" id="height'.$i.'" value="'.$item['height'].'" size="2" onblur="dc.packingCalculator('.$i.',2)"></td>
				<td><input type="text" name="cartonNo['.$i.']" value="'.$item['cartonNo'].'" size="8"></td>
				<td><input type="text" name="perNetWeight['.$i.']" value="'.$item['perNetWeight'].'" size="8" id="perNetWeight'.$i.'" onblur="dc.packingCalculator('.$i.',3)"></td>
				<td><input type="text" name="perGrossWeight['.$i.']" value="'.$item['perGrossWeight'].'" size="8" id="perGrossWeight'.$i.'" onblur="dc.packingCalculator('.$i.',4)"></td>
				<td><input type="text" name="netWeight['.$i.']" value="'.$item['netWeight'].'" size="8"></td>
				<td><input type="text" name="grossWeight['.$i.']" value="'.$item['grossWeight'].'" size="8"></td>
				<td><input type="text" name="perVolume['.$i.']" id="perVolume'.$i.'" value="'.$item['perVolume'].'" size="8"></td>
				<td><input type="text" name="totalCartons['.$i.']" id="totalCartons'.$i.'" value="'.$item['totalCartons'].'" size="8"></td>
				<td><input type="text" name="totalNetWeight['.$i.']" id="totalNetWeight'.$i.'" value="'.$item['totalNetWeight'].'" size="8"></td>
				<td><input type="text" name="totalWeight['.$i.']" id="totalWeight'.$i.'" value="'.$item['totalWeight'].'" size="8"></td>
				<td><input type="text" name="totalVolume['.$i.']" value="'.$item['totalVolume'].'" id="totalVolume'.$i.'" size="8"></td>
				</tr>';
				$i++;
			}
		}
		if($invoice['onBoardDate']>0){
			$onBoardDate=date('Y-m-d', $invoice['onBoardDate']);
		}else{
			$onBoardDate='--';
		}
		if($invoice['dateOfArrival']>0){
			$dateOfArrival=date('Y-m-d', $invoice['dateOfArrival']);
		}else{
			$dateOfArrival='--';
		}
		
		$this->kclass->br2nl=true;
		$packinglist['remark']=$this->kclass->parseConvertValue($packinglist['remark']);
		$PLStr = 'PL'.$packinglist['packinglistno'];
		$currency = $this->kclass->chooserCurrency(array('name'=>'currencyid', 'hasBlank'=>1, 'width'=>100, 'selectedid'=>$packinglist['currencyid']));
		
		$upload = $this->kclass->upload(array('title'=>'相关附件：<span class="small darkred">请注意：包装清单里的附件不会出现在打印页面，仅在系统内部显示和存档，发货前拍照后此处上传存档。</span>'));
		$region = $this->kclass->chooserRegion(array('regionid'=>$packinglist['regionid'], 'hasBlank'=>1, 'regionid'=>$packinglist['regionid']));
$body = <<<EOF
<form action="/s.php?module=invoice&action=doupdatePacking" name="packinglist" method="post">
<input type="hidden" name="module" value="invoice">
<input type="hidden" name="action" value="doupdatePacking">
<input type="hidden" name="packinglistid" value="{$this->kclass->input['packinglistid']}">
<table class="hundred">
<thead>
<tr>
	<th colspan="4">修改发票为 {$invoice['invoiceno']} 的包装清单,包装编号为：<span class="darkred">{$PLStr}</span></th>
</tr>
</thead>
<tbody>
<tr class="odd">
	<td title="Invoice No">包装编号：</td>
	<td><input type="text" name="packinglistno" style="width:450px;" value="{$PLStr}"></td>
	<td>币　　种：<span class="red bold">*</span></td>
	<td><input type="hidden" name="refno" value="">{$currency}　</td></td>
</tr>
</tbody>
<thead>
<tr>
	<th colspan="4">客户 <span class="bold darkred">{$packinglist['title']}</span> 的信息：<span class="gray normal">客户不能修改，如要修改客户，则需要重新建立一张新的发票。</span></th>
</tr>
</thead>
<tbody>
<tr class="odd">
	<td title="Customer Title">客户名称：<span class="red bold">*</span></td>
	<td><input type="text" style="width:340px" name="title" value="{$packinglist['title']}"> <span class="gray small">客户需要的发票抬头</span></td>
	<td title="Customer Address">客户地址：<span class="red bold">*</span></td>
	<td><input type="text" style="width:330px" name="address" value="{$packinglist['address']}"> <span class="gray small">国家/省 在下面选择</span></td>
</tr>
<tr class="even">
	<td title="Remark">包装备注：</td>
	<td><textarea name="remark" style="width:444px;height:30px"></textarea></td>
	<td>国家地区：<span class="red bold">*</span></td>
	<td>{$region}</td>
</tr>
</tbody>
<thead>
<tr>
	<th colspan="4"><span class="right"><span class="normal" title="Currency">币种：</span>{$packinglist['currencyTitle']} {$packinglist['currencySymbol']} <span class="gray">{$packinglist['currencyRemark']}</span></span>发票信息</th>
</tr>
</thead>
<tbody>
<tr class="odd">
	<td width="100" title="Shipment Method">运输方式：</td>
	<td class="middle">{$packinglist['shipmethodTitle']} <span class="gray">{$packinglist['shipmethodEntitle']}</span></td>
	<td width="100" title="Delivery Term">交货条款：</td>
	<td class="middle">{$packinglist['deliverytermTitle']} <span class="gray">{$packinglist['deliverytermEntitle']}</span></td>
</tr>
<tr class="even">
	<td title="Port of Loading">装货地点：</td>
	<td class="middle">{$packinglist['loadingCountryTitle']} {$packinglist['loadingTitle']} <span class="gray">{$packinglist['loadingCountryTitle']} {$packinglist['loadingTitle']}</span></td>
	<td title="Shipment Date">交货日期：</td>
	<td class="middle"><span class="bold darkred">{$onBoardDate}</span></td>
</tr>
<tr class="odd">
	<td title="Port of Discharge">卸货地点：</td>
	<td class="middle">{$packinglist['dischargeCountryTitle']} {$packinglist['dischargeTitle']} <span class="gray">{$packinglist['dischargeCountryEntitle']} {$packinglist['dischargeEntitle']}</span></td>
	<td title="Date of Arrival">到达日期：</td>
	<td class="middle"><span class="bold darkred">{$dateOfArrival}</span></td>
</tr>
<tr class="odd">
	<td title="Final Destination">最终目的地：</td>
	<td class="middle">{$packinglist['finalDestination']}</td>
	<td title="1st Carrier">1<sup>st</sup>货运公司：</td>
	<td class="middle">{$packinglist['firstCarrier']}</td>
</tr>
<tr class="odd">
	<td title="Payment Term">支付条款：</td>
	<td class="middle">{$packinglist['paymenttermTitle']} <span class="gray">{$packinglist['paymenttermEntitle']}</span></td>
	<td title="2nd Carrier">2<sup>nd</sup>货运公司：</td>
	<td class="middle">{$packinglist['secondCarrier']}</td>
</tr>
<tr class="even">
	<td valign="top" title="Commodity">报关商品：</td>
	<td>{$packinglist['commodity']}</td>
	<td valign="top" title="Remark">备　　注：</td>
	<td>{$packinglist['invoiceRemark']}</td>
</tr>
</tbody>
</table>

<table class="tlist tsuite" align="center" width="100%">
<thead>
<tr><th colspan="19">包装明细</th></tr>
</thead>
<tbody class="small">
<tr>
	<td width="90" >订单号</td>
	<td width="120">产品编号/名称</td>
	<td >产品特有属性</td>
	<td align="right" width="50">总数量<span class="tiny"></span></td>
	<td width="85">每箱数量<span class="tiny">/PCS</span><span class="red bold">*</span></td>
	<td width="110">外箱规格<span class="tiny">/CM</span><span class="red bold">*</span></td>
	<td width="60">箱编号<span class="red bold">*</span></td>
	<td width="80">每个净重<span class="tiny">/KG</span><span class="red bold">*</span></td>
	<td width="80">每个毛重<span class="tiny">/KG</span></td>
	<td width="80">每箱净重<span class="tiny">/KG</span></td>
	<td width="80">每箱毛重<span class="tiny">/KG</span></td>
	<td width="90">每箱体积<span class="tiny">/CBM</span><span class="red bold">*</span></td>
	<td width="75">总箱数<span class="tiny">/CTN</span><span class="red bold">*</span></td>
	<td width="70">总净重<span class="tiny">/KG</span><span class="red bold">*</span></td>
	<td width="70">总重量<span class="tiny">/KG</span><span class="red bold">*</span></td>
	<td width="75">总体积<span class="tiny">/CBM</span><span class="red bold">*</span></td>
</tr>
{$orderList}
</tbody>
</table>
{$upload}
<table class="tlist tsuite" align="center" width="100%">
<tbody>
<tr class="odd">
	<td colspan="4" align="center">
		<input type="submit" id="submitButton" value="  保存  " accesskey="s">　　
		<input type="reset" value="  复位  ">
	</td>
</tr>
</tbody>
</table>
</form>
EOF;
		$this->kclass->page['title'] .= ' - 修改包装清单'.$packinglist['invoiceno'];
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . '- <a href="/s.php?module=invoice&action=view&invoiceid='.$packinglist['invoiceid'].'">'.$packinglist['invoiceno'].'</a> - 修改包装清单', 'right' => '<a href="/s.php?module=invoice">返回列表</a>', 'body'=>$body));
	}
	//
	function killPacking(){
		if($this->kclass->input['inquiryid']<=0){
			$this->kclass->boinkIt('/s.php?module=product');
		}
		if($this->kclass->input['confirm'] == 0){
			$this->kclass->boinkIt('/s.php?module=product&action=view&productid='.$this->kclass->input['productid']);
		}
		if($this->kclass->input['inquiryid'] < 0){
			$e = '<li>请从正确的页面进行操作，如果此问题持续存在，请联系网站管理员。</li>';
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '删除产品询价单',
				'text' => '您在删除产品询价单的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$inquiry = $this->kclass->DB->queryFirst("
			SELECT inquiry.inquiryid, inquiry.productid, 
				product.title AS product
			FROM productinquiry AS inquiry 
			LEFT JOIN product ON (product.productid=inquiry.productid) 
			WHERE inquiry.inquiryid='".$this->kclass->input['inquiryid']."'
		");
		if($inquiry){
			$this->kclass->DB->query("
				UPDATE `productinquiry`
				SET killed=".TIMENOW." 
				WHERE inquiryid='".$inquiry['inquiryid']."'
			");
			$this->kclass->messager(array(
				'title' => '删除产品询价单成功',
				'text' => '产品 <b>'.$inquiryid['product'].'</b> 下的 询价单 已成功被标记为删除!',
				'url' => '/s.php?module=product&action=view&productid='.$inquiry['productid'],
				'sec' => 2
			));
		}else{
			$this->kclass->messager(array(
				'title' => '删除产品询价单失败',
				'text' => '您要删除的产品询价单，不存在！',
				'url' => '/s.php?module=product&action=view&productid='.$inquiry['productid'],
				'sec' => 3
			));
		}
	}

	//
	function removePacking(){
		if($this->kclass->input['inquiryid']<=0){
			$this->kclass->boinkIt('/s.php?module=product');
		}
		$inquiry = $this->kclass->DB->queryFirst("
			SELECT inquiry.inquiryid, inquiry.productid, 
				product.title AS product
			FROM productinquiry AS inquiry 
			LEFT JOIN product ON (product.productid=inquiry.productid) 
			WHERE inquiry.inquiryid='".$this->kclass->input['inquiryid']."'
		");
		if($inquiry){
$body = <<<EOF
<form action="/s.php?module=product&action=killPacking" name="product" method="post">
<input type="hidden" name="module" value="product">
<input type="hidden" name="action" value="killPacking">
<input type="hidden" name="productid" value="{$inquiry['productid']}">
<input type="hidden" name="inquiryid" value="{$inquiry['inquiryid']}">
<table><thead>
<thead>
<tr>
	<th>删除产品：{$inquiry['product']}</th>
</tr>
</thead>
<tbody>
<tr class="even">
	<td class="middle">你确定要删除产品 <a href="/s.php?module=product&action=view&productid={$inquiry['productid']}" class="middle bold" target="_blank">{$inquiry['product']}</a> 的 <span class="big bold">询价单</span> 吗?</td>
</tr>
<tr class="odd">
	<td align="center">
		<label for="c1" class="red"><input type="radio" id="c1" name="confirm" value="1">是</label>　　
		<label for="c0" class="green"><input type="radio" id="c0" name="confirm" value="0" checked>否</label>
	</td>
</tr>
<tr class="even">
	<td align="center">
		<input type="submit" value="   保存   " accesskey="s">
		<input type="reset" value="   复位   ">
	</td>
</tr>
</tbody>
</table>
</form>
EOF;
		}else{
			$body = '要删除的询价单并不存在，请返回列表刷新后再操作。（如果问题持续存在，请联系系统管理员。）';
		}
		$this->kclass->page['title'] .= ' - '.$inquiry['product'].' - 删除产品询价';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - <a href="/s.php?module=product&action=view&productid='.$product['productid'].'">'.$inquiry['product'].'</a> - 删除产品询价', 'right' => '<a href="/s.php?module=product">返回列表</a>', 'body'=>$body));
	}

	//
	function revivalPacking(){
		if($this->kclass->input['inquiryid']<=0){
			$this->kclass->boinkIt('/s.php?module=product');
		}
		if($this->kclass->input['confirm'] == 0){
			$this->kclass->boinkIt('/s.php?module=product&action=view&productid='.$this->kclass->input['productid']);
		}
		if($this->kclass->input['inquiryid'] < 0){
			$e = '<li>请从正确的页面进行操作，如果此问题持续存在，请联系网站管理员。</li>';
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '恢复产品询价单',
				'text' => '您在恢复产品询价单的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$inquiry = $this->kclass->DB->queryFirst("
			SELECT inquiry.inquiryid, inquiry.productid, 
				product.title AS product
			FROM productinquiry AS inquiry 
			LEFT JOIN product ON (product.productid=inquiry.productid) 
			WHERE inquiry.inquiryid='".$this->kclass->input['inquiryid']."'
		");
		if($inquiry){
			$this->kclass->DB->query("
				UPDATE `productinquiry`
				SET killed=0
				WHERE inquiryid='".$inquiry['inquiryid']."'
			");
			$this->kclass->messager(array(
				'title' => '恢复产品询价单成功',
				'text' => '产品 <b>'.$inquiryid['product'].'</b> 下的 询价单 已成功被标记为恢复!',
				'url' => '/s.php?module=product&action=view&productid='.$inquiry['productid'],
				'sec' => 2
			));
		}else{
			$this->kclass->messager(array(
				'title' => '恢复产品询价单失败',
				'text' => '您要恢复的产品询价单，不存在！',
				'url' => '/s.php?module=product&action=view&productid='.$inquiry['productid'],
				'sec' => 3
			));
		}
	}

	//
	function restorePacking(){
		if($this->kclass->input['inquiryid']<=0){
			$this->kclass->boinkIt('/s.php?module=product');
		}
		$inquiry = $this->kclass->DB->queryFirst("
			SELECT inquiry.inquiryid, inquiry.productid, 
				product.title AS product
			FROM productinquiry AS inquiry 
			LEFT JOIN product ON (product.productid=inquiry.productid) 
			WHERE inquiry.inquiryid='".$this->kclass->input['inquiryid']."'
		");
		if($inquiry){
$body = <<<EOF
<form action="/s.php?module=product&action=revivalPacking" name="product" method="post">
<input type="hidden" name="module" value="product">
<input type="hidden" name="action" value="revivalPacking">
<input type="hidden" name="productid" value="{$inquiry['productid']}">
<input type="hidden" name="inquiryid" value="{$inquiry['inquiryid']}">
<table><thead>
<thead>
<tr>
	<th>恢复产品：{$inquiry['product']}</th>
</tr>
</thead>
<tbody>
<tr class="even">
	<td class="middle">你确定要恢复产品 <a href="/s.php?module=product&action=view&productid={$inquiry['productid']}" class="middle bold" target="_blank">{$inquiry['product']}</a> 的 <span class="big bold">询价单</span> 吗?</td>
</tr>
<tr class="odd">
	<td align="center">
		<label for="c1" class="red"><input type="radio" id="c1" name="confirm" value="1">是</label>　　
		<label for="c0" class="green"><input type="radio" id="c0" name="confirm" value="0" checked>否</label>
	</td>
</tr>
<tr class="even">
	<td align="center">
		<input type="submit" value="   保存   " accesskey="s">
		<input type="reset" value="   复位   ">
	</td>
</tr>
</tbody>
</table>
</form>
EOF;
		}else{
			$body = '要恢复的询价单并不存在，请返回列表刷新后再操作。（如果问题持续存在，请联系系统管理员。）';
		}
		$this->kclass->page['title'] .= ' - '.$inquiry['product'].' - 恢复产品询价';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - <a href="/s.php?module=product&action=view&productid='.$product['productid'].'">'.$inquiry['product'].'</a> - 恢复产品询价', 'right' => '<a href="/s.php?module=product">返回列表</a>', 'body'=>$body));
	}
}
?>