<?php
class order{
	function autoRun(){
		$this->kclass->page['title'] = '订单';
		$this->baseurl = '<a href="/s.php">首页</a> - <a href="/s.php?module=order">订单</a>';
		$this->right = '<span class="small"><a href="/s.php?module=order&action=find">查找</a>：
				<form style="display:inline-block" action="/s.php?module=order&action=list" name="order" method="post">
				<input type="text" style="width:50px;height:15px;margin-top:-3px" name="title" value="'.$this->kclass->input['title'].'">
				<input type="submit" value="找" accesskey="s" style="height:23px"></form></span>';

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
			case 'addOld':
				$this->addOld();
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
			case 'orderCopy':
				$this->orderCopy();
			break;
			case 'insertCopy':
				$this->insertCopy();
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
			case 'addInquiry':
				$this->addInquiry();
			break;
			case 'insertInquiry':
				$this->insertInquiry();
			break;
			case 'updateInquiry':
				$this->updateInquiry();
			break;
			case	'statistics':
				$this->statistics();
			break;
			case 'selectstatistics':
				$this->selectstatistics();
			break;
			case 'doupdateInquiry':
				$this->doupdateInquiry();
			break;
			case 'removeInquiry':
				$this->removeInquiry();
			break;
			case 'killInquiry':
				$this->killInquiry();
			break;
			case 'revivalInquiry':
				$this->revivalInquiry();
			break;
			case 'restoreInquiry':
				$this->restoreInquiry();
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
			case 'revise':
				$this->revise();
			break;
			case 'verify':
				$this->verify();
			break;
			case 'approve':
				$this->approve();
			break;
			case 'doverify':
				$this->doverify();
			break;
			case 'doapprove':
				$this->doapprove();
			break;
			case 'createPO':
				$this->createPO();
			break;
			case 'chooseLanguage':
				$this->chooseLanguage();
			break;
			default:
				$this->mmlist();
		}
	}

	/**
	 * 查看订单的修改版本信息
	 * @author creator mayinghao 2013-07-17 8:15
	 * @access public
	 * @param none 没有参数
	 * @return void 没有返回信息
	 * @throws none 没有异常
	 */
	function revise(){
		if($this->kclass->input['reviseid']>0){
			$revise = $this->kclass->DB->queryFirst("
				SELECT `revise`.ptext,`revise`.version,`revise`.remark
				FROM `revise`
				WHERE reviseid='".$this->kclass->input['reviseid']."'
			");
			if($revise){
				$this->kclass->page['onload'] .= "dc.tabs({'id':'order'});dc.tabhover()";
				$this->kclass->page['title'] .= ' - 订单版本R'.$revise['version'];
				$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl.' - 订单版本信息　版本R'.$revise['version'], 'right' => '', 'body'=>$revise['ptext']));
			}else{
				$this->kclass->messager(array(
						'title' => '查看订单历史版本',
						'text' => '没有此历史版本!',
						'url' => '/s.php?module=order&action=view&orderid='.$this->kclass->input['orderid'],
						'sec' => 5
				));
			}
		}
	}

	/**
	 * 返回订单的HTML信息
	 * @author creator mayinghao 2013-07-17 8:15
	 * @access public
	 * @param none 没有参数
	 * @return string 返回HTML字符串
	 * @throws none 没有异常
	 */
	function _getOrderHtml(){
		if(!$this->kclass->input['orderid'] > 0)$this->kclass->boinkIt('/s.php?module=order');
			$order = $this->kclass->DB->queryFirst("
				SELECT `order`.*,
					route.title AS routeTitle, route.caption AS routeCaption,
					customer.title AS customer, customer.address AS customerAddress, customer.linkman AS customerLinkman, customer.position AS customerPosition, customer.telephone AS customerTelephone, customer.fax AS customerFax,
					supplier.title AS supplierTitle, supplier.address AS supplierAddress, supplier.linkman AS supplierLinkman, supplier.position AS supplierPosition, supplier.telephone AS supplierTelephone, supplier.fax AS supplierFax,
					shipmethod.title AS shipmethodTitle, shipmethod.entitle AS shipmethodEntitle,
					loading.title AS loadingTitle, loading.entitle AS loadingEntitle,
					loadingCountry.country AS loadingCountryTitle, loadingCountry.encountry AS loadingCountryEntitle,
					discharge.title AS dischargeTitle, discharge.entitle AS dischargeEntitle,
					dischargeCountry.country AS dischargeCountryTitle, dischargeCountry.encountry AS dischargeCountryEntitle,
					paymentterm.title AS paymenttermTitle, paymentterm.entitle AS paymenttermEntitle,
					deliveryterm.title AS deliverytermTitle, deliveryterm.entitle AS deliverytermEntitle,
					currency.title AS currencyTitle, currency.symbol AS currencySymbol, currency.remark AS currencyRemark,
					m.username AS mname, c.username AS cname,t.username AS tname,d.username AS dname
				FROM `order`
				LEFT JOIN orderroute AS route ON (route.routeid=`order`.routeid)
				LEFT JOIN customer ON (customer.customerid=`order`.customerid)
				LEFT JOIN supplier ON (supplier.supplierid=`order`.supplierid)
				LEFT JOIN currency ON (currency.currencyid=`order`.currencyid)
				LEFT JOIN shipmethod ON (shipmethod.shipmethodid=`order`.shipmethodid)
				LEFT JOIN port AS loading ON (loading.portid=`order`.loading)
				LEFT JOIN `region` AS loadingCountry ON (loadingCountry.regionid=loading.regionid)
				LEFT JOIN port AS discharge ON (discharge.portid=`order`.discharge)
				LEFT JOIN `region` AS dischargeCountry ON (dischargeCountry.regionid=discharge.regionid)
				LEFT JOIN paymentterm ON (paymentterm.paymenttermid=`order`.paymenttermid)
				LEFT JOIN deliveryterm ON (deliveryterm.deliverytermid=`order`.deliverytermid)
				LEFT JOIN `user` AS m ON (m.userid=order.modifier)
				LEFT JOIN `user` AS c ON (c.userid=order.creator)
				LEFT JOIN `user` AS d ON (d.userid=order.director)
				LEFT JOIN `user` AS t ON (t.userid=order.tracker)
				WHERE `order`.killed=0 AND orderid='".$this->kclass->input['orderid']."'
			");
		if(!$order)	$this->kclass->boinkIt('/s.php?module=order&action=find');
		if($order['type']=='PI'){
			$orderTitleStr = 'PI (销售订单)';
		}else{
			$orderTitleStr = 'PO (采购订单)';
		}
		if($order['modifier']>0){
			$modified = '，'.$order['mname'].' 于 '.date('y-m-d H:i:s', $order['modified']).' 最后修改';
		}
		if($order['customerid']>0){
			$customer=$order['customer'];
		}else{
			$customer=$order['customerTitle'];
		}
		$created = date('y-m-d H:i:s', $order['created']);
		if($attach!= false){
			$attachs = '<div class="clear">'.$attach.'</div>';
		}
		$shipmentDate=date('Y-m-d', $order['shipmentDate']);
		$item=$this->kclass->relatedOrderItem(array('orderid'=>$order['orderid'], 'title'=>'客户'));
		$total = $item['total'] ;
		$total = number_format($total, 2);
		$fee=$this->kclass->relatedFee(array('orderid'=>$order['orderid']));
		$total += $fee['total'];
		$discount=$this->kclass->relatedDiscount(array('module'=>'order','mid'=>$order['orderid'],'total'=>$total));
		$revise['arr']=serialize(array_merge($order,$item['array'],$fee['array'],$discount['array']));

		$revise['html']=<<<EOF
<div class="bold darkred big" style="clear:both;">{$order['ordertitle']}　　　　　<span class="gray small">业务人员：<span class="bold darkred">{$order['dname']}</span></span>　　　　　<span class="gray small">跟单人员：<span class="bold darkred">{$order['tname']}</span></span>　　　　　<span class="gray small">由 {$order['cname']} 于 {$created} 建立{$modified}</span></div>
<table class="hundred">
<thead>
<tr>
	<th colspan="4">基本信息 (Basic Information)</th>
</tr>
</thead>
<tbody>
<tr>
	<td width="573" colspan="2" class="bold" align="center">客户 (Buyer)</td>
	<td width="573" colspan="2" class="bold gray" align="center">供应商 (Seller)</td>
</tr>
<tr class="odd">
	<td width="70">订单号：</td>
	<td>{$order['customerNo']}</td>
	<td width="70" class="gray">订单号：</td>
	<td class="gray">{$order['supplierNo']}</td>
</tr>
<tr class="odd">
	<td>名　称：</td>
	<td><a href="/s.php?module=customer&action=view&customerid={$order['customerid']}" target="_blank">{$customer}</a></td>
	<td class="gray">名　称：</td>
	<td class="gray">{$order['supplierTitle']}</td>
</tr>
<tr class="odd">
	<td>地　址：</td>
	<td>{$order['customerAddress']}</td>
	<td class="gray">地　址：</td>
	<td class="gray">{$order['supplierAddress']}</td>
</tr>
<tr class="odd">
	<td>联系人：</td>
	<td>{$order['customerLinkman']} <span class="gray">{$order['customerPosition']}</span></td>
	<td class="gray">联系人：</td>
	<td class="gray">{$order['supplierLinkman']} <span class="gray">{$order['supplierPosition']}</span></td>
</tr>
<tr class="odd">
	<td>电　话：</td>
	<td>{$order['customerTelephone']}</td>
	<td class="gray">电　话：</td>
	<td class="gray">{$order['supplierTelephone']}</td>
</tr>
<tr class="odd">
	<td>传　真：</td>
	<td>{$order['customerFax']}</td>
	<td class="gray">传　真：</td>
	<td class="gray">{$order['supplierFax']}</td>
</tr>
</tbody>
</table>
<table class="hundred">
<thead>
<tr>
	<th colspan="4"><span class="right"><span class="normal" title="Currency">币种：</span><span class="darkred">{$order['currencyTitle']} {$order['currencySymbol']}</span> <span class="gray">{$order['currencyRemark']}</span></span>一般信息 (General Information)</th>
</tr>
</thead>
<tbody>
<tr>
	<td width="70" title="Shipment Method">运输方式：</td>
	<td width="494" class="middle">{$order['shipmethodTitle']} <span class="gray">{$order['shipmethodEntitle']}</span></td>
	<td width="70" title="Payment Term">支付条款：</td>
	<td class="middle">{$order['paymenttermTitle']} <span class="gray">{$order['paymenttermEntitle']}</span></td>
</tr>
<tr>
	<td title="Port of Loading">装货地点：</td>
	<td class="middle">{$order['loadingCountryTitle']} {$order['loadingTitle']} <span class="gray">{$order['loadingCountryTitle']} {$order['loadingTitle']}</span></td>
	<td title="Delivery Term">交货条款：</td>
	<td class="middle">{$order['deliverytermTitle']} <span class="gray">{$order['deliverytermEntitle']}</span></td>
</tr>
<tr>
	<td title="Port of Discharge">卸货地点：</td>
	<td class="middle">{$order['dischargeCountryTitle']} {$order['dischargeTitle']} <span class="gray">{$order['dischargeCountryEntitle']} {$order['dischargeEntitle']}</span></td>
	<td title="Shipment Date">交货日期：</td>
	<td class="middle"><span class="bold darkred">{$shipmentDate}</span></td>
</tr>
</tbody>
</table>
<table class="hundred">
<thead>
<tr>
	<th colspan="4">标准信息 (Critertion Information)</th>
</tr>
</thead>
<tbody>
<tr>
	<td width="70" valign="top" title="Labeling">标签说明：</td>
	<td width="494" class="middle">{$order['labeling']}</td>
	<td width="70" valign="top" title="Packing">包装说明：</td>
	<td class="middle">{$order['packing']}</td>
</tr>
<tr>
	<td valign="top" title="Special">特别说明：</td>
	<td class="middle">{$order['special']}</td>
	<td valign="top" title="Remark">备　　注：</td>
	<td class="middle">{$order['remark']}</td>
</tr>
</tbody>
</table>
<table class="hundred">
<thead>
<tr>
	<th colspan="12"><span class="right"><span class="normal" title="Currency">币种：</span><span class="darkred">{$order['currencyTitle']} {$order['currencySymbol']}</span> <span class="gray">{$order['currencyRemark']}</span></span>订单明细 (Item Information)</th>
</tr>
</thead>
<tbody>
{$item['tr']}
</tbody>
</table>
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
<table class="hundred">
<thead>
<tr>
	<th colspan="4">折扣信息 (Discount Information)</th>
</tr>
</thead>
<tbody>
{$discount['tr']}
</tbody>
</table>
<table class="hundred">
<tbody>
<tr><td align="right">总计(Total Amount)：<span class="middle bold darkred">{$order['currencyTitle']} {$order['currencySymbol']}{$total}</span></td></tr>
</tbody>
</table>
EOF;
		return $revise;
	}
	//
	function insertCopy(){
     if($this->kclass->input['requestMethod'] != 'post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			$route = $this->kclass->DB->queryFirst("SELECT * FROM `orderroute` WHERE routeid='".$this->kclass->input['routeid']."'");
			if(!$route){
				$e .= '<li>请选择 订单路径。</li>';
			}
			if($this->kclass->input['m']=='old'){
				$orderno=$this->kclass->input['orderno'];
			}else{
				if($this->kclass->input['noType']==0){
					$orderno=$this->kclass->id(array('order'=>TIMENOW));
				}elseif($this->kclass->input['noType']==1){
					$orderno=$this->kclass->input['orderno'];
				}
			}
			$order=$this->kclass->DB->queryFirst("SELECT orderid FROM `order` WHERE orderno='".$orderno."' LIMIT 0,1");
			if($order['orderid']){
				$e='<li>编号<b>'.$orderno.'</b> 在订单中 已存在</li>';
			}

				$this->kclass->input['paymentamount']=trim($this->kclass->input['paymentamount']);
			if($this->kclass->input['paymentamount']!=''){
			if(!is_numeric($this->kclass->input['paymentamount'])){
				$e.='<li>支付金额必须为数字，请输入数字</li>';
			}
			}else{
				$this->kclass->input['paymentamount']=0;
			}
			if($this->kclass->input['tracker'] <= 0){
				$e .= '<li>请选择跟单人员。</li>';
			}
			if($this->kclass->input['currencyid'] <= 0){
				$e .= '<li>请选择订单中的 币种。</li>';
			}
			if(!($this->kclass->input['customerid']>0 OR $this->kclass->input['customer']!='')){
				$e .= '<li>请选择订单中的 客户。</li>';
			}
			/*else{
				$verify=$this->kclass->DB->queryFirst("SELECT ifverify FROM `customer` WHERE `customer`.customerid='".$this->kclass->input['customerid']."'");
			if($verify['ifverify']!=1){
				$e.='<li>客户未通过审核或未审核，请审核客户以后再试。</li>';
			}
			}*/
			if($this->kclass->input['supplierid'] <= 0){
				$e .= '<li>请选择订单中的 供应商。</li>';
			}
			if($this->kclass->input['shipmentDate'] == ''){
				$e .= '<li>请填写订单的 交货日期。</li>';
			}
			$j = count($this->kclass->input['itemProductid']);
			$hasItem=$hasPartItem=0;
			for($i = 1; $i < $j+1; $i++){
				if($this->kclass->input['itemProductid'][$i]>0 AND $this->kclass->input['itemQuantity'][$i]!='' AND $this->kclass->input['itemUnitid'][$i]>0 AND $this->kclass->input['itemPrice'][$i]>=0){
					$hasItem=1;
				}
			}
			$m=count($this->kclass->input['itemPartsid']);
			for($n=1;$n<$m+1;$n++){
				if($this->kclass->input['itemPartsid'][$n]>0 AND $this->kclass->input['itemPartsQuantity'][$n]!='' AND $this->kclass->input['itemPartsUnitid'][$n]>0 AND $this->kclass->input['itemPartsPrice'][$n]>=0){
					$hasPartItem=1;
				}
			}
			if($hasItem==0 AND $hasPartItem==0){
				$e .= '<li>需要填写至少有一条订单明细，才能建立订单。</li>';
			}
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '新建订单',
				'text' => '您在新建订单的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$sd = explode('-', $this->kclass->input['shipmentDate']);
		$shipmentDate = mktime(0,0,0,$sd[1],$sd[2],$sd[0]);
       
        $pd=explode('-',$this->kclass->input['paymentDate']);
		$paymentDate=mktime(0,0,0,$pd[1],$pd[2],$pd[0]);
		 $paymentDate=$this->kclass->iif($this->kclass->input['paymentDate']!='',$paymentDate,0);
		$typelist = explode(',', $route['typelist']);
		$csid = explode(',', $route['defaultcsid']);
		$dv='';
		$parentid=0;
		for($i = 0; $i < $route['copies']; $i++){
			$customer=$this->kclass->DB->queryFirst("
				SELECT c.abbr AS customerAbbr,c.ifverify AS cverify,r.regionid,r.countryid
				FROM customer AS c
				LEFT JOIN region AS r ON (r.regionid=c.regionid)
				WHERE c.customerid='".$this->kclass->input['customerid']."'
			");
			
			if($customer['countryid']>0){
				$region=$this->kclass->DB->queryFirst("SELECT abbr2 FROM region WHERE regionid='".$customer['countryid']."' LIMIT 0,1");
				$countryAbbr=$region['abbr2'];
			}else{
				$region=$this->kclass->DB->queryFirst("SELECT abbr2 FROM region WHERE regionid='".$customer['regionid']."' LIMIT 0,1");
				$countryAbbr=$region['abbr2'];
			}
			$dv = explode(':', $csid[$i]);
			if($dv[0]=='c'){
				$customerid=$this->kclass->input['customerid'];
				$customerNo=$this->kclass->input['customerNo'];
			}else{
				$customerid=$dv[0];
				$orderno = 'PO'.substr($orderno,2);
				$ordertitle = 'PO'.substr($ordertitle,2);
				$customerNo=$orderno;
			}
			if($dv[1]=='s'){
				$supplierid=$this->kclass->input['supplierid'];
				$this->kclass->input['supplierNo'] = 'PO'.substr($this->kclass->input['supplierNo'],2);
				$supplierNo=$this->kclass->input['supplierNo'];
			}else{
				$supplierid=$dv[1];
				$supplierNo=$orderno;
			}
			
			$this->kclass->DB->query("
				INSERT INTO `order` (`parentid`, `paymenttime`,`paymentamount`,`paymentstatus`,`routeid`,`noType`, `type`, `orderno`, `customerid`, `supplierid`,`customerTitle`, `customerNo`, `supplierNo`, `loading`, `discharge`, `currencyid`, `deliverytermid`, `paymenttermid`, `shipmethodid`, `shipmentDate`, `packing`, `labeling`, `special`, `remark`,killed,`status`, `modified`,`director`, `tracker`,`creator`, `created`)
					VALUES ('".$parentid."','".$paymentDate."','".$this->kclass->input['paymentamount']."','".$this->kclass->input['paymentstatusid']."','".$route['routeid']."','".$this->kclass->input['noType']."', '".$typelist[$i]."', '".$orderno."', '".$customerid."', '".$supplierid."','".$this->kclass->input['customer']."', '".$customerNo."',  '".$supplierNo."', '".$this->kclass->input['loading']."', '".$this->kclass->input['discharge']."', '".$this->kclass->input['currencyid']."', '".$this->kclass->input['deliverytermid']."', '".$this->kclass->input['paymenttermid']."', '".$this->kclass->input['shipmethodid']."', '".$shipmentDate."', '".$this->kclass->input['packing']."', '".$this->kclass->input['labeling']."', '".$this->kclass->input['special']."', '".$this->kclass->input['remark']."', '0','0', '".TIMENOW."', '".$this->kclass->input['director']."','".$this->kclass->input['tracker']."','".$this->kclass->user['userid']."', '".TIMENOW."')
			");
			$orderid = $this->kclass->DB->insertID();
			if($i==0){
				$parentid = $orderid;
			}
			$pitem=$partItem='';
			// order item  产品明细
			for($k = 1; $k < (count($this->kclass->input['itemProductid'])+1); $k++){
				$this->kclass->input['itemQuantity'][$k]=intVal($this->kclass->input['itemQuantity'][$k]);
				$this->kclass->input['itemPrice'][$k]=floatVal($this->kclass->input['itemPrice'][$k]);
				$this->kclass->input['itemDiscount'][$k]=intVal($this->kclass->input['itemDiscount'][$k]);
				if($this->kclass->input['itemDiscount'][$k]<0 OR $this->kclass->input['itemDiscount'][$k]>100){
					$this->kclass->input['itemDiscount'][$k]=100;
				}
				$amount = $this->kclass->input['itemQuantity'][$k] * $this->kclass->input['itemPrice'][$k];
				if($this->kclass->input['itemDiscount'][$k]<100){
					$amount = $amount * $this->kclass->input['itemDiscount'][$k] / 100;
				}
				$amount = number_format($amount, 2, '.', '');
				if($this->kclass->input['itemProductid'][$k]>0 AND $this->kclass->input['itemQuantity'][$k]!='' AND $this->kclass->input['itemUnitid'][$k]>0 AND $this->kclass->input['itemPrice'][$k]>=0){
					$productItem=$this->kclass->DB->queryFirst("
						SELECT pi.productid,pi.attributevalue,
							p.title
						FROM productitem AS pi
						LEFT JOIN product AS p ON (p.productid=pi.productid)
						WHERE pi.productitemid='".$this->kclass->input['itemProductid'][$k]."'
					");
					$pitem.='-'.$this->kclass->input['itemQuantity'][$k].$productItem['title'];
					$this->kclass->DB->query("
						INSERT INTO `item` (`module`,`mid`,`productid`,`productitemid`,`productAttributes`,`caption`,`brandid`,`packingid`,`typeid`,`quantity`,`discount`,`unitid`,`price`,`amount`,`modified`,`creator`,`created`)
						VALUES ('order','".$orderid."','".$productItem['productid']."','".$this->kclass->input['itemProductid'][$k]."','".$productItem['attributevalue']."','".$this->kclass->input['itemCaption'][$k]."','".$this->kclass->input['itemBrandid'][$k]."','".$this->kclass->input['itemPackingid'][$k]."','".$this->kclass->input['itemTypeid'][$k]."','".$this->kclass->input['itemQuantity'][$k]."','".$this->kclass->input['itemDiscount'][$k]."','".$this->kclass->input['itemUnitid'][$k]."','".$this->kclass->iif($typelist[$i]=='PI', $this->kclass->input['itemPrice'][$k], 0)."','".$this->kclass->iif($typelist[$i]=='PI', $amount, 0)."','".TIMENOW."','".$this->kclass->user['userid']."','".TIMENOW."')
					");
				}
			}

			//配件明细
			for($k = 1; $k <= count($this->kclass->input['itemPartsid']); $k++){
				$this->kclass->input['itemPartsQuantity'][$k]=intVal($this->kclass->input['itemPartsQuantity'][$k]);
				$this->kclass->input['itemPartsPrice'][$k]=floatVal($this->kclass->input['itemPartsPrice'][$k]);
				$this->kclass->input['itemPartsDiscount'][$k]=intVal($this->kclass->input['itemPartsDiscount'][$k]);
				if($this->kclass->input['itemPartsDiscount'][$k]<0 OR $this->kclass->input['itemPartsDiscount'][$k]>100){
					$this->kclass->input['itemPartsDiscount'][$k]=100;
				}

				$amount = $this->kclass->input['itemPartsQuantity'][$k] * $this->kclass->input['itemPartsPrice'][$k];
				if($this->kclass->input['itemPartsDiscount'][$k]<100){
					$amount = $amount * $this->kclass->input['itemPartsDiscount'][$k] / 100;
				}
				$amount = number_format($amount, 2, '.', '');
				if($this->kclass->input['itemPartsid'][$k]>0 AND $this->kclass->input['itemPartsQuantity'][$k]!='' AND $this->kclass->input['itemPartsUnitid'][$k]>0 AND $this->kclass->input['itemPartsPrice'][$k]>=0){
					$part=$this->kclass->DB->queryFirst("
						SELECT p.materialid,
							m.materialno
						FROM product p
						LEFT JOIN material AS m ON (m.materialid=p.materialid)
						WHERE productid='".$this->kclass->input['itemPartsid'][$k]."'
					");
					$partItem='-PARTS';
					$this->kclass->DB->query("
						INSERT INTO `item` (`module`,`mid`,`materialid`,`productid`,`caption`,`brandid`,`packingid`,`typeid`,`quantity`,`discount`,`unitid`,`price`,`amount`,`modified`,`creator`,`created`)
						VALUES ('order','".$orderid."','".$part['materialid']."','".$this->kclass->input['itemPartsid'][$k]."','".$this->kclass->input['itemPartsCaption'][$k]."','".$this->kclass->input['itemPartsBrandid'][$k]."','".$this->kclass->input['itemPartsPackingid'][$k]."','".$this->kclass->input['itemPartsTypeid'][$k]."','".$this->kclass->input['itemPartsQuantity'][$k]."','".$this->kclass->input['itemPartsDiscount'][$k]."','".$this->kclass->input['itemPartsUnitid'][$k]."','".$this->kclass->iif($typelist[$i]=='PI', $this->kclass->input['itemPartsPrice'][$k], 0)."','".$this->kclass->iif($typelist[$i]=='PI', $amount, 0)."','".TIMENOW."','".$this->kclass->user['userid']."','".TIMENOW."')
					");
				}
			}
			$orderTitle=$orderno.'-'.$countryAbbr.'-'.$customer['customerAbbr'].$pitem.$partItem;
			$this->kclass->DB->query("UPDATE `order` SET ordertitle='".$orderTitle."' WHERE orderid='".$orderid."'");
			$this->kclass->updateFee(array('module'=>'order','mid'=>$orderid));
			$this->kclass->updateDiscount(array('module'=>'order','mid'=>$orderid));
		}
		$this->kclass->updateAttachs(array('module'=>'order', 'mid'=>$parentid));

		$this->kclass->messager(array(
			'title' => '复制订单',
			'text' => '订单 <b>'.$orderno.'</b> 已复制成功!',
			'url' => '/s.php?module=order&action=view&orderid='.$parentid,
			'sec' => 2
		));
	}
	//
	function orderCopy(){
		$orderno=$this->kclass->id(array('order'=>TIMENOW));
		$route = $this->kclass->chooserOrderRoute(array('name'=>'routeid', 'selectedid'=>1));
		$order = $this->kclass->DB->queryFirst("
			SELECT `order`.*, route.title AS routeTitle,
					customer.title AS customerTitle, customer.cntitle AS customerCntitle,
					supplier.title AS supplierTitle, supplier.entitle AS supplierEntitle
			FROM `order`
			LEFT JOIN `orderroute` AS route ON (`route`.routeid=`order`.routeid)
			LEFT JOIN customer ON (customer.customerid=`order`.customerid)
			LEFT JOIN supplier ON (supplier.supplierid=`order`.supplierid)
			WHERE `order`.killed=0 AND `orderid`='".$this->kclass->input['orderid']."'
		");
		//print_r($order);
		if(!$order){
			$e.='<li>订单数据错误</li>';
		}else{
			/*if($order['type']=='PI'){
				$title = '客户';
				if($this->kclass->user['userid']!=$order['director']){
					$e.='<li>您不是订单 <b>'.$order['orderno'].'</b> 的业务员无权限进行修改!</li>';
				}
			}elseif($order['type']=='PO'){
				$title = '供应商';
			}*/
		}
		if($e){
			$this->kclass->messager(array(
				'title' => '修改订单',
				'text' => '您在修改订单的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => REFERER,
				'sec' => 3
			));
		}

		$shipmentDate = date('Y-m-d', $order['shipmentDate']);
		$paymentDate=date('Y-m-d',$order['paymenttime']);
		$this->kclass->br2nl=true;
		$order['labeling']=$this->kclass->parseConvertValue($order['labeling']);
		$order['packing']=$this->kclass->parseConvertValue($order['packing']);
		$order['special']=$this->kclass->parseConvertValue($order['special']);
		$order['remark']=$this->kclass->parseConvertValue($order['remark']);
		$director = $this->kclass->chooserTracker(array('name'=>'director','condition'=>'positionid=7','width'=>80,'selectedid'=>$order['director']));
		$tracker = $this->kclass->chooserTracker(array('name'=>'tracker','condition'=>'positionid=7','width'=>80,'selectedid'=>$order['tracker']));
		$loading = $this->kclass->chooserPort(array('name'=>'loading', 'hasBlank'=>1, 'width'=>450, 'selectedid'=>$order['loading']));
		$discharge = $this->kclass->chooserPort(array('name'=>'discharge', 'hasBlank'=>1, 'width'=>450, 'selectedid'=>$order['discharge']));
		$paymentterm = $this->kclass->chooserPaymentterm(array('name'=>'paymenttermid', 'hasBlank'=>1, 'width'=>450, 'selectedid'=>$order['paymenttermid']));
		$paymentstatus=$this->kclass->chooserPaymentstatus(array('name'=>'paymentstatusid','hasBlank'=>1,'width'=>300,'selectedid'=>$order['paymentstatus']));
		$deliveryterm = $this->kclass->chooserDeliveryterm(array('name'=>'deliverytermid', 'hasBlank'=>1, 'width'=>450, 'selectedid'=>$order['deliverytermid']));
		$shipmethod = $this->kclass->chooserShipmethod(array('name'=>'shipmethodid', 'hasBlank'=>1, 'width'=>450, 'selectedid'=>$order['shipmethodid']));
		$currency = $this->kclass->chooserCurrency(array('name'=>'currencyid', 'hasBlank'=>1, 'width'=>100, 'selectedid'=>$order['currencyid']));
		$upload = $this->kclass->upload(array('title'=>'相关附件：', 'module'=>'order', 'mid'=>$order['orderid']));

		if(strpos($order['ordertitle'],'-')>0){
			$customerStr = substr($order['ordertitle'],strpos($order['ordertitle'],'-'),(strpos($order['ordertitle'],'+')-12));
		}else{
			$customerStr = '';
		}
		if(strpos($order['ordertitle'],'+')>0){
			$productStr = substr($order['ordertitle'],strpos($order['ordertitle'],'+'));
		}else{
			$productStr = '';
		}
		// 审核或者审批后修改  修改原因
		if($order['ifVerify']!=0 OR $order['ifApprove']!=0){
			$updateReason='<tr class="odd"><td class="red bold">修改原因：</td><td><textarea name="reviseRemark" style="width:444px;height:100px"></textarea></td><td></td><td></td></tr>';
		}
		$items = $this->kclass->DB->query("SELECT * FROM `item` WHERE killed=0 AND module='order' AND mid='".$order['orderid']."' ORDER BY itemid ASC, modified ASC");
		$k=1;
		$n=1;
		if($this->kclass->DB->numRows()){
			$this->kclass->tbline+=1;
			$i=1;
			while($item = $this->kclass->DB->fetchArray($items)){
				if($item['productitemid']>0){
					$product = $this->kclass->DB->queryFirst("
						SELECT `product`.title,`product`.entitle
						FROM  `productitem`
						LEFT JOIN `product` ON (`productitem`.productid = `product`.productid)
						WHERE productitemid='".$item['productitemid']."'
					");
					$seletStr = '<input type="hidden" id="tditemProductid'.$k.'input" value="'.$product['entitle'].'" />';
					$itemtr .= '<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').'" id=rec'.$i.'>
					<td><span id="tditemProductid'.$k.'">'.$seletStr.'</span><input type="hidden" name="itemId['.$k.']" value="'.$item['itemid'].'">'.$this->kclass->chooserProductItem(array('name'=>'itemProductid['.$k.']','selectName'=>'itemProductid'.$k, 'selectedid'=>$item['productitemid'], 'hasBlank'=>1, 'width'=>500)).'</td>
					<td><input type="text" name="itemCaption['.$k.']" value="'.$item['caption'].'" size="65"></td>
					
					<td>'.$this->kclass->chooserBrand(array('name'=>'itemBrandid['.$k.']', 'hasBlank'=>1, 'width'=>80, 'selectedid'=>$item['brandid'])).'</td>
					<td>'.$this->kclass->chooserItemType(array('name'=>'itemTypeid['.$k.']','width'=>80)).'</td>
					<td>'.$this->kclass->chooserPacking(array('name'=>'itemPackingid['.$k.']', 'hasBlank'=>1, 'width'=>100, 'selectedid'=>$item['packingid'])).'</td>
					<td><input type="text" name="itemDiscount['.$k.']" value="'.$item['discount'].'" size="3"></td>
					<td><input type="text" name="itemQuantity['.$k.']" value="'.$item['quantity'].'" size="3" onblur="dc.returnProductStr('.$k.',this.value)"></td>
					<td>'.$this->kclass->chooserUnit(array('name'=>'itemUnitid['.$k.']', 'hasBlank'=>1, 'width'=>80, 'selectedid'=>$item['unitid'])).'</td>
					<td><input type="text" name="itemPrice['.$k.']" value="'.$item['price'].'" size="5"></td>
					<td><input type="text" name="itemOrdering['.$k.']" value="'.$item['ordering'].'" size="5"></td>
					<td><input type="checkbox" name="itemKill['.$k.']" value="'.$item['itemid'].'" onchange="copydel('.$i.')"></td>
					</tr>';
					$k++;
				}else{
					$parts .= '<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').'" id=rec'.$i.'>
					<td><input type="hidden" name="itemPartsId['.$n.']" value="'.$item['itemid'].'">'.$this->kclass->chooserParts(array('name'=>'itemPartsid['.$n.']', 'selectedid'=>$item['productid'], 'hasBlank'=>1, 'width'=>500)).'</td>
					<td><input type="text" name="itemPartsCaption['.$n.']" value="'.$item['caption'].'" size="65"></td>
					<td>'.$this->kclass->chooserItemType(array('name'=>'itemPartsTypeid['.$n.']','width'=>80)).'</td>
					<td>'.$this->kclass->chooserBrand(array('name'=>'itemPartsBrandid['.$n.']', 'hasBlank'=>1, 'width'=>80, 'selectedid'=>$item['brandid'])).'</td>
					<td>'.$this->kclass->chooserPacking(array('name'=>'itemPartsPackingid['.$n.']', 'hasBlank'=>1, 'width'=>100, 'selectedid'=>$item['packingid'])).'</td>
					<td><input type="text" name="itemPartsDiscount['.$n.']" value="'.$item['discount'].'" size="3"></td>
					<td><input type="text" name="itemPartsQuantity['.$n.']" value="'.$item['quantity'].'" size="3"></td>
					<td>'.$this->kclass->chooserUnit(array('name'=>'itemPartsUnitid['.$n.']', 'hasBlank'=>1, 'width'=>80, 'selectedid'=>$item['unitid'])).'</td>
					<td><input type="text" name="itemPartsPrice['.$n.']" value="'.$item['price'].'" size="5"></td>
					<td><input type="text" name="itemPartsOrdering['.$n.']" value="'.$item['ordering'].'" size="5"></td>
					<td><input type="checkbox" name="itemPartsKill['.$n.']" value="'.$item['itemid'].'" onchange="copydel('.$i.')"></td>
					
					</tr>';
					$n++;
				}
				$i++;
			}
		}
		for($i = $k; $i < ($k+5); $i++){
			$itemtrs .= '<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').'">
			<td><span id="tditemProductid'.$i.'"></span>'.$this->kclass->chooserProductItem(array('name'=>'itemProductid['.$i.']','selectName'=>'itemProductid'.$i, 'hasBlank'=>1, 'width'=>500)).'</td>
			<td><input type="text" name="itemCaption['.$i.']" value="'.$item['caption'].'"  size="65"></td>
			<td>'.$this->kclass->chooserBrand(array('name'=>'itemBrandid['.$i.']', 'hasBlank'=>1, 'width'=>80,'selectedid'=>1)).'</td>
			<td>'.$this->kclass->chooserItemType(array('name'=>'itemTypeid['.$i.']','width'=>80)).'</td>
			<td>'.$this->kclass->chooserPacking(array('name'=>'itemPackingid['.$i.']', 'hasBlank'=>1, 'width'=>100,'selectedid'=>6)).'</td>
			<td><input type="text" name="itemDiscount['.$i.']" value="100" size="3"></td>
			<td><input type="text" name="itemQuantity['.$i.']" value="'.$item['quantity'].'" size="3"  onblur="dc.returnProductStr('.$i.',this.value)"></td>
			<td>'.$this->kclass->chooserUnit(array('name'=>'itemUnitid['.$i.']', 'hasBlank'=>1, 'width'=>80, 'selectedid'=>5)).'</td>
			<td><input type="text" name="itemPrice['.$i.']" value="'.$item['price'].'" size="5"></td>
			<td><input type="text" name="itemOrdering['.$i.']" value="1" size="5"></td>
			<td><input type="checkbox" name="itemKill['.$i.']" value="'.$item['itemid'].'"></td>
			</tr>';
		}
		for($i = $n; $i < ($n+5); $i++){
			$partStr .= '<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').'">
			<td>'.$this->kclass->chooserParts(array('name'=>'itemPartsid['.$i.']', 'hasBlank'=>1, 'width'=>500)).'</td>
			<td><input type="text" name="itemPartsCaption['.$i.']" value="'.$item['caption'].'"  size="65"></td>
			<td>'.$this->kclass->chooserBrand(array('name'=>'itemPartsBrandid['.$i.']', 'hasBlank'=>1, 'width'=>80,'selectedid'=>1)).'</td>
			<td>'.$this->kclass->chooserItemType(array('name'=>'itemPartsTypeid['.$i.']','width'=>80)).'</td>
			<td>'.$this->kclass->chooserPacking(array('name'=>'itemPartsPackingid['.$i.']', 'hasBlank'=>1, 'width'=>100,'selectedid'=>6)).'</td>
			<td><input type="text" name="itemPartsDiscount['.$i.']" value="100" size="3"></td>
			<td><input type="text" name="itemPartsQuantity['.$i.']" value="'.$item['quantity'].'" size="3"></td>
			<td>'.$this->kclass->chooserUnit(array('name'=>'itemPartsUnitid['.$i.']', 'hasBlank'=>1, 'width'=>80, 'selectedid'=>5)).'</td>
			<td><input type="text" name="itemPartsPrice['.$i.']" value="'.$item['price'].'" size="5"></td>
			<td><input type="text" name="itemPartsOrdering['.$i.']" value="1" size="5"></td>
			<td><input type="checkbox" name="itemPartsKill['.$i.']" value="'.$item['itemid'].'"></td>
			</tr>';
		}
		$fee=$this->kclass->formFee(array('module'=>'order','mid'=>$order['orderid']));
		$discount=$this->kclass->formDiscount(array('module'=>'order','mid'=>$order['orderid']));
		if($order['supplierid']==1){
			$supplier=$order['supplierTitle'].'<input type="hidden" name="supplierid" value="1">';
			$supplierNo = $order['supplierNo'].'<input type="hidden" name="supplierNo" value="'.$order['supplierNo'].'">';
		}else{
			$supplier = $this->kclass->chooserSupplierSelect(array('name'=>'supplierid', 'hasBlank'=>1, 'width'=>450, 'selectedid'=>$order['supplierid']));
			$supplierNo = '<input type="text" style="width:330px" name="supplierNo" value="'.$order['supplierNo'].'"> <span class="gray small">供应商使用的订单号</span>';
		}
		if($order['customerid']==1){
			$customer=$order['customerTitle'].'<input type="hidden" name="customerid" value="1">';
			$customerNo = $order['customerNo'].'<input type="hidden" name="customerNo" value="'.$order['customerNo'].'">';
		}else{
			$customer = $this->kclass->chooserCustomer(array('hName'=>'customerid','selectedid'=>$order['customerid'],'value'=>$order['customerTitle'],'name'=>'customer','width'=>450, 'onchange'=>'customerStr'));
			//$customer = $this->kclass->chooserCustomer(array('name'=>'customerid', 'hasBlank'=>1, 'width'=>450,'onchange'=>'customerStr','selectedid'=>$order['customerid']));
			$customerNo = '<input type="text" style="width:340px" name="customerNo" value="'.$order['customerNo'].'"> <span class="gray small">客户使用的订单号</span>';
		}

$body = <<<EOF
<form action="/s.php?module=order&action=insertCopy" name="order" method="post">
<input type="hidden" name="module" value="order">
<input type="hidden" name="action" value="insertCopy">
<input type="hidden" name="orderid" value="{$this->kclass->input['orderid']}">
<input type="hidden" name="rt" value="{$this->kclass->input['rt']}">
<table class="hundred">
<thead>
<tr>
	<th colspan="4">复制订单：<span class="darkred" id="yearStr">{$order['ordertitle']}</span><span class="darkred" id="customerStr">{$customerStr}</span><span class="darkred" id="productStr">{$productStr}</span> <span class="normal"></th>
</tr>
</thead>
<tbody>
<tr class="even">
  <td title="">订单编号：<span class="red bold">*</span></td>
	<td><input type="text" name="orderno" value="{$orderno}"  class="dark middle bold" /><span class="red small"> 此为预估编号</span>　　编号生成方式：<input type="radio" name="noType" value="1" >手动<input type="radio" name="noType" value="0" checked>自动<input type="hidden" name="ordertitle" id="orderTitle" value="{$orderno}"></td>
	<td title="">订单类型：<span class="red bold">*</span></td>
	<td>{$route}</td>
</tr>
<tr class="odd">
	<td title="">币　　种：<span class="red bold">*</span></td>
	<td>{$currency}</td>
	<td title="">业务人员：</td>
	<td class="">{$director}　　　跟单人员：<span class="red">*</span>{$tracker}</td>
</tr>
<tr class="even">
	<td title="Customer">客　　户：<span class="red bold">*</span></td>
	<td>{$customer}</td>
	<td title="Supplier">供 应 商：<span class="red bold">*</span></td>
	<td>{$supplier}</td>
</tr>
<tr class="odd">
	<td title="Customer Order No">订 单 号：</td>
	<td>{$customerNo}</td>
	<td title="Supplier Order No">供应商单号：</td>
	<td>{$supplierNo}</td>
</tr>
<tr class="even">
	<td title="Shipment Method">运送方式：</td>
	<td>{$shipmethod}</td>
	<td title="Port of Loading">装货地点：</td>
	<td>{$loading}</td>
</tr>
<tr class="odd">
	<td title="Shipment Date">交货日期：<span class="red bold">*</span></td>
	<td><input type="text" style="width:150px" id="shipmentDate" name="shipmentDate" value="{$shipmentDate}"> <span class="gray small">除非补单，否则日期不能比建单当日早！</span></td>
	<td title="Port of Discharge">卸货地点：</td>
	<td>{$discharge}</td>
</tr>
<tr class="even">
	<td title="Payment Term">支付条款：</td>
	<td>{$paymentterm}</td>
	<td title="Delivery Term">交货条款：</td>
	<td>{$deliveryterm}</td>
</tr>
<tr class="even">
<td title="Delivery Term">付款状态：</td>
	<td>{$paymentstatus}</td>
	<td title="Payment Date">付款时间：</td>
	<td><input type="text" style="width:150px" id="paymentDate" name="paymentDate" value="{$paymentDate}"><span class="gray small">填到款的实际日期</span>　　<b>支付金额：</b><input type="text" name="paymentamount" value="{$order['paymentamount']}"></td>
</tr>
<tr class="odd">
	<td valign="top" title="Labeling">标签说明：</td>
	<td><textarea name="labeling" style="width:444px;height:100px">{$order['labeling']}</textarea></td>
	<td valign="top" title="Packing">包装说明：</td>
	<td><textarea name="packing" style="width:444px;height:100px">{$order['packing']}</textarea></td>
</tr>
<tr class="even">
	<td valign="top" title="Special">特别说明：</td>
	<td><textarea name="special" style="width:444px;height:100px">{$order['special']}</textarea></td>
	<td valign="top" title="Remark">备　　注：</td>
	<td><textarea name="remark" style="width:444px;height:100px">{$order['remark']}</textarea></td>
</tr>
{$updateReason}
</tbody>
</table>
<table class="hundred">
<thead>
<tr>
	<th colspan="12">订单明细表</th>
</tr>
</thead>
<tbody class="small">
<tr><td>产品选择<span class="red bold">*</span></td><td width="300">说明</td><td width="100">品牌</td><td>购买方式</td><td width="50">包装</td><td width="30">折扣</td><td width="30">数量<span class="red bold">*</span></td><td width="80">单位<span class="red bold">*</span></td><td width="50">单价<span class="red bold">*</span></td><td width="30">排序</td><td  width="20">删</td></tr>
{$itemtr}
<tr><td>配件选择<span class="red bold">*</span></td><td width="300">说明</td><td width="100">品牌</td><td>购买方式</td><td width="50">包装</td><td width="30">折扣</td><td width="30">数量<span class="red bold">*</span></td><td width="80">单位<span class="red bold">*</span></td><td width="50">单价<span class="red bold">*</span></td><td width="30">排序</td><td  width="20">删</td></tr>
{$parts}
<tr>
	<th colspan="12">新建明细</th>
</tr>
<tr><td>产品选择<span class="red bold">*</span></td><td width="300">说明</td><td width="100">品牌</td><td width="300">购买方式</td><td width="50">包装</td><td width="30">折扣</td><td width="30">数量<span class="red bold">*</span></td><td width="80">单位<span class="red bold">*</span></td><td width="50">单价<span class="red bold">*</span></td><td width="30">排序</td><td  width="20">删</td></tr>
{$itemtrs}
<tr><td>配件选择<span class="red bold">*</span></td><td width="300">说明</td><td width="100">品牌</td><td width="300">购买方式</td><td width="50">包装</td><td width="30">折扣</td><td width="30">数量<span class="red bold">*</span></td><td width="80">单位<span class="red bold">*</span></td><td width="50">单价<span class="red bold">*</span></td><td width="30">排序</td><td  width="20">删</td></tr>
{$partStr}
<tr>
	<td colspan="12" class="gray small">注：①如行数不够，请保存后再进入修改页面。②至少有“名称”、“数量”、“单位”、“单价”等四个项目，该条明细才会被保存。③折扣值范围“1-100”%，不填默认为“100”%。</td>
</tr>
</tbody>
</table>
{$fee}
{$discount}
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
		$this->kclass->page['title'] .= ' - '.$order['orderno'].' - 复制订单';
		$this->kclass->page['onload'] .= 'dc.tabhover();$(\'#shipmentDate\').datepicker();dc.tabhover();$(\'#paymentDate\').datepicker();';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - <a href="/s.php?module=order&action=view&orderid='.$order['orderid'].'">'.$order['orderno'].'</a> - 复制订单', 'right' => '<a href="/s.php?module=order">返回列表</a>', 'body'=>$body));

		}
	//
	function view(){
		if(!$this->kclass->input['orderid'] > 0)$this->kclass->boinkIt('/s.php?module=order');
			$order = $this->kclass->DB->queryFirst("
				SELECT `order`.*,
					route.title AS routeTitle, route.caption AS routeCaption,
					customer.title AS customer, customer.address AS customerAddress, customer.linkman AS customerLinkman, customer.position AS customerPosition,
						customer.telephone AS customerTelephone, customer.fax AS customerFax,
					supplier.title AS supplierTitle, supplier.address AS supplierAddress, supplier.linkman AS supplierLinkman, supplier.position AS supplierPosition,
						supplier.telephone AS supplierTelephone, supplier.fax AS supplierFax,
					shipmethod.title AS shipmethodTitle, shipmethod.entitle AS shipmethodEntitle,
					loading.title AS loadingTitle, loading.entitle AS loadingEntitle,
					loadingCountry.country AS loadingCountryTitle, loadingCountry.encountry AS loadingCountryEntitle,
					discharge.title AS dischargeTitle, discharge.entitle AS dischargeEntitle,
					dischargeCountry.country AS dischargeCountryTitle, dischargeCountry.encountry AS dischargeCountryEntitle,
					paymentterm.title AS paymenttermTitle, paymentterm.entitle AS paymenttermEntitle,
					paymentstatus.title AS paymentstatustitle,paymentstatus.entitle AS paymentstatusentitle,
					deliveryterm.title AS deliverytermTitle, deliveryterm.entitle AS deliverytermEntitle,
					currency.title AS currencyTitle, currency.symbol AS currencySymbol, currency.remark AS currencyRemark,
					m.realname AS mname,
					c.realname AS cname,
					t.realname AS tname,
					d.realname AS dname,
					me.realname AS verifyName,
					mem.realname AS approveName
				FROM `order`
				LEFT JOIN orderroute AS route ON (route.routeid=`order`.routeid)
				LEFT JOIN customer ON (customer.customerid=`order`.customerid)
				LEFT JOIN supplier ON (supplier.supplierid=`order`.supplierid)
				LEFT JOIN currency ON (currency.currencyid=`order`.currencyid)
				LEFT JOIN shipmethod ON (shipmethod.shipmethodid=`order`.shipmethodid)
				LEFT JOIN port AS loading ON (loading.portid=`order`.loading)
				LEFT JOIN `region` AS loadingCountry ON (loadingCountry.regionid=loading.regionid)
				LEFT JOIN port AS discharge ON (discharge.portid=`order`.discharge)
				LEFT JOIN `region` AS dischargeCountry ON (dischargeCountry.regionid=discharge.regionid)
				LEFT JOIN `paymentstatus` ON(paymentstatus.paymentstatusid=order.paymentstatus)
				LEFT JOIN paymentterm ON (paymentterm.paymenttermid=`order`.paymenttermid)
				LEFT JOIN deliveryterm ON (deliveryterm.deliverytermid=`order`.deliverytermid)
				LEFT JOIN `member` AS m ON (m.userid=order.modifier)
				LEFT JOIN `member` AS c ON (c.userid=order.creator)
				LEFT JOIN `member` AS d ON (d.userid=order.director)
				LEFT JOIN `member` AS t ON (t.userid=order.tracker)
				LEFT JOIN `member` AS me ON (me.userid=order.verifier)
				LEFT JOIN `member` AS mem ON (mem.userid=order.approver)
				WHERE `order`.killed=0 AND orderid='".$this->kclass->input['orderid']."'
			");
		if(!$order)	$this->kclass->boinkIt('/s.php?module=order&action=find');
		
		$created = date('Y-m-d H:i:s', $order['created']);
		$paymentDate=$this->kclass->iif($order['paymenttime']==0,'-',date('Y-m-d ',$order['paymenttime']));
		$status='';
		if($order['ifVerify']==0){
			$statusid=2;
			$status = '<span class="darkred">未审核　未审批</span>　（<a href="/s.php?module=order&action=verify&orderid='.$order['orderid'].'">审核</a>）';
		}elseif($order['ifVerify']==-1){
			$statusid=2;
			$status = '<span class="red">审核未通过</span>　<span class="darkred">未审批</span>';
		}elseif($order['ifVerify']==1 AND $order['ifApprove']==0){
			$statusid=3;
			$status = '<span class="green">审核通过</span>　<span class="darkred">未审批</span>　（<a href="/s.php?module=order&action=approve&orderid='.$order['orderid'].'">审批</a>）';
		}elseif($order['ifVerify']==1 AND $order['ifApprove']==-1){
			$statusid=3;
			$status = '<span class="green">审核通过</span>　<span class="red">审批未通过</span>';
		}elseif($order['ifVerify']==1 AND $order['ifApprove']==1){
			$statusid=4;
			$status = '<span class="green">审核通过　审批通过</span>';
		}
		$packingStr = '';
			$packing = $this->kclass->DB->queryFirst("SELECT * FROM `packinglist` WHERE module='order' AND mid='".$order['orderid']."'");
		if($packing){
			$packingStr = '<a href="/s.php?module=packinglist&action=updatePacking&orderid='.$order['orderid'].'&packinglistid='.$packing['packinglistid'].'">修改包装箱单</a>';
		}else{
			$packingStr = '<a href="/s.php?module=packinglist&action=addPacking&orderid='.$order['orderid'].'">新建包装箱单</a>';
		}
		// PO 订单
		if($order['type']=='PO'){
			$status.=$this->kclass->iif($order['ifChooser']==1,'　<span class="green">已投产</span>','　<span class="darkred">未投产</span>');
			$outbound=$this->kclass->DB->queryFirst("
				SELECT outboundid
				FROM outbound
				WHERE killed=0 AND ifComplete=2 AND orderid='".$order['orderid']."'
			");
			$status.=$this->kclass->iif($outbound=='','　<span class="darkred">未出库</span>','　<span class="green">已出库</span>');
			if($order['ifChooser']==1)$statusid=5;
			if($outbound!='')$statusid=6;
			$orderid=$order['orderid']; // PO 订单id
			//
			$freight = $this->kclass->DB->queryFirst("SELECT * FROM `freight` WHERE module='order' AND mid='".$order['orderid']."'");
			$freightStr = '';
			if($freight){
				$freightStr = '<a href="/s.php?module=freight&action=update&orderid='.$order['orderid'].'">修改货运信息</a>　<a href="/s.php?module=freight&action=kill&orderid='.$order['orderid'].'" onclick="return confirm(\'确定删除货运信息?\');">删除货运信息</a>';
			}else{
				$freight = array();
				$freightStr = '<a href="/s.php?module=freight&action=add&orderid='.$order['orderid'].'">新建货运信息</a>';
			}
			$freightInfo = $this->kclass->getFreightInfo($freight);
		}else{
			$o=$this->kclass->DB->queryFirst("
				SELECT orderid
				FROM `order`
				WHERE killed=0 AND type='PO' AND parentid='".$order['orderid']."'
			");
			$orderid=$o['orderid'];
		}
		if($order['customerid']>0){
			$customer=$order['customer'];
		}else{
			$customer=$order['customerTitle'];
		}
		if($order['verified']>0){
			$verify='由 '.$order['verifyName'].' 于 '.date('Y-m-d H:i:s', $order['verified']).' 最后审核';
		}
		if($order['approved']>0){
			$approve='，由 '.$order['approveName'].' 于 '.date('Y-m-d H:i:s', $order['approved']).' 最后审批';
		}

		$attach = $this->kclass->getAttachs(array('module'=>'order', 'mid'=>$order['orderid']));
		if($attach!= false){
			$attachs = '<div class="clear">'.$attach.'</div>';
		}
		if($order['type']=='PI'){
			$orderTitleStr = 'PI (销售订单)';
			$flow=$this->kclass->listFlow(array('module'=>'piorder','statusid'=>$statusid));
		}else{
			$orderTitleStr = 'PO (采购订单)';
			$flow=$this->kclass->listFlow(array('module'=>'poorder','statusid'=>$statusid));
		}
		if($order['modifier']>0){
			$modified = '，'.$order['mname'].' 于 '.date('y-m-d H:i:s', $order['modified']).' 最后修改';
		}
		$shipmentDate=date('Y-m-d', $order['shipmentDate']);
		$item = $this->kclass->relatedOrderItem(array('orderid'=>$order['orderid'], 'title'=>'客户'));
		$total = $item['total'] ;
		$revise=$this->kclass->relatedRevise(array('module'=>'order','mid'=>$order['orderid']));
		$related = $this->kclass->relatedOtherOrder(array('parentid'=>$order['orderid']));
		$fee=$this->kclass->relatedFee(array('orderid'=>$order['orderid']));
		$discount=$this->kclass->relatedDiscount(array('module'=>'order','mid'=>$order['orderid'],'total'=>$total));
		$invoice=$this->kclass->relatedInvoice(array('orderid'=>$order['orderid']));
		$packinglist=$this->kclass->relatedPackinglist(array('type'=>'produce','module'=>'order','orderid'=>$orderid));
		$commission=$this->kclass->relatedCommission(array('orderid'=>$order['orderid']));
		$deposit=$this->kclass->relatedDeposit(array('orderid'=>$order['orderid']));
		$guarantee=$this->kclass->relatedGuarantee(array('orderid'=>$order['orderid']));
		$creditnote=$this->kclass->relatedCreditnote(array('orderid'=>$order['orderid']));
		$recipt=$this->kclass->relatedRecipt(array('orderid'=>$order['orderid']));
		$debitnote=$this->kclass->relatedDebitnote(array('module'=>'order','orderid'=>$order['orderid']));
		$payment=$this->kclass->relatedPayment(array('module'=>'order','mid'=>$order['orderid']));
		$claim=$this->kclass->relatedClaim(array('module'=>'order','orderid'=>$order['orderid']));
		$return=$this->kclass->relatedReturn(array('module'=>'order','orderid'=>$order['orderid']));
		$exchange=$this->kclass->relatedExchange(array('module'=>'order','orderid'=>$order['orderid']));
		$service = $this->kclass->relatedService(array('orderid'=>$order['orderid']));
		$requirement = $this->kclass->releatedRequirement(array('orderid'=>$orderid,'panel'=>'off'));//相关需求计划单
		$inbound = $this->kclass->relatedInbound(array('orderid'=>$orderid));//相关出库单
		$outbound = $this->kclass->relatedOutbound(array('orderid'=>$orderid));//相关出库单

		$total+=$fee['total']+$discount['total'];
		$total = number_format($total, 2);
$body = <<<EOF
<div class="title"><span class="small">({$order['routeTitle']} <span class="gray">{$order['routeCaption']}</span>)</span></div>
<dl id="order" class="tabs">
	<dt>{$orderTitleStr}</dt>
	{$related['tabs']}
	<dt title="与此订单相关的发票"{$invoice['off']}>发票{$invoice['count']}</dt>
	<dt title="与此订单相关的需求计划"{$requirement['off']} ajax="requirement">需求计划</dt>
	<dt title="与此订单相关的入库单"{$inbound['off']}>入库单{$inbound['count']}</dt>
	<dt title="与此订单相关的出库单"{$outbound['off']}>出库单{$outbound['count']}</dt>
	<dt title="与此订单相关的包装清单"{$packinglist['off']}>包装清单</dt>
	<!--
	<dt title="销售佣金记录"{$commission['off']}>佣金{$commission['count']}</dt>
	<dt title="与订单相关的定金记录"{$deposit['off']}>定金{$deposit['count']}</dt>
	<dt title="与订单相关的保证金记录"{$guarantee['off']}>保证金{$guarantee['count']}</dt>
	<dt title="与订单相关的应收款项"{$creditnote['off']}>应收{$creditnote['count']}</dt>
	<dt title="与订单相关的已经收到的款项"{$recipt['off']}>已收{$recipt['count']}</dt>
	<dt title="与订单相关的应该付款的款项"{$debitnote['off']}>应付{$debitnote['count']}</dt>
	<dt title="与订单相关的已经付款的款项"{$payment['off']}>已付{$payment['count']}</dt>
	<dt title="与订单相关的订单服务信息"{$service['off']}>客服{$service['count']}</dt>
	<dt title="与订单相关的索赔记录"{$claim['off']}>索赔{$claim['count']}</dt>
	<dt title="订单的退货记录"{$return['off']}>退货{$return['count']}</dt>
	<dt title="订单的换货记录"{$exchange['off']}>换货{$exchange['count']}</dt>
	-->
	<dd>
<div><span class="small gray">当前PI状态：{$status}</span>　　　　　<span class="gray small">{$verify}{$approve}</span><span class="right normal"><a href="/s.php?module=invoice&action=add&orderid={$order['orderid']}&step=2&customerid={$order['customerid']}">新建发票</a>　{$freightStr}　{$packingStr}　|　<a href="/s.php?module=order&action=update&orderid={$order['orderid']}&rt=view">修改</a>　<a href="/s.php?module=order&action=remove&orderid={$order['orderid']}">删除</a>　<a href="/s.php?module=order&action=chooseLanguage&actionid=pi&orderid={$order['orderid']}" target="_blank">打印PI</a>　<a href="/s.php?module=order&action=orderCopy&orderid={$order['orderid']}">复制订单</a></span></div>
<div class="bold darkred big" style="clear:both;">{$order['ordertitle']}{$revise['currencyRevise']}　　　　　<span class="gray small">业务人员：<span class="bold darkred">{$order['dname']}</span></span>　　　　　<span class="gray small">跟单人员：<span class="bold darkred">{$order['tname']}</span></span>　　　　　<span class="gray small">由 {$order['cname']} 于 {$created} 建立{$modified}</span></div>
<div><span class="right">{$flow}</span></div>
<table class="hundred">
<thead>
<tr>
	<th colspan="4">基本信息 (Basic Information)</th>
</tr>
</thead>
<tbody>
<tr>
	<td width="573" colspan="2" class="bold" align="center">客户 (Buyer)</td>
	<td width="573" colspan="2" class="bold gray" align="center">供应商 (Seller)</td>
</tr>
<tr class="odd">
	<td width="70">订单号：</td>
	<td>{$order['customerNo']}</td>
	<td width="70" class="gray">订单号：</td>
	<td class="gray">{$order['supplierNo']}</td>
</tr>
<tr class="odd">
	<td>名　称：</td>
	<td><a href="/s.php?module=customer&action=view&customerid={$order['customerid']}" target="_blank">{$customer}</a></td>
	<td class="gray">名　称：</td>
	<td class="gray">{$order['supplierTitle']}</td>
</tr>
<tr class="odd">
	<td>地　址：</td>
	<td>{$order['customerAddress']}</td>
	<td class="gray">地　址：</td>
	<td class="gray">{$order['supplierAddress']}</td>
</tr>
<tr class="odd">
	<td>联系人：</td>
	<td>{$order['customerLinkman']} <span class="gray">{$order['customerPosition']}</span></td>
	<td class="gray">联系人：</td>
	<td class="gray">{$order['supplierLinkman']} <span class="gray">{$order['supplierPosition']}</span></td>
</tr>
<tr class="odd">
	<td>电　话：</td>
	<td>{$order['customerTelephone']}</td>
	<td class="gray">电　话：</td>
	<td class="gray">{$order['supplierTelephone']}</td>
</tr>
<tr class="odd">
	<td>传　真：</td>
	<td>{$order['customerFax']}</td>
	<td class="gray">传　真：</td>
	<td class="gray">{$order['supplierFax']}</td>
</tr>
</tbody>
</table>
<table class="hundred">
<thead>
<tr>
	<th colspan="4"><span class="right"><span class="normal" title="Currency">币种：</span><span class="darkred">{$order['currencyTitle']} {$order['currencySymbol']}</span> <span class="gray">{$order['currencyRemark']}</span></span>一般信息 (General Information)</th>
</tr>
</thead>
<tbody>
<tr>
	<td width="70" title="Shipment Method">运输方式：</td>
	<td width="494" class="middle">{$order['shipmethodTitle']} <span class="gray">{$order['shipmethodEntitle']}</span></td>
	<td width="70" title="Payment Term">支付条款：</td>
	<td class="middle">{$order['paymenttermTitle']} <span class="gray">{$order['paymenttermEntitle']}</span></td>
</tr>
<tr>
	<td title="Port of Loading">装货地点：</td>
	<td class="middle">{$order['loadingCountryTitle']} {$order['loadingTitle']} <span class="gray">{$order['loadingCountryTitle']} {$order['loadingTitle']}</span></td>
	<td title="Delivery Term">交货条款：</td>
	<td class="middle">{$order['deliverytermTitle']} <span class="gray">{$order['deliverytermEntitle']}</span></td>
</tr>
<tr>
	<td title="Port of Discharge">卸货地点：</td>
	<td class="middle">{$order['dischargeCountryTitle']} {$order['dischargeTitle']} <span class="gray">{$order['dischargeCountryEntitle']} {$order['dischargeEntitle']}</span></td>
	<td title="Shipment Date">交货日期：</td>
	<td class="middle"><span class="bold darkred">{$shipmentDate}</span></td>
</tr>
<tr>
<td title="paymentstatus">付款状态</td>
<td>{$order['paymentstatustitle']}</td>
<td title="paymentDate">付款金额</td>
<td> {$order['paymentamount']}　　　付款时间：{$paymentDate}</td>
</tr>
</tbody>
</table>
<table class="hundred">
<thead>
<tr>
	<th colspan="4">标准信息 (Critertion Information)</th>
</tr>
</thead>
<tbody>
<tr>
	<td width="70" valign="top" title="Labeling">标签说明：</td>
	<td width="494" class="middle">{$order['labeling']}</td>
	<td width="70" valign="top" title="Packing">包装说明：</td>
	<td class="middle">{$order['packing']}</td>
</tr>
<tr>
	<td valign="top" title="Special">特别说明：</td>
	<td class="middle">{$order['special']}</td>
	<td valign="top" title="Remark">备　　注：</td>
	<td class="middle">{$order['remark']}</td>
</tr>
</tbody>
</table>
<table class="hundred">
<thead>
<tr>
	<th colspan="12"><span class="right"><span class="normal" title="Currency">币种：</span><span class="darkred">{$order['currencyTitle']} {$order['currencySymbol']}</span> <span class="gray">{$order['currencyRemark']}</span></span>订单明细 (Item Information)</th>
</tr>
</thead>
<tbody>
{$item['tr']}
</tbody>
</table>
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
<table class="hundred">
<thead>
<tr>
	<th colspan="4">折扣信息 (Discount Information)</th>
</tr>
</thead>
<tbody>
{$discount['tr']}
</tbody>
</table>
<table class="hundred">
<tbody>
<tr><td align="right">总计(Total Amount)：<span class="middle bold darkred">{$order['currencyTitle']} {$order['currencySymbol']}{$total}</span></td></tr>
</tbody>
</table>
{$freightInfo}
{$revise['panel']}
	</dd>
	{$related['panel']}
	<dd>{$invoice['panel']}</dd>
	<dd id="requirement" load="0"></dd>
	<dd>{$inbound['panel']}</dd>
	<dd>{$outbound['panel']}</dd>
	<dd>{$packinglist['panel']}</dd>
	<dd>{$commission['panel']}</dd>
	<dd>{$deposit['panel']}</dd>
	<dd>{$guarantee['panel']}</dd>
	<dd>{$creditnote['panel']}</dd>
	<dd>{$receipt['panel']}</dd>
	<dd>{$debitnote['panel']}</dd>
	<dd>{$payment['panel']}</dd>
	<dd>{$claim['panel']}</dd>
	<dd>{$service['panel']}</dd>
	<dd>{$receipt['panel']}</dd>
	<dd>{$exchange['panel']}</dd>
</dl>
{$attachs}
EOF;
		$json="{'id':'order','orderid':".$orderid."}";
		$this->kclass->page['onload'] .= 'dc.tabs('.$json.');dc.tabhover();dc.show()';
		$this->kclass->page['title'] .= ' - '.$order['orderno'];
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl.' - 订单信息', 'right' => '<span class="gray small">操作：</span><a href="s.php?module=order&action=add">新建</a>', 'body'=>$body));
	}
	//
	function insert(){
		//print_r($this->kclass->input);

		if($this->kclass->input['requestMethod'] != 'post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			$route = $this->kclass->DB->queryFirst("SELECT * FROM `orderroute` WHERE routeid='".$this->kclass->input['routeid']."'");
			if(!$route){
				$e .= '<li>请选择 订单路径。</li>';
			}
			if($this->kclass->input['m']=='old'){
				$orderno=$this->kclass->input['orderno'];
			}else{
				if($this->kclass->input['noType']==0){
					$orderno=$this->kclass->id(array('order'=>TIMENOW));
				}elseif($this->kclass->input['noType']==1){
					$orderno=$this->kclass->input['orderno'];
				}
			}
			$order=$this->kclass->DB->queryFirst("SELECT orderid FROM `order` WHERE orderno='".$orderno."' LIMIT 0,1");
			if($order['orderid']){
				$e='<li>编号<b>'.$orderno.'</b> 在订单中 已存在</li>';
			}

				$this->kclass->input['paymentamount']=trim($this->kclass->input['paymentamount']);
			if($this->kclass->input['paymentamount']!=''){
			if(!is_numeric($this->kclass->input['paymentamount'])){
				$e.='<li>支付金额必须为数字，请输入数字</li>';
			}
			}else{
				$this->kclass->input['paymentamount']=0;
			}
			$this->kclass->input['paymentamount']=trim($this->kclass->input['paymentamount']);
			if($this->kclass->input['paymentamount']!=''){
			if(!is_numeric($this->kclass->input['paymentamount'])){
				$e.='<li>支付金额必须为数字，请输入数字</li>';
			}
			}else{
				$this->kclass->input['paymentamount']=0;
			}

			if($this->kclass->input['tracker'] <= 0){
				$e .= '<li>请选择跟单人员。</li>';
			}
			if($this->kclass->input['currencyid'] <= 0){
				$e .= '<li>请选择订单中的 币种。</li>';
			}
			if(!($this->kclass->input['customerid']>0 OR $this->kclass->input['customer']!='')){
				$e .= '<li>请选择订单中的 客户。</li>';
			}
			/*else{
				$verify=$this->kclass->DB->queryFirst("SELECT ifverify FROM `customer` WHERE `customer`.customerid='".$this->kclass->input['customerid']."'");
			if($verify['ifverify']!=1){
				$e.='<li>客户未通过审核或未审核，请审核客户以后再试。</li>';
			}
			}*/
			if($this->kclass->input['supplierid'] <= 0){
				$e .= '<li>请选择订单中的 供应商。</li>';
			}
			if($this->kclass->input['shipmentDate'] == ''){
				$e .= '<li>请填写订单的 交货日期。</li>';
			}
			$j = count($this->kclass->input['itemProductid']);
			$hasItem=$hasPartItem=0;
			for($i = 1; $i < $j+1; $i++){
				if($this->kclass->input['itemProductid'][$i]>0 AND $this->kclass->input['itemQuantity'][$i]!='' AND $this->kclass->input['itemUnitid'][$i]>0 AND $this->kclass->input['itemPrice'][$i]>=0){
					$hasItem=1;
				}
			}
			$m=count($this->kclass->input['itemPartsid']);
			for($n=1;$n<$m+1;$n++){
				if($this->kclass->input['itemPartsid'][$n]>0 AND $this->kclass->input['itemPartsQuantity'][$n]!='' AND $this->kclass->input['itemPartsUnitid'][$n]>0 AND $this->kclass->input['itemPartsPrice'][$n]>=0){
					$hasPartItem=1;
				}
			}
			if($hasItem==0 AND $hasPartItem==0){
				$e .= '<li>需要填写至少有一条订单明细，才能建立订单。</li>';
			}
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '新建订单',
				'text' => '您在新建订单的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$sd = explode('-', $this->kclass->input['shipmentDate']);
		$shipmentDate = mktime(0,0,0,$sd[1],$sd[2],$sd[0]);
        $pd=explode('-',$this->kclass->input['paymentDate']);
		$paymentDate=mktime(0,0,0,$pd[1],$pd[2],$pd[0]);
       
		$typelist = explode(',', $route['typelist']);
		$csid = explode(',', $route['defaultcsid']);
		$dv='';
		$parentid=0;
		for($i = 0; $i < $route['copies']; $i++){
			$customer=$this->kclass->DB->queryFirst("
				SELECT c.abbr AS customerAbbr,c.ifverify AS cverify,r.regionid,r.countryid
				FROM customer AS c
				LEFT JOIN region AS r ON (r.regionid=c.regionid)
				WHERE c.customerid='".$this->kclass->input['customerid']."'
			");
			
			if($customer['countryid']>0){
				$region=$this->kclass->DB->queryFirst("SELECT abbr2 FROM region WHERE regionid='".$customer['countryid']."' LIMIT 0,1");
				$countryAbbr=$region['abbr2'];
			}else{
				$region=$this->kclass->DB->queryFirst("SELECT abbr2 FROM region WHERE regionid='".$customer['regionid']."' LIMIT 0,1");
				$countryAbbr=$region['abbr2'];
			}
			$dv = explode(':', $csid[$i]);
			if($dv[0]=='c'){
				$customerid=$this->kclass->input['customerid'];
				$customerNo=$this->kclass->input['customerNo'];
			}else{
				$customerid=$dv[0];
				$orderno = 'PO'.substr($orderno,2);
				$ordertitle = 'PO'.substr($ordertitle,2);
				$customerNo=$orderno;
			}
			if($dv[1]=='s'){
				$supplierid=$this->kclass->input['supplierid'];
				$this->kclass->input['supplierNo'] = 'PO'.substr($this->kclass->input['supplierNo'],2);
				$supplierNo=$this->kclass->input['supplierNo'];
			}else{
				$supplierid=$dv[1];
				$supplierNo=$orderno;
			}
			
			$this->kclass->DB->query("
				INSERT INTO `order` (`parentid`, `paymenttime`,`paymentamount`,`paymentstatus`,`routeid`,`noType`, `type`, `orderno`, `customerid`, `supplierid`,`customerTitle`, `customerNo`, `supplierNo`, `loading`, `discharge`, `currencyid`, `deliverytermid`, `paymenttermid`, `shipmethodid`, `shipmentDate`, `packing`, `labeling`, `special`, `remark`,killed,`status`, `modified`,`director`, `tracker`,`creator`, `created`)
					VALUES ('".$parentid."','".$paymentDate."','".$this->kclass->input['paymentamount']."','".$this->kclass->input['paymentstatusid']."','".$route['routeid']."','".$this->kclass->input['noType']."', '".$typelist[$i]."', '".$orderno."', '".$customerid."', '".$supplierid."','".$this->kclass->input['customer']."', '".$customerNo."',  '".$supplierNo."', '".$this->kclass->input['loading']."', '".$this->kclass->input['discharge']."', '".$this->kclass->input['currencyid']."', '".$this->kclass->input['deliverytermid']."', '".$this->kclass->input['paymenttermid']."', '".$this->kclass->input['shipmethodid']."', '".$shipmentDate."', '".$this->kclass->input['packing']."', '".$this->kclass->input['labeling']."', '".$this->kclass->input['special']."', '".$this->kclass->input['remark']."', '0','0', '".TIMENOW."', '".$this->kclass->input['director']."','".$this->kclass->input['tracker']."','".$this->kclass->user['userid']."', '".TIMENOW."')
			");
			$orderid = $this->kclass->DB->insertID();
			if($i==0){
				$parentid = $orderid;
			}
			$pitem=$partItem='';
			// order item  产品明细
			for($k = 1; $k < (count($this->kclass->input['itemProductid'])+1); $k++){
				$this->kclass->input['itemQuantity'][$k]=intVal($this->kclass->input['itemQuantity'][$k]);
				$this->kclass->input['itemPrice'][$k]=floatVal($this->kclass->input['itemPrice'][$k]);
				$this->kclass->input['itemDiscount'][$k]=intVal($this->kclass->input['itemDiscount'][$k]);
				if($this->kclass->input['itemDiscount'][$k]<0 OR $this->kclass->input['itemDiscount'][$k]>100){
					$this->kclass->input['itemDiscount'][$k]=100;
				}
				$amount = $this->kclass->input['itemQuantity'][$k] * $this->kclass->input['itemPrice'][$k];
				if($this->kclass->input['itemDiscount'][$k]<100){
					$amount = $amount * $this->kclass->input['itemDiscount'][$k] / 100;
				}
				$amount = number_format($amount, 2, '.', '');
				if($this->kclass->input['itemProductid'][$k]>0 AND $this->kclass->input['itemQuantity'][$k]!='' AND $this->kclass->input['itemUnitid'][$k]>0 AND $this->kclass->input['itemPrice'][$k]>=0){
					$productItem=$this->kclass->DB->queryFirst("
						SELECT pi.productid,pi.attributevalue,
							p.title
						FROM productitem AS pi
						LEFT JOIN product AS p ON (p.productid=pi.productid)
						WHERE pi.productitemid='".$this->kclass->input['itemProductid'][$k]."'
					");
					$pitem.='-'.$this->kclass->input['itemQuantity'][$k].$productItem['title'];
					$this->kclass->DB->query("
						INSERT INTO `item` (`module`,`mid`,`productid`,`productitemid`,`productAttributes`,`caption`,`brandid`,`packingid`,`typeid`,`quantity`,`discount`,`unitid`,`price`,`amount`,`modified`,`creator`,`created`)
						VALUES ('order','".$orderid."','".$productItem['productid']."','".$this->kclass->input['itemProductid'][$k]."','".$productItem['attributevalue']."','".$this->kclass->input['itemCaption'][$k]."','".$this->kclass->input['itemBrandid'][$k]."','".$this->kclass->input['itemPackingid'][$k]."','".$this->kclass->input['itemTypeid'][$k]."','".$this->kclass->input['itemQuantity'][$k]."','".$this->kclass->input['itemDiscount'][$k]."','".$this->kclass->input['itemUnitid'][$k]."','".$this->kclass->iif($typelist[$i]=='PI', $this->kclass->input['itemPrice'][$k], 0)."','".$this->kclass->iif($typelist[$i]=='PI', $amount, 0)."','".TIMENOW."','".$this->kclass->user['userid']."','".TIMENOW."')
					");
				}
			}

			//配件明细
			for($k = 1; $k < (count($this->kclass->input['itemPartsid'])+1); $k++){
				$this->kclass->input['itemPartsQuantity'][$k]=intVal($this->kclass->input['itemPartsQuantity'][$k]);
				$this->kclass->input['itemPartsPrice'][$k]=floatVal($this->kclass->input['itemPartsPrice'][$k]);
				$this->kclass->input['itemPartsDiscount'][$k]=intVal($this->kclass->input['itemPartsDiscount'][$k]);
				if($this->kclass->input['itemPartsDiscount'][$k]<0 OR $this->kclass->input['itemPartsDiscount'][$k]>100){
					$this->kclass->input['itemPartsDiscount'][$k]=100;
				}

				$amount = $this->kclass->input['itemPartsQuantity'][$k] * $this->kclass->input['itemPartsPrice'][$k];
				if($this->kclass->input['itemPartsDiscount'][$k]<100){
					$amount = $amount * $this->kclass->input['itemPartsDiscount'][$k] / 100;
				}
				$amount = number_format($amount, 2, '.', '');
				if($this->kclass->input['itemPartsid'][$k]>0 AND $this->kclass->input['itemPartsQuantity'][$k]!='' AND $this->kclass->input['itemPartsUnitid'][$k]>0 AND $this->kclass->input['itemPartsPrice'][$k]>=0){
					$part=$this->kclass->DB->queryFirst("
						SELECT p.materialid,
							m.materialno
						FROM product p
						LEFT JOIN material AS m ON (m.materialid=p.materialid)
						WHERE productid='".$this->kclass->input['itemPartsid'][$k]."'
					");
					$partItem='-PARTS';
					$this->kclass->DB->query("
						INSERT INTO `item` (`module`,`mid`,`materialid`,`productid`,`caption`,`brandid`,`packingid`,`typeid`,`quantity`,`discount`,`unitid`,`price`,`amount`,`modified`,`creator`,`created`)
						VALUES ('order','".$orderid."','".$part['materialid']."','".$this->kclass->input['itemPartsid'][$k]."','".$this->kclass->input['itemPartsCaption'][$k]."','".$this->kclass->input['itemPartsBrandid'][$k]."','".$this->kclass->input['itemPartsPackingid'][$k]."','".$this->kclass->input['itemPartsTypeid'][$k]."','".$this->kclass->input['itemPartsQuantity'][$k]."','".$this->kclass->input['itemPartsDiscount'][$k]."','".$this->kclass->input['itemPartsUnitid'][$k]."','".$this->kclass->iif($typelist[$i]=='PI', $this->kclass->input['itemPartsPrice'][$k], 0)."','".$this->kclass->iif($typelist[$i]=='PI', $amount, 0)."','".TIMENOW."','".$this->kclass->user['userid']."','".TIMENOW."')
					");
				}
			}
			$orderTitle=$orderno.'-'.$countryAbbr.'-'.$customer['customerAbbr'].$pitem.$partItem;
			$this->kclass->DB->query("UPDATE `order` SET ordertitle='".$orderTitle."' WHERE orderid='".$orderid."'");
			$this->kclass->updateFee(array('module'=>'order','mid'=>$orderid));
			$this->kclass->updateDiscount(array('module'=>'order','mid'=>$orderid));
		}
		$this->kclass->updateAttachs(array('module'=>'order', 'mid'=>$parentid));

		$this->kclass->messager(array(
			'title' => '新建订单',
			'text' => '订单 <b>'.$orderno.'</b> 已新建成功!',
			'url' => '/s.php?module=order&action=view&orderid='.$parentid,
			'sec' => 2
		));
	}

	//order add
	function add(){
		$orderno=$this->kclass->id(array('order'=>TIMENOW));
		$route = $this->kclass->chooserOrderRoute(array('name'=>'routeid', 'selectedid'=>1));
		$supplier = $this->kclass->chooserSupplierSelect(array('name'=>'supplierid', 'hasBlank'=>1, 'width'=>300, 'selectedid'=>2));
		$director = $this->kclass->chooserTracker(array('name'=>'director','condition'=>'positionid =7','selectedid'=>$this->kclass->user['userid'],'width'=>200));
		$tracker = $this->kclass->chooserTracker(array('name'=>'tracker','condition'=>'positionid=7','width'=>200,'selectedid'=>28));
		$fee=$this->kclass->formFee(array('module'=>'order'));
		$discount=$this->kclass->formDiscount(array('module'=>'order'));
		$upload = $this->kclass-> upload(array('title'=>'相关附件：'));
		$loading = $this->kclass->chooserPort(array('name'=>'loading', 'hasBlank'=>1, 'width'=>450, 'selectedid'=>9));
		$paymentstatus=$this->kclass->chooserPaymentstatus(array('name'=>'paymentstatusid','hasBlank'=>1,'width'=>300,'selectedid'=>1));
	if($this->kclass->input['quotingid']!=""){ 
      if($this->kclass->input['customerid']<=0){
			$this->kclass->boinkIt('/s.php?module=order&action=add');
		}
     $ifverify=$this->kclass->DB->queryFirst("SELECT ifverify FROM `quoting` WHERE `quoting`.quotingid='".$this->kclass->input['quotingid']."'");
	 if($ifverify['ifverify']!=1){
		 $e.='<li>发往客户的报价单未审核或审核未通过，请审核或修改后再试。</li>';
	 }
	 if(isset($e)){
		 $this->kclass->messager(array(
			 'title'=>'报价单导入订单',
			 'text'=>'您在导入订单的过程中出现以下错误:<ul>'.$e.'</ul>',
			'url'=>'javascript:history.back()',
			'sec'=>3
			 ));
	 }
	  $item=$this->kclass->DB->queryFirst("
					SELECT  p.entitle AS pentitle,p.title AS ptitle, c.title AS ctitle,qt.title AS qttitle,u.username AS uname,
					q.*,
					de.title AS  detitle,de.entitle AS deentitle
					FROM `quoting` AS q
					LEFT JOIN `port` AS p ON ( p.portid=q.portid)
					LEFT JOIN `currency` AS c ON (c.currencyid=q.currencyid)
					LEFT JOIN `quotingtype` AS qt ON (qt.typeid=q.typeid)
					LEFT JOIN `user`			AS	u	ON  (u.userid=q.creator)
					LEFT JOIN `deliveryterm` AS de ON (de.deliverytermid=q.deliverytermid)
					WHERE q.killed=0 AND q.quotingid='".$this->kclass->input['quotingid']."'
				");
	           
				$port=$item['ptitle'].'('.$item['pentitle'].')';
				$deliveryterm=$item['detitle'].'('.$item['deentitle'].')';
				$standrad=$item['standrad'];
				$material=$item['material'];
				$technics=$item['technics'];
				$remark=$item['remark'];
				$type=$item['qttitle'];
				if($item['dateline']!=''){
					$date=date('Y-m-d ',$item['dateline']);
				}else{
					$date=date('Y-m-d');
				}
				$customers=$this->kclass->DB->queryFirst("SELECT customerid, title,cntitle, linkman, position FROM `customer` WHERE killed=0 AND customerid='".$this->kclass->input['customerid']."'");
				if($customers['cntitle']!=''){
						$customer='<input type="hidden" name="customerid" value="'.$this->kclass->input['customerid'].'">'.$customers['title'].'('.$customers['cntitle'].')';
					}else{
						$customer='<input type="hidden" name="customerid" value="'.$this->kclass->input['customerid'].'">'.$customers['title'];
					}
					//$customer = $this->kclass->chooserCustomer(array('hName'=>'customerid','name'=>'customer','width'=>450, 'onchange'=>'customerStr','value'=>$customers['customerid']));
				$currency = $this->kclass->chooserCurrency(array('name'=>'currencyid', 'hasBlank'=>1, 'width'=>100, 'selectedid'=>$item['currencyid']));
		$packing = $this->kclass->chooserPacking(array('name'=>'packingid', 'hasBlank'=>1, 'width'=>140, 'selectedid'=>1));
		$shipmethod = $this->kclass->chooserShipmethod(array('name'=>'shipmethodid', 'hasBlank'=>1, 'width'=>300, 'selectedid'=>1));
		$paymentterm = $this->kclass->chooserPaymentterm(array('name'=>'paymenttermid', 'hasBlank'=>1, 'width'=>300, 'selectedid'=>1));
		$deliveryterm = $this->kclass->chooserDeliveryterm(array('name'=>'deliverytermid', 'hasBlank'=>1, 'width'=>300, 'selectedid'=>$item['deliverytermid']));
		$discharge = $this->kclass->chooserPort(array('name'=>'discharge', 'hasBlank'=>1, 'width'=>300, 'selectedid'=>$item['portid']));
		$quotingtype=$this->kclass->chooserQuotingtype(array('name'=>'typeid','hasBlank'=>1,'width'=>100,'selectedid'=>$item['typeid']));
                $items=$this->kclass->DB->query("
		SELECT `item`.*,
					product.title AS product,
					material.standard,
					packing.title AS packing,
					unit.title			AS unit
		FROM `item`
		LEFT JOIN product ON (product.productid=`item`.productid)
		LEFT JOIN material ON (material.materialid=`item`.materialid)
		LEFT JOIN packing ON (packing.packingid=`item`.packingid)
		LEFT JOIN unit		ON (unit.unitid=`item`.unitid)
		WHERE `item`	.killed=0 AND `item`.module='quoting' AND
		`item`.mid='".$this->kclass->input['quotingid']."'
		ORDER BY itemid ASC
	 ");
	 $itemtr='';
	 if($itemnum=$this->kclass->DB->numRows()){
				$k=1;
				$n=1;
				while($item = $this->kclass->DB->fetchArray($items)){
					if($item['productitemid']>0){
						$product = $this->kclass->DB->queryFirst("
							SELECT `product`.title,`product`.entitle
							FROM  `productitem`
							LEFT JOIN `product` ON (`productitem`.productid = `product`.productid)
							WHERE productitemid='".$item['productitemid']."'
						");
						//$seletStr = '<input type="hidden" id="tditemProductid'.$k.'input" value="'.$product['entitle'].'" />';
						$itemtr .= '<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').'" id="producttr'.$i.'">
						<td >'.$k.'</td>
						<td><span id="tditemProductid'.$k.'">'.$seletStr.'</span><input type="hidden" name="itemId['.$k.']" value="'.$item['itemid'].'">'.$this->kclass->chooserProductItem(array('name'=>'itemProductid['.$k.']','selectName'=>'itemProductid'.$k, 'selectedid'=>$item['productitemid'], 'hasBlank'=>1, 'width'=>500)).'</td>
						<td><input type="text" name="itemCaption['.$k.']" value="'.$item['caption'].'" size="65"></td>
						<td>'.$this->kclass->chooserItemType(array('name'=>'itemTypeid['.$k.']','width'=>80)).'</td>
						<td>'.$this->kclass->chooserBrand(array('name'=>'itemBrandid['.$k.']', 'hasBlank'=>1, 'width'=>80, 'selectedid'=>$item['brandid'])).'</td>
						<td>'.$this->kclass->chooserPacking(array('name'=>'itemPackingid['.$k.']', 'hasBlank'=>1, 'width'=>100, 'selectedid'=>$item['packingid'])).'</td>
						<td><input type="text" name="itemDiscount['.$k.']" value="'.$item['discount'].'" size="3"></td>
						<td><input type="text" name="itemQuantity['.$k.']" value="'.$item['quantity'].'" size="3" onblur="dc.returnProductStr('.$k.',this.value)"></td>
						<td>'.$this->kclass->chooserUnit(array('name'=>'itemUnitid['.$k.']', 'hasBlank'=>1, 'width'=>80, 'selectedid'=>$item['unitid'])).'</td>
						<td><input type="text" name="itemPrice['.$k.']" value="'.$item['price'].'" size="5"></td>
						</tr>';
						$k++;
						
					}else{
						$partsStr .= '<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').'">
						<td>'.$n.'</td>
						<td><input type="hidden" name="itemPartsId['.$n.']" value="'.$item['itemid'].'">'.$this->kclass->chooserParts(array('name'=>'itemPartsid['.$n.']', 'selectedid'=>$item['productid'], 'hasBlank'=>1, 'width'=>500)).'</td>
						<td><input type="text" name="itemPartsCaption['.$n.']" value="'.$item['caption'].'" size="65"></td>
						<td>'.$this->kclass->chooserItemType(array('name'=>'itemPartsTypeid['.$n.']','width'=>80)).'</td>
						<td>'.$this->kclass->chooserBrand(array('name'=>'itemPartsBrandid['.$n.']', 'hasBlank'=>1, 'width'=>80, 'selectedid'=>$item['brandid'])).'</td>
						<td>'.$this->kclass->chooserPacking(array('name'=>'itemPartsPackingid['.$n.']', 'hasBlank'=>1, 'width'=>100, 'selectedid'=>$item['packingid'])).'</td>
						<td><input type="text" name="itemPartsDiscount['.$n.']" value="'.$item['discount'].'" size="3"></td>
						<td><input type="text" name="itemPartsQuantity['.$n.']" value="'.$item['quantity'].'" size="3"></td>
						<td>'.$this->kclass->chooserUnit(array('name'=>'itemPartsUnitid['.$n.']', 'hasBlank'=>1, 'width'=>80, 'selectedid'=>$item['unitid'])).'</td>
						<td><input type="text" name="itemPartsPrice['.$n.']" value="'.$item['price'].'" size="5"></td>
						</tr>';
						$n++;
					}
				}
				$itemtr.='<tr class="odd bold gray normal" id="orderProduct" nohover><td colspan=10><span class="hand" onclick="dc.addLine(\'orderProduct\',\'orderProduct\')" style="border:2px solid gray;background:#FFE8EB">增加一行产品明细</span></td></tr>';
			}
			
			for($i = $k; $i < ($k+5); $i++){
				$itemtrs .= '<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').'" id="producttr'.$i.'">
				<td>'.$i.'</td>
				<td><span id="tditemProductid'.$i.'"></span>'.$this->kclass->chooserProductItem(array('name'=>'itemProductid['.$i.']','selectName'=>'itemProductid'.$i, 'hasBlank'=>1, 'width'=>500)).'</td>
				<td><input type="text" name="itemCaption['.$i.']" value="'.$item['caption'].'"  size="65"></td>
				<td>'.$this->kclass->chooserItemType(array('name'=>'itemTypeid['.$i.']','width'=>80)).'</td>
				<td>'.$this->kclass->chooserBrand(array('name'=>'itemBrandid['.$i.']', 'hasBlank'=>1, 'width'=>80,'selectedid'=>1)).'</td>
				<td>'.$this->kclass->chooserPacking(array('name'=>'itemPackingid['.$i.']', 'hasBlank'=>1, 'width'=>100,'selectedid'=>6)).'</td>
				<td><input type="text" name="itemDiscount['.$i.']" value="100" size="3"></td>
				<td><input type="text" name="itemQuantity['.$i.']" value="'.$item['quantity'].'" size="3"  onblur="dc.returnProductStr('.$i.',this.value)"></td>
				<td>'.$this->kclass->chooserUnit(array('name'=>'itemUnitid['.$i.']', 'hasBlank'=>1, 'width'=>80, 'selectedid'=>5)).'</td>
				<td><input type="text" name="itemPrice['.$i.']" value="'.$item['price'].'" size="5"></td>
				</tr>';
			}
			$itemtrs.='<tr class="odd bold gray normal" id="orderProduct" nohover><td colspan=10><span class="hand" onclick="dc.addLine(\'orderProduct\',\'orderProduct\')" style="border:2px solid gray;background:#FFE8EB">增加一行产品明细</span></td></tr>';
			for($i = $n; $i < ($n+5); $i++){
				$partStr .= '<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').'">
				<td>'.$i.'</td>
				<td>'.$this->kclass->chooserParts(array('name'=>'itemPartsid['.$i.']', 'hasBlank'=>1, 'width'=>500)).'</td>
				<td><input type="text" name="itemPartsCaption['.$i.']" value="'.$item['caption'].'"  size="65"></td>
				<td>'.$this->kclass->chooserItemType(array('name'=>'itemPartsTypeid['.$i.']','width'=>80)).'</td>
				<td>'.$this->kclass->chooserBrand(array('name'=>'itemPartsBrandid['.$i.']', 'hasBlank'=>1, 'width'=>80,'selectedid'=>1)).'</td>
				<td>'.$this->kclass->chooserPacking(array('name'=>'itemPartsPackingid['.$i.']', 'hasBlank'=>1, 'width'=>100,'selectedid'=>6)).'</td>
				<td><input type="text" name="itemPartsDiscount['.$i.']" value="100" size="3"></td>
				<td><input type="text" name="itemPartsQuantity['.$i.']" value="'.$item['quantity'].'" size="3"></td>
				<td>'.$this->kclass->chooserUnit(array('name'=>'itemPartsUnitid['.$i.']', 'hasBlank'=>1, 'width'=>80, 'selectedid'=>5)).'</td>
				<td><input type="text" name="itemPartsPrice['.$i.']" value="'.$item['price'].'" size="5"></td>
				</tr>';
	 }
	$partStr.='<tr class="odd bold gray normal" id="orderPart" nohover><td colspan=10><span class="hand" onclick="dc.addLine(\'orderPart\',\'orderPart\')" style="border:2px solid gray;background:#FFE8EB">增加一行配件明细</span></td></tr>';
	}else{
		if($order['shipmentDate']!=''){
			$date=$order['shipmentDate'];
		}else{
			$date=date('Y-m-d');
		}
		$customer = $this->kclass->chooserCustomer(array('hName'=>'customerid','name'=>'customer','width'=>300, 'onchange'=>'customerStr'));
		
		$loading = $this->kclass->chooserPort(array('name'=>'loading', 'hasBlank'=>1, 'width'=>300, 'selectedid'=>9));
		$discharge = $this->kclass->chooserPort(array('name'=>'discharge', 'hasBlank'=>1, 'width'=>300, 'selectedid'=>$this->kclass->input['discharge']));
		$paymentterm = $this->kclass->chooserPaymentterm(array('name'=>'paymenttermid', 'hasBlank'=>1, 'width'=>300, 'selectedid'=>$this->kclass->input['paymenttermid']));
		$deliveryterm = $this->kclass->chooserDeliveryterm(array('name'=>'deliverytermid', 'hasBlank'=>1, 'width'=>300, 'selectedid'=>$this->kclass->input['deliverytermid']));
		$shipmethod = $this->kclass->chooserShipmethod(array('name'=>'shipmethodid', 'hasBlank'=>1, 'width'=>300, 'selectedid'=>1));
		$currency = $this->kclass->chooserCurrency(array('name'=>'currencyid', 'hasBlank'=>1, 'width'=>300, 'selectedid'=>1));
		$flow=$this->kclass->listFlow(array('module'=>'poorder','statusid'=>1));

		
		for($i = 1; $i < 5; $i++){
			$itemtr .= '<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').'" id="producttr'.$i.'">
			<td>'.$i.'</td>
			<td><span id="tditemProductid'.$i.'"></span>'.$this->kclass->chooserProductItem(array('name'=>'itemProductid['.$i.']','selectName'=>'itemProductid'.$i, 'hasBlank'=>1, 'width'=>500)).'</td>
			<td><input type="text" name="itemCaption['.$i.']" value="" size="65"></td>
			<td>'.$this->kclass->chooserItemType(array('name'=>'itemTypeid['.$i.']','width'=>80)).'</td>
			<td>'.$this->kclass->chooserBrand(array('name'=>'itemBrandid['.$i.']', 'hasBlank'=>1, 'width'=>80, 'selectedid'=>1)).'</td>
			<td>'.$this->kclass->chooserPacking(array('name'=>'itemPackingid['.$i.']', 'hasBlank'=>1, 'width'=>100, 'selectedid'=>6)).'</td>
			<td><input type="text" name="itemDiscount['.$i.']" value="100" size="3"></td>
			<td><input type="text" name="itemQuantity['.$i.']" value="'.$item['quantity'].'" size="3" onblur="dc.returnProductStr('.$i.',this.value)"></td>
			<td>'.$this->kclass->chooserUnit(array('name'=>'itemUnitid['.$i.']', 'hasBlank'=>1, 'width'=>80, 'selectedid'=>5)).'</td>
			<td><input type="text" name="itemPrice['.$i.']" value="'.$item['price'].'" size="5"></td>
			</tr>';
		}
		$itemtr.='<tr class="odd bold gray normal" id="orderProduct" nohover><td colspan=10><span class="hand" onclick="dc.addLine(\'orderProduct\',\'orderProduct\')" style="border:2px solid gray;background:#FFE8EB">增加一行产品明细</span></td></tr>';
		for($i = 1; $i <5; $i++){
			$partsStr .= '<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').'" id="parttr'.$i.'">
			<td>'.$i.'</td>
			<td>'.$this->kclass->chooserParts(array('name'=>'itemPartsid['.$i.']', 'hasBlank'=>1, 'width'=>500)).'</td>
			<td><input type="text" name="itemPartsCaption['.$i.']" value="'.$item['caption'].'" size="65"></td>
			<td>'.$this->kclass->chooserItemType(array('name'=>'itemPartsTypeid['.$i.']','width'=>80)).'</td>
			<td>'.$this->kclass->chooserBrand(array('name'=>'itemPartsBrandid['.$i.']', 'hasBlank'=>1, 'width'=>80, 'selectedid'=>1)).'</td>
			<td>'.$this->kclass->chooserPacking(array('name'=>'itemPartsPackingid['.$i.']', 'hasBlank'=>1, 'width'=>100, 'selectedid'=>6)).'</td>
			<td><input type="text" name="itemPartsDiscount['.$i.']" value="100" size="3"></td>
			<td><input type="text" name="itemPartsQuantity['.$i.']" value="'.$item['quantity'].'" size="3" ></td>
			<td>'.$this->kclass->chooserUnit(array('name'=>'itemPartsUnitid['.$i.']', 'hasBlank'=>1, 'width'=>80, 'selectedid'=>5)).'</td>
			<td><input type="text" name="itemPartsPrice['.$i.']" value="'.$item['price'].'" size="5"></td>
			</tr>';
		}
		$partsStr.='<tr class="odd bold gray normal" id="orderPart" nohover><td colspan=10><span class="hand" onclick="dc.addLine(\'orderPart\',\'orderPart\')" style="border:2px solid gray;background:#FFE8EB">增加一行配件明细</span></td></tr>';
		
	}
$body .= <<<EOF
<form action="/s.php?module=order&action=insert" name="order" method="post">
<input type="hidden" name="module" value="order">
<input type="hidden" name="action" value="insert">
<div><span class="right">{$flow}</span></div>
<table class="hundred">
<thead>
<tr>
	<th colspan="4">新建订单：<span class="darkred" id="yearStr">{$orderno}</span><span class="darkred" id="customerStr"></span><span class="darkred" id="productStr"></span></th>
</tr>
</thead>
<tbody>
<tr class="even">
	<td title="">订单编号：<span class="red bold">*</span></td>
	<td><input type="text" name="orderno" value="{$orderno}"  class="dark middle bold" /><span class="red small"> 此为预估编号</span>　　编号生成方式：<input type="radio" name="noType" value="1" >手动<input type="radio" name="noType" value="0" checked>自动<input type="hidden" name="ordertitle" id="orderTitle" value="{$orderno}"></td>
	<td title="">订单类型：<span class="red bold">*</span></td>
	<td>{$route}</td>
</tr>
<tr class="odd">
	<td>币　　种：<span class="red bold">*</span></td>
	<td>{$currency}</td>
	<td>业务人员：<span class="red bold">*</span></td>
	<td>{$director}　跟单人员：<span class="red bold">*</span>　{$tracker}</td>
</tr>
<tr class="even">
	<td title="Customer">客　　户：<span class="red bold">*</span></td>
	<td>{$customer}</td>
	<td title="Supplier">供 应 商：<span class="red bold">*</span></td>
	<td>{$supplier}</td>
</tr>
<tr class="odd">
	<td title="Customer Order No">订 单 号：</td>
	<td><input type="text" style="width:300px" name="customerNo" value=""> <span class="gray small">客户使用的订单号</span></td>
	<td title="Supplier Order No">供应商编号：</td>
	<td><input type="text" style="width:300px" name="supplierNo" value="{$PIStr}"> <span class="gray small">供应商使用的订单号</span></td>
</tr>
<tr class="even">
	<td title="Shipment Method">运送方式：</td>
	<td>{$shipmethod}</td>
	<td title="Port or Loading">装货地点：</td>
	<td>{$loading}</td>
</tr>
<tr class="odd">
	<td title="Shipment Date">交货日期：<span class="red bold">*</span></td>
	<td><input type="text" style="width:150px" id="shipmentDate" name="shipmentDate" value="$date"> <span class="gray small">除非补单，否则日期不能比建单当日早！</span></td>
	<td title="Port of Discharge">卸货地点：</td>
	<td>{$discharge}</td>
</tr>
<tr class="even">
	<td title="Payment Term">支付条款：</td>
	<td>{$paymentterm}</td>
	<td title="Delivery Term">交货条款：</td>
	<td>{$deliveryterm}</td>
</tr>
<tr class="even">
<td title="Delivery Term">付款状态：</td>
	<td>{$paymentstatus}</td>
	<td title="Payment Date">付款时间：</td>
	<td><input type="text" style="width:150px" id="paymentDate" name="paymentDate" value="$date"><span class="gray small">填到款的实际日期</span>　　<b>支付金额：</b><input type="text" name="paymentamount"></td>
</tr>
<tr class="odd">
	<td valign="top" title="Labeling">标签说明：</td>
	<td><textarea name="labeling" style="width:444px;height:100px">{$order['labeling']}</textarea></td>
	<td valign="top" title="Packing">包装说明：</td>
	<td><textarea name="packing" style="width:444px;height:100px">{$order['packing']}</textarea></td>
</tr>
<tr class="even">
	<td valign="top" title="Special">特别说明：</td>
	<td><textarea name="special" style="width:444px;height:100px">{$order['special']}</textarea></td>
	<td valign="top" title="Remark">备　　注：</td>
	<td><textarea name="remark" style="width:444px;height:100px">{$order['remark']}</textarea></td>
</tr>
</tbody>
</table>
<table class="hundred">
<thead>
<tr>
	<th colspan="11">订单明细表</th>
</tr>
</thead>
<tbody class="small">
<tr><td width="15">ID</td><td width="390">产品选择<span class="red bold">*</span></td><td  width="300">备注说明</td><td width="70">购买方式</td><td  width="100">品牌</td><td  width="50">包装</td><td  width="45">折扣%</td><td width="30">数量<span class="red bold">*</span></td><td width="80">单位<span class="red bold">*</span></td><td width="50">单价<span class="red bold">*</span></td></tr>
{$itemtr}
<tr><td width="15">ID</td><td width="390">配件选择<span class="red bold">*</span></td><td  width="300">备注说明</td><td width="70">购买方式</td><td  width="100">品牌</td><td  width="50">包装</td><td  width="45">折扣%</td><td width="30">数量<span class="red bold">*</span></td><td width="80">单位<span class="red bold">*</span></td><td width="50">单价<span class="red bold">*</span></td></tr>
{$partsStr}

EOF;
if($this->kclass->input['quotingid']!=''){
	$body.=<<<EOF
<tr><th colspan="10">新建明细</th></tr>
<tr><td width="15">ID</td><td width="390">产品选择<span class="red bold">*</span></td><td  width="300">备注说明</td><td width="70">购买方式</td><td  width="100">品牌</td><td  width="50">包装</td><td  width="45">折扣%</td><td width="30">数量<span class="red bold">*</span></td><td width="80">单位<span class="red bold">*</span></td><td width="50">单价<span class="red bold">*</span></td></tr>
	{$itemtrs}
<tr><td width="15">ID</td><td width="390">配件选择<span class="red bold">*</span></td><td  width="300">备注说明</td><td width="70">购买方式</td><td  width="100">品牌</td><td  width="50">包装</td><td  width="45">折扣%</td><td width="30">数量<span class="red bold">*</span></td><td width="80">单位<span class="red bold">*</span></td><td width="50">单价<span class="red bold">*</span></td></tr>
	{$partStr}
EOF;
}
$body.=<<<EOF
</tbody>
</table>
<tr>
	<td colspan="11" class="gray small">注：①如行数不够，请保存后再进入修改页面。②至少有“名称”、“数量”、“单位”、“单价”等四个项目，该条明细才会被保存。③折扣值范围“1-100”，不填默认为“100”。④</td>
</tr>
</tbody>
</table>
{$fee}
{$discount}
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
		$this->kclass->page['title'] .= ' - 新建订单';
		//$this->kclass->page['onload'] .= 'dc.tabhover();$(\'#shipmentDate\').datepicker();';
		$this->kclass->page['onload'].='dc.tabhover();$(\'#shipmentDate\').datepicker();var dates=dc.tabhover();$(\'#paymentDate\').datepicker();';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 新建订单', 'right' => '<a href="/s.php?module=order">返回列表</a>', 'body'=>$body));
	}
	//
	function addOld(){
		$dateStr = date("Ymd");
		$year=((int)substr($dateStr,0,4));//取得年份
		$month=((int)substr($dateStr,4,2));//取得月份
		$day=((int)substr($dateStr,6,2));//取得几号
		$dateStamp = mktime(0,0,0,$month,$day,$year);

		$orderNum = $this->kclass->DB->queryFirst("SELECT COUNT(orderid) AS num FROM `order` WHERE `order`.type='PI' AND `order`.created > ".$dateStamp);
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
		$PIStr = 'PI'.$yearStr;
		$director = $this->kclass->chooserTracker(array('name'=>'director','condition'=>'positionid=7','selectedid'=>$this->kclass->user['userid'],'width'=>200));
		$tracker = $this->kclass->chooserTracker(array('name'=>'tracker','condition'=>'positionid=7','width'=>200,'selectedid'=>28));
		$customer = $this->kclass->chooserCustomer(array('name'=>'customerid', 'hasBlank'=>1, 'width'=>450, 'onchange'=>'customerStr','selectedid'=>$this->kclass->input['customerid']));
		$supplier = $this->kclass->chooserSupplierSelect(array('name'=>'supplierid', 'hasBlank'=>1, 'width'=>450, 'selectedid'=>2));
		$loading = $this->kclass->chooserPort(array('name'=>'loading', 'hasBlank'=>1, 'width'=>450, 'selectedid'=>9));
		$discharge = $this->kclass->chooserPort(array('name'=>'discharge', 'hasBlank'=>1, 'width'=>450, 'selectedid'=>$this->kclass->input['discharge']));
		$paymentterm = $this->kclass->chooserPaymentterm(array('name'=>'paymenttermid', 'hasBlank'=>1, 'width'=>450, 'selectedid'=>$this->kclass->input['paymenttermid']));
		$deliveryterm = $this->kclass->chooserDeliveryterm(array('name'=>'deliverytermid', 'hasBlank'=>1, 'width'=>450, 'selectedid'=>$this->kclass->input['deliverytermid']));
		$shipmethod = $this->kclass->chooserShipmethod(array('name'=>'shipmethodid', 'hasBlank'=>1, 'width'=>450, 'selectedid'=>1));
		$currency = $this->kclass->chooserCurrency(array('name'=>'currencyid', 'hasBlank'=>1, 'width'=>450, 'selectedid'=>1));
		$orderno=$this->kclass->id(array('order'=>686));
		$upload = $this->kclass->upload(array('title'=>'相关附件：'));
		for($i = 1; $i < 7; $i++){
			$itemtr .= '<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').'">
			<td><span id="tditemProductid'.$i.'"></span>'.$this->kclass->chooserProductItem(array('name'=>'itemProductid['.$i.']','selectName'=>'itemProductid'.$i, 'hasBlank'=>1, 'width'=>500)).'</td>
			<td><input type="text" name="itemCaption['.$i.']" value="'.$item['caption'].'" size="65"></td>
			<td>'.$this->kclass->chooserBrand(array('name'=>'itemBrandid['.$i.']', 'hasBlank'=>1, 'width'=>80, 'selectedid'=>1)).'</td>
			<td>'.$this->kclass->chooserPacking(array('name'=>'itemPackingid['.$i.']', 'hasBlank'=>1, 'width'=>100, 'selectedid'=>6)).'</td>
			<td><input type="text" name="itemDiscount['.$i.']" value="100" size="3"></td>
			<td><input type="text" name="itemQuantity['.$i.']" value="'.$item['quantity'].'" size="3" onblur="dc.returnProductStr('.$i.',this.value)"></td>
			<td>'.$this->kclass->chooserUnit(array('name'=>'itemUnitid['.$i.']', 'hasBlank'=>1, 'width'=>80, 'selectedid'=>5)).'</td>
			<td><input type="text" name="itemPrice['.$i.']" value="'.$item['price'].'" size="5"></td>
			</tr>';
		}

		for($i = 1; $i < 7; $i++){
			$partsStr .= '<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').'">
			<td>'.$this->kclass->chooserParts(array('name'=>'itemPartsid['.$i.']', 'hasBlank'=>1, 'width'=>500)).'</td>
			<td><input type="text" name="itemPartsCaption['.$i.']" value="'.$item['caption'].'" size="65"></td>
			<td>'.$this->kclass->chooserBrand(array('name'=>'itemPartsBrandid['.$i.']', 'hasBlank'=>1, 'width'=>80, 'selectedid'=>1)).'</td>
			<td>'.$this->kclass->chooserPacking(array('name'=>'itemPartsPackingid['.$i.']', 'hasBlank'=>1, 'width'=>100, 'selectedid'=>6)).'</td>
			<td><input type="text" name="itemPartsDiscount['.$i.']" value="100" size="3"></td>
			<td><input type="text" name="itemPartsQuantity['.$i.']" value="'.$item['quantity'].'" size="3" ></td>
			<td>'.$this->kclass->chooserUnit(array('name'=>'itemPartsUnitid['.$i.']', 'hasBlank'=>1, 'width'=>80, 'selectedid'=>5)).'</td>
			<td><input type="text" name="itemPartsPrice['.$i.']" value="'.$item['price'].'" size="5"></td>
			</tr>';
		}
		$fee=$this->kclass->formFee(array('module'=>'order'));
		$discount=$this->kclass->formDiscount(array('module'=>'order'));
		$route = $this->kclass->chooserOrderRoute(array('name'=>'routeid', 'selectedid'=>1));
$body = <<<EOF
<form action="/s.php?module=order&action=insert" name="order" method="post">
<input type="hidden" name="module" value="order">
<input type="hidden" name="action" value="insert">
<input type="hidden" name="m" value="old">
<table class="hundred">
<thead>
<tr>
	<th colspan="4">新建订单：<span class="darkred" id="yearStr">{$PIStr}</span><span class="darkred" id="customerStr"></span><span class="darkred" id="productStr"></span></th>
</tr>
</thead>
<tbody>
<tr class="even">
	<td title="">订单编号：<span class="red bold">*</span></td>
	<td><input type="text" name="orderno" id="orderNo" value="{$PIStr}" style="width:443px;"><input type="hidden" name="ordertitle" id="orderTitle" value="{$PIStr}"><br/></td>
	<td title="">订单类型：<span class="red bold">*</span></td>
	<td>{$route}</td>
</tr>
<tr class="odd">
	<td>币　　种：<span class="red bold">*</span></td>
	<td>{$currency}</td>
	<td>业务人员：<span class="red bold">*</span></td>
	<td>{$director}　跟单人员：<span class="red bold">*</span>　{$tracker}</td>
</tr>
<tr class="even">
	<td title="Customer">客　　户：<span class="red bold">*</span></td>
	<td>{$customer}</td>
	<td title="Supplier">供 应 商：<span class="red bold">*</span></td>
	<td>{$supplier}</td>
</tr>
<tr class="odd">
	<td title="Customer Order No">订 单 号：</td>
	<td><input type="text" style="width:340px" name="customerNo" value=""> <span class="gray small">客户使用的订单号</span></td>
	<td title="Supplier Order No">供应商单号：</td>
	<td><input type="text" style="width:330px" name="supplierNo" value="{$PIStr}"> <span class="gray small">供应商使用的订单号</span></td>
</tr>
<tr class="even">
	<td title="Shipment Method">运送方式：</td>
	<td>{$shipmethod}</td>
	<td title="Port or Loading">装货地点：</td>
	<td>{$loading}</td>
</tr>
<tr class="odd">
	<td title="Shipment Date">交货日期：<span class="red bold">*</span></td>
	<td><input type="text" style="width:150px" id="shipmentDate" name="shipmentDate" value="{$order['shipmentDate']}"> <span class="gray small">除非补单，否则日期不能比建单当日早！</span></td>
	<td title="Port of Discharge">卸货地点：</td>
	<td>{$discharge}</td>
</tr>
<tr class="even">
	<td title="Payment Term">支付条款：</td>
	<td>{$paymentterm}</td>
	<td title="Delivery Term">交货条款：</td>
	<td>{$deliveryterm}</td>
</tr>
<tr class="odd">
	<td valign="top" title="Labeling">标签说明：</td>
	<td><textarea name="labeling" style="width:444px;height:100px">{$order['labeling']}</textarea></td>
	<td valign="top" title="Packing">包装说明：</td>
	<td><textarea name="packing" style="width:444px;height:100px">{$order['packing']}</textarea></td>
</tr>
<tr class="even">
	<td valign="top" title="Special">特别说明：</td>
	<td><textarea name="special" style="width:444px;height:100px">{$order['special']}</textarea></td>
	<td valign="top" title="Remark">备　　注：</td>
	<td><textarea name="remark" style="width:444px;height:100px">{$order['remark']}</textarea></td>
</tr>
</tbody>
</table>
<table class="hundred">
<thead>
<tr>
	<th colspan="11">订单明细表</th>
</tr>
</thead>
<tbody class="small">
<tr><td width="390">产品选择<span class="red bold">*</span></td><td  width="300">备注说明</td><td  width="100">品牌</td><td  width="50">包装</td><td  width="45">折扣%</td><td width="30">数量<span class="red bold">*</span></td><td width="80">单位<span class="red bold">*</span></td><td width="50">单价<span class="red bold">*</span></td></tr>
{$itemtr}
<tr><td width="390">配件选择<span class="red bold">*</span></td><td  width="300">备注说明</td><td  width="100">品牌</td><td  width="50">包装</td><td  width="45">折扣%</td><td width="30">数量<span class="red bold">*</span></td><td width="80">单位<span class="red bold">*</span></td><td width="50">单价<span class="red bold">*</span></td></tr>
{$partsStr}
<tr>
	<td colspan="11" class="gray small">注：①如行数不够，请保存后再进入修改页面。②至少有“名称”、“数量”、“单位”、“单价”等四个项目，该条明细才会被保存。③折扣值范围“1-100”，不填默认为“100”。④</td>
</tr>
</tbody>
</table>
{$fee}
{$discount}
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
		$this->kclass->page['title'] .= ' - 新建订单';
		$this->kclass->page['onload'] .= 'dc.tabhover();$(\'#shipmentDate\').datepicker();';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 新建订单', 'right' => '<a href="/s.php?module=order">返回列表</a>', 'body'=>$body));
	}
	//
	function doupdate(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			$order = $this->kclass->DB->queryFirst("SELECT orderid, orderno, customerid, supplierid,iflocked,ifVerify,ifApprove FROM `order` WHERE orderid='".$this->kclass->input['orderid']."'");
			if(!$order){
				$e .= '<li>您要编辑的订单并不存在，请返回列表刷新后再操作。</li>';
			}else{
				$orderRelatePO = $this->kclass->DB->queryFirst("SELECT orderid FROM `order` WHERE orderno='PO".substr($order['orderno'], 2)." '");
			}
			if($order['iflocked']==1){
				$e.='<li>您要修改的订单已经被锁定，请通知生产部解锁</li>';
			}
			if($this->kclass->input['currencyid'] <= 0){
				$e .= '<li>请选择订单中的 币种。</li>';
			}
			if($this->kclass->input['shipmentDate'] == ''){
				$e .= '<li>请填写订单的 交货日期。</li>';
			}
			$this->kclass->input['paymentamount']=floatVal($this->kclass->input['paymentamount']);
			if(!is_numeric($this->kclass->input['paymentamount'])){
				$e.='<li>支付金额必须为数字，请输入数字</li>';
			}
			$isOrder=$this->kclass->DB->queryFirst("SELECT 1 FROM `order` WHERE orderid<>{$order['orderid']} AND orderno='{$this->kclass->input['orderno']}'");
			if($isOrder['1']){
				$e.='<li>修改后的 订单编号 已存在。</li>';
			}
			$j = count($this->kclass->input['itemProductid']);
			$hasItem =$hasPartItem= 0;
			for($i = 1; $i < $j+1; $i++){
				if($this->kclass->input['itemKill'][$i]=='' AND $this->kclass->input['itemProductid'][$i]>0 AND $this->kclass->input['itemQuantity'][$i]!='' AND $this->kclass->input['itemUnitid'][$i]>0 AND $this->kclass->input['itemPrice'][$i]>=0){
					$hasItem=1;
				}
			}
			$m=count($this->kclass->input['itemPartsId']);
			for($n=1;$n<$m+1;$n++){
				if($this->kclass->input['itemPartsKill'][$i]=='' AND $this->kclass->input['itemPartsid'][$n]>0 AND $this->kclass->input['itemPartsQuantity'][$n]!='' AND $this->kclass->input['itemPartsUnitid'][$n]>0 AND $this->kclass->input['itemPartsPrice'][$n]>=0){
					$hasPartItem=1;
				}
			}
			if($hasItem==0 AND $hasPartItem==0){
				$e .= '<li>需要填写至少有一条订单明细，才能建立订单。</li>';
			}
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '修改订单',
				'text' => '您在修改订单的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		//获取历史版本信息
		$orderHtml = '';
		if($order['ifVerify']!=0 OR $order['ifApprove']!=0){
			$revise = $this->_getOrderHtml();
			$this->kclass->insertReviseInfo(array('module'=>'order', 'mid'=>$order['orderid'],'revise'=> $revise,'remark'=>$this->kclass->input['reviseRemark']));
			$this->kclass->DB->query("UPDATE `order` SET ifVerify=0,verified=0,verifier=0,ifApprove=0,approved=0,approver=0,ifChooser=0 WHERE orderid='".$order['orderid']."'");
		}
		$pitem=$partItem='';
		// 产品明细
		for($i = 1; $i < count($this->kclass->input['itemProductid'])+1; $i++){
			$this->kclass->input['itemQuantity'][$i]=intVal($this->kclass->input['itemQuantity'][$i]);
			$this->kclass->input['itemPrice'][$i]=floatVal($this->kclass->input['itemPrice'][$i]);
			$this->kclass->input['itemDiscount'][$i]=intVal($this->kclass->input['itemDiscount'][$i]);
			if($this->kclass->input['itemDiscount'][$i]<0 OR $this->kclass->input['itemDiscount'][$i]>100){
				$this->kclass->input['itemDiscount'][$i]=100;
			}
			$amount = $this->kclass->input['itemQuantity'][$i] * $this->kclass->input['itemPrice'][$i];
			if($this->kclass->input['itemDiscount'][$i]<100){
				$amount = $amount * $this->kclass->input['itemDiscount'][$i] / 100;
			}
			$amount = number_format($amount, 2, '.', '');

			$productItem=$this->kclass->DB->queryFirst("
				SELECT pi.productid,pi.attributevalue,
					p.title
				FROM productitem AS pi
				LEFT JOIN product AS p ON (p.productid=pi.productid)
				WHERE productitemid='".$this->kclass->input['itemProductid'][$i]."'
			");

			if($this->kclass->input['itemId'][$i]>0){// 删除或者修改
				$res = $this->kclass->DB->queryFirst("select productid from `item` WHERE itemid=".$this->kclass->input['itemId'][$i]." ");
				$re = $this->kclass->DB->queryFirst("select itemid from `item` WHERE module='order' AND mid=".$orderRelatePO['orderid']." AND productid=".$res['productid']." ");
				if($this->kclass->input['itemKill'][$i]>0){
					$this->kclass->DB->query("UPDATE `item` SET killer=".$this->kclass->user['userid'].",killed=".TIMENOW."  WHERE itemid IN (".$this->kclass->input['itemId'][$i].",".$re['itemid'].")");
				}else{
					$pitem.='-'.$this->kclass->input['itemQuantity'][$i].$productItem['title'];
					$this->kclass->DB->query("
						UPDATE `item` SET
							`productid`='".$productItem['productid']."',
							`productitemid`='".$this->kclass->input['itemProductid'][$i]."',
							`productAttributes`='".$productItem['attributevalue']."',
							`caption`='".$this->kclass->input['itemCaption'][$i]."',
							`brandid`='".$this->kclass->input['itemBrandid'][$i]."',
							`packingid`='".$this->kclass->input['itemPackingid'][$i]."',
							`discount`='".$this->kclass->input['itemDiscount'][$i]."',
							`quantity`='".$this->kclass->input['itemQuantity'][$i]."',
							`unitid`='".$this->kclass->input['itemUnitid'][$i]."',
							`price`='".$this->kclass->input['itemPrice'][$i]."',
							`ordering`='".$this->kclass->input['itemOrdering'][$i]."',
							`amount`='".$amount."',
							`modified`='".TIMENOW."',
							`modifier`='".$this->kclass->user['userid']."'
						WHERE itemid IN (".$this->kclass->input['itemId'][$i].",".$re['itemid'].")
					");
					$quantity+=$this->kclass->input['itemQuantity'][$i];
				}
			}elseif($this->kclass->input['itemProductid'][$i]>0 AND $this->kclass->input['itemQuantity'][$i]!='' AND $this->kclass->input['itemUnitid'][$i]>0 AND $this->kclass->input['itemPrice'][$i]>=0){// 新增
				$pitem.='-'.$this->kclass->input['itemQuantity'][$i].$productItem['title'];
				$this->kclass->DB->query("
					INSERT INTO `item` (`module`,`mid`,`productid`,`productitemid`,`productAttributes`,`title`,`model`,`caption`,`articleNo`,`brandid`,`packingid`,`quantity`,`discount`,`unitid`,`price`,`ordering`,`amount`,`modified`,`creator`,`created`)
					VALUES
					('order','".$order['orderid']."','".$productItem['productid']."','".$this->kclass->input['itemProductid'][$i]."','".$productItem['attributevalue']."','".$this->kclass->input['itemTitle'][$i]."', '".$this->kclass->input['itemModel'][$i]."', '".$this->kclass->input['itemCaption'][$i]."',  '".$this->kclass->input['itemArticleNo'][$i]."', '".$this->kclass->input['itemBrandid'][$i]."', '".$this->kclass->input['itemPackingid'][$i]."', '".$this->kclass->input['itemQuantity'][$i]."', '".$this->kclass->input['itemDiscount'][$i]."', '".$this->kclass->input['itemUnitid'][$i]."', '".$this->kclass->input['itemPrice'][$i]."', '".$this->kclass->input['itemOrdering'][$i]."', '".$amount."', '".TIMENOW."', '".$this->kclass->user['userid']."', '".TIMENOW."')
					,('order','".$orderRelatePO['orderid']."','".$productItem['productid']."','".$this->kclass->input['itemProductid'][$i]."','".$productItem['attributevalue']."','".$this->kclass->input['itemTitle'][$i]."', '".$this->kclass->input['itemModel'][$i]."', '".$this->kclass->input['itemCaption'][$i]."',  '".$this->kclass->input['itemArticleNo'][$i]."', '".$this->kclass->input['itemBrandid'][$i]."', '".$this->kclass->input['itemPackingid'][$i]."', '".$this->kclass->input['itemQuantity'][$i]."', '".$this->kclass->input['itemDiscount'][$i]."', '".$this->kclass->input['itemUnitid'][$i]."', '0', '".$this->kclass->input['itemOrdering'][$i]."', '0', '".TIMENOW."', '".$this->kclass->user['userid']."', '".TIMENOW."')
				");
				$quantity+=$this->kclass->input['itemQuantity'][$i];
			}
		}

		$partsCount=count($this->kclass->input['itemPartsid']);
		//配件明细
		for($i = 1; $i<$partsCount+1; $i++){
			$this->kclass->input['itemPartsQuantity'][$i]=intVal($this->kclass->input['itemPartsQuantity'][$i]);
			$this->kclass->input['itemPartsPrice'][$i]=floatVal($this->kclass->input['itemPartsPrice'][$i]);
			$this->kclass->input['itemPartsDiscount'][$i]=intVal($this->kclass->input['itemPartsDiscount'][$i]);
			if($this->kclass->input['itemPartsDiscount'][$i]<0 OR $this->kclass->input['itemPartsDiscount'][$i]>100){
				$this->kclass->input['itemPartsDiscount'][$i]=100;
			}
			$amount = $this->kclass->input['itemPartsQuantity'][$i] * $this->kclass->input['itemPartsPrice'][$i];
			if($this->kclass->input['itemPartsDiscount'][$i]<100){
				$amount = $amount * $this->kclass->input['itemPartsDiscount'][$i] / 100;
			}
			$amount = number_format($amount, 2, '.', '');
			$part=$this->kclass->DB->queryFirst("SELECT materialid FROM product WHERE productid='".$this->kclass->input['itemPartsid'][$i]."'");
			if($this->kclass->input['itemPartsId'][$i]>0){
				$res = $this->kclass->DB->queryFirst("select productid from `item` WHERE itemid=".$this->kclass->input['itemPartsId'][$i]." ");
				$re = $this->kclass->DB->queryFirst("select itemid from `item` WHERE module='order' AND mid=".$orderRelatePO['orderid']." AND productid=".$res['productid']." ");				
				if($this->kclass->input['itemPartsKill'][$i]>0){
					$this->kclass->DB->query("UPDATE `item` SET killer=".$this->kclass->user['userid'].",killed=".TIMENOW."  WHERE itemid IN (".$this->kclass->input['itemPartsId'][$i].",".$re['itemid'].")");
				}else{
					$partItem='-PARTS';
					$this->kclass->DB->query("
						UPDATE `item` SET
							`materialid`='".$part['materialid']."',
							`productid`='".$this->kclass->input['itemPartsid'][$i]."',
							`caption`='".$this->kclass->input['itemPartsCaption'][$i]."',
							`brandid`='".$this->kclass->input['itemPartsBrandid'][$i]."',
							`packingid`='".$this->kclass->input['itemPartsPackingid'][$i]."',
							`discount`='".$this->kclass->input['itemPartsDiscount'][$i]."',
							`quantity`='".$this->kclass->input['itemPartsQuantity'][$i]."',
							`unitid`='".$this->kclass->input['itemPartsUnitid'][$i]."',
							`price`='".$this->kclass->input['itemPartsPrice'][$i]."',
							`ordering`='".$this->kclass->input['itemPartsOrdering'][$i]."',
							`amount`='".$amount."',
							`modified`='".TIMENOW."',
							`modifier`='".$this->kclass->user['userid']."'
						WHERE itemid IN (".$this->kclass->input['itemPartsId'][$i].",".$re['itemid'].")
					");
					$quantity+=$this->kclass->input['itemPartsQuantity'][$i];
				}
			}elseif($this->kclass->input['itemPartsid'][$i]>0 AND $this->kclass->input['itemPartsQuantity'][$i]!='' AND $this->kclass->input['itemPartsUnitid'][$i]>0 AND $this->kclass->input['itemPartsPrice'][$i]>=0){
				$partItem='-PARTS';
				$this->kclass->DB->query("
					INSERT INTO `item` 
						(`module`,`mid`,`materialid`,`productid`,`title`,`model`,`caption`,`articleNo`,`brandid`,`packingid`,`quantity`,`discount`,`unitid`,`price`,`ordering`,`amount`,`modified`,`creator`,`created`)
					VALUES 
						('order','".$order['orderid']."','".$part['materialid']."','".$this->kclass->input['itemPartsid'][$i]."','".$this->kclass->input['itemTitle'][$i]."','".$this->kclass->input['itemModel'][$i]."','".$this->kclass->input['itemPartsCaption'][$i]."','".$this->kclass->input['itemArticleNo'][$i]."','".$this->kclass->input['itemPartsBrandid'][$i]."','".$this->kclass->input['itemPartsPackingid'][$i]."','".$this->kclass->input['itemPartsQuantity'][$i]."','".$this->kclass->input['itemPartsDiscount'][$i]."','".$this->kclass->input['itemPartsUnitid'][$i]."','".$this->kclass->input['itemPartsPrice'][$i]."','".$this->kclass->input['itemPartsOrdering'][$i]."','".$amount."','".TIMENOW."','".$this->kclass->user['userid']."', '".TIMENOW."')
						,('order','".$orderRelatePO['orderid']."','".$part['materialid']."','".$this->kclass->input['itemPartsid'][$i]."','".$this->kclass->input['itemTitle'][$i]."','".$this->kclass->input['itemModel'][$i]."','".$this->kclass->input['itemPartsCaption'][$i]."','".$this->kclass->input['itemArticleNo'][$i]."','".$this->kclass->input['itemPartsBrandid'][$i]."','".$this->kclass->input['itemPartsPackingid'][$i]."','".$this->kclass->input['itemPartsQuantity'][$i]."','".$this->kclass->input['itemPartsDiscount'][$i]."','".$this->kclass->input['itemPartsUnitid'][$i]."','0','".$this->kclass->input['itemPartsOrdering'][$i]."','0','".TIMENOW."','".$this->kclass->user['userid']."', '".TIMENOW."')

				");
				$quantity+=$this->kclass->input['itemPartsQuantity'][$i];
			}
		}

		$sd = explode('-', $this->kclass->input['shipmentDate']);
		$shipmentDate = mktime(0,0,0,$sd[1],$sd[2],$sd[0]);
		if($this->kclass->input['paymentDate']==''){
			$paymentDate=0;
		}else{
            $pd=explode('-',$this->kclass->input['paymentDate']);
		$paymentDate=mktime(0,0,0,$pd[1],$pd[2],$pd[0]);
		}
		$customer=$this->kclass->DB->queryFirst("
			SELECT c.abbr AS customerAbbr,r.regionid,r.countryid
			FROM customer AS c
			LEFT JOIN region AS r ON (r.regionid=c.regionid)
			WHERE c.customerid='".$this->kclass->input['customerid']."'
		");
		if($customer['countryid']>0){
			$region=$this->kclass->DB->queryFirst("SELECT abbr2 FROM region WHERE regionid='".$customer['countryid']."' LIMIT 0,1");
			$countryAbbr=$region['abbr2'];
		}else{
			$region=$this->kclass->DB->queryFirst("SELECT abbr2 FROM region WHERE regionid='".$customer['regionid']."' LIMIT 0,1");
			$countryAbbr=$region['abbr2'];
		}
		if($order['customerid']==1){
			$customer = " ";
		}
		if($order['supplierid']==1){
			$supplier = "`supplierid` = '".$this->kclass->input['supplierid']."', `supplierNo` = '".$this->kclass->input['supplierNo']."', ";
		}
		
		$orderTitle=$this->kclass->input['orderno'].'-'.$countryAbbr.'-'.$customer['customerAbbr'].$pitem.$partItem;
		$orderTitlePO="PO".substr($orderTitle, 2);
		$this->kclass->DB->query("
			UPDATE `order` SET ".$supplier."
				`orderno`='".$this->kclass->input['orderno']."',
				`customerid` = '".$this->kclass->input['customerid']."',
				`customerNo` = '".$this->kclass->input['customerNo']."',
				`supplierNo` = '".$this->kclass->input['orderno']."',
				`director`= '".$this->kclass->input['director']."',
				`tracker`= '".$this->kclass->input['tracker']."',
				`ordertitle` = '".$orderTitle."',
				`paymenttime`='".$paymentDate."',
				`paymentamount`='".$this->kclass->input['paymentamount']."',
				`paymentstatus`='".$this->kclass->input['paymentstatusid']."',
				`deliverytermid` = '".$this->kclass->input['deliverytermid']."',
				`paymenttermid` = '".$this->kclass->input['paymenttermid']."',
				`shipmethodid` = '".$this->kclass->input['shipmethodid']."',
				`currencyid` = '".$this->kclass->input['currencyid']."',
				`shipmentDate` = '".$shipmentDate."',
				`loading` = '".$this->kclass->input['loading']."',
				`discharge` = '".$this->kclass->input['discharge']."',
				`labeling` = '".$this->kclass->input['labeling']."',
				`packing` = '".$this->kclass->input['packing']."',
				`special` = '".$this->kclass->input['special']."',
				`remark` = '".$this->kclass->input['remark']."',
				`status` = '0',
				`modified` = '".TIMENOW."',
				`modifier` = '".$this->kclass->user['userid']."'
			WHERE orderid='".$order['orderid']."'
		");
		$this->kclass->DB->query("
			UPDATE `order` SET
				`director`= '".$this->kclass->input['director']."',
				`tracker`= '".$this->kclass->input['tracker']."',
				`ordertitle` = '".$orderTitlePO."',
				`paymenttime`='".$paymentDate."',
				`paymentstatus`='".$this->kclass->input['paymentstatusid']."',
				`deliverytermid` = '".$this->kclass->input['deliverytermid']."',
				`paymenttermid` = '".$this->kclass->input['paymenttermid']."',
				`shipmethodid` = '".$this->kclass->input['shipmethodid']."',
				`currencyid` = '".$this->kclass->input['currencyid']."',
				`shipmentDate` = '".$shipmentDate."',
				`loading` = '".$this->kclass->input['loading']."',
				`discharge` = '".$this->kclass->input['discharge']."',
				`labeling` = '".$this->kclass->input['labeling']."',
				`packing` = '".$this->kclass->input['packing']."',
				`special` = '".$this->kclass->input['special']."',
				`remark` = '".$this->kclass->input['remark']."',
				`status` = '0',
				`modified` = '".TIMENOW."',
				`modifier` = '".$this->kclass->user['userid']."'
			WHERE orderid='".$orderRelatePO['orderid']."'
		");

		$this->kclass->updateFee(array('module'=>'order','mid'=>$order['orderid']));
		$this->kclass->updateDiscount(array('module'=>'order','mid'=>$order['orderid']));
		$this->kclass->updateAttachs(array('module'=>'order', 'mid'=>$order['orderid']));
		if($this->kclass->input['rt']=='list'){
			$rt='&action=list';
		}elseif($this->kclass->input['rt']=='view'){
			$rt='&action=view&orderid='.$order['orderid'];
		}

		$this->kclass->messager(array(
			'title' => '修改订单',
			'text' => '订单 <b>'.$order['orderno'].'</b> 的信息已成功修改!',
			'url' => 's.php/?module=order'.$rt,
			'sec' => 3
		));
	}
	//
	function update(){
		$order = $this->kclass->DB->queryFirst("
			SELECT `order`.*, route.title AS routeTitle,
					customer.title AS customerTitle, customer.cntitle AS customerCntitle,
					supplier.title AS supplierTitle, supplier.entitle AS supplierEntitle
			FROM `order`
			LEFT JOIN `orderroute` AS route ON (`route`.routeid=`order`.routeid)
			LEFT JOIN customer ON (customer.customerid=`order`.customerid)
			LEFT JOIN supplier ON (supplier.supplierid=`order`.supplierid)
			WHERE `order`.killed=0 AND `orderid`='".$this->kclass->input['orderid']."'
		");
		//print_r($order);
		if(!$order){
			$e.='<li>订单数据错误</li>';
		}else{
			/*if($order['type']=='PI'){
				$title = '客户';
				if($this->kclass->user['userid']!=$order['director']){
					$e.='<li>您不是订单 <b>'.$order['orderno'].'</b> 的业务员无权限进行修改!</li>';
				}
			}elseif($order['type']=='PO'){
				$title = '供应商';
			}*/
		}
		if($e){
			$this->kclass->messager(array(
				'title' => '修改订单',
				'text' => '您在修改订单的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => REFERER,
				'sec' => 3
			));
		}

		$shipmentDate = date('Y-m-d', $order['shipmentDate']);
		$paymentDate=$this->kclass->iif($order['paymenttime']<=0,' ',date('Y-m-d',$order['paymenttime']));
		$this->kclass->br2nl=true;
		$order['labeling']=$this->kclass->parseConvertValue($order['labeling']);
		$order['packing']=$this->kclass->parseConvertValue($order['packing']);
		$order['special']=$this->kclass->parseConvertValue($order['special']);
		$order['remark']=$this->kclass->parseConvertValue($order['remark']);
		$director = $this->kclass->chooserTracker(array('name'=>'director','condition'=>'positionid=7','width'=>80,'selectedid'=>$order['director']));
		$tracker = $this->kclass->chooserTracker(array('name'=>'tracker','condition'=>'positionid=7','width'=>80,'selectedid'=>$order['tracker']));
		$loading = $this->kclass->chooserPort(array('name'=>'loading', 'hasBlank'=>1, 'width'=>450, 'selectedid'=>$order['loading']));
		$discharge = $this->kclass->chooserPort(array('name'=>'discharge', 'hasBlank'=>1, 'width'=>450, 'selectedid'=>$order['discharge']));
		$paymentterm = $this->kclass->chooserPaymentterm(array('name'=>'paymenttermid', 'hasBlank'=>1, 'width'=>450, 'selectedid'=>$order['paymenttermid']));
		$paymentstatus=$this->kclass->chooserPaymentstatus(array('name'=>'paymentstatusid','hasBlank'=>1,'width'=>300,'selectedid'=>$order['paymentstatus']));
		$deliveryterm = $this->kclass->chooserDeliveryterm(array('name'=>'deliverytermid', 'hasBlank'=>1, 'width'=>450, 'selectedid'=>$order['deliverytermid']));
		$shipmethod = $this->kclass->chooserShipmethod(array('name'=>'shipmethodid', 'hasBlank'=>1, 'width'=>450, 'selectedid'=>$order['shipmethodid']));
		$currency = $this->kclass->chooserCurrency(array('name'=>'currencyid', 'hasBlank'=>1, 'width'=>100, 'selectedid'=>$order['currencyid']));
		$upload = $this->kclass->upload(array('title'=>'相关附件：', 'module'=>'order', 'mid'=>$order['orderid']));

		if(strpos($order['ordertitle'],'-')>0){
			$customerStr = substr($order['ordertitle'],strpos($order['ordertitle'],'-'),(strpos($order['ordertitle'],'+')-12));
		}else{
			$customerStr = '';
		}
		if(strpos($order['ordertitle'],'+')>0){
			$productStr = substr($order['ordertitle'],strpos($order['ordertitle'],'+'));
		}else{
			$productStr = '';
		}
		// 审核或者审批后修改  修改原因
		if($order['ifVerify']!=0 OR $order['ifApprove']!=0){
			$updateReason='<tr class="odd"><td class="red bold">修改原因：</td><td><textarea name="reviseRemark" style="width:444px;height:100px"></textarea></td><td></td><td></td></tr>';
		}
		$items = $this->kclass->DB->query("SELECT * FROM `item` WHERE killed=0 AND module='order' AND mid='".$order['orderid']."' ORDER BY itemid ASC, modified ASC");
		$k=1;
		$n=1;
		if($this->kclass->DB->numRows()){
			$this->kclass->tbline+=1;
			while($item = $this->kclass->DB->fetchArray($items)){
				if($item['productitemid']>0){
					$product = $this->kclass->DB->queryFirst("
						SELECT `product`.title,`product`.entitle
						FROM  `productitem`
						LEFT JOIN `product` ON (`productitem`.productid = `product`.productid)
						WHERE productitemid='".$item['productitemid']."'
					");
					$seletStr = '<input type="hidden" id="tditemProductid'.$k.'input" value="'.$product['entitle'].'" />';
					$itemtr .= '<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').'">
					<td><span id="tditemProductid'.$k.'">'.$seletStr.'</span><input type="hidden" name="itemId['.$k.']" value="'.$item['itemid'].'">'.$this->kclass->chooserProductItem(array('name'=>'itemProductid['.$k.']','selectName'=>'itemProductid'.$k, 'selectedid'=>$item['productitemid'], 'hasBlank'=>1, 'width'=>500)).'</td>
					<td><input type="text" name="itemCaption['.$k.']" value="'.$item['caption'].'" size="65"></td>
					<td>'.$this->kclass->chooserBrand(array('name'=>'itemBrandid['.$k.']', 'hasBlank'=>1, 'width'=>80, 'selectedid'=>$item['brandid'])).'</td>
					<td>'.$this->kclass->chooserPacking(array('name'=>'itemPackingid['.$k.']', 'hasBlank'=>1, 'width'=>100, 'selectedid'=>$item['packingid'])).'</td>
					<td><input type="text" name="itemDiscount['.$k.']" value="'.$item['discount'].'" size="3"></td>
					<td><input type="text" name="itemQuantity['.$k.']" value="'.$item['quantity'].'" size="3" onblur="dc.returnProductStr('.$k.',this.value)"></td>
					<td>'.$this->kclass->chooserUnit(array('name'=>'itemUnitid['.$k.']', 'hasBlank'=>1, 'width'=>80, 'selectedid'=>$item['unitid'])).'</td>
					<td><input type="text" name="itemPrice['.$k.']" value="'.$item['price'].'" size="5"></td>
					<td><input type="text" name="itemOrdering['.$k.']" value="'.$item['ordering'].'" size="5"></td>
					<td><input type="checkbox" name="itemKill['.$k.']" value="'.$item['itemid'].'"></td>
					</tr>';
					$k++;
				}else{
					$parts .= '<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').'">
					<td><input type="hidden" name="itemPartsId['.$n.']" value="'.$item['itemid'].'">'.$this->kclass->chooserParts(array('name'=>'itemPartsid['.$n.']', 'selectedid'=>$item['productid'], 'hasBlank'=>1, 'width'=>500)).'</td>
					<td><input type="text" name="itemPartsCaption['.$n.']" value="'.$item['caption'].'" size="65"></td>
					<td>'.$this->kclass->chooserBrand(array('name'=>'itemPartsBrandid['.$n.']', 'hasBlank'=>1, 'width'=>80, 'selectedid'=>$item['brandid'])).'</td>
					<td>'.$this->kclass->chooserPacking(array('name'=>'itemPartsPackingid['.$n.']', 'hasBlank'=>1, 'width'=>100, 'selectedid'=>$item['packingid'])).'</td>
					<td><input type="text" name="itemPartsDiscount['.$n.']" value="'.$item['discount'].'" size="3"></td>
					<td><input type="text" name="itemPartsQuantity['.$n.']" value="'.$item['quantity'].'" size="3"></td>
					<td>'.$this->kclass->chooserUnit(array('name'=>'itemPartsUnitid['.$n.']', 'hasBlank'=>1, 'width'=>80, 'selectedid'=>$item['unitid'])).'</td>
					<td><input type="text" name="itemPartsPrice['.$n.']" value="'.$item['price'].'" size="5"></td>
					<td><input type="text" name="itemPartsOrdering['.$n.']" value="'.$item['ordering'].'" size="5"></td>
					<td><input type="checkbox" name="itemPartsKill['.$n.']" value="'.$item['itemid'].'"></td>
					</tr>';
					$n++;
				}
			}
		}
		for($i = $k; $i < ($k+5); $i++){
			$itemtrs .= '<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').'">
			<td><span id="tditemProductid'.$i.'"></span>'.$this->kclass->chooserProductItem(array('name'=>'itemProductid['.$i.']','selectName'=>'itemProductid'.$i, 'hasBlank'=>1, 'width'=>500)).'</td>
			<td><input type="text" name="itemCaption['.$i.']" value="'.$item['caption'].'"  size="65"></td>
			<td>'.$this->kclass->chooserBrand(array('name'=>'itemBrandid['.$i.']', 'hasBlank'=>1, 'width'=>80,'selectedid'=>1)).'</td>
			<td>'.$this->kclass->chooserPacking(array('name'=>'itemPackingid['.$i.']', 'hasBlank'=>1, 'width'=>100,'selectedid'=>6)).'</td>
			<td><input type="text" name="itemDiscount['.$i.']" value="100" size="3"></td>
			<td><input type="text" name="itemQuantity['.$i.']" value="'.$item['quantity'].'" size="3"  onblur="dc.returnProductStr('.$i.',this.value)"></td>
			<td>'.$this->kclass->chooserUnit(array('name'=>'itemUnitid['.$i.']', 'hasBlank'=>1, 'width'=>80, 'selectedid'=>5)).'</td>
			<td><input type="text" name="itemPrice['.$i.']" value="'.$item['price'].'" size="5"></td>
			<td><input type="text" name="itemOrdering['.$i.']" value="1" size="5"></td>
			<td></td>
			</tr>';
		}
		for($i = $n; $i < ($n+5); $i++){
			$partStr .= '<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').'">
			<td>'.$this->kclass->chooserParts(array('name'=>'itemPartsid['.$i.']', 'hasBlank'=>1, 'width'=>500)).'</td>
			<td><input type="text" name="itemPartsCaption['.$i.']" value="'.$item['caption'].'"  size="65"></td>
			<td>'.$this->kclass->chooserBrand(array('name'=>'itemPartsBrandid['.$i.']', 'hasBlank'=>1, 'width'=>80,'selectedid'=>1)).'</td>
			<td>'.$this->kclass->chooserPacking(array('name'=>'itemPartsPackingid['.$i.']', 'hasBlank'=>1, 'width'=>100,'selectedid'=>6)).'</td>
			<td><input type="text" name="itemPartsDiscount['.$i.']" value="100" size="3"></td>
			<td><input type="text" name="itemPartsQuantity['.$i.']" value="'.$item['quantity'].'" size="3"></td>
			<td>'.$this->kclass->chooserUnit(array('name'=>'itemPartsUnitid['.$i.']', 'hasBlank'=>1, 'width'=>80, 'selectedid'=>5)).'</td>
			<td><input type="text" name="itemPartsPrice['.$i.']" value="'.$item['price'].'" size="5"></td>
			<td><input type="text" name="itemPartsOrdering['.$i.']" value="1" size="5"></td>
			<td></td>
			</tr>';
		}
		$fee=$this->kclass->formFee(array('module'=>'order','mid'=>$order['orderid']));
		$discount=$this->kclass->formDiscount(array('module'=>'order','mid'=>$order['orderid']));
		if($order['supplierid']==1){
			$supplier=$order['supplierTitle'].'<input type="hidden" name="supplierid" value="1">';
			$supplierNo = $order['supplierNo'].'<input type="hidden" name="supplierNo" value="'.$order['supplierNo'].'">';
		}else{
			$supplier = $this->kclass->chooserSupplierSelect(array('name'=>'supplierid', 'hasBlank'=>1, 'width'=>450, 'selectedid'=>$order['supplierid']));
			$supplierNo = '<input type="text" style="width:330px" name="supplierNo" value="'.$order['supplierNo'].'"> <span class="gray small">供应商使用的订单号</span>';
		}
		if($order['customerid']==1){
			$customer=$order['customerTitle'].'<input type="hidden" name="customerid" value="1">';
			$customerNo = $order['customerNo'].'<input type="hidden" name="customerNo" value="'.$order['customerNo'].'">';
		}else{
			$customer = $this->kclass->chooserCustomer(array('hName'=>'customerid','selectedid'=>$order['customerid'],'value'=>$order['customerTitle'],'name'=>'customer','width'=>450, 'onchange'=>'customerStr'));
			//$customer = $this->kclass->chooserCustomer(array('name'=>'customerid', 'hasBlank'=>1, 'width'=>450,'onchange'=>'customerStr','selectedid'=>$order['customerid']));
			$customerNo = '<input type="text" style="width:340px" name="customerNo" value="'.$order['customerNo'].'"> <span class="gray small">客户使用的订单号</span>';
		}

$body = <<<EOF
<form action="/s.php?module=order&action=doupdate" name="order" method="post">
<input type="hidden" name="module" value="order">
<input type="hidden" name="action" value="doupdate">
<input type="hidden" name="orderid" value="{$this->kclass->input['orderid']}">
<input type="hidden" name="rt" value="{$this->kclass->input['rt']}">
<table class="hundred">
<thead>
<tr>
	<th colspan="4">修改订单：<span class="darkred" id="yearStr">{$order['orderno']}</span><span class="darkred" id="customerStr">{$customerStr}</span><span class="darkred" id="productStr">{$productStr}</span> <span class="normal">当前订单类型：</span><span class="darkred">{$order['type']}</span></th>
</tr>
</thead>
<tbody>
<tr class="even">
	<td title="">订单编号：</td>
	<td><input type="hidden" id="orderTitle" name="ordertitle" value="{$order['ordertitle']}"><input class="red middle bold" type="text" name="orderno" value="{$order['orderno']}"></td>
	<td title="">订单类型：</td>
	<td class="middle bold">{$order['routeTitle']} <span class="normal">当前订单类型：</span><span class="darkred">{$order['type']}</span></td>
</tr>
<tr class="odd">
	<td title="">币　　种：<span class="red bold">*</span></td>
	<td>{$currency}</td>
	<td title="">业务人员：</td>
	<td class="">{$director}　　　跟单人员：<span class="red">*</span>{$tracker}</td>
</tr>
<tr class="even">
	<td title="Customer">客　　户：<span class="red bold">*</span></td>
	<td>{$customer}</td>
	<td title="Supplier">供 应 商：<span class="red bold">*</span></td>
	<td>{$supplier}</td>
</tr>
<tr class="odd">
	<td title="Customer Order No">订 单 号：</td>
	<td>{$customerNo}</td>
	<td title="Supplier Order No">供应商单号：</td>
	<td>{$supplierNo}</td>
</tr>
<tr class="even">
	<td title="Shipment Method">运送方式：</td>
	<td>{$shipmethod}</td>
	<td title="Port of Loading">装货地点：</td>
	<td>{$loading}</td>
</tr>
<tr class="odd">
	<td title="Shipment Date">交货日期：<span class="red bold">*</span></td>
	<td><input type="text" style="width:150px" id="shipmentDate" name="shipmentDate" value="{$shipmentDate}"> <span class="gray small">除非补单，否则日期不能比建单当日早！</span></td>
	<td title="Port of Discharge">卸货地点：</td>
	<td>{$discharge}</td>
</tr>
<tr class="even">
	<td title="Payment Term">支付条款：</td>
	<td>{$paymentterm}</td>
	<td title="Delivery Term">交货条款：</td>
	<td>{$deliveryterm}</td>
</tr>
<tr class="even">
<td title="Delivery Term">付款状态：</td>
	<td>{$paymentstatus}</td>
	<td title="Payment Date">付款时间：</td>
	<td><input type="text" style="width:150px" id="paymentDate" name="paymentDate" value="{$paymentDate}"><span class="gray small">填到款的实际日期</span>　　<b>支付金额：</b><input type="text" name="paymentamount" value="{$order['paymentamount']}"></td>
</tr>
<tr class="odd">
	<td valign="top" title="Labeling">标签说明：</td>
	<td><textarea name="labeling" style="width:444px;height:100px">{$order['labeling']}</textarea></td>
	<td valign="top" title="Packing">包装说明：</td>
	<td><textarea name="packing" style="width:444px;height:100px">{$order['packing']}</textarea></td>
</tr>
<tr class="even">
	<td valign="top" title="Special">特别说明：</td>
	<td><textarea name="special" style="width:444px;height:100px">{$order['special']}</textarea></td>
	<td valign="top" title="Remark">备　　注：</td>
	<td><textarea name="remark" style="width:444px;height:100px">{$order['remark']}</textarea></td>
</tr>
{$updateReason}
</tbody>
</table>
<table class="hundred">
<thead>
<tr>
	<th colspan="12">订单明细表</th>
</tr>
</thead>
<tbody class="small">
<tr><td>产品选择<span class="red bold">*</span></td><td width="300">说明</td><td width="100">品牌</td><td width="50">包装</td><td width="30">折扣</td><td width="30">数量<span class="red bold">*</span></td><td width="80">单位<span class="red bold">*</span></td><td width="50">单价<span class="red bold">*</span></td><td width="30">排序</td><td  width="20">删</td></tr>
{$itemtr}
<tr><td>配件选择<span class="red bold">*</span></td><td width="300">说明</td><td width="100">品牌</td><td width="50">包装</td><td width="30">折扣</td><td width="30">数量<span class="red bold">*</span></td><td width="80">单位<span class="red bold">*</span></td><td width="50">单价<span class="red bold">*</span></td><td width="30">排序</td><td  width="20">删</td></tr>
{$parts}
<tr>
	<th colspan="12">新建明细</th>
</tr>
<tr><td>产品选择<span class="red bold">*</span></td><td width="300">说明</td><td width="100">品牌</td><td width="50">包装</td><td width="30">折扣</td><td width="30">数量<span class="red bold">*</span></td><td width="80">单位<span class="red bold">*</span></td><td width="50">单价<span class="red bold">*</span></td><td width="30">排序</td><td  width="20"></td></tr>
{$itemtrs}
<tr><td>配件选择<span class="red bold">*</span></td><td width="300">说明</td><td width="100">品牌</td><td width="50">包装</td><td width="30">折扣</td><td width="30">数量<span class="red bold">*</span></td><td width="80">单位<span class="red bold">*</span></td><td width="50">单价<span class="red bold">*</span></td><td width="30">排序</td><td  width="20"></td></tr>
{$partStr}
<tr>
	<td colspan="12" class="gray small">注：①如行数不够，请保存后再进入修改页面。②至少有“名称”、“数量”、“单位”、“单价”等四个项目，该条明细才会被保存。③折扣值范围“1-100”%，不填默认为“100”%。</td>
</tr>
</tbody>
</table>
{$fee}
{$discount}
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
		$this->kclass->page['title'] .= ' - '.$order['orderno'].' - 修改订单';
		$this->kclass->page['onload'] .= 'dc.tabhover();$(\'#shipmentDate\').datepicker();dc.tabhover();$(\'#paymentDate\').datepicker();';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - <a href="/s.php?module=order&action=view&orderid='.$order['orderid'].'">'.$order['orderno'].'</a> - 修改订单', 'right' => '<a href="/s.php?module=order">返回列表</a>', 'body'=>$body));
	}
	// old order statistics 
	function statistics(){
		$body=<<<EOF
		<form action="/s.php?module=order&action=selectstatistics"method="post">
		<table width="600px">
		<thead>
		<tr><th colspan="2">客户订单统计</th></tr>
		</thead>
		<tbody>
		<tr>
		<td >选择统计方式：</td>
        </tr>
		<tr>
		<td align="center">
		  <label for="r1"><input type="radio" name="type" value="r1" >不同型号比较</label>
		  <label for="r2"><input type="radio" name="type" value="r2"  checked>同一型号比较</label>
		</td>
		</tr>
	    <tr>
		<td align="center"><input type="submit" value="下一步"/></td>
		</tr>
		</table>
		</form>
EOF;

		$this->kclass->page['title'].='-订单统计';
		$this->kclass->page['onload'].='dc.tabhover();$(\'#shipmentDate\').datepicker()';
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.'-订单统计','right'=>'<a href="/s.php?module=order">返回列表</a>','body'=>$body));
	}
	//作废
	function nostatistics(){
		$item='<table>
		<thead><tr><th colspan="4">订单统计</th></tr></thead>
         <tbody>
		 <tr >
		 <td>按照订单编号：</td><td colspan="3"><input type="text" style="width:250px" name="orderno" value=""><span class="small gray">至少要填写四个字符以上,如 0730 月份和日期</span></td>
		 </tr>
		 <tr>
		<td >或按照型号：</td><td colspan="3">'.$this->kclass->chooserProduct(array('name'=>'productid','selectName'=>'productid', 'selectedid'=>1, 'hasBlank'=>1, 'width'=>250)).'</td>
		</tr>
		 <tr>
		 <td>或按照客户名：</td><td colspan="3"><input type="text" style="width:250px" name="customer" id="autoComplete" value="'.$customer['title'].'"> <span class="small gray">可使用客户名称中的关键字查找</span></td>
		 </tr>
		 <tr>
		 <td>或按照业务员：</td><td colspan="3">'.$this->kclass->chooserTracker(array('name'=>'director','condition'=>'positionid=7','hasBlank'=>1,'width'=>250)).'</td>
		 </tr>
		 <tr>
		 <td>或按照国家或地区：</td><td colspan="3">'. $this->kclass->chooserRegion(array('name'=>'regionid', 'hasBlank'=>1, 'selectedid'=>1,'width'=>250)).'</td>
		 </tr>
		 <tr>
		 <td>或按照运输方式：</td><td colspan="3">'.$this->kclass->chooserShipmethod(array('name'=>'shipmethodid', 'hasBlank'=>1, 'width'=>250, 'selectedid'=>$inquiry['shipmethodid'])).'</td>
		 </tr>
		 <tr>
		 <td>或按照支付条款：</td><td colspan="3">'.$this->kclass->chooserPaymentterm(array('name'=>'paymenttermid', 'hasBlank'=>1, 'width'=>250, 'selectedid'=>$inquiry['paymenttermid'])).'</td>
		 </tr>
		 <tr>
		 <td>或按照下单时间：</td><td colspan="3">开始时间：<input type="text"  id="orderstarttime" name="orderstarttime">　　结束时间：<input type="text"  id="orderendtime" name="orderendtime" ></td>
		 </tr>
		 <tr>
		 <td>或按照来源时间：</td><td colspan="3">开始时间：<input type="text" id="sourcestarttime" name="sourcestarttime" >　　结束时间：<input type="text"  id="sourceendtime" name="sourceendtime" ></td>
		 </tr>
		 <tr>
		 <td>或按照发货时间：</td><td colspan="3">开始时间：<input type="text"  id="shipstarttime" name="shipstarttime" >　　结束时间：<input type="text"  id="shipendtime" name="shipendtime" ></td>
		 </tr>
		 <tr>
		 <td>或按照客户来源：</td><td colspan="3">'. $this->kclass->chooserCustomerSources(array('name'=>'customersourceid', 'hasBlank'=>1, 'width'=>250)).'</td>
		 </tr>
		 
		 <th colspan="4">
		选项
		</th>
		</tr>
		<tr class="even">
	<td>排序方式：</td>
	<td colspan="3">
	<label for="o0"><input type="radio" name="orderby" value="orderquantity" id="o0" >订单量</label>
	<label for="o1"><input type="radio" name="orderby" value="customer" id="o1">客户姓名</label>
	<label for="o2"><input type="radio" name="orderby" value="orderid" id="o2" checked >订单号</label> 
	<label for="o3"><input type="radio" name="orderby" value="dateline" id="o3">交货时间</label>
	</td>
</tr>
<tr class="odd">
	<td>排列方向：</td>
	<td colspan="3">
		<label for="d0"><input type="radio" id="d0" name="direction" value="ASC" checked>升序(A-Z)</label> 
		<label for="d1"><input type="radio" id="d1" name="direction" value="DESC">降序(Z-A)</label> 
	</td>
</tr>
<tr class="even">
	<td>显示选项：</td>
	<td colspan="3">
		<label for="s0"><input type="radio" id="s0" name="show" value="default" checked>默认列表</label> 
		<label for="s1"><input type="radio" id="s1" name="show" value="all">所有<span class="small gray">(包括删除)</span></label> 
	</td>
</tr>
		 <tr><td  colspan="4" align="center"><input type="submit" name="port" value="提交"><input type="reset" value="重置"></td></tr>
		 </tbody>
		</table>';
		$body=<<<EOF
  <form action="/p.php?module=printer&action=orderstatistics " method="post" target="_blank">
		{$item}
</form>
<script type="text/javascript">
$(function(){
	$("#autoComplete").inputAutoComplete({location:"/s.php?module=ajax&action=findCustomer",datatype:"json",selectcount:"scrollbar",scrollbarcount:9});
});
</script>
EOF;
$this->kclass->page['onload'] .= 'dc.tabhover();$(\'#date\').datepicker();var dates=$(\'#orderstarttime,#orderendtime\').datepicker({onSelect:function(selectedDate){var option=this.id==\'orderstarttime\'?\'minDate\':\'maxDate\',instance=$(this).data(\'datepicker\'),date=$.datepicker.parseDate(instance.settings.dateFormat || $.datepicker._defaults.dateFormat,selectedDate,instance.settings);dates.not(this).datepicker(\'option\',option,date);}});var dates2=$(\'#sourcestarttime,#sourceendtime\').datepicker({onSelect:function(selectedDate){var option2=this.id==\'sourcestarttime\'?\'minDate\':\'maxDate\',instance2=$(this).data(\'datepicker\'),date2=$.datepicker.parseDate(instance2.settings.dateFormat || $.datepicker._defaults.dateFormat,selectedDate,instance2.settings);dates2.not(this).datepicker(\'option\',option2,date2);}});var dates3=$(\'#shipstarttime,#shipendtime\').datepicker({onSelect:function(selectedDate){var option3=this.id==\'shipstarttime\'?\'minDate\':\'maxDate\',instance3=$(this).data(\'datepicker\'),date3=$.datepicker.parseDate(instance3.settings.dateFormat || $.datepicker._defaults.dateFormat,selectedDate,instance3.settings);dates3.not(this).datepicker(\'option\',option3,date3);}});var dates4=$(\'#startDate,#endDate\').datepicker({onSelect:function(selectedDate){var option4=this.id==\'startDate\'?\'minDate\':\'maxDate\',instance4=$(this).data(\'datepicker\'),date4=$.datepicker.parseDate(instance4.settings.dateFormat || $.datepicker._defaults.dateFormat,selectedDate,instance4.settings);dates4.not(this).datepicker(\'option\',option4,date4);}});';
    $this->kclass->page['title'].='-订单统计';
	//$this->kclass->page['onload'].='dc.tabhover();$(\'#date\').datepicker()';
	$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.'-订单统计','right'=>'<a href="/s.php?module=order">返回列表</a>','body'=>$body));
	}
	//
	function selectstatistics(){
	if($this->kclass->input['type']==''){
		$e='<li>请从正确的页面进行操作，如果此问题持续存在，请联系网站管理员。</li>';
	}
	if(isset($e)){
		$this->kcalss->messager(array(
			'title'=>'选择统计类型',
			'text'=>'您在选择统计类型的过程中有以下错误：<ul>'.$e.'</ul>',
			'url'=>'javascript:history.back()',
			'sec'=>3
			));
	}
	$startDate=date('Y-m-d');
	$endDate=date('Y-m-d');
	if($this->kclass->input['type']=='r2'){
		$item='<table >
		<thead><tr><th colspan="4">同种型号比较统计</th></tr></thead>
		<tbody>
		<tr>
		<td >选择型号：</td><td colspan="3">'.$this->kclass->chooserProduct(array('name'=>'productid','selectName'=>'productid', 'selectedid'=>1, 'hasBlank'=>1, 'width'=>300)).'</td>
		</tr>
		<tr>
		<td>起始时间：</td><td><input type="text" id="startDate" name="startdate" value="'.$startDate.'"></td><td>截止时间：</td><td><input type="text"   id="endDate" name="enddate" value="'.$endDate.'"></td>
		</tr>
		<tr>
		<th colspan="4">
		选项
		</th>
		</tr>
		<tr class="even">
	<td>排序方式：</td>
	<td colspan="3">
		<label for="o0"><input type="radio" name="orderby" value="orderid" id="o0" checked>订单号</label> 
		<label for="o1"><input type="radio" name="orderby" value="country" id="o1">国家</label> 
		<label for="o2"><input type="radio" name="orderby" value="customer" id="o2">客户姓名</label> 
		<label for="o3"><input type="radio" name="orderby" value="dateline" id="o3">交货时间</label>
		<label for="o4"><input type="radio" name="orderby" value="orderquantity" id="o4">订单量</label>
	</td>
</tr>
<tr class="odd">
	<td>排列方向：</td>
	<td colspan="3">
		<label for="d0"><input type="radio" id="d0" name="direction" value="ASC" checked>升序(A-Z)</label> 
		<label for="d1"><input type="radio" id="d1" name="direction" value="DESC">降序(Z-A)</label> 
	</td>
</tr>
<tr class="even">
	<td>显示选项：</td>
	<td colspan="3">
		<label for="s0"><input type="radio" id="s0" name="show" value="default" checked>默认列表</label> 
		<label for="s1"><input type="radio" id="s1" name="show" value="all">所有<span class="small gray">(包括删除)</span></label> 
	</td>
</tr>
		<tr><td  colspan="4" align="center"><input type="submit" name="product" value="提交"><input type="reset" value="重置"></td></tr>
		</tbody>
		</table>';
	}else{
		$item='<table>
		<thead><tr><th colspan="4">订单统计</th></tr></thead>
         <tbody>
		 <tr >
		 <td>按照订单编号：</td><td colspan="3"><input type="text" style="width:250px" name="orderno" value=""><span class="small gray">至少要填写四个字符以上,如 0730 月份和日期</span></td>
		 </tr>
		 <tr>
		 <td>或按照客户名：</td><td colspan="3"><input type="text" style="width:250px" name="customer" id="autoComplete" value="'.$customer['title'].'"> <span class="small gray">可使用客户名称中的关键字查找</span></td>
		 </tr>
		 <tr>
		 <td>或按照业务员：</td><td colspan="3">'.$this->kclass->chooserTracker(array('name'=>'director','condition'=>'positionid=7','hasBlank'=>1,'width'=>250)).'</td>
		 </tr>
		 <tr>
		 <td>或按照国家或地区：</td><td colspan="3">'. $this->kclass->chooserRegion(array('name'=>'regionid', 'hasBlank'=>1, 'selectedid'=>1,'width'=>250)).'</td>
		 </tr>
		 <tr>
		 <td>或按照运输方式：</td><td colspan="3">'.$this->kclass->chooserShipmethod(array('name'=>'shipmethodid', 'hasBlank'=>1, 'width'=>250, 'selectedid'=>$inquiry['shipmethodid'])).'</td>
		 </tr>
		 <tr>
		 <td>或按照支付条款：</td><td colspan="3">'.$this->kclass->chooserPaymentterm(array('name'=>'paymenttermid', 'hasBlank'=>1, 'width'=>250, 'selectedid'=>$inquiry['paymenttermid'])).'</td>
		 </tr>
		 <tr>
		 <td>或按照下单时间：</td><td colspan="3">开始时间：<input type="text"  id="orderstarttime" name="orderstarttime">　　结束时间：<input type="text"  id="orderendtime" name="orderendtime" ></td>
		 </tr>
		 <tr>
		 <td>或按照来源时间：</td><td colspan="3">开始时间：<input type="text" id="sourcestarttime" name="sourcestarttime" >　　结束时间：<input type="text"  id="sourceendtime" name="sourceendtime" ></td>
		 </tr>
		 <tr>
		 <td>或按照发货时间：</td><td colspan="3">开始时间：<input type="text"  id="shipstarttime" name="shipstarttime" >　　结束时间：<input type="text"  id="shipendtime" name="shipendtime" ></td>
		 </tr>
		 <tr>
		 <td>或按照客户来源：</td><td colspan="3">'. $this->kclass->chooserCustomerSources(array('name'=>'customersourceid', 'hasBlank'=>1, 'width'=>250)).'</td>
		 </tr>
		 
		 <th colspan="4">
		选项
		</th>
		</tr>
		<tr class="even">
	<td>排序方式：</td>
	<td colspan="3">
	<label for="o0"><input type="radio" name="orderby"  value="orderquantity" id="o0" >订单量</label>
	<label for="o1"><input type="radio" name="orderby" value="customer" id="o1">客户姓名</label>
	<label for="o2"><input type="radio" name="orderby" value="orderid" id="o2" checked >订单号</label> 
	<label for="o3"><input type="radio" name="orderby" value="dateline" id="o3">交货时间</label>
	</td>
</tr>
<tr class="odd">
	<td>排列方向：</td>
	<td colspan="3">
		<label for="d0"><input type="radio" id="d0" name="direction" value="ASC" checked>升序(A-Z)</label> 
		<label for="d1"><input type="radio" id="d1" name="direction" value="DESC">降序(Z-A)</label> 
	</td>
</tr>
<tr class="even">
	<td>显示选项：</td>
	<td colspan="3">
		<label for="s0"><input type="radio" id="s0" name="show" value="default" checked>默认列表</label> 
		<label for="s1"><input type="radio" id="s1" name="show" value="all">所有<span class="small gray">(包括删除)</span></label> 
	</td>
</tr>
		 <tr><td  colspan="4" align="center"><input type="submit" name="port" value="提交"><input type="reset" value="重置"></td></tr>
		 </tbody>
		</table>';
	}
$body=<<<EOF
  <form action="/p.php?module=printer&action=orderstatistics " method="post" target="_blank">
		{$item}
</form>
<script type="text/javascript">
$(function(){
	$("#autoComplete").inputAutoComplete({location:"/s.php?module=ajax&action=findCustomer",datatype:"json",selectcount:"scrollbar",scrollbarcount:9});
});
</script>
EOF;
$this->kclass->page['onload'] .= 'dc.tabhover();$(\'#date\').datepicker();var dates=$(\'#orderstarttime,#orderendtime\').datepicker({onSelect:function(selectedDate){var option=this.id==\'orderstarttime\'?\'minDate\':\'maxDate\',instance=$(this).data(\'datepicker\'),date=$.datepicker.parseDate(instance.settings.dateFormat || $.datepicker._defaults.dateFormat,selectedDate,instance.settings);dates.not(this).datepicker(\'option\',option,date);}});var dates2=$(\'#sourcestarttime,#sourceendtime\').datepicker({onSelect:function(selectedDate){var option2=this.id==\'sourcestarttime\'?\'minDate\':\'maxDate\',instance2=$(this).data(\'datepicker\'),date2=$.datepicker.parseDate(instance2.settings.dateFormat || $.datepicker._defaults.dateFormat,selectedDate,instance2.settings);dates2.not(this).datepicker(\'option\',option2,date2);}});var dates3=$(\'#shipstarttime,#shipendtime\').datepicker({onSelect:function(selectedDate){var option3=this.id==\'shipstarttime\'?\'minDate\':\'maxDate\',instance3=$(this).data(\'datepicker\'),date3=$.datepicker.parseDate(instance3.settings.dateFormat || $.datepicker._defaults.dateFormat,selectedDate,instance3.settings);dates3.not(this).datepicker(\'option\',option3,date3);}});var dates4=$(\'#startDate,#endDate\').datepicker({onSelect:function(selectedDate){var option4=this.id==\'startDate\'?\'minDate\':\'maxDate\',instance4=$(this).data(\'datepicker\'),date4=$.datepicker.parseDate(instance4.settings.dateFormat || $.datepicker._defaults.dateFormat,selectedDate,instance4.settings);dates4.not(this).datepicker(\'option\',option4,date4);}});';
    $this->kclass->page['title'].='-订单统计';
	//$this->kclass->page['onload'].='dc.tabhover();$(\'#date\').datepicker()';
	$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.'-订单统计','right'=>'<a href="/s.php?module=order">返回列表</a>','body'=>$body));
	}
	//
	
	//
	function kill(){
		if($this->kclass->input['orderid'] < 0){
			$e = '<li>请从正确的页面进行操作，如果此问题持续存在，请联系网站管理员。</li>';
		}
		if($this->kclass->input['confirm']==0){
			$this->kclass->boinkIt('/s.php?module=order');
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
			$rt = '&action=view&orderid='.$this->kclass->input['orderid'];
		}elseif($this->kclass->input['rt']=='list'){
			$rt = '#'.$this->kclass->input['orderid'];
		}
		$order = $this->kclass->DB->queryFirst("
			SELECT *
			FROM `order`
			WHERE orderid='".$this->kclass->input['orderid']."'
		");
		if($order){
			if(2==$order['status']){
				$this->kclass->messager(array(
						'title' => '修改订单',
						'text' => '订单 <b>'.$order['orderno'].'</b> 的信息已经通过了审批不能再删除!',
						'url' => REFERER,
						'sec' => 3
				));
			}else{
				$this->kclass->DB->query("
					UPDATE `order`
					SET killed=".TIMENOW."
					WHERE orderid='".$this->kclass->input['orderid']."'
				");
				$this->kclass->messager(array(
					'title' => '删除订单成功',
					'text' => '订单 <b>'.$order['orderno'].'</b> 已成功被标记为删除!',
					'url' => '/s.php?module=order'.$rt,
					'sec' => 2
				));
			}
		}else{
			$this->kclass->messager(array(
				'title' => '删除订单失败',
				'text' => '您要删除的订单，不存在！',
				'url' => '/s.php?module=order'.$rt,
				'sec' => 3
			));
		}
	}

	/**
	 * 审核订单表单
	 * @author creator mayinghao 2013-07-17 8:15
	 * @access public
	 * @param none 没有参数
	 * @return void 没有返回信息
	 * @throws none 没有异常
	 */
	function verify(){
		$order=$this->kclass->DB->queryFirst("SELECT orderno,ifVerify FROM `order` WHERE orderid='".$this->kclass->input['orderid']."'");
		if(!$order){
			$e.='<li>数据错误，很抱歉~</li>';
		}
		if($order['ifVerify']!=0){
			$e.='<li>订单 <b>'.$order['orderno'].'</b> 已通过审核，请进行审批</li>';
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '审核订单',
				'text' => '审核订单<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
$body = <<<EOF
<form action="/s.php?module=order&action=doverify" name="order" method="post">
<input type="hidden" name="module" value="order">
<input type="hidden" name="action" value="doverify">
<input type="hidden" name="orderid" value="{$this->kclass->input['orderid']}">
<table class="hundred">
<thead>
<tr>
<th colspan="4">审核订单 <b>{$order['orderno']}</b>：</th>
</tr>
</thead>
<tbody>
<tr class="odd">
<td colspan="4" align="center">审核通过：<input type="radio" name="ifVerify" value="1" checked>　　　审核不通过：<input type="radio" name="ifVerify" value="0"></td>
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
		$this->kclass->page['title'] .= ' - 审核订单';
		$this->kclass->page['onload'] .= 'dc.tabhover();$(\'#shipmentDate\').datepicker();';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 审核订单', 'right' => '<a href="'.REFERER.'">返回</a>', 'body'=>$body));
	}

	/**
	 * 审核订单
	 * @author creator mayinghao 2013-07-17 8:15
	 * @access public
	 * @param none 没有参数
	 * @return void 没有返回信息
	 * @throws none 没有异常
	 */
	function doverify(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e='<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			$order=$this->kclass->DB->queryFirst("SELECT orderno,ifVerify FROM `order` WHERE orderid='".$this->kclass->input['orderid']."'");
			if(!$order){
				$e.='<li>数据错误。</li>';
			}
			if($order['ifVerify']!=0){
				$e.='<li>订单 <b>'.$order['orderno'].'</b> 已进行过审核，请进行审批</li>';
			}
			if($this->kclass->input['ifVerify']!=1 AND $this->kclass->input['ifVerify']!=-1){
				$e.='<li>请选择订单中的 是否通过审核 按钮。</li>';
			}
		}
		if($e){
			$this->kclass->messager(array(
				'title' => '审核订单',
				'text' => '您在审核订单的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$this->kclass->DB->query("
			UPDATE `order` SET
				ifVerify='".$this->kclass->input['ifVerify']."',
				verifier='".$this->kclass->user['userid']."',
				verified='".TIMENOW."'
			WHERE orderid='".$this->kclass->input['orderid']."'
		");
		if($this->kclass->input['ifVerify']==1){
			$verify='已通过';
		}elseif($this->kclass->input['ifVerify']==-1){
			$verify='未通过';
		}
		$this->kclass->messager(array(
			'title' => '审核订单',
			'text' => '订单 【<b>'.$order['orderno'].'</b>】 '.$verify.'审核!返回查看订单',
			'url' => '/s.php?module=order&action=view&orderid='.$this->kclass->input['orderid'],
			'sec' => 2
		));
	}


	/**
	 * 审批订单表单
	 * @author creator mayinghao 2013-07-17 8:15
	 * @access public
	 * @param none 没有参数
	 * @return void 没有返回信息
	 * @throws none 没有异常
	 */
	function approve(){
		$body = <<<EOF
<form action="/s.php?module=order&action=doapprove" name="order" method="post">
<input type="hidden" name="module" value="order">
<input type="hidden" name="action" value="doapprove">
<input type="hidden" name="orderid" value="{$this->kclass->input['orderid']}">
<table class="hundred">
<thead>
<tr>
<th colspan="4">审批订单：</th>
</tr>
</thead>
<tbody>
<tr class="odd">
<td colspan="4" align="center">审批通过：<input type="radio" name="ifApprove" value="1" checked>　　　审批不通过：<input type="radio" name="ifApprove" value="0"></td>
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
		$this->kclass->page['title'] .= ' - 审批订单';
		$this->kclass->page['onload'] .= 'dc.tabhover();$(\'#shipmentDate\').datepicker();';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 审批订单', 'right' => '<a href="'.REFERER.'">返回</a>', 'body'=>$body));
	}

	/**
	 * 审批订单
	 * @author creator mayinghao 2013-07-17 8:30
	 * @access public
	 * @param none 没有参数
	 * @return void 没有返回信息
	 * @throws none 没有异常
	 */
	function doapprove(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e='<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			$order=$this->kclass->DB->queryFirst("SELECT orderno,ifApprove FROM `order` WHERE orderid='".$this->kclass->input['orderid']."'");
			if(!$order){
				$e.='<li>数据错误。</li>';
			}
			if($order['ifApprove']!=0){
				$e.='<li>订单 <b>'.$order['orderno'].'</b> 已进行过审批</li>';
			}
			if($this->kclass->input['ifApprove']!=1 AND $this->kclass->input['ifApprove']!=-1){
				$e.='<li>请选择订单中的 是否通过审批 按钮。</li>';
			}
		}
		if($e){
			$this->kclass->messager(array(
				'title' => '审批订单',
				'text' => '您在审批订单的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$this->kclass->DB->query("
			UPDATE `order` SET
				ifApprove='".$this->kclass->input['ifApprove']."',
				approver='".$this->kclass->user['userid']."',
				approved='".TIMENOW."'
			WHERE orderid='".$this->kclass->input['orderid']."'
		");
		if($this->kclass->input['ifApprove']==1){
			$verify='已通过';
		}elseif($this->kclass->input['ifApprove']==-1){
			$verify='未通过';
		}
		$this->kclass->messager(array(
			'title' => '审批订单',
			'text' => '订单 【<b>'.$order['orderno'].'</b>】 '.$verify.'审批!返回查看订单',
			'url' => '/s.php?module=order&action=view&orderid='.$this->kclass->input['orderid'],
			'sec' => 2
		));
	}

	/**
	 * 生成PO
	 * @author creator mayinghao 2013-07-17 8:30
	 * @access public
	 * @param none 没有参数
	 * @return void 没有返回信息
	 * @throws none 没有异常
	 */
	function createPO(){
		if($this->kclass->input['orderid']>0){
			$order = $this->kclass->DB->queryFirst("
				SELECT `order`.*
				FROM `order`
				WHERE orderid='".$this->kclass->input['orderid']."'
			");

			if($order){
				$suborders = $this->kclass->DB->query("
					SELECT `order`.orderid,`order`.type
					FROM `order`
					WHERE parentid='".$this->kclass->input['orderid']."' ORDER BY orderid ASC
				");
				if($this->kclass->DB->numRows()>0){
					$items = $this->kclass->DB->query("
						SELECT `item`.*
						FROM `item`
						WHERE module='order' AND mid='".$this->kclass->input['orderid']."'
					");
					$itemArr = array();
					if($items){
						while($item=$this->kclass->DB->fetchArray($items)){
							$itemArr[] = $item;
						}
					}
					while($suborder=$this->kclass->DB->fetchArray($suborders)){
						if('PO'==$suborder['type']){
							$orderno = substr($order['orderno'],2,(strlen($order['orderno'])-2));
							$ordertitle = substr($order['ordertitle'],2,(strlen($order['ordertitle'])-2));
							$supplierNo = substr($order['supplierNo'],2,(strlen($order['supplierNo'])-2));
							$order['orderno'] = 'PO'.$orderno;
							$order['ordertitle'] = 'PO'.$ordertitle;
							$order['supplierNo'] = 'PO'.$supplierNo;
						}
						$this->kclass->DB->query("
							UPDATE `order` SET
								orderno='".$order['orderno']."',
								ordertitle='".$order['ordertitle']."',
								supplierNo='".$order['supplierNo']."',
								loading='".$order['loading']."',
								discharge='".$order['discharge']."',
								currencyid='".$order['currencyid']."',
								deliverytermid='".$order['deliverytermid']."',
								paymenttermid='".$order['paymenttermid']."',
								shipmethodid='".$order['shipmethodid']."',
								shipmentDate='".$order['shipmentDate']."',
								labeling='".$order['labeling']."',
								special='".$order['special']."',
								remark='".$order['remark']."',
								status='0',
								killed='0',
								modified='".TIMENOW."',
								director='".$order['director']."',
								tracker='".$order['tracker']."',
								creator='".$this->kclass->user['userid']."',
								created='".TIMENOW."'
							WHERE orderid='".$suborder['orderid']."'
						");
						foreach ($itemArr as $key => $val){
							$this->kclass->DB->query("
								INSERT INTO `item` (`module`,`mid`,`productid`,`productAttributes`,`title`,`model`,`caption`,`articleNo`,`brandid`,`packingid`,`quantity`,`discount`,`unitid`,`price`,`amount`,`modified`,`creator`,`created`)
								VALUES ('order','".$suborder['orderid']."','".$val['productid']."','".$val['productAttributes']."','".$val['title']."','".$val['model']."','".$val['caption']."','".$val['articleNo']."','".$val['brandid']."','".$val['packingid']."','".$val['quantity']."','".$val['discount']."','".$val['unitid']."','".$val['price']."','".$val['amount']."','".TIMENOW."','".$this->kclass->user['userid']."','".TIMENOW."')
							");
						}
					}
					$this->kclass->messager(array(
							'title' => '生成PO',
							'text' => 'PO生成成功!',
							'url' => '/s.php?module=order&action=view&orderid='.$this->kclass->input['orderid'],
							'sec' => 5
					));
				}
			}else{
				$this->kclass->messager(array(
					'title' => '生成PO',
					'text' => '没有此订单!',
					'url' => 'javascript:history.back()',
					'sec' => 5
				));
			}
		}else{
			$this->kclass->messager(array(
				'title' => '生成PO',
				'text' => '订单参数不正确!',
				'url' => 'javascript:history.back()',
				'sec' => 5
			));
		}
	}

	//
	function remove(){
		if($this->kclass->input['orderid']<=0){
			$this->kclass->boinkIt('/s.php?module=order');
		}
		$order = $this->kclass->DB->queryFirst("
			SELECT orderno AS title
			FROM `order`
			WHERE orderid='".$this->kclass->input['orderid']."'
		");
$body = <<<EOF
<form action="/s.php?module=order&action=kill" name="order" method="post">
<input type="hidden" name="module" value="order">
<input type="hidden" name="action" value="kill">
<input type="hidden" name="orderid" value="{$this->kclass->input['orderid']}">
<table><thead>
<thead>
<tr>
	<th>删除订单：{$order['title']}</th>
</tr>
</thead>
<tbody>
<tr class="even">
	<td class="middle">你确定要删除订单: <a href="/s.php?module=order&action=view&orderid={$this->kclass->input['orderid']}" class="big bold" target="_blank">{$order['title']}</a> 吗?</td>
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
		$this->kclass->page['title'] .= ' - 删除 - '.$order['title'];
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 删除订单 - '.$order['title'], 'right' => '<a href="/s.php?module=order">返回列表</a>', 'body'=>$body));
	}

	//
	function revival(){
		if($this->kclass->input['orderid']<=0){
			$this->kclass->boinkIt('/s.php?module=order');
		}
		if($this->kclass->input['confirm'] == 0){
			$this->kclass->boinkIt('/s.php?module=order&action=view&orderid='.$this->kclass->input['orderid']);
		}
		if($this->kclass->input['orderid'] < 0){
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
			SELECT ordertitle
			FROM `order`
			WHERE orderid='".$this->kclass->input['orderid']."'
		");
		if($this->kclass->input['rt']=='view'){
			$rt = '&action=view&orderid='.$this->kclass->input['orderid'];
		}elseif($this->kclass->input['rt']=='list'){
			$rt = '#'.$this->kclass->input['orderid'];
		}
		if($order){
			$this->kclass->DB->query("
				UPDATE `order`
				SET killed=0,killer=0
				WHERE orderid='".$this->kclass->input['orderid']."'
			");
			$this->kclass->messager(array(
				'title' => '恢复订单成功',
				'text' => '订单 <b>'.$order['ordertitle'].'</b> 已成功被恢复！',
				'url' => '/s.php?module=order'.$rt,
				'sec' => 2
			));
		}else{
			$this->kclass->messager(array(
				'title' => '恢复订单失败',
				'text' => '您要恢复的订单不存在！',
				'url' => '/s.php?module=order'.$rt,
				'sec' => 3
			));
		}
	}

	//
	function restore(){
		if($this->kclass->input['orderid']<=0){
			$this->kclass->boinkIt('/s.php?module=order');
		}
		$order = $this->kclass->DB->queryFirst("
			SELECT ordertitle
			FROM `order`
			WHERE orderid='".$this->kclass->input['orderid']."'
		");
$body = <<<EOF
<form action="/s.php?module=order&action=revival" name="order" method="post">
<input type="hidden" name="module" value="order">
<input type="hidden" name="action" value="revival">
<input type="hidden" name="orderid" value="{$this->kclass->input['orderid']}">
<table><thead>
<thead>
<tr>
	<th>恢复订单：{$order['ordertitle']}</th>
</tr>
</thead>
<tbody>
<tr class="even">
	<td class="middle">你确定要恢复订单: <a href="/s.php?module=order&action=view&orderid={$this->kclass->input['orderid']}" class="big bold" target="_blank">{$order['ordertitle']}</a> 吗?</td>
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
		$this->kclass->page['title'] .= ' - 恢复 - '.$order['title'];
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 恢复订单 - '.$order['title'], 'right' => '<a href="/s.php?module=order">返回列表</a>', 'body'=>$body));
	}
	function find(){
		$director = $this->kclass->chooserTracker(array('name'=>'director','condition'=>'positionid=7','hasBlank'=>1,'width'=>450));
		$tracker = $this->kclass->chooserTracker(array('name'=>'tracker','condition'=>'positionid=7','hasBlank'=>1,'width'=>450));
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
	<th colspan="2">查找订单</th>
</tr>
</thead>
<tbody>
<tr class="even">
	<td>订单编号 包含：</td>
	<td title="Order No."><input type="text" style="width:150px" name="orderno" value=""><span class="small gray">至少要填写四个字符以上,如 0730 月份和日期</span></td>
</tr>
<tr class="even">
	<td>订单客户 包含：</td>
	<td><input type="text" style="width:244px" name="customer" id="autoComplete" value="{$customer['title']}"> <span class="small gray">可使用客户名称中的关键字查找</span></td>
</tr>
<tr class="even">
	<td>订单业务人员 是：</td>
	<td>{$director}</td>
</tr>
<tr class="odd">
	<td>订单跟单人员 是：</td>
	<td>{$tracker}</td>
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
	<td title="Delivery Term">且 递送条款 是：</td>
	<td>{$deliveryterm}</td>
</tr>
<tr class="even">
	<td title="Shipment Date">且 交货日期：</td>
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
		<label for="t1"><input type="radio" id="t1" name="layout" value="list" checked>列表</label>
		<label for="t0"><input type="radio" id="t0" name="layout" value="grid">格子</label>
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


<script type="text/javascript">
$(function(){
	$("#autoComplete").inputAutoComplete({location:"/s.php?module=ajax&action=findCustomer",datatype:"json",selectcount:"scrollbar",scrollbarcount:9});
});
</script>
EOF;
		$this->kclass->page['onload'] .= 'dc.tabhover();$(\'#shipmentDate\').datepicker();var dates=$(\'#startDate,#endDate\').datepicker({onSelect:function(selectedDate){var option=this.id==\'startDate\'?\'minDate\':\'maxDate\',instance=$(this).data(\'datepicker\'),date=$.datepicker.parseDate(instance.settings.dateFormat || $.datepicker._defaults.dateFormat,selectedDate,instance.settings);dates.not(this).datepicker(\'option\',option,date);}});var dates2=$(\'#startCreateDate,#endCreateDate\').datepicker({onSelect:function(selectedDate){var option2=this.id==\'startCreateDate\'?\'minDate\':\'maxDate\',instance2=$(this).data(\'datepicker\'),date2=$.datepicker.parseDate(instance2.settings.dateFormat || $.datepicker._defaults.dateFormat,selectedDate,instance2.settings);dates2.not(this).datepicker(\'option\',option2,date2);}});';
		$this->kclass->page['title'] .= ' - 查找订单';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 查找订单', 'right' => '<a href="/s.php?module=order">返回列表</a>', 'body'=>$body));
	}
	//
	function chooseLanguage(){
	//print_r($this->kclass->input);
	$orderid = $this->kclass->input['orderid'];
	$actionid = $this->kclass->input['actionid'];
	if($actionid!=null){
		switch($actionid){
			case "pi":
				$action = "orderPI";
				break;
			case "po":
				$action = "orderPO";
				break;
			case "pk":
				$action = "packingPO";
				break;
			case "pl":
				$action = "packingPL";
				break;
			default:
					
		}
	}
	$body=<<<EOF
	<form action="/p.php?module=printer&action={$action}&orderid={$orderid}" target="_blank" method="post">
	<table width="800px">
	<thead>
	<tr>
	<th>选择语言</th>
	</tr>
	</thead>
	<tbody>
	<tr>
	<td align="center">
	<label for="c1"><input type="radio" name="chooselanguage" id="c1" value="c1">中文</label>
	<label for="c2"><input type="radio" name="chooselanguage" id="c2" value="c2" checked>英文</label>
	</td>
	</tr>
	<tr>
	<td align="center"><input type="submit" value="提交"/><input type="reset" value="重置"/></td>
	</tr>
	</tbody>
	</table>
	</form>
EOF;
     $this->kclass->page['title'].='-语言选择';
	$this->kclass->page['onload'] .= "dc.tabs({'id':'customer'})";
	$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.'-标准询价单','right'=>'<a href="/s.php?moudle=customer">返回列表</a>','body'=>$body));
}
	//
	function mmlist(){

		//如果ID存在，直接转向到View页面
		//print_r($this->kclass->input);
		$this->kclass->input['id']=intval($this->kclass->input['id']);
		if($this->kclass->input['id']>0){
			$order=$this->kclass->DB->queryFirst("SELECT orderid FROM `order` WHERE `order`.orderid='".$this->kclass->input['id']."'");
			if($order){
				$this->kclass->boinkIt('/s.php?module=order&action=view&orderid='.$order['orderid']);
			}
		}
		if(empty($this->kclass->input['show'])){
			$condition=' AND `order`.killed=0';
		}
		//print_r(strlen($this->kclass->input['orderno']));
		if(strlen($this->kclass->input['orderno'])>3){
			$orderview=$this->kclass->DB->queryFirst("SELECT orderid FROM `order` WHERE `order`.orderno='".$this->kclass->input['orderno']."'");
			$condition .= " AND INSTR(LCASE(`order`.orderno),'".(strtolower($this->kclass->input['orderno']))."')>0";
			$query['orderno']=$this->kclass->input['orderno'];
			
		}

		$this->kclass->input['customer']=trim($this->kclass->input['customer']);
		$this->kclass->input['shipmentDate']=trim($this->kclass->input['shipmentDate']);
		if(strlen($this->kclass->input['customer'])>3){
			$customers=$this->kclass->DB->query("SELECT customerid FROM `customer` WHERE INSTR(LCASE(`customer`.title),'".(strtolower($this->kclass->input['customer']))."')>0");
			if($this->kclass->DB->numRows()>0){
				while($customer=$this->kclass->DB->fetchArray($customers)){
					$customerid[]=$customer['customerid'];
				}
				$condition.=" AND `order`.customerid IN (".implode(',', $customerid).")";
				$query['customer']=$this->kclass->input['customer'];
			}
		}
		if($this->kclass->input['director']>0){
			$condition .= " AND `order`.director='".$this->kclass->input['director']."'";
			$query['director']=$this->kclass->input['director'];
		}
		if($this->kclass->input['tracker']>0){
			$condition .= " AND `order`.tracker='".$this->kclass->input['tracker']."'";
			$query['tracker']=$this->kclass->input['tracker'];
		}
		if($this->kclass->input['shipmethodid']>0){
			$condition .= " AND `order`.shipmethodid='".$this->kclass->input['shipmethodid']."'";
			$query['shipmethodid']=$this->kclass->input['shipmethodid'];
		}
		if($this->kclass->input['loading']>0){
			$condition .= " AND `order`.loading='".$this->kclass->input['loading']."'";
			$query['loading']=$this->kclass->input['loading'];
		}
		if($this->kclass->input['discharge']>0){
			$condition .= " AND `order`.discharge='".$this->kclass->input['discharge']."'";
			$query['discharge']=$this->kclass->input['discharge'];
		}
		if($this->kclass->input['paymenttermid']>0){
			$condition .= " AND `order`.paymenttermid='".$this->kclass->input['paymenttermid']."'";
			$query['paymenttermid']=$this->kclass->input['paymenttermid'];
		}
		if($this->kclass->input['deliverytermid']>0){
			$condition .= " AND `order`.deliverytermid='".$this->kclass->input['deliverytermid']."'";
			$query['deliverytermid']=$this->kclass->input['deliverytermid'];
		}
		if($this->kclass->input['shipmentDate']!=''){
			$year = substr($this->kclass->input['shipmentDate'],0,4);
			$month = substr($this->kclass->input['shipmentDate'],5,2);
			$day = substr($this->kclass->input['shipmentDate'],8,2);
			if (checkdate($month,$day,$year)) {
				$shipmentDate = $this->kclass->input['shipmentDate'] = mktime(0,0,0,$month,$day,$year);
			}
			if($this->kclass->input['shipmentDate']>0){
				switch($this->kclass->input['shipDateline']){
					case 'before':
						$shipDateline="<=";
						break;
					case 'equal':
						$shipDateline="=";
						break;
					case 'after':
						$shipDateline=">=";
						break;
				}
				$condition .= " AND `order`.shipmentDate".$shipDateline."'".$shipmentDate."'";
				$query['shipmentDate']=$this->kclass->input['shipmentDate'];
				$query['shipDateline']=$this->kclass->input['shipDateline'];
			}
		}

		if($this->kclass->input['startModDate']!=''){
			$year = substr($this->kclass->input['startModDate'],0,4);
			$month = substr($this->kclass->input['startModDate'],5,2);
			$day = substr($this->kclass->input['startModDate'],8,2);
			if (checkdate($month,$day,$year)) {
				$this->kclass->input['startModDate'] = mktime(0,0,0,$month,$day,$year);
			}
			if($this->kclass->input['startModDate']>0){
				$condition .= " AND `order`.modified >='".$this->kclass->input['startModDate']."'";
			}
		}

		if($this->kclass->input['endModDate']!=''){
			$year = substr($this->kclass->input['endModDate'],0,4);
			$month = substr($this->kclass->input['endModDate'],5,2);
			$day = substr($this->kclass->input['endModDate'],8,2);
			if (checkdate($month,$day,$year)) {
				$this->kclass->input['endModDate'] = mktime(0,0,0,$month,$day,$year);
			}
			if($this->kclass->input['endModDate']>0){
				$condition .= " AND `order`.modified <='".$this->kclass->input['endModDate']."'";
			}
		}

		if($this->kclass->input['startCreateDate']!=''){
			$year = substr($this->kclass->input['startCreateDate'],0,4);
			$month = substr($this->kclass->input['startCreateDate'],5,2);
			$day = substr($this->kclass->input['startCreateDate'],8,2);
			if (checkdate($month,$day,$year)) {
				$this->kclass->input['startCreateDate'] = mktime(0,0,0,$month,$day,$year);
			}
			if($this->kclass->input['startCreateDate']>0){
				$condition .= " AND `order`.created >='".$this->kclass->input['startCreateDate']."'";
			}
		}

		if($this->kclass->input['endCreateDate']!=''){
			$year = substr($this->kclass->input['endCreateDate'],0,4);
			$month = substr($this->kclass->input['endCreateDate'],5,2);
			$day = substr($this->kclass->input['endCreateDate'],8,2);
			if (checkdate($month,$day,$year)) {
				$this->kclass->input['endCreateDate'] = mktime(0,0,0,$month,$day,$year);
			}
			if($this->kclass->input['endCreateDate']>0){
				$condition .= " AND `order`.created <='".$this->kclass->input['endCreateDate']."'";
			}
		}

		if($this->kclass->input['orderby']!='')$query['orderby']=$this->kclass->input['orderby'];
		if($this->kclass->input['direction']!='')$query['direction']=$this->kclass->input['direction'];
		if($this->kclass->input['show']!='')$query['show']=$this->kclass->input['show'];
		if($this->kclass->input['layout']!=''){
			$query['layout']=$this->kclass->input['layout'];
		}else {
			$query['layout']=$this->kclass->input['layout'] ='list';
		}

		if($this->kclass->input['start']==''){
			$b['start']=0;
		}else{
			$b['start']=$this->kclass->input['start'];
		}
		if($this->kclass->input['perpage']=='')$b['perpage']=20;

		$orderby=$this->kclass->orderby(array('module'=>'order','direction'=>'desc','orderby'=>'order.created', 'default'=>'created', 'serial'=>array(array('title'=>'编号', 'field'=>'orderno'), array('title'=>'ID', 'field'=>'orderid','word'=>'id'), array('title'=>'修改时间', 'field'=>'modified'), array('title'=>'建立时间', 'field'=>'created'), array('title'=>'客户ID', 'field'=>'customerid')),'appendUrl'=>$query));
		if(is_array($query) AND count($query)>0)$queryPart='&'.http_build_query($query);
		$type=array('PI','PO');$item=array();
		foreach($type as $key => $val){
		$orders = $this->kclass->DB->query("
			SELECT `order`.orderid, `order`.orderno,`order`.ordertitle, `order`.customerid,`order`.status, `order`.supplierid, `order`.shipmentDate, `order`.cover, `order`.attachs, `order`.images, `order`.modified, `order`.created, `order`.killed,`order`.ifVerify,`order`.ifApprove,`order`.ifChooser,
				route.title AS routeTitle, route.caption AS routeCaption, `route`.typelist,
				customer.title AS customerTitle, shipmethod.title AS shipmethod,
				m.realname AS modifier, c.realname AS creator,t.realname AS tracker,d.realname AS director
			FROM `order`
			LEFT JOIN orderroute AS route ON (route.routeid=`order`.routeid)
			LEFT JOIN `customer` ON (customer.customerid=`order`.customerid)
			LEFT JOIN `shipmethod` ON (shipmethod.shipmethodid=`order`.shipmethodid)
			LEFT JOIN `member` AS m ON (m.userid=`order`.modifier)
			LEFT JOIN `member` AS c ON (c.userid=`order`.creator)
			LEFT JOIN `member` AS t ON (t.userid=`order`.tracker)
			LEFT JOIN `member` AS d ON (d.userid=`order`.director)
			WHERE `order`.type='".$val."' ".$condition."
			ORDER BY ".$orderby['sql']."
			LIMIT ".$b['start'].",".$b['perpage']."
		");
		if($sum=$this->kclass->DB->numRows()){
			if($this->kclass->input['layout']=='list'){
				$item[$val] = '<table class="hundred tablesorter mytable"><thead><th width="60">订单ID</th><th width="100">订单编号</th><th width="300">订单状态</th><th width="280">客户</th><th width="80">业务人员</th><th width="80">跟单人员</th><th width="80">递送方式</th><th width="80">递送日期</th><th width="120">创建日期</th><th width="120">修改日期</th><th width="60">创建者</th><th width="60">修改者</th><th width="36">操作</th></thead><tbody>';
			}else{
				$item[$val] = '<ul id="mmlist" class="mmlist clear">';
			}
			$i=1;
			while($order = $this->kclass->DB->fetchArray($orders)){
				$customerTitle = $this->kclass->parseConvertValue($order['customerTitle']);
				$customerTitle = $this->kclass->iif(strlen($customerTitle)>30, mb_substr($customerTitle, 0, 30, 'UTF-8').'...', $customerTitle);
				if($this->kclass->input['customer']!=''){
					$customerTitle = $this->kclass->highlight($customerTitle, $this->kclass->input['customer']);
				}
				$operator = $creator = '由 '.$customer['creator'].' 建于 '.date('y-m-d H:m', $customer['created']);
				if($customer['modifier']!=''){
					$modifier = $customer['modifier'].' 改于 '.date('Y-m-d H:m', $customer['modified']);
					$operator .= ', '.$modifier;
					$modifier = '由 '.$modifier;
				}else{
					$modifier = $creator;
				}

				if($order['killed']>0){
					$link = '<a href="/s.php?module=order&action=restore&orderid='.$order['orderid'].'&rt=list">恢复</a>';
				}else{
					$link = '<a href="/s.php?module=order&action=update&orderid='.$order['orderid'].'&rt=list">改</a> <a href="/s.php?module=order&action=kill&orderid='.$order['orderid'].'" target="_blank">删</a>';
				}
				$status='';
				if($order['ifVerify']==0){
					$status = '<span class="darkred">未审核　未审批</span>　（<a href="/s.php?module=order&action=verify&orderid='.$order['orderid'].'">审核</a>）';
				}elseif($order['ifVerify']==-1){
					$status = '<span class="red">审核未通过</span>　<span class="darkred">未审批</span>';
				}elseif($order['ifVerify']==1 AND $order['ifApprove']==0){
					$status = '<span class="green">审核通过</span>　<span class="darkred">未审批</span>　（<a href="/s.php?module=order&action=approve&orderid='.$order['orderid'].'">审批</a>）';
				}elseif($order['ifVerify']==1 AND $order['ifApprove']==-1){
					$status = '<span class="green">审核通过</span>　<span class="red">审批未通过</span>';
				}elseif($order['ifVerify']==1 AND $order['ifApprove']==1){
					$status = '<span class="green">审核通过　审批通过</span>';
				}
				if($val=='PO'){
					$status.=$this->kclass->iif($order['ifChooser']==1,'　<span class="green">已投产</span>','　<span class="darkred">未投产</span>');
					$outbound=$this->kclass->DB->queryFirst("
						SELECT outboundid
						FROM outbound
						WHERE killed=0 AND ifComplete=2 AND orderid='".$order['orderid']."'
					");
					$status.=$this->kclass->iif($outbound=='','　<span class="darkred">未出库</span>','　<span class="green">已出库</span>');
				}
				if($this->kclass->input['layout']=='list'){//表格
					$item[$val] .='<tr class="'.$this->kclass->rotateLine().'">
						<!--<td style="vertical-align:middle;" height="60">'.$order['routeTitle'].'</td>-->
						<td style="vertical-align:middle;">'.$i.'</td>
						<td style="vertical-align:middle;"><a class="tip" href="/s.php?module=order&action=view&orderid='.$order['orderid'].'" title="'.$order['ordertitle'].'">'.$order['orderno'].'</a></td>
						<td style="vertical-align:middle;">'.$status.'</td>
						<td style="vertical-align:middle;"><a href="/s.php?module=customer&action=view&customerid='.$customer['customerid'].'">'.$customerTitle.'</a></td>
						<td style="vertical-align:middle;">'. $order['director'].'</td>
						<td style="vertical-align:middle;">'. $order['tracker'].'</td>
						<td style="vertical-align:middle;">'. $order['shipmethod'].'</td>
						<td style="vertical-align:middle;" class="darkred">'.date('Y-m-d', $order['shipmentDate']).'</td>
						<td style="vertical-align:middle;">'.date('Y-m-d H:i', $order['created']).'</td>
						<td style="vertical-align:middle;">'.date('Y-m-d H:i', $order['modified']).'</td>
						<td style="vertical-align:middle;">'.$order['creator'].'</td>
						<td style="vertical-align:middle;">'.$order['modifier'].'</td>
						<td style="vertical-align:middle;" align="center">'.$link.'</td>
					</tr>';
				}else{//方块列表
					$item[$val] .= '<li title="由 '.$order['creator'].' 建于 '.$created.$this->kclass->iif($order['modifier']!='', '，'.$order['modifier'].' 改于 '.$modified, '').'"'.$this->kclass->iif($i%4==0, ' class="end"', '').'>
					<div class="mmlistt">
						<span class="right normal">';
					if($order['killed']>0){
						$item[$val] .= '<a href="/s.php?module=order&action=restore&orderid='.$order['orderid'].'&rt=list">恢复</a>';
					}else{
						$item[$val] .= '<a href="/s.php?module=order&action=kill&orderid='.$order['orderid'].'&rt=list" onclick="return confirm(\'你确定要删除这个样品 '.$order['title'].' 吗？\');">删</a> <a href="/s.php?module=order&action=update&orderid='.$order['orderid'].'&rt=list">改PI</a>';
					}
					$item[$val] .= '</span>
						<span class="small">'.$order['attr'].'</span> <a href="/s.php?module=order&action=view&orderid='.$order['orderid'].'">'.$order['orderno'].'</a><span class="small">('.$order['typelist'].')</span><br><span class="small gray right">'.date('Y-m-d', $order['created']).'</span>'.$this->kclass->iif($order['attachs']>0, ' <span class="attachFile" title="有'.$order['attachs'].'个附件。"></span> ', '').$this->kclass->iif($order['images']>0, ' <span class="attachImage" title="有'.$order['images'].'个图片。"></span> ', '').'<span class="small gray">交货日期：</span>'.date('Y-m-d', $order['shipmentDate']).'</div>
					<div class="mmlistb">
						<div title="'.$order['routeCaption'].'"><span class="small gray">类型：</span>'.$order['routeTitle'].'</div>
						<div title="'.$order['customerTitle'].'"><span class="small gray">客户：</span><a href="/s.php?module=customer&action=view&customerid='.$order['customerid'].'" target="_blank">'.$order['customerTitle'].'</a></div>
						'.$this->kclass->iif($order['cover']!='', '<div class="center"><img src="'.$order['cover'].'"></div>', '').'
						<div class="small clear"><span class="gray small">业务人员：<span class="darkred">'.$order['creator'].'</span></span>&nbsp;&nbsp;&nbsp;<span class="gray small">跟单人员：<span class="darkred">'.$order['tracker'].'</span></span></div>
						<div class="small clear"><span class="right" title="由 '.$order['creator'].' 建于 '.date('y-m-d H:m', $order['created']).'">'.$this->kclass->iif($order['modifier']!='', '由 '.$order['modifier'].' 改于 ', '建于 ').date('Y-m-d H:m', $order['modified']).'</span></div>
					</div>
					</li>';
				}
				$i++;
			}
			if($this->kclass->input['layout']=='list'){
				$item[$val] .= '</tbody></table>';
			}else{
				$item[$val] .= '</ul>';
			}
		}else{
			$item[$val] = '暂无相关记录。';
		}
	}
		
		$body=<<<EOF
<dl id="orderlist" class="tabs" style="display: block;">
<dt>PI(销售订单)</dt>
<dt>PO(采购订单)</dt>
<dd>{$item['PI']}</dd>
<dd>{$item['PO']}</dd>
</dl>
EOF;
		$pcount=$this->kclass->DB->queryFirst("SELECT COUNT(orderid) AS count FROM `order` WHERE `order`.type='PI' ".$condition." ");
		$body.='<div class="clear"><span class="gray small left">(共<span class="darkred">'.$pcount['count'].'</span>组记录)</span></div>';
		$prestart=$b['start']-$b['perpage'];
		if($prestart>=0)$pre='<a href="/s.php?module=order&action=list&orderby='.$query['orderby'].'&layout='.$query['layout'].'&show='.$query['show'].'&direction='.$query['direction'].'&start='.$prestart.'"><-前一页</a>　';
		$aftstart=$b['start']+$b['perpage'];
		if($aftstart<$pcount['count'])$aft='　<a href="/s.php?module=order&action=list&orderby='.$query['orderby'].'&layout='.$query['layout'].'&show='.$query['show'].'&direction='.$query['direction'].'&start='.$aftstart.'">后一页-></a>';
		$body.='<div class="clear center"><span class=" middle bold">'.$pre.$aft.'</span></div>';

		if($this->kclass->input['layout']=='list'){
			$layoutLink='列表 <a href="/s.php?module=order&action=list&layout=grid'.str_replace('&layout=list', '', $queryPart).'">格子</a>';
		}else{
			$layoutLink='<a href="/s.php?module=order&action=list&layout=list'.str_replace('&layout=grid', '', $queryPart).'">列表</a> 格子';
		}
		if($this->kclass->input['show']=='all'){
			$showLink='<a href="/s.php?module=order&action=list'.str_replace('&show=all', '', $queryPart).'">默认</a> 所有<span class="small gray">(包括删除)</span>';
		}else{
			$showLink='默认 <a href="/s.php?module=order&action=list&show=all'.str_replace('&show=all', '', $queryPart).'">所有<span class="small gray">(包括删除)</span></a>';
		}

		$this->kclass->page['onload'] .= 'dc.listhover();dc.tips();dc.tabs({\'id\':\'orderlist\'});dc.tabhover();$(\'.mytable\').fixedtableheader();$(\'.tablesorter\').tablesorter( {sortList: [[0,0]], headers: { 6: { sorter: false},12: { sorter: false} } } );';
		$this->kclass->page['title'] .= ' - 列表';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 列表<span class="tiny">('.$sum.')</span>', 'right' => $this->right.'　|　<span class="small">排序：</span>'.$orderby['link'].'|　<span class="small">显示：</span>'.$showLink.'　|　<span class="small">布局：</span>'.$layoutLink.'　|　<a href="/s.php?module=order&action=add">新建</a>　<a href="/s.php?module=order&action=addOld">新建旧订单</a>', 'body'=>$body));
	}
}
?>