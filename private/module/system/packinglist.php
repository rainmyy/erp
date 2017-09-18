<?php
class packinglist{
	function autoRun(){
		$this->kclass->page['title'] = '装箱清单';
		$this->baseurl = '<a href="/s.php">首页</a> ';
		$this->right = '<span class="small"><a href="/s.php?module=packlist&action=find">查找</a>：<form style="display:inline-block" action="/s.php?module=packlist&action=list" name="packlist" method="post"><input type="text" style="width:50px;height:15px;margin-top:-3px" name="title" value="'.$this->kclass->input['title'].'"><input type="submit" value="找" accesskey="s" style="height:23px"></form></span>';
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
			case 'viewPackingSize': // 查看物资包装规格
				$this->viewPackingSize();
			break;
			case 'updatePackingSize': // 修改物资包装规格
				$this->updatePackingSize();
			break;
			case 'doupdatePackingSize':
				$this->doupdatePackingSize();
			break;
			default:
				$this->mmlist();
		}
	}
	//
	function view(){
		if(!$this->kclass->input['packinglistid'] > 0)$this->kclass->boinkIt('/s.php?module=packinglist');

		$packinglist = $this->kclass->DB->queryFirst("
			SELECT pl.packinglistid,pl.packinglistno,pl.type,pl.module,pl.mid,pl.cartonCount,pl.netWeight,pl.grossWeight,pl.volume,pl.modified,pl.created,
				c.customerid,c.title AS customer,
				m.realname AS modifier,mem.realname AS creator
			FROM `packinglist` AS pl
			LEFT JOIN `customer` AS c ON (c.customerid=pl.customerid)
			LEFT JOIN `member` AS m ON (m.userid=pl.modifier)
			LEFT JOIN `member` AS mem ON (mem.userid=pl.creator)
			WHERE pl.packinglistid='".$this->kclass->input['packinglistid']."'
		");
		if(!$packinglist)	$this->kclass->boinkIt('/s.php?module=packinglist&action=list');
		if($packinglist['modifier']>0){
			$modified = '，'.$packinglist['modifier'].' 于 '.date('y-m-d H:i:s', $packinglist['modified']).' 最后修改';
		}
		$created = date('y-m-d H:i:s', $packinglist['created']);
		if($packinglist['module']=='order'){
			$module=$this->kclass->DB->queryFirst("
				SELECT orderid,orderno AS no
				FROM `order`
				WHERE orderid={$packinglist['mid']}
			");
		}elseif($packinglist['module']=='sample'){
			$module=$this->kclass->DB->queryFirst("
				SELECT sampleid,sampleno AS no
				FROM `sample`
				WHERE sampleid={$packinglist['mid']}
			");
		}
		
		$items=$this->kclass->DB->query("
			SELECT pli.cartonno,pli.length,pli.width,pli.height,pli.volume,pli.perCarton,pli.perNetWeight,pli.perGrossWeight,pli.netWeight,pli.grossWeight,pli.startno,pli.endno,pli.remark,
				mv.versionid,mv.materialid,mv.itemid
			FROM packinglistitem AS pli
			LEFT JOIN materialversion AS mv ON (pli.versionid=mv.versionid)
			WHERE pli.killed=0 AND pli.packinglistid={$packinglist['packinglistid']}
			ORDER BY cartonno ASC
		");
		if($this->kclass->DB->numRows()){
			$k=1;
			$cartonno=array();
			while($item=$this->kclass->DB->fetchArray($items)){
				if(!in_array($item['cartonno'],$cartonno)){
					$cartonno[]=$item['cartonno'];
				}
				$materialInfo=$this->kclass->getMaterial(array('materialid'=>$item['materialid'],'itemid'=>$item['itemid']));
				$itemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').'">
					<td>'.$k.'</td>
					<td class="middle gray">'.str_pad($item['cartonno'],2,0,'STR_PAD_LEFT').'</td>
					<td><a href="'.$materialInfo['url'].'">'.$materialInfo['no'].'</a>　'.$materialInfo['material'].'　'.$materialInfo['standard'].'</td>
					<td>'.$item['length'].' * '.$item['width'].' * '.$item['height'].'</td>
					<td>'.$item['volume'].'</td>
					<td>'.$item['perCarton'].'</td>
					<td>'.$item['perNetWeight'].'</td>
					<td>'.$item['perGrossWeight'].'</td>
					<td>'.$item['netWeight'].'</td>
					<td>'.$item['grossWeight'].'</td>
					<td>'.$item['remark'].'</td>
					<td>'.$item['startno'].' 到 '.$item['endno'].'</td>
				</tr>';
				$k++;
			}
		}

$body = <<<EOF
<div class="title"><span class="right small gray">由 {$packinglist['creator']} 于 {$created} 建立{$modified}。</span>{$packinglist['packinglistno']}</div>
<dl id="packinglist" class="tabs">
<dt>生产装箱清单</dt>
<dt></dt>
<dd>
<table class="hundred">
<thead><tr><th colspan=6>基本信息</th></tr></thead>
<tbody>
<tr>
	<td width="100">装箱清单号：</td><td width="350">{$packinglist['packinglistno']}</td>
	<td width="100">相关PO订单：</td><td width="350"><a href="/s.php?module={$packinglist['module']}&action=view&orderid={$module['orderid']}">{$module['no']}</td>
	<td width="100">客　　户：</td><td><a href="/s.php?module=customer&action=view&customerid={$packinglist['customerid']}">{$packinglist['customer']}</a></td>
</tr>
	<tr><td>总箱数：</td><td>{$packinglist['cartonCount']}</td>
		<td>总体积：</td><td>{$packinglist['volume']} m^3</td>
		<td>总重量：</td><td>{$packinglist['grossWeight']}</td></tr>
</tbody>
</table>
<table class="hundred">
	<thead><tr><th colspan=12>包装清单明细</th></tr></thead>
	<tbody><tr><td width="15">ID</td><td width="30">箱号</td><td>产品</td><td>长 x 宽 x 高 (cm)</td><td>体积 (m^3)</td><td>件/箱</td><td>每个净重(kg)</td><td>每个毛重(kg)</td><td>每箱净重(kg)</td><td>每箱毛重(kg)</td><td>备注</td><td>快速服务代码范围</td></tr>
	{$itemtr}
	</tbody>
</table>
</dd>
</dl>

EOF;
		$this->kclass->page['onload'] .= "dc.tabs({'id':'packinglist'});dc.tabhover()";
		$this->kclass->page['title'] .= ' - '.$packinglist['packinglistno'];
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl.' - 装箱清单信息', 'right' => '<a href="/s.php?module=printer&action=packinglist&packinglistid='.$packinglist['packinglistid'].'" target="_blank">打印</a>　|　'.$this->kclass->iif($packinglist['killed']==1, '<a href="/s.php?module=packinglist&action=restore&packinglistid='.$packinglist['packinglistid'].'&rt=view">恢复</a>', '<a href="/s.php?module=packinglist&action=remove&packinglistid='.$packinglist['packinglistid'].'&rt=view">删除</a>　<a href="/s.php?module=packinglist&action=update&packinglistid='.$packinglist['packinglistid'].'&rt=view">修改</a>'), 'body'=>$body));
	}
	//
	function insert(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			$count=count($this->kclass->input['cartonno']);
			$hasItem=0;
			for($i=1;$i<=$count;$i++){
				if($this->kclass->input['cartonno'][$i]>0 AND $this->kclass->input['itemVersionid'][$i]>0 AND $this->kclass->input['packingsizeid'][$i]>=0 AND  $this->kclass->input['length'][$i]>0 AND $this->kclass->input['width'][$i]>0 AND $this->kclass->input['height'][$i]>0 AND $this->kclass->input['perCarton'][$i]>0 AND $this->kclass->input['perNetWeight'][$i]>0 AND $this->kclass->input['perGrossWeight'][$i]>0 AND $this->kclass->input['netWeight'][$i]>0 AND $this->kclass->input['grossWeight'][$i]>0){
					$hasItem=1;
				}
			}
			if($hasItem==0){
				$e.='<li>请至少输入一条明细</li>';
			}
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '新建装箱清单',
				'text' => '您在新建装箱清单的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		// get paclinglistno
		$start=strtotime(date('Y-m-d',TIMENOW));
		$end=strtotime(date('Y-m-d',$start))+86400;
		$counter=$this->kclass->DB->queryFirst("SELECT COUNT(*) AS count FROM packinglist WHERE type='produce' AND created>='".$start."' AND created<='".$end."'");
		$packinglistno=$this->kclass->id(array('packinglist'=>TIMENOW,'number'=>$counter['count']));
		// insert packinglist
		$this->kclass->DB->query("
			INSERT INTO packinglist (packinglistno,type,module,mid,customerid,cartonCount,created,creator)
			VALUES ('{$packinglistno}','produce','{$this->kclass->input['m']}','{$this->kclass->input['mid']}','{$this->kclass->input['customerid']}','{$this->kclass->input['cartonCount']}','".TIMENOW."','{$this->kclass->user['userid']}')
		");
		$packinglistid=$this->kclass->DB->insertID();
		// insert item
		$cartonno=array();
		for($i=1;$i<=$count;$i++){
			if($this->kclass->input['cartonno'][$i]>0 AND $this->kclass->input['itemVersionid'][$i]>0 AND $this->kclass->input['packingsizeid'][$i]>=0 AND  $this->kclass->input['length'][$i]>0 AND $this->kclass->input['width'][$i]>0 AND $this->kclass->input['height'][$i]>0 AND $this->kclass->input['perCarton'][$i]>0 AND $this->kclass->input['perNetWeight'][$i]>0 AND $this->kclass->input['perGrossWeight'][$i]>0 AND $this->kclass->input['netWeight'][$i]>0 AND $this->kclass->input['grossWeight'][$i]>0){
				$packingsize=$this->kclass->iif($this->kclass->input['packingsizeid'][$i]>0,$this->kclass->input['packingsizeid'][$i],0);
				$volume=round(($this->kclass->input['length'][$i]*$this->kclass->input['width'][$i]*$this->kclass->input['height'][$i])/1000000,3);
				$startno=$this->kclass->iif($this->kclass->input['startno'][$i]!='',$this->kclass->input['startno'][$i],'');
				$endno=$this->kclass->iif($this->kclass->input['endno'][$i]!='',$this->kclass->input['endno'][$i],'');
				if(!in_array($this->kclass->input['cartonno'][$i],$cartonno)){
					$netWeight+=$this->kclass->input['netWeight'][$i];
					$grossWeight+=$this->kclass->input['grossWeight'][$i];
					$totalVolume+=$volume;
				}

				$this->kclass->DB->query("
					INSERT INTO packinglistitem (packinglistid,cartonno,versionid,packingsizeid,length,width,height,volume,perCarton,perNetWeight,perGrossWeight,netWeight,grossWeight,startno,endno,remark,created,creator)
					VALUES ({$packinglistid},{$this->kclass->input['cartonno'][$i]},{$this->kclass->input['itemVersionid'][$i]},{$packingsize},{$this->kclass->input['length'][$i]},{$this->kclass->input['width'][$i]},{$this->kclass->input['height'][$i]},{$volume},{$this->kclass->input['perCarton'][$i]},{$this->kclass->input['perNetWeight'][$i]},{$this->kclass->input['perGrossWeight'][$i]},{$this->kclass->input['netWeight'][$i]},{$this->kclass->input['grossWeight'][$i]},'{$startno}','{$endno}','{$this->kclass->input['remark'][$i]}','".TIMENOW."','{$this->kclass->user['userid']}')
				");
			}
		}
		$this->kclass->DB->query("UPDATE packinglist SET netWeight='{$netWeight}',grossWeight='{$grossWeight}',volume='{$totalVolume}' WHERE packinglistid={$packinglistid}");
		$this->kclass->messager(array(
			'title' => '新建装箱清单',
			'text' => '装箱清单 <b>'.$packinglistno.'</b> 的明细内容已经新建成功! ',
			'url' => '/s.php?module=packinglist&action=view&packinglistid='.$packinglistid,
			'sec' => 2
		));
	}

	//
	function add(){
		if($this->kclass->input['mid']<0){
			$e='<li>系统数据错误，很抱歉！</li>';
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '删除工作中心供应的物资',
				'text' => '您在删除工作中心供应的物资的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$start=strtotime(date('Y-m-d',TIMENOW));
		$end=strtotime(date('Y-m-d',$start))+86400;
		$counter=$this->kclass->DB->queryFirst("SELECT COUNT(*) AS count FROM packinglist WHERE type='produce' AND created>='".$start."' AND created<='".$end."'");
		$packinglistno=$this->kclass->id(array('packinglist'=>TIMENOW,'number'=>$counter['count']));
		if(!$this->kclass->input['cartonCount'] OR $this->kclass->input['cartonCount']<0){
			$body=<<<EOF
<form action="/s.php?module=packinglist&action=add" name="packinglist" method="get">
<input type="hidden" name="module" value="packinglist">
<input type="hidden" name="action" value="add">
<input type="hidden" name="m" value="{$this->kclass->input['m']}">
<input type="hidden" name="mid" value="{$this->kclass->input['mid']}">
<table style="width:500px">
<thead><tr><th colspan=4>新建包装清单</th></tr></thead>
<tbody>
<tr><td width="100">包装清单号：</td><td>{$packinglistno}</td><td>总箱数：</td><td><input type="text" name="cartonCount" size=5 /></td></tr>
<tr align="center"><td colspan=4><input type="submit" value="提交"></td></tr>
</tbody>
</table>
</form>
EOF;
		}else{
			if($this->kclass->input['m']=='order'){
				$module=$this->kclass->DB->queryFirst("
					SELECT o.orderid,o.orderno,c.customerid,c.title AS customer 
					FROM `order` AS o 
					LEFT JOIN customer AS c ON (c.customerid=o.customerid)
					WHERE o.orderid={$this->kclass->input['mid']}
				");
				$no='<a href="/s.php?module=order&action=view&orderid='.$module['orderid'].'">'.$module['orderno'].'</a>';
			}elseif($this->kclass->input['m']=='sample'){
				$module=$this->kclass->DB->queryFirst("
					SELECT s.sampleid,s.sampleno,c.customerid,c.title AS customer 
					FROM `sample` AS s 
					LEFT JOIN customer AS c ON (c.customerid=s.customerid)
					WHERE s.sampleid={$this->kclass->input['mid']}
				");
				$no='<a href="/s.php?module=sample&action=view&sampleid='.$module['sampleid'].'">'.$module['sampleno'].'</a>';
			}
			
			$orderitems=$this->kclass->DB->query("
				SELECT ri.itemid,ri.materialid,ri.materialitemid,ri.versionid,ri.quantity,ri.dateline,
					r.requirementid,r.requirementno,
					mv.title AS version,mv.qualified,mv.disqualified
				FROM `requirementitem` AS ri 
				LEFT JOIN `requirement` AS r ON (r.requirementid=ri.requirementid)
				LEFT JOIN `materialversion` AS mv ON (mv.versionid=ri.versionid)
				WHERE ri.killed=0 AND r.killed=0 AND r.locked<>0 AND ri.module='{$this->kclass->input['m']}' AND ri.mid='{$this->kclass->input['mid']}'
				ORDER BY ri.itemid ASC
			");
			if($this->kclass->DB->numRows()){
				$i=1;
				while($orderitem=$this->kclass->DB->fetchArray($orderitems)){
					$materialInfo=$this->kclass->getMaterial(array('materialid'=>$orderitem['materialid'],'itemid'=>$orderitem['materialitemid']));
					$startno=$this->kclass->DB->queryFirst("SELECT serviceno FROM serviceno WHERE orderid='".$module['orderid']."' AND versionid='".$orderitem['versionid']."' AND requirementitemid='".$orderitem['itemid']."' ORDER BY serviceno ASC LIMIT 0,1");
					$endno=$this->kclass->DB->queryFirst("SELECT serviceno FROM serviceno WHERE orderid='".$module['orderid']."' AND versionid='".$orderitem['versionid']."' AND requirementitemid='".$orderitem['itemid']."' ORDER BY serviceno DESC LIMIT 0,1");
					$orderitemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').'">
						<td>'.$i.'</td>
						<td><a href="'.$materialInfo['url'].'">'.$materialInfo['no'].'</a>　'.$materialInfo['material'].'　'.$materialInfo['standard'].'</td>
						<td>'.$orderitem['version'].'　良品数：'.$orderitem['qualified'].'　不良品数：'.$orderitem['disqualified'].'</td>
						<td>'.$orderitem['quantity'].'</td>
						<td>'.date('Y-m-d',$orderitem['dateline']).'</td>					
						<td>起始号：'.$startno['serviceno'].'　结束号：'.$endno['serviceno'].'</td>
					</tr>';
					$i++;
				}
			}
			// 获得箱数选择
			for($m=1;$m<=$this->kclass->input['cartonCount'];$m++){
				$cartons.='<option value="'.$m.'">'.$m.'</option>';
			}
			for($k=1;$k<=5;$k++){
				$itemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').'" id="packingsize'.$k.'">
					<td>'.$k.'</td>
					<td><select name="cartonno['.$k.']" style="width:35px">'.$cartons.'</select></td>
					<td>'.$this->_chooserOrderItem(array('name'=>'itemVersionid['.$k.']','id'=>'itemVersionid'.$k,'width'=>350,'hasBlank'=>1,'topname'=>'','m'=>$this->kclass->input['m'],'mid'=>$this->kclass->input['mid'],'onchange'=>'dc.material.packingsize({\'obj\':this,\'type\':\'chooser\',\'line\':'.$k.'})')).'</td>
					<td class="packingsize" width="100"></td>
					<td>长：<input type="text" class="length" name="length['.$k.']" size="5">　
						宽：<input type="text" class="width" name="width['.$k.']" size="5">　
						高：<input type="text" class="height" name="height['.$k.']" size="5"></td>
					<td><input type="text" class="perCarton" name="perCarton['.$k.']" size="5"></td>
					<td><input type="text" class="perNetWeight" name="perNetWeight['.$k.']" size="5"></td>
					<td><input type="text" class="perGrossWeight" name="perGrossWeight['.$k.']" size="5"></td>
					<td><input type="text" class="netWeight" name="netWeight['.$k.']" size="5"></td>
					<td><input type="text" class="grossWeight" name="grossWeight['.$k.']" size="5"></td>
					<td><input type="text" class="remark" name="remark['.$k.']" size="30"></td>
					<td><input type="text" name="startno['.$k.']" size="10"> 到 <input type="text" name="endno['.$k.']" size="10"></td>
				</tr>';
			}
		$itemtr.='<tr class="odd bold gray normal" id="packinglist" nohover><td colspan=12><span class="hand" onclick="dc.addLine(\'packinglist\',\'packinglist\')" style="border:2px solid gray;background:#FFE8EB">增加一行配件明细</span></td></tr>';
		$body=<<<EOF
<div class="title">{$packinglistno}</div>
<table class="hundred">
<thead><tr><th colspan=6>基本信息</th></tr></thead>
<tbody>
	<tr>
		<td width="100">包装清单号：</td><td class="red bold" width="700">{$packinglistno}</td>
		<td width="100">总 箱 数：</td><td>{$this->kclass->input['cartonCount']}</td>
	</tr>
	<tr>
		<td>订单或样品单：</td><td>{$no}</td>
		<td>供 应 商：</td><td><a href="/s.php?module=customer&action=view&customerid={$module['customerid']}">{$module['customer']}</a></td>
	</tr>
</tbody>
</table>
<table class="hundred">
<thead><tr><th colspan=6>订单【<a href="/s.php?module=order&action=view&orderid={$module['orderid']}">{$module['orderno']}</a>】的明细</th></tr></thead>
<tbody>
<tr><td width="15">ID</td><td>物资</td><td>版本</td><td>数量</td><td>需求时间</td><td>序列号</td></tr>
{$orderitemtr}
</tbody>
</table>
<form action="/s.php?module=packinglist&action=insert" method="post">
<input type="hidden" name="module" value="packinglist" />
<input type="hidden" name="action" value="insert" />
<input type="hidden" name="m" value="{$this->kclass->input['m']}" />
<input type="hidden" name="mid" value="{$this->kclass->input['mid']}" />
<input type="hidden" name="customerid" value="{$module['customerid']}" />
<input type="hidden" name="cartonCount" value="{$this->kclass->input['cartonCount']}" />
<table class="hundred"><tbody class="small"><tr class="center"><td width="15">ID</td><td>箱号</td><td>选择产品</td><td>选择包装规格</td><td>箱规格(cm) <span class="red bold">*</span></td><td>件/箱 <span class="red bold">*</span></td><td>每个净重(kg) <span class="red bold">*</span></td><td>每个毛重(kg) <span class="red bold">*</span></td><td>每箱净重(kg) <span class="red bold">*</span></td><td>每箱毛重(kg) <span class="red bold">*</span></td><td>备注</td><td>快速服务代码范围</td></tr>
{$itemtr}
</tbody>
</table>
<table class="hundred">
<tr align="center"><td colspan=9><input type="submit" value="提交" ><input type="reset" value="重置" ></td></tr>
</table>
</form>
EOF;
		}

		$this->kclass->page['title'].='新建工作中心供应的物资';
		$this->kclass->page['onload'].='dc.tabhover();';
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - <a href="/s.php?module=workcenter&action=view&workcenterid=5">查看工作中心</a> - 新建工作中心供应的物资', 'right'=>'<a href="/s.php?module=workcenter&action=view&workcenterid=5">返回查看工作中心</a>','body'=>$body));
	}

	//
	function doupdate(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			$packinglist = $this->kclass->DB->queryFirst("SELECT packinglistid, packinglistno FROM `packinglist` WHERE killed=0 AND packinglistid='".$this->kclass->input['packinglistid']."'");
			if(!$packinglist){
				$e .= '<li>您要编辑的装箱清单并不存在，请返回列表刷新后再操作。</li>';
			}
			$count=count($this->kclass->input['cartonno']);
			$hasItem=0;
			for($i=1;$i<=$count;$i++){
				if($this->kclass->input['itemKill'][$i]=='' AND $this->kclass->input['cartonno'][$i]>0 AND $this->kclass->input['itemVersionid'][$i]>0 AND $this->kclass->input['packingsizeid'][$i]>=0 AND  $this->kclass->input['length'][$i]>0 AND $this->kclass->input['width'][$i]>0 AND $this->kclass->input['height'][$i]>0 AND $this->kclass->input['perCarton'][$i]>0 AND $this->kclass->input['perNetWeight'][$i]>0 AND $this->kclass->input['perGrossWeight'][$i]>0 AND $this->kclass->input['netWeight'][$i]>0 AND $this->kclass->input['grossWeight'][$i]>0){
					$hasItem=1;
				}
			}
			if($hasItem==0){
				$e.='<li>请至少输入一条明细</li>';
			}
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '修改装箱清单',
				'text' => '您在修改装箱清单的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		
		for($i=1;$i<=$count;$i++){
			if($this->kclass->input['cartonno'][$i]>0 AND $this->kclass->input['itemVersionid'][$i]>0 AND $this->kclass->input['packingsizeid'][$i]>=0 AND  $this->kclass->input['length'][$i]>0 AND $this->kclass->input['width'][$i]>0 AND $this->kclass->input['height'][$i]>0 AND $this->kclass->input['perCarton'][$i]>0 AND $this->kclass->input['perNetWeight'][$i]>0 AND $this->kclass->input['perGrossWeight'][$i]>0 AND $this->kclass->input['netWeight'][$i]>0 AND $this->kclass->input['grossWeight'][$i]>0){
				$volume=round(($this->kclass->input['length'][$i]*$this->kclass->input['width'][$i]*$this->kclass->input['height'][$i])/1000000,3);
				$startno=$this->kclass->iif($this->kclass->input['startno'][$i]!='',$this->kclass->input['startno'][$i],'');
				$endno=$this->kclass->iif($this->kclass->input['endno'][$i]!='',$this->kclass->input['endno'][$i],'');
				if($this->kclass->input['itemId'][$i]>0){
					if($this->kclass->input['itemKill'][$i]>0 AND $this->kclass->input['itemKill'][$i]=$this->kclass->input['itemId'][$i]){
						$this->kclass->DB->query("UPDATE packinglistitem SET killed='".TIMENOW."',killer='".$this->kclass->user['userid']."' WHERE itemid={$this->kclass->input['itemId'][$i]}");
					}else{
						if(!in_array($this->kclass->input['cartonno'][$i],$cartonno)){
							$netWeight+=$this->kclass->input['netWeight'][$i];
							$grossWeight+=$this->kclass->input['grossWeight'][$i];
							$totalVolume+=$volume;
						}

						$this->kclass->DB->query("
							UPDATE packinglistitem SET 
								cartonno='{$this->kclass->input['cartonno'][$i]}',
								versionid='{$this->kclass->input['itemVersionid'][$i]}',
								length='{$this->kclass->input['length'][$i]}',
								width='{$this->kclass->input['width'][$i]}',
								height='{$this->kclass->input['height'][$i]}',
								volume='{$volume}',
								perCarton='{$this->kclass->input['perCarton'][$i]}',
								perNetWeight='{$this->kclass->input['perNetWeight'][$i]}',
								perGrossWeight='{$this->kclass->input['perGrossWeight'][$i]}',
								netWeight='{$this->kclass->input['netWeight'][$i]}',
								grossWeight='{$this->kclass->input['grossWeight'][$i]}',
								startno='{$startno}',
								endno='{$endno}',
								remark='{$this->kclass->input['remark'][$i]}',
								modified='".TIMENOW."',
								modifier='".$this->kclass->user['userid']."'
							WHERE itemid={$this->kclass->input['itemId'][$i]}
						");
					}
				}else{
					if(!in_array($this->kclass->input['cartonno'][$i],$cartonno)){
						$netWeight+=$this->kclass->input['netWeight'][$i];
						$grossWeight+=$this->kclass->input['grossWeight'][$i];
						$totalVolume+=$volume;
					}
					$this->kclass->DB->query("
						INSERT INTO packinglistitem (packinglistid,cartonno,versionid,packingsizeid,length,width,height,volume,perCarton,perNetWeight,perGrossWeight,netWeight,grossWeight,startno,endno,remark,created,creator)
						VALUES ({$packinglist['packinglistid']},{$this->kclass->input['cartonno'][$i]},{$this->kclass->input['itemVersionid'][$i]},{$this->kclass->input['packingsizeid'][$i]},{$this->kclass->input['length'][$i]},{$this->kclass->input['width'][$i]},{$this->kclass->input['height'][$i]},{$volume},{$this->kclass->input['perCarton'][$i]},{$this->kclass->input['perNetWeight'][$i]},{$this->kclass->input['perGrossWeight'][$i]},{$this->kclass->input['netWeight'][$i]},{$this->kclass->input['grossWeight'][$i]},'{$startno}','{$endno}','{$this->kclass->input['remark'][$i]}','".TIMENOW."','{$this->kclass->user['userid']}')
					");
				}
			}
		}
		$this->kclass->DB->query("
			UPDATE packinglist SET 
			cartonCount={$this->kclass->input['cartonCount']},
			netWeight='{$netWeight}',
			grossWeight='{$grossWeight}',
			volume='{$totalVolume}' 
			WHERE packinglistid={$packinglist['packinglistid']}
		");
		$this->kclass->messager(array(
			'title' => '修改装箱清单',
			'text' => '装箱清单 <b>'.$packinglist['packinglistno'].'</b> 的信息已成功修改!',
			'url' => '/s.php?module=packinglist&action=view&packinglistid='.$packinglist['packinglistid'],
			'sec' => 3
		));
	}

	//
	function update(){
		if($this->kclass->input['packinglistid']<=0 OR !$packinglist = $this->kclass->DB->queryFirst("SELECT * FROM `packinglist` WHERE `packinglistid`='".$this->kclass->input['packinglistid']."'")){
			$this->kclass->boinkIt('/s.php?module=packinglist');
		}
	if(!$this->kclass->input['cartonCount'] OR $this->kclass->input['cartonCount']<0){
			$body=<<<EOF
<form action="/s.php?module=packinglist&action=update" name="packinglist" method="get">
<input type="hidden" name="module" value="packinglist">
<input type="hidden" name="action" value="update">
<input type="hidden" name="packinglistid" value="{$this->kclass->input['packinglistid']}">
<table style="width:500px">
<thead><tr><th colspan=4>修改包装清单</th></tr></thead>
<tbody>
<tr><td width="100">包装清单号：</td><td>{$packinglist['packinglistno']}</td><td>总箱数：</td><td><input type="text" name="cartonCount" size=5 value="{$packinglist['cartonCount']}" /></td></tr>
<tr align="center"><td colspan=4><input type="submit" value="提交"></td></tr>
</tbody>
</table>
</form>
EOF;
		}else{
			if($packinglist['module']=='order'){
				$module=$this->kclass->DB->queryFirst("
					SELECT o.orderid,o.orderno,c.customerid,c.title AS customer 
					FROM `order` AS o 
					LEFT JOIN customer AS c ON (c.customerid=o.customerid)
					WHERE o.orderid={$packinglist['mid']}
				");
			}elseif($packinglist['module']=='sample'){
				$module=$this->kclass->DB->queryFirst("
					SELECT s.sampleid,s.sampleno,c.customerid,c.title AS customer 
					FROM `sample` AS s 
					LEFT JOIN customer AS c ON (c.customerid=o.customerid)
					WHERE s.sampleid={$packinglist['mid']}
				");
			}

			$orderitems=$this->kclass->DB->query("
				SELECT ri.itemid,ri.materialid,ri.materialitemid,ri.versionid,ri.quantity,ri.dateline,
					r.requirementid,r.requirementno,
					mv.title AS version,mv.qualified,mv.disqualified
				FROM `requirementitem` AS ri 
				LEFT JOIN `requirement` AS r ON (r.requirementid=ri.requirementid)
				LEFT JOIN `materialversion` AS mv ON (mv.versionid=ri.versionid)
				WHERE ri.killed=0 AND r.killed=0 AND r.locked<>0 AND ri.module='{$packinglist['module']}' AND ri.mid='{$packinglist['mid']}'
				ORDER BY ri.itemid ASC
			");
			if($this->kclass->DB->numRows()){
				$i=1;
				while($orderitem=$this->kclass->DB->fetchArray($orderitems)){
					$materialInfo=$this->kclass->getMaterial(array('materialid'=>$orderitem['materialid'],'itemid'=>$orderitem['materialitemid']));
					$startno=$this->kclass->DB->queryFirst("SELECT serviceno FROM serviceno WHERE orderid='".$module['orderid']."' AND versionid='".$orderitem['versionid']."' AND requirementitemid='".$orderitem['itemid']."' ORDER BY serviceno ASC LIMIT 0,1");
					$endno=$this->kclass->DB->queryFirst("SELECT serviceno FROM serviceno WHERE orderid='".$module['orderid']."' AND versionid='".$orderitem['versionid']."' AND requirementitemid='".$orderitem['itemid']."' ORDER BY serviceno DESC LIMIT 0,1");
					$orderitemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').'">
						<td>'.$i.'</td>
						<td><a href="'.$materialInfo['url'].'">'.$materialInfo['no'].'</a>　'.$materialInfo['material'].'　'.$materialInfo['standard'].'</td>
						<td>'.$orderitem['version'].'　良品数：'.$orderitem['qualified'].'　不良品数：'.$orderitem['disqualified'].'</td>
						<td>'.$orderitem['quantity'].'</td>
						<td>'.date('Y-m-d',$orderitem['dateline']).'</td>					
						<td>起始号：'.$startno['serviceno'].'　结束号：'.$endno['serviceno'].'</td>
					</tr>';
					$i++;
				}
			}

			// 已经存在的包装明细
			$items=$this->kclass->DB->query("
				SELECT pli.*,pz.title AS packingsize
				FROM packinglistitem AS pli
				LEFT JOIN packingsize AS pz ON (pz.packingsizeid=pli.packingsizeid)
				WHERE pli.killed=0 AND pli.packinglistid={$packinglist['packinglistid']}
				ORDER BY packinglistid ASC
			");
			$j=1;
			if($this->kclass->DB->numRows()){
				while($item=$this->kclass->DB->fetchArray($items)){
					// 获得箱数选择
					for($m=1;$m<=$this->kclass->input['cartonCount'];$m++){
						$cartons.='<option value="'.$m.'"';
						if($m==$item['cartonno']){
							$cartons.=' selected';
						}
						$cartons.='>'.$m.'</option>';
					}
					$itemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').'" id="packingsize'.$j.'">
						<td><input type="hidden" name="itemId['.$j.']" value="'.$item['itemid'].'" />'.$j.'</td>
						<td><select name="cartonno['.$j.']" style="width:35px">'.$cartons.'</select></td>
						<td>'.$this->_chooserOrderItem(array('name'=>'itemVersionid['.$j.']','id'=>'itemVersionid'.$j,'width'=>350,'hasBlank'=>1,'topname'=>'','m'=>$packinglist['module'],'mid'=>$packinglist['mid'],'selectedid'=>$item['versionid'],'onchange'=>'dc.material.packingsize({\'obj\':this,\'type\':\'chooser\',\'line\':'.$j.'})')).'</td>
						<td class="packingsize" width="100">'.$item['packingsize'].'</td>
						<td>长：<input type="text" class="length" name="length['.$j.']" value="'.$item['length'].'" size="5">　
							宽：<input type="text" class="width" name="width['.$j.']" value="'.$item['width'].'" size="5">　
							高：<input type="text" class="height" name="height['.$j.']" value="'.$item['height'].'" size="5"></td>
						<td><input type="text" class="cartonNum" name="perCarton['.$j.']" value="'.$item['perCarton'].'" size="5"></td>
						<td><input type="text" class="weight" name="perNetWeight['.$j.']" value="'.$item['perNetWeight'].'" size="5"></td>
						<td><input type="text" class="weight" name="perGrossWeight['.$j.']" value="'.$item['perGrossWeight'].'" size="5"></td>
						<td><input type="text" class="weight" name="netWeight['.$j.']" value="'.$item['netWeight'].'" size="5"></td>
						<td><input type="text" class="weight" name="grossWeight['.$j.']" value="'.$item['grossWeight'].'" size="5"></td>
						<td><input type="text" class="remark" name="remark['.$j.']" value="'.$item['remark'].'" size="20"></td>
						<td><input type="text" name="startno['.$j.']" value="'.$item['startno'].'" size="10"> 到 <input type="text" name="endno['.$j.']" value="'.$item['endno'].'" size="10"></td>
						<td><input type="checkbox" name="itemKill['.$j.']" value="'.$item['itemid'].'"></td>
					</tr>';
					$j++;
				}
				$itemtr.='<tr><td colspan=13>新建包装明细</td></tr>';
			}
			// 获得箱数选择
			for($m=1;$m<=$this->kclass->input['cartonCount'];$m++){
				$cartonse.='<option value="'.$m.'">'.$m.'</option>';
			}
			for($k=$j;$k<=5+$j;$k++){
				$itemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').'" id="packingsize'.$k.'">
					<td>'.$k.'</td>
					<td><select name="cartonno['.$k.']" style="width:35px">'.$cartonse.'</select></td>
					<td>'.$this->_chooserOrderItem(array('name'=>'itemVersionid['.$k.']','id'=>'itemVersionid'.$k,'width'=>350,'hasBlank'=>1,'topname'=>'','m'=>$packinglist['module'],'mid'=>$packinglist['mid'],'onchange'=>'dc.material.packingsize({\'obj\':this,\'type\':\'chooser\',\'line\':'.$k.'})')).'</td>
					<td class="packingsize" width="100"></td>
					<td>长：<input type="text" class="length" name="length['.$k.']" size="5">　
						宽：<input type="text" class="width" name="width['.$k.']" size="5">　
						高：<input type="text" class="height" name="height['.$k.']" size="5"></td>
					<td><input type="text" class="perCarton" name="perCarton['.$k.']" size="5"></td>
					<td><input type="text" class="perNetWeight" name="perNetWeight['.$k.']" size="5"></td>
					<td><input type="text" class="perGrossWeight" name="perGrossWeight['.$k.']" size="5"></td>
					<td><input type="text" class="netWeight" name="netWeight['.$k.']" size="5"></td>
					<td><input type="text" class="grossWeight" name="grossWeight['.$k.']" size="5"></td>
					<td><input type="text" class="remark" name="remark['.$k.']" size="20"></td>
					<td><input type="text" name="startno['.$k.']" size="10"> 到 <input type="text" name="endno['.$k.']" size="10"></td>
					<td></td>
				</tr>';
			}
		$body=<<<EOF
<div class="title">{$packinglist['packinglistno']}</div>
<table class="hundred">
<thead><tr><th colspan=6>基本信息</th></tr></thead>
<tbody>
	<tr>
		<td width="100">包装清单号：</td><td class="red bold" width="700">{$packinglist['packinglistno']}</td>
		<td width="100">总 箱 数：</td><td>{$this->kclass->input['cartonCount']}</td>
	</tr>
	<tr>
		<td>订单或样品单：</td><td><a href="/s.php?module=order&action=view&orderid={$module['orderid']}">{$module['orderno']}</a></td>
		<td>供 应 商：</td><td><a href="/s.php?module=customer&action=view&customerid={$module['customerid']}">{$module['customer']}</a></td>
	</tr>
</tbody>
</table>
<table class="hundred">
<thead><tr><th colspan=6>订单【<a href="/s.php?module=order&action=view&orderid={$module['orderid']}">{$module['orderno']}</a>】的明细</th></tr></thead>
<tbody>
<tr><td width="15">ID</td><td>物资</td><td>版本</td><td>数量</td><td>需求时间</td><td>序列号</td></tr>
{$orderitemtr}
</tbody>
</table>
<form action="/s.php?module=packinglist&action=doupdate" method="post">
<input type="hidden" name="module" value="packinglist" />
<input type="hidden" name="action" value="doupdate" />
<input type="hidden" name="packinglistid" value="{$packinglist['packinglistid']}">
<input type="hidden" name="cartonCount" value="{$this->kclass->input['cartonCount']}" />
<table class="hundred"><tbody class="small"><tr class="center"><td width="15">ID</td><td>箱号</td><td>选择产品</td><td>选择包装规格</td><td>箱规格(cm) <span class="red bold">*</span></td><td>件/箱 <span class="red bold">*</span></td><td>每个净重(kg) <span class="red bold">*</span></td><td>每个毛重(kg) <span class="red bold">*</span></td><td>每箱净重(kg) <span class="red bold">*</span></td><td>每箱毛重(kg) <span class="red bold">*</span></td><td>备注</td><td>快速服务代码范围</td><td>删</td></tr>
{$itemtr}
</tbody>
</table>
<table class="hundred">
<tr align="center"><td colspan=9><input type="submit" value="提交" ><input type="reset" value="重置" ></td></tr>
</table>
</form>
EOF;
		}
		$this->kclass->page['title'].='新建工作中心供应的物资';
		$this->kclass->page['onload'].='dc.tabhover();';
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - <a href="/s.php?module=workcenter&action=view&workcenterid=5">查看工作中心</a> - 新建工作中心供应的物资', 'right'=>'<a href="/s.php?module=workcenter&action=view&workcenterid=5">返回查看工作中心</a>','body'=>$body));
	}
	//
	function kill(){
		if($this->kclass->input['productid']<=0){
			$this->kclass->boinkIt('/s.php?module=packinglist');
		}
		if($this->kclass->input['confirm'] == 0){
			$this->kclass->boinkIt('/s.php?module=packinglist&action=view&productid='.$this->kclass->input['productid']);
		}
		if($this->kclass->input['productid'] < 0){
			$e = '请从正确的页面进行操作，如果此问题持续存在，请联系网站管理员。';
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '删除装箱清单',
				'text' => '您在删除装箱清单的过程中有以下错误：'.$e.'',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		if($this->kclass->input['rt']=='view'){
			$rt = '&action=view&productid='.$this->kclass->input['productid'];
		}elseif($this->kclass->input['rt']=='list'){
			$rt = '#'.$this->kclass->input['productid'];
		}
		$packinglist = $this->kclass->DB->queryFirst("
			SELECT title
			FROM packinglist
			WHERE productid='".$this->kclass->input['productid']."'
		");
		if($packinglist){
			$this->kclass->DB->query("
				UPDATE `packinglist`
				SET killed=".TIMENOW."
				WHERE productid='".$this->kclass->input['productid']."'
			");
			$this->kclass->messager(array(
				'title' => '删除装箱清单成功',
				'text' => '装箱清单 <b>'.$packinglist['title'].'</b> 已成功被标记为删除!',
				'url' => '/s.php?module=packinglist'.$rt,
				'sec' => 2
			));
		}else{
			$this->kclass->messager(array(
				'title' => '删除装箱清单失败',
				'text' => '您要删除的装箱清单，不存在！',
				'url' => '/s.php?module=packinglist'.$rt,
				'sec' => 3
			));
		}
	}

	//
	function remove(){
		if($this->kclass->input['productid']<=0){
			$this->kclass->boinkIt('/s.php?module=packinglist');
		}
		$packinglist = $this->kclass->DB->queryFirst("
			SELECT title
			FROM packinglist
			WHERE productid='".$this->kclass->input['productid']."'
		");
$body = <<<EOF
<form action="/s.php?module=packinglist&action=kill" name="packinglist" method="post">
<input type="hidden" name="module" value="packinglist">
<input type="hidden" name="action" value="kill">
<input type="hidden" name="productid" value="{$this->kclass->input['productid']}">
<table><thead>
<thead>
<tr>
	<th>删除装箱清单：{$packinglist['title']}</th>
</tr>
</thead>
<tbody>
<tr class="even">
	<td class="middle">你确定要删除装箱清单: <a href="/s.php?module=packinglist&action=view&productid={$this->kclass->input['productid']}" class="big bold" target="_blank">{$packinglist['title']}</a> 吗?</td>
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
		$this->kclass->page['title'] .= ' - 删除 - '.$packinglist['title'];
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 删除装箱清单 - '.$packinglist['title'], 'right' => '<a href="/s.php?module=packinglist">返回列表</a>', 'body'=>$body));
	}

	//
	function revival(){
		if($this->kclass->input['productid']<=0){
			$this->kclass->boinkIt('/s.php?module=packinglist');
		}
		if($this->kclass->input['confirm'] == 0){
			$this->kclass->boinkIt('/s.php?module=packinglist&action=view&productid='.$this->kclass->input['productid']);
		}
		if($this->kclass->input['productid'] < 0){
			$e = '请从正确的页面进行操作，如果此问题持续存在，请联系网站管理员。';
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '恢复装箱清单',
				'text' => '您在恢复装箱清单的过程中有以下错误：'.$e.'',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$packinglist = $this->kclass->DB->queryFirst("
			SELECT title
			FROM packinglist
			WHERE productid='".$this->kclass->input['productid']."'
		");
		if($this->kclass->input['rt']=='view'){
			$rt = '&action=view&productid='.$this->kclass->input['productid'];
		}elseif($this->kclass->input['rt']=='list'){
			$rt = '#'.$this->kclass->input['productid'];
		}
		if($packinglist){
			$this->kclass->DB->query("
				UPDATE `packinglist`
				SET killed=0
				WHERE productid='".$this->kclass->input['productid']."'
			");
			$this->kclass->messager(array(
				'title' => '恢复装箱清单成功',
				'text' => '装箱清单 <b>'.$packinglist['title'].'</b> 已成功被恢复！',
				'url' => '/s.php?module=packinglist'.$rt,
				'sec' => 2
			));
		}else{
			$this->kclass->messager(array(
				'title' => '恢复装箱清单失败',
				'text' => '您要恢复的装箱清单不存在！',
				'url' => '/s.php?module=packinglist'.$rt,
				'sec' => 3
			));
		}
	}

	//
	function restore(){
		if($this->kclass->input['productid']<=0){
			$this->kclass->boinkIt('/s.php?module=packinglist');
		}
		$packinglist = $this->kclass->DB->queryFirst("
			SELECT title
			FROM packinglist
			WHERE productid='".$this->kclass->input['productid']."'
		");
$body = <<<EOF
<form action="/s.php?module=packinglist&action=revival" name="packinglist" method="post">
<input type="hidden" name="module" value="packinglist">
<input type="hidden" name="action" value="revival">
<input type="hidden" name="productid" value="{$this->kclass->input['productid']}">
<table><thead>
<thead>
<tr>
	<th>恢复装箱清单：{$packinglist['title']}</th>
</tr>
</thead>
<tbody>
<tr class="even">
	<td class="middle">你确定要恢复装箱清单: <a href="/s.php?module=packinglist&action=view&productid={$this->kclass->input['productid']}" class="big bold" target="_blank">{$packinglist['title']}</a> 吗?</td>
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
		$this->kclass->page['title'] .= ' - 恢复 - '.$packinglist['title'];
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 恢复装箱清单 - '.$packinglist['title'], 'right' => '<a href="/s.php?module=packinglist">返回列表</a>', 'body'=>$body));
	}
	function find(){
		$region = $this->kclass->chooserRegion(array('regionid'=>0, 'hasBlank'=>1));
$body = <<<EOF
<form action="/s.php?module=packlist&action=list" name="packlist" method="post">
<input type="hidden" name="module" value="packlist">
<input type="hidden" name="action" value="list">
<table>
<thead>
<tr>
	<th colspan="2">查找客户</th>
</tr>
</thead>
<tbody>
<tr class="even">
	<td>客户ID 是：</td>
	<td><input type="text" style="width:244px" name="id" value="{$packlist['packlistid']}"></td>
</tr>
<tr class="even">
	<td>客户名称 包含：</td>
	<td><input type="text" style="width:244px" name="title" value="{$packlist['title']}"> <span class="small gray">可使用客户名称中的关键字查找</span></td>
</tr>
<tr class="odd">
	<td>且 联系人名 包含：</td>
	<td><input type="text" style="width:244px" name="linkman" value="{$packlist['linkman']}"></td>
</tr>
<tr class="even">
	<td>且 邮箱 包含：</td>
	<td><input type="text" style="width:244px" name="email" value="{$packlist['email']}"></td>
</tr>
<tr class="odd">
	<td>且 电话 包含：</td>
	<td><input type="text" style="width:244px" name="phone" value="{$packlist['phone']}"> <span class="small gray">固定电话或手机</span></td>
</tr>
<tr class="odd">
	<td>且 地区 是：</td>
	<td>{$region}</td>
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
		<label for="o0"><input type="radio" name="orderby" value="title" id="o0" checked>名称</label>
		<label for="o1"><input type="radio" name="orderby" value="packlistid" id="o1">ID</label>
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
		$this->kclass->page['title'] .= ' - 查找客户';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 查找客户', 'right' => '<a href="/s.php?module=packlist">返回列表</a>', 'body'=>$body));
	}

	//
	function mmlist(){
		if($this->kclass->input['show']=='all'){
			$condition='';
		}else{
			$condition='`packinglist`.killed=0';
		}
		$cs = $this->kclass->DB->query("
			SELECT `packinglist`.packinglistid, `packinglist`.packinglistno, `packinglist`.attachs, `packinglist`.images, `packinglist`.modified, `packinglist`.created, `packinglist`.killed,
				m.username AS modifier, c.username AS creator
			FROM `packinglist`
			LEFT JOIN `user` AS m ON (m.userid=`packinglist`.modifier)
			LEFT JOIN `user` AS c ON (c.userid=`packinglist`.creator)
			WHERE  ".$condition."
			
		");
		if($sum=$this->kclass->DB->numRows()){
			$cody = '<table class="hundred"><thead><tr><th>ID</th><th>装箱清单编号</th><th align="center">建立/修改 日期</th><th width="48">操作</th></tr></thead><tbody>';
			while($c = $this->kclass->DB->fetchArray($cs)){
				$cody .= '<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even');
				$cody .= $this->kclass->iif($c['killed']==1, ' killed', '').'">
				<td><a name="'.$c['packinglistid'].'"></a>'.$c['packinglistid'].'</td>
				<td><a href="/s.php?module=packinglist&action=view&packinglistid='.$c['packinglistid'].'">'.$c['title'].'</a>'.$this->kclass->iif($c['attachs']>0, ' <span class="attachFile" title="有'.$c['attachs'].'个附件。"></span> ', '').$this->kclass->iif($c['images']>0, ' <span class="attachImage" title="有'.$c['images'].'个图片。"></span> ', '').$c['packinglistno'].'</td>
				<td class="tiny" title="建立者：'.$c['creator'].$this->kclass->iif($c['modifier']!='', '，修改者：'.$c['modifier'], '').'"><div class="gray">'.date('Y-m-d H:i:s', $c['created']).'</div>'.date('Y-m-d H:i:s', $c['modified']).'</td>';
				if($c['killed']==1){
					$cody .= '<td><a href="/s.php?module=packinglist&action=restore&packinglistid='.$c['packinglistid'].'&rt=list"><span class="restore" title="恢复"></span></a></td>';
				}else{
					$cody .= '<td align="right"><a href="/s.php?module=packinglist&action=kill&packinglistid='.$c['packinglistid'].'&rt=list" onclick="return confirm(\'你确定要删除这个装箱清单 '.$c['title'].' 吗？\');"><span class="remove" title="删除"></span></a> <a href="/s.php?module=packinglist&action=update&packinglistid='.$c['packinglistid'].'&rt=list"><span class="edit" title="修改"></span></a></td></tr>';
				}
			}
			$cody .= '</tbody></table>';
		}else{
			$cody = '暂无相关记录。';
		}

		$this->kclass->page['onload'] .= 'dc.menu(\'rightNav\');dc.tabhover();';
		$this->kclass->page['title'] .= ' - 列表';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 列表', 'right' => '显示： <a href="/s.php?module=packinglist">默认列表</a>   <a href="/s.php?module=packinglist&action=list&show=all">所有<span class="small gray">(包括删除)</span></a>    |    <a href="/s.php?module=packinglist&action=add">新建</a>', 'body'=>$cody));
	}

	/**
	 * 添加营销包装清单
	 * @author creator mayinghao 2013-07-26 10:33
	 * @access public
	 * @param none 没有参数
	 * @return none 没有返回值
	 * @throws none 没有异常
	 */
	function addPacking(){
		$url = REFERER;
		if($this->kclass->input['orderid']>0){
			$module = 'order';
			$mid = $this->kclass->input['orderid'];
			$data = $this->kclass->DB->queryFirst("SELECT `order`.* FROM `order` WHERE `orderid` = ".$this->kclass->input['orderid']);
			if($data['ifApprove']=0){
				$this->kclass->messager(array(
					'title' => '新建包装清单',
					'text' => '订单通过审核之后才能生成',
					'url' => REFERER,
					'sec' => 3
				));
			}
		}elseif($this->kclass->input['sampleid']>0){
			$module = 'sample';
			$mid = $this->kclass->input['sampleid'];
			$data = $this->kclass->DB->queryFirst("SELECT `sample`.* FROM `sample` WHERE `sampleid` = ".$this->kclass->input['sampleid']);
			if($data['ifApprove']=0){
				$this->kclass->messager(array(
					'title' => '新建包装清单',
					'text' => '样品单通过审核之后才能生成',
					'url' => REFERER,
					'sec' => 3
				));
			}
		}else{
			$this->kclass->messager(array(
				'title' => '新建包装清单',
				'text' => '订单ID传递不正确!',
				'url' => REFERER,
				'sec' => 3
			));
		}
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

		$orderList = '';
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
			WHERE `item`.killed=0 AND `item`.module='".$module."' AND `item`.mid='".$mid."' AND item.productitemid>0
			ORDER BY itemid ASC, modified ASC
		");
		if($this->kclass->DB->numRows()){
			$i=0;
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
				if($item['quantity'] >= $packingsize['cartonNum']){
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
					$subWeight .= 'kg';
					$loopNum = $loopNum+$cartonNum;
					if(1==$cartonNum){
						$cartonNumStr = '1carton';
					}else{
						$cartonNumStr = $cartonNum.'cartons';
					}
					if($modNum>0){
						$subWeight .= '<br/><span class="small darkred">剩余 '.$modNum.'pcs 请在剩余包装中说明</span>';
					}
				}else{
					$subWeight='<span class="small darkred">数量不足1箱,请在剩余包装中说明</span>';
				}
				$orderList .= '<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').'">
					<td valign="middle">'.$temp.'<br>'.$packingsize['length'].' x '.$packingsize['width'].' x '.$packingsize['height'].'/'.$packingsize['cartonNum'].'pcs<br>'.$cartonNumStr.'　'.$subWeight.'</td>
					<td valign="middle">'.$product['title'].'： '.$selectStr.'</td>
					<td valign="middle">'.$item['quantity'].' '.$item['unit'].'</td>
					<td valign="middle"><input type="hidden" name="itemId['.$i.']" value="'.$item['productitemid'].'"><textarea name="serialNo['.$i.']" style="width:200px;height:60px;"></textarea></td>
				</tr>';
				$i++;
			}

		$body = <<<EOF
<form action="/s.php?module=packinglist&action=insertPacking" name="invoice" method="post">
<input type="hidden" name="module" value="packinglist">
<input type="hidden" name="action" value="insertPacking">
<input type="hidden" name="orderid" value="{$this->kclass->input['orderid']}">
<input type="hidden" name="url" value="{$url}">
<input type="hidden" name="modules" value="{$module}">
<input type="hidden" name="mid" value="{$mid}">
<table style="width:900px;">
<thead>
<tr><th colspan="4">包装编号：<span class="darkred">{$PLStr}</span><input type="hidden" name="packingno" value="{$PLStr}"></th></tr>
</thead>
<tbody>
<tr class="bold">
<td width="200">箱号/规格</td><td>装箱内容</td><td width="160">数量</td><td width="200">序列号</td>
</tr>
{$orderList}
<tr>
	<td colspan="2">剩余包装：</td><td>剩余包装体积（M³）：</td><td>剩余包装重量（KG）：</td>
</tr>
<tr>
	<td colspan="2"><textarea name="packingNote" style="width:95%;height:130px;"></textarea></td>
	<td><textarea name="surplusVolume" style="width:95%;height:130px;"></textarea></td>
	<td><textarea name="surplusWeight" style="width:95%;height:130px;"></textarea></td>
</tr>
<tr class="even">
	<td align="center" colspan="6">
		<input type="submit" id="submitButton" value="      保存      " accesskey="s">
		<input type="reset" value="      复位      ">
	</td>
</tr>
</tbody>
</table>
</form>
EOF;
			$this->kclass->page['title'] .= ' - 新建包装清单';
			$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 新建包装清单', 'right' => '<a href="/s.php?module=invoice">返回列表</a>', 'body'=>$body));
		}
	}

	/**
	 * 向数据库插入包装清单
	 * @author creator mayinghao 2013-07-26 10:33
	 * @access public
	 * @param none 没有参数
	 * @return none 没有返回值
	 * @throws none 没有异常
	 */
	function insertPacking(){
		if($this->kclass->input['packingNote'] ==''){
			$e .= '<li>请填写剩余包装的信息。</li>';
		}
		if($this->kclass->input['surplusVolume'] ==''){
			$e .= '<li>请填写剩余包装的总体积。</li>';
		}
		if($this->kclass->input['surplusWeight'] ==''){
			$e .= '<li>请填写剩余包装的总重量。</li>';
		}
		if(isset($e)){
			$this->kclass->messager(array(
					'title' => '新建包装清单',
					'text' => '您在新建包装清单的过程中有以下错误：<ul>'.$e.'</ul>',
					'url' => 'javascript:history.back()',
					'sec' => 3
			));
		}

		$this->kclass->DB->query("
		INSERT INTO `packinglist` (packinglistno,module,mid,packingNote,surplusVolume,surplusWeight,created,creator)
		VALUES ('".$this->kclass->input['packingno']."','".$this->kclass->input['modules']."','".$this->kclass->input['mid']."','".$this->kclass->input['packingNote']."','".$this->kclass->input['surplusVolume']."','".$this->kclass->input['surplusWeight']."','".TIMENOW."','".$this->kclass->user['userid']."')
		");
		$packingid = $this->kclass->DB->insertID();
		$j = count($this->kclass->input['itemId']);
		for($i = 0; $i < $j; $i++){
			$this->kclass->DB->query("
				UPDATE `item` SET
					cartonNo = '".$this->kclass->input['serialNo'][$i]."',
					modified = '".TIMENOW."',
					modifier = '".$this->kclass->user['userid']."'
				WHERE `item`.module = '".$this->kclass->input['modules']."' AND `item`.mid='".$this->kclass->input['mid']."' AND `item`.productitemid='".$this->kclass->input['itemId'][$i]."'
			");
		}

		$this->kclass->messager(array(
			'title' => '新建包装清单',
			'text' => '包装清单<b>'.$this->kclass->input['packingno'].'</b> 已新建成功!',
			'url' => $this->kclass->input['url'],
			'sec' => 3
		));
	}

	/**
	 * 更新包装清单
	 * @author creator mayinghao 2013-07-26 10:33
	 * @access public
	 * @param none 没有参数
	 * @return none 没有返回值
	 * @throws none 没有异常
	 */
	function updatePacking(){
		$url = REFERER;
		if($this->kclass->input['orderid']>0){
			$module = 'order';
			$mid = $this->kclass->input['orderid'];
		}elseif($this->kclass->input['sampleid']>0){
			$module = 'sample';
			$mid = $this->kclass->input['sampleid'];
		}else{
			$this->kclass->messager(array(
				'title' => '新建包装清单',
				'text' => '订单ID传递不正确!',
				'url' => REFERER,
				'sec' => 3
			));
		}
			$packinglist = $this->kclass->DB->queryFirst("
				SELECT * FROM `packinglist` WHERE packinglistid = ".$this->kclass->input['packinglistid']."
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
			WHERE `item`.killed=0 AND `item`.module='".$module."' AND `item`.mid='".$mid."' AND item.productitemid>0
			ORDER BY itemid ASC, modified ASC
		");
			if($this->kclass->DB->numRows()){
				$i=0;
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
					if($item['quantity'] >= $packingsize['cartonNum']){
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
						$subWeight .= 'kg';
						$loopNum = $loopNum+$cartonNum;
						if(1==$cartonNum){
							$cartonNumStr = '1carton';
						}else{
							$cartonNumStr = $cartonNum.'cartons';
						}
						if($modNum>0){
							$subWeight .= '<br/><span class="small darkred">剩余 '.$modNum.'pcs 请在剩余包装中说明</span>';
						}
					}else{
						$subWeight='<span class="small darkred">数量不足1箱,请在剩余包装中说明</span>';
					}
					$orderList .= '<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').'">
						<td valign="middle">'.$temp.'<br>'.$packingsize['length'].' x '.$packingsize['width'].' x '.$packingsize['height'].'/'.$packingsize['cartonNum'].'pcs<br>'.$cartonNumStr.'　'.$subWeight.'</td>
						<td valign="middle">'.$product['title'].'： '.$selectStr.'</td>
						<td valign="middle">'.$item['quantity'].' '.$item['unit'].'</td>
						<td valign="middle"><input type="hidden" name="itemId['.$i.']" value="'.$item['productitemid'].'"><textarea name="serialNo['.$i.']" style="width:200px;height:60px;">'.$item['cartonNo'].'</textarea></td>
					</tr>';
					$i++;
				}

			$body = <<<EOF
<form action="/s.php?module=packinglist&action=insertPacking" name="invoice" method="post">
<input type="hidden" name="module" value="packinglist">
<input type="hidden" name="action" value="doupdatePacking">
<input type="hidden" name="orderid" value="{$this->kclass->input['orderid']}">
<input type="hidden" name="packinglistid" value="{$this->kclass->input['packinglistid']}">
<input type="hidden" name="url" value="{$url}">
<input type="hidden" name="modules" value="{$module}">
<input type="hidden" name="mid" value="{$mid}">
<table style="width:900px;">
<thead>
<tr><th colspan="4">包装编号：<span class="darkred">{$packinglist['packinglistno']}</span><input type="hidden" name="packingno" value="{$packinglist['packinglistno']}"></th></tr>
</thead>
<tbody>
<tr class="bold">
<td width="200">箱号/规格</td><td>装箱内容</td><td width="160">数量</td><td width="200">序列号</td>
</tr>
{$orderList}
<tr>
	<td colspan="2">剩余包装：</td><td>剩余包装体积（M³）：</td><td>剩余包装重量（KG）：</td>
</tr>
<tr>
	<td colspan="2"><textarea name="packingNote" style="width:95%;height:130px;">{$packinglist['packingNote']}</textarea></td>
	<td><textarea name="surplusVolume" style="width:95%;height:130px;">{$packinglist['surplusVolume']}</textarea></td>
	<td><textarea name="surplusWeight" style="width:95%;height:130px;">{$packinglist['surplusWeight']}</textarea></td>
</tr>
<tr class="even">
	<td align="center" colspan="6">
		<input type="submit" id="submitButton" value="      保存      " accesskey="s">
		<input type="reset" value="      复位      ">
	</td>
</tr>
</tbody>
</table>
</form>
EOF;
			$this->kclass->page['title'] .= ' - 修改包装清单';
			$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 修改包装清单', 'right' => '<a href="/s.php?module=invoice">返回列表</a>', 'body'=>$body));
		}
	}

	/**
	 * 向数据库更新包装清单
	 * @author creator mayinghao 2013-07-26 10:33
	 * @access public
	 * @param none 没有参数
	 * @return none 没有返回值
	 * @throws none 没有异常
	 */
	function doupdatePacking(){
		if($this->kclass->input['packingNote'] ==''){
			$e .= '<li>请填写剩余包装的信息。</li>';
		}
		if($this->kclass->input['surplusVolume'] ==''){
			$e .= '<li>请填写剩余包装的总体积。</li>';
		}
		if($this->kclass->input['surplusWeight'] ==''){
			$e .= '<li>请填写剩余包装的总重量。</li>';
		}

		if(isset($e)){
			$this->kclass->messager(array(
					'title' => '更新包装清单',
					'text' => '您在更新包装清单的过程中有以下错误：<ul>'.$e.'</ul>',
					'url' => 'javascript:history.back()',
					'sec' => 3
			));
		}

		$this->kclass->DB->query("
		UPDATE `packinglist` SET
				module = '".$this->kclass->input['modules']."',
				mid = '".$this->kclass->input['mid']."',
				packingNote = '".$this->kclass->input['packingNote']."',
				surplusVolume = '".$this->kclass->input['surplusVolume']."',
				surplusWeight = '".$this->kclass->input['surplusWeight']."',
				created = '".TIMENOW."',
				creator = '".$this->kclass->user['userid']."'
		WHERE packinglistid= ".$this->kclass->input['packinglistid']."
		");

		$j = count($this->kclass->input['itemId']);
		for($i = 0; $i < $j; $i++){
			if(in_array($this->kclass->input['itemId'][$i],$itemidArr[$i-1])){
				$this->kclass->DB->query("
					UPDATE `item` SET
						cartonNo = '".$this->kclass->input['serialNo'][$i]."',
						modified = '".TIMENOW."',
						modifier = '".$this->kclass->user['userid']."'
					WHERE `item`.module = '".$this->kclass->input['modules']."' AND `item`.mid='".$this->kclass->input['mid']."' AND `item`.productitemid='".$this->kclass->input['itemid'][$i]."'
				");
			}
		}

		$this->kclass->messager(array(
				'title' => '更新包装清单',
				'text' => '包装清单<b>'.$this->kclass->input['packingno'].'</b> 已更新成功!',
				'url' => $this->kclass->input['url'],
				'sec' => 3
		));
	}

	/**
	 * 向数据库插入产品属性
	 * @author creator mayinghao 2013-07-26 10:33
	 * @access public
	 * @param none 没有参数
	 * @return none 没有返回值
	 * @throws none 没有异常
	 */
	function removePacking(){

	}
	/**
	 * 显示产品规格表单
	 * @author creator zhourui 2014-5-23 13:55:44
	 * @access public
	 * @param none 没有参数
	 * @return none 没有返回值
	 * @throws none 没有异常
	 */
	function viewPackingSize(){
		if($this->kclass->input['wcitemid']<0){
			$e='<li>请点击正确的网址！</li>';
		}
		$wcitem=$this->kclass->DB->queryFirst("SELECT itemid,workcenterid,versionid,materialid,materialitemid FROM workcenteritem WHERE itemid={$this->kclass->input['wcitemid']}");
		if(!$wcitem){
			$e.='<li>数据错误，很抱歉！</li>';
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '修改物资包装规格',
				'text' => '您在修改物资包装规格的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$materialinfo=$this->kclass->getMaterial(array('materialid'=>$wcitem['materialid'],'itemid'=>$wcitem['materialitemid']));
		$packingSizes=$this->kclass->DB->query("
			SELECT packingsizeid,title,length,width,height,volume,perCarton,perNetWeight,perGrossWeight,netWeight,grossWeight
			FROM packingsize
			WHERE killed=0 AND wcitemid={$wcitem['itemid']} AND versionid={$wcitem['versionid']}
			ORDER BY created ASC
		");
		$i=1;
		if($this->kclass->DB->numRows()){
			while($packingSize=$this->kclass->DB->fetchArray($packingSizes)){
				$itemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').'">
					<td>'.$i.'</td>
					<td>'.$packingSize['title'].'</td>
					<td>'.$packingSize['length'].'　*　'.$packingSize['width'].'　*　'.$packingSize['height'].'</td>
					<td>'.$packingSize['perCarton'].'</td>
					<td>'.$packingSize['perNetWeight'].'</td>
					<td>'.$packingSize['perGrossWeight'].'</td>
					<td>'.$packingSize['netWeight'].'</td>
					<td>'.$packingSize['grossWeight'].'</td>
					<td>'.$packingSize['remark'].'</td>
				</tr>';
				$i++;
			}
		}else{
			$this->kclass->boinkIt("/s.php?module=packinglist&action=updatePackingSize&wcitemid=".$this->kclass->input['wcitemid']);
		}
		$body=<<<EOF
<div class="title">{$materialinfo['no']}　{$materialinfo['material']}　{$materialinfo['standard']}</div>
<table class="hundred">
<thead></tr><th colspan="9">物资包装规格</th></tr></thead>
<tbody>
<tr class="center even" nohover><td width="15">ID</td><td>包装标题</td><td>箱规格(cm) (长 x 宽 x 高)</td><td>件/箱</td><td>每个净重(kg)</td><td>每个毛重(kg)</td><td>每箱净重(kg)</td><td>每箱毛重(kg)</td><td>备注</td></tr>
{$itemtr}
</tbody>
</table>
EOF;
		$this->kclass->page['title'].='新建工作中心供应的物资';
		$this->kclass->page['onload'].='dc.tabhover();';
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - <a href="/s.php?module=workcenter&action=view&workcenterid='.$wcitem['workcenterid'].'">查看工作中心</a> - 修改物资的包装规格', 'right'=>'<a href="/s.php?module=workcenter&action=view&workcenterid='.$wcitem['workcenterid'].'">返回查看工作中心</a>','body'=>$body));
	}
	/**
	 * 修改产品规格表单
	 * @author creator zhourui 2014-5-23 13:55:44
	 * @access public
	 * @param none 没有参数
	 * @return none 没有返回值
	 * @throws none 没有异常
	 */
	function updatePackingSize(){
		if($this->kclass->input['wcitemid']<0){
			$e='<li>请点击正确的网址！</li>';
		}
		$wcitem=$this->kclass->DB->queryFirst("SELECT itemid,workcenterid,versionid,materialid,materialitemid FROM workcenteritem WHERE itemid={$this->kclass->input['wcitemid']}");
		if(!$wcitem){
			$e.='<li>数据错误，很抱歉！</li>';
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '修改物资包装规格',
				'text' => '您在修改物资包装规格的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$materialinfo=$this->kclass->getMaterial(array('materialid'=>$wcitem['materialid'],'itemid'=>$wcitem['materialitemid']));
		
		$packingSizes=$this->kclass->DB->query("
			SELECT packingsizeid,title,length,width,height,volume,perCarton,perNetWeight,perGrossWeight,netWeight,grossWeight
			FROM packingsize
			WHERE killed=0 AND wcitemid={$wcitem['itemid']} AND versionid={$wcitem['versionid']}
			ORDER BY created ASC
		");
		$i=1;
		if($this->kclass->DB->numRows()){
			while($packingSize=$this->kclass->DB->fetchArray($packingSizes)){
				$itemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').'">
					<td><input type="hidden" name="itemId['.$i.']" value="'.$i.'" >
						<input type="hidden" name="itemPackingsizeid['.$i.']" value="'.$packingSize['packingsizeid'].'" >'.$i.'</td>
					<td><input type="text" name="title['.$i.']" value="'.$packingSize['title'].'" size="30"></td>
					<td>长：<input type="text" name="length['.$i.']" value="'.$packingSize['length'].'" size="5">　宽：<input type="text" name="width['.$i.']" value="'.$packingSize['width'].'" size="5">　高：<input type="text" name="heigth['.$i.']" value="'.$packingSize['height'].'" size="5"></td>
					<td><input type="text" name="perCarton['.$i.']" value="'.$packingSize['perCarton'].'" size="5"></td>
					<td><input type="text" name="perNetWeight['.$i.']" value="'.$packingSize['perNetWeight'].'" size="5"></td>
					<td><input type="text" name="perGrossWeight['.$i.']" value="'.$packingSize['perGrossWeight'].'" size="5"></td>
					<td><input type="text" name="netWeight['.$i.']" value="'.$packingSize['netWeight'].'" size="5"></td>
					<td><input type="text" name="grossWeight['.$i.']" value="'.$packingSize['grossWeight'].'" size="5"></td>
					<td><input type="text" name="remark['.$i.']" value="'.$packingSize['remark'].'" size="40"></td>
					<td><input type="checkbox" name="itemKill['.$i.']" value="'.$packingSize['packingsizeid'].'"></td>
				</tr>';
				$i++;
			}
			$itemtr.='<tr><td colspan=10>新增包装规格</td></tr>';
		}
		for($k=$i;$k<$i+5;$k++){
			$itemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').'">
				<td><input type="hidden" name="itemId['.$k.']" value="'.$k.'" >'.$k.'</td>
				<td><input type="text" name="title['.$k.']" value="'.$packingSize['title'].'" size="30"></td>
				<td>长：<input type="text" name="length['.$k.']" value="'.$packingSize['length'].'" size="5">　宽：<input type="text" name="width['.$k.']" value="'.$packingSize['width'].'" size="5">　高：<input type="text" name="heigth['.$k.']" value="'.$packingSize['height'].'" size="5"></td>
				<td><input type="text" name="perCarton['.$k.']" value="'.$packingSize['perCarton'].'" size="5"></td>
				<td><input type="text" name="perNetWeight['.$k.']" value="'.$packingSize['perNetWeight'].'" size="5"></td>
				<td><input type="text" name="perGrossWeight['.$k.']" value="'.$packingSize['perGrossWeight'].'" size="5"></td>
				<td><input type="text" name="netWeight['.$k.']" value="'.$packingSize['netWeight'].'" size="5"></td>
				<td><input type="text" name="grossWeight['.$k.']" value="'.$packingSize['grossWeight'].'" size="5"></td>
				<td><input type="text" name="remark['.$k.']" value="'.$packingSize['remark'].'" size="40"></td>
				<td></td>
			</tr>';
		}
		$body=<<<EOF
<div class="title">{$materialinfo['no']}　{$materialinfo['material']}　{$materialinfo['standard']}</div>
<form action="/s.php?module=packinglist&action=doupdatePackingSize" name="packinglist" method="post">
<input type="hidden" name="module" value="packinglist" />
<input type="hidden" name="action" value="doupdatePackingSize" />
<input type="hidden" name="wcitemid" value="{$wcitem['itemid']}" />
<input type="hidden" name="versionid" value="{$wcitem['versionid']}" />
<input type="hidden" name="materialid" value="{$wcitem['materialid']}" />
<input type="hidden" name="materialitemid" value="{$wcitem['materialitemid']}" />
<table class="hundred">
<thead></tr><th colspan="10">物资包装规格</th></tr></thead>
<tbody>
<tr class="center even" nohover><td width="15">ID</td><td>包装标题</td><td>箱规格(cm) <span class="red bold">*</span></td><td>件/箱 <span class="red bold">*</span></td><td>每个净重(kg) <span class="red bold">*</span></td><td>每个毛重(kg) <span class="red bold">*</span></td><td>每箱净重(kg) <span class="red bold">*</span></td><td>每箱毛重(kg) <span class="red bold">*</span></td><td>备注</td><td width="15">删</td></tr>
{$itemtr}
<tr class="even">
	<td class="small gray" colspan="10">
		注意事项：
		带 * 项请填写完整</td>
</tr>
</tbody>
</table>
<table class="tlist tsuite" align="center" width="100%">
<tbody>
<tr class="odd" nohover>
<td class="center"><input type="submit" value="  提交  " accesskey="s""><input type="reset" value="  复位  "/></td>
</tr>
</tbody>
</table>
</form>
EOF;
		$this->kclass->page['title'].='新建工作中心供应的物资';
		$this->kclass->page['onload'].='dc.tabhover();';
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - <a href="/s.php?module=workcenter&action=view&workcenterid='.$wcitem['workcenterid'].'">查看工作中心</a> - 修改物资的包装规格', 'right'=>'<a href="/s.php?module=workcenter&action=view&workcenterid='.$wcitem['workcenterid'].'">返回查看工作中心</a>','body'=>$body));
	}
	/**
	 * 新增产品规格表单
	 * @author creator zhourui 2014-5-23 13:55:44
	 * @access public
	 * @param none 没有参数
	 * @return none 没有返回值
	 * @throws none 没有异常
	 */
	function doupdatePackingSize(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e='<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			$hasItem=0;
			$count=count($this->kclass->input['itemId']);
			for($i=1;$i<=$count;$i++){
				if($this->kclass->input['itemKill'][$i]=='' AND $this->kclass->input['length'][$i]>0 AND $this->kclass->input['width'][$i]>0 AND $this->kclass->input['heigth'][$i]>0 AND $this->kclass->input['perCarton'][$i]>0 AND $this->kclass->input['perNetWeight'][$i]>0 AND $this->kclass->input['perGrossWeight'][$i]>0 AND $this->kclass->input['netWeight'][$i]>0 AND $this->kclass->input['grossWeight'][$i]>0){
					$hasItem=1;
				}
			}
			if($hasItem==0){
				$e='<li>需要修改的物资 请把数据填写完整</li>';
			}
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '新建工作中心供应的物资',
				'text' => '您在新建工作中心供应的物资的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		for($i=1;$i<=$count;$i++){
			if($this->kclass->input['length'][$i]>0 AND $this->kclass->input['width'][$i]>0 AND $this->kclass->input['heigth'][$i]>0 AND $this->kclass->input['perCarton'][$i]>0 AND $this->kclass->input['perNetWeight'][$i]>0 AND $this->kclass->input['perGrossWeight'][$i]>0 AND $this->kclass->input['netWeight'][$i]>0 AND $this->kclass->input['grossWeight'][$i]>0){
				$volume=round($this->kclass->input['length'][$i]*$this->kclass->input['width'][$i]*$this->kclass->input['heigth'][$i],2);
				if($this->kclass->input['itemPackingsizeid'][$i]>0){
					if($this->kclass->input['itemKill'][$i]>0 AND $this->kclass->input['itemKill'][$i]==$this->kclass->input['itemPackingsizeid'][$i]){
						$this->kclass->DB->query("
							UPDATE packingsize SET 
								killer='{$this->kclass->user['userid']}',
								killed='".TIMENOW."'
							WHERE packingsizeid='{$this->kclass->input['itemPackingsizeid'][$i]}'
						");
					}else{
						$this->kclass->DB->query("
							UPDATE packingsize SET 
								title='{$this->kclass->input['title'][$i]}',
								length='{$this->kclass->input['length'][$i]}',
								width='{$this->kclass->input['width'][$i]}',
								height='{$this->kclass->input['heigth'][$i]}',
								volume='{$volume}',
								perCarton='{$this->kclass->input['perCarton'][$i]}',
								perNetWeight='{$this->kclass->input['perNetWeight'][$i]}',
								perGrossWeight='{$this->kclass->input['perGrossWeight'][$i]}',
								netWeight='{$this->kclass->input['netWeight'][$i]}',
								grossWeight='{$this->kclass->input['grossWeight'][$i]}',
								remark='{$this->kclass->input['remark'][$i]}',
								modifier='{$this->kclass->user['userid']}',
								modified='".TIMENOW."'
							WHERE packingsizeid='{$this->kclass->input['itemPackingsizeid'][$i]}'
						");
					}
				}else{
					$this->kclass->DB->query("
						INSERT INTO packingsize (materialid,wcitemid,materialitemid,versionid,title,length,width,height,volume,perCarton,perNetWeight,perGrossWeight,netWeight,grossWeight,remark,created,creator)
						VALUES ('{$this->kclass->input['materialid']}','{$this->kclass->input['wcitemid']}','{$this->kclass->input['materialitemid']}','{$this->kclass->input['versionid']}','{$this->kclass->input['title'][$i]}','{$this->kclass->input['length'][$i]}','{$this->kclass->input['width'][$i]}','{$this->kclass->input['heigth'][$i]}','{$volume}','{$this->kclass->input['perCarton'][$i]}','{$this->kclass->input['perNetWeight'][$i]}','{$this->kclass->input['perGrossWeight'][$i]}','{$this->kclass->input['netWeight'][$i]}','{$this->kclass->input['grossWeight'][$i]}','{$this->kclass->input['remark'][$i]}','".TIMENOW."','{$this->kclass->user['userid']}')
					");
				}
			}
		}
		$this->kclass->messager(array(
			'title' => '修改物资包装清单',
			'text' => '物资包装清单  已修改成功!',
			'url' => '/s.php?module=packinglist&action=viewPackingSize&wcitemid='.$this->kclass->input['wcitemid'],
			'sec' => 2
		));
	}
	
/***************************************** private function *******************************************************/	
		//
	function _chooserOrderItem($b){
		if($b['orderby'] == ''){
			$b['orderby']='ordering';
		}
		if($b['direction'] == ''){
			$b['direction']='ASC';
		}
		if($b['width'] > 0){
			$b['width']=' style="width: '.$b['width'].'px"';
		}
		if($b['onchange']){
			$onchange='onchange="'.$b['onchange'].'"';
		}
		
		$orderitems=$this->kclass->DB->query("
			SELECT ri.itemid,ri.materialid,ri.materialitemid,ri.versionid,ri.quantity,ri.dateline,
				r.requirementid,r.requirementno,
				mv.title AS version,mv.qualified,mv.disqualified
			FROM `requirementitem` AS ri 
			LEFT JOIN `requirement` AS r ON (r.requirementid=ri.requirementid)
			LEFT JOIN `materialversion` AS mv ON (mv.versionid=ri.versionid)
			WHERE ri.killed=0 AND r.killed=0 AND r.locked<>0 AND ri.module='{$b['m']}' AND ri.mid='{$b['mid']}'
			ORDER BY ri.itemid ASC
		");
		$r='<select id="'.$this->kclass->iif($b['id'],$b['id'],$b['name']).'" name="'.$b['name'].'"'.$b['width'].' '.$onchange.' >';
		if($this->kclass->DB->numRows()){
			if($b['hasBlank']){
				$r.='<option value="0">'.$b['topname'].'</option>';
			}
			while($orderitem=$this->kclass->DB->fetchArray($orderitems)){
				$materialInfo=$this->kclass->getMaterial(array('materialid'=>$orderitem['materialid'],'itemid'=>$orderitem['materialitemid']));
				$r.='<option value="'.$orderitem['versionid'].'" ';
				if($b['selectedid']==$orderitem['versionid']){
					$r.='selected';
				}
				$r.='>'.$materialInfo['no'].'　'.$materialInfo['material'].'　'.$materialInfo['standard'].'　'.$orderitem['version'].'</option>';
			}
		}
		$r .= '</select>';

		return $r;
	}
}
?>