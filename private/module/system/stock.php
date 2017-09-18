<?php
	//
	class stock{
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
				case 'import':
					return $this->import();
					break;
				case 'doimport':
				return $this->doimport();
				break;
				case 'listOriginal':
				return $this->listOriginal();
				break;
				case 'exchange':
				return $this->exchange();
				break;
				default:
					return $this->mmlist();

			}
		}
		// main page,shows all the inbound info list
		function mmlist(){
			if($this->kclass->input['show']=='all'){
				$show=' 1=1';
			}elseif($this->kclass->input['show']=='stock'){
				$show=' `stock`.categoryid=16';
			}elseif($this->kclass->input['show']=='area'){
				$show=' `stock`.categoryid=17';
			}elseif($this->kclass->input['show']=='refrigerator'){
				$show=' `stock`.categoryid=18';
			}elseif($this->kclass->input['show']=='empty'){
				$show=' `stock`.ifEmpty=0';
			}elseif($this->kclass->input['show']=='unempty'){
				$show=' `stock`.ifEmpty=1';
			}else{
				$show=' `stock`.killed=0';
			}
			if($this->kclass->input['orderby']!='')$query['orderby']=$this->kclass->input['orderby'];
			if($this->kclass->input['direction']!='')$query['direction']=$this->kclass->input['direction'];
			if($this->kclass->input['show']!='')$query['show']=$this->kclass->input['show'];
			if($this->kclass->input['layout']!='')$query['layout']=$this->kclass->input['layout'];
			$orderby=$this->kclass->orderby(array('module'=>'stock','direction'=>'asc','orderby'=>'modified', 'default'=>'stockno', 'serial'=>array( array('title'=>'编号', 'field'=>'stockno','word'=>'no'), array('title'=>'修改时间', 'field'=>'modified'), array('title'=>'建立时间', 'field'=>'created')),'appendUrl'=>$query));
			if(is_array($query) AND count($query)>0)$queryPart='&'.http_build_query($query);
			$stocks=$this->kclass->DB->query("
				SELECT `stock`.*,
					c.title AS category,
					sn1.title AS upName,
					sn2.title AS downName,
					sn3.title AS leftName,
					sn4.title AS rightName,
					sn5.title AS frontName,
					sn6.title AS behindName,
					u.username AS creator,
					us.username AS modifier
				FROM `stock`
				LEFT JOIN `category` AS c ON (c.categoryid=`stock`.categoryid)
				LEFT JOIN `stocknear` AS sn1 ON (sn1.nearid=`stock`.upid)
				LEFT JOIN `stocknear` AS sn2 ON (sn2.nearid=`stock`.downid)
				LEFT JOIN `stocknear` AS sn3 ON (sn3.nearid=`stock`.leftid)
				LEFT JOIN `stocknear` AS sn4 ON (sn4.nearid=`stock`.rightid)
				LEFT JOIN `stocknear` AS sn5 ON (sn5.nearid=`stock`.frontid)
				LEFT JOIN `stocknear` AS sn6 ON (sn6.nearid=`stock`.behindid)
				LEFT JOIN `user` AS u ON (u.userid=`stock`.creator)
				LEFT JOIN `user` AS us ON (us.userid=`stock`.modifier)
				WHERE ".$show."
				ORDER BY ".$orderby['sql']."
			");
			if($this->kclass->DB->numRows()){
				if($this->kclass->input['layout']=='grid'){
					$body='<ul id="mmlist" class="mmlist clear">';
				}else{
					$body='<table class="hundred"><thead><tr colspan=11><th width="15">ID</th><th width="30">状态</th><th width="30">类别</th><th width="80">编号</th><th width="150">上</th><th width="150">下</th><th width="150">左</th><th width="150">右</th><th width="150">前</th><th width="150">后</th><th width="50"></th></tr></thead><tbody>';
				}
				$i=1;
				while($stock=$this->kclass->DB->fetchArray($stocks)){
					$stock['created']=date('Y-m-d',$stock['created']);
					$stock['modified']=date('Y-m-d',$stock['modified']);
					if($stock['ifEmpty']==0){
						$ifEmpty='空闲';
					}elseif($stock['ifEmpty']==1){
						$ifEmpty='被占用';
					}
					$tray=$this->kclass->iif($stock['ifTray']==0,'否','是');
					if($stock['status']=='permanent'){
						$status='永久库位';
					}elseif($stock['status']=='temporary'){
						$status='临时库位';
					}
					if($stock['killed']>0){
						$link='<a href="/s.php?module=stock&action=restore&stockid='.$stock['stockid'].'&rt=list">恢复</a>';
					}else{
						$link='<a href="/s.php?module=stock&action=update&stockid='.$stock['stockid'].'&rt=list">改</a>　<a href="/p.php?action=stock&stockid='.$stock['stockid'].'" target="_blank">印</a>';
					}
					$stock['height']=$this->kclass->iif($stock['height']=='max','——',$stock['height']);
					if($this->kclass->input['layout']=='grid'){//表格
						$body.='<li title="由 '.$stock['creator'].' 建于 '.$stock['created'].$this->kclass->iif($stock['modified']==0, '，'.$stock['modifier'].' 改于 '.$stock['modified'], '').'"'.$this->kclass->iif($i%4==0, ' class="end"', '').'><a name="'.$stock['stockid'].'"></a>
						<div class="mmlistt">
							<span class="right normal">';
						if($stock['killed']>0){
							$body .= '<a href="/s.php?module=stock&action=revival&stockid='.$stock['stockid'].'&rt=list" onclick="return confirm(\'你确定要恢复这个库位 '.$stock['stockno'].' 吗？\');">恢复</a>';
						}else{
							$body .= '<a href="/s.php?module=stock&action=kill&stockid='.$stock['stockid'].'&rt=list" onclick="return confirm(\'你确定要删除这个库位 '.$stock['stockno'].' 吗？\');">删</a> <a href="/s.php?module=stock&action=update&stockid='.$stock['stockid'].'&rt=list">改</a>';
						}
						$body.='</span>
						 <a href="/s.php?module=stock&action=view&stockid='.$stock['stockid'].'">'.$stock['stockno'].'</a><br><span class="small gray right">'. $stock['created'].'</span>'.$this->kclass->iif($stock['attachs']>0, ' <span class="attachFile" title="有'.$stock['attachs'].'个附件。"></span> ', '').$this->kclass->iif($stock['images']>0, ' <span class="attachImage" title="有'.$stock['images'].'个图片。"></span> ', '').$ifEmpty.'</div>
						<div class=mmlistb>
							<div><span class="small gray">库位类别：</span>'.$stock['category'].'</div>
							<div><span class="small gray">库位状态：</span>'.$status.'</div>
							<div><span class="small gray">是否可放托盘：</span>'.$tray.'</div>
							<div class="small clear"><span class=right title="由 '.$stock['creator'].' 建于 '.$stock['created'].'">由 '.$stock['creator'].' 建于 '.$stock['created'].'</span></div></div></li>';
					}else{
						$body .='<tr class="'.$this->kclass->rotateLine().$alarm.' small">
							<td>'.$i.'</td>
							<td>'.$ifEmpty.'</td>
							<td><span class="small">'.$stock['category'].'</span></td>
							<td><a href="/s.php?module=stock&action=view&stockid='.$stock['stockid'].'">'.$stock['stockno'].'</a>'.$this->kclass->iif($stock['attachs']>0, ' <span class="attachFile" title="有'.$stock['attachs'].'个附件。"></span> ', '').$this->kclass->iif($stock['images']>0, ' <span class="attachImage" title="有'.$stock['images'].'个图片。"></span> ', '').'</td>
							<td style="word-break:break-all">'.$this->kclass->iif($stock['upid']==6,$this->_relatedStock($stock['up']),$stock['upName']).'</td>
							<td style="word-break:break-all">'.$this->kclass->iif($stock['downid']==6,$this->_relatedStock($stock['down']),$stock['downName']).'</td>
							<td style="word-break:break-all">'.$this->kclass->iif($stock['leftid']==6,$this->_relatedStock($stock['left']),$stock['leftName']).'</td>
							<td style="word-break:break-all">'.$this->kclass->iif($stock['rightid']==6,$this->_relatedStock($stock['right']),$stock['rightName']).'</td>
							<td style="word-break:break-all">'.$this->kclass->iif($stock['frontid']==6,$this->_relatedStock($stock['front']),$stock['frontName']).'</td>
							<td style="word-break:break-all">'.$this->kclass->iif($stock['behindid']==6,$this->_relatedStock($stock['behind']),$stock['upName']).'</td>
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
				$body='暂无相关记录。';
			}
			if($this->kclass->input['layout']=='grid'){
				$layoutLink='<a href="/s.php?module=stock&action=list&layout=list'.str_replace('&layout=grid', '', $queryPart).'">列表</a> 格子';
			}else{
				$layoutLink='列表 <a href="/s.php?module=stock&action=list&layout=grid'.str_replace('&layout=list', '', $queryPart).'">格子</a>';
			}
			if($this->kclass->input['show']=='all'){
				$showLink='<a href="/s.php?module=stock&action=list'.str_replace('&show=all', '', $queryPart).'">默认</a> 所有<span class="small gray">(包括删除)</span> <a href="/s.php?module=stock&action=list&show=empty'.str_replace('&show=all', '', $queryPart).'">空闲</a> <a href="/s.php?module=stock&action=list&show=unempty'.str_replace('&show=all', '', $queryPart).'">非空闲</a>';
			}elseif($this->kclass->input['show']=='empty'){
				$showLink='<a href="/s.php?module=stock&action=list'.str_replace('&show=empty', '', $queryPart).'">默认</a> <a href="/s.php?module=stock&action=list&show=all'.str_replace('&show=empty', '', $queryPart).'">所有<span class="small gray">(包括删除)</span></a> 空闲 <a href="/s.php?module=stock&action=list&show=unempty'.str_replace('&show=empty', '', $queryPart).'">非空闲</a>';
			}elseif($this->kclass->input['show']=='unempty'){
				$showLink='<a href="/s.php?module=stock&action=list'.str_replace('&show=all', '', $queryPart).'">默认</a> <a href="/s.php?module=stock&action=list&show=all'.str_replace('&show=unempty', '', $queryPart).'">所有<span class="small gray">(包括删除)</span></a> <a href="/s.php?module=stock&action=list&show=empty'.str_replace('&show=unempty', '', $queryPart).'">空闲</a> 非空闲';
			}else{
				$showLink='默认 <a href="/s.php?module=stock&action=list&show=all'.str_replace('&show=all', '', $queryPart).'">所有<span class="small gray">(包括删除)</span></a> <a href="/s.php?module=stock&action=list&show=empty'.str_replace('&show=all', '', $queryPart).'">空闲</a> <a href="/s.php?module=stock&action=list&show=unempty'.str_replace('&show=all', '', $queryPart).'">非空闲</a>';
			}
			$this->kclass->page['onload']='dc.tabhover();';
			$this->kclass->page['title']='库位列表';
			$this->kclass->page['main']=$this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 列表', 'right' => '<span class="small">排序：</span>'.$orderby['link'].'|　<span class="small">显示方式：</span>'.$showLink.'　|　<span class="small">布局：</span>'.$layoutLink.'　|　<a href="/s.php?module=stock&action=add">新建</a>　|　<a href="/s.php?module=stock&action=import">批量导入</a>', 'body'=>$body));
		}
		// main page,shows all the inbound info list
		function view(){
			$stock=$this->kclass->DB->queryFirst("
				SELECT `stock`.*,
					su.title AS stockUp,
					sd.title AS stockDown,
					sl.title AS stockLeft,
					sr.title AS stockRight,
					sf.title AS stockFront,
					sb.title AS stockBehind,
					`category`.title AS category,
					u.username AS creator,
					us.username AS modifier
				FROM `stock`
				LEFT JOIN `category` ON (`category`.categoryid=`stock`.categoryid)
				LEFT JOIN `stocknear` AS su ON (su.nearid=`stock`.upid)
				LEFT JOIN `stocknear` AS sd ON (sd.nearid=`stock`.downid)
				LEFT JOIN `stocknear` AS sl ON (sl.nearid=`stock`.leftid)
				LEFT JOIN `stocknear` AS sr ON (sr.nearid=`stock`.rightid)
				LEFT JOIN `stocknear` AS sf ON (sf.nearid=`stock`.frontid)
				LEFT JOIN `stocknear` AS sb ON (sb.nearid=`stock`.behindid)
				LEFT JOIN `user` AS u ON (u.userid=`stock`.creator)
				LEFT JOIN `user` AS us ON (us.userid=`stock`.modifier)
				WHERE `stock`.stockid='".$this->kclass->input['stockid']."'
				LIMIT 0,1
			");
			if($stock){
				$stock['created']=date('Y-m-d H:i',$stock['created']);
				if($stock['modified']!=0)$modify='，由'.$stock['modifier'].'于'.date('Y-m-d H:i',$stock['modified']).'修改';
				if($stock['ifEmpty']==0){
					$stock['ifEmpty']='空闲';
				}elseif($stock['ifEmpty']==1){
					$stock['ifEmpty']='被占用';
				}
				$tray=$this->kclass->iif($stock['ifTray']==0,'否','是');
				if($stock['status']=='permanent'){
					$status='永久库位';
				}elseif($stock['status']=='temporary'){
					$status='临时库位';
				}
				$attach=$this->kclass->getAttachs(array('module'=>'stock', 'mid'=>$stock['stockid']));
				if($attach!= false){
					$attachs='<div class="clear">'.$attach.'</div>';
				}
				$up=$this->kclass->iif($stock['up']=='','无',$this->kclass->iif($stock['upid']==6,$this->_relatedStock($stock['up']),$stock['up']));
				$down=$this->kclass->iif($stock['down']=='','无',$this->kclass->iif($stock['downid']==6,$this->_relatedStock($stock['down']),$stock['down']));
				$left=$this->kclass->iif($stock['left']=='','无',$this->kclass->iif($stock['leftid']==6,$this->_relatedStock($stock['left']),$stock['left']));
				$right=$this->kclass->iif($stock['right']=='','无',$this->kclass->iif($stock['rightid']==6,$this->_relatedStock($stock['right']),$stock['right']));
				$front=$this->kclass->iif($stock['front']=='','无',$this->kclass->iif($stock['frontid']==6,$this->_relatedStock($stock['front']),$stock['front']));
				$behind=$this->kclass->iif($stock['behind']=='','无',$this->kclass->iif($stock['behindid']==6,$this->_relatedStock($stock['behind']),$stock['behind']));

				// 库位物资
				$items=$this->kclass->DB->query("
					SELECT si.*,
						m.materialno,m.title,m.standard,m.unitid,
						bt.batchid,bt.batchno,
						br.barcodeid,br.barcode
					FROM stockitem AS si
					LEFT JOIN material AS m ON (m.materialid=si.materialid)
					LEFT JOIN batch AS bt ON (bt.batchid=si.batchid)
					LEFT JOIN barcode AS br ON (br.barcodeid=si.batchid)
					WHERE si.killed=0 AND si.stockid='".$this->kclass->input['stockid']."'
					ORDER BY stockitemid DESC
				");
				if($itemList['num']=$this->kclass->DB->numRows()){
					$itemList['panel']='<table class="hundred"><thead><tr><th colspan="4">库位物资</th></tr></thead><tr class="bold center"><td width="120">物资批号</td><td width="250">条码信息</td><td width="450">物资信息</td><td>数量/单位</td></tr>';
					while($item=$this->kclass->DB->fetchArray($items)){
						$unit=$this->kclass->DB->queryFirst("SELECT title FROM unit WHERE killed=0 AND unitid='".$item['unitid']."'");
						$itemList['panel'].='<tr><td><a href="/s.php?module=batch&action=view&batchid='.$item['batchid'].'">'.$item['batchno'].'</a></td><td><a href="/s.php?module=barcode&action=view&barcodeid='.$item['barcodeid'].'">'.$item['barcode'].'</a></td><td><a href="/s.php?module=material&action=view&materialid='.$item['materialid'].'">'.$item['materialno'].'　'.$item['title'].'　'.$item['standard'].'</a></td><td>'.$item['quantity'].$unit['title'].'</td></tr>';
					}
					$itemList['panel'].='</tbody></table>';
					$itemList['count']='('.$itemList['num'].')';
				}else{
					$itemList['off'].=' disabled';
				}

$body=<<<EOF
<div class="title"><span class="right small gray">由{$stock['creator']}于{$stock['created']}建立{$modify}。</span>{$stock['stockno']}</div>
<dl id="stock" class="tabs" style="display: block;">
	<dt tabid="0">基本信息</dt>
	<dt title="存放与此库位的物资" {$itemList['off']}>库位物资{$itemList['count']}</dt>
<dd>
<table class="hundred">
<thead><tr><th colspan="4">库位信息</th></tr></thead>
<tbody>
<tr class="odd">
	<td width="100">库位编号：</td><td width="475">{$stock['stockno']}</td>
	<td width="100">库位类别：</td><td>{$stock['category']}</td>
</tr>
<tr class="even">
	<td>是否空闲：</td><td>{$stock['ifEmpty']}</td>
	<td>长 * 宽 * 高：</td><td>{$stock['length']}cm * {$stock['width']}cm * {$stock['height']}cm</td>
</tr>
<tr class="odd">
	<td>是否可放托盘：</td><td>{$tray}</td>
	<td>库位状态：</td><td>{$status}</td>
</tr>
<tr class="even">
	<td>周围情况：</td><td colspan=3>上：{$stock['stockUp']}　下：{$stock['stockDown']}　左：{$stock['stockLeft']}　右：{$stock['stockRight']}　前：{$stock['stockFront']}　后：{$stock['stockBehind']}</td>
</tr>
<tr class="even">
	<td>周围情况备注：</td><td colspan=3 style="word-break:break-all">上：{$up}　下：{$down}　左：{$left}　右：{$right}　前：{$front}　后：{$behind}</td>
</tr>
</tbody>
</table>
{$attachs}
</dd>
<dd>{$itemList['panel']}</dd>
EOF;
			}else{
				$body='数据错误，很抱歉！';
			}

			$this->kclass->tbline+=4;
			$this->kclass->page['title'].='查看库位';
			$this->kclass->page['onload'].='dc.tabhover();dc.tabs({\'id\':\'stock\'});';
			$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - <a href="/s.php?module=stock&action=list">库位列表</a> - 查看库位','right'=>'<span class="small">新建：</span><a href="/s.php?module=stock&action=add">库位</a>　|　'.$this->kclass->iif($stock['killed']==0,'<a href="/s.php?module=stock&action=update&stockid='.$stock['stockid'].'">修改</a>　<a href="/s.php?module=stock&action=remove&stockid='.$stock['stockid'].'&rt=view">删除</a></span>','<a href="/s.php?module=stock&action=restore&stockid='.$stock['stockid'].'&rt=view">恢复</a>'),'body'=>$body));
		}

		// a form  for add a stock
		function add(){
			$category=$this->kclass->chooserCategory(array('name'=>'categoryid','width'=>250,'parentid'=>15,'hasBlank'=>1,'topname'=>' '));
			$stocks=$this->kclass->DB->query("SELECT stockid,stockno FROM `stock` WHERE killed=0 ORDER BY stockid ASC");
			if($this->kclass->DB->numRows()){
				$nearStock='<select name="nearStockid[]" style="width:120px"><option value="0"></option>';
				while($stock=$this->kclass->DB->fetchArray($stocks)){
					$nearStock.='<option value="'.$stock['stockid'].'">'.$stock['stockno'].'</option>';
				}
				$nearStock.='</select>';
			}
			$up=$this->_chooserStockNear(array('name'=>'upid','width'=>'120'));
			$down=$this->_chooserStockNear(array('name'=>'downid','width'=>'120'));
			$left=$this->_chooserStockNear(array('name'=>'leftid','width'=>'120'));
			$right=$this->_chooserStockNear(array('name'=>'rightid','width'=>'120'));
			$front=$this->_chooserStockNear(array('name'=>'frontid','width'=>'120'));
			$behind=$this->_chooserStockNear(array('name'=>'behindid','width'=>'120'));
			$upload=$this->kclass->upload(array('title'=>'相关附件：'));
			$body=<<<EOF
<form method="post" action="/s.php?module=stock&action=insert" name="stock">
<input type="hidden" name="module" value="stock" />
<input type="hidden" name="action" value="insert" />
<table class="hundred">
<thead><tr><th colspan="4">新建库位</th></tr></thead>
<tbody>
<tr class="odd">
<td width="120">流 水 号：<span class="red bold">*</span></td><td width="475"><input type="text" name="number"></td>
<td width="80">层　　数：<span class="red bold">*</span></td><td><input type="text" name="floor"></td>
</tr>
<tr class="even">
<td>体　　积：<span class="red bold">*</span></td><td>长：<input type="text" name="length" size="10">cm　宽：<input type="text" name="width" size="10">cm　高：<input type="text" name="height" size="10">cm</td>
<td>类　　别：<span class="red bold">*</span></td><td>{$category}</td>
</tr>
<tr class="odd">
<td>周围情况：<span class="red bold">*</span></td><td colspan=3>上：{$up}　下：{$down}　左：{$left}　右：{$right}　前：{$front}　后：{$behind}</td>
</tr>
<tr class="even">
<td>周围备注：</td><td colspan=3>
上：<input type="text" name="up" style="width:115px" />
　下：<input type="text" name="down" style="width:115px" />
　左：<input type="text" name="left" style="width:115px" />
　右：<input type="text" name="right" style="width:115px" />
　前：<input type="text" name="front" style="width:115px" />
　后：<input type="text" name="behind" style="width:115px" /></td>
</tr>
<tr class="odd">
<td>是否可放托盘：<span class="red bold">*</span></td><td><input type="radio" value="1" name="ifTray" checked />是 <input type="radio" value="0" name="ifTray" />否</td>
<td>库位状态：<span class="red bold">*</span></td><td><input type="radio" value="permanent" name="status" checked />永久 <input type="radio" value="temporary" name="status" />临时</td>
</tr>
<tr class="even">
<td>备注：</td><td><textarea name="remark" style="width:444px;height:111px;"></textarea></td>
<td></td><td></td>
</tr>
<tr>
<td colspan="11" class="red small">
注意事项：<br>
①流水号为对该库位横向排列的序号。<br>
②“类别”为“区域”时，高度只能为0，层数只能为1。<br>
③如果周围情况为“库位”的时候，那么就在“相邻库位”中选择库位，若相邻库位号还未确定，那么等待相邻库位号确定后，才返回修改此库位，选择已确定的相邻库位号。</td>
</tr>
</tbody>
</table>
{$upload}
<table class="tlist tsuite" align="center" width="100%">
<tbody>
<tr class="odd">
<td colspan="4" align="center">
	<input type="submit" id="submitButton" value="  保存  " accesskey="s">
	<input type="reset" value="  复位  ">
</td>
</tr>
</tbody>
</table>
</form>
EOF;
			$this->kclass->page['title'].='新建库位';
			$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.' - <a href="/s.php?module=stock">列表</a> - 新建库位', 'right'=>'<a href="/s.php?module=stock">返回列表</a>','body'=>$body));
		}
		// insert stock
		function insert(){
			if($this->kclass->input['requestMethod'] != 'post'){
				$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
			}else{
				if($this->kclass->input['number']==''){
					$e.='<li>请填写库位的 流水号 </li>';
				}
				if($this->kclass->input['floor']==''){
					$e.='<li>请填写库位的 层数 </li>';
				}
				if($this->kclass->input['length']==''){
					$e.='<li>请填写库位的 长度 </li>';
				}
				if($this->kclass->input['width']==''){
					$e.='<li>请填写库位的 宽度 </li>';
				}
				if($this->kclass->input['height']==''){
					$e.='<li>请填写库位的 高度 </li>';
				}
				if($this->kclass->input['categoryid']==0){
					$e.='<li>请选择库位的 类别 </li>';
				}
			}
			if(isset($e)){
				$this->kclass->messager(array(
					'title' => '新建库位',
					'text' => '您在新建库位的过程中有以下错误：<ul>'.$e.'</ul>',
					'url' => 'javascript:history.back()',
					'sec' => 3
				));
			}
			$stockno=$this->kclass->input['number'].$this->kclass->input['floor'];
			$this->kclass->DB->query("
				INSERT INTO stock
					(`stockno`,`categoryid`,`buildingFloor`,`floor`,`number`,`length`,`width`,`height`,`upid`,`downid`,`leftid`,`rightid`,`frontid`,`behindid`,`up`,`down`,`left`,`right`,`front`,`behind`,`ifTray`,`status`,`remark`,`created`,`creator`)
				VALUES
					('".$stockno."','".$this->kclass->input['categoryid']."','".$this->kclass->input['buildingFloor']."','".$this->kclass->input['floor']."','".$this->kclass->input['number']."','".$this->kclass->input['length']."','".$this->kclass->input['width']."','".$this->kclass->input['height']."','".$this->kclass->input['upid']."','".$this->kclass->input['downid']."','".$this->kclass->input['leftid']."','".$this->kclass->input['rightid']."','".$this->kclass->input['frontid']."','".$this->kclass->input['behindid']."','".$this->kclass->input['up']."','".$this->kclass->input['down']."','".$this->kclass->input['left']."','".$this->kclass->input['right']."','".$this->kclass->input['front']."','".$this->kclass->input['behind']."','".$this->kclass->input['ifTray']."','".$this->kclass->input['status']."','".$this->kclass->input['remark']."','".TIMENOW."','".$this->kclass->user['userid']."')
			");
			$stockid = $this->kclass->DB->insertID();
			$this->kclass->updateAttachs(array('module'=>'stock', 'mid'=>$stockid));
			$this->kclass->messager(array(
				'title' => '新建库位',
				'text' => '库位 <b>'.$stockno.'</b> 已新建成功!',
				'url' => '/s.php?module=stock&action=view&stockid='.$stockid,
				'sec' => 2
			));
		}
		//
		function update(){
			$stock=$this->kclass->DB->queryFirst("SELECT * FROM stock WHERE killed=0 AND stockid='".$this->kclass->input['stockid']."' LIMIT 0,1");
			if(!$stock){
				$e='数据错误，很抱歉！';
			}
			if(isset($e)){
				$this->kclass->messager(array(
					'title' => '新建库位',
					'text' => '您在新建库位的过程中有以下错误：<ul>'.$e.'</ul>',
					'url' => 'javascript:history.back()',
					'sec' => 3
				));
			}
			$category=$this->kclass->chooserCategory(array('name'=>'categoryid','selectedid'=>$stock['categoryid'],'width'=>250,'parentid'=>15));
			$stockUp=$this->_chooserStockNear(array('name'=>'upid','width'=>'120','selectedid'=>$stock['upid']));
			$stockDown=$this->_chooserStockNear(array('name'=>'downid','width'=>'120','selectedid'=>$stock['downid']));
			$stockLeft=$this->_chooserStockNear(array('name'=>'leftid','width'=>'120','selectedid'=>$stock['leftid']));
			$stockRight=$this->_chooserStockNear(array('name'=>'rightid','width'=>'120','selectedid'=>$stock['rightid']));
			$stockFront=$this->_chooserStockNear(array('name'=>'frontid','width'=>'120','selectedid'=>$stock['frontid']));
			$stockBehind=$this->_chooserStockNear(array('name'=>'behindid','width'=>'120','selectedid'=>$stock['behindid']));

			if($stock['ifTray']==1){
				$trayCheck1='checked';
			}elseif($stock['ifTray']==1){
				$trayCheck1='checked';
			}
			if($stock['status']=='permanent'){
				$statusCheck1='checked';
			}elseif($stock['status']=='temporary'){
				$statusCheck2='checked';
			}
			$upload = $this->kclass->upload(array('title'=>'相关附件：','module'=>'stock','mid'=>$stock['stockid']));
			$body=<<<EOF
<form method="post" action="/s.php?module=stock&action=doupdate" name="stock">
<input type="hidden" name="module" value="stock" />
<input type="hidden" name="action" value="doupdate" />
<input type="hidden" name="stockid" value="{$stock['stockid']}" />
<table class="hundred">
<thead><tr><th colspan="4">修改库位</th></tr></thead>
<tbody>
<tr class="odd">
	<td width="120">当前库位号：</td><td width="475"><span class="big red bold">{$stock['stockno']}</span>
	　　楼层：<input type="text" name="buildingFloor" value="{$stock['buildingFloor']}" size="5"></td>
	<td width="120">流 水 号：<span class="red bold">*</span></td><td width="475"><input type="text" name="number" value="{$stock['number']}">
	层　　数：<span class="red bold">*</span><input type="text" name="floor" value="{$stock['floor']}"></td>
</tr>
<tr class="even">
	<td>体　　积：<span class="red bold">*</span></td><td>长：<input type="text" name="length" size="10" value="{$stock['length']}">cm　宽：<input type="text" name="width" size="10" value="{$stock['width']}">cm　高：<input type="text" name="height" size="10" value="{$stock['height']}">cm</td>
	<td>类　　别：<span class="red bold">*</span></td><td>{$category}</td>
</tr>
<tr class="odd">
	<td>周围情况：<span class="red bold">*</span></td><td colspan=3>上：{$stockUp}　下：{$stockDown}　左：{$stockLeft}　右：{$stockRight}　前：{$stockFront}　后：{$stockBehind}</td>
</tr>
<tr class="even">
	<td>相邻库位：</td><td colspan=3>
	上：<input type="text" name="up" style="width:115px" value="{$stock['up']}" />
	　下：<input type="text" name="down" style="width:115px" value="{$stock['down']}" />
	　左：<input type="text" name="left" style="width:115px" value="{$stock['left']}" />
	　右：<input type="text" name="right" style="width:115px" value="{$stock['right']}" />
	　前：<input type="text" name="front" style="width:115px" value="{$stock['front']}" />
	　后：<input type="text" name="behind" style="width:115px" value="{$stock['behind']}" /></td>
</tr>
<tr class="odd">
	<td>是否可放托盘：<span class="red bold">*</span></td><td><input type="radio" value="1" name="ifTray" {$trayCheck1} />是 <input type="radio" value="0" name="ifTray" {$trayCheck2} />否</td>
	<td>库位状态：<span class="red bold">*</span></td><td><input type="radio" value="permanent" name="status" {$statusCheck1} />永久 <input type="radio" value="temporary" name="status" {$statusCheck2} />临时</td>
</tr>
<tr class="even">
<td>备注：</td><td><textarea name="remark" value="{$stock['remark']}" style="width:444px;height:111px;"></textarea></td>
<td></td><td></td>
</tr>
<tr>
	<td colspan="11" class="red small">
	注意事项：<br>
	①流水号为对该库位横向排列的序号。<br>
	②“类别”为“区域”时，高度只能为0，层数只能为1。<br>
	③如果周围情况为“库位”的时候，那么就在“相邻库位”中选择库位，若相邻库位号还未确定，那么等待相邻库位号确定后，才返回修改此库位，选择已确定的相邻库位号。
	</td>
</tr>
</tbody>
</table>
{$upload}
<table class="tlist tsuite" align="center" width="100%">
<tbody>
<tr class="odd">
	<td colspan="4" align="center">
		<input type="submit" id="submitButton" value="  保存  " accesskey="s">
		<input type="reset" value="  复位  ">
	</td>
</tr>
</tbody>
</table>
</form>
EOF;

			$this->kclass->page['title'].=$stock['stockno'].' - 修改库位';
			$this->kclass->page['onload'].='dc.tabhover();';
			$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - <a href="/s.php?module=stock&action=view&stockid='.$this->kclass->input['stockid'].'">查看库位信息</a> - 修改库位', 'right'=>'<a href="/s.php?module=stock">返回列表</a>','body'=>$body));
		}
		//
		function doupdate(){
			if($this->kclass->input['requestMethod'] != 'post'){
				$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
			}else{
				if($this->kclass->input['number']==''){
					$e.='<li>请填写库位的 流水号 </li>';
				}
				if($this->kclass->input['floor']==''){
					$e.='<li>请填写库位的 层数 </li>';
				}
				if($this->kclass->input['length']==''){
					$e.='<li>请填写库位的 长度 </li>';
				}
				if($this->kclass->input['width']==''){
					$e.='<li>请填写库位的 宽度 </li>';
				}
				if($this->kclass->input['height']==''){
					$e.='<li>请填写库位的 高度 </li>';
				}
				if($this->kclass->input['categoryid']==0){
					$e.='<li>请选择库位的 类别 </li>';
				}
			}
			if(isset($e)){
				$this->kclass->messager(array(
					'title' => '修改库位',
					'text' => '您在修改库位的过程中有以下错误：<ul>'.$e.'</ul>',
					'url' => 'javascript:history.back()',
					'sec' => 3
				));
			}

			$surrounding=serialize($this->kclass->input['nearid']);
			$surroundingStock=serialize($this->kclass->input['nearStockid']);
			$stockno=$this->kclass->input['buildingFloor'].$this->kclass->input['number'].$this->kclass->input['floor'];
			$this->kclass->DB->query("
				UPDATE stock SET
					`stockno`='".$stockno."',
					`categoryid`='".$this->kclass->input['categoryid']."',
					`buildingFloor`='".$this->kclass->input['buildingFloor']."',
					`floor`='".$this->kclass->input['floor']."',
					`number`='".$this->kclass->input['number']."',
					`length`='".$this->kclass->input['length']."',
					`width`='".$this->kclass->input['width']."',
					`height`='".$this->kclass->input['height']."',
					`upid`='".$this->kclass->input['upid']."',
					`downid`='".$this->kclass->input['downid']."',
					`leftid`='".$this->kclass->input['leftid']."',
					`rightid`='".$this->kclass->input['rightid']."',
					`frontid`='".$this->kclass->input['frontid']."',
					`behindid`='".$this->kclass->input['behindid']."',
					`up`='".$this->kclass->input['up']."',
					`down`='".$this->kclass->input['down']."',
					`left`='".$this->kclass->input['left']."',
					`right`='".$this->kclass->input['right']."',
					`front`='".$this->kclass->input['front']."',
					`behind`='".$this->kclass->input['behind']."',
					`ifTray`='".$this->kclass->input['ifTray']."',
					`status`='".$this->kclass->input['status']."',
					`remark`='".$this->kclass->input['remark']."',
					`modified`='".TIMENOW."',
					`modifier`='".$this->kclass->user['userid']."'
				WHERE stockid='".$this->kclass->input['stockid']."'
			");
			$this->kclass->updateAttachs(array('module'=>'stock', 'mid'=>$this->kclass->input['stockid']));
			$this->kclass->messager(array(
				'title' => '修改库位',
				'text' => '库位 <b>'.$stockno.'</b> 已修改成功!',
				'url' => '/s.php?module=stock&action=view&stockid='.$this->kclass->input['stockid'],
				'sec' => 2
			));
		}
		//
		function kill(){
			if($this->kclass->input['stockid']<=0){
				$this->kclass->boinkIt('/s.php?module=stock');
			}
			if($this->kclass->input['rt']!='list' AND $this->kclass->input['confirm'] == 0){
				$this->kclass->boinkIt('/s.php?module=stock&action=view&stockid='.$this->kclass->input['stockid']);
			}
			if($this->kclass->input['stockid'] < 0){
				$e = '<li>请从正确的页面进行操作，如果此问题持续存在，请联系网站管理员。</li>';
			}
			if(isset($e)){
				$this->kclass->messager(array(
					'title' => '删除库位',
					'text' => '您在删除库位的过程中有以下错误：<ul>'.$e.'</ul>',
					'url' => 'javascript:history.back()',
					'sec' => 3
				));
			}
			if($this->kclass->input['rt']=='view'){
				$rt = '&action=view&stockid='.$this->kclass->input['stockid'];
			}elseif($this->kclass->input['rt']=='list'){
				$rt = '#'.$this->kclass->input['stockid'];
			}
			$stock=$this->kclass->DB->queryFirst("
				SELECT stockno
				FROM stock
				WHERE stockid='".$this->kclass->input['stockid']."'
			");
			if($stock){
				$this->kclass->DB->query("
					UPDATE `stock`
					SET killed=".TIMENOW.",killer='".$this->kclass->user['userid']."'
					WHERE stockid='".$this->kclass->input['stockid']."'
				");
				$this->kclass->messager(array(
					'title' => '删除库位成功',
					'text' => '库位 <b>'.$stock['stockno'].'</b> 已成功被标记为删除!',
					'url' => '/s.php?module=stock'.$rt,
					'sec' => 2
				));
			}else{
				$this->kclass->messager(array(
					'title' => '删除库位失败',
					'text' => '您要删除的库位，不存在！',
					'url' => '/s.php?module=stock'.$rt,
					'sec' => 3
				));
			}
		}

		//
		function remove(){
			if($this->kclass->input['stockid']<=0){
				$this->kclass->boinkIt('/s.php?module=stock');
			}
			$stock = $this->kclass->DB->queryFirst("
				SELECT stockno
				FROM `stock`
				WHERE stockid='".$this->kclass->input['stockid']."'
			");
$body = <<<EOF
<form action="/s.php?module=stock&action=kill" name="stock" method="post">
<input type="hidden" name="module" value="stock">
<input type="hidden" name="action" value="kill">
<input type="hidden" name="rt" value="{$this->kclass->input['rt']}">
<input type="hidden" name="stockid" value="{$this->kclass->input['stockid']}">
<table><thead>
<thead>
<tr>
	<th>删除库位：{$stock['stockno']}</th>
</tr>
</thead>
<tbody>
<tr class="even">
	<td class="middle">你确定要删除库位: <a href="/s.php?module=stock&action=view&stockid={$this->kclass->input['stockid']}" class="big bold" target="_blank">{$stock['stockno']}</a> 吗?</td>
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
			$this->kclass->page['title'] .= ' - 删除 - '.$stock['stockno'];
			$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 删除库位 - '.$stock['stockno'], 'right' => '<a href="/s.php?module=stock">返回列表</a>', 'body'=>$body));
		}

		//
		function revival(){
			if($this->kclass->input['stockid']<=0){
				$this->kclass->boinkIt('/s.php?module=stock');
			}
			if($this->kclass->input['rt']!='list' AND $this->kclass->input['confirm']==0){
				$this->kclass->boinkIt('/s.php?module=stock&action=view&stockid='.$this->kclass->input['stockid']);
			}
			if($this->kclass->input['stockid'] < 0){
				$e = '<li>请从正确的页面进行操作，如果此问题持续存在，请联系网站管理员。</li>';
			}
			if(isset($e)){
				$this->kclass->messager(array(
					'title' => '恢复库位',
					'text' => '您在恢复库位的过程中有以下错误：<ul>'.$e.'</ul>',
					'url' => 'javascript:history.back()',
					'sec' => 3
				));
			}
			$stock = $this->kclass->DB->queryFirst("
				SELECT stockno
				FROM `stock`
				WHERE stockid='".$this->kclass->input['stockid']."'
			");
			if($this->kclass->input['rt']=='view'){
				$rt = '&action=view&stockid='.$this->kclass->input['stockid'];
			}elseif($this->kclass->input['rt']=='list'){
				$rt = '#'.$this->kclass->input['stockid'];
			}
			if($stock){
				$this->kclass->DB->query("
					UPDATE `stock`
					SET killed=0,killer=0
					WHERE stockid='".$this->kclass->input['stockid']."'
				");
				$this->kclass->messager(array(
					'title' => '恢复库位成功',
					'text' => '库位 <b>'.$stock['stockno'].'</b> 已成功被恢复！',
					'url' => '/s.php?module=stock'.$rt,
					'sec' => 2
				));
			}else{
				$this->kclass->messager(array(
					'title' => '恢复库位失败',
					'text' => '您要恢复的库位不存在！',
					'url' => '/s.php?module=stock'.$rt,
					'sec' => 3
				));
			}
		}

		//
		function restore(){
			if($this->kclass->input['stockid']<=0){
				$this->kclass->boinkIt('/s.php?module=stock');
			}
			$stock = $this->kclass->DB->queryFirst("
				SELECT stockno
				FROM `stock`
				WHERE stockid='".$this->kclass->input['stockid']."'
			");
$body = <<<EOF
<form action="/s.php?module=stock&action=revival" name="stock" method="post">
<input type="hidden" name="module" value="stock">
<input type="hidden" name="action" value="revival">
<input type="hidden" name="rt" value="{$this->kclass->input['rt']}">
<input type="hidden" name="stockid" value="{$this->kclass->input['stockid']}">
<table><thead>
<thead>
<tr>
	<th>恢复库位：{$stock['stockno']}</th>
</tr>
</thead>
<tbody>
<tr class="even">
	<td class="middle">你确定要恢复库位: <a href="/s.php?module=stock&action=view&stockid={$this->kclass->input['stockid']}" class="big bold" target="_blank">{$stock['stockno']}</a> 吗?</td>
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
			$this->kclass->page['title'] .= ' - 恢复 - '.$stock['stockno'];
			$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 恢复库位 - '.$stock['stockno'], 'right' => '<a href="/s.php?module=stock">返回列表</a>', 'body'=>$body));
		}
		//
		function import(){
			$upload=$this->kclass->upload(array('title'=>'批量导入：'));
$body=<<<EOF
<form method="post" action="/s.php?module=stock&action=doimport">
<input type="hidden" name="module" value="stock">
<input type="hidden" name="action" value="doimport">
{$upload}
<table class="tlist tsuite" align="center" width="100%">
<tbody>
<tr class="odd center" nohover>
<td colspan=4><input type="submit" id="submitButton" value="  提交  " accesskey="s"><input type="reset" value="  复位  "/></td>
</tr>
</tbody>
</table>
</form>
EOF;
			$this->kclass->page['title']=' 批量导入库位';
			$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - 批量导入库位', 'right'=>'<a href="/s.php?module=stock">返回列表</a>','body'=>$body));
		}
		//
		function doimport(){
			require ROOT.'/phpexcel/Classes/PHPExcel.php';
			require ROOT.'/PHPExcel/Classes/PHPExcel/IOFactory.php';
			$this->kclass->input['times']+=1;
			$fileCount=0;
			if(count($this->kclass->input['attach'])>0 AND $this->kclass->input['times']==1){
				foreach($this->kclass->input['attach'] as $k => $v){
					if($v==0 OR $v==1){//0:保留，需要检查title与ordering是否更改。1:选择
						$cdt='';
						if($this->kclass->input['attachTitle'][$k] != $this->kclass->input['attachOrgTitle'][$k])$cdt="`title`='".$this->kclass->input['attachTitle'][$k]."', ";
						if($this->kclass->input['attachOrdering'][$k] != $this->kclass->input['attachOrgOrdering'][$k])$cdt.="`ordering`='".$this->kclass->input['attachOrdering'][$k]."', ";
						if($v==1)$cdt.= "`module`='".$this->kclass->input['module']."', ";
						if($cdt != ''){
							$this->kclass->DB->query("UPDATE `attachs` SET ".$cdt."`modifier`='".$this->kclass->user['userid']."', `modified`='".TIMENOW."' WHERE `attachid`='".$k."'");
						}
						$fileCount++;
					}elseif($v==2){//删除
						$attach=$this->kclass->DB->queryFirst("SELECT `folder`, `newName`, `image`, `imginfo` FROM `attachs` WHERE `attachid`='".$k."'");
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
				if($attach=$this->kclass->DB->queryFirst("SELECT COUNT(attachid) AS n,newName,folder,type FROM `attachs` WHERE `module`='".$this->kclass->input['module']."' AND `mid`=0 AND `image`<> 1 ORDER BY ordering ASC")){
					$objPHPExcel=PHPExcel_IOFactory::load('public/attachs/'.$attach['folder'].'/'.$attach['newName']);
					foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
						$worksheetTitle=$worksheet->getTitle();
						$highestRow=$worksheet->getHighestRow();
						if($highestRow>1){
							$highestColumn=$worksheet->getHighestColumn();
							$highestColumnIndex=PHPExcel_Cell::columnIndexFromString($highestColumn);
							$nrColumns=ord($highestColumn)-64;

							$total=ceil(($highestRow-1)/10);//刷新次数
							$count=$this->kclass->iif($this->kclass->input['times']==$total,$highestRow,($this->kclass->input['times'])*10+1);
							$start=($this->kclass->input['times']-1)*10+2;
							for($row=$start;$row<=$count;$row++){
								$val=array();
								for ($col=0;$col<$highestColumnIndex;$col++) {
									$cell=$worksheet->getCellByColumnAndRow($col, $row);
									$val[$col]=$cell->getValue();
								}
								if($val[0]!=''){
									$this->kclass->DB->query("
										INSERT INTO originalStock
											(`stockno`,`buildingFloor`,`number`,`floor`,`category`,`length`,`width`,`height`,`up`,`down`,`left`,`right`,`front`,`behind`,`tray`,`status`,`created`,`creator`)
										VALUES
											('".$val[0]."','".$val[1]."','".$val[2]."','".$val[3]."','".$val[4]."','".$val[5]."','".$val[6]."','".$val[7]."','".$val[8]."','".$val[9]."','".$val[10]."','".$val[11]."','".$val[12]."','".$val[13]."','".$val[14]."','".$val[15]."','".TIMENOW."','".$this->kclass->user['userid']."')
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
					'url' => '/s.php?module=stock&action=listOriginal',
					'sec' => 2
				));
			}else{
				$this->kclass->messager(array(
					'title' => '批量导入物资',
					'text' => '正在批量导入物资，每次导入10个，请等待自动返回页面...',
					'url' => '/s.php?module=stock&action=doimport&times='.$this->kclass->input['times'],
					'sec' => 2
				));
			}
		}
		//
		function listOriginal(){
			$stocks=$this->kclass->DB->query("SELECT * FROM originalStock ORDER BY originalStockid ASC");
			if($this->kclass->DB->numRows()){
				while($stock=$this->kclass->DB->fetchArray($stocks)){
					$r.='<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').'">
					<td>'.$stock['category'].'</a></td>
					<td class="middle bold">'.$stock['stockno'].'</td>
					<td>'.$stock['length'].'</td>
					<td>'.$stock['width'].'</td>
					<td>'.$stock['height'].'</td></tr>';
				}
			}
$body .= <<<EOF
<table>
<thead>
<tr>
	<th>类别</th>
	<th>库位号</th>
	<th>长</th>
	<th>宽</th>
	<th>高</th>
</tr>
</thead>
<tbody>
{$r}
</tr>
</tbody>
</table>
EOF;
		$this->kclass->page['title'] = ' 原始库位列表';
		$this->kclass->page['main'] .= $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 原始库位列表', 'right' => '<a href="/s.php?module=stock&action=exchange">转换到库位列表</a>', 'body'=>$body));
		}
		//
		function exchange(){
			$abbr=array();
			foreach($abbr as $key=>$val){
				if(strlen($val['abbr'])!=3){
					unset($abbr[$key]);
				}
			}
			$this->kclass->input['times']+=1;
			$originalStocks=$this->kclass->DB->query("SELECT * FROM originalstock ORDER BY originalStockid ASC");
			$counter=$this->kclass->DB->numRows();
			$total=ceil(($counter)/10);//刷新次数
			$count=$this->kclass->iif($this->kclass->input['times']==$total,$counter,($this->kclass->input['times'])*10);
			$min=($this->kclass->input['times']-1)*10;
			$max=$this->kclass->input['times']*10;
			if($counter){
				$i=0;
				while($originalStock=$this->kclass->DB->fetchArray($originalStocks)){
					if($i>=$min AND $max-$i<=10 AND $max-$i>0){
						$cs=$this->kclass->DB->query("SELECT categoryid,title,abbr,childlist FROM category WHERE parentid=15 ORDER BY categoryid ASC");
						if($this->kclass->DB->numRows()){
							while($c=$this->kclass->DB->fetchArray($cs)){
								if($c['title']==$originalStock['category']){
									$originalStock['categoryid']=$c['categoryid'];
								}
							}
						}
						$nears=$this->kclass->DB->query("SELECT nearid,title FROM stocknear ORDER BY nearid ASC");
						if($this->kclass->DB->numRows()){
							while($near=$this->kclass->DB->fetchArray($nears)){
								if(intval($originalStock['up'])>0){
									$originalStock['upid']=6;
								}else{
									if($originalStock['up']==$near['title']){
										$originalStock['upid']=$near['nearid'];
										$originalStock['up']='';
									}
								}
								if(intval($originalStock['down'])>0){
									$originalStock['downid']=6;
								}else{
									if($originalStock['down']==$near['title']){
										$originalStock['downid']=$near['nearid'];
										$originalStock['down']='';
									}
								}
								if(intval($originalStock['left'])>0){
									$originalStock['leftid']=6;
								}else{
									if($originalStock['left']==$near['title']){
										$originalStock['leftid']=$near['nearid'];
										$originalStock['left']='';
									}
								}
								if(intval($originalStock['right'])>0){
									$originalStock['rightid']=6;
								}else{
									if($originalStock['right']==$near['title']){
										$originalStock['rightid']=$near['nearid'];
										$originalStock['right']='';
									}
								}
								if(intval($originalStock['front'])>0){
									$originalStock['frontid']=6;
								}else{
									if($originalStock['front']==$near['title']){
										$originalStock['frontid']=$near['nearid'];
										$originalStock['front']='';
									}
								}
								if(intval($originalStock['behind'])>0){
									$originalStock['behindid']=6;
								}else{
									if($originalStock['behind']==$near['title']){
										$originalStock['behindid']=$near['nearid'];
										$originalStock['behind']='';
									}
								}
							}
						}
						if($originalStock['tray']=='是'){
							$originalStock['ifTray']=1;
						}elseif($originalStock['tray']=='否'){
							$originalStock['ifTray']=0;
						}
						$this->kclass->DB->query("
							INSERT INTO stock
								(`categoryid`,`stockno`,`buildingFloor`,`number`,`floor`,`length`,`width`,`height`,`upid`,`downid`,`leftid`,`rightid`,`frontid`,`behindid`,`up`,`down`,`left`,`right`,`front`,`behind`,`ifTray`,`status`,`remark`,`created`,`creator`)
							VALUES
								('".$originalStock['categoryid']."','".$originalStock['stockno']."','".$originalStock['buildingFloor']."','".$originalStock['number']."','".$originalStock['floor']."','".$originalStock['length']."','".$originalStock['width']."','".$originalStock['height']."','".$originalStock['upid']."','".$originalStock['downid']."','".$originalStock['leftid']."','".$originalStock['rightid']."','".$originalStock['frontid']."','".$originalStock['behindid']."','".$originalStock['up']."','".$originalStock['down']."','".$originalStock['left']."','".$originalStock['right']."','".$originalStock['front']."','".$originalStock['behind']."','".$originalStock['ifTray']."','".$originalStock['status']."','".$originalStock['remark']."','".TIMENOW."','".$this->kclass->user['userid']."')
						");
					}
					$i++;
				}
			}
			if($this->kclass->input['times']==$total){
				$this->kclass->messager(array(
					'title' => '加入库位列表',
					'text' => '物资加入库位列表成功！',
					'url' => '/s.php?module=stock&action=list',
					'sec' => 2
				));
			}else{
				$this->kclass->messager(array(
					'title' => '加入库位列表',
					'text' => '正在批量加入库位列表，每次加入10个，请等待自动返回页面...',
					'url' => '/s.php?module=stock&action=exchange&times='.$this->kclass->input['times'],
					'sec' => 2
				));
			}
		}
/********************* Private Function **********************/
		//
		function _chooserStockNear($b){
			if($b['orderby'] == ''){
				$b['orderby'] = 'nearid';
			}
			if($b['direction'] == ''){
				$b['direction'] = 'ASC';
			}
			if($b['width'] > 0){
				$b['width'] = ' style="width: '.$b['width'].'px;"';
			}

			$stockNears=$this->kclass->DB->query("
				SELECT nearid,title
				FROM `stocknear`
				WHERE killed=0
				ORDER BY ".$b['orderby']." ".$b['direction']."
			");
			$r='<select id="'.$b['name'].'" name="'.$b['name'].'"'.$b['width'].'>';
			if($this->kclass->DB->numRows()){
				if($b['hasBlank']){
					$r.='<option value="0">'.$b['topname'].'</option>';
				}
				while($stockNear=$this->kclass->DB->fetchArray($stockNears)){
					$r.='<option  value="'.$stockNear['nearid'].'"';
					if($b['selectedid'] == $stockNear['nearid']){
						$r.='selected ';
					}
					$r.='>'.$stockNear['title'].'</option>';
				}
			}
			$r.='</select>';

			return $r;
		}
		// 
		function _relatedStock($b){
			$arr=array();
			$s=explode(',',$b);
			foreach($s as $key => $val){
				$stock=$this->kclass->DB->queryFirst("SELECT stockid,ifEmpty FROM stock WHERE stockno='".$val."' LIMIT 0,1");
				$arr[]='<a href="/s.php?module=stock&action=view&stockid='.$stock['stockid'].'">'.$val.'('.$this->kclass->iif($stock['ifEmpty']==0,'空','非').')</a>';
			}
			$r=implode(',',$arr);
			return $r;
		}
	}
?>