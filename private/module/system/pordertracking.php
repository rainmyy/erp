<?php
//
class porderTracking{
var $kclass;
//
function autorun(){
		$this->baseurl='<a href="/s.php?">首页</a>';
		switch($this->kclass->input['action']){
			case 'list':
				return $this->mmlist();
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
			case 'addPayment':
				return $this->addPayment();
				break;
			case 'insertPayment':
				return $this->insertPayment();
				break;
			case 'update':
				return $this->update();
				break;
			case 'doupdate':
				return $this->doupdate();
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
			case 'insertInbound':
				return $this->insertInbound();
				break;
			default:
				return $this->mmlist();

		}
	}
	// main page,shows all the inbound info list
	function mmlist(){
		$items=$this->kclass->DB->query("
			SELECT `item`.itemid,`item`.mid,`item`.materialid,`item`.quantity,`item`.dateline,`item`.amount,
				m.materialno,m.title AS material,m.standard,
				po.porderid,po.orderno,po.supplierid,po.created,
				u.title AS unit
			FROM `item` 
			LEFT JOIN `material` AS m ON (m.materialid=`item`.materialid)
			LEFT JOIN `porder` AS po ON (po.porderid=`item`.mid)
			LEFT JOIN `unit` AS u ON (u.unitid=`item`.unitid)
			WHERE `item`.module='porder' AND po.ifVerify=1 AND `item`.killed=0 
			ORDER BY po.created DESC
		");
		if($this->kclass->DB->numRows()){
			$body='<table class="hundred"><thead><th width="15">ID</th><th width="80">订单时间</th><th width="80">订单号</th><th width="200">供应商</th><th width="200">物资编号</th><th>应到时间</th><th width="60">应到数量</th><th width="60">已到数量</th><th>应付金额</th><th>已付金额</th><th></th></thead><tbody>';
			$i=1;
			while($item=$this->kclass->DB->fetchArray($items)){
				$supplier=$this->kclass->DB->queryFirst("SELECT supplierno,title AS supplier FROM supplier WHERE supplierid='".$item['supplierid']."' LIMIT 0,1");
				$countTracking=$this->kclass->DB->queryFirst("
					SELECT SUM(ordertracking.quantity) AS arrivalCount 
					FROM ordertracking 
					WHERE ordertracking.killed=0 AND ordertracking.module='porder' AND ordertracking.itemid='".$item['itemid']."'
					LIMIT 0,1
				");
				$amountTracking=$this->kclass->DB->queryFirst("
					SELECT SUM(ordertracking.amount) AS amount 
					FROM ordertracking 
					LEFT JOIN payment ON (ordertracking.paymentid=payment.paymentid) 
					WHERE ordertracking.killed=0 AND ordertracking.module='porder' AND ordertracking.itemid='".$item['itemid']."' AND payment.killed=0 
					LIMIT 0,1
				");
				if($item['quantity']>$countTracking['arrivalCount'] OR $item['amount']>$amountTracking['amount']){
					$body.='<tr class="'.$this->kclass->rotateLine().'">
						<td>'.$i.'</td>
						<td>'.date('Y-m-d',$item['created']).'</td>
						<td><a href="/s.php?module=porder&action=view&porderid='.$item['porderid'].'" class="small">'.$item['orderno'].'</a></td>
						<td><a href="/s.php?module=psupplier&action=view&supplierid='.$item['supplierid'].'" class="small">('.$supplier['supplierno'].')'.$this->kclass->iif(strlen($supplier['supplier'])>12,mb_substr($supplier['supplier'],0,12, 'UTF-8').'...',$supplier['supplier']).'</td>
						<td title=""><a href="/s.php?module=material&action=view&materialid='.$item['materialid'].'">'.$item['materialno'].'</a><br>
						<span class="small">名称：'.$this->kclass->iif(strlen($item['material'])>12,mb_substr($item['material'],0,12, 'UTF-8').'...',$item['material']).'</span><br><span class="small">规格：'.$this->kclass->iif(strlen($item['standard'])>12,mb_substr($item['standard'],0,12, 'UTF-8').'...',$item['standard']).'</span></td>
						<td>'.date('Y-m-d',$item['dateline']).'</td>
						<td>'.$item['quantity'].$item['unit'].'</td>
						<td>'.$this->kclass->iif($countTracking['arrivalCount']=='',0,$countTracking['arrivalCount']).$item['unit'].'</td>
						<td>'.$item['amount'].'</td>
						<td>'.$this->kclass->iif($amountTracking['amount'],$amountTracking['amount'],0).'</td>
						<td><a href="/s.php?module=porderTracking&action=view&itemid='.$item['itemid'].'">查看记录</a>'./*<br><a href="/s.php?module=porderTracking&action=add&itemid='.$item['itemid'].'">新增到货记录</a><br><a href="/s.php?module=accounting&action=addDebitnote&porderid='.$item['porderid'].'">应付</a>　<a href="/s.php?module=accounting&action=addPayment&porderid='.$item['porderid'].'">已付</a>*/'</td>
					</tr>';
				}
				$i++;
			}
			$body.='</tbody></table>';
		}else{
			$body='暂无相关记录。';
		}
		$this->kclass->page['onload']='dc.tabhover();';
		$this->kclass->page['title']='物资跟踪记录';
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 列表', 'body'=>$body));
	}
	//
	function view(){
		$item=$this->kclass->DB->queryFirst("
			SELECT `item`.itemid,`item`.quantity AS total,`item`.amount,`item`.dateline,
				`material`.materialno,`material`.title AS material,`material`.standard,
				`porder`.porderid,`porder`.orderno,`porder`.supplierid,`porder`.created,
				`unit`.title AS unit
			FROM `item`
			LEFT JOIN `material` ON (`material`.materialid=`item`.materialid)
			LEFT JOIN `unit` ON (`unit`.unitid=`item`.unitid)
			LEFT JOIN `porder` ON (`porder`.porderid=`item`.mid)
			WHERE `item`.killed=0 AND `item`.module='porder' AND `item`.itemid='".$this->kclass->input['itemid']."'
			ORDER BY `item`.created ASC
		");
		$body='<table class="hundred"><thead><th width="30">订单时间</th><th width="80">订单号</th><th width="150">供应商</th><th width="70">物资编号</th><th width="120">物资名称</th><th width="130">规格</th><th>应到时间</th><th width="60">应到数量</th><th width="60">已到数量</th><th width="60">应付金额</th><th width="60">已付金额</th></thead><tbody>';
		$supplier=$this->kclass->DB->queryFirst("SELECT supplierno,title AS supplier FROM supplier WHERE supplierid='".$item['supplierid']."' LIMIT 0,1");
		$countTracking=$this->kclass->DB->queryFirst("
			SELECT SUM(ordertracking.quantity) AS arrivalCount 
			FROM ordertracking 
			WHERE ordertracking.killed=0 AND ordertracking.module='porder' AND ordertracking.itemid='".$item['itemid']."'
			LIMIT 0,1
		");
		$amountTracking=$this->kclass->DB->queryFirst("
			SELECT SUM(ordertracking.amount) AS amount 
			FROM ordertracking 
			LEFT JOIN payment ON (ordertracking.paymentid=payment.paymentid) 
			WHERE ordertracking.killed=0 AND ordertracking.module='porder' AND ordertracking.itemid='".$item['itemid']."' AND payment.killed=0 
			LIMIT 0,1
		");
		$unArrivalCount=$item['total']-$countTracking['arrivalCount'];
		$body.='<tr '.$this->kclass->rotateLine().'>
			<td>'.date('Y-m-d',$item['created']).'</td>
			<td><a href="/s.php?module=porder&action=view&porderid='.$item['porderid'].'" class="small">'.$item['orderno'].'</a></td>
			<td><a href="/s.php?module=psupplier&action=view&supplierid='.$item['supplierid'].'" class="small">('.$supplier['supplierno'].')'.$this->kclass->iif(strlen($supplier['supplier'])>12,mb_substr($supplier['supplier'],0,12, 'UTF-8').'...',$supplier['supplier']).'</td>
			<td><a href="/s.php?module=material&action=view&materialid='.$item['materialid'].'">'.$item['materialno'].'</a></td>
			<td><span class="small">'.$this->kclass->iif(strlen($item['material'])>12,mb_substr($item['material'],0,12, 'UTF-8').'...',$item['material']).'</span></td>
			<td><span class="small">'.$this->kclass->iif(strlen($item['standard'])>12,mb_substr($item['standard'],0,12, 'UTF-8').'...',$item['standard']).'</span></td>
			<td>'.date('Y-m-d',$item['dateline']).'</td>
			<td>'.$item['total'].$item['unit'].'</td>
			<td>'.$this->kclass->iif($countTracking['arrivalCount']=='',0,$countTracking['arrivalCount']).$item['unit'].'</td>
			<td>'.$item['amount'].'</td>
			<td>'.$this->kclass->iif($amountTracking['amount']=='',$amountTracking['amount'],0).'</td></tr>';
		$orderTrackings=$this->kclass->DB->query("
			SELECT `ordertracking`.*,
				`material`.materialno,`material`.title,`material`.standard,
				`unit`.title AS unit
			FROM `ordertracking`
			LEFT JOIN `material` ON (`material`.materialid=`ordertracking`.materialid)
			LEFT JOIN `unit` ON (`unit`.unitid=`ordertracking`.unitid)
			WHERE `ordertracking`.killed=0 AND `ordertracking`.module='porder' AND `ordertracking`.itemid='".$this->kclass->input['itemid']."'
			ORDER BY `ordertracking`.created ASC
		");
		if($this->kclass->DB->numRows()){
			$i=1;
			$j=1;
			while($orderTracking=$this->kclass->DB->fetchArray($orderTrackings)){
				$barcode=$this->kclass->DB->queryFirst("SELECT barcodeid FROM barcode WHERE materialid='".$orderTracking['materialid']."' AND porderid='".$orderTracking['mid']."' AND arrivalTime='".$orderTracking['dateline']."'");
				$addBarcode=$this->kclass->iif($barcode,'<a href="/s.php?module=barcode&action=view&barcodeid='.$barcode['barcodeid'].'">查看条码</a>',''/*保留'<a href="/s.php?module=barcode&action=add&porderid='.$item['porderid'].'&trackingid='.$orderTracking['trackingid'].'">生成条码</a> <a href="/s.php?module=porderTracking&action=update&trackingid='.$orderTracking['trackingid'].'">修改<a> <a href="/s.php?module=porderTracking&action=removeQuantity&trackingid='.$orderTracking['trackingid'].'" onclick="return confirm(\'你确定要删除这条记录吗？(删除不可恢复！)\')">删除</a>'*/);
				if($orderTracking['paymentid']==0){
					$materialtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').'">
						<td width="20">'.$i.'</td>
						<td width="50"><a href="/s.php?module=material&action=view&materialid='.$orderTracking['materialid'].'">'.$orderTracking['materialno'].'</a></td>
						<td>'.$orderTracking['title'].'</td>
						<td>'.$orderTracking['standard'].'</td>
						<td align="right" width="80">'.number_format($orderTracking['quantity']).' '.$orderTracking['unit'].'</td>
						<td align="right" width="80">'.date('Y-m-d',$orderTracking['dateline']).'</td>
						<td align="right" width="130">'.$addBarcode.'</td></tr>';
					$i++;
				}else{
					$payment=$this->kclass->DB->queryFirst("SELECT paymentid FROM payment WHERE killed=0 AND paymentid='".$orderTracking['paymentid']."'");
					if($payment['paymentid']){
						$amounttr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').'">
							<td width="20">'.$j.'</td>
							<td width="50"><a href="/s.php?module=material&action=view&materialid='.$orderTracking['materialid'].'">'.$orderTracking['materialno'].'</a></td>
							<td>'.$orderTracking['title'].'</td>
							<td>'.$orderTracking['standard'].'</td>
							<td align="right" width="80">'.number_format($orderTracking['amount'],2).'</td>
							<td align="right" width="80">'.date('Y-m-d',$orderTracking['dateline']).'</td>
							<td align="right" width="130"><a href="/s.php?module=accounting&action=updatePayment&paymentid='.$orderTracking['paymentid'].'&step=item">修改<a> <a href="/s.php?module=porderTracking&action=removeQuantity&trackingid='.$orderTracking['trackingid'].'" onclick="return confirm(\'你确定要删除这条记录吗？(删除不可恢复！)\')">删除</a></td></tr>';
					}
					$j++;
				}
			}
		}
		if($materialtr==''){
			$materialtr='<tbody><tr><td colspan=10>暂无记录</td></tr></tbody>';
		}
		if($amounttr==''){
			$amounttr='<tbody><tr><td colspan=10>暂无记录</td></tr></tbody>';
		}
$body.=<<<EOF
<table class="hundred"><thead><tr><th colspan=10>已到物资</th></tr></thead><tbody><tr class="even bold center"><td>ID</td><td>编号</td><td>名称</td><td>规格</td><td>已到数量</td><td>到货时间</td><td>操作</td></tr>{$materialtr}</tbody></table>
<table class="hundred"><thead><tr><th colspan=10>已付金额</th></tr></thead><tbody><tr class="even bold center"><td>ID</td><td>编号</td><td>名称</td><td>规格</td><td>已付金额</td><td>付款时间</td><td>操作</td></tr>{$amounttr}</tbody></table>
EOF;
		$this->kclass->tbline+=4;
		$this->kclass->page['title'].='查看物资到货记录';
		$this->kclass->page['onload'].='dc.tabhover();dc.tabs({\'id\':\'stock\'});';
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - <a href="/s.php?module=porderTracking">列表</a> - 查看物资到货记录','right'=>'<span class="small gray">新建：</span>'./*<a href="/s.php?module=porderTracking&action=add&itemid='.$item['itemid'].'">到货记录</a>　.*/'<a href="/s.php?module=accounting&action=addDebitnote&porderid='.$item['porderid'].'">应付</a>　<a href="/s.php?module=accounting&action=addPayment&porderid='.$item['porderid'].'">已付</a>','body'=>$body));
	}

	//
	function add(){
		$items=$this->kclass->DB->query("
			SELECT i.itemid,i.mid AS porderid,i.materialid,i.quantity,i.unitid,i.package,i.price,i.amount,i.discount,i.dateline,
				m.materialno,m.title,m.standard,
				u.title AS unit,
				br.title AS brand
			FROM `item` AS i
			LEFT JOIN `material` AS m ON (m.materialid=i.materialid)
			LEFT JOIN `unit` AS u ON (u.unitid=i.unitid)
			LEFT JOIN `brand` AS br ON (br.brandid=i.brandid)
			WHERE i.killed=0 AND i.module='porder' AND i.mid='".$this->kclass->input['porderid']."'
			ORDER BY itemid ASC
		");
		if($counter=$this->kclass->DB->numRows()){
			$i=1;
			while($item=$this->kclass->DB->fetchArray($items)){
				$tracking=$this->kclass->DB->queryFirst("SELECT SUM(quantity) AS arrivalCount FROM ordertracking WHERE killed=0 AND module='porder' AND itemid='".$item['itemid']."' LIMIT 0,1");
				if($tracking['arrivalCount']!=$item['quantity']){
					$arrivalItem.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').' small" nohover>
					<input type="hidden" name="itemId['.$i.']" value="'.$item['itemid'].'" />
					<input type="hidden" name="itemMaterialid['.$i.']" value="'.$item['materialid'].'">
					<td><a href="/s.php?module=material&action=view&materialid='.$item['materialid'].'">'.$item['materialno'].'</a></td>
					<td>'.$item['title'].'</td>
					<td>'.$item['standard'].$item['unit'].'</td>
					<td>'.$item['quantity'].$item['unit'].'</td>
					<td>'.$this->kclass->iif($tracking['arrivalCount'],$tracking['arrivalCount'],0).$item['unit'].'</td>
					<td><input type="text" name="itemQuantity['.$i.']" value="0" /></td>
					<td><input type="text" name="itemRemark['.$i.']" style="width:250px;" /></td></tr>';
					$i++;
				}
			}
		}
		$nowTime=date('Y-m-d',TIMENOW);
		$body=<<<EOF
<form action="/s.php?module=porderTracking&action=insert" name="porderTracking" method="post">
<input type="hidden" name="module" value="porderTracking">
<input type="hidden" name="action" value="insert">
<input type="hidden" name="porderid" value="{$this->kclass->input['porderid']}">
<table class="hundred">
<thead><tr><th colspan=8>新建物资跟踪记录</th></tr></thead>
<tbody>
<tr class="odd"><td width="80">到货单号：</td><td width="450"><input type="text" name="arrivalno" value="" /></td><td width="100">到货时间： <span class="bold red">*</span></td><td><input type="text" name="dateline" id="dateline" value="{$nowTime}" size="30" /></td></tr>
</tbody><table class="hundred"><tbody>
<tr class="center even"><td>物资编号</td><td>物资名称</td><td>规格</td><td>应到数量</td><td>已到数量</td><td width="120">到货数量 <span class="bold red">*</span></td><td width="250">备注</td></tr></tbody>
{$arrivalItem}
<tr class="center"><td colspan=8><input type="submit" value=" 提交 "><input type="reset" value=" 重置 "></td></tr>
</table>
</form>
EOF;
		$this->kclass->page['title'].='订单物资跟踪';
		$this->kclass->page['onload'].='dc.tabhover();$(\'#dateline\').datepicker()';
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - <a href="/s.php?module=porderTracking">列表</a> - 新建物资跟踪记录', 'right'=>'<a href="/s.php?module=porderTracking">返回列表</a>　|　<a href="/s.php?module=porder&action=view&porderid='.$this->kclass->input['porderid'].'">返回订单</a>','body'=>$body));
	}
	// insert stock
	function insert(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			if($this->kclass->input['dateline']==''){
				$e='<li>请输入到货记录的 到货时间。</li>';
			}
			$hasItem=0;
			$j=count($this->kclass->input['itemId']);
			for($i=1;$i<$j+1;$i++){
				if(intval($this->kclass->input['itemQuantity'][$i])>0){
					$hasItem=1;
				}
			}
			if($hasItem==0){
				$e.='<li>请填写 至少一条 到货跟踪记录。</li>';
			}
			$porder=$this->kclass->DB->queryFirst("SELECT orderno FROM porder WHERE killed=0 AND porderid='".$this->kclass->input['porderid']."'");
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '新建物资跟踪记录',
				'text' => '您在新建物资跟踪记录的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		if($this->kclass->input['dateline']!=''){
			$sd=explode('-',$this->kclass->input['dateline']);
			$dateline=mktime(0,0,0,$sd[1],$sd[2],$sd[0]);
		}else{
			$dateline=0;
		}
		for($m=1;$m<$j+1;$m++){
			if(intval($this->kclass->input['itemQuantity'][$m])>0){
				$this->kclass->input['itemQuantity'][$m]=intval($this->kclass->input['itemQuantity'][$m]);
				$material=$this->kclass->DB->queryFirst("SELECT materialid,materialno,unitid FROM material WHERE materialid='".$this->kclass->input['itemMaterialid'][$m]."'");
				$this->kclass->DB->query("
					INSERT INTO ordertracking
						(`module`,`mid`,`itemid`,`materialid`,`arrivalno`,`quantity`,`unitid`,`dateline`,`remark`,`created`,`creator`)
					VALUES
						('porder','".$this->kclass->input['porderid']."','".$this->kclass->input['itemId'][$m]."','".$this->kclass->input['itemMaterialid'][$m]."','".$this->kclass->input['arrivalno']."','".$this->kclass->input['itemQuantity'][$m]."','".$material['unitid']."','".$dateline."','".$this->kclass->input['itemRemark'][$m]."','".TIMENOW."','".$this->kclass->user['userid']."')
				");
			}
		}
		$this->kclass->messager(array(
			'title' => '新建物资跟踪记录',
			'text' => '订单 <b>'.$porder['orderno'].'</b> 跟踪记录 已新建成功!',
			'url' => '/s.php?module=porder&action=view&porderid='.$this->kclass->input['porderid'],
			'sec' => 2
		));
	}

	//
	function update(){
		$items=$this->kclass->DB->query("
			SELECT ot.*,
				i.itemid,i.quantity AS total,
				m.materialno,m.title,m.standard,
				po.porderid,po.orderno,
				u.title AS unit
			FROM ordertracking AS ot
			LEFT JOIN `item` AS i ON (i.itemid=ot.itemid)
			LEFT JOIN `material` AS m ON (m.materialid=ot.materialid)
			LEFT JOIN `porder` AS po ON (po.porderid=ot.mid)
			LEFT JOIN `unit` AS u ON (u.unitid=ot.unitid)
			WHERE ot.killed=0 AND ot.module='porder' AND ot.mid='".$this->kclass->input['porderid']."' AND ot.created='".$this->kclass->input['created']."'
		");
		if($this->kclass->DB->numRows()){
			$i=1;
			while($item=$this->kclass->DB->fetchArray($items)){
				$tracking=$this->kclass->DB->queryFirst("SELECT SUM(quantity) AS arrivalCount FROM ordertracking WHERE killed=0 AND module='porder' AND itemid='".$item['itemid']."' LIMIT 0,1");
				if($tracking['arrivalCount']!=$item['quantity']){
					$itemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').' small" nohover>
					<input type="hidden" name="itemId['.$i.']" value="'.$item['itemid'].'" />
					<input type="hidden" name="trackingid['.$i.']" value="'.$item['trackingid'].'" />
					<input type="hidden" name="itemMaterialid['.$i.']" value="'.$item['materialid'].'">
					<td><a href="/s.php?module=material&action=view&materialid='.$item['materialid'].'">'.$item['materialno'].'</a></td>
					<td>'.$item['title'].'</td>
					<td>'.$item['standard'].$item['unit'].'</td>
					<td>'.$item['total'].$item['unit'].'</td>
					<td>'.$tracking['arrivalCount'].$item['unit'].'</td>
					<td><input type="text" name="itemQuantity['.$i.']" value="'.$item['quantity'].'" /></td>
					<td><input type="text" name="itemRemark['.$i.']" style="width:250px;" value="'.$item['remark'].'" /></td></tr>';
					$i++;
				}
			}
		}else{
			$this->kclass->boinkIt(REFERFER);
		}
		$porderTracking=$this->kclass->DB->queryFirst("SELECT DISTINCT arrivalno,dateline FROM ordertracking WHERE killed=0 AND module='porder' AND mid='".$this->kclass->input['porderid']."' AND created='".$this->kclass->input['created']."'");
		$porder=$this->kclass->DB->queryFirst("SELECT porderid,orderno FROM porder WHERE killed=0 AND porderid='".$this->kclass->input['porderid']."'");
		$created=date('Y-m-d',$porderTracking['dateline']);
		$body=<<<EOF
<form action="/s.php?module=porderTracking&action=doupdate" name="porderTracking" method="post">
<input type="hidden" name="module" value="porderTracking">
<input type="hidden" name="action" value="doupdate">
<input type="hidden" name="porderid" value="{$this->kclass->input['porderid']}">
<input type="hidden" name="created" value="{$this->kclass->input['created']}">
<table class="hundred">
<thead><tr><th colspan=8>修改物资跟踪记录</th></tr></thead>
<tbody>
<tr class="odd"><td width="80">到货单号：</td><td width="450"><input type="text" name="arrivalno" value="{$porderTracking['arrivalno']}" /></td><td width="100">到货时间： <span class="bold red">*</span></td><td><input type="text" name="dateline" id="dateline" value="{$created}" size="30" /></td></tr>
</tbody><table class="hundred"><tbody>
<tbody><tr class="center even"><td>物资编号</td><td>物资名称</td><td>规格</td><td>应到数量</td><td>已到数量</td><td width="120">到货数量 <span class="bold red">*</span></td><td width="250">备注</td></tr></tbody>
{$itemtr}
<tr class="center"><td colspan=8><input type="submit" value=" 提交 "><input type="reset" value=" 重置 "></td></tr>
</table>
</form>
EOF;
		$this->kclass->page['title'].='修改订单到货记录 - '.$porder['orderno'];
		$this->kclass->page['onload'].='dc.tabhover();$(\'#dateline\').datepicker()';
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - <a href="/s.php?module=porderTracking">列表</a> - 修改订单到货记录 - '.$porder['orderno'], 'right'=>'<a href="/s.php?module=porderTracking">返回列表</a>　|　<a href="/s.php?module=porder&action=view&porderid='.$this->kclass->input['porderid'].'">查看订单</a>','body'=>$body));

	}
	//
	function doupdate(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			if($this->kclass->input['dateline']==''){
				$e='<li>请输入到货记录的 到货时间。</li>';
			}
			$hasItem=0;
			$j=count($this->kclass->input['itemId']);
			for($i=1;$i<$j+1;$i++){
				if(intval($this->kclass->input['itemQuantity'][$i])>0){
					$hasItem=1;
				}
			}
			if($hasItem==0){
				$e.='<li>请填写 至少一条 到货跟踪记录。</li>';
			}
			$porder=$this->kclass->DB->queryFirst("SELECT orderno FROM porder WHERE killed=0 AND porderid='".$this->kclass->input['porderid']."'");
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '修改物资跟踪记录',
				'text' => '您在修改物资跟踪记录的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		if($this->kclass->input['dateline']!=''){
			$sd=explode('-',$this->kclass->input['dateline']);
			$dateline=mktime(0,0,0,$sd[1],$sd[2],$sd[0]);
		}else{
			$dateline=0;
		}
		for($m=1;$m<$j+1;$m++){
			if(intval($this->kclass->input['itemQuantity'][$m])>0){
				$this->kclass->input['itemQuantity'][$m]=intval($this->kclass->input['itemQuantity'][$m]);
				$material=$this->kclass->DB->queryFirst("SELECT materialid,materialno,unitid FROM material WHERE materialid='".$this->kclass->input['itemMaterialid'][$m]."'");
				$this->kclass->DB->query("
					UPDATE ordertracking SET	
						`arrivalno`='".$this->kclass->input['arrivalno']."',
						`quantity`='".$this->kclass->input['itemQuantity'][$m]."',
						`dateline`='".$dateline."',
						`remark`='".$this->kclass->input['itemRemark'][$m]."',
						`modified`='".TIMENOW."',
						`modifier`='".$this->kclass->user['userid']."'
					WHERE trackingid='".$this->kclass->input['trackingid'][$m]."'
				");
			}
		}

		$this->kclass->messager(array(
			'title' => '修改订单物资跟踪记录',
			'text' => '订单 <b>'.$porder['orderno'].'</b> 跟踪记录 已修改成功!',
			'url' => '/s.php?module=porder&action=view&porderid='.$this->kclass->input['porderid'],
			'sec' => 2
		));
	}
	//
	function killQuantity(){
		if($this->kclass->input['trackingid']<=0){
			$this->kclass->boinkIt('/s.php?module=porderTracking');
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
			$this->kclass->boinkIt('/s.php?module=porderTracking&action=view&itemid='.$tracking['itemid']);
		}

		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '删除物资跟踪记录',
				'text' => '您在删除物资跟踪记录的过程中有以下错误：<ul>'.$e.'</ul>',
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
				'title' => '删除物资跟踪记录成功',
				'text' => '该条物资跟踪记录 已成功被标记为删除!',
				'url' => '/s.php?module=porderTracking&action=view&itemid='.$tracking['itemid'],
				'sec' => 2
			));
		}else{
			$this->kclass->messager(array(
				'title' => '删除物资跟踪记录失败',
				'text' => '您要删除的物资跟踪记录，不存在！',
				'url' => '/s.php?module=porderTracking&action=view&itemid='.$tracking['itemid'],
				'sec' => 3
			));
		}
	}

	//
	function removeQuantity(){
		if($this->kclass->input['trackingid']<=0){
			$this->kclass->boinkIt('/s.php?module=porderTracking');
		}
		$tracking=$this->kclass->DB->queryFirst("
			SELECT trackingid,itemid
			FROM orderTracking
			WHERE trackingid='".$this->kclass->input['trackingid']."'
		");
$body = <<<EOF
<form action="/s.php?module=porderTracking&action=killQuantity" name="porderTracking" method="post">
<input type="hidden" name="module" value="porderTracking">
<input type="hidden" name="action" value="killQuantity">
<input type="hidden" name="trackingid" value="{$this->kclass->input['trackingid']}">
<table><thead>
<thead>
<tr>
<th>删除物资跟踪记录</th>
</tr>
</thead>
<tbody>
<tr class="even">
<td class="middle">你确定要删除这条物资跟踪记录吗?</td>
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
		$this->kclass->page['title'] .= ' - 删除物资跟踪记录 ';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 删除物资跟踪记录 ', 'right' => '<a href="/s.php?module=porderTrackingmodule=porderTracking&action=view&itemid='.$tracking['itemid'].'">返回查看物资跟踪记录</a>', 'body'=>$body));
	}

	//
	function revivalQuantity(){
		if($this->kclass->input['trackingid']<=0){
			$this->kclass->boinkIt('/s.php?module=porderTracking');
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
			$this->kclass->boinkIt('/s.php?module=porderTracking&action=view&itemid='.$tracking['itemid']);
		}

		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '恢复物资跟踪记录',
				'text' => '您在恢复物资跟踪记录的过程中有以下错误：<ul>'.$e.'</ul>',
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
				'title' => '恢复物资跟踪记录成功',
				'text' => '该条 物资跟踪记录 已成功被恢复！',
				'url' => '/s.php?module=porderTracking&action=view&itemid='.$tracking['itemid'],
				'sec' => 2
			));
		}else{
			$this->kclass->messager(array(
				'title' => '恢复物资跟踪记录失败',
				'text' => '您要恢复的物资跟踪记录不存在！',
				'url' => '/s.php?module=porderTracking&action=view&itemid='.$tracking['itemid'],
				'sec' => 3
			));
		}
	}

	//
	function restoreQuantity(){
		if($this->kclass->input['stockid']<=0){
			$this->kclass->boinkIt('/s.php?module=porderTracking');
		}
		$tracking=$this->kclass->DB->queryFirst("
			SELECT trackingid,itemid
			FROM orderTracking
			WHERE trackingid='".$this->kclass->input['trackingid']."'
		");
$body = <<<EOF
<form action="/s.php?module=porderTracking&action=revival" name="porderTracking" method="post">
<input type="hidden" name="module" value="porderTracking">
<input type="hidden" name="action" value="revivalQuantity">
<input type="hidden" name="trackingid" value="{$this->kclass->input['trackingid']}">
<table><thead>
<thead>
<tr>
<th>恢复物资跟踪记录</th>
</tr>
</thead>
<tbody>
<tr class="even">
<td class="middle">你确定要恢复这条物资跟踪记录吗?</td>
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
		$this->kclass->page['title'] .= ' - 恢复物资跟踪记录';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 恢复物资跟踪记录 ', 'right' => '<a href="/s.php?module=porderTracking">返回列表</a>', 'body'=>$body));
	}
	
	// 
	function insertInbound(){
		$porder=$this->kclass->DB->queryFirst("SELECT porderid,orderno,supplierid FROM porder WHERE killed=0 AND porderid='".$this->kclass->input['porderid']."'");
		$porderTracking=$this->kclass->DB->queryFirst("SELECT DISTINCT arrivalno,dateline FROM ordertracking WHERE killed=0 AND module='porder' AND mid='".$this->kclass->input['porderid']."' AND created='".$this->kclass->input['created']."'");
		if($e){
			$this->kclass->messager(array(
				'title' => '新增至入库申请单',
				'text' => '您在新增至入库申请单的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$department=$this->kclass->DB->queryFirst("SELECT departmentid,departmentno,parentid FROM department WHERE departmentid=6");
		$year=date('Y',TIMENOW);
		$month=date('n',TIMENOW);
		$start=strtotime(date('Y-m-d', mktime(0,0,0,$month,1,$year)));
		$end=strtotime(date('Y-m-t', mktime(0,0,0,$month,1,$year)));
		$counter=$this->kclass->DB->queryFirst("SELECT COUNT(*) AS count FROM inbound WHERE created>='".$start."' AND created<='".$end."' AND ifVerify<>-1 AND departmentid='".$department['departmentid']."'");
		if($department['parentid']==-1){//给没有下属部门的部门编号加为01
			$department['departmentno']=$department['departmentno'].'01';
		}
		$inboundno=$this->kclass->id(array('no'=>$department['departmentno'],'inbound'=>TIMENOW,'number'=>$counter['count']));
		$this->kclass->DB->query("
			INSERT INTO inbound 
				(inboundno,departmentid,typeid,porderid,supplierid,arrivalno,applicant,purpose,arrivalTime,ifConfirm,confirmer,confirmed,created,creator)
			VALUES ('".$inboundno."',6,4,'".$porder['porderid']."','".$porder['supplierid']."','".$porderTracking['arrivalno']."','".$this->kclass->user['realname']."','收料','".$porderTracking['dateline']."',1,'".$this->kclass->user['userid']."','".TIMENOW."','".TIMENOW."','".$this->kclass->user['userid']."')
		");
		$inboundid=$this->kclass->DB->insertID();
		
		$porderTrackings=$this->kclass->DB->query("
			SELECT trackingid,materialid,quantity,unitid,dateline,remark 
			FROM orderTracking 
			WHERE killed=0 AND module='porder' AND mid='".$porder['porderid']."' AND created='".$this->kclass->input['created']."'
		");
		if($this->kclass->DB->numRows()){
			while($porderTracking=$this->kclass->DB->fetchArray($porderTrackings)){
				$this->kclass->DB->query("
					INSERT INTO preinbounditem (`module`,`mid`,`materialid`,`version`,`qualified`,`disqualified`,`unitid`,`dateline`,`change`,`remark`,`created`,`creator`)
					VALUES ('inbound','".$inboundid."','".$porderTracking['materialid']."','V01','".$porderTracking['quantity']."',0,'".$porderTracking['unitid']."','".$porderTracking['dateline']."','','".$this->kclass->input['itemRemark'][$i]."','".TIMENOW."','".$this->kclass->user['userid']."')
				");
				$this->kclass->DB->query("
					UPDATE orderTracking SET 
						inboundid='".$inboundid."',
						modifier='".$this->kclass->user['userid']."',
						modified='".TIMENOW."'
					WHERE killed=0 AND trackingid='".$porderTracking['trackingid']."'
				");
			}
		}
		$this->kclass->messager(array(
			'title' => '新增至入库申请单',
			'text' => '采购订单 【<b>'.$porder['orderno'].'</b>】 生成入库申请单<b>'.$inboundno.'</b>，返回查看采购订单',
			'url' => '/s.php?module=porder&action=list&porderid='.$porder['porderid'],
			'sec' => 2
		));
	}
}
?>