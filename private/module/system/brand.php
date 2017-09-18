<?php
class brand{
	function autoRun(){
		$this->kclass->page['title'] = '设置品牌';
		$this->baseurl = '<a href="/s.php">首页</a> - <a href="/s.php?module=brand">品牌</a>';
		$this->right = '<a href="/s.php?module=brand">列表</a> <a href="/s.php?module=brand&action=add">新建</a>';
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
			default:
				$this->mmlist();
		}
	}
	// 
	function view(){
		if(!$this->kclass->input['brandid'] > 0)$this->kclass->boinkIt('/s.php?module=brand');
		$brand = $this->kclass->DB->queryFirst("
			SELECT `brand`.*, 
				region.encountry AS regionEncountry, region.country AS regionCountry, region.enstate AS regionEnstate, region.state AS regionState, region.encity AS regionEncity, region.city AS regionCity, 
				m.username AS mname, c.username AS cname
			FROM `brand` 
			LEFT JOIN region ON (region.regionid=`brand`.regionid) 
			LEFT JOIN `user` AS m ON (m.userid=brand.modifier) 
			LEFT JOIN `user` AS c ON (c.userid=brand.creator) 
			WHERE brandid='".$this->kclass->input['brandid']."'
		");
		if(!$brand)	$this->kclass->boinkIt('/s.php?module=brand');
		if($brand['modifier']>0){
			$modified = '，'.$brand['mname'].' 于 '.date('y-m-d H:i:s', $brand['modified']).' 最后修改';
		}
		$created = date('y-m-d H:i:s', $brand['created']);
		if($brand['regionid']>0){
			if($brand['regionEncity']!=''){
				$region = $brand['regionEncity'].', ';
			}
			if($brand['regionEnstate']!=''){
				$region .= $brand['regionEnstate'].', <br>';
			}
			if($brand['regionEncountry']!=''){
				$region .= $brand['regionEncountry'];
			}
		}
		$attach = $this->kclass->getAttachs(array('module'=>'brand', 'mid'=>$brand['brandid']));
		if($attach!= false){
			$attachs = '</tbody><thead><tr><th colspan="4">相关附件</th></tr></thead><tbody><tr class="even"><td colspan="4">'.$attach.'</td></tr>';
		}
$body = <<<EOF
<div class="title"><span class="right small gray">由 {$brand['cname']} 于 {$created} 建立{$modified}。</span>{$brand['title']}</div>
<table>
<thead>
<tr><th colspan="4">品牌信息 - {$brand['title']}</th></tr>
</thead>
<tbody>
<tr class="odd">
	<td width="80">品　　牌：</td>
	<td class="middle">{$brand['title']}</td>
	<td>备　　注：</td>
	<td class="middle">{$brand['remark']}</td>
</tr>
<tr class="even">
	<td>所属公司：</td>
	<td class="middle">{$brand['company']}</td>
	<td>国　　家：</td>
	<td class="middle">{$region}</td>
</tr>
<tr class="odd">
	<td>网　　站：</td>
	<td colspan="3">{$brand['website']}</td>
</tr>
{$attachs}
</tbody>
</table>
EOF;
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl.' - 品牌信息', 'right' => $this->right.' | '.$this->kclass->iif($brand['killed']==1, '<td><a href="/s.php?module=brand&action=restore&brandid='.$brand['brandid'].'&rt=view">恢复</a></td>', '<td align="right"><a href="/s.php?module=brand&action=remove&brandid='.$brand['brandid'].'&rt=view">删除</a> <a href="/s.php?module=brand&action=update&brandid='.$brand['brandid'].'&rt=view">修改</a></td></tr>'), 'body'=>$body));
	}
	//
	function insert(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			if($this->kclass->input['title'] == ''){
				$e = '<li>请填写品牌的 名称。</li>';
			}else{
				if($this->kclass->DB->queryFirst("SELECT brandid FROM `brand` WHERE `title`='".$this->kclass->input['title']."'")){
					$e = '<li>您要新建的品牌 【'.$this->kclass->input['title'].'】 已经存在。</li>';
				}
			}
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '新建品牌',
				'text' => '您在新建品牌的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$this->kclass->DB->query("
			INSERT INTO `brand` ( `title` , `remark` , `company` , `countryid` , `website` , `ordering` , `modified`, `creator` , `created`)
			VALUES (
			'".$this->kclass->input['title']."', '".$this->kclass->input['remark']."', '".$this->kclass->input['company']."', '".$this->kclass->input['countryid']."', '".$this->kclass->input['website']."', '".$this->kclass->input['ordering']."', '".TIMENOW."', '".$this->kclass->user['userid']."', '".TIMENOW."'
			)
		");
		$this->kclass->insertAttachs(array('module'=>'brand', 'mid'=>$this->kclass->DB->insertID()));

		$this->kclass->messager(array(
			'title' => '新建品牌',
			'text' => '品牌 <b>'.$this->kclass->input['title'].'</b> 已新建成功!',
			'url' => '/s.php?module=brand&action='.$rurl,
			'sec' => 3
		));
	}

	//
	function add(){
		$bountry = $this->kclass->chooserRegion(array('name'=>'countryid', 'width'=>203,'topname'=>'', 'displaytop'=>1));
		$upload = $this->kclass->upload(array('title'=>'相关附件：'));
$body = <<<EOF
<form action="/s.php?module=brand&action=insert" name="brand" method="post">
<input type="hidden" name="module" value="brand">
<input type="hidden" name="action" value="insert">
<table class="tlist" align="center" width="100%">
<tr>
	<td colspan="4">品牌新建表单：</td>
</tr>
<tr class="odd">
	<td>名　　称：<span class="red bold">*</span></td>
	<td><input type="text" style="width:350px" size="26" name="title" value="{$brand['title']}"></td>
	<td valign="top">备　　注：</td>
	<td><input type="text" style="width:350px" size="26" name="remark" value="{$brand['remark']}"></td>
</tr>
<tr class="even">
	<td>所属公司：</td>
	<td><input type="text" style="width:350px" size="26" name="company" value="{$brand['company']}"></td>
	<td valign="top">国　　家：</td>
	<td>{$bountry}</td>
</tr>
<tr class="odd">
	<td>品牌网址：</td>
	<td><input type="text" style="width:350px" size="26" name="website" value="{$brand['website']}"></td>
	<td valign="top">列表排序：</td>
	<td><input type="text" style="width:100px" size="16" name="ordering" value="1"> <span class="small gray">填入整数，数字越小排位越靠前。</span></td>
</tr>
</table>
{$upload}
<table class="tlist tsuite" align="center" width="100%">
<tr class="odd">
	<td colspan="4" align="center">
		<input type="submit" id="submitButton" value="      保存      " accesskey="s">　　
		<input type="reset" value="      复位      ">
	</td>
</tr>
</table>
</form>
EOF;
		$this->kclass->tbline+=3;
		$this->kclass->page['title'] .= ' - 新建品牌';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 新建品牌', 'right' => '<a href="/s.php?module=brand">返回列表</a>', 'body'=>$body));
}

	//
	function doupdate(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			if($this->kclass->input['title'] == ''){
				$e = '<li>请填写品牌的 名称。</li>';
			}else{
				if($brand = $this->kclass->DB->queryFirst("SELECT brandid FROM `brand` WHERE `brandid`<>'".$this->kclass->input['brandid']."' AND `title`='".$this->kclass->input['title']."'")){
					$e = '<li>您要修改的品牌 【'.$this->kclass->input['title'].'】 已经存在。</li>';
				}
			}
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '新建品牌',
				'text' => '您在新建品牌的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$this->kclass->DB->query("
			UPDATE `brand`
			SET `title` = '".$this->kclass->input['title']."', 
				`remark` = '".$this->kclass->input['remark']."', 
				`company` = '".$this->kclass->input['company']."', 
				`countryid` = '".$this->kclass->input['countryid']."', 
				`website` = '".$this->kclass->input['website']."', 
				`ordering` = '".$this->kclass->input['ordering']."', 
				`modified` = '".TIMENOW."', 
				`modifier` = '".$this->kclass->user['userid']."'
			WHERE brandid='".$this->kclass->input['brandid']."'
		");
		$this->kclass->insertAttachs(array('module'=>'brand', 'mid'=>$this->kclass->input['brandid']));
		if($this->kclass->input['rt']=='view'){
			$rt = '&action=view&brandid='.$this->kclass->input['brandid'];
		}elseif($this->kclass->input['rt']=='list'){
			$rt = '#'.$this->kclass->input['brandid'];
		}
		$this->kclass->messager(array(
			'title' => '修改品牌',
			'text' => '品牌 <b>'.$this->kclass->input['title'].'</b> 的信息已成功修改!',
			'url' => '/s.php?module=brand'.$rt,
			'sec' => 3
		));
	}

	//
	function update(){
		if($this->kclass->input['brandid']<=0 OR !$brand = $this->kclass->DB->queryFirst("SELECT `brand`.* FROM `brand` WHERE `brandid`='".$this->kclass->input['brandid']."'")){
			$this->kclass->boinkIt('/s.php?module=brand');
		}
		$bountry = $this->kclass->chooserRegion(array('name'=>'countryid', 'width'=>203,'topname'=>'', 'displaytop'=>1, 'countryid'=>$brand['countryid']));
		$upload = $this->kclass->upload(array('module'=>'brand', 'mid'=>$brand['brandid']));
$body = <<<EOF
<form action="/s.php?module=brand&action=doupdate" name="brand" method="post">
<input type="hidden" name="module" value="brand">
<input type="hidden" name="action" value="doupdate">
<input type="hidden" name="brandid" value="{$this->kclass->input['brandid']}">
<table class="hundred">
<thead>
<tr>
	<th colspan="4">品牌修改：{$brand['title']}</th>
</tr>
</thead>
<tbody>
<tr class="odd">
	<td>名　　称：<span class="red bold">*</span></td>
	<td><input type="text" style="width:350px" size="26" name="title" value="{$brand['title']}"></td>
	<td valign="top">备　　注：</td>
	<td><input type="text" style="width:350px" size="26" name="remark" value="{$brand['remark']}"></td>
</tr>
<tr class="even">
	<td>所属公司：</td>
	<td><input type="text" style="width:350px" size="26" name="company" value="{$brand['company']}"></td>
	<td valign="top">国　　家：</td>
	<td>{$bountry}</td>
</tr>
<tr class="odd">
	<td>品牌网址：</td>
	<td><input type="text" style="width:350px" size="26" name="website" value="{$brand['website']}"></td>
	<td valign="top">列表排序：</td>
	<td><input type="text" style="width:100px" size="16" name="ordering" value="1"> <span class="small gray">填入整数，数字越小排位越靠前。</span></td>
</tr>
</table>
{$upload}
<table class="tlist tsuite" align="center" width="100%">
<tr class="odd">
	<td align="center">
		<input type="submit" id="submitButton" value="      保存      " accesskey="s">　　
		<input type="reset" value="      复位      ">
	</td>
</tr>
</tbody>
</table>
</form>
EOF;
		$this->kclass->tbline+=2;
		$this->kclass->page['title'] .= ' - 修改品牌 - '.$brand['title'];
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 修改品牌 - '.$brand['title'], 'right' => '<a href="/s.php?module=brand">返回列表</a>', 'body'=>$body));
	}
	//
	function kill(){
		if($this->kclass->input['brandid']<=0){
			$this->kclass->boinkIt('/s.php?module=brand');
		}
		if($this->kclass->input['confirm'] == 0){
			$this->kclass->boinkIt('/s.php?module=brand');
		}
		if($this->kclass->input['brandid'] < 0){
			$e = '<li>请从正确的页面进行操作，如果此问题持续存在，请联系网站管理员。';
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '删除品牌',
				'text' => '您在删除品牌的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		if($this->kclass->input['rt']=='view'){
			$rt = '&action=view&brandid='.$this->kclass->input['brandid'];
		}elseif($this->kclass->input['rt']=='list'){
			$rt = '#'.$this->kclass->input['brandid'];
		}
		$brand = $this->kclass->DB->queryFirst("
			SELECT title
			FROM brand
			WHERE brandid='".$this->kclass->input['brandid']."'
		");
		if($brand){
			$this->kclass->DB->query("
				UPDATE `brand`
				SET killed=1
				WHERE brandid='".$this->kclass->input['brandid']."'
			");
			$this->kclass->messager(array(
				'title' => '删除品牌成功',
				'text' => '品牌 <b>'.$brand['title'].'</b> 已成功被标记为删除!',
				'url' => '/s.php?module=brand'.$rt,
				'sec' => 3
			));
		}else{
			$this->kclass->messager(array(
				'title' => '删除品牌失败',
				'text' => '您要删除的品牌，不存在！',
				'url' => '/s.php?module=brand'.$rt,
				'sec' => 3
			));
		}
	}

	//
	function remove(){
		if($this->kclass->input['brandid']<=0){
			$this->kclass->boinkIt('/s.php?module=brand');
		}
		$brand = $this->kclass->DB->queryFirst("
			SELECT title
			FROM brand
			WHERE brandid='".$this->kclass->input['brandid']."'
		");
$body = <<<EOF
<form action="/s.php?module=brand&action=kill" name="brand" method="post">
<input type="hidden" name="module" value="brand">
<input type="hidden" name="action" value="kill">
<input type="hidden" name="brandid" value="{$this->kclass->input['brandid']}">
<table>
<thead>
<tr>
	<th>删除品牌：{$brand['title']}</th>
</tr>
</thead>
<tbody>
<tr class="odd">
	<td class="middle">你确定要删除品牌: <a href="/s.php?module=brand&action=view&brandid={$this->kclass->input['brandid']}" class="big bold" target="_blank">{$brand['title']}</a> 吗?</td>
</tr>
<tr class="even">
	<td align="center">
		<label for="c1" class="red"><input type="radio" id="c1" name="confirm" value="1">是</label>　　
		<label for="c0" class="green"><input type="radio" id="c0" name="confirm" value="0" checked>否</label>
	</td>
</tr>
<tr class="odd">
	<td align="center">
		<input type="submit" value="   保存   " accesskey="s">
		<input type="reset" value="   复位   ">
	</td>
</tr>
</tbody>
</table>
</form>
EOF;
		$this->kclass->page['title'] .= ' - 删除品牌 - '.$brand['title'];
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 删除品牌 - '.$brand['title'], 'right' => '<a href="/s.php?module=brand">返回列表</a>', 'body'=>$body));
	}

	//
	function revival(){
		if($this->kclass->input['brandid']<=0){
			$this->kclass->boinkIt('/s.php?module=brand');
		}
		if($this->kclass->input['confirm'] == 0){
			$this->kclass->boinkIt('/s.php?module=brand');
		}
		if($this->kclass->input['brandid'] < 0){
			$e = '<li>请从正确的页面进行操作，如果此问题持续存在，请联系网站管理员。';
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '恢复品牌',
				'text' => '您在恢复品牌的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$brand = $this->kclass->DB->queryFirst("
			SELECT title
			FROM brand
			WHERE brandid='".$this->kclass->input['brandid']."'
		");
		if($this->kclass->input['rt']=='view'){
			$rt = '&action=view&brandid='.$this->kclass->input['brandid'];
		}elseif($this->kclass->input['rt']=='list'){
			$rt = '#'.$this->kclass->input['brandid'];
		}
		if($brand){
			$this->kclass->DB->query("
				UPDATE `brand`
				SET killed=0
				WHERE brandid='".$this->kclass->input['brandid']."'
			");
			$this->kclass->messager(array(
				'title' => '恢复品牌成功',
				'text' => '品牌 <b>'.$brand['title'].'</b> 已成功被恢复！',
				'url' => '/s.php?module=brand'.$rt,
				'sec' => 3
			));
		}else{
			$this->kclass->messager(array(
				'title' => '恢复品牌失败',
				'text' => '您要恢复的品牌不存在！',
				'url' => '/s.php?module=brand'.$rt,
				'sec' => 3
			));
		}
	}

	//
	function restore(){
		if($this->kclass->input['brandid']<=0){
			$this->kclass->boinkIt('/s.php?module=brand');
		}
		$brand = $this->kclass->DB->queryFirst("
			SELECT title
			FROM brand
			WHERE brandid='".$this->kclass->input['brandid']."'
		");
$body = <<<EOF
<form action="/s.php?module=brand&action=revival" name="brand" method="post">
<input type="hidden" name="module" value="brand">
<input type="hidden" name="action" value="revival">
<input type="hidden" name="brandid" value="{$this->kclass->input['brandid']}">
<table>
<thead>
<tr>
	<th>恢复品牌：{$brand['title']}</th>
</tr>
</thead>
<tbody>
<tr class="odd">
	<td class="middle">你确定要恢复品牌: <a href="/s.php?module=brand&action=view&brandid={$this->kclass->input['brandid']}" class="big bold" target="_blank">{$brand['title']}</a> 吗?</td>
</tr>
<tr class="even">
	<td align="center">
		<label for="c1" class="red"><input type="radio" id="c1" name="confirm" value="1">是</label>　　
		<label for="c0" class="green"><input type="radio" id="c0" name="confirm" value="0" checked>否</label>
	</td>
</tr>
<tr class="odd">
	<td align="center">
		<input type="submit" value="   保存   " accesskey="s">
		<input type="reset" value="   复位   ">
	</td>
</tr>
</tbody>
</table>
</form>
EOF;
		$this->kclass->page['title'] .= ' - 恢复品牌 - '.$brand['title'];
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 恢复品牌 - '.$brand['title'], 'right' => '<a href="/s.php?module=brand">返回列表</a>', 'body'=>$body));
	}
	// 
	function mmlist(){
		$bs = $this->kclass->DB->query("
			SELECT *
			FROM `brand`
			ORDER BY modified DESC, created DESC
		");
		if($this->kclass->DB->numRows()){
			$body = '<table class="eightypercent mytable"><thead><tr><th>ID</th><th>名称</th><th>备注</th><th align="center">建立日期</th><th align="center">修改日期</th><th align="center">操作</th></thead></tr><tbody>';
			while($b = $this->kclass->DB->fetchArray($bs)){
				$body .= '<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even');
				$body .= $this->kclass->iif($b['killed']==1, ' through', '').'"><td><a name="'.$b['brandid'].'"></a>'.$b['brandid'].'</td><td><a href="/s.php?module=brand&action=view&brandid='.$b['brandid'].'">'.$b['title'].'</a>'.$this->kclass->iif($b['attachs']>0, ' <span class="attachFile" title="有'.$b['attachs'].'个附件。"></span>', '').$this->kclass->iif($b['images']>0, ' <span class="attachImage" title="有'.$b['images'].'个图片。"></span>', '').'</td><td>'.$b['remark'].'</td><td class="tiny">'.date('y-m-d H:i', $b['created']).'</td><td class="tiny gray">'.date('y-m-d H:i', $b['modified']).'</td>';
				if($b['killed']==1){
					$body .= '<td><a href="/s.php?module=brand&action=restore&brandid='.$b['brandid'].'&rt=list">恢</a></td>';
				}else{
					$body .= '<td><a href="/s.php?module=brand&action=remove&brandid='.$b['brandid'].'&rt=list">删</a> <a href="/s.php?module=brand&action=update&brandid='.$b['brandid'].'&rt=list">改</a></td></tr>';
				}
			}
			$body .= '</tbody></table>';
		}else{
			$body = '暂无相关记录。';
		}

		$this->kclass->page['onload'] = 'dc.tabhover();$(\'.mytable\').fixedtableheader();';
		$this->kclass->page['title'] .= ' - 列表';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 列表', 'right' => $this->right, 'body'=>$body));
	}
}
?>