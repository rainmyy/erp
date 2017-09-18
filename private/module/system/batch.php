<?php
	//
class batch{
	var $kclass;
	//
	function autorun(){
		$this->baseurl='<a href="/s.php?">首页</a>';
		switch($this->kclass->input['action']){
			case 'view':
				return $this->view();
				break;
			case 'add':
				return $this->add();
				break;
			case 'insert':
				return $this->insert();
				break;
			case 'addMaterial':
				return $this->addMaterial();
				break;
			case 'insertMaterial':
				return $this->insertMaterial();
				break;
			case 'addOutbound':
				return $this->addOutbound();
				break;
			case 'insertOutbound':
				return $this->insertOutbound();
				break;
			case 'update':
				return $this->update();
				break;
			case 'doupdate':
				return $this->doupdate();
				break;
			case 'addStock':
				return $this->addStock();
				break;
			case 'insertStock':
				return $this->insertStock();
				break;
			case 'updateStock':
				return $this->updateStock();
				break;
			case 'doupdateStock':
				return $this->doupdateStock();
				break;
			case 'remove':
				return $this->remove();
				break;
			case 'kill':
				return $this->kill();
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
	// main page,shows all the batch info list
	function mmlist(){
		$this->kclass->input['orderby']=$this->kclass->iif($this->kclass->input['orderby'],$this->kclass->input['orderby'],'batchid');
		$this->kclass->input['direction']=$this->kclass->iif($this->kclass->input['direction'],$this->kclass->input['direction'],'ASC');
		$this->kclass->input['condition']=$this->kclass->iif($this->kclass->input['condition'],$this->kclass->input['condition'],'1=1');
		$batchs=$this->kclass->DB->query("
			SELECT i.*,
				u.username AS creator,
				us.username AS modifier
			FROM `batch` AS i
			LEFT JOIN `user` AS u ON (u.userid=i.creator)
			LEFT JOIN `user` AS us ON (us.userid=i.modifier)
			WHERE i.killed=0 AND '".$this->kclass->input['condition']."'
			ORDER BY ".$this->kclass->input['orderby']." ".$this->kclass->input['direction']."
		");
		if($this->kclass->DB->numRows()){
			$body='<ul class="mmlist clear" id="mmlist">';
			$i=1;
			while($batch=$this->kclass->DB->fetchArray($batchs)){
				$batch['created']=date('Y-m-d',$batch['created']);
				$barcode=$this->kclass->DB->queryFirst("SELECT barcodeid,barcode FROM barcode WHERE batchid='".$batch['batchid']."'");
				$barcode['barcode']=$this->kclass->iif($barcode['barcode'],'<a href="/s.php?module=barcode&action=view&barcodeid='.$barcode['barcodeid'].'">'.$barcode['barcode'].'</a>','暂无');
				$body.='<li title="由'.$batch['creator'].'于 '.$batch['created'].' 建立"'.$this->kclass->iif($batch['modifier']!='', '，'.$batch['modifier'].' 改于 '.date('Y-m-d',$batch['modified']), '').'"'.$this->kclass->iif($i%4==0, ' class="end"', '').'>
					<div class="mmlistt"><span class="right normal"><a onclick="return confirm("你确定要删除这个批次 '.$batch['batchno'].' 吗？");" href="/s.php?module=batch&action=remove&batchid='.$batch['batchid'].'">删</a> <a href="/s.php?module=batch&action=update&batchid='.$batch['batchid'].'">改</a></span> <a href="/s.php?module=batch&action=view&batchid='.$batch['batchid'].'">'.$batch['batchno'].'</a> <br> <span class="normal">'.$barcode['barcode'].'</span></div>
					<div class=mmlistb>
					<div title="'.$batch['materialno'].$batch['materialTitle'].$batch['standard'].'"><span class="small gray">相关物资：'.$batch['materialno'].' <a href="/s.php?module=material&action=view&materialid='.$batch['materialid'].'">'.$batch['materialTitle'].' '.$batch['standard'].'</a></span></div>
					<div title="'.$batch['recipient'].'"><span class="small gray">签收人：'.$batch['recipient'].'</span></div>
					<div><span class="small gray">数量：'.$batch['quantity'].'　良品数：'.$batch['qualified'].'　不良品数：'.$batch['disqualified'].'</span></div>
					<div class="small clear"><span class=right title="由 '.$batch['creator'].' 建于 '.$batch['created'].'">由 '.$batch['creator'].'  建于 '.$batch['created'].'</span></div></div></li>';
				$i++;
			}
			$body.='</ul>';
		}
		$this->kclass->page['title'].='批次列表';
		$this->kclass->page['onload'].='dc.listhover();';
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - 批次列表','right' => '<span class="small">显示方式：</span><a href="/s.php?module=batch">默认列表</a>　<a href="/s.php?module=batch&action=list&show=all">所有<span class="small gray">(包括删除)</span></a>　|　<a href="/s.php?module=batch&action=add">新建</a>　|　<a href="/s.php?module=batch&action=import">批量导入</a>　','body'=>$body));
	}
	//
	function view(){
		$batch=$this->kclass->DB->queryFirst("
			SELECT b.*,
				un.title AS unit,
				o.porderid,o.orderno,
				s.supplierno,s.title AS supplier,
				u.username AS creator,
				us.username AS modifier
			FROM batch AS b
			LEFT JOIN `porder` AS o ON (o.porderid=b.porderid)
			LEFT JOIN `unit` AS un ON (un.unitid=b.unitid)
			LEFT JOIN `supplier` AS s ON (s.supplierid=b.supplierid)
			LEFT JOIN `user` AS u ON (u.userid=b.creator)
			LEFT JOIN `user` AS us ON (us.userid=b.modifier)
			WHERE b.killed=0 AND b.batchid='".$this->kclass->input['batchid']."'
			LIMIT 0,1
		");
		if($batch){
			$batch['created']=date('Y-m-d H:i',$batch['created']);
			if($batch['modified']!=0)$modify='，由'.$batch['modifier'].'于'.date('Y-m-d H:i',$batch['modified']).'修改';
			$batch['arrivalTime']=date('Y-n-d',$batch['arrivalTime']);
			$barcode=$this->kclass->DB->queryFirst("SELECT barcodeid,barcode FROM barcode WHERE batchid='".$this->kclass->input['batchid']."'");
			// this batch item of stock
			$items=$this->kclass->DB->query("
				SELECT `stockitem`.quantity,`stockitem`.created,
					`stock`.stockid,`stock`.stockno
				FROM `stockitem`
				LEFT JOIN `stock` ON (`stock`.stockid=`stockitem`.stockid)
				WHERE `stockitem`.killed=0 AND `stockitem`.batchid='".$this->kclass->input['batchid']."'
				ORDER BY `stockitem`.created ASC
			");
			$stock['num']=$this->kclass->DB->numRows();
			if($stock['num']){
				$i=1;
				$stock['panel']='<table class="center" style="width:450px;"><thead><tr><th colspan="4">所在库位及数量</th></tr></thead><tbody><tr class="even"><td wdith="30">序号</td><td>库位</td><td>数量</td><td>开始存放时间</td></tr>';
				while($item=$this->kclass->DB->fetchArray($items)){
					$created=date('Y-m-d H:j:s',$item['created']);
					$stock['panel'].='<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').'">
						<td>'.$i.'</td>
						<td><a href="/s.php?module=stock&action=view&stockid='.$item['stockid'].'">'.$item['stockno'].'</a></td>
						<td>'.$item['quantity'].'</td>
						<td>'.$created.'</td></tr>';
					$i++;
				}
				$stock['panel'].='</tbody></table>';
				$stock['count']='('.$stock['num'].')';
			}else{
				$stock['off']=' disabled';
			}
			// this batch history stock
			$historyStocks=$this->kclass->DB->query("
				SELECT `stockitem`.quantity,`stockitem`.created,`stockitem`.killed,
					`stock`.stockid,`stock`.stockno
				FROM `stockitem`
				LEFT JOIN `stock` ON (`stock`.stockid=`stockitem`.stockid)
				WHERE `stockitem`.killed<>0 AND `stockitem`.batchid='".$this->kclass->input['batchid']."'
				ORDER BY `stockitem`.killed DESC
			");
			if($this->kclass->DB->numRows()){
				$stock['panel'].='<table class="center" style="width:450px;"><thead><tr><th colspan=4>历史库位</th></tr></thead><tbody><tr class="even"><td wdith="30">序号</td><td>库位</td><td>开始时间</td><td>结束时间</td></tr>';
				while($historyStock=$this->kclass->DB->fetchArray($historyStocks)){
					$created=date('Y-m-d H:j:s',$historyStock['created']);
					$killed=date('Y-m-d H:j:s',$historyStock['killed']);
					$stock['panel'].='<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').'">
						<td>'.$i.'</td>
						<td><a href="/s.php?module=stock&action=view&stockid='.$historyStock['stockid'].'">'.$historyStock['stockno'].'</a></td>
						<td>'.$created.'</td>
						<td>'.$killed.'</td></tr>';
					$i++;
				}
				$stock['panel'].='</tbody></table>';
			}

$body=<<<EOF
<div class="title">{$batch['batchno']}<span class="right small gray">由{$batch['creator']}于{$batch['created']}建立{$modify}。</span></div>
<dl id="batch" class="tabs">
<dt tabid="0">基本信息</dt>
<dt title="与此物资相关的属性"{$stock['off']}>库位信息{$stock['count']}</dt>
<dd>
<table class="hundred">
<thead><tr><th colspan="4">基本信息</th></tr></thead>
<tbody>
<tr class="odd">
<td width="100">批次编号：</td><td>{$batch['batchno']}</td>
<td width="100">入库编号：</td><td><a href="/s.php?module=inbound&action=view&inboundid={$batch['inboundid']}">{$batch['inboundno']}</a></td>
</tr>
<tr class="even">
<td>签 收 人：</td><td>{$batch['recipient']}</td>
<td>单　　位：</td><td>{$batch['unit']}</td>
</tr>
<tr class="odd">
<td>物资编号：</td><td><a href="/s.php?module=material&action=view&materialid={$batch['materialid']}">{$batch['materialno']}</a></td>
<td>物资名称：</td><td>{$batch['materialTitle']}</td>
</tr>
<tr class="even">
<td>物资规格：</td><td>{$batch['standard']}</td>
<td>版 本 号：</td><td>{$batch['version']}</td>
</tr>
<tr class="odd">
<td>供 应 商：</td><td>{$batch['supplier']}</td>
<td>订 单 号：</td><td><a href="/s.php?module=porder&action=view&porderid={$batch['porderid']}">{$batch['orderno']}</a></td>
</tr>
<tr class="even">
<td>到货时间：</td><td>{$batch['arrivalTime']}</td>
<td>计划数量：</td><td>{$batch['planQuantity']}</td>
</tr>
<tr class="odd">
<td>入库数量：</td><td>{$batch['arrivalQuantity']}</td>
<td>实际数量：</td><td>{$batch['actualQuantity']}</td>
</tr>
<tr class="even">
<td>库存数量：</td><td>{$batch['quantity']}</td>
<td>良　　品：</td><td>{$batch['qualified']}</td>
</tr>
<tr class="odd">
<td>不 良 品：</td><td>{$batch['disqualified']}</td>
<td></td><td></td>
</tr>
<tr class="even">
<td>条　　码：</td><td><a href="/s.php?module=barcode&action=view&barcodeid={$barcode['barcodeid']}">{$barcode['barcode']}</a></td>
<td>备　　注：</td><td width="40%">{$batch['remark']}</td>
</tr>
</tbody>
</table>
</dd>
<dd>{$stock['panel']}</dd>
EOF;

		}else{
				$body = '系统数据出错，很抱歉！';
		}

		$this->kclass->page['title'].='查看批次';
		$this->kclass->page['onload'].='dc.tabhover();dc.tabs({\'id\':\'batch\'})';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - <a href="/s.php?module=batch&action=list">批次列表</a> - 查看批次详情', 'right' => '<span class="small gray">新建：</span><a href="/s.php?module=batch&action=addStock&batchid='.$batch['batchid'].'">库位</a>　|　<span class="small gray">修改：</span><a href="/s.php?module=batch&action=update&batchid='.$batch['batchid'].'">批次</a>　<a href="/s.php?module=batch&action=updateStock&batchid='.$batch['batchid'].'">库位</a>　|　<a href="/s.php?module=batch&action=remove&batchid='.$batch['batchid'].'&rt=view">删除</a>','body' => $body));
	}
	// add batch
	function add(){
		$inbound=$this->kclass->DB->queryFirst("
			SELECT i.inboundid,i.inboundno,i.porderid,i.supplierid,i.arrivalTime, 
				s.title AS supplier,s.supplierno
			FROM inbound AS i
			LEFT JOIN supplier AS s ON (s.supplierid=i.supplierid)
			WHERE i.killed=0 AND i.inboundid='".$this->kclass->input['inboundid']."' 
			LIMIT 0,1
		");
		if(!$inbound){
			$e='<li>数据错误，很抱歉~</li>';
		}else{
			$item=$this->kclass->DB->queryFirst("
				SELECT pi.itemid,pi.materialid,pi.version,pi.quantity,pi.qualified,pi.disqualified,pi.unitid,pi.dateline,pi.remark,
					m.materialno,m.title,m.standard
				FROM preinbounditem AS pi
				LEFT JOIN material AS m ON (m.materialid=pi.materialid)
				WHERE pi.killed=0 AND pi.module='inbound' AND pi.mid='".$inbound['inboundid']."' AND pi.itemid='".$this->kclass->input['itemid']."'
				LIMIT 0,1
			");
			$defaultSupplier=$this->kclass->DB->queryFirst("SELECT title,supplierno FROM supplier WHERE supplierid=1");
			if(!$item){
				$e='<li>数据错误，很抱歉~</li>';
			}
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '新建入库记录的批次编号',
				'text' => '新建入库记录的批次编号<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		if($inbound['porderid']>0){
			$order=$this->kclass->chooserOrder(array('name'=>'porderid','module'=>'porder','hasBlank'=>1,'width'=>200,'topname'=>'','selectedid'=>$inbound['porderid']));
			$orderItem=$this->kclass->DB->queryFirst("SELECT quantity FROM item WHERE module='porder' AND mid='".$inbound['porderid']."' AND materialid='".$item['materialid']."' LIMIT 0,1");
			$r='<tr class="even"><td>选择订单：<span class="red bold">*</span></td><td>'.$order.'</td><td>计划数量：<span class="red bold">*</span></td><td><input type="text" name="planQuantity" value="'.$orderItem['quantity'].'" /></td>/tr>';
		}
		$supplier=$this->kclass->chooserSupplier(array('hName'=>'supplierid','name'=>'supplier',''=>'','width'=>400,'selectedid'=>$this->kclass->iif($item['supplierid'],$inbound['supplierid'],1),'value'=>$this->kclass->iif($inbound['supplierid'],$inbound['supplier'].' ('.$inbound['supplierno'].')',$defaultSupplier['title'].' ('.$defaultSupplier['supplierno'].')')));
		$unit=$this->kclass->chooserUnit(array('name'=>'unitid','hasBlank'=>1,'width'=>200,'topname'=>'','selectedid'=>$item['unitid']));
		$barcode=$this->kclass->chooserBarcode(array('name'=>'barcodeid','condition'=>'materialid='.$item['materialid'].' AND arrivalTime='.$item['dateline']));
		$batchno=$this->kclass->id(array('batch'=>$item['dateline'],'materialno'=>$item['materialno']));
		$version=$this->kclass->iif($item['version'],$item['version'],'A01');
		$item['dateline']=date('Y-m-d',$item['dateline']);
		$quantity=$this->kclass->iif($item['qualified']==0,$item['disqualified'],$item['qualified']);
$body=<<<EOF
<form action="/s.php?module=batch&action=insert" name="batch" method="post">
<input type="hidden" name="module" value="batch" />
<input type="hidden" name="action" value="insert" />
<input type="hidden" name="inboundid" value="{$this->kclass->input['inboundid']}" />
<input type="hidden" name="itemid" value="{$this->kclass->input['itemid']}" />
<input type="hidden" name="materialid" value="{$item['materialid']}" />
<table class="hundred">
<thead></tr><th colspan="4">添加批次信息</th></tr></thead>
<tbody>
<tr class="odd">
<td width="100">批次编号：<span class="red bold">*</span></td><td><input type="text" name="batchno" value="{$batchno}" readonly /></td>
<td width="100">选择条码：<span class="red bold">*</span></td><td>{$barcode}</td>
</tr>
<tr class="even">
<td>签 收 人：<span class="red bold">*</span></td><td><input type="text" name="recipient" value="" /></td>
<td>派工单号：</td><td><input type="text" name="dispatchno" value="" /></td>
</tr>
<tr class="odd">
<td>物资编号：<span class="red bold">*</span></td><td><input type="text" name="materialno" value="{$item['materialno']}" readonly /></td>
<td>物资名称：</td><td><input type="text" name="materialTitle" value="{$item['title']}" style="width:400px;" readonly /></td>
</tr>
<tr class="even">
<td>物资规格：</td><td><input type="text" name="standard" value="{$item['standard']}" style="width:400px;" readonly /></td>
<td>物资版本号：</td><td><input type="text" name="version" value="{$version}" readonly /><span class="small gray">（没有版本则默认为A01）</span></td>
</tr>
<tr class="odd">
<td>选择供应商：<span class="red bold">*</span></td><td>{$supplier}</td>
<td>批次时间：<span class="red bold">*</span></td><td><input type="text" name="arrivalTime" value="{$item['dateline']}" id="date" /> <span class="small gray">时间按2012-01-01格式输入</span></td>
</tr>
{$r}
<tr class="odd">
<td>入库数量：<span class="red bold">*</span></td><td><input type="text" name="arrivalQuantity" value="{$quantity}" /></td>
<td>实际数量：<span class="red bold">*</span></td><td><input type="text" name="actualQuantity" value="" /></td>
</tr>
<tr class="even">
<td>良品数量：<span class="red bold">*</span></td><td><input type="text" name="qualified" value="{$item['qualified']}" /></td>
<td>不良数量：<span class="red bold">*</span></td><td><input type="text" name="disqualified" value="{$item['disqualified']}" /></td>
</tr>
<tr class="even">
<td>备　　注：</td><td><input type="text" name="remark" style="width:450px;height:60px;" /></td>
<td></td><td></td>
</tr>
<tr class="odd">
<td class="small gray" colspan="4">注意事项：确认信息正确无误</td>
</tr>
</tbody>
</table>
<table class="tlist tsuite" align="center" width="100%">
<tbody>
	<tr class="even center" colspan=4 nohover><td><input type="submit" id="submitButton" value="  提交  " accesskey="s"><input type="reset" value="  复位  "/></td></td></tr>
</tbody>
</table>
</form>
EOF;

		$this->kclass->tbline+=4;
		$this->kclass->page['title'].='添加入库的批次';
		$this->kclass->page['onload'].='var dates=$(\'#date\').datepicker({onSelect:function(selectedDate){instance=$(this).data(\'datepicker\'),date=$.datepicker.parseDate(instance.settings.dateFormat || $.datepicker._defaults.dateFormat,selectedDate,instance.settings);}});dc.tabhover()';
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - <a href="/s.php?module=inbound&action=view&inboundid='.$this->kclass->input['inboundid'].'">查看入库单</a> - 添加入库物资', 'right'=>'<a href="/s.php?module=inbound&action=view&inboundid='.$this->kclass->input['inboundid'].'">返回查看入库单</a>','body'=>$body));
	}

	//
	function insert(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			if($this->kclass->input['batchno']==''){
				$e.='<li>请填写批次信息的  批次编号 </li>';
			}
			if($this->kclass->input['barcodeid']==0){
				$e.='<li>请填写批次信息的  条码 </li>';
			}
			if($this->kclass->input['recipient']==''){
				$e.='<li>请填写批次信息的  签收人 </li>';
			}
			if($this->kclass->input['materialid']==0){
				$e.='<li>请选择批次信息的  物资 </li>';
			}
			if($this->kclass->input['actualQuantity']==''){
				$e.='<li>请填写批次信息的  实际数量 </li>';
			}
		}
		if($e){
			$this->kclass->messager(array(
				'title' => '新建批次信息',
				'text' => '您在新建批次信息的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		if($this->kclass->input['arrivalTime']!=''){
			$sd=explode('-',$this->kclass->input['arrivalTime']);
			$arrivalTime=mktime(0,0,0,$sd[1],$sd[2],$sd[0]);
		}else{
			$arrivalTime=0;
		}
		$material=$this->kclass->DB->queryFirst("SELECT materialno,title,standard,unitid FROM material WHERE materialid='".$this->kclass->input['materialid']."' LIMIT 0,1");
		$batchno=$this->kclass->id(array('batch'=>$arrivalTime,'materialno'=>$material['materialno']));
		$version=$this->kclass->DB->queryFirst("SELECT versionid,title FROM materialversion WHERE killed=0 AND materialid='".$this->kclass->input['materialid']."' AND title='".$this->kclass->input['version']."' LIMIT 0,1");
		if($version){
			$this->kclass->DB->query("
				UPDATE materialversion SET
					quantity=quantity+'".$this->kclass->input['actualQuantity']."',
					qualified='".$this->kclass->input['qualified']."',
					disqualified='".$this->kclass->input['disqualified']."'
				WHERE versionid='".$version['versionid']."'
			");
			$versionid=$version['versionid'];
		}else{
			$this->kclass->DB->query("
				INSERT INTO materialversion (`materialid`,`title`,`quantity`,`qualified`,`disqualified`,`change`,`created`,`creator`)
				VALUES ('".$this->kclass->input['materialid']."','".$this->kclass->input['version']."','".$this->kclass->input['actualQuantity']."','".$this->kclass->input['qualified']."','".$this->kclass->input['disqualified']."','".$this->kclass->input['change']."','".TIMENOW."','".$this->kclass->user['userid']."')
			");
			$versionid=$this->kclass->DB->insertID();
		}
		$porderid=$this->kclass->iif($this->kclass->input['porderid'],$this->kclass->input['porderid'],0);
		$planQuantity=$this->kclass->iif($this->kclass->input['planQuantity'],$this->kclass->input['planQuantity'],0);
		$this->kclass->DB->query("
			INSERT INTO batch
				(batchno,inboundid,supplierid,materialid,porderid,versionid,unitid,dispatchno,arrivalTime,arrivalQuantity,planQuantity,actualQuantity,quantity,qualified,disqualified,recipient,remark,created,creator)
			VALUES ('".$batchno."','".$this->kclass->input['inboundid']."','".$this->kclass->input['supplierid']."','".$this->kclass->input['materialid']."','".$porderid."','".$versionid."','".$material['unitid']."','".$this->kclass->input['dispatchno']."','".$arrivalTime."','".$this->kclass->input['arrivalQuantity']."','".$planQuantity."','".$this->kclass->input['actualQuantity']."','".$this->kclass->input['actualQuantity']."','".$this->kclass->input['qualified']."','".$this->kclass->input['disqualified']."','".$this->kclass->input['recipient']."','".$this->kclass->input['remark']."','".TIMENOW."','".$this->kclass->user['userid']."')
		");
		$batchid=$this->kclass->DB->insertID();
		//$this->kclass->DB->query("UPDATE preinbounditem SET batchid='".$batchid."' WHERE itemid='".$this->kclass->input['itemid']."'");
		$this->kclass->DB->query("UPDATE barcode SET batchid='".$batchid."',modifier='".$this->kclass->user['userid']."',modified='".TIMENOW."' WHERE barcodeid='".$this->kclass->input['barcodeid']."'");
		$this->kclass->messager(array(
			'title' => '新建入库单的批次信息',
			'text' => '批次 <b>'.$batchno.'</b> 已新建成功!返回入库单页面',
			'url' => '/s.php?module=inbound&action=view&inboundid='.$this->kclass->input['inboundid'],
			'sec' => 2
		));
	}
	//
	function addMaterial(){
		$material=$this->kclass->DB->queryFirst("SELECT materialno,title,standard,unitid FROM material WHERE materialid='".$this->kclass->input['materialid']."' LIMIT 0,1");
		if($material){
			$unit=$this->kclass->chooserUnit(array('name'=>'unitid','hasBlank'=>1,'width'=>200,'topname'=>'','selectedid'=>$material['unitid']));
			$barcode=$this->kclass->chooserBarcode(array('name'=>'barcodeid','hasBlank'=>1,'topname'=>'请选择'));

$body=<<<EOF
<form action="/s.php?module=batch&action=insertMaterial" name="batch" method="post">
<input type="hidden" name="module" value="batch" />
<input type="hidden" name="action" value="insertMaterial" />
<input type="hidden" name="materialid" value="{$this->kclass->input['materialid']}" />
<table class="hundred">
<thead></tr><th colspan="4">添加批次信息</th></tr></thead>
<tbody>
<tr class="odd">
<td width="100">批次编号：<span class="red bold">*</span></td><td><input type="text" name="batchno" value="" /> <span class="small gray">编号规则：物资编号+6位日期（年月日各两位）</span></td>
<td>物资编号：<span class="red bold">*</span></td><td><input type="text" name="materialno" value="{$material['materialno']}" /></td>
</tr>
<tr class="even">
<td>物资名称：</td><td><input type="text" name="materialTitle" value="{$material['title']}" style="width:250px;" /></td>
<td>物资规格：</td><td><input type="text" name="standard" value="{$material['standard']}" style="width:250px;"  /></td>
</tr>
<tr class="odd">
<td>版 本 号：</td><td><input type="text" name="version" value="A01" /><span class="small gray">（没有版本则默认为A01）</span></td>
<td>批次时间：<span class="red bold">*</span></td><td><input type="text" name="dateline" value="" id="date" /></td>
</tr>
<tr class="even">
<td>该批总数：<span class="red bold">*</span></td><td><input type="text" name="quantity" value="" /></td>
<td>良品数量：<span class="red bold">*</span></td><td><input type="text" name="qualified" value="" /></td>
</tr>
<tr class="odd">
<td>不良品数：<span class="red bold">*</span></td><td><input type="text" name="disqualified" value="0" /></td>
<td>单　　位：</td><td>{$unit}</td>
</tr>
<tr class="even">
<td>备　　注：</td><td width="40%"><textarea type="text" name="remark" value="" style="width:400px;" ></textarea></td>
<td></td><td></td>
</tr>
<tr class="odd">
<td class="small gray" colspan="4">注意事项：该添加批次操作仅限于对于以前未加入系统的物资进行添加</td>
</tr>
</tbody>
</table>
<table class="tlist tsuite" align="center" width="100%">
<tbody>
	<tr class="even center" colspan=4 nohover><td><input type="submit" id="submitButton" value="  提交  " accesskey="s"><input type="reset" value="  复位  "/></td></td></tr>
</tbody>
</table>
</form>
EOF;
		}else{
			$body='数据错误，很抱歉！';
		}
		$this->kclass->page['title'].='添加入库的批次';
		$this->kclass->page['onload'].='var dates=$(\'#date\').datepicker({onSelect:function(selectedDate){instance=$(this).data(\'datepicker\'),date=$.datepicker.parseDate(instance.settings.dateFormat || $.datepicker._defaults.dateFormat,selectedDate,instance.settings);}});dc.tabhover()';
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - <a href="/s.php?module=inbound&action=view&materialid='.$this->kclass->input['materialid'].'">查看物资</a> - 添加物资批号', 'right'=>'<a href="/s.php?module=material&action=view&materialid='.$this->kclass->input['materialid'].'">返回查看物资</a>','body'=>$body));
	}
	//
	function insertMaterial(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e='<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			if($this->kclass->input['batchno']==''){
				$e.='请填写订单的 批次编号。';
			}
			if($this->kclass->input['quantity']==''){
				$e.='请填写订单的 批次时间。';
			}
			if($this->kclass->input['qualified']==''){
				$e.='请填写订单的 批次时间。';
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
		if($this->kclass->input['dateline']!=''){
			$sd=explode('-',$this->kclass->input['dateline']);
			$dateline=mktime(0,0,0,$sd[1],$sd[2],$sd[0]);
		}else{
			$dateline='';
		}
		$this->kclass->DB->query("
			INSERT batch (batchno,materialid,materialno,materialTitle,standard,version,unitid,arrivalTime,quantity,qualified,disqualified,remark,created,creator)
			VALUES ('".$this->kclass->input['batchno']."','".$this->kclass->input['materialid']."','".$this->kclass->input['materialno']."','".$this->kclass->input['materialTitle']."','".$this->kclass->input['standard']."','".$this->kclass->input['version']."','".$this->kclass->input['unitid']."','".$dateline."','".$this->kclass->input['quantity']."','".$this->kclass->input['qualified']."','".$this->kclass->input['disqualified']."','".$this->kclass->input['remark']."','".TIMENOW."','".$this->kclass->user['userid']."')
		");

		$this->kclass->messager(array(
			'title' => '新建该物资的批次',
			'text' => '该物资的批次信息 '.$this->kclass->input['batchno'].' 已新建成功!返回物资详情页',
			'url' => '/s.php?module=material&action=view&materialid='.$this->kclass->input['materialid'],
			'sec' => 2
		));
	}
	//
	function update(){
		$batch=$this->kclass->DB->queryFirst("
			SELECT `batch`.*,
				`barcode`.barcodeid, 
				`material`.materialno,`material`.title AS materialTitle,`material`.standard AS materialStandard,
				`supplier`.title AS supplier,`supplier`.supplierno
			FROM `batch` 
			LEFT JOIN `barcode` ON (`barcode`.batchid=`batch`.batchid) 
			LEFT JOIN `material` ON (`material`.materialid=`batch`.materialid)
			LEFT JOIN `supplier` ON (`supplier`.supplierid=`batch`.supplierid)
			WHERE `batch`.batchid='".$this->kclass->input['batchid']."' 
			LIMIT 0,1
		");
		if(!$batch){
			$e='<li>数据错误，很抱歉！</li>';
		}
		if($e){
			$this->kclass->messager(array(
				'title' => '修改批次信息',
				'text' => '您在修改批次信息的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		
		$material=$this->kclass->chooserMaterial(array('name'=>'materialid','hasBlank'=>1,'width'=>200,'topname'=>'新物品','selectedid'=>$batch['materialid']));
		if($inbound['porderid']>0){
			$order=$this->kclass->chooserOrder(array('name'=>'porderid','module'=>'porder','hasBlank'=>1,'width'=>200,'topname'=>'','selectedid'=>$inbound['porderid']));
			$orderItem=$this->kclass->DB->queryFirst("SELECT quantity FROM item WHERE module='porder' AND mid='".$inbound['porderid']."' AND materialid='".$item['materialid']."' LIMIT 0,1");
			$r='<tr class="even"><td>选择订单：<span class="red bold">*</span></td><td>'.$order.'</td><td>计划数量：<span class="red bold">*</span></td><td><input type="text" name="planQuantity" value="'.$orderItem['quantity'].'" /></td>/tr>';
		}
		$order=$this->kclass->chooserOrder(array('name'=>'porderid','module'=>'porder','hasBlank'=>1,'width'=>200,'topname'=>'','selectedid'=>$batch['porderid']));
		$supplier=$this->kclass->chooserSupplier(array('hName'=>'supplierid','name'=>'supplier',''=>'','width'=>400,'selectedid'=>$batch['supplierid'],'value'=>$batch['supplier'].' ('.$batch['supplierno'].')'));
		$unit=$this->kclass->chooserUnit(array('name'=>'unitid','hasBlank'=>1,'width'=>200,'topname'=>'','selectedid'=>$batch['unitid']));
		$barcode=$this->kclass->chooserBarcode(array('name'=>'barcodeid','hasBlank'=>1,'topname'=>'请选择','selectedid'=>$batch['barcodeid'],'condition'=>'materialid='.$batch['materialid']));
		$batch['arrivalTime']=date('Y-m-d',$batch['arrivalTime']);
		$version=$this->kclass->chooserMaterialVersion(array('name'=>'versionid','selectedid'=>$batch['versionid'],'width' =>150,'materialid'=>$batch['materialid']));
$body=<<<EOF
<form action="/s.php?module=batch&action=doupdate" name="batch" method="post">
<input type="hidden" name="module" value="batch" />
<input type="hidden" name="action" value="doupdate" />
<input type="hidden" name="inboundid" value="{$batch['inboundid']}" />
<input type="hidden" name="batchid" value="{$batch['batchid']}" />
<input type="hidden" name="materialid" value="{$batch['materialid']}" />
<table class="hundred">
<thead></tr><th colspan="4">添加批次信息</th></tr></thead>
<tbody>
<tr class="odd">
<td width="100">批次编号：<span class="red bold">*</span></td><td><span class="red bold middle">{$batch['batchno']}</span></td>
<td width="100">选择条码：<span class="red bold">*</span></td><td>{$barcode}</td>
</tr>
<tr class="even">
<td>签收人：<span class="red bold">*</span></td><td><input type="text" name="recipient" value="{$batch['recipient']}" /></td>
<td>派工单号：</td><td><input type="text" name="dispatchno" value="{$batch['dispatchno']}" /></td>
</tr>
<tr class="odd">
<td>物资编号：</td><td><input type="text" name="materialno" value="{$batch['materialno']}" readonly /></td>
<td>物资名称：</td><td><input type="text" name="materialTitle" value="{$batch['materialTitle']}" style="width:250px;" readonly /></td>
</tr>
<tr class="even">
<td>物资规格：</td><td><input type="text" name="standard" value="{$batch['materialStandard']}" style="width:250px;" readonly /></td>
<td>物资版本：<span class="red bold">*</span></td><td>{$version}</td>
</tr>
<tr class="odd">
<td>选择供应商：<span class="red bold">*</span></td><td>{$supplier}</td>
<td>批次时间：<span class="red bold">*</span></td><td><input type="text" name="arrivalTime" value="{$batch['arrivalTime']}" id="date" /> <span class="small gray">时间按2012-01-01格式输入</span></td>
</tr>
{$r}
<tr class="odd">
<td>入库数量：<span class="red bold">*</span></td><td><input type="text" name="arrivalQuantity" value="{$batch['arrivalQuantity']}" /></td>
<td>实际数量：<span class="red bold">*</span></td><td><input type="text" name="actualQuantity" value="{$batch['quantity']}" /></td>
</tr>
<tr class="odd">
<td>良品数量：<span class="red bold">*</span></td><td><input type="text" name="qualified" value="{$batch['qualified']}" /></td>
<td>不良数量：<span class="red bold">*</span></td><td><input type="text" name="disqualified" value="{$batch['disqualified']}" /></td>
</tr>
<tr class="even">
<td>备注：</td><td width="40%"><textarea type="text" name="remark" value="{$batch['remark']}" style="width:450px;height:60px;" ></textarea></td>
<td></td><td></td>
</tr>
<tr class="odd">
<td class="small gray" colspan="4">注意事项：确认信息正确无误</td>
</tr>
</tbody>
</table>
<table class="tlist tsuite" align="center" width="100%">
<tbody>
	<tr class="even center" colspan=4 nohover><td><input type="submit" id="submitButton" value="  提交  " accesskey="s"><input type="reset" value="  复位  "/></td></td></tr>
</tbody>
</table>
</form>
EOF;

		$this->kclass->tbline+=4;
		$this->kclass->page['title'].='修改批次信息';
		$this->kclass->page['onload'].='var dates=$(\'#date\').datepicker({onSelect:function(selectedDate){instance=$(this).data(\'datepicker\'),date=$.datepicker.parseDate(instance.settings.dateFormat || $.datepicker._defaults.dateFormat,selectedDate,instance.settings);}});dc.tabhover()';
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - <a href="/s.php?module=batch&action=view&batchid='.$this->kclass->input['batchid'].'">查看批次信息</a> - 修改批次信息', 'right'=>'<a href="/s.php?module=batch&action=view&batchid='.$this->kclass->input['batchid'].'">返回查看批次信息</a>','body'=>$body));
	}
	//
	function doupdate(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			if($this->kclass->input['recipient']==''){
				$e.='<li>请填写批次信息的  签收人 </li>';
			}
			if($this->kclass->input['materialid']==0){
				$e.='<li>请选择批次信息的  物资 </li>';
			}
			if($this->kclass->input['actualQuantity']==''){
				$e.='<li>请填写批次信息的  实际数量 </li>';
			}
		}
		if($e){
			$this->kclass->messager(array(
				'title' => '修改批次信息',
				'text' => '您在修改批次信息的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}

		if($this->kclass->input['arrivalTime']!=''){
			$sd=explode('-',$this->kclass->input['arrivalTime']);
			$arrivalTime=mktime(0,0,0,$sd[1],$sd[2],$sd[0]);
		}else{
			$arrivalTime=0;
		}
		$material=$this->kclass->DB->queryFirst("SELECT materialno,title,standard,unitid FROM material WHERE materialid='".$this->kclass->input['materialid']."' LIMIT 0,1");
		$batchno=$this->kclass->id(array('batch'=>$arrivalTime,'materialno'=>$material['materialno']));
		$porderid=$this->kclass->iif($this->kclass->input['porderid'],$this->kclass->input['porderid'],0);
		$planQuantity=$this->kclass->iif($this->kclass->input['planQuantity'],$this->kclass->input['planQuantity'],0);
		$this->kclass->DB->query("
			UPDATE batch SET
				batchno='".$batchno."',
				supplierid='".$this->kclass->input['supplierid']."',
				materialid='".$this->kclass->input['materialid']."',
				porderid='".$porderid."',
				versionid='".$this->kclass->input['versionid']."',
				unitid='".$material['unitid']."',
				dispatchno='".$this->kclass->input['dispatchno']."',
				arrivalTime='".$arrivalTime."',
				arrivalQuantity='".$this->kclass->input['arrivalQuantity']."',
				planQuantity='".$planQuantity."',
				actualQuantity='".$this->kclass->input['actualQuantity']."',
				quantity='".$this->kclass->input['actualQuantity']."',
				qualified='".$this->kclass->input['qualified']."',
				disqualified='".$this->kclass->input['disqualified']."',
				recipient='".$this->kclass->input['recipient']."',
				remark='".$this->kclass->input['remark']."',
				modifier='".$this->kclass->user['userid']."',
				modified='".TIMENOW."'
			WHERE batchid='".$this->kclass->input['batchid']."'
		");

		$this->kclass->messager(array(
			'title' => '修改入库单的批次信息',
			'text' => '批次 <b>'.$batchno.'</b> 已修改成功!返回入库单页面',
			'url' => '/s.php?module=batch&action=view&batchid='.$this->kclass->input['batchid'],
			'sec' => 2
		));
	}
	//
	function addStock(){
		$batch=$this->kclass->DB->queryFirst("
			SELECT `batch`.batchno,`batch`.materialid,`batch`.quantity,`batch`.qualified,`batch`.disqualified,
				`barcode`.barcodeid
			FROM `batch`
			LEFT JOIN `barcode` ON (`barcode`.batchid=`batch`.batchid)
			WHERE `batch`.batchid='".$this->kclass->input['batchid']."'
		");
		if(!$batch){
			$e='<li>数据错误，很抱歉！</li>';
		}
		if($e){
			$this->kclass->messager(array(
				'title' => '新增批次所在库位',
				'text' => '您在新增批次所在库位的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		for($i=1;$i<6;$i++){
			$stocktr.='<tr class="'.$this->kclass->iif($this->kclass->rotate,'odd','even').'">
				<td>'.$this->kclass->chooserStock(array('name'=>'itemStockid['.$i.']','width'=>'250')).'</td>
				<td><input type="text" name="itemQuantity['.$i.']" /></td>
				<td><input type="radio" name="itemEmpty['.$i.']" value="0" checked>是 <input type="radio" name="itemEmpty['.$i.']" value=1 >否</td></tr>';
		}
$body=<<<EOF
<div class="title">{$batch['batchno']}</div>
<table class="hundred">
<thead><tr><th colspan="4">基本信息</th></tr></thead>
<tbody>
<tr class="odd">
<td width="100">批次编号：</td><td width="475">{$batch['batchno']}</td>
<td width="100">总　　数：</td><td>{$batch['quantity']}</td>
</tr>
<tr class="even">
<td>良品数量：</td><td>{$batch['qualified']}</td>
<td>不良品数：</td><td>{$batch['disqualified']}</td>
</tr>
</tbody>
</table>
<form method="post" action="/s.php?module=batch&action=insertStock">
<input type="hidden" name="module" value="batch" />
<input type="hidden" name="action" value="insertStock" />
<input type="hidden" name="batchid" value="{$this->kclass->input['batchid']}" />
<input type="hidden" name="materialid" value="{$batch['materialid']}" />
<input type="hidden" name="barcodeid" value="{$batch['barcodeid']}" />
<table>
<thead><tr><th colspan="4">库位明细</th></tr></thead>
<tr class="center"><td>库位 <span class="red bold">*</span></td><td>数量 <span class="red bold">*</span></td><td>是否空闲 <span class="red bold">*</span></td></tr>
{$stocktr}
<tr class="odd center" nohover>
<td colspan=4><input type="submit" value="  提交  " accesskey="s" onclick="dc.supplier.check(document.supplier)"><input type="reset" value="  复位  "/></td>
</tr>
</tbody>
</table>
</form>
EOF;
		$this->kclass->tbline+=4;
		$this->kclass->page['title'].='修改批次信息';
		$this->kclass->page['onload'].='dc.tabhover()';
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - <a href="/s.php?module=batch&action=view&batchid='.$this->kclass->input['batchid'].'">查看批次信息</a> - 修改批次信息', 'right'=>'<a href="/s.php?module=batch&action=view&batchid='.$this->kclass->input['batchid'].'">返回查看批次信息</a>','body'=>$body));
	}
	//
	function insertStock(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			$hasItem=0;
			for($i=1;$i<6;$i++){
				if($this->kclass->input['itemStockid'][$i]>0 AND $this->kclass->input['itemQuantity'][$i]!='' AND ($this->kclass->input['itemEmpty'][$i]==0 OR $this->kclass->input['itemEmpty'][$i]==1)){
					$hasItem=1;
				}
			}
			if($hasItem==0){
				$e.='<li>需要填写至少有一条库位明细，才能新建成功。</li>';
			}
		}
		if($e){
			$this->kclass->messager(array(
				'title' => '新建库位批次信息',
				'text' => '您在新建库位批次信息的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$batch=$this->kclass->DB->queryFirst("SELECT batchid,batchno FROM batch WHERE batchid='".$this->kclass->input['batchid']."'");
		$key=array();
		for($i=1;$i<6;$i++){
			if($this->kclass->input['itemStockid'][$i]>0){
				$key[]=$i;
			}
		}
		$count=count($key);
		for($m=0;$m<$count;$m++){
			$this->kclass->DB->query("
				INSERT INTO stockitem
					(stockid,batchid,barcodeid,materialid,quantity,created,creator)
				VALUES
					('".$this->kclass->input['itemStockid'][$key[$m]]."','".$this->kclass->input['batchid']."','".$this->kclass->input['barcodeid']."','".$this->kclass->input['materialid']."','".$this->kclass->input['itemQuantity'][$key[$m]]."','".TIMENOW."','".$this->kclass->user['userid']."')
			");
			if($this->kclass->input['itemEmpty'][$key[$m]]==1){
				$this->kclass->DB->query("UPDATE stock SET ifEmpty=1 WHERE stockid='".$this->kclass->input['itemStockid'][$key[$m]]."'");
			}
		}
		$this->kclass->messager(array(
			'title' => '新建库位批次信息',
			'text' => '批次 【'.$batch['batchno'].'】 库位信息已新建成功!返回查看批次页面',
			'url' => '/s.php?module=batch&action=view&batchid='.$this->kclass->input['batchid'],
			'sec' => 2
		));
	}
	//
	function updateStock(){
		$batch=$this->kclass->DB->queryFirst("
			SELECT `batch`.batchno,`batch`.materialid,`batch`.quantity,`batch`.qualified,`batch`.disqualified,
				`barcode`.barcodeid
			FROM `batch`
			LEFT JOIN `barcode` ON (`barcode`.batchid=`batch`.batchid)
			WHERE `batch`.batchid='".$this->kclass->input['batchid']."' AND `barcode`.parentid=0
		");
		if(!$batch){
			$e='<li>数据错误，很抱歉！</li>';
		}
		if($e){
			$this->kclass->messager(array(
				'title' => '修改批次所在库位',
				'text' => '您在修改批次所在库位的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$items=$this->kclass->DB->query("
			SELECT `stockitem`.stockitemid,`stockitem`.batchid,`stockitem`.quantity,
				`stock`.stockid,`stock`.stockno,`stock`.ifEmpty
			FROM `stockitem`
			LEFT JOIN `stock` ON (`stock`.stockid=`stockitem`.stockid)
			WHERE `stockitem`.killed=0 AND batchid='".$this->kclass->input['batchid']."'
			ORDER BY `stockitem`.stockitemid ASC
		");
		if($this->kclass->DB->numRows()){
			$k=1;
			while($item=$this->kclass->DB->fetchArray($items)){
				if($item['ifEmpty']==0){
					$checked1='checked';
				}elseif($item['ifEmpty']==1){
					$checked2='checked';
				}
				$stocktr.='<tr class="'.$this->kclass->iif($this->kclass->rotate,'odd','even').'">
					<td><input type="hidden" name="itemId['.$k.']" value="'.$item['stockitemid'].'">'.$this->kclass->chooserStock(array('name'=>'itemStockid['.$k.']','width'=>'250','selectedid'=>$item['stockid'])).'</td>
					<td><input type="text" name="itemQuantity['.$k.']" value="'.$item['quantity'].'" /></td>
					<td><input type="radio" name="itemEmpty['.$k.']" value="0" '.$checked1.'>是 <input type="radio" name="itemEmpty['.$k.']" value=1 '.$checked2.'>否</td>
					</tr>';
				$k++;
			}
			$stocktr.='<tr><td colspan="4" class="bold">新增批次所在库位</td></tr>';
		}
		for($i=$k;$i<($k+5);$i++){
			$stocktr.='<tr class="'.$this->kclass->iif($this->kclass->rotate,'odd','even').'">
				<td>'.$this->kclass->chooserStock(array('name'=>'itemStockid['.$i.']','width'=>'250')).'</td>
				<td><input type="text" name="itemQuantity['.$i.']" /></td>
				<td><input type="radio" name="itemEmpty['.$i.']" value="0" checked>是 <input type="radio" name="itemEmpty['.$i.']" value=1 >否</td></tr>';
		}
$body=<<<EOF
<div class="title">{$batch['batchno']}</div>
<table class="hundred">
<thead><tr><th colspan="4">基本信息</th></tr></thead>
<tbody>
<tr class="odd">
<td width="100">批次编号：</td><td width="475">{$batch['batchno']}</td>
<td width="100">总　　数：</td><td>{$batch['quantity']}</td>
</tr>
<tr class="even">
<td>良品数量：</td><td>{$batch['qualified']}</td>
<td>不良品数：</td><td>{$batch['disqualified']}</td>
</tr>
</tbody>
</table>
<form method="post" action="/s.php?module=batch&action=doupdateStock" name="batch">
<input type="hidden" name="module" value="batch" />
<input type="hidden" name="action" value="doupdateStock" />
<input type="hidden" name="batchid" value="{$this->kclass->input['batchid']}" />
<input type="hidden" name="materialid" value="{$batch['materialid']}" />
<input type="hidden" name="barcodeid" value="{$batch['barcodeid']}" />
<input type="hidden" name="quantity" value="{$batch['quantity']}" />
<table>
<thead><tr><th colspan="4">库位明细</th></tr></thead>
<tr class="center"><td>库位 <span class="red bold">*</span></td><td>数量 <span class="red bold">*</span></td><td>是否空闲 <span class="red bold">*</span></td></tr>
{$stocktr}
<tr class="odd center" nohover>
<td colspan=4><input type="submit" value="  提交  " accesskey="s" onclick="dc.supplier.check(document.supplier)"><input type="reset" value="  复位  "/></td>
</tr>
</tbody>
</table>
</form>
EOF;
		$this->kclass->tbline+=4;
		$this->kclass->page['title'].='修改批次所在库位';
		$this->kclass->page['onload'].='dc.tabhover()';
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - <a href="/s.php?module=batch&action=view&batchid='.$this->kclass->input['batchid'].'">查看批次信息</a> - 修改批次所在库位', 'right'=>'<a href="/s.php?module=batch&action=view&batchid='.$this->kclass->input['batchid'].'">返回查看批次信息</a>','body'=>$body));
	}
	//
	function doupdateStock(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			$hasItem=0;
			$j=count($this->kclass->input['itemStockid']);
			for($i=1;$i<$j;$i++){
				if($this->kclass->input['itemStockid'][$i]>0 AND $this->kclass->input['itemQuantity'][$i]!='' AND ($this->kclass->input['itemEmpty'][$i]==0 OR $this->kclass->input['itemEmpty'][$i]==1)){
					$hasItem=1;
					$quantity+=$this->kclass->input['itemQuantity'][$i];
				}
			}
			if($hasItem==0){
				$e.='<li>需要填写至少有一条库位明细，才能新建成功。</li>';
			}
			if($quantity>$this->kclass->input['quantity']){
				$e.='<li>放置在库位的数量大于该批次的总数</li>';
			}
		}
		if($e){
			$this->kclass->messager(array(
				'title' => '修改修改批次所在库位',
				'text' => '您在修改修改批次所在库位的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}

		$batch=$this->kclass->DB->queryFirst("SELECT batchid,batchno FROM batch WHERE batchid='".$this->kclass->input['batchid']."'");
		$key=array();
		for($i=1;$i<6;$i++){
			if($this->kclass->input['itemStockid'][$i]>0){
				$key[]=$i;
			}
		}
		$count=count($key);
		for($m=0;$m<$count;$m++){
			if($this->kclass->input['itemId'][$key[$m]]>0){
				$this->kclass->DB->query("UPDATE `stockitem` SET killed=".TIMENOW."  WHERE stockitemid='".$this->kclass->input['itemId'][$key[$m]]."'");
				if($this->kclass->input['itemEmpty'][$key[$m]]==1){
					$this->kclass->DB->query("UPDATE stock SET ifEmpty=0 WHERE stockid='".$this->kclass->input['itemStockid'][$key[$m]]."'");
				}
			}
			$this->kclass->DB->query("
				INSERT INTO stockitem
					(stockid,batchid,barcodeid,materialid,quantity,created,creator)
				VALUES
					('".$this->kclass->input['itemStockid'][$key[$m]]."','".$this->kclass->input['batchid']."','".$this->kclass->input['barcodeid']."','".$this->kclass->input['materialid']."','".$this->kclass->input['itemQuantity'][$key[$m]]."','".TIMENOW."','".$this->kclass->user['userid']."')
			");

			if($this->kclass->input['itemEmpty'][$key[$m]]==1){
				$this->kclass->DB->query("UPDATE stock SET ifEmpty=1 WHERE stockid='".$this->kclass->input['itemStockid'][$key[$m]]."'");
			}
		}
		$this->kclass->messager(array(
			'title' => '修改批次所在库位信息',
			'text' => '批次 【'.$batch['batchno'].'】 库位信息已新建成功!返回查看批次页面',
			'url' => '/s.php?module=batch&action=view&batchid='.$this->kclass->input['batchid'],
			'sec' => 2
		));
	}
	//
	function kill(){
		if($this->kclass->input['batchid']<=0){
			$this->kclass->boinkIt('/s.php?module=batch');
		}
		if(!$this->kclass->input['rt'] AND $this->kclass->input['confirm'] == 0){
			$this->kclass->boinkIt('/s.php?module=batch');
		}
		if($this->kclass->input['batchid'] < 0){
			$e = '<li>请从正确的页面进行操作，如果此问题持续存在，请联系网站管理员。</li>';
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '删除出库单',
				'text' => '您在删除出库单的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		if($this->kclass->input['rt']=='view'){
			$rt = '&action=view&batchid='.$this->kclass->input['batchid'];
		}elseif($this->kclass->input['rt']=='list'){
			$rt = '#'.$this->kclass->input['batchid'];
		}
		$batch = $this->kclass->DB->queryFirst("
			SELECT batchno
			FROM batch
			WHERE batchid='".$this->kclass->input['batchid']."'
			LIMIT 0,1
		");
		if($batch){
			$this->kclass->DB->query("
				UPDATE `batch`
				SET killed=".TIMENOW.",killer='".$this->kclass->user['userid']."'
				WHERE batchid='".$this->kclass->input['batchid']."'
			");
			$this->kclass->messager(array(
				'title' => '删除出库单成功',
				'text' => '出库单 <b>'.$batch['batchno'].'</b> 已成功被标记为删除!',
				'url' => '/s.php?module=batch'.$rt,
				'sec' => 3
			));
		}else{
			$this->kclass->messager(array(
				'title' => '删除出库单失败',
				'text' => '您要删除的出库单，不存在！',
				'url' => '/s.php?module=batch'.$rt,
				'sec' => 3
			));
		}
	}

	//
	function remove(){
		if($this->kclass->input['batchid']<=0){
			$this->kclass->boinkIt('/s.php?module=batch');
		}
		$batch = $this->kclass->DB->queryFirst("
			SELECT batchno
			FROM batch
			WHERE batchid='".$this->kclass->input['batchid']."'
		");
$body = <<<EOF
<form action="/s.php?module=batch&action=kill" name="batch" method="post">
<input type="hidden" name="module" value="batch">
<input type="hidden" name="action" value="kill">
<input type="hidden" name="batchid" value="{$this->kclass->input['batchid']}">
<table><thead>
<thead>
<tr>
<td>删除出库单：{$batch['batchno']}</td>
</tr>
</thead>
<tbody>
<tr class="odd">
<td class="middle">你确定要删除出库单: <a href="/s.php?module=batch&action=view&batchid={$this->kclass->input['batchid']}" class="big bold" target="_blank">{$batch['batchno']}</a> 吗?</td>
</tr>
<tr class="even">
<td align="center">
	<label for="c1" class="red"><input type="radio" id="c1" name="confirm" value="1">是</label>
	<label for="c0" class="green"><input type="radio" id="c0" name="confirm" value="0" checked>否</label>
</td>
</tr>
<tr class="odd">
<td align="center" nohover>
	<input type="submit" value="   保存   " accesskey="s">
	<input type="reset" value="   复位   ">
</td>
</tr>
</tbody>
</table>
</form>
EOF;
		$this->kclass->page['title'] .= ' - 删除 - '.$batch['batchno'];
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 删除出库单 - '.$batch['title'], 'right' => '<a href="/s.php?module=batch">返回列表</a>', 'body'=>$body));
	}

	//
	function revival(){
		if($this->kclass->input['batchid']<=0){
			$this->kclass->boinkIt('/s.php?module=batch');
		}
		if(!$this->kclass->input['rt'] AND $this->kclass->input['confirm'] == 0){
			$this->kclass->boinkIt('/s.php?module=batch');
		}
		if($this->kclass->input['batchid'] < 0){
			$e = '<li>请从正确的页面进行操作，如果此问题持续存在，请联系网站管理员。</li>';
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '恢复出库单',
				'text' => '您在恢复出库单的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$batch = $this->kclass->DB->queryFirst("
			SELECT batchno
			FROM batch
			WHERE batchid='".$this->kclass->input['batchid']."'
		");
		if($this->kclass->input['rt']=='view'){
			$rt = '&action=view&batchid='.$this->kclass->input['batchid'];
		}elseif($this->kclass->input['rt']=='list'){
			$rt = '#'.$this->kclass->input['batchid'];
		}
		if($batch){
			$this->kclass->DB->query("
				UPDATE `batch`
				SET killed=0,killer=0
				WHERE batchid='".$this->kclass->input['batchid']."'
			");
			$this->kclass->messager(array(
				'title' => '恢复出库单成功',
				'text' => '出库单 <b>'.$batch['batchno'].'</b> 已成功被恢复！',
				'url' => '/s.php?module=batch'.$rt,
				'sec' => 3
			));
		}else{
			$this->kclass->messager(array(
				'title' => '恢复出库单失败',
				'text' => '您要恢复的出库单不存在！',
				'url' => '/s.php?module=batch'.$rt,
				'sec' => 3
			));
		}
	}

	//
	function restore(){
		if($this->kclass->input['batchid']<=0){
			$this->kclass->boinkIt('/s.php?module=batch');
		}
		$batch = $this->kclass->DB->queryFirst("
			SELECT batchno
			FROM batch
			WHERE batchid='".$this->kclass->input['batchid']."'
		");
$body = <<<EOF
<form action="/s.php?module=batch&action=revival" name="batch" method="post">
<input type="hidden" name="module" value="batch">
<input type="hidden" name="action" value="revival">
<input type="hidden" name="batchid" value="{$this->kclass->input['batchid']}">
<table><thead>
<thead>
<tr>
<td>恢复供应商：{$batch['batchno']}</td>
</tr>
</thead>
<tbody>
<tr class="odd">
<td class="middle">你确定要恢复出库单: <a href="/s.php?module=batch&action=view&batchid={$this->kclass->input['batchid']}" class="big bold" target="_blank">{$batch['batchno']}</a> 吗?</td>
</tr>
<tr class="even">
<td align="center">
	<label for="c1" class="red"><input type="radio" id="c1" name="confirm" value="1">是</label>
	<label for="c0" class="green"><input type="radio" id="c0" name="confirm" value="0" checked>否</label>
</td>
</tr>
<tr class="odd">
<td align="center" nohover>
	<input type="submit" value="   保存   " accesskey="s">
	<input type="reset" value="   复位   ">
</td>
</tr>
<tbody>
</table>
</form>
EOF;
		$this->kclass->page['title'] .= ' - 恢复 - '.$batch['batchno'];
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 恢复出库单 - '.$batch['batchno'], 'right' => '<a href="/s.php?module=batch">返回列表</a>', 'body'=>$body));
	}
}
?>