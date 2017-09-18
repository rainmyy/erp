<?php
//
class requirement{
	var $kclass;
	var $mftType=array(array('entitle'=>'normal','title'=>'订单需求'),array('entitle'=>'forecast','title'=>'库存需求'));
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
			case 'revise':
				$this->revise();
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
			case 'viewMRP':
				return $this->viewMRP();
				break;
			case 'addMRP':
				return $this->addMRP();
				break;
			case 'insertMRP':
				return $this->insertMRP();
				break;
			case 'orderChange':
				return $this->orderChange();
				break;
			case 'doorderChange':
				return $this->doorderChange();
				break;
			case 'appointComplete':
				return $this->appointComplete();
				break;
			case 'doappointComplete':
				return $this->doappointComplete();
				break;
			case 'addPurchaseApply':
				return $this->addPurchaseApply();
				break;
			case 'addPlan':
				return $this->addPlan();
				break;
			case 'addEntrust':
				return $this->addEntrust();
				break;
			case 'addOutbound':
				return $this->addOutbound();
				break;
			case 'addExtra':
				return $this->addExtra();
				break;
			case 'insertExtra':
				return $this->insertExtra();
				break;
			case 'updateExtra':
				return $this->updateExtra();
				break;
			case 'doupdateExtra':
				return $this->doupdateExtra();
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
			case 'lock':
				return $this->lock();
				break;
			case 'dolock':
				return $this->dolock();
				break;
			case 'unlock':
				return $this->unlock();
				break;
			case 'dounlock':
				return $this->dounlock();
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
	// main page,shows all the requirement info list
	function mmlist(){
		if($this->kclass->input['show']=='all'){
			$condition='`requirement`.killed>=0';
		}elseif($this->kclass->input['show']=='unlock'){
			$condition='`requirement`.statusid=1 AND `requirement`.killed=0';
		}elseif($this->kclass->input['show']=='locked'){
			$condition='`requirement`.statusid=2 AND `requirement`.killed=0';
		}elseif($this->kclass->input['show']=='inProduction'){
			$condition='`requirement`.statusid=3 AND `requirement`.killed=0';
		}elseif($this->kclass->input['show']=='Done'){
			$condition='`requirement`.statusid=4 AND `requirement`.killed=0';
		}else{
			$condition='`requirement`.killed=0';
		}
		$find=$this->kclass->findRequirement(array('created'=>$this->kclass->input['created'],'quantity'=>$this->kclass->input['quantity'],'dateline'=>$this->kclass->input['dateline'],'mftType'=>$this->kclass->input['mftType'],'modeid'=>$this->kclass->input['modeid'],'materialno'=>$this->kclass->input['materialno'],'materialtitle'=>$this->kclass->input['materialtitle'],'requirementno'=>$this->kclass->input['requirementno'],'orderno'=>$this->kclass->input['orderno'],'sampleno'=>$this->kclass->input['sampleno']));
		//查找页面
		if($this->kclass->input['requirementno']!='')
		$body.='　计划单编号: "<span class="red">'.$this->kclass->input['requirementno'].'</span>"　';
		if($this->kclass->input['quantity']!='')
		$body.='　毛需求数量: "<span class="red">'.$this->kclass->input['quantity'].'</span>"　';
		if($this->kclass->input['dateline']!='')
		$body.='　需求交期: "<span class="red">'.$this->kclass->input['dateline'].'</span>"　';
		if($this->kclass->input['mftType']!='')
		$body.='　计划来源: "<span class="red">'.$this->kclass->iif($this->kclass->input['mftType']==1,'订单需求','库存需求').'</span>"　';
		if($this->kclass->input['modeid']!='')
		$body.='　计算方式: "<span class="red">'.$this->kclass->iif($this->kclass->input['modeid']==0,'净需求',$this->kclass->iif($this->kclass->input['modeid']==1,'毛需求','毛需求(BOM)')).'</span>"　';
		if($this->kclass->input['materialno']!='')
		$body.='　物料编号: "<span class="red">'.$this->kclass->input['materialno'].'</span>"　';
		if($this->kclass->input['materialtitle']!='')
		$body.='　物料名称: "<span class="red">'.$this->kclass->input['materialtitle'].'</span>"　';
		if($this->kclass->input['orderno']!='')
		$body.='　PO单编号: "<span class="red">'.$this->kclass->input['orderno'].'</span>"　';
		if($this->kclass->input['sampleno']!='')
		$body.='　样品单编号: "<span class="red">'.$this->kclass->input['sampleno'].'</span>"　';
		if($this->kclass->input['created']!='')
		$body.='　计划时间: "<span class="red">'.$this->kclass->input['created'].'</span>"　';
		if($body)$body='<div class="toptip"><span class="bold">在需求计划中查找：</span>'.$body.'</div>';
		//
		if($this->kclass->input['orderby']!='')$query['orderby']=$this->kclass->input['orderby'];
		if($this->kclass->input['direction']!='')$query['direction']=$this->kclass->input['direction'];
		if($this->kclass->input['show']!='')$query['show']=$this->kclass->input['show'];
		if($this->kclass->input['layout']!='')$query['layout']=$this->kclass->input['layout'];
		$orderby=$this->kclass->orderby(array('module'=>'requirement','direction'=>'desc','orderby'=>'created', 'default'=>'requirementno', 'serial'=>array( array('title'=>'编号', 'field'=>'requirementno','word'=>'no'), array('title'=>'修改时间', 'field'=>'modified'), array('title'=>'建立时间', 'field'=>'created')),'appendUrl'=>$query));
		if(is_array($query) AND count($query)>0)$queryPart='&'.http_build_query($query);
		//分页
		$perpage=20;
		$requirementList=$this->kclass->listRequirement(array('prepage'=>$perpage,'find'=>$find,'orderby'=>$query['orderby'],'direction'=>$query['direction'],'show'=>$query['show'],'layout'=>$query['layout']));
		if($this->kclass->input['layout']=='grid'){
				$body.='<ul id="tbody" class="mmlist clear">';
			}else{
				$body.='<table class="hundred mytable tablesorter"><thead><th width="20">ID</th><th width="80">编号</th><th width="50">紧急情况</th><th width="50">计划来源</th><th width="50">需求计算方式</th><th width="50">计划员</th><th width="70">计划时间</th><th width="70">备注</th><th width="70">状态</th><th width="80">选项</th></thead><tbody id="tbody">';
			}
		$body.=$requirementList['body'];
		if($requirementList['counter']>0){
			if($requirementList['count']>$requirementList['counter']){
				$body.='<div class="page" id="requirementpage"><a href="javascript:dc.tableItem.page('.$perpage.')">点击查看更多</a></div>';
			}
		}
		$body.='<div class="clear"><span class="gray small left">(共<span class="darkred">'.$requirementList['count'].'</span>条记录)</span></div>';
		//
		$mode=$this->kclass->chooserMode(array('name'=>'modeid','selectedid'=>99));
		$body.='<div class="search_menu" id="search_menu" style="display:none;">
	<form action="/s.php?module=requirement&action=list" method="get">
<input type="hidden" name="module" value="requirement">
<input type="hidden" name="action" value="list">
<table style="width:450px">
<thead><tr><th colspan=2>根据条件查找需求计划单<span class=right>[<a href="#" class="switchsearch">关闭</a>]</span></th></tr></thead>
<tbody>
<tr class="odd">
<td width="100">计划单编号：</td><td><input type="text" name="requirementno"></td>
</tr>
<tr class="even">
<td width="100">毛需求数量：</td><td><input type="text" name="quantity"></td>
</tr>
<tr class="odd">
<td width="100">需求交期：</td><td><input type="text" name="dateline" id="dateline"></td>
</tr>
<tr class="even">
<td width="100">计划来源：</td><td class="small"><input name="mftType" value="" checked="" type="radio">所有　<input name="mftType" value="1" type="radio">订单需求　<input name="mftType" value="2" type="radio">库存需求</td>
</tr>
<tr class="odd">
<td width="100">计算方式：</td><td class="small"><input name="modeid" value="" checked="" type="radio">所有　'.$mode.'</td>
</tr>
<tr class="even">
<td width="100">物料编号：</td><td><input type="text" name="materialno" style="width:300px"></td>
</tr>
<tr class="odd">
<td width="100">物料名称：</td><td><input type="text" name="materialtitle" style="width:300px"></td>
</tr>
<tr class="even">
<td width="100">PO订单编号：</td><td><input type="text" name="orderno" ></td>
</tr>
<tr class="odd">
<td width="100">样品单编号：</td><td><input type="text" name="sampleno"></td>
</tr>
<tr class="even">
<td width="100">计划时间：</td><td><input type="text" name="created" id="created"></td>
</tr>
<tr class="odd">
<td class="small gray" colspan="9">
①需求计划单可以通过输入“计划单编号”，“日期”和“物料编号”进行查找<br>
②请保证输入的值无误</td>
</tr>
<tr class="even" nohover>
<td class="center" colspan=2><input type="submit" id="submitButton" value="  提交  "><input type="reset" value="  复位  "/></td>
</tr>
</tbody>
</table>
</form>
</div>';
		$id.=',#created';
		$id.=',#dateline';
		if($this->kclass->input['layout']=='grid'){
			$layoutLink='<a href="/s.php?module=requirement&action=list&layout=list'.str_replace('&layout=grid', '', $queryPart).'">列表</a> 格子';
		}else{
			$layoutLink='列表 <a href="/s.php?module=requirement&action=list&layout=grid'.str_replace('&layout=list', '', $queryPart).'">格子</a>';
		}
			$showLink='<a href="/s.php?module=requirement&action=list">默认</a> <a href="/s.php?module=requirement&action=list&show=all">所有<span class="small gray">(包括删除)</span></a> <a href="/s.php?module=requirement&action=list&show=unlock">未锁定</a>  <a href="/s.php?module=requirement&action=list&show=locked">已锁定</a>  <a href="/s.php?module=requirement&action=list&show=inProduction">生产中</a>  <a href="/s.php?module=requirement&action=list&show=Done">生产完成</a>';
		$this->kclass->page['title'].='需求计划单列表';
		$this->kclass->page['onload'].='dc.listhover();dc.switchsearch();$(\'.mytable\').fixedtableheader();$(\'#date,'.$id.'\').datepicker();';
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - 需求计划单列表','right' => '<a href="#" class="switchsearch">查找</a>　 | 　<span class="small">排序：</span>'.$orderby['link'].'|　<span class="small">显示方式：</span>'.$showLink.'　|　<span class="small">布局：</span>'.$layoutLink.'　|　<a href="/s.php?module=requirement&action=add">新建</a>　|　<a href="/s.php?module=requirement&action=viewMRP">MRP列表</a>','body'=>$body));
	}
	//find页面需要完善，可作为高级搜索，设置更多的查询条件 --gaowenfei 2013/11/25 8:45
	function find(){
			$mode=$this->kclass->chooserMode(array('name'=>'modeid','selectedid'=>99));
			$body=<<<EOF
<form action="/s.php?module=requirement&action=list" method="get">
<input type="hidden" name="module" value="requirement">
<input type="hidden" name="action" value="list">
<table style="width:450px">
<thead><tr><th colspan=2>根据条件查找需求计划单</th></tr></thead>
<tbody>
<tr class="odd">
<td width="100">计划单编号：</td><td><input type="text" name="requirementno"></td>
</tr>
<tr class="even">
<td width="100">毛需求数量：</td><td><input type="text" name="quantity"></td>
</tr>
<tr class="odd">
<td width="100">需求交期：</td><td><input type="text" name="dateline" id="dateline"></td>
</tr>
<tr class="even">
<td width="100">计划来源：</td><td class="small"><input name="mftType" value="" checked="" type="radio">所有　<input name="mftType" value="1" type="radio">订单需求　<input name="mftType" value="2" type="radio">库存需求</td>
</tr>
<tr class="odd">
<td width="100">计算方式：</td><td class="small"><input name="modeid" value="" checked="" type="radio">所有　{$mode}</td>
</tr>
<tr class="even">
<td width="100">物料编号：</td><td><input type="text" name="materialno" style="width:300px"></td>
</tr>
<tr class="odd">
<td width="100">物料名称：</td><td><input type="text" name="materialtitle" style="width:300px"></td>
</tr>
<tr class="even">
<td width="100">PO订单编号：</td><td><input type="text" name="orderno" ></td>
</tr>
<tr class="odd">
<td width="100">样品单编号：</td><td><input type="text" name="sampleno"></td>
</tr>
<tr class="even">
<td width="100">计划时间：</td><td><input type="text" name="created" id="created"></td>
</tr>
<tr class="odd">
<td class="small gray" colspan="9">
①需求计划单可以通过输入“计划单编号”，“日期”和“物料编号”进行查找<br>
②请保证输入的值无误</td>
</tr>
<tr class="even" nohover>
<td class="center" colspan=2><input type="submit" id="submitButton" value="  提交  "><input type="reset" value="  复位  "/></td>
</tr>
</tbody>
</table>
</form>
EOF;
			$id.=',#created';
			$id.=',#dateline';

			$this->kclass->page['title'].='查找需求计划单';
			$this->kclass->page['onload'].='dc.listhover();$(\'#date,'.$id.'\').datepicker();';
			$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - 查找需求计划单','right' => '<a href="/s.php?module=requirement">查看列表</a>','body'=>$body));
	}
	//保存历史版本
	function revise(){
		if($this->kclass->input['reviseid']>0){
			$revise = $this->kclass->DB->queryFirst("
				SELECT `revise`.ptext,`revise`.version,`revise`.remark
				FROM `revise`
				WHERE reviseid='".$this->kclass->input['reviseid']."'
			");
			if($revise){
				$this->kclass->page['onload'] .= "dc.tabs({'id':'requirement'});dc.tabhover()";
				$this->kclass->page['title'] .= ' - 需求计划单版本R'.$revise['version'];
				$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl.' - 需求计划单版本信息　版本R'.$revise['version'], 'right' => '', 'body'=>$revise['ptext']));
			}else{
				$this->kclass->messager(array(
						'title' => '查看需求计划单历史版本',
						'text' => '没有此历史版本!',
						'url' => '/s.php?module=requirement&action=view&requirementid='.$this->kclass->input['requirementid'],
						'sec' => 5
				));
			}
		}
	}
	//
	function view(){
		$requirement=$this->kclass->DB->queryFirst("
			SELECT r.requirementid,r.requirementno,r.statusid,r.modeid,r.orderChange,r.planner,r.remark,r.locked,r.killed,r.modified,r.created,
				mt.title AS type,
				msi.title AS situation,
				s.title AS status,
				m.realname AS creator,
				me.realname AS modifier,
				mem.realname AS locker
			FROM requirement AS r
			LEFT JOIN mftstatus AS s ON (s.statusid=r.statusid)
			LEFT JOIN `mfttype` AS mt ON (mt.typeid=r.typeid)
			LEFT JOIN `mftsituation` AS msi ON (msi.situationid=r.situationid)
			LEFT JOIN member AS m ON (m.userid=r.creator)
			LEFT JOIN member AS me ON (me.userid=r.modifier)
			LEFT JOIN member AS mem ON (mem.userid=r.locker)
			WHERE r.requirementid='".$this->kclass->input['requirementid']."'
			LIMIT 0,1
		");
		if(!$requirement){
			$e='<li>数据错误，很抱歉</li>';
		}
		if($e){
			$this->kclass->messager(array(
				'title' => '查看需求计划单',
				'text' => '您在查看需求计划单的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$requirement['created']=date('Y-m-d H:i:s',$requirement['created']);
		if($requirement['modified']!=0)$modify='，由 '.$requirement['modifier'].' 于 '.date('Y-m-d H:i:s',$requirement['modified']).' 修改';
		if($requirement['statusid']==2){
			$pstatus='，由 '.$requirement['locker'].' 于 '.date('Y-m-d H:i:s',$requirement['locked']).' 锁定';
		}
		if($requirement['statusid']==1){
			$changeStatus='<a href="/s.php?module=requirement&action=remove&requirementid='.$requirement['requirementid'].'&rt=view">删除</a>　|　<span class="gray small">修改：</span><a href="/s.php?module=requirement&action=update&requirementid='.$requirement['requirementid'].'">计划单</a>　<a href="/s.php?module=requirement&action=updateExtra&requirementid='.$requirement['requirementid'].'">补计划单</a>　|　<a href="/s.php?module=requirement&action=lock&requirementid='.$requirement['requirementid'].'">锁定</a>';
		}elseif($requirement['statusid']!=1){
			$changeStatus='|　<a href="/s.php?module=requirement&action=unlock&requirementid='.$requirement['requirementid'].'">解锁</a>　|　<a href="/s.php?module=outbound&action=add&departmentid=8&typeid=3">新建出库单</a>';
		}
		//计划的物资
		$items=$this->kclass->DB->query("
			SELECT ri.module,ri.mid,ri.mitemid,ri.materialid,ri.versionid,ri.materialitemid,ri.statusid,ri.reviseid,ri.dateline,ri.quantity,
				m.materialno,m.title,m.standard,m.min,m.originid,
				mi.itemno,
				mv.title AS version,mv.`change`,mv.qualified,mv.planInbound,mv.planOutbound,
				materialorigin.title AS origin,
				r.version AS reviseversion
			FROM requirementitem AS ri
			LEFT JOIN material AS m ON (m.materialid=ri.materialid)
			LEFT JOIN materialitem AS mi ON (mi.itemid=ri.materialitemid)
			LEFT JOIN materialversion AS mv ON (mv.versionid=ri.versionid)
			LEFT JOIN materialorigin ON (materialorigin.originid=m.originid)
			LEFT JOIN revise AS r ON (r.reviseid=ri.reviseid)
			WHERE ri.killed=0 AND ri.requirementid='".$requirement['requirementid']."'
			ORDER BY ri.module ASC,ri.itemid ASC,m.materialno ASC
		");
		$itemCount=$this->kclass->DB->numRows();
		if($itemCount){
			$i=1;$ifbom='';
			while($item=$this->kclass->DB->fetchArray($items)){
				$usable=$item['qualified']+$item['planInbound']-$item['planOutbound']; // 可用库存
				$item['dateline']=date('Y-m-d',$item['dateline']);
				// 订单或者样品单 版本
				$orderrevise=$this->kclass->relatedRevise(array('module'=>$item['module'],'mid'=>$item['mid']));
				$orderversion='(R'.($item['reviseversion']+1).')</span>';
				if($item['module']=='order'){
					$order=$this->kclass->DB->queryFirst("SELECT orderno,ordertitle,orderid FROM `order` WHERE orderid='".$item['mid']."'");
					$orderno=$this->kclass->iif($orderrevise['reviseid']==$item['reviseid'],'<a class="tip" href="/s.php?module=order&action=view&orderid='.$item['mid'].'" title="'.$order['ordertitle'].'">'.$order['orderno'].'</a> <span class="small green">','<a class="tip" href="/s.php?module=order&action=revise&reviseid='.$orderrevise['reviseid'].'" title="'.$order['ordertitle'].'">'.$order['orderno'].'</a> <span class="small darkred">').$orderversion;
				}elseif($item['module']=='sample'){
					$order=$this->kclass->DB->queryFirst("SELECT sampleno,title AS sampletitle,sampleid FROM `sample` WHERE sampleid='".$item['mid']."'");
					$orderno=$this->kclass->iif($orderrevise['reviseid']==$item['reviseid'],'<a class="tip" href="/s.php?module=sample&action=view&sampleid='.$item['mid'].'" title="'.$order['sampletitle'].'">'.$order['sampleno'].'</a> <span class="small green">','<a class="tip" href="/s.php?module=sample&action=revise&reviseid='.$orderrevise['reviseid'].'" title="'.$order['sampletitle'].'">'.$order['sampleno'].'</a> <span class="small darkred">').$orderversion;
				}elseif($item['module']=='stock'){
					$orderno='库存需求';
				}
				//判断是否存在子BOM
				if($item['originid']==1){
					$child=$this->kclass->bomChildList(array('parentid'=>$item['materialid'],'materialitemid'=>$item['materialitemid'],'versionid'=>$item['versionid'],'loopNum'=>1,'getCount'=>1));
					$ifbom=$this->kclass->iif($child==1,'是','否');
				}else{
					$ifbom='-';
				}
				// 需求计划明细
				$materialInfo=$this->kclass->getMaterial(array('materialid'=>$item['materialid'],'itemid'=>$item['materialitemid'])); // 获得物资信息
				$itemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').' '.$this->kclass->iif($item['originid']==1,$this->kclass->iif($ifbom=='否','bgRed',''),'gray').' small">
					<td>'.$i.'</td>
					<td>'.$orderno.'</td>
					<td><a href="'.$materialInfo['url'].'">'.$materialInfo['no'].'</a></td>
					<td>'.$materialInfo['material'].'</td>
					<td>'.$materialInfo['standard'].'</td>
					<td>'.$this->kclass->iif($item['change']=='',$item['version'],'<a class="tip" href="#" title="( '.$item['change'].' )">'.$item['version'].$this->kclass->iif($item['itemno']!='',' ('.$item['itemno'].') ','').'</a>').'</td>
					<td>'.$ifbom.'</td>
					<td>'.$item['origin'].'</td>
					<td>'.$item['quantity'].'</td>
					<td>'.$item['dateline'].'</td>
					<td>'.$item['remark'].'</td>
					<td>'.$item['qualified'].'</td>
					<td>'.$item['planInbound'].'</td>
					<td>'.$item['planOutbound'].'</td>
					<td>'.$item['min'].'</td>
					<td>'.$usable.'</td>
				</tr>';
				$i++;
				if(($requirement['modeid']==2 AND $item['originid']==1) AND $requirement['statusid']==1){//毛需求(BOM)
					$bomItemtr.='<tr><td colspan="16" class="bold"><a href="'.$materialInfo['url'].'">'.$materialInfo['no'].'</a>　'.$materialInfo['material'].'　'.$materialInfo['standard'].'　 (版本：'.$item['version'].')</td></tr>';
					if($child==1){
						$k=1;
						$child=$this->kclass->bomChildList(array('parentid'=>$item['materialid'],'materialitemid'=>$item['materialitemid'],'versionid'=>$item['versionid'],'loopNum'=>1));
						foreach($child['bom'] as $val){
							$usable=$val['qualified']+$val['planInbound']-$val['planOutbound'];
							$bomItemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').' small '.$this->kclass->iif($val['ifGeneral']==0,$this->kclass->iif($val['quantity']>$val['qualified'],'bgYellow',''),'').'">
								<td>'.$k.'</td>
								<td><a href="'.$val['url'].'">'.$val['no'].'</a>　'.$val['title'].'　'.$val['standard'].'</td>
								<td>'.$val['version'].'</td>
								<td>'.$val['origin'].'</td>
								<td>'.$this->kclass->iif($val['ifGeneral']==0,$val['quantity'],'通用辅料').'</td>
								<td>'.$val['qualified'].'</td>
								<td>'.$val['planInbound'].'</td>
								<td>'.$val['planOutbound'].'</td>
								<td>'.$val['min'].'</td>
								<td>'.$usable.'</td>
							</tr>';
							$k++;
						}
					}else{
						$bomItemtr.='<tr><td colspan="16" class="darkred">物资 [<a href="'.$materialInfo['url'].'">'.$materialInfo['no'].'</a>　'.$materialInfo['material'].'　'.$materialInfo['standard'].'</td>] BOM(子) 不存在！</td></tr>';
					}
					unset($child);
				}
			}
		}
		if($requirement['statusid']==1){
			if($requirement['modeid']==2){
				$bomItemth='<table class="hundred"><thead><tr><th colspan="15">物料需求明细'.$this->kclass->iif($requirement['statusid']==1,'','<span class="right"><a href="/s.php?module=task&action=add">新增生产任务</a></span>').'</th></tr></thead><tbody><tr class=""><td width="30">ID</td><td>物资</td><td>版本</td><td width="50">来源</td><td width="70">毛需求数量</td><td width="70"	>良品库存</td><td width="70">待入库存</td><td width="70">待出库存</td><td width="70">安全库存</td><td width="70">计划可用</td></tr>';
				$bomItemtr=$bomItemth.$bomItemtr.'</tbody></table>';
			}elseif($requirement['modeid']==0 OR $requirement['modeid']==1){//净需求和毛需求
				$versionId=$materialId=$materialitemId=$quantity=$dateline=array();
				$mrpitems=$this->kclass->DB->query("
					SELECT ri.*,
						m.min,
						mv.qualified,mv.planInbound,mv.planOutbound
					FROM requirementitem AS ri
					LEFT JOIN material AS m ON (m.materialid=ri.materialid)
					LEFT JOIN materialversion AS mv ON (mv.versionid=ri.versionid)
					WHERE ri.killed=0 AND m.originid=1 AND ri.requirementid='".$this->kclass->input['requirementid']."'
					ORDER BY ri.ordering ASC
				");
				$mrpItemtr='<table class="hundred"><thead><tr><th colspan="12">MRP明细</th></tr></thead><tbody><tr class="even center"><td width="15">ID</td><td width="900">物资</td><td>版本</td><td>存在BOM(子)</td><td width="150">来源</td><td width="150">净需求数量</td><td width="150">需求日期</td></tr></tbody><tbody class="small">';
				if($this->kclass->DB->numRows()){
					while($mrpitem=$this->kclass->DB->fetchArray($mrpitems)){
						if(in_array($mrpitem['versionid'],$versionId)){
							$quantity[$mrpitem['versionid']]+=$mrpitem['quantity'];
							$dateline[$mrpitem['versionid']]=$this->kclass->iif($dateline[$mrpitem['versionid']]>$mrpitem['dateline'],$dateline[$mrpitem['versionid']],$mrpitem['dateline']);
						}else{
							$versionId[]=$mrpitem['versionid'];
							$materialId[$mrpitem['versionid']]=$mrpitem['materialid'];
							$materialitemId[$mrpitem['versionid']]=$mrpitem['$materialitemid'];
							$quantity[$mrpitem['versionid']]=$this->kclass->iif($requirement['modeid']==0,$mrpitem['quantity']-$mrpitem['qualified']+$mrpitem['min']-$mrpitem['planInbound']+$mrpitem['planOutbound'],$mrpitem['quantity']);
							$dateline[$mrpitem['versionid']]=$mrpitem['dateline'];
						}
					}
				}
				$i=1;
				foreach($versionId as $versionid){
					if($quantity[$versionid]>0){
						$version=$this->kclass->DB->queryFirst("SELECT * FROM materialversion WHERE killed=0 AND versionid='".$versionid."' LIMIT 0,1");
						$material=$this->kclass->getMaterial(array('materialid'=>$version['materialid'],'itemid'=>$version['itemid']));
						//判断是否存在子BOM
						if($material['originid']==1){
							$child=$this->kclass->bomChildList(array('parentid'=>$material['materialid'],'materialitemid'=>$version['itemid'],'versionid'=>$versionid,'loopNum'=>1,'getCount'=>1));
							$ifbom=$this->kclass->iif($child==1,'是','否');
						}else{
							$ifbom='-';
						}
						// 明细列表
						$mrpItemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').' '.$this->kclass->iif($material['originid']==1,$this->kclass->iif($ifbom=='否','bgRed',''),'bgYellow').'">
							<td>'.$i.'</td>
							<td><a href="'.$material['url'].'">'.$material['no'].'</a>　'.$material['material'].'　'.$material['standard'].'</td>
							<td>'.$version['title'].'</td>
							<td>'.$ifbom.'</td>
							<td>'.$material['origin'].'</td>
							<td>'.$quantity[$versionid].'</td>
							<td>'.date('Y-m-d',$dateline[$versionid]).'</td>
						</tr>';
						$i++;$ifmrp=1;
					}
				}
				$arr=$this->kclass->mrpList(array('materialid'=>$materialId,'materialitemid'=>$materialitemId,'versionid'=>$versionId,'quantity'=>$quantity,'dateline'=>$dateline));
				if($arr){
					foreach($arr as $key => $val){
						if($val['netQuantity']>0){
							$ifbom='';
							$material=$this->kclass->getMaterial(array('itemid'=>$val['materialitemid'],'materialid'=>$val['materialid']));
							if($material['originid']==1){
								$child=$this->kclass->bomChildList(array('parentid'=>$material['materialid'],'materialitemid'=>$version['itemid'],'versionid'=>$versionid,'loopNum'=>1,'getCount'=>1));
								$ifbom=$this->kclass->iif($child==1,'是','否');
							}else{
								$ifbom='-';
							}
							$mrpItemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').' '.$this->kclass->iif($material['originid']==1,$this->kclass->iif($ifbom=='否','bgRed',''),'bgYellow').'">
								<td>'.$i.'</td>
								<td><a href="'.$material['url'].'">'.$material['no'].'</a>　'.$material['material'].'　'.$material['standard'].'</td>
								<td>'.$val['version'].'</td>
								<td>'.$ifbom.'</td>
								<td>'.$material['origin'].'</td>
								<td>'.$val['netQuantity'].'</td>
								<td>'.date('Y-m-d',$val['dateline']).'</td>
							</tr>';
							$i++;$ifmrp=1;
						}
					}
				}
				if($ifmrp!=1)$mrpItemtr.='<tr><td colspan="14" class="darkred center">物料需求为空！</td></tr>';
				$mrpItemtr.='</tbody></table>';
			}
		}
		if($requirement['statusid']>1){ // 锁定状态后，获得mrp item
			$mrpitems=$this->kclass->DB->query("
				SELECT mrp.mrpid,mrp.requirementid,mrp.materialid,mrp.materialitemid,mrp.versionid,mrp.netQuantity,mrp.requiredate,mrp.ifChooser,mrp.revision,
					mv.title AS version
				FROM mrp
				LEFT JOIN materialversion AS mv ON (mv.versionid=mrp.versionid)
				WHERE mrp.killed=0 AND mrp.requirementid='".$this->kclass->input['requirementid']."'
				ORDER BY mrp.mrpid ASC
			");
			$mrpCount=$this->kclass->DB->numRows();
			if($mrpCount){
				$i=1;$j=1;$k=1;
				$taskItemtr='<table class="hundred"><thead><tr><th colspan="12">MRP明细 - 生产任务物资<span class="right"><a href="/s.php?module=task&action=add">新增生产任务</a></span></th></tr></thead><tbody><tr class="even center"><td width="15">ID</td><td width="800">物资</td><td width="30">版本</td><td width="150">来源</td><td width="150">净需求数量</td><td width="150">需求日期</td><td width="30">版次</td><td width="100">操作</td></tr></tbody><tbody class="small">';
				$purchaseItemtr='<table class="hundred"><thead><tr><th colspan="12">MRP明细 - 采购申请物资<span class="right"><a href="/s.php?module=requirement&action=addPurchaseApply&requirementid='.$this->kclass->input['requirementid'].'">新增采购申请</a></span></th></tr></thead><tbody><tr class="even center"><td width="15">ID</td><td width="800">物资</td><td width="30">版本</td><td width="150">来源</td><td width="150">净需求数量</td><td width="150">需求日期</td><td width="100">操作</td></tr></tbody><tbody class="small">';
				$entrustItemtr='<table class="hundred"><thead><tr><th colspan="12">MRP明细 - 外发加工物资<span class="right"><a href="/s.php?module=requirement&action=addEntrust&requirementid='.$this->kclass->input['requirementid'].'">新增外发加工申请</a></span></th></tr></thead><tbody><tr class="even center"><td width="15">ID</td><td width="800">物资</td><td width="30">版本</td><td width="150">来源</td><td width="150">净需求数量</td><td width="150">需求日期</td><td width="100">操作</td></tr></tbody><tbody class="small">';
				while($mrpitem=$this->kclass->DB->fetchArray($mrpitems)){
					$materialInfo=$this->kclass->getMaterial(array('materialid'=>$mrpitem['materialid'],'itemid'=>$mrpitem['materialitemid']));
					if($materialInfo['originid']<=1){
						$iftask=1;
						$taskItemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').'">
							<td>'.$i.'</td>
							<td><a href="'.$materialInfo['url'].'">'.$materialInfo['no'].'</a>　'.$materialInfo['material'].'　'.$materialInfo['standard'].'</td>
							<td>'.$mrpitem['version'].'</td>
							<td>'.$materialInfo['origin'].'</td>
							<td>'.$mrpitem['netQuantity'].'</td>
							<td>'.date('Y-m-d',$mrpitem['requiredate']).'</td>
							<td>'.$mrpitem['revision'].'</td>
							<td>'.$this->kclass->iif($mrpitem['ifChooser']>0,'已投产','待选择').'</td>
						</tr>';
						$i++;
					}elseif($materialInfo['originid']==2){
						$ifpurchase=1;
						$purchaseItemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').'">
							<td>'.$j.'</td>
							<td><a href="'.$materialInfo['url'].'">'.$materialInfo['no'].'</a>　'.$materialInfo['material'].'　'.$materialInfo['standard'].'</td>
							<td>'.$mrpitem['version'].'</td>
							<td>'.$materialInfo['origin'].'</td>
							<td>'.$mrpitem['netQuantity'].'</td>
							<td>'.date('Y-m-d',$mrpitem['requiredate']).'</td>
							<td>'.$this->kclass->iif($mrpitem['ifChooser']>0,'已申请','待选择').'</td>
						</tr>';
						$j++;
					}elseif($materialInfo['originid']==3){
						$ifentrust=1;
						$entrustItemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').'">
							<td>'.$k.'</td>
							<td><a href="'.$materialInfo['url'].'">'.$materialInfo['no'].'</a>　'.$materialInfo['material'].'　'.$materialInfo['standard'].'</td>
							<td>'.$mrpitem['version'].'</td>
							<td>'.$materialInfo['origin'].'</td>
							<td>'.$mrpitem['netQuantity'].'</td>
							<td>'.date('Y-m-d',$mrpitem['requiredate']).'</td>
							<td>'.$this->kclass->iif($mrpitem['ifChooser']>0,'已申请','待选择').'</td>
						</tr>';
						$k++;
					}
				}
				if($iftask!=1)$taskItemtr.='<tr><td colspan="14" class="darkred center">暂无相关记录！</td></tr>';
				if($ifpurchase!=1)$purchaseItemtr.='<tr><td colspan="14" class="darkred center">暂无相关记录！</td></tr>';
				if($ifentrust!=1)$entrustItemtr.='<tr><td colspan="14" class="darkred center">暂无相关记录！</td></tr>';
				$taskItemtr.='</tbody></table>';
				$purchaseItemtr.='</tbody></table>';
				$entrustItemtr.='</tbody></table>';
			}
			if($requirement['modeid']==2){
				$addMrp='<a class="right" href="/s.php?module=requirement&action=addMRP&requirementid='.$requirement['requirementid'].'">选择需要生产的物资添加到MRP中</a>';
			}
		}
		$mode=$this->kclass->iif($requirement['modeid']==0,'净需求',$this->kclass->iif($requirement['modeid']==1,'毛需求','毛需求(BOM)'));
		$orderChange=$this->kclass->iif($requirement['orderChange']==0,'',' <span class="small darkred"> ( 订单取消 ) </span>');
		if($requirement['statusid']==2){
			$mrp=$this->kclass->DB->queryFirst("SELECT Count(mrpid) AS count FROM mrp WHERE killed=0 AND ifChooser=0 AND requirementid='".$this->kclass->input['requirementid']."' LIMIT 0,1
			");
		}
		$orderChangeth=$this->kclass->iif($requirement['statusid']==1 OR $mrp['count']!='' OR $requirement['typeid']==2,'','<span class="right"><a href="/s.php?module=requirement&action=orderChange&requirementid='.$this->kclass->input['requirementid'].'">订单变更</a></span>');
		$situation='<span class="'.$this->kclass->iif($requirement['situationid']==2,'darkred middle',$this->kclass->iif($requirement['situationid']==3,'red middle bold','')).'">'.$requirement['situation'].'</span>';
		$orderlist = $this->kclass->relatedOrder(array('requirementid'=>$this->kclass->input['requirementid'],'panel'=>'off'));//工作中心相关PO单
		$samplelist = $this->kclass->relatedSample(array('requirementid'=>$this->kclass->input['requirementid'],'panel'=>'off'));//工作中心相关样品单
		$tasklist = $this->kclass->releatedTasklist(array('requirementid'=>$this->kclass->input['requirementid'],'panel'=>'off'));//工作中心相关生产任务单
		$dispatchList = $this->kclass->releatedDispatchlist(array('requirementid'=>$this->kclass->input['requirementid'],'panel'=>'off'));//工作中心相关生产任务单
		$purchaseapplylist = $this->kclass->relatedPurchaseApply(array('requirementid'=>$this->kclass->input['requirementid']));//工作中心相关采购申请单
		$entrustlist = $this->kclass->relatedEntrust(array('requirementid'=>$this->kclass->input['requirementid']));//工作中心相关外发加工单
		$revise=$this->kclass->relatedRevise(array('module'=>'requirement','mid'=>$requirement['requirementid']));
		$checkmrp=$this->kclass->DB->queryFirst("SELECT mrpid FROM mrp WHERE killed=0 AND ifChooser=0 AND requirementid='".$this->kclass->input['requirementid']."' LIMIT 0,1");
		$checkifmrp=$this->kclass->DB->queryFirst("SELECT mrpid FROM mrp WHERE killed=0 AND requirementid='".$this->kclass->input['requirementid']."' LIMIT 0,1");
		$statusid=$this->kclass->iif($checkmrp=='' && $checkifmrp,4,3); 
		if($requirement['statusid']==1){
			$statusid=2;
		}
		$flow=$this->kclass->listFlow(array('module'=>'requirement','statusid'=>$statusid));
		$actionlog = $this->kclass->releatedActionLog(array('module'=>'requirement','mid'=>$this->kclass->input['requirementid'],'panel'=>'off'));//操作日志
		$body=<<<EOF
<div class="title"><span class="right small gray">由 {$requirement['creator']} 于 {$requirement['created']} 建立 {$modify}{$pstatus}。</span>{$requirement['requirementno']}{$revise['currencyRevise']}　</div>
<dl id="requirement" class="tabs" style="display: block;">
<dt tabid="0">基本信息</dt>
<dt title="与此物资需求计划相关的PO订单"{$orderlist['off']} ajax="order">PO订单{$orderlist['count']}</dt>
<dt title="与此物资需求计划相关的样品单"{$samplelist['off']} ajax="sample">样品单{$samplelist['count']}</dt>
<dt title="与此物资需求计划相关的生产任务"{$tasklist['off']} ajax="task">生产任务{$tasklist['count']}</dt>
<dt title="与此物资需求计划相关的派工单"{$dispatchList['off']} ajax="dispatch">派工单{$dispatchList['count']}</dt>
<dt title="与此物资需求计划相关的采购申请"{$purchaseapplylist['off']}>采购申请{$purchaseapplylist['count']}</dt>
<dt title="与此物资需求计划相关的外发加工申请"{$entrustlist['off']}>外发加工{$entrustlist['count']}</dt>
<dt title="与此物资需求计划相关的操作日志"{$actionlog['off']} ajax="log">操作日志{$actionlog['count']}</dt>
<dd>
<div><span class="right">{$flow}</span></div>
<table class="hundred">
<thead><tr><th colspan="6">需求计划单</th></tr></thead>
<tbody>
<tr class="odd">
<td width="100">编　　号：</td><td width="375">{$requirement['requirementno']}　（{$requirement['status']}）</td>
<td width="100">计算方式：</td><td width="375"><span class="bold darkred">{$mode}</span></td>
<td width="100">计 划 员：</td><td>{$requirement['planner']}　　计划时间：{$requirement['created']}</td>
</tr>
<tr class="even">
<td>计划来源：</td><td>{$requirement['type']}{$orderChange}</td>
<td>紧急情况：</td><td>{$situation}</td>
<td>备　　注：</td><td>{$requirement['remark']}</td>
</tr>
</tbody>
</table>
<table class="hundred">
<thead><tr><th colspan="16">需求计划明细{$orderChangeth}{$addMrp}</th></tr></thead>
<tbody>
<tr class="center even">
<td width="15">ID</td><td width="140">订单编号</td><td>物资编号</td><td>物资名称</td><td>物资规格</td><td>版本</td><td>存在BOM(子)</td><td>来源</td><td>毛需求数量</td><td>需求交期</td><td>备注</td><td>良品库存</td><td>待入库存</td><td>待出库存</td><td>安全库存</td><td>计划可用</td>
</tr>
{$itemtr}
</tbody>
</table>
{$mrpItemtr}
{$taskItemtr}
{$bomItemtr}
{$purchaseItemtr}
{$entrustItemtr}
{$revise['panel']}
</dd>
<dd id="order" load="0"></dd>
<dd id="sample" load="0"></dd>
<dd id="task" load="0"></dd>
<dd id="dispatch" load="0"></dd
<dd>{$purchaseapplylist['panel']}</dd>
<dd>{$entrustlist['panel']}</dd>
<dd id="log" load="0"></dd>
EOF;
		$json="{'id':'requirement','requirementid':".$this->kclass->input['requirementid']."}";
		$this->kclass->page['title'].='需求计划单';
		$this->kclass->page['onload'].='dc.tabhover();dc.tips();dc.tabs('.$json.')';
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - <a href="/s.php?module=requirement&action=list">需求计划单列表</a> - 需求计划单','right' => '<span class = "small">操作：</span> '.$this->kclass->iif($requirement['killed']==0,'<a href="/s.php?module=requirement&action=add">新建</a>　'.$changeStatus.'　','<a href="/s.php?module=requirement&action=restore&requirementid='.$this->kclass->input['requirementid'].'&rt=view">恢复</a>').' |　<a href="/s.php?module=requirement&action=viewMRP">MRP列表</a>','body'=>$body));
	}
	// the form for add a requirement
	function add(){
		if($this->kclass->input['step']==''){
			if($this->kclass->input['mftType']==''){
		$body=<<<EOF
<form action="/s.php?module=requirement&action=add" method="get">
<input type="hidden" name="module" value="requirement">
<input type="hidden" name="action" value="add">
<table style="width:450px;">
<thead><tr><th colspan=2>新建需求计划：选择需求来源类型</th></tr></thead>
<tbody>
<tr><td width="90">选择需求来源</td><td><input type="radio" name="mftType" value="normal" checked>订单需求<br><br><input type="radio" name="mftType" value="forecast">库存需求</td></tr>
<tr class="center"><td colspan=2><input type="submit" value=" 提交 "><input type="reset" value=" 重置 "></td></tr>
</tbody>
</table>
EOF;
	}else{
		if($this->kclass->input['mftType']=='normal'){
			$orders=$this->kclass->DB->query("SELECT * FROM `order` WHERE killed=0 AND ifApprove=1 AND ifChooser=0 AND type='PO' ORDER BY created DESC");
			if($this->kclass->DB->numRows()){
				$i=1;$j=1;
				while($order=$this->kclass->DB->fetchArray($orders)){
					//未投产订单（PO）
					$items=$this->kclass->DB->query("
						SELECT item.itemid,item.productid,item.productitemid,item.productAttributes,item.price,item.quantity,item.dateline,
							p.title,
							m.materialid,m.standard,
							brand.title AS brand,
							packing.title AS packing,
							unit.title AS unit
						FROM `item`
						LEFT JOIN `product` AS p ON (p.productid=`item`.productid)
						LEFT JOIN `material` AS m ON (m.materialid=`item`.materialid)
						LEFT JOIN `packing` ON (packing.packingid=item.packingid)
						LEFT JOIN `brand` ON (brand.brandid=item.brandid)
						LEFT JOIN `unit` ON (unit.unitid=item.unitid)
						WHERE `item`.killed=0 AND `item`.module='order' AND `item`.mid='".$order['orderid']."'
						ORDER BY item.itemid ASC,item.modified ASC
					");
					if($this->kclass->DB->numRows()){
						$k=1;
						$revise=$this->kclass->relatedRevise(array('module'=>'order','mid'=>$order['orderid']));
						if($revise['reviseid']=='')$revise['reviseid']=0;
						$itemtr.='<tr class="bold "><td colspan=15><input type="hidden" name="itemOrderId['.$j.']" value="'.$order['orderid'].'"><input type="hidden" name="itemReviseId['.$j.']" value="'.$revise['reviseid'].'"><span class="normal">订单(PO)编号：</span><a class="middle bold" href="/s.php?module=order&action=view&orderid='.$order['orderid'].'">'.$order['orderno'].'</a>'.$revise['currencyRevise'].'　<span class="normal">创建日期：</span>'.date('Y-m-d',$order['created']).'　<span class="normal">交货日期：</span>'.date('Y-m-d',$order['shipmentDate']).'<span class="right">勾选<input type="checkbox" name="orderSelect['.$j.']" value="'.$order['orderid'].'"></span></td></tr><tr class="center even" nohover><td width="15">ID</td><td>产品或配件</td><td>品牌</td><td>包装</td><td>数量 <span class="red bold">*</span></td><td>备注</td></tr>';
						while($item=$this->kclass->DB->fetchArray($items)){
							$product=$this->kclass->getProduct(array('productid'=>$item['productid'],'itemid'=>$item['productitemid']));
							$itemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').' small"><input type="hidden" name="itemId['.$i.']" value="'.$item['itemid'].'"><td>'.$k.'</td>
								<td width="500"><a href="/s.php?module=product&action=view&productid='.$item['productid'].'">'.$product['title'].'</a> '.$product['standard'].'</td>
								<td class="center">'.$item['brand'].'</a></td>
								<td class="center">'.$item['packing'].'</td>
								<td class="center">'.$item['quantity'].' '.$item['unit'].'</td>
								<td>'.$item['remark'].'</td>
							</tr>';
							$k++;$i++;
						}
					}
					$j++;
				}
				$itemtr.='';
			}
			if(!$itemtr)$itemtr.='<tr class="darkred center"><td colspan="10">　暂无可用的 订单(PO) 明细！　</td></tr>';
			$itemtr='<tr class="center"><th colspan="10">请选择 <span class="darkred">订单(PO)</span> 明细　</th></tr>'.$itemtr.'<tr class="center"><th colspan="10">请选择 <span class="darkred">样品单</span> 明细　</th></tr>';
			$samples=$this->kclass->DB->query("
				SELECT s.*,
					st.title AS sampletype 
				FROM `sample` AS s
				LEFT JOIN `sampletype` AS st ON (st.typeid=s.typeid)
				WHERE s.killed=0 AND s.ifApprove=1 AND s.ifChooser=0 
				ORDER BY s.created DESC
			");
			if($this->kclass->DB->numRows()){
				$i=1;$j=1;
				while($sample=$this->kclass->DB->fetchArray($samples)){
					//未投产样品单
					$sampleitems=$this->kclass->DB->query("
						SELECT item.itemid,item.productid,item.productitemid,item.productAttributes,item.price,item.quantity,item.dateline,
							p.title,
							m.materialid,m.standard,
							brand.title AS brand,
							packing.title AS packing,
							unit.title AS unit
						FROM `item`
						LEFT JOIN `product` AS p ON (p.productid=`item`.productid)
						LEFT JOIN `material` AS m ON (m.materialid=`item`.materialid)
						LEFT JOIN packing ON (packing.packingid=item.packingid)
						LEFT JOIN brand ON (brand.brandid=item.brandid)
						LEFT JOIN unit ON (unit.unitid=item.unitid)
						WHERE `item`.killed=0 AND `item`.module='sample' AND `item`.mid='".$sample['sampleid']."'
						ORDER BY item.itemid ASC,item.modified ASC
					");
					if($this->kclass->DB->numRows()){
						$k=1;
						$samplerevise=$this->kclass->relatedRevise(array('module'=>'sample','mid'=>$sample['sampleid']));
						if($samplerevise['reviseid']=='')$samplerevise['reviseid']=0;
						$sampleitemtr.='<tr class="bold "><td colspan=15><input type="hidden" name="itemSampleId['.$j.']" value="'.$sample['sampleid'].'"><input type="hidden" name="itemSampleReviseId['.$j.']" value="'.$revise['reviseid'].'"><span class="normal">样品单编号：</span><a class="middle bold" href="/s.php?module=sample&action=view&orderid='.$sample['sampleid'].'">'.$sample['sampleno'].'</a>'.$samplerevise['currencyRevise'].'　<span class="normal">类型：</span>'.$sample['sampletype'].'　<span class="normal">创建日期：</span>'.date('Y-m-d',$sample['created']).'　<span class="normal">交货日期：</span>'.date('Y-m-d',$sample['deliveryDate']).'<span class="right">勾选<input type="checkbox" name="sampleSelect['.$j.']" value="'.$sample['sampleid'].'"></span></td></tr><tr class="center even" nohover><td width="15">ID</td><td>产品或配件</td><td>品牌</td><td>包装</td><td>数量 <span class="red bold">*</span></td><td>备注</td></tr>';
						while($sampleitem=$this->kclass->DB->fetchArray($sampleitems)){
							$product=$this->kclass->getProduct(array('productid'=>$sampleitem['productid'],'itemid'=>$sampleitem['productitemid']));
							$sampleitemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').' small"><input type="hidden" name="sampleitemId['.$i.']" value="'.$sampleitem['itemid'].'"><td>'.$k.'</td>
								<td width="500"><a href="/s.php?module=product&action=view&productid='.$sampleitem['productid'].'">'.$product['title'].'</a> '.$product['standard'].'</td>
								<td class="center">'.$sampleitem['brand'].'</a></td>
								<td class="center">'.$sampleitem['packing'].'</td>
								<td class="center">'.$sampleitem['quantity'].' '.$sampleitem['unit'].'</td>
								<td>'.$sampleitem['remark'].'</td>
								</tr>';
							$k++;$i++;
						}
					}
					$j++;
				}
				$sampleitemtr.='';
			}
			if(!$sampleitemtr)$itemtr.='<tr class="darkred center"><td colspan="10">　暂无可用的 样品单 明细！　</td></tr>';
			$itemtr.=$sampleitemtr;
			$body=<<<EOF
<form action="/s.php?module=requirement&action=add" method="post">
<input type="hidden" name="module" value="requirement" />
<input type="hidden" name="action" value="add" />
<input type="hidden" name="step" value="2" />
<input type="hidden" name="mftType" value="{$this->kclass->input['mftType']}" />
<table class="hundred"><tbody>
{$itemtr}
<tr class="center">
<td colspan=13><input type="submit" value="提交"><input type="reset" value="重置"></td>
</tr>
</tbody></table>
</form>
EOF;
		}elseif($this->kclass->input['mftType']=='forecast'){
			$start=strtotime(date('Y-m-d',TIMENOW));
			$end=strtotime(date('Y-m-d',$start))+86400;
			$counter=$this->kclass->DB->queryFirst("SELECT COUNT(*) AS count FROM requirement WHERE created>='".$start."' AND created<='".$end."'");
			$requirementno=$this->kclass->id(array('requirement'=>TIMENOW,'number'=>$counter['count']));
			$situation=$this->kclass->chooserMftSituation(array('name'=>'situationid','selectedid'=>1));
			$mode=$this->kclass->chooserMode(array('name'=>'modeid','selectedid'=>2));
			for($i=1; $i<6; $i++){
				$material=$this->kclass->chooserMaterial(array('hName'=>'itemMaterialid['.$i.']','hId'=>'itemMaterialid'.$i,'name'=>'itemMaterial['.$i.']','id'=>'itemMaterial'.$i,'width'=>300,'line'=>$i,'showVersion'=>1));
				$itemtr .= '<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').'">
					<td>'.$i.'</td>
					<td>'.$material.'</td>
					<td><select name="itemVersionid['.$i.']" id="itemVersionid'.$i.'" style="width:80px;"></select></td>
					<td><input type="text" name="itemQuantity['.$i.']" size="10"></td>
					<td><input type="text" name="itemDateline['.$i.']" id="dateline'.$i.'" size="15"></td>
					<td><input type="text" name="itemRemark['.$i.']" size="25"></td>
				</tr>';
				if($i<6){
					$id.='#dateline'.$i.',';
				}else{
					$id.='#dateline'.$i;
				}
			}
			$flow=$this->kclass->listFlow(array('module'=>'requirement','statusid'=>'1'));
			$body=<<<EOF
<form action="/s.php?module=requirement&action=insert" name="requirement" method="post">
<input type="hidden" name="module" value="requirement" />
<input type="hidden" name="action" value="insert" />
<input type="hidden" name="requirementno" value="{$requirementno}" />
<input type="hidden" name="typeid" value="2" />
<div><span class="right">{$flow}</span></div>
<table class="hundred">
<thead><tr><th colspan="4">新建需求计划单 <span class="small darkred">(库存需求)</span></th></tr></thead>
<tbody>
<tr class="odd">
<td>编　　号：<span class="red bold">*</span></td><td><span class="middle bold darkred">{$requirementno}</span> <span class="gray small">此为预估编号，保存后才可确定</span></td>
<td>紧急情况：<span class="red bold">*</span></td>
<td>{$situation}</td>
</tr>
<tr class="even">
<td>计划员：<span class="red bold">*</span></td><td><input type="text" name="planner"  value="{$this->kclass->user['realname']}"></td>
<td>需求计算方式：<span class="red bold">*</span></td>
<td>{$mode}</td>
</tr>
<tr class="odd">
<td>备　　注：</td>
<td><textarea type="text" name="remark" style="width:300px;height:80px"></textarea></td>
<td></td>
<td></td>
</tr>
</tbody>
</table>
<table class="hundred">
<thead>
<tr>
<th colspan="11">选择需求计划明细</th>
</tr>
<tr><th width="30">ID</th><th>产品或配件<span class="red bold">*</span></th><th>版本<span class="red bold">*</span></th><th>毛需求数量<span class="red bold">*</span></th><th>需求交期<span class="red bold">*</span></th><th>备注</th></tr>
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
	}elseif($this->kclass->input['step']==2){
		if($this->kclass->input['orderSelect']=='' AND $this->kclass->input['sampleSelect']==''){
			$e.='<li>请选择订单明细</li>';
			$this->kclass->messager(array(
				'title' => '新建需求计划',
				'text' => '您在新建需求计划的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$start=strtotime(date('Y-m-d',TIMENOW));
		$end=strtotime(date('Y-m-d',$start))+86400;
		$counter=$this->kclass->DB->queryFirst("SELECT COUNT(*) AS count FROM requirement WHERE created>='".$start."' AND created<='".$end."'");
		$requirementno=$this->kclass->id(array('requirement'=>TIMENOW,'number'=>$counter['count']));
		$situation=$this->kclass->chooserMftSituation(array('name'=>'situationid','selectedid'=>1));
		$mode=$this->kclass->chooserMode(array('name'=>'modeid','selectedid'=>0));
		$orderCount=count($this->kclass->input['itemOrderId']);

		for($k=1;$k<=$orderCount;$k++){// 列出订单明细
			if($this->kclass->input['orderSelect'][$k]!=''){
				$itemtr.=$this->_formItem(array('module'=>'order','mid'=>$this->kclass->input['orderSelect'][$k]));
			}
		}
		if($itemtr)$itemtr='<tr class="bold"><td colspan="10">　订单(PO) 明细：　</td></tr>'.$itemtr;
		$sampleCount=count($this->kclass->input['itemSampleId']);
		for($k=1;$k<=$sampleCount;$k++){
			if($this->kclass->input['sampleSelect'][$k]!=''){
				$sampleitemtr.=$this->_formItem(array('module'=>'sample','mid'=>$this->kclass->input['sampleSelect'][$k]));
			}
		}
		if($sampleitemtr)$itemtr.='<tr class="bold"><td colspan="10">　样品单 明细：　</td></tr>'.$sampleitemtr;
		$flow=$this->kclass->listFlow(array('module'=>'requirement','statusid'=>'1'));
$body=<<<EOF
<form action="/s.php?module=requirement&action=insert" name="requirement" method="post">
<input type="hidden" name="module" value="requirement" />
<input type="hidden" name="action" value="insert" />
<input type="hidden" name="requirementno" value="{$requirementno}" />
<input type="hidden" name="typeid" value="1" />
<div><span class="right">{$flow}</span></div>
<table class="hundred">
<thead><tr><th colspan="6">新建需求计划单 <span class="small darkred">(订单需求)</span></th></tr></thead>
<tbody>
<tr class="odd">
<td width="100">编　　号：<span class="red bold">*</span></td><td width="350"><span class="middle bold darkred">{$requirementno}</span> <span class="gray small">此为预估编号，保存后才可确定</span></td>
<td width="100">紧急情况：<span class="red bold">*</span></td><td width="350">{$situation}</td>
<td width="100">计 划 员：<span class="red bold">*</span></td><td><input type="text" name="planner"  value="{$this->kclass->user['realname']}"></td>
</tr>
<tr class="even">
<td>计算方式：<span class="red bold">*</span></td>
<td>{$mode}</td>
<td>备　　注：</td>
<td><textarea type="text" name="remark" style="width:300px;height:80px"></textarea></td><td></td><td></td>
</tr>
</tr>
<tr class="even">

</tbody>
</table>
<table class="hundred">
<thead><tr><th colspan="12">计划生产明细</th></tr></thead>
<tbody><tr><th width="15">ID</th><th width="130">订单</th><th width="80">订单交期</th><th>产品或配件</th><th width="80">毛需求数量</th><th width="350">选择要生产的物资</th><th width="100">需求交期<span class="red bold">*</span></th><th width="100">备注</th></tr></tbody><tbody class="small">
{$itemtr}
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
}
			$this->kclass->tbline+=4;
			$this->kclass->page['title'].='新建需求计划单';
			$this->kclass->page['onload'].='var dates=$(\''.$id.'\').datepicker({onSelect:function(selectedDate){instance=$(this).data(\'datepicker\'),date=$.datepicker.parseDate(instance.settings.dateFormat || $.datepicker._defaults.dateFormat,selectedDate,instance.settings);}});dc.tabhover();dc.tips();';
			$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - <a href="/s.php?module=requirement&action=list">需求计划单列表</a> - 新建需求计划单', 'right'=>'<a href="/s.php?module=requirement&action=list">返回列表</a>','body'=>$body));
	}
	// insert requirement
	function insert(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e='<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			if($this->kclass->input['planner']==''){
				$e.='<li>请填写需求计划单中的 计划员</li>';
			}
			if($this->kclass->input['situationid']==''){
				$e.='<li>请填写需求计划单中的 紧急情况</li>';
			}
			if($this->kclass->input['modeid']==''){
				$e.='<li>请填写需求计划单中的 需求计算方式</li>';
			}
			if($this->kclass->input['typeid']==1){
				$count=count($this->kclass->input['itemId']);
				for($i=1;$i<=$count;$i++){
					if($this->kclass->input['itemDateline'][$i]==''){
						$e.='<li>请填写需求计划单中订单(PO)的生产明细[ID:'.$i.']的 需求日期</li>';
					}
					$jcount=count($this->kclass->input['itemVersionid'.$i]);
					$ifnull=0;$quantity=0;
					for($j=1;$j<=$jcount;$j++){
						if(intval($this->kclass->input['quantity'.$i][$j])>0){
							$ifnull=1;
							$quantity+=$this->kclass->input['quantity'.$i][$j];
						}
					}
					$item=$this->kclass->DB->queryFirst("SELECT p.attrid FROM item AS i LEFT JOIN product AS p ON (p.productid=i.productid) WHERE i.killed=0 AND i.itemid='".$this->kclass->input['itemId'][$i]."'");
					if($ifnull==0){
						$e.='<li>请填写至少一个需求计划单中订单(PO)的生产明细[ID:'.$i.']的 版本 需求数量</li>';
					}elseif($quantity!=$this->kclass->input['totalQuantity'][$i] AND $item['attrid']!=5){
						$e.='<li>需求计划单中订单(PO)的生产明细[ID:'.$i.'] 各版本的需求数量之和 必须和订单数量相等</li>';
					}
				}
			}elseif($this->kclass->input['typeid']==2){
				$ifitem=0;
				$count=count($this->kclass->input['itemMaterialid']);
				for($n=1;$n<$count+1;$n++){
					if($this->kclass->input['itemMaterialid'][$n]>0){
						if($this->kclass->input['itemVersionid'][$n]==''){
							$e.='<li>请填写需求计划单中生产明细[ID:'.$n.']的 版本</li>';
						}
						if($this->kclass->input['itemQuantity'][$n]==''){
							$e.='<li>请填写需求计划单中生产明细[ID:'.$n.']的 毛需求数量</li>';
						}
						if($this->kclass->input['itemDateline'][$n]==''){
							$e.='<li>请填写需求计划单中生产明细[ID:'.$n.']的 需求日期</li>';
						}
						$ifitem=1;
					}
				}
				if($ifitem==0){
					$e.='<li>请至少填写一条需求计划单中的 计划明细</li>';
				}
			}
		}
		if($e){
			$this->kclass->messager(array(
				'title' => '新建需求计划单',
				'text' => '您在新建需求计划单的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$start=strtotime(date('Y-m-d',TIMENOW));
		$end=strtotime(date('Y-m-d',$start))+86400;
		$counter=$this->kclass->DB->queryFirst("SELECT COUNT(*) AS count FROM requirement WHERE created>='".$start."' AND created<='".$end."'");
		$requirementno=$this->kclass->id(array('requirement'=>TIMENOW,'number'=>$counter['count']));
		$this->kclass->DB->query("
			INSERT INTO `requirement` (`requirementno`,`typeid`,`situationid`,`modeid`,`planner`,`remark`,`created`,`creator`)
			VALUES ('".$requirementno."',".$this->kclass->input['typeid'].",'".$this->kclass->input['situationid']."','".$this->kclass->input['modeid']."','".$this->kclass->input['planner']."','".$this->kclass->input['remark']."','".TIMENOW."','".$this->kclass->user['userid']."')
		");
		$requirementid=$this->kclass->DB->insertID();
		if($this->kclass->input['typeid']==2){
			$this->kclass->actionLog(array('module'=>'requirement','mid'=>$requirementid,'note'=>'新建需求计划单(库存需求)'));
			$count=count($this->kclass->input['itemMaterialid']);
			for($n=1;$n<$count+1;$n++){
				$this->kclass->input['itemQuantity'][$n]=intVal($this->kclass->input['itemQuantity'][$n]);
				if($this->kclass->input['itemMaterialid'][$n]>0 AND $this->kclass->input['itemQuantity'][$n]>0 AND $this->kclass->input['itemVersionid'][$n]>0){
					$this->kclass->input['productItemid'][$n]=$this->kclass->iif($this->kclass->input['productItemid'][$n],$this->kclass->input['productItemid'][$n],0);
					if($this->kclass->input['itemDateline'][$n]!=0){
						$sd=explode('-', $this->kclass->input['itemDateline'][$n]);
						$dateline=mktime(0,0,0,$sd[1],$sd[2],$sd[0]);
					}else{
						$dateline=0;
					}
					$materialItemid=$this->kclass->iif($this->kclass->input['materialItemid'][$n]>0,$this->kclass->input['materialItemid'][$n],0);
					$this->kclass->DB->query("
						INSERT INTO `requirementitem` (requirementid,module,materialid,versionid,materialitemid,quantity,dateline,remark,created,creator)
						VALUES ('".$requirementid."','stock','".$this->kclass->input['itemMaterialid'][$n]."','".$this->kclass->input['itemVersionid'][$n]."','".$materialItemid."','".$this->kclass->input['itemQuantity'][$n]."','".$dateline."','".$this->kclass->input['itemRemark'][$n]."','".TIMENOW."','".$this->kclass->user['userid']."')
					");
				}
			}
		}elseif($this->kclass->input['typeid']==1){	
			$this->kclass->actionLog(array('module'=>'requirement','mid'=>$requirementid,'note'=>'新建需求计划单(订单需求)'));
			$count=count($this->kclass->input['itemId']);
			for($i=1;$i<=$count;$i++){
				if($this->kclass->input['itemDateline'][$i]!=''){
					$sd=explode('-', $this->kclass->input['itemDateline'][$i]);
					$dateline=mktime(0,0,0,$sd[1],$sd[2],$sd[0]);
				}else{
					$dateline=0;
				}
				$jcount=count($this->kclass->input['itemVersionid'.$i]);
				for($j=1;$j<=$jcount;$j++){				
					if(intval($this->kclass->input['quantity'.$i][$j])>0){
						$materialItemid=$this->kclass->iif($this->kclass->input['materialItemid'.$i][$j]>0,$this->kclass->input['materialItemid'.$i][$j],0);
						$this->kclass->DB->query("
							INSERT INTO requirementitem (requirementid,materialid,versionid,materialitemid,productid,productitemid,module,mid,mitemid,reviseid,quantity,dateline,remark,created,creator)
							VALUES ('".$requirementid."','".$this->kclass->input['materialid'.$i][$j]."','".$this->kclass->input['itemVersionid'.$i][$j]."','".$materialItemid."','".$this->kclass->input['productId'][$i]."','".$this->kclass->input['productItemid'][$i]."','".$this->kclass->input['m'][$i]."','".$this->kclass->input['mid'][$i]."','".$this->kclass->input['itemId'][$i]."','".$this->kclass->input['reviseId'][$i]."','".$this->kclass->input['quantity'.$i][$j]."','".$dateline."','".$this->kclass->input['itemRemark'][$i]."','".TIMENOW."','".$this->kclass->user['userid']."')
						");
					}
				}
				if($this->kclass->input['m'][$i]=='order'){
					$this->kclass->DB->query("UPDATE `order` SET ifChooser=1 WHERE orderid='".$this->kclass->input['mid'][$i]."'");
				}elseif($this->kclass->input['m'][$i]=='sample'){
					$this->kclass->DB->query("UPDATE `sample` SET ifChooser=1 WHERE sampleid='".$this->kclass->input['mid'][$i]."'");
				}
			}
		}
		$this->kclass->messager(array(
			'title' => '新建需求计划单',
			'text' => '需求计划单 【<b>'.$requirementno.'</b>】 已新建成功!返回添加申请生产物资',
			'url' => '/s.php?module=requirement&action=view&requirementid='.$requirementid,
			'sec' => 2
		));
	}
	//
	function update(){
		$requirement=$this->kclass->DB->queryFirst("
			SELECT requirementid,requirementno,situationid,typeid,statusid,modeid,planner,remark
			FROM requirement
			WHERE killed=0 AND requirementid='".$this->kclass->input['requirementid']."'
			LIMIT 0,1
		");
		if(!$requirement OR $requirement['statusid']!=1){
			$this->kclass->messager(array(
				'title' => '修改生产',
				'text' => '选择的需求计划单数据错误',
				'url' => '/s.php?module=requirement&action=list',
				'sec' => 2
			));
		}
		$situation=$this->kclass->chooserMftSituation(array('name'=>'situationid','selectedid'=>$requirement['situationid']));
		$mode=$this->kclass->chooserMode(array('name'=>'modeid','selectedid'=>$requirement['modeid']));
		if($requirement['typeid']==1){
			// 获得当前需求计划明细物资 
			$orgItemtr='<tr><td width="15">ID</td><td width="130">订单</td><td width="80">订单交期</td><td>产品或配件<span class="red bold">*</span></td><td width="80">毛需求数量</td><td width="300">选择要生产的物资</td><td width="100">需求交期<span class="red bold">*</span></td><td width=100px>备注</td></tr>';
			$items=$this->kclass->DB->query("
				SELECT ri.itemid,ri.module,ri.mid,ri.materialid,ri.mitemid,ri.materialitemid,ri.reviseid,ri.quantity,ri.dateline,
					i.materialid AS mmaterialid,i.productid,i.productitemid,i.productAttributes,i.quantity AS mquantity,
					mv.versionid,mv.title AS version,mv.qualified,mv.disqualified
				FROM requirementitem AS ri
				LEFT JOIN item AS i ON (i.itemid=ri.mitemid)
				LEFT JOIN materialversion AS mv ON (mv.versionid=ri.versionid)
				WHERE ri.killed=0 AND i.killed=0 AND mv.killed=0 AND ri.requirementid=".$this->kclass->input['requirementid']."
				ORDER BY ri.module ASC,ri.mitemid ASC,ri.itemid ASC
			");
			if($this->kclass->DB->numRows()){
				$i=1;$oitemId=$sitemId=array();$arrangedItems=array();
				while($item=$this->kclass->DB->fetchArray($items)){
					if($item['materialitemid']==0){
						$arrangedItems['p'.$item['itemid']]=$item;
					}else{
						if($item['module']=='order'){
							if(in_array($item['mitemid'],$oitemId)){
								$arrangedItems['opi'.$item['mitemid']][$item['itemid']]=array('itemid'=>$item['itemid'],'materialid'=>$item['materialid'],'materialitemid'=>$item['materialitemid'],'versionid'=>$item['versionid'],'quantity'=>$item['quantity']);
							}else{
								$arrangedItems['opi'.$item['mitemid']]=$item;
								$arrangedItems['opi'.$item['mitemid']][$item['itemid']]=array('itemid'=>$item['itemid'],'materialid'=>$item['materialid'],'materialitemid'=>$item['materialitemid'],'versionid'=>$item['versionid'],'quantity'=>$item['quantity']);
								$oitemId[]=$item['mitemid'];
							}
						}elseif($item['module']=='sample'){
							if(in_array($item['mitemid'],$sitemId)){
								$arrangedItems['spi'.$item['mitemid']][$item['itemid']]=array('itemid'=>$item['itemid'],'materialid'=>$item['materialid'],'materialitemid'=>$item['materialitemid'],'versionid'=>$item['versionid'],'quantity'=>$item['quantity']);
							}else{
								$arrangedItems['spi'.$item['mitemid']]=$item;
								$arrangedItems['spi'.$item['mitemid']][$item['itemid']]=array('itemid'=>$item['itemid'],'materialid'=>$item['materialid'],'materialitemid'=>$item['materialitemid'],'versionid'=>$item['versionid'],'quantity'=>$item['quantity']);
								$sitemId[]=$item['mitemid'];
							}
						}
					}
				}
			}
			$mitemId=array();
			$selectedOrder=$selectedSample=array();
			if(!empty($arrangedItems)){
				foreach($arrangedItems AS $arrangedItem){
					if($arrangedItem['module']=='order'){
						$module=$this->kclass->DB->queryFirst("SELECT orderid,orderno AS no FROM `order` WHERE killed=0 AND orderid=".$arrangedItem['mid']);
						$selectedOrder[$arrangedItem['mid']]=$arrangedItem['reviseid'];
					}elseif($arrangedItem['module']=='sample'){
						$module=$this->kclass->DB->queryFirst("SELECT sampleid,sampleno AS no FROM `sample` WHERE killed=0 AND sampleid=".$arrangedItem['mid']);
						$selectedSample[$arrangedItem['mid']]=$arrangedItem['reviseid'];
					}
					$mquantity=$arrangedItem['mquantity']; // 订单需求数量
					$revise=$this->kclass->relatedRevise(array('module'=>$arrangedItem['module'],'mid'=>$arrangedItem['mid']));// 获得相关版本
					if($revise['reviseid']=='')$revise['reviseid']=0;
					// 获得产品或配件名称
					$product=$this->kclass->DB->queryFirst("SELECT productid,title,attrid FROM product WHERE killed=0 AND productid=".$arrangedItem['productid']." LIMIT 0,1");
					$standard='';
					if($arrangedItem['productAttributes']!=''){
						$valueid=explode(',',$arrangedItem['productAttributes']);
						foreach($valueid as $val){
							$attribute=$this->kclass->DB->queryFirst("
								SELECT av.valueid,av.title AS value,
									an.nameid,an.title AS name
								FROM attributevalue AS av
								LEFT JOIN attributename AS an ON (an.nameid=av.nameid)
								WHERE an.killed=0 AND av.killed=0 AND av.valueid='".$val."'
								ORDER BY an.ordering DESC
							");
							if($attribute['name'] AND $attribute['value']){
								$standard.=$attribute['name'].'：'.'<span class="darkred">'.$attribute['value'].'</span>　';
							}
						}
					}else{
						if($product['attrid']==5){
							$product=$this->kclass->DB->queryFirst("
								SELECT pi.quantity,p.productid,p.title
								FROM partitem AS pi 
								LEFT JOIN product AS p ON (p.productid=pi.childid)
								WHERE pi.killed=0 AND p.killed=0 AND p.materialid=".$arrangedItem['materialid']."
								LIMIT 0,1
							");
							$mquantity=$mquantity*$product['quantity'];
						}
					}
					if($arrangedItem['materialitemid']==0){
						$orgItemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').' small">
							<td><input type="hidden" name="oldmodule['.$i.']" value="'.$arrangedItem['module'].'">
							<input type="hidden" name="oldMid['.$i.']" value="'.$arrangedItem['mid'].'">
							<input type="hidden" name="oldOrderItemId['.$i.']" value="'.$arrangedItem['mitemid'].'">
							<input type="hidden" name="oldReviseId['.$i.']" value="'.$revise['reviseid'].'">
							<input type="hidden" name="oldProductItemid['.$i.']" value="'.$arrangedItem['productitemid'].'" />
							<input type="hidden" name="oldTotalQuantity['.$i.']" value="'.$mquantity.'">'.$i.'</td>
							<td><a href="/s.php?module='.$arrangedItem['module'].'&action=view&'.$arrangedItem['module'].'id='.$arrangedItem['mid'].'">'.$module['no'].'</a> '.$revise['currencyRevise'].'</td>
							<td>'.date('Y-m-d',$arrangedItem['dateline']).'</td>
							<td><a href="/s.php?module=product&action=view&productid='.$product['productid'].'">'.$product['title'].'</a>　'.$standard.'</td>
							<td>'.$mquantity.' '.$arrangedItem['unit'].'</td>
							<td>'.$this->_getMaterial(array('materialid'=>$arrangedItem['materialid'],'line'=>$i,'productitemid'=>$arrangedItem['productitemid'],'quantity'=>$arrangedItem['quantity'],'selected'=>$arrangedItem)).'</td>
							<td><input type="text" name="oldDateline['.$i.']" id="oldDteline'.$i.'" size="15" value="'.date('Y-m-d',$arrangedItem['dateline']).'"></td>
							<td><input type="text" name="oldRemark['.$i.']" size="25" value="'.$arrangedItem['remark'].'"></td>
						</tr>';
					}else{
						if(!in_array($arrangedItem['mitemid'],$mitemId)){
							$orgItemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').' small">
							<td><input type="hidden" name="oldmodule['.$i.']" value="'.$arrangedItem['module'].'">
							<input type="hidden" name="oldMid['.$i.']" value="'.$arrangedItem['mid'].'">
							<input type="hidden" name="oldOrderItemId['.$i.']" value="'.$arrangedItem['mitemid'].'">
							<input type="hidden" name="oldReviseId['.$i.']" value="'.$revise['reviseid'].'">
							<input type="hidden" name="oldProductItemid['.$i.']" value="'.$arrangedItem['productitemid'].'" />
							<input type="hidden" name="oldTotalQuantity['.$i.']" value="'.$mquantity.'">'.$i.'</td>
							<td><a href="/s.php?module='.$arrangedItem['module'].'&action=view&'.$arrangedItem['module'].'id='.$arrangedItem['mid'].'">'.$module['no'].'</a> '.$revise['currencyRevise'].'</td>
							<td>'.date('Y-m-d',$arrangedItem['dateline']).'</td>
							<td><a href="/s.php?module=product&action=view&productid='.$product['productid'].'">'.$product['title'].'</a>　'.$standard.'</td>
							<td>'.$mquantity.' '.$arrangedItem['unit'].'</td>
							<td>';
						}
						$materialItems=$this->kclass->DB->query("
							SELECT mv.versionid,mv.title AS version,mv.materialid,mv.quantity,mv.qualified,mv.disqualified,
								mi.itemid,mi.itemno,mi.attributevalue,
								m.materialid,m.title AS material
							FROM materialversion AS mv
							LEFT JOIN materialitem AS mi ON (mi.itemid=mv.itemid)
							LEFT JOIN material AS m ON (m.materialid=mv.materialid)
							WHERE mv.killed=0 AND mi.killed=0 AND mi.productitemid=".$arrangedItem['productitemid']."
							ORDER BY mv.ifProduce DESC,mv.produceCount DESC
						");
						if($this->kclass->DB->numRows()){
							$t=1;$materialitemId[]=$arrangedItem['materialitemid'];
							$itemArrs=array();
							while($materialItem=$this->kclass->DB->fetchArray($materialItems)){
								$materialInfo=$this->kclass->getMaterial(array('materialid'=>$materialItem['materialid'],'itemid'=>$materialItem['itemid']));
								foreach($arrangedItem AS $versionArr){
									if(is_array($versionArr) AND $versionArr['versionid']==$materialItem['versionid']){
										$orgItemtr.='<input type="hidden" name="itemId'.$i.'['.$t.']" value="'.$versionArr['itemid'].'" ><input type="hidden" name="oldMaterialid'.$i.'['.$t.']" value="'.$versionArr['materialid'].'" /><input type="hidden" name="oldMaterialItemid'.$i.'['.$t.']" value="'.$versionArr['materialitemid'].'" /><input type="hidden" name="oldVersionid'.$i.'['.$t.']" value="'.$materialItem['versionid'].'" /><a title="'.$materialInfo['material'].'　'.$materialInfo['standard'].'" class="tip" href="'.$materialInfo['url'].'">'.$materialInfo['no'].'</a>　版本：<a href="#" class="tip" title="良品：'.$materialItem['qualified'].'　不良品：'.$materialItem['disqualified'].'">'.$materialItem['version'].'</a>　需求数：<input type="text" name="oldQuantity'.$i.'['.$t.']" value="'.$versionArr['quantity'].'" size=7 /><br/>';
										$itemArrs[]=$versionArr['versionid'];
									}
								}
								if(!in_array($materialItem['versionid'],$itemArrs)){
									$orgItemtr.='<input type="hidden" name="oldMaterialid'.$i.'['.$t.']" value="'.$materialItem['materialid'].'" /><input type="hidden" name="oldMaterialItemid'.$i.'['.$t.']" value="'.$materialItem['itemid'].'" /><input type="hidden" name="oldVersionid'.$i.'['.$t.']" value="'.$materialItem['versionid'].'" /><a title="'.$materialInfo['material'].'　'.$materialInfo['standard'].'" class="tip" href="'.$materialInfo['url'].'">'.$materialInfo['no'].'</a>　版本：<a href="#" class="tip" title="良品：'.$materialItem['qualified'].'　不良品：'.$materialItem['disqualified'].'">'.$materialItem['version'].'</a>　需求数：<input type="text" name="oldQuantity'.$i.'['.$t.']" value="0" size=7 /><br/>';
								}
								$t++;
							}
						}
						if(!in_array($arrangedItem['mitemid'],$mitemId)){
							$orgItemtr.='</td><td><input type="text" name="oldDateline['.$i.']" id="oldDteline'.$i.'" size="15" value="'.date('Y-m-d',$arrangedItem['dateline']).'"></td><td><input type="text" name="oldRemark['.$i.']" size="25" value="'.$arrangedItem['remark'].'"></td>
						</tr>';
							$mitemId[]=$arrangedItem['mitemid'];
						}
					}

					$id.='#oldDteline'.$i.',';
					$i++;
				}
			}
			
			$orgItemtr.='</tbody></table>';
			$selecteds=array('order'=>array_unique($selectedOrder),'sample'=>array_unique($selectedSample));
			// 已被选中的订单或 样品单
			if(is_array($selecteds)){
				$n=1;
				$orgItemtr.='<table class="hundred"><thead><tr><th colspan=14>当前需求计划关联的 订单(PO)</th></tr></thead><tbody><tr class="center even" nohover><td width="15">ID</td><td width="180">订单编号</td><td>订单名称</td><td>业务人员</td><td>跟单人员</td><td>交货日期</td><td width="30">删</td></tr>';
				foreach($selecteds as $key=>$value){
					foreach($value AS $k=>$v){
						$selected='';
						if($key=='order'){
							$old=$this->kclass->DB->queryFirst("
								SELECT o.orderno AS no,o.ordertitle AS title,o.shipmentDate AS dateline,
									m.realname AS director,
									me.realname AS tracker
								FROM `order` AS o
								LEFT JOIN member AS m ON (m.userid=o.director)
								LEFT JOIN member AS me ON (me.userid=o.tracker)
								WHERE o.killed=0 AND o.orderid=".$k."
								LIMIT 0,1
							");
							$orderRevise=$this->kclass->relatedRevise(array('module'=>'order','mid'=>$k));
							$orderVersion='(R'.($old['version']+1).')</span>';
							$selected=$this->kclass->iif($v==$orderRevise['reviseid'],'<a href="/s.php?module=order&action=view&orderid='.$k.'">'.$old['no'].'</a> <span class="small green">','<a href="/s.php?module=order&action=revise&reviseid='.$orderRevise['reviseid'].'">'.$old['no'].'</a>').'<span class="small darkred">'.$orderVersion.'</span>';
						}elseif($key=='sample'){
							$old=$this->kclass->DB->queryFirst("
								SELECT s.sampleno AS no,s.title,s.deliveryDate AS dateline,
									m.realname AS director,
									me.realname AS tracker
								FROM `sample` AS s
								LEFT JOIN member AS m ON (m.userid=s.director)
								LEFT JOIN member AS me ON (me.userid=s.tracker)
								WHERE s.killed=0 AND s.sampleid=".$k."
								LIMIT 0,1
							");
							$orderRevise=$this->kclass->relatedRevise(array('module'=>'sample','mid'=>$k));
							$orderVersion='(R'.($old['version']+1).')</span>';
							$selected=$this->kclass->iif($v==$orderRevise['reviseid'],'<a href="/s.php?module=sample&action=view&sampleid='.$k.'">'.$old['no'].'</a> <span class="small green">','<a href="/s.php?module=sample&action=revise&reviseid='.$orderRevise['reviseid'].'">'.$old['no'].'</a>').'<span class="small darkred">'.$orderVersion.'</span>';
							
						}
						$orgItemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').'">
							<td><input type="hidden" name="selectedModule['.$n.']" value="'.$key.'">
							<input type="hidden" name="selectedMid['.$n.']" value="'.$k.'">
							<input type="hidden" name="selectedReviseid['.$n.']" value="'.$v.'">'.$n.'</td>
							<td>'.$selected.'</td>
							<td>'.$old['title'].'</td>
							<td>'.$old['director'].'</td>
							<td>'.$old['tracker'].'</td>
							<td>'.date('Y-m-d',$old['dateline']).'</td>
							<td><input type="checkbox" name="orderKill['.$n.']" value="'.$k.'"/></td>
						</tr>';
						$n++;
					}
				}
				$orgItemtr.='</tbody></table>';
			}
			//未投产订单（PO）
			$j=1;
			$orders=$this->kclass->DB->query("SELECT * FROM `order` WHERE killed=0 AND ifApprove=1 AND type='PO' AND ifChooser=0 ORDER BY created DESC");
			if($this->kclass->DB->numRows()){
				$i=1;
				while($order=$this->kclass->DB->fetchArray($orders)){
					$revise=$this->kclass->relatedRevise(array('module'=>'order','mid'=>$order['orderid']));
					$itemtr.='<tr class="bold "><td colspan=15><input type="hidden" name="slectedMid['.$j.']" value="'.$order['orderid'].'"><input type="hidden" name="selectm['.$j.']" value="order">['.$j.']<span class="normal">订单(PO)编号：</span><a class="middle bold" href="/s.php?module=order&action=view&orderid='.$order['orderid'].'">'.$order['orderno'].'</a>'.$revise['currencyRevise'].'　<span class="normal">创建日期：</span>'.date('Y-m-d',$order['created']).'　<span class="normal">交货日期：</span>'.date('Y-m-d',$order['shipmentDate']).'<span class="right">勾选<input type="checkbox" name="selectid['.$j.']" value="'.$order['orderid'].'"></span></td></tr><tr class="center even" nohover><td width="15">ID</td><td width="130">订单编号</td><td width="130">需求时间</td><td>产品或配件</td><td width="80">订单数量</td><td width="300">选择要生产的物资 <span class="red bold">*</span></td><td width="80">需求交期 <span class="red bold">*</span></td><td width="100">备注</td></tr>';
					$itemtr.=$this->_formItem(array('module'=>'order','mid'=>$order['orderid']));
					$j++;
				}
			}
			if(!$itemtr)$itemtr.='<tr class="darkred center"><td colspan="10">　暂无可用的 订单(PO) 明细！　</td></tr>';
			$itemtr='<tr class="center"><th colspan="10">请选择 <span class="darkred">订单(PO)</span> 明细　</th></tr>'.$itemtr.'<tr class="center"><th colspan="10">请选择 <span class="darkred">样品单</span> 明细　</th></tr>';
			//未投产样品单
			$samples=$this->kclass->DB->query("
				SELECT s.*,
					st.title AS sampletype 
				FROM `sample` AS s
				LEFT JOIN `sampletype` AS st ON (st.typeid=s.typeid)
				WHERE s.killed=0 AND s.ifApprove=1 AND s.ifChooser=0 
				ORDER BY s.created DESC
			");
			if($this->kclass->DB->numRows()){
				$i=1;
				while($sample=$this->kclass->DB->fetchArray($samples)){
					$samplerevise=$this->kclass->relatedRevise(array('module'=>'sample','mid'=>$sample['sampleid']));
					$sampleitemtr.='<tr class="bold "><td colspan=15><input type="hidden" name="slectMid['.$j.']" value="'.$sample['sampleid'].'"><input type="hidden" name="selectm['.$j.']" value="sample">['.$j.']<span class="normal">样品单编号：</span><a class="middle bold" href="/s.php?module=sample&action=view&sampleid='.$sample['sampleid'].'">'.$sample['sampleno'].'</a>'.$samplerevise['currencyRevise'].'　<span class="normal">类型：</span>'.$sample['sampletype'].'　<span class="normal">创建日期：</span>'.date('Y-m-d',$sample['created']).'　<span class="normal">交货日期：</span>'.date('Y-m-d',$sample['deliveryDate']).'<span class="right">勾选<input type="checkbox" name="selectid['.$j.']" value="'.$sample['sampleid'].'"></span></td></tr><tr class="center even" nohover><td width="15">ID</td><td width="130">样品单编号</td><td width="130">需求时间</td><td>产品或配件</td><td>订单数量</td><td width="250">选择要生产的物资 <span class="red bold">*</span></td><td width="80">需求交期 <span class="red bold">*</span></td><td>生产备注</td></tr>';
					$sampleitemtr.=$this->_formItem(array('module'=>'sample','mid'=>$sample['sampleid']));
					$j++;
				}
			}
			if(!$sampleitemtr)$itemtr.='<tr class="darkred center"><td colspan="10">　暂无可用的 样品单 明细！　</td></tr>';
			$itemtr.=$sampleitemtr;
			/*<-- // baoliu
			'<table class="hundred">
<thead>
<tr>
<th colspan="11">选择需求计划明细</th>
</tr>
<tr><th width="30">ID</th><th>产品或配件<span class="red bold">*</span></th><th>版本<span class="red bold">*</span></th><th>毛需求数量<span class="red bold">*</span></th><th>需求交期<span class="red bold">*</span></th><th>备注</th></tr>
</thead>
<tbody class="small">
{$itemtr}
</tbody>
</table>';---->*/
$body=<<<EOF
<form action="/s.php?module=requirement&action=doupdate" method="post">
<input type="hidden" name="module" value="requirement" />
<input type="hidden" name="action" value="doupdate" />
<input type="hidden" name="requirementid" value="{$this->kclass->input['requirementid']}" />
<input type="hidden" name="type" value="1" />
<table class="hundred">
<thead><tr><th colspan="6">修改需求计划单 <span class="small darkred">(订单需求)</span></th></tr></thead>
<tbody>
<tr class="odd">
<td width="100">编号：</td><td width="375">{$requirement['requirementno']}<span class="small gray">(编号不会被修改)</span></td>
<td width="100">紧急情况：</td><td width="375">{$situation}</td>
<td width="100">计划员：</td><td width="375"><input type="text" name="planner" value="{$requirement['planner']}"></td>
</tr>
<tr class="even">
<td>需求计算方式：</td><td>{$mode}</td>
<td width="100">备注：</td><td><textarea type="text" name="remark" style="width:300px;height:80px;">{$requirement['remark']}</textarea></td>
<td></td><td></td>
</tr>
</tbody>
</table>
<table class="hundred"><thead><tr><th colspan=14>当前需求计划单明细</th></tr></thead>
<tbody>
{$orgItemtr}
<table class="hundred">
{$itemtr}
<tr class="center">
<td colspan=13><input type="submit" value="提交"><input type="reset" value="重置"></td>
</tr>
</tbody></table>
</form>
EOF;
			$this->kclass->tbline+=4;
			$this->kclass->page['title'].='选择需求计划单明细';
			$this->kclass->page['onload'].='var dates=$(\''.$id.'\').datepicker({onSelect:function(selectedDate){instance=$(this).data(\'datepicker\'),date=$.datepicker.parseDate(instance.settings.dateFormat || $.datepicker._defaults.dateFormat,selectedDate,instance.settings);}});dc.tabhover();dc.tips();';
			$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - <a href="/s.php?module=requirement&action=view&requirementid='.$this->kclass->input['requirementid'].'">'.$requirement['requirementno'].'</a> - 修改需求计划单', 'right'=>'<a href="/s.php?module=requirement&action=update">返回列表</a>　|　<a href="/s.php?module=requirement&action=view&requirementid='.$this->kclass->input['requirementid'].'">查看需求计划单</a>　','body'=>$body));
		}elseif($requirement['typeid']==2){
			$items=$this->kclass->DB->query("
				SELECT ri.itemid,ri.module,ri.mid,ri.materialid,ri.mitemid,ri.materialitemid,ri.reviseid,ri.quantity,ri.dateline,
					mv.versionid,mv.title AS version,mv.qualified,mv.disqualified
				FROM requirementitem AS ri
				LEFT JOIN materialversion AS mv ON (mv.versionid=ri.versionid)
				WHERE ri.killed=0 AND mv.killed=0 AND ri.requirementid=".$this->kclass->input['requirementid']."
				ORDER BY ri.module ASC,ri.itemid ASC
			");
			$k=1;
			if($this->kclass->DB->numRows()){
				while($item=$this->kclass->DB->fetchArray($items)){
					$materialInfo=$this->kclass->getMaterial(array('materialid'=>$item['materialid'],'itemid'=>$item['materialitemid']));
					$itemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').'">
						<td><input type="hidden" name="itemId['.$k.']" value="'.$item['itemid'].'" />'.$k.'</td>
						<td>'.$this->kclass->chooserMaterial(array('hName'=>'itemMaterialid['.$k.']','hId'=>'itemMaterialid'.$k,'name'=>'itemMaterial['.$k.']','id'=>'itemMaterial'.$k,'width'=>700,'line'=>$k,'showVersion'=>1,'selectItemid'=>$item['materialitemid'],'selectedid'=>$item['materialid'],'value'=>$materialInfo['no'].'　'.$materialInfo['material'].'　'.$materialInfo['standard'])).'</td>
						<td>'.$this->kclass->chooserMaterialVersion(array('name'=>'itemVersionid['.$k.']','selectedid'=>$item['versionid'],'width' =>250,'id'=>'itemVersionid'.$k,'materialid'=>$item['materialid'],'itemid'=>$item['materialitemid'])).'</td>
						<td><input type="text" name="itemQuantity['.$k.']" size="10" value="'.$item['quantity'].'"></td>
						<td><input type="text" name="itemDateline['.$k.']" id="dateline'.$k.'" size="15" value="'.date('Y-m-d',$item['dateline']).'"></td>
						<td><input type="text" name="itemRemark['.$k.']" size="25" value="'.$item['remark'].'"></td>
						<td><input type="checkbox" name="itemKill['.$k.']" value="'.$item['itemid'].'"></td>
					</tr>';
					$id.='#dateline'.$k.',';
					$k++;
				}
				$itemtr.='<tr><td colspan=7>新增明细</td></tr>';
			}
			for($i=$k; $i<$k+5; $i++){
				$material=$this->kclass->chooserMaterial(array('hName'=>'itemMaterialid['.$i.']','hId'=>'itemMaterialid'.$i,'name'=>'itemMaterial['.$i.']','id'=>'itemMaterial'.$i,'width'=>700,'line'=>$i,'showVersion'=>1));
				$itemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').'">
					<td>'.$i.'</td>
					<td>'.$material.'</td>
					<td><select name="itemVersionid['.$i.']" id="itemVersionid'.$i.'" style="width:250px;"></select></td>
					<td><input type="text" name="itemQuantity['.$i.']" size="10"></td>
					<td><input type="text" name="itemDateline['.$i.']" id="dateline'.$i.'" size="15"></td>
					<td><input type="text" name="itemRemark['.$i.']" size="25"></td>
					<td></td>
				</tr>';
				if($i<$k+4){
					$id.='#dateline'.$i.',';
				}else{
					$id.='#dateline'.$i;
				}
			}
			$body=<<<EOF
<form action="/s.php?module=requirement&action=doupdate" name="requirement" method="post">
<input type="hidden" name="module" value="requirement" />
<input type="hidden" name="action" value="doupdate" />
<input type="hidden" name="requirementid" value="{$this->kclass->input['requirementid']}" />
<input type="hidden" name="type" value="2" />
<table class="hundred">
<thead><tr><th colspan="6">修改需求计划单 <span class="small darkred">(库存需求)</span></th></tr></thead>
<tbody>
<tr class="odd">
<td width="100">编　　号：<span class="red bold">*</span></td><td width="375">{$requirement['requirementno']}<span class="small gray">(编号不会被修改)</span></td>
<td width="100">紧急情况：<span class="red bold">*</span></td><td width="375">{$situation}</td>
<td width="100">计 划 员：<span class="red bold">*</span></td><td><input type="text" name="planner" value="{$requirement['planner']}"></td>
</tr>
<tr class="even">
<td>计算方式：<span class="red bold">*</span></td><td>{$mode}</td>
<td width="100">备　　注：</td><td><textarea type="text" name="remark" style="width:300px;height:80px;">{$requirement['remark']}</textarea></td>
<td></td><td></td>
</tr>
</tbody>
</table>
<table class="hundred"><thead><tr><th colspan=14>当前需求计划单明细</th></tr></thead>
<tbody>
<tr><td width="30">ID</td><td>产品或配件<span class="red bold">*</span></td><td>版本<span class="red bold">*</span></td><td>毛需求数量<span class="red bold">*</span></td><td>需求交期<span class="red bold">*</span></td><td>备注</td><td>删</td></tr>
{$itemtr}
</tbody></table>
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
				$this->kclass->page['title'].='修改需求计划单';
				$this->kclass->page['onload'].='var dates=$(\''.$id.'\').datepicker({onSelect:function(selectedDate){instance=$(this).data(\'datepicker\'),date=$.datepicker.parseDate(instance.settings.dateFormat || $.datepicker._defaults.dateFormat,selectedDate,instance.settings);}});dc.tabhover();dc.tips()';
				$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - <a href="/s.php?module=requirement&action=view&requirementid='.$this->kclass->input['requirementid'].'">查看需求计划单</a> - 修改需求计划单', 'right'=>'<a href="/s.php?module=requirement&action=update">返回列表</a>　|　<a href="/s.php?module=requirement&action=view&requirementid='.$this->kclass->input['requirementid'].'">查看需求计划单</a>　','body'=>$body));
			}
		}
		//
		function doupdate(){
			if($this->kclass->input['requestMethod'] != 'post'){
				$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
			}else{
				$requirement=$this->kclass->DB->queryFirst("SELECT requirementid,requirementno,statusid FROM `requirement` WHERE requirementid='".$this->kclass->input['requirementid']."'");
				if(!$requirement){
					$e .= '<li>您要编辑的需求计划单并不存在，请返回列表刷新后再操作。</li>';
				}
				if($requirement['statusid']>1){
					$e .= '<li>您要编辑的需求计划单已经[锁定]，无法进行修改。</li>';
				}
				if(!$this->kclass->input['planner']){
					$e .= '<li>请选择需求计划单中的 计划员。</li>';
				}
				if($this->kclass->input['situationid']==''){
					$e.='<li>请填写需求计划单中的 紧急情况</li>';
				}
				if($this->kclass->input['modeid']==''){
					$e.='<li>请填写需求计划单中的 需求计算方式</li>';
				}
				
				if($this->kclass->input['type']==1){
					$count=count($this->kclass->input['oldOrderItemId']);
					for($m=1;$m<=$count;$m++){
						if($this->kclass->input['oldDateline'][$m]==''){
							$e.='<li>请填写需求计划单[原]生产明细[ID:'.$m.']中的 需求交期</li>';
						}
						$jcount=count($this->kclass->input['oldVersionid'.$m]);
						$ifnull=0;$quantity=0;
						for($j=1;$j<=$jcount;$j++){
							if($this->kclass->input['oldQuantity'.$m][$j]>0){
								$ifnull=1;
								$quantity+=$this->kclass->input['oldQuantity'.$m][$j];
							}
						}
						$item=$this->kclass->DB->queryFirst("SELECT p.attrid FROM item AS i LEFT JOIN product AS p ON (p.productid=i.productid) WHERE i.killed=0 AND i.itemid='".$this->kclass->input['orderItemId'][$m]."'");
						if($ifnull==0){
							$e.='<li>请填写至少一个需求计划单中订单(PO)的生产明细[ID:'.$m.']的 版本 需求数量</li>';
						}elseif($quantity!=$this->kclass->input['oldTotalQuantity'][$m] AND $item['attrid']!=5){
							$e.='<li>需求计划单中订单(PO)的生产明细[ID:'.$m.'] 各版本的需求数量之和 必须和订单数量相等</li>';
						}
					}
					$killCount=count($this->kclass->input['orderKill']);
					$selectCount=count($this->kclass->input['selectid']);
					if(!empty($this->kclass->input['orderKill']) AND count(array_filter($this->kclass->input['orderKill']))==$killCount){
						if($selectCount>0 AND empty($this->kclass->input['selectid'])){
							$e.='<li>请在勾选了删除订单后，再勾选要选择添加的订单。</li>';
						}
					}
				}elseif($this->kclass->input['type']==2){
					$count=count($this->kclass->input['itemMaterialid']);
					for($n=1;$n<$count+1;$n++){
						if($this->kclass->input['itemMaterialid'][$n]>0){
							if($this->kclass->input['itemVersionid'][$n]==''){
								$e.='<li>请填写需求计划单[新]生产明细[ID:'.$n.']的 版本</li>';
							}
							if($this->kclass->input['itemQuantity'][$n]==''){
								$e.='<li>请填写需求计划单[新]生产明细[ID:'.$n.']的 毛需求数量</li>';
							}
							if($this->kclass->input['itemDateline'][$n]==''){
								$e.='<li>请填写需求计划单[新]生产明细[ID:'.$n.']的 需求交期</li>';
							}
						}
					}
				}
			}
			if($e){
				$this->kclass->messager(array(
					'title' => '修改需求计划单',
					'text' => '您在修改需求计划单的过程中有以下错误：<ul>'.$e.'</ul>',
					'url' => 'javascript:history.back()',
					'sec' => 3
				));
			}
			// update requirement
			$this->kclass->DB->query("
				UPDATE requirement SET
					planner='".$this->kclass->input['planner']."',
					situationid='".$this->kclass->input['situationid']."',
					modeid='".$this->kclass->input['modeid']."',
					remark='".$this->kclass->input['remark']."',
					modified='".TIMENOW."',
					modifier='".$this->kclass->user['userid']."'
				WHERE requirementid='".$requirement['requirementid']."'
			");
			$this->kclass->actionLog(array('module'=>'requirement','mid'=>$requirement['requirementid'],'note'=>'修改了需求计划单'));
			if($this->kclass->input['type']==1){
				// 当前明细的修改或新增
				$count=count($this->kclass->input['oldOrderItemId']);
				for($m=1;$m<=$count;$m++){
					if($this->kclass->input['oldDateline'][$m]){
						$sd=explode('-',$this->kclass->input['oldDateline'][$m]);
						$dateline=mktime(0,0,0,$sd[1],$sd[2],$sd[0]);
					}else{
						$dateline=0;
					}
					$versionCount=count($this->kclass->input['oldVersionid'.$m]);
					for($j=1;$j<=$versionCount;$j++){ // 对具体生产的物资进行操作
						if($this->kclass->input['itemId'.$m][$j]>0){
							if($this->kclass->input['oldQuantity'.$m][$j]==0){ // 当前明细的数量变为0的时候， 删除
								$this->kclass->DB->query("UPDATE requirementitem SET killed='".TIMENOW."',killer='".$this->kclass->user['userid']."' WHERE itemid='".$this->kclass->input['itemId'.$m][$j]."'");
							}else{ // 不为0 的时候 进行修改
								$this->kclass->DB->query("
									UPDATE requirementitem SET
										quantity='".$this->kclass->input['oldQuantity'.$m][$j]."',
										remark='".$this->kclass->input['oldRemark'][$m]."',
										dateline='".$dateline."',
										modified='".TIMENOW."',
										modifier='".$this->kclass->user['userid']."'
									WHERE itemid='".$this->kclass->input['itemId'.$m][$j]."'
								");
							}
						}else{
							if($this->kclass->input['oldQuantity'.$m][$j]>0){
								$materialItemid=$this->kclass->iif($this->kclass->input['oldMaterialItemid'.$m][$j]>0,$this->kclass->input['oldMaterialItemid'.$m][$j],0);
								$this->kclass->DB->query("
									INSERT INTO requirementitem
										(requirementid,materialid,versionid,materialitemid,module,mid,mitemid,reviseid,quantity,dateline,remark,created,creator)
									VALUES
										('".$requirement['requirementid']."','".$this->kclass->input['oldMaterialid'.$m][$j]."','".$this->kclass->input['oldVersionid'.$m][$j]."','".$materialItemid."','".$this->kclass->input['oldmodule'][$m]."','".$this->kclass->input['oldMid'][$m]."','".$this->kclass->input['oldOrderItemId'][$m]."','".$this->kclass->input['oldReviseId'][$m]."','".$this->kclass->input['oldQuantity'.$m][$j]."','".$dateline."','".$this->kclass->input['oldRemark'][$m]."','".TIMENOW."','".$this->kclass->user['userid']."')
								");
							}
						}
					}
				}

				// 当前已经被关联的订单 或 样品单
				$oldCount=count($this->kclass->input['selectedModule']);
				for($s=1;$s<=$oldCount;$s++){
					if($this->kclass->input['orderKill'][$s]>0 AND $this->kclass->input['orderKill'][$s]==$this->kclass->input['selectedMid'][$s]){
						$this->kclass->DB->query("UPDATE `requirementitem` SET killed=".TIMENOW.",killer='".$this->kclass->user['userid']."'  WHERE module='".$this->kclass->input['selectedModule'][$s]."' AND mid='".$this->kclass->input['orderKill'][$s]."' AND reviseid='".$this->kclass->input['selectedReviseid'][$s]."'");
						$revise=$this->kclass->relatedRevise(array('module'=>$this->kclass->input['selectedModule'][$s],'mid'=>$this->kclass->input['orderKill'][$s]));
						if($revise['reviseid']=='')$revise['reviseid']=0;
						if($revise['reviseid']==$this->kclass->input['selectedReviseid'][$s]){
							if($this->kclass->input['selectedModule'][$s]=='order'){
								$this->kclass->DB->query("UPDATE `order` SET ifChooser=0  WHERE orderid='".$this->kclass->input['orderKill'][$s]."'");
							}elseif($this->kclass->input['selectedModule'][$s]=='sample'){
								$this->kclass->DB->query("UPDATE `order` SET ifChooser=0  WHERE orderid='".$this->kclass->input['orderKill'][$s]."'");
							}
						}
					}
				}
				// 新增其他的订单或者样品单作为计划
				$selectedCount=count($this->kclass->input['slectedMid']);
				for($t=1;$t<=$selectedCount;$t++){
					$count=count($this->kclass->input['itemId']);
					for($i=1;$i<=$count;$i++){
						if($this->kclass->input['selectid'][$t]==$this->kclass->input['mid'][$i] AND$this->kclass->input['selectm'][$t]==$this->kclass->input['m'][$i]){
							if($this->kclass->input['itemDateline'][$i]!=''){
								$sd=explode('-', $this->kclass->input['itemDateline'][$i]);
								$dateline=mktime(0,0,0,$sd[1],$sd[2],$sd[0]);
							}else{
								$dateline=0;
							}
							$jcount=count($this->kclass->input['itemVersionid'.$i]);
							for($j=1;$j<=$jcount;$j++){
								if(intval($this->kclass->input['quantity'.$i][$j])>0){
									$materialItemid=$this->kclass->iif($this->kclass->input['materialItemid'.$i][$j]>0,$this->kclass->input['materialItemid'.$i][$j],0);
									$this->kclass->DB->query("
										INSERT INTO requirementitem (requirementid,materialid,versionid,materialitemid,productid,productitemid,module,mid,mitemid,reviseid,quantity,dateline,remark,created,creator)
										VALUES ('".$requirement['requirementid']."','".$this->kclass->input['materialid'.$i][$j]."','".$this->kclass->input['itemVersionid'.$i][$j]."','".$materialItemid."','".$this->kclass->input['productId'][$i]."','".$this->kclass->input['productItemid'][$i]."','".$this->kclass->input['m'][$i]."','".$this->kclass->input['mid'][$i]."','".$this->kclass->input['itemId'][$i]."','".$this->kclass->input['reviseId'][$i]."','".$this->kclass->input['quantity'.$i][$j]."','".$dateline."','".$this->kclass->input['itemRemark'][$i]."','".TIMENOW."','".$this->kclass->user['userid']."')
									");
								}
							}
						}
					}
					if($this->kclass->input['selectm'][$t]=='order'){
						$this->kclass->DB->query("UPDATE `order` SET ifChooser=1 WHERE orderid='".$this->kclass->input['selectid'][$t]."'");
					}elseif($this->kclass->input['selectm'][$t]=='sample'){
						$this->kclass->DB->query("UPDATE `sample` SET ifChooser=1 WHERE sampleid='".$this->kclass->input['selectid'][$t]."'");
					}
				}
			}elseif($this->kclass->input['type']==2){
				$count=count($this->kclass->input['itemMaterialid']);
				for($n=1;$n<=$count;$n++){
					$this->kclass->input['itemQuantity'][$n]=intval($this->kclass->input['itemQuantity'][$n]);
					if($this->kclass->input['itemMaterialid'][$n]>0 AND $this->kclass->input['itemQuantity'][$n]>0 AND $this->kclass->input['itemVersionid'][$n]>0 AND $this->kclass->input['itemDateline'][$n]!=''){
						$sd=explode('-', $this->kclass->input['itemDateline'][$n]);
						$dateline=mktime(0,0,0,$sd[1],$sd[2],$sd[0]);
						$materialItemid=$this->kclass->iif($this->kclass->input['materialItemid'][$n],$this->kclass->input['materialItemid'][$n],0);
						if($this->kclass->input['itemId'][$n]>0){
							if($this->kclass->input['itemKill'][$n]>0 AND $this->kclass->input['itemKill'][$n]==$this->kclass->input['itemId'][$n]){
								$this->kclass->DB->query("UPDATE requirementitem SET killed='".TIMENOW."',killer='".$this->kclass->user['userid']."' WHERE itemid='".$this->kclass->input['itemKill'][$n]."'");
							}else{
								$this->kclass->DB->query("
									UPDATE requirementitem SET 
										materialid='".$this->kclass->input['itemMaterialid'][$n]."',
										versionid='".$this->kclass->input['itemVersionid'][$n]."',
										materialitemid='".$materialItemid."',
										quantity='".$this->kclass->input['itemQuantity'][$n]."',
										dateline='".$dateline."',
										remark='".$this->kclass->input['itemRemark'][$n]."',
										modified='".TIMENOW."',
										modifier='".$this->kclass->user['userid']."'
									WHERE itemid='".$this->kclass->input['itemId'][$n]."'
								");
							}
						}else{
							$this->kclass->DB->query("
								INSERT INTO `requirementitem` (requirementid,module,materialid,versionid,materialitemid,quantity,dateline,remark,created,creator)
								VALUES ('".$requirement['requirementid']."','stock','".$this->kclass->input['itemMaterialid'][$n]."','".$this->kclass->input['itemVersionid'][$n]."','".$materialItemid."','".$this->kclass->input['itemQuantity'][$n]."','".$dateline."','".$this->kclass->input['itemRemark'][$n]."','".TIMENOW."','".$this->kclass->user['userid']."')
							");
						}
					}
				}
			}
			$this->kclass->messager(array(
				'title' => '修改需求计划单',
				'text' => '需求计划单 【<b>'.$requirement['requirementno'].'</b>】 已修改成功!返回修改需求计划单',
				'url' => '/s.php?module=requirement&action=view&requirementid='.$requirement['requirementid'],
				'sec' => 2
			));
		}
		//查看MRP所有明细 后续开发需加入简单分页和查找功能 --gaowenfei 2013/11/25 8:49
	function viewMRP(){
		$orderby=$this->kclass->orderby(array('module'=>'requirement','action'=>'viewMRP','direction'=>'desc','orderby'=>'`requirement`.modified', 'default'=>'requirementid', 'serial'=>array(array('title'=>'编号', 'field'=>'requirementno'), array('title'=>'ID', 'field'=>'requirementid'), array('title'=>'修改时间', 'field'=>'modified'), array('title'=>'建立时间', 'field'=>'created')),'appendUrl'=>$query));
		$requirements=$this->kclass->DB->query("
			SELECT `requirement`.*,
					ms.title AS status,
					mt.title AS type,
				u.username AS creator,
				us.username AS modifier
			FROM `requirement` 
			LEFT JOIN user AS u ON (u.userid=`requirement`.creator)
			LEFT JOIN user AS us ON (us.userid=`requirement`.modifier)
			LEFT JOIN mftstatus AS ms ON (ms.statusid=`requirement`.statusid)
			LEFT JOIN mfttype AS mt ON (mt.typeid=`requirement`.typeid)
			WHERE `requirement`.killed=0 AND `requirement`.statusid>1 
			ORDER BY ".$orderby['sql']."
		");
		if($this->kclass->DB->numRows()){
			$i=1;
			while($requirement=$this->kclass->DB->fetchArray($requirements)){
				$created=date('Y-m-d', $requirement['created']);
				$items=$this->kclass->DB->query("
					SELECT mrp.mrpid,mrp.requirementid,mrp.materialid,mrp.materialitemid,mrp.versionid,mrp.netQuantity,mrp.requiredate,mrp.ifChooser,mrp.revision,
						mv.title AS version
					FROM mrp
					LEFT JOIN materialversion AS mv ON (mv.versionid=mrp.versionid)
					WHERE mrp.killed=0 AND mrp.requirementid='".$requirement['requirementid']."'
					ORDER BY mrp.mrpid ASC
				");
				if($this->kclass->DB->numRows()){
					while($item=$this->kclass->DB->fetchArray($items)){
						$material=$this->kclass->getMaterial(array('materialid'=>$item['materialid'],'itemid'=>$item['materialitemid']));
						$itemtr.='<tr class="'.$this->kclass->rotateLine(). ' small">
							<td>'.$i.'</td>
							<td><span class="small"><a href="/s.php?module=requirement&action=view&requirementid='.$requirement['requirementid'].'">'.$requirement['requirementno'].'</a></span></td>
							<td>'.$requirement['type'].'</td>
							<td><span class="small"><a href="'.$material['url'].'">'.$material['no'].'</a>　'.$material['material'].'　'.$material['standard'].'</span></td>
							<td>'.$item['version'].'</td>
							<td>'.$material['origin'].'</td>
							<td><span class="bold darkred">'.number_format($item['netQuantity']).'</span> '.$material['unit'].'</td>
							<td><span class="bold darkred">'.date('Y-m-d',$item['requiredate']).'</span></td>
							<td>'.$item['revision'].'</td>
							<td><span class="bold">'.$this->kclass->iif($item['ifChooser']==1,'已投产','待选择').'</span></td>
							<td></td>
						</tr>';
						$i++;
					}
				}
			}
		}
		if(!$itemtr){
			$itemtr='<tr><td colspan=14 class="center"><span class="darkred">暂无相关记录</span></td></tr>';
		}
		$body.=<<<EOF
<table class="hundred tablesorter mytable">
<thead>
<tr><th width="35">ID</th><th width="120">需求计划编号</th><th width="100">计划来源</th><th width="600">MRP物资</th><th>版本</th><th>来源</th><th>净需求数量</th><th>需求日期</th><th>版次</th><th>状态</th><th>操作</th></tr>
</thead>
<tbody>
{$itemtr}
</tbody>
</table>
EOF;
		$this->kclass->page['onload']="dc.tabhover();$('.tablesorter').tablesorter( {sortList: [[0,0]], headers: { 10: {sorter: false} }} );$('.mytable').fixedtableheader();";
		$this->kclass->page['title'].=' 查看MRP';
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl . ' - <a href="/s.php?module=requirement&action=list">需求计划列表</a> - 查看MRP','right'=> '<span class="small">排序：</span>'.$orderby['link'].' ','body'=>$body));
		//<a href="/s.php?module=requirement&action=appointComplete&mrpid='.$mrpitem['mrpid'].'">指定完工</a>
	}
	// 如果是毛需求bom方式，那么从需求计划明细和明细的bom中选择需要生产的物资。
	function addMRP(){
		$requirement=$this->kclass->DB->queryFirst("SELECT requirementid,requirementno,modeid FROM requirement WHERE killed=0 AND statusid=2 AND requirementid='".$this->kclass->input['requirementid']."' LIMIT 0,1");
		if(!$requirement){
			$e='<li>数据错误，很抱歉</li>';
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '选择需要生产的物资添加到MRP中',
				'text' => '选择需要生产的物资添加到MRP中 <ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		//计划的物资
		$items=$this->kclass->DB->query("
			SELECT ri.module,ri.mid,ri.mitemid,ri.materialid,ri.versionid,ri.materialitemid,ri.statusid,ri.reviseid,ri.dateline,ri.quantity,
				mv.title AS version,mv.`change`,mv.qualified,mv.planInbound,mv.planOutbound
			FROM requirementitem AS ri 
			LEFT JOIN material AS m ON (m.materialid=ri.materialid)
			LEFT JOIN materialitem AS mi ON (mi.itemid=ri.itemid)			
			LEFT JOIN materialversion AS mv ON (mv.versionid=ri.versionid)
			WHERE ri.killed=0 AND ri.requirementid='".$requirement['requirementid']."'
			ORDER BY ri.module ASC,m.materialno ASC,mi.itemno ASC 
		");
		if($this->kclass->DB->numRows()){
			$i=1;$ritems=array();$id='';
			while($item=$this->kclass->DB->fetchArray($items)){
				$usable=$item['qualified']+$item['planInbound']-$item['planOutbound']; // 可用库存
				if($item['quantity']>$usable){
					$ritems[]=$item;
				}
				$item['dateline']=date('Y-m-d',$item['dateline']);
				// 需求计划明细
				$materialInfo=$this->kclass->getMaterial(array('materialid'=>$item['materialid'],'itemid'=>$item['materialitemid'])); // 获得物资信息
				$itemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').' small">
					<td><input type="hidden" name="itemId['.$i.']" value="'.$i.'">
					<input type="hidden" name="itemMaterialid['.$i.']" value="'.$item['materialid'].'" />
					<input type="hidden" name="materialItemid['.$i.']" value="'.$item['materialitemid'].'" />
					<input type="hidden" name="itemVersionid['.$i.']" value="'.$item['versionid'].'" />
					<input type="hidden" name="itemQuantity['.$i.']" value="'.$item['quantity'].'" />'.$i.'</td>
					<td><a href="'.$materialInfo['url'].'">'.$materialInfo['no'].'</a>　'.$materialInfo['material'].'　'.$materialInfo['standard'].'</td>
					<td>'.$this->kclass->iif($item['change']=='',$item['version'],'<a class="tip" href="#" title="( '.$item['change'].' )">'.$item['version'].$this->kclass->iif($item['itemno']!='',' ('.$item['itemno'].') ','').'</a>').'</td>
					<td>'.$materialInfo['origin'].'</td>
					<td>'.$item['quantity'].'</td>
					<td><input type="text" id="dateline'.$i.'" size="10" name="itemDateline['.$i.']" value="'.$item['dateline'].'" ></td>
					<td>'.$item['qualified'].'</td>
					<td>'.$item['planInbound'].'</td>
					<td>'.$item['planOutbound'].'</td>
					<td>'.$usable.'</td>
					<td><input type="checkbox" name="itemSelect['.$i.']" value="'.$i.'"></td>
				</tr>';
				$id.='#dateline'.$i.',';
				$i++;
			}
		}
		$k=$i;
		foreach($ritems AS $ritem){
			$child=$this->kclass->bomChildList(array('parentid'=>$ritem['materialid'],'materialitemid'=>$ritem['materialitemid'],'versionid'=>$ritem['versionid'],'quantity'=>$ritem['quantity'],'loopNum'=>1));
			if(!empty($child['bom'])){
				foreach($child['bom'] as $val){
					$childUsable=$val['qualified']+$val['planInbound']-$val['planOutbound'];
					if($val['ifGeneral']==0 AND $val['quantity']>$childUsable){
						$bomItemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').' small ">
							<td><input type="hidden" name="itemId['.$k.']" value="'.$k.'">
							<input type="hidden" name="itemMaterialid['.$k.']" value="'.$val['materialid'].'" />
							<input type="hidden" name="materialItemid['.$k.']" value="'.$val['materialitemid'].'" />
							<input type="hidden" name="itemVersionid['.$k.']" value="'.$val['versionid'].'" />
							<input type="hidden" name="itemQuantity['.$k.']" value="'.$val['quantity'].'" />'.$k.'</td>
							<td><a href="'.$val['url'].'">'.$val['no'].'</a>　'.$val['title'].'　'.$val['standard'].'</td>
							<td>'.$val['version'].'</td>
							<td>'.$val['origin'].'</td>
							<td>'.$val['quantity'].'</td>
							<td><input type="text" id="dateline'.$k.'" size="10" name="itemDateline['.$k.']" value="'.$ritem['dateline'].'" ></td>
							<td>'.$val['qualified'].'</td>
							<td>'.$val['planInbound'].'</td>
							<td>'.$val['planOutbound'].'</td>
							<td>'.$childUsable.'</td>
							<td><input type="checkbox" name="itemSelect['.$k.']" value="'.$k.'"></td>
						</tr>';
						$id.='#dateline'.$k.',';
						$k++;
					}
				}
			}
		}
		$revise=$this->kclass->relatedRevise(array('module'=>'requirement','mid'=>$requirement['requirementid']));
		$body=<<<EOF
<div class="title">{$requirement['requirementno']}{$revise['currencyRevise']}</div>
<form method="post" action="/s.php?module=requirement&action=insertMRP">
<input type="hidden" name="module" value="requirement">
<input type="hidden" name="action" value="insertMRP">
<input type="hidden" name="requirementid" value="{$this->kclass->input['requirementid']}">
<table class="hundred">
<thead><tr><th colspan="16">选择物资明细</th></tr></thead>
<tbody>
<tr class="center even">
<td width="15">ID</td><td>物资</td><td width="80">版本</td><td width="70">物资来源</td><td width="70">毛需求数量</td><td width="80">需求交期 <span class="bold red">*</span></td><td width="60">良品库存</td><td width="60">待入库存</td><td width="60">待出库存</td><td width="60">计划可用</td><td width="15">选</td>
</tr>
{$itemtr}
{$bomItemtr}
<tr><td colspan=16 class="center"><input type="submit" value=" 提交 " /><input type="reset" value="重置" /></td></tr>
</tbody>
</table>
</form>
EOF;
		$this->kclass->page['onload']="dc.tabhover();dc.tips();var dates=$('$id').datepicker({onSelect:function(selectedDate){instance=$(this).data('datepicker'),date=$.datepicker.parseDate(instance.settings.dateFormat || $.datepicker._defaults.dateFormat,selectedDate,instance.settings);}});";
		$this->kclass->page['title'].=' 加入MRP';
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl . ' - <a href="/s.php?module=requirement&action=list">需求计划列表</a> - 加入MRP','body'=>$body));
	}
	function insertMRP(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			$hasItem=0;
			$j=count($this->kclass->input['itemId']);
			for($i=1;$i<=$j;$i++){
				if($this->kclass->input['itemSelect'][$i]!='' AND $this->kclass->input['itemDateline'][$i]!=''){
					$hasItem=1;
				}
			}
			if($hasItem==0){
				$e='<li>请填写被选择明细的完整内容</li>';
			}
		}
		if($e){
			$this->kclass->messager(array(
				'title' => '加入MRP',
				'text' => '您在加入MRP的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$requirement=$this->kclass->DB->queryFirst("SELECT requirementid,requirementno FROM requirement WHERE requirementid='".$this->kclass->input['requirementid']."'");
		for($i=1;$i<=$j;$i++){
			if($this->kclass->input['itemSelect'][$i]!='' AND $this->kclass->input['itemDateline'][$i]!=''){
				$sd=explode('-',$this->kclass->input['itemDateline'][$i]);
				$dateline=mktime(0,0,0,$sd[1],$sd[2],$sd[0]);
				$this->kclass->DB->query("
					INSERT INTO mrp (requirementid,materialid,versionid,materialitemid,netQuantity,requiredate,created,creator)
					VALUE ('".$this->kclass->input['requirementid']."','".$this->kclass->input['itemMaterialid'][$i]."','".$this->kclass->input['itemVersionid'][$i]."','".$this->kclass->input['materialItemid'][$i]."','".$this->kclass->input['itemQuantity'][$i]."','".$dateline."','".TIMENOW."','".$this->kclass->user['userid']."')
				");
			}
		}

		$this->kclass->messager(array(
			'title' => '加入MRP',
			'text' => '需求计划单 【<b>'.$requirement['requirementno'].'</b>】 已成功加入MRP!返回查看需求计划单',
			'url' => '/s.php?module=requirement&action=view&requirementid='.$requirement['requirementid'],
			'sec' => 2
		));
	}
	//订单变更发生时取消订单 将订单型需求计划转为库存 减去订单物资待出数量
	function orderChange(){
		$rt=$this->kclass->DB->queryFirst("
			SELECT r.*,
				s.title AS status,
				u.username AS creator,
				us.username AS modifier
			FROM requirement AS r
			LEFT JOIN mftstatus AS s ON (s.statusid=r.statusid)
			LEFT JOIN user AS u ON (u.userid=r.creator)
			LEFT JOIN user AS us ON (u.userid=r.modifier)
			WHERE r.killed=0 AND r.requirementid='".$this->kclass->input['requirementid']."'
			LIMIT 0,1
		");
		if(!$rt OR $rt['statusid']==1 OR $rt['typeid']==2){
			$e='<li>数据错误，很抱歉~</li>';
		}
		$mrp=$this->kclass->DB->queryFirst("
				SELECT mrpid
				FROM mrp
				WHERE killed=0 AND ifChooser=0 AND requirementid='".$this->kclass->input['requirementid']."'
				LIMIT 0,1
			");
		if(!$mrp==''){
			$e='<li>需求计划单还没有投产，不需要进行订单变更！</li>';
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '需求计划（PO）订单变更',
				'text' => '需求计划（PO）订单变更<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
$body=<<<EOF
<form method="post" action="/s.php?module=requirement&action=doorderChange">
<input type="hidden" name="module" value="requirement">
<input type="hidden" name="action" value="doorderChange">
<input type="hidden" name="requirementid" value="{$this->kclass->input['requirementid']}">
<table>
<thead><tr><th colspan="6">选择需求计划单 <span class="bold darkred">{$rt['requirementno']}</span> （PO）订单变更</th></tr></thead>
<tbody>
<tr class="even">
	<td>选项：<span class="bold red">*</span></td><td class="center"><input type="radio" name="orderChange" value="1" checked="checked" />订单取消　　　<input type="radio" name="orderChange" value="0" />订单投产　　　</td>
</tr>
<tr class="odd">
	<td colspan=6 class="center small darkred">注：（PO）订单取消后将转入库存需求！</td>
</tr>
<tr class="even">
	<td colspan=6 class="center"><input type="submit" value=" 提交 " /><input type="reset" value="重置" /></td>
</tr>
</tbody>
</table>
</form>
EOF;
			$this->kclass->tbline+=4;
			$this->kclass->page['title'].='（PO）订单变更';
			$this->kclass->page['onload'].='dc.tabhover();';
			$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - <a href="/s.php?module=requirement&action=view&requirementid='.$this->kclass->input['requirementid'].'">查看需求计划单</a> - （PO）订单变更', 'right'=>'<a href="/s.php?module=requirement&action=list">返回列表</a>　|　<a href="/s.php?module=requirement&action=view&requirementid='.$this->kclass->input['requirementid'].'">查看需求计划单</a>','body'=>$body));
	}
	//执行订单变更
	function doorderChange(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			$rt=$this->kclass->DB->queryFirst("SELECT requirementno,statusid,typeid,modeid,orderChange FROM requirement WHERE requirementid='".$this->kclass->input['requirementid']."'");
			if($this->kclass->input['orderChange']==''){
				$e.='<li>请选择需求计划单中的 选项 按钮。</li>';
			}
			if($this->kclass->input['orderChange']==$rt['orderChange']){
				$e.='<li>订单变更已进行了该操作，请选择其他选项！</li>';
			}
			if($rt['statusid']==1){
				$e.='<li>需求计划单 '.$rt['requirementno'].' 未锁定，不需要进行订单变更！</li>';
			}
			if($rt['typeid']==2){
				$e.='<li>需求计划单 '.$rt['requirementno'].' 为库存需求，不需要进行订单变更！</li>';
			}
		}
		if($e){
			$this->kclass->messager(array(
				'title' => '（PO）订单变更',
				'text' => '您在（PO）订单变更的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$option=$this->kclass->iif($this->kclass->input['orderChange']==1,'-','+');
		//订单需求成品预出
		if($rt['typeid']==1){
			$vs=$this->kclass->DB->query("
				SELECT ri.versionid,ri.quantity
				FROM requirementitem AS ri
				LEFT JOIN material AS m ON (m.materialid=ri.materialid)
				WHERE ri.killed=0 AND ri.requirementid='".$this->kclass->input['requirementid']."'
			");
			if($this->kclass->DB->numRows()){
				while($v=$this->kclass->DB->fetchArray($vs)){
					$this->kclass->DB->query("
						UPDATE materialversion SET
							planOutbound=planOutbound ".$option." '".$v['quantity']."'
						WHERE versionid='".$v['versionid']."'
					");
				}
			}
		}
		$this->kclass->DB->query("
		UPDATE requirement SET
			orderChange='".$this->kclass->input['orderChange']."',
			modifier='".$this->kclass->user['userid']."',
			modified='".TIMENOW."'
		WHERE requirementid='".$this->kclass->input['requirementid']."'
		");
		$change=$this->kclass->iif($this->kclass->input['orderChange']==1,'取消订单，转为库存','订单投产，更新待出');
		$this->kclass->actionLog(array('module'=>'requirement','mid'=>$this->kclass->input['requirementid'],'note'=>'进行订单变更：'.$change));
		$this->kclass->messager(array(
			'title' => '（PO）订单变更',
			'text' => '需求计划单 <b>'.$rt['requirementno'].'</b> 订单变更成功！',
			'url' => '/s.php?module=requirement&action=view&requirementid='.$this->kclass->input['requirementid'],
			'sec' => 2
		));
	}
	//
	//订单变更原本计划使用指定完工 现已经用orderChange代替 此功能作废 看需求跟进开发保留 --gaowenfei 2013/11/25 8:54
	function appointComplete(){
		$mrp=$this->kclass->DB->queryFirst("
			SELECT m.*,
				r.requirementno,r.planner,r.typeid
			FROM mrp AS m
			LEFT JOIN requirement AS r ON (r.requirementid=m.requirementid)
			WHERE m.killed=0 AND m.ifChooser=0 AND m.mrpid='".$this->kclass->input['mrpid']."'
			LIMIT 0,1
		");
		if(!$mrp){
			$e='<li>数据错误，很抱歉~</li>';
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '指定完工MRP明细',
				'text' => '指定完工MRP明细时发生以下错误:<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
$body=<<<EOF
<form method="post" action="/s.php?module=requirement&action=doappointComplete">
<input type="hidden" name="module" value="requirement">
<input type="hidden" name="action" value="dolock">
<input type="hidden" name="mrpid" value="{$this->kclass->input['mrpid']}">
<table class="hundred">
<thead><tr><th colspan="10">MRP明细</th></tr></thead>
<tbody>
<tr class="even">
<td width="100">需求计划编号：</td><td width="475">{$mrp['requirementno']}</td>
<td>计划时间：</td><td><span class="bold darkred">{$mode}</span></td>
<td width="100">计划员：</td><td>{$mrp['planner']}</td>
<td width="100">计划来源：</td><td>{$mrp['typeid']}</td>
</tr>
<tr class="odd">
<td width="100">生产任务物资：</td><td>{$mrp['typeid']}</td>
<td width="100">版本：</td><td>{$mrp['typeid']}</td>
<td width="100">净需求量：</td><td>{$mrp['typeid']}</td>
<td width="100">需求日期：</td><td>{$mrp['typeid']}</td>
</tr>
<tr class="odd">
	<td colspan=10 class="center"><input type="submit" value=" 提交 " /><input type="reset" value="重置" /></td>
</tr>
</tbody>
</table>
</form>
EOF;
			$this->kclass->tbline+=4;
			$this->kclass->page['title'].='指定完工MRP明细';
			$this->kclass->page['onload'].='dc.tabhover();';
			$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - <a href="/s.php?module=requirement&action=view&requirementid='.$mrp['requirementid'].'">查看需求计划单</a> - 指定完工需求计划明细', 'right'=>'<a href="/s.php?module=requirement&action=list">返回列表</a>　|　<a href="/s.php?module=requirement&action=view&requirementid='.$mrp['requirementid'].'">查看需求计划单</a>','body'=>$body));
	}
	//
	//
	function doappointComplete(){

	}
	//

	/*旧版本获取mrp 现已经提取直接在view页面显示 --gaowenfei 2013/11/25 8:55
	function getMRP(){
		$rt=$this->kclass->DB->queryFirst("SELECT * FROM requirement WHERE requirementid='".$this->kclass->input['requirementid']."' LIMIT 0,1");
		if(!$rt){
			$e='<li>数据错误，很抱歉</li>';
		}
		if($rt['statusid']==2){
					$e.='<li>需求计划单 '.$rt['requirementno'].' 已经锁定。</li>';
				}
		if($e){
			$this->kclass->messager(array(
				'title' => '查看需求计划单',
				'text' => '您在查看需求计划单的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$materialid=$productItemid=$versionid=array();
		$items=$this->kclass->DB->query("
			SELECT ri.*,
				pi.mid,
				m.min,
				mv.qualified,mv.planInbound,mv.planOutbound
			FROM requirementitem AS ri
			LEFT JOIN productitem AS pi ON (pi.productitemid=ri.productitemid)
			LEFT JOIN material AS m ON (m.materialid=ri.materialid)
			LEFT JOIN materialversion AS mv ON (mv.versionid=ri.versionid)
			WHERE ri.killed=0 AND ri.requirementid='".$this->kclass->input['requirementid']."'
			ORDER BY ri.ordering ASC
		");
		if($this->kclass->DB->numRows()){
			while($item=$this->kclass->DB->fetchArray($items)){
				if(in_array($item['materialid'],$materialid) AND in_array($item['productitemid'],$productItemid) AND in_array($item['versionid'],$versionid)){
					$quantity[$item['versionid']]+=$item['quantity'];
					if($dateline[$item['versionid']]>$item['dateline']){
						$dateline[$item['versionid']]=$dateline[$item['versionid']];
					}else{
						$dateline[$item['versionid']]=$item['dateline'];
					}
				}else{
					$productid[$item['versionid']]=$item['mid'];
					$materialid[$item['versionid']]=$item['materialid'];
					$productItemid[$item['versionid']]=$item['productitemid'];
					$versionid[$item['versionid']]=$item['versionid'];
					$quantity[$item['versionid']]=$item['quantity']-$item['qualified']+$item['min']-$item['planInbound']+$item['planOutbound'];
					$dateline[$item['versionid']]=$item['dateline'];
					if($quantity[$item['versionid']]>0){
						$this->kclass->DB->query("
							INSERT `mrp` (requirementid,materialid,versionid,productitemid,netQuantity,requiredate,created,creator)
							VALUE ('".$this->kclass->input['requirementid']."','".$item['materialid']."','".$item['versionid']."','".$item['productitemid']."','".$quantity[$item['versionid']]."','".$item['dateline']."','".TIMENOW."','".$this->kclass->user['userid']."')
						");
					}
				}
			}
		}
		$arr=$this->kclass->mrpList(array('productid'=>$productid,'materialid'=>$materialid,'productitemid'=>$productItemid,'versionid'=>$versionid,'quantity'=>$quantity,'dateline'=>$dateline));
		$j=1;
		if($arr){
			foreach($arr as $key => $val){
				if($val['netQuantity']>0){
					$this->kclass->DB->query("
						INSERT `mrp` (requirementid,materialid,versionid,productitemid,netQuantity,requiredate,created,creator)
						VALUE ('".$this->kclass->input['requirementid']."','".$val['materialid']."','".$val['versionid']."','".$val['productitemid']."','".$val['netQuantity']."','".$val['dateline']."','".TIMENOW."','".$this->kclass->user['userid']."')
					");
				}
			}
		}
		$this->kclass->messager(array(
			'title' => '生成需求计划单的MRP',
			'text' => '需求计划单 【<b>'.$rt['requirementno'].'</b>】 MRP 生成成功!返回查看需求计划单',
			'url' => '/s.php?module=requirement&action=view&requirementid='.$this->kclass->input['requirementid'],
			'sec' => 2
		));
	}
	*/
	//需求计划锁定后新增采购申请
	function addPurchaseApply(){
		$requirement=$this->kclass->DB->queryFirst("SELECT requirementno FROM requirement WHERE killed=0 AND statusid=2 AND requirementid='".$this->kclass->input['requirementid']."'");
		if(!$this->kclass->input['itemId']){
			$mrpitems=$this->kclass->DB->query("
				SELECT mrp.mrpid,mrp.requirementid,mrp.materialid,mrp.materialitemid,mrp.versionid,mrp.netQuantity,mrp.requiredate,
					mv.title AS version
				FROM mrp
				LEFT JOIN material AS m ON (m.materialid=mrp.materialid)
				LEFT JOIN materialversion AS mv ON (mv.versionid=mrp.versionid)
				WHERE mrp.killed=0 AND mrp.ifChooser=0 AND m.originid=2 AND mrp.requirementid='".$this->kclass->input['requirementid']."'
				ORDER BY mrp.created DESC
			");
			if($this->kclass->DB->numRows()){
				$i=1;
				while($mrpitem=$this->kclass->DB->fetchArray($mrpitems)){
					$materialInfo=$this->kclass->getMaterial(array('itemid'=>$mrpitem['materialitemid'],'materialid'=>$mrpitem['materialid']));
					$itemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').'">
						<input type="hidden" name="itemId['.$i.']" value="'.$mrpitem['mrpid'].'">
						<td>'.$i.'</td>
						<td><a href="'.$materialInfo['url'].'">'.$materialInfo['no'].'</a>　'.$materialInfo['material'].'　'.$materialInfo['standard'].'</td>
						<td>'.$mrpitem['version'].'</td>
						<td>'.$mrpitem['netQuantity'].'</td>
						<td>'.date('Y-m-d',$mrpitem['requiredate']).'</td>
						<td><input type="checkbox" name="itemSelect['.$i.']" value="'.$mrpitem['mrpid'].'" /></td>
					</tr>';
					$i++;
				}
			}
			$body=<<<EOF
<form method="post" action="" name="requirement">
<input type="hidden" name="module" value="requirement" />
<input type="hidden" name="action" value="addPurchaseApply" />
<input type="hidden" name="requirementid" value="{$this->kclass->input['requirementid']}" />
<table class="hundred">
<thead><tr><th colspan=6>选择明细</th></tr></thead>
<tbody>
<tr class="odd"><td width="15">ID</td><td>物资</td><td>版本</td><td>数量</td><td>需求时间</td><td>选</td></tr>
{$itemtr}
<tr class="even center" nohover><td colspan=6><input type="submit" value="提交" /><input type="reset" value="重置" /></td></tr>
</tbody>
</table>
</form>
EOF;
			$this->kclass->page['title'] .= $requirement['requirementno'].' - 查看mrp';
			$this->kclass->page['onload'].='dc.tabhover();$(\''.$id.',#paymentDate\').datepicker()';
			$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - <a href="/s.php?module=requirement">需求计划列表</a> - 查看mrp', 'right'=>'<a href="/s.php?module=requirement&action=list">返回列表</a>　','body'=>$body));
		}else{
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
			$j=count($this->kclass->input['itemId']);
			$i=1;
			for($m=1;$m<$j+1;$m++){
				if($this->kclass->input['itemSelect'][$m]>0){
					$erpitem=$this->kclass->DB->queryFirst("
						SELECT mrp.mrpid,mrp.requirementid,mrp.materialid,mrp.materialitemid,mrp.versionid,mrp.netQuantity,mrp.requiredate,
							mv.title AS version
						FROM mrp
						LEFT JOIN materialversion AS mv ON (mv.versionid=mrp.versionid)
						WHERE mrp.killed=0 AND mrp.mrpid='".$this->kclass->input['itemSelect'][$m]."'
						ORDER BY mrp.created DESC
					");
					$materialInfo=$this->kclass->getMaterial(array('itemid'=>$erpitem['materialitemid'],'materialid'=>$erpitem['materialid']));
					$itemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').' small">
						<input type="hidden" name="itemId['.$i.']" value="'.$erpitem['mrpid'].'">
						<input type="hidden" name="itemMaterialid['.$i.']" value="'.$erpitem['materialid'].'">
						<input type="hidden" name="productItemid['.$i.']" value="'.$erpitem['productitemid'].'">
						<input type="hidden" name="itemVersionid['.$i.']" value="'.$erpitem['versionid'].'">
						<td><a href="'.$materialInfo['url'].'">'.$materialInfo['no'].'</a>　'.$materialInfo['material'].'　'.$materialInfo['standard'].'</td>
						<td>'.$erpitem['version'].'</td>
						<td><input type="text" name="itemQuantity['.$i.']" value="'.$erpitem['netQuantity'].'" size=15 /></td>
						<td>'.$this->kclass->chooserUnit(array('name'=>'itemUnitid['.$i.']','width'=>'85','hasBlank'=>1,'selectedid'=>$materialInfo['unitid'])).'</td>
						<td><input type="text" name="itemDateline['.$i.']" id="dateline'.$i.'" value="'.date('Y-m-d',$erpitem['requiredate']).'" /></td>
						<td><input type="text" name="itemRemark['.$i.']" style="width:350px;" /></td></tr>';
					if($i<$j){
						$id.='#dateline'.$i.',';
					}else{
						$id.='#dateline'.$i;
					}
					$i++;
				}
			}

			$body=<<<EOF
<form action="/s.php?module=purchaseApply&action=insert" name="purchaseApply" method="post">
<input type="hidden" name="module" value="purchaseApply" />
<input type="hidden" name="action" value="insert" />
<input type="hidden" name="requirementid" value="{$this->kclass->input['requirementid']}" />
<input type="hidden" name="departmentid" value="{$this->kclass->user['departmentid']}" />
<table class="hundred">
<thead><tr><th colspan="4">基本信息</th></tr></thead>
<tbody>
<tr class="even">
<td>编　　号：</td><td><span class="middle bold darkred">{$applyno}</span>　　<span class="small gray">（此为预估编号）</span></td>
<td>申请人员：<span class="bold red">*</span></td><td><input type="text" name="applicant" value="{$this->kclass->user['realname']}"></td>
</tr>
<tr class="odd">
<td>部　　门：<span class="bold red">*</span></td><td>{$this->kclass->user['department']}</td>
<td>采购类型：<span class="bold red">*</span></td><td>生产类：{$chooserType1}　非生产类：{$chooserType2}</td>
</tr>
<tr class="even">
<td>用　　途：<span class="bold red">*</span></td>
<td><textarea type="text" name="purpose" style="width:444px;height:100px"></textarea>
<td>备　　注：</td>
<td><textarea type="text" name="remark" style="width:444px;height:100px"></textarea>
</tr>
</tbody>
</table>
<table class="hundred">
<thead><tr><th colspan="9">申请采购的物资</th></tr></thead>
<tbody>
<tr class="even center" nohover><td>物资 <span class="bold red">*</span></td><td>版本</td><td>数量 <span class="bold red">*</span></td><td>单位</td><td>到货时间 <span class="bold red">*</span></td><td>备注</td></tr>
{$itemtr}
<tr class="even">
<td class="small gray" colspan="9">
注意事项：<br>
①如果所列表格不够，那么在提交保存后再修改采购申请。<br>
②若要采购新物资，则选择“物资信息”中的“新物资”，必须填写物资名称；否则在“物资信息”中间选择物资，“物资编号”、“物资名称”、“物资规格”不需要填写。<br>
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
			$this->kclass->page['title'] .= $requirement['requirementno'].' - 查看mrp';
			$this->kclass->page['onload'].='var dates=$(\''.$id.'\').datepicker({onSelect:function(selectedDate){instance=$(this).data(\'datepicker\'),date=$.datepicker.parseDate(instance.settings.dateFormat || $.datepicker._defaults.dateFormat,selectedDate,instance.settings);}});dc.tabhover();';
			$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - <a href="/s.php?module=purchaseApply&action=list">采购申请列表</a> - 新建采购申请', 'right'=>'<a href="/s.php?module=purchaseApply&action=list">返回列表</a>','body'=>$body));
		}
	}
	/*用于旧版本转换 现已作废 --gaowenfei 2013/11/25 8:56
	function addPlan(){
   $items=$this->kclass->DB->query("
			SELECT o.orderid,ri.itemid
			FROM `requirementitem` AS ri
			LEFT JOIN `item` AS i ON (i.itemid=ri.orderitemid)
			LEFT JOIN `order` AS o ON (i.mid=o.orderid)
			WHERE 1
			ORDER BY ri.`itemid` ASC
   ");
	 if($this->kclass->DB->numRows()){
		while($item=$this->kclass->DB->fetchArray($items)){
			if($item['orderid']=='')$item['orderid']=0;
			$this->kclass->DB->query("
				UPDATE `requirementitem` SET `orderid`='".$item['orderid']."' WHERE `itemid`='".$item['itemid']."'
			");
		}
	 }
 echo "导入成功！";
	}
	*/
	//需求计划锁定时新增外发加工
	function addEntrust(){
		$requirement=$this->kclass->DB->queryFirst("SELECT requirementno FROM requirement WHERE killed=0 AND statusid=2 AND requirementid='".$this->kclass->input['requirementid']."'");
		if(!$this->kclass->input['itemId']){
			$mrpitems=$this->kclass->DB->query("
				SELECT mrp.mrpid,mrp.requirementid,mrp.materialid,mrp.productitemid,mrp.versionid,mrp.netQuantity,mrp.requiredate,
					mv.title AS version
				FROM mrp
				LEFT JOIN material AS m ON (m.materialid=mrp.materialid)
				LEFT JOIN materialversion AS mv ON (mv.versionid=mrp.versionid)
				WHERE mrp.killed=0 AND mrp.ifChooser=0 AND m.originid=3 AND mrp.requirementid='".$this->kclass->input['requirementid']."'
				ORDER BY mrp.created DESC
			");
			if($this->kclass->DB->numRows()){
				$i=1;
				while($mrpitem=$this->kclass->DB->fetchArray($mrpitems)){
					$materialInfo=$this->kclass->getMaterialUrl(array('itemid'=>$mrpitem['materialitemid'],'materialid'=>$mrpitem['materialid']));
					$itemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').'">
						<input type="hidden" name="itemId['.$i.']" value="'.$mrpitem['mrpid'].'">
						<td>'.$i.'</td>
						<td><a href="'.$materialInfo['url'].'">'.$materialInfo['no'].'</a>　'.$materialInfo['material'].'　'.$materialInfo['standard'].'</td>
						<td>'.$mrpitem['version'].'</td>
						<td>'.$mrpitem['netQuantity'].'</td>
						<td>'.date('Y-m-d',$mrpitem['requiredate']).'</td>
						<td><input type="checkbox" name="itemSelect['.$i.']" value="'.$mrpitem['mrpid'].'" /></td>
					</tr>';
					$i++;
				}
			}
			$body=<<<EOF
<form method="post" action="" name="requirement">
<input type="hidden" name="module" value="requirement" />
<input type="hidden" name="action" value="addEntrust" />
<input type="hidden" name="requirementid" value="{$this->kclass->input['requirementid']}" />
<table class="hundred">
<thead><tr><th colspan=6>选择明细</th></tr></thead>
<tbody>
<tr class="odd"><td width="15">ID</td><td>物资</td><td>版本</td><td>数量</td><td>需求时间</td><td>选</td></tr>
{$itemtr}
<tr class="even center" nohover><td colspan=6><input type="submit" value="提交" /><input type="reset" value="重置" /></td></tr>
</tbody>
</table>
</form>
EOF;
			$this->kclass->page['title'] .= $requirement['requirementno'].' - 查看mrp';
			$this->kclass->page['onload'].='dc.tabhover();$(\''.$id.',#paymentDate\').datepicker()';
			$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - <a href="/s.php?module=requirement">需求计划列表</a> - 查看mrp', 'right'=>'<a href="/s.php?module=requirement&action=list">返回列表</a>　','body'=>$body));
		}else{
			$start=strtotime(date('Y-m-d',TIMENOW));
			$end=strtotime(date('Y-m-d',$start))+86400;
			$counter=$this->kclass->DB->queryFirst("SELECT COUNT(*) AS count FROM `entrust` WHERE created>='".$start."' AND created<='".$end."'");
			$entrustno=$this->kclass->id(array('entrust'=>TIMENOW,'number'=>$counter['count']));
			$upload=$this->kclass->upload(array('title'=>'相关附件：'));
			$j=count($this->kclass->input['itemId']);
			$i=1;
			for($m=1;$m<$j+1;$m++){
				if($this->kclass->input['itemSelect'][$m]>0){
					$erpitem=$this->kclass->DB->queryFirst("
						SELECT mrp.mrpid,mrp.requirementid,mrp.materialid,mrp.materialitemid,mrp.versionid,mrp.netQuantity,mrp.requiredate,
							mv.title AS version
						FROM mrp
						LEFT JOIN materialversion AS mv ON (mv.versionid=mrp.versionid)
						WHERE mrp.killed=0 AND mrp.mrpid='".$this->kclass->input['itemSelect'][$m]."'
						ORDER BY mrp.created DESC
					");
					$materialInfo=$this->kclass->getMaterial(array('itemid'=>$erpitem['materialitemid'],'materialid'=>$erpitem['materialid']));
					$itemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').' small">
						<input type="hidden" name="itemId['.$i.']" value="'.$erpitem['mrpid'].'">
						<input type="hidden" name="itemMaterialid['.$i.']" value="'.$erpitem['materialid'].'">
						<input type="hidden" name="productItemid['.$i.']" value="'.$erpitem['materialitemid'].'">
						<input type="hidden" name="itemVersionid['.$i.']" value="'.$erpitem['versionid'].'">
						<td><a href="'.$materialInfo['url'].'">'.$materialInfo['no'].'</a>　'.$materialInfo['material'].'　'.$materialInfo['standard'].'</td>
						<td>'.$erpitem['version'].'</td>
						<td><input type="text" name="itemQuantity['.$i.']" value="'.$erpitem['netQuantity'].'" size=15 /></td>
						<td>'.$this->kclass->chooserUnit(array('name'=>'itemUnitid['.$i.']','width'=>'85','hasBlank'=>1,'selectedid'=>$materialInfo['unitid'])).'</td>
						<td><input type="text" name="itemDateline['.$i.']" id="dateline'.$i.'" value="'.date('Y-m-d',$erpitem['requiredate']).'" /></td>
						<td><input type="text" name="itemRequirement['.$i.']" style="width:350px;" /></td>
						<td><input type="text" name="itemRemark['.$i.']" style="width:250px;" /></td></tr>';
					if($i<5){
						$id.='#dateline'.$i.',';
					}else{
						$id.='#dateline'.$i;
					}
					$i++;
				}
			}

			$body=<<<EOF
<form action="/s.php?module=entrust&action=insert" name="entrust" method="post">
<input type="hidden" name="module" value="entrust" />
<input type="hidden" name="action" value="insert" />
<input type="hidden" name="entrustno" value="{$entrustno}" />
<input type="hidden" name="requirementid" value="{$this->kclass->input['requirementid']}" />
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
			$this->kclass->page['title'] .= $requirement['requirementno'].' - 查看mrp';
			$this->kclass->page['onload'].='dc.tabhover();$(\''.$id.',#date\').datepicker()';
			$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - <a href="/s.php?module=requirement">需求计划列表</a> - 查看mrp', 'right'=>'<a href="/s.php?module=requirement&action=list">返回列表</a>　','body'=>$body));
		}
	}
	function addExtra(){
		if($this->kclass->input['orderid']==''){
			$orders=$this->kclass->DB->query("SELECT * FROM `order` WHERE killed=0 AND ifApprove=1 AND ifChooser=1 AND ifInbound=1 AND type='PO' ORDER BY created DESC");
			if($this->kclass->DB->numRows()){
				while($order=$this->kclass->DB->fetchArray($orders)){
					$orderSelect.='<option value="'.$order['orderid'].'">'.$order['ordertitle'].'</option>';
				}
			}
			$body=<<<EOF
<form action="/s.php?module=requirement&action=addExtra" method="get">
<input type="hidden" name="module" value="requirement" />
<input type="hidden" name="action" value="addExtra" />
<table style="width:600px">
<thead><tr><th colspan=2>选择销售PO订单</th></tr></thead>
<tbody>
<tr><td>销售PO订单：</td><td><select name="orderid"><option value="0">请选择PO订单</option>{$orderSelect}</select></td></tr>
<tr class="center">
<td colspan=13><input type="submit" value="提交"><input type="reset" value="重置"></td>
</tr>
</tbody></table>
</form>
EOF;
	}else{
		$start=strtotime(date('Y-m-d',TIMENOW));
		$end=strtotime(date('Y-m-d',$start))+86400;
		$counter=$this->kclass->DB->queryFirst("SELECT COUNT(*) AS count FROM requirement WHERE created>='".$start."' AND created<='".$end."'");
		$requirementno=$this->kclass->id(array('requirement'=>TIMENOW,'number'=>$counter['count']));
		$situation=$this->kclass->chooserMftSituation(array('name'=>'situationid','selectedid'=>1));
		$mode=$this->kclass->chooserMode(array('name'=>'modeid','selectedid'=>0));
		$items=$this->kclass->DB->query("
			SELECT item.itemid,item.productid,item.productitemid,item.productAttributes,item.price,item.quantity,item.dateline,
				p.title,
				m.materialid,m.standard,
				unit.title AS unit,
				order.orderid AS id,order.orderno AS no,order.shipmentDate AS dateline
			FROM `item`
			LEFT JOIN `product` AS p ON (p.productid=`item`.productid)
			LEFT JOIN `material` AS m ON (m.materialid=`item`.materialid)
			LEFT JOIN unit ON (unit.unitid=item.unitid)
			LEFT JOIN `order` ON (order.orderid=item.mid)
			WHERE `item`.killed=0 AND `item`.module='order' AND `item`.mid='{$this->kclass->input['orderid']}'
			ORDER BY `item`.itemid ASC,`item`.modified ASC
		");
		if($this->kclass->DB->numRows()){
			$i=1;
			while($item=$this->kclass->DB->fetchArray($items)){
				$revise=$this->kclass->relatedRevise(array('module'=>'order','mid'=>$this->kclass->input['orderid']));// 获得相关版本
				if($revise['reviseid']=='')$revise['reviseid']=0;
				if($item['productid']>0 AND $item['productitemid']==0){
					$partitems=$this->kclass->DB->query("
						SELECT pi.quantity,pi.childid,p.title,p.materialid,m.materialno,m.title AS material,m.standard
						FROM partitem AS pi 
						LEFT JOIN product AS p ON (p.productid=pi.childid)
						LEFT JOIN material AS m ON (m.materialid=p.materialid)
						WHERE pi.killed=0 AND p.killed=0 AND pi.productid='".$item['productid']."'
						ORDER BY pi.ordering ASC
					");
					if($this->kclass->DB->numRows()){// 存在配件包的时候。列出配件包的内容及数量
						while($partitem=$this->kclass->DB->fetchArray($partitems)){
							$r.='<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').' small">
								<td><input type="hidden" name="orderid['.$i.']" value="'.$this->kclass->input['orderid'].'">
								<input type="hidden" name="itemId['.$i.']" value="'.$item['itemid'].'">
								<input type="hidden" name="reviseId['.$i.']" value="'.$revise['reviseid'].'">
								<input type="hidden" name="productId['.$i.']" value="'.$partitem['childid'].'" />
								<input type="hidden" name="productItemid['.$i.']" value="0" />
								<input type="hidden" name="totalQuantity['.$i.']" value="'.($item['quantity']*$partitem['quantity']).'">'.$i.'</td>
								<td><a href="/s.php?module=order&action=view&orderid='.$item['id'].'">'.$item['no'].'</a> '.$revise['currencyRevise'].'</td>
								<td>'.date('Y-m-d',$item['dateline']).'</td>
								<td><a href="/s.php?module=product&action=view&productid='.$partitem['childid'].'">'.$partitem['title'].'</a></td>
								<td>'.($item['quantity']*$partitem['quantity']).' '.$item['unit'].'</td>
								<td>'.$this->_getMaterial(array('materialid'=>$partitem['materialid'],'line'=>$i,'quantity'=>($item['quantity']*$partitem['quantity']))).'</td>
								<td><input type="text" name="itemDateline['.$i.']" id="shipmentDate'.$i.'" size="15" value="'.date('Y-m-d',$item['dateline']).'"></td>
								<td><input type="text" name="itemRemark['.$i.']" size="25" value="'.$item['remark'].'"></td>
								<td><input type="checkbox" name="itemSelect['.$i.']" value="'.$i.'" ></td>
							</tr>';
							$id.='#shipmentDate'.$i.',';
							$i++;
						}
					}else{
						$r.='<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').' small">
							<td><input type="hidden" name="orderid['.$i.']" value="'.$this->kclass->input['orderid'].'">
							<input type="hidden" name="itemId['.$i.']" value="'.$item['itemid'].'">
							<input type="hidden" name="reviseId['.$i.']" value="'.$revise['reviseid'].'">
							<input type="hidden" name="productId['.$i.']" value="'.$item['productid'].'" />
							<input type="hidden" name="productItemid['.$i.']" value="'.$item['productitemid'].'" />
							<input type="hidden" name="totalQuantity['.$i.']" value="'.$item['quantity'].'">'.$i.'</td>
							<td><a href="/s.php?module=order&action=view&orderid='.$item['id'].'">'.$item['no'].'</a> '.$revise['currencyRevise'].'</td>
							<td>'.date('Y-m-d',$item['dateline']).'</td>
							<td><a href="/s.php?module=product&action=view&productid='.$item['productid'].'">'.$item['title'].'</a></td>
							<td>'.$item['quantity'].' '.$item['unit'].'</td>
							<td>'.$this->_getMaterial(array('materialid'=>$item['materialid'],'line'=>$i,'quantity'=>$item['quantity'])).'</td>
							<td><input type="text" name="itemDateline['.$i.']" id="shipmentDate'.$i.'" size="15" value="'.date('Y-m-d',$item['dateline']).'"></td>
							<td><input type="text" name="itemRemark['.$i.']" size="25" value="'.$item['remark'].'"></td>
							<td><input type="checkbox" name="itemSelect['.$i.']" value="'.$i.'" ></td>
						</tr>';
						$id.='#shipmentDate'.$i.',';
						$i++;
					}
				}else{
					$product=$this->kclass->getProduct(array('productid'=>$item['productid'],'itemid'=>$item['productitemid']));
					$r.='<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').' small">
						<td><input type="hidden" name="orderid['.$i.']" value="'.$this->kclass->input['orderid'].'">
						<input type="hidden" name="itemId['.$i.']" value="'.$item['itemid'].'">
						<input type="hidden" name="reviseId['.$i.']" value="'.$revise['reviseid'].'">
						<input type="hidden" name="productId['.$i.']" value="'.$item['productid'].'" />
						<input type="hidden" name="productItemid['.$i.']" value="'.$item['productitemid'].'" />
						<input type="hidden" name="totalQuantity['.$i.']" value="'.$item['quantity'].'">'.$i.'</td>
						<td><a href="/s.php?module=order&action=view&orderid='.$item['id'].'">'.$item['no'].'</a> '.$revise['currencyRevise'].'</td>
						<td>'.date('Y-m-d',$item['dateline']).'</td>
						<td><a href="/s.php?module=product&action=view&productid='.$item['productid'].'">'.$product['title'].'</a> '.$product['standard'].'</td>
						<td>'.$item['quantity'].' '.$item['unit'].'</td>
						<td>'.$this->_getMaterial(array('materialid'=>$item['materialid'],'line'=>$i,'productitemid'=>$item['productitemid'],'quantity'=>$item['quantity'])).'</td>
						<td><input type="text" name="itemDateline['.$i.']" id="shipmentDate'.$i.'" size="15" value="'.date('Y-m-d',$item['dateline']).'"></td>
						<td><input type="text" name="itemRemark['.$i.']" size="25" value="'.$item['remark'].'"></td>
						<td><input type="checkbox" name="itemSelect['.$i.']" value="'.$i.'" ></td>
					</tr>';
					$id.='#shipmentDate'.$i.',';
					$i++;
				}
			}
		}

		$flow=$this->kclass->listFlow(array('module'=>'requirement','statusid'=>'1'));
$body=<<<EOF
<form action="/s.php?module=requirement&action=insertExtra" name="requirement" method="post">
<input type="hidden" name="module" value="requirement" />
<input type="hidden" name="action" value="insertExtra" />
<input type="hidden" name="typeid" value="1" />
<div><span class="right">{$flow}</span></div>
<table class="hundred">
<thead><tr><th colspan="6">新建需求计划单 <span class="small darkred">(订单需求)</span></th></tr></thead>
<tbody>
<tr class="odd">
<td width="100">编　　号：<span class="red bold">*</span></td><td width="350"><span class="middle bold darkred">{$requirementno}</span> <span class="gray small">此为预估编号，保存后才可确定</span></td>
<td width="100">紧急情况：<span class="red bold">*</span></td><td width="350">{$situation}</td>
<td width="100">计 划 员：<span class="red bold">*</span></td><td><input type="text" name="planner"  value="{$this->kclass->user['realname']}"></td>
</tr>
<tr class="even">
<td>计算方式：<span class="red bold">*</span></td>
<td>{$mode}</td>
<td>备　　注：</td>
<td><textarea type="text" name="remark" style="width:300px;height:80px"></textarea></td><td></td><td></td>
</tr>
</tbody>
</table>
<table class="hundred">
<thead><tr><th colspan="12">计划生产明细</th></tr></thead>
<tbody><tr class="center bold"><td width="15">ID</td><td width="130">订单</td><td width="80">订单交期</td><td>产品或配件</td><td width="80">毛需求数量</td><td width="350">选择要生产的物资</td><td width="100">需求交期<span class="red bold">*</span></td><td width="100">备注</td><td width="15">选</td></tr></tbody><tbody class="small">
{$r}
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
		}
		$this->kclass->tbline+=4;
		$this->kclass->page['title'].='新建需求计划单';
		$this->kclass->page['onload'].='var dates=$(\''.$id.'\').datepicker({onSelect:function(selectedDate){instance=$(this).data(\'datepicker\'),date=$.datepicker.parseDate(instance.settings.dateFormat || $.datepicker._defaults.dateFormat,selectedDate,instance.settings);}});dc.tabhover();dc.tips();';
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - <a href="/s.php?module=requirement&action=list">需求计划单列表</a> - 新建需求计划单', 'right'=>'<a href="/s.php?module=requirement&action=list">返回列表</a>','body'=>$body));
	}
	function insertExtra(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e='<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			if($this->kclass->input['planner']==''){
				$e.='<li>请填写需求计划单中的 计划员</li>';
			}
			if($this->kclass->input['situationid']==''){
				$e.='<li>请填写需求计划单中的 紧急情况</li>';
			}
			if($this->kclass->input['modeid']==''){
				$e.='<li>请填写需求计划单中的 需求计算方式</li>';
			}
			$count=count($this->kclass->input['itemId']);
			for($i=1;$i<=$count;$i++){
				if($this->kclass->input['itemSelect'][$i]>0){
					if($this->kclass->input['itemDateline'][$i]==''){
						$e.='<li>请填写需求计划单中订单(PO)的生产明细[ID:'.$i.']的 需求日期</li>';
					}
					$jcount=count($this->kclass->input['itemVersionid'.$i]);
					$ifnull=0;$quantity=0;
					for($j=1;$j<=$jcount;$j++){
						if(intval($this->kclass->input['quantity'.$i][$j])>0){
							$ifnull=1;
							$quantity+=$this->kclass->input['quantity'.$i][$j];
						}
					}
					$item=$this->kclass->DB->queryFirst("SELECT p.attrid FROM item AS i LEFT JOIN product AS p ON (p.productid=i.productid) WHERE i.killed=0 AND i.itemid='".$this->kclass->input['itemId'][$i]."'");
					if($ifnull==0){
						$e.='<li>请填写至少一个需求计划单中订单(PO)的生产明细[ID:'.$i.']的 版本 需求数量</li>';
					}elseif($quantity!=$this->kclass->input['totalQuantity'][$i] AND $item['attrid']!=5){
						$e.='<li>需求计划单中订单(PO)的生产明细[ID:'.$i.'] 各版本的需求数量之和 必须和订单数量相等</li>';
					}
				}
			}
		}
		if($e){
			$this->kclass->messager(array(
				'title' => '新建需求计划单',
				'text' => '您在新建需求计划单的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$start=strtotime(date('Y-m-d',TIMENOW));
		$end=strtotime(date('Y-m-d',$start))+86400;
		$counter=$this->kclass->DB->queryFirst("SELECT COUNT(*) AS count FROM requirement WHERE created>='".$start."' AND created<='".$end."'");
		$requirementno=$this->kclass->id(array('requirement'=>TIMENOW,'number'=>$counter['count']));
		$this->kclass->DB->query("
			INSERT INTO `requirement` (`requirementno`,`typeid`,`situationid`,`modeid`,`planner`,`remark`,`created`,`creator`)
			VALUES ('".$requirementno."',1,'".$this->kclass->input['situationid']."','".$this->kclass->input['modeid']."','".$this->kclass->input['planner']."','".$this->kclass->input['remark']."','".TIMENOW."','".$this->kclass->user['userid']."')
		");
		$requirementid=$this->kclass->DB->insertID();
		$this->kclass->actionLog(array('module'=>'requirement','mid'=>$requirementid,'note'=>'新建需求计划单(订单需求)'));
		$count=count($this->kclass->input['itemId']);
		for($i=1;$i<=$count;$i++){
			if($this->kclass->input['itemSelect'][$i]>0){
				if($this->kclass->input['itemDateline'][$i]!=''){
					$sd=explode('-', $this->kclass->input['itemDateline'][$i]);
					$dateline=mktime(0,0,0,$sd[1],$sd[2],$sd[0]);
				}else{
					$dateline=0;
				}
				$jcount=count($this->kclass->input['itemVersionid'.$i]);
				for($j=1;$j<=$jcount;$j++){				
					if(intval($this->kclass->input['quantity'.$i][$j])>0){
						$materialItemid=$this->kclass->iif($this->kclass->input['materialItemid'.$i][$j]>0,$this->kclass->input['materialItemid'.$i][$j],0);
						$this->kclass->DB->query("
							INSERT INTO requirementitem (requirementid,materialid,versionid,materialitemid,productid,productitemid,module,mid,mitemid,reviseid,quantity,dateline,remark,created,creator)
							VALUES ('".$requirementid."','".$this->kclass->input['materialid'.$i][$j]."','".$this->kclass->input['itemVersionid'.$i][$j]."','".$materialItemid."','".$this->kclass->input['productId'][$i]."','".$this->kclass->input['productItemid'][$i]."','order','".$this->kclass->input['orderid'][$i]."','".$this->kclass->input['itemId'][$i]."','".$this->kclass->input['reviseId'][$i]."','".$this->kclass->input['quantity'.$i][$j]."','".$dateline."','".$this->kclass->input['itemRemark'][$i]."','".TIMENOW."','".$this->kclass->user['userid']."')
						");
					}
				}
			}
		}
		$this->kclass->messager(array(
			'title' => '新建需求计划单',
			'text' => '需求计划单 【<b>'.$requirementno.'</b>】 已新建成功!返回添加申请生产物资',
			'url' => '/s.php?module=requirement&action=view&requirementid='.$requirementid,
			'sec' => 2
		));
	}
	function updateExtra(){
		$requirement=$this->kclass->DB->queryFirst("
			SELECT r.requirementid,r.requirementno,r.situationid,r.typeid,r.statusid,r.modeid,r.planner,r.remark,
				m.realname
			FROM requirement AS r
			LEFT JOIN member AS m ON (m.userid=r.creator) 
			WHERE r.killed=0 AND r.requirementid='".$this->kclass->input['requirementid']."'
			LIMIT 0,1
		");
		$reitem=$this->kclass->DB->queryFirst("SELECT DISTINCT mid FROM requirementitem WHERE killed=0 AND requirementid={$this->kclass->input['requirementid']} LIMIT 0,1");
		if(!$requirement OR $requirement['statusid']!=1){
			$this->kclass->messager(array(
				'title' => '修改生产',
				'text' => '选择的需求计划单数据错误',
				'url' => '/s.php?module=requirement&action=list',
				'sec' => 2
			));
		}
		if($this->kclass->input['orderid']<=0){
			$orders=$this->kclass->DB->query("SELECT orderid,ordertitle FROM `order` WHERE killed=0 AND ifApprove=1 AND ifChooser=1 AND ifInbound=1 AND type='PO' ORDER BY created DESC");
			if($this->kclass->DB->numRows()){
				while($order=$this->kclass->DB->fetchArray($orders)){
					$orderSelect.='<option value="'.$order['orderid'].'"';
					if($order['orderid']==$reitem['mid']){
						$orderSelect.=' selected';
					}
					$orderSelect.='>'.$order['ordertitle'].'</option>';
				}
			}
			$body=<<<EOF
<form action="/s.php?module=requirement&action=updateExtra" method="get">
<input type="hidden" name="module" value="requirement" />
<input type="hidden" name="action" value="updateExtra" />
<input type="hidden" name="requirementid" value="{$this->kclass->input['requirementid']}" />
<table style="width:600px">
<thead><tr><th colspan=2>选择销售PO订单</th></tr></thead>
<tbody>
<tr><td>销售PO订单：</td><td><select name="orderid"><option value="0">请选择PO订单</option>{$orderSelect}</select></td></tr>
<tr class="center">
<td colspan=13><input type="submit" value="提交"><input type="reset" value="重置"></td>
</tr>
</tbody></table>
</form>
EOF;
	}else{
		$situation=$this->kclass->chooserMftSituation(array('name'=>'situationid','selectedid'=>$requirement['situationid']));
		$mode=$this->kclass->chooserMode(array('name'=>'modeid','selectedid'=>$requirement['modeid']));
		
		$orgItems=$this->kclass->DB->query("
			SELECT ri.itemid,ri.mitemid,ri.productid,ri.productitemid,ri.materialid,ri.materialitemid,ri.quantity,ri.dateline,
				order.orderid AS id,order.orderno AS no,order.shipmentDate AS dateline
			FROM requirementitem AS ri 
			LEFT JOIN `order` ON (order.orderid=ri.mid)
			WHERE ri.killed=0 AND ri.requirementid={$this->kclass->input['requirementid']} AND ri.module='order' AND ri.mid={$this->kclass->input['orderid']}
			ORDER BY ri.itemid ASC
		");
		$i=1;
		if($this->kclass->DB->numRows()){
			$mitemid=0;
			while($orgItem=$this->kclass->DB->fetchArray($orgItems)){
				$revise=$this->kclass->relatedRevise(array('module'=>'order','mid'=>$this->kclass->input['orderid']));// 获得相关版本
				if($revise['reviseid']=='')$revise['reviseid']=0;
				$product=$this->kclass->getProduct(array('productid'=>$orgItem['productid'],'itemid'=>$orgItem['productitemid']));
				$r.='<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').' small">
					<td><input type="hidden" name="orderid['.$i.']" value="'.$this->kclass->input['orderid'].'">
					<input type="hidden" name="orgItemId['.$i.']" value="'.$orgItem['itemid'].'">
					<td><a href="/s.php?module=order&action=view&orderid='.$orgItem['id'].'">'.$orgItem['no'].'</a> '.$revise['currencyRevise'].'</td>
					<td>'.date('Y-m-d',$orgItem['dateline']).'</td>
					<td><a href="/s.php?module=product&action=view&productid='.$orgItem['productid'].'">'.$product['title'].'</a> '.$product['standard'].'</td>
					<td>'.$orgItem['quantity'].' '.$orgItem['unit'].'</td>
					<td>'.$this->_getMaterial(array('materialid'=>$orgItem['materialid'],'line'=>$i,'productitemid'=>$orgItem['productitemid'],'quantity'=>$orgItem['quantity'])).'</td>
					<td><input type="text" name="orgItemDateline['.$i.']" id="shipmentDate'.$i.'" size="15" value="'.date('Y-m-d',$orgItem['dateline']).'"></td>
					<td><input type="text" name="orgItemRemark['.$i.']" size="25" value="'.$orgItem['remark'].'"></td>
					<td><input type="checkbox" name="itemKill['.$i.']" value="'.$orgItem['itemid'].'" ></td>
				</tr>';
				$id.='#shipmentDate'.$i.',';
				$i++;
				$mitemid.=','.$orgItem['mitemid'];
			}
			$r.='<tr class="middle bold"><td colspan=9>订单其他明细</td></tr>';
		}
		$items=$this->kclass->DB->query("
			SELECT item.itemid,item.productid,item.productitemid,item.productAttributes,item.price,item.quantity,item.dateline,
				p.title,
				m.materialid,m.standard,
				unit.title AS unit,
				order.orderid AS id,order.orderno AS no,order.shipmentDate AS dateline
			FROM `item`
			LEFT JOIN `product` AS p ON (p.productid=`item`.productid)
			LEFT JOIN `material` AS m ON (m.materialid=`item`.materialid)
			LEFT JOIN unit ON (unit.unitid=item.unitid)
			LEFT JOIN `order` ON (order.orderid=item.mid)
			WHERE `item`.killed=0 AND `item`.module='order' AND `item`.mid='{$this->kclass->input['orderid']}' AND `item`.itemid NOT IN ({$mitemid})
			ORDER BY `item`.itemid ASC,`item`.modified ASC
		");
		if($this->kclass->DB->numRows()){
			$r.='<tbody><tr class="center bold"><td width="15">ID</td><td width="130">订单</td><td width="80">订单交期</td><td>产品或配件</td><td width="80">毛需求数量</td><td width="350">选择要生产的物资</td><td width="100">需求交期<span class="red bold">*</span></td><td width="100">备注</td><td width="15">选</td></tr></tbody>';
			while($item=$this->kclass->DB->fetchArray($items)){
				$revise=$this->kclass->relatedRevise(array('module'=>'order','mid'=>$this->kclass->input['orderid']));// 获得相关版本
				if($revise['reviseid']=='')$revise['reviseid']=0;
				if($item['productid']>0 AND $item['productitemid']==0){
					$partitems=$this->kclass->DB->query("
						SELECT pi.quantity,pi.childid,p.title,p.materialid,m.materialno,m.title AS material,m.standard
						FROM partitem AS pi 
						LEFT JOIN product AS p ON (p.productid=pi.childid)
						LEFT JOIN material AS m ON (m.materialid=p.materialid)
						WHERE pi.killed=0 AND p.killed=0 AND pi.productid='".$item['productid']."'
						ORDER BY pi.ordering ASC
					");
					if($this->kclass->DB->numRows()){// 存在配件包的时候。列出配件包的内容及数量
						while($partitem=$this->kclass->DB->fetchArray($partitems)){
							$r.='<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').' small">
								<td><input type="hidden" name="orderid['.$i.']" value="'.$this->kclass->input['orderid'].'">
								<input type="hidden" name="itemId['.$i.']" value="'.$item['itemid'].'">
								<input type="hidden" name="reviseId['.$i.']" value="'.$revise['reviseid'].'">
								<input type="hidden" name="productId['.$i.']" value="'.$partitem['childid'].'" />
								<input type="hidden" name="productItemid['.$i.']" value="0" />
								<input type="hidden" name="totalQuantity['.$i.']" value="'.($item['quantity']*$partitem['quantity']).'">'.$i.'</td>
								<td><a href="/s.php?module=order&action=view&orderid='.$item['id'].'">'.$item['no'].'</a> '.$revise['currencyRevise'].'</td>
								<td>'.date('Y-m-d',$item['dateline']).'</td>
								<td><a href="/s.php?module=product&action=view&productid='.$partitem['childid'].'">'.$partitem['title'].'</a></td>
								<td>'.($item['quantity']*$partitem['quantity']).' '.$item['unit'].'</td>
								<td>'.$this->_getMaterial(array('materialid'=>$partitem['materialid'],'line'=>$i,'quantity'=>($item['quantity']*$partitem['quantity']))).'</td>
								<td><input type="text" name="itemDateline['.$i.']" id="shipmentDate'.$i.'" size="15" value="'.date('Y-m-d',$item['dateline']).'"></td>
								<td><input type="text" name="itemRemark['.$i.']" size="25" value="'.$item['remark'].'"></td>
								<td><input type="checkbox" name="itemSelect['.$i.']" value="'.$i.'" ></td>
							</tr>';
							$id.='#shipmentDate'.$i.',';
							$i++;
						}
					}else{
						$r.='<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').' small">
							<td><input type="hidden" name="orderid['.$i.']" value="'.$this->kclass->input['orderid'].'">
							<input type="hidden" name="itemId['.$i.']" value="'.$item['itemid'].'">
							<input type="hidden" name="reviseId['.$i.']" value="'.$revise['reviseid'].'">
							<input type="hidden" name="productId['.$i.']" value="'.$item['productid'].'" />
							<input type="hidden" name="productItemid['.$i.']" value="'.$item['productitemid'].'" />
							<input type="hidden" name="totalQuantity['.$i.']" value="'.$item['quantity'].'">'.$i.'</td>
							<td><a href="/s.php?module=order&action=view&orderid='.$item['id'].'">'.$item['no'].'</a> '.$revise['currencyRevise'].'</td>
							<td>'.date('Y-m-d',$item['dateline']).'</td>
							<td><a href="/s.php?module=product&action=view&productid='.$item['productid'].'">'.$item['title'].'</a></td>
							<td>'.$item['quantity'].' '.$item['unit'].'</td>
							<td>'.$this->_getMaterial(array('materialid'=>$item['materialid'],'line'=>$i,'quantity'=>$item['quantity'])).'</td>
							<td><input type="text" name="itemDateline['.$i.']" id="shipmentDate'.$i.'" size="15" value="'.date('Y-m-d',$item['dateline']).'"></td>
							<td><input type="text" name="itemRemark['.$i.']" size="25" value="'.$item['remark'].'"></td>
							<td><input type="checkbox" name="itemSelect['.$i.']" value="'.$i.'" ></td>
						</tr>';
						$id.='#shipmentDate'.$i.',';
						$i++;
					}
				}else{
					$product=$this->kclass->getProduct(array('productid'=>$item['productid'],'itemid'=>$item['productitemid']));
					$r.='<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').' small">
						<td><input type="hidden" name="orderid['.$i.']" value="'.$this->kclass->input['orderid'].'">
						<input type="hidden" name="itemId['.$i.']" value="'.$item['itemid'].'">
						<input type="hidden" name="reviseId['.$i.']" value="'.$revise['reviseid'].'">
						<input type="hidden" name="productId['.$i.']" value="'.$item['productid'].'" />
						<input type="hidden" name="productItemid['.$i.']" value="'.$item['productitemid'].'" />
						<input type="hidden" name="totalQuantity['.$i.']" value="'.$item['quantity'].'">'.$i.'</td>
						<td><a href="/s.php?module=order&action=view&orderid='.$item['id'].'">'.$item['no'].'</a> '.$revise['currencyRevise'].'</td>
						<td>'.date('Y-m-d',$item['dateline']).'</td>
						<td><a href="/s.php?module=product&action=view&productid='.$item['productid'].'">'.$product['title'].'</a> '.$product['standard'].'</td>
						<td>'.$item['quantity'].' '.$item['unit'].'</td>
						<td>'.$this->_getMaterial(array('materialid'=>$item['materialid'],'line'=>$i,'productitemid'=>$item['productitemid'],'quantity'=>$item['quantity'])).'</td>
						<td><input type="text" name="itemDateline['.$i.']" id="shipmentDate'.$i.'" size="15" value="'.date('Y-m-d',$item['dateline']).'"></td>
						<td><input type="text" name="itemRemark['.$i.']" size="25" value="'.$item['remark'].'"></td>
						<td><input type="checkbox" name="itemSelect['.$i.']" value="'.$i.'" ></td>
					</tr>';
					$id.='#shipmentDate'.$i.',';
					$i++;
				}
			}
		}

		$flow=$this->kclass->listFlow(array('module'=>'requirement','statusid'=>'1'));
$body=<<<EOF
<form action="/s.php?module=requirement&action=doupdateExtra" name="requirement" method="post">
<input type="hidden" name="module" value="requirement" />
<input type="hidden" name="action" value="doupdateExtra" />
<input type="hidden" name="requirementid" value="{$this->kclass->input['requirementid']}" />
<div><span class="right">{$flow}</span></div>
<table class="hundred">
<thead><tr><th colspan="6">新建需求计划单 <span class="small darkred">(订单需求)</span></th></tr></thead>
<tbody>
<tr class="odd">
<td width="100">编　　号：<span class="red bold">*</span></td><td width="350"><span class="middle bold darkred">{$requirement['requirementno']}</span> <span class="gray small">此为预估编号，保存后才可确定</span></td>
<td width="100">紧急情况：<span class="red bold">*</span></td><td width="350">{$situation}</td>
<td width="100">计 划 员：<span class="red bold">*</span></td><td><input type="text" name="planner"  value="{$requirement['realname']}"></td>
</tr>
<tr class="even">
<td>计算方式：<span class="red bold">*</span></td>
<td>{$mode}</td>
<td>备　　注：</td>
<td><textarea type="text" name="remark" style="width:300px;height:80px">{$requirement['remark']}</textarea></td><td></td><td></td>
</tr>
</tbody>
</table>
<table class="hundred">
<thead><tr><th colspan="12">计划生产明细</th></tr></thead>
<tbody><tr class="center bold"><td width="15">ID</td><td width="130">订单</td><td width="80">订单交期</td><td>产品或配件</td><td width="80">毛需求数量</td><td width="350">选择要生产的物资</td><td width="100">需求交期<span class="red bold">*</span></td><td width="100">备注</td><td width="15">删</td></tr></tbody><tbody class="small">
{$r}
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
		}
		$this->kclass->tbline+=4;
		$this->kclass->page['title'].='新建需求计划单';
		$this->kclass->page['onload'].='var dates=$(\''.$id.'\').datepicker({onSelect:function(selectedDate){instance=$(this).data(\'datepicker\'),date=$.datepicker.parseDate(instance.settings.dateFormat || $.datepicker._defaults.dateFormat,selectedDate,instance.settings);}});dc.tabhover();dc.tips();';
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - <a href="/s.php?module=requirement&action=list">需求计划单列表</a> - 新建需求计划单', 'right'=>'<a href="/s.php?module=requirement&action=list">返回列表</a>','body'=>$body));
	}
	function doupdateExtra(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e='<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			if($this->kclass->input['planner']==''){
				$e.='<li>请填写需求计划单中的 计划员</li>';
			}
			if($this->kclass->input['situationid']==''){
				$e.='<li>请填写需求计划单中的 紧急情况</li>';
			}
			if($this->kclass->input['modeid']==''){
				$e.='<li>请填写需求计划单中的 需求计算方式</li>';
			}
			$hasKill=$hasItem=0;
			// 需要修改的明细
			$count=count($this->kclass->input['orgItemId']);
			for($m=1;$m<=$count;$m++){
				if($this->kclass->input['itemKill'][$m]==''){
					if($this->kclass->input['itemDateline'][$m]==''){
						$e.='<li>请填写需求计划单中订单(PO)的生产明细[ID:'.$m.']的 需求日期</li>';
					}
					$jcount=count($this->kclass->input['itemVersionid'.$m]);
					$quantity=0;
					for($j=1;$j<=$jcount;$j++){
						if(intval($this->kclass->input['quantity'.$m][$j])>0){
							$hasKill=1;
							$quantity+=$this->kclass->input['quantity'.$m][$j];
						}
					}
					$item=$this->kclass->DB->queryFirst("SELECT p.attrid FROM item AS i LEFT JOIN product AS p ON (p.productid=i.productid) WHERE i.killed=0 AND i.itemid='".$this->kclass->input['itemId'][$m]."'");
					if($hasKill==0){
						$e.='<li>请填写至少一个需求计划单中订单(PO)的生产明细[ID:'.$m.']的 版本 需求数量</li>';
					}elseif($quantity!=$this->kclass->input['totalQuantity'][$m] AND $item['attrid']!=5){
						$e.='<li>需求计划单中订单(PO)的生产明细[ID:'.$m.'] 各版本的需求数量之和 必须和订单数量相等</li>';
					}
				}
			}
			// 可以增加的明细
			$count1=count($this->kclass->input['itemId']);
			for($i=1;$i<=$count1;$i++){
				if($this->kclass->input['itemSelect'][$i]>0){
					if($this->kclass->input['itemDateline'][$i]==''){
						$e.='<li>请填写需求计划单中订单(PO)的生产明细[ID:'.$i.']的 需求日期</li>';
					}
					$jcount=count($this->kclass->input['itemVersionid'.$i]);
					$quantity=0;
					for($j=1;$j<=$jcount;$j++){
						if(intval($this->kclass->input['quantity'.$i][$j])>0){
							$hasItem=1;
							$quantity+=$this->kclass->input['quantity'.$i][$j];
						}
					}
					$item=$this->kclass->DB->queryFirst("SELECT p.attrid FROM item AS i LEFT JOIN product AS p ON (p.productid=i.productid) WHERE i.killed=0 AND i.itemid='".$this->kclass->input['itemId'][$i]."'");
					if($hasItem==0){
						$e.='<li>请填写至少一个需求计划单中订单(PO)的生产明细[ID:'.$i.']的 版本 需求数量</li>';
					}elseif($quantity!=$this->kclass->input['totalQuantity'][$i] AND $item['attrid']!=5){
						$e.='<li>需求计划单中订单(PO)的生产明细[ID:'.$i.'] 各版本的需求数量之和 必须和订单数量相等</li>';
					}
				}
			}
			if($hasKill==0 AND $hasItem==0){
				$e='<li>计划生产必须至少有一条明细</li>';
			}
		}
		if($e){
			$this->kclass->messager(array(
				'title' => '新建需求计划单',
				'text' => '您在新建需求计划单的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$requirement=$this->kclass->DB->queryFirst("SELECT requirementid,requirementno FROM requirement WHERE requirementid={$this->kclass->input['requirementid']}");
		$this->kclass->DB->query("
			UPDATE requirement SET 
				situationid='{$this->kclass->input['situationid']}',
				modeid='{$this->kclass->input['modeid']}',
				planner='{$this->kclass->input['planner']}',
				remark='{$this->kclass->input['remark']}',
				modifier='{$this->kclass->user['userid']}',
				modified='".TIMENOW."'
			WHERE requirementid={$this->kclass->input['requirementid']}
		");
		for($m=1;$m<=$count;$m++){
			if($this->kclass->input['itemKill'][$m]>0 AND $this->kclass->input['itemKill'][$m]==$this->kclass->input['orgItemId'][$m]){
				$this->kclass->DB->query("UPDATE requirementitem SET killed='".TIMENOW."',killer='".$this->kclass->user['userid']."' WHERE itemid={$this->kclass->input['itemKill'][$m]}");
			}else{
				if($this->kclass->input['itemDateline'][$i]!=''){
					$sd=explode('-', $this->kclass->input['itemDateline'][$i]);
					$dateline=mktime(0,0,0,$sd[1],$sd[2],$sd[0]);
				}else{
					$dateline=0;
				}
				$ncount=count($this->kclass->input['itemVersionid'.$m]);
				for($n=1;$n<=$ncount;$n++){				
					if(intval($this->kclass->input['quantity'.$m][$n])>0){
						$materialItemid=$this->kclass->iif($this->kclass->input['materialItemid'.$m][$n]>0,$this->kclass->input['materialItemid'.$m][$n],0);
						$this->kclass->DB->query("
							UPDATE requirementitem SET 
								materialid='{$this->kclass->input['materialid'.$m][$n]}',
								materialitemid='{$materialItemid}',
								versionid='{$this->kclass->input['itemVersionid'.$m][$n]}',
								quantity={$this->kclass->input['quantity'.$m][$n]},
								dateline='{$dateline}',
								modified='".TIMENOW."',
								modifier='{$this->kclass->user['userid']}'
							WHERE itemid={$this->kclass->input['orgItemId'][$m]}
						");
					}
				}
			}
		}

		for($i=$m;$i<=$count1+$m;$i++){
			if($this->kclass->input['itemSelect'][$i]>0){
				if($this->kclass->input['itemDateline'][$i]!=''){
					$sd=explode('-', $this->kclass->input['itemDateline'][$i]);
					$dateline=mktime(0,0,0,$sd[1],$sd[2],$sd[0]);
				}else{
					$dateline=0;
				}
				$jcount=count($this->kclass->input['itemVersionid'.$i]);
				for($j=1;$j<=$jcount;$j++){				
					if(intval($this->kclass->input['quantity'.$i][$j])>0){
						$materialItemid=$this->kclass->iif($this->kclass->input['materialItemid'.$i][$j]>0,$this->kclass->input['materialItemid'.$i][$j],0);
						$this->kclass->DB->query("
							INSERT INTO requirementitem (requirementid,materialid,versionid,materialitemid,productid,productitemid,module,mid,mitemid,reviseid,quantity,dateline,remark,created,creator)
							VALUES ('".$this->kclass->input['requirementid']."','".$this->kclass->input['materialid'.$i][$j]."','".$this->kclass->input['itemVersionid'.$i][$j]."','".$materialItemid."','".$this->kclass->input['productId'][$i]."','".$this->kclass->input['productItemid'][$i]."','order','".$this->kclass->input['orderid'][$i]."','".$this->kclass->input['itemId'][$i]."','".$this->kclass->input['reviseId'][$i]."','".$this->kclass->input['quantity'.$i][$j]."','".$dateline."','".$this->kclass->input['itemRemark'][$i]."','".TIMENOW."','".$this->kclass->user['userid']."')
						");
					}
				}
			}
		}
		$this->kclass->messager(array(
			'title' => '修改需求计划单',
			'text' => '需求计划单 【<b>'.$requirement['requirementno'].'</b>】 已修改成功!返回添加申请生产物资',
			'url' => '/s.php?module=requirement&action=view&requirementid='.$$requirement['requirementid'],
			'sec' => 2
		));
	}
	//锁定需求计划 并更新待入待出
	function lock(){
		$rt=$this->kclass->DB->queryFirst("
			SELECT r.*,
				s.title AS status,
				u.username AS creator,
				us.username AS modifier
			FROM requirement AS r
			LEFT JOIN mftstatus AS s ON (s.statusid=r.statusid)
			LEFT JOIN user AS u ON (u.userid=r.creator)
			LEFT JOIN user AS us ON (u.userid=r.modifier)
			WHERE r.killed=0 AND r.requirementid='".$this->kclass->input['requirementid']."'
			LIMIT 0,1
		");
		if(!$rt OR $rt['statusid']!=1){
			$e='<li>数据错误，很抱歉~</li>';
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '锁定需求计划单',
				'text' => '锁定需求计划单<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
$body=<<<EOF
<form method="post" action="/s.php?module=requirement&action=dolock">
<input type="hidden" name="module" value="requirement">
<input type="hidden" name="action" value="dolock">
<input type="hidden" name="requirementid" value="{$this->kclass->input['requirementid']}">
<table>
<thead><tr><th colspan="6">确定要锁定需求计划单 <span class="bold darkred">{$rt['requirementno']}</span> 吗？</th></tr></thead>
<tbody>
<tr class="even">
	<td>锁定：<span class="bold red">*</span></td><td class="center"><input type="radio" name="statusid" value="2" checked="checked" />锁定　　　<input type="radio" name="statusid" value="1" />取消</td>
</tr>
<tr class="odd">
	<td colspan=6 class="center"><input type="submit" value=" 提交 " /><input type="reset" value="重置" /></td>
</tr>
</tbody>
</table>
</form>
EOF;
		$this->kclass->tbline+=4;
		$this->kclass->page['title'].='锁定需求计划单';
		$this->kclass->page['onload'].='dc.tabhover();';
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - <a href="/s.php?module=requirement&action=view&requirementid='.$this->kclass->input['requirementid'].'">查看需求计划单</a> - 锁定需求计划单', 'right'=>'<a href="/s.php?module=requirement&action=list">返回列表</a>　|　<a href="/s.php?module=requirement&action=view&requirementid='.$this->kclass->input['requirementid'].'">查看需求计划单</a>','body'=>$body));
	}
	//
	function dolock(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			$requirement=$this->kclass->DB->queryFirst("SELECT requirementno,statusid,typeid,modeid FROM requirement WHERE requirementid='".$this->kclass->input['requirementid']."'");
			if($this->kclass->input['statusid']==''){
				$e.='<li>请选择需求计划单中的 是否锁定 按钮。</li>';
			}
			if($requirement['statusid']!=1){
				$e.='<li>需求计划单 '.$requirement['requirementno'].' 已经锁定。</li>';
			}
		}
		if($e){
			$this->kclass->messager(array(
				'title' => '锁定需求计划单',
				'text' => '您在锁定需求计划单的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		if($this->kclass->input['statusid']==2){
			 if($requirement['modeid']==0 OR $requirement['modeid']==1){ //如果是非毛需求bom方式的话，那么再锁定时，将需求的物资插入到mrp中。	否则，将会在锁定之后，新增到mrp中
				//插入MRP
				$materialid=$materialItemid=$versionid=array();
				$items=$this->kclass->DB->query("
					SELECT ri.*,
						m.min,
						mv.qualified,mv.planInbound,mv.planOutbound
					FROM requirementitem AS ri
					LEFT JOIN material AS m ON (m.materialid=ri.materialid)
					LEFT JOIN materialversion AS mv ON (mv.versionid=ri.versionid)
					WHERE ri.killed=0 AND m.originid=1 AND ri.requirementid='".$this->kclass->input['requirementid']."'
					ORDER BY ri.ordering ASC
				");
				if($this->kclass->DB->numRows()){
					while($item=$this->kclass->DB->fetchArray($items)){
						if(in_array($item['versionid'],$versionid)){
							$quantity[$item['versionid']]+=$item['quantity'];
							$dateline[$item['versionid']]=$this->kclass->iif($dateline[$item['versionid']]>$item['dateline'],$dateline[$item['versionid']],$item['dateline']);
						}else{
							$materialid[$item['versionid']]=$item['materialid'];
							$materialItemid[$item['versionid']]=$item['materialitemid'];
							$versionid[$item['versionid']]=$item['versionid'];
							$quantity[$item['versionid']]=$this->kclass->iif($requirement['modeid']==0,$item['quantity']-$item['qualified']+$item['min']-$item['planInbound']+$item['planOutbound'],$item['quantity']);
							$dateline[$item['versionid']]=$item['dateline'];
						}
					}
				}
				foreach($versionid as $val){
					if($quantity[$val]>0){
						$this->kclass->DB->query("
							INSERT INTO `mrp` (requirementid,materialid,versionid,materialitemid,netQuantity,requiredate,created,creator)
							VALUE ('".$this->kclass->input['requirementid']."','".$materialid[$val]."','".$val."','".$materialItemid[$val]."','".$quantity[$val]."','".$dateline[$val]."','".TIMENOW."','".$this->kclass->user['userid']."')
						");
					}
				}
				$arr=$this->kclass->mrpList(array('materialid'=>$materialid,'materialitemid'=>$materialItemid,'versionid'=>$versionid,'quantity'=>$quantity,'dateline'=>$dateline));
				if($arr){
					foreach($arr as $val){
						if($val['netQuantity']>0){
							$this->kclass->DB->query("
								INSERT INTO `mrp` (requirementid,materialid,versionid,materialitemid,netQuantity,requiredate,created,creator)
								VALUE ('".$this->kclass->input['requirementid']."','".$val['materialid']."','".$val['versionid']."','".$val['materialitemid']."','".$val['netQuantity']."','".$val['dateline']."','".TIMENOW."','".$this->kclass->user['userid']."')
							");
						}
					}
				}
				//订单需求成品预出
				if($requirement['typeid']==1){
					$requirements=$this->kclass->DB->query("
						SELECT ri.versionid,ri.quantity
						FROM requirementitem AS ri
						LEFT JOIN material AS m ON (m.materialid=ri.materialid)
						WHERE ri.killed=0 AND ri.requirementid='".$this->kclass->input['requirementid']."'
					");
					if($this->kclass->DB->numRows()){
						while($requirement=$this->kclass->DB->fetchArray($requirements)){
							$this->kclass->DB->query("
								UPDATE materialversion SET
									planOutbound=planOutbound+'".$requirement['quantity']."'
								WHERE versionid='".$requirement['versionid']."'
							");
						}
					}
				}
				//半成品预入
				$mrpitems=$this->kclass->DB->query("
					SELECT mp.*
					FROM `mrp` AS mp
					LEFT JOIN material AS m ON (m.materialid=mp.materialid)
					WHERE mp.killed=0 AND m.originid=1 AND mp.ifChooser=0 AND mp.requirementid='".$this->kclass->input['requirementid']."'
				");
				if($this->kclass->DB->numRows()){
					while($mrpitem=$this->kclass->DB->fetchArray($mrpitems)){
						$child=$this->kclass->bomChildList(array('parentid'=>$mrpitem['materialid'],'materialitemid'=>$mrpitem['materialitemid'],'versionid'=>$mrpitem['versionid'],'quantity'=>$mrpitem['netQuantity'],'loopNum'=>1));
						//原料预出
						if($child['bom']){
							foreach($child['bom'] as $val){
								if($val['ifGeneral']==0){
									$this->kclass->DB->query("
										UPDATE materialversion SET
											planOutbound=planOutbound+'".$val['quantity']."'
										WHERE versionid='".$val['versionid']."'
									");
								}
							}
						}
						// 待入
						$this->kclass->DB->query("
							UPDATE materialversion SET
								planInbound=planInbound+'".$mrpitem['netQuantity']."'
							WHERE versionid='".$mrpitem['versionid']."'
						");
					}
				}
			}
			//锁定
			$lock='锁定成功';
			$this->kclass->DB->query("
				UPDATE requirement SET
					statusid='".$this->kclass->input['statusid']."',
					locker='".$this->kclass->user['userid']."',
					locked='".TIMENOW."'
				WHERE requirementid='".$this->kclass->input['requirementid']."'
			");
			$this->kclass->actionLog(array('module'=>'requirement','mid'=>$this->kclass->input['requirementid'],'note'=>'锁定了需求计划单'));
		}elseif($this->kclass->input['statusid']==1){
			$lock='锁定操作取消';
		}
		$this->kclass->messager(array(
			'title' => '锁定需求计划单',
			'text' => '需求计划单 <b>'.$requirement['requirementno'].'</b> '.$lock.'！',
			'url' => '/s.php?module=requirement&action=view&requirementid='.$this->kclass->input['requirementid'],
			'sec' => 2
		));
	}
	//
	//解锁需求计划 保存历史版本 并还原到未锁定状态 还原待入待出
	function unlock(){
		$rt=$this->kclass->DB->queryFirst("
			SELECT r.*,
				s.title AS status,
				u.username AS creator,
				us.username AS modifier
			FROM requirement AS r
			LEFT JOIN mftstatus AS s ON (s.statusid=r.statusid)
			LEFT JOIN user AS u ON (u.userid=r.creator)
			LEFT JOIN user AS us ON (u.userid=r.modifier)
			WHERE r.killed=0 AND r.requirementid='".$this->kclass->input['requirementid']."'
			LIMIT 0,1
		");
		if(!$rt OR $rt['statusid']!=2){
			$e.='<li>数据错误，很抱歉~</li>';
		}else{
			$mrp=$this->kclass->DB->queryFirst("
				SELECT mrpid
				FROM mrp
				WHERE killed=0 AND ifChooser=0 AND requirementid='".$this->kclass->input['requirementid']."'
				LIMIT 0,1
			");
			$ifmrp=$this->kclass->DB->queryFirst("
				SELECT mrpid
				FROM mrp
				WHERE killed=0 AND requirementid='".$this->kclass->input['requirementid']."'
				LIMIT 0,1
			");
			/*if($mrp=='' && $ifmrp){
				$e.='<li>需求计划单已经全部投产，无法解锁！</li>';
			}*/
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '解锁需求计划单',
				'text' => '解锁需求计划单<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
$body=<<<EOF
<form method="post" action="/s.php?module=requirement&action=dounlock">
<input type="hidden" name="module" value="requirement">
<input type="hidden" name="action" value="dounlock">
<input type="hidden" name="requirementid" value="{$this->kclass->input['requirementid']}">
<table>
<thead><tr><th colspan="6">确定要解锁需求计划单 <span class="bold darkred">{$rt['requirementno']}</span> 吗？</th></tr></thead>
<tbody>
<tr class="even">
	<td>解锁：<span class="bold red">*</span></td><td class="center"><input type="radio" name="statusid" value="1" checked="checked" />解锁　　　<input type="radio" name="statusid" value="2" />取消</td>
</tr>
<tr class="odd">
	<td colspan=6 class="center"><input type="submit" value=" 提交 " /><input type="reset" value="重置" /></td>
</tr>
</tbody>
</table>
</form>
EOF;
		$this->kclass->tbline+=4;
		$this->kclass->page['title'].='解锁需求计划单';
		$this->kclass->page['onload'].='dc.tabhover();';
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - <a href="/s.php?module=requirement&action=view&requirementid='.$this->kclass->input['requirementid'].'">查看需求计划单</a> - 解锁需求计划单', 'right'=>'<a href="/s.php?module=requirement&action=list">返回列表</a>　|　<a href="/s.php?module=requirement&action=view&requirementid='.$this->kclass->input['requirementid'].'">查看需求计划单</a>','body'=>$body));
	}
	//
	function dounlock(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			$rt=$this->kclass->DB->queryFirst("SELECT requirementid,requirementno,statusid,typeid,modeid FROM requirement WHERE requirementid='".$this->kclass->input['requirementid']."'");
			if($this->kclass->input['statusid']==''){
				$e.='<li>请选择需求计划单中的 是否解锁 按钮。</li>';
			}
			if($rt['statusid']==1){
				$e.='<li>需求计划单 '.$rt['requirementno'].' 已经解锁。</li>';
			}
		}
		if($e){
			$this->kclass->messager(array(
				'title' => '解锁需求计划单',
				'text' => '您在解锁需求计划单的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		if($this->kclass->input['statusid']==1){
			//保存历史版本
			$revise = $this->_getRequirementHtml();
			$this->kclass->insertReviseInfo(array('module'=>'requirement', 'mid'=>$rt['requirementid'],'revise'=> $revise,'remark'=>''));
			//去除订单需求成品预出
			if($rt['typeid']==1){
				$items=$this->kclass->DB->query("
					SELECT ri.versionid,ri.quantity
					FROM requirementitem AS ri
					LEFT JOIN material AS m ON (m.materialid=ri.materialid)
					WHERE ri.killed=0 AND ri.requirementid='".$this->kclass->input['requirementid']."'
				");
				if($this->kclass->DB->numRows()){
					while($item=$this->kclass->DB->fetchArray($items)){
						$this->kclass->DB->query("
							UPDATE materialversion SET
								planOutbound=planOutbound-'".$item['quantity']."'
							WHERE versionid='".$item['versionid']."'
						");
					}
				}
			}
		//去除未投产半成品预入、去除原料预出
		$mrpitems=$this->kclass->DB->query("
			SELECT mp.*
			FROM `mrp` AS mp
			LEFT JOIN material AS m ON (m.materialid=mp.materialid)
			WHERE mp.killed=0 AND m.originid=1 AND mp.ifChooser=0 AND mp.requirementid='".$this->kclass->input['requirementid']."'
		");
		if($this->kclass->DB->numRows()){
			while($mrpitem=$this->kclass->DB->fetchArray($mrpitems)){
				$child=$this->kclass->bomChildList(array('parentid'=>$mrpitem['materialid'],'materialitemid'=>$mrpitem['materialitemid'],'versionid'=>$mrpitem['versionid'],'quantity'=>$mrpitem['netQuantity'],'loopNum'=>1));
				//原料预出
				if($child['bom']){
					foreach($child['bom'] as $val){
						if($val['ifGeneral']==0){
							$this->kclass->DB->query("
								UPDATE materialversion SET
									planOutbound=planOutbound-'".$val['quantity']."'
								WHERE versionid='".$val['versionid']."'
							");
						}
					}
				}
			}
		}
		//去除未投产MRP 已投产MRP增加版次
		$this->kclass->DB->query("
			UPDATE `mrp` SET
				killed=".TIMENOW.",killer='".$this->kclass->user['userid']."'
			WHERE killed=0 AND ifChooser=0 AND requirementid='".$this->kclass->input['requirementid']."'
		");
		$this->kclass->DB->query("
			UPDATE `mrp` SET
				revision=revision+1
			WHERE killed=0 AND ifChooser=1 AND requirementid='".$this->kclass->input['requirementid']."'
		");
		//解锁
		$lock='解锁成功';
		$this->kclass->DB->query("
			UPDATE requirement SET
				statusid='".$this->kclass->input['statusid']."',
				locker='0',
				locked='0'
			WHERE requirementid='".$this->kclass->input['requirementid']."'
		");
		$this->kclass->actionLog(array('module'=>'requirement','mid'=>$this->kclass->input['requirementid'],'note'=>'解锁了需求计划单,并生成历史版本'));
		}elseif($this->kclass->input['statusid']==2){
			$lock='解锁操作取消';
		}
		$this->kclass->messager(array(
			'title' => '解锁需求计划单',
			'text' => '需求计划单 <b>'.$rt['requirementno'].'</b> '.$lock.'！',
			'url' => '/s.php?module=requirement&action=view&requirementid='.$this->kclass->input['requirementid'],
			'sec' => 2
		));
	}
	//已作废 看需求开放功能 --gaowenfei 2013/11/25 9:01
	function verify(){
		$requirement=$this->kclass->DB->queryFirst("
			SELECT p.requirementid,p.requirementno,p.applicant,p.ifVerify,p.purpose,p.remark,p.created,p.modified,p.amount,
				c.title AS currency,
				u.username AS creator,
				us.username AS modifier
			FROM requirement AS p
			LEFT JOIN currency AS c ON (c.currencyid=p.currencyid)
			LEFT JOIN user AS u ON (u.userid=p.creator)
			LEFT JOIN user AS us ON (u.userid=p.modifier)
			WHERE p.killed=0 AND p.requirementid='".$this->kclass->input['requirementid']."'
			LIMIT 0,1
		");
		if(!$requirement AND $requirement['ifVerify']!=0){
			$e='<li>数据错误，很抱歉~</li>';
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '审核需求计划单',
				'text' => '审核需求计划单<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$requirement['created']=date('Y-n-d',$requirement['created']);
		$verify=$this->kclass->verify($requirement['ifVerify']);
		if($requirement['modified']!=0)$modify='，由'.$requirement['modifier'].'于'.date('Y-n-d',$requirement['modified']).'修改';
		if($requirement['ifVerify']==0){
			$verifyOperate='|　<a href="/s.php?module=requirement&action=update&requirementid='.$requirement['requirementid'].'">修改</a>　|　<span class="small">审核：</span><a href="/s.php?module=requirement&action=verify&requirementid='.$requirement['requirementid'].'" onclick="return confirm(\'你确定要对这个需求计划单 '.$requirement['requirementno'].' 审核通过吗？\');">通过</a>　<a href="/s.php?module=requirement&action=unverify&requirementid='.$requirement['requirementid'].'" onclick="return confirm(\'你确定要对这个需求计划单 '.$requirement['requirementno'].' 审核不通过吗？\');">不通过</a>';
		}elseif($requirement['ifVerify']==1){
			$verifyOperate='';
		}
		//申请的物资
		$items=$this->kclass->DB->query("
			SELECT p.*,
				m.materialno,m.title,m.standard,
				s.supplierid,s.title AS supplier,
				u.title AS unit,
				c.title AS currency,c.symbol
			FROM requirementitem AS p
			LEFT JOIN material AS m ON (m.materialid=p.materialid)
			LEFT JOIN supplier AS s ON (s.supplierid=p.supplierid)
			LEFT JOIN unit AS u ON (u.unitid=p.unitid)
			LEFT JOIN currency AS c ON (c.currencyid=p.currencyid)
			WHERE p.killed=0 AND p.requirementid='".$requirement['requirementid']."'
			ORDER BY p.ordering ASC
		");
		if($this->kclass->DB->numRows()){
			$i=1;
			while($item=$this->kclass->DB->fetchArray($items)){
				$addMaterial='';
				if($requirement['ifVerify']==1 AND $item['materialid']==0){
					$addMaterial='<a target="_blank" href="/s.php?module=material&action=add&itemid='.$item['itemid'].'">新增该物资</a>';
				}

				$item['arrivalTime']=date('Y-m-d',$item['arrivalTime']);
				$class=$this->kclass->iif($i%2==0,'class="even"','class="odd"');
				if($item['module']=='requirementApply'){
					$mitem=$this->kclass->DB->queryFirst("SELECT pi.*,pa.applyno,pa.requirementApplyid FROM preinbounditem AS pi LEFT JOIN requirementapply AS pa ON (pi.mid=pa.requirementApplyid) WHERE pi.killed=0 AND pi.module='requirementApply' AND pi.itemid='".$item['mitemid']."'");
					$type="需求计划";
					$typeno='<a href="/s.php?module=requirementapply&action=view&requirementApplyid='.$mitem['requirementApplyid'].'">'.$mitem['applyno'].'</a>';
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
<div class="title"><span class="right small gray">由{$requirement['creator']}于{$requirement['created']}建立{$modify}。</span>{$requirement['requirementno']}</div>
<table class="hundred">
<thead><tr><th colspan="4">需求计划单</th></tr></thead>
<tbody>
<tr class="odd">
<td width="100">编　　号：</td><td width="475">{$requirement['requirementno']}　（{$verify}）</td>
<td width="100">采 购 员：</td><td>{$requirement['applicant']}</td>
</tr>
<tr class="even">
<td>总　　价：</td><td>{$requirement['amount']}</td>
<td>申请时间：</td><td>{$requirement['created']}</td>
</tr>
<tr class="odd">
<td>目　　的：</td><td>{$requirement['purpose']}</td>
<td>备　　注：</td><td>{$requirement['remark']}</td>
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
<form method="post" action="/s.php?module=requirement&action=doverify">
<input type="hidden" name="module" value="requirement">
<input type="hidden" name="action" value="doverify">
<input type="hidden" name="requirementid" value="{$this->kclass->input['requirementid']}">
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
		$this->kclass->page['title'].='审核需求计划单';
		$this->kclass->page['onload'].='dc.tabhover();';
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - <a href="/s.php?module=requirement&action=view&requirementid='.$this->kclass->input['requirementid'].'">查看需求计划单</a> - 审核需求计划单', 'right'=>'<a href="/s.php?module=requirement&action=list">返回列表</a>　|　<a href="/s.php?module=requirement&action=view&requirementid='.$this->kclass->input['requirementid'].'">查看需求计划单</a>','body'=>$body));
	}
	//
	function doverify(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			$requirement=$this->kclass->DB->queryFirst("SELECT requirementno FROM requirement WHERE requirementid='".$this->kclass->input['requirementid']."'");
			if($this->kclass->input['ifVerify']!=1 AND $this->kclass->input['ifVerify']!=-1){
				$e.='<li>请选择需求计划单中的 是否通过审核 按钮。</li>';
			}
		}
		if($e){
			$this->kclass->messager(array(
				'title' => '审核需求计划单',
				'text' => '您在审核需求计划单的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$this->kclass->DB->query("
			UPDATE requirement SET
				ifVerify='".$this->kclass->input['ifVerify']."',
				verifier='".$this->kclass->user['userid']."',
				verified='".TIMENOW."',
				verifyRemark='".$this->kclass->input['verifyRemark']."'
			WHERE requirementid='".$this->kclass->input['requirementid']."'
		");
		if($this->kclass->input['ifVerify']==1){
			$verify='已通过';
		}elseif($this->kclass->input['ifVerify']==-1){
			$verify='未通过';
		}
		$this->kclass->messager(array(
			'title' => '审核需求计划单',
			'text' => '需求计划单 【<b>'.$requirement['requirementno'].'</b>】 '.$verify.'审核!返回查看需求计划单',
			'url' => '/s.php?module=requirement&action=view&requirementid='.$this->kclass->input['requirementid'],
			'sec' => 2
		));
	}
	//删除需求计划 需要修改需求计划明细为空
	function kill(){
		if($this->kclass->input['requirementid']<=0){
			$this->kclass->boinkIt('/s.php?module=requirement');
		}
		if($this->kclass->input['rt']!='list' AND $this->kclass->input['confirm'] == 0){
			$this->kclass->boinkIt('/s.php?module=requirement&action=view&requirementid='.$this->kclass->input['requirementid']);
		}
		if($this->kclass->input['requirementid'] < 0){
			$e = '<li>请从正确的页面进行操作，如果此问题持续存在，请联系网站管理员。</li>';
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '删除需求计划单',
				'text' => '您在删除需求计划单的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		if($this->kclass->input['rt']=='view'){
			$rt = '&action=view&requirementid='.$this->kclass->input['requirementid'];
		}elseif($this->kclass->input['rt']=='list'){
			$rt = '#'.$this->kclass->input['requirementid'];
		}
		$requirement=$this->kclass->DB->queryFirst("
			SELECT requirementno
			FROM requirement
			WHERE requirementid='".$this->kclass->input['requirementid']."'
		");
		if($requirement){
			$this->kclass->DB->query("
				UPDATE `requirement`
				SET killed=".TIMENOW.",killer='".$this->kclass->user['userid']."'
				WHERE requirementid='".$this->kclass->input['requirementid']."'
			");
			$this->kclass->actionLog(array('module'=>'requirement','mid'=>$this->kclass->input['requirementid'],'note'=>'删除了需求计划单'));
			$this->kclass->messager(array(
				'title' => '删除需求计划单成功',
				'text' => '需求计划单 <b>'.$requirement['requirementno'].'</b> 已成功被标记为删除!',
				'url' => '/s.php?module=requirement'.$rt,
				'sec' => 2
			));
		}else{
			$this->kclass->messager(array(
				'title' => '删除需求计划单失败',
				'text' => '您要删除的需求计划单，不存在！',
				'url' => '/s.php?module=requirement'.$rt,
				'sec' => 3
			));
		}
	}

	//
	function remove(){
		if($this->kclass->input['requirementid']<=0){
			$this->kclass->boinkIt('/s.php?module=requirement');
		}
		$requirement = $this->kclass->DB->queryFirst("
			SELECT requirementno
			FROM `requirement`
			WHERE requirementid='".$this->kclass->input['requirementid']."'
		");
		$ri=$this->kclass->DB->queryFirst("
			SELECT itemid
			FROM `requirementitem`
			WHERE killed=0 AND requirementid='".$this->kclass->input['requirementid']."'
		");
		if($ri){
			$e.='<li>需求计划单中存在需求明细，请进入修改页面把相应明细删除再进行操作！</li>';
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '删除需求计划单',
				'text' => '需求计划单无法删除<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
$body = <<<EOF
<form action="/s.php?module=requirement&action=kill" name="requirement" method="post">
<input type="hidden" name="module" value="requirement">
<input type="hidden" name="action" value="kill">
<input type="hidden" name="requirementid" value="{$this->kclass->input['requirementid']}">
<input type="hidden" name="rt" value="{$this->kclass->input['rt']}">
<table><thead>
<thead>
<tr>
<th>删除需求计划单：{$requirement['requirementno']}</th>
</tr>
</thead>
<tbody>
<tr class="even">
<td class="middle">你确定要删除需求计划单: <a href="/s.php?module=requirement&action=view&requirementid={$this->kclass->input['requirementid']}" class="big bold" target="_blank">{$requirement['requirementno']}</a> 吗?</td>
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
		$this->kclass->page['title'] .= '删除 - '.$requirement['requirementno'];
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 删除需求计划单 - '.$requirement['requirementno'], 'right' => '<a href="/s.php?module=requirement">返回列表</a>', 'body'=>$body));
	}

	//还原删除的需求计划
	function revival(){
		if($this->kclass->input['requirementid']<=0){
			$this->kclass->boinkIt('/s.php?module=requirement');
		}
		if($this->kclass->input['rt']!='list' AND $this->kclass->input['confirm']==0){
			$this->kclass->boinkIt('/s.php?module=requirement&action=view&requirementid='.$this->kclass->input['requirementid']);
		}
		if($this->kclass->input['requirementid']<0){
			$e = '<li>请从正确的页面进行操作，如果此问题持续存在，请联系网站管理员。</li>';
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '恢复需求计划单',
				'text' => '您在恢复需求计划单的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$requirement = $this->kclass->DB->queryFirst("
			SELECT requirementno
			FROM `requirement`
			WHERE requirementid='".$this->kclass->input['requirementid']."'
		");
		if($this->kclass->input['rt']=='view'){
			$rt = '&action=view&requirementid='.$this->kclass->input['requirementid'];
		}elseif($this->kclass->input['rt']=='list'){
			$rt = '#'.$this->kclass->input['requirementid'];
		}
		if($requirement){
			$this->kclass->DB->query("
				UPDATE `requirement`
				SET killed=0,killer=0
				WHERE requirementid='".$this->kclass->input['requirementid']."'
			");
			$this->kclass->actionLog(array('module'=>'requirement','mid'=>$this->kclass->input['requirementid'],'note'=>'恢复了需求计划单'));
			$this->kclass->messager(array(
				'title' => '恢复需求计划单成功',
				'text' => '需求计划单 <b>'.$requirement['requirementno'].'</b> 已成功被恢复！',
				'url' => '/s.php?module=requirement'.$rt,
				'sec' => 2
			));
		}else{
			$this->kclass->messager(array(
				'title' => '恢复需求计划单',
				'text' => '您要恢复的需求计划单不存在！',
				'url' => '/s.php?module=requirement'.$rt,
				'sec' => 3
			));
		}
	}

	//
	function restore(){
		if($this->kclass->input['requirementid']<=0){
			$this->kclass->boinkIt('/s.php?module=requirement');
		}
		$requirement = $this->kclass->DB->queryFirst("
			SELECT requirementno
			FROM `requirement`
			WHERE requirementid='".$this->kclass->input['requirementid']."'
		");
$body = <<<EOF
<form action="/s.php?module=requirement&action=revival" name="requirement" method="post">
<input type="hidden" name="module" value="requirement">
<input type="hidden" name="action" value="revival">
<input type="hidden" name="requirementid" value="{$this->kclass->input['requirementid']}">
<input type="hidden" name="rt" value="{$this->kclass->input['rt']}">
<table><thead>
<thead>
<tr>
<th>恢复需求计划单：{$requirement['requirementno']}</th>
</tr>
</thead>
<tbody>
<tr class="even">
<td class="middle">你确定要恢复需求计划单: <a href="/s.php?module=requirement&action=view&requirementid={$this->kclass->input['requirementid']}" class="big bold" target="_blank">{$requirement['requirementno']}</a> 吗?</td>
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
		$this->kclass->page['title'] .= ' - 恢复 - '.$requirement['requirementno'];
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 恢复需求计划单 - '.$requirement['requirementno'], 'right' => '<a href="/s.php?module=requirement">返回列表</a>', 'body'=>$body));
	}
	
	/******************************************** private function ***********************************************************************/
	//历史版本获取html保存
	function _getRequirementHtml(){
		if(!$this->kclass->input['requirementid'] > 0)$this->kclass->boinkIt('/s.php?module=requirement');
			$requirement=$this->kclass->DB->queryFirst("
				SELECT r.requirementid,r.requirementno,r.statusid,r.modeid,r.orderChange,r.planner,r.remark,r.locked,r.killed,r.modified,r.created,
					mt.title AS type,
					msi.title AS situation,
					s.title AS status,
					m.realname AS creator,
					me.realname AS modifier,
					mem.realname AS locker
				FROM requirement AS r
				LEFT JOIN mftstatus AS s ON (s.statusid=r.statusid)
				LEFT JOIN `mfttype` AS mt ON (mt.typeid=r.typeid)
				LEFT JOIN `mftsituation` AS msi ON (msi.situationid=r.situationid)
				LEFT JOIN member AS m ON (m.userid=r.creator)
				LEFT JOIN member AS me ON (me.userid=r.modifier)
				LEFT JOIN member AS mem ON (mem.userid=r.locker)
				WHERE r.requirementid='".$this->kclass->input['requirementid']."'
				LIMIT 0,1
			");
		if(!$requirement)$this->kclass->boinkIt('/s.php?module=requirement');
		$requirement['created']=date('Y-m-d H:i:s',$requirement['created']);
		if($requirement['modified']!=0)$modify='，由 '.$requirement['modifier'].' 于 '.date('Y-m-d H:i:s',$requirement['modified']).' 修改';
		if($requirement['statusid']==2){
			$pstatus='，由 '.$requirement['locker'].' 于 '.date('Y-m-d H:i:s',$requirement['locked']).' 锁定';
		}
		//计划的物资
		$items=$this->kclass->DB->query("
			SELECT ri.module,ri.mid,ri.mitemid,ri.materialid,ri.versionid,ri.materialitemid,ri.statusid,ri.reviseid,ri.dateline,ri.quantity,
				m.materialno,m.title,m.standard,m.min,m.originid,
				mi.itemno,
				mv.title AS version,mv.`change`,mv.qualified,mv.planInbound,mv.planOutbound,
				materialorigin.title AS origin,
				r.version AS reviseversion
			FROM requirementitem AS ri
			LEFT JOIN material AS m ON (m.materialid=ri.materialid)
			LEFT JOIN materialitem AS mi ON (mi.itemid=ri.materialitemid)
			LEFT JOIN materialversion AS mv ON (mv.versionid=ri.versionid)
			LEFT JOIN materialorigin ON (materialorigin.originid=m.originid)
			LEFT JOIN revise AS r ON (r.reviseid=ri.reviseid)
			WHERE ri.killed=0 AND ri.requirementid='".$requirement['requirementid']."'
			ORDER BY ri.module ASC,ri.itemid ASC,m.materialno ASC
		");
		if($this->kclass->DB->numRows()){
			$i=1;$ifbom='';
			while($item=$this->kclass->DB->fetchArray($items)){
				$rtnitem[]=$item;
				$usable=$item['mvqualified']+$item['planInbound']-$item['planOutbound'];
				$item['dateline']=date('Y-m-d',$item['dateline']);
				// 订单或者样品单 版本
				$orderrevise=$this->kclass->relatedRevise(array('module'=>$item['module'],'mid'=>$item['mid']));
				$orderversion='(R'.($item['reviseversion']+1).')</span>';
				if($item['module']=='order'){
					$order=$this->kclass->DB->queryFirst("SELECT orderno,orderid FROM `order` WHERE orderid='".$item['mid']."'");
					$orderno=$this->kclass->iif($orderrevise['reviseid']==$item['reviseid'],'<a href="/s.php?module=order&action=view&orderid='.$item['mid'].'">'.$order['orderno'].'</a> <span class="small green">','<a href="/s.php?module=order&action=revise&reviseid='.$orderrevise['reviseid'].'">'.$order['orderno'].'</a> <span class="small darkred">').$orderversion;
				}elseif($item['module']=='sample'){
					$order=$this->kclass->DB->queryFirst("SELECT sampleno,sampleid FROM `sample` WHERE sampleid='".$item['mid']."'");
					$orderno=$this->kclass->iif($orderrevise['reviseid']==$item['reviseid'],'<a href="/s.php?module=sample&action=view&sampleid='.$item['mid'].'">'.$order['sampleno'].'</a> <span class="small green">','<a href="/s.php?module=sample&action=revise&reviseid='.$orderrevise['reviseid'].'">'.$order['sampleno'].'</a> <span class="small darkred">').$orderversion;
				}elseif($item['module']=='stock'){
					$orderno='库存需求';
				}
				//判断是否存在子BOM
				if($item['originid']==1){
					$child=$this->kclass->bomChildList(array('parentid'=>$item['materialid'],'materialitemid'=>$item['materialitemid'],'versionid'=>$item['versionid'],'loopNum'=>1,'getCount'=>1));
					$ifbom=$this->kclass->iif($child==1,'是','否');
				}else{
					$ifbom='-';
				}
				//需求计划明细
				$materialInfo=$this->kclass->getMaterial(array('materialid'=>$item['materialid'],'itemid'=>$item['materialitemid'])); // 获得物资信息
				$itemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').' '.$this->kclass->iif($item['originid']==1,$this->kclass->iif($ifbom=='否','bgRed',''),'bgOrange').' small">
					<td>'.$i.'</td>
					<td>'.$orderno.'</td>
					<td><a href="'.$materialInfo['url'].'">'.$materialInfo['no'].'</a></td>
					<td>'.$materialInfo['material'].'</td>
					<td>'.$materialInfo['standard'].'</td>
					<td>'.$this->kclass->iif($item['change']=='',$item['version'],'<a class="tip" href="#" title="( '.$item['change'].' )">'.$item['version'].$this->kclass->iif($item['itemno']!='',' ('.$item['itemno'].') ','').'</a>').'</td>
					<td>'.$ifbom.'</td>
					<td>'.$item['origin'].'</td>
					<td>'.$item['quantity'].'</td>
					<td>'.$item['dateline'].'</td>
					<td>'.$item['remark'].'</td>
					<td>'.$item['mvqualified'].'</td>
					<td>'.$item['planInbound'].'</td>
					<td>'.$item['planOutbound'].'</td>
					<td>'.$item['min'].'</td>
					<td>'.$usable.'</td>
					</tr>';
				$i++;
				if($rt['modeid']==2 AND $item['originid']==1 AND $requirement['statusid']==1){//毛需求(BOM)
					$bomItemtr.='<tr><td colspan="16" class="bold"><a href="'.$materialInfo['url'].'">'.$materialInfo['no'].'</a>　'.$materialInfo['material'].'　'.$materialInfo['standard'].'　(版本：'.$item['version'].')</td></tr>';
					if($child==1){
						$k=1;
						$child=$this->kclass->bomChildList(array('parentid'=>$item['materialid'],'materialitemid'=>$item['materialitemid'],'versionid'=>$item['versionid'],'loopNum'=>1));
						foreach($child['bom'] as $val){
							$usable=$val['qualified']+$val['planInbound']-$val['planOutbound'];
							$bomItemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').' small '.$this->kclass->iif($val['ifGeneral']==0,$this->kclass->iif($val['quantity']>$val['qualified'],'bgYellow',''),'').'">
								<td>'.$k.'</td>
								<td><a href="'.$val['url'].'">'.$val['no'].'</a>　'.$val['title'].'　'.$val['standard'].'</td>
								<td>'.$val['version'].'</td>
								<td>'.$val['origin'].'</td>
								<td>'.$this->kclass->iif($val['ifGeneral']==0,$val['quantity'],'通用辅料').'</td>
								<td>'.$val['qualified'].'</td>
								<td>'.$val['planInbound'].'</td>
								<td>'.$val['planOutbound'].'</td>
								<td>'.$val['min'].'</td>
								<td>'.$usable.'</td>
								</tr>';
								$k++;
						}
					}else{
						$bomItemtr.='<tr><td colspan="16" class="darkred">物资 [<a href="'.$materialInfo['url'].'">'.$materialInfo['no'].'</a>　'.$materialInfo['material'].'　'.$materialInfo['standard'].'</td>] BOM(子) 不存在！</td></tr>';
					}
					unset($child);
				}
			}
		}
		if($requirement['statusid']>1){
			$mrpitems=$this->kclass->DB->query("
				SELECT mrp.mrpid,mrp.requirementid,mrp.materialid,mrp.materialitemid,mrp.versionid,mrp.netQuantity,mrp.requiredate,mrp.ifChooser,mrp.revision,
					mv.title AS version
				FROM mrp
				LEFT JOIN materialversion AS mv ON (mv.versionid=mrp.versionid)
				WHERE mrp.killed=0 AND mrp.requirementid='".$this->kclass->input['requirementid']."'
				ORDER BY mrp.mrpid ASC
			");
			if($this->kclass->DB->numRows()){
				$i=1;$j=1;$k=1;
				$taskItemtr='<table class="hundred"><thead><tr><th colspan="12">MRP明细 - 生产任务物资<span class="right"><a href="/s.php?module=task&action=add">新增生产任务</a></span></th></tr></thead><tbody><tr class="even center"><td width="15">ID</td><td width="800">物资</td><td width="30">版本</td><td width="150">来源</td><td width="150">净需求数量</td><td width="150">需求日期</td><td width="30">版次</td><td width="100">操作</td></tr></tbody><tbody class="small">';
				$purchaseItemtr='<table class="hundred"><thead><tr><th colspan="12">MRP明细 - 采购申请物资<span class="right"><a href="/s.php?module=requirement&action=addPurchaseApply&requirementid='.$this->kclass->input['requirementid'].'">新增采购申请</a></span></th></tr></thead><tbody><tr class="even center"><td width="15">ID</td><td width="800">物资</td><td width="30">版本</td><td width="150">来源</td><td width="150">净需求数量</td><td width="150">需求日期</td><td width="100">操作</td></tr></tbody><tbody class="small">';
				$entrustItemtr='<table class="hundred"><thead><tr><th colspan="12">MRP明细 - 外发加工物资<span class="right"><a href="/s.php?module=requirement&action=addEntrust&requirementid='.$this->kclass->input['requirementid'].'">新增外发加工申请</a></span></th></tr></thead><tbody><tr class="even center"><td width="15">ID</td><td width="800">物资</td><td width="30">版本</td><td width="150">来源</td><td width="150">净需求数量</td><td width="150">需求日期</td><td width="100">操作</td></tr></tbody><tbody class="small">';
				while($mrpitem=$this->kclass->DB->fetchArray($mrpitems)){
					$materialInfo=$this->kclass->getMaterial(array('materialid'=>$mrpitem['materialid'],'itemid'=>$mrpitem['materialitemid']));
					if($materialInfo['originid']<=1){
						$iftask=1;
						$taskItemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').'">
							<td>'.$i.'</td>
							<td><a href="'.$materialInfo['url'].'">'.$materialInfo['no'].'</a>　'.$materialInfo['material'].'　'.$materialInfo['standard'].'</td>
							<td>'.$mrpitem['version'].'</td>
							<td>'.$materialInfo['origin'].'</td>
							<td>'.$mrpitem['netQuantity'].'</td>
							<td>'.date('Y-m-d',$mrpitem['requiredate']).'</td>
							<td>'.$mrpitem['revision'].'</td>
							<td>'.$this->kclass->iif($mrpitem['ifChooser']>0,'已投产','待选择').'</td>
						</tr>';
						$i++;
					}elseif($materialInfo['originid']==2){
						$ifpurchase=1;
						$purchaseItemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').'">
							<td>'.$j.'</td>
							<td><a href="'.$materialInfo['url'].'">'.$materialInfo['no'].'</a>　'.$materialInfo['material'].'　'.$materialInfo['standard'].'</td>
							<td>'.$mrpitem['version'].'</td>
							<td>'.$materialInfo['origin'].'</td>
							<td>'.$mrpitem['netQuantity'].'</td>
							<td>'.date('Y-m-d',$mrpitem['requiredate']).'</td>
							<td>'.$this->kclass->iif($mrpitem['ifChooser']>0,'已申请','待选择').'</td>
						</tr>';
						$j++;
					}elseif($materialInfo['originid']==3){
						$ifentrust=1;
						$entrustItemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').'">
							<td>'.$k.'</td>
							<td><a href="'.$materialInfo['url'].'">'.$materialInfo['no'].'</a>　'.$materialInfo['material'].'　'.$materialInfo['standard'].'</td>
							<td>'.$mrpitem['version'].'</td>
							<td>'.$materialInfo['origin'].'</td>
							<td>'.$mrpitem['netQuantity'].'</td>
							<td>'.date('Y-m-d',$mrpitem['requiredate']).'</td>
							<td>'.$this->kclass->iif($mrpitem['ifChooser']>0,'已申请','待选择').'</td>
						</tr>';
						$k++;
					}
				}
				if($iftask!=1)$taskItemtr.='<tr><td colspan="14" class="darkred center">暂无相关记录！</td></tr>';
				if($ifpurchase!=1)$purchaseItemtr.='<tr><td colspan="14" class="darkred center">暂无相关记录！</td></tr>';
				if($ifentrust!=1)$entrustItemtr.='<tr><td colspan="14" class="darkred center">暂无相关记录！</td></tr>';
				$taskItemtr.='</tbody></table>';
				$purchaseItemtr.='</tbody></table>';
				$entrustItemtr.='</tbody></table>';
			}
		}
		$mode=$this->kclass->iif($requirement['modeid']==0,'净需求',$this->kclass->iif($requirement['modeid']==1,'毛需求','毛需求(BOM)'));
		$situation='<span class="'.$this->kclass->iif($requirement['situationid']==2,'darkred middle',$this->kclass->iif($requirement['situationid']==3,'red middle bold','')).'">'.$requirement['situation'].'</span>';
		$revise=$this->kclass->relatedRevise(array('module'=>'requirement','mid'=>$requirement['requirementid']));
		$revise['arr']=serialize(array_merge($requirement,$rtnitem));

$revise['html']=<<<EOF
<div class="title"><span class="right small gray">由{$requirement['creator']}于{$requirement['created']}建立{$modify}{$pstatus}。</span>{$requirement['requirementno']}{$revise['currencyRevise']}</div>
<table class="hundred">
<thead><tr><th colspan="4">需求计划单</th></tr></thead>
<tbody>
<tr class="odd">
<td width="100">编号：</td><td width="475">{$requirement['requirementno']}　（{$requirement['status']}）</td>
<td>需求计算方式：</td><td><span class="bold darkred">{$mode}</span></td>
</tr>
<tr class="even">
<td width="100">计划员：</td><td>{$requirement['planner']}</td>
<td width="100">计划来源：</td><td>{$requirement['type']}</td>
</tr>
<tr class="odd">
<td width="100">紧急情况：</td><td>{$situation}</td>
<td>计划时间：</td><td>{$requirement['created']}</td>
</tr>
<tr class="even">
<td width="100">备注：</td><td>{$requirement['remark']}</td>
<td></td><td></td>
</tr>
</tbody>
</table>
<table class="hundred">
<thead><tr><th colspan="16">需求计划明细</th></tr></thead>
<tbody>
<tr class="center even">
<td width="15">ID</td><td width="140">订单编号</td><td>物资编号</td><td>物资名称</td><td>物资规格</td><td>版本</td><td>存在BOM(子)</td><td>来源</td><td>毛需求数量</td><td>需求交期</td><td>备注</td><td>良品库存</td><td>待入库存</td><td>待出库存</td><td>安全库存</td><td>计划可用</td>
</tr>
{$itemtr}
</tbody>
</table>
{$taskItemtr}
{$bomItemtr}
{$purchaseItemtr}
{$entrustItemtr}
EOF;
		return $revise;
	}
	// 获得materialitem列表
	function _getMaterial($b){
		if($b['line']>0){
			$line=$b['line'];
		}
		if($b['quantity']>0){
			$quantity=$b['quantity'];
		}
		if($b['productitemid']>0){
			$sql='SELECT mv.versionid,mv.title AS version,mv.materialid,mv.quantity,mv.qualified,mv.disqualified,
				mi.itemid,mi.itemno,mi.attributevalue,
				m.materialid,m.title AS material
			FROM materialversion AS mv
			LEFT JOIN materialitem AS mi ON (mi.itemid=mv.itemid)
			LEFT JOIN material AS m ON (m.materialid=mv.materialid)
			WHERE mv.killed=0 AND mi.killed=0 AND mi.productitemid='.$b['productitemid'].'
			ORDER BY mv.ifProduce DESC,mv.produceCount DESC';
		}else{
			$sql='SELECT mv.versionid,mv.title AS version,mv.quantity,mv.qualified,mv.disqualified,
				m.materialid,m.materialno,m.title AS material,m.standard
			FROM materialversion AS mv
			LEFT JOIN material AS m ON (m.materialid=mv.materialid)
			WHERE mv.killed=0 AND m.killed=0 AND m.materialid='.$b['materialid'].'
			ORDER BY mv.ifProduce DESC,mv.produceCount DESC';
		}
		$items=$this->kclass->DB->query($sql);
		if($this->kclass->DB->numRows()){
			$i=1;
			while($item=$this->kclass->DB->fetchArray($items)){
				$materialno=$this->kclass->iif($item['itemno']!='',$item['itemno'],$item['materialno']);
				$material=$materialno.'　'.$item['material'];
				if($item['attributevalue']){
					$valueId=explode(",",$item['attributevalue']);
					foreach($valueId as $key=>$val){
						$attribute=$this->kclass->DB->queryFirst("
							SELECT an.nameid,an.title AS name,
								av.valueid,av.title AS value
							FROM  attributename AS an
							LEFT JOIN attributevalue AS av ON (av.nameid=an.nameid)
							WHERE an.killed=0 AND an.hidden=0 AND av.killed=0 AND av.valueid='".$val."'
							ORDER BY an.ordering ASC
						");
						if($attribute['name'] AND $attribute['value']){
							$material.='　'.$attribute['name'].'：'.$attribute['value'];
						}
					}
				}else{
					$material.='　'.$item['standard'];
				}
				if($item['itemid']>0){
					$related=$this->kclass->DB->queryFirst("
						SELECT mi.relatedid,mv.qualified
						FROM materialitem AS mi 
						LEFT JOIN materialversion AS mv ON (mi.relatedid=mv.itemid) 
						WHERE mi.itemid='".$item['itemid']."' 
						LIMIT 0,1
					");
					$showQuantity=' 包：<b>'.$item['qualified'].'</b> 产：<b>'.$related['qualified'].'</b>';
				}
				
				if($b['selected']!=''){// update
					$hidden='';$quantity=0;
					if($b['selected']['materialitemid']==$item['itemid'] OR ($b['selected']['materialitemid']==0 AND $b['selected']['materialid']==$item['materialid'])){
						$hidden='<input type="hidden" name="itemId'.$line.'['.$i.']" value="'.$b['selected']['itemid'].'" >';
						$quantity=$b['selected']['quantity'];
					}
					$r.=$hidden.'<input type="hidden" name="oldMaterialid'.$line.'['.$i.']" value="'.$b['selected']['materialid'].'" /><input type="hidden" name="oldMaterialItemid'.$line.'['.$i.']" value="'.$b['selected']['materialitemid'].'" /><input type="hidden" name="oldVersionid'.$line.'['.$i.']" value="'.$item['versionid'].'" /><a title="'.$material.'" class="tip" href="#">'.$materialno.'</a>　版本：<a href="#" class="tip" title="良品：'.$item['qualified'].'　不良品：'.$item['disqualified'].'">'.$item['version'].'</a>'.$showQuantity.'　需求数：<input type="text" name="oldQuantity'.$line.'['.$i.']" value="'.$quantity.'" size=7 /><br/>';
				}else{// add
					$r.='<input type="hidden" name="materialid'.$line.'['.$i.']" value="'.$item['materialid'].'" /><input type="hidden" name="materialItemid'.$line.'['.$i.']" value="'.$item['itemid'].'" /><input type="hidden" name="itemVersionid'.$line.'['.$i.']" value="'.$item['versionid'].'" /><a title="'.$material.'" class="tip" href="#">'.$materialno.'</a>　版本：<a href="#" class="tip" title="良品：'.$item['qualified'].'　不良品：'.$item['disqualified'].'">'.$item['version'].'</a>'.$showQuantity.'　需求数：<input type="text" name="quantity'.$line.'['.$i.']" value="'.$quantity.'" size=7 /><br/>';
					$quantity=0;
				}

				$i++;
			}
		}
		return $r;
	}
	// 计划生产明细的表单。
	function _formItem($b){
		static $i=1;
		switch($b['module']){
			case 'order':
				$leftJoin='LEFT JOIN `order` ON (order.orderid=item.mid)';
				$field='order.orderid AS id,order.orderno AS no,order.shipmentDate AS dateline';
			break;
			case 'sample':
				$leftJoin='LEFT JOIN `sample` ON (sample.sampleid=item.mid)';
				$field='sample.sampleid AS id,sample.sampleno AS no,sample.deliveryDate AS dateline';
			break;
		}
		$items=$this->kclass->DB->query("
			SELECT item.itemid,item.productid,item.productitemid,item.productAttributes,item.price,item.quantity,item.dateline,
				p.title,
				m.materialid,m.standard,
				brand.title AS brand,
				packing.title AS packing,
				unit.title AS unit,
				{$field}
			FROM `item`
			LEFT JOIN `product` AS p ON (p.productid=`item`.productid)
			LEFT JOIN `material` AS m ON (m.materialid=`item`.materialid)
			LEFT JOIN packing ON (packing.packingid=item.packingid)
			LEFT JOIN brand ON (brand.brandid=item.brandid)
			LEFT JOIN unit ON (unit.unitid=item.unitid)
			{$leftJoin}
			WHERE `item`.killed=0 AND `item`.module='".$b['module']."' AND `item`.mid='".$b['mid']."'
			ORDER BY `item`.itemid ASC,`item`.modified ASC
		");
		if($this->kclass->DB->numRows()){
			while($item=$this->kclass->DB->fetchArray($items)){
				$revise=$this->kclass->relatedRevise(array('module'=>$b['module'],'mid'=>$b['mid']));// 获得相关版本
				if($revise['reviseid']=='')$revise['reviseid']=0;
				if($item['productid']>0 AND $item['productitemid']==0){
					$partitems=$this->kclass->DB->query("
						SELECT pi.quantity,pi.childid,p.title,p.materialid,m.materialno,m.title AS material,m.standard
						FROM partitem AS pi 
						LEFT JOIN product AS p ON (p.productid=pi.childid)
						LEFT JOIN material AS m ON (m.materialid=p.materialid)
						WHERE pi.killed=0 AND p.killed=0 AND pi.productid='".$item['productid']."'
						ORDER BY pi.ordering ASC
					");
					if($this->kclass->DB->numRows()){// 存在配件包的时候。列出配件包的内容及数量
						while($partitem=$this->kclass->DB->fetchArray($partitems)){
							$r.='<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').' small">
								<td><input type="hidden" name="m['.$i.']" value="'.$b['module'].'">
								<input type="hidden" name="mid['.$i.']" value="'.$b['mid'].'">
								<input type="hidden" name="itemId['.$i.']" value="'.$item['itemid'].'">
								<input type="hidden" name="reviseId['.$i.']" value="'.$revise['reviseid'].'">
								<input type="hidden" name="productId['.$i.']" value="'.$partitem['childid'].'" />
								<input type="hidden" name="productItemid['.$i.']" value="0" />
								<input type="hidden" name="totalQuantity['.$i.']" value="'.($item['quantity']*$partitem['quantity']).'">'.$i.'</td>
								<td><a href="/s.php?module='.$b['module'].'&action=view&'.$b['module'].'id='.$item['id'].'">'.$item['no'].'</a> '.$revise['currencyRevise'].'</td>
								<td>'.date('Y-m-d',$item['dateline']).'</td>
								<td><a href="/s.php?module=product&action=view&productid='.$partitem['childid'].'">'.$partitem['title'].'</a></td>
								<td>'.($item['quantity']*$partitem['quantity']).' '.$item['unit'].'</td>
								<td>'.$this->_getMaterial(array('materialid'=>$partitem['materialid'],'line'=>$i,'quantity'=>($item['quantity']*$partitem['quantity']))).'</td>
								<td><input type="text" name="itemDateline['.$i.']" id="shipmentDate'.$i.'" size="15" value="'.date('Y-m-d',$item['dateline']).'"></td>
								<td><input type="text" name="itemRemark['.$i.']" size="25" value="'.$item['remark'].'"></td>
							</tr>';
							$id.='#shipmentDate'.$i.',';
							$i++;
						}
					}else{
						$r.='<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').' small">
							<td><input type="hidden" name="m['.$i.']" value="'.$b['module'].'">
							<input type="hidden" name="mid['.$i.']" value="'.$b['mid'].'">
							<input type="hidden" name="itemId['.$i.']" value="'.$item['itemid'].'">
							<input type="hidden" name="reviseId['.$i.']" value="'.$revise['reviseid'].'">
							<input type="hidden" name="productId['.$i.']" value="'.$item['productid'].'" />
							<input type="hidden" name="productItemid['.$i.']" value="'.$item['productitemid'].'" />
							<input type="hidden" name="totalQuantity['.$i.']" value="'.$item['quantity'].'">'.$i.'</td>
							<td><a href="/s.php?module='.$b['module'].'&action=view&'.$b['module'].'id='.$item['id'].'">'.$item['no'].'</a> '.$revise['currencyRevise'].'</td>
							<td>'.date('Y-m-d',$item['dateline']).'</td>
							<td><a href="/s.php?module=product&action=view&productid='.$item['productid'].'">'.$item['title'].'</a></td>
							<td>'.$item['quantity'].' '.$item['unit'].'</td>
							<td>'.$this->_getMaterial(array('materialid'=>$item['materialid'],'line'=>$i,'quantity'=>$item['quantity'])).'</td>
							<td><input type="text" name="itemDateline['.$i.']" id="shipmentDate'.$i.'" size="15" value="'.date('Y-m-d',$item['dateline']).'"></td>
							<td><input type="text" name="itemRemark['.$i.']" size="25" value="'.$item['remark'].'"></td>
						</tr>';
						$id.='#shipmentDate'.$i.',';
						$i++;
					}
				}else{
					$product=$this->kclass->getProduct(array('productid'=>$item['productid'],'itemid'=>$item['productitemid']));
					$r.='<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').' small">
						<td><input type="hidden" name="m['.$i.']" value="'.$b['module'].'">
						<input type="hidden" name="mid['.$i.']" value="'.$b['mid'].'">
						<input type="hidden" name="itemId['.$i.']" value="'.$item['itemid'].'">
						<input type="hidden" name="reviseId['.$i.']" value="'.$revise['reviseid'].'">
						<input type="hidden" name="productId['.$i.']" value="'.$item['productid'].'" />
						<input type="hidden" name="productItemid['.$i.']" value="'.$item['productitemid'].'" />
						<input type="hidden" name="totalQuantity['.$i.']" value="'.$item['quantity'].'">'.$i.'</td>
						<td><a href="/s.php?module='.$b['module'].'&action=view&'.$b['module'].'id='.$item['id'].'">'.$item['no'].'</a> '.$revise['currencyRevise'].$hidden.'</td>
						<td>'.date('Y-m-d',$item['dateline']).'</td>
						<td><a href="/s.php?module=product&action=view&productid='.$item['productid'].'">'.$product['title'].'</a> '.$product['standard'].'</td>
						<td>'.$item['quantity'].' '.$item['unit'].'</td>
						<td>'.$this->_getMaterial(array('materialid'=>$item['materialid'],'line'=>$i,'productitemid'=>$item['productitemid'],'quantity'=>$item['quantity'])).'</td>
						<td><input type="text" name="itemDateline['.$i.']" id="shipmentDate'.$i.'" size="15" value="'.date('Y-m-d',$item['dateline']).'"></td>
						<td><input type="text" name="itemRemark['.$i.']" size="25" value="'.$item['remark'].'"></td>
					</tr>';
					$id.='#shipmentDate'.$i.',';
					$i++;
				}
			}
		}
		return $r;
	}
}
?>