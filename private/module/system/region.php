<?php
class region{
	function autoRun(){
		$this->kclass->page['title'] = '地区与币种';
		$this->baseurl = '<a href="/s.php">首页</a> - <a href="/s.php?module=region">地区与币种</a>';
		$this->right = '<a href="/s.php?module=region">列表</a> <a href="/s.php?module=region&action=add">新建</a>';
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
			case 'province':
				$this->province();
			break;
			case 'city':
				$this->city();
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
			if($this->kclass->input['title'] == ''){
				$e = '<li>请填写地区与币种的 名称。</li>';
			}else{
				$this->kclass->input['title'] = trim($this->kclass->input['title']);
				if($this->kclass->input['provinceid']>0 AND $region = $this->kclass->DB->queryFirst("SELECT regionid FROM `region` WHERE provinceid='".$this->kclass->input['provinceid']."' AND `city`='".$this->kclass->input['title']."'")){
					$e = '<li>您要修改的地区与币种 【'.$this->kclass->input['title'].'】 已经存在。</li>';
				}elseif($this->kclass->input['countryid']>0 AND $region = $this->kclass->DB->queryFirst("SELECT regionid FROM `region` WHERE countryid='".$this->kclass->input['countryid']."' AND provinceid=0 AND `state`='".$this->kclass->input['title']."'")){
					$e = '<li>您要修改的地区与币种 【'.$this->kclass->input['title'].'】 已经存在。</li>';
				}elseif($region = $this->kclass->DB->queryFirst("SELECT regionid FROM `region` WHERE `regionid`<>'".$this->kclass->input['regionid']."' AND countryid=0 AND `country`='".$this->kclass->input['title']."'")){
					$e = '<li>您要修改的地区与币种 【'.$this->kclass->input['title'].'】 已经存在。</li>';
				}
			}
			if($this->kclass->input['provinceid'] == -1 OR $this->kclass->input['countryid'] == -1){
				$e .= '<li>请选择地区与币种的 上级区域。</li>';
			}
			if($this->kclass->input['abbr'] == ''){
				$e .= '<li>请填写地区与币种的 中文名称。</li>';
			}
			if($this->kclass->input['entitle'] == ''){
				$e .= '<li>请填写地区与币种的 英文名称。</li>';
			}
			if($this->kclass->input['areacode'] == ''){
				$e .= '<li>请填写地区与币种的 电话区号（例如中国是“+86”）。</li>';
			}
			if($this->kclass->input['timezone'] == ''){
				$e .= '<li>请填写地区与币种的 时区（例如中国是“+8”）。</li>';
			}
			if($this->kclass->input['currency'] == ''){
				$e .= '<li>请填写地区与币种的 中文币种（且小于等于10个字符）。</li>';
			}
			if($this->kclass->input['encurrency'] == ''){
				$e .= '<li>请填写地区与币种的 英文币种（且小于等于10个字符）。</li>';
			}
			if(strlen($this->kclass->input['code']) < 6){
				$e .= '<li>请填写地区与币种的 币种代码（且小于等于5个字符）。</li>';
			}
			if($this->kclass->input['symbol'] == ''){
				$e .= '<li>请填写地区与币种的 币种符号（且小于等于10个字符）。</li>';
			}
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '新建地区与币种',
				'text' => '您在新建地区与币种的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		if($this->kclass->input['provinceid']>0){
			$region = $this->kclass->DB->queryFirst("SELECT * FROM `region` WHERE regionid='".$this->kclass->input['provinceid']."'");
			$tp = $this->kclass->DB->queryFirst("SELECT COUNT(regionid) AS t FROM `region` WHERE countryid='".$region['countryid']."' AND provinceid=0");
			$tc = $this->kclass->DB->queryFirst("SELECT COUNT(regionid) AS t FROM `region` WHERE provinceid='".$this->kclass->input['provinceid']."'");
			$countryid = $region['countryid'];
			$country=$region['country'];
			$encountry=$region['encountry'];
			$abbr=$region['abbr'];
			$provinceid = $region['regionid'];
			$pnum=$tp['t'];
			$state=$region['state'];
			$enstate=$region['enstate'];
			$sabbr=$region['sabbr'];
			$cnum=$tc['t'];
			$city = $this->kclass->input['title'];
			$encity = $this->kclass->input['entitle'];
			$cabbr = $this->kclass->input['abbr'];
		}elseif($this->kclass->input['countryid']>0){
			$region = $this->kclass->DB->queryFirst("SELECT * FROM `region` WHERE regionid='".$this->kclass->input['countryid']."'");
			$tp = $this->kclass->DB->queryFirst("SELECT COUNT(regionid) AS t FROM `region` WHERE countryid='".$this->kclass->input['countryid']."' AND provinceid=0");
			$countryid = $region['regionid'];
			$country=$region['country'];
			$encountry=$region['encountry'];
			$abbr=$region['abbr'];
			$pnum=$tp['t'];
			$state = $this->kclass->input['title'];
			$enstate = $this->kclass->input['entitle'];
			$sabbr = $this->kclass->input['abbr'];
			$cnum=0;
		}else{
			$country = $this->kclass->input['title'];
			$encountry = $this->kclass->input['entitle'];
			$abbr = $this->kclass->input['abbr'];
		}
		
		$this->kclass->DB->query("
		INSERT INTO `region` (`country`, `encountry`, `abbr`, `countryid`, `pnum`, `state`, `enstate`, `sabbr`, `provinceid`, `cnum`, `city`, `encity`, `cabbr`, `areaid`,`areacode`, `timezone`, `currency`, `encurrency`, `code`, `symbol`, `remark`, `ordering`, `modified`, `creator`, `created`) 
		VALUES (
		'".$country."', 
		'".$encountry."', 
		'".$abbr."', 
		'".$countryid."', 
		'".$pnum."', 
		'".$state."', 
		'".$enstate."', 
		'".$sabbr."', 
		'".$provinceid."', 
		'".$cnum."', 
		'".$city."', 
		'".$encity."', 
		'".$cabbr."', 
		'".$this->kclass->input['areaid']."', 
		'".$this->kclass->input['areacode']."', 
		'".$this->kclass->input['timezone']."', 
		'".$this->kclass->input['currency']."', 
		'".$this->kclass->input['encurrency']."', 
		'".$this->kclass->input['code']."', 
		'".$this->kclass->input['symbol']."', 
		'".$this->kclass->input['remark']."', 
		'".$this->kclass->input['ordering']."', '".TIMENOW."', '".$this->kclass->user['userid']."', '".TIMENOW."'
		)");
		if($this->kclass->input['provinceid']>0){
			$this->kclass->DB->query("UPDATE SET cnum=(cnum+1) WHERE provinceid='".$this->kclass->input['provinceid']."'");
		}elseif($this->kclass->input['countryid']>0){
			$this->kclass->DB->query("UPDATE SET pnum=(pnum+1) WHERE countryid='".$this->kclass->input['countryid']."'");
		}
		$this->kclass->messager(array(
			'title' => '新建地区与币种',
			'text' => '地区与币种 <b>'.$this->kclass->input['country'].'</b> 已新建成功!',
			'url' => '/s.php?module=region#'.$this->kclass->DB->insertID(),
			'sec' => 3
		));
	}

	//
	function add(){
		if($this->kclass->input['provinceid']>0){
			$r = $this->kclass->DB->queryFirst("SELECT countryid FROM `region` WHERE regionid='".$this->kclass->input['provinceid']."'");
			$area = '<select name="provinceid"><option value="-1"> </option>';
			$cs = $this->kclass->DB->query("SELECT * FROM region WHERE countryid='".$r['countryid']."' AND provinceid=0 ORDER BY enstate ASC");
			if($this->kclass->DB->numRows()){
				while($c=$this->kclass->DB->fetchArray($cs)){
					$area .= '<option value="'.$c['regionid'].'"';
					if($c['regionid']==$this->kclass->input['provinceid']){
						$area .= ' selected';
						$areaTitle = '<span class="darkred">'.$c['country'].'('.$c['encountry'].') - '.$c['state'].'('.$c['enstate'].')</span> 的下级区域';
					}
					$area .= '>'.$c['state'].'【'.$c['enstate'].'】('.$c['cnum'].')</option>';
				}
			}
			$area .= '</select>';
		}elseif($this->kclass->input['countryid']>0){
			$area = '<select name="countryid"><option value="-1"> </option>';
			$cs = $this->kclass->DB->query("SELECT * FROM region WHERE countryid=0 ORDER BY encountry ASC");
			if($this->kclass->DB->numRows()){
				while($c=$this->kclass->DB->fetchArray($cs)){
					$area .= '<option value="'.$c['regionid'].'"';
					if($c['regionid'] == $this->kclass->input['countryid']){
						$area .= ' selected';
						$areaTitle = '<span class="darkred">'.$c['country'].'('.$c['encountry'].')</span> 的下级区域';
					}
					$area .= '>'.$c['country'].'【'.$c['encountry'].'】('.$c['pnum'].')</option>';
				}
			}
			$area .= '</select>';
		}else{
			$areaTitle = '最上级 <span class="darkred">国家和地区</span>';
		}
		if($area != ''){
			$area = '<tr class="odd"><td>上级区域：<span class="red bold">*</span></td><td colspan="3">'.$area.'</td></tr>';
		}
		$areas = $this->kclass->chooserArea(array('name'=>'areaid','width'=>103));
$body = <<<EOF
<form action="/s.php?module=region&action=insert" name="region" method="post">
<input type="hidden" name="module" value="region">
<input type="hidden" name="action" value="insert">
<table><thead>
<tr>
	<th colspan="4">新建地区与币种：{$areaTitle}</th>
</tr>
</thead>
<tbody>
{$area}
<tr class="odd">
	<td>名称<span class="small gray">(中文)</span>：<span class="red bold">*</span></td>
	<td><input type="text" style="width:350px" size="26" name="title" value="{$title}"></td>
	<td>名称<span class="small gray">(英文)</span>：<span class="red bold">*</span></td>
	<td><input type="text" style="width:100px" size="16" name="entitle" value="{$entitle}"> 简称：<span class="red bold">*</span><input type="text" style="width:60px" size="6" name="abbr" value="{$abbr}"></td>
</tr>
<tr class="even">
	<td>币种<span class="small gray">(中文)</span>：<span class="red bold">*</span></td>
	<td><input type="text" style="width:350px" size="26" name="currency" value="{$region['currency']}"></td>
	<td>电话区号：<span class="red bold">*</span></td>
	<td><input type="text" style="width:100px" size="7" name="areacode" value="{$region['areacode']}"> 时区：<span class="red bold">*</span><input type="text" style="width:60px" size="6" name="timezone" value="{$region['timezone']}"></td>
</tr>
<tr class="odd">
	<td>币种<span class="small gray">(英文)</span>：<span class="red bold">*</span></td>
	<td><input type="text" style="width:350px" size="26" name="encurrency" value="{$region['encurrency']}"></td>
	<td>币种代码：<span class="red bold">*</span></td>
	<td><input type="text" style="width:100px" size="7" name="code" value="{$region['code']}"> 符号：<span class="red bold">*</span><input type="text" style="width:60px" size="6" name="symbol" value="{$region['symbol']}"></td>
</tr>
<tr class="even">
	<td>备　　注：</td>
	<td><input type="text" style="width:350px" size="32" name="remark" value="{$region['remark']}"></td>
	<td>所属区域：<span class="red bold">*</span></td>
	<td>{$areas} 排序：<span class="red bold">*</span><input type="text" style="width:60px" size="26" name="ordering" value="1"></td>
</tr>
<tr class="odd">
	<td align="center" colspan="4">
		<input type="submit" id="submitButton" value="      保存      " accesskey="s">　　
		<input type="reset" value="      复位      ">
	</td>
</tr>
</tbody>
</table>
</form>
EOF;
		$this->kclass->page['onload'] = 'dc.tabhover()';
		$this->kclass->page['title'] .= ' - 新建地区与币种';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 新建地区与币种', 'right' => '<a href="/s.php?module=region">返回列表</a>', 'body'=>$body));
}

	//
	function doupdate(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			if($this->kclass->input['title'] == ''){
				$e = '<li>请填写地区与币种的 中文名称。</li>';
			}else{
				if($this->kclass->input['provinceid']>0 AND $region = $this->kclass->DB->queryFirst("SELECT regionid FROM `region` WHERE `regionid`<>'".$this->kclass->input['regionid']."' AND provinceid='".$this->kclass->input['provinceid']."' AND `city`='".$this->kclass->input['title']."'")){
					$e = '<li>您要修改的地区与币种 【'.$this->kclass->input['title'].'】 已经存在。</li>';
				}elseif($this->kclass->input['countryid']>0 AND $region = $this->kclass->DB->queryFirst("SELECT regionid FROM `region` WHERE `regionid`<>'".$this->kclass->input['regionid']."' AND countryid='".$this->kclass->input['countryid']."' AND provinceid=0 AND `state`='".$this->kclass->input['title']."'")){
					$e = '<li>您要修改的地区与币种 【'.$this->kclass->input['title'].'】 已经存在。</li>';
				}elseif($region = $this->kclass->DB->queryFirst("SELECT regionid FROM `region` WHERE `regionid`<>'".$this->kclass->input['regionid']."' AND countryid=0 AND `country`='".$this->kclass->input['title']."'")){
					$e = '<li>您要修改的地区与币种 【'.$this->kclass->input['title'].'】 已经存在。</li>';
				}
			}
			if($this->kclass->input['abbr'] == ''){
				$e .= '<li>请填写地区与币种的 中文名称。</li>';
			}
			if($this->kclass->input['entitle'] == ''){
				$e .= '<li>请填写地区与币种的 英文名称。</li>';
			}
			if($this->kclass->input['areacode'] == ''){
				$e .= '<li>请填写地区与币种的 电话区号（例如中国是“+86”）。</li>';
			}
			if($this->kclass->input['timezone'] == ''){
				$e .= '<li>请填写地区与币种的 时区（例如中国是“+8”）。</li>';
			}
			if($this->kclass->input['currency'] == ''){
				$e .= '<li>请填写地区与币种的 中文币种（且小于等于10个字符）。</li>';
			}
			if($this->kclass->input['encurrency'] == ''){
				$e .= '<li>请填写地区与币种的 英文币种（且小于等于10个字符）。</li>';
			}
			if($this->kclass->input['code']=='' OR strlen($this->kclass->input['code']) < 6){
				$e .= '<li>请填写地区与币种的 币种代码（且小于等于5个字符）。</li>';
			}
			if($this->kclass->input['symbol'] == ''){
				$e .= '<li>请填写地区与币种的 币种符号（且小于等于10个字符）。</li>';
			}
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '修改地区与币种',
				'text' => '您在修改地区与币种的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$region = $this->kclass->DB->queryFirst("SELECT regionid,countryid,provinceid FROM `region` WHERE `regionid`='".$this->kclass->input['regionid']."'");
		if($region['provinceid']>0){
			$set = "`provinceid`='".$this->kclass->input['provinceid']."', `city`='".$this->kclass->input['title']."', `encity`='".$this->kclass->input['entitle']."', `cabbr`='".$this->kclass->input['abbr']."'";
		}elseif($region['countryid']>0){
			$set = "`countryid`='".$this->kclass->input['countryid']."', `state`='".$this->kclass->input['title']."', `enstate`='".$this->kclass->input['entitle']."', `sabbr`='".$this->kclass->input['abbr']."'";
		}else{
			$set = "`country`='".$this->kclass->input['title']."', `encountry`='".$this->kclass->input['entitle']."', `abbr`='".$this->kclass->input['abbr']."'";
		}
		
		$this->kclass->DB->query("
			UPDATE `region`
			SET ".$set.", 
				`areaid` = '".$this->kclass->input['areaid']."',
				`areacode` = '".$this->kclass->input['areacode']."', 
				`timezone` = '".$this->kclass->input['timezone']."', 
				`currency` = '".$this->kclass->input['currency']."', 
				`encurrency` = '".$this->kclass->input['encurrency']."', 
				`code` = '".$this->kclass->input['code']."', 
				`symbol` = '".$this->kclass->input['symbol']."', 
				`remark` = '".$this->kclass->input['remark']."', 
				`ordering` = '".$this->kclass->input['ordering']."', 
				`modified` = '".TIMENOW."', 
				`modifier` = '".$this->kclass->user['userid']."'
			WHERE regionid='".$this->kclass->input['regionid']."'
		");
		if($this->kclass->input['options']==1){ //同时更新下级区域
			if($region['countryid']==0){//国家下级
				$this->kclass->DB->query("
					UPDATE `region`
					SET `timezone` = '".$this->kclass->input['timezone']."', 
					`areaid` = '".$this->kclass->input['areaid']."', 
					`currency` = '".$this->kclass->input['currency']."', 
					`encurrency` = '".$this->kclass->input['encurrency']."', 
					`code` = '".$this->kclass->input['code']."', 
					`symbol` = '".$this->kclass->input['symbol']."', 
					`modified` = '".TIMENOW."', 
					`modifier` = '".$this->kclass->user['userid']."'
					WHERE countryid='".$region['regionid']."'
				");
			}elseif($region['provinceid']==0){//省市下级
				$this->kclass->DB->query("
					UPDATE `region`
					SET `timezone` = '".$this->kclass->input['timezone']."', 
					`areaid` = '".$this->kclass->input['areaid']."', 
					`currency` = '".$this->kclass->input['currency']."', 
					`encurrency` = '".$this->kclass->input['encurrency']."', 
					`code` = '".$this->kclass->input['code']."', 
					`symbol` = '".$this->kclass->input['symbol']."', 
					`modified` = '".TIMENOW."', 
					`modifier` = '".$this->kclass->user['userid']."'
					WHERE provinceid='".$region['regionid']."'
				");
				$rurl = '&action=province&countryid='.$region['countryid'];
			}else{
				$rurl = '&action=city&provinceid='.$region['provinceid'];
			}
		}
		$this->kclass->messager(array(
			'title' => '修改地区与币种',
			'text' => '地区与币种 <b>'.$this->kclass->input['country'].'</b> 的信息已成功修改!',
			'url' => '/s.php?module=region'.$rurl.'#'.$this->kclass->input['regionid'],
			'sec' => 3
		));
	}

	//
	function update(){
		if($this->kclass->input['regionid']<=0 OR !$region = $this->kclass->DB->queryFirst("SELECT `region`.* FROM `region` WHERE `regionid`='".$this->kclass->input['regionid']."'")){
			$this->kclass->boinkIt('/s.php?module=region');
		}
		if($region['provinceid']>0){
			$area = '<select name="provinceid">';
			$cs = $this->kclass->DB->query("SELECT regionid, state, enstate, cnum FROM region WHERE countryid='".$region['countryid']."' AND provinceid=0 ORDER BY enstate ASC");
			if($this->kclass->DB->numRows()){
				while($c=$this->kclass->DB->fetchArray($cs)){
					$area .= '<option value="'.$c['regionid'].'"';
					if($c['regionid']==$region['provinceid']){
						$area .= ' selected';
					}
					$area .= '>'.$c['state'].'【'.$c['enstate'].'】('.$c['cnum'].')</option>';
				}
			}
			$area .= '</select>';
			$title = $region['city'];
			$entitle = $region['encity'];
			$abbr = $region['sabbr'];
		}elseif($region['countryid']>0){
			$area = '<select name="countryid">';
			$cs = $this->kclass->DB->query("SELECT regionid, country, encountry, pnum FROM region WHERE countryid=0 ORDER BY encountry ASC");
			if($this->kclass->DB->numRows()){
				while($c=$this->kclass->DB->fetchArray($cs)){
					$area .= '<option value="'.$c['regionid'].'"';
					if($c['regionid'] == $region['countryid']){
						$area .= ' selected';
					}
					$area .= '>'.$c['country'].'【'.$c['encountry'].'】('.$c['pnum'].')</option>';
				}
			}
			$area .= '</select>';
			$title = $region['state'];
			$entitle = $region['enstate'];
			$abbr = $region['sabbr'];
		}else{
			$title = $region['country'];
			$entitle = $region['encountry'];
			$abbr = $region['abbr'];
		}
		if($area != ''){
			$area = '<tr class="odd"><td>上级区域：<span class="red bold">*</span></td><td colspan="3">'.$area.'</td></tr>';
		}
		$areas = $this->kclass->chooserArea(array('name'=>'areaid','selectedid'=>$region['areaid'],'width'=>103));
		$areaTitle = $region['country'].$this->kclass->iif($region['state']!='', ' - '.$region['state'].$this->kclass->iif($region['city']!='', ' - '.$region['city'], ''), '');
		switch($this->kclass->myGetcookie('updateRegionOptions')){
			case 1:
				$o1 = ' checked';
				break;
			case 2:
				$o2 = ' checked';
				break;
			default:
				$o1 = ' checked';
				break;
		}
$body = <<<EOF
<form action="/s.php?module=region&action=doupdate" name="region" method="post">
<input type="hidden" name="module" value="region">
<input type="hidden" name="action" value="doupdate">
<input type="hidden" name="regionid" value="{$this->kclass->input['regionid']}">
<table><thead>
<tr>
	<th colspan="4">地区与币种 修改：<span class="darkred">{$areaTitle}</span></th>
</tr>
</thead>
<tbody>
{$area}
<tr class="odd">
	<td>名称<span class="small gray">(中文)</span>：<span class="red bold">*</span></td>
	<td><input type="text" style="width:350px" size="26" name="title" value="{$title}"></td>
	<td>名称<span class="small gray">(英文)</span>：<span class="red bold">*</span></td>
	<td><input type="text" style="width:100px" size="16" name="entitle" value="{$entitle}"> 简称：<span class="red bold">*</span><input type="text" style="width:60px" size="6" name="abbr" value="{$abbr}"></td>
</tr>
<tr class="even">
	<td>币种<span class="small gray">(中文)</span>：<span class="red bold">*</span></td>
	<td><input type="text" style="width:350px" size="26" name="currency" value="{$region['currency']}"></td>
	<td>电话区号：<span class="red bold">*</span></td>
	<td><input type="text" style="width:100px" size="7" name="areacode" value="{$region['areacode']}"> 时区：<span class="red bold">*</span><input type="text" style="width:60px" size="6" name="timezone" value="{$region['timezone']}"></td>
</tr>
<tr class="odd">
	<td>币种<span class="small gray">(英文)</span>：<span class="red bold">*</span></td>
	<td><input type="text" style="width:350px" size="26" name="encurrency" value="{$region['encurrency']}"></td>
	<td>币种代码：<span class="red bold">*</span></td>
	<td><input type="text" style="width:100px" size="7" name="code" value="{$region['code']}"> 符号：<span class="red bold">*</span><input type="text" style="width:60px" size="6" name="symbol" value="{$region['symbol']}"></td>
</tr>
<tr class="even">
	<td>备　　注：</td>
	<td><input type="text" style="width:350px" size="32" name="remark" value="{$region['remark']}"></td>
	<td>所属区域：<span class="red bold">*</span></td>
	<td>{$areas} 排序：<span class="red bold">*</span><input type="text" style="width:60px" size="26" name="ordering" value="{$region['ordering']}"></td>
</tr>
<tr class="odd">
	<td>选　　项：</td>
	<td colspan="3">
		同时将时区、币种、币种代码、符号设定到下级省州区域：<label for="o0" class="green"><input type="radio" id="o0" name="options" value="1"{$o1}>是</label> 
		<label for="o1" class="red"><input type="radio" id="o1" name="options" value="0"{$o2}>否</label>
	</td>
</tr>
<tr class="even">
	<td align="center" colspan="4">
		<input type="submit" id="submitButton" value="      保存      " accesskey="s">　　
		<input type="reset" value="      复位      ">
	</td>
</tr>
</tbody>
</table>
</form>
EOF;
		$this->kclass->page['onload'] = 'dc.tabhover()';
		$this->kclass->page['title'] .= ' - 修改地区与币种 - '.$region['country'];
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 修改地区与币种 - '.$region['country'], 'right' => '<a href="/s.php?module=region">返回列表</a>', 'body'=>$body));
	}
	//
	function kill(){
		if($this->kclass->input['regionid']<=0){
			$this->kclass->boinkIt('/s.php?module=region');
		}
		if($this->kclass->input['confirm'] == 0){
			$this->kclass->boinkIt('/s.php?module=region');
		}
		if($this->kclass->input['regionid'] < 0){
			$e = '<li>请从正确的页面进行操作，如果此问题持续存在，请联系网站管理员。';
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '删除地区与币种',
				'text' => '您在删除地区与币种的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$region = $this->kclass->DB->queryFirst("
			SELECT country
			FROM region
			WHERE regionid='".$this->kclass->input['regionid']."'
		");
		if($region){
			$this->kclass->DB->query("
				UPDATE `region`
				SET killed=1
				WHERE regionid='".$this->kclass->input['regionid']."'
			");
			$this->kclass->messager(array(
				'title' => '删除地区与币种成功',
				'text' => '地区与币种 <b>'.$region['country'].'</b> 已成功被标记为删除!',
				'url' => '/s.php?module=region',
				'sec' => 3
			));
		}else{
			$this->kclass->messager(array(
				'title' => '删除地区与币种失败',
				'text' => '您要删除的地区与币种，不存在！',
				'url' => '/s.php?module=region#'.$this->kclass->input['regionid'],
				'sec' => 3
			));
		}
	}

	//
	function remove(){
		if($this->kclass->input['regionid']<=0){
			$this->kclass->boinkIt('/s.php?module=region');
		}
		$region = $this->kclass->DB->queryFirst("
			SELECT country
			FROM region
			WHERE regionid='".$this->kclass->input['regionid']."'
		");
$body = <<<EOF
<form action="/s.php?module=region&action=kill" name="region" method="post">
<input type="hidden" name="module" value="region">
<input type="hidden" name="action" value="kill">
<input type="hidden" name="regionid" value="{$this->kclass->input['regionid']}">
<table><thead>
<tr>
	<td>删除地区与币种：{$region['country']}</td>
</tr>
</thead>
<tbody>
<tr class="even">
	<td class="middle">你确定要删除地区与币种: <a href="/s.php?module=region&action=view&regionid={$this->kclass->input['regionid']}" class="big bold" target="_blank">{$region['country']}</a> 吗?</td>
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
		$this->kclass->page['title'] .= ' - 删除地区与币种 - '.$region['country'];
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 删除地区与币种 - '.$region['country'], 'right' => '<a href="/s.php?module=region">返回列表</a>', 'body'=>$body));
	}

	//
	function revival(){
		if($this->kclass->input['regionid']<=0){
			$this->kclass->boinkIt('/s.php?module=region');
		}
		if($this->kclass->input['confirm'] == 0){
			$this->kclass->boinkIt('/s.php?module=region');
		}
		if($this->kclass->input['regionid'] < 0){
			$e = '<li>请从正确的页面进行操作，如果此问题持续存在，请联系网站管理员。';
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '恢复地区与币种',
				'text' => '您在恢复地区与币种的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$region = $this->kclass->DB->queryFirst("
			SELECT country
			FROM region
			WHERE regionid='".$this->kclass->input['regionid']."'
		");
		if($region){
			$this->kclass->DB->query("
				UPDATE `region`
				SET killed=0
				WHERE regionid='".$this->kclass->input['regionid']."'
			");
			$this->kclass->messager(array(
				'title' => '恢复地区与币种成功',
				'text' => '地区与币种 <b>'.$region['country'].'</b> 已成功被恢复！',
				'url' => '/s.php?module=region',
				'sec' => 3
			));
		}else{
			$this->kclass->messager(array(
				'title' => '恢复地区与币种失败',
				'text' => '您要恢复的地区与币种不存在！',
				'url' => '/s.php?module=region#'.$this->kclass->input['regionid'],
				'sec' => 3
			));
		}
	}

	//
	function restore(){
		if($this->kclass->input['regionid']<=0){
			$this->kclass->boinkIt('/s.php?module=region');
		}
		$region = $this->kclass->DB->queryFirst("
			SELECT country
			FROM region
			WHERE regionid='".$this->kclass->input['regionid']."'
		");
$body = <<<EOF
<form action="/s.php?module=region&action=revival" name="region" method="post">
<input type="hidden" name="module" value="region">
<input type="hidden" name="action" value="revival">
<input type="hidden" name="regionid" value="{$this->kclass->input['regionid']}">
<table><thead>
<tr>
	<th>恢复地区与币种：{$region['country']}</th>
</tr>
</thead>
<tbody>
<tr class="even">
	<td class="middle">你确定要恢复地区与币种: <a href="/s.php?module=region&action=view&regionid={$this->kclass->input['regionid']}" class="big bold" target="_blank">{$region['country']}</a> 吗?</td>
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
		$this->kclass->page['title'] .= ' - 恢复地区与币种 - '.$region['country'];
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 恢复地区与币种 - '.$region['country'], 'right' => '<a href="/s.php?module=region">返回列表</a>', 'body'=>$body));
	}
	// 
	function city(){
		$bs = $this->kclass->DB->query("
			SELECT *
			FROM `region`
			WHERE provinceid='".$this->kclass->input['provinceid']."'
			ORDER BY enstate ASC, modified DESC, created DESC
		");
		if($counter = $this->kclass->DB->numRows()){
			$body = '<table><thead><tr><th colspan="2">市县<span class="small"> (共'.$counter.'个)</span></th><th>电话区号</th><th>时区</th><th align="center">修改日期</th><th align="center" class="small">操作</th></tr></thead><tbody>';
			$countryid=$country=$province='';
			
			$i=1;
			while($b = $this->kclass->DB->fetchArray($bs)){
				if($country==''){
					$country=$b['country'];
					$countryid=$b['countryid'];
				}
				if($province=='')$province=$b['state'];
				$body .= '<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even');
				$body .= $this->kclass->iif($b['killed']==1, ' through', '').'"><td align="right" class="small">'.$i.'</td><td><a name="'.$b['regionid'].'"></a><span class="small bold">'.$b['cabbr'].'</span> '.$b['city'].'<br />'.$b['encity'].'</td><td>'.$b['areacode'].'</td><td>'.$b['timezone'].'</td><td class="tiny gray" title="'.date('Y-m-d H:i:s', $b['modified']).'">'.date('y-m-d H:i', $b['modified']).'</td>';
				if($b['killed']==1){
					$body .= '<td><a href="/s.php?module=region&action=restore&regionid='.$b['regionid'].'">恢</a></td>';
				}else{
					$body .= '<td><a href="/s.php?module=region&action=remove&regionid='.$b['regionid'].'" class="small">删</a> <a href="/s.php?module=region&action=update&regionid='.$b['regionid'].'">改</a></td></tr>';
				}
				$i++;
			}
			$body .= '</tbody></table>';
		}else{
			$body = '暂无相关记录。';
		}

		$this->kclass->page['title'] .= ' - '.$country. ' - '.$province;
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - <a href="/s.php?module=region&action=province&countryid='.$countryid.'">'.$country.'</a> - '.$province, 'right' => $this->right, 'body'=>$body));
	}
	// 
	function province(){
		$bs = $this->kclass->DB->query("
			SELECT *
			FROM `region`
			WHERE provinceid=0 AND countryid='".$this->kclass->input['countryid']."'
			ORDER BY enstate ASC, modified DESC, created DESC
		");
		if($counter = $this->kclass->DB->numRows()){
			$body = '<table id="mytable"><thead><tr><th colspan="2"><span class="right small gray">市县数</span>省市名称<span class="small"> (共'.$counter.'个)</span></th><th>电话区号</th><th>时区</th><th align="center">修改日期</th><th align="center" class="small">操作</th></tr></thead><tbody>';
			$country='';
			$i=1;
			while($b = $this->kclass->DB->fetchArray($bs)){
				if($country==''){
					$country=$b['country'];
				}
				$body .= '<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even');
				$body .= $this->kclass->iif($b['killed']==1, ' through', '').'"><td align="right" class="small">'.$i.'</td><td><span class="right tiny">'.$b['cnum'].'</span><a name="'.$b['regionid'].'"></a>'.$this->kclass->iif($b['cnum']>1, '<a href="/s.php?module=region&action=city&provinceid='.$b['regionid'].'"><span class="small bold">'.$b['sabbr'].'</span> '.$b['state'].' <br />'.$b['enstate'].'</a>', '<span class="small bold">'.$b['sabbr'].'</span> '.$b['state'].'<br />'.$b['enstate']).'</td><td>'.$b['areacode'].'</td><td>'.$b['timezone'].'</td><td class="tiny gray" title="'.date('Y-m-d H:i:s', $b['modified']).'">'.date('y-m-d H:i', $b['modified']).'</td>';
				if($b['killed']==1){
					$body .= '<td><a href="/s.php?module=region&action=restore&regionid='.$b['regionid'].'">恢</a></td>';
				}else{
					$body .= '<td><a href="/s.php?module=region&action=remove&regionid='.$b['regionid'].'" class="small">删</a> <a href="/s.php?module=region&action=update&regionid='.$b['regionid'].'">改</a></td></tr>';
				}
				$i++;
			}
			$body .= '</tbody></table>';
		}else{
			$body = '暂无相关记录。';
		}

		$this->kclass->page['title'] .= ' - '.$country;
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - '.$country, 'right' => $this->right, 'body'=>$body));
	}
	// 
	function mmlist(){
		$bs = $this->kclass->DB->query("
			SELECT *
			FROM `region`
			WHERE countryid=0
			ORDER BY encountry ASC, modified DESC, created DESC
		");
		if($counter = $this->kclass->DB->numRows()){
			$head = '<thead><tr><th colspan="2"><span class="right small">省州数</span>国家名称<span class="small"> (共'.$counter.'个)</span></th><th>电话区号</th><th>时区</th><th>币种</th><th>代码</th><th>符号</th><th align="center">修改日期</th><th align="center" class="small">操作</th></tr></thead><tbody>';
			$body = '<table class="eightypercent mytable">'.$head;
			$i=1;
			while($b = $this->kclass->DB->fetchArray($bs)){
				//if($i%21==0)$body.=$head;
				$body .= '<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even');
				$body .= $this->kclass->iif($b['killed']==1, ' through', '').'"><td align="right" class="small bold">'.$i.'</td><td width="300"><span class="right tiny">'.$b['pnum'].'</span><a name="'.$b['regionid'].'"></a>'.$this->kclass->iif($b['pnum']>0, '<a href="/s.php?module=region&action=province&countryid='.$b['regionid'].'"><span class="small bold">'.$b['abbr'].'</span> '.$b['country'].'<br />'.$b['encountry'].'</a>', '<span class="small bold">'.$b['abbr'].'</span> '.$b['country'].'<br />'.$b['encountry']).'　　<span class="small bold">'.$b['abbr2'].'</span> </td><td>'.$b['areacode'].'</td><td>'.$b['timezone'].'</td><td class="small">'.$b['currency'].'</td><td>'.$b['code'].'</td><td>'.$b['symbol'].'</div></td><td class="tiny gray" title="'.date('Y-m-d H:i:s', $b['modified']).'">'.date('y-m-d H:i', $b['modified']).'</td>';
				if($b['killed']==1){
					$body .= '<td><a href="/s.php?module=region&action=restore&regionid='.$b['regionid'].'">恢</a></td>';
				}else{
					$body .= '<td><a href="/s.php?module=region&action=remove&regionid='.$b['regionid'].'" class="small">删</a> <a href="/s.php?module=region&action=update&regionid='.$b['regionid'].'">改</a></td></tr>';
				}
				$i++;
			}
			$body .= '</tbody></table>';
		}else{
			$body = '暂无相关记录。';
		}

		$this->kclass->page['onload'] = 'dc.tabhover();$(\'.mytable\').fixedtableheader();';
		$this->kclass->page['title'] .= ' - 列表';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 列表', 'right' => '<a href="/s.php?module=region&action=add">新建国家</a>', 'body'=>$body));
	}
}
?>