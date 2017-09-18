<?php
	//
	class psupplier{
		var $kclass;
		var $category=array(array('id'=>1,'title'=>'五金类'),array('id'=>2,'title'=>'包装类'),array('id'=>3,'title'=>'塑胶类'),array('id'=>4,'title'=>'电子类'),array('id'=>5,'title'=>'组件类'),array('id'=>6,'title'=>'辅料类'),array('id'=>7,'title'=>'工具类'),array('id'=>8,'title'=>'其他类'),);
		//
		function autorun(){
			$this->baseurl='<a href="/s.php?">首页</a> ';
			switch($this->kclass->input['action']){
				case 'list':
					return $this->mmlist();
					break;
				case 'find':
					return $this->find();
					break;
				case 'view':
					return $this->view();
					break;
				case 'add':
					return $this->add();
					break;
				case 'insert':
					return $this->insert();
					break;
				case 'update':
					return $this->update();
					break;
				case 'doupdate':
					return $this->doupdate();
					break;
				case 'kill':
					return $this->kill();
					break;
				case 'remove':
					return $this->remove();
					break;
				case 'revival':
					return $this->revival();
					break;
				case 'restore':
					return $this->restore();
					break;
				case 'updateMaterial':
					return $this->updateMaterial();
					break;
				case 'doupdateMaterial':
					return $this->doupdateMaterial();
					break;
				case 'evaluate':
					return $this->evaluate();
					break;
				case 'doevaluate':
					return $this->doevaluate();
					break;
				case 'inquiry':
					return $this->inquiry();
					break;
				case 'addInquiry':
					return $this->addInquiry();
					break;
				case 'insertInquiry':
					return $this->insertInquiry();
					break;
				case 'updateInquiry':
					return $this->updateInquiry();
					break;
				case 'doupdateInquiry':
					return $this->doupdateInquiry();
					break;
				case 'killInquiry':
					return $this->killInquiry();
					break;
				case 'removeInquiry':
					return $this->removeInquiry();
					break;
				case 'revivalInquiry':
					return $this->revivalInquiry();
					break;
				case 'restoreInquiry':
					return $this->restoreInquiry();
					break;
				case 'quoting':
					return $this->quoting();
					break;
				case 'addQuoting':
					return $this->addQuoting();
					break;
				case 'insertQuoting':
					return $this->insertQuoting();
					break;
				case 'updateQuoting':
					return $this->updateQuoting();
					break;
				case 'doupdateQuoting':
					return $this->doupdateQuoting();
					break;
				case 'killQuoting':
					return $this->killQuoting();
					break;
				case 'removeQuoting':
					return $this->removeQuoting();
					break;
				case 'revivalQuoting':
					return $this->revivalQuoting();
					break;
				case 'restoreQuoting':
					return $this->restoreQuoting();
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
				case 'revivalVisit':
					$this->revivalVisit();
				break;
				case 'restoreVisit':
					$this->restoreVisit();
				break;
				case 'updateBankDetail':
					$this->updateBankDetail();
				break;
				case 'doupdateBankDetail':
					$this->doupdateBankDetail();
				break;
				case 'import':// 批量导入
					$this->import();
				break;
				case 'doimport':
					$this->doimport();
				break;
				case 'listOriginal':// 原始数据列表
					$this->listOriginal();
				break;
				case 'exchange':// 原始供应商数据转换成标准结构的供应商
					$this->exchange();
				break;
				case 'evaluateofyear':
					$this->evaluateofyear();
				break;
				case 'doevaluateofyear':
					$this->doevaluateofyear();
				break;
				case 'updateevaluate':
					$this->updateevaluate();
				break;
				case 'doupdateevaluate':
					$this->doupdateevaluate();
				break;
				case 'removeevaluate':
					$this->removeevaluate();
				break;
				case 'killevaluate':
					$this->killevaluate();
				break;
				case 'inbounditemlist':
					$this->inbounditemlist();
				break;
				case 'evaluatesupplier':
					$this->evaluatesupplier();
				break;
				case 'doevaluatesupplier':
					$this->doevaluatesupplier();
				break;
				case 'verifyconsignevaluate':
					$this->verifyconsignevaluate();
				break;
				case 'doverifyconsignevaluate':
					$this->doverifyconsignevaluate();
				break;
				case 'approveconsignevaluate':
					$this->approveconsignevaluate();
				break;
				case 'doapproveconsignevaluate':
					$this->doapproveconsignevaluate();
				break;
				case 'updateconsignevaluate':
					$this->updateconsignevaluate();
				break;
				case 'doupdateconsignevaluate':
					$this->doupdateconsignevaluate();
				break;
				case 'killconsignevaluate':
					$this->killconsignevaluate();
				break;
				case 'dokillconsignevaluate';
					$this->dokillconsignevaluate();
				default:
					return $this->mmlist();
			}
		}
		//供应商列表
		function mmlist(){
			//规定信息显示方式		
			if($this->kclass->input['orderby']!='')$query['orderby']=$this->kclass->input['orderby'];
			if($this->kclass->input['direction']!='')$query['direction']=$this->kclass->input['direction'];
			if($this->kclass->input['show']!='')$query['show']=$this->kclass->input['show'];
			if($this->kclass->input['layout']!='')$query['layout']=$this->kclass->input['layout'];
			$orderby=$this->kclass->orderby(
				array('module'=>'psupplier','direction'=>'asc','orderby'=>'`supplier`.modified', 'default'=>'supplierno', 'serial'=>
					array(
						array('title'=>'名称', 'field'=>'title','word'=>'name'), 
						array('title'=>'ID', 'field'=>'supplierid','word'=>'id'), 
						array('title'=>'编号', 'field'=>'supplierno'), 
						array('title'=>'修改时间', 'field'=>'modified'), 
						array('title'=>'建立时间', 'field'=>'created')
					),'appendUrl'=>$query)
				);
			if(is_array($query) AND count($query)>0)$queryPart='&'.http_build_query($query);
			if($this->kclass->input['startno']){
				$startNum = $this->kclass->input['startno'];
			}else{
				$startNum = 0;
			}
			$condition=$this->kclass->iif($this->kclass->input['show']=='all','1=1','`supplier`.killed=0');
			if(!($this->kclass->input['submit1'] or $this->kclass->input['submit2'])){//全部供应商列表
				$keywords=array();
				$search = '';
				$conten = $this->kclass->supplierList(
					array('condition'=>$condition,'search'=>$search,'orderby'=>$orderby,'layout'=>$this->kclass->input['layout'],'supplier'=>
						array('suppliertitle'=>$this->kclass->input['title'],
							  'linkman'=>$this->kclass->input['linkman'],
							  'telephone'=>$this->kclass->input['telephone'],
							  'email'=>$this->kclass->input['email'],
							  'mobile'=>$this->kclass->input['mobile'],
							  'regionid'=>$this->kclass->input['regionid']
						),'category'=>$this->category,'startNum'=>$startNum		
					)
				);
			}
			if($this->kclass->input['submit2']!=''){//find页面的供应商查询结果
				$keywords=array('keywords'=>$this->kclass->input['keywords']);
				$search= ' AND (`supplier`.supplierno like \'%'.$this->kclass->input['keywords'].'%\' OR `supplier`.title like \'%'.$this->kclass->input['keywords'].'%\')';
				$conten = $this->kclass->supplierList(
					array('condition'=>$condition,'search'=>$search,'orderby'=>$orderby,'layout'=>$this->kclass->input['layout'],'supplier'=>
						array('suppliertitle'=>$this->kclass->input['title'],
							  'linkman'=>$this->kclass->input['linkman'],
							  'telephone'=>$this->kclass->input['telephone'],
							  'email'=>$this->kclass->input['email'],
							  'mobile'=>$this->kclass->input['mobile'],
							  'regionid'=>$this->kclass->input['regionid']
						),'category'=>$this->category,'keywords'=>$keywords,'startNum'=>$startNum		
					)
				);					
			}
			if($this->kclass->input['submit1']!=''){//列表页分条件查询供应商
				$keywords = array('rank'=>$this->kclass->input['keywords'],'mtitle'=>$this->kclass->input['mtitle'],'categoryid'=>$this->kclass->input['categoryid'],'supplier'=>$this->kclass->input['supplier']);				
				if($this->kclass->input['rank']==''){//评价等级为空
					if($this->kclass->input['mtitle']!=''){
						$search= ' AND `supplier`.mainProduct like \'%'.$this->kclass->input['mtitle'].'%\'';
					}
					if($this->kclass->input['categoryid']!=''){
						$search= ' AND `supplier`.categoryid=\''.$this->kclass->input['categoryid'].'\'';
					}
					if($this->kclass->input['supplier']!=''){
						$search= ' AND (`supplier`.supplierno like \'%'.$this->kclass->input['supplier'].'%\' OR `supplier`.title like \'%'.$this->kclass->input['supplier'].'%\')';
					}
					//查询供应商信息并遍历列表
					$conten = $this->kclass->supplierList(
						array('condition'=>$condition,'search'=>$search,'orderby'=>$orderby,'layout'=>$this->kclass->input['layout'],'supplier'=>
							array('suppliertitle'=>$this->kclass->input['title'],
								  'linkman'=>$this->kclass->input['linkman'],
								  'telephone'=>$this->kclass->input['telephone'],
								  'email'=>$this->kclass->input['email'],
								  'mobile'=>$this->kclass->input['mobile'],
								  'regionid'=>$this->kclass->input['regionid']
							),'category'=>$this->category,'keywords'=>$keywords,'startNum'=>$startNum	
						)
					);
				}else{//评价等级非空
					$supplierids = $this->kclass->DB->query("SELECT supplierid FROM `evaluate` WHERE rank='".$this->kclass->input['rank']."'");
					if($this->kclass->DB->numRows()){
						$j=1;
						while($all=$this->kclass->DB->fetchArray($supplierids)){
							$supplierid[$j]=$all['supplierid'];
							$j++;
						}
						$supplierid = array_unique($supplierid);
						$supplierid = array_values($supplierid);
						/*echo "<pre>";
						print_r($supplierid);
						exit;*/
						foreach($supplierid AS $k=>$sid){
							$search = ' AND `supplier`.supplierid=\''.$sid.'\''; 
							if($this->kclass->input['mtitle']!=''){
								$search= ' AND `supplier`.supplierid=\''.$sid.'\' AND `supplier`.mainProduct like \'%'.$this->kclass->input['mtitle'].'%\'';
							}
							if($this->kclass->input['categoryid']!=''){
								$search= ' AND `supplier`.supplierid=\''.$sid.'\' AND `supplier`.categoryid=\''.$this->kclass->input['categoryid'].'\'';
							}
							if($this->kclass->input['materialno']!=''){
								$material = $this->kclass->DB->queryFirst("SELECT title FROM `material` WHERE materialno='".$this->kclass->input['materialno']."'");	
								$search = ' AND `supplier`.mainProduct=\''.$material['title'].'\''; 					
							}
							$item .= $this->kclass->supplierList(
								array('condition'=>$condition,'search'=>$search,'orderby'=>$orderby,'layout'=>$this->kclass->input['layout'],'supplier'=>
									array('rank'=>$this->kclass->input['rank'],
										  'suppliertitle'=>$this->kclass->input['title'],
										  'linkman'=>$this->kclass->input['linkman'],
										  'telephone'=>$this->kclass->input['telephone'],
										  'email'=>$this->kclass->input['email'],
										  'mobile'=>$this->kclass->input['mobile'],
										  'regionid'=>$this->kclass->input['regionid']
									),'category'=>$this->category,'keywords'=>$keywords,'startNum'=>$startNum,'id'=>$k
								)
							);						
						}						
						if($this->kclass->input['layout']=='grid'){
							$conten .= '<ul id="mmlist" class="mmlist clear">'.$item.'</ul>';
						}else{
							$conten .='<table class="hundred"><thead><th width="15">ID</th><th width="40">编号</th><th>供应商名称</th><th>简称</th><th width="35">等级</th><th>分类</th><th>主营</th><th>电话</th><th>地址</th><th width="60">选项</th></thead><tbody>'.$item.'			
							</tbody></table>';
						}
					}
				}
			}

			if($conten==''){
				$conten="未查询到相关数据";
			}
			if($this->kclass->input['layout']=='grid'){
				$layoutLink='<a href="/s.php?module=psupplier&action=list&layout=list'.str_replace('&layout=grid', '', $queryPart).'">列表</a> 格子';
			}else{
				$layoutLink='列表 <a href="/s.php?module=psupplier&action=list&layout=grid'.str_replace('&layout=list', '', $queryPart).'">格子</a>';
			}
			if($this->kclass->input['show']=='all'){
				$showLink='<a href="/s.php?module=psupplier&action=list'.str_replace('&show=all', '', $queryPart).'">默认</a> 所有<span class="small gray">(包括删除)</span>';
			}else{
				$showLink='默认 <a href="/s.php?module=psupplier&action=list&show=all'.str_replace('&show=all', '', $queryPart).'">所有<span class="small gray">(包括删除)</span></a>';
			}
$body=<<<EOF
<table class="hundred psupplier">
<tr><th>供应商筛选</th></tr>
<tr><td><form action="/s.php?module=psupplier&action=list" name="" method="post">
<select id="chooseType">
	<option value="">选择查询方式</option>
	<option value="1">按物资名称查询</option>
	<option value="2">供应商类别查询</option>
	<option value="3">供应商等级查询</option>
	<option value="4">特定供应商查询</option>
</select>  
<span class="nodisplay" id="sp-mltitle">
：<input type="text" name="mtitle" size="12" />
</span>
<span class="nodisplay" id="sp-category">
：<select name="categoryid" style="width:100px;height:26px">
			<option value="">选择类别</option>
			<option value="1">五金类</option>
			<option value="2">包装类</option>
			<option value="3">塑胶类</option>
			<option value="4">电子类</option>
			<option value="5">组件类</option>
			<option value="6">辅料类</option>							
			<option value="7">工具类</option>
			<option value="8">其他类</option>
</select>
</span>
<span class="nodisplay" id="sp-supplier">
：<input type="text" name="supplier" size="12" />
 (输入关键字查找特定供应商，关键字被供应商名称或编号包含即可！)</span>
<span class="nodisplay" id="sp-rank" style="width:100px;height:26px">
	<select id="condition" name="rank">
		<option value="">供应商等级</option>
		<option value="A"> A </option>
		<option value="B"> B </option>
		<option value="C"> C </option>
		<option value="D"> D </option>
	</select>
</span> 
<span class="nodisplay" id="sp-submit"><input type="submit" name="submit1" value=" 提交筛选 " /></span>
</form></td></tr>
</table>
{$conten}
EOF;
			$this->kclass->page['onload']='dc.tabhover(); dc.searchType()';
			$this->kclass->page['title']='供应商列表';
			$this->kclass->page['main']=$this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 列表', 'right' => '<span class="small">排序：</span>'.$orderby['link'].'|　<span class="small">显示方式：</span>'.$showLink.'　|　<span class="small">布局：</span>'.$layoutLink.'　|　<a href="/s.php?module=psupplier&action=add">新建</a>　|　<a href="/s.php?module=psupplier&action=import">批量导入</a>　<a href="/s.php?module=psupplier&action=inbounditemlist">入库单列表</a>', 'body'=>$body));
		}
		
		
		function find(){
			if($this->kclass->input['keywords']==''){
$body=<<<EOF
<form method="get" action="/s.php?module=psupplier&action=list">
<input type="hidden" name="module" value="psupplier">
<input type="hidden" name="action" value="list">
<table>
<thead><tr><th colspan=2>查找供应商：</th></tr></thead>
<tbody>
<tr><td>请输入查找关键词：</td><td><input type="text" name="keywords" style="width:350px"></td></tr>
<tr><td colspan=2 class="small gray">注意：请输入要查找供应商的编号或供应商名称</td></tr>
<tr class="center"><td colspan=2><input type="submit" name="submit2" value=" 提交 "><input type="reset" value=" 重置 "></td></tr>
</tbody>
</table>
</form>
EOF;
			}
			$this->kclass->page['title'].='供应商查找';
			$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - 供应商查找','right' => '<a href="/s.php?module=psupplier">返回列表</a>','body'=>$body));
		}
		//
		function view(){
			$supplierid=$this->kclass->input['supplierid'];
			$supplier=$this->kclass->DB->queryFirst("
				SELECT `supplier`.*,
					m.username AS modifier,
					c.username AS creator,
					b.title AS businesstype,
					r.country,r.state,r.city
				FROM `supplier`
				LEFT JOIN `user` AS m ON (m.userid=supplier.modifier)
				LEFT JOIN `user` AS c ON (c.userid=supplier.creator)
				LEFT JOIN `businesstype` AS b ON (b.businesstypeid=supplier.businesstypeid)
				LEFT JOIN `region` AS r ON (r.regionid=supplier.regionid)
				WHERE `supplier`.supplierid ='".$supplierid."'
				ORDER BY `supplier`.supplierid ASC,`supplier`.modified DESC,`supplier`.created DESC
				LIMIT 0,1
			");
			if($supplier){
				if($supplier['regionid']>0){
					if($this->kclass->purviews(array('module'=>'region','action'=>'list'))){//判断相关模块是否有权限查看链接到相应模块
						$region='<a href="/s.php?module=region&action=view&regionid='.$supplier['regionid'].'" target="_blank">'.$supplier['country'].' '.$supplier['state'].' '.$supplier['city'].'</a>';
					}else{
						$region=$supplier['country'].' '.$supplier['state'].' '.$supplier['city'];
					}
				}else{
					$region='';
				}
				foreach($this->category AS $category){
					if($category['id']==$supplier['categoryid']){
						$cat=$category['title'];
					}
				}
				$attach=$this->kclass->getAttachs(array('module'=>'supplier', 'mid'=>$supplier['supplierid']));
				if($attach!= false){
					$attachs='<div class="clear">'.$attach.'</div>';
				}
				$supplier['created']=date('Y-m-d H:i:s',$supplier['created']);
				if($supplier['modified']!=0)$modify='，由'.$supplier['modifier'].'于'.date('Y-m-d H:i:s',$supplier['modified']).'修改';
				$materialList=$this->kclass->relatedSupplierMaterial(array('supplierid'=>$supplier['supplierid']));//供应商供应的物资

				//供应商的评价
				$evaluates=$this->kclass->DB->query("SELECT * FROM evaluate WHERE killed=0 AND supplierid='".$supplierid."' ORDER BY evaluatedate DESC");
				if($evaluateList['num']=$this->kclass->DB->numRows()){
					$index=0;
					$evaluateList['panel']='<table class="hundred"><thead><tr><th colspan="10">供应商等级评价列表<a class="right" href="/s.php?module=psupplier&action=evaluateofyear&supplierid='.$supplier['supplierid'].'">选择年度评审明细</a></th></tr>
		<tr><th>编号</th><th>评价日期</th><th>产品质量(40)</th><th>发货速度(30)</th><th>价格评价(15)</th><th>服务(15)</th><th>总分(100)</th><th>等级</th><th>备注</th><th>操作</th></tr></thead><tbody>';
					while($evaluate=$this->kclass->DB->fetchArray($evaluates)){
						$index+=1;
						$evaluate['created']=date('Y-m-d H:i',$evaluate['created']);
						if($evaluate['evaluatedate']>0){
							$evaluatedate=date('Y-m-d ',$evaluate['evaluatedate']);
						}else{
							$evaluatedate="--";	
						}
						
						$evaluateList['panel'].='<tr class="'.$this->kclass->rotateLine().'">
							<td>'.$index.'</td>
							<td>'.$evaluatedate.'</td>
							<td>'.$evaluate['quality'].'</td>
							<td>'.$evaluate['delivery'].'</td>
							<td>'.$evaluate['price'].'</td>
							<td>'.$evaluate['service'].'</td>
							<td>'.$evaluate['total'].'</td>
							<td>'.$evaluate['rank'].'</td>
							<td>'.$evaluate['remark'].'</td>
							<td><a href="/s.php?module=psupplier&action=updateevaluate&supplierid='.$supplier['supplierid'].'&evaluateid='.$evaluate['evaluateid'].'">修改</a> <a href="/s.php?module=psupplier&action=removeevaluate&supplierid='.$supplier['supplierid'].'&evaluateid='.$evaluate['evaluateid'].'">删除</a></td></tr>';
					}
					/*$evaluateList['panel'].='<tr>
						<td colspan=2 align="right">合计：</td>
						<td>'.$supplier['quality'].'</td>
						<td>'.$supplier['price'].'</td>
						<td>'.$supplier['delivery'].'</td>
						<td>'.$supplier['service'].'</td>
						<td>'.$supplier['situation'].'</td>
						<td colspan=2>'.$supplier['average'].'</td>
						</tr>*/
					if($supplier['price']>0){
						$evaluateList['panel'].='<tr class="'.$this->kclass->rotateLine().'">
							<td colspan="2" align="right">年终总计：</td>
							<td>'.$supplier['quality'].'</td>
							<td>'.$supplier['delivery'].'</td>
							<td>'.$supplier['price'].'</td>
							<td>'.$supplier['service'].'</td>
							<td>'.$supplier['total'].'</td>
							<td colspan="3">'.$supplier['rank'].'</td>
						</tr>';
					}
					$evaluateList['panel'].='<tr>
					<td colspan="10"><span class="gray small">等级说明：100分-90分 优秀 级别 A级；89分-80分 合格 级别 B级；70-79分 限期改进 级别 C级； 69分以下 不合格 级别 D级。<br/>
					　　　　　得分在90分（含）以上者，为优秀供应商，可适当加大采购份额；<br/>
					　　　　　得分在89-80分（含）者，为合格供应商，可继续正常采购；<br/>
				    　　　　　得分在79-70分（含）者，为辅助供应商，需减小订单或暂停采购；<br/>
					　　　　　得分在70分以下者，为不合格供应商，需做淘汰处理且在半年内不得重新录用；如需特采，必须经总经理室审批签字。</span></td>
					</tr></tbody></table>';
					$evaluateList['count']='('.$evaluateList['num'].')';

				}else{
					$evaluateList['off']='disabled';
				}
				$bankDetail=$this->kclass->relatedBankDetail(array('module'=>'psupplier','supplierid'=>$supplier['supplierid']));
				$tracking=$this->kclass->relatedOrderTracking(array('supplierid'=>$supplier['supplierid']));
				$sample=$this->kclass->relatedSample(array('supplierid'=>$supplier['supplierid']));
				$inquiry=$this->kclass->relatedPInquiry(array('supplierid'=>$supplier['supplierid']));
				$quoting=$this->kclass->relatedPQuoting(array('supplierid'=>$supplier['supplierid']));
				$visit=$this->kclass->relatedPVisit(array('supplierid'=>$supplier['supplierid']));
				$order=$this->kclass->relatedPOrder(array('supplierid'=>$supplier['supplierid'], 'type'=>'PO'));
				$invoice=$this->kclass->relatedPInvoice(array('supplierid'=>$supplier['supplierid']));
				$commission=$this->kclass->relatedCommission(array('supplierid'=>$supplier['supplierid']));
				$deposit=$this->kclass->relatedDeposit(array('supplierid'=>$supplier['supplierid']));
				$guarantee=$this->kclass->relatedGuarantee(array('supplierid'=>$supplier['supplierid']));
				$creditnote=$this->kclass->relatedCreditnote(array('module'=>'porder','supplierid'=>$supplier['supplierid']));
				$recipt=$this->kclass->relatedRecipt(array('module'=>'porder','supplierid'=>$supplier['supplierid']));
				$debitnote=$this->kclass->relatedDebitnote(array('module'=>'porder','supplierid'=>$supplier['supplierid']));
				$payment=$this->kclass->relatedPayment(array('module'=>'porder','supplierid'=>$supplier['supplierid']));
				$claim=$this->kclass->relatedClaim(array('module'=>'porder','supplierid'=>$supplier['supplierid']));
				$return=$this->kclass->relatedReturn(array('module'=>'porder','supplierid'=>$supplier['supplierid']));
				$exchange=$this->kclass->relatedExchange(array('module'=>'porder','supplierid'=>$supplier['supplierid']));
				$customer=$this->kclass->relatedCustomer(array('supplierid'=>$supplier['supplierid']));
				$consignment=$this->kclass->relatedConsignment(array('supplierid'=>$supplier['supplierid']));
$body=<<<EOF
<div class="title"><span class="right small gray">由{$supplier['creator']}于{$supplier['created']}建立{$modify}。</span>{$supplier['title']}</div>
<dl id="supplier" class="tabs" style="display: block;">
	<dt>资料</dt>
	<dt title="供应商的评价"{$evaluateList['off']}>评价{$evaluateList['count']}</dt>
	<dt title="供应商来访与我们拜访"{$visit['off']}>互访{$visit['count']}</dt>
	<dt title="供应商询价记录"{$inquiry['off']}>询价{$inquiry['count']}</dt>
	<dt title="给供应商的报价记录"{$quoting['off']}>报价{$quoting['count']}</dt>
	<dt title="发给供应商的各类样品"{$sample['off']}>样品{$sample['count']}</dt>
	<dt title="与供应商相关的订单"{$order['off']}>订单{$order['count']}</dt>
	<dt title="与供应商相关的发票"{$invoice['off']}>发票{$invoice['count']}</dt>
	<dt title="与此供应商的物资跟踪情况">物资跟踪</dt>
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
	<dt title="供应商的交货评价记录"{$consignment['off']}>交货明细{$consignment['count']}</dt>
	<dd>
<div class="gray small"><span class="right normal"><span class="small">新建：</span><a href="/s.php?module=psupplier&action=evaluate&supplierid={$supplier['supplierid']}">评价供应商</a>　|　<a href="/s.php?module=psupplier&action=update&supplierid={$supplier['supplierid']}">修改</a>　|　<a href="/s.php?module=psupplier&action=addInquiry&supplierid={$supplier['supplierid']}">询价</a>　|　<a href="/p.php?action=order&amp;type=PI&amp;orderid=1" target="_blank">打印PI</a></span>由{$supplier['creator']}于{$supplier['created']}建立{$modify}。</div>
<table class="hundred">
<thead><tr><th colspan="4">供应商信息 </th></tr></thead>
	<tr class="odd">
		<td width="100">公　　司：</td><td>{$supplier['title']} （ {$supplier['supplierno']} ）</td>
		<td width="80">英文名称：</td><td>{$supplier['entitle']}</td>
	</tr>
	<tr class="even">
		<td>简　　称：</td><td>{$supplier['abbr']}</td>
		<td>地　　址：</td><td>{$supplier['address']}</td>
	</tr>
	<tr class="odd">
		<td>国　　家：</td><td>{$region}</td>
		<td>英文地址：</td><td>{$supplier['enaddress']}</td>
	</tr>
	<tr class="odd">
		<td>供应商类别：</td>
		<td>{$cat}</td>
		<td>主营产品：</td>
		<td>{$supplier['mainProduct']}</td>
	</tr>
	<tr class="even">
		<td>网　　址：</td><td>{$supplier['website']}</td>
		<td>邮　　箱：</td><td>{$supplier['email']}</td>
	</tr>
	<tr class="odd">
		<td>邮　　编：</td><td>{$supplier['postalcode']}</td>
		<td>固定电话：</td><td>{$supplier['telephone']}　传真：{$supplier['fax']}</td>
	</tr>
	<tr class="even">
		<td>商业类型：</td><td>{$supplier['businesstype']}</td>
		<td>联系方式：</td><td>{$supplier['mobile']}</td>
	</tr>
	<tr class="odd">
		<td>主联系人：</td><td>{$supplier['linkman']}（{$supplier['position']}）</td>
		<td>次联系人：</td><td>{$supplier['linkman2']}（{$supplier['position2']}）</td>
	</tr>
	<tr class="even">
		<td>简　　介：</td><td colspan=3>{$supplier['description']}</td>
	</tr>
	<tr class="odd">
		<td>备　　注：</td><td colspan=3>{$supplier['remark']}</td>
	</tr>
</table>
{$bankDetail['panel']}
{$materialList}
{$attachs}
</dd>
<dd>{$evaluateList['panel']}</dd>
<dd>{$visit['panel']}</dd>
<dd>{$inquiry['panel']}</dd>
<dd>{$quoting['panel']}</dd>
<dd>{$sample['panel']}</dd>
<dd>{$order['panel']}</dd>
<dd>{$invoice['panel']}</dd>
<dd>{$tracking}</dd>
<dd>{$commission['panel']}</dd>
<dd>{$deposit['panel']}</dd>
<dd>{$guarantee['panel']}</dd>
<dd>{$creditnote['panel']}</dd>
<dd>{$receipt['panel']}</dd>
<dd>{$debitnote['panel']}</dd>
<dd>{$payment['panel']}</dd>
<dd>{$claim['panel']}</dd>
<dd>{$return['panel']}</dd>
<dd>{$exchange['panel']}</dd>
<dd>{$consignment['panel']}</dd>
EOF;

			}else{
					$body='系统数据出错，很抱歉！';
			}
			$this->kclass->page['title'].='查看供应商';
			$this->kclass->page['onload'].='dc.tabhover();dc.tabs({\'id\':\'supplier\'});dc.show()';
			$this->kclass->page['main']=$this->kclass->skin->dbody(array('title' => $this->baseurl . ' - <a href="/s.php?module=psupplier&action=list">供应商列表</a> - 查看供应商', 'right' => $this->kclass->iif($supplier['killed']==0,'<span class="small">打印：</span><a href="/p.php?action=supplier&supplierid='.$supplier['supplierid'].'" target="_blank">基本资料</a>　<a href="/p.php?action=quoting&supplierid='.$supplier['supplierid'].'" target="_blank">报价表</a>　|　<span class="small">新建：</span><a href="/s.php?module=psupplier&action=evaluate&supplierid='.$supplier['supplierid'].'">评价供应商</a>　<a href="/s.php?module=psupplier&action=addVisit&supplierid='.$supplier['supplierid'].'">互访记录</a>　<a href="/s.php?module=psupplier&action=addInquiry&supplierid='.$supplier['supplierid'].'">询价单</a>　|　<span class="small">修改：</span><a href="/s.php?module=psupplier&action=update&supplierid='.$supplier['supplierid'].'&rt=view">供应商</a>　<a href="/s.php?module=psupplier&action=updateMaterial&supplierid='.$supplier['supplierid'].'" class="hand">供应物资</a>　<a href="/s.php?module=psupplier&action=updateBankDetail&supplierid='.$supplier['supplierid'].'" class="hand">银行信息</a>　|　<a href="/s.php?module=psupplier&action=remove&supplierid='.$supplier['supplierid'].'&rt=view">删除</a>','<span class="small">操作：</span><a href="/s.php?module=psupplier&action=restore&supplierid='.$supplier['supplierid'].'&rt=view">恢复</a>'),'body' => $body));
		}
		// insert
		function add(){
			$supplier=$this->kclass->DB->queryFirst("SELECT supplierno FROM supplier ORDER BY supplierno DESC LIMIT 0,1");
			$supplierno=$this->kclass->id(array('supplier'=>intval($supplier['supplierno']+1)));
			$region=$this->kclass->chooserRegion(array('name'=>'regionid', 'hasBlank'=>1, 'width'=>200, 'selectedid'=>$this->kclass->input['regionid']));
			$businesstype=$this->kclass->chooserBusinesstype(array('name'=>'businesstypeid', 'hasBlank'=>1, 'width'=>200, 'selectedid'=>$this->kclass->input['businesstypeid']));
			$category=$this->_chooserCategory();
			$upload=$this->kclass->upload(array('title'=>'相关附件：'));
			for($i=1;$i<6;$i++){
				$itemList.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').' small">
					<td>'.$this->kclass->chooserMaterial(array('hName'=>'itemMaterialid['.$i.']','hId'=>'itemMaterialid'.$i,'name'=>'itemMaterial['.$i.']','id'=>'itemMaterial'.$i,'width'=>400,'line'=>$i)).'</td>
					<td>'.$this->kclass->chooserBrand(array('name'=>'itemBrandid['.$i.']','hasBlank'=>1,'topname'=>'请选择')).'</td>
					<td>'.$this->kclass->chooserUnit(array('name'=>'itemUnitid['.$i.']','hasBlank'=>1,'topname'=>'请选择')).'</td>
					<td><input type="text" name="itemPrice['.$i.']" style="width:250px;" /></td>
					<td><input type="text" name="itemRemark['.$i.']" style="width:150px;" /></td>
				</tr>';
				$banktr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').'">
					<td><input type="text" name="bankAccountName['.$i.']" style="width:200px" /></td>
					<td><input type="text" name="bank['.$i.']" style="width:400px" /></td>
					<td><input type="text" name="bankAccount['.$i.']" style="width:250px" /></td>
				</tr>';
			}

$body=<<<EOF
<form action="/s.php?module=psupplier&action=insert" name="psupplier" method="post">
<input type="hidden" name="module" value="psupplier" />
<input type="hidden" name="action" value="insert" />
<input type="hidden" name="supplierno" value="{$supplierno}" />
<table class="hundred">
<thead><tr><th colspan="4">新建供应商</th></tr></thead>
<tbody>
<tr class="odd">
	<td>供应商编号：<span class="red bold">*</span></td>
	<td><input type="text" style="width:250px" name="supplierno" value="{$supplierno}"/> <span class="gray small">此为预估编号</span></td>
	<td>中文名称：<span class="red bold">*</span></td>
	<td><input type="text" style="width:450px" name="title" /></td>
</tr>
<tr class="even">
	<td>英文名称：</td>
	<td><input type="text" style="width:450px" name="entitle" /></td>
	<td>简　　称：<span class="red bold">*</span></td>
	<td><input type="text" name="abbr" /></td>
</tr>
<tr class="odd">
	<td>中文地址：<span class="red bold">*</span></td>
	<td><input type="text" style="width:450px" name="address" /></td>
	<td>英文地址：</td>
	<td><input type="text" style="width:450px" name="enaddress" /></td>
</tr>
<tr class="odd">
	<td>供应商类别：<span class="red bold">*</span></td>
	<td>{$category}</td>
	<td>主营产品：<span class="red bold">*</span></td>
	<td><input type="text" name="mainProduct" style="width:450px" /></td>
</tr>
<tr class="even">
	<td>网　　站：</td>
	<td><input type="text" name="website"/></td>
	<td>邮　　编：</td>
	<td><input type="text" name="postalcode"/></td>
</tr>
<tr class="even">
	<td>国　　家：<span class="red bold">*</span></td>
	<td>{$region}</td>
	<td>行　　业：<span class="red bold">*</span></td>
	<td>{$businesstype}</td>
</tr>
<tr class="odd">
	<td>固定电话：<span class="red bold">*</span></td>
	<td><input type="text"  name="telephone" /></td>
	<td>移动电话：</td>
	<td><input type="text"  name="mobile" /></td>
</tr>
<tr class="even">
	<td>传　　真：</td>
	<td><input type="text" name="fax" /></td>
	<td>邮　　件：</td>
	<td><input type="text" name="email"/></td>
</tr>
<tr class="odd">
	<td>主联系人：<span class="red bold">*</span></td>
	<td><input type="text"  name="linkman" style="width:450px" /></td>
	<td>职　　称：<span class="red bold">*</span></td>
	<td><input type="text"  name="position" /></td>
</tr>
<tr class="even">
	<td>次联系人：</td>
	<td><input type="text" name="linkman2" style="width:450px" /></td>
	<td>职　　称：</td>
	<td><input type="text" name="position2" /></td>
</tr>

<tr class="even">
	<td>简　　介：</td>
	<td><textarea type="text" name="description" style="width:444px;height:111px;"></textarea></td>
	<td>备　　注：</td>
	<td><textarea type="text" name="remark" style="width:444px;height:111px;"></textarea></td>
</tr>
</tbody>
</table>
{$upload}
<table class="tlist tsuite" align="center" width="100%">
<tbody>
<tr class="odd center" nohover>
<td colspan=4><input type="submit" value="  提交  " accesskey="s"><input type="reset" value="  复位  "/></td>
</tr>
</tbody>
</table>
</form>
EOF;
			$this->kclass->tbline+=4;
			$this->kclass->page['title'].='新建供应商';
			$this->kclass->page['onload'].='dc.tabhover();';
			$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - <a href="/s.php?module=psupplier">供应商列表</a>  - 新建供应商', 'right'=>'<a href="/s.php?module=psupplier">返回列表</a>','body'=>$body));

		}
		//
		function insert(){
			if($this->kclass->input['requestMethod'] != 'post'){
				$e='<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
			}else{
				if($this->kclass->input['supplierno']==''){
					$e .= '<li>请填写供应商的 供应商编号。</li>';
				}
				if($this->kclass->input['title'] == ''){
					$e='<li>请填写供应商的 中文名称。</li>';
				}else{
					if($this->kclass->DB->queryFirst("SELECT supplierid FROM `supplier` WHERE `title`='".$this->kclass->input['title']."'")){
						$e='<li>您要新建的供应商 【'.$this->kclass->input['title'].'】 已经存在。</li>';
					}
				}
				if(strlen($this->kclass->input['abbr']) > 18 OR strlen($this->kclass->input['abbr']) <6){
					$e .= '<li>请填写供应商的 简称缩写（不能超过6个中文字符,）。</li>';
				}
				if($this->kclass->input['address']==''){
					$e .= '<li>请填写供应商的 中文地址。</li>';
				}
				if($this->kclass->input['telephone']==''){
					$e .= '<li>请填写供应商的 固定电话。</li>';
				}
				if(!$this->kclass->input['regionid'] > 0){
					$e .= '<li>请选择供应商的 国家。</li>';
				}
				if(!$this->kclass->input['businesstypeid'] > 0){
					$e .= '<li>请选择供应商的 行业。</li>';
				}
				if(!$this->kclass->input['regionid'] > 0){
					$e .= '<li>请选择供应商的 国家。</li>';
				}
				if($this->kclass->input['linkman']==''){
					$e .= '<li>请填写供应商的 主联系人。</li>';
				}
				if($this->kclass->input['position']==''){
					$e .= '<li>请填写供应商的 职称。</li>';
				}
				if(!$this->kclass->input['categoryid']){
					$e .= '<li>请选择供应商的 类别。</li>';
				}
				if($this->kclass->input['mainProduct']==''){
					$e .= '<li>请填写供应商的 主营产品。</li>';
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
			$supplier=$this->kclass->DB->queryFirst("SELECT supplierno FROM supplier ORDER BY supplierno DESC LIMIT 0,1");
			$supplierno=$this->kclass->id(array('supplier'=>intval($supplier['supplierno']+1)));
			$this->kclass->DB->query("
				INSERT INTO `supplier` (`supplierno`,`field`,`title`,`entitle`,`abbr`,`address`,`enaddress`,`regionid`,`postalcode`,`telephone`,`mobile`,`fax`,`email`,`website`, `linkman`,`linkman2`,`position`,`position2`,`businesstypeid`,`description`,`categoryid`,`mainProduct`,`remark`,`hidden`,`modified`,`creator`,`created`) VALUES ('".$supplierno."','produce','".$this->kclass->input['title']."','".$this->kclass->input['entitle']."', '".$this->kclass->input['abbr']."','".$this->kclass->input['address']."','".$this->kclass->input['enaddress']."','".$this->kclass->input['regionid']."','".$this->kclass->input['postalcode']."','".$this->kclass->input['telephone']."','".$this->kclass->input['mobile']."','".$this->kclass->input['fax']."','".$this->kclass->input['email']."','".$this->kclass->input['website']."','".$this->kclass->input['linkman']."','".$this->kclass->input['linkman2']."','".$this->kclass->input['position']."','".$this->kclass->input['position2']."','".$this->kclass->input['businesstypeid']."','".$this->kclass->input['description']."','".$this->kclass->input['categoryid']."','".$this->kclass->input['mainProduct']."','".$this->kclass->input['remark']."','0','".TIMENOW."', '".$this->kclass->user['userid']."','".TIMENOW."')
			");
			$supplierid=$this->kclass->DB->insertID();
			$this->kclass->updateAttachs(array('module'=>'supplier', 'mid'=>$supplierid));

			for($m=1;$m<6;$m++){
				//insert supplier material item
				if($this->kclass->input['itemMaterialid'][$m]>0){
					$this->kclass->DB->query("
						INSERT INTO suppliermaterial (materialid,supplierid,price,brandid,unitid,remark,created,creator)
						VALUES ('".$this->kclass->input['itemMaterialid'][$m]."','".$supplierid."','".$this->kclass->input['itemPrice'][$m]."','".$this->kclass->input['itemBrandid'][$m]."','".$this->kclass->input['itemUnitid'][$m]."','".$this->kclass->input['itemRemark'][$m]."','".TIMENOW."','".$this->kclass->user['userid']."')
					");
				}
				if($this->kclass->input['bankAccountName'][$m]!='' AND $this->kclass->input['bank'][$m]!='' AND $this->kclass->input['bankAccount'][$m]!=''){
					$this->kclass->DB->query("
						INSERT INTO bankdetail (supplierid,bankAccountName,bank,bankAccount,created,creator) 
						VALUES ('".$supplierid."','".$this->kclass->input['bankAccountName'][$m]."','".$this->kclass->input['bank'][$m]."','".$this->kclass->input['bankAccount'][$m]."','".TIMENOW."','".$this->kclass->user['userid']."')
					");
				}
			}

			$this->kclass->messager(array(
				'title' => '新建供应商',
				'text' => '供应商 <b>'.$this->kclass->input['title'].'</b> 已新建成功!',
				'url' => '/s.php?module=psupplier&action=view&supplierid='.$supplierid,
				'sec' => 2
			));
		}
		// update supplier
		function update(){
			if($this->kclass->input['supplierid']>0){
				$supplier=$this->kclass->DB->queryFirst("
					SELECT `supplier`.*,
						m.username AS modifier,
						c.username AS creator
					FROM `supplier`
					LEFT JOIN `user` AS m ON (m.userid=supplier.modifier)
					LEFT JOIN `user` AS c ON (c.userid=supplier.creator)
					WHERE `supplier`.killed=0 AND `supplier`.supplierid ='".$this->kclass->input['supplierid']."'
					ORDER BY `supplier`.supplierid ASC,`supplier`.modified DESC,`supplier`.created DESC
					LIMIT 0,1
				");
				if($supplier){
					$region=$this->kclass->chooserRegion(array('name'=>'regionid','hasBlank'=>1,'width'=>200,'regionid'=>$supplier['regionid']));
					$businesstype=$this->kclass->chooserBusinesstype(array('name'=>'businesstypeid','hasBlank'=>1,'width'=>200,'selectedid'=>$supplier['businesstypeid']));
					$upload=$this->kclass->upload(array('module'=>'supplier', 'mid'=>$supplier['supplierid']));
					$category=$this->_chooserCategory($supplier['supplierid']);
$body=<<<EOF
<form action="/s.php?module=psupplier&action=doupdate" name="psupplier" method="post">
<input type="hidden" name="module" value="psupplier" />
<input type="hidden" name="action" value="doupdate" />
<input type="hidden" name="supplierid" value="{$supplier['supplierid']}" />
<table class="hundred">
<thead><tr><th colspan="4">新建供应商</th></tr></thead>
<tbody>
<tr class="odd">
	<td>中文名称：<span class="red bold">*</span></td>
	<td><input type="text" style="width:450px" name="title" value="{$supplier['title']}" /></td>
	<td>英文名称：</td>
	<td><input type="text" style="width:450px" name="entitle" value="{$supplier['entitle']}" /></td>
</tr>
<tr class="even">
	<td>供应商编号：<span class="red bold">*</span></td>
	<td><input type="text" name="supplierno" value="{$supplier['supplierno']}" /></td>
	<td>简　　称：<span class="red bold">*</span></td>
	<td><input type="text" name="abbr" value="{$supplier['abbr']}" /></td>
</tr>
<tr class="odd">
	<td>中文地址：<span class="red bold">*</span></td>
	<td><input type="text" style="width:450px" name="address" value="{$supplier['address']}" /></td>
	<td>英文地址：</td>
	<td><input type="text" style="width:450px" name="enaddress" value="{$supplier['enaddress']}" /></td>
</tr>
<tr class="odd">
	<td>供应商类别：<span class="red bold">*</span></td>
	<td>{$category}</td>
	<td>主营产品：<span class="red bold">*</span></td>
	<td><input type="text" name="mainProduct" style="width:450px" value="{$supplier['mainProduct']}" /></td>
</tr>
<tr class="even">
	<td>网　　站：</td>
	<td><input type="text" name="website" value="{$supplier['website']}" /></td>
	<td>邮　　编：</td>
	<td><input type="text" name="postalcode" value="{$supplier['postalcode']}" /></td>
</tr>
<tr class="even">
	<td>国　　家：<span class="red bold">*</span></td>
	<td>{$region}</td>
	<td>行　　业：<span class="red bold">*</span></td>
	<td>{$businesstype}</td>
</tr>
<tr class="odd">
	<td>固定电话：<span class="red bold">*</span></td>
	<td><input type="text"  name="telephone" value="{$supplier['telephone']}" /></td>
	<td>移动电话：</td>
	<td><input type="text"  name="mobile" value="{$supplier['mobile']}" /></td>
</tr>
<tr class="even">
	<td>传　　真：</td>
	<td><input type="text" name="fax" value="{$supplier['fax']}" /></td>
	<td>邮　　件：</td>
	<td><input type="text" name="email" value="{$supplier['email']}" /></td>
</tr>
<tr class="odd">
	<td>主联系人：<span class="red bold">*</span></td>
	<td><input type="text"  name="linkman" value="{$supplier['linkman']}" /></td>
	<td>职　　称：<span class="red bold">*</span></td>
	<td><input type="text"  name="position" value="{$supplier['position']}" /></td>
</tr>
<tr class="even">
	<td>次联系人：</td>
	<td><input type="text" name="linkman2" value="{$supplier['linkman2']}" /></td>
	<td>职　　称：</td>
	<td><input type="text" name="position2" value="{$supplier['position2']}" /></td>
</tr>
<tr class="even">
	<td>简　　介：</td>
	<td><textarea type="text" name="description" style="width:444px;height:111px;" >{$supplier['description']}</textarea></td>
	<td>备　　注：</td>
	<td><textarea type="text" name="remark" style="width:444px;height:111px;" >{$supplier['remark']}</textarea></td>
</tr>
</tbody>
</table>
{$upload}
<table class="tlist tsuite" align="center" width="100%">
<tbody>
<tr class="odd center" nohover>
<td colspan=4><input type="submit" value="  提交  " accesskey="s"><input type="reset" value="  复位  "/></td>
</tr>
</tbody>
</table>
</form>
EOF;
				}else{
					$body='系统数据出错，很抱歉！';
				}
				$this->kclass->tbline+=4;
				$this->kclass->page['title'].='修改供应商';
				$this->kclass->page['onload'].='dc.tabhover()';
				$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - <a href="/s.php?module=psupplier&action=view&supplierid='.$supplier['supplierid'].'">查看供应商</a> - 修改供应商', 'right'=>'<a href="/s.php?module=psupplier">返回列表</a>','body'=>$body));
			}
		}
		//
		function doupdate(){
			if($this->kclass->input['requestMethod'] != 'post'){
				$e='<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
			}else{
				if($this->kclass->input['supplierno'] == ''){
					$e='<li>请填写供应商的 编号。</li>';
				}
				if($this->kclass->input['title'] == ''){
					$e='<li>请填写供应商的 中文名称。</li>';
				}
				if(strlen($this->kclass->input['abbr']) > 18 OR strlen($this->kclass->input['abbr']) <6){
					$e .= '<li>请填写供应商的 简称缩写（不能超过6个中文字符,）。</li>';
				}
				if($this->kclass->input['address']==''){
					$e .= '<li>请填写供应商的 中文地址。</li>';
				}
				if($this->kclass->input['telephone']==''){
					$e .= '<li>请填写供应商的 固定电话。</li>';
				}
				if($this->kclass->input['regionid']<=0){
					$e .= '<li>请选择供应商的 国家。</li>';
				}
				if(!$this->kclass->input['businesstypeid'] > 0){
					$e .= '<li>请选择供应商的 行业。</li>';
				}
				if(!$this->kclass->input['regionid'] > 0){
					$e .= '<li>请选择供应商的 国家。</li>';
				}
				if($this->kclass->input['linkman']==''){
					$e .= '<li>请填写供应商的 主联系人。</li>';
				}
				if($this->kclass->input['position']==''){
					$e .= '<li>请填写供应商的 职称。</li>';
				}
				if(!$this->kclass->input['categoryid']){
					$e .= '<li>请选择供应商的 类别。</li>';
				}
				if($this->kclass->input['mainProduct']==''){
					$e .= '<li>请填写供应商的 主营产品。</li>';
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

			$this->kclass->DB->query("
				UPDATE `supplier` SET
					`supplierno`='".$this->kclass->input['supplierno']."',
					`title`='".$this->kclass->input['title']."',
					`entitle`='".$this->kclass->input['entitle']."',
					`abbr`='".$this->kclass->input['abbr']."',
					`address`='".$this->kclass->input['address']."',
					`enaddress`='".$this->kclass->input['enaddress']."',
					`regionid`='".$this->kclass->input['regionid']."',
					`postalcode`='".$this->kclass->input['postalcode']."',
					`telephone`='".$this->kclass->input['telephone']."',
					`mobile`='".$this->kclass->input['mobile']."',
					`fax`='".$this->kclass->input['fax']."',
					`email`='".$this->kclass->input['email']."',
					`website`='".$this->kclass->input['website']."',
					`linkman`='".$this->kclass->input['linkman']."',
					`linkman2`='".$this->kclass->input['linkman2']."',
					`position`='".$this->kclass->input['position']."',
					`position2`='".$this->kclass->input['position2']."',
					`businesstypeid`='".$this->kclass->input['businesstypeid']."',
					`description`='".$this->kclass->input['description']."',
					`categoryid`='".$this->kclass->input['categoryid']."',
					`mainProduct`='".$this->kclass->input['mainProduct']."',
					`remark`='".$this->kclass->input['remark']."',
					`modified`='".TIMENOW."',
					`modifier`='".$this->kclass->user['userid']."'
				WHERE supplierid='".$this->kclass->input['supplierid']."'
			");
			$count1=count($this->kclass->input['bankAccountName'])+1;

			$this->kclass->updateAttachs(array('module'=>'supplier', 'mid'=>$this->kclass->input['supplierid']));
			

			$this->kclass->messager(array(
				'title' => '修改供应商',
				'text' => '修改供应商 <b>'.$this->kclass->input['supplierno'].' '.$this->kclass->input['title'].'</b> 已修改成功!',
				'url' => '/s.php?module=psupplier&action=view&supplierid='.$this->kclass->input['supplierid'],
				'sec' => 2
			));
		}
					//
		function kill(){
			if($this->kclass->input['supplierid']<=0){
				$this->kclass->boinkIt('/s.php?module=psupplierid');
			}
			if($this->kclass->input['rt']!='list' AND  $this->kclass->input['confirm'] == 0){
				$this->kclass->boinkIt('/s.php?module=psupplier&action=view&supplierid='.$this->kclass->input['supplierid']);
			}
			if($this->kclass->input['supplierid'] < 0){
				$e='<li>请从正确的页面进行操作，如果此问题持续存在，请联系网站管理员。</li>';
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
				$rt='&action=view&supplierid='.$this->kclass->input['supplierid'];
			}elseif($this->kclass->input['rt']=='list'){
				$rt='#'.$this->kclass->input['supplierid'];
			}
			$supplier=$this->kclass->DB->queryFirst("
				SELECT supplierno
				FROM supplier
				WHERE supplierid='".$this->kclass->input['supplierid']."'
			");
			if($supplier){
				$this->kclass->DB->query("
					UPDATE `supplier`
					SET killed=".TIMENOW.",killer='".$this->kclass->user['userid']."'
					WHERE supplierid='".$this->kclass->input['supplierid']."'
				");
				$this->kclass->messager(array(
					'title' => '删除供应商成功',
					'text' => '供应商 <b>'.$supplier['supplierno'].'</b> 已成功被标记为删除!',
					'url' => '/s.php?module=psupplier&'.$rt,
					'sec' => 2
				));
			}else{
				$this->kclass->messager(array(
					'title' => '删除供应商失败',
					'text' => '您要删除的供应商，不存在！',
					'url' => '/s.php?module=psupplier&action='.$rt,
					'sec' => 3
				));
			}
	}

		//
		function remove(){
			if($this->kclass->input['supplierid']<=0){
				$this->kclass->boinkIt('/s.php?module=psupplier');
			}
			$supplier=$this->kclass->DB->queryFirst("
				SELECT supplierno
				FROM `supplier`
				WHERE supplierid='".$this->kclass->input['supplierid']."'
			");
$body=<<<EOF
<form action="/s.php?module=psupplier&action=kill" name="supplier" method="post">
<input type="hidden" name="module" value="supplier">
<input type="hidden" name="action" value="kill">
<input type="hidden" name="rt" value="{$this->kclass->input['rt']}">
<input type="hidden" name="supplierid" value="{$this->kclass->input['supplierid']}">
<table><thead>
<thead>
<tr>
	<th>删除供应商：{$supplier['supplierno']}</th>
</tr>
</thead>
<tbody>
<tr class="even">
	<td class="middle">你确定要删除供应商: <a href="/s.php?module=psupplier&action=view&supplierid={$this->kclass->input['supplierid']}" class="big bold" target="_blank">{$supplier['supplierno']}</a> 吗?</td>
</tr>
<tr class="odd">
	<td align="center">
		<label for="c1" class="red"><input type="radio" id="c1" name="confirm" value="1">是</label>
		<label for="c0" class="green"><input type="radio" id="c0" name="confirm" value="0" checked>否</label>
	</td>
</tr>
<tr class="even" nohover>
	<td align="center">
		<input type="submit" value="   保存   " accesskey="s">
		<input type="reset" value="   复位   ">
	</td>
</tr>
</tbody>
</table>
</form>
EOF;
			$this->kclass->page['title'] .= ' - 删除 - '.$supplier['supplierno'];
			$this->kclass->page['main']=$this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 删除供应商 - '.$supplier['supplierno'], 'right' => '<a href="/s.php?module=psupplier">返回列表</a>', 'body'=>$body));
		}

		//
		function revival(){
			if($this->kclass->input['supplierid']<=0){
				$this->kclass->boinkIt('/s.php?module=psupplier');
			}
			if($this->kclass->input['rt']!='list' AND $this->kclass->input['confirm'] == 0){
				$this->kclass->boinkIt('/s.php?module=psupplier&action=view&supplierid='.$this->kclass->input['supplierid']);
			}
			if($this->kclass->input['supplierid'] < 0){
				$e='<li>请从正确的页面进行操作，如果此问题持续存在，请联系网站管理员。</li>';
			}
			if(isset($e)){
				$this->kclass->messager(array(
					'title' => '恢复供应商',
					'text' => '您在恢复供应商的过程中有以下错误：<ul>'.$e.'</ul>',
					'url' => 'javascript:history.back()',
					'sec' => 3
				));
			}
			$supplier=$this->kclass->DB->queryFirst("
				SELECT supplierno
				FROM `supplier`
				WHERE supplierid='".$this->kclass->input['supplierid']."'
			");
			if($this->kclass->input['rt']=='view'){
				$rt='&action=view&supplierid='.$this->kclass->input['supplierid'];
			}elseif($this->kclass->input['rt']=='list'){
				$rt='#'.$this->kclass->input['supplierid'];
			}
			if($supplier){
				$this->kclass->DB->query("
					UPDATE `supplier`
					SET killed=0,killer=0
					WHERE supplierid='".$this->kclass->input['supplierid']."'
				");
				$this->kclass->messager(array(
					'title' => '恢复供应商成功',
					'text' => '供应商 <b>'.$supplier['supplierno'].'</b> 已成功被恢复！',
					'url' => '/s.php?module=psupplier'.$rt,
					'sec' => 2
				));
			}else{
				$this->kclass->messager(array(
					'title' => '恢复供应商失败',
					'text' => '您要恢复的供应商不存在！',
					'url' => '/s.php?module=psupplier'.$rt,
					'sec' => 3
				));
			}
		}

		//
		function restore(){
			if($this->kclass->input['supplierid']<=0){
				$this->kclass->boinkIt('/s.php?module=psupplier');
			}
			$supplier=$this->kclass->DB->queryFirst("
				SELECT supplierno
				FROM `supplier`
				WHERE supplierid='".$this->kclass->input['supplierid']."'
			");
$body=<<<EOF
<form action="/s.php?module=psupplier&action=revival" name="supplier" method="post">
<input type="hidden" name="module" value="supplier">
<input type="hidden" name="action" value="revival">
<input type="hidden" name="rt" value="{$this->kclass->input['rt']}">
<input type="hidden" name="supplierid" value="{$this->kclass->input['supplierid']}">
<table><thead>
<thead>
<tr>
	<th>恢复供应商：{$supplier['supplierno']}</th>
</tr>
</thead>
<tbody>
<tr class="even">
	<td class="middle">你确定要恢复供应商: <a href="/s.php?module=psupplier&action=view&supplierid={$this->kclass->input['supplierid']}" class="big bold" target="_blank">{$supplier['supplierno']}</a> 吗?</td>
</tr>
<tr class="odd">
	<td align="center">
		<label for="c1" class="red"><input type="radio" id="c1" name="confirm" value="1">是</label>
		<label for="c0" class="green"><input type="radio" id="c0" name="confirm" value="0" checked>否</label>
	</td>
</tr>
<tr class="even" nohover>
	<td align="center">
		<input type="submit" value="   保存   " accesskey="s">
		<input type="reset" value="   复位   ">
	</td>
</tr>
</tbody>
</table>
</form>
EOF;
			$this->kclass->page['title'] .= ' - 恢复 - '.$supplier['supplierno'];
			$this->kclass->page['main']=$this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 恢复供应商 - '.$supplier['supplierno'], 'right' => '<a href="/s.php?module=psupplier">返回列表</a>', 'body'=>$body));
		}
		// 供应商物资 add
		function updateMaterial(){
			$supplierid=$this->kclass->input['supplierid'];
			$supplier=$this->kclass->DB->queryFirst("
				SELECT `supplier`.*,
					m.username AS modifier,
					c.username AS creator,
					b.title AS businesstype,
					r.country,r.state,r.city
				FROM `supplier`
				LEFT JOIN `user` AS m ON (m.userid=supplier.modifier)
				LEFT JOIN `user` AS c ON (c.userid=supplier.creator)
				LEFT JOIN `businesstype` AS b ON (b.businesstypeid=supplier.businesstypeid)
				LEFT JOIN `region` AS r ON (r.regionid=supplier.regionid)
				WHERE `supplier`.killed=0 AND `supplier`.supplierid ='".$supplierid."'
				ORDER BY `supplier`.supplierid ASC,`supplier`.modified DESC,`supplier`.created DESC
				LIMIT 0,1
			");
			if($supplier){
				$supplier['country']=$this->kclass->iif($supplier['country'],$supplier['country'],'');
				$supplier['state']=$this->kclass->iif($supplier['state'],$supplier['state'],'');
				$supplier['city']=$this->kclass->iif($supplier['city'],$supplier['city'],'');
				$supplier['created']=date('Y-n-d',$supplier['created']);
				if($supplier['modified']!=0)$modify='，由'.$supplier['modifier'].'于'.date('Y-n-d',$supplier['modified']).'修改';
				//获得供应商的供应物资
				$materials=$this->kclass->DB->query("
					SELECT `suppliermaterial`.*,
						`material`.materialno,`material`.title,`material`.standard
					FROM `suppliermaterial` 
					LEFT JOIN `material` ON (`suppliermaterial`.materialid=`material`.materialid)
					WHERE `suppliermaterial`.killed=0 AND `suppliermaterial`.supplierid='".$supplierid."'
					ORDER BY supplierMaterialid ASC
				");
				$k=1;
				if($this->kclass->DB->numRows()){	
					while($material=$this->kclass->DB->fetchArray($materials)){
						$itemList.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').'">
							<td><input type="hidden" name="itemId['.$k.']" value="'.$material['supplierMaterialid'].'" />'.$this->kclass->chooserMaterial(array('hName'=>'itemMaterialid['.$k.']','hId'=>'itemMaterialid'.$k,'name'=>'itemMaterial['.$k.']','id'=>'itemMaterial'.$k,'width'=>400,'line'=>$k,'selectedid'=>$material['materialid'],'value'=>$material['materialno'].'　'.$material['title'].'　'.$material['standard'])).'</td>
							<td>'.$this->kclass->chooserMaterialVersion(array('name'=>'itemVersionid['.$k.']','selectedid'=>$material['versionid'],'width' =>80,'id'=>'itemVersionid'.$k,'materialid'=>$material['materialid'])).'</td>
							<td>'.$this->kclass->chooserBrand(array('name'=>'itemBrandid['.$k.']','hasBlank'=>1,'topname'=>'请选择','selectedid'=>$material['brandid'])).'</td>
							<td><input type="text" name="itemPrice['.$k.']" style="width:250px;" value="'.$material['price'].'" /></td>
							<td><input type="text" name="itemRemark['.$k.']" style="width:150px;" value="'.$material['remark'].'" /></td>
							<td><input type="checkbox" name="itemSelect['.$k.']" value="'.$material['supplierMaterialid'].'"></td>
						</tr>';
						$k++;
					}
					$itemList.='<tr><td colspan="8">新增物资</td></tr>';
				}
				for($i=$k;$i<$k+5;$i++){
					$itemList.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').'">
						<td>'.$this->kclass->chooserMaterial(array('hName'=>'itemMaterialid['.$i.']','hId'=>'itemMaterialid'.$i,'name'=>'itemMaterial['.$i.']','id'=>'itemMaterial'.$i,'width'=>600,'line'=>$i,'showVersion'=>1)).'</td>
						<td><select name="itemVersionid['.$i.']" id="itemVersionid'.$i.'" style="width:200px;"></select></td>
						<td>'.$this->kclass->chooserBrand(array('name'=>'itemBrandid['.$i.']','hasBlank'=>1,'topname'=>'请选择')).'</td>
						<td><input type="text" name="itemPrice['.$i.']" style="width:250px;" /></td>
						<td><input type="text" name="itemRemark['.$i.']" style="width:150px;" /></td>
						<td></td>
					</tr>';
				}

$body=<<<EOF
<div class="title"><span class="right small gray">由{$supplier['creator']}于{$supplier['created']}建立{$modify}。</span>{$supplier['title']}</div>
	{$supplierMaterialList}
<form action="/s.php?module=psupplier&action=doupdateMaterial" name="psupplier" method="post">
<input type="hidden" name="module" value="psupplier" />
<input type="hidden" name="action" value="doupdateMaterial" />
<input type="hidden" name="supplierid" value="{$supplierid}" />
<table class="hundred">
<thead></tr><th colspan="8">供应物资信息</th></tr></thead>
<tbody>
<tr class="center even" nohover><td>物资 <span class="bold red">*</span></td><td>版本 <span class="bold red">*</span></td><td>品牌</td><td>单价</td><td>备注</td><td>删</td></tr>
{$itemList}
<tr class="even">
<td class="small gray" colspan="8">
注意事项：
如果所列表格不够，那么在提交保存后再进行修改供应商</td>
</tr>
</tbody>
</table>
<table class="tlist tsuite" align="center" width="100%">
<tbody>
<tr class="odd" nohover>
<td class="center"><input type="submit" value="  提交  " accesskey="s""><input type="reset" value="  复位  "/></td>
</tr>
</tbody>
</table>
</form>
EOF;
			}
			$this->kclass->tbline+=4;
			$this->kclass->page['title'].='新建供应商供应的物资';
			$this->kclass->page['onload'].='dc.tabhover();';
			$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - <a href="/s.php?module=psupplier&action=view&supplierid='.$supplierid.'">查看供应商</a> - 新建供应商供应的物资', 'right'=>'<a href="/s.php?module=psupplier&action=view&supplierid='.$supplierid.'">返回查看供应商</a>','body'=>$body));
		}
		// insert
		function doupdateMaterial(){
			if($this->kclass->input['requestMethod'] != 'post'){
				$e='<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
			}
			if(isset($e)){
				$this->kclass->messager(array(
					'title' => '修改供应商供应的物资',
					'text' => '您在修改供应商供应的物资的过程中有以下错误：<ul>'.$e.'</ul>',
					'url' => 'javascript:history.back()',
					'sec' => 3
				));
			}

			$count=count($this->kclass->input['itemMaterialid'])+1;
			for($m=1;$m<$count;$m++){
				//insert supplier material item
				if($this->kclass->input['itemId'][$m]>0){
					if($this->kclass->input['itemSelect'][$m]>0 AND $this->kclass->input['itemSelect'][$m]==$this->kclass->input['itemId'][$m]){
						$this->kclass->DB->query("UPDATE `suppliermaterial` SET killed='".TIMENOW."',killer='".$this->kclass->user['userid']."' WHERE suppliermaterialid='".$this->kclass->input['itemSelect'][$m]."'");
					}else{
						if($this->kclass->input['itemMaterialid'][$m]>0){
							$this->kclass->DB->query("
								UPDATE suppliermaterial SET
									materialid='".$this->kclass->input['itemMaterialid'][$m]."',
									versionid='".$this->kclass->input['itemVersionid'][$m]."',
									supplierid='".$this->kclass->input['supplierid']."',
									brandid='".$this->kclass->input['itemBrandid'][$m]."',
									price='".$this->kclass->input['itemPrice'][$m]."',
									remark='".$this->kclass->input['itemRemark'][$m]."',
									modified='".TIMENOW."',
									modifier='".$this->kclass->user['userid']."'
								WHERE suppliermaterialid='".$this->kclass->input['itemId'][$m]."'
							");
						}
					}
				}else{
					if($this->kclass->input['itemMaterialid'][$m]>0){
						$this->kclass->DB->query("
							INSERT INTO suppliermaterial (materialid,versionid,supplierid,brandid,price,remark,created,creator)
							VALUES ('".$this->kclass->input['itemMaterialid'][$m]."','".$this->kclass->input['itemVersionid'][$m]."','".$this->kclass->input['supplierid']."','".$this->kclass->input['itemBrandid'][$m]."','".$this->kclass->input['itemPrice'][$m]."','".$this->kclass->input['itemRemark'][$m]."','".TIMENOW."','".$this->kclass->user['userid']."')
						");
					}
				}
			}
			$this->kclass->messager(array(
				'title' => '修改供应商供应的物资',
				'text' => '供应商供应的物资  已修改成功!',
				'url' => '/s.php?module=psupplier&action=view&supplierid='.$this->kclass->input['supplierid'],
				'sec' => 2
			));
		}
		// 对供应商评价
		function evaluate(){
			if($this->kclass->input['supplierid']<=0){
				$e='<li>系统数据错误，很抱歉！</li>';
			}
			$supplier=$this->kclass->DB->queryFirst("SELECT supplierno,title,supplierid FROM supplier WHERE supplierid='".$this->kclass->input['supplierid']."' LIMIT 0,1");
			if(!$supplier){
				$e.='<li>系统数据错误，很抱歉！</li>';
			}
			if(isset($e)){
				$this->kclass->messager(array(
					'title' => '给供应商评价',
					'text' => '您在给供应商评价的过程中有以下错误：<ul>'.$e.'</ul>',
					'url' => 'javascript:history.back()',
					'sec' => 3
				));
			}
			if($this->kclass->input['startDate']!=''){
				$st=explode('-',$this->kclass->input['startDate']);
				$startdate=mktime(0,0,0,$st[1],$st[2],$st[0]);
			}
			if($this->kclass->input['endDate']!=''){
				$ed=explode('-',$this->kclass->input['endDate']);
				$enddate=mktime(0,0,0,$ed[1],$ed[2],$ed[0]);
			}
			if($this->kclass->input['startDate']!='' and  $this->kclass->input['endDate']!=''){
				$inbounds=$this->kclass->DB->query("SELECT inboundid FROM `inbound` AS i WHERE killed=0  AND i.ifComplete=2  AND i.typeid=1 AND i.supplierid='".$this->kclass->input['supplierid']."' AND i.realtime>'".$startdate."' AND i.realtime<'".$enddate."'");
				if($this->kclass->DB->numRows()){
					$i=0;
					$qualityNum=0; $deliveryNum=0;$totalprice=0;$totalservice=0;
					while($inbound=$this->kclass->DB->fetchArray($inbounds)){
						$evaluates=$this->kclass->DB->query("SELECT * FROM `consignevaluate` WHERE supplierid=".$this->kclass->input['supplierid']." AND killed=0 AND ifApprove=1 AND inboundid='".$inbound['inboundid']."'");
						if($this->kclass->DB->numRows()){
							while($evaluate=$this->kclass->DB->fetchArray($evaluates)){
								if($evaluate['quality']==1){
									$qualityNum++; 
								}
								if($evaluate['delivery']==1){
									$deliveryNum++;
								}
								$totalprice+=$evaluate['price'];
								$totalservice+=$evaluate['service'];
								$i++;
							}
							$avgprice=round($totalprice/$i,2);
							$avgservice=round($totalservice/$i,2);
						}
						
					}
				}
			}
			/*$evaluates=$this->kclass->DB->query("
				SELECT * FROM `consignevaluate` WHERE supplierid=".$this->kclass->input['supplierid']." AND killed=0 AND ifApprove=1
			");
			if($this->kclass->DB->numRows()){
				$i=0;
				$qualityNum=0; $deliveryNum=0;$totalprice=0;$totalservice=0;
				while($evaluate=$this->kclass->DB->fetchArray($evaluates)){
					if($evaluate['quality']==1){
						$qualityNum++; 
					}
					if($evaluate['delivery']==1){
						$deliveryNum++;
					}
					$totalprice+=$evaluate['price'];
					$totalservice+=$evaluate['service'];
					$i++;
				}
				$avgprice=round($totalprice/$i,2);
				$avgservice=round($totalservice/$i,2);
			}*/

$body=<<<EOF
<dl id="supplier" class="tabs" style="display: block;">
<dt title="对供应商进行定期评价">定期评价</dt>
<dt title="现场考核评价供应商">现场考核</dt>
<dd>
<div class="title">{$supplier['title']}</div>
<form action="/s.php?module=psupplier&action=evaluate&supplierid={$this->kclass->input['supplierid']}" method="post" ><div ><span class="gray">选择入库时间范围：</span><input type="text" name="startDate" id="startDate">--<input type="text" name="endDate" id="endDate"> <input type="submit"  id="submitButton" value="提交" accesskey="s">	<span class="gray small">说明：该时间范围内入库单的评价结果作为评价供应商季度考核的依据</span></div></form>
<form action="/s.php?module=psupplier&action=doevaluate" name="supplier" method="post">
<input type="hidden" name="module" value="psupplier" />
<input type="hidden" name="action" value="doevaluate" />
<input type="hidden" name="supplierid" value="{$supplier['supplierid']}" />
<table class="hundred">
<thead></tr><th colspan="7">对供应商进行评价</th></tr></thead>
<tbody>
	<tr class="odd">
		<td width="100">产品质量：</td><td colspan="3"> 进料批数:<input type="text" name="allcount"  id="allcount" size="10" value="{$i}"> 合 格 批 数: <input type="text" name="qualified" id="qualified" size="10" value="{$qualityNum}">  合　格　率:<input type="text" name="acceptability" id="acceptability" size="10" value="" onclick="autoaccount()"> 最后得分:<input type="text" name="quality" id="quality" size="10"></td>
	</tr>
	<tr class="odd">
		<td width="80">发货速度：</td><td colspan="3">应交批数:<input type="text" name="need" id="need" size="10" value="{$i}"> 准时交货批数:<input type="text" name="reality" id="reality" size="10" value="{$deliveryNum}"> 交期达成率:<input type="text" name="reachrate" id="reachrate" size="10" onclick="autocaculate()"> 最后得分:<input type="text" name="delivery" id="delivery" size="10"></td>
	</tr>
	<tr class="even">
		<td>价格：</td><td><input type="text" name="price" id="price" width="80px" value="{$avgprice}"></td>
		<td>服务：</td><td><input type="text" name="service" id="service" width="80px" value="{$avgservice}"> 评价日期：<input type="text" id="evalutedate" name="evalutedate" value=""> <span class="gray small">该日期为评价供应商哪个时间内的等级，与评价时的当前时间无关</span></td>
	</tr>
	<tr class="odd">
		<td>备注：</td><td colspan="3"><textarea name="remark" style="width:600px;height:60px"></textarea></td>
	</tr>
	<tr class="odd">
		<td colspan="4" class="gray small"><span>评分细则：①每次评分的总分是100分,其中产品质量满分40分,发货速度满分30分,价格满分15分,服务满分15分。<br/>　　　　　并且产品质量的评分规则是根据合格批数/进料批数*100%,最后质量得分就是百分比*40;<br/>　　　　　发货速度的评分规则是根据准时交货批数/应交批数*100%,最后发货速度得分就是百分比*30。</span></td>
	</tr>
</tbody>
</table>
<table class="tlist tsuite" align="center" width="100%">
	<tbody>
		<tr class="odd" nohover>
		<td align="right"><input type="submit" value="  提交  " accesskey="s"></td>
		<td><input type="reset" value="  复位  "/></td>
		</tr>
	</tbody>
</table>
</form>
</dd>
<dd>
<div class="title">{$supplier['title']}</div></dd>
<script type="text/javascript">
	function autoaccount(){
		var allcount=document.getElementById('allcount').value;
		var qualified=document.getElementById('qualified').value;
		document.getElementById('acceptability').value=Math.round((qualified/allcount)*100);
	    document.getElementById('quality').value=Math.round((qualified/allcount)*40);
	}

	function autocaculate(){
		var need=document.getElementById('need').value;
		var reality=document.getElementById('reality').value;
		document.getElementById('reachrate').value=Math.round((reality/need)*100);
	    document.getElementById('delivery').value=Math.round((reality/need)*30);
	}
</script>
EOF;

			$this->kclass->tbline+=4;
			$this->kclass->page['title'].='给供应商评价';
			$this->kclass->page['onload'].='dc.tabhover();dc.tabs({\'id\':\'supplier\'});dc.show();$(\'#evalutedate\').datepicker();var dates=$(\'#startDate,#endDate\').datepicker({onSelect:function(selectedDate){var option=this.id==\'startDate\'?\'minDate\':\'maxDate\',instance=$(this).data(\'datepicker\'),date=$.datepicker.parseDate(instance.settings.dateFormat || $.datepicker._defaults.dateFormat,selectedDate,instance.settings);dates.not(this).datepicker(\'option\',option,date);}});';
			$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.='-<a href="/s.php?module=psupplier&action=view&supplierid='.$this->kclass->input['supplierid'].'">'.$supplier['title'].'</a> - 给供应商评价', 'right'=>'<a href="/s.php?module=psupplier&action=view&supplierid='.$this->kclass->input['supplierid'].'">返回查看供应商</a>','body'=>$body));
		}
		// 
		function updateBankDetail(){
			if($this->kclass->input['supplierid']<=0){
				$this->kclass->boinkIt('/s.php?module=psupplier');
			}
			$bankDetails=$this->kclass->DB->query("
				SELECT bankDetailid,bankAccountName,bank,bankAccount 
				FROM bankdetail 
				WHERE killed=0 AND supplierid='".$this->kclass->input['supplierid']."'
				ORDER BY bankDetailid ASC
			");
			$k=1;
			if($this->kclass->DB->numRows()){
				while($bankDetail=$this->kclass->DB->fetchArray($bankDetails)){
					$banktr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').'">
						<input type="hidden" name="bankDetailId['.$k.']" value="'.$bankDetail['bankDetailid'].'">
						<td><input type="text" name="bankAccountName['.$k.']" style="width:200px" value="'.$bankDetail['bankAccountName'].'" /></td>
						<td><input type="text" name="bank['.$k.']" style="width:400px"  value="'.$bankDetail['bank'].'" /></td>
						<td><input type="text" name="bankAccount['.$k.']" style="width:250px"  value="'.$bankDetail['bankAccount'].'" /></td>
						<td><input type="checkbox" name="bankDetailKill['.$k.']" value="'.$bankDetail['bankDetailid'].'"></td>
					</tr>';
					$k++;
				}
				$banktr.='<tr><td colspan="8">新增银行信息</td></tr>';
			}
			for($i=$k;$i<$k+5;$i++){
				$banktr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').'">
					<td><input type="text" name="bankAccountName['.$i.']" style="width:200px" value="'.$bankDetail['bankAccountName'].'" /></td>
					<td><input type="text" name="bank['.$i.']" style="width:400px"  value="'.$bankDetail['bank'].'" /></td>
					<td><input type="text" name="bankAccount['.$i.']" style="width:250px"  value="'.$bankDetail['bankAccount'].'" /></td>
					<td></td>
				</tr>';
			}
			$body=<<<EOF
<form action="/s.php?module=psupplier&action=doupdateBankDetail" name="psupplier" method="post">
<input type="hidden" name="module" value="psupplier">
<input type="hidden" name="action" value="doupdateBankDetail">
<input type="hidden" name="supplierid" value="{$this->kclass->input['supplierid']}">
<table class="hundred">
<thead>
<tr>
	<th colspan="4">修改供应商的银行资料</th>
</tr>
</thead>
<tbody>
<tr class="even center"><td>户名 <span class="red bold">*</span></td><td>开户行 <span class="red bold">*</span></td><td>银行账号 <span class="red bold">*</span></td><td>删</td></tr>
{$banktr}
<tr class="even" nohover>
	<td colspan="4" align="center">
		<input type="submit" id="submitButton" value="  保存  " accesskey="s">
		<input type="reset" value="  复位  ">
	</td>
</tr>
</tbody>
</table>
</form>
EOF;
			$this->kclass->page['title'] .= ' - 修改供应商的银行资料';
			$this->kclass->page['onload'] .= 'dc.tabhover()';
			$this->kclass->page['main']=$this->kclass->skin->dbody(array('title' => $this->baseurl . ' - <a href="/s.php?module=psupplier&action=view&supplierid='.$bankDetail['supplierid'].'">'.$bankDetail['supplier'].'</a> - 修改供应商的银行资料', 'right' => '<a href="/s.php?module=psupplier">返回列表</a>', 'body'=>$body));
		}
		// 
		function doupdateBankDetail(){
			if($this->kclass->input['requestMethod'] != 'post'){
				$e='<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
			}else{
				$count=count($this->kclass->input['bankAccountName'])+1;
				$hasItem=0;
				for($i=1;$i<$count;$i++){
					if($this->kclass->input['bankDetailKill'][$i]=='' AND $this->kclass->input['bankAccountName'][$i]!='' AND $this->kclass->input['bank'][$i]!='' AND $this->kclass->input['bankAccount'][$i]!=''){
						$hasItem=1;
					}
				}
				if($hasItem==0){
					$e.='<li>请至少填写一条 银行信息记录</li>';
				}
			}
			if(isset($e)){
				$this->kclass->messager(array(
					'title' => '修改供应商的银行资料',
					'text' => '您在修改供应商的银行资料的过程中有以下错误：<ul>'.$e.'</ul>',
					'url' => 'javascript:history.back()',
					'sec' => 3
				));
			}
			
			for($m=1;$m<$count;$m++){
				if($this->kclass->input['bankDetailId'][$m]>0){
					if($this->kclass->input['bankDetailKill'][$m]>0 AND $this->kclass->input['bankDetailKill'][$m]==$this->kclass->input['bankDetailId'][$m]){
						$this->kclass->DB->query("UPDATE bankdetail SET killed='".TIMENOW."',killer='".$this->kclass->user['userid']."' WHERE bankDetailid='".$this->kclass->input['bankDetailKill'][$m]."'");
					}else{
						if($this->kclass->input['bankAccountName'][$m]!='' AND $this->kclass->input['bank'][$m]!='' AND $this->kclass->input['bankAccount'][$m]!=''){
							$this->kclass->DB->query("
								UPDATE bankdetail SET 
									bankAccountName='".$this->kclass->input['bankAccountName'][$m]."',
									bank='".$this->kclass->input['bank'][$m]."',
									bankAccount='".$this->kclass->input['bankAccount'][$m]."',
									modified='".TIMENOW."',
									modifier='".$this->kclass->user['userid']."'
								WHERE bankDetailid='".$this->kclass->input['bankDetailId'][$m]."'
							");
						}
					}
				}else{
					if($this->kclass->input['bankAccountName'][$m]!='' AND $this->kclass->input['bank'][$m]!='' AND $this->kclass->input['bankAccount'][$m]!=''){
						$this->kclass->DB->query("
							INSERT INTO bankdetail (supplierid,bankAccountName,bank,bankAccount,created,creator) 
							VALUES ('".$this->kclass->input['supplierid']."','".$this->kclass->input['bankAccountName'][$m]."','".$this->kclass->input['bank'][$m]."','".$this->kclass->input['bankAccount'][$m]."','".TIMENOW."','".$this->kclass->user['userid']."')
						");
					}
				}
			}

			$this->kclass->messager(array(
				'title' => '修改供应商的银行资料',
				'text' => '供应商<b>'.$this->kclass->input['supplier'].'</b> 的银行已修改成功!',
				'url' => '/s.php?module=psupplier&action=view&supplierid='.$this->kclass->input['supplierid'],
				'sec' => 2
			));
		}
		//
		function doevaluate(){
			//echo $this->kclass->input['requestMethod'];
			if($this->kclass->input['requestMethod'] != 'post'){
				$e='<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
			}else{
				if($this->kclass->input['quality']>40 or $this->kclass->input['quality']<0){
					$e.='<li>产品的质量得分要在0分到40分之间，请重新填写</li>';
				}
				if($this->kclass->input['delivery']>30 or $this->kclass->input['delivery']<0){
					$e.='<li>发货速度得分要在0分到30分之间，请重新填写</li>';
				}
				if($this->kclass->input['price']>15 or $this->kclass->input['price']<0){
					$e.='<li>价格得分要在0分到15分之间，请重新填写</li>';
				}
				if($this->kclass->input['service']>15 or $this->kclass->input['service']<0){
					$e.='<li>服务得分要在0分到15分之间，请重新填写</li>';
				}
				if($this->kclass->input['evalutedate']==''){
					$e.='<li>请选择评价时间</li>';
				}
			}
			if($e){
				$this->kclass->messager(array(
					'title' => '供应商评级',
					'text' => '您在新建供应商评级的过程中有以下错误：<ul>'.$e.'</ul>',
					'url' => 'javascript:history.back()',
					'sec' => 3
				));
			}
			$supplier=$this->kclass->DB->queryFirst("SELECT supplierno,title,supplierid FROM supplier WHERE supplierid='".$this->kclass->input['supplierid']."' LIMIT 0,1");
			//$average=round(($this->kclass->input['quality']+$this->kclass->input['price']+$this->kclass->input['delivery']+$this->kclass->input['service'])/4,2);
			$sum=($this->kclass->input['quality']+$this->kclass->input['price']+$this->kclass->input['delivery']+$this->kclass->input['service']);
			if($sum>=90 and $sum<=100){
				$rank="A";
			}elseif($sum>=80 and $sum<90){
				$rank="B";
			}elseif($sum>=70 and $sum<80){
				$rank="C";
			}elseif($sum>0 and $sum<70){
				$rank="D";
			}else{
				$rank="";
			}
			if($this->kclass->input['evalutedate']!=''){
				$dd=explode("-",$this->kclass->input['evalutedate']);	
				$evaluatedate=mktime(0,0,0,$dd[1],$dd[2],$dd[0]);
			}
			$this->kclass->DB->query("
				INSERT INTO evaluate
					(supplierid,quality,price,delivery,service,allcount,qualified,acceptability,need,reality,reachrate,total,rank,remark,evaluatedate,created,creator)
				VALUES ('".$this->kclass->input['supplierid']."','".$this->kclass->input['quality']."','".$this->kclass->input['price']."','".$this->kclass->input['delivery']."','".$this->kclass->input['service']."','".$this->kclass->input['allcount']."','".$this->kclass->input['qualified']."','".$this->kclass->input['acceptability']."','".$this->kclass->input['need']."','".$this->kclass->input['reality']."','".$this->kclass->input['reachrate']."','".$sum."','".$rank."','".$this->kclass->input['remark']."','".$evaluatedate."','".TIMENOW."','".$this->kclass->user['userid']."')
			");

			
			//$supplierQuality=round($quality/$count,2);
			//$supplierPrice=round($price/$count,2);
			//$supplierDelivery=round($delivery/$count,2);
			//$supplierService=round($service/$count,2);
			//$supplierSituation=round($situation/$count,2);
			//$supplierAverage=round($average/$count,2);

			/*$this->kclass->DB->query("
				UPDATE supplier SET
					quality='".$supplierQuality."',
					price='".$supplierPrice."',
					delivery='".$supplierDelivery."',
					service='".$supplierService."',
					situation='".$supplierSituation."',
					average='".$supplierAverage."'
				WHERE supplierid='".$this->kclass->input['supplierid']."'
			");*/
			$this->kclass->messager(array(
				'title' => '对供应商进行评价',
				'text' => '供应商 <b>'.$supplier['title'].'</b> 已评论成功!',
				'url' => '/s.php?module=psupplier&action=view&supplierid='.$this->kclass->input['supplierid'],
				'sec' => 2
			));
		}
		 //供应商年终评级
		function evaluateofyear(){ 
			//echo $this->kclass->input['supplierid'];
			if($this->kclass->input['supplierid']<=0){
				$this->kclass->boinkIt('/s.php?module=psupplier');
			}
			$year=$this->kclass->input['year9'];
			if($year!=-100){
				$startdate=strtotime(date('Y-m-d', mktime(0,0,0,1,1,$year)));
				$enddate=strtotime(date('Y-m-t', mktime(0,0,0,1,1,$year+1)));
			}
			if($year==''){
				$year=date('Y');
				$startdate=strtotime(date('Y-m-d', mktime(0,0,0,1,1,$year)));
				$enddate=strtotime(date('Y-m-t', mktime(0,0,0,1,1,$year+1)));
			}
			//$today=date('Y-m-d');
			/*$date=$this->kclass->gettheYear($today);
			if($date[0]){
				$st=explode('-',$date[0]);
				$startdate=mktime(0,0,0,$st[1],$st[2],$st[0]);	
			}	
			if($date[1]){
				$ed=explode('-',$date[1]);
				$enddate=mktime(0,0,0,$ed[1],$ed[2],$ed[0]);	
			}*/
	
			$supplier=$this->kclass->DB->queryFirst("SELECT * FROM supplier WHERE supplierid=".$this->kclass->input['supplierid']."");
			if($this->kclass->input['year9']!=-100){
				$evaluates=$this->kclass->DB->query("SELECT * FROM evaluate WHERE killed=0 AND supplierid='".$this->kclass->input['supplierid']."' and evaluatedate>='".$startdate."' and evaluatedate<='".$enddate."' ORDER BY created DESC");
			}else{
				$evaluates=$this->kclass->DB->query("SELECT * FROM evaluate WHERE killed=0 AND supplierid='".$this->kclass->input['supplierid']."' ORDER BY evaluatedate DESC");
			}
			$nowYear=date('Y',TIMENOW);$nowMonth=date('n',TIMENOW);
			foreach($this->kclass->year AS $val){
				$year.='<option value="'.$val['value'].'"';
				if($val['value']==$nowYear){
					$year.='selected';
				}
				$year.='>'.$val['title'].'</option>';
			}
			$filter=<<<EOF
				<form action="/s.php?module=psupplier&action=evaluateofyear&supplierid={$this->kclass->input['supplierid']}" method="post" ><div ><span class="gray">筛选：</span><select name="year9" id="year9">{$year}</select>　<input type="submit"  id="submitButton" value="筛选" accesskey="s"></div></form>
EOF;
			if($evaluateList['num']=$this->kclass->DB->numRows()){
				$index=0;
				
				$evaluateList['panel'].=$filter.'<form action="/s.php?module=psupplier&action=doevaluateofyear&supplierid='.$this->kclass->input['supplierid'].'" name="psupplier" method="post" onsubmit="dc.checkSubmit(this)"><table class="hundred"><thead><tr><th colspan="11">当年度评价明细<span class="right"><input type="checkbox" name="checkall" id="selectAll" value=1 >全选 </span></th></tr>
		<tr><th>编号</th><th>评价日期</th><th>产品质量</th><th>发货速度</th><th>价格评价</th><th>服务</th><th>总分</th><th>等级</th><th>备注</th><th>选</th></tr></thead><tbody>';
				while($evaluate=$this->kclass->DB->fetchArray($evaluates)){
					$index+=1;
					$evaluate['created']=date('Y-m-d H:i',$evaluate['created']);
					$evaluate['evaluatedate']=date('Y-m-d H:i',$evaluate['evaluatedate']);
					$evaluateList['panel'].='<tr class="'.$this->kclass->rotateLine().'">
						<td>'.$index.'</td>
						<td>'.$evaluate['evaluatedate'].'</td>
						<td>'.$evaluate['quality'].'</td>
						<td>'.$evaluate['delivery'].'</td>
						<td>'.$evaluate['price'].'</td>
						<td>'.$evaluate['service'].'</td>
						<td>'.$evaluate['total'].'</td>
						<td>'.$evaluate['rank'].'</td>
						<td>'.$evaluate['remark'].'</td>
						<td><input class="select" type="checkbox" name="itemSelect['.$evaluate['evaluateid'].']" value="'.$evaluate['evaluateid'].'"></td></tr>';
						
					}
					/*$evaluateList['panel'].='<tr>
						<td colspan=2 align="right">合计：</td>
						<td>'.$supplier['quality'].'</td>
						<td>'.$supplier['price'].'</td>
						<td>'.$supplier['delivery'].'</td>
						<td>'.$supplier['service'].'</td>
						<td>'.$supplier['situation'].'</td>
						<td colspan=2>'.$supplier['average'].'</td>
						</tr>*/
					$evaluateList['panel'].='<tr class="odd" nohover><td class="center" colspan=12><input type="submit" id="submitButton" value="  提交  " accesskey="s"><input type="reset" value="  复位  "/></td>
</tr></tbody></table></from>';
					$evaluateList['count']='('.$evaluateList['num'].')';

				}else{
					$evaluateList['panel']=$filter."数据错误！";
					$evaluateList['off']='disabled';
				}
				$this->kclass->page['title'].='年终评审';
				$this->kclass->page['onload'].='dc.tabhover();dc.tabs({\'id\':\'supplier\'});dc.show();dc.selectAll()';
				$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.='- <a href="/s.php?module=psupplier&action=view&supplierid='.$this->kclass->input['supplierid'].'">'.$supplier['title'].'</a> - 年终评审', 'right'=>'<a href="/s.php?module=psupplier&action=view&supplierid='.$this->kclass->input['supplierid'].'">返回查看供应商</a>','body'=>$evaluateList['panel']));
		}
		//
		function doevaluateofyear(){
			if($this->kclass->input['requestMethod'] != 'post'){
				$e='<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
			}else{
				if($this->kclass->input['itemSelect']==''){
					$e='<li>请选择评级明细。</li>';
				}
			}
			if(isset($e)){
				$this->kclass->messager(array(
					'title' => '供应商年终评级',
					'text' => '你在进行供应商年终评级的过程中出现以下错误：<ul>'.$e.'</ul>',
					'url' => 'javascript:history.back()',
					'sec' => 3
				));		
			}
			$evaluateids=implode(",",$this->kclass->input['itemSelect']);
			$evaluate=$this->kclass->DB->query("SELECT * FROM evaluate WHERE evaluateid in(".$evaluateids.") and supplierid=".$this->kclass->input['supplierid']."");
			if($num=$this->kclass->DB->numRows()){
				$i=0;
				$totalquality=0;
				$totaldelivery=0;
				$totalservice=0;
				$totalprice=0;
				$total=0;
				while($evaluates=$this->kclass->DB->fetchArray($evaluate)){
					$totalquality+=$evaluates['quality'];
					$totaldelivery+=$evaluates['delivery'];
					$totalprice+=$evaluates['price'];
					$totalservice+=$evaluates['service'];
					$total+=$evaluates['total'];
					$i++;
				}
				$avgquality=round($totalquality/$i,2);
				$avgdelivery=round($totaldelivery/$i,2);
				$avgprice=round($totalprice/$i,2);
				$avgservice=round($totalservice/$i,2);
				$avgtotal=round($total/$i,2);
				if($avgtotal>=90 and $avgtotal<=100){
					$rank="A";
				}elseif($avgtotal>=80 and $avgtotal<90){
					$rank="B";
				}elseif($avgtotal>=70 and $avgtotal<80){
					$rank="C";
				}elseif($avgtotal>0 and $avgtotal<70){
					$rank="D";
				}else{
					$rank="";
				}
				$this->kclass->DB->query("
					UPDATE supplier SET 
						`quality`='".$avgquality."',
						`price`='".$avgprice."',
						`delivery`='".$avgdelivery."',
						`service`='".$avgservice."',
						`total`='".$avgtotal."',
						`rank`='".$rank."'
					WHERE supplierid=".$this->kclass->input['supplierid']."
				");
			}
			$this->kclass->messager(array(
				'title' => '供应商年终评级',
				'text' => '供应商年终评级成功！',
				'url' => '/s.php?module=psupplier&action=view&supplierid='.$this->kclass->input['supplierid'],
				'sec' => 2
			));	
		}
	
		function updateevaluate(){
			//echo $this->kclass->input['evaluateid'];
			if($this->kclass->input['evaluateid']<=0){
				$this->kclass->boinkIt('/s.php?module=psupplier');
			}
			$evaluate=$this->kclass->DB->queryFirst("
				SELECT * FROM `evaluate`
				WHERE evaluateid='".$this->kclass->input['evaluateid']."'
			");
			$supplier=$this->kclass->DB->queryFirst("
				SELECT supplierno,supplierid,title
				FROM `supplier`
				WHERE supplierid='".$this->kclass->input['supplierid']."'
			");
			//echo $evaluate['evaluatedate'];
			if($evaluate['evaluatedate']==0){
				$evaluatedate='2014-01-01';
			}else{
				$evaluatedate=date("Y-m-d",$evaluate['evaluatedate']);
			}
			$body=<<<EOF

<div class="title">{$supplier['title']}</div>
<form action="/s.php?module=psupplier&action=doupdateevaluate" name="supplier" method="post">
<input type="hidden" name="module" value="psupplier" />
<input type="hidden" name="action" value="doupdateevaluate" />
<input type="hidden" name="evaluateid" value="{$this->kclass->input['evaluateid']}" />
<input type="hidden" name="supplierid" value="{$supplier['supplierid']}" />
<table class="hundred">
<thead></tr><th colspan="7">修改供应商评价信息</th></tr></thead>
<tbody>
	<tr class="odd">
		<td width="100">产品质量：</td><td colspan="3"> 进料批数:<input type="text" name="allcount"  id="allcount" size="10" value="{$evaluate['allcount']}"> 合 格 批 数: <input type="text" name="qualified" id="qualified" size="10" value="{$evaluate['qualified']}">  合　格　率:<input type="text" name="acceptability" id="acceptability" size="10" value="{$evaluate['acceptability']}" onclick="autoaccount()"> 最后得分:<input type="text" name="quality" id="quality" size="10" value="{$evaluate['quality']}"></td>
	</tr>
	<tr class="odd">
		<td width="80">发货速度：</td><td colspan="3">应交批数:<input type="text" name="need" id="need" size="10" value="{$evaluate['need']}"> 准时交货批数:<input type="text" name="reality" id="reality" size="10" value="{$evaluate['reality']}"> 交期达成率:<input type="text" name="reachrate" id="reachrate" size="10" value="{$evaluate['reachrate']}" onclick="autocaculate()"> 最后得分:<input type="text" name="delivery" id="delivery" size="10" value="{$evaluate['delivery']}"></td>
	</tr>
	<tr class="even">
		<td>价格：</td><td><input type="text" name="price" id="price" width="80px" value="{$evaluate['price']}"></td>
		<td>服务：</td><td><input type="text" name="service" id="service" width="80px" value="{$evaluate['service']}"> 评价日期：<input type="text" id="evalutedate" name="evalutedate" value="{$evaluatedate}"> <span class="gray small">该日期为评价供应商哪个时间内的等级，与评价时的当前时间无关</span></td>
	</tr>
	<tr class="odd">
		<td>备注：</td><td colspan="3"><textarea name="remark" style="width:600px;height:60px"></textarea></td>
	</tr>
	<tr class="odd">
		<td colspan="4" class="gray small"><span>评分细则：①每次评分的总分是100分,其中产品质量满分40分,发货速度满分30分,价格满分15分,服务满分15分。<br/>　　　　　并且产品质量的评分规则是根据合格批数/进料批数*100%,最后质量得分就是百分比*40;<br/>　　　　　发货速度的评分规则是根据准时交货批数/应交批数*100%,最后发货速度得分就是百分比*30。</span></td>
	</tr>
</tbody>
</table>
<table class="tlist tsuite" align="center" width="100%">
	<tbody>
		<tr class="odd" nohover>
		<td align="right"><input type="submit" value="  提交  " accesskey="s"></td>
		<td><input type="reset" value="  复位  "/></td>
		</tr>
	</tbody>
</table>
</form>
<script type="text/javascript">
	function autoaccount(){
		var allcount=document.getElementById('allcount').value;
		var qualified=document.getElementById('qualified').value;
		document.getElementById('acceptability').value=Math.round((qualified/allcount)*100);
	    document.getElementById('quality').value=Math.round((qualified/allcount)*40);
	}

	function autocaculate(){
		var need=document.getElementById('need').value;
		var reality=document.getElementById('reality').value;
		document.getElementById('reachrate').value=Math.round((reality/need)*100);
	    document.getElementById('delivery').value=Math.round((reality/need)*30);
	}
</script>
EOF;
			$this->kclass->page['title'].='给供应商评价';
			$this->kclass->page['onload'].='dc.tabhover();dc.tabs({\'id\':\'supplier\'});dc.show();$(\'#evalutedate\').datepicker()';
			$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.='-<a href="/s.php?module=psupplier&action=view&supplierid='.$this->kclass->input['supplierid'].'">'.$supplier['title'].'</a> - 修改供应商评价信息', 'right'=>'<a href="/s.php?module=psupplier&action=view&supplierid='.$this->kclass->input['supplierid'].'">返回查看供应商</a>','body'=>$body));
		}

		function doupdateevaluate(){
			echo $this->kclass->input['supplierid'];
			if($this->kclass->input['requestMethod']!='post'){
				$e='<li>请从正确的页面进行操作，如果此问题持续存在，请联系网站管理员。</li>';
			}else{
				if($this->kclass->input['quality']>40 or $this->kclass->input['quality']<0){
					$e.='<li>产品的质量得分要在0分到40分之间，请重新填写</li>';
				}
				if($this->kclass->input['delivery']>30 or $this->kclass->input['delivery']<0){
					$e.='<li>发货速度得分要在0分到30分之间，请重新填写</li>';
				}
				if($this->kclass->input['price']>15 or $this->kclass->input['price']<0){
					$e.='<li>价格得分要在0分到15分之间，请重新填写</li>';
				}
				if($this->kclass->input['service']>15 or $this->kclass->input['service']<0){
					$e.='<li>服务得分要在0分到15分之间，请重新填写</li>';
				}
				if($this->kclass->input['evalutedate']==''){
					$e.='<li>请选择评价时间</li>';
				}
			}
			if(isset($e)){
				$this->kclass->messager(array(
					'title' => '修改供应商评级记录',
					'text' => '您在修改供应商评级记录的过程中有以下错误：<ul>'.$e.'</ul>',
					'url' => 'javascript:history.back()',
					'sec' => 3
				));
			}
			$sum=round($this->kclass->input['quality']+$this->kclass->input['delivery']+$this->kclass->input['price']+$this->kclass->input['service']/4,2);
			if($sum>=90 and $sum<=100){
				$rank="A";
			}elseif($sum>=80 and $sum<90){
				$rank="B";
			}elseif($sum>=70 and $sum<80){
				$rank="C";
			}elseif($sum>0 and $sum<70){
				$rank="D";
			}else{
				$rank="";
			}
			$dd=explode("-",$this->kclass->input['evalutedate']);
			$evaluatedate=mktime(0,0,0,$dd[1],$dd[2],$dd[0]);
			$this->kclass->DB->query("
				UPDATE `evaluate` SET 
					`quality`='".$this->kclass->input['quality']."',
					`delivery`='".$this->kclass->input['delivery']."',
					`price`='".$this->kclass->input['price']."',
					`service`='".$this->kclass->input['service']."',
					`allcount`='".$this->kclass->input['allcount']."',
					`qualified`='".$this->kclass->input['qualified']."',
					`acceptability`='".$this->kclass->input['acceptability']."',
					`need`='".$this->kclass->input['need']."',
					`reality`='".$this->kclass->input['reality']."',
					`reachrate`='".$this->kclass->input['reachrate']."',
					`total`='".$sum."',
					`rank`='".$rank."',
					`remark`='".$this->kclass->input['remark']."',
					`evaluatedate`='".$evaluatedate."'
				WHERE evaluateid='".$this->kclass->input['evaluateid']."'
			");
			$this->kclass->messager(array(
				'title' => '修改供应商评级记录',
				'text' => '供应商评级已修改成功',
				'url' => '/s.php?module=psupplier&action=view&supplierid='.$this->kclass->input['supplierid'],
				'sec' => 2
			));
		}

		function removeevaluate(){
			if($this->kclass->input['evaluateid']<=0){
				$this->kclass->boinkIt('/s.php?module=psupplier');
			}
			
$body=<<<EOF
<form action="/s.php?module=psupplier&action=killevaluate" name="supplier" method="post">
<input type="hidden" name="module" value="psupplier">
<input type="hidden" name="action" value="killevaluate">
<input type="hidden" name="evaluateid" value="{$this->kclass->input['evaluateid']}">
<input type="hidden" name="supplierid" value="{$this->kclass->input['supplierid']}">

<table><thead>
<thead>
<tr>
	<th>删除供应商评级记录：{$supplier['supplierno']}</th>
</tr>
</thead>
<tbody>
<tr class="even">
	<td class="middle">你确定要删除该供应商的评级记录吗?</td>
</tr>
<tr class="odd">
	<td align="center">
		<label for="c1" class="red"><input type="radio" id="c1" name="confirm" value="1">是</label>
		<label for="c0" class="green"><input type="radio" id="c0" name="confirm" value="0" checked>否</label>
	</td>
</tr>
<tr class="even" nohover>
	<td align="center">
		<input type="submit" value="   保存   " accesskey="s">
		<input type="reset" value="   复位   ">
	</td>
</tr>
</tbody>
</table>
</form>
EOF;
			$this->kclass->page['title'] .= ' - 删除 -  供应商评级记录';
			$this->kclass->page['main']=$this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 删除供应商评级记录 ', 'right' => '<a href="/s.php?module=psupplier">返回列表</a>', 'body'=>$body));

		}

		function killevaluate(){
			if($this->kclass->input['requestMethod']!='post'){
				$e='<li>请从正确的页面进行操作，如果此问题持续存在，请联系网站管理员。</li>';
			}else{
				if($this->kclass->input['evaluateid']<=0){
					$this->kclass->boinkIt('/s.php?module=psupplier');
				}
				if($this->kclass->input['confirm'] == 0){
					$this->kclass->boinkIt('/s.php?module=psupplier&action=view&supplierid='.$this->kclass->input['supplierid']);
				}
			}
			if(isset($e)){
				$this->kclass->messager(array(
					'title' => '删除供应商评级记录',
					'text' => '您在删除供应商评级记录的过程中有以下错误：<ul>'.$e.'</ul>',
					'url' => 'javascript:history.back()',
					'sec' => 3
				));
			}
			$this->kclass->DB->query("
				UPDATE `evaluate`
				SET killed=".TIMENOW.",killer='".$this->kclass->user['userid']."'
				WHERE evaluateid='".$this->kclass->input['evaluateid']."'
			");
			
			$this->kclass->messager(array(
				'title' => '删除供应商评级记录成功',
				'text' => '供应商评级记录已被成功标记为删除！',
				'url' => '/s.php?module=psupplier&action=view&supplierid='.$this->kclass->input['supplierid'],
				'sec' => 2
			));
			
		}

		//显示所有供应商对应的入库交货明细记录
		function inbounditemlist(){
			//规定数据查询起始数$startNum
			if($this->kclass->input['startno']!=""){
				if($this->kclass->input['startno']>=0){
					$startNum = $this->kclass->input['startno'];
				}else{
					$startNum = 0;
				}
			}else{
				$startNum = 0;
			}
			$inbounds=$this->kclass->DB->query("
				SELECT i.inboundid,i.inboundno,i.supplierid,i.porderid,
					s.title AS supplier,
					po.orderno AS porder
				FROM `inbound` AS i
				LEFT JOIN `supplier` s ON (s.supplierid=i.supplierid)
				LEFT JOIN `porder` po ON (po.porderid=i.porderid)
				WHERE i.killed=0 AND i.ifVerify=1 AND i.ifComplete=2 AND i.typeid=1 AND i.supplierid<>0 ORDER BY i.created DESC LIMIT {$startNum},60
			");	
			if($this->kclass->DB->numRows()){
				$m=0;
				while($inbound=$this->kclass->DB->fetchArray($inbounds)){
					$symbol = 0;
					$item="";
					$evaluates=$this->kclass->DB->query("SELECT * FROM `consignevaluate` WHERE supplierid='".$inbound['supplierid']."' AND inboundid='".$inbound['inboundid']."' AND killed=0 ORDER BY dateline DESC  
					");
					if($this->kclass->DB->numRows()){
						$symbol=1;
						$i=1;
						//供应商评价记录列表
						$item.='
						<tr><th colspan="12">供应商评价记录</th></tr>
						<tr><th>编号</th><th>评价日期</th><th>产品质量(40)</th><th>发货速度(30)</th><th>价格评价(15)</th><th>服务(15)</th><th colspan="4">备注</th><th colspan="2">操作</th></tr>
						';
						while($evaluate=$this->kclass->DB->fetchArray($evaluates)){
							$operation="";
							if($evaluate['ifApprove']==0){
								if($evaluate['ifVerify']==0){ 
									$operation.='<a href="/s.php?module=psupplier&action=verifyconsignevaluate&consignevaluateid='.$evaluate['consignevaluateid'].'">审核</a>　<a href="/s.php?module=psupplier&action=killconsignevaluate&consignevaluateid='.$evaluate['consignevaluateid'].'">删除</a>';
								}elseif($evaluate['ifVerify']==1){
									$operation.='<a href="/s.php?module=psupplier&action=approveconsignevaluate&consignevaluateid='.$evaluate['consignevaluateid'].'">审批</a>　<a href="/s.php?module=psupplier&action=killconsignevaluate&consignevaluateid='.$evaluate['consignevaluateid'].'">删除</a>';
									$symbol = 2;
								}
							}else{
								$operation.='　';
								$symbol = 3;
							}
							if($evaluate['created']!=0){
								$evaludate=date('Y-m-d',$evaluate['created']);
							}
							$item.='<tr class=odd>
								<td>'.$i.'</td>
								<td>'.$evaludate.'</td>
								<td>'.$evaluate['quality'].'</td>
								<td>'.$evaluate['delivery'].'</td>
								<td>'.$evaluate['price'].'</td>
								<td>'.$evaluate['service'].'</td>
								<td colspan=4>'.$evaluate['remark'].'</td>
								<td colspan=3>'.$operation.'</td>
								</tr>';
							$i++;
						}
						//$item.="</tbody>";
					}
					$inboundItemtr=''; //获取入库单的入库批次列表
					$inboundItems=$this->kclass->DB->query("
						SELECT ii.itemid,ii.batchid,ii.barcodeid,ii.materialid,ii.materialitemid,ii.versionid,ii.created,
							bt.batchno,
							bc.barcode,
							mv.title AS version,
							mem.realname AS creator
						FROM inbounditem AS ii
						LEFT JOIN batch AS bt ON (ii.batchid=bt.batchid)
						LEFT JOIN barcode AS bc ON (bc.barcodeid=ii.barcodeid)
						LEFT JOIN material AS m ON (m.materialid=ii.materialid)
						LEFT JOIN materialversion AS mv ON (mv.versionid=ii.versionid)
						LEFT JOIN preinbounditem AS pii ON (pii.itemid=ii.preitemid)
						LEFT JOIN member AS mem ON (mem.userid=ii.creator)
						WHERE ii.killed=0 AND ii.inboundid='".$inbound['inboundid']."'
						ORDER BY m.materialno ASC
					");
					if($this->kclass->DB->numRows()){
						$length = $this->kclass->DB->numRows();
						$i=1;
						$inboundItemtr='<tr class="even center"><td width="20">ID</td><td width="80">编号</td><td width="300">名称/属性</td><td width="45">采购数</td><td width="35">单价</td><td width="80">承诺交期</td><td width="45">签收人</td><td width="45">已入数</td><td width="80">入库时间</td><td colspan="3">评分</td></tr>';
						while($inboundItem=$this->kclass->DB->fetchArray($inboundItems)){
							$in=$this->kclass->DB->queryFirst("SELECT SUM(quantity) AS count FROM inbounditem WHERE killed=0 AND itemid='".$inboundItem['itemid']."'");
							$batch=$this->kclass->DB->queryFirst("SELECT * FROM batch WHERE killed=0 AND batchid='".$inboundItem['batchid']."'");
							$materialInfo=$this->kclass->getMaterial(array('materialid'=>$inboundItem['materialid'],'itemid'=>$inboundItem['materialitemid']));
							if($materialInfo['ifPerBarcode']==1);{
								$status='';
								$startno=$this->kclass->DB->queryFirst("
									SELECT number
									FROM childBarcode 
									WHERE versionid={$inboundItem['versionid']} AND inboundid={$inbound['inboundid']} 
									ORDER BY title ASC 
									LIMIT 0,1
								");
								$endno=$this->kclass->DB->queryFirst("
									SELECT number
									FROM childBarcode 
									WHERE versionid={$inboundItem['versionid']} AND inboundid={$inbound['inboundid']} 
									ORDER BY title DESC 
									LIMIT 0,1
								");
								$porderinfo=$this->kclass->DB->queryFirst("SELECT quantity,dateline,price,price1 FROM item WHERE materialid={$materialInfo['materialid']} AND mid={$inbound['porderid']}");
								$price=$this->kclass->iif($porderinfo['price']!=0,$porderinfo['price'],$porderinfo['price1']);
								$perCost[$m][]=$price*$porderinfo['quantity'];
								$childBarcode=$this->kclass->DB->queryFirst("SELECT COUNT(*) AS count FROM childBarcode WHERE versionid={$inboundItem['versionid']} AND inboundid={$inbound['inboundid']} AND barcodeid={$inboundItem['barcodeid']} LIMIT 0,1");
								$info='共<b>'.$this->kclass->iif($childBarcode['count'],$childBarcode['count'],0).'</b>条';
								if($childBarcode['count']!=$in['count']){
									$quantity=$in['count']-$childBarcode['count'];
									$status='<a href="javascript:dc.barcode.addBarcode(\'item'.$i.'\')">生成流水条码</a>';
								}else{
									$status='生成完成';
								}
							}
							
							$score = $this->kclass->DB->queryFirst("SELECT totalScore FROM `consignevaluateitem` WHERE materialid='".$materialInfo['materialid']."'");
							if($i==1){
								$remarkTd = '<td align="center" width="100" class="display2 display3" style="display:none" rowspan="'.$length.'"><textarea class="clearText" name="remark" rows="'.$length.'" cols="30">(备注信息)</textarea></td>';
								$submitTd = '<td align="center" width="60" class="display2 display3" style="display:none" rowspan="'.$length.'"><input type="submit" value="提交" size="5" /></td>';
								$show = 'show1';
							}else{
								$remarkTd = '';
								$submitTd = '';	
								$show = 'show2';
							}
							//操作面板
							if($score['totalScore']!=null){
								$options = '
								<input type="hidden" name="materialid['.$i.']" value="'.$materialInfo['materialid'].'" >
								<span class="display1" style="">分数：'.$score['totalScore'].' <a href="javascript:void(0)" class="addInputs" onclick="dc.addInputs.'.$show.'(this)">(修改)</a></span>
								<span class="display2" style="display:none">
								<input type="text" name="date['.$i.']" class="clearText date" value="到货时间" size="8" />
								<select name="deliverytime['.$i.']">
									<option selected="selected" size="4" value="">交货</option>
									<option value="30">准   时</option><option value="0">不准时</option>
								</select>
								<select name="quality['.$i.']">
									<option selected="selected" size="4" value="">质量</option>
									<option value="40">合   格</option><option value="0">不合格</option>
								</select> 								
								<input type="text" name="price['.$i.']" id="price" class="clearText" value="价格" size="4" />
								<input type="text" name="service['.$i.']" id="service" class="clearText" value="服务" size="4" /> 
								<input type="text" name="dateline['.$i.']" class="clearText dateline" value="评价日期" size="8" />
								</span>
								';					
							}else{
								$options = '
								<input type="hidden" name="materialid['.$i.']" value="'.$materialInfo['materialid'].'" >
								<span class="display1" style=""><a href="javascript:void(0)" class="addInputs" onclick="dc.addInputs.'.$show.'(this)">(评价)</a></span>
								<span class="display2" style="display:none">
								<input type="text" name="date['.$i.']" class="clearText date" value="到货时间" size="8" />
								<select name="deliverytime['.$i.']">
									<option selected="selected" size="4" value="">交货</option>
									<option value="30">准   时</option><option value="0">不准时</option>
								</select> 
								<select name="quality['.$i.']">
									<option selected="selected" size="4" value="">质量</option>
									<option value="40">合   格</option><option value="0">不合格</option>
								</select> 								
								<input type="text" name="price['.$i.']" id="price" class="clearText" value="价格" size="4" />
								<input type="text" name="service['.$i.']" id="service" class="clearText" value="服务"  size="4" /> 
								<input type="text" name="dateline['.$i.']" class="clearText dateline" value="评价日期" size="8" />
								</span>
								';						
							}
							//入库单订单详细信息列表/
							$inboundItemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').'" id="item'.$i.'">
								<td><input type="hidden" name="itemid" value="'.$inboundItem['itemid'].'">
									<input type="hidden" name="versionid" value="'.$inboundItem['versionid'].'">
									<input type="hidden" name="quantity" value="'.$quantity.'">
									<input type="hidden" name="barcodeid" value="'.$inboundItem['barcodeid'].'">'.$i.'</td>
								<td><a href="'.$materialInfo['url'].'">'.$materialInfo['no'].'</a></td>
								<td>'.$materialInfo['material'].'　'.$materialInfo['standard'].'</td>
								<td align="center">'.$porderinfo['quantity'].'</td>
								<td align="center">'.$price.'</td>	
								<td class="date1">'.date('Y-m-d',$porderinfo['dateline']).'</td>								
								<td>'.$inboundItem['creator'].'</td>								
								<td align="center">'.$this->kclass->iif($in['count'],$in['count'],0).'</td>
								<td align="center">'.date('Y-m-d',$inboundItem['created']).'</td>
								<td align="center">'.$options.'</td>
								'.$remarkTd.'
								'.$submitTd.'
								</tr>
								';
							$i++;
							$totalCost=array_sum($perCost[$m]);
						}
					}
					//评价状态
					if($symbol==1){
						$options = '<span style="color:#D00058">待审核</span>';					
					}elseif($symbol==2){
						$options = '<span style="color:#D00058">待审批</span>';						
					}elseif($symbol==3){
						$options = '<span>已评价</span>';						
					}else{
						$options = '<span style="color:#0068A5">未评价</span>';						
					}
					//标题信息
					$hasevalueated = "";
					$title = '';					
					if($inbound['inboundno']!='')$title.='　<span class="small">入库单号：</span><a href="/s.php?module=inbound&action=view&inboundid='.$inbound['inboundid'].'">'.$inbound['inboundno'].'</a>';
					if($inbound['porderid']!='')$title.='　<span class="small">订单号：</span><a href="/s.php?module=porder&action=view&porderid='.$inbound['porderid'].'">'.$inbound['porder'].'</a>';
					if($inbound['supplier']!='')$title.='　<span class="small">供应商：</span>'.$inbound['supplier'];
					if($totalCost!=null)$title.='　<span class="small">费用：</span>'.sprintf("%01.2f",$totalCost);
					//判断是否有评价记录
					$ifevaluates = $this->kclass->DB->query("SELECT consignevaluateid FROM consignevaluate WHERE porderid='".$inbound['porderid']."'");
					//确定表单提交的目标
					if($this->kclass->DB->numRows()>0){
						$action = 'doupdateconsignevaluate';
					}else{
						$action = 'doevaluatesupplier';
					}
					$body .= <<<EOF
<form action="/s.php?module=psupplier&action={$action}" name="evaluate "method="post">
<input type="hidden" name="supplierid" value="{$inbound['supplierid']}" />
<input type="hidden" name="inboundid" value="{$inbound['inboundid']}" />
<input type="hidden" name="porderid" value="{$inbound['porderid']}" />
<input type="hidden" name="length" value="{$length}" />
<table class="hundred">
<thead>
<tr><th colspan="12"><span class="right">{$options} <span class="plus" onclick="dc.showInquiry('inbound{$inbound['inboundid']}')"></span></span>	<span class="middle bold">{$inquiry['contact']}</span>{$title}</th></tr>
</thead>
<tbody id="inbound{$inbound['inboundid']}" style="display:none">
{$inboundItemtr}{$item}
</tbody>
</table>
</form>
EOF;
				$m++;
				}
				//列表分页
				$counter = $this->kclass->DB->queryFirst("SELECT COUNT(inboundid) FROM `inbound` AS i WHERE i.killed=0 AND i.ifVerify=1 AND i.ifComplete=2 AND i.ifEvaluate=0 AND i.typeid=1 AND i.supplierid<>0");
				$count = $counter['COUNT(inboundid)'];
				$pagesize = 60;
				$topage=$this->kclass->commonPaging(array('module'=>'psupplier','action'=>'inbounditemlist','count'=>$count,'pagesize'=>$pagesize,'startNum'=>$startNum));
				$body .= '<table width="100%"><tr><td align="center">'.$topage.'</td></tr></table>';
			}else{
				$body="数据错误！";
			}
			$this->kclass->page['onload']='dc.tabhover();$(\'.date\').datepicker();$(\'.dateline\').datepicker();dc.clearText();dc.timeCompare()';
			$this->kclass->page['title']='供应商入库明细列表';
			$this->kclass->page['main']=$this->kclass->skin->dbody(array('title' =>$this->baseurl.='- <a href="/s.php?module=psupplier&action=list" >供应商列表</a> - 供应商入库明细列表',  'body'=>$body));
		}
		
		//
		function evaluatesupplier(){
			if(!$this->kclass->input['inboundid']){
				$this->kclass->boinkIt('/s.php?module=psupplier');
			}else{
				$inbound=$this->kclass->DB->queryFirst("SELECT * FROM `inbound` WHERE inboundid='".$this->kclass->input['inboundid']."'");
			}
			$body=<<<EOF
<form action="/s.php?module=psupplier&action=doevaluatesupplier" name="evaluate "method="post">
<input type="hidden" name="module" value="psupplier">
<input type="hidden" name="action" value="doevaluatesupplier">
<input type="hidden" name="materialid" value="{$this->kclass->input['materialid']}" >
<input type="hidden" name="supplierid" value="{$this->kclass->input['supplierid']}" >
<input type="hidden" name="inboundid" value="{$this->kclass->input['inboundid']}" >
<input type="hidden" name="porderid" value="{$inbound['porderid']}" >
<table class="hundred">
<thead><tr><th colspan="4">评价交货情况</th></tr></thead>
<tbody>
<tr class="odd">
<td width="100">是否准时交货：</td><td width="450"><input type="radio" name="deliverytime" value="0" checked="true"/>否　<input type="radio" name="deliverytime" value="30" />是</td>
<td width="100">质量是否合格：</td><td><input type="radio" name="quality" value="0" checked="true"/>否　<input type="radio" name="quality" value="40" />是</td>
</tr>
<tr class="even">
<td>价　　　　格：</td><td><input type="text" name="price" id="price" value=""></td><td>服　　　　务:</td><td><input type="text" name="service" id="service" value="">   评价日期：<input type="text" name="dateline" id="dateline" value=""></td>
</tr>
<tr class="even">
<td>备注信息：</td><td colspan="3"><textarea name="remark" style="width:560px;height:60px"></textarea></td>
</tr>
</tbody>
</table>
</table>
<table class="tlist tsuite" align="center" width="100%">
<tbody>
<tr colspan="4" class="even" nohover>
<td class="center"><input type="submit" id="submitButton" value="  提交  " accesskey="s"><input type="reset" value="  复位  "/></td>
</tr>
</tbody>
</table>
EOF;
			$this->kclass->page['title'].='评价供应商';
			$this->kclass->page['onload']='dc.tabhover();dc.show();$(\'#dateline\').datepicker()';
			$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - <a href="/s.php?module=psupplier&action=inbounditemlist">入库单列表</a> - 查看入库单','body' => $body));
		}

		function doevaluatesupplier(){
			//echo $this->kclass->input['supplierid'];
			if($this->kclass->input['requestMethod'] != 'post'){
				$e='<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员!</li>';
			}else{
				if($this->kclass->input['length']!==''){
					for($i=1;$i<=$this->kclass->input['length'];$i++){
						if($this->kclass->input['deliverytime'][$i]==''){
							$e.='<li>请选择准时交货最终评价结果!</li>';
						}	
						if($this->kclass->input['quality'][$i]==''){
							$e.='<li>请选择质量标准最终评价结果!</li>';
						}
						if($this->kclass->input['price'][$i]=='' or !is_numeric($this->kclass->input['price'][$i])){
							$e.='服务得分不能为空或价格输入有误，请重新输入!';
						}						
						if($this->kclass->input['service'][$i]=='' or !is_numeric($this->kclass->input['service'][$i])){
							$e.='<li>服务得分不能为空或价格输入有误，请重新输入!</li>';
						}
						if($this->kclass->input['dateline'][$i]==''){
							$e.='<li>请输入评价时间</li>';
						}
						if($this->kclass->input['price'][$i]>15 or $this->kclass->input['price'][$i]<0){
							$e.='<li>价格得分要在0分到15分之间，请重新填写!</li>';
						}
						if($this->kclass->input['service'][$i]>15 or $this->kclass->input['service'][$i]<0){
							$e.='<li>服务得分要在0分到15分之间，请重新填写!</li>';
						}
						if($this->kclass->input['dateline'][$i]==''){
							$e.='<li>请选择评价时间!</li>';
						}
					}
				}else{
					$e.='<li>没有入库物资...!</li>';
				}
			}
			if(isset($e)){
				$this->kclass->messager(array(
					'title' => '评价供应商',
					'text' => '您在修改供应商的过程中有以下错误：<ul>'.$e.'</ul>',
					'url' => 'javascript:history.back()',
					'sec' => 3
				));	
			}			
			if($this->kclass->input['length']!==''){
				for($i=1;$i<=$this->kclass->input['length'];$i++){
					$dd=explode("-",$this->kclass->input['dateline'][$i]);
					$dateline=mktime(0,0,0,$dd[1],$dd[2],$dd[0]);
					$totalScore=$this->kclass->input['price'][$i]+$this->kclass->input['quality'][$i]+$this->kclass->input['service'][$i]+$this->kclass->input['deliverytime'][$i];
					//建立供应商对应物资评价记录
					$this->kclass->DB->query("
						INSERT INTO `consignevaluateitem`
						(materialid,inboundid,porderid,suplierid,priceScore,serviceScore,deliveryScore,qualityScore,totalScore,creator,created)
						VALUES(
						'".$this->kclass->input['materialid'][$i]."',
						'".$this->kclass->input['inboundid']."',
						'".$this->kclass->input['porderid']."',
						'".$this->kclass->input['supplierid']."',
						'".$this->kclass->input['price'][$i]."',
						'".$this->kclass->input['service'][$i]."',
						'".$this->kclass->input['deliverytime'][$i]."',
						'".$this->kclass->input['quality'][$i]."',
						'".$totalScore."',
						'".$this->kclass->user['userid']."',
						'".TIMENOW."'
						)
					");			
				}			
			}
			//查询所有供应商物资评价条目
			$scores=$this->kclass->DB->queryFirst("
				SELECT AVG(priceScore),AVG(serviceScore),AVG(deliveryScore),AVG(qualityScore)
				FROM `consignevaluateitem`
				WHERE porderid='".$this->kclass->input['porderid']."'
			");
			if($this->kclass->input['remark']!=='(备注信息)'){
				$remark = $this->kclass->input['remark'];
			}else{
				$remark = '';
			}
			//创建供应商评价记录
			$this->kclass->DB->query("
				INSERT INTO `consignevaluate`
				(supplierid,inboundid,porderid,price,quality,delivery,service,dateline,remark,creator,created)
				VALUES(
				'".$this->kclass->input['supplierid']."',
				'".$this->kclass->input['inboundid']."',
				'".$this->kclass->input['porderid']."',
				'".$scores['AVG(priceScore)']."',
				'".$scores['AVG(qualityScore)']."',
				'".$scores['AVG(deliveryScore)']."',
				'".$scores['AVG(serviceScore)']."',
				'0',
				'".$remark."',
				'".$this->kclass->user['userid']."',
				'".TIMENOW."'
				)
			");
			$this->kclass->messager(array(
				'title' => '评价供应商',
				'text' => '供应商评价成功',
				'url' => '/s.php?module=psupplier&action=inbounditemlist',
				'sec' => 2
			));	

		}
		//审核评价记录
		function verifyconsignevaluate(){
			if(!$this->kclass->input['consignevaluateid']){
				$this->kclass->boinkIt('/s.php?module=psupplier');
			}
			$evaluate=$this->kclass->DB->queryFirst("SELECT * FROM `consignevaluate` WHERE consignevaluateid='".$this->kclass->input['consignevaluateid']."'");
			if($evaluate['dateline']){
				$dateline=date('Y-m-d',$evaluate['dateline']);
			}
			if($evaluate['quality']==0){
				$quality="否";
			}else{
				$quality="是";
			}
			if($evaluate['delivery']==0){
				$delivery="否";
			}else{
				$delivery="是";
			}
			$body=<<<EOF
<table class="hundred">
<thead><tr><th colspan="4">评价交货情况</th></tr></thead>
<tbody>
<tr class="odd">
<td width="100">是否准时交货：</td><td width="450">{$quality}</td>
<td width="100">质量是否合格：</td><td>{$delivery}</td>
</tr>
<tr class="even">
<td>价　　　　格：</td><td>{$evaluate['price']}</td><td>服　　　　务:</td><td>{$evaluate['service']}　　　　　评价日期：{$dateline}</td>
</tr>
<tr class="even">
<td>备注信息：</td><td colspan="3">{$evaluate['remark']}</td>
</tr>
</tbody>
</table>
</table>
<form method="post" action="/s.php?module=psupplier&action=doverifyconsignevaluate">
<input type="hidden" name="module" value="psupplier">
<input type="hidden" name="action" value="doverifyconsignevaluate">
<input type="hidden" name="consignevaluateid" value="{$this->kclass->input['consignevaluateid']}">
<input type="hidden" name="supplierid" value="{$evaluate['supplierid']}">
<table>
<thead><tr><th colspan="4">审核</th></tr></thead>
<tbody>
<tr class="even">
	<td>审　　核：<span class="bold red">*</span></td><td class="center"><input type="radio" name="ifVerify" value="1" />通过　　　<input type="radio" name="ifVerify" value="-1" checked />不通过</td>
</tr>
<tr class="odd">
	<td>审核备注：</td><td><textarea name="verifyRemark" style="width:440px;height:110px;"></textarea></td>
</tr>
<tr class="odd">
	<td colspan=2 class="center"><input type="submit" value=" 提交 " /><input type="reset" value="重置" /></td>
</tr>
</tbody>
</table>
</form>
EOF;
			$this->kclass->page['title'].='审核供应商评价';
			$this->kclass->page['onload'].='dc.tabhover();';
			$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - <a href="/s.php?module=psupplier&action=inbounditemlist">供应商入库明细列表</a> - 审核供应商评价记录','body'=>$body));
		}

		function doverifyconsignevaluate(){
			if($this->kclass->input['requestMethod'] != 'post'){
				$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
			}else{
				if($this->kclass->input['ifVerify']!=1 AND $this->kclass->input['ifVerify']!=-1){
					$e.='<li>请选择供应商评价单中的 是否通过审核 按钮。</li>';
				}
			}
			if(isset($e)){
				$this->kclass->messager(array(
					'title' => '审核供应商评级记录单',
					'text' => '您在审核供应商评级记录单的过程中有以下错误：<ul>'.$e.'</ul>',
					'url' => 'javascript:history.back()',
					'sec' => 3
				));
			}
			$this->kclass->DB->query("
				UPDATE consignevaluate SET
					ifVerify='".$this->kclass->input['ifVerify']."',
					verifier='".$this->kclass->user['userid']."',
					verified='".TIMENOW."',
					verifyRemark='".$this->kclass->input['verifyRemark']."'
				WHERE consignevaluateid='".$this->kclass->input['consignevaluateid']."'
			");
			if($this->kclass->input['ifVerify']==1){
				$verify='已通过';
			}elseif($this->kclass->input['ifVerify']==-1){
				$verify='未通过';
			}
			$this->kclass->messager(array(
				'title' => '审核供应商评价记录',
				'text' => '供应商评价记录 '.$verify.'审核!返回进行审批操作',
				'url' => '/s.php?module=psupplier&action=inbounditemlist',
				'sec' => 2
			));
		}
		
		function approveconsignevaluate(){
			if(!$this->kclass->input['consignevaluateid']){
				$this->kclass->boinkIt('/s.php?module=psupplier');
			}
			$evaluate=$this->kclass->DB->queryFirst("SELECT * FROM `consignevaluate` WHERE consignevaluateid='".$this->kclass->input['consignevaluateid']."'");
			if($evaluate['dateline']){
				$dateline=date('Y-m-d',$evaluate['dateline']);
			}
			if($evaluate['quality']==0){
				$quality="否";
			}else{
				$quality="是";
			}
			if($evaluate['delivery']==0){
				$delivery="否";
			}else{
				$delivery="是";
			}
			$body=<<<EOF
<table class="hundred">
<thead><tr><th colspan="4">评价交货情况</th></tr></thead>
<tbody>
<tr class="odd">
<td width="100">是否准时交货：</td><td width="450">{$quality}</td>
<td width="100">质量是否合格：</td><td>{$delivery}</td>
</tr>
<tr class="even">
<td>价　　　　格：</td><td>{$evaluate['price']}</td><td>服　　　　务:</td><td>{$evaluate['service']}　　　　　评价日期：{$dateline}</td>
</tr>
<tr class="even">
<td>备注信息：</td><td colspan="3">{$evaluate['remark']}</td>
</tr>
</tbody>
</table>
</table>
<form method="post" action="/s.php?module=psupplier&action=doapproveconsignevaluate">
<input type="hidden" name="module" value="psupplier">
<input type="hidden" name="action" value="doapproveconsignevaluate">
<input type="hidden" name="consignevaluateid" value="{$this->kclass->input['consignevaluateid']}">
<input type="hidden" name="supplierid" value="{$evaluate['supplierid']}">
<table>
<thead><tr><th colspan="4">审批</th></tr></thead>
<tbody>
<tr class="even">
	<td>审　　批：<span class="bold red">*</span></td><td class="center"><input type="radio" name="ifApprove" value="1" />通过　　　<input type="radio" name="ifApprove" value="-1" checked />不通过</td>
</tr>
<tr class="odd">
	<td>审批备注：</td><td><textarea name="approveRemark" style="width:440px;height:110px;"></textarea></td>
</tr>
<tr class="odd">
	<td colspan=2 class="center"><input type="submit" value=" 提交 " /><input type="reset" value="重置" /></td>
</tr>
</tbody>
</table>
</form>
EOF;
			$this->kclass->page['title'].='审批供应商评价';
			$this->kclass->page['onload'].='dc.tabhover();';
			$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - <a href="/s.php?module=psupplier&action=inbounditemlist">供应商入库明细列表</a> - 审批供应商评价记录','body'=>$body));
		}

		function doapproveconsignevaluate(){
			if($this->kclass->input['requestMethod'] != 'post'){
				$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
			}else{
				if($this->kclass->input['ifApprove']!=1 AND $this->kclass->input['ifApprove']!=-1){
					$e.='<li>请选择供应商评价单中的 是否通过审批 按钮。</li>';
				}
			}
			if(isset($e)){
				$this->kclass->messager(array(
					'title' => '审批供应商评级记录单',
					'text' => '您在审批供应商评级记录单的过程中有以下错误：<ul>'.$e.'</ul>',
					'url' => 'javascript:history.back()',
					'sec' => 3
				));
			}
			$this->kclass->DB->query("
				UPDATE consignevaluate SET
					ifApprove='".$this->kclass->input['ifApprove']."',
					approver='".$this->kclass->user['userid']."',
					approved='".TIMENOW."',
					approvalRemark='".$this->kclass->input['approveRemark']."'
				WHERE consignevaluateid='".$this->kclass->input['consignevaluateid']."'
			");
			if($this->kclass->input['ifApprove']==1){
				$verify='已通过';
				$inbound=$this->kclass->DB->queryFirst("SELECT inboundid  FROM `consignevaluate` WHERE  consignevaluateid='".$this->kclass->input['consignevaluateid']."' ");
			$this->kclass->DB->query("UPDATE `inbound` SET `ifEvaluate`=1  WHERE inboundid='".$inbound['inboundid']."'");
			}elseif($this->kclass->input['ifApprove']==-1){
				$verify='未通过';
			}
			//改变入库单中的是否评价状态，当审批结束后，入库单中的状态自然更改为1
			
			$this->kclass->messager(array(
				'title' => '审核供应商评价记录',
				'text' => '供应商评价记录 '.$verify.'审核!返回查看采购单',
				'url' => '/s.php?module=psupplier&action=view&supplierid='.$this->kclass->input['supplierid'],
				'sec' => 2
			));
		}
		
		function updateconsignevaluate(){
			if(!$this->kclass->input['consignevaluateid']){
				$this->kclass->boinkIt('/s.php?module=psupplier');
			}
			$evaluate=$this->kclass->DB->queryFirst("SELECT * FROM `consignevaluate` WHERE consignevaluateid='".$this->kclass->input['consignevaluateid']."'");
			if($evaluate['dateline']){
				$dateline=date('Y-m-d',$evaluate['dateline']);
			}
			if($evaluate['quality']==0){
				$quality='<input type="radio" name="quality" value="0" checked="true"/>否　<input type="radio" name="quality" value="1" />是';
			}else{
				$quality='<input type="radio" name="quality" value="0" />否　<input type="radio" name="quality" value="1"  checked="true"/>是';
			}
			if($evaluate['delivery']==0){
				$delivery='<input type="radio" name="delivery" value="0" checked="true"/>否　<input type="radio" name="delivery" value="1" />是';
			}else{
				$delivery='<input type="radio" name="delivery" value="0" />否　<input type="radio" name="delivery" value="1" checked="true"/>是';
			}
			$body.=<<<EOF
<form action="/s.php?module=psupplier&action=doupdateconsignevaluate" name="psupplier "method="post">
<input type="hidden" name="module" value="psupplier">
<input type="hidden" name="action" value="doupdateconsignevaluate">
<input type="hidden" name="consignevaluate" value="{$this->kclass->input['consignevaluateid']}">
<input type="hidden" name="supplierid" value="{$evaluate['supplierid']}">
<table class="hundred">
<thead><tr><th colspan="4">评价交货情况</th></tr></thead>
<tbody>
<tr class="odd">
<td width="100">是否准时交货：</td><td width="450">{$delivery}</td>
<td width="100">质量是否合格：</td><td>{$quality}</td>
</tr>
<tr class="even">
<td>价　　　　格：</td><td><input type="text" name="price" id="price" value="{$evaluate['price']}"></td><td>服　　　　务:</td><td><input type="text" name="service" id="service" value="{$evaluate['service']}">   评价日期：<input type="text" name="dateline" id="dateline" value="{$dateline}"></td>
</tr>
<tr class="even">
<td>备注信息：</td><td colspan="3"><textarea name="remark" style="width:560px;height:60px">{$remark}</textarea></td>
</tr>
</tbody>
</table>
</table>
<table class="tlist tsuite" align="center" width="100%">
<tbody>
<tr colspan="4" class="even" nohover>
<td class="center"><input type="submit" id="submitButton" value="  提交  " accesskey="s"><input type="reset" value="  复位  "/></td>
</tr>
</tbody>
</table>
EOF;
			$this->kclass->page['title'].='修改供应商评价';
			$this->kclass->page['onload'].='dc.tabhover();';
			$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - <a href="/s.php?module=psupplier&action=inbounditemlist">供应商入库明细列表</a> - 修改供应商评价记录','body'=>$body));
		}

		function doupdateconsignevaluate(){
			//echo $this->kclass->input['supplierid'];
			if($this->kclass->input['requestMethod'] != 'post'){
				$e='<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
			}else{
				if($this->kclass->input['length']!==''){
					for($i=1;$i<=$this->kclass->input['length'];$i++){
						if($this->kclass->input['deliverytime'][$i]==''){
							$e.='<li>请选择准时交货最终评价结果!</li>';
						}	
						if($this->kclass->input['quality'][$i]==''){
							$e.='<li>请选择质量标准最终评价结果!</li>';
						}
						if($this->kclass->input['price'][$i]=='' or !is_numeric($this->kclass->input['price'][$i])){
							$e.='服务得分不能为空或价格输入有误，请重新输入!';
						}						
						if($this->kclass->input['service'][$i]=='' or !is_numeric($this->kclass->input['service'][$i])){
							$e.='<li>服务得分不能为空或价格输入有误，请重新输入!</li>';
						}
						if($this->kclass->input['dateline'][$i]==''){
							$e.='<li>请输入评价时间</li>';
						}
						if($this->kclass->input['price'][$i]>15 or $this->kclass->input['price'][$i]<0){
							$e.='<li>价格得分要在0分到15分之间，请重新填写!</li>';
						}
						if($this->kclass->input['service'][$i]>15 or $this->kclass->input['service'][$i]<0){
							$e.='<li>服务得分要在0分到15分之间，请重新填写!</li>';
						}
						if($this->kclass->input['dateline'][$i]==''){
							$e.='<li>请选择评价时间!</li>';
						}
					}
				}else{
					$e.='<li>没有入库物资...!</li>';
				}
			}
			if($e){
				$this->kclass->messager(array(
					'title' => '修改供应商评级',
					'text' => '您在修改供应商评级的过程中有以下错误：<ul>'.$e.'</ul>',
					'url' => 'javascript:history.back()',
					'sec' => 3
				));
			}
			if($this->kclass->input['length']!==''){
				for($i=1;$i<=$this->kclass->input['length'];$i++){			
					$dd=explode("-",$this->kclass->input['dateline'][$i]);
					$dateline=mktime(0,0,0,$dd[1],$dd[2],$dd[0]);
					$totalScore=$this->kclass->input['price'][$i]+$this->kclass->input['quality'][$i]+$this->kclass->input['service'][$i]+$this->kclass->input['deliverytime'][$i];
					//更新供应商对应订单的物资评价记录
					$this->kclass->DB->query("
						UPDATE `consignevaluateitem` SET 
							`priceScore`='".$this->kclass->input['price'][$i]."',
							`deliveryScore`='".$this->kclass->input['deliverytime'][$i]."',
							`qualityScore`='".$this->kclass->input['quality'][$i]."',
							`serviceScore`='".$this->kclass->input['service'][$i]."',
							`totalScore`='".$totalScore."'
							WHERE materialid='".$this->kclass->input['materialid'][$i]."'
					");
				}
			}		
			//查询所有供应商对应订单物资评价平均分
			$scores=$this->kclass->DB->queryFirst("
				SELECT AVG(priceScore),AVG(serviceScore),AVG(deliveryScore),AVG(qualityScore)
				FROM `consignevaluateitem`
				WHERE porderid='".$this->kclass->input['porderid']."'
			");
			if($this->kclass->input['remark']!=='(备注信息)'){
				$remark = $this->kclass->input['remark'];
			}else{
				$remark = '';
			}
			//更新供应商评价记录
			$this->kclass->DB->query("
				UPDATE `consignevaluate` SET
				`price`=".$scores['AVG(priceScore)'].",
				`quality`=".$scores['AVG(qualityScore)'].",
				`delivery`=".$scores['AVG(deliveryScore)'].",
				`service`=".$scores['AVG(serviceScore)'].",
				`remark`='".$remark."',
				`modifier`='".$this->kclass->user['userid']."',
				`modified`='".TIMENOW."'
				WHERE porderid='".$this->kclass->input['porderid']."' AND killed=0
			");
			$this->kclass->messager(array(
				'title' => '修改供应商评级',
				'text' => '供应商记录修改成功!',
				'url' => '/s.php?module=psupplier&action=inbounditemlist',
				'sec' => 2
			));
		}

		function inquiry(){
			$inquiry=$this->kclass->DB->queryFirst("
				SELECT i.*,
					su.supplierno,su.title AS supplier,
					c.title AS currency,
					pa.title AS packing,
					po.title AS port,
					d.title AS deliveryterm,
					pt.entitle AS paymentterm,
					s.title AS shipmethod,
					u.username AS creator,
					us.username AS modifier
				FROM inquiry AS i
				LEFT JOIN supplier AS su ON (su.supplierid=i.supplierid)
				LEFT JOIN currency AS c ON (c.currencyid=i.currencyid)
				LEFT JOIN packing AS pa ON (pa.packingid=i.packingid)
				LEFT JOIN port AS po ON (po.portid=i.portid)
				LEFT JOIN deliveryterm AS d ON (d.deliverytermid=i.deliverytermid)
				LEFT JOIN paymentterm AS pt ON (pt.paymenttermid=i.paymenttermid)
				LEFT JOIN shipmethod AS s ON (s.shipmethodid=i.shipmethodid)
				LEFT JOIN `user` AS u ON (u.userid=i.creator)
				LEFT JOIN `user` AS us ON (us.userid=i.modifier)
				WHERE i.killed=0 AND i.inquiryid='".$this->kclass->input['inquiryid']."'
				LIMIT 0,1
			");
			if(!$inquiry){
				$e='<li>数据错误，很抱歉~</li>';
			}
			if(isset($e)){
				$this->kclass->messager(array(
					'title' => '新建入库记录的批次编号',
					'text' => '新建入库记录的批次编号<ul>'.$e.'</ul>',
					'url' => 'javascript:history.back()',
					'sec' => 3
				));
			}

			// 询价item
			$items=$this->kclass->DB->query("
				SELECT i.materialid,i.materialno,i.materialTitle,i.materialStandard,i.package,i.discount,i.quantity,
					m.materialno AS olMaterialno,m.title AS olTitle,m.standard AS olStandard,
					b.title AS brand,
					p.title AS packing,
					u.title AS unit
				FROM `item` AS i 
				LEFT JOIN `material` AS m ON (m.materialid=i.materialid)
				LEFT JOIN `brand` AS b ON (b.brandid=i.brandid)
				LEFT JOIN `packing` AS p ON (p.packingid=i.packingid)
				LEFT JOIN `unit` AS u ON (u.unitid=i.unitid)
				WHERE i.module='inquiry' AND i.killed=0 AND i.mid='".$this->kclass->input['inquiryid']."'
				ORDER BY i.created ASC
			");
			if($this->kclass->DB->numRows()){
				$itemList='<table class="hundred small"><thead></tr><th colspan="10">询价物资列表</th></tr></thead><tbody><tr class="center even" nohover><td width="30">ID</td><td>物资编号</td><td>物资名称</td><td>规格</td><td>品牌</td><td>单位</td><td>包装方式</td><td>包装</td><td>折扣</td><td>数量</td></tr>';
				$i=1;
				while($item=$this->kclass->DB->fetchArray($items)){
					$itemList.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').'">
						<td>'.$i.'</td>
						<td><a href="/s.php?module=material&action=view&materialid='.$item['materialid'].'">'.$this->kclass->iif($item['materialid']==0,$item['materialno'],$item['olMaterialno']).'</td>
						<td>'.$this->kclass->iif($item['materialid']==0,$item['materialTitle'],$item['olTitle']).'</td>
						<td>'.$this->kclass->iif($item['materialid']==0,$item['materialStandard'],$item['olStandard']).'</td>
						<td>'.$item['brand'].'</td>
						<td>'.$item['unit'].'</td>
						<td>'.$item['package'].'</td>
						<td>'.$item['packing'].'</td>
						<td>'.$item['discount'].'</td>
						<td>'.$item['quantity'].'</td></tr>';
						$i++;
				}
				$itemList.='</tbody></table>';
			}

			$inquiry['created']=date('Y-m-d H:i',$inquiry['created']);
			$inquiry['dateline']=date('Y-m-d',$inquiry['dateline']);
			if($inquiry['modified']!=0)$modify='，由'.$inquiry['modifier'].'于'.date('Y-m-d',$inquiry['modified']).'修改';
			$quoting=$this->kclass->iif($inquiry['quotingid']>0,'<a href="/s.php?module=psupplier&action=quoting&inquiryid='.$inquiry['inquiryid'].'">查看报价</a> <a href="/s.php?module=psupplier&action=addQuoting&inquiryid='.$inquiry['inquiryid'].'">再次报价</a>','<a href="/s.php?module=psupplier&action=addQuoting&inquiryid='.$inquiry['inquiryid'].'">报价</a>');

$body=<<<EOF
<div class="title"><span class="right small gray">由{$inquiry['creator']}于{$inquiry['created']}建立{$modify}。</span></div>
<table class="hundred">
<thead></tr><th colspan="4">查看询价：基本信息<span class="right">{$quoting}</span></th></tr></thead>
<tbody>
<tr class="odd">
	<td width="80">供应商：</td><td width="400"><a href="/s.php?module=psupplier&action=view&supplierid={$inquiry['supplierid']}">{$inquiry['supplier']}（{$inquiry['supplierno']}）</a></td>
	<td width="80">付款方式：</td><td>{$inquiry['paymentterm']}</td>
</tr><tr class="even">
	<td>币种：</td><td>{$inquiry['currency']}</td>
	<td>交货条款: </td><td>{$inquiry['deliveryterm']}</td>
</tr><tr class="odd">
	<td>港口：</td><td>{$inquiry['port']}</td>
	<td>运输方式：</td><td>{$inquiry['shipmethod']}</td>
</tr><tr class="even">
	<td>联系人: </td><td>{$inquiry['contact']}</td>
	<td></td><td></td>
</tr><tr class="odd">
	<td>规格: </td><td>{$inquiry['standard']}</td>
	<td>物资信息: </td><td>{$inquiry['material']}</td>
</tr><tr class="even">
	<td>工艺: </td><td>{$inquiry['technics']}</td>
	<td>备注：</td><td>{$inquiry['remark']}</td>
</tr>
</tbody>
</table>
{$itemList}
EOF;
			$this->kclass->tbline+=4;
			$this->kclass->page['title'].='查看询价';
			$this->kclass->page['onload'].='dc.tabhover();';
			$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - 查看询价', 'right'=>'<a href="/s.php?module=psupplier&action=view&supplierid='.$this->kclass->input['supplierid'].'">返回查看供应商</a>','right'=>'<div class="gray"><span class="right normal"><a href="/s.php?module=psupplier&action=updateInquiry&inquiryid='.$inquiry['inquiryid'].'">修改</a>　|　<a href="/p.php?action=order&amp;type=PI&amp;orderid=1" target="_blank">打印PI</a></span></div>','body'=>$body));
		}
		//
		function addInquiry(){
			$supplier=$this->kclass->DB->queryFirst("SELECT supplierid FROM supplier WHERE killed=0 AND supplierid='".$this->kclass->input['supplierid']."' LIMIT 0,1");
			if($supplier){
				$packing=$this->kclass->chooserPacking(array('name'=>'packingid','width'=>250,'selectedid'=>$inquiry['packingid'],'topname'=>'','hasBlank'=>1));
				$port=$this->kclass->chooserPort(array('name'=>'portid','width'=>250,'selectedid'=>$inquiry['portid'],'topname'=>'','hasBlank'=>1));
				$paymentterm=$this->kclass->chooserPaymentterm(array('name'=>'paymenttermid','width'=>250,'selectedid'=>$inquiry['paymenttermid'],'topname'=>'','hasBlank'=>1));
				$currency=$this->kclass->chooserCurrency(array('name'=>'currencyid','width'=>250,'selectedid'=>$inquiry['currencyid'],'topname'=>'','hasBlank'=>1));
				$shipmethod=$this->kclass->chooserShipmethod(array('name'=>'shipmethodid','width'=>250,'selectedid'=>$inquiry['shipmethodid'],'topname'=>'','hasBlank'=>1));
				$deliveryterm=$this->kclass->chooserDeliveryterm(array('name'=>'deliverytermid','width'=>250,'selectedid'=>$inquiry['deliverytermid'],'topname'=>'','hasBlank'=>1));

				for($i=1;$i<6;$i++){
					$itemList.='<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').'">
						<td>'.$this->_chooserSupplierMaterial(array('name'=>'itemMaterialid['.$i.']','hasBlank'=>1,'width'=>150,'topname'=>'新物资','supplierid'=>$this->kclass->input['supplierid'])).'</td>
						<td><input type="text" name="itemMaterialno['.$i.']" style="width:70px"></td>
						<td><input type="text" name="itemMaterialTitle['.$i.']"></td>
						<td><input type="text" name="itemStandard['.$i.']"></td>
						<td>'.$this->kclass->chooserBrand(array('name'=>'itemBrandid['.$i.']','hasBlank'=>1,'topname'=>'请选择')).'</td>
						<td>'.$this->kclass->chooserUnit(array('name'=>'itemUnitid['.$i.']','hasBlank'=>1,'topname'=>'请选择','selectedid'=>5)).'</td>
						<td><input type="text" name="itemPackage['.$i.']"></td>
						<td>'.$this->kclass->chooserPacking(array('name'=>'itemPackingid['.$i.']', 'hasBlank'=>1, 'width'=>154)).'</td>
						<td><input type="text" name="itemDiscount['.$i.']" value="100" size="3"></td>
						<td><input type="text" name="itemQuantity['.$i.']" size="5"></td>
					</tr>';
				}

$body=<<<EOF
<form action="/s.php?module=psupplier&action=insertInquiry" method="post">
<input type="hidden" name="module" value="psupplier" />
<input type="hidden" name="action" value="insertInquiry" />
<input type="hidden" name="supplierid" value="{$this->kclass->input['supplierid']}" />
<table class="hundred">
<thead></tr><th colspan="4">新建询价：基本信息</th></tr></thead>
<tbody>
<tr class="even">
	<td>联 系 人：<span class="red bold">*</span></td><td><input type="text" style="width:244px" name="contact" value="{$inquiry['contact']}"> <span class="small gray">指供应商方询价人员</span></td>
	<td>币　　种：<span class="red bold">*</span></td><td>{$currency}</td>
</tr>
<tr class="even">
	<td>运送方式：</td><td>{$shipmethod}</td>
	<td>交货地点：</td><td>{$port}</td>
</tr>
<tr class="odd">
	<td>支付条款：</td>
	<td>{$paymentterm}</td>
	<td>交货条款：</td>
	<td>{$deliveryterm}</td>
</tr>
	<td>规　　格：</td><td><textarea style="width:444px;height:111px;" name="standard"></textarea></td>
	<td>物资信息：</td><td><textarea style="width:444px;height:111px;" name="material"></textarea></td>
</tr><tr class="even">
	<td>工　　艺：</td><td><textarea style="width:444px;height:111px;" name="technics"></textarea></td>
	<td>备　　注：</td><td><textarea  style="width:444px;height:111px;" name="remark"></textarea></td>
</tr>
</table>
<table class="hundred small">
<thead></tr><th colspan="10">询价明细</th></tr></thead>
<tbody>
<tr class="center even" nohover><td>物资<span class="red">*</span></td><td>物资编号</td><td>物资名称</td><td>规格</td><td>品牌</td><td>单位<span class="red">*</span></td><td>包装方式</td><td>包装</td><td>折扣</td><td>数量<span class="red">*</span></td></tr>
		{$itemList}
	<tr class="odd">
		<td class="small gray" colspan="10">
			注：①如行数不够，请保存后再进入修改页面。②至少有“物资”、“数量”、“单位”三个项目选择填写，该明细才会被保存。③“折扣”请填写百分比“%”。</td>
	</tr>
</tbody>
</table>
<table class="tlist tsuite" align="center" width="100%">
	<tbody>
		<tr class="odd center">
		<td colspan=4><input type="submit" value="  提交  " accesskey="s"><input type="reset" value="  复位  "/></td>
		</tr>
	</tbody>
</table>
</form>
EOF;
			}else{
				$body='数据出错，很抱歉！';
			}

			$this->kclass->tbline+=4;
			$this->kclass->page['title'].='向供应商询价';
			$this->kclass->page['onload'].='dc.tabhover();';
			$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - <a href="/s.php?module=psupplier&action=view&supplierid='.$this->kclass->input['supplierid'].'">查看供应商</a> - 向供应商询价', 'right'=>'<a href="/s.php?module=psupplier&action=view&supplierid='.$this->kclass->input['supplierid'].'">返回查看供应商</a>','body'=>$body));
		}
		//
		function insertInquiry(){
			if($this->kclass->input['requestMethod'] != 'post'){
				$e='<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
			}else{
				if(strlen($this->kclass->input['contact']) < 2){
					$e .= '<li>请填写询价供应商的相关联系人（不能少于2个字符）。</li>';
				}
				if($this->kclass->input['currencyid'] <= 0){
					$e .= '<li>请选择要供应商询价的币种。</li>';
				}
				$j=count($this->kclass->input['itemMaterialid']);
				$hasItem=0;
				for($i=1; $i<$j+1; $i++){
					if(($this->kclass->input['itemMaterialid'][$i]>0 OR $this->kclass->input['itemMaterialTitle']!='') AND $this->kclass->input['itemUnitid'][$i]>0 AND $this->kclass->input['itemQuantity'][$i]>0){
						$hasItem=1;
					}
				}
				if($hasItem==0){
					$e .= '<li>需要填写至少有一条订单明细，才能建立订单。</li>';
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

			$supplier=$this->kclass->DB->queryFirst("SELECT supplierid,supplierno FROM supplier WHERE killed=0 AND supplierid='".$this->kclass->input['supplierid']."' LIMIT 0,1");
			$this->kclass->DB->query("
				INSERT INTO inquiry
					(supplierid,contact,currencyid,portid,paymenttermid,deliverytermid,shipmethodid,standard,material,technics,remark,created,creator)
				VALUES
					('".$this->kclass->input['supplierid']."','".$this->kclass->input['contact']."','".$this->kclass->input['currencyid']."','".$this->kclass->input['portid']."','".$this->kclass->input['paymenttermid']."','".$this->kclass->input['deliverytermid']."','".$this->kclass->input['shipmethodid']."','".$this->kclass->input['standard']."','".$this->kclass->input['material']."','".$this->kclass->input['technics']."','".$this->kclass->input['remark']."','".TIMENOW."','".$this->kclass->user['userid']."')
			");
			$inquiryid=$this->kclass->DB->insertID();

			$key=array();
			for($i=1;$i<5;$i++){//获得输入框行的编号
				if($this->kclass->input['itemMaterialid'][$i]!=0 OR $this->kclass->input['itemMaterialTitle'][$i]!='')
					$key[]=$i;
			}
			$count=count($key);
			for($m=0;$m<$count;$m++){
				$this->kclass->input['itemDiscount'][$key[$m]]=intval($this->kclass->input['itemDiscount'][$key[$m]]);
				$this->kclass->DB->query("
					INSERT INTO item
						(module,mid,materialid,materialno,materialTitle,materialStandard,brandid,unitid,package,packingid,discount,quantity,created,creator)
					VALUES
						('inquiry','".$inquiryid."','".$this->kclass->input['itemMaterialid'][$key[$m]]."','".$this->kclass->input['itemMaterialno'][$key[$m]]."','".$this->kclass->input['itemMaterialTitle'][$key[$m]]."','".$this->kclass->input['itemStandard'][$key[$m]]."','".$this->kclass->input['itemBrandid'][$key[$m]]."','".$this->kclass->input['itemUnitid'][$key[$m]]."','".$this->kclass->input['itemPackage'][$key[$m]]."','".$this->kclass->input['itemPackingid'][$key[$m]]."','".$this->kclass->input['itemDiscount'][$key[$m]]."','".$this->kclass->input['itemQuantity'][$key[$m]]."','".TIMENOW."','".$this->kclass->user['userid']."')
				");
			}

			$this->kclass->messager(array(
				'title' => '新建询价单',
				'text' => '对供应商 【<b>'.$supplier['supplierno'].'</b>】 的询价单已新建成功!返回供应商详情页',
				'url' => '/s.php?module=psupplier&action=view&supplierid='.$supplier['supplierid'],
				'sec' => 2
			));

		}
		//
		function updateInquiry(){
			$inquiry=$this->kclass->DB->queryFirst("
				SELECT i.*,
					u.username AS creator,
					us.username AS modifier
				FROM inquiry AS i
				LEFT JOIN `user` AS u ON (u.userid=i.creator)
				LEFT JOIN `user` AS us ON (us.userid=i.modifier)
				WHERE i.killed=0 AND i.inquiryid='".$this->kclass->input['inquiryid']."'
				LIMIT 0,1
			");
			if($inquiry){
				$this->kclass->br2nl=true;
				$inquiry['standard']=$this->kclass->parseConvertValue($inquiry['standard']);
				$inquiry['material']=$this->kclass->parseConvertValue($inquiry['material']);
				$inquiry['technics']=$this->kclass->parseConvertValue($inquiry['technics']);
				$inquiry['remark']=$this->kclass->parseConvertValue($inquiry['remark']);

				$packing=$this->kclass->chooserPacking(array('name'=>'packingid','width'=>250,'selectedid'=>$inquiry['packingid'],'topname'=>''));
				$port=$this->kclass->chooserPort(array('name'=>'portid','width'=>250,'selectedid'=>$inquiry['portid'],'topname'=>''));
				$paymentterm=$this->kclass->chooserPaymentterm(array('name'=>'paymenttermid','width'=>250,'selectedid'=>$inquiry['paymenttermid'],'topname'=>''));
				$currency=$this->kclass->chooserCurrency(array('name'=>'currencyid','width'=>250,'selectedid'=>$inquiry['currencyid'],'topname'=>''));
				$shipmethod=$this->kclass->chooserShipmethod(array('name'=>'shipmethodid','width'=>250,'selectedid'=>$inquiry['shipmethodid'],'topname'=>''));
				$deliveryterm=$this->kclass->chooserDeliveryterm(array('name'=>'deliverytermid','width'=>250,'selectedid'=>$inquiry['deliverytermid'],'topname'=>''));
				$inquiry['dateline']=date('Y-m-d',$inquiry['dateline']);

				// item
				$items=$this->kclass->DB->query("
					SELECT itemid,materialid,unitid,brandid,materialno,materialTitle,materialStandard,package,packingid,quantity
					FROM `item`
					WHERE killed=0 AND module='inquiry' AND mid='".$inquiry['inquiryid']."'
					ORDER BY itemid ASC
				");
				if($this->kclass->DB->numRows()){
					$k=1;
					while($item=$this->kclass->DB->fetchArray($items)){
						$itemList.='<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').' small">
							<td><input type="hidden" name="itemId['.$k.']" value="'.$item['itemid'].'" />'.$this->_chooserSupplierMaterial(array('name'=>'itemMaterialid['.$k.']','hasBlank'=>1,'width'=>150,'topname'=>'新物资','supplierid'=>$inquiry['supplierid'],'selectedid'=>$item['materialid'])).'</td>
							<td><input type="text" name="itemMaterialno['.$k.']" style="width:70px" value="'.$item['materialno'].'"></td>
							<td><input type="text" name="itemMaterialTitle['.$k.']" value="'.$item['materialTitle'].'"></td>
							<td><input type="text" name="itemStandard['.$k.']" value="'.$item['materialStandard'].'"></td>
							<td>'.$this->kclass->chooserBrand(array('name'=>'itemBrandid['.$k.']','hasBlank'=>1,'topname'=>'请选择','selectedid'=>$item['brandid'])).'</td>
							<td>'.$this->kclass->chooserUnit(array('name'=>'itemUnitid['.$k.']','hasBlank'=>1,'topname'=>'请选择','selectedid'=>$item['unitid'])).'</td>
							<td><input type="text" name="itemPackage['.$k.']" value="'.$item['package'].'"></td>
							<td>'.$this->kclass->chooserPacking(array('name'=>'itemPackingid['.$k.']', 'hasBlank'=>1, 'width'=>154,'selectedid'=>$item['packingid'])).'</td>
							<td><input type="text" name="itemDiscount['.$k.']" value="100" size="3" value="'.$item['discount'].'"></td>
							<td><input type="text" name="itemQuantity['.$k.']" size="5" value="'.$item['quantity'].'"></td>
							<td><input type="checkbox" name="itemSelect['.$k.']" value="'.$item['itemid'].'"></td>
						</tr>';
						$k++;
					}
					$itemList.='<tr><td colspan="12">新增明细</td></tr>';
				}
				for($i=$k; $i<($k+5); $i++){
					$itemList.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').' small">
						<td>'.$this->_chooserSupplierMaterial(array('name'=>'itemMaterialid['.$i.']','hasBlank'=>1,'width'=>150,'topname'=>'新物资','supplierid'=>$inquiry['supplierid'])).'</td>
						<td><input type="text" name="itemMaterialno['.$i.']" style="width:70px"></td>
						<td><input type="text" name="itemMaterialTitle['.$i.']"></td>
						<td><input type="text" name="itemStandard['.$i.']"></td>
						<td>'.$this->kclass->chooserBrand(array('name'=>'itemBrandid['.$i.']','hasBlank'=>1,'topname'=>'请选择')).'</td>
						<td>'.$this->kclass->chooserUnit(array('name'=>'itemUnitid['.$i.']','hasBlank'=>1,'topname'=>'请选择')).'</td>
						<td><input type="text" name="itemPackage['.$i.']"></td>
						<td>'.$this->kclass->chooserPacking(array('name'=>'itemPackingid['.$i.']', 'hasBlank'=>1, 'width'=>154)).'</td>
						<td><input type="text" name="itemDiscount['.$i.']" value="100" size="3"></td>
						<td><input type="text" name="itemQuantity['.$i.']" size="5"></td>
						<td></td>
					</tr>';
				}
				if($this->kclass->input['options']=='version'){
					$hidden='<input type="hidden" name="options" value="'.$this->kclass->input['options'].'" /><input type="hidden" name="version" value="'.$inquiry['revise'].'" />';
				}
$body=<<<EOF
<form action="/s.php?module=psupplier&action=doupdateInquiry" method="post">
<input type="hidden" name="module" value="psupplier" />
<input type="hidden" name="action" value="doupdateInquiry" />
<input type="hidden" name="inquiryid" value="{$inquiry['inquiryid']}" />
<input type="hidden" name="supplierid" value="{$inquiry['supplierid']}" />
{$hidden}
<table class="hundred">
<thead></tr><th colspan="4">修改询价：基本信息</th></tr></thead>
<tr class="even">
	<td>联 系 人：<span class="red bold">*</span></td><td><input type="text" style="width:244px" name="contact" value="{$inquiry['contact']}"> <span class="small gray">指供应商方询价人员</span></td>
	<td>币　　种：<span class="red bold">*</span></td><td>{$currency}</td>
</tr>
<tr class="even">
	<td>运送方式：</td><td>{$shipmethod}</td>
	<td>交货地点：</td><td>{$port}</td>
</tr>
<tr class="odd">
	<td>支付条款：</td>
	<td>{$paymentterm}</td>
	<td>交货条款：</td>
	<td>{$deliveryterm}</td>
</tr>
	<td>规　　格：</td><td><textarea style="width:444px;height:111px;" name="standard">{$inquiry['standard']}</textarea></td>
	<td>物资信息：</td><td><textarea style="width:444px;height:111px;" name="material">{$inquiry['material']}</textarea></td>
</tr><tr class="even">
	<td>工　　艺：</td><td><textarea style="width:444px;height:111px;" name="technics">{$inquiry['technics']}</textarea></td>
	<td>备　　注：</td><td><textarea  style="width:444px;height:111px;" name="remark">{$inquiry['remark']}</textarea></td>
</tr>
</table>
<table class="hundred">
<thead></tr><th colspan="11">询价明细</th></tr></thead>
<tbody>
<tr class="even center"><td>物资<span class="red">*</span></td><td>物资编号</td><td>物资名称</td><td>规格</td><td>品牌</td><td>单位<span class="red">*</span></td><td>包装方式</td><td>包装</td><td>折扣</td><td>数量<span class="red">*</span></td><td>删</td></tr>
		{$itemList}
	<tr class="odd">
		<td class="small gray" colspan="11">
			注意事项：
			如果所列表格不够，那么在提交保存后再添加申请的物资</td>
	</tr>
</tbody>
</table>
<table class="tlist tsuite" align="center" width="100%">
	<tbody>
		<tr class="even" nohover>
		<td align="right"><input type="submit" id="submitButton" value="  提交  " accesskey="s"></td>
		<td><input type="reset" value="  复位  "/></td>
		</tr>
	</tbody>
</table>
</form>
EOF;
			}else{
				$body='数据出错，很抱歉！';
			}

			$this->kclass->page['title'].='修改询价';
			$this->kclass->page['onload'].='dc.tabhover();';
			$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - 修改询价', 'right'=>'<a href="/s.php?module=psupplier&action=view&supplierid='.$inquiry['supplierid'].'">返回查看供应商</a>','body'=>$body));
		}
		//
		function doupdateInquiry(){
			if($this->kclass->input['requestMethod'] != 'post'){
				$e='<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
			}else{
				if(strlen($this->kclass->input['contact']) < 2){
					$e .= '<li>请填写询价供应商的相关联系人（不能少于2个字符）。</li>';
				}
				if($this->kclass->input['currencyid'] <= 0){
					$e .= '<li>请选择要供应商询价的币种。</li>';
				}
				$j=count($this->kclass->input['itemMaterialid']);
				$hasItem=0;
				for($i=1; $i<$j+1; $i++){
					if($this->kclass->input['itemKill'][$i]!='on' AND ($this->kclass->input['itemMaterialid'][$i]>0 OR $this->kclass->input['itemMaterialTitle']!='') AND $this->kclass->input['itemUnitid'][$i]>0 AND $this->kclass->input['itemQuantity'][$i]>0){
						$hasItem=1;
					}
				}
				if($hasItem==0){
					$e .= '<li>需要填写至少有一条订单明细，才能建立订单。</li>';
				}
			}
			if($e){
				$this->kclass->messager(array(
					'title' => '修改询价单',
					'text' => '您在修改询价单的过程中有以下错误：<ul>'.$e.'</ul>',
					'url' => 'javascript:history.back()',
					'sec' => 3
				));
			}
			$supplier=$this->kclass->DB->queryFirst("SELECT supplierid,supplierno FROM supplier WHERE killed=0 AND supplierid='".$this->kclass->input['supplierid']."' LIMIT 0,1");
			if($this->kclass->input['options']=='version'){
				$this->kclass->DB->query("UPDATE `inquiry` SET killed='".TIMENOW."',killer='".$this->kclass->user['userid']."' WHERE inquiryid='".$this->kclass->input['inquiryid']."'");
				$this->kclass->DB->query("UPDATE `item` SET killed='".TIMENOW."',killer='".$this->kclass->user['userid']."' WHERE module='inquiry' AND  mid='".$this->kclass->input['inquiryid']."'");
				$this->kclass->DB->query("
					INSERT INTO inquiry
						(supplierid,contact,currencyid,portid,paymenttermid,deliverytermid,shipmethodid,standard,material,technics,remark,referer,revise,reviser,revised,created,creator)
					VALUES
						('".$this->kclass->input['supplierid']."','".$this->kclass->input['contact']."','".$this->kclass->input['currencyid']."','".$this->kclass->input['portid']."','".$this->kclass->input['paymenttermid']."','".$this->kclass->input['deliverytermid']."','".$this->kclass->input['shipmethodid']."','".$this->kclass->input['standard']."','".$this->kclass->input['material']."','".$this->kclass->input['technics']."','".$this->kclass->input['remark']."','".$this->kclass->input['inquiryid']."','".($this->kclass->input['revise']+1)."','".$this->kclass->user['userid']."','".TIMENOW."','".TIMENOW."','".$this->kclass->user['userid']."')
				");
				$inquiryid=$this->kclass->DB->insertID();

				$$key=array();
				$itemCount=count($this->kclass->input['itemMaterialid']);
				for($i=1;$i<$itemCount;$i++){//获得输入框行的编号
					if($this->kclass->input['itemMaterialid'][$i]!=0 OR $this->kclass->input['itemMaterialTitle'][$i]!='')
						$key[]=$i;
				}
				$count=count($key);
				for($m=0;$m<$count;$m++){
					$this->kclass->input['itemDiscount'][$key[$m]]=intval($this->kclass->input['itemDiscount'][$key[$m]]);
					if($this->kclass->input['itemSelect'][$key[$m]]=='' AND $this->kclass->input['itemQuantity'][$key[$m]]>0){
						$this->kclass->DB->query("
							INSERT INTO item
								(module,mid,materialid,materialno,materialTitle,materialStandard,brandid,unitid,package,packingid,discount,quantity,created,creator)
							VALUES
								('inquiry','".$inquiryid."','".$this->kclass->input['itemMaterialid'][$key[$m]]."','".$this->kclass->input['itemMaterialno'][$key[$m]]."','".$this->kclass->input['itemMaterialTitle'][$key[$m]]."','".$this->kclass->input['itemStandard'][$key[$m]]."','".$this->kclass->input['itemBrandid'][$key[$m]]."','".$this->kclass->input['itemUnitid'][$key[$m]]."','".$this->kclass->input['itemPackage'][$key[$m]]."','".$this->kclass->input['itemPackingid'][$key[$m]]."','".$this->kclass->input['itemDiscount'][$key[$m]]."','".$this->kclass->input['itemQuantity'][$key[$m]]."','".TIMENOW."','".$this->kclass->user['userid']."')
						");
					}
				}
			}else{
				$this->kclass->DB->query("
					UPDATE inquiry SET
						supplierid='".$this->kclass->input['supplierid']."',
						contact='".$this->kclass->input['contact']."',
						currencyid='".$this->kclass->input['currencyid']."',
						portid='".$this->kclass->input['portid']."',
						paymenttermid='".$this->kclass->input['paymenttermid']."',
						deliverytermid='".$this->kclass->input['deliverytermid']."',
						shipmethodid='".$this->kclass->input['shipmethodid']."',
						standard='".$this->kclass->input['standard']."',
						material='".$this->kclass->input['material']."',
						technics='".$this->kclass->input['technics']."',
						remark='".$this->kclass->input['remark']."',
						modified='".TIMENOW."',
						modifier='".$this->kclass->user['userid']."'
					WHERE inquiryid='".$this->kclass->input['inquiryid']."'
				");
				$key=array();
				$itemCount=count($this->kclass->input['itemMaterialid']);
				for($i=1;$i<$itemCount;$i++){//获得输入框行的编号
					if($this->kclass->input['itemMaterialid'][$i]!=0 OR $this->kclass->input['itemMaterialTitle'][$i]!='')
						$key[]=$i;
				}
				$count=count($key);
				for($m=0;$m<$count;$m++){
					if($this->kclass->input['itemId'][$key[$m]]>0){
						if($this->kclass->input['itemSelect'][$key[$m]]>0 AND $this->kclass->input['itemSelect'][$key[$m]]==$this->kclass->input['itemId'][$key[$m]]){
							$this->kclass->DB->query("UPDATE `item` SET killed='".TIMENOW."',killer='".$this->kclass->user['userid']."' WHERE itemid='".$this->kclass->input['itemSelect'][$key[$m]]."'");
						}else{
							$this->kclass->DB->query("
								UPDATE `item` SET
									materialid='".$this->kclass->input['itemMaterialid'][$key[$m]]."',
									materialno='".$materialno."',
									materialTitle='".$materialTitle."',
									materialStandard='".$standard."',
									brandid='".$this->kclass->input['itemBrandid'][$key[$m]]."',
									unitid='".$this->kclass->input['itemUnitid'][$key[$m]]."',
									package='".$this->kclass->input['itemPackage'][$key[$m]]."',
									packingid='".$this->kclass->input['itemPackingid'][$key[$m]]."',
									discount='".$this->kclass->input['itemDiscount'][$key[$m]]."',
									quantity='".$this->kclass->input['itemQuantity'][$key[$m]]."',
									modified='".TIMENOW."',
									modifier='".$this->kclass->user['userid']."'
								WHERE `item`.itemid='".$this->kclass->input['itemId'][$key[$m]]."'
							");
						}
					}else{
						$this->kclass->DB->query("
						INSERT INTO item
							(module,mid,materialid,materialno,materialTitle,materialStandard,brandid,unitid,package,packingid,discount,quantity,created,creator)
						VALUES
							('inquiry','".$inquiryid."','".$this->kclass->input['itemMaterialid'][$key[$m]]."','".$this->kclass->input['itemMaterialno'][$key[$m]]."','".$this->kclass->input['itemMaterialTitle'][$key[$m]]."','".$this->kclass->input['itemStandard'][$key[$m]]."','".$this->kclass->input['itemBrandid'][$key[$m]]."','".$this->kclass->input['itemUnitid'][$key[$m]]."','".$this->kclass->input['itemPackage'][$key[$m]]."','".$this->kclass->input['itemPackingid'][$key[$m]]."','".$this->kclass->input['itemDiscount'][$key[$m]]."','".$this->kclass->input['itemQuantity'][$key[$m]]."','".TIMENOW."','".$this->kclass->user['userid']."')
					");
					}
				}
			}

			$this->kclass->messager(array(
				'title' => '修改询价单',
				'text' => '对供应商 【<b>'.$supplier['supplierno'].'</b>】 的询价单已新建成功!返回供应商详情页',
				'url' => '/s.php?module=psupplier&action=view&supplierid='.$supplier['supplierid'],
				'sec' => 3
			));
		}
		//
		function killInquiry(){
			if($this->kclass->input['inquiryid']<=0){
				$this->kclass->boinkIt('/s.php?module=psupplier');
			}
			if($this->kclass->input['confirm'] == 0){
				$this->kclass->boinkIt('/s.php?module=psupplier&action=view&supplierid='.$this->kclass->input['supplierid']);
			}
			if($this->kclass->input['inquiryid'] < 0){
				$e='<li>请从正确的页面进行操作，如果此问题持续存在，请联系网站管理员。</li>';
			}
			if(isset($e)){
				$this->kclass->messager(array(
					'title' => '删除供应商询价单',
					'text' => '您在删除供应商询价单的过程中有以下错误：<ul>'.$e.'</ul>',
					'url' => 'javascript:history.back()',
					'sec' => 3
				));
			}
			$inquiry=$this->kclass->DB->queryFirst("
				SELECT inquiry.inquiryid, inquiry.supplierid,
					supplier.title AS supplier
				FROM `inquiry`
				LEFT JOIN supplier ON (supplier.supplierid=inquiry.supplierid)
				WHERE inquiry.inquiryid='".$this->kclass->input['inquiryid']."'
			");
			if($inquiry){
				$this->kclass->DB->query("
					UPDATE `inquiry`
					SET killed=".TIMENOW.",killer='".$this->kclass->user['userid']."'
					WHERE inquiryid='".$inquiry['inquiryid']."'
				");
				$this->kclass->messager(array(
					'title' => '删除供应商询价单成功',
					'text' => '供应商 <b>'.$inquiryid['supplier'].'</b> 下的 询价单 已成功被标记为删除!',
					'url' => '/s.php?module=psupplier&action=view&supplierid='.$inquiry['supplierid'],
					'sec' => 2
				));
			}else{
				$this->kclass->messager(array(
					'title' => '删除供应商询价单失败',
					'text' => '您要删除的供应商询价单，不存在！',
					'url' => '/s.php?module=psupplier&action=view&supplierid='.$inquiry['supplierid'],
					'sec' => 3
				));
			}
		}

		//
		function removeInquiry(){
			if($this->kclass->input['inquiryid']<=0){
				$this->kclass->boinkIt('/s.php?module=psupplier');
			}
			$inquiry=$this->kclass->DB->queryFirst("
				SELECT inquiry.inquiryid, inquiry.supplierid,
					supplier.title AS supplier
				FROM `inquiry`
				LEFT JOIN supplier ON (supplier.supplierid=inquiry.supplierid)
				WHERE inquiry.inquiryid='".$this->kclass->input['inquiryid']."'
			");
			if($inquiry){
$body=<<<EOF
<form action="/s.php?module=psupplier&action=killInquiry" name="psupplier" method="post">
<input type="hidden" name="module" value="psupplier">
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
	<td class="middle">你确定要删除产品 <a href="/s.php?module=psupplier&action=view&supplierid={$inquiry['supplierid']}" class="middle bold" target="_blank">{$inquiry['supplier']}</a> 的 <span class="big bold">询价单</span> 吗?</td>
</tr>
<tr class="odd">
	<td align="center">
		<label for="c1" class="red"><input type="radio" id="c1" name="confirm" value="1">是</label>
		<label for="c0" class="green"><input type="radio" id="c0" name="confirm" value="0" checked>否</label>
	</td>
</tr>
<tr class="even" nohover>
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
				$body='要删除的询价单并不存在，请返回列表刷新后再操作。（如果问题持续存在，请联系系统管理员。）';
			}
			$this->kclass->page['title'] .= ' - '.$inquiry['supplier'].' - 删除供应商询价单';
			$this->kclass->page['main']=$this->kclass->skin->dbody(array('title' => $this->baseurl . ' - <a href="/s.php?module=psupplier&action=view&supplierid='.$supplier['supplierid'].'">'.$inquiry['supplier'].'</a> - 删除供应商询价单', 'right' => '<a href="/s.php?module=psupplier">返回列表</a>', 'body'=>$body));
		}

		//
		function revivalInquiry(){
			if($this->kclass->input['inquiryid']<=0){
				$this->kclass->boinkIt('/s.php?module=psupplier');
			}
			if($this->kclass->input['confirm'] == 0){
				$this->kclass->boinkIt('/s.php?module=psupplier&action=view&supplierid='.$this->kclass->input['supplierid']);
			}
			if($this->kclass->input['inquiryid'] < 0){
				$e='<li>请从正确的页面进行操作，如果此问题持续存在，请联系网站管理员。</li>';
			}
			if(isset($e)){
				$this->kclass->messager(array(
					'title' => '恢复供应商询价单',
					'text' => '您在恢复供应商询价单的过程中有以下错误：<ul>'.$e.'</ul>',
					'url' => 'javascript:history.back()',
					'sec' => 3
				));
			}
			$inquiry=$this->kclass->DB->queryFirst("
				SELECT inquiry.inquiryid, inquiry.supplierid,
					supplier.title AS supplier
				FROM `inquiry`
				LEFT JOIN supplier ON (supplier.supplierid=inquiry.supplierid)
				WHERE inquiry.inquiryid='".$this->kclass->input['inquiryid']."'
			");
			if($inquiry){
				$this->kclass->DB->query("
					UPDATE `inquiry`
					SET killed=0,killer=0
					WHERE inquiryid='".$inquiry['inquiryid']."'
				");
				$this->kclass->messager(array(
					'title' => '恢复供应商询价单成功',
					'text' => '供应商 <b>'.$inquiryid['supplier'].'</b> 下的 询价单 已成功被标记为恢复!',
					'url' => '/s.php?module=psupplier&action=view&supplierid='.$inquiry['supplierid'],
					'sec' => 2
				));
			}else{
				$this->kclass->messager(array(
					'title' => '恢复供应商询价单失败',
					'text' => '您要恢复的供应商询价单，不存在！',
					'url' => '/s.php?module=psupplier&action=view&supplierid='.$inquiry['supplierid'],
					'sec' => 3
				));
			}
		}

		//
		function restoreInquiry(){
			if($this->kclass->input['inquiryid']<=0){
				$this->kclass->boinkIt('/s.php?module=psupplier');
			}
			$inquiry=$this->kclass->DB->queryFirst("
				SELECT inquiry.inquiryid, inquiry.supplierid,
					supplier.title AS supplier
				FROM `inquiry`
				LEFT JOIN supplier ON (supplier.supplierid=inquiry.supplierid)
				WHERE inquiry.inquiryid='".$this->kclass->input['inquiryid']."'
			");
			if($inquiry){
$body=<<<EOF
<form action="/s.php?module=psupplier&action=revivalInquiry" name="psupplier" method="post">
<input type="hidden" name="module" value="psupplier">
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
	<td class="middle">你确定要恢复产品 <a href="/s.php?module=psupplier&action=view&supplierid={$inquiry['supplierid']}" class="middle bold" target="_blank">{$inquiry['supplier']}</a> 的 <span class="big bold">询价单</span> 吗?</td>
</tr>
<tr class="odd">
	<td align="center">
		<label for="c1" class="red"><input type="radio" id="c1" name="confirm" value="1">是</label>
		<label for="c0" class="green"><input type="radio" id="c0" name="confirm" value="0" checked>否</label>
	</td>
</tr>
<tr class="even" nohover>
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
				$body='要恢复的询价单并不存在，请返回列表刷新后再操作。（如果问题持续存在，请联系系统管理员。）';
			}
			$this->kclass->page['title'] .= ' - '.$inquiry['supplier'].' - 恢复供应商询价单';
			$this->kclass->page['main']=$this->kclass->skin->dbody(array('title' => $this->baseurl . ' - <a href="/s.php?module=psupplier&action=view&supplierid='.$supplier['supplierid'].'">'.$inquiry['supplier'].'</a> - 恢复供应商询价单', 'right' => '<a href="/s.php?module=psupplier">返回列表</a>', 'body'=>$body));
		}
		//
		function quoting(){
			$quoting=$this->kclass->DB->queryFirst("
				SELECT q.*,
					su.supplierno,su.title AS supplier,
					c.title AS currency,
					po.title AS port,
					d.title AS deliveryterm,
					pt.entitle AS paymentterm,
					s.title AS shipmethod,
					u.username AS creator,
					us.username AS modifier
				FROM quoting AS q
				LEFT JOIN supplier AS su ON (su.supplierid=q.supplierid)
				LEFT JOIN currency AS c ON (c.currencyid=q.currencyid)
				LEFT JOIN port AS po ON (po.portid=q.portid)
				LEFT JOIN deliveryterm AS d ON (d.deliverytermid=q.deliverytermid)
				LEFT JOIN paymentterm AS pt ON (pt.paymenttermid=q.paymenttermid)
				LEFT JOIN shipmethod AS s ON (s.shipmethodid=q.shipmethodid)
				LEFT JOIN `user` AS u ON (u.userid=q.creator)
				LEFT JOIN `user` AS us ON (us.userid=q.modifier)
				WHERE q.killed=0 AND q.quotingid='".$this->kclass->input['quotingid']."'
				LIMIT 0,1
			");
			if(!$quoting){
				$e='<li>数据错误，很抱歉~</li>';
			}
			if(isset($e)){
				$this->kclass->messager(array(
					'title' => '新建入库记录的批次编号',
					'text' => '新建入库记录的批次编号<ul>'.$e.'</ul>',
					'url' => 'javascript:history.back()',
					'sec' => 3
				));
			}

			// 报价item
			$items=$this->kclass->DB->query("
				SELECT i.materialid,i.materialno,i.materialTitle,i.materialStandard,i.quantity,i.discount,i.price,i.amount,i.package,
					b.title AS brand,
					u.title AS unit,
					p.title AS packing,
					m.materialno AS olMaterialno,m.title AS olTitle,m.standard AS olStandard
				FROM item AS i
				LEFT JOIN brand AS b ON (b.brandid=i.brandid)
				LEFT JOIN unit AS u ON (u.unitid=i.unitid)
				LEFT JOIN packing AS p ON (p.packingid=i.packingid)
				LEFT JOIN material AS m ON (m.materialid=i.materialid)
				WHERE i.module='quoting' AND i.killed=0 AND i.mid='".$quoting['quotingid']."'
				ORDER BY i.created ASC
			");
			if($this->kclass->DB->numRows()){
				$i=1;
				$itemList='<table class="hundred small"><thead></tr><th colspan="11">询价物资列表</th></tr></thead><tbody><tr class="even center" nohover><td widht="15">ID</td><td width="50">物资编号</td><td>物资名称</th><td>物资规格</th><td>品牌</td><td>包装方式</td><td>包装</td><td>数量/单位</td><td>折扣</td><td>单价</td><td>总价</td></tr>';
				while($item=$this->kclass->DB->fetchArray($items)){
					$itemList.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').'">
						<td>'.$i.'</td>
						<td><a href="/s.php?module=material&action=view&materialid='.$item['materialid'].'">'.$this->kclass->iif($item['materialid']==0,$item['materialno'],$item['olMaterialno']).'</td>
						<td>'.$this->kclass->iif($item['materialid']==0,$item['materialTitle'],$item['olTitle']).'</td>
						<td>'.$this->kclass->iif($item['materialid']==0,$item['materialStandard'],$item['olStandard']).'</td>
						<td>'.$item['brand'].'</td>
						<td>'.$item['package'].'</td>
						<td>'.$item['packing'].'</td>
						<td>'.$item['quantity'].$item['unit'].'</td>
						<td>'.$item['discount'].'%</td>
						<td>'.$item['price'].'</td>
						<td>'.$item['amount'].'</td></tr>';
					$i++;
				}
				$itemList.='</tbody></table>';
			}

			$quoting['created']=date('Y-m-d',$quoting['created']);
			$quoting['dateline']=date('Y-m-d',$quoting['dateline']);
			if($quoting['modified']!=0)$modify='，由'.$quoting['modifier'].'于'.date('Y-m-d',$quoting['modified']).'修改';
			$reQuoting=$this->kclass->iif($quoting['quotingid']>0,'<a href="/s.php?module=psupplier&action=inquiry&inquiryid='.$quoting['inquiryid'].'">查看报价</a> <a href="/s.php?module=psupplier&action=addQuoting&inquiryid='.$quoting['inquiryid'].'">再次报价</a>','<a href="/s.php?module=psupplier&action=addQuoting&inquiryid='.$quoting['inquiryid'].'">报价</a>');

$body=<<<EOF
<div class="title"><span class="right small gray">由{$quoting['creator']}于{$quoting['created']}建立{$modify}。</span></div>
<table class="hundred">
<thead></tr><th colspan="4">查看报价</th></tr></thead>
<tbody>
<tr class="even">
	<td width="80">供应商：</td>
	<td width="400"><a href="/s.php?module=psupplier&action=view&supplierid={$quoting['supplierid']}">{$quoting['supplier']}（{$quoting['supplierno']}）</a></td>
	<td width="80">报价时间：</td>
	<td>{$quoting['created']}</td>
</tr><tr class="odd">
	<td width="80">港口：</td><td>{$quoting['port']}</td>
	<td>付款方式：</td><td>{$quoting['paymentterm']}</td>
</tr><tr class="odd">
	<td>运输方式：</td><td>{$quoting['shipmethod']}</td><td>交货条款: </td><td>{$quoting['deliveryterm']}</td>
</tr><tr class="even">
	<td>联系人: </td><td>{$quoting['contact']}</td>
	<td>币种：</td><td>{$quoting['currency']}</td>
</tr><tr class="odd">
	<td>规格: </td><td>{$quoting['standard']}</td>
	<td>物资信息: </td><td>{$quoting['material']}</td>
</tr><tr class="even">
	<td>工艺: </td><td>{$quoting['technics']}</td>
	<td>备注：</td><td>{$quoting['remark']}</td>
</tr>
</tbody>
</table>
{$itemList}
EOF;
			$this->kclass->tbline+=4;
			$this->kclass->page['title'].='查看报价';
			$this->kclass->page['onload'].='dc.tabhover();';
			$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - <a href="/s.php?module=psupplier&action=view&supplierid='.$quoting['supplierid'].'">'.$quoting['supplier'].'</a> - 查看报价', 'right'=>'<a href="/s.php?module=psupplier&action=view&supplierid='.$this->kclass->input['supplierid'].'">返回查看供应商</a>','right'=>'<div class="gray"><span class="right normal"><a href="/s.php?module=psupplier&action=updateInquiry&inquiryid='.$inquiry['inquiryid'].'">修改</a>　|　<a href="/p.php?action=order&amp;type=PI&amp;orderid=1" target="_blank">打印PI</a></span></div>','body'=>$body));
		}
		//
		function addQuoting(){
			$inquiry=$this->kclass->DB->queryFirst("SELECT * FROM inquiry WHERE killed=0 AND inquiryid='".$this->kclass->input['inquiryid']."' LIMIT 0,1");
			if($inquiry){
				$packing=$this->kclass->chooserPacking(array('name'=>'packingid','width'=>250,'selectedid'=>$inquiry['packingid'],'topname'=>''));
				$port=$this->kclass->chooserPort(array('name'=>'portid','width'=>250,'selectedid'=>$inquiry['portid'],'topname'=>''));
				$paymentterm=$this->kclass->chooserPaymentterm(array('name'=>'paymenttermid','width'=>250,'selectedid'=>$inquiry['paymenttermid'],'topname'=>''));
				$currency=$this->kclass->chooserCurrency(array('name'=>'currencyid','width'=>250,'selectedid'=>$inquiry['currencyid'],'topname'=>''));
				$shipmethod=$this->kclass->chooserShipmethod(array('name'=>'shipmethodid','width'=>250,'selectedid'=>$inquiry['shipmethodid'],'topname'=>''));
				$deliveryterm=$this->kclass->chooserDeliveryterm(array('name'=>'deliverytermid','width'=>250,'selectedid'=>$inquiry['deliverytermid'],'topname'=>''));

				$items=$this->kclass->DB->query("
					SELECT itemid,materialid,materialno,materialTitle,materialStandard,unitid,brandid,package,packingid,quantity
					FROM item
					WHERE killed=0 AND module='inquiry' AND mid='".$inquiry['inquiryid']."'
					ORDER BY itemid ASC
				");
				if($this->kclass->DB->numRows()){
					$i=0;
					while($item=$this->kclass->DB->fetchArray($items)){
						$i+=1;
						$itemList.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').' small">
							<td>'.$this->_chooserSupplierMaterial(array('name'=>'itemMaterialid['.$i.']','hasBlank'=>1,'width'=>150,'topname'=>'新物资','supplierid'=>$inquiry['supplierid'],'selectedid'=>$item['materialid'])).'</td>
							<td><input type="text" name="itemMaterialno['.$i.']" style="width:70px" value="'.$item['materialno'].'"></td>
							<td><input type="text" name="itemMaterialTitle['.$i.']" value="'.$item['materialTitle'].'"></td>
							<td><input type="text" name="itemStandard['.$i.']" value="'.$item['materialStandard'].'"></td>
							<td>'.$this->kclass->chooserBrand(array('name'=>'itemBrandid['.$i.']','hasBlank'=>1,'topname'=>'请选择','selectedid'=>$item['brandid'])).'</td>
							<td><input type="text" name="itemPackage['.$i.']" value="'.$item['package'].'"></td>
							<td>'.$this->kclass->chooserPacking(array('name'=>'itemPackingid['.$i.']','hasBlank'=>1,'topname'=>'请选择','selectedid'=>$item['packingid'],'width'=>100	)).'</td>
							<td><input type="text" name="itemDiscount['.$i.']" size="3" value="100" /></td>
							<td><input type="text" name="itemQuantity['.$i.']" value="'.$item['quantity'].'" size="10" /></td>
							<td>'.$this->kclass->chooserUnit(array('name'=>'itemUnitid['.$i.']','hasBlank'=>1,'topname'=>'请选择','selectedid'=>$item['unitid'])).'</td>
							<td><input type="text" name="itemPrice['.$i.']" value="'.$item['price'].'" size="10" /></td>
						</tr>';
					}
				}

$body=<<<EOF
<form action="/s.php?module=psupplier&action=insertQuoting" method="post">
<input type="hidden" name="module" value="psupplier" />
<input type="hidden" name="action" value="insertQuoting" />
<input type="hidden" name="inquiryid" value="{$this->kclass->input['inquiryid']}" />
<table class="hundred">
<thead></tr><th colspan="4">新建报价</th></tr></thead>
<tr class="odd">
	<td width="80">联 系 人：<span class="red bold">*</span></td><td width="400"><input name="contact" type="text" value="" /></td>
	<td width="80">币种：</td><td>{$currency}</td>	
</tr><tr class="even">
	<td width="80">港　　口：</td><td>{$port}</td>
	<td>付款方式：</td><td>{$paymentterm}</td>
</tr><tr class="odd">
	<td>运输方式：</td><td>{$shipmethod}</td>
	<td>交货条款: </td><td>{$deliveryterm}</td>
<tr class="even">
	<td>规格说明: </td><td><textarea style="width:450px;height:60px" name="quotingStandard" />{$inquiry['standard']}</textarea></td>
	<td>主要材料：: </td><td><textarea style="width:450px;height:60px" name="material" />{$inquiry['material']}</textarea></td>
</tr><tr class="odd">
	<td>工艺说明: </td><td><textarea style="width:450px;height:60px" name="technics" />{$inquiry['technics']}</textarea></td>
	<td>备　　注：</td><td><textarea style="width:450px;height:60px" name="remark" />{$inquiry['remark']}</textarea></td>
</tr>
</table>
<table class="hundred">
<thead></tr><th colspan="13">供应物资信息</th></tr></thead>
<tbody>
<tr class="center even" nohover><td>物资<span class="red bold">*</span></td><td>物资编号</td><td>物资名称</td><td>规格</td><td>品牌</td><td>包装方式</td><td>包装</td><td>折扣</td><td>数量<span class="red bold">*</span></td><td>单位<span class="red bold">*</span></td><td>单价<span class="red bold">*</span></td></tr>
		{$itemList}
	<tr class="odd">
		<td class="small gray" colspan="13">
			注意事项：
			如果所列表格不够，那么在提交保存后再添加申请的物资</td>
	</tr>
</tbody>
</table>
<table class="tlist tsuite" align="center" width="100%">
	<tbody>
		<tr class="even" nohover>
		<td align="right"><input type="submit" id="submitButton" value="  提交  " accesskey="s"></td>
		<td><input type="reset" value="  复位  "/></td>
		</tr>
	</tbody>
</table>
</form>
EOF;
			}else{
				$body='数据出错，很抱歉！';
			}

			$this->kclass->tbline+=4;
			$this->kclass->page['title'].='新增报价单';
			$this->kclass->page['onload'].='dc.tabhover()';
			$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - 新增报价单', 'right'=>'<a href="/s.php?module=psupplier&action=view&supplierid='.$this->kclass->input['supplierid'].'">返回查看供应商</a>','body'=>$body));
		}
		//
		function insertQuoting(){
			if($this->kclass->input['requestMethod'] != 'post'){
				$e='<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
			}else{
				if(strlen($this->kclass->input['contact']) < 2){
					$e .= '<li>请填写询价供应商的相关联系人（不能少于2个字符）。</li>';
				}
				if($this->kclass->input['currencyid'] <= 0){
					$e .= '<li>请选择要供应商询价的币种。</li>';
				}
			}
			if($e){
				$this->kclass->messager(array(
					'title' => '新增报价单',
					'text' => '您在新增报价单的过程中有以下错误：<ul>'.$e.'</ul>',
					'url' => 'javascript:history.back()',
					'sec' => 3
				));
			}
			$inquiry=$this->kclass->DB->queryFirst("
				SELECT i.*,
					s.supplierid,s.supplierno
				FROM inquiry AS i
				LEFT JOIN supplier AS s ON (s.supplierid=i.supplierid)
				WHERE i.killed=0 AND i.inquiryid='".$this->kclass->input['inquiryid']."'
				LIMIT 0,1
			");
			$this->kclass->DB->query("
				INSERT INTO quoting
					(inquiryid,supplierid,contact,currencyid,portid,paymenttermid,deliverytermid,shipmethodid,standard,material,technics,remark,created,creator)
				VALUES
					('".$this->kclass->input['inquiryid']."','".$inquiry['supplierid']."','".$this->kclass->input['contact']."','".$this->kclass->input['currencyid']."','".$this->kclass->input['portid']."','".$this->kclass->input['paymenttermid']."','".$this->kclass->input['deliverytermid']."','".$this->kclass->input['shipmethodid']."','".$this->kclass->input['quotingStandard']."','".$this->kclass->input['material']."','".$this->kclass->input['technics']."','".$this->kclass->input['remark']."','".TIMENOW."','".$this->kclass->user['userid']."')
			");
			$quotingid=$this->kclass->DB->insertID();
			$count=count($this->kclass->input['itemMaterialTitle']);
			for($i=1;$i<$count+1;$i++){
				$this->kclass->input['itemQuantity'][$i]=intVal($this->kclass->input['itemQuantity'][$i]);
				$this->kclass->input['itemPrice'][$i]=floatVal($this->kclass->input['itemPrice'][$i]);
				$this->kclass->input['itemDiscount'][$i]=intVal($this->kclass->input['itemDiscount'][$i]);
				if($this->kclass->input['itemDiscount'][$i]<1 OR $this->kclass->input['itemDiscount'][$i]>100){
					$this->kclass->input['itemDiscount'][$i]=100;
				}
				$amount=$this->kclass->input['itemQuantity'][$i] * $this->kclass->input['itemPrice'][$i];
				if($this->kclass->input['itemDiscount'][$i]<100){
					$amount=$amount * $this->kclass->input['itemDiscount'][$i]/100;
				}
				$amount=number_format($amount,2,'.','');
				$this->kclass->DB->query("
					INSERT INTO item
						(module,mid,materialid,materialno,materialTitle,materialStandard,brandid,unitid,package,packingid,quantity,price,amount,created,creator)
					VALUES
						('quoting','".$quotingid."','".$this->kclass->input['itemMaterialid'][$i]."','".$this->kclass->input['itemMaterialno'][$i]."','".$this->kclass->input['itemMaterialTitle'][$i]."','".$this->kclass->input['itemStandard'][$i]."','".$this->kclass->input['itemBrandid'][$i]."','".$this->kclass->input['itemUnitid'][$i]."','".$this->kclass->input['itemPackage'][$i]."','".$this->kclass->input['itemPackingid'][$i]."','".$this->kclass->input['itemQuantity'][$i]."','".$this->kclass->input['itemPrice'][$i]."','".$amount."','".TIMENOW."','".$this->kclass->user['userid']."')
				");
			}

			$this->kclass->DB->query("
				UPDATE inquiry SET
					quotingid='".$quotingid."',
					replied='".TIMENOW."',
					replyTimes=replyTimes+1
				WHERE killed=0 AND inquiryid='".$this->kclass->input['inquiryid']."'
			");

			$this->kclass->messager(array(
				'title' => '新建报价单',
				'text' => '报价单已新建成功!返回供应商详情页',
				'url' => '/s.php?module=psupplier&action=view&supplierid='.$inquiry['supplierid'],
				'sec' => 2
			));
		}
		//
		function updateQuoting(){
			if($this->kclass->input['quotingid']<=0 OR !$quoting=$this->kclass->DB->queryFirst("SELECT `quoting`.*, supplier.title AS supplier FROM `quoting` LEFT JOIN supplier ON (supplier.supplierid=quoting.supplierid) WHERE `quotingid`='".$this->kclass->input['quotingid']."'")){
				$this->kclass->boinkIt('/s.php?module=psupplier');
			}
			$this->kclass->br2nl=true;
			$quoting['standard']=$this->kclass->parseConvertValue($quoting['standard']);
			$quoting['material']=$this->kclass->parseConvertValue($quoting['material']);
			$quoting['technics']=$this->kclass->parseConvertValue($quoting['technics']);
			$quoting['remark']=$this->kclass->parseConvertValue($quoting['remark']);

			$currency=$this->kclass->chooserCurrency(array('name'=>'currencyid', 'hasBlank'=>1, 'width'=>100, 'selectedid'=>$quoting['currencyid']));
			$shipmethod=$this->kclass->chooserShipmethod(array('name'=>'shipmethodid', 'hasBlank'=>1, 'width'=>450, 'selectedid'=>$quoting['shipmethodid']));
			$paymentterm=$this->kclass->chooserPaymentterm(array('name'=>'paymenttermid', 'hasBlank'=>1, 'width'=>450, 'selectedid'=>$quoting['paymenttermid']));
			$deliveryterm=$this->kclass->chooserDeliveryterm(array('name'=>'deliverytermid', 'hasBlank'=>1, 'width'=>450, 'selectedid'=>$quoting['deliverytermid']));
			$port=$this->kclass->chooserPort(array('name'=>'portid', 'hasBlank'=>1, 'width'=>450, 'selectedid'=>$quoting['portid']));

			$items=$this->kclass->DB->query("SELECT * FROM `item` WHERE module='quoting' AND mid='".$quoting['quotingid']."' ORDER BY modified ASC");
			if($this->kclass->DB->numRows()){
				$k=1;
				while($item=$this->kclass->DB->fetchArray($items)){
					$itemtr .= '<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').' small">
						<td><input type="hidden" name="itemId['.$k.']" value="'.$item['itemid'].'" />'.$this->_chooserSupplierMaterial(array('name'=>'itemMaterialid['.$k.']','hasBlank'=>1,'width'=>150,'topname'=>'新物资','supplierid'=>$quoting['supplierid'],'selectedid'=>$item['materialid'])).'</td>
						<td><input type="text" name="itemMaterialno['.$k.']" style="width:70px" value="'.$item['materialno'].'"></td>
						<td><input type="text" name="itemMaterialTitle['.$k.']" value="'.$item['materialTitle'].'" style="width:110px"></td>
						<td><input type="text" name="itemStandard['.$k.']" value="'.$item['materialStandard'].'" style="width:110px"></td>
						<td>'.$this->kclass->chooserBrand(array('name'=>'itemBrandid['.$k.']','hasBlank'=>1,'topname'=>'请选择','selectedid'=>$item['brandid'])).'</td>
						<td><input type="text" name="itemPackage['.$k.']" value="'.$item['package'].'" size="16"></td>
						<td>'.$this->kclass->chooserPacking(array('name'=>'itemPackingid['.$k.']','hasBlank'=>1,'topname'=>'请选择','selectedid'=>$item['packingid'],'width'=>100	)).'</td>
						<td><input type="text" name="itemDiscount['.$k.']" value="'.$item['discount'].'" size="3" value="100" /></td>
						<td><input type="text" name="itemQuantity['.$k.']" value="'.$item['quantity'].'" size="10" /></td>
						<td>'.$this->kclass->chooserUnit(array('name'=>'itemUnitid['.$k.']','hasBlank'=>1,'topname'=>'请选择','selectedid'=>$item['unitid'])).'</td>
						<td><input type="text" name="itemPrice['.$k.']" value="'.$item['price'].'" size="10" /></td>
						<td><input type="checkbox" name="itemKill['.$k.'] value="'.$item['itemid'].'"></td>
						</tr>';
					$k++;
				}
			}
			$itemtr.='<tr><td colspan="12">新建明细</td></tr>';
			for($i=$k; $i < ($k+5); $i++){
				$itemtr .= '<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').' small">
					<td>'.$this->_chooserSupplierMaterial(array('name'=>'itemMaterialid['.$i.']','hasBlank'=>1,'width'=>150,'topname'=>'新物资','supplierid'=>$quoting['supplierid'],'selectedid'=>$item['materialid'])).'</td>
					<td><input type="text" name="itemMaterialno['.$i.']" style="width:70px"></td>
					<td><input type="text" name="itemMaterialTitle['.$i.']" style="width:110px"></td>
					<td><input type="text" name="itemStandard['.$i.']" style="width:110px"></td>
					<td>'.$this->kclass->chooserBrand(array('name'=>'itemBrandid['.$i.']','hasBlank'=>1,'topname'=>'请选择')).'</td>
					<td><input type="text" name="itemPackage['.$i.']" size="16"></td>
					<td>'.$this->kclass->chooserPacking(array('name'=>'itemPackingid['.$i.']','hasBlank'=>1,'topname'=>'请选择','width'=>100	)).'</td>
					<td><input type="text" name="itemDiscount['.$i.']" size="3" value="100" /></td>
					<td><input type="text" name="itemQuantity['.$i.']" size="10" /></td>
					<td>'.$this->kclass->chooserUnit(array('name'=>'itemUnitid['.$i.']','hasBlank'=>1,'topname'=>'请选择')).'</td>
					<td><input type="text" name="itemPrice['.$i.']" size="10" /></td>
					<td></td>
					</tr>';
			}

$body=<<<EOF
<form action="/s.php?module=psupplier&action=doupdateQuoting" name="psupplier" method="post">
<input type="hidden" name="module" value="psupplier">
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
	<th colspan="12">物资明细表</th>
</tr>
</thead>
<tbody>
<tr class="center even" nohover><td>物资<span class="red bold">*</span></td><td>物资编号</td><td>物资名称</td><td>规格</td><td>品牌</td><td>包装方式</td><td>包装</td><td>折扣</td><td>数量<span class="red bold">*</span></td><td>单位<span class="red bold">*</span></td><td>单价<span class="red bold">*</span></td><td>删</td></tr>
{$itemtr}
<tr class="even">
	<td colspan="12" class="gray small">注：①如行数不够，请保存后再进入修改页面。②至少有“产品”、“数量”、“单位”三个项目选择填写，该明细才会被保存。③“折扣”请填写百分比“%”。</td>
</tr>
</tbody>
<table class="hundred">
<tbody>
<tr class="odd" nohover>
	<td colspan="4" align="center">
		<input type="submit" value="  保存  " accesskey="s">
		<input type="reset" value="  复位  ">
	</td>
</tr>
</tbody>
</table>
</form>
EOF;
			$this->kclass->page['title'] .= $quoting['supplier'].' - 修改来自供应商报价单';
			$this->kclass->page['onload'] .= 'dc.tabhover()';
			$this->kclass->page['main']=$this->kclass->skin->dbody(array('title' => $this->baseurl . ' - <a href="/s.php?module=psupplier&action=view&supplierid='.$quoting['supplierid'].'">'.$quoting['supplier'].'</a> - 修改来自供应商报价单', 'right' => '<a href="/s.php?module=psupplier">返回列表</a>', 'body'=>$body));
		}
		//
		function doupdateQuoting(){
			if($this->kclass->input['requestMethod'] != 'post'){
				$e='<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
			}else{
				if(strlen($this->kclass->input['contact']) < 2){
					$e .= '<li>请填写询价供应商的相关联系人（不能少于2个字符）。</li>';
				}
				if($this->kclass->input['currencyid'] <= 0){
					$e .= '<li>请选择要供应商询价的币种。</li>';
				}
				$j=count($this->kclass->input['itemMaterialid']);
				$hasItem=0;
				for($i=1; $i<$j+1; $i++){
					if($this->kclass->input['itemKill'][$i]!='on' AND ($this->kclass->input['itemMaterialid'][$i]>0 OR $this->kclass->input['itemMaterialTitle']!='') AND $this->kclass->input['itemUnitid'][$i]>0 AND $this->kclass->input['itemQuantity'][$i]>0 AND $this->kclass->input['itemPrice'][$i]){
						$hasItem=1;
					}
				}
				if($hasItem==0){
					$e .= '<li>需要填写至少有一条订单明细，才能建立订单。</li>';
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
			for($i=1; $i<count($this->kclass->input['itemMaterialid'])+1; $i++){
				$this->kclass->input['itemQuantity'][$i]=intVal($this->kclass->input['itemQuantity'][$i]);
				$this->kclass->input['itemPrice'][$i]=floatVal($this->kclass->input['itemPrice'][$i]);
				$this->kclass->input['itemDiscount'][$i]=intVal($this->kclass->input['itemDiscount'][$i]);
				if($this->kclass->input['itemDiscount'][$i]<1 OR $this->kclass->input['itemDiscount'][$i]>100){
					$this->kclass->input['itemDiscount'][$i]=100;
				}
				$amount=$this->kclass->input['itemQuantity'][$i] * $this->kclass->input['itemPrice'][$i];
				if($this->kclass->input['itemDiscount'][$i]<100){
					$amount=$amount * $this->kclass->input['itemDiscount'][$i]/100;
				}
				$amount=number_format($amount,2,'.','');

				if($this->kclass->input['itemId'][$i]>0){
					if($this->kclass->input['itemKill'][$i]=='on'){
						$this->kclass->DB->query("UPDATE `item` SET killed='".TIMENOW."',killer='".$this->kclass->user['userid']."' WHERE itemid='".$this->kclass->input['itemSelect'][$key[$m]]."'");
					}else{
						$this->kclass->DB->query("
							UPDATE `item` SET
								`materialid`='".$this->kclass->input['itemMaterialid'][$i]."',
								`materialno`='".$this->kclass->input['itemMaterialno'][$i]."',
								`materialTitle`='".$this->kclass->input['itemMaterialTitle'][$i]."',
								`materialStandard`='".$this->kclass->input['itemMaterialStandard'][$i]."',
								`brandid`='".$this->kclass->input['itemBrandid'][$i]."',
								`package`='".$this->kclass->input['itemPackage'][$i]."',
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
				}elseif(($this->kclass->input['itemMaterialid'][$i]>0 OR $this->kclass->input['itemMaterialTitle']!='') AND $this->kclass->input['itemQuantity'][$i]!='' AND $this->kclass->input['itemUnitid'][$i]>0){
					$this->kclass->DB->query("
						INSERT INTO item
							(module,mid,materialid,materialno,materialTitle,materialStandard,brandid,unitid,package,packingid,quantity,price,amount,created,creator)
						VALUES
							('quoting','".$this->kclass->input['quotingid']."','".$this->kclass->input['itemMaterialid'][$i]."','".$this->kclass->input['itemMaterialno'][$i]."','".$this->kclass->input['itemMaterialTitle'][$i]."','".$this->kclass->input['itemStandard'][$i]."','".$this->kclass->input['itemBrandid'][$i]."','".$this->kclass->input['itemUnitid'][$i]."','".$this->kclass->input['itemPackage'][$i]."','".$this->kclass->input['itemPackingid'][$i]."','".$this->kclass->input['itemQuantity'][$i]."','".$this->kclass->input['itemPrice'][$i]."','".$amount."','".TIMENOW."','".$this->kclass->user['userid']."')
					");
				}
			}
			$this->kclass->DB->query("
				UPDATE `quoting` SET 
					`contact`='".$this->kclass->input['contact']."',
					`currencyid`='".$this->kclass->input['currencyid']."',
					`portid`='".$this->kclass->input['portid']."',
					`deliverytermid`='".$this->kclass->input['deliverytermid']."',
					`paymenttermid`='".$this->kclass->input['paymenttermid']."',
					`shipmethodid`='".$this->kclass->input['shipmethodid']."',
					`standard`='".$this->kclass->input['standard']."',
					`technics`='".$this->kclass->input['technics']."',
					`material`='".$this->kclass->input['material']."',
					`remark`='".$this->kclass->input['remark']."',
					`modified`='".TIMENOW."',
					`modifier`='".$this->kclass->user['userid']."'
				WHERE quotingid='".$this->kclass->input['quotingid']."'
			");
			$this->kclass->messager(array(
				'title' => '修改来自供应商报价单',
				'text' => '来自供应商的报价单已修改成功!',
				'url' => '/s.php?module=psupplier&action=view&supplierid='.$this->kclass->input['supplierid'],
				'sec' => 2
			));
		}
		//
		function killQuoting(){
			if($this->kclass->input['quotingid']<=0){
				$this->kclass->boinkIt('/s.php?module=psupplier');
			}
			if($this->kclass->input['confirm'] == 0){
				$this->kclass->boinkIt('/s.php?module=psupplier&action=view&supplierid='.$this->kclass->input['supplierid']);
			}
			if($this->kclass->input['quotingid'] < 0){
				$e='<li>请从正确的页面进行操作，如果此问题持续存在，请联系网站管理员。</li>';
			}
			if(isset($e)){
				$this->kclass->messager(array(
					'title' => '删除来自供应商的报价单',
					'text' => '您在删除来自供应商的报价单的过程中有以下错误：<ul>'.$e.'</ul>',
					'url' => 'javascript:history.back()',
					'sec' => 3
				));
			}
			$quoting=$this->kclass->DB->queryFirst("
				SELECT quoting.quotingid, quoting.supplierid,
					supplier.title AS supplier
				FROM quoting
				LEFT JOIN supplier ON (supplier.supplierid=quoting.supplierid)
				WHERE quoting.quotingid='".$this->kclass->input['quotingid']."'
			");
			if($quoting){
				if($quoting['inquiryid']>0){
					$this->kclass->DB->query("UPDATE `inquiry` SET quotingid=0, replied=0, replyTimes=(replyTimes-1) WHERE quotingid='".$quoting['quotingid']."' AND inquiryid='".$quoting['inquiryid']."'");
				}

				$this->kclass->DB->query("
					UPDATE `quoting`
					SET killed=".TIMENOW.",killer='".$this->kclass->user['userid']."'
					WHERE quotingid='".$quoting['quotingid']."'
				");
				$this->kclass->messager(array(
					'title' => '删除来自供应商的报价单成功',
					'text' => '来自供应商 <b>'.$quotingid['supplier'].'</b> 下的 报价单 已成功被标记为删除!',
					'url' => '/s.php?module=psupplier&action=view&supplierid='.$quoting['supplierid'],
					'sec' => 2
				));
			}else{
				$this->kclass->messager(array(
					'title' => '删除来自供应商的报价单失败',
					'text' => '您要删除的产品报价单，不存在！',
					'url' => '/s.php?module=psupplier&action=view&supplierid='.$quoting['supplierid'],
					'sec' => 3
				));
			}
		}

		//
		function removeQuoting(){
			if($this->kclass->input['quotingid']<=0){
				$this->kclass->boinkIt('/s.php?module=psupplier');
			}
			$quoting=$this->kclass->DB->queryFirst("
				SELECT quoting.quotingid, quoting.supplierid,
					supplier.title AS supplier
				FROM quoting
				LEFT JOIN supplier ON (supplier.supplierid=quoting.supplierid)
				WHERE quoting.quotingid='".$this->kclass->input['quotingid']."'
			");
			if($quoting){
$body=<<<EOF
<form action="/s.php?module=psupplier&action=killQuoting" name="psupplier" method="post">
<input type="hidden" name="module" value="psupplier">
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
	<td class="middle">你确定要删除来自供应商 <a href="/s.php?module=psupplier&action=view&supplierid={$quoting['supplierid']}" class="middle bold" target="_blank">{$quoting['supplier']}</a> 的 <span class="big bold">报价单</span> 吗?</td>
</tr>
<tr class="odd">
	<td align="center">
		<label for="c1" class="red"><input type="radio" id="c1" name="confirm" value="1">是</label>
		<label for="c0" class="green"><input type="radio" id="c0" name="confirm" value="0" checked>否</label>
	</td>
</tr>
<tr class="even" nohover>
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
				$body='要删除的报价单并不存在，请返回列表刷新后再操作。（如果问题持续存在，请联系系统管理员。）';
			}
			$this->kclass->page['title'] .= ' - '.$quoting['supplier'].' - 删除来自供应商的报价单';
			$this->kclass->page['main']=$this->kclass->skin->dbody(array('title' => $this->baseurl . ' - <a href="/s.php?module=psupplier&action=view&supplierid='.$supplier['supplierid'].'">'.$quoting['supplier'].'</a> - 删除来自供应商的报价单', 'right' => '<a href="/s.php?module=psupplier">返回列表</a>', 'body'=>$body));
		}

		//
		function revivalQuoting(){
			if($this->kclass->input['quotingid']<=0){
				$this->kclass->boinkIt('/s.php?module=psupplier');
			}
			if($this->kclass->input['confirm'] == 0){
				$this->kclass->boinkIt('/s.php?module=psupplier&action=view&supplierid='.$this->kclass->input['supplierid']);
			}
			if($this->kclass->input['quotingid'] < 0){
				$e='<li>请从正确的页面进行操作，如果此问题持续存在，请联系网站管理员。</li>';
			}
			if(isset($e)){
				$this->kclass->messager(array(
					'title' => '恢复来自供应商的报价单',
					'text' => '您在恢复来自供应商的报价单的过程中有以下错误：<ul>'.$e.'</ul>',
					'url' => 'javascript:history.back()',
					'sec' => 3
				));
			}
			$quoting=$this->kclass->DB->queryFirst("
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
					'url' => '/s.php?module=psupplier&action=view&supplierid='.$quoting['supplierid'],
					'sec' => 2
				));
			}else{
				$this->kclass->messager(array(
					'title' => '恢复来自供应商的报价单失败',
					'text' => '您要恢复来自供应商的报价单，不存在！',
					'url' => '/s.php?module=psupplier&action=view&supplierid='.$quoting['supplierid'],
					'sec' => 3
				));
			}
		}

		//
		function restoreQuoting(){
			if($this->kclass->input['quotingid']<=0){
				$this->kclass->boinkIt('/s.php?module=psupplier');
			}
			$quoting=$this->kclass->DB->queryFirst("
				SELECT quoting.quotingid, quoting.supplierid,
					supplier.title AS supplier
				FROM quoting
				LEFT JOIN supplier ON (supplier.supplierid=quoting.supplierid)
				WHERE quoting.quotingid='".$this->kclass->input['quotingid']."'
			");
			if($quoting){
$body=<<<EOF
<form action="/s.php?module=psupplier&action=revivalQuoting" name="psupplier" method="post">
<input type="hidden" name="module" value="psupplier">
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
	<td class="middle">你确定要恢复来自供应商 <a href="/s.php?module=psupplier&action=view&supplierid={$quoting['supplierid']}" class="middle bold" target="_blank">{$quoting['supplier']}</a> 的 <span class="big bold">报价单</span> 吗?</td>
</tr>
<tr class="odd">
	<td align="center">
		<label for="c1" class="red"><input type="radio" id="c1" name="confirm" value="1">是</label>
		<label for="c0" class="green"><input type="radio" id="c0" name="confirm" value="0" checked>否</label>
	</td>
</tr>
<tr class="even" nohover>
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
				$body='要恢复的报价单并不存在，请返回列表刷新后再操作。（如果问题持续存在，请联系系统管理员。）';
			}
			$this->kclass->page['title'] .= ' - '.$quoting['supplier'].' - 恢复来自供应商的报价单';
			$this->kclass->page['main']=$this->kclass->skin->dbody(array('title' => $this->baseurl . ' - <a href="/s.php?module=psupplier&action=view&supplierid='.$supplier['supplierid'].'">'.$quoting['supplier'].'</a> - 恢复来自供应商的报价单', 'right' => '<a href="/s.php?module=psupplier">返回列表</a>', 'body'=>$body));
		}
		//
		function addVisit(){
			if($this->kclass->input['supplierid']<=0){
				$this->kclass->boinkIt('/s.php?module=psupplier');
			}
			$supplier=$this->kclass->DB->queryFirst("SELECT supplierid, title FROM `supplier` WHERE killed=0 AND supplierid='".$this->kclass->input['supplierid']."'");
			$startHour='<select name="startHour"><option value="-1"> </option>';
			$endHour='<select name="endHour"><option value="-1"> </option>';
			$startMin='<select name="startMin"><option value="-1"> </option>';
			$endMin='<select name="endMin"><option value="-1"> </option>';
			for($i=0; $i < 24; $i++){
				$startHour.='<option value="'.$i.'">'.sprintf('%02d', $i).'</option>';
				$endHour.='<option value="'.$i.'">'.sprintf('%02d', $i).'</option>';
			}
			for($i=0; $i < 59; $i++){
				$startMin.='<option value="'.$i.'">'.sprintf('%02d', $i).'</option>';
				$endMin.='<option value="'.$i.'">'.sprintf('%02d', $i).'</option>';
			}
			$startHour.='</select>';
			$endHour.='</select>';
			$startMin.='</select>';
			$endMin.='</select>';

$body=<<<EOF
<form action="/s.php?module=psupplier&action=insertVisit" name="psupplier" method="post">
<input type="hidden" name="module" value="psupplier">
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
<tr class="even" nohover>
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
			$this->kclass->page['main']=$this->kclass->skin->dbody(array('title' => $this->baseurl . ' - <a href="/s.php?module=psupplier&action=view&supplierid='.$supplier['supplierid'].'">'.$supplier['title'].'</a> - 互访记录', 'right' => '<a href="/s.php?module=psupplier">返回列表</a>', 'body'=>$body));
		}
		//
		function insertVisit(){
			if($this->kclass->input['requestMethod'] != 'post'){
				$e='<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
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
			$sd=explode('-', $this->kclass->input['startDate']);
			$start=mktime($this->kclass->input['startHour'],$this->kclass->input['startMin'],0,$sd[1],$sd[2],$sd[0]);
			$ed=explode('-', $this->kclass->input['endDate']);
			$end=mktime($this->kclass->input['endHour'],$this->kclass->input['endMin'],0,$ed[1],$ed[2],$ed[0]);
			$this->kclass->DB->query("
				INSERT INTO `visit` (`supplierid`, `start`, `end`, `theirstaff`, `ourstaff`, `place`, `purpose`, `point`, `schedule`, `exception`, `remark`, `creator`, `created`) VALUES ('".$this->kclass->input['supplierid']."', '".$start."', '".$end."', '".$this->kclass->input['theirstaff']."', '".$this->kclass->input['ourstaff']."', '".$this->kclass->input['place']."', '".$this->kclass->input['purpose']."', '".$this->kclass->input['point']."', '".$this->kclass->input['schedule']."', '".$this->kclass->input['exception']."', '".$this->kclass->input['remark']."', '".$this->kclass->user['userid']."', '".TIMENOW."')
			");
			$visitid=$this->kclass->DB->insertID();
			$this->kclass->messager(array(
				'title' => '新建互访记录',
				'text' => '与供应商<b>'.$this->kclass->input['title'].'</b> 的互访记录已新建成功!',
				'url' => '/s.php?module=psupplier&action=view&supplierid='.$this->kclass->input['supplierid'],
				'sec' => 2
			));
		}
		//
		function doupdateVisit(){
			if($this->kclass->input['requestMethod'] != 'post'){
				$e='<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
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
					'title' => '修改发往供应商互访记录',
					'text' => '您在修改发往供应商互访记录的过程中有以下错误：<ul>'.$e.'</ul>',
					'url' => 'javascript:history.back()',
					'sec' => 3
				));
			}
			$sd=explode('-', $this->kclass->input['startDate']);
			$start=mktime($this->kclass->input['startHour'],$this->kclass->input['startMin'],0,$sd[1],$sd[2],$sd[0]);
			$ed=explode('-', $this->kclass->input['endDate']);
			$end=mktime($this->kclass->input['endHour'],$this->kclass->input['endMin'],0,$ed[1],$ed[2],$ed[0]);

			$this->kclass->DB->query("
				UPDATE `visit` SET 
					`supplierid`='".$this->kclass->input['contact']."',
					`start`='".$this->kclass->input['currencyid']."',
					`end`='".$this->kclass->input['portid']."',
					`theirstaff`='".$this->kclass->input['deliverytermid']."',
					`ourstaff`='".$this->kclass->input['deliverytermid']."',
					`place`='".$this->kclass->input['deliverytermid']."',
					`purpose`='".$this->kclass->input['deliverytermid']."',
					`point`='".$this->kclass->input['deliverytermid']."',
					`schedule`='".$this->kclass->input['deliverytermid']."',
					`exception`='".$this->kclass->input['deliverytermid']."',
					`remark`='".$this->kclass->input['deliverytermid']."',
					`modified`='".TIMENOW."',
					`modifier`='".$this->kclass->user['userid']."'
				WHERE visitid='".$this->kclass->input['visitid']."'
			");
			$this->kclass->messager(array(
				'title' => '修改发往供应商互访记录',
				'text' => '发往供应商<b>'.$this->kclass->input['title'].'</b> 的互访记录已修改成功!',
				'url' => '/s.php?module=psupplier&action=view&supplierid='.$this->kclass->input['supplierid'],
				'sec' => 2
			));
		}

		//
		function updateVisit(){
			if($this->kclass->input['visitid']<=0 OR !$visit=$this->kclass->DB->queryFirst("SELECT `visit`.*, supplier.title AS supplier FROM `visit` LEFT JOIN supplier ON (supplier.supplierid=visit.supplierid) WHERE `visitid`='".$this->kclass->input['visitid']."'")){
				$this->kclass->boinkIt('/s.php?module=psupplier');
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
			for($i=0; $i < 24; $i++){
				$startHour.='<option value="'.$i.'">'.sprintf('%02d', $i).'</option>';
				$endHour.='<option value="'.$i.'">'.sprintf('%02d', $i).'</option>';
			}
			for($i=0; $i < 59; $i++){
				$startMin.='<option value="'.$i.'">'.sprintf('%02d', $i).'</option>';
				$endMin.='<option value="'.$i.'">'.sprintf('%02d', $i).'</option>';
			}
			$startHour.='</select>';
			$endHour.='</select>';
			$startMin.='</select>';
			$endMin.='</select>';

$body=<<<EOF
<form action="/s.php?module=psupplier&action=doupdateVisit" name="psupplier" method="post">
<input type="hidden" name="module" value="psupplier">
<input type="hidden" name="action" value="doupdateVisit">
<input type="hidden" name="supplierid" value="{$visit['supplierid']}">
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
<tr class="even" nohover>
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
			$this->kclass->page['main']=$this->kclass->skin->dbody(array('title' => $this->baseurl . ' - <a href="/s.php?module=psupplier&action=view&supplierid='.$visit['supplierid'].'">'.$visit['supplier'].'</a> - 修改发往供应商互访记录', 'right' => '<a href="/s.php?module=psupplier">返回列表</a>', 'body'=>$body));
		}
		//
		function killVisit(){
			if($this->kclass->input['visitid']<=0){
				$this->kclass->boinkIt('/s.php?module=psupplier');
			}
			if($this->kclass->input['confirm'] == 0){
				$this->kclass->boinkIt('/s.php?module=psupplier&action=view&supplierid='.$this->kclass->input['supplierid']);
			}
			if($this->kclass->input['visitid'] < 0){
				$e='<li>请从正确的页面进行操作，如果此问题持续存在，请联系网站管理员。</li>';
			}
			if(isset($e)){
				$this->kclass->messager(array(
					'title' => '删除发往供应商的互访记录',
					'text' => '您在删除发往供应商的互访记录的过程中有以下错误：<ul>'.$e.'</ul>',
					'url' => 'javascript:history.back()',
					'sec' => 3
				));
			}
			$visit=$this->kclass->DB->queryFirst("
				SELECT visit.visitid, visit.supplierid,
					supplier.title AS supplier
				FROM visit
				LEFT JOIN supplier ON (supplier.supplierid=visit.supplierid)
				WHERE visit.visitid='".$this->kclass->input['visitid']."'
			");
			if($visit){
				$this->kclass->DB->query("
					UPDATE `visit`
					SET killed=".TIMENOW.",killer='".$this->kclass->user['userid']."'
					WHERE visitid='".$visit['visitid']."'
				");
				$this->kclass->messager(array(
					'title' => '删除发往供应商的互访记录成功',
					'text' => '发往供应商 <b>'.$visit['supplier'].'</b> 下的 互访记录 已成功被标记为删除!',
					'url' => '/s.php?module=psupplier&action=view&supplierid='.$visit['supplierid'],
					'sec' => 2
				));
			}else{
				$this->kclass->messager(array(
					'title' => '删除发往供应商的互访记录失败',
					'text' => '您要删除的产品互访记录，不存在！',
					'url' => '/s.php?module=psupplier&action=view&supplierid='.$visit['supplierid'],
					'sec' => 3
				));
			}
		}

		//
		function removeVisit(){
			if($this->kclass->input['visitid']<=0){
				$this->kclass->boinkIt('/s.php?module=psupplier');
			}
			$visit=$this->kclass->DB->queryFirst("
				SELECT visit.visitid, visit.supplierid,
					supplier.title AS supplier
				FROM visit
				LEFT JOIN supplier ON (supplier.supplierid=visit.supplierid)
				WHERE visit.visitid='".$this->kclass->input['visitid']."'
			");
			if($visit){
$body=<<<EOF
<form action="/s.php?module=psupplier&action=killVisit" name="supplier" method="post">
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
	<td class="middle">你确定要删除发往供应商 <a href="/s.php?module=psupplier&action=view&supplierid={$visit['supplierid']}" class="middle bold" target="_blank">{$visit['supplier']}</a> 的 <span class="big bold">互访记录</span> 吗?</td>
</tr>
<tr class="odd">
	<td align="center">
		<label for="c1" class="red"><input type="radio" id="c1" name="confirm" value="1">是</label>
		<label for="c0" class="green"><input type="radio" id="c0" name="confirm" value="0" checked>否</label>
	</td>
</tr>
<tr class="even" nohover>
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
				$body='要删除的互访记录并不存在，请返回列表刷新后再操作。（如果问题持续存在，请联系系统管理员。）';
			}
			$this->kclass->page['title'] .= ' - '.$visit['supplier'].' - 删除发往供应商的互访记录';
			$this->kclass->page['main']=$this->kclass->skin->dbody(array('title' => $this->baseurl . ' - <a href="/s.php?module=psupplier&action=view&supplierid='.$visit['supplierid'].'">'.$visit['supplier'].'</a> - 删除发往供应商的互访记录', 'right' => '<a href="/s.php?module=psupplier">返回列表</a>', 'body'=>$body));
		}

		//
		function revivalVisit(){
			if($this->kclass->input['visitid']<=0){
				$this->kclass->boinkIt('/s.php?module=psupplier');
			}
			if($this->kclass->input['confirm'] == 0){
				$this->kclass->boinkIt('/s.php?module=psupplier&action=view&supplierid='.$this->kclass->input['supplierid']);
			}
			if($this->kclass->input['visitid'] < 0){
				$e='<li>请从正确的页面进行操作，如果此问题持续存在，请联系网站管理员。</li>';
			}
			if(isset($e)){
				$this->kclass->messager(array(
					'title' => '恢复发往供应商的互访记录',
					'text' => '您在恢复发往供应商的互访记录的过程中有以下错误：<ul>'.$e.'</ul>',
					'url' => 'javascript:history.back()',
					'sec' => 3
				));
			}
			$visit=$this->kclass->DB->queryFirst("
				SELECT visit.visitid, visit.supplierid,
					supplier.title AS supplier
				FROM visit
				LEFT JOIN supplier ON (supplier.supplierid=visit.supplierid)
				WHERE visit.visitid='".$this->kclass->input['visitid']."'
			");
			if($visit){
				$this->kclass->DB->query("
					UPDATE `visit`
					SET killed=0,killer=0
					WHERE visitid='".$visit['visitid']."'
				");
				$this->kclass->messager(array(
					'title' => '恢复发往供应商的互访记录成功',
					'text' => '供应商 <b>'.$visitid['supplier'].'</b> 下的 互访记录 已成功被标记为恢复!',
					'url' => '/s.php?module=psupplier&action=view&supplierid='.$visit['supplierid'],
					'sec' => 2
				));
			}else{
				$this->kclass->messager(array(
					'title' => '恢复发往供应商的互访记录失败',
					'text' => '您要恢复发往供应商的互访记录，不存在！',
					'url' => '/s.php?module=psupplier&action=view&supplierid='.$visit['supplierid'],
					'sec' => 3
				));
			}
		}
		//
		function restoreVisit(){
			if($this->kclass->input['visitid']<=0){
				$this->kclass->boinkIt('/s.php?module=psupplier');
			}
			$visit=$this->kclass->DB->queryFirst("
				SELECT visit.visitid, visit.supplierid,
					supplier.title AS supplier
				FROM visit
				LEFT JOIN supplier ON (supplier.supplierid=visit.supplierid)
				WHERE visit.visitid='".$this->kclass->input['visitid']."'
			");
			if($visit){
$body=<<<EOF
<form action="/s.php?module=psupplier&action=revivalVisit" name="supplier" method="post">
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
	<td class="middle">你确定要恢复发往供应商 <a href="/s.php?module=psupplier&action=view&supplierid={$visit['supplierid']}" class="middle bold" target="_blank">{$visit['supplier']}</a> 的 <span class="big bold">互访记录</span> 吗?</td>
</tr>
<tr class="odd">
	<td align="center">
		<label for="c1" class="red"><input type="radio" id="c1" name="confirm" value="1">是</label>
		<label for="c0" class="green"><input type="radio" id="c0" name="confirm" value="0" checked>否</label>
	</td>
</tr>
<tr class="even" nohover>
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
				$body='要恢复的互访记录并不存在，请返回列表刷新后再操作。（如果问题持续存在，请联系系统管理员。）';
			}
			$this->kclass->page['title'] .= ' - '.$visit['supplier'].' - 恢复发往供应商的互访记录';
			$this->kclass->page['main']=$this->kclass->skin->dbody(array('title' => $this->baseurl . ' - <a href="/s.php?module=psupplier&action=view&supplierid='.$supplier['supplierid'].'">'.$visit['supplier'].'</a> - 恢复发往供应商的互访记录', 'right' => '<a href="/s.php?module=psupplier">返回列表</a>', 'body'=>$body));
		}
		
		// 批量导入
		function import(){
			$upload=$this->kclass->upload(array('title'=>'批量导入：'));
$body=<<<EOF
<form method="post" action="/s.php?module=psupplier&action=doimport">
<input type="hidden" name="module" value="psupplier">
<input type="hidden" name="action" value="doimport">
{$upload}
<table class="tlist tsuite" align="center" width="100%">
<tbody>
<tr class="odd center" nohover>
<td colspan=4><input type="submit" id="submitButton" value="  提交  "><input type="reset" value="  复位  "/></td>
</tr>
</tbody>
</table>
</form>
EOF;
			$this->kclass->page['title']=' 批量导入供应商';
			$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - 批量导入供应商', 'right'=>'<a href="/s.php?module=psupplier">返回列表</a>','body'=>$body));
		}
		//
		function doimport(){
			require ROOT.'/phpexcel/Classes/PHPExcel.php';
			require ROOT.'/PHPExcel/Classes/PHPExcel/IOFactory.php';
			$this->kclass->input['times']+=1;
			$fileCount=0;
			if(count($this->kclass->input['attach'])>0 AND $this->kclass->input['times']==1){
				foreach($this->kclass->input['attach'] as $k => $v){
					if($v==0 OR $v==1){//0:保留，需要检查title与ordering是否更改。1:选择
						$cdt='';
						if($this->kclass->input['attachTitle'][$k] != $this->kclass->input['attachOrgTitle'][$k])$cdt="`title`='".$this->kclass->input['attachTitle'][$k]."', ";
						if($this->kclass->input['attachOrdering'][$k] != $this->kclass->input['attachOrgOrdering'][$k])$cdt.="`ordering`='".$this->kclass->input['attachOrdering'][$k]."', ";
						if($v==1)$cdt.= "`module`='".$this->kclass->input['module']."', ";
						if($cdt != ''){
							$this->kclass->DB->query("UPDATE `attachs` SET ".$cdt."`modifier`='".$this->kclass->user['userid']."', `modified`='".TIMENOW."' WHERE `attachid`='".$k."'");
						}
						$fileCount++;
					}elseif($v==2){//删除
						$attach=$this->kclass->DB->queryFirst("SELECT `folder`, `newName`, `image`, `imginfo` FROM `attachs` WHERE `attachid`='".$k."'");
						if($attach['image']==1){
							$img=unserialize($attach['imginfo']);
							@unlink('public/attachs/'.$img['thumb']);
							@unlink('public/attachs/'.$img['senior']);
							@unlink('public/attachs/'.$img['big']);
						}
						@unlink('public/attachs/'.$attach['folder'].'/'.$attach['newName']);
						$this->kclass->DB->query("DELETE FROM `attachs` WHERE `attachid`='".$k."'");
					}
				}
			}else{
				$fileCount=1;
			}
			if($fileCount > 0){
				if($attach=$this->kclass->DB->queryFirst("SELECT COUNT(attachid) AS n,newName,folder,type FROM `attachs` WHERE `module`='".$this->kclass->input['module']."' AND `mid`=0 AND `image`<> 1 ORDER BY ordering ASC")){
					$objPHPExcel=PHPExcel_IOFactory::load('public/attachs/'.$attach['folder'].'/'.$attach['newName']);
					foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
						$worksheetTitle=$worksheet->getTitle();
						$highestRow=$worksheet->getHighestRow(); // e.g. 10
						if($highestRow>1){
							$highestColumn=$worksheet->getHighestColumn(); // e.g 'F'
							$highestColumnIndex=PHPExcel_Cell::columnIndexFromString($highestColumn);
							$nrColumns=ord($highestColumn)-64;

							$total=ceil(($highestRow-1)/10);//刷新次数
							$count=$this->kclass->iif($this->kclass->input['times']==$total,$highestRow,($this->kclass->input['times'])*10+1);
							$start=($this->kclass->input['times']-1)*10+2;
							for($row=$start;$row<=$count;$row++){
								$val=array();
								for ($col=0;$col<$highestColumnIndex;$col++) {
									$cell=$worksheet->getCellByColumnAndRow($col, $row);
									$val[$col]=$cell->getValue();
								}
								if($val[0]!='' AND $val[1]!=''){
									$this->kclass->DB->query("
										INSERT INTO originalSupplier
											(supplierno,title,bankAccountName,bank,bankAccount,linkman,position,linkman2,position2,telephone,mobile,address,remark,created,creator)
										VALUES
											('".$val[0]."','".$val[1]."','".$val[2]."','".$val[3]."','".$val[4]."','".$val[5]."','".$val[6]."','".$val[7]."','".$val[8]."','".$val[9]."','".$val[10]."','".$val[11]."','".$val[12]."','".TIMENOW."','".$this->kclass->user['userid']."')
									");
								}
							}
						}
					}
				}
			}
			if($this->kclass->input['times']==$total){
				$this->kclass->messager(array(
					'title' => '批量导入供应商',
					'text' => '供应商批量全部导入成功！',
					'url' => '/s.php?module=psupplier&action=listOriginal',
					'sec' => 2
				));
			}else{
				$this->kclass->messager(array(
					'title' => '批量导入供应商',
					'text' => '正在批量导入供应商，每次导入10个，请等待自动返回页面...',
					'url' => '/s.php?module=psupplier&action=doimport&times='.$this->kclass->input['times'],
					'sec' => 2
				));
			}
		}
		//
		function listOriginal(){
			$suppliers=$this->kclass->DB->query("SELECT * FROM originalsupplier ORDER BY originalid ASC");
			if($this->kclass->DB->numRows()){
				while($supplier=$this->kclass->DB->fetchArray($suppliers)){
					$r.='<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').'">
					<td>'.$supplier['supplierno'].'</td>
					<td class="middle bold">'.$supplier['title'].'</td>
					<td>'.$supplier['bankAccountName'].'</a></td>
					<td>'.$supplier['bank'].'</td>
					<td>'.$supplier['bankAccount'].'</td>
					<td>'.$supplier['linkman'].'</td>
					<td>'.$supplier['position'].'</td>
					<td>'.$supplier['linkman2'].'</td>
					<td>'.$supplier['position2'].'</td>
					<td>'.$supplier['telephone'].'</td>
					<td>'.$supplier['mobile'].'</td>
					<td>'.$supplier['address'].'</td>
					<td>'.$supplier['remark'].'</td></tr>';
				}
			}
$body .= <<<EOF
<table>
<thead>
<tr>
	<th>编号</th>
	<th>供应商名称</th>
	<th>户名</th>
	<th>开户行</th>
	<th>卡号（帐号）</th>
	<th>联系人1</th>
	<th>称谓1	</th>
	<th>联系人2</th>
	<th>称谓2</th>
	<th>手机</th>
	<th>电话</th>
	<th>地址</th>
	<th>备注</th>
</tr>
</thead>
<tbody>
{$r}
</tr>
</tbody>
</table>
EOF;
		$this->kclass->page['title']=' 原始物资列表';
		$this->kclass->page['main'] .= $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 原始供应商列表', 'right' => '<a href="/s.php?module=psupplier&action=exchange">转换到供应商列表</a>', 'body'=>$body));
		}
		//
		function exchange(){
			$this->kclass->input['times']+=1;
			$originalSuppliers=$this->kclass->DB->query("SELECT * FROM originalsupplier ORDER BY originalid ASC");
			$counter=$this->kclass->DB->numRows();
			$total=ceil(($counter)/10);//刷新次数
			$count=$this->kclass->iif($this->kclass->input['times']==$total,$counter,($this->kclass->input['times'])*10);
			$min=($this->kclass->input['times']-1)*10;
			$max=$this->kclass->input['times']*10;
			if($counter){
				$i=0;
				while($originalSupplier=$this->kclass->DB->fetchArray($originalSuppliers)){
					$supplier=$this->kclass->DB->queryFirst("SELECT supplierid FROM supplier WHERE supplierno='".$originalSupplier['supplierno']."'");
					if(!$supplier){
						$this->kclass->DB->query("
							INSERT INTO supplier
								(field,supplierno,title,linkman,linkman2,position,position2,telephone,mobile,fax,address,remark,created,creator)
							VALUES
								('produce','".$originalSupplier['supplierno']."','".$originalSupplier['title']."','".$originalSupplier['linkman']."','".$originalSupplier['linkman2']."','".$originalSupplier['position']."','".$originalSupplier['position2']."','".$originalSupplier['telephone']."','".$originalSupplier['mobile']."','".$originalSupplier['fax']."','".$originalSupplier['address']."','".$originalmaterial['remark']."','".TIMENOW."','".$this->kclass->user['userid']."')
						");
						$supplierid=$this->kclass->DB->insertID();
					}else{
						$supplierid=$supplier['supplierid'];
					}
					$bankDetail[$supplierid][]=array('supplierid'=>$supplierid,'bankAccountName'=>$originalSupplier['bankAccountName'],'bank'=>$originalSupplier['bank'],'bankAccount'=>$originalSupplier['bankAccount']);
					$i++;
				}
			}
			
			if($this->kclass->input['times']==$total){
				foreach($bankDetail as $key => $val){
					foreach($val as $k=>$v){
						$this->kclass->DB->query("
							INSERT INTO bankdetail 
								(supplierid,bankAccountName,bank,bankAccount,created,creator) 
							VALUES 
								('".$v['supplierid']."','".$v['bankAccountName']."','".$v['bank']."','".$v['bankAccount']."','".TIMENOW."','".$this->kclass->user['userid']."')
						");
					}
				}
				$this->kclass->messager(array(
					'title' => '加入供应商列表',
					'text' => '供应商加入供应商列表成功！',
					'url' => '/s.php?module=psupplier&action=list',
					'sec' => 2
				));
			}else{
				$this->kclass->messager(array(
					'title' => '加入供应商列表',
					'text' => '正在批量加入供应商列表，每次加入10个，请等待自动返回页面...',
					'url' => '/s.php?module=psupplier&action=exchange&times='.$this->kclass->input['times'],
					'sec' => 2
				));
			}
		}

		
/***********************private function***************************************/
		//
		function _chooserSupplierMaterial($b){
			if($b['orderby'] == ''){
				$b['orderby']='ordering';
			}
			if($b['direction'] == ''){
				$b['direction']='ASC';
			}
			if($b['width'] > 0){
				$b['width']=' style="width: '.$b['width'].'px;"';
			}

			$supplierMaterials=$this->kclass->DB->query("
				SELECT `suppliermaterial`.*,
					`material`.materialno,`material`.title AS materialTitle,`material`.standard AS materialStandard 
				FROM `suppliermaterial`
				LEFT JOIN `material` ON (`material`.materialid=`suppliermaterial`.materialid)
				WHERE `suppliermaterial`.killed=0 AND `suppliermaterial`.supplierid='".$b['supplierid']."'
				ORDER BY ".$b['orderby']." ".$b['direction']."
			");
			$r='<select id="'.$b['name'].'" name="'.$b['name'].'"'.$b['width'].'>';
			if($this->kclass->DB->numRows()){
				if($b['hasBlank']){
					$r.='<option value="0">'.$b['topname'].'</option>';
				}
				while($supplierMaterial=$this->kclass->DB->fetchArray($supplierMaterials)){
					$r.='<option value="'.$supplierMaterial['materialid'].'" ';
					if($b['selectedid']==$supplierMaterial['materialid']){
						$r.='selected';
					}
					$r.='>'.$supplierMaterial['materialno'].'　'.$supplierMaterial['materialTitle'].'　'.$supplierMaterial['materialStandard'].'</option>';
				}
			}
			$r .= '</select>';

			return $r;
		}
		function _chooserCategory($id=0){
			foreach($this->category AS $category){
				$r.='<input type="radio" name="categoryid" value="'.$category['id'].'" ';
				if($category['id']==$id){
					$r.=' selected';
				}
				$r.='>'.$category['title'];
			}
			return $r;
		}
		/**
		 *确认删除供应商评价记录
		 *Created 2014-11-07
		 */
		function killconsignevaluate(){	
		$consignevaluateid = $this->kclass->input['consignevaluateid'];
		//echo $consignevaluateid;
		$body=<<<EOF
<form method="post" action="/s.php?module=psupplier&action=dokillconsignevaluate&consignevaluateid={$consignevaluateid}">
<table class="tlist tsuite" align="center" width="320">
<thead>
<tr><th colspan="4" align="center">确认是否要删除该供应闪评价记录？</th></tr>
</thead>
<tbody>
<tr class="odd center" nohover>
<td colspan="4"><input type="radio" name="confirm" value="y" /> 是  <input type="radio" name="confirm"  value="n" checked /> 否 </td>
</tr>
<tr class="odd center" nohover>
<td colspan="4"><input type="submit" id="submitButton" value="  提交  "></td>
</tr>
</tbody>
</table>
</form>
EOF;
	
		
			$this->kclass->page['title'].='删除供应商评价记录';
			$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - <a href="/s.php?module=psupplier&action=inbounditemlist">供应商入库明细列表</a> - 删除供应商评价记录','body'=>$body));
		
		}
		/**
		 *执行删除供应商评价记录
		 *Created 2014-11-07
		 */
		function dokillconsignevaluate(){	
			if($this->kclass->input['requestMethod']!='post'){
				$e='<li>请从正确的页面进行操作，如果此问题持续存在，请联系网站管理员。</li>';
			}
			$confirmvalue = $this->kclass->input['confirm'];
			$consignevaluateid = $this->kclass->input['consignevaluateid'];
			$killer = $this->kclass->user['userid'];
			$killed = TIMENOW;
			//echo $killer."<br/>".$consignevaluateid."<br />";
			if($confirmvalue=="y"){
				$sql = $this->kclass->DB->query("UPDATE consignevaluate SET killer='{$killer}',killed='{$killed}' WHERE consignevaluateid='{$consignevaluateid}'");
				if($sql==true){
					$this->kclass->messager(array(
						'title' => '删除供应商评价记录成功！',
						'text' => '正在为您进行页面跳转...',
						'url' => '/s.php?module=psupplier&action=inbounditemlist',
						'sec' => 2
					));
				}else{
					$this->kclass->messager(array(
						'title' => '删除供应商评价记录失败！',
						'text' => '未获取到该条供应商评价信息...',
						'url' => '/s.php?module=psupplier&action=inbounditemlist',
						'sec' => 2
					));					
				}
			}else{
				$this->kclass->messager(array(
					'title' => '取消供应商评价记录删除操作！',
					'text' => '正在为您进行页面跳转...',
					'url' => '/s.php?module=psupplier&action=inbounditemlist',
					'sec' => 2
				));			
			}
		}
	}
?>