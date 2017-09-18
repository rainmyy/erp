<?php
//
class purchaseApply{
	var $kclass;
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
	// 采购申请列表
	function mmlist(){
		if($this->kclass->input['applyno']!=''){
			$applyno=trim($this->kclass->input['applyno']);
			$apply=$this->kclass->DB->queryFirst("SELECT purchaseApplyid FROM purchaseapply WHERE applyno='".$applyno."' LIMIT 0,1");
		}else{
			if($this->kclass->input['dateline']!=''){
				$sd=explode('-',$this->kclass->input['dateline']);
				$start=mktime(0,0,0,$sd[1],$sd[2],$sd[0]);
				$end=$start+86400;
				$find.=' AND (p.created>'.$start.' AND p.created<'.$end.')';
			}
			if($this->kclass->input['materialno']!=''){
				$materialno=trim($this->kclass->input['materialno']);
				$find.=' AND m.materialno="'.$materialno.'"';
			}
			if($this->kclass->input['title']!=''){
				$title=trim($this->kclass->input['title']);
				$find.=' AND (m.title="'.$this->kclass->input['title'].'" OR p.title="'.$title.'")';
			}
			if($find){
				$applys=$this->kclass->DB->query("
					SELECT DISTINCT p.mid AS purchaseApplyid
					FROM preinbounditem AS p
					LEFT JOIN material AS m ON (m.materialid=p.materialid)
					WHERE p.killed=0 ".$find."
					ORDER BY p.created DESC
				");
				if($this->kclass->DB->numRows()==1){
					while($apply=$this->kclass->DB->fetchArray($applys)){
						$applyid=$apply['purchaseApplyid'];
					}
				}
			}
		}
		if($applyid){
			$this->kclass->boinkIt('/s.php?module=purchaseApply&action=view&purchaseApplyid='.$applyid);
		}

		if($this->kclass->input['show']=='all'){
			$condition='1=1';
		}elseif($this->kclass->input['show']=='verify'){
			$condition='`purchaseapply`.ifVerify=1';
		}elseif($this->kclass->input['show']=='unverify'){
			$condition='`purchaseapply`.ifVerify=-1';
		}elseif($this->kclass->input['show']=='inverify'){
			$condition='`purchaseapply`.ifVerify=0';
		}else{
			$condition='`purchaseapply`.killed=0 AND `purchaseapply`.ifVerify=0 AND `purchaseapply`.ifApprove=0';
		}
		if($this->kclass->input['orderby']!='')$query['orderby']=$this->kclass->input['orderby'];
		if($this->kclass->input['direction']!='')$query['direction']=$this->kclass->input['direction'];
		if($this->kclass->input['show']!='')$query['show']=$this->kclass->input['show'];
		if($this->kclass->input['layout']!='')$query['layout']=$this->kclass->input['layout'];
		$orderby=$this->kclass->orderby(array('module'=>'purchaseapply','direction'=>'asc','orderby'=>'modified', 'default'=>'applyno', 'serial'=>array( array('title'=>'编号', 'field'=>'applyno','word'=>'no'), array('title'=>'修改时间', 'field'=>'modified'), array('title'=>'建立时间', 'field'=>'created')),'appendUrl'=>$query));
		if(is_array($query) AND count($query)>0)$queryPart='&'.http_build_query($query);
		$applys=$this->kclass->DB->query("
			SELECT `purchaseapply`.*,
				d.title AS department,
				u.username AS creator,
				us.username AS modifier
			FROM `purchaseapply`
			LEFT JOIN department AS d ON (d.departmentid=`purchaseapply`.departmentid)
			LEFT JOIN user AS u ON (u.userid=`purchaseapply`.creator)
			LEFT JOIN user AS us ON (us.userid=`purchaseapply`.modifier)
			WHERE $condition 
			ORDER BY ".$orderby['sql']."
		");
		if($this->kclass->DB->numRows()){
			if($this->kclass->input['layout']=='grid'){
				$body='<ul id="mmlist" class="mmlist clear">';
			}else{
				$body='<table class="hundred"><thead><th width="30">ID</th><th width="120">编号</th><th width="150">申请人</th><th>申请部门</th><th>物资类型</th><th width="200">审核状态</th><th width="120"></th></thead><tbody>';
			}
			$j=1;
			while($apply=$this->kclass->DB->fetchArray($applys)){
				$alarm='';
				$apply['created']=date('Y-m-d',$apply['created']);
				$verify=$this->kclass->verify($apply['ifVerify']);
				$approve=$this->kclass->approve($apply['ifApprove']);
				if($apply['ifVerify']==0){
					$alarm='bgYellow';
				}elseif($apply['ifVerify']==-1){
					$alarm='bgRed';
				}
				$typeValue='';
				$types=$this->kclass->DB->query("SELECT title FROM purchasetype WHERE killed=0 AND typeid IN (".$apply['typeid'].")");
				if($counter=$this->kclass->DB->numRows()){
					$i=1;
					while($type=$this->kclass->DB->fetchArray($types)){
						if($i<$counter){
							$typeValue.=$type['title'].'、';
						}else{
							$typeValue.=$type['title'];
						}
						$i++;
					}
				}
				if($apply['killed']>0){
					$link='<a href="/s.php?module=purchaseApply&action=restore&purchaseApplyid='.$apply['purchaseApplyid'].'&rt=list">恢复</a>';
				}else{
					// 审核审批状态转换的功能操作
					$link='';
					if($apply['ifApprove']==0){
						if($apply['ifVerify']==0){
							if($this->kclass->purviews(array('module'=>'purchaseApply','action'=>'verify','final'=>1))){
								$link.=' <a href="/s.php?module=purchaseApply&action=verify&purchaseApplyid='.$apply['purchaseApplyid'].'" >核</a> <a href="/s.php?module=purchaseApply&action=remove&purchaseApplyid='.$this->kclass->input['purchaseApplyid'].'&rt=listApply">删</a> ';
							}
						}elseif($apply['ifVerify']==1){
							$link.='<a href="/s.php?module=purchaseApply&action=approve&purchaseApplyid='.$apply['purchaseApplyid'].'" >批</a> ';
						}elseif($apply['ifVerify']==0){
							$link.='<a href="/s.php?module=purchaseApply&action=remove&purchaseApplyid='.$this->kclass->input['purchaseApplyid'].'&rt=listApply">删</a> ';
						}
						$link.='<a href="/s.php?module=purchaseApply&action=update&purchaseApplyid='.$apply['purchaseApplyid'].'">改</a>';
					}elseif($apply['ifApprove']==-1){
						$link.='<a href="/s.php?module=purchaseApply&action=remove&purchaseApplyid='.$this->kclass->input['purchaseApplyid'].'&rt=listApply">删</a>';
					}elseif($apply['ifApprove']==1){
						 $link.=' <a href="/p.php?action=purchaseApply&purchaseApplyid='.$apply['purchaseApplyid'].'" target="_blank">印</a>';
					}
				}
				if($this->kclass->input['layout']=='grid'){//表格
					$body.='<li title="由 '.$apply['creator'].' 建于 '.$apply['created'].$this->kclass->iif($apply['modifier']!='', '，'.$apply['modifier'].' 改于 '.date('Y-m-d H:i',$apply['modified']), '').'"'.$this->kclass->iif($i%4==0, ' class="end"', '').'>
					<div class="mmlistt">
						<span class="right normal">';
					if($apply['killed']>0){
						$body.='<a href="/s.php?module=purchaseApply&action=revival&purchaseApplyid='.$apply['purchaseApplyid'].'&rt=list" onclick="return confirm(\'你确定要恢复这个采购申请单 '.$apply['applyno'].' 吗？\');">恢复</a>';
					}else{
						$body.='<a href="/s.php?module=purchaseApply&action=kill&purchaseApplyid='.$apply['purchaseApplyid'].'&rt=list" onclick="return confirm(\'你确定要删除这个采购申请单 '.$apply['applyno'].' 吗？\');">删</a> '.$this->kclass->iif($apply['ifVerify']==0,'<a href="/s.php?module=purchaseApply&action=update&purchaseApplyid='.$apply['purchaseApplyid'].'&rt=listApply">改</a>','');
					}
					$body.='</span>
						<span class="small">'.$apply['attr'].'</span> <a href="/s.php?module=purchaseApply&action=view&purchaseApplyid='.$apply['purchaseApplyid'].'">'.$apply['applyno'].'</a><br><span class="small gray right">'.$apply['created'].'</span>'.$this->kclass->iif($apply['attachs']>0, ' <span class="attachFile" title="有'.$apply['attachs'].'个附件。"></span> ', '').$this->kclass->iif($apply['images']>0, ' <span class="attachImage" title="有'.$apply['images'].'个图片。"></span> ', '').$verify.'</div>
					<div class="mmlistb"><div><span class="small gray">申请人：</span>'.$apply['applicant'].'</div>
						<div><span class="small gray">申请部门：</span>'.$apply['department'].'</div>
						<div><span class="small gray">采购类型：</span>'.$typeValue.'</div>
						<div class="small clear"><span class=right title="由 '.$apply['creator'].' 建于 '.$apply['created'].'">由 '.$apply['creator'].' 建于 '.$apply['created'].'</span></div></div></li>';
				}else{
					$body .='<tr class="'.$this->kclass->rotateLine().' '.$alarm.'">
						<td>'.$j.'</td>
						<td><a href="/s.php?module=purchaseApply&action=view&purchaseApplyid='.$apply['purchaseApplyid'].'">'.$apply['applyno'].$this->kclass->iif($apply['attachs']>0, ' <span class="attachFile" title="有'.$apply['attachs'].'个附件。"></span> ', '').$this->kclass->iif($apply['images']>0, ' <span class="attachImage" title="有'.$apply['images'].'个图片。"></span> ', '').'</a></td>
						<td title="'.$apply['applicant'].'">'. $apply['applicant'].'</td>
						<td>'.$apply['department'].'</td>
						<td>'.$typeValue.'</td>
						<td>'.$verify.' , '.$approve.'</td>
						<td align="center">'.$link.'</td>
					</tr>';
				}
				$j++;
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
			$layoutLink='<a href="/s.php?module=purchaseApply&action=list&layout=list'.str_replace('&layout=grid', '', $queryPart).'">列表</a> 格子';
		}else{
			$layoutLink='列表 <a href="/s.php?module=purchaseApply&action=list&layout=grid'.str_replace('&layout=list', '', $queryPart).'">格子</a>';
		}
		if($this->kclass->input['show']=='all'){
			$showLink='<a href="/s.php?module=purchaseApply&action=list'.str_replace('&show=all', '', $queryPart).'">默认</a> 所有<span class="small gray">(包括删除)</span> <a href="/s.php?module=purchaseApply&action=list&show=verify'.str_replace('&show=all', '', $queryPart).'">已通过</a>  <a href="/s.php?module=purchaseApply&action=list&show=inverify'.str_replace('&show=all', '', $queryPart).'">待审核</a>  <a href="/s.php?module=purchaseApply&action=list&show=unverify'.str_replace('&show=all', '', $queryPart).'">未通过</a>';
		}elseif($this->kclass->input['show']=='verify'){
			$showLink='<a href="/s.php?module=purchaseApply&action=list'.str_replace('&show=verify', '', $queryPart).'">默认</a> <a href="/s.php?module=purchaseApply&action=list&show=all'.str_replace('&show=verify', '', $queryPart).'">所有<span class="small gray">(包括删除)</span></a> 已通过 <a href="/s.php?module=purchaseApply&action=list&show=inverify'.str_replace('&show=verify', '', $queryPart).'">待审核</a>  <a href="/s.php?module=purchaseApply&action=list&show=unverify'.str_replace('&show=verify', '', $queryPart).'">未通过</a>';
		}elseif($this->kclass->input['show']=='inverify'){
			$showLink='<a href="/s.php?module=purchaseApply&action=list'.str_replace('&show=inverify', '', $queryPart).'">默认</a> <a href="/s.php?module=purchaseApply&action=list&show=all'.str_replace('&show=inverify', '', $queryPart).'">所有<span class="small gray">(包括删除)</span></a> <a href="/s.php?module=purchaseApply&action=list&show=verify'.str_replace('&show=inverify', '', $queryPart).'">已通过</a> 待审核 <a href="/s.php?module=purchaseApply&action=list&show=unverify'.str_replace('&show=inverify', '', $queryPart).'">未通过</a>';
		}elseif($this->kclass->input['show']=='unverify'){
			$showLink='<a href="/s.php?module=purchaseApply&action=list'.str_replace('&show=unverify', '', $queryPart).'">默认</a> <a href="/s.php?module=purchaseApply&action=list&show=all'.str_replace('&show=unverify', '', $queryPart).'">所有<span class="small gray">(包括删除)</span></a> <a href="/s.php?module=purchaseApply&action=list&show=inverify'.str_replace('&show=unverify', '', $queryPart).'">已通过</a> <a href="/s.php?module=purchaseApply&action=list&show=inverify'.str_replace('&show=unverify', '', $queryPart).'">待审核</a> 未通过';
		}else{
			$showLink='默认 <a href="/s.php?module=purchaseApply&action=list&show=all'.str_replace('&show=all', '', $queryPart).'">所有<span class="small gray">(包括删除)</span></a> <a href="/s.php?module=purchaseApply&action=list&show=verify'.str_replace('&show=all', '', $queryPart).'">已通过</a>  <a href="/s.php?module=purchaseApply&action=list&show=inverify'.str_replace('&show=all', '', $queryPart).'">待审核</a>  <a href="/s.php?module=purchaseApply&action=list&show=unverify'.str_replace('&show=all', '', $queryPart).'">未通过</a>';
		}
		$this->kclass->page['title'].='采购单列表';
		$this->kclass->page['onload'].='dc.listhover();';
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - 采购申请单列表','right' => '<span class="small">排序：</span>'.$orderby['link'].'|　<span class="small">显示方式：</span>'.$showLink.'　|　<span class="small">布局：</span>'.$layoutLink.'　|　<a href="/s.php?module=purchaseApply&action=add">新建</a>','body'=>$body));
	}
	//
	function find(){
		$body=<<<EOF
<form action="/s.php?module=purchaseapply&action=list" method="get">
<input type="hidden" name="module" value="purchaseapply">
<input type="hidden" name="action" value="list">
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
<td width="100">申请单编号：</td><td><input type="text" name="applyno"></td>
</tr>
<tr class="even">
<td class="small gray" colspan="9">
注意事项：<br>
①查找采购申请单时，可以通过输入“申请单编号”直接查找，也可以通过输入“日期”和“物料编号”进行查找<br>
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
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - 查找采购申请单','right' => '<a href="/s.php?module=purchaseApply">查看列表</a>','body'=>$body));
	}
	//
	function view(){
		$apply=$this->kclass->DB->queryFirst("
			SELECT p.purchaseApplyid,p.applyno,p.applicant,p.ifVerify,p.ifApprove,p.purpose,p.remark,p.typeid,
					p.verifyRemark,p.approvalRemark,p.modified,p.verified,p.approved,p.killed,p.created,
				d.title AS department,
				u.username AS creator,
				us.username AS modifier,
				ur.username AS verifier,
				usr.username AS approver
			FROM purchaseapply AS p
			LEFT JOIN department AS d ON (d.departmentid=p.departmentid)
			LEFT JOIN user AS u ON (u.userid=p.creator)
			LEFT JOIN user AS us ON (us.userid=p.modifier)
			LEFT JOIN user AS ur ON (ur.userid=p.verifier)
			LEFT JOIN user AS usr ON (usr.userid=p.verifier)
			WHERE p.purchaseApplyid='".$this->kclass->input['purchaseApplyid']."'
			LIMIT 0,1
		");
		if(!$apply){
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
		$types=$this->kclass->DB->query("SELECT title FROM purchasetype WHERE killed=0 AND typeid IN (".$apply['typeid'].")");
		if($counter=$this->kclass->DB->numRows()){
			$i=1;
			while($type=$this->kclass->DB->fetchArray($types)){
				if($i<$counter){
					$typeValue.=$type['title'].'、';
				}else{
					$typeValue.=$type['title'];
				}
				$i++;
			}
		}
		$apply['created']=date('Y-m-d H:i:s',$apply['created']);
		$verify=$this->kclass->verify($apply['ifVerify']);
		if($apply['modified']!=0)$modify='，由'.$apply['modifier'].'于'.date('Y-m-d H:i:s',$apply['modified']).'修改';
		if($apply['verified']!=0){
			$averify.='，由'.$apply['verifier'].'于'.date('Y-m-d H:i:s',$apply['verified']).'审核';
			$remark.='<tr class="even"><td>审核备注：</td><td>'.$apply['verifyRemark'].'</td><td></td><td></td></tr>';
		}
		if($apply['approved']!=0){
			$averify.='，由'.$apply['approver'].'于'.date('Y-m-d H:i:s',$apply['approved']).'审批';
			$remark.='<tr class="even"><td>审批备注：</td><td>'.$apply['approvalRemark'].'</td><td></td><td></td></tr>';
		}
		if($apply['ifApprove']==0){
			if($apply['ifVerify']==0){
				if($this->kclass->purviews(array('module'=>'purchaseApply','action'=>'verify','final'=>1))){
					$operate.='　<a href="/s.php?module=purchaseApply&action=remove&purchaseApplyid='.$apply['purchaseApplyid'].'&rt=view">删除</a>　<a href="/s.php?module=purchaseApply&action=verify&purchaseApplyid='.$apply['purchaseApplyid'].'" >审核</a>';
				}
			}elseif($apply['ifVerify']==1){
				$operate.='　<a href="/s.php?module=purchaseApply&action=approve&purchaseApplyid='.$apply['purchaseApplyid'].'" >审批</a>';
			}
			$operate.='　<a href="/s.php?module=purchaseApply&action=update&purchaseApplyid='.$apply['purchaseApplyid'].'">修改</a>';
		}elseif($apply['ifApprove']==-1){
			$operate.='<a href="/s.php?module=purchaseApply&action=remove&purchaseApplyid='.$apply['purchaseApplyid'].'&rt=view">删除</a>';
		}

		//申请的物资
		$items=$this->kclass->DB->query("
			SELECT p.itemid,p.module,p.mid,p.materialid,p.materialno AS newMaterialno,p.title AS newTitle,p.standard AS newStandard,p.versionid,p.quantity,p.dateline,p.remark,
				m.materialno,m.title,m.standard,
				u.title AS unit,
				mv.title AS version
			FROM preinbounditem AS p
			LEFT JOIN material AS m ON (m.materialid=p.materialid)
			LEFT JOIN unit AS u ON (u.unitid=p.unitid)
			LEFT JOIN materialversion AS mv ON (mv.versionid=p.versionid)
			WHERE p.module='purchaseApply' AND p.killed=0 AND p.mid='".$apply['purchaseApplyid']."'
			ORDER BY p.ordering ASC
		");
		if($this->kclass->DB->numRows()){
			$i=1;
			while($item=$this->kclass->DB->fetchArray($items)){
				$itemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').'" nohover>';
				if($item['materialid']==0){
					$itemtr.='<td>'.$i.'</td>
						<td>'.$item['newMaterialno'].'</td>
						<td>'.$item['newTitle'].'</td>
						<td>'.$item['newStandard'].'</td>';
				}else{
					$itemtr.='<td>'.$i.'</td>
						<td><a href="/s.php?module=material&action=view&materialid='.$item['materialid'].'">'.$item['materialno'].'</a></td>
						<td>'.$item['title'].'</td>
						<td>'.$item['standard'].'</td>';
				}
				$itemtr.='<td>'.$item['version'].'</td><td>'.$item['quantity'].$item['unit'].'</td><td>'.date('Y-m-d',$item['dateline']).'</td><td>'.$item['remark'].'</td></tr>';
				$i++;
			}
		}
		$attach = $this->kclass->getAttachs(array('module'=>'purchaseApply', 'mid'=>$apply['purchaseApplyid']));
		if($attach!= false){
			$attachs = '<div class="clear">'.$attach.'</div>';
		}
$body=<<<EOF
<div class="title"><span class="right small gray">由{$apply['creator']}于{$apply['created']}建立{$modify}{$averify}。</span>{$apply['applyno']}</div>
<table class="hundred">
<thead><tr><th colspan="4">采购申请单 </th></tr></thead>
<tbody>
<tr class="even">
<td width="100">编　　号：</td><td width="475">{$apply['applyno']}（{$verify}）</td>
<td width="100">采购类型：</td><td>{$typeValue}</td>
</tr>
<tr class="odd">
<td>部　　门：</td><td>{$apply['department']}</td>
<td>申 请 人：</td><td>{$apply['applicant']}</td>
</tr>
<tr class="even">
<td>用　　途：</td><td>{$apply['purpose']}</td>
<td>备　　注：</td><td>{$apply['remark']}</td>
</tr>
{$remark}
</tbody>
</table>
<table class="hundred small">
<thead><tr><th colspan="8">采购申请物资</th></tr></thead>
<tbody>
<tr class="center even" nohover>
<td width="30">ID</td><td>物资编号</td><td>物资名称</td><td>物资规格</td><td>版本</td><td>数量/单位</td><td>到货时间</td><td>备注</td>
</tr>
{$itemtr}
</tbody>
</table>
{$attachs}
EOF;
		$this->kclass->page['title'].='采购申请单';
		$this->kclass->page['onload'].='dc.tabhover();';
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - <a href="/s.php?module=purchaseApply&action=list">采购申请单列表</a> - 采购申请单', 'right' => $this->kclass->iif($apply['ifApprove']==1,'<span class = "small">打印：</span><a href="/p.php?action=purchaseApply&purchaseApplyid='.$apply['purchaseApplyid'].'">采购申请单</a>　|　','').'<span class = "small">操作：</span>'.$this->kclass->iif($apply['killed']==0,' <a href="/s.php?module=purchaseApply&action=add">新建</a>'.$operate,'<a href="/s.php?module=purchaseApply&action=restore&purchaseApplyid='.$this->kclass->input['purchaseApplyid'].'&rt=view">恢复</a>'),'body'=>$body));
	}
	// a form  for add a purchase apply
	function add(){
		$upload=$this->kclass->upload(array('title'=>'相关附件：'));
		$start=strtotime(date('Y-m-d',TIMENOW));
		$end=strtotime(date('Y-m-d',$start))+86400;
		$counter=$this->kclass->DB->queryFirst("SELECT COUNT(*) AS count FROM purchaseapply WHERE killed=0 AND created>='".$start."' AND created<='".$end."'");
		$applyno=$this->kclass->id(array('purchaseApply'=>TIMENOW,'number'=>$counter['count']));
		$department=$this->kclass->DB->queryFirst("SELECT title FROM department WHERE departmentid='".$this->kclass->user['departmentid']."'");
		$types=$this->kclass->DB->query("SELECT typeid,module,title FROM purchasetype WHERE killed=0 ORDER BY created ASC");
		if($this->kclass->DB->numRows()){
			$i=1;
			while($type=$this->kclass->DB->fetchArray($types)){
				if($type['module']=='produce'){
					$chooserType1.='<input type="checkbox" name="typeid['.$i.']" value="'.$type['typeid'].'">'.$type['title'];
				}elseif($type['module']=='unproduce'){
					$chooserType2.='<input type="checkbox" name="typeid['.$i.']" value="'.$type['typeid'].'">'.$type['title'];
				}
				$i++;
			}
		}
		if($this->kclass->input['type']=='material'){ // 在生产计划列表中。获得未达到安全库存的物资。
			$count=count($this->kclass->input['itemMaterialid']);
			$selectCount=count($this->kclass->input['itemMaterialSelect']);
			$j=1;
			for($i=1;$i<$count+1;$i++){
				if($this->kclass->input['itemMaterialSelect'][$i]>0){
					$material=$this->kclass->DB->queryFirst("
						SELECT m.materialid,m.materialno,m.title AS material,m.standard,m.max,m.qualified
						FROM material AS m
						WHERE m.materialid='".$this->kclass->input['itemMaterialSelect'][$i]."'
					");
					$itemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').'">
						<td><input type="hidden" name="itemMaterialid['.$j.']" value="'.$material['materialid'].'">'.$i.'</td>
						<td><a href="/s.php?module=material&action=view&materialid='.$material['materialid'].'">'.$material['materialno'].'</a>　'.$material['title'].'　'.$material['standard'].'</td>
						<td></td>
						<td>'.$this->kclass->chooserMaterialVersion(array('name'=>'itemVersionid['.$j.']','width' =>200,'id'=>'itemVersionid'.$j,'materialid'=>$material['materialid'])).'</td>
						<td><input type="text" name="itemQuantity['.$j.']" value="'.abs($material['max']-$material['qualified']).'" size="9"/></td>
						<td>'.$this->kclass->chooserUnit(array('name'=>'itemUnitid['.$j.']','width'=>'85','hasBlank'=>1,'selectedid'=>5)).'</td>
						<td><input type="text" name="itemDateline['.$j.']" id="dateline'.$j.'" value="'.date('Y-m-d',TIMENOW).'" size="10" /></td>
						<td><input type="text" name="itemRemark['.$j.']" style="width:150px;" /></td></tr>';
					if($j<$selectCount){
						$id.='#dateline'.$j.',';
					}else{
						$id.='#dateline'.$j;
					}
					$j++;
				}
			}
		}else{
			for($i=1;$i<=12;$i++){
				$itemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').' small">
					<td>'.$i.'</td>
					<td>'.$this->kclass->chooserMaterial(array('hName'=>'itemMaterialid['.$i.']','hId'=>'itemMaterialid'.$i,'name'=>'itemMaterial['.$i.']','id'=>'itemMaterial'.$i,'width'=>650,'line'=>$i,'showVersion'=>1)).'</td>
					<td><input type="text" name="itemStandard['.$i.']" style="width:200px" /></td>
					<td><select name="itemVersionid['.$i.']" id="itemVersionid'.$i.'" style="width:200px;"></select></td>
					<td><input type="text" name="itemQuantity['.$i.']" size="9" /></td>
					<td>'.$this->kclass->chooserUnit(array('name'=>'itemUnitid['.$i.']','width'=>'85','hasBlank'=>1,'selectedid'=>5)).'</td>
					<td><input type="text" name="itemDateline['.$i.']" id="dateline'.$i.'" size="10" /></td>
					<td><input type="text" name="itemRemark['.$i.']" style="width:150px;" /></td></tr>';
				if($i<12){
					$id.='#dateline'.$i.',';
				}else{
					$id.='#dateline'.$i;
				}
			}
		}
		$body=<<<EOF
<form action="/s.php?module=purchaseApply&action=insert" name="purchaseApply" method="post">
<input type="hidden" name="module" value="purchaseApply" />
<input type="hidden" name="action" value="insert" />
<input type="hidden" name="applyno" value="{$applyno}" />
<input type="hidden" name="departmentid" value="{$this->kclass->user['departmentid']}" />
<table class="hundred">
<thead><tr><th colspan="4">基本信息</th></tr></thead>
<tbody>
<tr class="even">
<td>编　　号：</td><td><span class="middle bold darkred">{$applyno}</span>　　<span class="small gray">（此为预估编号）</span></td>
<td>申请人员：<span class="bold red">*</span></td><td><input type="text" name="applicant" value="{$this->kclass->user['realname']}"></td>
</tr>
<tr class="odd">
<td>部　　门：<span class="bold red">*</span></td><td>{$department['title']} </td>
<td>采购类型：<span class="bold red">*</span></td><td>生产类：{$chooserType1}　非生产类：{$chooserType2}</td>
</tr>
<tr class="even">
<td>用　　途：<span class="bold red">*</span></td>
<td><textarea type="text" name="purpose" style="width:444px;height:100px">空</textarea>
<td>备　　注：</td>
<td><textarea type="text" name="remark" style="width:444px;height:100px"></textarea>
</tr>
</tbody>
</table>
<table class="hundred">
<thead><tr><th colspan="10">申请采购的物资</th></tr></thead>
<tbody>
<tr class="even center" nohover><td width="15">ID</td><td>物资 <span class="bold red">*</span></td><td>物资规格</td><td>版本</td><td>数量 <span class="bold red">*</span></td><td>单位</td><td>到货时间 <span class="bold red">*</span></td><td>备注</td></tr>
{$itemtr}
<tr class="even">
<td class="small gray" colspan="10">
注意事项：<br>
①如果所列表格不够，那么在提交保存后再修改采购申请。<br>
②若要采购新物资，则必须填写物资名称，以及物资规格，不用选择版本；否则在“物资信息”中间选择物资，“物资名称”、“物资规格”不需要填写。<br>
③从库中物资选择时，“单位”不需要选，但是如果申请采购的物资不存在库中时，就必须要选择“单位”。</td>
</tr>
</tbody>
</table>
{$upload}
<table class="tlist tsuite" align="center" width="100%">
<tbody>
<tr class="odd" nohover>
<td class="center" colspan=4><input type="submit" id="submitButton" value="  提交  " accesskey="s" onclick="dc.purchase.checkApply(document.apply)"><input type="reset" value="  复位  "/></td>
</tr>
</tbody>
</table>
</form>
EOF;
		$this->kclass->tbline+=4;
		$this->kclass->page['title'].='新建采购申请';
		$this->kclass->page['onload'].='var dates=$(\''.$id.'\').datepicker({onSelect:function(selectedDate){instance=$(this).data(\'datepicker\'),date=$.datepicker.parseDate(instance.settings.dateFormat || $.datepicker._defaults.dateFormat,selectedDate,instance.settings);}});dc.tabhover();';
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - <a href="/s.php?module=purchaseApply&action=list">采购申请列表</a> - 新建采购申请', 'right'=>'<a href="/s.php?module=purchaseApply&action=list">返回列表</a>','body'=>$body));
	}
	// insert purchase apply
	function insert(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			if(!$this->kclass->input['applicant']){
				$e .= '<li>请选择采购申请单中的 申请人员。</li>';
			}
			if($this->kclass->input['departmentid'] <= 0){
				$e .= '<li>请选择采购申请单中的 部门。</li>';
			}
			if(!$this->kclass->input['purpose']){
				$e .= '<li>请选择采购申请单中的 目的。</li>';
			}
			if($this->kclass->input['typeid']==''){
				$e .= '<li>请选择采购申请单中的 采购类型。</li>';
			}
			$start=strtotime(date('Y-m-d',TIMENOW));
			$end=strtotime(date('Y-m-d',$start))+86400;
			$counter=$this->kclass->DB->queryFirst("SELECT COUNT(*) AS count FROM purchaseapply WHERE killed=0 AND created>='".$start."' AND created<='".$end."'");
			$applyno=$this->kclass->id(array('purchaseApply'=>TIMENOW,'number'=>$counter['count']));
			$apply=$this->kclass->DB->queryFirst("SELECT * FROM purchaseapply WHERE killed=0 AND applyno='".$applyno."'");
			if($apply){
				$e.='已经存在采购申请单号 <b>'.$applyno.'</b> 请返回刷新页面重新填写';
			}
			$j=count($this->kclass->input['itemMaterialid']);
			$hasItem=0;
			for($i=1;$i<=$j;$i++){
				if(($this->kclass->input['itemMaterialid'][$i]>0 OR $this->kclass->input['itemMaterial'][$i]!='') AND $this->kclass->input['itemVersionid'][$i]>0 AND $this->kclass->input['itemQuantity'][$i]>0 AND $this->kclass->input['itemDateline'][$i]!=''){
					$hasItem=1;
				}
			}
			if($hasItem==0){
				$e .= '<li>需要填写至少有一条订单明细，才能建立订单。</li>';
			}
		}
		if($e){
			$this->kclass->messager(array(
				'title' => '新建采购申请',
				'text' => '您在新建采购申请的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$count=count($this->kclass->input['typeid']);
		$i=1;
		foreach($this->kclass->input['typeid'] as $key => $val){
			if($i<$count){
				$typeid.=$val.',';
			}else{
				$typeid.=$val;
			}
			$i++;
		}
		$this->kclass->input['requirementid']=$this->kclass->iif($this->kclass->input['requirementid']!='',$this->kclass->input['requirementid'],0);
		$this->kclass->DB->query("
			INSERT INTO purchaseapply (applyno,requirementid,typeid,departmentid,applicant,purpose,remark,created,creator)
			VALUES ('".$applyno."','".$this->kclass->input['requirementid']."','".$typeid."','".$this->kclass->input['departmentid']."','".$this->kclass->input['applicant']."','".$this->kclass->input['purpose']."','".$this->kclass->input['remark']."','".TIMENOW."','".$this->kclass->user['userid']."')
		");
		$purchaseApplyid=$this->kclass->DB->insertID();

		for($m=1;$m<$j+1;$m++){
			if(($this->kclass->input['itemMaterialid'][$m]!=0 OR $this->kclass->input['itemMaterial'][$m]!='') AND $this->kclass->input['itemVersionid'][$m]>0 AND $this->kclass->input['itemQuantity'][$m]>0 AND $this->kclass->input['itemDateline'][$m]!=''){
				if($this->kclass->input['itemDateline'][$m]!=''){
					$sd=explode('-',$this->kclass->input['itemDateline'][$m]);
					$dateline=mktime(0,0,0,$sd[1],$sd[2],$sd[0]);
				}else{
					$dateline=0;
				}
				$material=$this->kclass->DB->queryFirst("SELECT unitid FROM material WHERE killed=0 AND materialid='".$this->kclass->input['itemMaterialid'][$m]."'");
				$unitid=$this->kclass->iif($this->kclass->input['itemMaterialid'][$m]==0,$this->kclass->input['itemUnitid'][$m],$material['unitid']);
				$this->kclass->input['itemId'][$m]=$this->kclass->iif($this->kclass->input['itemId'][$m]!='',$this->kclass->input['itemId'][$m],0);
				$this->kclass->DB->query("
					INSERT INTO preinbounditem (module,mid,materialid,title,standard,versionid,mrpid,quantity,unitid,dateline,remark,created,creator)
					VALUES ('purchaseApply','".$purchaseApplyid."','".$this->kclass->input['itemMaterialid'][$m]."','".$this->kclass->input['itemMaterial'][$m]."','".$this->kclass->input['itemStandard'][$m]."','".$this->kclass->input['itemVersionid'][$m]."','".$this->kclass->input['itemId'][$m]."','".$this->kclass->input['itemQuantity'][$m]."','".$unitid."','".$dateline."','".$this->kclass->input['itemRemark'][$m]."','".TIMENOW."','".$this->kclass->user['userid']."')
				");
				if($this->kclass->input['itemId'][$m]>0){
					$this->kclass->DB->query("UPDATE mrp SET ifChooser=1 WHERE mrpid='".$this->kclass->input['itemId'][$m]."'");
				}
			}
		}
		$this->kclass->updateAttachs(array('module'=>'purchaseApply', 'mid'=>$purchaseApplyid));
		$this->kclass->messager(array(
			'title' => '新建采购申请',
			'text' => '采购申请单 【<b>'.$applyno.'</b>】已新建成功!返回添加申请采购物资',
			'url' => '/s.php?module=purchaseApply&action=view&purchaseApplyid='.$purchaseApplyid,
			'sec' => 2
		));
	}
	//
	function update(){
		$apply=$this->kclass->DB->queryFirst("
			SELECT purchaseApplyid,applyno,typeid,requirementid,departmentid,applicant,ifVerify,ifApprove,purpose,remark,created
			FROM purchaseapply
			WHERE killed=0 AND purchaseApplyid='".$this->kclass->input['purchaseApplyid']."'
			LIMIT 0,1
		");
		if(!$apply OR $apply['ifApprove']!=0){
			$this->kclass->messager(array(
				'title' => '修改采购',
				'text' => '选择的采购单数据错误',
				'url' => '/s.php?module=purchaseApply&action=list',
				'sec' => 2
			));
		}
		$typeId=explode(',',$apply['typeid']);
		$department=$this->kclass->chooserDepartment(array('name'=>'departmentid','hasBlank'=>1,'width'=>200,'selectedid'=>$apply['departmentid']));
		$upload=$this->kclass->upload(array('title'=>'相关附件：','module'=>'purchaseApply','mid'=>$apply['purchaseApplyid']));
		$types=$this->kclass->DB->query("SELECT typeid,module,title FROM purchasetype WHERE killed=0 ORDER BY created ASC");
			if($this->kclass->DB->numRows()){
				$i=1;
				while($type=$this->kclass->DB->fetchArray($types)){
					if($type['module']=='produce'){
						$chooserType1.='<input type="checkbox" ';
						if(in_array($type['typeid'],$typeId)){
							$chooserType1.='checked';
						}
						$chooserType1.=' name="typeid['.$i.']" value="'.$type['typeid'].'">'.$type['title'];
					}elseif($type['module']=='unproduce'){
						$chooserType2.='<input type="checkbox" ';
						if(in_array($type['typeid'],$typeId)){
							$chooserType2.='checked';
						}
						$chooserType2.=' name="typeid['.$i.']" value="'.$type['typeid'].'">'.$type['title'];
					}
					$i++;
				}
			}

		// purchase apply item
		$items=$this->kclass->DB->query("
			SELECT p.itemid,p.module,p.mid,p.materialid,p.materialno AS newMaterialno,p.title AS newTitle,p.standard AS newStandard,p.quantity,p.unitid,p.dateline,p.remark,
				m.materialno,m.title,m.standard
			FROM preinbounditem AS p
			LEFT JOIN material AS m ON (p.materialid=m.materialid)
			WHERE p.module='purchaseApply' AND p.killed=0 AND p.mid='".$this->kclass->input['purchaseApplyid']."'
			ORDER BY p.ordering ASC
		");
		if($this->kclass->DB->numRows()){
			$k=1;
			$this->kclass->tbline+=1;
			while($item=$this->kclass->DB->fetchArray($items)){
				if($item['materialid']==0){
					$standard=$item['newStandard'];
					$material=$item['newTitle'];
				}else{
					$material=$item['materialno'].'　'.$item['title'].'　'.$item['standard'];
				}
				$itemList.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd', 'even').' small">
					<td><input type="hidden" name="itemId['.$k.']" value="'.$item['itemid'].'">'.$this->kclass->chooserMaterial(array('hName'=>'itemMaterialid['.$k.']','hId'=>'itemMaterialid'.$k,'name'=>'itemMaterial['.$k.']','id'=>'itemMaterial'.$k,'width'=>400,'line'=>$k,'selectedid'=>$item['materialid'],'value'=>$material)).'</td>
					<td><input type="text" name="itemStandard['.$k.']" value="'.$standard.'" style="width:200px"/></td>
					<td>'.$this->kclass->chooserMaterialVersion(array('name'=>'itemVersionid['.$k.']','selectedid'=>$item['versionid'],'width' =>80,'id'=>'itemVersionid'.$k,'materialid'=>$item['materialid'])).'</td>
					<td><input type="text" name="itemQuantity['.$k.']" value="'.$item['quantity'].'" size="10" /></td>
					<td>'.$this->kclass->chooserUnit(array('name'=>'itemUnitid['.$k.']','width'=>'85','hasBlank'=>1,'selectedid'=>$item['unitid'])).'</td>
					<td><input type="text" name="itemDateline['.$k.']" id="dateline'.$k.'" value="'.date('Y-m-d',$item['dateline']).'" /></td>
					<td><input type="text" name="itemRemark['.$k.']" style="width:150px;" value="'.$item['remark'].'" /></td>
					<td><input type="checkbox" name="itemKill['.$k.']" value="'.$item['itemid'].'"></td></tr>';
				$id.='#dateline'.$k.',';
				$k++;
			}
			$itemList.='<tr><td colspan="12">新建明细</td></tr>';
		}
		for($i=$k; $i<($k+5); $i++){
			$itemList.='<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').' small">
				<td>'.$this->kclass->chooserMaterial(array('hName'=>'itemMaterialid['.$i.']','hId'=>'itemMaterialid'.$i,'name'=>'itemMaterial['.$i.']','id'=>'itemMaterial'.$i,'width'=>400,'line'=>$i,'showVersion'=>1)).'</td>
					<td><input type="text" name="itemStandard['.$i.']" style="width:200px" /></td>
					<td><select name="itemVersionid['.$i.']" id="itemVersionid'.$i.'" style="width:80px;"></select></td>
					<td><input type="text" name="itemQuantity['.$i.']" size="10" /></td>
					<td>'.$this->kclass->chooserUnit(array('name'=>'itemUnitid['.$i.']','width'=>'85','hasBlank'=>1,'selectedid'=>5)).'</td>
					<td><input type="text" name="itemDateline['.$i.']" id="dateline'.$i.'" /></td>
					<td><input type="text" name="itemRemark['.$i.']" style="width:150px;" /></td>
				<td></td></tr>';
			if($i<($k+4)){
				$id.='#dateline'.$i.',';
			}else{
				$id.='#dateline'.$i;
			}
		}

$body=<<<EOF
<form action="/s.php?module=purchaseApply&action=doupdate" name="purchaseApply" method="post">
<input type="hidden" name="module" value="purchaseApply" />
<input type="hidden" name="action" value="doupdate" />
<input type="hidden" name="purchaseApplyid" value="{$this->kclass->input['purchaseApplyid']}" />
<input type="hidden" name="applyno" value="{$apply['applyno']}" />
<table class="hundred">
<thead><tr><th colspan="4">新建采购申请</th></tr></thead>
<tbody>
<tr class="even">
<td>编　　号：</td><td><span class="middle bold darkred">{$apply['applyno']}</span> <span class="gray small">单号不可更改</span></td>
<td>申 请 人：<span class="bold red">*</span></td><td><input type="text" name="applicant" value="{$apply['applicant']}" /></td>
</tr>
<tr class="odd">
<td>部　　门：<span class="bold red">*</span></td><td>{$department} </td>
<td>采购类型：<span class="bold red">*</span></td><td>生产类：{$chooserType1}　非生产类：{$chooserType2}</td>
</tr>
<tr class="even">
<td>用　　途：<span class="bold red">*</span></td>
<td><textarea type="text" name="purpose" id="purpose" style="width:444px;height:100px">{$apply['purpose']}</textarea>
<td>备　　注：</td>
<td><textarea type="text" name="remark" id="remark" style="width:444px;height:100px">{$apply['remark']}</textarea>
</tr>
</tbody>
</table>
<table class="hundred">
<thead><tr><th colspan="9">申请采购的物资</th></tr></thead>
<tbody>
<tr class="even center"><td>物资 <span class="bold red">*</span></td><td>物资规格</td><td>版本</td><td>数量 <span class="bold red">*</span></td><td>单位</td><td>到货时间 <span class="bold red">*</span></td><td>备注</td><td>删</td></tr>
{$itemList}
<tr class="even">
<td class="small gray" colspan=9">
注意事项：<br>
①如果所列表格不够，那么在提交保存后再添加申请的物资。<br>
②若要采购新物资，则选择“物资信息”中的“新物资”，必须填写物资名称；否则在“物资信息”中间选择物资，“物资编号”、“物资名称”、“物资规格”不需要填写</td>
</tr>
</tbody>
</table>
{$upload}
<table class="tlist tsuite" align="center" width="100%">
<tbody>
<tr class="odd" nohover>
<td align="center" colspan=4><input type="submit" id="submitButton" value="  提交  " accesskey="s" onclick="dc.purchase.checkApply(document.apply)"><input type="reset" value="  复位  "/></td>
</tr>
</tbody>
</table>
</form>
EOF;

		$this->kclass->tbline+=4;
		$this->kclass->page['title'].='修改采购申请';
		$this->kclass->page['onload'].='var dates=$(\''.$id.'\').datepicker({onSelect:function(selectedDate){instance=$(this).data(\'datepicker\'),date=$.datepicker.parseDate(instance.settings.dateFormat || $.datepicker._defaults.dateFormat,selectedDate,instance.settings);}});dc.tabhover()';
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - <a href="/s.php?module=purchaseApply&action=view&purchaseApplyid='.$this->kclass->input['purchaseApplyid'].'">查看采购申请</a> - 修改采购申请', 'right'=>'<a href="/s.php?module=purchaseApply&action=list">返回列表</a>　|　<a href="/s.php?module=purchaseApply&action=view&purchaseApplyid='.$this->kclass->input['purchaseApplyid'].'">查看采购申请</a>','body'=>$body));
	}
	//
	function doupdate(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			$apply=$this->kclass->DB->queryFirst("SELECT purchaseApplyid,applyno FROM `purchaseapply` WHERE purchaseApplyid='".$this->kclass->input['purchaseApplyid']."'");
			if(!$apply){
				$e .= '<li>您要编辑的采购申请单并不存在，请返回列表刷新后再操作。</li>';
			}
			$j = count($this->kclass->input['itemMaterialid']);
			$hasItem = 0;
			for($i=1;$i<=$j;$i++){
				if($this->kclass->input['itemKill'][$i]=='' AND ($this->kclass->input['itemMaterialid'][$i]>0 OR $this->kclass->input['itemMaterial'][$i]!='') AND $this->kclass->input['itemVersionid'][$i]>0 AND $this->kclass->input['itemQuantity'][$i]>0 AND $this->kclass->input['itemDateline'][$i]!=''){
					$hasItem=1;
				}
			}
			if($hasItem==0){
				$e .= '<li>需要填写至少有一条订单明细，才能保存订单。</li>';
			}
		}
		if($e){
			$this->kclass->messager(array(
				'title' => '修改采购申请单',
				'text' => '您在修改采购申请单的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		//明细
		$count=count($this->kclass->input['itemMaterialid']);
		for($m=1;$m<$count+1;$m++){
			if(($this->kclass->input['itemMaterialid'][$m]>0 OR $this->kclass->input['itemMaterial'][$m]!='') AND $this->kclass->input['itemVersionid'][$m]>0 AND $this->kclass->input['itemQuantity'][$m]>0 AND $this->kclass->input['itemQuantity'][$m]!=''){
				$this->kclass->input['itemQuantity'][$m]=intVal($this->kclass->input['itemQuantity'][$m]);
				if($this->kclass->input['itemDateline'][$m]!=''){
					$sd=explode('-',$this->kclass->input['itemDateline'][$m]);
					$dateline=mktime(0,0,0,$sd[1],$sd[2],$sd[0]);
				}else{
					$dateline=0;
				}
				$material=$this->kclass->DB->queryFirst("SELECT unitid FROM material WHERE killed=0 AND materialid='".$this->kclass->input['itemMaterialid'][$m]."'");
				$unitid=$this->kclass->iif($this->kclass->input['itemMaterialid'][$m]==0,$this->kclass->input['itemUnitid'][$m],$material['unitid']);
				if($this->kclass->input['itemId'][$m]>0){
					if($this->kclass->input['itemKill'][$m] AND $this->kclass->input['itemKill'][$m]==$this->kclass->input['itemId'][$m]){
						$this->kclass->DB->query("UPDATE `preinbounditem` SET killed=".TIMENOW.",killer='".$this->kclass->user['userid']."'  WHERE itemid='".$this->kclass->input['itemId'][$m]."'");
					}else{
						//update item
						$this->kclass->DB->query("
							UPDATE preinbounditem SET
								materialid='".$this->kclass->input['itemMaterialid'][$m]."',
								title='".$this->kclass->input['itemMaterial'][$m]."',
								standard='".$this->kclass->input['itemStandard'][$m]."',
								versionid='".$this->kclass->input['itemVersionid'][$m]."',
								quantity='".$this->kclass->input['itemQuantity'][$m]."',
								unitid='".$unitid."',
								dateline='".$dateline."',
								remark='".$this->kclass->input['itemRemark'][$m]."',
								modified='".TIMENOW."',
								modifier='".$this->kclass->user['userid']."'
							WHERE itemid='".$this->kclass->input['itemId'][$m]."'
						");
						$quantity+=$this->kclass->input['itemQuantity'][$m];
					}
				}else{
					$this->kclass->DB->query("
						INSERT INTO preinbounditem (module,mid,materialid,title,standard,versionid,quantity,unitid,dateline,remark,created,creator)
						VALUES
							('purchaseApply','".$this->kclass->input['purchaseApplyid']."','".$this->kclass->input['itemMaterialid'][$m]."','".$this->kclass->input['itemMaterial'][$m]."','".$this->kclass->input['itemStandard'][$m]."','".$this->kclass->input['itemVersionid'][$m]."','".$this->kclass->input['itemQuantity'][$m]."','".$unitid."','".$dateline."','".$this->kclass->input['itemRemark'][$m]."','".TIMENOW."','".$this->kclass->user['userid']."')
					");
					$quantity+=$this->kclass->input['itemQuantity'][$m];
				}
			}
		}
		// update purchase apply
		$count=count($this->kclass->input['typeid']);
		$i=1;
		foreach($this->kclass->input['typeid'] as $key => $val){
			if($i<$count){
				$typeid.=$val.',';
			}else{
				$typeid.=$val;
			}
			$i++;
		}
		$this->kclass->DB->query("
			UPDATE purchaseapply SET
				typeid='".$typeid."',
				departmentid='".$this->kclass->input['departmentid']."',
				applicant='".$this->kclass->input['applicant']."',
				purpose='".$this->kclass->input['purpose']."',
				remark='".$this->kclass->input['remark']."',
				ifVerify=0,
				verifier=0,
				verified=0,
				verifyRemark='',
				modified='".TIMENOW."',
				modifier='".$this->kclass->user['userid']."'
			WHERE purchaseApplyid='".$this->kclass->input['purchaseApplyid']."'
		");
		$this->kclass->updateAttachs(array('module'=>'purchaseApply', 'mid'=>$this->kclass->input['purchaseApplyid']));
		/*if($apply['ifVerify']==-1){
			$this->kclass->DB->query("
				UPDATE purchaseapply SET 
					ifVerify=0,
					verifier=0,
					verified=0,
					verifyRemark=''
				WHERE purchaseApplyid='".$this->kclass->input['purchaseApplyid']."'
			");
		}*/
		$this->kclass->messager(array(
			'title' => '修改采购申请单',
			'text' => '采购申请单 【<b>'.$this->kclass->input['applyno'].'</b>】 已修改成功!返回查看采购申请单',
			'url' => '/s.php?module=purchaseApply&action=view&purchaseApplyid='.$this->kclass->input['purchaseApplyid'],
			'sec' => 2
		));
	}

	//
	function verify(){
		$apply=$this->kclass->DB->queryFirst("
			SELECT p.purchaseApplyid,p.typeid,p.applyno,p.applicant,p.ifVerify,p.purpose,p.remark,p.modified,p.killed,p.created,
				d.title AS department,
				u.username AS creator,
				us.username AS modifier
			FROM purchaseapply AS p
			LEFT JOIN department AS d ON (d.departmentid=p.departmentid)
			LEFT JOIN user AS u ON (u.userid=p.creator)
			LEFT JOIN user AS us ON (us.userid=p.modifier)
			WHERE p.purchaseApplyid='".$this->kclass->input['purchaseApplyid']."'
			LIMIT 0,1
		");
		if(!$apply AND $apply['ifVerify']!=0){
			$e='<li>数据错误，很抱歉~</li>';
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '审核采购申请单',
				'text' => '审核采购申请单<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$types=$this->kclass->DB->query("SELECT title FROM purchasetype WHERE killed=0 AND typeid IN (".$apply['typeid'].")");
		if($counter=$this->kclass->DB->numRows()){
			$i=1;
			while($type=$this->kclass->DB->fetchArray($types)){
				if($i<$counter){
					$typeValue.=$type['title'].'、';
				}else{
					$typeValue.=$type['title'];
				}
				$i++;
			}
		}
		$apply['created']=date('Y-m-d',$apply['created']);
		$verify=$this->kclass->verify($apply['ifVerify']);
		if($apply['modified']!=0)$modify='，由'.$apply['modifier'].'于'.date('Y-m-d',$apply['modified']).'修改';
		//申请的物资
		$items=$this->kclass->DB->query("
			SELECT p.itemid,p.module,p.mid,p.materialid,p.materialno AS newMaterialno,p.title AS newTitle,p.standard AS newStandard,p.quantity,p.dateline,p.remark,
				m.materialno,m.title,m.standard
			FROM preinbounditem AS p
			LEFT JOIN material AS m ON (p.materialid=m.materialid)
			WHERE p.module='purchaseApply' AND p.killed=0 AND p.mid='".$apply['purchaseApplyid']."'
			ORDER BY p.ordering ASC
		");
		if($this->kclass->DB->numRows()){
			while($item=$this->kclass->DB->fetchArray($items)){
				if($item['materialid']==0){
					$itemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').'">
						<td>'.$item['newMaterialno'].'</td>
						<td>'.$item['newTitle'].'</td>
						<td>'.$item['newStandard'].'</td>';
				}else{
					$itemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').'">
						<td>'.$item['materialno'].'</td>
						<td>'.$item['title'].'</td>
						<td>'.$item['standard'].'</td>';
				}
				$itemtr.='<td>'.$item['quantity'].'</td><td>'.date('Y-m-d',$item['dateline']).'</td><td>'.$item['remark'].'</td></tr>';
			}
		}

$body=<<<EOF
<div class="title"><span class="right small gray">由{$apply['creator']}于{$apply['created']}建立{$modify}。</span>{$apply['applyno']}</div>
<table class="hundred">
<thead><tr><th colspan="4">采购申请单 </th></tr></thead>
<tbody>
<tr class="even">
<td width="100">采购申请编号：</td><td width="475">{$apply['applyno']}（{$verify}）</td>
<td width="100">采购类型：</td><td>{$typeValue}</td>
</tr>
<tr class="odd">
<td>部　　门：</td><td>{$apply['department']}</td>
<td>申 请 人：</td><td>{$apply['applicant']}</td>
</tr>
<tr class="even">
<td>用　　途：</td><td>{$apply['purpose']}</td>
<td>备　　注：</td><td>{$apply['remark']}</td>
</tr>
</tbody>
</table>
<table class="hundred small">
<thead><tr><th colspan="6">采购申请物资</th></tr></thead>
<tbody>
<tr class="center even" nohover>
<td>物资编号</td><td>物资名称</td><td>物资规格</td><td>物资数量</td><td>到达时间</td><td>备注</td>
</tr>
{$itemtr}
</tbody>
</table>
<form action="/s.php?module=purchaseApply&action=doverify" method="post">
<input type="hidden" name="module" value="purchaseApply">
<input type="hidden" name="action" value="doverify">
<input type="hidden" name="purchaseApplyid" value="{$this->kclass->input['purchaseApplyid']}">
<table>
<thead><tr><th colspan="4">审核 <span class="small red">(由具有需求部门主管审核，暂由采购部审核)</span></th></tr></thead>
<tbody>
<tr class="even">
<td>审　　核：<span class="bold red">*</span></td><td class="center"><input type="radio" name="ifVerify" value="1" />通过　　　<input type="radio" name="ifVerify" value="-1" checked />不通过</td>
</tr>
<tr class="odd">
<td>审核备注：</td><td><textarea name="verifyRemark" style="width:440px;height:110px;"></textarea></td>
</tr>
<tr class="even">
<td>选项：</td>
<td colspan="3">
	<label for="options1"><input type="radio" id="options1" name="ifPlan" value="1" checked>保存后，需要通过采购计划来选择购买。</label>
	<label for="options2"><input type="radio" id="options2" name="ifPlan" value="0">保存后，不需要通过采购计划来选择购买。</label>
</td>
</tr>
<tr class="odd">
<td colspan=2 class="center"><input type="submit" value=" 提交 " /><input type="reset" value="重置" /></td>
</tr>
</tbody>
</table>
</form>
EOF;
		$this->kclass->tbline+=4;
		$this->kclass->page['title'].='审核采购申请单';
		$this->kclass->page['onload'].='dc.tabhover();';
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - <a href="/s.php?module=purchaseApply&action=view&purchaseApplyid='.$this->kclass->input['purchaseApplyid'].'">查看采购申请单</a> - 审核采购申请单', 'right'=>'<a href="/s.php?module=purchaseApply&action=list">返回列表</a>　|　<a href="/s.php?module=purchaseApply&action=view&purchaseApplyid='.$this->kclass->input['purchaseApplyid'].'">查看采购申请单</a>','body'=>$body));
	}
	//
	function doverify(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			$apply=$this->kclass->DB->queryFirst("SELECT * FROM purchaseapply WHERE killed=0 AND purchaseApplyid='".$this->kclass->input['purchaseApplyid']."' LIMIT 0,1 ");
			if($apply['ifVerify']!=0){
				$e.='<li>采购单 <b>'.$apply['applyno'].'</b> 已经被审核过。</li>';
			}
			if($this->kclass->input['ifVerify']!=1 AND $this->kclass->input['ifVerify']!=-1){
				$e.='<li>请选择采购申请单中的 是否通过审核 按钮。</li>';
			}
		}
		if($e){
			$this->kclass->messager(array(
				'title' => '审核采购申请单',
				'text' => '您在审核采购申请单的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}

		$this->kclass->DB->query("
			UPDATE purchaseapply SET
				ifVerify='".$this->kclass->input['ifVerify']."',
				verifier='".$this->kclass->user['userid']."',
				verified='".TIMENOW."',
				verifyRemark='".$this->kclass->input['verifyRemark']."',
				ifPlan='".$this->kclass->input['ifPlan']."'
			WHERE purchaseApplyid='".$this->kclass->input['purchaseApplyid']."'
		");

		if($this->kclass->input['ifVerify']==1){
			$verify='已通过';
		}elseif($this->kclass->input['ifVerify']==-1){
			$verify='未通过';
		}
		$this->kclass->messager(array(
			'title' => '审核采购申请单',
			'text' => '采购申请单 【<b>'.$apply['applyno'].'</b>】 '.$verify.'审核!返回查看采购申请单',
			'url' => '/s.php?module=purchaseApply&action=view&purchaseApplyid='.$this->kclass->input['purchaseApplyid'],
			'sec' => 2
		));
	}
	//
	function approve(){
		$apply=$this->kclass->DB->queryFirst("
			SELECT p.purchaseApplyid,p.typeid,p.applyno,p.applicant,p.ifVerify,
					p.purpose,p.remark,p.verifyRemark,p.modified,p.verified,p.killed,p.created,
				d.title AS department,
				u.username AS creator,
				us.username AS modifier
			FROM purchaseapply AS p
			LEFT JOIN department AS d ON (d.departmentid=p.departmentid)
			LEFT JOIN user AS u ON (u.userid=p.creator)
			LEFT JOIN user AS us ON (us.userid=p.modifier)
			WHERE p.purchaseApplyid='".$this->kclass->input['purchaseApplyid']."'
			LIMIT 0,1
		");
		if(!$apply AND $apply['ifVerify']!=0){
			$e='<li>数据错误，很抱歉~</li>';
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '审核采购申请单',
				'text' => '审核采购申请单<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$types=$this->kclass->DB->query("SELECT title FROM purchasetype WHERE killed=0 AND typeid IN (".$apply['typeid'].")");
		if($counter=$this->kclass->DB->numRows()){
			$i=1;
			while($type=$this->kclass->DB->fetchArray($types)){
				if($i<$counter){
					$typeValue.=$type['title'].'、';
				}else{
					$typeValue.=$type['title'];
				}
				$i++;
			}
		}
		$apply['created']=date('Y-m-d',$apply['created']);
		$verify=$this->kclass->verify($apply['ifVerify']);
		$approve=$this->kclass->approve($apply['ifApprove']);
		if($apply['modified']!=0)$modify='，由'.$apply['modifier'].'于'.date('Y-m-d',$apply['modified']).'修改';
		if($apply['verified']!=0){
			$averify='，由'.$apply['verifier'].'于'.date('Y-m-d',$apply['verified']).'审核';
			$verifyRemark='<tr class="even"><td>审核备注：</td><td>'.$apply['verifyRemark'].'</td><td></td><td></td></tr>';
		}
		//申请的物资
		$items=$this->kclass->DB->query("
			SELECT p.itemid,p.module,p.mid,p.materialid,p.materialno AS newMaterialno,p.title AS newTitle,p.standard AS newStandard,p.quantity,p.dateline,p.remark,
				m.materialno,m.title,m.standard
			FROM preinbounditem AS p
			LEFT JOIN material AS m ON (p.materialid=m.materialid)
			WHERE p.module='purchaseApply' AND p.killed=0 AND p.mid='".$apply['purchaseApplyid']."'
			ORDER BY p.ordering ASC
		");
		if($this->kclass->DB->numRows()){
			while($item=$this->kclass->DB->fetchArray($items)){
				if($item['materialid']==0){
					$itemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').'">
						<td>'.$item['newMaterialno'].'</td>
						<td>'.$item['newTitle'].'</td>
						<td>'.$item['newStandard'].'</td>';
				}else{
					$itemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').'">
						<td>'.$item['materialno'].'</td>
						<td>'.$item['title'].'</td>
						<td>'.$item['standard'].'</td>';
				}
				$itemtr.='<td>'.$item['quantity'].'</td><td>'.date('Y-m-d',$item['dateline']).'</td><td>'.$item['remark'].'</td></tr>';
			}
		}

$body=<<<EOF
<div class="title"><span class="right small gray">由{$apply['creator']}于{$apply['created']}建立{$modify}。</span>{$apply['applyno']}</div>
<table class="hundred">
<thead><tr><th colspan="4">采购申请单 </th></tr></thead>
<tbody>
<tr class="even">
<td width="100">采购申请编号：</td><td width="475">{$apply['applyno']}（{$verify} , {$approve}）</td>
<td width="100">采购类型：</td><td>{$typeValue}</td>
</tr>
<tr class="odd">
<td>部　　门：</td><td>{$apply['department']}</td>
<td>申 请 人：</td><td>{$apply['applicant']}</td>
</tr>
<tr class="even">
<td>用　　途：</td><td>{$apply['purpose']}</td>
<td>备　　注：</td><td>{$apply['remark']}</td>
</tr>
{$verifyRemark}
</tbody>
</table>
<table class="hundred small">
<thead><tr><th colspan="6">采购申请物资</th></tr></thead>
<tbody>
<tr class="center even" nohover>
<td>物资编号</td><td>物资名称</td><td>物资规格</td><td>物资数量</td><td>到达时间</td><td>备注</td>
</tr>
{$itemtr}
</tbody>
</table>
<form action="/s.php?module=purchaseApply&action=doapprove" method="post">
<input type="hidden" name="module" value="purchaseApply">
<input type="hidden" name="action" value="doapprove">
<input type="hidden" name="purchaseApplyid" value="{$this->kclass->input['purchaseApplyid']}">
<table>
<thead><tr><th colspan="4">审批 <span class="small red">(由具有总经办审批，暂由采购部审核)</span></th></tr></thead>
<tbody>
<tr class="even">
<td>审　　批：<span class="bold red">*</span></td><td class="center"><input type="radio" name="ifApprove" value="1" />通过　　　<input type="radio" name="ifApprove" value="-1" checked />不通过</td>
</tr>
<tr class="odd">
<td>审批备注：</td><td><textarea name="approvalRemark" style="width:440px;height:110px;"></textarea></td>
</tr>
<tr class="even">
<td colspan=2 class="center"><input type="submit" value=" 提交 " /><input type="reset" value="重置" /></td>
</tr>
</tbody>
</table>
</form>
EOF;
		$this->kclass->tbline+=4;
		$this->kclass->page['title'].='审批采购申请单';
		$this->kclass->page['onload'].='dc.tabhover();';
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - <a href="/s.php?module=purchaseApply&action=view&purchaseApplyid='.$this->kclass->input['purchaseApplyid'].'">查看采购申请单</a> - 审批采购申请单', 'right'=>'<a href="/s.php?module=purchaseApply&action=list">返回列表</a>　|　<a href="/s.php?module=purchaseApply&action=view&purchaseApplyid='.$this->kclass->input['purchaseApplyid'].'">查看采购申请单</a>','body'=>$body));
	}
	//
	function doapprove(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			$apply=$this->kclass->DB->queryFirst("SELECT * FROM purchaseapply WHERE killed=0 AND purchaseApplyid='".$this->kclass->input['purchaseApplyid']."' LIMIT 0,1 ");
			if($apply['ifApprove']!=0){
				$e.='<li>采购单 <b>'.$apply['applyno'].'</b> 已经被审批过。</li>';
			}
			if($this->kclass->input['ifApprove']!=1 AND $this->kclass->input['ifApprove']!=-1){
				$e.='<li>请选择采购申请单中的 是否通过审批 按钮。</li>';
			}
		}
		if($e){
			$this->kclass->messager(array(
				'title' => '审批采购申请单',
				'text' => '您在审批采购申请单的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}

		$this->kclass->DB->query("
			UPDATE purchaseapply SET
				ifApprove='".$this->kclass->input['ifApprove']."',
				approver='".$this->kclass->user['userid']."',
				approved='".TIMENOW."',
				approvalRemark='".$this->kclass->input['approvalRemark']."'
			WHERE purchaseApplyid='".$this->kclass->input['purchaseApplyid']."'
		");

		if($this->kclass->input['ifApprove']==1){
			$approve='已通过';
		}elseif($this->kclass->input['ifApprove']==-1){
			$approve='未通过';
			$mrps=$this->kclass->DB->query("SELECT mrp.mrpid FROM mrp LEFT JOIN material ON (material.materialid=mrp.materialid) WHERE killed=0 AND requirementid='".$apply['requirementid']."' AND material.originid=2");
			if($this->kclass->DB->numRows()){
				while($mrp=$this->kclass->DB->fetchArray($mrps)){
					$this->kclass->DB->query("UPDATE mrp SET ifChooser=0 WHERE killed=0 AND mrpid='".$mrp['mrpid']."'");
				}
			}
		}
		$this->kclass->messager(array(
			'title' => '审批采购申请单',
			'text' => '采购申请单 【<b>'.$apply['applyno'].'</b>】 '.$approve.'审批!返回查看采购申请单',
			'url' => '/s.php?module=purchaseApply&action=view&purchaseApplyid='.$this->kclass->input['purchaseApplyid'],
			'sec' => 2
		));
	}
	//
	function kill(){
		if($this->kclass->input['purchaseApplyid']<=0){
			$this->kclass->boinkIt('/s.php?module=purchaseApply');
		}
		if($this->kclass->input['rt']!='list' AND $this->kclass->input['confirm']==0){
			$this->kclass->boinkIt('/s.php?module=purchaseApply&action=view&purchaseApplyid='.$this->kclass->input['purchaseApplyid']);
		}
		if($this->kclass->input['purchaseApplyid'] < 0){
			$e = '<li>请从正确的页面进行操作，如果此问题持续存在，请联系网站管理员。</li>';
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '删除采购申请',
				'text' => '您在删除采购申请的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		if($this->kclass->input['rt']=='view'){
			$rt = 'view&purchaseApplyid='.$this->kclass->input['purchaseApplyid'];
		}elseif($this->kclass->input['rt']=='list'){
			$rt = 'list#'.$this->kclass->input['purchaseApplyid'];
		}
		$apply=$this->kclass->DB->queryFirst("
			SELECT applyno
			FROM purchaseapply
			WHERE purchaseApplyid='".$this->kclass->input['purchaseApplyid']."'
		");
		if($apply){
			$this->kclass->DB->query("
				UPDATE `purchaseapply`
				SET killed=".TIMENOW.",killer='".$this->kclass->user['userid']."'
				WHERE purchaseApplyid='".$this->kclass->input['purchaseApplyid']."'
			");
			$this->kclass->messager(array(
				'title' => '删除采购申请成功',
				'text' => '采购申请 <b>'.$apply['applyno'].'</b> 已成功被标记为删除!',
				'url' => '/s.php?module=purchaseApply&action='.$rt,
				'sec' => 2
			));
		}else{
			$this->kclass->messager(array(
				'title' => '删除采购申请失败',
				'text' => '您要删除的采购申请，不存在！',
				'url' => '/s.php?module=purchaseApply&action='.$rt,
				'sec' => 3
			));
		}
	}

	//
	function remove(){
		if($this->kclass->input['purchaseApplyid']<=0){
			$this->kclass->boinkIt('/s.php?module=purchaseApply&action=list');
		}
		$apply=$this->kclass->DB->queryFirst("
			SELECT applyno
			FROM `purchaseapply`
			WHERE purchaseApplyid='".$this->kclass->input['purchaseApplyid']."'
		");
$body=<<<EOF
<form name="purchaseApply" method="post" action="/s.php?module=purchaseApply&action=kill">
<input type="hidden" name="module" value="purchaseApply">
<input type="hidden" name="action" value="kill">
<input type="hidden" name="purchaseApplyid" value="{$this->kclass->input['purchaseApplyid']}">
<input type="hidden" name="rt" value="{$this->kclass->input['rt']}">
<table><thead>
<thead>
<tr>
<th>删除采购申请单：{$apply['applyno']}</th>
</tr>
</thead>
<tbody>
<tr class="even">
<td class="middle">你确定要删除采购申请: <a href="/s.php?module=purchaseApply&action=view&purchaseApplyid={$this->kclass->input['purchaseApplyid']}" class="big bold" target="_blank">{$apply['applyno']}</a> 吗?</td>
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
		$this->kclass->page['title'] .= ' - 删除 - '.$apply['applyno'];
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 删除采购申请单 - '.$apply['applyno'], 'right' => '<a href="/s.php?module=purchaseApply&action=list">返回列表</a>', 'body'=>$body));
	}

	//
	function revival(){
		if($this->kclass->input['purchaseApplyid']<=0){
			$this->kclass->boinkIt('/s.php?module=purchaseApply&action=list');
		}
		if($this->kclass->input['rt']!='listApply' AND $this->kclass->input['confirm']==0){
			$this->kclass->boinkIt('/s.php?module=purchaseApply&action=view&purchaseApplyid='.$this->kclass->input['purchaseApplyid']);
		}
		if($this->kclass->input['purchaseApplyid'] < 0){
			$e = '<li>请从正确的页面进行操作，如果此问题持续存在，请联系网站管理员。</li>';
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '恢复采购申请单',
				'text' => '您在恢复采购申请单的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$apply=$this->kclass->DB->queryFirst("
			SELECT applyno
			FROM `purchaseapply`
			WHERE purchaseApplyid='".$this->kclass->input['purchaseApplyid']."'
		");
		if($this->kclass->input['rt']=='view'){
			$rt = 'view&purchaseApplyid='.$this->kclass->input['purchaseApplyid'];
		}elseif($this->kclass->input['rt']=='list'){
			$rt = 'list#'.$this->kclass->input['purchaseApplyid'];
		}
		if($apply){
			$this->kclass->DB->query("
				UPDATE `purchaseapply`
				SET killed=0,killer=0
				WHERE purchaseApplyid='".$this->kclass->input['purchaseApplyid']."'
			");
			$this->kclass->messager(array(
				'title' => '恢复采购申请单',
				'text' => '采购申请单 <b>'.$apply['applyno'].'</b> 已成功被恢复！',
				'url' => '/s.php?module=purchaseApply&action='.$rt,
				'sec' => 2
			));
		}else{
			$this->kclass->messager(array(
				'title' => '恢复采购申请单失败',
				'text' => '您要恢复的采购申请单不存在！',
				'url' => '/s.php?module=purchaseApply&action='.$rt,
				'sec' => 3
			));
		}
	}

	//
	function restore(){
		if($this->kclass->input['purchaseApplyid']<=0){
			$this->kclass->boinkIt('/s.php?module=purchaseApply&action=list');
		}
		$apply = $this->kclass->DB->queryFirst("
			SELECT applyno
			FROM `purchaseapply`
			WHERE purchaseApplyid='".$this->kclass->input['purchaseApplyid']."'
		");
$body = <<<EOF
<form action="/s.php?module=purchaseApply&action=revival" name="purchaseApply" method="post">
<input type="hidden" name="module" value="purchaseApply">
<input type="hidden" name="action" value="revival">
<input type="hidden" name="purchaseApplyid" value="{$this->kclass->input['purchaseApplyid']}">
<input type="hidden" name="rt" value="{$this->kclass->input['rt']}">
<table><thead>
<thead>
<tr>
<th>恢复采购申请单：{$apply['applyno']}</th>
</tr>
</thead>
<tbody>
<tr class="even">
<td class="middle">你确定要恢复采购申请单: <a href="/s.php?module=purchaseApply&action=view&purchaseApplyid={$this->kclass->input['purchaseApplyid']}" class="big bold" target="_blank">{$apply['applyno']}</a> 吗?</td>
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
		$this->kclass->page['title'] .= ' - 恢复 - '.$apply['applyno'];
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 恢复采购申请单 - '.$apply['applyno'], 'right' => '<a href="/s.php?module=purchaseApply&action=view">返回列表</a>', 'body'=>$body));
	}
}
?>