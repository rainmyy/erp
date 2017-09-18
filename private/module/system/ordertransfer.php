<?php
class ordertransfer{
	private $type=array(0=>array('id'=>1,'title'=>'源订单','module'=>'s'),1=>array('id'=>2,'title'=>'目标订单','module'=>'t'),2=>array('id'=>3,'title'=>'源样品单','module'=>'s'),3=>array('id'=>4,'title'=>'目标样品单','module'=>'t'));
	function autoRun(){
		$this->kclass->page['title'] = '订单借调单';
		$this->baseurl = '<a href="/s.php">首页</a> - <a href="/s.php?module=ordertransfer">订单借调单</a>';
		$this->right = '<a href="/s.php?module=ordertransfer">列表</a> <a href="/s.php?module=ordertransfer&action=add">新建</a>';
		switch($this->kclass->input['action']){
			case 'list':
				$this->mmlist();
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
			case 'verify':
				$this->verify();
			break;
			case 'doverify':
				$this->doverify();
			break;
			default:
				$this->mmlist();
		}
	}
	//
	function insert(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			if($this->kclass->input['sourceItemid']<=0){
				$e.='<li>选择源订单已入库的明细物资</li>';
			}
			if($this->kclass->input['transferQuantity']<=0){
				$e.='<li>填写需要从该物资借调的 数量 </li>';
			}
			if($this->kclass->input['targetItemid']<=0){
				$e.='<li>请选择该需要借调的订单产品</li>';
			}
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '新建订单借调记录',
				'text' => '您在新建订单借调记录的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$sourceItem=$this->kclass->DB->queryFirst("SELECT orderid,sampleid FROM inbounditem WHERE itemid={$this->kclass->input['sourceItemid']}");
		if($sourceItem['orderid']>0){
			$module='order';$mid=$sourceItem['orderid'];
		}elseif($sourceItem['sampleid']>0){
			$module='sample';$mid=$sourceItem['sampleid'];
		}
		$targetItem=$this->kclass->DB->queryFirst("SELECT module,mid FROM item WHERE itemid={$this->kclass->input['targetItemid']}");
		// 订单借调记录
		$this->kclass->DB->query("
			INSERT INTO `ordertransfer` (`sourceModule`,`sourceMid`,`sourceItemid`,`targetModule`,`targetMid`,`targetItemid`,`transferQuantity`,`remark`,`creator`,`created`)
			VALUES ('".$module."','".$mid."','".$this->kclass->input['sourceItemid']."','".$targetItem['module']."','".$targetItem['mid']."','".$this->kclass->input['targetItemid']."','".$this->kclass->input['transferQuantity']."','".$this->kclass->input['remark']."','".$this->kclass->user['userid']."','".TIMENOW."')
		");
		$ordertransferid=$this->kclass->DB->insertID();

		$this->kclass->messager(array(
			'title' => '新建订单借调记录',
			'text' => '订单借调记录 已新建成功!',
			'url' => '/s.php?module=ordertransfer#'.$ordertransferid,
			'sec' => 2
		));
	}

	//
	function add(){
		$itemtr.='<tr>
			<td><select name="sourceItemid" id="sourceItemid" style="width:600px"><option value="0"></option>'.$this->_chooserSourceItem().'</select></td>
			<td><input type="text" name="transferQuantity" size="9"  /></td>
			<td><select name="targetItemid" id="targetItemid" style="width:600px"><option value="0"></option>'.$this->_chooserTargetItem().'</select></td>
			<td><input type="text" name="remark" style="width:200px" /></td>
		</tr>';
		$body=<<<EOF
<form action="/s.php?module=ordertransfer&action=insert" name="ordertransfer" method="post">
<input type="hidden" name="module" value="ordertransfer">
<input type="hidden" name="action" value="insert">
<table class="hundred">
<thead><tr><th colspan=8>订单借调记录</th></tr></thead>
<tbody>
<tr class="odd center bold">
<td colspan=2>源订单（PO）</td><td>目标订单（PI）</td><td></td>
</tr>
<tr class="center"><td>选择源订单明细 <span class="red bold">*</span></td><td>借调数量 <span class="red bold">*</span></td><td>选择目标订单明细 <span class="red bold">*</span></td><td>备注</td></tr>
{$itemtr}
<tr class="odd">
<td align="center" nohover colspan=8>
	<input type="submit" value="   保存   " accesskey="s">
	<input type="reset" value="   复位   ">
</td>
</tr>
<tbody>
</table>
</form>
EOF;
		$this->kclass->page['onload'] = 'dc.tabhover()';
		$this->kclass->page['title'] .= ' - 新建订单借调记录';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 新建订单借调记录','right' =>$this->right, 'body'=>$body));
}

	//
	function doupdate(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			if($this->kclass->input['sourceItemid']<=0){
				$e.='<li>选择源订单已入库的明细物资</li>';
			}
			if($this->kclass->input['transferQuantity']<=0){
				$e.='<li>填写需要从该物资借调的 数量 </li>';
			}
			if($this->kclass->input['targetItemid']<=0){
				$e.='<li>请选择该需要借调的订单产品</li>';
			}
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '修改订单借调单',
				'text' => '您在修改订单借调单的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$sourceItem=$this->kclass->DB->queryFirst("SELECT orderid,sampleid FROM inbounditem WHERE itemid={$this->kclass->input['sourceItemid']}");
		if($sourceItem['orderid']>0){
			$module='order';$mid=$sourceItem['orderid'];
		}elseif($sourceItem['sampleid']>0){
			$module='sample';$mid=$sourceItem['sampleid'];
		}
		$targetItem=$this->kclass->DB->queryFirst("SELECT module,mid FROM item WHERE itemid={$this->kclass->input['targetItemid']}");
		$this->kclass->DB->query("
			UPDATE `ordertransfer` SET 
				`sourceModule`='".$module."',
				`sourceMid` = '".$mid."', 
				`sourceItemid` = '".$this->kclass->input['sourceItemid']."', 
				`targetModule`='".$targetItem['module']."',
				`targetMid`='".$targetItem['mid']."',
				`targetItemid` = '".$this->kclass->input['targetItemid']."', 
				`transferQuantity` = '".$this->kclass->input['transferQuantity']."', 
				`remark` = '".$this->kclass->input['remark']."', 
				`modified` = '".TIMENOW."', 
				`modifier` = '".$this->kclass->user['userid']."'
			WHERE ordertransferid='".$this->kclass->input['ordertransferid']."'
		");
		$this->kclass->messager(array(
			'title' => '修改订单借调单',
			'text' => '修改订单借调单 已成功修改!',
			'url' => '/s.php?module=ordertransfer#'.$this->kclass->input['ordertransferid'],
			'sec' => 2
		));
	}

	//
	function update(){
		$ordertransfer=$this->kclass->DB->queryFirst("SELECT ordertransferid,sourceModule,sourceMid,sourceItemid,targetModule,targetMid,targetItemid,transferQuantity,remark,ifVerify FROM `ordertransfer` WHERE ordertransferid='".$this->kclass->input['ordertransferid']."'");
		if(!$ordertransfer){
			$e.='<li>数据错误，很抱歉~</li>';
		}
		if($ordertransfer['ifVerify']!=0){
			$e.='<li>该条订单借调单 已通过审核，请返回</li>';
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '修改订单借调单',
				'text' => '修改订单借调单<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$itemtr.='<tr>
			<td><select name="sourceItemid" id="sourceItemid" style="width:600px"><option value="0"></option>'.$this->_chooserSourceItem(array('selectedid'=>$ordertransfer['sourceItemid'])).'</select></td>
			<td><input type="text" name="transferQuantity" size="9" value="'.$ordertransfer['transferQuantity'].'" /></td>
			<td><select name="targetItemid" id="targetItemid" style="width:600px"><option value="0"></option>'.$this->_chooserTargetItem(array('selectedid'=>$ordertransfer['targetItemid'])).'</select></td>
			<td><input type="text" name="remark" style="width:200px" value="'.$ordertransfer['remark'].'" /></td>
		</tr>';
		$body=<<<EOF
<form action="/s.php?module=ordertransfer&action=doupdate" name="ordertransfer" method="post">
<input type="hidden" name="module" value="ordertransfer">
<input type="hidden" name="action" value="doupdate">
<input type="hidden" name="ordertransferid" value="{$ordertransfer['ordertransferid']}">
<table class="hundred">
<thead><tr><th colspan=8>修改订单借调记录</th></tr></thead>
<tbody>
<tr class="odd center bold">
<td colspan=2>源订单（PO）</td><td>目标订单（PI）</td><td></td>
</tr>
<tr class="center"><td>选择物资 <span class="red bold">*</span></td><td>借调数量 <span class="red bold">*</span></td><td>选择产品 <span class="red bold">*</span></td><td>备注</td></tr>
{$itemtr}
<tr class="odd">
<td align="center" nohover colspan=8>
	<input type="submit" value="   保存   " accesskey="s">
	<input type="reset" value="   复位   ">
</td>
</tr>
<tbody>
</table>
</form>
EOF;
		$this->kclass->page['onload'] = 'dc.tabhover()';
		$this->kclass->page['title'] .= ' - 修改订单借调记录';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 修改订单借调记录','right' =>$this->right, 'body'=>$body));
	}
	//
	function kill(){
		if($this->kclass->input['ordertransferid']<=0){
			$this->kclass->boinkIt('/s.php?module=ordertransfer');
		}
		if($this->kclass->input['confirm'] == 0){
			$this->kclass->boinkIt('/s.php?module=ordertransfer');
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '删除计量单位',
				'text' => '您在删除计量单位的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$unit = $this->kclass->DB->queryFirst("
			SELECT 1
			FROM ordertransfer
			WHERE ordertransferid='".$this->kclass->input['ordertransferid']."'
		");
		if($unit){
			$this->kclass->DB->query("
				UPDATE `ordertransfer`
				SET killed='".TIMENOW."',killer={$this->kclass->user['userid']}
				WHERE ordertransferid='".$this->kclass->input['ordertransferid']."'
			");
			$this->kclass->messager(array(
				'title' => '删除订单借调单',
				'text' => '订单借调单 已成功被标记为删除!',
				'url' => '/s.php?module=ordertransfer',
				'sec' => 2
			));
		}else{
			$this->kclass->messager(array(
				'title' => '删除订单借调单',
				'text' => '您要删除的订单借调单，不存在！',
				'url' => '/s.php?module=ordertransfer#'.$this->kclass->input['ordertransferid'],
				'sec' => 3
			));
		}
	}

	//
	function remove(){
		if($this->kclass->input['ordertransferid']<=0){
			$this->kclass->boinkIt('/s.php?module=ordertransfer');
		}
		$ordertransfer=$this->kclass->DB->query("
			SELECT ot.ordertransferid,ot.sourceMid,ot.sourceItemid,ot.targetMid,ot.targetItemid,ot.transferQuantity,ot.ifVerify,ot.killed,
				po.orderid AS porderid,po.orderno AS porderno,
				pi.orderid,pi.orderno,
				ii.materialid,ii.materialitemid,
				i.productid,i.productitemid
			FROM `ordertransfer` AS ot
			LEFT JOIN `order` AS po ON (po.orderid=ot.sourceMid)
			LEFT JOIN `order` AS pi ON (pi.orderid=ot.targetMid)
			LEFT JOIN `inbounditem` AS ii ON (ii.itemid=ot.sourceItemid)
			LEFT JOIN `item` AS i ON (i.itemid=ot.targetItemid)
			WHERE ordertransferid={$this->kclass->input['ordertransferid']}
			ORDER BY ot.created DESC
		");
$body = <<<EOF
<form action="/s.php?module=ordertransfer&action=kill" name="ordertransfer" method="post">
<input type="hidden" name="module" value="ordertransfer">
<input type="hidden" name="action" value="kill">
<input type="hidden" name="ordertransferid" value="{$this->kclass->input['ordertransferid']}">
<table><thead>
<tr>
	<th>删除订单借调单</th>
</tr>
</thead>
<tbody>
<tr class="even">
	<td class="middle">你确定要删除该订单借调单吗?</td>
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
		$this->kclass->page['title'] .= ' - 删除订单借调单 ';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 删除订单借调单 ', 'right' => '<a href="/s.php?module=ordertransfer">返回列表</a>', 'body'=>$body));
	}

	//
	function revival(){
		if($this->kclass->input['unitid']<=0){
			$this->kclass->boinkIt('/s.php?module=unit');
		}
		if($this->kclass->input['confirm'] == 0){
			$this->kclass->boinkIt('/s.php?module=unit');
		}
		if($this->kclass->input['unitid'] < 0){
			$e = '<li>请从正确的页面进行操作，如果此问题持续存在，请联系网站管理员。';
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '恢复计量单位',
				'text' => '您在恢复计量单位的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$unit = $this->kclass->DB->queryFirst("
			SELECT title
			FROM unit
			WHERE unitid='".$this->kclass->input['unitid']."'
		");
		if($unit){
			$this->kclass->DB->query("
				UPDATE `unit`
				SET killed=0
				WHERE unitid='".$this->kclass->input['unitid']."'
			");
			$this->kclass->messager(array(
				'title' => '恢复计量单位成功',
				'text' => '计量单位 <b>'.$unit['title'].'</b> 已成功被恢复！',
				'url' => '/s.php?module=unit',
				'sec' => 2
			));
		}else{
			$this->kclass->messager(array(
				'title' => '恢复计量单位失败',
				'text' => '您要恢复的计量单位不存在！',
				'url' => '/s.php?module=unit#'.$this->kclass->input['unitid'],
				'sec' => 3
			));
		}
	}

	//
	function restore(){
		if($this->kclass->input['unitid']<=0){
			$this->kclass->boinkIt('/s.php?module=unit');
		}
		$unit = $this->kclass->DB->queryFirst("
			SELECT title
			FROM unit
			WHERE unitid='".$this->kclass->input['unitid']."'
		");
$body = <<<EOF
<form action="/s.php?module=unit&action=revival" name="unit" method="post">
<input type="hidden" name="module" value="unit">
<input type="hidden" name="action" value="revival">
<input type="hidden" name="unitid" value="{$this->kclass->input['unitid']}">
<table><thead>
<tr>
	<th>恢复计量单位：{$unit['title']}</th>
</tr>
</thead>
<tbody>
<tr class="even">
	<td class="middle">你确定要恢复计量单位: <a href="/s.php?module=unit&action=view&unitid={$this->kclass->input['unitid']}" class="big bold" target="_blank">{$unit['title']}</a> 吗?</td>
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
		$this->kclass->page['title'] .= ' - 恢复计量单位 - '.$unit['title'];
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 恢复计量单位 - '.$unit['title'], 'right' => '<a href="/s.php?module=unit">返回列表</a>', 'body'=>$body));
	}
	// 
	function mmlist(){
		if($this->kclass->input['show']=='all'){
			$condition='1=1';
		}else{
			$condition='ot.killed=0';
		}
		$bs = $this->kclass->DB->query("
			SELECT ot.ordertransferid,ot.sourceModule,ot.sourceMid,ot.sourceItemid,ot.targetModule,ot.targetMid,ot.targetItemid,ot.transferQuantity,ot.ifVerify,ot.killed,
				ii.materialid,ii.materialitemid,
				i.productid,i.productitemid
			FROM `ordertransfer` AS ot
			LEFT JOIN `inbounditem` AS ii ON (ii.itemid=ot.sourceItemid)
			LEFT JOIN `item` AS i ON (i.itemid=ot.targetItemid)
			WHERE ".$condition."
			ORDER BY ot.created DESC
		");
		if($this->kclass->DB->numRows()){
			$i=1;
			$body = '<table class="hundred"><thead><tr><th width="15">ID</th><th width="100">源订单</th><th  width="700">源订单入库物资</th><th width="100">目标订单</th><th width="450">目标订单产品明细</th><th width="70">借调数量</th><th align="center" class="small">操作</th></tr></thead><tbody>';
			while($b = $this->kclass->DB->fetchArray($bs)){
				if($b['sourceModule']=='order'){
					$sourceUrl='/s.php?module=order&action=view&orderid='.$b['sourceMid'].'&type=PO';
					$source=$this->kclass->DB->queryFirst("SELECT orderno FROM `order` WHERE killed=0 AND orderid={$b['sourceMid']}");
					$sourceno=$source['orderno'];
				}elseif($b['sourceModule']=='sample'){
					$sourceUrl='/s.php?module=sample&action=view&sampleid='.$b['sourceMid'];
					$source=$this->kclass->DB->queryFirst("SELECT sampleno FROM `sample` WHERE killed=0 AND sampleid={$b['sourceMid']}");
					$sourceno=$source['sampleno'];
				}
				if($b['targetModule']=='order'){
					$targetUrl='/s.php?module=order&action=view&orderid='.$b['targetMid'].'&type=PI';
					$target=$this->kclass->DB->queryFirst("SELECT orderno FROM `order` WHERE killed=0 AND orderid={$b['targetMid']}");
					$targetno=$target['orderno'];
				}elseif($b['targetModule']=='sample'){
					$targetUrl='/s.php?module=sample&action=view&sampleid='.$b['targetMid'].'';
					$target=$this->kclass->DB->queryFirst("SELECT sampleno FROM `sample` WHERE killed=0 AND sampleid={$b['targetMid']}");
					$targetno=$target['sampleno'];
				}
				$materialInfo=$this->kclass->getMaterial(array('materialid'=>$b['materialid'],'itemid'=>$b['materialitemid']));
				$productInfo=$this->kclass->getProduct(array('productid'=>$b['productid'],'itemid'=>$b['productitemid']));
				$body .= '<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').'">
					<td><a name="'.$b['ordertransferid'].'">'.$i.'</a></td>
					<td><a href="'.$sourceUrl.'">'.$sourceno.'</a></td>
					<td><a href="'.$materialInfo['url'].'">'.$materialInfo['no'].'</a>　'.$materialInfo['material'].'　'.$materialInfo['standard'].'</td>
					<td><a href="'.$targetUrl.'">'.$targetno.'</a></td>
					<td><a href="'.$productInfo['url'].'">'.$productInfo['title'].'　'.$productInfo['standard'].'</a></td>
					<td>'.$b['transferQuantity'].'</td>
					<td>'.$this->kclass->iif($b['killed']==0,$this->kclass->iif($b['ifVerify']==1,'<span class="small">审核通过</span>','<a href="/s.php?module=ordertransfer&action=remove&ordertransferid='.$b['ordertransferid'].'" class="small">删</a> <a href="/s.php?module=ordertransfer&action=update&ordertransferid='.$b['ordertransferid'].'">改</a> <a href="/s.php?module=ordertransfer&action=verify&ordertransferid='.$b['ordertransferid'].'">审</a>'),'<a href="/s.php?module=ordertransfer&action=restore&ordertransferid='.$b['ordertransferid'].'">恢</a>').'</td>	
				</tr>';
				$i++;
			}
			$body .= '</tbody></table>';
		}else{
			$body = '暂无相关记录。';
		}

		$this->kclass->page['onload'] = 'dc.tabhover()';
		$this->kclass->page['title'] .= ' - 列表';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 列表', 'right' => '<span class="small">显示方式：</span><a href="/s.php?module=ordertransfer">默认列表</a>　<a href="/s.php?module=ordertransfer&action=list&show=all">所有<span class="small gray">(包括删除)</span></a>　|　<a href="/s.php?module=ordertransfer&action=add">新建</a>', 'body'=>$body));
	}
	/**
	 * 审核订单借调单
	 * @author creator zhourui 2014-5-4 11:11:37
	 * @access public
	 * @param none 没有参数
	 * @return void 没有返回信息
	 * @throws none 没有异常
	 */
	function verify(){
		$ordertransfer=$this->kclass->DB->queryFirst("SELECT ifVerify FROM `ordertransfer` WHERE ordertransferid='".$this->kclass->input['ordertransferid']."'");
		if(!$ordertransfer){
			$e.='<li>数据错误，很抱歉~</li>';
		}
		if($ordertransfer['ifVerify']!=0){
			$e.='<li>该条订单借调单 已通过审核，请返回</li>';
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '修改订单',
				'text' => '修改订单<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
$body = <<<EOF
<form action="/s.php?module=ordertransfer&action=doverify" name="ordertransfer" method="post">
<input type="hidden" name="module" value="ordertransfer">
<input type="hidden" name="action" value="doverify">
<input type="hidden" name="ordertransferid" value="{$this->kclass->input['ordertransferid']}">
<table class="hundred">
<thead>
<tr>
<th colspan="4">审核订单借调单</th>
</tr>
</thead>
<tbody>
<tr class="odd">
<td colspan="4" align="center">审核通过：<input type="radio" name="ifVerify" value="1" checked>　　　审核不通过：<input type="radio" name="ifVerify" value="0"></td>
</tr>
</tbody>
</table>
<table class="tlist tsuite" align="center" width="100%">
<tbody>
<tr class="odd">
<td colspan="4" align="center">
<input type="submit" id="submitButton" value="  提交  " accesskey="s">
</td>
</tr>
</tbody>
</table>
</form>
EOF;
		$this->kclass->page['title'] .= ' - 审核订单借调单';
		$this->kclass->page['onload'] .= 'dc.tabhover();$(\'#shipmentDate\').datepicker();';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 审核订单借调单', 'right' => '<a href="'.REFERER.'">返回</a>', 'body'=>$body));
	}

	/**
	 * 审核订单借调单
	 * @author creator zhourui 2014-5-4 11:14:11
	 * @access public
	 * @param none 没有参数
	 * @return void 没有返回信息
	 * @throws none 没有异常
	 */
	function doverify(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e='<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			$ordertransfer=$this->kclass->DB->queryFirst("SELECT ordertransferid,sourceModule,sourceMid,targetModule,targetMid,sourceItemid,targetItemid,transferQuantity,remark,ifVerify FROM `ordertransfer` WHERE ordertransferid='".$this->kclass->input['ordertransferid']."'");
			if(!$ordertransfer){
				$e.='<li>数据错误，很抱歉~</li>';
			}
			if($ordertransfer['ifVerify']!=0){
				$e.='<li>该条订单借调单 已通过审核，请返回</li>';
			}
			if($this->kclass->input['ifVerify']!=1 AND $this->kclass->input['ifVerify']!=-1){
				$e.='<li>请选择订单中的 是否通过审核 按钮。</li>';
			}
		}
		if($e){
			$this->kclass->messager(array(
				'title' => '审核订单借调单',
				'text' => '您在审核订单借调单的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		
		$this->kclass->DB->query("
			UPDATE `ordertransfer` SET
				ifVerify='".$this->kclass->input['ifVerify']."',
				verifier='".$this->kclass->user['userid']."',
				verified='".TIMENOW."'
			WHERE ordertransferid='".$this->kclass->input['ordertransferid']."'
		");
		$orderid=0;
		$sampleid=0;
		if($this->kclass->input['ifVerify']==1){
			$verify='已通过';
			$sourceOrder=$this->kclass->DB->queryFirst("
				SELECT inboundid,batchid,barcodeid,materialid,materialitemid,preitemid,versionid,quantity,unitid,dateTime
				FROM `inbounditem` 
				WHERE itemid={$ordertransfer['sourceItemid']}
			");
			if($ordertransfer['targetModule']=='order'){
				$orderid=$ordertransfer['targetMid']+1;
			}elseif($ordertransfer['targetModule']=='sample'){
				$sampleid=$ordertransfer['targetMid'];
			}
			// 减少该订单的入库数量
			$this->kclass->DB->query("
				UPDATE inbounditem SET
					quantity=quantity-{$ordertransfer['transferQuantity']}
				WHERE itemid={$ordertransfer['sourceItemid']}
			");
			// 增加借调目的订单的入库明细。
			$this->kclass->DB->query("
				INSERT INTO inbounditem (inboundid,batchid,barcodeid,orderid,sampleid,materialid,materialitemid,preitemid,versionid,quantity,iftransfer,unitid,dateTime,created,creator)
				VALUES ('{$sourceOrder['inboundid']}','{$sourceOrder['batchid']}','{$sourceOrder['barcodeid']}','{$orderid}','{$sampleid}','{$sourceOrder['materialid']}','{$sourceOrder['materialitemid']}','{$sourceOrder['preitemid']}','{$sourceOrder['versionid']}','{$ordertransfer['transferQuantity']}',1,'{$sourceOrder['unitid']}','{$sourceOrder['dateTime']}','".TIMENOW."','{$this->kclass->user['userid']}')
			");
		}elseif($this->kclass->input['ifVerify']==-1){
			$verify='未通过';
		}
		$this->kclass->messager(array(
			'title' => '审核订单借调单',
			'text' => '审核订单借调单 '.$verify.'审核!返回查看列表',
			'url' => '/s.php?module=ordertransfer&action=list#'.$this->kclass->input['ordertransferid'],
			'sec' => 2
		));
	}
	
	/*************************************************** private function ***************************************************/
	function _chooserType($b){
		if($b['width']){
			$width='style="width:'.$b['width'].'px"';
		}
		$r='<select name="'.$b['name'].'" id="'.$b['name'].'" onchange="dc.order.list(this,\''.$b['obj'].'\')" '.$width.'><option value=0></option>';
		foreach($this->type AS $type){
			if($type['module']==$b['module']){
				$r.='<option value="'.$type['id'].'">'.$type['title'].'</option>';
			}
		}
		$r.='</select>';
		return $r;
	}
	function _chooserSourceItem($b=array()){
		$id=$id1=array();
		$orders=$this->kclass->DB->query("SELECT orderid,orderno,orderTitle,ifOutbound,ifsalesReturn FROM `order` WHERE killed=0 AND type='PO' AND ifInbound=2 ORDER BY orderno DESC");
		if($this->kclass->DB->numRows()){
			while($order=$this->kclass->DB->fetchArray($orders)){
				if($order['ifOutbound']==2){ // 出库没有完全出完。但是该订单完结。
					if($order['ifsalesReturn']==1){
						$id[] = $order['orderid'];
					}
					$item=$this->kclass->DB->queryFirst("SELECT SUM(quantity) AS quantity FROM item WHERE killed=0 AND module='order' AND mid='{$order['orderid']}' ");
					$outbound=$this->kclass->DB->queryFirst("SELECT SUM(outQuantity) AS quantity FROM outbounditem AS obi LEFT JOIN outbound AS o ON (o.outboundid=obi.outboundid) WHERE o.killed=0 AND obi.killed=0 AND o.orderid={$order['orderid']} ");
					if($item['quantity']!=$outbound['quantity']){
						$id[]=$order['orderid'];
					}
				}else{
					$id[]=$order['orderid'];
				}
			}
		}
		if(!empty($id)){
			$orderItems=$this->kclass->DB->query("
				SELECT ii.itemid,ii.materialid,ii.materialitemid,ii.orderid,ii.versionid,ii.quantity,
					mv.title AS version
				FROM inbounditem AS ii
				LEFT JOIN materialversion AS mv ON (ii.versionid=mv.versionid)
				WHERE ii.killed=0 AND ii.orderid<>0 AND ii.orderid IN (".implode(',',$id).")
			");
			if($this->kclass->DB->numRows()){
				while($orderItem=$this->kclass->DB->fetchArray($orderItems)){
					$currentOrder=$this->kclass->DB->queryFirst("SELECT orderno FROM `order` WHERE orderid={$orderItem['orderid']}");
					$materialInfo=$this->kclass->getMaterial(array('materialid'=>$orderItem['materialid'],'itemid'=>$orderItem['materialitemid']));
					$itemtr.='<option value="'.$orderItem['itemid'].'"';
					if($b['selectedid']==$orderItem['itemid']){
						$itemtr.='selected';
					}
					$itemtr.=' >【'.$currentOrder['orderno'].'】　'.$materialInfo['no'].'　'.$materialInfo['material'].'　'.$materialInfo['standard'].'　版本：'.$orderItem['version'].'　已入库数量：'.$orderItem['quantity'].'</option>';
				}
			}
		}
		// 源样品单明细
		$samples=$this->kclass->DB->query("SELECT sampleid,sampleno,title,ifOutbound FROM `sample` WHERE killed=0 AND ifInbound=2 ORDER BY sampleno DESC");
		if($this->kclass->DB->numRows()){
			while($sample=$this->kclass->DB->fetchArray($samples)){
				if($sample['ifOutbound']==2){
					$item=$this->kclass->DB->queryFirst("SELECT SUM(quantity) AS quantity FROM item WHERE killed=0 AND module='sample' AND mid='{$sample['sampleid']}' ");
					$outbound=$this->kclass->DB->queryFirst("SELECT SUM(outQuantity) AS quantity FROM outbounditem AS obi LEFT JOIN outbound AS o ON (o.outboundid=obi.outboundid) WHERE o.killed=0 AND obi.killed=0 AND o.orderid={$sample['sampleid']} ");
					if($item['quantity']!=$outbound['quantity']){
						$id1[]=$sample['sampleid'];
					}
				}else{
					$id1[]=$sample['sampleid'];
				}
			}
		}
		if(!empty($id)){
			$sampleItems=$this->kclass->DB->query("
				SELECT ii.itemid,ii.materialid,ii.materialitemid,ii.sampleid,ii.versionid,ii.quantity,
					mv.title AS version
				FROM inbounditem AS ii
				LEFT JOIN materialversion AS mv ON (ii.versionid=mv.versionid)
				WHERE ii.killed=0 AND  ii.sampleid<>0 AND ii.sampleid IN (".implode(',',$id1).")
			");
			if($this->kclass->DB->numRows()){
				while($sampleItem=$this->kclass->DB->fetchArray($sampleItems)){
					$currentSample=$this->kclass->DB->queryFirst("SELECT sampleno FROM `sample` WHERE sampleid={$sampleItem['sampleid']}");
					$materialInfo=$this->kclass->getMaterial(array('materialid'=>$sampleItem['materialid'],'itemid'=>$sampleItem['materialitemid']));
					$itemtr.='<option value="'.$sampleItem['itemid'].'"';
					if($b['selectedid']==$sampleItem['itemid']){
						$itemtr.='selected';
					}
					$itemtr.='>【'.$currentSample['sampleno'].'】　'.$materialInfo['no'].'　'.$materialInfo['material'].'　'.$materialInfo['standard'].'　版本：'.$sampleItem['version'].'　已入库数量：'.$sampleItem['quantity'].'</option>';
				}
			}
		}
		
		return $itemtr;
	}
	function _chooserTargetItem($b=array()){
		$id=$id1=array();
		// 目标订单明细
		$orders=$this->kclass->DB->query("SELECT orderid,orderno,orderTitle,parentid FROM `order` WHERE killed=0 AND type='PO' AND ifOutbound<>2 ORDER BY orderno DESC");
		if($this->kclass->DB->numRows()){
			while($order=$this->kclass->DB->fetchArray($orders)){
				$id[]=$order['parentid'];
			}
		}
		if(!empty($id)){
			$items=$this->kclass->DB->query("
				SELECT itemid,productid,productitemid,quantity,mid
				FROM item 
				WHERE killed=0 AND module='order' AND mid IN (".implode(',',$id).")
			");
			if($this->kclass->DB->numRows()){
				while($item=$this->kclass->DB->fetchArray($items)){
					$currentOrder=$this->kclass->DB->queryFirst("SELECT orderno FROM `order` WHERE orderid={$item['mid']}");
					$product=$this->kclass->getProduct(array('productid'=>$item['productid'],'itemid'=>$item['productitemid']));
					$itemtr.='<option value="'.$item['itemid'].'"';
					if($b['selectedid']==$item['itemid']){
						$itemtr.='selected';
					}
					$itemtr.='>【'.$currentOrder['orderno'].'】　'.$product['title'].'　'.$product['standard'].'　需求数量：'.$item['quantity'].'</option>';
				}
			}
		}
		// 目标样品单明细
		$samples=$this->kclass->DB->query("SELECT sampleid,sampleno FROM `sample` WHERE killed=0 AND ifOutbound<>2 ORDER BY sampleno DESC");
		if($this->kclass->DB->numRows()){
			while($sample=$this->kclass->DB->fetchArray($samples)){
				$id1[]=$sample['sampleid'];
			}
		}
		if(!empty($id1)){
			$items=$this->kclass->DB->query("
				SELECT itemid,productid,productitemid,quantity,mid
				FROM item 
				WHERE killed=0 AND module='sample' AND mid IN (".implode(',',$id1).")
			");
			if($this->kclass->DB->numRows()){
				while($item=$this->kclass->DB->fetchArray($items)){
					$currentSample=$this->kclass->DB->queryFirst("SELECT sampleno FROM `sample` WHERE sampleid={$item['mid']}");
					$product=$this->kclass->getProduct(array('productid'=>$item['productid'],'itemid'=>$item['productitemid']));
					$itemtr.='<option value="'.$item['itemid'].'"';
					if($b['selectedid']==$item['itemid']){
						$itemtr.='selected';
					}
					$itemtr.='>【'.$currentSample['sampleno'].'】　'.$product['title'].'　'.$product['standard'].'　需求数量：'.$item['quantity'].'</option>';
				}
			}
		}
		return $itemtr;
	}
}
?>