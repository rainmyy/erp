<?php
/**
 * 包装规格模块的增删改查
 * @author mayinghao <mayinghao@wisdom.hk>
 * @version 1.0 2013-7-23
 * @copyright New Wisdom Investment Limited
 */
class packingsize{
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
	 * 包装规格列表
	 * @author creator mayinghao 2013-07-23 10:30 
	 * @access public
	 * @param none 没有参数
	 * @return none 没有返回值
	 * @throws none 没有异常
	 */
	function mmlist(){
		$sizes = $this->kclass->DB->query("
			SELECT `packingsize`.*
			FROM `packingsize`
			WHERE killed = 0
			ORDER BY `packingsize`.packingsizeid ASC
		");
		
		if($this->kclass->DB->numRows()){
			$index = 1;
			while($size = $this->kclass->DB->fetchArray($sizes)){
				$itemtr .= '<tr class="odd">
					<td>'.$size['length'].'　X　'.$size['width'].'　X　'.$size['height'].'</td>
					<td>'.$size['cartonNum'].'</td>
					<td>'.$size['remark'].'</td>
					<td><a href="/s.php?module=packingsize&action=update&packingsizeid='.$size['packingsizeid'].'">修改</a> <a href="/s.php?module=packingsize&action=kill&packingsizeid='.$size['packingsizeid'].'&rt=list" onclick="return confirm(\'你确定要删除这个 包装规格吗？\');">删除</a></td>
				</tr>';
				$index++;
			}
			
$body.=<<<EOF
<table class="hundred">
<thead>
<tr><th width="260">规格参数(<span class="small gray">长 X 宽 X 高  cm</span>)</th><th width="160">每箱数量(pcs)</th><th>备注</th><th width="80">操作</th></tr>
</thead>
<tbody>
{$itemtr}
</tbody>
</table>
EOF;
		}
		
		$this->kclass->page['onload'] .= "dc.listhover();dc.tabhover();";
		$this->kclass->page['title'] .= ' 包装规格列表';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 包装规格列表<span class="tiny">('.$sum.')</span>', 'right' => $this->right.'<a href="/s.php?module=packingsize&action=add">新建包装规格</a>', 'body'=>$body));
	}
	
	/**
	 * 添加包装规格
	 * @author creator mayinghao 2013-07-23 10:33 
	 * @access public
	 * @param none 没有参数
	 * @return none 没有返回值
	 * @throws none 没有异常
	 */
	function add(){
		$body = <<<EOF
<form action="/s.php?module=packingsize&action=insert" name="unit" method="post">
<input type="hidden" name="module" value="packingsize">
<input type="hidden" name="action" value="insert">
<table><thead>
<tr>
	<th colspan="4">新建包装规格：</th>
</tr>
</thead>
<tbody>
<tr class="even">
	<td>规格参数：<span class="red bold">*</span></td>
	<td><span class="small gray">长：<input type="text" size="3" name="length">　X　宽：<input type="text" size="3" name="width">　X　高：<input type="text" size="3" name="height">　(单位：cm)</span></td>
</tr>
<tr class="odd">
	<td>每箱数量(PCS)</span>：</td>
	<td><input type="text" style="width:260px" size="26" name="cartonNum" value=""><span class="small gray">每箱能装的个数</span></td>
</tr>
<tr class="even">
	<td>备　　注：</td>
	<td><input type="text" style="width:260px" size="26" name="remark" value=""><span class="small gray">用于哪些产品的包装</span></td>
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
		$this->kclass->page['title'] .= ' - 新建包装规格';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 新建客户区域', 'right' => '<a href="/s.php?module=packingsize">返回列表</a>', 'body'=>$body));
}
	
	/**
	 * 向数据库插入包装规格
	 * @author creator mayinghao 2013-07-23 10:33 
	 * @access public
	 * @param none 没有参数
	 * @return none 没有返回值
	 * @throws none 没有异常
	 */
	function insert(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			if($this->kclass->input['length'] == '' || $this->kclass->input['width'] == '' || $this->kclass->input['height'] == ''){
				$e = '<li>请填写包装箱的长宽高。</li>';
			}else if($this->kclass->input['cartonNum'] == ''){
				$e = '<li>请填写包装箱的每箱能够包装的数量。</li>';
			}else{
				if($this->kclass->DB->queryFirst("SELECT packingsizeid FROM `packingsize` WHERE `length`='".$this->kclass->input['length']."' AND `width`='".$this->kclass->input['width']."' AND `height`='".$this->kclass->input['height']."' AND `cartonNum`='".$this->kclass->input['cartonNum']."'")){
					$e = '<li>您要新建的包装规格已经存在。</li>';
				}
			}
		}
		
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '新建包装规格',
				'text' => '您在新建包装规格的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		
		$this->kclass->DB->query("
			INSERT INTO `packingsize` ( `length` ,   `width` , `height` ,`cartonNum` ,`remark`, `ordering` , `modified`, `creator` , `created`)
			VALUES (
			'".$this->kclass->input['length']."', '".$this->kclass->input['width']."',  '".$this->kclass->input['height']."', '".$this->kclass->input['cartonNum']."','".$this->kclass->input['remark']."', '".$this->kclass->input['ordering']."', '".TIMENOW."', '".$this->kclass->user['userid']."', '".TIMENOW."'
			)
		");

		$this->kclass->messager(array(
			'title' => '包装规格',
			'text' => '包装规格 已新建成功!',
			'url' => '/s.php?module=packingsize',
			'sec' => 3
		));
	}
	
	/**
	 * 修改包装规格
	 * @author creator mayinghao 2013-07-23 10:33 
	 * @access public
	 * @param none 没有参数
	 * @return none 没有返回值
	 * @throws none 没有异常
	 */
	function update(){
		if($this->kclass->input['packingsizeid']<=0){
			$this->kclass->boinkIt('/s.php?module=packingsize');
		}
		
		$size = $this->kclass->DB->queryFirst("SELECT `packingsize`.* FROM `packingsize` WHERE `packingsizeid`='".$this->kclass->input['packingsizeid']."'");
		if(!$size){
			$this->kclass->messager(array(
				'title' => '修改包装规格',
				'text' => '请求的包装规格数据不存在!',
				'url' => '/s.php?module=packingsize',
				'sec' => 3
			));
		}
		
$body = <<<EOF
<form action="/s.php?module=packingsize&action=doupdate" name="unit" method="post">
<input type="hidden" name="module" value="packingsize">
<input type="hidden" name="action" value="doupdate">
<input type="hidden" name="packingsizeid" value="{$this->kclass->input['packingsizeid']}">
<table><thead>
<tr>
	<th colspan="4">修改包装规格：</th>
</tr>
</thead>
<tbody>
<tr class="even">
	<td>规格参数：<span class="red bold">*</span></td>
	<td><span class="small gray">长：<input type="text" size="3" name="length" value="{$size['length']}">　X　宽：<input type="text" size="3" name="width" value="{$size['width']}">　X　高：<input type="text" size="3" name="height" value="{$size['height']}">　(单位：cm)</span></td>
</tr>
<tr class="odd">
	<td>每箱数量(PCS)</span>：</td>
	<td><input type="text" style="width:260px" size="26" name="cartonNum" value="{$size['cartonNum']}"><span class="small gray">每箱能装的个数</span></td>
</tr>
<tr class="even">
	<td>备　　注：</td>
	<td><input type="text" style="width:260px" size="26" name="remark" value="{$size['remark']}"><span class="small gray">用于哪些产品的包装</span></td>
</tr>
<tr class="odd">
	<td>排　　序：</td>
	<td><input type="text" style="width:100px" size="26" name="ordering" value="{$size['ordering']}"> <span class="small gray">填入整数，数字越小排位越靠前。</span></td>
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
		$this->kclass->page['title'] .= ' - 修改包装规格';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 修改包装规格', 'right' => '<a href="/s.php?module=packingsize">返回列表</a>', 'body'=>$body));
	}
	
	/**
	 * 向数据库修改包装规格
	 * @author creator mayinghao 2013-07-23 10:33 
	 * @access public
	 * @param none 没有参数
	 * @return none 没有返回值
	 * @throws none 没有异常
	 */
	function doupdate(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			if($this->kclass->input['length'] == '' || $this->kclass->input['width'] == '' || $this->kclass->input['height'] == ''){
				$e = '<li>请填写包装箱的长宽高。</li>';
			}else if($this->kclass->input['cartonNum'] == ''){
				$e = '<li>请填写包装箱的每箱能够包装的数量。</li>';
			}
		}
		
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '修改包装规格',
				'text' => '您在修改包装规格的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		
		$this->kclass->DB->query("
			UPDATE `packingsize`
			SET `length` = '".$this->kclass->input['length']."', 
				`width` = '".$this->kclass->input['width']."',
				`height` = '".$this->kclass->input['height']."', 
				`cartonNum` = '".$this->kclass->input['cartonNum']."',  
				`remark` = '".$this->kclass->input['remark']."', 
				`ordering` = '".$this->kclass->input['ordering']."', 
				`modified` = '".TIMENOW."', 
				`modifier` = '".$this->kclass->user['userid']."'
			WHERE packingsizeid='".$this->kclass->input['packingsizeid']."'
		");
		
		$this->kclass->messager(array(
			'title' => '修改包装规格',
			'text' => '包装规格的信息已成功修改!',
			'url' => '/s.php?module=packingsize',
			'sec' => 3
		));
	}
	
	/**
	 * 向数据库删除包装规格
	 * @author creator mayinghao 2013-07-23 10:33 
	 * @access public
	 * @param none 没有参数
	 * @return none 没有返回值
	 * @throws none 没有异常
	 */
	function kill(){
	if($this->kclass->input['packingsizeid']<=0){
			$this->kclass->boinkIt('/s.php?module=packingsize');
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '删除包装规格',
				'text' => '您在删除包装规格的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$area = $this->kclass->DB->queryFirst("
			SELECT packingsizeid
			FROM packingsize
			WHERE packingsizeid='".$this->kclass->input['packingsizeid']."'
		");
		if($area){
			$this->kclass->DB->query("
				UPDATE `packingsize`
				SET killed=1
				WHERE packingsizeid='".$this->kclass->input['packingsizeid']."'
			");
			$this->kclass->messager(array(
				'title' => '删除包装规格成功',
				'text' => '包装规格已成功被标记为删除!',
				'url' => '/s.php?module=packingsize',
				'sec' => 2
			));
		}else{
			$this->kclass->messager(array(
				'title' => '删除包装规格失败',
				'text' => '您要删除的包装规格，不存在！',
				'url' => '/s.php?module=packingsize',
				'sec' => 3
			));
		}
	}
}
?>