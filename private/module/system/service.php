<?php
// 
class service{

	function autorun(){
		$this->baseurl='<a href="/s.php?">首页</a>';
		switch($this->kclass->input['action']){
			case 'claim':
				return $this->claim();
			break;
			case 'addClaim':
				return $this->addClaim();
			break;
			case 'insertClaim':
				return $this->insertClaim();
			break;
			case 'updateClaim':
				return $this->updateClaim();
			break;
			case 'doupdateClaim':
				return $this->doupdateClaim();
			break;
			case 'killClaim':
				return $this->killClaim();
			break;
			case 'return':
				return $this->mmreturn();
			break;
			case 'addReturn':
				return $this->addReturn();
			break;
			case 'insertReturn':
				return $this->insertReturn();
			break;
			case 'updateReturn':
				return $this->updateReturn();
			break;
			case 'doupdateReturn':
				return $this->doupdateReturn();
			break;
			case 'killReturn':
				return $this->killReturn();
			break;
			default:
				return $this->mmlist();
		}
	}
	//
	function addClaim(){
		if($this->kclass->input['orderid'] AND $this->kclass->input['porderid']){
			$this->kclass->boinkIt('/s.php');
		}elseif(!$this->kclass->input['orderid'] AND !$this->kclass->input['porderid']){
			if(!$this->kclass->input['type']){
				$body=<<<EOF
<table style="width:450px;">
<thead><tr><th colspan=2>新建索赔</th></tr></thead>
<tbody>
<form action="/s.php?module=accounting&action=addDebitnote" method="get">
<input type="hidden" name="module" value="accounting">
<input type="hidden" name="action" value="addDebitnote">
<tr><td>订单类型</td><td><input type="radio" name="type" value="PO" checked />采购订单 <input type="radio" name="type" value="PI" />销售订单</td></tr>
<tr><td colspan=2 class="center"><input type="submit" value="提交"><input type="reset" value="重置"></td></tr>
</form>
</tbody>
</table>
EOF;
			}else{
				if($this->kclass->input['type']=='PO'){
					$order=$this->kclass->chooserOrder(array('module'=>'porder','name'=>'porderid','hasBlank'=>1,'topname'=>'请选择','width'=>250));
				}elseif($this->kclass->input['type']=='PI'){
					$order=$this->kclass->chooserOrder(array('module'=>'order','name'=>'orderid','hasBlank'=>1,'topname'=>'请选择','type'=>'PI','width'=>250));
				}
				$body=<<<EOF
<table style="width:450px;">
<thead><tr><th colspan=2>新建索赔</th></tr></thead>
<tbody>
<form action="/s.php?module=accounting&action=addDebitnote" method="get">
<input type="hidden" name="module" value="accounting">
<input type="hidden" name="action" value="addDebitnote">
<tr><td>请选择订单：</td><td>{$order}</td></tr>
<tr><td colspan=2 class="center"><input type="submit" value="提交"> <input type="reset" value="重置"></td></tr>
</form>
</tbody>
</table>
EOF;
			}
		}else{
			if($this->kclass->input['orderid']>0){
				$module='order';
				$id=$this->kclass->input['orderid'];
				$value='`product`.productno,`product`.title';
				$leftJoin='`product` ON (`product`.productid=`item`.productid)';
			}elseif($this->kclass->input['porderid']>0){
				$module='porder';
				$id=$this->kclass->input['porderid'];
				$value='`material`.materialno,`material`.title,`material`.standard';
				$leftJoin='`material` ON (`material`.materialid=`item`.materialid)';
			}

			$order=$this->kclass->DB->queryFirst("
				SELECT o.".$module."id,o.orderno,o.currencyid,supplierid
				FROM `".$module."` AS o
				WHERE o.killed=0 AND o.".$module."id='".$id."'
				LIMIT 0,1
			");
			$items=$this->kclass->DB->query("
				SELECT `item`.itemid,
					".$value." 
				FROM `item` 
				LEFT JOIN ".$leftJoin."
				WHERE `item`.killed=0 AND `item`.module='".$module."' AND `item`.mid='".$id."' 
				ORDER BY itemid ASC
			");
			if($this->kclass->DB->numRows()){
				while($item=$this->kclass->DB->fetchArray($items)){
					$itemSelect.='<option value="'.$item['itemid'].'">'.$this->kclass->iif($module=='porder',$item['materialno'].'　'.$item['title'].'　'.$item['standard'],$item['productno'].'　'.$item['title']).'</option>';
				}
			}
			$currency=$this->kclass->chooserCurrency(array('name'=>'currencyid','width'=>100,'hasBlank'=>1,'topname'=>'','selectedid'=>$order['currencyid']));

			$body=<<<EOF
<form action="/s.php?module=service&action=insertClaim" method="post">
<input type="hidden" name="module" value="service">
<input type="hidden" name="action" value="insertClaim">
<input type="hidden" name="m" value="{$module}">
<input type="hidden" name="mid" value="{$id}">
<input type="hidden" name="supplierid" value="{$order['supplierid']}">
<table class="hundred">
<thead></tr><th colspan="4">新建索赔记录</th></tr></thead>
<tbody>
<tr class="odd">
	<td width="120">标　　题：<span class="red bold">*</span></td><td width="475"><input type="text" name="title" style="width:250px;" />　　币种：{$currency}</td>
	<td width="80">订单编号：</td><td><a href="/s.php?module={$module}&action=view&{$module}id={$id}">{$order['orderno']}</a></td>
</tr>
<tr class="even">
	<td>该订单的物资：<span class="red bold">*</span></td><td><select name="itemid"><option value="0">请选择</option>{$itemSelect}</select></td>
	<td>索赔金额：<span class="red bold">*</span></td><td><input type="text" name="amount" /></td>
</tr>
<tr class="odd">
	<td>索赔理由：<span class="red bold">*</span></td><td><textarea name="reason" style="width:444px;height:111px"></textarea></td>
	<td>备　　注：</td><td><textarea name="remark" style="width:444px;height:111px"></textarea></td>
</tr>
<tbody>
</table>
<table class="tlist tsuite" align="center" width="100%">
<tbody>
<tr class="even" nohover>
<td class="center" colspan=4><input type="submit" id="submitButton" value="  提交  " accesskey="s""><input type="reset" value="  复位  "/></td>
</tr>
</tbody>
</table>
</form>
EOF;
		}
		$this->kclass->tbline+=4;
		$this->kclass->page['title'].='新增索赔单';
		$this->kclass->page['onload'].='dc.tabhover();';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - '.$order['orderno'].' - 新增索赔单','body' => $body));
	}
	// 
	function insertClaim(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			if($this->kclass->input['title']==''){
				$e.='<li>请填写索赔单的 标题 </li>';
			}
			if($this->kclass->input['currencyid']<=0){
				$e.='<li>请填写索赔单的 币种 </li>';
			}
			if($this->kclass->input['itemid']<=0){
				$e.='<li>请选择索赔单的 物资 </li>';
			}
			if($this->kclass->input['amount']==''){
				$e.='<li>请填写索赔单的 金额 </li>';
			}
			if($this->kclass->input['reason']==''){
				$e.='<li>请填写索赔单的 理由 </li>';
			}
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '新增索赔单',
				'text' => '新增索赔单<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		
		$this->kclass->DB->query("
			INSERT INTO claim
				(module,supplierid,mid,title,itemid,amount,currencyid,reason,remark,created,creator) 
			VALUES
				('".$this->kclass->input['m']."','".$this->kclass->input['supplierid']."','".$this->kclass->input['mid']."','".$this->kclass->input['title']."','".$this->kclass->input['itemid']."','".$this->kclass->input['amount']."','".$this->kclass->input['currencyid']."','".$this->kclass->input['reason']."','".$this->kclass->input['remark']."','".TIMENOW."','".$this->kclass->user['userid']."')
		");
		$claimid=$this->kclass->DB->insertID();

		$this->kclass->messager(array(
			'title' => '新增索赔单',
			'text' => '索赔单 <b>'.$this->kclass->input['title'].'</b> 已新建成功!返回索赔详情页',
			'url' => '/s.php?module='.$this->kclass->input['m'].'&action=view&'.$this->kclass->input['m'].'id='.$this->kclass->input['mid'],
			'sec' => 2
		));
	}
	// 
	function updateClaim(){
		$claim=$this->kclass->DB->queryFirst("SELECT * FROM claim WHERE killed=0 AND claimid='".$this->kclass->input['claimid']."' LIMIT 0,1");
		if(!$claim){
			$e='<li>数据错误，很抱歉！ </li>';
		}
		if($e){
			$this->kclass->messager(array(
				'title' => '修改索赔单',
				'text' => '修改索赔单<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		if($claim['module']=='order'){
			$value='`product`.productno,`product`.title';
			$leftJoin='`product` ON (`product`.productid=`item`.productid)';
		}elseif($claim['module']=='porder'){
			$value='`material`.materialno,`material`.title,`material`.standard';
			$leftJoin='`material` ON (`material`.materialid=`item`.materialid)';
		}
		$order=$this->kclass->DB->queryFirst("
			SELECT ".$claim['module']."id,orderno,currencyid,supplierid
			FROM `".$claim['module']."` 
			WHERE killed=0 AND ".$claim['module']."id='".$claim['mid']."'
			LIMIT 0,1
		");
		$items=$this->kclass->DB->query("
			SELECT `item`.itemid,
				".$value." 
			FROM `item` 
			LEFT JOIN ".$leftJoin."
			WHERE `item`.killed=0 AND `item`.module='".$claim['module']."' AND `item`.mid='".$claim['mid']."' 
			ORDER BY itemid ASC
		");
		if($this->kclass->DB->numRows()){
			while($item=$this->kclass->DB->fetchArray($items)){
				$itemSelect.='<option value="'.$item['itemid'].'" ';
				if($item['itemid']==$claim['itemid']){
					$itemSelect.='selected ';
				}
				$itemSelect.='>'.$this->kclass->iif($claim['module']=='porder',$item['materialno'].'　'.$item['title'].'　'.$item['standard'],$item['productno'].'　'.$item['title']).'</option>';
			}
		}
		$currency=$this->kclass->chooserCurrency(array('name'=>'currencyid','width'=>100,'hasBlank'=>1,'topname'=>'','selectedid'=>$claim['currencyid']));
		$body=<<<EOF
<form action="/s.php?module=service&action=doupdateClaim" method="post">
<input type="hidden" name="module" value="service">
<input type="hidden" name="action" value="doupdateClaim">
<input type="hidden" name="m" value="{$claim['module']}">
<input type="hidden" name="mid" value="{$claim['mid']}">
<input type="hidden" name="claimid" value="{$this->kclass->input['claimid']}">
<input type="hidden" name="supplierid" value="{$order['supplierid']}">
<table class="hundred">
<thead></tr><th colspan="4">新建索赔记录</th></tr></thead>
<tbody>
<tr class="odd">
	<td width="120">标　　题：<span class="red bold">*</span></td><td width="475"><input type="text" name="title" style="width:250px;" value="{$claim['title']}" />　　币种：{$currency}</td>
	<td width="80">订单编号：</td><td><a href="/s.php?module={$claim['module']}&action=view&{$claim['module']}id={$claim['mid']}">{$order['orderno']}</a></td>
</tr>
<tr class="even">
	<td>该订单的物资：<span class="red bold">*</span></td><td><select name="itemid"><option value="0">请选择</option>{$itemSelect}</select></td>
	<td>索赔金额：<span class="red bold">*</span></td><td><input type="text" name="amount" value="{$claim['amount']}" /></td>
</tr>
<tr class="odd">
	<td>索赔理由：<span class="red bold">*</span></td><td><textarea name="reason" style="width:444px;height:111px">{$claim['reason']}</textarea></td>
	<td>备　　注：</td><td><textarea name="remark" style="width:444px;height:111px">{$claim['remark']}</textarea></td>
</tr>
<tbody>
</table>
<table class="tlist tsuite" align="center" width="100%">
<tbody>
<tr class="even" nohover>
<td class="center" colspan=4><input type="submit" id="submitButton" value="  提交  " accesskey="s""><input type="reset" value="  复位  "/></td>
</tr>
</tbody>
</table>
</form>
EOF;
		$this->kclass->tbline+=4;
		$this->kclass->page['title'].='修改索赔单';
		$this->kclass->page['onload'].='dc.tabhover();';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - '.$order['orderno'].' - 修改索赔单','body' => $body));
	}
	// 
	function doupdateClaim(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			if($this->kclass->input['title']==''){
				$e.='<li>请填写索赔单的 标题 </li>';
			}
			if($this->kclass->input['currencyid']<=0){
				$e.='<li>请填写索赔单的 币种 </li>';
			}
			if($this->kclass->input['itemid']<=0){
				$e.='<li>请选择索赔单的 物资 </li>';
			}
			if($this->kclass->input['amount']==''){
				$e.='<li>请填写索赔单的 金额 </li>';
			}
			if($this->kclass->input['reason']==''){
				$e.='<li>请填写索赔单的 理由 </li>';
			}
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '修改索赔单',
				'text' => '修改索赔单<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		
		$this->kclass->DB->query("
			UPDATE claim SET 
				title='".$this->kclass->input['title']."',
				itemid='".$this->kclass->input['itemid']."',
				amount='".$this->kclass->input['amount']."',
				currencyid='".$this->kclass->input['currencyid']."',
				reason='".$this->kclass->input['reason']."',
				remark='".$this->kclass->input['remark']."',
				modified='".TIMENOW."',
				modifier='".$this->kclass->user['userid']."'
			WHERE claimid='".$this->kclass->input['claimid']."'
		");

		$this->kclass->messager(array(
			'title' => '修改索赔单',
			'text' => '索赔单 <b>'.$this->kclass->input['title'].'</b> 已修改成功!返回订单详情页',
			'url' => '/s.php?module='.$this->kclass->input['m'].'&action=view&'.$this->kclass->input['m'].'id='.$this->kclass->input['mid'],
			'sec' => 2
		));
	}
	// 
	function killClaim(){
		if($this->kclass->input['claimid']<=0){
			$this->kclass->boinkIt('/s.php?module=service&action=claim');
		}
		if($this->kclass->input['rt']!='list' AND $this->kclass->input['confirm'] == 0){
			$this->kclass->boinkIt('/s.php?module=service&action=claim&claimid='.$this->kclass->input['claimid']);
		}
		if($this->kclass->input['claimid'] < 0){
			$e = '<li>请从正确的页面进行操作，如果此问题持续存在，请联系网站管理员。</li>';
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '删除应付',
				'text' => '您在删除应付的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		if($this->kclass->input['rt']=='view'){
			$rt = '&claimid='.$this->kclass->input['claimid'];
		}elseif($this->kclass->input['rt']=='list'){
			$rt = '#'.$this->kclass->input['claimid'];
		}
		$claim=$this->kclass->DB->queryFirst("
			SELECT title
			FROM `claim`
			WHERE claimid='".$this->kclass->input['claimid']."'
		");
		if($claim){
			$this->kclass->DB->query("
				UPDATE `claim`
				SET killed=".TIMENOW.",killer='".$this->kclass->user['userid']."'
				WHERE claimid='".$this->kclass->input['claimid']."'
			");
			$this->kclass->messager(array(
				'title' => '删除应付成功',
				'text' => '应付 <b>'.$claim['title'].'</b> 已成功被标记为删除!',
				'url' => REFERER,
				'sec' => 2
			));
		}else{
			$this->kclass->messager(array(
				'title' => '删除应付失败',
				'text' => '您要删除的应付，不存在！',
				'url' => REFERER,
				'sec' => 3
			));
		}
	}
	// 
	function mmreturn(){
		if($this->kclass->input['returnid']<0){
$body=<<<EOF

EOF;
			$this->kclass->page['title'].='退货单列表';
			$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 退货单列表', 'right'=>'','body' => $body));

		}else{
			$return=$this->kclass->DB->queryFirst("
				SELECT r.*, 
					o.orderno,
					i.materialid,i.materialno,i.title,i.standard, 
					u.username AS creator, 
					us.username AS modifier 
				FROM `return` AS r 
				LEFT JOIN `order` AS o ON (o.orderid=r.orderid) 
				LEFT JOIN `item` AS i ON (i.itemid=r.itemid) 
				LEFT JOIN `user` AS u ON (u.userid=r.creator) 
				LEFT JOIN `user` AS us ON (us.userid=r.modifier) 
				WHERE r.killed=0 AND r.returnid='".$this->kclass->input['returnid']."' 
				LIMIT 0,1
			");
			if($return){
				$return['created']=date('Y-n-d H:s',$return['created']);
				if($return['modified']!=0)$modify='，由'.$return['modifier'].'于'.$return['modified'].'修改';

$body=<<<EOF
<div class="title"><span class="right small gray">由{$return['creator']}于{$return['created']}建立{$modify}。</span>{$return['returnno']}</div>
<table class="hundred">
<thead></tr><th colspan="4">查看退货</th></tr></thead>
<tbody>
<tr class="odd">
	<td width="150">退货单号：<span class="red bold">*</span></td><td width="450">{$return['returnno']}</td>
	<td width="80">订单编号：<span class="red bold">*</span></td><td><a href="/s.php?module=order&action=view&orderid={$order['orderid']}">{$return['orderno']}</a></td>
</tr>
<tr class="even">
	<td>选择该订单的物资：<span class="red bold">*</span></td><td><a href="/s.php?module=material&action=view&materialid={$return['materialid']}">{$return['materialno']} {$return['title']} {$return['standard']}</a></td>
	<td>退货理由：<span class="red bold">*</span></td><td>{$return['reason']}</td>
</tr>
<tr class="odd">
	<td>备注：</td><td>{$return['remark']}</textarea></td>
	<td></td><td></td>
</tr>
<tbody>
</table>
EOF;
			}else{
				$body='数据错误，请抱歉！';
			}
			$this->kclass->tbline+=4;
			$this->kclass->page['title'].='查看退货单';
			$this->kclass->page['onload'].='dc.tabhover();';
			$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - <a href="/s.php?module=return&action=list">退货单列表</a> - 查看退货单', 'right' => '<a href="/s.php?module=service&action=updateReturn&orderid='.$return['orderid'].'">修改</a>','body' => $body));
		}
	}
	//
	function addreturn(){
		if($this->kclass->input['orderid'] AND $this->kclass->input['porderid']){
			$this->kclass->boinkIt('/s.php');
		}elseif(!$this->kclass->input['orderid'] AND !$this->kclass->input['porderid']){
			if(!$this->kclass->input['type']){
				$body=<<<EOF
<table style="width:450px;">
<thead><tr><th colspan=2>新建退货</th></tr></thead>
<tbody>
<form action="/s.php?module=accounting&action=addDebitnote" method="get">
<input type="hidden" name="module" value="accounting">
<input type="hidden" name="action" value="addDebitnote">
<tr><td>订单类型</td><td><input type="radio" name="type" value="PO" checked />采购订单 <input type="radio" name="type" value="PI" />销售订单</td></tr>
<tr><td colspan=2 class="center"><input type="submit" value="提交"><input type="reset" value="重置"></td></tr>
</form>
</tbody>
</table>
EOF;
			}else{
				if($this->kclass->input['type']=='PO'){
					$order=$this->kclass->chooserOrder(array('module'=>'porder','name'=>'porderid','hasBlank'=>1,'topname'=>'请选择','width'=>250));
				}elseif($this->kclass->input['type']=='PI'){
					$order=$this->kclass->chooserOrder(array('module'=>'order','name'=>'orderid','hasBlank'=>1,'topname'=>'请选择','type'=>'PI','width'=>250));
				}
				$body=<<<EOF
<table style="width:450px;">
<thead><tr><th colspan=2>新建退货</th></tr></thead>
<tbody>
<form action="/s.php?module=accounting&action=addDebitnote" method="get">
<input type="hidden" name="module" value="accounting">
<input type="hidden" name="action" value="addDebitnote">
<tr><td>请选择订单：</td><td>{$order}</td></tr>
<tr><td colspan=2 class="center"><input type="submit" value="提交"> <input type="reset" value="重置"></td></tr>
</form>
</tbody>
</table>
EOF;
			}
		}else{
			if($this->kclass->input['orderid']>0){
				$module='order';
				$id=$this->kclass->input['orderid'];
				$value='`product`.productno,`product`.title';
				$leftJoin='`product` ON (`product`.productid=`item`.productid)';
			}elseif($this->kclass->input['porderid']>0){
				$module='porder';
				$id=$this->kclass->input['porderid'];
				$value='`material`.materialno,`material`.title,`material`.standard';
				$leftJoin='`material` ON (`material`.materialid=`item`.materialid)';
			}

			$order=$this->kclass->DB->queryFirst("
				SELECT o.".$module."id,o.orderno,o.currencyid,supplierid
				FROM `".$module."` AS o
				WHERE o.killed=0 AND o.".$module."id='".$id."'
				LIMIT 0,1
			");
			$items=$this->kclass->DB->query("
				SELECT `item`.itemid,
					".$value." 
				FROM `item` 
				LEFT JOIN ".$leftJoin."
				WHERE `item`.killed=0 AND `item`.module='".$module."' AND `item`.mid='".$id."' 
				ORDER BY itemid ASC
			");
			if($this->kclass->DB->numRows()){
				while($item=$this->kclass->DB->fetchArray($items)){
					$itemSelect.='<option value="'.$item['itemid'].'">'.$this->kclass->iif($module=='porder',$item['materialno'].'　'.$item['title'].'　'.$item['standard'],$item['productno'].'　'.$item['title']).'</option>';
				}
			}
			$body=<<<EOF
<form action="/s.php?module=service&action=insertReturn" method="post">
<input type="hidden" name="module" value="service">
<input type="hidden" name="action" value="insertReturn">
<input type="hidden" name="m" value="{$module}">
<input type="hidden" name="mid" value="{$id}">
<input type="hidden" name="supplierid" value="{$order['supplierid']}">
<table class="hundred">
<thead></tr><th colspan="4">新建退货记录</th></tr></thead>
<tbody>
<tr class="odd">
	<td width="120">退货单号：<span class="red bold">*</span></td><td><input type="text" name="returnno" /></td>
	<td width="80">订单编号：<span class="red bold">*</span></td><td><a href="/s.php?module={$module}&action=view&{$module}id={$id}">{$order['orderno']}</a></td>
</tr>
<tr class="even">
	<td>该订单的物资：<span class="red bold">*</span></td><td><select name="itemid"><option value="0">请选择</option>{$itemSelect}</select></td>
	<td>退货理由：<span class="red bold">*</span></td><td><textarea name="reason" style="width:444px;height:111px;" ></textarea></td>
</tr>
<tr class="odd">
	<td>备　　注：</td><td><textarea name="remark" style="width:444px;height:111px;"></textarea></td>
	<td></td><td></td>
</tr>
<tbody>
</table>
<table class="tlist tsuite" align="center" width="100%">
<tbody>
<tr class="even" nohover>
<td class="center" colspan=4><input type="submit" id="submitButton" value="  提交  " accesskey="s""><input type="reset" value="  复位  "/></td>
</tr>
</tbody>
</table>
</form>
EOF;
		}
		$this->kclass->tbline+=4;
		$this->kclass->page['title'].='新增退货单';
		$this->kclass->page['onload'].='dc.tabhover();';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - <a href="/s.php?module=return&action=list">退货单列表</a> - 新增退货单', 'right' => '<a href="/s.php?module=service&action=return">返回列表</a>','body' => $body));
	}
	// 
	function insertreturn(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			if($this->kclass->input['returnno']==''){
				$e.='<li>请填写退货的 退货单号</li>';
			}
			if($this->kclass->input['itemid']==''){
				$e.='<li>请选择退货的 物资</li>';
			}
			if($this->kclass->input['reason']==''){
				$e.='<li>请填写退货的 理由</li>';
			}
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '新增退货单',
				'text' => '新增退货单<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		
		$this->kclass->DB->query("
			INSERT INTO `return`
				(module,supplierid,mid,returnno,itemid,reason,remark,created,creator) 
			VALUES
				('".$this->kclass->input['m']."','".$this->kclass->input['supplierid']."','".$this->kclass->input['mid']."','".$this->kclass->input['returnno']."','".$this->kclass->input['itemid']."','".$this->kclass->input['reason']."','".$this->kclass->input['remark']."','".TIMENOW."','".$this->kclass->user['userid']."')
		");
		$returnid=$this->kclass->DB->insertID();

		$this->kclass->messager(array(
			'title' => '新增退货单',
			'text' => '退货单 <b>'.$this->kclass->input['returnno'].'</a> 已新建成功!返回退货详情页',
			'url' => '/s.php?module='.$this->kclass->input['m'].'&action=view&'.$this->kclass->input['m'].'id='.$this->kclass->input['mid'],
			'sec' => 2
		));
	}
	// 
	function updateReturn(){
		$return=$this->kclass->DB->queryFirst("SELECT * FROM `return` WHERE killed=0 AND returnid='".$this->kclass->input['returnid']."' LIMIT 0,1");
		if(!$return){
			$e='<li>数据错误，很抱歉！ </li>';
		}
		if($e){
			$this->kclass->messager(array(
				'title' => '修改退货单',
				'text' => '修改退货单<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		if($return['module']=='order'){
			$value='`product`.productno,`product`.title';
			$leftJoin='`product` ON (`product`.productid=`item`.productid)';
		}elseif($return['module']=='porder'){
			$value='`material`.materialno,`material`.title,`material`.standard';
			$leftJoin='`material` ON (`material`.materialid=`item`.materialid)';
		}
		$order=$this->kclass->DB->queryFirst("
			SELECT ".$return['module']."id,orderno,currencyid,supplierid
			FROM `".$return['module']."` 
			WHERE killed=0 AND ".$return['module']."id='".$return['mid']."'
			LIMIT 0,1
		");
		$items=$this->kclass->DB->query("
			SELECT `item`.itemid,
				".$value." 
			FROM `item` 
			LEFT JOIN ".$leftJoin."
			WHERE `item`.killed=0 AND `item`.module='".$return['module']."' AND `item`.mid='".$return['mid']."' 
			ORDER BY itemid ASC
		");
		if($this->kclass->DB->numRows()){
			while($item=$this->kclass->DB->fetchArray($items)){
				$itemSelect.='<option value="'.$item['itemid'].'" ';
				if($item['itemid']==$return['itemid']){
					$itemSelect.='selected ';
				}
				$itemSelect.='>'.$this->kclass->iif($return['module']=='porder',$item['materialno'].'　'.$item['title'].'　'.$item['standard'],$item['productno'].'　'.$item['title']).'</option>';
			}
		}
		$body=<<<EOF
<form action="/s.php?module=service&action=doupdateReturn" method="post">
<input type="hidden" name="module" value="service">
<input type="hidden" name="action" value="doupdateReturn">
<input type="hidden" name="m" value="{$return['module']}">
<input type="hidden" name="mid" value="{$return['mid']}">
<input type="hidden" name="returnid" value="{$this->kclass->input['returnid']}">
<input type="hidden" name="returnno" value="{$return['returnno']}">
<input type="hidden" name="supplierid" value="{$order['supplierid']}">
<table class="hundred">
<thead></tr><th colspan="4">新建退货记录</th></tr></thead>
<tbody>
<tr class="odd">
	<td width="120">退货单号：<span class="red bold">*</span></td><td>{$return['returnno']} <span class="small gray">退货单号不可更改</span></td>
	<td width="80">订单编号：<span class="red bold">*</span></td><td><a href="/s.php?module={$module}&action=view&{$module}id={$id}">{$order['orderno']}</a></td>
</tr>
<tr class="even">
	<td>该订单的物资：<span class="red bold">*</span></td><td><select name="itemid"><option value="0">请选择</option>{$itemSelect}</select></td>
	<td>退货理由：<span class="red bold">*</span></td><td><textarea name="reason" style="width:444px;height:111px;" >{$return['reason']}</textarea></td>
</tr>
<tr class="odd">
	<td>备　　注：</td><td><textarea name="remark" style="width:444px;height:111px;">{$return['remark']}</textarea></td>
	<td></td><td></td>
</tr>
<tbody>
</table>
<table class="tlist tsuite" align="center" width="100%">
<tbody>
<tr class="even" nohover>
<td class="center" colspan=4><input type="submit" id="submitButton" value="  提交  " accesskey="s""><input type="reset" value="  复位  "/></td>
</tr>
</tbody>
</table>
</form>
EOF;
		$this->kclass->tbline+=4;
		$this->kclass->page['title'].='修改退货单';
		$this->kclass->page['onload'].='dc.tabhover();';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - <a href="/s.php?module=service&action=return">退货单列表</a> - 修改退货单','body' => $body));
	}
	// 
	function doupdateReturn(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			if($this->kclass->input['returnno']==''){
				$e.='<li>请填写退货的 退货单号</li>';
			}
			if($this->kclass->input['itemid']==''){
				$e.='<li>请选择退货的 物资</li>';
			}
			if($this->kclass->input['reason']==''){
				$e.='<li>请填写退货的 理由</li>';
			}
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '新增退货单',
				'text' => '新增退货单<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$this->kclass->DB->query("
			UPDATE `return` SET 
				itemid='".$this->kclass->input['itemid']."',
				reason='".$this->kclass->input['reason']."',
				remark='".$this->kclass->input['remark']."',
				modified='".TIMENOW."',
				modifier='".$this->kclass->user['userid']."'
			WHERE returnid='".$this->kclass->input['returnid']."'
		");

		$this->kclass->messager(array(
			'title' => '修改退货单',
			'text' => '退货单 <b>'.$this->kclass->input['returnno'].'</b> 已修改成功!返回退货详情页',
			'url' => '/s.php?module='.$this->kclass->input['m'].'&action=view&'.$this->kclass->input['m'].'id='.$this->kclass->input['mid'],
			'sec' => 2
		));
	}
	// 
	function killReturn(){
		if($this->kclass->input['returnid']<=0){
			$this->kclass->boinkIt('/s.php?module=service&action=return');
		}
		if($this->kclass->input['rt']!='list' AND $this->kclass->input['confirm'] == 0){
			$this->kclass->boinkIt('/s.php?module=service&action=return&returnid='.$this->kclass->input['returnid']);
		}
		if($this->kclass->input['returnid'] < 0){
			$e = '<li>请从正确的页面进行操作，如果此问题持续存在，请联系网站管理员。</li>';
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '删除应付',
				'text' => '您在删除应付的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		if($this->kclass->input['rt']=='view'){
			$rt = '&returnid='.$this->kclass->input['returnid'];
		}elseif($this->kclass->input['rt']=='list'){
			$rt = '#'.$this->kclass->input['returnid'];
		}
		$return=$this->kclass->DB->queryFirst("
			SELECT returnno
			FROM `return`
			WHERE returnid='".$this->kclass->input['returnid']."'
		");
		if($return){
			$this->kclass->DB->query("
				UPDATE `return`
				SET killed=".TIMENOW.",killer='".$this->kclass->user['userid']."'
				WHERE returnid='".$this->kclass->input['returnid']."'
			");
			$this->kclass->messager(array(
				'title' => '删除应付成功',
				'text' => '应付 <b>'.$return['title'].'</b> 已成功被标记为删除!',
				'url' => REFERER,
				'sec' => 2
			));
		}else{
			$this->kclass->messager(array(
				'title' => '删除应付失败',
				'text' => '您要删除的应付，不存在！',
				'url' => REFERER,
				'sec' => 3
			));
		}
	}
}

?>