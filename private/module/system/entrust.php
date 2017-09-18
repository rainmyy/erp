<?php
class Entrust{
	function autoRun(){
		$this->kclass->page['title']='外发加工申请';
		$this->baseurl='<a href="/s.php">首页</a> - <a href="/s.php?module=entrust">外发加工</a>';
		$this->right='<a href="/s.php?module=entrust">列表</a> <a href="/s.php?module=entrust&action=add">新建</a>';
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
			case 'verify':
				$this->verify();
			break;
			case 'doverify':
				$this->doverify();
			break;
			case 'approve':
				$this->approve();
			break;
			case 'doapprove':
				$this->doapprove();
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
			case 'materiallink':
				$this->materiallink();
			break;
			case 'domateriallink':
				$this->domateriallink();
			break;
			default:
				$this->mmlist();
		}
	}
	// 
	function mmlist(){
		//echo $this->kclass->input['layout'];
		if($this->kclass->input['show']=='all'){
			$condition='1=1';
		}elseif($this->kclass->input['show']=='verify'){
			$condition='`entrust`.ifVerify=1';
		}elseif($this->kclass->input['show']=='unverify'){
			$condition='`entrust`.ifVerify=-1';
		}elseif($this->kclass->input['show']=='inverify'){
			$condition='`entrust`.ifVerify=0';
		}else{
			$condition='`entrust`.killed=0';
		}
		if($this->kclass->input['layout']!=''){
			$query['layout']=$this->kclass->input['layout'];
		}else{
			$query['layout']=$this->kclass->input['layout']='list';
		}
		$orderby=$this->kclass->orderby(array('module'=>'entrust','direction'=>'asc','orderby'=>'`entrust`.modified', 'default'=>'entrustid', 'serial'=>array(array('title'=>'编号', 'field'=>'entrustno'), array('title'=>'ID', 'field'=>'entrustid'), array('title'=>'修改时间', 'field'=>'modified'), array('title'=>'建立时间', 'field'=>'created')),'appendUrl'=>$query));
		if(is_array($query) AND count($query)>0)$queryPart='&'.http_build_query($query);
		$entrusts=$this->kclass->DB->query("
			SELECT `entrust`.entrustid,`entrust`.entrustno,`entrust`.applicant,
					`entrust`.ifVerify,`entrust`.ifApprove,`entrust`.modified,`entrust`.created,`entrust`.killed,
				u.username AS creator,
				us.username AS modifier
			FROM entrust 
			LEFT JOIN user AS u ON (u.userid=`entrust`.creator)
			LEFT JOIN user AS us ON (us.userid=`entrust`.modifier)
			WHERE ".$condition."
			ORDER BY ".$orderby['sql']."
		");
		if($this->kclass->DB->numRows()){
			$body=$this->kclass->input['layout']=='list'?'<table class="hundred mytable tablesorter"><thead><tr><th width="10%">ID</th><th width="15%">编号</th><th width="15%">审核状态</th><th width="15%">申请人</th><th width="15%">创建时间</th><th width="15%">修改时间</th><th width="15%">选项</th></tr></thead><tbody>':'<ul class="mmlist clear" id="mmlist">';
			$i=1;
			while($entrust=$this->kclass->DB->fetchArray($entrusts)){
				if($entrust['killed']>0){
					$link = '<a href="/s.php?module=entrust&action=restore&entrustid='.$entrust['entrustid'].'&rt=list">恢复</a>';
				}else{
					$link = '<a href="/s.php?module=entrust&action=update&entrustid='.$entrust['entrustid'].'&rt=list">修改</a>　<a href="/p.php?action=entrust&entrustid='.$entrust['entrustid'].'" target="_blank">打印</a>';
				}
		
				$entrust['created']=date('Y-m-d',$entrust['created']);
				if($entrust['modified']==0){
					$entrust['modified']='';	
				}else{
					$entrust['modified']=date('Y-m-d',$entrust['modified']);
				}
				
				$verify=$this->kclass->verify($entrust['ifVerify']);
				$approve=$this->kclass->approve($entrust['ifApprove']);
				if($this->kclass->input['layout']=='list'){ //表格
					$body.='<tr  '.$this->kclass->iif(($entrust['ifVerify']==1 and $entrust['ifApprove']==1), '','style="background:yellow"').'>
						<td>'.$i.'</td>
						<td><a href="/s.php?module=entrust&action=view&entrustid='.$entrust['entrustid'].'">'.$entrust['entrustno'].' </a></td>
						<td><span class="small">'.$verify.','.$approve.'</span></td>
						<td>'.$entrust['creator'].'</td>
						<td>'.$entrust['created'].'</td>
						<td>'.$entrust['modified'].'</td>
						<td>'.$link.'</td>
					</tr>';
					$i++;
				}else{
					
					$body.='<li title="由 '.$entrust['creator'].' 建于 '.$created.$this->kclass->iif($entrust['modifier']!='', '，'.$entrust['modifier'].' 改于 '.$entrust['modified'], '').'"'.$this->kclass->iif($i%4==0, ' class="end"', '').'>
					<div class="mmlistt">
					<span class="right normal">';
					if($entrust['killed']>0){
						$body.='<a href="/s.php?module=entrust&action=revival&entrustid='.$entrust['entrustid'].'&rt=list" onclick="return confirm(\'你确定要恢复这个采购单 '.$entrust['entrustno'].' 吗？\');">恢复</a>';
					}else{
						$body.='<a href="/s.php?module=entrust&action=kill&entrustid='.$entrust['entrustid'].'&rt=list" onclick="return confirm(\'你确定要删除这个采购单 '.$entrust['entrustno'].' 吗？\');">删</a> '.$this->kclass->iif($entrust['ifVerify']==0,'<a href="/s.php?module=entrust&action=update&entrustid='.$entrust['entrustid'].'&rt=list">改</a>','');
					}
					$body.='</span>
					<span class="small">'.$entrust['attr'].'</span> <a href="/s.php?module=entrust&action=view&entrustid='.$entrust['entrustid'].'">'.$entrust['entrustno'].'</a><br><span class="small gray right">'.$entrust['created'].'</span>'.$this->kclass->iif($entrust['attachs']>0, ' <span class="attachFile" title="有'.$entrust['attachs'].'个附件。"></span> ', '').$this->kclass->iif($entrust['images']>0, ' <span class="attachImage" title="有'.$entrust['images'].'个图片。"></span> ', '').''.$verify.'</div>
					<div class="mmlistb">
					<div><span class="small gray">申请人：'.$entrust['applicant'].'</span></div>
					<div class="small clear"><span class=right title="由 '.$entrust['creator'].' 建于 '.$entrust['created'].'">由 '.$entrust['creator'].' 建于 '.$entrust['created'].'</span></div></div></li>';
			
				}
			}
			$body.=$this->kclass->input['layout']=='list'?'</tbody></table>':'</ul>';
		}else{
			$body='暂无相关记录';
		}
		if($this->kclass->input['layout']=='list'){
			$layoutLink='列表 <a href="/s.php?module=entrust&action=list&layout=grid'.str_replace('&layout=list', '', $queryPart).'">格子</a>';
		}else{
			$layoutLink='<a href="/s.php?module=entrust&action=list&layout=list'.str_replace('&layout=grid', '', $queryPart).'">列表</a> 格子';
		}
		$this->kclass->page['onload']='dc.tabhover()';
		$this->kclass->page['title'].=' - 列表';
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl . ' - 列表','right'=> '<span class="small">排序：</span>'.$orderby['link'].'<span class="small">显示方式：</span><a href="/s.php?module=entrust">默认列表</a>　<a href="/s.php?module=entrust&action=list&show=all">所有<span class="small gray">(包括删除)</span></a>　<a href="/s.php?module=entrust&action=list&show=verify">通过</a>　<a href="/s.php?module=entrust&action=list&show=inverify">待审核</a>　<a href="/s.php?module=entrust&action=list&show=unverify">未通过</a>　|　<span class="small">布局：</span>'.$layoutLink.'　|　<a href="/s.php?module=entrust&action=add">新建</a>','body'=>$body));
	}
	// 
	function view(){
		$entrust=$this->kclass->DB->queryFirst("
			SELECT o.entrustid,o.entrustno,o.applicant,o.special,o.ifVerify,o.verified,o.verifyRemark,
					o.ifApprove,o.approved,o.approvalRemark,o.remark,o.created,o.killed,o.modified, 
				u.username AS creator,
				us.username AS modifier,
				ur.username AS verifier,
				usr.username AS approver
			FROM entrust AS o
			LEFT JOIN user AS u ON (u.userid=o.creator)
			LEFT JOIN user AS us ON (us.userid=o.modifier)
			LEFT JOIN user AS ur ON (ur.userid=o.verifier)
			LEFT JOIN user AS usr ON (usr.userid=o.approver)
			WHERE o.entrustid='".$this->kclass->input['entrustid']."'
			LIMIT 0,1
		");
		if($entrust){
			$entrust['created']=date('Y-m-d',$entrust['created']);
			$verify=$this->kclass->verify($entrust['ifVerify']);
			if($entrust['modified']!=0)$modify='，由'.$entrust['modifier'].'于'.date('Y-n-d',$entrust['modified']).'修改';
			if($entrust['ifVerify']!=0){
				$pverify='，由'.$entrust['verifier'].'于'.date('Y-m-d',$entrust['verified']).'审核';
				$remark='<tr class="odd"><td>审核备注：</td><td>'.$entrust['verifyRemark'].'</td><td></td><td></td></tr>';
			}
			if($entrust['ifApprove']!=0){
				$averify.='，由'.$entrust['approver'].'于'.date('Y-m-d H:i:s',$entrust['approved']).'审批';
				$remark.='<tr class="even"><td>审批备注：</td><td>'.$entrust['approvalRemark'].'</td><td></td><td></td></tr>';
			}
			if($entrust['ifVerify']==0 AND $entrust['ifApprove']==0){
				$remark='';
			}
			if($entrust['ifApprove']==0){
				if($entrust['ifVerify']==0){
					$operate.='　<a href="/s.php?module=entrust&action=remove&entrustid='.$entrust['entrustid'].'&rt=view">删除</a>　<a href="/s.php?module=entrust&action=verify&entrustid='.$entrust['entrustid'].'" >审核</a>';
				}elseif($entrust['ifVerify']==1){
					$operate.='　<a href="/s.php?module=entrust&action=approve&entrustid='.$entrust['entrustid'].'" >审批</a>';
				}
				$operate.='　|　<a href="/s.php?module=entrust&action=update&entrustid='.$entrust['entrustid'].'">修改</a>';
			}elseif($entrust['ifApprove']==-1){
				$operate.='<a href="/s.php?module=entrust&action=remove&entrustid='.$entrust['entrustid'].'&rt=view">删除</a>';
			}
			//申请加工的物资
			$items=$this->kclass->DB->query("
				SELECT ei.itemid,ei.materialid,ei.versionid,ei.quantity,ei.dateline,ei.requirement,ei.remark,u.title AS unit
				FROM `entrustitem` AS ei
				LEFT JOIN unit AS u ON (ei.unitid=u.unitid)
				WHERE ei.killed=0 AND ei.entrustid='".$entrust['entrustid']."'
				ORDER BY ei.itemid ASC
			");
			if($this->kclass->DB->numRows()){
				$i=1;
				while($item=$this->kclass->DB->fetchArray($items)){
					$item['dateline']=date('Y-m-d',$item['dateline']);
					$materialInfo=$this->kclass->getMaterial(array('materialid'=>$item['materialid']));
					$itemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').'">
						<td>'.$i.'</td>
						<td><a href="'.$materialInfo['url'].'">'.$materialInfo['no'].'</a>　'.$materialInfo['title'].'　'.$materialInfo['standard'].'</td>
						<td>'.$item['quantity'].' '.$item['unit'].'</td>
						<td>'.$item['dateline'].'</td>
						<td>'.$item['requirement'].'</td>
						<td>'.$item['remark'].'</td></tr>';
					$i++;
				}
			}

$body=<<<EOF
<div class="title"><span class="right small gray">由{$entrust['creator']}于{$entrust['created']}建立{$modify}{$pverify}。</span>{$entrust['entrustno']}</div>
<table class="hundred">
<thead><tr><th colspan="4">外发加工申请单</th></tr></thead>
<tbody>
<tr class="odd">
<td width="100">编　　号：</td><td width="475">{$entrust['entrustno']}　（{$verify}）</td>
<td width="100">申 请 人：</td><td>{$entrust['applicant']}</td>
</tr>
<tr class="even">
<td>特别说明：</td><td>{$entrust['special']}</td>
<td>备　　注：</td><td>{$entrust['remark']}</td>
</tr>
{$remark}
</tbody>
</table>
<table class="hundred">
<thead><tr><th colspan="13">物资明细</th></tr></thead>
<tbody>
<tr class="center even">
<td width="15">ID</td><td width="750">物资</td><td width="70">数量/单位</td><td width="70">加工交期</td><td>加工要求</td><td>备注</td></td>
</tr>
{$itemtr}
</tbody>
</table>
EOF;
		}else{
			$body='数据错误，很抱歉！';
		}

		$this->kclass->page['title'].=' - '.$entrust['entrustno'];
		$this->kclass->page['onload'].='dc.tabhover();';
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - 外发加工申请单','right' => '<span class = "small">操作：</span> '.$this->kclass->iif($entrust['killed']==0,'<a href="/s.php?module=entrust&action=add">新建</a>'.$operate,'<a href="/s.php?module=entrust&action=restore&entrustid='.$this->kclass->input['entrustid'].'&rt=view">恢复</a>'),'body'=>$body));
	}
	//
	function add(){
		$start=strtotime(date('Y-m-d',TIMENOW));
		$end=strtotime(date('Y-m-d',$start))+86400;
		$counter=$this->kclass->DB->queryFirst("SELECT COUNT(*) AS count FROM `entrust` WHERE created>='".$start."' AND created<='".$end."'");
		$entrustno=$this->kclass->id(array('entrust'=>TIMENOW,'number'=>$counter['count']));
		$upload=$this->kclass->upload(array('title'=>'相关附件：'));
		for($i=1;$i<6;$i++){
			$itemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').' small">
				<td>'.$this->kclass->chooserMaterial(array('hName'=>'itemMaterialid['.$i.']','hId'=>'itemMaterialid'.$i,'name'=>'itemMaterial['.$i.']','id'=>'itemMaterial'.$i,'width'=>400,'line'=>$i,'showVersion'=>1)).'</td>
				<td><select name="itemVersionid['.$i.']" id="itemVersionid'.$i.'" style="width:200px;"></select></td>
				<td><input type="text" name="itemQuantity['.$i.']" size="10"></td>
				<td>'.$this->kclass->chooserUnit(array('name'=>'itemUnitid['.$i.']','hasBlank'=>1,'topname'=>'请选择','width'=>100)).'</td>
				<td><input type="text" name="itemDateline['.$i.']" id="dateline'.$i.'" style="width:80px;"><span class="gray small"> 时间格式如：2012-02-06</span></td>
				<td><input type="text" name="itemRequirement['.$i.']" style="width:250px;"></td>
				<td><input type="text" name="itemRemark['.$i.']" style="width:150px;"></td>
			</tr>';
			if($i<5){
				$id.='#dateline'.$i.',';
			}else{
				$id.='#dateline'.$i;
			}
		}
$body=<<<EOF
<form action="/s.php?module=entrust&action=insert" name="entrust" method="post">
<input type="hidden" name="module" value="entrust" />
<input type="hidden" name="action" value="insert" />
<input type="hidden" name="entrustno" value="{$entrustno}" />
<table class="hundred">
<thead></tr><th colspan="4">基本信息</th></tr></thead>
<tbody>
<tr class="odd">
<td>外发加工编号：<span class="red bold">*</span></td><td><span class="middle bold darkred">{$entrustno}</span> <span class="gray small">此为预估编号，保存后才可确定</span></td>
<td>申 请 人：<span class="red bold">*</span></td><td><input type="text" name="applicant" value="{$this->kclass->user['realname']}"></td>
</tr>
<tr class="even">
<td>特别说明：</td><td><textarea name="special" style="width:444px;height:111px;"></textarea></td>
<td>备　　注：</td><td><textarea name="remark" style="width:444px;height:111px;"></textarea></td>
</tr>
</tbody>
</table>
<table class="hundred">
<thead></tr><th colspan="10">物资明细</th></tr></thead>
<tbody>
<tr class="center"><td>物资 <span class="red bold">*</span></td><td>版本 <span class="red bold">*</span></td><td>数量 <span class="red bold">*</span></td><td>单位 <span class="red bold">*</span></td><td>加工交期 <span class="red bold">*</span></td><td>加工要求</td><td>备注</td></tr>
{$itemtr}
<tr class="even">
<td class="small gray" colspan="10">
注意事项：<br>
　　①编号咱未确定编号规则，所以默认为OM+时间戳<br>
　　②如果所列表格不够，那么在提交保存后再点击“修改”，来增加订单物资；<br>
　　③需要加工的物资从“物资”中选择；<br>
　　④“加工交期 ”的格式为2012-02-06，中间用半角短横杠隔开；<br>
</td>
</tr>
</tbody>
</table>
{$upload}
<table class="tlist tsuite" align="center" width="100%">
<tbody>
<tr class="odd" nohover>
<td align="center" colspan=4><input type="submit" id="submitButton" value="  提交  " accesskey="s"><input type="reset" value="  复位  "/></td>
</tr>
</tbody>
</table>
</form>
EOF;
		$this->kclass->page['onload']='dc.tabhover();$(\'#date,'.$id.'\').datepicker()';
		$this->kclass->page['title'].=' - 新建外发加工申请单';
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl . ' - 新建外发加工申请单','right'=>'<a href="/s.php?module=entrust">返回列表</a>','body'=>$body));
	}
	//
	function insert(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e='<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			if($this->kclass->input['applicant'] == ''){
				$e='<li>请填写外发加工申请单的 申请人。</li>';
			}
			$j=count($this->kclass->input['itemMaterialid']);
			$hasItem=0;

			for($i=1;$i<$j+1;$i++){
				$this->kclass->input['itemQuantity'][$i]=intval($this->kclass->input['itemQuantity'][$i]);
				if($this->kclass->input['itemMaterialid'][$i]!=0 AND $this->kclass->input['itemQuantity'][$i]>0 AND $this->kclass->input['united'][$i]>=0 AND $this->kclass->input['itemDateline'][$i]!=''){
					$hasItem=1;
				}
			}
			if($hasItem==0){
				$e.='<li>需要填写至少有一条采购单明细，才能建立采购单。</li>';
			}
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title'=>'新建外发加工申请单',
				'text'=>'您在新建外发加工申请单的过程中有以下错误：<ul>'.$e.'</ul>',
				'url'=>'javascript:history.back()',
				'sec'=>3
			));
		}
		$requirementid=$this->kclass->iif($this->kclass->input['requirementid'],$this->kclass->input['requirementid'],0);
		$this->kclass->DB->query("
			INSERT INTO `entrust` ( `entrustno`,`requirementid`,`applicant`,`special`,`remark`,`creator`,`created`)
			VALUES (
			'".$this->kclass->input['entrustno']."','".$requirementid."','".$this->kclass->input['applicant']."','".$this->kclass->input['special']."','".$this->kclass->input['remark']."','".$this->kclass->user['userid']."','".TIMENOW."')
		");
		$entrustid=$this->kclass->DB->insertID();
		$count=count($this->kclass->input['itemMaterialid']);
		for($m=1;$m<$count+1;$m++){
			if($this->kclass->input['itemMaterialid'][$m]!=0 AND $this->kclass->input['itemQuantity'][$m]>0 AND $this->kclass->input['united'][$m]>=0 AND $this->kclass->input['itemDateline'][$m]!=''){
				if($this->kclass->input['itemDateline'][$m]){
					$sd=explode('-',$this->kclass->input['itemDateline'][$m]);
					$dateline=mktime(0,0,0,$sd[1],$sd[2],$sd[0]);
				}else{
					$dateline=0;
				}
				$mrpid=$this->kclass->iif($this->kclass->input['itemId'][$m],$this->kclass->input['itemId'][$m],0);
				$this->kclass->DB->query("
					INSERT INTO `entrustitem` 
						(entrustid,materialid,versionid,mrpid,quantity,unitid,dateline,requirement,remark,created,creator) 
					VALUES
						('".$entrustid."','".$this->kclass->input['itemMaterialid'][$m]."','".$this->kclass->input['itemVersionid'][$m]."','".$mrpid."','".$this->kclass->input['itemQuantity'][$m]."','".$this->kclass->input['itemUnitid'][$m]."','".$dateline."','".$this->kclass->input['itemRequirement'][$m]."','".$this->kclass->input['itemRemark'][$m]."','".TIMENOW."','".$this->kclass->user['userid']."')
				");
				if($this->kclass->input['itemId'][$m]>0){
					$this->kclass->DB->query("UPDATE mrp SET ifChooser=1 WHERE itemid='".$this->kclass->input['itemId'][$m]."'");
				}
			}
		}
		$this->kclass->updateAttachs(array('module'=>'entrust', 'mid'=>$entrustid));
		$this->kclass->messager(array(
			'title'=>'新建外发加工申请单',
			'text'=>'外发加工申请单 <b>'.$this->kclass->input['entrustno'].'</b> 已新建成功!',
			'url'=>'/s.php?module=entrust&action=view&entrustid='.$entrustid,
			'sec'=>2
		));
	}
	//
	function update(){
		if($this->kclass->input['entrustid']<=0 OR !$entrust=$this->kclass->DB->queryFirst("SELECT `entrust`.* FROM `entrust` WHERE killed=0 AND `entrustid`='".$this->kclass->input['entrustid']."'")){
			$this->kclass->boinkIt('/s.php?module=entrust');
		}
		$upload=$this->kclass->upload(array('title'=>'相关附件：','module'=>'entrust','mid'=>$entrust['entrustid']));
		$items=$this->kclass->DB->query("
			SELECT ei.itemid,ei.materialid,ei.versionid,ei.quantity,ei.unitid,ei.dateline,ei.requirement,ei.remark,
				m.materialno,m.title,m.standard
			FROM `entrustitem` AS ei
			LEFT JOIN `material` AS m ON (ei.materialid=m.materialid)
			WHERE ei.killed=0 AND ei.entrustid='".$entrust['entrustid']."' 
			ORDER BY ei.itemid ASC 
		");
		if($this->kclass->DB->numRows()){
			$k=1;
			while($item=$this->kclass->DB->fetchArray($items)){
				$itemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').' small">
					<td><input type="hidden" name="itemId['.$k.']" value="'.$item['itemid'].'">'.$k.'</td>
					<td>'.$this->kclass->chooserMaterial(array('hName'=>'itemMaterialid['.$k.']','hId'=>'itemMaterialid'.$k,'name'=>'itemMaterial['.$k.']','id'=>'itemMaterial'.$k,'width'=>350,'line'=>$k,'selectedid'=>$item['materialid'],'value'=>$item['materialno'].'　'.$item['title'].'　'.$item['standard'],'showVersion'=>1)).'</td>
					<td>'.$this->kclass->chooserMaterialVersion(array('name'=>'itemVersionid['.$k.']','selectedid'=>$item['versionid'],'width' =>200,'id'=>'itemVersionid'.$k,'materialid'=>$item['materialid'])).'</td>
					<td><input type="text" name="itemQuantity['.$k.']" size="10" value="'.$item['quantity'].'"></td>
					<td>'.$this->kclass->chooserUnit(array('name'=>'itemUnitid['.$k.']','hasBlank'=>1,'topname'=>'请选择','width'=>100,'selectedid'=>$item['unitid'])).'</td>
					<td><input type="text" name="itemDateline['.$k.']" id="dateline'.$k.'" style="width:80px;" value="'.date('Y-m-d',$item['dateline']).'"><span class="gray small"> 时间格式如：2012-02-06</span></td>
					<td><input type="text" name="itemRequirement['.$k.']" style="width:250px;" value="'.$item['requirement'].'"></td>
					<td><input type="text" name="itemRemark['.$k.']" style="width:150px;" value="'.$item['remark'].'"></td>
					<td><input type="checkbox" name="itemKill['.$k.']" value="'.$item['itemid'].'"></td></td>
					</tr>';
				$id.='#dateline'.$k.',';
				$k++;
			}
			$itemtr.='<tr><td colspan="9">新建明细</td></tr>';
		}
		for($i=$k;$i<$k+5;$i++){
			$itemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').' small">
				<td>'.$i.'</td>
				<td>'.$this->kclass->chooserMaterial(array('hName'=>'itemMaterialid['.$i.']','hId'=>'itemMaterialid'.$i,'name'=>'itemMaterial['.$i.']','id'=>'itemMaterial'.$i,'width'=>350,'line'=>$i,'showVersion'=>1)).'</td>
				<td><select name="itemVersionid['.$i.']" id="itemVersionid'.$i.'" style="width:200px;"></select></td>
				<td><input type="text" name="itemQuantity['.$i.']" size="10"></td>
				<td>'.$this->kclass->chooserUnit(array('name'=>'itemUnitid['.$i.']','hasBlank'=>1,'topname'=>'请选择','width'=>100)).'</td>
				<td><input type="text" name="itemDateline['.$i.']" id="dateline'.$i.'" style="width:80px;"><span class="gray small"> 时间格式如：2012-02-06</span></td>
				<td><input type="text" name="itemRequirement['.$i.']" style="width:250px;"></td>
				<td><input type="text" name="itemRemark['.$i.']" style="width:150px;"></td>
				<td></td>
			</tr>';
			if($i<$k+4){
				$id.='#dateline'.$i.',';
			}else{
				$id.='#dateline'.$i;
			}
		}
$body=<<<EOF
<form action="/s.php?module=entrust&action=doupdate" name="entrust" method="post">
<input type="hidden" name="module" value="entrust" />
<input type="hidden" name="action" value="doupdate" />
<input type="hidden" name="entrustid" value="{$entrust['entrustid']}" />
<table class="hundred">
<thead></tr><th colspan="4">基本信息</th></tr></thead>
<tbody>
<tr class="odd">
<td>外发加工编号：<span class="red bold">*</span></td><td><span class="middle bold darkred">{$entrust['entrustno']}</span> <span class="gray small">编号不可修改</span></td>
<td>申 请 人：<span class="red bold">*</span></td><td><input type="text" name="applicant" value="{$entrust['applicant']}"></td>
</tr>
<tr class="even">
<td>特别说明：</td><td><textarea name="special" style="width:444px;height:111px;">{$entrust['special']}</textarea></td>
<td>备　　注：</td><td><textarea name="remark" style="width:444px;height:111px;">{$entrust['remark']}</textarea></td>
</tr>
</tbody>
</table>
<table class="hundred">
<thead></tr><th colspan="9">物资明细</th></tr></thead>
<tbody>
<tr class="center"><td width="15">ID</td><td>物资 <span class="red bold">*</span></td><td>版本 <span class="red bold">*</span></td><td>数量 <span class="red bold">*</span></td><td>单位 <span class="red bold">*</span></td><td>加工交期 <span class="red bold">*</span></td><td>加工要求</td><td>备注</td><td>删</td></tr>
{$itemtr}
<tr class="even">
<td class="small gray" colspan="9">
注意事项：<br>
　　①编号咱未确定编号规则，所以默认为OM+时间戳<br>
　　②如果所列表格不够，那么在提交保存后再点击“修改”，来增加订单物资；<br>
　　③需要加工的物资从“物资”中选择；<br>
　　④“加工交期 ”的格式为2012-02-06，中间用半角短横杠隔开；<br>
</td>
</tr>
</tbody>
</table>
{$upload}
<table class="tlist tsuite" align="center" width="100%">
<tbody>
<tr class="odd" nohover>
<td align="center" colspan=4><input type="submit" id="submitButton" value="  提交  " accesskey="s"><input type="reset" value="  复位  "/></td>
</tr>
</tbody>
</table>
</form>
EOF;
		$this->kclass->page['onload']='dc.tabhover();$(\'#date,'.$id.'\').datepicker()';
		$this->kclass->page['title'].=' - 修改外发加工申请单 - '.$entrust['entrustno'];
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl . ' - 修改外发加工申请单 - '.$entrust['entrustno'],'right'=>'<a href="/s.php?module=entrust">返回列表</a>','body'=>$body));
	}
	//
	function doupdate(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e='<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			if($this->kclass->input['applicant'] == ''){
				$e='<li>请填写外发加工申请单的 申请人。</li>';
			}
			$j=count($this->kclass->input['itemMaterialid']);
			$hasItem=0;
			for($i=1;$i<$j+1;$i++){
				$this->kclass->input['itemQuantity'][$i]=intval($this->kclass->input['itemQuantity'][$i]);
				if($this->kclass->input['itemMaterialid'][$i]!=0 AND $this->kclass->input['itemQuantity'][$i]>0 AND $this->kclass->input['itemDateline'][$i]!=''){
					$hasItem=1;
				}
			}
			if($hasItem==0){
				$e.='<li>需要填写至少有一条采购单明细，才能建立采购单。</li>';
			}
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title'=>'修改外发加工申请单',
				'text'=>'您在修改外发加工申请单的过程中有以下错误：<ul>'.$e.'</ul>',
				'url'=>'javascript:history.back()',
				'sec'=>3
			));
		}
		$entrust=$this->kclass->DB->queryFirst("SELECT entrustid,entrustno FROM entrust WHERE entrustid='".$this->kclass->input['entrustid']."'");
		$this->kclass->DB->query("
			UPDATE `entrust` SET 
				`applicant`='".$this->kclass->input['applicant']."',
				`special`='".$this->kclass->input['special']."',
				`remark`='".$this->kclass->input['remark']."',
				`modified`='".TIMENOW."',
				`modifier`='".$this->kclass->user['userid']."'
			WHERE entrustid='".$this->kclass->input['entrustid']."'
		");
		/*$key=array();
		for($i=1;$i<=$j;$i++){//获得输入框行的编号
			if($this->kclass->input['itemMaterialid'][$i]!=0){
				$key[]=$i;
			}
		}
		$count=count($key);*/
		$amount=0;
		for($m=0;$m<$j+1;$m++){
			if($this->kclass->input['itemMaterialid'][$m]!=0 AND $this->kclass->input['itemVersionid'][$m]>0 AND $this->kclass->input['itemQuantity'][$m]>0 AND $this->kclass->input['itemDateline'][$m]!=''){
				$sd=explode('-',$this->kclass->input['itemDateline'][$m]);
				$dateline=mktime(0,0,0,$sd[1],$sd[2],$sd[0]);
				if($this->kclass->input['itemId'][$m]>0){
					if($this->kclass->input['itemKill'][$m] AND $this->kclass->input['itemKill'][$m]==$this->kclass->input['itemId'][$m]){
						$this->kclass->DB->query("UPDATE `entrustitem` SET killed=".TIMENOW.",killer='".$this->kclass->user['userid']."'  WHERE itemid='".$this->kclass->input['itemId'][$m]."'");
					}else{
						$this->kclass->DB->query("
							UPDATE `entrustitem` SET 
								`materialid`='".$this->kclass->input['itemMaterialid'][$m]."',
								`versionid`='".$this->kclass->input['itemVersionid'][$m]."',
								`quantity`='".$this->kclass->input['itemQuantity'][$m]."',
								`unitid`='".$this->kclass->input['itemUnitid'][$m]."',
								`dateline`='".$dateline."',
								`requirement`='".$this->kclass->input['itemRequirement'][$m]."',
								`remark`='".$this->kclass->input['itemRemark'][$m]."',
								`modified`='".TIMENOW."',
								`modifier`='".$this->kclass->user['userid']."'
							WHERE itemid='".$this->kclass->input['itemId'][$m]."'
						");
					}
				}else{
					$this->kclass->DB->query("
						INSERT INTO `entrustitem` 
							(`entrustid`,`materialid`,`versionid`,`quantity`,`unitid`,`dateline`,`requirement`,`remark`,`created`,`creator`) 
						VALUES
							('".$this->kclass->input['entrustid']."','".$this->kclass->input['itemMaterialid'][$m]."','{$this->kclass->input['itemVersionid'][$m]}','".$this->kclass->input['itemQuantity'][$m]."','".$this->kclass->input['itemUnitid'][$m]."','".$dateline."','".$this->kclass->input['itemRequirement'][$m]."','".$this->kclass->input['itemRemark'][$m]."','".TIMENOW."','".$this->kclass->user['userid']."')
					");
				}
			}
		}
		$this->kclass->updateAttachs(array('module'=>'entrust', 'mid'=>$this->kclass->input['entrustid']));
		$this->kclass->messager(array(
			'title'=>'修改外发加工申请单',
			'text'=>'外发加工申请单 <b>'.$entrust['entrustno'].'</b> 的信息已成功修改!',
			'url'=>'/s.php?module=entrust&action=view&entrustid='.$this->kclass->input['entrustid'],
			'sec'=>2
		));
	}
	// 
	function verify(){
		$entrust=$this->kclass->DB->queryFirst("
			SELECT o.entrustid,o.entrustno,o.applicant,o.special,o.ifVerify,o.verified,o.verifyRemark,o.remark,o.created,o.killed,o.modified, 
				u.username AS creator,
				us.username AS modifier,
				ur.username AS verifier
			FROM entrust AS o
			LEFT JOIN user AS u ON (u.userid=o.creator)
			LEFT JOIN user AS us ON (us.userid=o.modifier)
			LEFT JOIN user AS ur ON (ur.userid=o.verifier)
			WHERE o.entrustid='".$this->kclass->input['entrustid']."'
			LIMIT 0,1
		");
		if(!$entrust AND $entrust['ifVerify']!=0){
			$e='<li>数据错误，很抱歉~</li>';
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '审核外发加工申请单',
				'text' => '审核外发加工申请单<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$entrust['created']=date('Y-m-d',$entrust['created']);
		$verify=$this->kclass->verify($entrust['ifVerify']);
		$verifyRemark='';
		if($entrust['modified']!=0)$modify='，由'.$entrust['modifier'].'于'.date('Y-n-d',$entrust['modified']).'修改';
		if($entrust['verified']!=0){
			$pverify='，由'.$entrust['verifier'].'于'.date('Y-n-d',$entrust['verified']).'审核';
			$verifyRemark='<tr class="odd"><td>审核备注：</td><td>'.$entrust['verifyRemark'].'</td><td></td><td></td>';
		}
		if($entrust['ifVerify']==0){
			$verifyOperate='|　<a href="/s.php?module=entrust&action=update&entrustid='.$entrust['entrustid'].'">修改</a>　|　<a href="/s.php?module=entrust&action=verify&entrustid='.$entrust['entrustid'].'">审核</a>';
		}elseif($entrust['ifVerify']==1){
			$verifyOperate='';
		}
		//申请加工的物资
		$items=$this->kclass->DB->query("
			SELECT e.quantity,e.dateline,e.requirement,e.remark,
				m.materialno,m.title,m.standard,
				u.title AS unit 
			FROM `entrustitem` AS e
			LEFT JOIN material AS m ON (m.materialid=e.materialid)
			LEFT JOIN unit AS u ON (u.unitid=e.unitid)
			WHERE e.killed=0 AND e.entrustid='".$entrust['entrustid']."'
			ORDER BY e.itemid ASC
		");
		if($this->kclass->DB->numRows()){
			$i=0;
			while($item=$this->kclass->DB->fetchArray($items)){
				$item['dateline']=date('Y-m-d',$item['dateline']);
				$itemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').' small">
					<td><a href="/s.php?module=material&action=view&materialid='.$item['materialid'].'">'.$item['materialno'].'</a></td>
					<td>'.$item['title'].'</td>
					<td>'.$item['standard'].'</td>
					<td>'.$item['quantity'].' '.$item['unit'].'</td>
					<td>'.$item['dateline'].'</td>
					<td>'.$item['requirement'].'</td>
					<td>'.$item['remark'].'</td></tr>';
				$i++;
			}
		}

$body=<<<EOF
<div class="title"><span class="right small gray">由{$entrust['creator']}于{$entrust['created']}建立{$modify}{$pverify}。</span>{$entrust['entrustno']}</div>
<table class="hundred">
<thead><tr><th colspan="4">外发加工申请单</th></tr></thead>
<tbody>
<tr class="odd">
<td width="100">编　　号：</td><td width="475">{$entrust['entrustno']}　（{$verify}）</td>
<td width="100">申 请 人：</td><td>{$entrust['applicant']}</td>
</tr>
<tr class="even">
<td>特别说明：</td><td>{$entrust['special']}</td>
<td>备　　注：</td><td>{$entrust['remark']}</td>
</tr>
{$verifyRemark}
</tbody>
</table>
<table class="hundred">
<thead><tr><th colspan="13">物资明细</th></tr></thead>
<tbody>
<tr class="center even">
<td>物资编号</td><td>物资名称</td><td>物资规格</td><td>数量/单位</td><td>加工交期</td><td>加工要求</td><td>备注</td></td>
</tr>
{$itemtr}
</tbody>
</table>
<form method="post" action="/s.php?module=entrust&action=doverify">
<input type="hidden" name="module" value="entrust">
<input type="hidden" name="action" value="doverify">
<input type="hidden" name="entrustid" value="{$this->kclass->input['entrustid']}">
<table>
<thead><tr><th colspan="4">审核</th></tr></thead>
<tbody>
<tr class="even">
	<td>审　　核：<span class="bold red">*</span></td><td class="center"><input type="radio" name="ifVerify" value="1" />通过　　　<input type="radio" name="ifVerify" value="-1" checked />不通过</td>
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
		$this->kclass->page['title'].='审核外发加工申请单';
		$this->kclass->page['onload'].='dc.tabhover();';
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - <a href="/s.php?module=entrust&action=view&entrustid='.$this->kclass->input['entrustid'].'">查看详细</a> - 审核外发加工申请单', 'right'=>'<a href="/s.php?module=entrust&action=list">返回列表</a>　|　<a href="/s.php?module=entrust&action=view&entrustid='.$this->kclass->input['entrustid'].'">查看详细</a>','body'=>$body));
	}
	// 
	function doverify(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			$entrust=$this->kclass->DB->queryFirst("SELECT entrustno FROM entrust WHERE entrustid='".$this->kclass->input['entrustid']."'");
			if($this->kclass->input['ifVerify']!=1 AND $this->kclass->input['ifVerify']!=-1){
				$e.='<li>请选择采购单中的 是否通过审核 按钮。</li>';
			}
		}
		if($e){
			$this->kclass->messager(array(
				'title' => '审核外发加工申请单',
				'text' => '您在审核外发加工申请单的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$this->kclass->DB->query("
			UPDATE entrust SET
				ifVerify='".$this->kclass->input['ifVerify']."',
				verifier='".$this->kclass->user['userid']."',
				verified='".TIMENOW."',
				verifyRemark='".$this->kclass->input['verifyRemark']."'
			WHERE entrustid='".$this->kclass->input['entrustid']."'
		");
		$items=$this->kclass->DB->query("SELECT itemid,versionid,quantity FROm entrustitem WHERE killed=0 AND entrustid='".$this->kclass->input['entrustid']."'");
		if($this->kclass->DB->numRows()){
			while($item=$this->kclass->DB->fetchArray($items)){
				$this->kclass->DB->query("UPDATE materialversion SET planOutbound=planOutbound+'".$item['quantity']."' WHERE versionid='".$item['versionid']."'");
			}
		}

		if($this->kclass->input['ifVerify']==1){
			$verify='已通过';
		}elseif($this->kclass->input['ifVerify']==-1){
			$verify='未通过';
		}
		$this->kclass->messager(array(
			'title' => '审核采购单',
			'text' => '采购单 【<b>'.$entrust['entrustno'].'</b>】 '.$verify.'审核!返回查看采购单',
			'url' => '/s.php?module=entrust&action=view&entrustid='.$this->kclass->input['entrustid'],
			'sec' => 2
		));
	}
	//
	function approve(){
		$entrust=$this->kclass->DB->queryFirst("
			SELECT o.entrustid,o.entrustno,o.applicant,o.special,o.ifVerify,o.verified,o.verifyRemark,o.remark,o.created,o.killed,o.modified, 
				u.username AS creator,
				us.username AS modifier,
				ur.username AS verifier
			FROM entrust AS o
			LEFT JOIN user AS u ON (u.userid=o.creator)
			LEFT JOIN user AS us ON (us.userid=o.modifier)
			LEFT JOIN user AS ur ON (ur.userid=o.verifier)
			WHERE o.entrustid='".$this->kclass->input['entrustid']."'
			LIMIT 0,1
		");
		if($entrust){
			$entrust['created']=date('Y-m-d',$entrust['created']);
			$verify=$this->kclass->verify($entrust['ifVerify']);
			if($entrust['modified']!=0)$modify='，由'.$entrust['modifier'].'于'.date('Y-n-d',$entrust['modified']).'修改';
			if($entrust['verified']!=0){
				$pverify='，由'.$entrust['verifier'].'于'.date('Y-n-d',$entrust['verified']).'审核';
				$verifyRemark='<tr class="odd"><td>审核备注：</td><td>'.$entrust['verifyRemark'].'</td><td></td><td></td></tr>';
			}
			if($entrust['ifVerify']==0){
				$verifyOperate='|　<a href="/s.php?module=entrust&action=update&entrustid='.$entrust['entrustid'].'">修改</a>　|　<a href="/s.php?module=entrust&action=verify&entrustid='.$entrust['entrustid'].'">审核</a>';
			}elseif($entrust['ifVerify']==1){
				$verifyOperate='';
			}
			//申请加工的物资
			$items=$this->kclass->DB->query("
				SELECT e.quantity,e.dateline,e.requirement,e.remark,
					m.materialno,m.title,m.standard,
					u.title AS unit 
				FROM `entrustitem` AS e
				LEFT JOIN material AS m ON (m.materialid=e.materialid)
				LEFT JOIN unit AS u ON (u.unitid=e.unitid)
				WHERE e.killed=0 AND e.entrustid='".$entrust['entrustid']."'
				ORDER BY e.itemid ASC
			");
			if($this->kclass->DB->numRows()){
				$i=0;
				while($item=$this->kclass->DB->fetchArray($items)){
					$item['dateline']=date('Y-m-d',$item['dateline']);
					$itemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').' small">
						<td><a href="/s.php?module=material&action=view&materialid='.$item['materialid'].'">'.$item['materialno'].'</a></td>
						<td>'.$item['title'].'</td>
						<td>'.$item['standard'].'</td>
						<td>'.$item['quantity'].' '.$item['unit'].'</td>
						<td>'.$item['dateline'].'</td>
						<td>'.$item['requirement'].'</td>
						<td>'.$item['remark'].'</td></tr>';
					$i++;
				}
			}

$body=<<<EOF
<table class="hundred">
<thead><tr><th colspan="4">外发加工申请单</th></tr></thead>
<tbody>
<tr class="odd">
<td width="100">编　　号：</td><td width="475">{$entrust['entrustno']}　（{$verify}）</td>
<td width="100">申 请 人：</td><td>{$entrust['applicant']}</td>
</tr>
<tr class="even">
<td>特别说明：</td><td>{$entrust['special']}</td>
<td>备　　注：</td><td>{$entrust['remark']}</td>
</tr>
{$verifyRemark}
</tbody>
</table>
<table class="hundred">
<thead><tr><th colspan="13">物资明细</th></tr></thead>
<tbody>
<tr class="center even">
<td>物资编号</td><td>物资名称</td><td>物资规格</td><td>数量/单位</td><td>加工交期</td><td>加工要求</td><td>备注</td></td>
</tr>
{$itemtr}
</tbody>
</table>
<form method="post" action="/s.php?module=entrust&action=doapprove">
<input type="hidden" name="module" value="entrust">
<input type="hidden" name="action" value="doapprove">
<input type="hidden" name="entrustid" value="{$this->kclass->input['entrustid']}">
<table>
<thead><tr><th colspan="4">审批</th></tr></thead>
<tbody>
<tr class="even">
<td>审　　批：<span class="bold red">*</span></td><td class="center"><input type="radio" name="ifApprove" value="1" />通过　　　<input type="radio" name="ifApprove" value="-1" checked />不通过</td>
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
		}
		$this->kclass->tbline+=4;
		$this->kclass->page['title'].='审核采购计划单';
		$this->kclass->page['onload'].='dc.tabhover();';
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - <a href="/s.php?module=entrust&action=view&entrustid='.$this->kclass->input['entrustid'].'">查看外发加工申请单</a> - 审核外发加工申请单', 'right'=>'<a href="/s.php?module=entrust&action=list">返回列表</a>　|　<a href="/s.php?module=entrust&action=view&entrustid='.$this->kclass->input['entrustid'].'">查看外发加工申请单</a>','body'=>$body));
	}
	//
	function doapprove(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			$entrust=$this->kclass->DB->queryFirst("SELECT * FROM entrust WHERE killed=0 AND entrustid='".$this->kclass->input['entrustid']."' LIMIT 0,1 ");
			if($entrust['ifApprove']!=0){
				$e.='<li>外发加工申请单 <b>'.$entrust['entrustno'].'</b> 已经被审批过。</li>';
			}
			if($this->kclass->input['ifApprove']!=1 AND $this->kclass->input['ifApprove']!=-1){
				$e.='<li>请选择外发加工申请单中的 是否通过审批 按钮。</li>';
			}
		}
		if($e){
			$this->kclass->messager(array(
				'title' => '审批外发加工申请单',
				'text' => '您在审批外发加工申请单的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}

		$this->kclass->DB->query("
			UPDATE entrust SET
				ifApprove='".$this->kclass->input['ifApprove']."',
				approver='".$this->kclass->user['userid']."',
				approved='".TIMENOW."',
				approvalRemark='".$this->kclass->input['approvalRemark']."'
			WHERE entrustid='".$this->kclass->input['entrustid']."'
		");

		if($this->kclass->input['ifApprove']==1){
			$approve='已通过';
		}elseif($this->kclass->input['ifApprove']==-1){
			$approve='未通过';
			$mrps=$this->kclass->DB->query("SELECT mrp.mrpid FROM mrp LEFT JOIN material ON (material.materialid=mrp.materialid) WHERE killed=0 AND requirementid='".$entrust['requirementid']."' AND material.originid=3");
			if($this->kclass->DB->numRows()){
				while($mrp=$this->kclass->DB->fetchArray($mrps)){
					$this->kclass->DB->query("UPDATE mrp SET ifChooser=0 WHERE killed=0 AND mrpid='".$mrp['mrpid']."'");
				}
			}
		}
		$this->kclass->messager(array(
			'title' => '审批外发加工申请单',
			'text' => '外发加工申请单 【<b>'.$entrust['entrustno'].'</b>】 '.$approve.'审批!返回查看外发加工申请单',
			'url' => '/s.php?module=entrust&action=view&entrustid='.$this->kclass->input['entrustid'],
			'sec' => 2
		));
	}
//
	function kill(){
		if($this->kclass->input['entrustid']<=0){
			$this->kclass->boinkIt('/s.php?module=entrust');
		}
		if($this->kclass->input['rt']!='list' AND $this->kclass->input['confirm'] == 0){
			$this->kclass->boinkIt('/s.php?module=entrust&action=view&entrustid='.$this->kclass->input['entrustid']);
		}
		if($this->kclass->input['entrustid'] < 0){
			$e = '<li>请从正确的页面进行操作，如果此问题持续存在，请联系网站管理员。</li>';
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '删除采购单',
				'text' => '您在删除采购单的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		if($this->kclass->input['rt']=='view'){
			$rt = '&action=view&entrustid='.$this->kclass->input['entrustid'];
		}elseif($this->kclass->input['rt']=='list'){
			$rt = '#'.$this->kclass->input['entrustid'];
		}
		$entrust=$this->kclass->DB->queryFirst("
			SELECT entrustno,requirementid
			FROM entrust
			WHERE entrustid='".$this->kclass->input['entrustid']."'
		");
		if($entrust){
			$this->kclass->DB->query("
				UPDATE `entrust`
				SET killed=".TIMENOW.",killer='".$this->kclass->user['userid']."'
				WHERE entrustid='".$this->kclass->input['entrustid']."'
			");
			$this->kclass->messager(array(
				'title' => '删除采购单成功',
				'text' => '采购单 <b>'.$entrust['entrustno'].'</b> 已成功被标记为删除!',
				'url' => '/s.php?module=entrust'.$rt,
				'sec' => 2
			));
		}else{
			$this->kclass->messager(array(
				'title' => '删除采购单失败',
				'text' => '您要删除的采购单，不存在！',
				'url' => '/s.php?module=entrust'.$rt,
				'sec' => 3
			));
		}
	}

	//
	function remove(){
		if($this->kclass->input['entrustid']<=0){
			$this->kclass->boinkIt('/s.php?module=entrust');
		}
		$entrust = $this->kclass->DB->queryFirst("
			SELECT entrustno
			FROM `entrust`
			WHERE entrustid='".$this->kclass->input['entrustid']."'
		");
$body = <<<EOF
<form action="/s.php?module=entrust&action=kill" name="entrust" method="post">
<input type="hidden" name="module" value="entrust">
<input type="hidden" name="action" value="kill">
<input type="hidden" name="entrustid" value="{$this->kclass->input['entrustid']}">
<input type="hidden" name="rt" value="{$this->kclass->input['rt']}">
<table><thead>
<thead>
<tr>
<th>删除采购单：{$entrust['entrustno']}</th>
</tr>
</thead>
<tbody>
<tr class="even">
<td class="middle">你确定要删除采购单: <a href="/s.php?module=entrust&action=view&entrustid={$this->kclass->input['entrustid']}" class="big bold" target="_blank">{$entrust['entrustno']}</a> 吗?</td>
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
		$this->kclass->page['title'] .= ' - 删除 - '.$entrust['entrustno'];
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 删除采购单 - '.$entrust['entrustno'], 'right' => '<a href="/s.php?module=entrust">返回列表</a>', 'body'=>$body));
	}

	//
	function revival(){
		if($this->kclass->input['entrustid']<=0){
			$this->kclass->boinkIt('/s.php?module=entrust');
		}
		if($this->kclass->input['rt']!='list' AND $this->kclass->input['confirm']==0){
			$this->kclass->boinkIt('/s.php?module=entrust&action=view&entrustid='.$this->kclass->input['entrustid']);
		}
		if($this->kclass->input['entrustid']<0){
			$e = '<li>请从正确的页面进行操作，如果此问题持续存在，请联系网站管理员。</li>';
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '恢复采购单',
				'text' => '您在恢复采购单的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$entrust = $this->kclass->DB->queryFirst("
			SELECT entrustno
			FROM `entrust`
			WHERE entrustid='".$this->kclass->input['entrustid']."'
		");
		if($this->kclass->input['rt']=='view'){
			$rt = '&action=view&entrustid='.$this->kclass->input['entrustid'];
		}elseif($this->kclass->input['rt']=='list'){
			$rt = '#'.$this->kclass->input['entrustid'];
		}
		if($entrust){
			$this->kclass->DB->query("
				UPDATE `entrust`
				SET killed=0,killer=0
				WHERE entrustid='".$this->kclass->input['entrustid']."'
			");
			$this->kclass->messager(array(
				'title' => '恢复采购单成功',
				'text' => '采购单 <b>'.$entrust['entrustno'].'</b> 已成功被恢复！',
				'url' => '/s.php?module=entrust'.$rt,
				'sec' => 2
			));
		}else{
			$this->kclass->messager(array(
				'title' => '恢复采购单',
				'text' => '您要恢复的采购单不存在！',
				'url' => '/s.php?module=entrust'.$rt,
				'sec' => 3
			));
		}
	}

	//
	function restore(){
		if($this->kclass->input['entrustid']<=0){
			$this->kclass->boinkIt('/s.php?module=entrust');
		}
		$entrust = $this->kclass->DB->queryFirst("
			SELECT entrustno
			FROM `entrust`
			WHERE entrustid='".$this->kclass->input['entrustid']."'
		");
$body = <<<EOF
<form action="/s.php?module=entrust&action=revival" name="entrust" method="post">
<input type="hidden" name="module" value="entrust">
<input type="hidden" name="action" value="revival">
<input type="hidden" name="entrustid" value="{$this->kclass->input['entrustid']}">
<input type="hidden" name="rt" value="{$this->kclass->input['rt']}">
<table><thead>
<thead>
<tr>
<th>恢复采购单：{$entrust['entrustno']}</th>
</tr>
</thead>
<tbody>
<tr class="even">
<td class="middle">你确定要恢复采购单: <a href="/s.php?module=entrust&action=view&entrustid={$this->kclass->input['entrustid']}" class="big bold" target="_blank">{$entrust['entrustno']}</a> 吗?</td>
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
		$this->kclass->page['title'] .= ' - 恢复 - '.$entrust['entrustno'];
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 恢复采购单 - '.$entrust['entrustno'], 'right' => '<a href="/s.php?module=entrust">返回列表</a>', 'body'=>$body));
	}
	
	//外发加工物资关联
	function materiallink(){
	
$body=<<<EOF
<form action="/s.php?module=entrust&action=domateriallink" name="entrust" method="post">
<table>
<thead>
<tr>
<th colspan="2">添加外发加工物资关联：</th>
</tr>
</thead>
<tbody>
<tr>
<td>加工前物资：</td><td>{$this->kclass->chooserMaterial(array('hName'=>'itemMaterialid','hId'=>'itemMaterialid','name'=>'itemMaterial','id'=>'itemMaterial','width'=>400,'showVersion'=>1))} </td>
</tr>
<tr>
<td>加工后物资：</td><td>{$this->kclass->chooserMaterial(array('hName'=>'itemMaterialid2','hId'=>'itemMaterialid','name'=>'itemMaterial','id'=>'itemMaterial2','width'=>400,'showVersion'=>1))}</td>
</tr>
<td colspan="2" align="center">
<input type="submit" name="submit" value="提交" />&nbsp;&nbsp;&nbsp;&nbsp;<input type="reset" name="reset" value="重置" />
</td>
</tbody>
</table>
</form>
EOF;
	
		$this->kclass->page['onload']='dc.tabhover();$(\'#date,'.$id.'\').datepicker()';	
		$this->kclass->page['title'] .= ' - 物资关联 - ';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 外发加工物资关联 - ', 'right' => '<a href="/s.php?module=entrust">返回列表</a>', 'body'=>$body));
	}
	
	//执行外发加工物资关联
	function domateriallink(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e='<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			if($this->kclass->input['itemMaterialid']=="0"){
				$e='加工前物资不可为空！';
			}
			if($this->kclass->input['itemMaterialid2']=="0"){
				$e='加工后物资不可为空！';			
			}
		}	
		if($e){
			$this->kclass->messager(array(
				'title' => '添加外发加工物资关联',
				'text' => '您在添加外发加工物资关联的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => '/s.php?module=entrust&action=materiallink',
				'sec' => 2
			));
		}	
		$sql = $this->kclass->DB->query("UPDATE material SET entrustMaterialid='{$this->kclass->input['itemMaterialid2']}' WHERE materialid='{$this->kclass->input['itemMaterialid']}'"); 
		if($sql==true){
			$this->kclass->messager(array(
				'title' => '添加外发加工物资关联',
				'text' => '添加外发加工物资关联成功！',
				'url' => '/s.php?module=entrust&action=add',
				'sec' => 2
			));		
		}else{
			$this->kclass->messager(array(
				'title' => '添加外发加工物资关联',
				'text' => '添加外发加工物资关联失败！',
				'url' => '/s.php?module=entrust&action=materiallink',
				'sec' => 2
			));		
		
		
		}	
	}
}
?>