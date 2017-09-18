<?php
class product{
	function autoRun(){
		$this->kclass->page['title'] = '产品';
		$this->baseurl = '<a href="/s.php">首页</a> - <a href="/s.php?module=product">产品</a>';
		$this->right = '<a href="/s.php?module=product">列表</a> <a href="/s.php?module=product&action=add">新建</a>';
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
			case 'addParts':
				$this->addParts();
			break;
			case 'insertParts':
				$this->insertParts();
			break;
			case 'updateParts':
				$this->updateParts();
			break;
			case 'doupdateParts':
				$this->doupdateParts();
			break;
			case 'addPartsPackage':
				$this->addPartsPackage();
			break;
			case 'insertPartsPackage':
				$this->insertPartsPackage();
			break;
			case 'updatePartsPackage':
				$this->updatePartsPackage();
			break;
			case 'doupdatePartsPackage':
				$this->doupdatePartsPackage();
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
			case 'find':
				$this->find();
			break;
			case 'doItemOrder':
				$this->doItemOrder();
			break;
			case 'updateItem':
				$this->updateItem();
			break;
			case 'doupdateItem':
				$this->doupdateItem();
			break;
			default:
				$this->mmlist();
		}
	}
	//
	function view(){
		if(!$this->kclass->input['productid'] > 0)$this->kclass->boinkIt('/s.php?module=product');
		$product=$this->kclass->getProduct(array('productid'=>$this->kclass->input['productid']));
		if($product['materialid']>0){
			$material = $this->kclass->DB->queryFirst("
				SELECT materialid,materialno,title,standard
				FROM `material`
				WHERE materialid = ".$product['materialid']."
				");
		}
		if(!$product)	$this->kclass->boinkIt('/s.php?module=product');
		if($product['modified']>0){
			$modified = '，由 '.$product['modifier'].' 在 '.date('Y-m-d H:i:s',$product['modified']).' 被修改';
		}
		if($product['productno']){
			$productno= '('.$product['productno'].')';
		}
		$created = date('Y-m-d H:i:s',$product['created']);
		$attach = $this->kclass->getAttachs(array('module'=>'product','mid'=>$product['productid']));
		if($attach!= false){
			$attachs = '<div class="clear">'.$attach.'</div>';
		}
		// 配件包配件
		if($product['attrid']==5){
			$items=$this->kclass->DB->query("
				SELECT pi.childid,pi.quantity,p.title,m.materialid,m.materialno
				FROM partitem AS pi 
				LEFT JOIN product AS p ON (p.productid=pi.childid)
				LEFT JOIN material AS m ON (m.materialid=p.materialid)
				WHERE pi.killed=0 AND p.killed=0 AND pi.productid='".$product['productid']."'
				ORDER BY pi.ordering ASC
			");
			if($this->kclass->DB->numRows()){
				$i=1;
				$partItemtr='<table class="hundred"><thead><tr><td colspan=5>配件包明细</td></tr></thead><tbody><tr class="center"><td width="15">ID</td><td width="35">对应编号</td><td>配件名称</td><td width="100">数量</td><td width="250">备注</td></tr>';
				while($item=$this->kclass->DB->fetchArray($items)){
					$partItemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').'">
						<td>'.$i.'</td>
						<td>'.$item['materialno'].'</td>
						<td><a href="/s.php?module=product&action=view&productid='.$item['childid'].'">'.$item['title'].'</a></td>
						<td>'.$item['quantity'].'</td>
						<td>'.$item['remark'].'</td>
					</tr>';
					$i++;
				}
				$partItemtr.='</tbody></table>';
			}
		}
		// 产品明细
		$productitems = $this->kclass->DB->query("
			SELECT productitemid,itemno,attributevalue,ordering
			FROM `productitem` 
			WHERE killed=0 AND `productid`='".$product['productid']."'
			ORDER BY ordering DESC
		");
		if($this->kclass->DB->numRows()){
			$i=1;
			$itemtr.='<form method="post" action="/s.php?module=product&action=doItemOrder" name="product"><input type="hidden" name="module" value="product">
<input type="hidden" name="action" value="doItemOrder"><input type="hidden" name="productid" value="'.$product['productid'].'"><table class="hundred"><thead><tr><th colspan="5">产品条目</th></tr></thead><tbody><tr class="even bold center"><td width="15">ID</td><td width="180">产品名称</td><td>产品属性</td><td width="150">其他信息</td><td width="60">排序</td></tr>';
			while($productitem = $this->kclass->DB->fetchArray($productitems)){
				$pitem=$this->kclass->getProduct(array('productid'=>$product['productid'],'itemid'=>$productitem['itemid']));
				$relatedMaterialItem='';
				$materialitems=$this->kclass->DB->query("
					SELECT materialid,itemid
					FROM materialitem
					WHERE killed=0 AND productitemid='".$productitem['itemid']."'
					ORDER BY itemno ASC
				");
				if($this->kclass->DB->numRows()){
					while($materialitem=$this->kclass->DB->fetchArray($materialitems)){
						$mitem=$this->kclass->getMaterial(array('materialid'=>$materialitem['materialid'],'itemid'=>$materialitem['itemid']));
						$relatedMaterialItem.=$mitem['no'].'　'.$mitem['material'].'　'.$mitem['standard'].'</br>';
					}
				}

				$itemtr .= '<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').'">
					<td><input type="hidden" name="itemId['.$i.']" value="'.$productitem['itemid'].'">'.$i.'</td>
					<td>'.$pitem['title'].'</td>
					<td>'.$pitem['standard'].'</td>
					<td><a class="tip" title="'.$relatedMaterialItem.'" href="#">查看相关物资明细</a></td>
					<td><input name="ordering['.$i.']" value="'.$productitem['ordering'].'" size=5 /></td>
				</tr>';
				$i++;
			}
			$itemtr.='<tr class="center"><td colspan=5><input type="submit" value="排序"></td></tr></tbody></table></form>';
		}
		$attriute=$this->kclass->relatedAttribute(array('module'=>'product','mid'=>$product['productid']));
		$sample=$this->kclass->relatedSample(array('productid'=>$product['productid']));
		$inquiry=$this->kclass->relatedInquiry(array('productid'=>$product['productid'],'options'=>1));
		$quoting=$this->kclass->relatedQuoting(array('productid'=>$product['productid'],'options'=>1));
		$order=$this->kclass->relatedOrder(array('productid'=>$product['productid'],'type'=>'PI'));
		$purchase=$this->kclass->relatedOrder(array('productid'=>$product['productid'],'type'=>'PO'));
		$claim=$this->kclass->relatedClaim(array('module'=>'order','productid'=>$product['productid']));
		$return=$this->kclass->relatedReturn(array('module'=>'order','productid'=>$product['productid']));
		$exchange=$this->kclass->relatedExchange(array('productid'=>$product['productid']));
		$customer = $this->kclass->relatedCustomer(array('productid'=>$product['customerid']));
		$supplier = $this->kclass->relatedSupplier(array('productid'=>$product['supplierid']));
		//$productitems = $this->kclass->relatedProductItem(array('productid'=>$product['productid']));
$body = <<<EOF
<div class="title"><span class="right small gray">由 {$product['creator']} 在{$created} 建立{$modified}</span>{$product['title']}<span class="small">{$productno}</span></div>
<dl id="product" class="tabs">
	<dt>资料</dt>
	<dt title="相关的样品单"{$sample['off']}>样品单{$sample['count']}</dt>
	<dt title="询价记录"{$inquiry['off']}>询价单{$inquiry['count']}</dt>
	<dt title="报价记录"{$quoting['off']}>报价单{$quoting['count']}</dt>
	<dt title="采购订单"{$purchase['off']}>采购订单{$purchase['count']}</dt>
	<dt title="销售订单"{$order['off']}>销售订单{$order['count']}</dt>
	<dt title="索赔记录"{$claim['off']}>索赔{$claim['count']}</dt>
	<dt title="退货记录"{$return['off']}>退货{$return['count']}</dt>
	<dt title="换货记录"{$exchange['off']}>换货{$exchange['count']}</dt>
	<dt title="购买过次商品的客户"{$customer['off']}>客户{$customer['count']}</dt>
	<dt title="采购过的供应商"{$supplier['off']}>供应商{$supplier['count']}</dt>
	<dd>
<table cellspacing="10" width="100%">
<tr class="odd">
	<td width="80">中文名称：</td>
	<td class="middle" width="450">{$product['title']}</td>
	<td width="80">英文名称：</td>
	<td class="middle" width="450">{$product['entitle']}</td>
	<td width="80">简　　称：</td>
	<td class="middle">{$product['abbr']}</td>
</tr>
<tr class="even">
	<td>编　　号：</td>
	<td class="middle bold">{$product['productno']}</td>
	<td>产品分类：</td>
	<td class="middle"><a href="/s.php?module=category" target="_blank">{$product['category']}</a></td>
	<td>产品类型：</td>
	<td class="middle">{$product['attr']}</td>
</tr>
<tr class="odd">
	<td>关联物资：</td>
	<td class="small"><a href="/s.php?module=material&action=view&materialid={$material['materialid']}">{$material['materialno']}</a>　{$material['title']}　{$material['standard']}</td>
	<td>品　　牌：</td>
	<td class="middle">{$product['brand']}　　单位：{$product['unit']}</a></td>
	<td>包　　装：</td>
	<td class="middle">{$product['packing']}</a></td>
</tr>
<tr class="even">
	<td valign="top">产品特色：</td>
	<td class="middle">{$product['features']}</td>
	<td valign="top">技术参数：</td>
	<td class="middle">{$product['technical']}</td>
	<td valign="top">详细规格：</td>
	<td class="middle">{$product['specifications']}</td>
</tr>
<tr class="odd">
	<td valign="top">产品说明：</td>
	<td class="middle">{$product['details']}</td>
	<td valign="top">产品描述：</td>
	<td class="middle">{$product['description']}</td>
	<td valign="top">备　　注：</td>
	<td>{$product['remark']}</td>
</tr>
</table>
{$partItemtr}
{$attriute}
{$itemtr}
{$attachs}
	</dd>
	<dd>{$sample['panel']}</dd>
	<dd>{$inquiry['panel']}</dd>
	<dd>{$quoting['panel']}</dd>
	<dd>{$purchase['panel']}</dd>
	<dd>{$order['panel']}</dd>
	<dd>{$claim['panel']}</dd>
	<dd>{$receipt['panel']}</dd>
	<dd>{$exchange['panel']}</dd>
	<dd>{$customer['panel']}</dd>
	<dd>{$supplier['panel']}</dd>
</dl>
EOF;
		$this->kclass->page['onload'] .= "dc.tabs({'id':'product'});dc.tabhover();dc.tips()";
		$this->kclass->page['title'] .= ' - '.$product['title'];
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl.' - '.$product['title'].' 的产品信息','right' => '<span class="gray small">操作：</span>'.$this->kclass->iif($product['killed']==1,'<a href="/s.php?module=product&action=restore&productid='.$product['productid'].'&rt=view">恢复</a>','<a href="/s.php?module=product&action=remove&productid='.$product['productid'].'&rt=view">删除</a>　<a href="/s.php?module=product&action=update&productid='.$product['productid'].'&rt=view">修改</a>　|　<span class="gray small">新建：</span><a href="/s.php?module=product&action=add">产品</a>　<a href="/s.php?module=attribute&action=add&productid='.$product['productid'].'">特有属性</a>　<a href="/s.php?module=product&action=updateItem&productid='.$product['productid'].'">产品明细</a>　<a href="/s.php?module=product&action=addInquiry&productid='.$product['productid'].'" target="_blank">询价</a>　<a href="/s.php?module=product&action=addQuoting&productid='.$product['productid'].'" target="_blank">报价</a> '),'body'=>$body));
	}

	function find(){
		$category = $this->kclass->chooserCategory(array('parentid'=>1,'name'=>'categoryid','hasBlank'=>1,'width'=>450,'selectedid'=>$this->kclass->input['categoryid']));
		$attr = $this->kclass->chooserAttr(array('name'=>'attrid','selectedid'=>1));
		$body = <<<EOF
<form action="/s.php?module=product&action=list" name="order" method="post">
<input type="hidden" name="module" value="product">
<input type="hidden" name="action" value="list">
<table>
<thead>
<tr>
	<th colspan="2">查找产品</th>
</tr>
</thead>
<tbody>
<tr class="even">
	<td>产品名称 包含：</td>
	<td><input type="text" style="width:150px" name="productTitle" value=""></td>
</tr>
<tr class="even">
	<td>产品简称 包含：</td>
	<td><input type="text" style="width:150px" name="productAbbr" value=""></td>
</tr>
<tr class="odd">
	<td>且 产品类型 是：</td>
	<td>{$category}</td>
</tr>
<tr class="even">
	<td>且 产品所属 是：</td>
	<td>{$attr}</td>
</tr>
<tr class="odd">
	<td>且 修改日期 ：</td>
	<td>
		开始：
		<input type="text" id="startDate" name="startModDate" size="12" value="{$startDate}">
		结束：
		<input type="text" id="endDate" name="endModDate" size="12" value="{$endDate}">
	</td>
</tr>
<tr class="even">
	<td>且 创建时间：</td>
	<td>
		开始：
		<input type="text" id="startCreateDate" name="startCreateDate" size="12" value="{$startDate}">
		结束：
		<input type="text" id="endCreateDate" name="endCreateDate" size="12" value="{$endDate}">
	</td>
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
		<label for="o0"><input type="radio" name="orderby" value="productno" id="o0" checked>产品编号</label>
		<label for="o1"><input type="radio" name="orderby" value="productid" id="o1">ID</label>
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
		<label for="s0"><input type="radio" id="s0" name="show" value="" checked>默认列表</label>
		<label for="s1"><input type="radio" id="s1" name="show" value="all">　<span class="small gray">(包括删除)</span></label>
	</td>
</tr>
<!--
<tr class="odd">
	<td>列表方式：</td>
	<td disabled>
		<label for="t0"><input type="radio" id="t0" name="layout" value="grid" checked>格子</label>
		<label for="t1"><input type="radio" id="t1" name="layout" value="list">列表</label>
	</td>
</tr>
-->
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
		$this->kclass->page['onload'] .= 'dc.tabhover();$(\'#shipmentDate\').datepicker();var dates=$(\'#startDate,#endDate\').datepicker({onSelect:function(selectedDate){var option=this.id==\'startDate\'?\'minDate\':\'maxDate\',instance=$(this).data(\'datepicker\'),date=$.datepicker.parseDate(instance.settings.dateFormat || $.datepicker._defaults.dateFormat,selectedDate,instance.settings);dates.not(this).datepicker(\'option\',option,date);}});var dates2=$(\'#startCreateDate,#endCreateDate\').datepicker({onSelect:function(selectedDate){var option2=this.id==\'startCreateDate\'?\'minDate\':\'maxDate\',instance2=$(this).data(\'datepicker\'),date2=$.datepicker.parseDate(instance2.settings.dateFormat || $.datepicker._defaults.dateFormat,selectedDate,instance2.settings);dates2.not(this).datepicker(\'option\',option2,date2);}});';
		$this->kclass->page['title'] .= ' - 查找产品';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 查找样品单','right' => '<a href="/s.php?module=sample">返回列表</a>','body'=>$body));

	}
	//
	function insert(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			if($this->kclass->input['title'] == ''){
				$e = '<li>请填写产品的 中文名称。</li>';
			}else{
				if($this->kclass->DB->queryFirst("SELECT productid FROM `product` WHERE `title`='".$this->kclass->input['title']."'")){
					$e = '<li>您要新建的产品 【'.$this->kclass->input['title'].'】 已经存在。</li>';
				}
			}
			if($this->kclass->input['entitle'] == ''){
				$e .= '<li>请填写产品的 英文名称。</li>';
			}
			if(strlen($this->kclass->input['abbr']) > 10 OR strlen($this->kclass->input['abbr']) == 0){
				$e .= '<li>请填写产品的 产品简称缩写（不能超过10个英文字符）。</li>';
			}
			if($this->kclass->input['categoryid']<=0){
				$e .= '<li>请填写产品的 分类。</li>';
			}
			if(!$this->kclass->input['unitid'] > 0){
				$e .= '<li>请选择产品的 数量单位。</li>';
			}
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '新建产品',
				'text' => '您在新建产品的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$materialid=$this->kclass->iif($this->kclass->input['materialid']>0,$this->kclass->input['materialid'],0);
		$this->kclass->DB->query("
			INSERT INTO `product` (`title`,`entitle`,`abbr`,`productno`,`materialid`,`categoryid`,`attrid`,`brandid`,`unitid`,`packingid`,`features`,`technical`,`specifications`,`details`,`description`,`display`,`remark`,`ordering`,`creator`,`created`)
			VALUES ('".$this->kclass->input['title']."','".$this->kclass->input['entitle']."','".$this->kclass->input['abbr']."','".$this->kclass->input['productno']."','".$materialid."','".$this->kclass->input['categoryid']."','".$this->kclass->input['attrid']."','".$this->kclass->input['brandid']."','".$this->kclass->input['unitid']."','".$this->kclass->input['packingid']."','".$this->kclass->input['features']."','".$this->kclass->input['technical']."','".$this->kclass->input['specifications']."','".$this->kclass->input['details']."','".$this->kclass->input['description']."','".$this->kclass->input['display']."','".$this->kclass->input['remark']."','".$this->kclass->input['ordering']."','".$this->kclass->user['userid']."','".TIMENOW."')
		");
		$productid = $this->kclass->DB->insertID();
		$this->kclass->updateAttachs(array('module'=>'product','mid'=>$productid));

		$this->kclass->messager(array(
			'title' => '新建产品',
			'text' => '产品 <b>'.$this->kclass->input['title'].'</b> 已新建成功!',
			'url' => '/s.php?module=product&action=view&productid='.$productid,
			'sec' => 3
		));
	}

	//
	function add(){
		$attr=$this->kclass->chooserAttr(array('name'=>'attrid','module'=>'product','selectedid'=>1));
		$category=$this->kclass->chooserCategory(array('parentid'=>1,'name'=>'categoryid','hasBlank'=>1,'width'=>450,'selectedid'=>$this->kclass->input['categoryid']));
		$brand=$this->kclass->chooserBrand(array('name'=>'brandid','hasBlank'=>1,'width'=>100,'selectedid'=>1));
		$packing=$this->kclass->chooserPacking(array('name'=>'packingid','hasBlank'=>1,'width'=>200,'selectedid'=>6));
		$unit=$this->kclass->chooserUnit(array('name'=>'unitid','hasBlank'=>1,'width'=>160,'selectedid'=>5));
		$upload=$this->kclass->upload(array('title'=>'相关附件'));

$body = <<<EOF
<form action="/s.php?module=product&action=insert" name="product" method="post">
<input type="hidden" name="module" value="product">
<input type="hidden" name="action" value="insert">
<table class="hundred">
<thead>
<tr>
	<th colspan="7">产品资料</th>
</tr>
</thead>
<tbody>
<tr class="odd">
	<td width="100">中文名称：<span class="red bold">*</span></td>
	<td width="725"><input type="text" style="width:450px" name="title" value=""></td>
	<td width="100">英文名称：<span class="red bold">*</span></td>
	<td><input type="text" style="width:450px" name="entitle" value=""></td>
</tr>
<tr class="even">
	<td>产品编号：</td>
	<td><input type="text" style="width:250px" name="productno" value="">　　是否在网站上显示：<input type="radio" name="display" value=1 checked />是 <input type="radio" name="display" value=0 >否</td>
	<td>产品类型：<span class="red bold">*</span></td>
	<td>{$attr}</td>
</tr>
<tr class="even">
	<td>单　　位：<span class="red bold">*</span></td>
	<td>{$unit}　　缩写：<span class="red bold">*</span><input type="text" style="width:130px" name="abbr" value=""> <span class="gray small">不超过30个字：</span></td>
	<td>分　　类：<span class="red bold">*</span></td>
	<td>{$category}</td>
</tr>
<tr class="even">
	<td>品　　牌：<span class="red bold">*</span></td>
	<td>{$brand}</td>
	<td>包　　装：<span class="red bold">*</span></td>
	<td>{$packing}</td>
</tr>
<tr class="odd">
	<td valign="top">产品特色：</td>
	<td><textarea name="features" style="width:450px;height:150px"></textarea></td>
	<td valign="top">技术参数：</td>
	<td><textarea name="technical" style="width:450px;height:150px"></textarea></td>
</tr>
<tr class="even">
	<td valign="top">详细规格：</td>
	<td><textarea name="specifications" style="width:450px;height:150px"></textarea></td>
	<td valign="top">产品说明：</td>
	<td><textarea name="details" style="width:450px;height:150px"></textarea></td>
</tr>
<tr class="odd">
	<td valign="top">产品描述：</td>
	<td><textarea name="description" style="width:450px;height:150px"></textarea></td>
	<td valign="top">备　　注：</td>
	<td><textarea name="remark" style="width:450px;height:150px"></textarea></td>
</tr>
<tr class="even">
	<td valign="top">产品排序：</td>
	<td colspan="3"><input type="text" name="ordering" value="1"></td>
</tr>
</tbody>
</table>
{$upload}
<table class="tlist tsuite" align="center" width="100%">
<tbody>
<tr class="odd">
	<td colspan="4" align="center">
		<input type="submit" id="submitButton" value="  保存  " accesskey="s">
		<input type="reset" value="  复位  ">
	</td>
</tr>
<tr class="even">
	<td class="small gray">
	注意事项：
	英文地址：请严格按英文的格式进行(会影响订单中打印的地址)，基本与中文地址相反的顺序。
	未有列项的内容，写入“备注”中，长度上限3万中文字。
	</td>
</tr>
</tbody>
</table>
</form>
EOF;
		$this->kclass->tbline+=4;
		$this->kclass->page['title'] .= ' - 新建产品';
		$this->kclass->page['onload'] .= 'dc.tabhover()';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 新建产品','right' => '<a href="/s.php?module=product">返回列表</a>','body'=>$body));
	}

	//
	function doupdate(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员：<</li>';
		}else{
			if($this->kclass->input['title'] == ''){
				$e = '<li>请填写产品的 中文名称：</li>';
				}
			if($this->kclass->input['entitle'] == ''){
				$e .= '<li>请填写产品的 英文名称。</li>';
			}
			if(strlen($this->kclass->input['abbr']) > 10 OR strlen($this->kclass->input['abbr']) == 0){
				$e .= '<li>请填写产品的 产品缩写（不能超过0个英文字符）。</li>';
			}
			if($this->kclass->input['categoryid']<=0){
				$e .= '<li>请填写产品的 分类。</li>';
			}
			if(!$this->kclass->input['unitid'] > 0){
				$e .= '<li>请：<择产品的 数量单位。</li>';
			}
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '修改产品',
				'text' => '您在修改产品的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		
		$this->kclass->DB->query("
			UPDATE `product` SET 
				`title` = '".$this->kclass->input['title']."',
				`entitle` = '".$this->kclass->input['entitle']."',
				`abbr` = '".$this->kclass->input['abbr']."',
				`productno` = '".$this->kclass->input['productno']."',
				`categoryid` = '".$this->kclass->input['categoryid']."',
				`attrid`='".$this->kclass->input['attrid']."',
				`brandid`='".$this->kclass->input['brandid']."',
				`unitid`='".$this->kclass->input['unitid']."',
				`packingid`='".$this->kclass->input['packingid']."',
				`features` = '".$this->kclass->input['features']."',
				`technical` = '".$this->kclass->input['technical']."',
				`specifications` = '".$this->kclass->input['specifications']."',
				`details` = '".$this->kclass->input['details']."',
				`description` = '".$this->kclass->input['description']."',
				`display` = '".$this->kclass->input['display']."',
				`remark` = '".$this->kclass->input['remark']."',
				`ordering` = '".$this->kclass->input['ordering']."',
				`modified` = '".TIMENOW."',
				`modifier` = '".$this->kclass->user['userid']."'
			WHERE productid='".$this->kclass->input['productid']."'
		");
		$this->kclass->updateAttachs(array('module'=>'product','mid'=>$this->kclass->input['productid']));
		if($this->kclass->input['rt']=='view'){
			$rt = '&action=view&productid='.$this->kclass->input['productid'];
		}elseif($this->kclass->input['rt']=='list'){
			$rt = '#'.$this->kclass->input['productid'];
		}
		$this->kclass->messager(array(
			'title' => '修改产品',
			'text' => '产品 <b>'.$this->kclass->input['title'].'</b> 的信息已成功修改!',
			'url' => '/s.php?module=product'.$rt,
			'sec' => 3
		));
	}

	//
	function update(){
		if($this->kclass->input['productid']<=0 OR !$product = $this->kclass->DB->queryFirst("SELECT `product`.* FROM `product` WHERE `productid`='".$this->kclass->input['productid']."'")){
			$this->kclass->boinkIt('/s.php?module=product');
		}

		$this->kclass->br2nl=true;
		$attr = $this->kclass->chooserAttr(array('name'=>'attrid','module'=>'product','selectedid'=>$product['attrid']));
		$category = $this->kclass->chooserCategory(array('parentid'=>1,'name'=>'categoryid','hasBlank'=>1,'width'=>450,'selectedid'=>$product['categoryid']));
		$brand = $this->kclass->chooserBrand(array('name'=>'brandid','hasBlank'=>1,'width'=>100,'selectedid'=>$product['brandid']));
		$packing = $this->kclass->chooserPacking(array('name'=>'packingid','hasBlank'=>1,'width'=>200,'selectedid'=>$product['packingid']));
		$unit = $this->kclass->chooserUnit(array('name'=>'unitid','hasBlank'=>1,'width'=>160,'selectedid'=>$product['unitid']));
		$upload = $this->kclass->upload(array('module'=>'product','mid'=>$product['productid'],'title'=>'相关附件：'));
		if($product['display']==1){
			$checked1=' checked';
			$checked2='';
		}elseif($product['display']==0){
			$checked1='';
			$checked2=' checked';
		}
		if($product['attrid']==3){
			$parentProduct = $this->kclass->chooserParentProduct(array('parentProduct'=>$product['parentProduct']));
			$materialdata = $this->kclass->DB->queryFirst("SELECT `material`.* FROM `material` WHERE `materialid`='".$product['materialid']."'");
			$material=$this->kclass->chooserMaterial(array('hName'=>'materialid','hId'=>'materialid','name'=>'materialTitle','id'=>'materialTitle','width'=>450,'hasBlank'=>1,'topname'=>'','selectedid'=>$product['materialid'],'value'=>$this->kclass->iif($materialdata,$materialdata['materialno'].'　'.$materialdata['title'].'　'.$materialdata['standard'],'')));
$body = <<<EOF
<form action="/s.php?module=product&action=doupdateParts" name="product" method="post">
<input type="hidden" name="module" value="product">
<input type="hidden" name="action" value="doupdateParts">
<input type="hidden" name="productid" value="{$this->kclass->input['productid']}">
<table class="hundred">
<thead>
<tr>
	<th colspan="7">配件资料：</th>
</tr>
</thead>
<tbody>
<tr class="odd">
	<td width="155">中文名称：<span class="red bold">*</span></td>
	<td width="625"><input type="text" style="width:450px" name="title" value="{$product['title']}"></td>
	<td width="155">英文名称：<span class="red bold">*</span></td>
	<td><input type="text" style="width:450px" name="entitle" value="{$product['entitle']}"></td>
</tr>
<tr class="even">
	<td>关联物资：</td>
	<td>{$material}</td>
	<td>产品类型：<span class="red bold">*</span></td>
	<td>{$attr}　　名称缩写：<input type="text" style="width:130px" name="abbr" value="{$product['abbr']}"><span class="gray small">不超10个字符</span></td>
</tr>
<tr class="odd">
	<td valign="top">产品分类：</td>
	<td>{$category}</td>
	<td valign="top">产品品牌：</td>
	<td>{$brand}</td>
</tr>
<tr class="even">
	<td>包　　装：<span class="red bold">*</span></td>
	<td>{$packing}</td>
	<td>单　　位：<span class="red bold">*</span></td>
	<td>{$unit}　　是否在网站上显示：<input type="radio" name="display" value=1 {$checked1} />是 <input type="radio" name="display" value=0 {$checked2} />否</td>
</tr>
<tr class="even">
	<td valign="top">所属成品：</td>
	<td colspan="3">{$parentProduct}</td>
</tr>
<tr class="odd">
	<td valign="top">配件特色：</td>
	<td><textarea name="features" style="width:450px;height:150px">{$product['features']}</textarea></td>
	<td valign="top">技术参数：</td>
	<td><textarea name="technical" style="width:450px;height:150px">{$product['technical']}</textarea></td>
</tr>
<tr class="even">
	<td valign="top">详细规格：</td>
	<td><textarea name="specifications" style="width:450px;height:150px">{$product['specifications']}</textarea></td>
	<td valign="top">配件说明：</td>
	<td><textarea name="details" style="width:450px;height:150px">{$product['details']}</textarea></td>
</tr>
<tr class="odd">
	<td valign="top">配件描述：</td>
	<td><textarea name="description" style="width:450px;height:150px">{$product['description']}</textarea></td>
	<td valign="top">备　　注：</td>
	<td><textarea name="remark" style="width:450px;height:150px">{$product['remark']}</textarea></td>
</tr>
<tr class="even">
	<td valign="top">产品排序：</td>
	<td colspan="3"><input type="text" name="ordering" value="{$product['ordering']}"></td>
</tr>
</tbody>
</table>
{$upload}
<table class="tlist tsuite" align="center" width="100%">
<tbody>
<tr class="odd">
	<td colspan="4" align="center">
		<input type="submit" id="submitButton" value="  保存  " accesskey="s">
		<input type="reset" value="  复位  ">
	</td>
</tr>
<tr class="even">
	<td class="small gray">
	注意事项：
	英文地址：请严格按英文的格式进行(会影响订单中打印的地址)，基本与中文地址相反的顺序。
	未有列项的内容，写入“备注”中，长度上限3万中文字。
	</td>
</tr>
</tbody>
</table>
</form>
EOF;
		}elseif($product['attrid']==5){
			$parentProduct = $this->kclass->chooserParentProduct(array('parentProduct'=>$product['parentProduct']));
			$materialdata = $this->kclass->DB->queryFirst("SELECT `material`.* FROM `material` WHERE `materialid`='".$product['materialid']."'");
			$material=$this->kclass->chooserMaterial(array('hName'=>'materialid','hId'=>'materialid','name'=>'materialTitle','id'=>'materialTitle','width'=>450,'hasBlank'=>1,'topname'=>'','selectedid'=>$product['materialid'],'value'=>$this->kclass->iif($materialdata,$materialdata['materialno'].'　'.$materialdata['title'].'　'.$materialdata['standard'],'')));
			$childProduct=explode(',',$product['childProduct']);
			$i=1;
			$items=$this->kclass->DB->query("
				SELECT pp.itemid,pp.quantity,pp.childid,pp.remark,
					p.title
				FROM partitem AS pp
				LEFT JOIN product AS p ON (pp.childid=p.productid)
				WHERE pp.killed=0 AND p.killed=0 AND pp.productid='".$product['productid']."'
				ORDER BY pp.ordering ASC
			");
			if($this->kclass->DB->numRows()){
				$i=1;
				while($item=$this->kclass->DB->fetchArray($items)){
					$itemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').'">
						<td><input type="hidden" name="itemId['.$i.']" value="'.$item['itemid'].'">'.$i.'</td>
						<td><a href="/s.php?module=product&action=view&productid='.$item['childid'].'">'.$item['title'].'</a></td>
						<td><input type="text" name="orgQuantity['.$i.']" value="'.$item['quantity'].'" ></td>
						<td><input type="text" name="orgRemark['.$i.']" value="'.$item['remark'].'" style="width:250px" ></td>
						<td><input type="checkbox" name="orgKill['.$i.']" value="'.$item['itemid'].'"></td>
					</tr>';
					$i++;
				}
			}

$body = <<<EOF
<form action="/s.php?module=product&action=doupdatePartsPackage" name="product" method="post">
<input type="hidden" name="module" value="product">
<input type="hidden" name="action" value="doupdatePartsPackage">
<input type="hidden" name="productid" value="{$this->kclass->input['productid']}">
<table class="hundred">
<thead>
<tr>
	<th colspan="7">配件资料：</th>
</tr>
</thead>
<tbody>
<tr class="odd">
	<td width="100">中文名称：<span class="red bold">*</span></td>
	<td width="750"><input type="text" style="width:450px" name="title" value="{$product['title']}"></td>
	<td width="100">英文名称：<span class="red bold">*</span></td>
	<td><input type="text" style="width:450px" name="entitle" value="{$product['entitle']}"></td>
</tr>
<td>产品类型：<span class="red bold">*</span></td>
	<td>{$attr}</td>
	<td>名称缩写：</td>
	<td><input type="text" style="width:130px" name="abbr" value="{$product['abbr']}"><span class="gray small">不超10个字符 </span>　产品排序：<input type="text" name="ordering" value="{$product['ordering']}" size=5></td>
<tr class="odd">
	<td valign="top">产品分类：</td>
	<td>{$category}</td>
	<td valign="top">产品品牌：</td>
	<td>{$brand}　　是否在网站上显示：<input type="radio" name="display" value=1 {$checked1} />是　<input type="radio" name="display" value=0 {$checked2} />否</td>
</tr>
<tr class="even">
	<td valign="top">所属成品：</td>
	<td colspan="3">{$parentProduct}</td>
</tr>
<tr class="odd">
	<td valign="top">配件特色：</td>
	<td><textarea name="features" style="width:450px;height:150px">{$product['features']}</textarea></td>
	<td valign="top">技术参数：</td>
	<td><textarea name="technical" style="width:450px;height:150px">{$product['technical']}</textarea></td>
</tr>
<tr class="even">
	<td valign="top">详细规格：</td>
	<td><textarea name="specifications" style="width:450px;height:150px">{$product['specifications']}</textarea></td>
	<td valign="top">配件说明：</td>
	<td><textarea name="details" style="width:450px;height:150px">{$product['details']}</textarea></td>
</tr>
<tr class="odd">
	<td valign="top">配件描述：</td>
	<td><textarea name="description" style="width:450px;height:150px">{$product['description']}</textarea></td>
	<td valign="top">备　　注：</td>
	<td><textarea name="remark" style="width:450px;height:150px">{$product['remark']}</textarea></td>
</tr>
</tbody>
</table>
<table class="hundred"><thead><tr><th colspan=5>配件明细</th></tr></thead>
<tbody>
<tr class="center"><td width="15">ID</td><td>配件名称</td><td width="30">数量</td><td width="250">备注</td><td width="15">：</td></tr>
{$itemtr}
<tr id='tr'><td colspan=5>选择要加入配件包的配：</td></tr>
<tr class="red"><td colspan=5><span onclick="dc.product.showParts('tr')" class="hand" style="width:200px;height:20px;border:1px solid red">+选择要添加的配件</span></td></tr>
</tbody>
</table>
{$upload}
<table class="tlist tsuite" align="center" width="100%">
<tbody>
<tr class="odd">
	<td colspan="4" align="center">
		<input type="submit" id="submitButton" value="  保存  " accesskey="s">
		<input type="reset" value="  复位  ">
	</td>
</tr>
<tr class="even">
	<td class="small gray">
	注意事项：<
	英文地址：请严格按英文的格式进行(会影响订单中打印的地址，基本与中文地址相反的顺序；
	未有列项的内容，写入“备注”中，长度上限3万中文字。
	</td>
</tr>
</tbody>
</table>
</form>
<div id="partsPackage" title="选择配件"></div>
EOF;
		}else{
			if($product['materialid']>0){
				$material = $this->kclass->DB->queryFirst("SELECT * FROM `material`	WHERE materialid = ".$product['materialid']."");
				$material['no']=$material['materialno'];
			}

			$product['features']=$this->kclass->parseConvertValue($product['features']);
			$product['technical']=$this->kclass->parseConvertValue($product['technical']);
			$product['specifications']=$this->kclass->parseConvertValue($product['specifications']);
			$product['details']=$this->kclass->parseConvertValue($product['details']);
			$product['description']=$this->kclass->parseConvertValue($product['description']);
			$product['remark']=$this->kclass->parseConvertValue($product['remark']);

$body = <<<EOF
<form action="/s.php?module=product&action=doupdate" name="product" method="post">
<input type="hidden" name="module" value="product">
<input type="hidden" name="action" value="doupdate">
<input type="hidden" name="productid" value="{$this->kclass->input['productid']}">
<input type="hidden" name="rt" value="{$this->kclass->input['rt']}">
<table class="hundred">
<thead>
<tr>
	<th colspan="4">修改产品：{$product['title']}</th>
</tr>
</thead>
<tbody>
<tr class="odd">
	<td>中文名称：<span class="red bold">*</span></td>
	<td><input type="text" style="width:450px" name="title" value="{$product['title']}"></td>
	<td>英文名称：<span class="red bold">*</span></td>
	<td><input type="text" style="width:450px" name="entitle" value="{$product['entitle']}"></td>
</tr>
<tr class="even">
	<td>产品编号：</td>
	<td><input type="text" style="width:250px" name="productno" value="{$product['productno']}">　是否在网站上显示：<input type="radio" name="display" value=1 {$checked1} />是 <input type="radio" name="display" value=0 {$checked2} />否</td>
	<td>产品类型：<span class="red bold">*</span></td>
	<td>{$attr}</td>
</tr>
<tr class="even">
	<td>单　　位：<span class="red bold">*</span></td>
	<td>{$unit}　　缩写：<span class="red bold">*</span><input type="text" style="width:130px" name="abbr" value="{$product['abbr']}"> <span class="gray small">不超过30个字：</span></td>
	<td>分　　类：<span class="red bold">*</span></td>
	<td>{$category}</td>
</tr>
<tr class="even">
	<td>品　　牌：<span class="red bold">*</span></td>
	<td>{$brand}</td>
	<td>包　　装：<span class="red bold">*</span></td>
	<td>{$packing}</td>
</tr>
<tr class="even">
	<td valign="top">产品特色：</td>
	<td><textarea name="features" style="width:450px;height:150px">{$product['features']}</textarea></td>
	<td valign="top">技术参数：</td>
	<td><textarea name="technical" style="width:450px;height:150px">{$product['technical']}</textarea></td>
</tr>
<tr class="odd">
	<td valign="top">详细规格：</td>
	<td><textarea name="specifications" style="width:450px;height:150px">{$product['specifications']}</textarea></td>
	<td valign="top">产品说明：</td>
	<td><textarea name="details" style="width:450px;height:150px">{$product['details']}</textarea></td>
</tr>
<tr class="even">
	<td valign="top">产品描述：</td>
	<td><textarea name="description" style="width:450px;height:150px">{$product['description']}</textarea></td>
	<td valign="top">备　　注：</td>
	<td><textarea name="remark" style="width:450px;height:150px">{$product['remark']}</textarea></td>
</tr>
<tr class="even">
	<td valign="top">产品排序：</td>
	<td colspan="3"><input type="text" name="ordering" value="{$product['ordering']}"></td>
</tr>
</tbody>
</table>
{$upload}
<table class="tlist tsuite" align="center" width="100%">
<tbody>
<tr class="odd">
	<td colspan="4" align="center">
		<input type="submit" id="submitButton" value="  保存  " accesskey="s">
		<input type="reset" value="  复位  ">
	</td>
</tr>
<tr class="even">
	<td class="small gray">
	注意事项：
	英文地址：请严格按英文的格式进行(会影响订单中打印的地址)，基本与中文地址相反的顺序。
	未有列项的内容，写入“备注”中，长度上限3万中文字。
	</td>
</tr>
</tbody>
</table>
</form>
EOF;
		}
		$this->kclass->tbline+=2;
		$this->kclass->page['title'] .= ' - 修改 - '.$product['title'];
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 修改产品 - '.$product['title'],'right' => '<a href="/s.php?module=product">返回列表</a>','body'=>$body));
	}
	//
	function kill(){
		if($this->kclass->input['productid']<=0){
			$this->kclass->boinkIt('/s.php?module=product');
		}
		if($this->kclass->input['confirm'] == 0){
			$this->kclass->boinkIt('/s.php?module=product&action=view&productid='.$this->kclass->input['productid']);
		}
		if($this->kclass->input['productid'] < 0){
			$e = '<li>请从正确的页面进行操作，如果此问题持续存在，请联系网站管理员：</li>';
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '删除产品',
				'text' => '您在删除产品的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		if($this->kclass->input['rt']=='view'){
			$rt = '&action=view&productid='.$this->kclass->input['productid'];
		}elseif($this->kclass->input['rt']=='list'){
			$rt = '#'.$this->kclass->input['productid'];
		}
		$product = $this->kclass->DB->queryFirst("
			SELECT title
			FROM product
			WHERE productid='".$this->kclass->input['productid']."'
		");
		if($product){
			$this->kclass->DB->query("
				UPDATE `product`
				SET killed=".TIMENOW."
				WHERE productid='".$this->kclass->input['productid']."'
			");
			$this->kclass->messager(array(
				'title' => '删除产品成功',
				'text' => '产品 <b>'.$product['title'].'</b> 已成功被标记为删除!',
				'url' => '/s.php?module=product'.$rt,
				'sec' => 2
			));
		}else{
			$this->kclass->messager(array(
				'title' => '删除产品失败',
				'text' => '您要删除的产品，不存在！',
				'url' => '/s.php?module=product'.$rt,
				'sec' => 3
			));
		}
	}

	//
	function remove(){
		if($this->kclass->input['productid']<=0){
			$this->kclass->boinkIt('/s.php?module=product');
		}
		$product = $this->kclass->DB->queryFirst("
			SELECT title
			FROM product
			WHERE productid='".$this->kclass->input['productid']."'
		");
$body = <<<EOF
<form action="/s.php?module=product&action=kill" name="product" method="post">
<input type="hidden" name="module" value="product">
<input type="hidden" name="action" value="kill">
<input type="hidden" name="productid" value="{$this->kclass->input['productid']}">
<table><thead>
<thead>
<tr>
	<th>删除产品：{$product['title']}</th>
</tr>
</thead>
<tbody>
<tr class="even">
	<td class="middle">你确定要删除产品: <a href="/s.php?module=product&action=view&productid={$this->kclass->input['productid']}" class="big bold" target="_blank">{$product['title']}</a> 吗?</td>
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
		$this->kclass->page['title'] .= ' - 删除 - '.$product['title'];
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 删除产品 - '.$product['title'],'right' => '<a href="/s.php?module=product">返回列表</a>','body'=>$body));
	}

	//
	function revival(){
		if($this->kclass->input['productid']<=0){
			$this->kclass->boinkIt('/s.php?module=product');
		}
		if($this->kclass->input['confirm'] == 0){
			$this->kclass->boinkIt('/s.php?module=product&action=view&productid='.$this->kclass->input['productid']);
		}
		if($this->kclass->input['productid'] < 0){
			$e = '<li>请从正确的页面进行操作，如果此问题持续存在，请联系网站管理员。</li>';
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '恢复产品',
				'text' => '您在恢复产品的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$product = $this->kclass->DB->queryFirst("
			SELECT title
			FROM product
			WHERE productid='".$this->kclass->input['productid']."'
		");
		if($this->kclass->input['rt']=='view'){
			$rt = '&action=view&productid='.$this->kclass->input['productid'];
		}elseif($this->kclass->input['rt']=='list'){
			$rt = '#'.$this->kclass->input['productid'];
		}
		if($product){
			$this->kclass->DB->query("
				UPDATE `product`
				SET killed=0
				WHERE productid='".$this->kclass->input['productid']."'
			");
			$this->kclass->messager(array(
				'title' => '恢复产品成功',
				'text' => '产品 <b>'.$product['title'].'</b> 已成功被恢复！',
				'url' => '/s.php?module=product'.$rt,
				'sec' => 2
			));
		}else{
			$this->kclass->messager(array(
				'title' => '恢复产品失败',
				'text' => '您要恢复的产品不存在！',
				'url' => '/s.php?module=product'.$rt,
				'sec' => 3
			));
		}
	}

	//
	function restore(){
		if($this->kclass->input['productid']<=0){
			$this->kclass->boinkIt('/s.php?module=product');
		}
		$product = $this->kclass->DB->queryFirst("
			SELECT title
			FROM product
			WHERE productid='".$this->kclass->input['productid']."'
		");
$body = <<<EOF
<form action="/s.php?module=product&action=revival" name="product" method="post">
<input type="hidden" name="module" value="product">
<input type="hidden" name="action" value="revival">
<input type="hidden" name="productid" value="{$this->kclass->input['productid']}">
<table><thead>
<thead>
<tr>
	<th>恢复产品：{$product['title']}</th>
</tr>
</thead>
<tbody>
<tr class="even">
	<td class="middle">你确定要恢复产品: <a href="/s.php?module=product&action=view&productid={$this->kclass->input['productid']}" class="big bold" target="_blank">{$product['title']}</a> 吗?</td>
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
		$this->kclass->page['title'] .= ' - 恢复 - '.$product['title'];
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 恢复产品 - '.$product['title'],'right' => '<a href="/s.php?module=product">返回列表</a>','body'=>$body));
	}


	//产品列表
	function mmlist(){
		$flag = false;
		//产品名称
		$this->kclass->input['productTitle']=trim($this->kclass->input['productTitle']);
		if($this->kclass->input['productTitle']!=''){
			if($flag){
				$condition.=" AND";
			}
			$condition.=" `product`.title LIKE '%".$this->kclass->input['productTitle']."%'";
			$flag = true;
		}
		//产品简称
		$this->kclass->input['productAbbr']=trim($this->kclass->input['productAbbr']);
		if(strlen($this->kclass->input['productAbbr'])>0){
			if($flag){
				$condition.=" AND";
			}
			$condition.=" `product`.abbr LIKE '%".$this->kclass->input['productAbbr']."%' ";
			$flag = true;
		}

		//样品类型
		if($this->kclass->input['categoryid']>0){
			if($flag){
				$condition.=" AND";
			}
			$condition.=" `product`.categoryid='".$this->kclass->input['categoryid']."'";
			$flag = true;
		}

		//样品所属
		if($this->kclass->input['attrid']>0){
			if($flag){
				$condition.=" AND";
			}
			$condition.=" `product`.attrid = '".$this->kclass->input['attrid']."'";
			$flag = true;
		}

		//修改日期
		if($this->kclass->input['startModDate']!=''){
			$year = substr($this->kclass->input['startModDate'],0,4);
			$month = substr($this->kclass->input['startModDate'],5,2);
			$day = substr($this->kclass->input['startModDate'],8,2);
			if (checkdate($month,$day,$year)) {
				$this->kclass->input['startModDate'] = mktime(0,0,0,$month,$day,$year);
			}
			if($this->kclass->input['startModDate']>0){
				if($flag){
					$condition.=" AND";
				}
				$condition .= " `product`.modified >='".$this->kclass->input['startModDate']."'";
				$flag = true;
			}
		}

		if($this->kclass->input['endModDate']!=''){
			$year = substr($this->kclass->input['endModDate'],0,4);
			$month = substr($this->kclass->input['endModDate'],5,2);
			$day = substr($this->kclass->input['endModDate'],8,2);
			if (checkdate($month,$day,$year)) {
				$this->kclass->input['endModDate'] = mktime(0,0,0,$month,$day,$year);
			}
			if($this->kclass->input['endModDate']>0){
				if($flag){
					$condition.=" AND";
				}
				$condition .= " `product`.modified <='".$this->kclass->input['endModDate']."'";
				$flag = true;
			}
		}

		//创建时期
		if($this->kclass->input['startCreateDate']!=''){
			$year = substr($this->kclass->input['startCreateDate'],0,4);
			$month = substr($this->kclass->input['startCreateDate'],5,2);
			$day = substr($this->kclass->input['startCreateDate'],8,2);
			if (checkdate($month,$day,$year)) {
				$this->kclass->input['startCreateDate'] = mktime(0,0,0,$month,$day,$year);
			}
			if($this->kclass->input['startCreateDate']>0){
				if($flag){
					$condition.=" AND";
				}
				$condition .= " `product`.created >='".$this->kclass->input['startCreateDate']."'";
				$flag = true;
			}
		}

		if($this->kclass->input['endCreateDate']!=''){
			$year = substr($this->kclass->input['endCreateDate'],0,4);
			$month = substr($this->kclass->input['endCreateDate'],5,2);
			$day = substr($this->kclass->input['endCreateDate'],8,2);
			if (checkdate($month,$day,$year)) {
				$this->kclass->input['endCreateDate'] = mktime(0,0,0,$month,$day,$year);
			}
			if($this->kclass->input['endCreateDate']>0){
				if($flag){
					$condition.=" AND";
				}
				$condition .= " `product`.created <='".$this->kclass->input['endCreateDate']."'";
				$flag = true;
			}
		}

		//排序方式
		if($this->kclass->input['orderby']!=''){
			$orderBy .= ' ORDER BY `product`.'.$this->kclass->input['orderby'];
		}else{

			$orderBy .= ' ORDER BY `product`.productid ';
		}
		if($this->kclass->input['direction']!=''){
			$orderBy .= ' '.$this->kclass->input['direction'];
		}else{
			$orderBy .=' ASC';
		}
		if($this->kclass->input['show']=='all'){
			$condition =' 1=1';
		}else{
			if($flag){
				$condition.=" AND";
			}
			$condition .=' `product`.killed=0';
			$flag = true;
		}
		/*
		if($this->kclass->input['show']=='all'){
			$condition='`product`.killed=0';
		}else{
			$condition='1=1';
		}*/

		$products = $this->kclass->DB->query("
			SELECT `product`.productid,`product`.title,`product`.ordering,`product`.entitle,`product`.productno,`product`.cover,`product`.attachs,`product`.images,`product`.modified,`product`.created,`product`.killed,
				category.title AS categoryTitle,category.entitle AS categoryEntitle,
				attr.title AS attr,`material`.materialno,`material`.title AS material,`material`.standard,
				m.username AS modifier,c.username AS creator
			FROM `product`
			LEFT JOIN `material` ON (material.materialid=product.materialid)
			LEFT JOIN `category` ON (category.categoryid=product.categoryid)
			LEFT JOIN `attr` ON (attr.attrid=product.attrid)
			LEFT JOIN `user` AS m ON (m.userid=`product`.modifier)
			LEFT JOIN `user` AS c ON (c.userid=`product`.creator)
			WHERE ".$condition." ".$orderBy."
		");

		if($sum=$this->kclass->DB->numRows()){
			$body = '<table class="hundred tablesorter mytable"><thead><tr><th width="75">物资编号</th><th>产品名称</th><th width="150">产品类型</th><th></th><th width="50">排序</th><th width="120">创建日期</th><th width="120">修改日期</th><th width="60">创建者</th><th width="60">修改者</th><th width="36">操作</th></tr></thead><tbody>';
			$i=1;
			while($product = $this->kclass->DB->fetchArray($products)){
				$created = date('Y-m-d H:m',$product['created']);
				$modified = date('Y-m-d H:m',$product['modified']);
				$product['title'] = $this->kclass->parseConvertValue($product['title']);
				$product['entitle'] = $this->kclass->parseConvertValue($product['entitle']);
				$w = 0;
				$w = 25-strlen($product['productno']);
				$body .= '
				<tr class="'.$this->kclass->rotateLine().'">
					<td>'.$product['materialno'].'</a></td>
					<td><a href="/s.php?module=product&action=view&productid='.$product['productid'].'" title="'.$product['title'].'">'.$product['title'].'</a>　'.$product['standard'].'</td>
					<td>'.$product['categoryTitle'].'</td>
					<td></td>
					<td>'.$product['ordering'].'</td>
					<td>'.$created.'</td>
					<td>'.$modified.'</td>
					<td>'.$product['creator'].'</td>
					<td>'.$product['modifier'].'</td>
					<td>'.$this->kclass->iif($sample['killed']>0,'<a href="/s.php?module=product&action=restore&productid='.$product['productid'].'&rt=list">恢复</a>','<a href="/s.php?module=product&action=remove&productid='.$product['productid'].'&rt=list" onclick="return confirm(\'你确定要删除这个产品 '.$product['title'].' 吗？\');">删</a> <a href="/s.php?module=product&action=update&productid='.$product['productid'].'&rt=list">改</a>').'</td>
				</tr>';
				$i++;
			}
			$body .= '</tbody></table>';
		}else{
			$body = '暂无相关记录。';
		}

		$this->kclass->page['onload'].="dc.tabhover();$('.mytable').fixedtableheader();$('.tablesorter').tablesorter( {sortList: [[0,0]], headers: { 3: {sorter: false}, 9: {sorter: false} } } );";
		$this->kclass->page['title'] .= ' - 列表';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 列表<span class="tiny">('.$sum.')</span>','right' => '<span class="small">显示方式：</span><a href="/s.php?module=product">默认列表</a>　<a href="/s.php?module=product&action=list&show=all">所有<span class="small gray">(包括删除)</span></a>　|　<span class="small gray">新建：</span><a href="/s.php?module=product&action=add">产品</a>　<a href="/s.php?module=product&action=addParts">配件</a>　<a href="/s.php?module=product&action=addPartsPackage">配件包</a>','body'=>$body));
	}

	function addParts(){
		$attr = $this->kclass->chooserAttr(array('name'=>'attrid','module'=>'product','selectedid'=>3));
		$category = $this->kclass->chooserCategory(array('parentid'=>1,'name'=>'categoryid','hasBlank'=>1,'width'=>450,'selectedid'=>2));
		$brand = $this->kclass->chooserBrand(array('name'=>'brandid','hasBlank'=>1,'width'=>100,'selectedid'=>1));
		$packing = $this->kclass->chooserPacking(array('name'=>'packingid','hasBlank'=>1,'width'=>200,'selectedid'=>6));
		$unit = $this->kclass->chooserUnit(array('name'=>'unitid','hasBlank'=>1,'width'=>160,'selectedid'=>5));
		$upload = $this->kclass->upload(array('title'=>'相关附件：'));
		$parentProduct = $this->kclass->chooserParentProduct(array('parentProduct'=>''));
		$material=$this->kclass->chooserMaterial(array('hName'=>'materialid','hId'=>'materialid','name'=>'materialTitle','id'=>'materialTitle','width'=>450,'hasBlank'=>1,'topname'=>'','selectedid'=>$b['selected']['materialid'],'value'=>$this->kclass->iif($b['selected'],$b['selected']['no'].'　'.$b['selected']['materialTitle'].'　'.$b['selected']['materialStandard'],''),'line'=>''));
$body = <<<EOF
<form action="/s.php?module=product&action=insertParts" name="product" method="post">
<input type="hidden" name="module" value="product">
<input type="hidden" name="action" value="insertParts">
<table class="hundred">
<thead>
<tr>
	<th colspan="7">配件资料：</th>
</tr>
</thead>
<tbody>
<tr class="odd">
	<td width="100">中文名称：<span class="red bold">*</span></td>
	<td width="750"><input type="text" style="width:450px" name="title" value="{$product['title']}"></td>
	<td width="100">英文名称：<span class="red bold">*</span></td>
	<td><input type="text" style="width:450px" name="entitle" value="{$product['entitle']}"></td>
</tr>
<tr class="even">
	<td>所属物资：</td>
	<td>{$material}</td>
	<td>产品类型：<span class="red bold">*</span></td>
	<td>{$attr}　简称缩写：<input type="text" style="width:130px" name="abbr" value="{$product['abbr']}"><span class="gray small">不超过10个字符</span></td>
</tr>
<tr class="odd">
	<td valign="top">产品分类：</td>
	<td>{$category}</td>
	<td valign="top">产品品牌：</td>
	<td>{$brand}　　包装：<span class="red bold">*</span>{$packing}</td>
</tr>
<tr class="even">
	<td>产品编号：</td>
	<td><input type="text" style="width:250px" name="productno" value=""></td>
	<td>单　　位：<span class="red bold">*</span></td>
	<td>{$unit}　　是否在网站上显示：<input type="radio" name="display" value=1 checked />是 <input type="radio" name="display" value=0 />否</td>
</tr>
<tr class="even">
	<td valign="top">所属成品：</td>
	<td colspan="3">{$parentProduct}</td>
</tr>
<tr class="odd">
	<td valign="top">配件特色：</td>
	<td><textarea name="features" style="width:450px;height:150px">{$product['features']}</textarea></td>
	<td valign="top">技术参数：</td>
	<td><textarea name="technical" style="width:450px;height:150px">{$product['technical']}</textarea></td>
</tr>
<tr class="even">
	<td valign="top">详细规格：</td>
	<td><textarea name="specifications" style="width:450px;height:150px">{$product['specifications']}</textarea></td>
	<td valign="top">配件说明：</td>
	<td><textarea name="details" style="width:450px;height:150px">{$product['details']}</textarea></td>
</tr>
<tr class="odd">
	<td valign="top">配件描述：</td>
	<td><textarea name="description" style="width:450px;height:150px">{$product['description']}</textarea></td>
	<td valign="top">备　　注：</td>
	<td><textarea name="remark" style="width:450px;height:150px">{$product['remark']}</textarea></td>
</tr>
<tr class="even">
	<td valign="top">产品排序：</td>
	<td><input type="text" name="ordering" value="1"></td>
	<td></td><td></td>
</tr>
</tbody>
</table>
{$upload}
<table class="tlist tsuite" align="center" width="100%">
<tbody>
<tr class="odd">
	<td colspan="4" align="center">
		<input type="submit" id="submitButton" value="  保存  " accesskey="s">
		<input type="reset" value="  复位  ">
	</td>
</tr>
<tr class="even">
	<td class="small gray">
	注意事项：
	英文地址：请严格按英文的格式进行(会影响订单中打印的地址)，基本与中文地址相反的顺序。
	未有列项的内容，写入“备注”中，长度上限3万中文字。
	</td>
</tr>
</tbody>
</table>
</form>
EOF;
		$this->kclass->tbline+=4;
		$this->kclass->page['title'] .= ' - 新建产品配件';
		$this->kclass->page['onload'] .= 'dc.tabhover()';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 新建产品配件','right' => '<a href="/s.php?module=product">返回列表</a>','body'=>$body));
	}

	function insertParts(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			if($this->kclass->input['title'] == ''){
				$e = '<li>请填写产品的 中文名称。</li>';
			}
			if($this->kclass->input['entitle'] == ''){
				$e .= '<li>请填写产品的 英文名称。</li>';
			}
			if($this->kclass->input['materialid'] <= 0){
				$e .= '<li>请填写产品所属物资。</li>';
			}
		}
		if(isset($e)){
			$this->kclass->messager(array(
					'title' => '新建配件',
					'text' => '您在新建配件的过程中有以下错误：<ul>'.$e.'</ul>',
					'url' => 'javascript:history.back()',
					'sec' => 3
			));
		}

		if(!empty($this->kclass->input['parentProduct'])){
			sort($this->kclass->input['parentProduct']);
			$parentProductStr=implode(',',$this->kclass->input['parentProduct']);
		}else{
			$parentProductStr=0;
		}

		$this->kclass->DB->query("
			INSERT INTO `product` (`title`,`entitle`,`materialid`,`abbr`,`productno`,`categoryid`,`attrid`,`brandid`,`packingid`,`unitid`,`parentProduct`,`features`,`technical`,`specifications`,`details`,`description`,`display`,`remark`,`ordering`,`modified`,`creator`,`created`)
				VALUES ('".$this->kclass->input['title']."','".$this->kclass->input['entitle']."','".$this->kclass->input['materialid']."','".$this->kclass->input['abbr']."','".$this->kclass->input['productno']."','".$this->kclass->input['categoryid']."','".$this->kclass->input['attrid']."','".$this->kclass->input['packingid']."','".$this->kclass->input['unitid']."','".$this->kclass->input['brandid']."','".$parentProductStr."','".$this->kclass->input['features']."','".$this->kclass->input['technical']."','".$this->kclass->input['specifications']."','".$this->kclass->input['details']."','".$this->kclass->input['description']."','".$this->kclass->input['display']."','".$this->kclass->input['remark']."','".$this->kclass->input['ordering']."','".TIMENOW."','".$this->kclass->user['userid']."','".TIMENOW."')
		");
		$productid = $this->kclass->DB->insertID();
		$this->kclass->updateAttachs(array('module'=>'product','mid'=>$productid));

		$this->kclass->messager(array(
			'title' => '新建配件',
			'text' => '配件 <b>'.$this->kclass->input['title'].'</b> 已新建成功!',
			'url' => '/s.php?module=product&action=view&productid='.$productid,
			'sec' => 3
		));
	}

	function updateParts(){

	}

	function doupdateParts(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			if($this->kclass->input['title'] == ''){
				$e = '<li>请填写产品的 中文名称。</li>';
			}
			if($this->kclass->input['entitle'] == ''){
				$e .= '<li>请填写产品的 英文名称。</li>';
			}
			if($this->kclass->input['materialid'] <= 0){
				$e .= '<li>请填写产品所属物资。</li>';
			}
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '修改配件',
				'text' => '您在新建配件的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}

		$marterial = $this->kclass->DB->queryFirst("SELECT * FROM material WHERE materialid='".$this->kclass->input['materialid']."'");
		if(!$marterial){
			$this->kclass->messager(array(
				'title' => '修改配件',
				'text' => '没有对应的生产物资',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		sort($this->kclass->input['parentProduct']);
		$parentProductStr = implode(',',$this->kclass->input['parentProduct']);
		$this->kclass->DB->query("
			UPDATE `product` SET
				`title` = '".$this->kclass->input['title']."',
				`entitle` = '".$this->kclass->input['entitle']."',
				`materialid` = '".$this->kclass->input['materialid']."',
				`abbr` = '".$this->kclass->input['abbr']."',
				`productno` = '".$this->kclass->input['productno']."',
				`categoryid` = '".$this->kclass->input['categoryid']."',
				`attrid` = '".$this->kclass->input['attrid']."',
				`brandid` = '".$this->kclass->input['brandid']."',
				`unitid` = '".$this->kclass->input['unitid']."',
				`packingid` = '".$this->kclass->input['packingid']."',
				`parentProduct` = '".$parentProductStr."',
				`features` = '".$this->kclass->input['features']."',
				`technical` = '".$this->kclass->input['technical']."',
				`specifications` = '".$this->kclass->input['specifications']."',
				`details` = '".$this->kclass->input['details']."',
				`description` = '".$this->kclass->input['description']."',
				`remark` = '".$this->kclass->input['remark']."',
				`display` = '".$this->kclass->input['display']."',
				`ordering` = '".$this->kclass->input['ordering']."',
				`modified` = '".TIMENOW."',
				`creator` = '".$this->kclass->user['userid']."',
				`created` = '".TIMENOW."'
			WHERE productid = '".$this->kclass->input['productid']."'
		");
		$this->kclass->updateAttachs(array('module'=>'product','mid'=>$this->kclass->input['productid']));

		$this->kclass->messager(array(
				'title' => '修改配件',
				'text' => '配件 <b>'.$this->kclass->input['title'].'</b> 已修改成功!',
				'url' => '/s.php?module=product&action=view&productid='.$this->kclass->input['productid'],
				'sec' => 3
		));
	}

	// 
	function addPartsPackage(){
		$attr = $this->kclass->chooserAttr(array('name'=>'attrid','module'=>'product','selectedid'=>5));
		$category = $this->kclass->chooserCategory(array('parentid'=>1,'name'=>'categoryid','hasBlank'=>1,'width'=>450,'selectedid'=>2));
		$brand = $this->kclass->chooserBrand(array('name'=>'brandid','hasBlank'=>1,'width'=>100,'selectedid'=>1));
		$upload = $this->kclass->upload(array('title'=>'相关附件：'));
		$parentProduct = $this->kclass->chooserParentProduct(array('parentProduct'=>''));
		$parts=$this->kclass->chooserParts(array('name'=>'itemPartsid['.$i.']','hasBlank'=>1,'width'=>500));
		//$material=$this->kclass->chooserMaterial(array('hName'=>'materialid','hId'=>'materialid','name'=>'materialTitle','id'=>'materialTitle','width'=>450,'hasBlank'=>1,'topname'=>'','selectedid'=>$b['selected']['materialid'],'value'=>$this->kclass->iif($b['selected'],$b['selected']['no'].'：<.$b['selected']['materialTitle'].'：<.$b['selected']['materialStandard'],''),'line'=>''));
$body = <<<EOF
<form action="/s.php?module=product&action=insertPartsPackage" name="product" method="post">
<input type="hidden" name="module" value="product">
<input type="hidden" name="action" value="insertPartsPackage">
<table class="hundred">
<thead>
<tr>
	<th colspan="7">配件资料：</th>
</tr>
</thead>
<tbody>
<tr class="odd">
	<td width="155">中文名称：<span class="red bold">*</span></td>
	<td width="625"><input type="text" style="width:450px" name="title" value="{$product['title']}"></td>
	<td width="155">英文名称：<span class="red bold">*</span></td>
	<td><input type="text" style="width:450px" name="entitle" value="{$product['entitle']}"></td>
</tr>
<tr class="even">
	<td>产品类型：<span class="red bold">*</span></td>
	<td>{$attr}</td>
	<td>简称缩写：</td>
	<td><input type="text" style="width:130px" name="abbr" value="{$product['abbr']}"><span class="gray small">不超过10个字符</span>　产品排序：<input type="text" name="ordering" value="1" size=5></td>
</tr>
<tr class="odd">
	<td valign="top">产品分类：</td>
	<td>{$category}</td>
	<td valign="top">产品品牌：</td>
	<td>{$brand}　　是否在网站上显示：<input type="radio" name="display" value=1 checked />是 <input type="radio" name="display" value=0 />否</td>
</tr>
<tr class="even">
	<td valign="top">所属成品：</td>
	<td colspan="3">{$parentProduct}</td>
</tr>
<tr class="odd">
	<td valign="top">配件特色：</td>
	<td><textarea name="features" style="width:450px;height:150px">{$product['features']}</textarea></td>
	<td valign="top">技术参数：</td>
	<td><textarea name="technical" style="width:450px;height:150px">{$product['technical']}</textarea></td>
</tr>
<tr class="even">
	<td valign="top">详细规格：</td>
	<td><textarea name="specifications" style="width:450px;height:150px">{$product['specifications']}</textarea></td>
	<td valign="top">配件说明：</td>
	<td><textarea name="details" style="width:450px;height:150px">{$product['details']}</textarea></td>
</tr>
<tr class="odd">
	<td valign="top">配件描述：</td>
	<td><textarea name="description" style="width:450px;height:150px">{$product['description']}</textarea></td>
	<td valign="top">备　　注：</td>
	<td><textarea name="remark" style="width:450px;height:150px">{$product['remark']}</textarea></td>
</tr>
</tbody>
</table>
<table class="hundred"><thead><tr><th colspan=4>配件明细</th></tr></thead>
<tbody>
<tr id='tr'><td colspan=4>选择要加入配件包的配件</td></tr>
<tr class="red"><td colspan=4><span onclick="dc.product.showParts('tr')" class="hand" style="width:200px;height:20px;border:1px solid red">+选择要添加的配件</span></td></tr>
</tbody>
</table>
{$upload}
<table class="tlist tsuite" align="center" width="100%">
<tbody>
<tr class="odd">
	<td colspan="4" align="center">
		<input type="submit" id="submitButton" value="  保存  " accesskey="s">
		<input type="reset" value="  复位  ">
	</td>
</tr>
<tr class="even">
	<td class="small gray">
	注意事项：
	英文地址：请严格按英文的格式进行(会影响订单中打印的地址)，基本与中文地址相反的顺序。
	未有列项的内容，写入“备注”中，长度上限3万中文字。
	</td>
</tr>
</tbody>
</table>
</form>
<div id="partsPackage" title="选择配件"></div>
EOF;
		$this->kclass->tbline+=4;
		$this->kclass->page['title'] .= ' - 新建产品配件包';
		$this->kclass->page['onload'] .= 'dc.tabhover()';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 新建产品配件','right' => '<a href="/s.php?module=product">返回列表</a>','body'=>$body));
	}
	// 
	function insertPartsPackage(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			if($this->kclass->input['title'] == ''){
				$e = '<li>请填写产品的 中文名称。</li>';
			}
			if($this->kclass->input['entitle'] == ''){
				$e .= '<li>请填写产品的 英文名称。</li>';
			}
			$hasItem=0;
			$count=count($this->kclass->input['partId']);
			for($i=1;$i<=$count;$i++){
				if($this->kclass->input['partId'][$i]>0 AND $this->kclass->input['itemQuantity'][$i]>0){
					$hasItem=1;
				}
			}
			if($hasItem==0){
				$e.='<li>请选择要加入配件包的配件</li>';
			}
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '新建配件包',
				'text' => '您在新建配件包的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		if(!empty($this->kclass->input['parentProduct'])){
			sort($this->kclass->input['parentProduct']);
			$parentProductStr=implode(',',$this->kclass->input['parentProduct']);
		}else{
			$parentProductStr=0;
		}

		$this->kclass->DB->query("
			INSERT INTO `product` (`title`,`entitle`,`abbr`,`productno`,`categoryid`,`attrid`,`brandid`,`parentProduct`,`features`,`technical`,`specifications`,`details`,`description`,`display`,`remark`,`ordering`,`modified`,`creator`,`created`)
			VALUES 
				('".$this->kclass->input['title']."','".$this->kclass->input['entitle']."','".$this->kclass->input['abbr']."','".$this->kclass->input['productno']."','".$this->kclass->input['categoryid']."','".$this->kclass->input['attrid']."','".$this->kclass->input['brandid']."','".$parentProductStr."','".$this->kclass->input['features']."','".$this->kclass->input['technical']."','".$this->kclass->input['specifications']."','".$this->kclass->input['details']."','".$this->kclass->input['description']."','".$this->kclass->input['display']."','".$this->kclass->input['remark']."','".$this->kclass->input['ordering']."','".TIMENOW."','".$this->kclass->user['userid']."','".TIMENOW."')
		");
		$productid = $this->kclass->DB->insertID();
		for($i=1;$i<=$count;$i++){
			if($this->kclass->input['partId'][$i]>0 AND $this->kclass->input['itemQuantity'][$i]>0){
				$this->kclass->DB->query("
					INSERT INTO partitem (productid,childid,quantity,remark,created,creator) VALUES ('".$productid."','".$this->kclass->input['partId'][$i]."','".$this->kclass->input['itemQuantity'][$i]."','".$this->kclass->input['itemRemark'][$i]."','".TIMENOW."','".$this->kclass->user['userid']."')
				");
			}
		}
		$this->kclass->updateAttachs(array('module'=>'product','mid'=>$productid));

		$this->kclass->messager(array(
			'title' => '新建配件',
			'text' => '配件 <b>'.$this->kclass->input['title'].'</b> 已新建成功!',
			'url' => '/s.php?module=product&action=view&productid='.$productid,
			'sec' => 3
		));
	}
	// 
	function doupdatePartsPackage(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			if($this->kclass->input['title'] == ''){
				$e = '<li>请填写产品的 中文名称。</li>';
			}
			if($this->kclass->input['entitle'] == ''){
				$e .= '<li>请填写产品的 英文名称。</li>';
			}
			$product=$this->kclass->DB->queryFirst("SELECT productid,title,attrid FROM product WHERE killed=0 AND productid='".$this->kclass->input['productid']."' LIMIT 0,1");
			$hasItem=0;
			$itemCount=count($this->kclass->input['itemId']);
			for($i=1;$i<=$itemCount;$i++){
				if($this->kclass->input['orgKill'][$i]=='' AND $this->kclass->input['orgQuantity'][$i]>0){
					$hasItem=1;
				}
			}
			if($hasItem==0 AND count($this->kclass->input['partId'])==0){
				$e.='<li>请添加配件的明细</li>';
			}
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '修改配件包',
				'text' => '您在修改配件包的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		
		for($i=1;$i<=$itemCount;$i++){
			if($this->kclass->input['orgKill'][$i]>0 AND $this->kclass->input['orgKill'][$i]==$this->kclass->input['itemId'][$i]){
				$this->kclass->DB->query("UPDATE partitem SET killed='".TIMENOW."',killer='".$this->kclass->user['userid']."' WHERE itemid='".$this->kclass->input['itemId'][$i]."'");
			}else{
				if($this->kclass->input['itemId'][$i]>0 AND $this->kclass->input['orgQuantity'][$i]>0){
					$this->kclass->DB->query("
						UPDATE partitem SET 
							quantity='".$this->kclass->input['orgQuantity'][$i]."',
							remark='".$this->kclass->input['orgRemark'][$i]."',
							modified='".TIMENOW."',
							modifier='".$this->kclass->user['userid']."'
						WHERE itemid='".$this->kclass->input['itemId'][$i]."'
					");
				}
			}
		}
		$partCount=count($this->kclass->input['partId']);
		for($j=1;$j<=$partCount;$j++){
			if($this->kclass->input['itemId'][$j]>0 AND $this->kclass->input['orgQuantity'][$j]>0){
				$this->kclass->DB->query("
					INSERT INTO partitem (productid,childid,quantity,remark,created,creator) VALUES ('".$this->kclass->input['productid']."','".$this->kclass->input['partId'][$j]."','".$this->kclass->input['itemQuantity'][$j]."','".$this->kclass->input['itemRemark'][$j]."','".TIMENOW."','".$this->kclass->user['userid']."')
				");
			}
		}
		$this->kclass->messager(array(
			'title' => '修改配件包',
			'text' => '配件包 <b>'.$this->kclass->input['title'].'</b> 已修改成功!',
			'url' => '/s.php?module=product&action=view&productid='.$this->kclass->input['productid'],
			'sec' => 3
		));
	}

	//
	function insertInquiry(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			if($this->kclass->input['supplierid'] <= 0){
				$e = '<li>请选择要询价的供应商(工厂)。</li>';
			}
			if($this->kclass->input['contact'] == ''){
				$e .= '<li>请填写询价供应商的相关联系人。</li>';
			}
			if($this->kclass->input['quantity'] == ''){
				$e .= '<li>请填写询价的数量，如果无数量要求，请填写“0”。</li>';
			}
			if($this->kclass->input['unitid'] <= 0){
				$e .= '<li>请选择询价产品的数量单位。</li>';
			}
			if($this->kclass->input['currencyid'] <= 0){
				$e .= '<li>请选择询价的价格币种。</li>';
			}
			if($this->kclass->input['price'] == ''){
				$e .= '<li>请填写询价的产品单价。</li>';
			}
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '新建产品询价单',
				'text' => '您在新建产品询价单的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$this->kclass->DB->query("
			INSERT INTO `productinquiry` (`productid`,`supplierid`,`contact`,`title`,`model`,`quantity`,`unitid`,`price`,`currencyid`,`discount`,`packingid`,`portid`,`deliverytermid`,`paymenttermid`,`shipmethodid`,`standard`,`material`,`technics`,`remark`,`modified`,`creator`,`created`) VALUES ('".$this->kclass->input['productid']."','".$this->kclass->input['supplierid']."','".$this->kclass->input['contact']."','".$this->kclass->input['title']."','".$this->kclass->input['model']."','".$this->kclass->input['quantity']."','".$this->kclass->input['unitid']."','".$this->kclass->input['price']."','".$this->kclass->input['currencyid']."','".$this->kclass->input['discount']."','".$this->kclass->input['packingid']."','".$this->kclass->input['portid']."','".$this->kclass->input['deliverytermid']."','".$this->kclass->input['paymenttermid']."','".$this->kclass->input['shipmethodid']."','".$this->kclass->input['standard']."','".$this->kclass->input['material']."','".$this->kclass->input['technics']."','".$this->kclass->input['remark']."','".TIMENOW."','".$this->kclass->user['userid']."','".TIMENOW."')
		");
		$this->kclass->mySetcookie(array('name'=>'tabsproduct',2));

		$this->kclass->messager(array(
			'title' => '新建产品询价单',
			'text' => '产品<b>'.$this->kclass->input['title'].'</b> 的询价单已新建成功!',
			'url' => '/s.php?module=product&action=view&productid='.$this->kclass->input['productid'],
			'sec' => 2
		));
	}

	//
	function addInquiry(){
		if($this->kclass->input['productid']<=0){
			$this->kclass->boinkIt('/s.php?module=product');
		}
		$product=$this->kclass->DB->queryFirst("SELECT productid,title FROM `product` WHERE killed=0 AND productid='".$this->kclass->input['productid']."'");
		$supplier = $this->kclass->chooserSupplier(array('name'=>'supplierid','hasBlank'=>1,'width'=>450,'selectedid'=>$this->kclass->input['supplierid']));
		$currency = $this->kclass->chooserCurrency(array('name'=>'currencyid','hasBlank'=>1,'width'=>100,'selectedid'=>3));
		$packing = $this->kclass->chooserPacking(array('name'=>'packingid','hasBlank'=>1,'width'=>140,'selectedid'=>$this->kclass->input['packingid']));
		$unit = $this->kclass->chooserUnit(array('name'=>'unitid','hasBlank'=>1,'width'=>110,'selectedid'=>$this->kclass->input['unitid']));
		$shipmethod = $this->kclass->chooserShipmethod(array('name'=>'shipmethodid','hasBlank'=>1,'width'=>140,'selectedid'=>$this->kclass->input['shipmethodid']));
		$paymentterm = $this->kclass->chooserPaymentterm(array('name'=>'paymenttermid','hasBlank'=>1,'width'=>450,'selectedid'=>$this->kclass->input['paymenttermid']));
		$deliveryterm = $this->kclass->chooserDeliveryterm(array('name'=>'deliverytermid','hasBlank'=>1,'width'=>450,'selectedid'=>$this->kclass->input['deliverytermid']));
		$port = $this->kclass->chooserPort(array('name'=>'portid','hasBlank'=>1,'width'=>450,'selectedid'=>$this->kclass->input['portid']));
$body = <<<EOF
<form action="/s.php?module=product&action=insertInquiry" name="product" method="post">
<input type="hidden" name="module" value="product">
<input type="hidden" name="action" value="insertInquiry">
<input type="hidden" name="productid" value="{$this->kclass->input['productid']}">
<table class="hundred">
<thead>
<tr>
	<th colspan="4">新建产品询价单：<span class="darkred">{$product['title']}</span></th>
</tr>
</thead>
<tbody>
<tr class="even">
	<td>供 应 商：<span class="red bold">*</span></td>
	<td>{$supplier}</td>
	<td>产品名称：</td>
	<td><input type="text" style="width:250px" name="title" value="{$inquiry['title']}"> <span class="small gray">指供应商用的名称</span></td>
</tr>
<tr class="odd">
	<td>联 系 人：<span class="red bold">*</span></td>
	<td><input type="text" style="width:250px" name="contact" value="{$inquiry['contact']}"> <span class="small gray">指被询价方人员</span></td>
	<td>产品型号：</td>
	<td><input type="text" style="width:250px" name="model" value="{$inquiry['model']}"> <span class="small gray">指供应商用的型号</span></td>
</tr>
<tr class="odd">
	<td>数　　量：<span class="red bold">*</span></td>
	<td><input type="text" style="width:70px" name="quantity" value="{$inquiry['quantity']}">{$unit} 折扣：<input type="text" style="width:116px" name="discount" value="{$inquiry['discount']}"></td>
	<td>产品单价：<span class="red bold">*</span></td>
	<td>{$currency}<input type="text" style="width:250px" name="price" value="{$inquiry['price']}"></td>
</tr>
<tr class="even">
	<td>包装方式：</td>
	<td>{$packing} 运送方式：{$shipmethod}</td>
	<td>交货地点：</td>
	<td>{$port}</td>
</tr>
<tr class="even">
	<td>支付条款：</td>
	<td>{$paymentterm}</td>
	<td>交货条款：</td>
	<td>{$deliveryterm}</td>
</tr>
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
		$this->kclass->page['title'] .= ' - '.$product['title'].' - 新建产品询价';
		$this->kclass->page['onload'] .= 'dc.tabhover()';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - <a href="/s.php?module=product&action=view&productid='.$product['productid'].'">'.$product['title'].'</a> - 新建产品询价','right' => '<a href="/s.php?module=product">返回列表</a>','body'=>$body));
	}

	//
	function doupdateInquiry(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			if($this->kclass->input['supplierid'] <= 0){
				$e = '<li>请选择要询价的供应商(工厂)。</li>';
			}
			if($this->kclass->input['contact'] == ''){
				$e .= '<li>请填写询价供应商的相关联系人。</li>';
			}
			if($this->kclass->input['quantity'] == ''){
				$e .= '<li>请填写询价的数量，如果无数量要求，请填写“0”。</li>';
			}
			if($this->kclass->input['unitid'] <= 0){
				$e .= '<li>请选择询价产品的数量单位。</li>';
			}
			if($this->kclass->input['currencyid'] <= 0){
				$e .= '<li>请选择询价的价格币种。</li>';
			}
			if($this->kclass->input['price'] == ''){
				$e .= '<li>请填写询价的产品单价。</li>';
			}
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '修改产品询价单',
				'text' => '您在修改产品询价单的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$this->kclass->DB->query("
			UPDATE `productinquiry`
			SET `supplierid` = '".$this->kclass->input['supplierid']."',
				`contact` = '".$this->kclass->input['contact']."',
				`title` = '".$this->kclass->input['title']."',
				`model` = '".$this->kclass->input['model']."',
				`quantity` = '".$this->kclass->input['quantity']."',
				`unitid` = '".$this->kclass->input['unitid']."',
				`price` = '".$this->kclass->input['price']."',
				`currencyid` = '".$this->kclass->input['currencyid']."',
				`discount` = '".$this->kclass->input['discount']."',
				`packingid` = '".$this->kclass->input['packingid']."',
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
			'title' => '修改产品询价单',
			'text' => '产品<b>'.$this->kclass->input['title'].'</b> 的询价单已修改成功!',
			'url' => '/s.php?module=product&action=view&productid='.$this->kclass->input['productid'],
			'sec' => 2
		));
	}

	//
	function updateInquiry(){
		if($this->kclass->input['inquiryid']<=0 OR !$inquiry = $this->kclass->DB->queryFirst("SELECT `inquiry`.*,product.title AS product FROM `productinquiry` AS `inquiry` LEFT JOIN product ON (product.productid=inquiry.productid) WHERE `inquiryid`='".$this->kclass->input['inquiryid']."'")){
			$this->kclass->boinkIt('/s.php?module=inquiry');
		}
		$this->kclass->br2nl=true;
		$inquiry['standard']=$this->kclass->parseConvertValue($inquiry['standard']);
		$inquiry['material']=$this->kclass->parseConvertValue($inquiry['material']);
		$inquiry['technics']=$this->kclass->parseConvertValue($inquiry['technics']);
		$inquiry['remark']=$this->kclass->parseConvertValue($inquiry['remark']);
		$supplier = $this->kclass->chooserSupplier(array('name'=>'supplierid','hasBlank'=>1,'width'=>450,'selectedid'=>$inquiry['supplierid']));
		$currency = $this->kclass->chooserCurrency(array('name'=>'currencyid','hasBlank'=>1,'width'=>100,'selectedid'=>$inquiry['currencyid']));
		$packing = $this->kclass->chooserPacking(array('name'=>'packingid','hasBlank'=>1,'width'=>140,'selectedid'=>$inquiry['packingid']));
		$unit = $this->kclass->chooserUnit(array('name'=>'unitid','hasBlank'=>1,'width'=>110,'selectedid'=>$inquiry['unitid']));
		$shipmethod = $this->kclass->chooserShipmethod(array('name'=>'shipmethodid','hasBlank'=>1,'width'=>140,'selectedid'=>$inquiry['shipmethodid']));
		$paymentterm = $this->kclass->chooserPaymentterm(array('name'=>'paymenttermid','hasBlank'=>1,'width'=>450,'selectedid'=>$inquiry['paymenttermid']));
		$deliveryterm = $this->kclass->chooserDeliveryterm(array('name'=>'deliverytermid','hasBlank'=>1,'width'=>450,'selectedid'=>$inquiry['deliverytermid']));
		$port = $this->kclass->chooserPort(array('name'=>'portid','hasBlank'=>1,'width'=>450,'selectedid'=>$inquiry['portid']));
$body = <<<EOF
<form action="/s.php?module=product&action=doupdateInquiry" name="product" method="post">
<input type="hidden" name="module" value="product">
<input type="hidden" name="action" value="doupdateInquiry">
<input type="hidden" name="productid" value="{$inquiry['productid']}">
<input type="hidden" name="inquiryid" value="{$inquiry['inquiryid']}">
<table class="hundred">
<thead>
<tr>
	<th colspan="4">修改产品询价单：<span class="darkred">{$inquiry['product']}</span></th>
</tr>
</thead>
<tbody>
<tr class="even">
	<td>供 应 商：<span class="red bold">*</span></td>
	<td>{$supplier}</td>
	<td>产品名称：</td>
	<td><input type="text" style="width:250px" name="title" value="{$inquiry['title']}"> <span class="small gray">指供应商用的名称</span></td>
</tr>
<tr class="odd">
	<td>联 系 人：<span class="red bold">*</span></td>
	<td><input type="text" style="width:250px" name="contact" value="{$inquiry['contact']}"> <span class="small gray">指被询价方人员</span></td>
	<td>产品型号：</td>
	<td><input type="text" style="width:250px" name="model" value="{$inquiry['model']}"> <span class="small gray">指供应商用的型号</span></td>
</tr>
<tr class="odd">
	<td>数　　量：<span class="red bold">*</span></td>
	<td><input type="text" style="width:70px" name="quantity" value="{$inquiry['quantity']}">{$unit} 折扣：<input type="text" style="width:116px" name="discount" value="{$inquiry['discount']}"></td>
	<td>产品单价：<span class="red bold">*</span></td>
	<td>{$currency}<input type="text" style="width:250px" name="price" value="{$inquiry['price']}"></td>
</tr>
<tr class="even">
	<td>包装方式：</td>
	<td>{$packing} 运送方式：{$shipmethod}</td>
	<td>交货地点：</td>
	<td>{$port}</td>
</tr>
<tr class="even">
	<td>支付条款：</td>
	<td>{$paymentterm}</td>
	<td>交货条款：</td>
	<td>{$deliveryterm}</td>
</tr>
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
		$this->kclass->page['title'] .= ' - '.$inquiry['product'].' - 修改产品询价';
		$this->kclass->page['onload'] .= 'dc.tabhover()';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - <a href="/s.php?module=product&action=view&productid='.$inquiry['productid'].'">'.$inquiry['product'].'</a> - 修改产品询价','right' => '<a href="/s.php?module=product">返回列表</a>','body'=>$body));
	}
	//
	function killInquiry(){
		if($this->kclass->input['inquiryid']<=0){
			$this->kclass->boinkIt('/s.php?module=product');
		}
		if($this->kclass->input['confirm'] == 0){
			$this->kclass->boinkIt('/s.php?module=product&action=view&productid='.$this->kclass->input['productid']);
		}
		if($this->kclass->input['inquiryid'] < 0){
			$e = '<li>请从正确的页面进行操作，如果此问题持续存在，请联系网站管理员。</li>';
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '删除产品询价单',
				'text' => '您在删除产品询价单的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$inquiry = $this->kclass->DB->queryFirst("
			SELECT inquiry.inquiryid,inquiry.productid,
				product.title AS product
			FROM productinquiry AS inquiry
			LEFT JOIN product ON (product.productid=inquiry.productid)
			WHERE inquiry.inquiryid='".$this->kclass->input['inquiryid']."'
		");
		if($inquiry){
			$this->kclass->DB->query("
				UPDATE `productinquiry`
				SET killed=".TIMENOW."
				WHERE inquiryid='".$inquiry['inquiryid']."'
			");
			$this->kclass->messager(array(
				'title' => '删除产品询价单成功',
				'text' => '产品 <b>'.$inquiry['product'].'</b> 下的 询价单 已成功被标记为删除!',
				'url' => '/s.php?module=product&action=view&productid='.$inquiry['productid'],
				'sec' => 2
			));
		}else{
			$this->kclass->messager(array(
				'title' => '删除产品询价单失败',
				'text' => '您要删除的产品询价单，不存在！',
				'url' => '/s.php?module=product&action=view&productid='.$inquiry['productid'],
				'sec' => 3
			));
		}
	}

	//
	function removeInquiry(){
		if($this->kclass->input['inquiryid']<=0){
			$this->kclass->boinkIt('/s.php?module=product');
		}
		$inquiry = $this->kclass->DB->queryFirst("
			SELECT inquiry.inquiryid,inquiry.productid,
				product.title AS product
			FROM productinquiry AS inquiry
			LEFT JOIN product ON (product.productid=inquiry.productid)
			WHERE inquiry.inquiryid='".$this->kclass->input['inquiryid']."'
		");
		if($inquiry){
$body = <<<EOF
<form action="/s.php?module=product&action=killInquiry" name="product" method="post">
<input type="hidden" name="module" value="product">
<input type="hidden" name="action" value="killInquiry">
<input type="hidden" name="productid" value="{$inquiry['productid']}">
<input type="hidden" name="inquiryid" value="{$inquiry['inquiryid']}">
<table><thead>
<thead>
<tr>
	<th>删除产品：{$inquiry['product']}</th>
</tr>
</thead>
<tbody>
<tr class="even">
	<td class="middle">你确定要删除产品 <a href="/s.php?module=product&action=view&productid={$inquiry['productid']}" class="middle bold" target="_blank">{$inquiry['product']}</a> 的 <span class="big bold">询价单</span> 吗?</td>
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
		$this->kclass->page['title'] .= ' - '.$inquiry['product'].' - 删除产品询价';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - <a href="/s.php?module=product&action=view&productid='.$product['productid'].'">'.$inquiry['product'].'</a> - 删除产品询价','right' => '<a href="/s.php?module=product">返回列表</a>','body'=>$body));
	}

	//
	function revivalInquiry(){
		if($this->kclass->input['inquiryid']<=0){
			$this->kclass->boinkIt('/s.php?module=product');
		}
		if($this->kclass->input['confirm'] == 0){
			$this->kclass->boinkIt('/s.php?module=product&action=view&productid='.$this->kclass->input['productid']);
		}
		if($this->kclass->input['inquiryid'] < 0){
			$e = '<li>请从正确的页面进行操作，如果此问题持续存在，请联系网站管理员。</li>';
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '恢复产品询价单',
				'text' => '您在恢复产品询价单的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$inquiry = $this->kclass->DB->queryFirst("
			SELECT inquiry.inquiryid,inquiry.productid,
				product.title AS product
			FROM productinquiry AS inquiry
			LEFT JOIN product ON (product.productid=inquiry.productid)
			WHERE inquiry.inquiryid='".$this->kclass->input['inquiryid']."'
		");
		if($inquiry){
			$this->kclass->DB->query("
				UPDATE `productinquiry`
				SET killed=0
				WHERE inquiryid='".$inquiry['inquiryid']."'
			");
			$this->kclass->messager(array(
				'title' => '恢复产品询价单成功',
				'text' => '产品 <b>'.$inquiryid['product'].'</b> 下的 询价单 已成功被标记为恢复!',
				'url' => '/s.php?module=product&action=view&productid='.$inquiry['productid'],
				'sec' => 2
			));
		}else{
			$this->kclass->messager(array(
				'title' => '恢复产品询价单失败',
				'text' => '您要恢复的产品询价单，不存在！',
				'url' => '/s.php?module=product&action=view&productid='.$inquiry['productid'],
				'sec' => 3
			));
		}
	}

	//
	function restoreInquiry(){
		if($this->kclass->input['inquiryid']<=0){
			$this->kclass->boinkIt('/s.php?module=product');
		}
		$inquiry = $this->kclass->DB->queryFirst("
			SELECT inquiry.inquiryid,inquiry.productid,
				product.title AS product
			FROM productinquiry AS inquiry
			LEFT JOIN product ON (product.productid=inquiry.productid)
			WHERE inquiry.inquiryid='".$this->kclass->input['inquiryid']."'
		");
		if($inquiry){
$body = <<<EOF
<form action="/s.php?module=product&action=revivalInquiry" name="product" method="post">
<input type="hidden" name="module" value="product">
<input type="hidden" name="action" value="revivalInquiry">
<input type="hidden" name="productid" value="{$inquiry['productid']}">
<input type="hidden" name="inquiryid" value="{$inquiry['inquiryid']}">
<table><thead>
<thead>
<tr>
	<th>恢复产品：{$inquiry['product']}</th>
</tr>
</thead>
<tbody>
<tr class="even">
	<td class="middle">你确定要恢复产品 <a href="/s.php?module=product&action=view&productid={$inquiry['productid']}" class="middle bold" target="_blank">{$inquiry['product']}</a> 的 <span class="big bold">询价单</span> 吗?</td>
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
		$this->kclass->page['title'] .= ' - '.$inquiry['product'].' - 恢复产品询价';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - <a href="/s.php?module=product&action=view&productid='.$product['productid'].'">'.$inquiry['product'].'</a> - 恢复产品询价','right' => '<a href="/s.php?module=product">返回列表</a>','body'=>$body));
	}
	//
	function insertQuoting(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			if($this->kclass->input['customerid'] <= 0){
				$e = '<li>请选择要报价的客户(客户)。</li>';
			}
			if($this->kclass->input['contact'] == ''){
				$e .= '<li>请填写报价客户的相关联系人。</li>';
			}
			if($this->kclass->input['quantity'] == ''){
				$e .= '<li>请填写报价的数量，如果无数量要求，请填写“0”。</li>';
			}
			if($this->kclass->input['unitid'] <= 0){
				$e .= '<li>请选择报价产品的数量单位。</li>';
			}
			if($this->kclass->input['currencyid'] <= 0){
				$e .= '<li>请选择报价的价格币种。</li>';
			}
			if($this->kclass->input['price'] == ''){
				$e .= '<li>请填写报价的产品单价。</li>';
			}
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '新建产品报价单',
				'text' => '您在新建产品报价单的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$this->kclass->DB->query("
			INSERT INTO `productquoting` (`productid`,`customerid`,`contact`,`title`,`model`,`quantity`,`unitid`,`price`,`currencyid`,`discount`,`packingid`,`portid`,`deliverytermid`,`paymenttermid`,`shipmethodid`,`standard`,`material`,`technics`,`remark`,`modified`,`creator`,`created`) VALUES ('".$this->kclass->input['productid']."','".$this->kclass->input['customerid']."','".$this->kclass->input['contact']."','".$this->kclass->input['title']."','".$this->kclass->input['model']."','".$this->kclass->input['quantity']."','".$this->kclass->input['unitid']."','".$this->kclass->input['price']."','".$this->kclass->input['currencyid']."','".$this->kclass->input['discount']."','".$this->kclass->input['packingid']."','".$this->kclass->input['portid']."','".$this->kclass->input['deliverytermid']."','".$this->kclass->input['paymenttermid']."','".$this->kclass->input['shipmethodid']."','".$this->kclass->input['standard']."','".$this->kclass->input['material']."','".$this->kclass->input['technics']."','".$this->kclass->input['remark']."','".TIMENOW."','".$this->kclass->user['userid']."','".TIMENOW."')
		");
		$this->kclass->mySetcookie(array('name'=>'tabsproduct',2));

		$this->kclass->messager(array(
			'title' => '新建产品报价单',
			'text' => '产品<b>'.$this->kclass->input['title'].'</b> 的报价单已新建成功!',
			'url' => '/s.php?module=product&action=view&productid='.$this->kclass->input['productid'],
			'sec' => 2
		));
	}

	//
	function addQuoting(){
		if($this->kclass->input['productid']<=0){
			$this->kclass->boinkIt('/s.php?module=product');
		}
		$product=$this->kclass->DB->queryFirst("SELECT productid,title FROM `product` WHERE killed=0 AND productid='".$this->kclass->input['productid']."'");
		$customer = $this->kclass->chooserCustomer(array('name'=>'customerid','hasBlank'=>1,'width'=>450,'selectedid'=>$this->kclass->input['customerid']));
		$currency = $this->kclass->chooserCurrency(array('name'=>'currencyid','hasBlank'=>1,'width'=>100,'selectedid'=>1));
		$packing = $this->kclass->chooserPacking(array('name'=>'packingid','hasBlank'=>1,'width'=>140,'selectedid'=>$this->kclass->input['packingid']));
		$unit = $this->kclass->chooserUnit(array('name'=>'unitid','hasBlank'=>1,'width'=>110,'selectedid'=>$this->kclass->input['unitid']));
		$shipmethod = $this->kclass->chooserShipmethod(array('name'=>'shipmethodid','hasBlank'=>1,'width'=>140,'selectedid'=>$this->kclass->input['shipmethodid']));
		$paymentterm = $this->kclass->chooserPaymentterm(array('name'=>'paymenttermid','hasBlank'=>1,'width'=>450,'selectedid'=>$this->kclass->input['paymenttermid']));
		$deliveryterm = $this->kclass->chooserDeliveryterm(array('name'=>'deliverytermid','hasBlank'=>1,'width'=>450,'selectedid'=>$this->kclass->input['deliverytermid']));
		$port = $this->kclass->chooserPort(array('name'=>'portid','hasBlank'=>1,'width'=>450,'selectedid'=>$this->kclass->input['portid']));
$body = <<<EOF
<form action="/s.php?module=product&action=insertQuoting" name="product" method="post">
<input type="hidden" name="module" value="product">
<input type="hidden" name="action" value="insertQuoting">
<input type="hidden" name="productid" value="{$this->kclass->input['productid']}">
<table class="hundred">
<thead>
<tr>
	<th colspan="4">新建产品报价单：<span class="darkred">{$product['title']}</span></th>
</tr>
</thead>
<tbody>
<tr class="even">
	<td>客　　户：<span class="red bold">*</span><div>Customer</div></td>
	<td>{$customer}</td>
	<td>产品名称：<div>Product</div></td>
	<td><input type="text" style="width:250px" name="title" value="{$quoting['title']}"> <span class="small gray">指客户用的名称</span></td>
</tr>
<tr class="odd">
	<td>联 系 人：<span class="red bold">*</span><div>Contact</div></td>
	<td><input type="text" style="width:250px" name="contact" value="{$quoting['contact']}"> <span class="small gray">指客户方询价人员</span></td>
	<td>产品型号：<div>Model</div></td>
	<td><input type="text" style="width:250px" name="model" value="{$quoting['model']}"> <span class="small gray">指客户用的型号</span></td>
</tr>
<tr class="even">
	<td>数　　量：<span class="red bold">*</span><div>Quantity</div></td>
	<td><input type="text" style="width:70px" name="quantity" value="{$quoting['quantity']}">{$unit} 折扣：<input type="text" style="width:116px" name="discount" value="{$quoting['discount']}"><div style="margin-left:190px">Discount</div></td>
	<td>产品单价：<span class="red bold">*</span><div>Price</div></td>
	<td>{$currency}<input type="text" style="width:250px" name="price" value="{$quoting['price']}"></td>
</tr>
<tr class="odd">
	<td>包装方式：<div>Package</div></td>
	<td>{$packing} 运送方式：{$shipmethod}<div style="margin-left:144px">Shipment Method</div></td>
	<td>交货地点：<div>Port</div></td>
	<td>{$port}</td>
</tr>
<tr class="even">
	<td>支付条款：<div>Payment Term</div></td>
	<td>{$paymentterm}</td>
	<td>交货条款：<div>Delivery Term</div></td>
	<td>{$deliveryterm}</td>
</tr>
<tr class="odd">
	<td valign="top">规格说明：<div>Standard</div></td>
	<td><textarea name="standard" style="width:450px;height:60px">{$quoting['standard']}</textarea></td>
	<td valign="top">主要材料：<div>Material</div></td>
	<td><textarea name="material" style="width:450px;height:60px">{$quoting['material']}</textarea></td>
</tr>
<tr class="even">
	<td valign="top">工艺说明：<div>Technics</div></td>
	<td><textarea name="technics" style="width:450px;height:60px">{$quoting['technics']}</textarea></td>
	<td valign="top">备　　注：<div>Remark</div></td>
	<td><textarea name="remark" style="width:450px;height:60px">{$quoting['remark']}</textarea></td>
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
		$this->kclass->page['title'] .= ' - '.$product['title'].' - 新建产品报价';
		$this->kclass->page['onload'] .= 'dc.tabhover()';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - <a href="/s.php?module=product&action=view&productid='.$product['productid'].'">'.$product['title'].'</a> - 新建产品报价','right' => '<a href="/s.php?module=product">返回列表</a>','body'=>$body));
	}

	//
	function doupdateQuoting(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			if($this->kclass->input['customerid'] <= 0){
				$e = '<li>请选择要报价的客户(客户)。</li>';
			}
			if($this->kclass->input['contact'] == ''){
				$e .= '<li>请填写报价客户的相关联系人。</li>';
			}
			if($this->kclass->input['quantity'] == ''){
				$e .= '<li>请填写报价的数量，如果无数量要求，请填写“0”。</li>';
			}
			if($this->kclass->input['unitid'] <= 0){
				$e .= '<li>请选择报价产品的数量单位。</li>';
			}
			if($this->kclass->input['currencyid'] <= 0){
				$e .= '<li>请选择报价的价格币种。</li>';
			}
			if($this->kclass->input['price'] == ''){
				$e .= '<li>请填写报价的产品单价。</li>';
			}
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '修改产品报价单',
				'text' => '您在修改产品报价单的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$this->kclass->DB->query("
			UPDATE `productquoting`
			SET `customerid` = '".$this->kclass->input['customerid']."',
				`contact` = '".$this->kclass->input['contact']."',
				`title` = '".$this->kclass->input['title']."',
				`model` = '".$this->kclass->input['model']."',
				`quantity` = '".$this->kclass->input['quantity']."',
				`unitid` = '".$this->kclass->input['unitid']."',
				`price` = '".$this->kclass->input['price']."',
				`currencyid` = '".$this->kclass->input['currencyid']."',
				`discount` = '".$this->kclass->input['discount']."',
				`packingid` = '".$this->kclass->input['packingid']."',
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
			'title' => '修改产品报价单',
			'text' => '产品<b>'.$this->kclass->input['title'].'</b> 的报价单已修改成功!',
			'url' => '/s.php?module=product&action=view&productid='.$this->kclass->input['productid'],
			'sec' => 2
		));
	}

	//
	function updateQuoting(){
		if($this->kclass->input['quotingid']<=0 OR !$quoting = $this->kclass->DB->queryFirst("SELECT `quoting`.*,product.title AS product FROM `productquoting` AS `quoting` LEFT JOIN product ON (product.productid=quoting.productid) WHERE `quotingid`='".$this->kclass->input['quotingid']."'")){
			$this->kclass->boinkIt('/s.php?module=quoting');
		}
		$this->kclass->br2nl=true;
		$quoting['standard']=$this->kclass->parseConvertValue($quoting['standard']);
		$quoting['material']=$this->kclass->parseConvertValue($quoting['material']);
		$quoting['technics']=$this->kclass->parseConvertValue($quoting['technics']);
		$quoting['remark']=$this->kclass->parseConvertValue($quoting['remark']);
		$customer = $this->kclass->chooserCustomer(array('name'=>'customerid','hasBlank'=>1,'width'=>450,'selectedid'=>$quoting['customerid']));
		$currency = $this->kclass->chooserCurrency(array('name'=>'currencyid','hasBlank'=>1,'width'=>100,'selectedid'=>$quoting['currencyid']));
		$packing = $this->kclass->chooserPacking(array('name'=>'packingid','hasBlank'=>1,'width'=>140,'selectedid'=>$quoting['packingid']));
		$unit = $this->kclass->chooserUnit(array('name'=>'unitid','hasBlank'=>1,'width'=>110,'selectedid'=>$quoting['unitid']));
		$shipmethod = $this->kclass->chooserShipmethod(array('name'=>'shipmethodid','hasBlank'=>1,'width'=>140,'selectedid'=>$quoting['shipmethodid']));
		$paymentterm = $this->kclass->chooserPaymentterm(array('name'=>'paymenttermid','hasBlank'=>1,'width'=>450,'selectedid'=>$quoting['paymenttermid']));
		$deliveryterm = $this->kclass->chooserDeliveryterm(array('name'=>'deliverytermid','hasBlank'=>1,'width'=>450,'selectedid'=>$quoting['deliverytermid']));
		$port = $this->kclass->chooserPort(array('name'=>'portid','hasBlank'=>1,'width'=>450,'selectedid'=>$quoting['portid']));
$body = <<<EOF
<form action="/s.php?module=product&action=doupdateQuoting" name="product" method="post">
<input type="hidden" name="module" value="product">
<input type="hidden" name="action" value="doupdateQuoting">
<input type="hidden" name="productid" value="{$quoting['productid']}">
<input type="hidden" name="quotingid" value="{$quoting['quotingid']}">
<table class="hundred">
<thead>
<tr>
	<th colspan="4">修改产品报价单：<span class="darkred">{$quoting['product']}</span></th>
</tr>
</thead>
<tbody>
<tr class="even">
	<td>客　　户：<span class="red bold">*</span></td>
	<td>{$customer}</td>
	<td>产品名称：</td>
	<td><input type="text" style="width:250px" name="title" value="{$quoting['title']}"> <span class="small gray">指客户用的名称</span></td>
</tr>
<tr class="odd">
	<td>联 系 人：<span class="red bold">*</span></td>
	<td><input type="text" style="width:250px" name="contact" value="{$quoting['contact']}"> <span class="small gray">指客户方询价人员</span></td>
	<td>产品型号：</td>
	<td><input type="text" style="width:250px" name="model" value="{$quoting['model']}"> <span class="small gray">指客户用的型号</span></td>
</tr>
<tr class="odd">
	<td>数　　量：<span class="red bold">*</span></td>
	<td><input type="text" style="width:70px" name="quantity" value="{$quoting['quantity']}">{$unit} 折扣：<input type="text" style="width:116px" name="discount" value="{$quoting['discount']}"></td>
	<td>产品单价：<span class="red bold">*</span></td>
	<td>{$currency}<input type="text" style="width:250px" name="price" value="{$quoting['price']}"></td>
</tr>
<tr class="even">
	<td>包装方式：</td>
	<td>{$packing} 运送方式：{$shipmethod}</td>
	<td>交货地点：</td>
	<td>{$port}</td>
</tr>
<tr class="even">
	<td>支付条款：</td>
	<td>{$paymentterm}</td>
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
		$this->kclass->page['title'] .= ' - '.$quoting['product'].' - 修改产品报价';
		$this->kclass->page['onload'] .= 'dc.tabhover()';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - <a href="/s.php?module=product&action=view&productid='.$product['productid'].'">'.$quoting['product'].'</a> - 修改产品报价','right' => '<a href="/s.php?module=product">返回列表</a>','body'=>$body));
	}
	//
	function killQuoting(){
		if($this->kclass->input['quotingid']<=0){
			$this->kclass->boinkIt('/s.php?module=product');
		}
		if($this->kclass->input['confirm'] == 0){
			$this->kclass->boinkIt('/s.php?module=product&action=view&productid='.$this->kclass->input['productid']);
		}
		if($this->kclass->input['quotingid'] < 0){
			$e = '<li>请从正确的页面进行操作，如果此问题持续存在，请联系网站管理员。</li>';
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '删除产品报价单',
				'text' => '您在删除产品报价单的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$quoting = $this->kclass->DB->queryFirst("
			SELECT quoting.quotingid,quoting.productid,
				product.title AS product
			FROM productquoting AS quoting
			LEFT JOIN product ON (product.productid=quoting.productid)
			WHERE quoting.quotingid='".$this->kclass->input['quotingid']."'
		");
		if($quoting){
			$this->kclass->DB->query("
				UPDATE `productquoting`
				SET killed=".TIMENOW."
				WHERE quotingid='".$quoting['quotingid']."'
			");
			$this->kclass->messager(array(
				'title' => '删除产品报价单成功',
				'text' => '产品 <b>'.$quoting['product'].'</b> 下的 报价单 已成功被标记为删除',
				'url' => '/s.php?module=product&action=view&productid='.$quoting['productid'],
				'sec' => 2
			));
		}else{
			$this->kclass->messager(array(
				'title' => '删除产品报价单失败',
				'text' => '您要删除的产品报价单，不存在！',
				'url' => '/s.php?module=product&action=view&productid='.$quoting['productid'],
				'sec' => 3
			));
		}
	}

	//
	function removeQuoting(){
		if($this->kclass->input['quotingid']<=0){
			$this->kclass->boinkIt('/s.php?module=product');
		}
		$quoting = $this->kclass->DB->queryFirst("
			SELECT quoting.quotingid,quoting.productid,
				product.title AS product
			FROM productquoting AS quoting
			LEFT JOIN product ON (product.productid=quoting.productid)
			WHERE quoting.quotingid='".$this->kclass->input['quotingid']."'
		");
		if($quoting){
$body = <<<EOF
<form action="/s.php?module=product&action=killQuoting" name="product" method="post">
<input type="hidden" name="module" value="product">
<input type="hidden" name="action" value="killQuoting">
<input type="hidden" name="productid" value="{$quoting['productid']}">
<input type="hidden" name="quotingid" value="{$quoting['quotingid']}">
<table><thead>
<thead>
<tr>
	<th>删除产品：{$quoting['product']}</th>
</tr>
</thead>
<tbody>
<tr class="even">
	<td class="middle">你确定要删除产品 <a href="/s.php?module=product&action=view&productid={$quoting['productid']}" class="middle bold" target="_blank">{$quoting['product']}</a> 的 <span class="big bold">报价单</span> 吗?</td>
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
		$this->kclass->page['title'] .= ' - '.$quoting['product'].' - 删除产品报价';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - <a href="/s.php?module=product&action=view&productid='.$product['productid'].'">'.$quoting['product'].'</a> - 删除产品报价','right' => '<a href="/s.php?module=product">返回列表</a>','body'=>$body));
	}

	//
	function revivalQuoting(){
		if($this->kclass->input['quotingid']<=0){
			$this->kclass->boinkIt('/s.php?module=product');
		}
		if($this->kclass->input['confirm'] == 0){
			$this->kclass->boinkIt('/s.php?module=product&action=view&productid='.$this->kclass->input['productid']);
		}
		if($this->kclass->input['quotingid'] < 0){
			$e = '<li>请从正确的页面进行操作，如果此问题持续存在，请联系网站管理员。</li>';
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '恢复产品报价单',
				'text' => '您在恢复产品报价单的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$quoting = $this->kclass->DB->queryFirst("
			SELECT quoting.quotingid,quoting.productid,
				product.title AS product
			FROM productquoting AS quoting
			LEFT JOIN product ON (product.productid=quoting.productid)
			WHERE quoting.quotingid='".$this->kclass->input['quotingid']."'
		");
		if($quoting){
			$this->kclass->DB->query("
				UPDATE `productquoting`
				SET killed=0
				WHERE quotingid='".$quoting['quotingid']."'
			");
			$this->kclass->messager(array(
				'title' => '恢复产品报价单成功',
				'text' => '产品 <b>'.$quoting['product'].'</b> 下的 报价单 已成功被标记为恢复!',
				'url' => '/s.php?module=product&action=view&productid='.$quoting['productid'],
				'sec' => 2
			));
		}else{
			$this->kclass->messager(array(
				'title' => '恢复产品报价单失败',
				'text' => '您要恢复的产品报价单，不存在！',
				'url' => '/s.php?module=product&action=view&productid='.$quoting['productid'],
				'sec' => 3
			));
		}
	}

	//
	function restoreQuoting(){
		if($this->kclass->input['quotingid']<=0){
			$this->kclass->boinkIt('/s.php?module=product');
		}
		$quoting = $this->kclass->DB->queryFirst("
			SELECT quoting.quotingid,quoting.productid,
				product.title AS product
			FROM productquoting AS quoting
			LEFT JOIN product ON (product.productid=quoting.productid)
			WHERE quoting.quotingid='".$this->kclass->input['quotingid']."'
		");
		if($quoting){
$body = <<<EOF
<form action="/s.php?module=product&action=revivalQuoting" name="product" method="post">
<input type="hidden" name="module" value="product">
<input type="hidden" name="action" value="revivalQuoting">
<input type="hidden" name="productid" value="{$quoting['productid']}">
<input type="hidden" name="quotingid" value="{$quoting['quotingid']}">
<table><thead>
<thead>
<tr>
	<th>恢复产品：{$quoting['product']}</th>
</tr>
</thead>
<tbody>
<tr class="even">
	<td class="middle">你确定要恢复产品 <a href="/s.php?module=product&action=view&productid={$quoting['productid']}" class="middle bold" target="_blank">{$quoting['product']}</a> 的 <span class="big bold">报价单</span> 吗?</td>
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
		$this->kclass->page['title'] .= ' - '.$quoting['product'].' - 恢复产品报价';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - <a href="/s.php?module=product&action=view&productid='.$product['productid'].'">'.$quoting['product'].'</a> - 恢复产品报价', 'right' => '<a href="/s.php?module=product">返回列表</a>', 'body'=>$body));
	}
	//
	function doItemOrder(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e='<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '新建产品条目',
				'text' => '您在新建产品条目的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$j=count($this->kclass->input['ordering']);
		for($i=0;$i<$j;$i++){
			$this->kclass->DB->query("UPDATE productitem SET ordering='".$this->kclass->input['ordering'][$i]."' WHERE productitemid='".$this->kclass->input['itemId'][$i]."'");
		}
		$this->kclass->messager(array(
			'title' => '产品条目排序',
			'text' => '产品条目 已排序成功!',
			'url' => '/s.php?module=product&action=view&productid='.$this->kclass->input['productid'],
			'sec' => 3
		));
	}

	function updateItem(){
		if($this->kclass->input['productid']>0){
			$product=$this->kclass->DB->queryFirst("SELECT title,materialid FROM product WHERE killed=0 AND productid='".$this->kclass->input['productid']."' LIMIT 0,1");
			$k=0;
			$items=$this->kclass->DB->query("SELECT * FROM productitem WHERE killed=0 AND productid='".$this->kclass->input['productid']."'");
			if($this->kclass->DB->numRows()){
				while($item=$this->kclass->DB->fetchArray($items)){
					$itemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').' small">
						<td>'.$k.'</td>
						<input type="hidden" name="itemId['.$k.']" value="'.$item['productitemid'].'" />
						<td><input type="text" name="itemno['.$k.']" value="'.$item['itemno'].'" size=10 /></td>
						<td>'.$this->_chooserAttribute(array('name'=>'itemValueid'.$k,'productid'=>$this->kclass->input['productid'],'value'=>$item['attributevalue'])).'</td>
						<td><input type="text" title="数值越大越排在前面" name="ordering['.$k.']" value="'.$item['ordering'].'" size="6"></td>
						<td><input type="text" name="perGrossWeight['.$k.']" value="'.$item['perGrossWeight'].'" size="9"></td>
						<td>'.$this->kclass->chooserPackingsize(array('name'=>'packingsizeid['.$k.']','width'=>'160','selectedid'=>$item['packingsizeid'])).'</td>
						<td>'.$this->kclass->chooserPackingsize(array('name'=>'packingsizeid1['.$k.']','hasBlank'=>'1','width'=>'160','selectedid'=>$item['packingsizeid1'])).'</td>
						<td><input type="checkbox" name="itemKill['.$k.']" value="'.$item['itemid'].'" /></td>
					</tr>';
					$k++;
				}
				$itemtr.='<tr><td colspan=9>新增产品条目</td></tr>';
			}
			for($i=$k;$i<$k+5;$i++){
				$itemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').' small">
					<td>'.$i.'</td>
					<td><input type="text" name="itemno['.$i.']" value="'.$item['itemno'].'" size=10 /></td>
					<td>'.$this->_chooserAttribute(array('name'=>'itemValueid'.$i,'productid'=>$this->kclass->input['productid'])).'</td>
					<td><input type="text" title="数值越大越排在前面" name="ordering['.$i.']" value="1" size="6"></td>
					<td><input type="text" name="perGrossWeight['.$i.']" value="" size="9"></td>
					<td>'.$this->kclass->chooserPackingsize(array('name'=>'packingsizeid['.$i.']','width'=>'160')).'</td>
					<td>'.$this->kclass->chooserPackingsize(array('name'=>'packingsizeid1['.$i.']','hasBlank'=>'1','width'=>'160')).'</td>
					<td></td>
				</tr>';
			}
$body = <<<EOF
<form action="/s.php?module=product&action=doupdateItem" name="product" method="post">
<input type="hidden" name="module" value="product" />
<input type="hidden" name="action" value="doupdateItem" />
<input type="hidden" name="productid" value="{$this->kclass->input['productid']}" />
<div class="title">{$product['title']}</div>
<table style="width:100%;">
<thead><tr><th colspan="9">添加产品 {$product['title']} 条目信息</th></tr></thead>
<tbody>
<tr class="odd center">
	<td width="15">ID</td>
	<td width="60">明细编号</td>
	<td>产品属性<span class="red bold">*</span></td>
	<td width="60" title="数值越大越排在前面">排序</td>
	<td width="90">每箱毛重(KG)</td>
	<td width="160">产品主包装规格</td>
	<td width="160">产品副包装规格</td>
	<td width="15">删</td>
</tr>
{$itemtr}
<tr class="even">
<td class="small gray" colspan="9">注意事项：确认信息正确无误</td>
</tr>
<tr class="even" nohover>
<td class="center" colspan=9><input type="submit" id="submitButton" value="  提交  "><input type="reset" value="  复位  "/></td>
</tr>
</tbody>
</table>
</form>
EOF;
		}
		$this->kclass->page['title'] .= ' - 新建产品条目';
		$this->kclass->page['onload'] .= 'dc.tabhover()';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 新建产品条目','right' => '<a href="'.REFERER.'">返回</a>','body'=>$body));
	}

	function doupdateItem(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e='<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员：<</li>';
		}else{
			$hasItem=0;
			$j=count($this->kclass->input['ordering']);
			$product=$this->kclass->DB->queryFirst("SELECT productid,materialid FROM product WHERE killed=0 AND productid='".$this->kclass->input['productid']."'");
			for($i=0;$i<$j;$i++){
				if($this->kclass->input['itemKill'][$i]=='' AND is_array($this->kclass->input['itemValueid'.$i]) AND !in_array(0,$this->kclass->input['itemValueid'.$i])){
					sort($this->kclass->input['itemValueid'.$i]);
					$value=implode(',',$this->kclass->input['itemValueid'.$i]);
					if($this->kclass->input['itemId'][$i]==''){
						$item=$this->kclass->DB->queryFirst("SELECT COUNT(productitemid) AS count	FROM `productitem` WHERE productid='".$this->kclass->input['productid']."' AND `attributevalue`='".$value."' LIMIT 0,1");
						if($item['count']>0){
							$e='<li>您要新建的产品明细已经存在：</li>';
						}
					}
					$hasItem=1;
				}
			}
			if($hasItem==0){
				$e='<li>请填写完整的产品明细</li>';
			}
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '新建或者修改产品明细',
				'text' => '您在新建或者修改产品条目的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}

		for($m=0;$m<$j;$m++){
			$packingsizeid=$this->kclass->iif($this->kclass->input['packingsizeid'][$m]>0,$this->kclass->input['packingsizeid'][$m],0);
			$packingsizeid1=$this->kclass->iif($this->kclass->input['packingsizeid1'][$m]>0,$this->kclass->input['packingsizeid1'][$m],0);
			if($this->kclass->input['itemId'][$m]>0){
				if($this->kclass->input['itemKill'][$m]!='' AND $this->kclass->input['itemKill'][$m]==$this->kclass->input['itemId'][$m]){
				$this->kclass->DB->query("UPDATE productitem SET killed='".TIMENOW."',killer='".$this->kclass->user['userid']."' WHERE productitemid='".$this->kclass->input['itemId'][$m]."'");
				}else{
					if(is_array($this->kclass->input['itemValueid'.$m]) AND !in_array(0,$this->kclass->input['itemValueid'.$m])){
						sort($this->kclass->input['itemValueid'.$m]);
						$value=implode(',',$this->kclass->input['itemValueid'.$m]);
						$this->kclass->DB->query("
							UPDATE productitem SET
								itemno='".$this->kclass->input['itemno'][$m]."',
								attributevalue='".$value."',
								ordering='".$this->kclass->input['ordering'][$m]."',
								perGrossWeight='".$this->kclass->input['perGrossWeight'][$m]."',
								packingsizeid='".$packingsizeid."',
								packingsizeid1='".$packingsizeid1."',
								modifier='".$this->kclass->user['userid']."',
								modified='".TIMENOW."'
							WHERE productitemid='".$this->kclass->input['itemId'][$m]."'
						");
					}
				}
			}else{
				if(is_array($this->kclass->input['itemValueid'.$m]) AND !in_array(0,$this->kclass->input['itemValueid'.$m])){
					sort($this->kclass->input['itemValueid'.$m]);
					$value=implode(',',$this->kclass->input['itemValueid'.$m]);
					$this->kclass->DB->query("
						INSERT INTO productitem
							(productid,itemno,attributevalue,ordering,perGrossWeight,packingsizeid,packingsizeid1,created,creator)
						VALUES
							(".$this->kclass->input['productid'].",'".$this->kclass->input['itemno'][$m]."','".$value."','".$this->kclass->input['ordering'][$m]."','".$this->kclass->iif($this->kclass->input['perGrossWeight'][$m]>0,$this->kclass->input['perGrossWeight'][$m],0)."','".$packingsizeid."','".$packingsizeid1."','".TIMENOW."','".$this->kclass->user['userid']."')
					");
				}
			}
		}
		$this->kclass->messager(array(
			'title' => '新建或者修改产品明细',
			'text' => '产品明细 已新建或修改成功',
			'url' => '/s.php?module=product&action=view&productid='.$this->kclass->input['productid'],
			'sec' => 3
		));
	}

	/***************************** private function ***************************************/
	//获取产品属性信息
	function _chooserAttribute($b){
		if($b['productid']<0){
			return false;
		}
		//获取产品属性信息
		$names=$this->kclass->DB->query("
			SELECT nameid,title
			FROM  attributename
			WHERE killed=0 AND module='product' AND mid='".$b['productid']."'
			ORDER BY nameid ASC
		");
		$j=0;
		$r='';
		if($this->kclass->DB->numRows($names)){
			while($name=$this->kclass->DB->fetchArray($names)){
				$values=$this->kclass->DB->query("
					SELECT valueid,title,`default`
					FROM attributevalue
					WHERE killed=0 AND nameid='".$name['nameid']."'
					ORDER BY valueid ASC
				");
				if($this->kclass->DB->numRows($values)){
					$r.='<select name="'.$b['name'].'['.$j.']" style="width:'.$b['width'].'"><option value="0">'.$name['title'].'</option>';
					while($value=$this->kclass->DB->fetchArray($values)){
						$valueid=explode(',',$b['value']);
						$r.='<option value="'.$value['valueid'].'" ';
						if(in_array($value['valueid'],$valueid)){
							$r.='selected';
						}
						$r.=' >'.$value['title'].'</option>';
					}
					$r.='</select>';
				}
				$j++;
			}
		}
		return $r;
	}
}
?>
