<?php
/**
 * 
 * @author mayinghao
 *
 */
class printer{
	function autoRun(){
		switch($this->kclass->input['action']){
			case 'list':
				$this->mmlist();
			break;
			case 'customer':
				$this->customer();
			break;
			case 'supplier':
				$this->supplier();
			break;
			case 'sample':
				$this->sample();
			break;
			case 'samplePL':
				$this->samplePL();
				break;
			case 'product':
				$this->product();
			break;
			case 'order':
				$this->order();
			break;
			case 'orderPI':
				$this->orderPI();
			break;
			case 'orderPO':
				$this->orderPO();
				break;
			case 'exportcustomerinfo':
				$this->exportcustomerinfo();
			   break;
			case 'printQstatistics':
				$this->printQstatistics();
			break;
			case 'printIstatistics':
				$this->printIstatistics();
			break;
			case 'orderstatistics':
				$this->orderstatistics();
			break;
			case 'STQuotingprint':
				$this->stquotingprint();
			break;
			case 'quotingprint':
				$this->quotingprint();
			break;
			case 'customerInquiry':
				$this->customerInquiry();
			break;
			case 'customerprint':
				$this->customerprint();
			break;
			case 'invoice':
				$this->invoice();
			break;
			case 'packingPO':
				$this->packingPO();
			break;
			case 'packingPL':
				$this->packingPL();
			break;
			case 'packinglist':
				$this->packinglist();
			break;
			case 'doupdateAll':
				$this->doupdateAll();
			break;
			case 'porder':
				$this->porder();
			break;
			case 'purchaseApply':
				$this->purchaseApply();
			break;
			case 'inventory':
				$this->inventory();
			break;
			case 'inboundApply':
				$this->inboundApply();
			break;
			case 'outboundApply':
				$this->outboundApply();
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
			case 'accordinglist':
				$this->accordinglist();
			break;
			default:
				$this->mmlist();
		}
	}
	// 
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
			$condition.=" and `sample`.director=".$this->kclass->input['director']."";
		}
		if($this->kclass->input['productnum']){
			$productnum=trim($this->kclass->input['productnum']);
			$condition.=" and `sample`.title LIKE '%".$productnum."%'";
		}
		/*if($this->kclass->input['startDate'] && $this->kclass->input['endDate']){
			$st=explode('-',$this->kclass->input['startDate']);
			$startdate=mktime(0,0,0,$st[1],$st[2],$st[0]);
			$ed=explode('-',$this->kclass->input['endDate']);
			$enddate=mktime(0,0,0,$ed[1],$ed[2],$ed[0]);
			$condition.=" and `sample`.created BETWEEN ".$startdate." AND ".$enddate.""; 
		}*/
		if($this->kclass->input['startDate']){
			$st=explode('-',$this->kclass->input['startDate']);
			$startdate=mktime(0,0,0,$st[1],$st[2],$st[0]);
			$condition.=" and `sample`.created>=".$startdate."";
		}
		if($this->kclass->input['endDate']){
			$ed=explode('-',$this->kclass->input['endDate']);
			$enddate=mktime(0,0,0,$ed[1],$ed[2],$ed[0]);
			$condition.=" and `sample`.created<=".$enddate."";
		}
		if($this->kclass->input['ordertype']){
			if($this->kclass->input['ordertype']==1){
				$orderby.='r.country';
			}elseif($this->kclass->input['ordertype']==2){
				$orderby.='u.username';
			}else{
				$orderby.='`sample`.title';
			}
		}
		if($this->kclass->input['direction']){
			if($this->kclass->input['direction']==1){
				$paixu=' ASC';
			}else{
				$paixu=' DESC';
			}
		}
		$sql="SELECT `sample`.sampleid,`sample`.sampleno,`sample`.amount,`sample`.toMessrs,`sample`.deliveryFee,`sample`.customerid,`sample`.director,`sample`.title,`sample`.created,
				c.title AS customername, 
				r.country,
				r.countryid,
				r.regionid,
				c.customerid,
				u.username
			 FROM `sample` 
			 LEFT JOIN `customer` AS c ON (c.customerid=`sample`.customerid)
			 LEFT JOIN `user` AS u ON (u.userid=`sample`.director)
			 LEFT JOIN `region` AS r ON (c.regionid=r.regionid)
			 WHERE ".$condition."
			 ORDER BY ".$orderby.$paixu." 
		";
		$samplecount=$this->kclass->DB->query($sql);
		$i=1;
		$j=0;
		$total=0;
		$lampTotal=0;
		$sampleTotalSum=0;
		$sampleDeliveryFee=0;
		$rowsNum=$this->kclass->DB->numRows();
		if($this->kclass->DB->numRows()){
			$this->kclass->page['main'] = '<table class="hundred tablesorter mytable"><thead><tr><th width="60">ID号</th><th width="80">样品单号</th><th width="100">样品信息</th><th width="100">样品灯数量</th><th width="100">业务员</th><th width="200">客户名称</th><th width="100">国家</th><th width="100">额外费用总金额</th><th width="100">总价小计</th></tr></thead><tbody>';
			$customerid=$regionid=$countryid=$director=array();
			while($samplecounts = $this->kclass->DB->fetchArray($samplecount)){
				$rows[]=$samplecounts;
				if($samplecounts['regionid']){
					$regionid[]=$samplecounts['regionid'];
				}
				if($samplecounts['created']<=1390545984){
					$lampCount=$this->kclass->DB->query("select quantity from `item` where module='sample' and mid=".$samplecounts['sampleid']." and productitemid!=0");
				}else{
					$lampCount=$this->kclass->DB->query("select quantity from `item` where module='sample' and mid=".$samplecounts['sampleid']." and materialid=0");
				}
				if(!$samplecounts['customername']){
					$samplecounts['customername']=$samplecounts['toMessrs'];
				}
				$lampNum=0;
				$sampleTotals=0;
				while($lampCounts=$this->kclass->DB->fetchArray($lampCount)){
					$lampNum+=$lampCounts['quantity'];
				}
				$sampleTotal=$this->kclass->DB->query("select amount from item where module='sample' and mid=".$samplecounts['sampleid']."");
				while($sampleTotalRow=$this->kclass->DB->fetchArray($sampleTotal)){
					$sampleTotals+=$sampleTotalRow['amount'];
					$sampleTotalSum+=$sampleTotalRow['amount'];
				}
				$productItem=$this->kclass->relatedProducts(array('module'=>'sample','moduleid'=>$samplecounts['sampleid']));
				$sampleTotals+=$samplecounts['deliveryFee'];
				$lampTotal+=$productItem['quantity'];
				$total+=$sampleTotals;
				$sampleDeliveryFee+=$samplecounts['deliveryFee'];
				$samplecounts['deliveryFee']=number_format($samplecounts['deliveryFee'],2,'.',',');
				$sampleTotals=number_format($sampleTotals,2,'.',',');
				$items=$this->kclass->relatedSampleProductItem(array('sampleid'=>$samplecounts['sampleid']));
				$this->kclass->page['main'].="
				<tr class='".$this->kclass->rotateLine()."'>
				<td>{$i}</td>
				<td>".$samplecounts['sampleno']."</td>
				<td><table>".$productItem['panel']."</table></td>
				<td>".$productItem['quantity']."</td>
				<td>".$samplecounts['username']."</td>
				<td>".$samplecounts['customername']."</td>
				<td>".$samplecounts['country']."</td>
				<td>".$samplecounts['deliveryFee']."</td>
				<td>".$sampleTotals."</td>
				</tr>";
				$i++;	
			}
			$sampleTotalSum=number_format($sampleTotalSum,2,'.',',');
			$total=number_format($total,2,'.',',');
			$sampleDeliveryFee=number_format($sampleDeliveryFee,2,'.',',');

			//按国家
			//$itemstr='<tr><td align="center" class="red" width="550px"><b>ID</b></td><td align="center" class="red"  width="150px"><b>国家名称</b></td><td align="center" class="red" width="80px"><b>订单数量（国家）</b></td><td align="center" class="red" width="80px"><b>产品数量（国家）</b></td><td align="center" class="red" width="80px"><b>订单金额（国家）</b></td><td align="center" class="red" width="60px"><b>已到账（国家）</b></td><td align="center" class="red" width="60px"><b>未到账（国家）</b></td>';
			//$itemstr.='<td align="center" class="red" width="100px"><b>客户名称</b></td><td align="center" class="red" width="80px"><b>订单数量（客户）</b></td><td align="center" class="red" width="80px"><b>产品数量（客户）</b></td><td align="center" class="red" width="80px"><b>订单金额（客户）</b></td><td align="center" class="red" width="60px"><b>已到账(客户)</b></td><td align="center" class="red" width="60px"><b>未到账（客户）</b></td></tr>';
			$itemstr='<table class="hundred tablesorter mytable"><thead><tr><th width="60">序列号</th><th width="80">国家名称</th><th width="100">样品单数量</th><th width="100">产品数量</th><th width="100">样品单总金额</th><th width="200">客户名称</th><th width="100">产品数量</th><th width="100">额外费用总金额</th><th width="100">总价小计</th></tr></thead><tbody>';
			$brr='';
			foreach ($rows as $key => $value) {
				if($value['country']){
					if(empty($brr)){
						$brr[$value['country']]=$value['country'];
					}elseif(!in_array($value['country'],$brr)){
						$brr[$value['country']]=$value['country'];
					}
				}
			}
			foreach ($brr as $key => &$value) {
				$value='';
				foreach ($rows as $k=>$v) {
					if($v['country']==$key){
						$value[]=$v;
					}
				}
			}
			//组装数组
			// foreach ($brr as $assk => &$assv) {
			// 	$cusName='';
			// 	foreach ($assv as $sonk => &$sonv) {
			// 		if(!in_array($sonv['customername'], $cusName)){
			// 			$cusName[]=$sonv['customername'];
			// 		}else{
						
			// 		}
			// 		$accsonProductItem=$this->kclass->relatedProducts(array('module'=>'sample','moduleid'=>$sonv['sampleid']));
			// 		$sonv['reProNum']=$accsonProductItem['quantity'];
			// 		$sonv['reProTotal']=$accsonProductItem['total'];	
			// 	}
			// }	
			// $cusBrr=$brr;
			// foreach ($cusBrr as $cuskey => $cusval) {
			// 	foreach ($cusval as $cusk => $cusv) {
			// 		$cusName[]=$cusv['customername'];
			// 	}
			// }
			// $cusName=array_unique($cusName);
			// echo "<pre>";
			// var_dump($brr);
			// echo "</pre>";
			$i=1;
			foreach ($brr as $keys => $values) {
				$accProNum=0;
				$accTotal=0;
				foreach($values as $k=>$v){
					$accProductItem=$this->kclass->relatedProducts(array('module'=>'sample','moduleid'=>$v['sampleid']));	
					if(!$accProductItem['quantity']){
						$accProductItem['quantity']=0;
					}
					$accProNum+=$accProductItem['quantity'];
					$accTotal+=$v['deliveryFee'];
					$accTotal+=$accProductItem['total'];
				}
				$accTotal=number_format($accTotal,2,'.',',');
				$mergeRows=count($values);
				$itemstr.="<tr><td align='center' rowspan=".$mergeRows.">".$i."</td><td align='center' rowspan=".$mergeRows.">".$keys."</td><td align='center' rowspan=".$mergeRows.">".$mergeRows."</td><td align='center' rowspan=".$mergeRows.">".$accProNum."</td><td align='center' rowspan=".$mergeRows.">".$accTotal."</td>";
				$j=1;

				foreach ($values as $k => $v) {
					$accProductItem=$this->kclass->relatedProducts(array('module'=>'sample','moduleid'=>$v['sampleid']));
					$accSampleTotal=$accProductItem['total']+$v['deliveryFee'];
					$accSampleTotal=number_format($accSampleTotal,2,'.',',');
					// echo "<pre>";
					// var_dump($v);
					// echo "</pre>";
					if(!$accProductItem['quantity']){
						$accProductItem['quantity']=0;
					}
					if($j==1){
						$itemstr.="<td>".$v['customername']."</td><td>".$accProductItem['quantity']."</td><td>".$v['deliveryFee']."</td><td>".$accSampleTotal."</td></tr>";
						$j++;
					}else{
						$itemstr.="<tr><td>".$v['customername']."</td><td>".$accProductItem['quantity']."</td><td>".$v['deliveryFee']."</td><td>".$accSampleTotal."</td></tr>";
						$j++;
					}

				}
				$i++;
			}
			$itemstr.="</table>";
			$this->kclass->page['main'].=$itemstr;

			//按业务员
			$arr='';
			foreach ($rows as $key => $val) {
				if($val['director']){
					if(empty($brr)){
						$arr[$val['director']]=$val['director'];
					}elseif(!in_array($value['director'],$arr)){
						$arr[$val['director']]=$val['director'];
					}
				}
			}
			foreach ($arr as $key => &$val) {
				$val='';
				foreach ($rows as $k=>$v) {
					if($v['director']==$key){
						$val[]=$v;
					}
				}
			}
			// echo "<pre>";
			// var_dump($arr);
			// echo "</pre>";
			$itemstrs.='<table class="hundred tablesorter mytable"><tr><td align="center" class="red">ID</td><td align="center" class="red"><b>业务员名称</b></td><td align="center" class="red"><b>样品单数量</b></td><td align="center" class="red"><b>产品数量</b></td><td align="center" class="red"><b>样品单金额</b></td></tr>';
			$i=1;
			foreach($arr as $dirkey=>$dirval){
				$dirSampleNum=count($dirval);
				$dirProNum=0;
				$dirTotal=0;
				foreach ($dirval as $dirk => $dirv) {
					$dirProductItem=$this->kclass->relatedProducts(array('module'=>'sample','moduleid'=>$dirv['sampleid']));
					$dirProNum+=$dirProductItem['quantity'];
					$dirTotal+=$dirProductItem['total'];
					$dirTotal+=$dirv['deliveryFee'];
				}
				$dirTotal=number_format($dirTotal,2,'.',',');
				$dirName=$this->kclass->DB->queryFirst("select username from user where userid=".$dirkey."");
				$itemstrs.="<tr><td align='center'>{$i}</td><td align='center'>{$dirName['username']}</td><td align='center'>{$dirSampleNum}</td><td align='center'>{$dirProNum}</td><td align='center'>{$dirTotal}</td></tr>";
				$i++;
			}
			$itemstrs.="</table>";
			$this->kclass->page['main'].=$itemstrs;
			$this->kclass->page['main'].='</tbody></table>';
			$this->kclass->page['main'].="<table class='hundred'><thead><tr><td  colspan='9' style='font-size:20px'>样品单总计：共有样品单数：<span style='color:red'>{$rowsNum}</span>,　 产品总数量：<span style='color:red'>{$lampTotal}</span>,　样品单总金额：<span style='color:red'>{$sampleTotalSum}</span>,　额外费用总金额：<span style='color:red'>{$sampleDeliveryFee}</span>,　总金额：<span style='color:red'>{$total}</span></td></tr></thead></table>";
		}else{
			$this->kclass->page['main'] = '暂无相关记录。';
		}
		$this->kclass->page['onload'] .= "dc.tabhover();dc.tips();$('.mytable').fixedtableheader();$('.tablesorter').tablesorter( { headers: { 2: { sorter: false}, 7: {sorter: false} } } );";
		$t = date('F j, Y, H:i:s', TIMENOW);
		$footer=<<<EOD
			<div class="right">Page Created On: {$t}</div>
		New Wisdom Investment Limited
EOD;
		$this->kclass->page['pageTitle']='ORDER - '.$order['orderno'];
		$this->kclass->page['title'] = 'Sample according';
		$this->kclass->page['contact']='Address: 4-306, Xianglixincuen, Hongli West Road, Shenzhen, China<br>Tel: 86-755-83911841  Fax: 86-755-83906115  E-Mail: info@wisdom.hk';
		$this->kclass->page['caption'] = '';
		$this->kclass->page['footer'] = $footer;
	}

	function customer(){
		if(!$this->kclass->input['customerid'] > 0)$this->kclass->boinkIt('/s.php?module=customer');
		$customer = $this->kclass->DB->queryFirst("
			SELECT `customer`.*, 
				region.encountry AS regionEncountry, region.country AS regionCountry, region.enstate AS regionEnstate, region.state AS regionState, region.encity AS regionEncity, region.city AS regionCity, 
				b.title AS business, 
				m.username AS mname, c.username AS cname
			FROM `customer` 
			LEFT JOIN `businesstype` AS b ON (b.businesstypeid=customer.businesstypeid) 
			LEFT JOIN region ON (region.regionid=customer.regionid) 
			LEFT JOIN `user` AS m ON (m.userid=customer.modifier) 
			LEFT JOIN `user` AS c ON (c.userid=customer.creator) 
			WHERE customerid='".$this->kclass->input['customerid']."'
		");
		if(!$customer)	$this->kclass->boinkIt('/s.php?module=customer');
		if($customer['regionid']>0){
			$region = $customer['regionCountry'].' '.$customer['regionState'].' '.$customer['regionCity'];
		}else{
			$region='';
		}
		if($customer['modifier']>0){
			$modified = '，'.$customer['mname'].'于'.date('y-m-d H:i:s', $customer['modified']).'最后修改';
		}
		$created = date('y-m-d H:i:s', $customer['created']);
		if($customer['postalcode']!='')$postalcode='<span class="normal">(邮编：'.$customer['postalcode'].')</span>';
		if($customer['position']!='')$position='<span class="normal">(称谓：'.$customer['position'].')</span>';
		if($customer['position2']!='')$position2='<span class="normal">(称谓：'.$customer['position2'].')</span>';
		$attach = $this->kclass->getAttachs(array('module'=>'customer', 'mid'=>$customer['customerid']));
		if($attach!= false){
			$attachs = '<div class="clear">'.$attach.'</div>';
		}
		$sample=$this->kclass->relatedSample(array('customerid'=>$customer['customerid']));
		$inquiry=$this->kclass->relatedInquiry(array('customerid'=>$customer['customerid']));
		$quoting=$this->kclass->relatedQuoting(array('customerid'=>$customer['customerid']));
		$visit=$this->kclass->relatedVisit(array('customerid'=>$customer['customerid']));
		$order=$this->kclass->relatedOrder(array('customerid'=>$customer['customerid'], 'type'=>'PI'));
		$commission=$this->kclass->relatedCommission(array('customerid'=>$customer['customerid']));
		$deposit=$this->kclass->relatedDeposit(array('customerid'=>$customer['customerid']));
		$guarantee=$this->kclass->relatedGuarantee(array('customerid'=>$customer['customerid']));
		$creditnote=$this->kclass->relatedCreditnote(array('customerid'=>$customer['customerid']));
		$recipt=$this->kclass->relatedRecipt(array('customerid'=>$customer['customerid']));
		$debitnote=$this->kclass->relatedDebitnote(array('module'=>'order','customerid'=>$customer['customerid']));
		$payment=$this->kclass->relatedPayment(array('module'=>'order','customerid'=>$customer['customerid']));
		$claim=$this->kclass->relatedClaim(array('module'=>'order','customerid'=>$customer['customerid']));
		$return=$this->kclass->relatedReturn(array('module'=>'order','customerid'=>$customer['customerid']));
		$exchange=$this->kclass->relatedExchange(array('customerid'=>$customer['customerid']));
		$website = $this->kclass->relatedWebsite(array('customerid'=>$customer['customerid']));
		$service = $this->kclass->relatedService(array('customerid'=>$customer['customerid']));		
$this->kclass->page['main'] = <<<EOF
<table class="hundred">
<tr class="odd">
	<td width="80">英文名称：</td>
	<td class="middle bold">{$customer['title']}</td>
	<td width="80">简　　称：</td>
	<td class="middle">{$customer['abbr']}</td>
</tr>
<tr class="even">
	<td>中文名称：</td>
	<td class="middle">{$customer['cntitle']}</td>
	<td>商业类型：</td>
	<td class="middle">{$customer['business']}</td>
</tr>
<tr class="odd">
	<td>英文地址：</td>
	<td class="middle">{$customer['address']}</td>
	<td>国　　家：</td>
	<td class="middle">{$region}</td>
</tr>
<tr class="even">
	<td>中文地址：</td>
	<td class="middle">{$customer['cnaddress']} {$postalcode}</td>
	<td>网　　站：</td>
	<td class="middle">{$customer['website']}</td>
</tr>
<tr class="odd">
	<td>主联系人：</td>
	<td class="middle">{$customer['linkman']} {$position}</td>
	<td>邮　　箱：</td>
	<td class="middle">{$customer['email']}</td>
</tr>
<tr class="even">
	<td>次联系人：</td>
	<td class="middle">{$customer['linkman2']} {$position2}</td>
	<td>传　　真：</td>
	<td class="middle">{$customer['fax']}</td>
</tr>
<tr class="odd">
	<td>电　　话：</td>
	<td class="middle">{$customer['telephone']}</td>
	<td>手　　机：</td>
	<td class="middle">{$customer['mobile']}</td>
</tr>
<tr class="even">
	<td valign="top">银行资料：</td>
	<td colspan="3">{$customer['bankdetail']}</td>
</tr>
<tr class="odd">
	<td valign="top">备　　注：</td>
	<td colspan="3">{$customer['remark']}</td>
</tr>
</table>
EOF;
		$this->kclass->page['pageTitle']='CUSTOMER - '.$customer['title'];
		$this->kclass->page['title'] = 'CUSTOMER';
		$this->kclass->page['caption'] = 'Customer No.: '.$customer['customerno'].'<br>Created: '.date('Y-m-d', $customer['created']).'. Modified: '.date('Y-m-d', $customer['modified']);
	}
	// 
	function product(){
		if(!$this->kclass->input['productid'] > 0)$this->kclass->boinkIt('/s.php?module=product');
		$product = $this->kclass->DB->queryFirst("
			SELECT `product`.*, 
				category.title AS category, 
				attr.title AS attr, 
				m.username AS mname, c.username AS cname
			FROM `product` 
			LEFT JOIN `category` ON (category.categoryid=product.categoryid) 
			LEFT JOIN `attr` ON (attr.attrid=product.attrid) 
			LEFT JOIN `user` AS m ON (m.userid=product.modifier) 
			LEFT JOIN `user` AS c ON (c.userid=product.creator) 
			WHERE productid='".$this->kclass->input['productid']."'
		");
		if(!$product)	$this->kclass->boinkIt('/s.php?module=product');
		if($product['modifier']>0){
			$modified = '，'.$product['mname'].' 于 '.date('y-m-d H:i:s', $product['modified']).' 最后修改';
		}
		$created = date('y-m-d H:i:s', $product['created']);
		$attach = $this->kclass->getAttachs(array('module'=>'product', 'mid'=>$product['productid']));
$this->kclass->page['main']=<<<EOF
<table class="hundred">
<tr class="odd">
	<td width="80">英文名称：</td>
	<td class="middle">{$product['title']}</td>
	<td width="80">简　　称：</td>
	<td class="middle">{$product['abbr']}　<span class="normal">编号：</span>{$product['productno']}</td>
</tr>
<tr class="even">
	<td>产品类别：</td>
	<td class="middle">{$product['category']}</td>
	<td>产品属性：</td>
	<td class="middle">{$product['attr']}</td>
</tr>
<tr class="odd">
	<td valign="top">产品特色：</td>
	<td colspan="3" class="middle">{$product['features']}</td>
</tr>
<tr class="even">
	<td valign="top">技术参数：</td>
	<td colspan="3" class="middle">{$product['technical']}</td>
</tr>
<tr class="odd">
	<td valign="top">详细规格：</td>
	<td colspan="3" class="middle">{$product['specifications']}</td>
</tr>
<tr class="even">
	<td valign="top">产品说明：</td>
	<td colspan="3" class="middle">{$product['details']}</td>
</tr>
<tr class="odd">
	<td valign="top">产品描述：</td>
	<td colspan="3" class="middle">{$product['description']}</td>
</tr>
<tr class="even">
	<td valign="top">备　　注：</td>
	<td colspan="3">{$product['remark']}</td>
</tr>
</table>
EOF;
		$this->kclass->page['pageTitle']='PRODUCT - '.$product['entitle'];
		$this->kclass->page['title'] = 'PRODUCT';
		$this->kclass->page['caption'] = 'Product No.: '.$product['productno'].'<br>Created: '.date('Y-m-d', $product['created']).'. Modified: '.date('Y-m-d', $product['modified']);
	}
	// 
	// 
	function orderPI(){
		$orderid = $this->kclass->input['orderid'];
		$language = $this->kclass->input['chooselanguage'];
		if(!$orderid > 0)$this->kclass->boinkIt('/s.php?module=order');


		//中文信息查询
		$order = $this->kclass->DB->queryFirst("
			SELECT `order`.*, 
				route.title AS routeTitle, route.caption AS routeCaption, 
				customer.cntitle AS customerTitle,customer.title AS customerenTitle, customer.address AS customerAddress, customer.linkman AS customerLinkman, customer.position AS customerPosition, customer.telephone AS customerTelephone, customer.fax AS customerFax, 
				supplier.title AS supplierTitle, supplier.entitle AS supplierenTitle, supplier.address AS supplierAddress, supplier.enaddress AS supplierenAddress,supplier.linkman AS supplierLinkman, supplier.position AS supplierPosition, supplier.telephone AS supplierTelephone, supplier.fax AS supplierFax, 
				shipmethod.title AS shipmethodTitle,shipmethod.entitle AS shipmethodEntitle, 
				loading.title AS loadingTitle, loading.entitle AS loadingEntitle,
				loadingCountry.country AS loadingCountryTitle,loadingCountry.encountry AS loadingCountryEntitle, 
				discharge.title AS dischargeTitle, discharge.entitle AS dischargeEntitle, 
				dischargeCountry.country AS dischargeCountryTitle,dischargeCountry.encountry AS dischargeCountryEntitle, 
				paymentterm.title AS paymenttermTitle,paymentterm.entitle AS paymenttermEntitle, 
				deliveryterm.title AS deliverytermTitle,deliveryterm.entitle AS deliverytermEntitle, 
				currency.title AS currencyTitle, currency.symbol AS currencySymbol, currency.remark AS currencyRemark, 
				m.username AS mname, c.username AS cname
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
			WHERE `order`.killed=0 AND orderid='".$this->kclass->input['orderid']."'
		");		
		if($language=='c1'){		
		//项目名称
			$ptitle = "形式发票";
			$thbuyer = "买方：";
			$thseller = "卖方：";
			$thoderNo = "订单编号.：";
			$thName = "公司名称：";
			$thAddress = "地址：";
			$thLinkman = "联系人：";
			$thTel = "电话：";
			$thFax = "传真：";
			$thExpress = "运送方式：";
			$thLPlace = "装货地点：";
			$thPayment = "支付条款：";
			$thunLPlace = "卸货地点：";
			$thDetime = "交货时间：";
			$thDeterms = "交货条款：";
			$thLNote = "说明：";
			$thPNote = "包装说明：";
			$thSNote = "特别说明：";
			$thRemark = "备注：";
			$thCurrency = "币种：";
			$thIteminfo = "项目详细信息";
			$Pcontact = "公司地址：深圳市龙岗区宝荷路嶂背大道9号1楼<br>Tel: 86-755-83911841  Fax: 86-755-83906115  E-Mail: info@wisdom.hk";


			if($order['customerTitle']!=""){
				$Customer = $order['customerTitle'];
			}else{
				$Customer = $order['customerenTitle'];			
			}
			
			if($order['supplierTitle']!=""){
				$Supplier = $order['supplierTitle'];
			}else{
				$Supplier = $order['supplierenTitle'];
			}
			
			if($order['supplierAddress']!=""){
				$Saddress = $order['supplierAddress'];
			}else{
				$Saddress = $order['supplierenAddress'];
			}
		
		}

		if($language=='c2'){		
			//item title
			$ptitle = "Proforma Invoice";
			$thbuyer = "Buyer：";
			$thseller = "Seller：";
			$thoderNo = "Order No.：";
			$thName = "Company：";
			$thAddress = "Address：";
			$thLinkman = "Linkman：";
			$thTel = "Tel：";
			$thFax = "Fax：";
			$thExpress = "Express Method";
			$thLPlace = "Loading Place";
			$thPayment = "Payment Terms";
			$thunLPlace = "Unloading Place";
			$thDetime = "Delivery Time";
			$thDeterms = "Delivery Terms";
			$thLNote = "Label Note";
			$thPNote = "Packing Note";
			$thSNote = "Special Note";
			$thRemark = "Remark";
			$thCurrency = "Currency：";
			$thIteminfo = "Item Information";
			$Pcontact = "Address: 4-306, Xianglixincuen, Hongli West Road, Shenzhen, China<br>Tel: 86-755-83911841  Fax: 86-755-83906115  E-Mail: info@wisdom.hk";
			
			if($order['customerenTitle']!=""){
				$Customer = $order['customerenTitle'];
			}else{
				$Customer = $order['customerTitle'];			
			}		

			if($order['supplierenTitle']!=""){
				$Supplier = $order['supplierenTitle'];
			}else{
				$Supplier = $order['supplierTitle'];
			}
			
			if($order['supplierenAddress']!=""){
				$Saddress = $order['supplierenAddress'];
			}else{
				$Saddress = $order['supplierAddress'];
			}
		}
		if(!$order)	$this->kclass->boinkIt('/s.php?module=order&action=find');
		if($order['modifier']>0){
			$modified = '，'.$order['mname'].' 于 '.date('y-m-d H:i:s', $order['modified']).' 最后修改';
		}
		$created = date('y-m-d H:i:s', $order['created']);
		$attach = $this->kclass->getAttachs(array('module'=>'order', 'mid'=>$order['orderid']));
		if($attach!= false){
			$attachs = '<div class="clear">'.$attach.'</div>';
		}
		$shipmentDate=date('Y-m-d', $order['shipmentDate']);
		$item = $this->kclass->relatedPrintPIOrderItem(array('orderid'=>$order['orderid'], 'title'=>'客户','language'=>$language));
		//$fee = $this->kclass->relatedPrintFee(array('orderid'=>$order['orderid']));
		
		$nowTime = date('F j, Y, H:i:s', TIMENOW);
$this->kclass->page['main']=<<<EOF
<table class="hundred">
<tbody>
<tr class="even">
	<td colspan="3" width="50%" align="center" class="bold">{$thbuyer}</td>
	<td colspan="3" width="50%" align="center" class="bold">{$thseller}</td>
</tr>
<tr class="odd">
	<td colspan="3" width="50%">
		{$thoderNo}{$order['customerNo']}<br/>
		{$thName}{$Customer}<br/>
		{$thAddress}{$order['customerAddress']}<br/>
		{$thLinkman}{$order['customerLinkman']}<br/>
		{$thTel}{$order['customerTelephone']}<br/>
		{$thFax}{$order['customerFax']}
	</td>
	<td colspan="3" width="50%">
		{$thoderNo}{$order['supplierNo']}<br/>
		{$thName}{$Supplier}<br/>
		{$thAddress}{$Saddress}<br/>
		{$thLinkman}{$order['supplierLinkman']}<br/>
		{$thTel}{$order['supplierTelephone']}<br/>
		{$thFax}{$order['supplierFax']}
	</td>
</tr>
<tr class="even">
	<td width="16%" align="center">{$thExpress}</td>
	<td width="16%" align="center">{$thLPlace}</td>
	<td width="18%" align="center">{$thPayment}</td>
	<td width="16%" align="center">{$thunLPlace}</td>
	<td width="16%" align="center">{$thDetime}</td>
	<td width="18%" align="center">{$thDeterms}</td>
</tr>
<tr class="odd">
	<td align="center">{$order['shipmethodEntitle']}</td>
	<td align="center">{$order['loadingCountryEntitle']} {$order['loadingEntitle']}</td>
	<td align="center">{$order['paymenttermEntitle']}</td>
	<td align="center">{$order['dischargeCountryEntitle']} {$order['dischargeEntitle']}</td>
	<td align="center">{$shipmentDate}</td>
	<td align="center">{$order['deliverytermEntitle']}</td>
</tr>
<tr class="odd">
	<td align="center" style="background:#F8F8F8">{$thPNote}</td>
	<td colspan="2" >{$order['packing']}</td>
	<td align="center" style="background:#F8F8F8">{$thRemark}</td>
	<td colspan="2">{$order['remark']}</td>
</tr>
<table class="hundred">
<tbody>
<tr class="even">
	<td colspan="11" class="bold"><span class="right">{$thCurrency}{$order['currencyTitle']}</span>{$thIteminfo}</td>
</tr>
{$item}
</tbody>
</table>
<!--
{$fee['tr']}


<table class="hundred">
<tbody>
<tr><td align="right" class="middle bold">Total Amount：<span>{$order['currencyTitle']} {$order['currencySymbol']}{$total}</span></td></tr>
</tbody>
</table>
-->
EOF;

		$t = date('F j, Y, H:i:s', TIMENOW);
		$footer=<<<EOD
			<div class="right">Page Created On: {$t}</div>
		New Wisdom Investment Limited
EOD;
		$this->kclass->page['pageTitle']='ORDER - '.$order['orderno'];
		$this->kclass->page['title'] = $ptitle;
		$this->kclass->page['contact']= $Pcontact;
		$this->kclass->page['caption'] = '<span class="middle bold">Order No.: '.$order['orderno'].'</span>'.$this->kclass->iif($order['customerNo']!='', ' (Cust. Order No.: '.$order['customerNo'].')', '').'<br>Created: '.date('Y-m-d', $order['created']).'. Modified: '.date('Y-m-d', $order['modified']);
		$this->kclass->page['footer'] = $footer;
	}
	
	//
	function orderPO(){
		$orderid = $this->kclass->input['orderid'];
		$language = $this->kclass->input['chooselanguage'];
		if(!$orderid > 0)$this->kclass->boinkIt('/s.php?module=order');
		//中文信息查询
		if($language=="c1"){
		$order = $this->kclass->DB->queryFirst("
				SELECT `order`.*,
					route.title AS routeTitle, route.caption AS routeCaption,
					customer.cntitle AS customerTitle, customer.address AS customerAddress, customer.linkman AS customerLinkman, customer.position AS customerPosition, customer.telephone AS customerTelephone, customer.fax AS customerFax,
					supplier.title AS supplierTitle, supplier.address AS supplierAddress, supplier.linkman AS supplierLinkman, supplier.position AS supplierPosition, supplier.telephone AS supplierTelephone, supplier.fax AS supplierFax,
					shipmethod.title AS shipmethodTitle,
					loading.title AS loadingTitle, 
					loadingCountry.country AS loadingCountryTitle,
					discharge.title AS dischargeTitle,
					dischargeCountry.country AS dischargeCountryTitle,
					paymentterm.title AS paymenttermTitle,
					deliveryterm.title AS deliverytermTitle,
					currency.title AS currencyTitle, currency.symbol AS currencySymbol, currency.remark AS currencyRemark,
					m.username AS mname, c.username AS cname, d.username AS director, t.username AS tracker
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
			
			//表格项目名称
			$thPONumber = "客 户 订 单 号：";
			$thCreater = "制&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;单&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;人：";
			$thCustomer = "客&nbsp;&nbsp;户&nbsp;&nbsp;&nbsp;名&nbsp;&nbsp;称：";
			$thSalesman = "业&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;务&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;员：";
			$thDelimethod = "发&nbsp;&nbsp;货&nbsp;&nbsp;&nbsp;方&nbsp;&nbsp;式：";
			$thCreated = "制&nbsp;&nbsp;单&nbsp;&nbsp;&nbsp;日&nbsp;&nbsp;期：";
			$thExpectcDate = "计划完成日期：";
			$thExpectdDate = "计划发货日期：";
			$thActualcDate = "实际完成日期：";
			$thActualdDate = "实际发货日期：";
			$thShippingNo = "货&nbsp;&nbsp;运&nbsp;&nbsp;单&nbsp;&nbsp;号：";
			$thCarton = "箱&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;数：";
			$thWeight = "重&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;量：";
			$thQontrol = "质&nbsp;&nbsp;&nbsp;检&nbsp;&nbsp;OQC：";
			$thShipconfirm = "发&nbsp;&nbsp;货&nbsp;&nbsp;确&nbsp;&nbsp;认：";
			$thDelivername = "提货司机姓名：";
			$thDeliverphone = "司&nbsp;&nbsp;机&nbsp;&nbsp;电&nbsp;&nbsp;话：";
			$thPlatenumber = "车&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;牌&nbsp;&nbsp;&nbsp;&nbsp;号：";
			$thDeliverID = "司机&nbsp;身&nbsp;份&nbsp;号：";
			$thShippingcomp = "货&nbsp;&nbsp;运&nbsp;&nbsp;公&nbsp;&nbsp;司：";
			$thDeliverconfirm = "司&nbsp;&nbsp;机&nbsp;&nbsp;确&nbsp;&nbsp;认：";
			$thPtitle = "采 购 单";
			$thAddress = "公司地址：深圳市龙岗区宝荷路嶂背大道9号1楼";
			$thPackrequire = "	包装要求：<br>1._____ 常规包装（具体包装方法参见《包装作业要求》指导书）；<br>
	 		 2._____ 特殊包装（具体注明包装方式）。";
			$thPackdetails = "(特殊包装详细):";
			$thRemark = "备注：";
			$Pcontact = "公司地址：深圳市龙岗区宝荷路嶂背大道9号1楼<br>Tel: 86-755-83911841  Fax: 86-755-83906115  E-Mail: info@wisdom.hk";
		}
		//英文信息查询
		if($language=="c2"){
		$order = $this->kclass->DB->queryFirst("
				SELECT `order`.*,
					route.title AS routeTitle, route.encaption AS routeCaption,
					customer.title AS customerTitle, customer.address AS customerAddress, customer.linkman AS customerLinkman, customer.position AS customerPosition, customer.telephone AS customerTelephone, customer.fax AS customerFax,
					supplier.title AS supplierTitle, supplier.address AS supplierAddress, supplier.linkman AS supplierLinkman, supplier.position AS supplierPosition, supplier.telephone AS supplierTelephone, supplier.fax AS supplierFax,
					shipmethod.entitle AS shipmethodTitle,
					loading.entitle AS loadingEntitle,
					loadingCountry.encountry AS loadingCountryEntitle,
					discharge.entitle AS dischargeEntitle,
					dischargeCountry.encountry AS dischargeCountryEntitle,
					paymentterm.entitle AS paymenttermEntitle,
					deliveryterm.entitle AS deliverytermEntitle,
					currency.title AS currencyTitle, currency.symbol AS currencySymbol, currency.remark AS currencyRemark,
					m.username AS mname, c.username AS cname, d.username AS director, t.username AS tracker
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
			
			//Table th Name项目名称
			$thPONumber = "PO No.:";
			$thCreater = "Creater:";
			$thCustomer = "Attn:";
			$thSalesman = "Sales Person:";
			$thDelimethod = "Delivery Mothhod:";
			$thCreated = "Creating Date:";
			$thExpectcDate = "Expected Completion Date:";
			$thExpectdDate = "Expected delivery date:";
			$thActualcDate = "Actual Completion Date:";
			$thActualdDate = "Actual delivery date:";
			$thShippingNo = "Shipping No.:";
			$thCarton = "Carton:";
			$thWeight = "Weight:";
			$thQontrol = "Quality Control:";
			$thShipconfirm = "Shipping Confirmation:";
			$thDelivername = "Delivers' name:";
			$thDeliverphone = "Deliver's phone:";
			$thPlatenumber = "Plate number:";
			$thDeliverID = "ID NO. of deliver:";
			$thShippingcomp = "Shipping company:";
			$thDeliverconfirm = "Deliver’s comfirmation:";
			$thPtitle = "Purchase Order";
			$thAddress = "Address: No.9 Zhangbei Road, Longgang, Shenzhen, China";
			$thPackrequire = "Packing Requirements:<br> 1._____Conventional Packing(refer to the packaging instruction for the specific package method);<br>
			2._____Special Packing(Specify the mode of packing);";
			$thPackdetails = "(Special Packing Details):";
			$thRemark = "Remarks:";
			$Pcontact = "Address: 4-306, Xianglixincuen, Hongli West Road, Shenzhen, China<br>Tel: 86-755-83911841  Fax: 86-755-83906115  E-Mail: info@wisdom.hk";
		}
		if(!$order)	$this->kclass->boinkIt('/s.php?module=order&action=find');
		if($order['modifier']>0){
			$modified = '，'.$order['mname'].' 于 '.date('y-m-d H:i:s', $order['modified']).' 最后修改';
		}
		$created = date('y-m-d H:i:s', $order['created']);
		$shipmentDate=date('Y-m-d', $order['shipmentDate']);
		$item = $this->kclass->relatedPrintPOOrderItem(array('orderid'=>$order['orderid'], 'title'=>'客户','language'=>$language));
		//$fee = $this->kclass->relatedFee(array('orderid'=>$order['orderid']));
		$nowTime = date('F j, Y, H:i:s', TIMENOW);
		$nowDate = date('Y-m-d', TIMENOW);
		$freight = $this->kclass->DB->queryFirst("SELECT * FROM `freight` WHERE module='order' AND mid='{$orderid}'");
		
		/*if($freight==''){
			echo "查询结果为空！"; //测试查询结果（非工作程序）
		}*/
		
		$ExpectdDate = date("Y-m-d",$order['shipmentDate']);
		$ActualdDate = date("Y-m-d",$order['actualshipmentDate']);
		$ExpectcDate = date("Y-m-d",$order['completeDate']);
		$AtualcDate = date("Y-m-d",$order['actualcompleteDate']);
		
		$this->kclass->page['main']=<<<EOF
<table class="printNoborderTab">
<tbody>
<tr class="odd" align="right">
	<td width="250">{$thPONumber}</td>
	<td width="300"><input type="text" class="underlineInput" value="{$order['customerNo']}"></td>
	<td width="250">{$thCreater}</td>
	<td width="300"><input type="text" class="underlineInput" value="{$order['tracker']}"></td>
</tr>
<tr class="odd" align="right">
	<td>{$thCustomer}</td>
	<td><input type="text" class="underlineInput" value="{$order['customerTitle']}"></td>
	<td>{$thSalesman}</td>
	<td><input type="text" class="underlineInput" value="{$order['director']}"></td>
</tr>
<tr class="odd" align="right">
	<td>{$thDelimethod}</td>
	<td><input type="text" class="underlineInput" value="{$order['shipmethodTitle']}"></td>
	<td>{$thCreated}</td>
	<td><input type="text" class="underlineInput" value="{$nowDate}"></span></td>
</tr>
<tr class="odd" align="right">
	<td width="70">{$thExpectcDate}</td>
	<td><input type="text" class="underlineInput" value="{$ExpectcDate}"></td>
	<td>{$thActualcDate}</td>
	<td><input type="text" class="underlineInput" value="{$AtualcDate}"></td>
</tr>
<tr class="odd" align="right">
	<td width="70">{$thExpectdDate}</td>
	<td><input type="text" class="underlineInput" value="{$ExpectdDate}"></td>
	<td>{$thActualdDate}</td>
	<td><input type="text" class="underlineInput" value="{$ActualdDate}"></td>
</tr>
	
<table class="printTab">
<tbody>

{$item}
<tr>
	<td colspan="12" height="60" valign="top">
	{$thPackrequire}
	</td>
</tr>
<tr>
	<td colspan="12" height="90" valign="top">
	{$thPackdetails}<br/>&nbsp;&nbsp;&nbsp;&nbsp;{$order['special']}
	</td>
</tr>
<tr>
	<td colspan="12" height="90" valign="top">
	{$thRemark}<br/>&nbsp;&nbsp;&nbsp;&nbsp;{$order['remark']}
	</td>
</tr>
</tbody>
</table>

<table class="printNoborderTab">
<tbody>
<tr class="odd" align="right">
	<td width="250">{$thShippingNo}</td>
	<td width="300"><input type="text" class="underlineInput" value="{$freight['expressNo']}"></td>
	<td width="250"></td>
	<td width="300"></td>
</tr>
<tr class="odd" align="right">
	<td width="250">{$thCarton}</td>
	<td width="300"><input type="text" class="underlineInput" value="{$freight['carton']}"></td>
	<td width="250">{$thWeight}</td>
	<td width="300"><input type="text" class="underlineInput" value="{$freight['weight']}"></span></td>
</tr>
<tr class="odd" align="right">
	<td width="250">{$thQontrol}</td>
	<td width="250"><input type="text" class="underlineInput" value="{$freight['oqc']}"></td>
	<td width="250">{$thShipconfirm}</td>
	<td width=""><input type="text" class="underlineInput" value="{$freight['shippingVerify']}"></span></td>
</tr>
<tr class="odd" align="right">
	<td width="250">{$thDelivername}</td>
	<td width="250"><input type="text" class="underlineInput" value="{$freight['driverName']}"></td>
	<td width="250">{$thDeliverphone}</td>
	<td width=""><input type="text" class="underlineInput" value="{$freight['driverMobile']}"></span></td>
</tr>
<tr class="odd" align="right">
	<td width="250">{$thPlatenumber}</td>
	<td width="250"><input type="text" class="underlineInput" value="{$freight['carID']}"></td>
	<td width="250">{$thDeliverID}</td>
	<td width="250"><input type="text" class="underlineInput" value="{$freight['driverID']}"></span></td>
</tr>
<tr class="odd" align="right">
	<td width="250">{$thShippingcomp}</td>
	<td width="250"><input type="text" class="underlineInput" value="{$freight['freightCompany']}"></td>
	<td width="250">{$thDeliverconfirm}</td>
	<td width="250"><input type="text" class="underlineInput" value="{$freight['driverVerify']}"></span></td>
</tr>
</tbody>
</table>

EOF;

		$t = date('F j, Y, H:i:s', TIMENOW);
		$footer=<<<EOD
			<div class="right">Page Created On: {$t}</div>
		New Wisdom Investment Limited
EOD;
		$this->kclass->page['pageTitle']='ORDER - '.$order['orderno'];
		$this->kclass->page['title'] = $thPtitle;
		$this->kclass->page['contact']= $Pcontact;
		$this->kclass->page['caption'] = '<span class="middle bold">Order No.: '.$order['orderno'].'</span>'.$this->kclass->iif($order['customerNo']!='', ' (Cust. Order No.: '.$order['customerNo'].')', '').'<br>Created: '.date('Y-m-d', $order['created']).'. Modified: '.date('Y-m-d', $order['modified']).' NW-QR-SL001 V1.01';
		$this->kclass->page['footer']=$footer;
	}
	//询价单统计
	function printIstatistics(){
 //  print_r($this->kclass->input);
  if($this->kclass->input['show']=='default'){
			$condition= ' inquiry.killed=0 ';
		}else{
        $condition='1=1 ';
		}
		////按询价单类型号
		if($this->kclass->input['typeid']>0){
			$condition.=" AND inquiry.inquirytype=".$this->kclass->input['typeid']." ";
		}
        $this->kclass->input['orderstarttime']=trim($this->kclass->input['orderstarttime']);
		$this->kclass->input['orderendtime']=trim($this->kclass->input['orderendtime']);
		$this->kclass->input['sourcestarttime']=trim($this->kclass->input['sourcestarttime']);
		$this->kclass->input['sourceendtime']=trim($this->kclass->input['sourceendtime']);
		$this->kclass->input['shipstarttime']=trim($this->kclass->input['shipstarttime']);
		$this->kclass->input['shipendtime']=trim($this->kclass->input['shipendtime']);
		$this->kclass->input['startdate']=trim($this->kclass->input['startdate']);
		$this->kclass->input['enddate']=trim($this->kclass->input['enddate']);
		//按客户名
		$this->kclass->input['customer']=trim($this->kclass->input['customer']);
		//print_r(strlen($this->kclass->input['customer']));
		if(strlen($this->kclass->input['customer'])>3){
			$customers=$this->kclass->DB->query("SELECT customerid FROM `customer` WHERE INSTR(LCASE(`customer`.title),'".(strtolower($this->kclass->input['customer']))."')>0");
			if($this->kclass->DB->numRows()>0){
				while($customer=$this->kclass->DB->fetchArray($customers)){
					$customerid[]=$customer['customerid'];
				}
				$condition.=" AND `inquiry`.customerid IN (".implode(',', $customerid).")";
				$query['customer']=$this->kclass->input['customer'];
			}
		}
    //国家
		if($this->kclass->input['regionid']>0){
			$condition.=" AND `region`.countryid='".$this->kclass->input['regionid']."' ";
			$query['regionid']=$this->kclass->input['regionid'];
		}
		//运送方式
		if($this->kclass->input['shipmethodid']>0){
			$condition.="AND `inquiry`.shipmethodid='".$this->kclass->input['shipmethodid']."' ";
			$query['shipmethodid']=$this->kclass->input['shipmethodid'];
		}
		//付款方式
		if($this->kclass->input['paymenttermid']>0){
			$condition.="AND `inquiry`.paymenttermid='".$this->kclass->input['paymenttermid']."'";
			$query['paymenttermid']=$this->kclass->input['paymenttermid'];
		}
		//询价单创建开始时间
		if($this->kclass->input['orderstarttime']!=''){
			$year=substr($this->kclass->input['orderstarttime'],0,4);
			$month=substr($this->kclass->input['orderstarttime'],5,2);
			$day=substr($this->kclass->input['orderstarttime'],8,2);
			if(checkdate($month,$day,$year)){
				$this->kclass->input['orderstarttime']=mktime(0,0,0,$month,$day,$year);
			}
			if($this->kclass->input['orderstarttime']>0){
				$condition.=" AND `inquiry`.created>='".$this->kclass->input['orderstarttime']."' ";
			}
		}
		//询价单创建结束时间
		if($this->kclass->input['orderendtime']!=''){
			$year=substr($this->kclass->input['orderendtime'],0,4);
			$month=substr($this->kclass->input['orderendtime'],5,2);
			$day=substr($this->kclass->input['orderendtime'],8,2);
			if(checkdate($month,$day,$year)){
				$this->kclass->input['orderendtime']=mktime(0,0,0,$month,$day,$year);
			}
			if($this->kclass->input['orderendtime']>0){
				$condition.=" AND `inquiry`.created<='".$this->kclass->input['orderendtime']."'";
			}
		}
		//查询来源时间内符合添加的客户
		if( $this->kclass->input['sourcestarttime']!='' AND $this->kclass->input['shipstarttime']!='' AND isset($this->kclass->input['customersourceid'])){
			//
		if($this->kclass->input['sourcestarttime']!=''){
			$year=substr($this->kclass->input['sourcestarttime'],0,4);
			$month=substr($this->kclass->input['sourcestarttime'],5,2);
			$day=substr($this->kclass->input['sourcestarttime'],8,2);
			if(checkdate($month,$day,$year)){
				$this->kclass->input['sourcestarttime']=mktime(0,0,0,$month,$day,$year);
			}
			if($this->kclass->input['sourcestarttime']>0){
				$condition.=" AND `customer`.sourcedate>='".$this->kclass->input['sourcestarttime']."'";
			}
		}
		//
		if($this->kclass->input['sourceendtime']!=''){
			$year=substr($this->kclass->input['sourceendtime'],0,4);
			$month=substr($this->kclass->input['sourceendtime'],5,2);
			$day=substr($this->kclass->input['sourceendtime'],8,2);
			if(checkdate($month,$day,$year)){
				$this->kclass->input['sourceendtime']=mktime(0,0,0,$month,$day,$year);
			}
			if($this->kclass->input['sourceendtime']>0){
				$condition.=" AND `customer`.sourcedate<='".$this->kclass->input['sourceendtime']."'";
			}
		}
		//
		if(isset($this->kclass->input['customersourceid'])){
			$condition.="AND `customersource`.customersourceid IN (".implode(',',$this->kclass->input['customersourceid']).")";
		}
		//print_r($customercondition);
		$customersource=$this->kclass->DB->query("SELECT `customer`.customerid   ,`customersource`.customersourceid 
		                                         FROM `customer`
												LEFT JOIN `customersource` ON (`customersource`.customersourceid=`customer`.customersourceid) 
												 WHERE 1=1  ".$customercondition." ");
		if($this->kclass->DB->numRows()>0){
			while($customersources=$this->kclass->DB->fetchArray($customersource)){
				$customerid[]=$customersources['customerid'];
			}
			$condition.="AND `inquiry`.customerid IN (".implode(',',$customerid).")";
		}
		}
	  //发货开始时间
		if($this->kclass->input['shipstarttime']!=''){
			$year=substr($this->kclass->input['shipstarttime'],0,4);
			$month=substr($this->kclass->input['shipstarttime'],5,2);
			$day=substr($this->kclass->input['shipstarttime'],8,2);
			if(checkdate($month,$day,$year)){
				$this->kclass->input['shipstarttime']=mktime(0,0,0,$month,$day,$year);
			}
			if($this->kclass->input['shipstarttime']>0){
				$condition.=" AND  `inquiry`.dateline>='".$this->kclass->input['shipstarttime']."'";
			}
		}
		//发货结束时间
		if($this->kclass->input['shipendtime']!=''){
			$year=substr($this->kclass->input['shipendtime'],0,4);
			$month=substr($this->kclass->input['shipendtime'],5,2);
			$day=substr($this->kclass->input['shipendtime'],8,2);
			if(checkdate($month,$day,$year)){
				$this->kclass->input['shipendtime']=mktime(0,0,0,$month,$day,$year);
			}
			if($this->kclass->input['shipendtime']>0){
				$condition.=" AND  `inquiry`.dateline<='".$this->kclass->input['shipendtime']."'";
			}
		}
		if($this->kclass->input['orderby']=='orderid'){
			$orderby="ORDER BY  `inquiry`.inquiryid";
		}elseif($this->kclass->input['orderby']=='customer'){
			$orderby="ORDER BY  `customer`.title";
		}elseif($this->kclass->input['orderby']=='dateline'){
           $orderby="ORDER BY  `inquiry`.dateline";
		}elseif($this->kclass->input['orderby']=='orderquantity'){
           $orderby="ORDER BY  `inquiry`.dateline";
		}
		if($this->kclass->input['direction']=='ASC'){
			$direction='ASC';
		}else{
			$direction="DESC";
		}

		$inquiry=$this->kclass->DB->query("
				SELECT `inquiry`.*,`deliveryterm`.title AS dtitle,`customer`.title AS ctitle ,`customer`.regionid,`customer`.linkman , `customer`.linkman2 ,`customer`.mobile,`customer`.customerid,`customer`.mobile,`customer`.telephone,`customer`.telephone2,`customer`.fax,`customer`.fax2,`customer`.email,`customer`.email2,`region`.country,`region`.regionid, `region`.countryid,`region`.state,`member`.realname AS mname
				FROM `inquiry` 
				LEFT JOIN `deliveryterm`   ON(`deliveryterm`.deliverytermid=`inquiry`.deliverytermid)
				LEFT JOIN `customer`        ON(`customer`.customerid=`inquiry`.customerid)
				LEFT JOIN `region`             ON(`region`.regionid=`customer`.regionid)
				LEFT JOIN `member`         ON (`member`.userid=`inquiry`.creator)
				WHERE   ".$condition." ".$orderby." ".$direction."
				");
        if($inquirynum=$this->kclass->DB->numRows()){
			$i=1;
			$countryid=$regionid=$customerid=array();
			while($inquirys=$this->kclass->DB->fetchArray($inquiry)){
				switch($inquirys['inquirytype']){
					case 0:
						$inquirytype="未知类型";
					break;
					case 1:
						$inquirytype="无型号无数量";
						break;
					case 2:
						$inquirytype="无型号有数量";
						break;
					case 3:
						$inquirytype="有型号无数量";
						break;
					case 4:
						$inquirytype="有型号有数量";
						break;
				}
				if($inquirys['countryid']==0){
					$countryid[]=$inquirys['regionid'];
					$regionid[]=$inquirys['regionid'];
				}else{
					$countryid[]=$inquirys['countryid'];
					$regionid[]=$inquirys['regionid'];
				}
				$customerid[]=$inquirys['customerid'];
				$productItem=$this->kclass->relatedIproducts(array('module'=>'inquiry','moduleid'=>$inquirys['inquiryid']));
				if($inquirys['linkman']!=''){
					$linkman=$inquirys['linkman'];
					$connact=$this->kclass->iif($inquirys['telephone'],"<div>电话：".$inquirys['telephone'],'').$this->kclass->iif($inquirys['mobile'],"</div><div>手机：".$inquirys['mobile'],'').$this->kclass->iif($inquirys['fax'],"</div><div>传真：".$inquirys['fax'],'').$this->kclass->iif($inquirys['email'],"</div><div>邮件：".$inquirys['email'].'</div>','');
				}else{
					$linkman=$inquirys['linkman2'];
					$connact=$this->kclass->iif($inquirys['telephone2'],"电话：".$inquirys['telephone2'],'').$this->kclass->iif($inquirys['mobile2'],"<br/>手机：".$inquirys['mobile2'],'').$this->kclass->iif($inquirys['fax2'],"<br/>传真：".$inquirys['fax2'],'').$this->kclass->iif($inquirys['email2'],"<br/>邮件：".$inquirys['email2'],'');
				}
              $item.='<tr>
							<td align="center">'.$i.'</td>
							<td align="center">'.$inquirys['inquiryid'].'</td>
							<td align="center">'.$inquirytype.'</td>
							<td align="center">'.$inquirys['dtitle'].'</td>
							<td align="center">'.$inquirys['country'].'</td>
							<td align="center">'.$inquirys['ctitle'].'</td>
							<td align="center">'.$inquirys['contact'].'</td>
							<td align="center">'.$connact.'</td>
							<td align="center"><table frame="'.$this->kclass->iif($productItem['panel'],'below','void').'" border="0"  width="100%" height="100%">'.$this->kclass->iif($inquirys['standard']!='' &  $inquirys['material']!='' & $inquirys['material']!='','<tr><td class="red" align="center" width="25%" colspan="3" align="center"><b>询价内容</b></td></tr><tr><td style="word-break:break-all"  colspan="3" align="center">'.$inquirys['standard'].'</td></tr>',' ').'</table>'.$productItem['panel'].'</td>
							<td align="center">'.$inquirys['mname'].'</td>
							</tr>';
							//<td class="red" align="center" width="25%"><b>主要材料</b></td><td class="red" align="center" width="25%"><b>工艺说明</b></td><td colspan="2" class="red" align="center"  width="25%"><b>备注</b></td>
				$i++;
			}
		
			$countryid=array_unique($countryid);
			$regionid=array_unique($regionid);
			$customerid=array_unique($customerid);
			$country=$this->kclass->DB->query("
							SELECT `region`.country,`region`.regionid,`region`.countryid FROM `region` WHERE `region`.regionid IN (".implode(',', $countryid).") AND `region`.countryid=0
			");
			$itemstr='<tr><td  width="5%" align="center" class="red"><b>ID</b></td><td align="center" class="red" width="20%"><b>国家</b></td><td align="center" class="red" width="10%"><b>询价单数量</b></td><td align="center" class="red" width="10%"><b>询价产品数量</b></td>';
			$itemstr.='<td align="center" class="red"  width="30%"><b>客户名称</b></td><td align="center" class="red" width="10%"><b>询价单数量（客户）</b></td><td align="center" class="red" width="10%"><b>产品数量（客户）</b></td></tr>';
			if($num=$this->kclass->DB->numRows()){
				$i=1;
				while($countrys=$this->kclass->DB->fetchArray($country)){
					$findregion=$this->kclass->getRegion(array('countryid'=>$countrys['regionid'],'regionid'=>$regionid,'condition'=>$condition));//取得该询价单下各个国家的regionid
				    $region=$this->kclass->relatedInquiryregion(array('regionid'=>$findregion,'condition'=>$condition));//取得各个地区下的询价单
					$customeritem=$this->kclass->relatedInquirycustomer(array('regionid'=>$findregion,'customerid'=>$customerid,'condition'=>$condition));//取得每个国家对应客户的询价单
					$customeritem['num']=$customeritem['num']+1;
				$itemstr.='<tr><td rowspan="'.$customeritem['num'].'" align="center"><b>'.$i.'</b></td>
									<td rowspan="'.$customeritem['num'].'" align="center">'.$countrys['country'].'</td>
									<td rowspan="'.$customeritem['num'].'" align="center">'.$region['quotingnum'].'</td>
									<td rowspan="'.$customeritem['num'].'" align="center">'.$region['quantitys'].'</td>
									'.$customeritem['panel'].'
									</tr>';
						$quantitys+=$region['quantitys'];
						$i++;
				}
			}
			$item.='<tr><td align="center">合计</td> <td colspan="12"><table frame="void" border="0"  width=100% height=100%>'.$itemstr.'</table></td></tr>';
			$item.='<tr><td colspan="13"> 报价单总计：共有报价单数：<b>'.$inquirynum.'</b>,　 产品总数量：<b>'.$quantitys.'</b>,　</tr>';
		}
    
    
	$body=<<<EOF
	<table class="hundred">
      <tr><td align="center" width="100px">ID</td><td width="100px">询价单编号</td><td width="80px" align="center">询价类型</td><td width="150px" align="center">交货条款</td><td align="center" width="150px">国家</td><td align="center" width="120px">客户</td><td align="center" width="120px">联系人</td><td align="center"  width="250px" style="word-break:break-all">联系方式</td><td align="center" width="400px" >询价单详细</td><td align="center" width="150px">创建人</td></tr>
		{$item}
	  </table>
	  <span class="small gray">注意：①该询价单统计的产品数量仅是在该条件下灯的总数<br/> 　&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;②在合计列下产品数量是指灯类的数量<br/> 　</span>
EOF;
	$t = date('F j, Y, H:i:s', TIMENOW);
		$footer=<<<EOD
			<div class="right">Page Created On: {$t}</div>
		New Wisdom Investment Limited
EOD;
		$this->kclass->page['pageTitle']='询价单单统计 - '.$invoice['invoiceno'];
		$this->kclass->page['title'] = '询价单统计';
		$this->kclass->page['contact']='公司地址：深圳市龙岗区宝荷路嶂背大道9号1楼<br>
		Tel: 86-755-83911841  Fax: 86-755-83906115 ';
		$this->kclass->page['caption'] = '<span class="middle bold"> '.$product['title'].'</span>'.$this->kclass->iif($invoice['customerNo']!='', ' (Cust. Order No.: '.$invoice['customerNo'].')', '');
		$this->kclass->page['main']=$body;
		$this->kclass->page['footer']=$footer;
	}

	//报价单统计
	function printQstatistics(){
		echo "<pre>";
        print_r($this->kclass->input);
        echo "</pre>";
	   if(!$this->kclass->input['requestMethod'] =='post')$this->kclass->boinkIt('/s.php?module=customer');
		
		if($this->kclass->input['show']=='default'){
			$condition='  `quoting`.killed=0 ';
		}else{
        $condition='1=1 ';
		}
		////按按报价单类型号
		if($this->kclass->input['typeid']>0){
			$condition.=" AND `quoting`.typeid =".$this->kclass->input['typeid']." ";
		}
		//按照产品
		//if($this->kclass->input['productid']>0){
			//$condition.="AND `item`.module='order' AND //`item`.productid='".$this->kclass->input['productid']."'";
	//	}
		$this->kclass->input['orderstarttime']=trim($this->kclass->input['orderstarttime']);
		$this->kclass->input['orderendtime']=trim($this->kclass->input['orderendtime']);
		$this->kclass->input['sourcestarttime']=trim($this->kclass->input['sourcestarttime']);
		$this->kclass->input['sourceendtime']=trim($this->kclass->input['sourceendtime']);
		$this->kclass->input['shipstarttime']=trim($this->kclass->input['shipstarttime']);
		$this->kclass->input['shipendtime']=trim($this->kclass->input['shipendtime']);
		$this->kclass->input['startdate']=trim($this->kclass->input['startdate']);
		$this->kclass->input['enddate']=trim($this->kclass->input['enddate']);
		//按客户名
		$this->kclass->input['customer']=trim($this->kclass->input['customer']);
		//print_r(strlen($this->kclass->input['customer']));
		if(strlen($this->kclass->input['customer'])>3){
			$customers=$this->kclass->DB->query("SELECT customerid FROM `customer` WHERE INSTR(LCASE(`customer`.title),'".(strtolower($this->kclass->input['customer']))."')>0");
			if($this->kclass->DB->numRows()>0){
				while($customer=$this->kclass->DB->fetchArray($customers)){
					$customerid[]=$customer['customerid'];
				}
				echo "<pre>";
        		print_r($customerid);
        		echo "</pre>";
				$condition.=" AND `quoting`.customerid IN (".implode(',', $customerid).")";
				$query['customer']=$this->kclass->input['customer'];
			}
		}
    //国家
		if($this->kclass->input['regionid']>0){
			$condition.=" AND `region`.countryid='".$this->kclass->input['regionid']."' ";
			$query['regionid']=$this->kclass->input['regionid'];
		}
		//运送方式
		if($this->kclass->input['shipmethodid']>0){
			$condition.="AND `quoting`.shipmethodid='".$this->kclass->input['shipmethodid']."' ";
			$query['shipmethodid']=$this->kclass->input['shipmethodid'];
		}
		//付款方式
		if($this->kclass->input['paymenttermid']>0){
			$condition.="AND `quoting`.paymenttermid='".$this->kclass->input['paymenttermid']."'";
			$query['paymenttermid']=$this->kclass->input['paymenttermid'];
		}
		//订单创建开始时间
		if($this->kclass->input['orderstarttime']!=''){
			$year=substr($this->kclass->input['orderstarttime'],0,4);
			$month=substr($this->kclass->input['orderstarttime'],5,2);
			$day=substr($this->kclass->input['orderstarttime'],8,2);
			if(checkdate($month,$day,$year)){
				$this->kclass->input['orderstarttime']=mktime(0,0,0,$month,$day,$year);
			}
			if($this->kclass->input['orderstarttime']>0){
				$condition.=" AND `quoting`.created>='".$this->kclass->input['orderstarttime']."' ";
			}
		}
		//报价单创建结束时间
		if($this->kclass->input['orderendtime']!=''){
			$year=substr($this->kclass->input['orderendtime'],0,4);
			$month=substr($this->kclass->input['orderendtime'],5,2);
			$day=substr($this->kclass->input['orderendtime'],8,2);
			if(checkdate($month,$day,$year)){
				$this->kclass->input['orderendtime']=mktime(0,0,0,$month,$day,$year);
			}
			if($this->kclass->input['orderendtime']>0){
				$condition.=" AND `quoting`.created<='".$this->kclass->input['orderendtime']."'";
			}
		}
		//查询来源时间内符合添加的客户
		if( $this->kclass->input['sourcestarttime']!='' AND $this->kclass->input['shipstarttime']!='' AND isset($this->kclass->input['customersourceid'])){
			//
		if($this->kclass->input['sourcestarttime']!=''){
			$year=substr($this->kclass->input['sourcestarttime'],0,4);
			$month=substr($this->kclass->input['sourcestarttime'],5,2);
			$day=substr($this->kclass->input['sourcestarttime'],8,2);
			if(checkdate($month,$day,$year)){
				$this->kclass->input['sourcestarttime']=mktime(0,0,0,$month,$day,$year);
			}
			if($this->kclass->input['sourcestarttime']>0){
				$condition.=" AND `customer`.sourcedate>='".$this->kclass->input['sourcestarttime']."'";
			}
		}
		//
		if($this->kclass->input['sourceendtime']!=''){
			$year=substr($this->kclass->input['sourceendtime'],0,4);
			$month=substr($this->kclass->input['sourceendtime'],5,2);
			$day=substr($this->kclass->input['sourceendtime'],8,2);
			if(checkdate($month,$day,$year)){
				$this->kclass->input['sourceendtime']=mktime(0,0,0,$month,$day,$year);
			}
			if($this->kclass->input['sourceendtime']>0){
				$condition.=" AND `customer`.sourcedate<='".$this->kclass->input['sourceendtime']."'";
			}
		}
		//
		if(isset($this->kclass->input['customersourceid'])){
			$condition.="AND `customersource`.customersourceid IN (".implode(',',$this->kclass->input['customersourceid']).")";
		}
		//print_r($customercondition);
		$customersource=$this->kclass->DB->query("SELECT `customer`.customerid   ,`customersource`.customersourceid 
		                                         FROM `customer`
												LEFT JOIN `customersource` ON (`customersource`.customersourceid=`customer`.customersourceid) 
												 WHERE 1=1  ".$customercondition." ");
		if($this->kclass->DB->numRows()>0){
			while($customersources=$this->kclass->DB->fetchArray($customersource)){
				$customerid[]=$customersources['customerid'];
			}
			$condition.="AND `quoting`.customerid IN (".implode(',',$customerid).")";
		}
		}
	  //发货开始时间
		if($this->kclass->input['shipstarttime']!=''){
			$year=substr($this->kclass->input['shipstarttime'],0,4);
			$month=substr($this->kclass->input['shipstarttime'],5,2);
			$day=substr($this->kclass->input['shipstarttime'],8,2);
			if(checkdate($month,$day,$year)){
				$this->kclass->input['shipstarttime']=mktime(0,0,0,$month,$day,$year);
			}
			if($this->kclass->input['shipstarttime']>0){
				$condition.=" AND  `quoting`.dateline>='".$this->kclass->input['shipstarttime']."'";
			}
		}
		//发货结束时间
		if($this->kclass->input['shipendtime']!=''){
			$year=substr($this->kclass->input['shipendtime'],0,4);
			$month=substr($this->kclass->input['shipendtime'],5,2);
			$day=substr($this->kclass->input['shipendtime'],8,2);
			if(checkdate($month,$day,$year)){
				$this->kclass->input['shipendtime']=mktime(0,0,0,$month,$day,$year);
			}
			if($this->kclass->input['shipendtime']>0){
				$condition.=" AND  `quoting`.dateline<='".$this->kclass->input['shipendtime']."'";
			}
		}
		if($this->kclass->input['orderby']=='orderid'){
			$orderby="ORDER BY  `quoting`.quotingid";
		}elseif($this->kclass->input['orderby']=='customer'){
			$orderby="ORDER BY  `customer`.title";
		}elseif($this->kclass->input['orderby']=='dateline'){
           $orderby="ORDER BY  `quoting`.dateline";
		}elseif($this->kclass->input['orderby']=='orderquantity'){
           $orderby="ORDER BY  `quoting`.dateline";
		}
		if($this->kclass->input['direction']=='ASC'){
			$direction='ASC';
		}else{
			$direction="DESC";
		}
		$quoting=$this->kclass->DB->query("SELECT 
		                           `quoting`.*,q.title AS qtitle,d.title AS dtitle,p.title AS ptitle,p.regionid,region.regionid,region.countryid,region.country AS rtitle,region.encountry AS rentitle,`customer`.regionid,`customer`.title AS ctitle,`customer`.linkman , `customer`.linkman2 ,`customer`.mobile,`customer`.customerid,`customer`.mobile,`customer`.telephone,`customer`.telephone2,`customer`.fax,`customer`.fax2,`customer`.email,`customer`.email2, s.title AS stitle ,s.entitle AS sentitle,m.realname AS mname
								   FROM `quoting`
								   LEFT JOIN `quotingtype`   AS  q  ON(q.typeid=`quoting`.typeid)
								   LEFT JOIN `deliveryterm`  AS  d  ON(`quoting`.deliverytermid=d.deliverytermid)
								   LEFT JOIN `port`                AS  p  ON(p.portid=`quoting`.portid)
								   LEFT JOIN `customer`		 ON(`customer`.customerid=`quoting`.customerid)
								   LEFT JOIN `region`              ON(region.regionid=`customer`.regionid)
								   LEFT JOIN `shipmethod`  AS s  ON(s.shipmethodid=`quoting`.shipmethodid)   
								   LEFT  JOIN `member`        AS   m ON(m.userid=`quoting`.creator)
								   WHERE   ".$condition." ".$orderby." ".$direction."
								   ");
		if($quotingnum=$this->kclass->DB->numRows()){
			$i=1;
			$customerid=$countryid=$regionid=array();
			while($quotings=$this->kclass->DB->fetchArray($quoting)){
               $customerid[]=$quotings['customerid'];
			   if($quotings['countryid']!=0){
			   $countryid[]=$quotings['countryid'];
			   $regionid[]=$quotings['regionid'];
			   }else{
				$countryid[]=$quotings['regionid'];
				$regionid[]=$quotings['regionid'];
			   }
			   $customerid[]=$quotings['customerid'];
				$productItem=$this->kclass->relatedProducts(array('module'=>'quoting','moduleid'=>$quotings['quotingid']));
				if($quotings['linkman']!=''){
					$linkman=$quotings['linkman'];
					$connact=$this->kclass->iif($quotings['telephone'],"<div>电话：".$quotings['telephone'],'').$this->kclass->iif($quotings['mobile'],"</div><div>手机：".$quotings['mobile'],'').$this->kclass->iif($quotings['fax'],"</div><div>传真：".$quotings['fax'],'').$this->kclass->iif($quotings['email'],"</div><div>邮件：".$quotings['email'].'</div>','');
				}else{
					$linkman=$quotings['linkman2'];
					$connact=$this->kclass->iif($quotings['telephone2'],"电话：".$quotings['telephone2'],'').$this->kclass->iif($quotings['mobile2'],"<br/>手机：".$quotings['mobile2'],'').$this->kclass->iif($quotings['fax2'],"<br/>传真：".$quotings['fax2'],'').$this->kclass->iif($quotings['email2'],"<br/>邮件：".$quotings['email2'],'');
				}
				$date=date('Y-m-d',$quotings['dateline']);
				if($quotings['ifverify']==0){
					$status='<span class="red">未审核</span>';
				}elseif($quotings['ifverify']==1){
					$status='<span class="green">审核通过</span>';
				}else{
					$status='<span class="darkred">审核未通过</span>';
				}
				$item.='<tr>
					<td align="center">'.$i.'</td>
					<td align="center">'.$quotings['qtitle'].'</td>
					<td align="center">'.$quotings['dtitle'].'</td>
					<td align="center">'.$quotings['rtitle'].'</td>
					<td align="center">'.$quotings['ctitle'].'</td>
					<td align="center">'.$linkman.'</td>
					<td align="center">'.$connact.'</td>
					<td align="center"><table frame="void" border="0"  width=100% height=100%>'.$productItem['panel'].'</table></td>
					<td align="center">'.$quotings['stitle'].'</td>
					<td align="center">'.$quotings['freight'].'</td>
					<td align="center">'.$date.'</td>
					<td align="center">'.$quotings['mname'].'</td>
					<td align="center">'.$status.'</td>
				</tr>';
				$i++;
			}
			$itemstr='<tr><td align="center" class="red" width="50px"><b>ID</b></td><td align="center" class="red" width="200px" ><b>国家名称</b></td><td align="center" class="red" width="150px"><b>报价单数量（国家）</b></td><td align="center" class="red" width="150px"><b>产品数量（国家）</b></td><td align="center" class="red" width="150px"><b>报价单金额（国家）</b></td>';
					//$customerstr='<tr><td class="red" align="center">客户姓名</td><td class="red" align="center">订单数量</td></tr>';
					$itemstr.='<td align="center" class="red"  ><b>客户名称</b></td><td align="center" class="red"width="200px"><b>报价单数量（客户）</b></td><td align="center" class="red" width="150px"><b>产品数量（客户）</b></td><td align="center" class="red" width="150px"><b>报价单金额（客户）</b></td></tr>';
			/*$customerid=array_unique($customerid);
			//每个客户对应的订单数量，订单详细
				$customerid=array_filter(array_unique($customerid));
				$customerorder=$this->kclass->DB->query(" SELECT `customer`. title ,`customer`.customerid,`customer`.regionid,`region`.countryid ,`region`.regionid,`region`.countryid
									FROM `customer`
									LEFT JOIN `region`     ON(`region`.regionid=`customer`.regionid)
									WHERE   `customer` .customerid IN (".implode(',',$customerid).") AND `region`.regionid=749 ");
				if($customernum=$this->kclass->DB->numRows()){
					$i=1;
					$num=$num+1;
					//$customerstr='<tr><td class="red" align="center">客户姓名</td><td class="red" align="center">订单数量</td></tr>';
					while($customerorders=$this->kclass->DB->fetchArray($customerorder)){
						$findregion=$this->kclass->getRegion(array('countryid'=>$countrys['regionid'],'regionid'=>$regionid,'condition'=>$condition));//取得该询价单下各个国家的regionid
						$theorders=$this->kclass->relatedCustomerquoting(array('customerid'=>$customerorders['customerid'] ,'condition'=>$condition,'killed'=>$killed));
					//	print_r($theorders);
						//$customerstr.='<b>'.$customerorders['title'].'</b>：<b><span class="green">'.$ordernum.'</span></b><br/>';
						$itemstrs.='<tr><td  align="center"><b>'.$customerorders['title'].'</b></td><td width="30%" align="center">'.$theorders['quotingnum'].'</td><td align="center">'.$theorders['quantitys'].'</td><td align="center">'.$theorders['totals'].'</tr></td>';      
						$i++;
					}
				}
               */
			 //每个国家对应的报价单详细
			$countryid=array_filter(array_unique($countryid));
			$regionid=array_filter(array_unique($regionid));
			$customerid=array_unique($customerid);
				$regionorder=$this->kclass->DB->query("SELECT `region`.country,`region`.regionid,`region`.countryid FROM `region` WHERE `region`.regionid IN (".implode(',', $countryid).") AND `region`.countryid=0 ");
				if($num=$this->kclass->DB->numRows()){
					$num=$num+1;
					$i=1;
					while($regionorders=$this->kclass->DB->fetchArray($regionorder)){
						$findregion=$this->kclass->getRegion(array('countryid'=>$regionorders['regionid'],'regionid'=>$regionid,'condition'=>$condition));//取得该询价单下各个国家的regionid
						$theorder=$this->kclass->relatedquotingRegion(array('regionid'=>$findregion,'condition'=>$condition));
						$customeritem=$this->kclass->relatedquotingCustomeritem(array('regionid'=>$findregion,'customerid'=>$customerid,'condition'=>$condition));
						$customeritem['num']=$customeritem['num']+1;
                        $itemstr.='<tr><td  align="center" rowspan="'.$customeritem['num'].'"><b>'.$i.'</b></td><td  align="center" rowspan="'.$customeritem['num'].'">'.$regionorders['country'].'</td><td  align="center" rowspan="'.$customeritem['num'].'">'.$theorder['quotingnum'].'</td><td align="center" rowspan="'.$customeritem['num'].'">'.$theorder['quantitys'].'</td><td align="center" rowspan="'.$customeritem['num'].'">'.$theorder['totals'].'</td>'.$customeritem['panel'].'</tr>';
						$quantitys+=$theorder['quantitys'];
						$totals+=$theorder['totals'];
						$i++;
					}
				}	
			$item.='<tr><td align="center">合计</td> <td colspan="12"><table frame="void" border="0"  width=100% height=100%>'.$itemstr.'</table></td></tr>';
			$item.='<tr><td colspan="13"> 报价单总计：共有报价单数：<b>'.$quotingnum.'</b>,　 产品总数量：<b>'.$quantitys.'</b>,　报价单总金额：<b>'.$totals.'</b>,</tr>';
		}else{
          $item='<tr ><td colspan="13"><span class="red" >该条件下不存在对应的订单</span></td><tr>';
		}
       
	  $body=<<<EOF
	  <table class="hundred">
      <tr><td align="center" width="50px">报价单序号</td><td width="80px" align="center">报价类型</td><td width="100px" align="center">交货条款</td><td align="center" width="100px">国家</td><td align="center" width="120px">客户</td><td align="center" width="120px">联系人</td><td align="left"  width="250px" style="word-break:break-all">联系方式</td><td align="center">报价单详细</td><td align="center" width="100px">运送方式</td><td align="center" width="50px">运费</td><td align="center" width="100px">交货日期</td><td align="center" width="100px">创建人</td><td align="center" width="100px">报价单审核</td></tr>
		{$item}
	  </table>
	  <span class="small gray">注意：①该报价单统计的产品数量仅是在该条件下灯的总数<br/> 　&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;②在每条报价单下面的总价是指所有产品的总价，包括：灯类、充电类、配件类<br/> 　&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;③在合计列下产品数量是指灯类的数量，订单金额包括：灯类、充电类、配件类<br/> 　&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;④该报价单统计的总计统计是指在该条件下所有产品的总金额，包括：灯类，充电类，配件类</span>
EOF;
		$t = date('F j, Y, H:i:s', TIMENOW);
		$footer=<<<EOD
			<div class="right">Page Created On: {$t}</div>
		New Wisdom Investment Limited
EOD;
		$this->kclass->page['pageTitle']='报价单统计 - '.$invoice['invoiceno'];
		$this->kclass->page['title'] = '报价单统计';
		$this->kclass->page['contact']='公司地址：深圳市龙岗区宝荷路嶂背大道9号1楼<br>
		Tel: 86-755-83911841  Fax: 86-755-83906115 ';
		$this->kclass->page['caption'] = '<span class="middle bold"> '.$product['title'].'</span>'.$this->kclass->iif($invoice['customerNo']!='', ' (Cust. Order No.: '.$invoice['customerNo'].')', '');
		$this->kclass->page['main']=$body;
		$this->kclass->page['footer']=$footer;
	}


    //订单统计
	function orderstatistics(){
		// echo "<pre>";
		// var_dump($this->kclass->input);
		// echo "</pre>";
		if(!$this->kclass->input['requestMethod'] =='post')$this->kclass->boinkIt('/s.php?module=order');
		//按订单号
		if(strlen($this->kclass->input['orderno'])>=3){
			$condition.="AND INSTR(LCASE(`order`.orderno),'".(strtolower($this->kclass->input['orderno']))."')>0 ";
			$query['orderno']=$this->kclass->input['orderno'];
		}
		//按照产品
		//if($this->kclass->input['productid']>0){
			//$condition.="AND `item`.module='order' AND //`item`.productid='".$this->kclass->input['productid']."'";
	//	}
		$this->kclass->input['orderstarttime']=trim($this->kclass->input['orderstarttime']);
		$this->kclass->input['orderendtime']=trim($this->kclass->input['orderendtime']);
		$this->kclass->input['sourcestarttime']=trim($this->kclass->input['sourcestarttime']);
		$this->kclass->input['sourceendtime']=trim($this->kclass->input['sourceendtime']);
		$this->kclass->input['shipstarttime']=trim($this->kclass->input['shipstarttime']);
		$this->kclass->input['shipendtime']=trim($this->kclass->input['shipendtime']);
		$this->kclass->input['startdate']=trim($this->kclass->input['startdate']);
		$this->kclass->input['enddate']=trim($this->kclass->input['enddate']);
		//按客户名
		$this->kclass->input['customer']=trim($this->kclass->input['customer']);
		//print_r(strlen($this->kclass->input['customer']));
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
		//业务员
		if($this->kclass->input['director']>0){
			$condition.="AND `order`.director='".$this->kclass->input['director']."' ";
			$query['director']=$this->kclass->input['director'];
		}
		//国家
		if($this->kclass->input['regionid']>0){
			if($this->kclass->input['countryid']==0){
				$cid=$this->kclass->input['regionid'];
			}else{
				$cid=$this->kclass->input['countryid'];
			}
			$condition.=" AND (`region`.countryid='".$cid."' OR (`region`.countryid=0 AND `region`.regionid=".$cid.")) ";
			$query['regionid']=$this->kclass->input['regionid'];
		}
		//运送方式
		if($this->kclass->input['shipmethodid']>0){
			$condition.="AND `order`.shipmethodid='".$this->kclass->input['shipmethodid']."' ";
			$query['shipmethodid']=$this->kclass->input['shipmethodid'];
		}
		//付款方式
		if($this->kclass->input['paymenttermid']>0){
			$condition.="AND `order`.paymenttermid='".$this->kclass->input['paymenttermid']."'";
			$query['paymenttermid']=$this->kclass->input['paymenttermid'];
		}
		//订单创建开始时间
		if($this->kclass->input['orderstarttime']!=''){
			$year=substr($this->kclass->input['orderstarttime'],0,4);
			$month=substr($this->kclass->input['orderstarttime'],5,2);
			$day=substr($this->kclass->input['orderstarttime'],8,2);
			if(checkdate($month,$day,$year)){
				$this->kclass->input['orderstarttime']=mktime(0,0,0,$month,$day,$year);
			}
			if($this->kclass->input['orderstarttime']>0){
				$condition.=" AND `order`.created>='".$this->kclass->input['orderstarttime']."' ";
			}
		}
		//订单创建结束时间
		if($this->kclass->input['orderendtime']!=''){
			$year=substr($this->kclass->input['orderendtime'],0,4);
			$month=substr($this->kclass->input['orderendtime'],5,2);
			$day=substr($this->kclass->input['orderendtime'],8,2);
			if(checkdate($month,$day,$year)){
				$this->kclass->input['orderendtime']=mktime(0,0,0,$month,$day,$year);
			}
			if($this->kclass->input['orderendtime']>0){
				$condition.=" AND `order`.created<='".$this->kclass->input['orderendtime']."'";
			}
		}
		//查询来源时间内符合添加的客户
		if( $this->kclass->input['sourcestarttime']!='' AND $this->kclass->input['shipstarttime']!='' AND isset($this->kclass->input['customersourceid'])){
			//
		if($this->kclass->input['sourcestarttime']!=''){
			$year=substr($this->kclass->input['sourcestarttime'],0,4);
			$month=substr($this->kclass->input['sourcestarttime'],5,2);
			$day=substr($this->kclass->input['sourcestarttime'],8,2);
			if(checkdate($month,$day,$year)){
				$this->kclass->input['sourcestarttime']=mktime(0,0,0,$month,$day,$year);
			}
			if($this->kclass->input['sourcestarttime']>0){
				$customercondition=" AND `customer`.sourcedate>='".$this->kclass->input['sourcestarttime']."'";
			}
		}
		//
		if($this->kclass->input['sourceendtime']!=''){
			$year=substr($this->kclass->input['sourceendtime'],0,4);
			$month=substr($this->kclass->input['sourceendtime'],5,2);
			$day=substr($this->kclass->input['sourceendtime'],8,2);
			if(checkdate($month,$day,$year)){
				$this->kclass->input['sourceendtime']=mktime(0,0,0,$month,$day,$year);
			}
			if($this->kclass->input['sourceendtime']>0){
				$customercondition.=" AND `customer`.sourcedate<='".$this->kclass->input['sourceendtime']."'";
			}
		}
		//
		if(isset($this->kclass->input['customersourceid'])){
			$customercondition.="AND `customersource`.customersourceid IN (".implode(',',$this->kclass->input['customersourceid']).")";
		}
		//print_r($customercondition);
		$customersource=$this->kclass->DB->query("SELECT `customer`.customerid   ,`customersource`.customersourceid 
		                                         FROM `customer`
												LEFT JOIN `customersource` ON (`customersource`.customersourceid=`customer`.customersourceid) 
												 WHERE 1=1  ".$customercondition." ");
		if($this->kclass->DB->numRows()>0){
			while($customersources=$this->kclass->DB->fetchArray($customersource)){
				$customerid[]=$customersources['customerid'];
			}
			$condition.="AND `order`.customerid IN (".implode(',',$customerid).")";
		}
		}
	  //发货开始时间
		if($this->kclass->input['shipstarttime']!=''){
			$year=substr($this->kclass->input['shipstarttime'],0,4);
			$month=substr($this->kclass->input['shipstarttime'],5,2);
			$day=substr($this->kclass->input['shipstarttime'],8,2);
			if(checkdate($month,$day,$year)){
				$this->kclass->input['shipstarttime']=mktime(0,0,0,$month,$day,$year);
			}
			if($this->kclass->input['shipstarttime']>0){
				$condition.=" AND `order`.shipmentDate>='".$this->kclass->input['shipstarttime']."'";
			}
		}
		//发货结束时间
		if($this->kclass->input['shipendtime']!=''){
			$year=substr($this->kclass->input['shipendtime'],0,4);
			$month=substr($this->kclass->input['shipendtime'],5,2);
			$day=substr($this->kclass->input['shipendtime'],8,2);
			if(checkdate($month,$day,$year)){
				$this->kclass->input['shipendtime']=mktime(0,0,0,$month,$day,$year);
			}
			if($this->kclass->input['shipendtime']>0){
				$condition.=" AND `order`.shipmentDate<='".$this->kclass->input['shipendtime']."'";
			}
		}
	//	print_r($condition);
		if($this->kcalss->input['show']=='default'){
			$killed="AND `order`.killed >0";
		}elseif($this->kclass->input['show']=='all'){
			$killed="AND 1=1";
		}
		if($this->kclass->input['orderby']=='orderid'){
			$orderby="ORDER BY  `order`.orderid";
		}elseif($this->kclass->input['orderby']=='customer'){
			$orderby="ORDER BY  `customer`.title";
		}elseif($this->kclass->input['orderby']=='dateline'){
           $orderby="ORDER BY  `order`.shipmentDate";
		}elseif($this->kclass->input['orderby']=='orderquantity'){
           $orderby="ORDER BY  `order`.shipmentDate";
		}
		if($this->kclass->input['direction']=='ASC'){
			$direction='ASC';
		}else{
			$direction="DESC";
		}
		if(isset($this->kclass->input['port'])){
			$order=$this->kclass->DB->query(" SELECT `order`.* ,`customer`.customerid ,`customer`.title AS ctitle,`customer`.cntitle AS ccntitle,`region`.regionid ,`region`.country AS rtitle,`region`.countryid,`region`.encountry AS rentitle,`supplier`.title AS stitle ,m.realname AS mname,d.title AS dtitle,p.entitle AS pentitle,p.title AS ptitle,s.title AS sstitle ,ps.title AS pstitle ,ps.entitle AS psentitle
										FROM `order`
										LEFT JOIN `customer` ON(`customer`.customerid=`order`.customerid)
										LEFT JOIN `region`		ON(`region`.regionid=`customer`.regionid)
										LEFT JOIN `supplier`	ON(`supplier`.supplierid=`order`.supplierid)
										LEFT JOIN `member` AS m ON(m.userid=`order`.director)
										LEFT JOIN `paymentstatus` AS ps ON (ps.paymentstatusid=`order`.paymentstatus)
										LEFT JOIN `deliveryterm`  AS d ON(d.deliverytermid=`order`.deliverytermid)
										LEFT JOIN `paymentterm` AS p ON(p.paymenttermid=`order`.paymenttermid)
										LEFT JOIN `shipmethod`    AS s ON (s.shipmethodid=`order`.shipmethodid)
										WHERE `order`.type='PI'  ".$condition." ".$killed." ".$orderby." ".$direction."
										");
			if($ordernumers=$this->kclass->DB->numRows()){
				$i=1;
				$customerid=$regionid=$countryid=$director=array();
				while($orders=$this->kclass->DB->fetchArray($order)){
					$customerid[]=$orders['customerid'];
					$director[]=$orders['director'];
					if($orders['countryid']==0){
						$countryid[]=$orders['regionid'];
						$regionid[]=$orders['regionid'];
					}else{
						$countryid[]=$orders['countryid'];
						$regionid[]=$orders['regionid'];
					}
					$fee=$this->kclass->relatedFees(array('orderid'=>$orders['orderid']));
					$productItem=$this->kclass->relatedProducts(array('module'=>'order','moduleid'=>$orders['orderid']));
					$subtotals=floatval($fee['total'])+floatval($productItem['total']);
					$subtotal=number_format($subtotals,2);
					if($orders['rentitle']!=''){
						$orders['rentitle']='('.$orders['rentitle'].')';
					}
					if($orders['paymenttime']==0 & $orders['paymentamount']==0 & $orders['paymentstatus']==0){
						$orders['paymenttime']='未支付';
						$orders['paymentamount']='未支付';
						$orders['paymentstatus']='未支付';
					}else{
						$orders['paymenttime']=date('Y-m-d',$orders['paymenttime']);
						$orders['paymentamount']=$orders['paymentamount'];
					if($order['psentitle']!=''){
						$orders['paymentstatus']=$orders['pstitle'].'('.$orders['psentitle'].')';
					}else{
						$orders['paymentstatus']=$orders['pstitle'];
					}
					}
					$item .='<tr>
					<td align="center">'.$i.'</td>
					<td style="word-break:break-all" align="center"><a href="/s.php?module=order&action=view&orderid='.$orders['orderid'].'">'.$orders['orderno'].'</td>
					<td align="center">'.$orders['rtitle'].$orders['rentitle'].'</td>
					<td>'.$orders['ctitle'].'</td>
					<td align="center">'.$orders['mname'].'</td>
					<td align="center"><table frame="void" border="0"  width=100% height=100%>'.$productItem['panel'].'</table></td>
					<td align="center">
					'.$fee['panel'].'
					</td>
					<td align="center">'.$orders['paymenttime'].'</td>
					<td align="center">'.$orders['paymentstatus'].'</td>
					<td align="center">'.$subtotal.'</td>
					<td align="center">'.$orders['paymentamount'].'</td>
					
					<td align="center">'.$subtotal.'</td>
					</tr>';
					$amounts+=$productItem['quantity'];
					$prices+=$productItem['total'];
					$paymentamount+=$orders['paymentamount'];
					$fees+=$fee['total'];
					$total+=$subtotals;
					$i++;
				}
				$itemstr='<tr><td align="center" class="red" width="50px"><b>ID</b></td><td align="center" class="red"  width="150px"><b>国家名称</b></td><td align="center" class="red" width="80px"><b>订单数量（国家）</b></td><td align="center" class="red" width="80px"><b>产品数量（国家）</b></td><td align="center" class="red" width="80px"><b>订单金额（国家）</b></td><td align="center" class="red" width="60px"><b>已到账（国家）</b></td><td align="center" class="red" width="60px"><b>未到账（国家）</b></td>';
				$itemstr.='<td align="center" class="red" width="100px"><b>客户名称</b></td><td align="center" class="red" width="80px"><b>订单数量（客户）</b></td><td align="center" class="red" width="80px"><b>产品数量（客户）</b></td><td align="center" class="red" width="80px"><b>订单金额（客户）</b></td><td align="center" class="red" width="60px"><b>已到账(客户)</b></td><td align="center" class="red" width="60px"><b>未到账（客户）</b></td></tr>';

				/*
				$customerorder=$this->kclass->DB->query("SELECT `customer`. title ,`customer`.customerid FROM `customer`
				WHERE `customer` .customerid IN (".implode(',',$customerid).")");
				if($num=$this->kclass->DB->numRows()){
					$i=1;
					$num=$num+1;
					//$itemstr='<tr><td rowspan="'.$num.'" align="center" class="red" width="10%"><b>客户</b></td><td align="center" class="red" ><b>名称</b></td><td align="center" class="red" width="50px"><b>订单数量</b></td><td align="center" class="red"><b>产品数量</b></td><td align="center" class="red"><b>订单金额</b></td><td align="center" class="red"><b>已到账</b></td><td align="center" class="red"><b>未到账</b></td></tr>';
					$customerstr='<tr><td class="red" align="center">客户姓名</td><td class="red" align="center">订单数量</td></tr>';
					while($customerorders=$this->kclass->DB->fetchArray($customerorder)){
						$theorders=$this->kclass->relatedCustomerorder(array('customerid'=>$customerorders['customerid'] ,'condition'=>$condition,'killed'=>$killed));
					//	print_r($theorders);
						$customerstr.='<b>'.$customerorders['title'].'</b>：<b><span class="green">'.$ordernum.'</span></b><br/>';
						$itemstr.='<tr><td  align="center"><b>'.$customerorders['title'].'</b></td><td width="30%" align="center">'.$theorders['ordernum'].'</td><td align="center">'.$theorders['quantitys'].'</td><td align="center">'.$theorders['totals'].'</td><td>'.$theorders['paymentamount'].'</td><td>'.floatval($theorders['totals']-$theorders['paymentamount']).'</td></tr>';
						$i++;
					}
				}
				*/
				//每个国家对应的订单数量订单详细
				$regionid=array_filter(array_unique($regionid));
				$countryid=array_filter(array_unique($countryid));
				$customerid=array_filter(array_unique($customerid));
				//$regionorder=$this->kclass->DB->query("SELECT `region`.regionid,`region`.country,`region`.encountry FROM `region` WHERE `region`.regionid IN (".implode(',',$regionid).")");
				$regionorder=$this->kclass->DB->query("
							SELECT `region`.country,`region`.regionid,`region`.countryid FROM `region` WHERE `region`.regionid IN (".implode(',', $countryid).") AND `region`.countryid=0
			");
				if($num=$this->kclass->DB->numRows()){
					$i=1;
					//$itemstr.='<tr><td rowspan="'.$num.'" align="center" width="100px" class="red"><b>国家</b></td></tr>';
					//$regionstr='<tr><td class="red" align="center">国家</td><td class="red" align="center">订单数量</td></tr>';
					while($regionorders=$this->kclass->DB->fetchArray($regionorder)){
						$findregion=$this->kclass->getRegion(array('countryid'=>$regionorders['regionid'],'regionid'=>$regionid,'condition'=>$condition));//取得该询价单下各个国家的regionid
						$theorders=$this->kclass->relatedOrderregion(array('regionid'=>$findregion ,'condition'=>$condition));
						$customeritem=$this->kclass->relatedOrdercustomer(array('regionid'=>$findregion,'customerid'=>$customerid,'condition'=>$condition));//取得每个国家对应客户的询价单
						$customeritem['num']=$customeritem['num']+1;
                        $itemstr.='<tr><td  align="center" rowspan='.$customeritem['num'].'><b>'.$i.'</b></td><td  align="center" rowspan='.$customeritem['num'].'><b>'.$regionorders['country'].'</b></td><td  align="center"  rowspan='.$customeritem['num'].'>'.$theorders['ordernum'].'</td><td align="center"  rowspan='.$customeritem['num'].'>'.$theorders['quantitys'].'</td><td align="center"  rowspan='.$customeritem['num'].'>'.$theorders['totals'].'</td><td  align="center" rowspan='.$customeritem['num'].'>'.$theorders['paymentamount'].'</td><td align="center"  rowspan='.$customeritem['num'].'>'.floatval($theorders['totals']-$theorders['paymentamount']).'</td>'.$customeritem['panel'].'</tr>';
						$i++;
					}
				}	
				//每个业务员对应的订单数量，订单详细
				$director=array_unique($director);
				$directororder=$this->kclass->DB->query("SELECT `member`.userid ,username FROM `member` WHERE `member`.userid IN(".implode(',',$director).") ");
				if($num=$this->kclass->DB->numRows()){
					$itemstrs.='<tr><td align="center" class="red"><b>业务员名称</b></td><td align="center" class="red"><b>订单数量</b></td><td align="center" class="red"><b>产品数量</b></td><td align="center" class="red"><b>订单金额</b></td><td align="center" class="red"><b>已到账</b></td><td align="center" class="red"><b>未到账</b></td></tr>';
					//$itemstr.='<tr><td rowspan="'.$num.'" align="center" width="100px" class="red"><b>业务员</b></td></tr>';
					while($directororders=$this->kclass->DB->fetchArray($directororder)){	
						$theorders=$this->kclass->relatedDirectororder(array('userid'=>$directororders['userid'] ,'condition'=>$condition));
						$itemstrs.='<tr><td  align="center"><b>'.$directororders['username'].'</b></td><td  align="center">'.$theorders['ordernum'].'</td><td align="center">'.$theorders['quantitys'].'</td><td align="center">'.$theorders['totals'].'</td><td align="center">'.$theorders['paymentamount'].'</td><td align="center">'.floatval($theorders['totals']-$theorders['paymentamount']).'</td></tr>';
					}
				}
				$itemstrs.='<tr><td colspan="12" align="right"><span class="red">产品总数量：'.$amounts.'</span><br/><span class="red">订单总金额：'.$prices.'</span><br/><span class="red">已支付总金额：'.$paymentamount.'</span><br/><span class="red">未支付总金额：'.floatval($prices-$paymentamount).'</span></td></tr>';
				$item.='<tr><td  align="right">合计</td><td align="center">'.$ordernumers.'</td><td colspan="7"><table frame="below" border="1"  width=100% height=100%>'.$itemstr.'</table><table frame="above" border="1"  width=100% height=100%>'.$itemstrs.'</td></table></td><td class="red">额外费用总金额：'.$fees.'</td><td class="red">已支付总金额：'.$paymentamount.'</td><td  align="right" class="red">总金额：'.$total.'</td></tr>';
				$item.='<tr><td colspan="12"> 订单总计：共有订单数：<b>'.$ordernumers.'</b>,　 产品总数量：<b>'.$amounts.'</b>,　订单总金额：<b>'.$prices.'</b>,　额外费用总金额：<b>'.$fees.'</b>,　总金额：<b>'.$total.'</b></td></tr>';
          // $item.='<tr><td colspan="12"><b>订单统计</b></td></tr><tr><td>国家</td><td></td><td>客户姓名</td><td>订单总量</td><td>产品总数</td><td>订单总金额</td><td>业务员</td></tr>';function orders
			
			}else{
				$item='<tr ><td colspan="12"><span class="red" >该条件下不存在对应的订单</span></td><tr>';
			}
			
		}elseif(isset($this->kclass->input['product'])){
			if($this->kclass->input['productid']<0){
				$e='<li>请从正确的页面进行提交，如果问题持续存在，请联系网站管理员。</li>';
		 	}
			if($this->kcalss->input['show']=='default'){
				$killed="AND killed =0";
			}elseif($this->kclass->input['show']=='all'){
				$killed="AND 1=1";
			}
			if($this->kclass->input['orderby']=='orderid'){
				$orderby=" ORDER BY `order`.orderid";
			}elseif($this->kclass->input['orderby']=='country'){
				$orderby=" ORDER BY rtitle";
			}elseif($this->kclass->input['orderby']=='customer'){
				$orderby=" ORDER BY ctitle";
			}elseif($this->kclass->input['orderby']=='dateline'){
          		$orderby=" ORDER BY `order`.shipmentDate";
			}
			if($this->kclass->input['direction']=='ASC'){
				$direction=" ASC ";
			}elseif ($this->kclass->input['direction']=='DESC'){
				$direction=" DESC ";
			}
			if($this->kclass->input['startdate']){
				$year=substr($this->kclass->input['startdate'],0,4);
				$month=substr($this->kclass->input['startdate'],5,2);
				$day=substr($this->kclass->input['startdate'],8,2);
				if(checkdate($month,$day,$year)){
					$this->kclass->input['startdate']=mktime(0,0,0,$month,$day,$year);
				}
				if($this->kclass->input['startdate']>0){
					$condition.=" AND `order`.shipmentDate>='".$this->kclass->input['startdate']."' ";
				}
			}
			if($this->kclass->input['enddate']){
				$year=substr($this->kclass->input['enddate'],0,4);
				$month=substr($this->kclass->input['enddate'],5,2);
				$day=substr($this->kclass->input['enddate'],8,2);
				if(checkdate($month,$day,$year)){
					$this->kclass->input['enddate']=mktime(0,0,0,$month,$day,$year);
				}
				if($this->kclass->input['enddate']>0){
					$condition.=" AND `order`.shipmentDate<='".$this->kclass->input['enddate']."' ";
				}
			}
			if($this->kclass->input['productid']){
				$condition.=" AND `item`.productid={$this->kclass->input['productid']}";
			}
			// $order=$this->kclass->DB->query("
	  //       SELECT `order`.ordertitle,`order`.orderid,`order`.customerid,`order`.shipmentDate,`order`.orderno,`order`.director,`order`.customerid,`order`.ifChooser,`order`.ifInbound,`order`.ifOutbound,`order`.created,`order`.modified,`order`.ifVerify,`order`.verifier,`order`.verified,`order`.ifApprove,`order`.approver,`order`.approved,`customer`.title AS ctitle,`customer`.cntitle AS ccntitle,`customer`.regionid,`region`.country AS rcountry,`region`.city AS rcity ,`region`.encity AS rencity,`member`.realname AS mname,b.realname AS bname,p.realname AS pname,r.realname AS rname,q.realname AS qname,`item`.quantity,`item`.amount
			// FROM `item`
			// LEFT JOIN `order`			ON (`order`.orderid=`item`.mid)
	  //       LEFT JOIN `customer`	ON (`customer`.customerid=`order`.customerid) 
			// LEFT JOIN `region`		ON(`region`.regionid=`customer`.regionid)
			// LEFT JOIN `member`	ON(`member`.memberid=`order`.creator)
			// LEFT JOIN `member` AS b ON(b.memberid=`order`.director)
			// LEFT JOIN `member` AS p ON(p.memberid=`order`.tracker)
			// LEFT JOIN `member` AS r ON(r.memberid=`order`.verifier)
			// LEFT JOIN `member` AS q  ON(q.memberid=`order`.approver)  
			// WHERE`order`.type='PI' ".$killed." AND `item`.module='order' AND `order`.shipmentDate>='".$startdate."' AND `order`.shipmentDate<='".$enddate."' AND`item`. productid='".$this->kclass->input['productid']."' ORDER BY ".$orderby. " ".$this->kclass->input['direction']."
			// ");


// WHERE`order`.type='PI' ".$killed." AND `item`.module='order' AND `order`.shipmentDate>='".$startdate."' AND `order`.shipmentDate<='".$enddate."' AND`item`. productid='".$this->kclass->input['productid']."' ORDER BY ".$orderby. " ".$this->kclass->input['direction']."

			$order=$this->kclass->DB->query(" SELECT `order`.* ,`customer`.customerid ,`customer`.title AS ctitle,`customer`.cntitle AS ccntitle,`region`.regionid ,`region`.country AS rtitle,`region`.countryid,`region`.encountry AS rentitle,`supplier`.title AS stitle ,m.realname AS mname,d.title AS dtitle,p.entitle AS pentitle,p.title AS ptitle,s.title AS sstitle ,ps.title AS pstitle ,ps.entitle AS psentitle
										FROM `order`
										LEFT JOIN `customer` ON(`customer`.customerid=`order`.customerid)
										LEFT JOIN `region`		ON(`region`.regionid=`customer`.regionid)
										LEFT JOIN `supplier`	ON(`supplier`.supplierid=`order`.supplierid)
										LEFT JOIN `member` AS m ON(m.userid=`order`.director)
										LEFT JOIN `paymentstatus` AS ps ON (ps.paymentstatusid=`order`.paymentstatus)
										LEFT JOIN `deliveryterm`  AS d ON(d.deliverytermid=`order`.deliverytermid)
										LEFT JOIN `paymentterm` AS p ON(p.paymenttermid=`order`.paymenttermid)
										LEFT JOIN `shipmethod`    AS s ON (s.shipmethodid=`order`.shipmethodid)
										LEFT JOIN `item`	ON(`item`.mid=`order`.orderid)
										WHERE `order`.type='PI' ".$killed." AND `item`.module='order' ".$condition." ".$orderby." ".$direction."
										");
			if($ordernumers=$this->kclass->DB->numRows()){
				$i=1;
				$customerid=$regionid=$countryid=$director=array();
				$orderidArr=array();
				while($orders=$this->kclass->DB->fetchArray($order)){
					if(in_array($orders['ordertitle'], $orderidArr)){
							$ordernumers--;
							continue;
					}else{
						$orderidArr[]=$orders['ordertitle'];
					}
					$customerid[]=$orders['customerid'];
					$director[]=$orders['director'];
					if($orders['countryid']==0){
						$countryid[]=$orders['regionid'];
						$regionid[]=$orders['regionid'];
					}else{
						$countryid[]=$orders['countryid'];
						$regionid[]=$orders['regionid'];
					}
					$fee=$this->kclass->relatedFees(array('orderid'=>$orders['orderid']));
					$productItem=$this->kclass->relatedProducts(array('module'=>'order','moduleid'=>$orders['orderid']));
					$subtotals=floatval($fee['total'])+floatval($productItem['total']);
					$subtotal=number_format($subtotals,2);
					if($orders['rentitle']!=''){
						$orders['rentitle']='('.$orders['rentitle'].')';
					}
					if($orders['paymenttime']==0 & $orders['paymentamount']==0 & $orders['paymentstatus']==0){
						$orders['paymenttime']='未支付';
						$orders['paymentamount']='未支付';
						$orders['paymentstatus']='未支付';
					}else{
						$orders['paymenttime']=date('Y-m-d',$orders['paymenttime']);
						$orders['paymentamount']=$orders['paymentamount'];
					if($order['psentitle']!=''){
						$orders['paymentstatus']=$orders['pstitle'].'('.$orders['psentitle'].')';
					}else{
						$orders['paymentstatus']=$orders['pstitle'];
					}
					}
					$item .='<tr>
					<td align="center">'.$i.'</td>
					<td style="word-break:break-all" align="center"><a href="/s.php?module=order&action=view&orderid='.$orders['orderid'].'">'.$orders['orderno'].'</td>
					<td align="center">'.$orders['rtitle'].$orders['rentitle'].'</td>
					<td>'.$orders['ctitle'].'</td>
					<td align="center">'.$orders['mname'].'</td>
					<td align="center"><table frame="void" border="0"  width=100% height=100%>'.$productItem['panel'].'</table></td>
					<td align="center">
					'.$fee['panel'].'
					</td>
					<td align="center">'.$orders['paymenttime'].'</td>
					<td align="center">'.$orders['paymentstatus'].'</td>
					<td align="center">'.$subtotal.'</td>
					<td align="center">'.$orders['paymentamount'].'</td>
					
					<td align="center">'.$subtotal.'</td>
					</tr>';
					$amounts+=$productItem['quantity'];
					$prices+=$productItem['total'];
					$paymentamount+=$orders['paymentamount'];
					$fees+=$fee['total'];
					$total+=$subtotals;
					$i++;
				}
				$itemstr='<tr><td align="center" class="red" width="50px"><b>ID</b></td><td align="center" class="red"  width="150px"><b>国家名称</b></td><td align="center" class="red" width="80px"><b>订单数量（国家）</b></td><td align="center" class="red" width="80px"><b>产品数量（国家）</b></td><td align="center" class="red" width="80px"><b>订单金额（国家）</b></td><td align="center" class="red" width="60px"><b>已到账（国家）</b></td><td align="center" class="red" width="60px"><b>未到账（国家）</b></td>';
				$itemstr.='<td align="center" class="red" width="100px"><b>客户名称</b></td><td align="center" class="red" width="80px"><b>订单数量（客户）</b></td><td align="center" class="red" width="80px"><b>产品数量（客户）</b></td><td align="center" class="red" width="80px"><b>订单金额（客户）</b></td><td align="center" class="red" width="60px"><b>已到账(客户)</b></td><td align="center" class="red" width="60px"><b>未到账（客户）</b></td></tr>';

				/*
				$customerorder=$this->kclass->DB->query("SELECT `customer`. title ,`customer`.customerid FROM `customer`
				WHERE `customer` .customerid IN (".implode(',',$customerid).")");
				if($num=$this->kclass->DB->numRows()){
					$i=1;
					$num=$num+1;
					//$itemstr='<tr><td rowspan="'.$num.'" align="center" class="red" width="10%"><b>客户</b></td><td align="center" class="red" ><b>名称</b></td><td align="center" class="red" width="50px"><b>订单数量</b></td><td align="center" class="red"><b>产品数量</b></td><td align="center" class="red"><b>订单金额</b></td><td align="center" class="red"><b>已到账</b></td><td align="center" class="red"><b>未到账</b></td></tr>';
					$customerstr='<tr><td class="red" align="center">客户姓名</td><td class="red" align="center">订单数量</td></tr>';
					while($customerorders=$this->kclass->DB->fetchArray($customerorder)){
						$theorders=$this->kclass->relatedCustomerorder(array('customerid'=>$customerorders['customerid'] ,'condition'=>$condition,'killed'=>$killed));
					//	print_r($theorders);
						$customerstr.='<b>'.$customerorders['title'].'</b>：<b><span class="green">'.$ordernum.'</span></b><br/>';
						$itemstr.='<tr><td  align="center"><b>'.$customerorders['title'].'</b></td><td width="30%" align="center">'.$theorders['ordernum'].'</td><td align="center">'.$theorders['quantitys'].'</td><td align="center">'.$theorders['totals'].'</td><td>'.$theorders['paymentamount'].'</td><td>'.floatval($theorders['totals']-$theorders['paymentamount']).'</td></tr>';
						$i++;
					}
				}
				*/
				//每个国家对应的订单数量订单详细
				$regionid=array_filter(array_unique($regionid));
				$countryid=array_filter(array_unique($countryid));
				$customerid=array_filter(array_unique($customerid));
				//$regionorder=$this->kclass->DB->query("SELECT `region`.regionid,`region`.country,`region`.encountry FROM `region` WHERE `region`.regionid IN (".implode(',',$regionid).")");
				// AND `item`.productid=
				if($this->kclass->input['productid']){
					$condition = preg_replace('/ AND `item`.productid=(\d+)/i', " ", $condition);
					$res=$this->kclass->DB->query("
							SELECT mid from `item` where module='order' and productid=".$this->kclass->input['productid']."
					");
					if($this->kclass->DB->numRows()){
						while($mid=$this->kclass->DB->fetchArray($res)){
							$mids[]=$mid['mid'];
						}
						$condition.=" AND `order`.orderid IN (".implode(",", $mids).") ";
					}
				}
				
				$regionorder=$this->kclass->DB->query("
							SELECT `region`.country,`region`.regionid,`region`.countryid FROM `region` WHERE `region`.regionid IN (".implode(',', $countryid).") AND `region`.countryid=0
			");
				if($num=$this->kclass->DB->numRows()){
					$i=1;
					//$itemstr.='<tr><td rowspan="'.$num.'" align="center" width="100px" class="red"><b>国家</b></td></tr>';
					//$regionstr='<tr><td class="red" align="center">国家</td><td class="red" align="center">订单数量</td></tr>';
					while($regionorders=$this->kclass->DB->fetchArray($regionorder)){
						$findregion=$this->kclass->getRegion(array('countryid'=>$regionorders['regionid'],'regionid'=>$regionid,'condition'=>$condition));//取得该询价单下各个国家的regionid
						$theorders=$this->kclass->relatedOrderregion(array('regionid'=>$findregion ,'condition'=>$condition));
						$customeritem=$this->kclass->relatedOrdercustomer(array('regionid'=>$findregion,'customerid'=>$customerid,'condition'=>$condition));//取得每个国家对应客户的询价单
						$customeritem['num']=$customeritem['num']+1;
                        $itemstr.='<tr><td  align="center" rowspan='.$customeritem['num'].'><b>'.$i.'</b></td><td  align="center" rowspan='.$customeritem['num'].'><b>'.$regionorders['country'].'</b></td><td  align="center"  rowspan='.$customeritem['num'].'>'.$theorders['ordernum'].'</td><td align="center"  rowspan='.$customeritem['num'].'>'.$theorders['quantitys'].'</td><td align="center"  rowspan='.$customeritem['num'].'>'.$theorders['totals'].'</td><td  align="center" rowspan='.$customeritem['num'].'>'.$theorders['paymentamount'].'</td><td align="center"  rowspan='.$customeritem['num'].'>'.floatval($theorders['totals']-$theorders['paymentamount']).'</td>'.$customeritem['panel'].'</tr>';
						$i++;
					}
				}	
				//每个业务员对应的订单数量，订单详细
				$director=array_unique($director);
				$directororder=$this->kclass->DB->query("SELECT `member`.userid ,username FROM `member` WHERE `member`.userid IN(".implode(',',$director).") ");
				if($num=$this->kclass->DB->numRows()){
					$itemstrs.='<tr><td align="center" class="red"><b>业务员名称</b></td><td align="center" class="red"><b>订单数量</b></td><td align="center" class="red"><b>产品数量</b></td><td align="center" class="red"><b>订单金额</b></td><td align="center" class="red"><b>已到账</b></td><td align="center" class="red"><b>未到账</b></td></tr>';
					//$itemstr.='<tr><td rowspan="'.$num.'" align="center" width="100px" class="red"><b>业务员</b></td></tr>';
					while($directororders=$this->kclass->DB->fetchArray($directororder)){	
						$theorders=$this->kclass->relatedDirectororder(array('userid'=>$directororders['userid'] ,'condition'=>$condition));
						$itemstrs.='<tr><td  align="center"><b>'.$directororders['username'].'</b></td><td  align="center">'.$theorders['ordernum'].'</td><td align="center">'.$theorders['quantitys'].'</td><td align="center">'.$theorders['totals'].'</td><td align="center">'.$theorders['paymentamount'].'</td><td align="center">'.floatval($theorders['totals']-$theorders['paymentamount']).'</td></tr>';
					}
				}
				$itemstrs.='<tr><td colspan="12" align="right"><span class="red">产品总数量：'.$amounts.'</span><br/><span class="red">订单总金额：'.$prices.'</span><br/><span class="red">已支付总金额：'.$paymentamount.'</span><br/><span class="red">未支付总金额：'.floatval($prices-$paymentamount).'</span></td></tr>';
				$item.='<tr><td  align="right">合计</td><td align="center">'.$ordernumers.'</td><td colspan="7"><table frame="below" border="1"  width=100% height=100%>'.$itemstr.'</table><table frame="above" border="1"  width=100% height=100%>'.$itemstrs.'</td></table></td><td class="red">额外费用总金额：'.$fees.'</td><td class="red">已支付总金额：'.$paymentamount.'</td><td  align="right" class="red">总金额：'.$total.'</td></tr>';
				$item.='<tr><td colspan="12"> 订单总计：共有订单数：<b>'.$ordernumers.'</b>,　 产品总数量：<b>'.$amounts.'</b>,　订单总金额：<b>'.$prices.'</b>,　额外费用总金额：<b>'.$fees.'</b>,　总金额：<b>'.$total.'</b></td></tr>';
			}else{
				$item='<tr ><td colspan="12"><span class="red" >该条件下不存在对应的产品</span></td><tr>';
			}
		}else{
			$e='<li>查询的结果不存在。</li>';
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title'=>'统计订单',
				'text'=>'您在统计订单的过程中有一下错误：<ul>'.$e.'</ul>',
				'url'=>'javascript:history.back()',
				'sec'=>3
				));
		}
		$body=<<<EOF
 <table class="hundred">
 <tr>
 <td>序号</td><td width="100px" align="center">订单号</td><td width="100px" align="center">国家</td><td width="200px" align="center">客户</td><td width="200px" align="center">业务员</td width="600px"><td align="center">产品详细</td><td width="200px" align="center">额外费用明细</td><td width="90px" align="center">到款时间</td><td width="90px" align="center">付款情况</td><td  width="90px" align="center">发票金额</td><td width="90px" align="center">到款金额</td><td width="90px" align="center">总价小计</td>
 <tr>
		{$item}
 </table>
 <span class="small gray">注意：①该订单统计的产品数量仅是在该条件下灯的总数<br/> 　&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;②在每条订单下面的总价是指所有产品的总价，包括：灯类、充电类、配件类<br/> 　&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;③在合计列下产品数量是指灯类的数量，订单金额包括：灯类、充电类、配件类<br/> 　&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;④该订单统计的总计统计是指在该条件下所有产品的总金额，包括：灯类，充电类，配件类<br/> 　&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;⑤合计中只统计由国家有客户信息的订单，对于无国家无客户信息的订单暂时无法统计</span>
EOF;
		
		$this->kclass->page['title'].='-订单统计';
	 $this->kclass->page['onload'].='dc.tabhover();$(\'#shipmentDate\').datepicker()';
	 //$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.'-订单统计','right'=>'<a href="/s.php?module=order">返回列表</a>','body'=>$body));
	 $t = date('F j, Y, H:i:s', TIMENOW);
		$footer=<<<EOD
			<div class="right">Page Created On: {$t}</div>
		New Wisdom Investment Limited
EOD;
		$this->kclass->page['pageTitle']='订单 - '.$invoice['invoiceno'];
		$this->kclass->page['title'] = '订单统计';
		$this->kclass->page['contact']='公司地址：深圳市龙岗区宝荷路嶂背大道9号1楼<br>
		Tel: 86-755-83911841  Fax: 86-755-83906115 ';
		$this->kclass->page['caption'] = '<span class="middle bold"> '.$product['title'].'</span>'.$this->kclass->iif($invoice['customerNo']!='', ' (Cust. Order No.: '.$invoice['customerNo'].')', '');
		$this->kclass->page['main']=$body;
		$this->kclass->page['footer']=$footer;
	}
	//
	function nodostatistics(){
	 if(isset($this->kclass->input['port'])){
		if($this->kclass->input['portid']<0){
			$e='<li>请从正确的页面进行提交，如果问题持续存在，请联系网站管理员。</li>';
		}
		if($this->kcalss->input['show']=='default'){
			$killed="AND killed !=0";
		}elseif($this->kclass->input['show']=='all'){
			$killed="AND 1=1";
		}
		if($this->kclass->input['orderby']=='orderid'){
			$orderby="`order`.orderid";
		}elseif($this->kclass->input['orderby']=='customer'){
			$orderby="ctitle";
		}elseif($this->kclass->input['orderby']=='dateline'){
           $orderby="`order`.shipmentDate";
		}
      $sd=explode('-',$this->kclass->input['startdate']);
		 $startdate=mktime(0,0,0,$sd[1],$sd[2],$sd[0]);
		$ed=explode('-',$this->kclass->input['enddate']);
		$enddate=mktime(0,0,0,$ed[1],$ed[2],$ed[0]);
		//$country=$this->kclass->DB->queryFirst("SELECT `region`.countryid FROM `region` WHERE `region`.regionid='".$this->kclass->input['portid']."'");
		$country=$this->kclass->DB->queryFirst("SELECT country FROM region WHERE countryid='".$this->kclass->input['countryid']."' ");
		  $title="国家或地区：".$country['country']."";
      $order=$this->kclass->DB->query("
		SELECT `order`.ordertitle,`order`.orderid,`order`.shipmentDate,`order`.orderno,`order`.director,`order`.customerid,`order`.ifChooser,`order`.ifInbound,`order`.ifOutbound,`order`.ifVerify,`order`.verifier,`order`.verified,`order`.ifApprove,`order`.approver,`order`.approved,c.title AS ctitle,c.cntitle AS ccntitle,c.regionid,r.country AS rcountry,r.city AS rcity ,r.encity AS rencity,`member`.realname AS mname,b.realname AS bname,p.realname AS pname,n.realname AS nname,q.realname AS qname,
					i.*
		FROM `order`
		LEFT JOIN	`customer` AS	c ON(c.customerid=`order`.customerid)
		LEFT JOIN	`region`	  AS	 r ON(r.regionid=c.regionid)
		LEFT JOIN	`item`         AS  i ON(i.mid=`order`.orderid)
		LEFT JOIN `member`	ON(`member`.memberid=`order`.creator)
		LEFT JOIN `member` AS b ON(b.memberid=`order`.director)
		LEFT JOIN `member` AS p ON(p.memberid=`order`.tracker)
		LEFT JOIN `member` AS n ON(n.memberid=`order`.verifier)
		LEFT JOIN `member` AS q  ON(q.memberid=`order`.approver)  
		WHERE`order`.type='PI' AND `order`.shipmentDate>='".$startdate."' AND `order`.shipmentDate<='".$enddate."' AND r. countryid='".$this->kclass->input['countryid']."'  AND `i`.module='order' ORDER BY ".$orderby. " ".$this->kclass->input['direction']."
	  ");
	  if($num=$this->kclass->DB->numRows()){
		   $i=1;
		  while($orders=$this->kclass->DB->fetchArray($order)){
			 
			 $shipment=date('Y-m-d',$orders['shipmentDate']); 
			 if($orders['cntitle']!=''){
					$customer=$this->kclass->iif(strlen($orders['ctitle'])>30,mb_substr($orders['ctitle'],0,30,utf-8).'...').'('.$orders['ccntitle'].')';
				}else{
					$customer=$this->kclass->iif(strlen($orders['ctitle'])>30,mb_substr($orders['ctitle'],0,30,'UTF-8').'...',$orders['ctitle']);
				}
				$status='';
				if($orders['ifVerify']==0){
					$status = '<span class="darkred">未审核　未审批</span>';
				}elseif($orders['ifVerify']==-1){
					$status = '<span class="red">审核未通过</span>　<span class="darkred">未审批</span>';
				}elseif($orders['ifVerify']==1 AND $orders['ifApprove']==0){
					$status = '<span class="green">审核通过</span>　<span class="darkred">未审批</span>　';
				}elseif($orders['ifVerify']==1 AND $orders['ifApprove']==-1){
					$status = '<span class="green">审核通过</span>　<span class="red">审批未通过</span>';
				}elseif($orders['ifVerify']==1 AND $orders['ifApprove']==1){
					$status = '<span class="green">审核通过　审批通过</span>';
				}
				
			 $item.='<tr>
				<td>'.$i.'</td>
				<td>'.$orders['orderno'].'</td>
				<td>'.$orders['rcountry'].'</td>
				<td>'.$customer.'</td>
				<td>'.$shipment.'</td>
				<td>'.$orders['quantity'].'</td>
				<td>'.$orders['amount'].'</td>
				<td>'.$status.'</td>
				<td>'.$orders['nname'].'</td>
				<td>'.$orders['qname'].'</td>
				</tr>';
				$i++;
		  }
		  
	  }
       
	 }elseif(isset($this->kclass->input['product'])){
		 if($this->kclass->input['productid']<0){
			 $e='<li>请从正确的页面进行提交，如果问题持续存在，请联系网站管理员。</li>';
		 }
		if($this->kcalss->input['show']=='default'){
			$killed="AND killed !=0";
		}elseif($this->kclass->input['show']=='all'){
			$killed="AND 1=1";
		}
		if($this->kclass->input['orderby']=='orderid'){
			$orderby="`order`.orderid";
		}elseif($this->kclass->input['orderby']=='country'){
			$orderby="rcountry";
		}elseif($this->kclass->input['orderby']=='customer'){
			$orderby="ctitle";
		}elseif($this->kclass->input['orderby']=='dateline'){
           $orderby="`order`.shipmentDate";
		}
		 $sd=explode('-',$this->kclass->input['startdate']);
		 $startdate=mktime(0,0,0,$sd[1],$sd[2],$sd[0]);
		$ed=explode('-',$this->kclass->input['enddate']);
		$enddate=mktime(0,0,0,$ed[1],$ed[2],$ed[0]);
		$order=$this->kclass->DB->query("
        SELECT `order`.ordertitle,`order`.orderid,`order`.customerid,`order`.shipmentDate,`order`.orderno,`order`.director,`order`.customerid,`order`.ifChooser,`order`.ifInbound,`order`.ifOutbound,`order`.created,`order`.modified,`order`.ifVerify,`order`.verifier,`order`.verified,`order`.ifApprove,`order`.approver,`order`.approved,`customer`.title AS ctitle,`customer`.cntitle AS ccntitle,`customer`.regionid,`region`.country AS rcountry,`region`.city AS rcity ,`region`.encity AS rencity,`member`.realname AS mname,b.realname AS bname,p.realname AS pname,r.realname AS rname,q.realname AS qname,`item`.quantity,`item`.amount
		FROM `item`
		LEFT JOIN `order`			ON (`order`.orderid=`item`.mid)
        LEFT JOIN `customer`	ON (`customer`.customerid=`order`.customerid) 
		LEFT JOIN `region`		ON(`region`.regionid=`customer`.regionid)
		LEFT JOIN `member`	ON(`member`.memberid=`order`.creator)
		LEFT JOIN `member` AS b ON(b.memberid=`order`.director)
		LEFT JOIN `member` AS p ON(p.memberid=`order`.tracker)
		LEFT JOIN `member` AS r ON(r.memberid=`order`.verifier)
		LEFT JOIN `member` AS q  ON(q.memberid=`order`.approver)  
		WHERE`order`.type='PI' ".$killed." AND `item`.module='order' AND `order`.shipmentDate>='".$startdate."' AND `order`.shipmentDate<='".$enddate."' AND`item`. productid='".$this->kclass->input['productid']."' ORDER BY ".$orderby. " ".$this->kclass->input['direction']."
		");
		if($num=$this->kclass->DB->numRows()){
				$i=1;
				 $item='';
				$num=array(); 
				$product=$this->kclass->DB->queryFirst("SELECT title FROM product WHERE productid='".$this->kclass->input['productid']."'");
				$title="产品名称：".$product['title'];
			while($orders=$this->kclass->DB->fetchArray($order)){
				$shipment=date('Y-m-d',$orders['shipmentDate']); 
				if($orders['cntitle']!=''){
					$customer=$this->kclass->iif(strlen($orders['ctitle'])>30,mb_substr($orders['ctitle'],0,30,utf-8).'...').'('.$orders['ccntitle'].')';
				}else{
					$customer=$this->kclass->iif(strlen($orders['ctitle'])>30,mb_substr($orders['ctitle'],0,30,'UTF-8').'...',$orders['ctitle']);
				}
				$status='';
				if($orders['ifVerify']==0){
					$status = '<span class="darkred">未审核　未审批</span>';
				}elseif($orders['ifVerify']==-1){
					$status = '<span class="red">审核未通过</span>　<span class="darkred">未审批</span>';
				}elseif($orders['ifVerify']==1 AND $orders['ifApprove']==0){
					$status = '<span class="green">审核通过</span>　<span class="darkred">未审批</span>　';
				}elseif($orders['ifVerify']==1 AND $orders['ifApprove']==-1){
					$status = '<span class="green">审核通过</span>　<span class="red">审批未通过</span>';
				}elseif($orders['ifVerify']==1 AND $orders['ifApprove']==1){
					$status = '<span class="green">审核通过　审批通过</span>';
				}
              
				$item.='<tr>
				<td>'.$i.'</td>
				<td>'.$orders['orderno'].'</td>
				<td>'.$orders['rcountry'].'</td>
				<td>'.$customer.'</td>
				<td>'.$shipment.'</td>
				<td>'.$orders['quantity'].'</td>
				<td>'.$orders['amount'].'</td>
				<td>'.$status.'</td>
				<td>'.$orders['rname'].'</td>
				<td>'.$orders['qname'].'</td>
				</tr>';
				$i++;
				
			}	
		}
	 }else{
		$e='<li>查询的结果不存在。</li>';
	 }
	 if(isset($e)){
		 $this->kclass->messager(array(
			 'title'=>'删除订单',
			 'text'=>'您在删除订单的过程中有一下错误：<ul>'.$e.'</ul>',
			 'url'=>'javascript:history.back()',
			 'sec'=>3
			 ));
	 }
	  
			  	$body=<<<EOF
	<table class="hundred">
	<tbody>
<tr>
	<td colspan="12" class="bold"><span class="right">{$title}</span>统计详情</td>
</tr>
	<tr class="odd blod" ><td>编号</td><td>订单号</td><td>国家或地区</td><td>客户姓名</td><td>交货时间</td><td>产品数量</td><td>总价</td><td>订单状态</td><td>审核人</td><td>审批人</td>
	</tr>
		{$item}
		</tbody>
		</table>
		
EOF;
	
	 $this->kclass->page['title'].='-订单统计';
	 $this->kclass->page['onload'].='dc.tabhover();$(\'#shipmentDate\').datepicker()';
	 //$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.'-订单统计','right'=>'<a href="/s.php?module=order">返回列表</a>','body'=>$body));
	 $t = date('F j, Y, H:i:s', TIMENOW);
		$footer=<<<EOD
			<div class="right">Page Created On: {$t}</div>
		New Wisdom Investment Limited
EOD;
		$this->kclass->page['pageTitle']='订单 - '.$invoice['invoiceno'];
		$this->kclass->page['title'] = '订单统计';
		$this->kclass->page['contact']='Address: 4-306, Xianglixincuen, Hongli West Road, Shenzhen, China<br>Tel: 86-755-83911841  Fax: 86-755-83906115  E-Mail: info@wisdom.hk';
		$this->kclass->page['caption'] = '<span class="middle bold">产品名称.: '.$product['title'].'</span>'.$this->kclass->iif($invoice['customerNo']!='', ' (Cust. Order No.: '.$invoice['customerNo'].')', '');
		$this->kclass->page['main']=$body;
		$this->kclass->page['footer']=$footer;
	}
	//
	function stquotingprint(){	
   $item=$this->kclass->DB->queryFirst("
					SELECT  p.entitle AS pentitle,p.title AS ptitle, c.title AS ctitle,qt.title AS qttitle,u.username AS uname,st.cntitle AS stcntitle,st.entitle AS stentitle,st.created AS stcreated,de.title AS  detitle,de.entitle AS deentitle,st.standrad,st.material,st.technics,st.remark,st.ifverify,st.verifier,st.verified,st.modifier,st.modified,st.creator,st.created,m.realname AS mname,b.realname	AS bname
					FROM `stquoting`				AS	st
					LEFT JOIN `port`					AS	p		ON ( p.portid=st.portid)
					LEFT JOIN `currency`			AS	c		ON (c.currencyid=st.currencyid)
					LEFT JOIN `quotingtype`	AS	qt		ON (qt.typeid=st.typeid)
					LEFT JOIN `user`					AS	u		ON  (u.userid=st.creator)
					LEFT JOIN `deliveryterm`	AS	de	ON (de.deliverytermid=st.deliverytermid)
					LEFT JOIN `member`			AS	m		ON	(m.userid=st.verifier)
					LEFT JOIN `member`			AS    b		ON	(b.userid=st.creator)
					WHERE st.quotingid='".$this->kclass->input['STQuotingid']."'
				");
	if($item['ifverify']==0){
			$ifverify='<span class="darkred">未审核';
			$status=2;
		}elseif($item['ifverify']==1){
			$ifverify='<span class="green">已审核';
			$status=3;
		}elseif($item['ifverify']==-1){
			$ifverify='<span class="red">审核未通过';
			$status=2;
		}
		if($item['verified']!=0){
			$verifier=$item['mname'].'于'.date('Y-m-d H:i:s',$item['verified']).'审核';
		}else{
			$verifier=" ";
		}
		if($item['modified']!=0){
			$modifier=$item['modifier'].'于'.date('Y-m-s H:i:s',$item['modified']).'最后修改';
		}
		$flow=$this->kclass->listFlow(array('module'=>'STQuoting','statusid'=>$status));
	  $title=$item['stentitle'].'('.$item['stcntitle'].')';
	  $date=date('Y-m-d H:i:s',$item['stcreated']);
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
		WHERE `item`	.killed=0 AND `item`.module='stquoting' AND
		`item`.mid='".$this->kclass->input['STQuotingid']."'
		ORDER BY itemid ASC
	 ");
	 $itemtr='';
	 $total=$quantity=0;
	 if($itemnum=$this->kclass->DB->numRows()){
		 while($itemstr=$this->kclass->DB->fetchArray($items)){
			 if($itemstr['quantity']>0 AND $itemstr['price']>0){
				 $amount=$itemstr['quantity']*$itemstr['price'];
				 $total+=$amount;
			 }
			 //print_r($itemstr);
			 $product=$this->kclass->getProduct(array('productid'=>$itemstr['productid'],'itemid'=>$itemstr['productitemid']));
			 $itemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').'">
			                     <td>'.$product['title'].'</td>
								 <td>'.$this->kclass->iif($itemstr['materialid']>0,$itemstr['standard'],$product['standard']).'</td>
								 <td>'.$itemstr['caption'].'</td>
								 <td>'.$itemstr['price'].'</td>
								
								 <td>'.$itemstr['packing'].'</td>
								 <td>'.$itemstr['discount'].'</td>
								  <td>'.$itemstr['quantity'].''.$itemstr['unit'].'</td>
								 <td>'.number_format($amount,2).'</td>
							</tr>';
			$quantity+=$itemstr['quantity'];
		 }
		 $itemtr.='<tr><td colspan="6" align="right">合计：</td><td>'.number_format($quantity).'</td><td>'.number_format($total,2).'</td></tr>';
	 }
			
	$body.=<<<EOF
		<div><span class="small gray">当前审核状态：{$ifverify}</span>   &nbsp;&nbsp;&nbsp;&nbsp;    <span class="gray small">{$verifier}</span>
		</div>
		<div class="title"><span class="right small gray">由 {$item['bname']}创建于{$date}</span>
		</div>
		<table class="hundred">
		<tr ><td colspan="4">名称： <strong>{$title}</strong>&nbsp;&nbsp;报价类型：<strong>{$item['qttitle']}</strong>&nbsp;&nbsp;支持币种:<strong>{$item['ctitle']}</strong> <strong>由{$item['uname']}于{$date}创建</strong></td></tr>
		<tr class="odd"><td width="350px">支持交货条款</td><td width="350px">{$item['detitle']}({$item['deentitle']})</td><td	width="350px">支持地区</td><td width="350px">{$item['ptitle']}({$item['pentitle']})</td></tr>
		<tr><td>规格说明</td><td>工艺说明</td><td>主要材料</td><td>备注</td></tr>
		<tr>
           <td>{$item['standrad']}</td>
            <td>{$item['technics']}</td>
               <td>{$item['material']}</td>
              <td>{$item['remark']}</td>
		</tr>
		</table>
		<table>
		<tbody>
		<tr><td  colspan=8>产品明细</td></tr>
		<tr class="odd">
		<td width="100px">报价产品</td><td >产品属性描述</td><td >说明</td><td>单价</td><td>包装</td><td>折扣</td><td>订购量</td><td>小计</td>	
		</tr>
		{$itemtr}
		</tbody>
		</table>
EOF;
    $t = date('F j, Y, H:i:s', TIMENOW);
		$footer=<<<EOD
			<div class="right">Page Created On: {$t}</div>
		New Wisdom Investment Limited
EOD;
     $this->kclass->page['pageTitle']='报价单-'.$title;
	 $this->kclass->page['title']='标准报价单';
	 $this->kclass->page['contact']='Address: 4-306, Xianglixincuen, Hongli West Road, Shenzhen, China<br>Tel: 86-755-83911841  Fax: 86-755-83906115  E-Mail: info@wisdom.hk';
     $this->kclass->page['caption'] = '<span class="middle bold">报价单名称.: '.$title.'</span>'.$this->kclass->iif($item['customerNo']!='', ' (Cust. Order No.: '.$item['customerNo'].')', '').'<br>Created: '.date('Y-m-d', $item['created']).'. Modified: '.date('Y-m-d', $item['modified']);
		$this->kclass->page['main']=$body;
		$this->kclass->page['footer']=$footer;

	}
	//
	function quotingprint(){
		//if(!$this->kclass->input['quotingid'] > 0)$this->kclass->boinkIt('/s.php?module=customer');
		if($this->kclass->input['chooselanguage']=='c1'){
			$quoting=$this->kclass->DB->queryFirst("SELECT quoting.*,
				supplier.title AS supplier,
			customer.title AS customer,
				shipmethod.title AS shipmethod,
			port.title AS port,
			paymentterm.title AS paymentterm, paymentterm.title AS enpaymentterm,
			deliveryterm.title AS deliveryterm,
			currency.title AS currency,
				m.realname AS creator,m.realname AS modifier,b.realname AS bname
			FROM `quoting`
			LEFT JOIN supplier ON (supplier.supplierid=`quoting`.supplierid)
			LEFT JOIN customer ON (customer.customerid=`quoting`.customerid)
			LEFT JOIN currency ON (currency.currencyid=`quoting`.currencyid)
			LEFT JOIN shipmethod ON (shipmethod.shipmethodid=`quoting`.shipmethodid)
			LEFT JOIN port ON (port.portid=`quoting`.portid)
		LEFT JOIN paymentterm ON (paymentterm.paymenttermid=`quoting`.paymenttermid)
			LEFT JOIN deliveryterm ON (deliveryterm.deliverytermid=`quoting`.deliverytermid)
			LEFT JOIN member AS m ON (m.userid=`quoting`.creator)
		LEFT JOIN member AS mem ON (mem.userid=`quoting`.modifier)
			LEFT JOIN `member` AS b      ON(b.userid=`quoting`.verifier)
			WHERE `quoting`.quotingid='".$this->kclass->input['quotingid']."'");
			if($quoting['ifverify']==0){
					$status='<span class="red">未审核</span>'.$action;
					$statusid=2;
				}elseif($quoting['ifverify']==1){
					$status='<span class="green">审核通过</span>';
					$verifier='（Approved by '.$quoting['bname'].' in'.date('Y-m-d',$quoting['verified']).'）';
					$statusid=3;
				}elseif($quoting['ifverify']==-1){
					$status='<span class="red">审核未通过</span>';
					$verifier='（Approved by '.$quoting['bname'].' in'.date('Y-m-d',$quoting['verified']).'）';
					$statusid=3;
				}
				$title = '';
				$dateline=date('Y-m-d',$quoting['dateline']);
				$created=date('Y-m-d',$quoting['created']);
				$modified=date('Y-m-d',$quoting['modified']);
				if($quoting['dateline']!='')$title .= '　<span class="small">交货时间：</span>'.$dateline;
			   	//if($quoting['shipmethod']!='')$title .= '　<span class="small">Delivery Terms：</span>'.$quoting['shipmethod'];
				//if($quoting['port']!='')$title .= '　<span class="small">Delivery Place：</span>'.$quoting['port'];
				if($quoting['paymentterm']!='')$title .= '　<span class="small">支付条款：</span>'.$quoting['paymentterm'];
				if($quoting['deliveryterm']!='')$title .= '　<span class="small">交货条款：</span>'.$quoting['deliveryterm'];
				$items = $this->kclass->DB->query("
					SELECT `item`.*,
						product.title AS product,
						material.standard,
						packing.title AS packing,
						unit.title AS unit
					FROM `item`
					LEFT JOIN product ON (product.productid=`item`.productid)
					LEFT JOIN material ON (material.materialid=`item`.materialid)
					LEFT JOIN packing ON (packing.packingid=`item`.packingid)
					LEFT JOIN unit ON (unit.unitid=`item`.unitid)
					WHERE `item`.killed=0 AND `item`.module='quoting' AND `item`.mid='".$quoting['quotingid']."'
					ORDER BY itemid ASC
				");
				$itemtr='';
				$total=$quantity=0;
				if($itemNum=$this->kclass->DB->numRows()){
					$itemtr = '<tr><td colspan="10"><table class="hundred"><thead><tr><td width="100px">产品</td><td width="280px">产品描述</td><td    width="400px">说明</td><td width="100px">包装</td><td width="100px">折扣</td><td width="100px">数量</td><td width="100px">单价</td><td width="100px">小计</td></tr></thead><tbody>';
					while($item = $this->kclass->DB->fetchArray($items)){
						if($item['quantity']>0 AND $item['price']>0){
							$amount=$item['quantity']*$item['price'];
							$total+=$amount;
						}
						$product=$this->kclass->getProduct(array('productid'=>$item['productid'],'itemid'=>$item['productitemid']));
						$itemtr .= '<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').'">
						<td>'.$product['title'].'</td>
						<td>'.$this->kclass->iif($item['materialid']>0,$item['standard'],$product['standard']).'</td>
						<td style="word-break:break-all">'.$item['caption'].'</td>
						<td>'.$item['packing'].'</td>
						<td align="right">'.$item['discount'].'</td>
						<td align="right">'.$item['quantity'].' '.$item['unit'].'</td>
						<td align="right">'.$item['price'].'</td>
						<td align="right">'.number_format($amount,2).'</td>
						</tr>';
						$quantity+=$item['quantity'];
					}
					$itemtr .= '<tr><td colspan="5" align="right">总计：</td><td align="right">'.number_format($quantity).'</td><td></td><td align="right">'.number_format($total, 2).'</td></tr><tr><td colspan="5" align="right">运费：</td><td align="right">'.$quoting['freight'].'</td><td align="right"> 总价</td><td align="right">'.number_format($total+$quoting['freight'], 2).'</td></tr></tbody></table></td></tr>';
				}
		$body=<<<EOF
		<table class="hundred">
<thead>
<tr><th colspan="10"><span class="right">{$options} <span class="plus hand" onclick="$('#quoting{$quoting['quotingid']}').toggle('fast');$(this).toggleClass('minus');"></span></span>	<span class="small">联系人：</span><span class="middle bold">{$quoting['contact']}</span>{$title}</th></tr>
</thead>
<tbody >
<tr><td colspan="10">报价单状态：{$status}{$verifier}<span class="right">{$flow}</span></td></tr>
<tr class="odd">
	<td width="150">运送方式：</td><td class="middle">{$quoting['shipmethod']}</td>
	<td width="150">交货地点：</td><td class="middle">{$quoting['port']}</td>
	<td width="150">交货条款：</td><td class="middle">{$quoting['deliveryterm']}</td>
	<td width="150">运费：</td><td class="middle">{$quoting['freight']}</td>
	<td width="150">交货时间：</td><td>{$dateline}</td>
</tr>
<tr class="even">
	<td colspan="2">规格说明：</td>
	<td colspan="2">主要材料：</td>
	<td colspan="2">工艺说明：</td>
	<td colspan="2">备注：</td>
	<td colspan="2">其他：</td>
</tr>
<tr class="odd">
<td class="middle" width="350px" colspan="2">{$quoting['standard']}</td>
<td class="middle" width="350px" colspan="2">{$quoting['material']}</td>
<td class="middle" width="350px" colspan="2">{$quoting['technics']}</td>
<td class="middle" width="350px" colspan="2">{$quoting['remark']}</td>
<td colspan="2"> {$quoting['creator']}  {$created} 创建， {$quoting['modifier']}  {$modified} 修改</td>
</tr>
{$itemtr}
</tbody>
</table>
EOF;
		}else{
		$quoting=$this->kclass->DB->queryFirst("SELECT quoting.*,
				supplier.title AS supplier,
			customer.title AS customer,
				shipmethod.entitle AS shipmethod,
			port.entitle AS port,
			paymentterm.entitle AS paymentterm, paymentterm.entitle AS enpaymentterm,
			deliveryterm.entitle AS deliveryterm,
			currency.title AS currency,
				m.username AS creator,m.username AS modifier,b.username AS bname
			FROM `quoting`
			LEFT JOIN supplier ON (supplier.supplierid=`quoting`.supplierid)
			LEFT JOIN customer ON (customer.customerid=`quoting`.customerid)
			LEFT JOIN currency ON (currency.currencyid=`quoting`.currencyid)
			LEFT JOIN shipmethod ON (shipmethod.shipmethodid=`quoting`.shipmethodid)
			LEFT JOIN port ON (port.portid=`quoting`.portid)
		LEFT JOIN paymentterm ON (paymentterm.paymenttermid=`quoting`.paymenttermid)
			LEFT JOIN deliveryterm ON (deliveryterm.deliverytermid=`quoting`.deliverytermid)
			LEFT JOIN member AS m ON (m.userid=`quoting`.creator)
		LEFT JOIN member AS mem ON (mem.userid=`quoting`.modifier)
			LEFT JOIN `member` AS b      ON(b.userid=`quoting`.verifier)
			WHERE `quoting`.quotingid='".$this->kclass->input['quotingid']."'");
			if($quoting['ifverify']==0){
					$status='<span class="red">Wait to be approved</span>'.$action;
					$statusid=2;
				}elseif($quoting['ifverify']==1){
					$status='<span class="green">Approved</span>';
					$verifier='（Approved by '.$quoting['bname'].' in'.date('Y-m-d',$quoting['verified']).'）';
					$statusid=3;
				}elseif($quoting['ifverify']==-1){
					$status='<span class="red">Fail to be approved</span>';
					$verifier='（Approved by '.$quoting['bname'].' in'.date('Y-m-d',$quoting['verified']).'）';
					$statusid=3;
				}
				$title = '';
				$dateline=date('Y-m-d',$quoting['dateline']);
				$created=date('Y-m-d',$quoting['created']);
				$modified=date('Y-m-d',$quoting['modified']);
				if($quoting['dateline']!='')$title .= '　<span class="small">Delivery Date：</span>'.$dateline;
			   	//if($quoting['shipmethod']!='')$title .= '　<span class="small">Delivery Terms：</span>'.$quoting['shipmethod'];
				//if($quoting['port']!='')$title .= '　<span class="small">Delivery Place：</span>'.$quoting['port'];
				if($quoting['paymentterm']!='')$title .= '　<span class="small">Payment Terms：</span>'.$quoting['paymentterm'];
				if($quoting['deliveryterm']!='')$title .= '　<span class="small">Delivery Terms：</span>'.$quoting['deliveryterm'];
				$items = $this->kclass->DB->query("
					SELECT `item`.*,
						product.entitle AS product,
						material.enstandard,
						packing.entitle AS packing,
						unit.entitle AS unit
					FROM `item`
					LEFT JOIN product ON (product.productid=`item`.productid)
					LEFT JOIN material ON (material.materialid=`item`.materialid)
					LEFT JOIN packing ON (packing.packingid=`item`.packingid)
					LEFT JOIN unit ON (unit.unitid=`item`.unitid)
					WHERE `item`.killed=0 AND `item`.module='quoting' AND `item`.mid='".$quoting['quotingid']."'
					ORDER BY itemid ASC
				");
				$itemtr='';
				$total=$quantity=0;
				if($itemNum=$this->kclass->DB->numRows()){
					$itemtr = '<tr><td colspan="10"><table class="hundred"><thead><tr><td width="100px">products</td><td width="280px">Product  Attribute</td><td    width="400px">Note</td><td width="100px">Package</td><td width="100px">discount</td><td width="100px">Amount</td><td width="100px">Price</td><td width="100px">Subtotal</td></tr></thead><tbody>';
					while($item = $this->kclass->DB->fetchArray($items)){
						if($item['quantity']>0 AND $item['price']>0){
							$amount=$item['quantity']*$item['price'];
							$total+=$amount;
						}
						$product=$this->kclass->getProduct(array('productid'=>$item['productid'],'itemid'=>$item['productitemid'],'module'=>'quoting'));
						$itemtr .= '<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').'">
						<td>'.$product['entitle'].'</td>
						<td>'.$this->kclass->iif($item['materialid']>0,$item['standard'],$product['standard']).'</td>
						<td style="word-break:break-all">'.$item['caption'].'</td>
						<td>'.$item['packing'].'</td>
						<td align="right">'.$item['discount'].'</td>
						<td align="right">'.$item['quantity'].' '.$item['unit'].'</td>
						<td align="right">'.$item['price'].'</td>
						<td align="right">'.number_format($amount,2).'</td>
						</tr>';
						$quantity+=$item['quantity'];
					}
					$itemtr .= '<tr><td colspan="5" align="right">total：</td><td align="right">'.number_format($quantity).'</td><td></td><td align="right">'.number_format($total, 2).'</td></tr><tr><td colspan="5" align="right">Freight：</td><td align="right">'.$quoting['freight'].'</td><td align="right"> Total Price</td><td align="right">'.number_format($total+$quoting['freight'], 2).'</td></tr></tbody></table></td></tr>';
				}
		$body=<<<EOF
		<table class="hundred">
<thead>
<tr><th colspan="10"><span class="right">{$options} <span class="plus hand" onclick="$('#quoting{$quoting['quotingid']}').toggle('fast');$(this).toggleClass('minus');"></span></span>	<span class="small">contacts：</span><span class="middle bold">{$quoting['contact']}</span>{$title}</th></tr>
</thead>
<tbody >
<tr><td colspan="10">Quotation Status：{$status}{$verifier}<span class="right">{$flow}</span></td></tr>
<tr class="odd">
	<td width="150">Shipping Method：</td><td class="middle">{$quoting['shipmethod']}</td>
	<td width="150">Delivery Place：</td><td class="middle">{$quoting['port']}</td>
	<td width="150">Delivery Terms：</td><td class="middle">{$quoting['deliveryterm']}</td>
	<td width="150">Freight：</td><td class="middle">{$quoting['freight']}</td>
	<td width="150">Delivery Date：</td><td>{$dateline}</td>
</tr>
<tr class="even">
	<td colspan="2">Specification：</td>
	<td colspan="2">Main Material：</td>
	<td colspan="2">Craft：</td>
	<td colspan="2">Remark：</td>
	<td colspan="2">Others：</td>
</tr>
<tr class="odd">
<td class="middle" width="350px" colspan="2">{$quoting['standard']}</td>
<td class="middle" width="350px" colspan="2">{$quoting['material']}</td>
<td class="middle" width="350px" colspan="2">{$quoting['technics']}</td>
<td class="middle" width="350px" colspan="2">{$quoting['remark']}</td>
<td colspan="2"> {$quoting['creator']}  {$created} Created， {$quoting['modifier']}  {$modified} Modified</td>
</tr>
{$itemtr}
</tbody>
</table>
EOF;
	}
       $t = date('F j, Y, H:i:s', TIMENOW);
		$footer=<<<EOD
			<div class="right">Page Created On: {$t}</div>
		New Wisdom Investment Limited
EOD;
    $this->kclass->page['pageTitle']='quoting-';
	$this->kclass->page['title']='Quotation';
	$this->kclass->page['contact']='Address: 4-306, Xianglixincuen, Hongli West Road, Shenzhen, China<br>Tel: 86-755-83911841  Fax: 86-755-83906115  E-Mail: info@wisdom.hk';
	$this->kclass->page['caption'] = '<span class="middle bold">Buyer.: '.$quoting['customer'].'</span>'.$this->kclass->iif($quoting['customerNo']!='', ' (Cust. Order No.: '.$quoting['customerNo'].')', '').'<br>Created: '.date('Y-m-d', $quoting['created']).'. Modified: '.date('Y-m-d', $quoting['modified']);
	$this->kclass->page['main']=$body;
		$this->kclass->page['footer']=$footer;
	}
	//
	/**
	 *询价单信息打印
	 *
	 */
	function customerInquiry(){
		$inquiryid = $this->kclass->input['inquiryid'];
		$language = $this->kclass->input['chooselanguage'];
		If($language=='c1'){			
			$inquirys=$this->kclass->DB->query("
			SELECT inquiry.*,
				shipmethod.title AS shipmethod,
				port.title AS port,
				paymentterm.title AS paymentterm,
				deliveryterm.title AS deliveryterm,			
				currency.title AS currency,
				m.realname AS creator,
				m.realname AS modifier,
				customer.abbr AS customer
			FROM `inquiry`
			LEFT JOIN currency ON (currency.currencyid=`inquiry`.currencyid)
			LEFT JOIN shipmethod ON (shipmethod.shipmethodid=`inquiry`.shipmethodid)
			LEFT JOIN port ON (port.portid=`inquiry`.portid)
			LEFT JOIN paymentterm ON (paymentterm.paymenttermid=`inquiry`.paymenttermid)
			LEFT JOIN deliveryterm ON (deliveryterm.deliverytermid=`inquiry`.deliverytermid)
			LEFT JOIN member AS m ON (m.userid=`inquiry`.creator)
			LEFT JOIN member AS mem ON (mem.userid=`inquiry`.modifier)
			LEFT JOIN customer ON (customer.customerid=`inquiry`.customerid)
			WHERE `inquiry`.killed=0 AND `inquiry`.inquiryid='{$inquiryid}'
		");
		//询价信息(zhCN）)
			while($inquiry = $this->kclass->DB->fetchArray($inquirys)){

				$title = '';
				switch($inquiry['inquirytype']){
					case 1:
						$inquirytype="无型号无数量";
						break;
					case 2:
						$inquirytype="无型号有数量";
						break;
					case 3:
						$inquirytype="有型号无数量";
						break;
					case 4:
						$inquirytype="有型号有数量";
						break;
				}

				$dateline=date('Y-m-d',$inquiry['dateline']);
				$created=date('Y-m-d',$inquiry['created']);
				$modified=date('Y-m-d',$inquiry['modified']);
				$contact = $inquiry['contact'];
				$currency = $inquiry['currency'];
				$paymentterm = $inquiry['paymentterm'];
				$shipmethod = $inquiry['shipmethod'];
				$port = $inquiry['port'];
				$deliveryterm = $inquiry['deliveryterm'];
				$inquirytype = $inquirytype;
				$cstandard = $inquiry['standard'];
				$material = $inquiry['material'];
				$technics = $inquiry['technics'];
				$remark = $inquiry['remark'] ;
				$creator = $inquiry['creator'];
				$modifier = $inquiry['modifier'];
				$customer =$inquiry['customer'];
				$created2 = date("Ymd",$inquiry['created']);
				$number = $inquiry['inquiryid'];


			}
			$thcontact = " 联系人：";
			$thcurrency = " 币种：";
			$thdateline = " 交货日期：";
			$thpaymentterm = " 支付条款：";
			$thshipmethod = " 运送方式：";
			$thport = " 交货地点：";
			$thdeliveryterm = " 交货条款：";
			$thinquirytype = " 询价单类型：";
			$thcontent = "询价内容：";
			$thmaterial = "主要材料";
			$thtechnics = "工艺说明：";
			$thremark = "备　　注：";
			$thothers = "其　　他：";
			$otherinfo = "由 {$creator} 于 {$created} 建立，由 {$modifier} 于 {$modified} 最后修改";	
			$thproduct = "咨询产品";
			$thattribute = "产品特有属性";
			$thnotes = "说明";
			$thpackage = "包装";
			$thdiscount = "折扣";
			$thamount = "数量";
			$ptitle = "询价单";
			
			//产品信息（zhCN）
			$items = $this->kclass->DB->query("
				SELECT `item`.*,
					product.title AS product,
					material.standard,
					packing.title AS packing,
					unit.title AS unit
				FROM `item`
				LEFT JOIN product ON (product.productid=`item`.productid)
				LEFT JOIN material ON (material.materialid=`item`.materialid)
				LEFT JOIN packing ON (packing.packingid=`item`.packingid)
				LEFT JOIN unit ON (unit.unitid=`item`.unitid)
				WHERE `item`.killed=0 AND `item`.module='inquiry'
				AND mid='{$inquiryid}'
			
		");	
		

		
		$itemtr='';
		if($itemNum=$this->kclass->DB->numRows()){
			$itemtr = '<table class="hundred"><thead><tr><th>'.$thproduct.'</th><th>'.$thattribute.'</th><th>'.$thnotes.'</th><th>'.$thpackage.'</th><th>'.$thdiscount.'</th><th>'.$thamount.'</th></tr></thead><tbody>';
			while($item = $this->kclass->DB->fetchArray($items)){
				$productid = $item['productid'];
				$productitemid = $item['productitemid'];
				$productitle = $item['product'];
				$standard = $item['standard'];
				$caption = $item['caption'];
				$packing = $item['packing'];
				$discount = $item['discount'];
				$quantity = $item['quantity'];
				$unit = $item['unit'];
				$pcreated = date("Y-m-d H:i:s",$item['created']);
				
			}
				$selectStr = '';
				$itemtr .= '<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').'">
				<td>'.$productitle.'</td>
				<td>'.$standard.'</td>
				<td>'.$caption.'</td>
				<td>'.$packing.'</td>
				<td>'.$discount.'</td>
				<td>'.$quantity.' '.$unit.'</td>
				</tr>';
			$itemtr .= '</tbody></table>';

		}

		}
		elseif($language=='c2'){
			$inquirys=$this->kclass->DB->query("
			SELECT inquiry.*,
				shipmethod.entitle AS enshipmethod,
				port.entitle AS enport,
				paymentterm.entitle AS enpaymentterm,
				deliveryterm.entitle AS endeliveryterm,				
				currency.title AS currency,
				m.realname AS creator,
				m.realname AS modifier,
				customer.abbr AS customer
			FROM `inquiry`
			LEFT JOIN currency ON (currency.currencyid=`inquiry`.currencyid)
			LEFT JOIN shipmethod ON (shipmethod.shipmethodid=`inquiry`.shipmethodid)
			LEFT JOIN port ON (port.portid=`inquiry`.portid)
			LEFT JOIN paymentterm ON (paymentterm.paymenttermid=`inquiry`.paymenttermid)
			LEFT JOIN deliveryterm ON (deliveryterm.deliverytermid=`inquiry`.deliverytermid)
			LEFT JOIN member AS m ON (m.userid=`inquiry`.creator)
			LEFT JOIN member AS mem ON (mem.userid=`inquiry`.modifier)
			LEFT JOIN customer ON (customer.customerid=`inquiry`.customerid)
			WHERE `inquiry`.killed=0 AND `inquiry`.inquiryid='{$inquiryid}'
		");
			//询价信息(en)
			while($inquiry = $this->kclass->DB->fetchArray($inquirys)){

				$title = '';
				switch($inquiry['inquirytype']){
					case 1:
						$inquirytype="Without Model and Amount";
						break;
					case 2:
						$inquirytype="Without Model With Amount";
						break;
					case 3:
						$inquirytype="With Model Without Amount";
						break;
					case 4:
						$inquirytype="With Model With Amount";
						break;
				}

				$dateline=date('Y-m-d',$inquiry['dateline']);
				$created=date('Y-m-d',$inquiry['created']);
				$modified=date('Y-m-d',$inquiry['modified']);
				$contact = $inquiry['contact'];
				$currency = $inquiry['currency'];
				$paymentterm = $inquiry['enpaymentterm'];
				$shipmethod = $inquiry['enshipmethod'];
				$port = $inquiry['enport'];
				$deliveryterm = $inquiry['endeliveryterm'];
				$inquirytype = $inquirytype;
				$cstandard = $inquiry['standard'];
				$material = $inquiry['material'];
				$technics = $inquiry['technics'];
				$remark = $inquiry['remark'] ;
				$creator = $inquiry['creator'];
				$modifier = $inquiry['modifier'];
				$customer =$inquiry['customer'];
				$created2 = date("Ymd",$inquiry['created']);
				$number = $inquiry['inquiryid'];


			}
			$thcontact = " Attn:";
			$thcurrency = " Currency:";
			$thdateline = " Delivery Date:";
			$thpaymentterm = " Payment Term:";
			$thshipmethod = " Shipping Method:";
			$thport = " Delivery Place:";
			$thdeliveryterm = " Delivery Term:";
			$thinquirytype = " Inquiry Type:";
			$thcontent = "Inquiry Content:";
			$thmaterial = "Main Material：";
			$thtechnics = "Technics:";
			$thremark = "Remarks:";
			$thothers = "Others:";
			$otherinfo = "{$creator} created {$created} ,{$modifier} modified {$modified}";		
			$thproduct = " Inquiry Products:";
			$thattribute = " Product Attribute:";
			$thnotes = " Note:";
			$thpackage = " Package:";
			$thdiscount = " Discount:";
			$thamount = " Amount:";
			$ptitle = "Inquiry";

			//产品信息（en）

			$items = $this->kclass->DB->query("
			SELECT `item`.*,
				product.entitle AS enproduct,
				material.enstandard,
				packing.entitle AS enpacking,
				unit.entitle AS enunit
			FROM `item`
			LEFT JOIN product ON (product.productid=`item`.productid)
			LEFT JOIN material ON (material.materialid=`item`.materialid)
			LEFT JOIN packing ON (packing.packingid=`item`.packingid)
			LEFT JOIN unit ON (unit.unitid=`item`.unitid)
			WHERE `item`.killed=0 AND `item`.module='inquiry'
			AND mid='{$inquiryid}'
			
		");

			$itemtr='';			
			if($itemNum=$this->kclass->DB->numRows()){
				$itemtr = '<table class="hundred"><thead><tr><th>'.$thproduct.'</th><th>'.$thattribute.'</th><th>'.$thnotes.'</th><th>'.$thpackage.'</th><th>'.$thdiscount.'</th><th>'.$thamount.'</th></tr></thead><tbody>';
				while($item = $this->kclass->DB->fetchArray($items)){
					$productid = $item['productid'];
					$productitemid = $item['productitemid'];
					$productitle = $item['enproduct'];
					$standard = $item['enstandard'];
					$caption = $item['caption'];
					$packing = $item['enpacking'];
					$discount = $item['discount'];
					$quantity = $item['quantity'];
					$unit = $item['enunit'];
					$pcreated = date("Y-m-d H:i:s",$item['created']);
					$pcreated2 = date("YmdHi",$item['created']);
				}
					$selectStr = '';
					$itemtr .= '<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').'">
					<td>'.$productitle.'</td>
					<td>'.$standard.'</td>
					<td>'.$caption.'</td>
					<td>'.$packing.'</td>
					<td>'.$discount.'</td>
					<td>'.$quantity.' '.$unit.'</td>
					</tr>';
				$itemtr .= '</tbody></table>';
			}
		}
	$body = <<<EOF
	<table class="hundred">
	<thead>
		<tr class="even">
			<th colspan="10">
				<span class="small">{$thcontact}</span><span class="middle bold">{$contact}</span>
				<span class="small">{$thcurrency}</span>
					{$currency}
				<span class="small">{$thdateline}</span>
					{$dateline}
				<span class="small">{$thpaymentterm}</span>
					{$paymentterm}
				<span class="small">{$thshipmethod}</span>
					{$shipmethod}
				<span class="small">{$thport}</span>
					{$port}
				<span class="small">{$thdeliveryterm}</span>
					{$deliveryterm}
				<span class="small">{$thinquirytype}</span>
					{$inquirytype}
			</th>	
		</tr>
	</thead>
	<tbody id="inquiry{$inquiry['inquiryid']}" style="">
		<tr class="odd">
			<td width="80">{$thshipmethod}</td><td class="middle">{$shipmethod}</td>
			<td width="80">{$thport}</td><td class="middle">{$port}</td>
			<td width="80">{$thpaymentterm}</td><td class="middle">{$paymentterm}</td>
			<td width="80">{$thdeliveryterm}</td><td class="middle">{$deliveryterm}</td>
			<td width="80">{$thdateline}</td><td>{$dateline}</td>
		</tr>
		<tr class="even">
			<td>{$thcontent}</td><td class="middle">{$cstandard}</td>
			<td>{$thmaterial}</td><td class="middle">{$material}</td>
			<td>{$thtechnics}</td><td class="middle">{$technics}</td>
			<td>{$thremark}</td><td class="middle">{$remark}</td>
			<td>{$thothers}</td><td>{$otherinfo}</td>
		</tr>
	{$itemtr}
	</tbody>
	</table>
EOF;
    $t = date('F j, Y, H:i:s', TIMENOW);
		$footer=<<<EOD
			<div class="right">Page Created On: {$t}</div>
		New Wisdom Investment Limited
EOD;
	
	$this->kclass->page['pageTitle']='询价单-'.$title;
	$this->kclass->page['title']=$ptitle;
	$this->kclass->page['contact']='Address: 4-306, Xianglixincuen, Hongli West Road, Shenzhen, China<br>Tel: 86-755-83911841  Fax: 86-755-83906115  E-Mail: info@wisdom.hk';
	$this->kclass->page['caption'] = '<span class="middle bold">Inquiry No.: '.$created2.$number.' '.$customer.'</span>'.$this->kclass->iif($item['customerNo']!='', ' (Cust. Order No.: '.$item['customerNo'].')', '').'<br>Created: '.$created.'. Modified: '.$modified;
	$this->kclass->page['main']=$body;
	$this->kclass->page['footer']=$footer;
	
	}
	 
	// 订单打印
	function invoice(){
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
		");
		if(!$invoice)	$this->kclass->boinkIt('/s.php?module=invoice&action=find');
		if($invoice['modifier']>0){
			$modified = '，'.$invoice['mname'].' 于 '.date('y-m-d H:i:s', $invoice['modified']).' 最后修改';
		}
		$created = date('y-m-d H:i:s', $invoice['created']);
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
		$item = $this->kclass->relatedPrintInvoiceItem(array('invoiceid'=>$invoice['invoiceid']));
		$total = $item['total'];
		$feeitem = $this->kclass->relatedPrintFee(array('invoiceid'=>$invoice['invoiceid']));
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
				$discounttr .= '<tr class="odd">
				<td>'.$discount['reason'].'</td>
				<td align="right">'.$discount['addition'].''.$discount['discount'].''.$this->kclass->iif($discount['sign']=='%', $discount['sign'], '').'</td>
				<td align="right">'.$discount['addition'].number_format($amount, 2).'</td>
				<td align="right">'.$invoice['currencySymbol'].number_format($total, 2).'</td>
				</tr>';
			}
			$discounttr = '<table class="printTab">
 			<tbody><tr class="bold"><td colspan="4">Discount Information</td></tr><tr><td title="Reason">Discount Reason</td><td title="Discount" align="right">Discount</td><td align="right">Number of discount</td><td title="Amount" align="right">Amount</td></tr>'.$discounttr.'<tr><td colspan="12" align="right">Subtotal：<span class="bold">'.$invoice['currencyTitle'].' '.$invoice['currencySymbol'].number_format($total, 2).'</span></td></tr></tbody></table>';
		}else{
			$discounttr = '';
		}
		$total = number_format($total, 2);
		if($invoice['refno']==''){
			$invoice['refno'] = '<i class="gray">None</i>';
		}
		//$related=$this->kclass->relatedPackinglist(array('invoiceid'=>$invoice['invoiceid']));
		//		$inquiry=$this->kclass->relatedPacking(array('productid'=>$invoice['productid'], 'options'=>1));
		//		$quoting=$this->kclass->relatedQuoting(array('productid'=>$invoice['productid'], 'options'=>1));
		$body = <<<EOF
<table class="printTab">
<tbody>
<tr class="bold">
	<td colspan="4"><span class="right"><span class="normal">Number of Cargo：</span>{$invoice['quantity']} {$invoice['unitEntitle']} <span class="normal">Total Amount：</span>{$invoice['currencyTitle']} {$invoice['currencySymbol']}{$total}</span>Customer Information</td>
</tr>
<tr class="odd">
	<td width="180">Customer Name：<br>Invoice Title：</td>
	<td width="310">{$invoice['customerTitle']}<br>{$invoice['title']}</td>
	<td width="180">Invoice Address：</td>
	<td>{$invoice['address']}<br>{$region}</td>
</tr>
</tbody>
</table>
		
<table class="printTab">
<tbody>
<tr class="bold">
	<td colspan="4"><span class="right"><span class="normal" title="Currency">Currency：</span>{$invoice['currencyTitle']} </span>General Information</td>
</tr>
<tr class="odd">
	<td width="180" title="Shipment Method">Express Method：</td>
	<td width="310">{$invoice['shipmethodEntitle']}</td>
	<td width="180" title="Delivery Term">Delivery Terms：</td>
	<td class="middle">{$invoice['deliverytermEntitle']}</td>
</tr>
<tr class="odd">
	<td title="Port of Loading">Loading Place：</td>
	<td class="middle">{$invoice['loadingCountryEntitle']} {$invoice['loadingEntitleitle']} </td>
	<td title="Shipment Date">Delivery Time：</td>
	<td class="middle"><span class="bold">{$onBoardDate}</span></td>
</tr>
<tr class="odd">
	<td title="Port of Discharge">Unloading Place：</td>
	<td class="middle">{$invoice['dischargeCountryEntitle']} {$invoice['dischargeEntitle']}</td>
	<td title="Date of Arrival">Arrival Time：</td>
	<td class="middle"><span class="bold">{$dateOfArrival}</span></td>
</tr>
<tr class="odd">
	<td title="Final Destination">final destination：</td>
	<td class="middle">{$invoice['finalDestination']}</td>
	<td title="1st Carrier">1<sup>st</sup>Freight Company：</td>
	<td class="middle">{$invoice['firstCarrier']}</td>
</tr>
<tr class="odd">
	<td title="Payment Term">Payment Terms：</td>
	<td class="middle">{$invoice['paymenttermEntitle']}</td>
	<td title="2nd Carrier">2<sup>nd</sup>Freight Company：</td>
	<td class="middle">{$invoice['secondCarrier']}</td>
</tr>
<tr class="odd">
	<td valign="top" title="Commodity">Goods for declaration：</td>
	<td>{$invoice['commodity']}</td>
	<td valign="top" title="Remark">Remark：</td>
	<td>{$invoice['remark']}</td>
</tr>
</tbody>
</table>
		

{$item['tr']}


{$feeitem['tr']}

		

{$discounttr}

<table class="printNoborderTab">
<tbody>
<tr><td align="right">Total Amount：<span class="middle bold">{$invoice['currencyTitle']} {$invoice['currencySymbol']}{$total}</span></td></tr>
</tbody>
</table>
EOF;
		$t = date('F j, Y, H:i:s', TIMENOW);
		$footer=<<<EOD
			<div class="right">Page Created On: {$t}</div>
		New Wisdom Investment Limited
EOD;
		$this->kclass->page['pageTitle']='INVOICE - '.$invoice['invoiceno'];
		$this->kclass->page['title'] = 'COMMERCIAL INVOICE';
		$this->kclass->page['contact']='Address: 4-306, Xianglixincuen, Hongli West Road, Shenzhen, China<br>Tel: 86-755-83911841  Fax: 86-755-83906115  E-Mail: info@wisdom.hk';
		$this->kclass->page['caption'] = '<span class="middle bold">Invoice No.: '.$invoice['invoiceno'].'</span>'.$this->kclass->iif($invoice['customerNo']!='', ' (Cust. Order No.: '.$invoice['customerNo'].')', '').'<br>Created: '.date('Y-m-d', $invoice['created']).'. Modified: '.date('Y-m-d', $invoice['modified']);
		$this->kclass->page['main']=$body;
		$this->kclass->page['footer']=$footer;
	}
	
	function packingPO(){
		$orderList = '';
		$order = $this->kclass->DB->queryFirst("
			SELECT * FROM `order` WHERE orderid=".$this->kclass->input['orderid']."
		");
		
		$packinglist = $this->kclass->DB->queryFirst("
			SELECT * FROM `packinglist` WHERE module='order' AND mid=".$this->kclass->input['orderid']."
		");
		$items = $this->kclass->DB->query("
			SELECT item.*,
				productitem.attributevalue,
				productitem.mid AS productId,
				productitem.packingsizeid,
				productitem.packingsizeid1,
				productitem.perGrossWeight,
				brand.title AS brand,
				packing.title AS packing,
				unit.entitle AS unit
			FROM `item`
			LEFT JOIN `productitem` ON (productitem.productitemid=item.productitemid)
			LEFT JOIN packing ON (packing.packingid=item.packingid)
			LEFT JOIN brand ON (brand.brandid=item.brandid)
			LEFT JOIN unit ON (unit.unitid=item.unitid)
			WHERE `item`.killed=0 AND `item`.module='order' AND `item`.mid='".$this->kclass->input['orderid']."' AND item.productitemid>0
			ORDER BY itemid ASC, modified ASC
		");
		if($this->kclass->DB->numRows()){
			$i=1;
			$loopNum = 1;
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
						ORDER BY an.ordering ASC
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
						$selectStr .= '';
						for($n=0;$n<count($value[$val]);$n++){
							if($valueId[$val][$n]==$valueStr[$key]){
								$selectStr .= ''.$value[$val][$n].'';
							}
						}
						$selectStr .= '　';
					}
				}else{
					$selectStr .= '';
				}
				
				$packingsize = $this->kclass->DB->queryFirst("SELECT * FROM `packingsize` WHERE packingsizeid='".$item['packingsizeid']."'");				
				if(0!=$item['packingsize1']){
					$packingsize1 = $this->kclass->DB->queryFirst("SELECT * FROM `packingsize` WHERE packingsizeid='".$item['packingsizeid1']."'");
				}
				$cartonNum = intVal($item['quantity']/$packingsize['cartonNum']);
				$modNum = $item['quantity']%$packingsize['cartonNum'];
				$temp = '';
				if(10>$loopNum){
					$temp .= '0'.$loopNum;
				}else{
					$temp .= $loopNum;
				}
				$num = $loopNum+$cartonNum-1;
				if(10>$num){
					$temp .= ' - 0'.$num;
				}else{
					$temp .= ' - '.$num;
				}
				$subWeight = $cartonNum*$item['perGrossWeight'];
				$loopNum = $loopNum+$cartonNum;
				if(1==$cartonNum){
					$cartonNumStr = '1carton';
				}else{
					$cartonNumStr = $cartonNum.'cartons';
				}
				$orderList .= '<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').'">
					<td>'.$temp.'<br>'.$packingsize['length'].' x '.$packingsize['width'].' x '.$packingsize['height'].'/'.$packingsize['cartonNum'].'pcs<br>'.$cartonNumStr.'　'.$subWeight.'kg</td>
					<td>'.$product['title'].'： '.$selectStr.'</td>
					<td>'.$item['quantity'].' '.$item['unit'].'</td>
					<td>'.$item['cartonNo'].'</td>	
				</tr>';
				/*
				$productitem = $this->kclass->DB->queryFirst("SELECT * FROM `productitem` WHERE productitemid='".$item['productitemid']."'");
				if($productitem){
					$packingsize = $this->kclass->DB->queryFirst("SELECT * FROM `packingsize` WHERE packingsizeid='".$productitem['packingsize']."'");
					$packingsize1 = $this->kclass->DB->queryFirst("SELECT * FROM `packingsize` WHERE packingsizeid='".$productitem['packingsize1']."'");
				}
					
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
				*/
			}
			$orderList .= '<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').'">
				<td class="bold" valign="top" height="100">剩余包装</td>
				<td colspan="3"  valign="top" >'.$packinglist['packingNote'].'</td>		
			</tr>';
		}
		$orderDate = date('Y-m-d',$order['created']);
		$body = <<<EOF
<form action="/s.php?module=packinglist&action=insertPacking" name="invoice" method="post">
<input type="hidden" name="module" value="packinglist">
<input type="hidden" name="action" value="insertPacking">
<input type="hidden" name="orderid" value="{$this->kclass->input['orderid']}">
<table class="printNoborderTab">
<tbody>
<tr class="odd">
	<td width="115" align="right">订单编号：</td>
	<td width=""><input type="text" class="underlineInput150" value="{$order['orderno']}"></td>
	<td width="115" align="right">计划完成日期：</td>
	<td><input type="text" class="underlineInput150" value=""></td>
	<td width="115" align="right">计划发货日期：</td>
	<td><input type="text" class="underlineInput150" value=""></td>
</tr>
<tr class="odd">
	<td width="115" align="right">下单日期：</td>
	<td width=""><input type="text" class="underlineInput150" value="{$orderDate}"></td>
	<td width="115" align="right">实际完成日期：</td>
	<td><input type="text" class="underlineInput150" value=""></td>
	<td width="115" align="right">实际发货日期：</td>
	<td><input type="text" class="underlineInput150" value=""></td>
</tr>
</tbody>
</table>
<table class="printTab">
<thead>
<tr><th width="200">箱号</th><th>装箱内容</th><th width="60">数量</th><th width="200">序列号</th></tr>
</thead>
<tbody>
{$orderList}
</tbody>
</table>
</form>
EOF;
$footer=<<<EOD
			<div class="right">Tel：0755-28990261　　Fax：0755-28998861</div>
		公司地址：深圳市龙岗区宝荷路嶂背大道9号1楼
EOD;
		$this->kclass->page['title'] = '装 箱 单';
		$this->kclass->page['contact']='深圳市中孚能电气设备有限公司';
		$this->kclass->page['caption'] = '记录编号：NW-QR-SL001　版本：V1.01';
		$this->kclass->page['main']=$body;
		$this->kclass->page['footer']=$footer;
	}
	
	
	function packingPL(){
		$order = $this->kclass->DB->queryFirst("
			SELECT `order`.* ,
			`customer`.title AS customerTitle,`customer`.address AS customerAddress,`customer`.linkman AS customerLinkman,`customer`.telephone AS customerTelephone
			FROM `order` 
			LEFT JOIN customer ON (customer.customerid=`order`.customerid)
			WHERE orderid=".$this->kclass->input['orderid']."
		");
		if($order['parentid']!=0){
			$parentorder = $this->kclass->DB->queryFirst("
				SELECT `order`.* ,
				`customer`.title AS customerTitle,`customer`.address AS customerAddress,`customer`.linkman AS customerLinkman,`customer`.telephone AS customerTelephone
				FROM `order`
				LEFT JOIN customer ON (customer.customerid=`order`.customerid)
				WHERE orderid=".$order['parentid']."
			");
		}else{
			$parentorder = $order;
		}
		$packinglist = $this->kclass->DB->queryFirst("
			SELECT * FROM `packinglist` WHERE module='order' AND mid=".$this->kclass->input['orderid']."
		");
		
		$items = $this->kclass->DB->query("
			SELECT item.*,
				productitem.attributevalue,
				productitem.mid AS productId,
				productitem.packingsizeid,
				productitem.packingsizeid1,
				productitem.perGrossWeight,
				brand.title AS brand,
				packing.title AS packing,
				unit.entitle AS unit
			FROM `item`
			LEFT JOIN `productitem` ON (productitem.productitemid=item.productitemid)
			LEFT JOIN packing ON (packing.packingid=item.packingid)
			LEFT JOIN brand ON (brand.brandid=item.brandid)
			LEFT JOIN unit ON (unit.unitid=item.unitid)
			WHERE `item`.killed=0 AND `item`.module='order' AND `item`.mid='".$this->kclass->input['orderid']."' AND item.productitemid>0
			ORDER BY itemid ASC, modified ASC
		");
		if($numRow = $this->kclass->DB->numRows()){
			$i=1;
			$loopNum = 1;
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
						ORDER BY an.ordering ASC
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
						$selectStr .= '';
						for($n=0;$n<count($value[$val]);$n++){
							if($valueId[$val][$n]==$valueStr[$key]){
								$selectStr .= ''.$value[$val][$n].'';
							}
						}
						$selectStr .= '　';
					}
				}else{
					$selectStr .= '';
				}
		
				$packingsize = $this->kclass->DB->queryFirst("SELECT * FROM `packingsize` WHERE packingsizeid='".$item['packingsizeid']."'");
				if(0!=$item['packingsize1']){
					$packingsize1 = $this->kclass->DB->queryFirst("SELECT * FROM `packingsize` WHERE packingsizeid='".$item['packingsizeid1']."'");
				}
				$cartonNum = intVal($item['quantity']/$packingsize['cartonNum']);
				$modNum = $item['quantity']%$packingsize['cartonNum'];
				$temp = '';
				if(10>$loopNum){
					$temp .= '0'.$loopNum;
				}else{
					$temp .= $loopNum;
				}
				$num = $loopNum+$cartonNum-1;
				if(10>$num){
					$temp .= ' - 0'.$num;
				}else{
					$temp .= ' - '.$num;
				}
				
				$subWeight += $cartonNum*$item['perGrossWeight'];
				$subVolume += $cartonNum*$packingsize['length']*$packingsize['width']*$packingsize['height'];
				
				$loopNum = $loopNum+$cartonNum;
				if(1==$cartonNum){
					$cartonNumStr = '1CARTON';
				}else{
					$cartonNumStr = $cartonNum.'CARTONS';
				}
				
				$productStr .= $product['title'].'<br><br>';
				$standStr .= $temp.'<br>'.$packingsize['length'].' x '.$packingsize['width'].' x '.$packingsize['height'].'CM<br>'.$cartonNumStr.'/'.$item['perGrossWeight'].'KG<br>'.$packingsize['cartonNum'].'PCS　'.$product['title'].'<br><br>';
				$quantityStr .= $product['title'].'<br>('.$item['quantity'].' '.$item['unit'].')<br><br>';
				$i++;
			}
			$subVolume = floatVal($subVolume/1000000);
			$TotalVolume = $subVolume + $packinglist['surplusVolume'];
			$TotalWeight = $subWeight + $packinglist['surplusWeight'];
			$TotalVolume = number_format($TotalVolume, 2);
			$TotalWeight = number_format($TotalWeight, 2);
			$standStr .= $packinglist['packingNote'];
			$tabstr = '
				<tr class="odd">
					<td height="300" valign="top"><input type="text" style="border:0px;" /></td>
					<td valign="top">'.$productStr.'</td>
					<td valign="top"><input type="text" style="border:0px;" /></td>
					<td valign="top">'.$standStr.'</td>
					<td valign="top">'.$TotalVolume.'CBM</td>
					<td valign="top">'.$TotalWeight.'KGS</td>
					<td valign="top">'.$quantityStr.'</td>
				</tr>
			';
		}
		$nowDate = date('Y-m-d',TIMENOW);
$body = <<<EOF
<table class="printNoborderTab">
		<tr>
			<td width="160">PackingList No.：</td>
			<td width="170">{$packinglist['packinglistno']}</td>
			<td width="160">PurchaseOrder No.：</td>
			<td width="170">{$parentorder['orderno']}</td>
			<td width="130">DATE：</td>
			<td width="170">{$nowDate}</td>
		</tr>
		<tr><td colspan="6">　</td></tr>
		<tr><td colspan="6" class="bold">Sell To：</td></tr>
		<tr>
			<td width="160">Company：</td>
			<td colspan="5">{$parentorder['customerTitle']}</td>
		</tr>
		<tr>
			<td width="160">Name：</td>
			<td colspan="5">{$parentorder['customerLinkman']}</td>
		</tr>	
		<tr>
			<td width="160">Address：</td>
			<td colspan="5">{$parentorder['customerAddress']}</td>
		</tr>
		<tr>
			<td width="160">Phone：</td>
			<td colspan="5">{$parentorder['customerTelephone']}</td>
		</tr>
</table>
		
<table class="printTab">
	<thead>
			<tr>
				<th rowspan="2" align="center" width="160">MARKS</th>
				<th colspan="3" align="center">DESCRIPTION</th>
				<th align="center">CUBAGE</th>
				<th align="center">G.W.</th>
				<th align="center" width="120">QUANTITY</th>
			</tr>
			<tr>
				<th align="center">PRODUCTS NAME</th>
				<th align="center">CARTON NUMBER</th>
				<th align="center">CARTON SIZE(cm)</th>
				<th align="center">(M³)</th>
				<th align="center">(KG)</th>
				<th align="center">(PCS)</th>
			</tr>
	</thead>
	<tbody>
		{$tabstr}
	</tbody>
</table>
<table class="printNoborderTab">
		<tr>
			<td align="center">If you have any questions concerning this invoice, Please contact: Tom Ding,  86-755-83911841,  tom@li-ion-battery.com</td>
		<tr>
		<tr>
			<td align="center">THANK YOU FOR YOUR BUSINESS!</td>
		</tr>
</table>
EOF;
		
				$t = date('F j, Y, H:i:s', TIMENOW);
				$footer=<<<EOD
			<div class="right">Page Created On: {$t}</div>
		New Wisdom Investment Limited
EOD;
				$this->kclass->page['pageTitle']='PACKING LIST - '.$packinglist['packinglistno'];
				$this->kclass->page['title'] = 'PACKING LIST';
				$this->kclass->page['contact']='Address: 4-306, Xianglixincuen, Hongli West Road, Shenzhen, China<br>Tel: 86-755-83911841  Fax: 86-755-83906115  E-Mail: info@wisdom.hk';
				$this->kclass->page['caption'] = '<span class="middle bold">Packinglist No.: '.$packinglist['packinglistno'].'</span>'.$this->kclass->iif($invoice['customerNo']!='', ' (Cust. Order No.: '.$invoice['customerNo'].')', '').'<br>Created: '.date('Y-m-d', $packinglist['created']).'.';
				$this->kclass->page['main']=$body;
				$this->kclass->page['footer']=$footer;
	}
	
	function packinglist(){
		$pl = $this->kclass->DB->queryFirst("
			SELECT p.packinglistid, p.packinglistno, p.title, p.address, p.invoiceno, p.remark, p.modified, p.modifier, p.created, p.creator,
				region.encountry AS regionEncountry, region.country AS regionCountry, region.enstate AS regionEnstate, region.state AS regionState, region.encity AS regionEncity, region.city AS regionCity,
				customer.title AS customerTitle, customer.address AS customerAddress, customer.linkman AS customerLinkman, customer.position AS customerPosition, customer.telephone AS customerTelephone, customer.fax AS customerFax,
				m.username AS mname, c.username AS cname
			FROM `packinglist` AS p
			LEFT JOIN region ON (region.regionid=`p`.regionid)
			LEFT JOIN customer ON (customer.customerid=`p`.customerid)
			LEFT JOIN `user` AS m ON (m.userid=p.modifier)
			LEFT JOIN `user` AS c ON (c.userid=p.creator)
			WHERE p.killed=0 AND p.packinglistid='".$this->kclass->input['packinglistid']."'
		");
		$items = $this->kclass->DB->query("
			SELECT `item`.*,
				`unit`.title AS unitTitle,
				`order`.orderno, `order`.created,
				`product`.productno, `product`.title AS productTitle
			FROM `item`
			LEFT JOIN `order` ON (`order`.orderid=`item`.orderid)
			LEFT JOIN `unit` ON (`unit`.unitid=`item`.unitid)
			LEFT JOIN `product` ON (`product`.productid=`item`.productid)
			WHERE `item`.killed=0 AND `item`.module='packinglist' AND mid='".$pl['packinglistid']."'
			ORDER BY itemid ASC, modified ASC
		");
		if($this->kclass->DB->numRows()){
			$cartons=$grossWeight=$netWeight=$volume=0;
			$itemtr='';
				while($item = $this->kclass->DB->fetchArray($items)){
					$itemtr .= '<tr class="odd">
					<td></td>
					<td align="center">'.$item['productTitle'].'</td>
					<td align="center">'.$item['cartonNo'].'</td>
					<td align="center">'.$item['length'].' X '.$item['width'].' X '.$item['height'].'</td>
					<td align="center">'.$item['totalVolume'].'CBM</td>
					<td align="center">'.$item['totalWeight'].'KGS</td>
					<td align="center">'.$item['quantity'].'PCS</td>
					</tr>';

					$cartons+=$item['totalCartons'];
					$Weight+=$item['totalWeight'];
					$netWeight+=$item['totalNetWeight'];
					$volume+=$item['totalVolume'];
				}
		}

$body = <<<EOF
<table class="printNoborderTab">
		<tr>
			<td width="130">INVOICE：</td>
			<td width="200">{$pl['invoiceno']}</td>
			<td width="130">PO：</td>
			<td width="200"></td>
			<td width="130">DATE：</td>
			<td width="200"></td>
		</tr>
		<tr><td colspan="6">　</td></tr>
		<tr><td colspan="6" class="bold">Sell To：</td></tr>
		<tr>
			<td width="130">Name：</td>
			<td colspan="5">{$pl['customerLinkman']}</td>
		</tr>
		<tr>
			<td width="130">Company：</td>
			<td colspan="5">{$pl['customerTitle']}</td>
		</tr>
		<tr>
			<td width="130">Address：</td>
			<td colspan="5">{$pl['customerAddress']}</td>
		</tr>
		<tr>
			<td width="130">Phone：</td>
			<td colspan="5">{$pl['customerTelephone']}</td>
		</tr>
</table>

<table class="printTab">
	<thead>
			<tr>
				<th rowspan="2" align="center" width="160">MARKS</th>
				<th colspan="3" align="center">DESCRIPTION</th>
				<th align="center">CUBAGE</th>
				<th align="center">G.W.</th>
				<th align="center" width="120">QUANTITY</th>
			</tr>
			<tr>
				<th align="center">PRODUCTS NAME</th>
				<th align="center">CARTON NUMBER</th>
				<th align="center">CARTON SIZE(cm)</th>
				<th align="center">(M³)</th>
				<th align="center">(KG)</th>
				<th align="center">(PCS)</th>
			</tr>
	</thead>
	<tbody>
		{$itemtr}
	</tbody>
</table>
<table class="printNoborderTab">
		<tr>
			<td align="center">If you have any questions concerning this invoice, Please contact: Tom Ding,  86-755-83911841,  tom@li-ion-battery.com</td>
		<tr>
		<tr>
			<td align="center">THANK YOU FOR YOUR BUSINESS!</td>
		</tr>
</table>
EOF;
		
		$t = date('F j, Y, H:i:s', TIMENOW);
		$footer=<<<EOD
			<div class="right">Page Created On: {$t}</div>
		New Wisdom Investment Limited
EOD;
		$this->kclass->page['pageTitle']='PACKING LIST - '.$invoice['invoiceno'];
		$this->kclass->page['title'] = 'PACKING LIST';
		$this->kclass->page['caption'] = '<span class="middle bold">Packinglist No.: '.$invoice['invoiceno'].'</span>'.$this->kclass->iif($invoice['customerNo']!='', ' (Cust. Order No.: '.$invoice['customerNo'].')', '').'<br>Created: '.date('Y-m-d', $invoice['created']).'. Modified: '.date('Y-m-d', $invoice['modified']);
		$this->kclass->page['main']=$body;
		$this->kclass->page['footer']=$footer;
	}
	
	// 
	function mmlist(){
$body=<<<EOF
OK
EOF;
		$this->kclass->page['title'] .= '打印模块';
		$this->kclass->page['main'] = $body;
	}
	
	function sample(){
		if(!$this->kclass->input['sampleid'] > 0)$this->kclass->boinkIt('/s.php?module=sample');
		$sample = $this->kclass->DB->queryFirst("
			SELECT `sample`.*,
				customer.title AS customerTitle, customer.address AS customerAddress, customer.linkman AS customerLinkman, customer.position AS customerPosition, customer.telephone AS customerTelephone, customer.fax AS customerFax,
				supplier.title AS supplierTitle, supplier.address AS supplierAddress, supplier.linkman AS supplierLinkman, supplier.position AS supplierPosition, supplier.telephone AS supplierTelephone, supplier.fax AS supplierFax,
				shipmethod.title AS shipmethodTitle, shipmethod.entitle AS shipmethodEntitle,
				currency.title AS currencyTitle, currency.symbol AS currencySymbol, currency.remark AS currencyRemark,
				m.username AS mname, c.username AS cname, d.username AS director, t.username AS tracker
			FROM `sample`
			LEFT JOIN customer ON (customer.customerid=`sample`.customerid)
			LEFT JOIN supplier ON (supplier.supplierid=`sample`.supplierid)
			LEFT JOIN currency ON (currency.currencyid=`sample`.currencyid)
			LEFT JOIN shipmethod ON (shipmethod.shipmethodid=`sample`.shipmethodid)
			LEFT JOIN `user` AS m ON (m.userid=sample.modifier)
			LEFT JOIN `user` AS c ON (c.userid=sample.creator)
			LEFT JOIN `user` AS d ON (d.userid=sample.director)
			LEFT JOIN `user` AS t ON (t.userid=sample.tracker)
			WHERE `sample`.killed=0 AND sampleid='".$this->kclass->input['sampleid']."'
		");
		if(!$sample)	$this->kclass->boinkIt('/s.php?module=sample&action=find');
		if($sample['modifier']>0){
			$modified = '，'.$order['mname'].' 于 '.date('y-m-d H:i:s', $order['modified']).' 最后修改';
		}
		$created = date('y-m-d H:i:s', $sample['created']);
		$shipmentDate=date('Y-m-d', $sample['shipmentDate']);
		$item = $this->kclass->relatedPrintSampleProductItem(array('sampleid'=>$sample['sampleid'], 'title'=>'客户'));
		//$fee = $this->kclass->relatedFee(array('orderid'=>$order['orderid']));
		$nowTime = date('F j, Y, H:i:s', TIMENOW);
		$nowDate = date('Y-m-d', TIMENOW);
		$freight = $this->kclass->DB->queryFirst("SELECT * FROM `freight` WHERE module='sample' AND mid='".$sample['sampleid']."'");
		$this->kclass->page['main']=<<<EOF
<table class="printNoborderTab">
<tbody>
<tr class="odd">
	<td width="135">订　单 编　号：</td>
	<td width="530"><input type="text" class="underlineInput" value="{$sample['sampleno']}"></td>
	<td width="135">制&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;单&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;人：</td>
	<td><input type="text" class="underlineInput" value="{$sample['tracker']}"></td>
</tr>
<tr class="odd">
	<td>客&nbsp;&nbsp;户&nbsp;&nbsp;&nbsp;名&nbsp;&nbsp;称：</td>
	<td><input type="text" class="underlineInput" value="{$sample['customerTitle']}"></td>
	<td>业&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;务&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;员：</td>
	<td><input type="text" class="underlineInput" value="{$sample['director']}"></td>
</tr>
<tr class="odd">
	<td>发&nbsp;&nbsp;货&nbsp;&nbsp;&nbsp;方&nbsp;&nbsp;式：</td>
	<td><input type="text" class="underlineInput" value="{$sample['shipmethodTitle']}"></td>
	<td>制&nbsp;&nbsp;单&nbsp;&nbsp;&nbsp;日&nbsp;&nbsp;期：</td>
	<td><input type="text" class="underlineInput" value="{$nowDate}"></span></td>
</tr>
<tr class="odd">
	<td width="70">计划完成日期：</td>
	<td><input type="text" class="underlineInput" value=""></td>
	<td>实际完成日期：</td>
	<td><input type="text" class="underlineInput" value=""></td>
</tr>
<tr class="odd">
	<td width="70">计划发货日期：</td>
	<td><input type="text" class="underlineInput" value=""></td>
	<td>实际发货日期：</td>
	<td><input type="text" class="underlineInput" value=""></td>
</tr>
<!--
<tr class="odd">
	<td width="70">订单号：</td>
	<td>{$order['customerNo']}</td>
	<td width="70" title="Shipment Method">运输方式：</td>
	<td width="494" class="middle">{$order['shipmethodTitle']} <span class="gray">{$order['shipmethodEntitle']}</span></td>
</tr>
<tr class="odd">
	<td>名　称：</td>
	<td><a href="/s.php?module=customer&action=view&customerid={$order['customerid']}" target="_blank">{$order['customerTitle']}</a></td>
	<td width="70" title="Payment Term">支付条款：</td>
	<td class="middle">{$order['paymenttermTitle']} <span class="gray">{$order['paymenttermEntitle']}</span></td>
</tr>
<tr class="odd">
	<td>地　址：</td>
	<td>{$order['customerAddress']}</td>
	<td title="Port of Loading">装货地点：</td>
	<td class="middle">{$order['loadingCountryTitle']} {$order['loadingTitle']} <span class="gray">{$order['loadingCountryTitle']} {$order['loadingTitle']}</span></td>
</tr>
<tr class="odd">
	<td>联系人：</td>
	<td>{$order['customerLinkman']} <span class="gray">{$order['customerPosition']}</span></td>
	<td title="Delivery Term">交货条款：</td>
	<td class="middle">{$order['deliverytermTitle']} <span class="gray">{$order['deliverytermEntitle']}</span></td>
</tr>
<tr class="odd">
	<td>电　话：</td>
	<td>{$order['customerTelephone']}</td>
	<td title="Port of Discharge">卸货地点：</td>
	<td class="middle">{$order['dischargeCountryTitle']} {$order['dischargeTitle']} <span class="gray">{$order['dischargeCountryEntitle']} {$order['dischargeEntitle']}</span></td>
</tr>
<tr class="odd">
	<td>传　真：</td>
	<td>{$order['customerFax']}</td>
	<td title="Shipment Date">交货日期：</td>
	<td class="middle"><span class="bold darkred">{$shipmentDate}</span></td>
</tr>
</tbody>
</table>
	
<table class="hundred">
<tbody>
<tr>
	<td width="70" valign="top" title="Labeling">标签说明：</td>
	<td width="494" class="middle">{$order['labeling']}</td>
	<td width="70" valign="top" title="Packing">包装说明：</td>
	<td class="middle">{$order['packing']}</td>
</tr>
<tr>
	<td valign="top" title="Remark">备　　注：</td>
	<td class="middle">{$order['remark']}</td>
	<td valign="top" title="Special">特别说明：</td>
	<td class="middle">{$order['special']}</td>
</tr>
</tbody>
</table>
-->
	
<table class="printTab">
<tbody>
<!--
<tr>
	<td colspan="12">订单明细 (Item Information)</td>
</tr>
-->
{$item}
<tr>
	<td colspan="12" height="60" valign="top">
	包装要求：<br>1._____ 常规包装（具体包装方法参见《包装作业要求》指导书）；<br>
	 		 2._____ 特殊包装（具体注明包装方式）。
	</td>
</tr>
<tr>
	<td colspan="12" height="90" valign="top">
	(特殊包装详细)：
	</td>
</tr>
<tr>
	<td colspan="12" height="90" valign="top">
	备注：
	</td>
</tr>
</tbody>
</table>

<table class="printNoborderTab">
<tbody>
<tr class="odd">
	<td width="130">货&nbsp;&nbsp;运&nbsp;&nbsp;单&nbsp;&nbsp;号：</td>
	<td width="550"><input type="text" class="underlineInput" value="{$freight['expressNo']}"></td>
	<td width="130"></td>
	<td width=""></td>
</tr>
<tr class="odd">
	<td width="130">箱&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;数：</td>
	<td width="550"><input type="text" class="underlineInput" value="{$freight['carton']}"></td>
	<td width="130">重&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;量：</td>
	<td width=""><input type="text" class="underlineInput" value="{$freight['weight']}"></span></td>
</tr>
<tr class="odd">
	<td width="130">质&nbsp;&nbsp;&nbsp;检&nbsp;&nbsp;OQC：</td>
	<td width="550"><input type="text" class="underlineInput" value="{$freight['oqc']}"></td>
	<td width="130">发&nbsp;&nbsp;货&nbsp;&nbsp;确&nbsp;&nbsp;认：</td>
	<td width=""><input type="text" class="underlineInput" value="{$freight['shippingVerify']}"></span></td>
</tr>
<tr class="odd">
	<td width="130">提货司机姓名：</td>
	<td width="550"><input type="text" class="underlineInput" value="{$freight['driverName']}"></td>
	<td width="130">司&nbsp;&nbsp;机&nbsp;&nbsp;电&nbsp;&nbsp;话：</td>
	<td width=""><input type="text" class="underlineInput" value="{$freight['driverMobile']}"></span></td>
</tr>
<tr class="odd">
	<td width="130">车&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;牌&nbsp;&nbsp;&nbsp;&nbsp;号：</td>
	<td width="550"><input type="text" class="underlineInput" value="{$freight['carID']}"></td>
	<td width="130">司机&nbsp;身&nbsp;份&nbsp;号：</td>
	<td width=""><input type="text" class="underlineInput" value="{$freight['driverID']}"></span></td>
</tr>
<tr class="odd">
	<td width="130">货&nbsp;&nbsp;运&nbsp;&nbsp;公&nbsp;&nbsp;司：</td>
	<td width="550"><input type="text" class="underlineInput" value="{$freight['freightCompany']}"></td>
	<td width="130">司&nbsp;&nbsp;机&nbsp;&nbsp;确&nbsp;&nbsp;认：</td>
	<td width=""><input type="text" class="underlineInput" value="{$freight['driverVerify']}"></span></td>
</tr>
</tbody>
</table>



<!--
<table class="hundred">
<tbody>
<tr class="odd">
	<td width="70" valign="top">Buyer：</td>
	<td>{$order['customerTitle']}</td>
	<td width="70" valign="top">Seller：</td>
	<td>{$order['supplierTitle']}</td>
</tr>
<tr class="odd">
	<td width="70" valign="top">Date：</td>
	<td>{$nowTime}</td>
	<td width="70" valign="top">Date：</td>
	<td>{$nowTime}</td>
</tr>
</tbody>
</table>
-->


EOF;

		$t = date('F j, Y, H:i:s', TIMENOW);
		$footer=<<<EOD
			<div class="right">Page Created On: {$t}</div>
		New Wisdom Investment Limited
EOD;
		$this->kclass->page['pageTitle']='Sample - '.$sample['sampleno'];
		$this->kclass->page['title'] = 'Sample';
		$this->kclass->page['contact']='Address: 4-306, Xianglixincuen, Hongli West Road, Shenzhen, China<br>Tel: 86-755-83911841  Fax: 86-755-83906115  E-Mail: info@wisdom.hk';
		$this->kclass->page['caption'] = '<span class="middle bold">Sample No.: '.$sample['sampleno'].'</span>'.$this->kclass->iif($order['customerNo']!='', ' (Cust. Order No.: '.$order['customerNo'].')', '').'<br>Created: '.date('Y-m-d', $sample['created']).'. Modified: '.date('Y-m-d', $sample['modified']).' NW-QR-SL001 V1.01';
		$this->kclass->page['footer']=$footer;
	}

	function samplePL(){
		$orderList = '';
		$sample = $this->kclass->DB->queryFirst("
			SELECT * FROM `sample` WHERE sampleid=".$this->kclass->input['sampleid']."
		");
		
		$packinglist = $this->kclass->DB->queryFirst("
			SELECT * FROM `packinglist` WHERE module='sample' AND mid=".$this->kclass->input['sampleid']."
		");
		$items = $this->kclass->DB->query("
			SELECT item.*,
				productitem.attributevalue,
				productitem.mid AS productId,
				productitem.packingsizeid,
				productitem.packingsizeid1,
				productitem.perGrossWeight,
				brand.title AS brand,
				packing.title AS packing,
				unit.entitle AS unit
			FROM `item`
			LEFT JOIN `productitem` ON (productitem.productitemid=item.productitemid)
			LEFT JOIN packing ON (packing.packingid=item.packingid)
			LEFT JOIN brand ON (brand.brandid=item.brandid)
			LEFT JOIN unit ON (unit.unitid=item.unitid)
			WHERE `item`.killed=0 AND `item`.module='sample' AND `item`.mid='".$this->kclass->input['sampleid']."' AND item.productitemid>0
			ORDER BY itemid ASC, modified ASC
		");
		if($this->kclass->DB->numRows()){
			$i=1;
			$loopNum = 1;
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
						ORDER BY an.ordering ASC
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
						$selectStr .= '';
						for($n=0;$n<count($value[$val]);$n++){
							if($valueId[$val][$n]==$valueStr[$key]){
								$selectStr .= ''.$value[$val][$n].'';
							}
						}
						$selectStr .= '　';
					}
				}else{
					$selectStr .= '';
				}
		
				$orderList .= '<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').'">
					<td></td>
					<td>'.$product['title'].'： '.$selectStr.'</td>
					<td>'.$item['quantity'].' '.$item['unit'].'</td>
					<td>'.$item['cartonNo'].'</td>
				</tr>';
				/*
				 $productitem = $this->kclass->DB->queryFirst("SELECT * FROM `productitem` WHERE productitemid='".$item['productitemid']."'");
				if($productitem){
				$packingsize = $this->kclass->DB->queryFirst("SELECT * FROM `packingsize` WHERE packingsizeid='".$productitem['packingsize']."'");
				$packingsize1 = $this->kclass->DB->queryFirst("SELECT * FROM `packingsize` WHERE packingsizeid='".$productitem['packingsize1']."'");
				}
					
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
				*/
			}
			$orderList .= '<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').'">
				<td class="bold" valign="top" height="100">包装信息</td>
				<td colspan="3"  valign="top" >'.$packinglist['packingNote'].'</td>
			</tr>';
		}
		$sampleDate = date('Y-m-d',$sample['created']);
		$body = <<<EOF
<form action="/s.php?module=packinglist&action=insertPacking" name="invoice" method="post">
<input type="hidden" name="module" value="packinglist">
<input type="hidden" name="action" value="insertPacking">
<input type="hidden" name="orderid" value="{$this->kclass->input['orderid']}">
<table class="printNoborderTab">
<tbody>
<tr class="odd">
	<td width="115" align="right">订单编号：</td>
	<td width=""><input type="text" class="underlineInput150" value="{$sample['sampleno']}"></td>
	<td width="115" align="right">计划完成日期：</td>
	<td><input type="text" class="underlineInput150" value=""></td>
	<td width="115" align="right">计划发货日期：</td>
	<td><input type="text" class="underlineInput150" value=""></td>
</tr>
<tr class="odd">
	<td width="115" align="right">下单日期：</td>
	<td width=""><input type="text" class="underlineInput150" value="{$sampleDate}"></td>
	<td width="115" align="right">实际完成日期：</td>
	<td><input type="text" class="underlineInput150" value=""></td>
	<td width="115" align="right">实际发货日期：</td>
	<td><input type="text" class="underlineInput150" value=""></td>
</tr>
</tbody>
</table>
<table class="printTab">
<thead>
<tr><th width="200">箱号</th><th>装箱内容</th><th width="60">数量</th><th width="200">序列号</th></tr>
</thead>
<tbody>
{$orderList}
</tbody>
</table>
</form>
EOF;
		$footer=<<<EOD
			<div class="right">Tel：0755-28990261　　Fax：0755-28998861</div>
		公司地址：深圳市龙岗区宝荷路嶂背大道9号1楼
EOD;
		$this->kclass->page['title'] = '装 箱 单';
		$this->kclass->page['contact']='深圳市中孚能电气设备有限公司';
		$this->kclass->page['caption'] = '记录编号：NW-QR-SL001　版本：V1.01';
		$this->kclass->page['main']=$body;
		$this->kclass->page['footer']=$footer;
	}
	
	function sampleEn(){
		if(!$this->kclass->input['sampleid'] > 0)$this->kclass->boinkIt('/s.php?module=sample');
		$sample = $this->kclass->DB->queryFirst("
			SELECT `sample`.*,
				attr.entitle AS attr,
				currency.title AS currencyTitle, currency.symbol AS currencySymbol, currency.remark AS currencyRemark,
				shipmethod.entitle AS shipmethod,
				m.username AS mname, c.username AS cname
			FROM `sample`
			LEFT JOIN `attr` ON (attr.attrid=sample.attrid)
			LEFT JOIN `currency` ON (currency.currencyid=sample.currencyid)
			LEFT JOIN `shipmethod` ON (shipmethod.shipmethodid=sample.shipmethodid)
			LEFT JOIN `user` AS m ON (m.userid=sample.modifier)
			LEFT JOIN `user` AS c ON (c.userid=sample.creator)
			WHERE `sample`.sampleid='".$this->kclass->input['sampleid']."'
		");
		if(!$sample)	$this->kclass->boinkIt('/s.php?module=sample');
		if($sample['typeid']!=''){
			$stypes = $this->kclass->DB->query("SELECT typeid, title,entitle FROM `sampletype` WHERE typeid IN (".$sample['typeid'].")");
			if($this->kclass->DB->numRows()){
				while($stype = $this->kclass->DB->fetchArray($stypes)){
					$sampleType .= '<span title="('.$stype['title'].') '.$stype['remark'].'" style="padding-right:12px">'.$stype['entitle'].'</span>';
				}
			}
		}
		if($sample['customerid']>0){
			$customer = $this->kclass->DB->queryFirst("SELECT * FROM `customer` WHERE customerid='".$sample['customerid']."'");
		}
		if($sample['supplierid']>0){
			$supplier = $this->kclass->DB->queryFirst("SELECT * FROM `supplier` WHERE supplierid='".$sample['supplierid']."'");
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
			$deliveryDate=' '.date('Y-m-d', $sample['deliveryDate']).'';
		}
		if($sample['deliveryFee']>0){
			$deliveryFee=' (<span class="small">'.$sample['currency'].'</span>'.$sample['deliveryFee'].')';
		}
		$created = date('y-m-d H:i:s', $sample['created']);
	
		$total=0;
		$item = $this->kclass->relatedPrintSampleItem(array('sampleid'=>$sample['sampleid']));
	
		$items = $this->kclass->relatedPrintSampleProductItem(array('sampleid'=>$sample['sampleid']));
		$total = $item['total']+$items['total'];
		$fee = $this->kclass->relatedPrintFee(array('sampleid'=>$sample['sampleid']));
		$total += $fee['total'];
		$discounts = $this->kclass->DB->query("SELECT * FROM `discount` WHERE killed=0 AND module='sample' AND mid='".$sample['sampleid']."'");
		if($this->kclass->DB->numRows()){
			while($discount=$this->kclass->DB->fetchArray($discounts)){
				if($item['total']>0 || $items['total']>0){
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
				$discounttr .= '<tr class="odd">
				<td>'.$discount['reason'].'</td>
				<td align="right">'.$discount['addition'].''.$discount['discount'].''.$this->kclass->iif($discount['sign']=='%', $discount['sign'], '').'</td>
				<td align="right">'.$discount['addition'].number_format($amount, 2).'</td>
				<td align="right">'.$sample['currencySymbol'].number_format($total, 2).'</td>
				</tr>';
			}
			$discounttr = '
				<table class="hundred">
				<tbody>
					<tr>
						<td colspan="4" class="bold middle">Discount Information</td>
					</tr>
						<tr><td title="Reason">Discount Reason</td><td title="Discount" align="right">Discount</td><td title="Amount" align="right">Number of discount</td><td title="Amount" align="right">Amount</td></tr>'.$discounttr.'<tr><td colspan="12" align="right">Subtotal：<span class="bold">'.$sample['currencyTitle'].' '.$sample['currencySymbol'].number_format($total, 2).'</span></td></tr>
				</tbody>
				</table>';
		}else{
			$discounttr = '';
		}
		$total = number_format($total, 2);
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
		$body = <<<EOF
	
<table cellspacing="10" width="100%">
<tr class="odd">
	<td colspan="2" align="center" width="300" class="bold middle">Vendor</td>
	<td colspan="2" align="center" class="bold middle">Ship To</td>
</tr>
<tr class="odd">
	<td colspan="2" valign="top">
		Name：{$supplier['entitle']}<br/>
		Address：{$supplier['enaddress']}<br/>
		Telephone：{$supplier['telephone']}<br/>
		Fax：{$supplier['fax']}
	</td>
	<td colspan="2" valign="top">
		Name：{$customer['title']}<br/>
		Address：{$customer['address']}<br/>
		Telephone：{$customer['telephone']}<br/>
		Fax：{$customer['fax']}
	</td>
</tr>
<tr class="odd">
	<td align="center" width="25%">Sample Type：</td>
	<td align="center" width="25%">Express Method：</td>
	<td align="center" width="25%">Sample Category：</td>
	<td align="center" width="25%">Sample Fee：</td>
</tr>
<tr class="odd">
	<td align="center">{$sampleType}</td>
	<td align="center">{$sample['shipmethod']}{$deliveryFee}{$deliveryDate}</td>
	<td align="center">{$sample['attr']}</td>
	<td align="center">{$sample['currencyTitle']} {$sample['currencySymbol']}{$sample['amount']}</td>
</tr>
<tr>
	<td colspan="2" align="center" width="50%">Description</td>
	<td colspan="2" align="center" width="50%">Remark</td>
</tr>
<tr>
	<td colspan="2" align="center">{$sample['description']}</td>
	<td colspan="2" align="center">{$sample['remark']}</td>
</tr>
</table>
	
{$item['tr']}
	
{$items['tr']}
	
{$fee['tr']}
	
{$discounttr}
	
<table class="hundred">
<tbody>
<tr><td align="right" class="middle bold">Total Amount：<span>{$sample['currencyTitle']} {$sample['currencySymbol']}{$total}</span></td></tr>
</tbody>
</table>
{$eventtbl}
EOF;
	
	$t = date('F j, Y, H:i:s', TIMENOW);
	$footer=<<<EOD
	<div class="right">Page Created On: {$t}</div>
New Wisdom Investment Limited
EOD;
	$this->kclass->page['pageTitle']='SAMPLE - '.$sample['sampleno'];
		$this->kclass->page['title'] = 'Sample';
		$this->kclass->page['caption'] = '<span class="middle bold">Sample No.: '.$sample['sampleno'].'</span>'.$this->kclass->iif($sample['customerNo']!='', ' (Cust. Order No.: '.$sample['customerNo'].')', '').'<br>Created: '.date('Y-m-d', $sample['created']).'. Modified: '.date('Y-m-d', $sample['modified']);
		$this->kclass->page['main']=$body;
		$this->kclass->page['footer']=$footer;
	}
	
	// 
	function order(){
		if(!$this->kclass->input['orderid'] > 0)$this->kclass->boinkIt('/s.php?module=order');

		$order = $this->kclass->DB->queryFirst("
			SELECT `order`.*, 
				route.title AS routeTitle, route.caption AS routeCaption, 
				customer.title AS customerTitle, customer.address AS customerAddress, customer.linkman AS customerLinkman, customer.position AS customerPosition, customer.telephone AS customerTelephone, customer.fax AS customerFax, 
				supplier.title AS supplierTitle, supplier.address AS supplierAddress, supplier.linkman AS supplierLinkman, supplier.position AS supplierPosition, supplier.telephone AS supplierTelephone, supplier.fax AS supplierFax, 
				shipmethod.title AS shipmethodTitle, shipmethod.entitle AS shipmethodEntitle, 
				loading.title AS loadingTitle, loading.entitle AS loadingEntitle, 
				loadingCountry.country AS loadingCountryTitle, loadingCountry.encountry AS loadingCountryEntitle, 
				discharge.title AS dischargeTitle, discharge.entitle AS dischargeEntitle, 
				dischargeCountry.country AS dischargeCountryTitle, dischargeCountry.encountry AS dischargeCountryEntitle, 
				paymentterm.title AS paymenttermTitle, paymentterm.entitle AS paymenttermEntitle, 
				deliveryterm.title AS deliverytermTitle, deliveryterm.entitle AS deliverytermEntitle, 
				currency.title AS currencyTitle, currency.symbol AS currencySymbol, currency.remark AS currencyRemark, 
				m.username AS mname, c.username AS cname
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
			WHERE `order`.killed=0 AND orderid='".$this->kclass->input['orderid']."'
		");
		if(!$order)	$this->kclass->boinkIt('/s.php?module=order&action=find');
		if($order['modifier']>0){
			$modified = '，'.$order['mname'].' 于 '.date('y-m-d H:i:s', $order['modified']).' 最后修改';
		}
		$created = date('y-m-d H:i:s', $order['created']);
		$attach = $this->kclass->getAttachs(array('module'=>'order', 'mid'=>$order['orderid']));
		if($attach!= false){
			$attachs = '<div class="clear">'.$attach.'</div>';
		}
		$shipmentDate=date('Y-m-d', $order['shipmentDate']);
		$item = $this->kclass->relatedOrderItem(array('orderid'=>$order['orderid'], 'title'=>'客户'));
		$fee = $this->kclass->relatedFee(array('orderid'=>$order['orderid']));
$this->kclass->page['main']=<<<EOF
<table>
<tbody>
<tr>
	<td>Customer: </td><td>{$order['customerTitle']}</td>
	<td>Payment Terms: </td><td>{$order['aaa']}</td>
	<td>Payment: </td><td>{$order['aaa']}</td>
</tr>
<tr>
	<td>Payment: </td><td>{$order['aaa']}</td>
	<td>Payment: </td><td>{$order['aaa']}</td>
	<td>Payment: </td><td>{$order['aaa']}</td>
</tr>
<tr>
	<td>Payment: </td><td>{$order['aaa']}</td>
	<td>Payment: </td><td>{$order['aaa']}</td>
	<td>Payment: </td><td>{$order['aaa']}</td>
</tr>
</tbody>
</table>
EOF;

		$this->kclass->page['pageTitle']='ORDER - '.$order['orderno'];
		$this->kclass->page['title'] = 'Proforma Invoice';
		$this->kclass->page['caption'] = '<span class="middle bold">Order No.: '.$order['orderno'].'</span>'.$this->kclass->iif($order['customerNo']!='', ' (Cust. Order No.: '.$order['customerNo'].')', '').'<br>Created: '.date('Y-m-d', $order['created']).'. Modified: '.date('Y-m-d', $order['modified']);
	}

	//
	function inventory(){
		if(!$this->kclass->input['inventoryid'] > 0)$this->kclass->boinkIt('/s.php?module=inventory');
	
		$inventory=$this->kclass->DB->queryFirst("
				SELECT i.*,
				c.parentlist,c.title AS category,
				u.username AS creator,
				u.username AS modifier
				FROM inventory AS i
				LEFT JOIN category AS c ON (i.categoryid=c.categoryid)
				LEFT JOIN user AS u ON (u.userid=i.creator)
				LEFT JOIN user AS us ON (us.userid=i.modifier)
				WHERE i.killed=0 AND i.inventoryid='".$this->kclass->input['inventoryid']."'
				");
		if($inventory){
			$categoryid=explode(',',$inventory['parentlist']);
			foreach($categoryid as $key=>$value){//获得物资分类
				if($key!=0){
					$category=$this->kclass->DB->queryFirst("SELECT title FROM category WHERE categoryid='".$value."' LIMIT 0,1");
					if($category['title']){
						$inventory['category']=$category['title'].'-'.$inventory['category'];
					}
				}
			}
			$preInventory=$this->kclass->DB->queryFirst("
					SELECT dateline
					FROM inventory
					WHERE killed=0 AND categoryid='".$inventory['categoryid']."' AND inventoryid<>'".$inventory['inventoryid']."'
					ORDER BY created DESC
					LIMIT 0,1
					");
			if($inventory['inventoryType']=='choose'){
				$inventoryType='抽盘';
			}elseif($inventory['inventoryType']=='must'){
				$inventoryType='必盘';
			}
			//get material list for inventory
			$items=$this->kclass->DB->query("
					SELECT i.*,
					m.materialid,m.materialno,m.title,m.standard,m.uptolerance,m.belowtolerance
					FROM inventoryitem AS i
					LEFT JOIN material AS m ON (m.materialid=i.materialid)
					WHERE i.killed=0 AND inventoryid='".$this->kclass->input['inventoryid']."'
					ORDER BY created ASC
					");
			if($this->kclass->DB->numRows()){
				$body.='<table class="hundred"><thead><tr class="center even"><th width="50">物资编号</th><th>物资名称</th><th>规格</th><th>账面数量</th><th>盘点数</th><th>差数</th><th>误差</th><th width="100">允差率</th><th>备注</th><th>原因分析</th></tr></thead>';
				while($item=$this->kclass->DB->fetchArray($items)){
					$tolerance=$this->kclass->iif($item['uptolerance']==0 AND $item['belowtolerance']==0,0,'>'.$item['belowtolerance'].'%,<'.$item['uptolerance'].'%');
					$body.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').'">
					<td>'.$item['materialno'].'</td>
					<td>'.$item['title'].'</td>
					<td>'.$item['standard'].'</td>
					<td>'.$item['countQuantity'].'</td>
					<td>'.$item['quantity'].'</td>
					<td>'.$item['difference'].'</td>
					<td>'.$item['errorrate'].'‰</td>
					<td>'.$tolerance.'</td>
					<td>'.$item['remark'].'</td>
					<td>'.$item['reason'].'</td>
					</tr>';
				}
				$body.='</tbody></table>';
			}
		}
		$this->kclass->page['pageTitle']='盘点 - '.$inventory['title'];
		$this->kclass->page['title'] = '盘点';
		$this->kclass->page['caption'] = '<div class="right">标　题 : '.$inventory['title'].'（'.$inventoryType.'）　　盘点物资分类：'.$inventory['category'].'<br>盘点时间：'.date('Y 年 m 月 d 日',$inventory['created']).'　盘点人：'.$inventory['inventorier'].'　复核人：'.$inventory['checker'].'</div>';
		$this->kclass->page['main']=$body;
	}
	// 
	function inboundApply(){
		if(!$this->kclass->input['inboundid'] > 0)$this->kclass->boinkIt('/s.php?module=inbound');
		
		$inbound=$this->kclass->DB->queryFirst("
			SELECT 
				i.inboundid,i.inboundno,i.typeid,i.applicant,i.arrivalno,i.purpose,i.arrivalTime,i.remark,
					i.verifyRemark,i.ifVerify,i.verified,i.modified,i.created,
				o.porderid,o.orderno,
				d.title AS department,
				s.supplierid,s.title AS supplier,
				u.username AS creator,
				us.username AS modifier,
				ur.username AS verifier
			FROM inbound AS i
			LEFT JOIN `porder` AS o ON (o.porderid=i.porderid)
			LEFT JOIN `department` AS d ON (d.departmentid=i.departmentid)
			LEFT JOIN `supplier` AS s ON (s.supplierid=i.supplierid)
			LEFT JOIN `user` AS u ON (u.userid=i.creator)
			LEFT JOIN `user` AS us ON (us.userid=i.modifier)
			LEFT JOIN `user` AS ur ON (ur.userid=i.verifier)
			WHERE i.killed=0 AND inboundid='".$this->kclass->input['inboundid']."'
			LIMIT 0,1
		");
		if($inbound){
			if($inbound['typeid']==1){
				$title='进　　　　仓　　　　单';
				$notitle='生产单号：';
				$no='';
				$footer='<div><span style="width:240px;float:left">验收：</span><span style="width:240px;float:left">复核：</span><span style="width:240px;float:left">检验：</span><span style="width:240px;float:left">制单：</span></div>';
			}elseif($inbound['typeid']==2 OR $inbound['typeid']==5){
				$title='退　　　　料　　　　单';
				$notitle='生产单号：';
				$no='';
				$footer='<div><span style="width:150px;float:left">主管：</span><span style="width:150px;float:left">会计：</span><span style="width:150px;float:left">记账：</span><span style="width:150px;float:left">收料：</span><span style="width:150px;float:left">退料：</span><span style="width:150px;float:left">制单：</span></div>';
			}elseif($inbound['typeid']==3 OR $inbound['typeid']==6){
				$title='入　　　　库　　　　单';
				$notitle='生产单号：';
				$no='';
				$footer='<div><span style="width:240px;float:left">验收：</span><span style="width:240px;float:left">复核：</span><span style="width:240px;float:left">检验：</span><span style="width:240px;float:left">制单：</span></div>';
			}elseif($inbound['typeid']==4){
				$title='收　　　　料　　　　单';
				$notitle='采购单号：';
				$no=$inbound['orderno'];
				$invoice=$this->kclass->DB->queryFirst("SELECT invoiceno FROM pinvoice WHERE killed=0 AND ".$inbound['porderid']." IN (porderid)");
				$invoiceinfo='<span class="right">发票编号：'.$invoice['invoiceno'].'</span>';
				$arrivalTime='<span class="right">　　　年　 　月　　日 收到</span>';
				$footer='<div><span style="width:240px;float:left">记账：</span><span style="width:240px;float:left">复核：</span><span style="width:240px;float:left">检验：</span><span style="width:240px;float:left">制单：</span></div>';
			}

			$body='<div style="margin-top:3px"><span>部　　门：</span><span>'.$inbound['department'].'</span><span class="right">'.date('Y 年 m 月 d 日',$inbound['created']).'</span></div><div><span>'.$notitle.'</span><span>'.$no.'</span>'.$invoiceinfo.'</div><div><span>供 应 商：</span><span>'.$inbound['supplier'].'</span>'.$arrivalTime.'</div>';
			//获得物资列表
			$items=$this->kclass->DB->query("
				SELECT ii.*,
					bt.batchno,
					bc.barcode,
					m.materialno,m.title AS material,m.standard,
					mv.title AS version,
					p.pmaterialid,
					pi.productitemid,pi.qualified,pi.disqualified,
					u.title AS unit
				FROM inbounditem AS ii
				LEFT JOIN batch AS bt ON (ii.batchid=bt.batchid)
				LEFT JOIN barcode AS bc ON (bc.barcodeid=ii.barcodeid)
				LEFT JOIN material AS m ON (m.materialid=ii.materialid)
				LEFT JOIN pmaterial AS p ON (p.materialid=ii.materialid)
				LEFT JOIN materialversion AS mv ON (mv.versionid=ii.versionid)
				LEFT JOIN preinbounditem AS pi ON (pi.itemid=ii.preitemid)
				LEFT JOIN unit AS u ON (u.unitid=m.unitid)
				WHERE ii.killed=0 AND ii.inboundid='".$this->kclass->input['inboundid']."'
				ORDER BY ii.itemid ASC
			");
			if($this->kclass->DB->numRows()){
				$i=0;
				$body.='<table class="left" style="width:970px;margin:0"><tbody>
<tr class="center small"><td rowspan=2 width="30">编号</td><td rowspan=2 width="60">批次</td><td rowspan=2>品名</td><td rowspan=2>规格</td><td rowspan=2 width="30">单位</td><td rowspan=2 width="40">良品数</td><td rowspan=2 width="50">不良品数</td><td rowspan=2 style="border-right:double" width="30">单价</td><td colspan=9 align="center">金额</td></tr><tr class="small"><td style="width:12px">百</td><td style="width:12px">十</td><td style="width:12px">万</td><td style="width:12px">千</td><td style="width:12px">百</td><td style="width:12px">十</td><td style="width:12px">元</td><td style="width:12px">角</td><td style="width:12px">分</td></tr>';
				while($item=$this->kclass->DB->fetchArray($items)){
					$am=$this->kclass->getMaterialUrl(array('materialno'=>$item['materialno'],'productitemid'=>$item['productitemid'],'pmaterialid'=>$item['pmaterialid']));
					$body.='<tr class="small"><td>'.$item['materialno'].'</td>
						<td>'.$item['batchno'].'</td>
						<td>'.$item['material'].'</td>
						<td>'.$item['standard'].' '.$am['mvalue'].'</td>
						<td>'.$item['unit'].'</td>
						<td>'.$item['qualified'].'</td>
						<td>'.$item['disqualified'].'</td>
						<td style="border-right:double"></td>
						<td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>';
					$i++;
				}
				for($j=0;$j<15-$i;$j++){
					$body.='<tr height="20"><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td style="border-right:double"></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>';
				}
				$body.='<tr><td>备注</td><td colspan=6>'.$outbound['remark'].'</td>
				<td>合计</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr></tbody>';
			}
			$body.='</table><div class="left" style="width:11px;font-size:12px;line-height:17px;margin:28px 0 0 5px">第一联存根　第二联交财务　第三联交保管</div>'.$footer;
		}
		$this->kclass->page['caption']='<div class="title" style="padding:10px 0 5px"><span style="width:220px;border-bottom:double;margin-left:150px;font-size:24px">'.$title.'</span><span class="right" style="border-bottom:double;font-size:20px">'.$inbound['inboundno'].'</span></div>';
		$this->kclass->page['title'] .= '打印入库单-'.$inbound['inboundno'];
		$this->kclass->page['main']=$body;
	}
	// 
	function outboundApply(){
		if(!$this->kclass->input['outboundid'] > 0)$this->kclass->boinkIt('/s.php?module=outbound');
		$outbound=$this->kclass->DB->queryFirst("
			SELECT i.outboundid,i.outboundno,i.typeid,i.created,i.purpose,
				o.orderno,
				s.title AS supplier,
				d.title AS department,
				u.username AS creator,
				us.username AS modifier,
				ur.username AS verifier
			FROM outbound AS i
			LEFT JOIN `order` AS o ON (o.orderid=i.orderid)
			LEFT JOIN `supplier` AS s ON (s.supplierid=i.supplierid)
			LEFT JOIN `department` AS d ON (d.departmentid=i.departmentid)
			LEFT JOIN `user` AS u ON (u.userid=i.creator)
			LEFT JOIN `user` AS us ON (us.userid=i.modifier)
			LEFT JOIN `user` AS ur ON (ur.userid=i.verifier)
			WHERE i.killed=0 AND outboundid='".$this->kclass->input['outboundid']."'
			LIMIT 0,1
		");
		if($outbound){
			if($outbound['typeid']==1 OR $outbound['typeid']==2){
				$title='领　　　　料　　　　单';
				$version='<div>NW-QR-PC014  V1.05</div>';
			}elseif($outbound['typeid']==3 OR $outbound['typeid']==8){
				$title='出　　　　库　　　　单';
				$version='<div>NW-QR-PC014  V1.05</div>';
			}elseif($outbound['typeid']==4){
				$title='外　　发　　加　　工　　单';
				$version='<div>NW-QR-PC016 V1.01</div>';
			}elseif($outbound['typeid']==5){
				$title='退　　　　货　　　　单';
				$version='<div>NW-QR-PC018 V1.01</div>';
			}elseif($outbound['typeid']==6 OR $outbound['typeid']==7){
				$title='补　　　　料　　　　单';
				$version='<div>NW-QR-PC014  V1.01</div>';
			}
			
			$body='<div class="middle" style="margin-top:3px;"><span>领料部门：</span><span>'.$outbound['department'].'</span></div><div class="middle"><span>生产单号：</span><span></span><span class="right">'.date('Y 年 m 月 d 日',$outbound['created']).'</span></div>';
			//获得物资列表
			$items=$this->kclass->DB->query("
				SELECT oi.*,
					ba.batchno,
					bc.barcode,
					m.materialno,m.title AS materialTitle,m.standard,
					p.pmaterialid,
					pi.productitemid,pi.remark AS piremark,
					u.title AS unit,
					mem.realname AS creator
				FROM outbounditem AS oi
				LEFT JOIN batch AS ba ON (ba.batchid=oi.batchid)
				LEFT JOIN barcode AS bc ON (bc.barcodeid=oi.barcodeid)
				LEFT JOIN material AS m ON (m.materialid=oi.materialid)
				LEFT JOIN pmaterial AS p ON (p.materialid=oi.materialid)
				LEFT JOIN preoutbounditem AS pi ON (pi.itemid=oi.preitemid)
				LEFT JOIN unit AS u ON (u.unitid=m.unitid)
				LEFT JOIN member AS mem ON (mem.userid=oi.creator)
				WHERE oi.killed=0 AND oi.outboundid='".$this->kclass->input['outboundid']."'
				ORDER BY oi.preitemid ASC
			");
			if($this->kclass->DB->numRows()){
				$i=0;
				$body.='<table class="left" style="width:970px;margin:0"><tbody><tr><td>用途</td><td colspan=8>'.$outbound['purpose'].'</td></tr>
<tr class="center normal"><td width="30">编号</td><td width="60">批次</td><td>品名</td><td>规格</td><td width="30">单位</td><td width="50">请领数量</td><td width="80">损耗数（率）</td><td width="50">实发数量</td><td>备注</td></tr>';
				while($item=$this->kclass->DB->fetchArray($items)){
					$am=$this->kclass->getMaterialUrl(array('pmaterialid'=>$item['pmaterialid'],'productitemid'=>$item['productitemid'],'materialno'=>$item['materialno']));
					$body.='<tr class="middle"><td>'.$item['materialno'].'</td>
						<td>'.$item['batchno'].'</td>
						<td>'.$item['materialTitle'].'</td>
						<td>'.$item['standard'].'  '.$am['mvalue'].'</td>
						<td>'.$item['unit'].'</td>
						<td>'.$item['applyQuantity'].'</td>
						<td>'.$item['attritionRate'].'</td>
						<td>'.$item['outQuantity'].'</td>
						<td>'.$item['piremark'].'</td></tr>';
					$i++;
				}
				for($j=0;$j<18-$i;$j++){
					$body.='<tr height="20"><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>';
				}
				$body.='<tr><td>备注</td><td colspan=8>'.$outbound['remark'].'</td>
				</tr></tbody>';
			}
			$body.='</table><div class="left" style="width:11px;font-size:14px;line-height:17px;margin:50px 0 0 5px">第一联存根　第二联交财务　第三联交保管</div><div><span style="width:230px;float:left">会计：</span><span style="width:230px;float:left">发料：</span><span style="width:230px;float:left">审批：</span><span style="width:230px;float:left">领料：</span></div><div class="clear"></div>'.$version;
		}
		$this->kclass->page['caption']='<div class="title" style="padding:10px 0 5px"><span style="width:220px;border-bottom:double;margin-left:150px;font-size:24px">'.$title.'</span><span class="right" style="border-bottom:double;font-size:20px">'.$outbound['outboundno'].'</span></div>';
		$this->kclass->page['title'] .= '打印出库单-'.$outbound['outboundno'];
		$this->kclass->page['main']=$body;
	}
	//
	function purchaseApply(){
		$apply=$this->kclass->DB->queryFirst("
			SELECT p.purchaseApplyid,p.applyno,p.typeid,p.applicant,p.ifVerify,p.purpose,p.remark,p.verifyRemark,p.verified,p.created,
			d.title AS department,
			ur.username AS verifier
			FROM purchaseapply AS p
			LEFT JOIN department AS d ON (d.departmentid=p.departmentid)
			LEFT JOIN user AS ur ON (ur.userid=p.verifier)
			WHERE p.purchaseApplyid='".$this->kclass->input['purchaseApplyid']."'
			LIMIT 0,1
		");
		if($apply){
			$apply['created']=date('Y-m-d',$apply['created']);
			$typeId=explode(',',$apply['typeid']);
			$types=$this->kclass->DB->query("SELECT typeid,module,title FROM purchaseType WHERE killed=0 ORDER BY created ASC");
			if($this->kclass->DB->numRows()){
				$i=1;
				while($type=$this->kclass->DB->fetchArray($types)){
					if($type['module']=='produce'){
						$chooserType1.='<input type="checkbox" ';
						if(in_array($type['typeid'],$typeId)){
							$chooserType1.='checked';
						}
						$chooserType1.=' name="typeid['.$i.']" value="'.$type['typeid'].'">'.$type['title'].'　';
					}elseif($type['module']=='unproduce'){
						$chooserType2.='<input type="checkbox" ';
						if(in_array($type['typeid'],$typeId)){
							$chooserType2.='checked';
						}
						$chooserType2.=' name="typeid['.$i.']" value="'.$type['typeid'].'">'.$type['title'].'　';
					}
					$i++;
				}
			}
			//申请的物资
			$items=$this->kclass->DB->query("
				SELECT p.itemid,p.module,p.mid,p.materialid,p.materialno AS newMaterialno,p.title AS newTitle,p.standard AS newStandard,p.quantity,p.dateline,p.remark,
				m.materialno,m.title,m.standard,
				u.title AS unit
				FROM preinbounditem AS p
				LEFT JOIN material AS m ON (m.materialid=p.materialid)
				LEFT JOIN unit AS u ON (u.unitid=p.unitid)
				WHERE p.module='purchaseApply' AND p.killed=0 AND p.mid='".$apply['purchaseApplyid']."'
				ORDER BY p.ordering ASC
			");
			if($this->kclass->DB->numRows()){
				$i=1;
				while($item=$this->kclass->DB->fetchArray($items)){
					$itemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').'" nohover>';
					if($item['materialid']==0){
						$itemtr.='<td>'.$item['newMaterialno'].'</td>
						<td>'.$item['newTitle'].'</td>
						<td>'.$item['newStandard'].'</td>';
					}else{
						$itemtr.='<td><a href="/s.php?module=material&action=view&materialid='.$item['materialid'].'">'.$item['materialno'].'</a></td>
						<td>'.$item['title'].'</td>
						<td>'.$item['standard'].'</td>';
					}
					$itemtr.='<td>'.$item['quantity'].$item['unit'].'</td><td>'.date('Y-m-d',$item['dateline']).'</td><td>'.$item['remark'].'</td></tr>';
					$i++;
				}
			}
			for($j=0;$j<13-$i;$j++){
				$itemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').'" style="height:32px;"><td></td><td></td><td></td><td></td><td></td><td></td></tr>';
			}
			$body=<<<EOF
<div class="title center">采购申请单</div>
<table class="hundred">
<thead><tr><th colspan="6" class="center">采购申请</th></tr></thead>
<tbody>
<tr class="even">
<td width="100" >部　　门：</td><td>{$apply['department']}</td>
<td width="100" >申 请 人：</td><td>{$apply['applicant']}</td>
<td width="100" >申请时间：</td><td>{$apply['created']}</td>
<tr class="even">
<td>采购类型：<span class="bold red">*</span></td><td colspan=5>生产类：{$chooserType1}　非生产类：{$chooserType2}</td>
</tr>
</tbody>
</table><table class="hundred" style="margin:-11px 0 0 0">
<tbody>
<tr class="center even" nohover>
<td>物资编号</td><td>物资名称</td><td>物资规格</td><td>数量/单位</td><td>需求日期</td><td>备注</td>
</tr>
{$itemtr}
</tbody>
</table>
<div style="width:984px;height:240px;border:1px solid #CCCCCC">
<div class="center bold" style="height:34px;border-bottom:1px solid #CCCCCC">采购审核</div>
<div style="height:180px">审核意见：</div>
<div><span class="right">审核人:　　　　　　　审核时间：　　　　　　　　</span></div>
</div>
<div style="width:984px;height:240px;border:1px solid #CCCCCC">
<div class="center bold" style="height:34px;border-bottom:1px solid #CCCCCC">采购批准</div>
<div style="height:180px">批准意见：</div>
<div><span class="right">批准人:　　　　　　　批准时间：　　　　　　　　</span></div>
</div>
EOF;
		}
		$this->kclass->page['pageTitle']='采购申请单 - '.$apply['applyno'];
		$this->kclass->page['title'] = '采购申请单';
		$this->kclass->page['caption'] = '<div class="right"><span class="right">No: '.$apply['applyno'].'</span><br>记录编号：NW-QR-PC001　版本：V1.01</div>';
		$this->kclass->page['main']=$body;
	}
	//
	function porder(){
		$order=$this->kclass->DB->queryFirst("
			SELECT o.porderid,o.supplierid,o.pinvoiceTypeid,o.orderno,o.type,o.purchaseType,o.shipmentDate,o.paymentDate,o.delivery,
					o.taxRate,o.consignee,o.consigneeTel,o.special,o.spare,o.ifVerify,o.verified,o.verifyRemark,o.remark,o.modified,o.created,o.killed,
			it.title AS invoiceType,
			s.title AS supplier,s.supplierno,s.address AS supplierAddress,
			s.linkman AS supplierLinkman,s.linkman2 AS supplierLinkman2,s.mobile AS supplierMobile,
			s.telephone AS supplierTelephone,s.email AS supplierEmail,s.fax AS supplierFax,
			cu.cntitle AS customer,cu.customerno,cu.address AS customerAddress,cu.linkman AS customerLinkman,cu.mobile AS customerMobile,
			cu.telephone AS customerTelephone,cu.email AS customerEmail,cu.fax AS customerFax,
			c.title AS currency,c.symbol AS encurrency,
			lo.title AS loading,lo.entitle AS enloading,
			dis.title AS discharge,dis.entitle AS endischarge,
			sh.title AS shipmethod,sh.entitle AS enshipmethod,
			p.title AS paymentterm,p.entitle AS enpaymentterm,
			d.title AS deliveryterm,d.entitle AS endeliveryterm
			FROM `porder` AS o
			LEFT JOIN `pinvoicetype` AS it ON (it.pinvoiceTypeid=o.pinvoiceTypeid)
			LEFT JOIN `supplier` AS s ON (s.supplierid=o.supplierid)
			LEFT JOIN `customer` AS cu ON (cu.customerid=o.customerid)
			LEFT JOIN `currency` AS c ON (c.currencyid=o.currencyid)
			LEFT JOIN `port` AS lo ON (lo.portid=o.loading)
			LEFT JOIN `port` AS dis ON (dis.portid=o.discharge)
			LEFT JOIN `shipmethod` AS sh ON (sh.shipmethodid=o.shipmethodid)
			LEFT JOIN `paymentterm` AS p ON (p.paymenttermid=o.paymenttermid)
			LEFT JOIN `deliveryterm` AS d ON (d.deliverytermid=o.deliverytermid)
			WHERE o.routeid=4 AND o.type='PO' AND o.porderid='".$this->kclass->input['porderid']."'
			LIMIT 0,1
		");
		if($order){
			$created=date("Y 年 m 月 d 日",$order['created']);
			$invoiceType=$this->kclass->iif($order['pinvoiceTypeid']==1,'无发票','开'.$order['taxRate'].'% '.$order['invoiceType']);
			$order['delivery']=str_replace('{CONSIGNEE}','<span class="underline">'.$order['consignee'].'　电话： '.$order['consigneeTel'].'</span>',$order['delivery']);
			$order['remark']=str_replace('{PAYMENT}','<span class="underline bold" style="font-size:24px">'.$order['paymentterm'].'</span>',$order['remark']);
			$order['remark']=str_replace('{SQARE}','<span class="underline">'.$order['spare'].' %</span>',$order['remark']);
			$order['remark']=$this->kclass->parseConvertValue($order['remark']);
			$order['delivery']=$this->kclass->parseConvertValue($order['delivery']);
			$order['special']=$this->kclass->parseConvertValue($order['special']);
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
			if($counter=$this->kclass->DB->numRows()){
				while($item=$this->kclass->DB->fetchArray($items)){
					$total+=$item['amount'];
					$itemtr.='<tr class="middle">
					<td>'.$item['materialno'].'</td>
					<td>'.$item['material'].'　'.$item['standard'].'</td>
					<td>'.$item['brand'].'</td>
					<td>'.$item['package'].'</td>
					<td>'.$item['unit'].'</td>
					<td align="left">'.number_format($item['price'],4).'</td>
					<td align="left">'.number_format($item['quantity']).'</td>
					<td align="left">'.number_format($item['amount'],3).'</td>
					<td>'.$this->kclass->iif($o['purchaseType']=='entrust',$item['requirement'],date('Y-m-d',$item['dateline'])).'</td>
					<td>'.$item['remark'].'</td></tr>';
				}
				if($counter<5){
					for($i=0;$i<10-$counter;$i++){
						$annotate='';
						if($i==0){
							$annotate='以下空白';
						}
						$itemtr.='<tr style="height:30px"><td></td><td>'.$annotate.'</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>';
					}
				}
				$total=number_format($total, 3);
				$cntotal=$this->kclass->numberToCNAccount($total);
				$itemtr.='<tr><td colspan=10>产品要求：'.$order['special'].'</td></tr>
				<tr><td align="center">合　　计</td><td colspan="6">人　民　币（大写）：　 <span class="bold" style="font-size:24px">'.$cntotal.'</span></td><td colspan="3">小写： <span class="bold" style="font-size:24px">￥'.$total.'</span></td></tr>';
			}
			$body=<<<EOF
<div class="center"><div class="title black" style="font-size:30px;padding:10px 0 3px">深圳市中孚能电气设备有限公司</div><div style="font-size:20px;">订购单（采购合同）</div></div>
<div class="hundred">
<span class="left">订单编号：<strong style="font-size:24px">{$order['orderno']}({$invoiceType})</strong></span>
<span class="right">供应商编号：{$order['supplierno']}</span>
</div>
<table class="hundred noline">
<tr><td>订货单位：</td><td width="420"><甲方>深圳市中孚能电气设备有限公司</td><td>联 系 人：</td><td>丁小姐 13312803158</td></tr>
<tr><td rowspan=2>公司地址：</td><td rowspan=2>深圳市龙岗区宝荷路嶂背大道9号1楼<br>（即正中高尔夫球会对面喜来国际家居1楼）</td><td>电　　话：</td><td>0755-28990261</td></tr>
<tr><td>传　　真：</td><td>0755-28998861   22635577(采购部)</td></tr>
<tr><td>供货单位：</td><td><乙方>{$order['supplier']}</td><td>联 系 人：</td><td>{$order['supplierLinkman']}　{$order['mobile']}</td></tr>
<tr><td>地　　址：</td><td>{$order['supplierAddress']}</td><td>电　　话：</td><td>{$order['supplierTelephone']}　传真：{$order['supplierFax']}</td></tr>
</table>
<span>经双方协商一致,甲方向乙方购以下产品：</span>
<table class="hundred"><tbody>
<tr class="center"><td width="80">编码</td><td>名称 规格</td><td>品牌</td><td>封装</td><td>单位</td><td>单价</td><td>数量</td><td>金额</td><td>交货日期</td><td>备注</td></tr>
{$itemtr}
</tbody></table>
<div style="border-bottom:2px solid black;padding:10px 5px;line-height:30px;margin-bottom:10px">
一、产品规格及发货补充说明：<br />
{$order['delivery']}
二、本合同其它条款：<br />{$order['remark']}
</div>
<div class="bottom">
<div class="left" style="width:480px;line-height:30px">
订货单位 (甲方) ：深圳市中孚能电气设备有限公司<br>
授权代表 (签章) ：  丁小姐  13312903158<br>
{$created}
</div>
<div class="left" style="line-height:30px">
供货单位 (乙方) ：{$order['supplier']}<br>
授权代表 (签章) ：<br>
　　年　　月　　日
</div>
</div>
EOF;
		}
		$this->kclass->page['pageTitle']='采购订单 - '.$order['orderno'];
		$this->kclass->page['title'] = '采购订单'; 
		$this->kclass->page['caption'] = '<div class="right middle" style="line-height:45px">深圳市中孚能电气设备有限公司</div>';
		$this->kclass->page['main']='<div style="font-size:18px;line-height:30px;">'.$body.'</div>';
		$this->kclass->page['footer']='<div style="font-size:16px;position:absolute;top:180px">公司地址：深圳市龙岗区宝荷路嶂背大道9号1楼　　　　　　　　　　　　　　　　　　　　Tel：0755-28990261,Fax：0755-28998861</span></div>';
	}

//
function exportcustomerinfo(){
		$customers=$this->kclass->DB->query("SELECT * FROM `customer` WHERE killed=0 AND ifverify=1");
		if($this->kclass->DB->numRows()){
			$i=1;
			$this->kclass->page['main']='<table class="hundred tablesorter mytable"><thead><tr><th width="60">ID号</th><th width="100">客户名称</th><th width="80">联系人</th><th width="300">邮箱</th></tr></thead><tbody>';
			while($customer=$this->kclass->DB->fetchArray()){
				$this->kclass->page['main'].='<tr>
					<td>'.$i.'</td>
					<td>'.$customer['title'].'</td>
					<td>'.$customer['linkman'].'</td>
					<td>'.$customer['email'].'</td>
				</tr>';
				$i++;
			}
			$this->kclass->page['main'].='</tbody></table>';
		}
		$this->kclass->page['onload'] .= "dc.tabhover();dc.tips();$('.mytable').fixedtableheader();$('.tablesorter').tablesorter( { headers: { 5: { sorter: false}, 7: {sorter: false} } } );";
		$this->kclass->page['title'] = 'CustomerInfo';
		$this->kclass->page['footer']='<div style="font-size:16px;position:absolute;top:180px">公司地址：深圳市龙岗区宝荷路嶂背大道9号1楼　　　　　　　　　　　　　　　　　　　　Tel：0755-28990261,Fax：0755-28998861</span></div>';
	}


}

?>