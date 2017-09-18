<?php
//
class help{
	var $kclass;
	//
	function autorun(){
		$this->baseurl='<a href="/s.php?">首页</a>';
		switch($this->kclass->input['action']){
			case 'updateRuleNo':
				return $this->updateRuleNo();
			case 'doupdateRuleNo':
				return $this->doupdateRuleNo();
			case 'viewRuleNo':
				return $this->viewRuleNo();
			break;
		}
	}
	function viewRuleNo(){
		$help=$this->kclass->DB->queryFirst("SELECT helpid,title,content FROM help WHERE module='ruleNo'");
		if(!$help){
			$this->kclass->messager(array(
				'title' => '查看物资编码规则',
				'text' => '您在查看物资编码规则的过程中有以下错误：<ul><li>暂时无编码规则可查看，请 <a href="/s.php?module=help&action=updateRuleNo">添加</a></li></ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$content=$this->kclass->parseConvertValue($help['content']);
		$body=<<<EOF
<table style="width:600px">
<thead><tr><th colspan=2>{$help['title']}</th></tr></thead>
<tbody>
<tr><td>{$content}</td></tr>
</tbody>
</table>
EOF;
		$this->kclass->page['title']=' 查看物资编码规则';
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - <a href="/s.php?module=material">物资列表</a> - 查看物资编码规则','right'=>'<a href="/s.php?module=material">返回列表</a>　|　<a href="/s.php?module=help&action=updateRuleNo">修改</a>','body'=>$body));
	}
	//
	function updateRuleNo(){
		$this->kclass->br2nl+=2;
		$help=$this->kclass->DB->queryFirst("SELECT helpid,title,content FROM help WHERE module='ruleNo'");
		$content=$this->kclass->parseConvertValue($help['content']);
		$body=<<<EOF
<form method="post" action="/s.php?module=help&action=doupdateRuleNo">
<input type="hidden" name="module" value="help">
<input type="hidden" name="action" value="doupdateRuleNo">
<input type="hidden" name="helpid" value="{$help['helpid']}">
<table style="width:600px">
<thead><tr><th colspan=2>物资编码规则</th></tr></thead>
<tbody>
<tr><td>标题：<span class="bold red">*</span></td><td><input type="text" name="title" value="{$help['title']}" style="width:500px"></td></tr>
<tr><td>内容：<span class="bold red">*</span></td><td><textarea name="content" style="width:500px;height:700px">{$content}</textarea></td></tr>
<tr class="odd center" nohover>
<td colspan=4><input type="submit" id="submitButton" value="  提交  " ><input type="reset" value="  复位  "/></td>
</tr>
</tbody>
</table>
</form>	
EOF;
		$this->kclass->page['title']=' 修改物资编码规则';
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - <a href="/s.php?module=material">物资列表</a> - 修改物资编码规则','right'=>'<a href="/s.php?module=material">返回列表</a>','body'=>$body));
	}
	function doupdateRuleNo(){
		if($this->kclass->input['requestMethod']!='post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			if($this->kclass->input['title']==''){
				$e.='<li>请填写标题</li>';
			}
			if($this->kclass->input['content']==''){
				$e.='<li>请填写内容</li>';
			}
		}
		if($e){
			$this->kclass->messager(array(
				'title' => '修改物资编码规则',
				'text' => '您在修改物资编码规则的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$content=$this->kclass->convertText($this->kclass->input['content']);
		if($this->kclass->input['helpid']>0){
			$this->kclass->DB->query("
				UPDATE help SET 
					title='{$this->kclass->input['title']}',
					content='{$content}',
				  modifier='{$this->kclass->user['userid']}',
					modified='".TIMENOW."'
				WHERE helpid='{$this->kclass->input['helpid']}'
			");
		}else{
			$this->kclass->DB->query("
				INSERT INTO help (module,title,content,created,creator)
				VALUES ('ruleNo','{$this->kclass->input['title']}','{$content}','".TIMENOW."','{$this->kclass->user['userid']}')
			");
		}
		$this->kclass->messager(array(
			'title' => '修改物资编码规则',
			'text' => '物资编码规则 已修改成功!',
			'url' => '/s.php?module=material',
			'sec' => 3
		));
	}
}

?>