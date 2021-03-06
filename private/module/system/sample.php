<?php
class sample{
	function autoRun(){
		$this->kclass->page['title'] = '样品单';
		$this->baseurl = '<a href="/s.php">首页</a> - <a href="/s.php?module=sample">样品单</a>';
		switch($this->kclass->input['action']){
			case 'list':
				$this->mmlist();
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
			case 'kill':
				$this->kill();
			break;
			case 'revival':
				$this->revival();
			break;
			case 'restore':
				$this->restore();
			break;
			case 'sampleCopy':
				$this->sampleCopy();
			break;
			case 'insertCopy':
				$this->insertCopy();
			break;
			case 'addEvent':
				$this->addEvent();
			break;
			case 'insertEvent':
				$this->insertEvent();
			break;
			case 'updateEvent':
				$this->updateEvent();
			break;
			case 'doupdateEvent':
				$this->doupdateEvent();
			break;
			case 'removeEvent':
				$this->removeEvent();
			break;
			case 'killEvent':
				$this->killEvent();
			break;
			case 'find';
				$this->find();
				break;
			case 'revise':
				$this->revise();
				break;
			case 'verify':
				$this->verify();
				break;
			case 'doverify':
				$this->doverify();
				break;
			case 'approve':
				$this->approve();
				break;
			case 'doapprove':
				$this->doapprove();
				break;
			case 'according':
				$this->according();
				break;
			case 'accordinglist':
				$this->accordinglist();
				break;
			default:
				$this->mmlist();
		}
	}

	/**
	 * 查看样品单的修改版本信息
	 * @author creator mayinghao 2013-07-17 8:15
	 * @access public
	 * @param none 没有参数
	 * @return void 没有返回信息
	 * @throws none 没有异常
	 */
	function revise(){
		if($this->kclass->input['reviseid']>0){
			$revise = $this->kclass->DB->queryFirst("
				SELECT `revise`.ptext,`revise`.version
				FROM `revise`
				WHERE reviseid='".$this->kclass->input['reviseid']."'
			");
			if($revise){
				$this->kclass->page['onload'] .= "dc.tabs({'id':'order'});dc.tabhover()";
				$this->kclass->page['title'] .= ' - 样品单版本R'.$revise['version'];
				$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl.' - 样品单版本信息　版本R'.$revise['version'], 'right' => '', 'body'=>$revise['ptext']));
			}else{
				$this->kclass->messager(array(
						'title' => '查看样品单历史版本',
						'text' => '没有此历史版本!',
						'url' => '/s.php?module=sample&action=view&orderid='.$this->kclass->input['sampleid'],
						'sec' => 5
				));
			}
		}
	}

	/**
	 * 返回样品单的HTML信息
	 * @author creator mayinghao 2013-07-17 8:15
	 * @access public
	 * @param none 没有参数
	 * @return string 返回HTML字符串
	 * @throws none 没有异常
	 */
	function _getSampleHtml(){
	if(!$this->kclass->input['sampleid'] > 0)$this->kclass->boinkIt('/s.php?module=sample');
		$sample = $this->kclass->DB->queryFirst("
			SELECT `sample`.*,
				attr.title AS attr,
				currency.title AS currencyTitle, currency.symbol AS currencySymbol, currency.remark AS currencyRemark,
				shipmethod.title AS shipmethod,
				m.username AS mname, c.username AS cname, d.username AS director, t.username AS tracker
			FROM `sample`
			LEFT JOIN `attr` ON (attr.attrid=sample.attrid)
			LEFT JOIN `currency` ON (currency.currencyid=sample.currencyid)
			LEFT JOIN `shipmethod` ON (shipmethod.shipmethodid=sample.shipmethodid)
			LEFT JOIN `user` AS m ON (m.userid=sample.modifier)
			LEFT JOIN `user` AS c ON (c.userid=sample.creator)
			LEFT JOIN `user` AS d ON (d.userid=sample.director)
			LEFT JOIN `user` AS t ON (t.userid=sample.tracker)
			WHERE `sample`.sampleid='".$this->kclass->input['sampleid']."'
		");
		if(!$sample)	$this->kclass->boinkIt('/s.php?module=sample');
		if($sample['typeid']!=''){
			$stypes = $this->kclass->DB->query("SELECT typeid, title FROM `sampletype` WHERE typeid IN (".$sample['typeid'].")");
			if($this->kclass->DB->numRows()){
				while($stype = $this->kclass->DB->fetchArray($stypes)){
					$sampleType .= '<span title="('.$stype['entitle'].') '.$stype['remark'].'" style="padding-right:12px">'.$stype['title'].'</span>';
				}
			}
		}
		if($sample['customerid']>0){
			$customer = $this->kclass->relatedCustomer(array('id'=>$sample['customerid']));
		}else{
			$customer['link']=$sample['customerTitle'];
		}
		if($sample['supplierid']>0){
			$supplier = $this->kclass->relatedSupplier(array('id'=>$sample['supplierid']));
		}
		if($sample['productid']>0 AND $sample['productid']!=''){
			$product = $this->kclass->relatedProduct(array('id'=>$sample['productid']));
		}else{
			$product=array('off'=>' disabled', 'num'=>'', 'panel'=>'');
		}
		if($sample['modifier']>0){
			$modified = '，'.$sample['mname'].' 于 '.date('y-m-d H:i:s', $sample['modified']).' 最后修改';
		}
		if($sample['deliveryDate']>0){
			$deliveryDate=' <span class="darkred">'.date('Y-m-d', $sample['deliveryDate']).'</span>';
		}
		if($sample['deliveryFee']>0){
			$deliveryFee=' (<span class="small">'.$sample['currency'].'</span>'.$sample['deliveryFee'].')';
		}
		$created = date('y-m-d H:i:s', $sample['created']);

		$total=0;
		//$item = $this->kclass->relatedSampleItem(array('sampleid'=>$sample['sampleid']));

		$items = $this->kclass->relatedSampleProductItem(array('sampleid'=>$sample['sampleid']));
		$total =$items['total'];

		$events = $this->kclass->DB->query("
			SELECT event.*,
				dc.title AS deliveryCurrency,
				sc.title AS sampleCurrency,
				shipmethod.title AS shipmethod
			FROM sampleevent AS `event`
			LEFT JOIN currency AS dc ON (dc.currencyid=`event`.deliveryCurrency)
			LEFT JOIN currency AS sc ON (sc.currencyid=`event`.sampleCurrency)
			LEFT JOIN shipmethod ON (shipmethod.shipmethodid=`event`.shipmethodid)
			WHERE `event`.killed=0 AND `event`.sampleid='".$sample['sampleid']."'
			ORDER BY `event`.modified DESC
		");
		if($this->kclass->DB->numRows()){
			$eventtbl = '<table class="hundred"><thead><tr><th colspan="7">收发与费用</th></tr><tr><th>收发类型</th><th>收发数量</th><th>收发日期</th><th>递送方式</th><th>递送费用</th><th>样品费用</th><th>操作</th></tr></thead><tbody>';
			$this->kclass->tbline+=1;
			while($event = $this->kclass->DB->fetchArray($events)){
				$eventtbl.='<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').'"><td'.$this->kclass->iif($event['direction']=='sent', ' class="green">发出', ' class="red">收到').'</td><td'.$this->kclass->iif($event['totalQuantity']=='all', '>全部', ' class="bold">部分').'</td><td>'.date('Y-m-d', $event['deliveryDate']).'</td><td>'.$event['shipmethod'].'</td><td>'.$event['deliveryCurrency'].$event['deliveryFee'].$this->kclass->iif($event['deliveryCaption']!='', ' ('.$event['deliveryCaption'].')', '').'</td><td>'.$event['sampleCurrency'].$event['sampleFee'].$this->kclass->iif($event['sampleCaption']!='', ' ('.$event['sampleCaption'].')', '').'</td><td><a href="/s.php?module=sample&action=removeEvent&eventid='.$event['eventid'].'" onclick="return confirm(\'你确定要删除这条收发与费用记录吗？(删除不可恢复！)\')">删</a> <a href="/s.php?module=sample&action=updateEvent&eventid='.$event['eventid'].'">改</a></td></tr>';
			}
			$eventtbl .= '</tbody></table>';
		}else{
			$event['off']=' disabled';
		}
		$fee = $this->kclass->relatedFee(array('sampleid'=>$sample['sampleid']));
		$total += $fee['total'];
		$discount=$this->kclass->relatedDiscount(array('module'=>'order','mid'=>$order['orderid'],'total'=>$total));
		$total += $discount['total'];
		$total = number_format($total, 2);
		$revise['arr']=serialize(array_merge($order,$item['array'],$fee['array'],$discount['array']));
		$revise['html']=<<<EOF
<div class="bold darkred big" style="clear:both;">{$sample['title']}<span class="small">({$sample['sampleno']})</span>　　　<span class="small gray">　　　业务人员：<span class="darkred bold">{$sample['director']}</span>　　　跟单人员：<span class="darkred bold">{$sample['tracker']}</span>　　　　由 {$sample['cname']} 于 {$created} 建立{$modified}。</span></div>
<table cellspacing="10" width="100%">
<tr class="odd">
	<td width="80">样品类型：</td>
	<td class="middle">{$sampleType}</td>
	<td width="80">样品标题：</td>
	<td class="middle">{$sample['title']}</td>
</tr>
<tr class="even">
	<td valign="top">样品所属：</td>
	<td class="middle">{$sample['attr']}</td>
	<td valign="top">客　　户：</td>
	<td class="middle">{$customer['link']}</td>
</tr>
<tr class="odd">
	<td>供 应 商：</td>
	<td class="middle">{$supplier['link']}</td>
	<td>联系人员：</td>
	<td class="middle">{$sample['toMessrs']}</td>
</tr>
<tr class="odd">
	<td>递送方式：</td>
	<td class="middle">{$sample['shipmethod']}{$deliveryFee}{$deliveryDate}</td>
	<td>样品费用：</td>
	<td class="middle">{$sample['currencyTitle']} {$sample['currencySymbol']}{$sample['amount']}</td>
</tr>
<tr class="even">
	<td valign="top">样品描述：</td>
	<td class="middle">{$sample['description']}</td>
	<td valign="top">备　　注：</td>
	<td>{$sample['remark']}</td>
</tr>
</table>
<table class="hundred">
<thead>
<tr>
	<th colspan="12">样品明细表 (Item Information)</th>
</tr>
</thead>
<tbody class="small">
{$items['tr']}
</tbody>
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
{$discounttr}
</tbody>
</table>
<table class="hundred">
<tbody>
<tr><td align="right">总计(Total Amount)：<span class="middle bold darkred">{$sample['currencyTitle']} {$sample['currencySymbol']}{$total}</span></td></tr>
</tbody>
</table>
{$eventtbl}
EOF;
		return $revise;
	}
//
function sampleCopy(){
$sampleno=$this->kclass->id(array('sample'=>TIMENOW));
$sample = $this->kclass->DB->queryFirst("
			SELECT `sample`.*,
					customer.title AS customerTitle, customer.cntitle AS customerCntitle
			FROM `sample`
			LEFT JOIN customer ON (customer.customerid=`sample`.customerid)
			WHERE `sample`.killed=0 AND `sample`.`sampleid`='".$this->kclass->input['sampleid']."'
		");
		//$sample = $this->kclass->DB->queryFirst("SELECT * FROM `sample` WHERE `sampleid`='".$this->kclass->input['sampleid']."'")
		if($this->kclass->input['sampleid']<=0 OR !$sample){
			$this->kclass->boinkIt('/s.php?module=sample');
		}

		$this->kclass->br2nl=1;
		$sample['description']=$this->kclass->parseConvertValue($sample['description']);
		$sample['remark']=$this->kclass->parseConvertValue($sample['remark']);
		$sampletype = $this->kclass->chooserSampleType(array('name'=>'typeid', 'width'=>350, 'selectedid'=>$sample['typeid']));
		$attr = $this->kclass->chooserAttr(array('name'=>'attrid','module'=>'product','selectedid'=>$sample['attrid']));
		$supplier = $this->kclass->chooserSupplierSelect(array('name'=>'supplierid', 'hasBlank'=>1, 'width'=>350, 'selectedid'=>$sample['supplierid']));
		$customer=$this->kclass->chooserCustomer(array('hName'=>'customerid','selectedid'=>$sample['customerid'],'value'=>$sample['customerTitle'],'name'=>'customer','width'=>450, 'onchange'=>'customerStr'));
		$currency = $this->kclass->chooserCurrency(array('name'=>'currencyid', 'width'=>100, 'hasBlank'=>1, 'selectedid'=>$sample['currencyid']));
		$shipmethod = $this->kclass->chooserShipmethod(array('name'=>'shipmethodid', 'width'=>350, 'hasBlank'=>1, 'selectedid'=>$sample['shipmethodid']));
		if(strpos($sample['title'],'-')>0){
			$customerStr = substr($sample['title'],strpos($sample['title'],'-'),(strpos($sample['title'],'+')-12));
		}else{
			$customerStr = '';
		}
		if(strpos($sample['title'],'+')>0){
			$productStr = substr($sample['title'],strpos($sample['title'],'+'));
		}else{
			$productStr = '';
		}
		// 审核或者审批后修改  修改原因
		if($sample['ifVerify']!=0 OR $sample['ifApprove']!=0){
			$updateReason='<tr class="odd"><td>修改原因：</td><td><textarea name="reviseRemark" style="width:350px;height:70px"></textarea></td><td></td><td></td></tr>';
		}
		// item
		$items = $this->kclass->DB->query("
			SELECT item.*,
				p.productid,p.title,p.entitle
			FROM `item` 
			LEFT JOIN `product` AS p ON (p.productid=`item`.productid)
			WHERE `item`.module='sample' AND `item`.mid='".$sample['sampleid']."' AND `item`.killed =0
			ORDER BY itemid ASC
		");
		$k=1;
		$n=1;
		if($this->kclass->DB->numRows()){
			$flag=0;
			while($item = $this->kclass->DB->fetchArray($items)){
				if($item['productitemid']>0){
					if($n==1){
						$itemtr .= '<tr><td width="100">样品名称<span class="red bold">*</span></td><td  width="50">说明</td><td  width="100">品牌</td><td  width="50">包装</td><td  width="45">折扣%</td><td width="30">数量<span class="red bold">*</span></td><td width="80">单位<span class="red bold">*</span></td><td width="50">单价<span class="red bold">*</span></td><td width="30">删</td></tr>';
					}
					$seletStr = '<input type="hidden" id="tditemProductid'.$n.'input" value="'.$item['entitle'].'" />';
					$itemtr .= '<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').'">
					<td><span id="tditemProductid'.$n.'">'.$seletStr.'</span><input type="hidden" name="itemProId['.$n.']" value="'.$item['itemid'].'">'.$this->kclass->chooserProductItem(array('name'=>'itemProductid['.$n.']','selectName'=>'itemProductid'.$n, 'selectedid'=>$item['productitemid'], 'hasBlank'=>1, 'width'=>500)).'</td>
					<td><input type="text" name="itemProductCaption['.$n.']" value="'.$item['caption'].'" size="50"></td>
					<td>'.$this->kclass->chooserBrand(array('name'=>'itemProductBrandid['.$n.']', 'hasBlank'=>1, 'width'=>100, 'selectedid'=>$item['brandid'])).'</td>
					<td>'.$this->kclass->chooserPacking(array('name'=>'itemProductPackingid['.$n.']', 'hasBlank'=>1, 'width'=>100, 'selectedid'=>$item['packingid'])).'</td>
					<td><input type="text" name="itemProductDiscount['.$n.']" value="'.$item['discount'].'" size="3"></td>
					<td><input type="text" name="itemProductQuantity['.$n.']" value="'.$item['quantity'].'" size="3" onblur="dc.returnProductStr('.$n.',this.value)"></td>
					<td>'.$this->kclass->chooserUnit(array('name'=>'itemProductUnitid['.$n.']', 'hasBlank'=>1, 'width'=>80, 'selectedid'=>$item['unitid'])).'</td>
					<td><input type="text" name="itemProductPrice['.$n.']" value="'.$item['price'].'" size="5"></td>
					<td><input type="checkbox" name="itemProductKill['.$n.']" value="'.$item['itemid'].'"></td>
					</tr>';
					$n++;
				}else{
					if($k==1){
						$parts .= '<tr><td width="100">配件名称<span class="red bold">*</span></td><td  width="50">说明</td><td  width="100">品牌</td><td  width="50">包装</td><td  width="45">折扣%</td><td width="30">数量<span class="red bold">*</span></td><td width="80">单位<span class="red bold">*</span></td><td width="50">单价<span class="red bold">*</span></td><td width="30">删</td></tr>';
					}
					$parts .= '<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').'">
					<td><input type="hidden" name="itemParId['.$k.']" value="'.$item['itemid'].'">'.$this->kclass->chooserParts(array('name'=>'itemPartsid['.$k.']','selectedid'=>$item['productid'], 'hasBlank'=>1, 'width'=>500)).'</td>
					<td><input type="text" name="itemPartsCaption['.$k.']" value="'.$item['caption'].'" size="50"></td>
					<td>'.$this->kclass->chooserBrand(array('name'=>'itemPartsBrandid['.$k.']', 'hasBlank'=>1, 'width'=>100, 'selectedid'=>$item['brandid'])).'</td>
					<td>'.$this->kclass->chooserPacking(array('name'=>'itemPartsPackingid['.$k.']', 'hasBlank'=>1, 'width'=>100, 'selectedid'=>$item['packingid'])).'</td>
					<td><input type="text" name="itemPartsDiscount['.$k.']" value="'.$item['discount'].'" size="3"></td>
					<td><input type="text" name="itemPartsQuantity['.$k.']" value="'.$item['quantity'].'" size="3"></td>
					<td>'.$this->kclass->chooserUnit(array('name'=>'itemPartsUnitid['.$k.']', 'hasBlank'=>1, 'width'=>80, 'selectedid'=>$item['unitid'])).'</td>
					<td><input type="text" name="itemPartsPrice['.$k.']" value="'.$item['price'].'" size="5"></td>
					<td><input type="checkbox" name="itemPartsKill['.$k.']" value="'.$item['itemid'].'"></td>
					</tr>';
					$k++;
				}
			}
		}
		$itemtrs .= '<tr><td width="100">样品名称<span class="red bold">*</span></td><td  width="50">说明</td><td  width="100">品牌</td><td  width="50">包装</td><td  width="45">折扣%</td><td width="30">数量<span class="red bold">*</span></td><td width="80">单位<span class="red bold">*</span></td><td width="50">单价<span class="red bold">*</span></td><td width="30">删</td></tr>';
		for($i = $n; $i < ($n+5); $i++){
			$itemtrs .= '<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').'">
			<td><span id="tditemProductid'.$i.'"></span>'.$this->kclass->chooserProductItem(array('name'=>'itemProductid['.$i.']','selectName'=>'itemProductid'.$i, 'hasBlank'=>1, 'width'=>500)).'</td>
			<td><input type="text" name="itemProductCaption['.$i.']" value="'.$item['caption'].'" size="50"></td>
			<td>'.$this->kclass->chooserBrand(array('name'=>'itemProductBrandid['.$i.']', 'hasBlank'=>1, 'width'=>100, 'selectedid'=>1)).'</td>
			<td>'.$this->kclass->chooserPacking(array('name'=>'itemProductPackingid['.$i.']', 'hasBlank'=>1, 'width'=>100, 'selectedid'=>6)).'</td>
			<td><input type="text" name="itemProductDiscount['.$i.']" value="100" size="3"></td>
			<td><input type="text" name="itemProductQuantity['.$i.']" value="'.$item['quantity'].'" size="3" onblur="dc.returnProductStr('.$i.',this.value)"></td>
			<td>'.$this->kclass->chooserUnit(array('name'=>'itemProductUnitid['.$i.']', 'hasBlank'=>1, 'width'=>80, 'selectedid'=>5)).'</td>
			<td><input type="text" name="itemProductPrice['.$i.']" value="'.$item['price'].'" size="5"></td>
			<td></td></tr>';
		}

		$partStr .= '<tr><td width="100">配件名称<span class="red bold">*</span></td><td  width="50">说明</td><td  width="100">品牌</td><td  width="50">包装</td><td  width="45">折扣%</td><td width="30">数量<span class="red bold">*</span></td><td width="80">单位<span class="red bold">*</span></td><td width="50">单价<span class="red bold">*</span></td><td width="30">删</td></tr>';
		for($i = $k; $i < ($k+5); $i++){
			$partStr .= '<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').'">
			<td>'.$this->kclass->chooserParts(array('name'=>'itemPartsid['.$i.']','hasBlank'=>1, 'width'=>500)).'</td>
			<td><input type="text" name="itemPartsCaption['.$i.']" value="'.$item['caption'].'" size="50"></td>
			<td>'.$this->kclass->chooserBrand(array('name'=>'itemPartsBrandid['.$i.']', 'hasBlank'=>1, 'width'=>100, 'selectedid'=>1)).'</td>
			<td>'.$this->kclass->chooserPacking(array('name'=>'itemPartsPackingid['.$i.']', 'hasBlank'=>1, 'width'=>100, 'selectedid'=>6)).'</td>
			<td><input type="text" name="itemPartsDiscount['.$i.']" value="100" size="3"></td>
			<td><input type="text" name="itemPartsQuantity['.$i.']" value="'.$item['quantity'].'" size="3"></td>
			<td>'.$this->kclass->chooserUnit(array('name'=>'itemPartsUnitid['.$i.']', 'hasBlank'=>1, 'width'=>80, 'selectedid'=>5)).'</td>
			<td><input type="text" name="itemPartsPrice['.$i.']" value="'.$item['price'].'" size="5"></td>
			<td></td></tr>';
		}

		$director = $this->kclass->chooserTracker(array('name'=>'director','condition'=>'positionid=7','width'=>80,'selectedid'=>$sample['director']));
		$tracker = $this->kclass->chooserTracker(array('name'=>'tracker','condition'=>'positionid=7','width'=>80,'selectedid'=>$sample['tracker']));

		$fee=$this->kclass->formFee(array('module'=>'sample','mid'=>$sample['sampleid']));
		$discount=$this->kclass->formDiscount(array('module'=>'sample','mid'=>$sample['sampleid']));

		$upload = $this->kclass->upload(array('module'=>'sample', 'mid'=>$sample['sampleid']));
		if($sample['deliveryDate']>0){
			$deliveryDate = date('Y-m-d', $sample['deliveryDate']);
		}
$body = <<<EOF
<form action="/s.php?module=sample&action=insertCopy" name="sample" method="post">
<input type="hidden" name="module" value="sample">
<input type="hidden" name="action" value="insertCopy">
<input type="hidden" name="sampleid" value="{$sample['sampleid']}">
<input type="hidden" name="rt" value="{$this->kclass->input['rt']}">
<table class="hundred">
<thead>
<tr>
	<th colspan="4">原样品单：<span class="darkred" id="yearStr">{$sample['sampleno']}</span><span class="darkred" id="customerStr">{$customerStr}</span><span class="darkred" id="productStr">{$productStr}</span></th>
</tr>
</thead>
<tbody>
<tr class="even">
	<td width="100">样品编号：<span class="red bold">*</span></td>
	<td><input type="text" name="sampleno" value="{$sampleno}"  class="dark middle bold" /><span class="red small"> 此为预估编号</span>　　编号生成方式：<input type="radio" name="noType" value="1" >手动<input type="radio" name="noType" value="0" checked>自动<input type="hidden" name="title" id="orderTitle" value="{$sampleno}" style="width:350px;"></td>
	<td width="100">样品所属：<span class="red bold">*</span></td>
	<td>{$attr}</td>
</tr>
<tr class="odd">
	<td>联 系 人：<span class="red bold">*</span></td>
	<td><input type="text" style="width:350px" name="toMessrs" value="{$sample['toMessrs']}"></td>
	<td>样品类型：<span class="red bold">*</span></td>
	<td>{$sampletype}</td>
</tr>
<tr class="even">
	<td>客　　户：<span class="red bold">*</span></td>
	<td>{$customer}</td>
	<td>供 应 商：<span class="red bold">*</span></td>
	<td>{$supplier}</td>
</tr>
<tr class="odd">
	<td>递送方式：</td>
	<td>{$shipmethod}</td>
	<td>运费预估：</td>
	<td><input type="text" style="width:170px" name="deliveryFee" value="{$sample['deliveryFee']}">　业务人员：{$director}</td>
</tr>
<tr class="even">
	<td>递送日期：<span class="red bold">*</span></td>
	<td><input type="text" style="width:350px" id="deliveryDate" name="deliveryDate" value="{$deliveryDate}"></td>
	<td>样品费用：<span class="red bold">*</span></td>
	<td>{$currency}<input type="text" style="width:70px" id="total" name="amount" value="{$sample['amount']}">　跟单人员：{$tracker}</td>
</tr>
<tr class="odd">
	<td valign="top">样品描述：</td>
	<td><textarea name="description" style="width:350px;height:70px">{$sample['description']}</textarea></td>
	<td valign="top">备　　注：</td>
	<td><textarea name="remark" style="width:350px;height:70px">{$sample['remark']}</textarea></td>
</tr>
{$updateReason}
</tbody>
</table>
<table class="hundred">
<thead>
<tr>
	<th colspan="10">样品明细表</th>
</tr>
</thead>
{$itemtr}
{$parts}
{$itemtrs}
{$partStr}
<tr>
	<td colspan="10" class="gray small">注：①如行数不够，请保存后再进入修改页面。②至少有“名称”、“数量”、“单位”三个项目，该明细才会被保存。</td>
</tr>
</tbody>
</table>
{$fee}
{$discount}
{$upload}
<table class="hundred">
<tbody>
<tr class="odd">
	<td colspan="4" align="center">
		<input type="submit" value="  保存  " accesskey="s">
		<input type="reset" value="  复位  ">
	</td>
</tr>
<tr class="even">
	<td class="small gray">
	注意事项：<ul>
	<li>英文地址：请严格按英文的格式进行(会影响订单中打印的地址)，基本与中文地址相反的顺序。</li>
	<li>未有列项的内容，写入“备注”中，长度上限3万中文字。</li>
	</ul>
	</td>
</tr>
</tbody>
</table>
</form>
EOF;
		$this->kclass->page['title'] .= ' - 复制样品单 - '.$sample['title'];
		$this->kclass->page['onload'] .= 'dc.tabhover();$(\'#deliveryDate\').datepicker()';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 复制样品单', 'right' => '<a href="/s.php?module=sample">返回列表</a>', 'body'=>$body));
}
//
function insertCopy(){
print_r($this->kclass->input);
if($this->kclass->input['requestMethod'] != 'post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			if($this->kclass->input['m']=='old'){
				$sampleno=$this->kclass->input['sampleno'];
			}else{
				if($this->kclass->input['noType']==0){
					$sampleno=$this->kclass->id(array('sample'=>TIMENOW));
				}elseif($this->kclass->input['noType']==1){
					$sampleno=$this->kclass->input['sampleno'];
				}
			}
			$sample=$this->kclass->DB->queryFirst("SELECT sampleid FROM sample WHERE sampleno='".$sampleno."' LIMIT 0,1");
			if($sample['sampleid']){
				$e='<li>编号<b>'.$sampleno.'</b> 在样品单中 已存在</li>';
			}
			if($this->kclass->input['title'] == ''){
				$e = '<li>请填写样品单的 标题。</li>';
			}
			if(count($this->kclass->input['typeid']) == 0){
				$e .= '<li>请填写样品单的 样品类型。</li>';
			}
			if($this->kclass->input['attrid'] < 1){
				$e .= '<li>请填写样品单的 样品所属。</li>';
			}
			if(!$this->kclass->input['supplierid'] > 0){
				$e .= '<li>请选择样品单的所属 供应商。</li>';
			}
			if($this->kclass->input['amount'] < 0){
				$e .= '<li>请选择样品单的 样品费用。</li>';
			}
			$j1=count($this->kclass->input['itemProductid']);

		}

		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '新建样品单',
				'text' => '您在新建样品单的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
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
		$deliveryDate=0;
		if(strlen($this->kclass->input['deliveryDate'])==10){
			$dd = explode('-', $this->kclass->input['deliveryDate']);
			$deliveryDate = mktime(0,0,0,$dd[1],$dd[2],$dd[0]);
		}
		if($this->kclass->input['deliveryFee']==''){
			$this->kclass->input['deliveryFee']=0;
		}
		$this->kclass->input['amount']=floatVal($this->kclass->input['amount']);
		$this->kclass->DB->query("
			INSERT INTO `sample`
				(`productid`,`typeid`,`noType`,`sampleno`,`entitle`,`toMessrs`,`supplierid`,`customerid`,`customerTitle`,`attrid`,`shipmethodid`,`deliveryFee`,`deliveryDate`,`currencyid`,`amount`,`description`,`remark`,`modified`,`director`,`tracker`,`creator`,`created`)
			VALUES
				('".intVal($this->kclass->input['productid'])."','".implode(',',$this->kclass->input['typeid'])."','".$this->kclass->input['noType']."','".$sampleno."','".$this->kclass->input['entitle']."','".$this->kclass->input['toMessrs']."','".$this->kclass->input['supplierid']."','".$this->kclass->input['customerid']."','".$this->kclass->input['customer']."','".$this->kclass->input['attrid']."','".$this->kclass->input['shipmethodid']."','".$this->kclass->input['deliveryFee']."','".$deliveryDate."','".$this->kclass->input['currencyid']."','".$this->kclass->input['amount']."','".$this->kclass->input['description']."','".$this->kclass->input['remark']."','".TIMENOW."','".$this->kclass->input['director']."','".$this->kclass->input['tracker']."','".$this->kclass->user['userid']."','".TIMENOW."')
		");
		$sampleid = $this->kclass->DB->insertID();
		$quantity=0;
		$total=0;
		$pitem=$partItem='';
		for($k = 1; $k < (count($this->kclass->input['itemProductid'])+1); $k++){
			$this->kclass->input['itemProductQuantity'][$k]=intVal($this->kclass->input['itemProductQuantity'][$k]);
			$this->kclass->input['itemProductPrice'][$k]=floatVal($this->kclass->input['itemProductPrice'][$k]);
			$this->kclass->input['itemProductDiscount'][$k]=intVal($this->kclass->input['itemProductDiscount'][$k]);
			if($this->kclass->input['itemProductDiscount'][$k]<0 OR $this->kclass->input['itemProductDiscount'][$k]>100){
				$this->kclass->input['itemProductDiscount'][$k]=100;
			}

			$amount = $this->kclass->input['itemProductQuantity'][$k] * $this->kclass->input['itemProductPrice'][$k];
			if($this->kclass->input['itemProductDiscount'][$k]<100){
				$amount = $amount * $this->kclass->input['itemProductDiscount'][$k] / 100;
			}
			$amount = number_format($amount, 2, '.', '');

			if($this->kclass->input['itemProductid'][$k]>0 AND $this->kclass->input['itemProductQuantity'][$k]!='' AND $this->kclass->input['itemProductUnitid'][$k]>0 AND $this->kclass->input['itemProductPrice'][$k]>=0){
				$productItem=$this->kclass->DB->queryFirst("
					SELECT pi.productid,pi.attributevalue,
						p.title
					FROM productitem AS pi
					LEFT JOIN product AS p ON (p.productid=pi.productid)
					WHERE productitemid='".$this->kclass->input['itemProductid'][$k]."'
				");
				$pitem.='-'.$this->kclass->input['itemProductQuantity'][$k].$productItem['title'];
				$this->kclass->DB->query("
					INSERT INTO `item` 
						(`module`,`mid`,`productid`,`productitemid`,`productAttributes`,`caption`,`brandid`,`packingid`,`quantity`,`discount`,`unitid`,`price`,`amount`,`modified`,`creator`,`created`)
					VALUES 
						('sample','".$sampleid."','".$productItem['productid']."','".$this->kclass->input['itemProductid'][$k]."','".$productItem['attributevalue']."','".$this->kclass->input['itemProductCaption'][$k]."', '".$this->kclass->input['itemProductBrandid'][$k]."', '".$this->kclass->input['itemProductPackingid'][$k]."', '".$this->kclass->input['itemProductQuantity'][$k]."', '".$this->kclass->input['itemProductDiscount'][$k]."', '".$this->kclass->input['itemProductUnitid'][$k]."', '".$this->kclass->input['itemProductPrice'][$k]."', '". $amount."', '".TIMENOW."', '".$this->kclass->user['userid']."', '".TIMENOW."')
				");
				$quantity+=$this->kclass->input['itemProductQuantity'][$i];
				$total+=$amount;
			}
		}
		$count=count($this->kclass->input['itemPartsid']);
		for($k = 1; $k<$count; $k++){
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
					INSERT INTO `item` (`module`,`mid`,`materialid`,`productid`,`caption`,`brandid`,`packingid`,`quantity`,`discount`,`unitid`,`price`,`amount`,`modified`,`creator`,`created`)
					VALUES ('sample','".$sampleid."','".$part['materialid']."','".$this->kclass->input['itemPartsid'][$k]."','".$this->kclass->input['itemPartsCaption'][$k]."', '".$this->kclass->input['itemPartsBrandid'][$k]."', '".$this->kclass->input['itemPartsPackingid'][$k]."', '".$this->kclass->input['itemPartsQuantity'][$k]."', '".$this->kclass->input['itemPartsDiscount'][$k]."', '".$this->kclass->input['itemPartsUnitid'][$k]."', '".$this->kclass->input['itemPartsPrice'][$k]."', '". $amount."', '".TIMENOW."', '".$this->kclass->user['userid']."', '".TIMENOW."')
				");
				$quantity+=$this->kclass->input['itemPartsQuantity'][$i];
				$total+=$amount;
			}
		}
		$sampleTitle=$sampleno.'-'.$countryAbbr.'-'.$customer['customerAbbr'].$pitem.$partItem;
		$this->kclass->DB->query("UPDATE `sample` SET title='".$sampleTitle."', quantity='".$quantity."', amount='".$total."' WHERE sampleid='".$sampleid."'");
		// fee & discount
		$this->kclass->updateAttachs(array('module'=>'sample', 'mid'=>$sampleid));
		$this->kclass->updateFee(array('module'=>'sample','mid'=>$sampleid));
		$this->kclass->updateDiscount(array('module'=>'sample','mid'=>$sampleid));

		$this->kclass->messager(array(
			'title' => '新建样品单',
			'text' => '样品单 <b>'.$sampleno.'</b> 已新建成功!',
			'url' => '/s.php?module=sample&action=view&sampleid='.$sampleid,
			'sec' => 2
		));
}
	//
	function view(){
		if(!$this->kclass->input['sampleid'] > 0)$this->kclass->boinkIt('/s.php?module=sample');
		$sample = $this->kclass->DB->queryFirst("
			SELECT `sample`.*,
				attr.title AS attr,
				currency.title AS currencyTitle, currency.symbol AS currencySymbol, currency.remark AS currencyRemark,
				shipmethod.title AS shipmethod,
				m.realname AS mname, c.realname AS cname, d.realname AS director, t.realname AS tracker
			FROM `sample`
			LEFT JOIN `attr` ON (attr.attrid=sample.attrid)
			LEFT JOIN `currency` ON (currency.currencyid=sample.currencyid)
			LEFT JOIN `shipmethod` ON (shipmethod.shipmethodid=sample.shipmethodid)
			LEFT JOIN `member` AS m ON (m.userid=sample.modifier)
			LEFT JOIN `member` AS c ON (c.userid=sample.creator)
			LEFT JOIN `member` AS d ON (d.userid=sample.director)
			LEFT JOIN `member` AS t ON (t.userid=sample.tracker)
			WHERE `sample`.sampleid='".$this->kclass->input['sampleid']."'
		");
		if(!$sample)	$this->kclass->boinkIt('/s.php?module=sample');
		if($sample['typeid']!=''){
			$stypes = $this->kclass->DB->query("SELECT typeid, title FROM `sampletype` WHERE typeid IN (".$sample['typeid'].")");
			if($this->kclass->DB->numRows()){
				while($stype = $this->kclass->DB->fetchArray($stypes)){
					$sampleType .= '<span title="('.$stype['entitle'].') '.$stype['remark'].'" style="padding-right:12px">'.$stype['title'].'</span>';
				}
			}
		}
		if($sample['customerid']>0){
			$customer = $this->kclass->relatedCustomer(array('id'=>$sample['customerid']));
		}
		$customerTitle=$this->kclass->iif($sample['customerTitle']!='',$sample['customerTitle'],$customer['link']);
		if($sample['supplierid']>0){
			$supplier = $this->kclass->relatedSupplier(array('id'=>$sample['supplierid']));
		}
		if($sample['productid']>0 AND $sample['productid']!=''){
			$product = $this->kclass->relatedProduct(array('id'=>$sample['productid']));
		}else{
			$product=array('off'=>' disabled', 'num'=>'', 'panel'=>'');
		}
		if($sample['modifier']>0){
			$modified = '，'.$sample['mname'].' 于 '.date('y-m-d H:i:s', $sample['modified']).' 最后修改';
		}
		if($sample['deliveryDate']>0){
			$deliveryDate=' <span class="darkred">'.date('Y-m-d', $sample['deliveryDate']).'</span>';
		}
		if($sample['deliveryFee']>0){
			$deliveryFee=' (<span class="small">'.$sample['currency'].'</span>'.$sample['deliveryFee'].')';
		}
		$created = date('y-m-d H:i:s', $sample['created']);
		$status='';
		if($sample['ifVerify']==0){
			$statusid=2;
			$status = '<span class="darkred">未审核　未审批</span>　（<a href="/s.php?module=sample&action=verify&sampleid='.$sample['sampleid'].'">审核</a>）';
		}elseif($sample['ifVerify']==-1){
			$statusid=2;
			$status = '<span class="red">审核未通过</span>　<span class="darkred">未审批</span>';
		}elseif($sample['ifVerify']==1 AND $sample['ifApprove']==0){
			$statusid=3;
			$status = '<span class="green">审核通过</span>　<span class="darkred">未审批</span>　（<a href="/s.php?module=sample&action=approve&sampleid='.$sample['sampleid'].'">审批</a>）';
		}elseif($sample['ifVerify']==1 AND $sample['ifApprove']==-1){
			$statusid=3;
			$status = '<span class="green">审核通过</span>　<span class="red">审批未通过</span>';
		}elseif($sample['ifVerify']==1 AND $sample['ifApprove']==1){
			$statusid=4;
			$status = '<span class="green">审核通过　审批通过</span>';
		}
		$status.=$this->kclass->iif($sample['ifChooser']==1,'　<span class="green">已投产</span>','　<span class="darkred">未投产</span>');
		$ifoutbound=$this->kclass->DB->queryFirst("
			SELECT outboundid
			FROM outbound
			WHERE killed=0 AND ifComplete=2 AND sampleid='".$sample['sampleid']."'
		");
		$status.=$this->kclass->iif($ifoutbound=='','　<span class="darkred">未出库</span>','　<span class="green">已出库</span>');
		if($sample['ifChooser']==1)$statusid=5;
		if($ifoutbound!='')$statusid=6;
		$total=0;
		$items=$this->kclass->relatedSampleProductItem(array('sampleid'=>$sample['sampleid']));
		$total=$items['total'];
		$fee = $this->kclass->relatedFee(array('sampleid'=>$sample['sampleid']));
		$total += $fee['total'];
		$discounts = $this->kclass->DB->query("SELECT * FROM `discount` WHERE killed=0 AND module='sample' AND mid='".$sample['sampleid']."'");
		if($this->kclass->DB->numRows()){
			while($discount=$this->kclass->DB->fetchArray($discounts)){
				if($items['total']>0){
					$amount = 0;
					if($discount['sign']=='%'){
						$discount['discount'] = intVal($discount['discount']);
						$amount = ($total * $discount['discount'] / 100);
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
				<td align="right">'.$sample['currencySymbol'].number_format($total, 2).'</td>
				</tr>';
			}
			$discounttr = '<tr><td title="Reason">折扣说明(理由)</td><td title="Discount" align="right">折扣</td><td title="Amount" align="right">计算折扣数</td><td title="Amount" align="right">折扣后总数</td></tr>'.$discounttr.'<tr><td colspan="12" align="right">折后计：<span class="bold">'.$sample['currencyTitle'].' '.$sample['currencySymbol'].number_format($total, 2).'</span></td></tr>';
		}else{
			$discounttr = '<tr><td class="gray"><i>无折扣信息</i></td></tr>';
		}
		$total = number_format($total, 2);

		$attach = $this->kclass->getAttachs(array('module'=>'sample', 'mid'=>$sample['sampleid']));
		if($attach!= false){
			$attachs = '<div class="clear">'.$attach.'</div>';
		}
		$events = $this->kclass->DB->query("
			SELECT event.*,
				dc.title AS deliveryCurrency,
				sc.title AS sampleCurrency,
				shipmethod.title AS shipmethod
			FROM sampleevent AS `event`
			LEFT JOIN currency AS dc ON (dc.currencyid=`event`.deliveryCurrency)
			LEFT JOIN currency AS sc ON (sc.currencyid=`event`.sampleCurrency)
			LEFT JOIN shipmethod ON (shipmethod.shipmethodid=`event`.shipmethodid)
			WHERE `event`.killed=0 AND `event`.sampleid='".$sample['sampleid']."'
			ORDER BY `event`.modified DESC
		");
		if($this->kclass->DB->numRows()){
			$eventtbl = '<table class="hundred"><thead><tr><th colspan="7">收发与费用</th></tr><tr><th>收发类型</th><th>收发数量</th><th>收发日期</th><th>递送方式</th><th>递送费用</th><th>样品费用</th><th>操作</th></tr></thead><tbody>';
			$this->kclass->tbline+=1;
			while($event = $this->kclass->DB->fetchArray($events)){
				$eventtbl.='<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').'"><td'.$this->kclass->iif($event['direction']=='sent', ' class="green">发出', ' class="red">收到').'</td><td'.$this->kclass->iif($event['totalQuantity']=='all', '>全部', ' class="bold">部分').'</td><td>'.date('Y-m-d', $event['deliveryDate']).'</td><td>'.$event['shipmethod'].'</td><td>'.$event['deliveryCurrency'].$event['deliveryFee'].$this->kclass->iif($event['deliveryCaption']!='', ' ('.$event['deliveryCaption'].')', '').'</td><td>'.$event['sampleCurrency'].$event['sampleFee'].$this->kclass->iif($event['sampleCaption']!='', ' ('.$event['sampleCaption'].')', '').'</td><td><a href="/s.php?module=sample&action=removeEvent&eventid='.$event['eventid'].'" onclick="return confirm(\'你确定要删除这条收发与费用记录吗？(删除不可恢复！)\')">删</a> <a href="/s.php?module=sample&action=updateEvent&eventid='.$event['eventid'].'">改</a></td></tr>';
			}
			$eventtbl .= '</tbody></table>';
		}else{
			$event['off']=' disabled';
		}

		$revise=$this->kclass->relatedRevise(array('module'=>'sample','mid'=>$sample['sampleid']));
		$freight = $this->kclass->DB->queryFirst("SELECT * FROM `freight` WHERE module='sample' AND mid='".$sample['sampleid']."'");
		$freightStr = '';
		if($freight){
			$freightStr = '<a href="/s.php?module=freight&action=update&sampleid='.$sample['sampleid'].'">修改货运信息</a>　<a href="/s.php?module=freight&action=kill&sampleid='.$sample['sampleid'].'" onclick="return confirm(\'确定删除货运信息?\');">删除货运信息</a>';
		}else{
			$freight = array();
			$freightStr = '<a href="/s.php?module=freight&action=add&sampleid='.$sample['sampleid'].'">新建货运信息</a>';
		}
		$freightInfo = $this->kclass->getFreightInfo($freight);
		$packingStr = '';
		$packing = $this->kclass->DB->queryFirst("SELECT * FROM `packinglist` WHERE module='sample' AND mid='".$sample['sampleid']."'");
		if($packing){
			$packingStr = '<a href="/s.php?module=packinglist&action=updatePacking&sampleid='.$sample['sampleid'].'&packinglistid='.$packing['packinglistid'].'">修改包装箱单</a>';
		}else{
			$packingStr = '<a href="/s.php?module=packinglist&action=addPacking&sampleid='.$sample['sampleid'].'">生成包装箱单</a>';
		}
		$outbound = $this->kclass->relatedOutbound(array('sampleid'=>$sample['sampleid']));//相关出库单
		$inbound = $this->kclass->relatedInbound(array('sampleid'=>$sample['sampleid']));//相关入库单
		$requirement = $this->kclass->releatedRequirement(array('sampleid'=>$sample['sampleid'],'panel'=>'off'));//相关需求计划单
		$flow=$this->kclass->listFlow(array('module'=>'poorder','statusid'=>$statusid));
$body = <<<EOF
<dl id="sample" class="tabs">
	<dt>资料</dt>
	<dt title="询价记录"{$event['off']}>收发与费用{$event['count']}</dt>
	<dt title="相关产品"{$product['off']}>产品信息{$product['count']}</dt>
	<dt title="相关客户基本资料"{$customer['off']}>相关客户</dt>
	<dt title="相关供应商基本资料"{$supplier['off']}>相关供应商</dt>
	<dt title="相关的需求计划"{$requirement['off']} ajax="requirement">需求计划{$requirement['count']}</dt>
	<dt title="相关的出库单"{$outbound['off']}>出库单{$outbound['count']}</dt>
	<dt title="相关的入库单"{$inbound['off']}>入库单{$inbound['count']}</dt>
	<dd>
<div class="gray small"><span class="small darkred">{$reviseInfo['now']}{$reviseInfo['old']}</span><span class="small gray">当前样品单状态：{$status}</span><span class="right normal">{$freightStr}　{$packingStr}　|　<a href="/p.php?module=printer&action=sample&sampleid={$sample['sampleid']}" target="_blank">打印样品单</a>　<a href="/p.php?module=printer&action=samplePL&sampleid={$sample['sampleid']}" target="_blank">打印样品单装箱明细</a>　<a href="/s.php?module=sample&action=sampleCopy&sampleid={$sample['sampleid']}">复制样品单</a></span></div>
<div class="bold darkred big" style="clear:both;">{$sample['title']}<span class="small">({$sample['sampleno']})</span>{$revise['currencyRevise']}　　　<span class="small gray">　　　业务人员：<span class="darkred bold">{$sample['director']}</span>　　　跟单人员：<span class="darkred bold">{$sample['tracker']}</span>　　　　由 {$sample['cname']} 于 {$created} 建立{$modified}。</span></div>
<div><span class="right">{$flow}</span></div>
<table cellspacing="10" width="100%">
<tr class="odd">
	<td width="80">样品类型：</td>
	<td class="middle">{$sampleType}</td>
	<td width="80">样品标题：</td>
	<td class="middle">{$sample['title']}</td>
</tr>
<tr class="even">
	<td valign="top">样品所属：</td>
	<td class="middle">{$sample['attr']}</td>
	<td valign="top">客　　户：</td>
	<td class="middle">{$customerTitle}</td>
</tr>
<tr class="odd">
	<td>供 应 商：</td>
	<td class="middle">{$supplier['link']}</td>
	<td>联系人员：</td>
	<td class="middle">{$sample['toMessrs']}</td>
</tr>
<tr class="odd">
	<td>递送方式：</td>
	<td class="middle">{$sample['shipmethod']}{$deliveryFee}{$deliveryDate}</td>
	<td>样品费用：</td>
	<td class="middle">{$sample['currencyTitle']} {$sample['currencySymbol']}{$sample['amount']}</td>
</tr>
<tr class="even">
	<td valign="top">样品描述：</td>
	<td class="middle">{$sample['description']}</td>
	<td valign="top">备　　注：</td>
	<td>{$sample['remark']}</td>
</tr>
</table>
<table class="hundred">
<thead>
<tr>
	<th colspan="12">样品明细表 (Item Information)</th>
</tr>
</thead>
<tbody class="small">
{$items['tr']}
</tbody>
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
{$discounttr}
</tbody>
</table>
<table class="hundred">
<tbody>
<tr><td align="right">总计(Total Amount)：<span class="middle bold darkred">{$sample['currencyTitle']} {$sample['currencySymbol']}{$total}</span></td></tr>
</tbody>
</table>
{$eventtbl}
{$freightInfo}
{$revise['panel']}
{$attachs}
	</dd>
	<dd>{$eventtbl}</dd>
	<dd>{$product['panel']}</dd>
	<dd>{$customer['panel']}</dd>
	<dd>{$supplier['panel']}</dd>
	<dd id="requirement" load="0"></dd>
	<dd>{$outbound['panel']}</dd>
	<dd>{$inbound['panel']}</dd>
</dl>
EOF;
		$json="{'id':'sample','sampleid':".$sample['sampleid']."}";
		$this->kclass->page['onload'] .= 'dc.tabs('.$json.');dc.tabhover();dc.show()';
		$this->kclass->page['title'] .= ' - '.$sample['title'];
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl.' - 样品单信息', 'right' => ''.$this->kclass->iif($b['killed']==1, '<a href="/s.php?module=sample&action=restore&sampleid='.$sample['sampleid'].'&rt=view">恢复</a>', '<a href="/s.php?module=sample&action=remove&sampleid='.$sample['sampleid'].'&rt=view">删除</a>　<a href="/s.php?module=sample&action=update&sampleid='.$sample['sampleid'].'&rt=view">修改</a>　|　<a href="/s.php?module=sample&action=addEvent&sampleid='.$sample['sampleid'].'&rt=view">新建收发&费用</a>'), 'body'=>$body));
	}
	//
	function insert(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			if($this->kclass->input['m']=='old'){
				$sampleno=$this->kclass->input['sampleno'];
			}else{
				if($this->kclass->input['noType']==0){
					$sampleno=$this->kclass->id(array('sample'=>TIMENOW));
				}elseif($this->kclass->input['noType']==1){
					$sampleno=$this->kclass->input['sampleno'];
				}
			}
			$sample=$this->kclass->DB->queryFirst("SELECT sampleid FROM sample WHERE sampleno='".$sampleno."' LIMIT 0,1");
			if($sample['sampleid']){
				$e='<li>编号<b>'.$sampleno.'</b> 在样品单中 已存在</li>';
			}
			if($this->kclass->input['title'] == ''){
				$e = '<li>请填写样品单的 标题。</li>';
			}
			if(count($this->kclass->input['typeid']) == 0){
				$e .= '<li>请填写样品单的 样品类型。</li>';
			}
			if($this->kclass->input['attrid'] < 1){
				$e .= '<li>请填写样品单的 样品所属。</li>';
			}
			if(!$this->kclass->input['supplierid'] > 0){
				$e .= '<li>请选择样品单的所属 供应商。</li>';
			}
			if($this->kclass->input['amount'] < 0){
				$e .= '<li>请选择样品单的 样品费用。</li>';
			}
			$j1=count($this->kclass->input['itemProductid']);

		}

		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '新建样品单',
				'text' => '您在新建样品单的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
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
		$deliveryDate=0;
		if(strlen($this->kclass->input['deliveryDate'])==10){
			$dd = explode('-', $this->kclass->input['deliveryDate']);
			$deliveryDate = mktime(0,0,0,$dd[1],$dd[2],$dd[0]);
		}
		if($this->kclass->input['deliveryFee']==''){
			$this->kclass->input['deliveryFee']=0;
		}
		$this->kclass->input['amount']=floatVal($this->kclass->input['amount']);
		$this->kclass->DB->query("
			INSERT INTO `sample`
				(`productid`,`typeid`,`noType`,`sampleno`,`entitle`,`toMessrs`,`supplierid`,`customerid`,`customerTitle`,`attrid`,`shipmethodid`,`deliveryFee`,`deliveryDate`,`currencyid`,`amount`,`description`,`remark`,`modified`,`director`,`tracker`,`creator`,`created`)
			VALUES
				('".intVal($this->kclass->input['productid'])."','".implode(',',$this->kclass->input['typeid'])."','".$this->kclass->input['noType']."','".$sampleno."','".$this->kclass->input['entitle']."','".$this->kclass->input['toMessrs']."','".$this->kclass->input['supplierid']."','".$this->kclass->input['customerid']."','".$this->kclass->input['customer']."','".$this->kclass->input['attrid']."','".$this->kclass->input['shipmethodid']."','".$this->kclass->input['deliveryFee']."','".$deliveryDate."','".$this->kclass->input['currencyid']."','".$this->kclass->input['amount']."','".$this->kclass->input['description']."','".$this->kclass->input['remark']."','".TIMENOW."','".$this->kclass->input['director']."','".$this->kclass->input['tracker']."','".$this->kclass->user['userid']."','".TIMENOW."')
		");
		$sampleid = $this->kclass->DB->insertID();
		$quantity=0;
		$total=0;
		$pitem=$partItem='';
		for($k = 1; $k < (count($this->kclass->input['itemProductid'])+1); $k++){
			$this->kclass->input['itemProductQuantity'][$k]=intVal($this->kclass->input['itemProductQuantity'][$k]);
			$this->kclass->input['itemProductPrice'][$k]=floatVal($this->kclass->input['itemProductPrice'][$k]);
			$this->kclass->input['itemProductDiscount'][$k]=intVal($this->kclass->input['itemProductDiscount'][$k]);
			if($this->kclass->input['itemProductDiscount'][$k]<0 OR $this->kclass->input['itemProductDiscount'][$k]>100){
				$this->kclass->input['itemProductDiscount'][$k]=100;
			}

			$amount = $this->kclass->input['itemProductQuantity'][$k] * $this->kclass->input['itemProductPrice'][$k];
			if($this->kclass->input['itemProductDiscount'][$k]<100){
				$amount = $amount * $this->kclass->input['itemProductDiscount'][$k] / 100;
			}
			$amount = number_format($amount, 2, '.', '');

			if($this->kclass->input['itemProductid'][$k]>0 AND $this->kclass->input['itemProductQuantity'][$k]!='' AND $this->kclass->input['itemProductUnitid'][$k]>0 AND $this->kclass->input['itemProductPrice'][$k]>=0){
				$productItem=$this->kclass->DB->queryFirst("
					SELECT pi.productid,pi.attributevalue,
						p.title
					FROM productitem AS pi
					LEFT JOIN product AS p ON (p.productid=pi.productid)
					WHERE productitemid='".$this->kclass->input['itemProductid'][$k]."'
				");
				$pitem.='-'.$this->kclass->input['itemProductQuantity'][$k].$productItem['title'];
				$this->kclass->DB->query("
					INSERT INTO `item` 
						(`module`,`mid`,`productid`,`productitemid`,`productAttributes`,`caption`,`brandid`,`packingid`,`quantity`,`discount`,`unitid`,`price`,`amount`,`modified`,`creator`,`created`)
					VALUES 
						('sample','".$sampleid."','".$productItem['productid']."','".$this->kclass->input['itemProductid'][$k]."','".$productItem['attributevalue']."','".$this->kclass->input['itemProductCaption'][$k]."', '".$this->kclass->input['itemProductBrandid'][$k]."', '".$this->kclass->input['itemProductPackingid'][$k]."', '".$this->kclass->input['itemProductQuantity'][$k]."', '".$this->kclass->input['itemProductDiscount'][$k]."', '".$this->kclass->input['itemProductUnitid'][$k]."', '".$this->kclass->input['itemProductPrice'][$k]."', '". $amount."', '".TIMENOW."', '".$this->kclass->user['userid']."', '".TIMENOW."')
				");
				$quantity+=$this->kclass->input['itemProductQuantity'][$i];
				$total+=$amount;
			}
		}
		$count=count($this->kclass->input['itemPartsid']);
		for($k = 1; $k<=$count; $k++){
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
					INSERT INTO `item` (`module`,`mid`,`materialid`,`productid`,`caption`,`brandid`,`packingid`,`quantity`,`discount`,`unitid`,`price`,`amount`,`modified`,`creator`,`created`)
					VALUES ('sample','".$sampleid."','".$part['materialid']."','".$this->kclass->input['itemPartsid'][$k]."','".$this->kclass->input['itemPartsCaption'][$k]."', '".$this->kclass->input['itemPartsBrandid'][$k]."', '".$this->kclass->input['itemPartsPackingid'][$k]."', '".$this->kclass->input['itemPartsQuantity'][$k]."', '".$this->kclass->input['itemPartsDiscount'][$k]."', '".$this->kclass->input['itemPartsUnitid'][$k]."', '".$this->kclass->input['itemPartsPrice'][$k]."', '". $amount."', '".TIMENOW."', '".$this->kclass->user['userid']."', '".TIMENOW."')
				");
				$quantity+=$this->kclass->input['itemPartsQuantity'][$i];
				$total+=$amount;
			}
		}
		$sampleTitle=$sampleno.'-'.$countryAbbr.'-'.$customer['customerAbbr'].$pitem.$partItem;
		$this->kclass->DB->query("UPDATE `sample` SET title='".$sampleTitle."', quantity='".$quantity."', amount='".$total."' WHERE sampleid='".$sampleid."'");
		// fee & discount
		$this->kclass->updateAttachs(array('module'=>'sample', 'mid'=>$sampleid));
		$this->kclass->updateFee(array('module'=>'sample','mid'=>$sampleid));
		$this->kclass->updateDiscount(array('module'=>'sample','mid'=>$sampleid));
		$this->kclass->messager(array(
			'title' => '新建样品单',
			'text' => '样品单 <b>'.$sampleno.'</b> 已新建成功!',
			'url' => '/s.php?module=sample&action=view&sampleid='.$sampleid,
			'sec' => 2
		));
	}

	//
	function add(){

		$sampleno=$this->kclass->id(array('sample'=>TIMENOW));
		$director = $this->kclass->chooserTracker(array('name'=>'director','condition'=>'positionid=7','selectedid'=>$this->kclass->user['userid'],'width'=>100));
		$tracker = $this->kclass->chooserTracker(array('name'=>'tracker','condition'=>'positionid=7','width'=>100,'selectedid'=>3));
		$sampletype = $this->kclass->chooserSampleType(array('name'=>'typeid', 'width'=>350, 'selectedid'=>2));
		$supplier = $this->kclass->chooserSupplierSelect(array('name'=>'supplierid', 'hasBlank'=>1, 'width'=>350, 'selectedid'=>2));
		$customer=$this->kclass->chooserCustomer(array('hName'=>'customerid','name'=>'customer','width'=>450, 'onchange'=>'customerStr'));
		$currency = $this->kclass->chooserCurrency(array('name'=>'currencyid', 'hasBlank'=>1, 'width'=>100, 'selectedid'=>3));
		$shipmethod = $this->kclass->chooserShipmethod(array('name'=>'shipmethodid', 'hasBlank'=>1, 'width'=>350, 'selectedid'=>5));
		$attr = $this->kclass->chooserAttr(array('name'=>'attrid','module'=>'product','selectedid'=>1));

		$fee=$this->kclass->formFee(array('module'=>'sample'));
		$discount=$this->kclass->formDiscount(array('module'=>'sample'));

		$deliveryDate = date('Y-m-d', TIMENOW);
		$upload = $this->kclass->upload(array('title'=>'相关附件：'));
		$flow=$this->kclass->listFlow(array('module'=>'poorder','statusid'=>'1'));

		for($i = 1; $i < 7; $i++){
			$itemtrs .= '<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').'" id="producttr'.$i.'">
			<td>'.$i.'</td>
			<td><span id="tditemProductid'.$i.'"></span>'.$this->kclass->chooserProductItem(array('name'=>'itemProductid['.$i.']','selectName'=>'itemProductid'.$i, 'hasBlank'=>1, 'width'=>500)).'</td>
			<td><input type="text" name="itemProductCaption['.$i.']" value="'.$item['caption'].'" size="60"></td>
			<td>'.$this->kclass->chooserBrand(array('name'=>'itemProductBrandid['.$i.']', 'hasBlank'=>1, 'width'=>80, 'selectedid'=>1)).'</td>
			<td>'.$this->kclass->chooserPacking(array('name'=>'itemProductPackingid['.$i.']', 'hasBlank'=>1, 'width'=>100, 'selectedid'=>6)).'</td>
			<td><input type="text" name="itemProductDiscount['.$i.']" value="100" size="3"></td>
			<td><input type="text" name="itemProductQuantity['.$i.']" value="'.$item['quantity'].'" size="3" onblur="dc.returnProductStr('.$i.',this.value)"></td>
			<td>'.$this->kclass->chooserUnit(array('name'=>'itemProductUnitid['.$i.']', 'hasBlank'=>1, 'width'=>80, 'selectedid'=>5)).'</td>
			<td><input type="text" name="itemProductPrice['.$i.']" value="0" size="5"></td>
			</tr>';
		}
		$itemtrs.='<tr class="odd bold gray normal" id="sampleProduct" nohover><td colspan=10><span class="hand" onclick="dc.addLine(\'sampleProduct\',\'sampleProduct\')" style="border:2px solid gray;background:#FFE8EB">增加一行产品明细</span></td></tr>';
		for($i = 1; $i < 7; $i++){
			$parts .= '<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').'" id="parttr'.$i.'">
			<td>'.$i.'</td>
			<td>'.$this->kclass->chooserParts(array('name'=>'itemPartsid['.$i.']', 'hasBlank'=>1, 'width'=>500)).'</td>
			<td><input type="text" name="itemPartsCaption['.$i.']" value="'.$item['caption'].'" size="60"></td>
			<td>'.$this->kclass->chooserBrand(array('name'=>'itemPartsBrandid['.$i.']', 'hasBlank'=>1, 'width'=>80, 'selectedid'=>1)).'</td>
			<td>'.$this->kclass->chooserPacking(array('name'=>'itemPartsPackingid['.$i.']', 'hasBlank'=>1, 'width'=>100, 'selectedid'=>6)).'</td>
			<td><input type="text" name="itemPartsDiscount['.$i.']" value="100" size="3"></td>
			<td><input type="text" name="itemPartsQuantity['.$i.']" value="'.$item['quantity'].'" size="3"></td>
			<td>'.$this->kclass->chooserUnit(array('name'=>'itemPartsUnitid['.$i.']', 'hasBlank'=>1, 'width'=>80, 'selectedid'=>5)).'</td>
			<td><input type="text" name="itemPartsPrice['.$i.']" value="0" size="5"></td>
			</tr>';
		}
		$parts.='<tr class="odd bold gray normal" id="samplePart" nohover><td colspan=10><span class="hand" onclick="dc.addLine(\'samplePart\',\'samplePart\')" style="border:2px solid gray;background:#FFE8EB">增加一行配件明细</span></td></tr>';

$body = <<<EOF
<form action="/s.php?module=sample&action=insert" name="sample" method="post">
<input type="hidden" name="module" value="sample">
<input type="hidden" name="action" value="insert">
<div><span class="right">{$flow}</span></div>
<table class="hundred">
<thead>
<tr>
	<th colspan="4">新建样品单：<span class="darkred" id="yearStr">{$sampleno}</span><span class="darkred" id="customerStr"></span><span class="darkred" id="productStr"></span></th>
</tr>
</thead>
<tbody>
<tr class="even">
	<td>样品编号：<span class="red bold">*</span></td>
	<td><input type="text" name="sampleno" value="{$sampleno}"  class="dark middle bold" /><span class="red small"> 此为预估编号</span>　　编号生成方式：<input type="radio" name="noType" value="1" >手动<input type="radio" name="noType" value="0" checked>自动<input type="hidden" name="title" id="orderTitle" value="{$sampleno}" style="width:350px;"></td>
	<td>样品所属：<span class="red bold">*</span></td>
	<td>{$attr}</td>
</tr>
<tr class="odd">
	<td>联 系 人：<span class="red bold">*</span></td>
	<td><input type="text" style="width:350px" name="toMessrs" value="{$sample['toMessrs']}"></td>
	<td>样品类型：<span class="red bold">*</span></td>
	<td>{$sampletype}</td>
</tr>
<tr class="even">
	<td>客　　户：<span class="red bold">*</span></td>
	<td>{$customer}</td>
	<td>供 应 商：<span class="red bold">*</span></td>
	<td>{$supplier}</td>
</tr>
<tr class="odd">
	<td>递送方式：</td>
	<td>{$shipmethod}</td>
	<td>运费预估：</td>
	<td><input type="text" style="width:170px" name="deliveryFee" value="{$sample['deliveryFee']}">　业务人员：{$director}</td>
</tr>
<tr class="even">
	<td>递送日期：<span class="red bold">*</span></td>
	<td><input type="text" style="width:350px" id="deliveryDate" name="deliveryDate" value="{$deliveryDate}"></td>
	<td>样品费用：<span class="red bold">*</span></td>
	<td>{$currency}<input type="text" style="width:70px" id="total" name="amount" value="{$sample['amount']}">　跟单人员：{$tracker}</td>
</tr>
<tr class="odd">
	<td valign="top">样品描述：</td>
	<td><textarea name="description" style="width:350px;height:70px">{$sample['description']}</textarea></td>
	<td valign="top">备　　注：</td>
	<td><textarea name="remark" style="width:350px;height:70px">{$sample['remark']}</textarea></td>
</tr>
</tbody>
</table>
<table class="hundred">
<thead>
<tr><th colspan="9">样品明细</th></tr>
</thead>
<tbody class="small">
<tr><td width="30">ID</td><td width="100">产品选择<span class="red bold">*</span></td><td  width="300">说明</td><td  width="100">品牌</td><td  width="50">包装</td><td  width="45">折扣%</td><td width="30">数量<span class="red bold">*</span></td><td width="80">单位<span class="red bold">*</span></td><td width="50">单价<span class="red bold">*</span></td></tr>
{$itemtrs}
<tr><td width="30">ID</td><td width="100">配件选择<span class="red bold">*</span></td><td  width="300">说明</td><td  width="100">品牌</td><td  width="50">包装</td><td  width="45">折扣%</td><td width="30">数量<span class="red bold">*</span></td><td width="80">单位<span class="red bold">*</span></td><td width="50">单价<span class="red bold">*</span></td></tr>
{$parts}
<tr>
	<td colspan="9" class="gray small">注：①如行数不够，请保存后再进入修改页面。②至少有“名称”、“数量”、“单位”三个项目，该明细才会被保存。③如果没有费用，请填写“0”。</td>
</tr>
</tbody>
</table>
{$fee}
{$discount}
{$upload}
<table class="hundred">
<tbody>
<tr class="odd">
	<td colspan="4" align="center">
		<input type="submit" value="  保存  " accesskey="s">
		<input type="reset" value="  复位  ">
	</td>
</tr>
<tr class="even">
	<td class="small gray">
	注意事项：<ul>
	<li>英文地址：请严格按英文的格式进行(会影响订单中打印的地址)，基本与中文地址相反的顺序。</li>
	<li>未有列项的内容，写入“备注”中，长度上限3万中文字。</li>
	</ul>
	</td>
</tr>
</tbody>
</table>
</form>
EOF;
		$this->kclass->tbline+=4;
		$this->kclass->page['title'] .= ' - 新建样品';
		$this->kclass->page['onload'] .= 'dc.menu(\'rightNav\');dc.tabhover();$(\'#deliveryDate\').datepicker()';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 新建样品', 'right' => '<ul id="rightNav">'.$this->right . '<li></li><li><a href="/s.php?module=sample">返回列表</a></li></ul>', 'body'=>$body));	
	}
	//
	function addOld(){
		$dateStr = date("Ymd");
		$year=((int)substr($dateStr,0,4));//取得年份
		$month=((int)substr($dateStr,4,2));//取得月份
		$day=((int)substr($dateStr,6,2));//取得几号
		$dateStamp = mktime(0,0,0,$month,$day,$year);

		$orderNum = $this->kclass->DB->queryFirst("SELECT COUNT(sampleid) AS num FROM `sample` WHERE created > ".$dateStamp);
		$noStr = '';
		$orderNum['num']++;
		if(strlen($orderNum['num']) < 2 AND strlen($orderNum['num']) > 0){
			$noStr = "0".$orderNum['num'];
		}elseif(strlen($orderNum['num']) == 2){
			$noStr = $orderNum['num'];
		}else{
			exit("订单数量有误！");
		}
		$yearStr = 'SP'.$dateStr.$noStr;

		$director = $this->kclass->chooserTracker(array('name'=>'director','condition'=>'positionid=7','selectedid'=>$this->kclass->user['userid'],'width'=>100));
		$tracker = $this->kclass->chooserTracker(array('name'=>'tracker','condition'=>'positionid=7','width'=>100,'selectedid'=>28));
		$sampletype = $this->kclass->chooserSampleType(array('name'=>'typeid', 'width'=>350, 'selectedid'=>2));
		$supplier = $this->kclass->chooserSupplierSelect(array('name'=>'supplierid', 'hasBlank'=>1, 'width'=>350, 'selectedid'=>2));
		$customer = $this->kclass->chooserCustomer(array('name'=>'customerid', 'hasBlank'=>1, 'width'=>350, 'onchange'=>'customerStr','selectedid'=>$this->kclass->input['customerid']));
		$currency = $this->kclass->chooserCurrency(array('name'=>'currencyid', 'hasBlank'=>1, 'width'=>100, 'selectedid'=>3));
		$shipmethod = $this->kclass->chooserShipmethod(array('name'=>'shipmethodid', 'hasBlank'=>1, 'width'=>350, 'selectedid'=>5));
		$attr = $this->kclass->chooserAttr(array('name'=>'attrid','module'=>'product','selectedid'=>1));

		$fee=$this->kclass->formFee(array('module'=>'sample'));
		$discount=$this->kclass->formDiscount(array('module'=>'sample'));

		$deliveryDate = date('Y-m-d', TIMENOW);
		$upload = $this->kclass->upload(array('title'=>'相关附件：'));

		for($i = 1; $i < 7; $i++){
			$itemtrs .= '<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').'">
			<td><span id="tditemProductid'.$i.'"></span>'.$this->kclass->chooserProductItem(array('name'=>'itemProductid['.$i.']','selectName'=>'itemProductid'.$i, 'hasBlank'=>1, 'width'=>500)).'</td>
			<td><input type="text" name="itemProductCaption['.$i.']" value="'.$item['caption'].'" size="60"></td>
			<td>'.$this->kclass->chooserBrand(array('name'=>'itemProductBrandid['.$i.']', 'hasBlank'=>1, 'width'=>80, 'selectedid'=>1)).'</td>
			<td>'.$this->kclass->chooserPacking(array('name'=>'itemProductPackingid['.$i.']', 'hasBlank'=>1, 'width'=>100, 'selectedid'=>6)).'</td>
			<td><input type="text" name="itemProductDiscount['.$i.']" value="0" size="3"></td>
			<td><input type="text" name="itemProductQuantity['.$i.']" value="'.$item['quantity'].'" size="3" onblur="dc.returnProductStr('.$i.',this.value)"></td>
			<td>'.$this->kclass->chooserUnit(array('name'=>'itemProductUnitid['.$i.']', 'hasBlank'=>1, 'width'=>80, 'selectedid'=>5)).'</td>
			<td><input type="text" name="itemProductPrice['.$i.']" value="'.$item['price'].'" size="5"></td>
			</tr>';
		}

		for($i = 1; $i < 7; $i++){
			$parts .= '<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').'">
			<td>'.$this->kclass->chooserParts(array('name'=>'itemPartsid['.$i.']', 'hasBlank'=>1, 'width'=>500)).'</td>
			<td><input type="text" name="itemPartsCaption['.$i.']" value="'.$item['caption'].'" size="60"></td>
			<td>'.$this->kclass->chooserBrand(array('name'=>'itemPartsBrandid['.$i.']', 'hasBlank'=>1, 'width'=>80, 'selectedid'=>1)).'</td>
			<td>'.$this->kclass->chooserPacking(array('name'=>'itemPartsPackingid['.$i.']', 'hasBlank'=>1, 'width'=>100, 'selectedid'=>6)).'</td>
			<td><input type="text" name="itemPartsDiscount['.$i.']" value="0" size="3"></td>
			<td><input type="text" name="itemPartsQuantity['.$i.']" value="'.$item['quantity'].'" size="3"></td>
			<td>'.$this->kclass->chooserUnit(array('name'=>'itemPartsUnitid['.$i.']', 'hasBlank'=>1, 'width'=>80, 'selectedid'=>5)).'</td>
			<td><input type="text" name="itemPartsPrice['.$i.']" value="'.$item['price'].'" size="5"></td>
			</tr>';
		}

$body = <<<EOF
<form action="/s.php?module=sample&action=insert" name="sample" method="post">
<input type="hidden" name="module" value="sample">
<input type="hidden" name="action" value="insert">
<input type="hidden" name="m" value="old">
<table class="hundred">
<thead>
<tr>
	<th colspan="4">新建样品单：<span class="darkred" id="yearStr">{$yearStr}</span><span class="darkred" id="customerStr"></span><span class="darkred" id="productStr"></span></th>
</tr>
</thead>
<tbody>
<tr class="even">
	<td>样品编号：<span class="red bold">*</span></td>
	<td><input type="text" name="sampleno" id="orderNo" value="{$yearStr}" style="width:350px;"><input type="hidden" name="title" id="orderTitle" value="{$yearStr}" style="width:350px;"></td>
	<td>样品所属：<span class="red bold">*</span></td>
	<td>{$attr}</td>
</tr>
<tr class="odd">
	<td>联 系 人：<span class="red bold">*</span></td>
	<td><input type="text" style="width:350px" name="toMessrs" value="{$sample['toMessrs']}"></td>
	<td>样品类型：<span class="red bold">*</span></td>
	<td>{$sampletype}</td>
</tr>
<tr class="even">
	<td>客　　户：<span class="red bold">*</span></td>
	<td>{$customer}</td>
	<td>供 应 商：<span class="red bold">*</span></td>
	<td>{$supplier}</td>
</tr>
<tr class="odd">
	<td>递送方式：</td>
	<td>{$shipmethod}</td>
	<td>运费预估：</td>
	<td><input type="text" style="width:170px" name="deliveryFee" value="{$sample['deliveryFee']}">　业务人员：{$director}</td>
</tr>
<tr class="even">
	<td>递送日期：<span class="red bold">*</span></td>
	<td><input type="text" style="width:350px" id="deliveryDate" name="deliveryDate" value="{$deliveryDate}"></td>
	<td>样品费用：<span class="red bold">*</span></td>
	<td>{$currency}<input type="text" style="width:70px" id="total" name="amount" value="{$sample['amount']}">　跟单人员：{$tracker}</td>
</tr>
<tr class="odd">
	<td valign="top">样品描述：</td>
	<td><textarea name="description" style="width:350px;height:70px">{$sample['description']}</textarea></td>
	<td valign="top">备　　注：</td>
	<td><textarea name="remark" style="width:350px;height:70px">{$sample['remark']}</textarea></td>
</tr>
</tbody>
</table>
<table class="hundred">
<thead>
<tr><th colspan="9">样品明细</th></tr>
</thead>
<tbody class="small">
<tr><td width="100">产品选择<span class="red bold">*</span></td><td  width="300">说明</td><td  width="100">品牌</td><td  width="50">包装</td><td  width="45">折扣%</td><td width="30">数量<span class="red bold">*</span></td><td width="80">单位<span class="red bold">*</span></td><td width="50">单价<span class="red bold">*</span></td></tr>
{$itemtrs}
<tr><td width="100">配件选择<span class="red bold">*</span></td><td  width="300">说明</td><td  width="100">品牌</td><td  width="50">包装</td><td  width="45">折扣%</td><td width="30">数量<span class="red bold">*</span></td><td width="80">单位<span class="red bold">*</span></td><td width="50">单价<span class="red bold">*</span></td></tr>
{$parts}
<tr>
	<td colspan="9" class="gray small">注：①如行数不够，请保存后再进入修改页面。②至少有“名称”、“数量”、“单位”三个项目，该明细才会被保存。③如果没有费用，请填写“0”。</td>
</tr>
</tbody>
</table>
{$fee}
{$discount}
{$upload}
<table class="hundred">
<tbody>
<tr class="odd">
	<td colspan="4" align="center">
		<input type="submit" value="  保存  " accesskey="s">
		<input type="reset" value="  复位  ">
	</td>
</tr>
<tr class="even">
	<td class="small gray">
	注意事项：<ul>
	<li>英文地址：请严格按英文的格式进行(会影响订单中打印的地址)，基本与中文地址相反的顺序。</li>
	<li>未有列项的内容，写入“备注”中，长度上限3万中文字。</li>
	</ul>
	</td>
</tr>
</tbody>
</table>
</form>
EOF;
		$this->kclass->tbline+=4;
		$this->kclass->page['title'] .= ' - 新建样品';
		$this->kclass->page['onload'] .= 'dc.menu(\'rightNav\');dc.tabhover();$(\'#deliveryDate\').datepicker()';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 新建样品', 'right' => '<ul id="rightNav">'.$this->right . '<li></li><li><a href="/s.php?module=sample">返回列表</a></li></ul>', 'body'=>$body));
	}
	//
	function doupdate(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			$sample=$this->kclass->DB->queryFirst("SELECT sampleid,sampleno,customerid,supplierid,iflocked,ifVerify,ifApprove FROM `sample` WHERE sampleid='".$this->kclass->input['sampleid']."'");
			if(count($this->kclass->input['typeid']) == 0){
				$e .= '<li>请填写样品单的 样品简称缩写。</li>';
			}
			if($sample['iflocked']==1){
				$e.='<li>您要修改的订单已经被锁定，请通知生产部解锁</li>';
			}
			if(!$this->kclass->input['supplierid'] > 0){
				$e .= '<li>请选择样品单的所属 供应商。</li>';
			}
		}

		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '修改样品单',
				'text' => '您在修改样品单的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}

		//获取历史版本信息
		$sampleHtml = '';
		if($sample['ifVerify']!=0 OR $sample['ifApprove']!=0){
			$revise = $this->_getSampleHtml();
			$this->kclass->insertReviseInfo(array('module'=>'sample', 'mid'=>$sample['sampleid'],'revise'=> $revise,'remark'=>$this->kclass->input['reviseRemark']));
			$this->kclass->DB->query("UPDATE `sample` SET ifVerify=0,verified=0,verifier=0,ifApprove=0,approved=0,approver=0,ifChooser=0 WHERE sampleid='".$sample['sampleid']."'");
		}

		$this->kclass->updateAttachs(array('module'=>'sample', 'mid'=>$this->kclass->input['sampleid']));
		// item
		$quantity=0;
		$pitem=$partItem='';
		$count1=count($this->kclass->input['itemProductid']);
		for($k = 1; $k<$count1+1; $k++){
			$this->kclass->input['itemProductQuantity'][$k]=intVal($this->kclass->input['itemProductQuantity'][$k]);
			$this->kclass->input['itemProductPrice'][$k]=floatVal($this->kclass->input['itemProductPrice'][$k]);
			$this->kclass->input['itemProductDiscount'][$k]=intVal($this->kclass->input['itemProductDiscount'][$k]);
			if($this->kclass->input['itemProductDiscount'][$k]<0 OR $this->kclass->input['itemProductDiscount'][$k]>100){
				$this->kclass->input['itemProductDiscount'][$k]=100;
			}
			$amount = $this->kclass->input['itemProductQuantity'][$k] * $this->kclass->input['itemProductPrice'][$k];
			if($this->kclass->input['itemProductDiscount'][$k]<100){
				$amount = $amount * $this->kclass->input['itemProductDiscount'][$k] / 100;
			}
			$amount = number_format($amount, 2, '.', '');
			$productItem=$this->kclass->DB->queryFirst("
				SELECT pi.productid,pi.attributevalue,
					p.title
				FROM productitem AS pi
				LEFT JOIN product AS p ON (p.productid=pi.productid)
				WHERE pi.productitemid='".$this->kclass->input['itemProductid'][$k]."'
			");
			if($this->kclass->input['itemProId'][$k]>0){
				if($this->kclass->input['itemProductKill'][$k]!='' AND $this->kclass->input['itemProductKill'][$k]==$this->kclass->input['itemProId'][$k]){
					$this->kclass->DB->query("UPDATE `item` SET killed='".TIMENOW."',killer='".$this->kclass->user['userid']."' WHERE itemid='".$this->kclass->input['itemProId'][$k]."'");
				}else{
					$pitem.='-'.$this->kclass->input['itemProductQuantity'][$k].$productItem['title'];
					$this->kclass->DB->query("
						UPDATE `item` SET 
							`productid`='".$productItem['productid']."',
							`productitemid` = '".$this->kclass->input['itemProductid'][$k]."',
							`productAttributes`='".$productItem['attributevalue']."',
							`caption` = '".$this->kclass->input['itemProductCaption'][$k]."',
							`brandid` = '".$this->kclass->input['itemProductBrandid'][$k]."',
							`packingid` = '".$this->kclass->input['itemProductPackingid'][$k]."',
							`quantity` = '".$this->kclass->input['itemProductQuantity'][$k]."',
							`discount` = '".$this->kclass->input['itemProductDiscount'][$k]."',
							`unitid` = '".$this->kclass->input['itemProductUnitid'][$k]."',
							`price` = '".$this->kclass->input['itemProductPrice'][$k]."',
							`amount` = '". $amount."',
							`modified` = '".TIMENOW."',
							`modifier`='".$this->kclass->user['userid']."'
						WHERE itemid='".$this->kclass->input['itemProId'][$k]."'
					");
					$quantity+=$this->kclass->input['itemProductQuantity'][$k];
				}
			}else{
				if($this->kclass->input['itemProductid'][$k]>0 AND $this->kclass->input['itemProductQuantity'][$k]!='' AND $this->kclass->input['itemProductUnitid'][$k]>0 AND $this->kclass->input['itemProductPrice'][$k]>=0){
					$pitem.='-'.$this->kclass->input['itemProductQuantity'][$k].$productItem['title'];
					$this->kclass->DB->query("
						INSERT INTO `item` (`module`,`mid`,`productid`,`productitemid`,`productAttributes`,`caption`,`brandid`,`packingid`,`quantity`,`discount`,`unitid`,`price`,`amount`,`modified`,`creator`,`created`)
						VALUES ('sample','".$this->kclass->input['sampleid']."','".$productItem['productid']."','".$this->kclass->input['itemProductid'][$k]."','".$productItem['attributevalue']."','".$this->kclass->input['itemProductCaption'][$k]."','".$this->kclass->input['itemProductBrandid'][$k]."','".$this->kclass->input['itemProductPackingid'][$k]."','".$this->kclass->input['itemProductQuantity'][$k]."','".$this->kclass->input['itemProductDiscount'][$k]."','".$this->kclass->input['itemProductUnitid'][$k]."', '".$this->kclass->input['itemProductPrice'][$k]."', '". $amount."', '".TIMENOW."', '".$this->kclass->user['userid']."', '".TIMENOW."')
					");
				}
			}
		}
		$count2=count($this->kclass->input['itemPartsid']);
		for($k = 1; $k<$count2+1; $k++){
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
			$part=$this->kclass->DB->queryFirst("SELECT materialid FROM product WHERE productid='".$this->kclass->input['itemPartsid'][$k]."'");
			if($this->kclass->input['itemParId'][$k]>0){
				if($this->kclass->input['itemPartsKill'][$k]!='' AND $this->kclass->input['itemPartsKill'][$k]==$this->kclass->input['itemParId'][$k]){
					$this->kclass->DB->query("UPDATE `item` SET killed='".TIMENOW."',killer='".$this->kclass->user['userid']."' WHERE itemid='".$this->kclass->input['itemParId'][$k]."'");
				}else{
					$partItem='-PARTS';
					$this->kclass->DB->query("
						UPDATE `item` SET
							`materialid`='".$part['materialid']."',
							`productid` = '".$this->kclass->input['itemPartsid'][$k]."',
							`caption` = '".$this->kclass->input['itemPartsCaption'][$k]."',
							`brandid` = '".$this->kclass->input['itemPartsBrandid'][$k]."',
							`packingid` = '".$this->kclass->input['itemPartsPackingid'][$k]."',
							`quantity` = '".$this->kclass->input['itemPartsQuantity'][$k]."',
							`discount` = '".$this->kclass->input['itemPartsDiscount'][$k]."',
							`unitid` = '".$this->kclass->input['itemPartsUnitid'][$k]."',
							`price` = '".$this->kclass->input['itemPartsPrice'][$k]."',
							`amount` = '". $amount."',
							`modified` = '".TIMENOW."',
							`modifier`='".$this->kclass->user['userid']."'
						WHERE itemid='".$this->kclass->input['itemParId'][$k]."'
					");
					$quantity+=$this->kclass->input['itemPartsQuantity'][$k];
				}
			}else{
				if($this->kclass->input['itemPartsid'][$k]>0 AND $this->kclass->input['itemPartsQuantity'][$k]!='' AND $this->kclass->input['itemPartsUnitid'][$k]>0 AND $this->kclass->input['itemPartsPrice'][$k]>=0){
					$partItem='-PARTS';
					$this->kclass->DB->query("
						INSERT INTO `item` 
							(`module`,`mid`,`materialid`,`productid`,`caption`,`brandid`,`packingid`,`quantity`,`discount`,`unitid`,`price`,`amount`,`modified`,`creator`,`created`)
						VALUES 
							('sample','".$this->kclass->input['sampleid']."','".$part['materialid']."','".$this->kclass->input['itemPartsid'][$k]."','".$this->kclass->input['itemPartsCaption'][$k]."','".$this->kclass->input['itemPartsBrandid'][$k]."','".$this->kclass->input['itemPartsPackingid'][$k]."','".$this->kclass->input['itemPartsQuantity'][$k]."','".$this->kclass->input['itemPartsDiscount'][$k]."','".$this->kclass->input['itemPartsUnitid'][$k]."','".$this->kclass->input['itemPartsPrice'][$k]."','". $amount."','".TIMENOW."','".$this->kclass->user['userid']."','".TIMENOW."')
					");
				}
			}
		}

		// fee & discount
		$this->kclass->updateFee(array('module'=>'sample','mid'=>$this->kclass->input['sampleid']));
		$this->kclass->updateDiscount(array('module'=>'sample','mid'=>$this->kclass->input['sampleid']));

		if(1==$this->kclass->input['history']){
			$this->kclass->insertReviseInfo(array('module'=>'sample', 'mid'=>$this->kclass->input['sampleid'],'html'=> $sampleHtml));
		}
		$deliveryDate=0;
		if($this->kclass->input['deliveryDate']!=''){
			$dd = explode('-', $this->kclass->input['deliveryDate']);
			$deliveryDate = mktime(0,0,0,$dd[1],$dd[2],$dd[0]);
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
		$sampleTitle=$sample['sampleno'].'-'.$countryAbbr.'-'.$customer['customerAbbr'].$pitem.$partItem;
		$this->kclass->DB->query("
			UPDATE `sample` SET
				`typeid`='".implode(',', $this->kclass->input['typeid'])."',
				`sampleno`='".$sample['sampleno']."',
				`title`='".$sampleTitle."',
				`entitle`='".$this->kclass->input['entitle']."',
				`toMessrs`='".$this->kclass->input['toMessrs']."',
				`supplierid`='".$this->kclass->input['supplierid']."',
				`customerid`='".$this->kclass->input['customerid']."',
				`customerTitle`='".$this->kclass->input['customer']."',
				`attrid`='".$this->kclass->input['attrid']."',
				`quantity`='".$quantity."',
				`shipmethodid`='".$this->kclass->input['shipmethodid']."',
				`deliveryFee`='".$this->kclass->input['deliveryFee']."',
				`deliveryDate`='".$deliveryDate."',
				`currencyid`='".$this->kclass->input['currencyid']."',
				`amount`='".$this->kclass->input['amount']."',
				`description`='".$this->kclass->input['description']."',
				`remark`='".$this->kclass->input['remark']."',
				`director`='".$this->kclass->input['director']."',
				`tracker`='".$this->kclass->input['tracker']."',
				`modified`='".TIMENOW."',
				`modifier`='".$this->kclass->user['userid']."'
			WHERE sampleid='".$this->kclass->input['sampleid']."'
		");
		$this->kclass->messager(array(
			'title' => '修改样品单',
			'text' => '样品单 <b>'.$this->kclass->input['sampleno'].'</b> 已修改成功!',
			'url' => '/s.php?module=sample&action=view&sampleid='.$this->kclass->input['sampleid'],
			'sec' => 2
		));
	}

	//
	function update(){
		$sample = $this->kclass->DB->queryFirst("
			SELECT `sample`.*,
					customer.title AS customerTitle, customer.cntitle AS customerCntitle
			FROM `sample`
			LEFT JOIN customer ON (customer.customerid=`sample`.customerid)
			WHERE `sample`.killed=0 AND `sample`.`sampleid`='".$this->kclass->input['sampleid']."'
		");
		//$sample = $this->kclass->DB->queryFirst("SELECT * FROM `sample` WHERE `sampleid`='".$this->kclass->input['sampleid']."'")
		if($this->kclass->input['sampleid']<=0 OR !$sample){
			$this->kclass->boinkIt('/s.php?module=sample');
		}

		$this->kclass->br2nl=1;
		$sample['description']=$this->kclass->parseConvertValue($sample['description']);
		$sample['remark']=$this->kclass->parseConvertValue($sample['remark']);
		$sampletype = $this->kclass->chooserSampleType(array('name'=>'typeid', 'width'=>350, 'selectedid'=>$sample['typeid']));
		$attr = $this->kclass->chooserAttr(array('name'=>'attrid','module'=>'product','selectedid'=>$sample['attrid']));
		$supplier = $this->kclass->chooserSupplierSelect(array('name'=>'supplierid', 'hasBlank'=>1, 'width'=>350, 'selectedid'=>$sample['supplierid']));
		$customer=$this->kclass->chooserCustomer(array('hName'=>'customerid','selectedid'=>$sample['customerid'],'value'=>$sample['customerTitle'],'name'=>'customer','width'=>450, 'onchange'=>'customerStr'));
		$currency = $this->kclass->chooserCurrency(array('name'=>'currencyid', 'width'=>100, 'hasBlank'=>1, 'selectedid'=>$sample['currencyid']));
		$shipmethod = $this->kclass->chooserShipmethod(array('name'=>'shipmethodid', 'width'=>350, 'hasBlank'=>1, 'selectedid'=>$sample['shipmethodid']));
		if(strpos($sample['title'],'-')>0){
			$customerStr = substr($sample['title'],strpos($sample['title'],'-'),(strpos($sample['title'],'+')-12));
		}else{
			$customerStr = '';
		}
		if(strpos($sample['title'],'+')>0){
			$productStr = substr($sample['title'],strpos($sample['title'],'+'));
		}else{
			$productStr = '';
		}
		// 审核或者审批后修改  修改原因
		if($sample['ifVerify']!=0 OR $sample['ifApprove']!=0){
			$updateReason='<tr class="odd"><td>修改原因：</td><td><textarea name="reviseRemark" style="width:350px;height:70px"></textarea></td><td></td><td></td></tr>';
		}
		// item
		$items = $this->kclass->DB->query("
			SELECT item.*,
				p.productid,p.title,p.entitle
			FROM `item` 
			LEFT JOIN `product` AS p ON (p.productid=`item`.productid)
			WHERE module='sample' AND mid='".$sample['sampleid']."' AND `item`.killed=0
			ORDER BY itemid ASC
		");
		$k=1;
		$n=1;
		if($this->kclass->DB->numRows()){
			$flag=0;
			while($item = $this->kclass->DB->fetchArray($items)){
				if($item['productitemid']>0){
					if($n==1){
						$itemtr .= '<tr><td width="100">样品名称<span class="red bold">*</span></td><td  width="50">说明</td><td  width="100">品牌</td><td  width="50">包装</td><td  width="45">折扣%</td><td width="30">数量<span class="red bold">*</span></td><td width="80">单位<span class="red bold">*</span></td><td width="50">单价<span class="red bold">*</span></td><td width="30">删</td></tr>';
					}
					$seletStr = '<input type="hidden" id="tditemProductid'.$n.'input" value="'.$item['entitle'].'" />';
					$itemtr .= '<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').'">
					<td><span id="tditemProductid'.$n.'">'.$seletStr.'</span><input type="hidden" name="itemProId['.$n.']" value="'.$item['itemid'].'">'.$this->kclass->chooserProductItem(array('name'=>'itemProductid['.$n.']','selectName'=>'itemProductid'.$n, 'selectedid'=>$item['productitemid'], 'hasBlank'=>1, 'width'=>500)).'</td>
					<td><input type="text" name="itemProductCaption['.$n.']" value="'.$item['caption'].'" size="50"></td>
					<td>'.$this->kclass->chooserBrand(array('name'=>'itemProductBrandid['.$n.']', 'hasBlank'=>1, 'width'=>100, 'selectedid'=>$item['brandid'])).'</td>
					<td>'.$this->kclass->chooserPacking(array('name'=>'itemProductPackingid['.$n.']', 'hasBlank'=>1, 'width'=>100, 'selectedid'=>$item['packingid'])).'</td>
					<td><input type="text" name="itemProductDiscount['.$n.']" value="'.$item['discount'].'" size="3"></td>
					<td><input type="text" name="itemProductQuantity['.$n.']" value="'.$item['quantity'].'" size="3" onblur="dc.returnProductStr('.$n.',this.value)"></td>
					<td>'.$this->kclass->chooserUnit(array('name'=>'itemProductUnitid['.$n.']', 'hasBlank'=>1, 'width'=>80, 'selectedid'=>$item['unitid'])).'</td>
					<td><input type="text" name="itemProductPrice['.$n.']" value="'.$item['price'].'" size="5"></td>
					<td><input type="checkbox" name="itemProductKill['.$n.']" value="'.$item['itemid'].'"></td>
					</tr>';
					$n++;
				}else{
					if($k==1){
						$parts .= '<tr><td width="100">配件名称<span class="red bold">*</span></td><td  width="50">说明</td><td  width="100">品牌</td><td  width="50">包装</td><td  width="45">折扣%</td><td width="30">数量<span class="red bold">*</span></td><td width="80">单位<span class="red bold">*</span></td><td width="50">单价<span class="red bold">*</span></td><td width="30">删</td></tr>';
					}
					$parts .= '<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').'">
					<td><input type="hidden" name="itemParId['.$k.']" value="'.$item['itemid'].'">'.$this->kclass->chooserParts(array('name'=>'itemPartsid['.$k.']','selectedid'=>$item['productid'], 'hasBlank'=>1, 'width'=>500)).'</td>
					<td><input type="text" name="itemPartsCaption['.$k.']" value="'.$item['caption'].'" size="50"></td>
					<td>'.$this->kclass->chooserBrand(array('name'=>'itemPartsBrandid['.$k.']', 'hasBlank'=>1, 'width'=>100, 'selectedid'=>$item['brandid'])).'</td>
					<td>'.$this->kclass->chooserPacking(array('name'=>'itemPartsPackingid['.$k.']', 'hasBlank'=>1, 'width'=>100, 'selectedid'=>$item['packingid'])).'</td>
					<td><input type="text" name="itemPartsDiscount['.$k.']" value="'.$item['discount'].'" size="3"></td>
					<td><input type="text" name="itemPartsQuantity['.$k.']" value="'.$item['quantity'].'" size="3"></td>
					<td>'.$this->kclass->chooserUnit(array('name'=>'itemPartsUnitid['.$k.']', 'hasBlank'=>1, 'width'=>80, 'selectedid'=>$item['unitid'])).'</td>
					<td><input type="text" name="itemPartsPrice['.$k.']" value="'.$item['price'].'" size="5"></td>
					<td><input type="checkbox" name="itemPartsKill['.$k.']" value="'.$item['itemid'].'"></td>
					</tr>';
					$k++;
				}
			}
		}
		$itemtrs .= '<tr><td width="100">样品名称<span class="red bold">*</span></td><td  width="50">说明</td><td  width="100">品牌</td><td  width="50">包装</td><td  width="45">折扣%</td><td width="30">数量<span class="red bold">*</span></td><td width="80">单位<span class="red bold">*</span></td><td width="50">单价<span class="red bold">*</span></td><td width="30">删</td></tr>';
		for($i = $n; $i < ($n+5); $i++){
			$itemtrs .= '<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').'">
			<td><span id="tditemProductid'.$i.'"></span>'.$this->kclass->chooserProductItem(array('name'=>'itemProductid['.$i.']','selectName'=>'itemProductid'.$i, 'hasBlank'=>1, 'width'=>500)).'</td>
			<td><input type="text" name="itemProductCaption['.$i.']" value="'.$item['caption'].'" size="50"></td>
			<td>'.$this->kclass->chooserBrand(array('name'=>'itemProductBrandid['.$i.']', 'hasBlank'=>1, 'width'=>100, 'selectedid'=>1)).'</td>
			<td>'.$this->kclass->chooserPacking(array('name'=>'itemProductPackingid['.$i.']', 'hasBlank'=>1, 'width'=>100, 'selectedid'=>6)).'</td>
			<td><input type="text" name="itemProductDiscount['.$i.']" value="100" size="3"></td>
			<td><input type="text" name="itemProductQuantity['.$i.']" value="1" size="3" onblur="dc.returnProductStr('.$i.',this.value)"></td>
			<td>'.$this->kclass->chooserUnit(array('name'=>'itemProductUnitid['.$i.']', 'hasBlank'=>1, 'width'=>80, 'selectedid'=>5)).'</td>
			<td><input type="text" name="itemProductPrice['.$i.']" value="'.$item['price'].'" size="5"></td>
			<td></td></tr>';
		}

		$partStr .= '<tr><td width="100">配件名称<span class="red bold">*</span></td><td  width="50">说明</td><td  width="100">品牌</td><td  width="50">包装</td><td  width="45">折扣%</td><td width="30">数量<span class="red bold">*</span></td><td width="80">单位<span class="red bold">*</span></td><td width="50">单价<span class="red bold">*</span></td><td width="30">删</td></tr>';
		for($i = $k; $i < ($k+5); $i++){
			$partStr .= '<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').'">
			<td>'.$this->kclass->chooserParts(array('name'=>'itemPartsid['.$i.']','hasBlank'=>1, 'width'=>500)).'</td>
			<td><input type="text" name="itemPartsCaption['.$i.']" value="'.$item['caption'].'" size="50"></td>
			<td>'.$this->kclass->chooserBrand(array('name'=>'itemPartsBrandid['.$i.']', 'hasBlank'=>1, 'width'=>100, 'selectedid'=>1)).'</td>
			<td>'.$this->kclass->chooserPacking(array('name'=>'itemPartsPackingid['.$i.']', 'hasBlank'=>1, 'width'=>100, 'selectedid'=>6)).'</td>
			<td><input type="text" name="itemPartsDiscount['.$i.']" value="100" size="3"></td>
			<td><input type="text" name="itemPartsQuantity['.$i.']" value="1" size="3"></td>
			<td>'.$this->kclass->chooserUnit(array('name'=>'itemPartsUnitid['.$i.']', 'hasBlank'=>1, 'width'=>80, 'selectedid'=>5)).'</td>
			<td><input type="text" name="itemPartsPrice['.$i.']" value="'.$item['price'].'" size="5"></td>
			<td></td></tr>';
		}

		$director = $this->kclass->chooserTracker(array('name'=>'director','condition'=>'positionid=7','width'=>80,'selectedid'=>$sample['director']));
		$tracker = $this->kclass->chooserTracker(array('name'=>'tracker','condition'=>'positionid=7','width'=>80,'selectedid'=>$sample['tracker']));

		$fee=$this->kclass->formFee(array('module'=>'sample','mid'=>$sample['sampleid']));
		$discount=$this->kclass->formDiscount(array('module'=>'sample','mid'=>$sample['sampleid']));

		$upload = $this->kclass->upload(array('module'=>'sample', 'mid'=>$sample['sampleid']));
		if($sample['deliveryDate']>0){
			$deliveryDate = date('Y-m-d', $sample['deliveryDate']);
		}
$body = <<<EOF
<form action="/s.php?module=sample&action=doupdate" name="sample" method="post">
<input type="hidden" name="module" value="sample">
<input type="hidden" name="action" value="doupdate">
<input type="hidden" name="sampleid" value="{$sample['sampleid']}">
<input type="hidden" name="rt" value="{$this->kclass->input['rt']}">
<table class="hundred">
<thead>
<tr>
	<th colspan="4">修改样品单：<span class="darkred" id="yearStr">{$sample['sampleno']}</span><span class="darkred" id="customerStr">{$customerStr}</span><span class="darkred" id="productStr">{$productStr}</span></th>
</tr>
</thead>
<tbody>
<tr class="even">
	<td width="100">样品编号：<span class="red bold">*</span></td>
	<td class="red middle bold" width="650">{$sample['sampleno']}  <span class="small">订单编号不能更改</span><input type="hidden" name="title" id="orderTitle" value="{$sample['title']}"></td>
	<td width="100">样品所属：<span class="red bold">*</span></td>
	<td>{$attr}</td>
</tr>
<tr class="odd">
	<td>联 系 人：<span class="red bold">*</span></td>
	<td><input type="text" style="width:350px" name="toMessrs" value="{$sample['toMessrs']}"></td>
	<td>样品类型：<span class="red bold">*</span></td>
	<td>{$sampletype}</td>
</tr>
<tr class="even">
	<td>客　　户：<span class="red bold">*</span></td>
	<td>{$customer}</td>
	<td>供 应 商：<span class="red bold">*</span></td>
	<td>{$supplier}</td>
</tr>
<tr class="odd">
	<td>递送方式：</td>
	<td>{$shipmethod}</td>
	<td>运费预估：</td>
	<td><input type="text" style="width:170px" name="deliveryFee" value="{$sample['deliveryFee']}">　业务人员：{$director}</td>
</tr>
<tr class="even">
	<td>递送日期：<span class="red bold">*</span></td>
	<td><input type="text" style="width:350px" id="deliveryDate" name="deliveryDate" value="{$deliveryDate}"></td>
	<td>样品费用：<span class="red bold">*</span></td>
	<td>{$currency}<input type="text" style="width:70px" id="total" name="amount" value="{$sample['amount']}">　跟单人员：{$tracker}</td>
</tr>
<tr class="odd">
	<td valign="top">样品描述：</td>
	<td><textarea name="description" style="width:350px;height:70px">{$sample['description']}</textarea></td>
	<td valign="top">备　　注：</td>
	<td><textarea name="remark" style="width:350px;height:70px">{$sample['remark']}</textarea></td>
</tr>
{$updateReason}
</tbody>
</table>
<table class="hundred">
<thead>
<tr>
	<th colspan="10">样品明细表</th>
</tr>
</thead>
{$itemtr}
{$parts}
{$itemtrs}
{$partStr}
<tr>
	<td colspan="10" class="gray small">注：①如行数不够，请保存后再进入修改页面。②至少有“名称”、“数量”、“单位”三个项目，该明细才会被保存。</td>
</tr>
</tbody>
</table>
{$fee}
{$discount}
{$upload}
<table class="hundred">
<tbody>
<tr class="odd">
	<td colspan="4" align="center">
		<input type="submit" value="  保存  " accesskey="s">
		<input type="reset" value="  复位  ">
	</td>
</tr>
<tr class="even">
	<td class="small gray">
	注意事项：<ul>
	<li>英文地址：请严格按英文的格式进行(会影响订单中打印的地址)，基本与中文地址相反的顺序。</li>
	<li>未有列项的内容，写入“备注”中，长度上限3万中文字。</li>
	</ul>
	</td>
</tr>
</tbody>
</table>
</form>
EOF;
		$this->kclass->page['title'] .= ' - 修改 - '.$sample['title'];
		$this->kclass->page['onload'] .= 'dc.tabhover();$(\'#deliveryDate\').datepicker()';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 修改样品 ', 'right' => '<a href="/s.php?module=sample">返回列表</a>', 'body'=>$body));
	}


	/**
	 * 审核样品单表单
	 * @author creator mayinghao 2013-07-17 8:15
	 * @access public
	 * @param none 没有参数
	 * @return void 没有返回信息
	 * @throws none 没有异常
	 */
	function verify(){
		$url = REFERER;
		$body = <<<EOF
<form action="/s.php?module=sample&action=doverify" name="sample" method="post">
<input type="hidden" name="module" value="sample">
<input type="hidden" name="action" value="doverify">
<input type="hidden" name="sampleid" value="{$this->kclass->input['sampleid']}">
<table class="hundred">
<thead>
<tr>
<th colspan="4">审核样品单：</th>
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
		$this->kclass->page['title'] .= ' - 审核样品单';
		$this->kclass->page['onload'] .= 'dc.tabhover();$(\'#shipmentDate\').datepicker();';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 审核样品单', 'right' => '<a href="'.REFERER.'">返回</a>', 'body'=>$body));
	}

	/**
	 * 审核样品单
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
			$sample=$this->kclass->DB->queryFirst("SELECT sampleno,ifVerify FROM `sample` WHERE sampleid='".$this->kclass->input['sampleid']."'");
			if(!$sample){
				$e.='<li>数据错误。</li>';
			}
			if($sample['ifVerify']!=0){
				$e.='<li>样品单 <b>'.$sample['sampleno'].'</b> 已进行过审核，请进行审批</li>';
			}
			if($this->kclass->input['ifVerify']!=1 AND $this->kclass->input['ifVerify']!=-1){
				$e.='<li>请选择样品单中的 是否通过审核 按钮。</li>';
			}
		}
		if($e){
			$this->kclass->messager(array(
				'title' => '审核样品单',
				'text' => '您在审核样品单的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$this->kclass->DB->query("
			UPDATE `sample` SET
				ifVerify='".$this->kclass->input['ifVerify']."',
				verifier='".$this->kclass->user['userid']."',
				verified='".TIMENOW."'
			WHERE sampleid='".$this->kclass->input['sampleid']."'
		");
		if($this->kclass->input['ifVerify']==1){
			$verify='已通过';
		}elseif($this->kclass->input['ifVerify']==-1){
			$verify='未通过';
		}
		$this->kclass->messager(array(
			'title' => '审核样品单',
			'text' => '样品单 【<b>'.$sample['sampleno'].'</b>】 '.$verify.'审核!返回查看样品单',
			'url' => '/s.php?module=sample&action=view&sampleid='.$this->kclass->input['sampleid'],
			'sec' => 2
		));
	}


	/**
	 * 审批样品单表单
	 * @author creator mayinghao 2013-07-17 8:15
	 * @access public
	 * @param none 没有参数
	 * @return void 没有返回信息
	 * @throws none 没有异常
	 */
	function approve(){
		$body = <<<EOF
<form action="/s.php?module=sample&action=doapprove" name="sample" method="post">
<input type="hidden" name="module" value="sample">
<input type="hidden" name="action" value="doapprove">
<input type="hidden" name="sampleid" value="{$this->kclass->input['sampleid']}">
<table class="hundred">
<thead>
<tr>
<th colspan="4">审批样品单：</th>
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
		$this->kclass->page['title'] .= ' - 审批样品单';
		$this->kclass->page['onload'] .= 'dc.tabhover();$(\'#shipmentDate\').datepicker();';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 审批样品单', 'right' => '<a href="'.REFERER.'">返回</a>', 'body'=>$body));
	}

	/**
	 * 审批样品单
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
			$sample=$this->kclass->DB->queryFirst("SELECT sampleno,ifApprove FROM `sample` WHERE sampleid='".$this->kclass->input['sampleid']."'");
			if(!$sample){
				$e.='<li>数据错误。</li>';
			}
			if($sample['ifApprove']!=0){
				$e.='<li>样品单 <b>'.$sample['sampleno'].'</b> 已进行过审批</li>';
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
			UPDATE `sample` SET
				ifApprove='".$this->kclass->input['ifApprove']."',
				approver='".$this->kclass->user['userid']."',
				approved='".TIMENOW."'
			WHERE sampleid='".$this->kclass->input['sampleid']."'
		");
		if($this->kclass->input['ifApprove']==1){
			$verify='已通过';
		}elseif($this->kclass->input['ifApprove']==-1){
			$verify='未通过';
		}
		$this->kclass->messager(array(
			'title' => '审批订单',
			'text' => '订单 【<b>'.$sample['sampleno'].'</b>】 '.$verify.'审批!返回查看订单',
			'url' => '/s.php?module=sample&action=view&sampleid='.$this->kclass->input['sampleid'],
			'sec' => 2
		));
	}


	//
	function kill(){
		if($this->kclass->input['sampleid']<=0){
			$this->kclass->boinkIt('/s.php?module=sample');
		}
		if($this->kclass->input['confirm'] == 0 AND $this->kclass->input['rt']=='view'){
			$this->kclass->boinkIt('/s.php?module=sample');
		}
		if($this->kclass->input['sampleid'] < 0){
			$e = '<li>请从正确的页面进行操作，如果此问题持续存在，请联系网站管理员。';
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '删除样品',
				'text' => '您在删除样品的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		if($this->kclass->input['rt']=='view'){
			$rt = '&action=view&sampleid='.$this->kclass->input['sampleid'];
		}elseif($this->kclass->input['rt']=='list'){
			$rt = '#'.$this->kclass->input['sampleid'];
		}
		$sample = $this->kclass->DB->queryFirst("
			SELECT title
			FROM sample
			WHERE sampleid='".$this->kclass->input['sampleid']."'
		");
		if($sample){
			if(2==$sample['status']){
				$this->kclass->messager(array(
						'title' => '修改样品单',
						'text' => '样品单 <b>'.$sample['sampleno'].'</b> 的信息已经通过了审批不能再删除!',
						'url' => REFERER,
						'sec' => 3
				));
			}else{
				$this->kclass->DB->query("
					UPDATE `sample`
					SET killed=".TIMENOW.",killer='".$this->kclass->user['userid']."'
					WHERE sampleid='".$this->kclass->input['sampleid']."'
				");
				$this->kclass->messager(array(
					'title' => '删除样品成功',
					'text' => '样品 <b>'.$sample['title'].'</b> 已成功被标记为删除!',
					'url' => '/s.php?module=sample'.$rt,
					'sec' => 3
				));
			}
		}else{
			$this->kclass->messager(array(
				'title' => '删除样品失败',
				'text' => '您要删除的样品，不存在！',
				'url' => '/s.php?module=sample'.$rt,
				'sec' => 3
			));
		}
	}

	//
	function remove(){
		if($this->kclass->input['sampleid']<=0){
			$this->kclass->boinkIt('/s.php?module=sample');
		}
		$sample = $this->kclass->DB->queryFirst("
			SELECT title
			FROM sample
			WHERE sampleid='".$this->kclass->input['sampleid']."'
		");
$body = <<<EOF
<form action="/s.php?module=sample&action=kill" name="sample" method="post">
<input type="hidden" name="module" value="sample">
<input type="hidden" name="action" value="kill">
<input type="hidden" name="sampleid" value="{$this->kclass->input['sampleid']}">
<input type="hidden" name="rt" value="{$this->kclass->input['rt']}">
<table>
<thead>
<tr>
	<th>删除样品：{$sample['title']}</th>
</tr>
</thead>
<tbody>
<tr class="even">
	<td class="middle">你确定要删除样品: <a href="/s.php?module=sample&action=view&sampleid={$this->kclass->input['sampleid']}" class="big bold" target="_blank">{$sample['title']}</a> 吗?</td>
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
		$this->kclass->page['title'] .= ' - 删除 - '.$sample['title'];
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - <a href="/s.php?module=sample&action=view&sampleid='.$sample['sampleid'].'">'.$sample['title'].'</a> - 删除样品', 'right' => '<a href="/s.php?module=sample">返回列表</a>', 'body'=>$body));
	}

	//
	function revival(){
		if($this->kclass->input['sampleid']<=0){
			$this->kclass->boinkIt('/s.php?module=sample');
		}
		if($this->kclass->input['confirm'] == 0){
			$this->kclass->boinkIt('/s.php?module=sample');
		}
		if($this->kclass->input['sampleid'] < 0){
			$e = '<li>请从正确的页面进行操作，如果此问题持续存在，请联系网站管理员。';
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '恢复样品',
				'text' => '您在恢复样品的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$sample = $this->kclass->DB->queryFirst("
			SELECT title
			FROM sample
			WHERE sampleid='".$this->kclass->input['sampleid']."'
		");
		if($this->kclass->input['rt']=='view'){
			$rt = '&action=view&sampleid='.$this->kclass->input['sampleid'];
		}elseif($this->kclass->input['rt']=='list'){
			$rt = '#'.$this->kclass->input['sampleid'];
		}
		if($sample){
			$this->kclass->DB->query("
				UPDATE `sample`
				SET killed=0
				WHERE sampleid='".$this->kclass->input['sampleid']."'
			");
			$this->kclass->messager(array(
				'title' => '恢复样品成功',
				'text' => '样品 <b>'.$sample['title'].'</b> 已成功被恢复！',
				'url' => '/s.php?module=sample'.$rt,
				'sec' => 3
			));
		}else{
			$this->kclass->messager(array(
				'title' => '恢复样品失败',
				'text' => '您要恢复的样品不存在！',
				'url' => '/s.php?module=sample'.$rt,
				'sec' => 3
			));
		}
	}

	//
	function restore(){
		if($this->kclass->input['sampleid']<=0){
			$this->kclass->boinkIt('/s.php?module=sample');
		}
		$sample = $this->kclass->DB->queryFirst("
			SELECT title
			FROM sample
			WHERE sampleid='".$this->kclass->input['sampleid']."'
		");
$body = <<<EOF
<form action="/s.php?module=sample&action=revival" name="sample" method="post">
<input type="hidden" name="module" value="sample">
<input type="hidden" name="action" value="revival">
<input type="hidden" name="sampleid" value="{$this->kclass->input['sampleid']}">
<table>
<thead>
<tr>
	<th>恢复样品：{$sample['title']}</th>
</tr>
</thead>
<tbody>
<tr class="even">
	<td class="middle">你确定要恢复样品: <a href="/s.php?module=sample&action=view&sampleid={$this->kclass->input['sampleid']}" class="big bold" target="_blank">{$sample['title']}</a> 吗?</td>
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
		$this->kclass->page['title'] .= ' - 恢复 - '.$sample['title'];
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - <a href="/s.php?module=sample&action=view&sampleid='.$sample['sampleid'].'">'.$sample['title'].'</a> - 恢复样品', 'right' => '<a href="/s.php?module=sample">返回列表</a>', 'body'=>$body));
	}
	//
	function _cells(){
		$this->kclass->input['page'] = intval($this->kclass->input['page']);
		if($this->kclass->input['page'] <= 0){
			$this->kclass->input['page'] = $this->kclass->myGetcookie($this->kclass->input['module'].'page');
			if($this->kclass->input['page']<=0){
				$this->kclass->input['page'] = 1;
			}
		}
		if ($perpage == '') {
			$perpage = 4;
		}
		$condition = "`sample`.killed=0";
		$caption = array();
		if($this->kclass->input['q'] != ''){
			$this->kclass->input['q'] = rawurldecode($this->kclass->input['q']);
			$condition .= " AND (INSTR(LCASE(`sample`.sampleno),'".(strtolower($this->kclass->input['q']))."')>0 OR INSTR(LCASE(`sample`.title),'".(strtolower($this->kclass->input['q']))."')>0)";
			$caption[] = '<b>样品编号</b>或<b>名称</b>包含<b>'.$this->kclass->input['q'].'</b>';
		}
		if($this->kclass->input['sampleno'] != ''){
			$this->kclass->input['sampleno'] = rawurldecode($this->kclass->input['sampleno']);
			$condition .= " AND INSTR(LCASE(sample.sampleno),'".(strtolower($this->kclass->input['sampleno']))."')>0";
			$caption[] = '<b>样品编号</b>包含<b>'.$this->kclass->input['sampleno'].'</b>';
		}
		if($this->kclass->input['title'] != ''){
			$this->kclass->input['title'] = rawurldecode($this->kclass->input['title']);
			$condition .= " AND INSTR(LCASE(sample.title),'".(strtolower($this->kclass->input['title']))."')>0";
			$caption[] = '<b>名称</b>包含<b>'.$this->kclass->input['title'].'</b>';
		}
		if (intval($this->kclass->input['typeid']) > 0) {
			$condition .= " AND sample.typeid = '".$this->kclass->input['typeid']."'";
			$type = $this->kclass->DB_site->query_first("
				SELECT title
				FROM `type`
				WHERE typeid = '".$this->kclass->input['typeid']."'
			");
			$caption[] = '<b>类别</b>是<b>'.$type['title'].'</b>';
		}
		if (intval($this->kclass->input['supplierid']) > 0) {
			$condition .= " AND sample.supplierid = '".$this->kclass->input['supplierid']."'";
			$supplier = $this->kclass->DB_site->query_first("
				SELECT title
				FROM `supplier`
				WHERE supplierid = '".$this->kclass->input['supplierid']."'
			");
			$caption[] = '<b>供应商</b>是<b>'.$supplier['title'].'</b>';
		}
		if (intval($this->kclass->input['customerid']) > 0) {
			$condition .= " AND sample.customerid = '".$this->kclass->input['customerid']."'";
			$customer = $this->kclass->DB_site->query_first("
				SELECT title
				FROM `customer`
				WHERE customerid = '".$this->kclass->input['customerid']."'
			");
			$caption[] = '<b>供应商</b>是<b>'.$customer['title'].'</b>';
		}
		if($this->kclass->input['orderby'] != ''){
			$orderby = $this->kclass->input['orderby'];
		}else{
			$orderby = '`sample`.modified';
		}
		if($this->kclass->input['direction'] != ''){
			$direction = $this->kclass->input['direction'];
		}else{
			$direction = 'DESC';
		}
		$samples = $this->kclass->DB->query("
			SELECT `sample`.sampleid, `sample`.title, `sample`.entitle, `sample`.typeid, `sample`.sampleno, `sample`.attachs, `sample`.images, `sample`.modified, `sample`.created, `sample`.killed,
				attr.title AS attr,
				m.username AS modifier, c.username AS creator
			FROM `sample`
			LEFT JOIN `attr` ON (attr.attrid=sample.attrid)
			LEFT JOIN `user` AS m ON (m.userid=`sample`.modifier)
			LEFT JOIN `user` AS c ON (c.userid=`sample`.creator)
			ORDER BY `sample`.sampleid ASC, `sample`.modified DESC, `sample`.created DESC
			LIMIT ".$this->kclass->pagination(array())."
		");



		$counter = $this->kclass->DB_site->query_first("
			SELECT COUNT(productid) AS total
			FROM product
			WHERE $condition
		");
		$page->set($perpage, $counter['total'], $this->kclass->input['page']);

	}


	//样品单列表
	function mmlist(){
		$flag = false;
		//样品名称
		$this->kclass->input['sampleno']=trim($this->kclass->input['sampleno']);
		if(strlen($this->kclass->input['sampleno'])>3){
			if($flag){
				$condition.=" AND";
			}
			$condition.=" INSTR(LCASE(`sample`.sampleno),'".(strtolower($this->kclass->input['sampleno']))."')>0";
			$flag = true;
		}

		//客户名称
		$this->kclass->input['customer']=trim($this->kclass->input['customer']);
		if(strlen($this->kclass->input['customer'])>3){
			$customers=$this->kclass->DB->query("SELECT customerid FROM `customer` WHERE INSTR(LCASE(`customer`.title),'".(strtolower($this->kclass->input['customer']))."')>0");
			if($this->kclass->DB->numRows()>0){
				while($customer=$this->kclass->DB->fetchArray($customers)){
					$customerid[]=$customer['customerid'];
				}
				if($flag){
					$condition.=" AND";
				}
				$condition.=" `sample`.customerid IN (".implode(',', $customerid).")";
				$flag = true;
				$query['customer']=$this->kclass->input['customer'];
			}
		}

		//样品类型

		if(count($this->kclass->input['typeid'])){
			if(is_array($this->kclass->input['typeid'])){
				foreach ($this->kclass->input['typeid'] as $key => $val){
					if($key==0){
						if($flag){
							$condition.=" AND";
						}
						$condition.=" INSTR(LCASE(`sample`.typeid),'".$val."')>0";
						$flag = true;
					}else{
						$condition.=" OR INSTR(LCASE(`sample`.typeid),'".$val."')>0";
					}
				}
			}else{
				if($flag){
					$condition.=" AND";
				}
				$condition.=" INSTR(LCASE(`sample`.typeid),'".$val."l";
				$flag = true;
			}
		}

		//样品所属
		if($this->kclass->input['attrid']>0){
			if($flag){
				$condition.=" AND";
			}
			$condition.=" `sample`.attrid = '".$this->kclass->input['attrid']."'";
			$flag = true;
		}

		//运输方式
		if($this->kclass->input['director']>0){
			if($flag){
				$condition.=" AND";
			}
			$condition.=" `sample`.director = '".$this->kclass->input['director']."'";
			$flag = true;
		}

		//运输方式
		if($this->kclass->input['tracker']>0){
			if($flag){
				$condition.=" AND";
			}
			$condition.=" `sample`.tracker = '".$this->kclass->input['tracker']."'";
			$flag = true;
		}

		//运输方式
		if($this->kclass->input['shipmethodid']>0){
			if($flag){
				$condition.=" AND";
			}
			$condition.=" `sample`.shipmethodid = '".$this->kclass->input['shipmethodid']."'";
			$flag = true;
		}

		//递送日期

		$this->kclass->input['deliveryDate']=trim($this->kclass->input['deliveryDate']);
		if($this->kclass->input['deliveryDate']!=''){
			$year = substr($this->kclass->input['deliveryDate'],0,4);
			$month = substr($this->kclass->input['deliveryDate'],5,2);
			$day = substr($this->kclass->input['deliveryDate'],8,2);
			if (checkdate($month,$day,$year)) {
				$deliveryDate = $this->kclass->input['deliveryDate'] = mktime(0,0,0,$month,$day,$year);
			}
			if($this->kclass->input['deliveryDate']>0){
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
				if($flag){
					$condition.=" AND";
				}
				$condition .= " `sample`.deliveryDate".$shipDateline."'".$deliveryDate."'";
				$flag = true;
				$query['deliveryDate']=$this->kclass->input['deliveryDate'];
				$query['shipDateline']=$this->kclass->input['shipDateline'];
			}
		}

		//修改日期
		if($this->kclass->input['startModDate']!=''){
			$year = substr($this->kclass->input['startModDate'],0,4);
			$month = substr($this->kclass->input['startModDate'],5,2);
			$day = substr($this->kclass->input['startModDate'],8,2);
			if (checkdate($month,$day,$year)) {
				$this->kclass->input['startModDate'] = mktime(0,0,0,$month,$day,$year);
			}
			if($this->kclass->input['startModDate']>0){
				if($flag){
					$condition.=" AND";
				}
				$condition .= " `sample`.modified >='".$this->kclass->input['startModDate']."'";
				$flag = true;
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
				if($flag){
					$condition.=" AND";
				}
				$condition .= " `sample`.modified <='".$this->kclass->input['endModDate']."'";
				$flag = true;
			}
		}

		//创建时期
		if($this->kclass->input['startCreateDate']!=''){
			$year = substr($this->kclass->input['startCreateDate'],0,4);
			$month = substr($this->kclass->input['startCreateDate'],5,2);
			$day = substr($this->kclass->input['startCreateDate'],8,2);
			if (checkdate($month,$day,$year)) {
				$this->kclass->input['startCreateDate'] = mktime(0,0,0,$month,$day,$year);
			}
			if($this->kclass->input['startCreateDate']>0){
				if($flag){
					$condition.=" AND";
				}
				$condition .= " `sample`.created >='".$this->kclass->input['startCreateDate']."'";
				$flag = true;
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
				if($flag){
					$condition.=" AND";
				}
				$condition .= " `sample`.created <='".$this->kclass->input['endCreateDate']."'";
				$flag = true;
			}
		}

		//排序方式
		if($this->kclass->input['orderby']!=''){
			$orderBy .= ' ORDER BY `sample`.'.$this->kclass->input['orderby'];
		}else{

			$orderBy .= ' ORDER BY `sample`.sampleid ';
		}
		if($this->kclass->input['direction']!=''){
			$orderBy .= ' '.$this->kclass->input['direction'];
		}else{
			$orderBy .=' DESC';
		}
		if($this->kclass->input['show']=='all'){
			$condition =' 1=1';
		}else{
			if($flag){
				$condition.=" AND";
			}
			$condition .=' `sample`.killed=0';
			$flag = true;
		}

		if($this->kclass->input['start']==''){
			$b['start']=0;
		}else{
			$b['start']=$this->kclass->input['start'];
		}
		if($this->kclass->input['perpage']=='')$b['perpage']=20;
		if($this->kclass->input['show']!='')$query['show']=$this->kclass->input['show'];

		$samples = $this->kclass->DB->query("
			SELECT `sample`.sampleid, `sample`.title, `sample`.entitle,`sample`.customerid,`sample`.typeid, `sample`.sampleno,`sample`.ifVerify,`sample`.ifApprove,`sample`.ifChooser,  `sample`.attachs,`sample`.deliveryDate, `sample`.cover, `sample`.images, `sample`.modified, `sample`.created, `sample`.killed,
				attr.title AS attr,
				shipmethod.title AS shipmethod,
				m.username AS modifier, c.username AS creator, d.username AS director, t.username AS tracker
			FROM `sample`
			LEFT JOIN `attr` ON (attr.attrid=sample.attrid)
			LEFT JOIN `shipmethod` ON (shipmethod.shipmethodid=sample.shipmethodid)
			LEFT JOIN `user` AS m ON (m.userid=`sample`.modifier)
			LEFT JOIN `user` AS c ON (c.userid=`sample`.creator)
			LEFT JOIN `user` AS d ON (d.userid=`sample`.director)
			LEFT JOIN `user` AS t ON (t.userid=`sample`.tracker)
			WHERE ".$condition.$orderBy." 
			LIMIT ".$b['start'].",".$b['perpage']."
			");
		if($this->kclass->DB->numRows()){
			$body = '<table class="hundred tablesorter mytable"><thead><tr><th width="50">ID号</th><th width="100">样品单号</th><th width="80">样品类型</th><th width="300">样品单状态</th><th>客户名称</th><th width="75">递送方式</th><th width="80">递送日期</th><th width="75">业务人员</th><th width="75">跟单人员</th><th width="120">创建日期</th><th width="120">修改日期</th><th width="60">创建者</th><th width="60">修改者</th><th width="36">操作</th></tr></thead><tbody>';
			$i=1;
			while($sample = $this->kclass->DB->fetchArray($samples)){
				$sampleType='';
				if($sample['typeid']!=''){
					$stypes = $this->kclass->DB->query("SELECT typeid, title FROM `sampletype` WHERE typeid IN (".$sample['typeid'].")");
					if($this->kclass->DB->numRows()){
						while($stype = $this->kclass->DB->fetchArray($stypes)){
							$sampleType .= '<span title="('.$stype['entitle'].') '.$stype['remark'].'" style="padding-right:12px">'.$stype['title'].'</span>';
						}
					}
				}
				$customer = $this->kclass->relatedCustomer(array('id'=>$sample['customerid']));
				$deliveryDate=' <span class="darkred">'.date('Y-m-d', $sample['deliveryDate']).'</span>';
				$created = date('Y-m-d H:i', $sample['created']);
				$modified = date('Y-m-d H:i', $sample['modified']);
				$status='';
				if($sample['ifVerify']==0){
					$status = '<span class="darkred">未审核　未审批</span>　（<a href="/s.php?module=sample&action=verify&sampleid='.$sample['sampleid'].'">审核</a>）';
				}elseif($sample['ifVerify']==-1){
					$status = '<span class="red">审核未通过</span>　<span class="darkred">未审批</span>';
				}elseif($sample['ifVerify']==1 AND $sample['ifApprove']==0){
					$status = '<span class="green">审核通过</span>　<span class="darkred">未审批</span>　（<a href="/s.php?module=sample&action=approve&sampleid='.$sample['sampleid'].'">审批</a>）';
				}elseif($sample['ifVerify']==1 AND $sample['ifApprove']==-1){
					$status = '<span class="green">审核通过</span>　<span class="red">审批未通过</span>';
				}elseif($sample['ifVerify']==1 AND $sample['ifApprove']==1){
					$status = '<span class="green">审核通过　审批通过</span>';
				}
				$status.=$this->kclass->iif($sample['ifChooser']==1,'　<span class="green">已投产</span>','　<span class="darkred">未投产</span>');
				$outbound=$this->kclass->DB->queryFirst("
					SELECT outboundid
					FROM outbound
					WHERE killed=0 AND ifComplete=2 AND sampleid='".$sample['sampleid']."'
				");
				$status.=$this->kclass->iif($outbound=='','　<span class="darkred">未出库</span>','　<span class="green">已出库</span>');
				$body .= '
				<tr class="'.$this->kclass->rotateLine().'">
					<td>'.$i.'</td>
					<td><a class="tip" href="/s.php?module=sample&action=view&sampleid='.$sample['sampleid'].'" title="'.$sample['title'].'">'.$sample['sampleno'].'</a></td>
					<td>'.$sampleType.'</td>
					<td>'.$status.'</td>
					<td>'.$customer['link'].'</td>
					<td>'.$sample['shipmethod'].'</td>
					<td>'.$deliveryDate.'</td>
					<td>'.$sample['director'].'</td>
					<td>'.$sample['tracker'].'</td>
					<td>'.$created.'</td>
					<td>'.$modified.'</td>
					<td>'.$sample['creator'].'</td>
					<td>'.$sample['modifier'].'</td>
					<td>'.$this->kclass->iif($sample['killed']>0,'<a href="/s.php?module=sample&action=restore&sampleid='.$sample['sampleid'].'&rt=list">恢复</a>','<a href="/s.php?module=sample&action=kill&sampleid='.$sample['sampleid'].'&rt=list" onclick="return confirm(\'你确定要删除这个样品 '.$sample['title'].' 吗？\');">删</a> <a href="/s.php?module=sample&action=update&sampleid='.$sample['sampleid'].'&rt=list">改</a>').'</td>
				</tr>';
				$i++;
			}
			$body .= '</tbody></table>';
		}else{
			$body = '暂无相关记录。';
		}

		$pcount=$this->kclass->DB->queryFirst("SELECT COUNT(sampleid) AS count FROM `sample` WHERE ".$condition." ");
		$body.='<div class="clear"><span class="gray small left">(共<span class="darkred">'.$pcount['count'].'</span>组记录)</span></div>';
		$prestart=$b['start']-$b['perpage'];
		if($prestart>=0)$pre='<a href="/s.php?module=sample&action=list&show='.$query['show'].'&start='.$prestart.'"><-前一页</a>　';
		$aftstart=$b['start']+$b['perpage'];
		if($aftstart<$pcount['count'])$aft='　<a href="/s.php?module=sample&action=list&show='.$query['show'].'&start='.$aftstart.'">后一页-></a>';
		$body.='<div class="clear center"><span class=" middle bold">'.$pre.$aft.'</span></div>';

		$this->kclass->page['onload'] .= "dc.tabhover();dc.tips();$('.mytable').fixedtableheader();$('.tablesorter').tablesorter( {sortList: [[0,0]], headers: { 2: { sorter: false}, 13: {sorter: false} } } );";
		$this->kclass->page['title'] .= ' - 列表';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 列表', 'right' => '<span class="small">显示方式：</span><a href="/s.php?module=sample">默认列表</a>　<a href="/s.php?module=sample&action=list&show=all">所有<span class="small gray">(包括删除)</span></a>　|　<a href="/s.php?module=sample&action=add">新建</a>　<a href="/s.php?module=sample&action=addOld">新建旧样品单</a>', 'body'=>$body));
	}

	/**
	 * 根据字段查找样品单
	 */
	function find(){
		$director = $this->kclass->chooserTracker(array('name'=>'director','condition'=>'positionid=7','hasBlank'=>1,'width'=>450));
		$tracker = $this->kclass->chooserTracker(array('name'=>'tracker','condition'=>'positionid=7','hasBlank'=>1,'width'=>450));
		$sampletype = $this->kclass->chooserSampleType(array('name'=>'typeid', 'width'=>350, 'selectedid'=>$this->kclass->input['typeid']));
		$attr = $this->kclass->chooserAttr(array('name'=>'attrid', 'selectedid'=>1));
		$shipmethod = $this->kclass->chooserShipmethod(array('name'=>'shipmethodid', 'hasBlank'=>1, 'width'=>300, 'selectedid'=>$order['shipmethodid']));
$body = <<<EOF
<form action="/s.php?module=sample&action=list" name="sample" method="post">
<input type="hidden" name="module" value="sample">
<input type="hidden" name="action" value="list">
<table>
<thead>
<tr>
	<th colspan="2">查找样品单</th>
</tr>
</thead>
<tbody>
<tr class="even">
	<td>样品单单号 包含：</td>
	<td><input type="text" style="width:150px" name="sampleno" value=""><span class="small gray">至少要填写四个字符以上,如 0730 月份和日期</span></td>
</tr>
<tr class="odd">
	<td>样品单客户 包含：</td>
	<td><input type="text" style="width:150px" name="customer" value=""> <span class="small gray">可使用订单中客户名称中的关键字查找，大于3个字符</span></td>
</tr>
<tr class="even">
	<td>且 样品类型 是：</td>
	<td>{$sampletype}</td>
</tr>
<tr class="odd">
	<td>且 样品所属 是：</td>
	<td>{$attr}</td>
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
	<td title="Shipment Date">且 递送日期：</td>
	<td>
		<label for="s0"><input type="radio" name="shipDateline" value="before" id="s0" checked>早于</label>
		<label for="s1"><input type="radio" name="shipDateline" value="equal" id="s1">等于</label>
		<label for="s2"><input type="radio" name="shipDateline" value="after" id="s2">晚于</label>
		<input type="text" style="width:150px" id="shipmentDate" name="deliveryDate" value="">
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
		<label for="o0"><input type="radio" name="orderby" value="sampleno" id="o0" checked>样品单编号</label>
		<label for="o1"><input type="radio" name="orderby" value="sampleid" id="o1">ID</label>
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
		<label for="s0"><input type="radio" id="s0" name="show" value="" checked>默认列表</label>
		<label for="s1"><input type="radio" id="s1" name="show" value="all">所有<span class="small gray">(包括删除)</span></label>
	</td>
</tr>
<!--
<tr class="odd">
	<td>列表方式：</td>
	<td disabled>
		<label for="t0"><input type="radio" id="t0" name="layout" value="grid">格子</label>
		<label for="t1"><input type="radio" id="t1" name="layout" value="list" checked>列表</label>
	</td>
</tr>
-->
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
		$this->kclass->page['onload'] .= 'dc.tabhover();$(\'#shipmentDate\').datepicker();var dates=$(\'#startDate,#endDate\').datepicker({onSelect:function(selectedDate){var option=this.id==\'startDate\'?\'minDate\':\'maxDate\',instance=$(this).data(\'datepicker\'),date=$.datepicker.parseDate(instance.settings.dateFormat || $.datepicker._defaults.dateFormat,selectedDate,instance.settings);dates.not(this).datepicker(\'option\',option,date);}});var dates2=$(\'#startCreateDate,#endCreateDate\').datepicker({onSelect:function(selectedDate){var option2=this.id==\'startCreateDate\'?\'minDate\':\'maxDate\',instance2=$(this).data(\'datepicker\'),date2=$.datepicker.parseDate(instance2.settings.dateFormat || $.datepicker._defaults.dateFormat,selectedDate,instance2.settings);dates2.not(this).datepicker(\'option\',option2,date2);}});';
		$this->kclass->page['title'] .= ' - 查找样品单';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 查找样品单', 'right' => '<a href="/s.php?module=sample">返回列表</a>', 'body'=>$body));

	}


	//
	function doupdateEvent(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			if($this->kclass->input['direction']==''){
				$e = '<li>请选择样品收发的类型。</li>';
			}
			if($this->kclass->input['deliveryDate'] == ''){
				$e .= '<li>请填写样品收到或发出的日期。</li>';
			}
			if($this->kclass->input['shipmethodid'] == 0){
				$e .= '<li>请选择样品递送的方式。</li>';
			}
			if($this->kclass->input['deliveryFee'] == ''){
				$e .= '<li>请填写样品递送的费用，如无费用，请填写“0”。</li>';
			}
			if($this->kclass->input['sampleFee'] == ''){
				$e .= '<li>请填写样品费用，如无费用，请填写“0”。</li>';
			}
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '修改样品单收发与费用',
				'text' => '您在修改样品单收发与费用的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$deliveryDate=0;
		if($this->kclass->input['deliveryDate']!=''){
			$dd = explode('-', $this->kclass->input['deliveryDate']);
			$deliveryDate = mktime(0,0,0,$dd[1],$dd[2],$dd[0]);
		}
		$this->kclass->DB->query("
			UPDATE `sampleevent` SET
				`direction`='".$this->kclass->input['direction']."',
				`totalQuantity`='".$this->kclass->input['totalQuantity']."',
				`deliveryDate`='".$deliveryDate."',
				`deliveryCurrency`='".$this->kclass->input['deliveryCurrency']."',
				`deliveryFee`='".$this->kclass->input['deliveryFee']."',
				`deliveryCaption`='".$this->kclass->input['deliveryCaption']."',
				`shipmethodid`='".$this->kclass->input['shipmethodid']."',
				`sampleCurrency`='".$this->kclass->input['sampleCurrency']."',
				`sampleFee`='".$this->kclass->input['sampleFee']."',
				`sampleCaption`='".$this->kclass->input['sampleCaption']."',
				`remark`='".$this->kclass->input['remark']."',
				`modified`='".TIMENOW."',
				`modifier`='".$this->kclass->member['userid']."'
			WHERE eventid='".$this->kclass->input['eventid']."'
		");

		$this->kclass->messager(array(
			'title' => '修改样品单收发与费用',
			'text' => '修改样品单收发与费用成功!',
			'url' => '/s.php?module=sample&action=view&sampleid='.$this->kclass->input['sampleid'],
			'sec' => 2
		));
	}

	//只要在彼此的心里还有一个地方……希望明天会更好！
	function updateEvent(){
		if($this->kclass->input['eventid']<=0 OR !$event = $this->kclass->DB->queryFirst("SELECT `event`.*,sample.title FROM `sampleevent` AS `event` LEFT JOIN `sample` ON (`sample`.sampleid=`event`.sampleid) WHERE `eventid`='".$this->kclass->input['eventid']."'")){
			$this->kclass->boinkIt('/s.php?module=sample');
		}
		$deliveryDate = date('Y-m-d', $event['deliveryDate']);
		$shipmethod = $this->kclass->chooserShipmethod(array('name'=>'shipmethodid', 'hasBlank'=>1, 'width'=>350, 'selectedid'=>$event['shipmethodid']));
		$sampleCurrency = $this->kclass->chooserCurrency(array('name'=>'sampleCurrency', 'hasBlank'=>1, 'width'=>100, 'selectedid'=>$event['sampleCurrency']));
		$deliveryCurrency = $this->kclass->chooserCurrency(array('name'=>'deliveryCurrency', 'hasBlank'=>1, 'width'=>100, 'selectedid'=>$event['deliveryCurrency']));
		$t1 = $d1=' checked';
		if($event['direction']=='sent'){
			$d2=$d1;
			$d1='';
		}
		if($event['totalQuantity']=='part'){
			$t2=$t1;
			$t1='';
		}
		$deliveryDate=date('Y-m-d', $event['deliveryDate']);
$body = <<<EOF
<form action="/s.php?module=sample&action=doupdateEvent" name="sample" method="post">
<input type="hidden" name="module" value="sample">
<input type="hidden" name="action" value="doupdateEvent">
<input type="hidden" name="sampleid" value="{$event['sampleid']}">
<input type="hidden" name="eventid" value="{$event['eventid']}">
<table class="hundred">
<thead>
<tr>
	<th colspan="8">新建样品收发与费用：</th>
</tr>
</thead>
<tbody>
<tr class="odd">
	<td>收发类型：<span class="red bold">*</span></td>
	<td>
		<label for="direction1"><input type="radio" id="direction1" name="direction" value="received"{$d1}>收到</label>
		<label for="direction2"><input type="radio" id="direction2" name="direction" value="sent"{$d2}>发出</label>
	</td>
	<td>收发数量：<span class="red bold">*</span></td>
	<td>
		<label for="totalQuantity1"><input type="radio" id="totalQuantity1" name="totalQuantity" value="all"{$t1}>样品单全部数量</label>
		<label for="totalQuantity2"><input type="radio" id="totalQuantity2" name="totalQuantity" value="part"{$t2}>部分<span class="gray small">(请在备注中注明)</span></label>
	</td>
</tr>
<tr class="even">
	<td>收发日期：<span class="red bold">*</span></td>
	<td><input type="text" style="width:350px" id="deliveryDate" name="deliveryDate" value="{$deliveryDate}"></td>
	<td>递送方式：<span class="red bold">*</span></td>
	<td>{$shipmethod}</td>
</tr>
<tr class="odd">
	<td>递送费用：<span class="red bold">*</span></td>
	<td>{$deliveryCurrency} <input type="text" style="width:100px" name="deliveryFee" value="{$event['deliveryFee']}"></td>
	<td>运费说明：</td>
	<td><input type="text" style="width:350px" name="deliveryCaption" value="{$event['deliveryCaption']}"></td>
</tr>
<tr class="even">
	<td>样品费用：<span class="red bold">*</span></td>
	<td>{$sampleCurrency} <input type="text" style="width:100px" name="sampleFee" value="{$event['sampleFee']}"></td>
	<td>样品费说明：</td>
	<td><input type="text" style="width:350px" name="sampleCaption" value="{$event['sampleCaption']}"></td>
</tr>
<tr class="odd">
	<td>备　　注：</td>
	<td colspan="3"><textarea name="remark" style="width:350px;height:70px">{$event['remark']}</textarea></td>
</tr>
<tr class="even">
	<td colspan="8" align="center">
		<input type="submit" value="  保存  " accesskey="s">
		<input type="reset" value="  复位  ">
	</td>
</tr>
<tr class="odd">
	<td colspan="8" class="small gray">注意：①如果没有费用，请填写“0”。②日期如手动填写，格式为“YYYY-mm-dd”。</td>
</tr>
</tbody>
</table>
</form>
EOF;
		$this->kclass->page['onload'] .= 'dc.tabhover();$(\'#deliveryDate\').datepicker()';
		$this->kclass->page['title'] .= ' - '.$event['title'].' - 新建样品单收发与费用';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - <a href="/s.php?module=sample&action=view&sampleid='.$event['sampleid'].'">'.$event['title'].'</a> - 新建样品单收发与费用', 'right' => '<a href="/s.php?module=sample">返回列表</a>', 'body'=>$body));
	}
	//
	function insertEvent(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			if($this->kclass->input['direction']==''){
				$e = '<li>请选择样品收发的类型。</li>';
			}
			if($this->kclass->input['deliveryDate'] == ''){
				$e .= '<li>请填写样品收到或发出的日期。</li>';
			}
			if($this->kclass->input['shipmethodid'] == 0){
				$e .= '<li>请选择样品递送的方式。</li>';
			}
			if($this->kclass->input['deliveryFee'] == ''){
				$e .= '<li>请填写样品递送的费用，如无费用，请填写“0”。</li>';
			}
			if($this->kclass->input['sampleFee'] == ''){
				$e .= '<li>请填写样品费用，如无费用，请填写“0”。</li>';
			}
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '新建样品单收发与费用',
				'text' => '您在新建样品单收发与费用的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$deliveryDate=0;
		if($this->kclass->input['deliveryDate']!=''){
			$dd = explode('-', $this->kclass->input['deliveryDate']);
			$deliveryDate = mktime(0,0,0,$dd[1],$dd[2],$dd[0]);
		}
		$this->kclass->DB->query("
			INSERT INTO `sampleevent` (`sampleid`, `direction`, `totalQuantity`, `deliveryDate`, `deliveryCurrency`, `deliveryFee`, `deliveryCaption`, `shipmethodid`, `sampleCurrency`, `sampleFee`, `sampleCaption`, `remark`, `modified`, `creator`, `created`) VALUES ('".$this->kclass->input['sampleid']."', '".$this->kclass->input['direction']."', '".$this->kclass->input['totalQuantity']."', '".$deliveryDate."', '".$this->kclass->input['deliveryCurrency']."', '".$this->kclass->input['deliveryFee']."', '".$this->kclass->input['deliveryCaption']."', '".$this->kclass->input['shipmethodid']."', '".$this->kclass->input['sampleCurrency']."', '".$this->kclass->input['sampleFee']."', '".$this->kclass->input['sampleCaption']."', '".$this->kclass->input['remark']."', '".TIMENOW."', '".$this->kclass->member['userid']."', '".TIMENOW."')
		");

		$this->kclass->messager(array(
			'title' => '新建样品单收发与费用',
			'text' => '新建样品单收发与费用成功!',
			'url' => '/s.php?module=sample&action=view&sampleid='.$this->kclass->input['sampleid'],
			'sec' => 3
		));
	}

	//
	function addEvent(){
		if($this->kclass->input['sampleid']<=0 OR !$sample = $this->kclass->DB->queryFirst("SELECT `sample`.* FROM `sample` WHERE `sampleid`='".$this->kclass->input['sampleid']."'")){
			$this->kclass->boinkIt('/s.php?module=sample');
		}
		$deliveryDate = date('Y-m-d', TIMENOW);
		$shipmethod = $this->kclass->chooserShipmethod(array('name'=>'shipmethodid', 'hasBlank'=>1, 'width'=>350, 'selectedid'=>5));
		$sampleCurrency = $this->kclass->chooserCurrency(array('name'=>'sampleCurrency', 'hasBlank'=>1, 'width'=>100, 'selectedid'=>3));
		$deliveryCurrency = $this->kclass->chooserCurrency(array('name'=>'deliveryCurrency', 'hasBlank'=>1, 'width'=>100, 'selectedid'=>3));
$body = <<<EOF
<form action="/s.php?module=sample&action=insertEvent" name="sample" method="post">
<input type="hidden" name="module" value="sample">
<input type="hidden" name="action" value="insertEvent">
<input type="hidden" name="sampleid" value="{$sample['sampleid']}">
<table class="hundred">
<thead>
<tr>
	<th colspan="8">新建样品收发与费用：</th>
</tr>
</thead>
<tbody>
<tr class="odd">
	<td>收发类型：<span class="red bold">*</span></td>
	<td>
		<label for="direction1"><input type="radio" id="direction1" name="direction" value="received">收到</label>
		<label for="direction2"><input type="radio" id="direction2" name="direction" value="sent">发出</label>
	</td>
	<td>收发数量：<span class="red bold">*</span></td>
	<td>
		<label for="totalQuantity1"><input type="radio" id="totalQuantity1" name="totalQuantity" value="all">样品单全部数量</label>
		<label for="totalQuantity2"><input type="radio" id="totalQuantity2" name="totalQuantity" value="part">部分<span class="gray small">(请在备注中注明)</span></label>
	</td>
</tr>
<tr class="even">
	<td>收发日期：<span class="red bold">*</span></td>
	<td><input type="text" style="width:350px" id="deliveryDate" name="deliveryDate" value="{$deliveryDate}"></td>
	<td>递送方式：<span class="red bold">*</span></td>
	<td>{$shipmethod}</td>
</tr>
<tr class="odd">
	<td>递送费用：<span class="red bold">*</span></td>
	<td>{$deliveryCurrency} <input type="text" style="width:100px" name="deliveryFee" value="{$event['deliveryFee']}"></td>
	<td>运费说明：</td>
	<td><input type="text" style="width:350px" name="deliveryCaption" value="{$event['deliverCaption']}"></td>
</tr>
<tr class="even">
	<td>样品费用：<span class="red bold">*</span></td>
	<td>{$sampleCurrency} <input type="text" style="width:100px" name="sampleFee" value="{$event['sampleFee']}"></td>
	<td>样品费说明：</td>
	<td><input type="text" style="width:350px" name="sampleCaption" value="{$event['sampleCaption']}"></td>
</tr>
<tr class="odd">
	<td>备　　注：</td>
	<td colspan="3"><textarea name="remark" style="width:350px;height:70px">{$event['remark']}</textarea></td>
</tr>
<tr class="even">
	<td colspan="8" align="center">
		<input type="submit" value="  保存  " accesskey="s">
		<input type="reset" value="  复位  ">
	</td>
</tr>
<tr class="odd">
	<td colspan="8" class="small gray">注意：①如果没有费用，请填写“0”。②日期如手动填写，格式为“YYYY-mm-dd”。</td>
</tr>
</tbody>
</table>
</form>
EOF;
		$this->kclass->page['onload'] .= 'dc.tabhover();$(\'#deliveryDate\').datepicker()';
		$this->kclass->page['title'] .= ' - '.$sample['title'].' - 新建样品单收发与费用';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - <a href="/s.php?module=sample&action=view&sampleid='.$sample['sampleid'].'">'.$sample['title'].'</a> - 新建样品单收发与费用', 'right' => '<a href="/s.php?module=sample">返回列表</a>', 'body'=>$body));
	}
	//
	function killEvent(){
		if($this->kclass->input['sampleid']<=0 OR $this->kclass->input['eventid']<=0){
			$this->kclass->boinkIt('/s.php?module=sample');
		}
		if($this->kclass->input['confirm'] == 0){
			$this->kclass->boinkIt('/s.php?module=sample&action=view&sampleid='.$this->kclass->input['sampleid']);
		}
		if($this->kclass->input['sampleid'] < 0 OR $this->kclass->input['eventid'] < 0){
			$e = '<li>请从正确的页面进行操作，如果此问题持续存在，请联系网站管理员。';
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '删除样品单中的收发与费用记录',
				'text' => '您在删除样品单中的收发与费用记录的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$sample = $this->kclass->DB->queryFirst("
			SELECT title
			FROM sample
			WHERE sampleid='".$this->kclass->input['sampleid']."'
		");
		if($sample){
			$this->kclass->DB->query("
				UPDATE `sampleevent`
				SET killed=".TIMENOW."
				WHERE eventid='".$this->kclass->input['eventid']."'
			");
			$this->kclass->messager(array(
				'title' => '删除样品单中的收发与费用记录成功',
				'text' => '样品 <b>'.$sample['title'].'</b> 下的相关 收发与费用记录 已成功被标记为删除!',
				'url' => '/s.php?module=sample&action=view&sampleid='.$this->kclass->input['sampleid'],
				'sec' => 2
			));
		}else{
			$this->kclass->messager(array(
				'title' => '删除样品单中的收发与费用记录失败',
				'text' => '您要删除的样品或收发与费用记录，不存在！',
				'url' => '/s.php?module=sample&action=view&sampleid='.$this->kclass->input['sampleid'],
				'sec' => 3
			));
		}
	}

	//
	function removeEvent(){
		if($this->kclass->input['eventid']<=0){
			$this->kclass->boinkIt('/s.php?module=sample');
		}
		$event = $this->kclass->DB->queryFirst("
			SELECT `event`.eventid, `event`.sampleid,
				`sample`.title
			FROM `sampleevent` AS `event`
			LEFT JOIN `sample` ON (`sample`.sampleid=`event`.sampleid)
			WHERE `event`.eventid='".$this->kclass->input['eventid']."'
		");
		if($event){
$body = <<<EOF
<form action="/s.php?module=sample&action=killEvent" name="sample" method="post">
<input type="hidden" name="module" value="sample">
<input type="hidden" name="action" value="killEvent">
<input type="hidden" name="sampleid" value="{$event['sampleid']}">
<input type="hidden" name="eventid" value="{$event['eventid']}">
<table>
<thead>
<tr>
	<th>删除样品：{$sample['title']}</th>
</tr>
</thead>
<tbody>
<tr class="even">
	<td class="middle">你确定要删除样品单 <a href="/s.php?module=sample&action=view&sampleid={$event['sampleid']}" class="big" target="_blank">{$event['title']}</a> 下的 <span class="big bold darkred">收发与费用</span> 记录吗?</td>
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
			$body = '您要删除的样品单收发与费用记录并不存在，请返回列表刷新后再进行操作。（如果问题持续出现，请联系管理员。）';
		}
		$this->kclass->page['title'] .= ' - '.$event['title'].' - 删除收发与费用记录';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - <a href="/s.php?module=sample&action=view&sampleid='.$event['sampleid'].'">'.$event['title'].'</a> - 删除收发与费用记录', 'right' => '<a href="/s.php?module=sample">返回列表</a>', 'body'=>$body));
	}
	//
	function revivalEvent(){
		if($this->kclass->input['sampleid']<=0 OR $this->kclass->input['eventid']<=0){
			$this->kclass->boinkIt('/s.php?module=sample');
		}
		if($this->kclass->input['confirm'] == 0){
			$this->kclass->boinkIt('/s.php?module=sample&action=view&sampleid='.$this->kclass->input['sampleid']);
		}
		if($this->kclass->input['sampleid'] < 0 OR $this->kclass->input['eventid'] < 0){
			$e = '<li>请从正确的页面进行操作，如果此问题持续存在，请联系网站管理员。';
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '恢复样品单中的收发与费用记录',
				'text' => '您在恢复样品单中的收发与费用记录的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$sample = $this->kclass->DB->queryFirst("
			SELECT title
			FROM sample
			WHERE sampleid='".$this->kclass->input['sampleid']."'
		");
		if($sample){
			$this->kclass->DB->query("
				UPDATE `sampleevent`
				SET killed=".TIMENOW."
				WHERE eventid='".$this->kclass->input['eventid']."'
			");
			$this->kclass->messager(array(
				'title' => '恢复样品单中的收发与费用记录成功',
				'text' => '样品 <b>'.$sample['title'].'</b> 下的相关 收发与费用记录 已成功被标记为恢复!',
				'url' => '/s.php?module=sample&action=view&sampleid='.$this->kclass->input['sampleid'],
				'sec' => 2
			));
		}else{
			$this->kclass->messager(array(
				'title' => '恢复样品单中的收发与费用记录失败',
				'text' => '您要恢复的样品或收发与费用记录，不存在！',
				'url' => '/s.php?module=sample&action=view&sampleid='.$this->kclass->input['sampleid'],
				'sec' => 3
			));
		}
	}

	//
	function restoreEvent(){
		if($this->kclass->input['eventid']<=0){
			$this->kclass->boinkIt('/s.php?module=sample');
		}
		$event = $this->kclass->DB->queryFirst("
			SELECT `event`.eventid, `event`.sampleid,
				`sample`.title
			FROM `sampleevent` AS `event`
			LEFT JOIN `sample` ON (`sample`.sampleid=`event`.sampleid)
			WHERE `event`.eventid='".$this->kclass->input['eventid']."'
		");
		if($event){
$body = <<<EOF
<form action="/s.php?module=sample&action=revivalEvent" name="sample" method="post">
<input type="hidden" name="module" value="sample">
<input type="hidden" name="action" value="revivalEvent">
<input type="hidden" name="sampleid" value="{$event['sampleid']}">
<input type="hidden" name="eventid" value="{$event['eventid']}">
<table>
<thead>
<tr>
	<th>恢复样品：{$sample['title']}</th>
</tr>
</thead>
<tbody>
<tr class="even">
	<td class="middle">你确定要恢复样品: <a href="/s.php?module=sample&action=view&sampleid={$event['sampleid']}" class="big bold" target="_blank">{$event['title']}</a> 吗?</td>
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
			$body = '您要删除的样品单收发与费用记录并不存在，请返回列表刷新后再进行操作。（如果问题持续出现，请联系管理员。）';
		}
		$this->kclass->page['title'] .= ' - '.$event['title'].' - 恢复收发与费用记录';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - <a href="/s.php?module=sample&action=view&sampleid='.$event['sampleid'].'">'.$event['title'].'</a> - 恢复收发与费用记录', 'right' => '<a href="/s.php?module=sample">返回列表</a>', 'body'=>$body));
	}

	function according(){
		$director=$this->kclass->chooserTracker(array('name'=>'director','condition'=>'positionid=7','hasBlank'=>1,'width'=>450));
		$body=<<<EOF
<form action="/p.php?module=printer&action=accordinglist" method="post">
<input type="hidden" name="module" value="printer">
<input type="hidden" name="action" value="accordinglist">
<table>
<thead>
<tr>
<th colspan="2">统计样品单，请选择统计方式</th>
</tr>
</thead>
<tbody>
<tr class="odd">
<td>你要选择的时间范围是：</td>
<td>开始时间：<input type='text' id='startDate' name='startDate' value=''>结束时间：<input type='text' id='endDate' name='endDate' value=''></td>
</tr>
<tr class="odd" nohover>
<tr class="odd">
<td>你要统计的样品型号是：</td>
<td><input type='productnum' id='productnum' name='productnum' value='' size='40'></td>
</tr>
<tr class="odd">
<td>你要选择的业务员是：</td>
<td>{$director}</td>
</tr>
<tr>
<th colspan="2">选项</th>
</tr>
<tr class="odd">
<td>排列方式：</td>
<td><input type='radio' id='ordertype' name='ordertype' value='1' checked='true'>国家<input type='radio' id='ordertype' name='ordertype' value='2'>业务员
<input type='radio' id='ordertype' name='ordertype' value='3'>产品型号</td>
</tr>
<tr class="odd">
<td>排列方向：</td>
<td><input type='radio' id='direction' name='direction' value='1' checked='true'>升序<input type='radio' id='direction' name='direction' value='2'>降序</td>
</tr>
<tr class="odd" nohover>
<td colspan="2" class="center" nohover>
	<input type="submit" id="submitButton" value=" 生成统计  " accesskey="s">
	<input type="reset" value="  复位  ">
</td>
</tr>
</tbody>
</table>
</form>
EOF;
			$this->kclass->page['onload'] .= 'var dates=$(\'#startDate,#endDate\').datepicker({onSelect:function(selectedDate){var option=this.id==\'startDate\'?\'minDate\':\'maxDate\',instance=$(this).data(\'datepicker\'),date=$.datepicker.parseDate(instance.settings.dateFormat || $.datepicker._defaults.dateFormat,selectedDate,instance.settings);dates.not(this).datepicker(\'option\',option,date);}});';
			$this->kclass->page['title'] .= ' - 样品单统计';
			$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . '  - 样品单统计', 'right' => '<a		href="/s.php?module=sample">返回列表</a>', 'body'=>$body));
			
	}
	function accordinglist(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e='<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '生成样品单统计表',
				'text' => '您在生成样品单统计表的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$condition='1';
		$orderby='';
		if($this->kclass->input['director']){
			$username=$this->kclass->DB->query("
				SELECT `user`.username FROM `user`
				WHERE `user`.userid='".$this->kclass->input['director']."'
			");
			if($username){
				while($usernames = $this->kclass->DB->fetchArray($username)){
					$directors=$usernames['username'];
					$condition.=' and u.username="'.$directors.'"';
				}
			}
		}
		if($this->kclass->input['productnum']){
			$productnum=trim($this->kclass->input['productnum']);
			$condition.=' and m.title="'.$productnum.'"';
		}
		if($this->kclass->input['startDate']){
			$st=explode('-',$this->kclass->input['startDate']);
			$startdate=mktime(0,0,0,$st[1],$st[2],$st[0]);
		}
		if($this->kclass->input['endDate']){
			$ed=explode('-',$this->kclass->input['endDate']);
			$enddate=mktime(0,0,0,$ed[1],$ed[2],$ed[0]);
		}
		if($this->kclass->input['startDate']!='' && $this->kclass->input['endDate']!=''){
			$condition.=' and oi.actualTime>="'.$startdate.'" and oi.actualTime<="'.$enddate.'"';
		}elseif($this->kclass->input['startDate']=='' && $this->kclass->input['endDate']!=''){
			$condition.=' and oi.actualTime<="'.$enddate.'"';
		}elseif($this->kclass->input['startDate']!='' && $this->kclass->input['endDate']==''){
			$condition.=' and oi.actualTime>="'.$startdate.'"';	
		}
		if($this->kclass->input['ordertype']){
			if($this->kclass->input['ordertype']==1){
				$orderby.='r.country';
			}elseif($this->kclass->input['ordertype']==2){
				$orderby.='u.username';
			}else{
				$orderby.='m.title';
			}
		}
		if($this->kclass->input['direction']){
			if($this->kclass->input['direction']==1){
				$paixu=' ASC';
			}else{
				$paixu=' DESC';
			}

		}
		
		$sql="SELECT distinct `sample`.sampleid,`sample`.sampleno,`sample`.customerid,`sample`.director,
				c.title AS customername, 
				r.country,
				u.username,
				i.materialid,
				i.module,
				m.title AS materialname,
				oi.outQuantity,
				oi.actualTime
			 FROM `sample` 
			 LEFT JOIN `customer` AS c ON (c.customerid=`sample`.customerid)
			 LEFT JOIN `user` AS u ON (u.userid=`sample`.director)
			 LEFT JOIN `region` AS r ON (c.regionid=r.regionid)
			 LEFT JOIN `item` AS i ON (i.mid=`sample`.sampleid and i.module='sample')
			 LEFT JOIN `material` AS m ON (i.materialid=m.materialid)
			 LEFT JOIN `outbounditem` AS oi ON (i.materialid=oi.materialid)
			 WHERE ".$condition."
			 ORDER BY ".$orderby.$paixu."
		";
		//echo $sql;
		$samplecount=$this->kclass->DB->query($sql);
		$i=1;
		$total=0;
		if($this->kclass->DB->numRows()){
			$body = '<table class="hundred tablesorter mytable"><thead><tr><th width="60">ID号</th><th width="100">样品型号</th><th width="80">样品单号</th><th width="300">业务员</th><th width="300">客户名称</th><th width="100">国家</th><th width="80">出库数量</th><th width="75">出库时间</th></tr></thead><tbody>';
			while($samplecounts = $this->kclass->DB->fetchArray($samplecount)){
				
				$actualtime=date('Y-m-d',$samplecounts['actualTime']);
				$body.="
				<tr class='".$this->kclass->rotateLine()."'>
				<td>{$i}</td>
				<td>".$samplecounts['materialname']."</td>
				<td>".$samplecounts['sampleno']."</td>
				<td>".$samplecounts['username']."</td>
				<td>".$samplecounts['customername']."</td>
				<td>".$samplecounts['country']."</td>
				<td>".$samplecounts['outQuantity']."</td>
				<td>".$this->kclass->iif($samplecounts['outQuantity']!='',date('Y-m-d',$samplecounts['actualTime']),"--")."</td>
				</tr>";
				$i++;
				$total=$total+$samplecounts['outQuantity'];
			}
			
			$body.='</tbody></table>';
			$body.='<table class="hundred"><thead><tr><td  colspan="6">最后统计结果：</td><td  colspan="2">'.$total.'</td></tr></thead></table>';
		}else{
			$body = '暂无相关记录。';
		}
		$this->kclass->page['onload'] .= "dc.tabhover();dc.tips();$('.mytable').fixedtableheader();$('.tablesorter').tablesorter( { headers: { 2: { sorter: false}, 7: {sorter: false} } } );";
		$this->kclass->page['title'] .= ' - 样品统计表';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 样品统计表', 'right' => '<span class="small">显示方式：</span><a href="/s.php?module=sample">默认列表</a>　<a href="/s.php?module=sample&action=list&show=all">所有<span class="small gray">(包括删除)</span></a>　|　<a href="/s.php?module=printer&action=orderPI">打印</a>', 'body'=>$body));
		/**/
		/*if($this->kclass->input['productnum']){
			$material=$this->kclass->DB->query("
				SELECT `material`.materialid FROM `material`
				WHERE `material`.title='".$this->kclass->input['productnum']."'
			");
			if($sum=$this->kclass->DB->numRows()){
				while($materials = $this->kclass->DB->fetchArray($material)){
					$this->kclass->DB->query("
						SELECT `outbound`.real
					
					")
				}
			}
			
		}*/
		
	}
}
?>