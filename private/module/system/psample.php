<?php
class psample{
	var $type=array(
		array('id'=>1,'title'=>'电子元器件','abbr'=>'D'),
		array('id'=>2,'title'=>'五金类','abbr'=>'W'),
		array('id'=>3,'title'=>'塑料胶','abbr'=>'S'),
		array('id'=>4,'title'=>'工具类','abbr'=>'G'),
		array('id'=>5,'title'=>'辅料类','abbr'=>'F'),
		array('id'=>6,'title'=>'包装类','abbr'=>'B'),
		array('id'=>7,'title'=>'其它类','abbr'=>'Q'),
	);
	function autoRun(){
		$this->kclass->page['title']='样品单';
		$this->baseurl='<a href="/s.php">首页</a> - <a href="/s.php?module=psample">列表</a>';
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
			case 'upload':
				$this->upload();
			break;
			case 'doupload':
				$this->doupload();
			break;
			case 'validateItem':
				$this->validateItem();
			break;
			case 'dovalidateItem':
				$this->dovalidateItem();
			break;
			case 'listItem':
				$this->listItem();
			break;
			case 'addEvent':
				$this->addEvent();
			break;
			case 'insertEvent':
				$this->insertEvent();
			break;
			case 'updateEvent':
				$this->updateEvent();
			break;
			case 'doupdateEvent':
				$this->doupdateEvent();
			break;
			case 'removeEvent':
				$this->removeEvent();
			break;
			case 'killEvent':
				$this->killEvent();
			break;
			case 'meetingroomsamplelist':
				$this->meetingroomsamplelist();
			break;
			case 'addmeetingroomsample':
				$this->addmeetingroomsample();
			break;
			case 'insertmeetingroomsample':
				$this->insertmeetingroomsample();
			break;
			case 'meetingroomsampleview':
				$this->meetingroomsampleview();
			break;
			case 'updatemeetingroomsample':
				$this->updatemeetingroomsample();
			break;
			case 'doupdatemeetingroomsample':
				$this->doupdatemeetingroomsample();
			break;
			case 'killmeetingroomsample':
				$this->killmeetingroomsample();
			break;
			case 'removemeetingroomsample':
				$this->removemeetingroomsample();
			break;
			default:
				$this->mmlist();
		}
	}
	// 
	function mmlist(){
		if($this->kclass->input['show']=='all'){
			$condition='1=1';
		}else{
			$condition='s.killed=0';
		}
		$samples=$this->kclass->DB->query("
			SELECT s.psampleid,s.sampleno,s.typeid,s.applicant,s.customerid,s.supplierid,s.supplierTitle,s.linkman,s.shipmentDate,s.detail,s.attachs,s.cover,s.images,s.remark,s.killed,s.modified,s.created,
				sm.title AS shipmethod,
				d.title AS department,
				m.realname AS modifier,mem.realname AS creator
			FROM `psample` AS s
			LEFT JOIN `shipmethod` AS sm ON (sm.shipmethodid=s.shipmethodid) 
			LEFT JOIN `department` AS d ON (d.departmentid=s.departmentid) 
			LEFT JOIN `member` AS m ON (m.userid=s.modifier) 
			LEFT JOIN `member` AS mem ON (mem.userid=s.creator) 
			WHERE ".$condition."
			ORDER BY s.sampleno DESC
		");
		if($this->kclass->DB->numRows()){
			if($this->kclass->input['layout']=='grid'){
				$body='<ul id="mmlist" class="mmlist clear">';
			}else{
				$body='<table class="hundred"><thead><th width="15">ID</th><th>样品单号</th><th>样品单类型</th><th>需求部门</th><th>申请人</th><th>供应商</th><th>联系人</th><th>递送方式</th><th width="50">递送日期</th><th>操作</th></thead><tbody>';
			}
			$i=1;
			while($sample=$this->kclass->DB->fetchArray($samples)){
				$sampleType='';
				if($sample['typeid']!=''){
					$stypes=$this->kclass->DB->query("SELECT typeid,title FROM `sampletype` WHERE typeid IN (".$sample['typeid'].")");
					if($this->kclass->DB->numRows()){
						while($stype=$this->kclass->DB->fetchArray($stypes)){
							$sampleType .= '<span title="('.$stype['entitle'].') '.$stype['remark'].'" style="padding-right:12px">'.$stype['title'].'</span>';
						}
					}
				}
				$created=date('Y-m-d H:m',$sample['created']);
				$modified=date('Y-m-d H:m',$sample['modified']);
				if($sample['customerid']>0){
					$customer=$this->kclass->relatedCustomer(array('id'=>$sample['customerid']));
				}
				if($sample['supplierid']>0){
					$supplier=$this->kclass->relatedSupplier(array('id'=>$sample['supplierid']));
					$supplier1=$this->kclass->DB->queryFirst("SELECT title FROM supplier WHERE supplierid={$sample['supplierid']}");
					$supplierTitle='<a href="/s.php?module=psupplier&action=view&supplierid='.$sample['supplierid'].'">'.$supplier1['title'];
				}else{
					$supplierTitle=$sample['supplierTitle'];
				}
				$title='';
				$items=$this->kclass->DB->query("
					SELECT material,standard
					FROM psampleitem
					WHERE killed=0 AND sampleid={$sample['psampleid']}
					ORDER BY itemid ASC
				");
				if($this->kclass->DB->numRows()){
					while($item=$this->kclass->DB->fetchArray($items)){
						$title.=$item['material'].'　'.$item['standard'].'<br />';
					} 
				}
				if($sample['killed']>0){
					$link='<a href="/s.php?module=psample&action=restore&psampleid='.$sample['psampleid'].'&rt=list">恢复</a>';
				}else{
					$link='<a href="/s.php?module=psample&action=update&psampleid='.$sample['psampleid'].'&rt=list">改</a>　<a href="/p.php?action=psample&psampleid='.$sample['psampleid'].'" target="_blank">印</a>';
				}
				if($this->kclass->input['layout']=='grid'){//列表
					$body .= '<li title="由 '.$sample['creator'].' 建于 '.$created.$this->kclass->iif($sample['modifier']!='','，'.$sample['modifier'].' 改于 '.$modified,'').'"'.$this->kclass->iif($i%4==0,' class="end"','').'>
					<div class="mmlistt">
						<span class="right normal">';
					if($sample['killed']>0){
						$body .= '<a href="/s.php?module=psample&action=restore&psampleid='.$sample['psampleid'].'&rt=list">恢复</a>';
					}else{
						$body .= '<a href="/s.php?module=psample&action=kill&psampleid='.$sample['psampleid'].'&rt=list" onclick="return confirm(\'你确定要删除这个样品 '.$sample['title'].' 吗？\');">删</a> <a href="/s.php?module=psample&action=update&psampleid='.$sample['psampleid'].'&rt=list">改</a>';
					}
					$body .= '</span>
						<span class="small">'.$sample['attr'].'</span> <a href="/s.php?module=psample&action=view&psampleid='.$sample['psampleid'].'">'.$sample['title'].'</a><br>'.$this->kclass->iif($sample['attachs']>0,' <span class="attachFile" title="有'.$sample['attachs'].'个附件。"></span> ','').$this->kclass->iif($sample['images']>0,' <span class="attachImage" title="有'.$sample['images'].'个图片。"></span> ','').$this->kclass->iif($sample['entitle']!='','<span class="small">'.$sample['entitle'].'</span>','').'</div>
					<div class="mmlistb">
						<div><span class="small">类型：</span>'.$sampleType.'</div>'.$this->kclass->iif($sample['cover']!='','<div class="center"><img src="'.$sample['cover'].'"></div>','').'
						<div class="small clear"><span class="right" title="由 '.$sample['creator'].' 建于 '.date('y-m-d H:m',$sample['created']).'">'.$this->kclass->iif($sample['modifier']!='','由 最后'.$sample['modifier'].' 改于 '.date('Y-m-d H:m',$sample['modified']),'建于 '.date('Y-m-d H:m',$sample['created'])).'</span></div>
					</div>
					</li>';
				}else{
					$body .='<tr '.$this->kclass->iif($this->kclass->rotate(),'odd','even').'>
						<td>'.$i.'</td>
						<td class="tip" title="'.$title.'">'.$this->kclass->iif($sample['attachs']>0,' <span class="attachFile" title="有'.$sample['attachs'].'个附件。"></span> ','').$this->kclass->iif($sample['images']>0,' <span class="attachImage" title="有'.$sample['images'].'个图片。"></span> ','').'<a href="/s.php?module=psample&action=view&psampleid='.$sample['psampleid'].'">'.$sample['sampleno'].'</a></td>
						<td>'.$sampleType.'</td>
						<td>'.$sample['department'].'</td>
						<td>'.$sample['applicant'].'</td>
						<td>'.$supplierTitle.'</td>
						<td>'.$sample['linkman'].'</td>
						<td>'.$sample['shipmethod'].'</td>
						<td>'.date('Y-m-d',$sample['shipmentDate']).'</td>
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
			$layoutLink='<a href="/s.php?module=psample&action=list&layout=list'.str_replace('&layout=grid', '', $queryPart).'">列表</a> 格子';
		}else{
			$layoutLink='列表 <a href="/s.php?module=psample&action=list&layout=grid'.str_replace('&layout=list', '', $queryPart).'">格子</a>';
		}
		if($this->kclass->input['show']=='all'){
			$showLink='<a href="/s.php?module=psample&action=list'.str_replace('&show=all', '', $queryPart).'">默认</a> 所有<span class="small gray">(包括删除)</span>';
		}else{
			$showLink='默认 <a href="/s.php?module=psample&action=list&show=all'.str_replace('&show=all', '', $queryPart).'">所有<span class="small gray">(包括删除)</span></a>';
		}
		$this->kclass->page['onload'] .= 'dc.tabhover();dc.tips()';
		$this->kclass->page['title'] .= ' - 列表';
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 列表','right' => '<span class="small">显示方式：</span>'.$showLink.'　|　<span class="small">布局：</span>'.$layoutLink.'　|　<a href="/s.php?module=psample&action=add">新建样品单</a>　|　<a href="/s.php?module=psample&action=listItem">样品明细列表</a>','body'=>$body));
	}

	function meetingroomsamplelist(){
		if($this->kclass->input['show']=='all'){
			$condition='1=1';
		}else{
			$condition='ms.killed=0';
		}
		if($this->kclass->input['type']!=''){
			$condition.=' AND ms.type='.$this->kclass->input['type'].'';
		}
		if($this->kclass->input['sampleno']!=''){
			$condition.="  AND ms.sampleno LIKE '%".$this->kclass->input['sampleno']."%'";
		}
		if($this->kclass->input['title']!=''){
			$condition.="  AND ms.title LIKE '%".$this->kclass->input['title']."%'";
		}
		$samples=$this->kclass->DB->query("
			SELECT * FROM `meetingroomsample` AS ms
			WHERE ".$condition."
			ORDER BY ms.created DESC
		");
		$typeList='<a href="/s.php?module=psample&action=meetingroomsamplelist&type=1">有绳灯</a>　<a href="/s.php?module=psample&action=meetingroomsamplelist&type=2">无绳灯</a>　<a href="/s.php?module=psample&action=meetingroomsamplelist&type=3">充电器</a>　<a href="/s.php?module=psample&action=meetingroomsamplelist&type=4">配件</a>';
		$body=<<<EOF
			<form action="/s.php?module=psample&action=meetingroomsamplelist" method="get">
<input type="hidden" name="module" value="psample" >
<input type="hidden" name="action" value="meetingroomsamplelist" >
<table class="hundred">
<thead><tr><th colspan=3>查询【<a href="/s.php?module=psample&action=meetingroomsamplelist">全部</a>】</th></tr></thead>
<tbody>
<tr><td>分　　类：</td><td colspan=2>{$typeList}</td></tr>
<tr><td>样品编号：</td><td><input type="text" name="sampleno" value="{$this->kclass->input['sampleno']}" size="150"></td><td><input type="submit" value="查询" ></td></tr>
<tr><td>样品名：</td><td><input type="text" name="title" value="{$this->kclass->input['title']}" size="150"></td><td><input type="submit" value="查询" ></td></tr>
</tbody>
</table>
</form>
EOF;
		if($this->kclass->DB->numRows()){
			$body.='<table class="hundred"><thead><th width="15">ID</th><th width="60">样品名称</th><th width="60">样品编号</th><th width="60">样品类型</th><th width="70">序列号</th><th width="70">规格</th><th width="60">PCB</th><th width="100">特性参数</th><th width="80">充满时间</th><th width="60">操作</th></thead><tbody>';
			$i=1;
			while($sample=$this->kclass->DB->fetchArray($samples)){
				switch($sample['type']){
					case 1:
						$type='有绳灯';
					break;
					case 2:
						$type='无绳灯';
					break;
					case 3:
						$type='充电器';
					break;
					case 4:
						$type='配件';
					break;
				}
				$operation='<a href="/s.php?module=psample&action=updatemeetingroomsample&meetingroomsampleid='.$sample['meetingroomsampleid'].'"">修改</a>　<a href="/s.php?module=psample&action=removemeetingroomsample&meetingroomsampleid='.$sample['meetingroomsampleid'].'"">删除</a>';
				$body.='<tr '.$this->kclass->iif($this->kclass->rotate(),'odd','even').'>
					<td>'.$i.'</td>
					<td>'.$sample['title'].'</td>
					<td><a href="/s.php?module=psample&action=meetingroomsampleview&meetingroomsampleid='.$sample['meetingroomsampleid'].'">'.$sample['sampleno'].'</a></td>
					<td>'.$type.'</td>
					<td>'.$sample['batchno'].'</td>
					<td>'.$sample['standard'].'</td>
					<td>'.$sample['PCB'].'</td>
					<td>'.$sample['character'].'</td>
					<td>'.$sample['chargetime'].'</td>
					<td>'.$operation.'</td></tr>';
				$i++;
			}
			$body.='</tbody></table>';
		}else{
			$body.='暂无相关记录';
		}
		if($this->kclass->input['layout']=='grid'){
			$layoutLink='<a href="/s.php?module=psample&action=meetingroomsamplelist&layout=list'.str_replace('&layout=grid', '', $queryPart).'">列表</a> 格子';
		}else{
			$layoutLink='列表 <a href="/s.php?module=psample&action=meetingroomsamplelist&layout=grid'.str_replace('&layout=list', '', $queryPart).'">格子</a>';
		}
		if($this->kclass->input['show']=='all'){
			$showLink='<a href="/s.php?module=psample&action=meetingroomsamplelist'.str_replace('&show=all', '', $queryPart).'">默认</a> 所有<span class="small gray">(包括删除)</span>';
		}else{
			$showLink='默认 <a href="/s.php?module=psample&action=meetingroomsamplelist&show=all'.str_replace('&show=all', '', $queryPart).'">所有<span class="small gray">(包括删除)</span></a>';
		}
		$this->kclass->page['onload'] .= 'dc.tabhover();dc.tips()';
		$this->kclass->page['title'] .= ' - 会议室样品列表';
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 会议室样品列表','right' => '<span class="small">显示方式：</span>'.$showLink.'　|　<span class="small">布局：</span>'.$layoutLink.'　|　<a href="/s.php?module=psample&action=addmeetingroomsample">新建会议室样品单</a>','body'=>$body));
	}
	function meetingroomsampleview(){
		if(!$this->kclass->input['meetingroomsampleid']){
			$this->kclass->boinkIt('/s.php?module=psample&action=meetingroomsamplelist');
		}
		$sample=$this->kclass->DB->queryFirst("SELECT * FROM `meetingroomsample` WHERE killed=0 AND meetingroomsampleid='".$this->kclass->input['meetingroomsampleid']."'");
		$type='<input type="radio" name="sampletype"  value="1" '.$this->kclass->iif($sample['type']==1, 'checked', '').'>有绳灯 <input type="radio" name="sampletype"  value="2" '.$this->kclass->iif($sample['type']==2, 'checked', '').'>无绳灯 <input type="radio" name="sampletype"  value="3" '.$this->kclass->iif($sample['type']==3, 'checked', '').'>充电器 <input type="radio" name="sampletype"  value="4" '.$this->kclass->iif($sample['type']==4, 'checked', '').'>配件';
		$body=<<<EOF
<table class="hundred">
<thead>
<tr>
	<th colspan="4">会议室样品单</th>
</tr>
</thead>
<tbody>
<tr class="odd">
	<td width="10%">样品编码：<span class="red bold">*</span></td>
	<td width="40%">{$sample['sampleno']}</td>
	<td width="10%">样品名称：<span class="red bold">*</span></td>
	<td>{$sample['title']}</td>
</tr>
<tr class="even">
	<td>样品类型：<span class="red bold">*</span></td>
	<td>{$type}</td>
	<td>序 列 号：<span class="red bold">*</span></td>
	<td>{$sample['batchno']}</td>
</tr>
<tr class="odd">
	<td>产品规格：</td>
	<td >{$sample['standard']}</td>
	<td>PCB：</td>
	<td>{$sample['PCB']}</td>
</tr>

<tr class="odd">
	<td valign="top">特性参数：</td>
	<td>{$sample['character']}</td>
	<td valign="top">充满时间：</td>
	<td>{$sample['chargetime']}</td>
</tr>
<tr class="odd">
	<td valign="top">备		注：</td>
	<td colspan="3">{$sample['remark']}</td>
	
</tr>
</tbody>
</table>
EOF;
		$this->kclass->page['onload'] .= "dc.tabhover()";
		$this->kclass->page['title'] .= ' - '.$sample['sampleno'];
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title' => $this->baseurl . ' - <a href="/s.php?module=psample&action=meetingroomsamplelist">会议室样品列表</a> - '.$sample['sampleno'],'right' => '<span class="small">操作：</span><a href="/s.php?module=psample&action=updatemeetingroomsample&meetingroomsampleid='.$this->kclass->input['meetingroomsampleid'].'">修改</a>　<a href="/s.php?module=psample&action=removemeetingroomsample&meetingroomsampleid='.$this->kclass->input['meetingroomsampleid'].'">删除</a>','body'=>$body));
	}
	//
	function addmeetingroomsample(){
		$body=<<<EOF
<form action="/s.php?module=psample&action=insertmeetingroomsample" name="psample" method="post">
<input type="hidden" name="module" value="psample">
<input type="hidden" name="action" value="insertmeetingroomsample">
<table class="hundred">
<thead>
<tr>
	<th colspan="4">新建会议室样品单：<span class="small red">(由研发一部填写)</span></th>
</tr>
</thead>
<tbody>
<tr class="odd">
	<td>样品编码：<span class="red bold">*</span></td>
	<td><input type="text" name="sampleno" id="sampleno" value=""></td>
	<td>样品名称：<span class="red bold">*</span></td>
	<td><input type="text" name="title" id="title" value=""></td>
</tr>
<tr class="even">
	<td>样品类型：<span class="red bold">*</span></td>
	<td><input type="radio" name="sampletype"  value="1">有绳灯 <input type="radio" name="sampletype"  value="2">无绳灯 <input type="radio" name="sampletype"  value="3">充电器 <input type="radio" name="sampletype"  value="4">配件 </td>
	<td>序 列 号：</td>
	<td><input type="text" name="batchno" id="batchno" value=""></td>
</tr>
<tr class="odd">
	<td>产品规格：</td>
	<td ><input type="text" style="width:500px" name="standard" value=""></td>
	<td>PCB：</td>
	<td><input type="text" id="PCB" name="PCB" value="" ></td>
</tr>

<tr class="odd">
	<td valign="top">特性参数：</td>
	<td><textarea name="character" id="character" style="width:350px;height:70px"></textarea></td>
	<td valign="top">充满时间：</td>
	<td><textarea name="chargetime" id="chargetime" style="width:350px;height:70px"></textarea></td>
</tr>
<tr class="odd">
	<td valign="top">备		注：</td>
	<td colspan="3"><textarea name="remark" style="width:350px;height:70px"></textarea></td>
	
</tr>
</tbody>
</table>
<table class="hundred">
<tbody>
<tr class="odd">
	<td colspan="4" align="center">
		<input type="submit" value="  保存  " accesskey="s">　　
		<input type="reset" value="  复位  ">
	</td>
</tr>
<tr class="even">
	<td class="small gray">
	注意事项：<ul>
	<li>未有列项的内容，写入“备注”中，长度上限3万中文字。</li>
	</ul>
	</td>
</tr>
</tbody>
</table>
</form>
EOF;
		$this->kclass->page['title'] .= ' - 新建会议室样品';
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title' => $this->baseurl . ' - <a href="/s.php?module=psample&action=meetingroomsamplelist">会议室样品列表</a> - 新建会议室样品','right' => '<a href="/s.php?module=psample&action=meetingroomsamplelist">返回列表</a>','body'=>$body));
	}
	//
	function insertmeetingroomsample(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e='<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			if($this->kclass->input['sampleno']==''){
				$e.='<li>请输入样品编码。</li>';
			}
			if($this->kclass->input['title']==''){
				$e.='<li>请输入样品名称。</li>';
			}
			if($this->kclass->input['sampletype']==''){
				$e.='<li>请输入样品类型。</li>';
			}
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '新建会议室样品单',
				'text' => '您在新建会议室样品单的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));		
		}
		$this->kclass->DB->query("INSERT INTO `meetingroomsample`
		(`title`,`sampleno`,`type`,`standard`,`batchno`,`PCB`,`character`,`chargetime`,`remark`,`created`,`creator`)
		VALUES
		('".$this->kclass->input['title']."','".$this->kclass->input['sampleno']."','".$this->kclass->input['sampletype']."','".$this->kclass->input['standard']."','".$this->kclass->input['batchno']."','".$this->kclass->input['PCB']."','".$this->kclass->input['character']."','".$this->kclass->input['chargetime']."','".$this->kclass->input['remark']."','".TIMENOW."','".$this->kclass->user['userid']."')
		");
		$this->kclass->messager(array(
			'title' => '新建会议室样品单',
			'text' => '会议室样品单已经新建成功',
			'url' => '/s.php?module=psample&action=meetingroomsamplelist',
			'sec' => 2
		));	
	}
	//
	function updatemeetingroomsample(){
		if(!$this->kclass->input['meetingroomsampleid']){
			$this->kclass->boinkIt('/s.php?module=psample&action=meetingroomsamplelist');
		}
		$sample=$this->kclass->DB->queryFirst("SELECT * FROM `meetingroomsample` WHERE killed=0 AND meetingroomsampleid='".$this->kclass->input['meetingroomsampleid']."'");
		$type='<input type="radio" name="sampletype"  value="1" '.$this->kclass->iif($sample['type']==1, 'checked', '').'>有绳灯 <input type="radio" name="sampletype"  value="2" '.$this->kclass->iif($sample['type']==2, 'checked', '').'>无绳灯 <input type="radio" name="sampletype"  value="3" '.$this->kclass->iif($sample['type']==3, 'checked', '').'>充电器 <input type="radio" name="sampletype"  value="4" '.$this->kclass->iif($sample['type']==4, 'checked', '').'>配件';
		$character=$this->kclass->parseConvertValue($sample['character']);
		$chargetime=$this->kclass->parseConvertValue($sample['chargetime']);
		$body=<<<EOF
<form action="/s.php?module=psample&action=doupdatemeetingroomsample" name="psample" method="post">
<input type="hidden" name="module" value="psample">
<input type="hidden" name="action" value="doupdatemeetingroomsample">
<input type="hidden" name="meetingroomsampleid" value="{$this->kclass->input['meetingroomsampleid']}">
<table class="hundred">
<thead>
<tr>
	<th colspan="4">修改会议室样品单：<span class="small red">(由研发一部填写)</span></th>
</tr>
</thead>
<tbody>
<tr class="odd">
	<td>样品编码：<span class="red bold">*</span></td>
	<td><input type="text" name="sampleno" id="sampleno" value="{$sample['sampleno']}"></td>
	<td>样品名称：<span class="red bold">*</span></td>
	<td><input type="text" name="title" id="title" value="{$sample['title']}"></td>
</tr>
<tr class="even">
	<td>样品类型：<span class="red bold">*</span></td>
	<td>{$type}</td>
	<td>序 列 号：</td>
	<td><input type="text" name="batchno" id="batchno" value="{$sample['batchno']}"></td>
</tr>
<tr class="odd">
	<td>产品规格：</td>
	<td ><input type="text" style="width:500px" name="standard" value="{$sample['standard']}"></td>
	<td>PCB：</td>
	<td><input type="text" id="PCB" name="PCB" value="{$sample['PCB']}" ></td>
</tr>

<tr class="odd">
	<td valign="top">特性参数：</td>
	<td><textarea name="character" id="character" style="width:350px;height:70px">{$character}</textarea></td>
	<td valign="top">充满时间：</td>
	<td><textarea name="chargetime" id="chargetime" style="width:350px;height:70px">{$chargetime}</textarea></td>
</tr>
<tr class="odd">
	<td valign="top">备		注：</td>
	<td colspan="3"><textarea name="remark" style="width:350px;height:70px">{$sample['remark']}</textarea></td>
	
</tr>
</tbody>
</table>
<table class="hundred">
<tbody>
<tr class="odd">
	<td colspan="4" align="center">
		<input type="submit" value="  保存  " accesskey="s">　　
		<input type="reset" value="  复位  ">
	</td>
</tr>
<tr class="even">
	<td class="small gray">
	注意事项：<ul>
	<li>未有列项的内容，写入“备注”中，长度上限3万中文字。</li>
	</ul>
	</td>
</tr>
</tbody>
</table>
</form>
EOF;
		$this->kclass->page['title'] .= ' - 修改会议室样品';
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title' => $this->baseurl . ' - <a href="/s.php?module=psample&action=meetingroomsamplelist">会议室样品列表</a> - 修改会议室样品','right' => '<a href="/s.php?module=psample&action=meetingroomsamplelist">返回列表</a>','body'=>$body));	
	}
	//
	function doupdatemeetingroomsample(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e='<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			if($this->kclass->input['sampleno']==''){
				$e.='<li>请输入样品编码。</li>';
			}
			if($this->kclass->input['title']==''){
				$e.='<li>请输入样品名称。</li>';
			}
			if($this->kclass->input['sampletype']==''){
				$e.='<li>请输入样品类型。</li>';
			}
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '修改会议室样品单',
				'text' => '您在修改会议室样品单的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));		
		}
		echo $this->kclass->input['sampleno'];
		echo $this->kclass->input['meetingroomsampleid'];
		$this->kclass->DB->query("UPDATE `meetingroomsample` SET
			`title`='".$this->kclass->input['title']."',
			`sampleno`='".$this->kclass->input['sampleno']."',
			`type`='".$this->kclass->input['sampletype']."',
			`standard`='".$this->kclass->input['standard']."',
			`batchno`='".$this->kclass->input['batchno']."',
			`PCB`='".$this->kclass->input['PCB']."',
			`character`='".$this->kclass->input['character']."',
			`chargetime`='".$this->kclass->input['chargetime']."',
			`remark`='".$this->kclass->input['remark']."',
			`modified`='".TIMENOW."',
			`modifier`='".$this->kclass->user['userid']."'
			WHERE meetingroomsampleid='".$this->kclass->input['meetingroomsampleid']."'
		");
		$this->kclass->messager(array(
			'title' => '修改会议室样品单',
			'text' => '会议室样品单已经修改成功',
			'url' => '/s.php?module=psample&action=meetingroomsamplelist',
			'sec' => 2
		));	
	}

	function removemeetingroomsample(){
		if(!$this->kclass->input['meetingroomsampleid']){
			$this->kclass->boinkIt('/s.php?module=psample&action=meetingroomsamplelist');
		}
		$sample=$this->kclass->DB->queryFirst("SELECT * FROM `meetingroomsample` WHERE killed=0 AND meetingroomsampleid='".$this->kclass->input['meetingroomsampleid']."'");
		$body=<<<EOF
<form action="/s.php?module=psample&action=killmeetingroomsample" name="psample" method="post">
<input type="hidden" name="module" value="psample">
<input type="hidden" name="action" value="killmeetingroomsample">
<input type="hidden" name="meetingroomsampleid" value="{$this->kclass->input['meetingroomsampleid']}">
<table>
<thead>
<tr>
	<th>删除样品：{$sample['sampleno']}</th>
</tr>
</thead>
<tbody>
<tr class="even">
	<td class="middle">你确定要删除样品: <a href="/s.php?module=psample&action=meetingroomsampleview&psampleid={$this->kclass->input['psampleid']}" class="big bold" target="_blank">{$sample['sampleno']}</a> 吗?</td>
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
		$this->kclass->page['title'] .= ' - 删除会议室样品';
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title' => $this->baseurl . ' - <a href="/s.php?module=psample&action=meetingroomsamplelist">会议室样品列表</a> - 删除会议室样品','right' => '<a href="/s.php?module=psample&action=meetingroomsamplelist">返回列表</a>','body'=>$body));
	}

	function killmeetingroomsample(){
		if($this->kclass->input['meetingroomsampleid']<=0){
			$this->kclass->boinkIt('/s.php?module=psample&action=meetingroomsamplelist');
		}
		if($this->kclass->input['confirm'] == 0){
			$this->kclass->boinkIt('/s.php?module=psample&action=meetingroomsamplelist');
		}
		if($this->kclass->input['meetingroomsampleid']<0){
			$e='<li>请从正确的页面进行操作，如果此问题持续存在，请联系网站管理员。';
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '删除会议室样品单',
				'text' => '您在删除会议室样品单的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));		
		}
		$sample=$this->kclass->DB->queryFirst("
			SELECT *
			FROM `meetingroomsample`
			WHERE meetingroomsampleid='".$this->kclass->input['meetingroomsampleid']."'
		");
		if($sample){
			$this->kclass->DB->query("
				UPDATE `meetingroomsample` 
				SET killed=".TIMENOW.",killer=".$this->kclass->user['userid']."
				WHERE meetingroomsampleid='".$this->kclass->input['meetingroomsampleid']."'
			");
			$this->kclass->messager(array(
				'title' => '删除样品成功',
				'text' => '样品 <b>'.$sample['sampleno'].'</b> 已成功被标记为删除!',
				'url' => '/s.php?module=psample&action=meetingroomsamplelist',
				'sec' => 2
			));
		}else{
			$this->kclass->messager(array(
				'title' => '删除样品失败',
				'text' => '你要删除的样品不存在!',
				'url' => '/s.php?module=psample&action=meetingroomsamplelist',
				'sec' => 3
			));
		}
		
	}

	// 
	function view(){
		if(!$this->kclass->input['psampleid'] > 0)$this->kclass->boinkIt('/s.php?module=psample');
		$sample=$this->kclass->DB->queryFirst("
			SELECT s.psampleid,s.sampleno,s.typeid,s.customerid,s.supplierid,s.supplierTitle,s.linkman,s.shipmentDate,s.detail,s.remark,s.modified,s.created,s.killed,
				sm.title AS shipmethod,
				d.title AS department,
				m.realname AS modifier,mem.realname AS creator
			FROM `psample` AS s
			LEFT JOIN `shipmethod` AS sm ON (sm.shipmethodid=s.shipmethodid) 
			LEFT JOIN `department` AS d ON (d.departmentid=s.departmentid) 
			LEFT JOIN `member` AS m ON (m.userid=s.modifier) 
			LEFT JOIN `member` AS mem ON (mem.userid=s.creator) 
			WHERE s.psampleid='".$this->kclass->input['psampleid']."'
		");
		if(!$sample)	$this->kclass->boinkIt('/s.php?module=psample');
		if($sample['typeid']!=''){
			$stypes=$this->kclass->DB->query("SELECT typeid,title FROM `sampletype` WHERE typeid IN (".$sample['typeid'].")");
			if($this->kclass->DB->numRows()){
				while($stype=$this->kclass->DB->fetchArray($stypes)){
					$sampleType .= '<span title="('.$stype['entitle'].') '.$stype['remark'].'" style="padding-right:12px">'.$stype['title'].'</span>';
				}
			}
		}
		if($sample['customerid']>0){
			$customer=$this->kclass->relatedCustomer(array('id'=>$sample['customerid']));
		}
		if($sample['supplierid']>0){
			$supplier=$this->kclass->relatedSupplier(array('id'=>$sample['supplierid']));
			$supplier1=$this->kclass->DB->queryFirst("SELECT title FROM supplier WHERE supplierid={$sample['supplierid']}");
			$supplierTitle=$supplier1['title'];
		}else{
			$supplierTitle=$sample['supplierTitle'];
		}

		if($sample['modified']>0){
			$modified='，'.$sample['modifier'].' 于 '.date('Y-m-d H:i:s',$sample['modified']).' 最后修改';
		}
		if($sample['shipmentDate']>0){
			$shipmentDate=' <span class="darkred">'.date('Y-m-d',$sample['shipmentDate']).'</span>';
		}
		$created=date('Y-m-d H:i:s',$sample['created']);
		
		$total=0;
		$item=$this->kclass->relatedPSampleItem(array('psampleid'=>$sample['psampleid']));
		$total=$item['total'];
		$fee=$this->kclass->relatedFee(array('module'=>'psample','psampleid'=>$sample['psampleid']));
		$total += $fee['total'];
		$discounts=$this->kclass->DB->query("SELECT * FROM `discount` WHERE killed=0 AND module='psample' AND mid='".$sample['psampleid']."'");
		if($this->kclass->DB->numRows()){
			while($discount=$this->kclass->DB->fetchArray($discounts)){
				if($item['total']>0){
					$amount=0;
					if($discount['sign']=='%'){
						$discount['discount']=intVal($discount['discount']);
						$amount=($item['total'] * $discount['discount'] / 100);
					}else{
						$amount=$discount['discount'];
					}
					if($discount['addition']=='+'){
						$total += $amount;
					}else{
						$total -= $amount;
					}
				}
				$discounttr .= '<tr class="'.$this->kclass->iif($this->kclass->rotate(),'even','odd').'">
				<td>'.$discount['reason'].'</td>
				<td align="right">'.$discount['addition'].''.$discount['discount'].''.$this->kclass->iif($discount['sign']=='%',$discount['sign'],'').'</td>
				<td align="right">'.$discount['addition'].number_format($amount,2).'</td>
				<td align="right">'.$sample['currencySymbol'].number_format($total,2).'</td>
				</tr>';
			}
			$discounttr='<tr><td title="Reason">折扣说明(理由)</td><td title="Discount" align="right">折扣</td><td title="Amount" align="right">计算折扣数</td><td title="Amount" align="right">折扣后总数</td></tr>'.$discounttr.'<tr><td colspan="12" align="right">折后计：<span class="bold">'.$sample['currencyTitle'].' '.$sample['currencySymbol'].number_format($total,2).'</span></td></tr>';
		}else{
			$discounttr='<tr><td class="gray"><i>无折扣信息</i></td></tr>';
		}
		$total=number_format($total,2);
		// attach
		$attach=$this->kclass->getAttachs(array('module'=>'psample','mid'=>$sample['psampleid']));
		if($attach!= false){
			$attachs='<div class="clear">'.$attach.'</div>';
		}
		/*$events=$this->kclass->DB->query("
			SELECT event.*,
				dc.title AS deliveryCurrency,
				sc.title AS sampleCurrency,
				shipmethod.title AS shipmethod 
			FROM sampleevent AS `event`
			LEFT JOIN currency AS dc ON (dc.currencyid=`event`.deliveryCurrency) 
			LEFT JOIN currency AS sc ON (sc.currencyid=`event`.sampleCurrency) 
			LEFT JOIN shipmethod ON (shipmethod.shipmethodid=`event`.shipmethodid) 
			WHERE `event`.killed=0 AND `event`.psampleid='".$sample['psampleid']."'
			ORDER BY `event`.modified DESC
		");
		if($this->kclass->DB->numRows()){
			$eventtbl='<table class="hundred"><thead><tr><th colspan="7">收发与费用</th></tr><tr><th>收发类型</th><th>收发数量</th><th>收发日期</th><th>递送方式</th><th>递送费用</th><th>样品费用</th><th>操作</th></tr></thead><tbody>';
			$this->kclass->tbline+=1;
			while($event=$this->kclass->DB->fetchArray($events)){
				$eventtbl.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').'"><td'.$this->kclass->iif($event['direction']=='sent',' class="green">发出',' class="red">收到').'</td><td'.$this->kclass->iif($event['totalQuantity']=='all','>全部',' class="bold">部分').'</td><td>'.date('Y-m-d',$event['deliveryDate']).'</td><td>'.$event['shipmethod'].'</td><td>'.$event['deliveryCurrency'].$event['deliveryFee'].$this->kclass->iif($event['deliveryCaption']!='',' ('.$event['deliveryCaption'].')','').'</td><td>'.$event['sampleCurrency'].$event['sampleFee'].$this->kclass->iif($event['sampleCaption']!='',' ('.$event['sampleCaption'].')','').'</td><td><a href="/s.php?module=psample&action=removeEvent&eventid='.$event['eventid'].'" onclick="return confirm(\'你确定要删除这条收发与费用记录吗？(删除不可恢复！)\')">删</a> <a href="/s.php?module=psample&action=updateEvent&eventid='.$event['eventid'].'">改</a></td></tr>';
			}
			$eventtbl .= '</tbody></table>';
		}else{
			$event['off']=' disabled';
		}*/
$body=<<<EOF
<div class="title"><span class="right small gray">由 {$sample['cname']} 于 {$created} 建立{$modified}。</span>{$sample['sampleno']}</div>
<dl id="sample" class="tabs">
	<dt>资料</dt>
	<dt title="询价记录"{$event['off']}>收发与费用{$event['count']}</dt>
	<dt title="相关客户基本资料"{$customer['off']}>相关客户</dt>
	<dt title="相关供应商基本资料"{$supplier['off']}>相关供应商</dt>
	<dd>
<table cellspacing="10" width="100%">
	<tr class="odd">
	<td width="80">样品编号：<span class="red bold">*</span></td><td width="650" ><span class="red middle bold">{$sample['sampleno']}</span></td>
	<td width="80">样品类型：<span class="red bold">*</span></td>	<td>{$sampleType}</td>
</tr>
<tr class="even">
	<td>供 应 商：<span class="red bold">*</span></td><td>{$supplierTitle}</td>
	<td>客　　户：</td>	<td>{$customer['link']}</td>
</tr>
<tr class="odd">
	<td>联系人员：</td>	<td>{$sample['linkman']}</td>
	<td>递送方式：<span class="red bold">*</span></td><td>{$sample['shipmethod']}</td>
</tr>
<tr class="even">
	<td>递送日期：<span class="red bold">*</span></td><td>{$shipmentDate}</td>
	<td>需求部门：<span class="red bold">*</span></td><td>{$sample['department']}</td>
</tr>
<tr class="odd">
	<td valign="top">详细描述：</td><td>{$sample['detail']}</td>
	<td valign="top">备　　注：</td><td>{$sample['remark']}</td>
</tr>
</table>
<table class="hundred">
<thead>
<tr>
	<th colspan="12">样品明细表 (Item Information)</th>
</tr>
</thead>
<tbody>
{$item['tr']}
</tbody>
</table>
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
<table class="hundred">
<thead>
<tr>
	<th colspan="4">折扣信息 (Discount Information)</th>
</tr>
</thead>
<tbody>
{$discounttr}
</tbody>
</table>
<table class="hundred">
<tbody>
<tr><td align="right">总计(Total Amount)：<span class="middle bold darkred">{$sample['currencyTitle']} {$sample['currencySymbol']}{$total}</span></td></tr>
</tbody>
</table>
{$eventtbl}
{$attachs}
	</dd>
	<dd>{$eventtbl}</dd>
	<dd>{$customer['panel']}</dd>
	<dd>{$supplier['panel']}</dd>
</dl>
EOF;
		$this->kclass->page['onload'] .= "dc.tabs({'id':'sample'});dc.tabhover()";
		$this->kclass->page['title'] .= ' - '.$sample['title'];
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title' => $this->baseurl.' - 样品单信息','right' => '<span class="small">打印：</span><a href="/p.php?action=sample&psampleid='.$sample['psampleid'].'" target="_blank">基本资料</a>　<a href="/p.php?action=sample&psampleid='.$sample['psampleid'].'" target="_blank">收发费用</a>　|　'.$this->kclass->iif($sample['killed']>0,'<a href="/s.php?module=psample&action=restore&psampleid='.$sample['psampleid'].'&rt=view">恢复</a>','<a href="/s.php?module=psample&action=remove&psampleid='.$sample['psampleid'].'&rt=view">删除</a>　<a href="/s.php?module=psample&action=update&psampleid='.$sample['psampleid'].'&rt=view">修改</a>　<a href="/s.php?module=psample&action=validateItem&psampleid='.$sample['psampleid'].'">验收</a>　|　<a href="/s.php?module=psample&action=addEvent&psampleid='.$sample['psampleid'].'&rt=view">新建收发&费用</a>　|　<a href="/s.php?module=psample&action=upload&psampleid='.$sample['psampleid'].'&rt=view">上传其他附件</a>'),'body'=>$body));
	}
	//
	function add(){
		$defaultCustomer=$this->kclass->DB->queryFirst("SELECT title FROM customer WHERE customerid=1");
		$sampletype=$this->kclass->chooserSampleType(array('name'=>'typeid','width'=>350,'module'=>'produce','selectedid'=>$this->kclass->input['typeid']));
		$supplier=$this->kclass->chooserSupplier(array('hName'=>'supplierid','hId'=>'supplierid','name'=>'supplier','id'=>'supplier','width'=>350));
		$customer = $this->kclass->chooserCustomer(array('hName'=>'customerid','name'=>'customer','value'=>$defaultCustomer['title'],'width'=>350,'selectedid'=>1));
		$shipmethod=$this->kclass->chooserShipmethod(array('name'=>'shipmethodid','hasBlank'=>1,'width'=>350,'selectedid'=>$this->kclass->input['shipmethodid']));
		$department=$this->kclass->chooserDepartment(array('name'=>'departmentid','departmentid'=>0,'hasBlank'=>1,'width'=>350));
		
		// sampleno
		$start=strtotime(date('Y-m-d',TIMENOW));
		$end=strtotime(date('Y-m-d',$start))+86400;
		$counter=$this->kclass->DB->queryFirst("SELECT COUNT(*) AS count FROM psample WHERE killed=0 AND created>='".$start."' AND created<='".$end."'");
		$sampleno=$this->kclass->id(array('psample'=>TIMENOW,'number'=>$counter['count']));
		//item
		for($i=1; $i < 7; $i++){
			$itemtr .= '<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').'">
				<td>'.$i.'</td>
				<td><input type="text" name="itemMaterialno['.$i.']" size="6"></td>
				<td><input type="text" name="itemMaterial['.$i.']" size="55"></td>
				<td><input type="text" name="itemStandard['.$i.']" size="50"></td>
				<td>'.$this->_chooserType(array('name'=>'itemTypeid['.$i.']','width'=>'150','hasBlank'=>1)).'</td>
				<td><input type="text" name="itemQuantity['.$i.']" size="5"></td>
				<td>'.$this->kclass->chooserUnit(array('name'=>'itemUnitid['.$i.']','hasBlank'=>1,'width'=>120,'selectedid'=>$this->kclass->input['unitid'])).'</td>
				<td><input type="text" name="itemPrice['.$i.']" size="7"></td>
				<td><input type="text" name="itemRemark['.$i.']" size="40"></td>
			</tr>';
		}
		$fee=$this->kclass->formFee(array('module'=>'sample'));
		$discount=$this->kclass->formDiscount(array('module'=>'sample'));
		$shipmentDate=date('Y-m-d',TIMENOW);
		$upload=$this->kclass->upload(array('title'=>'相关附件：'));
$body=<<<EOF
<form action="/s.php?module=psample&action=insert" name="psample" method="post">
<input type="hidden" name="module" value="psample">
<input type="hidden" name="action" value="insert">
<table class="hundred">
<thead>
<tr>
	<th colspan="4">新建样品单：<span class="small red">(由采购部填写)</span></th>
</tr>
</thead>
<tbody>
<tr class="odd">
	<td>样品编号：<span class="red bold">*</span></td>
	<td><span class="red middle bold">{$sampleno}</span>　<span class="small gray">此为预估编号</span></td>
	<td>样品类型：<span class="red bold">*</span></td>
	<td>{$sampletype}</td>
</tr>
<tr class="even">
	<td>供 应 商：<span class="red bold">*</span></td>
	<td>{$supplier}</td>
	<td>客　　户：</td>
	<td>{$customer}</td>
</tr>
<tr class="odd">
	<td>联系人员：</td>
	<td><input type="text" style="width:350px" name="linkman" ></td>
	<td>递送方式：<span class="red bold">*</span></td>
	<td>{$shipmethod}</td>
</tr>
<tr class="even">
	<td>递送日期：<span class="red bold">*</span></td>
	<td><input type="text" id="shipmentDate" name="shipmentDate" value="{$shipmentDate}" size="10">　　　申请人：<input type="text" name="applicant" size="10" ></td>
	<td>需求部门：<span class="red bold">*</span></td>
	<td>{$department}</td>
</tr>
<tr class="odd">
	<td valign="top">详细描述：</td>
	<td><textarea name="detail" style="width:350px;height:70px"></textarea></td>
	<td valign="top">备　　注：</td>
	<td><textarea name="remark" style="width:350px;height:70px"></textarea></td>
</tr>
</tbody>
</table>
<table class="hundred">
<thead>
<tr>
	<th colspan="9">样品 明细表</th>
</tr>
</thead>
<tbody>
<tr><td width="15">ID</td><td>编号<span class="red bold">*</span></td><td>名称<span class="red bold">*</span></td><td>规格</td><td>分类<span class="red bold">*</span></td><td>数量<span class="red bold">*</span></td><td>单位<span class="red bold">*</span></td><td>单价<span class="red bold">*</span></td><td>备注</td></tr>
{$itemtr}
<tr>
	<td colspan="9" class="gray small">注：①如行数不够，请保存后再进入修改页面。②至少有“名称”、“数量”、“单位”三个项目，该明细才会被保存。③如果没有费用，请填写“0”。</td>
</tr>
</tbody>
</table>
{$fee}
{$discount}
{$upload}
<table class="hundred">
<tbody>
<tr class="odd">
	<td colspan="4" align="center">
		<input type="submit" value="  保存  " accesskey="s">　　
		<input type="reset" value="  复位  ">
	</td>
</tr>
<tr class="even">
	<td class="small gray">
	注意事项：<ul>
	<li>英文地址：请严格按英文的格式进行(会影响订单中打印的地址)，基本与中文地址相反的顺序。</li>
	<li>未有列项的内容，写入“备注”中，长度上限3万中文字。</li>
	</ul>
	</td>
</tr>
</tbody>
</table>
</form>
EOF;
		$this->kclass->tbline+=4;
		$this->kclass->page['title'] .= ' - 新建样品';
		$this->kclass->page['onload'] .= 'dc.menu(\'rightNav\');dc.tabhover();$(\'#shipmentDate\').datepicker()';
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 新建样品','right' => '<ul id="rightNav">'.$this->right . '<li></li><li><a href="/s.php?module=psample">返回列表</a></li></ul>','body'=>$body));
	}
	//
	function insert(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e='<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			if(count($this->kclass->input['typeid']) == 0){
				$e .= '<li>请填写样品单的 样品类型。</li>';
			}
			if(!$this->kclass->input['shipmethodid'] > 0){
				$e .= '<li>请选择样品单的所属 递送方式。</li>';
			}
			if($this->kclass->input['shipmentDate']==''){
				$e .= '<li>请选择样品单的所属 递送日期。</li>';
			}
			if(!$this->kclass->input['departmentid']>0){
				$e .= '<li>请选择样品单的 需求部门。</li>';
			}
			$count=count($this->kclass->input['itemMaterial']);
			$hasItem=0;
			for($i=1;$i<=$count;$i++){
				if($this->kclass->input['itemMaterial'][$i]!='' AND $this->kclass->input['itemTypeid'][$i]>0 AND $this->kclass->input['itemQuantity'][$i]>0 AND $this->kclass->input['itemUnitid'][$i]>0 AND $this->kclass->input['itemPrice'][$i]>=0){
					$hasItem=1;
				}
			}
			if($hasItem==0){
				$e.='<li>请至少填写一条明细</li>';
			}
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '新建样品单',
				'text' => '您在新建样品单的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		// sampleno
		$start=strtotime(date('Y-m-d',TIMENOW));
		$end=strtotime(date('Y-m-d',$start))+86400;
		$counter=$this->kclass->DB->queryFirst("SELECT COUNT(*) AS count FROM psample WHERE killed=0 AND created>='".$start."' AND created<='".$end."'");
		$sampleno=$this->kclass->id(array('psample'=>TIMENOW,'number'=>$counter['count']));
		// time
		$shipmentDate=0;
		if($this->kclass->input['shipmentDate']!=''){
			$dd=explode('-',$this->kclass->input['shipmentDate']);
			$shipmentDate=mktime(0,0,0,$dd[1],$dd[2],$dd[0]);
		}
		if($this->kclass->input['supplierid']>0){
			$supplier=$this->kclass->DB->queryFirst("SELECT linkman FROM supplier WHERE supplierid={$this->kclass->input['supplierid']}");
			$linkman=$supplier['linkman'];
		}else{
			$linkman=$this->kclass->input['linkman'];
		}
		$typeid=implode(',',$this->kclass->input['typeid']);
		$this->kclass->DB->query("
			INSERT INTO `psample` (`sampleno`,`typeid`,`customerid`,`supplierid`,`supplierTitle`,`applicant`,`linkman`,`shipmethodid`,`shipmentDate`,`departmentid`,`detail`,`remark`,`creator`,`created`) 
			VALUES ('{$sampleno}','{$typeid}','".$this->kclass->input['customerid']."','".$this->kclass->input['supplierid']."','".$this->kclass->input['supplier']."','{$this->kclass->input['applicant']}','".$linkman."','".$this->kclass->input['shipmethodid']."','".$shipmentDate."','".$this->kclass->input['departmentid']."','".$this->kclass->input['detail']."','".$this->kclass->input['remark']."','".$this->kclass->user['userid']."','".TIMENOW."')
		");
		$psampleid=$this->kclass->DB->insertID();

		// item
		for($i=1;$i<=$count;$i++){
			if($this->kclass->input['itemMaterial'][$i]!='' AND $this->kclass->input['itemTypeid'][$i]>0 AND $this->kclass->input['itemQuantity'][$i]>0 AND $this->kclass->input['itemUnitid'][$i]>0 AND $this->kclass->input['itemPrice'][$i]>=0){
				$this->kclass->input['itemQuantity'][$i]=floatval($this->kclass->input['itemQuantity'][$i]);
				$this->kclass->input['itemPrice'][$i]=floatVal($this->kclass->input['itemPrice'][$i]);
				$amount=number_format($this->kclass->input['itemQuantity'][$i] * $this->kclass->input['itemPrice'][$i],2,'.','');
				$this->kclass->DB->query("
					INSERT INTO `psampleitem` (`materialno`,`material`,`sampleid`,`typeid`,`standard`,`quantity`,`unitid`,`price`,`amount`,`remark`,`creator`,`created`)
					VALUES ('".$this->kclass->input['itemMaterialno'][$i]."','".$this->kclass->input['itemMaterial'][$i]."',{$psampleid},'{$this->kclass->input['itemTypeid'][$i]}','".$this->kclass->input['itemStandard'][$i]."','".$this->kclass->input['itemQuantity'][$i]."','".$this->kclass->input['itemUnitid'][$i]."','".$this->kclass->input['itemPrice'][$i]."','".$amount."','".$this->kclass->input['itemRemark'][$i]."','".$this->kclass->user['userid']."','".TIMENOW."')
				");
			}
		}
		// fee & discount
		$this->kclass->updateFee(array('module'=>'psample','mid'=>$psampleid));
		$this->kclass->updateDiscount(array('module'=>'psample','mid'=>$psampleid));
		$this->kclass->updateAttachs(array('module'=>'psample','mid'=>$psampleid));
		$this->kclass->messager(array(
			'title' => '新建样品单',
			'text' => '样品单 <b>'.$sampleno.'</b> 已新建成功!',
			'url' => '/s.php?module=psample&action=view&psampleid='.$psampleid,
			'sec' => 2
		));
	}
	//
	function update(){
		if($this->kclass->input['psampleid']<=0){
			$this->kclass->boinkIt('/s.php?module=psample');
		}
		$sample=$this->kclass->DB->queryFirst("
			SELECT s.psampleid,s.sampleno,s.typeid,s.customerid,s.supplierid,s.supplierTitle,s.applicant,s.linkman,s.shipmethodid,s.shipmentDate,s.departmentid,s.detail,s.remark,
				c.title AS customer,su.title AS supplier
			FROM `psample` AS s
			LEFT JOIN customer AS c ON (c.customerid=s.customerid)
			LEFT JOIN supplier AS su ON (su.supplierid=s.supplierid)
			WHERE s.killed=0 AND s.`psampleid`='".$this->kclass->input['psampleid']."'
		");
		if(!$sample){
			$this->kclass->boinkIt('/s.php?module=psample');
		}
		$shipmentDate=date('Y-m-d',$sample['shipmentDate']);
		$this->kclass->br2nl=1;
		$sample['detail']=$this->kclass->parseConvertValue($sample['detail']);
		$sample['remark']=$this->kclass->parseConvertValue($sample['remark']);
		$sampletype=$this->kclass->chooserSampleType(array('name'=>'typeid','width'=>350,'module'=>'produce','selectedid'=>$sample['typeid']));
		$supplier=$this->kclass->chooserSupplier(array('hName'=>'supplierid','hId'=>'supplierid','name'=>'supplier','value'=>$this->kclass->iif($sample['supplierid']>0,$sample['supplier'],$sample['supplierTitle']),'id'=>'supplier','width'=>350,'selectedid'=>$sample['supplierid']));
		$shipmethod=$this->kclass->chooserShipmethod(array('name'=>'shipmethodid','hasBlank'=>1,'width'=>350,'selectedid'=>$sample['shipmethodid']));
		$department=$this->kclass->chooserDepartment(array('name'=>'departmentid','hasBlank'=>1,'width'=>350,'selectedid'=>$sample['departmentid']));

		// sample items 
		$items=$this->kclass->DB->query("
			SELECT itemid,materialno,material,standard,typeid,quantity,unitid,price,amount,remark
			FROM psampleitem
			WHERE killed=0 AND sampleid={$sample['psampleid']}
		");
		$k=1;
		if($this->kclass->DB->numRows()){
			while($item=$this->kclass->DB->fetchArray($items)){
				$itemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').'">
					<td><input type="hidden" name="itemId['.$k.']" value="'.$item['itemid'].'" >'.$k.'</td>
					<td><input type="text" name="itemMaterialno['.$k.']" size="6" value="'.$item['materialno'].'"></td>
					<td><input type="text" name="itemMaterial['.$k.']" size="50" value="'.$item['material'].'"></td>
					<td><input type="text" name="itemStandard['.$k.']" size="45" value="'.$item['standard'].'"></td>
					<td>'.$this->_chooserType(array('name'=>'itemTypeid['.$k.']','width'=>'150','hasBlank'=>1,'selectedid'=>$item['typeid'])).'</td>
					<td><input type="text" name="itemQuantity['.$k.']" size="5" value="'.$item['quantity'].'"></td>
					<td>'.$this->kclass->chooserUnit(array('name'=>'itemUnitid['.$k.']','hasBlank'=>1,'width'=>120,'selectedid'=>$item['unitid'])).'</td>
					<td><input type="text" name="itemPrice['.$k.']" size="7" value="'.$item['price'].'"></td>
					<td><input type="text" name="itemRemark['.$k.']" size="40" value="'.$item['remark'].'"></td>
					<td><input type="checkbox" name="itemKill['.$k.']" size="40" value="'.$item['itemid'].'"></td>
				</tr>';
				$k++;
			}
			$itemtr.='<tr><td colspan=10>新增明细</td></tr>';
		}
		//item
		for($i=$k; $i<6+$k; $i++){
			$itemtr .= '<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').'">
				<td>'.$i.'</td>
				<td><input type="text" name="itemMaterialno['.$i.']" size="6"></td>
				<td><input type="text" name="itemMaterial['.$i.']" size="50"></td>
				<td><input type="text" name="itemStandard['.$i.']" size="45"></td>
				<td>'.$this->_chooserType(array('name'=>'itemTypeid['.$i.']','width'=>'150','hasBlank'=>1)).'</td>
				<td><input type="text" name="itemQuantity['.$i.']" size="5"></td>
				<td>'.$this->kclass->chooserUnit(array('name'=>'itemUnitid['.$i.']','hasBlank'=>1,'width'=>120,'selectedid'=>$this->kclass->input['unitid'])).'</td>
				<td><input type="text" name="itemPrice['.$i.']" size="7"></td>
				<td><input type="text" name="itemRemark['.$i.']" size="40"></td>
				<td></td>
			</tr>';
		}
		$fee=$this->kclass->formFee(array('module'=>'psample','mid'=>$sample['psampleid']));
		$discount=$this->kclass->formDiscount(array('module'=>'psample','mid'=>$sample['psampleid']));
		$upload=$this->kclass->upload(array('title'=>'相关附件：','module'=>'psample','mid'=>$sample['psampleid']));
$body=<<<EOF
<form action="/s.php?module=psample&action=doupdate" name="psample" method="post">
<input type="hidden" name="module" value="psample">
<input type="hidden" name="action" value="doupdate">
<input type="hidden" name="psampleid" value="{$this->kclass->input['psampleid']}">
<table class="hundred">
<thead>
<tr>
	<th colspan="4">新建样品单：<span class="small red">(由采购部填写)</span></th>
</tr>
</th80ead>
<tbody>
<tr class="odd">
	<td>样品编号：<span class="red bold">*</span></td>
	<td><span class="red middle bold">{$sample['sampleno']}</span>　<span class="small gray">此为预估编号</span></td>
	<td>样品类型：<span class="red bold">*</span></td>
	<td>{$sampletype}</td>
</tr>
<tr class="even">
	<td>供 应 商：<span class="red bold">*</span></td>
	<td>{$supplier}</td>
	<td>客　　户：</td>
	<td>{$sample['customer']}</td>
</tr>
<tr class="odd">
	<td>联系人员：</td>
	<td><input type="text" style="width:350px" name="linkman" value="{$sample['linkman']}"></td>
	<td>递送方式：</td>
	<td>{$shipmethod}</td>
</tr>
<tr class="even">
	<td>递送日期：<span class="red bold">*</span></td>
	<td><input type="text" size="10" id="deliveryDate" name="shipmentDate" value="{$shipmentDate}">　申请人：<input type="text" name="applicant" size="10" value="{$sample['applicant']}"></td>
	<td>需求部门：<span class="red bold">*</span></td>
	<td>{$department}</td>
</tr>
<tr class="odd">
	<td valign="top">详细描述：</td>
	<td><textarea name="detail" style="width:350px;height:70px">{$sample['detail']}</textarea></td>
	<td valign="top">备　　注：</td>
	<td><textarea name="remark" style="width:350px;height:70px">{$sample['remark']}</textarea></td>
</tr>
</tbody>
</table>
<table class="hundred">
<thead>
<tr>
	<th colspan="10">样品 明细表</th>
</tr>
</thead>
<tbody>
<tr class="center"><td width="15">ID</td><td>编号<span class="red bold">*</span></td><td>名称<span class="red bold">*</span></td><td>规格</td><td>分类</td><td>数量<span class="red bold">*</span></td><td>单位<span class="red bold">*</span></td><td>单价<span class="red bold">*</span></td><td>备注</td><td>删</td></tr>
{$itemtr}
<tr>
	<td colspan="10" class="gray small">注：①如行数不够，请保存后再进入修改页面。②至少有“名称”、“数量”、“单位”三个项目，该明细才会被保存。③如果没有费用，请填写“0”。</td>
</tr>
</tbody>
</table>
{$fee}
{$discount}
{$upload}
<table class="hundred">
<tbody>
<tr class="odd">
	<td colspan="4" align="center">
		<input type="submit" value="  保存  " accesskey="s">　　
		<input type="reset" value="  复位  ">
	</td>
</tr>
<tr class="even">
	<td class="small gray">
	注意事项：<ul>
	<li>英文地址：请严格按英文的格式进行(会影响订单中打印的地址)，基本与中文地址相反的顺序。</li>
	<li>未有列项的内容，写入“备注”中，长度上限3万中文字。</li>
	</ul>
	</td>
</tr>
</tbody>
</table>
</form>
EOF;
		$this->kclass->page['title'] .= ' - 修改 - '.$sample['title'];
		$this->kclass->page['onload'] .= 'dc.tabhover();$(\'#deliveryDate\').datepicker()';
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title' => $this->baseurl .'<a href="/s.php?module=psample&action=view&psampleid='.$sample['psampleid'].'">'.$sample['sampleno'].'</a>'.' - 修改样品 - '.$sample['title'],'right' => '<a href="/s.php?module=psample">返回列表</a>','body'=>$body));
	}
	//
	function doupdate(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e='<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			if(count($this->kclass->input['typeid']) == 0){
				$e .= '<li>请填写样品单的 样品类型。</li>';
			}
			if(!$this->kclass->input['shipmethodid'] > 0){
				$e .= '<li>请选择样品单的所属 递送方式。</li>';
			}
			if($this->kclass->input['shipmentDate']==''){
				$e .= '<li>请选择样品单的所属 递送日期。</li>';
			}
			if(!$this->kclass->input['departmentid']>0){
				$e .= '<li>请选择样品单的 需求部门。</li>';
			}
			$count=count($this->kclass->input['itemMaterial']);
			$hasItem=0;
			for($i=1;$i<=$count;$i++){
				if($this->kclass->input['itemKill'][$i]=='' AND $this->kclass->input['itemMaterial'][$i]!='' AND $this->kclass->input['itemTypeid'][$i]>0 AND $this->kclass->input['itemQuantity'][$i]>0 AND $this->kclass->input['itemUnitid'][$i]>0 AND $this->kclass->input['itemPrice'][$i]>=0){
					$hasItem=1;
				}
			}
			if($hasItem==0){
				$e.='<li>请至少填写一条明细</li>';
			}
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '修改样品单',
				'text' => '您在修改样品单的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$sample=$this->kclass->DB->queryFirst("SELECT psampleid,sampleno FROM psample WHERE psampleid={$this->kclass->input['psampleid']}");
		// item
		for($i=1;$i<=$count;$i++){
			if($this->kclass->input['itemMaterial'][$i]!='' AND $this->kclass->input['itemTypeid'][$i]>0 AND $this->kclass->input['itemQuantity'][$i]>0 AND $this->kclass->input['itemUnitid'][$i]>0 AND $this->kclass->input['itemPrice'][$i]>=0){
				$this->kclass->input['itemQuantity'][$i]=floatval($this->kclass->input['itemQuantity'][$i]);
				$this->kclass->input['itemPrice'][$i]=floatVal($this->kclass->input['itemPrice'][$i]);
				$amount=number_format($this->kclass->input['itemQuantity'][$i] * $this->kclass->input['itemPrice'][$i],2,'.','');
				if($this->kclass->input['itemId'][$i]>0){
					if($this->kclass->input['itemKill'][$i]>0 AND $this->kclass->input['itemKill'][$i]==$this->kclass->input['itemId'][$i]){
						$this->kclass->DB->query("UPDATE psampleitem SET killed='".TIMENOW."',killer='".$this->kclass->user['userid']."' WHERE itemid={$this->kclass->input['itemId'][$i]}");
					}else{
						$this->kclass->DB->query("
							UPDATE psampleitem SET 
								`materialno`='{$this->kclass->input['itemMaterialno'][$i]}',
								`material`='{$this->kclass->input['itemMaterial'][$i]}',
								`typeid`='{$this->kclass->input['itemTypeid'][$i]}',
								`standard`='{$this->kclass->input['itemStandard'][$i]}',
								`quantity`='{$this->kclass->input['itemQuantity'][$i]}',
								`unitid`='{$this->kclass->input['itemUnitid'][$i]}',
								`price`='{$this->kclass->input['itemPrice'][$i]}',
								`amount`='{$amount}',
								`remark`='{$this->kclass->input['itemRemark'][$i]}',
								`modified`='".TIMENOW."',
								`modifier`='{$this->kclass->user['userid']}'
							WHERE itemid={$this->kclass->input['itemId'][$i]}
						");
					}
				}else{
					$this->kclass->DB->query("
						INSERT INTO `psampleitem` (`materialno`,`material`,`sampleid`,`typeid`,`standard`,`quantity`,`unitid`,`price`,`amount`,`remark`,`creator`,`created`)
						VALUES ('{$this->kclass->input['itemMaterialno'][$i]}','".$this->kclass->input['itemMaterial'][$i]."',{$sample['psampleid']},'{$this->kclass->input['itemTypeid'][$i]}','".$this->kclass->input['itemStandard'][$i]."','".$this->kclass->input['itemQuantity'][$i]."','".$this->kclass->input['itemUnitid'][$i]."','".$this->kclass->input['itemPrice'][$i]."','".$amount."','".$this->kclass->input['itemRemark'][$i]."','".$this->kclass->user['userid']."','".TIMENOW."')
					");
				}
			}
		}
		// update sample
		$shipmentDate=0;
		if($this->kclass->input['shipmentDate']!=''){
			$dd=explode('-',$this->kclass->input['shipmentDate']);
			$shipmentDate=mktime(0,0,0,$dd[1],$dd[2],$dd[0]);
		}
		if($this->kclass->input['supplierid']>0){
			$supplier=$this->kclass->DB->queryFirst("SELECT linkman FROM supplier WHERE supplierid={$this->kclass->input['supplierid']}");
			$linkman=$supplier['linkman'];
		}else{
			$linkman=$this->kclass->input['linkman'];
		}
		$typeid=implode(',',$this->kclass->input['typeid']);
		$this->kclass->DB->query("
			UPDATE `psample` SET 
				`typeid`='".$typeid."',
				`supplierid`='".$this->kclass->input['supplierid']."',
				`supplierTitle`='".$this->kclass->input['supplier']."',
				`applicant`='".$this->kclass->input['applicant']."',
				`linkman`='".$linkman."',
				`shipmethodid`='".$this->kclass->input['shipmethodid']."',
				`shipmentDate`='".$shipmentDate."',
				`departmentid`='".$this->kclass->input['departmentid']."',
				`detail`='".$this->kclass->input['detail']."',
				`remark`='".$this->kclass->input['remark']."',
				`modified`='".TIMENOW."',
				`modifier`='".$this->kclass->user['userid']."' 
			WHERE psampleid='".$sample['psampleid']."'
		");
		// fee & discount
		$this->kclass->updateFee(array('module'=>'psample','mid'=>$sample['psampleid']));
		$this->kclass->updateDiscount(array('module'=>'psample','mid'=>$sample['psampleid']));
		$this->kclass->updateAttachs(array('module'=>'psample','mid'=>$sample['psampleid']));
		$this->kclass->messager(array(
			'title' => '修改样品单',
			'text' => '样品单 <b>'.$sample['sampleno'].'</b> 已修改成功!',
			'url' => '/s.php?module=psample&action=view&psampleid='.$sample['psampleid'],
			'sec' => 2
		));
	}
	//
	function kill(){
		if($this->kclass->input['psampleid']<=0){
			$this->kclass->boinkIt('/s.php?module=psample');
		}
		if($this->kclass->input['confirm'] == 0){
			$this->kclass->boinkIt('/s.php?module=psample');
		}
		if($this->kclass->input['psampleid'] < 0){
			$e='<li>请从正确的页面进行操作，如果此问题持续存在，请联系网站管理员。';
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '删除样品',
				'text' => '您在删除样品的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		if($this->kclass->input['rt']=='view'){
			$rt='&action=view&psampleid='.$this->kclass->input['psampleid'];
		}elseif($this->kclass->input['rt']=='list'){
			$rt='#'.$this->kclass->input['psampleid'];
		}
		$sample=$this->kclass->DB->queryFirst("
			SELECT sampleno
			FROM psample
			WHERE psampleid='".$this->kclass->input['psampleid']."'
		");
		if($sample){
			$this->kclass->DB->query("
				UPDATE `psample`
				SET killed=".TIMENOW.",killer=".$this->kclass->user['userid']."
				WHERE psampleid='".$this->kclass->input['psampleid']."'
			");
			$this->kclass->messager(array(
				'title' => '删除样品成功',
				'text' => '样品 <b>'.$sample['sampleno'].'</b> 已成功被标记为删除!',
				'url' => '/s.php?module=psample'.$rt,
				'sec' => 2
			));
		}else{
			$this->kclass->messager(array(
				'title' => '删除样品失败',
				'text' => '您要删除的样品，不存在！',
				'url' => '/s.php?module=psample'.$rt,
				'sec' => 3
			));
		}
	}

	//
	function remove(){
		if($this->kclass->input['psampleid']<=0){
			$this->kclass->boinkIt('/s.php?module=psample');
		}
		$sample=$this->kclass->DB->queryFirst("
			SELECT sampleno
			FROM psample
			WHERE psampleid='".$this->kclass->input['psampleid']."'
		");
$body=<<<EOF
<form action="/s.php?module=psample&action=kill" name="psample" method="post">
<input type="hidden" name="module" value="psample">
<input type="hidden" name="action" value="kill">
<input type="hidden" name="psampleid" value="{$this->kclass->input['psampleid']}">
<table>
<thead>
<tr>
	<th>删除样品：{$sample['sampleno']}</th>
</tr>
</thead>
<tbody>
<tr class="even">
	<td class="middle">你确定要删除样品: <a href="/s.php?module=psample&action=view&psampleid={$this->kclass->input['psampleid']}" class="big bold" target="_blank">{$sample['sampleno']}</a> 吗?</td>
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
		$this->kclass->page['title'] .= ' - 删除 - '.$sample['sampleno'];
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title' => $this->baseurl . ' - <a href="/s.php?module=psample&action=view&psampleid='.$sample['psampleid'].'">'.$sample['sampleno'].'</a> - 删除样品','right' => '<a href="/s.php?module=psample">返回列表</a>','body'=>$body));
	}

	//
	function revival(){
		if($this->kclass->input['psampleid']<=0){
			$this->kclass->boinkIt('/s.php?module=psample');
		}
		if($this->kclass->input['confirm'] == 0){
			$this->kclass->boinkIt('/s.php?module=psample');
		}
		if($this->kclass->input['psampleid'] < 0){
			$e='<li>请从正确的页面进行操作，如果此问题持续存在，请联系网站管理员。';
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '恢复样品',
				'text' => '您在恢复样品的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$sample=$this->kclass->DB->queryFirst("
			SELECT sampleno
			FROM psample
			WHERE psampleid='".$this->kclass->input['psampleid']."'
		");
		if($this->kclass->input['rt']=='view'){
			$rt='&action=view&psampleid='.$this->kclass->input['psampleid'];
		}elseif($this->kclass->input['rt']=='list'){
			$rt='#'.$this->kclass->input['psampleid'];
		}
		if($sample){
			$this->kclass->DB->query("
				UPDATE `psample`
				SET killed=0,killer=0
				WHERE psampleid='".$this->kclass->input['psampleid']."'
			");
			$this->kclass->messager(array(
				'title' => '恢复样品成功',
				'text' => '样品 <b>'.$sample['sampleno'].'</b> 已成功被恢复！',
				'url' => '/s.php?module=psample'.$rt,
				'sec' => 3
			));
		}else{
			$this->kclass->messager(array(
				'title' => '恢复样品失败',
				'text' => '您要恢复的样品不存在！',
				'url' => '/s.php?module=psample'.$rt,
				'sec' => 3
			));
		}
	}

	//
	function restore(){
		if($this->kclass->input['psampleid']<=0){
			$this->kclass->boinkIt('/s.php?module=psample');
		}
		$sample=$this->kclass->DB->queryFirst("
			SELECT sampleno
			FROM psample
			WHERE psampleid='".$this->kclass->input['psampleid']."'
		");
$body=<<<EOF
<form action="/s.php?module=psample&action=revival" name="psample" method="post">
<input type="hidden" name="module" value="psample">
<input type="hidden" name="action" value="revival">
<input type="hidden" name="psampleid" value="{$this->kclass->input['psampleid']}">
<table>
<thead>
<tr>
	<th>恢复样品：{$sample['title']}</th>
</tr>
</thead>
<tbody>
<tr class="even">
	<td class="middle">你确定要恢复样品: <a href="/s.php?module=psample&action=view&psampleid={$this->kclass->input['psampleid']}" class="big bold" target="_blank">{$sample['sampleno']}</a> 吗?</td>
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
		$this->kclass->page['title'] .= ' - 恢复 - '.$sample['sampleno'];
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title' => $this->baseurl . ' - <a href="/s.php?module=psample&action=view&psampleid='.$sample['psampleid'].'">'.$sample['sampleno'].'</a> - 恢复样品','right' => '<a href="/s.php?module=psample">返回列表</a>','body'=>$body));
	}
	function upload(){
		if($this->kclass->input['psampleid']<=0){
			$this->kclass->boinkIt('/s.php?module=psample');
		}
		$sample=$this->kclass->DB->queryFirst("
			SELECT psampleid,sampleno
			FROM `psample`
			WHERE `psampleid`='".$this->kclass->input['psampleid']."'
		");
		if(!$sample){
			$this->kclass->boinkIt('/s.php?module=psample');
		}
		$upload=$this->kclass->upload(array('title'=>'相关附件：'));
		$body=<<<EOF
<div class="title">{$sample['sampleno']}</div>
<form action="/s.php?module=psample&action=doupload" name="psample" method="post">
<input type="hidden" name="module" value="psample">
<input type="hidden" name="action" value="doupload">
<input type="hidden" name="psampleid" value="{$sample['psampleid']}">
{$upload}
<table class="hundred">
<tbody>
<tr class="odd">
	<td colspan="4" align="center">
		<input type="submit" value="  保存  " accesskey="s">　　
		<input type="reset" value="  复位  ">
	</td>
</tr>
</tbody>
</table>
</form>
EOF;
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 上传样品附件','right' => $this->right . '<a href="/s.php?module=psample">返回列表</a>','body'=>$body));
	}
	function doupload(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e='<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '修改样品单',
				'text' => '您在修改样品单的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$this->kclass->updateAttachs(array('module'=>'psample','mid'=>$this->kclass->input['psampleid']));
		$this->kclass->messager(array(
			'title' => '上传样品附件',
			'text' => '附件上传成功！',
			'url' => '/s.php?module=psample&action=view&psampleid='.$this->kclass->input['psampleid'],
			'sec' => 3
		));
	}
	function validateItem(){
		if(!$this->kclass->input['psampleid']){
			$e.='<li>请从正确的链接进入</li>';
		}
		$sample=$this->kclass->DB->queryFirst("SELECT psampleid,sampleno FROM psample WHERE psampleid={$this->kclass->input['psampleid']}");
		if(!$sample){
			$e.='<li>数据错误，很抱歉!</li>';
		}
		if($e){
			$this->kclass->messager(array(
				'title' => '验收样品',
				'text' => '您在验收样品的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$items=$this->kclass->DB->query("
			SELECT psi.itemid,psi.materialno,psi.material,psi.standard,psi.quantity,psi.amount,psi.ifValidate,psi.validater,psi.validateResult,psi.remark,
			u.title AS unit
			FROM psampleitem AS psi
			LEFT JOIN unit AS u ON (u.unitid=psi.unitid)
			WHERE psi.killed=0 AND psi.sampleid={$sample['psampleid']}
		");
		if($this->kclass->DB->numRows()){
			$i=1;
			while($item=$this->kclass->DB->fetchArray($items)){
				$checked1=$checked2='';
				if($item['ifValidate']==1){
					$checked1=' checked';
					$checked2=' ';
				}elseif($item['ifValidate']==-1){
					$checked2=' checked';
					$checked1=' ';
				}

				$itemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').'">
					<td><input type="hidden" name="itemId['.$i.']" value="'.$item['itemid'].'">'.$i.'</td>
					<td>'.$item['materialno'].'</td>
					<td>'.$item['material'].'</td>
					<td>'.$item['standard'].'</td>
					<td align="right">'.$item['quantity'].$item['unit'].'</td>
					<td align="right">'.$item['amount'].'</td>
					<td>'.$item['remark'].'</td>
					<td><input type="radio" name="ifValidate['.$i.']" value=1 '.$checked1.' >是 <input type="radio" name="ifValidate['.$i.']" value="-1" '.$checked2.'>否 </td>
					<td><input type="text" name="validater['.$i.']" size="7" value="'.$this->kclass->iif($item['validater'],$item['validater'],$this->kclass->user['user']).'"></td>
					<td><input type="text" name="validateResult['.$i.']" size="40" value="'.$item['validateResult'].'"></td>
					<td><input type="checkbox" name="itemSelect['.$i.']" value="'.$item['itemid'].'"></td>
				</tr>';
				$i++;
			}
		}
		$body=<<<EOF
<form action="/s.php?module=psample&action=dovalidateItem" name="psample" method="post">
<input type="hidden" name="module" value="psample">
<input type="hidden" name="action" value="dovalidateItem">
<input type="hidden" name="psampleid" value="{$this->kclass->input['psampleid']}">
<div class="title">{$sample['sampleno']}</div>
<table class="hundred">
<thead>
<tr>
	<th colspan="11">样品 明细表</th>
</tr>
</thead>
<tbody>
<tr><td width="15">ID</td><td width="50">编号</td><td>名称</td><td>规格</td><td width="80">数量</td><td width="80">单价</td><td>备注</td><td width="80">是否合格 <span class="bold red">*</span></td><td width="80">验收人 <span class="bold red">*</span></td><td width="290">验收结果 <span class="bold red">*</span></td><td width="15">选</td></tr>
{$itemtr}
<tr class="odd">
	<td colspan="11" align="center">
		<input type="submit" value="  保存  " accesskey="s">　　
		<input type="reset" value="  复位  ">
	</td>
</tr>
</tbody>
</table>
</form>
EOF;
		$this->kclass->tbline+=4;
		$this->kclass->page['title'] .= ' - 新建样品';
		$this->kclass->page['onload'] .= 'dc.menu(\'rightNav\');dc.tabhover();$(\'#shipmentDate\').datepicker()';
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 新建样品','right' => '<ul id="rightNav">'.$this->right . '<li></li><li><a href="/s.php?module=psample">返回列表</a></li></ul>','body'=>$body));
	}
	function dovalidateItem(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e='<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			$sample=$this->kclass->DB->queryFirst("SELECT psampleid,sampleno FROM psample WHERE psampleid={$this->kclass->input['psampleid']}");
			if(!$sample){
				$e.='<li>数据错误，很抱歉</li>';
			}
			$count=count($this->kclass->input['itemId']);
			$hasItem=0;
			for($i=1;$i<=$count;$i++){
				if($this->kclass->input['itemSelect'][$i]>0 AND $this->kclass->input['validater'][$i]!='' AND $this->kclass->input['validateResult'][$i]!=''){
					$hasItem=1;
				}
			}
			if($hasItem==0){
				$e.='<li>请至少填写一条明细</li>';
			}
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '修改样品单',
				'text' => '您在修改样品单的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		for($i=1;$i<=$count;$i++){
			if($this->kclass->input['itemSelect'][$i]>0 AND $this->kclass->input['validater'][$i]!='' AND $this->kclass->input['validateResult'][$i]!=''){
				$this->kclass->DB->query("UPDATE psampleitem SET ifValidate={$this->kclass->input['ifValidate'][$i]},validater='{$this->kclass->input['validater'][$i]}',validated='".TIMENOW."',validateResult='{$this->kclass->input['validateResult'][$i]}' WHERE itemid={$this->kclass->input['itemId'][$i]}");
			}
		}
		$this->kclass->messager(array(
			'title' => '验收样品单',
			'text' => '样品单 <b>'.$sample['sampleno'].'</b> 已成功验收!',
			'url' => '/s.php?module=psample&action=view&psampleid='.$sample['psampleid'],
			'sec' => 2
		));
	}
	function listItem(){
		$id=array();
		if($this->kclass->input['ifValidate']!=''){
			$c='';
			if($this->kclass->input['typeid']>0){
				$c=' AND pli.typeid='.$this->kclass->input['typeid'];
			}
			$condition=' AND pli.ifValidate='.$this->kclass->input['ifValidate'].$c;
		}elseif($this->kclass->input['typeid']>0){
			$c='';
			if($this->kclass->input['ifValidate']!=''){
				$c=' AND pli.ifValidate='.$this->kclass->input['ifValidate'];
			}
			$condition=' AND pli.typeid='.$this->kclass->input['typeid'].$c;
		}elseif($this->kclass->input['supplier']!=''){
			$supplier1=$this->kclass->DB->queryFirst("SELECT supplierid FROM supplier WHERE title LIKE '%{$this->kclass->input['supplier']}%'");
			if($supplier1['supplierid']){
				$condition=' AND ps.supplierid='.$supplier1['supplierid'];
			}else{
				$condition=" AND ps.supplierTitle LIKE '%{$this->kclass->input['supplier']}%'";
			}
		}elseif($this->kclass->input['materialno']!=''){
			$nos=explode(';',$this->kclass->input['materialno']);
			if(is_array($nos) AND count($nos)>1){
				foreach($nos AS $no){
					$find=$this->kclass->DB->queryFirst("SELECT itemid FROM psampleitem WHERE materialno='{$no}'");
					if(!$find['itemid']){
						$id[]=0;
					}else{
						$id[]=$find['itemid'];
					}
				}
				$condition.=' AND pli.itemid IN ('.implode(',',$id).')';
			}elseif(count($nos)==1){
				if($this->kclass->input['type']==1){
					$finds=$this->kclass->DB->query("SELECT itemid FROM psampleitem WHERE materialno LIKE '%{$this->kclass->input['materialno']}%'");
					if($this->kclass->DB->numRows()){
						while($find=$this->kclass->DB->fetchArray($finds)){
							if(!$find['itemid']){
								$id[]=0;
							}else{
								$id[]=$find['itemid'];
							}
						}
					}
					$condition.=' AND pli.itemid IN ('.implode(',',$id).')';
				}elseif($this->kclass->input['type']==0){
					$condition.=' AND pli.materialno="'.$this->kclass->input['materialno'].'"';
				}
			}
		}elseif($this->kclass->input['material']!=''){
			$condition=" AND pli.material LIKE '%{$this->kclass->input['material']}%'";
		}
		
		
		$items=$this->kclass->DB->query("
			SELECT pli.itemid,pli.materialno,pli.material,pli.standard,pli.typeid,pli.quantity,pli.price,pli.amount,pli.remark,pli.ifValidate,pli.validater,pli.validated,pli.validateResult,u.title AS unit,ps.psampleid,ps.sampleno,ps.supplierid,ps.supplierTitle
			FROM psampleitem AS pli
			LEFT JOIN unit AS u ON (u.unitid=pli.unitid)
			LEFT JOIN psample AS ps ON (ps.psampleid=pli.sampleid)
			WHERE pli.killed=0 AND ps.killed=0 {$condition}
			ORDER BY pli.created ASC
		");
		if($this->kclass->DB->numRows()){
			$i=1;
			while($item=$this->kclass->DB->fetchArray($items)){
				$type='';
				foreach($this->type AS $t){
					if($t['id']==$item['typeid']){
						$type=$t['title'];
					}
				}
				if($item['supplierid']>0){
					$supplier=$this->kclass->DB->queryFirst("SELECT supplierid,title FROM supplier WHERE supplierid={$item['supplierid']}");
				}
				if($item['ifValidate']==1){
					$ifValidate='合格';
				}elseif($item['ifValidate']==-1){
					$ifValidate='不合格';
				}
				
				$itemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').' small">
					<td>'.$i.'</td>
					<td><a href="/s.php?module=psample&action=view&psampleid='.$item['psampleid'].'">'.$item['sampleno'].'</a></td>
					<td>'.$this->kclass->iif($item['supplierid']>0,'<a href="/s.php?module=supplier&action=view&supplierid='.$item['supplierid'].'">'.$supplier['title'].'</a>',$item['supplierTitle']).'</td>	
					<td>'.$item['materialno'].'</td>
					<td>'.$item['material'].'</td>
					<td>'.$item['standard'].'</td>
					<td>'.$type.'</td>
					<td>'.$item['quantity'].$item['unit'].'</td>
					<td>'.$item['price'].'</td>
					<td>'.$item['amount'].'</td>
					<td>'.$item['remark'].'</td>
					<td>'.$ifValidate.'</td>
					<td>'.$item['validater'].'</td>
					<td>'.date('Y-m-d H:i:s',$item['validated']).'</td>
					<td>'.$item['validateResult'].'</td>
				</tr>';
				$i++;
			}
		}
		
		foreach($this->type AS $type){
			$typeList.='<a href="/s.php?module=psample&action=listItem&ifValidate='.$this->kclass->input['ifValidate'].'&typeid='.$type['id'].'">'.$type['title'].'</a>　';
		}
		$body=<<<EOF
<form action="/s.php?module=psample&action=listItem" method="get">
<input type="hidden" name="module" value="psample" >
<input type="hidden" name="action" value="listItem" >
<table class="hundred">
<thead><tr><th colspan=3>查询【<a href="/s.php?module=psample&action=listItem">全部</a>】</th></tr></thead>
<tbody>
<tr><td>是否合格：</td><td colspan=2><a href="/s.php?module=psample&action=listItem&ifValidate=1&typeid={$this->kclass->input['typeid']}">合格</a>　<a href="/s.php?module=psample&action=listItem&ifValidate=0&typeid={$this->kclass->input['typeid']}">不合格</a></td></tr>
<tr><td>分　　类：</td><td colspan=2>{$typeList}</td></tr>
<tr><td>供 应 商：</td><td><input type="text" name="supplier" value="{$this->kclass->input['supplier']}" size="150"></td><td><input type="submit" value="查询" ></td></tr>
<tr><td>编　　号：</td><td><input type="text" name="materialno" value="{$this->kclass->input['materialno']}" size="150"><input type="radio" name="type" value=0 >精确查找 <input type="radio" name="type" value=1 checked>模糊查找</td></td><td><input type="submit" value="查询" ></td></tr>
<tr><td>名　　称：</td><td><input type="text" name="material" value="{$this->kclass->input['material']}" size="150"></td></td><td><input type="submit" value="查询" ></td></tr>
</tbody>
</table>
</form>
<table class="hundred">
<thead><tr><th colspan=15>样品明细列表</th></tr></thead>			
<tbody>
<tr><td width="15">ID</td><td width="80">样品编号</td><td>供应商</td><td width="40">编号</td><td>样品名称</td><td>规格</td><td>分类</td><td width="40">数量</td><td width="60">单价</td><td width="60">总价</td><td>备注</td><td width="60">是否合格</td><td width="60">验收人</td><td width="150">验收时间</td><td width="200">验收结果</td></tr>
{$itemtr}
</tbody>
</table>
EOF;
		$this->kclass->tbline+=4;
		$this->kclass->page['title'] .= ' - 样品明细列表';
		$this->kclass->page['onload'] .= 'dc.menu(\'rightNav\');dc.tabhover();$(\'#shipmentDate\').datepicker()';
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 样品明细列表','right' => $this->right . '<a href="/s.php?module=psample">返回样品单列表</a>','body'=>$body));
	}

	//
	function updateEvent(){
		if($this->kclass->input['eventid']<=0 OR !$event=$this->kclass->DB->queryFirst("SELECT `event`.*,sample.title FROM `sampleevent` AS `event` LEFT JOIN `sample` ON (`sample`.psampleid=`event`.psampleid) WHERE `eventid`='".$this->kclass->input['eventid']."'")){
			$this->kclass->boinkIt('/s.php?module=psample');
		}
		$deliveryDate=date('Y-m-d',$event['deliveryDate']);
		$shipmethod=$this->kclass->chooserShipmethod(array('name'=>'shipmethodid','hasBlank'=>1,'width'=>350,'selectedid'=>$event['shipmethodid']));
		$sampleCurrency=$this->kclass->chooserCurrency(array('name'=>'sampleCurrency','hasBlank'=>1,'width'=>100,'selectedid'=>$event['sampleCurrency']));
		$deliveryCurrency=$this->kclass->chooserCurrency(array('name'=>'deliveryCurrency','hasBlank'=>1,'width'=>100,'selectedid'=>$event['deliveryCurrency']));
		$t1=$d1=' checked';
		if($event['direction']=='sent'){
			$d2=$d1;
			$d1='';
		}
		if($event['totalQuantity']=='part'){
			$t2=$t1;
			$t1='';
		}
		$deliveryDate=date('Y-m-d',$event['deliveryDate']);
$body=<<<EOF
<form action="/s.php?module=psample&action=doupdateEvent" name="psample" method="post">
<input type="hidden" name="module" value="psample">
<input type="hidden" name="action" value="doupdateEvent">
<input type="hidden" name="psampleid" value="{$event['psampleid']}">
<input type="hidden" name="eventid" value="{$event['eventid']}">
<table class="hundred">
<thead>
<tr>
	<th colspan="8">新建样品收发与费用：</th>
</tr>
</thead>
<tbody>
<tr class="odd">
	<td>收发类型：<span class="red bold">*</span></td>
	<td>
		<label for="direction1"><input type="radio" id="direction1" name="direction" value="received"{$d1}>收到</label> 
		<label for="direction2"><input type="radio" id="direction2" name="direction" value="sent"{$d2}>发出</label> 
	</td>
	<td>收发数量：<span class="red bold">*</span></td>
	<td>
		<label for="totalQuantity1"><input type="radio" id="totalQuantity1" name="totalQuantity" value="all"{$t1}>样品单全部数量</label> 
		<label for="totalQuantity2"><input type="radio" id="totalQuantity2" name="totalQuantity" value="part"{$t2}>部分<span class="gray small">(请在备注中注明)</span></label> 
	</td>
</tr>
<tr class="even">
	<td>收发日期：<span class="red bold">*</span></td>
	<td><input type="text" style="width:350px" id="deliveryDate" name="deliveryDate" value="{$deliveryDate}"></td>
	<td>递送方式：<span class="red bold">*</span></td>
	<td>{$shipmethod}</td>
</tr>
<tr class="odd">
	<td>递送费用：<span class="red bold">*</span></td>
	<td>{$deliveryCurrency} <input type="text" style="width:100px" name="deliveryFee" value="{$event['deliveryFee']}"></td>
	<td>运费说明：</td>
	<td><input type="text" style="width:350px" name="deliveryCaption" value="{$event['deliveryCaption']}"></td>
</tr>
<tr class="even">
	<td>样品费用：<span class="red bold">*</span></td>
	<td>{$sampleCurrency} <input type="text" style="width:100px" name="sampleFee" value="{$event['sampleFee']}"></td>
	<td>样品费说明：</td>
	<td><input type="text" style="width:350px" name="sampleCaption" value="{$event['sampleCaption']}"></td>
</tr>
<tr class="odd">
	<td>备　　注：</td>
	<td colspan="3"><textarea name="remark" style="width:350px;height:70px">{$event['remark']}</textarea></td>
</tr>
<tr class="even">
	<td colspan="8" align="center">
		<input type="submit" value="  保存  " accesskey="s">　　
		<input type="reset" value="  复位  ">
	</td>
</tr>
<tr class="odd">
	<td colspan="8" class="small gray">注意：①如果没有费用，请填写“0”。②日期如手动填写，格式为“YYYY-mm-dd”。</td>
</tr>
</tbody>
</table>
</form>
EOF;
		$this->kclass->page['onload'] .= 'dc.tabhover();$(\'#deliveryDate\').datepicker()';
		$this->kclass->page['title'] .= ' - '.$event['title'].' - 修改样品单收发与费用';
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title' => $this->baseurl . ' - <a href="/s.php?module=psample&action=view&psampleid='.$event['psampleid'].'">'.$event['title'].'</a> - 新建样品单收发与费用','right' => '<a href="/s.php?module=psample">返回列表</a>','body'=>$body));
	}
	//
	function doupdateEvent(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e='<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			if($this->kclass->input['direction']==''){
				$e='<li>请选择样品收发的类型。</li>';
			}
			if($this->kclass->input['deliveryDate'] == ''){
				$e .= '<li>请填写样品收到或发出的日期。</li>';
			}
			if($this->kclass->input['shipmethodid'] == 0){
				$e .= '<li>请选择样品递送的方式。</li>';
			}
			if($this->kclass->input['deliveryFee'] == ''){
				$e .= '<li>请填写样品递送的费用，如无费用，请填写“0”。</li>';
			}
			if($this->kclass->input['sampleFee'] == ''){
				$e .= '<li>请填写样品费用，如无费用，请填写“0”。</li>';
			}
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '修改样品单收发与费用',
				'text' => '您在修改样品单收发与费用的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$deliveryDate=0;
		if($this->kclass->input['deliveryDate']!=''){
			$dd=explode('-',$this->kclass->input['deliveryDate']);
			$deliveryDate=mktime(0,0,0,$dd[1],$dd[2],$dd[0]);
		}
		$this->kclass->DB->query("
			UPDATE `sampleevent` SET 
				`direction`='".$this->kclass->input['direction']."',
				`totalQuantity`='".$this->kclass->input['totalQuantity']."',
				`deliveryDate`='".$deliveryDate."',
				`deliveryCurrency`='".$this->kclass->input['deliveryCurrency']."',
				`deliveryFee`='".$this->kclass->input['deliveryFee']."',
				`deliveryCaption`='".$this->kclass->input['deliveryCaption']."',
				`shipmethodid`='".$this->kclass->input['shipmethodid']."',
				`sampleCurrency`='".$this->kclass->input['sampleCurrency']."',
				`sampleFee`='".$this->kclass->input['sampleFee']."',
				`sampleCaption`='".$this->kclass->input['sampleCaption']."',
				`remark`='".$this->kclass->input['remark']."',
				`modified`='".TIMENOW."',
				`modifier`='".$this->kclass->user['userid']."'
			WHERE eventid='".$this->kclass->input['eventid']."'
		");

		$this->kclass->messager(array(
			'title' => '修改样品单收发与费用',
			'text' => '修改样品单收发与费用成功!',
			'url' => '/s.php?module=psample&action=view&psampleid='.$this->kclass->input['psampleid'],
			'sec' => 2
		));
	}
	//
	function addEvent(){
		if($this->kclass->input['psampleid']<=0 OR !$sample=$this->kclass->DB->queryFirst("SELECT `sample`.* FROM `sample` WHERE `psampleid`='".$this->kclass->input['psampleid']."'")){
			$this->kclass->boinkIt('/s.php?module=psample');
		}
		$deliveryDate=date('Y-m-d',TIMENOW);
		$shipmethod=$this->kclass->chooserShipmethod(array('name'=>'shipmethodid','hasBlank'=>1,'width'=>350,'selectedid'=>5));
		$sampleCurrency=$this->kclass->chooserCurrency(array('name'=>'sampleCurrency','hasBlank'=>1,'width'=>100,'selectedid'=>3));
		$deliveryCurrency=$this->kclass->chooserCurrency(array('name'=>'deliveryCurrency','hasBlank'=>1,'width'=>100,'selectedid'=>3));
$body=<<<EOF
<form action="/s.php?module=psample&action=insertEvent" name="psample" method="post">
<input type="hidden" name="module" value="psample">
<input type="hidden" name="action" value="insertEvent">
<input type="hidden" name="psampleid" value="{$sample['psampleid']}">
<table class="hundred">
<thead>
<tr>
	<th colspan="8">新建样品收发与费用： <span class="small red">(由采购部填写)</span></th>
</tr>
</thead>
<tbody>
<tr class="odd">
	<td>收发类型：<span class="red bold">*</span></td>
	<td>
		<label for="direction1"><input type="radio" id="direction1" name="direction" value="received">收到</label> 
		<label for="direction2"><input type="radio" id="direction2" name="direction" value="sent">发出</label> 
	</td>
	<td>收发数量：<span class="red bold">*</span></td>
	<td>
		<label for="totalQuantity1"><input type="radio" id="totalQuantity1" name="totalQuantity" value="all">样品单全部数量</label> 
		<label for="totalQuantity2"><input type="radio" id="totalQuantity2" name="totalQuantity" value="part">部分<span class="gray small">(请在备注中注明)</span></label> 
	</td>
</tr>
<tr class="even">
	<td>收发日期：<span class="red bold">*</span></td>
	<td><input type="text" style="width:350px" id="deliveryDate" name="deliveryDate" value="{$deliveryDate}"></td>
	<td>递送方式：<span class="red bold">*</span></td>
	<td>{$shipmethod}</td>
</tr>
<tr class="odd">
	<td>递送费用：<span class="red bold">*</span></td>
	<td>{$deliveryCurrency} <input type="text" style="width:100px" name="deliveryFee" value="{$event['deliveryFee']}"></td>
	<td>运费说明：</td>
	<td><input type="text" style="width:350px" name="deliveryCaption" value="{$event['deliverCaption']}"></td>
</tr>
<tr class="even">
	<td>样品费用：<span class="red bold">*</span></td>
	<td>{$sampleCurrency} <input type="text" style="width:100px" name="sampleFee" value="{$event['sampleFee']}"></td>
	<td>样品费说明：</td>
	<td><input type="text" style="width:350px" name="sampleCaption" value="{$event['sampleCaption']}"></td>
</tr>
<tr class="odd">
	<td>备　　注：</td>
	<td colspan="3"><textarea name="remark" style="width:350px;height:70px">{$event['remark']}</textarea></td>
</tr>
<tr class="even">
	<td colspan="8" align="center">
		<input type="submit" value="  保存  " accesskey="s">　　
		<input type="reset" value="  复位  ">
	</td>
</tr>
<tr class="odd">
	<td colspan="8" class="small gray">注意：①如果没有费用，请填写“0”。②日期如手动填写，格式为“YYYY-mm-dd”。</td>
</tr>
</tbody>
</table>
</form>
EOF;
		$this->kclass->page['onload'] .= 'dc.tabhover();$(\'#deliveryDate\').datepicker()';
		$this->kclass->page['title'] .= ' - '.$sample['title'].' - 新建样品单收发与费用';
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title' => $this->baseurl . ' - <a href="/s.php?module=psample&action=view&psampleid='.$sample['psampleid'].'">'.$sample['title'].'</a> - 新建样品单收发与费用','right' => '<a href="/s.php?module=psample">返回列表</a>','body'=>$body));
	}
	//
	function insertEvent(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e='<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			if($this->kclass->input['direction']==''){
				$e='<li>请选择样品收发的类型。</li>';
			}
			if($this->kclass->input['deliveryDate'] == ''){
				$e .= '<li>请填写样品收到或发出的日期。</li>';
			}
			if($this->kclass->input['shipmethodid'] == 0){
				$e .= '<li>请选择样品递送的方式。</li>';
			}
			if($this->kclass->input['deliveryFee'] == ''){
				$e .= '<li>请填写样品递送的费用，如无费用，请填写“0”。</li>';
			}
			if($this->kclass->input['sampleFee'] == ''){
				$e .= '<li>请填写样品费用，如无费用，请填写“0”。</li>';
			}
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '新建样品单收发与费用',
				'text' => '您在新建样品单收发与费用的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$deliveryDate=0;
		if($this->kclass->input['deliveryDate']!=''){
			$dd=explode('-',$this->kclass->input['deliveryDate']);
			$deliveryDate=mktime(0,0,0,$dd[1],$dd[2],$dd[0]);
		}
		$this->kclass->DB->query("
			INSERT INTO `sampleevent` (`psampleid`,`direction`,`totalQuantity`,`deliveryDate`,`deliveryCurrency`,`deliveryFee`,`deliveryCaption`,`shipmethodid`,`sampleCurrency`,`sampleFee`,`sampleCaption`,`remark`,`modified`,`creator`,`created`) VALUES ('".$this->kclass->input['psampleid']."','".$this->kclass->input['direction']."','".$this->kclass->input['totalQuantity']."','".$deliveryDate."','".$this->kclass->input['deliveryCurrency']."','".$this->kclass->input['deliveryFee']."','".$this->kclass->input['deliveryCaption']."','".$this->kclass->input['shipmethodid']."','".$this->kclass->input['sampleCurrency']."','".$this->kclass->input['sampleFee']."','".$this->kclass->input['sampleCaption']."','".$this->kclass->input['remark']."','".TIMENOW."','".$this->kclass->user['userid']."','".TIMENOW."')
		");

		$this->kclass->messager(array(
			'title' => '新建样品单收发与费用',
			'text' => '新建样品单收发与费用成功!',
			'url' => '/s.php?module=psample&action=view&psampleid='.$this->kclass->input['psampleid'],
			'sec' => 3
		));
	}
	//
	function killEvent(){
		if($this->kclass->input['psampleid']<=0 OR $this->kclass->input['eventid']<=0){
			$this->kclass->boinkIt('/s.php?module=psample');
		}
		if($this->kclass->input['confirm'] == 0){
			$this->kclass->boinkIt('/s.php?module=psample&action=view&psampleid='.$this->kclass->input['psampleid']);
		}
		if($this->kclass->input['psampleid'] < 0 OR $this->kclass->input['eventid'] < 0){
			$e='<li>请从正确的页面进行操作，如果此问题持续存在，请联系网站管理员。';
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '删除样品单中的收发与费用记录',
				'text' => '您在删除样品单中的收发与费用记录的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$sample=$this->kclass->DB->queryFirst("
			SELECT title
			FROM sample
			WHERE psampleid='".$this->kclass->input['psampleid']."'
		");
		if($sample){
			$this->kclass->DB->query("
				UPDATE `sampleevent`
				SET killed=".TIMENOW.",killer='".$this->kclass->user['userid']."' 
				WHERE eventid='".$this->kclass->input['eventid']."'
			");
			$this->kclass->messager(array(
				'title' => '删除样品单中的收发与费用记录成功',
				'text' => '样品 <b>'.$sample['title'].'</b> 下的相关 收发与费用记录 已成功被标记为删除!',
				'url' => '/s.php?module=psample&action=view&psampleid='.$this->kclass->input['psampleid'],
				'sec' => 2
			));
		}else{
			$this->kclass->messager(array(
				'title' => '删除样品单中的收发与费用记录失败',
				'text' => '您要删除的样品或收发与费用记录，不存在！',
				'url' => '/s.php?module=psample&action=view&psampleid='.$this->kclass->input['psampleid'],
				'sec' => 3
			));
		}
	}

	//
	function removeEvent(){
		if($this->kclass->input['eventid']<=0){
			$this->kclass->boinkIt('/s.php?module=psample');
		}
		$event=$this->kclass->DB->queryFirst("
			SELECT `event`.eventid,`event`.psampleid,
				`sample`.title
			FROM `sampleevent` AS `event` 
			LEFT JOIN `sample` ON (`sample`.psampleid=`event`.psampleid) 
			WHERE `event`.eventid='".$this->kclass->input['eventid']."'
		");
		if($event){
$body=<<<EOF
<form action="/s.php?module=psample&action=killEvent" name="psample" method="post">
<input type="hidden" name="module" value="psample">
<input type="hidden" name="action" value="killEvent">
<input type="hidden" name="psampleid" value="{$event['psampleid']}">
<input type="hidden" name="eventid" value="{$event['eventid']}">
<table>
<thead>
<tr>
	<th>删除样品：{$sample['title']}</th>
</tr>
</thead>
<tbody>
<tr class="even">
	<td class="middle">你确定要删除样品单 <a href="/s.php?module=psample&action=view&psampleid={$event['psampleid']}" class="big" target="_blank">{$event['title']}</a> 下的 <span class="big bold darkred">收发与费用</span> 记录吗?</td>
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
		}else{
			$body='您要删除的样品单收发与费用记录并不存在，请返回列表刷新后再进行操作。（如果问题持续出现，请联系管理员。）';
		}
		$this->kclass->page['title'] .= ' - '.$event['title'].' - 删除收发与费用记录';
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title' => $this->baseurl . ' - <a href="/s.php?module=psample&action=view&sampleid='.$event['sampleid'].'">'.$event['title'].'</a> - 删除收发与费用记录','right' => '<a href="/s.php?module=psample">返回列表</a>','body'=>$body));
	}
	//
	function revivalEvent(){
		if($this->kclass->input['sampleid']<=0 OR $this->kclass->input['eventid']<=0){
			$this->kclass->boinkIt('/s.php?module=psample');
		}
		if($this->kclass->input['confirm'] == 0){
			$this->kclass->boinkIt('/s.php?module=psample&action=view&sampleid='.$this->kclass->input['sampleid']);
		}
		if($this->kclass->input['sampleid'] < 0 OR $this->kclass->input['eventid'] < 0){
			$e='<li>请从正确的页面进行操作，如果此问题持续存在，请联系网站管理员。';
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '恢复样品单中的收发与费用记录',
				'text' => '您在恢复样品单中的收发与费用记录的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$sample=$this->kclass->DB->queryFirst("
			SELECT title
			FROM sample
			WHERE sampleid='".$this->kclass->input['sampleid']."'
		");
		if($sample){
			$this->kclass->DB->query("
				UPDATE `sampleevent`
				SET killed=".TIMENOW.",killer=0 
				WHERE eventid='".$this->kclass->input['eventid']."'
			");
			$this->kclass->messager(array(
				'title' => '恢复样品单中的收发与费用记录成功',
				'text' => '样品 <b>'.$sample['title'].'</b> 下的相关 收发与费用记录 已成功被标记为恢复!',
				'url' => '/s.php?module=psample&action=view&sampleid='.$this->kclass->input['sampleid'],
				'sec' => 2
			));
		}else{
			$this->kclass->messager(array(
				'title' => '恢复样品单中的收发与费用记录失败',
				'text' => '您要恢复的样品或收发与费用记录，不存在！',
				'url' => '/s.php?module=psample&action=view&sampleid='.$this->kclass->input['sampleid'],
				'sec' => 3
			));
		}
	}

	//
	function restoreEvent(){
		if($this->kclass->input['eventid']<=0){
			$this->kclass->boinkIt('/s.php?module=psample');
		}
		$event=$this->kclass->DB->queryFirst("
			SELECT `event`.eventid,`event`.sampleid,
				`sample`.title
			FROM `sampleevent` AS `event` 
			LEFT JOIN `sample` ON (`sample`.sampleid=`event`.sampleid) 
			WHERE `event`.eventid='".$this->kclass->input['eventid']."'
		");
		if($event){
$body=<<<EOF
<form action="/s.php?module=psample&action=revivalEvent" name="psample" method="post">
<input type="hidden" name="module" value="psample">
<input type="hidden" name="action" value="revivalEvent">
<input type="hidden" name="sampleid" value="{$event['sampleid']}">
<input type="hidden" name="eventid" value="{$event['eventid']}">
<table>
<thead>
<tr>
	<th>恢复样品：{$sample['title']}</th>
</tr>
</thead>
<tbody>
<tr class="even">
	<td class="middle">你确定要恢复样品: <a href="/s.php?module=psample&action=view&sampleid={$event['sampleid']}" class="big bold" target="_blank">{$event['title']}</a> 吗?</td>
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
		}else{
			$body='您要删除的样品单收发与费用记录并不存在，请返回列表刷新后再进行操作。（如果问题持续出现，请联系管理员。）';
		}
		$this->kclass->page['title'] .= ' - '.$event['title'].' - 恢复收发与费用记录';
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title' => $this->baseurl . ' - <a href="/s.php?module=psample&action=view&sampleid='.$event['sampleid'].'">'.$event['title'].'</a> - 恢复收发与费用记录','right' => '<a href="/s.php?module=psample">返回列表</a>','body'=>$body));
	}
	/******************************************* private function ****************************************************/
		// 
	function _cells(){
		$this->kclass->input['page']=intval($this->kclass->input['page']);
		if($this->kclass->input['page'] <= 0){
			$this->kclass->input['page']=$this->kclass->myGetcookie($this->kclass->input['module'].'page');
			if($this->kclass->input['page']<=0){
				$this->kclass->input['page']=1;
			}
		}
		if ($perpage == '') {
			$perpage=4;
		}
		$condition="`sample`.killed=0";
		$caption=array();
		if($this->kclass->input['q'] != ''){
			$this->kclass->input['q']=rawurldecode($this->kclass->input['q']);
			$condition .= " AND (INSTR(LCASE(`sample`.sampleno),'".(strtolower($this->kclass->input['q']))."')>0 OR INSTR(LCASE(`sample`.title),'".(strtolower($this->kclass->input['q']))."')>0)";
			$caption[]='<b>样品编号</b>或<b>名称</b>包含<b>'.$this->kclass->input['q'].'</b>';
		}
		if($this->kclass->input['sampleno'] != ''){
			$this->kclass->input['sampleno']=rawurldecode($this->kclass->input['sampleno']);
			$condition .= " AND INSTR(LCASE(sample.sampleno),'".(strtolower($this->kclass->input['sampleno']))."')>0";
			$caption[]='<b>样品编号</b>包含<b>'.$this->kclass->input['sampleno'].'</b>';
		}
		if($this->kclass->input['title'] != ''){
			$this->kclass->input['title']=rawurldecode($this->kclass->input['title']);
			$condition .= " AND INSTR(LCASE(sample.title),'".(strtolower($this->kclass->input['title']))."')>0";
			$caption[]='<b>名称</b>包含<b>'.$this->kclass->input['title'].'</b>';
		}
		if (intval($this->kclass->input['typeid']) > 0) {
			$condition .= " AND sample.typeid='".$this->kclass->input['typeid']."'";
			$type=$this->kclass->DB_site->query_first("
				SELECT title
				FROM `type`
				WHERE typeid='".$this->kclass->input['typeid']."'
			");
			$caption[]='<b>类别</b>是<b>'.$type['title'].'</b>';
		}
		if (intval($this->kclass->input['supplierid']) > 0) {
			$condition .= " AND sample.supplierid='".$this->kclass->input['supplierid']."'";
			$supplier=$this->kclass->DB_site->query_first("
				SELECT title
				FROM `supplier`
				WHERE supplierid='".$this->kclass->input['supplierid']."'
			");
			$caption[]='<b>供应商</b>是<b>'.$supplier['title'].'</b>';
		}
		if (intval($this->kclass->input['customerid']) > 0) {
			$condition .= " AND sample.customerid='".$this->kclass->input['customerid']."'";
			$customer=$this->kclass->DB_site->query_first("
				SELECT title
				FROM `customer`
				WHERE customerid='".$this->kclass->input['customerid']."'
			");
			$caption[]='<b>供应商</b>是<b>'.$customer['title'].'</b>';
		}
		if($this->kclass->input['orderby'] != ''){
			$orderby=$this->kclass->input['orderby'];
		}else{
			$orderby='`sample`.modified';
		}
		if($this->kclass->input['direction'] != ''){
			$direction=$this->kclass->input['direction'];
		}else{
			$direction='DESC';
		}
		$samples=$this->kclass->DB->query("
			SELECT `sample`.psampleid,`sample`.title,`sample`.entitle,`sample`.typeid,`sample`.sampleno,`sample`.attachs,`sample`.images,`sample`.modified,`sample`.created,`sample`.killed,
				attr.title AS attr,
				m.username AS modifier,c.username AS creator
			FROM `sample`
			LEFT JOIN `attr` ON (attr.attrid=sample.attrid) 
			LEFT JOIN `user` AS m ON (m.userid=`sample`.modifier) 
			LEFT JOIN `user` AS c ON (c.userid=`sample`.creator) 
			ORDER BY `sample`.psampleid ASC,`sample`.modified DESC,`sample`.created DESC
			LIMIT ".$this->kclass->pagination(array())."
		");
		


		$counter=$this->kclass->DB_site->query_first("
			SELECT COUNT(productid) AS total
			FROM product
			WHERE $condition
		");
		$page->set($perpage,$counter['total'],$this->kclass->input['page']);
		
	}
	
	function _chooserType($b){
		if($b['width']){
			$width='style="width:'.$b['width'].'px"';
		}
		$r='<select name="'.$b['name'].'" id="'.$this->kclass->iif($b['id'],$b['id'],$b['name']).'" '.$width.'>';
		if($b['hasBlank']){
			$r.='<option value=0>'.$b['topName'].'</option>';
		}
		foreach($this->type AS $type){
			$r.='<option value="'.$type['id'].'"';
			if($type['id']==$b['selectedid']){
				$r.=' selected';
			}
			$r.='>'.'('.$type['abbr'].')'.$type['title'].'</option>';
		}
		$r.='</select>';
		return $r;
	}
}
?>