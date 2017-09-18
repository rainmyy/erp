<?php
//
class Barcode{
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
			case 'addPerBarcode':
				return $this->addPerBarcode();
			break;
			case 'addServiceNo': // 新增快速服务代码
				return $this->addServiceNo();
			break;
			case 'viewAttribute':
				return $this->viewAttribute();
			break;
			case 'addAttribute':
				return $this->addAttribute();
			break;
			case 'insertAttribute':
				return $this->insertAttribute();
			break;
			case 'doscrap':
				return $this->doscrap();
			break;
			case 'scrap':
				return $this->scrap();
			break;
			case 'revival':
				return $this->revival();
			break;
			case 'restore':
				return $this->restore();
			break;
			case 'viewChildren':
				return $this->viewChildren();
			break;
			case 'exportSerialno':
				return $this->exportSerialno();
			break;
			case 'importSerialno':
				return $this->importSerialno();
			break;
			case 'doimportSerialno':
				return $this->doimportSerialno();
			break;
			case 'listSerialno':
				return $this->listSerialno();
			break;
			case 'exchangeSerialno':
				return $this->exchangeSerialno();
			break;
			default:
				return $this->mmlist();
		}
	}
	// add barcode
	function _addBarcode($b){
		$arr=array();
		$start=1;
		$sd=explode('-',$b['date']);
		foreach($sd as $key => $val){
			$time36.=strtoupper(base_convert($val,10,36));
		}
		$b['date']=$time36;//将日期转化为3位数格式
		//验证该物资条码是否存在流水号编号
		$barcode=$this->kclass->DB->queryFirst("
			SELECT barcodeid,MAX(number) AS start
			FROM barcode
			WHERE parentid='".$b['barcodeid']."' AND country='".$b['country']."' AND materialno='".$b['materialno']."' AND arrivalTime='".$b['date']."' AND version='".$b['version']."'
			ORDER BY barcodeid ASC
			LIMIT 0,1
		");
		if($barcode AND $barcode['start']!=0){
			$start=$barcode['start']+1;
			$b['count']=$b['count']+$index;
		}
		if($b['ifPerBarcode']==1){
			for($i=$start;$i<=$b['count'];$i++){
				if($i<10000){
					$b['index']=str_pad($i,4,'0',STR_PAD_LEFT);
				}else{
					$a=$i-9999;
					$s='';
					while($a>0){
						$b=$a%26;
						if($b==0)$b=26;
						$s=chr($b+64).$s;
						$a=($a-$b)/26;
					}
					$r=str_pad($s,4,'A',STR_PAD_LEFT);
				}
				$arr[]=$b['country'].'-'.$b['materialno'].'-'.$b['supplierno'].'-'.$b['date'].'-'.$b['index'].'-'.$b['version'];
			}
			return $arr;
		}
		else{
			$r=$b['country'].'-'.$b['materialno'].'-'.$b['supplierno'].'-'.$b['date'].'-'.$b['version'];
			return $r;
		}
	}

	//
	function mmlist(){
		if($this->kclass->input['show']=='all'){
			$condition='1=1';
		}else{
			$condition='barcode.scraped=0';
		}
		$barcodes=$this->kclass->DB->query("
			SELECT barcode.*,
				material.standard,material.title AS material,material.ifPerBarcode,
				supplier.supplierid,supplier.title AS supplier,
				u.username AS creator,
				us.username AS modifier
			FROM barcode
			LEFT JOIN material ON (material.materialid=barcode.materialid)
			LEFT JOIN supplier ON (supplier.supplierid=barcode.supplierid)
			LEFT JOIN `user` AS u ON (u.userid=barcode.creator)
			LEFT JOIN `user` AS us ON (us.userid=barcode.modifier)
			WHERE barcode.parentid=0 AND ".$condition."
		");
		if($this->kclass->DB->numRows()){
			$body='<ul class="mmlist clear" id="mmlist">';
			$i=1;
			while($barcode=$this->kclass->DB->fetchArray($barcodes)){
				$ifPerBarcode=$this->kclass->iif($barcode['ifPerBarcode']==1,'存在','不存在');
				$barcode['created']=date('Y-m-d',$barcode['created']);
				$body.='<li title="由 '.$barcode['creator'].' 建于 '.$created.$this->kclass->iif($barcode['modifier']!='', '，'.$barcode['modifier'].' 改于 '.date('Y-m-d',$barcode['modified']), '').'"'.$this->kclass->iif($i%4==0, ' class="end"', '').'>
					<div class="mmlistt">
						<span class="right normal">';
				if($barcode['scraped']>0){
					$body .= '<a href="/s.php?module=barcode&action=revival&barcodeid='.$barcode['barcodeid'].'&rt=list" onclick="return confirm(\'你确定要恢复这个物资 '.$barcode['barcode'].' 吗？\');">恢复</a>';
				}else{
					$body .= '<a href="/s.php?module=barcode&action=doscrap&barcodeid='.$barcode['barcodeid'].'&rt=list" onclick="return confirm(\'你确定要删除这个物资 '.$barcode['barcode'].' 吗？\');">报废</a>';
				}
				$body.='</span>
					<a href="/s.php?module=barcode&action=view&barcodeid='.$barcode['barcodeid'].'">'.$barcode['barcode'].'</a><br><span class="small gray right">'. $barcode['created'].'</span></div>
				<div class=mmlistb>
					<div title="'.$barcode['materialno'].'　'.$barcode['material'].'　'.$barcode['standard'].'"><span class="small gray">物资：<a href="/s.php?module=material&action=view&materialid='.$barcode['materialid'].'">'.$barcode['materialno'].'</a></span></div>
					<div title="'.$barcode['supplier'].'"><span class="small gray">供应商：<a href="/s.php?module=psupplier&action=view&supplierid='.$barcode['supplierid'].'">'.$barcode['supplier'].'</a></span></div>
					<div title="'.$ifPerBarcode.'"><span class="small gray">是否存在流水线条码：'.$ifPerBarcode.'</span></div>
					<div class="small clear"><span class=right title="由 '.$barcode['creator'].' 建于 '.$barcode['created'].'">由 '.$barcode['creator'].' 建于 '.$barcode['created'].'</span></div></div></li>';
				$i++;
			}
			$body.='</ul>';
		}else{
			$body='暂无相关记录';
		}

		$this->kclass->page['title'].='条码列表';
		$this->kclass->page['onload'].='dc.listhover();';
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - 条码列表','right' => '<span class="small">显示方式：</span><a href="/s.php?module=barcode">默认列表</a>　<a href="/s.php?module=barcode&action=list&show=all">所有<span class="small gray">(包括报废)</span></a>　|　<a href="/s.php?module=barcode&action=add">新建</a>','body'=>$body));
	}
	//
	function view(){
		$barcode=$this->kclass->DB->queryFirst("
			SELECT b.*,
				m.title,m.standard,m.title AS material,m.ifPerBarcode,
				s.supplierid,s.supplierno,s.title AS supplier,
				u.username AS creator 
			FROM barcode AS b
			LEFT JOIN material AS m ON (m.materialid=b.materialid)
			LEFT JOIN supplier AS s ON (s.supplierid=b.supplierid)
			LEFT JOIN `user` AS u ON (u.userid=b.creator)
			WHERE b.scraped=0 AND b.barcodeid='".$this->kclass->input['barcodeid']."'
			LIMIT 0,1
		");
		if($barcode){
			$this->kclass->input['min']=$this->kclass->iif($this->kclass->input['min'],$this->kclass->input['min'],0);
			$barcode['created']=date('Y-m-d',$barcode['created']);
			$barcode['arrivalTime']=date('Y-m-d',$barcode['arrivalTime']);
			$perBarcodes=$this->kclass->DB->query("
				SELECT barcodeid,number,barcode
				FROM barcode 
				WHERE parentid='".$barcode['barcodeid']."' 
				LIMIT ".$this->kclass->input['min'].",500 
			");
			if($this->kclass->DB->numRows()){
				$i=0;
				$count=$this->kclass->DB->queryFirst("SELECT COUNT(*) AS counter FROM barcode WHERE parentid='".$barcode['barcodeid']."'");
				$number=$count['counter']/500;
				for($i=0;$i<$number;$i++){
					$link.='<a href="/s.php?module=barcode&action=view&barcodeid='.$this->kclass->input['barcodeid'].'&min='.($i*500).'">'.($i*500+1).'~'.(($i+1)*500).'</a>　';
				}
				$perBarcodeList['panel']='<div class>'.$link.'</div><table class="hundred"><thead><tr><th colspan="4">该批次的流水号条码列表</th></tr></thead><tbody><tr class="even center bold"><td>流水号</td><td>条码</td><td></td></tr>';
				while($perBarcode=$this->kclass->DB->fetchArray($perBarcodes)){
					$i+=1;
					$attribute=$this->kclass->DB->queryFirst("
						SELECT COUNT(valueid) AS count
						FROM attributevalue AS v 
						LEFT JOIN attributename AS n ON (v.nameid=n.nameid) 
						WHERE n.killed=0 AND n.module='barcode' AND n.mid='".$perBarcode['barcodeid']."'
					");
					$addAttribute=$this->kclass->iif($attribute['count']>0,'<a href="/s.php?module=barcode&action=viewAttribute&barcodeid='.$perBarcode['barcodeid'].'">查看属性值</a>','<a href="/s.php?module=attribute&action=add&barcodeid='.$perBarcode['barcodeid'].'" target="_blank">添加属性</a>');
					$perBarcodeList['panel'].='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd center','even center').'"><td>'.$perBarcode['number'].'</td>
					<td>'.$perBarcode['barcode'].'</td><td>'.$addAttribute.'</td></tr>';
				}
				$perBarcodeList['panel'].='</tbody></table>';
				$perBarcodeList['count']=' ('.$count['counter'].')';
			}else{
				$perBarcodeList['off']=' disabled';
			}
			$addPerBarcode=$this->kclass->iif($barcode['ifPerBarcode']==0,'',$this->kclass->iif($barcode['quantity']==$count['counter'],'','<span class="gray right"><a href="/s.php?module=barcode&action=addPerBarcode&barcodeid='.$barcode['barcodeid'].'">生成流水号条码</a></span>'));
			$viewOrder=$this->kclass->iif($barcode['porderid']==0,'','<a href="/s.php?module=porder&action=view&porderid='.$barcode['porderid'].'">返回查看订单</a>');
$body=<<<EOF
<div class="title"><span class="right gray small">由{$barcode['creator']}建于{$barcode['created']}</span>{$barcode['barcode']}</div>
<dl id="barcode" class="tabs" style="display: block;">
	<dt tabid="0">批次条码信息</dt>
	<dt title="该批次下的流水号条码" {$perBarcodeList['off']}>流水号条码{$perBarcodeList['count']}</dt>
<dd>
<table class="hundred">
<thead><tr><th colspan="4">批次条码信息{$addPerBarcode}</th></tr></thead>
<tbody>
<tr class="odd">
<td width="100">物资编号：</td><td><a href="/s.php?module=material&action=view&materialid={$barcode['materialid']}">{$barcode['materialno']}</a></td>
<td width="120">物资名称：</td><td>{$barcode['material']}</td>
</tr>
<tr class="even">
<td>物资规格：</td><td>{$barcode['standard']}</td>
<td>该批次数量：</td><td>{$barcode['quantity']}</td>
</tr>
<tr class="odd">
<td>供应商编号：</td><td><a href="/s.php?module=psupplier&action=view&supplierid={$barcode['supplierid']}">{$barcode['supplierno']}</a></td>
<td>供应商名称：</td><td><a href="/s.php?module=psupplier&action=view&supplierid={$barcode['supplierid']}">{$barcode['supplier']}</a></td>
</tr>
<tr class="even">
<td>物资版本：</td><td>{$barcode['version']}</td>
<td></td><td></td>
</tr>
<tr class="odd">
<td>到货时间：</td><td>{$barcode['arrivalTime']}</td>
<td></td><td></td>
</tr>
</tbody>
</table>
</dd>
<dd>{$perBarcodeList['panel']}</dd>
EOF;
		}else{
				$body = '系统数据出错，很抱歉！';
		}
		$this->kclass->page['title'].='查看条码';
		$this->kclass->page['onload'].='dc.tabhover();dc.tabs({\'id\':\'barcode\'})';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - <a href="/s.php?module=barcode&action=list">批次条码列表</a> - 详细信息','right'=>$viewOrder,'body' => $body));
	}
	//
	function add(){
		if($this->kclass->input['porderid']>0 AND $this->kclass->input['trackingid']>0){
			$orderTracking=$this->kclass->DB->queryFirst("SELECT itemid,quantity,dateline FROM ordertracking WHERE trackingid='".$this->kclass->input['trackingid']."'");
			$default=$this->kclass->DB->queryFirst("
				SELECT i.materialid,i.quantity,i.orderid,
					v.title AS version,
					m.ifPerBarcode,m.materialno,m.title,m.standard,
					o.porderid,o.supplierid,o.shipmentDate AS arrivalTime
				FROM item AS i
				LEFT JOIN materialVersion AS v ON (v.materialid=i.materialid)
				LEFT JOIN material AS m ON (m.materialid=i.materialid)
				LEFT JOIN `porder` AS o ON (o.porderid=i.mid)
				WHERE i.killed=0 AND i.itemid='".$orderTracking['itemid']."'
				LIMIT 0,1
			");
			$default['dateline']=date('Y-m-d',$orderTracking['dateline']);
			if($default['ifPerbarcode']==1){
				$perBarcode='<span class="small gray">（将会对该类物资的每一项生成流水号条码）</span>';
			}
			$default['version']=$this->kclass->iif($default['version']=='','A01',$default['version']);
			$supplier=$this->kclass->DB->queryFirst("SELECT supplierid,supplierno,regionid FROM supplier WHERE killed=0 AND supplierid='".$default['supplierid']."' LIMIT 0,1");
			$region=$this->kclass->DB->queryFirst("SELECT re.abbr FROM `region` AS r LEFT JOIN `region` AS re ON (r.countryid=re.regionid) WHERE re.regionid='".$supplier['regionid']."'");
			$default['abbr']=$this->kclass->iif($region['abbr'],$region['abbr'],'CN');
			$default['supplierno']=$supplier['supplierno'];
			$default['quantity']=$orderTracking['quantity'];
		}elseif($this->kclass->input['itemid']>0){
			$default=$this->kclass->DB->queryFirst("
				SELECT ii.version,ii.materialid,ii.qualified,ii.disqualified,ii.version,ii.dateline,
					m.materialno,m.title,m.standard,m.ifPerBarcode,
					u.title AS unit
				FROM preinbounditem AS ii 
				LEFT JOIN material AS m ON (ii.materialid=m.materialid) 
				LEFT JOIN unit AS u ON (u.unitid=ii.unitid)
				WHERE ii.killed=0 AND ii.itemid='".$this->kclass->input['itemid']."' 
				LIMIT 0,1
			");
			$default['supplierid']=1;
			$default['supplierno']='0000';
			$default['abbr']='CN';
			$default['quantity']=$this->kclass->iif($default['qualified']==0,$default['disqualified'],$default['qualified']);
			$default['dateline']=date('Y-m-d',$default['dateline']);
		}else{
			$this->kclass->boinkIt('/s.php?module=barcode');
		}
		$body=<<<EOF
<form action="/s.php?module=barcode&action=insert" name="barcode" method="post">
<input type="hidden" name="module" value="barcode" />
<input type="hidden" name="action" value="insert" />
<input type="hidden" name="materialid" value="{$default['materialid']}" />
<input type="hidden" name="supplierid" value="{$default['supplierid']}" />
<input type="hidden" name="porderid" value="{$default['porderid']}" />
<input type="hidden" name="ifPerBarcode" value="{$default['ifPerBarcode']}" />
<table class="hundred">
<thead></tr><th colspan="4">物资信息</th></tr></thead>
<tbody>
<tr class="odd">
<td width="120">物资编号：<span class="red bold">*</span></td><td width="475"><input type="text" name="materialno" value="{$default['materialno']}" readonly/>{$perBarcode}</td>
<td width="100">物资名称：<span class="red bold">*</span></td><td><input type="text" name="title" value="{$default['title']}" style="width:350px;" readonly/></td>
</tr>
<tr class="odd">
<td>物资规格：<span class="red bold">*</span></td><td><input type="text" name="standard" value="{$default['standard']}" style="width:350px;" readonly/></td>
<td>物资版本号：<span class="red bold">*</span></td><td><input type="text" name="version" value="{$default['version']}" /><span class="small gray">（没有版本则默认为A01）</span></td>
</tr>
<tr class="even">
<td>供应商编号：<span class="red bold">*</span></td><td><input type="text" name="supplierno" value="{$default['supplierno']}" readonly/></td>
<td>供应商国家：<span class="red bold">*</span></td><td><input type="text" name="country" value="{$default['abbr']}" readonly /></td>
</tr>
<tr class="odd">
<td>批号时间：<span class="red bold">*</span></td><td><input type="text" name="arrivalTime" id="date" value="{$default['dateline']}" readonly/></td>
<td>数　　量：<span class="red bold">*</span></td><td><input type="text" name="quantity" value="{$default['quantity']}" readonly/></td>
</tr>
<tr class="odd">
<td>详　　情：</td><td><textarea name="detail" style="width:444px;height:111px;" /></textarea>
<td>备　　注：</td><td><textarea name="remark" style="width:444px;height:111px;" /></textarea>
</tr>
<tr class="even">
<td class="small gray" colspan="4">注意事项：确认信息正确无误</td>
</tr>
</tbody>
</table>
<table class="tlist tsuite" align="center" width="100%">
<tbody>
<tr class="odd" nohover>
<td class="center" colspan=4><input type="submit" id="submitButton" value="  生成条码  " accesskey="s"><input type="reset" value="  复位  "/></td>
</tr>
</tbody>
</table>
</form>
EOF;
		$this->kclass->tbline+=4;
		$this->kclass->page['title'].='生成条码';
		$this->kclass->page['onload'].='dc.tabhover();$(\'#date\').datepicker()';
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - <a href="/s.php?module=porder&action=view&porderid='.$material['porderid'].'">查看订单</a> - 生成条码', 'right'=>'<a href="/s.php?module=porder&action=list">返回列表</a>','body'=>$body));
	}
	//
	function insert(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			$barcode=$this->kclass->DB->queryFirst("
				SELECT barcodeid
				FROM barcode
				WHERE scraped=0 AND materialid='".$this->kclass->input['materialid']."' AND arrivalTime='".$this->kclass->input['arrivalTime']."' AND supplierid='".$this->kclass->input['supplierid']."'
			");
			if($barcode['barcodeid']){
				$e.='<li>该物资已有条码</li>';
			}
		}
		if($e){
			$this->kclass->messager(array(
				'title' => '生成条码',
				'text' => '您在生成条码的过程中有以下错误：<ul>'.$e.'</ul>',
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
		$date=date('y-n-j',$arrivalTime);
		$this->kclass->input['porderid']=$this->kclass->iif($this->kclass->input['porderid']>0,$this->kclass->input['porderid'],0);
		$barcode=$this->_addBarcode(array('country'=>$this->kclass->input['country'],'materialno'=>$this->kclass->input['materialno'],'supplierno'=>$this->kclass->input['supplierno'],'date'=>$date,'count'=>$this->kclass->input['quantity'],'version'=>$this->kclass->input['version']));

		$this->kclass->DB->query("
			INSERT INTO barcode (materialid,supplierid,porderid,barcode,country,materialno,supplierno,arrivalTime,version,quantity,,created,creator)
			VALUES ('".$this->kclass->input['materialid']."','".$this->kclass->input['supplierid']."','".$this->kclass->input['porderid']."','".$barcode."','".$this->kclass->input['country']."','".$this->kclass->input['materialno']."','".$this->kclass->input['supplierno']."','".$arrivalTime."','".$this->kclass->input['version']."','".$this->kclass->input['quantity']."','".TIMENOW."','".$this->kclass->user['userid']."')
		");
		$barcodeid=$this->kclass->DB->insertID();

		$this->kclass->messager(array(
			'title' => '生成条码',
			'text' => '该物资的条码 <b>'.$barcode.'</b> 已新建成功!返回条码详情页',
			'url' => '/s.php?module=barcode&action=view&barcodeid='.$barcodeid,
			'sec' => 2
		));
	}
	// 对每个物资进行条码插入
	function addPerBarcode(){
		$this->kclass->input['times']+=1;
		$parentBarcode=$this->kclass->DB->queryFirst("SELECT b.*,m.ifPerBarcode FROM barcode AS b LEFT JOIN material AS m ON (b.materialid=m.materialid) WHERE barcodeid='".$this->kclass->input['barcodeid']."'LIMIT 0,1");
		//获得所有的条码
		$date=date('y-n-j',$parentBarcode['arrivalTime']);
		$perBarcode=$this->_addBarcode(array('country'=>$parentBarcode['country'],'materialno'=>$parentBarcode['materialno'],'supplierno'=>$parentBarcode['supplierno'],'date'=>$date,'count'=>$parentBarcode['quantity'],'version'=>$parentBarcode['version'],'barcodeid'=>$parentBarcode['barcodeid'],'ifPerBarcode'=>1));

		$total=ceil($parentBarcode['quantity']/200);//刷新次数
		$remainder=$this->kclass->iif($parentBarcode['quantity']%200==0,200,$parentBarcode['quantity']%200);//最后一次的insert次数
		$count=$this->kclass->iif($this->kclass->input['times']==$total,$remainder,200);
		for($i=1;$i<=$count;$i++){
			$number=$i+($this->kclass->input['times']-1)*200;
			$parentBarcode['count']=$parentBarcode['number']+$number;
			$this->kclass->DB->query("
				INSERT INTO barcode (materialid,supplierid,parentid,barcode,country,materialno,supplierno,number,arrivalTime,version,quantity,created,creator)
				VALUES ('".$parentBarcode['materialid']."','".$parentBarcode['supplierid']."','".$this->kclass->input['barcodeid']."','".$perBarcode[$number-1]."','".$parentBarcode['country']."','".$parentBarcode['materialno']."','".$parentBarcode['supplierno']."','".$parentBarcode['count']."','".$parentBarcode['arrivalTime']."','".$parentBarcode['version']."','".$parentBarcode['quantity']."','".TIMENOW."','".$this->kclass->user['userid']."')
			");
		}
		if($this->kclass->input['times']==$total){
			$this->kclass->messager(array(
				'title' => '生成条码',
				'text' => '该物资的条码 '.$barcode.' 已新建成功!返回条码详情页',
				'url' => '/s.php?module=barcode&action=view&barcodeid='.$this->kclass->input['barcodeid'],
				'sec' => 2
			));
		}else{
			$this->kclass->messager(array(
				'title' => '生成流水号条码',
				'text' => '正在生成流水号条码，每次生成200个流水号,当前流水号区间为【'.(($this->kclass->input['times']-1)*200+1).'——'.(($this->kclass->input['times']-1)*200+1+$remainder).'】,请等待自动返回页面...',
				'url' => '/s.php?module=barcode&action=addPerBarcode&barcodeid='.$parentBarcode['barcodeid'].'&times='.$this->kclass->input['times'],
				'sec' => 2
			));
		}
	}
	// 
	function viewAttribute(){
		$barcode=$this->kclass->DB->queryFirst("SELECT barcodeid,parentid,barcode FROM barcode WHERE barcodeid='".$this->kclass->input['barcodeid']."' LIMIT 0,1");
		if($barcode){
			//获得属性值
      $attributes=$this->kclass->DB->query("
        SELECT an.nameid,an.title AS name,
          av.title AS value
        FROM  attributename AS an
        LEFT JOIN attributevalue AS av ON (av.nameid=an.nameid)
        WHERE an.killed=0 AND an.module='barcode' AND an.mid='".$barcode['barcodeid']."'
        ORDER BY an.ordering ASC
      ");
      $nameId=$title=$value=array();
      if($this->kclass->DB->numRows()){
        while($attribute=$this->kclass->DB->fetchArray($attributes)){
          if(in_array($attribute['nameid'],$nameId)){
            $title[$attribute['nameid']]=$attribute['name'];
            $value[$attribute['nameid']].=$attribute['value'].'<br>';
          }else{
            $nameId[]=$attribute['nameid'];
            $title[$attribute['nameid']]=$attribute['name'];
            $value[$attribute['nameid']]=$attribute['value'].'<br>';
          }
        }
				foreach($nameId as $key => $val){
					$attributetr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').'"><td>'.$title[$val].'</td><td>'.$value[$val].' '.$unit[$val].'</td><td><a href="/s.php?module=attribute&action=remove&barcodeid='.$barcode['barcodeid'].'&nameid='.$val.'" onclick="return confirm(\'你确定要删除这个属性 '.$title[$val].' 吗（删除后不可恢复）？\');">删除</a></td></tr>';
				}
      }
$body=<<<EOF
<div class="title">{$barcode['barcode']}</div>
<table style="width:450px;">
<thead><tr><th colspan=3>流水号条码属性值<span class="right"><a href="/s.php?module=attribute&action=update&barcodeid={$barcode['barcodeid']}">修改属性</a></span></th></tr></thead>
<tbody>
<tr class="even center"><td>属性名</td><td>属性值</td><td>操作</td></tr>
{$attributetr}
</tbody>
</table>
EOF;
		}else{
			$body='数据错误，很抱歉';
		}

		$this->kclass->page['title'].='查看流水号条码属性值';
		$this->kclass->page['onload'].='dc.tabhover();';
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - <a href="/s.php?module=barcode&action=view&barcodeid='.$barcode['parentid'].'">查看条码</a> - 查看属性', 'right'=>'<a href="/s.php?module=barcode&action=view&barcodeid='.$barcode['parentid'].'">返回查看条码</a>　|　<a href="/s.php?module=barcode&action=updateAttribute&barcodeid='.$barcode['parentid'].'">修改</a>','body'=>$body));
	}
	//
	function doscrap(){
		if($this->kclass->input['barcodeid']<=0){
			$this->kclass->boinkIt('/s.php?module=barcode');
		}
		if(!$this->kclass->input['rt'] AND $this->kclass->input['confirm'] == 0){
			$this->kclass->boinkIt('/s.php?module=barcode&action=view&barcodeid='.$this->kclass->input['barcodeid']);
		}
		if($this->kclass->input['barcodeid'] < 0){
			$e = '<li>请从正确的页面进行操作，如果此问题持续存在，请联系网站管理员。</li>';
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '条码报废',
				'text' => '您在条码报废的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		if($this->kclass->input['rt']=='view'){
			$rt = '&action=view&barcodeid='.$this->kclass->input['barcodeid'];
		}elseif($this->kclass->input['rt']=='list'){
			$rt = '#'.$this->kclass->input['barcodeid'];
		}
		$barcode=$this->kclass->DB->queryFirst("
			SELECT barcode
			FROM barcode
			WHERE barcodeid='".$this->kclass->input['barcodeid']."'
		");
		if($barcode){
			$this->kclass->DB->query("
				UPDATE `barcode`
				SET scraped=".TIMENOW.",scraper='".$this->kclass->user['userid']."'
				WHERE barcodeid='".$this->kclass->input['barcodeid']."'
			");
			$this->kclass->messager(array(
				'title' => '条码报废成功',
				'text' => '物资 <b>'.$barcode['barcode'].'</b> 已成功被标记为报废!',
				'url' => '/s.php?module=barcode&action='.$rt,
				'sec' => 2
			));
		}else{
			$this->kclass->messager(array(
				'title' => '条码报废失败',
				'text' => '您要报废的条码，不存在！',
				'url' => '/s.php?module=barcode&action='.$rt,
				'sec' => 3
			));
		}
	}

	//
	function scrap(){
		if($this->kclass->input['barcodeid']<=0){
			$this->kclass->boinkIt('/s.php?module=barcode');
		}
		$barcode = $this->kclass->DB->queryFirst("
			SELECT barcode
			FROM `barcode`
			WHERE barcodeid='".$this->kclass->input['barcodeid']."'
		");
$body = <<<EOF
<form action="/s.php?module=barcode&action=kill" name="barcode" method="post">
<input type="hidden" name="module" value="barcode">
<input type="hidden" name="action" value="doscrap">
<input type="hidden" name="barcodeid" value="{$this->kclass->input['barcodeid']}">
<table><thead>
<thead>
<tr>
	<th>删除物资：{$barcode['barcode']}</th>
</tr>
</thead>
<tbody>
<tr class="even">
	<td class="middle">你确定要报废物资: <a href="/s.php?module=barcode&action=view&barcodeid={$this->kclass->input['barcodeid']}" class="big bold" target="_blank">{$barcode['barcode']}</a> 吗?</td>
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
		$this->kclass->page['title'] .= ' - 报废 - '.$barcode['barcode'];
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 条码报废 - '.$barcode['barcode'], 'right' => '<a href="/s.php?module=barcode">返回列表</a>', 'body'=>$body));
	}

	//
	function revival(){
		if($this->kclass->input['barcodeid']<=0){
			$this->kclass->boinkIt('/s.php?module=barcode');
		}
		if(!$this->kclass->input['rt'] AND $this->kclass->input['confirm'] == 0){
			$this->kclass->boinkIt('/s.php?module=barcode&action=view&barcodeid='.$this->kclass->input['barcodeid']);
		}
		if($this->kclass->input['barcodeid'] < 0){
			$e = '<li>请从正确的页面进行操作，如果此问题持续存在，请联系网站管理员。</li>';
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '恢复物资',
				'text' => '您在恢复条码的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$barcode = $this->kclass->DB->queryFirst("
			SELECT barcode
			FROM `barcode`
			WHERE barcodeid='".$this->kclass->input['barcodeid']."'
		");
		if($this->kclass->input['rt']=='view'){
			$rt = '&action=view&barcodeid='.$this->kclass->input['barcodeid'];
		}elseif($this->kclass->input['rt']=='list'){
			$rt = '#'.$this->kclass->input['barcodeid'];
		}
		if($barcode){
			$this->kclass->DB->query("
				UPDATE `barcode`
				SET scraped=0,scraper=0
				WHERE barcodeid='".$this->kclass->input['barcodeid']."'
			");
			$this->kclass->messager(array(
				'title' => '恢复条码成功',
				'text' => '条码 <b>'.$barcode['barcode'].'</b> 已成功被恢复！',
				'url' => '/s.php?module=barcode'.$rt,
				'sec' => 2
			));
		}else{
			$this->kclass->messager(array(
				'title' => '恢复条码失败',
				'text' => '您要恢复的条码不存在！',
				'url' => '/s.php?module=barcode'.$rt,
				'sec' => 3
			));
		}
	}

	//
	function restore(){
		if($this->kclass->input['barcodeid']<=0){
			$this->kclass->boinkIt('/s.php?module=barcode');
		}
		$barcode = $this->kclass->DB->queryFirst("
			SELECT barcode
			FROM `barcode`
			WHERE barcodeid='".$this->kclass->input['barcodeid']."'
		");
$body = <<<EOF
<form action="/s.php?module=barcode&action=revival" name="barcode" method="post">
<input type="hidden" name="module" value="barcode">
<input type="hidden" name="action" value="revival">
<input type="hidden" name="barcodeid" value="{$this->kclass->input['barcodeid']}">
<table><thead>
<thead>
<tr>
	<th>恢复条码：{$barcode['barcode']}</th>
</tr>
</thead>
<tbody>
<tr class="even">
	<td class="middle">你确定要恢复条码: <a href="/s.php?module=barcode&action=view&barcodeid={$this->kclass->input['barcodeid']}" class="big bold" target="_blank">{$barcode['barcode']}</a> 吗?</td>
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
		$this->kclass->page['title'] .= ' - 恢复 - '.$barcode['barcode'];
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 恢复条码 - '.$barcode['barcode'], 'right' => '<a href="/s.php?module=barcode">返回列表</a>', 'body'=>$body));
	}
	function viewChildren(){
		if($this->kclass->input['title']==''){
			$body=<<<EOF
<form method="get" action="/s.php?module=barcode&action=viewChildren" >
<input type="hidden" name="module" value="barcode">
<input type="hidden" name="action" value="viewChildren">
<table width="600">
	<thead><tr><th colspan=5>查询子BOM的批次（① 快速服务代码；② 条码；③ 旧序列号）</th></tr></thead>
	<tbody><tr>
		<td>输入查询关键字：<input type="text" name="title" value="" size="50" ></td>
		<td><input type="submit" value="查询"></td>
	</tr></tbody>
</table>
</form>
EOF;
		}else{
			$reg1='/^\d{8}$/';// 快速服务代码
			$reg2='/^[A-Z]{2}-[a-zA-Z]{3}\d{3}-\w{4}-\w{3}(-\d{4})?-\d{2}$/';// 条码
			$reg3='/(NW|NWC|NWCR)\w+/';// 旧序列号
			$title=htmlspecialchars($this->kclass->input['title']);
			if(preg_match($reg1,$title)){
				$type=1;
			}elseif(preg_match($reg2,$title)){
				$type=2;
			}elseif(preg_match($reg3,$title)){
				$type=3;
			}
			if($type==1){
				$result=$this->kclass->DB->queryFirst("
					SELECT sn.*,mv.itemid,mv.materialid
					FROM serviceno AS sn
					LEFT JOIN materialversion AS mv ON (sn.versionid=mv.versionid)
					WHERE sn.serviceno='{$title}'
					LIMIT 0,1
				");
			}elseif($type==2){
				$result1=$this->kclass->DB->queryFirst("
					SELECT ba.barcodeid
					FROM barcode AS ba
					LEFT JOIN `batch` AS b ON (b.batchid=ba.batchid)
					WHERE ba.barcode='{$title}'
					LIMIT 0,1
				");
				if(!$result1){
					$result=$this->kclass->DB->queryFirst("
						SELECT cb.childid,cb.title AS barcode,mv.itemid,mv.materialid,sn.serviceno,sn.orderid,b.batchno
						FROM childBarcode AS cb
						LEFT JOIN materialversion AS mv ON (mv.versionid=cb.versionid)
						LEFT JOIN serviceno AS sn ON (sn.childbarcodeid=cb.childid)
						LEFT JOIN batch AS b ON (b.batchid=cb.batchid)
						WHERE cb.title='{$title}'
						LIMIT 0,1
					");
				}else{
					$result=$this->kclass->DB->queryFirst("
						SELECT oi.materialid,oi.materialitemid AS itemid,o.orderid,o.sampleid,b.batchid,b.batchno,ba.barcode
						FROM outbounditem AS oi
						LEFT JOIN outbound AS o ON (oi.outboundid=o.outboundid)
						LEFT JOIN batch AS b ON (b.batchid=oi.batchid)
						LEFT JOIN barcode AS ba ON (ba.barcodeid=oi.barcodeid)
						WHERE oi.barcodeid={$result1['barcodeid']}
					");
				}
			}elseif($type==3){
				$result=$this->kclass->DB->queryFirst("SELECT * FROM serviceno WHERE oldno='{$title}' LIMIT 0,1 ");
			}
			if($result){
				$materialinfo=$this->kclass->getMaterial(array('materialid'=>$result['materialid'],'itemid'=>$result['itemid']));
				if($materialinfo){
					$material=$materialinfo['no'].'　'.$materialinfo['material'].'　'.$materialinfo['standard'];
				}else{
					$material=$result['material'];
				}
				if($result['orderid']>0){ // 订单
					$porder=$this->kclass->DB->queryFirst("SELECT parentid FROM `order` WHERE orderid={$result['orderid']}");
					if($porder['parentid']==0){//  查询的信息的订单为PI订单
						$po=$this->kclass->DB->queryFirst("SELECT orderid FROM `order` WHERE parentid={$result['orderid']}");
						$orderid=$porder['parentid'];
						$porderid=$po['orderid'];
					}else{//  查询的信息的订单为PO订单
						$porderid=$result['orderid'];
						$orderid=$porder['parentid'];
					}
					// 查询PI订单信息
					$order=$this->kclass->DB->queryFirst("
						SELECT o.orderid,o.orderno,o.shipmentDate,
							c.customerid,c.title AS customer,c.regionid,
							r.country AS regionCountry,r.state AS regionState,r.city AS regionCity
						FROM `order` AS o
						LEFT JOIN `customer` AS c ON (o.customerid=c.customerid)
						LEFT JOIN region AS r ON (c.regionid=r.regionid)
						WHERE orderid={$orderid}
					");
					$orderno='<a href="/s.php?module=order&action=view&orderid='.$order['orderid'].'">'.$order['orderno'].'</a>';
					$customer='<a href="/s.php?module=customer&action=view&customerid='.$order['customerid'].'">'.$order['customer'].'</a>';
					$dateline=date('Y-m-d',$order['shipmentDate']);
					if($order['regionid']>0){
						$region = $order['regionCountry'].' '.$order['regionState'].' '.$order['regionCity'];
					}else{
						$region='无';
					}
					// 通过PO 订单 获得出入库信息
					$outbound=$this->kclass->DB->queryFirst("SELECT outboundid,outboundno FROM outbound WHERE killed=0 AND orderid={$porderid} LIMIT 0,1");
					$inbound=$this->kclass->DB->queryFirst("SELECT inboundid,inboundno FROM inbound WHERE killed=0 AND orderid={$porderid} LIMIT 0,1");
					if($outbound){
						$result=array_merge($result,$outbound,$inbound);
					}else{
						$result=array_merge($result,$inbound);
					}
					$type='<span class="gray">（订单）</span>';
				}elseif($result['sampleid']>0){ // 样品单 样品单部分PI 和 PO 。
					$sample=$this->kclass->DB->queryFirst("
						SELECT s.sampleid,s.sampleno,s.deliveryDate,
						c.customerid,c.title AS customer,c.regionid,
						r.country AS regionCountry,r.state AS regionState,r.city AS regionCity
						FROM `sample` AS s
						LEFT JOIN `customer` AS c ON (s.customerid=c.customerid)
						LEFT JOIN region AS r ON (c.regionid=r.regionid)
						WHERE s.sampleid={$result['sampleid']}
					");
					$orderno='<a href="/s.php?module=sample&action=view&sampleid='.$sample['sampleid'].'">'.$sample['sampleno'].'</a>';
					$customer='<a href="/s.php?module=customer&action=view&customerid='.$sample['customerid'].'">'.$sample['customer'].'</a>';
					$dateline=date('Y-m-d',$sample['deliveryDate']);
					if($sample['regionid']>0){
						$region = $sample['regionCountry'].' '.$sample['regionState'].' '.$sample['regionCity'];
					}else{
						$region='无';
					}
					$outbound=$this->kclass->DB->queryFirst("SELECT outboundid,outboundno FROM outbound WHERE killed=0 AND sampleid={$sample['sampleid']} LIMIT 0,1");
					$inbound=$this->kclass->DB->queryFirst("SELECT inboundid,inboundno FROM inbound WHERE killed=0 AND sampleid={$sample['sampleid']} LIMIT 0,1");
					if($outbound){
						$result=array_merge($result,$outbound,$inbound);
					}else{
						$result=array_merge($result,$inbound);
					}
					$type='<span class="gray">（样品单）</span>';
				}else{
					$orderno=$result['orderno'];
					$customer='无';
					$region='无';
				}
				if($result['childbarcodeid']>0){
					$childBarcode=$this->kclass->DB->queryFirst("
						SELECT cb.title,cb.versionid,b.materialid,b.materialitemid,cb.title AS childBarcode,b.batchno
						FROM childbarcode AS cb
						LEFT JOIN materialversion AS mv ON (mv.versionid=cb.versionid)
						LEFT JOIN `batch` AS b ON (b.batchid=cb.batchid)
						WHERE cb.childid='{$result['childbarcodeid']}'
						LIMIT 0,1
					");
					$barcode=$childBarcode['title'];
					$result['batchno']=$childBarcode['batchno'];
				}else{
					$barcode=$this->kclass->iif($result['barcode'],$result['barcode'],'');
				}
			/*	// 获得底层批次
			  $itemtr.='<tr><td>物资</td><td>批次</td><td>条码</td></tr>';
				if($this->kclass->input['type']==1){
					$outbound=$this->kclass->DB->queryFirst("
						SELECT DISTINCT poi.mid,poi.itemid,taskitemid
						FROM preoutbounditem AS poi
						LEFT JOIN  taskitem AS ti ON (ti.itemid=poi.taskitemid)
						LEFT JOIN requirementitem AS ri ON (ri.requirementid=ti.requirementid)
						WHERE ri.itemid={$result['requirementitemid']} AND ti.versionid={$result['versionid']}
						LIMIT 0,1	
					");
					$outboundno=$this->kclass->DB->queryFirst("SELECT outboundid,outboundno FROM outbound WHERE outboundid={$outbound['mid']} LIMIT 0,1");
					$outitems=$this->kclass->DB->query("
						SELECT oi.batchid,oi.barcodeid,oi.materialid,oi.materialitemid,
							b.batchno,ba.barcode
						FROM outbounditem AS oi
						LEFT JOIN material AS m ON (m.materialid=oi.materialid)
						LEFT JOIN batch AS b ON (b.batchid=oi.batchid)
						LEFT JOIN barcode AS ba ON (ba.barcodeid=oi.barcodeid)
						WHERE oi.outboundid={$outbound['mid']} AND oi.preitemid={$outbound['itemid']}
						ORDER BY m.materialno ASC 
					");
					if($this->kclass->DB->numRows()){
						while($outitem=$this->kclass->DB->fetchArray($outitems)){
							$outMaterial=$this->kclass->getMaterial(array('materialid'=>$outitem['materialid'],'itemid'=>$outitem['materialitemid']));
							$a=$this->_getChildrenBarcode(array('barcodeid'=>$outitem['barcodeid']));
							$itemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').'">
								<td>'.$outMaterial['no'].'　'.$outMaterial['material'].'　'.$outMaterial['standard'].'</td>
								<td>'.$outitem['batchno'].'</td>
								<td>'.$outitem['barcode'].'</td>
							</tr>';
						}
					}
				}else{
					$outitems=$this->kclass->DB->query("
						SELECT oi.outboundid,oi.batchid,oi.barcodeid,oi.materialid,oi.materialitemid,o.outboundno,
							b.batchno,ba.barcode
						FROM outbounditem AS oi
						LEFT JOIN preoutbounditem AS poi ON (poi.itemid=oi.preitemid)
						LEFT JOIN outbound AS o ON (o.outboundid=oi.outboundid)
						LEFT JOIN taskitem AS ti ON (ti.itemid=poi.taskitemid)
						LEFT JOIN material AS m ON (m.materialid=oi.materialid)
						LEFT JOIN batch AS b ON (b.batchid=oi.batchid)
						LEFT JOIN barcode AS ba ON (ba.barcodeid=oi.barcodeid)
						WHERE oi.killed=0 AND poi.killed=0 AND oi.barcodeid={$result['barcodeid']}
						GROUP BY oi.barcodeid						
						ORDER BY m.materialno ASC
					");
					if($this->kclass->DB->numRows()){
						while($outitem=$this->kclass->DB->fetchArray($outitems)){
							$outMaterial=$this->kclass->getMaterial(array('materialid'=>$outitem['materialid'],'itemid'=>$outitem['materialitemid']));
							$a=$this->_getChildrenBarcode(array('barcodeid'=>$outitem['barcodeid']));
							$itemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').'">
								<td>'.$outMaterial['no'].'　'.$outMaterial['material'].'　'.$outMaterial['standard'].'</td>
								<td>'.$outitem['batchno'].'<a href="/s.php?module=outbound&action=view&outboundid='.$outitem['outboundid'].'">'.$outitem['outboundno'].'</a></td>
								<td>'.$outitem['barcode'].'</td>
							</tr>';
						}
					}
				}*/
			}
			$outbound=$this->kclass->iif($result['outboundno'],'<a href="/s.php?module=outbound&action=view&outboundid='.$result['outboundid'].'">'.$result['outboundno'].'</a>','无');
			$inbound=$this->kclass->iif($result['inboundno'],'<a href="/s.php?module=inbound&action=view&inboundid='.$result['inboundid'].'">'.$result['inboundno'].'</a>','无');
			$body=<<<EOF
<form method="get" action="/s.php?module=barcode&action=viewChildren" >
<input type="hidden" name="module" value="barcode">
<input type="hidden" name="action" value="viewChildren">
<table width="600">
	<thead><tr><th colspan=5>根据快速服务代码或者条码查询子BOM的批次</th></tr></thead>
	<tbody><tr>
		<td>输入查询关键字：<input type="text" name="title" value="{$this->kclass->input['title']}" size="50" ></td>
		<td><input type="submit" value="查询"></td>
	</tr></tbody>
</table>
</form>
<table class="hundred">
<thead><tr><th colspan=6>查询条件 【<span class="red">{$this->kclass->input['title']}</span>】　查询结果 【<span class="red">{$material}</span>】</th></tr></thead>
<tbody>
<tr>
	<td width="700">快速服务编号：<b>{$result['serviceno']}</b></td>
	<td>批　　　次：<b>{$result['batchno']}</b>　　　　条　　　码：<b>{$barcode}</b></td>
	
</tr>
<tr>
	<td>出　库　单：<b>{$outbound}</b></td>
	<td>入　库　单：<b>{$inbound}</b></td>
</tr>
<tr>
	<td>订单或样品单：<b>{$orderno}</b>{$type}</td>
	<td>订单交货时间：<b>{$dateline}</b></td>
</tr>
<tr>
	<td>客　　　户：<b>{$customer}</b></td>
	<td>客 户 国 家：<b>{$region}</b></td>
</tr>
{$itemtr}
</tbody>
</table>
EOF;
		}
		$this->kclass->tbline+=4;
		$this->kclass->page['title'].='生成条码';
		$this->kclass->page['onload'].='$(\'#date\').datepicker()';
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - 查询子BOM的批次', 'right'=>'<a href="/s.php?module=porder&action=list">返回列表</a>','body'=>$body));
	}
	function exportSerialno(){
		$this->kclass->input['times']+=1;
		$results=$this->kclass->DB->query("SELECT * FROM serviceno ORDER BY servicenoid ASC LIMIT ".(($this->kclass->input['times']-1)*10).",10");
		/*$totalQuantity=$this->kclass->DB->queryFirst("SELECT SUM(quantity) AS quantity FROM originalserviceno");
		$counter=$totalQuantity['quantity'];*/
		$counter=$this->kclass->DB->queryFirst("SELECT COUNT(*) AS count FROM serviceno");
		$total=ceil(($counter['count'])/10);//刷新次数
		$count=$this->kclass->iif($this->kclass->input['times']==$total,$counter['count'],($this->kclass->input['times'])*10);
		$min=($this->kclass->input['times']-1)*10;
		$max=$this->kclass->input['times']*10;
		if($counter['count']){
			$i=0;
			while($result=$this->kclass->DB->fetchArray($results)){
				$materialinfo=$this->kclass->getMaterial(array('materialid'=>$result['materialid'],'itemid'=>$result['itemid']));
				if($materialinfo){
					$material=$materialinfo['no'].'　'.$materialinfo['material'].'　'.$materialinfo['standard'];
				}else{
					$material=$result['material'];
				}
				$sampleno=$customer=$linkman='';
				$orderid=$sampleid=$dateline=$customerid=$creator=$modifier=0;
				$orderno=$result['orderno'];
				$region='';
				if($result['orderid']>0){ // 订单
					$porder=$this->kclass->DB->queryFirst("SELECT parentid FROM `order` WHERE orderid={$result['orderid']}");
					if($porder['parentid']==0){//  查询的信息的订单为PI订单
						$po=$this->kclass->DB->queryFirst("SELECT orderid FROM `order` WHERE parentid={$result['orderid']}");
						$orderid=$porder['parentid'];
						$porderid=$po['orderid'];
					}else{//  查询的信息的订单为PO订单
						$porderid=$result['orderid'];
						$orderid=$porder['parentid'];
					}
					// 查询PI订单信息
					$order=$this->kclass->DB->queryFirst("
						SELECT orderid,orderno,customerid,shipmentDate,creator,modifier,customerTitle
						FROM `order`
						WHERE orderid={$orderid}
					");
					if($order['customerid']>0){
						$customerinfo=$this->kclass->DB->queryFirst("
							SELECT c.customerid,c.title AS customer,c.regionid,c.linkman,
								r.country AS regionCountry,r.state AS regionState,r.city AS regionCity
							FROM `customer` AS c 
							LEFT JOIN `region` AS r ON (c.regionid=r.regionid)
							WHERE customerid={$order['customerid']}
						");
						$customerid=$customerinfo['customerid'];
						$customer=$customerinfo['customer'];
						$linkman=$customerinfo['linkman'];
						$region=$customerinfo['regionCountry'].' '.$customerinfo['regionState'].' '.$customerinfo['regionCity'];
					}else{
						$customerid=0;
						$customer=$order['customerTitle'];
						$region='';
						$linkman=$order['customerTitle'];
					}
					$orderid=$order['orderid'];
					$orderno=$order['orderno'];
					$dateline=$order['shipmentDate'];
					$modifier=$order['modifier'];
					$creator=$order['creator'];
	
					// 通过PO 订单 获得出入库信息
					$outbound=$this->kclass->DB->queryFirst("SELECT outboundid,outboundno FROM outbound WHERE killed=0 AND orderid={$porderid} LIMIT 0,1");
					$inbound=$this->kclass->DB->queryFirst("SELECT inboundid,inboundno FROM inbound WHERE killed=0 AND orderid={$porderid} LIMIT 0,1");
					if($outbound AND $inbound){
						$result=array_merge($result,$outbound,$inbound);
					}elseif($inbound){
						$result=array_merge($result,$inbound);
					}else{
						$result=$result;
					}
				}elseif($result['sampleid']>0){ // 样品单 样品单部分PI 和 PO 。
					$sample=$this->kclass->DB->queryFirst("
						SELECT sampleid,sampleno,customerid,customerTitle,deliveryDate,modifier,creator
						FROM `sample` 
						WHERE sampleid={$result['sampleid']}
					");
					if($sample['customerid']>0){
						$customerinfo=$this->kclass->DB->queryFirst("
							SELECT c.customerid,c.title AS customer,c.regionid,c.linkman,
								r.country AS regionCountry,r.state AS regionState,r.city AS regionCity
							FROM `customer` AS c 
							LEFT JOIN `region` AS r ON (c.regionid=r.regionid)
							WHERE customerid={$sample['customerid']}
						");
						$customerid=$customerinfo['customerid'];
						$customer=$customerinfo['customer'];
						$linkman=$customerinfo['linkman'];
						$region=$customerinfo['regionCountry'].' '.$customerinfo['regionState'].' '.$customerinfo['regionCity'];
					}else{
						$customerid=0;
						$customer=$sample['customerTitle'];
						$linkman=$sample['customerTitle'];
						$region='';
					}
					$sampleid=$sample['sampleid'];
					$sampleno=$sample['sampleno'];
					$dateline=$sample['deliveryDate'];
					$modifier=$sample['modifier'];
					$creator=$sample['creator'];

					$outbound=$this->kclass->DB->queryFirst("SELECT outboundid,outboundno FROM outbound WHERE killed=0 AND sampleid={$sample['sampleid']} LIMIT 0,1");
					$inbound=$this->kclass->DB->queryFirst("SELECT inboundid,inboundno FROM inbound WHERE killed=0 AND sampleid={$sample['sampleid']} LIMIT 0,1");
					if($outbound AND $inbound){
						$result=array_merge($result,$outbound,$inbound);
					}elseif($inbound){
						$result=array_merge($result,$inbound);
					}else{
						$result=$result;
					}
				}

				$arr=array(
					'oldno'=>$result['oldno'],
					'batchno'=>$result['batchno'],
					'serviceno'=>$result['serviceno'],
					'orderid'=>$orderid,
					'orderno'=>$orderno,
					'sampleid'=>$sampleid,
					'sampleno'=>$sampleno,
					'linkman'=>$linkman,
					'dateline'=>$dateline,
					'customerid'=>$customerid,
					'customer'=>$customer,
					'region'=>$region,
					'orderCreator'=>$creator,
					'orderModifier'=>$modifier
				);
				$this->kclass->DB->query("
					INSERT INTO serialnoresult (oldno,batchno,serviceno,orderid,orderno,sampleid,sampleno,linkman,dateline,customerid,customer,region,orderCreator,orderModifier,result) 
					VALUES ('{$result['oldno']}','{$result['batchno']}','{$result['serviceno']}','{$orderid}','{$orderno}','{$sampleid}','{$sampleno}','{$linkman}','{$dateline}','{$customerid}','{$customer}','{$region}','{$creator}','{$modifier}','".serialize($arr)."')
				");
				$i++;
			}
		}
		if($this->kclass->input['times']==$total){
			$this->kclass->messager(array(
				'title' => '加入物资列表',
				'text' => '物资加入物资列表成功！',
				'url' => '/s.php?module=barcode&action=list',
				'sec' => 2
			));
		}else{
			$this->kclass->messager(array(
				'title' => '加入物资列表',
				'text' => '正在批量加入物资列表，每次加入10个，请等待自动返回页面...',
				'url' => '/s.php?module=barcode&action=exportSerialno&times='.$this->kclass->input['times'],
				'sec' => 2
			));
		}
	}
		//
	function importSerialno(){
		$upload=$this->kclass->upload(array('title'=>'批量导入：'));
$body=<<<EOF
<form method="post" action="/s.php?module=barcode&action=doimportSerialno">
<input type="hidden" name="module" value="barcode">
<input type="hidden" name="action" value="doimportSerialno">
{$upload}
<table class="tlist tsuite" align="center" width="100%">
<tbody>
<tr class="odd center" nohover>
<td colspan=4><input type="submit" id="submitButton" value="  提交  "><input type="reset" value="  复位  "/></td>
</tr>
</tbody>
</table>
</form>
EOF;
		$this->kclass->page['title']=' 批量导入序列号';
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - 批量导入序列号','right'=>'<a href="/s.php?module=barcode">返回列表</a>','body'=>$body));
	}
	//
	function doimportSerialno(){
		$this->kclass->input['times']+=1;
		$fileCount=0;
		if(count($this->kclass->input['attach'])>0 AND $this->kclass->input['times']==1){
			foreach($this->kclass->input['attach'] as $k => $v){
				if($v==0 OR $v==1){//0:保留，需要检查title与ordering是否更改。1:选择
					$cdt='';
					if($this->kclass->input['attachTitle'][$k] != $this->kclass->input['attachOrgTitle'][$k])$cdt="`title`='".$this->kclass->input['attachTitle'][$k]."',";
					if($this->kclass->input['attachOrdering'][$k] != $this->kclass->input['attachOrgOrdering'][$k])$cdt.="`ordering`='".$this->kclass->input['attachOrdering'][$k]."',";
					if($v==1)$cdt.= "`module`='".$this->kclass->input['module']."',";
					if($cdt != ''){
						$this->kclass->DB->query("UPDATE `attachs` SET ".$cdt."`modifier`='".$this->kclass->user['userid']."',`modified`='".TIMENOW."' WHERE `attachid`='".$k."'");
					}
					$fileCount++;
				}elseif($v==2){//删除
					$attach=$this->kclass->DB->queryFirst("SELECT `folder`,`newName`,`image`,`imginfo` FROM `attachs` WHERE `attachid`='".$k."'");
					if($attach['image']==1){
						$img=unserialize($attach['imginfo']);
						@unlink('public/attachs/'.$img['thumb']);
						@unlink('public/attachs/'.$img['senior']);
						@unlink('public/attachs/'.$img['big']);
					}
					@unlink('public/attachs/'.$attach['folder'].'/'.$attach['newName']);
					$this->kclass->DB->query("DELETE FROM `attachs` WHERE `attachid`='".$k."'");
				}
			}
		}else{
			$fileCount=1;
		}
		if($fileCount > 0){
			if($attach=$this->kclass->DB->queryFirst("SELECT COUNT(attachid) AS n,newName,folder,type FROM `attachs` WHERE `module`='".$this->kclass->input['module']."' AND `mid`=0 AND `image`<> 1 ORDER BY attachid DESC LIMIT 0,1")){
				require ROOT.'/phpexcel/Classes/PHPExcel.php';
				require ROOT.'/phpexcel/Classes/PHPExcel/IOFactory.php';
				$objPHPExcel=PHPExcel_IOFactory::load('public/attachs/'.$attach['folder'].'/'.$attach['newName']);
				foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
					$worksheetTitle=$worksheet->getTitle();
					$highestRow=$worksheet->getHighestRow(); // e.g. 10
					if($highestRow>1){
						$highestColumn=$worksheet->getHighestColumn(); // e.g 'F'
						$highestColumnIndex=PHPExcel_Cell::columnIndexFromString($highestColumn);
						$nrColumns=ord($highestColumn)-64;

						$total=ceil(($highestRow-1)/10);//刷新次数
						$count=$this->kclass->iif($this->kclass->input['times']==$total,$highestRow,($this->kclass->input['times'])*10+1);
						$start=($this->kclass->input['times']-1)*10+2;
						for($row=$start;$row<=$count;$row++){
							$val=array();
							for ($col=0;$col<$highestColumnIndex;$col++) {
								$cell=$worksheet->getCellByColumnAndRow($col,$row);
								$val[$col]=$cell->getValue();
							}
							if($val[1]!=''){
								$this->kclass->DB->query("
									INSERT INTO originalserviceno
										(orderno,product,materialno,batch,quantity,number,created,creator)
									VALUES
										('".$val[0]."','".$val[1]."','".$this->kclass->iif($val[2],$val[2],'')."','".$this->kclass->iif($val[3],$val[3],'')."','".$this->kclass->iif($val[4],$val[4],'')."','".$this->kclass->iif($val[5],$val[5],'')."','".TIMENOW."','".$this->kclass->user['userid']."')
								");
							}
						}
					}
				}
			}
		}
		if($this->kclass->input['times']==$total){
			$this->kclass->messager(array(
				'title' => '批量导入物资',
				'text' => '物资批量全部导入成功！',
				'url' => '/s.php?module=barcode&action=listSerialno',
				'sec' => 2
			));
		}else{
			$this->kclass->messager(array(
				'title' => '批量导入物资',
				'text' => '正在批量导入物资，每次导入10个，请等待自动返回页面...',
				'url' => '/s.php?module=barcode&action=doimportSerialno&times='.$this->kclass->input['times'],
				'sec' => 2
			));
		}
	}
	//
	function listSerialno(){
		$materials=$this->kclass->DB->query("SELECT * FROM originalserviceno ORDER BY originalid ASC");
		if($this->kclass->DB->numRows()){
			while($material=$this->kclass->DB->fetchArray($materials)){
				$r.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').'">
				<td>'.$material['orderno'].'</td>
				<td>'.$material['product'].'</td>
				<td>'.$material['materialno'].'</td>
				<td>'.$material['batch'].'</td>
				<td>'.$material['quantity'].'</td>
				<td>'.$material['number'].'</td></tr>';
			}
		}
		$body .= <<<EOF
<table>
<thead>
<tr>
<th>订单</th><th>产品</th><th>编号</th><th>批次</th><th>数量</th><th>序列号</th>
</tr>
</thead>
<tbody>
{$r}
</tr>
</tbody>
</table>
EOF;
		$this->kclass->page['title'] = ' 原始物资列表';
		$this->kclass->page['main'] .= $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 原始物资列表','right' => '<a href="/s.php?module=barcode&action=exchangeSerialno">转换到物资列表</a>','body'=>$body));
	}
	//
	function exchangeSerialno(){
		$this->kclass->input['times']+=1;
		$originalmaterials=$this->kclass->DB->query("SELECT * FROM originalserviceno ORDER BY originalid ASC LIMIT ".(($this->kclass->input['times']-1)*10).",10");
		/*$totalQuantity=$this->kclass->DB->queryFirst("SELECT SUM(quantity) AS quantity FROM originalserviceno");
		$counter=$totalQuantity['quantity'];*/
		$counter=$this->kclass->DB->queryFirst("SELECT COUNT(*) AS count FROM originalserviceno");
		$total=ceil(($counter['count'])/10);//刷新次数
		$count=$this->kclass->iif($this->kclass->input['times']==$total,$counter['count'],($this->kclass->input['times'])*10);
		$min=($this->kclass->input['times']-1)*10;
		$max=$this->kclass->input['times']*10;
		$endno=$this->kclass->DB->queryFirst("SELECT serviceno FROM serviceno WHERE killed=0 ORDER BY servicenoid DESC");
		$serviceno=$this->kclass->iif($endno['serviceno'],$endno['serviceno']+1,'1000000');
		if($counter['count']){
			$i=0;
			while($originalmaterial=$this->kclass->DB->fetchArray($originalmaterials)){
				//if($i>=$min AND $max-$i<=10 AND $max-$i>0){
				$material=$this->kclass->DB->queryFirst("
					SELECT mv.versionid,mv.materialid
					FROM materialversion AS mv 
					LEFT JOIN materialitem AS mi ON (mv.itemid=mi.itemid) 
					WHERE mi.itemno='".$originalmaterial['materialno']."' 
					LIMIT 0,1
				");
				$order=$this->kclass->DB->queryFirst("SELECT orderid FROM `order` WHERE orderno='".$originalmaterial['orderno']."' LIMIT 0,1");
				$sample=$this->kclass->DB->queryFirst("SELECT sampleid FROM `sample` WHERE sampleno='".$originalmaterial['orderno']."' LIMIT 0,1");
				if(strpos($originalmaterial['number'],'NW')>=0){
					$head=substr($originalmaterial['number'],0,6);
					$startno=substr($originalmaterial['number'],6,6);
					$tail=substr($originalmaterial['number'],12);
				}elseif(strpos($originalmaterial['number'],'NWC')>=0){
					$head=substr($originalmaterial['number'],0,7);
					$startno=substr($originalmaterial['number'],7,6);
					$tail=substr($originalmaterial['number'],13);
				}elseif(strpos($originalmaterial['number'],'NWCR')>=0){
					$head=substr($originalmaterial['number'],0,8);
					$startno=substr($originalmaterial['number'],8,6);
					$tail=substr($originalmaterial['number'],14);
				}
				for($j=1;$j<=$originalmaterial['quantity'];$j++){
					$this->kclass->DB->query("
						INSERT INTO serviceno (serviceno,oldno,material,batchno,versionid,orderid,sampleid,orderno,created,creator)
						VALUES ('{$serviceno}','".$head.$startno.$tail."','{$originalmaterial['product']}','".$originalmaterial['materialno'].'-'.$originalmaterial['batch']."','".$this->kclass->iif($material['versionid'],$material['versionid'],0)."','".$this->kclass->iif($order['orderid'],$order['orderid'],0)."','".$this->kclass->iif($sample['sampleid'],$sample['sampleid'],0)."','".$this->kclass->iif($order['orderid']>0 OR  $sample['sampleid']>0,'',$originalmaterial['orderno'])."','".TIMENOW."','{$this->kclass->user['userid']}')
					");
					$startno++;$serviceno++;
				}
				//}
				$i++;
			}
		}
		if($this->kclass->input['times']==$total){
			$this->kclass->messager(array(
				'title' => '加入物资列表',
				'text' => '物资加入物资列表成功！',
				'url' => '/s.php?module=barcode&action=list',
				'sec' => 2
			));
		}else{
			$this->kclass->messager(array(
				'title' => '加入物资列表',
				'text' => '正在批量加入物资列表，每次加入10个，请等待自动返回页面...',
				'url' => '/s.php?module=barcode&action=exchangeSerialno&times='.$this->kclass->input['times'],
				'sec' => 2
			));
		}
	}
	/****************************** private function *******************************/
	function _getChildrenBarcode($b){
		if(!$b['barcodeid'] OR $b['barcodeid']<=0){
			return false;
		}
		$i=$b['i']?$b['i']:1;
		$in=$this->kclass->DB->queryFirst("
			SELECT pii.taskitemid
			FROM inbounditem AS ii
			LEFT JOIN preinbounditem AS pii ON (pii.itemid=ii.preitemid)
			WHERE ii.killed=0 AND pii.taskitemid>0 AND pii.killed=0 AND ii.barcodeid={$b['barcodeid']}
		");
		if($in['taskitemid']){
			$as=$this->kclass->DB->query("
				SELECT oi.itemid,oi.materialid,oi.barcodeid,oi.batchid,poi.versionid,oi.materialitemid,o.outboundid,o.outboundno,
					mv.versionid,mv.title AS version,
					ba.barcode,b.batchno
				FROM outbounditem AS oi
				LEFT JOIN outbound AS o ON (o.outboundid=oi.outboundid)
				LEFT JOIN preoutbounditem AS poi ON (poi.itemid=oi.preitemid)
				LEFT JOIN materialversion AS mv ON (mv.versionid=poi.versionid)
				LEFT JOIN barcode AS ba ON (ba.barcodeid=oi.barcodeid)
				LEFT JOIN batch AS b ON (b.batchid=oi.batchid)
				WHERE oi.killed=0 AND poi.taskitemid={$in['taskitemid']}
			");
			if($this->kclass->DB->numRows()){
				while($a=$this->kclass->DB->fetchArray($as)){
					$ma=$this->kclass->getMaterial(array('materialid'=>$a['materialid'],'itemid'=>$a['materialitemid']));
					/*$str.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').'">
						<td>'.$ma['no'].'　'.$ma['material'].'　'.$ma['standard'].'</td>
						<td>'.$a['batchno'].'<a href="/s.php?module=outbound&action=view&outboundid='.$a['outboundid'].'">'.$a['outboundno'].'</a></td>
						<td>'.$a['barcode'].'</td>
					</tr>';*/
					
					$this->_getChildrenBarcode(array('barcodeid'=>$a['barcodeid'],'i'=>$i));
					
					echo '<pre>'.print_r($a,1).'</pre>';
									print_r($ma);
					if($b['i']==2){
						break;
					$i++;
					}
				}
			}
		}
		//return $str;
	}
}
?>