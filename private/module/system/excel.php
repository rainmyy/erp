<?php
/**
 * 导出EXCEL文件
 * @author Huangjinlong 2014-12-13
 */
class excel{
	var $kclass;
	var $purchaseType=array(array('entitle'=>'normal','title'=>'普通采购'),array('entitle'=>'entrust','title'=>'加工采购'));
	function autoRun(){
		switch($this->kclass->input['action']){
			case 'porder':
				$this->porder();
			break;
			case 'material':
				$this->material();
			break;
			default:
		}
	}
	//采购单列表
	function porder(){
		if($this->kclass->input['exportType']==''){
			$title = '错误：未选择导出类型！';
		}	
		$exportType = $this->kclass->input['exportType'];
		$condition = $this->kclass->input['condition'];	
		$keywords = $this->kclass->input['keywords'];
		$filter = $this->kclass->input['filter'];
		$orderby = $this->kclass->input['orderby'];	
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
			WHERE ".$condition.$keywords.$filter."
			ORDER BY ".$orderby."
		");
		if($this->kclass->DB->numRows()){
			if($exportType=="a"){
				$title = "采购列表";
				$body .='<table class="hundred">
							<thead>
								<th width="30">ID</th>
								<th width="90">订单号</th>
								<th width="70">采购类型</th>
								<th width="100">供应商</th>
								<th>订单物资</th>
								<th width="30">币种</th>
								<th width="60">总额</th>
								<th width="60">应付</th>
								<th width="60">已付</th>
								<th width="60">申请时间</th>
							</thead>
						<tbody>';
			}else if($exportType=="b"){
				$title = "采购列表详细";
				$body .='<table class="hundred">
							<thead>
								<th width="30">ID</th>
								<th width="100">订单号</th>
								<th width="80">采购类型</th>
								<th width="100">供应商</th>
								<th width="100">申请时间</th>
								<th width="90">物资编号</th>
								<th width="90">物资名称</th>
								<th width="250">物资规格</th>
								<th width="60">订购数量</th>
								<th width="60">单价</th>
								<th width="60">费用</th>
								<th width="100">预计交期</th>
							</thead>
						<tbody>';			
			}
			
			$i=1;
			while($order=$this->kclass->DB->fetchArray($orders)){
				$alarm='';
				$shipmentDate=date('Y-m-d',$order['shipmentDate']);
				$paymentDate=date('Y-m-d',$order['paymentDate']);
				$order['created']=date('Y-m-d',$order['created']);
				foreach($this->purchaseType AS $key=>$val){
					if($order['purchaseType']==$val['entitle']){
						$purchaseType=$val['title'];
					}
				}
				// 订单物资
				$orderItem='';
				$items=$this->kclass->DB->query("SELECT i.quantity,i.price,i.price1,i.amount,i.dateline,
													m.materialno,m.title,m.standard
												FROM `item` AS i
												LEFT JOIN material AS m ON (m.materialid=i.materialid)
												WHERE module='porder' AND mid='".$order['porderid']."'
												");
				if($exportType=="a"){
					if($this->kclass->DB->numRows()){
						while($item=$this->kclass->DB->fetchArray($items)){
							$orderItem.=$item['title'].' ；';
						}
					}
					$total=$this->kclass->DB->queryFirst("SELECT SUM(amount) AS amount FROM item WHERE killed=0 AND module='porder' AND mid='".$order['porderid']."' LIMIT 0,1");// 总金额
					$payment=$this->kclass->DB->queryFirst("SELECT SUM(ordertracking.amount) AS amount FROM ordertracking LEFT JOIN payment ON (ordertracking.paymentid=payment.paymentid) WHERE ordertracking.killed=0 AND payment.killed=0 AND ordertracking.paymentid<>0 AND ordertracking.module='porder' AND ordertracking.mid='".$order['porderid']."' LIMIT 0,1");// 已付
					$debitnote=$total['amount']-$payment['amount'];// 应付
					$body .='<tr class="'.$this->kclass->rotateLine().' '.$alarm.' small">
						<td align="center">'.$i.'</td>
						<td>'.$order['orderno'].'</td>
						<td>'.$purchaseType.'</td>
						<td '.$order['abbr'].'</td>
						<td>'.$orderItem.'</td>
						<td align="right">'.$order['currency'].'</td>
						<td align="right">'.$total['amount'].'</td>
						<td align="right">'.$this->kclass->iif($debitnote<0,0,$debitnote).'</td>
						<td align="right">'.$this->kclass->iif($payment['amount'],$payment['amount'],0).'</td>
						<td>'.$order['created'].'</td>
					</tr>';
				}else if($exportType=="b"){
					if($this->kclass->DB->numRows()){
						$j = 1;
						while($item=$this->kclass->DB->fetchArray($items)){
							$price = $this->kclass->iif($item['price'],$item['price'],$item['price1']);
							$body .='<tr class="'.$this->kclass->rotateLine().' '.$alarm.' small">
										<td align="center">'.$i.'</td>
										<td>'.$order['orderno'].'</td>
										<td>'.$purchaseType.'</td>
										<td '.$order['abbr'].'</td>
										<td align="center">'.$order['created'].'</td>
										<td>'.$item['materialno'].'</td>
										<td>'.$item['title'].'</td>
										<td>'.$item['standard'].'</td>										
										<td>'.$item['quantity'].'</td>
										<td>'.$price.'</td>
										<td>'.$item['amount'].'</td>
										<td>'.date("Y-m-d",$item['dateline']).'</td>
									</tr>';	
							$j++;
						}
					}			
				}
				$i++;
			}
			$body.='</tbody></table>';
			$this->kclass->page['title'].='采购列表-导出EXCEL';
			header("Content-type:application/vnd.ms-excel");
			header("Content-Disposition:attachment;filename=".$title.".xls");
			echo $body;
		}
	
	}
	//物资列表
	function material(){
		$title = $this->kclass->input['title'];
		$condition = $this->kclass->input['condition'];
		$show = $this->kclass->input['show'];
		$orderby = $this->kclass->input['orderby'];
		$direction = $this->kclass->input['direction'];
		$layout = $this->kclass->input['layout'];
		if($orderby!='') $query['orderby'] = $orderby;
		if($direction!='') $query['direction'] = $direction;
		if($show!='') $query['show'] = $show;
		if($layout!='') $query['layout'] = $layout;
		$orderby=$this->kclass->orderby(array('module'=>'material','direction'=>'asc','orderby'=>'modified','default'=>'materialno','serial'=>
											array(
												array('title'=>'名称','field'=>'title','word'=>'name'),
													array('title'=>'编号','field'=>'materialno','word'=>'no'),
														array('title'=>'修改时间','field'=>'modified'),
															array('title'=>'建立时间','field'=>'created')
												),'appendUrl'=>$query
											)
										);
		if(is_array($query) AND count($query)>0)$queryPart='&'.http_build_query($query);
		if($perpage=='')$perpage=500;
		$pcount=$this->kclass->DB->queryFirst("SELECT COUNT(materialid) AS count FROM material WHERE killed=0");
		$list=$this->kclass->listMaterial(array(
											'perpage'=>$perpage,
											'find'=>$find,
											'condition'=>$condition,
											'orderby'=>$query['orderby'],
											'direction'=>$query['direction'],
											'show'=>$query['show'],
											'layout'=>$query['layout'])
										);
		$materialList.='<table class="hundred">
							<thead>
								<tr>
									<th width="35">ID</th>
									<th width="80">物资编号</th>
									<th width="250">名称</th>
									<th width="450">规格</th>
									<th width="80">单价</th>
									<th width="80">通用物料</th>
									<th width="80">总库存数</th>
									<th width="80">良品数</th>
									<th width="80">不良品数</th>
									<th width="80">最小值</th>
									<th width="80">来源</th>
									<th width="60">单位</th>
									<th width="80"></th>
								</tr>
							</thead>
							<tbody>';
		$materialList.=$list['body'];
		$this->kclass->page['title'].='物资列表-导出EXCEL';
		header("Content-type:application/vnd.ms-excel");
		header("Content-Disposition:attachment;filename=".$title.".xls");
		echo $materialList;		
	}

}

?>