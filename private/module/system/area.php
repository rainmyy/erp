<?php
/**
 * 客户区域的增删改查
 * @author mayinghao <mayinghao@wisdom.hk>
 * @version 1.0 2013-7-05
 * @copyright New Wisdom Investment Limited
 */
class area{
	# Global
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
	
	/**
	 * 客户区域列表
	 */
	function mmlist(){
		$areas = $this->kclass->DB->query("
			SELECT `area`.*
			FROM `area`
			WHERE killed = 0
			ORDER BY `area`.areaid ASC
		");
		
		if($this->kclass->DB->numRows()){
			$index = 1;
			while($area = $this->kclass->DB->fetchArray($areas)){
				$regions = $this->kclass->DB->query("
					SELECT `region`.*
					FROM `region`
					WHERE killed = 0 AND countryid=0 AND `region`.areaid='".$area['areaid']."'
					ORDER BY `region`.regionid ASC
				");
				
				$itemtr = '<tr class="even"><td><span class="bold">国家名称</span></td><td><span class="bold">省州数</span></td></tr>';
				while($region = $this->kclass->DB->fetchArray($regions)){
					$itemtr .= '<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').'">
						<td><span class="gray small">('.$region['abbr'].')</span> &nbsp;&nbsp;<a href="/s.php?module=region&action=province&countryid='.$region['regionid'].'">'.$region['country'].'<span class="small">['.$region['encountry'].']</span></a></td>
						<td>'.$region['pnum'].'</td>
					</tr>';
				}
				
				$options='<a href="/s.php?module=area&action=update&areaid='.$area['areaid'].'">修改</a> <a href="/s.php?module=area&action=kill&areaid='.$area['areaid'].'&rt=list" onclick="return confirm(\'你确定要删除这个 '.$area['title'].' 客户区域吗？\');">删除</a>';
				$body.=<<<EOF
<table class="hundred">
<thead>
<tr><th colspan="15"><span class="right">{$options} <span class="plus hand" onclick="$('#area{$area['areaid']}').toggle('fast');$(this).toggleClass('minus');"></span></span>	<span class="normal">[<b>{$index}</b>]</span>{$area[title]} <span class="small">{$area[entitle]}</span></th></tr>
</thead>
<tbody id="area{$area['areaid']}" style="display:none">
{$itemtr}
</tbody>
</table>
EOF;
				$index++;
			}
		}
		
		
		
		$this->kclass->page['onload'] .= "dc.listhover();";
		$this->kclass->page['title'] .= ' 客户区域列表';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 客户区域列表<span class="tiny">('.$sum.')</span>', 'right' => $this->right.'　|　<span class="small">排序：</span>'.$orderby['link'].'|　<span class="small">显示：</span>'.$showLink.'　|　<span class="small">布局：</span>'.$layoutLink.'　|　<a href="/s.php?module=area&action=add">新建</a>', 'body'=>$body));
	}
	
	/**
	 * 添加客户区域
	 * @author creator mayinghao 2013-07-05 10:33
	 * @access public
	 * @param none 没有参数
	 * @return none 没有返回值
	 * @throws none 没有异常
	 */
	function add(){
		$body = <<<EOF
<form action="/s.php?module=area&action=insert" name="unit" method="post">
<input type="hidden" name="module" value="area">
<input type="hidden" name="action" value="insert">
<table><thead>
<tr>
	<th colspan="4">新建客户区域：</th>
</tr>
</thead>
<tbody>
<tr class="even">
	<td>名称<span class="small">(中文)</span>：<span class="red bold">*</span></td>
	<td><input type="text" style="width:350px" size="26" name="title" value=""></td>
</tr>
<tr class="odd">
	<td>名称<span class="small">(英文)</span>：</td>
	<td><input type="text" style="width:350px" size="26" name="entitle" value=""></td>
</tr>
<tr class="even">
	<td>备　　注：</td>
	<td><input type="text" style="width:350px" size="26" name="remark" value=""></td>
</tr>
<tr class="odd">
	<td>排　　序：</td>
	<td><input type="text" style="width:100px" size="26" name="ordering" value="1"> <span class="small gray">填入整数，数字越小排位越靠前。</span></td>
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
		$this->kclass->page['title'] .= ' - 新建客户区域';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 新建客户区域', 'right' => '<a href="/s.php?module=area">返回列表</a>', 'body'=>$body));
}
	
	/**
	 * 向数据库插入产品属性
	 * @author creator mayinghao 2013-07-05 10:33
	 * @access public
	 * @param none 没有参数
	 * @return none 没有返回值
	 * @throws none 没有异常
	 */
	function insert(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			if($this->kclass->input['title'] == ''){
				$e = '<li>请填写客户区域的 中文名称。</li>';
			}else{
				if($this->kclass->DB->queryFirst("SELECT areaid FROM `area` WHERE `title`='".$this->kclass->input['title']."'")){
					$e = '<li>您要新建的客户区域 【'.$this->kclass->input['title'].'】 已经存在。</li>';
				}
			}
		}
		
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '新建客户区域',
				'text' => '您在新建客户区域的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		
		$this->kclass->DB->query("
			INSERT INTO `area` ( `title` ,   `entitle` , `remark` , `ordering` , `modified`, `creator` , `created`)
			VALUES (
			'".$this->kclass->input['title']."', '".$this->kclass->input['entitle']."', '".$this->kclass->input['remark']."', '".$this->kclass->input['ordering']."', '".TIMENOW."', '".$this->kclass->user['userid']."', '".TIMENOW."'
			)
		");

		$this->kclass->messager(array(
			'title' => '新建客户区域',
			'text' => '客户区域 <b>'.$this->kclass->input['title'].'</b> 已新建成功!',
			'url' => '/s.php?module=area',
			'sec' => 2
		));
	}
	
	//修改客户区域
	function update(){
		if($this->kclass->input['areaid']<=0){
			$this->kclass->boinkIt('/s.php?module=area');
		}
		
		$area = $this->kclass->DB->queryFirst("SELECT `area`.* FROM `area` WHERE `areaid`='".$this->kclass->input['areaid']."'");
		if(!$area){
			$this->kclass->messager(array(
					'title' => '修改客户区域',
					'text' => '请求的客户区域数据不存在!',
					'url' => '/s.php?module=area',
					'sec' => 3
			));
		}
		
$body = <<<EOF
<form action="/s.php?module=area&action=doupdate" name="area" method="post">
<input type="hidden" name="module" value="area">
<input type="hidden" name="action" value="doupdate">
<input type="hidden" name="areaid" value="{$this->kclass->input['areaid']}">
<table><thead>
<tr>
	<th colspan="4">修改客户区域：</th>
</tr>
</thead>
<tbody>
<tr class="even">
	<td>名称<span class="small">(中文)</span>：<span class="red bold">*</span></td>
	<td><input type="text" style="width:350px" size="26" name="title" value="{$area['title']}"></td>
</tr>
<tr class="odd">
	<td>名称<span class="small">(英文)</span>：</td>
	<td><input type="text" style="width:350px" size="26" name="entitle" value="{$area['entitle']}"></td>
</tr>
<tr class="even">
	<td>备　　注：</td>
	<td><input type="text" style="width:350px" size="26" name="remark" value="{$area['remark']}"></td>
</tr>
<tr class="odd">
	<td>排　　序：</td>
	<td><input type="text" style="width:100px" size="26" name="ordering" value="{$area['ordering']}"> <span class="small gray">填入整数，数字越小排位越靠前。</span></td>
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
		$this->kclass->page['title'] .= ' - 修改客户区域';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 修改客户区域', 'right' => '<a href="/s.php?module=area">返回列表</a>', 'body'=>$body));
	}
	//
	function doupdate(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			if($this->kclass->input['title'] == ''){
				$e = '<li>请填写客户区域的 中文名称。</li>';
			}
		}
		
		if(isset($e)){
			$this->kclass->messager(array(
					'title' => '修改客户区域',
					'text' => '您在新建客户区域的过程中有以下错误：<ul>'.$e.'</ul>',
					'url' => 'javascript:history.back()',
					'sec' => 3
			));
		}
		
		$this->kclass->DB->query("
			UPDATE `area`
			SET `title` = '".$this->kclass->input['title']."', 
				`entitle` = '".$this->kclass->input['entitle']."', 
				`remark` = '".$this->kclass->input['remark']."', 
				`ordering` = '".$this->kclass->input['ordering']."', 
				`modified` = '".TIMENOW."', 
				`modifier` = '".$this->kclass->user['userid']."'
			WHERE areaid='".$this->kclass->input['areaid']."'
		");
		
		$this->kclass->messager(array(
			'title' => '修改客户区域',
			'text' => '客户区域 <b>'.$this->kclass->input['title'].'</b> 的信息已成功修改!',
			'url' => '/s.php?module=area',
			'sec' => 2
		));
	}
	
	//删除客户区域
	function kill(){
	if($this->kclass->input['areaid']<=0){
			$this->kclass->boinkIt('/s.php?module=area');
		}
		if($this->kclass->input['confirm'] == 0){
			$this->kclass->boinkIt('/s.php?module=area');
		}
		if($this->kclass->input['area'] < 0){
			$e = '<li>请从正确的页面进行操作，如果此问题持续存在，请联系网站管理员。';
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '删除客户区域',
				'text' => '您在删除客户区域的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$area = $this->kclass->DB->queryFirst("
			SELECT title
			FROM area
			WHERE areaid='".$this->kclass->input['areaid']."'
		");
		if($area){
			$this->kclass->DB->query("
				UPDATE `area`
				SET killed=1
				WHERE areaid='".$this->kclass->input['areaid']."'
			");
			$this->kclass->messager(array(
				'title' => '删除客户区域成功',
				'text' => '客户区域 <b>'.$area['title'].'</b> 已成功被标记为删除!',
				'url' => '/s.php?module=area',
				'sec' => 2
			));
		}else{
			$this->kclass->messager(array(
				'title' => '删除客户区域失败',
				'text' => '您要删除的客户区域，不存在！',
				'url' => '/s.php?module=area',
				'sec' => 3
			));
		}
	}
}
?>