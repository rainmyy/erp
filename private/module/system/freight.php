<?php
//
class freight{
	var $kclass;
	function autoRun(){
		$this->baseurl='<a href="/s.php?">首页</a>';
		switch($this->kclass->input['action']){
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
			case 'kill':
				return $this->kill();
				break;
			default:
				$this->mmlist();
		}
	}
	
	function add(){
		$url = REFERER;
		if($this->kclass->input['orderid']>0){
			$module = 	'order';
			$mid = 	$this->kclass->input['orderid'];
		}else if($this->kclass->input['sampleid']>0){
			$module = 	'sample';
			$mid = 	$this->kclass->input['sampleid'];
		}else{
			$this->kclass->messager(array(
				'title' => '新建货运信息',
				'text' => '货运参数不正确!',
				'url' => $url,
				'sec' => 2
			));
		}
		$upload = $this->kclass->upload(array('title'=>'相关附件：'));
		$str = <<<EOD
<form action="/s.php?module=freight&action=insert" method="post">
<input type="hidden" name="module" value="freight">
<input type="hidden" name="action" value="insert">
<input type="hidden" name="url" value="{$url}">		
<input type="hidden" name="modules" value="{$module}">		
<input type="hidden" name="mid" value="{$mid}">				
<table class="hundred">
<thead>
<tr>
	<th colspan="6">货运信息</th>
</tr>
</thead>
<tbody>
<tr class="even">
	<td>货运单号：</td>
	<td colspan="3"><input type="text" name="expressNo" value="" style="width:350px;"></td>
</tr>
<tr class="odd">
	<td>箱　　　　数：</td>
	<td><input type="text" name="carton" value="" style="width:350px;"></td>
	<td>重　　　　量：</td>
	<td><input type="text" name="weight" value="" style="width:350px;"></td>
</tr>
<tr class="even">
	<td>质　检 OQC：</td>
	<td><input type="text" name="oqc" value="" style="width:350px;"></td>
 	<td>发&nbsp;&nbsp;货&nbsp;&nbsp;&nbsp;确&nbsp;&nbsp;认：</td>
 	<td><input type="text" name="shippingVerify" value="" style="width:350px;"></td>
		
</tr>
<tr class="odd">
 	<td>提货司机姓名：</td>
	<td><input type="text" name="driverName" value="" style="width:350px;"></td>
	<td>司机电话号码：</td>
	<td><input type="text" name="driverMobile" value="" style="width:350px;"></td>
		
</tr>
<tr class="even">
 	<td>车　牌　号：</td>
	<td><input type="text" name="carID" value="" style="width:350px;"></td>
	<td>司机身份证号：</td>
	<td><input type="text" name="driverID" value="" style="width:350px;"></td>
</tr>
<tr class="odd">
	<td valign="top">司&nbsp;&nbsp;机&nbsp;&nbsp;&nbsp;确&nbsp;&nbsp;认：</td>
	<td><input type="text" name="driverVerify" value="" style="width:350px;"></td>
	<td valign="top">货&nbsp;&nbsp;运&nbsp;&nbsp;&nbsp;公&nbsp;&nbsp;司：</td>
	<td><input type="text" name="freightCompany" value="" style="width:350px;"></td>
</tr>
<tr class="even">
	<td align="center" colspan="6">
		<input type="submit" id="submitButton" value="      保存      " accesskey="s">　　
		<input type="reset" value="      复位      ">
	</td>
</tr>
	
</tbody>
</table>

{$upload}
EOD;
		$body .= $str;
		$this->kclass->page['onload'] = 'dc.tabhover()';
		$this->kclass->page['title'] .= ' - 新建货运信息';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 新建货运信息', 'body'=>$body));
	}
	
	function insert(){	
		if($this->kclass->input['requestMethod'] != 'post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}
		$this->kclass->DB->query("
			INSERT INTO `freight` ( `module` ,`mid` ,`expressNo` ,`carton` ,   `weight` , `oqc` , `shippingVerify` ,`driverName` ,`driverMobile` ,`carID` ,`driverID` ,`driverVerify` ,`freightCompany` , `modified`, `creator` , `created`)
			VALUES (
			'".$this->kclass->input['modules']."','".$this->kclass->input['mid']."','".$this->kclass->input['expressNo']."','".$this->kclass->input['carton']."', '".$this->kclass->input['weight']."', '".$this->kclass->input['oqc']."', '".$this->kclass->input['shippingVerify']."','".$this->kclass->input['driverName']."','".$this->kclass->input['driverMobile']."','".$this->kclass->input['carID']."','".$this->kclass->input['driverID']."','".$this->kclass->input['driverVerify']."','".$this->kclass->input['freightCompany']."', '".TIMENOW."', '".$this->kclass->user['userid']."', '".TIMENOW."'
			)
		");
		$freightid = $this->kclass->DB->insertID();
		$this->kclass->updateAttachs(array('module'=>'freight', 'mid'=>$freightid));
		$url = $this->kclass->input['url'];
		$this->kclass->messager(array(
				'title' => '新建货运信息',
				'text' => '货运信息 已新建成功!',
				'url' => $url,
				'sec' => 2
		));
		
	}
	
	function update(){
		$url = REFERER;
		if($this->kclass->input['orderid']>0){
			$module = 	'order';
			$mid = 	$this->kclass->input['orderid'];
		}else if($this->kclass->input['sampleid']>0){
			$module = 	'sample';
			$mid = 	$this->kclass->input['sampleid'];
		}else{
			$this->kclass->messager(array(
				'title' => '更新货运信息',
				'text' => '货运参数不正确!',
				'url' => $url,
				'sec' => 2
			));
		}
		$freight = $this->kclass->DB->queryFirst("SELECT * FROM `freight` WHERE module='".$module."' AND mid='".$mid."'");
		$upload = $this->kclass->upload(array('title'=>'相关附件：', 'module'=>'freight', 'mid'=>$freight['freightid']));
		$str = <<<EOD
<form action="/s.php?module=freight&action=insert" method="post">
<input type="hidden" name="module" value="freight">
<input type="hidden" name="action" value="doupdate">
<input type="hidden" name="url" value="{$url}">	
<input type="hidden" name="modules" value="{$module}">		
<input type="hidden" name="mid" value="{$mid}">		
<table style="width:800px;">
<thead>
<tr>
	<th colspan="6">货运信息</th>
</tr>
</thead>
<tbody>
<tr class="even">
	<td>货运单号：</td>
	<td colspan="3"><input type="text" name="expressNo" value="{$freight['expressNo']}" style="width:350px;"></td>
</tr>
<tr class="odd">
	<td>箱　　　　数：</td>
	<td><input type="text" name="carton" value="{$freight['carton']}" style="width:350px;"></td>
	<td>重　　　　量：</td>
	<td><input type="text" name="weight" value="{$freight['weight']}" style="width:350px;"></td>
</tr>
<tr class="even">
	<td>质　检 OQC：</td>
	<td><input type="text" name="oqc" value="{$freight['oqc']}" style="width:350px;"></td>
 	<td>发&nbsp;&nbsp;货&nbsp;&nbsp;&nbsp;确&nbsp;&nbsp;认：</td>
 	<td><input type="text" name="shippingVerify" value="{$freight['shippingVerify']}" style="width:350px;"></td>
		
</tr>
<tr class="odd">
 	<td>提货司机姓名：</td>
	<td><input type="text" name="driverName" value="{$freight['driverName']}" style="width:350px;"></td>
	<td>司机电话号码：</td>
	<td><input type="text" name="driverMobile" value="{$freight['driverMobile']}" style="width:350px;"></td>
		
</tr>
<tr class="even">
 	<td>车　牌　号：</td>
	<td><input type="text" name="carID" value="{$freight['carID']}" style="width:350px;"></td>
	<td>司机身份证号：</td>
	<td><input type="text" name="driverID" value="{$freight['driverID']}" style="width:350px;"></td>
</tr>
<tr class="odd">
	<td valign="top">司&nbsp;&nbsp;机&nbsp;&nbsp;&nbsp;确&nbsp;&nbsp;认：</td>
	<td><input type="text" name="driverVerify" value="{$freight['driverVerify']}" style="width:350px;"></td>
	<td valign="top">货&nbsp;&nbsp;运&nbsp;&nbsp;&nbsp;公&nbsp;&nbsp;司：</td>
	<td><input type="text" name="freightCompany" value="{$freight['freightCompany']}" style="width:350px;"></td>
</tr>
<tr class="even">
	<td align="center" colspan="6">
		<input type="submit" id="submitButton" value="      保存      " accesskey="s">　　
		<input type="reset" value="      复位      ">
	</td>
</tr>
</tbody>
</table>
		
{$upload}
EOD;
		$body .= $str;
		$this->kclass->page['onload'] = 'dc.tabhover()';
		$this->kclass->page['title'] .= ' - 修改货运信息';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 修改货运信息', 'body'=>$body));
	}
	
	function doupdate(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}
		
		$this->kclass->DB->query("
			UPDATE `freight`
			SET `expressNo` = '".$this->kclass->input['expressNo']."',
				`carton` = '".$this->kclass->input['carton']."',
				`weight` = '".$this->kclass->input['weight']."',
				`oqc` = '".$this->kclass->input['oqc']."',
				`shippingVerify` = '".$this->kclass->input['shippingVerify']."',
				`driverName` = '".$this->kclass->input['driverName']."',
				`driverMobile` = '".$this->kclass->input['driverMobile']."',
				`carID` = '".$this->kclass->input['carID']."',
				`driverID` = '".$this->kclass->input['driverID']."',
				`driverVerify` = '".$this->kclass->input['driverVerify']."',
				`freightCompany` = '".$this->kclass->input['freightCompany']."',
				`modified` = '".TIMENOW."',
				`modifier` = '".$this->kclass->user['userid']."'
			WHERE module='".$this->kclass->input['modules']."' AND mid='".$this->kclass->input['mid']."'
		");
		$freight = $this->kclass->DB->queryFirst("SELECT * FROM `freight` WHERE module='".$this->kclass->input['modules']."' AND mid='".$this->kclass->input['mid']."'");
		$this->kclass->updateAttachs(array('module'=>'freight', 'mid'=>$freight['freightid']));
		$url = $this->kclass->input['url'];
		$this->kclass->messager(array(
				'title' => '修改货运信息',
				'text' => '货运信息已成功修改!',
				'url' => $url,
				'sec' => 2
		));
	}
	
	function kill(){
		if($this->kclass->input['orderid']>0){
			$this->kclass->DB->query("
				DELETE FROM `freight` 
				WHERE module='order' AND mid='".$this->kclass->input['orderid']."'
			");
			$this->kclass->messager(array(
					'title' => '删除货运信息',
					'text' => '删除货运信息成功',
					'url' => REFERER,
					'sec' => 2
			));
		}elseif($this->kclass->input['sampleid']>0){
			$this->kclass->DB->query("
				DELETE FROM `freight`
				WHERE module = 'sample' AND mid='".$this->kclass->input['sampleid']."'
			");
			$this->kclass->messager(array(
					'title' => '删除货运信息',
					'text' => '删除货运信息成功',
					'url' => REFERER,
					'sec' => 2
			));
		}else{
			$this->kclass->messager(array(
					'title' => '删除货运信息',
					'text' => '删除货运信息失败,没有正确的参数',
					'url' => REFERER,
					'sec' => 2
			));
		}
	}
}
?>