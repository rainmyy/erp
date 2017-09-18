<?php
class maverickPurchase{
	var $type=array(
		array('id'=>1,'title'=>'电子元器件','abbr'=>'D'),
		array('id'=>2,'title'=>'五金类','abbr'=>'W'),
		array('id'=>3,'title'=>'塑料胶','abbr'=>'S'),
		array('id'=>4,'title'=>'工具类','abbr'=>'G'),
		array('id'=>5,'title'=>'辅料类','abbr'=>'F'),
		array('id'=>6,'title'=>'包装类','abbr'=>'B'),
		array('id'=>7,'title'=>'其它类','abbr'=>'Q'),
	);
	function autoRun(){
		$this->kclass->page['title']='零星采购单';
		$this->baseurl='<a href="/s.php">首页</a> - <a href="/s.php?module=maverickPurchase">列表</a>';
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
			default:
				$this->mmlist();
		}
	}
	// 
	function mmlist(){
		if($this->kclass->input['show']=='all'){
			$condition='1=1';
		}else{
			$condition='s.killed=0';
		}
		$mps=$this->kclass->DB->query("
			SELECT s.maverickpurchaseid,s.mpno,s.applicant,s.customerid,s.supplierid,s.supplierTitle,s.linkman,s.shipmentDate,s.detail,s.attachs,s.cover,s.images,s.remark,s.killed,s.modified,s.created,
				sm.title AS shipmethod,
				d.title AS department,
				m.realname AS modifier,mem.realname AS creator
			FROM `maverickpurchase` AS s
			LEFT JOIN `shipmethod` AS sm ON (sm.shipmethodid=s.shipmethodid) 
			LEFT JOIN `department` AS d ON (d.departmentid=s.departmentid) 
			LEFT JOIN `member` AS m ON (m.userid=s.modifier) 
			LEFT JOIN `member` AS mem ON (mem.userid=s.creator) 
			WHERE ".$condition."
			ORDER BY s.mpno DESC
		");
		if($this->kclass->DB->numRows()){
			if($this->kclass->input['layout']=='grid'){
				$body='<ul id="mmlist" class="mmlist clear">';
			}else{
				$body='<table class="hundred"><thead><th width="15">ID</th><th>样品单号</th><th>需求部门</th><th>申请人</th><th>供应商</th><th>联系人</th><th>递送方式</th><th width="50">递送日期</th><th>操作</th></thead><tbody>';
			}
			$i=1;
			while($mp=$this->kclass->DB->fetchArray($mps)){
				$created=date('Y-m-d H:m',$mp['created']);
				$modified=date('Y-m-d H:m',$mp['modified']);
				if($mp['customerid']>0){
					$customer=$this->kclass->relatedCustomer(array('id'=>$mp['customerid']));
				}
				if($mp['supplierid']>0){
					$supplier=$this->kclass->relatedSupplier(array('id'=>$mp['supplierid']));
					$supplier1=$this->kclass->DB->queryFirst("SELECT title FROM supplier WHERE supplierid={$mp['supplierid']}");
					$supplierTitle='<a href="/s.php?module=psupplier&action=view&supplierid='.$mp['supplierid'].'">'.$supplier1['title'];
				}else{
					$supplierTitle=$mp['supplierTitle'];
				}
				$title='';
				$items=$this->kclass->DB->query("
					SELECT material,standard
					FROM mpitem
					WHERE killed=0 AND maverickpurchaseid={$mp['maverickpurchaseid']}
					ORDER BY itemid ASC
				");
				if($this->kclass->DB->numRows()){
					while($item=$this->kclass->DB->fetchArray($items)){
						$title.=$item['material'].'　'.$item['standard'].'<br />';
					} 
				}
				if($mp['killed']>0){
					$link='<a href="/s.php?module=maverickPurchase&action=restore&maverickpurchaseid='.$mp['maverickpurchaseid'].'&rt=list">恢复</a>';
				}else{
					$link='<a href="/s.php?module=maverickPurchase&action=remove&maverickpurchaseid='.$mp['maverickpurchaseid'].'&rt=list" onclick="return confirm(\'你确定要删除这个零星采购单 '.$mp['title'].' 吗？\');">删</a>　<a href="/s.php?module=maverickPurchase&action=update&maverickpurchaseid='.$mp['maverickpurchaseid'].'&rt=list">改</a>';
				}
				if($this->kclass->input['layout']=='grid'){//列表
					$body .= '<li title="由 '.$mp['creator'].' 建于 '.$created.$this->kclass->iif($mp['modifier']!='','，'.$mp['modifier'].' 改于 '.$modified,'').'"'.$this->kclass->iif($i%4==0,' class="end"','').'>
					<div class="mmlistt">
						<span class="right normal">';
					if($mp['killed']>0){
						$body .= '<a href="/s.php?module=maverickPurchase&action=restore&maverickpurchaseid='.$mp['maverickpurchaseid'].'&rt=list">恢复</a>';
					}else{
						$body .= '<a href="/s.php?module=maverickPurchase&action=kill&maverickpurchaseid='.$mp['maverickpurchaseid'].'&rt=list" onclick="return confirm(\'你确定要删除这个样品 '.$mp['title'].' 吗？\');">删</a> <a href="/s.php?module=maverickPurchase&action=update&maverickpurchaseid='.$mp['maverickpurchaseid'].'&rt=list">改</a>';
					}
					$body .= '</span>
						<span class="small">'.$mp['attr'].'</span> <a href="/s.php?module=maverickPurchase&action=view&maverickpurchaseid='.$mp['maverickpurchaseid'].'">'.$mp['title'].'</a><br>'.$this->kclass->iif($mp['attachs']>0,' <span class="attachFile" title="有'.$mp['attachs'].'个附件。"></span> ','').$this->kclass->iif($mp['images']>0,' <span class="attachImage" title="有'.$mp['images'].'个图片。"></span> ','').$this->kclass->iif($mp['entitle']!='','<span class="small">'.$mp['entitle'].'</span>','').'</div>
					<div class="mmlistb">
						<div>'.$this->kclass->iif($mp['cover']!='','<div class="center"><img src="'.$mp['cover'].'"></div>','').'
						<div class="small clear"><span class="right" title="由 '.$mp['creator'].' 建于 '.date('y-m-d H:m',$mp['created']).'">'.$this->kclass->iif($mp['modifier']!='','由 最后'.$mp['modifier'].' 改于 '.date('Y-m-d H:m',$mp['modified']),'建于 '.date('Y-m-d H:m',$mp['created'])).'</span></div>
					</div>
					</li>';
				}else{
					$body .='<tr '.$this->kclass->iif($this->kclass->rotate(),'odd','even').'>
						<td>'.$i.'</td>
						<td class="tip" title="'.$title.'">'.$this->kclass->iif($mp['attachs']>0,' <span class="attachFile" title="有'.$mp['attachs'].'个附件。"></span> ','').$this->kclass->iif($mp['images']>0,' <span class="attachImage" title="有'.$mp['images'].'个图片。"></span> ','').'<a href="/s.php?module=maverickPurchase&action=view&maverickpurchaseid='.$mp['maverickpurchaseid'].'">'.$mp['mpno'].'</a></td>
						<td>'.$mp['department'].'</td>
						<td>'.$mp['applicant'].'</td>
						<td>'.$supplierTitle.'</td>
						<td>'.$mp['linkman'].'</td>
						<td>'.$mp['shipmethod'].'</td>
						<td>'.date('Y-m-d',$mp['shipmentDate']).'</td>
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
			$layoutLink='<a href="/s.php?module=maverickPurchase&action=list&layout=list'.str_replace('&layout=grid', '', $queryPart).'">列表</a> 格子';
		}else{
			$layoutLink='列表 <a href="/s.php?module=maverickPurchase&action=list&layout=grid'.str_replace('&layout=list', '', $queryPart).'">格子</a>';
		}
		if($this->kclass->input['show']=='all'){
			$showLink='<a href="/s.php?module=maverickPurchase&action=list'.str_replace('&show=all', '', $queryPart).'">默认</a> 所有<span class="small gray">(包括删除)</span>';
		}else{
			$showLink='默认 <a href="/s.php?module=maverickPurchase&action=list&show=all'.str_replace('&show=all', '', $queryPart).'">所有<span class="small gray">(包括删除)</span></a>';
		}
		$this->kclass->page['onload'] .= 'dc.tabhover();dc.tips()';
		$this->kclass->page['title'] .= ' - 列表';
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 列表','right' => '<span class="small">显示方式：</span>'.$showLink.'　|　<span class="small">布局：</span>'.$layoutLink.'　|　<a href="/s.php?module=maverickPurchase&action=add">新建零星采购单</a>','body'=>$body));
	}
	// 
	function view(){
		if(!$this->kclass->input['maverickpurchaseid'] > 0)$this->kclass->boinkIt('/s.php?module=maverickPurchase');
		$mp=$this->kclass->DB->queryFirst("
			SELECT s.maverickpurchaseid,s.mpno,s.customerid,s.supplierid,s.supplierTitle,s.linkman,s.shipmentDate,s.detail,s.remark,s.modified,s.created,s.killed,
				sm.title AS shipmethod,
				d.title AS department,
				m.realname AS modifier,mem.realname AS creator
			FROM `maverickpurchase` AS s
			LEFT JOIN `shipmethod` AS sm ON (sm.shipmethodid=s.shipmethodid) 
			LEFT JOIN `department` AS d ON (d.departmentid=s.departmentid) 
			LEFT JOIN `member` AS m ON (m.userid=s.modifier) 
			LEFT JOIN `member` AS mem ON (mem.userid=s.creator) 
			WHERE s.maverickpurchaseid='".$this->kclass->input['maverickpurchaseid']."'
		");
		if(!$mp)	$this->kclass->boinkIt('/s.php?module=maverickPurchase');
		if($mp['customerid']>0){
			$customer=$this->kclass->relatedCustomer(array('id'=>$mp['customerid']));
		}
		if($mp['supplierid']>0){
			$supplier=$this->kclass->relatedSupplier(array('id'=>$mp['supplierid']));
			$supplier1=$this->kclass->DB->queryFirst("SELECT title FROM supplier WHERE supplierid={$mp['supplierid']}");
			$supplierTitle=$supplier1['title'];
		}else{
			$supplierTitle=$mp['supplierTitle'];
		}

		if($mp['modified']>0){
			$modified='，'.$mp['modifier'].' 于 '.date('Y-m-d H:i:s',$mp['modified']).' 最后修改';
		}
		if($mp['shipmentDate']>0){
			$shipmentDate=' <span class="darkred">'.date('Y-m-d',$mp['shipmentDate']).'</span>';
		}
		$created=date('Y-m-d H:i:s',$mp['created']);
		
		$total=0;
		$items = $this->kclass->DB->query("
			SELECT si.materialno,si.material,si.standard,si.quantity,si.unitid,si.price,si.amount,si.remark,
				unit.title AS unit
			FROM `mpitem` AS si
			LEFT JOIN unit ON (unit.unitid=si.unitid)
			WHERE si.killed=0 AND si.maverickpurchaseid='".$mp['maverickpurchaseid']."'
			ORDER BY si.ordering,si.modified ASC,si.itemid ASC
		");
		if($this->kclass->DB->numRows()){
			$k=1;
			while($item = $this->kclass->DB->fetchArray($items)){
				$itemtr .= '<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').'">
					<td>'.$k.'</td>
					<td>'.$item['materialno'].'</td>
					<td>'.$item['material'].'</td>
					<td>'.$item['standard'].'</td>
					<td align="right">'.number_format($item['quantity']).' '.$item['unit'].'</td>
					<td align="right">'.number_format($item['price'], 2).'</td>
					<td align="right">'.number_format($item['amount'], 2).'</td>
					<td>'.$item['remark'].'</td>
				</tr>';
				$k++;
				$quantity+=$item['quantity'];
				$total+=$item['amount'];
			}
		}

		$fee=$this->kclass->relatedFee(array('module'=>'maverickpurchase','maverickpurchaseid'=>$mp['maverickpurchaseid']));
		if($fee['total']>0){
			$total += $fee['total'];
		}
		$discounts=$this->kclass->DB->query("SELECT * FROM `discount` WHERE killed=0 AND module='maverickpurchase' AND mid='".$mp['maverickpurchaseid']."'");
		if($this->kclass->DB->numRows()){
						
			while($discount=$this->kclass->DB->fetchArray($discounts)){
				if($item['total']>0){
					$amount=0;
					if($discount['sign']=='%'){
						$discount['discount']=intVal($discount['discount']);
						$amount=($item['total'] * $discount['discount'] / 100);
					}else{
						$amount=$discount['discount'];
					}
					if($discount['addition']=='+'){
						$total += $amount;
					}else{
						$total -= $amount;
					}
				}
				$discounttr .= '<tr class="'.$this->kclass->iif($this->kclass->rotate(),'even','odd').'">
				<td>'.$discount['reason'].'</td>
				<td align="right">'.$discount['addition'].''.$discount['discount'].''.$this->kclass->iif($discount['sign']=='%',$discount['sign'],'').'</td>
				<td align="right">'.$discount['addition'].number_format($amount,2).'</td>
				<td align="right">'.$mp['currencySymbol'].number_format($total,2).'</td>
				</tr>';
			}
			$discounttr='<tr><td title="Reason">折扣说明(理由)</td><td title="Discount" align="right">折扣</td><td title="Amount" align="right">计算折扣数</td><td title="Amount" align="right">折扣后总数</td></tr>'.$discounttr.'<tr><td colspan="12" align="right">折后计：<span class="bold">'.$mp['currencyTitle'].' '.$mp['currencySymbol'].number_format($total,2).'</span></td></tr>';
		}else{
			$discounttr='<tr><td class="gray"><i>无折扣信息</i></td></tr>';
		}
		$total=number_format($total,2);
		// attach
		$attach=$this->kclass->getAttachs(array('module'=>'maverickpurchase','mid'=>$mp['maverickpurchaseid']));
		if($attach!= false){
			$attachs='<div class="clear">'.$attach.'</div>';
		}
		/*$events=$this->kclass->DB->query("
			SELECT event.*,
				dc.title AS deliveryCurrency,
				sc.title AS sampleCurrency,
				shipmethod.title AS shipmethod 
			FROM sampleevent AS `event`
			LEFT JOIN currency AS dc ON (dc.currencyid=`event`.deliveryCurrency) 
			LEFT JOIN currency AS sc ON (sc.currencyid=`event`.sampleCurrency) 
			LEFT JOIN shipmethod ON (shipmethod.shipmethodid=`event`.shipmethodid) 
			WHERE `event`.killed=0 AND `event`.psampleid='".$sample['psampleid']."'
			ORDER BY `event`.modified DESC
		");
		if($this->kclass->DB->numRows()){
			$eventtbl='<table class="hundred"><thead><tr><th colspan="7">收发与费用</th></tr><tr><th>收发类型</th><th>收发数量</th><th>收发日期</th><th>递送方式</th><th>递送费用</th><th>样品费用</th><th>操作</th></tr></thead><tbody>';
			$this->kclass->tbline+=1;
			while($event=$this->kclass->DB->fetchArray($events)){
				$eventtbl.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').'"><td'.$this->kclass->iif($event['direction']=='sent',' class="green">发出',' class="red">收到').'</td><td'.$this->kclass->iif($event['totalQuantity']=='all','>全部',' class="bold">部分').'</td><td>'.date('Y-m-d',$event['deliveryDate']).'</td><td>'.$event['shipmethod'].'</td><td>'.$event['deliveryCurrency'].$event['deliveryFee'].$this->kclass->iif($event['deliveryCaption']!='',' ('.$event['deliveryCaption'].')','').'</td><td>'.$event['sampleCurrency'].$event['sampleFee'].$this->kclass->iif($event['sampleCaption']!='',' ('.$event['sampleCaption'].')','').'</td><td><a href="/s.php?module=psample&action=removeEvent&eventid='.$event['eventid'].'" onclick="return confirm(\'你确定要删除这条收发与费用记录吗？(删除不可恢复！)\')">删</a> <a href="/s.php?module=psample&action=updateEvent&eventid='.$event['eventid'].'">改</a></td></tr>';
			}
			$eventtbl .= '</tbody></table>';
		}else{
			$event['off']=' disabled';
		}*/
$body=<<<EOF
<div class="title"><span class="right small gray">由 {$mp['cname']} 于 {$created} 建立{$modified}。</span>{$mp['sampleno']}</div>
<dl id="sample" class="tabs">
	<dt>资料</dt>
	<dt title="询价记录"{$event['off']}>收发与费用{$event['count']}</dt>
	<dt title="相关客户基本资料"{$customer['off']}>相关客户</dt>
	<dt title="相关供应商基本资料"{$supplier['off']}>相关供应商</dt>
	<dd>
<table cellspacing="10" width="100%">
	<tr class="odd">
	<td width="80">样品编号：<span class="red bold">*</span></td><td width="650" ><span class="red middle bold">{$mp['sampleno']}</span></td>
	<td width="80">样品类型：<span class="red bold">*</span></td>	<td>{$sampleType}</td>
</tr>
<tr class="even">
	<td>供 应 商：<span class="red bold">*</span></td><td>{$supplierTitle}</td>
	<td>客　　户：</td>	<td>{$customer['link']}</td>
</tr>
<tr class="odd">
	<td>联系人员：</td>	<td>{$mp['linkman']}</td>
	<td>递送方式：<span class="red bold">*</span></td><td>{$mp['shipmethod']}</td>
</tr>
<tr class="even">
	<td>递送日期：<span class="red bold">*</span></td><td>{$shipmentDate}</td>
	<td>需求部门：<span class="red bold">*</span></td><td>{$mp['department']}</td>
</tr>
<tr class="odd">
	<td valign="top">详细描述：</td><td>{$mp['detail']}</td>
	<td valign="top">备　　注：</td><td>{$mp['remark']}</td>
</tr>
</table>
<table class="hundred">
<thead>
<tr>
	<th colspan="12">样品明细表 (Item Information)</th>
</tr>
</thead>
<tbody>
<tr><td width="15">ID</td><td width="40">编号</td><td>名称</td><td>规格</td><td width="100">数量</td><td width="100">单价</td><td width="100">金额</td><td width="200">备注</td></tr>	
{$itemtr}
<tr><td colspan="4" align="right">合计：</td><td align="right">{$quantity}</td><td align="right">总价</td><td align="right" class="middle bold">{$total}</td><td></td></tr>
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
{$discounttr}
</tbody>
</table>
<table class="hundred">
<tbody>
<tr><td align="right">总计(Total Amount)：<span class="middle bold darkred">{$mp['currencyTitle']} {$mp['currencySymbol']}{$total}</span></td></tr>
</tbody>
</table>
{$eventtbl}
{$attachs}
	</dd>
	<dd>{$eventtbl}</dd>
	<dd>{$customer['panel']}</dd>
	<dd>{$supplier['panel']}</dd>
</dl>
EOF;
		$this->kclass->page['onload'] .= "dc.tabs({'id':'sample'});dc.tabhover()";
		$this->kclass->page['title'] .= ' - '.$mp['title'];
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title' => $this->baseurl.' - 零星采购信息','right' =>$this->kclass->iif($mp['killed']>0,'<a href="/s.php?module=maverickPurchase&action=restore&maverickpurchaseid='.$mp['maverickpurchaseid'].'&rt=view">恢复</a>','<a href="/s.php?module=maverickPurchase&action=remove&maverickpurchaseid='.$mp['maverickpurchaseid'].'&rt=view">删除</a>　<a href="/s.php?module=maverickPurchase&action=update&maverickpurchaseid='.$mp['maverickpurchaseid'].'&rt=view">修改</a>'),'body'=>$body));
	}
	//
	function add(){
		$defaultCustomer=$this->kclass->DB->queryFirst("SELECT title FROM customer WHERE customerid=1");
		$supplier=$this->kclass->chooserSupplier(array('hName'=>'supplierid','hId'=>'supplierid','name'=>'supplier','id'=>'supplier','width'=>350));
		$customer = $this->kclass->chooserCustomer(array('hName'=>'customerid','name'=>'customer','value'=>$defaultCustomer['title'],'width'=>350,'selectedid'=>1));
		$shipmethod=$this->kclass->chooserShipmethod(array('name'=>'shipmethodid','hasBlank'=>1,'width'=>350,'selectedid'=>$this->kclass->input['shipmethodid']));
		$department=$this->kclass->chooserDepartment(array('name'=>'departmentid','departmentid'=>0,'hasBlank'=>1,'width'=>350));
		
		// mpno
		$start=strtotime(date('Y-m-d',TIMENOW));
		$end=strtotime(date('Y-m-d',$start))+86400;
		$counter=$this->kclass->DB->queryFirst("SELECT COUNT(*) AS count FROM maverickpurchase WHERE killed=0 AND created>='".$start."' AND created<='".$end."'");
		$mpno=$this->kclass->id(array('maverickPurchase'=>TIMENOW,'number'=>$counter['count']));
		//item
		for($i=1; $i < 7; $i++){
			$itemtr .= '<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').'">
				<td>'.$i.'</td>
				<td><input type="text" name="itemMaterialno['.$i.']" size="6"></td>
				<td><input type="text" name="itemMaterial['.$i.']" size="55"></td>
				<td><input type="text" name="itemStandard['.$i.']" size="50"></td>
				<td>'.$this->_chooserType(array('name'=>'itemTypeid['.$i.']','width'=>'150','hasBlank'=>1)).'</td>
				<td><input type="text" name="itemQuantity['.$i.']" size="5"></td>
				<td>'.$this->kclass->chooserUnit(array('name'=>'itemUnitid['.$i.']','hasBlank'=>1,'width'=>120,'selectedid'=>$this->kclass->input['unitid'])).'</td>
				<td><input type="text" name="itemPrice['.$i.']" size="7"></td>
				<td><input type="text" name="itemRemark['.$i.']" size="40"></td>
			</tr>';
		}
		$fee=$this->kclass->formFee(array('module'=>'maverickpurchase'));
		$discount=$this->kclass->formDiscount(array('module'=>'maverickpurchase'));
		$shipmentDate=date('Y-m-d',TIMENOW);
		$upload=$this->kclass->upload(array('title'=>'相关附件：'));
$body=<<<EOF
<form action="/s.php?module=maverickPurchase&action=insert" name="maverickPurchase" method="post">
<input type="hidden" name="module" value="maverickPurchase">
<input type="hidden" name="action" value="insert">
<table class="hundred">
<thead>
<tr>
	<th colspan="4">新建零星采购订单：<span class="small red">(由采购部填写)</span></th>
</tr>
</thead>
<tbody>
<tr class="odd">
	<td>采购单编号：<span class="red bold">*</span></td>
	<td><span class="red middle bold">{$mpno}</span>　<span class="small gray">此为预估编号</span></td>
	<td>申 请 人：<span class="red bold">*</span></td>
	<td><input type="text" name="applicant" size="10" ></td>
</tr>
<tr class="even">
	<td>供 应 商：<span class="red bold">*</span></td>
	<td>{$supplier}</td>
	<td>客　　户：</td>
	<td>{$customer}</td>
</tr>
<tr class="odd">
	<td>联系人员：</td>
	<td><input type="text" style="width:350px" name="linkman" ></td>
	<td>递送方式：<span class="red bold">*</span></td>
	<td>{$shipmethod}</td>
</tr>
<tr class="even">
	<td>递送日期：<span class="red bold">*</span></td>
	<td><input type="text" id="shipmentDate" name="shipmentDate" value="{$shipmentDate}" size="10"></td>
	<td>需求部门：<span class="red bold">*</span></td>
	<td>{$department}</td>
</tr>
<tr class="odd">
	<td valign="top">详细描述：</td>
	<td><textarea name="detail" style="width:350px;height:70px"></textarea></td>
	<td valign="top">备　　注：</td>
	<td><textarea name="remark" style="width:350px;height:70px"></textarea></td>
</tr>
</tbody>
</table>
<table class="hundred">
<thead>
<tr>
	<th colspan="9">零星采购 明细表</th>
</tr>
</thead>
<tbody>
<tr><td width="15">ID</td><td>编号</td><td>名称<span class="red bold">*</span></td><td>规格</td><td>分类<span class="red bold">*</span></td><td>数量<span class="red bold">*</span></td><td>单位<span class="red bold">*</span></td><td>单价<span class="red bold">*</span></td><td>备注</td></tr>
{$itemtr}
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
		$this->kclass->page['title'] .= ' - 新建零星采购';
		$this->kclass->page['onload'] .= 'dc.menu(\'rightNav\');dc.tabhover();$(\'#shipmentDate\').datepicker()';
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 新建零星采购','right' => $this->right.'<a href="/s.php?module=maverickPurchase">返回列表</a>','body'=>$body));
	}
	//
	function insert(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e='<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			if(!$this->kclass->input['shipmethodid'] > 0){
				$e .= '<li>请选择零星采购单的所属 递送方式。</li>';
			}
			if($this->kclass->input['shipmentDate']==''){
				$e .= '<li>请选择零星采购单的所属 递送日期。</li>';
			}
			if(!$this->kclass->input['departmentid']>0){
				$e .= '<li>请选择零星采购单的 需求部门。</li>';
			}
			$count=count($this->kclass->input['itemMaterial']);
			$hasItem=0;
			for($i=1;$i<=$count;$i++){
				if($this->kclass->input['itemMaterial'][$i]!='' AND $this->kclass->input['itemQuantity'][$i]>0 AND $this->kclass->input['itemUnitid'][$i]>0 AND $this->kclass->input['itemPrice'][$i]>=0){
					$hasItem=1;
				}
			}
			if($hasItem==0){
				$e.='<li>请至少填写一条明细</li>';
			}
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '新建零星采购单',
				'text' => '您在新建零星采购单的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		// sampleno
		$start=strtotime(date('Y-m-d',TIMENOW));
		$end=strtotime(date('Y-m-d',$start))+86400;
		$counter=$this->kclass->DB->queryFirst("SELECT COUNT(*) AS count FROM maverickpurchase WHERE killed=0 AND created>='".$start."' AND created<='".$end."'");
		$mpno=$this->kclass->id(array('maverickPurchase'=>TIMENOW,'number'=>$counter['count']));
		// time
		$shipmentDate=0;
		if($this->kclass->input['shipmentDate']!=''){
			$dd=explode('-',$this->kclass->input['shipmentDate']);
			$shipmentDate=mktime(0,0,0,$dd[1],$dd[2],$dd[0]);
		}
		if($this->kclass->input['supplierid']>0){
			$supplier=$this->kclass->DB->queryFirst("SELECT linkman FROM supplier WHERE supplierid={$this->kclass->input['supplierid']}");
			$linkman=$supplier['linkman'];
		}else{
			$linkman=$this->kclass->input['linkman'];
		}
		$this->kclass->DB->query("
			INSERT INTO `maverickpurchase` (`mpno`,`customerid`,`supplierid`,`supplierTitle`,`applicant`,`linkman`,`shipmethodid`,`shipmentDate`,`departmentid`,`detail`,`remark`,`creator`,`created`) 
			VALUES ('{$mpno}','".$this->kclass->input['customerid']."','".$this->kclass->input['supplierid']."','".$this->kclass->input['supplier']."','{$this->kclass->input['applicant']}','".$linkman."','".$this->kclass->input['shipmethodid']."','".$shipmentDate."','".$this->kclass->input['departmentid']."','".$this->kclass->input['detail']."','".$this->kclass->input['remark']."','".$this->kclass->user['userid']."','".TIMENOW."')
		");
		$maverickpurchaseid=$this->kclass->DB->insertID();

		// item
		for($i=1;$i<=$count;$i++){
			if($this->kclass->input['itemMaterial'][$i]!='' AND $this->kclass->input['itemQuantity'][$i]>0 AND $this->kclass->input['itemUnitid'][$i]>0 AND $this->kclass->input['itemPrice'][$i]>=0){
				$this->kclass->input['itemQuantity'][$i]=floatval($this->kclass->input['itemQuantity'][$i]);
				$this->kclass->input['itemPrice'][$i]=floatVal($this->kclass->input['itemPrice'][$i]);
				$amount=number_format($this->kclass->input['itemQuantity'][$i] * $this->kclass->input['itemPrice'][$i],2,'.','');
				$this->kclass->DB->query("
					INSERT INTO `mpitem` (`materialno`,`material`,`maverickpurchaseid`,`typeid`,`standard`,`quantity`,`unitid`,`price`,`amount`,`remark`,`creator`,`created`)
					VALUES ('".$this->kclass->input['itemMaterialno'][$i]."','".$this->kclass->input['itemMaterial'][$i]."',{$maverickpurchaseid},'{$this->kclass->input['itemTypeid'][$i]}','".$this->kclass->input['itemStandard'][$i]."','".$this->kclass->input['itemQuantity'][$i]."','".$this->kclass->input['itemUnitid'][$i]."','".$this->kclass->input['itemPrice'][$i]."','".$amount."','".$this->kclass->input['itemRemark'][$i]."','".$this->kclass->user['userid']."','".TIMENOW."')
				");
			}
		}
		// fee & discount
		$this->kclass->updateFee(array('module'=>'maverickpurchase','mid'=>$maverickpurchaseid));
		$this->kclass->updateDiscount(array('module'=>'maverickpurchase','mid'=>$maverickpurchaseid));
		$this->kclass->updateAttachs(array('module'=>'maverickpurchase','mid'=>$maverickpurchaseid));
		$this->kclass->messager(array(
			'title' => '新建样品单',
			'text' => '样品单 <b>'.$mpno.'</b> 已新建成功!',
			'url' => '/s.php?module=maverickPurchase&action=view&maverickpurchaseid='.$maverickpurchaseid,
			'sec' => 2
		));
	}
	//
	function update(){
		if($this->kclass->input['maverickpurchaseid']<=0){
			$this->kclass->boinkIt('/s.php?module=maverickPurchase');
		}
		$mp=$this->kclass->DB->queryFirst("
			SELECT mp.maverickpurchaseid,mp.mpno,mp.customerid,mp.supplierid,mp.supplierTitle,mp.applicant,mp.linkman,mp.shipmethodid,mp.shipmentDate,mp.departmentid,mp.detail,mp.remark,
				c.title AS customer,su.title AS supplier
			FROM `maverickpurchase` AS mp
			LEFT JOIN customer AS c ON (c.customerid=mp.customerid)
			LEFT JOIN supplier AS su ON (su.supplierid=mp.supplierid)
			WHERE mp.killed=0 AND mp.`maverickpurchaseid`='".$this->kclass->input['maverickpurchaseid']."'
		");
		if(!$mp){
			$this->kclass->boinkIt('/s.php?module=maverickPurchase');
		}
		$shipmentDate=date('Y-m-d',$mp['shipmentDate']);
		$this->kclass->br2nl=1;
		$mp['detail']=$this->kclass->parseConvertValue($mp['detail']);
		$mp['remark']=$this->kclass->parseConvertValue($mp['remark']);
		$supplier=$this->kclass->chooserSupplier(array('hName'=>'supplierid','hId'=>'supplierid','name'=>'supplier','value'=>$this->kclass->iif($mp['supplierid']>0,$mp['supplier'],$mp['supplierTitle']),'id'=>'supplier','width'=>350,'selectedid'=>$mp['supplierid']));
		$shipmethod=$this->kclass->chooserShipmethod(array('name'=>'shipmethodid','hasBlank'=>1,'width'=>350,'selectedid'=>$mp['shipmethodid']));
		$department=$this->kclass->chooserDepartment(array('name'=>'departmentid','hasBlank'=>1,'width'=>350,'selectedid'=>$mp['departmentid']));

		// sample items 
		$items=$this->kclass->DB->query("
			SELECT itemid,materialno,material,standard,typeid,quantity,unitid,price,amount,remark
			FROM mpitem
			WHERE killed=0 AND maverickpurchaseid={$mp['maverickpurchaseid']}
		");
		$k=1;
		if($this->kclass->DB->numRows()){
			while($item=$this->kclass->DB->fetchArray($items)){
				$itemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').'">
					<td><input type="hidden" name="itemId['.$k.']" value="'.$item['itemid'].'" >'.$k.'</td>
					<td><input type="text" name="itemMaterialno['.$k.']" size="6" value="'.$item['materialno'].'"></td>
					<td><input type="text" name="itemMaterial['.$k.']" size="50" value="'.$item['material'].'"></td>
					<td><input type="text" name="itemStandard['.$k.']" size="45" value="'.$item['standard'].'"></td>
					<td>'.$this->_chooserType(array('name'=>'itemTypeid['.$k.']','width'=>'150','hasBlank'=>1,'selectedid'=>$item['typeid'])).'</td>
					<td><input type="text" name="itemQuantity['.$k.']" size="5" value="'.$item['quantity'].'"></td>
					<td>'.$this->kclass->chooserUnit(array('name'=>'itemUnitid['.$k.']','hasBlank'=>1,'width'=>120,'selectedid'=>$item['unitid'])).'</td>
					<td><input type="text" name="itemPrice['.$k.']" size="7" value="'.$item['price'].'"></td>
					<td><input type="text" name="itemRemark['.$k.']" size="40" value="'.$item['remark'].'"></td>
					<td><input type="checkbox" name="itemKill['.$k.']" size="40" value="'.$item['itemid'].'"></td>
				</tr>';
				$k++;
			}
			$itemtr.='<tr><td colspan=10>新增明细</td></tr>';
		}
		//item
		for($i=$k; $i<6+$k; $i++){
			$itemtr .= '<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').'">
				<td>'.$i.'</td>
				<td><input type="text" name="itemMaterialno['.$i.']" size="6"></td>
				<td><input type="text" name="itemMaterial['.$i.']" size="50"></td>
				<td><input type="text" name="itemStandard['.$i.']" size="45"></td>
				<td>'.$this->_chooserType(array('name'=>'itemTypeid['.$i.']','width'=>'150','hasBlank'=>1)).'</td>
				<td><input type="text" name="itemQuantity['.$i.']" size="5"></td>
				<td>'.$this->kclass->chooserUnit(array('name'=>'itemUnitid['.$i.']','hasBlank'=>1,'width'=>120,'selectedid'=>$this->kclass->input['unitid'])).'</td>
				<td><input type="text" name="itemPrice['.$i.']" size="7"></td>
				<td><input type="text" name="itemRemark['.$i.']" size="40"></td>
				<td></td>
			</tr>';
		}
		$fee=$this->kclass->formFee(array('module'=>'maverickpurchase','mid'=>$mp['maverickpurchaseid']));
		$discount=$this->kclass->formDiscount(array('module'=>'maverickpurchase','mid'=>$mp['maverickpurchaseid']));
		$upload=$this->kclass->upload(array('title'=>'相关附件：','module'=>'maverickPurchase','mid'=>$mp['maverickpurchaseid']));
$body=<<<EOF
<form action="/s.php?module=maverickPurchase&action=doupdate" name="maverickPurchase" method="post">
<input type="hidden" name="module" value="maverickPurchase">
<input type="hidden" name="action" value="doupdate">
<input type="hidden" name="maverickpurchaseid" value="{$this->kclass->input['maverickpurchaseid']}">
<table class="hundred">
<thead>
<tr>
	<th colspan="4">新建零星采购单：<span class="small red">(由采购部填写)</span></th>
</tr>
</th80ead>
<tbody>
<tr class="odd">
	<td>零星采购编号：<span class="red bold">*</span></td>
	<td><span class="red middle bold">{$mp['mpno']}</span>　<span class="small gray">此为预估编号</span></td>
	<td>申 请 人：<span class="red bold">*</span></td>
	<td><input type="text" name="applicant" size="10" value="{$mp['applicant']}"></td>
</tr>
<tr class="even">
	<td>供 应 商：<span class="red bold">*</span></td>
	<td>{$supplier}</td>
	<td>客　　户：</td>
	<td>{$mp['customer']}</td>
</tr>
<tr class="odd">
	<td>联系人员：</td>
	<td><input type="text" style="width:350px" name="linkman" value="{$mp['linkman']}"></td>
	<td>递送方式：</td>
	<td>{$shipmethod}</td>
</tr>
<tr class="even">
	<td>递送日期：<span class="red bold">*</span></td>
	<td><input type="text" size="10" id="deliveryDate" name="shipmentDate" value="{$shipmentDate}"></td>
	<td>需求部门：<span class="red bold">*</span></td>
	<td>{$department}</td>
</tr>
<tr class="odd">
	<td valign="top">详细描述：</td>
	<td><textarea name="detail" style="width:350px;height:70px">{$mp['detail']}</textarea></td>
	<td valign="top">备　　注：</td>
	<td><textarea name="remark" style="width:350px;height:70px">{$mp['remark']}</textarea></td>
</tr>
</tbody>
</table>
<table class="hundred">
<thead>
<tr>
	<th colspan="10">零星采购单 明细表</th>
</tr>
</thead>
<tbody>
<tr class="center"><td width="15">ID</td><td>编号</td><td>名称<span class="red bold">*</span></td><td>规格</td><td>分类</td><td>数量<span class="red bold">*</span></td><td>单位<span class="red bold">*</span></td><td>单价<span class="red bold">*</span></td><td>备注</td><td>删</td></tr>
{$itemtr}
<tr>
	<td colspan="10" class="gray small">注：①如行数不够，请保存后再进入修改页面。②至少有“名称”、“数量”、“单位”三个项目，该明细才会被保存。③如果没有费用，请填写“0”。</td>
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
		$this->kclass->page['title'] .= ' - 修改 - '.$mp['mpno'];
		$this->kclass->page['onload'] .= 'dc.tabhover();$(\'#deliveryDate\').datepicker()';
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title' => $this->baseurl .'<a href="/s.php?module=maverickPurchase&action=view&maverickpurchaseid='.$mp['maverickpurchaseid'].'">'.$mp['mpno'].'</a>'.' - 修改零星采购单 - '.$mp['title'],'right' => '<a href="/s.php?module=maverickPurchase">返回列表</a>','body'=>$body));
	}
	//
	function doupdate(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e='<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			if(!$this->kclass->input['shipmethodid'] > 0){
				$e .= '<li>请选择样品单的所属 递送方式。</li>';
			}
			if($this->kclass->input['shipmentDate']==''){
				$e .= '<li>请选择样品单的所属 递送日期。</li>';
			}
			if(!$this->kclass->input['departmentid']>0){
				$e .= '<li>请选择样品单的 需求部门。</li>';
			}
			$count=count($this->kclass->input['itemMaterial']);
			$hasItem=0;
			for($i=1;$i<=$count;$i++){
				if($this->kclass->input['itemKill'][$i]=='' AND $this->kclass->input['itemMaterial'][$i]!='' AND $this->kclass->input['itemTypeid'][$i]>0 AND $this->kclass->input['itemQuantity'][$i]>0 AND $this->kclass->input['itemUnitid'][$i]>0 AND $this->kclass->input['itemPrice'][$i]>=0){
					$hasItem=1;
				}
			}
			if($hasItem==0){
				$e.='<li>请至少填写一条明细</li>';
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
		$mp=$this->kclass->DB->queryFirst("SELECT maverickpurchaseid,mpno FROM maverickpurchase WHERE maverickpurchaseid={$this->kclass->input['maverickpurchaseid']}");
		// item
		for($i=1;$i<=$count;$i++){
			if($this->kclass->input['itemMaterial'][$i]!='' AND $this->kclass->input['itemTypeid'][$i]>0 AND $this->kclass->input['itemQuantity'][$i]>0 AND $this->kclass->input['itemUnitid'][$i]>0 AND $this->kclass->input['itemPrice'][$i]>=0){
				$this->kclass->input['itemQuantity'][$i]=floatval($this->kclass->input['itemQuantity'][$i]);
				$this->kclass->input['itemPrice'][$i]=floatVal($this->kclass->input['itemPrice'][$i]);
				$amount=number_format($this->kclass->input['itemQuantity'][$i] * $this->kclass->input['itemPrice'][$i],2,'.','');
				if($this->kclass->input['itemId'][$i]>0){
					if($this->kclass->input['itemKill'][$i]>0 AND $this->kclass->input['itemKill'][$i]==$this->kclass->input['itemId'][$i]){
						$this->kclass->DB->query("UPDATE mpitem SET killed='".TIMENOW."',killer='".$this->kclass->user['userid']."' WHERE itemid={$this->kclass->input['itemId'][$i]}");
					}else{
						$this->kclass->DB->query("
							UPDATE mpitem SET 
								`materialno`='{$this->kclass->input['itemMaterialno'][$i]}',
								`material`='{$this->kclass->input['itemMaterial'][$i]}',
								`typeid`='{$this->kclass->input['itemTypeid'][$i]}',
								`standard`='{$this->kclass->input['itemStandard'][$i]}',
								`quantity`='{$this->kclass->input['itemQuantity'][$i]}',
								`unitid`='{$this->kclass->input['itemUnitid'][$i]}',
								`price`='{$this->kclass->input['itemPrice'][$i]}',
								`amount`='{$amount}',
								`remark`='{$this->kclass->input['itemRemark'][$i]}',
								`modified`='".TIMENOW."',
								`modifier`='{$this->kclass->user['userid']}'
							WHERE itemid={$this->kclass->input['itemId'][$i]}
						");
					}
				}else{
					$this->kclass->DB->query("
						INSERT INTO `mpitem` (`materialno`,`material`,`maverickpurchaseid`,`typeid`,`standard`,`quantity`,`unitid`,`price`,`amount`,`remark`,`creator`,`created`)
						VALUES ('{$this->kclass->input['itemMaterialno'][$i]}','".$this->kclass->input['itemMaterial'][$i]."',{$mp['maverickpurchaseid']},'{$this->kclass->input['itemTypeid'][$i]}','".$this->kclass->input['itemStandard'][$i]."','".$this->kclass->input['itemQuantity'][$i]."','".$this->kclass->input['itemUnitid'][$i]."','".$this->kclass->input['itemPrice'][$i]."','".$amount."','".$this->kclass->input['itemRemark'][$i]."','".$this->kclass->user['userid']."','".TIMENOW."')
					");
				}
			}
		}
		// update sample
		$shipmentDate=0;
		if($this->kclass->input['shipmentDate']!=''){
			$dd=explode('-',$this->kclass->input['shipmentDate']);
			$shipmentDate=mktime(0,0,0,$dd[1],$dd[2],$dd[0]);
		}
		if($this->kclass->input['supplierid']>0){
			$supplier=$this->kclass->DB->queryFirst("SELECT linkman FROM supplier WHERE supplierid={$this->kclass->input['supplierid']}");
			$linkman=$supplier['linkman'];
		}else{
			$linkman=$this->kclass->input['linkman'];
		}
		$this->kclass->DB->query("
			UPDATE `maverickpurchase` SET 
				`supplierid`='".$this->kclass->input['supplierid']."',
				`supplierTitle`='".$this->kclass->input['supplier']."',
				`applicant`='".$this->kclass->input['applicant']."',
				`linkman`='".$linkman."',
				`shipmethodid`='".$this->kclass->input['shipmethodid']."',
				`shipmentDate`='".$shipmentDate."',
				`departmentid`='".$this->kclass->input['departmentid']."',
				`detail`='".$this->kclass->input['detail']."',
				`remark`='".$this->kclass->input['remark']."',
				`modified`='".TIMENOW."',
				`modifier`='".$this->kclass->user['userid']."' 
			WHERE maverickpurchaseid='".$mp['maverickpurchaseid']."'
		");
		// fee & discount
		$this->kclass->updateFee(array('module'=>'maverickpurchase','mid'=>$mp['maverickpurchaseid']));
		$this->kclass->updateDiscount(array('module'=>'maverickpurchase','mid'=>$mp['maverickpurchaseid']));
		$this->kclass->updateAttachs(array('module'=>'maverickpurchase','mid'=>$mp['maverickpurchaseid']));
		$this->kclass->messager(array(
			'title' => '修改样品单',
			'text' => '样品单 <b>'.$mp['mpno'].'</b> 已修改成功!',
			'url' => '/s.php?module=maverickPurchase&action=view&maverickpurchaseid='.$mp['maverickpurchaseid'],
			'sec' => 2
		));
	}
	//
	function kill(){
		if($this->kclass->input['maverickpurchaseid']<=0){
			$this->kclass->boinkIt('/s.php?module=maverickPurchase');
		}
		if($this->kclass->input['confirm'] == 0){
			$this->kclass->boinkIt('/s.php?module=maverickPurchase');
		}
		if($this->kclass->input['maverickpurchaseid'] < 0){
			$e='<li>请从正确的页面进行操作，如果此问题持续存在，请联系网站管理员。';
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '删除零星采购',
				'text' => '您在删除零星采购的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		if($this->kclass->input['rt']=='view'){
			$rt='&action=view&maverickpurchaseid='.$this->kclass->input['maverickpurchaseid'];
		}elseif($this->kclass->input['rt']=='list'){
			$rt='#'.$this->kclass->input['maverickpurchaseid'];
		}
		$mp=$this->kclass->DB->queryFirst("
			SELECT mpno
			FROM maverickpurchase
			WHERE maverickpurchaseid='".$this->kclass->input['maverickpurchaseid']."'
		");
		if($mp){
			$this->kclass->DB->query("
				UPDATE `maverickpurchase`
				SET killed=".TIMENOW.",killer=".$this->kclass->user['userid']."
				WHERE maverickpurchaseid='".$this->kclass->input['maverickpurchaseid']."'
			");
			$this->kclass->messager(array(
				'title' => '删除零星采购成功',
				'text' => '零星采购 <b>'.$mp['mpno'].'</b> 已成功被标记为删除!',
				'url' => '/s.php?module=maverickPurchase'.$rt,
				'sec' => 3
			));
		}else{
			$this->kclass->messager(array(
				'title' => '删除零星采购失败',
				'text' => '您要删除的零星采购，不存在！',
				'url' => '/s.php?module=maverickPurchase'.$rt,
				'sec' => 3
			));
		}
	}

	//
	function remove(){
		if($this->kclass->input['maverickpurchaseid']<=0){
			$this->kclass->boinkIt('/s.php?module=maverickPurchase');
		}
		$mp=$this->kclass->DB->queryFirst("
			SELECT mpno
			FROM maverickpurchase
			WHERE maverickpurchaseid='".$this->kclass->input['maverickpurchaseid']."'
		");
$body=<<<EOF
<form action="/s.php?module=maverickPurchase&action=kill" name="maverickPurchase" method="post">
<input type="hidden" name="module" value="maverickPurchase">
<input type="hidden" name="action" value="kill">
<input type="hidden" name="maverickpurchaseid" value="{$this->kclass->input['maverickpurchaseid']}">
<table>
<thead>
<tr>
	<th>删除样品：{$mp['sampleno']}</th>
</tr>
</thead>
<tbody>
<tr class="even">
	<td class="middle">你确定要删除样品: <a href="/s.php?module=maverickPurchase&action=view&maverickpurchaseid={$this->kclass->input['maverickpurchaseid']}" class="big bold" target="_blank">{$mp['mpno']}</a> 吗?</td>
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
		$this->kclass->page['title'] .= ' - 删除 - '.$mp['mpno'];
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title' => $this->baseurl . ' - <a href="/s.php?module=maverickPurchase&action=view&maverickpurchaseid='.$mp['maverickpurchaseid'].'">'.$mp['mpno'].'</a> - 删除样品','right' => '<a href="/s.php?module=maverickPurchase">返回列表</a>','body'=>$body));
	}

	//
	function revival(){
		if($this->kclass->input['maverickpurchaseid']<=0){
			$this->kclass->boinkIt('/s.php?module=maverickPurchase');
		}
		if($this->kclass->input['confirm'] == 0){
			$this->kclass->boinkIt('/s.php?module=maverickPurchase');
		}
		if($this->kclass->input['psampleid'] < 0){
			$e='<li>请从正确的页面进行操作，如果此问题持续存在，请联系网站管理员。';
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '恢复零星采购',
				'text' => '您在恢复零星采购的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$mp=$this->kclass->DB->queryFirst("
			SELECT mpno
			FROM maverickpurchase
			WHERE maverickpurchaseid='".$this->kclass->input['maverickpurchaseid']."'
		");
		if($this->kclass->input['rt']=='view'){
			$rt='&action=view&maverickpurchaseid='.$this->kclass->input['maverickpurchaseid'];
		}elseif($this->kclass->input['rt']=='list'){
			$rt='#'.$this->kclass->input['maverickpurchaseid'];
		}
		if($mp){
			$this->kclass->DB->query("
				UPDATE `maverickpurchase`
				SET killed=0,killer=0
				WHERE maverickpurchaseid='".$this->kclass->input['maverickpurchaseid']."'
			");
			$this->kclass->messager(array(
				'title' => '恢复零星采购成功',
				'text' => '样品 <b>'.$mp['mpno'].'</b> 已成功被恢复！',
				'url' => '/s.php?module=maverickPurchase'.$rt,
				'sec' => 3
			));
		}else{
			$this->kclass->messager(array(
				'title' => '恢复零星采购失败',
				'text' => '您要恢复的零星采购不存在！',
				'url' => '/s.php?module=maverickPurchase'.$rt,
				'sec' => 3
			));
		}
	}

	//
	function restore(){
		if($this->kclass->input['maverickpurchaseid']<=0){
			$this->kclass->boinkIt('/s.php?module=maverickPurchase');
		}
		$mp=$this->kclass->DB->queryFirst("
			SELECT mpno
			FROM maverickpurchase
			WHERE maverickpurchaseid='".$this->kclass->input['maverickpurchaseid']."'
		");
$body=<<<EOF
<form action="/s.php?module=maverickPurchase&action=revival" name="maverickPurchase" method="post">
<input type="hidden" name="module" value="maverickPurchase">
<input type="hidden" name="action" value="revival">
<input type="hidden" name="maverickpurchaseid" value="{$this->kclass->input['maverickpurchaseid']}">
<table>
<thead>
<tr>
	<th>恢复样品：{$mp['mpno']}</th>
</tr>
</thead>
<tbody>
<tr class="even">
	<td class="middle">你确定要恢复零星采购: <a href="/s.php?module=maverickPurchase&action=view&maverickpurchaseid={$this->kclass->input['maverickpurchaseid']}" class="big bold" target="_blank">{$mp['mpno']}</a> 吗?</td>
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
		$this->kclass->page['title'] .= ' - 恢复 - '.$mp['mpno'];
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title' => $this->baseurl . ' - <a href="/s.php?module=maverickPurchase&action=view&maverickpurchaseid='.$mp['maverickpurchaseid'].'">'.$mp['mpno'].'</a> - 恢复零星采购','right' => '<a href="/s.php?module=maverickPurchase">返回列表</a>','body'=>$body));
	}
	/******************************************* private function ****************************************************/

	function _chooserType($b){
		if($b['width']){
			$width='style="width:'.$b['width'].'px"';
		}
		$r='<select name="'.$b['name'].'" id="'.$this->kclass->iif($b['id'],$b['id'],$b['name']).'" '.$width.'>';
		if($b['hasBlank']){
			$r.='<option value=0>'.$b['topName'].'</option>';
		}
		foreach($this->type AS $type){
			$r.='<option value="'.$type['id'].'"';
			if($type['id']==$b['selectedid']){
				$r.=' selected';
			}
			$r.='>'.'('.$type['abbr'].')'.$type['title'].'</option>';
		}
		$r.='</select>';
		return $r;
	}
}
?>