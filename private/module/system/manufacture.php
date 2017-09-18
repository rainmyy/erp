<?php
//
class manufacture{
	var $kclass;
	var $mftType=array(array('entitle'=>'normal','title'=>'一般订单'),array('entitle'=>'forecast','title'=>'预测订单'));
	//
	function autorun(){
		$this->baseurl='<a href="/s.php?">首页</a>';
		switch($this->kclass->input['action']){
			case 'list':
				return $this->mmlist();
			break;
			case 'find':
				return $this->find();
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
			case 'update':
				return $this->update();
			break;
			case 'doupdate':
				return $this->doupdate();
			break;
			case 'addTracking':
				return $this->addTracking();
			break;
			case 'insertTracking':
				return $this->insertTracking();
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
			case 'verify':
				$this->verify();
			break;
			case 'doverify':
				$this->doverify();
			case 'revise':
				$this->revise();
			break;
			default:
				return $this->mmlist();
		}
	}
	//
	function mmlist(){
		if($this->kclass->input['show']=='all'){
			$condition='1=1';
			}elseif($this->kclass->input['show']=='unlock'){
				$condition='`manufacture`.statusid=1';
			}elseif($this->kclass->input['show']=='locked'){
				$condition='`manufacture`.statusid=2';
			}elseif($this->kclass->input['show']=='inProduction'){
				$condition='`manufacture`.statusid=3';
			}elseif($this->kclass->input['show']=='Done'){
				$condition='`manufacture`.statusid=4';
		}else{
			$condition='`manufacture`.killed=0';
		}
		$keywords=$this->kclass->iif($this->kclass->input['keywords']=='',' AND 1=1',' AND `manufacture`.manufactureno like \'%'.$this->kclass->input['keywords'].'%\' OR `manufacture`.title like \'%'.$this->kclass->input['keywords'].'%\'');
		if($this->kclass->input['orderby']!='')$query['orderby']=$this->kclass->input['orderby'];
		if($this->kclass->input['direction']!='')$query['direction']=$this->kclass->input['direction'];
		if($this->kclass->input['show']!='')$query['show']=$this->kclass->input['show'];
		if($this->kclass->input['layout']!='')$query['layout']=$this->kclass->input['layout'];
		$manufactureby=$this->kclass->orderby(array('module'=>'manufacture','direction'=>'asc','orderby'=>'modified', 'default'=>'modified', 'serial'=>array(array('title'=>'编号', 'field'=>'manufactureno','word'=>'no'), array('title'=>'修改时间', 'field'=>'modified'), array('title'=>'建立时间', 'field'=>'created')),'appendUrl'=>$query));
		$manufactures=$this->kclass->DB->query("
			SELECT `manufacture`.manufactureid,`manufacture`.manufactureno,`manufacture`.statusid,`manufacture`.remark,`manufacture`.created,`manufacture`.modified,
				ms.title AS status,
				mt.title AS type,
				msi.title AS situation,
				u.username AS creator,
				us.username AS modifier
			FROM `manufacture` 
			LEFT JOIN `mftstatus` AS ms ON (ms.statusid=`manufacture`.statusid)
			LEFT JOIN `mfttype` AS mt ON (mt.typeid=`manufacture`.typeid)
			LEFT JOIN `mftsituation` AS msi ON (msi.situationid=`manufacture`.situationid)
			LEFT JOIN `user` AS u ON (u.userid=`manufacture`.creator)
			LEFT JOIN `user` AS us ON (us.userid=`manufacture`.modifier)
			WHERE `manufacture`.killed=0 AND ".$condition.$keywords."
			ORDER BY ".$manufactureby['sql']."
		");
		if($this->kclass->DB->numRows()){
			if($this->kclass->input['layout']=='grid'){
				$body='<ul id="mmlist" class="mmlist clear">';
			}else{
				$body='<table class="hundred"><thead><th width="15">ID</th><th width="30">生产制造单号</th><th width="100">紧急情况</th><th width="80">申请来源</th><th width="100">备注</th><th width="80">状态</th><th width="80">创建日期</th><th width="80">创建人</th><th width="80">操作</th></thead><tbody>';
			}
			$i=1;
			while($manufacture=$this->kclass->DB->fetchArray($manufactures)){
				$alarm='';
				$shipmentDate=date('Y-m-d',$manufacture['shipmentDate']);
				$paymentDate=date('Y-m-d',$manufacture['paymentDate']);
				$manufacture['created']=date('Y-m-d',$manufacture['created']);
				$statusid=$manufacture['statusid'];
				if($statusid==1){
					$alarm='';
				}elseif($statusid==2){
					$alarm='';
				}
				if($manufacture['killed']>0){
					$link='<a href="/s.php?module=manufacture&action=restore&manufactureid='.$manufacture['manufactureid'].'&rt=list">恢复</a>';
				}else{
					$link=$this->kclass->iif($statusid==1,'<a href="/s.php?module=manufacture&action=verify&manufactureid='.$manufacture['manufactureid'].'">审</a> <a href="/s.php?module=manufacture&action=update&manufactureid='.$manufacture['manufactureid'].'&rt=list">改</a>','').' <a href="/p.php?action=manufacture&manufactureid='.$manufacture['manufactureid'].'" target="_blank">印</a>';
				}
				if($this->kclass->input['layout']=='grid'){//表格
					$body .= '<li title="由 '.$manufacture['creator'].' 建于 '.$manufacture['created'].$this->kclass->iif($manufacture['modifier']!='', '，'.$manufacture['modifier'].' 改于 '.date('Y-m-d',$manufacture['modified']), '').'"'.$this->kclass->iif($i%4==0, ' class="end"', '').'>
					<div class="mmlistt">
						<span class="right normal">';
					if($manufacture['killed']>0){
						$body.='<a href="/s.php?module=manufacture&action=revival&manufactureid='.$manufacture['manufactureid'].'&rt=list" onclick="return confirm(\'你确定要恢复这个订单 '.$manufacture['manufactureno'].' 吗？\');">恢复</a>';
					}else{
						$body.='<a href="/s.php?module=manufacture&action=kill&manufactureid='.$manufacture['manufactureid'].'&rt=list" onclick="return confirm(\'你确定要删除这个订单 '.$manufacture['manufactureno'].' 吗？\');">删</a> '.$this->kclass->iif($statusid==1,'<a href="/s.php?module=manufacture&action=update&manufactureid='.$manufacture['manufactureid'].'&rt=list">改</a>','');
					}
					$body.='</span>
						 <a href="/s.php?module=manufacture&action=view&manufactureid='.$manufacture['manufactureid'].'">'.$manufacture['manufactureno'].'</a><span class="small">('.$manufacture['status'].')</span><br><span class="small gray right">'.$manufacture['created'].'</span>'.$this->kclass->iif($manufacture['attachs']>0, ' <span class="attachFile" title="有'.$manufacture['attachs'].'个附件。"></span> ', '').$this->kclass->iif($manufacture['images']>0, ' <span class="attachImage" title="有'.$manufacture['images'].'个图片。"></span> ', '').'<span class="small">'.$manufacture['manufactureid'].'</span></div>
					<div class="mmlistb">
						<div title="'.$manufacture['situation'].'"><span class="small gray">紧急情况：'.$manufacture['situation'].'</span></div>
						<div title="'.$manufacture['type'].'"><span class="small gray">申请来源：'.$manufacture['type'].'</span></div>
					<div title="'.$manufacture['remark'].'"><span class="small gray">备注：'.$manufacture['remark'].'</span></div>
						<div class="small clear"><span class=right title="由 '.$manufacture['creator'].' 建于 '.$manufacture['created'].'">由 '.$manufacture['creator'].' 建于 '.$manufacture['created'].'</span></div></div></li>';
				}else{
					$body .='<tr class="'.$this->kclass->rotateLine().' '.$alarm.'">
						<td>'.$i.'</td>
						<td><a href="/s.php?module=manufacture&action=view&manufactureid='.$manufacture['manufactureid'].'">'.$manufacture['manufactureno'].$this->kclass->iif($manufacture['attachs']>0, ' <span class="attachFile" title="有'.$manufacture['attachs'].'个附件。"></span> ', '').$this->kclass->iif($manufacture['images']>0, ' <span class="attachImage" title="有'.$manufacture['images'].'个图片。"></span> ', '').'</a></td>
						<td>'.$manufacture['situation'].'</td>
						<td>'.$manufacture['type'].'</td>
						<td title="'.$manufacture['title'].'">'.$manufacture['remark'].'</a></td>
						<td>'.$manufacture['status'].'</td>
						<td>'.$manufacture['created'].'</td>
						<td>'.$manufacture['creator'].'</td>
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
			$layoutLink='<a href="/s.php?module=manufacture&action=list&layout=list'.str_replace('&layout=grid', '', $queryPart).'">列表</a> 格子';
		}else{
			$layoutLink='列表 <a href="/s.php?module=manufacture&action=list&layout=grid'.str_replace('&layout=list', '', $queryPart).'">格子</a>';
		}
			$showLink='<a href="/s.php?module=manufacture&action=list">默认</a> <a href="/s.php?module=manufacture&action=list&show=all">所有<span class="small gray">(包括删除)</span></a> <a href="/s.php?module=manufacture&action=list&show=unlock">未锁定</a>  <a href="/s.php?module=manufacture&action=list&show=locked">已锁定</a>  <a href="/s.php?module=manufacture&action=list&show=inProduction">生产中</a>  <a href="/s.php?module=manufacture&action=list&show=Done">生产完成</a>';
		$this->kclass->page['title'].='生产制造单列表';
		$this->kclass->page['onload'].='dc.listhover();';
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - 生产制造单列表','right' => '<span class="small">排序：</span>'.$manufactureby['link'].'|　<span class="small">显示方式：</span>'.$showLink.'　|　<span class="small">布局：</span>'.$layoutLink.'　|　<a href="/s.php?module=manufacture&action=add">新建</a>','body'=>$body));
	}
		//
	function find(){
		if($this->kclass->input['keywords']==''){
$body=<<<EOF
<form method="get" action="/s.php?module=manufacture&action=list">
<input type="hidden" name="module" value="manufacture">
<input type="hidden" name="action" value="list">
<table>
<thead><tr><th colspan=2>查找订单：</th></tr></thead>
<tbody>
<tr><td>请输入查找关键词：</td><td><input type="text" name="keywords" style="width:350px"></td></tr>
<tr><td colspan=2 class="small gray">注意：请输入要查找订单的编号</td></tr>
<tr class="center"><td colspan=2><input type="submit" value=" 提交 "><input type="reset" value=" 重置 "></td></tr>
</tbody>
</table>
</form>
EOF;
		}
		$this->kclass->page['title'].='订单查找';
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - 订单查找','right' => '<a href="/s.php?module=manufacture">返回列表</a>','body'=>$body));
	}
	//
	function view(){
		$manufacture=$this->kclass->DB->queryFirst("
			SELECT m.*,
				ms.title AS situation,
				mt.title AS type,
				u.username AS creator,
				us.username AS modifier
			FROM `manufacture` AS m
			LEFT JOIN `mftsituation` AS ms ON (ms.situationid=m.situationid)
			LEFT JOIN `mfttype` AS mt ON (mt.typeid=m.typeid)
			LEFT JOIN `user` AS u ON (u.userid=m.creator)
			LEFT JOIN `user` AS us ON (us.userid=m.modifier)
			WHERE m.manufactureid='".$this->kclass->input['manufactureid']."'
			LIMIT 0,1
		");
		if($manufacture){
			$manufacture['created']=date('Y-m-d H:i',$manufacture['created']);
			if($manufacture['modified']!=0)$modify='，由'.$manufacture['modifier'].'于'.date('Y-m-d H:i',$manufacture['modified']).'修改';
			$items=$this->kclass->DB->query("
				SELECT mi.*,
					m.materialno,m.title AS material,m.standard,
					pm.pmaterialid,
					mv.title AS version,
					o.orderno,o.orderid
				FROM manufactureitem AS mi
				LEFT JOIN `material` AS m ON (m.materialid=mi.materialid)
				LEFT JOIN `pmaterial` AS pm ON (pm.materialid=mi.materialid)
				LEFT JOIN `materialversion` AS mv ON (mv.versionid=mi.versionid)
				LEFT JOIN `item` AS i ON (i.itemid=mi.orderitemid)
				LEFT JOIN `order` AS o ON (o.orderid=i.mid)
				WHERE mi.killed=0 AND mi.manufactureid=".$manufacture['manufactureid']."
				ORDER BY itemid ASC
			");
			if($this->kclass->DB->numRows()){
				$i=1;
				while($item=$this->kclass->DB->fetchArray($items)){
					$am=$this->kclass->getMaterialUrl(array('pmaterialid'=>$item['pmaterialid'],'productitemid'=>$item['productitemid'],'materialno'=>$item['materialno']));
					$itemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').' small">
						<td width="15">'.$i.'</td>
						<td width="100" class="center">'.$this->kclass->iif($item['orderid']>0,'<a href="/s.php?module=order&action=view&orderid='.$item['orderid'].'">'.$item['orderno'].'</a>','无').'</td>
						<td width="450">'.$am['murl'].' '.$item['material'].' '.$item['standard'].' '.$am['mvalue'].'</td>
						<td class="center">'.$item['version'].'</td>
						<td class="center">'.$item['quantity'].'</td>
						<td class="center">'.date('Y-m-d',$item['workdate']).'</td>
						<td class="center">'.date('Y-m-d',$item['finishdate']).'</td>
						<td>'.$item['remark'].'</td>
					</tr>';
					$i++;
				}
			}
			$body=<<<EOF
<div class="title"><span class="right small gray">由{$manufacture['creator']}于{$manufacture['created']}建立{$modify}{$overify}。</span>{$manufacture['manufactureno']}</div>
<dl id="order" class="tabs" style="display: block;">
	<dt tabid="0">生产制造单</dt>
	<dd>
<table class="hundred">
<thead><tr><th colspan="4">基本信息</th></tr></thead>
<tbody>
<tr class="even">
<td width="120">生产制造单编号：</td><td width="450"><span class="middle bold darkred">{$manufacture['manufactureno']}</span></td>
<td width="90">类　　型：<span class="red bold">*</span></td><td width="450">{$manufacture['type']}</td>
</tr>
<tr class="odd">
<td>情急情况：<span class="red bold">*</span></td><td>{$manufacture['situation']}</td>
<td></td>
<td></td>
</tr>
<tr class="even">
<td>备　　注：</td><td>{$manufacture['remark']}</td>
<td></td><td style="width:444px;height:111px;"></td>
</tr>
</tbody>
</table>
<table class="hundred">
<thead><tr><th colspan="9">生产制造单明细 <a href="/s.php?module=manufacture&action=update&manufactureid={$manufacture['manufactureid']}&step=item" class="right small">修改生产制造单明细</a></th></tr></thead>
<tbody>
<tr class="even center" nohover><td>ID</td><td>销售订单</td><td>物资</td><td>版本</td><td>数量</td><td>投产日期</td><td>完工日期</td><td>备注</td></tr>
{$itemtr}
<tr class="even">
<td class="small gray" colspan="9">
注意事项：<br>
	如果所列表格不够，那么在提交保存后再修改物资。<br>
	</tr>
</tbody>
</table>
</dd>
<dd>{$tracking}</dd>
<dd>{$reOrder['panel']}</dd>
<dd>{$invoice['panel']}</dd>
<dd></dd>
<dd></dd>
<dd></dd>
<dd>
<div class="gray block"><span class="right normal"><span class="small">新建：</span><a href="/s.php?module=accounting&action=add&manufactureid={$manufacture['manufactureid']}">应收</a>　|　<a href="/s.php?module=manufacture&action=update&manufactureid={$manufacture['manufactureid']}">修改</a></span></div>
</dd>
<dd>
<div class="gray block"><span class="right normal"><span class="small">新建：</span><a href="/s.php?module=accounting&action=add&manufactureid={$manufacture['manufactureid']}">已收</a>　|　<a href="/s.php?module=manufacture&action=update&manufactureid={$manufacture['manufactureid']}">修改</a></span></div>
</dd>
<dd>{$debitnote['panel']}</dd>
<dd>{$payment['panel']}</dd>
<dd>
</dd>
<dd>{$claim['panel']}</dd>
<dd>{$return['panel']}</dd>
<dd>
</dd>
EOF;
		}else{
			$body='数据错误，很抱歉！';
		}
		$this->kclass->page['title'].='生产制造单';
		$this->kclass->page['onload'].='dc.tabhover();dc.tabs({\'id\':\'order\'});dc.show()';
		$this->kclass->page['main']=$this->kclass->skin->dbody(array(
			'title'=>$this->baseurl.=' - <a href="/s.php?module=manufacture&action=list">生产制造单列表</a> - 查看生产制造单',
			'right' => $this->kclass->iif($manufacture['killed']==0,'<span class="small">打印：</span>
					<a href="/p.php?action=order&manufactureid='.$manufacture['manufactureid'].'" target="_blank">基本资料</a>
					<a href="/p.php?action=quoting&manufactureid='.$manufacture['manufactureid'].'" target="_blank">报价表</a>　|
				　<span class="small">新建：</span>
					<a href="/s.php?module=manufacture&action=add">生产制造单</a>　|
				　<a href="/s.php?module=manufacture&action=remove&manufactureid='.$manufacture['manufactureid'].'&rt=view">删除</a>　<a href="/s.php?module=manufacture&action=update&manufactureid='.$manufacture['manufactureid'].'&rt=view">修改</a>　|','<span class="small">操作：</span><a href="/s.php?module=manufacture&action=restore&manufactureid='.$manufacture['manufactureid'].'&rt=view">恢复</a>'),
			'body'=>$body));
	}
	//
	function add(){
		$start=strtotime(date('Y-m-d',TIMENOW));
		$end=strtotime(date('Y-m-d',$start))+86400;
		$counter=$this->kclass->DB->queryFirst("SELECT COUNT(*) AS count FROM `manufacture` WHERE created>='".$start."' AND created<='".$end."'");
		$manufactureno=$this->kclass->id(array('order'=>TIMENOW,'number'=>$counter['count']));
		if($this->kclass->input['mftType']==''){
			foreach($this->mftType as $key => $val){
				$select.='<option value="'.$val['entitle'].'">'.$val['title'].'</option>';
			}
			$body=<<<EOF
<form action="/s.php?module=manufacture&action=add" method="get">
<input type="hidden" name="module" value="manufacture">
<input type="hidden" name="action" value="add">
<table style="width:450px;">
<thead><tr><th colspan=2>新建生产制造单：选择订单来源类型</th></tr></thead>
<tbody>
<tr><td>选择订单来源类型</td><td><select name="mftType" style="width:250px;">{$select}</select></td></tr>
<tr class="center"><td colspan=2><input type="submit" value=" 提交 "><input type="reset" value=" 重置 "></td></tr>
</tbody>
</table>
EOF;
		}else{
			if($this->kclass->input['mftType']=='normal' AND intval($this->kclass->input['manufactureid'])==''){
				$orders=$this->kclass->DB->query("SELECT * FROM `order` WHERE killed=0 AND type='PO' ORDER BY created DESC");
				if($this->kclass->DB->numRows()){
					$i=1;
					while($order=$this->kclass->DB->fetchArray($orders)){
						//未投产订单（PO）
						$items=$this->kclass->DB->query("
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
							$itemtr.='<tbody><tr class="bold "><td colspan=15>订单(PO)编号：<a class="middle bold" href="/s.php?module=order&action=view&orderid='.$order['orderid'].'">'.$order['orderno'].'</a>　创建日期：'.date('Y-m-d',$order['created']).'　交货日期：'.date('Y-m-d',$order['shipmentDate']).'</td></tr><tr class="center even" nohover><td width="15">ID</td><td>产品或配件</td><td>品牌</td><td>包装</td><td>数量 <span class="red bold">*</span></td><td>备注</td><td>选</td></tr>';
							while($item=$this->kclass->DB->fetchArray($items)){
								$p=$this->kclass->DB->queryFirst("
										SELECT pi.productitemid,
										pm.pmaterialid,
										m.materialno,m.title AS material 
										FROM productitem AS pi
										LEFT JOIN material AS m ON (m.materialid=pi.materialid)
										LEFT JOIN pmaterial AS pm ON (pm.materialid=m.materialid)
										WHERE pi.killed=0 AND pi.productitemid='".$item['productitemid']."'
										");
								$am=$this->kclass->getMaterialUrl(array('pmaterialid'=>$p['pmaterialid'],'productitemid'=>$p['productitemid'],'materialno'=>$p['materialno']));
								$itemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').' small"><input type="hidden" name="itemOrderId['.$i.']" value="'.$item['mid'].'"><input type="hidden" name="itemId['.$i.']" value="'.$item['itemid'].'"><td>'.$k.'</td>
									<td width="500">'.$am['murl'].' '.$p['material'].' '.$am['mvalue'].'</td>
									<td class="center">'.$item['brand'].'</a></td>
									<td class="center">'.$item['packing'].'</td>
									<td class="center">'.$item['quantity'].' '.$item['unit'].'</td>
									<td>'.$item['remark'].'</td>
									<td class="center"><input type="checkbox" name="itemSelect['.$i.']" value="'.$item['itemid'].'"></td></tr>';
								$k++;$i++;
							}
						}
					}
					$itemtr.='';
				}
				$body=<<<EOF
<form action="/s.php?module=manufacture&action=insert" method="post">
<input type="hidden" name="module" value="manufacture" />
<input type="hidden" name="action" value="insert" />
<input type="hidden" name="manufactureno" value="{$manufactureno}" />
<input type="hidden" name="step" value="item" />
<input type="hidden" name="mftType" value="{$this->kclass->input['mftType']}" />
<table class="hundred"><thead><tr><th colspan=13>选择来源订单明细</th></tr></thead>
{$itemtr}
<tr class="center">
<td colspan=13><input type="submit" value="提交"><input type="reset" value="重置"></td>
</tr>
</tbody></table>
</form>
EOF;
			}elseif($this->kclass->input['mftType']=='forecast' OR ($this->kclass->input['mftType']=='normal' AND intval($this->kclass->input['manufactureid'])>0)){
				$situation=$this->kclass->chooserMftSituation(array('name'=>'situationid','width'=>200,'hasBlank'=>0,'selectedid'=>1));
				if($this->kclass->input['mftType']=='normal'){
					$manufactureid=$this->kclass->input['manufactureid'];
					$manufacture=$this->kclass->DB->queryFirst("SELECT * FROM manufacture WHERE killed=0 AND manufactureid='".$this->kclass->input['manufacture']."'");
					$type='一般订单';
					$items=$this->kclass->DB->query("
						SELECT mi.*,
							pm.pmaterialid,
							m.materialno,m.title AS material
						FROM `manufactureitem` AS mi
						LEFT JOIN `material` AS m ON (m.materialid=mi.materialid)
						LEFT JOIN pmaterial AS pm ON (pm.materialid=m.materialid)
						WHERE mi.manufactureid='".$this->kclass->input['manufactureid']."'
					");
					if($counter=$this->kclass->DB->numRows()){
						$i=1;
						while($item=$this->kclass->DB->fetchArray($items)){
							$am=$this->kclass->getMaterialUrl(array('pmaterialid'=>$item['pmaterialid'],'productitemid'=>$item['productitemid'],'materialno'=>$item['materialno']));
							$vstr ='<select name="itemVersionid['.$i.']" id="itemVersionid'.$i.'" style="width:80px;">';
							$vs = $this->kclass->DB->query("
									SELECT *
									FROM materialversion
									WHERE killed=0 AND productitemid='".$item['productitemid']."'
									ORDER BY created ASC
								");
							if($this->kclass->DB->numRows()){
								while($v=$this->kclass->DB->fetchArray($vs)){
								$vstr .= '<option value="'.$v['versionid'].'">'.$v['title'].'</option>';
								}
							}
							$vstr .= '</select>';
							$itemtr .= '<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').'">
							<input type="hidden" name="itemId['.$i.']" value="'.$item['itemid'].'">
							<td>'.$i.'</td>
							<td>'.$am['murl'].' '.$item['material'].' '.$am['mvalue'].'</td>
							<td>'.$vstr.'</td>
							<td><input type="text" name="itemQuantity['.$i.']" size="10" value="'.$item['quantity'].'"></td>
							<td><input type="text" name="itemWorkdate['.$i.']" id="workdate'.$i.'" size="15"></td>
							<td><input type="text" name="itemFinishdate['.$i.']" id="finishdate'.$i.'" size="15"></td>
							<td><input type="text" name="itemRemark['.$i.']" size="25" value="'.$item['remark'].'"></td>
							</tr>';
							$id.='#workdate'.$i.',';
							if($i<$counter){
								$id.='#finishdate'.$i.',';
							}else{
								$id.='#finishdate'.$i;
							}
							$i++;
						}
					}
				}elseif($this->kclass->input['mftType']=='forecast'){
					$type='预测订单';
					for($i=1; $i<6; $i++){
						$material=$this->kclass->chooserMaterial(array('hName'=>'itemMaterialid['.$i.']','hId'=>'itemMaterialid'.$i,'name'=>'itemMaterial['.$i.']','id'=>'itemMaterial'.$i,'width'=>300,'line'=>$i,'showVersion'=>1));
						$itemtr .= '<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').'">
						<td>'.$i.'</td>
						<td>'.$material.'</td>
						<td><select name="itemVersionid['.$i.']" id="itemVersionid'.$i.'" style="width:80px;"></select></td>
						<td><input type="text" name="itemQuantity['.$i.']" size="10"></td>
						<td><input type="text" name="itemWorkdate['.$i.']" id="workdate'.$i.'" size="15"></td>
						<td><input type="text" name="itemFinishdate['.$i.']" id="finishdate'.$i.'" size="15"></td>
						<td><input type="text" name="itemRemark['.$i.']" size="25"></td>
						</tr>';
						$id.='#workdate'.$i.',';
						if($i<6){
							$id.='#finishdate'.$i.',';
						}else{
							$id.='#finishdate'.$i;
						}
					}
				}

				$body=<<<EOF
<form action="/s.php?module=manufacture&action=insert" name="manufacture" method="post">
<input type="hidden" name="module" value="manufacture" />
<input type="hidden" name="action" value="insert" />
<input type="hidden" name="manufactureno" value="{$manufactureno}" />
<input type="hidden" name="manufactureid" value="{$manufactureid}" />
<input type="hidden" name="step" value="2" />
<input type="hidden" name="mftType" value="{$this->kclass->input['mftType']}" />
<table class="hundred">
<thead></tr><th colspan="4">填写基本信息</th></tr></thead>
<tbody>
<tr class="even">
<td>生产制造单编号：</td><td><span class="middle bold darkred">{$manufactureno}</span><span class="gray small">（预估）</span></td>
<td></td><td></td>
</tr>
<tr class="odd">
<td>生产制造单类型：<span class="red bold">*</span></td><td>{$type}</td>
<td>情急情况：<span class="red bold">*</span></td><td>{$situation}</td>
</tr>
<tr class="even">
<td>备　　注：</td><td><textarea name="remark" style="width:444px;height:111px;"></textarea></td>
<td></td><td style="width:444px;height:111px;"></td>
</tr>
</table>
<table class="hundred">
<thead>
<tr>
<th colspan="11">生产制造单明细表</th>
</tr>
<tr><th width="30">ID</th><th>产品或配件<span class="red bold">*</span></th><th>版本<span class="red bold">*</span></th><th>数量<span class="red bold">*</span></th><th>投产日期<span class="red bold">*</span></th><th>完工日期<span class="red bold">*</span></th><th>备注</th></tr>
</thead>
<tbody class="small">
{$itemtr}
<tr>
	<td colspan="11" class="gray small">注：①如行数不够，请保存后再进入修改页面。②至少有“名称”、“数量”、“单位”、等四个项目，该条明细才会被保存。</td>
</tr>
<table class="tlist tsuite" align="center" width="100%">
<tbody>
<tr class="odd" nohover>
<td align="center" colspan=4><input type="submit" id="submitButton" value="  提交  " accesskey="s"><input type="reset" value="  复位  "/></td>
</tr>
</tbody>
</table>
</form>
EOF;
			}
		}
		$this->kclass->page['title'].='新建生产制造单';
		$this->kclass->page['onload'].='dc.tabhover();$(\''.$id.'\').datepicker()';
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - <a href="/s.php?module=manufacture">生产制造单列表</a> - 新建生产制造单', 'right'=>'<a href="/s.php?module=manufacture&action=list">返回列表</a>','body'=>$body));
	}
	//
	function insert(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			if($this->kclass->input['step']=='item'){
				if($this->kclass->input['itemSelect']==''){
						$e.='<li>请从生产制造单中选择订单的明细</li>';
					}
			}else{
				if($this->kclass->input['situationid']==''){
					$e.='<li>请选择生产制造单中 情急情况 </li>';
				}
				if($this->kclass->input['mftType']=='forecast'){

				}elseif($this->kclass->input['mftType']=='normal'){

				}

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
		if($this->kclass->input['step']=='item'){
			$this->kclass->DB->query("
				INSERT INTO `manufacture` (manufactureno,typeid,created,creator)
				VALUES ('".$this->kclass->input['manufactureno']."',1,'".TIMENOW."','".$this->kclass->user['userid']."')
			");
			$manufactureid=$this->kclass->DB->insertID();
			$count=count($this->kclass->input['itemId']);
			for($i=1;$i<$count+1;$i++){
				if($this->kclass->input['itemSelect'][$i]>0){
					$item=$this->kclass->DB->queryFirst("
						SELECT i.productitemid,i.quantity,
							pi.materialid
						FROM `item` AS i
						LEFT JOIN productitem AS pi ON (pi.productitemid=i.productitemid)
						WHERE i.killed=0 AND i.itemid='".$this->kclass->input['itemSelect'][$i]."'
					");
					$this->kclass->DB->query("
						INSERT INTO `manufactureitem`
							(`manufactureid`,`materialid`,`versionid`,`productitemid`,`orderitemid`,`quantity`,`created`,`creator`)
						VALUES
							('".$manufactureid."','".$item['materialid']."',0,'".$item['productitemid']."','".$this->kclass->input['itemSelect'][$i]."','".$item['quantity']."','".TIMENOW."','".$this->kclass->user['userid']."')
					");
					$this->kclass->DB->query("UPDATE `item` SET ifChooser=1 WHERE itemid='".$this->kclass->input['itemSelect'][$i]."'");
				}
			}
			$this->kclass->messager(array(
				'title' => '新建生产制造单明细',
				'text' => '生产制造单 <b>'.$this->kclass->input['manufactureno'].'</b> 已新建成功!返回生产制造单详情页',
				'url' => '/s.php?module=manufacture&action=add&mftType='.$this->kclass->input['mftType'].'&manufactureid='.$manufactureid,
				'sec' => 2
			));
		}else{
			if($this->kclass->input['mftType']=='normal'){
				$manufactureid=$this->kclass->input['manufactureid'];
				$this->kclass->DB->query("
					UPDATE `manufacture` SET 
						`situationid`='".$this->kclass->input['situationid']."',
						`remark`='".$this->kclass->input['remark']."'
					WHERE manufactureid='".$this->kclass->input['manufactureid']."'
				");
				$counter=count($this->kclass->input['itemId']);
				for($i=1;$i<$counter+1;$i++){
					if($this->kclass->input['itemQuantity'][$i]!=''){
						$this->kclass->input['itemQuantity'][$i]=intVal($this->kclass->input['itemQuantity'][$i]);
						if($this->kclass->input['itemWorkdate'][$i]!=0){
							$sd1=explode('-', $this->kclass->input['itemWorkdate'][$i]);
							$workdate=mktime(0,0,0,$sd1[1],$sd1[2],$sd1[0]);
						}else{
							$workdate=0;
						}
						if($this->kclass->input['itemFinishdate'][$i]!=0){
							$sd2=explode('-', $this->kclass->input['itemFinishdate'][$i]);
							$finishdate=mktime(0,0,0,$sd2[1],$sd2[2],$sd2[0]);
						}else{
							$finishdate=0;
						}
					}
					$planperiod=$finishdate-$workdate;
					$this->kclass->DB->query("
						UPDATE `manufactureitem` SET
							`versionid`='".$this->kclass->input['itemVersionid'][$i]."',
							`quantity`='".$this->kclass->input['itemQuantity'][$i]."',
							`workdate`='".$workdate."',
							`finishdate`='".$finishdate."',
							`remark`='".$this->kclass->input['itemRemark'][$i]."'
						WHERE itemid='".$this->kclass->input['itemId'][$i]."'
					");
				}
			}elseif($this->kclass->input['mftType']=='forecast'){
				$this->kclass->DB->query("
					INSERT INTO `manufacture` (manufactureno,typeid,situationid,remark,created,creator)
					VALUES
						('".$this->kclass->input['manufactureno']."',2,'".$this->kclass->input['situationid']."','".$this->kclass->input['remark']."','".TIMENOW."','".$this->kclass->user['userid']."')
				");
				$manufactureid=$this->kclass->DB->insertID();
				$count=count($this->kclass->input['itemMaterialid']);
				for($n=1;$n<$count+1;$n++){
					$this->kclass->input['itemQuantity'][$n]=intVal($this->kclass->input['itemQuantity'][$n]);
					if($this->kclass->input['itemMaterialid'][$n]>0 AND $this->kclass->input['itemQuantity'][$n]>0){
						if($this->kclass->input['itemWorkdate'][$n]!=0){
							$sd2=explode('-', $this->kclass->input['itemWorkdate'][$n]);
							$workdate=mktime(0,0,0,$sd2[1],$sd2[2],$sd2[0]);
						}else{
							$workdate=0;
						}
						if($this->kclass->input['itemFinishdate'][$n]!=0){
							$sd2=explode('-', $this->kclass->input['itemFinishdate'][$n]);
							$finishdate=mktime(0,0,0,$sd2[1],$sd2[2],$sd2[0]);
						}else{
							$finishdate=0;
						}
						$planperiod=$finishdate-$workdate;
						$this->kclass->DB->query("
							INSERT INTO manufactureitem (manufactureid,materialid,versionid,productitemid,quantity,planperiod,workdate,finishdate,remark,created,creator)
							VALUES ('".$manufactureid."','".$this->kclass->input['itemMaterialid'][$n]."','".$this->kclass->input['itemVersionid'][$n]."','".$this->kclass->input['productItemid'][$n]."','".$this->kclass->input['itemQuantity'][$n]."','".$planperiod."','".$workdate."','".$finishdate."','".$this->kclass->input['itemRemark'][$n]."','".TIMENOW."','".$this->kclass->user['userid']."')
						");
					}
				}
			}
		}

		$this->kclass->messager(array(
			'title' => '新建生产制造单',
			'text' => '生产制造单 <b>'.$this->kclass->input['manufactureno'].'</b> 已新建成功!返回生产制造单详情页',
			'url' => '/s.php?module=manufacture&action=view&manufactureid='.$manufactureid,
			'sec' => 2
		));
	}

	//
	function update(){
		$manufacture=$this->kclass->DB->queryFirst("
			SELECT m.*,
				mt.title AS type
			FROM `manufacture` AS m
			LEFT JOIN `mfttype` AS mt ON (mt.typeid=m.typeid)
			WHERE m.killed=0 AND  m.manufactureid='".$this->kclass->input['manufactureid']."'
			LIMIT 0,1
		");
		if($manufacture){
			$items=$this->kclass->DB->query("
				SELECT i.*,
					m.materialid,m.materialno,m.title AS material,m.standard,
					pm.pmaterialid,
					mv.title AS version
				FROM manufactureitem AS i
				LEFT JOIN `material` AS m ON (m.materialid=i.materialid)
				LEFT JOIN `pmaterial` AS pm ON (pm.materialid=i.materialid)
				LEFT JOIN `materialversion` AS mv ON (mv.versionid=i.versionid)
				WHERE i.killed=0 AND i.manufactureid='".$manufacture['manufactureid']."'
				ORDER BY itemid ASC
			");
			if($this->kclass->input['step']=='item'){
				if($manufacture['typeid']==2){
					$module='manufacture';
					$itemtr='<table class="hundred"><thead></tr><th colspan="8">生产制造单明细列表</th></tr></thead><tbody><tr class="even"><td>ID</td><td>产品或配件<span class="red bold">*</span></td><td>版本<span class="red bold">*</span></td><td>数量<span class="red bold">*</span></td><td>投产日期<span class="red bold">*</span></td><td>完工日期<span class="red bold">*</span></td><td>备注</td><td>删</td></tr>';
					if($this->kclass->DB->numRows($items)){
						$k=1;
						while($item=$this->kclass->DB->fetchArray($items)){
							$am=$this->kclass->getMaterialUrl(array('pmaterialid'=>$item['pmaterialid'],'productitemid'=>$item['productitemid'],'materialno'=>$item['materialno']));
							$vstr ='<select name="itemVersionid['.$k.']" id="itemVersionid'.$k.'" style="width:80px;">';
							if($item['productitemid']==0){
								$con='AND materialid='.$item['materialid'];
							}else{
								$con='AND productitemid='.$item['productitemid'];
							}
							$vs = $this->kclass->DB->query("
									SELECT *
									FROM materialversion
									WHERE killed=0 ".$con."
									ORDER BY created ASC
								");
							if($this->kclass->DB->numRows()){
								while($v=$this->kclass->DB->fetchArray($vs)){
									if($item['versionid'] == $v['versionid']){
										$vstr .= '<option value="'.$v['versionid'].'" selected>'.$v['title'].'</option>';
									}else{
										$vstr .= '<option value="'.$v['versionid'].'">'.$v['title'].'</option>';
									}
								}
							}
							$vstr .= '</select>';
							$itemtr .= '<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').' small">
								<td>'.$k.'</td>
								<input type="hidden" name="itemId['.$k.']" value="'.$item['itemid'].'" />
								<input type="hidden" name="itemMaterialid['.$k.']" value="'.$item['materialid'].'" />
								<td>'.$am['murl'].' '.$item['material'].' '.$item['standard'].' '.$am['mvalue'].'</td>
								<td>'.$vstr.'</td>
								<td><input type="text" name="itemQuantity['.$k.']" size="10" value="'.$item['quantity'].'"></td>
								<td><input type="text" name="itemWorkdate['.$k.']" id="workdate'.$k.'" size="15" value="'.date('Y-m-d',$item['workdate']).'"></td>
								<td><input type="text" name="itemFinishdate['.$k.']" id="finishdate'.$k.'" size="15" value="'.date('Y-m-d',$item['finishdate']).'"></td>
								<td><input type="text" name="itemRemark['.$k.']" size="25" value="'.$item['remark'].'"></td>
								<td><input type="checkbox" name="itemKill['.$k.']" value="'.$item['itemid'].'"/></td>
								</tr>';
							$id.='#workdate'.$k.',';
							if($k<6){
								$id.='#finishdate'.$k.',';
							}else{
								$id.='#finishdate'.$k;
							}
							$k++;
						}
						$itemtr.='<tr><td colspan="8" class="bold">新建明细</td></tr>';
					}
					for($i=$k;$i<$k+5;$i++){
						$material=$this->kclass->chooserMaterial(array('hName'=>'itemMaterialid['.$i.']','hId'=>'itemMaterialid'.$i,'name'=>'itemMaterial['.$i.']','id'=>'itemMaterial'.$i,'width'=>400,'line'=>$i,'showVersion'=>1));
						$itemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').' small">
							<td>'.$i.'</td>
							<input type="hidden" name="itemId['.$i.']" value="'.$item['itemid'].'" />
							<td>'.$material.'</td>
							<td><select name="itemVersionid['.$i.']" id="itemVersionid'.$i.'" style="width:80px;"></select></td>
							<td><input type="text" name="itemQuantity['.$i.']" size="10" ></td>
							<td><input type="text" name="itemWorkdate['.$i.']" id="workdate'.$i.'" size="15" ></td>
							<td><input type="text" name="itemFinishdate['.$i.']" id="finishdate'.$i.'" size="15" ></td>
							<td><input type="text" name="itemRemark['.$i.']" size="25" ></td>
							<td></td></tr>';
						$id.='#workdate'.$i.',';
						if($i<$k+4){
							$id.='#finishdate'.$i.',';
						}else{
							$id.='#finishdate'.$i;
						}
					}
				}elseif($manufacture['typeid']==1){
					$k=1;
					$itemtr.='<table class="hundred"><thead><tr><th colspan="12">产品或配件明细</th></tr></thead><tbody><tr class="center"><td width="30">ID</td><td>物资 <span class="red bold">*</span></td><td>版本<span class="red bold">*</span></td><td width="80">数量<span class="red bold">*</span></td><td>投产日期<span class="red bold">*</span></td><td>完工日期<span class="red bold">*</span></td><td>备注</td><td>删</td></tr>';
					if($counter=$this->kclass->DB->numRows($items)){
						$this->kclass->tbline+=1;
						while($item=$this->kclass->DB->fetchArray($items)){
							$am=$this->kclass->getMaterialUrl(array('pmaterialid'=>$item['pmaterialid'],'productitemid'=>$item['productitemid'],'materialno'=>$item['materialno']));
							$vstr ='<select name="itemVersionid['.$k.']" id="itemVersionid'.$k.'" style="width:80px;">';
									$vs = $this->kclass->DB->query("
											SELECT *
											FROM materialversion
											WHERE killed=0 AND productitemid<>0 AND productitemid='".$item['productitemid']."'
											ORDER BY created ASC
										");
									if($this->kclass->DB->numRows()){
										while($v=$this->kclass->DB->fetchArray($vs)){
											if($item['versionid'] == $v['versionid']){
												$vstr .= '<option value="'.$v['versionid'].'" selected>'.$v['title'].'</option>';
											}else{
												$vstr .= '<option value="'.$v['versionid'].'">'.$v['title'].'</option>';
											}
										}
									}
									$vstr .= '</select>';
							$itemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').' small"><input type="hidden" name="itemId['.$k.']" value="'.$item['itemid'].'"><td>'.$k.'</td>
							<td>'.$am['murl'].' '.$item['material'].' '.$item['standard'].' '.$am['mvalue'].'</td>
							<td>'.$vstr.'</td>
							<td align="right"><input type="text" name="itemQuantity['.$k.']" value="'.$item['quantity'].'" /></td>
							<td><input type="text" name="itemWorkdate['.$k.']" id="workdate'.$k.'" size="15" value="'.date('Y-m-d',$item['workdate']).'"></td>
							<td><input type="text" name="itemFinishdate['.$k.']" id="finishdate'.$k.'" size="15" value="'.date('Y-m-d',$item['finishdate']).'"></td>
							<td><input type="text" name="itemRemark['.$k.']" size="25" value="'.$item['remark'].'"></td>
							<td><input type="checkbox" name="itemKill['.$k.']" value="'.$item['itemid'].'" /></td>
							</tr>';
							$id.='#workdate'.$k.',#finishdate'.$k.',';
							$k+=1;
						}
						$itemtr.='<tr><td colspan="15" class="bold">新建明细</td></tr>';
					}
					$orderItemCount=$this->kclass->DB->queryFirst("SELECT COUNT(*) AS count FROM `item` LEFT JOIN `order` ON (`order`.orderid=`item`.mid) WHERE `item`.killed=0 AND `item`.ifChooser=0 AND `item`.module='order' AND `order`.type='PO' LIMIT 0,1");
					$orders=$this->kclass->DB->query("SELECT * FROM `order` WHERE killed=0 AND type='PO' ORDER BY created DESC");
					if($this->kclass->DB->numRows($orders)){
						$i=1;$n=$k;
						while($order=$this->kclass->DB->fetchArray($orders)){
							//未投产订单（PO）
							$orderItems=$this->kclass->DB->query("
								SELECT `item`.itemid,`item`.quantity,`item`.mid,`item`.productitemid,
									m.materialno,m.title AS material,m.standard,
									pm.pmaterialid,
									pk.title AS packing,
									b.title AS brand,
									u.title AS unit
								FROM `item`
								LEFT JOIN productitem AS pi ON (pi.productitemid=`item`.productitemid)
								LEFT JOIN material AS m ON (m.materialid=pi.materialid)
								LEFT JOIN pmaterial AS pm ON (pm.materialid=m.materialid)
								LEFT JOIN packing AS pk ON (pk.packingid=`item`.packingid)
								LEFT JOIN brand AS b ON (b.brandid=`item`.brandid)
								LEFT JOIN `unit` AS u ON (u.unitid=`item`.unitid)
								WHERE `item`.killed=0 AND `item`.ifChooser=0 AND `item`.module='order' AND `item`.mid='".$order['orderid']."'
								ORDER BY `item`.itemid ASC
							");
							if($counter2=$this->kclass->DB->numRows($orderItems)){
								$j=1;
								$itemtr.='<tbody><tr class="bold"><td colspan=15>订单(PO)编号：<a class="middle bold" href="/s.php?module=order&action=view&orderid='.$order['orderid'].'">'.$order['orderno'].'</a>　创建日期：'.date('Y-m-d',$order['created']).'　交货日期：'.date('Y-m-d',$order['shipmentDate']).'</td></tr><tr class="even" nohover><td width="30">ID</td><td>物资 <span class="red bold">*</span></td><td width="80">版本<span class="red bold">*</span></td><td width="80">数量<span class="red bold">*</span></td><td>投产日期<span class="red bold">*</span></td><td>完工日期<span class="red bold">*</span></td><td>备注</td><td>选</td></tr>';
								while($orderItem=$this->kclass->DB->fetchArray($orderItems)){
									$am=$this->kclass->getMaterialUrl(array('pmaterialid'=>$orderItem['pmaterialid'],'productitemid'=>$orderItem['productitemid'],'materialno'=>$orderItem['materialno']));
									$vstr ='<select name="itemVersionid['.$i.']" id="itemVersionid'.$i.'" style="width:80px;">';
									$vs = $this->kclass->DB->query("
											SELECT *
											FROM materialversion
											WHERE killed=0 AND productitemid<>0 AND productitemid='".$orderItem['productitemid']."'
											ORDER BY created ASC
										");
									if($this->kclass->DB->numRows()){
										while($v=$this->kclass->DB->fetchArray($vs)){
										$vstr .= '<option value="'.$v['versionid'].'">'.$v['title'].'</option>';
										}
									}
									$vstr .= '</select>';
									$itemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').' small"><input type="hidden" name="olOrderId['.$i.']" value="'.$orderItem['mid'].'"><input type="hidden" name="olItemId['.$i.']" value="'.$orderItem['itemid'].'"><td>'.$j.'</td>
										<td>'.$am['murl'].' '.$orderItem['material'].' '.$orderItem['standard'].' '.$am['mvalue'].'</td>
										<td>'.$vstr.'</td>
										<td align="right"><input type="text" name="olQuantity['.$i.']" value="'.$orderItem['quantity'].'" /></td>
										<td><input type="text" name="olWorkdate['.$i.']" id="workdate'.$n.'" size="15"></td>
										<td><input type="text" name="olFinishdate['.$i.']" id="finishdate'.$n.'" size="15" ></td>
										<td><input type="text" name="olRemark['.$i.']" size="25"></td>
										<td><input type="checkbox" name="olSelect['.$i.']" value="'.$orderItem['itemid'].'"></td></tr>';
									$id.='#workdate'.$n.',';
									if($i<$orderItemCount['count']){
										$id.='#finishdate'.$n.',';
									}else{
										$id.='#finishdate'.$n;
									}
									$j++;$i++;$n++;
								}
							}
						}
						$itemtr.='';
					}
					$module='forecast';
				}
				$itemtr.='<tr class="even"><td class="small gray" colspan="15">注意事项：<br>“日期”的格式为2012-02-06，中间用半角短横杠隔开；<br></tr></tbody></table>';
				$body=<<<EOF
<form action="/s.php?module=manufacture&action=doupdate" name="manufacture" method="post">
<input type="hidden" name="module" value="manufacture">
<input type="hidden" name="action" value="doupdate">
<input type="hidden" name="step" value="item">
<input type="hidden" name="typeid" value="{$manufacture['typeid']}">
<input type="hidden" name="manufactureid" value="{$manufacture['manufactureid']}">
<input type="hidden" name="manufactureno" value="{$manufacture['manufactureno']}">
{$itemtr}
<table class="tlist tsuite" align="center" width="100%">
<tbody>
<tr class="odd" nohover>
<td align="center" colspan=4><input type="submit" id="submitButton" value="  提交  " accesskey="s"><input type="reset" value="  复位  "/></td>
</tr>
</tbody>
</table>
</form>
EOF;

			}else{
				$situation=$this->kclass->chooserMftSituation(array('name'=>'situationid','width'=>200,'hasBlank'=>0,'selectedid'=>1));
				$body=<<<EOF
<form action="/s.php?module=manufacture&action=doupdate" name="manufacture" method="post">
<input type="hidden" name="module" value="manufacture" />
<input type="hidden" name="action" value="doupdate" />
<input type="hidden" name="manufactureid" value="{$manufacture['manufactureid']}" />
<input type="hidden" name="manufactureno" value="{$manufacture['manufactureno']}" />
<table class="hundred">
<thead></tr><th colspan="4">填写基本信息</th></tr></thead>
<tbody>
<tr class="even">
<td>生产制造单编号：</td><td><span class="middle bold darkred">{$manufacture['manufactureno']}</span><span class="gray small">（编号无法更改）</span></td>
<td>生产制造单类型：<span class="red bold">*</span></td><td>{$manufacture['type']}</td>
</tr>
<tr class="odd">
<td>情急情况：<span class="red bold">*</span></td><td>{$situation}</td>
<td></td>
<td></td>
</tr>
<tr class="even">
<td>备注：</td><td><textarea name="remark" style="width:444px;height:111px;"></textarea></td>
<td></td><td style="width:444px;height:111px;"></td>
</tr>
<tr class="odd" nohover>
<td align="center" colspan=4><input type="submit" id="submitButton" value="  提交  " accesskey="s"><input type="reset" value="  复位  "/></td>
</tr>
</tbody>
</table>
</form>
EOF;
			}
		}else{
			$body = '系统数据出错，很抱歉！';
		}
		$this->kclass->page['title'] .= $manufacture['manufactureno'].' - 修改生产制造单明细';
		$this->kclass->page['onload'].='dc.tabhover();$(\''.$id.',#paymentDate\').datepicker()';
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - <a href="/s.php?module=manufacture&action=view&manufactureid='.$manufacture['manufactureid'].'">查看订单</a> - 修改订单', 'right'=>'<a href="/s.php?module=manufacture&action=view&manufactureid='.$manufacture['manufactureid'].'">查看订单</a>　｜　<a href="/s.php?module=manufacture&action=list">返回列表</a>　','body'=>$body));
	}

	//
	function doupdate(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			if($this->kclass->input['step']=='item'){

			}else{
				if($this->kclass->input['situationid'] <= 0){
					$e .= '<li>请选择订单中的 情急情况。</li>';
				}
			}
		}
		if($e){
			$this->kclass->messager(array(
				'title' => '修改生产制造单',
				'text' => '您在修改生产制造单的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		if($this->kclass->input['step']=='item'){
			if($this->kclass->input['typeid']==2){
				$counter1=count($this->kclass->input['itemMaterialid']);
				for($i=1;$i<$counter1;$i++){
					$this->kclass->input['itemQuantity'][$i]=intVal($this->kclass->input['itemQuantity'][$i]);
					if($this->kclass->input['itemWorkdate'][$i]!=0){
						$sd1=explode('-', $this->kclass->input['itemWorkdate'][$i]);
						$workdate=mktime(0,0,0,$sd1[1],$sd1[2],$sd1[0]);
					}else{
						$workdate=0;
					}
					if($this->kclass->input['itemFinishdate'][$i]!=0){
						$sd2=explode('-', $this->kclass->input['itemFinishdate'][$i]);
						$finishdate=mktime(0,0,0,$sd2[1],$sd2[2],$sd2[0]);
					}else{
						$finishdate=0;
					}
					$planperiod=$finishdate-$workdate;
					if($this->kclass->input['itemId'][$i]>0){
						if($this->kclass->input['itemKill'][$i]>0 AND $this->kclass->input['itemKill'][$i]==$this->kclass->input['itemId'][$i]){
							$this->kclass->DB->query("UPDATE `manufactureitem` SET killed='".TIMENOW."',killer='".$this->kclass->user['userid']."' WHERE itemid='".$this->kclass->input['itemId'][$i]."'");
						}else{
							if($this->kclass->input['itemMaterialid'][$i]>0 AND $this->kclass->input['itemQuantity'][$i]>0 AND $this->kclass->input['itemWorkdate'][$i]!='' AND $this->kclass->input['itemFinishdate'][$i]!=''){
								$this->kclass->DB->query("
									UPDATE `manufactureitem` SET
										`versionid`='".$this->kclass->input['itemVersionid'][$i]."',
										`quantity`='".$this->kclass->input['itemQuantity'][$i]."',
										`workdate`='".$workdate."',
										`finishdate`='".$finishdate."',
										`planperiod`='".$planperiod."',
										`remark`='".$this->kclass->input['itemRemark'][$i]."',
										`modifier`='".$this->kclass->user['userid']."',
										`modified`='".TIMENOW."'
									WHERE itemid='".$this->kclass->input['itemId'][$i]."'
								");
							}
						}
					}else{
						if($this->kclass->input['itemMaterialid'][$i]>0 AND $this->kclass->input['itemVersionid'][$i]>0  AND $this->kclass->input['itemQuantity'][$i]>0 AND $this->kclass->input['itemWorkdate'][$i]!='' AND $this->kclass->input['itemFinishdate'][$i]!=''){
							$this->kclass->DB->query("
								INSERT INTO manufactureitem (manufactureid,materialid,versionid,productitemid,quantity,planperiod,workdate,finishdate,remark,created,creator)
								VALUES ('".$this->kclass->input['manufactureid']."','".$this->kclass->input['itemMaterialid'][$i]."','".$this->kclass->input['itemVersionid'][$i]."','".$this->kclass->input['productItemid'][$i]."','".$this->kclass->input['itemQuantity'][$i]."','".$planperiod."','".$workdate."','".$finishdate."','".$this->kclass->input['itemRemark'][$i]."','".TIMENOW."','".$this->kclass->user['userid']."')
							");
						}
					}
				}
			}elseif($this->kclass->input['typeid']==1){
				$counter1=count($this->kclass->input['itemId']);
				for($i=1;$i<$counter1+1;$i++){
					if($this->kclass->input['itemKill'][$i]!='' AND $this->kclass->input['itemKill'][$i]=$this->kclass->input['itemId'][$i]){
						$item=$this->kclass->DB->queryFirst("SELECT orderitemid FROM `manufactureitem` WHERE itemid='".$this->kclass->input['itemKill'][$i]."'");
						$this->kclass->DB->query("UPDATE `manufactureitem` SET killed='".TIMENOW."',killer='".$this->kclass->user['userid']."' WHERE itemid='".$this->kclass->input['itemKill'][$i]."'");
						$this->kclass->DB->query("UPDATE `item` SET ifChooser=0 WHERE itemid='".$item['orderitemid']."' ");
					}else{
						$this->kclass->input['itemQuantity'][$i]=intVal($this->kclass->input['itemQuantity'][$i]);
						if($this->kclass->input['itemWorkdate'][$i]!=0){
							$sd1=explode('-', $this->kclass->input['itemWorkdate'][$i]);
							$workdate=mktime(0,0,0,$sd1[1],$sd1[2],$sd1[0]);
						}else{
							$workdate=0;
						}
						if($this->kclass->input['itemFinishdate'][$i]!=0){
							$sd2=explode('-', $this->kclass->input['itemFinishdate'][$i]);
							$finishdate=mktime(0,0,0,$sd2[1],$sd2[2],$sd2[0]);
						}else{
							$finishdate=0;
						}
						$planperiod=$finishdate-$workdate;
						$this->kclass->DB->query("
							UPDATE `manufactureitem` SET 
								`versionid`='".$this->kclass->input['itemVersionid'][$i]."',
								`quantity`='".$this->kclass->input['itemQuantity'][$i]."',
								`workdate`='".$workdate."',
								`finishdate`='".$finishdate."',
								`planperiod`='".$planperiod."',
								`remark`='".$this->kclass->input['itemRemark'][$i]."',
								`modifier`='".$this->kclass->user['userid']."',
								`modified`='".TIMENOW."'
							WHERE itemid='".$this->kclass->input['itemId'][$i]."'
						");
					}
				}
				$counter2=count($this->kclass->input['olItemId']);
				for($m=1;$m<$counter2+1;$m++){
					if($this->kclass->input['olSelect'][$m]!='' AND $this->kclass->input['olQuantity'][$m]>0 AND $this->kclass->input['olWorkdate'][$m]>0 AND $this->kclass->input['olFinishdate'][$m]>0){
						$this->kclass->input['olQuantity'][$m]=intVal($this->kclass->input['olQuantity'][$m]);
						if($this->kclass->input['olWorkdate'][$m]!=0){
							$sd1=explode('-', $this->kclass->input['olWorkdate'][$m]);
							$workdate=mktime(0,0,0,$sd1[1],$sd1[2],$sd1[0]);
						}else{
							$workdate=0;
						}
						if($this->kclass->input['olFinishdate'][$m]!=0){
							$sd2=explode('-', $this->kclass->input['olFinishdate'][$m]);
							$finishdate=mktime(0,0,0,$sd2[1],$sd2[2],$sd2[0]);
						}else{
							$finishdate=0;
						}
						$planperiod=$finishdate-$workdate;
						$item=$this->kclass->DB->queryFirst("
						SELECT i.productitemid,i.quantity,
							pi.materialid
						FROM `item` AS i
						LEFT JOIN productitem AS pi ON (pi.productitemid=i.productitemid)
						WHERE i.killed=0 AND i.itemid='".$this->kclass->input['olSelect'][$m]."'
					");
					$this->kclass->DB->query("
						INSERT INTO `manufactureitem`
							(`manufactureid`,`materialid`,`versionid`,`productitemid`,`orderItemid`,`quantity`,`workdate`,`finishdate`,`planperiod`,`remark`,`created`,`creator`)
						VALUES
							('".$this->kclass->input['manufactureid']."','".$item['materialid']."','".$this->kclass->input['itemVersionid'][$m]."','".$item['productitemid']."','".$this->kclass->input['olSelect'][$m]."','".$this->kclass->input['olQuantity'][$m]."','".$workdate."','".$finishdate."','".$planperiod."','".$this->kclass->input['olRemark'][$m]."','".TIMENOW."','".$this->kclass->user['userid']."')
					");
						$this->kclass->DB->query("UPDATE `item` SET ifChooser=1 WHERE itemid='".$this->kclass->input['olSelect'][$m]."'");
					}
				}
			}
			

			$this->kclass->messager(array(
				'title' => '修改生产制造单明细',
				'text' => '生产制造单 <b>'.$this->kclass->input['manufactureno'].'</b> 已修改成功!返回生产制造单详情页',
				'url' => '/s.php?module=manufacture&action=view&manufactureid='.$this->kclass->input['manufactureid'],
				'sec' => 2
			));

		}else{
			$this->kclass->DB->query("
				UPDATE `manufacture` SET
					`situationid`='".$this->kclass->input['situationid']."',
					`remark`='".$this->kclass->input['remark']."',
					`modified` = '".TIMENOW."',
					`modifier` = '".$this->kclass->user['userid']."'
				WHERE manufactureid='".$manufacture['manufactureid']."'
			");
			$this->kclass->messager(array(
				'title' => '修改生产制造单',
				'text' => '生产制造单 <b>'.$this->kclass->input['manufactureno'].'</b> 已修改成功!返回生产制造单详情页',
				'url' => '/s.php?module=manufacture&action=view&manufactureid='.$this->kclass->input['manufactureid'],
				'sec' => 2
			));
		}


	}
	//
	function kill(){
		if($this->kclass->input['manufactureid']<=0){
			$this->kclass->boinkIt('/s.php?module=manufacture');
		}
		if($this->kclass->input['rt']!='list' AND $this->kclass->input['confirm'] == 0){
			$this->kclass->boinkIt('/s.php?module=manufacture&action=view&manufactureid='.$this->kclass->input['manufactureid']);
		}
		if($this->kclass->input['manufactureid'] < 0){
			$e = '<li>请从正确的页面进行操作，如果此问题持续存在，请联系网站管理员。</li>';
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '删除生产制造单',
				'text' => '您在删除生产制造单的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		if($this->kclass->input['rt']=='view'){
			$rt = '&action=view&manufactureid='.$this->kclass->input['manufactureid'];
		}elseif($this->kclass->input['rt']=='list'){
			$rt = '#'.$this->kclass->input['manufactureid'];
		}
		$manufacture=$this->kclass->DB->queryFirst("
			SELECT manufactureno
			FROM `manufacture`
			WHERE manufactureid='".$this->kclass->input['manufactureid']."'
		");
		if($manufacture){
			$this->kclass->DB->query("
				UPDATE `manufacture`
				SET killed=".TIMENOW.",killer='".$this->kclass->user['userid']."'
				WHERE manufactureid='".$this->kclass->input['manufactureid']."'
			");
			$this->kclass->messager(array(
				'title' => '删除生产制造单成功',
				'text' => '生产制造单 <b>'.$manufacture['manufactureno'].'</b> 已成功被标记为删除!',
				'url' => '/s.php?module=manufacture'.$rt,
				'sec' => 2
			));
		}else{
			$this->kclass->messager(array(
				'title' => '删除生产制造单失败',
				'text' => '您要删除的生产制造单，不存在！',
				'url' => '/s.php?module=manufacture'.$rt,
				'sec' => 3
			));
		}
	}

	//
	function remove(){
		if($this->kclass->input['manufactureid']<=0){
			$this->kclass->boinkIt('/s.php?module=manufacture');
		}
		$manufacture = $this->kclass->DB->queryFirst("
			SELECT manufactureno
			FROM `manufacture`
			WHERE manufactureid='".$this->kclass->input['manufactureid']."'
		");
$body = <<<EOF
<form action="/s.php?module=manufacture&action=kill" name="order" method="post">
<input type="hidden" name="module" value="manufacture">
<input type="hidden" name="action" value="kill">
<input type="hidden" name="manufactureid" value="{$this->kclass->input['manufactureid']}">
<input type="hidden" name="rt" value="{$this->kclass->input['rt']}">
<table><thead>
<thead>
<tr>
	<th>删除生产制造单：{$manufacture['manufactureno']}</th>
</tr>
</thead>
<tbody>
<tr class="even">
	<td class="middle">你确定要删除生产制造单: <a href="/s.php?module=manufacture&action=view&manufactureid={$this->kclass->input['manufactureid']}" class="big bold" target="_blank">{$manufacture['manufactureno']}</a> 吗?</td>
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
		$this->kclass->page['title'] .= ' - 删除 - '.$manufacture['manufactureno'];
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 删除生产制造单 - '.$manufacture['manufactureno'], 'right' => '<a href="/s.php?module=manufacture">返回列表</a>', 'body'=>$body));
	}

	//
	function revival(){
		if($this->kclass->input['manufactureid']<=0){
			$this->kclass->boinkIt('/s.php?module=manufacture');
		}
		if($this->kclass->input['rt']!='list' AND $this->kclass->input['confirm'] == 0){
			$this->kclass->boinkIt('/s.php?module=manufacture&action=view&manufactureid='.$this->kclass->input['manufactureid']);
		}
		if($this->kclass->input['manufactureid'] < 0){
			$e = '<li>请从正确的页面进行操作，如果此问题持续存在，请联系网站管理员。</li>';
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '恢复生产制造单',
				'text' => '您在恢复生产制造单的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$manufacture = $this->kclass->DB->queryFirst("
			SELECT manufactureno
			FROM `manufacture`
			WHERE manufactureid='".$this->kclass->input['manufactureid']."'
		");
		if($this->kclass->input['rt']=='view'){
			$rt = '&action=view&manufactureid='.$this->kclass->input['manufactureid'];
		}elseif($this->kclass->input['rt']=='list'){
			$rt = '#'.$this->kclass->input['manufactureid'];
		}
		if($manufacture){
			$this->kclass->DB->query("
				UPDATE `manufacture`
				SET killed=0,killer=0
				WHERE manufactureid='".$this->kclass->input['manufactureid']."'
			");
			$this->kclass->messager(array(
				'title' => '恢复生产制造单成功',
				'text' => '生产制造单 <b>'.$manufacture['manufactureno'].'</b> 已成功被恢复！',
				'url' => '/s.php?module=manufacture'.$rt,
				'sec' => 2
			));
		}else{
			$this->kclass->messager(array(
				'title' => '恢复生产制造单失败',
				'text' => '您要恢复的生产制造单不存在！',
				'url' => '/s.php?module=manufacture'.$rt,
				'sec' => 3
			));
		}
	}

	//
	function restore(){
		if($this->kclass->input['manufactureid']<=0){
			$this->kclass->boinkIt('/s.php?module=manufacture');
		}
		$manufacture = $this->kclass->DB->queryFirst("
			SELECT manufactureno
			FROM `manufacture`
			WHERE manufactureid='".$this->kclass->input['manufactureid']."'
		");
$body = <<<EOF
<form action="/s.php?module=manufacture&action=revival" name="manufacture" method="post">
<input type="hidden" name="module" value="manufacture">
<input type="hidden" name="action" value="revival">
<input type="hidden" name="manufactureid" value="{$this->kclass->input['manufactureid']}">
<input type="hidden" name="rt" value="{$this->kclass->input['rt']}">
<table><thead>
<thead>
<tr>
	<th>恢复生产制造单：{$manufacture['manufactureno']}</th>
</tr>
</thead>
<tbody>
<tr class="even">
	<td class="middle">你确定要恢复生产制造单: <a href="/s.php?module=manufacture&action=view&manufactureid={$this->kclass->input['manufactureid']}" class="big bold" target="_blank">{$manufacture['manufactureno']}</a> 吗?</td>
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
		$this->kclass->page['title'] .= ' - 恢复 - '.$manufacture['manufactureno'];
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 恢复生产制造单 - '.$manufacture['manufactureno'], 'right' => '<a href="/s.php?module=manufacture">返回列表</a>', 'body'=>$body));
	}
	//
	function verify(){
		$manufacture=$this->kclass->DB->queryFirst("
			SELECT o.manufactureid,o.supplierid,o.customerNo,o.supplierNo,o.orderno,o.type,o.mftType,o.shipmentDate,o.paymentDate,o.packing,
					o.labeling,o.taxRate,o.special,o.remark,o.modified,o.created,o.killed,
				it.title AS invoiceType,
				s.title AS supplier,s.supplierno,
				c.title AS currency,c.symbol AS encurrency,
				lo.title AS loading,lo.entitle AS enloading,
				dis.title AS discharge,dis.entitle AS endischarge,
				sh.title AS shipmethod,sh.entitle AS enshipmethod,
				p.title AS paymentterm,p.entitle AS enpaymentterm,
				d.title AS deliveryterm,d.entitle AS endeliveryterm,
				u.username AS creator,
				us.username AS modifier
			FROM `manufacture` AS o
			LEFT JOIN `orderroute` AS od ON (od.routeid=o.routeid)
			LEFT JOIN `pinvoicetype` AS it ON (it.pinvoiceTypeid=o.pinvoiceTypeid)
			LEFT JOIN `supplier` AS s ON (s.supplierid=o.supplierid)
			LEFT JOIN `customer` AS cu ON (cu.customerid=o.customerid)
			LEFT JOIN `currency` AS c ON (c.currencyid=o.currencyid)
			LEFT JOIN `port` AS lo ON (lo.portid=o.loading)
			LEFT JOIN `port` AS dis ON (dis.portid=o.discharge)
			LEFT JOIN `shipmethod` AS sh ON (sh.shipmethodid=o.shipmethodid)
			LEFT JOIN `paymentterm` AS p ON (p.paymenttermid=o.paymenttermid)
			LEFT JOIN `deliveryterm` AS d ON (d.deliverytermid=o.deliverytermid)
			LEFT JOIN `user` AS u ON (u.userid=o.creator)
			LEFT JOIN `user` AS us ON (us.userid=o.modifier)
			WHERE o.routeid=4 AND o.type='PO' AND o.manufactureid='".$this->kclass->input['manufactureid']."'
			LIMIT 0,1
		");
		if(!$purchase AND $purchase['ifVerify']!=0){
			$e='<li>数据错误，很抱歉~</li>';
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '审核订单',
				'text' => '审核订单<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$manufacture['taxRate']=($manufacture['taxRate']*100).'%';
		$manufacture['shipmentDate']=date('Y-m-d',$manufacture['shipmentDate']);
		$manufacture['paymentDate']=date('Y-m-d',$manufacture['paymentDate']);
		foreach($this->mftType AS $key=>$val){
			if($manufacture['mftType']==$val['entitle']){
				$mftType=$val['title'];
			}
		}
		// 订单明细
		if($manufacture['mftType']=='normal'){
			$items=$this->kclass->DB->query("
				SELECT i.itemid,i.mid AS manufactureid,i.materialid,i.quantity,i.package,i.price,i.amount,i.discount,
					m.materialno,m.title,m.standard,
					u.title AS unit,
					br.title AS brand,
					pu.purchaseid,pu.purchaseno
				FROM item AS i
				LEFT JOIN purchase AS p ON (p.purchaseid=i.purchaseid)
				LEFT JOIN material AS m ON (m.materialid=i.materialid)
				LEFT JOIN unit AS u ON (u.unitid=i.unitid)
				LEFT JOIN brand AS br ON (br.brandid=i.brandid)
				LEFT JOIN purchase AS pu ON (pu.purchaseid=i.purchaseid)
				WHERE i.killed=0 AND i.module='manufacture' AND i.mid='".$manufacture['manufactureid']."'
				ORDER BY itemid
			");
			if($this->kclass->DB->numRows()){
				$i=1;
				$manufactureItem.='<table class="hundred small">
					<thead></tr><th colspan="13">订单明细 </th></tr></thead>
					<tbody><tr class="center">
						<td width="30">ID</td><td>采购单编号</td><td>物资编号</td><td>物资名称</td><td>规格</td><td>品牌</td><td>包装方式</td><td align="right">数量/单位</td><td>折扣</td><td>单价</td><td>总价</td><td>备注</td>
					</tr>';
				while($item=$this->kclass->DB->fetchArray($items)){
					$total+=$item['amount'];
					$barcode=$this->kclass->DB->queryFirst("SELECT barcodeid FROM barcode WHERE materialid='".$item['materialid']."' AND manufactureid='".$item['manufactureid']."'");
					$addBarcode=$this->kclass->iif($barcode,'<a href="/s.php?module=barcode&action=view&barcodeid='.$barcode['barcodeid'].'">查看条码</a>','<a href="/s.php?module=barcode&action=add&manufactureid='.$item['manufactureid'].'&itemid='.$item['itemid'].'">生成条码</a>');
					$manufactureItem.='<tr class="'.$this->kclass->rotateLine().'">
						<td>'.$i.'</td>
						<td><a href="/s.php?module=purchase&action=view&purchaseid='.$item['purchaseid'].'">'.$item['purchaseno'].'</a></td>
						<td><a href="/s.php?module=material&action=view&materialid='.$item['materialid'].'">'.$item['materialno'].'</a></td>
						<td>'.$item['title'].'</td>
						<td>'.$item['standard'].'</td>
						<td>'.$item['brand'].'</td>
						<td>'.$item['package'].'</td>
						<td align="right">'.number_format($item['quantity']).' '.$item['unit'].'</td>
						<td align="right">'.$this->kclass->iif($item['discount']>0, $item['discount'].'%', '').'</td>
						<td>'.number_format($item['price'],2).'</td>
						<td>'.number_format($item['amount'],2).'</td>
						<td>'.$item['remark'].'</td></tr>';
					$i++;
				}
				$total=number_format($total, 2);
				$cntotal=$this->kclass->numberToCNAccount($total);
				$manufactureItem.='</tbody></table>';
			}
		}elseif($manufacture['mftType']=='forecast'){
			$items=$this->kclass->DB->query("
				SELECT i.itemid,i.mid AS manufactureid,i.materialid,i.quantity,i.package,i.price,i.amount,i.discount,i.requirement,
					m.materialno,m.title,m.standard,
					u.title AS unit,
					en.forecastid,en.forecastno
				FROM item AS i
				LEFT JOIN material AS m ON (m.materialid=i.materialid)
				LEFT JOIN unit AS u ON (u.unitid=i.unitid)
				LEFT JOIN forecast AS en ON (en.forecastid=i.mid)
				WHERE i.killed=0 AND i.module='forecast' AND i.manufactureid='".$manufacture['manufactureid']."'
				ORDER BY itemid
			");
			if($this->kclass->DB->numRows()){
				$manufactureItem.='<table class="hundred small">
					<thead></tr><th colspan="12">给付物资明细 </th></tr></thead><tbody><tr class="center"><td>外发加工单编号</td><td>物资编号</td><td>物资名称</td><td>规格</td><td>包装方式</td><td align="right">数量/单位</td><td>折扣</td><td>单价</td><td>总价</td><td>加工要求</td><td>备注</td></tr>';
				while($item=$this->kclass->DB->fetchArray($items)){
					$total+=$item['amount'];
					$manufactureItem.='<tr class="'.$this->kclass->rotateLine().'">
						<td><a href="/s.php?module=forecast&action=view&forecastid='.$item['forecastid'].'">'.$item['forecastno'].'</a></td>
						<td><a href="/s.php?module=material&action=view&materialid='.$item['materialid'].'">'.$item['materialno'].'</a></td>
						<td>'.$item['title'].'</td>
						<td>'.$item['standard'].'</td>
						<td>'.$item['package'].'</td>
						<td align="right">'.number_format($item['quantity']).' '.$item['unit'].'</td>
						<td align="right">'.$this->kclass->iif($item['discount']>0, $item['discount'].'%', '').'</td>
						<td>'.number_format($item['price'],2).'</td>
						<td>'.number_format($item['amount'],2).'</td>
						<td>'.$item['requirement'].'</td>
						<td>'.$item['remark'].'</td></tr>';
				}
				$total=number_format($total, 2);
				$cntotal=$this->kclass->numberToCNAccount($total);
				$manufactureItem.='<tr class="even"><td colspan="3" align="center">合　　计</td><td colspan="6">人　民　币：　 '.$cntotal.'</td><td colspan="3">小写： '.$total.'</td></tr>
			</tbody></table>';
			}
			$returnItems=$this->kclass->DB->query("
				SELECT i.itemid,i.mid AS manufactureid,i.articleNo,i.title,i.materialid,i.quantity,i.package,
					m.materialno,m.title AS material,m.standard,
					u.title AS unit
				FROM item AS i
				LEFT JOIN material AS m ON (m.materialid=i.materialid)
				LEFT JOIN unit AS u ON (u.unitid=i.unitid)
				WHERE i.killed=0 AND i.module='manufacture' AND i.mid='".$manufacture['manufactureid']."'
				ORDER BY itemid
			");
			if($this->kclass->DB->numRows()){
				$manufactureItem.='<table class="hundred small">
					<thead></tr><th colspan="12">订单明细 </th></tr></thead><tbody><tr class="center"><td>物资编号</td><td>物资名称</td><td>规格</td><td>供应商产品编号</td><td>供应商使用的名称</td><td>包装方式</td><td align="right">数量/单位</td><td>备注</td></tr>';
				while($returnItem=$this->kclass->DB->fetchArray($returnItems)){
					$total+=$returnItem['amount'];
					$manufactureItem.='<tr class="'.$this->kclass->rotateLine().'">
						<td><a href="/s.php?module=material&action=view&materialid='.$returnItem['materialid'].'">'.$returnItem['materialno'].'</a></td>
						<td>'.$returnItem['material'].'</td>
						<td>'.$returnItem['standard'].'</td>
						<td>'.$returnItem['articleNo'].'</td>
						<td>'.$returnItem['title'].'</td>
						<td>'.$returnItem['package'].'</td>
						<td align="right">'.number_format($returnItem['quantity']).' '.$returnItem['unit'].'</td>
						<td>'.$returnItem['remark'].'</td></tr>';
				}
				$manufactureItem.='</tbody></table>';
			}
		}

$body=<<<EOF
<table class="hundred">
<thead><tr><th colspan="4">订单</th></tr></thead>
<tbody>
<tr class="odd">
<td width="100">订单编号：</td><td width="475">{$manufacture['orderno']}</td>
<td width="100">采购类型：</td><td>{$mftType}</td>
</tr>
<tr class="even">
<td>供 应 商：</td><td><a href="/s.php?module=psupplier&action=view&supplierid={$manufacture['supplierid']}">{$manufacture['supplier']}</a></td>
<td>发票类型：</td><td>{$manufacture['invoiceType']} （{$manufacture['taxRate']}）</td>
</tr>
<tr class="odd">
	<td>交货方式：</td><td>{$manufacture['deliveryterm']} <span class="gray">{$manufacture['endeliveryterm']}</span></td>
	<td>付款方式：</td><td>{$manufacture['paymentterm']} <span class="gray">{$manufacture['enpaymentterm']}</span></td>
</tr>
<tr class="even">
	<td>运输方式：</td><td>{$manufacture['shipmethod']} <span class="gray">{$manufacture['enshipmethod']}</span></td>
	<td>币　　种：</td><td>{$manufacture['currency']} <span class="gray">{$manufacture['symbol']}</span></td>
</tr>
<tr class="odd">
	<td>交货地点：</td><td>{$manufacture['loading']}</td>
	<td>卸货地点：</td><td>{$manufacture['discharge']}</td>
</tr>
<tr class="even">
	<td>到货日期:</td><td>{$manufacture['shipmentDate']}</td>
EOF;
	$body.=$this->kclass->iif($manufacture['mftType']=='forecast','<td>给付日期：:</td><td>'.$manufacture['paymentDate'].'</td>','<td></td><td></td>');
$body.=<<<EOF
</tr>
<tr class="odd">
	<td>包　　装：</td><td>{$manufacture['packing']}</td>
	<td>标　　签：</td><td>{$manufacture['labeling']}</td>
</tr>
<tr class="even">
	<td>特　　殊：</td><td>{$manufacture['special']}</td>
	<td>备　　注：</td><td>{$manufacture['remark']}</td>
</tr>
</tbody>
</table>
{$manufactureItem}
<form method="post" action="/s.php?module=manufacture&action=doverify">
<input type="hidden" name="module" value="manufacture">
<input type="hidden" name="action" value="doverify">
<input type="hidden" name="manufactureid" value="{$this->kclass->input['manufactureid']}">
<table>
<thead><tr><th colspan="4">审核</th></tr></thead>
<tbody>
<tr class="even">
<td>审　　核：<span class="bold red">*</span></td><td class="center"><input type="radio" name="ifVerify" value="1" />通过　　　<input type="radio" name="ifVerify" value="-1" checked />不通过</td>
</tr>
<tr class="odd">
<td>审核备注：</td><td><textarea name="verifyRemark" style="width:440px;height:110px;"></textarea></td>
</tr>
<tr class="even">
<td colspan=2 class="center"><input type="submit" value=" 提交 " /><input type="reset" value="重置" /></td>
</tr>
</tbody>
</table>
</form>
EOF;
		$this->kclass->tbline+=4;
		$this->kclass->page['title'].='审核订单 - '.$manufacture['orderno'];
		$this->kclass->page['onload'].='dc.tabhover();';
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - <a href="/s.php?module=manufacture&action=view&manufactureid='.$this->kclass->input['manufactureid'].'">查看订单</a> - 审核订单 - '.$manufacture['orderno'], 'right'=>'<a href="/s.php?module=manufacture&action=list">返回列表</a>　|　<a href="/s.php?module=manufacture&action=view&manufactureid='.$this->kclass->input['manufactureid'].'">查看订单</a>','body'=>$body));
	}
	//
	function doverify(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			$manufacture=$this->kclass->DB->queryFirst("SELECT orderno FROM manufacture WHERE manufactureid='".$this->kclass->input['manufactureid']."'");
			if($this->kclass->input['ifVerify']!=1 AND $this->kclass->input['ifVerify']!=-1){
				$e.='<li>请选择采购计划单中的 是否通过审核 按钮。</li>';
			}
		}
		if($e){
			$this->kclass->messager(array(
				'title' => '审核采购计划单',
				'text' => '您在审核采购计划单的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$this->kclass->DB->query("
			UPDATE manufacture SET
				ifVerify='".$this->kclass->input['ifVerify']."',
				verifier='".$this->kclass->user['userid']."',
				verified='".TIMENOW."',
				verifyRemark='".$this->kclass->input['verifyRemark']."'
			WHERE manufactureid='".$this->kclass->input['manufactureid']."'
		");
		$item=$this->kclass->DB->queryFirst("SELECT SUM(amount) AS total FROM `item` WHERE killed=0 AND module='manufacture' AND mid='".$this->kclass->input['manufactureid']."'");
		$manufacture=$this->kclass->DB->queryFirst("SELECT supplierid,currencyid FROM manufacture WHERE manufactureid='".$this->kclass->input['manufactureid']."'");
		$this->kclass->DB->query("
			INSERT INTO debitnote
				(`module`,`mid`,`supplierid`,`amount`,`currencyid`,`created`,`creator`)
			VALUES
				('manufacture','".$this->kclass->input['manufactureid']."','".$manufacture['supplierid']."','".$item['total']."','".$manufacture['currencyid']."','".TIMENOW."','".$this->kclass->user['userid']."')
		");
		if($this->kclass->input['ifVerify']==1){
			$verify='已通过';
		}elseif($this->kclass->input['ifVerify']==-1){
			$verify='未通过';
		}
		$this->kclass->messager(array(
			'title' => '审核采购计划单',
			'text' => '采购计划单 【<b>'.$manufacture['orderno'].'</b>】 '.$verify.'审核!返回查看采购计划单',
			'url' => '/s.php?module=manufacture&action=view&manufactureid='.$this->kclass->input['manufactureid'],
			'sec' => 2
		));
	}
	//
	function revise(){
		$revise=$this->kclass->DB->queryFirst("SELECT `revise`.mid,`revise`.ptext FROM `revise` LEFT JOIN `manufacture` ON (`manufacture`.manufactureid=`revise`.mid) WHERE reviseid='".$this->kclass->input['reviseid']."' LIMIT 0,1");
		if(!$revise){
			$this->kclass->boinkIt('/s.php?module=manufacture');
		}
		$manufacture=$this->kclass->DB->queryFirst("
			SELECT o.manufactureid,o.supplierid,o.customerNo,o.supplierNo,o.orderno,o.type,o.mftType,o.shipmentDate,o.paymentDate,o.packing,
					o.labeling,o.taxRate,o.special,o.ifVerify,o.verified,o.verifyRemark,o.remark,o.modified,o.created,o.killed,
				od.routeid,od.title AS route,od.caption,
				it.title AS invoiceType,
				s.title AS supplier,s.supplierno,s.address AS supplierAddress,s.linkman AS supplierLinkman,s.mobile AS supplierMobile,
					s.telephone AS supplierTelephone,s.email AS supplierEmail,s.fax AS supplierFax,
				cu.title AS customer,cu.customerno,cu.address AS customerAddress,cu.linkman AS customerLinkman,cu.mobile AS customerMobile,
					cu.telephone AS customerTelephone,cu.email AS customerEmail,cu.fax AS customerFax,
				c.title AS currency,c.symbol AS encurrency,
				lo.title AS loading,lo.entitle AS enloading,
				dis.title AS discharge,dis.entitle AS endischarge,
				sh.title AS shipmethod,sh.entitle AS enshipmethod,
				p.title AS paymentterm,p.entitle AS enpaymentterm,
				d.title AS deliveryterm,d.entitle AS endeliveryterm,
				u.username AS creator,
				us.username AS modifier,
				ur.username AS verifier
			FROM `manufacture` AS o
			LEFT JOIN `orderroute` AS od ON (od.routeid=o.routeid)
			LEFT JOIN `pinvoicetype` AS it ON (it.pinvoiceTypeid=o.pinvoiceTypeid)
			LEFT JOIN `supplier` AS s ON (s.supplierid=o.supplierid)
			LEFT JOIN `customer` AS cu ON (cu.customerid=o.customerid)
			LEFT JOIN `currency` AS c ON (c.currencyid=o.currencyid)
			LEFT JOIN `port` AS lo ON (lo.portid=o.loading)
			LEFT JOIN `port` AS dis ON (dis.portid=o.discharge)
			LEFT JOIN `shipmethod` AS sh ON (sh.shipmethodid=o.shipmethodid)
			LEFT JOIN `paymentterm` AS p ON (p.paymenttermid=o.paymenttermid)
			LEFT JOIN `deliveryterm` AS d ON (d.deliverytermid=o.deliverytermid)
			LEFT JOIN `user` AS u ON (u.userid=o.creator)
			LEFT JOIN `user` AS us ON (us.userid=o.modifier)
			LEFT JOIN `user` AS ur ON (ur.userid=o.verifier)
			WHERE o.routeid=4 AND o.type='PO' AND o.manufactureid='".$revise['mid']."'
			LIMIT 0,1
		");
		$this->kclass->page['title'].='订单历史版本 - '.$revise['orderno'];
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - 订单历史版本','right' => '<a href="/s.php?module=manufacture">返回列表</a>','body'=>$this->_view($manufacture)));
	}
/**********************************private function*****************************************************/
	// view body of the order
	function _view($manufacture){
		if($manufacture){
			$manufacture['shipmentDate']=date('Y-m-d',$manufacture['shipmentDate']);
			$manufacture['paymentDate']=date('Y-m-d',$manufacture['paymentDate']);
			foreach($this->mftType AS $key=>$val){
				if($manufacture['mftType']==$val['entitle']){
					$mftType=$val['title'];
				}
			}
			if($manufacture['verified']!=0){
				$overify='，由'.$manufacture['verifier'].'于'.date('Y-m-d',$manufacture['verified']).'审核';
				$verifyRemark='<td>审核备注：</td><td>'.$manufacture['verifyRemark'].'</td><td></td><td></td>';
			}

			$manufacture['taxRate']=($manufacture['taxRate']*100).'%';
			$manufactureItem.=$this->kclass->relatedmanufactureItem(array('manufactureid'=>$manufacture['manufactureid'],'mftType'=>$manufacture['mftType']));
			$fee=$this->kclass->relatedFee(array('manufactureid'=>$manufacture['manufactureid']));
			$attach=$this->kclass->getAttachs(array('module'=>'manufacture','mid'=>$manufacture['manufactureid']));
			if($attach!= false){
				$attachs = '<div class="clear">'.$attach.'</div>';
			}

$r=<<<EOF
<table class="hundred">
	<thead><tr><th colspan="4">基本信息</th></tr></thead>
	<tbody>
<tr class="odd">
	<td width="80">订单编号：</td><td width="450">{$manufacture['orderno']}</td>
	<td width="85">采购类型：</td><td>{$mftType}</td>
</tr>
<tr class="even">
	<td>发票类型：</td><td>{$manufacture['invoiceType']}</td>
	<td>税　　率：</td><td>{$manufacture['taxRate']}</td>
</tr>
<tr><td colspan=4 class="center bold">订货商信息（甲方）</td></tr>
<tr class="odd">
	<td>订货单位：</td><td>{$manufacture['customer']} ( {$manufacture['customerno']} )</td>
	<td>客户编号：</td><td>{$manufacture['customerNo']}</td>
</tr>
<tr class="even">
	<td>联 系 人：</td><td>{$manufacture['customerLinkman']}</td>
	<td>公司地址：</td><td>{$manufacture['customerAddress']}</td>
</tr>
<tr class="odd">
	<td>手　　机：</td><td>{$manufacture['customerMobile']}</td>
	<td>电　　话：</td><td>{$manufacture['customerTelephone']}</td>
</tr>
<tr class="even">
	<td>邮　　箱：</td><td>{$manufacture['customerEmail']}</td>
	<td>传　　真：</td><td>{$manufacture['customerFax']}</td>
</tr>
<tr><td colspan=4 class="center bold">供应商信息（乙方）</td></tr>
<tr class="odd">
	<td>供货单位：</td><td><a href="/s.php?module=psupplier&action=view&supplierid={$manufacture['supplierid']}">{$manufacture['supplier']}</a> ( {$manufacture['supplierno']} )</td>
	<td>供货商编号：</td><td>{$manufacture['supplierNo']}</td>
</tr>
<tr class="even">
	<td>联 系 人：</td><td>{$manufacture['supplierLinkman']}</td>
	<td>公司地址：</td><td>{$manufacture['supplierAddress']}</td>
</tr>
<tr class="odd">
	<td>手　　机：</td><td>{$manufacture['supplierMobile']}</td>
	<td>电　　话：</td><td>{$manufacture['supplierTelephone']}</td>
</tr>
<tr class="even">
	<td>邮　　箱：</td><td>{$manufacture['supplierEmail']}</td>
	<td>传　　真：</td><td>{$manufacture['supplierFax']}</td>
</tr>
</tbody>
</table>
<table class="hundred">
<thead></tr><th colspan="4">条款</th></tr></thead>
<tbody>
<tr class="odd">
	<td width="80">交货方式：</td><td width="450">{$manufacture['deliveryterm']} <span class="gray">{$manufacture['endeliveryterm']}</span></td>
	<td width="85">付款方式：</td><td>{$manufacture['paymentterm']} <span class="gray">{$manufacture['enpaymentterm']}</span></td>
</tr>
<tr class="even">
	<td>运输方式：</td><td>{$manufacture['shipmethod']} <span class="gray">{$manufacture['enshipmethod']}</span></td>
	<td>币　　种：</td><td>{$manufacture['currency']} <span class="gray">{$manufacture['symbol']}</span></td>
</tr>
<tr>
	<td>交货地点：</td><td>{$manufacture['loading']}</td>
	<td>卸货地点：</td><td>{$manufacture['discharge']}</td>
</tr>
EOF;
	$r.=$this->kclass->iif($manufacture['mftType']=='forecast','<tr class="odd"><td>给付日期：:</td><td>'.$manufacture['paymentDate'].'</td><td></td><td></td></tr>','');
$r.=<<<EOF
<tr class="even">
	<td>包　　装：</td><td>{$manufacture['packing']}</td>
	<td>标　　签：</td><td>{$manufacture['labeling']}</td>
</tr>
<tr class="odd">
	<td>特　　殊：</td><td>{$manufacture['special']}</td>
	<td>备　　注：</td><td>{$manufacture['remark']}</td>
</tr>
{$verifyRemark}
</table>
{$manufactureItem}
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
{$attachs}
</dd>
EOF;
		}else{
			$r='数据错误，很抱歉！';
		}
		return $r;
	}

}

?>