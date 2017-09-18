<?php
	//
	class inventory{
		var $kclass;
		function autorun(){
			$this->baseurl='<a href="/s.php?">首页</a> ';
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
				case 'chooseMtype':
					return $this->chooseMtype();
				case 'additems':
					return $this->additems();
					break;
				case 'updateMaterial':
					return $this->updateMaterial();
					break;
				case 'mmlist':
					return $this->mmlist();
					break;
				case 'verify':
					return $this->verify();
					break;
				case 'kill':
					return $this->kill();
					break;
				case 'dokill':
					return $this->dokill();
					break;
				case 'killitem':
					return $this->killItem();
					break;
				case 'update':
					return $this->update();
					break;
				case 'ajust':
					return $this->ajust();
					break;
				case 'doajust':
					return $this->doajust();
					break;
				case 'doupdate':
					return $this->doupdate();
					break;
				case 'updateitem':
					return $this->updateItem();
					break;
				case 'doupdateitem':
					return $this->doupdateItem();
					break;
				default:
					return $this->mmlist();
			}
		}
		//
		function ajust(){		
			$body = <<<EOF
<form action="/s.php?module=inventory&action=doajust" method="post">
<table>
	<tr>
		<td>调整批号记录：<input type="radio" name="thev" value="1" /></td>
		<td>调整批次记录：<input type="radio" name="thev" value="2" /></td>
		<td>调整收发记录：<input type="radio" name="thev" value="3" /></td>
	</tr>
	<tr><td colspan="3"><input type="submit" value="  提 交  " /></td></tr>
</table>
</form>
EOF;

			$this->kclass->page['title'].='库存调整';
			$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - 库存调整','body'=>$body));
		}
		function doajust(){
			if($this->kclass->input['thev']==3){
				$dinoutrecord = $this->kclass->DB->query("DELETE FROM inoutrecord WHERE module='inventory' AND mid=1");
			}else{
				$batchids = $this->kclass->DB->query("SELECT batchid FROM batch WHERE batchno LIKE '%141231%'");
				if($this->kclass->DB->numRows()){
					if($this->kclass->input['thev']==1){
						while($batchid = $this->kclass->DB->fetchArray($batchids)){						
							$dbarcode = $this->kclass->DB->query("DELETE FROM barcode WHERE batchid={$batchid['batchid']}");
						}
					}elseif($this->kclass->input['thev']==2){
						while($batchid = $this->kclass->DB->fetchArray($batchids)){	
							$dbatch = $this->kclass->DB->query("DELETE FROM batch WHERE batchid={$batchid['batchid']}");
						}							
					}
				}
				if($dbarcode==true){
					$this->kclass->messager(array(
							'title' => '删除批号',
							'text' => '删除批号成功！',
							'url' => '/s.php?module=inventory&action=ajust',
							'sec' => 3
					));
				}
				if($dbatch==true){
					$this->kclass->messager(array(
							'title' => '删除批号',
							'text' => '删除批号成功！',
							'url' => '/s.php?module=inventory&action=ajust',
							'sec' => 3
					));	
				}
			}
			if($dinoutrecord==true){
				$this->kclass->messager(array(
						'title' => '删除收发记录',
						'text' => '删除收发记录成功！',
						'url' => '/s.php?module=inventory&action=ajust',
						'sec' => 3
				));
			}
		}
		//盘点列表
		function mmlist(){
			$this->kclass->input['orderby']=$this->kclass->iif($this->kclass->input['orderby'],$this->kclass->input['orderby'],'inventoryid');
			$this->kclass->input['direction']=$this->kclass->iif($this->kclass->input['direction'],$this->kclass->input['direction'],'ASC');
			$condition=$this->kclass->iif($this->kclass->input['show']=='all','1=1','i.killed=0');
			$inventorys=$this->kclass->DB->query("
				SELECT i.inventoryid,i.title,i.inventorier,inventoryType,i.dateline,i.created,i.modified,i.killed,
					u.username AS creator,
					us.username AS modifier
				FROM inventory AS i
				LEFT JOIN `user` AS u ON (u.userid=i.creator)
				LEFT JOIN `user` AS us ON (us.userid=i.modifier)
				WHERE ".$condition."
				ORDER BY ".$this->kclass->input['orderby']." ".$this->kclass->input['direction']."
			");
			if($this->kclass->DB->numRows()){
				$body='<ul class="mmlist clear" id="mmlist">';
				$i=1;
				while($inventory=$this->kclass->DB->fetchArray($inventorys)){
					$inventory['dateline']=date('Y-m-d',$inventory['dateline']);
					$inventory['nextround']=date('Y-m-d',$inventory['nextround']);
					$inventory['created']=date('Y-m-d H:i',$inventory['created']);
					$categoryid=explode(',',$inventory['parentlist']);
					foreach($categoryid as $key=>$value){
						if($key!=0){
							$category=$this->kclass->DB->queryFirst("SELECT title FROM category WHERE parentid=19 AND categoryid='".$value."' LIMIT 0,1");
							if($category['title'])
								$inventory['category']=$category['title'].' '.$inventory['category'];
						}
					}
					$body.='<li title="由 '.$inventory['creator'].' 建于 '.$created.$this->kclass->iif($inventory['modifier']!='', '，'.$inventory['modifier'].' 改于 '.date('Y-m-d H:i',$inventory['modified']), '').'"'.$this->kclass->iif($i%4==0, ' class="end"', '').'>
					<div class="mmlistt">
						<span class="right normal">';
					if($inventory['killed']>0){
						$body .= '<a href="/s.php?module=inventory&action=revival&inventoryid='.$inventory['inventoryid'].'&rt=list" onclick="return confirm(\'你确定要恢复这个盘点 '.$inventory['title'].' 吗？\');">恢复</a>';
					}else{
						$body .= '<a href="/s.php?module=inventory&action=kill&inventoryid='.$inventory['inventoryid'].'&rt=list" onclick="return confirm(\'你确定要删除这个盘点 '.$inventory['title'].' 吗？\');">删</a> <a href="/s.php?module=inventory&action=update&inventoryid='.$inventory['inventoryid'].'&rt=list">改</a>';
					}
					if($inventory['inventoryType']!=''){
						if($inventory['inventoryType']=='must'){
							$inventoryType = '必盘';
						}else{
							$inventoryType = '抽盘';
						}
					}
					$body.='</span>
						 <a href="/s.php?module=inventory&action=view&inventoryid='.$inventory['inventoryid'].'&mtypeid=1">'.$inventory['title'].'</a><br><span class="small gray right">'. $inventory['created'].'</span>盘点人：'.$inventory['inventorier'].'</div>
					<div class=mmlistb>
						<div title="'.$inventory['orderno'].'"><span class="small gray">盘点类型：'.$inventoryType.'</span></div>
						<div><span class="small gray">盘点日期：'.$inventory['dateline'].'</span></div>
						<div class="small clear"><span class=right title="由 '.$inventory['creator'].' 建于 '.$inventory['created'].'">由 '.$inventory['creator'].'  建于 '.$inventory['created'].'</span></div></div></li>';
					$i++;
				}
				$body.='</ul>';
			}else{
				$body='暂无相关记录';
			}

			$this->kclass->page['title'].='盘点列表';
			$this->kclass->page['onload'].='dc.listhover();';
			$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - 盘点列表','right' => '<span class="small">显示方式：</span><a href="/s.php?module=inventory">默认列表</a>　<a href="/s.php?module=inventory&action=list&show=all">所有<span class="small gray">(包括删除)</span></a>　|　<a href="/s.php?module=inventory&action=add">新建</a>','body'=>$body));
		}
		//
		function view(){
			$mtypeid = $this->kclass->input['mtypeid'];
			$inventory=$this->kclass->DB->queryFirst("
				SELECT i.*,
					u.username AS creator,
					u.username AS modifier
				FROM inventory AS i
				LEFT JOIN user AS u ON (u.userid=i.creator)
				LEFT JOIN user AS us ON (us.userid=i.modifier)
				WHERE i.killed=0 AND i.inventoryid='".$this->kclass->input['inventoryid']."'
			");
			if($inventory){
				$categoryid=explode(',',$inventory['parentlist']);
				foreach($categoryid as $key=>$value){//获得物资分类
					if($key!=0){
						$category=$this->kclass->DB->queryFirst("SELECT title FROM category WHERE categoryid='".$value."' LIMIT 0,1");
						if($category['title']){
							$inventory['category']=$category['title'].' '.$inventory['category'];
						}
					}
				}
				$preInventory=$this->kclass->DB->queryFirst("
					SELECT dateline
					FROM inventory
					WHERE killed=0 AND inventoryid<>'".$inventory['inventoryid']."'
					ORDER BY created DESC
					LIMIT 0,1
				");
				$inventory['dateline']=date('Y-n-d',$inventory['dateline']);
				$inventory['nextround']=date('Y-n-d',$inventory['nextround']);
				$inventory['created']=date('Y-n-d',$inventory['created']);
				$preInventory['dateline']=$this->kclass->iif($preInventory['dateline'],date('Y-n-d',$preInventory['dateline']),'暂无');
				if($inventory['modified']!=0)$modify='，由'.$inventory['modifier'].'于'.date('Y-n-d',$inventory['modified']).'修改';
				if($inventory['inventoryType']=='choose'){
					$inventoryType='抽盘';
				}elseif($inventory['inventoryType']=='must'){
					$inventoryType='必盘';
				}
				
				if($mtypeid!=''){
					$condition = 'AND materialType='.$mtypeid.'';	
					if($mtypeid==1){
						$titleSuffix = '【良品盘点】';
					}elseif($mtypeid==-1){
						$titleSuffix = '【不良品盘点】';
					}
				}else{
					$condition = '';	
					$titleSuffix = '【全部盘点】';
				}
				$ifverify = $this->kclass->DB->queryFirst("SELECT verify1,verify2,ifverify FROM inventory WHERE inventoryid={$this->kclass->input['inventoryid']}");
				//判断审核按钮的显示状态
				if($ifverify['ifverify']<>2){
					if($this->kclass->input['mtypeid']==1 AND $ifverify['verify1']==0){
							$verify = '<button>审核良品盘点</button>';
							$continue = '<span class="small right"><a href="/s.php?module=inventory&action=additems&mtypeid=1&inventoryid='.$inventory['inventoryid'].'">继续盘点</a></span>';
					}elseif($this->kclass->input['mtypeid']==-1 AND $ifverify['verify2']==0){
							$verify = '<button>审核不良品盘点</button>';
							$continue = '<span class="small right"><a href="/s.php?module=inventory&action=additems&mtypeid=1&inventoryid='.$inventory['inventoryid'].'">继续盘点</a></span>';
					}
				}elseif($ifverify['ifverify']==2){
					$verify = '';
					$continue = '';
				}
				//判断基本信息中审核状态
				if($ifverify['verify1']==1 AND $ifverify['verify2']==1){
					$verifyStatus = '审核完毕';
				}elseif($ifverify['verify1']==1 AND $ifverify['verify2']==0){
					$verifyStatus = '良品：<span style="color:green">已审核</span> 不良品：<span style="color:red">未审核</span>';
				}elseif($ifverify['verify1']==0 AND $ifverify['verify2']==1){
					$verifyStatus = '良品：<span style="color:red">未审核</span> 不良品：<span style="color:green">已审核</span>';
				}else{
					$verifyStatus = '未审核';
				}
				//get material list for inventory
				$items=$this->kclass->DB->query("
					SELECT i.*,
						m.materialid,m.materialno,m.title,m.standard,m.uptolerance,m.belowtolerance
					FROM inventoryitem AS i
					LEFT JOIN material AS m ON (m.materialid=i.materialid)
					WHERE i.killed=0 AND inventoryid='".$this->kclass->input['inventoryid']."' ".$condition."
					ORDER BY created ASC
				");
				if($this->kclass->DB->numRows()){
					$i = 1;
					$itemtr='<table class="hundred"><thead><tr><th colspan="13">已完成盘点的物资<span class="small"><a href="/s.php?module=inventory&action=view&mtypeid=1&inventoryid='.$inventory['inventoryid'].'"> [良品] </a><a href="/s.php?module=inventory&action=view&mtypeid=-1&inventoryid='.$inventory['inventoryid'].'"> [不良品] </a></span>'.$continue.'</th></tr></thead><tbody class="small"><tr class="center even"><td>ID</td><td width="50">物资编号</td><td>物资名称</td><td>规格</td><td>库存</td><td>初盘</td><td>复盘</td><td>盘点数</td><td>盈亏</td><td width="100">允差率</td><td>备注</td><td>原因分析</td><td>操作</td></tr>';
					while($item=$this->kclass->DB->fetchArray($items)){
						$materialno = $item['materialno'];
						if($item['materialitemid']>0){
							$query = $this->kclass->DB->queryFirst("SELECT itemno FROM materialitem WHERE materialid={$item['materialid']} AND itemid={$item['materialitemid']} ");
							$materialno = $query['itemno'];
						}
						$tolerance=$this->kclass->iif($item['uptolerance']==0 AND $item['belowtolerance']==0,0,'>'.$item['belowtolerance'].'%,<'.$item['uptolerance'].'%');
						$quantity = $this->kclass->iif($item['checkInventory']!='',$item['checkInventory'],$item['firstInventory']);
						$itemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').'">
							<td>'.$i.'</td>
							<td>'.$materialno.'</td>
							<td>'.$item['title'].'</td>
							<td>'.$item['standard'].'</td>
							<td>'.$item['stock'].'</td>
							<td>'.$item['firstInventory'].'</td>
							<td>'.$item['checkInventory'].'</td>
							<td>'.$quantity.'</td>
							<td>'.$item['difference'].'</td>
							<td>'.$tolerance.'</td>
							<td>'.$item['remark'].'</td>
							<td>'.$item['reason'].'</td>
							<td><a href="/s.php?module=inventory&action=killitem&inventoryid='.$inventory['inventoryid'].'&itemid='.$item['itemid'].'" onclick="return confirm(\'你确定要删除物资 '.$item['materialno'].' 此次盘点记录吗？（此删除不可恢复）\');">删除</a>　<a href="/s.php?module=inventory&action=updateitem&inventoryid='.$inventory['inventoryid'].'&itemid='.$item['itemid'].'">修改</a>　'.$check.'</td></tr>';
							$i++;
					}
				}
				$itemtr.='<tr><td align="center" colspan="13"><a href="/s.php?module=inventory&action=verify&inventoryid='.$inventory['inventoryid'].'&mtypeid='.$this->kclass->input['mtypeid'].'">'.$verify.'</a></td></tr></tbody></table>';

$body=<<<EOF
<div class="title"><span class="right small gray">由{$inventory['creator']}于{$inventory['created']}建立{$modify}。</span>{$inventory['title']}{$titleSuffix}</div>
<table class="hundred">
<thead><tr><th colspan="4">基本信息</th></tr></thead>
<tbody>
<tr class="odd">
	<td width="110">标题：</td><td width="450">{$inventory['title']}</td>
	<td width="110">盘  点  人：</td><td>{$inventory['inventorier']}</td>
</tr>
<tr class="even">
	<td>盘点类型：</td><td>{$inventoryType}</td>
	<td>盘点时间：</td><td>{$inventory['dateline']}</td>
</tr>
<tr class="odd">
	<td>上次盘点时间：</td><td>{$preInventory['dateline']}</td>
	<td>审核状态：</td>
	<td>{$verifyStatus}</td>
</tr>
</tbody>
</table>
		{$itemtr}
EOF;
			}else{
				$body = '系统数据出错，很抱歉！';
			}
			$this->kclass->page['title'].='查看盘点信息';
			$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - <a href="/s.php?module=inventory&action=list">盘点列表</a> - 查看盘点信息','right'=>'<span class="small">打印：</span>　<a href="/p.php?action=inventory&inventoryid='.$inventory['inventoryid'].'">盘点物资</a>　|　<span class="small">新增：</span><a href="/s.php?module=inventory&action=add">盘点</a>　<a href="/s.php?module=inventory&action=additems&mtypeid=1&inventoryid='.$inventory['inventoryid'].'">盘点物资</a>　|　<a href="/s.php?module=inventory&action=update&inventoryid='.$inventory['inventoryid'].'">修改</a>　<a href="/s.php?module=inventory&action=remove&inventoryid='.$inventory['inventoryid'].'">删除</a>　|　<a href="/s.php?module=inventory&action=list">返回列表</a>','body' => $body));

		}
		// insert
		function add(){		
			$mategialOrigin=$this->kclass->chooserMaterialOrigin(array('name'=>'originid','hasBlank'=>1,'selectedid'=>$this->kclass->input['originid']));
			$nowTime=date('Y-m-d',TIMENOW);
			$no = 'INV-'.date('Ymd',TIMENOW);
			for($i=1;$i<=5;$i++){
				$itemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').' small">
					<td align="center">'.$i.'</td>
					<td align="center">'.$this->kclass->chooserMaterial(array('hName'=>'itemMaterialid['.$i.']','hId'=>'itemMaterialid'.$i,'name'=>'itemMaterial['.$i.']','id'=>'itemMaterial'.$i,'width'=>450,'line'=>$i,'showVersion'=>1)).'</td>
					<td align="center"><select name="itemVersionid['.$i.']" id="itemVersionid'.$i.'" style="width:200px;"></select></td>
					<td align="center"><input type="text" name="qualifiedQuantity['.$i.']" size="6" /></td>
					<td align="center"><input type="text" name="disqualifiedQuantity['.$i.']" size="6" /></td>				
					<td align="center"><input type="text" name="itemRemark['.$i.']" size="60"></td>
				</tr>';
				if($i<5){
					$id.='#dateline'.$i.',';
				}else{
					$id.='#dateline'.$i;
				}
			}
		$itemtr.='<tr class="odd bold gray normal" id="inventorylList" nohover><td colspan=10><span class="hand" onclick="dc.addLine(\'inventorylList\',\'inventorylList\')" style="border:2px solid gray;background:#FFE8EB">增加物资明细</span></td></tr>';
$body=<<<EOF
<form action="/s.php?module=inventory&action=insert" name="inventory" method="post">
<table class="hundred">
<thead>
	<tr>
		<th colspan="8">新建盘点记录基本信息</th>
	</tr>
</thead>
<tbody>
	<tr class="odd">
		<td width="110">编    号：<span class="red bold">*</span></td>
		<td colspan="3" width="450"><input type="text" size="48" name="no" value="{$no}"/></td>
		<td width="110">标　　题：<span class="red bold">*</span></td>
		<td colspan="3" width="450"><input type="text" size="48" name="title" /></td>
	</tr>
	<tr class="even">
		<td width="110">盘点类型：<span class="red bold">*</span></td>
		<td colspan="1" >
			<select name="inventoryType" style="width:150px">
				<option value="choose">选择类型</option>
				<option value="choose">抽盘</option>
				<option value="must">必盘</option>
			</select>
		</td>
		<td width="110">盘点时间：<span class="red bold">*</span></td>
		<td colspan="1"><input type="text" name="dateline" id="nowTime" value="{$nowTime}" /></td>
		<td width="110">填写人：<span class="red bold">*</span></td>
		<td	colspan="1"><input type="text" name="inventorier" size="16" /></td>
	</tr>
</tbody>
<tbody>
</table>
<table class="hundred">
	<tr class="old">
	<td colspan="8" class="small gray"> 说明:提交盘点基本信息后便可添加物资盘点明细。</td>
	</tr>
</tbody>
</table>
<table class="tlist tsuite" width="100%">
<tbody>
	<tr class="odd center">
		<td colspan="8"><input type="submit" id="submitButton" value="  提交  " accesskey="s"><input type="reset" value="  复位  "/></td>
	</tr>
</tbody>
</table>
</form>
EOF;
			$this->kclass->tbline+=4;
			$this->kclass->page['title'].='新建盘点记录';
			$this->kclass->page['onload'].='dc.tabhover();$(\'#nowTime,#nextround\').datepicker();';
			$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - <a href="/s.php?module=inventory&action=list">盘点列表</a> - 新建盘点记录', 'right'=>'<a href="/s.php?module=inventory">返回列表</a>　|　<a href="/s.php?module=inventory&action=view&inventoryid='.$this->kclass->input['inventoryid'].'">查看盘点信息</a>　','body'=>$body));
		}
		//
		function insert(){
			if($this->kclass->input['requestMethod'] != 'post'){
				$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
			}else{
				if($this->kclass->input['title']==''){
					$e.='<li>请填写盘点记录的 标题</li>';
				}
				if($this->kclass->input['inventorier']==''){
					$e.='<li>请填写盘点记录的 盘点人</li>';
				}
				if($this->kclass->input['dateline']==''){
					$e.='<li>请填写盘点记录的 盘点时间</li>';
				}
				if($this->kclass->input['inventoryType']==''){
					$e.='<li>请选择盘点记录的 盘点类型</li>';
				}
			}
			if(isset($e)){
				$this->kclass->messager(array(
					'title' => '新建盘点记录',
					'text' => '新建盘点记录<ul>'.$e.'</ul>',
					'url' => 'javascript:history.back()',
					'sec' => 3
				));
			}
			if($this->kclass->input['dateline']){
				$sd=explode('-',$this->kclass->input['dateline']);
				$dateline=mktime(0,0,0,$sd[1],$sd[2],$sd[0]);
			}else{
				$dateline='';
			}
			$this->kclass->DB->query("
				INSERT INTO `inventory` (`inventoryno`,`title`,`inventorier`,`inventoryType`,`dateline`,`creator`,`created`) VALUES ('".$this->kclass->input['no']."','".$this->kclass->input['title']."','".$this->kclass->input['inventorier']."','".$this->kclass->input['inventoryType']."','".$dateline."','".$this->kclass->user['userid']."', '".TIMENOW."')
			");
			$inventoryid = $this->kclass->DB->insertID();
			$this->kclass->messager(array(
				'title' => '新建盘点记录',
				'text' => '盘点记录 <b>'.$this->kclass->input['title'].'</b> 已新建成功，正为您跳转至添加物资盘点明细···',
				'url' => '/s.php?module=inventory&action=chooseMtype&inventoryid='.$inventoryid,
				'sec' => 2
			));
		}
		//choose material type
		function chooseMtype(){
$body=<<<EOF
<form action="/s.php?module=inventory&action=additems" name="inventory" method="post">
<input type="hidden" name="inventoryid" value="{$this->kclass->input['inventoryid']}" />
<table class="hundred">
<thead>
	<tr>
		<th>选择盘点物资类型</th>
	</tr>
</thead>
<tbody>
	<tr>
		<td>
			<select name="mtypeid">
				<option value="">请选择类型</option>
				<option value="1">良品盘点</option>
				<option value="-1">不良品盘点</option>
			</select>
		</td>
	</tr>
	<tr class="odd center">
		<td colspan="8"><input type="submit" id="submitButton" value="  提交  " accesskey="s"><input type="reset" value="  复位  "/></td>
	</tr>
</tbody>
</table>
</form>
EOF;
			$this->kclass->tbline+=4;
			$this->kclass->page['title'].='选择盘点物资类型';
			$this->kclass->page['onload'].='dc.tabhover();$(\'#nowTime,#nextround\').datepicker();';
			$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - <a href="/s.php?module=inventory&action=list">盘点列表</a> - 新建盘点记录', 'right'=>'<a href="/s.php?module=inventory">返回列表</a>　|　<a href="/s.php?module=inventory&action=view&inventoryid='.$this->kclass->input['inventoryid'].'">查看盘点信息</a>　','body'=>$body));				
		}
		//
	function addItems(){
		if($this->kclass->input['mtypeid']!=''){
			$mtype = $this->kclass->iif($this->kclass->input['mtypeid']==1,'良品盘点','不良品盘点');
			if($this->kclass->input['stockzero']==''){
				if($this->kclass->input['mtypeid']>0){
					$mCondition = 'AND material.qualified>0';//良品数非零
					$mCondition2 = 'AND mi.qualified>0';
					$symbol = 1;
				}else{
					$mCondition = 'AND material.disqualified>0';//不良品数非零
					$mCondition2 = 'AND mi.disqualified>0';
					$symbol = -1;
				}
			}elseif($this->kclass->input['stockzero']==1){
				if($this->kclass->input['mtypeid']>0){
					$mCondition = 'AND material.qualified=0';//良品数非零
					$mCondition2 = 'AND mi.qualified=0';
					$symbol = 1;
				}else{
					$mCondition = 'AND material.disqualified=0';//不良品数非零
					$mCondition2 = 'AND mi.disqualified=0';
					$symbol = -1;
				}			
			}
		}
		//$fieldQuery = ',ivi.materialType,ivi.firstInventory,ivi.checkInventory,ivi.difference';//盘点字段查询
		$tableLink = 'LEFT JOIN (SELECT materialid AS id,materialType,firstInventory,checkInventory,difference,remark AS invremark FROM `inventoryitem` WHERE inventoryid='.$this->kclass->input['inventoryid'].') AS invi ON (`invi`.id = `material`.materialid)';//盘点明细表与物资表关联
		$id=array(0);
		$condition=$this->kclass->iif($this->kclass->input['show']=='all','1=1',' `material`.killed=0 '.$mCondition.'');
		if($this->kclass->input['materialno']!=''){
			$nos=explode(';',$this->kclass->input['materialno']);
			if(is_array($nos) AND count($nos)>1){
				foreach($nos AS $no){
					$find=$this->kclass->DB->queryFirst("SELECT materialid FROM material WHERE materialno='{$no}'");
					if(!$find['materialid']){
						$id[]=0;
					}else{
						$id[]=$find['materialid'];
					}
				}
				$condition.=' AND materialid IN ('.implode(',',$id).')';
			}elseif(count($nos)==1){
					$finds=$this->kclass->DB->query("SELECT materialid FROM material WHERE materialno LIKE '%{$this->kclass->input['materialno']}%'");
					if($this->kclass->DB->numRows()){
						while($find=$this->kclass->DB->fetchArray($finds)){
							if(!$find['materialid']){
								$id[]=0;
							}else{
								$id[]=$find['materialid'];
							}
						}
					}
					$condition.=' AND materialid IN ('.implode(',',$id).')';
			}
			$perpage=2000;
		}elseif($this->kclass->input['title']!=''){
			$finds=$this->kclass->DB->query("SELECT materialid FROM material WHERE title LIKE '%{$this->kclass->input['title']}%'");
			if($this->kclass->DB->numRows()){
				while($find=$this->kclass->DB->fetchArray($finds)){
					if(!$find['materialid']){
						$id[]=0;
					}else{
						$id[]=$find['materialid'];
					}
				}
			}
			$perpage=2000;
			$condition.=' AND materialid IN ('.implode(',',$id).')';
		}elseif($this->kclass->input['isUsed']==-1 OR $this->kclass->input['isUsed']==1 OR $this->kclass->input['originid']>0 OR $this->kclass->input['categoryid']>0){
			$c='';
			if($this->kclass->input['isUsed']==-1 OR $this->kclass->input['isUsed']==1){
				$condition.=' AND  `material`.isUsed='.$this->kclass->input['isUsed'];
				$c.=' AND  `material`.isUsed='.$this->kclass->input['isUsed'];
			}
			if($this->kclass->input['originid']>0){
				$condition.=' AND  `material`.originid='.$this->kclass->input['originid'];
				$c.=' AND  `material`.originid='.$this->kclass->input['originid'];
			}
			if($this->kclass->input['categoryid']>0){
				$category=$this->kclass->DB->queryFirst("SELECT childlist FROM category WHERE categoryid={$this->kclass->input['categoryid']}");
				$c.=' AND categoryid IN ('.$category['childlist'].')';
			}
			if($c){
				$finds=$this->kclass->DB->query("SELECT materialid FROM material WHERE 1=1 {$c}");
				if($this->kclass->DB->numRows()){
					while($find=$this->kclass->DB->fetchArray($finds)){
						if(!$find['materialid']){
							$id[]=0;
						}else{
							$id[]=$find['materialid'];
						}
					}
				}
				$perpage=2000;
				$condition.=' AND `material`.materialid IN ('.implode(',',$id).')';
			}
		}
		if($this->kclass->input['orderby']!='')$query['orderby']=$this->kclass->input['orderby'];
		if($this->kclass->input['direction']!='')$query['direction']=$this->kclass->input['direction'];
		if($this->kclass->input['show']!='')$query['show']=$this->kclass->input['show'];
		if($this->kclass->input['layout']!='')$query['layout']=$this->kclass->input['layout'];
		$orderby=$this->kclass->orderby(array('module'=>'inventory','direction'=>'asc','orderby'=>'modified','default'=>'materialno','serial'=>array(array('title'=>'名称','field'=>'title','word'=>'name'),array('title'=>'编号','field'=>'materialno','word'=>'no'),array('title'=>'修改时间','field'=>'modified'),array('title'=>'建立时间','field'=>'created')),'appendUrl'=>$query));
		if(is_array($query) AND count($query)>0)$queryPart='&'.http_build_query($query);
		if($perpage=='')$perpage=1000;
		$pcount=$this->kclass->DB->queryFirst("SELECT COUNT(materialid) AS count FROM material WHERE killed=0");
		$list=$this->kclass->listMaterial(array('module'=>'inventory','perpage'=>$perpage,'find'=>$find,'condition'=>$condition,'orderby'=>$query['orderby'],'direction'=>$query['direction'],'show'=>$query['show'],'layout'=>$query['layout'],'symbol'=>$symbol,'fieldQuery'=>$fieldQuery,'tableLink'=>$tableLink));
		if($query['layout']=='grid'){
			$materialList.='<ul id="tbody" class="mmlist clear">';
		}else{
			$materialList.='<table class="hundred">
								<thead>
									<tr>
										<th width="30">ID</th>
										<th width="60">物资编号</th>
										<th width="100">名称</th>
										<th width="40">来源</th>
										<th width="300">规格</th>
										<th width="80">单价</th>
										<th width="50">单位</th>										
										<th width="62">库存</th>
										<th width="60">初盘</th>
										<th width="60">复盘</th>
										<th width="60">终盘</th>
										<th width="50">盈亏</th>
										<th width="150">备注</th>
									</tr>
								</thead>
								<tbody id="tbody">';
		}
		$materialList.=$list['body'];

		if($pcount['count']>$list['counter']){
			$materialList.='<div class="page"><a href="javascript:dc.tableItem.page('.$perpage.','.$this->kclass->input['mtypeid'].')">点击查看更多</a></div>';
		}
		$materialList.='<div id="clear"></div>';
		if($this->kclass->input['layout']=='grid'){
			$layoutLink='<a href="/s.php?module=inventory&action=additems&mtypeid='.$this->kclass->input['mtypeid'].'&inventoryid='.$this->kclass->input['inventoryid'].'&layout=list'.str_replace('&layout=grid','',$queryPart).'">列表</a> 格子';
		}else{
			$layoutLink='列表 <a href="/s.php?module=inventory&action=additems&mtypeid='.$this->kclass->input['mtypeid'].'&inventoryid='.$this->kclass->input['inventoryid'].'&layout=grid'.str_replace('&layout=list','',$queryPart).'">格子</a>';
		}
		if($this->kclass->input['show']=='all'){
			$showLink='<a href="/s.php?module=inventory&action=additems&mtypeid='.$this->kclass->input['mtypeid'].'&inventoryid='.$this->kclass->input['inventoryid'].''.str_replace('&show=all','',$queryPart).'">默认</a> 所有<span class="small gray">(包括删除)</span>';
		}else{
			$showLink='默认 <a href="/s.php?module=inventory&action=additems&mtypeid='.$this->kclass->input['mtypeid'].'&inventoryid='.$this->kclass->input['inventoryid'].'&show=all'.str_replace('&show=all','',$queryPart).'">所有<span class="small gray">(包括删除)</span></a>';
		}
		// 产成品及包成品物资
		$filterItem=$this->kclass->filterItem(array('module'=>'material'));
		$filter=$this->kclass->filter(
			array('module'=>'inventory','action'=>'additems','mtypeid'=>$this->kclass->input['mtypeid'],'inventoryid'=>$this->kclass->input['inventoryid'],'table'=>'material','stockzero'=>$this->kclass->input['stockzero'],'serial'=>array(
			array('name'=>'materialid','dname'=>'包　成　品','status'=>$filterItem['bmaterial']),
			array('name'=>'materialid','dname'=>'产　成　品','status'=>$filterItem['cmaterial'])
		)));
		$materialItems=$this->kclass->DB->query("
			SELECT mi.itemid,mi.materialid,mi.relatedid,mi.itemno,mi.quantity,mi.qualified,mi.disqualified,
				material.materialno,material.unitid
			FROM materialitem AS mi
			LEFT JOIN material ON (material.materialid=mi.materialid)
			WHERE mi.killed=0 AND material.killed=0 {$filter['link']} {$mCondition2}
			ORDER BY material.materialno ASC,mi.itemno ASC
		");
		if($this->kclass->DB->numRows()){
			$i=1;
			$itemList.='<table class="hundred">
							<thead>
								<tr>
									<th width="30">ID</th>
									<th width="60">  编码</th>
									<th width="100">名称</th>
									<th width="40">来源</th>
									<th width="300">属性</th>
									<th width="80">单价</th>
									<th width="50">单位</th>
									<th width="60">库存</th>
									<th width="60">初盘</th>
									<th width="60">复盘</th>
									<th width="60">终盘</th>
									<th width="50">盈亏</th>
									<th width="150">备注</th>
								</tr>
							</thead>
						<tbody>';
			while($materialItem=$this->kclass->DB->fetchArray($materialItems)){
				//查询盘点信息
				$inventoryinfo = $this->kclass->DB->queryFirst("SELECT materialType,firstInventory,checkInventory,difference FROM `inventoryitem` WHERE inventoryid={$this->kclass->input['inventoryid']} AND materialitemid={$materialItem['itemid']}");
				$itemVersion=$this->kclass->DB->queryFirst("SELECT price,priceStatus FROM materialversion WHERE itemid={$materialItem['itemid']}");
				if($materialItem['relatedid']>0){
					$related=$this->kclass->DB->queryFirst("SELECT itemid,materialid,itemno,quantity,qualified,disqualified FROM materialitem WHERE itemid=".$materialItem['relatedid']." LIMIT 0,1");
				}else{
					$related=$this->kclass->DB->queryFirst("SELECT itemid,materialid,itemno,quantity,qualified,disqualified FROM materialitem WHERE relatedid=".$materialItem['itemid']." LIMIT 0,1");
				}
				$unit=$this->kclass->DB->queryFirst("SELECT title AS unit FROM unit WHERE unitid='".$materialItem['unitid']."'");
				/*$tableLink2 = 'LEFT JOIN (SELECT materialid AS id,materialType,firstInventory,checkInventory,difference FROM `inventoryitem` WHERE inventoryid='.$this->kclass->input['inventoryid'].') AS invi ON (`invi`.id = `m`.materialid)';*///盘点明细表与物资表关联
				$materialInfo=$this->kclass->getMaterial(array('itemid'=>$materialItem['itemid'],'materialid'=>$materialItem['materialid']));
				if($itemVersion['price']==0){
					$class='black';
				}else{
					$class='red';
				}
				//判断显示良品盘点或非良品盘点数据
				if($inventoryinfo['materialType']==$symbol){
					$firstInventory = $this->kclass->iif($inventoryinfo['firstInventory']!='',$inventoryinfo['firstInventory'],'+');
					$checkInventory = $this->kclass->iif($inventoryinfo['checkInventory']!='',$inventoryinfo['checkInventory'],'');
					$final = $this->kclass->iif($inventoryinfo['checkInventory']!='',$inventoryinfo['checkInventory'],$inventoryinfo['firstInventory']);
					$difference = $this->kclass->iif($inventoryinfo['difference']!='',$inventoryinfo['difference'],'');
				}else{
					$firstInventory = '+';
					$checkInventory = '';
					$final = '';
					$difference = '';
				}						
				$firstClass = $this->kclass->iif($inventoryinfo['firstInventory']!='','','first');
				if($inventoryinfo['materialType']==$symbol and $inventoryinfo['checkInventory']=='' and $inventoryinfo['firstInventory']!=''){
					$checkInventory = '+';
					$checkClass = 'check';
				}else{
					$checkClass = '';
				}
				$stock = $this->kclass->iif($this->kclass->input['mtypeid']==1,$materialItem['qualified'],$materialItem['disqualified']);
				if($materialInfo['material']!=''){
					$itemList.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').' small">
						<td>'.$i.'</td>
						<td class="mid"><a href="'.$materialInfo['url'].'">'.$materialItem['itemno'].'</a></td>
						<td>'.$materialInfo['material'].'</td>
						<td align="center"> - </td>
						<td>'.$materialInfo['standard'].'</td>
						<td align="right" class="bold normal '.$class.'">'.$this->kclass->iif($this->kclass->purviews(array('module'=>'material','action'=>'viewPrice','final'=>1)),$itemVersion['price'].' '.$this->kclass->priceStatus($itemVersion['priceStatus']),'—').'</span></td>
						<td align="center">'.$unit['unit'].'</td>
						<td align="center" class="bold normal">'.$stock.'</td>
						<td align="center" class="'.$firstClass.'">'.$firstInventory.'</td>
						<td class="'.$checkClass.'">'.$checkInventory.'</td>
						<td class="final">'.$final.'</td>
						<td class="diff">'.$difference.'</td>
						<td align="center"><input type="text" name="remark" class="remark" /></td>
					</tr>';
					$i++;
				}
			}
			$itemList.='</tbody></table>';
		}
		// 分类筛选
		$categorys=$this->kclass->DB->query("SELECT categoryid,title FROM category WHERE parentid='".$this->kclass->iif($this->kclass->input['categoryid']>0,$this->kclass->input['categoryid'],19)."' ORDER BY categoryid ASC");
		if($this->kclass->DB->numRows()){
			while($category=$this->kclass->DB->fetchArray($categorys)){
				$categoryList.='<a href="/s.php?module=inventory&action=additems&mtypeid='.$this->kclass->input['mtypeid'].'&inventoryid='.$this->kclass->input['inventoryid'].'&originid='.$this->kclass->input['originid'].'&categoryid='.$category['categoryid'].'">'.$category['title'].'</a>　';
			}
		}
		$parentCategory=$this->kclass->DB->queryFirst("SELECT parentid FROM category WHERE categoryid='".$this->kclass->iif($this->kclass->input['categoryid']>0,$this->kclass->input['categoryid'],19)."' LIMIT 0,1");
		$parentCategoryid=$this->kclass->iif($parentCategory['parentid']>0,$parentCategory['parentid'],19);
		// 来源筛选
		$origins=$this->kclass->DB->query("SELECT originid,title FROM materialorigin ORDER BY originid ASC");
		if($this->kclass->DB->numRows()){
			while($origin=$this->kclass->DB->fetchArray($origins)){
				$orginList.='<a href="/s.php?module=inventory&action=additems&mtypeid='.$this->kclass->input['mtypeid'].'&inventoryid='.$this->kclass->input['inventoryid'].'&isUsed='.$this->kclass->input['isUsed'].'&originid='.$origin['originid'].'&categoryid='.$this->kclass->input['categoryid'].'">'.$origin['title'].'</a>　';
			}
		}
		$body=<<<EOF
<dl id="material" class="tabs" style="display: block;">
<dt tabid="0">物资盘点</dt>
<dt title="1">产品物资盘点</dt>
<dd>
<form action="/s.php?module=inventory&action=additems" method="get">
<input type="hidden" name="module" value="inventory" >
<input type="hidden" name="action" value="additems" >
<table class="hundred">
<thead>
<tr>
<th colspan=3>查询【全部】- <a href="/s.php?module=inventory&action=additems&mtypeid={$this->kclass->input['mtypeid']}&inventoryid={$this->kclass->input['inventoryid']}">有库存</a> - <a href="/s.php?module=inventory&action=additems&mtypeid={$this->kclass->input['mtypeid']}&inventoryid={$this->kclass->input['inventoryid']}&stockzero=1">无库存</a><span class="small gray">（注意：多个编号请用英文分号(;)分隔开，搜索时只能选择三种中的一种进行搜索）</span></th>
</tr>
</thead>
<tbody>
<tr>
<td>是否使用：</td>
<td colspan=2><a href="/s.php?module=inventory&action=additems&mtypeid={$this->kclass->input['mtypeid']}&inventoryid={$this->kclass->input['inventoryid']}&isUsed=1&originid={$this->kclass->input['originid']}&categoryid={$this->kclass->input['categoryid']}">是</a>　<a href="/s.php?module=inventory&action=additems&mtypeid=&inventoryid={$this->kclass->input['inventoryid']}&isUsed=-1&originid={$this->kclass->input['originid']}&categoryid={$this->kclass->input['categoryid']}">否</a></td>
</tr>
<tr>
<td>来　　　源：</td><td colspan=2>{$orginList}</td>
</tr>
<tr>
<td>分　　　类：</td>
<td colspan=2>【<a href="/s.php?module=inventory&action=additems&mtypeid=&inventoryid={$this->kclass->input['inventoryid']}&isUsed={$this->kclass->input['isUsed']}&originid={$this->kclass->input['originid']}&categoryid={$parentCategoryid}">上一级</a>】　{$categoryList}</td>
</tr>
<tr>
<td>编　　　号：</td>
<td>
<input type="text" name="materialno" size="160" value="{$this->kclass->input['materialno']}">
<td><input type="submit" value="查询" >
</td>
</tr>
<tr>
<td>名　　　称：</td>
<td><input type="text" name="title" size="160" value="{$this->kclass->input['title']}"></td>
<td><input type="submit" value="查询" ></td>
</tr>
</tbody>
</table>
</form>
{$materialList}</dd>
<dd>{$filter['panel']}
{$itemList}</dd>
EOF;
		$this->kclass->page['title'].='物资盘点明细-'.$mtype;
		$this->kclass->page['onload'].='dc.listhover();dc.tabs({\'id\':\'material\'});dc.tabhover();dc.flow(\'listtr\');dc.inventory.firstIn({\'mtype\':\''.$this->kclass->input['mtypeid'].'\',\'inventoryid\':\''.$this->kclass->input['inventoryid'].'\'});dc.inventory.checkIn({\'mtype\':\''.$this->kclass->input['mtypeid'].'\',\'inventoryid\':\''.$this->kclass->input['inventoryid'].'\'});dc.inventory.addRmark({\'mtype\':\''.$this->kclass->input['mtypeid'].'\',\'inventoryid\':\''.$this->kclass->input['inventoryid'].'\'})';
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - 物资盘点明细 - '.$mtype,'right' => '<a href="/s.php?module=inventory&action=additems&mtypeid=1&inventoryid='.$this->kclass->input['inventoryid'].'" target="_blank"> 良品盘点 </a> | <a href="/s.php?module=inventory&action=additems&mtypeid=-1&inventoryid='.$this->kclass->input['inventoryid'].'" target="_blank"> 不良品盘点 </a>','body'=>$body));
	}	
	//盘点审核
	function verify(){
		$body = <<<EOF
<form action="/s.php?module=inventory&action=updateMaterial" method="post">
	<input type="hidden" name="inventoryid" value="{$this->kclass->input['inventoryid']}" />
	<input type="hidden" name="mtypeid" value="{$this->kclass->input['mtypeid']}" />
	<table width="20%">
		<tr>
			<th>确定要通过审核本次物资盘点？</th>
		</tr>	
		<tr>
			<td align="center"><input type="radio" name="verify" />确定 <input type="radio" name="verify" />取消</td>
		</tr>
		<tr><td class="small gray">提示：选“确定”将立即更新库存,请慎重考虑！</td></tr>		
		<tr>
			<td align="center"><input type="submit" name="submit" value="提交" /></td>
		</tr>
	</table>
</form>
EOF;
		$this->kclass->page['title'].='物资盘点审核';
		$this->kclass->page['onload'].='dc.listhover();dc.tabs({\'id\':\'material\'});dc.tabhover();dc.flow(\'listtr\')';
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - 物资盘点审核','right' =>'','body'=>$body));
	}
	//更新物资库存及收发信息
	function updateMaterial(){
		set_time_limit(300);//设置最大执行时间上限为300秒
		if($this->kclass->input['requestMethod'] != 'post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			if($this->kclass->input['inventoryid']==''){
				$e.='<li>未获取到盘点ID！</li>';
			}
			if($this->kclass->input['mtypeid']==''){
				$e.='<li>未获取到物资类别ID(用于区别良品非良品)！</li>';
			}
			if($this->kclass->input['verify']==''){
				$e.='<li>请选择确定或取消选项！</li>';
			}
		}			
		if($e){
			$this->kclass->messager(array(
				'title' => '物资盘点审核',
				'text' => '盘点审核提交过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$materialType = null;
		if($this->kclass->input['mtypeid']==1){
			$mtype = 'qualified';
			$title = '良品盘点审核';
			$balance = 'balance';
			$mtype2 = 'disqualified';
			$balance2 = 'disbalance';
			$verifyType = 'verify1';
		}else{
			$mtype = 'disqualified';
			$title = '不良品盘点审核';
			$balance = 'disbalance';
			$mtype2 = 'qualified';
			$balance2 = 'balance';
			$verifyType = 'verify2';
		}
		$ifverify = $this->kclass->DB->queryFirst("SELECT ifverify FROM inventory WHERE inventoryid={$this->kclass->input['inventoryid']}");
		// echo $ifverify['ifverify'];
		// exit;
		
		$items = $this->kclass->DB->query("SELECT inv.itemid,inv.materialid,inv.materialitemid,inv.firstInventory,inv.checkInventory,inv.difference,inv.created,
												m.materialno
											FROM inventoryitem AS inv
											LEFT JOIN material AS m ON (m.materialid = inv.materialid)
											WHERE inv.inventoryid=".$this->kclass->input['inventoryid']." AND inv.materialType=".$this->kclass->input['mtypeid']." AND inv.killed=0
										");
		if($this->kclass->DB->numRows()){
			$i = 1;
			while($item = $this->kclass->DB->fetchArray($items)){
				$batchno = $item['materialno'].'-141231';
				$newStock = null;
				if($item['materialitemid']==''){
					$materialitemid = 0;
					$version = $this->kclass->DB->queryFirst("SELECT versionid,title AS vtitle FROM materialversion WHERE materialid={$item['materialid']}");
				}else{
					$materialitemid = $item['materialitemid'];
					$version = $this->kclass->DB->queryFirst("SELECT versionid,title AS vtitle FROM materialversion WHERE itemid={$materialitemid} AND materialid={$item['materialid']}");
				}
				if($item['checkInventory']!=''){
					$newStock = $item['checkInventory'];
				}else{
					if($item['firstInventory']!=''){
						$newStock = $item['firstInventory'];
					}
				}
				if($item['difference']>=0){
					$adtion = '+';
					$sign = '';
				}else{
					$adtion = '-';
					$sign = '-';
				}
				$diff = abs($item['difference']);
				//更新物资版本表中的数量
				// if($materialitemid==0){
					// $updatemv=$this->kclass->DB->query("
						// UPDATE materialversion SET
							// quantity=quantity+{$item['difference']},
							// {$mtype}={$newStock}
						// WHERE versionid={$version['versionid']}
					// ");
				// }else{
					// $updatemv=$this->kclass->DB->query("
						// UPDATE materialversion SET
							// quantity=quantity+{$item['difference']},
							// {$mtype}={$newStock}
						// WHERE versionid={$version['versionid']}
					// ");				
				// }
				
				// 更新物资库存信息
				// $stock = $this->kclass->DB->query("UPDATE material SET quantity=quantity+{$item['difference']},{$mtype}={$newStock} WHERE materialid={$item['materialid']}");
				// if($materialitemid!=0){
					// $this->kclass->DB->query("UPDATE materialitem SET quantity=quantity+{$item['difference']},{$mtype}={$newStock} WHERE itemid={$materialitemid}");
				// }
				
				//设置生成批号的元素(供应商未知,设默认值)
				$supplier['supplierno']='0000';
				$supplier['supplierid']='1';
				$supplier['country']='CN';
				$sd=explode('-',date('y-n-j',$item['created']));
				$time36='';
				foreach($sd as $val){
					$time36.=strtoupper(base_convert($val,10,36));// 将日期转化为36位进制
				}
				$barcode=$supplier['country'].'-'.$material['materialno'].$this->kclass->iif($this->kclass->input['materialitemid']>0,'-'.$material['no'],'').'-'.$supplier['supplierno'].'-'.$time36.'-'.$version['vtitle'];
				//根据盘点审核状态进行物资信息新增或更新
				if($ifverify['ifverify']==0){
					if($item['difference']!=0){
						//新增批次
						$batch=$this->kclass->DB->query("
							INSERT INTO batch
								(batchno,materialid,materialitemid,versionid,dateline,quantity,{$mtype},{$mtype2},remark,created,creator)
							VALUES
								('".$batchno."',
								'".$item['materialid']."',
								'".$materialitemid."',
								'".$version['versionid']."',
								'".$item['created']."',
								'".$sign.$diff."',
								'".$sign.$diff."',
								'0',
								'',
								'".TIMENOW."',
								'".$this->kclass->user['userid']."
								')
						");
						$batchid=$this->kclass->DB->insertID();	
						$this->kclass->DB->query("
							UPDATE inventoryitem SET batchid={$batchid} WHERE materialid={$item['materialid']} AND inventoryid={$this->kclass->input['inventoryid']} AND materialType={$this->kclass->input['mtypeid']}
						");
						//新增批号
						$nbarcode=$this->kclass->DB->query("
							INSERT INTO barcode (materialid,supplierid,versionid,batchid,barcode,country,materialno,supplierno,arrivalTime,version,quantity,{$mtype},{$mtype2},created,creator)
							VALUES (
							'".$item['materialid']."',
							'".$supplier['supplierid']."',
							'".$version['versionid']."',
							'".$batchid."',
							'".$barcode."',
							'".$supplier['country']."',
							'".$item['materialno']."',
							'".$supplier['supplierno']."',
							'".$item['created']."',
							'".$version['vtitle']."',
							'".$sign.$diff."',
							'".$sign.$diff."',
							'0',
							'".TIMENOW."',
							'".$this->kclass->user['userid']."'
							)
						");
						$barcodeid = $this->kclass->DB->insertID();	
						$this->kclass->DB->query("
							UPDATE inventoryitem SET barcodeid={$barcodeid} WHERE materialid={$item['materialid']} AND inventoryid={$this->kclass->input['inventoryid']} AND materialType={$this->kclass->input['mtypeid']}
						");
					}
					// 新增物料收发卡记录
					$inoutrecord =$this->kclass->DB->query("
						INSERT inoutrecord (`module`,`mid`,`materialid`,`materialitemid`,`versionid`,`itemid`,`addition`,`{$mtype}`,`{$balance}`,`{$mtype2}`,`{$balance2}`,`dateline`,`created`,`creator`) VALUE (
						'inventory',
						'".$this->kclass->input['inventoryid']."',
						'".$item['materialid']."',
						'".$materialitemid."',
						'".$version['versionid']."',
						'".$item['itemid']."',
						'".$adtion."',
						'".$diff."',
						'".$newStock."',
						'0',
						'0',
						'".TIMENOW."',
						'".TIMENOW."',
						'".$this->kclass->user['userid']."
						')
					");	
					//更新盘点审核及完成状态
					$verifyInventory = $this->kclass->DB->query("UPDATE inventory SET {$verifyType}=1,ifverify=1 WHERE inventoryid={$this->kclass->input['inventoryid']}");
				}elseif($ifverify['ifverify']==1){
					$batchid = $this->kclass->DB->queryFirst("SELECT batchid FROM batch WHERE batchno='{$batchno}'");
					if($batchid!=''){
						//更新批号信息
						$batch=$this->kclass->DB->query("UPDATE batch SET quantity='{$sign}{$diff}',{$mtype}='{$sign}{$diff}' WHERE batchid={$batchid['batchid']}");
						//更新批号信息
						$barcodeid = $this->kclass->DB->queryFirst("SELECT barcodeid FROM barcode WHERE batchid={$batchid['batchid']}");	$nbarcode = $this->kclass->DB->query("UPDATE barcode SET quantity='{$sign}{$diff}',{$mtype}={$sign}{$diff} WHERE batchid={$batchid['batchid']}");	
					}else{
						//新增批次记录
						$batch=$this->kclass->DB->query("
							INSERT INTO batch
								(batchno,materialid,materialitemid,versionid,dateline,quantity,{$mtype},{$mtype2},remark,created,creator)
							VALUES
								('".$batchno."',
								'".$item['materialid']."',
								'".$materialitemid."',
								'".$version['versionid']."',
								'".$item['created']."',
								'".$sign.$diff."',
								'".$sign.$diff."',
								'0',
								'',
								'".TIMENOW."',
								'".$this->kclass->user['userid']."
								')
						");
						$batchid=$this->kclass->DB->insertID();	
						$this->kclass->DB->query("
							UPDATE inventoryitem SET batchid={$batchid} WHERE materialid={$item['materialid']} AND inventoryid={$this->kclass->input['inventoryid']} AND materialType={$this->kclass->input['mtypeid']}
						");					
						//新增批号记录
						$nbarcode=$this->kclass->DB->query("
							INSERT INTO barcode (materialid,supplierid,versionid,batchid,barcode,country,materialno,supplierno,arrivalTime,version,quantity,{$mtype},{$mtype2},created,creator)
							VALUES (
							'".$item['materialid']."',
							'".$supplier['supplierid']."',
							'".$version['versionid']."',
							'".$batchid."',
							'".$barcode."',
							'".$supplier['country']."',
							'".$item['materialno']."',
							'".$supplier['supplierno']."',
							'".$item['created']."',
							'".$version['vtitle']."',
							'".$sign.$diff."',
							'".$sign.$diff."',
							'0',
							'".TIMENOW."',
							'".$this->kclass->user['userid']."'
							)
						");
						$barcodeid = $this->kclass->DB->insertID();	
						$this->kclass->DB->query("
							UPDATE inventoryitem SET barcodeid={$barcodeid} WHERE materialid={$item['materialid']} AND inventoryid={$this->kclass->input['inventoryid']} AND materialType={$this->kclass->input['mtypeid']}
						");										
					}
					$recordid = $this->kclass->DB->queryFirst("SELECT recordid FROM inoutrecord WHERE module='inventory' AND itemid={$item['itemid']}");
					//更新收发卡信息
					if($recordid['recordid']!=''){
						$inoutrecord = $this->kclass->DB->query("UPDATE inoutrecord SET {$mtype}={$diff},{$balance}={$newStock} WHERE recordid={$recordid['recordid']}");
					}else{
						// 新增物料收发卡记录
						$inoutrecord =$this->kclass->DB->query("
							INSERT inoutrecord (`module`,`mid`,`materialid`,`materialitemid`,`versionid`,`itemid`,`addition`,`{$mtype}`,`{$balance}`,`{$mtype2}`,`{$balance2}`,`dateline`,`created`,`creator`) VALUE (
							'inventory',
							'".$this->kclass->input['inventoryid']."',
							'".$item['materialid']."',
							'".$materialitemid."',
							'".$version['versionid']."',
							'".$item['itemid']."',
							'".$adtion."',
							'".$diff."',
							'".$newStock."',
							'0',
							'0',
							'".TIMENOW."',
							'".TIMENOW."',
							'".$this->kclass->user['userid']."
							')
						");					
					}
					//更新盘点审核及完成状态
					$verifyInventory = $this->kclass->DB->query("UPDATE inventory SET {$verifyType}=1,ifverify=2 WHERE inventoryid={$this->kclass->input['inventoryid']}");
				}				
			}
			if($verifyInventory==true){
				$this->kclass->messager(array(
					'title' => $title.'成功！',
					'text' => '正在为您进行页面跳转...',
					'url' => '/s.php?module=inventory&action=view&inventoryid='.$this->kclass->input['inventoryid'].'&mtypeid=1',
					'sec' => 2
				));		
			}
		}
		
	}
		//删除本次盘点全部信息
		function kill(){
			if($this->kclass->input['inventoryid']<=0){
				$this->kclass->boinkIt('/s.php?module=inventory');
			}
			$inventory = $this->kclass->DB->queryFirst("
				SELECT title
				FROM `inventory`
				WHERE inventoryid='".$this->kclass->input['inventoryid']."'
			");
$body = <<<EOF
<form action="/s.php?module=inventory&action=dokill" name="inventory" method="post">
<input type="hidden" name="module" value="inventory">
<input type="hidden" name="inventoryid" value="{$this->kclass->input['inventoryid']}">
<table>
<thead>
<tr>
	<th>删除盘点：{$inventory['title']}</th>
</tr>
</thead>
<tbody>
<tr class="even">
	<td class="middle">你确定要删除盘点: <a href="/s.php?module=inventory&action=view&inventoryid={$this->kclass->input['inventoryid']}" class="big bold" target="_blank">{$inventory['inventoryno']}</a> 吗?</td>
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
			$this->kclass->page['title'] .= ' - 删除 - '.$inventory['inventoryno'];
			$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 删除盘点 - '.$inventory['title'], 'right' => '<a href="/s.php?module=inventory">返回列表</a>', 'body'=>$body));
		}
		
	function dokill(){
			/*if($this->kclass->input['inventoryid']<=0){
				$this->kclass->boinkIt('/s.php?module=inventory');
			}*/
			if(!$this->kclass->input['rt'] AND $this->kclass->input['confirm'] == 0){
				$this->kclass->boinkIt('/s.php?module=inventory&action=view&inventoryid='.$this->kclass->input['inventoryid']);
			}
			if($this->kclass->input['inventoryid'] < 0){
				$e = '<li>请从正确的页面进行操作，如果此问题持续存在，请联系网站管理员。</li>';
			}
			if(isset($e)){
				$this->kclass->messager(array(
					'title' => '删除盘点',
					'text' => '您在删除盘点的过程中有以下错误：<ul>'.$e.'</ul>',
					'url' => 'javascript:history.back()',
					'sec' => 3
				));
			}
			if($this->kclass->input['rt']=='view'){
				$rt = '&action=view&inventoryid='.$this->kclass->input['inventoryid'];
			}elseif($this->kclass->input['rt']=='list'){
				$rt = '#'.$this->kclass->input['inventoryid'];
			}
			$inventory=$this->kclass->DB->queryFirst("
				SELECT title
				FROM inventory
				WHERE inventoryid='".$this->kclass->input['inventoryid']."'
			");
			if($inventory){
				$this->kclass->DB->query("
					UPDATE `inventory`
					SET killed=".TIMENOW.",killer='".$this->kclass->user['userid']."'
					WHERE inventoryid='".$this->kclass->input['inventoryid']."'
				");
				$this->kclass->messager(array(
					'title' => '删除盘点成功',
					'text' => '盘点 <b>'.$inventory['title'].'</b> 已成功被标记为删除!',
					'url' => '/s.php?module=inventory&action='.$rt,
					'sec' => 2
				));
			}else{
				$this->kclass->messager(array(
					'title' => '删除盘点失败',
					'text' => '您要删除的盘点，不存在！',
					'url' => '/s.php?module=inventory&action='.$rt,
					'sec' => 3
				));
			}	
	}
	
	//删除盘点物资条目
	function killItem(){
		$itemid = $this->kclass->input['itemid'];
		$inventoryid = $this->kclass->input['inventoryid'];
		$kill = $this->kclass->DB->query("DELETE FROM inventoryitem WHERE itemid={$itemid}");
		if($kill==true){
			$this->kclass->messager(array(
				'title' => '删除盘点物资条目成功！',
				'text' => '正在为您进行页面跳转...',
				'url' => '/s.php?module=inventory&action=view&inventoryid='.$inventoryid.'&mtypeid=1',
				'sec' => 2
			));		
		}else{
			$this->kclass->messager(array(
				'title' => '删除盘点物资条目失败！',
				'text' => '正在为您进行页面跳转...',
				'url' => '/s.php?module=inventory&action=view&inventoryid='.$inventoryid.'&mtypeid=1',
				'sec' => 2
			));			
		}
	}
	
		function update(){
			$inventory=$this->kclass->DB->queryFirst("SELECT * FROM inventory	WHERE killed=0 AND inventoryid='".$this->kclass->input['inventoryid']."'");
			if($inventory){
				$mategialOrigin=$this->kclass->chooserMaterialOrigin(array('name'=>'originid','hasBlank'=>1,'selectedid'=>$this->kclass->input['originid']));
				$inventory['dateline']=date('Y-m-d',$inventory['dateline']);
				$inventory['nextround']=date('Y-m-d',$inventory['nextround']);
				$category=$this->kclass->chooserCategory(array('name'=>'categoryid','module'=>'material','hasBlank'=>1,'width'=>450,'parentid'=>19,'selectedid'=>$inventory['categoryid']));
				if($inventory['inventoryType']=='choose'){
					$selected1=' selected';
				}elseif($inventory['inventoryType']=='must'){
					$selected2=' selected';
				}

$body=<<<EOF
<form action="/s.php?module=inventory&action=doupdate" name="inventory" method="post">
<input type="hidden" name="module" value="inventory" />
<input type="hidden" name="action" value="doupdate" />
<input type="hidden" name="inventoryid" value="{$this->kclass->input['inventoryid']}" />
<table class="hundred">
<thead><tr><th colspan="4">新建盘点记录</th></tr></thead>
<tbody>
<tr class="odd">
	<td width="110">标　　题：<span class="red bold">*</span></td>
	<td><input type="text" style="width:450px" name="title" value="{$inventory['title']}" /></td>
	<td width="80">填 写 人：<span class="red bold">*</span></td>
	<td><input type="text" name="inventorier" value="{$inventory['inventorier']}"/></td>
</tr>
<tr class="even">
	<td>盘点类型：<span class="red bold">*</span></td><td><select name="inventoryType" style="width:100px"><option value="choose" {$selected1}>抽盘</option><option value="must" {$selected2}>必盘</option></select></td>
	<td>盘点时间：<span class="red bold">*</span></td>
	<td><input type="text" name="dateline" value="{$inventory['dateline']}" id="dateline" /><span class="small gray">时间格式例如：2012-02-02。中间用小横杠分开</span></td>
</tr>
<tr class="even">
<td class="small gray" colspan=4>
  注意事项：
	提交完盘点基本信息后，开始添加对物资盘点 </td>
</tr>
</tbody>
</table>
<table class="tlist tsuite" width="100%">
<tbody>
<tr class="odd center">
<td colspan=4 ><input type="submit" id="submitButton" value="  提交  " accesskey="s"><input type="reset" value="  复位  "/></td>
</tr>
</tbody>
</table>
</form>
EOF;
			}else{
				$body='数据错误，很抱歉！';
			}
			$this->kclass->page['title'].='修改盘点信息';
			$this->kclass->page['onload'].='var dates=$(\'#dateline,#nextround\').datepicker({onSelect:function(selectedDate){var option=this.id==\'startDate\'?\'minDate\':\'maxDate\',instance=$(this).data(\'datepicker\'),date=$.datepicker.parseDate(instance.settings.dateFormat || $.datepicker._defaults.dateFormat,selectedDate,instance.settings);dates.not(this).datepicker(\'option\',option,date);}});dc.tabhover()';
			$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - <a href="/s.php?module=inventory&action=view&inventoryid='.$inventory['inventoryid'].'">查看盘点信息</a> - 修改盘点信息', 'right'=>'<a href="/s.php?module=inventory">返回列表</a>','body'=>$body));
		}
		// 
		function doupdate(){
			if($this->kclass->input['requestMethod'] != 'post'){
				$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
			}else{
				if($this->kclass->input['title']==''){
					$e.='<li>请填写盘点记录的 标题</li>';
				}
				if($this->kclass->input['inventorier']==''){
					$e.='<li>请填写盘点记录的 填写人</li>';
				}
				if($this->kclass->input['dateline']==''){
					$e.='<li>请填写盘点记录的 盘点时间</li>';
				}
				if($this->kclass->input['inventoryType']==''){
					$e.='<li>请选择盘点记录的 盘点类型</li>';
				}
			}
			if(isset($e)){
				$this->kclass->messager(array(
					'title' => '修改盘点记录',
					'text' => '修改盘点记录<ul>'.$e.'</ul>',
					'url' => 'javascript:history.back()',
					'sec' => 3
				));
			}
			if($this->kclass->input['dateline']){
				$sd=explode('-',$this->kclass->input['dateline']);
				$dateline=mktime(0,0,0,$sd[1],$sd[2],$sd[0]);
			}else{
				$dateline='';
			}
			if($this->kclass->input['nextround']){
				$sd=explode('-',$this->kclass->input['nextround']);
				$nextround=mktime(0,0,0,$sd[1],$sd[2],$sd[0]);
			}else{
				$nextround='';
			}
			$this->kclass->DB->query("
				UPDATE `inventory` SET 
					`title`='".$this->kclass->input['title']."',
					`inventorier`='".$this->kclass->input['inventorier']."',
					`inventoryType`='".$this->kclass->input['inventoryType']."',
					`dateline`='".$dateline."',
					`modifier`='".$this->kclass->user['userid']."',
					`modified`='".TIMENOW."' 
				WHERE inventoryid='".$this->kclass->input['inventoryid']."'
			");

			$this->kclass->messager(array(
				'title' => '修改盘点记录',
				'text' => '盘点记录<b>'.$this->kclass->input['title'].'</b> 已修改成功，返回查看盘点记录并添加盘点物资!',
				'url' => '/s.php?module=inventory&action=view&inventoryid='.$this->kclass->input['inventoryid'],
				'sec' => 2
			));
		}
	
	//更改盘点物资条目
	function updateItem(){
		$inventoryid = $this->kclass->input['inventoryid'];
		$itemid = $this->kclass->input['itemid'];
		$item = $this->kclass->DB->queryFirst('SELECT firstInventory,checkInventory FROM inventoryitem WHERE itemid='.$itemid.'');
		$body = <<<EOF
<form action="/s.php?module=inventory&action=doupdateitem" method="post">
<input type="hidden" name="itemid" value="{$itemid}" />
<input type="hidden" name="inventoryid" value="{$inventoryid}" />
<table>
<thead>
	<tr>
		<th colspan="6">修改物资盘点：请填写需要修改的项目</th>
	</tr>
</thead>
<tbody>
	<tr>
		<td><span style="color:red">*</span>库存数：</td>
		<td><input type="text" name="stock" size="12" /></td>
		<td>初盘数：</td>
		<td><input type="text" name="firstInventory" size="12" /></td>
		<td>复盘数：</td>
		<td><input type="text" name="checkInventory" size="12" /></td>
	</tr>
	<tr>
		<td colspan="6" align="center"><input type="submit" name="submit" value="提交"> <input type="reset" name="reset" value="重置"></td>
	</tr>
</tbody>
</table>
</form>
EOF;
		$this->kclass->page['title'].='修改物资盘点';
		$this->kclass->page['onload'].='dc.listhover();dc.tabs({\'id\':\'material\'});dc.tabhover();dc.flow(\'listtr\')';
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - 修改物资盘点','right' =>'','body'=>$body));
	}
	//执行物资盘点条目修改
	function doupdateItem(){
		if($this->kclass->input['requestMethod']!='post'){
			$e = '<li>请从正确的页面进行操作，如果此问题持续存在，请联系网站管理员。</li>';
		}else{
			if($this->kclass->input['firstInventory']=='' AND $this->kclass->input['checkInventory']==''){
				$e = '<li>初盘数和复盘数请至少填写一项！</li>';
			}
			if($this->kclass->input['stock']==''){
				$e = '<li>库存数不能为空！</li>';
			}
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '修改物资盘点信息失败！',
				'text' => '您在修改物资盘点信息时存在以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 2	
				)
			);
		}
		if($this->kclass->input['firstInventory']!='' AND $this->kclass->input['checkInventory']!=''){
			$diff = $this->kclass->input['checkInventory']-$this->kclass->input['stock'];
			$sqlpart = 'stock='.$this->kclass->input['stock'].',firstInventory='.$this->kclass->input['firstInventory'].',checkInventory='.$this->kclass->input['checkInventory'].',difference='.$diff.'';
		}elseif($this->kclass->input['firstInventory']!='' AND $this->kclass->input['checkInventory']==''){
			$diff = $this->kclass->input['firstInventory']-$this->kclass->input['stock'];
			$sqlpart = 'stock='.$this->kclass->input['stock'].',firstInventory='.$this->kclass->input['firstInventory'].',difference='.$diff.'';		
		}elseif($this->kclass->input['firstInventory']=='' AND $this->kclass->input['checkInventory']!=''){
			$diff = $this->kclass->input['checkInventory']-$this->kclass->input['stock'];
			$sqlpart = 'stock='.$this->kclass->input['stock'].',checkInventory='.$this->kclass->input['checkInventory'].',difference='.$diff.'';		
		}
		$update = $this->kclass->DB->query("UPDATE inventoryitem SET {$sqlpart} WHERE itemid={$this->kclass->input['itemid']}");
		if($update==true){
			$this->kclass->messager(array(
				'title' => '修改物资盘点信息成功！',
				'text' => '正在为您跳转···',
				'url' => '/s.php?module=inventory&action=view&inventoryid='.$this->kclass->input['inventoryid'].'&mtypeid=1',
				'sec' => 2	
				)
			);
		}
	}
}
?>