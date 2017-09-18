<?php
//
class accounting{
	//
	var $kclass;
	function autorun(){
		$this->baseurl='<a href="/s.php?">首页</a>';
		switch($this->kclass->input['action']){
			case 'add':
				return $this->add();
			break;
			case 'list':
				return $this->mmlist();
			break;
			case 'creditnote':
				return $this->creditnote();//应收
			break;
			case 'addCreditnote':
				return $this->addCreditnote();
			break;
			case 'insertCreditnote':
				return $this->insertCreditnote();
			break;
			case 'updateCreditnote':
				return $this->updateCreditnote();
			break;
			case 'doupdateCreditnote':
				return $this->doupdateCreditnote();
			break;
			case 'killCreditnote':
				return $this->killDebitnote();
			break;
			case 'restoreCreditnote':
				return $this->restoreCreditnote();
			break;
			case 'debitnote':
				return $this->debitnote();//应付
			break;
			case 'addDebitnote':
				return $this->addDebitnote();
			break;
			case 'insertDebitnote':
				return $this->insertDebitnote();
			break;
			case 'updateDebitnote':
				return $this->updateDebitnote();
			break;
			case 'doupdateDebitnote':
				return $this->doupdateDebitnote();
			break;
			case 'removeDebitnote':
				return $this->removeDebitnote();
			break;
			case 'killDebitnote':
				return $this->killDebitnote();
			break;
			case 'restoreDebitnote':
				return $this->restoreDebitnote();
			break;
			case 'revivalDebitnote':
				return $this->revivalDebitnote();
			break;
			case 'payment'://已付
				return $this->payment();
			break;
			case 'addPayment':
				return $this->addPayment();
			break;
			case 'insertPayment':
				return $this->insertPayment();
			break;
			case 'updatePayment':
				return $this->updatePayment();
			break;
			case 'doupdatePayment':
				return $this->doupdatePayment();
			break;
			case 'removePayment':
				return $this->removePayment();
			break;
			case 'killPayment':
				return $this->killPayment();
			break;
			case 'restorePayment':
				return $this->restorePayment();
			break;
			case 'revivalPayment':
				return $this->revivalPayment();
			break;
		}
	}
	//
	function mmlist(){
$body=<<<EOF
<span>暂无信息</span>
<a href="/s.php?module=accounting&action=add">添加选项</a>
EOF;

		$this->kclass->page['title'].='新建选项';
		$this->kclass->page['onload'].='dc.tabhover();';
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - 新建选项','body'=>$body));
	}
	//
	function add(){
$body=<<<EOF
<div class="center big" style="height:200px;margin-top:100px;">
<form action="" method="get">
	<input type="hidden" name="module" value="accounting">
	<select name="action">
		<option value="addDebitnote">应付</option>
		<option value="addPayment">已付</option>
		<option value="addCreditnote">应收</option>
		<option value="addReceipt">已收</option>
	</select>
	<input type="submit" value="提交">
</form>
</div>
EOF;

		$this->kclass->page['title'].='新建选项';
		$this->kclass->page['onload'].='dc.tabhover();';
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - 新建选项','body'=>$body));

	}
	// 
	function creditnote(){
		
	}
	// 
	function addCreditnote(){

	}
	// 
	function insertCreditnote(){
		
	}
	// 
	function updateCreditnote(){
		
	}
	// 
	function doupdateCreditnote(){
		
	}
	// 
	function killCreditnote(){
		
	}
	// 
	function restoreCreditnote(){
		
	}
	//
	function addDebitnote(){
		if($this->kclass->input['orderid'] AND $this->kclass->input['porderid']){
			$this->kclass->boinkIt('/s.php');
		}elseif(!$this->kclass->input['orderid'] AND !$this->kclass->input['porderid']){
			if(!$this->kclass->input['type']){
				$body=<<<EOF
<table style="width:450px;">
<thead><tr><th colspan=2>新建应付</th></tr></thead>
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
<thead><tr><th colspan=2>新建应付</th></tr></thead>
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
			if($this->kclass->input['orderid']){
				$module='order';
				$id=$this->kclass->input['orderid'];
			}elseif($this->kclass->input['porderid']){
				$module='porder';
				$id=$this->kclass->input['porderid'];
			}

			$order=$this->kclass->DB->queryFirst("
				SELECT o.".$module."id,o.orderno,o.currencyid,supplierid
				FROM `".$module."` AS o
				WHERE o.killed=0 AND o.".$module."id='".$id."'
				LIMIT 0,1
			");
			//订单总金额
			$item=$this->kclass->DB->queryFirst("SELECT SUM(amount) AS amount FROM item WHERE killed=0 AND module='".$module."' AND mid='".$id."' LIMIT 0,1");
			//应付金额
			$debitnote=$this->kclass->DB->queryFirst("SELECT amount FROM debitnote WHERE killed=0 AND module='".$module."' AND mid='".$id."' ORDER BY created DESC LIMIT 0,1");
			$currency=$this->kclass->chooserCurrency(array('name'=>'currencyid','hasBlank'=>1,'topname'=>'请选择','selectedid'=>$order['currencyid']));
			// 已付金额
			$payment=$this->kclass->DB->queryFirst("
				SELECT SUM(amount) AS amount
				FROM payment
				WHERE killed=0 AND module='".$module."' AND mid='".$id."'
				LIMIT 0,1
			");
			$payment['amount']=$this->kclass->iif($payment['amount'],$payment['amount'],0);
			$amount=$this->kclass->iif($debitnote['amount'],$debitnote['amount'],$item['amount']);

			$body=<<<EOF
<form action="/s.php?module=accounting&action=insertDebitnote" method="post">
<input type="hidden" name="module" value="accounting">
<input type="hidden" name="action" value="insertDebitnote">
<input type="hidden" name="mid" value="{$id}">
<input type="hidden" name="m" value="{$module}">
<input type="hidden" name="supplierid" value="{$order['supplierid']}">
<input type="hidden" name="orderno" value="{$order['orderno']}">
<table class="hundred">
<thead><tr><th colspan="4">新建应付信息</th></tr></thead>
<tbody>
<tr class="odd">
	<td width="100">订　　单：<span class="red bold">*</span></td><td width="475"><a href="/s.php?module={$module}&action=view&{$module}id={$id}">{$order['orderno']}</a></td>
	<td width="100">标　　题：<span class="red bold">*</span></td><td><input type="text" name="title" style="width:250px;" /></td>
</tr>
<tr class="even">
	<td>应付金额总数：</td><td>{$amount}</td>
	<td>已付金额总数：</td><td>{$payment['amount']}</td>
</tr>
<tr class="odd">
	<td>币　　种：<span class="red bold">*</span></td><td>{$currency}</td>
	<td>应付金额：<span class="red bold">*</span></td><td><input type="text" name="amount" /></td>
</tr>
<tr class="even">
	<td>备　　注：</td><td><textarea name="remark" style="width:444px;height:111px;"></textarea></td>
	<td></td><td></td>
</tr>
</tbody>
</table>
<table class="tlist tsuite" align="center" width="100%">
	<tbody>
		<tr class="odd" nohover>
		<td class="center" colspan=4><input type="submit" id="submitButton" value="  提交  " accesskey="s"><input type="reset" value="  复位  "/></td>
		</tr>
	</tbody>
</table>
</form>
EOF;
		}

		$this->kclass->page['title'].='新建应付';
		$this->kclass->page['onload'].='dc.tabhover();';
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - 新建应付','right'=>'<a href="/s.php?module=accounting&action=debitnote">返回列表</a>','body'=>$body));
	}
	//
	function insertDebitnote(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			if($this->kclass->input['title']==''){
				$e.='<li>请填写应付的 标题</li>';
			}
			if($this->kclass->input['currencyid']<=0){
				$e.='<li>请选择应付的 币种</li>';
			}
			if($this->kclass->input['amount']==''){
				$e.='<li>请填写应付的 应付金额</li>';
			}
		}
		if($e){
			$this->kclass->messager(array(
				'title' => '新建应付',
				'text' => '您在新建应付的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}

		$this->kclass->DB->query("
			INSERT INTO debitnote
				(module,mid,supplierid,title,amount,currencyid,remark,created,creator)
			VALUES
				('".$this->kclass->input['m']."','".$this->kclass->input['mid']."','".$this->kclass->input['supplierid']."','".$this->kclass->input['title']."','".$this->kclass->input['amount']."','".$this->kclass->input['currencyid']."','".$this->kclass->input['remark']."','".TIMENOW."','".$this->kclass->user['userid']."')
		");
		$debitnoteid=$this->kclass->DB->insertID();

		$this->kclass->messager(array(
			'title' => '新建应付',
			'text' => '订单<b>'.$this->kclass->input['orderno'].'</b>应付 已新建成功!返回查看应付详细',
			'url' => '/s.php?module='.$this->kclass->input['m'].'&action=view&'.$this->kclass->input['m'].'id='.$this->kclass->input['mid'],
			'sec' => 2
		));
	}
	//
	function updateDebitnote(){
		$debitnote=$this->kclass->DB->queryFirst("SELECT * FROM debitnote WHERE debitnoteid='".$this->kclass->input['debitnoteid']."' LIMIT 0,1");
		if(!$debitnote){
			$e='<li>数据错误，很抱歉！</li>';
		}
		if($e){
			$this->kclass->messager(array(
				'title' => '修改应付',
				'text' => '您在新建订单的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}

		$order=$this->kclass->DB->queryFirst("
			SELECT o.orderno,o.currencyid
			FROM `".$debitnote['module']."` AS o
			WHERE o.killed=0 AND o.".$debitnote['module']."id='".$debitnote['mid']."'
			LIMIT 0,1
		");
		//订单总金额
		$item=$this->kclass->DB->queryFirst("SELECT SUM(amount) AS amount FROM item WHERE killed=0 AND module='".$debitnote['module']."' AND mid='".$debitnote['mid']."' LIMIT 0,1");
		//应付金额
		$amount=$this->kclass->DB->queryFirst("SELECT amount FROM debitnote WHERE killed=0 AND module='".$debitnote['module']."' AND mid='".$debitnote['mid']."' ORDER BY created DESC LIMIT 0,1");
		$currency=$this->kclass->chooserCurrency(array('name'=>'currencyid','hasBlank'=>1,'topname'=>'请选择','selectedid'=>$debitnote['currencyid']));
		// 已付金额
		$payment=$this->kclass->DB->queryFirst("
			SELECT SUM(amount) AS amount
			FROM payment
			WHERE killed=0 AND module='".$debitnote['module']."' AND mid='".$debitnote['mid']."'
			LIMIT 0,1
		");
		$payment['amount']=$this->kclass->iif($payment['amount'],$payment['amount'],0);
		$amount=$this->kclass->iif($amount['amount'],$amount['amount'],$item['amount']);

		$body=<<<EOF
<form action="/s.php?module=accounting&action=doupdateDebitnote" method="post">
<input type="hidden" name="module" value="accounting">
<input type="hidden" name="action" value="doupdateDebitnote">
<input type="hidden" name="mid" value="{$debitnote['mid']}">
<input type="hidden" name="m" value="{$debitnote['module']}">
<input type="hidden" name="supplierid" value="{$debitnote['supplierid']}">
<input type="hidden" name="orderno" value="{$order['orderno']}">
<input type="hidden" name="debitnoteid" value="{$debitnote['debitnoteid']}">
<table class="hundred">
<thead></tr><th colspan="4">修改应付信息</th></tr></thead>
<tbody>
<tr class="odd">
<td width="100">订单：<span class="red bold">*</span></td><td width="475"><a href="/s.php?module={$module}&action=view&{$module}id={$id}">{$order['orderno']}</a></td>
<td width="100">标题：<span class="red bold">*</span></td><td><input type="text" name="title" style="width:250px;" value="{$debitnote['title']}" /></td>
</tr>
<tr class="even">
<td>应付金额总数：</td><td>{$amount}</td>
<td>已付金额总数：</td><td>{$payment['amount']}</td>
</tr>
<tr class="odd">
<td>订单金额：</td><td>{$item['amount']}</td>
<td>币　　种：<span class="red bold">*</span></td><td>{$currency}</td>
</tr>
<tr class="even">
<td>应付金额：<span class="red bold">*</span></td><td><input type="text" name="amount" value="{$debitnote['amount']}" /></td>
<td>备　　注：</td><td><textarea name="remark" style="width:444px;height:111px;">{$debitnote['remark']}</textarea></td>
</tr>
</tbody>
</table>
<table class="tlist tsuite" align="center" width="100%">
<tbody>
<tr class="odd" nohover>
<td class="center" colspan=4><input type="submit" id="submitButton" value="  提交  " accesskey="s"><input type="reset" value="  复位  "/></td>
</tr>
</tbody>
</table>
</form>
EOF;
		$this->kclass->page['title'].='修改应付';
		$this->kclass->page['onload'].='dc.tabhover();';
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - '.$order['orderno'].' - 修改应付','body'=>$body));
	}
	//
	function doupdateDebitnote(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			if($this->kclass->input['title']==''){
				$e.='<li>请填写应付的 标题</li>';
			}
			if($this->kclass->input['currencyid']<=0){
				$e.='<li>请选择应付的 币种</li>';
			}
			if($this->kclass->input['amount']==''){
				$e.='<li>请填写应付的 应付金额</li>';
			}
		}
		if($e){
			$this->kclass->messager(array(
				'title' => '修改应付',
				'text' => '您在修改应付的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}

		$this->kclass->DB->query("
			UPDATE debitnote SET
				title='".$this->kclass->input['title']."',
				amount='".$this->kclass->input['amount']."',
				currencyid='".$this->kclass->input['currencyid']."',
				remark='".$this->kclass->input['remark']."',
				modified='".TIMENOW."',
				modifier='".$this->kclass->user['userid']."'
			WHERE debitnoteid='".$this->kclass->input['debitnoteid']."'
		");
		$this->kclass->messager(array(
			'title' => '修改应付',
			'text' => '订单 <b>'.$this->kclass->input['orderno'].'</b> 应付 已修改成功!返回查看应付详细',
			'url' => '/s.php?module='.$this->kclass->input['m'].'&action=view&'.$this->kclass->input['m'].'id='.$this->kclass->input['mid'],
			'sec' => 2
		));
	}
	//
	function killDebitnote(){
		if($this->kclass->input['debitnoteid']<=0){
			$this->kclass->boinkIt('/s.php?module=accounting&action=debitnote');
		}
		if($this->kclass->input['rt']!='list' AND $this->kclass->input['confirm'] == 0){
			$this->kclass->boinkIt('/s.php?module=accounting&action=debitnote&debitnoteid='.$this->kclass->input['debitnoteid']);
		}
		if($this->kclass->input['debitnoteid'] < 0){
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
			$rt = '&debitnoteid='.$this->kclass->input['debitnoteid'];
		}elseif($this->kclass->input['rt']=='list'){
			$rt = '#'.$this->kclass->input['debitnoteid'];
		}
		$debitnote=$this->kclass->DB->queryFirst("
			SELECT title
			FROM debitnote
			WHERE debitnoteid='".$this->kclass->input['debitnoteid']."'
		");
		if($debitnote){
			$this->kclass->DB->query("
				UPDATE `debitnote`
				SET killed=".TIMENOW.",killer='".$this->kclass->user['userid']."'
				WHERE debitnoteid='".$this->kclass->input['debitnoteid']."'
			");
			$this->kclass->messager(array(
				'title' => '删除应付成功',
				'text' => '应付 <b>'.$debitnote['title'].'</b> 已成功被标记为删除!',
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
	function removeDebitnote(){
		if($this->kclass->input['debitnoteid']<=0){
			$this->kclass->boinkIt('/s.php?module=debitnote');
		}
		$debitnote = $this->kclass->DB->queryFirst("
			SELECT title
			FROM `debitnote`
			WHERE debitnoteid='".$this->kclass->input['debitnoteid']."'
		");
$body = <<<EOF
<form action="/s.php?module=accounting&action=killDebitnote" name="debitnote" method="post">
<input type="hidden" name="module" value="accounting">
<input type="hidden" name="action" value="killDebitnote">
<input type="hidden" name="rt" value="{$this->kclass->input['rt']}">
<input type="hidden" name="debitnoteid" value="{$this->kclass->input['debitnoteid']}">
<table><thead>
<thead>
<tr>
	<th>删除应付：{$debitnote['title']}</th>
</tr>
</thead>
<tbody>
<tr class="even">
	<td class="middle">你确定要删除应付: <a href="/s.php?module=accounting&action=debitnote&debitnoteid={$this->kclass->input['debitnoteid']}" class="big bold" target="_blank">{$debitnote['title']}</a> 吗?</td>
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
		$this->kclass->page['title'].=' - 删除 - '.$debitnote['title'];
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 删除应付 - '.$debitnote['title'], 'right' => '<a href="/s.php?module=accounting&action=debitnote">返回列表</a>', 'body'=>$body));
	}

	//
	function revivalDebitnote(){
		if($this->kclass->input['debitnoteid']<=0){
			$this->kclass->boinkIt('/s.php?module=accounting&action=debitnote');
		}
		if($this->kclass->input['rt']!='list' AND $this->kclass->input['confirm'] == 0){
			$this->kclass->boinkIt('/s.php?module=accounting&action=debitnote&debitnoteid='.$this->kclass->input['debitnoteid']);
		}
		if($this->kclass->input['debitnoteid'] < 0){
			$e = '<li>请从正确的页面进行操作，如果此问题持续存在，请联系网站管理员。</li>';
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '恢复应付',
				'text' => '您在恢复应付的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$debitnote = $this->kclass->DB->queryFirst("
			SELECT title
			FROM `debitnote`
			WHERE debitnoteid='".$this->kclass->input['debitnoteid']."'
		");
		if($this->kclass->input['rt']=='view'){
			$rt = '&debitnoteid='.$this->kclass->input['debitnoteid'];
		}elseif($this->kclass->input['rt']=='list'){
			$rt = '#'.$this->kclass->input['debitnoteid'];
		}
		if($debitnote){
			$this->kclass->DB->query("
				UPDATE `debitnote`
				SET killed=0,killer=0
				WHERE debitnoteid='".$this->kclass->input['debitnoteid']."'
			");
			$this->kclass->messager(array(
				'title' => '恢复应付成功',
				'text' => '应付 <b>'.$debitnote['title'].'</b> 已成功被恢复！',
				'url' => '/s.php?module=accounting&action=debitnote'.$rt,
				'sec' => 2
			));
		}else{
			$this->kclass->messager(array(
				'title' => '恢复应付失败',
				'text' => '您要恢复的应付不存在！',
				'url' => '/s.php?module=accounting&action=debitnote'.$rt,
				'sec' => 3
			));
		}
	}

	//
	function restoreDebitnote(){
		if($this->kclass->input['debitnoteid']<=0){
			$this->kclass->boinkIt('/s.php?module=accounting&action=debitnote');
		}
		$debitnote = $this->kclass->DB->queryFirst("
			SELECT title
			FROM `debitnote`
			WHERE debitnoteid='".$this->kclass->input['debitnoteid']."'
		");
$body = <<<EOF
<form action="/s.php?module=accounting&action=revivalDebitnote" name="debitnote" method="post">
<input type="hidden" name="module" value="accounting">
<input type="hidden" name="action" value="revivalDebitnote">
<input type="hidden" name="rt" value="{$this->kclass->input['rt']}">
<input type="hidden" name="debitnoteid" value="{$this->kclass->input['debitnoteid']}">
<table><thead>
<thead>
<tr>
	<th>恢复应付：{$debitnote['title']}</th>
</tr>
</thead>
<tbody>
<tr class="even">
	<td class="middle">你确定要恢复应付: <a href="/s.php?module=accounting&action=debitnote&debitnoteid={$this->kclass->input['debitnoteid']}" class="big bold" target="_blank">{$debitnote['title']}</a> 吗?</td>
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
		$this->kclass->page['title'].=' - 恢复 - '.$debitnote['title'];
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 恢复应付 - '.$debitnote['title'], 'right' => '<a href="/s.php?module=debitnote">返回列表</a>', 'body'=>$body));
	}
	//
	function payment(){
		if($this->kclass->input['paymentid']<=0){

		}else{
			$payment=$this->kclass->DB->queryFirst("
				SELECT d.*,
					o.orderid,o.orderno,
					c.title AS currency
				FROM payment AS d
				LEFT JOIN `order` AS o ON (o.orderid=d.orderid)
				LEFT JOIN `currency` AS c ON (c.currencyid=d.currencyid)
				WHERE d.killed=0 AND d.paymentid='".$this->kclass->input['paymentid']."'
				LIMIT 0,1
			");
			$payment['dateline']=date('Y-m-d',$payment['dateline']);
$body=<<<EOF
<table class="hundred">
<thead></tr><th colspan="4">新建应付信息</th></tr></thead>
<tbody>
<tr class="odd">
	<td width="80">订单号：</td><td><a href="/s.php?module=order&action=view&orderid={$payment['orderid']}">{$payment['orderno']}</a></td>
	<td width="80">标题：</td><td>{$payment['title']}</td>
</tr>
<tr class="even">
	<td>币种：</td><td>{$payment['currency']}</td>
	<td>应付金额：</td><td>{$payment['amount']}</td>
</tr>
<tr class="odd">
	<td>付款时间：</td><td>{$payment['dateline']}</td>
	<td>备注：</td><td>{$payment['remark']}</td>
</tr>
</tbody>
</table>
EOF;

			$this->kclass->page['title'].='查看已付';
			$this->kclass->page['onload'].='dc.tabhover();';
			$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - 查看已付','body'=>$body));
		}
	}
	//
	function addPayment(){
		$supplier=$this->kclass->DB->queryFirst("SELECT supplierid FROM supplier WHERE killed=0 AND supplierid='".$this->kclass->input['supplierid']."'");
		$orders=$this->kclass->DB->query("SELECT porderid FROM porder WHERE killed=0 AND supplierid='".$supplier['supplierid']."'");
		$orderId=array();
		if($this->kclass->DB->numRows()){
			while($order=$this->kclass->DB->fetchArray($orders)){
				$orderId[]=$order['porderid'];
			}
		}
		$items=$this->kclass->DB->query("
			SELECT i.itemid,i.materialid,i.quantity AS total,i.price,i.amount, 
				m.materialno,m.title AS material,m.standard,
				u.title AS unit,
				p.porderid,p.orderno
			FROM `item` AS i
			LEFT JOIN `material` AS m ON (m.materialid=i.materialid) 
			LEFT JOIN `unit` AS u ON (u.unitid=i.unitid)
			LEFT JOIN `porder` AS p ON (p.porderid=i.mid)
			WHERE i.killed=0 AND i.module='porder' AND i.mid IN (".implode(',',$orderId).")  
			ORDER BY i.created ASC
		");
		if($this->kclass->DB->numRows()){
			$i=1;
			$itemtr='<table class="hundred"><thead></tr><th colspan="13">订单明细表</th></tr></thead><tbody><tr class="center even"><td width="15">ID</td><td width="50">订单编号</td><td>物资编号</td><td>物资名称</td><td>规格</td><td>应到数量</td><td>已到数量</td><td>总金额</td><td>已付金额</td><td>应付金额</td><td width="120">付款金额 <span class="bold red">*</span></td><td width="150">备注</td></tr>';
			while($item=$this->kclass->DB->fetchArray($items)){
				$countTracking=$this->kclass->DB->queryFirst("
					SELECT SUM(ot.quantity) AS arrivalCount 
					FROM ordertracking AS ot 
					WHERE ot.killed=0 AND ot.module='porder' AND ot.itemid='".$item['itemid']."'
					LIMIT 0,1
				");
				$amountTracking=$this->kclass->DB->queryFirst("
					SELECT SUM(ot.amount) AS amount 
					FROM ordertracking AS ot
					LEFT JOIN payment AS p ON (ot.paymentid=p.paymentid) 
					WHERE ot.killed=0 AND ot.module='porder' AND ot.itemid='".$item['itemid']."' AND p.killed=0 
					LIMIT 0,1
				");
				$itemDebitnote=$item['amount']-$amountTracking['amount'];
				if($itemDebitnote>0){
					$itemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').' small">
					<input type="hidden" name="itemId['.$i.']" value="'.$item['itemid'].'" />
					<input type="hidden" name="itemMaterialid['.$i.']" value="'.$item['materialid'].'" />
					<input type="hidden" name="itemPorderid['.$i.']" value="'.$item['porderid'].'" />
					<input type="hidden" name="itemAmount['.$i.']" value="'.$itemDebitnote.'" />
					<td>'.$i.'</td>
					<td><a href="/s.php?module=porder&action=view&porderid='.$item['porderid'].'">'.$item['orderno'].'</a></td>
					<td><a href="/s.php?module=material&action=view&materialid='.$item['materialid'].'">'.$item['materialno'].'</a></td>	
					<td>'.$item['material'].'</td>
					<td>'.$item['standard'].$item['unit'].'</td>
					<td>'.$item['total'].$item['unit'].'</td>
					<td>'.$this->kclass->iif($countTracking['arrivalCount']=='',0,$countTracking['arrivalCount']).$item['unit'].'</td>
					<td>'.$item['amount'].'</td>
					<td>'.$this->kclass->iif($amountTracking['amount']=='',0,$amountTracking['amount']).'</td>
					<td>'.$itemDebitnote.'</td>
					<td><input type="text" name="itemPayment['.$i.']" /></td>
					<td><input type="text" name="itemRemark['.$i.']" style="width:150px;" /></td></tr>';
					$i++;
				}
				$total+=$itemDebitnote;
			}
			$itemtr.='</tbody></table>';
		}
		$currency=$this->kclass->chooserCurrency(array('name'=>'currencyid','hasBlank'=>1,'topname'=>'请选择','selectedid'=>3));
$body=<<<EOF
<form action="/s.php?module=accounting&action=insertPayment" method="post">
<input type="hidden" name="module" value="accounting">
<input type="hidden" name="action" value="insertPayment">
<input type="hidden" name="supplierid" value="{$this->kclass->input['supplierid']}">
<input type="hidden" name="total" value="{$total}">
<table class="hundred">
<thead></tr><th colspan="4">新建已付信息</th></tr></thead>
<tbody>
<tr class="even">
	<td width="80">标　　题：<span class="red bold">*</span></td><td width="750"><input type="text" name="title" style="width:350px;" /></td>
	<td width="80">币　　种：<span class="red bold">*</span></td><td>{$currency}</td>
</tr>
<tr class="odd">
	<td>已付金额：</td><td><input type="text" name="amount" /></td>
	<td>付款时间：<span class="red bold">*</span></td><td><input type="text" name="dateline" id='date' /></td>
</tr>
</tbody>
</table>
{$itemtr}
<table class="tlist tsuite" align="center" width="100%">
	<tbody>
		<tr class="odd" nohover>
		<td class="center" colspan=4><input type="submit" id="submitButton" value="  提交  " accesskey="s"><input type="reset" value="  复位  "/></td>
		</tr>
	</tbody>
</table>
</form>
EOF;

		$this->kclass->page['title'].='新建已付';
		$this->kclass->page['onload'].='dc.tabhover();$(\'#date\').datepicker()';
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - 新建已付','right'=>'<a href="/s.php?module='.$module.'&action=view&'.$module.'id='.$id.'">返回订单</a>','body'=>$body));
	}
	//
	function insertPayment(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			if($this->kclass->input['title']==''){
				$e.='<li>请填写已付的 标题 </li>';
			}
			if($this->kclass->input['currencyid']<=0){
				$e.='<li>请选择已付的 币种 </li>';
			}
			if($this->kclass->input['dateline']==''){
				$e.='<li>请填写已付的 付款时间</li>';
			}
			$hasItem=0;
			$j=count($this->kclass->input['itemId']);
			for($i=1;$i<$j+1;$i++){
				if($this->kclass->input['itemId'][$i]>0 AND $this->kclass->input['itemPayment'][$i]!=''){
					$hasItem=1;
				}
			}
			if($hasItem==0){
				$e='<li>请至少填写一条付款明细。</li>';
			}
		}
		if($e){
			$this->kclass->messager(array(
				'title' => '新建已付',
				'text' => '您在新建已付的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$sd=explode('-',$this->kclass->input['dateline']);
		$dateline=mktime(0,0,0,$sd[1],$sd[2],$sd[0]);
		$this->kclass->DB->query("
			INSERT INTO payment
				(supplierid,title,amount,currencyid,dateline,remark,created,creator)
			VALUES
				('".$this->kclass->input['supplierid']."','".$this->kclass->input['title']."','".$this->kclass->input['amount']."','".$this->kclass->input['currencyid']."','".$dateline."','".$this->kclass->input['remark']."','".TIMENOW."','".$this->kclass->user['userid']."')
		");
		$paymentid=$this->kclass->DB->insertID();
		$orderId=array();
		$count=count($this->kclass->input['itemId']);
		for($m=1;$m<$count+1;$m++){
			$this->kclass->input['itemPayment'][$m]==floatval($this->kclass->input['itemPayment'][$m]);
			if($this->kclass->input['itemPayment'][$m]!=''){
				$this->kclass->DB->query("
					INSERT INTO ordertracking 
						(`module`,`mid`,`itemid`,`materialid`,`paymentid`,`amount`,`dateline`,`remark`,`created`,`creator`)
					VALUES
						('porder','".$this->kclass->input['itemPorderid'][$m]."','".$this->kclass->input['itemId'][$m]."','".$this->kclass->input['itemMaterialid'][$m]."','".$paymentid."','".$this->kclass->input['itemPayment'][$m]."','".$dateline."','".$this->kclass->input['itemRemark'][$m]."','".TIMENOW."','".$this->kclass->user['userid']."')
				");
				$amount+=$this->kclass->input['itemPayment'][$m];
				if(!in_array($this->kclass->input['itemPorderid'][$m], $orderId)){
					$orderId[] = $this->kclass->input['itemPorderid'][$m];
				}
			}
		}
		
		$debitnote=$this->kclass->input['total']-$amount;
		$this->kclass->DB->query("
			INSERT INTO debitnote 
				(`supplierid`,`paymentid`,`amount`,`currencyid`,`created`,`creator`) 
			VALUES
				('".$this->kclass->input['supplierid']."','".$paymentid."','".$debitnote."','".$this->kclass->input['currencyid']."','".TIMENOW."','".$this->kclass->user['userid']."')
		");
		// 改变付款状态
		foreach($orderId as $key=>$val){
			$item=$this->kclass->DB->queryFirst("SELECT SUM(amount) AS amount FROM item WHERE killed=0 AND module='porder' AND mid='".$val."' LIMIT 0,1");
			$payment=$this->kclass->DB->queryFirst("SELECT SUM(amount) AS amount FROM ordertracking WHERE killed=0 AND module='porder' AND mid='".$val."' LIMIT 0,1");
			if($item['amount']-$payment['amount']==0){// 已付款
				$this->kclass->DB->query("UPDATE porder SET payStatus=2 WHERE porderid='".$val."'");
			}elseif($item['amount']-$payment['amount']>0 AND $payment['amount']>0){
				$this->kclass->DB->query("UPDATE porder SET payStatus=1 WHERE porderid='".$val."'");
			}
		}

		$this->kclass->messager(array(
			'title' => '新建已付',
			'text' => '订单 <b>'.$this->kclass->input['orderno'].'</b> 已付 已新建成功!返回查看已付详细',
			'url' => '/s.php?module=psupplier&action=view&supplierid='.$this->kclass->input['supplierid'],
			'sec' => 2
		));
	}
	//
	function updatePayment(){
		$payment=$this->kclass->DB->queryFirst("
			SELECT `payment`.*,`supplier`.title AS supplier 
			FROM `payment` 
			LEFT JOIN `supplier` ON (`supplier`.supplierid=`payment`.supplierid) 
			WHERE paymentid='".$this->kclass->input['paymentid']."' 
			LIMIT 0,1
		");
		if(!$payment){
			$e='<li>数据错误，很抱歉！</li>';
		}
		if($e){
			$this->kclass->messager(array(
				'title' => '修改应付',
				'text' => '您在新建订单的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$itemid=array();
		$items=$this->kclass->DB->query("
			SELECT ot.trackingid,ot.itemid,ot.materialid,ot.amount, 
				m.materialno,m.title AS material,m.standard,
				i.quantity,i.amount AS total,i.price,
				u.title AS unit,
				p.porderid,p.orderno
			FROM `ordertracking` AS ot
			LEFT JOIN `material` AS m ON (m.materialid=ot.materialid) 
			LEFT JOIN `item` AS i ON (i.itemid=ot.itemid) 
			LEFT JOIN `unit` AS u ON (u.unitid=i.unitid) 
			LEFT JOIN `porder` AS p ON (p.porderid=ot.mid) 
			WHERE ot.killed=0 AND ot.paymentid='".$payment['paymentid']."' 
			ORDER BY ot.created ASC
		");
		if($this->kclass->DB->numRows()){
			$i=1;
			$itemtr='';
			while($item=$this->kclass->DB->fetchArray($items)){
				$tracking=$this->kclass->DB->queryFirst("SELECT SUM(quantity) AS arrivalCount,SUM(amount) AS amount FROM ordertracking WHERE killed=0 AND module='porder' AND itemid='".$item['itemid']."' LIMIT 0,1");
				$itemDebitnote=$item['total']-$tracking['amount']+$item['amount']; //该金额为上一次未付款金额 总金额-已付全部金额+上一次付款金额
				$itemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').' small">
					<input type="hidden" name="itemId['.$i.']" value="'.$item['itemid'].'" />
					<input type="hidden" name="itemTrackingid['.$i.']" value="'.$item['trackingid'].'" />
					<input type="hidden" name="itemOrderid['.$i.']" value="'.$item['porderid'].'">
					<input type="hidden" name="itemAmount['.$i.']" value="'.$itemDebitnote.'">
					<td>'.$i.'</td>
					<td><a href="/s.php?module=porder&action=view&porderid='.$item['porderid'].'">'.$item['orderno'].'</a></td>
					<td><a href="/s.php?module=material&action=view&materialid='.$item['materialid'].'">'.$item['materialno'].'</a></td>
					<td>'.$item['material'].'</td>
					<td>'.$item['standard'].'</td>
					<td>'.$item['quantity'].$item['unit'].'</td>
					<td>'.$this->kclass->iif($tracking['arrivalCount'],$tracking['arrivalCount'],0).$item['unit'].'</td>
					<td>'.$item['total'].'</td>
					<td>'.$tracking['amount'].'</td>
					<td>'.$itemDebitnote.'</td>
					<td><input type="text" name="itemPayment['.$i.']" value="'.$item['amount'].'" style="width:80px;"/></td>
					<td><input type="text" name="itemRemark['.$i.']" style="width:120px;" /></td>
					<td><input type="checkbox" name="itemKill['.$i.']" value="'.$item['itemid'].'" /></td></tr>';
				$itemId[]=$item['itemid'];
				$total+=$itemDebitnote;
				$i++;
			}
		}
		$olItems=$this->kclass->DB->query("
			SELECT i.itemid,i.mid,i.materialid,i.quantity AS total,i.price,i.amount, 
				m.materialno,m.title AS material,m.standard,
				u.title AS unit,
				p.porderid,p.orderno
			FROM `item` AS i
			LEFT JOIN `material` AS m ON (m.materialid=i.materialid) 
			LEFT JOIN `unit` AS u ON (u.unitid=i.unitid)
			LEFT JOIN `porder` AS p ON (p.porderid=i.mid)
			WHERE i.killed=0 AND i.module='porder' AND p.supplierid='".$payment['supplierid']."' AND i.itemid NOT IN (".implode(',',$itemId).")
			ORDER BY i.created ASC
		");
		if($counter=$this->kclass->DB->numRows()){
			$i=1;
			$olItemtr.='<tr class="odd">
	<td colspan="13" class="darkred">供应商 <span class="bold darkred">'.$payment['supplier'].'</span> 的其他订单下的条目明细 <span class="small gray">(共'.$counter.'个，但没有明细内容的订单不会显示)</span></td>
</tr><thead><tr class="center even"><th>ID</th><th>订单号</th><th>物资编号</th><th>物资名称</th><th>规格</th><th>应到数量</th><th>已到数量</th><th>总额</th><th>已付金额</th><th>未付金额</th><th>付款金额 <span class="bold red">*</span></th><th>备注</th><th>选</th></tr></thead><tbody>';
			while($olItem=$this->kclass->DB->fetchArray($olItems)){
				$tracking=$this->kclass->DB->queryFirst("SELECT SUM(quantity) AS arrivalCount,SUM(amount) AS amount FROM ordertracking WHERE killed=0 AND module='porder' AND itemid='".$olItem['itemid']."' LIMIT 0,1");
				$amount=$this->kclass->DB->queryFirst("SELECT trackingid,amount FROM ordertracking WHERE killed=0 AND paymentid='".$this->kclass->input['paymentid']."' AND itemid='".$olItem['itemid']."' LIMIT 0,1");
				$debitnote=$olItem['amount']-$tracking['amount']+$amount['amount']; //该金额为上一次未付款金额 总金额-已付全部金额+上一次付款金额
				if($debitnote>0){
					$olItemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').' small">
						<input type="hidden" name="olItemId['.$i.']" value="'.$olItem['itemid'].'" />
						<input type="hidden" name="olOrderid['.$i.']" value="'.$olItem['mid'].'">
						<input type="hidden" name="olMaterialid['.$i.']" value="'.$olItem['materialid'].'" />
						<input type="hidden" name="olAmount['.$i.']" value="'.$debitnote.'" />
						<td>'.$i.'</td>
						<td><a href="/s.php?module='.$payment['module'].'&action=view&'.$payment['module'].'id='.$olItem['mid'].'">'.$olItem['orderno'].'</a></td>
						<td><a href="/s.php?module=pmaterial&action=view&pmaterialid='.$olItem['pmaterialid'].'">'.$olItem['materialno'].'</a></td>
						<td>'.$olItem['material'].'</td>
						<td>'.$olItem['standard'].'</td>
						<td>'.$olItem['total'].$olItem['unit'].'</td>
						<td>'.$this->kclass->iif($tracking['arrivalCount'],$tracking['arrivalCount'],0).$olItem['unit'].'</td>
						<td>'.$olItem['amount'].'</td>
						<td>'.$tracking['amount'].'</td>
						<td>'.$debitnote.'</td>
						<td><input type="text" name="olPayment['.$i.']" value="'.$this->kclass->iif($amount['amount'],$amount['amount'],0).'" style="width:80px;"/></td>
						<td><input type="text" name="olRemark['.$i.']" style="width:120px;" /></td>
						<td><input type="checkbox" name="olSelect['.$i.']" value="'.$olItem['itemid'].'" /></td></tr>';
					$total+=$debitnote;
					$i++;
				}
			}
		}
		$payment['dateline']=date('Y-m-d',$payment['dateline']);
		$currency=$this->kclass->chooserCurrency(array('name'=>'currencyid','hasBlank'=>1,'topname'=>'请选择','selectedid'=>$payment['currencyid']));
$body = <<<EOF
<form action="/s.php?module=accounting&action=doupdatePayment" method="post">
<input type="hidden" name="module" value="accounting">
<input type="hidden" name="action" value="doupdatePayment">
<input type="hidden" name="paymentid" value="{$this->kclass->input['paymentid']}">
<input type="hidden" name="supplierid" value="{$payment['supplierid']}">
<input type="hidden" name="total" value="{$total}">
<table class="hundred">
<thead></tr><th colspan="4">修改已付信息</th></tr></thead>
<tbody>
<tr class="even">
	<td width="80">标　　题：<span class="red bold">*</span></td><td width="750"><input type="text" name="title" style="width:350px;" value="{$payment['title']}" /></td>
	<td width="80">币　　种：<span class="red bold">*</span></td><td>{$currency}</td>
</tr>
<tr class="odd">
	<td>已付金额：</td><td><input type="text" name="amount" value="{$payment['amount']}" /></td>
	<td>付款时间：<span class="red bold">*</span></td><td><input type="text" name="dateline" id='date' value="{$payment['dateline']}" /></td>
</tr>
</tbody>
</table>
<table class="hundred">
<thead>
<tr class="center even"><th width="15">ID</th><th>订单号</th><th>物资编号</th><th>物资名称</th><th>规格</th><th>应到数量</th><th>已到数量</th><th>总金额</th><th>已付金额</th><th>未付金额</th><th width="80">付款金额 <span class="bold red">*</span></th><th width="120">备注</th><th>删</th></tr>
</thead>
<tbody>
<tr>
	<td colspan="13" class="darkred">当前应付已有条目</td>
</tr>
{$itemtr}

</tbody>
{$olItemtr}
<tr class="odd" nohover>
	<td align="center" colspan="13">
		<input type="submit" id="submitButton" value="  保存  " accesskey="s">
		<input type="reset" value="  复位  ">
	</td>
</tr>
</tbody>
</table>
</form>
EOF;
		$this->kclass->page['title'] .= ' 修改已收 - '.$payment['supplier'];
		$this->kclass->page['onload'] .= 'dc.tabhover();';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 修改已收', 'right' => '<a href="/s.php?module=psupplier&action=view&psupplierid='.$payment['supplierid'].'">返回供应商'.$payment['supplier'].'</a>', 'body'=>$body));
	}
	//
	function doupdatePayment(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			if($this->kclass->input['title']==''){
				$e.='<li>请填写已付的 标题 </li>';
			}
			if($this->kclass->input['currencyid']<=0){
				$e.='<li>请选择已付的 币种 </li>';
			}
			if($this->kclass->input['amount']==''){
				$e.='<li>请填写已付的 已付金额 </li>';
			}
			if($this->kclass->input['dateline']==''){
				$e.='<li>请填写已付的 付款时间</li>';
			}
			$hasItem=0;
			$j=count($this->kclass->input['itemId']);
			for($i=1;$i<$j+1;$i++){
				if($this->kclass->input['itemKill'][$i]=='' AND $this->kclass->input['itemId'][$i]>0 AND $this->kclass->input['itemPayment'][$i]>0){
					$a=1;
				}
			}
			$k=count($this->kclass->input['olItemId']);
			for($i=1;$i<$k+1;$i++){
				if($this->kclass->input['olSelect'][$i]>0 AND $this->kclass->input['olItemId'][$i]>0 AND $this->kclass->input['olPayment'][$i]>0){
					$b=1;
				}
			}
			if($a==1 OR $b==1){
				$hasItem=1;
			}
			if($hasItem==0){
				$e='<li>请至少填写一条付款明细。</li>';
			}
		}
		if($e){
			$this->kclass->messager(array(
				'title' => '修改已付',
				'text' => '您在修改已付的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$orderId=array();
		$sd=explode('-',$this->kclass->input['dateline']);
		$dateline=mktime(0,0,0,$sd[1],$sd[2],$sd[0]);
		// update item
		$total=$amount='';
		$count1=count($this->kclass->input['itemId']);
		for($i=1;$i<$count1+1;$i++){
			if($this->kclass->input['itemKill'][$i]>0 AND $this->kclass->input['itemKill'][$i]==$this->kclass->input['itemId'][$i]){
				$this->kclass->DB->query("UPDATE `ordertracking` SET killed='".TIMENOW."',killer='".$this->kclass->user['userid']."' WHERE tracking='".$this->kclass->input['itemTrackingid']."'");
			}elseif($this->kclass->input['itemPayment'][$i]>0){
				$this->kclass->input['itemPayment'][$i]=floatval($this->kclass->input['itemPayment'][$i]);
				$this->kclass->DB->query("
					UPDATE ordertracking SET 
						`amount`='".$this->kclass->input['itemPayment'][$i]."',
						`remark`='".$this->kclass->input['itemRemark'][$i]."',
						`dateline`='".$dateline."',
						`modifier`='".$this->kclass->user['userid']."',
						`modified`='".TIMENOW."'
					WHERE trackingid='".$this->kclass->input['itemTrackingid'][$i]."'
				");
				$payment+=$this->kclass->input['itemPayment'][$i];
			}
			$total+=$this->kclass->input['itemAmount'][$i];
			if(!in_array($this->kclass->input['itemOrderid'][$i], $orderId)){
				$orderId[] = $this->kclass->input['itemOrderid'][$i];
			}
		}
		// insert item
		$count2=count($this->kclass->input['olItemId']);
		for($m=1;$m<$count2+1;$m++){
			if($this->kclass->input['olSelect'][$m]>0 AND  $this->kclass->input['olSelect'][$m]==$this->kclass->input['olItemId'][$m] AND $this->kclass->input['olPayment'][$m]>0){
				$this->kclass->input['itemPayment'][$m]=floatval($this->kclass->input['itemPayment'][$m]);
				$this->kclass->DB->query("
					INSERT INTO ordertracking 
						(`module`,`mid`,`itemid`,`materialid`,`paymentid`,`amount`,`dateline`,`remark`,`created`,`creator`)
					VALUES
						('porder','".$this->kclass->input['olOrderid'][$m]."','".$this->kclass->input['olItemId'][$m]."','".$this->kclass->input['olMaterialid'][$m]."','".$this->kclass->input['paymentid']."','".$this->kclass->input['olPayment'][$m]."','".$dateline."','".$this->kclass->input['olRemark'][$m]."','".TIMENOW."','".$this->kclass->user['userid']."')
				");
				$payment+=$this->kclass->input['olPayment'][$m];
				if(!in_array($this->kclass->input['olOrderid'][$m], $orderId)){
					$orderId[] = $this->kclass->input['olOrderid'][$m];
				}
			}
			$total+=$this->kclass->input['olAmount'][$m];
		}
		$counter=count($orderId);
		$debitnote=$total-$payment;
		$oldDebitnote=$this->kclass->DB->queryFirst("SELECT amount FROM debitnote WHERE killed=0 AND paymentid='".$this->kclass->input['paymentid']."' ORDER BY debitnoteid DESC LIMIT 0,1");
		if($oldDebitnote['amount']!=$debitnote){
			$this->kclass->DB->query("UPDATE debitnote SET `amount`='".$debitnote."',modified='".TIMENOW."',modifier='".$this->kclass->user['userid']."' WHERE paymentid='".$this->kclass->input['paymentid']."'");
		}
		
		// 改变付款状态
		foreach($orderId as $key=>$val){
			$item=$this->kclass->DB->queryFirst("SELECT SUM(amount) AS amount FROM item WHERE killed=0 AND module='porder' AND mid='".$val."' LIMIT 0,1");
			$payment=$this->kclass->DB->queryFirst("SELECT SUM(amount) AS amount FROM ordertracking WHERE killed=0 AND module='porder' AND mid='".$val."' LIMIT 0,1");
			if($item['amount']-$payment['amount']==0){// 已付款
				$this->kclass->DB->query("UPDATE porder SET payStatus=2 WHERE porderid='".$val."'");
			}elseif($item['amount']-$payment['amount']>0 AND $payment['amount']>0){
				$this->kclass->DB->query("UPDATE porder SET payStatus=1 WHERE porderid='".$val."'");
			}
		}
		// update info of payment
		$this->kclass->DB->query("
			UPDATE payment SET 
				title='".$this->kclass->input['title']."',
				currencyid='".$this->kclass->input['currencyid']."',
				amount='".$this->kclass->input['amount']."',
				dateline='".$dateline."',
				remark='".$this->kclass->input['remark']."',
				modified='".TIMENOW."',
				modifier='".$this->kclass->user['userid']."'
			WHERE paymentid='".$this->kclass->input['paymentid']."'
		");

		$this->kclass->messager(array(
			'title' => '修改已付',
			'text' => '订单 <b>'.$this->kclass->input['orderno'].'</b> 已付 已修改成功!返回查看已付详细',
			'url' => '/s.php?module=psupplier&action=view&supplierid='.$this->kclass->input['supplierid'],
			'sec' => 2
		));
	}
	//
	function removePayment(){

	}
	//
	function killPayment(){
		if($this->kclass->input['paymentid']<=0){
			$this->kclass->boinkIt('/s.php?module=accounting&action=payment');
		}
		if($this->kclass->input['rt']!='list' AND $this->kclass->input['confirm'] == 0){
			$this->kclass->boinkIt('/s.php?module=accounting&action=payment&paymentid='.$this->kclass->input['paymentid']);
		}
		if($this->kclass->input['paymentid'] < 0){
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
			$rt = '&paymentid='.$this->kclass->input['paymentid'];
		}elseif($this->kclass->input['rt']=='list'){
			$rt = '#'.$this->kclass->input['paymentid'];
		}
		$payment=$this->kclass->DB->queryFirst("
			SELECT title
			FROM payment
			WHERE paymentid='".$this->kclass->input['paymentid']."'
		");
		$amount=$this->kclass->DB->queryFirst("SELECT SUM(amount) AS amount FROM ordertracking WHERE killed=0 AND paymentid='".$this->kclass->input['paymentid']."'");
		if($payment){
			$this->kclass->DB->query("
				UPDATE `payment`
				SET killed=".TIMENOW.",killer='".$this->kclass->user['userid']."'
				WHERE paymentid='".$this->kclass->input['paymentid']."'
			");
			$this->kclass->DB->query("UPDATE debitnote SET killed='".TIMENOW."',killer='".$this->kclass->user['userid']."' WHERE paymentid='".$this->kclass->input['paymentid']."'");
			$this->kclass->messager(array(
				'title' => '删除应付成功',
				'text' => '应付 <b>'.$payment['title'].'</b> 已成功被标记为删除!',
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
	function restorePayment(){

	}
	//
	function revivalPayment(){

	}
}

?>