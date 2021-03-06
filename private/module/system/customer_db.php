<?php
class customer{
	function autoRun(){
		$this->kclass->page['title'] = '客户';
		$this->baseurl = '<a href="/s.php">首页</a> - <a href="/s.php?module=customer">客户</a>';
		$this->right = '<span class="small"><a href="/s.php?module=customer&action=find">查找</a>：<form style="display:inline-block" action="/s.php?module=customer&action=list" name="customer" method="post"><input type="text" style="width:50px;height:15px;margin-top:-3px" name="title" value="'.$this->kclass->input['title'].'"><input type="submit" value="找" accesskey="s" style="height:23px"></form></span>';
		switch($this->kclass->input['action']){
			case 'list':
				$this->mmlist();
			break;
			case 'quotingstatistics':
				$this->quotingstatistics();
			break;
			case 'inquirystatistics':
				$this->inquirystatistics();
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
			case 'verify':
				$this->verify();
			break;
			case 'doverify':
				$this->doverify();
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
			case 'STQuoting':
				$this->STQuoting();
			break;
			case 'addSTQuoting':
				$this->addSTQuoting();
			break;
			case 'STQuotingview':
				$this->STQuotingview();
			break;
			case 'STQuotingremove':
				$this->STQuotingremove();
			break;
			case 'STQuotingkill':
				$this->STQuotingkill();
			break;
			case 'newQuoting':
				$this->newQuoting();
			break;
			case 'STQuotingupdate':
				$this->STQuotingupdate();
			break;
			case 'STQuotingdoupdate':
				$this->STQuotingdoupdate();
			break;
			case 'STQuotingfind':
				$this->STQuotingfind();
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
			case 'chooseLanguage':
				$this->chooseLanguage();
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
			case 'insertFeedback':
				$this->insertFeedback();
			break;
			case 'addFeedback':
				$this->addFeedback();
			break;
			case 'doupdateFeedback':
				$this->doupdateFeedback();
			break;
			case 'updateFeedback':
				$this->updateFeedback();
			break;
			case 'removeFeedback':
				$this->removeFeedback();
			break;
			case 'killFeedback':
				$this->killFeedback();
			break;
			case 'questionnaire':
				$this->questionnaire();
			break;
			case 'addquestion':
				$this->addquestion();
			break;
			case 'questionview':
				$this->questionview();
			break;
			case 'removequestion':
				$this->removequestion();
            break;
			case 'killquestionnaire':
				$this->killquestionnaire();
			break;
			case 'updatequestionnaire':
				$this->updatequestionnaire();
			break;
			case 'doupdatequestion':
				$this->doupdatequestion();
			break;
			case 'customerrelation':
				$this->customerrelation();
			break;
			case 'chooserelation':
				$this->chooserelation();
			break;
			case 'addrelation':
				$this->addrelation();
			break;
			case 'doaddrelation':
				$this->doaddrelation();
			break;
			case 'sendmail' :
				$this->sendmail();
			default:
				$this->mmlist();
		}
	}
	// 
	function view(){
		if(!$this->kclass->input['customerid'] > 0)$this->kclass->boinkIt('/s.php?module=customer');
		$customer = $this->kclass->DB->queryFirst("
			SELECT `customer`.*, 
				region.encountry AS regionEncountry, region.country AS regionCountry, region.enstate AS regionEnstate, region.state AS regionState, region.encity AS regionEncity, region.city AS regionCity, 
				b.title AS business, 
				s.title AS source, 
				d.title AS domain, 
				m.username AS mname, c.username AS cname,t.username AS tname
			FROM `customer` 
			LEFT JOIN `businesstype` AS b ON (b.businesstypeid=customer.businesstypeid) 
			LEFT JOIN `customersource` AS s ON (s.customersourceid=customer.customersourceid) 
			LEFT JOIN `customerdomain` AS d ON (d.customerdomainid=customer.customerdomainid) 
			LEFT JOIN region ON (region.regionid=customer.regionid) 
			LEFT JOIN `user` AS m ON (m.userid=customer.modifier) 
			LEFT JOIN `user` AS c ON (c.userid=customer.creator)
			LEFT JOIN `user` AS t ON (t.userid=customer.tracker)  
			WHERE customerid='".$this->kclass->input['customerid']."'
		");
		if(!$customer)	$this->kclass->boinkIt('/s.php?module=customer');
		if($customer['regionid']>0){
			$region = $customer['regionCountry'].' '.$customer['regionState'].' '.$customer['regionCity'];
		}else{
			$region='';
		}
		if($customer['modifier']>0){
			$modified = '，'.$customer['mname'].'于'.date('y-m-d H:i:s', $customer['modified']).'最后修改';
		}
		if($customer['verified']>0){
			$verified=''.$customer['verifyname'].'于'.date('y-m-d H:i:s',$customer['verified']).'审核';
		}
		$doverify=$this->kclass->doverify(array('module'=>'customer','action'=>'verify'));
		if($doverify){
			$action='(<a href="/s.php?module=customer&action=verify&customerid='.$customer['customerid'].'">审核</a>)';
		}
		$created = date('y-m-d H:i:s', $customer['created']);
		$status='';
		if($customer['ifverify']==0){
			$statusid=2;
			$status = '<span class="darkred">未审核</span>'.$action;
		}elseif($customer['ifverify']==-1){
			$statusid=2;
            $status='<span class="red">审核未通过</span>';
		}elseif($customer['ifverify']==1){
			$statusid=3;
			$status='<span class="green">审核通过</span>';
		}
		if($customer['postalcode']!='')$postalcode='<span class="normal">(邮编：'.$customer['postalcode'].')</span>';
		if($customer['position']!='')$position='<span class="normal">(称谓：'.$customer['position'].')</span>';
		if($customer['position2']!='')$position2='<span class="normal">(称谓：'.$customer['position2'].')</span>';
		$attach = $this->kclass->getAttachs(array('module'=>'customer', 'mid'=>$customer['customerid']));
		if($attach!= false){
			$attachs = '<div class="clear">'.$attach.'</div>';
		}
		$sample=$this->kclass->relatedSample(array('customerid'=>$customer['customerid']));
		$inquiry=$this->kclass->relatedInquiry(array('customerid'=>$customer['customerid']));
		$quoting=$this->kclass->relatedQuoting(array('customerid'=>$customer['customerid']));
		$feedback=$this->kclass->relatedfeedback(array('customerid'=>$customer['customerid']));
		$visit=$this->kclass->relatedVisit(array('customerid'=>$customer['customerid']));
		$order=$this->kclass->relatedOrder(array('customerid'=>$customer['customerid'], 'type'=>'PI'));
		$commission=$this->kclass->relatedCommission(array('customerid'=>$customer['customerid']));
		$deposit=$this->kclass->relatedDeposit(array('customerid'=>$customer['customerid']));
		$guarantee=$this->kclass->relatedGuarantee(array('customerid'=>$customer['customerid']));
		$creditnote=$this->kclass->relatedCreditnote(array('customerid'=>$customer['customerid']));
		$recipt=$this->kclass->relatedRecipt(array('customerid'=>$customer['customerid']));
		$debitnote=$this->kclass->relatedDebitnote(array('module'=>'order','customerid'=>$customer['customerid']));
		$payment=$this->kclass->relatedPayment(array('module'=>'order','customerid'=>$customer['customerid']));
		$claim=$this->kclass->relatedClaim(array('module'=>'order','customerid'=>$customer['customerid']));
		$return=$this->kclass->relatedReturn(array('module'=>'order','customerid'=>$customer['customerid']));
		$exchange=$this->kclass->relatedExchange(array('customerid'=>$customer['customerid']));
		$website = $this->kclass->relatedWebsite(array('customerid'=>$customer['customerid']));
		$service = $this->kclass->relatedService(array('customerid'=>$customer['customerid']));
		$flow=$this->kclass->listFlow(array('module'=>'enquiry','statusid'=>$statusid));
		if($customer['sourcedate']!=0){
			$sourcedate=date('Y-m-d',$customer['sourcedate']);
		}else{
			$sourcedate='';
		}
$body = <<<EOF
<div class="title"><span class="right small gray">由{$customer['cname']}于{$created}建立{$modified}。</span>{$customer['title']}</div>
<dl id="customer" class="tabs">
	<dt>资料</dt>
	<dt title="客户来访与我们拜访"{$visit['off']}>互访{$visit['count']}</dt>
	<dt title="客户询价记录"{$inquiry['off']}>询价{$inquiry['count']}</dt>
	<dt title="给客户的报价记录"{$quoting['off']}>报价{$quoting['count']}</dt>
	<dt title="客户反馈的重要信息"{$feedback['off']}>反馈信息{$feedback['count']}</dt>
	<dt title="发给客户的各类样品"{$sample['off']}>样品{$sample['count']}</dt>
	<dt title="销售订单"{$order['off']}>订单{$order['count']}</dt>
	<dt title="与客户相关的客户服务信息"{$service['off']}>客户关系{$service['count']}</dt>
	<dt title="销售佣金记录"{$commission['off']}>佣金{$commission['count']}</dt>
	<dt title="与客户相关的定金记录"{$deposit['off']}>定金{$deposit['count']}</dt>
	<dt title="与客户相关的保证金记录"{$guarantee['off']}>保证金{$guarantee['count']}</dt>
	<dt title="与客户相关的应收款项"{$creditnote['off']}>应收{$creditnote['count']}</dt>
	<dt title="与客户相关的已经收到的款项"{$recipt['off']}>已收{$recipt['count']}</dt>
	<dt title="与客户相关的应该付款的款项"{$debitnote['off']}>应付{$debitnote['count']}</dt>
	<dt title="与客户相关的已经付款的款项"{$payment['off']}>已付{$payment['count']}</dt>
	<dt title="客户通过在线网站的评论内容"{$website['off']}>评论/收藏/转发{$website['count']}</dt>
	<dt title="与客户相关的索赔记录"{$claim['off']}>索赔{$claim['count']}</dt>
	<dt title="客户的退货记录"{$return['off']}>退货{$return['count']}</dt>
	<dt title="客户的换货记录"{$exchange['off']}>换货{$exchange['count']}</dt>
	<dd>
	<div><span class="small gray">当前审核状态：{$status}</span>　　　　　<span class="gray small">{$verified}</span><span class="right normal">|　<a href="/s.php?module=customer&action=update&customerid={$customer['customerid']}&rt=view">修改</a>　<a href="/s.php?module=customer&action=remove&customerid={$customer['customerid']}">删除</a>　<a href="/p.php?module=printer&action=customerprint&customerid={$customer['customerid']}" target="_blank">打印</a></span></div>
	<div class="title"><span class="right small gray">由{$customer['cname']}于{$created}建立{$modified}。
	</span>{$customer['title']}</div>
 <div><span class="right">{$flow}</span></div>
<table class="hundred">
<tr class="odd">
	<td width="80">英文名称：</td>
	<td class="middle">{$customer['title']}</td>
	<td width="80">简　　称：</td>
	<td class="middle">{$customer['abbr']}</td>
</tr>
<tr class="even">
	<td>中文名称：</td>
	<td class="middle">{$customer['cntitle']}</td>
	<td>商业类型：</td>
	<td class="middle">{$customer['business']}</td>
</tr>
<tr class="odd">
	<td>英文地址：</td>
	<td class="middle">{$customer['address']}</td>
	<td>国　　家：</td>
	<td class="middle">{$region}　业务人员：{$customer['tname']}</td>
</tr>
<tr class="even">
	<td>中文地址：</td>
	<td class="middle">{$customer['cnaddress']} {$postalcode}</td>
	<td>网　　站：</td>
	<td class="middle">{$customer['website']}</td>
</tr>
<tr class="even">
	<td>客户来源：</td>
	<td class="middle">{$customer['source']}　来源时间： {$sourcedate}</td>
	<td>行业领域：</td>
	<td class="middle">{$customer['domain']}</td>
</tr>
<tr class="odd">
	<td valign="top">银行资料：</td>
	<td>{$customer['bankdetail']}</td>
	<td valign="top">备　　注：</td>
	<td>{$customer['remark']}</td>
</tr>

<tr class="even">
	<td colspan="2" align="center"><span class="bold">主联系人</span></td>
	<td colspan="2" align="center"><span class="bold">次联系人</span></td>
</tr>
<tr class="odd">
	<td >姓　　名：</td>
	<td class="middle" width="450">{$customer['linkman']} {$position}</td>
	<td>姓　　名：</td>
	<td class="middle">{$customer['linkman2']} {$position2}</td>
</tr>
<tr class="even">
	<td>昵　　称：</td>
	<td class="middle">{$customer['nickname']}</td>
	<td>昵　　称：</td>
	<td class="middle">{$customer['nickname2']}</td>
</tr>
<tr class="even">
	<td>邮　　箱：</td>
	<td class="middle">{$customer['email']}</td>
	<td>邮　　箱：</td>
	<td class="middle">{$customer['email2']}</td>
	
</tr>
<tr class="odd">
	<td>传　　真：</td>
	<td class="middle">{$customer['fax']}</td>
	<td>传　　真：</td>
	<td class="middle">{$customer['fax2']}</td>
</tr>
<tr class="even">
	<td>电　　话：</td>
	<td class="middle">{$customer['telephone']}</td>
	<td>电　　话：</td>
	<td class="middle">{$customer['telephone2']}</td>
</tr>
<tr class="odd">
	<td>手　　机：</td>
	<td class="middle">{$customer['mobile']}</td>
	<td>手　　机：</td>
	<td class="middle">{$customer['mobile2']}</td>
</tr>
</table>
{$attachs}
	</dd>
	<dd>{$visit['panel']}</dd>
	<dd>{$inquiry['panel']}</dd>
	<dd>{$quoting['panel']}</dd>
	<dd>{$feedback['panel']}</dd>
	<dd>{$sample['panel']}</dd>
	<dd>{$order['panel']}</dd>
	<dd>{$service['panel']}</dd>
	<dd>{$commission['panel']}</dd>
	<dd>{$deposit['panel']}</dd>
	<dd>{$guarantee['panel']}</dd>
	<dd>{$creditnote['panel']}</dd>
	<dd>{$receipt['panel']}</dd>
	<dd>{$debitnote['panel']}</dd>
	<dd>{$payment['panel']}</dd>
	<dd>{$claim['panel']}</dd>
	<dd>{$website['panel']}</dd>
	<dd>{$receipt['panel']}</dd>
	<dd>{$exchange['panel']}</dd>
	
</dl>
EOF;
		$this->kclass->page['onload'] .= "dc.tabs({'id':'customer'})";
		$this->kclass->page['title'] .= ' - '.$customer['title'];
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl.' - 客户信息', 'right' => $this->right.'　|　
		<span class="small">打印：</span><a href="/p.php?action=customer&customerid='.$customer['customerid'].'" target="_blank">基本资料</a>　<a href="/p.php?action=quoting&customerid='.$customer['customerid'].'" target="_blank">报价表</a>　|　<span class="small">新建：</span><a href="/s.php?module=customer&action=addVisit&customerid='.$customer['customerid'].'">互访记录</a>　<a href="/s.php?module=customer&action=addInquiry&customerid='.$customer['customerid'].'">询价单</a>　<a href="/s.php?module=customer&action=newQuoting&customerid='.$customer['customerid'].'">报价单</a>　<a href="/s.php?module=customer&action=addFeedback&customerid='.$customer['customerid'].'">反馈信息</a>	　<a href="/s.php?module=customer&action=customerrelation&customerid='.$customer['customerid'].'">客户关系</a>　|　'.$this->kclass->iif($b['killed']==1, '<a href="/s.php?module=customer&action=restore&customerid='.$customer['customerid'].'&rt=view">恢复</a>', '<a href="/s.php?module=customer&action=remove&customerid='.$customer['customerid'].'&rt=view">删除</a>　<a href="/s.php?module=customer&action=update&customerid='.$customer['customerid'].'&rt=view">修改</a>'), 'body'=>$body));
	}
	//
	function chooseLanguage(){
	$actionid = $this->kclass->input['actionid'];

	if($actionid!=null){
			$inquiryid = $this->kclass->input['inquiryid'];
	$quotingid = $this->kclass->input['quotingid'];
		switch($actionid){
			case "qt":
				$action = "quotingprint";
				$idname = "quotingid";
				$id = $quotingid;
				$pagename = "-标准报价单";
				break;
			case "iq":
				$action = "customerInquiry";
				$idname = "inquiryid";
				$id = $inquiryid;
				$pagename = "-询价单";
				break;
			default:
		}
	}
		$this->kclass->input['verify'] = base64_decode($this->kclass->input['verify']);//echo $this->kclass->input['verify'] ;
	$body=<<<EOF

	<form action="/p.php?module=printer&action={$action}&{$idname}={$id}&quotingid={$this->kclass->input['quotingid']}" target="_blank" method="post" onSubmit='return e({$this->kclass->input["verify"]},{$this->kclass->input["m"]});'>
	<table width="800px">
	<thead>
	<tr>
	<th>选择语言</th>
	</tr>
	</thead>
	<tbody>
	<tr>
	<td align="center">
	<label for="c1"><input type="radio" name="chooselanguage" id="c1" value="c1">中文</label>
	<label for="c2"><input type="radio" name="chooselanguage" id="c2" value="c2" checked>英文</label>
	</td>
	</tr>
	<tr>
	<td align="center"><input type="submit" value="提交"/><input type="reset" value="重置"/></td>
	</tr>
	</tbody>
	</table>
	</form>
	<div id='ajax-div' style='display:none'></div>
	<script>
		function e(v,m1){
		var c = $('input[type=radio]:checked').val();
if(c == 'c1'){//zh-cn
		if(m1){
			switch(v){				
				case 0:
					alert('审核未通过！');
				return false;
				case -1:
					alert('审核未通过！');
				return false;
				default:
					//send mail
					$.ajaxSetup({ 
					async : false 
					});  			
					var url_ = "/s.php?module=customer&action=sendmail&customerid={$this->kclass->input['customerid']}&quotingid={$this->kclass->input['quotingid']}&verify={$this->kclass->input['verify']}";						
					var url2 = "/s.php?module=customer&action=view&customerid={$this->kclass->input['customerid']} #quoting{$this->kclass->input['quotingid']}";
					layer.load('加载中....'); 
					var date = $("#ajax-div").load(url2);
						if(date){
							$(date).find('tr:first').remove();
							var date = escape($(date).html());			
							$.ajax({
							url:url_+Math.random(),
							data:'data='+date,
							timeout:'100000',
							type:'post',				
							success:function(result){alert('发送成功！');},
							error:function(){alert('请查询客户资料是否完善!!');},
							complete:function(){layer.closeAll();}
							});							
						}								
					return false;					
			}
			return false;	
		}			
		else{ //print 
			return true;
		}	
}else{//en	
	
		if(m1){
			switch(v){				
				case 0:
					alert('审核未通过！');
				return false;
				case -1:
					alert('审核未通过！');
				return false;
				default:
					//send mail
					$.ajaxSetup({ 
					async : false 
					});  			
					var url_ = "/s.php?module=customer&action=sendmail&customerid={$this->kclass->input['customerid']}&quotingid={$this->kclass->input['quotingid']}&verify={$this->kclass->input['verify']}";						
					var url2 = "/p.php?module=printer&action=quotingprint&quotingid={$this->kclass->input['quotingid']} .hundred";
					layer.load('加载中....');
					var date = $("#ajax-div").load(url2);
						if(date){
							$(date).find('tbody tr:first,table thead').remove();
							var date = escape($(date).html());			
							$.ajax({
							url:url_+Math.random(),
							data:'data='+date,
							timeout:'100000',
							type:'post',				
							success:function(result){alert('发送成功！');},
							error:function(){alert('请查询客户资料是否完善!!');},
							complete:function(){layer.closeAll();}
							});							
						}								
					return false;					
			}
			return false;	
		}			
		else{ //print 
			return true;
		}	

}
	}
</script>
EOF;
    $this->kclass->page['title'].='-选择语言';
	$this->kclass->page['onload'] .= "dc.tabs({'id':'customer'})";
	$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.$pagename,'right'=>'<a href="/s.php?moudle=customer">返回列表</a>','body'=>$body));
}

	//customer relation
	function customerrelation(){
		$customer=$this->kclass->DB->queryFirst("
		SELECT `customer`.*,
		`inquiry`.created AS icreated,`quoting`.created AS qcreated,`order`.shipmentDate AS date,`sample`.deliveryDate AS sdate
		FROM `customer`
		LEFT JOIN `inquiry` ON (`inquiry`.customerid=`customer`.customerid)
		LEFT JOIN `quoting` ON(`quoting`.customerid=`customer`.customerid)
		LEFT JOIN `order`     ON(`order`.customerid=`customer`.customerid)
		LEFT JOIN `sample`   ON(`sample`.customerid=`customer`.customerid)
		WHERE `customer`.customerid='".$this->kclass->input['customerid']."' ORDER BY icreated DESC ,qcreated DESC LIMIT 0,1");
		if($customer['cntitle']!=''){
			$title=$customer['title'].'('.$customer['cntitle'].')';
		}else{
			$title=$customer['title'];
		}

		$newcustomer=time()-$customer['created'];
		if($newcustomer>0 AND $newcustomer/86400<1){
			$information="<span class='green'>用户于".date("Y-m-d",$customer['created'])."新建完成,请尽快做客户回访</span><br/>";
		   }if($newcustomer/86400>1 AND $newcustomer/604800<1){
			$information="<span class='green'>用户于".date("Y-m-d",$customer['created'])."新建，距离现在".round($newcustomer/86400)."天,请尽快做客户回访</span><br/>";
		}elseif($newcustomer/604800>1 AND $newcustomer/2592000<1){
            $information="<span class='darkred'>用户于".date("Y-m-d",$customer['created'])."新建，距离现在".round($newcustomer/604800)."周,请尽快做客户回访</span><br/>";
		}elseif($newcustomer/2592000>1 AND $newcustomer/31536000<1){
			$information="<span class='red'>用户于".date("Y-m-d",$customer['created'])."新建，距离现在".round($newcustomer/2592000)."月,请尽快做客户回访</span><br/>";
		}elseif($newcustomer/31536000>1){
			$information="<span class='red'>用户于".date("Y-m-d",$customer['created'])."新建，距离现在".round($newcustomer/31536000)."年,请尽快做客户回访</span><br/>";
		}

		if($customer['icreated']>0){
			$newinquiry=time()-$customer['icreated'];
			if($newinquiry>0 AND $newinquiry/86400<1){
			$information.="<span class='green'>用户于".date("Y-m-d",$customer['icreated'])."询价完成,请尽快做客户回访</span><br/>";
		   }elseif($newinquiry/86400>1 AND $newinquiry/604800<1){
			$information.="<span class='green'>用户最近询价于".date("Y-m-d",$customer['icreated'])."完成，距离现在".round($newinquiry/86400)."天,请尽快做客户回访</span><br/>";
		}elseif($newinquiry/604800>1 AND $newinquiry/2592000<1){
            $information.="<span class='darkred'>用户最近询价于".date("Y-m-d",$customer['icreated'])."完成，距离现在".round($newinquiry/604800)."周,请尽快做客户回访</span><br/>";
		}elseif($newinquiry/2592000>1 AND $newinquiry/31536000<1){
			$information.="<span class='red'>用户最近询价于".date("Y-m-d",$customer['icreated'])."完成，距离现在".round($newinquiry/2592000)."月,请尽快做客户回访</span><br/>";
		}elseif($newinquiry/31536000>1){
			$information.="<span class='red'>用户最近询价于".date("Y-m-d",$customer['icreated'])."完成，距离现在".round($newinquiry/31536000)."年,请尽快做客户回访</span><br/>";
		}
		}

		if($customer['qcreated']>0){
			$newquoting=time()-$customer['qcreated'];
            if($newquoting>0 AND $newquoting/86400<1){
			$information.="<span class='green'>用户报价于".date("Y-m-d",$customer['qcreated'])."完成，请尽快做客户回访</span><br/>";
		   }elseif($newquoting/86400>1 AND $newquoting/604800<1){
			$information.="<span class='green'>用户最近报价于".date("Y-m-d",$customer['qcreated'])."完成，距离现在".round($newquoting/86400)."天,请尽快做客户回访</span><br/>";
		}elseif($newquoting/604800>1 AND $newquoting/2592000<1){
            $information.="<span class='darkred'>用户最近报价于".date("Y-m-d",$customer['qcreated'])."完成，距离现在".round($newquoting/604800)."周,请尽快做客户回访</span><br/>";
		}elseif($newquoting/2592000>1 AND $newquoting/31536000<1){
			$information.="<span class='red'>用户最近报价于".date("Y-m-d",$customer['qcreated'])."完成，距离现在".round($newquoting/2592000)."月,请尽快做客户回访</span><br/>";
		}elseif($newquoting/31536000>1){
			$information.="<span class='red'>用户最近报价于".date("Y-m-d",$customer['qcreated'])."完成，距离现在".round($newquoting/31536000)."年,请尽快做客户回访</span><br/>";
		}
		}

        if($customer['sdate']>0){
        $newsample=time()-$customer['sdate'];
            if($newsample>0 AND $newsample/86400<1){
			$information.="<span class='green'>用户样品 于".date("Y-m-d",$newsample['sdate'])."发货,请尽快做客户回访</span><br/>";
		   }elseif($newsample/86400>1 AND $newsample/604800<1){
			$information.="<span class='green'>用户最近样品于".date("Y-m-d",$customer['sdate'])."发货,距离现在".round($newsample/86400)."天,请尽快做客户回访</span><br/>";
		}elseif($newsample/604800>1 AND $newsample/2592000<1){
            $information.="<span class='darkred'>用户最近样品于".date("Y-m-d",$customer['sdate'])."发货,距离现在".round($newsample/604800)."周,请尽快做客户回访</span><br/>";
		}elseif($newsample/2592000>1 AND $newsample/31536000<1){
			$information.="<span class='red'>用户最近样品于".date("Y-m-d",$customer['sdate'])."发货,距离现在".round($newsample/2592000)."月,请尽快做客户回访</span><br/>";
		}elseif($newsample/31536000>1){
			$information.="<span class='red'>用户最近样品于".date("Y-m-d",$customer['sdate'])."发货,距离现在".round($newsample/31536000)."年,请尽快做客户回访</span><br/>";
		}
		}

		if($customer['date']>0){
        $neworder=time()-$customer['date'];
            if($neworder>0 AND $neworder/86400<1){
			$information.="<span class='green'>用户订单 于".date("Y-m-d",$customer['date'])."发货,请尽快做客户回访</span><br/>";
		   }elseif($neworder/86400>1 AND $neworder/604800<1){
			$information.="<span class='green'>用户最近订单于".date("Y-m-d",$customer['date'])."发货,距离现在".round($neworder/86400)."天,请尽快做客户回访</span><br/>";
		}elseif($neworder/604800>1 AND $neworder/2592000<1){
            $information.="<span class='darkred'>用户最近订单于".date("Y-m-d",$customer['date'])."发货,距离现在".round($neworder/604800)."周,请尽快做客户回访</span><br/>";
		}elseif($neworder/2592000>1 AND $neworder/31536000<1){
			$information.="<span class='red'>用户最近订单于".date("Y-m-d",$customer['date'])."发货,距离现在".round($neworder/2592000)."月,请尽快做客户回访</span><br/>";
		}elseif($neworder/31536000>1){
			$information.="<span class='red'>用户最近订单于".date("Y-m-d",$customer['date'])."发货,距离现在".round($neworder/31536000)."年,请尽快做客户回访</span><br/>";
		}
		}
		$relationship=$this->kclass->chooserRelationship(array('name'=>'relationshipid','hasBlank'=>'1','width'=>350));
		$date=date('Y-m-d');
		$body=<<<EOF
		<form action="/s.php?module=customer&action=chooserelation" method="post">
		<input type="hidden" name="customerid" value="{$this->kclass->input['customerid']}">
		<table  width="600px">
		<thead>
		<tr class="even">
		<th colspan="2">{$title}信息：</th>
		</tr>
		</thead>
		<tbody>
		<tr class="odd">
       <td colspan="2" class="middle">
		{$information}
	   </td>
		</tr>
		<tr class="even">
		<td>选择客户关系类型：</td>
		<td align="center">{$relationship}</td>
		</tr>
		<tr class="odd">
         <td >
		 客户关系产生时间：
		 </td>
		 <td align="center"><input type="text" name="date" id="dateline" value={$date}></td>
		 </tr>
		 <tr class="odd">
         <td colspan="2" align="center"><input type="submit" value="提交"/><input type="reset" value="重置"/></td>
		 </tr>
		</tbody>
		</table>
		</form>
EOF;
$this->kclass->page['title'] .= ' - '.$customer['title'].' - 新建客户关系';
		$this->kclass->page['onload'] .= 'dc.tabhover();$(\'#dateline\').datepicker()';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - <a href="/s.php?module=customer&action=view&customerid='.$customer['customerid'].'">'.$customer['title'].'</a> - 新建客户关系', 'right' => '<a href="/s.php?module=customer">返回列表</a>', 'body'=>$body));
	}
	//
	function chooserelation(){
	 $customer=$this->kclass->DB->queryFirst("SELECT * FROM customer WHERE customerid='".$this->kclass->input['customerid']."'");
	 if($customer['cntitle']!=''){
		 $title=$customer['title'].'('.$customer['cntitle'].')';
	 }else{
		 $title=$customer['title'];
	 }
	$list=$this->kclass->questionlist(array('action'=>'add','relationshipid'=>$this->kclass->input['relationshipid']));
	$body=<<<EOF
	<form action="/s.php?module=customer&action=addrelation&customerid={$this->kclass->input['customerid']}" method="post">
	<input type="hidden" name="relationshipid" value="{$this->kclass->input['relationshipid']}">
	<table class="hundred">
	<thead>
	<tr class="even">
	<th colspan="7">选择针对客户{$title}的回访问卷</th>
	</tr>
	
	<tbody>
	<tr>
	<td>ID</td><td>编号</td><td>名称</td><td>类型</td><td>建立时间</td><td>创建人</td><td>选择</td>
	</tr>
	</thead>
	<tr >
		{$list}
	<td align="center" colspan="7"><input type="submit" value="提交"/><input type="reset" value="重置"/></td>
	</tr>
	</tbody>
	</table>
	</form>
EOF;
   $this->kclass->page['title'] .= ' - '.$customer['title'].' - 新建客户关系';
		$this->kclass->page['onload'] .= 'dc.tabhover();$(\'#dateline\').datepicker()';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - <a href="/s.php?module=customer&action=view&customerid='.$customer['customerid'].'">'.$customer['title'].'</a> - 新建客户关系', 'right' => '<a href="/s.php?module=customer">返回列表</a>', 'body'=>$body));
	}
  //
  function addrelation(){
	  $customer=$this->kclass->DB->queryFirst("SELECT `customer`.*,
	  b.title AS btitle,r.country AS rcountry,r.encountry AS rencountry,`inquiry`.created AS icreated,`inquiry`.contact AS ititle,`inquiry`.inquiryid,`quoting`.created AS qcreated,`quoting`.contact AS qtitle,`quoting`.quotingid,`order`.shipmentDate AS date,`order`.ordertitle AS otitle,`order`.orderid,`sample`.deliveryDate AS sdate,`sample`.sampleid,`sample`.title AS stitle,d.title AS domain
	  FROM `customer`
	  LEFT JOIN `businesstype` AS b ON (b.businesstypeid=`customer`.businesstypeid) 
	  LEFT JOIN `region`            AS r  ON(r.regionid=`customer`.regionid)
	  LEFT JOIN `inquiry` ON (`inquiry`.customerid=`customer`.customerid)
		LEFT JOIN `quoting` ON(`quoting`.customerid=`customer`.customerid)
		LEFT JOIN `order`     ON(`order`.customerid=`customer`.customerid)
		LEFT JOIN `sample`   ON(`sample`.customerid=`customer`.customerid)
		LEFT JOIN `customerdomain` AS d ON (d.customerdomainid=`customer`.customerdomainid) 
	  WHERE `customer`.customerid='".$this->kclass->input['customerid']."'
	  ORDER BY icreated DESC ,qcreated DESC LIMIT 0,1
	  ");
		if($customer['cntitle']!=''){
			$title=$customer['title'].'('.$customer['cntitle'].')';
		}else{
			$title=$customer['title'];
		}
		$date=date("Y-m-d",$customer['created']);
		$country=$customer['rcountry'].'('.$customer['rencountry'].')';
	 if($this->kclass->input['relationshipid']==1){
		 //new customer
		 $newcustomer=time()-$customer['created'];
		if($newcustomer>0 AND $newcustomer/86400<1){
			$information="<span class='green'>用户于".date("Y-m-d",$customer['created'])."新建完成,请尽快做客户回访</span><br/>";
		   }if($newcustomer/86400>1 AND $newcustomer/604800<1){
			$information="<span class='green'>用户于".date("Y-m-d",$customer['created'])."新建，距离现在".round($newcustomer/86400)."天,请尽快做客户回访</span><br/>";
		}elseif($newcustomer/604800>1 AND $newcustomer/2592000<1){
            $information="<span class='darkred'>用户于".date("Y-m-d",$customer['created'])."新建，距离现在".round($newcustomer/604800)."周,请尽快做客户回访</span><br/>";
		}elseif($newcustomer/2592000>1 AND $newcustomer/31536000<1){
			$information="<span class='red'>用户于".date("Y-m-d",$customer['created'])."新建，距离现在".round($newcustomer/2592000)."月,请尽快做客户回访</span><br/>";
		}elseif($newcustomer/31536000>1){
			$information="<span class='red'>用户于".date("Y-m-d",$customer['created'])."新建，距离现在".round($newcustomer/31536000)."年,请尽快做客户回访</span><br/>";
		}

	 }elseif($this->kclass->input['relationshipid']==2){
        //new inquiry
        $newinquiry=time()-$customer['icreated'];
		if($customer['icreated']>0){
	       if($newinquiry>0 AND $newinquiry/86400<1){
			$information.="<span class='green'>用户于".date("Y-m-d",$customer['icreated'])."询价完成,请尽快做客户回访</span><br/>";
		   }elseif($newinquiry/86400>1 AND $newinquiry/604800<1){
			$information.="<span class='green'>用户最近询价于".date("Y-m-d",$customer['icreated'])."完成，距离现在".round($newinquiry/86400)."天,请尽快做客户回访</span><br/>";
		}elseif($newinquiry/604800>1 AND $newinquiry/2592000<1){
            $information.="<span class='darkred'>用户最近询价于".date("Y-m-d",$customer['icreated'])."完成，距离现在".round($newinquiry/604800)."周,请尽快做客户回访</span><br/>";
		}elseif($newinquiry/2592000>1 AND $newinquiry/31536000<1){
			$information.="<span class='red'>用户最近询价于".date("Y-m-d",$customer['icreated'])."完成，距离现在".round($newinquiry/2592000)."月,请尽快做客户回访</span><br/>";
		}elseif($newinquiry/31536000>1){
			$information.="<span class='red'>用户最近询价于".date("Y-m-d",$customer['icreated'])."完成，距离现在".round($newinquiry/31536000)."年,请尽快做客户回访</span><br/>";
		}
     }

	 }elseif($this->kclass->input['relationshipid']==3){
		 // new quoting
          if($customer['qcreated']>0){
			$newquoting=time()-$customer['qcreated'];
            if($newquoting>0 AND $newquoting/86400<1){
			$information.="<span class='green'>用户报价于".date("Y-m-d",$customer['qcreated'])."完成，请尽快做客户回访</span><br/>";
		   }elseif($newquoting/86400>1 AND $newquoting/604800<1){
			$information.="<span class='green'>用户最近报价于".date("Y-m-d",$customer['qcreated'])."完成，距离现在".round($newquoting/86400)."天,请尽快做客户回访</span><br/>";
		}elseif($newquoting/604800>1 AND $newquoting/2592000<1){
            $information.="<span class='darkred'>用户最近报价于".date("Y-m-d",$customer['qcreated'])."完成，距离现在".round($newquoting/604800)."周,请尽快做客户回访</span><br/>";
		}elseif($newquoting/2592000>1 AND $newquoting/31536000<1){
			$information.="<span class='red'>用户最近报价于".date("Y-m-d",$customer['qcreated'])."完成，距离现在".round($newquoting/2592000)."月,请尽快做客户回访</span><br/>";
		}elseif($newquoting/31536000>1){
			$information.="<span class='red'>用户最近报价于".date("Y-m-d",$customer['qcreated'])."完成，距离现在".round($newquoting/31536000)."年,请尽快做客户回访</span><br/>";
		}
		}

	 }elseif($this->kclass->input['relationshipid']==4){
         //new order
		 if($customer['date']>0){
        $neworder=time()-$customer['date'];
            if($neworder>0 AND $neworder/86400<1){
			$information.="<span class='green'>用户订单 于".date("Y-m-d",$customer['date'])."发货,请尽快做客户回访</span><br/>";
		   }elseif($neworder/86400>1 AND $neworder/604800<1){
			$information.="<span class='green'>用户最近订单于".date("Y-m-d",$customer['date'])."发货,距离现在".round($neworder/86400)."天,请尽快做客户回访</span><br/>";
		}elseif($neworder/604800>1 AND $neworder/2592000<1){
            $information.="<span class='darkred'>用户最近订单于".date("Y-m-d",$customer['date'])."发货,距离现在".round($neworder/604800)."周,请尽快做客户回访</span><br/>";
		}elseif($neworder/2592000>1 AND $neworder/31536000<1){
			$information.="<span class='red'>用户最近订单于".date("Y-m-d",$customer['date'])."发货,距离现在".round($neworder/2592000)."月,请尽快做客户回访</span><br/>";
		}elseif($neworder/31536000>1){
			$information.="<span class='red'>用户最近订单于".date("Y-m-d",$customer['date'])."发货,距离现在".round($neworder/31536000)."年,请尽快做客户回访</span><br/>";
		}
		}
	 }elseif($this->kclass->input['relationshipid']==5){
            //new sample
			if($customer['sdate']>0){
        $newsample=time()-$customer['sdate'];
            if($newsample>0 AND $newsample/86400<1){
			$information.="<span class='green'>用户样品 于".date("Y-m-d",$newsample['sdate'])."发货,请尽快做客户回访</span><br/>";
		   }elseif($newsample/86400>1 AND $newsample/604800<1){
			$information.="<span class='green'>用户最近样品于".date("Y-m-d",$customer['sdate'])."发货,距离现在".round($newsample/86400)."天,请尽快做客户回访</span><br/>";
		}elseif($newsample/604800>1 AND $newsample/2592000<1){
            $information.="<span class='darkred'>用户最近样品于".date("Y-m-d",$customer['sdate'])."发货,距离现在".round($newsample/604800)."周,请尽快做客户回访</span><br/>";
		}elseif($newsample/2592000>1 AND $newsample/31536000<1){
			$information.="<span class='red'>用户最近样品于".date("Y-m-d",$customer['sdate'])."发货,距离现在".round($newsample/2592000)."月,请尽快做客户回访</span><br/>";
		}elseif($newsample/31536000>1){
			$information.="<span class='red'>用户最近样品于".date("Y-m-d",$customer['sdate'])."发货,距离现在".round($newsample/31536000)."年,请尽快做客户回访</span><br/>";
		}
		}
	 }elseif($this->kclass->input['relationshipid']==6){
          //new customer evalution
	 }
	 $sql=$this->kclass->DB->query("SELECT `questionnaire`.*,`customertype`.title AS ctitle,`customertype`.entitle AS centitle,`questionitem`.title AS qtitle ,`questionitem`.chooseid AS chooseid,`questionitem`.itemid
	                        FROM `questionnaire` 
							LEFT JOIN `customertype` ON(`customertype`.typeid=`questionnaire`.relationshipid)
							LEFT JOIN `questionitem`  ON(`questionitem`.questionnaireid=`questionnaire`.id)
							WHERE `questionnaire`.id='".$this->kclass->input['questionnaireid']."'");
	 if($num=$this->kclass->DB->numRows()){
		$i=1;
		while($views=$this->kclass->DB->fetchArray($sql)){
			$titles=$views['title'];
			$type=$views['ctitle'].'('.$views['centitle'].')';
			$choose=$this->kclass->questionchooser(array('questionnaireid'=>$this->kclass->input['questionnaireid'],'chooserid'=>$views['chooseid'],'itemid'=>$views['itemid']));
			if($i<10){
				$num='0'.$i;
			}else{
				$num=$i;
			}
			$item.='<tr class="odd">
			<td colspan="4">'.$num.'.　'.$views['qtitle'].'</td>
			</tr>
			<tr><td colspan="4">'.$choose.'</td></tr>';
			$i++;
		}

	}
	 $body=<<<EOF
	 <form action="/s.php?module=customer&action=doaddrelation&customerid={$this->kclass->input['customerid']}"method="post">
	 <input type="hidden" name="relationshipid" value="{$this->kclass->input['relationshipid']}">
	 <input type="hidden" name="questionnaireid" value="{$this->kclass->input['questionnaireid']}">
	 <table class="hundred">
	 <thead>
	 <tr class="even">
	 <th colspan="4">客户信息：</th>
	 </tr>
	 </thead>
	 <tbody>
	 <tr class="odd">
        <td width="120px">名称：</td><td class="middle">{$title}</td><td width="120px">英文地址：</td><td class="middle">{$customer['address']}</td>
		</tr>
	<tr class="odd">
	<td >商业类型：</td><td class="middle">{$customer['btitle']}</td><td>国家：</td><td class="middle">{$country}</td>
	</tr>
	<tr class="even">
	<td>来源时间：</td><td class="middle">{$date}</td><td>行业领域：</td><td class="middle">{$customer['domain']}</td>
	</tr class="odd">
	<td >备注：</td ><td colspan="3" class="middle">{$information}</td>
	<tr class="even">
	<th colspan="4">主要联系人：</th>
	</tr>
	<tr class="odd">
	<td>姓名：</td><td class="middle">{$customer['linkman']}</td><td>昵称：</td><td class="middle">{$customer['nickname']}</td>
	</tr>
	<tr class="even">
	<td>邮箱：</td><td class="middle"> {$customer['email']}</td><td>传真：</td><td class="middle">{$customer['fax']}</td>
	</tr>
	<tr class="odd">
	<td>电话：</td><td class="middle">{$customer['telephone']}</td><td class="middle">手机：</td><td> {$customer['mobile']}</td>
	</tr>
	<tr class="even" >
	<th colspan="4">客户回访详细内容：</th>
	</tr>
	<tr>
	<td>调查表名称：</td><td>{$titles}</td><td>调查表类型：</td><td>{$type}</td>
	</tr>
	  {$item}
	  <tr>
	  <td colspan="4" align="center"><input type="submit" value="提交"/><input type="reset" value="重置"></td>
	  </tr>
	 </tbody>
	 </table>
	 </form>
EOF;
	 $this->kclass->page['title'] .= ' - '.$customer['title'].' - 新建客户关系';
		$this->kclass->page['onload'] .= 'dc.tabhover();$(\'#dateline\').datepicker()';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - <a href="/s.php?module=customer&action=view&customerid='.$customer['customerid'].'">'.$customer['title'].'</a> - 新建客户关系', 'right' => '<a href="/s.php?module=customer">返回列表</a>', 'body'=>$body));
  }
  //
  function doaddrelation(){
   if($this->kclass->input['requestMethod'] != 'post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
         $sql=$this->kclass->DB->query("SELECT itemid,title,chooseid FROM `questionitem` WHERE questionnaireid='".$this->kclass->input['questionnaireid']."'");
		 $hasItem=array();
		 if($num=$this->kclass->DB->numRows()){
			 $i=1;
			 $hasItem[$i]=0;
			 while($item=$this->kclass->DB->fetchArray($sql)){
             if($this->kclass->input[$item['itemid']]){
				$hasItem[$i]=1;
			    }  
				 if($hasItem[$i]==0){
              $e.='<li>问卷第'.$i.'个问题未选择选项</li>';
		     }
			 $i++;
		 }
		 }else{
			 $e.='<li>选择问卷时发生错误，请联系管理员</li>';
		 }
		}
		if(isset($e)){
			$this->kclass->messager(array(
                   'title' => '添加客户关系',
				'text' => '您在新建调查问卷的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
				));
		}
		if(!isset($e)){
        $this->kclass->DB->query("INSERT INTO `customerrelationship`(customerid,questionnaireid,relationshiptypeid,created,creator) VALUES('".$this->kclass->input['customerid']."','".$this->kclass->input['questionnaireid']."','".$this->kclass->input['relationshipid']."','".TIMENOW."','".$this->kclass->user['userid']."')");
		$customerrelation=$this->kclass->DB->insertID();
		 $sql=$this->kclass->DB->query("SELECT itemid,title,chooseid FROM `questionitem` WHERE questionnaireid='".$this->kclass->input['questionnaireid']."'");
		if($num=$this->kclass->DB->numRows()){
			while($items=$this->kclass->DB->fetchArray($sql)){	
				//$chooser=$this->kclass->input[$items['itemid']];
				if($items['chooseid']==1){
					$chooser=$this->kclass->input[$items['itemid']];
				}elseif($items['chooseid']==2){
					if(count($this->kclass->input[$items['itemid']])>=2){
						$chooser=implode("-",$this->kclass->input[$items['itemid']]);
					}else{
						$chooser=$this->kclass->input[$items['itemid']][0];
					}
				}elseif($items['chooseid']==3){
					$chooser=trim($this->kclass->input[$items['itemid']]);
				}
		echo "<br/>";
     $this->kclass->DB->query("INSERT INTO `customerchooser`(relationshipid,questionnaireid,itemid,typeid,choosers,creator,created) VALUES('".$customerrelation."','".$this->kclass->input['questionnaireid']."','".$items['itemid']."','".$this->kclass->input['relationshipid']."','".$chooser."','".$this->kclass->user['userid']."','".TIMENOW."')");
			}
		}
		
		$customer=$this->kclass->DB->queryFirst("SELECT title,cntitle FROM customer WHERE customerid='".$this->kclass->input['customerid']."'");
		if($customer['cntitle']!=''){
			$title=$customer['title'].'('.$customer['cntitle'].')';
		}else{
			$title=$customer['title'];
		}
		$this->kclass->messager(array(
			'title' => '与客户'.$title.'的客户关系',
			'text' => '与客户<b>'.$title.'</b>的客户关系 已新建成功!',
			'url' => '/s.php?module=customer&action=view&customerid='.$this->kclass->input['customerid'],
			'sec' => 3
		));
		}
  }
     function questionnaire(){
	  $relationship=$this->kclass->chooserRelationship(array('name'=>'relationshipid','hasBlank'=>'1','width'=>350));
	  $list=$this->kclass->questionlist(array('action'=>'list'));

	   
	  for($i=1;$i<=15;$i++){
		$chooser=$this->kclass->typechooser(array('num1'=>$i));
		  $item.='<tr name="tr'.$i.'" class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').'">
		  <td>'.$i.'</td>
		  <td ><input type="text" name="questions['.$i.']" size="80px"></td>
          <td><select name="type['.$i.']" id="type'.$i.'" onChange="onChange(this)">
		           <option value="1" selected>单选题</option>
				   <option value="2" >多选题</option>
				   <option value="3" >文字题</option>
		  </select>
		  </td>
		  <td><span id="chooserdf">'.$chooser.'</span><span style="display:none"><input type="text" name="" size="90" value=" 文字题无选项···" disabled /></span></td>
		  <td>
		  <input type="checkbox" name="kill['.$i.']" value="1" />
		  </td>
		  </tr>';
	  }
	  //$item.='<tr class="odd bold gray normal" id="orderProduct" nohover><td colspan=5><span class="hand" onclick="dc.addLine(\'orderProduct\',\'orderProduct\')" style="border:2px solid gray;background:#FFE8EB">增加一行产品明细</span></td></tr>';
	  $style=$this->kclass->iif($i%2==0,"odd","even");
	  //print_r($this->kclass->user);
	  //echo $this->kclass->user['userid'];
	  //echo $this->kclass->$verifierid;
  $body=<<<EOF
<dl id="customer" class="tabs">
<dt>新建调查问卷</dt>
<dt>查找调查问卷</dt>
<dt>调查问卷列表</dt>
<dd>
<form action="/s.php?module=customer&action=addquestion" method="post">
<table class="hundred">
<thead>
<tr class="even">
<th colspan="5">调查问卷填写</th>
</tr>
</thead>
<tbody>
<tr class="odd">
<td width="100px">问卷名称：</td width="150px"><td ><input type="text" name="title" size="30px"></td>
<td width="150px">选择客户关系类型：</td><td width="150px" colspan="2">{$relationship}</td>
</tr>
<tr class="even">
<td>编号</td><td >问题</td><td width="100px">选项类型</td><td width="850px">答案选项</td><td width="50px">删</td>
</tr>
	  {$item}
<tr class="even">
<td colspan="5" align="center">
<input type="submit" value="提交"/><input type="reset" value="重置"/>
</td>
</tr>
</tbody>
</table>
</form>
</dd>
<!--查找调查问卷-->
<dd>
<table class="hundred">
<thead>
<tr>
<th colspan="9">调查问卷列表</th>
</tr>
</thead>
<tbody>
<tr>
<td width="50px">编号</td><td width="200px">审核状态</td><td>名称</td><td width="250px">类型</td><td width="150px">建立时间</td><td width="150px">创建人</td><td width="150px">最后修改时间</td><td width="150px">操作</td>
</tr>
	  {$list}
</tbody>
</table>
</dd>
</dl>
EOF;

 $this->kclass->page['title'] .= ' 调查问卷';
 $this->kclass->page['onload'].='dc.tabs({\'id\':\'customer\'});dc.tabhover();var dates=$(\'#startDate,#endDate\').datepicker({onSelect:function(selectedDate){var option=this.id==\'startDate\'?\'minDate\':\'maxDate\',instance=$(this).data(\'datepicker\'),date=$.datepicker.parseDate(instance.settings.dateFormat || $.datepicker._defaults.dateFormat,selectedDate,instance.settings);dates.not(this).datepicker(\'option\',option,date);}});
';
 $this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 调查问卷', 'right' => '<a href="/s.php?module=customer">返回列表</a>', 'body'=>$body));
}
  //
  function addquestion(){
	if($this->kclass->input['requestMethod'] != 'post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
       if(strlen($this->kclass->input['title'])<2 ){
		   $e='<li>名称不能短于2</li>';
	   }else{
            $hasItem=$hasradioItem=$hascheckboxItem==0;
			for($i=1;$i<count($this->kclass->input['questions'])+1;$i++){
               if($this->kclass->input['questions'][$i]!=''  AND $this->kclass->input['type'][$i]!='' AND $this->kclass->input['kill'][$i]==''){
				   $hasItem=1;
			   }
			}
			for($j=1;$i<count($this->kclass->input['radio'])+1;$j++){
				if($this->kclass->input['radio'][$j]!=''){
					$hasradioItem=1;
				}
			}
			for($k=1;$k<count($this->kclass->input['checkbox'])+1;$k++){
				if($this->kclass->input['radio'][$k]!=''){
					$hascheckboxItem=1;
				}
			}
       if($hasItem==0 AND $hasradioItem==0 AND $hascheckboxItem==0 ){
		   $e='<li>请至少填写一条问卷明细</li>';
	   }
	   }
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '新建调查问卷',
				'text' => '您在新建调查问卷的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
	  
	   if(!isset($e)){
		    $title=$this->kclass->parseConvertValue($this->kclass->input['title']);
	   $this->kclass->DB->query("INSERT INTO `questionnaire`(title,action,relationshipid,created,creator) VALUES('".$title."','".$this->kclass->input['action']."','".$this->kclass->input['relationshipid']."','".TIMENOW."','".$this->kclass->user['userid']."')");
	   $question=$this->kclass->DB->insertID();
	   //问题明细
	   $quesrtionitem=array();
	   for($m=1;$m<=count($this->kclass->input['questions']);$m++){
       $this->kclass->input['questions'][$m]=$this->kclass->parseConvertValue($this->kclass->input['questions'][$m]);
	   if($this->kclass->input['questions'][$m]!='' AND $this->kclass->input['kill'][$m]==''){
		   $this->kclass->DB->query("INSERT INTO `questionitem`(questionnaireid,title,chooseid,created,creator) VALUES('".$question."','".$this->kclass->input['questions'][$m]."','".$this->kclass->input['type'][$m]."','".TIMENOW."','".$this->kclass->user['userid']."')");
		   $questionitem=$this->kclass->DB->insertID();
		    for($h=1;$h<=count($this->kclass->input['chooser']);$h++){
         $this->kclass->input['chooser'][$h]=$this->kclass->parseConvertValue($this->kclass->input['chooser'][$h]);
         if($this->kclass->input['chooser'][$m][$h]!=''){
			$result=$this->kclass->DB->query("INSERT INTO `chooser`(chooserid,questionnaireid,itemid,title,created,creator) VALUES('".$this->kclass->input['type'][$m]."','".$question."','".$questionitem."','".$this->kclass->input['chooser'][$m][$h]."','".TIMENOW."','".$this->kclass->user['userid']."')");
		}
		 
	   }
	   }
	   }
       $this->kclass->messager(array(
			'title' => '新建调查问卷',
			'text' => '调查问卷 <b>'.$this->kclass->input['title'].'</b> 已新建成功!',
			'url' => '/s.php?module=customer&action=questionview&questionnaireid='.$question,
			'sec' => 3
		));
	   }
  }
//
function questionview(){
	$sql=$this->kclass->DB->query("SELECT `questionnaire`.*,`customertype`.title AS ctitle,`customertype`.entitle AS centitle,`questionitem`.title AS qtitle ,`questionitem`.chooseid AS qchooseid ,`questionitem`.itemid  AS qitemid
	                        FROM `questionnaire` 
							LEFT JOIN `customertype` ON(`customertype`.typeid=`questionnaire`.relationshipid)
							LEFT JOIN `questionitem`  ON(`questionitem`.questionnaireid=`questionnaire`.id)
							WHERE `questionnaire`.id='".$this->kclass->input['questionnaireid']."'");
	if($num=$this->kclass->DB->numRows()){
		$i=1;
		while($views=$this->kclass->DB->fetchArray($sql)){
			$title=$views['title'];
			$type=$views['ctitle'].'('.$views['centitle'].')';
			//print_r($views);
			$choose=$this->kclass->questionchooser(array('questionnaireid'=>$this->kclass->input['questionnaireid'],'chooserid'=>$views['qchooseid'],'itemid'=>$views['qitemid']));
			if($i<10){
				$num='0'.$i;
			}else{
				$num=$i;
			}
			$item.='<tr>
			<td colspan="4">'.$num.'.　'.$views['qtitle'].'</td>
			</tr>
			<tr><td colspan="4">'.$choose.'</td></tr>';
			$i++;
		}

	}
	$body=<<<EOF
	<table class="hundred">
	<thead>
	<tr>
	<th colspan="4">预览调查问卷<span class="green">{$title}</span>的详细信息：</th>
	</tr>
	<tr>
	<td colspan="4">该问卷共有{$num}个问题，已被引用{}次，涉及到{}个客户</td>
	</tr>
	</thead>
	<tbody>
	<tr>
	<td>问卷名称：</td><td>{$title}</td><td>客户关系类型：</td><td>{$type}</td>
	</tr>
    <tr>
	<th colspan="4">问卷详细</th>
	</tr>
	{$item}
	</tbody>
	</table>
EOF;
	 $this->kclass->page['title'] .= ' 问卷详细';
$this->kclass->page['onload'] .= 'dc.tabhover();$(\'#dateline\').datepicker()';
 $this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' -问卷详细', 'right' => '<a href="/s.php?module=customer">返回列表</a>', 'body'=>$body));
}
//
function removequestion(){
		if($this->kclass->input['questionnaireid']<=0){
			$this->kclass->boinkIt('/s.php?module=customer&action=questionnaire');
		}
		$questionnaire=$this->kclass->DB->queryFirst("SELECT title FROM `questionnaire` WHERE `id`='".$this->kclass->input['questionnaireid']."'");
$body=<<<EOF
<form action="/s.php?module=customer&action=killquestionnaire" name="customer" method="post">
<input type="hidden" name="questionnaireid" value="{$this->kclass->input['questionnaireid']}">
<table width="800px">
<thead>
<tr><th>删除调查问卷：{$questionnaire['title']}</th></tr>
</thead>
<tbody>
<tr class="odd">
<td>你确定要删除调查问卷：{$questionnaire['title']}</td>
</tr>
<tr class="even">
<td align="center">
<label for="c1" class="red"><input type="radio" id="c1" name="confirm" value="1">是</label>
<label for="c2" class="green"><input type="radio" id="c2" name="confirm" value="0" checked>否</label>
</td>
</tr>
<tr class="odd">
<td align="center"><input type="submit" value="提交"/><input type="reset" value="重置"/></td>
</tr>
</tbody>
</table>
EOF;
$this->kclass->page['title'] .= ' - 删除调查问卷 - '.$questionnaire['title'];
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 删除调查问卷 - '.$questionnaire['title'], 'right' => '<a href="/s.php?module=customer">返回列表</a>', 'body'=>$body));
}
//
function killquestionnaire(){
	if($this->kclass->input['confirm']==0){
		$this->kclass->boinkIt('/s.php?module=customer&action=questionnaire');
	}
	if($this->kclass->input['questionnaireid']<=0){
			$this->kclass->boinkIt('/s.php?module=customer&action=questionnaire');
		}
	if($this->kclass->input['requestMethod'] != 'post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '删除客户',
				'text' => '您在删除客户的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}else{
          $questionnaire=$this->kclass->DB->queryFirst("SELECT title FROM `questionnaire` WHERE `id`='".$this->kclass->input['questionnaireid']."'");
		  if($questionnaire){
         $this->kclass->DB->query("
					UPDATE `questionnaire`
					SET    ifkill=1,killer='".$this->kclass->user['userid']."',killed='".TIMENOW."'
					WHERE  `id`='".$this->kclass->input['questionnaireid']."'
		 ");
		 $this->kclass->messager(array(
				'title' => '删除调查问卷成功',
				'text' => '调查问卷 <b>'.$questionnaire['title'].'</b> 已成功被标记为删除!',
				'url' => '/s.php?module=customer&action=questionnaire',
				'sec' => 3
			));
		  }else{
            $this->kclass->messager(array(
				'title' => '删除客户失败',
				'text' => '您要删除的调查问卷，不存在！',
				'url' => '/s.php?module=customer&action=questionnaire',
				'sec' => 3
			));
		  }
		}
}
//
function  updatequestionnaire(){
	$relationship=$this->kclass->chooserRelationship(array('name'=>'relationshipid','selectedid'=>$question['relationshipid'],'hasBlank'=>'1','width'=>350));
	$question=$this->kclass->DB->query("SELECT q.id,q.title,q.creator,q.created,q.modifier,q.modified,m.realname AS mname,n.realname AS nname,p.title 
	AS ptitle
									FROM `questionnaire` AS q
									LEFT JOIN `member`    AS m ON(m.userid=q.creator)
									LEFT JOIN `questionitem`  AS p ON(p.questionnaireid=q.id)
									LEFT JOIN `member`    AS n  ON(n.userid=q.modifier)
									WHERE q.id='".$this->kclass->input['questionnaireid']."' ");
	if($num=$this->kclass->DB->numRows()){
		$i=1;
		while($questions=$this->kclass->DB->fetchArray($question)){
			print_r($questions);
	$title=$questions['title'];
	$creator=$questions['mname'];
	$date=date('Y-m-d',$questions['created']);
	if($questions['modified']>0){
		$action=',由'.$questions['nname'].'于'.date('Y-m-d',$qestions['modified']).'修改';
	}
	$item.='<tr>
				<td>'.$i.'</td>
				<td><input type="text" name="questions['.$i.']" size="80px" value="'.$questions['ptitle'].'"/></td>
				<td></td>
				<td></td>
				<td>
		          <input type="checkbox" name="kill['.$i.']" value="1" />
		       </td>
				</tr>';
			$i++;
	}
	}
$body=<<<EOF
<form action="/s.php?module=customer&action=doupdatequestion" method="post">
<table class="hundred">
<thead>
<tr>
<th colspan="5">调查问卷修改</th>
</tr>
</thead>
<tbody>
<tr>
<td>问卷名称：</td><td>{$title}</td><td>客户关系类型：</td><td colspan="2">{$relationship}</td>
</tr>
<tr><td colspan="5">本问卷是由{$creator}于{$date}创建{$action}。</td></tr>
<tr>
<th>编号</th><th>问题</th><th>选项类型</th><th>问题选项</th><th>删</th>
</tr>
	{$item}
</tbody>
</table>
</form>
EOF;
  $this->kclass->page['title'] .= ' - 修改调查问卷 - '.$question['title'];
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 修改调查问卷 - '.$question['title'], 'right' => '<a href="/s.php?module=customer">返回列表</a>', 'body'=>$body));
}
//
function doupdatequestion(){

}
	//
	function insert(){
		print_r($this->kclass->input);
		if($this->kclass->input['requestMethod'] != 'post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			$this->kclass->input['title']=trim($this->kclass->input['title']);
			$this->kclass->input['abbr']=trim($this->kclass->input['abbr']);
			$this->kclass->input['address']=trim($this->kclass->input['address']);
			$this->kclass->input['linkman']=trim($this->kclass->input['linkman']);
			$this->kclass->input['position']=trim($this->kclass->input['position']);
			$this->kclass->input['telephone']=trim($this->kclass->input['telephone']);
			$this->kclass->input['email']=trim($this->kclass->input['email']);
			if($this->kclass->input['title'] == ''){
				$e = '<li>请填写客户的 名称。</li>';
			}else{
				if($this->kclass->DB->queryFirst("SELECT customerid FROM `customer` WHERE `title`='".$this->kclass->input['title']."' AND `customer`.killed=0")){
					$e = '<li>您要新建的客户 【'.$this->kclass->input['title'].'】 已经存在。</li>';
				}
			}
			if($this->kclass->input['abbr'] == ''){
				$e .= '<li>请填写客户的英文简称。</li>';
			}elseif(strlen($this->kclass->input['abbr'])>20){
				$e .= '<li>客户的英文简称不能超过 20 个英文字符。</li>';
			}
			if($this->kclass->input['address'] == ''){
				$e .= '<li>请填写客户的英文地址。</li>';
			}elseif(strlen($this->kclass->input['abbr'])>255){
				$e .= '<li>客户的英文地址不能超过 255 个英文字符。</li>';
			}
			if($this->kclass->input['regionid'] <= 0 OR $this->kclass->input['regionid']==''){
				$e .= '<li>请填写客户的所属 国家。</li>';
			}
			if($this->kclass->input['customersourceid']=='' OR $this->kclass->input['customersourceid'] <= 0  ){
				$e .= '<li>请选择客户来源。</li>';
			}
			if($this->kclass->input['sourcedate']==''){
				$e .= '<li>请选择客户来源时间。</li>';
			}
			if($this->kclass->input['customerdomainid']=='' OR $this->kclass->input['customerdomainid'] <= 0){
				$e .= '<li>请选择行业领域。</li>';
			}
			if($this->kclass->input['linkman'] == ''){
				$e .= '<li>请填写客户的主联系人。</li>';
			}elseif(strlen($this->kclass->input['linkman'])>100){
				$e .= '<li>客户的主联系人不能超过 100 个英文字符。</li>';
			}
			if($this->kclass->input['position'] == ''){
				$e .= '<li>请填写客户主联系人的称谓。</li>';
			}elseif(strlen($this->kclass->input['position'])>50){
				$e .= '<li>客户的主联系人不能超过 50 个英文字符。</li>';
			}
			if($this->kclass->input['telephone'] == ''){
				$e .= '<li>请填写客户的 电话。</li>';
			}elseif(strlen($this->kclass->input['telephone'])>50){
				$e .= '<li>客户的电话不能超过 50 个英文字符。</li>';
			}
			if($this->kclass->input['email'] == ''){
				$e .= '<li>请填写客户的 邮箱。</li>';
			}elseif(strlen($this->kclass->input['email'])>500){
				$e .= '<li>客户的邮箱不能超过 100 个英文字符。</li>';
			}
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '新建客户',
				'text' => '您在新建客户的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		/*
		if($this->kclass->input['cityid']>0){
			$regionid=$this->kclass->input['cityid'];
		}elseif($this->kclass->input['provinceid']>0){
			$regionid=$this->kclass->input['provinceid'];
		}else{
			$regionid=$this->kclass->input['countryid'];
		}
		*/
		$sourcedate=0;
		if($this->kclass->input['sourcedate']!=''){
			$dd=explode('-',$this->kclass->input['sourcedate']);
			$sourcedate=mktime(0,0,0,$dd[1],$dd[2],$dd[0]);
		}
		$this->kclass->DB->query("
			INSERT INTO `customer` (
				`title`, 
				`cntitle`,
				 `abbr`,
				 `address`,
				 `cnaddress`,
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
				 `customersourceid`,
				 `sourcedate`,
				 `customerdomainid`,
				 `description`,
				 `bankdetail`,
				 `remark`,
				 `modified`,
				 `tracker`,
				`creator`,
				 `created`) 
				VALUES (
				'".$this->kclass->input['title']."',
				 '".$this->kclass->input['cntitle']."',
				 '".$this->kclass->input['abbr']."',
				 '".$this->kclass->input['address']."',
				 '".$this->kclass->input['cnaddress']."',
				 '".$this->kclass->input['regionid']."',
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
				 '".$this->kclass->input['customersourceid']."',
				 '".$sourcedate."',
				 '".$this->kclass->input['customerdomainid']."',
				 '".$this->kclass->input['description']."',
				 '".$this->kclass->input['bankdetail']."',
				 '".$this->kclass->input['remark']."',
				 '".TIMENOW."',
				 '".$this->kclass->input['tracker']."',
				 '".$this->kclass->user['userid']."',
				 '".TIMENOW."')
		");
		$customerid=$this->kclass->DB->insertID();
		$this->kclass->updateAttachs(array('module'=>'customer', 'mid'=>$customerid));
		

		$this->kclass->messager(array(
			'title' => '新建客户',
			'text' => '客户 <b>'.$this->kclass->input['title'].'</b> 已新建成功!',
			'url' => '/s.php?module=customer&action=view&customerid='.$customerid,
			'sec' => 3
		));
	}

	//
	function add(){
		$tracker = $this->kclass->chooserTracker(array('name'=>'tracker','condition'=>'positionid=7','width'=>80));
		$region = $this->kclass->chooserRegion(array('regionid'=>0, 'hasBlank'=>1));
		$business = $this->kclass->chooserBusinesstype(array('name'=>'businesstypeid', 'hasBlank'=>1, 'width'=>350));
		$source = $this->kclass->chooserCustomerSource(array('name'=>'customersourceid', 'hasBlank'=>1, 'width'=>350));
		$domain = $this->kclass->chooserCustomerDomain(array('name'=>'customerdomainid', 'hasBlank'=>1, 'width'=>350));
		$flow=$this->kclass->listFlow(array('module'=>'enquiry','statusid'=>1));
		$upload = $this->kclass->upload(array('title'=>'相关附件：'));
$body = <<<EOF
<form action="/s.php?module=customer&action=insert" id="customer" name="customer" method="post" onsubmit="return dc.customer.check(this);">
<input type="hidden" name="module" value="customer">
<input type="hidden" name="action" value="insert">
<div><span class="right">{$flow}</span></div>
<table class="hundred">
<thead>
<tr>
	<th colspan="4">客户新建表单：</th>
</tr>
</thead>
<tbody>
<tr class="odd">
	<td>英文名称：<span class="red bold">*</span></td>
	<td><input type="text" style="width:450px" name="title" value=""></td>
	<td>简　　称：<span class="red bold">*</span></td>
	<td><input type="text" size="20" name="abbr" value=""> <span class="gray small">不要超过10个英文字符，或5个中文字</span></td>
</tr>
<tr class="even">
	<td>中文名称：</td>
	<td><input type="text" style="width:450px" name="cntitle" value=""></td>
	<td>商业类型：</td>
	<td>{$business} <span class="gray small">选择一个最符合的类型</span></td>
</tr>
<tr class="odd">
	<td>英文地址：<span class="red bold">*</span></td>
	<td><input type="text" style="width:450px" name="address" value=""></td>
	<td>国　　家：<span class="red bold">*</span></td>
	<td>{$region} 业务人员：{$tracker}</td>
</tr>
<tr class="even">
	<td>中文地址：</td>
	<td><input type="text" style="width:348px" name="cnaddress" value=""> 邮编：<input type="text" style="width:50px" name="postalcode" value="{$customer['postalcode']}"></td>
	<td>网　　站：</td>
	<td><input type="text" style="width:450px" name="website" value=""></td>
</tr>
<tr class="even">
	<td>客户来源：</td>
	<td>{$source} <span class="gray small">选择一个最符合的类型</span> 来源时间： <input type="text" name="sourcedate" id="sourcedate" size="10"></td>
	<td>行业领域：</td>
	<td>{$domain} <span class="gray small">选择一个最符合的类型</span></td>
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
		$this->kclass->page['onload'] .= 'dc.tabhover();dc.tabhover();$(\'#sourcedate\').datepicker()';
		$this->kclass->page['title'] .= ' - 新建客户';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 新建客户', 'right' => '<a href="/s.php?module=customer">返回列表</a>', 'body'=>$body));
	}

	//
	function doupdate(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			$this->kclass->input['title']=trim($this->kclass->input['title']);
			$this->kclass->input['abbr']=trim($this->kclass->input['abbr']);
			$this->kclass->input['address']=trim($this->kclass->input['address']);
			$this->kclass->input['linkman']=trim($this->kclass->input['linkman']);
			$this->kclass->input['position']=trim($this->kclass->input['position']);
			$this->kclass->input['telephone']=trim($this->kclass->input['telephone']);
			$this->kclass->input['email']=trim($this->kclass->input['email']);
			if($this->kclass->input['title'] == ''){
				$e = '<li>请填写客户的 名称。</li>';
			}else{
				if($customer = $this->kclass->DB->queryFirst("SELECT customerid FROM `customer` WHERE `customerid`<>'".$this->kclass->input['customerid']."' AND `title`='".$this->kclass->input['title']."' AND `customer`.killed=0")){ //校验是否重名
					$e = '<li>您要修改的客户 【'.$this->kclass->input['title'].'】 已经存在。您可以<a href="/s.php?module=customer&action=view&curstomerid='.$customer['customerid'].'" target="_blank">点击这里</a>访问该客户的详细信息。</li>';
				}
			}
			if($this->kclass->input['abbr'] == ''){
				$e .= '<li>请填写客户的 英文简称。</li>';
			}elseif(strlen($this->kclass->input['abbr'])>20){
				$e .= '<li>客户的英文简称不能超过 20 个英文字符。</li>';
			}
			if($this->kclass->input['address'] == ''){
				$e .= '<li>请填写客户的 英文地址。</li>';
			}elseif(strlen($this->kclass->input['abbr'])>255){
				$e .= '<li>客户的英文地址不能超过 255 个英文字符。</li>';
			}
			if($this->kclass->input['regionid'] <= 0 OR $this->kclass->input['regionid']==''){
				$e .= '<li>请填写客户的所属 国家。</li>';
			}
			if($this->kclass->input['customersourceid']=='' OR $this->kclass->input['customersourceid'] <= 0  ){
				$e .= '<li>请选择客户来源。</li>';
			}
			if($this->kclass->input['sourcedate']==''){
				$e .= '<li>请选择客户来源时间。</li>';
			}
			if($this->kclass->input['customerdomainid']=='' OR $this->kclass->input['customerdomainid'] <= 0){
				$e .= '<li>请选择行业领域。</li>';
			}
			if($this->kclass->input['linkman'] == ''){
				$e .= '<li>请填写客户的 主联系人。</li>';
			}elseif(strlen($this->kclass->input['linkman'])>100){
				$e .= '<li>客户的主联系人不能超过 100 个英文字符。</li>';
			}
			if($this->kclass->input['position'] == ''){
				$e .= '<li>请填写客户主联系人的 称谓。</li>';
			}elseif(strlen($this->kclass->input['position'])>50){
				$e .= '<li>客户的主联系人不能超过 50 个英文字符。</li>';
			}
			if($this->kclass->input['telephone'] == ''){
				$e .= '<li>请填写客户的 电话。</li>';
			}elseif(strlen($this->kclass->input['telephone'])>50){
				$e .= '<li>客户的电话不能超过 50 个英文字符。</li>';
			}
			if($this->kclass->input['email'] == ''){
				$e .= '<li>请填写客户的 邮箱。</li>';
			}elseif(strlen($this->kclass->input['email'])>50){
				$e .= '<li>客户的邮箱不能超过 150 个英文字符。</li>';
			}
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '修改客户',
				'text' => '您在修改客户的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$sourcedate=0;
		if($this->kclass->input['sourcedate']!=''){
			$dd=explode('-',$this->kclass->input['sourcedate']);
			$sourcedate=mktime(0,0,0,$dd[1],$dd[2],$dd[0]);
		}
		$this->kclass->DB->query("
			UPDATE `customer`
			SET `title` = '".$this->kclass->input['title']."', 
				`cntitle` = '".$this->kclass->input['cntitle']."', 
				`abbr` = '".$this->kclass->input['abbr']."', 
				`businesstypeid` = '".$this->kclass->input['businesstypeid']."', 
				`customersourceid` = '".$this->kclass->input['customersourceid']."', 
				`sourcedate` = '".$sourcedate."',
				`customerdomainid` = '".$this->kclass->input['customerdomainid']."', 
				`address` = '".$this->kclass->input['address']."', 
				`regionid` = '".$this->kclass->input['regionid']."', 
				`cnaddress` = '".$this->kclass->input['cnaddress']."', 
				`postalcode` = '".$this->kclass->input['postalcode']."', 
				`website` = '".$this->kclass->input['website']."', 
				`linkman` = '".$this->kclass->input['linkman']."', 
				`position` = '".$this->kclass->input['position']."', 
				`linkman2` = '".$this->kclass->input['linkman2']."', 
				`position2` = '".$this->kclass->input['position2']."', 
				`email` = '".$this->kclass->input['email']."', 
				`email2` = '".$this->kclass->input['email2']."',
				`telephone` = '".$this->kclass->input['telephone']."', 
				`telephone2` = '".$this->kclass->input['telephone2']."', 
				`mobile` = '".$this->kclass->input['mobile']."', 
				`mobile2` = '".$this->kclass->input['mobile2']."', 
				`fax` = '".$this->kclass->input['fax']."', 
				`fax2` = '".$this->kclass->input['fax2']."', 
				`nickname` = '".$this->kclass->input['nickname']."', 
				`nickname2` = '".$this->kclass->input['nickname2']."', 
				`bankdetail` = '".$this->kclass->input['bankdetail']."', 
				`remark` = '".$this->kclass->input['remark']."', 
				`modified` = '".TIMENOW."', 
				`modifier` = '".$this->kclass->user['userid']."',
				`tracker` = '".$this->kclass->input['tracker']."'
			WHERE customerid='".$this->kclass->input['customerid']."'
		");
		$this->kclass->updateAttachs(array('module'=>'customer', 'mid'=>$this->kclass->input['customerid']));
		if($this->kclass->input['rt']=='view'){
			$rt = '&action=view&customerid='.$this->kclass->input['customerid'];
		}elseif($this->kclass->input['rt']=='list'){
			$rt = '#'.$this->kclass->input['customerid'];
		}
		$this->kclass->messager(array(
			'title' => '修改客户',
			'text' => '客户 <b>'.$this->kclass->input['title'].'</b> 的信息已成功修改!',
			'url' => '/s.php?module=customer'.$rt,
			'sec' => 3
		));
	}

	//
	function update(){
		if($this->kclass->input['customerid']<=0 OR !$customer = $this->kclass->DB->queryFirst("SELECT `customer`.*, m.username AS modifier, c.username AS creator FROM `customer` LEFT JOIN `user` AS m ON (m.userid=`customer`.modifier) LEFT JOIN `user` AS c ON (c.userid=`customer`.creator) WHERE `customerid`='".$this->kclass->input['customerid']."'")){
			$this->kclass->boinkIt('/s.php?module=customer');
		}
		$this->kclass->br2nl=true;
		$customer['bankdetail']=$this->kclass->parseConvertValue($customer['bankdetail']);
		$customer['remark']=$this->kclass->parseConvertValue($customer['remark']);
		$business = $this->kclass->chooserBusinesstype(array('name'=>'businesstypeid', 'width'=>350,'hasBlank'=>1, 'selectedid'=>$customer['businesstypeid']));
		$source = $this->kclass->chooserCustomerSource(array('name'=>'customersourceid', 'hasBlank'=>1, 'width'=>350, 'selectedid'=>$customer['customersourceid']));
		$domain = $this->kclass->chooserCustomerDomain(array('name'=>'customerdomainid', 'hasBlank'=>1, 'width'=>350, 'selectedid'=>$customer['customerdomainid']));
		$region = $this->kclass->chooserRegion(array('regionid'=>$customer['regionid'],'hasBlank'=>1,'cn'=>0));
		$upload = $this->kclass->upload(array('module'=>'customer', 'mid'=>$customer['customerid']));
		$operator = $creator = '由 '.$customer['creator'].' 建于 '.date('y-m-d H:m', $customer['created']);
		if($customer['modifier']!=''){
			$operator .= ', '.$customer['modifier'].' 改于 '.date('Y-m-d H:m', $customer['modified']);
		}
		
		$tracker = $this->kclass->chooserTracker(array('name'=>'tracker','condition'=>'positionid=7','width'=>80,'selectedid'=>$customer['tracker']));
		$operator.='。';
		if($customer['sourcedate']==0){
			$sourcedate='2014-01-01';
		}else{
			$sourcedate=date('Y-m-d',$customer['sourcedate']);
		}
$body = <<<EOF
<form action="/s.php?module=customer&action=doupdate" name="customer" method="post" onsubmit="return dc.customer.check(this);">
<input type="hidden" name="module" value="customer">
<input type="hidden" name="action" value="doupdate">
<input type="hidden" name="customerid" value="{$this->kclass->input['customerid']}">
<input type="hidden" name="rt" value="{$this->kclass->input['rt']}">
<table class="hundred">
<thead>
<tr>
	<th colspan="4"><span class="small right">{$operator}</span>客户修改：{$customer['title']}</th>
</tr>
</thead>
<tbody>
<tr class="odd">
	<td>英文名称：<span class="red bold">*</span></td>
	<td><input type="text" style="width:450px" name="title" value="{$customer['title']}"></td>
	<td>简　　称：<span class="red bold">*</span></td>
	<td><input type="text" size="20" name="abbr" value="{$customer['abbr']}"> <span class="gray small">不要超过10个英文字符，或5个中文字</span></td>
</tr>
<tr class="even">
	<td>中文名称：</td>
	<td><input type="text" style="width:450px" name="cntitle" value="{$customer['cntitle']}"></td>
	<td>商业类型：</td>
	<td>{$business} <span class="gray small">选择一个最符合的类型</span></td>
</tr>
<tr class="odd">
	<td>英文地址：<span class="red bold">*</span></td>
	<td><input type="text" style="width:450px" name="address" value="{$customer['address']}"></td>
	<td>国　　家：<span class="red bold">*</span></td>
	<td>{$region}　业务人员：{$tracker}</td>
</tr>
<tr class="even">
	<td>中文地址：</td>
	<td><input type="text" style="width:348px" name="cnaddress" value="{$customer['cnaddress']}"> 邮编：<input type="text" style="width:50px" name="postalcode" value="{$customer['postalcode']}"></td>
	<td>网　　站：</td>
	<td><input type="text" style="width:450px" name="website" value="{$customer['website']}"></td>
</tr>
<tr class="even">
	<td>客户来源：</td>
	<td>{$source} <span class="gray small">选择一个最符合的类型</span> 　来源时间：<input type="text" name="sourcedate" id="sourcedate" size="10"  value="{$sourcedate}"></td>
	<td>行业领域：</td>
	<td>{$domain} <span class="gray small">选择一个最符合的类型</span></td>
</tr>
<tr class="odd">
	<td>银行资料：</td>
	<td><textarea name="bankdetail" style="width:450px;height:100px">{$customer['bankdetail']}</textarea></td>
	<td>备　　注：</td>
	<td><textarea name="remark" style="width:450px;height:100px">{$customer['remark']}</textarea></td>
</tr>

<tr class="even">
	<td colspan="2" align="center"><span class="bold">主联系人：</span><span class="red bold">*</span></td>
	<td colspan="2" align="center"><span class="bold">次联系人：</span></td>
</tr>

<tr class="odd">
	<td>姓　　名：<span class="red bold">*</span></td>
	<td><input type="text" style="width:160px" name="linkman" value="{$customer['linkman']}"> 称谓：<span class="red bold">*</span><input type="text" style="width:170px" name="position" value="{$customer['position']}"><div class="gray small">联系人称谓，首选职位，如无职位则请标明先生/女士/小姐。</div></td>
	<td>姓　　名：<span class="red bold">*</span></td>
	<td><input type="text" style="width:160px" name="linkman2" value="{$customer['linkman2']}"> 称谓：<span class="red bold">*</span><input type="text" style="width:170px" name="position2" value="{$customer['position2']}"><div class="gray small">联系人称谓，首选职位，如无职位则请标明先生/女士/小姐。</div></td>
</tr>
<tr class="even">
	<td>昵　　称：<span class="red bold">*</span></td>
	<td><input type="text" style="width:320px" name="nickname" value="{$customer['nickname']}"/> <span class="gray small">用于发送邮件时的称呼</span></td>
	<td>昵　　称：<span class="red bold">*</span></td>
	<td><input type="text" style="width:320px" name="nickname2" value="{$customer['nickname2']}"/> <span class="gray small">用于发送邮件时的称呼</span></td>
</tr>
<tr class="odd">
	<td>邮　　箱：<span class="red bold">*</span></td>
	<td><input type="text" style="width:220px" name="email" value="{$customer['email']}"> 电话：<span class="red bold">*</span><input type="text" style="width:170px" name="telephone" value="{$customer['telephone']}"><div class="gray">电话与传真格式：“<span class="big bold">+</span><i class="small">国家区号</i> <span class="big bold">-</span><i class="small">地区区号</i><span class="small">(如果有)</span> <span class="big bold">-</span><i class="small">电话号码</i>”</div></td>
	<td>邮　　箱：<span class="red bold">*</span></td>
	<td><input type="text" style="width:220px" name="email2" value="{$customer['email2']}"> 电话：<span class="red bold">*</span><input type="text" style="width:170px" name="telephone2" value="{$customer['telephone2']}"><div class="gray">电话与传真格式：“<span class="big bold">+</span><i class="small">国家区号</i> <span class="big bold">-</span><i class="small">地区区号</i><span class="small">(如果有)</span> <span class="big bold">-</span><i class="small">电话号码</i>”</div></td>
</tr>
<tr class="even">
	<td>手　　机：</td>
	<td><input type="text" style="width:228px" name="mobile" value="{$customer['mobile']}"> 传真：<input type="text" style="width:170px" name="fax" value="{$customer['fax']}"><div class="gray">手机格式与电话相同，如：“+86-0138-28866666”</div></td>
	<td>手　　机：</td>
	<td><input type="text" style="width:228px" name="mobile2" value="{$customer['mobile2']}"> 传真：<input type="text" style="width:170px" name="fax2" value="{$customer['fax2']}"><div class="gray">手机格式与电话相同，如：“+86-0138-28866666”</div></td>
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
		$this->kclass->tbline+=2;
		$this->kclass->page['onload'] .= 'dc.tabhover();dc.tabhover();$(\'#sourcedate\').datepicker()';
		$this->kclass->page['title'] .= ' - 修改 - '.$customer['title'];
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 修改客户 - '.$customer['title'], 'right' => '<a href="/s.php?module=customer">返回列表</a>', 'body'=>$body));
	}
	//
	function kill(){
		if($this->kclass->input['customerid']<=0){
			$this->kclass->boinkIt('/s.php?module=customer');
		}
		if($this->kclass->input['confirm'] == 0){
			$this->kclass->boinkIt('/s.php?module=customer');
		}
		if($this->kclass->input['customerid'] < 0){
			$e = '<li>请从正确的页面进行操作，如果此问题持续存在，请联系网站管理员。</li>';
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '删除客户',
				'text' => '您在删除客户的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		if($this->kclass->input['rt']=='view'){
			$rt = '&action=view&customerid='.$this->kclass->input['customerid'];
		}elseif($this->kclass->input['rt']=='list'){
			$rt = '#'.$this->kclass->input['customerid'];
		}
		$customer = $this->kclass->DB->queryFirst("
			SELECT title
			FROM customer
			WHERE customerid='".$this->kclass->input['customerid']."'
		");
		if($customer){
			$this->kclass->DB->query("
				UPDATE `customer`
				SET killed=".TIMENOW." 
				WHERE customerid='".$this->kclass->input['customerid']."'
			");
			$this->kclass->messager(array(
				'title' => '删除客户成功',
				'text' => '客户 <b>'.$customer['title'].'</b> 已成功被标记为删除!',
				'url' => '/s.php?module=customer'.$rt,
				'sec' => 3
			));
		}else{
			$this->kclass->messager(array(
				'title' => '删除客户失败',
				'text' => '您要删除的客户，不存在！',
				'url' => '/s.php?module=customer'.$rt,
				'sec' => 3
			));
		}
	}

	//
	function remove(){
		if($this->kclass->input['customerid']<=0){
			$this->kclass->boinkIt('/s.php?module=customer');
		}
		$customer = $this->kclass->DB->queryFirst("
			SELECT title
			FROM customer
			WHERE customerid='".$this->kclass->input['customerid']."'
		");
$body = <<<EOF
<form action="/s.php?module=customer&action=kill" name="customer" method="post">
<input type="hidden" name="module" value="customer">
<input type="hidden" name="action" value="kill">
<input type="hidden" name="customerid" value="{$this->kclass->input['customerid']}">
<table>
<thead>
<tr>
	<th>删除客户：{$customer['title']}</th>
</tr>
</thead>
<tbody>
<tr class="odd">
	<td class="middle">你确定要删除客户: <a href="/s.php?module=customer&action=view&customerid={$this->kclass->input['customerid']}" class="big bold" target="_blank">{$customer['title']}</a> 吗?</td>
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
		$this->kclass->page['title'] .= ' - 删除 - '.$customer['title'];
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 删除客户 - '.$customer['title'], 'right' => '<a href="/s.php?module=customer">返回列表</a>', 'body'=>$body));
	}

	//
	function revival(){
		if($this->kclass->input['customerid']<=0){
			$this->kclass->boinkIt('/s.php?module=customer');
		}
		if($this->kclass->input['confirm'] == 0){
			$this->kclass->boinkIt('/s.php?module=customer');
		}
		if($this->kclass->input['customerid'] < 0){
			$e = '<li>请从正确的页面进行操作，如果此问题持续存在，请联系网站管理员。</li>';
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '恢复客户',
				'text' => '您在恢复客户的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$customer = $this->kclass->DB->queryFirst("
			SELECT title
			FROM customer
			WHERE customerid='".$this->kclass->input['customerid']."'
		");
		if($this->kclass->input['rt']=='view'){
			$rt = '&action=view&customerid='.$this->kclass->input['customerid'];
		}elseif($this->kclass->input['rt']=='list'){
			$rt = '#'.$this->kclass->input['customerid'];
		}
		if($customer){
			$this->kclass->DB->query("
				UPDATE `customer`
				SET killed=0
				WHERE customerid='".$this->kclass->input['customerid']."'
			");
			$this->kclass->messager(array(
				'title' => '恢复客户成功',
				'text' => '客户 <b>'.$customer['title'].'</b> 已成功被恢复！',
				'url' => '/s.php?module=customer'.$rt,
				'sec' => 3
			));
		}else{
			$this->kclass->messager(array(
				'title' => '恢复客户失败',
				'text' => '您要恢复的客户不存在！',
				'url' => '/s.php?module=customer'.$rt,
				'sec' => 3
			));
		}
	}

	//
	function restore(){
		if($this->kclass->input['customerid']<=0){
			$this->kclass->boinkIt('/s.php?module=customer');
		}
		$customer = $this->kclass->DB->queryFirst("
			SELECT title
			FROM customer
			WHERE customerid='".$this->kclass->input['customerid']."'
		");
$body = <<<EOF
<form action="/s.php?module=customer&action=revival" name="customer" method="post">
<input type="hidden" name="module" value="customer">
<input type="hidden" name="action" value="revival">
<input type="hidden" name="customerid" value="{$this->kclass->input['customerid']}">
<table>
<thead>
<tr>
	<th>恢复客户：{$customer['title']}</th>
</tr>
</thead>
<tbody>
<tr class="even">
	<td class="middle">你确定要恢复客户: <a href="/s.php?module=customer&action=view&customerid={$this->kclass->input['customerid']}" class="big bold" target="_blank">{$customer['title']}</a> 吗?</td>
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
		$this->kclass->page['title'] .= ' - 恢复 - '.$customer['title'];
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 恢复客户 - '.$customer['title'], 'right' => '<a href="/s.php?module=customer">返回列表</a>', 'body'=>$body));
	}
	function find(){
		$tracker = $this->kclass->chooserTracker(array('name'=>'tracker','condition'=>'positionid=7','hasBlank'=>1,'width'=>115));
		$areas = $this->kclass->chooserArea(array('name'=>'areaid','hasBlank'=>1,'width'=>115));
		$region = $this->kclass->chooserRegion(array('regionid'=>0, 'hasBlank'=>1));
		$business = $this->kclass->chooserBusinesstype(array('name'=>'businesstypeid', 'hasBlank'=>1, 'width'=>350));
		$source = $this->kclass->chooserCustomerSources(array('name'=>'customersourceid', 'hasBlank'=>1, 'width'=>350));
		$domain = $this->kclass->chooserCustomerDomain(array('name'=>'customerdomainid', 'hasBlank'=>1, 'width'=>350));
$body = <<<EOF
<form action="/s.php?module=customer&action=list" name="customer" method="post">
<input type="hidden" name="module" value="customer">
<input type="hidden" name="action" value="list">
<table>
<thead>
<tr>
	<th colspan="2">查找客户</th>
</tr>
</thead>
<tbody>
<tr class="even">
	<td>客户ID 是：</td>
	<td><input type="text" style="width:244px" name="id" value="{$customer['customerid']}"></td>
</tr>
<tr class="odd">
	<td>客户名称 包含：</td>
	<td><input type="text" style="width:244px" name="title" id="autoComplete" value="{$customer['title']}"> <span class="small gray">可使用客户名称中的关键字查找</span></td>
</tr>
<tr class="even">
	<td>且 联系人名称 包含：</td>
	<td><input type="text" style="width:244px" name="linkman" value=""> <span class="small gray">主联系人或次联系人的名称</span></td>
</tr>
<tr class="odd">
	<td>且 联系人昵称 包含：</td>
	<td><input type="text" style="width:244px" name="nickname" value=""> <span class="small gray">主联系人或次联系人的昵称</span></td>
</tr>
<tr class="even">
	<td>且 联系人邮箱 包含：</td>
	<td><input type="text" style="width:244px" name="email" value=""> <span class="small gray">主联系人或次联系人的邮箱</span></td>
</tr>
<tr class="odd">
	<td>且 联系人电话 包含：</td>
	<td><input type="text" style="width:244px" name="phone" value=""> <span class="small gray">主联系人或次联系人的电话</span></td>
</tr>
<tr class="even">
	<td>且 联系人手机 包含：</td>
	<td><input type="text" style="width:244px" name="mobile" value=""> <span class="small gray">主联系人或次联系人的手机</span></td>
</tr>
<tr class="odd">
	<td>且 联系人传真 包含：</td>
	<td><input type="text" style="width:244px" name="fax" value=""> <span class="small gray">主联系人或次联系人的传真</span></td>
</tr>
<tr class="odd">
	<td>且 商业类型 是：</td>
	<td>{$business}</td>
</tr>
<tr class="odd">
	<td>且 客户来源 是：</td>
	<td>{$source}</td>
</tr>
<tr class="odd">
	<td>且 来源时间 是：</td>
	<td>开始时间：<input type='text' id='startDate' name='startDate' value=''>结束时间：<input type='text' id='endDate' name='endDate' value=''></td>
</tr>
<tr class="odd">
	<td>且 创建时间 是：</td>
	<td>开始时间：<input type='text' id='startCreateDate' name='startCreateDate' value=''>结束时间：<input type='text' id='endCreateDate' name='endCreateDate' value=''></td>
</tr>
<tr class="odd">
	<td>且 行业领域 是：</td>
	<td>{$domain}</td>
</tr>
<tr class="even">
	<td>且 业务人员 是：</td>
	<td>{$tracker}</td>
</tr>
<tr class="odd">
	<td>且 所属地区 是：</td>
	<td>{$areas}</td>
</tr>
<tr class="even">
	<td>且 所在国家地区 是：</td>
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
		<label for="o1"><input type="radio" name="orderby" value="customerid" id="o1">ID</label> 
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
		<label for="t0"><input type="radio" id="t0" name="layout" value="list" checked>列表</label> 
		<label for="t1"><input type="radio" id="t1" name="layout" value="grid">格子</label> 
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

<script type="text/javascript">
$(function(){
	$("#autoComplete").inputAutoComplete({location:"/s.php?module=ajax&action=findCustomer",datatype:"json",selectcount:"scrollbar",scrollbarcount:9});
});
</script>
EOF;
		$this->kclass->page['onload'] .= 'dc.tabhover();dc.tabhover();var dates=$(\'#startDate,#endDate\').datepicker({onSelect:function(selectedDate){var option=this.id==\'startDate\'?\'minDate\':\'maxDate\',instance=$(this).data(\'datepicker\'),date=$.datepicker.parseDate(instance.settings.dateFormat || $.datepicker._defaults.dateFormat,selectedDate,instance.settings);dates.not(this).datepicker(\'option\',option,date);}});var dates2=$(\'#startCreateDate,#endCreateDate\').datepicker({onSelect:function(selectedDate){var option2=this.id==\'startCreateDate\'?\'minDate\':\'maxDate\',instance2=$(this).data(\'datepicker\'),date2=$.datepicker.parseDate(instance2.settings.dateFormat || $.datepicker._defaults.dateFormat,selectedDate,instance2.settings);dates2.not(this).datepicker(\'option\',option2,date2);}});';
		$this->kclass->page['title'] .= ' - 查找客户';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 查找客户', 'right' => '<a href="/s.php?module=customer">返回列表</a>', 'body'=>$body));
	}
	//
	function mmlist_test(){
     // print_r($this->kclass->input);
	  $body .= '
		<div class="title"></div>
		<dl id="customer" class="tabs">
			<dt title="订单数超过3个包括3个的">常客('.$customerNum['high'].')</dt>
			<dt title="订单数超过1个小于3个的">新客('.$customerNum['middle'].')</dt>
			<dt title="没有下过订单的">顾客('.$customerNum['low'].')</dt>
			<dt title="所有的顾客">所有顾客('.$customerNum['all'].')</dt>
		';
	  $customerTitle=array(
		 'high'=>'常客(超过3个订单)',
				'middle'=>'新客(超过1个小于3个订单)',
				'low'=>'顾客(没有订单)',
		        'all'=>'顾客（所有）'
		  );
		  $customerType = array(
			'max'=>3,
			'min'=>1	
		);
         $customer=$this->kclass->DB->query("SELECT customerid FROM `customer`");
		 if($num=$this->kclass->DB->numRows()){
			 $high=$middle=$low=$all=array();
			 while($customers=$this->kclass->DB->fetchArray($customer)){
                      $orderNum = $this->kclass->DB->queryFirst("
					SELECT COUNT(`orderid`) AS num 
					FROM `order`
					WHERE `order`.customerid = '".$customers['customerid']."'
				");
				if($orderNum['num']>=$customerType['max']){
					$high[]=$customers['customerid'];
				}elseif($orderNum['num']<$customerType['max']&&$orderNum['num']>=1){
                    $middle[]=$customers['customerid'];
				}elseif($orderNum['num']<1){
					$low[]=$customers['customerid'];
				}
				$all[]=$customers['customerid'];
			 }
		 }
		
		  foreach($customerTitle as $k=>$val){
             $body .= '<dd>'; 
			$body .= '<table class="hundred mytable tablesorter"><thead><tr><th width="26">ID</th><th width="35">订单</th><th width="100">审核状态</th><th>客户名称</th><th width="40">来源</th><th width="60">来源时间</th><th>联系人</th><th>邮箱</th><th width="150">电话</th><th >地区</th><th width="50">销售</th><th width="90">创建</th><th width="90">修改</th><th width="26">选项</th></tr></thead><tbody>';
			  if($this->kclass->input['page']==''){
			$showpage=1;
		    }else{
			$showpage=$this->kclass->input['page'];
		    }
		   if($k=='high'){
			$customerData=$this->kclass->customerItems(array('customerid'=>$high,'page'=>$showpage));
				if($customerData['num']>1){
					$body .= $customerData['panel'];	
					
				}else{
					$body .= '<tr><td>暂无相关记录</td></tr>';	
				}			
			}else if($k=='middle'){
			$customerData=$this->kclass->customerItems(array('customerid'=>$middle,'page'=>$showpage));
				if(isset($customerData['num'])){
					$body .= $customerData['panel'];	
					
				}else{
					$body .= '<tr><td>暂无相关记录</td></tr>';	
				}
			}else if($k=='low'){
			$customerData=$this->kclass->customerItems(array('customerid'=>$low,'page'=>$showpage));
				if(isset($customerData['num'])){
					$body .= $customerData['panel'];	
					
				}else{
					$body .= '<tr><td>暂无相关记录</td></tr>';	
				}	
			}else if($k=='all'){
                $customerData=$this->kclass->customerItems(array('customerid'=>$all,'page'=>$showpage));
				if(isset($customerData['num'])){
					$body .= $customerData['panel'];	
					
				}else{
					$body .= '<tr><td>暂无相关记录</td></tr>';	
				}
			}
			$body .=  '</tbody></table></dd>';
		  }
	  
			$body .= "</dl>
		<script type=\"text/javascript\">
				$('.tablesorter').tablesorter( { headers: {} } );$('.mytable').fixedtableheader();
		</script>		
		";
		$this->kclass->page['onload'] .= "dc.listhover();dc.tabhover();dc.tabs({'id':'customer'});";
		$this->kclass->page['title'] .= ' - 列表';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 列表<span class="tiny">('.$sum.')</span>', 'right' => $this->right.'　|　<span class="small">排序：</span>'.$orderby['link'].'|　<span class="small">显示：</span>'.$showLink.'　|　<span class="small">布局：</span>'.$layoutLink.'　|　<a href="/p.php?module=printer&action=exportcustomerinfo" target="_blank">导出客户资料</a>　<a href="/s.php?module=customer&action=quotingstatistics" >报价单统计</a>　<a href="/s.php?module=customer&action=inquirystatistics" >询价单统计</a>
' , 'body'=>$body));
	}
	// 
	function mmlist(){
		//如果ID存在，直接转向到View页面
		//print_r($this->kclass->input);
		$this->kclass->input['id']=intval($this->kclass->input['id']);
		if($this->kclass->input['id']>0){
			$customer=$this->kclass->DB->queryFirst("SELECT customerid FROM `customer` WHERE `customer`.customerid='".$this->kclass->input['id']."'");
			if($customer){
				$this->kclass->boinkIt('/s.php?module=customer&action=view&customerid='.$customer['customerid']);
			}
		}

		if(!$this->kclass->input['show']=='all'){
			$condition='`customer`.killed=0 AND `customer`.customerid>2';
		}else{
			$condition='1=1 AND `customer`.customerid>2';
		}
		
		$this->kclass->input['title']=trim($this->kclass->input['title']);
		$this->kclass->input['linkman']=trim($this->kclass->input['linkman']);
		$this->kclass->input['phone']=trim($this->kclass->input['phone']);
		$this->kclass->input['mobile']=trim($this->kclass->input['mobile']);
		$this->kclass->input['email']=trim($this->kclass->input['email']);
		if($this->kclass->input['title']!=''){
			$condition .= " AND INSTR(LCASE(`customer`.title),'".(strtolower($this->kclass->input['title']))."')>0";
			$query['title']=$this->kclass->input['title'];
		}
		if($this->kclass->input['linkman']!=''){
			$condition .= " AND (INSTR(LCASE(`customer`.linkman),'".(strtolower($this->kclass->input['linkman']))."')>0 OR INSTR(LCASE(`customer`.linkman2),'".(strtolower($this->kclass->input['linkman']))."')>0)";
			$query['linkman']=$this->kclass->input['linkman'];
		}
		if($this->kclass->input['nickname']!=''){
			$condition .= " AND (INSTR(LCASE(`customer`.nickname),'".(strtolower($this->kclass->input['nickname']))."')>0 OR INSTR(LCASE(`customer`.nickname2),'".(strtolower($this->kclass->input['nickname']))."')>0)";
			$query['phone']=$this->kclass->input['phone'];
		}
		if($this->kclass->input['phone']!=''){
			$condition .= " AND (INSTR(LCASE(`customer`.telephone),'".(strtolower($this->kclass->input['phone']))."')>0 OR INSTR(LCASE(`customer`.telephone2),'".(strtolower($this->kclass->input['phone']))."')>0)";
			$query['phone']=$this->kclass->input['phone'];
		}
		if($this->kclass->input['email']!=''){
			$condition .= " AND (INSTR(LCASE(`customer`.email),'".(strtolower($this->kclass->input['email']))."')>0 OR INSTR(LCASE(`customer`.email2),'".(strtolower($this->kclass->input['email']))."')>0)";
			$query['email']=$this->kclass->input['email'];
		}
		if($this->kclass->input['mobile']!=''){
			$condition .= " AND (INSTR(LCASE(`customer`.mobile),'".(strtolower($this->kclass->input['mobile']))."')>0 OR INSTR(LCASE(`customer`.mobile2),'".(strtolower($this->kclass->input['mobile']))."')>0)";
			$query['phone']=$this->kclass->input['phone'];
		}
		if($this->kclass->input['fax']!=''){
			$condition .= " AND (INSTR(LCASE(`customer`.fax),'".(strtolower($this->kclass->input['fax']))."')>0 OR INSTR(LCASE(`customer`.fax2),'".(strtolower($this->kclass->input['fax2']))."')>0)";
			$query['phone']=$this->kclass->input['phone'];
		}
		if($this->kclass->input['businesstypeid']>0){
			$condition .= " AND `customer`.businesstypeid='".$this->kclass->input['businesstypeid']."'";
			$query['businesstypeid']=$this->kclass->input['businesstypeid'];
		}
		if($this->kclass->input['customersourceid']>0){
			$sourceid=implode(",",$this->kclass->input['customersourceid']);
			$condition .= " AND `customer`.customersourceid in (".$sourceid.")";
			$query['customersourceid']=$this->kclass->input['customersourceid'];
		}
		$startdate=0;
		$enddate=0;
		if($this->kclass->input['startDate']){
			$st=explode('-',$this->kclass->input['startDate']);
			$startdate=mktime(0,0,0,$st[1],$st[2],$st[0]);	
		}
		if($this->kclass->input['endDate']){
			$ed=explode('-',$this->kclass->input['endDate']);
		$enddate=mktime(0,0,0,$ed[1],$ed[2],$ed[0]);
		}

		$createdstartdate=$createdenddate=0;
		if($this->kclass->input['startCreateDate']){
			$csd=explode('-',$this->kclass->input['startCreateDate']);
			$createdstartdate=mktime(0,0,0,$csd[1],$csd[2],$csd[0]);
		}
		if($this->kclass->input['endCreateDate']){
			$ced=explode('-',$this->kclass->input['endCreateDate']);
			$createdenddate=mktime(0,0,0,$ced[1],$ced[2],$ced[0]);
		}
		if($this->kclass->input['startCreateDate']!=''&&$this->kclass->input['endCreateDate']=''){
           $condition .= " AND `customer`.created>='".$createdstartdate."' and `customer`.created<='".$createdenddate."' ";
		}elseif($this->kclass->input['startCreateDate']=='' && $this->kclass->input['endCreateDate']!=''){
			$condition .= " AND  `customer`.created<='".$createdenddate."' ";
		}elseif($this->kclass->input['startCreateDate']!='' && $this->kclass->input['endCreateDate']==''){
			$condition .= " AND  `customer`.created>='".$createdstartdate."' ";
		}
		if($this->kclass->input['startDate']!='' && $this->kclass->input['endDate']!=''){
			
			$condition .= " AND `customer`.sourcedate>='".$startdate."' and `customer`.sourcedate<='".$enddate."' ";
			//$query['sourcedate']=$sourcedate;
		}elseif($this->kclass->input['startDate']=='' && $this->kclass->input['endDate']!=''){
			$condition .= " AND  `customer`.sourcedate<='".$enddate."' ";
		}elseif($this->kclass->input['startDate']=='' && $this->kclass->input['endDate']==''){
			$condition .= " AND  `customer`.sourcedate>='".$startdate."' ";
		}
        
		if($this->kclass->input['customerdomainid']>0){
			$condition .= " AND `customer`.customerdomainid='".$this->kclass->input['customerdomainid']."'";
			$query['customerdomainid']=$this->kclass->input['customerdomainid'];
		}

		if($this->kclass->input['tracker']>0){
			$condition .= " AND `customer`.tracker='".$this->kclass->input['tracker']."'";
			$query['tracker']=$this->kclass->input['tracker'];
		}
		
		if($this->kclass->input['areaid']>0){
			
			$idArr = $this->kclass->DB->query("
						SELECT regionid
						FROM `region`
						WHERE areaid='".$this->kclass->input['areaid']."'");
			$arr = array();
			if($this->kclass->DB->numRows()){
				while($id = $this->kclass->DB->fetchArray($idArr)){
					array_push($arr, $id['regionid']);
				}
				$regionidArr=implode(",", $arr);
				$condition .= " AND `customer`.regionid IN (".$regionidArr.")";
			}
			$query['areaid']=$this->kclass->input['areaid'];
		}else{
			//根据所在国家查找客户(之前的查找不精确)
			if($this->kclass->input['countryid']>0){
				if($this->kclass->input['provinceid']>0){
					$condition .= " AND `customer`.regionid='".$this->kclass->input['regionid']."'";
				}else{
					$idArr = $this->kclass->DB->query("
							SELECT regionid 
							FROM `region`
							WHERE countryid='".$this->kclass->input['regionid']."'");
					$arr = array();
					if($this->kclass->DB->numRows()){
						while($id = $this->kclass->DB->fetchArray($idArr)){
							array_push($arr, $id['regionid']);
						}
						$regionidArr=implode(",", $arr);
						$condition .= " AND `customer`.regionid IN (".$this->kclass->input['regionid'].",".$regionidArr.")";
					}
				}
			}
		}
		/* 之前根据国家查找客户的方法
		if($this->kclass->input['regionid']>0){
			$condition .= " AND `customer`.regionid='".$this->kclass->input['regionid']."'";
			$query['regionid']=$this->kclass->input['regionid'];
		}
		*/
		
		if($this->kclass->input['orderby']!='')$query['orderby']=$this->kclass->input['orderby'];
		if($this->kclass->input['direction']!='')$query['direction']=$this->kclass->input['direction'];
		if($this->kclass->input['show']!='')$query['show']=$this->kclass->input['show'];
		if($this->kclass->input['layout']!=''){
			$query['layout']=$this->kclass->input['layout'];
		}else{
			$query['layout']=$this->kclass->input['layout']='list';
		}
       
	   if($this->kclass->input['start']==''){
			$b['start']=0;
		}else{
			$b['start']=$this->kclass->input['start'];
		}
		if($this->kclass->input['perpage']=='')$b['perpage']=20;

		$orderby=$this->kclass->orderby(array('module'=>'customer','direction'=>'asc','orderby'=>'customer.modified', 'default'=>'title', 'serial'=>array(array('title'=>'名称', 'field'=>'title','word'=>'name'), array('title'=>'ID', 'field'=>'customerid','word'=>'id'), array('title'=>'修改时间', 'field'=>'modified'), array('title'=>'建立时间', 'field'=>'created'), array('title'=>'国家', 'field'=>'regionid')),'appendUrl'=>$query));
		if(is_array($query) AND count($query)>0)$queryPart='&'.http_build_query($query);
		
		//定义区分客户类型的数组
		$customerType = array(
			'max'=>3,
			'min'=>1	
		);
		//定义客户类型的标题
		$customerTitle = array(
				'high'=>'常客(超过3个订单)',
				'middle'=>'新客(超过1个小于3个订单)',
				'low'=>'顾客(没有订单)',
		        'all'=>'顾客（所有）'
		);
		$customerData = array();	
		$customerNum = array();
		$customerNum['low'] = 0;
		$customerNum['middle'] = 0;
		$customerNum['high'] = 0;
		$customerNum['all'] = 0;
		$sql = "
		SELECT `customer`.customerid, `customer`.title, `customer`.cntitle, `customer`.linkman, `customer`.position, `customer`.email, `customer`.telephone, `customer`.regionid, `customer`.cover, `customer`.attachs, `customer`.sourcedate, `customer`.images, `customer`.modified, `customer`.ifverify	,`customer`.verifier,	`customer`.verified, `customer`.created, `customer`.hidden, `customer`.killed, 
				b.title AS businessTitle, 
				s.title AS source, 
				d.title AS domain, 
				region.country, region.state, region.city, 
				m.username AS modifier, c.username AS creator, t.username AS tracker
			FROM `customer` 
			LEFT JOIN `region` ON (region.regionid=customer.regionid) 
			LEFT JOIN `businesstype` AS b ON (b.businesstypeid=`customer`.businesstypeid) 
			LEFT JOIN `customersource` AS s ON (s.customersourceid=customer.customersourceid) 
			LEFT JOIN `customerdomain` AS d ON (d.customerdomainid=customer.customerdomainid) 
			LEFT JOIN `user` AS m ON (m.userid=`customer`.modifier) 
			LEFT JOIN `user` AS c ON (c.userid=`customer`.creator) 
			LEFT JOIN `user` AS t ON (t.userid=`customer`.tracker) 
			WHERE ".$condition."
			ORDER BY ".$orderby['sql']."
		";
		$customers = $this->kclass->DB->query($sql);
		if($sum=$this->kclass->DB->numRows()){
			$i=1;
			while($customer = $this->kclass->DB->fetchArray($customers)){
				$tempStr =  '';				
				$orderNum = $this->kclass->DB->queryFirst("
					SELECT COUNT(`orderid`) AS num 
					FROM `order`
					WHERE `order`.customerid = '".$customer['customerid']."'
				");
				$customer['title'] = $this->kclass->parseConvertValue($customer['title']);
				$title = $this->kclass->iif(strlen($customer[
					'title'])>30, mb_substr($customer['title'], 0, 30, 'UTF-8').'...', $customer['title']);
				if($this->kclass->input['title']!=''){
					$title = $this->kclass->highlight($customer['title'], $this->kclass->input['title']);
				}
				$linkman=$customer['linkman'];
				if($this->kclass->input['linkman']!=''){
					$linkman = $this->kclass->highlight($customer['linkman'], $this->kclass->input['linkman']);
				}
				$email = $this->kclass->iif(strlen($customer['email'])>30, mb_substr($customer['email'], 0, 30, 'UTF-8').'...', $customer['email']);
				if($this->kclass->input['email']!=''){
					$email = $this->kclass->highlight($customer['email'], $this->kclass->input['email']);
				}
				$telephone = $customer['telephone'];
				$mobile = $customer['mobile'];
				if($this->kclass->input['telephone']!=''){
					$telephone = $this->kclass->highlight($telephone, $this->kclass->input['telephone']);
					$mobile = $this->kclass->highlight($mobile, $this->kclass->input['mobile']);
				}
				$region='';
				
				if($customer['regionid']>0){
					$region = $customer['country'].' '.$customer['state'].' '.$customer['city'];
				}
				if($this->kclass->input['countryid']>0){
					if($this->kclass->input['provinceid']>0){
						$region ='<span class="red">'.$customer['country'].' '.$customer['state'].' '.$customer['city'].'</span>';
					}else{
						$region ='<span class="red">'.$customer['country'].'</span>'.' '.$customer['state'].' '.$customer['city'];
					}
				}
				if($customer['sourcedate']!=0){
					$sourcedate=date('Y-m-d',$customer['sourcedate']);
				}else{
					$sourcedate='--';
				}
				/*
				if($customer['regionid']>0){
					$region = $customer['country'].' '.$customer['state'].' '.$customer['city'];
				}
				if($customer['regionid']==$this->kclass->input['regionid']){
					$region = '<span class="red">'.$region.'</span>';
				}
				*/
				$doverify=$this->kclass->doverify(array('module'=>'customer','action'=>'verify'));
				if($doverify){
					$action='(<a		  href="/s.php?module=customer&action=verify&customerid='.$customer['customerid'].'">审核</a>)';
				}
				$status="";
					if($customer['ifverify']==0){
						$status='<span class="darkred">未审核</span>'.$action;
					}elseif($customer['ifverify']==-1){
						$status='<span class="red">审核未通过</span>';
					}elseif($customer['ifverify']==1){
						$status='<span class="green">审核通过</span>';
					}
				$operator = $creator = '由 '.$customer['creator'].' 建于 '.date('y-m-d H:m', $customer['created']);
				if($customer['modifier']!=''){
					$modifier = $customer['modifier'].' 改于 '.date('Y-m-d H:m', $customer['modified']);
					$operator .= ', '.$modifier;
					$modifier = '由 '.$modifier;
				}else{
					$modifier = $creator;
				}

				if($customer['hidden']==0){
					if($customer['killed']>0){
						$link = '<a href="/s.php?module=customer&action=restore&customerid='.$customer['customerid'].'&rt=list">恢复</a>';
					}else{
						$link = '<a href="/s.php?module=customer&action=update&customerid='.$customer['customerid'].'&rt=list">修改</a><br><a href="/p.php?action=customer&customerid='.$customer['customerid'].'" target="_blank">打印</a>';
					}
				}

				if($this->kclass->input['layout']=='list'){//表格
					$tempStr .='<tr>
						<td>'.$this->kclass->id(array('customer'=>$customer['customerid'])).'</td>
						<td align="right" class="red">'.$orderNum['num'].'</td>
						<td >'.$status.'</td>
						<td><a href="/s.php?module=customer&action=view&customerid='.$customer['customerid'].'">'.$customer['customerno'].' <span class="normal" title="'.$customer['title'].'">'.$title.'</span></a><br>
						<span class="small">'.$customer['businessTitle'].'</span> '.$this->kclass->iif($customer['attachs']>0, ' <span class="attachFile" title="有'.$customer['attachs'].'个附件。"></span> ', '').$this->kclass->iif($customer['images']>0, ' <span class="attachImage" title="有'.$customer['images'].'个图片。"></span> ', '').'<span class="normal">'.$this->kclass->iif(strlen($customer['cntitle'])>12, mb_substr($customer['cntitle'], 0, 12, 'UTF-8').'...', $customer['cntitle']).'</span></td>
						<td>'.$customer['source'].'</td>
						<td>'.$sourcedate.'</td>
						<td>'.$linkman.' <span class="small">('.$customer['position'].')</span></td>
						<td>'.$email.'</td>
						<td>电话：'.$telephone.$this->kclass->iif($customer['mobile']!='', '<br>手机：'.$mobile, '').'</td>
						<td>'.$region.'</td>
						<td class="small">'.$customer['tracker'].'</td>
						<td class="small" title="由 '.$customer['creator'].' 创建于 '.date('Y-m-d H:m', $customer['created']).'。">'.$customer['creator'].'<br>'.date('y-m-d H:m', $customer['created']).'</td>'.$this->kclass->iif($customer['modified']>$customer['created'], '<td class="small" title="由 '.$customer['modifier'].' 修改于 '.date('Y-m-d H:m', $customer['modified']).'。">'.$customer['modifier'].'<br>'.date('y-m-d H:m', $customer['modified']).'</td>', '<td>--</td>').'
						<td align="center">'.$link.'</td>
					</tr>';
				}else{//方块列表
					$tempStr .= '<li class="customerli"><a name="'.$customer['customerid'].'"></a>
					<div title="'.$operator.'" class="mmlistt clear">
						<span class="right normal">'.$link.'</span>

								<a href="/s.php?module=customer&action=view&customerid='.$customer['customerid'].'">'.$customer['customerno'].' <span class="normal" title="'.$customer['title'].'">'.$title.'</span></a><br>
						<span class="small">'.$customer['businessTitle'].'</span> '.$this->kclass->iif($customer['attachs']>0, ' <span class="attachFile" title="有'.$customer['attachs'].'个附件。"></span> ', '').$this->kclass->iif($customer['images']>0, ' <span class="attachImage" title="有'.$customer['images'].'个图片。"></span> ', '').'<span class="normal">'.$this->kclass->iif(strlen($customer['cntitle'])>12, mb_substr($customer['cntitle'], 0, 12, 'UTF-8').'...', $customer['cntitle']).'</span></div>
					<div class="mmlistb">
						<div class="normal bold">'.$linkman.' <span class="small">('.$customer['position'].')</span></div>
						<div><span class="small gray">邮箱：</span><a href="mailto:'.$customer['email'].'">'.$email.'</a></div>
						<div><span class="small gray">电话：</span>'.$telephone.'</div>';
					if($customer['fax']!=''){
						$tempStr.='<div><span class="small gray">传真：</span>'.$customer['fax'].'</div>';
					}
					if($customer['mobile']!=''){
						$tempStr.='<div><span class="small gray">手机：</span>'.$mobile.'</div>';
					}
					$tempStr.='
						<div><span class="small gray">地区：</span>'.$region.'</div>
						<div><span class="small gray">订单个数：</span><span class="red">'.$orderNum['num'].'</span></div>
						<div class="small clear"><span class="right" title="'.$creator.'">'.$modifier.'</span></div>
					</div>
					</li>';
				}

				$customerData['all'] .= $tempStr;	
				$customerNum['all']++;
				if($orderNum['num']<$customerType['min']){
					$customerData['low'] .= $tempStr;	
					$customerNum['low']++;
				}else if($orderNum['num']>=$customerType['min'] && $orderNum['num']<$customerType['max']){
					$customerData['middle']	.= $tempStr;
					$customerNum['middle']++;
				}else if($orderNum['num']>=$customerType['max']){
					$customerData['high'] .= $tempStr;	
					$customerNum['high']++;
				}
				unset($tempStr);
				unset($orderNum);
				$i++;
			}
		}
		if($this->kclass->input['layout']=='list'){
			$layoutLink='列表 <a href="/s.php?module=customer&action=list&layout=grid'.str_replace('&layout=list', '', $queryPart).'">格子</a>';
		}else{
			$layoutLink='<a href="/s.php?module=customer&action=list&layout=list'.str_replace('&layout=grid', '', $queryPart).'">列表</a> 格子';
		}
		if($this->kclass->input['show']=='all'){
			$showLink='<a href="/s.php?module=customer&action=list'.str_replace('&show=all', '', $queryPart).'">默认</a> 所有<span class="small gray">(包括删除)</span>';
		}else{
			$showLink='默认 <a href="/s.php?module=customer&action=list&show=all'.str_replace('&show=all', '', $queryPart).'">所有<span class="small gray">(包括删除)</span></a>';
		}
		$body .= '
		<div class="title"></div>
		<dl id="customer" class="tabs">
			<dt title="订单数超过3个包括3个的">常客('.$customerNum['high'].')</dt>
			<dt title="订单数超过1个小于3个的">新客('.$customerNum['middle'].')</dt>
			<dt title="没有下过订单的">顾客('.$customerNum['low'].')</dt>
			<dt title="所有的顾客">所有顾客('.$customerNum['all'].')</dt>
		';
		foreach($customerTitle as $k=>$v){
			$body .= '<dd>'; 
			$body .= $this->kclass->input['layout']=='list' ? '<table class="hundred mytable tablesorter"><thead><tr><th width="26">ID</th><th width="35">订单</th><th width="100">审核状态</th><th>客户名称</th><th width="40">来源</th><th width="60">来源时间</th><th>联系人</th><th>邮箱</th><th width="150">电话</th><th >地区</th><th width="50">销售</th><th width="90">创建</th><th width="90">修改</th><th width="26">选项</th></tr></thead><tbody>' : '<ul id="mmlist" class="mmlist clear">';
               if($perpage=='')$perpage=20;
		   if($k=='high'){
				if(isset($customerData['high'])){
					$body .= $customerData['high'];	
					
				}else{
					$body .= '暂无相关记录';	
				}			
			}else if($k=='middle'){
				if(isset($customerData['middle'])){
					$body .= $customerData['middle'];	
				}else{
					$body .= '暂无相关记录';	
				}	
			}else if($k=='low'){
				if(isset($customerData['low'])){
					$body .= $customerData['low'];	
				}else{
					$body .= '暂无相关记录';	
				}	
			}else if($k=='all'){
                 if(isset($customerData['all'])){
					$body .= $customerData['all'];	
				}else{
					$body .= '暂无相关记录';	
				}	
			}
			$body .= $this->kclass->input['layout']=='list' ? '</tbody></table>' : '</ul>';
		}
		$body .= "</dl>
		<script type=\"text/javascript\">
				$('.tablesorter').tablesorter( { headers: {} } );$('.mytable').fixedtableheader();
		</script>		
		";
		
		$this->kclass->page['onload'] .= "dc.listhover();dc.tabhover();dc.tabs({'id':'customer'});";
		$this->kclass->page['title'] .= ' - 列表';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 列表<span class="tiny">('.$sum.')</span>', 'right' => $this->right.'　|　<span class="small">排序：</span>'.$orderby['link'].'|　<span class="small">显示：</span>'.$showLink.'　|　<span class="small">布局：</span>'.$layoutLink.'　|　<a href="/p.php?module=printer&action=exportcustomerinfo" target="_blank">导出客户资料</a>　<a href="/s.php?module=customer&action=quotingstatistics" >报价单统计</a>　<a href="/s.php?module=customer&action=inquirystatistics" >询价单统计</a>
' , 'body'=>$body));
	}
	//
	function quotingstatistics(){
		$body=<<<EOF
		<form action="/p.php?module=printer&action=printQstatistics" method="post" target="_blank">
		<table>
		<thead><tr><th colspan="4">报价单统计</th></tr></thead>
         <tbody>
		 <tr>
		 <td>或按照客户名：</td><td colspan="3"><input type="text" style="width:250px" name="customer" id="autoComplete" value="{$customer['title']}"> <span class="small gray">可使用客户名称中的关键字查找</span></td>
		 </tr>
		 <tr>
		 <td>或按照类型：</td><td colspan="3">{$this->kclass->chooserQuotingtype(array('name'=>'typeid','hasBlank'=>1,'width'=>250,'selectedid'=>1))}</td>
		 </tr>
		 <tr>
		 <td>或按照国家或地区：</td><td colspan="3">{$this->kclass->chooserRegion(array('name'=>'regionid', 'hasBlank'=>1, 'selectedid'=>1,'width'=>250))}</td>
		 </tr>
		 <tr>
		 <td>或按照运输方式：</td><td colspan="3">{$this->kclass->chooserShipmethod(array('name'=>'shipmethodid', 'hasBlank'=>1, 'width'=>250, 'selectedid'=>$inquiry['shipmethodid']))}</td>
		 </tr>
		 <tr>
		 <td>或按照支付条款：</td><td colspan="3">{$this->kclass->chooserPaymentterm(array('name'=>'paymenttermid', 'hasBlank'=>1, 'width'=>250, 'selectedid'=>$inquiry['paymenttermid']))}</td>
		 </tr>
		 <tr>
		 <td>或按照来源时间：</td><td colspan="3">开始时间：<input type="text" id="sourcestarttime" name="sourcestarttime" >　　结束时间：<input type="text"  id="sourceendtime" name="sourceendtime" ></td>
		 </tr>
		 <tr>
		 <td>或按照发货时间：</td><td colspan="3">开始时间：<input type="text"  id="shipstarttime" name="shipstarttime" >　　结束时间：<input type="text"  id="shipendtime" name="shipendtime" ></td>
		 </tr>
		 <tr>
		 <td>或按照客户来源：</td><td colspan="3">{$this->kclass->chooserCustomerSources(array('name'=>'customersourceid', 'hasBlank'=>1, 'width'=>250))}</td>
		 </tr>
		 <tr>
		 <tr>
		 <th colspan="4">
		选项
		</th>
		</tr>
		<tr class="even">
	<td>排序方式：</td>
	<td colspan="3">
	<label for="o1"><input type="radio" name="orderby" value="customer" id="o1">客户姓名</label>
	<label for="o2"><input type="radio" name="orderby" value="orderid" id="o2" checked >报价单单号</label> 
	<label for="o3"><input type="radio" name="orderby" value="dateline" id="o3">报价时间</label>
	</td>
</tr>
<tr class="odd">
	<td>排列方向：</td>
	<td colspan="3">
		<label for="d0"><input type="radio" id="d0" name="direction" value="ASC" checked>升序(A-Z)</label> 
		<label for="d1"><input type="radio" id="d1" name="direction" value="DESC">降序(Z-A)</label> 
	</td>
</tr>
<tr class="even">
	<td>显示选项：</td>
	<td colspan="3">
		<label for="s0"><input type="radio" id="s0" name="show" value="default" checked>默认列表</label> 
		<label for="s1"><input type="radio" id="s1" name="show" value="all">所有<span class="small gray">(包括删除)</span></label> 
	</td>
</tr>
		 <tr><td  colspan="4" align="center"><input type="submit" name="port" value="提交"><input type="reset" value="重置"></td></tr>
		 </tbody>
		</table>
		</form>
		<script type="text/javascript">
$(function(){
	$("#autoComplete").inputAutoComplete({location:"/s.php?module=ajax&action=findCustomer",datatype:"json",selectcount:"scrollbar",scrollbarcount:9});
});
</script>
EOF;
$this->kclass->page['onload'] .= 'dc.tabhover();$(\'#date\').datepicker();var dates=$(\'#orderstarttime,#orderendtime\').datepicker({onSelect:function(selectedDate){var option=this.id==\'orderstarttime\'?\'minDate\':\'maxDate\',instance=$(this).data(\'datepicker\'),date=$.datepicker.parseDate(instance.settings.dateFormat || $.datepicker._defaults.dateFormat,selectedDate,instance.settings);dates.not(this).datepicker(\'option\',option,date);}});var dates2=$(\'#sourcestarttime,#sourceendtime\').datepicker({onSelect:function(selectedDate){var option2=this.id==\'sourcestarttime\'?\'minDate\':\'maxDate\',instance2=$(this).data(\'datepicker\'),date2=$.datepicker.parseDate(instance2.settings.dateFormat || $.datepicker._defaults.dateFormat,selectedDate,instance2.settings);dates2.not(this).datepicker(\'option\',option2,date2);}});var dates3=$(\'#shipstarttime,#shipendtime\').datepicker({onSelect:function(selectedDate){var option3=this.id==\'shipstarttime\'?\'minDate\':\'maxDate\',instance3=$(this).data(\'datepicker\'),date3=$.datepicker.parseDate(instance3.settings.dateFormat || $.datepicker._defaults.dateFormat,selectedDate,instance3.settings);dates3.not(this).datepicker(\'option\',option3,date3);}});var dates4=$(\'#startDate,#endDate\').datepicker({onSelect:function(selectedDate){var option4=this.id==\'startDate\'?\'minDate\':\'maxDate\',instance4=$(this).data(\'datepicker\'),date4=$.datepicker.parseDate(instance4.settings.dateFormat || $.datepicker._defaults.dateFormat,selectedDate,instance4.settings);dates4.not(this).datepicker(\'option\',option4,date4);}});';
		$this->kclass->page['title'].='-报价单统计';
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.'-报价单统计','right'=>'<a href="/s.php?module=customer">返回列表</a>','body'=>$body));
	}
	//
	function inquirystatistics(){
        $body=<<<EOF
		<form action="/p.php?module=printer&action=printIstatistics" method="post" target="_blank">
		<table width="800px">
		<thead><tr ><th colspan="2">询价单统计</th></tr></thead>
		<tbody>
		<tr>
		 <td>或按照客户名：</td><td colspan="3"><input type="text" style="width:250px" name="customer" id="autoComplete" value="{$customer['title']}"> <span class="small gray">可使用客户名称中的关键字查找</span></td>
		 </tr>
		 <tr>
		 <td>或按照类型：</td><td colspan="3">{$this->kclass->chooserQuotingtype(array('name'=>'typeid','hasBlank'=>1,'width'=>250,'selectedid'=>0))}</td>
		 </tr>
		 <tr>
		 <td>或按照国家或地区：</td><td colspan="3">{$this->kclass->chooserRegion(array('name'=>'regionid', 'hasBlank'=>1, 'selectedid'=>1,'width'=>250))}</td>
		 </tr>
		 <tr>
		 <td>或按照运输方式：</td><td colspan="3">{$this->kclass->chooserShipmethod(array('name'=>'shipmethodid', 'hasBlank'=>1, 'width'=>250, 'selectedid'=>$inquiry['shipmethodid']))}</td>
		 </tr>
		 <tr>
		 <td>或按照支付条款：</td><td colspan="3">{$this->kclass->chooserPaymentterm(array('name'=>'paymenttermid', 'hasBlank'=>1, 'width'=>250, 'selectedid'=>$inquiry['paymenttermid']))}</td>
		 </tr>
		 <tr>
		 <td>或按照来源时间：</td><td colspan="3">开始时间：<input type="text" id="sourcestarttime" name="sourcestarttime" >　　结束时间：<input type="text"  id="sourceendtime" name="sourceendtime" ></td>
		 </tr>
		 <tr>
		 <td>或按照发货时间：</td><td colspan="3">开始时间：<input type="text"  id="shipstarttime" name="shipstarttime" >　　结束时间：<input type="text"  id="shipendtime" name="shipendtime" ></td>
		 </tr>
		 <tr>
		 <td>或按照客户来源：</td><td colspan="3">{$this->kclass->chooserCustomerSources(array('name'=>'customersourceid', 'hasBlank'=>1, 'width'=>250))}</td>
		 </tr>
		 <tr>
		 <tr>
		 <th colspan="4">
		选项
		</th>
		</tr>
		<tr class="even">
	<td>排序方式：</td>
	<td colspan="3">
	<label for="o1"><input type="radio" name="orderby" value="customer" id="o1">客户姓名</label>
	<label for="o2"><input type="radio" name="orderby" value="orderid" id="o2" checked >询价单单号</label> 
	<label for="o3"><input type="radio" name="orderby" value="dateline" id="o3">询价时间</label>
	</td>
</tr>
<tr class="odd">
	<td>排列方向：</td>
	<td colspan="3">
		<label for="d0"><input type="radio" id="d0" name="direction" value="ASC" checked>升序(A-Z)</label> 
		<label for="d1"><input type="radio" id="d1" name="direction" value="DESC">降序(Z-A)</label> 
	</td>
</tr>
<tr class="even">
	<td>显示选项：</td>
	<td colspan="3">
		<label for="s0"><input type="radio" id="s0" name="show" value="default" checked>默认列表</label> 
		<label for="s1"><input type="radio" id="s1" name="show" value="all">所有<span class="small gray">(包括删除)</span></label> 
	</td>
</tr>
		 <tr><td  colspan="4" align="center"><input type="submit" name="port" value="提交"><input type="reset" value="重置"></td></tr>
		</tbody>
		</table>
		</form>
		<script>
		$(function(){
	$("#autoComplete").inputAutoComplete({location:"/s.php?module=ajax&action=findCustomer",datatype:"json",selectcount:"scrollbar",scrollbarcount:9});
});
</script>
EOF;
		$this->kclass->page['onload']='dc.tabhover();$(\'#date\').datepicker();var dates=$(\'#sourcestarttime,#sourceendtime\').datepicker({onSelect:function(selectedDate){var option=this.id==\'sourcestarttime\'?\'minDate\':\'maxDate\',instance=$(this).data(\'datepicker\'),date=$.datepicker.parseDate(instance.settings.dateFormat || $.datepicker._defaults.dateFormat,selectedDate,instance.settings);dates.not(this).datepicker(\'option\',option,date);}});var dates2=$(\'#shipstarttime,#shipendtime\').datepicker({onSelect:function(selectedDate){var option2=this.id==\'shipstarttime\'?\'minDate\':\'maxDate\',instance2=$(this).data(\'datepicker\'),date2=$.datepicker.parseDate(instance2.settings.dateFormat || $.datepicker._defaults.dateFormat,selectedDate,instance2.settings);dates2.not(this).datepicker(\'option\',option2,date2);}});';
		$this->kclass->page['title'].='-询价单统计';
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.'-询价单统计','right'=>'<a href="/s.php?module=customer">返回列表</a>','body'=>$body));
	}
	// 
	function printList(){
		
	}
	// 
	function printFilter(){
$body = <<<EOF
<form action="/s.php?module=customer&action=printList" name="customer" method="post">
<input type="hidden" name="module" value="customer">
<input type="hidden" name="action" value="printList">
<table class="hundred">
<thead>
<tr>
	<th colspan="4">客户列表过滤器</th>
</tr>
</thead>
<tbody>
<tr class="even">
	<td>联 系 人：<span class="red bold">*</span></td>
	<td><input type="text" style="width:244px" name="contact" value="{$customer['linkman']}"> <span class="small gray">指客户方询价人员</span></td>
</tr>
<tr class="odd">
	<td>支付条款：</td>
	<td>{$paymentterm}</td>
</tr>
<tr class="even">
	<td>交货条款：</td>
	<td>{$deliveryterm}</td>
</tr>
<tr class="odd">
	<td valign="top">规格说明：</td>
	<td><textarea name="standard" style="width:450px;height:60px">{$inquiry['standard']}</textarea></td>
</tr>
<tr class="even">
	<td valign="top">工艺说明：</td>
	<td><textarea name="technics" style="width:450px;height:60px">{$inquiry['technics']}</textarea></td>
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
		$this->kclass->page['title'] .= ' - '.$customer['title'].' - 新建客户询价单';
		$this->kclass->page['onload'] .= 'dc.tabhover()';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - <a href="/s.php?module=customer&action=view&customerid='.$customer['customerid'].'">'.$customer['title'].'</a> - 新建客户询价单', 'right' => '<a href="/s.php?module=customer">返回列表</a>', 'body'=>$body));
	}
	//
	function insertInquiry(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			if($this->kclass->input['inquirytype']==1 or $this->kclass->input['inquirytype']==2){
				if($this->kclass->input['standard']==""){
					$e .= '<li>请填写客户询价单的询价内容。</li>';
				}
			}else{
				if(strlen($this->kclass->input['contact']) < 2){
					$e .= '<li>请填写询价客户的相关联系人（不能少于2个字符）。</li>';
				}
				if($this->kclass->input['currencyid'] <= 0){
					$e .= '<li>请选择要客户询价的币种。</li>';
				}
				$j = count($this->kclass->input['itemProductid']);
				$hasItem=$hasPartItem=0;
				echo $this->kclass->input['itemProductid'][1]."--".$this->kclass->input['itemQuantity'][1]."--".$this->kclass->input['itemUnitid'][1];
				for($i = 1; $i < $j+1; $i++){
					if($this->kclass->input['itemProductid'][$i]>0 AND $this->kclass->input['itemQuantity'][$i]!='' AND $this->kclass->input['itemUnitid'][$i]>0){
						$hasItem=1;
					}
				}
				$m=count($this->kclass->input['itemPartsid']);
				for($n=1;$n<$m+1;$n++){
					if($this->kclass->input['itemPartsid'][$n]>0 AND $this->kclass->input['itemPartsQuantity'][$n]!='' AND $this->kclass->input['itemPartsUnitid'][$n]>0){
						$hasPartItem=1;
					}
				}
				echo "--".$hasItem; echo $hasPartItem;
				if($hasItem==0 AND $hasPartItem==0){
					$e .= '<li>需要填写至少有一条订单明细，才能建立订单。</li>';
				}
			}
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '新建客户询价单',
				'text' => '您在新建客户询价单的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$dateline=0;
		if($this->kclass->input['dateline']!=''){
			$dd=explode('-',$this->kclass->input['dateline']);
			$dateline=mktime(0,0,0,$dd[1],$dd[2],$dd[0]);
		}
		$this->kclass->DB->query("
			INSERT INTO `inquiry` (`customerid`, `contact`, `inquirytype`,`currencyid`, `portid`, `deliverytermid`, `paymenttermid`, `shipmethodid`, `dateline`, `standard`, `material`, `technics`, `remark`, `modified`, `creator`, `created`) 
			VALUES ('".$this->kclass->input['customerid']."', '".$this->kclass->input['contact']."','".$this->kclass->input['inquirytype']."', '".$this->kclass->input['currencyid']."', '".$this->kclass->input['portid']."', '".$this->kclass->input['deliverytermid']."', '".$this->kclass->input['paymenttermid']."', '".$this->kclass->input['shipmethodid']."', '{$dateline}' ,'".$this->kclass->input['standard']."', '".$this->kclass->input['material']."', '".$this->kclass->input['technics']."', '".$this->kclass->input['remark']."', '".TIMENOW."', '".$this->kclass->user['userid']."', '".TIMENOW."')
		");
		//产品明细
		$inquiryid = $this->kclass->DB->insertID();
		for($k=1;$k<=$j;$k++){
			$this->kclass->input['itemQuantity'][$k]=intVal($this->kclass->input['itemQuantity'][$k]);
			$this->kclass->input['itemDiscount'][$k]=intVal($this->kclass->input['itemDiscount'][$k]);
			if($this->kclass->input['itemDiscount'][$k]<0 OR $this->kclass->input['itemDiscount'][$k]>100){
				$this->kclass->input['itemDiscount'][$k]=100;
			}

			if($this->kclass->input['itemProductid'][$k]>0 AND $this->kclass->input['itemQuantity'][$k]!='' AND $this->kclass->input['itemUnitid'][$k]>0){
				$productItem=$this->kclass->DB->queryFirst("
					SELECT pi.productid,pi.attributevalue,
						p.title
					FROM productitem AS pi
					LEFT JOIN product AS p ON (p.productid=pi.productid)
					WHERE pi.productitemid='".$this->kclass->input['itemProductid'][$k]."'
				");
				$this->kclass->DB->query("
					INSERT INTO `item` (`module`,`mid`,`productid`,`productitemid`,`productAttributes`,`caption`,`brandid`,`packingid`,`quantity`,`discount`,`unitid`,`creator`,`created`)
					VALUES ('inquiry','".$inquiryid."','".$productItem['productid']."','".$this->kclass->input['itemProductid'][$k]."','".$productItem['attributevalue']."','".$this->kclass->input['itemCaption'][$k]."','".$this->kclass->input['itemBrandid'][$k]."','".$this->kclass->input['itemPackingid'][$k]."','".$this->kclass->input['itemQuantity'][$k]."','".$this->kclass->input['itemDiscount'][$k]."','".$this->kclass->input['itemUnitid'][$k]."','".$this->kclass->user['userid']."','".TIMENOW."')
				");
			}
		}
		
		//配件明细
		for($k = 1; $k < (count($this->kclass->input['itemPartsid'])+1); $k++){
			$this->kclass->input['itemPartsQuantity'][$k]=intVal($this->kclass->input['itemPartsQuantity'][$k]);
			$this->kclass->input['itemPartsDiscount'][$k]=intVal($this->kclass->input['itemPartsDiscount'][$k]);
			if($this->kclass->input['itemPartsDiscount'][$k]<0 OR $this->kclass->input['itemPartsDiscount'][$k]>100){
				$this->kclass->input['itemPartsDiscount'][$k]=100;
			}

			if($this->kclass->input['itemPartsid'][$k]>0 AND $this->kclass->input['itemPartsQuantity'][$k]!='' AND $this->kclass->input['itemPartsUnitid'][$k]>0){
				$part=$this->kclass->DB->queryFirst("
					SELECT p.materialid,
						m.materialno
					FROM product p
					LEFT JOIN material AS m ON (m.materialid=p.materialid)
					WHERE productid='".$this->kclass->input['itemPartsid'][$k]."'
				");
				$this->kclass->DB->query("
					INSERT INTO `item` (`module`,`mid`,`materialid`,`productid`,`caption`,`brandid`,`packingid`,`quantity`,`discount`,`unitid`,`creator`,`created`)
					VALUES ('inquiry','".$inquiryid."','".$part['materialid']."','".$this->kclass->input['itemPartsid'][$k]."','".$this->kclass->input['itemPartsCaption'][$k]."','".$this->kclass->input['itemPartsBrandid'][$k]."','".$this->kclass->input['itemPartsPackingid'][$k]."','".$this->kclass->input['itemPartsQuantity'][$k]."','".$this->kclass->input['itemPartsDiscount'][$k]."','".$this->kclass->input['itemPartsUnitid'][$k]."','".$this->kclass->user['userid']."','".TIMENOW."')
				");
			}
		}

		$this->kclass->messager(array(
			'title' => '新建客户询价单',
			'text' => '客户<b>'.$this->kclass->input['title'].'</b> 的询价单已新建成功!',
			'url' => '/s.php?module=customer&action=view&customerid='.$this->kclass->input['customerid'],
			'sec' => 2
		));
	}

	//
	function addInquiry(){
		if($this->kclass->input['customerid']<=0){
			$this->kclass->boinkIt('/s.php?module=customer');
		}
		$customer=$this->kclass->DB->queryFirst("SELECT customerid, title, linkman FROM `customer` WHERE killed=0 AND customerid='".$this->kclass->input['customerid']."'");
		$currency = $this->kclass->chooserCurrency(array('name'=>'currencyid', 'hasBlank'=>1, 'width'=>100, 'selectedid'=>1));
		$shipmethod = $this->kclass->chooserShipmethod(array('name'=>'shipmethodid', 'hasBlank'=>1, 'width'=>450, 'selectedid'=>$this->kclass->input['shipmethodid']));
		$paymentterm = $this->kclass->chooserPaymentterm(array('name'=>'paymenttermid', 'hasBlank'=>1, 'width'=>450, 'selectedid'=>$this->kclass->input['paymenttermid']));
		$deliveryterm = $this->kclass->chooserDeliveryterm(array('name'=>'deliverytermid', 'hasBlank'=>1, 'width'=>450, 'selectedid'=>$this->kclass->input['deliverytermid']));
		$port = $this->kclass->chooserPort(array('name'=>'portid', 'hasBlank'=>1, 'width'=>450, 'selectedid'=>$this->kclass->input['portid']));
		for($i = 1; $i < 5; $i++){
			$itemtr .= '<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').'" id="producttr'.$i.'">
			<td>'.$i.'</td>
			<td><span id="tditemProductid'.$i.'"></span>'.$this->kclass->chooserProductItem(array('name'=>'itemProductid['.$i.']','selectName'=>'itemProductid'.$i, 'hasBlank'=>1, 'width'=>500)).'</td>
			<td><input type="text" name="itemCaption['.$i.']" value="" size="65"></td>
			<td>'.$this->kclass->chooserBrand(array('name'=>'itemBrandid['.$i.']', 'hasBlank'=>1, 'width'=>100, 'selectedid'=>1)).'</td>
			<td>'.$this->kclass->chooserPacking(array('name'=>'itemPackingid['.$i.']', 'hasBlank'=>1, 'width'=>100, 'selectedid'=>6)).'</td>
			<td><input type="text" name="itemDiscount['.$i.']" value="100" size="3"></td>
			<td><input type="text" name="itemQuantity['.$i.']" value="'.$item['quantity'].'" size="3" onblur="dc.returnProductStr('.$i.',this.value)"></td>
			<td>'.$this->kclass->chooserUnit(array('name'=>'itemUnitid['.$i.']', 'hasBlank'=>1, 'width'=>80, 'selectedid'=>5)).'</td>
			</tr>';
		}
		$itemtr.='<tr class="odd bold gray normal" id="orderProduct" nohover><td colspan=10><span class="hand" onclick="dc.addLine(\'orderProduct\',\'orderProduct\',\'inquiry\')" style="border:2px solid gray;background:#FFE8EB">增加一行产品明细</span></td></tr>';
		for($i = 1; $i <5; $i++){
			$partsStr .= '<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').'" id="parttr'.$i.'">
			<td>'.$i.'</td>
			<td>'.$this->kclass->chooserParts(array('name'=>'itemPartsid['.$i.']', 'hasBlank'=>1, 'width'=>500)).'</td>
			<td><input type="text" name="itemPartsCaption['.$i.']" value="'.$item['caption'].'" size="65"></td>
			<td>'.$this->kclass->chooserBrand(array('name'=>'itemPartsBrandid['.$i.']', 'hasBlank'=>1, 'width'=>100, 'selectedid'=>1)).'</td>
			<td>'.$this->kclass->chooserPacking(array('name'=>'itemPartsPackingid['.$i.']', 'hasBlank'=>1, 'width'=>100, 'selectedid'=>6)).'</td>
			<td><input type="text" name="itemPartsDiscount['.$i.']" value="100" size="3"></td>
			<td><input type="text" name="itemPartsQuantity['.$i.']" value="'.$item['quantity'].'" size="3" ></td>
			<td>'.$this->kclass->chooserUnit(array('name'=>'itemPartsUnitid['.$i.']', 'hasBlank'=>1, 'width'=>80, 'selectedid'=>5)).'</td>
			</tr>';
		}
		$partsStr.='<tr class="odd bold gray normal" id="orderPart" nohover><td colspan=10><span class="hand" onclick="dc.addLine(\'orderPart\',\'orderPart\',\'inquiry\')" style="border:2px solid gray;background:#FFE8EB">增加一行配件明细</span></td></tr>';
$body = <<<EOF
<form action="/s.php?module=customer&action=insertInquiry" name="customer" method="post">
<input type="hidden" name="module" value="customer">
<input type="hidden" name="action" value="insertInquiry">
<input type="hidden" name="customerid" value="{$this->kclass->input['customerid']}">
<table class="hundred">
<thead>
<tr>
	<th colspan="4">新建来自客户 <span class="big bold">{$customer['title']}</span> 的 <span class="middle bold">询价单</span></th>
</tr>
</thead>
<tbody>
<tr class="even">
	<td>联 系 人：<span class="red bold">*</span></td>
	<td><input type="text" style="width:244px" name="contact" value="{$customer['linkman']}"> <span class="small gray">指客户方询价人员</span></td>
	<td>币　　种：<span class="red bold">*</span></td>
	<td>{$currency}　交货日期：<input type="text" name="dateline" id="dateline" size="10" ></td>
</tr>
<tr>
	<td>询价单类型： <span class="red bold">*</span></td>
	<td colspan='3'><input type="radio" name="inquirytype" value="1" />无型号无数量  
<input type="radio" name="inquirytype" value="2" />无型号有数量  <input type="radio" 
name="inquirytype" value="3" />有型号无数量  <input type="radio" name="inquirytype" 
value="4" />有型号有数量</td>
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
<tr class="odd">
<td valign="top">询价内容：<span class="red bold">*</span></td>
<td><textarea name="standard" style="width:450px;height:60px">{$inquiry['standard']}</textarea><span class='small gray'><br/>说明：询价单类型为无型号无数量和无型号有数量的时候请填写询价单内容</span></td>
<td valign="top">主要材料：</td>
<td><textarea name="material" style="width:450px;height:60px">{$inquiry['material']}</textarea></td>
</tr>
<tr class="even">
<td valign="top">工艺说明：</td>
<td><textarea name="technics" style="width:450px;height:60px">{$inquiry['technics']}</textarea></td>
<td valign="top">备　　注：</td>
<td><textarea name="remark" style="width:450px;height:60px">{$inquiry['remark']}</textarea></td>
</tr>
</tbody>
</table>
<table class="hundred">
<thead>
<tr>
	<th colspan="10">产品明细表 <span class="right">{$options} <span class="plus" onclick="dc.showItem('inquiry{$inquiry['inquiryid']}')"></span></span></th>
</tr>
</thead>
<tbody id="inquiry{$inquiry['inquiryid']}" style="display:none">
<tr><td width="15">ID</td><td width="390">产品选择<span class="red bold">*</span></td><td  width="300">备注说明</td><td  width="100">品牌</td><td  width="50">包装</td><td  width="45">折扣%</td><td width="30">数量<span class="red bold">*</span></td><td width="80">单位<span class="red bold">*</span></td></tr>
{$itemtr}
<tr ><td width="15">ID</td><td width="390">配件选择<span class="red bold">*</span></td><td  width="300">备注说明</td><td  width="100">品牌</td><td  width="50">包装</td><td  width="45">折扣%</td><td width="30">数量<span class="red bold">*</span></td><td width="80">单位<span class="red bold">*</span></td></tr>
{$partsStr}
</tr>
<tr class="even">
	<td colspan="10" class="gray small">注：①如行数不够，请保存后再进入修改页面。②至少有“产品”、“数量”、“单位”三个项目选择填写，该明细才会被保存。③“折扣”请填写百分比“%”。</td>
</tr>
</tbody>
<tr class="odd">
<td colspan="10" align="center">
	<input type="submit" id="submitButton" value="  保存  " accesskey="s">　　
	<input type="reset" value="  复位  ">
</td>
</tr>
</table>
</form>
EOF;
		$this->kclass->page['title'] .= ' - '.$customer['title'].' - 新建客户询价单';
		$this->kclass->page['onload'] .= 'dc.tabhover();$(\'#dateline\').datepicker()';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - <a href="/s.php?module=customer&action=view&customerid='.$customer['customerid'].'">'.$customer['title'].'</a> - 新建客户询价单', 'right' => '<a href="/s.php?module=customer">返回列表</a>', 'body'=>$body));
	}

	//
	function doupdateInquiry(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			if($this->kclass->input['inquirytype']==1 or $this->kclass->input['inquirytype']==2){
				if($this->kclass->input['standard']==""){
					$e .= '<li>请在备注中填写客户询价单中的内容。</li>';
				}	
			}else{
				if(strlen($this->kclass->input['contact']) < 2){
					$e .= '<li>请填写询价客户的相关联系人（不能少于2个字符）。</li>';
				}
				if($this->kclass->input['currencyid'] <= 0){
					$e .= '<li>请选择要客户询价的币种。</li>';
				}
			$j = count($this->kclass->input['itemProductid']);
			$hasItem = 0;
			for($i = 1; $i < $j+1; $i++){
				if($this->kclass->input['itemKill'][$i]=='' AND $this->kclass->input['itemProductid'][$i]>0 AND $this->kclass->input['itemQuantity'][$i]!='' AND $this->kclass->input['itemUnitid'][$i]>0 AND $this->kclass->input['itemPrice'][$i]>=0){
					$hasItem=1;
				}
			}
			$m=count($this->kclass->input['itemPartsId']);
			for($n=1;$n<$m+1;$n++){
				if($this->kclass->input['itemPartsKill'][$i]=='' AND $this->kclass->input['itemPartsid'][$n]>0 AND $this->kclass->input['itemPartsQuantity'][$n]!='' AND $this->kclass->input['itemPartsUnitid'][$n]>0 AND $this->kclass->input['itemPartsPrice'][$n]>=0){
					$hasPartItem=1;
				}
			}
			if($hasItem==0 AND $hasPartItem==0){
				$e .= '<li>需要填写至少有一条订单明细，才能建立订单。</li>';
			}
		}
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '修改客户询价单',
				'text' => '您在修改客户询价单的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}

		// 产品明细
		for($i=1;$i<=$j;$i++){
			$this->kclass->input['itemQuantity'][$i]=intVal($this->kclass->input['itemQuantity'][$i]);
			$this->kclass->input['itemDiscount'][$i]=intVal($this->kclass->input['itemDiscount'][$i]);
			if($this->kclass->input['itemDiscount'][$i]<0 OR $this->kclass->input['itemDiscount'][$i]>100){
				$this->kclass->input['itemDiscount'][$i]=100;
			}
			if($this->kclass->input['itemProductid'][$i]>0 AND $this->kclass->input['itemQuantity'][$i]!='' AND $this->kclass->input['itemUnitid'][$i]>0 AND $this->kclass->input['itemPrice'][$i]>=0){
				$productItem=$this->kclass->DB->queryFirst("
					SELECT pi.productid,pi.attributevalue,
						p.title
					FROM productitem AS pi
					LEFT JOIN product AS p ON (p.productid=pi.productid)
					WHERE productitemid='".$this->kclass->input['itemProductid'][$i]."'
				");

				if($this->kclass->input['itemId'][$i]>0){// 删除或者修改
					if($this->kclass->input['itemKill'][$i]>0){
						$this->kclass->DB->query("UPDATE `item` SET killer=".$this->kclass->user['userid'].",killed=".TIMENOW."  WHERE itemid='".$this->kclass->input['itemId'][$i]."'");
					}else{
						$this->kclass->DB->query("
							UPDATE `item` SET
								`productid`='".$productItem['productid']."',
								`productitemid`='".$this->kclass->input['itemProductid'][$i]."',
								`productAttributes`='".$productItem['attributevalue']."',
								`caption`='".$this->kclass->input['itemCaption'][$i]."',
								`brandid`='".$this->kclass->input['itemBrandid'][$i]."',
								`packingid`='".$this->kclass->input['itemPackingid'][$i]."',
								`discount`='".$this->kclass->input['itemDiscount'][$i]."',
								`quantity`='".$this->kclass->input['itemQuantity'][$i]."',
								`unitid`='".$this->kclass->input['itemUnitid'][$i]."',
								`modified`='".TIMENOW."',
								`modifier`='".$this->kclass->user['userid']."'
							WHERE itemid='".$this->kclass->input['itemId'][$i]."'
						");
					}
				}else{// 新增
					$this->kclass->DB->query("
						INSERT INTO `item` (`module`,`mid`,`productid`,`productitemid`,`productAttributes`,`caption`,`brandid`,`packingid`,`quantity`,`discount`,`unitid`,`creator`,`created`)
						VALUES ('inquiry','".$this->kclass->input['inquiryid']."','".$productItem['productid']."','".$this->kclass->input['itemProductid'][$i]."','".$productItem['attributevalue']."','".$this->kclass->input['itemCaption'][$i]."','".$this->kclass->input['itemBrandid'][$i]."','".$this->kclass->input['itemPackingid'][$i]."','".$this->kclass->input['itemQuantity'][$i]."','".$this->kclass->input['itemDiscount'][$i]."','".$this->kclass->input['itemUnitid'][$i]."','".$this->kclass->user['userid']."','".TIMENOW."')
					");
				}
			}
		}
		//配件明细
		for($i = 1; $i<=$m; $i++){
			$this->kclass->input['itemPartsQuantity'][$i]=intVal($this->kclass->input['itemPartsQuantity'][$i]);
			$this->kclass->input['itemPartsDiscount'][$i]=intVal($this->kclass->input['itemPartsDiscount'][$i]);
			if($this->kclass->input['itemPartsDiscount'][$i]<0 OR $this->kclass->input['itemPartsDiscount'][$i]>100){
				$this->kclass->input['itemPartsDiscount'][$i]=100;
			}
			if($this->kclass->input['itemPartsid'][$i]>0 AND $this->kclass->input['itemPartsQuantity'][$i]!='' AND $this->kclass->input['itemPartsUnitid'][$i]>0 AND $this->kclass->input['itemPartsPrice'][$i]>=0){
				$part=$this->kclass->DB->queryFirst("SELECT materialid FROM product WHERE productid='".$this->kclass->input['itemPartsid'][$i]."'");
				if($this->kclass->input['itemPartsId'][$i]>0){
					if($this->kclass->input['itemPartsKill'][$i]>0){
						$this->kclass->DB->query("UPDATE `item` SET killer=".$this->kclass->user['userid'].",killed=".TIMENOW."  WHERE itemid='".$this->kclass->input['itemPartsId'][$i]."'");
					}else{
						$this->kclass->DB->query("
							UPDATE `item` SET
								`materialid`='".$part['materialid']."',
								`productid`='".$this->kclass->input['itemPartsid'][$i]."',
								`caption`='".$this->kclass->input['itemPartsCaption'][$i]."',
								`brandid`='".$this->kclass->input['itemPartsBrandid'][$i]."',
								`packingid`='".$this->kclass->input['itemPartsPackingid'][$i]."',
								`discount`='".$this->kclass->input['itemPartsDiscount'][$i]."',
								`quantity`='".$this->kclass->input['itemPartsQuantity'][$i]."',
								`unitid`='".$this->kclass->input['itemPartsUnitid'][$i]."',
								`modified`='".TIMENOW."',
								`modifier`='".$this->kclass->user['userid']."'
							WHERE itemid='".$this->kclass->input['itemPartsId'][$i]."'
						");
					}
				}else{
					$this->kclass->DB->query("
						INSERT INTO `item` (`module`,`mid`,`materialid`,`productid`,`caption`,`brandid`,`packingid`,`quantity`,`discount`,`unitid`,`creator`,`created`)
						VALUES ('inquiry','".$this->kclass->input['inquiryid']."','".$part['materialid']."','".$this->kclass->input['itemPartsid'][$i]."','".$this->kclass->input['itemPartsCaption'][$i]."','".$this->kclass->input['itemPartsBrandid'][$i]."','".$this->kclass->input['itemPartsPackingid'][$i]."','".$this->kclass->input['itemPartsQuantity'][$i]."','".$this->kclass->input['itemPartsDiscount'][$i]."','".$this->kclass->input['itemPartsUnitid'][$i]."','".$this->kclass->user['userid']."','".TIMENOW."')
					");
				}
			}
		}
		$dateline=0;
		if($this->kclass->input['dateline']!=''){
			$dd=explode('-',$this->kclass->input['dateline']);
			$dateline=mktime(0,0,0,$dd[1],$dd[2],$dd[0]);
		}
		$this->kclass->DB->query("
			UPDATE `inquiry` 
			SET `contact` = '".$this->kclass->input['contact']."', 
				`inquirytype` = '".$this->kclass->input['inquirytype']."',
				`currencyid` = '".$this->kclass->input['currencyid']."', 
				`portid` = '".$this->kclass->input['portid']."', 
				`deliverytermid` = '".$this->kclass->input['deliverytermid']."', 
				`paymenttermid` = '".$this->kclass->input['paymenttermid']."', 
				`shipmethodid` = '".$this->kclass->input['shipmethodid']."', 
				`dateline` = '".$dateline."', 
				`standard` = '".$this->kclass->input['standard']."', 
				`technics` = '".$this->kclass->input['technics']."', 
				`material` = '".$this->kclass->input['material']."', 
				`remark` = '".$this->kclass->input['remark']."', 
				`modified` = '".TIMENOW."', 
				`modifier` = '".$this->kclass->user['userid']."'
			WHERE inquiryid='".$this->kclass->input['inquiryid']."'
		");
		$this->kclass->messager(array(
			'title' => '修改客户询价单',
			'text' => '客户<b>'.$this->kclass->input['title'].'</b> 的询价单已修改成功!',
			'url' => '/s.php?module=customer&action=view&customerid='.$this->kclass->input['customerid'],
			'sec' => 2
		));
	}

	//
	function updateInquiry(){
		if($this->kclass->input['inquiryid']<=0 OR !$inquiry = $this->kclass->DB->queryFirst("SELECT `inquiry`.*, customer.title AS customer FROM `inquiry` LEFT JOIN customer ON (customer.customerid=inquiry.customerid) WHERE `inquiryid`='".$this->kclass->input['inquiryid']."'")){
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
		$inquirytype = '<input type="radio" name="inquirytype" value="1" '.$this->kclass->iif($inquiry['inquirytype']==1, 'checked', '').' />无型号无数量  <input type="radio" name="inquirytype" value="2" '.$this->kclass->iif($inquiry['inquirytype']==2, 'checked', '').'/>无型号有数量  <input type="radio" name="inquirytype" value="3" '.$this->kclass->iif($inquiry['inquirytype']==3, 'checked', '').'/>有型号无数量  <input type="radio" name="inquirytype" value="4"  '.$this->kclass->iif($inquiry['inquirytype']==4, 'checked', '').'/>有型号有数量';
		$items=$this->kclass->DB->query("SELECT * FROM `item` WHERE module='inquiry' AND mid='".$inquiry['inquiryid']."' ORDER BY modified ASC");
		if($this->kclass->DB->numRows()){
			$k=1;
			$n=1;
			while($item=$this->kclass->DB->fetchArray($items)){
				if($item['productitemid']>0){
					$product = $this->kclass->DB->queryFirst("
						SELECT `product`.title,`product`.entitle
						FROM  `productitem`
						LEFT JOIN `product` ON (`productitem`.productid = `product`.productid)
						WHERE productitemid='".$item['productitemid']."'
					");
					$seletStr = '<input type="hidden" id="tditemProductid'.$k.'input" value="'.$product['entitle'].'" />';
					$itemtr .= '<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').'">
					<td><span id="tditemProductid'.$k.'">'.$seletStr.'</span><input type="hidden" name="itemId['.$k.']" value="'.$item['itemid'].'">'.$this->kclass->chooserProductItem(array('name'=>'itemProductid['.$k.']','selectName'=>'itemProductid'.$k, 'selectedid'=>$item['productitemid'], 'hasBlank'=>1, 'width'=>500)).'</td>
					<td><input type="text" name="itemCaption['.$k.']" value="'.$item['caption'].'" size="65"></td>
					<td>'.$this->kclass->chooserBrand(array('name'=>'itemBrandid['.$k.']', 'hasBlank'=>1, 'width'=>80, 'selectedid'=>$item['brandid'])).'</td>
					<td>'.$this->kclass->chooserPacking(array('name'=>'itemPackingid['.$k.']', 'hasBlank'=>1, 'width'=>100, 'selectedid'=>$item['packingid'])).'</td>
					<td><input type="text" name="itemDiscount['.$k.']" value="'.$item['discount'].'" size="3"></td>
					<td><input type="text" name="itemQuantity['.$k.']" value="'.$item['quantity'].'" size="3" onblur="dc.returnProductStr('.$k.',this.value)"></td>
					<td>'.$this->kclass->chooserUnit(array('name'=>'itemUnitid['.$k.']', 'hasBlank'=>1, 'width'=>80, 'selectedid'=>$item['unitid'])).'</td>
					<td><input type="checkbox" name="itemKill['.$k.']" value="'.$item['itemid'].'"></td>
					</tr>';
					$k++;
				}else{
					$parts .= '<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').'">
					<td><input type="hidden" name="itemPartsId['.$n.']" value="'.$item['itemid'].'">'.$this->kclass->chooserParts(array('name'=>'itemPartsid['.$n.']', 'selectedid'=>$item['productid'], 'hasBlank'=>1, 'width'=>500)).'</td>
					<td><input type="text" name="itemPartsCaption['.$n.']" value="'.$item['caption'].'" size="65"></td>
					<td>'.$this->kclass->chooserBrand(array('name'=>'itemPartsBrandid['.$n.']', 'hasBlank'=>1, 'width'=>80, 'selectedid'=>$item['brandid'])).'</td>
					<td>'.$this->kclass->chooserPacking(array('name'=>'itemPartsPackingid['.$n.']', 'hasBlank'=>1, 'width'=>100, 'selectedid'=>$item['packingid'])).'</td>
					<td><input type="text" name="itemPartsDiscount['.$n.']" value="'.$item['discount'].'" size="3"></td>
					<td><input type="text" name="itemPartsQuantity['.$n.']" value="'.$item['quantity'].'" size="3"></td>
					<td>'.$this->kclass->chooserUnit(array('name'=>'itemPartsUnitid['.$n.']', 'hasBlank'=>1, 'width'=>80, 'selectedid'=>$item['unitid'])).'</td>
					<td><input type="checkbox" name="itemPartsKill['.$n.']" value="'.$item['itemid'].'"></td>
					</tr>';
					$n++;
				}
			}
		}
		for($i = $k; $i < ($k+5); $i++){
			$itemtrs .= '<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').'">
			<td><span id="tditemProductid'.$i.'"></span>'.$this->kclass->chooserProductItem(array('name'=>'itemProductid['.$i.']','selectName'=>'itemProductid'.$i, 'hasBlank'=>1, 'width'=>500)).'</td>
			<td><input type="text" name="itemCaption['.$i.']" value="'.$item['caption'].'"  size="65"></td>
			<td>'.$this->kclass->chooserBrand(array('name'=>'itemBrandid['.$i.']', 'hasBlank'=>1, 'width'=>80,'selectedid'=>1)).'</td>
			<td>'.$this->kclass->chooserPacking(array('name'=>'itemPackingid['.$i.']', 'hasBlank'=>1, 'width'=>100,'selectedid'=>6)).'</td>
			<td><input type="text" name="itemDiscount['.$i.']" value="100" size="3"></td>
			<td><input type="text" name="itemQuantity['.$i.']" value="'.$item['quantity'].'" size="3"  onblur="dc.returnProductStr('.$i.',this.value)"></td>
			<td>'.$this->kclass->chooserUnit(array('name'=>'itemUnitid['.$i.']', 'hasBlank'=>1, 'width'=>80, 'selectedid'=>5)).'</td>
			<td></td>
			</tr>';
		}
		for($i = $n; $i < ($n+5); $i++){
			$partStr .= '<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').'">
			<td>'.$this->kclass->chooserParts(array('name'=>'itemPartsid['.$i.']', 'hasBlank'=>1, 'width'=>500)).'</td>
			<td><input type="text" name="itemPartsCaption['.$i.']" value="'.$item['caption'].'"  size="65"></td>
			<td>'.$this->kclass->chooserBrand(array('name'=>'itemPartsBrandid['.$i.']', 'hasBlank'=>1, 'width'=>80,'selectedid'=>1)).'</td>
			<td>'.$this->kclass->chooserPacking(array('name'=>'itemPartsPackingid['.$i.']', 'hasBlank'=>1, 'width'=>100,'selectedid'=>6)).'</td>
			<td><input type="text" name="itemPartsDiscount['.$i.']" value="100" size="3"></td>
			<td><input type="text" name="itemPartsQuantity['.$i.']" value="'.$item['quantity'].'" size="3"></td>
			<td>'.$this->kclass->chooserUnit(array('name'=>'itemPartsUnitid['.$i.']', 'hasBlank'=>1, 'width'=>80, 'selectedid'=>5)).'</td>
			<td></td>
			</tr>';
		}
		$dateline=date('Y-m-d',$inquiry['dateline']);
$body = <<<EOF
<form action="/s.php?module=customer&action=doupdateInquiry" name="customer" method="post">
<input type="hidden" name="module" value="customer">
<input type="hidden" name="action" value="doupdateInquiry">
<input type="hidden" name="customerid" value="{$inquiry['customerid']}">
<input type="hidden" name="inquiryid" value="{$inquiry['inquiryid']}">
<table class="hundred">
<thead>
<tr>
	<th colspan="4">修改来自客户 <span class="big bold">{$inquiry['customer']}</span> 的 <span class="middle bold">询价单</span></th>
</tr>
</thead>
<tbody>
<tr class="even">
	<td>联 系 人：<span class="red bold">*</span></td>
	<td><input type="text" style="width:244px" name="contact" value="{$inquiry['contact']}"> <span class="small gray">指客户方询价人员</span></td>
	<td>币　　种：<span class="red bold">*</span></td>
	<td>{$currency}　交货日期： <input type="text" name="dateline" id="dateline" size="10"  ></td>
</tr>
<tr class="even">
	<td>询价单类型： <span class="red bold">*</span></td>
	<td colspan='3'>{$inquirytype}</td>
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
<tr class="odd">
	<td valign="top">询价内容：<span class="red bold">*</span></td>
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
</tbody>
</table>
<table class="hundred">
<thead>
<tr>
	<th colspan="10">产品明细表</th>
</tr>
</thead>
<tbody class="small">
<tr><td>产品选择<span class="red bold">*</span></td><td width="300">说明</td><td width="100">品牌</td><td width="50">包装</td><td width="30">折扣</td><td width="30">数量<span class="red bold">*</span></td><td width="80">单位<span class="red bold">*</span></td><td  width="20">删</td></tr>
{$itemtr}
<tr><td>配件选择<span class="red bold">*</span></td><td width="300">说明</td><td width="100">品牌</td><td width="50">包装</td><td width="30">折扣</td><td width="30">数量<span class="red bold">*</span></td><td width="80">单位<span class="red bold">*</span></td><td  width="20">删</td></tr>
{$parts}
<tr>
	<th colspan="12">新建明细</th>
</tr>
<tr><td>产品选择<span class="red bold">*</span></td><td width="300">说明</td><td width="100">品牌</td><td width="50">包装</td><td width="30">折扣</td><td width="30">数量<span class="red bold">*</span></td><td width="80">单位<span class="red bold">*</span></td><td  width="20"></td></tr>
{$itemtrs}
<tr><td>配件选择<span class="red bold">*</span></td><td width="300">说明</td><td width="100">品牌</td><td width="50">包装</td><td width="30">折扣</td><td width="30">数量<span class="red bold">*</span></td><td width="80">单位<span class="red bold">*</span></td><td  width="20"></td></tr>
{$partStr}
<tr class="even">
	<td colspan="10" class="gray small">注：①如行数不够，请保存后再进入修改页面。②至少有“产品”、“数量”、“单位”三个项目选择填写，该明细才会被保存。③“折扣”请填写百分比“%”。</td>
</tr>
</tbody>
<table class="hundred">
<tbody>

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
		$this->kclass->page['title'] .= ' - '.$inquiry['customer'].' - 修改客户询价单';
		$this->kclass->page['onload'] .= 'dc.tabhover();$(\'#dateline\').datepicker()';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - <a href="/s.php?module=customer&action=view&customerid='.$customer['customerid'].'">'.$inquiry['customer'].'</a> - 修改客户询价单', 'right' => '<a href="/s.php?module=customer">返回列表</a>', 'body'=>$body));
	}
	//
	function killInquiry(){
		if($this->kclass->input['inquiryid']<=0){
			$this->kclass->boinkIt('/s.php?module=customer');
		}
		if($this->kclass->input['confirm'] == 0){
			$this->kclass->boinkIt('/s.php?module=customer&action=view&customerid='.$this->kclass->input['customerid']);
		}
		if($this->kclass->input['inquiryid'] < 0){
			$e = '<li>请从正确的页面进行操作，如果此问题持续存在，请联系网站管理员。</li>';
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '删除客户询价单',
				'text' => '您在删除客户询价单的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$inquiry = $this->kclass->DB->queryFirst("
			SELECT inquiry.inquiryid, inquiry.customerid, 
				customer.title AS customer
			FROM `inquiry` 
			LEFT JOIN customer ON (customer.customerid=inquiry.customerid) 
			WHERE inquiry.inquiryid='".$this->kclass->input['inquiryid']."'
		");
		if($inquiry){
			$this->kclass->DB->query("
				UPDATE `inquiry`
				SET killed=".TIMENOW." 
				WHERE inquiryid='".$inquiry['inquiryid']."'
			");
			$this->kclass->messager(array(
				'title' => '删除客户询价单成功',
				'text' => '客户 <b>'.$inquiryid['customer'].'</b> 下的 询价单 已成功被标记为删除!',
				'url' => '/s.php?module=customer&action=view&customerid='.$inquiry['customerid'],
				'sec' => 2
			));
		}else{
			$this->kclass->messager(array(
				'title' => '删除客户询价单失败',
				'text' => '您要删除的客户询价单，不存在！',
				'url' => '/s.php?module=customer&action=view&customerid='.$inquiry['customerid'],
				'sec' => 3
			));
		}
	}

	//
	function removeInquiry(){
		if($this->kclass->input['inquiryid']<=0){
			$this->kclass->boinkIt('/s.php?module=customer');
		}
		$inquiry = $this->kclass->DB->queryFirst("
			SELECT inquiry.inquiryid, inquiry.customerid, 
				customer.title AS customer
			FROM `inquiry` 
			LEFT JOIN customer ON (customer.customerid=inquiry.customerid) 
			WHERE inquiry.inquiryid='".$this->kclass->input['inquiryid']."'
		");
		if($inquiry){
$body = <<<EOF
<form action="/s.php?module=customer&action=killInquiry" name="customer" method="post">
<input type="hidden" name="module" value="customer">
<input type="hidden" name="action" value="killInquiry">
<input type="hidden" name="customerid" value="{$inquiry['customerid']}">
<input type="hidden" name="inquiryid" value="{$inquiry['inquiryid']}">
<table><thead>
<thead>
<tr>
	<th>删除客户 {$inquiry['customer']} 的 询价单</th>
</tr>
</thead>
<tbody>
<tr class="even">
	<td class="middle">你确定要删除客户 <a href="/s.php?module=customer&action=view&customerid={$inquiry['customerid']}" class="middle bold" target="_blank">{$inquiry['customer']}</a> 的 <span class="big bold">询价单</span> 吗?</td>
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
		$this->kclass->page['title'] .= ' - '.$inquiry['customer'].' - 删除客户询价单';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - <a href="/s.php?module=customer&action=view&customerid='.$inquiry['customerid'].'">'.$inquiry['customer'].'</a> - 删除客户询价单', 'right' => '<a href="/s.php?module=customer">返回列表</a>', 'body'=>$body));
	}

	//
	function revivalInquiry(){
		if($this->kclass->input['inquiryid']<=0){
			$this->kclass->boinkIt('/s.php?module=customer');
		}
		if($this->kclass->input['confirm'] == 0){
			$this->kclass->boinkIt('/s.php?module=customer&action=view&customerid='.$this->kclass->input['customerid']);
		}
		if($this->kclass->input['inquiryid'] < 0){
			$e = '<li>请从正确的页面进行操作，如果此问题持续存在，请联系网站管理员。</li>';
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '恢复客户询价单',
				'text' => '您在恢复客户询价单的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$inquiry = $this->kclass->DB->queryFirst("
			SELECT inquiry.inquiryid, inquiry.customerid, 
				customer.title AS customer
			FROM `inquiry` 
			LEFT JOIN customer ON (customer.customerid=inquiry.customerid) 
			WHERE inquiry.inquiryid='".$this->kclass->input['inquiryid']."'
		");
		if($inquiry){
			$this->kclass->DB->query("
				UPDATE `inquiry`
				SET killed=0
				WHERE inquiryid='".$inquiry['inquiryid']."'
			");
			$this->kclass->messager(array(
				'title' => '恢复客户询价单成功',
				'text' => '客户 <b>'.$inquiryid['customer'].'</b> 下的 询价单 已成功被标记为恢复!',
				'url' => '/s.php?module=customer&action=view&customerid='.$inquiry['customerid'],
				'sec' => 2
			));
		}else{
			$this->kclass->messager(array(
				'title' => '恢复客户询价单失败',
				'text' => '您要恢复的客户询价单，不存在！',
				'url' => '/s.php?module=customer&action=view&customerid='.$inquiry['customerid'],
				'sec' => 3
			));
		}
	}

	//
	function restoreInquiry(){
		if($this->kclass->input['inquiryid']<=0){
			$this->kclass->boinkIt('/s.php?module=customer');
		}
		$inquiry = $this->kclass->DB->queryFirst("
			SELECT inquiry.inquiryid, inquiry.customerid, 
				customer.title AS customer
			FROM `inquiry` 
			LEFT JOIN customer ON (customer.customerid=inquiry.customerid) 
			WHERE inquiry.inquiryid='".$this->kclass->input['inquiryid']."'
		");
		if($inquiry){
$body = <<<EOF
<form action="/s.php?module=customer&action=revivalInquiry" name="customer" method="post">
<input type="hidden" name="module" value="customer">
<input type="hidden" name="action" value="revivalInquiry">
<input type="hidden" name="customerid" value="{$inquiry['customerid']}">
<input type="hidden" name="inquiryid" value="{$inquiry['inquiryid']}">
<table><thead>
<thead>
<tr>
	<th>删除客户 {$inquiry['customer']} 的 询价单</th>
</tr>
</thead>
<tbody>
<tr class="even">
	<td class="middle">你确定要恢复产品 <a href="/s.php?module=customer&action=view&customerid={$inquiry['customerid']}" class="middle bold" target="_blank">{$inquiry['customer']}</a> 的 <span class="big bold">询价单</span> 吗?</td>
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
		$this->kclass->page['title'] .= ' - '.$inquiry['customer'].' - 恢复客户询价单';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - <a href="/s.php?module=customer&action=view&customerid='.$customer['customerid'].'">'.$inquiry['customer'].'</a> - 恢复客户询价单', 'right' => '<a href="/s.php?module=customer">返回列表</a>', 'body'=>$body));
	}
	//
	function insertQuoting(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			if($this->kclass->input['inquiryid']>0){
				$title = '回复客户的询价 & 新建报价单';
			}else{
				$title = '新建发往客户的报价单';
			}
			if(strlen($this->kclass->input['contact']) < 2){
				$e .= '<li>请填写询价客户的相关联系人（不能少于2个字符）。</li>';
			}
			if($this->kclass->input['currencyid'] <= 0){
				$e .= '<li>请选择要客户询价的币种。</li>';
			}
			$j = count($this->kclass->input['itemProductid']);
			$hasItem =$hasPartItem= 0;
			for($i = 1; $i < $j+1; $i++){
				if($this->kclass->input['itemKill'][$i]=='' AND $this->kclass->input['itemProductid'][$i]>0 AND $this->kclass->input['itemQuantity'][$i]!='' AND $this->kclass->input['itemUnitid'][$i]>0 AND $this->kclass->input['itemPrice'][$i]>=0){
					$hasItem=1;
				}
			}
			$m=count($this->kclass->input['itemPartsId']);
			for($n=1;$n<$m+1;$n++){
				if($this->kclass->input['itemPartsKill'][$n]=='' AND $this->kclass->input['itemPartsid'][$n]>0 AND $this->kclass->input['itemPartsQuantity'][$n]!='' AND $this->kclass->input['itemPartsUnitid'][$n]>0 AND $this->kclass->input['itemPartsPrice'][$n]>=0){
					$hasPartItem=1;
				}
			}
			if($hasItem==0 AND $hasPartItem==0){
				$e .= '<li>需要填写至少有一条明细，才能建立报价单。</li>';
			}
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => $title,
				'text' => '您在添加客户报价单的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$dateline=0;
		if($this->kclass->input['dateline']!=''){
			$dd=explode('-',$this->kclass->input['dateline']);
			$dateline=mktime(0,0,0,$dd[1],$dd[2],$dd[0]);
		}
		if($this->kclass->input['inquiryid']!=''){
		$this->kclass->DB->query("
			INSERT INTO `quoting` (`customerid`, `inquiryid`,`freight`,`contact`, `currencyid`, `portid`, `deliverytermid`, `paymenttermid`, `shipmethodid`, `dateline`,`standard`, `material`, `technics`, `remark`, `modified`, `creator`, `created`) 
			VALUES ('".$this->kclass->input['customerid']."', '".$this->kclass->input['inquiryid']."','".$this->kclass->input['freight']."' ,'".$this->kclass->input['contact']."', '".$this->kclass->input['currencyid']."', '".$this->kclass->input['portid']."', '".$this->kclass->input['deliverytermid']."', '".$this->kclass->input['paymenttermid']."', '".$this->kclass->input['shipmethodid']."','{$dateline}', '".$this->kclass->input['standard']."', '".$this->kclass->input['material']."', '".$this->kclass->input['technics']."', '".$this->kclass->input['remark']."', '".TIMENOW."', '".$this->kclass->user['userid']."', '".TIMENOW."')
		");
		
		
		}else{
			$this->kclass->DB->query("
			INSERT INTO `quoting` (`customerid`,`typeid`,`freight`, `contact`, `currencyid`, `portid`, `deliverytermid`, `paymenttermid`, `shipmethodid`, `dateline`,`standard`, `material`, `technics`, `remark`, `modified`, `creator`, `created`) 
			VALUES ('".$this->kclass->input['customerid']."','".$this->kclass->input['typeid']."','".$this->kclass->input['freight']."','".$this->kclass->input['contact']."', '".$this->kclass->input['currencyid']."', '".$this->kclass->input['portid']."', '".$this->kclass->input['deliverytermid']."', '".$this->kclass->input['paymenttermid']."', '".$this->kclass->input['shipmethodid']."','{$dateline}', '".$this->kclass->input['standard']."', '".$this->kclass->input['material']."', '".$this->kclass->input['technics']."', '".$this->kclass->input['remark']."', '".TIMENOW."', '".$this->kclass->user['userid']."', '".TIMENOW."')
		");
		}
		$quotingid = $this->kclass->DB->insertID();
		for($k = 1; $k<=$j; $k++){
			$this->kclass->input['itemQuantity'][$k]=intVal($this->kclass->input['itemQuantity'][$k]);
			$this->kclass->input['itemPrice'][$k]=floatVal($this->kclass->input['itemPrice'][$k]);
			$this->kclass->input['itemDiscount'][$k]=intVal($this->kclass->input['itemDiscount'][$k]);
			if($this->kclass->input['itemDiscount'][$k]<0 OR $this->kclass->input['itemDiscount'][$k]>100){
				$this->kclass->input['itemDiscount'][$k]=100;
			}
			$amount = $this->kclass->input['itemQuantity'][$k] * $this->kclass->input['itemPrice'][$k];
			if($this->kclass->input['itemDiscount'][$k]<100){
				$amount = $amount * $this->kclass->input['itemDiscount'][$k] / 100;
			}
			$amount = number_format($amount, 2, '.', '');
			if($this->kclass->input['itemKill'][$k]=='' AND $this->kclass->input['itemProductid'][$k]>0 AND $this->kclass->input['itemQuantity'][$k]!='' AND $this->kclass->input['itemUnitid'][$k]>0 AND $this->kclass->input['itemPrice'][$k]>=0){
				$productItem=$this->kclass->DB->queryFirst("
					SELECT pi.productid,pi.attributevalue,
						p.title
					FROM productitem AS pi
					LEFT JOIN product AS p ON (p.productid=pi.productid)
					WHERE pi.productitemid='".$this->kclass->input['itemProductid'][$k]."'
				");
				$this->kclass->DB->query("
					INSERT INTO `item` (`module`,`mid`,`productid`,`productitemid`,`productAttributes`,`caption`,`brandid`,`packingid`,`quantity`,`discount`,`unitid`,`price`,`amount`,`modified`,`creator`,`created`)
					VALUES ('quoting','".$quotingid."','".$productItem['productid']."','".$this->kclass->input['itemProductid'][$k]."','".$productItem['attributevalue']."','".$this->kclass->input['itemCaption'][$k]."','".$this->kclass->input['itemBrandid'][$k]."','".$this->kclass->input['itemPackingid'][$k]."','".$this->kclass->input['itemQuantity'][$k]."','".$this->kclass->input['itemDiscount'][$k]."','".$this->kclass->input['itemUnitid'][$k]."','".$this->kclass->input['itemPrice'][$k]."','".$amount."','".TIMENOW."','".$this->kclass->user['userid']."','".TIMENOW."')
				");
			}
		}

		//配件明细
		for($k = 1; $k < (count($this->kclass->input['itemPartsid'])+1); $k++){
			$this->kclass->input['itemPartsQuantity'][$k]=intVal($this->kclass->input['itemPartsQuantity'][$k]);
			$this->kclass->input['itemPartsPrice'][$k]=floatVal($this->kclass->input['itemPartsPrice'][$k]);
			$this->kclass->input['itemPartsDiscount'][$k]=intVal($this->kclass->input['itemPartsDiscount'][$k]);
			if($this->kclass->input['itemPartsDiscount'][$k]<0 OR $this->kclass->input['itemPartsDiscount'][$k]>100){
				$this->kclass->input['itemPartsDiscount'][$k]=100;
			}

			$amount = $this->kclass->input['itemPartsQuantity'][$k] * $this->kclass->input['itemPartsPrice'][$k];
			if($this->kclass->input['itemPartsDiscount'][$k]<100){
				$amount = $amount * $this->kclass->input['itemPartsDiscount'][$k] / 100;
			}
			$amount = number_format($amount, 2, '.', '');
			if($this->kclass->input['itemPartsKill'][$k]=='' AND $this->kclass->input['itemPartsid'][$k]>0 AND $this->kclass->input['itemPartsQuantity'][$k]!='' AND $this->kclass->input['itemPartsUnitid'][$k]>0 AND $this->kclass->input['itemPartsPrice'][$k]>=0){
				$part=$this->kclass->DB->queryFirst("
					SELECT p.materialid,
						m.materialno
					FROM product p
					LEFT JOIN material AS m ON (m.materialid=p.materialid)
					WHERE productid='".$this->kclass->input['itemPartsid'][$k]."'
				");
				$this->kclass->DB->query("
					INSERT INTO `item` (`module`,`mid`,`materialid`,`productid`,`caption`,`brandid`,`packingid`,`quantity`,`discount`,`unitid`,`price`,`amount`,`modified`,`creator`,`created`)
					VALUES ('quoting','".$quotingid."','".$part['materialid']."','".$this->kclass->input['itemPartsid'][$k]."','".$this->kclass->input['itemPartsCaption'][$k]."','".$this->kclass->input['itemPartsBrandid'][$k]."','".$this->kclass->input['itemPartsPackingid'][$k]."','".$this->kclass->input['itemPartsQuantity'][$k]."','".$this->kclass->input['itemPartsDiscount'][$k]."','".$this->kclass->input['itemPartsUnitid'][$k]."','".$this->kclass->input['itemPartsPrice'][$k]."','".$amount."','".TIMENOW."','".$this->kclass->user['userid']."','".TIMENOW."')
				");
			}
		}
		if($this->kclass->input['inquiryid']>0){
			$this->kclass->DB->query("UPDATE `inquiry` SET quotingid='".$quotingid."', replied='".TIMENOW."', replyTimes=(replyTimes+1) WHERE inquiryid='".$this->kclass->input['inquiryid']."'");
		}
		$this->kclass->messager(array(
			'title' => $title,
			'text' => '客户<b>'.$this->kclass->input['title'].'</b> 的报价单已新建成功!',
			'url' => '/s.php?module=customer&action=view&customerid='.$this->kclass->input['customerid'],
			'sec' => 2
		));
	}
	function STQuoting(){
		//print_r($this->kclass->input);
		$list=$this->kclass->STquotingorder(array('action'=>'list'));
		$title="新建标准报价单";
		$paymentterm = $this->kclass->chooserPaymentterm(array('name'=>'paymenttermid', 'hasBlank'=>1, 'width'=>450,'selectedid'=>1));
		$deliveryterm = $this->kclass->chooserDeliveryterm(array('name'=>'deliverytermid', 'hasBlank'=>1, 'width'=>300,'selectedid'=>1));
		$port = $this->kclass->chooserPort(array('name'=>'portid', 'hasBlank'=>1, 'width'=>300,'selectedid'=>1 ));
		$currency = $this->kclass->chooserCurrency(array('name'=>'currencyid', 'hasBlank'=>1, 'width'=>100, 'selectedid'=>1));
		$quotingtype=$this->kclass->chooserQuotingtype(array('name'=>'typeid','hasBlank'=>1,'width'=>100,'selectedid'=>1));
		$flow=$this->kclass->listFlow(array('module'=>'STQuoting','statusid'=>1));

		$upload=$this->kclass->upload(array('title'=>'相关附件：','module'=>'stquoting'));
		for($i=1;$i<5;$i++){
			$selectStr='<input type="hidden"id="tditemProductid'.$k.'input" value="'.$product['entitle'].'"/>';
			$itemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').'">
			<td><span id="tditemProdctid'.$i.'">'.$selectStr.'</span><input type="hidden" name="itemId['.$i.']" value="'.$item['itemid'].'">'.$this->kclass->chooserProductItem(array('name'=>'itemProductid['.$i.']','selectName'=>'itemProductid'.$i.'','selectedid'=>$item['productitemid'],'hasBlank'=>1,'width'=>500)).'</td>
			<td><input type="text" name="itemCaption['.$i.']" size="65"/></td>
			<td>'.$this->kclass->chooserBrand(array('name'=>'itemBrandid['.$i.']','hasBlank'=>1,'width'=>100,'selectedid'=>1)).'</td>
			<td>'.$this->kclass->chooserPacking(array('name'=>'itemPackingid['.$i.']','hasBlank'=>1,'width'=>100,'selectedid'=>6)).'</td>
			<td><input type="text" name="itemDiscount['.$i.']" value="100" size="3"/></td>
			<td><input type="text" name="itemQuantity['.$i.']" size="3" onblur="dc.returnProductStr('.$i.',this.value)"></td>
			<td>'.$this->kclass->chooserUnit(array('name'=>'itemUnitid['.$i.']','hasBlank'=>1,'width'=>80,'selectedid'=>5)).'</td>
			<td><input type="text" name="itemPrice['.$i.']" size=5></td>
			<td><input type="checkbox" name="itemKill['.$i.']"></td>
			</tr>';
		}
	for($i=1;$i<5;$i++){
		$partStr.='<tr class="'.$this->kclass->iif($this->kclass->rotate,'odd','even').'" id="producttr'.$i.'">
		<td>'.$this->kclass->chooserParts(array('name'=>'itemPartsid['.$i.']','hasBlank'=>1,'width'=>500)).'</td>
         <td><input type="text" name="itemPartsCaption['.$i.']" size="65"/></td>
		 <td>'.$this->kclass->chooserBrand(array('name'=>'itemPartsBrandid['.$i.']','hasBlank'=>1,'width'=>100,'selectedid'=>1)).'</td>
			<td>'.$this->kclass->chooserPacking(array('name'=>'itemPartsPackingid['.$i.']','hasBlank'=>1,'width'=>100,'selectedid'=>6)).'</td>
			<td><input type="text" name="itemPartsDiscount['.$i.']" value="100" size="3"/></td>
			<td><input type="text" name="itemPartsQuantity['.$i.']" size="3"/></td>
			<td>'.$this->kclass->chooserUnit(array('name'=>'itemPartsUnitid['.$i.']','hasBlank'=>1,'width'=>80,'selectedid'=>5)).'</td>
			<td><input type="text" name="itemPartsPrice['.$i.']" size="5"/></td>
			<td><input type="checkbox" name="itemPartsKill['.$i.']" /></td>
		</tr>';
	}
	$created=date('Y-m-d');
$body.=<<<EOF

<dl id="customer" class="tabs">
	<dt title="新建标准报价单" id='d1'>新建报价单</dt>
	<dt title="查找标准报价单" id='d2'>查找报价单</dt>
	<dt title="标准报价单列表" id='d3'>标准报价单列表</dt>
	<dd>
	<div><span class="right">{$flow}</span></div>
	<form action="/s.php?module=customer&action=addSTQuoting" method="post">
    <table class="hundred">
		<thead>
		<tr>
		<th  colspan="4">{$title}</th>
		</tr>
		</thead>
		<tbody>
		<tr class="odd">
		<td>报价单类型：</td><td>{$quotingtype}</td>
        <td>支持交货条款：</td><td>{$deliveryterm} 　|　运费：<input type="text" name="freight"/></td>
		</tr>
      <tr class="odd">
	  <td >中文名称：</td><td><input type="text" name="cntitle"/></td>
	  <td>支持地区：</td><td>{$port}</td>
	  </tr>
	  <tr class="odd">
	  <td>英文名称：</td><td><input type="text" name="entitle"/></td>
	  <td>支持币种：</td><td>{$currency}</td>
	  </tr>
	  </tbody>
	</table>
	<table class="hundred">
	<thead>
		<tr>
		<th colspan="9">产品明细表</th>
		</tr>
	</thead>
	<tbody>
		<tr>
		<td>产品选择</td><td>参数说明</td><td>品牌</td><td>包装</td><td>折扣</td><td>数量</td>
		<td>单位</td><td>单价</td><td>删</td>
		</tr>
		{$itemtr}
		<tr>
		<td>配件选择</td><td>参数说明</td><td>品牌</td><td>包装</td><td>折扣</td><td>数量</td><td>单位</td><td>单价</td><td>删</td>
		</tr>
		{$partStr}
	<tr>
	<td colspan="9">
	<div>注：①如行数不够，请保存后再进入修改页面。②至少有“产品”、“数量”、“单位”三个项目选择填写，该明细才会被保存。③“折扣”请填写百分比“%”。④“折扣”意为打折，例如：折扣为80%，那么商品的价格为：单价格*数量*80</div>
	</td>
	</tr>
	</tbody>
	</table>
	<table class="hundred">
		<tbody>
			<tr class="odd">
				<td>规格说明：</td>
				<td><textarea name="standard" style="width:450px;height:60px"></textarea></td>
				<td>主要材料：</td>
				<td><textarea name="material" style="width:450px;height:60px"></textarea></td>
			</tr>
			<tr>
				<td>工艺说明：</td><td><textarea name="technics" style="width:450px;height:60px"></textarea></td>
				<td>备注：</td>
				<td><textarea name="remark" style="width:450px;height:60px"></textarea></td>
			</tr>
			<tr>
			<td colspan="4"><div>注释：①此页面内容一般情况下请使用英文填写。</div></td>
			</tr>
		</tbody>
	</table>
	{$upload}
	<table class="hundred">
		<tr>
		<td align="center">
		<input type="submit"  value="	保存		" accesskey="s"/>
		<input type="reset" value="	复位		">
		</td>
		</tr>
	</table>	
	</form>
	</dd>

	<dd>
	<form action="/s.php?module=customer&action=STQuotingfind" method="post">
	<table>
	<thead>
	<tr>
	<th colspan="2" width="600px">查找报价单</th>
	</tr>
	</thead>
	<tbody>
	<tr class="odd"><td>报价单ID：</td><td><input type="text" name="QuotingID"/></td></tr>
	<tr class="even"><td>报价单名称：</td><td><input type="text" name="Quotingname"/></td></tr>
	<tr class="odd"><td>报价单类型：</td><td>{$quotingtype}</td></tr>
	<tr class="even"><td>包括支持地区：</td><td>{$port}</td></tr>
	<tr class="odd"><td>包括币种：</td><td>{$currency} </td></tr>
	<tr class="even"><td>包括创建人：</td><td><input type="text" name="creator"></td></tr>
	<tr class="even"><th colspan="4">选项</th></tr>
	<tr class="odd"><td>排序方式：</td>
	<td>
	<label for="o0"><input type="radio" name="ordertype" value="title" id="o0" checked>名称</label>
	<label for="o1" ><input type="radio" name="ordertype" value="ID" id="o1">ID</label>
	<label for="o2"><input type="radio" name="ordertype" value="created" id="o2">创建时间</label>
	<label for="o3"><input type="radio" name="ordertype" value="modified" id="o3">最后修改时间</label>
	</td></tr>
	<tr class="even"><td>排列方向：</td>
	<td>
	<label for="d0"><input type="radio" name="direction" value="ASC" id="d0" checked>升序(A-Z)</label>
	<label for="d1"><input type="radio" name="direction" value="DESC" id="d1">降序(Z-A)</label>
	</td></tr>
	<tr class="odd"><td>显示方式：</td>
	<td>
      <label for="s0"><input type="radio" name="show" value="default" id="s0" checked>默认</label>
	  <label for="s1"><input type="radio" name="show" value="all">所有<span class="small gray">(包括删除)</span></label>
	</td>
	</tr>
    <tr>
	<td class="even">列表</td>
	<td>
	<label for="l0"><input type="radio" name="layout" value="list" checked>列表</label>
	<label foe="l1"><input type="radio" name="layout" value="grid">格子</label>
	</td>
	</tr>
	<tr class="odd">
	<td align="center" colspan="2">
		<input type="submit" value="   查找   " accesskey="s">
		<input type="reset" value="   复位   ">
	</td>
</tr>
	</tbody>
	</table>
	</dd>
	<dd>
	<table class="hundred">
	<thead>
	  <tr>
	  <th>编号</th><th>报价单名称</th><th>报价单类型</th><th>审核</th><th>支持地区</th><th>支持币种</th><th>创建人</th><th>审核人</th><th>操作</th>
	  </tr>
	</thead>
	{$list}
	</table>
	</dd>
</dl>
EOF;
$this->kclass->page['title'].='-标准报价单';
$this->kclass->page['onload'] .= "dc.tabs({'id':'customer'})";
$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.'-标准报价单','right'=>'<a href="/s.php?moudle=customer">返回列表</a>','body'=>$body));
	}
	//view standard quoting 
function STQuotingview(){
   if(!($this->kclass->input['STQuotingid']>0))$this->kclass->boinkIt('/s.php?module=customer');
   $item=$this->kclass->DB->queryFirst("
					SELECT  p.entitle AS pentitle,p.title AS ptitle, c.title AS ctitle,qt.title AS qttitle,u.username AS uname,st.cntitle AS stcntitle,st.freight AS stfreight,st.entitle AS stentitle,st.created AS stcreated,de.title AS  detitle,de.entitle AS deentitle,st.standrad,st.material,st.technics,st.remark,st.ifverify,st.verifier,st.verified,st.modifier,st.modified,st.creator,m.realname AS mname,b.realname	AS bname
					FROM `stquoting`				AS	st
					LEFT JOIN `port`					AS	p		ON ( p.portid=st.portid)
					LEFT JOIN `currency`			AS	c		ON (c.currencyid=st.currencyid)
					LEFT JOIN `quotingtype`	AS	qt		ON (qt.typeid=st.typeid)
					LEFT JOIN `user`					AS	u		ON  (u.userid=st.creator)
					LEFT JOIN `deliveryterm`	AS	de	ON (de.deliverytermid=st.deliverytermid)
					LEFT JOIN `member`			AS	m		ON	(m.userid=st.verifier)
					LEFT JOIN `member`			AS    b		ON	(b.userid=st.creator)
					WHERE st.quotingid='".$this->kclass->input['STQuotingid']."'
				");
	$doverify=$this->kclass->doverify(array('module'=>'customer','action'=>'verify'));
	if($doverify){
      $action='<a href="/s.php?module=customer&action=verify&stquotingid='.$this->kclass->input['STQuotingid'].'">审核</a>';
	}
	if($item['ifverify']==0){
			$ifverify='<span class="darkred">未审核'.$action;
			$status=2;
		}elseif($item['ifverify']==1){
			$ifverify='<span class="green">已审核';
			$status=3;
		}elseif($item['ifverify']==-1){
			$ifverify='<span class="red">审核未通过';
			$status=2;
		}
		if($item['verified']!=0){
			$verifier=$item['mname'].'于'.date('Y-m-d H:i:s',$item['verified']).'审核';
		}else{
			$verifier=" ";
		}
		if($item['modified']!=0){
			$modifier=$item['modifier'].'于'.date('Y-m-s H:i:s',$item['modified']).'最后修改';
		}
		$flow=$this->kclass->listFlow(array('module'=>'STQuoting','statusid'=>$status));
		$attach = $this->kclass->getAttachs(array('module'=>'customer', 'mid'=>$this->kclass->input['STQuotingid']));
		if($attach!= false){
			$attachs = '<div class="clear">'.$attach.'</div>';
		}
	  $title=$item['stentitle'].'('.$item['stcntitle'].')';
	  $date=date('Y-m-d H:i:s',$item['stcreated']);
     $items=$this->kclass->DB->query("
		SELECT `item`.*,
					product.title AS product,
					material.standard,
					packing.title AS packing,
					unit.title			AS unit
		FROM `item`
		LEFT JOIN product ON (product.productid=`item`.productid)
		LEFT JOIN material ON (material.materialid=`item`.materialid)
		LEFT JOIN packing ON (packing.packingid=`item`.packingid)
		LEFT JOIN unit		ON (unit.unitid=`item`.unitid)
		WHERE `item`	.killed=0 AND `item`.module='stquoting' AND
		`item`.mid='".$this->kclass->input['STQuotingid']."'
		ORDER BY itemid ASC
	 ");
	 $itemtr='';
	 $total=$quantity=0;
	 if($itemnum=$this->kclass->DB->numRows()){
		 while($itemstr=$this->kclass->DB->fetchArray($items)){
			 if($itemstr['quantity']>0 AND $itemstr['price']>0){
				 $amount=$itemstr['quantity']*$itemstr['price'];
				 $total+=$amount;
			 }
			 //print_r($itemstr);
			 $product=$this->kclass->getProduct(array('productid'=>$itemstr['productid'],'itemid'=>$itemstr['productitemid']));
			 $itemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').'">
			                     <td>'.$product['title'].'</td>
								 <td>'.$this->kclass->iif($itemstr['materialid']>0,$itemstr['standard'],$product['standard']).'</td>
								 <td>'.$itemstr['caption'].'</td>
								 <td>'.$itemstr['price'].'</td>
								
								 <td>'.$itemstr['packing'].'</td>
								 <td>'.$itemstr['discount'].'</td>
								  <td>'.$itemstr['quantity'].''.$itemstr['unit'].'</td>
								 <td>'.number_format($amount,2).'</td>
							</tr>';
			$quantity+=$itemstr['quantity'];
		 }
		 $itemtr.='<tr><td colspan="6" align="right">合计：</td><td>'.number_format($quantity).'</td><td>'.number_format($total,2).'</td></tr>';
	 }
	$body.=<<<EOF
		<div><span class="small gray">当前审核状态：{$ifverify}</span>   &nbsp;&nbsp;&nbsp;&nbsp;    <span class="gray small">{$verifier}</span><span class="right normal"><a href="/s.php?module=customer&action=STQuotingupdate&quotingid={$this->kclass->input['STQuotingid']}&rt=view">修改</a> &nbsp;&nbsp;&nbsp;<a href="/s.php?module=customer&action=STQuotingremove&STQuotingid={$this->kclass->input['STQuotingid']}">删除</a> &nbsp;&nbsp;&nbsp;<a href="/s.php?module=printer&action=&customerid={$this->kclass->input['STQuoting']}">打印</a></span>
	
		</div>
		<div class="title"><span class="right small gray">由 {$item['bname']}创建于{$date}</span>
		{$title}</div>
		<div><span class="right">{$flow}</span></div>
		<table class="hundred">
		<thead>
		<tr ><td colspan="4">名称： <strong>{$title}</strong>&nbsp;&nbsp;报价类型：<strong>{$item['qttitle']}</strong>&nbsp;&nbsp;支持币种:<strong>{$item['ctitle']}</strong> <strong>由{$item['uname']}于{$date}创建</strong></td></tr>
		</thead>
		</tbody>
		<tr class="odd"><td width="200px">支持交货条款</td><td>{$item['detitle']}({$item['deentitle']})　｜　运费：{$item['stfreight']}</td><td	width="200px">支持地区</td><td>{$item['ptitle']}({$item['pentitle']})</td></tr>
		<tr class="odd"><td>规格说明</td><td>{$item['standrad']}</td><td>工艺说明</td><td>{$item['technics']}</td></tr>
		<tr><td>主要材料</td><td>{$item['material']}</td><td>备注</td><td>{$item['remark']}</td></tr>
		</tbody>
		</table>
		<table class="hundred">
		<thead>
		<tr><th  colspan=8>产品明细</th></tr>
		</thead>
		<tbody>
		<tr>
		<td>报价产品</td><td>产品属性描述</td><td>说明</td><td>单价</td><td>包装</td><td>折扣</td><td>订购量</td><td>小计</td>
			{$itemtr}
			
		</tr>
	<tr><td colspan="8">{$attach}</td></tr>
		</tbody>
		</table>
EOF;
$this->kclass->page['title'].='-标准报价单详细';
$this->kclass->page['onload'].='dc.tabhover();$(\'#dateline\').datepicker()';
$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.'-标准定价单详细','right'=>'<a href="/s.php?module=customer">返回列表</a>','body'=>$body));
}
//update standard quoting
function STQuotingupdate(){
   if($this->kclass->input['quotingid']<0 OR !$quoting=$this->kclass->DB->queryFirst("SELECT `stquoting`.*,t.title AS ttitle FROM `stquoting` LEFT JOIN `quotingtype`  AS t ON(t.typeid=`stquoting`.typeid) WHERE quotingid='".$this->kclass->input['quotingid']."'")){
	   $this->kclass->boimkIt('/s.php?module=customer');
   }
   $this->kclass->br2nl=true;
   $quoting['standard']=$this->kclass->parseConvertValue($quoting['standard']);
   $quoting['material']=$this->kclass->parseConvertValue($quoting['material']);
   $quoting['technics']=$this->kclass->parseConvertValue($quotng['technics']);
   $quoting['remark']=$this->kclass->parseConvertValue($quoting['remark']);
   $currency=$this->kclass->chooserCurrency(array('name'=>'currencyid','hasBlank'=>1,'width'=>100,'selectedid'=>$quoting['currencyid']));
   $shipmethod=$this->kclass->chooserShipmethod(array('name'=>'shipmethod','hasBlank'=>1,'width'=>450,'selectedid'=>$quoting['shipmethodid']));
   $paymentterm=$this->kclass->chooserPaymentterm(array('name'=>'paymenttermid','hasBlank'=>1,'width'=>450,'selectedid'=>$quoting['paymenttermid']));
   $deliveryterm=$this->kclass->chooserDeliveryterm(array('name'=>'deliverytermid','hasBlank'=>1,'width'=>450,'selectedid'=>$quoting['deliverytermid']));
   $port=$this->kclass->chooserPort(array('name'=>'portid','hasBlank'=>1,'width'=>450,'selectedid'=>$quoting['portid']));
   $quotingtype=$this->kclass->chooserQuotingtype(array('name'=>'typeid','hasBlank'=>1,'width'=>100,'selectedid'=>$quoting['typeid']));
   $items=$this->kclass->DB->query("SELECT * FROM `ITEM`  WHERE module='stquoting' AND mid='".$quoting['quotingid']."' ORDER BY  modified ASC");
   if($this->kclass->DB->numRows()){
	   $k=1;$n=1;
	   while($item=$this->kclass->DB->fetchArray($items)){
		   if($item['productitemid']>0){
			   $product=$this->kclass->DB->queryFirst("
				SELECT `product`.title,`product`.entitle
				FROM `productitem`
				LEFT JOIN `product`	ON (`productitem`.productid=`product`.productid)
				WHERE productitemid='".$item['producitemid']."'
			   ");
			   $selectStr='<input type="hidden" id="tditemProductid'.$k.'input" value="'.$product['entitle'].'">';
			   $seletStr = '<input type="hidden" id="tditemProductid'.$k.'input" value="'.$product['entitle'].'" />';
					$itemtr .= '<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').'">
					<td><span id="tditemProductid'.$k.'">'.$seletStr.'</span><input type="hidden" name="itemId['.$k.']" value="'.$item['itemid'].'">'.$this->kclass->chooserProductItem(array('name'=>'itemProductid['.$k.']','selectName'=>'itemProductid'.$k, 'selectedid'=>$item['productitemid'], 'hasBlank'=>1, 'width'=>500)).'</td>
					<td><input type="text" name="itemCaption['.$k.']" value="'.$item['caption'].'" size="65"></td>
					<td>'.$this->kclass->chooserBrand(array('name'=>'itemBrandid['.$k.']', 'hasBlank'=>1, 'width'=>80, 'selectedid'=>$item['brandid'])).'</td>
					<td>'.$this->kclass->chooserPacking(array('name'=>'itemPackingid['.$k.']', 'hasBlank'=>1, 'width'=>100, 'selectedid'=>$item['packingid'])).'</td>
					<td><input type="text" name="itemDiscount['.$k.']" value="'.$item['discount'].'" size="3"></td>
					<td><input type="text" name="itemQuantity['.$k.']" value="'.$item['quantity'].'" size="3" onblur="dc.returnProductStr('.$k.',this.value)"></td>
					<td>'.$this->kclass->chooserUnit(array('name'=>'itemUnitid['.$k.']', 'hasBlank'=>1, 'width'=>80, 'selectedid'=>$item['unitid'])).'</td>
					<td><input type="text" name="itemPrice['.$k.']" value="'.$item['price'].'" size="5"></td>
					<td><input type="checkbox" name="itemKill['.$k.']" value="'.$item['itemid'].'"></td>
					</tr>';
					$k++;
		   }elseif($item['materialid']>0){
			   $parts .= '<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').'">
					<td><input type="hidden" name="itemPartsId['.$n.']" value="'.$item['itemid'].'">'.$this->kclass->chooserParts(array('name'=>'itemPartsid['.$n.']', 'selectedid'=>$item['productid'], 'hasBlank'=>1, 'width'=>500)).'</td>
					<td><input type="text" name="itemPartsCaption['.$n.']" value="'.$item['caption'].'" size="65"></td>
					<td>'.$this->kclass->chooserBrand(array('name'=>'itemPartsBrandid['.$n.']', 'hasBlank'=>1, 'width'=>80, 'selectedid'=>$item['brandid'])).'</td>
					<td>'.$this->kclass->chooserPacking(array('name'=>'itemPartsPackingid['.$n.']', 'hasBlank'=>1, 'width'=>100, 'selectedid'=>$item['packingid'])).'</td>
					<td><input type="text" name="itemPartsDiscount['.$n.']" value="'.$item['discount'].'" size="3"></td>
					<td><input type="text" name="itemPartsQuantity['.$n.']" value="'.$item['quantity'].'" size="3"></td>
					<td>'.$this->kclass->chooserUnit(array('name'=>'itemPartsUnitid['.$n.']', 'hasBlank'=>1, 'width'=>80, 'selectedid'=>$item['unitid'])).'</td>
					<td><input type="text" name="itemPartsPrice['.$n.']" value="'.$item['price'].'" size="5"></td>
					<td><input type="checkbox" name="itemPartsKill['.$n.']" value="'.$item['itemid'].'"></td>
					</tr>';
					$n++;
		   }
	   }
   }
   $itemtr.='<tr><td colspan="11">新建明细</td></tr>';
		for($i = $k; $i < ($k+5); $i++){
			$itemtrs .= '<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').'">
			<td><span id="tditemProductid'.$i.'"></span>'.$this->kclass->chooserProductItem(array('name'=>'itemProductid['.$i.']','selectName'=>'itemProductid'.$i, 'hasBlank'=>1, 'width'=>500)).'</td>
			<td><input type="text" name="itemCaption['.$i.']" value="'.$item['caption'].'"  size="65"></td>
			<td>'.$this->kclass->chooserBrand(array('name'=>'itemBrandid['.$i.']', 'hasBlank'=>1, 'width'=>80,'selectedid'=>1)).'</td>
			<td>'.$this->kclass->chooserPacking(array('name'=>'itemPackingid['.$i.']', 'hasBlank'=>1, 'width'=>100,'selectedid'=>6)).'</td>
			<td><input type="text" name="itemDiscount['.$i.']" value="100" size="3"></td>
			<td><input type="text" name="itemQuantity['.$i.']" value="'.$item['quantity'].'" size="3"  onblur="dc.returnProductStr('.$i.',this.value)"></td>
			<td>'.$this->kclass->chooserUnit(array('name'=>'itemUnitid['.$i.']', 'hasBlank'=>1, 'width'=>80, 'selectedid'=>5)).'</td>
			<td><input type="text" name="itemPrice['.$i.']" value="'.$item['price'].'" size="5"></td>
			</tr>';
		}
		for($i = $n; $i < ($n+5); $i++){
			$partStr .= '<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').'">
			<td>'.$this->kclass->chooserParts(array('name'=>'itemPartsid['.$i.']', 'hasBlank'=>1, 'width'=>500)).'</td>
			<td><input type="text" name="itemPartsCaption['.$i.']" value="'.$item['caption'].'"  size="65"></td>
			<td>'.$this->kclass->chooserBrand(array('name'=>'itemPartsBrandid['.$i.']', 'hasBlank'=>1, 'width'=>80,'selectedid'=>1)).'</td>
			<td>'.$this->kclass->chooserPacking(array('name'=>'itemPartsPackingid['.$i.']', 'hasBlank'=>1, 'width'=>100,'selectedid'=>6)).'</td>
			<td><input type="text" name="itemPartsDiscount['.$i.']" value="100" size="3"></td>
			<td><input type="text" name="itemPartsQuantity['.$i.']" value="'.$item['quantity'].'" size="3"></td>
			<td>'.$this->kclass->chooserUnit(array('name'=>'itemPartsUnitid['.$i.']', 'hasBlank'=>1, 'width'=>80, 'selectedid'=>5)).'</td>
			<td><input type="text" name="itemPartsPrice['.$i.']" value="'.$item['price'].'" size="5"></td>
			</tr>';
		}
   $body=<<<EOF
  <form action="/s.php?module=customer&action=STQuotingdoupdate" name="customer" method="post">
  <input type="hidden" name="module" value="customer"/>
  <input type="hidden" name="action" value="STQuotingdoupdate">
  <input type="hidden" name="quotingid" value="{$quoting['quotingid']}">
  <table class="hundred">
  <thead>
  <tr>
  <th colspan="4">修改<span class="middle bold">标准报价单</span><span class="big bold">{$quoting['cntitle']}({$quoting['entitle']})</span></th>
  </tr>
  </thead>
  <tbody>
  <tr class="even">
  <td>报价单类型：</td>
  <td>{$quotingtype}</td>
  <td>支持交货条款：</td>
	<td>{$deliveryterm} 　|　运费：<input type="text" name="freight" value="{$quoting['freight']}"/></td>
  </tr>
  <tr class="odd">
  <td>中文名称：<span class="red bold">*</span></td>
  <td><input type="text" style="width:244px" name="cntitle" value="{$quoting['cntitle']}"></td>
	<td>支持地区：</td>
	<td>{$port}</td>
  </tr>
  <tr class="even">
   <td>英文名称：<span class="red bold">*</span></td>
   <td><input type="text" style="width:244px" name="entitle" value="{$quoting['entitle']}"></td>
    <td>币种：<span class="red bold">*</span></td>
  <td>{$currency}</td>
  </tr>
  </tbody>
  </table>
  <table class="hundred">
  <thead>
  <tr>
  <th colspan="9">产品明细表</th>
  </tr>
  </thead>
  <tbody>
  <tr class="odd">
  <td>产品选择</td><td>参数说明</td><td>品牌</td><td>包装</td><td>折扣</td><td>数量</td><td>单位</td><td>单价</td><td>删</td>
  {$itemtr}
  </tr><trclass="odd">
  <td>配件选择</td><td>参数说明</td><td>品牌</td><td>包装</td><td>折扣</td><td>数量</td><td>单位</td><td>单价</td><td>删</td>
  {$parts}
  </tr>
  </tbody>
  </table>
  <table class="hundred">
  <thead>
  <tr>
	<th colspan="8">新建明细</th>
</tr>
</thead>
<tbody>
<tr >
  <td>产品选择</td><td>参数说明</td><td>品牌</td><td>包装</td><td>折扣</td><td>数量</td><td>单位</td><td>单价</td>
 {$itemtrs}
  <td>配件选择</td><td>参数说明</td><td>品牌</td><td>包装</td><td>折扣</td><td>数量</td><td>单位</td><td>单价</td>>
  {$partStr}
  </tr>
  <tr class="even">
	<td colspan="11" class="gray small">注：①如行数不够，请保存后再进入修改页面。②至少有“产品”、“数量”、“单位”三个项目选择填写，该明细才会被保存。③“折扣”请填写百分比“%”。</td>
</tr>
</tbody>
</table><table class="hundred">
<tbody>
<tr class="odd">
	<td valign="top">规格说明：</td>
	<td><textarea name="standard" style="width:450px;height:60px">{$quoting['standrad']}</textarea></td>
	<td valign="top">主要材料：</td>
	<td><textarea name="material" style="width:450px;height:60px">{$quoting['material']}</textarea></td>
</tr>

<tr class="even">
	<td valign="top">工艺说明：</td>
	<td><textarea name="technics" style="width:450px;height:60px">{$quoting['technics']}</textarea></td>
	<td valign="top">备　　注：</td>
	<td><textarea name="remark" style="width:450px;height:60px">{$quoting['remark']}</textarea></td>
</tr>
  </tbody>
  </table>
  <table class="hundred">
  <tbody>
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
	$this->kclass->page['title'].='-修改标准报价单-'.$quoting['cntitle'].'('.$quoting['entitle'].')';
	$this->kclass->page['onload'].='dc.tabhover();$(\'#dateline\').datepicker()';
	$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.'-修改标准报价单','right'=>'<a href="/s.php?module=customer">返回列表</a>','body'=>$body));
}
//
function STQuotingdoupdate(){
	//print_r($this->kclass->input);
	if($this->kclass->input['requestMethod']!='post'){
		$e='<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			if(strlen($this->kclass->input['cntitle'])<=1 AND strlen($this->kclass->input['entitle'])<=1){
				$e.='<li>报价单的名称不能少于2个字符。</li>';
			}
			if($this->kclass->input['currencyid']<=0){
				$e.='<li>请选择币种。</li>';
			}
			$j=count($this->kclass->input['itemProductid']);
			$hasItem=$hasPartItem=0;
			for($i=1;$i<$j+1;$i++){
				if($this->kclass->input['itemkill'][$i]=='' AND $this->kclass->input['itemProductid'][$i]>0 AND $this->kclass->input['itemQuantity'][$i]!='' AND $this->kclass->input['itemUnitid'][$i]>0 AND $this->kclass->input['itemPrice'][$i]>=0){
					$hasItem=1;
				}
			}
			$m=count($this->kclass->input['itemPartsId']);
			for($n=1;$n<$m+1;$n++){
				if($this->kclass->input['itemPartsKill'][$n]==''AND $this->kclass->input['itemPartsid'][$n]>0 AND $this->kclass->input['itemPartsQuantity'][$n]!='' AND $this->kclass->input['itemPartsUnitid'][$n]>0 AND $this->kclass->input['itemPartsPrice'][$n]>=0){
					$hasPartItem=1;
				}
			}
			if($hasIten==0 AND $hasPartItem==0){
				$e.='<li>需要至少一条订单明细，才能建立订单</li>';
			}
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title'=>'修改标准报价单',
				'text'=>'您在修改标准报价单的过程中有一下错误：<ul>'.$e.'</ul>',
				'url'=>'javascript:history.back()',
				'sec'=>3
				));
		}
		for($i=1;$i<=$j;$i++){
			$this->kclass->input['itemQuantity'][$i]=intVal($this->kclass->input['itemQuantity'][$i]);
			$this->kclass->input['itemPrice'][$i]=floatVal($this->kclass->input['itemPrice'][$i]);
			$this->kclass->input['itemDiscount'][$i]=intVal($this->kclass->input['itemDiscount'][$i]);
			if($this->kclass->input['itemDiscount'][$i]<0 OR $this->kclass->input['itemDiscount'][$i]>100){
				$this->kclass->input['itemDiscount'][$i]=100;
			}
			$amount = $this->kclass->input['itemQuantity'][$i] * $this->kclass->input['itemPrice'][$i];
			if($this->kclass->input['itemDiscount'][$i]<100){
				$amount = $amount * $this->kclass->input['itemDiscount'][$i] / 100;
			}
			$amount = number_format($amount, 2, '.', '');
			$productItem=$this->kclass->DB->queryFirst("
			SELECT pi.productid,pi.attributevalue,
						p.title
			FROM productitem AS  pi
			LEFT JOIN product AS p ON (p.productid=pi.productid)
			WHERE productitemid='".$this->kclass->input['itemProductid'][$i]."';
			");
			if($this->kclass->input['itemProductid'][$i]>0 AND $this->kclass->input['itemQuantity'][$i]!='' AND $this->kclass->input['itemUnitid'][$i]>0 AND $this->kclass->input['itemPrice'][$i]>=0){
				if($this->kclass->input['itemId'][$i]){
				if($this->kclass->input['itemkill'][$i]>0){//删除
					$this->kclass->DB->query("UPDATE `item` SET killer='".$this->kclass->user['userid']."',killed='".TIMENOW."' WHERE itemid='".$this->kclass->input['itemId'][$i]."'");
				}else{
					$pitem='-'.$this->kclass->input['itemQuantity'][$i].$productItem['title'];
					$this->kclass->DB->query("
					UPDATE `item` SET
						`productid`='".$productItem['productid']."',
						`productitemid`='".$this->kclass->input['itemProductid'][$i]."',
						`productAttributes`='".$productItem['attributevalue']."',
						`caption`='".$this->kclass->input['itemCaption'][$i]."',
						`brandid`='".$this->kclass->input['itemBrandid'][$i]."',
						`packingid`='".$this->kclass->input['itemPackingid'][$i]."',
						`discount`='".$this->kclass->input['itemDiscount'][$i]."',
						`quantity`='".$this->kclass->input['itemQuantity'][$i]."',
						`unitid`='".$this->kclass->input['itemUnitid'][$i]."',
						`price`='".$this->kclass->input['itemPrice'][$i]."',
						`amount`='".$amount."',
						`modified`='".TIMENOW."',
						`modifier`='".$this->kclass->user['userid']."'
						WHERE itemid='".$this->kclass->input['itemId'][$i]."'
					");
					$quantity+=$this->kclass->input['itemQuantity'][$i];
				}
			}else{
					$pitem.='-'.$this->kclass->input['itemQuantity'][$i].$productItem['title'];
					$this->kclass->DB->query("
						INSERT INTO `item`(`module`,`mid`,`productid`,`productitemid`,`productAttributes`,`title`,`module`,`caption`,`articleNO`,`brandid`,`packingid`,`quantity`,`unitid`,`price`,`amount`,`modified`,`creator`,`created`)
						VALUES
						('stquoting','".$this->kclass->input['quotingid']."','".$productItem['productid']."','".$this->kclass->input['itemProductid'][$i]."','".$productItem['attributevalue']."','".$this->kclass->input['itemTitle'][$i]."','".$this->kclass->input['itemModel'][$i]."','".$this->kclass->input['itemCaption'][$i]."','".$this->kclass->input['itemArticleNO'][$i]."','".$this->kclass->input['itemBrandid'][$i]."','".$this->kclass->input['itemPackingid'][$i]."','".$this->kclass->input['itemQuantity'][$i]."','".$this->kclass->input['itemDiscount'][$i]."','".$this->kclass->input['itemUnitid'][$i]."','".$this->kclass->input['itemPrice'][$i]."','".$amount."','".TIMENOW."','".$this->kclass->input['userid']."','".TIMENOW."')
						");
						}
		      }
		}
		//配件明细
		for($i = 1; $i<=$m; $i++){
			$this->kclass->input['itemPartsQuantity'][$i]=intVal($this->kclass->input['itemPartsQuantity'][$i]);
			$this->kclass->input['itemPartsPrice'][$i]=floatVal($this->kclass->input['itemPartsPrice'][$i]);
			$this->kclass->input['itemPartsDiscount'][$i]=intVal($this->kclass->input['itemPartsDiscount'][$i]);
			if($this->kclass->input['itemPartsDiscount'][$i]<0 OR $this->kclass->input['itemPartsDiscount'][$i]>100){
				$this->kclass->input['itemPartsDiscount'][$i]=100;
			}
			$amount = $this->kclass->input['itemPartsQuantity'][$i] * $this->kclass->input['itemPartsPrice'][$i];
			if($this->kclass->input['itemPartsDiscount'][$i]<100){
				$amount = $amount * $this->kclass->input['itemPartsDiscount'][$i] / 100;
			}
			$amount = number_format($amount, 2, '.', '');
			$part=$this->kclass->DB->queryFirst("SELECT materialid FROM product WHERE productid='".$this->kclass->input['itemPartsid'][$i]."'");
			if($this->kclass->input['itemPartsid'][$i]>0 AND $this->kclass->input['itemPartsQuantity'][$i]!='' AND $this->kclass->input['itemPartsUnitid'][$i]>0 AND $this->kclass->input['itemPartsPrice'][$i]>=0){
				if($this->kclass->input['itemPartsId'][$i]>0){
					if($this->kclass->input['itemPartsKill'][$i]>0){
						$this->kclass->DB->query("UPDATE `item` SET killer=".$this->kclass->user['userid'].",killed=".TIMENOW."  WHERE itemid='".$this->kclass->input['itemPartsId'][$i]."'");
					}else{
						$this->kclass->DB->query("
							UPDATE `item` SET
								`materialid`='".$part['materialid']."',
								`productid`='".$this->kclass->input['itemPartsid'][$i]."',
								`caption`='".$this->kclass->input['itemPartsCaption'][$i]."',
								`brandid`='".$this->kclass->input['itemPartsBrandid'][$i]."',
								`packingid`='".$this->kclass->input['itemPartsPackingid'][$i]."',
								`discount`='".$this->kclass->input['itemPartsDiscount'][$i]."',
								`quantity`='".$this->kclass->input['itemPartsQuantity'][$i]."',
								`unitid`='".$this->kclass->input['itemPartsUnitid'][$i]."',
								`price`='".$this->kclass->input['itemPartsPrice'][$i]."',
								`amount`='".$amount."',
								`modified`='".TIMENOW."',
								`modifier`='".$this->kclass->user['userid']."'
							WHERE itemid='".$this->kclass->input['itemPartsId'][$i]."'
						");
						$quantity+=$this->kclass->input['itemPartsQuantity'][$i];
					}
				}else{
					$this->kclass->DB->query("
						INSERT INTO `item` 
							(`module`,`mid`,`materialid`,`productid`,`title`,`model`,`caption`,`articleNo`,`brandid`,`packingid`,`quantity`,`discount`,`unitid`,`price`,`amount`,`modified`,`creator`,`created`)
						VALUES 
							('stquoting','".$this->kclass->input['quotingid']."','".$part['materialid']."','".$this->kclass->input['itemPartsid'][$i]."','".$this->kclass->input['itemTitle'][$i]."','".$this->kclass->input['itemModel'][$i]."','".$this->kclass->input['itemPartsCaption'][$i]."','".$this->kclass->input['itemArticleNo'][$i]."','".$this->kclass->input['itemPartsBrandid'][$i]."','".$this->kclass->input['itemPartsPackingid'][$i]."','".$this->kclass->input['itemPartsQuantity'][$i]."','".$this->kclass->input['itemPartsDiscount'][$i]."','".$this->kclass->input['itemPartsUnitid'][$i]."','".$this->kclass->input['itemPartsPrice'][$i]."','".$amount."','".TIMENOW."','".$this->kclass->user['userid']."', '".TIMENOW."')
					");
					$quantity+=$this->kclass->input['itemPartsQuantity'][$i];
				}
			}
		}
		$this->kclass->DB->query("
			UPDATE `stquoting` 
			SET `cntitle` = '".$this->kclass->input['cntitle']."',
					`entitle`='".$this->kclass->input['entitle']."',
					`typeid`='".$this->kclass->input['typeid']."',
				`currencyid` = '".$this->kclass->input['currencyid']."', 
				`portid` = '".$this->kclass->input['portid']."', 
				`deliverytermid` = '".$this->kclass->input['deliverytermid']."', 
				`freight` = '".$this->kclass->input['freight']."', 
				`standrad` = '".$this->kclass->input['standard']."', 
				`technics` = '".$this->kclass->input['technics']."', 
				`material` = '".$this->kclass->input['material']."', 
				`remark` = '".$this->kclass->input['remark']."', 
				`modified` = '".TIMENOW."', 
				`modifier` = '".$this->kclass->user['userid']."'
			WHERE quotingid='".$this->kclass->input['quotingid']."'
		");
		$this->kclass->messager(array(
			'title' => '修改发往客户报价单',
			'text' => '发往客户<b>'.$this->kclass->input['title'].'</b> 的报价单已修改成功!',
			'url' => '/s.php?module=customer&action=STQuotingview&STQuotingid='.$this->kclass->input['quotingid'].'',
			'sec' => 2
		));
}
//remove standard quoting
function STQuotingremove(){
  if($this->kclass->input['STQuotingid']<=0){
	  $this->kclass->boinkIt('/s.php?module=customer');
  }
  $quoting=$this->kclass->DB->queryFirst("SELECT  cntitle,entitle FROM stquoting WHERE `stquoting`.quotingid='".$this->kclass->input['STQuotingid']."'");
  if($quoting){
	  $body=<<<EOF
	  <form action="/s.php?module=customer&action=STQuotingkill"  name="customer" method="post">
	  <input type="hidden" name="module" value="customer">
	  <input type="hidden" name="action" value="STQuotingkill">
	  <input type="hidden" name="STQuotingid" value="{$this->kclass->input['STQuotingid']}">
		<table>
		<thead>
		<tr><th>删除标准报价单{$quoting['entitle']}({$quoting['cntitle']})</th></tr>
		</thead>
		<tbody>
			<tr class="even">
				<td class="middle" >你确定要删除标准报价单<b>{$quoting['entitle']}({$quoting['cntitle']})么？</b></td>
			</tr>
			<tr class="odd">
				<td  align="center">
					<label for="c1" class="red"><input type="radio" id="c1" name="confirm" value="1"> 是</label>
					<label for="c0" class="green"><input type="radio" id="c0" name="confirm" value="0" checked>否</label>
				</td>
				<tr class="odd">
				<td align="center">
					<input type="submit" value="提交" accseekey="s">
					<input type="reset" value="重置">
				</td>
				</tr>
			</tr>
		</tbody>
		</table>
	  </form>
EOF;
  }else{
	  $body.='要删除的报价单并不存在';，请返回列表刷新后再操作。（如果问题持续存在，请联系系统管理员。）;
  }
	  $this->kclass->page['title'].='-'.$quoting['entitle'].'('.$quoting['cntitle'].'-删除标准报价单';
	  $this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.'-删除标准报价单','right'=>'<a href="/s.php?module=customer">返回列表</a>','body'=>$body));
}
function STQuotingkill(){
	print_r($this->kclass->input);
	if($this->kclass->input['STQuotingid']<=0){
		$this->kclass->boinkIt('/s.php?module=customer');
	}
	if($this->kclass->input['confirm']==0){
		$this->kclass->boinkIt('/s.php?module=customer&action=STQuoting');
	}
	if($this->kclass->input['STQuotingid']<0){
		$e='<li>请从正确的页面进行操作，如果此问题持续存在，请联系网站管理员。</li>';
	}
	if(isset($e)){
		$this->kclass->messager(array(
			'title'=>'删除标准报价单',
			'text'=>'您在删除标准报价单时发生以下错误：<ul>'.$e.'</ul>',
			'url'=>'javascript',
			'sec'=>3
			));
	}
	$quoting=$this->kclass->DB->queryFirst("
	     SELECT	`stquoting`.quotingid,`stquoting`.killed,`stquoting`.cntitle,`stquoting`.entitle
		 FROM `stquoting`
		 WHERE `stquoting`.quotingid='".$this->kclass->input['STQuotingid']."'
	");
	if($quoting['killed']==0){
		$this->kclass->DB->query("
		UPDATE `stquoting`
		SET killed=".TIMENOW."
		WHERE quotingid='".$quoting['quotingid']."'
			");
			$this->kclass->messager(array(
				'title'=>'删除标准报价单成功',
				'text'=>'标准报价单<b>'.$quoting['entitle'].'('.$quoting['cntitle'].')</b>成功',
				'url'=>'/s.php?module=customer&action=STQuoting',
				'sec'=>2
				));
	}else{
		$this->kclass->messager(array(
			'title'=>'删除标准报价单失败',
			'text'=>'您要删除的产品报价单不存在',
			'url'=>'/s.php?module=customer&action=STQuoting',
			'sec'=>3
			));
	}
}
//
function addSTQuoting(){	
	//print_r($this->kclass->input);
	if($this->kclass->input['requestMethod']!='post'){
		$e.='<li>请从正确的页面进行提交，如果此问题持续出现，请联系网站管理员。</li>';
	}else{
		$title='新建标准报价单';
	if(strlen($this->kclass->input['cntitle'])<1&& strlen($this->kclass->input['entitle'])<1){
		$e.='<li>请填写标准报价单名称（不能为空）</li>';
	}

	$j=count($this->kclass->input['itemProductid']);
	$hasItem=$hasPartItem=0;
	for($i=1;$i<$j+1;$i++){
		if($this->kclass->input['itemKill'][$i]=='' AND $this->kclass->input['itemProductid'][$i]>0 AND $this->kclass->input['itemQuantity'][$i]!='' AND $this->kclass->input['itemUnitid'][$i]>0 AND $this->kclass->input['itemPrice'][$i]>=0){
			$hasItem=1;
		}
	}
	$m=count($this->kclass->input['itemPartsID']);
	for($n=1;$n<$m+1;$n++){
		if($this->kclass->input['itemPartsKill'][$n]=='' AND $this->kclass->input['itemPartsid'][$n]>0 AND $this->kclass->input['itemPartsid'][$n]>0 AND $this->kclass->input['itemPartsQuantity'][$n]='' AND $this->kclass->input['itemPartsUnitid'][$n]>0 AND $this->kclass->input['itemPartsPrice'][$n]>=0){
			$hasPartItem=1;
		}
	}
	if($hasItem==0 AND $hasPartItem==0){
		$e.='<li>需要填写至少有一条明细，才能建立报价单。</li>';
	}
	}
	if(isset($e)){
			$this->kclass->messager(array(
				'title' => $title,
				'text' => '您在添加标准报价单的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
	$this->kclass->DB->query("INSERT INTO `stquoting`(`typeid`,`deliverytermid`,`freight`,`cntitle`,`portid`,`entitle`,`currencyid`,`standrad`,`material`,`technics`,`remark`,`modified`,`creator`,`created`) 
	VALUES('".$this->kclass->input['typeid']."','".$this->kclass->input['deliverytermid']."','".$this->kclass->input['freight']."','".$this->kclass->input['cntitle']."','".$this->kclass->input['portid']."','".$this->kclass->input['entitle']."','".$this->kclass->input['currencyid']."','".$this->kclass->input['standard']."','".$this->kclass->input['material']."','".$this->kclass->input['technics']."','".$this->kclass->input['remark']."','".TIMENOW."','".$this->kclass->user[ 'userid' ]."','".TIMENOW."')"
	);
	$quotingid=$this->kclass->DB->insertID();
	for($k=1;$k<=$j;$k++){
		$this->kclass->input['itemQuantity'][$k]=intval($this->kclass->input['itemQuantity'][$k]);
		$this->kclass->input['itemPrice'][$k]=floatval($this->kclass->input['itemPrice'][$k]);
		$this->kclass->input['itemDiscount'][$k]=intval($this->kclass->input['itemDiscount'][$k]);
		if($this->kclass->input['itemDiscount'][$k]<0 OR $this->kclass->input['itemDiscount'][$k]){
			$this->kclass->input['itemDiscount'][$k]=100;
		}
		$amount=$this->kclass->input['itemQuantity'][$k]*$this->kclass->input['itemPrice'][$k];
		if($this->kclass->input['itemDiscount'][$k]<100){
			$amount=$amount*$this->kclass->intput['itemDiscount'][$k]/100;
		}
		$amount=number_format($amount,2,'.','');
		if($this->kclass->input['itemKill'][$k]==''AND $this->kclass->input['itemProductid'][$k]>0 AND $this->kclass->input['itemQuantity'][$k]!='' AND $this->kclass->input['itemUnitid'][$k]>0 AND $this->kclass->input['itemPrice'][$k]>=0){
			$productItem=$this->kclass->DB->queryFirst("
				SELECT pi.productid,pi.attributevalue,
							p.title
				FROM productitem  AS pi
				LEFT JOIN product  AS p ON (p.productid=pi.productid)
				WHERE pi.productitemid='".$this->kclass->input['itemProductid'][$k]."'
			");
		$this->kclass->DB->query("
				INSERT INTO `item`(`module`,`mid`,`productid`,`productitemid`,`productAttributes`,`caption`,`brandid`,`packingid`,`quantity`,`discount`,`unitid`,`price`,`amount`,`modified`,`creator`,`created`)
				VALUES('stquoting','".$quotingid."','".$productItem['productid']."','".$this->kclass->input['itemProductid'][$k]."','".$productItem['attributevalue']."','".$this->kclass->input['itemCaption'][$k]."','".$this->kclass->input['itemBrandid'][$k]."','".$this->kclass->input['itemPackingid'][$k]."','".$this->kclass->input['itemQuantity'][$k]."','".$this->kclass->input['itemDiscount'][$k]."','".$this->kclass->input['itemUnitid'][$k]."','".$this->kclass->input['itemPrice'][$k]."','".$amount."','".TIMENOW."','".$this->kclass->user['userid']."','".TIMENOW."')
		");
		}
	}
	//配件明细
	for($k=1;$k<(count($this->kclass->input['itemPartsid'])+1);$k++){
		$this->kclass->input['itemPartsQuantity'][$k]=intval($this->kclass->input['itemPartsQuantity'][$k]);
		$this->kclass->input['itemPartsPrice'][$k]=floatval($this->kclass->input['itemPartsPrice'][$k]);
		$this->kclass->input['itemPartsDiscount'][$k]=intval($this->kclass->input['itemPartsDiscount'][$k]);
		if($this->kclass->input['itemPartsDiscount'][$k]<0 OR $this->kclass->input['itemPartsDiscount'][$k]>100){
			$this->kclass->input['itemPartsDiscount'][$k]=100;
		}
		$amount=$this->kclass->input['itemPartsQuantity'][$k]*$this->kclass->input['itemPartsPrice'][$k];
		if($this->kclass->input['itemPrtsDiscount'][$k]<100){
			$amount=$amount*$this->kclass->input['itemPartsDiscount'][$k]/100;
		}
		$amount=number_format($amount,2,'.','');
		if($this->kclass->input['itemPartsKill'][$k]=='' AND $this->kclass->input['itemPartsid'][$k]>0 AND $this->kclass->input['itemPartsQuantity'][$k]!='' AND $this->kclass->input['itemPartsUnitid'][$k]>0 AND $this->kclass->input['itemPartsPrice'][$k]>=0){
			$part=$this->kclass->DB->queryFirst("
				SELECT p.materialid,
					m.materialno
				FROM product AS p
				LEFT JOIN material AS m ON (m.materialid=p.materialid)
				WHERE productid='".$this->kclass->input['itemPartsid'][$k]."';
			");
				
			$this->kclass->DB->query("
					INSERT INTO `item` (`module`,`mid`,`materialid`,`productid`,`caption`,`brandid`,`packingid`,`quantity`,`discount`,`unitid`,`price`,`amount`,`modified`,`creator`,`created`)
					VALUES ('stquoting','".$quotingid."','".$part['materialid']."','".$this->kclass->input['itemPartsid'][$k]."','".$this->kclass->input['itemPartsCaption'][$k]."','".$this->kclass->input['itemPartsBrandid'][$k]."','".$this->kclass->input['itemPartsPackingid'][$k]."','".$this->kclass->input['itemPartsQuantity'][$k]."','".$this->kclass->input['itemPartsDiscount'][$k]."','".$this->kclass->input['itemPartsUnitid'][$k]."','".$this->kclass->input['itemPartsPrice'][$k]."','".$amount."','".TIMENOW."','".$this->kclass->user['userid']."','".TIMENOW."')
				");
	}
	}
	$this->kclass->messager(array(
		'title'=>$title,
		'text'=>'标准报价单已经生成',
		'url'=>'http://erp/s.php?module=customer&action=STQuoting',
		'sec'=>2
		));
}
//
function STQuotingfind(){
	//print_r($this->kclass->input);
	
	$this->kclass->input['id']=intval($this->kclass->input['QuotingID']);
	if($this->kclass->input['id']>0){
		$STQuoting=$this->kclass->DB->queryFirst("SELECT quotingid FROM `stquoting` WHERE `stquoting`.quotingid='".$this->kclass->input['QuotingID']."'");
		 if($STQuoting){
			   $this->kclass->boinkIt('/s.php?module=customer&action=STQuotingview&STQuotingid='.$STQuoting['quotingid'].'');
		   }
		}
		 if($this->kclass->input['show']=='default'){
		   $condition='`stquoting`.killed=0 AND `stquoting`.quotingid>2';
	   }else{
		   $condition=' `stquoting`.quotingid>2';
	   }
	   $orderby=$this->kclass->input['direction'];
	   $this->kclass->input['Quotingname']=trim($this->kclass->input['Quotingname']);
	   $this->kclass->input['Quotingtype']=trim($this->kclass->input['Quotingtype']);
	   $this->kclass->input['Quotingport']=trim($this->kclass->input['Quotingport']);
	   $this->kclass->input['Quotingcurrency']=trim($this->kclass->input['Quotingcurrency']);
	   if($this->kcalss->input['Quptingname']!=''){
		   $condition="AND INSTR(LCASE(`stquoting`.cntitle),".(strtolower($this->kclass->input['Quotingname'])).")>0";
		   $query['Quotingname']=$this->kclass->input['Quotingname'];
	   }
	   if($this->kclass->input['typeid']!=''){
		   $condition.=" AND  `stquoting`.typeid=".$this->kclass->input['typeid']."";
		   $query['Quotingtype']=$this->kclass->input['Quotingtype'];
	   }
      if($this->kclass->input['Quotingport']!=''){
			$condition.="AND `stquoting`.portid=".$thiss->kclass->input['Quotingport']."";
			$query['Quotingport']=$this->kclass->input['Quotingport'];
	  }
	  if($this->kclass->input['Quotingcurrency']!=''){
		  $condition.="AND `stquoting`.currencyid=".$this->kclass->input['Quotingcurrency']."";
		  $query['Quotingcurrency']=$this->kclass->input['Quotingcurrency'];
	  }
	  
	  if($this->kclass->input['ordertype']!='')$query['ordertype']=$this->kclass->input['ordertype'];
	  if($this->kclass->input['direction']!='')$query['direction']=$this->kclass->input['direction'];
	  if($this->kclass->input['show']!='')$query['show']=$this->kclass->input['show'];
	//$query['layout']=$this->kclass->iif($thiss->kclass->input['layout']!='',$this->kclass->input['layout'],$this->kclass->input['layout']='list');
    //$orderby=$this->kclass->orderby(
			//	array('module'=>'stquoting','direction'=>'asc','orderby'=>'stquoting.modified', 'default'=>'name', 'serial'=>array(
				//	array('title'=>'名称', 'field'=>'cntitle','word'=>'name'),
					//	array('title'=>'ID', 'field'=>'quotingid','word'=>'id'), 
					//array('title'=>'修改时间', 'field'=>'modified'), 
					//array('title'=>'建立时间', 'field'=>'created'),
					//array('title'=>'支持地区', 'field'=>'port')
						//	),
				//	'appendUrl'=>$query)
			//);
	 if(is_array($query)AND count($query)>0)$queryPart='&'.http_build_query($query);
      if($this->kclass->input['ordertype']=='title'){
		  $ordertype="`stquoting`.cntitle";
	  }elseif($this->kclass->input['ordertype']==' ID'){
		  $ordertype="`stquoting`.quotingid";
	  }elseif($this->kclass->input['ordertype']=='created'){
		  $ordertype="`stquoting`.created";
	  }elseif($this->kclass->input['ordertype']=='modified'){
		  $ordertype="`stquoting`.modified";
	  }
	  $sql="SELECT `stquoting`.*,
				`port`.title AS ptitle,`port`.entitle AS pentitle,
				`quotingtype`.title AS qtitle,
				`currency`.title AS ctitle,
				m.realname AS mname ,b.realname AS bname,p.realname AS pname,d.title AS dtitle
				FROM `stquoting`
				LEFT JOIN `port` ON(`port`.portid=`stquoting`.portid)
				LEFT JOIN `quotingtype` ON(`quotingtype`.typeid=`stquoting`.typeid)
				LEFT JOIN `currency` ON(`currency`.currencyid=`stquoting`.currencyid)
				LEFT JOIN `member` AS m ON (M.userid=`stquoting`.verifier)
				LEFT JOIN `member`	 AS b ON(b.userid=`stquoting`.creator)
				LEFT JOIN `member`	AS p ON (p.userid=`stquoting`.modifier)
				LEFT JOIN `deliveryterm` AS d ON (d.deliverytermid=`stquoting`.deliverytermid)
				WHERE ".$condition." ORDER BY ".$ordertype ." ".$orderby." ";
	$stquotings=$this->kclass->DB->query($sql);
	if($sum=$this->kclass->DB->numRows()){
		$i=1;
		$body.=$this->kclass->input['layout']=='list'?'<table class="hundred mytable tablesorter"><thead><th>编号</th><th>报价单名称</th><th>报价单类型</th><th>审核</th><th>支持地区</th><th>支持币种</th><th>创建人</th><th>审核人</th><th>操作</th></thead><tbody>':'<ul id="mmlist" class="mmlist clear">';
		while($stquoting=$this->kclass->DB->fetchArray($stquotings)){
			$tempStr =  '';	
			$stquoting['cntitle']=$this->kclass->parseConvertValue($stquoting['cntitle']);
			$title=$this->kclass->iif(strlen($stquoting['cntitle'])>30,mb_substr($stquoting['cntitle'],0,30,'UTF-8').'…',$stquoting['cntitle']).'('.$this->kclass->iif(strlen($stquoting['entitle'])>30,mb_substr($stquoting['entitle'],0,30,'UTF-8').'...',$stquoting['entitle']).')';
            //verify standrand quoting information
			$doverify=$this->kclass->doverify(array('module'=>'customer','action'=>'verify'));
            if($doverify){
		       $action='(<a href="/s.php?module=customer&action=verify&quotingid='.$stquoting['quotingid'].'">审核</a>)';
	        }
	         $operator = $creator = '由 '.$stquoting['bname'].' 建于 '.date('y-m-d H:i', $stquoting['created']);
				if($customer['modifier']!=''){
					$modifier = $stquoting['pname'].' 改于 '.date('Y-m-d H:i', $stquoting['modified']);
					$operator .= ', '.$modifier;
					$modifier = '由 '.$modifier;
				}else{
					$modifier = $creator;
				}
			        $status="";
					if($stquoting['ifverify']==0){
						$status='<span class="darkred">未审核</span>'.$action;
					}elseif($stquoting['ifverify']==-1){
						$status='<span class="red">审核未通过</span>';
					}elseif($stquoting['ifverify']==1){
						$status='<span class="green">审核通过</span>';
					}
				if($stquoting['ifverify']==1){
					$verifier='(由'.$stquoting['mname'].'于'.date('Y-i-d H:m:s',$stquoting['verified']).'审核)';
				}elseif($stquoting['ifverify']==-1){
					$verifier="<span class='red'>审核未通过</span>";
				}elseif($stquoting['ifverify']==0){
					$verifier="<span class='darkred'>未审核</span>";
				}
				$link = '<a href="/s.php?module=customer&action=STQuotingupdate&quotingid='.$stquoting['quotingid'].'&rt=view">修改</a><br><a href="/p.php?action=stquoting&customerid='.$customer['customerid'].'" target="_blank">打印</a>';
			if($this->kclass->input['layout']=='list'){//表格
				$body.="<tr class='odd'>
				 <td>".$i."</td><td><a href=\"/s.php?module=customer&action=STQuotingview&STQuotingid=".$stquoting['quotingid']."\">".$title."</a></td><td>".$stquoting['qtitle']."</td><td>".$status."</td><td>".$stquoting['ptitle']."(".$stquoting['pentitle'].")</a></td><td>".$stquoting['ctitle']."</td>
				 <td>".$stquoting['uname']."(".date('Y-i-d H :m:s',$stquoting['created']).")</td><td class=\"".$this->kclass->iif($stquoting['ifverify']==1,'green','red')."\">".$verifier."</td><td><a href=\"/s.php?module=customer&action=STQuotingremove&STQuotingid=".$stquoting['quotingid']."\">删除</a>|打印</td>
				</tr>";
				
			}else{//方块列表
				$body .= '<li class="customerli"><a name="'.$stquoting['quotingid'].'"></a>
					<div title="'.$operator.'" class="mmlistt clear">
						<span class="right normal">'.$link.'</span>
						<a href="/s.php?module=customer&action=STQuotingview&STQuotingid='.$stquoting['quotingid'].'">'.$stquoting['quotingid'].' <span class="normal" title="'.$stquoting['title'].'">'.$title.'</span></a><br>
						<span class="small">'.$stquoting['businessTitle'].'</span> '.$this->kclass->iif($stquoting['attachs']>0, ' <span class="attachFile" title="有'.$stquoting['attachs'].'个附件。"></span> ', '').$this->kclass->iif($stquoting['images']>0, ' <span class="attachImage" title="有'.$stquoting['images'].'个图片。"></span> ', '').'<span class="normal">'.$this->kclass->iif(strlen($$stquoting['cntitle'])>12, mb_substr($stquoting['cntitle'], 0, 12, 'UTF-8').'...', $stquoting['cntitle']).'</span></div>
					<div class="mmlistb">
						<div class="normal bold">报价单类型：<span class="small">('.$stquoting['qtitle'].')</span></div>
						<div><span class="small gray">审核：</span>'.$verifier.'</div>
						<div><span class="small gray">支持地区:</span>'.$stquoting['ptitle'].'</div>';
				
					$body.='
						<div><span class="small gray">支持币种：</span>'.$stquoting['ctitle'].'</div>
						<div><span class="small gray">交货条款：</span><span class="red">'.$stquoting['dtitle'].'</span></div>
						<div class="small clear"><span class="right" title="'.$creator.'">'.$modifier.'</span></div>
					</div>
					</li>';
			}
			$i++;
		}
		$body.=$this->kclass->input['layout']=='list'?'</tbody></table>':'</ul>';
	}else{
		$body=<<<EOF
		暂无对应产品
EOF;
		}
      if($this->kclass->input['layout']=='list'){
		 $layoutLink='列表<a href="/s.php?module=customer&action=STQuotingfind&layout=grid'.str_replace('&layout=list','',$queryPart).'">格子</a>';
	  }else{
		  $layoutLink='<a href="/s.php?module=customer&action=STQuotingfind&layout=list'.str_replace('&layout=grid','',$queryPart).'">列表</a>格子';
		
	  }
		if($this->kclass->input['show']=='all'){
			$showLink='<a href="/s.php?module=customer&action=STQuotingfind'.str_replace('&show=all','', $queryPart).'">默认</a> 所有<span class="small gray">(包括删除)</span>';
		}else{
			$showLink='默认 <a href="/s.php?module=customer&action=STQuotingfind&show=all'.str_replace('&show=all', '', $queryPart).'">所有<span class="small gray">(包括删除)</span></a>';
		}


$body .= "
	<script type=\"text/javascript\">
			$('.tablesorter').tablesorter( {headers:{2:{sorter:false},3:{sorter:false},8:{sorter:false},10: {sorter: false} }});$('.mytable').fixedtableheader();
		</script>		
	";
$this->kclass->page['onload'].="dc.listhover();dc.tabhover();dc.tabs({'id':'stquoting'})";
$this->kclass->page['title'].='-列表';
$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.'<span class="blod">-报价单</span>列表','right'=>$this->right.'|<span class="small">显示：</span>'.$showLink.'|<span class="small">布局：</span>'.$layoutLink.'|<a href="">新建</a>','body'=>$body));
}
//
function newQuoting(){
	$item=$this->kclass->STquotingorder(array('action'=>'add'));
	//print_r($this->kclass->input);
	if($this->kclass->input['customerid']<=0){
		$this->kclass->boinkIt('/s.php?moudle=customer');
	}
	$customer=$this->kclass->DB->queryFirst("SELECT title,cntitle ,ifverify FROM `customer` WHERE	customerid=".$this->kclass->input['customerid']."");
	if($customer['cntitle']!=''){
		$title=$customer['title'].'('.$customer['cntitle'].')';
	}else{
		$title=$customer['title'];
	}
	if($customer['ifverify']!=1){
		$e='<li>该客户未审核或审核未通过</li>';
	}
	if(isset($e)){
			$this->kclass->messager(array(
				'title' => $title,
				'text' => '您在添加客户报价单的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
	}
$body=<<<EOF
<form action="/s.php?module=customer&action=addQuoting" method="post">
<input type="hidden" name="customerid" value="{$this->kclass->input['customerid']}">
<input type="hidden" name="inquiryid" value="{$this->kclass->input['inquiryid']}">
	<table class="hundred">
		<thead>
		<tr>
			<th>询价客户：{$title}</th>
		</tr>
		</thead>
		<tbody>
		<tr>
			<td colspan=4 align="center">
				新建报价单：<input type="radio" name="chooseQuoting" value="1" onclick="toggle()"/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				标准报价单：<input type="radio" name="chooseQuoting" value="2" onclick="toggle()" checked />
			</td>
		</tbody>
		</table>
		<div  id="hidden">
		<table class="hundred" >
		<thead>
		<tr>
		<th>ID</th><th>名称</th><th>类型</th><th>支持地区</th><th>支持币种</th><th>创建人</th><th>操作</th>
		</tr>
		</thead>
		<tbody>
			{$item}
		</tbody>
		</table>
		</div>
		<table class="hundred">
		<tbody>
		<tr>
			<td colspan=7 align="center">
			<input type="submit" name="submit" value="提交"/>
			</td>
		</tr>
		</tbody>
	</table>
</form>
EOF;
$this->kclass->page['title'].='-'.$customer['title'].'-客户报价单';
$this->kclass->page['onload'].='dc.tabhover();$(\'#dateline\').datepicker()';
$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.'-<a href="/s.php?module=customer&action=view&customerid='.$customer['customerid'].'">'.$customer['title'].'</a>-客户报价单','right'=>'<a href="/s.php?module=customer">返回列表</a>','body'=>$body));
}
	//
	function addQuoting(){
		if($this->kclass->input['customerid']<=0){
			$this->kclass->boinkIt('/s.php?module=customer');
		}
		if($this->kclass->input['inquiryid']>0){
			$dateline=date('Y-m-d',$inquiry ['dateline']);
		}else{
			$dateline=date('Y-m-d');
		}
			$customer=$this->kclass->DB->queryFirst("SELECT customerid, title, linkman, position FROM `customer` WHERE killed=0 AND customerid='".$this->kclass->input['customerid']."'");
		$currency = $this->kclass->chooserCurrency(array('name'=>'currencyid', 'hasBlank'=>1, 'width'=>100, 'selectedid'=>1));
		$packing = $this->kclass->chooserPacking(array('name'=>'packingid', 'hasBlank'=>1, 'width'=>140, 'selectedid'=>$inquiry['packingid']));
		$shipmethod = $this->kclass->chooserShipmethod(array('name'=>'shipmethodid', 'hasBlank'=>1, 'width'=>450, 'selectedid'=>$inquiry['shipmethodid']));
		$paymentterm = $this->kclass->chooserPaymentterm(array('name'=>'paymenttermid', 'hasBlank'=>1, 'width'=>450, 'selectedid'=>$inquiry['paymenttermid']));
		$deliveryterm = $this->kclass->chooserDeliveryterm(array('name'=>'deliverytermid', 'hasBlank'=>1, 'width'=>450, 'selectedid'=>$inquiry['deliverytermid']));
		$port = $this->kclass->chooserPort(array('name'=>'portid', 'hasBlank'=>1, 'width'=>450, 'selectedid'=>$inquiry['portid']));
		$flow=$this->kclass->listFlow(array('module'=>'STQuoting','statusid'=>1));
		if($this->kclass->input['inquiryid']>0){
			$titles = '回复客户的询价 & 新建报价单';
			$dateline=date('Y-m-d',$inquiry['dateline']);
			$inquiry = $this->kclass->DB->queryFirst("SELECT * FROM inquiry WHERE inquiryid='".$this->kclass->input['inquiryid']."'");
			$items = $this->kclass->DB->query("SELECT * FROM `item` WHERE killed=0 AND module='inquiry' AND mid='".$inquiry['inquiryid']."' ORDER BY modified ASC");
			if($this->kclass->DB->numRows()){
				$k=1;
				$n=1;
				while($item = $this->kclass->DB->fetchArray($items)){
					if($item['productitemid']>0){
						$product = $this->kclass->DB->queryFirst("
							SELECT `product`.title,`product`.entitle
							FROM  `productitem`
							LEFT JOIN `product` ON (`productitem`.productid = `product`.productid)
							WHERE productitemid='".$item['productitemid']."'
						");
						$seletStr = '<input type="hidden" id="tditemProductid'.$k.'input" value="'.$product['entitle'].'" />';
						$itemtr .= '<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').'">
						<td><span id="tditemProductid'.$k.'">'.$seletStr.'</span><input type="hidden" name="itemId['.$k.']" value="'.$item['itemid'].'">'.$this->kclass->chooserProductItem(array('name'=>'itemProductid['.$k.']','selectName'=>'itemProductid'.$k, 'selectedid'=>$item['productitemid'], 'hasBlank'=>1, 'width'=>500)).'</td>
						<td><input type="text" name="itemCaption['.$k.']" value="'.$item['caption'].'" size="65"></td>
						<td>'.$this->kclass->chooserBrand(array('name'=>'itemBrandid['.$k.']', 'hasBlank'=>1, 'width'=>80, 'selectedid'=>$item['brandid'])).'</td>
						<td>'.$this->kclass->chooserPacking(array('name'=>'itemPackingid['.$k.']', 'hasBlank'=>1, 'width'=>100, 'selectedid'=>$item['packingid'])).'</td>
						<td><input type="text" name="itemDiscount['.$k.']" value="'.$item['discount'].'" size="3"></td>
						<td><input type="text" name="itemQuantity['.$k.']" value="'.$item['quantity'].'" size="3" onblur="dc.returnProductStr('.$k.',this.value)"></td>
						<td>'.$this->kclass->chooserUnit(array('name'=>'itemUnitid['.$k.']', 'hasBlank'=>1, 'width'=>80, 'selectedid'=>$item['unitid'])).'</td>
						<td><input type="text" name="itemPrice['.$k.']" value="'.$item['price'].'" size="5"></td>
						<td><input type="checkbox" name="itemKill['.$k.']" value="'.$item['itemid'].'"></td>
						</tr>';
						$k++;
					}else{
						$parts .= '<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').'">
						<td><input type="hidden" name="itemPartsId['.$n.']" value="'.$item['itemid'].'">'.$this->kclass->chooserParts(array('name'=>'itemPartsid['.$n.']', 'selectedid'=>$item['productid'], 'hasBlank'=>1, 'width'=>500)).'</td>
						<td><input type="text" name="itemPartsCaption['.$n.']" value="'.$item['caption'].'" size="65"></td>
						<td>'.$this->kclass->chooserBrand(array('name'=>'itemPartsBrandid['.$n.']', 'hasBlank'=>1, 'width'=>80, 'selectedid'=>$item['brandid'])).'</td>
						<td>'.$this->kclass->chooserPacking(array('name'=>'itemPartsPackingid['.$n.']', 'hasBlank'=>1, 'width'=>100, 'selectedid'=>$item['packingid'])).'</td>
						<td><input type="text" name="itemPartsDiscount['.$n.']" value="'.$item['discount'].'" size="3"></td>
						<td><input type="text" name="itemPartsQuantity['.$n.']" value="'.$item['quantity'].'" size="3"></td>
						<td>'.$this->kclass->chooserUnit(array('name'=>'itemPartsUnitid['.$n.']', 'hasBlank'=>1, 'width'=>80, 'selectedid'=>$item['unitid'])).'</td>
						<td><input type="text" name="itemPartsPrice['.$n.']" value="'.$item['price'].'" size="5"></td>
						<td><input type="checkbox" name="itemPartsKill['.$n.']" value="'.$item['itemid'].'"></td>
						</tr>';
						$n++;
					}
				}
			}
			for($i = $k; $i < ($k+5); $i++){
				$itemtrs .= '<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').'">
				<td><span id="tditemProductid'.$i.'"></span>'.$this->kclass->chooserProductItem(array('name'=>'itemProductid['.$i.']','selectName'=>'itemProductid'.$i, 'hasBlank'=>1, 'width'=>500)).'</td>
				<td><input type="text" name="itemCaption['.$i.']" value="'.$item['caption'].'"  size="65"></td>
				<td>'.$this->kclass->chooserBrand(array('name'=>'itemBrandid['.$i.']', 'hasBlank'=>1, 'width'=>80,'selectedid'=>1)).'</td>
				<td>'.$this->kclass->chooserPacking(array('name'=>'itemPackingid['.$i.']', 'hasBlank'=>1, 'width'=>100,'selectedid'=>6)).'</td>
				<td><input type="text" name="itemDiscount['.$i.']" value="100" size="3"></td>
				<td><input type="text" name="itemQuantity['.$i.']" value="'.$item['quantity'].'" size="3"  onblur="dc.returnProductStr('.$i.',this.value)"></td>
				<td>'.$this->kclass->chooserUnit(array('name'=>'itemUnitid['.$i.']', 'hasBlank'=>1, 'width'=>80, 'selectedid'=>5)).'</td>
				<td><input type="text" name="itemPrice['.$i.']" value="'.$item['price'].'" size="5"></td>
				<td><input type="checkbox" name="itemPartsKill['.$i.']"	value="'.$item['itemid'].'"/></td>
				</tr>';
			}
			for($i = $n; $i < ($n+5); $i++){
				$partStr .= '<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').'">
				<td>'.$this->kclass->chooserParts(array('name'=>'itemPartsid['.$i.']', 'hasBlank'=>1, 'width'=>500)).'</td>
				<td><input type="text" name="itemPartsCaption['.$i.']" value="'.$item['caption'].'"  size="65"></td>
				<td>'.$this->kclass->chooserBrand(array('name'=>'itemPartsBrandid['.$i.']', 'hasBlank'=>1, 'width'=>80,'selectedid'=>1)).'</td>
				<td>'.$this->kclass->chooserPacking(array('name'=>'itemPartsPackingid['.$i.']', 'hasBlank'=>1, 'width'=>100,'selectedid'=>6)).'</td>
				<td><input type="text" name="itemPartsDiscount['.$i.']" value="100" size="3"></td>
				<td><input type="text" name="itemPartsQuantity['.$i.']" value="'.$item['quantity'].'" size="3"></td>
				<td>'.$this->kclass->chooserUnit(array('name'=>'itemPartsUnitid['.$i.']', 'hasBlank'=>1, 'width'=>80, 'selectedid'=>5)).'</td>
				<td><input type="text" name="itemPartsPrice['.$i.']" value="'.$item['price'].'" size="5"></td>
				<td><input type="checkbox" name="itemPartsKill['.$i.']"	value="'.$item['itemid'].'"/></td>
				</tr>';
			}
			$dateline=date('Y-m-d');
$order= <<<EOF
<tbody>
<tr class="odd">
	<td title="Contact">联 系 人：<span class="red bold">*</span></td>
	<td><input type="text" style="width:250px" name="contact" value="{$customer['position']} {$customer['linkman']}"> <span class="small gray">指客户方询价人员</span></td>
	<td title="Currency">币　　种：</td>
	<td>{$currency}　交货日期：<span class="bold red">*</span> <input type="text" name="dateline" id="dateline" size="10" value="{$dateline}" ></td>
</tr>
<tr class="odd">
	<td title="Shipment Method">运送方式：</td>
	<td>{$shipmethod}</td>
	<td title="Payment Term">支付条款：</td>
	<td>{$paymentterm}</td>
</tr>
<tr class="even">
	<td title="Port">交货地点：</td>
	<td>{$port}</td>
	<td title="Delivery Term">交货条款：</td>
	<td>{$deliveryterm}　｜　运费：<input type="text" name="freight"/></td>
</tr>
</tbody>
</table>
<table class="hundred">
<thead>
<tr>
	<th colspan="10">产品明细表</th>
</tr>
</thead>
<tr><td>产品选择<span class="red bold">*</span></td><td width="300">说明</td><td width="100">品牌</td><td width="50">包装</td><td width="30">折扣</td><td width="30">数量<span class="red bold">*</span></td><td width="80">单位<span class="red bold">*</span></td><td width="50">单价<span class="red bold">*</span></td><td  width="20">删</td></tr>
{$itemtr}
<tr><td>配件选择<span class="red bold">*</span></td><td width="300">说明</td><td width="100">品牌</td><td width="50">包装</td><td width="30">折扣</td><td width="30">数量<span class="red bold">*</span></td><td width="80">单位<span class="red bold">*</span></td><td width="50">单价<span class="red bold">*</span></td><td  width="20">删</td></tr>
{$parts}
<tr>
	<th colspan="12">新建明细</th>
</tr>
<tr><td>产品选择<span class="red bold">*</span></td><td width="300">说明</td><td width="100">品牌</td><td width="50">包装</td><td width="30">折扣</td><td width="30">数量<span class="red bold">*</span></td><td width="80">单位<span class="red bold">*</span></td><td width="50">单价<span class="red bold">*</span></td><td  width="20">删除</td></tr>
{$itemtrs}
<tr><td>配件选择<span class="red bold">*</span></td><td width="300">说明</td><td width="100">品牌</td><td width="50">包装</td><td width="30">折扣</td><td width="30">数量<span class="red bold">*</span></td><td width="80">单位<span class="red bold">*</span></td><td width="50">单价<span class="red bold">*</span></td><td  width="20">删除</td></tr>
{$partStr}
<tr>
<tr class="even">
	<td colspan="10" class="gray small">注：①如行数不够，请保存后再进入修改页面。②至少有“产品”、“数量”、“单位”三个项目选择填写，该明细才会被保存。③“折扣”请填写百分比“%”。</td>
</tr>
</tbody>
<table class="hundred">
<tbody>
<tr class="odd">
	<td valign="top" title="Standard">规格说明：</td>
	<td><textarea name="standard" style="width:450px;height:60px"></textarea></td>
	<td valign="top" title="Material">主要材料：</td>
	<td><textarea name="material" style="width:450px;height:60px"></textarea></td>
</tr>
<tr class="even">
	<td valign="top" title="Technics">工艺说明：</td>
	<td><textarea name="technics" style="width:450px;height:60px"></textarea></td>
	<td valign="top" title="Remark">备　　注：</td>
	<td><textarea name="remark" style="width:450px;height:60px"></textarea></td>
</tr>

EOF;
		}else{
			$titles = '回复客户的报价单';
			if($this->kclass->input['chooseQuoting']==1){
				$titles = '新建发往客户的报价单';
				$customer=$this->kclass->DB->queryFirst("SELECT customerid, title, linkman, position FROM `customer` WHERE killed=0 AND customerid='".$this->kclass->input['customerid']."'");
			$currency = $this->kclass->chooserCurrency(array('name'=>'currencyid', 'hasBlank'=>1, 'width'=>100, 'selectedid'=>1));
		$packing = $this->kclass->chooserPacking(array('name'=>'packingid', 'hasBlank'=>1, 'width'=>140, 'selectedid'=>1));
		$shipmethod = $this->kclass->chooserShipmethod(array('name'=>'shipmethodid', 'hasBlank'=>1, 'width'=>450, 'selectedid'=>1));
		$paymentterm = $this->kclass->chooserPaymentterm(array('name'=>'paymenttermid', 'hasBlank'=>1, 'width'=>450, 'selectedid'=>1));
		$deliveryterm = $this->kclass->chooserDeliveryterm(array('name'=>'deliverytermid', 'hasBlank'=>1, 'width'=>450, 'selectedid'=>1));
		$port = $this->kclass->chooserPort(array('name'=>'portid', 'hasBlank'=>1, 'width'=>450, 'selectedid'=>1));
		$quotingtype=$this->kclass->chooserQuotingtype(array('name'=>'typeid','hasBlank'=>1,'width'=>100,'selectedid'=>1));
		$contact=$customer['position'];
			//$seletStr = '<input type="hidden" id="tditemProductid'.$k.'input" value="'.$product['entitle'].'" >';
			for($i=1;$i<5;$i++){
			$itemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').'" id="producttr'.$i.'">
			<td><span id="tditemProductid'.$i.'"></span>'.$this->kclass->chooserProductItem(array('name'=>'itemProductid['.$i.']','selectName'=>'itemProductid'.$i,'hasBlank'=>1,'width'=>500)).'</td>
			<td><input type="text" name="itemCaption['.$i.']" size="65"/></td>
			<td>'.$this->kclass->chooserBrand(array('name'=>'itemBrandid['.$i.']','hasBlank'=>1,'width'=>100,'selectedid'=>1)).'</td>
			<td>'.$this->kclass->chooserPacking(array('name'=>'itemPackingid['.$i.']','hasBlank'=>1,'width'=>100,'selectedid'=>6)).'</td>
			<td><input type="text" name="itemDiscount['.$i.']" value="100" size="3"/></td>
			<td><input type="text" name="itemQuantity['.$i.']" size="3" onblur="dc.returnProductStr('.$i.',this.value)"></td>
           <td>'.$this->kclass->chooserUnit(array('name'=>'itemUnitid['.$k.']', 'hasBlank'=>1, 'width'=>80, 'selectedid'=>5)).'</td>
			<td><input type="text" name="itemPrice['.$i.']"  size="5"></td>
			<td><input type="checkbox" name="itemKill['.$i.']" ></td>
			</tr>';
			}
			$itemtr.='<tr class="odd bold gray normal" id="itemProduct" nohover><td colspan=9><span class="hand" onclick="dc.addLine(\'itemProduct\',\'itemProduct\')" style="border:2px solid gray;background:#FFE8EB">增加一行配件明细</span></td></tr>';
			for($i=1;$i<5;$i++){
				$parts.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').'" id="producttr'.$i.'">
				<td>'.$this->kclass->chooserParts(array('name'=>'itemPartsid['.$i.']','hasBlank'=>1,'width'=>500)).'</td>
				<td><input type="text" name="itemPartsCaption['.$i.']" "size="65"/></td>
				<td>'.$this->kclass->chooserBrand(array('name'=>'itemPartsBrandid['.$i.']','hasBlank'=>1,'width'=>100,'selectedid'=>1)).'</td>
				<td>'.$this->kclass->chooserPacking(array('name'=>'itemPartsPackingid['.$i.']','hasBlank'=>1,'width'=>100,'selectedid'=>6)).'</td>
				<td><input type="text" name="itemPartsDiscount['.$i.']" value="100"size="3"/></td>
				<td><input type="text" name="itemPartsQuantity['.$i.']" value="'.$item['quantity'].'"size="3"/></td>
				<td>'.$this->kclass->chooserUnit(array('name'=>'itemPartsUnitid['.$i.']','hasBlank'=>1,'width'=>80,'selectedid'=>5)).'</td>
				<td><input type="text"	name="itemPatrsPrice['.$i.']"	value="'.$item['price'].'"	size=5/></td>
				<td><input type="checkbox" name="itemPartsKill['.$i.']"	value="'.$item['itemid'].'"/></td>
				</tr>';
			}
				 $order=<<<EOF
<tr class="odd">
	<td title="Contact" >联 系 人：<span class="red bold">*</span></td>
	<td><input type="text" style="width:250px" name="contact" value="{$customer['position']} {$customer['linkman']}" /> <span class="small gray">指客户方询价人员</span></td>
	<td title="Currency">币　　种：</td>
	<td>{$currency}</td>
</tr>
		<tr class="even">
<td title="type">报价单类型：</td><td>{$quotingtype}</td>
<td title="dateline">交货日期<span class="bold red">*</span>:</td><td><input type="text" name="dateline" id="dateline" size="10" value="{$dateline}" ></td>
</tr>
<tr class="odd">
	<td title="Port">交货地点：</td>
	<td>{$port}</td>
	<td title="Delivery Term">交货条款：</td>
	<td>{$deliveryterm}　｜　运费：<input type="text" name="freight"/></td>
</tr>
<tr class="even">
	<td title="Shipment Method">运送方式：</td>
	<td>{$shipmethod}</td>
	<td title="Payment Term">支付条款：</td>
	<td>{$paymentterm}</td>
</tr>
</tbody>
</table>
<table class="hundred">
<thead>
<tr>
	<th colspan="10">产品明细表</th>
</tr>
</thead>
<tr><td>产品选择<span class="red bold">*</span></td><td width="300">说明</td><td width="100">品牌</td><td width="50">包装</td><td width="30">折扣</td><td width="30">数量<span class="red bold">*</span></td><td width="80">单位<span class="red bold">*</span></td><td width="50">单价<span class="red bold">*</span></td><td  width="20">删</td></tr>
{$itemtr}
<tr><td>配件选择<span class="red bold">*</span></td><td width="300">说明</td><td width="100">品牌</td><td width="50">包装</td><td width="30">折扣</td><td width="30">数量<span class="red bold">*</span></td><td width="80">单位<span class="red bold">*</span></td><td width="50">单价<span class="red bold">*</span></td><td  width="20">删</td></tr>
{$parts}
<tr class="even">
	<td colspan="10" class="gray small">注：①如行数不够，请保存后再进入修改页面。②至少有“产品”、“数量”、“单位”三个项目选择填写，该明细才会被保存。③“折扣”请填写百分比“%”。</td>
</tr>
</tbody>
<table class="hundred">
<tbody>
<tr class="odd">
	<td valign="top" title="Standard">规格说明：</td>
	<td><textarea name="standard" style="width:450px;height:60px" disabled = true">{$standrad}</textarea></td>
	<td valign="top" title="Material">主要材料：</td>
	<td><textarea name="material" style="width:450px;height:60px" disabled = true">{$material}</textarea></td>
</tr>
<tr class="even">
	<td valign="top" title="Technics">工艺说明：</td>
	<td><textarea name="technics" style="width:450px;height:60px" disabled = true">{$technics}</textarea></td>
	<td valign="top" title="Remark">备　　注：</td>
	<td><textarea name="remark" style="width:450px;height:60px" disabled = true">{$remark}</textarea></td>
</tr>
EOF;
			}elseif($this->kclass->input['chooseQuoting']==2){
				//print_r($this->kclass->input);
				if($this->kclass->input['stquoting']==''){
				  $e='<li>请选择标准报价单</li>';
				}   
				if(isset($e)){
					$this->kclass->messager(array(
						'title'=>'添加失败',
						'text'=>'您在添加报价单过程中有一下错误<ul>'.$e.'</ul>',
						'url'=>'javascript:history.back()',
						'sec'=>3
						));
				}
				$item=$this->kclass->DB->queryFirst("
					SELECT  p.entitle AS pentitle,p.title AS ptitle, c.title AS ctitle,qt.title AS qttitle,u.username AS uname,
					st.cntitle AS stcntitle,st.entitle AS stentitle,st.freight AS stfreight,st.created AS stcreated,de.title AS  detitle,de.entitle AS deentitle,st.*
					FROM `stquoting` AS st
					LEFT JOIN `port` AS p ON ( p.portid=st.portid)
					LEFT JOIN `currency` AS c ON (c.currencyid=st.currencyid)
					LEFT JOIN `quotingtype` AS qt ON (qt.typeid=st.typeid)
					LEFT JOIN `user`			AS	u	ON  (u.userid=st.creator)
					LEFT JOIN `deliveryterm` AS de ON (de.deliverytermid=st.deliverytermid)
					WHERE st.killed=0 AND st.quotingid='".$this->kclass->input['stquoting']."'
				");
	      
				$port=$item['ptitle'].'('.$item['pentitle'].')';
				$deliveryterm=$item['detitle'].'('.$item['deentitle'].')';
				$standrad=$item['standrad'];
				$material=$item['material'];
				$technics=$item['technics'];
				$remark=$item['remark'];
				$type=$item['qttitle'];
				$freight=$item['stfreight'];
				$customer=$this->kclass->DB->queryFirst("SELECT customerid, title, linkman, position FROM `customer` WHERE killed=0 AND customerid='".$this->kclass->input['customerid']."'");
				$currency = $this->kclass->chooserCurrency(array('name'=>'currencyid', 'hasBlank'=>1, 'width'=>100, 'selectedid'=>$item['currencyid']));
		$packing = $this->kclass->chooserPacking(array('name'=>'packingid', 'hasBlank'=>1, 'width'=>140, 'selectedid'=>1));
		$shipmethod = $this->kclass->chooserShipmethod(array('name'=>'shipmethodid', 'hasBlank'=>1, 'width'=>450, 'selectedid'=>1));
		$paymentterm = $this->kclass->chooserPaymentterm(array('name'=>'paymenttermid', 'hasBlank'=>1, 'width'=>450, 'selectedid'=>1));
		$deliveryterm = $this->kclass->chooserDeliveryterm(array('name'=>'deliverytermid', 'hasBlank'=>1, 'width'=>450, 'selectedid'=>$item['deliverytermid']));
		$port = $this->kclass->chooserPort(array('name'=>'portid', 'hasBlank'=>1, 'width'=>450, 'selectedid'=>$item['portid']));
		$quotingtype=$this->kclass->chooserQuotingtype(array('name'=>'typeid','hasBlank'=>1,'width'=>100,'selectedid'=>$item['typeid']));
                $items=$this->kclass->DB->query("
		SELECT `item`.*,
					product.title AS product,
					material.standard,
					packing.title AS packing,
					unit.title			AS unit
		FROM `item`
		LEFT JOIN product ON (product.productid=`item`.productid)
		LEFT JOIN material ON (material.materialid=`item`.materialid)
		LEFT JOIN packing ON (packing.packingid=`item`.packingid)
		LEFT JOIN unit		ON (unit.unitid=`item`.unitid)
		WHERE `item`	.killed=0 AND `item`.module='stquoting' AND
		`item`.mid='".$this->kclass->input['stquoting']."'
		ORDER BY itemid ASC
	 ");
	 $itemtr='';
	 if($itemnum=$this->kclass->DB->numRows()){
				$k=1;
				$n=1;
				while($item = $this->kclass->DB->fetchArray($items)){
					if($item['productitemid']>0){
						$product = $this->kclass->DB->queryFirst("
							SELECT `product`.title,`product`.entitle
							FROM  `productitem`
							LEFT JOIN `product` ON (`productitem`.productid = `product`.productid)
							WHERE productitemid='".$item['productitemid']."'
						");
						$seletStr = '<input type="hidden" id="tditemProductid'.$k.'input" value="'.$product['entitle'].'" />';
						$itemtr .= '<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').'">
						<td><span id="tditemProductid'.$k.'">'.$seletStr.'</span><input type="hidden" name="itemId['.$k.']" value="'.$item['itemid'].'">'.$this->kclass->chooserProductItem(array('name'=>'itemProductid['.$k.']','selectName'=>'itemProductid'.$k, 'selectedid'=>$item['productitemid'], 'hasBlank'=>1, 'width'=>500)).'</td>
						<td><input type="text" name="itemCaption['.$k.']" value="'.$item['caption'].'" size="65"></td>
						<td>'.$this->kclass->chooserBrand(array('name'=>'itemBrandid['.$k.']', 'hasBlank'=>1, 'width'=>80, 'selectedid'=>$item['brandid'])).'</td>
						<td>'.$this->kclass->chooserPacking(array('name'=>'itemPackingid['.$k.']', 'hasBlank'=>1, 'width'=>100, 'selectedid'=>$item['packingid'])).'</td>
						<td><input type="text" name="itemDiscount['.$k.']" value="'.$item['discount'].'" size="3"></td>
						<td><input type="text" name="itemQuantity['.$k.']" value="'.$item['quantity'].'" size="3" onblur="dc.returnProductStr('.$k.',this.value)"></td>
						<td>'.$this->kclass->chooserUnit(array('name'=>'itemUnitid['.$k.']', 'hasBlank'=>1, 'width'=>80, 'selectedid'=>$item['unitid'])).'</td>
						<td><input type="text" name="itemPrice['.$k.']" value="'.$item['price'].'" size="5"></td>
						<td><input type="checkbox" name="itemKill['.$k.']" value="'.$item['itemid'].'"></td>
						</tr>';
						$k++;
					}else{
						$parts .= '<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').'">
						<td><input type="hidden" name="itemPartsId['.$n.']" value="'.$item['itemid'].'">'.$this->kclass->chooserParts(array('name'=>'itemPartsid['.$n.']', 'selectedid'=>$item['productid'], 'hasBlank'=>1, 'width'=>500)).'</td>
						<td><input type="text" name="itemPartsCaption['.$n.']" value="'.$item['caption'].'" size="65"></td>
						<td>'.$this->kclass->chooserBrand(array('name'=>'itemPartsBrandid['.$n.']', 'hasBlank'=>1, 'width'=>80, 'selectedid'=>$item['brandid'])).'</td>
						<td>'.$this->kclass->chooserPacking(array('name'=>'itemPartsPackingid['.$n.']', 'hasBlank'=>1, 'width'=>100, 'selectedid'=>$item['packingid'])).'</td>
						<td><input type="text" name="itemPartsDiscount['.$n.']" value="'.$item['discount'].'" size="3"></td>
						<td><input type="text" name="itemPartsQuantity['.$n.']" value="'.$item['quantity'].'" size="3"></td>
						<td>'.$this->kclass->chooserUnit(array('name'=>'itemPartsUnitid['.$n.']', 'hasBlank'=>1, 'width'=>80, 'selectedid'=>$item['unitid'])).'</td>
						<td><input type="text" name="itemPartsPrice['.$n.']" value="'.$item['price'].'" size="5"></td>
						<td><input type="checkbox" name="itemPartsKill['.$n.']" value="'.$item['itemid'].'"></td>
						</tr>';
						$n++;
					}
				}
			}
			for($i = $k; $i < ($k+5); $i++){
				$itemtrs .= '<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').'">
				<td><span id="tditemProductid'.$i.'"></span>'.$this->kclass->chooserProductItem(array('name'=>'itemProductid['.$i.']','selectName'=>'itemProductid'.$i, 'hasBlank'=>1, 'width'=>500)).'</td>
				<td><input type="text" name="itemCaption['.$i.']" value="'.$item['caption'].'"  size="65"></td>
				<td>'.$this->kclass->chooserBrand(array('name'=>'itemBrandid['.$i.']', 'hasBlank'=>1, 'width'=>80,'selectedid'=>1)).'</td>
				<td>'.$this->kclass->chooserPacking(array('name'=>'itemPackingid['.$i.']', 'hasBlank'=>1, 'width'=>100,'selectedid'=>6)).'</td>
				<td><input type="text" name="itemDiscount['.$i.']" value="100" size="3"></td>
				<td><input type="text" name="itemQuantity['.$i.']" value="'.$item['quantity'].'" size="3"  onblur="dc.returnProductStr('.$i.',this.value)"></td>
				<td>'.$this->kclass->chooserUnit(array('name'=>'itemUnitid['.$i.']', 'hasBlank'=>1, 'width'=>80, 'selectedid'=>5)).'</td>
				<td><input type="text" name="itemPrice['.$i.']" value="'.$item['price'].'" size="5"></td>
				<td><input type="checkbox" name="itemPartsKill['.$i.']"	value="'.$item['itemid'].'"/></td>
				</tr>';
			}
			for($i = $n; $i < ($n+5); $i++){
				$partStr .= '<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').'">
				<td>'.$this->kclass->chooserParts(array('name'=>'itemPartsid['.$i.']', 'hasBlank'=>1, 'width'=>500)).'</td>
				<td><input type="text" name="itemPartsCaption['.$i.']" value="'.$item['caption'].'"  size="65"></td>
				<td>'.$this->kclass->chooserBrand(array('name'=>'itemPartsBrandid['.$i.']', 'hasBlank'=>1, 'width'=>80,'selectedid'=>1)).'</td>
				<td>'.$this->kclass->chooserPacking(array('name'=>'itemPartsPackingid['.$i.']', 'hasBlank'=>1, 'width'=>100,'selectedid'=>6)).'</td>
				<td><input type="text" name="itemPartsDiscount['.$i.']" value="100" size="3"></td>
				<td><input type="text" name="itemPartsQuantity['.$i.']" value="'.$item['quantity'].'" size="3"></td>
				<td>'.$this->kclass->chooserUnit(array('name'=>'itemPartsUnitid['.$i.']', 'hasBlank'=>1, 'width'=>80, 'selectedid'=>5)).'</td>
				<td><input type="text" name="itemPartsPrice['.$i.']" value="'.$item['price'].'" size="5"></td>
				<td><input type="checkbox" name="itemPartsKill['.$i.']"	value="'.$item['itemid'].'"/></td>
				</tr>';
	 }
	   $order=<<<EOF
<tbody>
<tr class="odd">
	<td title="Contact" >联 系 人：<span class="red bold">*</span></td>
	<td><input type="text" style="width:250px" name="contact" value="{$customer['position']} {$customer['linkman']}" > <span class="small gray">指客户方询价人员</span></td>
	<td title="Currency">币　　种：</td>
	<td>{$currency}</td>
</tr>
		<tr class="even">
<td title="type">报价单类型：</td><td>{$quotingtype}</td>
<td title="dateline">交货日期<span class="bold red">*</span>:</td><td><input type="text" name="dateline" id="dateline" size="10" value="{$dateline}" ></td>
</tr>
<tr class="odd">
	<td title="Port">交货地点：</td>
	<td>{$port}</td>
	<td title="Delivery Term">交货条款：</td>
	<td>{$deliveryterm}　｜　运费：<input type="text" name="freight" value="{$freight}"/></td>
</tr>
<tr class="even">
	<td title="Shipment Method">运送方式：</td>
	<td>{$shipmethod}</td>
	<td title="Payment Term">支付条款：</td>
	<td>{$paymentterm}</td>
</tr>
</tbody>
 </table>
 <table class="hundred">
<thead>
<tr>
	<th colspan="10">产品明细表</th>
</tr>
</thead>
<tr><td>产品选择<span class="red bold">*</span></td><td width="300">说明</td><td width="100">品牌</td><td width="50">包装</td><td width="30">折扣</td><td width="30">数量<span class="red bold">*</span></td><td width="80">单位<span class="red bold">*</span></td><td width="50">单价<span class="red bold">*</span></td><td  width="20">删</td></tr>
{$itemtr}
<tr><td>配件选择<span class="red bold">*</span></td><td width="300">说明</td><td width="100">品牌</td><td width="50">包装</td><td width="30">折扣</td><td width="30">数量<span class="red bold">*</span></td><td width="80">单位<span class="red bold">*</span></td><td width="50">单价<span class="red bold">*</span></td><td  width="20">删</td></tr>
{$parts}
<tr>
	<th colspan="12">新建明细</th>
</tr>
<tr><td>产品选择<span class="red bold">*</span></td><td width="300">说明</td><td width="100">品牌</td><td width="50">包装</td><td width="30">折扣</td><td width="30">数量<span class="red bold">*</span></td><td width="80">单位<span class="red bold">*</span></td><td width="50">单价<span class="red bold">*</span></td><td  width="20">删除</td></tr>
{$itemtrs}
<tr><td>配件选择<span class="red bold">*</span></td><td width="300">说明</td><td width="100">品牌</td><td width="50">包装</td><td width="30">折扣</td><td width="30">数量<span class="red bold">*</span></td><td width="80">单位<span class="red bold">*</span></td><td width="50">单价<span class="red bold">*</span></td><td  width="20">删除</td></tr>
{$partStr}
<tr>
<tr class="even">
	<td colspan="10" class="gray small">注：①如行数不够，请保存后再进入修改页面。②至少有“产品”、“数量”、“单位”三个项目选择填写，该明细才会被保存。③“折扣”请填写百分比“%”。</td>
</tr>
</tbody>
<table class="hundred">
<tbody>
<tr class="odd">
	<td valign="top" title="Standard">规格说明：</td>
	<td><textarea name="standard" style="width:450px;height:60px">{$standrad}</textarea></td>
	<td valign="top" title="Material">主要材料：</td>
	<td><textarea name="material" style="width:450px;height:60px">{$material}</textarea></td>
</tr>
<tr class="even">
	<td valign="top" title="Technics">工艺说明：</td>
	<td><textarea name="technics" style="width:450px;height:60px">{$technics}</textarea></td>
	<td valign="top" title="Remark">备　　注：</td>
	<td><textarea name="remark" style="width:450px;height:60px">{$remark}</textarea></td>
</tr>

EOF;
			}
			//$parts.='<tr class="odd bold gray normal" id="orderPart" nohover><td colspan=10><span class="hand" onclick="dc.addLine(\'orderPart\',\'orderPart\')" style="border:2px solid gray;background:#FFE8EB">增加一行配件明细</span></td></tr>';
		
	
		}
			
      
		$body.=<<<EOF
	  	 <form action="/s.php?module=customer&action=insertQuoting" name="customer" method="post">
<input type="hidden" name="module" value="customer">
<input type="hidden" name="action" value="insertQuoting">
<input type="hidden" name="customerid" value="{$this->kclass->input['customerid']}">
<input type="hidden" name="inquiryid" value="{$this->kclass->input['inquiryid']}">
<table class="hundred">
<span class="right">{$flow}</span>
<thead>
<tr>
	<th colspan="4">{$titles}</th>
	
</tr>
</thead>
  {$order}

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
		$this->kclass->page['title'] .= ' - '.$customer['title'].' - '.$titles;
		$this->kclass->page['onload'] .= 'dc.tabhover();$(\'#dateline\').datepicker()';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - <a href="/s.php?module=customer&action=view&customerid='.$customer['customerid'].'">'.$customer['title'].'</a> - '.$titles, 'right' => '<a href="/s.php?module=customer">返回列表</a>', 'body'=>$body));
	}


	//
	function doupdateQuoting(){
		
		if($this->kclass->input['requestMethod'] != 'post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			if(strlen($this->kclass->input['contact']) < 2){
				$e .= '<li>请填写询价客户的相关联系人（不能少于2个字符）。</li>';
			}
			if($this->kclass->input['currencyid'] <= 0){
				$e .= '<li>请选择要客户询价的币种。</li>';
			}
			$j = count($this->kclass->input['itemProductid']);
			$hasItem =$hasPartItem= 0;
			for($i = 1; $i < $j+1; $i++){
				if($this->kclass->input['itemKill'][$i]=='' AND $this->kclass->input['itemProductid'][$i]>0 AND $this->kclass->input['itemQuantity'][$i]!='' AND $this->kclass->input['itemUnitid'][$i]>0 AND $this->kclass->input['itemPrice'][$i]>=0){
					$hasItem=1;
				}
			}
			$m=count($this->kclass->input['itemPartsId']);
			for($n=1;$n<$m+1;$n++){
				if($this->kclass->input['itemPartsKill'][$n]=='' AND $this->kclass->input['itemPartsid'][$n]>0 AND $this->kclass->input['itemPartsQuantity'][$n]!='' AND $this->kclass->input['itemPartsUnitid'][$n]>0 AND $this->kclass->input['itemPartsPrice'][$n]>=0){
					$hasPartItem=1;
				}
			}
			if($hasItem==0 AND $hasPartItem==0){
				$e .= '<li>需要填写至少有一条订单明细，才能建立订单。</li>';
			}
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '修改发往客户报价单',
				'text' => '您在修改发往客户报价单的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		for($i = 1; $i <= $j; $i++){
			$this->kclass->input['itemQuantity'][$i]=intVal($this->kclass->input['itemQuantity'][$i]);
			$this->kclass->input['itemPrice'][$i]=floatVal($this->kclass->input['itemPrice'][$i]);
			$this->kclass->input['itemDiscount'][$i]=intVal($this->kclass->input['itemDiscount'][$i]);
			if($this->kclass->input['itemDiscount'][$i]<0 OR $this->kclass->input['itemDiscount'][$i]>100){
				$this->kclass->input['itemDiscount'][$i]=100;
			}
			$amount = $this->kclass->input['itemQuantity'][$i] * $this->kclass->input['itemPrice'][$i];
			if($this->kclass->input['itemDiscount'][$i]<100){
				$amount = $amount * $this->kclass->input['itemDiscount'][$i] / 100;
			}
			$amount = number_format($amount, 2, '.', '');

			$productItem=$this->kclass->DB->queryFirst("
				SELECT pi.productid,pi.attributevalue,
					p.title
				FROM productitem AS pi
				LEFT JOIN product AS p ON (p.productid=pi.productid)
				WHERE productitemid='".$this->kclass->input['itemProductid'][$i]."'
			");
			if($this->kclass->input['itemProductid'][$i]>0 AND $this->kclass->input['itemQuantity'][$i]!='' AND $this->kclass->input['itemUnitid'][$i]>0 AND $this->kclass->input['itemPrice'][$i]>=0){
				if($this->kclass->input['itemId'][$i]>0){// 删除或者修改
					if($this->kclass->input['itemKill'][$i]>0){
						$this->kclass->DB->query("UPDATE `item` SET killer=".$this->kclass->user['userid'].",killed=".TIMENOW."  WHERE itemid='".$this->kclass->input['itemId'][$i]."'");
					}else{
						$pitem.='-'.$this->kclass->input['itemQuantity'][$i].$productItem['title'];
						$this->kclass->DB->query("
							UPDATE `item` SET
								`productid`='".$productItem['productid']."',
								`productitemid`='".$this->kclass->input['itemProductid'][$i]."',
								`productAttributes`='".$productItem['attributevalue']."',
								`caption`='".$this->kclass->input['itemCaption'][$i]."',
								`brandid`='".$this->kclass->input['itemBrandid'][$i]."',
								`packingid`='".$this->kclass->input['itemPackingid'][$i]."',
								`discount`='".$this->kclass->input['itemDiscount'][$i]."',
								`quantity`='".$this->kclass->input['itemQuantity'][$i]."',
								`unitid`='".$this->kclass->input['itemUnitid'][$i]."',
								`price`='".$this->kclass->input['itemPrice'][$i]."',
								`amount`='".$amount."',
								`modified`='".TIMENOW."',
								`modifier`='".$this->kclass->user['userid']."'
							WHERE itemid='".$this->kclass->input['itemId'][$i]."'
						");
						$quantity+=$this->kclass->input['itemQuantity'][$i];
					}
				}else{// 新增
					$pitem.='-'.$this->kclass->input['itemQuantity'][$i].$productItem['title'];
					$this->kclass->DB->query("
						INSERT INTO `item` (`module`,`mid`,`productid`,`productitemid`,`productAttributes`,`title`,`model`,`caption`,`articleNo`,`brandid`,`packingid`,`quantity`,`discount`,`unitid`,`price`,`amount`,`modified`,`creator`,`created`)
						VALUES
						('quoting','".$this->kclass->input['quotingid']."','".$productItem['productid']."','".$this->kclass->input['itemProductid'][$i]."','".$productItem['attributevalue']."','".$this->kclass->input['itemTitle'][$i]."', '".$this->kclass->input['itemModel'][$i]."', '".$this->kclass->input['itemCaption'][$i]."',  '".$this->kclass->input['itemArticleNo'][$i]."', '".$this->kclass->input['itemBrandid'][$i]."', '".$this->kclass->input['itemPackingid'][$i]."', '".$this->kclass->input['itemQuantity'][$i]."', '".$this->kclass->input['itemDiscount'][$i]."', '".$this->kclass->input['itemUnitid'][$i]."', '".$this->kclass->input['itemPrice'][$i]."','".$amount."', '".TIMENOW."', '".$this->kclass->user['userid']."', '".TIMENOW."')
					");
				}
			}
		}

		//配件明细
		for($i = 1; $i<=$m; $i++){
			$this->kclass->input['itemPartsQuantity'][$i]=intVal($this->kclass->input['itemPartsQuantity'][$i]);
			$this->kclass->input['itemPartsPrice'][$i]=floatVal($this->kclass->input['itemPartsPrice'][$i]);
			$this->kclass->input['itemPartsDiscount'][$i]=intVal($this->kclass->input['itemPartsDiscount'][$i]);
			if($this->kclass->input['itemPartsDiscount'][$i]<0 OR $this->kclass->input['itemPartsDiscount'][$i]>100){
				$this->kclass->input['itemPartsDiscount'][$i]=100;
			}
			$amount = $this->kclass->input['itemPartsQuantity'][$i] * $this->kclass->input['itemPartsPrice'][$i];
			if($this->kclass->input['itemPartsDiscount'][$i]<100){
				$amount = $amount * $this->kclass->input['itemPartsDiscount'][$i] / 100;
			}
			$amount = number_format($amount, 2, '.', '');
			$part=$this->kclass->DB->queryFirst("SELECT materialid FROM product WHERE productid='".$this->kclass->input['itemPartsid'][$i]."'");
			if($this->kclass->input['itemPartsid'][$i]>0 AND $this->kclass->input['itemPartsQuantity'][$i]!='' AND $this->kclass->input['itemPartsUnitid'][$i]>0 AND $this->kclass->input['itemPartsPrice'][$i]>=0){
				if($this->kclass->input['itemPartsId'][$i]>0){
					if($this->kclass->input['itemPartsKill'][$i]>0){
						$this->kclass->DB->query("UPDATE `item` SET killer=".$this->kclass->user['userid'].",killed=".TIMENOW."  WHERE itemid='".$this->kclass->input['itemPartsId'][$i]."'");
					}else{
						$this->kclass->DB->query("
							UPDATE `item` SET
								`materialid`='".$part['materialid']."',
								`productid`='".$this->kclass->input['itemPartsid'][$i]."',
								`caption`='".$this->kclass->input['itemPartsCaption'][$i]."',
								`brandid`='".$this->kclass->input['itemPartsBrandid'][$i]."',
								`packingid`='".$this->kclass->input['itemPartsPackingid'][$i]."',
								`discount`='".$this->kclass->input['itemPartsDiscount'][$i]."',
								`quantity`='".$this->kclass->input['itemPartsQuantity'][$i]."',
								`unitid`='".$this->kclass->input['itemPartsUnitid'][$i]."',
								`price`='".$this->kclass->input['itemPartsPrice'][$i]."',
								`amount`='".$amount."',
								`modified`='".TIMENOW."',
								`modifier`='".$this->kclass->user['userid']."'
							WHERE itemid='".$this->kclass->input['itemPartsId'][$i]."'
						");
						$quantity+=$this->kclass->input['itemPartsQuantity'][$i];
					}
				}else{
					$this->kclass->DB->query("
						INSERT INTO `item` 
							(`module`,`mid`,`materialid`,`productid`,`title`,`model`,`caption`,`articleNo`,`brandid`,`packingid`,`quantity`,`discount`,`unitid`,`price`,`amount`,`modified`,`creator`,`created`)
						VALUES 
							('quoting','".$this->kclass->input['quotingid']."','".$part['materialid']."','".$this->kclass->input['itemPartsid'][$i]."','".$this->kclass->input['itemTitle'][$i]."','".$this->kclass->input['itemModel'][$i]."','".$this->kclass->input['itemPartsCaption'][$i]."','".$this->kclass->input['itemArticleNo'][$i]."','".$this->kclass->input['itemPartsBrandid'][$i]."','".$this->kclass->input['itemPartsPackingid'][$i]."','".$this->kclass->input['itemPartsQuantity'][$i]."','".$this->kclass->input['itemPartsDiscount'][$i]."','".$this->kclass->input['itemPartsUnitid'][$i]."','".$this->kclass->input['itemPartsPrice'][$i]."','".$amount."','".TIMENOW."','".$this->kclass->user['userid']."', '".TIMENOW."')
					");
					$quantity+=$this->kclass->input['itemPartsQuantity'][$i];
				}
			}
		}
		$dateline=0;
		if($this->kclass->input['dateline']!=''){
			$dd=explode('-',$this->kclass->input['dateline']);
			$dateline=mktime(0,0,0,$dd[1],$dd[2],$dd[0]);
		}
		$this->kclass->DB->query("
			UPDATE `quoting` 
			SET `contact` = '".$this->kclass->input['contact']."', 
				`currencyid` = '".$this->kclass->input['currencyid']."', 
				`portid` = '".$this->kclass->input['portid']."', 
				`deliverytermid` = '".$this->kclass->input['deliverytermid']."', 
				`freight` = '".$this->kclass->input['freight']."', 
				`paymenttermid` = '".$this->kclass->input['paymenttermid']."', 
				`shipmethodid` = '".$this->kclass->input['shipmethodid']."', 
				`dateline` = '".$dateline."', 
				`standard` = '".$this->kclass->input['standard']."', 
				`technics` = '".$this->kclass->input['technics']."', 
				`material` = '".$this->kclass->input['material']."', 
				`remark` = '".$this->kclass->input['remark']."', 
				`modified` = '".TIMENOW."', 
				`modifier` = '".$this->kclass->user['userid']."'
			WHERE quotingid='".$this->kclass->input['quotingid']."'
		");
		$this->kclass->messager(array(
			'title' => '修改发往客户报价单',
			'text' => '发往客户<b>'.$this->kclass->input['title'].'</b> 的报价单已修改成功!',
			'url' => '/s.php?module=customer&action=view&customerid='.$this->kclass->input['customerid'],
			'sec' => 2
		));
	}

	//
	function updateQuoting(){
		if($this->kclass->input['quotingid']<=0 OR !$quoting = $this->kclass->DB->queryFirst("SELECT `quoting`.*, customer.title AS customer FROM `quoting` LEFT JOIN customer ON (customer.customerid=quoting.customerid) WHERE `quotingid`='".$this->kclass->input['quotingid']."'")){
			$this->kclass->boinkIt('/s.php?module=customer');
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
			$k=1;$n=1;
			while($item = $this->kclass->DB->fetchArray($items)){
				if($item['productitemid']>0){
					$product = $this->kclass->DB->queryFirst("
						SELECT `product`.title,`product`.entitle
						FROM  `productitem`
						LEFT JOIN `product` ON (`productitem`.productid = `product`.productid)
						WHERE productitemid='".$item['productitemid']."'
					");
					$seletStr = '<input type="hidden" id="tditemProductid'.$k.'input" value="'.$product['entitle'].'" />';
					$itemtr .= '<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').'">
					<td><span id="tditemProductid'.$k.'">'.$seletStr.'</span><input type="hidden" name="itemId['.$k.']" value="'.$item['itemid'].'">'.$this->kclass->chooserProductItem(array('name'=>'itemProductid['.$k.']','selectName'=>'itemProductid'.$k, 'selectedid'=>$item['productitemid'], 'hasBlank'=>1, 'width'=>500)).'</td>
					<td><input type="text" name="itemCaption['.$k.']" value="'.$item['caption'].'" size="65"></td>
					<td>'.$this->kclass->chooserBrand(array('name'=>'itemBrandid['.$k.']', 'hasBlank'=>1, 'width'=>80, 'selectedid'=>$item['brandid'])).'</td>
					<td>'.$this->kclass->chooserPacking(array('name'=>'itemPackingid['.$k.']', 'hasBlank'=>1, 'width'=>100, 'selectedid'=>$item['packingid'])).'</td>
					<td><input type="text" name="itemDiscount['.$k.']" value="'.$item['discount'].'" size="3"></td>
					<td><input type="text" name="itemQuantity['.$k.']" value="'.$item['quantity'].'" size="3" onblur="dc.returnProductStr('.$k.',this.value)"></td>
					<td>'.$this->kclass->chooserUnit(array('name'=>'itemUnitid['.$k.']', 'hasBlank'=>1, 'width'=>80, 'selectedid'=>$item['unitid'])).'</td>
					<td><input type="text" name="itemPrice['.$k.']" value="'.$item['price'].'" size="5"></td>
					<td><input type="checkbox" name="itemKill['.$k.']" value="'.$item['itemid'].'"></td>
					</tr>';
					$k++;
				}else{
					$parts .= '<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').'">
					<td><input type="hidden" name="itemPartsId['.$n.']" value="'.$item['itemid'].'">'.$this->kclass->chooserParts(array('name'=>'itemPartsid['.$n.']', 'selectedid'=>$item['productid'], 'hasBlank'=>1, 'width'=>500)).'</td>
					<td><input type="text" name="itemPartsCaption['.$n.']" value="'.$item['caption'].'" size="65"></td>
					<td>'.$this->kclass->chooserBrand(array('name'=>'itemPartsBrandid['.$n.']', 'hasBlank'=>1, 'width'=>80, 'selectedid'=>$item['brandid'])).'</td>
					<td>'.$this->kclass->chooserPacking(array('name'=>'itemPartsPackingid['.$n.']', 'hasBlank'=>1, 'width'=>100, 'selectedid'=>$item['packingid'])).'</td>
					<td><input type="text" name="itemPartsDiscount['.$n.']" value="'.$item['discount'].'" size="3"></td>
					<td><input type="text" name="itemPartsQuantity['.$n.']" value="'.$item['quantity'].'" size="3"></td>
					<td>'.$this->kclass->chooserUnit(array('name'=>'itemPartsUnitid['.$n.']', 'hasBlank'=>1, 'width'=>80, 'selectedid'=>$item['unitid'])).'</td>
					<td><input type="text" name="itemPartsPrice['.$n.']" value="'.$item['price'].'" size="5"></td>
					<td><input type="checkbox" name="itemPartsKill['.$n.']" value="'.$item['itemid'].'"></td>
					</tr>';
					$n++;
				}
			}
		}
		$itemtr.='<tr><td colspan="11">新建明细</td></tr>';
		for($i = $k; $i < ($k+5); $i++){
			$itemtrs .= '<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').'">
			<td><span id="tditemProductid'.$i.'"></span>'.$this->kclass->chooserProductItem(array('name'=>'itemProductid['.$i.']','selectName'=>'itemProductid'.$i, 'hasBlank'=>1, 'width'=>500)).'</td>
			<td><input type="text" name="itemCaption['.$i.']" value="'.$item['caption'].'"  size="65"></td>
			<td>'.$this->kclass->chooserBrand(array('name'=>'itemBrandid['.$i.']', 'hasBlank'=>1, 'width'=>80,'selectedid'=>1)).'</td>
			<td>'.$this->kclass->chooserPacking(array('name'=>'itemPackingid['.$i.']', 'hasBlank'=>1, 'width'=>100,'selectedid'=>6)).'</td>
			<td><input type="text" name="itemDiscount['.$i.']" value="100" size="3"></td>
			<td><input type="text" name="itemQuantity['.$i.']" value="'.$item['quantity'].'" size="3"  onblur="dc.returnProductStr('.$i.',this.value)"></td>
			<td>'.$this->kclass->chooserUnit(array('name'=>'itemUnitid['.$i.']', 'hasBlank'=>1, 'width'=>80, 'selectedid'=>5)).'</td>
			<td><input type="text" name="itemPrice['.$i.']" value="'.$item['price'].'" size="5"></td>
			<td></td>
			</tr>';
		}
		for($i = $n; $i < ($n+5); $i++){
			$partStr .= '<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').'">
			<td>'.$this->kclass->chooserParts(array('name'=>'itemPartsid['.$i.']', 'hasBlank'=>1, 'width'=>500)).'</td>
			<td><input type="text" name="itemPartsCaption['.$i.']" value="'.$item['caption'].'"  size="65"></td>
			<td>'.$this->kclass->chooserBrand(array('name'=>'itemPartsBrandid['.$i.']', 'hasBlank'=>1, 'width'=>80,'selectedid'=>1)).'</td>
			<td>'.$this->kclass->chooserPacking(array('name'=>'itemPartsPackingid['.$i.']', 'hasBlank'=>1, 'width'=>100,'selectedid'=>6)).'</td>
			<td><input type="text" name="itemPartsDiscount['.$i.']" value="100" size="3"></td>
			<td><input type="text" name="itemPartsQuantity['.$i.']" value="'.$item['quantity'].'" size="3"></td>
			<td>'.$this->kclass->chooserUnit(array('name'=>'itemPartsUnitid['.$i.']', 'hasBlank'=>1, 'width'=>80, 'selectedid'=>5)).'</td>
			<td><input type="text" name="itemPartsPrice['.$i.']" value="'.$item['price'].'" size="5"></td>
			<td></td>
			</tr>';
		}
		$dateline=date('Y-m-d',$quoting['dateline']);
$body = <<<EOF
<form action="/s.php?module=customer&action=doupdateQuoting" name="customer" method="post">
<input type="hidden" name="module" value="customer">
<input type="hidden" name="action" value="doupdateQuoting">
<input type="hidden" name="customerid" value="{$quoting['customerid']}">
<input type="hidden" name="quotingid" value="{$quoting['quotingid']}">
<table class="hundred">
<thead>
<tr>
	<th colspan="4">修改发往客户 <span class="big bold">{$quoting['title']}</span> 的 <span class="middle bold">报价单</span></th>
</tr>
</thead>
<tbody>
<tr class="even">
	<td>联 系 人：<span class="red bold">*</span></td>
	<td><input type="text" style="width:244px" name="contact" value="{$quoting['contact']}"> <span class="small gray">指客户方询价人员</span></td>
	<td>币　　种：<span class="red bold">*</span></td>
	<td>{$currency}　交货日期：<span class="bold red">*</span> <input type="text" name="dateline" id="dateline" size="10" value="{$dateline}" ></td>
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
</tbody>
</table>
<table class="hundred">
<thead>
<tr>
	<th colspan="11">产品明细表</th>
</tr>
</thead>
<tr><td>产品选择<span class="red bold">*</span></td><td width="300">说明</td><td width="100">品牌</td><td width="50">包装</td><td width="30">折扣</td><td width="30">数量<span class="red bold">*</span></td><td width="80">单位<span class="red bold">*</span></td><td width="50">单价<span class="red bold">*</span></td><td  width="20">删</td></tr>
{$itemtr}
<tr><td>配件选择<span class="red bold">*</span></td><td width="300">说明</td><td width="100">品牌</td><td width="50">包装</td><td width="30">折扣</td><td width="30">数量<span class="red bold">*</span></td><td width="80">单位<span class="red bold">*</span></td><td width="50">单价<span class="red bold">*</span></td><td  width="20">删</td></tr>
{$parts}
<tr>
	<th colspan="12">新建明细</th>
</tr>
<tr><td>产品选择<span class="red bold">*</span></td><td width="300">说明</td><td width="100">品牌</td><td width="50">包装</td><td width="30">折扣</td><td width="30">数量<span class="red bold">*</span></td><td width="80">单位<span class="red bold">*</span></td><td width="50">单价<span class="red bold">*</span></td><td  width="20"></td></tr>
{$itemtrs}
<tr><td>配件选择<span class="red bold">*</span></td><td width="300">说明</td><td width="100">品牌</td><td width="50">包装</td><td width="30">折扣</td><td width="30">数量<span class="red bold">*</span></td><td width="80">单位<span class="red bold">*</span></td><td width="50">单价<span class="red bold">*</span></td><td  width="20"></td></tr>
{$partStr}
<tr>
<tr class="even">
	<td colspan="11" class="gray small">注：①如行数不够，请保存后再进入修改页面。②至少有“产品”、“数量”、“单位”三个项目选择填写，该明细才会被保存。③“折扣”请填写百分比“%”。</td>
</tr>
</tbody>
<table class="hundred">
<tbody>

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
		$this->kclass->page['title'] .= ' - '.$quoting['customer'].' - 修改发往客户报价单';
		$this->kclass->page['onload'] .= 'dc.tabhover();$(\'#dateline\').datepicker()';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - <a href="/s.php?module=customer&action=view&customerid='.$customer['customerid'].'">'.$quoting['customer'].'</a> - 修改发往客户报价单', 'right' => '<a href="/s.php?module=customer">返回列表</a>', 'body'=>$body));
	}
	//
	function killQuoting(){
		if($this->kclass->input['quotingid']<=0){
			$this->kclass->boinkIt('/s.php?module=customer');
		}
		if($this->kclass->input['confirm'] == 0){
			$this->kclass->boinkIt('/s.php?module=customer&action=view&customerid='.$this->kclass->input['customerid']);
		}
		if($this->kclass->input['quotingid'] < 0){
			$e = '<li>请从正确的页面进行操作，如果此问题持续存在，请联系网站管理员。</li>';
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '删除发往客户的报价单',
				'text' => '您在删除发往客户的报价单的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$quoting = $this->kclass->DB->queryFirst("
			SELECT quoting.quotingid, quoting.customerid, 
				customer.title AS customer
			FROM quoting 
			LEFT JOIN customer ON (customer.customerid=quoting.customerid) 
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
				'title' => '删除发往客户的报价单成功',
				'text' => '发往客户 <b>'.$quotingid['customer'].'</b> 下的 报价单 已成功被标记为删除!',
				'url' => '/s.php?module=customer&action=view&customerid='.$quoting['customerid'],
				'sec' => 2
			));
		}else{
			$this->kclass->messager(array(
				'title' => '删除发往客户的报价单失败',
				'text' => '您要删除的产品报价单，不存在！',
				'url' => '/s.php?module=customer&action=view&customerid='.$quoting['customerid'],
				'sec' => 3
			));
		}
	}

	//
	function removeQuoting(){
		if($this->kclass->input['quotingid']<=0){
			$this->kclass->boinkIt('/s.php?module=customer');
		}
		$quoting = $this->kclass->DB->queryFirst("
			SELECT quoting.quotingid, quoting.customerid, 
				customer.title AS customer
			FROM quoting 
			LEFT JOIN customer ON (customer.customerid=quoting.customerid) 
			WHERE quoting.quotingid='".$this->kclass->input['quotingid']."'
		");
		if($quoting){
$body = <<<EOF
<form action="/s.php?module=customer&action=killQuoting" name="customer" method="post">
<input type="hidden" name="module" value="customer">
<input type="hidden" name="action" value="killQuoting">
<input type="hidden" name="customerid" value="{$quoting['customerid']}">
<input type="hidden" name="quotingid" value="{$quoting['quotingid']}">
<table><thead>
<thead>
<tr>
	<th>删除发往客户 {$quoting['customer']} 的 报价单</th>
</tr>
</thead>
<tbody>
<tr class="even">
	<td class="middle">你确定要删除发往客户 <a href="/s.php?module=customer&action=view&customerid={$quoting['customerid']}" class="middle bold" target="_blank">{$quoting['customer']}</a> 的 <span class="big bold">报价单</span> 吗?</td>
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
		$this->kclass->page['title'] .= ' - '.$quoting['customer'].' - 删除发往客户的报价单';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - <a href="/s.php?module=customer&action=view&customerid='.$customer['customerid'].'">'.$quoting['customer'].'</a> - 删除发往客户的报价单', 'right' => '<a href="/s.php?module=customer">返回列表</a>', 'body'=>$body));
	}

	//
	function revivalQuoting(){
		if($this->kclass->input['quotingid']<=0){
			$this->kclass->boinkIt('/s.php?module=customer');
		}
		if($this->kclass->input['confirm'] == 0){
			$this->kclass->boinkIt('/s.php?module=customer&action=view&customerid='.$this->kclass->input['customerid']);
		}
		if($this->kclass->input['quotingid'] < 0){
			$e = '<li>请从正确的页面进行操作，如果此问题持续存在，请联系网站管理员。</li>';
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '恢复发往客户的报价单',
				'text' => '您在恢复发往客户的报价单的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$quoting = $this->kclass->DB->queryFirst("
			SELECT quoting.quotingid, quoting.customerid, 
				customer.title AS customer
			FROM quoting 
			LEFT JOIN customer ON (customer.customerid=quoting.customerid) 
			WHERE quoting.quotingid='".$this->kclass->input['quotingid']."'
		");
		if($quoting){
			$this->kclass->DB->query("
				UPDATE `quoting`
				SET killed=0
				WHERE quotingid='".$quoting['quotingid']."'
			");
			$this->kclass->messager(array(
				'title' => '恢复发往客户的报价单成功',
				'text' => '客户 <b>'.$quotingid['customer'].'</b> 下的 报价单 已成功被标记为恢复!',
				'url' => '/s.php?module=customer&action=view&customerid='.$quoting['customerid'],
				'sec' => 2
			));
		}else{
			$this->kclass->messager(array(
				'title' => '恢复发往客户的报价单失败',
				'text' => '您要恢复发往客户的报价单，不存在！',
				'url' => '/s.php?module=customer&action=view&customerid='.$quoting['customerid'],
				'sec' => 3
			));
		}
	}

	//
	function restoreQuoting(){
		if($this->kclass->input['quotingid']<=0){
			$this->kclass->boinkIt('/s.php?module=customer');
		}
		$quoting = $this->kclass->DB->queryFirst("
			SELECT quoting.quotingid, quoting.customerid, 
				customer.title AS customer
			FROM quoting 
			LEFT JOIN customer ON (customer.customerid=quoting.customerid) 
			WHERE quoting.quotingid='".$this->kclass->input['quotingid']."'
		");
		if($quoting){
$body = <<<EOF
<form action="/s.php?module=customer&action=revivalQuoting" name="customer" method="post">
<input type="hidden" name="module" value="customer">
<input type="hidden" name="action" value="revivalQuoting">
<input type="hidden" name="customerid" value="{$quoting['customerid']}">
<input type="hidden" name="quotingid" value="{$quoting['quotingid']}">
<table><thead>
<thead>
<tr>
	<th>恢复发往客户 {$quoting['customer']} 的 报价单</th>
</tr>
</thead>
<tbody>
<tr class="even">
	<td class="middle">你确定要恢复发往客户 <a href="/s.php?module=customer&action=view&customerid={$quoting['customerid']}" class="middle bold" target="_blank">{$quoting['customer']}</a> 的 <span class="big bold">报价单</span> 吗?</td>
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
		$this->kclass->page['title'] .= ' - '.$quoting['customer'].' - 恢复发往客户的报价单';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - <a href="/s.php?module=customer&action=view&customerid='.$customer['customerid'].'">'.$quoting['customer'].'</a> - 恢复发往客户的报价单', 'right' => '<a href="/s.php?module=customer">返回列表</a>', 'body'=>$body));
	}
	//
	function verify(){
		if($this->kclass->input['customerid']!=""){
		$customers=$this->kclass->DB->queryFirst("SELECT title	,cntitle,ifverify FROM `customer` where customerid='".$this->kclass->input['customerid']."'");
		
		if(!$customers){
			$e.='<li>抱歉，数据错误。</li>';
		}
		if($customers['cntitle']==''){
			$customer=$customers['title'];
		}else{
			$customer=$customers['title'].'('.$customers['cntitle'].')';
		}
		if($customers['ifverify']!=0){
			$e.='<li>客户<b>'.$customers['title'].$customers['cntitle'].'</b>已审核<li>
					<li><b>如果未通过审核，请修改后提交！</b></li>';
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title'=>'审核客户',
				'text'=>'审核客户出现以下错误：<ul>'.$e.'</ul>',
				'url'=>'javascript:history.back()',
				'sec'=>3
				));
		}
		
	
	$item='<input type="hidden" name="customerid" value="'.$this->kclass->input['customerid'].'"/>
		<table class="hundred">
		<thead>
		<tr>
		<th colspan="4">审核客户：<b>'.$customer.'</b></th>
		</tr>
		</thead>';
		

}elseif($this->kclass->input['stquotingid']!=""){
	$quoting=$this->kclass->DB->queryFirst("SELECT	cntitle,entitle,ifverify FROM `stquoting`  WHERE quotingid='".$this->kclass->input['stquotingid']."'");
	if(!$quoting){
		$e.='<li>抱歉，数据错误</li>';
	}
   if($quoting['ifverify']!=0){
		$e.='<li>标准报价单<b>'.$quoting['cntitle'].'('.$quoting['entitle'].')</b>已审核<li>
					<li><b>如果未通过审核，请修改后提交！</b></li>';
   }
   if(isset($e)){
	   $this->kclass->messager(array(
		   'title'=>'审核报价单',
		   'text'=>'审核报价单出现以下错误：<ul>'.$e.'</ul>',
		   'url'=>'javascript:history.back()',
		   'sec'=>3
		   ));
   }
	$item='<input type="hidden" name="stquotingid" value="'.$this->kclass->input['stquotingid'].'">
	<table class="hundred">
	<thead>
	<tr>
	<th colspan="4">审核客户报价单：<b>'.$quoting['cntitle'].'('.$quoting['entitle'].')</b></th>
	</tr>
	';

}elseif($this->kclass->input['quotingid']!=''){
	$quoting=$this->kclass->DB->queryFirst("SELECT	`quoting`.ifverify,`quoting`.customerid,`customer`.title,`customer`.cntitle 
	              FROM `quoting`  
				  LEFT JOIN `customer` ON(`customer`.customerid=`quoting`.customerid)
				  WHERE quotingid='".$this->kclass->input['quotingid']."'");
	if(!$quoting){
		$e.='<li>抱歉，数据错误</li>';
	}
   if($quoting['ifverify']!=0){
		$e.='<li>标准报价单<b>'.$quoting['cntitle'].'('.$quoting['entitle'].')</b>已审核<li>
					<li><b>如果未通过审核，请修改后提交！</b></li>';
   }
   if(isset($e)){
	   $this->kclass->messager(array(
		   'title'=>'审核报价单',
		   'text'=>'审核报价单出现以下错误：<ul>'.$e.'</ul>',
		   'url'=>'javascript:history.back()',
		   'sec'=>3
		   ));
   }
  
	$item='<input type="hidden" name="quotingid" value="'.$this->kclass->input['quotingid'].'">
	<table class="hundred">
	<thead>
	<tr>
	<th colspan="4">审核发往客户<b>'.$this->kclass->iif($quoting['cntitle']!='',$quoting['title'].'('.$quoting['cntitle'].')',$quoting['title']).'</b>报价单：</th>
	</tr>
	';
}
	$body=<<<EOF
	<form actio="/s.php?module=customer&action=doverify"name="customer" method="post">
	<input type="hidden" name="module" value="customer"/>
	<input type="hidden" name="action" value="doverify"/>
		{$item}
	<tbody>
		<tr class="odd">
		<td colspan="4" align="center">审核通过：<input type="radio" name="ifverify" value="1" checked>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;审核未通过：<input type="radio" name="ifverify" value="-1"></td>
		</tr>
		</table>
		<table class="hundred">
		<tr >
		<td colspan="4" align="center"><input type="submit" id="submitButton" value="提交" accesskey="s">
		</td>
		</tr>
		</tbody>
		</table>
	</form>
EOF;
			$this->kclass->page['title'].='-审核客户';
			$this->kclass->page['onload'] .= 'dc.tabhover();$(\'#dateline\').datepicker()';
			$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.'-审核订单','right' => '<a href="'.REFERER.'">返回</a>', 'body'=>$body));
	}
	//doverify
	function doverify(){
		if($this->kclass->input['requestMethod']!='post'){
			$e='<li>请从正确的页面提交，如此问题持续出现，请联系网站管理员</li>';
		}else{
			if($this->kclass->input['customerid']!=""){
				$title='审核客户';
			$customers=$this->kclass->DB->queryFirst("SELECT title,cntitle,ifverify FROM customer WHERE customerid='".$this->kclass->input['customerid']."'");
			if(!$customers){
				$e.='<li>数据错误</li>';
			}
           if($customers['cntitle']==''){
			   $customer=$customers['title'];
		   }else{
			   $customer=$customers['title'].'('.$customers['cntitle'].')';
		   }
			if($customers['ifverify']!=0){
				$e.='<li>客户<b>'.$customer.'</b>已进行审核，请勿重复审核</li>
						<li>如果未通过审核请在修改后重新提交</li>';
			}
			if($this->kclass->input['ifverify']!=1 AND $this->kclass->input['ifverify']!=-1){
				$e.='<li>请选择订单中的 是否通过审核 按钮。</li>';
			}
			}elseif($this->kclass->input['stquotingid']!=""){
				$title='审核标准报价单';
				$quoting=$this->kclass->DB->queryFirst("SELECT cntitle,entitle,ifverify FROM stquoting WHERE quotingid='".$this->kclass->input['stquotingid']."'");
				if(!$quoting){
					$e.='<li>数据错误</li>';
				}
				if($quoting['ifverify']!=0){
					$e.='<li>标准报价单<b>'.$quoting['cntitle'].'('.$quoting['entitle'].')</b>已通过审核，请勿重复审核</li>
								<li>如果未通过审核请在修改后重新提交</li>';
				}
				if($this->kclass->input['ifverify']!=1 AND $this->kclass->input['ifverify']!=-1){
					$e.='<li>请选择订单中是否通过审核按钮。</li>';
				}
			}elseif($this->kclass->input['quotingid']!=""){
				$quoting=$this->kclass->DB->queryFirst("SELECT	`quoting`.ifverify,`quoting`.customerid,`customer`.title,`customer`.cntitle 
	              FROM `quoting`  
				  LEFT JOIN `customer` ON(`customer`.customerid=`quoting`.customerid)
				  WHERE quotingid='".$this->kclass->input['quotingid']."'");
				  if($quoting['cntitle']!=''){
					  $customer=$quoting['title'].'('.$quoting['cntitle'].')';
				  }else{
					  $customer=$quoting['title'];
				  }
				 if(!$quoting){
					 $e.='<li>数据错误</li>';
				 }
				 if($quoting['ifverify']!=0){
					 $e.='<li>发往客户<b>'.$customer.'</b>的报价单已经审核，请勿重复审核</li>
							<li>如果为通过审核请在修改后重新提交。</li>';
				 }
				 if($this->kclass->input['ifverify']!=1 AND $this->kclass->input['ifverify']!=-1){
					 $e.='<li>请选择订单中的 是否通过审核 按钮。</li>';
				 }
			}
		}
		if($e){
			$this->kclass->messager(array(
				'title'=>$title,
				'text'=>'您在审核中出现以下错误<ul>'.$e.'</ul>',
				'url'	=>'javascript:history.back()',
				'sec'=>3,
				));
		}
        if($this->kclass->input['customerid']!=""){
		if($this->kclass->input['ifverify']==1){
				$verify='已通过审核';
			}elseif($this->kclass->input['ifverify']==-1){
				
				$verify='未通过审核';
			}
			$this->kclass->DB->query("
			UPDATE `customer` SET
							ifverify='".$this->kclass->input['ifverify']."',
							verifier='".$this->kclass->user['userid']."',
							verified='".TIMENOW."'
							WHERE customerid='".$this->kclass->input['customerid']."'
			");
			$title='审核客户';
			$text='客户【<b>'.$customer.'</b>】'.$verify.'审核！返回查看客户信息';
			$url='/s.php?module=customer&action=view&customerid='.$this->kclass->input['customer'].'';
	}elseif($this->kclass->input['stquotingid']!=""){
		if($this->kclass->input['ifverify']==1){
			$verify="通过审核";
		}elseif($this->kclass->input['ifverify']==-1){
			
			$verify="未通过审核";
		}
		$this->kclass->DB->query("
			UPDATE `stquoting` SET
							ifverify='".$this->kclass->input['ifverify']."',
							verifier='".$this->kclass->user['userid']."',
							verified='".TIMENOW."'
							WHERE quotingid='".$this->kclass->input['stquotingid']."'
			");
		$title='审核标准报价单';
		$text='标准报价单【<b>'.$quoting['cntitle'].'('.$quoting['entitle'].'）</b>】'.$verify.'！返回查看客户信息';
        $url='/s.php?module=customer&action=STQuotingview&STQuotingid='.$this->kclass->input['stquotingid'].'';
	}elseif($this->kclass->input['quotingid']!=""){
		
			if($this->kclass->input['ifverify']==1){
				$this->kclass->DB->query("
			UPDATE `quoting` SET
							ifverify='".$this->kclass->input['ifverify']."',
							verifier='".$this->kclass->user['userid']."',
							verified='".TIMENOW."'
							WHERE quotingid='".$this->kclass->input['quotingid']."'
			");
			$verify="通过审核";
		}elseif($this->kclass->input['ifverify']==-1){
			$this->kclass->DB->query("
			UPDATE `quoting` SET
							ifverify='".$this->kclass->input['ifverify']."',
							verifier='".$this->kclass->user['userid']."',
							verified='".TIMENOW."'
							WHERE quotingid='".$this->kclass->input['quotingid']."'
			");
			$verify='未通过审核';
		}
   
			$title='审核发往客户'.$customer.'报价单';
			$text='发往客户【<b>'.$customer.'</b>】'.$verify.'返回查看客户信息';
			$url='/s.php?module=customer&action=view&customer';
	}
			$this->kclass->messager(array(
				'title'=>$title,
				'text'=>$text,
				'url'=>$url,
				'sec'=>3
				));
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
				$e .= '<li>请填写客户主要人员（不能少于3个字符）。</li>';
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
			INSERT INTO `visit` (`customerid`, `start`, `end`, `theirstaff`, `ourstaff`, `place`, `purpose`, `point`, `schedule`, `exception`, `remark`, `creator`, `created`) VALUES ('".$this->kclass->input['customerid']."', '".$start."', '".$end."', '".$this->kclass->input['theirstaff']."', '".$this->kclass->input['ourstaff']."', '".$this->kclass->input['place']."', '".$this->kclass->input['purpose']."', '".$this->kclass->input['point']."', '".$this->kclass->input['schedule']."', '".$this->kclass->input['exception']."', '".$this->kclass->input['remark']."', '".$this->kclass->user['userid']."', '".TIMENOW."')
		");
		$visitid = $this->kclass->DB->insertID();
		$this->kclass->messager(array(
			'title' => '新建互访记录',
			'text' => '与客户<b>'.$this->kclass->input['title'].'</b> 的互访记录已新建成功!',
			'url' => '/s.php?module=customer&action=view&customerid='.$this->kclass->input['customerid'],
			'sec' => 2
		));
	}

	//
	function addVisit(){
		if($this->kclass->input['customerid']<=0){
			$this->kclass->boinkIt('/s.php?module=customer');
		}
		$customer=$this->kclass->DB->queryFirst("SELECT customerid, title FROM `customer` WHERE killed=0 AND customerid='".$this->kclass->input['customerid']."'");
		$startHour='<select name="startHour"><option value="-1"> </option>';
		$endHour='<select name="endHour"><option value="-1"> </option>';
		$startMin='<select name="startMin"><option value="-1"> </option>';
		$endMin='<select name="endMin"><option value="-1"> </option>';
		for($i = 0; $i < 24; $i++){
			$startHour.='<option value="'.$i.'">'.sprintf('%02d', $i).'</option>';
			$endHour.='<option value="'.$i.'">'.sprintf('%02d', $i).'</option>';
		}
		for($i = 0; $i < 60; $i++){
			$startMin.='<option value="'.$i.'">'.sprintf('%02d', $i).'</option>';
			$endMin.='<option value="'.$i.'">'.sprintf('%02d', $i).'</option>';
		}
		$startHour.='</select>';
		$endHour.='</select>';
		$startMin.='</select>';
		$endMin.='</select>';

$body = <<<EOF
<form action="/s.php?module=customer&action=insertVisit" name="customer" method="post">
<input type="hidden" name="module" value="customer">
<input type="hidden" name="action" value="insertVisit">
<input type="hidden" name="customerid" value="{$this->kclass->input['customerid']}">
<table class="hundred">
<thead>
<tr>
	<th colspan="4">我方 与 客户互访记录</th>
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
	<td title="Our Staff">客户人员：<span class="red bold">*</span></td>
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
		$this->kclass->page['title'] .= ' - '.$customer['title'].' - 互访记录';
		$this->kclass->page['onload'] .= 'var dates=$(\'#startDate,#endDate\').datepicker({onSelect:function(selectedDate){var option=this.id==\'startDate\'?\'minDate\':\'maxDate\',instance=$(this).data(\'datepicker\'),date=$.datepicker.parseDate(instance.settings.dateFormat || $.datepicker._defaults.dateFormat,selectedDate,instance.settings);dates.not(this).datepicker(\'option\',option,date);}});dc.tabhover()';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - <a href="/s.php?module=customer&action=view&customerid='.$customer['customerid'].'">'.$customer['title'].'</a> - 互访记录', 'right' => '<a href="/s.php?module=customer">返回列表</a>', 'body'=>$body));
	}

	//
	function doupdateVisit(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			/*if(strlen($this->kclass->input['contact']) < 2){
				$e .= '<li>请填写询价客户的相关联系人（不能少于2个字符）。</li>';
			}
			if($this->kclass->input['currencyid'] <= 0){
				$e .= '<li>请选择要客户询价的币种。</li>';
			}*/
			if($this->kclass->input['startDate']=='' OR $this->kclass->input['startHour']<0 OR $this->kclass->input['startMin']<0 OR $this->kclass->input['endDate']=='' OR $this->kclass->input['endHour']<0 OR $this->kclass->input['endMin']<0){
				$e .= '<li>开始访问时间与结束访问时间，都必需选择。</li>';
			}
			if(strlen($this->kclass->input['theirstaff']) < 3){
				$e .= '<li>请填写客户主要人员（不能少于3个字符）。</li>';
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
				'title' => '修改发往客户互访记录',
				'text' => '您在修改发往客户互访记录的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		/*for($i = 1; $i < count($this->kclass->input['itemTitle'])+1; $i++){
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
							`customerNo`='".$this->kclass->input['itemCustomerNo'][$i]."', 
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
					INSERT INTO `item` (`visitid`, `productid`,`title`,`model`,`caption`,`customerNo`,`packingid`,`quantity`,`discount`,`unitid`,`modified`,`creator`,`created`)
					VALUES (
					'".$this->kclass->input['visitid']."', '".$this->kclass->input['itemProductid'][$i]."', '".$this->kclass->input['itemTitle'][$i]."', '".$this->kclass->input['itemModel'][$i]."', '".$this->kclass->input['itemCaption'][$i]."',  '".$this->kclass->input['itemCustomerNo'][$i]."', '".$this->kclass->input['itemPackingid'][$i]."', '".$this->kclass->input['itemQuantity'][$i]."', '".$this->kclass->input['itemDiscount'][$i]."', '".$this->kclass->input['itemUnitid'][$i]."', '".TIMENOW."', '".$this->kclass->user['userid']."', '".TIMENOW."')
				");
				$quantity+=$this->kclass->input['itemQuantity'][$i];
			}
		}*/
		$sd = explode('-', $this->kclass->input['startDate']);
		$start = mktime($this->kclass->input['startHour'],$this->kclass->input['startMin'],0,$sd[1],$sd[2],$sd[0]);
		$ed = explode('-', $this->kclass->input['endDate']);
		$end = mktime($this->kclass->input['endHour'],$this->kclass->input['endMin'],0,$ed[1],$ed[2],$ed[0]);
		$this->kclass->DB->query("
			UPDATE `visit` 
			SET  `ourstaff`='".$this->kclass->input['ourstaff']."',
					 `theirstaff`='".$this->kclass->input['theirstaff']."',
					 `start`='".$start."',
					 `end`='".$end."',
					 `place`='".$this->kclass->input['place']."',
					 `purpose`='".$this->kclass->input['purpose']."',
					 `schedule`='".$this->kclass->input['schedule']."',
					 `point`='".$this->kclass->input['point']."',
					 `exception`='".$this->kclass->input['exception']."',
					 `remark`='".$this->kclass->input['remark']."',
					 `modifier`='".$this->kclass->user['userid']."',
					 `modified`='".TIMENOW."' 
			WHERE `visitid`='".$this->kclass->input['visitid']."'
		");
		$this->kclass->messager(array(
			'title' => '修改发往客户互访记录',
			'text' => '发往客户<b>'.$this->kclass->input['title'].'</b> 的互访记录已修改成功!',
			'url' => '/s.php?module=customer&action=view&customerid='.$this->kclass->input['customerid'],
			'sec' => 2
		));
	}

	//
	function updateVisit(){
		if($this->kclass->input['visitid']<=0 OR !$visit = $this->kclass->DB->queryFirst("SELECT `visit`.*, customer.title AS customer FROM `visit` LEFT JOIN customer ON (customer.customerid=visit.customerid) WHERE `visitid`='".$this->kclass->input['visitid']."'")){
			$this->kclass->boinkIt('/s.php?module=customer');
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
		$startSelectHour = date('H', $visit['start']);
		$startSelectMin = date('i', $visit['start']);
		$endSelectHour = date('H', $visit['end']);
		$endSelectMin = date('i', $visit['end']);
		
		for($i = 0; $i < 24; $i++){
			if($i==$startSelectHour){
				$startHour.='<option value="'.$i.'" selected>'.sprintf('%02d', $i).'</option>';
			}else{
				$startHour.='<option value="'.$i.'">'.sprintf('%02d', $i).'</option>';
			}
			if($i==$endSelectHour){
				$endHour.='<option value="'.$i.'" selected>'.sprintf('%02d', $i).'</option>';
			}else{
				$endHour.='<option value="'.$i.'">'.sprintf('%02d', $i).'</option>';
			}
			
		}
		for($i = 0; $i < 60; $i++){
			if($i==$startSelectMin){
				$startMin.='<option value="'.$i.'" selected>'.sprintf('%02d', $i).'</option>';
			}else{
				$startMin.='<option value="'.$i.'">'.sprintf('%02d', $i).'</option>';
			}
			
			if($i==$endSelectMin){
				$endMin.='<option value="'.$i.'" selected>'.sprintf('%02d', $i).'</option>';
			}else{
				$endMin.='<option value="'.$i.'">'.sprintf('%02d', $i).'</option>';
			}
			
		}
		$startHour.='</select>';
		$endHour.='</select>';
		$startMin.='</select>';
		$endMin.='</select>';

$body = <<<EOF
<form action="/s.php?module=customer&action=doupdateVisit&visitid={$this->kclass->input['visitid']}" name="customer" method="post">
<input type="hidden" name="module" value="customer">
<input type="hidden" name="action" value="doupdateVisit">
<input type="hidden" name="visitid" value="{$this->kclass->input['visitid']}">
<input type="hidden" name="customerid" value="{$this->kclass->input['customerid']}">
<table class="hundred">
<thead>
<tr>
	<th colspan="4">我方 与 客户互访记录</th>
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
	<td title="Our Staff">客户人员：<span class="red bold">*</span></td>
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
		$this->kclass->page['title'] .= ' - '.$visit['customer'].' - 修改发往客户互访记录';
		$this->kclass->page['onload'] .= 'var dates=$(\'#startDate,#endDate\').datepicker({onSelect:function(selectedDate){var option=this.id==\'startDate\'?\'minDate\':\'maxDate\',instance=$(this).data(\'datepicker\'),date=$.datepicker.parseDate(instance.settings.dateFormat || $.datepicker._defaults.dateFormat,selectedDate,instance.settings);dates.not(this).datepicker(\'option\',option,date);}});dc.tabhover()';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - <a href="/s.php?module=customer&action=view&customerid='.$visit['customerid'].'">'.$visit['customer'].'</a> - 修改发往客户互访记录', 'right' => '<a href="/s.php?module=customer">返回列表</a>', 'body'=>$body));
	}
	//
	function killVisit(){
		if($this->kclass->input['visitid']<=0){
			$this->kclass->boinkIt('/s.php?module=customer');
		}
		if($this->kclass->input['confirm'] == 0){
			$this->kclass->boinkIt('/s.php?module=customer&action=view&customerid='.$this->kclass->input['customerid']);
		}
		if($this->kclass->input['visitid'] < 0){
			$e = '<li>请从正确的页面进行操作，如果此问题持续存在，请联系网站管理员。</li>';
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '删除发往客户的互访记录',
				'text' => '您在删除发往客户的互访记录的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$visit = $this->kclass->DB->queryFirst("
			SELECT visit.visitid, visit.customerid, 
				customer.title AS customer
			FROM visit 
			LEFT JOIN customer ON (customer.customerid=visit.customerid) 
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
				'title' => '删除发往客户的互访记录成功',
				'text' => '发往客户 <b>'.$visitid['customer'].'</b> 下的 互访记录 已成功被标记为删除!',
				'url' => '/s.php?module=customer&action=view&customerid='.$visit['customerid'],
				'sec' => 2
			));
		}else{
			$this->kclass->messager(array(
				'title' => '删除发往客户的互访记录失败',
				'text' => '您要删除的产品互访记录，不存在！',
				'url' => '/s.php?module=customer&action=view&customerid='.$visit['customerid'],
				'sec' => 3
			));
		}
	}

	//
	function removeVisit(){
		if($this->kclass->input['visitid']<=0){
			$this->kclass->boinkIt('/s.php?module=customer');
		}
		$visit = $this->kclass->DB->queryFirst("
			SELECT visit.visitid, visit.customerid, 
				customer.title AS customer
			FROM visit 
			LEFT JOIN customer ON (customer.customerid=visit.customerid) 
			WHERE visit.visitid='".$this->kclass->input['visitid']."'
		");
		if($visit){
$body = <<<EOF
<form action="/s.php?module=customer&action=killVisit" name="customer" method="post">
<input type="hidden" name="module" value="customer">
<input type="hidden" name="action" value="killVisit">
<input type="hidden" name="customerid" value="{$visit['customerid']}">
<input type="hidden" name="visitid" value="{$visit['visitid']}">
<table><thead>
<thead>
<tr>
	<th>删除发往客户 {$visit['customer']} 的 互访记录</th>
</tr>
</thead>
<tbody>
<tr class="even">
	<td class="middle">你确定要删除发往客户 <a href="/s.php?module=customer&action=view&customerid={$visit['customerid']}" class="middle bold" target="_blank">{$visit['customer']}</a> 的 <span class="big bold">互访记录</span> 吗?</td>
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
		$this->kclass->page['title'] .= ' - '.$visit['customer'].' - 删除发往客户的互访记录';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - <a href="/s.php?module=customer&action=view&customerid='.$customer['customerid'].'">'.$visit['customer'].'</a> - 删除发往客户的互访记录', 'right' => '<a href="/s.php?module=customer">返回列表</a>', 'body'=>$body));
	}

	//
	function revivalVisit(){
		if($this->kclass->input['visitid']<=0){
			$this->kclass->boinkIt('/s.php?module=customer');
		}
		if($this->kclass->input['confirm'] == 0){
			$this->kclass->boinkIt('/s.php?module=customer&action=view&customerid='.$this->kclass->input['customerid']);
		}
		if($this->kclass->input['visitid'] < 0){
			$e = '<li>请从正确的页面进行操作，如果此问题持续存在，请联系网站管理员。</li>';
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '恢复发往客户的互访记录',
				'text' => '您在恢复发往客户的互访记录的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$visit = $this->kclass->DB->queryFirst("
			SELECT visit.visitid, visit.customerid, 
				customer.title AS customer
			FROM visit 
			LEFT JOIN customer ON (customer.customerid=visit.customerid) 
			WHERE visit.visitid='".$this->kclass->input['visitid']."'
		");
		if($visit){
			$this->kclass->DB->query("
				UPDATE `visit`
				SET killed=0
				WHERE visitid='".$visit['visitid']."'
			");
			$this->kclass->messager(array(
				'title' => '恢复发往客户的互访记录成功',
				'text' => '客户 <b>'.$visitid['customer'].'</b> 下的 互访记录 已成功被标记为恢复!',
				'url' => '/s.php?module=customer&action=view&customerid='.$visit['customerid'],
				'sec' => 2
			));
		}else{
			$this->kclass->messager(array(
				'title' => '恢复发往客户的互访记录失败',
				'text' => '您要恢复发往客户的互访记录，不存在！',
				'url' => '/s.php?module=customer&action=view&customerid='.$visit['customerid'],
				'sec' => 3
			));
		}
	}

	//
	function restoreVisit(){
		if($this->kclass->input['visitid']<=0){
			$this->kclass->boinkIt('/s.php?module=customer');
		}
		$visit = $this->kclass->DB->queryFirst("
			SELECT visit.visitid, visit.customerid, 
				customer.title AS customer
			FROM visit 
			LEFT JOIN customer ON (customer.customerid=visit.customerid) 
			WHERE visit.visitid='".$this->kclass->input['visitid']."'
		");
		if($visit){
$body = <<<EOF
<form action="/s.php?module=customer&action=revivalVisit" name="customer" method="post">
<input type="hidden" name="module" value="customer">
<input type="hidden" name="action" value="revivalVisit">
<input type="hidden" name="customerid" value="{$visit['customerid']}">
<input type="hidden" name="visitid" value="{$visit['visitid']}">
<table><thead>
<thead>
<tr>
	<th>恢复发往客户 {$visit['customer']} 的 互访记录</th>
</tr>
</thead>
<tbody>
<tr class="even">
	<td class="middle">你确定要恢复发往客户 <a href="/s.php?module=customer&action=view&customerid={$visit['customerid']}" class="middle bold" target="_blank">{$visit['customer']}</a> 的 <span class="big bold">互访记录</span> 吗?</td>
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
		$this->kclass->page['title'] .= ' - '.$visit['customer'].' - 恢复发往客户的互访记录';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - <a href="/s.php?module=customer&action=view&customerid='.$customer['customerid'].'">'.$visit['customer'].'</a> - 恢复发往客户的互访记录', 'right' => '<a href="/s.php?module=customer">返回列表</a>', 'body'=>$body));
	}

	// 
	function insertFeedback(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			if(strlen($this->kclass->input['contact']) < 2){
				$e .= '<li>请填写询价客户的相关联系人（不能少于2个字符）。</li>';
			}
			if($this->kclass->input['dateline']==''){
				$e .= '<li>请选择客户反馈日期。</li>';
			}
			if($this->kclass->input['content']==''){
				$e .= '<li>请填写客户反馈内容。</li>';
			}
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '新建客户反馈信息',
				'text' => '您在新建客户反馈信息的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$dateline=0;
		if($this->kclass->input['dateline']!=''){
			$dd=explode('-',$this->kclass->input['dateline']);
			$dateline=mktime(0,0,0,$dd[1],$dd[2],$dd[0]);
		}
		$this->kclass->DB->query("
			INSERT INTO `feedback` (`customerid`, `contact`, `content`,`dateline`, `remark`, `created`, `creator`, `modified`) 
			VALUES ('".$this->kclass->input['customerid']."', '".$this->kclass->input['contact']."','".$this->kclass->input['content']."', '".$dateline."', '".$this->kclass->input['remark']."', '".TIMENOW."', '".$this->kclass->user['userid']."', '".TIMENOW."')
		");
		$this->kclass->messager(array(
			'title' => '新建客户反馈信息',
			'text' => '客户<b>'.$this->kclass->input['title'].'</b> 的反馈信息已新建成功!',
			'url' => '/s.php?module=customer&action=view&customerid='.$this->kclass->input['customerid'],
			'sec' => 2
		));
	}
// 新增反馈信息
	function addFeedback(){
		if($this->kclass->input['customerid']<=0){
			$this->kclass->boinkIt('/s.php?module=customer');
		}
		$customer=$this->kclass->DB->queryFirst("SELECT customerid, title, linkman FROM `customer` WHERE killed=0 AND customerid='".$this->kclass->input['customerid']."'");
		$body = <<<EOF
<form action="/s.php?module=customer&action=insertFeedback" name="customer" method="post">
<input type="hidden" name="module" value="customer">
<input type="hidden" name="action" value="insertFeedback">
<input type="hidden" name="customerid" value="{$this->kclass->input['customerid']}">
<table class="hundred">
<thead>
<tr>
	<th colspan="4">新建来自客户 <span class="big bold">{$customer['title']}</span> 的 <span class="middle bold">反馈信息</span></th>
</tr>
</thead>
<tbody>
<tr class="even">
	<td>联 系 人：<span class="red bold">*</span></td>
	<td><input type="text" style="width:244px" name="contact" value="{$customer['linkman']}"> <span class="small gray">指客户方反馈信息人员</span></td>
	<td>　反馈日期：<span class="red bold">*</span></td>
	<td><input type="text" name="dateline" id="dateline" size="15" ></td>
</tr>
<tr class="even">
<td valign="top">反馈内容描述：<span class="red bold">*</span></td>
<td><textarea name="content" style="width:450px;height:60px">{$inquiry['content']}</textarea></td>
<td valign="top">备　　注：</td>
<td><textarea name="remark" style="width:450px;height:60px">{$inquiry['remark']}</textarea></td>
</tr>
</tbody>
</table>
<table class="hundred">
<thead>

<tr class="odd">
<td colspan="10" align="center">
	<input type="submit" id="submitButton" value="  保存  " accesskey="s">　　
	<input type="reset" value="  复位  ">
</td>
</tr>
</table>
</form>
EOF;
		$this->kclass->page['title'] .= ' - '.$customer['title'].' - 新建客户反馈信息';
		$this->kclass->page['onload'] .= 'dc.tabhover();$(\'#dateline\').datepicker()';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - <a href="/s.php?module=customer&action=view&customerid='.$customer['customerid'].'">'.$customer['title'].'</a> - 新建客户反馈信息', 'right' => '<a href="/s.php?module=customer">返回列表</a>', 'body'=>$body));
		}

  // 
	function doupdateFeedback(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			if(strlen($this->kclass->input['contact']) < 2){
				$e .= '<li>请填写反馈客户的相关联系人（不能少于2个字符）。</li>';
			}
			if($this->kclass->input['dateline'] == ''){
				$e .= '<li>请选择要客户反馈时间。</li>';
			}
			if($this->kclass->input['content'] == ''){
				$e .= '<li>请填写客户反馈内容。</li>';
			}
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '修改客户反馈信息',
				'text' => '您在修改客户反馈信息的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$dateline=0;
		if($this->kclass->input['dateline']!=''){
			$dd=explode('-',$this->kclass->input['dateline']);
			$dateline=mktime(0,0,0,$dd[1],$dd[2],$dd[0]);
		}
		$this->kclass->DB->query("
			UPDATE `feedback` 
			SET `contact` = '".$this->kclass->input['contact']."', 
				`content` = '".$this->kclass->input['content']."',
				`dateline` = '".$dateline."', 
				`remark` = '".$this->kclass->input['remark']."',  
				`modified` = '".TIMENOW."', 
				`modifier` = '".$this->kclass->user['userid']."'
			WHERE feedbackid='".$this->kclass->input['feedbackid']."'
		");
		$this->kclass->messager(array(
			'title' => '修改客户反馈信息',
			'text' => '客户<b>'.$this->kclass->input['title'].'</b> 的反馈信息已修改成功!',
			'url' => '/s.php?module=customer&action=view&customerid='.$this->kclass->input['customerid'],
			'sec' => 2
		));
	}
   // 
  function updateFeedback(){
		if($this->kclass->input['feedbackid']<=0 OR !$feedbacks = $this->kclass->DB->queryFirst("SELECT `feedback`.*, customer.title AS customer FROM `feedback` LEFT JOIN customer ON (customer.customerid=feedback.customerid) WHERE `feedbackid`='".$this->kclass->input['feedbackid']."'")){
			$this->kclass->boinkIt('/s.php?module=customer');
		}
		$this->kclass->br2nl=true;
		$feedbacks['content']=$this->kclass->parseConvertValue($feedbacks['content']);
		$dateline=date('Y-m-d',$feedbacks['dateline']);
$body = <<<EOF
<form action="/s.php?module=customer&action=doupdateFeedback" name="customer" method="post">
<input type="hidden" name="module" value="customer">
<input type="hidden" name="action" value="doupdateFeedback">
<input type="hidden" name="customerid" value="{$feedbacks['customerid']}">
<input type="hidden" name="feedbackid" value="{$feedbacks['feedbackid']}">
<table class="hundred">
<thead>
<tr>
	<th colspan="4">修改来自客户 <span class="big bold">{$feedbacks['customer']}</span> 的 <span class="middle bold">反馈信息</span></th>
</tr>
</thead>
<tbody>
<tr class="even">
	<td>联 系 人：<span class="red bold">*</span></td>
	<td><input type="text" style="width:244px" name="contact" value="{$feedbacks['contact']}"> <span class="small gray">指客户方反馈信息人员</span></td>
	<td>　反馈日期：<span class="red bold">*</span></td>
	<td><input type="text" name="dateline" id="dateline" size="15" value="{$dateline}"></td>
</tr>
<tr class="even">
<td valign="top">反馈内容描述：<span class="red bold">*</span></td>
<td><textarea name="content" style="width:450px;height:60px">{$feedbacks['content']}</textarea></td>
<td valign="top">备　　注：</td>
<td><textarea name="remark" style="width:450px;height:60px">{$feedbacks['remark']}</textarea></td>
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
		$this->kclass->page['title'] .= ' - '.$feedbacks['customer'].' - 修改客户反馈信息';
		$this->kclass->page['onload'] .= 'dc.tabhover();$(\'#dateline\').datepicker()';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - <a href="/s.php?module=customer&action=view&customerid='.$customer['customerid'].'">'.$feedbacks['customer'].'</a> - 修改客户反馈信息', 'right' => '<a href="/s.php?module=customer">返回列表</a>', 'body'=>$body));
  }
  // 
  function removeFeedback(){
		if($this->kclass->input['feedbackid']<=0){
			$this->kclass->boinkIt('/s.php?module=customer');
		}
		$feedbacks = $this->kclass->DB->queryFirst("
			SELECT feedback.feedbackid, feedback.customerid, 
				customer.title AS customer
			FROM `feedback` 
			LEFT JOIN customer ON (customer.customerid=feedback.customerid) 
			WHERE feedback.feedbackid='".$this->kclass->input['feedbackid']."'
		");
		if($feedbacks){
$body = <<<EOF
<form action="/s.php?module=customer&action=killFeedback" name="customer" method="post">
<input type="hidden" name="module" value="customer">
<input type="hidden" name="action" value="killFeedback">
<input type="hidden" name="customerid" value="{$feedbacks['customerid']}">
<input type="hidden" name="feedbackid" value="{$feedbacks['feedbackid']}">
<table><thead>
<thead>
<tr>
	<th>删除客户 {$feedbacks['customer']} 的 反馈信息</th>
</tr>
</thead>
<tbody>
<tr class="even">
	<td class="middle">你确定要删除客户 <a href="/s.php?module=customer&action=view&customerid={$feedbacks['customerid']}" class="middle bold" target="_blank">{$feedbacks['customer']}</a> 的 <span class="big bold">反馈信息</span> 吗?</td>
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
		$this->kclass->page['title'] .= ' - '.$feedbacks['customer'].' - 删除客户反馈信息';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - <a href="/s.php?module=customer&action=view&customerid='.$feedbacks['customerid'].'">'.$feedbacks['customer'].'</a> - 删除客户反馈信息', 'right' => '<a href="/s.php?module=customer">返回列表</a>', 'body'=>$body));
  }
  // 
	function killFeedback(){
		if($this->kclass->input['feedbackid']<=0){
			$this->kclass->boinkIt('/s.php?module=customer');
		}
		if($this->kclass->input['confirm'] == 0){
			$this->kclass->boinkIt('/s.php?module=customer&action=view&customerid='.$this->kclass->input['customerid']);
		}
		if($this->kclass->input['feedbackid'] < 0){
			$e = '<li>请从正确的页面进行操作，如果此问题持续存在，请联系网站管理员。</li>';
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '删除客户反馈信息',
				'text' => '您在删除客户反馈信息的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$feedbacks = $this->kclass->DB->queryFirst("
			SELECT feedback.feedbackid, feedback.customerid, 
				customer.title AS customer
			FROM `feedback` 
			LEFT JOIN customer ON (customer.customerid=feedback.customerid) 
			WHERE feedback.feedbackid='".$this->kclass->input['feedbackid']."'
		");
		if($feedbacks){
			$this->kclass->DB->query("
				UPDATE `feedback`
				SET killed=".TIMENOW." 
				WHERE feedbackid='".$feedbacks['feedbackid']."'
			");
			$this->kclass->messager(array(
				'title' => '删除客户反馈信息成功',
				'text' => '客户 <b>'.$feedbacks['customer'].'</b> 下的 反馈信息 已成功被标记为删除!',
				'url' => '/s.php?module=customer&action=view&customerid='.$feedbacks['customerid'],
				'sec' => 2
			));
		}else{
			$this->kclass->messager(array(
				'title' => '删除客户反馈信息失败',
				'text' => '您要删除的客户反馈信息，不存在！',
				'url' => '/s.php?module=customer&action=view&customerid='.$feedbacks['customerid'],
				'sec' => 3
			));
		}
	}
	
	//xue 10/28 send e-mail
	function sendmail(){		
	//echo json_encode($this->kclass->input['quotingid']);exit;
		if($this->kclass->input['quotingid'] == '' || $this->kclass->input['customerid'] == '')
		{
			$this->kclass->boinkIt('./s.php?module=customer');
		}
        //是否审核
       /*  if($_POST['verify'] != 1){
            $this->kclass->messager(array(
                'title' => '未审核',
                'text' => '未审核,请您先进行审核！',
                'url' => './s.php?module=customer&action=view&customerid='.$this->kclass->input['customerid'],
                'sec' => 3
            ));
        } */
        if($this->kclass->input['customerid'])
        {
            $customer = $this->kclass->DB->query('
            SELECT  `customer`.*  FROM `customer`
            WHERE  `customer`.customerid='.$this->kclass->input['customerid'].'
            ');
						
            if($customer){	
				
                while($customers = $this->kclass->DB->fetchArray($customer)){  //返回索引数组
                    $to = $customers['email'];
                    $cc = $customers['email2'];
                    $customername = $customers['title'];
                    $linkman = $customers['linkman'];
                    $linkman2 = $customers['linkman2'];
                    $fax = $customer['fax'];
                }
            } 
			
			$str = preg_replace('/<a (href|target)=[^>].*?>(.*?)<\/a>/mUi','$2',$_REQUEST['data']);
			$str = mb_convert_encoding($str,'utf-8','gb2312//IGNORE');
			$str = $this->js_unescape($str);
			$subject = "QUOTING FOR CUSTOMER";
			$message = "DEAR ".($linkman?$linkman:$linkman2).",
			This is the quoting for our wisdom product about KL5M and someof  others, please read the attachs if you want to konw more";
			$message .= "If you have any question concerning the quoting for our more products,Please contact: Tom Ding, 86-755-83911841,tom@wisdom.hk
			thank you for you  bussiness！<br/>";
			
			//print_r($str);exit;
			$message .= '<html>
			<head>
		
			<style>
			body{margin:0;padding:0;font:normal 14px "Microsoft YaHei",Verdana;line-height:20px}
				.minus{
				background:url("/public/images/bg.gif") no-repeat 0 0;display:inline-block;vertical-align:middle;width:16px;height:16px;background-position:-32px -19px;}		
				table{background-color:#ccc;border-collapse: separate;border-spacing:1px;*border-collapse:expression("separate",cellSpacing="1px");}				
				table.eightypercent{width:80%}
				table.hundred{width:100%}
				table thead tr {background:#ddd url("images/bgMenu.gif") repeat center;}
				table thead tr th{border:1px solid white;padding:6px 4px;font-weight:bold;text-align:left}
				table tbody tr th{background:#ddd url("images/bgMenu.gif") repeat center;border:1px solid white;padding:6px 4px;font-weight:bold;text-align:left}
				table tfoot tr th{background-color:#eee;border:1px solid white;padding:4px}
				table thead tr th.sort{cursor:pointer;padding-right:25px;background-image:url("images/bgSort.gif") no-repeat center right}
				table tbody tr{background-color:white}
				table tbody td{/* padding:6px 4px;*/vertical-align:top}
				table tbody tr.odd td{background-color:white}
				table tbody tr.even td{background-color:#f8f8f8}
				table tbody tr.over td{background-color:#FEEBD1}
				table tbody tr.pin td{background-color:#FFCC99}
				table tbody tr.bgRed td{background-color:#ff6600}
				table tbody tr.bgYellow td{background-color:yellow}
				table tbody tr.bgOrange td{background-color:#ffce9d}
				table thead tr.sortUp{background-image:url("images/asc.gif")}
				table thead tr.sortDown{background-image:url("images/desc.gif")}
				table tbody tr td{font-size:15px}
				table.tablesorter {}
				table.tablesorter thead tr th.header {background-image: url(images/bgsort.gif);background-repeat: no-repeat;background-position: center right;cursor: pointer;}
				table.tablesorter thead tr th.headerSortUp {background-image: url(images/asc.gif);}
				table.tablesorter thead tr th.headerSortDown {background-image: url(images/desc.gif);}
				table.tablesorter thead tr th.headerSortDown, table.tablesorter thead tr th.headerSortUp {}
				.right{float:right}
				.left{float:left}
				.center{text-align:center}
				.tiny{font-size:10px;font-weight:normal}
				.small{font-size:12px;font-weight:normal}
				.normal{font-size:14px;font-weight:normal}
				.middle{font-size:16px;font-weight:normal}
				.big{font-size:18px;font-weight:normal}
				.hand{cursor:pointer}
				.bold{font-weight:bold}
				.underline{text-decoration:underline}
				.fix{position:fixed}

				.darkred{color:#D00058}
				.red{color:red}
				.green{color:green}
				.black{color:black}
				.gray{color:gray}
				.blue{color:blue}
				.orange{color:orange}
				ul.form_status{
				  display: inline-block;
				  margin: 2px 1px 0 0;
				  padding: 0 15px 0 0;
				}
				ul.form_status li{
				  display: inline-block;
				  list-style-type: none;
				  margin: 0 -16px 0 0;
				  padding: 0;
				  background-color: #ededed;
				  background-image: -webkit-gradient(linear, left top, left bottom, from(#fcfcfc), to(#dedede));
				  background-image: -webkit-linear-gradient(top, #fcfcfc, #dedede);
				  background-image: -moz-linear-gradient(top, #fcfcfc, #dedede);
				  background-image: -ms-linear-gradient(top, #fcfcfc, #dedede);
				  background-image: -o-linear-gradient(top, #fcfcfc, #dedede);
				  background-image: linear-gradient(to bottom, #fcfcfc, #dedede);
				}
				ul.form_status li:first-child .label{
				  border-left: 1px solid #cacaca;
				  padding-left: 14px;
				}
				ul.form_status li:last-child{
				  border-right: 1px solid #cacaca;
				}
				ul.form_status li:last-child .label{
				  padding-right: 14px;
				}
				ul.form_status li:last-child .arrow{
				  display: none;
				}
				ul.form_status li .label{
				  color: #4c4c4c;
				  text-shadow: 0 1px 1px #fcfcfc, 0 -1px 1px #dedede;
				  padding: 7px;
				  display: inline-block;
				  padding-left: 24px;
				  margin: 0;
				  position: relative;
				}
				ul.form_status li .arrow{
				  width: 18px;
				  display: inline-block;
				  vertical-align: top;
				  overflow: hidden;
				  margin-left: 5px;
				}
				ul.form_status li .arrow span{
				  position: relative;
				  width: 26px;
				  height: 26px;
				  display: inline-block;
				  margin-left: -12px;
				  margin-top: 3px;
				  box-shadow: -1px 1px 2px rgba(255, 255, 255, 0.2), inset -1px 1px 1px rgba(0, 0, 0, 0.2);
				  background-color: #dedede;
				  background: -moz-linear-gradient(135deg, #dedede, #fcfcfc);
				  background: -o-linear-gradient(135deg, #fcfcfc, #dedede);
				  background: -webkit-gradient(linear, left top, right bottom, from(#fcfcfc), to(#dedede));
				  background: -ms-linear-gradient(top, #fcfcfc, #dedede);
				  -moz-border-radius: 3px;
				  -webkit-border-radius: 3px;
				  border-radius: 3px;
				  -webkit-transform: rotate(45deg);
				  -moz-transform: rotate(45deg);
				  -ms-transform: rotate(45deg);
				  -o-transform: rotate(45deg);
				  transform: rotate(45deg);
				}
				ul.form_status li.active{
				  background-color: #5382b9;
				  background-image: -webkit-gradient(linear, left top, left bottom, from(#729fcf), to(#3465a4));
				  background-image: -webkit-linear-gradient(top, #729fcf, #3465a4);
				  background-image: -moz-linear-gradient(top, #729fcf, #3465a4);
				  background-image: -ms-linear-gradient(top, #729fcf, #3465a4);
				  background-image: -o-linear-gradient(top, #729fcf, #3465a4);
				  background-image: linear-gradient(to bottom, #729fcf, #3465a4);
				}
				ul.form_status li.active .arrow span{
				  background-color: #3465a4;
				  background: -moz-linear-gradient(135deg, #3465a4, #729fcf);
				  background: -o-linear-gradient(135deg, #729fcf, #3465a4);
				  background: -webkit-gradient(linear, left top, right bottom, from(#729fcf), to(#3465a4));
				  background: -ms-linear-gradient(top, #729fcf, #3465a4);
				}
				ul.form_status li.active .label{
				  color: white;
				  text-shadow: 0 1px 1px #729fcf, 0 -1px 1px #3465a4;
				}
			</style>
			</head><body><br/>
			
			<tbody>'.$str.'</tbody>';
			include '/private/mailer.php';		
			$mailer = new mailer();			
			$mailer->IsSMTP();
			$mailer->CharSet="UTF-8";
			$mailer->IsHTML(true); //支持html格式内容 
			$mailer->Host="mail.n-wisdom.com";
			$mailer->From="xueyuejian@wisdom.hk";
			$mailer->Username="xueyuejian@wisdom.hk";
			$mailer->Password="5xaws8rj";
			$mailer->Subject = $subject;	//设置主题
			$mailer->Body = $message; 
			//存在主要联系人
			if($to)
			{								
				$to_ = explode(';', $to);  //多个邮箱以";"隔开
				foreach($to_ as $key => $value)
				{
					$mailer->AddAddress($value, 'wisdom.hk');
					if(!$mailer->Send())
					{
						$e .= $mailer->ErrorInfo;
					}
				}
			}
			//发送次要联系人	
			if($cc)
			{				
				$cc_ = explode(';', $cc); 
				foreach($cc_ as $k => $v)
				{
					$mailer->AddAddress($v, 'wisdom.hk');
					if(!$mailer->Send())
					{
						$e .= $mailer->ErrorInfo;
					}
				}		
			}
			if($to == '' && $cc =='')
			{
				$e .= '<li>收件人不能为空，请完善客户信息</li>';
			}
			if($linkman =='' && $linkman2 =='')
			{
				$e .= '<li>联系人不能为空，请完善客户信息</li>';
			}
				
        }
		if(empty($e)){ // means success!
			echo json_encode('1');exit;
		}else{
			echo json_encode($e);exit;
		}		
   
}	//js编码
	function js_unescape($str){       
		$ret = '';       
		$len = strlen($str);       
		for ($i = 0; $i < $len; $i++)       
		{
		if ($str[$i] == '%' && $str[$i+1] == 'u')               
		{                       
		$val = hexdec(substr($str, $i+2, 4));                       
		if ($val < 0x7f) $ret .= chr($val);                       
		else if($val < 0x800) $ret .= chr(0xc0|($val>>6)).chr(0x80|($val&0x3f));else $ret .= chr(0xe0|($val>>12)).chr(0x80|(($val>>6)&0x3f)).chr(0x80|($val&0x3f));                       
		$i += 5;               
		}               
		else if ($str[$i] == '%')               
		{                       
		$ret .= urldecode(substr($str, $i, 3));                       
		$i += 2;               
		}               
		else $ret .= $str[$i];       
		}       
		return $ret;
}
    
}
?>

