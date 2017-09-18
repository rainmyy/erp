<?php
class paymentstatus{
function autoRun(){
	$this->kclass->page['title']='支付状态';
	$this->baseurl = '<a href="/s.php">首页</a> - <a href="/s.php?module=paymentstatus">支付状态</a>';
	$this->right = '<a href="/s.php?module=paymentstatus">列表</a> <a href="/s.php?module=paymentstatus&action=add">新建</a>';
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
		case 'kill':
			$this->kill();
		break;
		case 'update':
			$this->update();
		break;
		case 'remove':
			$this->remove();
		break;
		default :
			$this->mmlist();
	}
}
//
function update(){

}
//
function remove(){
	print_r($this->kclass->input);
}
//
function kill(){
if($this->kclass->input['paymentstatusid']<=0){
	$this->kclass->boinkIt("/s.php?module=paymentstatus");
}
$paymentstatus=$this->kclass->DB->queryFirst("
				SELECT title,entitle 
				FROM `paymentstatus`
				WHERE `paymentstatusid`='".$this->kclass->input['paymentstatusid']."'
");
if($paymentstatus['entitle']){
	$title=$paymentstatus['title'].'('.$paymentstatus['entitle'].')';
}else{
	$title=$paymentstatus['entitle'];
}
$body=<<<EOF
<form action="/s.php?module=paymentstatus&action=remove" method="post">
<input type="hidden" name="module" value="paymentsatus"/>
<input type="hidden" name="action" value="remove"/>
<input type="hidden" name="paymentstatusid" value="{$this->kclass->input['paymentstatusid']}">
<table class="tlist" align="center">
<thead>
<tr><th>删除支付状态：<b>{$title}</b></th></tr>
</thead>
<tbody>
<tr class="odd">
<td>你确定要删除支付状态：<a href="/s.php?module=paymentstatus&action=view&paymentstatusid={$this->kclass->input['paymentstatusid']}" class="big blod" target="_blank">{$title}</a>吗？</td>
</tr>
<tr class="even" align="center">
<td>
<label for="c1"><input type="radio" name="confirm" id="c1" value="1">是</label>
<label for="c2"><input type="radio" name="confirm" id="c2" value="0" checked>否</label>
</td>
</tr>
<tr class="odd" align="center">
<td>
<input type="submit" value="　提交　"/>
<input type="reset" value="　重置　"/>
</td>
</tr>
</tbody>
</table>
</form>
EOF;
$this->kclass->page['title'].='-删除支付状态-'.$title;
$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.'-删除支付状态-'.$title,'right'=>$this->right.'|<a href="/s.php?module=paymentstatus">返回列表</a>','body'=>$body));
}
//
function insert(){
	print_r($this->kclass->input);
	if($this->kclass->input['requestMethod']!='post'){
		$e='<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
	}else{
		if($this->kclass->input['title']==''){
			$e.='<li>请填写支付状态的名称</li>';
		}else{
			if($this->kclass->DB->queryFirst("SELECT paymentstatusid FROM `paymentstatus` WHERE `title`='".$this->kclass->input['title']."'")){
				$e.= '<li>您要新建的支付状态 【'.$this->kclass->input['title'].'】 已经存在。</li>';
			}
		}
	}
	if(isset($e)){
		$this->kclass->messager(array(
			    'title' => '新建支付支付状态',
				'text' => '您在新建支付状态的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
	}else{
		$this->kclass->DB->query("
			INSERT INTO `paymentstatus` (`module`,`title`,`entitle`,`remark`,`ordering`,`creator`,`created`) VALUES('".$this->kclass->input['module']."','".$this->kclass->input['title']."','".$this->kclass->input['entitle']."','".$this->kclass->input['remark']."','".$this->kclass->input['ordering']."','".$this->kclass->user['userid']."','".TIMENOW."')
	 ");
	 $this->kclass->messager(array(
				'title'=>'新建支付状态',
				'text'=>'支付状态<b>'.$this->kclass->input['title'].'</b>已经新建成功',
				'url'=>'/s.php?module=paymentstatus#'.$this->kclass->DB->insertID().'',
				'sec'=>2
		 ));
	}
}
//
function add(){
 $body=<<<EOF
<form action="/s.php?module=paymentstatus&action=insert" name="paymentstatus" method="post">
<input type="hidden" name="module" value="paymentstatus"/>
<input type="hidden" name="action" value="insert"/>
<table class="tlist" align="center">
<tr class="head">
<td colspan="2">新建支付状态</td>
</tr>
<tr class="odd">
<td>名称<span class="small">(中文)</span> : <span class="red blod"> * </span></td>
<td><input type="text" style="width:300px" size ="26" name="title"></td>
</tr>
<tr class="even">
<td>名称<span class="small">(英文)</span> : </td>
<td><input type="text" style="width:300px" size ="26" name="entitle"></td>
</tr>
<tr class="odd">
<td>备     注：</td>
<td><input type="text" style="width:300px" size="26" name="remark"></td>
</tr>
<tr class="even">
<td>排     序：</td>
<td><input type="text" style="width:300px" size="26"name="ordering" value="1"></td>
</tr>
<tr class="odd">
<td colspan="2" align="center">
<input type="submit" id="submitButton" value="　保存　" accesskey="s"/ >
<input type="reset" value="　保存　">
</td>
</tr>
</table>
</form>
EOF;
	$this->kclass->page['title']='-新建支付状态';
	$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.'新建支付状态','right'=>$this->right.'|<a href="/s.php?module=paymentstatus">返回列表</a>','body'=>$body));
}
//
function mmlist(){
	$bs=$this->kclass->DB->query("
							SELECT * 
							FROM `paymentstatus` 
							ORDER BY ordering DESC,created DESC
							");
	if($this->kclass->DB->numRows()){
		$body='<table class="hundred">
		<thead>
		<tr>
		<th>名称<span class="gray">(中文)</span></th><th>名称<span class="gray">(英文)</span></th><th>备注</th><th>排序</th><th>创建日期</th><th>操作</th>
		</tr>
		</thead></tbody>';
		while($payments=$this->kclass->DB->fetchArray($bs)){
			$body.='<tr>
			<td>'.$payments['title'].'</td>
			<td>'.$payments['entitle'].'</td>
			<td>'.$payments['remark'].'</td>
			<td>'.$payments['ordering'].'</td>
			<td>'.date('Y-m-d',$payments['created']).'</td>
			<td><a href="/s.php?module=paymentstatus&action=kill&paymentstatusid='.$payments['paymentstatusid'].'">删</a>　<a href="/s.php?module=paymentstatus&action=update&paymentstatusid='.$payments['paymentstatusid'].'">改</a></td>
			</tr>';
		}
		$body.='</tbody></table>';
		}
 //  $this->kclass->page['onload'] = 'dc.tabhover();$(\'.mytable\').fixedtableheader();';
$this->kclass->page['title'].='-列表';
$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.'-列表','right'=>$this->right.' <a href="/s.php?module=paymentterm&action=updateAll">改全部</a>', 'body'=>$body));   
}
}
?>