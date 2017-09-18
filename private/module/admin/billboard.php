<?php
// 
class billboard{
	# Global
	var $kclass;
	var $output;

	/*--------------------*/
	// Auto run function
	/*--------------------*/
	function autoRun(){
		$this->kclass->page['title'] = '公告';
		$this->baseurl = '<a href="/a.php">首页</a> - <a href="/a.php?module=billboard">内部公告</a>';
		$this->right = '<a href="/a.php?module=billboard&action=list">列表</a> <a href="/a.php?module=billboard&action=add">新建</a>';

		switch($this->kclass->input['action']){
			case 'list':
				$this->modify();
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
			case 'find':
				$this->find();
			break;
		
			default:
				$this->modify();
		}
	}

	// 
	function view(){
		if($this->kclass->input['billboardid']<=0){
			$this->kclass->boinkIt('/a.php?module=billboard');
		}
		$billboard = $this->kclass->DB->queryFirst("
			SELECT *
			FROM billboard
			WHERE billboardid='".$this->kclass->input['billboardid']."'
		");
		if(!$billboard){
			$this->kclass->boinkIt('/a.php?module=billboard');
		}
		$mdate = date('Y-m-d H:i', $billboard['modified']);
		$start = $this->kclass->iif($billboard['start']>0, date('Y-m-d', $billboard['start']), '-');
		$end = $this->kclass->iif($billboard['end']>0, date('Y-m-d', $billboard['end']), '-');
		if($billboard['publish'] == 1){
			$publish = '显示';
		}else{
			$publish = '不显示(隐藏)';
		}
		if($billboard['pin'] == 1){
			$pin = '是';
		}else{
			$pin = '否';
		}
$body = <<<EOF
<table cellspacing="10" align="center">
<tr>
	<td><span class="title">{$billboard['title']}</span><br /><br />被阅读: <b>{$billboard['views']}</b> 次, 更新时间: <b>{$mdate}</b>. 开始: <b>{$start}</b>, 结束: <b>{$end}</b>. 显示选项: <b>{$publish}</b>, 置顶: <b>{$pin}</b>.</td>
</tr>
<tr>
	<td>{$billboard['content']}</td>
</tr>
</table>
EOF;
		$this->kclass->page['title'] = ' - 查看详细 - '.$billboard['title'];
		$this->kclass->page['main'] .= $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 查看公告', 'right' => $this->right . ' | <a href="/a.php?module=billboard&action=remove&billboardid='.$billboard['billboardid'].'">删除</a> <a href="/a.php?module=billboard&action=update&billboardid='.$billboard['billboardid'].'">编辑</a>', 'body'=>$body));
	}

	// 
	function insert(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e = '请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。';
		}else{
			if($this->kclass->input['title'] == ''){
				$e = '请填写公告标题。';
			}
			if(strlen($this->kclass->input['content']) < 4){
				$e .= '请填写内容';
			}
		}

		if($e != ''){
			$this->kclass->messager(array(
				'title' => '新建失败',
				'text' => '您在新建公告的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}

		$start = $this->kclass->iif($this->kclass->input['start']!='', strtotime($this->kclass->input['start']), '');
		$end = $this->kclass->iif($this->kclass->input['end']!='', strtotime($this->kclass->input['end']), 0);

		$this->kclass->DB->query("
			INSERT INTO `billboard` 
				(`pin` , `title` , `content` , `start` , `end` , `publish` , `modified` , `creator` , `created` ) 
			VALUES 
				('".$this->kclass->input['pin']."', '".$this->kclass->input['title']."', '".$this->kclass->input['content']."', '".$start."', '".$end."', '".$this->kclass->input['publish']."', '".TIMENOW."', '".$this->kclass->user['userid']."', '".TIMENOW."')
		");

		$this->kclass->messager(array(
			'title' => '新建公告',
			'text' => '公告 <b>'.$this->kclass->input['title'].'</b> 已新建成功!',
			'url' => '/a.php?module=billboard',
			'sec' => 1
		));
	}

	// 
	function add(){
		$start = date('Y-m-d', TIMENOW);
		$this->kclass->page['header'] = '<script src="/public/cal.js"></script><link rel="stylesheet" type="text/css" href="/public/cal.css" />';
$body = <<<EOF
<table cellspacing="10" align="center">
<form action="/a.php?module=billboard&action=insert" name="bb" method="post">
<input type="hidden" name="module" value="billboard">
<input type="hidden" name="action" value="insert">
<tr class="dotted">
	<td>标题：<span class="red">*</span></td>
	<td><input type="text" style="width:370px" size="50" name="title" value="{$b['title']}"></td>
</tr>
<tr class="dotted">
	<td>内容：<span class="red">*</span></td>
	<td><textarea name="content" style="width:370px;height: 100px;">{$b['content']}</textarea></td>
</tr>
<tr class="dotted">
	<td>开始：</td>
	<td><input type="text" size="10" style="width:86px" id="s0" name="start" value="{$start}"><img id="s1" src="/public/images/cal.gif" width="15" height="12" class="hand" /> 结束：<input type="text" size="10" style="width:86px" id="e0" name="end" value=""><img id="e1" src="/public/images/cal.gif" width="15" height="12" class="hand" /> <span class="small gray">日期格式为：YYYY-MM-DD。<span></td>
</tr>
<tr class="dotted">
	<td>发布：</td>
	<td>
		<label for="p1"><input type="radio" id="p1" name="publish" value="1" checked>是</label>　
		<label for="p0"><input type="radio" id="p0" name="publish" value="0">否</label> 
	</td>
</tr>
<tr class="dotted">
	<td>置顶：</td>
	<td>
		<label for="pin1"><input type="radio" id="pin1" name="pin" value="1">是</label>　
		<label for="pin0"><input type="radio" id="pin0" name="pin" value="0" checked>否</label> 
	</td>
</tr>
<tr class="dotted">
	<td colspan='2' align='center'>
		<input type="submit" value="   保存   " accesskey="s">
		<input type="reset" value="   复位   ">
	</td>
</tr>
</form>
</table>
<script type="text/javascript">
var cal = Calendar.setup({onSelect:function(cal){cal.hide()}});
cal.manageFields("s1", "s0", "%Y-%m-%d");
cal.manageFields("e1", "e0", "%Y-%m-%d");
</script>
EOF;
		$this->kclass->page['title'] .= ' - 新建';
		$this->kclass->page['main'] .= $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 新建', 'right' => $this->right . ' <a href="/a.php?module=billboard">返回</a>', 'body'=>$body));
	}

	// 
	function doupdate(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e = '请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。';
		}else{
			if($this->kclass->input['title'] == ''){
				$e = '请填写类别标题。';
			}
			if(strlen($this->kclass->input['content']) < 4){
				$e .= '请填写内容';
			}
		}

		if($e != ''){
			$this->kclass->messager(array(
				'title' => '修改公告',
				'text' => '您在修改公告的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}

		$start = $this->kclass->iif($this->kclass->input['start']!='', strtotime($this->kclass->input['start']), '');
		$end = $this->kclass->iif($this->kclass->input['end']!='', strtotime($this->kclass->input['end']), 0);

		$this->kclass->DB->query("
			UPDATE `billboard` 
			SET `title` = '".$this->kclass->input['title']."', 
				`content` = '".$this->kclass->input['content']."', 
				`start` = '".$start."', 
				`end` = '".$end."', 
				`pin` = '".$this->kclass->input['pin']."', 
				`publish` = '".$this->kclass->input['publish']."', 
				`modifier` = '".$this->kclass->user['userid']."',
				`modified` = '".TIMENOW."'
			WHERE billboardid='".$this->kclass->input['billboardid']."'
		");

		$this->kclass->messager(array(
			'title' => '修改公告',
			'text' => '公告 <b>'.$this->kclass->input['title'].'</b> 已成功修改!',
			'url' => '/a.php?module=billboard',
			'sec' => 1
		));
	}

	// 
	function update(){
		if($this->kclass->input['billboardid']<=0){
			$this->kclass->boinkIt('/a.php?module=billboard');
		}

		$b = $this->kclass->DB->queryFirst("
			SELECT *
			FROM billboard
			WHERE billboardid='".$this->kclass->input['billboardid']."'
		");
		if(!$b){
			$this->kclass->boinkIt('/a.php?module=billboard');
		}

		$this->kclass->br2nl = 1;
		$b['content'] = $this->kclass->parseConvertValue($b['content']);
		$start = $this->kclass->iif($b['start']>0, date('Y-m-d', $b['start']), '');
		$end = $this->kclass->iif($b['end']>0, date('Y-m-d', $b['end']), '');

		if($b['publish'] == 1){
			$p1 = ' checked';
		}else{
			$p0 = ' checked';
		}
		if($b['pin'] == 1){
			$pin1 = ' checked';
		}else{
			$pin0 = ' checked';
		}
		$this->kclass->page['header'] = '<script src="/public/cal.js"></script><link rel="stylesheet" type="text/css" href="/public/cal.css" />';
$body = <<<EOF
<table cellspacing="10" align="center">
<form action="/a.php?module=billboard&action=doupdate" name="bb" method="post">
<input type="hidden" name="module" value="billboard">
<input type="hidden" name="action" value="doupdate">
<input type="hidden" name="billboardid" value="{$b['billboardid']}">
<tr class="dotted">
	<td>标题：<span class="red">*</span></td>
	<td><input type="text" style="width:370px" size="50" name="title" value="{$b['title']}"></td>
</tr>
<tr class="dotted">
	<td>内容：<span class="red">*</span></td>
	<td><textarea name="content" style="width:370px;height: 100px;">{$b['content']}</textarea></td>
</tr>
<tr class="dotted">
	<td>开始：</td>
	<td><input type="text" size="10" style="width:86px" id="s0" name="start" value="{$start}"><img id="s1" src="/public/images/cal.gif" width="15" height="12" class="hand" /> 结束：<input type="text" size="10" style="width:86px" id="e0" name="end" value=""><img id="e1" src="/public/images/cal.gif" width="15" height="12" class="hand" /> <span class="small gray">日期格式为：YYYY-MM-DD。<span></td>
</tr>
<tr class="dotted">
	<td>发布：</td>
	<td>
		<label for="p1"><input type="radio" id="p1" name="publish" value="1"{$p1}>是</label>　
		<label for="p0"><input type="radio" id="p0" name="publish" value="0"{$p0}>否</label> 
	</td>
</tr>
<tr class="dotted">
	<td>置顶：</td>
	<td>
		<label for="pin1"><input type="radio" id="pin1" name="pin" value="1"{$pin1}>是</label>　
		<label for="pin0"><input type="radio" id="pin0" name="pin" value="0"{$pin0}>否</label> 
	</td>
</tr>
<tr class="dotted">
	<td colspan='2' align='center'>
		<input type="submit" value="   保存   " accesskey="s">
		<input type="reset" value="   复位   ">
	</td>
</tr>
</form>
</table>
<script type="text/javascript">
var cal = Calendar.setup({onSelect:function(cal){cal.hide()}});
cal.manageFields("s1", "s0", "%Y-%m-%d");
cal.manageFields("e1", "e0", "%Y-%m-%d");
</script>
EOF;
		$this->kclass->page['title'] .= ' - 修改';
		$this->kclass->page['main'] .= $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 修改', 'right' => $this->right . ' <a href="/a.php?module=billboard">返回</a>', 'body'=>$body));
	}

	// 
	function kill(){
		if($this->kclass->input['billboardid']<=0){
			$this->kclass->boinkIt('/a.php?module=billboard');
		}
		if($this->kclass->input['confirm'] == 0){
			$this->kclass->boinkIt('/a.php?module=billboard');
		}
		if($this->kclass->input['billboardid'] < 0){
			$e = '请从正确的页面进行操作，如果此问题持续存在，请联系网站管理员。';
		}
		if($e != ''){
			$this->kclass->messager(array(
				'title' => '删除失败',
				'text' => '您在删除类别的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$billboard = $this->kclass->DB->queryFirst("
			SELECT title
			FROM billboard
			WHERE billboardid='".$this->kclass->input['billboardid']."'
		");
		if($billboard){
			$this->kclass->DB->query("
				DELETE FROM `billboard` 
				WHERE billboardid='".$this->kclass->input['billboardid']."'
			");
			$this->kclass->messager(array(
				'title' => '删除公告',
				'text' => '公告 <b>'.$billboard['title'].'</b> 已成功被删除!',
				'url' => '/a.php?module=billboard',
				'sec' => 3
			));
		}else{
			$this->kclass->boinkIt('/a.php?module=billboard');
		}
	}

	// 
	function remove(){
		if($this->kclass->input['billboardid']<=0){
			$this->kclass->boinkIt('/a.php?module=billboard');
		}
		$billboard = $this->kclass->DB->queryFirst("
			SELECT title
			FROM billboard
			WHERE billboardid='".$this->kclass->input['billboardid']."'
		");
$body = <<<EOF
<table cellspacing="10" align="center">
<form action="/a.php?module=billboard&action=kill" name="bb" method="post">
<input type="hidden" name="module" value="billboard">
<input type="hidden" name="action" value="kill">
<input type="hidden" name="billboardid" value="{$this->kclass->input['billboardid']}">
<tr class="dotted">
	<td colspan="2">你确定要 <span class="darkred">删除</span> 公告: <span class="big">{$billboard['title']}</span> 吗?</td>
</tr>
<tr class="dotted">
	<td colspan="2" align="center">
		<label for="c1"><input type="radio" id="c1" name="confirm" value="1">是</label> 
		<label for="c0"><input type="radio" id="c0" name="confirm" value="0" checked>否</label> 
	</td>
</tr>
<tr>
	<td colspan='2' align='center'>
		<input type="submit" value="   保存   " accesskey="s">
		<input type="reset" value="   复位   ">
	</td>
</tr>
</form>
</table>
EOF;
		$this->kclass->page['title'] .= ' - 删除';
		$this->kclass->page['main'] .= $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 删除', 'right' => $this->right . ' <a href="/a.php?module=billboard">返回</a>','body'=>$body));
	}

	// 
	function modify(){
		$billboards = $this->kclass->DB->query("
			SELECT *
			FROM billboard
			ORDER BY modified DESC, creator DESC
		");
		if($this->kclass->DB->numRows()){
			$body = '<table cellspacing="10" align="center"><tr class="solid"><td>标题</td><td align="center">开始日期</td><td align="center">结束日期</td><td align="right">阅读</td><td align="center">更新日期</td><td align="center">发布</td><td align="center">置顶</td><td align="right">操作</td></tr>';
			while($billboard = $this->kclass->DB->fetchArray($billboards)){
				$body .= '<tr class="dotted"><td'.$this->kclass->iif($billboard['publish']==0, ' class="gray"', '').'><a href="/a.php?module=billboard&action=view&billboardid='.$billboard['billboardid'].'">'.$billboard['title'].'</a></td><td class="small">'.$this->kclass->iif($billboard['start']>0, date('y-m-d', $billboard['start']), '-').'</td><td class="small">'.$this->kclass->iif($billboard['end']>0, date('y-m-d', $billboard['end']), '-').'</td><td align="right" class="tiny">'.$billboard['views'].'</td><td class="small">'.date('y-m-d H:i', $billboard['modified']).'</td><td'.$this->kclass->iif($billboard['publish'] == 1, ' class="darkred">是', '>否').'</td><td'.$this->kclass->iif($billboard['pin'] == 1, ' class="darkred">是', '>否').'</td><td><a href="/a.php?module=billboard&action=remove&billboardid='.$billboard['billboardid'].'">删</a> <a href="/a.php?module=billboard&action=update&billboardid='.$billboard['billboardid'].'">改</a></td></tr>';
			}
			$body .= '</table>';
		}else{
			$body = '<div class="center">暂无系统公告。</div.';
		}

		$this->kclass->page['title'] .= ' - 列表';
		$this->kclass->page['main'] .= $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 列表', 'right' => $this->right, 'body'=>$body));
	}
}


?>