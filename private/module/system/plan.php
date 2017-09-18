<?php
	//
	class plan{
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
				case 'verify':
					return $this->verify();
					break;
				case 'doverify':
					return $this->doverify();
					break;
				default:
					return $this->mmlist();

			}
		}
		// main page,shows all the plan info list
		function mmlist(){
			if($this->kclass->input['show']=='all'){
				$condition='1=1';
			}elseif($this->kclass->input['show']=='unlock'){
				$condition='`plan`.statusid=1';
			}elseif($this->kclass->input['show']=='locked'){
				$condition='`plan`.statusid=2';
			}elseif($this->kclass->input['show']=='inProduction'){
				$condition='`plan`.statusid=3';
			}elseif($this->kclass->input['show']=='Done'){
				$condition='`plan`.statusid=4';
			}else{
				$condition='`plan`.killed=0';
			}
			if($this->kclass->input['orderby']!='')$query['orderby']=$this->kclass->input['orderby'];
			if($this->kclass->input['direction']!='')$query['direction']=$this->kclass->input['direction'];
			if($this->kclass->input['show']!='')$query['show']=$this->kclass->input['show'];
			if($this->kclass->input['layout']!='')$query['layout']=$this->kclass->input['layout'];
			$orderby=$this->kclass->orderby(array('module'=>'plan','direction'=>'asc','orderby'=>'modified', 'default'=>'planno', 'serial'=>array( array('title'=>'编号', 'field'=>'planno','word'=>'no'), array('title'=>'修改时间', 'field'=>'modified'), array('title'=>'建立时间', 'field'=>'created')),'appendUrl'=>$query));
			if(is_array($query) AND count($query)>0)$queryPart='&'.http_build_query($query);
			$plans=$this->kclass->DB->query("
				SELECT `plan`.*,
					s.title AS status,
					u.username AS creator,
					us.username AS modifier
				FROM `plan` 
				LEFT JOIN mftstatus AS s ON (s.statusid=`plan`.statusid)
				LEFT JOIN user AS u ON (u.userid=`plan`.creator)
				LEFT JOIN user AS us ON (us.userid=`plan`.modifier)
				WHERE ".$condition."
				ORDER BY ".$orderby['sql']."
			");
			if($this->kclass->DB->numRows()){
				if($this->kclass->input['layout']=='grid'){
					$body='<ul id="mmlist" class="mmlist clear">';
				}else{
					$body='<table class="hundred"><thead><th width="20">ID</th><th width="100">编号</th><th width="50">计划员</th><th width="70">计划时间</th><th width="70">备注</th><th width="70">状态</th><th width="80">选项</th></thead><tbody>';
				}
				$i=1;
				while($plan=$this->kclass->DB->fetchArray($plans)){
				
					$plan['created']=date('Y-m-d',$plan['created']);
					if($plan['killed']>0){
						$link='<a href="/s.php?module=plan&action=restore&planid='.$plan['planid'].'&rt=list">恢复</a>';
					}else{
						$link=$this->kclass->iif($plan['statusid']==1,'<a href="/s.php?module=plan&action=update&planid='.$plan['planid'].'&rt=list">改</a>','').' <a href="/p.php?action=plan&planid='.$plan['planid'].'" target="_blank">印</a>';
					}
					if($this->kclass->input['layout']=='grid'){//表格
						$body.='<li title="由 '.$plan['creator'].' 建于 '.$created.$this->kclass->iif($plan['modifier']!='', '，'.$plan['modifier'].' 改于 '.date('Y-m-d H:i',$plan['modified']), '').'"'.$this->kclass->iif($i%4==0, ' class="end"', '').'>
						<div class="mmlistt">
							<span class="right normal">';
						if($plan['killed']>0){
							$body.='<a href="/s.php?module=plan&action=revival&planid='.$plan['planid'].'&rt=list" onclick="return confirm(\'你确定要恢复这个生产计划单 '.$plan['planno'].' 吗？\');">恢复</a>';
						}else{
							$body.='<a href="/s.php?module=plan&action=kill&planid='.$plan['planid'].'&rt=list" onclick="return confirm(\'你确定要删除这个生产计划单 '.$plan['planno'].' 吗？\');">删</a> '.$this->kclass->iif($plan['ifVerify']==0,'<a href="/s.php?module=plan&action=update&planid='.$plan['planid'].'&rt=list">改</a>','');
						}
						$body.='</span>
							 <a href="/s.php?module=plan&action=view&planid='.$plan['planid'].'">'.$plan['planno'].'</a><span class="small">('.$plan['status'].')</span><br><span class="small">'.$plan['planid'].'</span><span class="small gray right">'.$plan['created'].'</span></div>
						<div class="mmlistb">
							<div><span class="small gray">计划员：'.$plan['planner'].'</span></div>
							<div><span class="small gray">备注：'.$plan['remark'].'</span></div>
							<div class="small clear"><span class=right title="由 '.$prchase['creator'].' 建于 '.$plan['created'].'">建于 '.$plan['created'].'</span></div></div></li>';
					}else{
						$body.='<tr class="'.$this->kclass->rotateLine().'">
							<td>'.$i.'</td>
							<td><a href="/s.php?module=plan&action=view&planid='.$plan['planid'].'">'.$plan['planno'].'</td>
							<td title="'.$plan['planner'].'">'. $plan['planner'].'</td>
							<td>'.$plan['created'].'</td>
							<td>'.$plan['remark'].'</td>
							<td>'.$plan['status'].'</td>
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
				$layoutLink='<a href="/s.php?module=plan&action=list&layout=list'.str_replace('&layout=grid', '', $queryPart).'">列表</a> 格子';
			}else{
				$layoutLink='列表 <a href="/s.php?module=plan&action=list&layout=grid'.str_replace('&layout=list', '', $queryPart).'">格子</a>';
			}
				$showLink='<a href="/s.php?module=plan&action=list">默认</a> <a href="/s.php?module=plan&action=list&show=all">所有<span class="small gray">(包括删除)</span></a> <a href="/s.php?module=plan&action=list&show=unlock">未锁定</a>  <a href="/s.php?module=plan&action=list&show=locked">已锁定</a>  <a href="/s.php?module=plan&action=list&show=inProduction">生产中</a>  <a href="/s.php?module=plan&action=list&show=Done">生产完成</a>';
			$this->kclass->page['title'].='生产计划单列表';
			$this->kclass->page['onload'].='dc.listhover();';
			$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - 生产计划单列表','right' => '<span class="small">排序：</span>'.$orderby['link'].'|　<span class="small">显示方式：</span>'.$showLink.'　|　<span class="small">布局：</span>'.$layoutLink.'　|　<a href="/s.php?module=plan&action=add">新建</a>','body'=>$body));
		}
		//
		function find(){
			if($this->kclass->input['requestMethod']!='post'){
				$body=<<<EOF
<form action="/s.php?module=plan&action=find" method="post">
<input type="hidden" name="module" value="plan">
<input type="hidden" name="action" value="find">
<table style="width:450px">
<thead><tr><th colspan=2>根据条件查找生产计划单</th></tr></thead>
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
	<td width="100">计划单编号：</td><td><input type="text" name="planno"></td>
</tr>
<tr class="even">
<td class="small gray" colspan="9">
注意事项：<br>
	①查找生产计划单时，可以通过输入“计划单编号”直接查找，也可以通过输入“日期”和“物料编号”进行查找<br>
	②请保证输入的值无误</td>
</tr>
<tr class="odd" nohover>
<td class="center" colspan=2><input type="submit" id="submitButton" value="  提交  "><input type="reset" value="  复位  "/></td>
</tr>
</tbody>
</table>
</form>
EOF;
				$this->kclass->page['title'].='查找生产计划单';
				$this->kclass->page['onload'].='dc.listhover();$(\'#dateline\').datepicker()';
				$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - 查找生产计划单','right' => '<a href="/s.php?module=plan">查看列表</a>','body'=>$body));
			}else{
				if($this->kclass->input['planno']!=''){
					$planno=trim($this->kclass->input['planno']);
					$plan=$this->kclass->DB->queryFirst("SELECT planid FROM plan WHERE planno='".$planno."' LIMIT 0,1");
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
					$plan=$this->kclass->DB->queryFirst("
						SELECT DISTINCT p.planid 
						FROM planitem AS p
						LEFT JOIN material AS m ON (m.materialid=p.materialid) 
						LEFT JOIN preinbounditem AS pi ON (pi.itemid=p.applyItemid)
						WHERE p.killed=0 ".$condition." 
						ORDER BY p.created DESC 
						LIMIT 0,1
					");
				}
				$this->kclass->boinkIt('/s.php?module=plan&action=view&planid='.$plan['planid']);
			}
		}
		//
		function view(){
			$plan=$this->kclass->DB->queryFirst("
				SELECT p.*,
					s.title AS status,
					u.username AS creator,
					us.username AS modifier
				FROM plan AS p
				LEFT JOIN mftstatus AS s ON (s.statusid=p.statusid)
				LEFT JOIN user AS u ON (u.userid=p.creator)
				LEFT JOIN user AS us ON (us.userid=p.modifier)
				WHERE p.planid='".$this->kclass->input['planid']."'
				LIMIT 0,1
			");
			if(!$plan){
				$e='<li>数据错误，很抱歉</li>';
			}
			if($e){
				$this->kclass->messager(array(
					'title' => '查看生产计划单',
					'text' => '您在查看生产计划单的过程中有以下错误：<ul>'.$e.'</ul>',
					'url' => 'javascript:history.back()',
					'sec' => 3
				));
			}
			$plan['created']=date('Y-m-d',$plan['created']);
			if($plan['modified']!=0)$modify='，由'.$plan['modifier'].'于'.date('Y-m-d',$plan['modified']).'修改';
			$updateItem='';
			$statusRemark='<td></td><td></td>';	
			if($plan['statusid']==2){
				$pstatus='，由'.$plan['planner'].'于'.date('Y-m-d',$plan['modified']).'锁定';
				$statusRemark='<td>审核备注：</td><td>'.$plan['status'].'</td>';
			}
			if($plan['statusid']==1){
				$changeStatus='|　<a href="/s.php?module=plan&action=update&planid='.$plan['planid'].'">修改</a>　|　<a href="/s.php?module=plan&action=verify&planid='.$plan['planid'].'">锁定</a>';
				$updateItem='<span class="right"><a href="/s.php?module=plan&action=update&planid='.$plan['planid'].'&step=item">修改生产计划明细</a></span>';
			}elseif($plan['statusid']!=1){
				$changeStatus='';
			}
			//计划的物资
			$items=$this->kclass->DB->query("
				SELECT pi.*,
					mft.manufactureid,mft.manufactureno,mft.typeid,
					t.title AS type,
					m.materialno,m.title,m.standard,
					pm.pmaterialid,
					mv.title AS version
				FROM planitem AS pi
				LEFT JOIN manufactureitem AS mfti ON (mfti.itemid=pi.mftitemid)
				LEFT JOIN manufacture AS mft ON (mft.manufactureid=mfti.manufactureid)
				LEFT JOIN mfttype AS t ON (t.typeid=mft.typeid)
				LEFT JOIN material AS m ON (m.materialid=pi.materialid)
				LEFT JOIN `pmaterial` AS pm ON (pm.materialid=pi.materialid)
				LEFT JOIN `materialversion` AS mv ON (mv.versionid=pi.versionid)
				WHERE pi.killed=0 AND pi.planid='".$plan['planid']."'
				ORDER BY pi.ordering ASC
			");
			if($this->kclass->DB->numRows()){
				$i=1;
				$id=array();
				while($item=$this->kclass->DB->fetchArray($items)){
					$item['workdate']=date('Y-m-d',$item['workdate']);
					$item['finishdate']=date('Y-m-d',$item['finishdate']);
					$class=$this->kclass->iif($i%2==0,'class="even"','class="odd"');
					$manufactureno='<a href="/s.php?module=manufacture&action=view&manufactureid='.$item['manufactureid'].'">'.$item['manufactureno'].'</a>';
					$title=$item['title'];
					$standard=$item['standard'];
					$am=$this->kclass->getMaterialUrl(array('pmaterialid'=>$item['pmaterialid'],'productitemid'=>$item['productitemid'],'materialno'=>$item['materialno']));
					$itemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').' small">
						<td>'.$i.'</td>
						<td>'.$item['type'].'</td>
						<td>'.$manufactureno.'</td>
						<td>'.$am['murl'].'</td>
						<td>'.$title.'</td>
						<td>'.$standard.''.$am['mvalue'].'</td>
						<td>'.$item['version'].'</td>
						<td>'.$item['quantity'].'</td>
						<td>'.$item['workdate'].'</td>
						<td>'.$item['finishdate'].'</td>
						<td>'.$item['workhours'].'</td>
						<td>'.$item['remark'].'</td></tr>';
					$i++;
				}
			}
		//mrp
			$arr=$this->kclass->mrpList($this->kclass->input['planid']);
			$j=1;
			if($arr){
				foreach($arr as $key => $val){
					$wc=$this->kclass->DB->queryFirst("
						SELECT wc.title AS workcenter,wc.burthen
						FROM workcenteritem 
						LEFT JOIN workcenter AS wc ON (wc.workcenterid=workcenteritem.workcenterid)
						WHERE materialid='".$val['materialid']."' AND versionid='".$val['versionid']."' AND productitemid='".$val['productitemid']."' 
					");
					if($val['netQuantity']<0){
						$mrpitem.='';
					}else{
						$version=$this->kclass->DB->queryFirst("SELECT title FROM materialversion WHERE versionid='".$val['versionid']."'");
						$mrpitem.='<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').'">
						<td>'.$j.'</td>
						<td>'.$val['materialno'].'</td>
						<td>'.$val['title'].'</td>
						<td>'.$val['standard'].'</td>
						<td>'.$version['title'].'</td>
						<td>'.$val['netQuantity'].'</td>
						<td>'.$this->kclass->iif($wc['workcenter']=='','未关联',$wc['workcenter']).'</td>
						<td>'.$this->kclass->iif($wc['burthen']=='','未关联',$wc['burthen']).'</td>
						<td></td>
						<td></td>
						<td></td>
						</tr>';
						$j++;
					}
				}
			}else{
				$mrpitem.='';
			}
		
$body=<<<EOF
<div class="title"><span class="right small gray">由{$plan['creator']}于{$plan['created']}建立{$modify}{$pstatus}。</span>{$plan['planno']}</div>
<dl id="plan" class="tabs" style="display: block;">
	<dt tabid="0">基本信息</dt>
	<dt title="与此物资生产计划相关的生产制造"{$planApply['off']}>生产制造单{$planApply['count']}</dt>
	<dt title="与此物资生产计划相关的外发加工申请"{$entrust['off']}>生产任务单{$entrust['count']}</dt>
<dd>
<table class="hundred">
<thead><tr><th colspan="4">生产计划单</th></tr></thead>
<tbody>
<tr class="odd">
<td width="100">编号：</td><td width="475">{$plan['planno']}　（{$plan['status']}）</td>
<td width="100">计划员：</td><td>{$plan['planner']}</td>
</tr>
<tr class="even">
<td>计划时间：</td><td>{$plan['created']}</td>
<td>备注：</td><td>{$plan['remark']}</td>
</tbody>
</table>
<table class="hundred">
<thead><tr><th colspan="15">生产计划明细{$updateItem}</th></tr></thead>
<tbody>
<tr class="center even">
<td width="15">ID</td><td width="70">制造单类型</td><td width="70">制造单编号</td><td>物资编号</td><td>物资名称</td><td>物资规格</td><td>版本</td><td>数量</td><td>投产日期</td><td>完工日期</td><td>工时</td><td>备注</td></td>
</tr>
{$itemtr}
</tbody>
</table>
<table class="hundred">
<thead><tr><th colspan="12">工作中心任务明细</th></tr></thead>
<tbody>
<tr class="even center"><td width="15">ID</td><td width="60">物资编号</td><td>物资名称</td><td>物资规格</td><td>版本</td><td width="100">净需求数量 <span class="red bold">*</span></td><td width="100">工作中心</td><td width="50">负荷</td><td width="90">投产日期</td><td width="90">完工日期</td><td width="90">工作时数</td></tr>
</tbody><tbody class="small">
{$mrpitem}
<tr class="even">
<td class="small gray" colspan="12">
注意事项：<br>
①如果所列表格不够，那么在提交保存后再点击“修改”，来添加所要生产的物资；<br>
②如果不从“生产任务单中”选择物资，而是生产新物资，那么就填写“物资编号”、“物资名称”、“物资规格”，或者在物资列表中选择；<br>
③“生产任务单及其物资”中 灰色的物资表示已经被选中过，不可以继续选择；<br>
④时间的格式为2012-02-06，中间用半角短横杠隔开；<br>
⑤从库中物资选择时，“单位”不需要选，但是如果生产物资不存在库中时，就必须要选择“单位”
</td>
</tr>
</tbody>
</table>
</dd>
<dd>{$planApply['panel']}</dd>
<dd>{$entrust['panel']}</dd>
EOF;
			$this->kclass->page['title'].='生产计划单';
			$this->kclass->page['onload'].='dc.tabhover();dc.tabs({\'id\':\'plan\'})';
			$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - <a href="/s.php?module=plan&action=list">生产计划单列表</a> - 生产计划单','right' => '<span class = "small">操作：</span> '.$this->kclass->iif($plan['killed']==0,'<a href="/s.php?module=plan&action=add">新建</a>　<a href="/s.php?module=plan&action=remove&planid='.$plan['planid'].'&rt=view">删除</a>　'.$changeStatus,'<a href="/s.php?module=plan&action=restore&planid='.$this->kclass->input['planid'].'&rt=view">恢复</a>'),'body'=>$body));
		}
		// the form for add a plan
		function add(){
			if($this->kclass->input['step']==''){
				$start=strtotime(date('Y-m-d',TIMENOW));
				$end=strtotime(date('Y-m-d',$start))+86400;
				$counter=$this->kclass->DB->queryFirst("SELECT COUNT(*) AS count FROM plan WHERE created>='".$start."' AND created<='".$end."'");
				$planno=$this->kclass->id(array('purchase'=>TIMENOW,'number'=>$counter['count']));
				$manufactures=$this->kclass->DB->query("
					SELECT `manufacture`.*,
					t.title AS type,
					u.username AS creator
					FROM `manufacture` 
					LEFT JOIN mfttype AS t ON (t.typeid=`manufacture`.typeid)
					LEFT JOIN user AS u ON (u.userid=`manufacture`.creator)
					WHERE `manufacture`.killed=0 AND `manufacture`.statusid=1
				");
				if($this->kclass->DB->numRows()){
					$i=1;
					while($manufacture=$this->kclass->DB->fetchArray($manufactures)){
						//申请制造的物资
						$items=$this->kclass->DB->query("
							SELECT mfti.*,
								m.materialno,m.title,m.standard,
								pm.pmaterialid,
								mv.title AS version,
								s.title AS status
							FROM `manufactureitem` AS mfti
							LEFT JOIN material AS m ON (m.materialid=mfti.materialid)
							LEFT JOIN `pmaterial` AS pm ON (pm.materialid=mfti.materialid)
							LEFT JOIN `materialversion` AS mv ON (mv.versionid=mfti.versionid)
							LEFT JOIN mftstatus AS s ON (s.statusid=mfti.statusid)
							WHERE mfti.statusid=1 AND mfti.killed=0 AND mfti.ifChooser=0 AND mfti.manufactureid='".$manufacture['manufactureid']."' 
							ORDER BY mfti.ordering ASC
						");
						if($this->kclass->DB->numRows()){
							$k=1;
							$itemtr.='<tbody><tr class="bold "><td colspan=10>生产制造单编号：<span class="middle bold darkred">'.$manufacture['manufactureno'].'</span>  ('.$manufacture['type'].')　申请人：'.$manufacture['creator'].'　申请时间：'.date('Y-m-d',$manufacture['created']).'</td></tr><tr class="center even" nohover><td width="30">ID</td><td>物资编号</td><td>物资名称</td><td>物资规格</td><td>版本</td><td>数量</td><td>投产日期</td><td>完工日期</td><td>备注</td><td>选</td></tr>';
							while($item=$this->kclass->DB->fetchArray($items)){
								$am=$this->kclass->getMaterialUrl(array('pmaterialid'=>$item['pmaterialid'],'productitemid'=>$item['productitemid'],'materialno'=>$item['materialno']));
								$itemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').'"><input type="hidden" name="m['.$i.']" value="manufacture" /><input type="hidden" name="itemId['.$i.']" value="'.$item['itemid'].'" />';	
									$itemtr.='<td>'.$k.'</td>
										<td>'.$am['murl'].'</td>
										<td>'.$item['title'].'</td>
										<td>'.$item['standard'].' '.$am['mvalue'].'</td>
										<td>'.$item['version'].'</td>';
								$itemtr.='<td>'.$item['quantity'].'</td><td>'.date('Y-m-d',$item['workdate']).'</td><td>'.date('Y-m-d',$item['finishdate']).'</td><td>'.$item['remark'].'</td><td><input type="checkbox" name="itemSelect['.$i.']" value="'.$item['itemid'].'"/></td></tr>';
								$k++;$i++;
							}
						}
					}
					$itemtr.='';
				}
				$body=<<<EOF
<form action="/s.php?module=plan&action=insert" method="post">
<input type="hidden" name="module" value="plan" />
<input type="hidden" name="action" value="insert" />
<input type="hidden" name="planno" value="{$planno}" />
<input type="hidden" name="step" value="1" />
<table><thead><tr><th colspan=10>选择生产制造单明细</th></tr></thead>
{$itemtr}
<tr class="center">
<td colspan=10><input type="submit" value="提交"><input type="reset" value="重置"></td>
</tr>
</tbody></table>
</form>
EOF;
				$this->kclass->tbline+=4;
				$this->kclass->page['title'].='选择生产计划单明细';
				$this->kclass->page['onload'].='dc.tabhover()';
				$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - <a href="/s.php?module=plan&action=list">生产计划单列表</a> - 新建生产计划单', 'right'=>'<a href="/s.php?module=plan&action=list">返回列表</a>','body'=>$body));
			}elseif($this->kclass->input['step']==2){
				$plan=$this->kclass->DB->queryFirst("SELECT planid,planno FROM plan WHERE killed=0 AND planid='".$this->kclass->input['planid']."'");
				$items=$this->kclass->DB->query("
					SELECT p.*,
						m.materialno,m.title,m.standard,
						pm.pmaterialid,
						mv.title AS version,
						mft.manufactureno,mft.situationid,
						s.title AS status,
						u.username AS creator
					FROM planitem AS p
					LEFT JOIN manufactureitem AS mfti ON (mfti.itemid=p.mftitemid)
					LEFT JOIN manufacture AS mft ON (mft.manufactureid=mfti.manufactureid)
					LEFT JOIN mftstatus AS s ON (s.statusid=p.statusid)
					LEFT JOIN material AS m ON (m.materialid=p.materialid)
					LEFT JOIN `pmaterial` AS pm ON (pm.materialid=mfti.materialid)
					LEFT JOIN `materialversion` AS mv ON (mv.versionid=mfti.versionid)
					LEFT JOIN user AS u ON (u.userid=p.creator)
					WHERE p.killed=0 AND p.planid='".$this->kclass->input['planid']."' AND p.creator='".$this->kclass->user['userid']."'
					ORDER BY p.ordering ASC
				");
				if($counter=$this->kclass->DB->numRows()){
					$i=1;
					while($item=$this->kclass->DB->fetchArray($items)){
						$si=$this->kclass->DB->queryFirst("SELECT title FROM mftsituation WHERE killed=0 AND situationid='".$item['situationid']."'");
						$manufactureno='<a href="/s.php?module=manufacture&action=view&manufactureid='.$item['manufactureid'].'">'.$item['manufactureno'].'</a>';
						$am=$this->kclass->getMaterialUrl(array('pmaterialid'=>$item['pmaterialid'],'productitemid'=>$item['productitemid'],'materialno'=>$item['materialno']));
						$itemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').'">
							<input type="hidden" name="itemId['.$i.']" value="'.$item['itemid'].'">
							<td>'.$i.'</td>
							<td>'.$manufactureno.'<input type="hidden" name="itemMftid['.$i.']" value="'.$item['manufactureid'].'"></td>
							<td>'.$si['title'].'</td>
							<td>'.$am['murl'].'</td>
							<td>'.$item['title'].'</td>
							<td>'.$item['standard'].''.$am['mvalue'].'</td>
							<td>'.$item['version'].'</td>
							<td>'.$item['quantity'].'</td>
							<td>'.$item['remark'].'</td>
							<td><input type="text" id="workdate'.$i.'" name="itemWorkdate['.$i.']" size="15" value="'.date('Y-m-d',$item['workdate']).'" /></td>
							<td><input type="text" id="finishdate'.$i.'" name="itemFinishdate['.$i.']" size="15" value="'.date('Y-m-d',$item['finishdate']).'" /></td>
							<td><input type="text" id="workhours'.$i.'" name="itemWorkhours['.$i.']" size="15" /></td>
							</tr>';
						if($i<=$counter){
							$id.='#workdate'.$i.',';
							$id.='#finishdate'.$i.',';
						}else{
							$id.='#workdate'.$i;
							$id.='#finishdate'.$i;
						}
						$i++;
					}
				}
			//mrp
			$arr=$this->kclass->mrpList($this->kclass->input['planid']);
			$j=1;
			if($arr){
				foreach($arr as $key => $val){
					$m=$this->kclass->DB->queryFirst("
						SELECT m.originid,mo.title 
						FROM material AS m
						LEFT JOIN materialorigin AS mo ON (mo.originid=m.originid) 
						WHERE materialid='".$val['materialid']."'
					");
					$wc=$this->kclass->DB->queryFirst("
						SELECT wc.title AS workcenter,wc.burthen
						FROM workcenteritem 
						LEFT JOIN workcenter AS wc ON (wc.workcenterid=workcenteritem.workcenterid)
						WHERE materialid='".$val['materialid']."' AND versionid='".$val['versionid']."' AND productitemid='".$val['productitemid']."' 
					");
					if($val['netQuantity']<0){
						$mrpitem.='';
					}else{
						$version=$this->kclass->DB->queryFirst("SELECT title FROM materialversion WHERE versionid='".$val['versionid']."'");
						$mrpitem.='<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').'">
						<input type="hidden" name="materialid['.$j.']" value="'.$val['materialid'].'">
						<input type="hidden" name="versionid['.$j.']" value="'.$val['versionid'].'">
						<input type="hidden" name="productitemid['.$j.']" value="'.$val['productitemid'].'">
						<input type="hidden" name="netQuantity['.$j.']" value="'.$val['netQuantity'].'">
						<td>'.$j.'</td>
						<td>'.$val['materialno'].'</td>
						<td>'.$val['title'].'</td>
						<td>'.$val['standard'].'</td>
						<td>'.$version['title'].'</td>
						<td>'.$val['netQuantity'].'</td>
						<td>'.$m['title'].''.$this->kclass->iif($m['originid']==1,'('.$this->kclass->iif($wc['workcenter']=='','未关联',$wc['workcenter']).')','无').'</td>
						<td><input type="text" id="requiredate'.$j.'" name="requiredate['.$j.']" size="25" /></td>
						<td><input type="text" id="remark'.$j.'" name="remark['.$j.']" size="15" /></td>
						</tr>';
						$id.='#requiredate'.$j.',';
						$j++;
					}
				}
			}else{
				$mrpitem.='';
			}
	

$body=<<<EOF
<form action="/s.php?module=plan&action=insert" name="plan" method="post">
<input type="hidden" name="module" value="plan" />
<input type="hidden" name="action" value="insert" />
<input type="hidden" name="planid" value="{$this->kclass->input['planid']}" />
<input type="hidden" name="planno" value="{$plan['planno']}" />
<input type="hidden" name="step" value="2" />
<table class="hundred">
<thead><tr><th colspan="4">新建生产计划单</th></tr></thead>
<tbody>
<tr class="odd">
<td>生产计划单编号：<span class="red bold">*</span></td><td><span class="middle bold darkred">{$plan['planno']}</span> <span class="gray small">此为预估编号，保存后才可确定</span></td>
<td>计划员：<span class="red bold">*</span></td><td><input type="text" name="planner"></td>
</tr>
<tr class="even">
<td>备注：<span class="red bold">*</span></td>
<td><textarea type="text" name="remark" style="width:250px;height:100px"></textarea>
<td></td>
<td></td>
</tr>
</tbody>
</table>
<table class="hundred">
<thead><tr><th colspan="12">计划生产明细</th></tr></thead>
<tbody>
<tr class="even center"><td width="15">ID</td><td>制造单编号</td><td>紧急情况</td><td width="60">物资编号</td><td>物资名称</td><td>物资规格</td><td>版本</td><td width="100">数量 <span class="red bold">*</span></td><td width="50">备注</td><td width="50">投产日期</td><td width="90">完工日期</td><td width="90">工作时数</td></tr>
</tbody><tbody class="small">
{$itemtr}
</tbody>
</table>
<table class="hundred">
<thead><tr><th colspan="12">物料需求明细</th></tr></thead>
<tbody>
<tr class="even center"><td width="15">ID</td><td width="60">物资编号</td><td>物资名称</td><td>物资规格</td><td>版本</td><td width="100">净需求数量 <span class="red bold">*</span></td><td width="100">需求来源</td><td width="50">需求日期</td><td width="80">备注</td></tr>
</tbody><tbody class="small">
{$mrpitem}
<tr class="even">
<td class="small gray" colspan="12">
注意事项：<br>
①如果所列表格不够，那么在提交保存后再点击“修改”，来添加所要生产的物资；<br>
②如果不从“生产任务单中”选择物资，而是生产新物资，那么就填写“物资编号”、“物资名称”、“物资规格”，或者在物资列表中选择；<br>
③“生产任务单及其物资”中 灰色的物资表示已经被选中过，不可以继续选择；<br>
④时间的格式为2012-02-06，中间用半角短横杠隔开；<br>
⑤从库中物资选择时，“单位”不需要选，但是如果生产物资不存在库中时，就必须要选择“单位”
</td>
</tr>
</tbody>
</table>
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
				$this->kclass->page['title'].='新建生产计划单';
				$this->kclass->page['onload'].='var dates=$(\''.$id.'\').datepicker({onSelect:function(selectedDate){instance=$(this).data(\'datepicker\'),date=$.datepicker.parseDate(instance.settings.dateFormat || $.datepicker._defaults.dateFormat,selectedDate,instance.settings);}});dc.tabhover();';
				$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - <a href="/s.php?module=plan&action=list">生产计划单列表</a> - 新建生产计划单', 'right'=>'<a href="/s.php?module=plan&action=list">返回列表</a>','body'=>$body));
			}
		}
				// insert plan
		function insert(){
			if($this->kclass->input['requestMethod'] != 'post'){
				$e='<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
			}else{
				if($this->kclass->input['step']==1){
					if($this->kclass->input['itemSelect']==''){
						$e.='<li>请从生产计划单中选择生产计划单的明细</li>';
					}
				}else{
					$j=count($this->kclass->input['itemId']);
					$hasItem=0;
					for($i=1;$i<$j+1;$i++){
						if($this->kclass->input['itemQuantity'][$i]>0){
							$hasItem=1;
						}
					}
					
				}
			}
			if($e){
				$this->kclass->messager(array(
					'title' => '新建生产计划单',
					'text' => '您在新建生产计划单的过程中有以下错误：<ul>'.$e.'</ul>',
					'url' => 'javascript:history.back()',
					'sec' => 3
				));
			}
			if($this->kclass->input['step']==1){
				$count=count($this->kclass->input['itemId']);
				$this->kclass->DB->query("
					INSERT INTO plan (planno,created,creator)
					VALUES ('".$this->kclass->input['planno']."','".TIMENOW."','".$this->kclass->user['userid']."')
				");
				$planid=$this->kclass->DB->insertID();
				for($i=1;$i<$count+1;$i++){
					if($this->kclass->input['itemSelect'][$i]!=''){
						$item=$this->kclass->DB->queryFirst("
								SELECT *
								FROM `manufactureitem` 
								WHERE killed=0 AND ifChooser=0 AND itemid='".$this->kclass->input['itemSelect'][$i]."'
							");
						$this->kclass->DB->query("
							INSERT INTO planitem
								(materialid,versionid,productitemid,planid,mftitemid,statusid,workdate,finishdate,quantity,remark,created,creator)
							VALUES
								('".$item['materialid']."','".$item['versionid']."','".$item['productitemid']."','".$planid."','".$item['itemid']."',1,'".$item['workdate']."','".$item['finishdate']."','".$item['quantity']."','".$item['remark']."','".TIMENOW."','".$this->kclass->user['userid']."')
						");
					}
					$this->kclass->DB->query("UPDATE `manufactureitem` SET ifChooser=1 WHERE itemid='".$this->kclass->input['itemSelect'][$i]."'");
				}

				$this->kclass->messager(array(
					'title' => '选择生产计划申请单明细',
					'text' => '生产计划单 【<b>'.$this->kclass->input['planno'].'</b>】 的明细已添加成功!返回新建生产计划申请单',
					'url' => '/s.php?module=plan&action=add&planid='.$planid.'&step=2',
					'sec' => 2
				));
					}elseif($this->kclass->input['step']==2){
				$this->kclass->DB->query("
					UPDATE plan SET 
						planner='".$this->kclass->input['planner']."',
						remark='".$this->kclass->input['remark']."'
					WHERE planid='".$this->kclass->input['planid']."'
				");
				$key=array();
				$count=count($this->kclass->input['itemId']);
				for($m=1;$m<=$count;$m++){
					if($this->kclass->input['itemWorkdate'][$m]){
						$sd=explode('-',$this->kclass->input['itemWorkdate'][$m]);
						$workdate=mktime(0,0,0,$sd[1],$sd[2],$sd[0]);
					}else{
						$workdate=0;
					}
						if($this->kclass->input['itemFinishdate'][$m]){
						$sd1=explode('-',$this->kclass->input['itemFinishdate'][$m]);
						$finishdate=mktime(0,0,0,$sd1[1],$sd1[2],$sd1[0]);
					}else{
						$finishdate=0;
					}
					$this->kclass->DB->query("
						UPDATE planitem SET 
							workdate='".$workdate."',
							finishdate='".$finishdate."',
							workhours='".$this->kclass->input['itemWorkhours'][$m]."'
						WHERE itemid='".$this->kclass->input['itemId'][$m]."'
					");
				}
				
				//planmrp
				$counter=count($this->kclass->input['materialid']);
				for($j=1;$j<=$counter;$j++){
					if($this->kclass->input['requiredate'][$j]){
						$sd3=explode('-',$this->kclass->input['requiredate'][$j]);
						$requiredate=mktime(0,0,0,$sd3[1],$sd3[2],$sd3[0]);
					}else{
						$requiredate=0;
					}
					$this->kclass->DB->query("
						INSERT INTO planmrp
							(materialid,versionid,productitemid,planid,netQuantity,requiredate,remark,created,creator)
						VALUES
							('".$this->kclass->input['materialid'][$j]."','".$this->kclass->input['versionid'][$j]."','".$this->kclass->input['productitemid'][$j]."','".$this->kclass->input['planid']."','".$this->kclass->input['netQuantity'][$j]."','".$requiredate."','".$this->kclass->input['remark'][$j]."','".TIMENOW."','".$this->kclass->user['userid']."')
					");
				}
				//
				
				$this->kclass->messager(array(
					'title' => '新建生产计划单',
					'text' => '生产计划单 【<b>'.$this->kclass->input['planno'].'</b>】 已新建成功!返回添加申请生产物资',
					'url' => '/s.php?module=plan&action=view&planid='.$this->kclass->input['planid'],
					'sec' => 2
				));
			}
		}
		//
		function update(){
			$plan=$this->kclass->DB->queryFirst("
				SELECT planid,planno,statusid,planner,remark,created
				FROM plan
				WHERE killed=0 AND planid='".$this->kclass->input['planid']."'
				LIMIT 0,1
			");
			if(!$plan OR $plan['statusid']!=1){
				$this->kclass->messager(array(
					'title' => '修改生产',
					'text' => '选择的生产计划单数据错误',
					'url' => '/s.php?module=plan&action=list',
					'sec' => 2
				));
			}
			if($this->kclass->input['step']=='item'){
				$planItems=$this->kclass->DB->query("
					SELECT p.*,
						m.materialno,m.title,m.standard,
						pm.pmaterialid,
						mv.title AS version,
						mft.manufactureno
					FROM planitem AS p
					LEFT JOIN material AS m ON (m.materialid=p.materialid)
					LEFT JOIN `pmaterial` AS pm ON (pm.materialid=p.materialid)
					LEFT JOIN `materialversion` AS mv ON (mv.versionid=p.versionid)
					LEFT JOIN manufactureitem AS mfti ON (mfti.itemid=p.mftitemid)
					LEFT JOIN manufacture AS mft ON (mft.manufactureid=mfti.manufactureid)
					WHERE p.killed=0 AND p.planid='".$this->kclass->input['planid']."'
					ORDER BY p.itemid ASC
				");
				if($this->kclass->DB->numRows()){
					$m=1;
					while($planItem=$this->kclass->DB->fetchArray($planItems)){
						$orgItemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').' small"><input type="hidden" name="planItemId['.$m.']" value="'.$planItem['itemid'].'" /><input type="hidden" name="manufactureid['.$m.']" value="'.$planItem['manufactureid'].'" /><td>'.$m.'</td>';
						$manufactureno='<a href="/s.php?module=manufacture&action=view&manufactureid='.$planItem['manufactureid'].'">'.$planItem['manufactureno'].'</a>';
						$title=$planItem['title'];
						$standard=$planItem['standard'];
						$am=$this->kclass->getMaterialUrl(array('pmaterialid'=>$planItem['pmaterialid'],'productitemid'=>$planItem['productitemid'],'materialno'=>$planItem['materialno']));
						$orgItemtr.='<td>'.$manufactureno.'</td>
							<td>'.$am['murl'].'</td>
							<td>'.$title.'</td>
							<td>'.$standard.''.$am['mvalue'].'</td>
							<td>'.$planItem['version'].'</td>
							<td>'.$planItem['quantity'].' </td>
							<td>'.$planItem['remark'].'</td>
							<td><input type="text" id="olworkdate'.$m.'" name="olworkdate['.$m.']" size="10" value="'.date('Y-m-d',$planItem['workdate']).'" /></td>
							<td><input type="text" id="olfinishdate'.$m.'" name="olfinishdate['.$m.']" size="10" value="'.date('Y-m-d',$planItem['finishdate']).'" /></td>
							<td><input type="text" name="olworkhours['.$m.']" value="'.$planItem['workhours'].'" size="10"/></td>
							<td><input type="checkbox" name="itemKill['.$m.']" value="'.$planItem['itemid'].'"/></td></tr>';
						$id.='#olworkdate'.$m.',';
						$id.='#olfinishdate'.$m.',';
						$m++;
					}
				}
				$manufactures=$this->kclass->DB->query("
					SELECT `manufacture`.*,
					u.username AS creator,
					us.username AS modifier
					FROM `manufacture`
					LEFT JOIN `user` AS u ON (u.userid=`manufacture`.creator)
					LEFT JOIN `user` AS us ON (us.userid=`manufacture`.modifier)
					WHERE `manufacture`.killed=0 AND `manufacture`.statusid=1
				");
				if($this->kclass->DB->numRows()){
					$i=1;
					while($manufacture=$this->kclass->DB->fetchArray($manufactures)){
						//申请的物资
						$items=$this->kclass->DB->query("
							SELECT mi.*,
								m.materialno,m.title,m.standard,
								pm.pmaterialid,
								mv.title AS version
							FROM manufactureitem AS mi
							LEFT JOIN material AS m ON (m.materialid=mi.materialid)
							LEFT JOIN `pmaterial` AS pm ON (pm.materialid=mi.materialid)
							LEFT JOIN `materialversion` AS mv ON (mv.versionid=mi.versionid)
							WHERE mi.killed=0 AND mi.materialid<>0 AND mi.ifChooser=0 AND mi.manufactureid='".$manufacture['manufactureid']."' 
							ORDER BY mi.ordering ASC
						");
						if($this->kclass->DB->numRows()){
							$k=1;
							$itemtr.='<tbody><tr class="bold "><td colspan=13>生产制造单编号：<span class="middle bold"><a href="/s.php?module=manufacture&action=view&manufactureid='.$manufacture['manufactureid'].'">'.$manufacture['manufactureno'].'</a></span>　申请人：'.$manufacture['creator'].'　申请时间：'.date('Y-m-d',$manufacture['created']).'</td></tr><tr class="center even" nohover><td width="15">ID</td><td>物资编号</td><td>物资名称</td><td>物资规格</td><td>版本</td><td>数量</td><td>备注</td><td width="50">投产日期</td><td width="50">完工日期</td><td width="90">工时</td><td>选</td></tr>';
							while($item=$this->kclass->DB->fetchArray($items)){
								$am=$this->kclass->getMaterialUrl(array('pmaterialid'=>$item['pmaterialid'],'productitemid'=>$item['productitemid'],'materialno'=>$item['materialno']));
								$itemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').' small"><input type="hidden" name="itemId['.$i.']" value="'.$item['itemid'].'" />';
									$itemtr.='<td>'.$k.'</td>
										<td>'.$am['murl'].'</td>
										<td>'.$item['title'].'</td>
										<td>'.$item['standard'].''.$am['mvalue'].'</td>
										<td>'.$item['version'].'</td>';
								$itemtr.='<td align="right">'.$item['quantity'].'</td>
							<td>'.$item['remark'].'</td>
							<td><input type="text" id="itemworkdate'.$i.'" name="itemworkdate['.$i.']" size="10" value="'.date('Y-m-d',$item['workdate']).'" /></td>
							<td><input type="text" id="itemfinishdate'.$i.'" name="itemfinishdate['.$i.']" size="10" value="'.date('Y-m-d',$item['finishdate']).'" /></td>
							<td><input type="text" name="itemworkhours['.$i.']" value="8" size="10"/></td>
								<td><input type="checkbox" name="itemSelect['.$i.']" value="'.$item['itemid'].'"/></td></tr>';
									$id.='#itemworkdate'.$i.',';
									$id.='#itemfinishdate'.$i.',';
								$k++;$i++;
							}
						}
					}
					$itemtr.='';
				}
$body=<<<EOF
<form action="/s.php?module=plan&action=doupdate" method="post">
<input type="hidden" name="module" value="plan" />
<input type="hidden" name="action" value="doupdate" />
<input type="hidden" name="planid" value="{$this->kclass->input['planid']}" />
<input type="hidden" name="planno" value="{$plan['planno']}" />
<input type="hidden" name="step" value="item" />
<table class="hundred"><thead><tr><th colspan=14>当前生产计划申请单明细</th></tr></thead>
<tbody>
<tr class="center even" nohover><td width="15">ID</td><td width="50">制造单编号</td><td>物资编号</td><td>物资名称</td><td>物资规格</td><td>版本</td><td>数量</td><td>备注</td><td width="50">投产日期</td><td width="50">完工日期</td><td width="90">工时</td><td>删</td></tr>
{$orgItemtr}
</tbody>
</table>
<table class="hundred"><thead><tr><th colspan=13>选择生产计划申请单明细</th></tr></thead>
{$itemtr}
<tr class="center">
<td colspan=13><input type="submit" value="提交"><input type="reset" value="重置"></td>
</tr>
</tbody></table>
</form>
EOF;
				$this->kclass->tbline+=4;
				$this->kclass->page['title'].='选择生产计划单明细';
				$this->kclass->page['onload'].='var dates=$(\''.$id.'\').datepicker({onSelect:function(selectedDate){instance=$(this).data(\'datepicker\'),date=$.datepicker.parseDate(instance.settings.dateFormat || $.datepicker._defaults.dateFormat,selectedDate,instance.settings);}});dc.tabhover()';
				$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - <a href="/s.php?module=plan&action=list">生产计划单列表</a> - 修改生产计划单', 'right'=>'<a href="/s.php?module=plan&action=list">返回列表</a>','body'=>$body));
			}else{	
$body=<<<EOF
<form action="/s.php?module=plan&action=doupdate" name="plan" method="post">
<input type="hidden" name="module" value="plan" />
<input type="hidden" name="action" value="doupdate" />
<input type="hidden" name="planid" value="{$this->kclass->input['planid']}" />
<input type="hidden" name="planno" value="{$plan['planno']}" />
<table class="hundred">
<thead><tr><th colspan="4">新建生产计划单</th></tr></thead>
<tbody>
<tr class="odd">
<td>生产计划单编号：<span class="red bold">*</span></td><td>{$plan['planno']}<span class="small gray">(编号不会被修改)</span></td>
<td>生产员：<span class="red bold">*</span></td><td><input type="text" name="planner" value="{$plan['planner']}"></td>
</tr>
<tr class="even">
<td>备注：</td>
<td><textarea type="text" name="remark" style="width:444px;height:111px;">{$plan['remark']}</textarea></td>
<td></td><td></td>
</tr>
</tbody>
</table>
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
				$this->kclass->page['title'].='修改生产计划单';
				$this->kclass->page['onload'].='dc.tabhover();';
				$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - <a href="/s.php?module=plan&action=view&planid='.$this->kclass->input['planid'].'">查看生产计划单</a> - 修改生产计划单', 'right'=>'<a href="/s.php?module=plan&action=update">返回列表</a>　|　<a href="/s.php?module=plan&action=view&planid='.$this->kclass->input['planid'].'">查看生产计划单</a>　','body'=>$body));
			}
		}
		//
		function doupdate(){
			if($this->kclass->input['requestMethod'] != 'post'){
				$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
			}else{
				$plan=$this->kclass->DB->queryFirst("SELECT planid,planno FROM `plan` WHERE planid='".$this->kclass->input['planid']."'");
				if(!$plan){
					$e .= '<li>您要编辑的生产计划单并不存在，请返回列表刷新后再操作。</li>';
				}
				if($this->kclass->input['step']=='item'){
					
				}else{
					if(!$this->kclass->input['planner']){
						$e .= '<li>请选择生产计划单中的 计划员。</li>';
					}
					if(!$this->kclass->input['remark']){
						$e .= '<li>请选择生产计划单中的 备注。</li>';
					}
				}
			}
			if($e){
				$this->kclass->messager(array(
					'title' => '修改生产计划单',
					'text' => '您在修改生产计划单的过程中有以下错误：<ul>'.$e.'</ul>',
					'url' => 'javascript:history.back()',
					'sec' => 3
				));
			}
			if($this->kclass->input['step']=='item'){
				$count=count($this->kclass->input['planItemId']);
				for($m=1;$m<$count+1;$m++){
					if($this->kclass->input['itemKill'][$m]!='' AND $this->kclass->input['itemKill'][$m]=$this->kclass->input['planItemId'][$m]){
						$this->kclass->DB->query("UPDATE `planitem` SET killed=".TIMENOW.",killer='".$this->kclass->user['userid']."'  WHERE itemid='".$this->kclass->input['planItemId'][$m]."'");
						$this->kclass->DB->query("UPDATE `manufactureitem` SET ifChooser=0 WHERE manufactureid='".$this->kclass->input['manufactureid'][$m]."'");
					}else{
						if($this->kclass->input['olworkdate'][$m]){
							$sd=explode('-',$this->kclass->input['olworkdate'][$m]);
							$workdate=mktime(0,0,0,$sd[1],$sd[2],$sd[0]);
						}else{
							$workdate=0;
						}
						if($this->kclass->input['olfinishdate'][$m]){
							$sd=explode('-',$this->kclass->input['olfinishdate'][$m]);
							$finishdate=mktime(0,0,0,$sd[1],$sd[2],$sd[0]);
						}else{
							$finishdate=0;
						}
						$this->kclass->DB->query("
							UPDATE planitem SET 
								workhours='".$this->kclass->input['olworkhours'][$m]."',
								workdate='".$workdate."',
								finishdate='".$finishdate."'
							WHERE itemid='".$this->kclass->input['planItemId'][$m]."'
						");
					}
				}
				$counter=count($this->kclass->input['itemId']);
				for($i=1;$i<$counter+1;$i++){
					if($this->kclass->input['itemSelect'][$i]!=''){
						if($this->kclass->input['itemworkdate'][$i]){
							$sd=explode('-',$this->kclass->input['itemworkdate'][$i]);
							$workdate=mktime(0,0,0,$sd[1],$sd[2],$sd[0]);
						}else{
							$workdate=0;
						}
						if($this->kclass->input['itemfinishdate'][$i]){
							$sd=explode('-',$this->kclass->input['itemfinishdate'][$i]);
							$finishdate=mktime(0,0,0,$sd[1],$sd[2],$sd[0]);
						}else{
							$finishdate=0;
						}
						$item=$this->kclass->DB->queryFirst("
								SELECT `manufactureitem`.*
								FROM `manufactureitem` 
								WHERE killed=0 AND itemid='".$this->kclass->input['itemSelect'][$i]."'
								LIMIT 0,1
							");
						$this->kclass->DB->query("
							INSERT INTO planitem
								(materialid,versionid,productitemid,mftitemid,planid,workdate,finishdate,workhours,quantity,remark,created,creator)
							VALUES
								('".$item['materialid']."','".$item['versionid']."','".$item['productitemid']."','".$item['itemid']."','".$this->kclass->input['planid']."','".$workdate."','".$finishdate."','".$this->kclass->input['itemworkhours'][$i]."','".$item['quantity']."','".$item['remark']."','".TIMENOW."','".$this->kclass->user['userid']."')
						");
						$this->kclass->DB->query("UPDATE `manufactureitem` SET ifChooser=1 WHERE itemid='".$this->kclass->input['itemSelect'][$i]."'");
					}
				}
				$this->kclass->messager(array(
					'title' => '修改生产计划申请单明细',
					'text' => '生产计划单 【<b>'.$plan['planno'].'</b>】 的明细已修改成功!返回新建生产计划申请单',
					'url' => '/s.php?module=plan&action=view&planid='.$this->kclass->input['planid'],
					'sec' => 2
				));
			}else{
				// update plan
				$this->kclass->DB->query("
					UPDATE plan SET
						planner='".$this->kclass->input['planner']."',
						remark='".$this->kclass->input['remark']."',
						modified='".TIMENOW."',
						modifier='".$this->kclass->user['userid']."'
					WHERE planid='".$this->kclass->input['planid']."'
				");
				$this->kclass->messager(array(
					'title' => '修改生产计划单',
					'text' => '生产计划单 【<b>'.$this->kclass->input['planno'].'</b>】 已修改成功!返回修改生产计划单',
					'url' => '/s.php?module=plan&action=view&planid='.$this->kclass->input['planid'],
					'sec' => 2
				));
			}
		}
		//
		function verify(){
			$plan=$this->kclass->DB->queryFirst("
				SELECT p.planid,p.planno,p.applicant,p.ifVerify,p.purpose,p.remark,p.created,p.modified,p.amount,
					c.title AS currency,
					u.username AS creator,
					us.username AS modifier
				FROM plan AS p
				LEFT JOIN currency AS c ON (c.currencyid=p.currencyid)
				LEFT JOIN user AS u ON (u.userid=p.creator)
				LEFT JOIN user AS us ON (u.userid=p.modifier)
				WHERE p.killed=0 AND p.planid='".$this->kclass->input['planid']."'
				LIMIT 0,1
			");
			if(!$plan AND $plan['ifVerify']!=0){
				$e='<li>数据错误，很抱歉~</li>';
			}
			if(isset($e)){
				$this->kclass->messager(array(
					'title' => '审核生产计划单',
					'text' => '审核生产计划单<ul>'.$e.'</ul>',
					'url' => 'javascript:history.back()',
					'sec' => 3
				));
			}
			$plan['created']=date('Y-n-d',$plan['created']);
			$verify=$this->kclass->verify($plan['ifVerify']);
			if($plan['modified']!=0)$modify='，由'.$plan['modifier'].'于'.date('Y-n-d',$plan['modified']).'修改';
			if($plan['ifVerify']==0){
				$verifyOperate='|　<a href="/s.php?module=plan&action=update&planid='.$plan['planid'].'">修改</a>　|　<span class="small">审核：</span><a href="/s.php?module=plan&action=verify&planid='.$plan['planid'].'" onclick="return confirm(\'你确定要对这个生产计划单 '.$plan['planno'].' 审核通过吗？\');">通过</a>　<a href="/s.php?module=plan&action=unverify&planid='.$plan['planid'].'" onclick="return confirm(\'你确定要对这个生产计划单 '.$plan['planno'].' 审核不通过吗？\');">不通过</a>';
			}elseif($plan['ifVerify']==1){
				$verifyOperate='';
			}
			//申请的物资
			$items=$this->kclass->DB->query("
				SELECT p.*,
					m.materialno,m.title,m.standard,
					s.supplierid,s.title AS supplier,
					u.title AS unit,
					c.title AS currency,c.symbol
				FROM planitem AS p
				LEFT JOIN material AS m ON (m.materialid=p.materialid)
				LEFT JOIN supplier AS s ON (s.supplierid=p.supplierid)
				LEFT JOIN unit AS u ON (u.unitid=p.unitid)
				LEFT JOIN currency AS c ON (c.currencyid=p.currencyid)
				WHERE p.killed=0 AND p.planid='".$plan['planid']."'
				ORDER BY p.ordering ASC
			");
			if($this->kclass->DB->numRows()){
				$i=1;
				while($item=$this->kclass->DB->fetchArray($items)){
					$addMaterial='';
					if($plan['ifVerify']==1 AND $item['materialid']==0){
						$addMaterial='<a target="_blank" href="/s.php?module=material&action=add&itemid='.$item['itemid'].'">新增该物资</a>';
					}
					
					$item['arrivalTime']=date('Y-m-d',$item['arrivalTime']);
					$class=$this->kclass->iif($i%2==0,'class="even"','class="odd"');
					if($item['module']=='planApply'){
						$mitem=$this->kclass->DB->queryFirst("SELECT pi.*,pa.applyno,pa.planApplyid FROM preinbounditem AS pi LEFT JOIN planapply AS pa ON (pi.mid=pa.planApplyid) WHERE pi.killed=0 AND pi.module='planApply' AND pi.itemid='".$item['mitemid']."'");
						$type="生产计划";
						$typeno='<a href="/s.php?module=planapply&action=view&planApplyid='.$mitem['planApplyid'].'">'.$mitem['applyno'].'</a>';
					}elseif($item['module']=='entrust'){
						$mitem=$this->kclass->DB->queryFirst("SELECT ei.*,e.entrustid,e.entrustno FROM entrustitem AS ei LEFT JOIN entrust AS e ON (e.entrustid=ei.entrustid) WHERE ei.killed=0 AND ei.itemid='".$item['mitemid']."'");
						$type="外发加工";
						$typeno='<a href="/s.php?module=entrust&action=view&entrustid='.$mitem['entrustid'].'">'.$mitem['entrustno'].'</a>';
					}
					$materialno=$this->kclass->iif($item['materialid']==0,$mitem['materialno'],'<a href="/s.php?module=material&action=view&materialid='.$item['materialid'].'">'.$item['materialno'].'</a>');
					$title=$this->kclass->iif($item['materialid']==0,$mitem['title'],$item['title']);
					$standard=$this->kclass->iif($item['materialid']==0,$mitem['stanard'],$item['standard']);

					$itemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').' small"><td>'.$i.'</td>
						<td>'.$type.'</td>
						<td>'.$typeno.'</td>
						<td>'.$materialno.'</td>
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
<div class="title"><span class="right small gray">由{$plan['creator']}于{$plan['created']}建立{$modify}。</span>{$plan['planno']}</div>
<table class="hundred">
<thead><tr><th colspan="4">生产计划单</th></tr></thead>
<tbody>
<tr class="odd">
	<td width="100">编　　号：</td><td width="475">{$plan['planno']}　（{$verify}）</td>
	<td width="100">采 购 员：</td><td>{$plan['applicant']}</td>
</tr>
<tr class="even">
	<td>总　　价：</td><td>{$plan['amount']}</td>
	<td>申请时间：</td><td>{$plan['created']}</td>
</tr>
<tr class="odd">
	<td>目　　的：</td><td>{$plan['purpose']}</td>
	<td>备　　注：</td><td>{$plan['remark']}</td>
</tr>
</tbody>
</table>
<table class="hundred">
<thead><tr><th colspan="14">生产物资</th></tr></thead>
<tbody>
<tr class="center even">
	<td width="15">ID</td><td width="60">申请类型</td><td width="50">申请单编号</td><td>物资编号</td><td>物资名称</td><td>物资规格</td><td>供应商</td><td>数量/单位</td><td>币种</td><td>单价</td><td>折扣</td><td>总价</td><td>到货时间</td><td>备注</td>
</tr>
	{$itemtr}
</tbody>
</table>
<form method="post" action="/s.php?module=plan&action=doverify">
<input type="hidden" name="module" value="plan">
<input type="hidden" name="action" value="doverify">
<input type="hidden" name="planid" value="{$this->kclass->input['planid']}">
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
			$this->kclass->page['title'].='审核生产计划单';
			$this->kclass->page['onload'].='dc.tabhover();';
			$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - <a href="/s.php?module=plan&action=view&planid='.$this->kclass->input['planid'].'">查看生产计划单</a> - 审核生产计划单', 'right'=>'<a href="/s.php?module=plan&action=list">返回列表</a>　|　<a href="/s.php?module=plan&action=view&planid='.$this->kclass->input['planid'].'">查看生产计划单</a>','body'=>$body));
		}
		//
		function doverify(){
			if($this->kclass->input['requestMethod'] != 'post'){
				$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
			}else{
				$plan=$this->kclass->DB->queryFirst("SELECT planno FROM plan WHERE planid='".$this->kclass->input['planid']."'");
				if($this->kclass->input['ifVerify']!=1 AND $this->kclass->input['ifVerify']!=-1){
					$e.='<li>请选择生产计划单中的 是否通过审核 按钮。</li>';
				}
			}
			if($e){
				$this->kclass->messager(array(
					'title' => '审核生产计划单',
					'text' => '您在审核生产计划单的过程中有以下错误：<ul>'.$e.'</ul>',
					'url' => 'javascript:history.back()',
					'sec' => 3
				));
			}
			$this->kclass->DB->query("
				UPDATE plan SET
					ifVerify='".$this->kclass->input['ifVerify']."',
					verifier='".$this->kclass->user['userid']."',
					verified='".TIMENOW."',
					verifyRemark='".$this->kclass->input['verifyRemark']."'
				WHERE planid='".$this->kclass->input['planid']."'
			");
			if($this->kclass->input['ifVerify']==1){
				$verify='已通过';
			}elseif($this->kclass->input['ifVerify']==-1){
				$verify='未通过';
			}
			$this->kclass->messager(array(
				'title' => '审核生产计划单',
				'text' => '生产计划单 【<b>'.$plan['planno'].'</b>】 '.$verify.'审核!返回查看生产计划单',
				'url' => '/s.php?module=plan&action=view&planid='.$this->kclass->input['planid'],
				'sec' => 2
			));
		}
		//
		function kill(){
			if($this->kclass->input['planid']<=0){
				$this->kclass->boinkIt('/s.php?module=plan');
			}
			if($this->kclass->input['rt']!='list' AND $this->kclass->input['confirm'] == 0){
				$this->kclass->boinkIt('/s.php?module=plan&action=view&planid='.$this->kclass->input['planid']);
			}
			if($this->kclass->input['planid'] < 0){
				$e = '<li>请从正确的页面进行操作，如果此问题持续存在，请联系网站管理员。</li>';
			}
			if(isset($e)){
				$this->kclass->messager(array(
					'title' => '删除生产计划单',
					'text' => '您在删除生产计划单的过程中有以下错误：<ul>'.$e.'</ul>',
					'url' => 'javascript:history.back()',
					'sec' => 3
				));
			}
			if($this->kclass->input['rt']=='view'){
				$rt = '&action=view&planid='.$this->kclass->input['planid'];
			}elseif($this->kclass->input['rt']=='list'){
				$rt = '#'.$this->kclass->input['planid'];
			}
			$plan=$this->kclass->DB->queryFirst("
				SELECT planno
				FROM plan
				WHERE planid='".$this->kclass->input['planid']."'
			");
			if($plan){
				$this->kclass->DB->query("
					UPDATE `plan`
					SET killed=".TIMENOW.",killer='".$this->kclass->user['userid']."'
					WHERE planid='".$this->kclass->input['planid']."'
				");
				$this->kclass->messager(array(
					'title' => '删除生产计划单成功',
					'text' => '生产计划单 <b>'.$plan['planno'].'</b> 已成功被标记为删除!',
					'url' => '/s.php?module=plan'.$rt,
					'sec' => 2
				));
			}else{
				$this->kclass->messager(array(
					'title' => '删除生产计划单失败',
					'text' => '您要删除的生产计划单，不存在！',
					'url' => '/s.php?module=plan'.$rt,
					'sec' => 3
				));
			}
		}

		//
		function remove(){
			if($this->kclass->input['planid']<=0){
				$this->kclass->boinkIt('/s.php?module=plan');
			}
			$plan = $this->kclass->DB->queryFirst("
				SELECT planno
				FROM `plan`
				WHERE planid='".$this->kclass->input['planid']."'
			");
$body = <<<EOF
<form action="/s.php?module=plan&action=kill" name="plan" method="post">
<input type="hidden" name="module" value="plan">
<input type="hidden" name="action" value="kill">
<input type="hidden" name="planid" value="{$this->kclass->input['planid']}">
<input type="hidden" name="rt" value="{$this->kclass->input['rt']}">
<table><thead>
<thead>
<tr>
	<th>删除生产计划单：{$plan['planno']}</th>
</tr>
</thead>
<tbody>
<tr class="even">
	<td class="middle">你确定要删除生产计划单: <a href="/s.php?module=plan&action=view&planid={$this->kclass->input['planid']}" class="big bold" target="_blank">{$plan['planno']}</a> 吗?</td>
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
			$this->kclass->page['title'] .= ' - 删除 - '.$plan['planno'];
			$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 删除生产计划单 - '.$plan['planno'], 'right' => '<a href="/s.php?module=plan">返回列表</a>', 'body'=>$body));
		}

		//
		function revival(){
			if($this->kclass->input['planid']<=0){
				$this->kclass->boinkIt('/s.php?module=plan');
			}
			if($this->kclass->input['rt']!='list' AND $this->kclass->input['confirm']==0){
				$this->kclass->boinkIt('/s.php?module=plan&action=view&planid='.$this->kclass->input['planid']);
			}
			if($this->kclass->input['planid']<0){
				$e = '<li>请从正确的页面进行操作，如果此问题持续存在，请联系网站管理员。</li>';
			}
			if(isset($e)){
				$this->kclass->messager(array(
					'title' => '恢复生产计划单',
					'text' => '您在恢复生产计划单的过程中有以下错误：<ul>'.$e.'</ul>',
					'url' => 'javascript:history.back()',
					'sec' => 3
				));
			}
			$plan = $this->kclass->DB->queryFirst("
				SELECT planno
				FROM `plan`
				WHERE planid='".$this->kclass->input['planid']."'
			");
			if($this->kclass->input['rt']=='view'){
				$rt = '&action=view&planid='.$this->kclass->input['planid'];
			}elseif($this->kclass->input['rt']=='list'){
				$rt = '#'.$this->kclass->input['planid'];
			}
			if($plan){
				$this->kclass->DB->query("
					UPDATE `plan`
					SET killed=0,killer=0
					WHERE planid='".$this->kclass->input['planid']."'
				");
				$this->kclass->messager(array(
					'title' => '恢复生产计划单成功',
					'text' => '生产计划单 <b>'.$plan['planno'].'</b> 已成功被恢复！',
					'url' => '/s.php?module=plan'.$rt,
					'sec' => 2
				));
			}else{
				$this->kclass->messager(array(
					'title' => '恢复生产计划单',
					'text' => '您要恢复的生产计划单不存在！',
					'url' => '/s.php?module=plan'.$rt,
					'sec' => 3
				));
			}
		}

		//
		function restore(){
			if($this->kclass->input['planid']<=0){
				$this->kclass->boinkIt('/s.php?module=plan');
			}
			$plan = $this->kclass->DB->queryFirst("
				SELECT planno
				FROM `plan`
				WHERE planid='".$this->kclass->input['planid']."'
			");
$body = <<<EOF
<form action="/s.php?module=plan&action=revival" name="plan" method="post">
<input type="hidden" name="module" value="plan">
<input type="hidden" name="action" value="revival">
<input type="hidden" name="planid" value="{$this->kclass->input['planid']}">
<input type="hidden" name="rt" value="{$this->kclass->input['rt']}">
<table><thead>
<thead>
<tr>
	<th>恢复生产计划单：{$plan['planno']}</th>
</tr>
</thead>
<tbody>
<tr class="even">
	<td class="middle">你确定要恢复生产计划单: <a href="/s.php?module=plan&action=view&planid={$this->kclass->input['planid']}" class="big bold" target="_blank">{$plan['planno']}</a> 吗?</td>
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
			$this->kclass->page['title'] .= ' - 恢复 - '.$plan['planno'];
			$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 恢复生产计划单 - '.$plan['planno'], 'right' => '<a href="/s.php?module=plan">返回列表</a>', 'body'=>$body));
		}
	}
?>