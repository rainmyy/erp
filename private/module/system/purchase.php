<?php
//
class purchase{
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
			case 'find':
				return $this->find();
				break;
			case 'add':
				return $this->add();
				break;
			case 'insert':
				return $this->insert();
				break;
			case 'view':
				return $this->view();
				break;
			case 'update':
				return $this->update();
				break;
			case 'doupdate':
				return $this->doupdate();
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
			case 'insertPurchase':
				return $this->insertPurchase();
				break;
			case 'verify':
				return $this->verify();
				break;
			case 'doverify':
				return $this->doverify();
				break;
			case 'approve':
				return $this->approve();
				break;
			case 'doapprove':
				return $this->doapprove();
				break;
			default:
				return $this->mmlist();

		}
	}
	// main page,shows all the purchase info list
	function mmlist(){
		// 小于安全库存的物料
		$materials=$this->kclass->DB->query("
			SELECT m.materialid,m.materialno,m.title,m.standard,m.quantity,m.qualified,m.disqualified,m.max,m.min,m.criticalNumber,
				u.title AS unit,
				c.parentlist
			FROM `material` AS m
			LEFT JOIN `unit` AS u ON (u.unitid=m.unitid)
			LEFT JOIN `category` AS c ON (c.categoryid=m.categoryid)
			WHERE m.killed=0 AND m.qualified<m.criticalNumber 
			ORDER BY m.materialno ASC
		");
		if($materialCounter=$this->kclass->DB->numRows()){
			$i=1;
			$materialList['panel']='<form action="/s.php?module=purchaseApply&action=add" method="post">
			<input type="hidden" name="module" value="purchaseApply" /><input type="hidden" name="action" value="add" /><input type="hidden" name="step" value="2" /><input type="hidden" name="type" value="material"/><table class="hundred"><thead><th width="15">ID</th><th width="30">物资编号</th><th width="170">名称</th><th width="250">规格</th><th width="200">分类</th><th width="60">良品数</th><th width="60">最小库存</th><th width="60">临界库存</th><th width="50">单位</th><th width="15"></th></thead><tbody>';
			while($material=$this->kclass->DB->fetchArray($materials)){
				if(abs($material['max']-$material['qualified'])>0){
					$categoryid=explode(',',$material['parentlist']);
					foreach($categoryid as $key=>$value){
						if($key!=0){
							$category=$this->kclass->DB->queryFirst("SELECT title FROM category WHERE categoryid='".$value."' LIMIT 0,1");
							if($category['title'])
								$material['category']=$category['title'].' '.$material['category'];
						}
					}
					$materialList['panel'].='<tr class="'.$this->kclass->rotateLine().'">
						<input type="hidden" name="itemMaterialid['.$i.']" value="'.$material['materialid'].'" />
						<td>'.$i.'</td>
						<td><a href="/s.php?module=material&action=view&materialid='.$material['materialid'].'">'.$material['materialno'].'</a></td>
						<td><span class="small" title="'.$material['title'].'">'.$this->kclass->iif(strlen($material['title'])>30, mb_substr($material['title'], 0, 30, 'UTF-8').'...', $material['title']).'</span> '.$this->kclass->iif($material['attachs']>0, ' <span class="attachFile" title="有'.$material['attachs'].'个附件。"></span> ', '').$this->kclass->iif($material['images']>0, ' <span class="attachImage" title="有'.$material['images'].'个图片。"></span> ', '').'</td>
						<td title="'.$material['standard'].'"><span class="small">'.$this->kclass->iif(strlen($material['standard'])>30, mb_substr($material['standard'], 0, 30, 'UTF-8').'...', $material['standard']).'</span></td>
						<td><span class="small">'.$material['category'].'</span></td>
						<td>'.$material['qualified'].'</td>
						<td>'.$material['min'].'</td>
						<td>'.$material['criticalNumber'].'</td>
						<td>'.$material['unit'].'</td>
						<td><input type="checkbox" name="itemMaterialSelect['.$i.']" value="'.$material['materialid'].'" /></td>
					</tr>';
					$i++;
				}
			}
			$materialList['panel'].='</tbody></table><div class="center"><input type="submit" value="新增为采购申请" /><input type="submit" value="复 位" /></div></form>';
			$materialList['count']='('.$materialCounter.')';
		}else{
			$materialList['off']=' disabled';
		}

		// 已审核已审批的采购申请
		$purchaseApply['panel'].='<form action="/s.php?module=purchase&action=insertPurchase" method="post"><input type="hidden" name="module" value="purchase" /><input type="hidden" name="action" value="insertPurchase" /><input type="hidden" name="type" value="purchaseApply" />';
		$applys=$this->kclass->DB->query("
			SELECT pa.*,
				d.title AS department
			FROM `purchaseapply` AS pa
			LEFT JOIN department AS d ON (d.departmentid=pa.departmentid)
			WHERE pa.killed=0 AND pa.ifVerify=1 AND pa.ifApprove=1 
			ORDER BY pa.created DESC
		");
		if($applyCount=$this->kclass->DB->numRows()){
			$i=1;$k=1;
			$applyCounter=0;
			while($apply=$this->kclass->DB->fetchArray($applys)){
				$typeValue='';
				$types=$this->kclass->DB->query("SELECT title FROM purchasetype WHERE killed=0 AND typeid IN (".$apply['typeid'].")");
				if($counter=$this->kclass->DB->numRows()){
					$j=1;
					while($type=$this->kclass->DB->fetchArray($types)){
						if($j<$counter){
							$typeValue.=$type['title'].'、';
						}else{
							$typeValue.=$type['title'];
						}
						$j++;
					}
				}
				$applyItems=$this->kclass->DB->query("
					SELECT p.itemid,p.module,p.mid,p.materialid,p.materialno AS newMaterialno,p.title AS newTitle,
							p.standard AS newStandard,p.quantity,p.dateline,p.remark,
						m.materialno,m.title,m.standard,
						u.title AS unit
					FROM preinbounditem AS p
					LEFT JOIN material AS m ON (m.materialid=p.materialid)
					LEFT JOIN unit AS u ON (u.unitid=p.unitid)
					WHERE p.module='purchaseApply' AND ifChooser=0 AND p.killed=0 AND p.mid='".$apply['purchaseApplyid']."'
					ORDER BY p.ordering ASC
				");
				if($this->kclass->DB->numRows()){
					$m=1;
					$applyCounter++;
					$purchaseApply['panel'].='<table class="hundred"><thead><tr><input type="hidden" name="applyId['.$k.']" value="'.$apply['purchaseApplyid'].'"><th colspan=8><span class="plus hand"></span>编号：<a href="/s.php?module=purchaseApply&action=view&purchaseApplyid='.$apply['purchaseApplyid'].'">'.$apply['applyno'].'</a>　申请人：'.$apply['applicant'].'　申请部门：'.$apply['department'].'　物资类型：'.$typeValue.'<span class="right">全选：<input type="checkbox" name="itemApplyid['.$k.']"  value="'.$apply['purchaseApplyid'].'"></span></th></tr></thead><tbody style="display:none"><tr><td width="15">ID</td><td width="80">物资编号</td><td width="200">名称</td><td width="500">规格</td><td width="80">数量</td><td>到货时间</td><td>备注</td><td width="50">选</td></tr>';
					while($applyItem=$this->kclass->DB->fetchArray($applyItems)){
						$purchaseApply['panel'].='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').'"><input type="hidden" name="m['.$i.']" value="purchaseApply" /><input type="hidden" name="itemId['.$i.']" value="'.$applyItem['itemid'].'" >';
						if($applyItem['materialid']==0){
							$purchaseApply['panel'].='<td>'.$m.'</td>
								<td>'.$applyItem['newMaterialno'].'</td>
								<td>'.$applyItem['newTitle'].'</td>
								<td>'.$applyItem['newStandard'].'</td>';
						}else{
							$purchaseApply['panel'].='<td>'.$m.'</td>
								<td><a href="/s.php?module=material&action=view&materialid='.$applyItem['materialid'].'">'.$applyItem['materialno'].'</a></td>
								<td>'.$applyItem['title'].'</td>
								<td>'.$applyItem['standard'].'</td>';
						}
						$purchaseApply['panel'].='<td>'.$applyItem['quantity'].$applyItem['unit'].'</td><td>'.date('Y-m-d',$applyItem['dateline']).'</td><td>'.$applyItem['remark'].'</td><td><input type="checkbox" name="itemApplyItemid['.$i.']" value="'.$applyItem['itemid'].'" /></td></tr>';
						$m++;$i++;
					}
					$purchaseApply['panel'].='</tbody></table>';
					$k++;
				}
			}
			$purchaseApply['count']='('.$applyCounter.')';
		}
		//已审核已审批的外发加工申请
		$entrusts=$this->kclass->DB->query("
			SELECT e.*
			FROM `entrust` AS e
			WHERE e.killed=0 AND e.ifVerify=1 AND e.ifApprove=1 
			ORDER BY e.created DESC
		");
		if($entrustCount=$this->kclass->DB->numRows()){
			$applyCounter=0;
			while($entrust=$this->kclass->DB->fetchArray($entrusts)){
				$entrustItems=$this->kclass->DB->query("
					SELECT e.itemid,e.quantity,e.dateline,e.requirement,e.remark,
						m.materialno,m.title,m.standard,
						u.title AS unit 
					FROM `entrustitem` AS e
					LEFT JOIN material AS m ON (m.materialid=e.materialid)
					LEFT JOIN unit AS u ON (u.unitid=e.unitid)
					WHERE e.killed=0 AND e.entrustid='".$entrust['entrustid']."'
					ORDER BY e.itemid ASC
				");
				if($this->kclass->DB->numRows()){
					$m=1;
					$applyCounter++;
					$purchaseApply['panel'].='<table class="hundred"><thead><tr><input type="hidden" name="applyId['.$k.']" value="'.$entrust['entrustid'].'"><th colspan=8><span class="plus hand"></span>编号：<a href="/s.php?module=entrust&action=view&entrustid='.$entrust['entrustid'].'">'.$entrust['entrustno'].'</a>　申请人：'.$entrust['applicant'].'　申请部门：'.$entrust['department'].'　<span class="right">全选：<input type="checkbox" name="itemEntrustid['.$k.']"  value="'.$entrust['entrustid'].'"></span></th></tr></thead><tbody style="display:none"><tr><td width="15">ID</td><td width="80">物资编号</td><td width="200">名称</td><td width="500">规格</td><td width="80">数量</td><td>到货时间</td><td>备注</td><td width="50">选</td></tr>';
					while($entrustItem=$this->kclass->DB->fetchArray($entrustItems)){
						$purchaseApply['panel'].='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').'">
						<input type="hidden" name="m['.$i.']" value="entrust" />
						<input type="hidden" name="itemId['.$i.']" value="'.$entrustItem['itemid'].'" >';
						$purchaseApply['panel'].='<td>'.$m.'</td>
							<td><a href="/s.php?module=material&action=view&materialid='.$entrustItem['materialid'].'">'.$entrustItem['materialno'].'</a></td>
							<td>'.$entrustItem['title'].'</td>
							<td>'.$entrustItem['standard'].'</td>
							<td>'.$entrustItem['quantity'].$entrustItem['unit'].'</td>
							<td>'.date('Y-m-d',$entrustItem['dateline']).'</td>
							<td>'.$applyItem['remark'].'</td>
							<td><input type="checkbox" name="itemApplyItemid['.$i.']" value="'.$entrustItem['itemid'].'" /></td>
							</tr>';
						$m++;$i++;
					}
					$purchaseApply['panel'].='</tbody></table>';
					$k++;
				}
			}
			$purchaseApply['count']='('.$applyCounter.')';
		}
		if(!$entrustCount AND !$applyCount){
			$purchaseApply['off']=' disabled';
		}
		$purchaseApply['panel'].='<div class="center"><input type="submit" value="新增为采购计划" /><input type="submit" value="复 位" /></div></form>';
		// 采购计划
		if($this->kclass->input['show']=='all'){
			$condition='1=1';
		}elseif($this->kclass->input['show']=='verify'){
			$condition='`purchase`.ifVerify=1';
		}elseif($this->kclass->input['show']=='unverify'){
			$condition='`purchase`.ifVerify=-1';
		}elseif($this->kclass->input['show']=='inverify'){
			$condition='`purchase`.ifVerify=0';
		}else{
			$condition='`purchase`.killed=0 AND `purchase`.ifVerify=0 OR `purchase`.ifApprove=0';
		}
		if($this->kclass->input['orderby']!='')$query['orderby']=$this->kclass->input['orderby'];
		if($this->kclass->input['direction']!='')$query['direction']=$this->kclass->input['direction'];
		if($this->kclass->input['show']!='')$query['show']=$this->kclass->input['show'];
		if($this->kclass->input['layout']!='')$query['layout']=$this->kclass->input['layout'];
		$orderby=$this->kclass->orderby(array('module'=>'purchase','direction'=>'asc','orderby'=>'modified', 'default'=>'purchaseno', 'serial'=>array( array('title'=>'编号', 'field'=>'purchaseno','word'=>'no'), array('title'=>'修改时间', 'field'=>'modified'), array('title'=>'建立时间', 'field'=>'created')),'appendUrl'=>$query));
		if(is_array($query) AND count($query)>0)$queryPart='&'.http_build_query($query);
		$purchases=$this->kclass->DB->query("
			SELECT `purchase`.*,
				c.title AS currency,
				u.username AS creator,
				us.username AS modifier
			FROM `purchase` 
			LEFT JOIN currency AS c ON (c.currencyid=`purchase`.currencyid)
			LEFT JOIN user AS u ON (u.userid=`purchase`.creator)
			LEFT JOIN user AS us ON (us.userid=`purchase`.modifier)
			WHERE ".$condition."
			ORDER BY ".$orderby['sql']."
		");
		if($counter=$this->kclass->DB->numRows()){
			if($this->kclass->input['layout']=='grid'){
				$purchaseBody['panel'].='<ul id="mmlist" class="mmlist clear">';
			}else{
				$purchaseBody['panel'].='<table class="hundred"><thead><th width="30">ID</th><th width="150">编号</th><th width="150">采购员</th><th>申请时间</th><th width="140">审核状态</th><th width="80"></th></thead><tbody>';
			}
			$i=1;
			while($purchase=$this->kclass->DB->fetchArray($purchases)){
				$alarm='';
				$purchase['created']=date('Y-m-d',$purchase['created']);
				$verify=$this->kclass->verify($purchase['ifVerify']);
				$approve=$this->kclass->approve($purchase['ifApprove']);
				if($purchase['ifVerify']==0){
					$alarm='bgYellow';
				}elseif($purchase['ifVerify']==-1){
					$alarm='bgRed';
				}
				if($purchase['killed']>0){
					$link='<a href="/s.php?module=purchase&action=restore&purchaseid='.$purchase['purchaseid'].'&rt=list">恢复</a>';
				}else{
					$link='';
					if($purchase['ifApprove']==0){
						if($purchase['ifVerify']==0){
							$link.=' <a href="/s.php?module=purchase&action=verify&purchaseid='.$purchase['purchaseid'].'" >核</a> <a href="/s.php?module=purchase&action=remove&purchaseid='.$this->kclass->input['purchaseid'].'&rt=listApply">删</a> ';
						}elseif($purchase['ifVerify']==1){
							$link.='<a href="/s.php?module=purchase&action=approve&purchaseid='.$purchase['purchaseid'].'" >批</a> ';
						}elseif($purchase['ifVerify']==0){
							$link.='<a href="/s.php?module=purchase&action=remove&purchaseid='.$this->kclass->input['purchaseid'].'&rt=listApply">删</a> ';
						}
						$link.='<a href="/s.php?module=purchase&action=update&purchaseid='.$purchase['purchaseid'].'">改</a>';
					}elseif($purchase['ifApprove']==-1){
						$link.='<a href="/s.php?module=purchase&action=remove&purchaseid='.$this->kclass->input['purchaseid'].'&rt=listApply">删</a>';
					}elseif($purchase['ifApprove']==1){
						 $link.=' <a href="/p.php?action=purchase&purchaseid='.$purchase['purchaseid'].'" target="_blank">印</a>';
					}
				}
				if($this->kclass->input['layout']=='grid'){//表格
					$purchaseBody['panel'].='<li title="由 '.$purchase['creator'].' 建于 '.$created.$this->kclass->iif($purchase['modifier']!='', '，'.$purchase['modifier'].' 改于 '.date('Y-m-d H:i',$purchase['modified']), '').'"'.$this->kclass->iif($i%4==0, ' class="end"', '').'>
					<div class="mmlistt">
						<span class="right normal">';
					if($purchase['killed']>0){
						$purchaseBody['panel'].='<a href="/s.php?module=purchase&action=revival&purchaseid='.$purchase['purchaseid'].'&rt=list" onclick="return confirm(\'你确定要恢复这个采购计划单 '.$purchase['purchaseno'].' 吗？\');">恢复</a>';
					}else{
						$purchaseBody['panel'].='<a href="/s.php?module=purchase&action=kill&purchaseid='.$purchase['purchaseid'].'&rt=list" onclick="return confirm(\'你确定要删除这个采购计划单 '.$purchase['purchaseno'].' 吗？\');">删</a> '.$this->kclass->iif($purchase['ifVerify']==0,'<a href="/s.php?module=purchase&action=update&purchaseid='.$purchase['purchaseid'].'&rt=list">改</a>','');
					}
					$purchaseBody['panel'].='</span>
						<span class="small">'.$purchase['attr'].'</span> <a href="/s.php?module=purchase&action=view&purchaseid='.$purchase['purchaseid'].'">'.$purchase['purchaseno'].'</a><br><span class="small gray right">'.$purchase['created'].'</span>'.$this->kclass->iif($purchase['attachs']>0, ' <span class="attachFile" title="有'.$purchase['attachs'].'个附件。"></span> ', '').$this->kclass->iif($purchase['images']>0, ' <span class="attachImage" title="有'.$purchase['images'].'个图片。"></span> ', '').''.$verify.'</div>
					<div class="mmlistb">
						<div><span class="small gray">采购员：'.$purchase['applicant'].'</span></div>
						<div class="small clear"><span class=right title="由 '.$prchase['creator'].' 建于 '.$purchase['created'].'">建于 '.$purchase['created'].'</span></div></div></li>';
				}else{
					$purchaseBody['panel'].='<tr class="'.$this->kclass->rotateLine().' '.$alarm.'">
						<td>'.$i.'</td>
						<td><a href="/s.php?module=purchase&action=view&purchaseid='.$purchase['purchaseid'].'">'.$purchase['purchaseno'].$this->kclass->iif($purchase['attachs']>0, ' <span class="attachFile" title="有'.$purchase['attachs'].'个附件。"></span> ', '').$this->kclass->iif($purchase['images']>0, ' <span class="attachImage" title="有'.$purchase['images'].'个图片。"></span> ', '').'</a></td>
						<td title="'.$purchase['applicant'].'">'. $purchase['applicant'].'</td>
						<td>'.$purchase['created'].'</td>
						<td>'.$verify.' , '.$approve.'</td>
						<td align="center">'.$link.'</td>
					</tr>';
				}
				$i++;
			}
			if($this->kclass->input['layout']=='grid'){
				$purchaseBody['panel'] .= '</ul>';
			}else{
				$purchaseBody['panel'].='</tbody></table>';
			}
			$purchaseBody['count']='('.$counter.')';
		}else{
			$purchaseBody['off']=' disabled';
		}
		if($this->kclass->input['layout']=='grid'){
			$layoutLink='<a href="/s.php?module=purchase&action=list&layout=list'.str_replace('&layout=grid', '', $queryPart).'">列表</a> 格子';
		}else{
			$layoutLink='列表 <a href="/s.php?module=purchase&action=list&layout=grid'.str_replace('&layout=list', '', $queryPart).'">格子</a>';
		}
		if($this->kclass->input['show']=='all'){
			$showLink='<a href="/s.php?module=purchase&action=list'.str_replace('&show=all', '', $queryPart).'">默认</a> 所有<span class="small gray">(包括删除)</span> <a href="/s.php?module=purchase&action=list&show=verify'.str_replace('&show=all', '', $queryPart).'">已通过</a>  <a href="/s.php?module=purchase&action=list&show=inverify'.str_replace('&show=all', '', $queryPart).'">待审核</a>  <a href="/s.php?module=purchase&action=list&show=unverify'.str_replace('&show=all', '', $queryPart).'">未通过</a>';
		}elseif($this->kclass->input['show']=='verify'){
			$showLink='<a href="/s.php?module=purchase&action=list'.str_replace('&show=verify', '', $queryPart).'">默认</a> <a href="/s.php?module=purchase&action=list&show=all'.str_replace('&show=verify', '', $queryPart).'">所有<span class="small gray">(包括删除)</span></a> 已通过 <a href="/s.php?module=purchase&action=list&show=inverify'.str_replace('&show=verify', '', $queryPart).'">待审核</a>  <a href="/s.php?module=purchase&action=list&show=unverify'.str_replace('&show=verify', '', $queryPart).'">未通过</a>';
		}elseif($this->kclass->input['show']=='inverify'){
			$showLink='<a href="/s.php?module=purchase&action=list'.str_replace('&show=inverify', '', $queryPart).'">默认</a> <a href="/s.php?module=purchase&action=list&show=all'.str_replace('&show=inverify', '', $queryPart).'">所有<span class="small gray">(包括删除)</span></a> <a href="/s.php?module=purchase&action=list&show=verify'.str_replace('&show=inverify', '', $queryPart).'">已通过</a> 待审核 <a href="/s.php?module=purchase&action=list&show=unverify'.str_replace('&show=inverify', '', $queryPart).'">未通过</a>';
		}elseif($this->kclass->input['show']=='unverify'){
			$showLink='<a href="/s.php?module=purchase&action=list'.str_replace('&show=unverify', '', $queryPart).'">默认</a> <a href="/s.php?module=purchase&action=list&show=all'.str_replace('&show=unverify', '', $queryPart).'">所有<span class="small gray">(包括删除)</span></a> <a href="/s.php?module=purchase&action=list&show=inverify'.str_replace('&show=unverify', '', $queryPart).'">已通过</a> <a href="/s.php?module=purchase&action=list&show=inverify'.str_replace('&show=unverify', '', $queryPart).'">待审核</a> 未通过';
		}else{
			$showLink='默认 <a href="/s.php?module=purchase&action=list&show=all'.str_replace('&show=all', '', $queryPart).'">所有<span class="small gray">(包括删除)</span></a> <a href="/s.php?module=purchase&action=list&show=verify'.str_replace('&show=all', '', $queryPart).'">已通过</a>  <a href="/s.php?module=purchase&action=list&show=inverify'.str_replace('&show=all', '', $queryPart).'">待审核</a>  <a href="/s.php?module=purchase&action=list&show=unverify'.str_replace('&show=all', '', $queryPart).'">未通过</a>';
		}

		// 已通过审批的采购计划
		$approvePurchases=$this->kclass->DB->query("
			SELECT `purchase`.*,
				c.title AS currency,
				u.username AS creator,
				us.username AS modifier
			FROM `purchase` 
			LEFT JOIN currency AS c ON (c.currencyid=`purchase`.currencyid)
			LEFT JOIN user AS u ON (u.userid=`purchase`.creator)
			LEFT JOIN user AS us ON (us.userid=`purchase`.modifier)
			WHERE `purchase`.killed=0 AND `purchase`.ifVerify=1 AND `purchase`.ifApprove=1
			ORDER BY purchaseno DESC
		");
		if($this->kclass->DB->numRows()){
			$approveCounter=0;
			$i=1;$k=1;
			$approveBody['panel'].='<form action="/s.php?module=porder&action=add" method="post"><input type="hidden" name="module" value="porder" /><input type="hidden" name="action" value="add" /><input type="hidden" name="step" value="2" /><table><tbody><tr><td>请选择采购类型</td><td><input type="radio" name="purchaseType" value="normal" checked>普通采购<input type="radio" name="purchaseType" value="entrust">加工采购';
			while($approvePurchase=$this->kclass->DB->fetchArray($approvePurchases)){
				$items=$this->kclass->DB->query("
					SELECT pi.itemid,pi.module,pi.mid,pi.mitemid,pi.materialid,pi.quantity,pi.unitid,pi.supplierid,pi.price,pi.discount,pi.arrivalTime,pi.remark,
						m.materialno,m.title,m.standard,m.entrustMaterialid,
						pii.materialno AS newMaterialno,pii.title AS newTitle,pii.standard AS newStandard,
						s.title AS supplier,
						b.title AS brand,
						u.title AS unit
					FROM purchaseitem AS pi
					LEFT JOIN material AS m ON (m.materialid=pi.materialid)
					LEFT JOIN preinbounditem AS pii ON (pii.itemid=pi.mitemid)
					LEFT JOIN supplier AS s ON (s.supplierid=pi.supplierid)
					LEFT JOIN brand AS b ON (b.brandid=pi.brandid)
					LEFT JOIN `unit` AS u ON (pi.unitid=u.unitid)
					WHERE pi.killed=0 AND pi.ifChooser=0 AND pi.purchaseid='".$approvePurchase['purchaseid']."'
					ORDER BY pi.itemid ASC
				");
				$approveItemCounter+=$this->kclass->DB->numRows($items);
				if($this->kclass->DB->numRows($items)){
					$approveCounter++;
					$m=1;
					$approveBody['panel'].='</td></tr></tbody></table><table class="hundred"><thead>
					<tr class="bold "><input type="hidden" name="purchaseId['.$k.']" value="'.$approvePurchase['purchaseid'].'">
					<th colspan=15><span class="plus hand"></span>采购计划单编号：<a class="middle bold" href="/s.php?module=purchase&action=view&purchaseid='.$approvePurchase['purchaseid'].'">'.$approvePurchase['purchaseno'].'</a>　申请人：'.$approvePurchase['applicant'].'　申请时间：'.date('Y-m-d',$approvePurchase['created']).'<span class="right">全选：<input type="checkbox" name="itemPurchaseid['.$k.']"  value="'.$approvePurchase['purchaseid'].'"></span></th></tr></thead>
					<tbody style="display:none"><tr class="center even"><td width="15">ID</td><td width="60">申请类型</td><td width="50">申请单编号</td><td>物资编号</td><td>物资名称</td><td>物资规格</td><td>供应商</td><td>数量/单位</td><td>币种</td><td>单价</td><td>折扣</td><td>总价</td><td>到货时间</td><td>备注</td><td>选</td></tr>';
					while($item=$this->kclass->DB->fetchArray($items)){
						$materialno=$this->kclass->iif($item['materialid']==0,$item['newMaterialno'],$item['materialno']);
						$materialid=$item['materialid'];
						$title=$this->kclass->iif($item['materialid']==0,$item['newTitle'],$item['title']);
						$standard=$this->kclass->iif($item['materialid']==0,$item['newStandard'],$item['standard']);
						$item['arrivalTime']=date('Y-m-d',$item['arrivalTime']);
						$class=$this->kclass->iif($i%2==0,'class="even"','class="odd"');
						if($item['module']=='purchaseApply'){
							$mitem=$this->kclass->DB->queryFirst("SELECT pi.*,pa.applyno,pa.purchaseApplyid FROM preinbounditem AS pi LEFT JOIN purchaseapply AS pa ON (pi.mid=pa.purchaseApplyid) WHERE pi.killed=0 AND pi.module='purchaseApply' AND pi.itemid='".$item['mitemid']."'");
							$type="采购申请";
							$typeno='<a href="/s.php?module=purchaseapply&action=view&purchaseApplyid='.$mitem['purchaseApplyid'].'">'.$mitem['applyno'].'</a>';
						}elseif($item['module']=='entrust'){
							$mitem=$this->kclass->DB->queryFirst("SELECT ei.*,e.entrustid,e.entrustno FROM entrustitem AS ei LEFT JOIN entrust AS e ON (e.entrustid=ei.entrustid) WHERE ei.killed=0 AND ei.itemid='".$item['mitemid']."'");
							$type="外发加工";
							$typeno='<a href="/s.php?module=entrust&action=view&entrustid='.$mitem['entrustid'].'">'.$mitem['entrustno'].'</a>';
							$materialid = $item['entrustMaterialid'];
							if($materialid!=""){
									$query = $this->kclass->DB->queryFirst("SELECT * FROM `material` WHERE materialid='{$materialid}'");
									$materialno = $query['materialno'];
									$standard = $query['standard'];	
							}
						}
						$approveBody['panel'].='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').' small">
							<input type="hidden" name="itemId['.$i.']" value="'.$item['itemid'].'">
							<td>'.$m.'</td>
							<td>'.$type.'</td>
							<td>'.$typeno.'</td>
							<td><a href="/s.php?module=material&action=view&materialid='.$materialid.'">'.$materialno.'</a></td>
							<td>'.$title.'</td>
							<td>'.$standard.'</td>
							<td><a href="/s.php?module=psupplier&action=view&supplierid='.$item['supplierid'].'">'.$item['supplier'].'</a></td>
							<td>'.$item['quantity'].' '.$item['unit'].'</td>
							<td>'.$item['currency'].'</td>
							<td>'.$item['price'].'</td>
							<td>'.$item['discount'].'%</td>
							<td>'.$item['total'].'</td>
							<td>'.$item['arrivalTime'].'</td>
							<td>'.$item['remark'].'</td>
							<td><input type="checkbox" name="itemSelect['.$i.']" value="'.$item['itemid'].'"></td></tr>';
						$m++;$i++;
					}
					$approveBody['panel'].='</tbody></table>';
					$k++;
				}
			}
			if($approveItemCounter!=0){
				$approveBody['panel'].='<div class="center"><input type="submit" value="新增为采购订单" /><input type="submit" value="复 位" /></div></form>';
			}
			$approveBody['count']='('.$approveCounter.')';
		}
		if($approveItemCounter==0){
			$approveBody['off']=' disabled';
		}
$body=<<<EOF
<dl id="purchase" class="tabs" style="display: block;">
<dt tabid="0"{$materialList['off']}>小于安全库存的物料{$materialList['count']}</dt>
<dt title="已通过审批的采购申请"{$purchaseApply['off']}>已通过审批的采购申请{$purchaseApply['count']}</dt>
<dt title="未审核未审批的采购计划"{$purchaseBody['off']}>未审核未审批的采购计划{$purchaseBody['count']}</dt>
<dt title="已通过审批的采购计划"{$approveBody['off']}>已通过审批的采购计划{$approveBody['count']}</dt>
<dd>{$materialList['panel']}</dd>
<dd>{$purchaseApply['panel']}</dd>
<dd>{$purchaseBody['panel']}</dd>
<dd>{$approveBody['panel']}</dd>
</dl>
EOF;
		$this->kclass->page['title'].='采购计划单列表';
		$this->kclass->page['onload'].='dc.listhover();dc.tabhover();dc.tabs({\'id\':\'purchase\'});dc.show()';
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - 采购计划单列表','right' => '<span class="small">排序：</span>'.$orderby['link'].'|　<span class="small">显示方式：</span>'.$showLink.'　|　<span class="small">布局：</span>'.$layoutLink.'　|　<a href="/s.php?module=purchase&action=add">新建</a>','body'=>$body));
	}
	//
	function find(){
		if($this->kclass->input['requestMethod']!='post'){
			$body=<<<EOF
<form action="/s.php?module=purchase&action=find" method="post">
<input type="hidden" name="module" value="purchase">
<input type="hidden" name="action" value="find">
<table style="width:450px">
<thead><tr><th colspan=2>根据条件查找采购申请单</th></tr></thead>
<tbody>
<tr class="odd">
<td width="100">日　　期：</td><td><input type="text" name="dateline" id="dateline"></td>
</tr>
<tr class="even">
<td width="100">物料编号：</td><td><input type="text" name="materialno" style="width:300px"></td>
</tr>
<tr class="even">
<td width="100">物料名称：</td><td><input type="text" name="title" style="width:300px"></td>
</tr>
<tr class="odd">
<td width="100">计划单编号：</td><td><input type="text" name="purchaseno"></td>
</tr>
<tr class="even">
<td class="small gray" colspan="9">
注意事项：<br>
①查找采购计划单时，可以通过输入“计划单编号”直接查找，也可以通过输入“日期”和“物料编号”进行查找<br>
②请保证输入的值无误</td>
</tr>
<tr class="odd" nohover>
<td class="center" colspan=2><input type="submit" id="submitButton" value="  提交  "><input type="reset" value="  复位  "/></td>
</tr>
</tbody>
</table>
</form>
EOF;
			$this->kclass->page['title'].='查找采购申请单';
			$this->kclass->page['onload'].='dc.listhover();$(\'#dateline\').datepicker()';
			$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - 查找采购申请单','right' => '<a href="/s.php?module=purchase">查看列表</a>','body'=>$body));
		}else{
			if($this->kclass->input['purchaseno']!=''){
				$purchaseno=trim($this->kclass->input['purchaseno']);
				$purchase=$this->kclass->DB->queryFirst("SELECT purchaseid FROM purchase WHERE purchaseno='".$purchaseno."' LIMIT 0,1");
			}else{
				if($this->kclass->input['dateline']!=''){
					$sd=explode('-',$this->kclass->input['dateline']);
					$start=mktime(0,0,0,$sd[1],$sd[2],$sd[0]);
					$end=$start+86400;
					$condition.=' AND (p.created>'.$start.' OR p.created<'.$end.')';
				}
				if($this->kclass->input['materialno']!=''){
					$materialno=trim($this->kclass->input['materialno']);
					$condition=' AND m.materialno="'.$materialno.'"';
				}
				if($this->kclass->input['title']!=''){
					$title=trim($this->kclass->input['title']);
					$condition.=' AND (m.title="'.$this->kclass->input['title'].'" OR p.title="'.$title.'" OR pi.title="'.$title.'")';
				}
				$purchase=$this->kclass->DB->queryFirst("
					SELECT DISTINCT p.purchaseid 
					FROM purchaseitem AS p
					LEFT JOIN material AS m ON (m.materialid=p.materialid) 
					LEFT JOIN preinbounditem AS pi ON (pi.itemid=p.applyItemid)
					WHERE p.killed=0 ".$condition." 
					ORDER BY p.created DESC 
					LIMIT 0,1
				");
			}
			$this->kclass->boinkIt('/s.php?module=purchase&action=view&purchaseid='.$purchase['purchaseid']);
		}
	}
	//
	function view(){
		$purchase=$this->kclass->DB->queryFirst("
			SELECT p.purchaseid,p.purchaseno,p.applicant,p.ifVerify,p.verified,p.ifApprove,p.approvalRemark,p.approved,p.purpose,
					p.verifyRemark,p.remark,p.created,p.killed,p.modified,p.amount,
				u.username AS creator,
				us.username AS modifier,
				ur.username AS verifier,
				usr.username AS approver
			FROM purchase AS p
			LEFT JOIN user AS u ON (u.userid=p.creator)
			LEFT JOIN user AS us ON (us.userid=p.modifier)
			LEFT JOIN user AS ur ON (ur.userid=p.verifier)
			LEFT JOIN user AS usr ON (usr.userid=p.approver)
			WHERE p.purchaseid='".$this->kclass->input['purchaseid']."'
			LIMIT 0,1
		");
		if(!$purchase){
			$e='<li>数据错误，很抱歉</li>';
		}
		if($e){
			$this->kclass->messager(array(
				'title' => '查看采购申请单',
				'text' => '您在查看采购申请单的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$updateItem='';
		$purchase['created']=date('Y-m-d',$purchase['created']);
		$verify=$this->kclass->verify($purchase['ifVerify']);
		$approve=$this->kclass->approve($purchase['ifApprove']);
		$verifyRemark='<td></td><td></td>';
		if($purchase['modified']!=0)$modify='，由'.$purchase['modifier'].'于'.date('Y-m-d',$purchase['modified']).'修改';
		if($purchase['ifVerify']!=0){
			$pverify='，由'.$purchase['verifier'].'于'.date('Y-m-d',$purchase['verified']).'审核';
			$remark='<td>审核备注：</td><td>'.$purchase['verifyRemark'].'</td>';
		}
		if($purchase['ifApprove']!=0){
			$averify.='，由'.$purchase['approver'].'于'.date('Y-m-d H:i:s',$purchase['approved']).'审批';
			$remark.='<tr class="even"><td>审批备注：</td><td>'.$purchase['approvalRemark'].'</td><td></td><td></td></tr>';
		}
		if($purchase['ifVerify']==0 AND $purchase['ifApprove']==0){
			$remark='<td></td><td></td>';
		}
		if($purchase['ifApprove']==0){
			if($purchase['ifVerify']==0){
				$operate.='　<a href="/s.php?module=purchase&action=remove&purchaseid='.$purchase['purchaseid'].'&rt=view">删除</a>　<a href="/s.php?module=purchase&action=verify&purchaseid='.$purchase['purchaseid'].'" >审核</a>';
			}elseif($purchase['ifVerify']==1){
				$operate.='　<a href="/s.php?module=purchase&action=approve&purchaseid='.$purchase['purchaseid'].'" >审批</a>';
			}
			$operate.='　|　<span class="gray">修改：</span><a href="/s.php?module=purchase&action=update&purchaseid='.$purchase['purchaseid'].'">采购计划</a>　<a href="/s.php?module=purchase&action=update&purchaseid='.$purchase['purchaseid'].'&step=item">明细</a>';
		}elseif($purchase['ifApprove']==-1){
			$operate.='<a href="/s.php?module=purchase&action=remove&purchaseid='.$purchase['purchaseid'].'&rt=view">删除</a>';
		}

		//计划的物资
		$items=$this->kclass->DB->query("
			SELECT p.*,
				m.materialno,m.title,m.standard,m.entrustMaterialid,
				s.supplierid,s.title AS supplier,
				u.title AS unit,
				c.title AS currency,c.symbol
			FROM purchaseitem AS p
			LEFT JOIN material AS m ON (m.materialid=p.materialid)
			LEFT JOIN supplier AS s ON (s.supplierid=p.supplierid)
			LEFT JOIN unit AS u ON (u.unitid=p.unitid)
			LEFT JOIN currency AS c ON (c.currencyid=p.currencyid)
			WHERE p.killed=0 AND p.purchaseid='".$purchase['purchaseid']."'
			ORDER BY p.ordering ASC
		");
		if($this->kclass->DB->numRows()){
			$i=1;
			$currencyId=$total=$currency=array();
			while($item=$this->kclass->DB->fetchArray($items)){
				$materialno = $item['materialno'];
				$title = $item['title'];
				$standard = $item['standard'];
				$materialid = $item['materialid'];
				$addMaterial='';
				if($purchase['ifVerify']==0 AND $item['materialid']==0){
					$addMaterial='<a target="_blank" href="/s.php?module=material&action=add&itemid='.$item['itemid'].'">新增该物资</a>';
				}
				$item['arrivalTime']=date('Y-m-d',$item['arrivalTime']);
				if($item['module']=='purchaseApply'){
					$mitem=$this->kclass->DB->queryFirst("SELECT pi.*,pa.applyno,pa.purchaseApplyid FROM preinbounditem AS pi LEFT JOIN purchaseapply AS pa ON (pi.mid=pa.purchaseApplyid) WHERE pi.killed=0 AND pi.module='purchaseApply' AND pi.itemid='".$item['mitemid']."'");
					$type="采购申请";
					$typeno='<a href="/s.php?module=purchaseapply&action=view&purchaseApplyid='.$mitem['purchaseApplyid'].'">'.$mitem['applyno'].'</a>';
				}elseif($item['module']=='entrust'){
					$mitem=$this->kclass->DB->queryFirst("SELECT ei.*,e.entrustid,e.entrustno FROM entrustitem AS ei LEFT JOIN entrust AS e ON (e.entrustid=ei.entrustid) WHERE ei.killed=0 AND ei.itemid='".$item['mitemid']."'");
					$type="外发加工";
					$typeno='<a href="/s.php?module=entrust&action=view&entrustid='.$mitem['entrustid'].'">'.$mitem['entrustno'].'</a>';
						$materialid = $item['entrustMaterialid'];
						if($materialid!=""){
								$query = $this->kclass->DB->queryFirst("SELECT * FROM `material` WHERE materialid='{$materialid}'");
								$materialno = $query['materialno'];
								$standard = $query['standard'];	
								$title = $query['title'];
						}
				}
				$itemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').' small"><td>'.$i.'</td>
					<td>'.$type.'</td>
					<td>'.$typeno.'</td>
					<td><a href="/s.php?module=material&action=view&materialid='.$materialid.'">'.$materialno.'</a></td>
					<td>'.$title.'</td>
					<td>'.$standard.'</td>
					<td><a href="/s.php?module=psupplier&action=view&supplierid='.$item['supplierid'].'">'.$item['supplier'].'</a></td>
					<td>'.$item['quantity'].' '.$item['unit'].'</td>
					<td>'.$item['currency'].'</td>
					<td>'.$item['price'].'</td>
					<td>'.$item['discount'].'%</td>
					<td>'.$item['total'].'</td>
					<td>'.$item['arrivalTime'].'</td>
					<td>'.$item['remark'].'</td>
					<td>'.$addMaterial.'</td></tr>';
				if(!in_array($item['currencyid'],$currencyId)){
					$currencyId[]=$item['currencyid'];
					$total[$item['currencyid']]=$item['total'];
					$currency[$item['currencyid']]=$item['currency'];
				}else{
					$total[$item['currencyid']]+=$item['total'];
				}
				$i++;
			}
			foreach($currencyId as $key => $val){
				$totalAmount.='<span class="middle">'.$currency[$val].'：'.$total[$val].'</span>';
			}
			$itemtr.='<tr><td colspan=3 class="center bold middle">合计：</td><td colspan=12 class="center">'.$totalAmount.'</td></tr>';
		}
		$purchaseApply=$this->kclass->relatedPurchaseApply(array('purchaseid'=>$purchase['purchaseid']));
		$entrust=$this->kclass->relatedEntrust(array('purchaseid'=>$purchase['purchaseid']));
		$attach = $this->kclass->getAttachs(array('module'=>'purchase', 'mid'=>$purchase['purchaseid']));
		if($attach!= false){
			$attachs = '<div class="clear">'.$attach.'</div>';
		}
$body=<<<EOF
<div class="title"><span class="right small gray">由{$purchase['creator']}于{$purchase['created']}建立{$modify}{$pverify}。</span>{$purchase['purchaseno']}</div>
<dl id="purchase" class="tabs" style="display: block;">
<dt tabid="0">基本信息</dt>
<dt title="与此物资采购计划相关的采购申请"{$purchaseApply['off']}>采购申请单{$purchaseApply['count']}</dt>
<dt title="与此物资采购计划相关的外发加工申请"{$entrust['off']}>外发加工申请单{$entrust['count']}</dt>
<dd>
<table class="hundred">
<thead><tr><th colspan="4">采购计划单</th></tr></thead>
<tbody>
<tr class="odd">
<td width="100">编　　号：</td><td width="475">{$purchase['purchaseno']}　（{$verify} , {$approve}）</td>
<td width="100">采 购 员：</td><td>{$purchase['applicant']}</td>
</tr>
<tr class="even">
<td>申请时间：</td><td>{$purchase['created']}</td>
<td>用　　途：</td><td>{$purchase['purpose']}</td>
</tr>
<tr class="odd">
<td>备　　注：</td><td>{$purchase['remark']}</td>
{$remark}
</tbody>
</table>
<table class="hundred">
<thead><tr><th colspan="15">采购计划明细{$updateItem}</th></tr></thead>
<tbody>
<tr class="center even">
<td width="15">ID</td><td width="60">申请类型</td><td width="50">申请单编号</td><td>物资编号</td><td>物资名称</td><td>物资规格</td><td>供应商</td><td>数量/单位</td><td>币种</td><td>单价</td><td>折扣</td><td>总价</td><td>到货时间</td><td>备注</td><td></td></td>
</tr>
{$itemtr}
</tbody>
</table>
{$attachs}
</dd>
<dd>{$purchaseApply['panel']}</dd>
<dd>{$entrust['panel']}</dd>
EOF;
		$this->kclass->page['title'].='采购计划单';
		$this->kclass->page['onload'].='dc.tabhover();dc.tabs({\'id\':\'purchase\'})';
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - <a href="/s.php?module=purchase&action=list">采购计划单列表</a> - 采购计划单','right' => '<span class = "small">操作：</span> '.$this->kclass->iif($purchase['killed']==0,'<a href="/s.php?module=purchase&action=add">新建</a>'.$operate,'<a href="/s.php?module=purchase&action=restore&purchaseid='.$this->kclass->input['purchaseid'].'&rt=view">恢复</a>'),'body'=>$body));
	}
	// the form for add a purchase
	function add(){
		$start=strtotime(date('Y-m-d',TIMENOW));
		$end=strtotime(date('Y-m-d',$start))+86400;
		$counter=$this->kclass->DB->queryFirst("SELECT COUNT(*) AS count FROM purchase WHERE created>='".$start."' AND created<='".$end."'");
		$purchaseno=$this->kclass->id(array('purchase'=>TIMENOW,'number'=>$counter['count']));
		if($this->kclass->input['step']==''){
			$applys=$this->kclass->DB->query("
				SELECT `purchaseapply`.*,
					d.title AS department
				FROM `purchaseapply`
				LEFT JOIN department AS d ON (d.departmentid=`purchaseapply`.departmentid)
				WHERE `purchaseapply`.killed=0 AND `purchaseapply`.ifVerify=1
			");
			if($this->kclass->DB->numRows()){
				$i=1;
				while($apply=$this->kclass->DB->fetchArray($applys)){
					//申请的物资
					$items=$this->kclass->DB->query("
						SELECT p.itemid,p.module,p.mid,p.materialid,p.materialno AS newMaterialno,p.title AS newTitle,p.standard AS newStandard,p.quantity,p.dateline,p.remark,
							m.materialno,m.title,m.standard,
							u.title AS unit
						FROM preinbounditem AS p
						LEFT JOIN material AS m ON (m.materialid=p.materialid)
						LEFT JOIN unit AS u ON (u.unitid=p.unitid)
						WHERE p.module='purchaseApply' AND p.killed=0 AND p.ifChooser=0 AND p.mid='".$apply['purchaseApplyid']."' 
						ORDER BY p.created ASC
					");
					if($this->kclass->DB->numRows()){
						$k=1;
						$itemtr.='<tbody><tr class="bold "><td colspan=8>采购申请单编号：<span class="middle bold">'.$apply['applyno'].'</span>　部门：'.$apply['department'].'　申请人：'.$apply['applicant'].'　申请时间：'.date('Y-m-d',$apply['created']).'</td></tr><tr class="center even" nohover><td width="30">ID</td><td>物资编号</td><td>物资名称</td><td>物资规格</td><td>数量/单位</td><td>到货时间</td><td>备注</td><td>选</td></tr>';
						while($item=$this->kclass->DB->fetchArray($items)){
							$itemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').'"><input type="hidden" name="m['.$i.']" value="purchaseApply" /><input type="hidden" name="itemId['.$i.']" value="'.$item['itemid'].'" />';
							if($item['materialid']==0){
								$itemtr.='<td>'.$k.'</td>
									<td>'.$item['newMaterialno'].'</td>
									<td>'.$item['newTitle'].'</td>
									<td>'.$item['newStandard'].'</td>';
							}else{
								$itemtr.='<td>'.$k.'</td>
									<td><a href="/s.php?module=material&action=view&materialid='.$item['materialid'].'">'.$item['materialno'].'</a></td>
									<td>'.$item['title'].'</td>
									<td>'.$item['standard'].'</td>';
							}
							$itemtr.='<td>'.$item['quantity'].$item['unit'].'</td><td>'.date('Y-m-d',$item['dateline']).'</td><td>'.$item['remark'].'</td><td><input type="checkbox" name="itemSelect['.$i.']" value="'.$item['itemid'].'"/></td></tr>';
							$k++;$i++;
						}
					}
				}
				$itemtr.='';
			}
			// 外发加工申请明细
			$entrusts=$this->kclass->DB->query("SELECT * FROM `entrust`	WHERE `entrust`.killed=0 AND `entrust`.ifVerify=1");
			if($this->kclass->DB->numRows()){
				$j=$this->kclass->iif($i,$i,1);
				while($entrust=$this->kclass->DB->fetchArray($entrusts)){
					//申请的物资
					$items=$this->kclass->DB->query("
						SELECT e.itemid,e.entrustid,e.materialid,e.quantity,e.dateline,e.remark,
							m.materialno,m.title,m.standard,
							u.title AS unit
						FROM entrustitem AS e
						LEFT JOIN material AS m ON (m.materialid=e.materialid)
						LEFT JOIN unit AS u ON (u.unitid=e.unitid)
						WHERE e.killed=0 AND e.ifChooser=0 AND e.entrustid='".$entrust['entrustid']."' 
						ORDER BY e.created ASC
					");
					if($this->kclass->DB->numRows()){
						$k=1;
						$itemtr.='<tbody><tr class="bold "><td colspan=8>外发加工申请单编号：<span class="middle bold"><a href="/s.php?module=entrust&action=view&entrustid='.$entrust['entrustid'].'">'.$entrust['entrustno'].'</a></span>　申请人：'.$entrust['applicant'].'　申请时间：'.date('Y-m-d',$entrust['created']).'　特别说明：'.$entrust['special'].'</td></tr><tr class="center even" nohover><td width="30">ID</td><td>物资编号</td><td>物资名称</td><td>物资规格</td><td>数量/单位</td><td>加工交期</td><td>加工要求</td><td>选</td></tr>';
						while($item=$this->kclass->DB->fetchArray($items)){
							$itemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').'">
								<input type="hidden" name="m['.$j.']" value="entrust" />
								<input type="hidden" name="itemId['.$j.']" value="'.$item['itemid'].'" />
								<td>'.$k.'</td>
								<td><a href="/s.php?module=material&action=view&materialid='.$item['materialid'].'">'.$item['materialno'].'</a></td>
								<td>'.$item['title'].'</td>
								<td>'.$item['standard'].'</td>
								<td>'.$item['quantity'].$item['unit'].'</td><td>'.date('Y-m-d',$item['dateline']).'</td><td>'.$item['remark'].'</td><td><input type="checkbox" name="itemSelect['.$j.']" value="'.$item['itemid'].'"/></td></tr>';
							$k++;$j++;
						}
					}
				}
				$itemtr.='';
			}
			$body=<<<EOF
<form action="/s.php?module=purchase&action=add" method="post">
<input type="hidden" name="module" value="purchase" />
<input type="hidden" name="action" value="add" />
<input type="hidden" name="step" value="2" />
<table><thead><tr><th colspan=8>选择采购计划申请单明细 <span class="small red">(由采购部填写)</span></th></tr></thead>
{$itemtr}
<tr class="center">
<td colspan=8><input type="submit" value="提交"><input type="reset" value="重置"></td>
</tr>
</tbody></table>
</form>
EOF;
			$this->kclass->tbline+=4;
			$this->kclass->page['title'].='选择采购计划单明细';
			$this->kclass->page['onload'].='dc.tabhover()';
			$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - <a href="/s.php?module=purchase&action=list">采购计划单列表</a> - 新建采购计划单', 'right'=>'<a href="/s.php?module=purchase&action=list">返回列表</a>','body'=>$body));
		}elseif($this->kclass->input['step']==2){
			$upload=$this->kclass->upload(array('title'=>'相关附件：'));
			$j=count($this->kclass->input['itemId']);
			$k=1;
			for($i=1;$i<$j+1;$i++){
				if($this->kclass->input['itemSelect'][$i]>0){
					if($this->kclass->input['m'][$i]=='purchaseApply'){
						$item=$this->kclass->DB->queryFirst("
							SELECT p.itemid,p.materialid,p.versionid,p.unitid,p.materialno AS newMaterialno,p.title AS newTitle,p.standard AS newStandard,p.quantity,p.dateline,p.remark,
								m.materialno,m.title,m.standard,
								u.title AS unit,
								pa.purchaseApplyid,pa.applyno,
								mv.title AS version
							FROM preinbounditem AS p
							LEFT JOIN material AS m ON (m.materialid=p.materialid)
							LEFT JOIN unit AS u ON (u.unitid=p.unitid)
							LEFT JOIN purchaseapply AS pa ON (pa.purchaseApplyid=p.mid)
							LEFT JOIN materialversion AS mv ON (mv.versionid=p.versionid)
							WHERE p.killed=0 AND p.itemid='".$this->kclass->input['itemSelect'][$i]."'
							ORDER BY p.created ASC
						");
						$type="采购申请";
						$typeno='<a href="/s.php?module=purchaseapply&action=view&purchaseApplyid='.$item['purchaseApplyid'].'">'.$item['applyno'].'</a>';
						$materialno=$item['materialno'];
						$title=$item['title'];
						$standard=$item['standard'];
					}elseif($this->kclass->input['m'][$i]=='entrust'){
						$item=$this->kclass->DB->queryFirst("
							SELECT p.itemid,p.materialid,p.versionid,p.unitid,p.quantity,p.dateline,p.remark,
								m.entrustMaterialid,
								u.title AS unit,
								e.entrustid,entrustno,
								mv.title AS version
							FROM entrustitem AS p
							LEFT JOIN material AS m ON (m.materialid=p.materialid)
							LEFT JOIN unit AS u ON (u.unitid=p.unitid)
							LEFT JOIN entrust AS e ON (e.entrustid=p.entrustid)
							LEFT JOIN materialversion AS mv ON (mv.versionid=p.versionid)
							WHERE p.killed=0 AND p.itemid='".$this->kclass->input['itemSelect'][$i]."'
							ORDER BY p.created ASC
						");
						$type="外发加工";
						$typeno='<a href="/s.php?module=entrust&action=view&entrustid='.$item['entrustid'].'">'.$item['entrustno'].'</a>';
						$materialid = $item['entrustMaterialid'];
						if($materialid!=""){
								$query = $this->kclass->DB->queryFirst("SELECT * FROM `material` WHERE materialid='{$materialid}'");
								$materialno = $query['materialno'];
								$standard = $query['standard'];	
								$title = $query['title'];
						}
					}
					$itemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').'">
						<input type="hidden" name="m['.$k.']" value="'.$this->kclass->input['m'][$i].'" />
						<input type="hidden" name="itemId['.$k.']" value="'.$item['itemid'].'">
						<input type="hidden" name="itemUnitid['.$k.']" value="'.$item['unitid'].'">
						<input type="hidden" name="itemVersionid['.$k.']" value="'.$item['versionid'].'">
						<td>'.$k.'</td>
						<td>'.$type.'</td>
						<td>'.$typeno.'</td>
						<td>'.$materialno.'</td>
						<td>'.$title.'</td>
						<td>'.$standard.'</td>
						<td>'.$item['version'].'</td>
						<td><input type="text" name="itemQuantity['.$k.']" value="'.$item['quantity'].'" size="9"/> '.$item['unit'].'</td>
						<td>'.$this->kclass->chooserSupplier(array('hName'=>'itemSupplierid['.$k.']','hId'=>'itemSupplierid'.$k,'name'=>'itemSupplier['.$ki.']','id'=>'itemSupplier'.$k,'line'=>$k,'width'=>150)).'</td>
						<td>'.$this->kclass->chooserCurrency(array('name'=>'itemCurrencyid['.$k.']','width'=>50,'hasBlank'=>1,'selectedid'=>3)).'</td>
						<td><input type="text" name="itemPrice['.$k.']" size="5" value="0"/></td>
						<td><input type="text" name="itemDiscount['.$k.']" value="100" size="3"/></td>
						<td><input type="text" id="arrivalTime'.$k.'" name="itemArrivalTime['.$k.']" size="10" value="'.date('Y-m-d',$item['dateline']).'" /></td></tr>';
					if($i<$counter){
						$id.='#arrivalTime'.$k.',';
					}else{
						$id.='#arrivalTime'.$k;
					}
					$k++;
				}
			}

$body=<<<EOF
<form action="/s.php?module=purchase&action=insert" name="purchase" method="post">
<input type="hidden" name="module" value="purchase" />
<input type="hidden" name="action" value="insert" />
<input type="hidden" name="purchaseno" value="{$purchaseno}" />
<input type="hidden" name="step" value="2" />
<table class="hundred">
<thead><tr><th colspan="4">新建采购计划单</th></tr></thead>
<tbody>
<tr class="odd">
<td>采购计划单编号：<span class="red bold">*</span></td><td><span class="middle bold darkred">{$purchaseno}</span> <span class="gray small">此为预估编号，保存后才可确定</span></td>
<td>采 购 员：<span class="red bold">*</span></td><td><input type="text" name="applicant" value="{$this->kclass->user['realname']}"></td>
</tr>
<tr class="even">
<td>用　　途：<span class="red bold">*</span></td>
<td><textarea type="text" name="purpose" style="width:444px;height:111px"></textarea>
<td>备　　注：</td>
<td><textarea type="text" name="remark" style="width:444px;height:111px"></textarea>
</tr>
</tbody>
</table>
<table class="hundred">
<thead><tr><th colspan="13">计划采购明细</th></tr></thead>
<tbody>
<tr class="even center"><td width="15">ID</td><td width="60">申请类型</td><td width="80">申请单编号</td><td width="60">物资编号</td><td>物资名称</td><td>物资规格</td><td width="60">版本</td><td width="100">数量 <span class="red bold">*</span></td><td width="150">供应商</td><td width="30">币种 <span class="red bold">*</span></td><td width="50">单价</td><td width="50">折扣</td><td width="90">到货时间</td></tr>
</tbody><tbody class="small">
{$itemtr}
<tr class="even">
<td class="small gray" colspan="13">
注意事项：<br>
①如果所列表格不够，那么在提交保存后再点击“修改”，来添加所要采购的物资；<br>
②如果不从“采购申请单中”选择物资，而是采购新物资，那么就填写“物资编号”、“物资名称”、“物资规格”，或者在物资列表中选择；<br>
③“采购申请单及其物资”中 灰色的物资表示已经被选中过，不可以继续选择；<br>
④时间的格式为2012-02-06，中间用半角短横杠隔开；<br>
⑤从库中物资选择时，“单位”不需要选，但是如果采购物资不存在库中时，就必须要选择“单位”
</td>
</tr>
</tbody>
</table>
{$upload}
<table class="tlist tsuite" align="center" width="100%">
<tbody>
<tr class="odd small" nohover>
<td class="center"><input type="submit" id="submitButton" value="  提交  " accesskey="s"><input type="reset" value="  复位  "/></td>
</tr>
</tbody>
</table>
</form>
EOF;

			$this->kclass->tbline+=4;
			$this->kclass->page['title'].='新建采购计划单';
			$this->kclass->page['onload'].='var dates=$(\''.$id.'\').datepicker({onSelect:function(selectedDate){instance=$(this).data(\'datepicker\'),date=$.datepicker.parseDate(instance.settings.dateFormat || $.datepicker._defaults.dateFormat,selectedDate,instance.settings);}});dc.tabhover();';
			$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - <a href="/s.php?module=purchase&action=list">采购计划单列表</a> - 新建采购计划单', 'right'=>'<a href="/s.php?module=purchase&action=list">返回列表</a>','body'=>$body));
		}
	}
			// insert purchase
	function insert(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e='<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			if($this->kclass->input['step']==1){
				if($this->kclass->input['itemSelect']==''){
					$e.='<li>请从采购申请单中选择采购计划单的明细</li>';
				}
			}else{
				if(!$this->kclass->input['applicant']){
					$e .= '<li>请选择采购计划单中的 采购员。</li>';
				}
				if(!$this->kclass->input['purpose']){
					$e .= '<li>请选择采购计划单中的 目的。</li>';
				}
				$j=count($this->kclass->input['itemId']);
				$hasItem=0;
				for($i=1;$i<$j+1;$i++){
					if($this->kclass->input['itemQuantity'][$i]>0 AND $this->kclass->input['itemDiscount'][$i]<=100 AND $this->kclass->input['itemDiscount'][$i]>0){
						$hasItem=1;
					}
				}
				if($hasItem==0){
					$e .= '<li>需要填写至少有一条采购计划单明细，才能建立采购计划单。</li>';
				}
			}
		}
		if($e){
			$this->kclass->messager(array(
				'title' => '新建采购计划单',
				'text' => '您在新建采购计划单的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$start=strtotime(date('Y-m-d',TIMENOW));
		$end=strtotime(date('Y-m-d',$start))+86400;
		$counter=$this->kclass->DB->queryFirst("SELECT COUNT(*) AS count FROM purchase WHERE created>='".$start."' AND created<='".$end."'");
		$purchaseno=$this->kclass->id(array('purchase'=>TIMENOW,'number'=>$counter['count']));
		if($this->kclass->input['step']==2){
			$this->kclass->DB->query("
				INSERT INTO purchase 
					(purchaseno,applicant,purpose,remark,created,creator)
				VALUES 
					('".$purchaseno."','".$this->kclass->input['applicant']."','".$this->kclass->input['purpose']."','".$this->kclass->input['remark']."','".TIMENOW."','".$this->kclass->user['userid']."')
			");
			$purchaseid=$this->kclass->DB->insertID();
			$key=array();
			$count=count($this->kclass->input['itemId']);
			$amount=0;
			for($m=1;$m<=$count;$m++){
				if($this->kclass->input['itemArrivalTime'][$m]){
					$sd=explode('-',$this->kclass->input['itemArrivalTime'][$m]);
					$arrivalTime=mktime(0,0,0,$sd[1],$sd[2],$sd[0]);
				}else{
					$arrivalTime=0;
				}
				if($this->kclass->input['m'][$m]=='purchaseApply'){
					$item=$this->kclass->DB->queryFirst("
						SELECT itemid,materialid,mid AS applyid,unitid
						FROM `preinbounditem` 
						WHERE killed=0 AND module='purchaseApply' AND itemid='".$this->kclass->input['itemId'][$m]."'
					");
					$mid=$item['applyid'];
					$this->kclass->DB->query("UPDATE `preinbounditem` SET ifChooser=1 WHERE itemid='".$this->kclass->input['itemId'][$m]."'");
				}elseif($this->kclass->input['m'][$m]=='entrust'){
					$item=$this->kclass->DB->queryFirst("
						SELECT itemid,materialid,entrustid,unitid,requirement 
						FROM `entrustitem` 
						WHERE killed=0 AND itemid='".$this->kclass->input['itemId'][$m]."'
					");
					$mid=$item['entrustid'];
					$this->kclass->DB->query("UPDATE `entrustitem` SET ifChooser=1 WHERE itemid='".$this->kclass->input['itemId'][$m]."'");
				}
				$discount=intval($this->kclass->input['itemDiscount'][$m])/100;
				$price=$this->kclass->iif(floatval($this->kclass->input['itemPrice'][$m])=='',0,floatval($this->kclass->input['itemPrice'][$m]));
				$total=$this->kclass->input['itemQuantity'][$m]*$price*$discount;
				$total=number_format($total,3,'.','');
				$amount+=$total;
				$this->kclass->DB->query("
					INSERT INTO purchaseitem
						(materialid,purchaseid,module,mid,mitemid,versionid,supplierid,quantity,currencyid,unitid,arrivalTime,price,total,requirement,created,creator)
					VALUES
						('".$item['materialid']."','".$purchaseid."','".$this->kclass->input['m'][$m]."','".$mid."','".$this->kclass->input['itemId'][$m]."','".$this->kclass->input['itemVersionid'][$m]."','".$this->kclass->input['itemSupplierid'][$m]."','".$this->kclass->input['itemQuantity'][$m]."','".$this->kclass->input['itemCurrencyid'][$m]."','".$item['unitid']."','".$arrivalTime."','".$price."','".$total."','".$item['requirement']."','".TIMENOW."','".$this->kclass->user['userid']."')
				");
			}
			if($this->kclass->input['itemId'][$m]>0){
				$this->kclass->DB->query("UPDATE requirementitem SET ifChooser=1 WHERE itemid='".$this->kclass->input['itemId'][$m]."'");
			}
			$this->kclass->updateAttachs(array('module'=>'purchase', 'mid'=>$purchaseid));
			$this->kclass->messager(array(
				'title' => '新建采购计划单',
				'text' => '采购计划单 【<b>'.$purchaseno.'</b>】 已新建成功!返回添加申请采购物资',
				'url' => '/s.php?module=purchase&action=view&purchaseid='.$purchaseid,
				'sec' => 2
			));
		}
	}
	//
	function update(){
		$purchase=$this->kclass->DB->queryFirst("
			SELECT purchaseid,purchaseno,applicant,currencyid,ifVerify,ifApprove,purpose,remark,created,amount
			FROM purchase
			WHERE killed=0 AND purchaseid='".$this->kclass->input['purchaseid']."'
			LIMIT 0,1
		");
		if(!$purchase OR $purchase['ifApprove']!=0){
			$this->kclass->messager(array(
				'title' => '修改采购',
				'text' => '选择的采购计划单数据错误',
				'url' => '/s.php?module=purchase&action=list',
				'sec' => 2
			));
		}
		if($this->kclass->input['step']=='item'){
			$purchaseItems=$this->kclass->DB->query("
				SELECT p.materialid,p.itemid,p.mitemid,p.mid,p.module,p.unitid,p.arrivalTime,p.price,p.quantity,p.discount,p.supplierid,p.currencyid,
					m.materialno,m.title,m.standard,
					s.supplierno,s.title AS supplier,
					u.title AS unit
				FROM purchaseitem AS p
				LEFT JOIN material AS m ON (m.materialid=p.materialid)
				LEFT JOIN supplier AS s ON (s.supplierid=p.supplierid)
				LEFT JOIN unit AS u ON (u.unitid=p.unitid)
				WHERE p.killed=0 AND p.purchaseid='".$this->kclass->input['purchaseid']."'
				ORDER BY p.itemid ASC
			");
			if($this->kclass->DB->numRows()){
				$m=1;
				while($purchaseItem=$this->kclass->DB->fetchArray($purchaseItems)){
					$orgItemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').' small"><input type="hidden" name="m['.$m.']" value="'.$purchaseItem['module'].'" /><input type="hidden" name="purchaseItemId['.$m.']" value="'.$purchaseItem['itemid'].'" /><input type="hidden" name="applyItemId['.$m.']" value="'.$purchaseItem['mitemid'].'" /><td>'.$m.'</td>';
					if($purchaseItem['module']=='purchaseApply'){
						$mitem=$this->kclass->DB->queryFirst("SELECT pi.*,pa.applyno,pa.purchaseApplyid FROM preinbounditem AS pi LEFT JOIN purchaseapply AS pa ON (pi.mid=pa.purchaseApplyid) WHERE pi.killed=0 AND pi.module='purchaseApply' AND pi.itemid='".$purchaseItem['mitemid']."'");
						$type="采购申请";
						$typeno='<a href="/s.php?module=purchaseapply&action=view&purchaseApplyid='.$mitem['purchaseApplyid'].'">'.$mitem['applyno'].'</a>';
					}elseif($purchaseItem['module']=='entrust'){
						$mitem=$this->kclass->DB->queryFirst("SELECT ei.*,e.entrustid,e.entrustno FROM entrustitem AS ei LEFT JOIN entrust AS e ON (e.entrustid=ei.entrustid) WHERE ei.killed=0 AND ei.itemid='".$purchaseItem['mitemid']."'");
						$type="外发加工";
						$typeno='<a href="/s.php?module=entrust&action=view&entrustid='.$mitem['entrustid'].'">'.$mitem['entrustno'].'</a>';
					}
					$materialno=$this->kclass->iif($purchaseItem['materialid']==0,$mitem['materialno'],'<a href="/s.php?module=material&action=view&materialid='.$purchaseItem['materialid'].'">'.$purchaseItem['materialno'].'</a>');
					$title=$this->kclass->iif($purchaseItem['materialid']==0,$mitem['title'],$purchaseItem['title']);
					$standard=$this->kclass->iif($purchaseItem['materialid']==0,$mitem['stanard'],$purchaseItem['standard']);

					$orgItemtr.='<td>'.$type.'</td><td>'.$typeno.'</td><td>'.$materialno.'</td><td>'.$title.'</td><td>'.$standard.'</td>
						<td><input type="hidden" name="olUnitid['.$m.']" value="'.$purchaseItem['unitid'].'"><input type="text" name="olQuantity['.$m.']" value="'.$purchaseItem['quantity'].'" size="9"/> '.$purchaseItem['unit'].'</td>
						<td>'.$this->kclass->chooserSupplier(array('hName'=>'olSupplierid['.$m.']','hId'=>'olSupplierid'.$m,'name'=>'olMaterial['.$m.']','id'=>'olMaterial'.$m,'line'=>$m,'width'=>80,'selectedid'=>$purchaseItem['supplierid'],'value'=>$purchaseItem['supplier'].' ('.$purchaseItem['supplierno'].')')).'</td>
						<td>'.$this->kclass->chooserCurrency(array('name'=>'olCurrencyid['.$m.']','width'=>50,'hasBlank'=>1,'selectedid'=>$purchaseItem['currencyid'])).'</td>
						<td><input type="text" name="olPrice['.$m.']" size="5" value="'.$purchaseItem['price'].'"/></td>
						<td><input type="text" name="olDiscount['.$m.']" value="'.$purchaseItem['discount'].'" size="3"/></td>
						<td><input type="text" id="olArrivalTime'.$m.'" name="olArrivalTime['.$m.']" size="10" value="'.date('Y-m-d',$purchaseItem['arrivalTime']).'" /></td>
						<td><input type="text" name="olRemark['.$i.']" value="'.$purchaseItem['remark'].'" size="10"/></td>
						<td><input type="checkbox" name="itemKill['.$m.']" value="'.$purchaseItem['itemid'].'"/></td></tr>';
					$id.='#olArrivalTime'.$m.',';
					$m++;
				}
			}

			$applys=$this->kclass->DB->query("
				SELECT `purchaseapply`.*,
					d.title AS department
				FROM `purchaseapply`
				LEFT JOIN department AS d ON (d.departmentid=`purchaseapply`.departmentid)
				WHERE `purchaseapply`.killed=0 AND `purchaseapply`.ifVerify=1 AND `purchaseapply`.ifApprove=1
			");
			if($this->kclass->DB->numRows()){
				$i=1;
				$applyItem=$this->kclass->DB->queryFirst("SELECT	COUNT(*) AS count FROM preinbounditem AS p LEFT JOIN `purchaseapply` AS pa ON (p.mid=pa.purchaseApplyid) WHERE p.module='purchaseApply' AND p.killed=0 AND p.ifChooser=0 AND pa.killed=0 AND pa.ifVerify=1 LIMIT 0,1");
				while($apply=$this->kclass->DB->fetchArray($applys)){
					//申请的物资
					$items=$this->kclass->DB->query("
						SELECT p.itemid,p.module,p.mid,p.materialid,p.materialno AS newMaterialno,p.title AS newTitle,p.standard AS newStandard,
								p.quantity,p.unitid,p.dateline,p.remark,
							m.materialno,m.title,m.standard,
							u.title AS unit
						FROM preinbounditem AS p
						LEFT JOIN material AS m ON (m.materialid=p.materialid)
						LEFT JOIN unit AS u ON (u.unitid=p.unitid)
						WHERE p.module='purchaseApply' AND p.killed=0 AND p.ifChooser=0 AND p.mid='".$apply['purchaseApplyid']."' 
						ORDER BY p.ordering ASC
					");
					if($this->kclass->DB->numRows()){
						$k=1;
						$itemtr.='<tbody><tr class="bold "><td colspan=13>采购申请单编号：<span class="middle bold"><a href="/s.php?module=purchaseapply&action=view&applyid='.$apply['purchaseApplyid'].'">'.$apply['applyno'].'</a></span>　部门：'.$apply['department'].'　申请人：'.$apply['applicant'].'　申请时间：'.date('Y-m-d',$apply['created']).'</td></tr><tr class="center even" nohover><td width="30">ID</td><td>物资编号</td><td>物资名称</td><td>物资规格</td><td>数量/单位</td><td width="80">供应商</td><td width="30">币种 <span class="red bold">*</span></td><td width="50">单价</td><td width="50">折扣</td><td width="90">预计到货时间</td><td>备注</td><td>选</td></tr>';
						while($item=$this->kclass->DB->fetchArray($items)){
							$itemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').' small"><input type="hidden" name="mo['.$i.']" value="purchaseApply" /><input type="hidden" name="itemId['.$i.']" value="'.$item['itemid'].'" />';
							if($item['materialid']==0){
								$itemtr.='<td>'.$k.'</td>
									<td>'.$item['newMaterialno'].'</td>
									<td>'.$item['newTitle'].'</td>
									<td>'.$item['newStandard'].'</td>';
							}else{
								$itemtr.='<td>'.$k.'</td>
									<td><a href="/s.php?module=material&action=view&materialid='.$item['materialid'].'">'.$item['materialno'].'</a></td>
									<td>'.$item['title'].'</td>
									<td>'.$item['standard'].'</td>';
							}
							$itemtr.='<td align="right"><input type="hidden" name="itemUnitid['.$i.']" value="'.$item['unitid'].'"><input type="text" name="itemQuantity['.$i.']" value="'.$item['quantity'].'" size="9"/> '.$item['unit'].'</td>
							<td>'.$this->kclass->chooserSupplier(array('hName'=>'itemSupplierid['.$i.']','hId'=>'itemSupplierid'.$i,'name'=>'itemSupplier['.$m.']','id'=>'itemSupplier'.$i,'line'=>$i,'width'=>80)).'</td>
							<td>'.$this->kclass->chooserCurrency(array('name'=>'itemCurrencyid['.$i.']','width'=>50,'hasBlank'=>1,'selectedid'=>3)).'</td>
							<td><input type="text" name="itemPrice['.$i.']" size="5" /></td>
							<td><input type="text" name="itemDiscount['.$i.']" value="100" size="3"/></td>
							<td><input type="text" id="arrivalTime'.$i.'" name="itemArrivalTime['.$i.']" size="10" value="'.date('Y-m-d',$item['dateline']).'" /></td>
							<td><input type="text" name="itemRemark['.$i.']" value="'.$item['remark'].'" size="15"/></td>
							<td><input type="checkbox" name="itemSelect['.$i.']" value="'.$item['itemid'].'"/></td></tr>';
							if($i<$applyItem['count']){
								$id.='#arrivalTime'.$i.',';
							}else{
								$id.='#arrivalTime'.$i;
							}
							$k++;$i++;
						}
					}
				}
				$itemtr.='';
			}
			// 外发加工申请明细
			$entrusts=$this->kclass->DB->query("SELECT * FROM `entrust`	WHERE `entrust`.killed=0 AND `entrust`.ifVerify=1");
			if($this->kclass->DB->numRows()){
				$j=$this->kclass->iif($i,$i,1);
				while($entrust=$this->kclass->DB->fetchArray($entrusts)){
					//申请的物资
					$items=$this->kclass->DB->query("
						SELECT e.itemid,e.entrustid,e.materialid,e.quantity,e.dateline,e.remark,
							m.materialno,m.title,m.standard,
							u.title AS unit
						FROM  entrustitem AS e
						LEFT JOIN material AS m ON (m.materialid=e.materialid)
						LEFT JOIN unit AS u ON (u.unitid=e.unitid)
						WHERE e.killed=0 AND e.ifChooser=0 AND e.entrustid='".$entrust['entrustid']."' 
						ORDER BY e.itemid ASC
					");
					if($counter=$this->kclass->DB->numRows()){
						$k=1;
						$itemtr.='<tbody><tr class="bold "><td colspan=15>外发加工申请单编号：<span class="middle bold"><a href="/s.php?module=entrust&action=view&entrustid='.$entrust['entrustid'].'">'.$entrust['entrustno'].'</a></span>　申请人：'.$entrust['applicant'].'　申请时间：'.date('Y-m-d',$entrust['applied']).'　特别说明：'.$entrust['special'].'</td></tr><tr class="center even" nohover><td width="30">ID</td><td>物资编号</td><td>物资名称</td><td>物资规格</td><td>数量/单位</td><td width="80">供应商</td><td width="30">币种 <span class="red bold">*</span></td><td width="50">单价</td><td width="50">折扣</td><td width="90">加工交期</td><td>加工要求</td><td>选</td></tr>';
						while($item=$this->kclass->DB->fetchArray($items)){
							$itemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').' small"><input type="hidden" name="mo['.$j.']" value="entrust" /><input type="hidden" name="itemId['.$j.']" value="'.$item['itemid'].'" />';
							$itemtr.='<td>'.$k.'</td>
								<td><a href="/s.php?module=material&action=view&materialid='.$item['materialid'].'">'.$item['materialno'].'</a></td>
								<td>'.$item['title'].'</td>
								<td>'.$item['standard'].'</td>
								<td align="right"><input type="hidden" name="itemUnitid['.$i.']" value="'.$item['unitid'].'"><input type="text" name="itemQuantity['.$i.']" value="'.$item['quantity'].'" size="9"/> '.$item['unit'].'</td>
								<td>'.$this->kclass->chooserSupplier(array('name'=>'itemSupplierid['.$i.']','width'=>80,'hasBlank'=>1)).'</td>
								<td>'.$this->kclass->chooserCurrency(array('name'=>'itemCurrencyid['.$i.']','width'=>50,'hasBlank'=>1,'selectedid'=>3)).'</td>
								<td><input type="text" name="itemPrice['.$i.']" size="5" /></td>
								<td><input type="text" name="itemDiscount['.$i.']" value="100" size="3"/></td>
								<td><input type="text" id="arrivalTime'.$i.'" name="itemArrivalTime['.$i.']" size="10" value="'.date('Y-m-d',$purchaseItem['dateline']).'" /></td>
								<td><input type="text" name="itemRemark['.$i.']" value="'.$item['remark'].'" size="15"/></td>
								<td><input type="checkbox" name="itemSelect['.$i.']" value="'.$item['itemid'].'"/></td></tr>';
							if($i<$counter){
								$id.=',#arrivalTime'.$i;
							}else{
								$id.=',#arrivalTime'.$i;
							}
							$k++;$j++;
						}
					}
				}
				$itemtr.='';
			}
			$body=<<<EOF
<form action="/s.php?module=purchase&action=doupdate" method="post">
<input type="hidden" name="module" value="purchase" />
<input type="hidden" name="action" value="doupdate" />
<input type="hidden" name="purchaseid" value="{$this->kclass->input['purchaseid']}" />
<input type="hidden" name="step" value="item" />
<table class="hundred"><thead><tr><th colspan=14>当前采购计划申请单明细</th></tr></thead>
<tbody>
<tr class="center even" nohover><td width="15">ID</td><td width="60">申请类型</td><td width="50">申请单编号</td><td>物资编号</td><td>物资名称</td><td>物资规格</td><td>数量/单位</td><td width="80">供应商</td><td width="30">币种 <span class="red bold">*</span></td><td width="50">单价</td><td width="50">折扣</td><td width="90">到货时间</td><td>备注</td><td>删</td></tr>
{$orgItemtr}
</tbody>
</table>
<table class="hundred"><thead><tr><th colspan=13>选择采购计划申请单明细</th></tr></thead>
{$itemtr}
<tr class="center">
<td colspan=13><input type="submit" value="提交"><input type="reset" value="重置"></td>
</tr>
</tbody></table>
</form>
EOF;
			$this->kclass->tbline+=4;
			$this->kclass->page['title'].='选择采购计划单明细';
			$this->kclass->page['onload'].='var dates=$(\''.$id.'\').datepicker({onSelect:function(selectedDate){instance=$(this).data(\'datepicker\'),date=$.datepicker.parseDate(instance.settings.dateFormat || $.datepicker._defaults.dateFormat,selectedDate,instance.settings);}});dc.tabhover()';
			$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - <a href="/s.php?module=purchase&action=list">采购计划单列表</a> - 新建采购计划单', 'right'=>'<a href="/s.php?module=purchase&action=list">返回列表</a>','body'=>$body));
		}else{
			$currency=$this->kclass->chooserCurrency(array('name'=>'currencyid','width'=>'80','hasBlank'=>1,'selectedid'=>$purchase['currencyid']));
			$upload=$this->kclass->upload(array('title'=>'相关附件：','module'=>'purchase','mid'=>$purchase['purchaseid']));
$body=<<<EOF
<form action="/s.php?module=purchase&action=doupdate" name="purchase" method="post">
<input type="hidden" name="module" value="purchase" />
<input type="hidden" name="action" value="doupdate" />
<input type="hidden" name="purchaseid" value="{$this->kclass->input['purchaseid']}" />
<table class="hundred">
<thead><tr><th colspan="4">新建采购计划单</th></tr></thead>
<tbody>
<tr class="odd">
<td>采购计划单编号：<span class="red bold">*</span></td><td><input type="text" name="purchaseno" value="{$purchase['purchaseno']}"> <span class="small gray">编号不会被修改</span></td>
<td>采购员：<span class="red bold">*</span></td><td><input type="text" name="applicant" value="{$purchase['applicant']}"></td>
</tr>
<tr class="even">
<td>目的：<span class="red bold">*</span></td>
<td><textarea type="text" name="purpose" style="width:444px;height:111px;">{$purchase['purpose']}</textarea>
<td>备注：</td>
<td><textarea type="text" name="remark" style="width:444px;height:111px;">{$purchase['remark']}</textarea>
</tr>
</tbody>
</table>
{$upload}
<table class="tlist tsuite" align="center" width="100%">
<tbody>
<tr class="odd" colspan=4 nohover>
<td class="center"><input type="submit" id="submitButton" value="  提交  " accesskey="s"><input type="reset" value="  复位  "/></td>
</tr>
</tbody>
</table>
</form>
EOF;
			$this->kclass->tbline+=4;
			$this->kclass->page['title'].='修改采购计划单';
			$this->kclass->page['onload'].='dc.tabhover();';
			$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - <a href="/s.php?module=purchase&action=view&purchaseid='.$this->kclass->input['purchaseid'].'">查看采购计划单</a> - 修改采购计划单', 'right'=>'<a href="/s.php?module=purchase&action=update">返回列表</a>　|　<a href="/s.php?module=purchase&action=view&purchaseid='.$this->kclass->input['purchaseid'].'">查看采购计划单</a>　','body'=>$body));
		}
	}
	//
	function doupdate(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			$purchase=$this->kclass->DB->queryFirst("SELECT purchaseid,purchaseno FROM `purchase` WHERE purchaseid='".$this->kclass->input['purchaseid']."'");
			if(!$purchase){
				$e .= '<li>您要编辑的采购计划单并不存在，请返回列表刷新后再操作。</li>';
			}
			if($this->kclass->input['step']=='item'){
				
			}else{
				if(!$this->kclass->input['applicant']){
					$e .= '<li>请选择采购计划单中的 采购员。</li>';
				}
				if(!$this->kclass->input['purpose']){
					$e .= '<li>请选择采购计划单中的 目的。</li>';
				}
			}
		}
		if($e){
			$this->kclass->messager(array(
				'title' => '修改采购计划单',
				'text' => '您在修改采购计划单的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		if($this->kclass->input['step']=='item'){
			$count=count($this->kclass->input['purchaseItemId']);
			for($m=1;$m<$count+1;$m++){
				if($this->kclass->input['itemKill'][$m]!='' AND $this->kclass->input['itemKill'][$m]=$this->kclass->input['purchaseItemId'][$m]){
					$this->kclass->DB->query("UPDATE `purchaseitem` SET killed=".TIMENOW.",killer='".$this->kclass->user['userid']."'  WHERE itemid='".$this->kclass->input['purchaseItemId'][$m]."'");
					if($this->kclass->input['m'][$m]=='purchaseApply'){
						$this->kclass->DB->query("UPDATE `preinbounditem` SET ifChooser=0 WHERE module='purchaseApply' AND itemid='".$this->kclass->input['applyItemId'][$m]."'");
					}elseif($this->kclass->input['m'][$m]=='entrust'){
						$this->kclass->DB->query("UPDATE `entrustitem` SET ifChooser=0 WHERE itemid='".$this->kclass->input['applyItemId'][$m]."'");
					}
				}else{
					if($this->kclass->input['olArrivalTime'][$m]){
						$sd=explode('-',$this->kclass->input['olArrivalTime'][$m]);
						$arrivalTime=mktime(0,0,0,$sd[1],$sd[2],$sd[0]);
					}else{
						$arrivalTime=0;
					}
					$discount=intval($this->kclass->input['olDiscount'][$m])/100;
					$price=$this->kclass->iif(floatval($this->kclass->input['olPrice'][$m])=='',0,floatval($this->kclass->input['olPrice'][$m]));
					$total=$this->kclass->input['olQuantity'][$m]*$price*$discount;
					$total=number_format($total,2,'.','');
					$amount+=$total;
					$this->kclass->DB->query("
						UPDATE purchaseitem SET 
							supplierid='".$this->kclass->input['olSupplierid'][$m]."',
							quantity='".$this->kclass->input['olQuantity'][$m]."',
							currencyid='".$this->kclass->input['olCurrencyid'][$m]."',
							unitid='".$this->kclass->input['olUnitid'][$m]."',
							discount='".$this->kclass->input['olDiscount'][$m]."',
							arrivalTime='".$arrivalTime."',
							price='".$price."',
							total='".$total."',
							remark='".$this->kclass->input['olRemark'][$m]."'
						WHERE itemid='".$this->kclass->input['purchaseItemId'][$m]."'
					");
				}
			}
			$counter=count($this->kclass->input['itemId']);
			for($i=1;$i<$counter+1;$i++){
				if($this->kclass->input['itemSelect'][$i]!=''){
					if($this->kclass->input['itemArrivalTime'][$i]){
						$sd=explode('-',$this->kclass->input['itemArrivalTime'][$i]);
						$arrivalTime=mktime(0,0,0,$sd[1],$sd[2],$sd[0]);
					}else{
						$arrivalTime=0;
					}
					$discount=intval($this->kclass->input['itemDiscount'][$i])/100;
					$price=$this->kclass->iif(floatval($this->kclass->input['itemPrice'][$i])=='',0,floatval($this->kclass->input['itemPrice'][$i]));
					$total=$this->kclass->input['itemQuantity'][$i]*$price*$discount;
					$total=number_format($total,2,'.','');
					$amount+=$total;
					if($this->kclass->input['mo'][$i]=='purchaseApply'){
						$item=$this->kclass->DB->queryFirst("
							SELECT itemid,materialid,mid AS applyid,unitid,quantity,dateline 
							FROM `preinbounditem` 
							WHERE killed=0 AND module='purchaseApply' AND itemid='".$this->kclass->input['itemSelect'][$i]."'
							LIMIT 0,1
						");
						$mid=$item['applyid'];
					}elseif($this->kclass->input['mo'][$i]=='entrust'){
						$item=$this->kclass->DB->queryFirst("
							SELECT itemid,materialid,entrustid,unitid,quantity,dateline,requirement 
							FROM `entrustitem` 
							WHERE killed=0 AND itemid='".$this->kclass->input['itemSelect'][$i]."' 
							LIMIT 0,1
						");
						$mid=$item['entrustid'];
					}
					$this->kclass->DB->query("
						INSERT INTO purchaseitem
							(materialid,supplierid,purchaseid,module,mid,mitemid,quantity,currencyid,unitid,discount,price,total,arrivalTime,requirement,remark,created,creator)
						VALUES
							('".$item['materialid']."','".$this->kclass->input['itemSupplierid'][$i]."','".$this->kclass->input['purchaseid']."','".$this->kclass->input['mo'][$i]."','".$mid."','".$this->kclass->input['itemSelect'][$i]."','".$this->kclass->input['itemQuantity'][$i]."','".$this->kclass->input['itemCurrencyid'][$i]."','".$item['unitid']."','".$this->kclass->input['itemDiscount'][$i]."','".$price."','".$total."','".$arrivalTime."','".$item['requirement']."','".$this->kclass->input['itemRemark'][$i]."','".TIMENOW."','".$this->kclass->user['userid']."')
					");
					if($this->kclass->input['mo'][$i]=='purchaseApply'){
						$this->kclass->DB->query("UPDATE `preinbounditem` SET ifChooser=1 WHERE itemid='".$this->kclass->input['itemSelect'][$i]."'");
					}elseif($this->kclass->input['mo'][$i]=='entrust'){
						$this->kclass->DB->query("UPDATE `entrustitem` SET ifChooser=1 WHERE itemid='".$this->kclass->input['itemSelect'][$i]."'");
					}				
				}
			}
			$this->kclass->messager(array(
				'title' => '修改采购计划申请单明细',
				'text' => '采购计划单 【<b>'.$purchase['purchaseno'].'</b>】 的明细已修改成功!返回新建采购计划申请单',
				'url' => '/s.php?module=purchase&action=view&purchaseid='.$this->kclass->input['purchaseid'],
				'sec' => 2
			));
		}else{
			// update purchase
			$this->kclass->DB->query("
				UPDATE purchase SET
					applicant='".$this->kclass->input['applicant']."',
					purpose='".$this->kclass->input['purpose']."',
					remark='".$this->kclass->input['remark']."',
					modified='".TIMENOW."',
					modifier='".$this->kclass->user['userid']."'
				WHERE purchaseid='".$this->kclass->input['purchaseid']."'
			");
			$this->kclass->updateAttachs(array('module'=>'purchase', 'mid'=>$this->kclass->input['purchaseid']));
			$this->kclass->messager(array(
				'title' => '修改采购计划单',
				'text' => '采购计划单 【<b>'.$this->kclass->input['purchaseno'].'</b>】 已修改成功!返回修改采购计划单',
				'url' => '/s.php?module=purchase&action=view&purchaseid='.$this->kclass->input['purchaseid'],
				'sec' => 2
			));
		}
	}
	// 
	function insertPurchase(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			if($this->kclass->input['type']=='purchaseApply'){
				$j=count($this->kclass->input['applyId']);
				$hasAll=0;
				for($i=1;$i<$j+1;$i++){
					if($this->kclass->input['itemApplyid'][$i]>0){
						$hasAll=1;
					}
				}
				$n=count($this->kclass->input['itemId']);
				$hasItem=0;
				for($m=1;$m<$n+1;$m++){
					if($this->kclass->input['itemApplyItemid'][$m]>0){
						$hasItem=1;
					}
				}
				if($hasAll=0 OR $hasItem=0){
					$e='<li>请选择已通过审批的采购申请明细</li>';
				}
				if($e){
					$this->kclass->messager(array(
						'title' => '从已通过审批的采购申请 生成 采购计划',
						'text' => '您在生成采购计划的过程中有以下错误：<ul>'.$e.'</ul>',
						'url' => 'javascript:history.back()',
						'sec' => 3
					));
				}
			}
		}
		$start=strtotime(date('Y-m-d',TIMENOW));
		$end=strtotime(date('Y-m-d',$start))+86400;
		$counter=$this->kclass->DB->queryFirst("SELECT COUNT(*) AS count FROM purchase WHERE created>='".$start."' AND created<='".$end."'");
		$purchaseno=$this->kclass->id(array('purchase'=>TIMENOW,'number'=>$counter['count']));
		$this->kclass->DB->query("
			INSERT INTO purchase (purchaseno,applicant,created,creator)
			VALUES ('".$purchaseno."','".$this->kclass->user['realname']."','".TIMENOW."','".$this->kclass->user['userid']."')
		");
		$purchaseid=$this->kclass->DB->insertID();

		$itemId=array();
		for($i=1;$i<$j+1;$i++){
			if($this->kclass->input['itemApplyid'][$i]>0){
				$items=$this->kclass->DB->query("
					SELECT itemid,mid,materialid,quantity,dateline,unitid,mid 
					FROM preinbounditem 
					WHERE killed=0 AND module='purchaseApply' AND mid='".$this->kclass->input['itemApplyid'][$i]."'
				");
				if($this->kclass->DB->numRows()){
					while($item=$this->kclass->DB->fetchArray($items)){
						$itemId[]=$item['itemid'];
						$this->kclass->DB->query("
							INSERT INTO purchaseitem
								(materialid,purchaseid,module,mid,mitemid,quantity,unitid,arrivalTime,requirement,created,creator)
							VALUES
								('".$item['materialid']."','".$purchaseid."','purchaseApply','".$item['mid']."','".$item['itemid']."','".$item['quantity']."','".$item['unitid']."','".$item['dateline']."','".$item['requirement']."','".TIMENOW."','".$this->kclass->user['userid']."')
						");
						$this->kclass->DB->query("UPDATE `preinbounditem` SET ifChooser=1 WHERE itemid='".$item['itemid']."'");
					}
				}
			}elseif($this->kclass->input['itemEntrustid'][$i]>0){
				$items=$this->kclass->DB->query("
					SELECT *
					FROM entrustitem 
					WHERE killed=0 AND entrustid='".$this->kclass->input['itemEntrustid'][$i]."'
				");
				if($this->kclass->DB->numRows()){
					while($item=$this->kclass->DB->fetchArray($items)){
						$itemId[]=$item['itemid'];
						$this->kclass->DB->query("
							INSERT INTO purchaseitem
								(materialid,purchaseid,module,mid,mitemid,quantity,unitid,arrivalTime,requirement,created,creator)
							VALUES
								('".$item['materialid']."','".$purchaseid."','entrust','".$item['entrustid']."','".$item['itemid']."','".$item['quantity']."','".$item['unitid']."','".$item['dateline']."','".$item['requirement']."','".TIMENOW."','".$this->kclass->user['userid']."')
						");
						$this->kclass->DB->query("UPDATE `entrustitem` SET ifChooser=1 WHERE itemid='".$item['itemid']."'");
					}
				}
			}
		}
		for($m=1;$m<$n+1;$m++){
			if($this->kclass->input['itemApplyItemid'][$m]>0){
				if(in_array($this->kclass->input['itemApplyItemid'][$m],$itemId)){
					unset($this->kclass->input['itemApplyItemid'][$m]);
				}
				if($this->kclass->input['m'][$m]=='purchaseApply'){
					$item=$this->kclass->DB->queryFirst("
						SELECT itemid,materialid,mid,unitid,quantity,dateline 
						FROM `preinbounditem` 
						WHERE killed=0 AND module='purchaseApply' AND itemid='".$this->kclass->input['itemApplyItemid'][$m]."'
					");
				}elseif($this->kclass->input['m'][$m]=='entrust'){
					$item=$this->kclass->DB->queryFirst("
						SELECT itemid,entrustid AS mid,materialid,unitid,quantity,dateline,requirement  
						FROM `entrustitem` 
						WHERE killed=0 AND itemid='".$this->kclass->input['itemApplyItemid'][$m]."'
					");
				}

				if($item){
					$this->kclass->DB->query("
						INSERT INTO `purchaseitem`
							(materialid,purchaseid,module,mid,mitemid,quantity,unitid,arrivalTime,requirement,created,creator)
						VALUES
							('".$item['materialid']."','".$purchaseid."','".$this->kclass->input['m'][$m]."','".$item['mid']."','".$this->kclass->input['itemApplyItemid'][$m]."','".$item['quantity']."','".$item['unitid']."','".$item['dateline']."','".$item['requirement']."','".TIMENOW."','".$this->kclass->user['userid']."')
					");
					if($this->kclass->input['m'][$m]=='purchaseApply'){
						$this->kclass->DB->query("UPDATE `preinbounditem` SET ifChooser=1 WHERE itemid='".$this->kclass->input['itemApplyItemid'][$m]."'");
					}elseif($this->kclass->input['m'][$m]=='entrust'){
						$this->kclass->DB->query("UPDATE `entrustitem` SET ifChooser=1 WHERE itemid='".$this->kclass->input['itemApplyItemid'][$m]."'");
					}
				}
			}
		}

		$this->kclass->messager(array(
			'title' => '新增采购计划单',
			'text' => '采购计划单 【<b>'.$purchaseno.'</b>】 已修改成功!返回修改采购计划单',
			'url' => '/s.php?module=purchase&action=view&purchaseid='.$purchaseid,
			'sec' => 2
		));
	}
	//
	function verify(){
		$purchase=$this->kclass->DB->queryFirst("
			SELECT p.purchaseid,p.purchaseno,p.applicant,p.ifVerify,p.verifyRemark,p.purpose,p.remark,p.created,p.verified,p.modified,p.amount,
				c.title AS currency,
				u.username AS creator,
				us.username AS modifier
			FROM purchase AS p
			LEFT JOIN currency AS c ON (c.currencyid=p.currencyid)
			LEFT JOIN user AS u ON (u.userid=p.creator)
			LEFT JOIN user AS us ON (u.userid=p.modifier)
			WHERE p.killed=0 AND p.purchaseid='".$this->kclass->input['purchaseid']."'
			LIMIT 0,1
		");
		if(!$purchase AND $purchase['ifVerify']!=0){
			$e='<li>数据错误，很抱歉~</li>';
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '审核采购计划单',
				'text' => '审核采购计划单<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$purchase['created']=date('Y-n-d',$purchase['created']);
		$verify=$this->kclass->verify($purchase['ifVerify']);
		if($purchase['modified']!=0)$modify='，由'.$purchase['modifier'].'于'.date('Y-n-d',$purchase['modified']).'修改';
		$verifyRemark='<td></td><td></td>';
		if($purchase['verified']!=0){
			$verifyRemark='<td>审核备注：</td><td>'.$purchase['verifyRemark'].'</td>';
		}
		//申请的物资
		$items=$this->kclass->DB->query("
			SELECT p.*,
				m.materialno,m.title,m.standard,m.entrustMaterialid,
				s.supplierid,s.title AS supplier,
				u.title AS unit,
				c.title AS currency,c.symbol
			FROM purchaseitem AS p
			LEFT JOIN material AS m ON (m.materialid=p.materialid)
			LEFT JOIN supplier AS s ON (s.supplierid=p.supplierid)
			LEFT JOIN unit AS u ON (u.unitid=p.unitid)
			LEFT JOIN currency AS c ON (c.currencyid=p.currencyid)
			WHERE p.killed=0 AND p.purchaseid='".$purchase['purchaseid']."'
			ORDER BY p.ordering ASC
		");
		if($this->kclass->DB->numRows()){
			$i=1;
			while($item=$this->kclass->DB->fetchArray($items)){
				$item['arrivalTime']=date('Y-m-d',$item['arrivalTime']);
				$class=$this->kclass->iif($i%2==0,'class="even"','class="odd"');
				$materialno=$item['materialno'];
				$materialid = $item['materialid'];
				$title=$item['title'];
				$standard=$item['standard'];
				if($item['module']=='purchaseApply'){
					$mitem=$this->kclass->DB->queryFirst("SELECT pi.*,pa.applyno,pa.purchaseApplyid FROM preinbounditem AS pi LEFT JOIN purchaseapply AS pa ON (pi.mid=pa.purchaseApplyid) WHERE pi.killed=0 AND pi.module='purchaseApply' AND pi.itemid='".$item['mitemid']."'");
					$type="采购申请";
					$typeno='<a href="/s.php?module=purchaseapply&action=view&purchaseApplyid='.$mitem['purchaseApplyid'].'">'.$mitem['applyno'].'</a>';
				}elseif($item['module']=='entrust'){
					$mitem=$this->kclass->DB->queryFirst("SELECT ei.*,e.entrustid,e.entrustno
					FROM entrustitem AS ei
					LEFT JOIN entrust AS e ON (e.entrustid=ei.entrustid)
					WHERE ei.killed=0 AND ei.itemid='".$item['mitemid']."'");
					$type="外发加工";
					$typeno='<a href="/s.php?module=entrust&action=view&entrustid='.$mitem['entrustid'].'">'.$mitem['entrustno'].'</a>';
					$materialid = $item['entrustMaterialid'];
					if($materialid!=""){
							$query = $this->kclass->DB->queryFirst("SELECT * FROM `material` WHERE materialid='{$materialid}'");
							$materialno = $query['materialno'];
							$standard = $query['standard'];	
							$title = $query['title'];
					}				
				}

				$itemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').' small"><td>'.$i.'</td>
					<td>'.$type.'</td>
					<td>'.$typeno.'</td>
					<td><a href="/s.php?module=material&action=view&materialid='.$materialid.'">'.$materialno.'</a></td>
					<td>'.$title.'</td>
					<td>'.$standard.'</td>
					<td><a href="/s.php?module=psupplier&action=view&supplierid='.$item['supplierid'].'">'.$item['supplier'].'</a></td>
					<td>'.$item['quantity'].' '.$item['unit'].'</td>
					<td>'.$item['currency'].'</td>
					<td>'.$item['price'].'</td>
					<td>'.$item['discount'].'%</td>
					<td>'.$item['total'].'</td>
					<td>'.$item['arrivalTime'].'</td>
					<td>'.$item['remark'].'</td></tr>';
				$i++;
			}
		}

$body=<<<EOF
<div class="title"><span class="right small gray">由{$purchase['creator']}于{$purchase['created']}建立{$modify}。</span>{$purchase['purchaseno']}</div>
<table class="hundred">
<thead><tr><th colspan="4">采购计划单</th></tr></thead>
<tbody>
<tr class="odd">
<td width="100">编　　号：</td><td width="475">{$purchase['purchaseno']}　（{$verify}）</td>
<td width="100">采 购 员：</td><td>{$purchase['applicant']}</td>
</tr>
<tr class="even">
<td>申请时间：</td><td>{$purchase['created']}</td>
<td>用　　途：</td><td>{$purchase['purpose']}</td>
</tr>
<tr class="odd">
<td>备　　注：</td><td>{$purchase['remark']}</td>
{$verifyRemark}
</tbody>
</table>
<table class="hundred">
<thead><tr><th colspan="14">采购物资</th></tr></thead>
<tbody>
<tr class="center even">
<td width="15">ID</td><td width="60">申请类型</td><td width="50">申请单编号</td><td>物资编号</td><td>物资名称</td><td>物资规格</td><td>供应商</td><td>数量/单位</td><td>币种</td><td>单价</td><td>折扣</td><td>总价</td><td>到货时间</td><td>备注</td>
</tr>
{$itemtr}
</tbody>
</table>
<form method="post" action="/s.php?module=purchase&action=doverify">
<input type="hidden" name="module" value="purchase">
<input type="hidden" name="action" value="doverify">
<input type="hidden" name="purchaseid" value="{$this->kclass->input['purchaseid']}">
<table>
<thead><tr><th colspan="4">审核 <span class="small red">(由采购部主管审核)</span></th></tr></thead>
<tbody>
<tr class="even">
<td>审　　核：<span class="bold red">*</span></td><td class="center"><input type="radio" name="ifVerify" value="1" checked />通过　　　<input type="radio" name="ifVerify" value="-1"  />不通过</td>
</tr>
<tr class="odd">
<td>审核备注：</td><td><textarea name="verifyRemark" style="width:440px;height:110px;"></textarea></td>
</tr>
<tr class="odd">
<td colspan=2 class="center"><input type="submit" value=" 提交 " /><input type="reset" value="重置" /></td>
</tr>
</tbody>
</table>
</form>
EOF;
		$this->kclass->tbline+=4;
		$this->kclass->page['title'].='审核采购计划单';
		$this->kclass->page['onload'].='dc.tabhover();';
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - <a href="/s.php?module=purchase&action=view&purchaseid='.$this->kclass->input['purchaseid'].'">查看采购计划单</a> - 审核采购计划单', 'right'=>'<a href="/s.php?module=purchase&action=list">返回列表</a>　|　<a href="/s.php?module=purchase&action=view&purchaseid='.$this->kclass->input['purchaseid'].'">查看采购计划单</a>','body'=>$body));
	}
	//
	function doverify(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			$purchase=$this->kclass->DB->queryFirst("SELECT purchaseno FROM purchase WHERE purchaseid='".$this->kclass->input['purchaseid']."'");
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
			UPDATE purchase SET
				ifVerify='".$this->kclass->input['ifVerify']."',
				verifier='".$this->kclass->user['userid']."',
				verified='".TIMENOW."',
				verifyRemark='".$this->kclass->input['verifyRemark']."'
			WHERE purchaseid='".$this->kclass->input['purchaseid']."'
		");
		if($this->kclass->input['ifVerify']==1){
			$verify='已通过';
		}elseif($this->kclass->input['ifVerify']==-1){
			$verify='未通过';
		}
		$this->kclass->messager(array(
			'title' => '审核采购计划单',
			'text' => '采购计划单 【<b>'.$purchase['purchaseno'].'</b>】 '.$verify.'审核!返回查看采购计划单',
			'url' => '/s.php?module=purchase&action=view&purchaseid='.$this->kclass->input['purchaseid'],
			'sec' => 2
		));
	}
	//
	function approve(){
		$purchase=$this->kclass->DB->queryFirst("
			SELECT p.purchaseid,p.purchaseno,p.applicant,p.ifVerify,p.verified,p.verifyRemark,p.approved,
					p.purpose,p.remark,p.created,p.modified,p.amount,
				c.title AS currency,
				u.username AS creator,
				us.username AS modifier
			FROM purchase AS p
			LEFT JOIN currency AS c ON (c.currencyid=p.currencyid)
			LEFT JOIN user AS u ON (u.userid=p.creator)
			LEFT JOIN user AS us ON (u.userid=p.modifier)
			WHERE p.killed=0 AND p.purchaseid='".$this->kclass->input['purchaseid']."'
			LIMIT 0,1
		");
		if(!$apply AND $apply['ifVerify']!=0){
			$e='<li>数据错误，很抱歉~</li>';
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '审批采购申请单',
				'text' => '审批采购申请单<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$purchase['created']=date('Y-n-d',$purchase['created']);
		$verify=$this->kclass->verify($purchase['ifVerify']);
		if($purchase['modified']!=0)$modify='，由'.$purchase['modifier'].'于'.date('Y-n-d',$purchase['modified']).'修改';
		if($purchase['verified']!=0){
			$pverify='，由'.$purchase['verifier'].'于'.date('Y-m-d',$purchase['verified']).'审核';
			$remark='<tr class="even"><td>审核备注：</td><td>'.$purchase['verifyRemark'].'</td><td></td><td></td></tr';
		}
		if($purchase['approved']!=0){
			$averify.='，由'.$purchase['approver'].'于'.date('Y-m-d H:i:s',$purchase['approved']).'审批';
			$remark.='<tr class="odd"><td>审批备注：</td><td>'.$purchase['approvalRemark'].'</td><td></td><td></td></tr>';
		}
		//申请的物资
		$items=$this->kclass->DB->query("
			SELECT p.*,
				m.materialno,m.title,m.standard,m.entrustMaterialid,
				s.supplierid,s.title AS supplier,
				u.title AS unit,
				c.title AS currency,c.symbol
			FROM purchaseitem AS p
			LEFT JOIN material AS m ON (m.materialid=p.materialid)
			LEFT JOIN supplier AS s ON (s.supplierid=p.supplierid)
			LEFT JOIN unit AS u ON (u.unitid=p.unitid)
			LEFT JOIN currency AS c ON (c.currencyid=p.currencyid)
			WHERE p.killed=0 AND p.purchaseid='".$purchase['purchaseid']."'
			ORDER BY p.ordering ASC
		");
		if($this->kclass->DB->numRows()){
			$i=1;
			while($item=$this->kclass->DB->fetchArray($items)){
				$item['arrivalTime']=date('Y-m-d',$item['arrivalTime']);
				$class=$this->kclass->iif($i%2==0,'class="even"','class="odd"');
				$materialno=$item['materialno'];
				$title=$item['title'];
				$standard=$item['standard'];
				$materialid=$item['materialid'];
				//判断是采购申请还是外发加工
				if($item['module']=='purchaseApply'){
					$mitem=$this->kclass->DB->queryFirst("SELECT pi.*,pa.applyno,pa.purchaseApplyid FROM preinbounditem AS pi LEFT JOIN purchaseapply AS pa ON (pi.mid=pa.purchaseApplyid) WHERE pi.killed=0 AND pi.module='purchaseApply' AND pi.itemid='".$item['mitemid']."'");
					$type="采购申请";
					$typeno='<a href="/s.php?module=purchaseapply&action=view&purchaseApplyid='.$mitem['purchaseApplyid'].'">'.$mitem['applyno'].'</a>';
				}elseif($item['module']=='entrust'){
					$mitem=$this->kclass->DB->queryFirst("SELECT ei.*,e.entrustid,e.entrustno
					FROM entrustitem AS ei
					LEFT JOIN entrust AS e ON (e.entrustid=ei.entrustid)
					WHERE ei.killed=0 AND ei.itemid='".$item['mitemid']."'");
					$type="外发加工";
					$typeno='<a href="/s.php?module=entrust&action=view&entrustid='.$mitem['entrustid'].'">'.$mitem['entrustno'].'</a>';					
					$materialid = $item['entrustMaterialid'];
					if($materialid!=""){
							$query = $this->kclass->DB->queryFirst("SELECT * FROM `material` WHERE materialid='{$materialid}'");
							$materialno = $query['materialno'];
							$standard = $query['standard'];	
							$title = $query['title'];
					}						
				}


				$itemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').' small"><td>'.$i.'</td>
					<td>'.$type.'</td>
					<td>'.$typeno.'</td>
					<td><a href="/s.php?module=material&action=view&materialid='.$materialid.'">'.$materialno.'</a></td>
					<td>'.$title.'</td>
					<td>'.$standard.'</td>
					<td><a href="/s.php?module=psupplier&action=view&supplierid='.$item['supplierid'].'">'.$item['supplier'].'</a></td>
					<td>'.$item['quantity'].' '.$item['unit'].'</td>
					<td>'.$item['currency'].'</td>
					<td>'.$item['price'].'</td>
					<td>'.$item['discount'].'%</td>
					<td>'.$item['total'].'</td>
					<td>'.$item['arrivalTime'].'</td>
					<td>'.$item['remark'].'</td></tr>';
				$i++;
			}
		}

$body=<<<EOF
<div class="title"><span class="right small gray">由{$purchase['creator']}于{$purchase['created']}建立{$modify}。</span>{$purchase['purchaseno']}</div>
<table class="hundred">
<thead><tr><th colspan="4">采购计划单</th></tr></thead>
<tbody>
<tr class="odd">
<td width="100">编　　号：</td><td width="475">{$purchase['purchaseno']}　（{$verify}）</td>
<td width="100">采 购 员：</td><td>{$purchase['applicant']}</td>
</tr>
<tr class="even">
<td>总　　价：</td><td>{$purchase['amount']}</td>
<td>申请时间：</td><td>{$purchase['created']}</td>
</tr>
<tr class="odd">
<td>用　　途：</td><td>{$purchase['purpose']}</td>
<td>备　　注：</td><td>{$purchase['remark']}</td>
</tr>
{$remark}
</tbody>
</table>
<table class="hundred">
<thead><tr><th colspan="14">采购物资</th></tr></thead>
<tbody>
<tr class="center even">
<td width="15">ID</td><td width="60">申请类型</td><td width="50">申请单编号</td><td>物资编号</td><td>物资名称</td><td>物资规格</td><td>供应商</td><td>数量/单位</td><td>币种</td><td>单价</td><td>折扣</td><td>总价</td><td>到货时间</td><td>备注</td>
</tr>
{$itemtr}
</tbody>
</table>
<form method="post" action="/s.php?module=purchase&action=doapprove">
<input type="hidden" name="module" value="purchase">
<input type="hidden" name="action" value="doapprove">
<input type="hidden" name="purchaseid" value="{$this->kclass->input['purchaseid']}">
<table>
<thead><tr><th colspan="4">审批 <span class="small red">(由采购部主管审核)</span></th></tr></thead>
<tbody>
<tr class="even">
<td>审　　批：<span class="bold red">*</span></td><td class="center"><input type="radio" name="ifApprove" value="1" checked />通过　　　<input type="radio" name="ifApprove" value="-1"  />不通过</td>
</tr>
<tr class="odd">
<td>审批备注：</td><td><textarea name="approvalRemark" style="width:440px;height:110px;"></textarea></td>
</tr>
<tr class="odd">
<td colspan=2 class="center"><input type="submit" value=" 提交 " /><input type="reset" value="重置" /></td>
</tr>
</tbody>
</table>
</form>
EOF;
		$this->kclass->tbline+=4;
		$this->kclass->page['title'].='审核采购计划单';
		$this->kclass->page['onload'].='dc.tabhover();';
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - <a href="/s.php?module=purchase&action=view&purchaseid='.$this->kclass->input['purchaseid'].'">查看采购计划单</a> - 审核采购计划单', 'right'=>'<a href="/s.php?module=purchase&action=list">返回列表</a>　|　<a href="/s.php?module=purchase&action=view&purchaseid='.$this->kclass->input['purchaseid'].'">查看采购计划单</a>','body'=>$body));
	}
	//
	function doapprove(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			$purchase=$this->kclass->DB->queryFirst("SELECT * FROM purchase WHERE killed=0 AND purchaseid='".$this->kclass->input['purchaseid']."' LIMIT 0,1 ");
			if($purchase['ifApprove']!=0){
				$e.='<li>采购单 <b>'.$purchase['purchaseno'].'</b> 已经被审批过。</li>';
			}
			if($this->kclass->input['ifApprove']!=1 AND $this->kclass->input['ifApprove']!=-1){
				$e.='<li>请选择采购计划中的 是否通过审批 按钮。</li>';
			}
		}
		if($e){
			$this->kclass->messager(array(
				'title' => '审批采购计划',
				'text' => '您在审批采购计划的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}

		$this->kclass->DB->query("
			UPDATE purchase SET
				ifApprove='".$this->kclass->input['ifApprove']."',
				approver='".$this->kclass->user['userid']."',
				approved='".TIMENOW."',
				approvalRemark='".$this->kclass->input['approvalRemark']."'
			WHERE purchaseid='".$this->kclass->input['purchaseid']."'
		");

		if($this->kclass->input['ifApprove']==1){
			$approve='已通过';
		}elseif($this->kclass->input['ifApprove']==-1){
			$approve='未通过';
		}
		$this->kclass->messager(array(
			'title' => '审批采购计划',
			'text' => '采购计划 【<b>'.$purchase['purchaseno'].'</b>】 '.$approve.'审批!返回查看采购申请单',
			'url' => '/s.php?module=purchase&action=view&purchaseid='.$this->kclass->input['purchaseid'],
			'sec' => 2
		));
	}
	//
	function kill(){
		if($this->kclass->input['purchaseid']<=0){
			$this->kclass->boinkIt('/s.php?module=purchase');
		}
		if($this->kclass->input['rt']!='list' AND $this->kclass->input['confirm'] == 0){
			$this->kclass->boinkIt('/s.php?module=purchase&action=view&purchaseid='.$this->kclass->input['purchaseid']);
		}
		if($this->kclass->input['purchaseid'] < 0){
			$e = '<li>请从正确的页面进行操作，如果此问题持续存在，请联系网站管理员。</li>';
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '删除采购计划单',
				'text' => '您在删除采购计划单的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		if($this->kclass->input['rt']=='view'){
			$rt = '&action=view&purchaseid='.$this->kclass->input['purchaseid'];
		}elseif($this->kclass->input['rt']=='list'){
			$rt = '#'.$this->kclass->input['purchaseid'];
		}
		$purchase=$this->kclass->DB->queryFirst("
			SELECT purchaseno
			FROM purchase
			WHERE purchaseid='".$this->kclass->input['purchaseid']."'
		");
		if($purchase){
			$this->kclass->DB->query("
				UPDATE `purchase`
				SET killed=".TIMENOW.",killer='".$this->kclass->user['userid']."'
				WHERE purchaseid='".$this->kclass->input['purchaseid']."'
			");
			$items=$this->kclass->DB->query("
				SELECT module,mitemid
				FROM purchaseitem 
				WHERE purchaseid='".$this->kclass->input['purchaseid']."'
			");
			if($this->kclass->DB->numRows()){
				while($item=$this->kclass->DB->fetchArray($items)){
					if($item['module']=='purchaseApply'){
						$this->kclass->DB->query("UPDATE preinbounditem SET ifChooser=0 WHERE module='purchaseApply' AND itemid='".$item['mitemid']."'");
					}elseif($item['module']=='entrust'){
						$this->kclass->DB->query("UPDATE entrustitem SET ifChooser=0 WHERE itemid='".$item['mitemid']."'");
					}
				}
			}
			$this->kclass->messager(array(
				'title' => '删除采购计划单成功',
				'text' => '采购计划单 <b>'.$purchase['purchaseno'].'</b> 已成功被标记为删除!',
				'url' => '/s.php?module=purchase'.$rt,
				'sec' => 2
			));
		}else{
			$this->kclass->messager(array(
				'title' => '删除采购计划单失败',
				'text' => '您要删除的采购计划单，不存在！',
				'url' => '/s.php?module=purchase'.$rt,
				'sec' => 3
			));
		}
	}

	//
	function remove(){
		if($this->kclass->input['purchaseid']<=0){
			$this->kclass->boinkIt('/s.php?module=purchase');
		}
		$purchase = $this->kclass->DB->queryFirst("
			SELECT purchaseno
			FROM `purchase`
			WHERE purchaseid='".$this->kclass->input['purchaseid']."'
		");
$body = <<<EOF
<form action="/s.php?module=purchase&action=kill" name="purchase" method="post">
<input type="hidden" name="module" value="purchase">
<input type="hidden" name="action" value="kill">
<input type="hidden" name="purchaseid" value="{$this->kclass->input['purchaseid']}">
<input type="hidden" name="rt" value="{$this->kclass->input['rt']}">
<table><thead>
<thead>
<tr>
<th>删除采购计划单：{$purchase['purchaseno']}</th>
</tr>
</thead>
<tbody>
<tr class="even">
<td class="middle">你确定要删除采购计划单: <a href="/s.php?module=purchase&action=view&purchaseid={$this->kclass->input['purchaseid']}" class="big bold" target="_blank">{$purchase['purchaseno']}</a> 吗?</td>
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
		$this->kclass->page['title'] .= ' - 删除 - '.$purchase['purchaseno'];
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 删除采购计划单 - '.$purchase['purchaseno'], 'right' => '<a href="/s.php?module=purchase">返回列表</a>', 'body'=>$body));
	}

	//
	function revival(){
		if($this->kclass->input['purchaseid']<=0){
			$this->kclass->boinkIt('/s.php?module=purchase');
		}
		if($this->kclass->input['rt']!='list' AND $this->kclass->input['confirm']==0){
			$this->kclass->boinkIt('/s.php?module=purchase&action=view&purchaseid='.$this->kclass->input['purchaseid']);
		}
		if($this->kclass->input['purchaseid']<0){
			$e = '<li>请从正确的页面进行操作，如果此问题持续存在，请联系网站管理员。</li>';
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '恢复采购计划单',
				'text' => '您在恢复采购计划单的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$purchase = $this->kclass->DB->queryFirst("
			SELECT purchaseno
			FROM `purchase`
			WHERE purchaseid='".$this->kclass->input['purchaseid']."'
		");
		if($this->kclass->input['rt']=='view'){
			$rt = '&action=view&purchaseid='.$this->kclass->input['purchaseid'];
		}elseif($this->kclass->input['rt']=='list'){
			$rt = '#'.$this->kclass->input['purchaseid'];
		}
		if($purchase){
			$this->kclass->DB->query("
				UPDATE `purchase`
				SET killed=0,killer=0
				WHERE purchaseid='".$this->kclass->input['purchaseid']."'
			");
			$this->kclass->messager(array(
				'title' => '恢复采购计划单成功',
				'text' => '采购计划单 <b>'.$purchase['purchaseno'].'</b> 已成功被恢复！',
				'url' => '/s.php?module=purchase'.$rt,
				'sec' => 2
			));
		}else{
			$this->kclass->messager(array(
				'title' => '恢复采购计划单',
				'text' => '您要恢复的采购计划单不存在！',
				'url' => '/s.php?module=purchase'.$rt,
				'sec' => 3
			));
		}
	}

	//
	function restore(){
		if($this->kclass->input['purchaseid']<=0){
			$this->kclass->boinkIt('/s.php?module=purchase');
		}
		$purchase = $this->kclass->DB->queryFirst("
			SELECT purchaseno
			FROM `purchase`
			WHERE purchaseid='".$this->kclass->input['purchaseid']."'
		");
$body = <<<EOF
<form action="/s.php?module=purchase&action=revival" name="purchase" method="post">
<input type="hidden" name="module" value="purchase">
<input type="hidden" name="action" value="revival">
<input type="hidden" name="purchaseid" value="{$this->kclass->input['purchaseid']}">
<input type="hidden" name="rt" value="{$this->kclass->input['rt']}">
<table><thead>
<thead>
<tr>
<th>恢复采购计划单：{$purchase['purchaseno']}</th>
</tr>
</thead>
<tbody>
<tr class="even">
<td class="middle">你确定要恢复采购计划单: <a href="/s.php?module=purchase&action=view&purchaseid={$this->kclass->input['purchaseid']}" class="big bold" target="_blank">{$purchase['purchaseno']}</a> 吗?</td>
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
		$this->kclass->page['title'] .= ' - 恢复 - '.$purchase['purchaseno'];
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 恢复采购计划单 - '.$purchase['purchaseno'], 'right' => '<a href="/s.php?module=purchase">返回列表</a>', 'body'=>$body));
	}
}
?>