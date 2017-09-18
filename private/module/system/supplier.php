<?php
class supplier{
	function autoRun(){
		$this->kclass->page['title'] = '供应商';
		$this->baseurl = '<a href="/s.php">首页</a> - <a href="/s.php?module=supplier">供应商</a>';
		$this->right = '<span class="small"><a href="/s.php?module=supplier&action=find">查找</a>：<form style="display:inline-block" action="/s.php?module=supplier&action=list" name="supplier" method="post"><input type="text" style="width:50px;height:15px;margin-top:-3px" name="title" value="'.$this->kclass->input['title'].'"><input type="submit" value="找" accesskey="s" style="height:23px"></form></span>';
		switch($this->kclass->input['action']){
			case 'list':
				$this->mmlist();
			break;
			case 'find':
				$this->find();
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
			case 'addInquiry':
				$this->addInquiry();
			break;
			case 'insertInquiry':
				$this->insertInquiry();
			break;
			case 'updateInquiry':
				$this->updateInquiry();
			break;
			case 'doupdateInquiry':
				$this->doupdateInquiry();
			break;
			case 'removeInquiry':
				$this->removeInquiry();
			break;
			case 'killInquiry':
				$this->killInquiry();
			break;
			case 'revivalInquiry':
				$this->revivalInquiry();
			break;
			case 'restoreInquiry':
				$this->restoreInquiry();
			break;
			case 'addQuoting':
				$this->addQuoting();
			break;
			case 'insertQuoting':
				$this->insertQuoting();
			break;
			case 'updateQuoting':
				$this->updateQuoting();
			break;
			case 'doupdateQuoting':
				$this->doupdateQuoting();
			break;
			case 'removeQuoting':
				$this->removeQuoting();
			break;
			case 'killQuoting':
				$this->killQuoting();
			break;
			case 'addVisit':
				$this->addVisit();
			break;
			case 'insertVisit':
				$this->insertVisit();
			break;
			case 'updateVisit':
				$this->updateVisit();
			break;
			case 'doupdateVisit':
				$this->doupdateVisit();
			break;
			case 'removeVisit':
				$this->removeVisit();
			break;
			case 'killVisit':
				$this->killVisit();
			break;
			default:
				$this->mmlist();
		}
	}
	// 
	function view(){
		if(!$this->kclass->input['supplierid'] > 0)$this->kclass->boinkIt('/s.php?module=supplier');
		$supplier = $this->kclass->DB->queryFirst("
			SELECT `supplier`.*, 
				region.country, region.state, region.city, 
				m.username AS mname, c.username AS cname
			FROM `supplier` 
			LEFT JOIN `region` ON (region.regionid=supplier.regionid) 
			LEFT JOIN `user` AS m ON (m.userid=supplier.modifier) 
			LEFT JOIN `user` AS c ON (c.userid=supplier.creator) 
			WHERE supplierid='".$this->kclass->input['supplierid']."'
		");
		if(!$supplier)	$this->kclass->boinkIt('/s.php?module=supplier');
		if($supplier['regionid']>0){
			if($this->kclass->purviews(array('module'=>'region','action'=>'list'))){//判断相关模块是否有权限查看链接到相应模块
				$region = '<a href="/s.php?module=region&action=view&regionid='.$supplier['regionid'].'" target="_blank">'.$supplier['country'].' '.$supplier['state'].' '.$supplier['city'].'</a>';
			}else{
				$region = $supplier['country'].' '.$supplier['state'].' '.$supplier['city'];
			}
		}else{
			$region='';
		}
		if($supplier['modifier']>0){
			$modified = '，'.$supplier['mname'].'于'.date('y-m-d H:i:s', $supplier['modified']).'最后修改';
		}
		$created = date('y-m-d H:i:s', $supplier['created']);
		if($supplier['postalcode']!='')$postalcode='<span class="normal">(邮编：'.$supplier['postalcode'].')</span>';
		if($supplier['position']!='')$position='<span class="normal">(称谓：'.$supplier['position'].')</span>';
		if($supplier['position2']!='')$position2='<span class="normal">(称谓：'.$supplier['position2'].')</span>';
		$attach = $this->kclass->getAttachs(array('module'=>'supplier', 'mid'=>$supplier['supplierid']));
		if($attach!= false){
			$attachs = '<div class="clear">'.$attach.'</div>';
		}
		$sample=$this->kclass->relatedSample(array('supplierid'=>$supplier['supplierid']));
		$inquiry=$this->kclass->relatedInquiry(array('supplierid'=>$supplier['supplierid']));
		$quoting=$this->kclass->relatedQuoting(array('supplierid'=>$supplier['supplierid']));
		$visit=$this->kclass->relatedVisit(array('supplierid'=>$supplier['supplierid']));
		$purchase=$this->kclass->relatedOrder(array('supplierid'=>$supplier['supplierid'], 'type'=>'PO'));
		$commission=$this->kclass->relatedCommission(array('supplierid'=>$supplier['supplierid']));
		$deposit=$this->kclass->relatedDeposit(array('supplierid'=>$supplier['supplierid']));
		$guarantee=$this->kclass->relatedGuarantee(array('supplierid'=>$supplier['supplierid']));
		$creditnote=$this->kclass->relatedCreditnote(array('supplierid'=>$supplier['supplierid']));
		$recipt=$this->kclass->relatedRecipt(array('supplierid'=>$supplier['supplierid']));
		$debitnote=$this->kclass->relatedDebitnote(array('module'=>'order','supplierid'=>$supplier['supplierid']));
		$payment=$this->kclass->relatedPayment(array('module'=>'order','supplierid'=>$supplier['supplierid']));
		$claim=$this->kclass->relatedClaim(array('module'=>'order','supplierid'=>$supplier['supplierid']));
		$return=$this->kclass->relatedReturn(array('module'=>'order','supplierid'=>$supplier['supplierid']));
		$exchange=$this->kclass->relatedExchange(array('supplierid'=>$supplier['supplierid']));
		$customer = $this->kclass->relatedCustomer(array('supplierid'=>$supplier['supplierid']));
$body = <<<EOF
<div class="title"><span class="right small gray">由{$supplier['cname']}于{$created}建立{$modified}。</span>{$supplier['title']}</div>
<dl id="supplier" class="tabs">
	<dt>资料</dt>
	<dt title="供应商来访与我们拜访"{$visit['off']}>互访{$visit['count']}</dt>
	<dt title="供应商询价记录"{$inquiry['off']}>询价{$inquiry['count']}</dt>
	<dt title="给供应商的报价记录"{$quoting['off']}>报价{$quoting['count']}</dt>
	<dt title="发给供应商的各类样品"{$sample['off']}>样品{$sample['count']}</dt>
	<dt title="销售订单"{$purchase['off']}>订单{$purchase['count']}</dt>
	<dt title="销售佣金记录"{$commission['off']}>佣金{$commission['count']}</dt>
	<dt title="与供应商相关的定金记录"{$deposit['off']}>定金{$deposit['count']}</dt>
	<dt title="与供应商相关的保证金记录"{$guarantee['off']}>保证金{$guarantee['count']}</dt>
	<dt title="与供应商相关的应收款项"{$creditnote['off']}>应收{$creditnote['count']}</dt>
	<dt title="与供应商相关的已经收到的款项"{$recipt['off']}>已收{$recipt['count']}</dt>
	<dt title="与供应商相关的应该付款的款项"{$debitnote['off']}>应付{$debitnote['count']}</dt>
	<dt title="与供应商相关的已经付款的款项"{$payment['off']}>已付{$payment['count']}</dt>
	<dt title="与供应商相关的索赔记录"{$claim['off']}>索赔{$claim['count']}</dt>
	<dt title="供应商的退货记录"{$return['off']}>退货{$return['count']}</dt>
	<dt title="供应商的换货记录"{$exchange['off']}>换货{$exchange['count']}</dt>
	<dd>
<table class="hundred">
<tr class="odd">
	<td width="80">中文名称：</td>
	<td class="middle">{$supplier['title']}</td>
	<td width="80">简　　称：</td>
	<td class="middle">{$supplier['abbr']}</td>
</tr>
<tr class="even">
	<td>英文名称：</td>
	<td class="middle">{$supplier['entitle']}</td>
	<td>商业类型：</td>
	<td class="middle">{$supplier['business']}</td>
</tr>
<tr class="odd">
	<td>中文地址：</td>
	<td class="middle">{$supplier['address']} {$postalcode}</td>
	<td>国　　家：</td>
	<td class="middle">{$region}</td>
</tr>
<tr class="even">
	<td>英文地址：</td>
	<td class="middle">{$supplier['enaddress']}</td>
	<td>网　　站：</td>
	<td class="middle">{$supplier['website']}</td>
</tr>
<tr class="odd">
	<td valign="top">银行资料：</td>
	<td>{$supplier['bankdetail']}</td>
	<td valign="top">备　　注：</td>
	<td>{$supplier['remark']}</td>
</tr>

<tr class="even">
	<td colspan="2" align="center"><span class="bold">主联系人</span></td>
	<td colspan="2" align="center"><span class="bold">次联系人</span></td>
</tr>
<tr class="odd">
	<td >姓　　名：</td>
	<td class="middle" width="450">{$supplier['linkman']} {$position}</td>
	<td>姓　　名：</td>
	<td class="middle">{$supplier['linkman2']} {$position2}</td>
</tr>
<tr class="even">
	<td>昵　　称：</td>
	<td class="middle">{$supplier['nickname']}</td>
	<td>昵　　称：</td>
	<td class="middle">{$supplier['nickname2']}</td>
</tr>
<tr class="even">
	<td>邮　　箱：</td>
	<td class="middle">{$supplier['email']}</td>
	<td>邮　　箱：</td>
	<td class="middle">{$supplier['email2']}</td>
	
</tr>
<tr class="odd">
	<td>传　　真：</td>
	<td class="middle">{$supplier['fax']}</td>
	<td>传　　真：</td>
	<td class="middle">{$supplier['fax2']}</td>
</tr>
<tr class="even">
	<td>电　　话：</td>
	<td class="middle">{$supplier['telephone']}</td>
	<td>电　　话：</td>
	<td class="middle">{$supplier['telephone2']}</td>
</tr>
<tr class="odd">
	<td>手　　机：</td>
	<td class="middle">{$supplier['mobile']}</td>
	<td>手　　机：</td>
	<td class="middle">{$customer['mobile2']}</td>
</tr>
</table>
{$attachs}
	</dd>
	<dd>{$visit['panel']}</dd>
	<dd>{$inquiry['panel']}</dd>
	<dd>{$quoting['panel']}</dd>
	<dd>{$sample['panel']}</dd>
	<dd>{$order['panel']}</dd>
	<dd>{$commission['panel']}</dd>
	<dd>{$deposit['panel']}</dd>
	<dd>{$guarantee['panel']}</dd>
	<dd>{$creditnote['panel']}</dd>
	<dd>{$receipt['panel']}</dd>
	<dd>{$debitnote['panel']}</dd>
	<dd>{$payment['panel']}</dd>
	<dd>{$claim['panel']}</dd>
	<dd>{$receipt['panel']}</dd>
	<dd>{$exchange['panel']}</dd>
</dl>
EOF;
		$this->kclass->page['onload'] .= "dc.tabs({'id':'supplier'});";
		$this->kclass->page['title'] .= ' - '.$supplier['title'];
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl.' - 供应商信息', 'right' => '<span class="small">打印：</span><a href="/p.php?action=supplier&supplierid='.$supplier['supplierid'].'" target="_blank">基本资料</a>　<a href="/p.php?action=quoting&supplierid='.$supplier['supplierid'].'" target="_blank">报价表</a>　|　<span class="small">新建：</span><a href="/s.php?module=supplier&action=addVisit&supplierid='.$supplier['supplierid'].'">互访记录</a>　<a href="/s.php?module=supplier&action=addInquiry&supplierid='.$supplier['supplierid'].'">询价单</a>　<a href="/s.php?module=supplier&action=addQuoting&supplierid='.$supplier['supplierid'].'">报价单</a>　|　'.$this->kclass->iif($b['killed']==1, '<a href="/s.php?module=supplier&action=restore&supplierid='.$supplier['supplierid'].'&rt=view">恢复</a>', '<a href="/s.php?module=supplier&action=remove&supplierid='.$supplier['supplierid'].'&rt=view">删除</a>　<a href="/s.php?module=supplier&action=update&supplierid='.$supplier['supplierid'].'&rt=view">修改</a>'), 'body'=>$body));
	}
	//
	function insert(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			if($this->kclass->input['title'] == ''){
				$e = '<li>请填写供应商的 中文名称。</li>';
			}else{
				if($this->kclass->DB->queryFirst("SELECT supplierid FROM `supplier` WHERE `title`='".$this->kclass->input['title']."'")){
					$e = '<li>您要新建的供应商 【'.$this->kclass->input['title'].'】 已经存在。</li>';
				}
			}
			if($this->kclass->input['abbr'] == ''){
				$e .= '<li>请填写供应商的 简称。</li>';
			}elseif(strlen($this->kclass->input['abbr'])>10){
				$e .= '<li>供应商的简称不能超过 10 个英文字符或 5 个中文字。</li>';
			}
			if($this->kclass->input['address'] == ''){
				$e .= '<li>请填写供应商的 中文地址。</li>';
			}elseif(strlen($this->kclass->input['abbr'])>255){
				$e .= '<li>供应商的中文地址不能超过 255 个英文字符。</li>';
			}
			if($this->kclass->input['countryid'] == -1 AND $this->kclass->input['provinceid'] == -1 AND $this->kclass->input['cityid'] == -1){
				$e .= '<li>请填写供应商的所属 国家。</li>';
			}
			if($this->kclass->input['linkman'] == ''){
				$e .= '<li>请填写供应商的 主联系人。</li>';
			}elseif(strlen($this->kclass->input['linkman'])>100){
				$e .= '<li>供应商的主联系人不能超过 100 个英文字符。</li>';
			}
			if($this->kclass->input['position'] == ''){
				$e .= '<li>请填写供应商主联系人的 称谓。</li>';
			}elseif(strlen($this->kclass->input['position'])>50){
				$e .= '<li>供应商的主联系人不能超过 50 个英文字符。</li>';
			}
			if($this->kclass->input['telephone'] == ''){
				$e .= '<li>请填写供应商的 电话。</li>';
			}elseif(strlen($this->kclass->input['telephone'])>50){
				$e .= '<li>供应商的电话不能超过 50 个英文字符。</li>';
			}
			if($this->kclass->input['email'] == ''){
				$e .= '<li>请填写供应商的 邮箱。</li>';
			}elseif(strlen($this->kclass->input['email'])>50){
				$e .= '<li>供应商的邮箱不能超过 150 个英文字符。</li>';
			}
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '新建供应商',
				'text' => '您在新建供应商的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		if($this->kclass->input['cityid']>0){
			$regionid=$this->kclass->input['cityid'];
		}elseif($this->kclass->input['provinceid']>0){
			$regionid=$this->kclass->input['provinceid'];
		}else{
			$regionid=$this->kclass->input['countryid'];
		}
		$this->kclass->DB->query("
			INSERT INTO `supplier` (
				`title`,
				`entitle`, 
				`abbr`, 
				`address`, 
				`enaddress`, 
				`regionid`, 
				`postalcode`, 
				`telephone`,
				 `telephone2`,
				 `mobile`,
				 `mobile2`,
				 `fax`,
				 `fax2`,
				 `email`,
				 `email2`,
				 `nickname`,
				 `nickname2`,
				 `website`,
				 `linkman`,
				 `linkman2`,
				 `position`,
				 `position2`, 
				`businesstypeid`, 
				`description`, 
				`bankdetail`, 
				`remark`, 
				`modified`, 
				`creator`, 
				`created`) 
				VALUES ('".$this->kclass->input['title']."', 
				'".$this->kclass->input['entitle']."', 
				'".$this->kclass->input['abbr']."', 
				'".$this->kclass->input['address']."', 
				'".$this->kclass->input['enaddress']."', 
				'".$regionid."', 
				'".$this->kclass->input['postalcode']."', 
				'".$this->kclass->input['telephone']."',
				 '".$this->kclass->input['telephone2']."',
				 '".$this->kclass->input['mobile']."',
			 	 '".$this->kclass->input['mobile2']."',
				 '".$this->kclass->input['fax']."', 
				 '".$this->kclass->input['fax2']."',
				 '".$this->kclass->input['email']."',
				 '".$this->kclass->input['email2']."',
				 '".$this->kclass->input['nickname']."',
				 '".$this->kclass->input['nickname2']."',
				 '".$this->kclass->input['website']."',
				 '".$this->kclass->input['linkman']."',
				 '".$this->kclass->input['linkman2']."',
				 '".$this->kclass->input['position']."',
				 '".$this->kclass->input['position2']."',
				'".$this->kclass->input['businesstypeid']."', 
				'".$this->kclass->input['description']."', 
				'".$this->kclass->input['bankdetail']."', 
				'".$this->kclass->input['remark']."', 
				'".TIMENOW."', '".$this->kclass->user['userid']."', '".TIMENOW."')
		");
		$this->kclass->updateAttachs(array('module'=>'supplier', 'mid'=>$this->kclass->DB->insertID()));

		$this->kclass->messager(array(
			'title' => '新建供应商',
			'text' => '供应商 <b>'.$this->kclass->input['title'].'</b> 已新建成功!',
			'url' => '/s.php?module=supplier&action='.$rurl,
			'sec' => 3
		));
	}

	//
	function add(){
		$region = $this->kclass->chooserRegion(array('regionid'=>1, 'hasBlank'=>1));
		$business = $this->kclass->chooserBusinesstype(array('name'=>'businesstypeid', 'hasBlank'=>1, 'width'=>152));
		$upload = $this->kclass->upload(array('title'=>'相关附件：'));
$body = <<<EOF
<form action="/s.php?module=supplier&action=insert" name="supplier" method="post" onsubmit="return dc.supplier.check(this)">
<input type="hidden" name="module" value="supplier">
<input type="hidden" name="action" value="insert">
<table class="hundred">
<thead>
<tr>
	<th colspan="4">供应商新建表单：</th>
</tr>
</thead>
<tbody>
<tr class="odd">
	<td>中文名称：<span class="red bold">*</span></td>
	<td><input type="text" style="width:450px" name="title" value=""></td>
	<td>简　　称：<span class="red bold">*</span></td>
	<td><input type="text" size="20" name="abbr" value=""> <span class="gray small">不要超过10个英文字符，或5个中文字</span></td>
</tr>
<tr class="even">
	<td>英文名称：<span class="red bold">*</span></td>
	<td><input type="text" style="width:450px" name="entitle" value=""></td>
	<td>商业类型：</td>
	<td>{$business} <span class="gray small">选择一个最符合的类型</span></td>
</tr>
<tr class="odd">
	<td>中文地址：<span class="red bold">*</span></td>
	<td><input type="text" style="width:348px" name="address" value=""> 邮编：<input type="text" style="width:50px" name="postalcode" value="{$customer['postalcode']}"></td>
	<td>国　　家：<span class="red bold">*</span></td>
	<td>{$region}</td>
</tr>
<tr class="even">
	<td>英文地址：</td>
	<td><input type="text" style="width:450px" name="enaddress" value=""></td>
	<td>网　　站：</td>
	<td><input type="text" style="width:450px" name="website" value=""></td>
</tr>

<tr class="odd">
	<td>银行资料：</td>
	<td><textarea name="bankdetail" style="width:450px;height:100px"></textarea></td>
	<td>备　　注：</td>
	<td><textarea name="remark" style="width:450px;height:100px"></textarea></td>
</tr>

<tr class="even">
	<td colspan="2" align="center"><span class="bold">主联系人：</span><span class="red bold">*</span></td>
	<td colspan="2" align="center"><span class="bold">次联系人：</span></td>
</tr>

<tr class="odd">
	<td>姓　　名：<span class="red bold">*</span></td>
	<td><input type="text" style="width:160px" name="linkman" value=""> 称谓：<span class="red bold">*</span><input type="text" style="width:170px" name="position" value=""><div class="gray small">联系人称谓，首选职位，如无职位则请标明先生/女士/小姐。</div></td>
	<td>姓　　名：<span class="red bold">*</span></td>
	<td><input type="text" style="width:160px" name="linkman2" value=""> 称谓：<span class="red bold">*</span><input type="text" style="width:170px" name="position2" value=""><div class="gray small">联系人称谓，首选职位，如无职位则请标明先生/女士/小姐。</div></td>
</tr>
<tr class="even">
	<td>昵　　称：<span class="red bold">*</span></td>
	<td><input type="text" style="width:320px" name="nickname" value=""/> <span class="gray small">用于发送邮件时的称呼</span></td>
	<td>昵　　称：<span class="red bold">*</span></td>
	<td><input type="text" style="width:320px" name="nickname2" value=""/> <span class="gray small">用于发送邮件时的称呼</span></td>
</tr>
<tr class="odd">
	<td>邮　　箱：<span class="red bold">*</span></td>
	<td><input type="text" style="width:220px" name="email" value=""> 电话：<span class="red bold">*</span><input type="text" style="width:170px" name="telephone" value=""><div class="gray">电话与传真格式：“<span class="big bold">+</span><i class="small">国家区号</i> <span class="big bold">-</span><i class="small">地区区号</i><span class="small">(如果有)</span> <span class="big bold">-</span><i class="small">电话号码</i>”</div></td>
	<td>邮　　箱：<span class="red bold">*</span></td>
	<td><input type="text" style="width:220px" name="email2" value=""> 电话：<span class="red bold">*</span><input type="text" style="width:170px" name="telephone2" value=""><div class="gray">电话与传真格式：“<span class="big bold">+</span><i class="small">国家区号</i> <span class="big bold">-</span><i class="small">地区区号</i><span class="small">(如果有)</span> <span class="big bold">-</span><i class="small">电话号码</i>”</div></td>
</tr>
<tr class="even">
	<td>手　　机：</td>
	<td><input type="text" style="width:228px" name="mobile" value=""> 传真：<input type="text" style="width:170px" name="fax" value=""><div class="gray">手机格式与电话相同，如：“+86-0138-28866666”</div></td>
	<td>手　　机：</td>
	<td><input type="text" style="width:228px" name="mobile2" value=""> 传真：<input type="text" style="width:170px" name="fax2" value=""><div class="gray">手机格式与电话相同，如：“+86-0138-28866666”</div></td>
</tr>
</tbody>
</table>
{$upload}
<table class="hundred">
<tbody>
<tr class="odd">
	<td colspan="4" align="center">
		<input type="submit" id="submitButton" value="      保存      " accesskey="s">　　
		<input type="reset" value="      复位      ">
	</td>
</tr>
<tr class="even">
	<td class="small gray">
	注意事项：<ul>
	<li>英文地址：请严格按英文的格式进行(会影响订单中打印的地址)，基本与中文地址相反的顺序。</li>
	<li>未有列项的内容，写入“备注”中，长度上限3万中文字。</li>
	</ul>
	</td>
</tr>
</tbody>
</table>
</form>
EOF;
		$this->kclass->tbline+=3;
		$this->kclass->page['onload'] .= 'dc.tabhover()';
		$this->kclass->page['title'] .= ' - 新建供应商';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 新建供应商', 'right' => '<a href="/s.php?module=supplier">返回列表</a>', 'body'=>$body));
	}

	//
	function doupdate(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			if($this->kclass->input['title'] == ''){
				$e = '<li>请填写供应商的 名称。</li>';
			}else{
				if($supplier = $this->kclass->DB->queryFirst("SELECT supplierid FROM `supplier` WHERE `supplierid`<>'".$this->kclass->input['supplierid']."' AND `title`='".$this->kclass->input['title']."'")){
					$e = '<li>您要修改的供应商 【'.$this->kclass->input['title'].'】 已经存在。您可以<a href="/s.php?module=supplier&action=view&curstomerid='.$supplier['supplierid'].'" target="_blank">点击这里</a>访问该供应商的详细信息。</li>';
				}
			}
			if($this->kclass->input['abbr'] == ''){
				$e .= '<li>请填写供应商的 简称。</li>';
			}elseif(strlen($this->kclass->input['abbr'])>10){
				$e .= '<li>供应商的简称不能超过 10 个英文字符或 5 个中文字。</li>';
			}
			if($this->kclass->input['address'] == ''){
				$e .= '<li>请填写供应商的 英文地址。</li>';
			}elseif(strlen($this->kclass->input['abbr'])>255){
				$e .= '<li>供应商的英文地址不能超过 255 个英文字符。</li>';
			}
			if($this->kclass->input['countryid'] == -1 AND $this->kclass->input['provinceid'] == -1 AND $this->kclass->input['cityid'] == -1){
				$e .= '<li>请填写供应商的所属 国家。</li>';
			}
			if($this->kclass->input['linkman'] == ''){
				$e .= '<li>请填写供应商的 主联系人。</li>';
			}elseif(strlen($this->kclass->input['linkman'])>100){
				$e .= '<li>供应商的主联系人不能超过 100 个英文字符。</li>';
			}
			if($this->kclass->input['position'] == ''){
				$e .= '<li>请填写供应商主联系人的 称谓。</li>';
			}elseif(strlen($this->kclass->input['position'])>50){
				$e .= '<li>供应商的主联系人不能超过 50 个英文字符。</li>';
			}
			if($this->kclass->input['telephone'] == ''){
				$e .= '<li>请填写供应商的 电话。</li>';
			}elseif(strlen($this->kclass->input['telephone'])>50){
				$e .= '<li>供应商的电话不能超过 50 个英文字符。</li>';
			}
			if($this->kclass->input['email'] == ''){
				$e .= '<li>请填写供应商的 邮箱。</li>';
			}elseif(strlen($this->kclass->input['email'])>50){
				$e .= '<li>供应商的邮箱不能超过 150 个英文字符。</li>';
			}
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '修改供应商',
				'text' => '您在修改供应商的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		if($this->kclass->input['cityid']>0){
			$regionid=$this->kclass->input['cityid'];
		}elseif($this->kclass->input['provinceid']>0){
			$regionid=$this->kclass->input['provinceid'];
		}else{
			$regionid=$this->kclass->input['countryid'];
		}
		$this->kclass->DB->query("
			UPDATE `supplier`
			SET `title` = '".$this->kclass->input['title']."', 
				`entitle` = '".$this->kclass->input['entitle']."', 
				`abbr` = '".$this->kclass->input['abbr']."', 
				`businesstypeid` = '".$this->kclass->input['businesstypeid']."', 
				`address` = '".$this->kclass->input['address']."', 
				`regionid` = '".$regionid."', 
				`enaddress` = '".$this->kclass->input['enaddress']."', 
				`postalcode` = '".$this->kclass->input['postalcode']."', 
				`website` = '".$this->kclass->input['website']."', 
				`linkman` = '".$this->kclass->input['linkman']."', 
				`position` = '".$this->kclass->input['position']."', 
				`linkman2` = '".$this->kclass->input['linkman2']."', 
				`position2` = '".$this->kclass->input['position2']."', 
				`nickname` = '".$this->kclass->input['nickname']."', 
				`nickname2` = '".$this->kclass->input['nickname2']."', 
				`email` = '".$this->kclass->input['email']."', 
				`email2` = '".$this->kclass->input['email2']."', 
				`telephone` = '".$this->kclass->input['telephone']."', 
				`telephone2` = '".$this->kclass->input['telephone2']."',
				`mobile` = '".$this->kclass->input['mobile']."', 
				`mobile2` = '".$this->kclass->input['mobile2']."', 
				`fax` = '".$this->kclass->input['fax']."',
				`fax2` = '".$this->kclass->input['fax2']."',  
				`bankdetail` = '".$this->kclass->input['bankdetail']."', 
				`remark` = '".$this->kclass->input['remark']."', 
				`modified` = '".TIMENOW."', 
				`modifier` = '".$this->kclass->user['userid']."'
			WHERE supplierid='".$this->kclass->input['supplierid']."'
		");
		$this->kclass->updateAttachs(array('module'=>'supplier', 'mid'=>$this->kclass->input['supplierid']));
		if($this->kclass->input['rt']=='view'){
			$rt = '&action=view&supplierid='.$this->kclass->input['supplierid'];
		}elseif($this->kclass->input['rt']=='list'){
			$rt = '#'.$this->kclass->input['supplierid'];
		}
		$this->kclass->messager(array(
			'title' => '修改供应商',
			'text' => '供应商 <b>'.$this->kclass->input['title'].'</b> 的信息已成功修改!',
			'url' => '/s.php?module=supplier'.$rt,
			'sec' => 3
		));
	}

	//
	function update(){
		if($this->kclass->input['supplierid']<=0 OR !$supplier = $this->kclass->DB->queryFirst("SELECT `supplier`.* FROM `supplier` WHERE `supplierid`='".$this->kclass->input['supplierid']."'")){
			$this->kclass->boinkIt('/s.php?module=supplier');
		}
		$this->kclass->br2nl=true;
		$supplier['bankdetail']=$this->kclass->parseConvertValue($supplier['bankdetail']);
		$supplier['remark']=$this->kclass->parseConvertValue($supplier['remark']);
		$business = $this->kclass->chooserBusinesstype(array('name'=>'businesstypeid', 'width'=>350,'hasBlank'=>1, 'selectedid'=>$supplier['businesstypeid']));
		$region = $this->kclass->chooserRegion(array('regionid'=>$supplier['regionid'],'hasBlank'=>1,'cn'=>1));
		$upload = $this->kclass->upload(array('module'=>'supplier', 'mid'=>$supplier['supplierid']));
$body = <<<EOF
<form action="/s.php?module=supplier&action=doupdate" name="supplier" method="post" onsubmit="return dc.supplier.check(this)">
<input type="hidden" name="module" value="supplier">
<input type="hidden" name="action" value="doupdate">
<input type="hidden" name="supplierid" value="{$this->kclass->input['supplierid']}">
<input type="hidden" name="rt" value="{$this->kclass->input['rt']}">
<table class="hundred">
<thead>
<tr>
	<th colspan="4">供应商修改：{$supplier['title']}</th>
</tr>
</thead>
<tbody>
<tr class="odd">
	<td>中文名称：<span class="red bold">*</span></td>
	<td><input type="text" style="width:450px" name="title" value="{$supplier['title']}"></td>
	<td>简　　称：<span class="red bold">*</span></td>
	<td><input type="text" size="20" name="abbr" value="{$supplier['abbr']}"> <span class="gray small">不要超过10个英文字符，或5个中文字</span></td>
</tr>
<tr class="even">
	<td>英文名称：<span class="red bold">*</span></td>
	<td><input type="text" style="width:450px" name="entitle" value="{$supplier['entitle']}"></td>
	<td>商业类型：</td>
	<td>{$business} <span class="gray small">选择一个最符合的类型</span></td>
</tr>
<tr class="odd">
	<td>中文地址：<span class="red bold">*</span></td>
	<td><input type="text" style="width:348px" name="address" value="{$supplier['address']}"> 邮编：<input type="text" style="width:50px" name="postalcode" value="{$supplier['postalcode']}"></td>
	<td>国　　家：<span class="red bold">*</span></td>
	<td>{$region}</td>
</tr>
<tr class="even">
	<td>英文地址：</td>
	<td><input type="text" style="width:450px" name="enaddress" value="{$supplier['enaddress']}"></td>
	<td>网　　站：</td>
	<td><input type="text" style="width:450px" name="website" value="{$supplier['website']}"></td>
</tr>
<tr class="odd">
	<td>银行资料：</td>
	<td><textarea name="bankdetail" style="width:450px;height:100px">{$supplier['bankdetail']}</textarea></td>
	<td>备　　注：</td>
	<td><textarea name="remark" style="width:450px;height:100px">{$supplier['remark']}</textarea></td>
</tr>

<tr class="even">
	<td colspan="2" align="center"><span class="bold">主联系人：</span><span class="red bold">*</span></td>
	<td colspan="2" align="center"><span class="bold">次联系人：</span></td>
</tr>

<tr class="odd">
	<td>姓　　名：<span class="red bold">*</span></td>
	<td><input type="text" style="width:160px" name="linkman" value="{$supplier['linkman']}"> 称谓：<span class="red bold">*</span><input type="text" style="width:170px" name="position" value="{$supplier['position']}"><div class="gray small">联系人称谓，首选职位，如无职位则请标明先生/女士/小姐。</div></td>
	<td>姓　　名：<span class="red bold">*</span></td>
	<td><input type="text" style="width:160px" name="linkman2" value="{$supplier['linkman2']}"> 称谓：<span class="red bold">*</span><input type="text" style="width:170px" name="position2" value="{$supplier['position2']}"><div class="gray small">联系人称谓，首选职位，如无职位则请标明先生/女士/小姐。</div></td>
</tr>
<tr class="even">
	<td>昵　　称：<span class="red bold">*</span></td>
	<td><input type="text" style="width:320px" name="nickname" value="{$supplier['nickname']}"/> <span class="gray small">用于发送邮件时的称呼</span></td>
	<td>昵　　称：<span class="red bold">*</span></td>
	<td><input type="text" style="width:320px" name="nickname2" value="{$supplier['nickname2']}"/> <span class="gray small">用于发送邮件时的称呼</span></td>
</tr>
<tr class="odd">
	<td>邮　　箱：<span class="red bold">*</span></td>
	<td><input type="text" style="width:220px" name="email" value="{$supplier['email']}"> 电话：<span class="red bold">*</span><input type="text" style="width:170px" name="telephone" value="{$supplier['telephone']}"><div class="gray">电话与传真格式：“<span class="big bold">+</span><i class="small">国家区号</i> <span class="big bold">-</span><i class="small">地区区号</i><span class="small">(如果有)</span> <span class="big bold">-</span><i class="small">电话号码</i>”</div></td>
	<td>邮　　箱：<span class="red bold">*</span></td>
	<td><input type="text" style="width:220px" name="email2" value="{$supplier['email2']}"> 电话：<span class="red bold">*</span><input type="text" style="width:170px" name="telephone2" value="{$supplier['telephone2']}"><div class="gray">电话与传真格式：“<span class="big bold">+</span><i class="small">国家区号</i> <span class="big bold">-</span><i class="small">地区区号</i><span class="small">(如果有)</span> <span class="big bold">-</span><i class="small">电话号码</i>”</div></td>
</tr>
<tr class="even">
	<td>手　　机：</td>
	<td><input type="text" style="width:228px" name="mobile" value="{$supplier['mobile']}"> 传真：<input type="text" style="width:170px" name="fax" value="{$supplier['fax']}"><div class="gray">手机格式与电话相同，如：“+86-0138-28866666”</div></td>
	<td>手　　机：</td>
	<td><input type="text" style="width:228px" name="mobile2" value="{$supplier['mobile2']}"> 传真：<input type="text" style="width:170px" name="fax2" value="{$supplier['fax2']}"><div class="gray">手机格式与电话相同，如：“+86-0138-28866666”</div></td>
</tr>
</tbody>
</table>
{$upload}
<table class="hundred">
<tbody>
<tr class="odd">
	<td align="center">
		<input type="submit" id="submitButton" value="      保存      " accesskey="s">　　
		<input type="reset" value="      复位      ">
	</td>
</tr>
<tr class="even">
	<td class="small gray">
	注意事项：<ul>
	<li>英文地址：请严格按英文的格式进行(会影响订单中打印的地址)，基本与中文地址相反的顺序。</li>
	<li>未有列项的内容，写入“备注”中，长度上限3万中文字。</li>
	</ul>
	</td>
</tr>
</tbody>
</table>
</form>
EOF;
		$this->kclass->page['onload'] .= 'dc.tabhover()';
		$this->kclass->page['title'] .= ' - 修改 - '.$supplier['title'];
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 修改供应商 - '.$supplier['title'], 'right' => '<a href="/s.php?module=supplier">返回列表</a>', 'body'=>$body));
	}
	//
	function kill(){
		if($this->kclass->input['supplierid']<=0){
			$this->kclass->boinkIt('/s.php?module=supplier');
		}
		if($this->kclass->input['confirm'] == 0){
			$this->kclass->boinkIt('/s.php?module=supplier');
		}
		if($this->kclass->input['supplierid'] < 0){
			$e = '<li>请从正确的页面进行操作，如果此问题持续存在，请联系网站管理员。</li>';
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '删除供应商',
				'text' => '您在删除供应商的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		if($this->kclass->input['rt']=='view'){
			$rt = '&action=view&supplierid='.$this->kclass->input['supplierid'];
		}elseif($this->kclass->input['rt']=='list'){
			$rt = '#'.$this->kclass->input['supplierid'];
		}
		$supplier = $this->kclass->DB->queryFirst("
			SELECT title
			FROM supplier
			WHERE supplierid='".$this->kclass->input['supplierid']."'
		");
		if($supplier){
			$this->kclass->DB->query("
				UPDATE `supplier`
				SET killed=".TIMENOW." 
				WHERE supplierid='".$this->kclass->input['supplierid']."'
			");
			$this->kclass->messager(array(
				'title' => '删除供应商成功',
				'text' => '供应商 <b>'.$supplier['title'].'</b> 已成功被标记为删除!',
				'url' => '/s.php?module=supplier'.$rt,
				'sec' => 3
			));
		}else{
			$this->kclass->messager(array(
				'title' => '删除供应商失败',
				'text' => '您要删除的供应商，不存在！',
				'url' => '/s.php?module=supplier'.$rt,
				'sec' => 3
			));
		}
	}

	//
	function remove(){
		if($this->kclass->input['supplierid']<=0){
			$this->kclass->boinkIt('/s.php?module=supplier');
		}
		$supplier = $this->kclass->DB->queryFirst("
			SELECT title
			FROM supplier
			WHERE supplierid='".$this->kclass->input['supplierid']."'
		");
$body = <<<EOF
<form action="/s.php?module=supplier&action=kill" name="supplier" method="post">
<input type="hidden" name="module" value="supplier">
<input type="hidden" name="action" value="kill">
<input type="hidden" name="supplierid" value="{$this->kclass->input['supplierid']}">
<table><thead>
<thead>
<tr>
	<td>删除供应商：{$supplier['title']}</td>
</tr>
</thead>
<tbody>
<tr class="odd">
	<td class="middle">你确定要删除供应商: <a href="/s.php?module=supplier&action=view&supplierid={$this->kclass->input['supplierid']}" class="big bold" target="_blank">{$supplier['title']}</a> 吗?</td>
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
		$this->kclass->page['title'] .= ' - 删除 - '.$supplier['title'];
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 删除供应商 - '.$supplier['title'], 'right' => '<a href="/s.php?module=supplier">返回列表</a>', 'body'=>$body));
	}

	//
	function revival(){
		if($this->kclass->input['supplierid']<=0){
			$this->kclass->boinkIt('/s.php?module=supplier');
		}
		if($this->kclass->input['confirm'] == 0){
			$this->kclass->boinkIt('/s.php?module=supplier');
		}
		if($this->kclass->input['supplierid'] < 0){
			$e = '<li>请从正确的页面进行操作，如果此问题持续存在，请联系网站管理员。</li>';
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '恢复供应商',
				'text' => '您在恢复供应商的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$supplier = $this->kclass->DB->queryFirst("
			SELECT title
			FROM supplier
			WHERE supplierid='".$this->kclass->input['supplierid']."'
		");
		if($this->kclass->input['rt']=='view'){
			$rt = '&action=view&supplierid='.$this->kclass->input['supplierid'];
		}elseif($this->kclass->input['rt']=='list'){
			$rt = '#'.$this->kclass->input['supplierid'];
		}
		if($supplier){
			$this->kclass->DB->query("
				UPDATE `supplier`
				SET killed=0
				WHERE supplierid='".$this->kclass->input['supplierid']."'
			");
			$this->kclass->messager(array(
				'title' => '恢复供应商成功',
				'text' => '供应商 <b>'.$supplier['title'].'</b> 已成功被恢复！',
				'url' => '/s.php?module=supplier'.$rt,
				'sec' => 3
			));
		}else{
			$this->kclass->messager(array(
				'title' => '恢复供应商失败',
				'text' => '您要恢复的供应商不存在！',
				'url' => '/s.php?module=supplier'.$rt,
				'sec' => 3
			));
		}
	}

	//
	function restore(){
		if($this->kclass->input['supplierid']<=0){
			$this->kclass->boinkIt('/s.php?module=supplier');
		}
		$supplier = $this->kclass->DB->queryFirst("
			SELECT title
			FROM supplier
			WHERE supplierid='".$this->kclass->input['supplierid']."'
		");
$body = <<<EOF
<form action="/s.php?module=supplier&action=revival" name="supplier" method="post">
<input type="hidden" name="module" value="supplier">
<input type="hidden" name="action" value="revival">
<input type="hidden" name="supplierid" value="{$this->kclass->input['supplierid']}">
<table><thead>
<thead>
<tr>
	<td>恢复供应商：{$supplier['title']}</td>
</tr>
</thead>
<tbody>
<tr class="odd">
	<td class="middle">你确定要恢复供应商: <a href="/s.php?module=supplier&action=view&supplierid={$this->kclass->input['supplierid']}" class="big bold" target="_blank">{$supplier['title']}</a> 吗?</td>
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
<tbody>
</table>
</form>
EOF;
		$this->kclass->page['title'] .= ' - 恢复 - '.$supplier['title'];
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 恢复供应商 - '.$supplier['title'], 'right' => '<a href="/s.php?module=supplier">返回列表</a>', 'body'=>$body));
	}
	function find(){
		$region = $this->kclass->chooserRegion(array('regionid'=>0, 'hasBlank'=>1));
$body = <<<EOF
<form action="/s.php?module=supplier&action=list" name="supplier" method="post">
<input type="hidden" name="module" value="supplier">
<input type="hidden" name="action" value="list">
<table>
<thead>
<tr>
	<th colspan="2">查找供应商</th>
</tr>
</thead>
<tbody>
<tr class="even">
	<td>供应商ID 是：</td>
	<td><input type="text" style="width:244px" name="id" value="{$supplier['supplierid']}"></td>
</tr>
<tr class="even">
	<td>供应商名称 包含：</td>
	<td><input type="text" style="width:244px" name="title" value="{$supplier['title']}"> <span class="small gray">可使用供应商名称中的关键字查找</span></td>
</tr>
<tr class="odd">
	<td>且 联系人名 包含：</td>
	<td><input type="text" style="width:244px" name="linkman" value="{$supplier['linkman']}"></td>
</tr>
<tr class="even">
	<td>且 邮箱 包含：</td>
	<td><input type="text" style="width:244px" name="email" value="{$supplier['email']}"></td>
</tr>
<tr class="odd">
	<td>且 电话 包含：</td>
	<td><input type="text" style="width:244px" name="phone" value="{$supplier['phone']}"> <span class="small gray">固定电话或手机</span></td>
</tr>
<tr class="odd">
	<td>且 地区 是：</td>
	<td>{$region}</td>
</tr>
</tbody>
<thead>
<tr>
	<th colspan="2">选项</th>
</tr>
</thead>
<tbody>
<tr class="even">
	<td>排序方式：</td>
	<td>
		<label for="o0"><input type="radio" name="orderby" value="title" id="o0" checked>名称</label> 
		<label for="o1"><input type="radio" name="orderby" value="supplierid" id="o1">ID</label> 
		<label for="o2"><input type="radio" name="orderby" value="modified" id="o2">修改时间</label> 
		<label for="o3"><input type="radio" name="orderby" value="created" id="o3">建立时间</label> 
	</td>
</tr>
<tr class="odd">
	<td>排列方向：</td>
	<td>
		<label for="d0"><input type="radio" id="d0" name="direction" value="ASC" checked>升序(A-Z)</label> 
		<label for="d1"><input type="radio" id="d1" name="direction" value="DESC">降序(Z-A)</label> 
	</td>
</tr>
<tr class="even">
	<td>显示选项：</td>
	<td>
		<label for="s0"><input type="radio" id="s0" name="show" value="all" checked>默认列表</label> 
		<label for="s1"><input type="radio" id="s1" name="show" value="">所有<span class="small gray">(包括删除)</span></label> 
	</td>
</tr>
<tr class="odd">
	<td>列表方式：</td>
	<td disabled>
		<label for="t0"><input type="radio" id="t0" name="layout" value="grid" checked>格子</label> 
		<label for="t1"><input type="radio" id="t1" name="layout" value="list">列表</label> 
	</td>
</tr>
<tr class="even">
	<td align="center" colspan="2">
		<input type="submit" value="   查找   " accesskey="s">
		<input type="reset" value="   复位   ">
	</td>
</tr>
</tbody>
</table>
</form>
EOF;
		$this->kclass->page['onload'] .= 'dc.tabhover();';
		$this->kclass->page['title'] .= ' - 查找供应商';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 查找供应商', 'right' => '<a href="/s.php?module=supplier">返回列表</a>', 'body'=>$body));
	}
	// 
	function mmlist(){
		//如果ID存在，直接转向到View页面
		$this->kclass->input['id']=intval($this->kclass->input['id']);
		if($this->kclass->input['id']>0){
			$supplier=$this->kclass->DB->queryFirst("SELECT supplierid FROM `supplier` WHERE `supplier`.supplierid='".$this->kclass->input['id']."'");
			if($supplier){
				$this->kclass->boinkIt('/s.php?module=supplier&action=view&supplierid='.$supplier['supplierid']);
			}
		}

		if(!$this->kclass->input['show']=='all'){
			$condition='`supplier`.killed=0';
		}else{
			$condition='1=1';
		}
		$this->kclass->input['layout']='list';
		$this->kclass->input['title']=trim($this->kclass->input['title']);
		$this->kclass->input['linkman']=trim($this->kclass->input['linkman']);
		$this->kclass->input['telephone']=trim($this->kclass->input['telephone']);
		$this->kclass->input['mobile']=trim($this->kclass->input['mobile']);
		$this->kclass->input['email']=trim($this->kclass->input['email']);
		if($this->kclass->input['title']!=''){
			$condition .= " AND INSTR(LCASE(`supplier`.title),'".(strtolower($this->kclass->input['title']))."')>0";
			$query['title']=$this->kclass->input['title'];
		}
		if($this->kclass->input['linkman']!=''){
			$condition .= " AND (INSTR(LCASE(`supplier`.linkman),'".(strtolower($this->kclass->input['linkman']))."')>0 OR INSTR(LCASE(`supplier`.linkman2),'".(strtolower($this->kclass->input['linkman']))."')>0)";
			$query['linkman']=$this->kclass->input['linkman'];
		}
		if($this->kclass->input['phone']!=''){
			$condition .= " AND (INSTR(LCASE(`supplier`.telephone),'".(strtolower($this->kclass->input['phone']))."')>0 OR INSTR(LCASE(`supplier`.mobile),'".(strtolower($this->kclass->input['phone']))."')>0)";
			$query['phone']=$this->kclass->input['phone'];
		}
		if($this->kclass->input['email']!=''){
			$condition .= " AND INSTR(LCASE(`supplier`.email),'".(strtolower($this->kclass->input['email']))."')>0";
			$query['email']=$this->kclass->input['email'];
		}
		if($this->kclass->input['regionid']>0){
			$condition .= " AND `supplier`.regionid='".$this->kclass->input['regionid']."'";
			$query['regionid']=$this->kclass->input['regionid'];
		}
		if($this->kclass->input['orderby']!='')$query['orderby']=$this->kclass->input['orderby'];
		if($this->kclass->input['direction']!='')$query['direction']=$this->kclass->input['direction'];
		if($this->kclass->input['show']!='')$query['show']=$this->kclass->input['show'];
		if($this->kclass->input['layout']!='')$query['layout']=$this->kclass->input['layout'];

		$orderby=$this->kclass->orderby(array('module'=>'supplier','direction'=>'asc','orderby'=>'supplier.modified', 'default'=>'title', 'serial'=>array(array('title'=>'名称', 'field'=>'title','word'=>'name'), array('title'=>'ID', 'field'=>'supplierid','word'=>'id'), array('title'=>'修改时间', 'field'=>'modified'), array('title'=>'建立时间', 'field'=>'created'), array('title'=>'国家', 'field'=>'regionid')),'appendUrl'=>$query));
		if(is_array($query) AND count($query)>0)$queryPart='&'.http_build_query($query);

		$suppliers = $this->kclass->DB->query("
			SELECT `supplier`.supplierid, `supplier`.title, `supplier`.entitle, `supplier`.linkman, `supplier`.position, `supplier`.telephone, `supplier`.email, `supplier`.regionid, `supplier`.cover, `supplier`.attachs, `supplier`.images, `supplier`.modified, `supplier`.created, `supplier`.hidden, `supplier`.killed, 
				b.title AS businessTitle, 
				region.country, region.state, region.city, 
				m.username AS modifier, c.username AS creator
			FROM `supplier` 
			LEFT JOIN `region` ON (region.regionid=supplier.regionid) 
			LEFT JOIN `businesstype` AS b ON (b.businesstypeid=`supplier`.businesstypeid) 
			LEFT JOIN `user` AS m ON (m.userid=`supplier`.modifier) 
			LEFT JOIN `user` AS c ON (c.userid=`supplier`.creator) 
			WHERE ".$condition."
			ORDER BY `supplier`.supplierid ASC, `supplier`.modified DESC, `supplier`.created DESC
		");
		if($sum=$this->kclass->DB->numRows()){
			if($this->kclass->input['layout']=='list'){
				$body = '<table class="hundred tablesorter mytable"><thead><th>ID</th><th>供应商名称</th><th>邮箱</th><th>电话</th><th>地区</th><th width="50">选项</th></thead><tbody>';
			}else{
				$body = '<ul id="mmlist" class="mmlist clear">';
			}
			$i=1;
			while($supplier = $this->kclass->DB->fetchArray($suppliers)){
				$supplier['title'] = $this->kclass->parseConvertValue($supplier['title']);
				$title = $this->kclass->iif(strlen($supplier['title'])>30, mb_substr($supplier['title'], 0, 30, 'UTF-8').'...', $supplier['title']);
				if($this->kclass->input['title']!=''){
					$title = $this->kclass->highlight($supplier['title'], $this->kclass->input['title']);
				}
				$linkman=$supplier['linkman'];
				if($this->kclass->input['linkman']!=''){
					$linkman = $this->kclass->highlight($supplier['linkman'], $this->kclass->input['linkman']);
				}
				$email = $this->kclass->iif(strlen($supplier['email'])>30, mb_substr($supplier['email'], 0, 30, 'UTF-8').'...', $supplier['email']);
				if($this->kclass->input['email']!=''){
					$email = $this->kclass->highlight($supplier['email'], $this->kclass->input['email']);
				}
				$telephone = $supplier['telephone'];
				$mobile = $supplier['mobile'];
				if($this->kclass->input['telephone']!=''){
					$telephone = $this->kclass->highlight($telephone, $this->kclass->input['telephone']);
					$mobile = $this->kclass->highlight($mobile, $this->kclass->input['mobile']);
				}
				$region='';
				if($supplier['regionid']>0){
					$region = $supplier['country'].' '.$supplier['state'].' '.$supplier['city'];
				}
				if($supplier['regionid']==$this->kclass->input['regionid']){
					$region = '<span class="red">'.$region.'</span>';
				}

				$operator = $creator = '由 '.$supplier['creator'].' 建于 '.date('y-m-d H:m', $supplier['created']);
				if($supplier['modifier']!=''){
					$modifier = $supplier['modifier'].' 改于 '.date('Y-m-d H:m', $supplier['modified']);
					$operator .= ', '.$modifier;
					$modifier = '由 '.$modifier;
				}else{
					$modifier = $creator;
				}

				if($supplier['hidden']==0){
					if($supplier['killed']>0){
						$link = '<a href="/s.php?module=supplier&action=restore&supplierid='.$supplier['supplierid'].'&rt=list">恢复</a>';
					}else{
						$link = '<a href="/s.php?module=supplier&action=update&supplierid='.$supplier['supplierid'].'&rt=list">改</a> <a href="/p.php?action=supplier&supplierid='.$supplier['supplierid'].'" target="_blank">印</a>';
					}
				}

				if($this->kclass->input['layout']=='list'){//表格
					$body .='<tr'.$this->kclass->rotateLine().'>
						<td>'.$this->kclass->id(array('supplier'=>$supplier['supplierid'])).'</td>
						<td><a href="/s.php?module=supplier&action=view&supplierid='.$supplier['supplierid'].'">'.$supplier['supplierno'].' <span class="normal" title="'.$supplier['title'].'">'.$title.'</span></a><br>
						<span class="small">'.$supplier['businessTitle'].'</span> '.$this->kclass->iif($supplier['attachs']>0, ' <span class="attachFile" title="有'.$supplier['attachs'].'个附件。"></span> ', '').$this->kclass->iif($supplier['images']>0, ' <span class="attachImage" title="有'.$supplier['images'].'个图片。"></span> ', '').'<span class="normal">'.$this->kclass->iif(strlen($supplier['cntitle'])>12, mb_substr($supplier['cntitle'], 0, 12, 'UTF-8').'...', $supplier['cntitle']).'</td>
						<td>'.$linkman.' <span class="small">('.$supplier['position'].')</span><br>'.$email.'</td>
						<td>电话：'.$telephone.$this->kclass->iif($supplier['mobile']!='', '<br>手机：'.$mobile, '').'</td>
						<td>'.$region.'</td>
						<td align="center">'.$link.'</td>
					</tr>';
				}else{//方块列表
					$body .= '<li'.$this->kclass->iif($i%4==0, ' class="end"', '').' class="killed"><a name="'.$supplier['supplierid'].'"></a>
					<div title="'.$operator.'" class="mmlistt clear">
						<span class="right normal">'.$link.'</span>
						<a href="/s.php?module=supplier&action=view&supplierid='.$supplier['supplierid'].'">'.$supplier['supplierno'].' <span class="normal" title="'.$supplier['title'].'">'.$title.'</span></a><br>
						<span class="small">'.$supplier['businessTitle'].'</span> '.$this->kclass->iif($supplier['attachs']>0, ' <span class="attachFile" title="有'.$supplier['attachs'].'个附件。"></span> ', '').$this->kclass->iif($supplier['images']>0, ' <span class="attachImage" title="有'.$supplier['images'].'个图片。"></span> ', '').'<span class="normal">'.$this->kclass->iif(strlen($supplier['entitle'])>12, mb_substr($supplier['entitle'], 0, 12, 'UTF-8').'...', $supplier['entitle']).'</span></div>
					<div class="mmlistb">
						<div class="normal bold">'.$linkman.' <span class="small">('.$supplier['position'].')</span></div>
						<div><span class="small gray">邮箱：</span><a href="mailto:'.$supplier['email'].'">'.$email.'</a></div>
						<div><span class="small gray">电话：</span>'.$telephone.'</div>';
					if($supplier['fax']!=''){
						$body.='<div><span class="small gray">传真：</span>'.$supplier['fax'].'</div>';
					}
					if($supplier['mobile']!=''){
						$body.='<div><span class="small gray">手机：</span>'.$mobile.'</div>';
					}
					$body.='
						<div><span class="small gray">地区：</span>'.$region.'</div>
						<div class="small clear"><span class="right" title="'.$creator.'">'.$modifier.'</span></div>
					</div>
					</li>';
				}
				$i++;
			}
			if($this->kclass->input['layout']=='list'){
				$body .= '</tbody></table>';
			}else{
				$body .= '</ul>';
			}
		}else{
			$body = '暂无相关记录。';
		}
		if($this->kclass->input['layout']=='list'){
			$layoutLink='列表 <a href="/s.php?module=supplier&action=list&layout=grid'.str_replace('&layout=list', '', $queryPart).'">格子</a>';
		}else{
			$layoutLink='<a href="/s.php?module=supplier&action=list&layout=list'.str_replace('&layout=grid', '', $queryPart).'">列表</a> 格子';
		}
		if($this->kclass->input['show']=='all'){
			$showLink='<a href="/s.php?module=supplier&action=list'.str_replace('&show=all', '', $queryPart).'">默认</a> 所有<span class="small gray">(包括删除)</span>';
		}else{
			$showLink='默认 <a href="/s.php?module=supplier&action=list&show=all'.str_replace('&show=all', '', $queryPart).'">所有<span class="small gray">(包括删除)</span></a>';
		}

		$this->kclass->page['onload'] .= "dc.listhover();dc.tabhover();$('.mytable').fixedtableheader();$('.tablesorter').tablesorter( {sortList: [[0,1]], headers: { 5: { sorter: false}  } } );";
		$this->kclass->page['title'] .= ' - 列表';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 列表<span class="tiny">('.$sum.')</span>', 'right' => $this->right.'　|　<span class="small">排序：</span>'.$orderby['link'].'|　<span class="small">显示：</span>'.$showLink.'　|　<span class="small">布局：</span>'.$layoutLink.'　|　<a href="/s.php?module=supplier&action=add">新建</a>', 'body'=>$body));
	}
	//
	function insertInquiry(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			if(strlen($this->kclass->input['contact']) < 2){
				$e .= '<li>请填写询价供应商的相关联系人（不能少于2个字符）。</li>';
			}
			if($this->kclass->input['currencyid'] <= 0){
				$e .= '<li>请选择要供应商询价的币种。</li>';
			}
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '新建发往供应商的询价单',
				'text' => '您在新建发往供应商的询价单的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$this->kclass->DB->query("
			INSERT INTO `inquiry` (`module`, `mid`, `contact`, `currencyid`, `portid`, `deliverytermid`, `paymenttermid`, `shipmethodid`, `standard`, `material`, `technics`, `remark`, `modified`, `creator`, `created`) VALUES ('supplier', '".$this->kclass->input['supplierid']."', '".$this->kclass->input['contact']."', '".$this->kclass->input['currencyid']."', '".$this->kclass->input['portid']."', '".$this->kclass->input['deliverytermid']."', '".$this->kclass->input['paymenttermid']."', '".$this->kclass->input['shipmethodid']."', '".$this->kclass->input['standard']."', '".$this->kclass->input['material']."', '".$this->kclass->input['technics']."', '".$this->kclass->input['remark']."', '".TIMENOW."', '".$this->kclass->user['userid']."', '".TIMENOW."')
		");
		$k = count($this->kclass->input['itemProductid']);
		if($k>0){
			$quantity=0;
			for($i = 1; $i < ($k+1); $i++){
				if($this->kclass->input['itemProductid'][$i] >0 AND $this->kclass->input['itemQuantity'][$i]!='' AND $this->kclass->input['itemUnitid'][$i]>0){
					$this->kclass->DB->query("
						INSERT INTO `item` (`module`,`mid`,`title`,`model`,`caption`,`supplierNo`,`packingid`,`quantity`,`discount`,`unitid`,`modified`,`creator`,`created`)
						VALUES (
						'product', '".$this->kclass->input['itemProductid'][$i]."', '".$this->kclass->input['itemTitle'][$i]."', '".$this->kclass->input['itemModel'][$i]."', '".$this->kclass->input['itemCaption'][$i]."',  '".$this->kclass->input['itemSupplierNo'][$i]."', '".$this->kclass->input['itemPackingid'][$i]."', '".$this->kclass->input['itemQuantity'][$i]."', '".$this->kclass->input['itemDiscount'][$i]."', '".$this->kclass->input['itemUnitid'][$i]."', '".TIMENOW."', '".$this->kclass->user['userid']."', '".TIMENOW."')
					");
					$quantity+=$this->kclass->input['itemQuantity'][$i];
				}
			}
			if($quantity>0){
				$this->kclass->DB->query("UPDATE `sample` SET quantity='".$quantity."' WHERE sampleid='".$sampleid."'");
			}
		}

		$this->kclass->messager(array(
			'title' => '新建发往供应商的询价单',
			'text' => '发往供应商<b>'.$this->kclass->input['title'].'</b> 的询价单已新建成功!',
			'url' => '/s.php?module=supplier&action=view&supplierid='.$this->kclass->input['supplierid'],
			'sec' => 2
		));
	}

	//
	function addInquiry(){
		if($this->kclass->input['supplierid']<=0){
			$this->kclass->boinkIt('/s.php?module=supplier');
		}
		$supplier=$this->kclass->DB->queryFirst("SELECT supplierid, title FROM `supplier` WHERE killed=0 AND supplierid='".$this->kclass->input['supplierid']."'");
		$currency = $this->kclass->chooserCurrency(array('name'=>'currencyid', 'hasBlank'=>1, 'width'=>100, 'selectedid'=>3));
		$shipmethod = $this->kclass->chooserShipmethod(array('name'=>'shipmethodid', 'hasBlank'=>1, 'width'=>450, 'selectedid'=>$this->kclass->input['shipmethodid']));
		$paymentterm = $this->kclass->chooserPaymentterm(array('name'=>'paymenttermid', 'hasBlank'=>1, 'width'=>450, 'selectedid'=>$this->kclass->input['paymenttermid']));
		$deliveryterm = $this->kclass->chooserDeliveryterm(array('name'=>'deliverytermid', 'hasBlank'=>1, 'width'=>450, 'selectedid'=>$this->kclass->input['deliverytermid']));
		$port = $this->kclass->chooserPort(array('name'=>'portid', 'hasBlank'=>1, 'width'=>450, 'selectedid'=>9));
		for($i = 1; $i < 6; $i++){
			$itemtr .= '<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').'">
			<td>'.$this->kclass->chooserProduct(array('name'=>'itemProductid['.$i.']', 'hasBlank'=>1, 'width'=>340)).'</td>
			<td><input type="text" name="itemSupplierNo['.$i.']" value="'.$item['supplierNo'].'" size="12"></td>
			<td><input type="text" name="itemTitle['.$i.']" value="'.$item['title'].'" size="15"></td>
			<td><input type="text" name="itemModel['.$i.']" value="'.$item['model'].'" size="10"></td>
			<td><input type="text" name="itemCaption['.$i.']" value="'.$item['caption'].'" size="16"></td>
			<td>'.$this->kclass->chooserPacking(array('name'=>'itemPackingid['.$i.']', 'hasBlank'=>1, 'width'=>154)).'</td>
			<td><input type="text" name="itemDiscount['.$i.']" value="'.$item['discount'].'" size="3"></td>
			<td><input type="text" name="itemQuantity['.$i.']" value="'.$item['quantity'].'" size="5"></td>
			<td>'.$this->kclass->chooserUnit(array('name'=>'itemUnitid['.$i.']', 'hasBlank'=>1, 'width'=>100, 'selectedid'=>$this->kclass->input['unitid'])).'</td>
			</tr>';
		}
$body = <<<EOF
<form action="/s.php?module=supplier&action=insertInquiry" name="supplier" method="post">
<input type="hidden" name="module" value="supplier">
<input type="hidden" name="action" value="insertInquiry">
<input type="hidden" name="supplierid" value="{$this->kclass->input['supplierid']}">
<table class="hundred">
<thead>
<tr>
	<th colspan="4">新建发往供应商 <span class="big bold">{$supplier['title']}</span> 的 <span class="middle bold">询价单</span></th>
</tr>
</thead>
<tbody>
<tr class="even">
	<td>联 系 人：<span class="red bold">*</span></td>
	<td><input type="text" style="width:244px" name="contact" value="{$inquiry['contact']}"> <span class="small gray">指供应商方询价人员</span></td>
	<td>币　　种：<span class="red bold">*</span></td>
	<td>{$currency}</td>
</tr>
<tr class="even">
	<td>运送方式：</td>
	<td>{$shipmethod}</td>
	<td>交货地点：</td>
	<td>{$port}</td>
</tr>
<tr class="odd">
	<td>支付条款：</td>
	<td>{$paymentterm}</td>
	<td>交货条款：</td>
	<td>{$deliveryterm}</td>
</tr>
</tbody>
</table>
<table class="hundred">
<thead>
<tr>
	<th colspan="9">产品明细表</th>
</tr>
<tr><th>询价产品或配件<span class="red bold">*</span></th><th>供应商产品编号</th><th>供应商使用的名称</th><th>供应商的型号</th><th>说明</th><th>包装</th><th>折扣</th><th>数量<span class="red bold">*</span></th><th>单位<span class="red bold">*</span></th></tr>
</thead>
<tbody class="small">
{$itemtr}
<tr class="even">
	<td colspan="9" class="gray small">注：①如行数不够，请保存后再进入修改页面。②至少有“产品”、“数量”、“单位”三个项目选择填写，该明细才会被保存。③“折扣”请填写百分比“%”。</td>
</tr>
</tbody>
<table class="hundred">
<tbody>
<tr class="odd">
	<td valign="top">规格说明：</td>
	<td><textarea name="standard" style="width:450px;height:60px">{$inquiry['standard']}</textarea></td>
	<td valign="top">主要材料：</td>
	<td><textarea name="material" style="width:450px;height:60px">{$inquiry['material']}</textarea></td>
</tr>
<tr class="even">
	<td valign="top">工艺说明：</td>
	<td><textarea name="technics" style="width:450px;height:60px">{$inquiry['technics']}</textarea></td>
	<td valign="top">备　　注：</td>
	<td><textarea name="remark" style="width:450px;height:60px">{$inquiry['remark']}</textarea></td>
</tr>
<tr class="odd">
	<td colspan="4" align="center">
		<input type="submit" id="submitButton" value="  保存  " accesskey="s">　　
		<input type="reset" value="  复位  ">
	</td>
</tr>
</tbody>
</table>
</form>
EOF;
		$this->kclass->page['title'] .= ' - '.$supplier['title'].' - 新建供应商询价单';
		$this->kclass->page['onload'] .= 'dc.tabhover()';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - <a href="/s.php?module=supplier&action=view&supplierid='.$supplier['supplierid'].'">'.$supplier['title'].'</a> - 新建供应商询价单', 'right' => '<a href="/s.php?module=supplier">返回列表</a>', 'body'=>$body));
	}

	//
	function doupdateInquiry(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			if(strlen($this->kclass->input['contact']) < 2){
				$e .= '<li>请填写询价供应商的相关联系人（不能少于2个字符）。</li>';
			}
			if($this->kclass->input['currencyid'] <= 0){
				$e .= '<li>请选择要供应商询价的币种。</li>';
			}
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '修改发往供应商的询价单',
				'text' => '您在修改发往供应商的询价单的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		for($i = 1; $i < count($this->kclass->input['itemTitle'])+1; $i++){
			if($this->kclass->input['itemId'][$i]>0){
				if($this->kclass->input['itemKill'][$i]=='on'){
					$this->kclass->DB->query("DELETE FROM `item` WHERE itemid='".$this->kclass->input['itemId'][$i]."'");
				}else{
					$this->kclass->DB->query("
						UPDATE `item` SET 
							`productid`='".$this->kclass->input['itemProductid'][$i]."', 
							`title`='".$this->kclass->input['itemTitle'][$i]."', 
							`model`='".$this->kclass->input['itemModel'][$i]."', 
							`caption`='".$this->kclass->input['itemCaption'][$i]."', 
							`supplierNo`='".$this->kclass->input['itemSupplierNo'][$i]."', 
							`quantity`='".$this->kclass->input['itemQuantity'][$i]."', 
							`unitid`='".$this->kclass->input['itemUnitid'][$i]."', 
							`discount`='".$this->kclass->input['itemDiscount'][$i]."', 
							`packingid`='".$this->kclass->input['itemPackingid'][$i]."', 
							`modified`='".TIMENOW."', 
							`modifier`='".$this->kclass->user['userid']."'
						WHERE itemid='".$this->kclass->input['itemId'][$i]."'
					");
					$quantity+=$this->kclass->input['itemQuantity'][$i];
				}
			}elseif($this->kclass->input['itemProductid'][$i] >0 AND $this->kclass->input['itemQuantity'][$i]!='' AND $this->kclass->input['itemUnitid'][$i]>0){
				$this->kclass->DB->query("
					INSERT INTO `item` (`module`,`mid`,`productid`,`title`,`model`,`caption`,`supplierNo`,`packingid`,`quantity`,`discount`,`unitid`,`modified`,`creator`,`created`)
					VALUES (
					'inquiry', '".$this->kclass->input['inquiryid']."', '".$this->kclass->input['itemProductid'][$i]."', '".$this->kclass->input['itemTitle'][$i]."', '".$this->kclass->input['itemModel'][$i]."', '".$this->kclass->input['itemCaption'][$i]."',  '".$this->kclass->input['itemSupplierNo'][$i]."', '".$this->kclass->input['itemPackingid'][$i]."', '".$this->kclass->input['itemQuantity'][$i]."', '".$this->kclass->input['itemDiscount'][$i]."', '".$this->kclass->input['itemUnitid'][$i]."', '".TIMENOW."', '".$this->kclass->user['userid']."', '".TIMENOW."')
				");
				$quantity+=$this->kclass->input['itemQuantity'][$i];
			}
		}
		$this->kclass->DB->query("
			UPDATE `inquiry` 
			SET `contact` = '".$this->kclass->input['contact']."', 
				`currencyid` = '".$this->kclass->input['currencyid']."', 
				`portid` = '".$this->kclass->input['portid']."', 
				`deliverytermid` = '".$this->kclass->input['deliverytermid']."', 
				`paymenttermid` = '".$this->kclass->input['paymenttermid']."', 
				`shipmethodid` = '".$this->kclass->input['shipmethodid']."', 
				`standard` = '".$this->kclass->input['standard']."', 
				`technics` = '".$this->kclass->input['technics']."', 
				`material` = '".$this->kclass->input['material']."', 
				`remark` = '".$this->kclass->input['remark']."', 
				`modified` = '".TIMENOW."', 
				`modifier` = '".$this->kclass->user['userid']."'
			WHERE inquiryid='".$this->kclass->input['inquiryid']."'
		");
		$this->kclass->messager(array(
			'title' => '修改发往供应商的询价单',
			'text' => '发往供应商<b>'.$this->kclass->input['title'].'</b> 的询价单已修改成功!',
			'url' => '/s.php?module=supplier&action=view&supplierid='.$this->kclass->input['supplierid'],
			'sec' => 2
		));
	}

	//
	function updateInquiry(){
		if($this->kclass->input['inquiryid']<=0 OR !$inquiry = $this->kclass->DB->queryFirst("SELECT `inquiry`.*, supplier.title AS supplier FROM `inquiry` LEFT JOIN supplier ON (supplier.supplierid=inquiry.supplierid) WHERE `inquiryid`='".$this->kclass->input['inquiryid']."'")){
			$this->kclass->boinkIt('/s.php?module=inquiry');
		}
		$this->kclass->br2nl=true;
		$inquiry['standard']=$this->kclass->parseConvertValue($inquiry['standard']);
		$inquiry['material']=$this->kclass->parseConvertValue($inquiry['material']);
		$inquiry['technics']=$this->kclass->parseConvertValue($inquiry['technics']);
		$inquiry['remark']=$this->kclass->parseConvertValue($inquiry['remark']);

		$currency = $this->kclass->chooserCurrency(array('name'=>'currencyid', 'hasBlank'=>1, 'width'=>100, 'selectedid'=>$inquiry['currencyid']));
		$shipmethod = $this->kclass->chooserShipmethod(array('name'=>'shipmethodid', 'hasBlank'=>1, 'width'=>450, 'selectedid'=>$inquiry['shipmethodid']));
		$paymentterm = $this->kclass->chooserPaymentterm(array('name'=>'paymenttermid', 'hasBlank'=>1, 'width'=>450, 'selectedid'=>$inquiry['paymenttermid']));
		$deliveryterm = $this->kclass->chooserDeliveryterm(array('name'=>'deliverytermid', 'hasBlank'=>1, 'width'=>450, 'selectedid'=>$inquiry['deliverytermid']));
		$port = $this->kclass->chooserPort(array('name'=>'portid', 'hasBlank'=>1, 'width'=>450, 'selectedid'=>$inquiry['portid']));

		$items = $this->kclass->DB->query("SELECT * FROM `item` WHERE module='inquiry' AND mid='".$inquiry['inquiryid']."' ORDER BY modified ASC");
		if($this->kclass->DB->numRows()){
			$k=1;
			while($item = $this->kclass->DB->fetchArray($items)){
				$itemtr .= '<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').'">
				<td><input type="hidden" name="itemId['.$k.']" value="'.$item['itemid'].'">'.$this->kclass->chooserProduct(array('name'=>'itemProductid['.$k.']', 'hasBlank'=>1, 'width'=>340, 'selectedid'=>$item['productid'])).'</td>
				<td><input type="text" name="itemSupplierNo['.$k.']" value="'.$item['supplierNo'].'" size="12"></td>
				<td><input type="text" name="itemTitle['.$k.']" value="'.$item['title'].'" size="15"></td>
				<td><input type="text" name="itemModel['.$k.']" value="'.$item['model'].'" size="10"></td>
				<td><input type="text" name="itemCaption['.$k.']" value="'.$item['caption'].'" size="16"></td>
				<td>'.$this->kclass->chooserPacking(array('name'=>'itemPackingid['.$k.']', 'hasBlank'=>1, 'width'=>154, 'selectedid'=>$item['packingid'])).'</td>
				<td><input type="text" name="itemDiscount['.$k.']" value="'.$item['discount'].'" size="3"></td>
				<td><input type="text" name="itemQuantity['.$k.']" value="'.$item['quantity'].'" size="5"></td>
				<td>'.$this->kclass->chooserUnit(array('name'=>'itemUnitid['.$k.']', 'hasBlank'=>1, 'width'=>100, 'selectedid'=>$item['unitid'])).'</td>
				<td><input type="checkbox" name="itemKill['.$k.'] value="'.$item['itemid'].'"></td>
				</tr>';
				$k++;
			}
		}
		$itemtr.='<tr><td colspan="10">新建明细</td></tr>';
		for($i = $k; $i < ($k+5); $i++){
			$itemtr .= '<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').'">
			<td>'.$this->kclass->chooserProduct(array('name'=>'itemProductid['.$i.']', 'hasBlank'=>1, 'width'=>340)).'</td>
			<td><input type="text" name="itemSupplierNo['.$i.']" value="'.$item['supplierNo'].'" size="12"></td>
			<td><input type="text" name="itemTitle['.$i.']" value="'.$item['title'].'" size="15"></td>
			<td><input type="text" name="itemModel['.$i.']" value="'.$item['model'].'" size="10"></td>
			<td><input type="text" name="itemCaption['.$i.']" value="'.$item['caption'].'" size="16"></td>
			<td>'.$this->kclass->chooserPacking(array('name'=>'itemPackingid['.$i.']', 'hasBlank'=>1, 'width'=>154)).'</td>
			<td><input type="text" name="itemDiscount['.$i.']" value="'.$item['discount'].'" size="3"></td>
			<td><input type="text" name="itemQuantity['.$i.']" value="'.$item['quantity'].'" size="5"></td>
			<td>'.$this->kclass->chooserUnit(array('name'=>'itemUnitid['.$i.']', 'hasBlank'=>1, 'width'=>100, 'selectedid'=>$item['unitid'])).'</td>
			<td></td>
			</tr>';
		}

$body = <<<EOF
<form action="/s.php?module=supplier&action=doupdateInquiry" name="supplier" method="post">
<input type="hidden" name="module" value="supplier">
<input type="hidden" name="action" value="doupdateInquiry">
<input type="hidden" name="supplierid" value="{$inquiry['supplierid']}">
<input type="hidden" name="inquiryid" value="{$inquiry['inquiryid']}">
<table class="hundred">
<thead>
<tr>
	<th colspan="4">修改发往供应商 <span class="big bold">{$supplier['title']}</span> 的 <span class="middle bold">询价单</span></th>
</tr>
</thead>
<tbody>
<tr class="even">
	<td>联 系 人：<span class="red bold">*</span></td>
	<td><input type="text" style="width:244px" name="contact" value="{$inquiry['contact']}"> <span class="small gray">指供应商方询价人员</span></td>
	<td>币　　种：<span class="red bold">*</span></td>
	<td>{$currency}</td>
</tr>
<tr class="even">
	<td>运送方式：</td>
	<td>{$shipmethod}</td>
	<td>交货地点：</td>
	<td>{$port}</td>
</tr>
<tr class="odd">
	<td>支付条款：</td>
	<td>{$paymentterm}</td>
	<td>交货条款：</td>
	<td>{$deliveryterm}</td>
</tr>
</tbody>
</table>
<table class="hundred">
<thead>
<tr>
	<th colspan="10">产品明细表</th>
</tr>
<tr><th>询价产品<span class="red bold">*</span></th><th><span class="small">供应商</span>产品编号</th><th><span class="small">供应商</span>使用的名称</th><th><span class="small">供应商</span>的型号</th><th>说明</th><th>包装</th><th>折扣</th><th>数量<span class="red bold">*</span></th><th>单位<span class="red bold">*</span></th><th>删</th></tr>
</thead>
<tbody class="small">
{$itemtr}
<tr class="even">
	<td colspan="10" class="gray small">注：①如行数不够，请保存后再进入修改页面。②至少有“产品”、“数量”、“单位”三个项目选择填写，该明细才会被保存。③“折扣”请填写百分比“%”。</td>
</tr>
</tbody>
<table class="hundred">
<tbody>
<tr class="odd">
	<td valign="top">规格说明：</td>
	<td><textarea name="standard" style="width:450px;height:60px">{$inquiry['standard']}</textarea></td>
	<td valign="top">主要材料：</td>
	<td><textarea name="material" style="width:450px;height:60px">{$inquiry['material']}</textarea></td>
</tr>
<tr class="even">
	<td valign="top">工艺说明：</td>
	<td><textarea name="technics" style="width:450px;height:60px">{$inquiry['technics']}</textarea></td>
	<td valign="top">备　　注：</td>
	<td><textarea name="remark" style="width:450px;height:60px">{$inquiry['remark']}</textarea></td>
</tr>
<tr class="odd">
	<td colspan="4" align="center">
		<input type="submit" id="submitButton" value="  保存  " accesskey="s">　　
		<input type="reset" value="  复位  ">
	</td>
</tr>
</tbody>
</table>
</form>
EOF;
		$this->kclass->page['title'] .= ' - '.$inquiry['supplier'].' - 修改供应商询价单';
		$this->kclass->page['onload'] .= 'dc.tabhover()';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - <a href="/s.php?module=supplier&action=view&supplierid='.$supplier['supplierid'].'">'.$inquiry['supplier'].'</a> - 修改供应商询价单', 'right' => '<a href="/s.php?module=supplier">返回列表</a>', 'body'=>$body));
	}
	//
	function killInquiry(){
		if($this->kclass->input['inquiryid']<=0){
			$this->kclass->boinkIt('/s.php?module=supplier');
		}
		if($this->kclass->input['confirm'] == 0){
			$this->kclass->boinkIt('/s.php?module=supplier&action=view&supplierid='.$this->kclass->input['supplierid']);
		}
		if($this->kclass->input['inquiryid'] < 0){
			$e = '<li>请从正确的页面进行操作，如果此问题持续存在，请联系网站管理员。</li>';
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '删除供应商询价单',
				'text' => '您在删除供应商询价单的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$inquiry = $this->kclass->DB->queryFirst("
			SELECT inquiry.inquiryid, inquiry.supplierid, 
				supplier.title AS supplier
			FROM `inquiry` 
			LEFT JOIN supplier ON (supplier.supplierid=inquiry.supplierid) 
			WHERE inquiry.inquiryid='".$this->kclass->input['inquiryid']."'
		");
		if($inquiry){
			$this->kclass->DB->query("
				UPDATE `inquiry`
				SET killed=".TIMENOW." 
				WHERE inquiryid='".$inquiry['inquiryid']."'
			");
			$this->kclass->messager(array(
				'title' => '删除供应商询价单成功',
				'text' => '供应商 <b>'.$inquiryid['supplier'].'</b> 下的 询价单 已成功被标记为删除!',
				'url' => '/s.php?module=supplier&action=view&supplierid='.$inquiry['supplierid'],
				'sec' => 2
			));
		}else{
			$this->kclass->messager(array(
				'title' => '删除供应商询价单失败',
				'text' => '您要删除的供应商询价单，不存在！',
				'url' => '/s.php?module=supplier&action=view&supplierid='.$inquiry['supplierid'],
				'sec' => 3
			));
		}
	}

	//
	function removeInquiry(){
		if($this->kclass->input['inquiryid']<=0){
			$this->kclass->boinkIt('/s.php?module=supplier');
		}
		$inquiry = $this->kclass->DB->queryFirst("
			SELECT inquiry.inquiryid, inquiry.supplierid, 
				supplier.title AS supplier
			FROM `inquiry` 
			LEFT JOIN supplier ON (supplier.supplierid=inquiry.supplierid) 
			WHERE inquiry.inquiryid='".$this->kclass->input['inquiryid']."'
		");
		if($inquiry){
$body = <<<EOF
<form action="/s.php?module=supplier&action=killInquiry" name="supplier" method="post">
<input type="hidden" name="module" value="supplier">
<input type="hidden" name="action" value="killInquiry">
<input type="hidden" name="supplierid" value="{$inquiry['supplierid']}">
<input type="hidden" name="inquiryid" value="{$inquiry['inquiryid']}">
<table><thead>
<thead>
<tr>
	<th>删除供应商 {$inquiry['supplier']} 的 询价单</th>
</tr>
</thead>
<tbody>
<tr class="even">
	<td class="middle">你确定要删除产品 <a href="/s.php?module=supplier&action=view&supplierid={$inquiry['supplierid']}" class="middle bold" target="_blank">{$inquiry['supplier']}</a> 的 <span class="big bold">询价单</span> 吗?</td>
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
		}else{
			$body = '要删除的询价单并不存在，请返回列表刷新后再操作。（如果问题持续存在，请联系系统管理员。）';
		}
		$this->kclass->page['title'] .= ' - '.$inquiry['supplier'].' - 删除供应商询价单';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - <a href="/s.php?module=supplier&action=view&supplierid='.$supplier['supplierid'].'">'.$inquiry['supplier'].'</a> - 删除供应商询价单', 'right' => '<a href="/s.php?module=supplier">返回列表</a>', 'body'=>$body));
	}

	//
	function revivalInquiry(){
		if($this->kclass->input['inquiryid']<=0){
			$this->kclass->boinkIt('/s.php?module=supplier');
		}
		if($this->kclass->input['confirm'] == 0){
			$this->kclass->boinkIt('/s.php?module=supplier&action=view&supplierid='.$this->kclass->input['supplierid']);
		}
		if($this->kclass->input['inquiryid'] < 0){
			$e = '<li>请从正确的页面进行操作，如果此问题持续存在，请联系网站管理员。</li>';
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '恢复供应商询价单',
				'text' => '您在恢复供应商询价单的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$inquiry = $this->kclass->DB->queryFirst("
			SELECT inquiry.inquiryid, inquiry.supplierid, 
				supplier.title AS supplier
			FROM `inquiry` 
			LEFT JOIN supplier ON (supplier.supplierid=inquiry.supplierid) 
			WHERE inquiry.inquiryid='".$this->kclass->input['inquiryid']."'
		");
		if($inquiry){
			$this->kclass->DB->query("
				UPDATE `inquiry`
				SET killed=0
				WHERE inquiryid='".$inquiry['inquiryid']."'
			");
			$this->kclass->messager(array(
				'title' => '恢复供应商询价单成功',
				'text' => '供应商 <b>'.$inquiryid['supplier'].'</b> 下的 询价单 已成功被标记为恢复!',
				'url' => '/s.php?module=supplier&action=view&supplierid='.$inquiry['supplierid'],
				'sec' => 2
			));
		}else{
			$this->kclass->messager(array(
				'title' => '恢复供应商询价单失败',
				'text' => '您要恢复的供应商询价单，不存在！',
				'url' => '/s.php?module=supplier&action=view&supplierid='.$inquiry['supplierid'],
				'sec' => 3
			));
		}
	}

	//
	function restoreInquiry(){
		if($this->kclass->input['inquiryid']<=0){
			$this->kclass->boinkIt('/s.php?module=supplier');
		}
		$inquiry = $this->kclass->DB->queryFirst("
			SELECT inquiry.inquiryid, inquiry.supplierid, 
				supplier.title AS supplier
			FROM `inquiry` 
			LEFT JOIN supplier ON (supplier.supplierid=inquiry.supplierid) 
			WHERE inquiry.inquiryid='".$this->kclass->input['inquiryid']."'
		");
		if($inquiry){
$body = <<<EOF
<form action="/s.php?module=supplier&action=revivalInquiry" name="supplier" method="post">
<input type="hidden" name="module" value="supplier">
<input type="hidden" name="action" value="revivalInquiry">
<input type="hidden" name="supplierid" value="{$inquiry['supplierid']}">
<input type="hidden" name="inquiryid" value="{$inquiry['inquiryid']}">
<table><thead>
<thead>
<tr>
	<th>删除供应商 {$inquiry['supplier']} 的 询价单</th>
</tr>
</thead>
<tbody>
<tr class="even">
	<td class="middle">你确定要恢复产品 <a href="/s.php?module=supplier&action=view&supplierid={$inquiry['supplierid']}" class="middle bold" target="_blank">{$inquiry['supplier']}</a> 的 <span class="big bold">询价单</span> 吗?</td>
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
		}else{
			$body = '要恢复的询价单并不存在，请返回列表刷新后再操作。（如果问题持续存在，请联系系统管理员。）';
		}
		$this->kclass->page['title'] .= ' - '.$inquiry['supplier'].' - 恢复供应商询价单';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - <a href="/s.php?module=supplier&action=view&supplierid='.$supplier['supplierid'].'">'.$inquiry['supplier'].'</a> - 恢复供应商询价单', 'right' => '<a href="/s.php?module=supplier">返回列表</a>', 'body'=>$body));
	}
	//
	function insertQuoting(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			if($this->kclass->input['inquiryid']>0){
				$title = '供应商对询价的回复 & 新建报价单';
			}else{
				$title = '新建来自供应商的报价单';
			}
			if(strlen($this->kclass->input['contact']) < 2){
				$e .= '<li>请填写询价供应商的相关联系人（不能少于2个字符）。</li>';
			}
			if($this->kclass->input['currencyid'] <= 0){
				$e .= '<li>请选择要供应商询价的币种。</li>';
			}
			$j = count($this->kclass->input['itemTitle']);
			$hasItem = 0;
			for($i = 1; $i < $j+1; $i++){
				if($this->kclass->input['itemProductid'][$i]>0 AND $this->kclass->input['itemQuantity'][$i]!='' AND $this->kclass->input['itemUnitid'][$i]>0 AND $this->kclass->input['itemPrice'][$i]>0){
					$hasItem=1;
				}
			}
			if($hasItem==0){
				$e .= '<li>需要填写至少有一条报价条目明细，才能建立一份报价单。</li>';
			}
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => $title,
				'text' => '您在修改供应商询价单的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$this->kclass->input['inquiryid'] = intVal($this->kclass->input['inquiryid']);
		$this->kclass->DB->query("
			INSERT INTO `quoting` (`supplierid`, `inquiryid`, `contact`, `currencyid`, `portid`, `deliverytermid`, `paymenttermid`, `shipmethodid`, `standard`, `material`, `technics`, `remark`, `modified`, `creator`, `created`) VALUES ('".$this->kclass->input['supplierid']."', '".$this->kclass->input['inquiryid']."', '".$this->kclass->input['contact']."', '".$this->kclass->input['currencyid']."', '".$this->kclass->input['portid']."', '".$this->kclass->input['deliverytermid']."', '".$this->kclass->input['paymenttermid']."', '".$this->kclass->input['shipmethodid']."', '".$this->kclass->input['standard']."', '".$this->kclass->input['material']."', '".$this->kclass->input['technics']."', '".$this->kclass->input['remark']."', '".TIMENOW."', '".$this->kclass->user['userid']."', '".TIMENOW."')
		");
		$quotingid = $this->kclass->DB->insertID();
		if($j>0){
			for($i = 1; $i < ($j+1); $i++){
				if($this->kclass->input['itemProductid'][$i]>0 AND $this->kclass->input['itemQuantity'][$i]!='' AND $this->kclass->input['itemUnitid'][$i]>0 AND $this->kclass->input['itemPrice'][$i]>0){
					$this->kclass->DB->query("
						INSERT INTO `item` (`module`,`mid`,`productid`,`title`,`model`,`caption`,`supplierNo`,`packingid`,`quantity`,`discount`,`unitid`,`price`,`modified`,`creator`,`created`)
						VALUES (
						'quoting', '".$quotingid."', '".$this->kclass->input['itemProductid'][$i]."', '".$this->kclass->input['itemTitle'][$i]."', '".$this->kclass->input['itemModel'][$i]."', '".$this->kclass->input['itemCaption'][$i]."',  '".$this->kclass->input['itemCustomerNo'][$i]."', '".$this->kclass->input['itemPackingid'][$i]."', '".$this->kclass->input['itemQuantity'][$i]."', '".$this->kclass->input['itemDiscount'][$i]."', '".$this->kclass->input['itemUnitid'][$i]."', '".$this->kclass->input['itemPrice'][$i]."', '".TIMENOW."', '".$this->kclass->user['userid']."', '".TIMENOW."')
					");
				}
			}
		}
		if($this->kclass->input['inquiryid']>0){
			$this->kclass->DB->query("UPDATE `inquiry` SET quotingid='".$quotingid."', replied='".TIMENOW."', replyTimes=(replyTimes+1) WHERE inquiryid='".$this->kclass->input['inquiryid']."'");
		}
		$this->kclass->messager(array(
			'title' => $title,
			'text' => '供应商<b>'.$this->kclass->input['title'].'</b> 的报价单已新建成功!',
			'url' => '/s.php?module=supplier&action=view&supplierid='.$this->kclass->input['supplierid'],
			'sec' => 2
		));
	}

	//
	function addQuoting(){
		if($this->kclass->input['supplierid']<=0){
			$this->kclass->boinkIt('/s.php?module=supplier');
		}
		$k=1;
		if($this->kclass->input['inquiryid']>0){
			$title = '供应商回复的询价 & 新建报价单';
			$inquiry = $this->kclass->DB->queryFirst("SELECT * FROM inquiry WHERE inquiryid='".$this->kclass->input['inquiryid']."'");
			$items = $this->kclass->DB->query("SELECT * FROM `item` WHERE module='inquiry' AND mid='".$inquiry['inquiryid']."' ORDER BY modified ASC");
			if($this->kclass->DB->numRows()){
				while($item = $this->kclass->DB->fetchArray($items)){
					$itemtr .= '<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').'">
					<td><input type="hidden" name="itemId['.$k.']" value="'.$item['itemid'].'">'.$this->kclass->chooserProduct(array('name'=>'itemProductid['.$k.']', 'hasBlank'=>1, 'width'=>300, 'selectedid'=>$item['productid'])).'</td>
					<td><input type="text" name="itemSupplierNo['.$k.']" value="'.$item['supplierNo'].'" size="12"></td>
					<td><input type="text" name="itemTitle['.$k.']" value="'.$item['title'].'" size="15"></td>
					<td><input type="text" name="itemModel['.$k.']" value="'.$item['model'].'" size="10"></td>
					<td><input type="text" name="itemCaption['.$k.']" value="'.$item['caption'].'" size="16"></td>
					<td>'.$this->kclass->chooserPacking(array('name'=>'itemPackingid['.$k.']', 'hasBlank'=>1, 'width'=>154, 'selectedid'=>$item['packingid'])).'</td>
					<td><input type="text" name="itemDiscount['.$k.']" value="'.$item['discount'].'" size="3"></td>
					<td><input type="text" name="itemQuantity['.$k.']" value="'.$item['quantity'].'" size="5"></td>
					<td>'.$this->kclass->chooserUnit(array('name'=>'itemUnitid['.$k.']', 'hasBlank'=>1, 'width'=>100, 'selectedid'=>$item['unitid'])).'</td>
					<td><input type="text" name="itemPrice['.$k.']" value="" size="5"></td>
					</tr>';
					$k++;
				}
			}
		}else{
			$title = '新建来自供应商回复的报价单';
		}
		$supplier=$this->kclass->DB->queryFirst("SELECT supplierid, title FROM `supplier` WHERE killed=0 AND supplierid='".$this->kclass->input['supplierid']."'");
		$currency = $this->kclass->chooserCurrency(array('name'=>'currencyid', 'hasBlank'=>1, 'width'=>100, 'selectedid'=>3));
		$packing = $this->kclass->chooserPacking(array('name'=>'packingid', 'hasBlank'=>1, 'width'=>140, 'selectedid'=>$inquiry['packingid']));
		$shipmethod = $this->kclass->chooserShipmethod(array('name'=>'shipmethodid', 'hasBlank'=>1, 'width'=>450, 'selectedid'=>$inquiry['shipmethodid']));
		$paymentterm = $this->kclass->chooserPaymentterm(array('name'=>'paymenttermid', 'hasBlank'=>1, 'width'=>450, 'selectedid'=>$inquiry['paymenttermid']));
		$deliveryterm = $this->kclass->chooserDeliveryterm(array('name'=>'deliverytermid', 'hasBlank'=>1, 'width'=>450, 'selectedid'=>$inquiry['deliverytermid']));
		$port = $this->kclass->chooserPort(array('name'=>'portid', 'hasBlank'=>1, 'width'=>450, 'selectedid'=>$inquiry['portid']));

		for($i = $k; $i < ($k+6); $i++){
			$itemtr .= '<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').'">
			<td>'.$this->kclass->chooserProduct(array('name'=>'itemProductid['.$i.']', 'hasBlank'=>1, 'width'=>300)).'</td>
			<td><input type="text" name="itemSupplierNo['.$i.']" value="'.$item['supplierNo'].'" size="12"></td>
			<td><input type="text" name="itemTitle['.$i.']" value="'.$item['title'].'" size="15"></td>
			<td><input type="text" name="itemModel['.$i.']" value="'.$item['model'].'" size="10"></td>
			<td><input type="text" name="itemCaption['.$i.']" value="'.$item['caption'].'" size="16"></td>
			<td>'.$this->kclass->chooserPacking(array('name'=>'itemPackingid['.$i.']', 'hasBlank'=>1, 'width'=>154)).'</td>
			<td><input type="text" name="itemDiscount['.$i.']" value="'.$item['discount'].'" size="3"></td>
			<td><input type="text" name="itemQuantity['.$i.']" value="'.$item['quantity'].'" size="5"></td>
			<td>'.$this->kclass->chooserUnit(array('name'=>'itemUnitid['.$i.']', 'hasBlank'=>1, 'width'=>100, 'selectedid'=>$this->kclass->input['unitid'])).'</td>
			<td><input type="text" name="itemPrice['.$i.']" value="'.$item['price'].'" size="5"></td>
			</tr>';
		}

$body = <<<EOF
<form action="/s.php?module=supplier&action=insertQuoting" name="supplier" method="post">
<input type="hidden" name="module" value="supplier">
<input type="hidden" name="action" value="insertQuoting">
<input type="hidden" name="supplierid" value="{$this->kclass->input['supplierid']}">
<input type="hidden" name="inquiryid" value="{$this->kclass->input['inquiryid']}">
<table class="hundred">
<thead>
<tr>
	<th colspan="4">{$title}</th>
</tr>
</thead>
<tbody>
<tr class="odd">
	<td title="Contact">联 系 人：<span class="red bold">*</span></td>
	<td><input type="text" style="width:250px" name="contact" value="{$inquiry['contact']}"> <span class="small gray">指供应商方询价人员</span></td>
	<td title="Currency">币　　种：</td>
	<td>{$currency}</td>
</tr>
<tr class="odd">
	<td title="Shipment Method">运送方式：</td>
	<td>{$shipmethod}</td>
	<td title="Port">交货地点：</td>
	<td>{$port}</td>
</tr>
<tr class="even">
	<td title="Payment Term">支付条款：</td>
	<td>{$paymentterm}</td>
	<td title="Delivery Term">交货条款：</td>
	<td>{$deliveryterm}</td>
</tr>
</tbody>
</table>
<table class="hundred">
<thead>
<tr>
	<th colspan="10">产品明细表</th>
</tr>
<tr><th>询价产品或配件<span class="red bold">*</span></th><th><span class="small">供应商</span>产品编号</th><th><span class="small">供应商</span>使用的名称</th><th><span class="small">供应商</span>的型号</th><th>说明</th><th>包装</th><th>折扣</th><th>数量<span class="red bold">*</span></th><th>单位<span class="red bold">*</span></th><th>单价<span class="red bold">*</span></th></tr>
</thead>
<tbody class="small">
{$itemtr}
<tr class="even">
	<td colspan="10" class="gray small">注：①如行数不够，请保存后再进入修改页面。②至少有“产品”、“数量”、“单位”三个项目选择填写，该明细才会被保存。③“折扣”请填写百分比“%”。</td>
</tr>
</tbody>
<table class="hundred">
<tbody>
<tr class="odd">
	<td valign="top" title="Standard">规格说明：</td>
	<td><textarea name="standard" style="width:450px;height:60px">{$inquiry['standard']}</textarea></td>
	<td valign="top" title="Material">主要材料：</td>
	<td><textarea name="material" style="width:450px;height:60px">{$inquiry['material']}</textarea></td>
</tr>
<tr class="even">
	<td valign="top" title="Technics">工艺说明：</td>
	<td><textarea name="technics" style="width:450px;height:60px">{$inquiry['technics']}</textarea></td>
	<td valign="top" title="Remark">备　　注：</td>
	<td><textarea name="remark" style="width:450px;height:60px">{$inquiry['remark']}</textarea></td>
</tr>
<tr class="odd">
	<td colspan="4" class="gray">注释：①此页面内容一般情况下请使用<b>英文</b>填写。</td>
</tr>
<tr class="even">
	<td colspan="4" align="center">
		<input type="submit" id="submitButton" value="  保存  " accesskey="s">　　
		<input type="reset" value="  复位  ">
	</td>
</tr>
</tbody>
</table>
</form>
EOF;
		$this->kclass->page['title'] .= ' - '.$supplier['title'].' - '.$title;
		$this->kclass->page['onload'] .= 'dc.tabhover()';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - <a href="/s.php?module=supplier&action=view&supplierid='.$supplier['supplierid'].'">'.$supplier['title'].'</a> - '.$title, 'right' => '<a href="/s.php?module=supplier">返回列表</a>', 'body'=>$body));
	}

	//
	function doupdateQuoting(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			if(strlen($this->kclass->input['contact']) < 2){
				$e .= '<li>请填写询价供应商的相关联系人（不能少于2个字符）。</li>';
			}
			if($this->kclass->input['currencyid'] <= 0){
				$e .= '<li>请选择要供应商询价的币种。</li>';
			}
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '修改来自供应商的报价单',
				'text' => '您在修改来自供应商报价单的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		for($i = 1; $i < count($this->kclass->input['itemTitle'])+1; $i++){
			if($this->kclass->input['itemId'][$i]>0){
				if($this->kclass->input['itemKill'][$i]=='on'){
					$this->kclass->DB->query("DELETE FROM `item` WHERE itemid='".$this->kclass->input['itemId'][$i]."'");
				}else{
					$this->kclass->DB->query("
						UPDATE `item` SET 
							`productid`='".$this->kclass->input['itemProductid'][$i]."', 
							`title`='".$this->kclass->input['itemTitle'][$i]."', 
							`model`='".$this->kclass->input['itemModel'][$i]."', 
							`caption`='".$this->kclass->input['itemCaption'][$i]."', 
							`supplierNo`='".$this->kclass->input['itemCustomerNo'][$i]."', 
							`quantity`='".$this->kclass->input['itemQuantity'][$i]."', 
							`unitid`='".$this->kclass->input['itemUnitid'][$i]."', 
							`discount`='".$this->kclass->input['itemDiscount'][$i]."', 
							`packingid`='".$this->kclass->input['itemPackingid'][$i]."', 
							`modified`='".TIMENOW."', 
							`modifier`='".$this->kclass->user['userid']."'
						WHERE itemid='".$this->kclass->input['itemId'][$i]."'
					");
					$quantity+=$this->kclass->input['itemQuantity'][$i];
				}
			}elseif($this->kclass->input['itemProductid'][$i] >0 AND $this->kclass->input['itemQuantity'][$i]!='' AND $this->kclass->input['itemUnitid'][$i]>0){
				$this->kclass->DB->query("
					INSERT INTO `item` (`module`,`mid`,`productid`,`title`,`model`,`caption`,`supplierNo`,`packingid`,`quantity`,`discount`,`unitid`,`modified`,`creator`,`created`)
					VALUES (
					'quoting', '".$this->kclass->input['quotingid']."', '".$this->kclass->input['itemProductid'][$i]."', '".$this->kclass->input['itemTitle'][$i]."', '".$this->kclass->input['itemModel'][$i]."', '".$this->kclass->input['itemCaption'][$i]."',  '".$this->kclass->input['itemCustomerNo'][$i]."', '".$this->kclass->input['itemPackingid'][$i]."', '".$this->kclass->input['itemQuantity'][$i]."', '".$this->kclass->input['itemDiscount'][$i]."', '".$this->kclass->input['itemUnitid'][$i]."', '".TIMENOW."', '".$this->kclass->user['userid']."', '".TIMENOW."')
				");
				$quantity+=$this->kclass->input['itemQuantity'][$i];
			}
		}
		$this->kclass->DB->query("
			UPDATE `quoting` 
			SET `contact` = '".$this->kclass->input['contact']."', 
				`currencyid` = '".$this->kclass->input['currencyid']."', 
				`portid` = '".$this->kclass->input['portid']."', 
				`deliverytermid` = '".$this->kclass->input['deliverytermid']."', 
				`paymenttermid` = '".$this->kclass->input['paymenttermid']."', 
				`shipmethodid` = '".$this->kclass->input['shipmethodid']."', 
				`standard` = '".$this->kclass->input['standard']."', 
				`technics` = '".$this->kclass->input['technics']."', 
				`material` = '".$this->kclass->input['material']."', 
				`remark` = '".$this->kclass->input['remark']."', 
				`modified` = '".TIMENOW."', 
				`modifier` = '".$this->kclass->user['userid']."'
			WHERE quotingid='".$this->kclass->input['quotingid']."'
		");
		$this->kclass->messager(array(
			'title' => '修改来自供应商报价单',
			'text' => '来自供应商<b>'.$this->kclass->input['title'].'</b> 的报价单已修改成功!',
			'url' => '/s.php?module=supplier&action=view&supplierid='.$this->kclass->input['supplierid'],
			'sec' => 2
		));
	}

	//
	function updateQuoting(){
		if($this->kclass->input['quotingid']<=0 OR !$quoting = $this->kclass->DB->queryFirst("SELECT `quoting`.*, supplier.title AS supplier FROM `quoting` LEFT JOIN supplier ON (supplier.supplierid=quoting.supplierid) WHERE `quotingid`='".$this->kclass->input['quotingid']."'")){
			$this->kclass->boinkIt('/s.php?module=supplier');
		}
		$this->kclass->br2nl=true;
		$quoting['standard']=$this->kclass->parseConvertValue($quoting['standard']);
		$quoting['material']=$this->kclass->parseConvertValue($quoting['material']);
		$quoting['technics']=$this->kclass->parseConvertValue($quoting['technics']);
		$quoting['remark']=$this->kclass->parseConvertValue($quoting['remark']);

		$currency = $this->kclass->chooserCurrency(array('name'=>'currencyid', 'hasBlank'=>1, 'width'=>100, 'selectedid'=>$quoting['currencyid']));
		$shipmethod = $this->kclass->chooserShipmethod(array('name'=>'shipmethodid', 'hasBlank'=>1, 'width'=>450, 'selectedid'=>$quoting['shipmethodid']));
		$paymentterm = $this->kclass->chooserPaymentterm(array('name'=>'paymenttermid', 'hasBlank'=>1, 'width'=>450, 'selectedid'=>$quoting['paymenttermid']));
		$deliveryterm = $this->kclass->chooserDeliveryterm(array('name'=>'deliverytermid', 'hasBlank'=>1, 'width'=>450, 'selectedid'=>$quoting['deliverytermid']));
		$port = $this->kclass->chooserPort(array('name'=>'portid', 'hasBlank'=>1, 'width'=>450, 'selectedid'=>$quoting['portid']));

		$items = $this->kclass->DB->query("SELECT * FROM `item` WHERE module='quoting' AND mid='".$quoting['quotingid']."' ORDER BY modified ASC");
		if($this->kclass->DB->numRows()){
			$k=1;
			while($item = $this->kclass->DB->fetchArray($items)){
				$itemtr .= '<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').'">
				<td><input type="hidden" name="itemId['.$k.']" value="'.$item['itemid'].'">'.$this->kclass->chooserProduct(array('name'=>'itemProductid['.$k.']', 'hasBlank'=>1, 'width'=>340, 'selectedid'=>$item['productid'])).'</td>
				<td><input type="text" name="itemSupplierNo['.$k.']" value="'.$item['supplierNo'].'" size="12"></td>
				<td><input type="text" name="itemTitle['.$k.']" value="'.$item['title'].'" size="15"></td>
				<td><input type="text" name="itemModel['.$k.']" value="'.$item['model'].'" size="10"></td>
				<td><input type="text" name="itemCaption['.$k.']" value="'.$item['caption'].'" size="16"></td>
				<td>'.$this->kclass->chooserPacking(array('name'=>'itemPackingid['.$k.']', 'hasBlank'=>1, 'width'=>154, 'selectedid'=>$item['packingid'])).'</td>
				<td><input type="text" name="itemDiscount['.$k.']" value="'.$item['discount'].'" size="3"></td>
				<td><input type="text" name="itemQuantity['.$k.']" value="'.$item['quantity'].'" size="5"></td>
				<td>'.$this->kclass->chooserUnit(array('name'=>'itemUnitid['.$k.']', 'hasBlank'=>1, 'width'=>100, 'selectedid'=>$item['unitid'])).'</td>
				<td><input type="checkbox" name="itemKill['.$k.'] value="'.$item['itemid'].'"></td>
				</tr>';
				$k++;
			}
		}
		$itemtr.='<tr><td colspan="10">新建明细</td></tr>';
		for($i = $k; $i < ($k+5); $i++){
			$itemtr .= '<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').'">
			<td>'.$this->kclass->chooserProduct(array('name'=>'itemProductid['.$i.']', 'hasBlank'=>1, 'width'=>340)).'</td>
			<td><input type="text" name="itemSupplierNo['.$i.']" value="'.$item['supplierNo'].'" size="12"></td>
			<td><input type="text" name="itemTitle['.$i.']" value="'.$item['title'].'" size="15"></td>
			<td><input type="text" name="itemModel['.$i.']" value="'.$item['model'].'" size="10"></td>
			<td><input type="text" name="itemCaption['.$i.']" value="'.$item['caption'].'" size="16"></td>
			<td>'.$this->kclass->chooserPacking(array('name'=>'itemPackingid['.$i.']', 'hasBlank'=>1, 'width'=>154)).'</td>
			<td><input type="text" name="itemDiscount['.$i.']" value="'.$item['discount'].'" size="3"></td>
			<td><input type="text" name="itemQuantity['.$i.']" value="'.$item['quantity'].'" size="5"></td>
			<td>'.$this->kclass->chooserUnit(array('name'=>'itemUnitid['.$i.']', 'hasBlank'=>1, 'width'=>100, 'selectedid'=>$item['unitid'])).'</td>
			<td></td>
			</tr>';
		}

$body = <<<EOF
<form action="/s.php?module=supplier&action=doupdateQuoting" name="supplier" method="post">
<input type="hidden" name="module" value="supplier">
<input type="hidden" name="action" value="doupdateQuoting">
<input type="hidden" name="supplierid" value="{$quoting['supplierid']}">
<input type="hidden" name="quotingid" value="{$quoting['quotingid']}">
<table class="hundred">
<thead>
<tr>
	<th colspan="4">修改来自供应商 <span class="big bold">{$supplier['title']}</span> 的 <span class="middle bold">报价单</span></th>
</tr>
</thead>
<tbody>
<tr class="even">
	<td>联 系 人：<span class="red bold">*</span></td>
	<td><input type="text" style="width:244px" name="contact" value="{$quoting['contact']}"> <span class="small gray">指供应商方询价人员</span></td>
	<td>币　　种：<span class="red bold">*</span></td>
	<td>{$currency}</td>
</tr>
<tr class="even">
	<td>运送方式：</td>
	<td>{$shipmethod}</td>
	<td>支付条款：</td>
	<td>{$paymentterm}</td>
</tr>
<tr class="odd">
	<td>交货地点：</td>
	<td>{$port}</td>
	<td>交货条款：</td>
	<td>{$deliveryterm}</td>
</tr>
</tbody>
</table>
<table class="hundred">
<thead>
<tr>
	<th colspan="10">产品明细表</th>
</tr>
<tr><th>询价产品<span class="red bold">*</span></th><th>供应商产品编号</th><th>供应商使用的名称</th><th>供应商的型号</th><th>说明</th><th>包装</th><th>折扣</th><th>数量<span class="red bold">*</span></th><th>单位<span class="red bold">*</span></th><th>删</th></tr>
</thead>
<tbody class="small">
{$itemtr}
<tr class="even">
	<td colspan="10" class="gray small">注：①如行数不够，请保存后再进入修改页面。②至少有“产品”、“数量”、“单位”三个项目选择填写，该明细才会被保存。③“折扣”请填写百分比“%”。</td>
</tr>
</tbody>
<table class="hundred">
<tbody>
<tr class="odd">
	<td valign="top">规格说明：</td>
	<td><textarea name="standard" style="width:450px;height:60px">{$quoting['standard']}</textarea></td>
	<td valign="top">主要材料：</td>
	<td><textarea name="material" style="width:450px;height:60px">{$quoting['material']}</textarea></td>
</tr>
<tr class="even">
	<td valign="top">工艺说明：</td>
	<td><textarea name="technics" style="width:450px;height:60px">{$quoting['technics']}</textarea></td>
	<td valign="top">备　　注：</td>
	<td><textarea name="remark" style="width:450px;height:60px">{$quoting['remark']}</textarea></td>
</tr>
<tr class="odd">
	<td colspan="4" align="center">
		<input type="submit" id="submitButton" value="  保存  " accesskey="s">　　
		<input type="reset" value="  复位  ">
	</td>
</tr>
</tbody>
</table>
</form>
EOF;
		$this->kclass->page['title'] .= ' - '.$quoting['supplier'].' - 修改来自供应商报价单';
		$this->kclass->page['onload'] .= 'dc.tabhover()';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - <a href="/s.php?module=supplier&action=view&supplierid='.$supplier['supplierid'].'">'.$quoting['supplier'].'</a> - 修改来自供应商报价单', 'right' => '<a href="/s.php?module=supplier">返回列表</a>', 'body'=>$body));
	}
	//
	function killQuoting(){
		if($this->kclass->input['quotingid']<=0){
			$this->kclass->boinkIt('/s.php?module=supplier');
		}
		if($this->kclass->input['confirm'] == 0){
			$this->kclass->boinkIt('/s.php?module=supplier&action=view&supplierid='.$this->kclass->input['supplierid']);
		}
		if($this->kclass->input['quotingid'] < 0){
			$e = '<li>请从正确的页面进行操作，如果此问题持续存在，请联系网站管理员。</li>';
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '删除来自供应商的报价单',
				'text' => '您在删除来自供应商的报价单的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$quoting = $this->kclass->DB->queryFirst("
			SELECT quoting.quotingid, quoting.supplierid, 
				supplier.title AS supplier
			FROM quoting 
			LEFT JOIN supplier ON (supplier.supplierid=quoting.supplierid) 
			WHERE quoting.quotingid='".$this->kclass->input['quotingid']."'
		");
		if($quoting){
/*
			if($quoting['inquiryid']>0){
				$this->kclass->DB->query("UPDATE `inquiry` SET quotingid=0, replied=0, replyTimes=(replyTimes-1) WHERE quotingid='".$quoting['quotingid']."' AND inquiryid='".$quoting['inquiryid']."'");
			}
*/
			$this->kclass->DB->query("
				UPDATE `quoting`
				SET killed=".TIMENOW." 
				WHERE quotingid='".$quoting['quotingid']."'
			");
			$this->kclass->messager(array(
				'title' => '删除来自供应商的报价单成功',
				'text' => '来自供应商 <b>'.$quotingid['supplier'].'</b> 下的 报价单 已成功被标记为删除!',
				'url' => '/s.php?module=supplier&action=view&supplierid='.$quoting['supplierid'],
				'sec' => 2
			));
		}else{
			$this->kclass->messager(array(
				'title' => '删除来自供应商的报价单失败',
				'text' => '您要删除的产品报价单，不存在！',
				'url' => '/s.php?module=supplier&action=view&supplierid='.$quoting['supplierid'],
				'sec' => 3
			));
		}
	}

	//
	function removeQuoting(){
		if($this->kclass->input['quotingid']<=0){
			$this->kclass->boinkIt('/s.php?module=supplier');
		}
		$quoting = $this->kclass->DB->queryFirst("
			SELECT quoting.quotingid, quoting.supplierid, 
				supplier.title AS supplier
			FROM quoting 
			LEFT JOIN supplier ON (supplier.supplierid=quoting.supplierid) 
			WHERE quoting.quotingid='".$this->kclass->input['quotingid']."'
		");
		if($quoting){
$body = <<<EOF
<form action="/s.php?module=supplier&action=killQuoting" name="supplier" method="post">
<input type="hidden" name="module" value="supplier">
<input type="hidden" name="action" value="killQuoting">
<input type="hidden" name="supplierid" value="{$quoting['supplierid']}">
<input type="hidden" name="quotingid" value="{$quoting['quotingid']}">
<table><thead>
<thead>
<tr>
	<th>删除来自供应商 {$quoting['supplier']} 的 报价单</th>
</tr>
</thead>
<tbody>
<tr class="even">
	<td class="middle">你确定要删除来自供应商 <a href="/s.php?module=supplier&action=view&supplierid={$quoting['supplierid']}" class="middle bold" target="_blank">{$quoting['supplier']}</a> 的 <span class="big bold">报价单</span> 吗?</td>
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
		}else{
			$body = '要删除的报价单并不存在，请返回列表刷新后再操作。（如果问题持续存在，请联系系统管理员。）';
		}
		$this->kclass->page['title'] .= ' - '.$quoting['supplier'].' - 删除来自供应商的报价单';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - <a href="/s.php?module=supplier&action=view&supplierid='.$supplier['supplierid'].'">'.$quoting['supplier'].'</a> - 删除来自供应商的报价单', 'right' => '<a href="/s.php?module=supplier">返回列表</a>', 'body'=>$body));
	}

	//
	function revivalQuoting(){
		if($this->kclass->input['quotingid']<=0){
			$this->kclass->boinkIt('/s.php?module=supplier');
		}
		if($this->kclass->input['confirm'] == 0){
			$this->kclass->boinkIt('/s.php?module=supplier&action=view&supplierid='.$this->kclass->input['supplierid']);
		}
		if($this->kclass->input['quotingid'] < 0){
			$e = '<li>请从正确的页面进行操作，如果此问题持续存在，请联系网站管理员。</li>';
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '恢复来自供应商的报价单',
				'text' => '您在恢复来自供应商的报价单的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$quoting = $this->kclass->DB->queryFirst("
			SELECT quoting.quotingid, quoting.supplierid, 
				supplier.title AS supplier
			FROM quoting 
			LEFT JOIN supplier ON (supplier.supplierid=quoting.supplierid) 
			WHERE quoting.quotingid='".$this->kclass->input['quotingid']."'
		");
		if($quoting){
			$this->kclass->DB->query("
				UPDATE `quoting`
				SET killed=0
				WHERE quotingid='".$quoting['quotingid']."'
			");
			$this->kclass->messager(array(
				'title' => '恢复来自供应商的报价单成功',
				'text' => '供应商 <b>'.$quotingid['supplier'].'</b> 下的 报价单 已成功被标记为恢复!',
				'url' => '/s.php?module=supplier&action=view&supplierid='.$quoting['supplierid'],
				'sec' => 2
			));
		}else{
			$this->kclass->messager(array(
				'title' => '恢复来自供应商的报价单失败',
				'text' => '您要恢复来自供应商的报价单，不存在！',
				'url' => '/s.php?module=supplier&action=view&supplierid='.$quoting['supplierid'],
				'sec' => 3
			));
		}
	}

	//
	function restoreQuoting(){
		if($this->kclass->input['quotingid']<=0){
			$this->kclass->boinkIt('/s.php?module=supplier');
		}
		$quoting = $this->kclass->DB->queryFirst("
			SELECT quoting.quotingid, quoting.supplierid, 
				supplier.title AS supplier
			FROM quoting 
			LEFT JOIN supplier ON (supplier.supplierid=quoting.supplierid) 
			WHERE quoting.quotingid='".$this->kclass->input['quotingid']."'
		");
		if($quoting){
$body = <<<EOF
<form action="/s.php?module=supplier&action=revivalQuoting" name="supplier" method="post">
<input type="hidden" name="module" value="supplier">
<input type="hidden" name="action" value="revivalQuoting">
<input type="hidden" name="supplierid" value="{$quoting['supplierid']}">
<input type="hidden" name="quotingid" value="{$quoting['quotingid']}">
<table><thead>
<thead>
<tr>
	<th>恢复来自供应商 {$quoting['supplier']} 的 报价单</th>
</tr>
</thead>
<tbody>
<tr class="even">
	<td class="middle">你确定要恢复来自供应商 <a href="/s.php?module=supplier&action=view&supplierid={$quoting['supplierid']}" class="middle bold" target="_blank">{$quoting['supplier']}</a> 的 <span class="big bold">报价单</span> 吗?</td>
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
		}else{
			$body = '要恢复的报价单并不存在，请返回列表刷新后再操作。（如果问题持续存在，请联系系统管理员。）';
		}
		$this->kclass->page['title'] .= ' - '.$quoting['supplier'].' - 恢复来自供应商的报价单';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - <a href="/s.php?module=supplier&action=view&supplierid='.$supplier['supplierid'].'">'.$quoting['supplier'].'</a> - 恢复来自供应商的报价单', 'right' => '<a href="/s.php?module=supplier">返回列表</a>', 'body'=>$body));
	}
	//
	function insertVisit(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			if($this->kclass->input['startDate']=='' OR $this->kclass->input['startHour']<0 OR $this->kclass->input['startMin']<0 OR $this->kclass->input['endDate']=='' OR $this->kclass->input['endHour']<0 OR $this->kclass->input['endMin']<0){
				$e .= '<li>开始访问时间与结束访问时间，都必需选择。</li>';
			}
			if(strlen($this->kclass->input['theirstaff']) < 3){
				$e .= '<li>请填写供应商主要人员（不能少于3个字符）。</li>';
			}
			if(strlen($this->kclass->input['ourstaff']) < 3){
				$e .= '<li>请填写我方主要人员（不能少于3个字符）。</li>';
			}
			if(strlen($this->kclass->input['place']) < 3){
				$e .= '<li>请填写访问地点（不能少于3个字符）。</li>';
			}
			if(strlen($this->kclass->input['purpose']) < 3){
				$e .= '<li>请填写访问目的（不能少于3个字符）。</li>';
			}
			if(strlen($this->kclass->input['point']) < 5){
				$e .= '<li>请填写关键内容（不能少于5个字符）。</li>';
			}
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '新建互访记录',
				'text' => '您在新建互访记录的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$sd = explode('-', $this->kclass->input['startDate']);
		$start = mktime($this->kclass->input['startHour'],$this->kclass->input['startMin'],0,$sd[1],$sd[2],$sd[0]);
		$ed = explode('-', $this->kclass->input['endDate']);
		$end = mktime($this->kclass->input['endHour'],$this->kclass->input['endMin'],0,$ed[1],$ed[2],$ed[0]);
		$this->kclass->DB->query("
			INSERT INTO `visit` (`supplierid`, `start`, `end`, `theirstaff`, `ourstaff`, `place`, `purpose`, `point`, `schedule`, `exception`, `remark`, `modified`, `creator`, `created`) VALUES ('".$this->kclass->input['supplierid']."', '".$start."', '".$end."', '".$this->kclass->input['theirstaff']."', '".$this->kclass->input['ourstaff']."', '".$this->kclass->input['place']."', '".$this->kclass->input['purpose']."', '".$this->kclass->input['point']."', '".$this->kclass->input['schedule']."', '".$this->kclass->input['exception']."', '".$this->kclass->input['remark']."', '".TIMENOW."', '".$this->kclass->user['userid']."', '".TIMENOW."')
		");
		$visitid = $this->kclass->DB->insertID();
		$this->kclass->messager(array(
			'title' => '新建互访记录',
			'text' => '与供应商<b>'.$this->kclass->input['title'].'</b> 的互访记录已新建成功!',
			'url' => '/s.php?module=supplier&action=view&supplierid='.$this->kclass->input['supplierid'],
			'sec' => 2
		));
	}

	//
	function addVisit(){
		if($this->kclass->input['supplierid']<=0){
			$this->kclass->boinkIt('/s.php?module=supplier');
		}
		$supplier=$this->kclass->DB->queryFirst("SELECT supplierid, title FROM `supplier` WHERE killed=0 AND supplierid='".$this->kclass->input['supplierid']."'");
		$startHour='<select name="startHour"><option value="-1"> </option>';
		$endHour='<select name="endHour"><option value="-1"> </option>';
		$startMin='<select name="startMin"><option value="-1"> </option>';
		$endMin='<select name="endMin"><option value="-1"> </option>';
		for($i = 0; $i < 24; $i++){
			$startHour.='<option value="'.$i.'">'.sprintf('%02d', $i).'</option>';
			$endHour.='<option value="'.$i.'">'.sprintf('%02d', $i).'</option>';
		}
		for($i = 0; $i < 59; $i++){
			$startMin.='<option value="'.$i.'">'.sprintf('%02d', $i).'</option>';
			$endMin.='<option value="'.$i.'">'.sprintf('%02d', $i).'</option>';
		}
		$startHour.='</select>';
		$endHour.='</select>';
		$startMin.='</select>';
		$endMin.='</select>';

$body = <<<EOF
<form action="/s.php?module=supplier&action=insertVisit" name="supplier" method="post">
<input type="hidden" name="module" value="supplier">
<input type="hidden" name="action" value="insertVisit">
<input type="hidden" name="supplierid" value="{$this->kclass->input['supplierid']}">
<table class="hundred">
<thead>
<tr>
	<th colspan="4">我方 与 供应商互访记录</th>
</tr>
</thead>
<tbody>
<tr class="odd">
	<td title="Start">开始时间：<span class="red bold">*</span></td>
	<td>日期：<input type="text" id="startDate" name="startDate" size="12" value="{$startDate}"> {$startHour}：{$startMin} <span class="small gray">当地时间</span></td>
	<td title="End">结束时间：<span class="red bold">*</span></td>
	<td>日期：<input type="text" id="endDate" name="endDate" size="12" value="{$endDate}"> {$endHour}：{$endMin} <span class="small gray">当地时间</span></td>
</tr>
<tr class="even">
	<td title="Our Staff">供应商人员：<span class="red bold">*</span></td>
	<td><input type="text" style="width:300px" name="theirstaff" value="{$visit['theirstaff']}"> <span class="small gray">主要来访人员，以逗号隔开</span></td>
	<td title="Their Staff">我方人员：<span class="red bold">*</span></td>
	<td><input type="text" style="width:300px" name="ourstaff" value="{$visit['ourstaff']}"> <span class="small gray">主要接待人员，以逗号隔开</span></td>
</tr>
<tr class="odd">
	<td title="Place">地　　点：<span class="red bold">*</span></td>
	<td><input type="text" style="width:300px" name="place" value="{$visit['place']}"> <span class="small gray">多个地点以逗号隔开</span></td>
	<td title="Purpose">访问目的：<span class="red bold">*</span></td>
	<td><input type="text" style="width:300px" name="purpose" value="{$visit['purpose']}"> <span class="small gray">多个目的请以①②③...开始</span></td>
</tr>
<tr class="even">
	<td valign="top" title="Point">关键内容：<span class="red bold">*</span></td>
	<td><textarea name="point" style="width:450px;height:60px">{$inquiry['point']}</textarea></td>
	<td valign="top" title="Schedule">访问行程：</td>
	<td><textarea name="schedule" style="width:450px;height:60px">{$inquiry['schedule']}</textarea></td>
</tr>
<tr class="odd">
	<td valign="top" title="Exception">例外事件：</td>
	<td><textarea name="exception" style="width:450px;height:60px">{$inquiry['exception']}</textarea></td>
	<td valign="top" title="Remark">备　　注：</td>
	<td><textarea name="remark" style="width:450px;height:60px">{$inquiry['remark']}</textarea></td>
</tr>
<tr class="even">
	<td colspan="4" align="center">
		<input type="submit" id="submitButton" value="  保存  " accesskey="s">　　
		<input type="reset" value="  复位  ">
	</td>
</tr>
</tbody>
</table>
</form>
EOF;
		$this->kclass->page['title'] .= ' - '.$supplier['title'].' - 互访记录';
		$this->kclass->page['onload'] .= 'var dates=$(\'#startDate,#endDate\').datepicker({onSelect:function(selectedDate){var option=this.id==\'startDate\'?\'minDate\':\'maxDate\',instance=$(this).data(\'datepicker\'),date=$.datepicker.parseDate(instance.settings.dateFormat || $.datepicker._defaults.dateFormat,selectedDate,instance.settings);dates.not(this).datepicker(\'option\',option,date);}});dc.tabhover()';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - <a href="/s.php?module=supplier&action=view&supplierid='.$supplier['supplierid'].'">'.$supplier['title'].'</a> - 互访记录', 'right' => '<a href="/s.php?module=supplier">返回列表</a>', 'body'=>$body));
	}

	//
	function doupdateVisit(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			if(strlen($this->kclass->input['contact']) < 2){
				$e .= '<li>请填写询价供应商的相关联系人（不能少于2个字符）。</li>';
			}
			if($this->kclass->input['currencyid'] <= 0){
				$e .= '<li>请选择要供应商询价的币种。</li>';
			}
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '修改发往供应商互访记录',
				'text' => '您在修改发往供应商互访记录的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		for($i = 1; $i < count($this->kclass->input['itemTitle'])+1; $i++){
			if($this->kclass->input['itemId'][$i]>0){
				if($this->kclass->input['itemKill'][$i]=='on'){
					$this->kclass->DB->query("DELETE FROM `item` WHERE itemid='".$this->kclass->input['itemId'][$i]."'");
				}else{
					$this->kclass->DB->query("
						UPDATE `item` SET 
							`productid`='".$this->kclass->input['itemProductid'][$i]."', 
							`title`='".$this->kclass->input['itemTitle'][$i]."', 
							`model`='".$this->kclass->input['itemModel'][$i]."', 
							`caption`='".$this->kclass->input['itemCaption'][$i]."', 
							`supplierNo`='".$this->kclass->input['itemSupplierNo'][$i]."', 
							`quantity`='".$this->kclass->input['itemQuantity'][$i]."', 
							`unitid`='".$this->kclass->input['itemUnitid'][$i]."', 
							`discount`='".$this->kclass->input['itemDiscount'][$i]."', 
							`packingid`='".$this->kclass->input['itemPackingid'][$i]."', 
							`modified`='".TIMENOW."', 
							`modifier`='".$this->kclass->user['userid']."'
						WHERE itemid='".$this->kclass->input['itemId'][$i]."'
					");
					$quantity+=$this->kclass->input['itemQuantity'][$i];
				}
			}elseif($this->kclass->input['itemTitle'][$i] != '' AND $this->kclass->input['itemQuantity'][$i]!='' AND $this->kclass->input['itemUnitid'][$i]>0){
				$this->kclass->DB->query("
					INSERT INTO `item` (`visitid`, `productid`,`title`,`model`,`caption`,`supplierNo`,`packingid`,`quantity`,`discount`,`unitid`,`modified`,`creator`,`created`)
					VALUES (
					'".$this->kclass->input['visitid']."', '".$this->kclass->input['itemProductid'][$i]."', '".$this->kclass->input['itemTitle'][$i]."', '".$this->kclass->input['itemModel'][$i]."', '".$this->kclass->input['itemCaption'][$i]."',  '".$this->kclass->input['itemSupplierNo'][$i]."', '".$this->kclass->input['itemPackingid'][$i]."', '".$this->kclass->input['itemQuantity'][$i]."', '".$this->kclass->input['itemDiscount'][$i]."', '".$this->kclass->input['itemUnitid'][$i]."', '".TIMENOW."', '".$this->kclass->user['userid']."', '".TIMENOW."')
				");
				$quantity+=$this->kclass->input['itemQuantity'][$i];
			}
		}
		$this->kclass->DB->query("
			UPDATE `visit` 
			SET `contact` = '".$this->kclass->input['contact']."', 
				`currencyid` = '".$this->kclass->input['currencyid']."', 
				`portid` = '".$this->kclass->input['portid']."', 
				`deliverytermid` = '".$this->kclass->input['deliverytermid']."', 
				`paymenttermid` = '".$this->kclass->input['paymenttermid']."', 
				`shipmethodid` = '".$this->kclass->input['shipmethodid']."', 
				`standard` = '".$this->kclass->input['standard']."', 
				`technics` = '".$this->kclass->input['technics']."', 
				`material` = '".$this->kclass->input['material']."', 
				`remark` = '".$this->kclass->input['remark']."', 
				`modified` = '".TIMENOW."', 
				`modifier` = '".$this->kclass->user['userid']."'
			WHERE visitid='".$this->kclass->input['visitid']."'
		");
		$this->kclass->messager(array(
			'title' => '修改发往供应商互访记录',
			'text' => '发往供应商<b>'.$this->kclass->input['title'].'</b> 的互访记录已修改成功!',
			'url' => '/s.php?module=supplier&action=view&supplierid='.$this->kclass->input['supplierid'],
			'sec' => 2
		));
	}

	//
	function updateVisit(){
		if($this->kclass->input['visitid']<=0 OR !$visit = $this->kclass->DB->queryFirst("SELECT `visit`.*, supplier.title AS supplier FROM `visit` LEFT JOIN supplier ON (supplier.supplierid=visit.supplierid) WHERE `visitid`='".$this->kclass->input['visitid']."'")){
			$this->kclass->boinkIt('/s.php?module=supplier');
		}
		$this->kclass->br2nl=true;
		$visit['schedule']=$this->kclass->parseConvertValue($visit['schedule']);
		$visit['point']=$this->kclass->parseConvertValue($visit['point']);
		$visit['exception']=$this->kclass->parseConvertValue($visit['exception']);
		$visit['remark']=$this->kclass->parseConvertValue($visit['remark']);

		$startHour='<select name="startHour"><option value="-1"> </option>';
		$endHour='<select name="endHour"><option value="-1"> </option>';
		$startMin='<select name="startMin"><option value="-1"> </option>';
		$endMin='<select name="endMin"><option value="-1"> </option>';
		$startDate=date('Y-m-d', $visit['start']);
		$endDate=date('Y-m-d', $visit['end']);
		for($i = 0; $i < 24; $i++){
			$startHour.='<option value="'.$i.'">'.sprintf('%02d', $i).'</option>';
			$endHour.='<option value="'.$i.'">'.sprintf('%02d', $i).'</option>';
		}
		for($i = 0; $i < 59; $i++){
			$startMin.='<option value="'.$i.'">'.sprintf('%02d', $i).'</option>';
			$endMin.='<option value="'.$i.'">'.sprintf('%02d', $i).'</option>';
		}
		$startHour.='</select>';
		$endHour.='</select>';
		$startMin.='</select>';
		$endMin.='</select>';

$body = <<<EOF
<form action="/s.php?module=supplier&action=insertVisit" name="supplier" method="post">
<input type="hidden" name="module" value="supplier">
<input type="hidden" name="action" value="insertVisit">
<input type="hidden" name="supplierid" value="{$this->kclass->input['supplierid']}">
<table class="hundred">
<thead>
<tr>
	<th colspan="4">我方 与 供应商互访记录</th>
</tr>
</thead>
<tbody>
<tr class="odd">
	<td title="Start">开始时间：<span class="red bold">*</span></td>
	<td>日期：<input type="text" id="startDate" name="startDate" size="12" value="{$startDate}"> {$startHour}：{$startMin} <span class="small gray">当地时间</span></td>
	<td title="End">结束时间：<span class="red bold">*</span></td>
	<td>日期：<input type="text" id="endDate" name="endDate" size="12" value="{$endDate}"> {$endHour}：{$endMin} <span class="small gray">当地时间</span></td>
</tr>
<tr class="even">
	<td title="Our Staff">供应商人员：<span class="red bold">*</span></td>
	<td><input type="text" style="width:300px" name="theirstaff" value="{$visit['theirstaff']}"> <span class="small gray">主要来访人员，以逗号隔开</span></td>
	<td title="Their Staff">我方人员：<span class="red bold">*</span></td>
	<td><input type="text" style="width:300px" name="ourstaff" value="{$visit['ourstaff']}"> <span class="small gray">主要接待人员，以逗号隔开</span></td>
</tr>
<tr class="odd">
	<td title="Place">地　　点：<span class="red bold">*</span></td>
	<td><input type="text" style="width:300px" name="place" value="{$visit['place']}"> <span class="small gray">多个地点以逗号隔开</span></td>
	<td title="Purpose">访问目的：<span class="red bold">*</span></td>
	<td><input type="text" style="width:300px" name="purpose" value="{$visit['purpose']}"> <span class="small gray">多个目的请以①②③...开始</span></td>
</tr>
<tr class="even">
	<td valign="top" title="Point">关键内容：<span class="red bold">*</span></td>
	<td><textarea name="point" style="width:450px;height:60px">{$visit['point']}</textarea></td>
	<td valign="top" title="Schedule">访问行程：</td>
	<td><textarea name="schedule" style="width:450px;height:60px">{$visit['schedule']}</textarea></td>
</tr>
<tr class="odd">
	<td valign="top" title="Exception">例外事件：</td>
	<td><textarea name="exception" style="width:450px;height:60px">{$visit['exception']}</textarea></td>
	<td valign="top" title="Remark">备　　注：</td>
	<td><textarea name="remark" style="width:450px;height:60px">{$visit['remark']}</textarea></td>
</tr>
<tr class="even">
	<td colspan="4" align="center">
		<input type="submit" id="submitButton" value="  保存  " accesskey="s">　　
		<input type="reset" value="  复位  ">
	</td>
</tr>
</tbody>
</table>
</form>
EOF;
		$this->kclass->page['title'] .= ' - '.$visit['supplier'].' - 修改发往供应商互访记录';
		$this->kclass->page['onload'] .= 'dc.tabhover()';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - <a href="/s.php?module=supplier&action=view&supplierid='.$visit['supplierid'].'">'.$visit['supplier'].'</a> - 修改发往供应商互访记录', 'right' => '<a href="/s.php?module=supplier">返回列表</a>', 'body'=>$body));
	}
	//
	function killVisit(){
		if($this->kclass->input['visitid']<=0){
			$this->kclass->boinkIt('/s.php?module=supplier');
		}
		if($this->kclass->input['confirm'] == 0){
			$this->kclass->boinkIt('/s.php?module=supplier&action=view&supplierid='.$this->kclass->input['supplierid']);
		}
		if($this->kclass->input['visitid'] < 0){
			$e = '<li>请从正确的页面进行操作，如果此问题持续存在，请联系网站管理员。</li>';
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '删除发往供应商的互访记录',
				'text' => '您在删除发往供应商的互访记录的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$visit = $this->kclass->DB->queryFirst("
			SELECT visit.visitid, visit.supplierid, 
				supplier.title AS supplier
			FROM visit 
			LEFT JOIN supplier ON (supplier.supplierid=visit.supplierid) 
			WHERE visit.visitid='".$this->kclass->input['visitid']."'
		");
		if($visit){
/*
			if($visit['inquiryid']>0){
				$this->kclass->DB->query("UPDATE `inquiry` SET visitid=0, replied=0, replyTimes=(replyTimes-1) WHERE visitid='".$visit['visitid']."' AND inquiryid='".$visit['inquiryid']."'");
			}
*/
			$this->kclass->DB->query("
				UPDATE `visit`
				SET killed=".TIMENOW." 
				WHERE visitid='".$visit['visitid']."'
			");
			$this->kclass->messager(array(
				'title' => '删除发往供应商的互访记录成功',
				'text' => '发往供应商 <b>'.$visitid['supplier'].'</b> 下的 互访记录 已成功被标记为删除!',
				'url' => '/s.php?module=supplier&action=view&supplierid='.$visit['supplierid'],
				'sec' => 2
			));
		}else{
			$this->kclass->messager(array(
				'title' => '删除发往供应商的互访记录失败',
				'text' => '您要删除的产品互访记录，不存在！',
				'url' => '/s.php?module=supplier&action=view&supplierid='.$visit['supplierid'],
				'sec' => 3
			));
		}
	}

	//
	function removeVisit(){
		if($this->kclass->input['visitid']<=0){
			$this->kclass->boinkIt('/s.php?module=supplier');
		}
		$visit = $this->kclass->DB->queryFirst("
			SELECT visit.visitid, visit.supplierid, 
				supplier.title AS supplier
			FROM visit 
			LEFT JOIN supplier ON (supplier.supplierid=visit.supplierid) 
			WHERE visit.visitid='".$this->kclass->input['visitid']."'
		");
		if($visit){
$body = <<<EOF
<form action="/s.php?module=supplier&action=killVisit" name="supplier" method="post">
<input type="hidden" name="module" value="supplier">
<input type="hidden" name="action" value="killVisit">
<input type="hidden" name="supplierid" value="{$visit['supplierid']}">
<input type="hidden" name="visitid" value="{$visit['visitid']}">
<table><thead>
<thead>
<tr>
	<th>删除发往供应商 {$visit['supplier']} 的 互访记录</th>
</tr>
</thead>
<tbody>
<tr class="even">
	<td class="middle">你确定要删除发往供应商 <a href="/s.php?module=supplier&action=view&supplierid={$visit['supplierid']}" class="middle bold" target="_blank">{$visit['supplier']}</a> 的 <span class="big bold">互访记录</span> 吗?</td>
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
		}else{
			$body = '要删除的互访记录并不存在，请返回列表刷新后再操作。（如果问题持续存在，请联系系统管理员。）';
		}
		$this->kclass->page['title'] .= ' - '.$visit['supplier'].' - 删除发往供应商的互访记录';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - <a href="/s.php?module=supplier&action=view&supplierid='.$supplier['supplierid'].'">'.$visit['supplier'].'</a> - 删除发往供应商的互访记录', 'right' => '<a href="/s.php?module=supplier">返回列表</a>', 'body'=>$body));
	}

	//
	function revivalVisit(){
		if($this->kclass->input['visitid']<=0){
			$this->kclass->boinkIt('/s.php?module=supplier');
		}
		if($this->kclass->input['confirm'] == 0){
			$this->kclass->boinkIt('/s.php?module=supplier&action=view&supplierid='.$this->kclass->input['supplierid']);
		}
		if($this->kclass->input['visitid'] < 0){
			$e = '<li>请从正确的页面进行操作，如果此问题持续存在，请联系网站管理员。</li>';
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '恢复发往供应商的互访记录',
				'text' => '您在恢复发往供应商的互访记录的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$visit = $this->kclass->DB->queryFirst("
			SELECT visit.visitid, visit.supplierid, 
				supplier.title AS supplier
			FROM visit 
			LEFT JOIN supplier ON (supplier.supplierid=visit.supplierid) 
			WHERE visit.visitid='".$this->kclass->input['visitid']."'
		");
		if($visit){
			$this->kclass->DB->query("
				UPDATE `visit`
				SET killed=0
				WHERE visitid='".$visit['visitid']."'
			");
			$this->kclass->messager(array(
				'title' => '恢复发往供应商的互访记录成功',
				'text' => '供应商 <b>'.$visitid['supplier'].'</b> 下的 互访记录 已成功被标记为恢复!',
				'url' => '/s.php?module=supplier&action=view&supplierid='.$visit['supplierid'],
				'sec' => 2
			));
		}else{
			$this->kclass->messager(array(
				'title' => '恢复发往供应商的互访记录失败',
				'text' => '您要恢复发往供应商的互访记录，不存在！',
				'url' => '/s.php?module=supplier&action=view&supplierid='.$visit['supplierid'],
				'sec' => 3
			));
		}
	}

	//
	function restoreVisit(){
		if($this->kclass->input['visitid']<=0){
			$this->kclass->boinkIt('/s.php?module=supplier');
		}
		$visit = $this->kclass->DB->queryFirst("
			SELECT visit.visitid, visit.supplierid, 
				supplier.title AS supplier
			FROM visit 
			LEFT JOIN supplier ON (supplier.supplierid=visit.supplierid) 
			WHERE visit.visitid='".$this->kclass->input['visitid']."'
		");
		if($visit){
$body = <<<EOF
<form action="/s.php?module=supplier&action=revivalVisit" name="supplier" method="post">
<input type="hidden" name="module" value="supplier">
<input type="hidden" name="action" value="revivalVisit">
<input type="hidden" name="supplierid" value="{$visit['supplierid']}">
<input type="hidden" name="visitid" value="{$visit['visitid']}">
<table><thead>
<thead>
<tr>
	<th>恢复发往供应商 {$visit['supplier']} 的 互访记录</th>
</tr>
</thead>
<tbody>
<tr class="even">
	<td class="middle">你确定要恢复发往供应商 <a href="/s.php?module=supplier&action=view&supplierid={$visit['supplierid']}" class="middle bold" target="_blank">{$visit['supplier']}</a> 的 <span class="big bold">互访记录</span> 吗?</td>
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
		}else{
			$body = '要恢复的互访记录并不存在，请返回列表刷新后再操作。（如果问题持续存在，请联系系统管理员。）';
		}
		$this->kclass->page['title'] .= ' - '.$visit['supplier'].' - 恢复发往供应商的互访记录';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - <a href="/s.php?module=supplier&action=view&supplierid='.$supplier['supplierid'].'">'.$visit['supplier'].'</a> - 恢复发往供应商的互访记录', 'right' => '<a href="/s.php?module=supplier">返回列表</a>', 'body'=>$body));
	}

}
?>