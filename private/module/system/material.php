	<?php
  //
class material{
	var $kclass;
	//
	function autorun(){
		$this->baseurl='<a href="/s.php?">首页</a>';
		switch($this->kclass->input['action']){
			case 'list':
				return $this->mmlist();
				break;
			case 'view':
				return $this->view();
				break;
			case 'find':
				return $this->find();
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
			case 'remove':
				return $this->remove();
				break;
			case 'kill':
				return $this->kill();
				break;
			case 'revival':
				return $this->revival();
				break;
			case 'restore':
				return $this->restore();
				break;
			case 'updateVersion':
				return $this->updateVersion();
				break;
			case 'doupdateVersion':
				return $this->doupdateVersion();
				break;
			case 'updateStock':
				return $this->updateStock();
				break;
			case 'doupdateStock':
				return $this->doupdateStock();
				break;
			case 'updateItem':
				return $this->updateItem();
				break;
			case 'doupdateItem':
				return $this->doupdateItem();
				break;
			case 'updateItemBelong':
				return $this->updateItemBelong();
				break;
			case 'doupdateItemBelong':
				return $this->doupdateItemBelong();
				break;
			case 'updateItemVersion':
				return $this->updateItemVersion();
				break;
			case 'doupdateItemVersion':
				return $this->doupdateItemVersion();
				break;
			case 'updatePacking':
				return $this->updatePacking();
				break;
			case 'doupdatePacking':
				return $this->doupdatePacking();
				break;
			case 'updatePrice': // 修改价格
				return $this->updatePrice();
				break;
			case 'doupdatePrice':
				return $this->doupdatePrice();
				break;
			case 'viewPrice': // 查看历史价格
				return $this->viewPrice();
				break;
			case 'updatePriceByBom': // 手动更新价格
				return $this->updatePriceByBom();
				break;
			case 'updateAllPrice': // 一键修改所有半成品
				return $this->updateAllPrice();
				break;
			case 'updatePriceStatus': // 一键修改所有半成品
				return $this->updatePriceStatus();
				break;
			case 'doupdatePriceStatus': // 一键修改所有半成品
				return $this->doupdatePriceStatus();
				break;
			case 'updateEntrust': // 新增外发加工内容
				return $this->updateEntrust();
				break;
			case 'doupdateEntrust': // 新增外发加工内容
				return $this->doupdateEntrust();
				break;
			case 'import':
				return $this->import();
				break;
			case 'doimport':
				return $this->doimport();
				break;
			case 'listOriginal':
				return $this->listOriginal();
				break;
			case 'exchange':
				return $this->exchange();
				break;
			case 'importStock':
				return $this->importStock();
				break;
			case 'doimportStock':
				return $this->doimportStock();
				break;
			case 'listOriginalStock':
				return $this->listOriginalStock();
				break;
			case 'exchangeStock':
				return $this->exchangeStock();
				break;
			case 'importPrice':
				return $this->importPrice();
				break;
			case 'doimportPrice':
				return $this->doimportPrice();
				break;
			case 'listOriginalPrice':
				return $this->listOriginalPrice();
				break;
			case 'exchangePrice':
				return $this->exchangePrice();
				break;
			case 'exchangeItem':
				return $this->exchangeItem();
				break;
			case 'updateVersionMaterialid':
				return $this->updateVersionMaterialid();
				break;
			case 'stopMaterial':
				return $this->stopMaterial();
				break;
			default:
				return $this->mmlist();

		}
	}
	// main page,shows all the material info list
	function mmlist(){
		$id=array(0);
		$condition=$this->kclass->iif($this->kclass->input['show']=='all','1=1',' `material`.killed=0');
		if($this->kclass->input['materialno']!=''){
			$nos=explode(';',$this->kclass->input['materialno']);
			if(is_array($nos) AND count($nos)>1){
				foreach($nos AS $no){
					$find=$this->kclass->DB->queryFirst("SELECT materialid FROM material WHERE materialno='{$no}'");
					if(!$find['materialid']){
						$id[]=0;
					}else{
						$id[]=$find['materialid'];
					}
				}
				$condition.=' AND materialid IN ('.implode(',',$id).')';
			}elseif(count($nos)==1){
				if($this->kclass->input['type']==1){
					$finds=$this->kclass->DB->query("SELECT materialid FROM material WHERE materialno LIKE '%{$this->kclass->input['materialno']}%'");
					if($this->kclass->DB->numRows()){
						while($find=$this->kclass->DB->fetchArray($finds)){
							if(!$find['materialid']){
								$id[]=0;
							}else{
								$id[]=$find['materialid'];
							}
						}
					}
					$condition.=' AND materialid IN ('.implode(',',$id).')';
				}elseif($this->kclass->input['type']==0){
					$find=$this->kclass->DB->queryFirst("SELECT materialid FROM material WHERE materialno='{$this->kclass->input['materialno']}'");
					$this->kclass->boinkIt('/s.php?module=material&action=view&materialid='.$find['materialid']);
				}
			}
			$perpage=2000;
		}elseif($this->kclass->input['title']!=''){
			$finds=$this->kclass->DB->query("SELECT materialid FROM material WHERE title LIKE '%{$this->kclass->input['title']}%'");
			if($this->kclass->DB->numRows()){
				while($find=$this->kclass->DB->fetchArray($finds)){
					if(!$find['materialid']){
						$id[]=0;
					}else{
						$id[]=$find['materialid'];
					}
				}
			}
			$perpage=2000;
			$condition.=' AND materialid IN ('.implode(',',$id).')';
		}elseif($this->kclass->input['isUsed']==-1 OR $this->kclass->input['isUsed']==1 OR $this->kclass->input['originid']>0 OR $this->kclass->input['categoryid']>0){
			$c='';
			if($this->kclass->input['isUsed']==-1 OR $this->kclass->input['isUsed']==1){
				$condition.=' AND  `material`.isUsed='.$this->kclass->input['isUsed'];
				$c.=' AND  `material`.isUsed='.$this->kclass->input['isUsed'];
			}
			if($this->kclass->input['originid']>0){
				$condition.=' AND  `material`.originid='.$this->kclass->input['originid'];
				$c.=' AND  `material`.originid='.$this->kclass->input['originid'];
			}
			if($this->kclass->input['categoryid']>0){
				$category=$this->kclass->DB->queryFirst("SELECT childlist FROM category WHERE categoryid={$this->kclass->input['categoryid']}");
				$c.=' AND categoryid IN ('.$category['childlist'].')';
			}
			if($c){
				$finds=$this->kclass->DB->query("SELECT materialid FROM material WHERE 1=1 {$c}");
				if($this->kclass->DB->numRows()){
					while($find=$this->kclass->DB->fetchArray($finds)){
						if(!$find['materialid']){
							$id[]=0;
						}else{
							$id[]=$find['materialid'];
						}
					}
				}
				$perpage=2000;
				$condition.=' AND `material`.materialid IN ('.implode(',',$id).')';
			}
			/*if($this->kclass->input['categoryid']>0){
				if($this->kclass->input['originid']>0){
					$c=' AND originid='.$this->kclass->input['originid'];
				}
				$category=$this->kclass->DB->queryFirst("SELECT childlist FROM category WHERE categoryid={$this->kclass->input['categoryid']}");
				$finds=$this->kclass->DB->query("SELECT materialid FROM material WHERE categoryid IN ({$category['childlist']}) {$c}");
				if($this->kclass->DB->numRows()){
					while($find=$this->kclass->DB->fetchArray($finds)){
						if(!$find['materialid']){
							$id[]=0;
						}else{
							$id[]=$find['materialid'];
						}
					}
				}
				$condition.=' AND materialid IN ('.implode(',',$id).')';
			}elseif($this->kclass->input['originid']>0){
				if($this->kclass->input['categoryid']>0){
					$category=$this->kclass->DB->queryFirst("SELECT childlist FROM category WHERE categoryid={$this->kclass->input['categoryid']}");
					$c=' AND categoryid IN ('.$category['childlist'].')';
				}
				$finds=$this->kclass->DB->query("SELECT materialid FROM material WHERE originid={$this->kclass->input['originid']} {$c}");
				if($this->kclass->DB->numRows()){
					while($find=$this->kclass->DB->fetchArray($finds)){
						if(!$find['materialid']){
							$id[]=0;
						}else{
							$id[]=$find['materialid'];
						}
					}
				}
				$condition.=' AND materialid IN ('.implode(',',$id).')';
			}*/
		}elseif($this->kclass->input['standard']!=''){
			$finds=$this->kclass->DB->query("SELECT materialid FROM material WHERE standard LIKE '%{$this->kclass->input['standard']}%'");
			if($this->kclass->DB->numRows()){
				while($find=$this->kclass->DB->fetchArray($finds)){
					if(!$find['materialid']){
						$id[]=0;
					}else{
						$id[]=$find['materialid'];
					}
				}
			}
			$perpage=500;
			$condition.=' AND materialid IN ('.implode(',',$id).')';
		}
		if($this->kclass->input['orderby']!='')$query['orderby']=$this->kclass->input['orderby'];
		if($this->kclass->input['direction']!='')$query['direction']=$this->kclass->input['direction'];
		if($this->kclass->input['show']!='')$query['show']=$this->kclass->input['show'];
		if($this->kclass->input['layout']!='')$query['layout']=$this->kclass->input['layout'];
		$orderby=$this->kclass->orderby(array('module'=>'material','direction'=>'asc','orderby'=>'modified','default'=>'materialno','serial'=>array(array('title'=>'名称','field'=>'title','word'=>'name'),array('title'=>'编号','field'=>'materialno','word'=>'no'),array('title'=>'修改时间','field'=>'modified'),array('title'=>'建立时间','field'=>'created')),'appendUrl'=>$query));
		if(is_array($query) AND count($query)>0)$queryPart='&'.http_build_query($query);
		if($perpage=='')$perpage=500;
		$pcount=$this->kclass->DB->queryFirst("SELECT COUNT(materialid) AS count FROM material WHERE killed=0");
		$list=$this->kclass->listMaterial(array('perpage'=>$perpage,'find'=>$find,'condition'=>$condition,'orderby'=>$query['orderby'],'direction'=>$query['direction'],'show'=>$query['show'],'layout'=>$query['layout']));
		if($query['layout']=='grid'){
			$materialList.='<ul id="tbody" class="mmlist clear">';
		}else{
			$materialList.='<table class="hundred"><thead><tr><th width="30">ID</th><th width="60">物资编号</th><th width="250">名称</th><th width="510">规格</th><th width="80">单价</th><th width="60">通用物料</th><th width="62">总库存数</th><th width="60">良品数</th><th width="60">不良品数</th><th width="50">最小值</th><th width="40">来源</th><th width="50">单位</th><td width="80" align="center"><b>编辑</b></td><th width="40">停用</th></tr></thead><tbody id="tbody"><tr nohover id="listtr" class="bold over" style="display:none"><td width="30">ID</td><td width="60">物资编号</td><td width="250">名称</td><td width="510">规格</td><td width="80">单价</td><td width="60">通用物料</td><td width="64">总库存数</td><td width="62">良品数</td><td width="62">不良品数</td><td width="52">最小值</td><td width="42">来源</td><td width="52">单位</td><td width="80">编辑</td><td width="40">停用</td></tr>';
		}
		$materialList.=$list['body'];
		//echo $list['body'];
		if($pcount['count']>$list['counter']){
			$materialList.='<div class="page"><a href="javascript:dc.tableItem.page('.$perpage.')">点击查看更多</a></div>';
		}
		$materialList.='<div id="clear"></div>';
		if($this->kclass->input['layout']=='grid'){
			$layoutLink='<a href="/s.php?module=material&action=list&layout=list'.str_replace('&layout=grid','',$queryPart).'">列表</a> 格子';
		}else{
			$layoutLink='列表 <a href="/s.php?module=material&action=list&layout=grid'.str_replace('&layout=list','',$queryPart).'">格子</a>';
		}
		if($this->kclass->input['show']=='all'){
			$showLink='<a href="/s.php?module=material&action=list'.str_replace('&show=all','',$queryPart).'">默认</a> 所有<span class="small gray">(包括删除)</span>';
		}else{
			$showLink='默认 <a href="/s.php?module=material&action=list&show=all'.str_replace('&show=all','',$queryPart).'">所有<span class="small gray">(包括删除)</span></a>';
		}
		// 产成品及包成品物资
		$filterItem=$this->kclass->filterItem(array('module'=>'material'));
		$filter=$this->kclass->filter(
			array('module'=>'material','action'=>'list','table'=>'material','serial'=>array(
			array('name'=>'materialid','dname'=>'包　成　品','status'=>$filterItem['bmaterial']),
			array('name'=>'materialid','dname'=>'产　成　品','status'=>$filterItem['cmaterial'])
		)));
		$materialItems=$this->kclass->DB->query("
			SELECT mi.itemid,mi.materialid,mi.relatedid,mi.itemno,mi.quantity,mi.qualified,mi.disqualified,
				material.materialno,material.unitid
			FROM materialitem AS mi
			LEFT JOIN material ON (material.materialid=mi.materialid)
			WHERE mi.killed=0 AND material.killed=0 {$filter['link']}
			ORDER BY material.materialno ASC,mi.itemno ASC
		");
		if($this->kclass->DB->numRows()){
			$i=1;
			$itemList.='<table class="hundred"><thead><tr><th width="15">ID</th><th width="50">父编码</th><th width="50">子编码</th><th width="160">对应包成品或产成品编码</th><th width="150">名称</th><th>属性</th><th width="100">单价</th><th width="50">良品</th><th width="50">不良品</th><th width="160">对应包成品或产成品库存</th></tr></thead><tbody>';
			while($materialItem=$this->kclass->DB->fetchArray($materialItems)){
				$itemVersion=$this->kclass->DB->queryFirst("SELECT price,priceStatus FROM materialversion WHERE itemid={$materialItem['itemid']}");
				if($materialItem['relatedid']>0){
					$related=$this->kclass->DB->queryFirst("SELECT itemid,materialid,itemno,quantity,qualified,disqualified FROM materialitem WHERE itemid=".$materialItem['relatedid']." LIMIT 0,1");
				}else{
					$related=$this->kclass->DB->queryFirst("SELECT itemid,materialid,itemno,quantity,qualified,disqualified FROM materialitem WHERE relatedid=".$materialItem['itemid']." LIMIT 0,1");
				}
				$unit=$this->kclass->DB->queryFirst("SELECT title AS unit FROM unit WHERE unitid='".$materialItem['unitid']."'");
				$materialInfo=$this->kclass->getMaterial(array('itemid'=>$materialItem['itemid'],'materialid'=>$materialItem['materialid']));
				if($itemVersion['price']==0){
					$class='black';
				}else{
					$class='red';
				}
				$itemList.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').' small">
					<td>'.$i.'</td>
					<td><a href="/s.php?module=material&action=view&materialid='.$materialItem['materialid'].'">'.$materialItem['materialno'].'</a></td>
					<td><a href="'.$materialInfo['url'].'">'.$materialItem['itemno'].'</a></td>
					<td class="center"><a href="/s.php?module=material&action=view&materialid='.$related['materialid'].'&itemid='.$related['itemid'].'">'.$related['itemno'].'</a></td>
					<td>'.$materialInfo['material'].'</td>
					<td>'.$materialInfo['standard'].'</td>
					<td align="right" class="bold normal '.$class.'">'.$this->kclass->iif($this->kclass->purviews(array('module'=>'material','action'=>'viewPrice','final'=>1)),$itemVersion['price'].' '.$this->kclass->priceStatus($itemVersion['priceStatus']),'—').'</span></td>
					<td class="bold normal" align="right">'.$materialItem['qualified'].$unit['title'].'</td>
					<td class="bold normal" align="right">'.$materialItem['disqualified'].$unit['title'].'</td>
					<td class="center bold gray normal">'.$related['quantity'].$unit['title'].'</td>
				</tr>';
				$i++;
			}
			$itemList.='</tbody></table>';
		}
		// 分类筛选
		$categorys=$this->kclass->DB->query("SELECT categoryid,title FROM category WHERE parentid='".$this->kclass->iif($this->kclass->input['categoryid']>0,$this->kclass->input['categoryid'],19)."' ORDER BY categoryid ASC");
		if($this->kclass->DB->numRows()){
			while($category=$this->kclass->DB->fetchArray($categorys)){
				$categoryList.='<a href="/s.php?module=material&action=list&originid='.$this->kclass->input['originid'].'&categoryid='.$category['categoryid'].'">'.$category['title'].'</a>　';
			}
		}
		$parentCategory=$this->kclass->DB->queryFirst("SELECT parentid FROM category WHERE categoryid='".$this->kclass->iif($this->kclass->input['categoryid']>0,$this->kclass->input['categoryid'],19)."' LIMIT 0,1");
		$parentCategoryid=$this->kclass->iif($parentCategory['parentid']>0,$parentCategory['parentid'],19);
		// 来源筛选
		$origins=$this->kclass->DB->query("SELECT originid,title FROM materialorigin ORDER BY originid ASC");
		if($this->kclass->DB->numRows()){
			while($origin=$this->kclass->DB->fetchArray($origins)){
				$orginList.='<a href="/s.php?module=material&action=list&isUsed='.$this->kclass->input['isUsed'].'&originid='.$origin['originid'].'&categoryid='.$this->kclass->input['categoryid'].'">'.$origin['title'].'</a>　';
			}
		}
		//echo $materialList;
		$body=<<<EOF
<dl id="material" class="tabs" style="display: block;">
<dt tabid="0">总编号</dt>
<dt title="1">产品物资</dt>
<dd>
<form action="/s.php?module=material&action=list" method="get">
<input type="hidden" name="module" value="material" >
<input type="hidden" name="action" value="list" >
<table class="hundred">
<thead><tr><th colspan=3>查询【<a href="/s.php?module=material">全部</a>】<span class="small gray">（注意：多个编号请用英文分号(;)分隔开，搜索时只能选择三种中的一种进行搜索）</span></th></tr></thead>
<tbody>
<tr><td>是否使用：</td><td colspan=2><a href="/s.php?module=material&action=list&isUsed=1&originid={$this->kclass->input['originid']}&categoryid={$this->kclass->input['categoryid']}">是</a>　<a href="/s.php?module=material&action=list&isUsed=-1&originid={$this->kclass->input['originid']}&categoryid={$this->kclass->input['categoryid']}">否</a></tr>
<tr><td>来　　　源：</td><td colspan=2>{$orginList}</td></tr>
<tr><td>分　　　类：</td><td colspan=2>【<a href="/s.php?module=material&action=list&isUsed={$this->kclass->input['isUsed']}&originid={$this->kclass->input['originid']}&categoryid={$parentCategoryid}">上一级</a>】　{$categoryList}</td></tr>
<tr><td>编　　　号：</td><td><input type="text" name="materialno" size="160" value="{$this->kclass->input['materialno']}"><input type="radio" name="type" value=0 >精确查找 <input type="radio" name="type" value=1 checked>模糊查找</td><td><input type="submit" value="查询" ></td></tr>
<tr><td>名　　　称：</td><td><input type="text" name="title" size="160" value="{$this->kclass->input['title']}"><td><input type="submit" value="查询" ></td></tr>
<tr><td>规      格：</td><td><input type="text" name="standard" size="160" value="{$this->kclass->input['standard']}"><td><input type="submit" value="查询" ></td></tr>
</tbody>
</table>
</form>
{$materialList}</dd>
<dd>{$filter['panel']}
{$itemList}</dd>
EOF;
		$this->kclass->page['title'].='物资列表';
		$this->kclass->page['onload'].='dc.listhover();dc.tabs({\'id\':\'material\'});dc.tabhover();dc.flow(\'listtr\')';
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - 物资列表','right' => '<span class="small">排序：</span>'.$orderby['link'].'|　<span class="small">显示方式：</span>'.$showLink.'　|　<span class="small">布局：</span>'.$layoutLink.'　|　<a href="/s.php?module=material&action=add">新建</a>　|　<span class="small">批量导入：</span><a href="/s.php?module=material&action=import">物资</a>　<a href="/s.php?module=material&action=importStock">库存</a>　|　<span class="small">打印：</span><a href="/p.php?action=material">物资列表</a>　|　<span class="small">其他功能：</span><a href="/r.php?action=report&action=bomCalculate">BOM计算器</a>　<a href="/r.php?action=report&action=showHistoryInOut">历史物资收发料详细</a>','body'=>$body));
	}	//
	function find(){
		$this->kclass->boinkIt('/s.php?module=material');
		$category=$this->kclass->chooserCategory(array('name'=>'categoryid','width'=>250,'parentid'=>19,'hasBlank'=>1,'topname'=>' '));
		$body=<<<EOF
<form action="/s.php?module=material&action=list" method="get">
<input type="hidden" name="module" value="material">
<input type="hidden" name="action" value="list">
<table style="width:450px">
<thead><tr><th colspan=2>根据条件查找物资</th></tr></thead>
<tbody>
<tr class="odd">
<td width="100">物资编号：</td><td><input type="text" name="materialno"></td>
</tr>
<tr class="even">
<td width="100">物资名称：</td><td><input type="text" name="title" style="width:300px"></td>
</tr>
<tr class="even">
<td width="100">物资规格：</td><td><input type="text" name="standard" style="width:300px"></td>
</tr>
<tr class="odd">
<td width="100">物资分类：</td><td>{$category}</td>
</tr>
<tr class="odd">
<td width="100">未盘点时间：</td><td>
<select name="unInventoryTime" style="width:300px">
<option value="0">请选择</option>
<option value="2592000">一个月以上</option>
<option value="7776000">三个月以上</option>
<option value="1555200">六个月以上</option>
</select>
</td>
</tr>
<tr class="even">
<td class="small gray" colspan="9">
注意事项：<br>
①查找采购计划单时，可以通过输入“计划单编号”直接查找，也可以通过输入“日期”和“物料编号”进行查找<br>
②请保证输入的值无误</td>
</tr>
<tr class="odd" nohover>
<td class="center" colspan=2><input type="submit" id="submitButton" value="  提交  "><input type="reset" value="  复位  "/></td>
</tr>
</tbody>
</table>
</form>
EOF;
		$this->kclass->page['title'].='查找物资';
		$this->kclass->page['onload'].='dc.listhover();$(\'#dateline\').datepicker()';
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - 查找物资','right' => '<a href="/s.php?module=material">查看列表</a>','body'=>$body));
	}
	//
	function view(){
		$material=$this->kclass->DB->queryFirst("
			SELECT m.materialid,m.materialno,m.title,m.entitle,m.standard,m.enstandard,m.attrid,m.categoryid,m.quantity,m.inventoryType,m.qualified,m.disqualified,m.uptolerance,m.belowtolerance,m.min,m.max,m.criticalNumber,m.ifPerBarcode,m.ifNeedentrust,m.entrustMaterialid,m.remark,m.killed,m.modified,m.created,
				c.parentlist,c.title AS category,
				mo.originid,mo.title AS origin,
				attr.title AS attr,
				unit.title AS unit,
				packing.title AS packing,
				brand.title AS brand,
				me.realname AS creator,
				mem.realname AS modifier
			FROM material AS m
			LEFT JOIN category AS c ON (c.categoryid=m.categoryid)
			LEFT JOIN materialorigin AS mo ON (mo.originid=m.originid)
			LEFT JOIN attr ON (attr.attrid=m.attrid)
			LEFT JOIN unit ON (unit.unitid=m.unitid)
			LEFT JOIN packing ON (packing.packingid=m.packingid)
			LEFT JOIN brand ON (brand.brandid=m.brandid)
			LEFT JOIN member AS me ON (me.userid=m.creator)
			LEFT JOIN member AS mem ON (mem.userid=m.modifier)
			WHERE m.materialid='".$this->kclass->input['materialid']."'
			LIMIT 0,1
		"); 
		if(!$material){
			$e='<li>数据错误，很抱歉</li>';
		}
		if($e){
			$this->kclass->messager(array(
				'title' => '查看物资',
				'text' => '您在查看物资的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$title=$material['materialno'].'　'.$material['title'].'　'.$material['standard'];
		if($material['attrid']==6){
			$pmaterial=$this->kclass->DB->queryfirst("
				SELECT pmaterialid,ifGeneral
				FROM pmaterial 
				WHERE materialid='".$material['materialid']."'
				LIMIT 0,1
			");
			$material=array_merge($material,$pmaterial);
		}
		if($this->kclass->input['itemid']>0){
			$item=$this->kclass->DB->queryfirst("SELECT itemid,itemno,attributevalue FROM materialitem WHERE killed=0 AND itemid=".$this->kclass->input['itemid']." LIMIT 0,1");
			if($item['attributevalue']){
				$attributes=$this->kclass->DB->query("
					SELECT av.valueid,av.title AS value,
						an.nameid,an.title AS name
					FROM attributevalue AS av
					LEFT JOIN attributename AS an ON (an.nameid=av.nameid)
					WHERE an.killed=0 AND av.killed=0 AND av.valueid IN (".$item['attributevalue'].")
					ORDER BY an.ordering DESC
				");
				if($this->kclass->DB->numRows()){
					while($attribute=$this->kclass->DB->fetchArray($attributes)){
						$value.=$attribute['value'].'　';
					}
				}
				$material['standard']=$value;
			}
			$itemid=$this->kclass->input['itemid'];
			$title=$material['materialno'].'　'.$item['itemno'].'　'.$material['title'].'　'.$material['standard'];
		}

		$categoryId=explode(',',$material['parentlist']);
		foreach($categoryId as $categoryid){//获得物资分类
			if($value!=-1){
				$category=$this->kclass->DB->queryFirst("SELECT categoryid,title FROM category WHERE categoryid='".$categoryid."' LIMIT 0,1");
				if($category['title'] AND $category['categoryid']!=$material['categoryid']){
					$material['category']=$category['title'].' '.$material['category'];
				}
			}
		}

		$perBarcode=$this->kclass->iif($material['ifPerBarcode']==1,'是','否');
		if($material['inventoryType']=='choose'){
			$invetoryType='抽盘';
		}elseif($material['inventoryType']=='must'){
			$invetoryType='必盘';
		}
		$ifGeneral=$this->kclass->iif($material['ifGeneral']==1,'是','否');
		$ifNeedentrust=$this->kclass->iif($material['ifNeedentrust']==1,'是','否');
		if($ifNeedentrust=='是'){
			$entrustMaterialid=explode(',',$material['entrustMaterialid']);
			//if(count($entrustMaterialid)>1){
				foreach($entrustMaterialid AS  $val){
					$materialno=$this->kclass->DB->queryFirst("SELECT materialno FROM material WHERE materialid='".$val."'");
					$entrustmaterial.=$materialno['materialno'].'　';
				}
		//	}
			//$materialno=$this->kclass->DB->queryFirst("SELECT materialno FROM material WHERE materialid='".$material['entrustMaterialid']."'");
			//$entrustmaterial=$materialno['materialno'];
		}else{
			$entrustmaterial='';
		}
		$attach=$this->kclass->getAttachs(array('module'=>'material','mid'=>$material['materialid']));
		if($attach!= false){
			$attachs='<div class="clear">'.$attach.'</div>';
		}
/*
		// 获得盘点信息
		$materialInventorys=$this->kclass->DB->query("
			SELECT i.inventoryid,i.title,i.inventorier,
				ii.quantity,ii.countQuantity,ii.difference,ii.dateline,ii.errorrate,ii.remark,ii.reason
			FROM inventory AS i
			LEFT JOIN inventoryitem AS ii ON (ii.inventoryid=i.inventoryid)
			WHERE i.killed=0 AND ii.materialid='".$material['materialid']."'
			ORDER BY i.dateline DESC
		");
		if($inventoryList['num']=$this->kclass->DB->numRows() AND $this->kclass->user['positionid']!=12){
			$inventoryList['panel']='<table class="hundred"><thead><tr><th colspan="8">盘点信息</th></tr></thead><tbody><tr class="center"><td>盘点日期</td><td>标题</td><td>账面数量</td><td>实际数量</td><td>误差数</td><td>误差率</td><td>备注</td><td>原因分析</td></tr>';
			while($materialInventory=$this->kclass->DB->fetchArray($materialInventorys)){
				$inventoryList['panel'].='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').'">
					<td>'.date('Y-m-d',$materialInventory['dateline']).'</td>
					<td><a href="/s.php?module=inventory&action=view&inventoryid='.$materialInventory['inventoryid'].'">'.$materialInventory['title'].'</a></td>
					<td>'.$materialInventory['countQuantity'].'</td>
					<td>'.$materialInventory['quantity'].'</td>
					<td>'.$materialInventory['difference'].'</td>
					<td>'.$materialInventory['errorrate'].'%</td>
					<td>'.$materialInventory['remark'].'</td>
					<td>'.$materialInventory['reason'].'</td></tr>';
			}
			$inventoryList['panel'].='</tbody></table>';
			$inventoryList['count']='('.$inventoryList['num'].')';
		}else{
			$inventoryList['off']=' disabled';
		}*/

		// 非产品物资版本信息
		if($material['attrid']==6){
			$versions=$this->kclass->DB->query("
				SELECT versionid,itemid,title,quantity,qualified,price,price1,disqualified,`change`,planInbound,planOutbound,ifProduce,priceStatus
				FROM materialversion
				WHERE killed=0 AND materialid='".$material['materialid']."' ".$this->kclass->iif($this->kclass->input['itemid']>0,' AND itemid='.$this->kclass->input['itemid'],'')."
				ORDER BY ifProduce DESC,created DESC
			");
		}elseif($material['attrid']==7 OR $material['attrid']==8){
			$versions=$this->kclass->DB->query("
				SELECT mv.versionid,mv.itemid,mv.title,mv.price,mv.quantity,mv.qualified,mv.disqualified,mv.`change`,mv.planInbound,mv.planOutbound,mv.ifProduce,priceStatus
				FROM materialversion AS mv
				LEFT JOIN materialitem AS mi ON (mi.itemid=mv.itemid)
				WHERE mv.killed=0 AND mi.killed=0 AND mv.materialid='".$material['materialid']."' ".$this->kclass->iif($this->kclass->input['itemid']>0,' AND mv.itemid='.$this->kclass->input['itemid'],'')."
				ORDER BY mv.ifProduce DESC,mi.itemno ASC,mv.created DESC
			");
		}
		
		$hasParent=$hasChild=0;
		if($this->kclass->DB->numRows()){
			if($material['attrid']==6){
				$mitemtr='<table class="hundred"><thead><th colspan=16>详细条目 <span class="right"><a href="/s.php?module=material&action=updateVersion&materialid='.$material['materialid'].'">修改物资版本</a></span></th></thead><tbody><tr class="center"><td width="15">ID</td><td width="60">编号</td><td>物资名称</td><td>物资名称（英文）</td><td>规格</td><td>规格（英文）</td><td width="30">版本</td><td>版本描述</td><td width="150">单价</td><td width="50">加工费</td><td>总数</td><td>良品数</td><td>不良品数</td><td>待入数</td><td>待出数</td><td width="120"></td></tr>';
			}elseif($material['attrid']==7 OR $material['attrid']==8){
				$mitemtr.='<table class="hundred"><thead><tr><th colspan=15>详细条目 <a class="right" href="/s.php?module=material&action=updateItemBelong&materialid='.$material['materialid'].'">修改物资明细归属</a></th></tr></thead><tbody><tr class="center"><td width="15">ID</td>'.$this->kclass->iif($material['attrid']==7,'<td width="50">产成品</td><td width="50">包成品</td>','<td width="50">包成品</td><td width="50">产成品</td>').'<td>名称</td><td>属性</td><td width="150">单价</td><td width="50">单位</td><td>当前库存</td><td>'.$this->kclass->iif($material['attrid']==7,'包成品库存','产成品库存').'</td><td width="30">版本</td><td>版本描述</td><td>待入数</td><td>待出数</td><td>是否生产</td><td width="180"></td></tr>';
			}

			$k=1;
			while($version=$this->kclass->DB->fetchArray($versions)){
				$versionItem=$this->kclass->getMaterial(array('materialid'=>$material['materialid'],'itemid'=>$version['itemid']));
				// 是否存在bom
				if($versionItem['originid']!=2){
					$child=$this->kclass->bomChildList(array('parentid'=>$material['materialid'],'materialitemid'=>$version['itemid'],'versionid'=>$version['versionid'],'loopNum'=>1,'getCount'=>1));
					if($child==1){
						$hasChild=1;
					}
					$bomOperate=$this->kclass->iif($child==1,'<a href="/s.php?module=bom&action=update&materialid='.$material['materialid'].'&parentVersionid='.$version['versionid'].'">修改BOM</a>','<a href="/s.php?module=bom&action=add&attrid='.$material['attrid'].'&versionid='.$version['versionid'].'">新增BOM</a>');
				}else{
					$bomOperate='';
				}
				$parent=$this->kclass->DB->queryFirst("SELECT COUNT(bomid) AS count FROM bom WHERE killed=0 AND childid='".$material['materialid']."' AND childItemid='".$version['itemid']."' AND versionid='".$version['versionid']."' ");
				if($parent['count']>0){
					$hasParent=1;
				}
				// 版本明细
				if($material['attrid']==6){
					$mitemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').' small">
						<td>'.$k.'</td>
						<td>'.$this->kclass->iif($version['itemno']=='',$material['materialno'],$version['itemno']).'</td>
						<td>'.$material['title'].'</td>
						<td>'.$material['entitle'].'</td>
						<td>'.$versionItem['standard'].'</td>
						<td>'.$versionItem['enstandard'].'</td>
						<td>'.$version['title'].'</td>
						<td>'.$version['change'].'</td>
						<td class="red bold normal">'.$this->kclass->iif($this->kclass->purviews(array('module'=>'material','action'=>'viewPrice','final'=>1)),$version['price'].' '.$this->kclass->priceStatus($version['priceStatus']).'　<a href="/s.php?module=material&action=viewPrice&materialid='.$material['materialid'].'&versionid='.$version['versionid'].'" class="small">（历史价格）</a>','—').'</td>
						<td class="red bold normal">'.$this->kclass->iif($this->kclass->purviews(array('module'=>'material','action'=>'viewPrice','final'=>1)),$version['price1'],'—').'</td>
						<td class="bold normal">'.$version['quantity'].'</td>
						<td>'.$version['qualified'].'</td>
						<td>'.$version['disqualified'].'</td>
						<td>'.$version['planInbound'].'</td>
						<td>'.$version['planOutbound'].'</td>
						<td>单价（<a href="/s.php?module=material&action=updatePrice&materialid='.$material['materialid'].'&versionid='.$version['versionid'].'&type=1">直接</a> <a href="/s.php?module=material&action=updatePrice&materialid='.$material['materialid'].'&versionid='.$version['versionid'].'&type=2">平均</a>）　'.$bomOperate.'</td>
					</tr>';
				}elseif($material['attrid']==7 OR $material['attrid']==8){
					if($versionItem['relatedid']!=0){// 产成品和包成品的关系
						$related=$this->kclass->DB->queryFirst("
							 SELECT itemid,itemno,materialid,relatedid,attributevalue,quantity,qualified,disqualified
							 FROM materialitem 
							 WHERE killed=0 AND itemid='".$versionItem['relatedid']."'
							 LIMIT 0,1
						");
					}else{
						$related=$this->kclass->DB->queryFirst("
							 SELECT itemid,itemno,materialid,relatedid,attributevalue,quantity,qualified,disqualified
							 FROM materialitem 
							 WHERE killed=0 AND relatedid='".$versionItem['itemid']."'
							 LIMIT 0,1
						");
					}
					$mitemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').$this->kclass->iif($version['ifProduce']==0,' gray','').' small">
						<td>'.$k.'</td>
						<td><a href="/s.php?module=material&action=view&materialid='.$material['materialid'].'&itemid='.$version['itemid'].'">'.$versionItem['itemno'].'</a></td>
						<td><a href="/s.php?module=material&action=view&materialid='.$related['materialid'].'&itemid='.$related['itemid'].'">'.$related['itemno'].'</a></td>
						<td>'.$material['title'].'</td>
						<td>'.$versionItem['standard'].'</td>
						<td class="red bold normal">'.$this->kclass->iif($this->kclass->purviews(array('module'=>'material','action'=>'viewPrice','final'=>1)),$version['price'].' '.$this->kclass->priceStatus($version['priceStatus']).'　<a href="/s.php?module=material&action=viewPrice&materialid='.$material['materialid'].'&versionid='.$version['versionid'].'" class="small">（历史价格）</a>','—').'</td>
						<td>'.$material['unit'].'</td>
						<td class="bold normal">'.$version['quantity'].'</td>
						<td class="bold normal">'.$related['quantity'].'</td>
						<td>'.$version['title'].'</td>
						<td>'.$version['change'].'</td>
						<td>'.$version['planInbound'].'</td>
						<td>'.$version['planOutbound'].'</td>
						<td>'.$this->kclass->iif($version['ifProduce']==1,'是','否').'</td>
						<td>单价（<a href="/s.php?module=material&action=updatePrice&materialid='.$material['materialid'].'&versionid='.$version['versionid'].'&type=1">直接</a> <a href="/s.php?module=material&action=updatePrice&materialid='.$material['materialid'].'&versionid='.$version['versionid'].'&type=2">平均</a>）　<a href="/s.php?module=material&action=updateItemVersion&materialid='.$material['materialid'].'&itemid='.$version['itemid'].'">修改版本</a>　'.$this->kclass->iif($material['attrid']==8,'<a href="/s.php?module=material&action=updatePacking&type=item&materialid='.$material['materialid'].'&versionid='.$version['versionid'].'">修改包装</a>　','').$bomOperate.'</td>
					</tr>';
				}
				$k++;
			}
		}
		$mitemtr.='</tbody></table>';
		if($hasChild==0){
			$childBomtr['off']=' disabled';
		}
		if($hasParent==0){
			$parentBomtr['off']=' disabled';
		}

		$link1=$this->kclass->iif($material['killed']==0,'<a href="" target="_blank">打印</a>　|　<span class="small">新建：</span><a href="/s.php?module=material&action=add">物资</a>　<a href="/s.php?module=attribute&action=add&materialid='.$material['materialid'].'">属性</a>　<a href="/s.php?module=material&action=updateItem&materialid='.$material['materialid'].'&attrid='.$material['attrid'].'">物资明细</a>　|　<span class="small">修改：</span><a href="/s.php?module=material&action=update&materialid='.$material['materialid'].'&rt=view">物资详情</a>　<a href="/s.php?module=material&action=updateStock&materialid='.$material['materialid'].$this->kclass->iif($itemid>0,'&itemid='.$itemid,'').'&rt=view" target="_blank">库存</a>　|　<a href="/s.php?module=material&action=remove&materialid='.$material['materialid'].'&rt=view">删除</a></span>','<span class="small">操作：</span><a href="/s.php?module=material&action=restore&materialid='.$material['materialid'].'&rt=view">恢复</a>');

		$creator=$material['creator'];
		$created=date('Y-m-d H:i:s',$material['created']);
		if($material['modified']!=0){
			$modify='，由'.$material['modifier'].'于'.date('Y-m-d H:i:s',$material['modified']).'修改';
		}
		$tolerance=$this->kclass->iif($material['uptolerance']==0 AND $material['belowtolerance']==0,0,'>'.$material['belowtolerance'].'%,<'.$material['uptolerance'].'%');

		$supplierList=$this->kclass->relatedSupplierMaterial(array('materialid'=>$material['materialid']));
		$apply=$this->kclass->relatedPurchaseApply(array('materialid'=>$material['materialid']));
		$purchase=$this->kclass->relatedPurchase(array('materialid'=>$material['materialid']));
		$order=$this->kclass->relatedPOrder(array('materialid'=>$material['materialid']));
		$invoice=$this->kclass->relatedPInvoice(array('materialid'=>$material['materialid']));
		$debitnote=$this->kclass->relatedDebitnote(array('module'=>'porder','materialid'=>$material['materialid']));
		$payment=$this->kclass->relatedPayment(array('module'=>'porder','materialid'=>$material['materialid']));
		$stock=$this->kclass->relatedStock(array('materialid'=>$material['materialid']));
		$batch=$this->kclass->relatedBatch(array('materialid'=>$material['materialid'],'itemid'=>$itemid));
		$barcode=$this->kclass->relatedBarcode(array('materialid'=>$material['materialid'],'itemid'=>$itemid));
		$inbound=$this->kclass->relatedInbound(array('materialid'=>$material['materialid'],'itemid'=>$itemid));
		$outbound=$this->kclass->relatedOutbound(array('materialid'=>$material['materialid'],'itemid'=>$itemid));
		$inoutRecord=$this->kclass->relatedInoutrecord(array('materialid'=>$material['materialid'],'itemid'=>$itemid));
		$inquiry=$this->kclass->relatedPInquiry(array('materialid'=>$material['materialid']));
		$quoting=$this->kclass->relatedPQuoting(array('materialid'=>$material['materialid']));
		$attribute=$this->kclass->relatedAttribute(array('module'=>'material','mid'=>$material['materialid']));
		// 获得年月
		$nowYear=date('Y',TIMENOW);$nowMonth=date('n',TIMENOW);
		foreach($this->kclass->year AS $val){
			$year.='<option value="'.$val['value'].'"';
			if($val['value']==$nowYear){
				$year.='selected';
			}
			$year.='>'.$val['title'].'</option>';
		}
		foreach($this->kclass->month AS $val){
			$month.='<option value="'.$val['value'].'"';
			if($val['value']==$nowMonth){
				$month.='selected';
			}
			$month.='>'.$val['title'].'</option>';
		}

		$body=<<<EOF
<div class="title"><span class="right small gray">由 {$creator} 于{$created}建立{$modify}。</span>{$title}</div>
<dl id="material" class="tabs" style="display: block;">
<dt tabid=0>物资基本信息</dt>
<dt title="此物资所在物料清单的子物资信息"{$childBomtr['off']} ajax="childBom">BOM(子)</dt>
<dt title="此物资所在物料清单的父物资信息"{$parentBomtr['off']} ajax="parentBom">BOM(父)</dt>
<dt title="与此物资所在的库位列表"{$stock['off']}>库位{$stock['count']}</dt>
<dt title="与此物资所在的批次信息"{$batch['off']}>批次{$batch['count']}</dt>
<dt title="与此物资相关的物资条码和单个物资条码范围"{$barcode['off']}>条码{$barcode['count']}</dt>
<dt title="与此物资相关的盘点记录"{$inventoryList['off']}>盘点{$inventoryList['count']}</dt>
<dt title="与此物资相关的入库记录"{$inbound['off']}>入库{$inbound['count']}</dt>
<dt title="与此物资相关的出库记录"{$outbound['off']}>出库{$outbound['count']}</dt>
<dt title="与此物资相关的物料收发记录"{$inoutRecord['off']} ajax="inoutRecord">收发卡{$inoutRecord['count']}</dt>
<dt title="与此物资相关的询价记录"{$inquiry['off']}>询价{$inquiry['count']}</dt>
<dt title="与此物资相关的报价记录"{$quoting['off']}>报价{$quoting['count']}</dt>
<dt title="与此物资相关的采购申请记录"{$apply['off']}>采购申请{$apply['count']}</dt>
<dt title="与此物资相关的采购计划记录"{$purchase['off']}>采购计划{$purchase['count']}</dt>
<dt title="与此物资相关的订单记录"{$order['off']}>订单{$order['count']}</dt>
<dt title="与此物资相关的应付记录"{$debitnote['off']}>应付{$debitnote['count']}</dt>
<dt title="与此物资相关的已付记录"{$payment['off']}>已付{$payment['count']}</dt>
<dt title="与此物资相关的发票记录"{$invoice['off']}>发票{$invoice['count']}</dt>
<dt title="与此物资相关的成本记录"{$price['off']}>成本{$price['count']}</dt>
<dd>
<div><span class="right gray">{$link1}</span></div>
<table class="hundred">
<thead><tr><th colspan="6">物资信息</th></tr></thead>
<tbody>
<tr class="odd">
<td width="150">物资编号：</td><td width="450">{$material['materialno']}</td>
<td width="150">物资名称：</td><td width="450">{$material['title']}</td>
<td width="100">物资规格：</td><td width="450">{$material['standard']}</td>
</tr>
<tr class="even">
<td >物资名称（英文）：</td><td width="450">{$material['entitle']}</td>
<td>物资标准（英文）：</td><td width="450" colspan="3">{$material['enstandard']}</td>
</tr>
<tr class="odd">
<td width="100">物资分类：</td><td width="450">{$material['category']}</td>
<td>品　　牌：</td><td>{$material['brand']}</td>
<td>物资单位：</td><td>{$material['unit']}　　　物资类别：{$material['attr']}</td>
</tr>
<tr class="even">
<td>物资来源：</td><td>{$material['origin']}　　　　　　　　是否是通用物料：{$ifGeneral}</td>
<td>盘点类型：</td><td>{$invetoryType}</td>
<td>包　　装：</td><td>{$material['packing']}</td>
</tr>
<tr class="odd">
<td>总　　数：</td><td>{$material['quantity']}</td>
<td>良品数量：</td><td>{$material['qualified']}</td>
<td>不良品数量：</td><td>{$material['disqualified']}</td>
</tr>
<tr class="odd">
<td>最大峰值：</td><td>{$material['max']}</td>
<td>最小报警值：</td><td>{$material['min']}　　　　　　　　临界报警值：{$material['criticalNumber']}</td>
<td>是否需要外发加工：</td><td>{$ifNeedentrust}　　<span class="red bold">{$entrustmaterial}</span></td>
</tr>
<tr class="odd">
<td>允 差 值：</td><td>{$tolerance}</td>
<td>是否有流水号条码：</td><td>{$perBarcode}</td>
<td>备　　注：</td><td>{$material['remark']}</td>
</tr>
</tbody>
</table>
{$attribute}
{$mitemtr}
{$supplierList}
{$attachs}
</dd>
<dd load="0" id="childBom"></dd>
<dd load="0" id="parentBom"></dd>
<dd>{$stock['panel']}</dd>
<dd>{$batch['panel']}</dd>
<dd>{$barcode['panel']}</dd>
<dd>{$inventoryList['panel']}</dd>
<dd>{$inbound['panel']}</dd>
<dd>{$outbound['panel']}</dd>
<dd ><div ><span class="gray">筛选：</span><select name="year9" id="year9">{$year}</select>　<select name="month9" id="month9">{$month}</select><button class="hand" onclick="dc.tabs({'id':'material','materialid':{$material['materialid']},'itemid':{$this->kclass->iif($itemid>0,$itemid,0)},'res':1})">筛选</button></div>
<div load=0 id="inoutRecord">{$inoutRecord['panel']}</div></dd>
<dd>{$inquiry['panel']}</dd>
<dd>{$quoting['panel']}</dd>
<dd>{$apply['panel']}</dd>
<dd>{$purchase['panel']}</dd>
<dd>{$order['panel']}</dd>
<dd>{$debitnote['panel']}</dd>
<dd>{$payment['panel']}</dd>
<dd>{$invoice['panel']}</dd>
EOF;
		$json="{'id':'material','materialid':".$material['materialid'].",'itemid':".$this->kclass->iif($itemid>0,$itemid,0)."}";
		$this->kclass->tbline+=4;
		$this->kclass->page['title'].='查看物资';
		$this->kclass->page['onload'].='dc.tabhover();dc.tabs('.$json.');dc.show()';
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - <a href="/s.php?module=material&action=list">物资列表</a> - '.$material['materialno'].$this->kclass->iif($material['itemno']!='',' - '.$material['itemno'],''),'right'=>'<a href="/s.php?module=material&action=view&materialid='.($material['materialid']-1).'">上一条</a>　<a href="/s.php?module=material&action=view&materialid='.($material['materialid']+1).'">下一条</a>　'.$this->kclass->iif($material['killed']==0,'<a href="" target="_blank">打印</a>　|　<span class="small">新建：</span><a href="/s.php?module=material&action=add">物资</a>　|　<span class="small">修改：</span><a href="/s.php?module=material&action=update&materialid='.$material['materialid'].'&rt=view">物资详情</a>　<a href="/s.php?module=material&action=updatePacking&type=all&materialid='.$material['materialid'].'">包装</a>　<a href="/s.php?module=material&action=updateStock&&materialid='.$material['materialid'].$this->kclass->iif($itemid>0,'&itemid='.$itemid,'').'&rt=view" target="_blank">库存</a>　|　<a href="/s.php?module=material&action=remove&materialid='.$material['materialid'].'&rt=view">删除</a></span>','<span class="small">操作：</span><a href="/s.php?module=material&action=restore&materialid='.$material['materialid'].'&rt=view">恢复</a>'),'body'=>$body));
	}
	// a form  for add a material
	function add(){	
		if($this->kclass->input['itemid']>0){
			$item=$this->kclass->DB->queryFirst("
				SELECT p.materialid,p.unitid,
					m.materialno AS newMaterialno,m.title AS newTitle,m.standard AS newStandard,
					pi.materialno,pi.title,pi.standard
				FROM `purchaseitem` AS p
				LEFT JOIN `material` AS m ON (m.materialid=p.materialid)
				LEFT JOIN `preinbounditem` AS pi ON (pi.itemid=p.mitemid)
				WHERE p.killed=0 AND p.module='purchaseApply' AND p.itemid='".$this->kclass->input['itemid']."'
				LIMIT 0,1
			");
			$item['materialno']=strtoupper($item['materialno']);
			$item['hidden']='<input type="hidden" name="itemid" value="'.$this->kclass->input['itemid'].'" />';
		}
		//$material=$this->kclass->formMaterial($item);
		$unit=$this->kclass->chooserUnit(array('name'=>'unitid','hasBlank'=>1,'width'=>100,'selectedid'=>5));
		$packing=$this->kclass->chooserPacking(array('name'=>'packingid','hasBlank'=>1,'width'=>200,'selectedid'=>6));
		$brand=$this->kclass->chooserBrand(array('name'=>'brandid','hasBlank'=>1,'width'=>200,'selectedid'=>1));
		$attr=$this->kclass->chooserAttr(array('name'=>'attrid','hasBlank'=>1,'module'=>'material'));
		$origin=$this->kclass->chooserMaterialOrigin(array('name'=>'originid','hasBlank'=>1,'width'=>100));
		$category=$this->kclass->chooserCategory(array('name'=>'categoryid','width'=>450));
		$upload=$this->kclass->upload(array('title'=>'相关附件：'));
		$body=<<<EOF
<form action="/s.php?module=material&action=insert" id="material" method="post">
<input type="hidden" name="module" value="material" />
<input type="hidden" name="action" value="insert" />
<table class="hundred">
<thead><tr><th colspan="4">物资基础资料</th></tr></thead>
<tbody>
<tr class="even">
<td width="100">物资编号：<span class="red bold">*</span></td><td width="450"><input type="text" name="materialno" value="" /></td>
<td width="100">物资名称：<span class="red bold">*</span></td><td><input type="text" name="title" id="title" style="width:450px" value="" /></td>
</tr>
<tr class="odd">
<td>物资规格：</td><td><input type="text" name="standard" style="width:450px" value="" /></td>
<td>物资分类：<span class="red bold">*</span></td><td width="450">{$category}</td>
</tr>
<tr class="even">
<td>单　　位：<span class="red bold">*</span></td><td>{$unit}　是否计入成本价：<input type="radio" name="ifPrice" value="0" />否 <input type="radio" name="ifPrice" value="1" checked />是</td>
<td>包　　装：</td><td>{$packing}</td>
</tr>
<tr class="odd">
<td>盘点类型：<span class="red bold">*</span></td><td><input type="radio" name="inventoryType" value="choose" >抽盘 </option><input type="radio" name="inventoryType" value="must" checked>必盘　　是否通用物料：<input type="radio" name="ifGeneral" value="1" />是 <input type="radio" name="ifGeneral" value="0" checked />否 </td>
<td>类　　别：<span class="red bold">*</span></td><td>{$attr}</td>
</tr>
<tr class="even">
<td>品　　牌：</td><td>{$brand}</td>
<td>物资来源：<span class="red bold">*</span></td><td>{$origin}</td>
</tr>
<tr class="odd">
<td>最大峰值：<span class="red bold">*</span></td><td><input type="text" name="max" value="0" />	最小报警值：<span class="red bold">*</span>		<input type="text" name="min" value="0" /></td>
<td>是否需要外发加工</td><td><input type="radio" name="ifEntrust" value="0" checked/>否 <input type="radio" name="ifEntrust" value="1"  />是 请选择外发加工物资来源：
EOF;
for($i=1;$i<=5;$i++){
$body.=$this->kclass->chooserEntrustMaterial(array('name'=>'entrustMaterialid['.$i.']','id'=>'entrustMaterial','hasBlank'=>1,'width'=>100)).'　';
//<span id="materiallist"><span  class="odd bold gray normal hand"  onclick="dc.addSelect('entrustMaterial','materiallist')" style="border:2px solid gray;background:#FFE8EB">增加明细</span></span>
}
$body.=<<<EOF
</td>
</tr>
<tr class="even">
<td>临界报警值：<span class="red bold">*</span></td><td><input type="text" name="criticalNumber" value="0" /></td>
<td>允 差 值：</td><td> < <input type="text" name="uptolerance" value="0" /> ‰ ,> <input type="text" name="belowtolerance" value="0" /> ‰</td>
</tr>
<tr class="odd">
<td>是否分配流水号条码：<span class="red bold">*</span></td><td><input type="radio" name="ifPerBarcode" value="0" checked />否 <input type="radio" name="ifPerBarcode" value="1" />是</td>
<td>备　　注：</td><td><textarea name="remark" style="width:444px;height:111px;"></textarea></td>
</tr>
</tbody>
</table>
{$upload}
<table class="tlist tsuite" align="center" width="100%">
<tbody>
<tr class="odd center" nohover>
<td colspan=4><input type="submit" id="submitButton" value="  提交  " accesskey="s"><input type="reset" value="  复位  "/></td>
</tr>
</tbody>
</table>
</form>
EOF;
		$this->kclass->tbline+=4;
		$this->kclass->page['title'].='新建物资';
		$this->kclass->page['onload'].='dc.tabhover()';
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - <a href="/s.php?module=material">物资列表</a> - 新建物资','right'=>'<a href="/s.php?module=material">返回列表</a>','body'=>$body));
	}
	// insert material
	function insert(){
		 $entrustMaterialid=implode(',',array_filter($this->kclass->input['entrustMaterialid']));
		if($this->kclass->input['requestMethod']!='post'){
			$e='<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			if($this->kclass->input['materialno']==''){
				$e='<li>请填写物资编号。</li>';
			}else{
				$material=$this->kclass->DB->queryFirst("SELECT materialid,materialno FROM `material` WHERE killed=0 AND materialno='".$this->kclass->input['materialno']."' AND attrid='".$this->kclass->input['attrid']."' LIMIT 0,1 ");
				if($material){
					$e.='<li>您要新建的 物资编号 【'.$material['materialno'].'】 已经存在。</li>';
				}
			}
			if(!$this->kclass->input['originid']>0){
				$e.='<li>请选择物资的 来源。</li>';
			}
			if($this->kclass->input['title']==''){
				$e.='<li>请填写物资的 名称。</li>';
			}
			if(!$this->kclass->input['unitid']>0){
				$e.='<li>请选择物资的 单位。</li>';
			}
			if(!$this->kclass->input['categoryid']>0){
				$e.='<li>请选择物资的 分类。</li>';
			}
			if(!$this->kclass->input['attrid']>0){
				$e.='<li>请选择物资的 类别。</li>';
			}
			if($this->kclass->input['inventoryType']<0){
				$e.='<li>请选择物资的 盘点类型。</li>';
			}
			if($this->kclass->input['min']<0){
				$e.='<li>请填写物资的 最小报警值。</li>';
			}
			if($this->kclass->input['max']<0){
				$e.='<li>请填写物资的 最大峰值。</li>';
			}
			if($this->kclass->input['criticalNumber']<0){
				$e.='<li>请填写物资的 临界报警值。</li>';
			}
			if($this->kclass->input['ifPerBarcode']!=0 AND $this->kclass->input['ifPerBarcode']!=1){
				$e.='<li>请选择物资的 是否流水号分配。</li>';
			}
			if( $this->kclass->input['ifEntrust']==1){
				if(empty($entrustMaterialid)){
				$e.='<li>请选择物资的 是否需要外发加工。</li>';
				}
			}
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '新建物资',
				'text' => '您在新建物资的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}

        
		//$materialid=$this->kclass->updateMaterial();
		// 生产物资、产品等物资
		$this->kclass->DB->query("
			INSERT INTO material (categoryid,originid,attrid,unitid,packingid,brandid,inventoryType,materialno,title,entitle,standard,enstandard,uptolerance,belowtolerance,min,max,criticalNumber,`ifPrice`,ifPerBarcode,ifNeedentrust,entrustMaterialid,remark,created,creator)
			VALUES ('".$this->kclass->input['categoryid']."','".$this->kclass->input['originid']."','".$this->kclass->input['attrid']."','".$this->kclass->input['unitid']."','".$this->kclass->input['packingid']."','".$this->kclass->input['brandid']."','".$this->kclass->input['inventoryType']."','".$this->kclass->input['materialno']."','".$this->kclass->input['title']."','".$this->kclass->input['entitle']."','".$this->kclass->input['standard']."','".$this->kclass->input['enstandard']."','".$this->kclass->input['uptolerance']."','".$this->kclass->input['belowtolerance']."','".$this->kclass->input['min']."','".$this->kclass->input['max']."','".$this->kclass->input['criticalNumber']."','".$this->kclass->input['ifPrice']."','".$this->kclass->input['ifPerBarcode']."','".$this->kclass->input['ifEntrust']."','".$entrustMaterialid."','".$this->kclass->input['remark']."','".TIMENOW."','".$this->kclass->user['userid']."')
		");
		$materialid=$this->kclass->DB->insertID();
		if($this->kclass->input['attrid']!=7 AND $this->kclass->input['attrid']!=8){
			// 新建版本
			$this->kclass->DB->query("
				INSERT INTO materialversion (`materialid`,`title`,`change`,`created`,`creator`)
				VALUES ('".$materialid."','01','".$this->kclass->input['change']."','".TIMENOW."','".$this->kclass->user['userid']."')
			");
		}
		if($this->kclass->input['attrid']==6){
			// 新建生产物资
			$this->kclass->DB->query("
				INSERT pmaterial (`materialid`,`materialno`,`title`,`entitle`,`standard`,`enstandard`,`ifGeneral`,`created`,`creator`) 
				VALUES ('".$materialid."','".$this->kclass->input['materialno']."','".$this->kclass->input['title']."','".$this->kclass->input['entitle']."','".$this->kclass->input['standard']."','".$this->kclass->input['enstandard']."','".$this->kclass->input['ifGeneral']."','".TIMENOW."','".$this->kclass->user['userid']."')
			");
		}

		$this->kclass->updateAttachs(array('module'=>'material','mid'=>$materialid));
		if($this->kclass->input['itemid']>0){
			$this->kclass->DB->query("UPDATE purchaseitem SET materialid='".$materialid."' WHERE itemid='".$this->kclass->input['itemid']."'");
		}
		$this->kclass->messager(array(
			'title' => '新建物资',
			'text' => '物资 <b>'.$this->kclass->input['materialno'].' '.$this->kclass->input['title'].'</b> 已新建成功!',
			'url' => '/s.php?module=material&action=view&materialid='.$materialid,
			'sec' => 2
		));
	}

	// a form for material update
	function update(){
		$material=$this->kclass->DB->queryFirst("
			SELECT m.materialid,m.materialno,m.title,m.entitle,m.standard,m.enstandard,m.attrid,m.categoryid,m.unitid,m.packingid,m.brandid,m.attrid,m.originid,m.quantity,m.inventoryType,m.qualified,m.disqualified,m.uptolerance,m.belowtolerance,m.min,m.max,m.criticalNumber,m.ifPrice,m.ifPerBarcode,m.remark,m.entrustMaterialid,
				pm.pmaterialid,pm.ifGeneral
			FROM material AS m
			LEFT JOIN pmaterial AS pm ON (pm.materialid=m.materialid)
			WHERE m.killed=0 AND m.materialid='".$this->kclass->input['materialid']."'
			LIMIT 0,1
		");
		if(!$material){
			$e='<li>数据错误，很抱歉</li>';
		}
		if($e){
			$this->kclass->messager(array(
				'title' => '查看物资',
				'text' => '您在查看物资的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		//$formMaterial=$this->kclass->formMaterial($material);
		$unit=$this->kclass->chooserUnit(array('name'=>'unitid','hasBlank'=>1,'width'=>100,'selectedid'=>$material['unitid']));
		$packing=$this->kclass->chooserPacking(array('name'=>'packingid','hasBlank'=>1,'width'=>200,'selectedid'=>$material['packingid']));
		$brand=$this->kclass->chooserBrand(array('name'=>'brandid','hasBlank'=>1,'width'=>200,'selectedid'=>$material['brandid']));
		$attr=$this->kclass->chooserAttr(array('name'=>'attrid','hasBlank'=>1,'module'=>'material','selectedid'=>$material['attrid']));
		$origin=$this->kclass->chooserMaterialOrigin(array('name'=>'originid','hasBlank'=>1,'width'=>100,'selectedid'=>$material['originid']));
		$entrustmaterial=$this->kclass->chooserEntrustMaterial(array('name'=>'entrustMaterialid','hasBlank'=>1,'width'=>200,'selectedid'=>$material['entrustMaterialid']));
		$category=$this->kclass->chooserCategory(array('name'=>'categoryid','width'=>450,'selectedid'=>$material['categoryid']));
		$upload=$this->kclass->upload(array('title'=>'相关附件：','module'=>'material','materialid'=>$this->kclass->input['materialid']));
		if($material['inventoryType']=='must'){
			$ichecked1='';
			$ichecked2=' checked';
		}elseif($material['inventoryType']=='choose'){
			$ichecked1=' checked';
			$ichecked2='';
		}else{
			$ichecked1='';
			$ichecked2=' checked';
		}
		if($material['ifGeneral']==1){
			$gchecked1=' checked';
			$gchecked2='';
		}elseif($material['ifGeneral']==0){
			$gchecked1='';
			$gchecked2=' checked';
		}
		if($material['ifPrice']==1){
			$pchecked1=' checked';
			$pchecked2='';
		}elseif($material['ifPrice']==0){
			$pchecked1='';
			$pchecked2=' checked';
		}
       $entrustMaterialid=explode(',',$material['entrustMaterialid']);
	   for($i=1;$i<=5;$i++){

	   }
		$body=<<<EOF
<form action="/s.php?module=material&action=doupdate" id="material" method="post">
<input type="hidden" name="module" value="material" />
<input type="hidden" name="action" value="doupdate" />
<input type="hidden" name="materialid" value="{$material['materialid']}" />
<table class="hundred">
<thead><tr><th colspan="4">物资基础资料</th></tr></thead>
<tbody>
<tr class="even">
<td width="100">物资编号：<span class="red bold">*</span></td><td width="450"><input type="text" name="materialno" value="{$material['materialno']}" /></td>
<td width="100">物资名称：<span class="red bold">*</span></td><td><input type="text" name="title" id="title" style="width:450px" value="{$material['title']}" /></td>
</tr>
<tr>
<td >物资英文名称：<span class="red blod">*</span></td><td><input type="text" name="entitle" id="entitle" style="width:450px" value="{$material['entitle']}"/></td>
<td>物资规格（英文）:</td><td><input type="text" name="enstandard" style="width:450px" value="{$material['enstandard']}"/></td>
</tr>
<tr class="odd">
<td>物资规格：</td><td><input type="text" name="standard" style="width:450px" value="{$material['standard']}" /></td>
<td>物资分类：<span class="red bold">*</span></td><td width="450">{$category}</td>
</tr>
<tr class="even">
<td>单　　位：<span class="red bold">*</span></td><td>{$unit}　是否计入成本价：<input type="radio" name="ifPrice" value="0" {$pchecked2} />否 <input type="radio" name="ifPrice" value="1" {$pchecked1} />是</td>
<td>包　　装：</td><td>{$packing}</td>
</tr>
<tr class="odd">
<td>盘点类型：<span class="red bold">*</span></td><td><input type="radio" name="inventoryType" value="choose" {$ichecked1} >抽盘 </option><input type="radio" name="inventoryType" value="must" {$ichecked2}>必盘　　是否通用物料：<input type="radio" name="ifGeneral" value="1" {$gchecked1} />是 <input type="radio" name="ifGeneral" value="0" {$gchecked2} />否 </td>
<td>类　　别：<span class="red bold">*</span></td><td>{$attr}</td>
</tr>
<tr class="even">
<td>品　　牌：</td><td>{$brand}</td>
<td>物资来源：<span class="red bold">*</span></td><td>{$origin}</td>
</tr>
<tr class="odd">
<td>最大峰值：<span class="red bold">*</span></td><td><input type="text" name="max" value="0" />	最小报警值：<span class="red bold">*</span> <input type="text" name="min" value="0" /></td>
<td>是否需要外发加工</td><td><input type="radio" name="ifEntrust" value="0" checked/>否 <input type="radio" name="ifEntrust" value="1"  />是 请选择外发加工后的物资名：
EOF;
for($i=1;$i<=5;$i++){
$body.=$this->kclass->chooserEntrustMaterial(array('name'=>'entrustMaterialid['.$i.']','id'=>'entrustMaterial','hasBlank'=>1,'width'=>100)).'　';
//<span id="materiallist"><span  class="odd bold gray normal hand"  onclick="dc.addSelect('entrustMaterial','materiallist')" style="border:2px solid gray;background:#FFE8EB">增加明细</span></span>
}
$body.=<<<EOF
</td>
</tr>
<tr class="even">
<td>临界报警值：<span class="red bold">*</span></td><td><input type="text" name="criticalNumber" value="0" /></td>
<td>允 差 值：</td><td> < <input type="text" name="uptolerance" value="0" /> ‰ ,> <input type="text" name="belowtolerance" value="0" /> ‰</td>
</tr>
<tr class="odd">
<td>是否分配流水号条码：<span class="red bold">*</span></td><td><input type="radio" name="ifPerBarcode" value="0" checked />否 <input type="radio" name="ifPerBarcode" value="1" />是</td>
<td>备　　注：</td><td><textarea name="remark" style="width:444px;height:111px;">{$material['remark']}</textarea></td>
</tr>
</tbody>
</table>
{$upload}
<table class="tlist tsuite" align="center" width="100%">
<tbody>
<tr class="odd center" nohover>
<td colspan=4><input type="submit" id="submitButton" value="  提交  " accesskey="s"><input type="reset" value="  复位  "/></td>
</tr>
</tbody>
</table>
</form>
EOF;

		$this->kclass->tbline+=4;
		$this->kclass->page['title'].='修改物资';
		$this->kclass->page['onload'].='dc.tabhover();';
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - <a href="/s.php?module=material&action=view&materialid='.$this->kclass->input['materialid'].'">查看物资</a> - 修改物资','right'=>'<a href="/s.php?module=material">返回列表</a>　|　<a href="/s.php?module=material&action=view&materialid='.$this->kclass->input['materialid'].'">返回查看物资</a>','body'=>$body));
	}

	//
	function doupdate(){
		if($this->kclass->input['requestMethod']!='post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			if($this->kclass->input['materialno']==''){
				$e='<li>请填写物资编号。</li>';
			}else{
				$material=$this->kclass->DB->queryFirst("SELECT materialid,materialno FROM `material` WHERE killed=0 AND materialno='".$this->kclass->input['materialno']."' AND attrid='".$this->kclass->input['attrid']."' AND materialid<>'".$this->kclass->input['materialid']."' LIMIT 0,1 ");
				if($material){
					$e.='<li>您要新建的 物资编号 【'.$material['materialno'].'】 已经存在。</li>';
				}
			}
			if(!$this->kclass->input['originid']>0){
				$e.='<li>请选择物资的 来源。</li>';
			}
			if($this->kclass->input['title']==''){
				$e.='<li>请填写物资的 名称。</li>';
			}
			if(!$this->kclass->input['unitid']>0){
				$e.='<li>请选择物资的 单位。</li>';
			}
			if(!$this->kclass->input['categoryid']>0){
				$e.='<li>请选择物资的 分类。</li>';
			}
			if(!$this->kclass->input['attrid']>0){
				$e.='<li>请选择物资的 类别。</li>';
			}
			if($this->kclass->input['inventoryType']<0){
				$e.='<li>请选择物资的 盘点类型。</li>';
			}
			if($this->kclass->input['min']<0){
				$e.='<li>请填写物资的 最小报警值。</li>';
			}
			if($this->kclass->input['max']<0){
				$e.='<li>请填写物资的 最大峰值。</li>';
			}
			if($this->kclass->input['criticalNumber']<0){
				$e.='<li>请填写物资的 临界报警值。</li>';
			}
			if($this->kclass->input['ifPerBarcode']!=0 AND $this->kclass->input['ifPerBarcode']!=1){
				$e.='<li>请选择物资的 是否流水号分配。</li>';
			}
			if($this->kclass->input['ifEntrust']==1){
				 $entrustMaterialid=implode(',',array_filter($this->kclass->input['entrustMaterialid']));
				 if(empty($entrustMaterialid)){
				$e.='<li>请选择物资的 是否需要外发加工。</li>';
				 }
			}
		}
		if($e){
			$this->kclass->messager(array(
				'title' => '修改物资',
				'text' => '您在修改物资的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$this->kclass->DB->query("
			UPDATE material SET
				categoryid='".$this->kclass->input['categoryid']."',
				originid='".$this->kclass->input['originid']."',
				attrid='".$this->kclass->input['attrid']."',
				unitid='".$this->kclass->input['unitid']."',
				packingid='".$this->kclass->input['packingid']."',
				brandid='".$this->kclass->input['brandid']."',
				inventoryType='".$this->kclass->input['inventoryType']."',
				materialno='".$this->kclass->input['materialno']."',
				title='".$this->kclass->input['title']."',
				entitle='".$this->kclass->input['entitle']."',
				standard='".$this->kclass->input['standard']."',
				enstandard='".$this->kclass->input['enstandard']."',
				belowtolerance='".$this->kclass->input['belowtolerance']."',
				uptolerance='".$this->kclass->input['uptolerance']."',
				min='".$this->kclass->input['min']."',
				max='".$this->kclass->input['max']."',
				criticalNumber='".$this->kclass->input['criticalNumber']."',
				ifPrice='".$this->kclass->input['ifPrice']."',
				ifPerBarcode='".$this->kclass->input['ifPerBarcode']."',
				ifNeedentrust='".$this->kclass->input['ifEntrust']."',
				entrustMaterialid='".$entrustMaterialid."',
				remark='".$this->kclass->input['remark']."',
				modified='".TIMENOW."',
				modifier='".$this->kclass->user['userid']."'
			WHERE materialid='".$this->kclass->input['materialid']."'
		");
		if($this->kclass->input['attrid']==6){
			$pmaterial=$this->kclass->DB->queryFirst("SELECT pmaterialid FROM pmaterial WHERE materialid='".$this->kclass->input['materialid']."' LIMIT 0,1");
			if($pmaterial){
				$this->kclass->DB->query("
					UPDATE pmaterial SET
						`materialno`='".$this->kclass->input['materialno']."',
						`title`='".$this->kclass->input['title']."',
						`entitle`='".$this->kclass->input['entitle']."',
						`standard`='".$this->kclass->input['standard']."',
						`enstandard`='".$this->kclass->input['enstandard']."',
						`ifGeneral`='".$this->kclass->input['ifGeneral']."',
						`modified`='".TIMENOW."',
						`modifier`='".$this->kclass->user['userid']."'
					WHERE pmaterialid='".$pmaterial['pmaterialid']."'
				");
			}else{
				// 新建生产物资
				$this->kclass->DB->query("
					INSERT pmaterial (`materialid`,`materialno`,`title`,`standard`,`ifGeneral`,`created`,`creator`) 
					VALUES ('".$this->kclass->input['materialid']."','".$this->kclass->input['materialno']."','".$this->kclass->input['title']."','".$this->kclass->input['standard']."','".$this->kclass->input['ifGeneral']."','".TIMENOW."','".$this->kclass->user['userid']."')
				");
			}
		}
		if($this->kclass->input['attrid']!=7 AND $this->kclass->input['attrid']!=8){
			$version=$this->kclass->DB->queryFirst("SELECT 1 FROM materialversion WHERE materialid={$this->kclass->input['materialid']}");
			if(!$version){
				// 新建版本
				$this->kclass->DB->query("
					INSERT INTO materialversion (`materialid`,`title`,`change`,`created`,`creator`)
					VALUES ('".$this->kclass->input['materialid']."','01','".$this->kclass->input['change']."','".TIMENOW."','".$this->kclass->user['userid']."')
				");
			}
		}

		$this->kclass->updateAttachs(array('module'=>'material','mid'=>$this->kclass->input['materialid']));
		$this->kclass->messager(array(
			'title' => '修改物资',
			'text' => '物资 <b>'.$this->kclass->input['materialno'].' '.$this->kclass->input['title'].'</b> 已修改成功!',
			'url' => '/s.php?module=material&action=view&materialid='.$this->kclass->input['materialid'],
			'sec' => 2
		));
	}
	//
	function kill(){
		if($this->kclass->input['materialid']<=0){
			$this->kclass->boinkIt('/s.php?module=material');
		}
		if($this->kclass->input['rt']!='list' AND $this->kclass->input['confirm'] == 0){
			$this->kclass->boinkIt('/s.php?module=material&action=view&materialid='.$this->kclass->input['materialid']);
		}
		if($this->kclass->input['materialid'] < 0){
			$e = '<li>请从正确的页面进行操作，如果此问题持续存在，请联系网站管理员。</li>';
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '删除物资',
				'text' => '您在删除物资的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		if($this->kclass->input['rt']=='view'){
			$rt = '&action=view&materialid='.$this->kclass->input['materialid'];
		}elseif($this->kclass->input['rt']=='list'){
			$rt = '#'.$this->kclass->input['materialid'];
		}
		$material=$this->kclass->DB->queryFirst("
			SELECT materialid,materialno,attrid
			FROM material
			WHERE materialid='".$this->kclass->input['materialid']."'
		");
		if($material){
			// 删除物资
			$this->kclass->DB->query("
				UPDATE `material`
				SET killed=".TIMENOW.",killer='".$this->kclass->user['userid']."'
				WHERE materialid='".$material['materialid']."'
			");
			if($material['attrid']==6){// 删除生产物资
				$this->kclass->DB->query("
					UPDATE `pmaterial`
					SET killed=".TIMENOW.",killer='".$this->kclass->user['userid']."'
					WHERE materialid='".$this->kclass->input['materialid']."'
				");
			}elseif($material['attrid']==7 OR $material['attrid']==8){
				$this->kclass->DB->query("
					UPDATE `materialitem`
					SET killed=".TIMENOW.",killer='".$this->kclass->user['userid']."'
					WHERE materialid='".$this->kclass->input['materialid']."'
				");
			}
			
			// 删除版本
			$this->kclass->DB->query("
				UPDATE `materialversion`
				SET killed=".TIMENOW.",killer='".$this->kclass->user['userid']."'
				WHERE materialid='".$this->kclass->input['materialid']."'
			");
			// 删除批次
			$this->kclass->DB->query("
				UPDATE `batch`
				SET killed=".TIMENOW.",killer='".$this->kclass->user['userid']."'
				WHERE materialid='".$this->kclass->input['materialid']."'
			");
			// 报废条码
			$this->kclass->DB->query("
				UPDATE `barcode`
				SET scraped=".TIMENOW.",scraper='".$this->kclass->user['userid']."'
				WHERE materialid='".$this->kclass->input['materialid']."'
			");
			$this->kclass->messager(array(
				'title' => '删除物资成功',
				'text' => '物资 <b>'.$material['materialno'].'</b> 已成功被标记为删除!',
				'url' => '/s.php?module=material&action='.$rt,
				'sec' => 2
			));
		}else{
			$this->kclass->messager(array(
				'title' => '删除物资失败',
				'text' => '您要删除的物资，不存在！',
				'url' => '/s.php?module=material&action='.$rt,
				'sec' => 3
			));
		}
	}

	//
	function remove(){
		if($this->kclass->input['materialid']<=0){
			$this->kclass->boinkIt('/s.php?module=material');
		}
		$material=$this->kclass->DB->queryFirst("
			SELECT materialid,materialno
			FROM material
			WHERE materialid='".$this->kclass->input['materialid']."'
		");
$body = <<<EOF
<form action="/s.php?module=material&action=kill" name="material" method="post">
<input type="hidden" name="module" value="material">
<input type="hidden" name="action" value="kill">
<input type="hidden" name="rt" value="{$this->kclass->input['rt']}">
<input type="hidden" name="materialid" value="{$this->kclass->input['materialid']}">
<table><thead>
<thead>
<tr>
<th>删除物资：{$material['materialno']}</th>
</tr>
</thead>
<tbody>
<tr class="even">
<td class="middle">你确定要删除物资: <a href="/s.php?module=material&action=view&materialid={$this->kclass->input['materialid']}" class="big bold" target="_blank">{$material['materialno']}</a> 吗?</td>
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
		$this->kclass->page['title'].=' - 删除 - '.$material['materialno'];
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 删除物资 - '.$material['materialno'],'right' => '<a href="/s.php?module=material">返回列表</a>','body'=>$body));
	}

	//
	function revival(){
		if($this->kclass->input['materialid']<=0){
			$this->kclass->boinkIt('/s.php?module=material');
		}
		if($this->kclass->input['rt']!='list' AND $this->kclass->input['confirm'] == 0){
			$this->kclass->boinkIt('/s.php?module=material&action=view&materialid='.$this->kclass->input['materialid']);
		}
		if($this->kclass->input['materialid'] < 0){
			$e = '<li>请从正确的页面进行操作，如果此问题持续存在，请联系网站管理员。</li>';
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '恢复物资',
				'text' => '您在恢复物资的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$material=$this->kclass->DB->queryFirst("
			SELECT materialid,materialno
			FROM material
			WHERE materialid='".$this->kclass->input['materialid']."'
		");
		if($this->kclass->input['rt']=='view'){
			$rt = '&action=view&materialid='.$this->kclass->input['materialid'];
		}elseif($this->kclass->input['rt']=='list'){
			$rt = '#'.$this->kclass->input['materialid'];
		}
		if($material){
			// 恢复物资
			$this->kclass->DB->query("
				UPDATE `material`
				SET killed=0,killer=0
				WHERE materialid='".$material['materialid']."'
			");
			if($material['attrid']==6){// 删除生产物资
				$this->kclass->DB->query("
					UPDATE `material`
					SET killed=0,killer=0
					WHERE materialid='".$this->kclass->input['materialid']."'
				");
			}elseif($material['attrid']==7 OR $material['attrid']==8){
				$this->kclass->DB->query("
					UPDATE `materialitem`
					SET killed=0,killer=0
					WHERE materialid='".$this->kclass->input['materialid']."'
				");
			}
			
			// 删除版本
			$this->kclass->DB->query("
				UPDATE `materialversion`
				SET killed=0,killer=0
				WHERE materialid='".$this->kclass->input['materialid']."'
			");
			// 删除批次
			$this->kclass->DB->query("
				UPDATE `batch`
				SET killed=0,killer=0
				WHERE materialid='".$this->kclass->input['materialid']."'
			");
			// 报废条码
			$this->kclass->DB->query("
				UPDATE `barcode`
				SET scraped=0,scraper=0
				WHERE materialid='".$this->kclass->input['materialid']."'
			");
			$this->kclass->messager(array(
				'title' => '恢复物资成功',
				'text' => '物资 <b>'.$material['materialno'].'</b> 已成功被恢复！',
				'url' => '/s.php?module=material'.$rt,
				'sec' => 2
			));
		}else{
			$this->kclass->messager(array(
				'title' => '恢复物资失败',
				'text' => '您要恢复的物资不存在！',
				'url' => '/s.php?module=material'.$rt,
				'sec' => 3
			));
		}
	}

	//
	function restore(){
		if($this->kclass->input['materialid']<=0){
			$this->kclass->boinkIt('/s.php?module=material');
		}
		$material=$this->kclass->DB->queryFirst("
			SELECT materialid,materialno
			FROM material
			WHERE materialid='".$this->kclass->input['materialid']."'
		");
$body = <<<EOF
<form action="/s.php?module=material&action=revival" name="material" method="post">
<input type="hidden" name="module" value="material">
<input type="hidden" name="action" value="revival">
<input type="hidden" name="rt" value="{$this->kclass->input['rt']}">
<input type="hidden" name="materialid" value="{$this->kclass->input['materialid']}">
<table><thead>
<thead>
<tr>
<th>恢复物资：{$material['materialno']}</th>
</tr>
</thead>
<tbody>
<tr class="even">
<td class="middle">你确定要恢复物资: <a href="/s.php?module=material&action=view&materialid={$this->kclass->input['materialid']}" class="big bold" target="_blank">{$material['materialno']}</a> 吗?</td>
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
		$this->kclass->page['title'].=' - 恢复 - '.$material['materialno'];
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 恢复物资 - '.$material['materialno'],'right' => '<a href="/s.php?module=material">返回列表</a>','body'=>$body));
	}
	//
	function updateVersion(){
		$material=$this->kclass->DB->queryFirst("SELECT materialid,materialno,title,standard FROM pmaterial WHERE killed=0 AND materialid='".$this->kclass->input['materialid']."'");
		$versions=$this->kclass->DB->query("SELECT `versionid`,`title`,`change`,`quantity`,`qualified`,`disqualified` FROM materialversion WHERE killed=0 AND materialid='".$material['materialid']."' ORDER BY created DESC");
		if($this->kclass->DB->numRows()){
			$k=1;
			while($version=$this->kclass->DB->fetchArray($versions)){
				$itemtr.='<tr class="odd">
					<input type="hidden" name="itemId['.$k.']" value="'.$k.'" />
					<input type="hidden" name="itemVersionId['.$k.']" value="'.$version['versionid'].'" />
					<td>'.$k.'</td>
					<td><input type="text" name="itemVersion['.$k.']" style="width:40px" value="'.$version['title'].'" /></td>
					<td><input type="text" name="itemChange['.$k.']" style="width:650px" value="'.$version['change'].'" /></td>
					<td><input type="text" name="itemQuantity['.$k.']" style="width:70px" value="'.$version['quantity'].'" /></td>
					<td><input type="text" name="itemQualified['.$k.']" style="width:70px" value="'.$version['qualified'].'" /></td>
					<td><input type="text" name="itemDisqualified['.$k.']" style="width:70px" value="'.$version['disqualified'].'" /></td>
					<td><input type="checkbox" name="itemKill['.$k.']" value="'.$version['versionid'].'" /></td>
				</tr>';
				$k++;
			}
			$itemtr.='<tr><td colspan=11 class="middle bold">新增版本信息</td></tr>';
		}
		for($i=$k;$i<$k+5;$i++){
			$itemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').'">
				<input type="hidden" value="'.$i.'" name="itemId['.$i.']"/>
				<td>'.$i.'</td>
				<td><input type="text" name="itemVersion['.$i.']" style="width:40px" value="" /></td>
				<td><input type="text" name="itemChange['.$i.']" style="width:650px" value="" /></td>
				<td><input type="text" name="itemQuantity['.$i.']" style="width:70px" value="0" /></td>
				<td><input type="text" name="itemQualified['.$i.']" style="width:70px" value="0" /></td>
				<td><input type="text" name="itemDisqualified['.$i.']" style="width:70px" value="0" /></td>
				<td></td>
			</tr>';
		}

$body=<<<EOF
<form action="/s.php?module=material&action=doupdateVersion" id="material" method="post">
<input type="hidden" name="module" value="material" />
<input type="hidden" name="action" value="doupdateVersion" />
<input type="hidden" name="materialid" value="{$material['materialid']}" />
<table class="hundred">
<thead><tr><th colspan="8">修改版本</th></tr></thead>
<tbody>
<tr class="odd center">
<td width="30">ID</td>
<td width="50">版本 <span class="red bold">*</span></td>
<td>版本描述</td>
<td width="70">总数 <span class="red bold">*</span></td>
<td width="70">良品数 <span class="red bold">*</span></td>
<td width="70">不良品数 <span class="red bold">*</span></td>
<td width="15">删</td>
</tr>
{$itemtr}
<tr class="even" nohover>
<td colspan="8" align="center">
<input type="submit" value="  保存  " />
<input type="reset" value="  复位  " />
</td>
</tr>
</tbody>
</table>
</form>
EOF;

		$this->kclass->page['title'].='修改物资版本';
		$this->kclass->page['onload'].='dc.tabhover();';
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - <a href="/s.php?module=material&action=view&materialid='.$this->kclass->input['materialid'].'">查看物资</a> - 修改物资版本','right'=>'<a href="/s.php?module=material&action=view&materialid='.$this->kclass->input['materialid'].'">返回查看物资</a>','body'=>$body));
	}
	//
	function doupdateVersion(){
		if($this->kclass->input['requestMethod']!='post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			$hasItem=0;
			$j=count($this->kclass->input['itemId']);
			for($i=1;$i<=$j;$i++){
				if($this->kclass->input['itemVersion'][$i]!='' AND $this->kclass->input['itemQuantity'][$i]>=0 AND $this->kclass->input['itemQualified'][$i]>=0 AND $this->kclass->input['itemDisqualified'][$i]>=0){
					if($this->kclass->input['itemVersionId'][$i]==''){
						$version=$this->kclass->DB->queryFirst("SELECT COUNT(versionid) AS count FROM `materialversion` WHERE materialid='".$this->kclass->input['materialid']."' AND title='".$this->kclass->input['itemVersion'][$i]."' LIMIT 0,1");
						if($version['count']>0){
							$e.='<li>修改 的版本 '.$this->kclass->input['itemVersion'][$i].' 已存在。</li>';
						}
					}

					if($this->kclass->input['itemKill'][$i]==''){
						$hasItem=1;
					}
				}
			}
			if($hasItem==0){
				$e.='<li>请填写 完整的产品明细</li>';
			}
		}
		if($e){
			$this->kclass->messager(array(
				'title' => '修改版本',
				'text' => '您在修改版本的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$material=$this->kclass->DB->queryFirst("SELECT materialid,materialno,title,standard FROM material WHERE killed=0 AND materialid='".$this->kclass->input['materialid']."'");
		for($i=0;$i<=$j;$i++){
			if($this->kclass->input['itemVersion'][$i]!='' AND $this->kclass->input['itemQuantity'][$i]>=0 AND $this->kclass->input['itemQualified'][$i]>=0 AND $this->kclass->input['itemDisqualified'][$i]>=0){
				if($this->kclass->input['itemVersionId'][$i]>0){
					if($this->kclass->input['itemKill'][$i]>0 AND $this->kclass->input['itemKill'][$i]==$this->kclass->input['itemVersionId'][$i]){
						$this->kclass->DB->query("UPDATE materialversion SET killed='".TIMENOW."',killer='".$this->kclass->user['userid']."' WHERE versionid='".$this->kclass->input['itemVersionId'][$i]."'");
					}else{
						$this->kclass->DB->query("
							UPDATE materialversion SET
								`title`='".$this->kclass->input['itemVersion'][$i]."',
								`change`='".$this->kclass->input['itemChange'][$i]."',
								`quantity`='".$this->kclass->input['itemQuantity'][$i]."',
								`qualified`='".$this->kclass->input['itemQualified'][$i]."',
								`disqualified`='".$this->kclass->input['itemDisqualified'][$i]."',
								`modifier`='".$this->kclass->user['userid']."',
								`modified`='".TIMENOW."'
							WHERE versionid='".$this->kclass->input['itemVersionId'][$i]."'
						");
					}
				}else{
					$this->kclass->DB->query("
						INSERT INTO materialversion
							(`materialid`,`title`,`change`,`quantity`,`qualified`,`disqualified`,`created`,`creator`)
						VALUES
							('".$this->kclass->input['materialid']."','".$this->kclass->input['itemVersion'][$i]."','".$this->kclass->input['itemChange'][$i]."','".$this->kclass->input['itemQuantity'][$i]."','".$this->kclass->input['itemQualified'][$i]."','".$this->kclass->input['itemDisqualified'][$i]."','".TIMENOW."','".$this->kclass->user['userid']."')
					");
				}
			}
		}

		$this->kclass->messager(array(
			'title' => '修改版本成功',
			'text' => '物资 '.$this->kclass->input['title'].' 修改成功',
			'url' => '/s.php?module=material&action=view&materialid='.$this->kclass->input['materialid'],
			'sec' => 3
		));
	}
	//
	function updateStock(){
		$material=$this->kclass->getMaterial(array('materialid'=>$this->kclass->input['materialid'],'itemid'=>$this->kclass->input['itemid']));
		if(!$material){
			$e='<li>数据错误。很抱歉！</li>';
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '修改物资库存',
				'text' => '您在修改物资库存的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}

		if($this->kclass->input['itemid']>0){
			$condition=' AND mv.itemid='.$this->kclass->input['itemid'];
		}

		$barcodes=$this->kclass->DB->query("
			SELECT b.barcodeid,b.supplierid,b.barcode,b.quantity,b.qualified,b.disqualified,
				mv.versionid,mv.versionid,mv.title AS version,mv.`change`,
				ba.batchid,ba.dateline,ba.batchno,
				s.title AS supplier,s.supplierno
			FROM barcode AS b
			LEFT JOIN materialversion AS mv ON (b.versionid=mv.versionid)
			LEFT JOIN batch AS ba ON (b.batchid=ba.batchid)
			LEFT JOIN supplier AS s ON (s.supplierid=b.supplierid)
			WHERE b.scraped=0 AND b.parentid=0 AND b.materialid='".$material['materialid']."' $condition
		");
		$i=1;
		$counter=$this->kclass->DB->numRows();
		if($counter){
			$orgItemtr.='<table class="hundred"><thead></tr><th colspan="10">修改物资<b>'.$material['materialno'].'</b>库存信息</th></tr></thead>
<tbody><tr class="center even" nohover><td width="15">ID</td><td width="200">条码</td><td width="80">批次 <span class="bold red">*</span></td><td width="30">版本</td><td width="90">该批时间</td><td width="30">良品数 <span class="red bold">*</span></td><td width="30">不良品数 <span class="red bold">*</span></td><td width="400">供应商 <span class="red bold">*</span></td><td width="60">版本描述</td></tr>';
			while($barcode=$this->kclass->DB->fetchArray($barcodes)){
				$orgItemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').'">
					<input type="hidden" name="itemId['.$i.']" value="'.$i.'" />
					<input type="hidden" name="itemBarcodeid['.$i.']" value="'.$barcode['barcodeid'].'">
					<input type="hidden" name="itemBatchid['.$i.']" value="'.$barcode['batchid'].'">
					<input type="hidden" name="itemVersionid['.$i.']" value="'.$barcode['versionid'].'">
					<input type="hidden" name="itemOriginQuantity['.$i.']" value="'.$barcode['quantity'].'">
					<input type="hidden" name="itemOriginQualified['.$i.']" value="'.$barcode['qualified'].'">
					<input type="hidden" name="itemOriginDisqualified['.$i.']" value="'.$barcode['disqualified'].'">
					<td>'.$i.'</td>
					<td><a href="/s.php?module=barcode&action=view&barcodeid='.$barcode['barcodeid'].'" >'.$barcode['barcode'].'</a></td>
					<td><a href="/s.php?module=batch&action=view&batchid='.$barcode['batchid'].'" >'.$barcode['batchno'].'</a></td>
					<td>'.$barcode['version'].'</td>
					<td><input type="text" name="itemDateline['.$i.']" id="dateline'.$i.'" value="'.date('Y-m-d',$barcode['dateline']).'" /></td>
					<td><input type="text" name="itemQualified['.$i.']" value="'.$barcode['qualified'].'" size="10" ></td>
					<td><input type="text" name="itemDisqualified['.$i.']" value="'.$barcode['disqualified'].'" size="10" ></td>
					<td>'.$this->kclass->chooserSupplier(array('hId'=>'itemSupplierid'.$i,'hName'=>'itemSupplierid['.$i.']','id'=>'itemSupplier'.$i,'name'=>'itemSupplier['.$i.']','width'=>400,'selectedid'=>$barcode['supplierid'],'value'=>$barcode['supplier'].' ('.$barcode['supplierno'].')','line'=>$i)).'</td>
					<td><input type="text" name="itemChange['.$i.']" value="" size="40" ></td>
				</tr>';
				$id.=$this->kclass->iif($i<$counter+1,'#dateline'.$i.',','#dateline'.$i);
				$i++;
			}
			$orgItemtr.='</tbody></table>';
		}
		$defaultSupplier=$this->kclass->DB->queryFirst("SELECT supplierid,title AS supplier,supplierno FROM supplier WHERE killed=0 AND supplierid=2");
		for($m=$i;$m<$i+5;$m++){
			$OlItemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').'">
				<input type="hidden" name="itemId['.$m.']" value="'.$m.'" />
				<td>'.$m.'</td>
				<td><input type="text" name="itemDateline['.$m.']" id="dateline'.$m.'" value="'.date('Y-m-d',TIMENOW).'" size="10" /></td>
				<td>'.$this->kclass->chooserMaterialVersion(array('materialid'=>$material['materialid'],'itemid'=>$this->kclass->input['itemid'],'name'=>'itemVersionid['.$m.']','width'=>'500')).'</td>
				<td><input type="text" name="itemQualified['.$m.']" value="'.$barcode['quantity'].'" size="10" ></td>
				<td><input type="text" name="itemDisqualified['.$m.']" value="0" size="10" ></td>
				<td>'.$this->kclass->chooserSupplier(array('hId'=>'itemSupplierid'.$m,'hName'=>'itemSupplierid['.$m.']','id'=>'itemSupplier'.$m,'name'=>'itemSupplier['.$m.']','width'=>400,'selectedid'=>$defaultSupplier['supplierid'],'value'=>$defaultSupplier['supplier'].' ('.$defaultSupplier['supplierno'].')','line'=>$m)).'</td>
				<td><input type="text" name="itemChange['.$m.']" value="" size="40" ></td>
			</tr>';
			$id.=$this->kclass->iif($m<$i+4,'#dateline'.$m.',','#dateline'.$m);
		}

$body=<<<EOF
<div class="title">{$material['no']}　{$material['material']}　{$material['standard']}</div>
 <form action="/s.php?module=material&action=doupdateStock" name="material" method="post">
<input type="hidden" name="module" value="material" />
<input type="hidden" name="action" value="doupdateStock" />
<input type="hidden" name="materialid" value="{$material['materialid']}" />
<input type="hidden" name="itemid" value="{$material['itemid']}" />
{$orgItemtr}
<table class="hundred"><thead><tr><th colspan=8>新增库存信息</th></tr></thead><tbody>
<tr class="center even" nohover><td width="15">ID</td><td>批次时间 <span class="bold red">*</span></td><td>版本 <span class="red bold">*</span></td><td>良品数 <span class="red bold">*</span></td><td>不良品数 <span class="red bold">*</span></td><td>供应商 <span class="red bold">*</span></td></td><td>版本描述</td></tr>
{$OlItemtr}
<tr class="odd">
<td class="small gray" colspan="7">
	注意事项：<br>
		①修改版本批次时间将会修改该条数据的批次和条码。<br>
		②输入供应商关键字选择供应商；
</td>
</tr>
</tbody>
</table>
<table class="hundred"><tbody>
<tr class="even center" nohover><td colspan=10><input type="submit" id="submitButton" value="  提交  " accesskey="s"><input type="reset" value="  复位  "/></td></td></tr>
</tbody>
</table>
</form>
EOF;

		$this->kclass->tbline+=4;
		$this->kclass->page['title'].=$material['materialno'].' - 修改库存';
		$this->kclass->page['onload'].='$(\''.$id.'\').datepicker();dc.tabhover()';
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - <a href="/s.php?module=material&action=view&materialid='.$material['materialid'].'">'.$material['materialno'].'</a> - 修改库存','right'=>'<a href="/s.php?module=material&action=view&materialid='.$material['materialid'].'">返回查看物资信息</a>','body'=>$body));
	}
	//
	function doupdateStock(){
		if($this->kclass->input['requestMethod']!='post'){
			$e='<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			$j=count($this->kclass->input['itemId']);
			for($i=1;$i<$j+1;$i++){
				if($this->kclass->input['itemVersionid'][$i]>0 AND $this->kclass->input['itemDateline'][$i]!='' AND $this->kclass->input['itemQualified'][$i]>=0 AND $this->kclass->input['itemDisqualified'][$i]>=0 AND $this->kclass->input['itemSupplierid'][$i]>0){
					$hasItem=1;
				}
			}
			if($hasItem==0){
				$e.='<li>请填写完整明细</li>';
			}
		}
		if($e){
			$this->kclass->messager(array(
				'title' => '修改物资库存信息',
				'text' => '您在修改物资库存信息的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}

		$material=$this->kclass->getMaterial(array('materialid'=>$this->kclass->input['materialid'],'itemid'=>$this->kclass->input['itemid']));

		// 修改
		$j=count($this->kclass->input['itemBarcodeid']);
		$batchId=$versionId=array();
		$orgTotal=$orgQualified=$orgDisqualified=0;
		for($i=1;$i<$j+1;$i++){
			if($this->kclass->input['itemVersionid'][$i]>0 AND $this->kclass->input['itemDateline'][$i]!='' AND floatval($this->kclass->input['itemQualified'][$i])>=0 AND $this->kclass->input['itemSupplierid'][$i]>0){
				$time36='';
				$version=$this->kclass->DB->queryFirst("SELECT title,quantity,qualified,disqualified FROM materialversion WHERE killed=0 AND versionid='".$this->kclass->input['itemVersionid'][$i]."'");
				$supplier=$this->kclass->DB->queryFirst("SELECT supplierno FROM supplier WHERE killed=0 AND supplierid='".$this->kclass->input['itemSupplierid'][$i]."'");
				$qualified=floatval($this->kclass->input['itemQualified'][$i]);
				$disqualified=floatval($this->kclass->input['itemDisqualified'][$i]);
				$quantity=$this->kclass->input['itemQualified'][$i]+$this->kclass->input['itemDisqualified'][$i];
				$sd=explode('-',$this->kclass->input['itemDateline'][$i]);
				$dateline=mktime(0,0,0,$sd[1],$sd[2],$sd[0]);
				$sd1=explode('-',date('y-n-j',$dateline));
				foreach($sd1 as $key => $val){
					$time36.=strtoupper(base_convert($val,10,36));
				}
				$barcode='CN-'.$material['materialno'].$this->kclass->iif($material['itemid']>0,'-'.$material['no'],'').'-'.$supplier['supplierno'].'-'.$time36.'-'.$version['title'];
				// 修改条码数量
				$this->kclass->DB->query("
					UPDATE barcode SET
						barcode='".$barcode."',
						supplierid='".$this->kclass->input['itemSupplierid'][$i]."',
						arrivalTime='".$dateline."',
						quantity=".$quantity.",
						qualified=".$qualified.",
						disqualified=".$disqualified.",
						modifier='".$this->kclass->user['userid']."',
						modified='".TIMENOW."'
					WHERE barcodeid='".$this->kclass->input['itemBarcodeid'][$i]."'
				");
				// 获得批次数量
				if(in_array($this->kclass->input['itemBatchid'][$i],$batchId)){
					$batchQuantity[$this->kclass->input['itemBatchid'][$i]]+=$quantity;
					$batchQualified[$this->kclass->input['itemBatchid'][$i]]+=$qualified;
					$batchDisqualified[$this->kclass->input['itemBatchid'][$i]]+=$disqualified;
				}else{
					$batchId[]=$this->kclass->input['itemBatchid'][$i];
					$batchVersionId[$this->kclass->input['itemBatchid'][$i]]=$this->kclass->input['itemVersionid'][$i];
					$batchQuantity[$this->kclass->input['itemBatchid'][$i]]=$quantity;
					$batchQualified[$this->kclass->input['itemBatchid'][$i]]=$qualified;
					$batchDisqualified[$this->kclass->input['itemBatchid'][$i]]=$disqualified;
					$batchDateline[$this->kclass->input['itemBatchid'][$i]]=$dateline;
				}
				// 获得版本数量
				if(in_array($this->kclass->input['itemVersionid'][$i],$versionId)){
					$versionQuantity[$this->kclass->input['itemVersionid'][$i]]+=$quantity;
					$versionQualified[$this->kclass->input['itemVersionid'][$i]]+=$qualified;
					$versionDisqualified[$this->kclass->input['itemVersionid'][$i]]+=$disqualified;
				}else{
					$versionId[]=$this->kclass->input['itemVersionid'][$i];
					$versionQuantity[$this->kclass->input['itemVersionid'][$i]]=$quantity;
					$versionQualified[$this->kclass->input['itemVersionid'][$i]]=$qualified;
					$versionDisqualified[$this->kclass->input['itemVersionid'][$i]]=$disqualified;
				}

				// 获得总数
				$orgTotal+=$version['quantity'];
				$orgQualified+=$version['qualified'];
				$orgDisqualified+=$version['disqualified'];
				$total1+=$quantity;
				$qualified1+=$qualified;
				$disqualified1+=$disqualified;
			}
		}

		// 修改批次库存
		if(!empty($batchId)){
			foreach($batchId as $key => $val){
				$batchno=$this->kclass->id(array('materialno'=>$material['no'],'batch'=>$batchDateline[$val]));
				$this->kclass->DB->query("
					UPDATE batch SET
						versionid='".$batchVersionId[$val]."',
						dateline='".$batchDateline[$val]."',
						batchno='".$batchno."',
						quantity=".$batchQuantity[$val].",
						qualified=".$batchQualified[$val].",
						disqualified=".$batchDisqualified[$val].",
						killer=0,killed=0,
						modifier='".$this->kclass->user['userid']."',
						modified='".TIMENOW."'
					WHERE batchid='".$val."'
				");
			}
		}
		if(!empty($versionId)){
			foreach($versionId as $key => $val){
				$this->kclass->DB->query("
					UPDATE materialversion SET
						quantity=".$versionQuantity[$val].",
						qualified=".$versionQualified[$val].",
						disqualified=".$versionDisqualified[$val].",
						modifier='".$this->kclass->user['userid']."',
						modified='".TIMENOW."'
					WHERE versionid='".$val."'
				");
			}
		}

		$n=count($this->kclass->input['itemId']);
		for($i=$j+1;$i<=$n;$i++){
			if($this->kclass->input['itemVersionid'][$i]>0 AND $this->kclass->input['itemDateline'][$i]!='' AND (floatval($this->kclass->input['itemQualified'][$i])>0 OR floatval($this->kclass->input['itemDisqualified'][$i])>0) AND $this->kclass->input['itemSupplierid'][$i]>0){
				$time36='';
				$version=$this->kclass->DB->queryFirst("SELECT title FROM materialversion WHERE killed=0 AND versionid='".$this->kclass->input['itemVersionid'][$i]."' LIMIT 0,1");
				$supplier=$this->kclass->DB->queryFirst("SELECT supplierno FROM supplier WHERE killed=0 AND supplierid='".$this->kclass->input['itemSupplierid'][$i]."' LIMIT 0,1");
				$qualified=floatval($this->kclass->input['itemQualified'][$i]);
				$disqualified=floatval($this->kclass->input['itemDisqualified'][$i]);
				$quantity=$qualified+$disqualified;
				$sd=explode('-',$this->kclass->input['itemDateline'][$i]);
				$dateline=mktime(0,0,0,$sd[1],$sd[2],$sd[0]);
				$sd1=explode('-',date('y-n-j',$dateline));
				foreach($sd1 as $val){
					$time36.=strtoupper(base_convert($val,10,36));
				}

				// 新增批次
				$batchno=$this->kclass->id(array('materialno'=>$material['no'],'batch'=>$dateline));
				$this->kclass->DB->query("
					INSERT INTO batch
						(materialid,batchno,versionid,dateline,quantity,qualified,disqualified,created,creator)
					VALUES
						('".$material['materialid']."','".$batchno."','".$this->kclass->input['itemVersionid'][$i]."','".$dateline."','".$quantity."','".$qualified."','".$disqualified."','".TIMENOW."','".$this->kclass->user['userid']."')
				");
				$batchid=$this->kclass->DB->insertID();
				// 修改版本数量
				$this->kclass->DB->query("
					UPDATE materialversion SET
						quantity=quantity+'".$quantity."',
						qualified=qualified+'".$qualified."',
						disqualified=disqualified+'".$disqualified."',
						modifier='".$this->kclass->user['userid']."',
						modified='".TIMENOW."'
					WHERE versionid='".$this->kclass->input['itemVersionid'][$i]."'
				");
				// 新增条码
				$barcode='CN-'.$material['materialno'].$this->kclass->iif($material['itemid']>0,'-'.$material['no'],'').'-'.$supplier['supplierno'].'-'.$time36.'-'.$version['title'];
				$this->kclass->DB->query("
					INSERT INTO barcode
						(batchid,materialid,supplierid,versionid,parentid,barcode,country,materialno,supplierno,arrivalTime,number,version,quantity,qualified,disqualified,statusid,creator,created)
					VALUES
						('".$batchid."','".$material['materialid']."','".$this->kclass->input['itemSupplierid'][$i]."','".$this->kclass->input['itemVersionid'][$i]."',0,'".$barcode."','CN','".$material['materialno']."','".$supplier['supplierno']."','".$dateline."',0,'".$version['title']."','".$quantity."','".$qualified."','".$disqualified."','0','".$this->kclass->user['userid']."','".TIMENOW."')
				");
				$total2+=$quantity;
				$qualified2+=$qualified;
				$disqualified2+=$disqualified;
			}
		}
		// 库存总数、良品数、不良品数
		$total=$total1+$total2;
		$totalQualified=$qualified1+$qualified2;
		$totalDisqualified=$disqualified1+$disqualified2;
		if($this->kclass->input['itemid']>0){
			$materialCount1=$this->kclass->DB->queryFirst("SELECT SUM(quantity) AS totalQuantity,SUM(qualified) AS totalQualified,SUM(disqualified) AS totalDisqualified FROM materialversion WHERE killed=0 AND materialid='".$this->kclass->input['materialid']."' AND itemid='".$this->kclass->input['itemid']."'");
			$this->kclass->DB->query("
				UPDATE materialitem SET
					quantity='".$materialCount1['totalQuantity']."',
					qualified='".$materialCount1['totalQualified']."',
					disqualified='".$materialCount1['totalDisqualified']."'
				WHERE itemid='".$this->kclass->input['itemid']."'
			");
		}
		$materialCount=$this->kclass->DB->queryFirst("SELECT SUM(quantity) AS totalQuantity,SUM(qualified) AS totalQualified,SUM(disqualified) AS totalDisqualified FROM materialversion WHERE killed=0 AND materialid='".$this->kclass->input['materialid']."'");
		$this->kclass->DB->query("
			UPDATE material SET
				quantity='".$materialCount['totalQuantity']."',
				qualified='".$materialCount['totalQualified']."',
				disqualified='".$materialCount['totalDisqualified']."'
			WHERE materialid='".$this->kclass->input['materialid']."'
		");

		$this->kclass->messager(array(
			'title' => '修改物资库存信息',
			'text' => '物资 <b>'.$material['materialno'].'</b> 库存已修改！',
			'url' => $material['url'],
			'sec' => 2
		));
	}
	function updateItem(){
		$attribute=$this->kclass->DB->queryFirst("SELECT COUNT(nameid) AS count FROM attributename WHERE killed=0 AND module='material' AND mid='".$this->kclass->input['materialid']."'");
		if($attribute['count']<=0 AND $this->kclass->input['attrid']!=8){
			$this->kclass->messager(array(
				'title' => '新建或者修改物资条目',
				'text' => '您在新建或者修改物资条目的过程中有以下错误：<ul><li>请先添加属性，点击 <a href="/s.php?module=attribute&action=add&materialid='.$this->kclass->input['materialid'].'">添加属性</a></li></ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$material=$this->kclass->DB->queryFirst("SELECT materialid,attrid,materialno,title,standard FROM material WHERE materialid='".$this->kclass->input['materialid']."' LIMIT 0,1");
		if($this->kclass->input['attrid']==7){
			$k=1;
			$itemtr='<tr class="odd center"><td width="15">ID</td><td width="30">编号 <span class="red">*</span></td><td>属性 <span class="red bold">*</span></td><td width="60" title="数值越大越排在前面">排序</td><td width="15">删</td></tr>';
			$items=$this->kclass->DB->query("
				SELECT itemno,itemid,attributevalue,ordering
				FROM materialitem
				WHERE killed=0 AND materialid='".$this->kclass->input['materialid']."'
				ORDER BY ordering DESC
			");
			if($this->kclass->DB->numRows()){
				while($item=$this->kclass->DB->fetchArray($items)){
					$itemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').'">
						<input type="hidden" name="itemId['.$k.']" value="'.$item['itemid'].'" />
						<td>'.$k.'</td>
						<td><input type="text" name="itemno['.$k.']" value="'.$item['itemno'].'" size="7" /></td>
						<td>'.$this->kclass->chooserAttribute(array('name'=>'itemValueid'.$k,'module'=>'material','mid'=>$this->kclass->input['materialid'],'value'=>$item['attributevalue'])).'</td>
						<td><input type="text" title="数值越大越排在前面" name="ordering['.$k.']" value="'.$item['ordering'].'" size="6"></td>
						<td><input type="checkbox" name="itemKill['.$k.']" value="'.$item['itemid'].'" /></td></tr>';
					$k++;
				}
				$itemtr.='<tr><td colspan=9 class="middle bold">新增产品条目</td></tr>';
			}

			for($i=$k;$i<$k+5;$i++){
				$itemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').'">
					<td>'.$i.'</td>
					<td><input type="text" name="itemno['.$i.']" size="7" /></td>
					<td>'.$this->kclass->chooserAttribute(array('name'=>'itemValueid'.$i,'module'=>'material','mid'=>$this->kclass->input['materialid'])).'</td>
					<td><input type="text" title="数值越大越排在前面" name="ordering['.$i.']" value="1" size="6"></td>
					<td></td>
				</tr>';
			}
			$body = <<<EOF
<form action="/s.php?module=material&action=doupdateItem" name="material" method="post">
<input type="hidden" name="module" value="material" />
<input type="hidden" name="action" value="doupdateItem" />
<input type="hidden" name="materialid" value="{$material['materialid']}" />
<input type="hidden" name="attrid" value="{$material['attrid']}" />
<div class="title">{$material['materialno']}　{$material['title']}　{$material['standard']}</div>
<table style="width:100%;">
<thead><tr><th colspan="11">添加物资 {$material['materialno']} {$material['title']}条目信息</th></tr></thead>
<tbody>
{$itemtr}
<tr class="even">
<td class="small gray" colspan="11">注意事项：确认信息正确无误</td>
</tr>
<tr class="even" nohover>
<td class="center" colspan=11><input type="submit" id="submitButton" value="  提交  "><input type="reset" value="  复位  "/></td>
</tr>
</tbody>
</table>
</form>
EOF;
		}elseif($this->kclass->input['attrid']==8){
			if($this->kclass->input['relatedid']==''){
				$related=$this->kclass->DB->queryFirst("
					SELECT DISTINCT mti.materialid
					FROM materialitem AS mi 
					LEFT JOIN materialitem AS mti ON (mi.relatedid=mti.itemid)
					WHERE mi.killed=0 AND mti.killed=0 AND mi.materialid='".$this->kclass->input['materialid']."'
					LIMIT 0,1
				");
				$relatedMaterials=$this->kclass->DB->query("
					SELECT materialid,materialno,title,standard
					FROM material
					WHERE attrid=7 AND killed=0 
					ORDER BY materialno ASC
				");
				if($this->kclass->DB->numRows()){
					while($relatedMaterial=$this->kclass->DB->fetchArray($relatedMaterials)){
						$select.='<option value="'.$relatedMaterial['materialid'].'"';
						if($related['materialid']>0 AND $related['materialid']==$relatedMaterial['materialid']){
							$select.=' selected';
						}
						$select.='>'.$relatedMaterial['materialno'].'　'.$relatedMaterial['title'].'　'.$relatedMaterial['standard'].'</option>';
					}
				}
				$body=<<<EOF
<form action="/s.php?module=material&action=updateItem" name="material" method="get">
<input type="hidden" name="module" value="material" />
<input type="hidden" name="action" value="updateItem" />
<input type="hidden" name="materialid" value="{$material['materialid']}" />
<input type="hidden" name="attrid" value="{$material['attrid']}" />
<div class="title">{$material['materialno']}　{$material['title']}　{$material['standard']}</div>
<table style="width:650px">
<thead><tr><th colspan="11">添加物资 {$material['materialno']} {$material['title']}条目信息：选择对应的产成品</th></tr></thead>
<tbody>
<tr><td>选择对应的产成品：<span class="red">*</span></td><td><select name="relatedid" style="width:450px"><option value=0><option>{$select}</select></td></tr>
<tr class="even">
<td class="small gray" colspan="11">注意事项：确认信息正确无误</td>
</tr>
<tr class="even" nohover>
<td class="center" colspan=11><input type="submit" id="submitButton" value="  提交  "><input type="reset" value="  复位  "/></td>
</tr>
</tbody>
</table>
</form>
EOF;
			}else{
				$k=1;
				$itemtr='<tr class="odd center"><td width="15">ID</td><td width="80">明细编号 <span class="red">*</span></td><td width="120">选择对应产成品 <span class="red bold">*</span></td><td>新增产品属性</td><td width="450">相关销售产品明细</td><td width="45">排序</td><td width="15">删</td></tr>';
				$items=$this->kclass->DB->query("
					SELECT itemid,itemno,relatedid,productitemid,addvalue,attributevalue,ordering
					FROM materialitem 
					WHERE killed=0 AND materialid='".$this->kclass->input['materialid']."'
					ORDER BY itemno ASC
				");
				if($this->kclass->DB->numRows()){
					while($item=$this->kclass->DB->fetchArray($items)){
						$itemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').'">
							<td><input type="hidden" value="'.$item['itemid'].'" name="itemId['.$k.']"/>'.$k.'</td>
							<td><input type="text" name="itemno['.$k.']" value="'.$item['itemno'].'" size=8 /></td>
							<td>'.$this->_chooserFinishedItem(array('name'=>'itemRelatedid['.$k.']','materialid'=>$this->kclass->input['relatedid'],'width'=>'300','selected'=>$item['relatedid'])).'</td>
							<td>'.$this->kclass->chooserAttribute(array('name'=>'itemValueid'.$k,'module'=>'material','mid'=>$material['materialid'],'value'=>$item['addvalue'],'width'=>'650')).'</td>
							<td>'.$this->kclass->chooserProductItem(array('name'=>'productItemid['.$k.']','width'=>'450','hasBlank'=>1,'selectedid'=>$item['productitemid'])).'</td>
							<td><input type="text" title="数值越大越排在前面" name="ordering['.$k.']" value="'.$item['ordering'].'" size="5"></td>
							<td><input type="checkbox" name="itemKill['.$k.']" value="'.$item['itemid'].'" /></td>
						</tr>';
						$k++;
					}
					$itemtr.='<tr><td colspan=11 class="middle bold">新增产品条目</td></tr>';
				}
				for($i=$k;$i<$k+5;$i++){
					$itemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').'">
						<td>'.$i.'</td>
						<td><input type="text" name="itemno['.$i.']" value="" size=8  /></td>
						<td>'.$this->_chooserFinishedItem(array('name'=>'itemRelatedid['.$i.']','materialid'=>$this->kclass->input['relatedid'],'width'=>'100')).'</td>
						<td>'.$this->kclass->chooserAttribute(array('name'=>'itemValueid'.$i,'module'=>'material','mid'=>$material['materialid'],'width'=>'650')).'</td>
						<td>'.$this->kclass->chooserProductItem(array('name'=>'productItemid['.$i.']','width'=>'450','hasBlank'=>1)).'</td>
						<td><input type="text" title="数值越大越排在前面" name="ordering['.$i.']" value="1" size="5"></td>
						<td></td>
					</tr>';
				}
				$body = <<<EOF
<form action="/s.php?module=material&action=doupdateItem" name="material" method="post">
<input type="hidden" name="module" value="material" />
<input type="hidden" name="action" value="doupdateItem" />
<input type="hidden" name="materialid" value="{$material['materialid']}" />
<input type="hidden" name="attrid" value="{$material['attrid']}" />
<input type="hidden" name="relatedid" value="{$this->kclass->input['relatedid']}" />
<div class="title">{$material['materialno']}　{$material['title']}　{$material['standard']}</div>
<table style="width:100%;">
<thead><tr><th colspan="11">添加物资 {$material['materialno']} {$material['title']}条目信息</th></tr></thead>
<tbody>
{$itemtr}
<tr class="even">
<td class="small gray" colspan="11">注意事项：确认信息正确无误</td>
</tr>
<tr class="even" nohover>
<td class="center" colspan=11><input type="submit" id="submitButton" value="  提交  "><input type="reset" value="  复位  "/></td>
</tr>
</tbody>
</table>
</form>
EOF;
			}
		}

		$this->kclass->page['title'] .= '新建或者修改物资条目';
		$this->kclass->page['onload'] .= 'dc.tabhover()';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - <a href="/s.php?module=material&action=view&materialid='.$material['materialid'].'">查看物资</a> - 新建或者修改物资条目','right' => '<a href="/s.php?module=material&action=view&materialid='.$material['materialid'].'">返回</a>','body'=>$body));
	}

	function doupdateItem(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e='<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			$hasItem=0;
			if($this->kclass->input['attrid']==7){
				$j=count($this->kclass->input['ordering']);
				for($i=1;$i<=$j;$i++){
					if($this->kclass->input['itemKill'][$i]=='' AND $this->kclass->input['itemno'][$i]!='' AND is_array($this->kclass->input['itemValueid'.$i]) AND !in_array(0,$this->kclass->input['itemValueid'.$i])){
						sort($this->kclass->input['itemValueid'.$i]);
						$value=implode(',',$this->kclass->input['itemValueid'.$i]);
						/*if($this->kclass->input['itemId'][$i]==''){
							$item=$this->kclass->DB->queryFirst("SELECT COUNT(itemid) AS count FROM `materialitem` WHERE killed=0 AND `materialid`='".$this->kclass->input['materialid']."' AND (`attributevalue`='".$value."' OR itemno='".$this->kclass->input['itemno'][$i]."') LIMIT 0,1");
							if($item['count']>0){
								$e='<li>您要新建的物资明细 <b>'.$item['count'].'</b> 已经存在。</li>';
							}
						}*/
						$hasItem=1;
					}
				}
			}elseif($this->kclass->input['attrid']==8){
				$j=count($this->kclass->input['ordering']);
				for($i=1;$i<=$j;$i++){
					if($this->kclass->input['itemKill'][$i]=='' AND $this->kclass->input['itemno'][$i]!='' AND $this->kclass->input['itemRelatedid'][$i]>0){
						if(is_array($this->kclass->input['itemValueid'.$i]) AND !in_array(0,$this->kclass->input['itemValueid'.$i]) AND $this->kclass->input['productItemid'][$i]>0){
							sort($this->kclass->input['itemValueid'.$i]);
							$value=implode(',',$this->kclass->input['itemValueid'.$i]);
						}else{
							$value=0;
						}
						/*if($this->kclass->input['itemId'][$i]==''){
							$item=$this->kclass->DB->queryFirst("SELECT COUNT(itemid) AS count	FROM `materialitem` WHERE materialid='".$this->kclass->input['materialid']."' AND `relatedid`='".$this->kclass->input['itemRelatedid'][$i]."' AND `addvalue`='".$value."' LIMIT 0,1");
							if($item['count']>0){
								$e='<li>您要新建的物资明细  已经存在。</li>';
							}
						}*/
						$hasItem=1;
					}
				}
			}

			if($hasItem==0){
				$e='<li>请填写 完整的产品明细</li>';
			}
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '新建物资条目',
				'text' => '您在新建物资条目的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		if($this->kclass->input['attrid']==7){
			$j=count($this->kclass->input['ordering']);
			for($m=1;$m<=$j;$m++){
				if(is_array($this->kclass->input['itemValueid'.$m]) AND !in_array(0,$this->kclass->input['itemValueid'.$m])){
					sort($this->kclass->input['itemValueid'.$m]);
					$value=implode(',',$this->kclass->input['itemValueid'.$m]);
					$ordering=$this->kclass->iif($this->kclass->input['ordering'][$m]!='',$this->kclass->input['ordering'][$m],1);
					if($this->kclass->input['itemId'][$m]>0){
						if($this->kclass->input['itemKill'][$m]!='' AND $this->kclass->input['itemKill'][$m]==$this->kclass->input['itemId'][$m]){
							$this->kclass->DB->query("UPDATE materialitem SET killed='".TIMENOW."',killer='".$this->kclass->user['userid']."' WHERE itemid='".$this->kclass->input['itemKill'][$m]."'");
							$this->kclass->DB->query("UPDATE materialversion SET killed='".TIMENOW."',killer='".$this->kclass->user['userid']."' WHERE itemid='".$this->kclass->input['itemKill'][$m]."'");
						}else{
							$this->kclass->DB->query("
								UPDATE materialitem SET
									itemno='".$this->kclass->input['itemno'][$m]."',
									attributevalue='".$value."',
									ordering='".$ordering."',
									modifier='".$this->kclass->user['userid']."',
									modified='".TIMENOW."'
								WHERE itemid='".$this->kclass->input['itemId'][$m]."'
							");
						}
					}else{
						$this->kclass->DB->query("
							INSERT INTO materialitem (materialid,itemno,attributevalue,ordering,created,creator)
							VALUES ('".$this->kclass->input['materialid']."','".$this->kclass->input['itemno'][$m]."','".$value."','".$ordering."','".TIMENOW."','".$this->kclass->user['userid']."')
						");
						$itemid=$this->kclass->DB->insertID();
						$version=$this->kclass->DB->queryFirst("SELECT COUNT(versionid) AS count FROM materialversion WHERE killed=0 AND itemid='".$itemid."' LIMIT 0,1");
						if(empty($version['count'])){
							$this->kclass->DB->query("
								INSERT INTO materialversion (materialid,itemid,title,created,creator)
								VALUES ('".$this->kclass->input['materialid']."','".$itemid."','01','".TIMENOW."','".$this->kclass->user['userid']."')
							");
						}
					}
				}
			}
		}elseif($this->kclass->input['attrid']==8){
			$j=count($this->kclass->input['ordering']);
			for($m=1;$m<=$j;$m++){
				if($this->kclass->input['itemno'][$m]!='' AND $this->kclass->input['itemRelatedid'][$m]>0){
					$relatedItem=$this->kclass->DB->queryFirst("SELECT itemid,attributevalue FROM materialitem WHERE itemid='".$this->kclass->input['itemRelatedid'][$m]."' LIMIT 0,1");
					$relatedValue=explode(',',$relatedItem['attributevalue']);
					// 新增的属性值与产成品的属性结合
					if($this->kclass->input['itemValueid'.$m]!=''){
						sort($this->kclass->input['itemValueid'.$m]);
						$value=implode(',',array_merge($relatedValue,$this->kclass->input['itemValueid'.$m]));
						$addValue=implode(',',$this->kclass->input['itemValueid'.$m]);
					}else{
						$value=$relatedItem['attributevalue'];
						$addValue='';
					}
					if($this->kclass->input['productItemid'][$m]>0){
						$productItem=$this->kclass->DB->queryFirst("SELECT productitemid,productid FROM productitem WHERE productitemid='".$this->kclass->input['productItemid'][$m]."' LIMIT 0,1");
					}
					$ordering=$this->kclass->iif($this->kclass->input['ordering'][$m]!='',$this->kclass->input['ordering'][$m],1);
					//
					if($this->kclass->input['itemId'][$m]>0){
						if($this->kclass->input['itemKill'][$m]!='' AND $this->kclass->input['itemKill'][$m]==$this->kclass->input['itemId'][$m]){
							$this->kclass->DB->query("UPDATE materialitem SET killed='".TIMENOW."',killer='".$this->kclass->user['userid']."' WHERE itemid='".$this->kclass->input['itemKill'][$m]."'");
							$this->kclass->DB->query("UPDATE materialversion SET killed='".TIMENOW."',killer='".$this->kclass->user['userid']."' WHERE itemid='".$this->kclass->input['itemKill'][$m]."'");
						}else{
							$this->kclass->DB->query("
								UPDATE materialitem SET
									productid='".$productItem['productid']."',
									productitemid='".$this->kclass->input['productItemid'][$m]."',
									itemno='".$this->kclass->input['itemno'][$m]."',
									relatedid='".$this->kclass->input['itemRelatedid'][$m]."',
									addvalue='".$addValue."',
									attributevalue='".$value."',
									ordering='".$ordering."',
									modifier='".$this->kclass->user['userid']."',
									modified='".TIMENOW."'
								WHERE itemid='".$this->kclass->input['itemId'][$m]."'
							");
						}
					}else{
						$this->kclass->DB->query("
							INSERT INTO materialitem
								(materialid,productid,productitemid,itemno,relatedid,addvalue,attributevalue,ordering,created,creator)
							VALUES
								('".$this->kclass->input['materialid']."','".$productItem['productid']."','".$this->kclass->input['productItemid'][$m]."','".$this->kclass->input['itemno'][$m]."','".$this->kclass->input['itemRelatedid'][$m]."','".$addValue."','".$value."','".$ordering."','".TIMENOW."','".$this->kclass->user['userid']."')
						");
						$itemid=$this->kclass->DB->insertID();
						$version=$this->kclass->DB->queryFirst("SELECT COUNT(versionid) AS count FROM materialversion WHERE killed=0 AND itemid='".$itemid."' LIMIT 0,1");
						if(empty($version['count'])){
							$this->kclass->DB->query("
								INSERT INTO materialversion (materialid,itemid,title,created,creator)
								VALUES ('".$this->kclass->input['materialid']."','".$itemid."','01','".TIMENOW."','".$this->kclass->user['userid']."')
							");
						}
					}
				}
			}
		}

		$this->kclass->messager(array(
			'title' => '新建或修改物资明细',
			'text' => '物资明细 已新建成功!',
			'url' => '/s.php?module=material&action=view&materialid='.$this->kclass->input['materialid'],
			'sec' => 3
		));
	}
	function updateItemBelong(){
		$material=$this->kclass->DB->queryFirst("SELECT materialid,materialno,title,standard,attrid FROM material WHERE killed=0 AND materialid='".$this->kclass->input['materialid']."' LIMIT 0,1");
		if(!$material['materialid']){
			$e.='<li>数据错误</li>';
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '修改物资明细的归属产品',
				'text' => '您在修改物资明细的归属产品的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$locationMaterials=$this->kclass->DB->query("
			SELECT materialid,materialno,title,standard
			FROM material
			WHERE killed=0 AND attrid={$material['attrid']}
			ORDER BY materialno ASC 
		");
		if($this->kclass->DB->numRows()){
			while($locationMaterial=$this->kclass->DB->fetchArray($locationMaterials)){
				$locationSelect.='<option value="'.$locationMaterial['materialid'].'"';
				if($locationMaterial['materialid']==$material['materialid']){
					$locationSelect.=' selected';
				}
				$locationSelect.='>'.$locationMaterial['materialno'].'　'.$locationMaterial['title'].'　'.$locationMaterial['standard'].'</option>';
			}
		}
		$items=$this->kclass->DB->query("
			SELECT materialid,itemid
			FROM materialitem 
			WHERE killed=0 AND materialid='".$material['materialid']."'
			ORDER BY itemno ASC,created DESC
		");
		if($this->kclass->DB->numRows()){
			$i=1;
			while($item=$this->kclass->DB->fetchArray($items)){
				$materialInfo=$this->kclass->getMaterial(array('materialid'=>$item['materialid'],'itemid'=>$item['itemid']));
				$itemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').'">
					<td><input type="hidden" name="itemId['.$i.']" value="'.$i.'">
						<input type="hidden" name="materialItemId['.$i.']" value="'.$item['itemid'].'">
						'.$i.'</td>
					<td><a href="'.$materialInfo['url'].'">'.$materialInfo['no'].'　'.$materialInfo['material'].'　'.$materialInfo['standard'].'</td>
					<td><select name="itemMaterialid['.$i.']">'.$locationSelect.'</select></td>
					<td><input type="checkbox" name="itemSelect['.$i.']" value="'.$i.'" /></td>
				</tr>';
				$i++;
			}
		}

		$body = <<<EOF
<form action="/s.php?module=material&action=doupdateItemBelong" name="material" method="post">
<input type="hidden" name="module" value="material" />
<input type="hidden" name="action" value="doupdateItemBelong" />
<input type="hidden" name="materialid" value="{$material['materialid']}" />
<div class="title">{$material['materialno']}　{$material['title']}　{$material['standard']}</div>
<table style="width:100%;">
<thead><tr><th colspan="9">修改产品{$material['materialno']} 明细的归属信息</th></tr></thead>
<tbody>
<tr class="odd center">
	<td width="15">ID</td>
	<td>物资明细</td>
	<td width="150">目标产品物资 <span class="red bold">*</span></td>
	<td width="15">选</td>
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

		$this->kclass->page['title'] .= '修改物资明细的归属产品';
		$this->kclass->page['onload'] .= 'dc.tabhover()';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 修改物资明细的归属产品','right' => '<a href="/s.php?module=material&action=view&materialid='.$material['materialid'].'">返回</a>','body'=>$body));
	}
	function doupdateItemBelong(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e='<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			$count=count($this->kclass->input['itemId']);
			$hasItem=0;
			for($i=1;$i<=$count;$i++){			
				if($this->kclass->input['itemSelect'][$i]>0 AND $this->kclass->input['itemMaterialid'][$i]>0){
					$hasItem=1;
				}
			}
		}
		if($hasItem==0){
			$e.='<li>请选择至少一条明细</li>';
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '修改物资明细的归属产品',
				'text' => '您在修改物资明细的归属产品的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		for($i=1;$i<=$count;$i++){
			if($this->kclass->input['itemSelect'][$i]>0 AND $this->kclass->input['itemMaterialid'][$i]>0){
				$version=$this->kclass->DB->queryFirst("SELECT versionid FROM materialversion WHERE killed=0 AND itemid={$this->kclass->input['materialItemId'][$i]}");
				$this->kclass->DB->query("UPDATE materialitem SET materialid={$this->kclass->input['itemMaterialid'][$i]} WHERE itemid={$this->kclass->input['materialItemId'][$i]}"); // 修改material
				$this->kclass->DB->query("UPDATE materialversion SET materialid={$this->kclass->input['itemMaterialid'][$i]} WHERE itemid={$this->kclass->input['materialItemId'][$i]}"); // 修改materialversion
				$this->kclass->DB->query("UPDATE barcode SET materialid={$this->kclass->input['itemMaterialid'][$i]} WHERE versionid={$version['versionid']}"); // 修改barcode
				$this->kclass->DB->query("UPDATE batch SET materialid={$this->kclass->input['itemMaterialid'][$i]} WHERE materialitemid={$this->kclass->input['materialItemId'][$i]}"); // 修改batch
				$this->kclass->DB->query("UPDATE bom SET materialid={$this->kclass->input['itemMaterialid'][$i]} WHERE parentitemid={$this->kclass->input['materialItemId'][$i]}"); // 修改bom父级
				$this->kclass->DB->query("UPDATE bom SET childid={$this->kclass->input['itemMaterialid'][$i]} WHERE childItemid={$this->kclass->input['materialItemId'][$i]}"); // 修改bom子级
				$this->kclass->DB->query("UPDATE dispatchitem SET materialid={$this->kclass->input['itemMaterialid'][$i]} WHERE materialitemid={$this->kclass->input['materialItemId'][$i]}"); // 修改dispatchitem
				$this->kclass->DB->query("UPDATE inbounditem SET materialid={$this->kclass->input['itemMaterialid'][$i]} WHERE materialitemid={$this->kclass->input['materialItemId'][$i]}"); // 修改inbounditem
				$this->kclass->DB->query("UPDATE inoutrecord SET materialid={$this->kclass->input['itemMaterialid'][$i]} WHERE materialitemid={$this->kclass->input['materialItemId'][$i]}"); // 修改inoutrecord
				$this->kclass->DB->query("UPDATE mrp SET materialid={$this->kclass->input['itemMaterialid'][$i]} WHERE materialitemid={$this->kclass->input['materialItemId'][$i]}"); // 修改inoutrecord
				$this->kclass->DB->query("UPDATE outbounditem SET materialid={$this->kclass->input['itemMaterialid'][$i]} WHERE materialitemid={$this->kclass->input['materialItemId'][$i]}"); // 修改outbounditem
				$this->kclass->DB->query("UPDATE preinbounditem SET materialid={$this->kclass->input['itemMaterialid'][$i]} WHERE materialitemid={$this->kclass->input['materialItemId'][$i]}"); // 修改preinbounditem
				$this->kclass->DB->query("UPDATE preoutbounditem SET materialid={$this->kclass->input['itemMaterialid'][$i]} WHERE materialitemid={$this->kclass->input['materialItemId'][$i]}"); // 修改preoutbounditem
				$this->kclass->DB->query("UPDATE requirementitem SET materialid={$this->kclass->input['itemMaterialid'][$i]} WHERE materialitemid={$this->kclass->input['materialItemId'][$i]}"); // 修改requirementitemord
				$this->kclass->DB->query("UPDATE taskitem SET materialid={$this->kclass->input['itemMaterialid'][$i]} WHERE materialitemid={$this->kclass->input['materialItemId'][$i]}"); // 修改taskitem
				$this->kclass->DB->query("UPDATE workcenteritem SET materialid={$this->kclass->input['itemMaterialid'][$i]} WHERE materialitemid={$this->kclass->input['materialItemId'][$i]}"); // 修改workcenteritem
			}
		}
		$this->kclass->messager(array(
			'title' => '修改物资明细的归属产品',
			'text' => '物资明细的归属产品 已修改成功!',
			'url' => '/s.php?module=material&action=view&materialid='.$this->kclass->input['materialid'],
			'sec' => 3
		));
	}
	//
	function updateItemVersion(){
		$item=$this->kclass->DB->queryFirst("SELECT mi.itemid,m.materialid,m.materialno,m.title,m.standard,m.attrid FROM materialitem AS mi LEFT JOIN material AS m ON (m.materialid=mi.materialid) WHERE mi.killed=0 AND mi.itemid='".$this->kclass->input['itemid']."' LIMIT 0,1");
		if(!$item['itemid']){
			$e.='<li>数据错误</li>';
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '新建或者修改物资条目版本',
				'text' => '您在新建或者修改物资条目版本的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$materialItem=$this->kclass->getMaterial(array('materialid'=>$item['materialid'],'itemid'=>$item['itemid']));
		$k=1;
		$versions=$this->kclass->DB->query("
			SELECT versionid,title,`change`,quantity,qualified,disqualified
			FROM materialversion
			WHERE killed=0 AND materialid=".$item['materialid']." AND itemid='".$this->kclass->input['itemid']."'
		");
		if($this->kclass->DB->numRows()){
			while($version=$this->kclass->DB->fetchArray($versions)){
				if($version['ifProduce']==1){
					$checked1=' checked';
					$checked2=' ';
				}elseif($version['ifProduce']==0){
					$checked1=' ';
					$checked2=' checked';
				}

				$itemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').' small">
					<td><input type="hidden" name="itemId['.$k.']" value="'.$version['versionid'].'" />'.$k.'</td>
					<td><input type="text" name="itemVersion['.$k.']" style="width:40px" value="'.$version['title'].'" /></td>
					<td><input type="text" name="itemChange['.$k.']" style="width:650px" value="'.$version['change'].'" /></td>
					<td><input type="text" name="itemQuantity['.$k.']" style="width:70px" value="'.$version['quantity'].'" /></td>
					<td><input type="text" name="itemQualified['.$k.']" style="width:70px" value="'.$version['qualified'].'" /></td>
					<td><input type="text" name="itemDisqualified['.$k.']" style="width:70px" value="'.$version['disqualified'].'" /></td>
					<td><input type="radio" name="ifProduce['.$k.']" value="1" '.$checked1.' />是 <input type="radio" name="ifProduce['.$k.']" value=0 '.$checked2.' />否 </td>
					<td><input type="checkbox" name="itemKill['.$k.']" value="'.$version['versionid'].'" /></td>
				</tr>';
				$k++;
			}
			$itemtr.='<tr><td colspan=9>新增明细</td></tr>';
		}
		for($i=$k;$i<$k+5;$i++){
			$itemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').' small">
				<td>'.$i.'</td>
				<td><input type="text" name="itemVersion['.$i.']" value="" style="width:40px"/></td>
				<td><input type="text" name="itemChange['.$i.']" style="width:650px"></td>
				<td><input type="text" name="itemQuantity['.$i.']" style="width:70px" value="" /></td>
				<td><input type="text" name="itemQualified['.$i.']" style="width:70px" value="" /></td>
				<td><input type="text" name="itemDisqualified['.$i.']" style="width:70px" value="" /></td>
				<td><input type="radio" name="ifProduce['.$i.']" value="1" checked />是 <input type="radio" name="ifProduce['.$i.']" value=0>否 </td>
				<td></td>
			</tr>';
		}
$body = <<<EOF
<form action="/s.php?module=material&action=doupdateItemVersion" name="material" method="post">
<input type="hidden" name="module" value="material" />
<input type="hidden" name="action" value="doupdateItemVersion" />
<input type="hidden" name="materialid" value="{$item['materialid']}" />
<input type="hidden" name="itemid" value="{$this->kclass->input['itemid']}" />
<div class="title">{$materialItem['materialno']}　{$materialItem['material']}　{$materialItem['standard']}</div>
<table style="width:100%;">
<thead><tr><th colspan="9">添加产品 {$materialItem['material']} 条目信息</th></tr></thead>
<tbody>
<tr class="odd center">
	<td width="15">ID</td>
	<td width="50">版本 <span class="red bold">*</span></td>
	<td>版本说明</td>
	<td width="70">总数 <span class="red bold">*</span></td>
	<td width="70">良品数 <span class="red bold">*</span></td>
	<td width="70">不良品数 <span class="red bold">*</span></td>
	<td width="80">是否生产 <span class="red bold">*</span></td>
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

		$this->kclass->page['title'] .= '新建或者修改物资条目版本';
		$this->kclass->page['onload'] .= 'dc.tabhover()';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 新建或者修改物资条目版本','right' => '<a href="/s.php?module=material&action=view&materialid='.$materialItem['materialid'].'">返回</a>','body'=>$body));
	}

	//
	function doupdateItemVersion(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e='<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			$hasItem=0;
			$j=count($this->kclass->input['itemVersion']);
			for($i=1;$i<=$j;$i++){
				if($this->kclass->input['itemVersion'][$i]!='' AND $this->kclass->input['itemQuantity'][$i]>=0 AND $this->kclass->input['itemQualified'][$i]>=0 AND $this->kclass->input['itemDisqualified'][$i]>=0 AND ($this->kclass->input['ifProduce'][$i]==1 OR $this->kclass->input['ifProduce'][$i]==0)){
					if(!$this->kclass->input['itemId'][$i]){
						$version1=$this->kclass->DB->queryFirst("SELECT versionid FROM `materialversion` WHERE killed=0 AND itemid='".$this->kclass->input['itemid']."' AND title='".$this->kclass->input['itemVersion'][$i]."' LIMIT 0,1");
						if($version1['versionid']>0){
							$e.='<li>新建的条目 的版本 '.$this->kclass->input['itemVersion'][$i].' 已存在。</li>';
						}
					}
					if($this->kclass->input['itemKill'][$i]!='' AND $this->kclass->input['itemKill'][$i]==$this->kclass->input['itemId'][$i]){
						$version2=$this->kclass->DB->queryFirst("SELECT versionid FROM `materialversion` WHERE killed=0 AND itemid='".$this->kclass->input['itemid']."' AND versionid<>'".$this->kclass->input['itemKill'][$i]."' LIMIT 0,1");
						if(!$version2['versionid']){
							$e.='<li>不可删除。删除后，该物资将没有版本。</li>';
						}
					}
					if($this->kclass->input['itemKill'][$i]==''){
						$hasItem=1;
					}
				}
			}
			if($hasItem==0){
				$e.='<li>请填写 完整的产品明细</li>';
			}
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '新建或者修改物资条目版本',
				'text' => '您在新建或者修改物资条目版本的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}

		for($m=1;$m<=$j;$m++){
			if($this->kclass->input['itemVersion'][$m]!='' AND $this->kclass->input['itemQuantity'][$m]>=0 AND $this->kclass->input['itemQualified'][$m]>=0 AND $this->kclass->input['itemDisqualified'][$m]>=0){
				if($this->kclass->input['itemId'][$m]>0){
					if($this->kclass->input['itemKill'][$m]!='' AND $this->kclass->input['itemKill'][$m]==$this->kclass->input['itemId'][$m]){
						$this->kclass->DB->query("UPDATE materialversion SET killed='".TIMENOW."',killer='".$this->kclass->user['userid']."' WHERE versionid='".$this->kclass->input['itemId'][$m]."'");
					}else{
						$this->kclass->DB->query("
							UPDATE materialversion SET
								`title`='".$this->kclass->input['itemVersion'][$m]."',
								`change`='".$this->kclass->input['itemChange'][$m]."',
								`quantity`='".$this->kclass->input['itemQuantity'][$m]."',
								`qualified`='".$this->kclass->input['itemQualified'][$m]."',
								`disqualified`='".$this->kclass->input['itemDisqualified'][$m]."',
								`ifProduce`='".$this->kclass->input['ifProduce'][$m]."',
								`modifier`='".$this->kclass->user['userid']."',
								`modified`='".TIMENOW."'
							WHERE versionid='".$this->kclass->input['itemId'][$m]."'
						");
						$versionid=$this->kclass->input['itemId'][$m];
					}
				}else{
					$this->kclass->DB->query("
						INSERT INTO materialversion
							(`materialid`,`itemid`,`title`,`change`,`quantity`,`qualified`,`disqualified`,`ifProduce`,`created`,`creator`)
						VALUES
							('".$this->kclass->input['materialid']."','".$this->kclass->input['itemid']."','".$this->kclass->input['itemVersion'][$m]."','".$this->kclass->input['itemChange'][$m]."','".$this->kclass->input['itemQuantity'][$m]."','".$this->kclass->input['itemQualified'][$m]."','".$this->kclass->input['itemDisqualified'][$m]."','".$this->kclass->input['ifProduce'][$m]."','".TIMENOW."','".$this->kclass->user['userid']."')
					");
					$versionid=$this->kclass->DB->insertID();
				}
			}
		}
		$this->kclass->messager(array(
			'title' => '新建产品条目',
			'text' => '产品条目 已新建成功!',
			'url' => '/s.php?module=material&action=view&materialid='.$this->kclass->input['materialid'],
			'sec' => 3
		));
	}
	//
	function updatePacking(){
		if($this->kclass->input['materialid']<=0){
			$e='<li>数据错误，很抱歉</li>';
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '新建或者修改物资条目版本',
				'text' => '您在新建或者修改物资条目版本的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		if($this->kclass->input['type']=='item'){
			$item=$this->kclass->DB->queryFirst("
				SELECT versionid,title,materialid,itemid
				FROM materialversion 
				WHERE versionid={$this->kclass->input['versionid']}
				LIMIT 0,1
			");
			$materialInfo=$this->kclass->getMaterial(array('materialid'=>$item['materialid'],'itemid'=>$item['itemid']));
			$packingSizes=$this->kclass->DB->query("
				SELECT title,length,width,height,volume,cartonNum,weight,remark
				FROM packingsize 
				WHERE killed=0 AND versionid={$item['versionid']}
				ORDER BY ordering DESC
			");
			$k=1;
			if($this->kclass->DB->numRows()){
				while($packingSize=$this->kclass->DB->fetchArray($packingSizes)){
					$itemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').'">
						<td><input type="hidden" name="id['.$k.']" value="'.$k.'">
							<input type="hidden" name="itemId['.$k.']" value="'.$packingSize['packingsizeid'].'">'.$k.'</td>
						<td>长：<input type="text" name="itemLength['.$k.']" value="'.$packingSize['length'].'">　
							宽：<input type="text" name="itemWidth['.$k.']" value="'.$packingSize['width'].'">　
							高：<input type="text" name="itemHeight['.$k.']" value="'.$packingSize['height'].'">　</td>
						<td><input type="text" name="itemVolume['.$k.']" value="'.$packingSize['volume'].'"></td>
						<td><input type="text" name="itemCartonNum['.$k.']" value="'.$packingSize['cartonNum'].'"></td>
						<td><input type="text" name="itemWeight['.$k.']" value="'.$packingSize['weight'].'"></td>
						<td><input type="text" name="itemRemark['.$k.']" value="'.$packingSize['remark'].'"></td>
						<td><input type="text" name="itemKill['.$k.']" value="'.$packingSize['packingsizeid'].'"></td>
					</tr>';
				}
			}
			for($i=$k;$i<=$k+2;$i++){
				$itemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').'">
					<td><input type="hidden" name="id['.$i.']" value="'.$i.'">'.$i.'</td>
					<td>长：<input type="text" name="itemLength['.$i.']" value="">　
						宽：<input type="text" name="itemWidth['.$i.']" value="">　
						高：<input type="text" name="itemHeight['.$i.']" value="">　</td>
					<td><input type="text" name="itemVolume['.$i.']" value=""></td>
					<td><input type="text" name="itemCartonNum['.$i.']" value=""></td>
					<td><input type="text" name="itemWeight['.$i.']" value=""></td>
					<td><input type="text" name="itemRemark['.$i.']" value=""></td>
					<td></td>
				</tr>';
			}
			$body=<<<EOF
<form action="/s.php?module=material&action=doupdatePacking" name="material" method="post">
<input type="hidden" name="module" value="material" />
<input type="hidden" name="action" value="doupdatePacking" />
<input type="hidden" name="materialid" value="{$item['materialid']}" />
<input type="hidden" name="itemid" value="{$item['itemid']}" />
<div class="title">{$materialInfo['no']}　{$materialInfo['title']}　{$materialInfo['standard']}</div>
<table style="width:100%;">
<thead><tr><th colspan="11">添加产品 <a href="{$materialInfo['url']}">{$materialInfo['no']} {$materialInfo['title']} {$materialInfo['standard']}</a> 的包装信息</th></tr></thead>
<tbody>
<tr class="even center bold"><td width="15">ID</td><td>箱规格 (米 m) <span class="bold red">*</span></td><td>体积(平方米 ㎡) <span class="bold red">*</span></td><td>件/箱 <span class="bold red">*</span></td><td>箱重 (㎏) <span class="bold red">*</span></td><td>备注</td><td width="15">删</td></tr>
{$itemtr}
<tr class="even">
<td class="small gray" colspan="11">注意事项：确认信息正确无误</td>
</tr>
<tr class="even" nohover>
<td class="center" colspan=11><input type="submit" id="submitButton" value="  提交  "><input type="reset" value="  复位  "/></td>
</tr>
</tbody>
</table>
</form>
EOF;
			$this->kclass->page['title'] .= '修改产品包装';
			$this->kclass->page['onload'] .= 'dc.tabhover()';
			$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 修改产品包装','right' => '<a href="/s.php?module=material&action=view&materialid='.$this->kclass->input['materialid'].'">返回</a>','body'=>$body));
		}elseif($this->kclass->input['type']=='all'){
			
		}
	}
	//
	function doupdatePacking(){
		if($this->kclass->input['requestMethod']!='post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			$j=count($this->kclass->input['id']);
			$hasItem=0;
			for($i=1;$i<=$j;$i++){
				if($this->kclass->input['itemLength'][$i]>0 AND $this->kclass->input['itemWidth'][$i]>0 AND $this->kclass->input['itemHeight'][$i]>0 AND $this->kclass->input['itemVolume'][$i]>0 AND $this->kclass->input['itemCartonNum'][$i]>0 AND $this->kclass->input['itemWeight'][$i]>0){
					$hasItem=1;
				}
			}
			if($hasItem==0){
				$e='<li>请输入完整包装数据</li>';
			}
		}
		if($e){
			$this->kclass->messager(array(
				'title' => '修改物资',
				'text' => '您在修改物资的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		for($i=1;$i<=$j;$i++){
			if($this->kclass->input['itemLength'][$i]>0 AND $this->kclass->input['itemWidth'][$i]>0 AND $this->kclass->input['itemHeight'][$i]>0 AND $this->kclass->input['itemVolume'][$i]>0 AND $this->kclass->input['itemCartonNum'][$i]>0 AND $this->kclass->input['itemWeight'][$i]>0){
				$hasItem=1;
			}
		}
	}
	function updatePrice(){
		if($this->kclass->input['materialid']=='' OR $this->kclass->input['versionid']==''){
			$e.='<li>数据错误，很抱歉</li>';
		}
		$version=$this->kclass->DB->queryFirst("
			SELECT mv.materialid,mv.versionid,mv.title AS version,mv.itemid,mv.price,mv.price1
			FROM materialversion AS mv
			LEFT JOIN material AS m ON (m.materialid=mv.materialid)
			WHERE mv.materialid={$this->kclass->input['materialid']} AND mv.versionid={$this->kclass->input['versionid']}
			LIMIT 0,1
		");
		if(!$version){
			$e.='<li>不存在该物资版本</li>';
		}
		if($e){
			$this->kclass->messager(array(
				'title' => '修改物资单价',
				'text' => '您在修改物资单价的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$materialInfo=$this->kclass->getMaterial(array('materialid'=>$version['materialid'],'itemid'=>$version['itemid']));
		if(!$this->kclass->input['type']){
			$body=<<<EOF
<form action="/s.php?module=material&action=updatePrice" method="get">
<input type="hidden" name="module" value="material" />
<input type="hidden" name="action" value="updatePrice" />
<input type="hidden" name="materialid" value="{$this->kclass->input['materialid']}" />
<input type="hidden" name="versionid" value="{$this->kclass->input['versionid']}" />
<table style="width:650px">
<thead><tr><th colspan=2>选择价格更新方式</th></tr></thead>
<tbody>
<tr><td>选择价格更新方式</td><td><input type="radio" name="type" value="1">直接更新<input type="radio" name="type" value="2">加权平均算法更新</td></tr>
<tr><td colspan=2 align="center"><input type="submit" value="确定"></td></tr>
</tbody>
</table>
</form>
EOF;
			$this->kclass->page['title'] .= '手动更新单价';
			$this->kclass->page['onload'] .= 'dc.tabhover()';
			$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - <a href="'.$materialInfo['url'].'">物资详细</a> - 修改物资单价','right' => '<a href="'.$materialInfo['url'].'">返回</a>','body'=>$body));
		}else{
			if($this->kclass->input['type']==1){
				$itemtr='<tr class="even center"><td>当前物资单价:<span class="bold red">*</span></td><td><input type="text" name="price" value="'.$version['price'].'" size="10"></td><td>加工费：<span class="bold red">*</span></td><td><input type="text" name="price1" value="'.$version['price1'].'" size="10"><span class="small gray">非外发物资加工费默认为0</span></td><td>修改原因</td><td><input type="text" name="reason" size="100"></td><td class="center" ><input type="submit" id="submitButton" value="  提交  "></td></tr>';
			}else{
				$porderItems=$this->kclass->DB->query("
					SELECT i.itemid,i.materialid,i.versionid,i.quantity,i.price,
						po.porderid,po.orderno
					FROM item AS i
					LEFT JOIN porder AS po ON (po.porderid=i.mid)
					WHERE po.killed=0 AND i.killed=0 AND i.module='porder' AND i.versionid={$version['versionid']}
					ORDER BY po.orderno DESC
				");
				if($this->kclass->DB->numRows()){
					$itemtr.='<tr class="center"><td width="30">ID</td><td>订单</td><td>数量</td><td>单价</td><td>入库时间</td><td width="15">选</td></tr>';
					while($porderItem=$this->kclass->DB->fetchArray($porderItems)){
						$i=1;
						$inItem=$this->kclass->DB->queryFirst("
							SELECT SUM(ii.quantity) AS count,ii.dateTime 
							FROM inbounditem AS ii
							LEFT JOIN inbound AS i ON (i.inboundid=ii.inboundid)
							WHERE ii.killed=0 AND i.killed=0 AND ii.ifPrice=0 AND i.porderid={$porderItem['porderid']} AND ii.versionid={$version['versionid']}
						");
						$itemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').'">
							<td><input type="hidden" name="itemId['.$i.']" value="'.$porderItem['itemid'].'">
							<input type="hidden" name="itemQuantity['.$i.']" value="'.$inItem['count'].'">'.$i.'</td>
							<td><a href="/s.php?module=porder&action=view&porderid='.$porderItem['porderid'].'">'.$porderItem['orderno'].'</a></td>
							<td>'.$inItem['count'].'</td>
							<td>'.$porderItem['price'].'</td>
							<td>'.date('Y-m-d',$inItem['dateTime']).'</td>
							<td><input type="checkbox" name="itemSelect['.$i.']" value="'.$porderItem['itemid'].'"></td>
						</tr>';
						$i++;
					}
					$itemtr.='<tr><td class="center" colspan=6><input type="submit" id="submitButton" value="  提交  "></td></tr>';
				}
			}
			$body=<<<EOF
<form action="/s.php?module=material&action=doupdatePrice" name="material" method="post">
<input type="hidden" name="module" value="material" />
<input type="hidden" name="action" value="doupdatePrice" />
<input type="hidden" name="materialid" value="{$version['materialid']}" />
<input type="hidden" name="versionid" value="{$version['versionid']}" />
<input type="hidden" name="type" value="{$this->kclass->input['type']}" />
<div class="title">{$materialInfo['no']}　{$materialInfo['title']}　{$materialInfo['standard']}</div>
<table style="width:100%;">
<thead><tr><th colspan="11">修改物资 <a href="{$materialInfo['url']}">{$materialInfo['no']} {$materialInfo['title']} {$materialInfo['standard']}</a> 的价格　当前价格：<span class="red">{$version['price']}</span></th></tr></thead>
<tbody>
{$itemtr}
</tbody>
</table>
</form>
EOF;
			$this->kclass->page['title'] .= '修改物资单价';
			$this->kclass->page['onload'] .= 'dc.tabhover()';
			$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - <a href="'.$materialInfo['url'].'">物资详细</a> - 修改物资单价','right' => '<a href="'.$materialInfo['url'].'">返回</a>','body'=>$body));
		}
	}
	function doupdatePrice(){
		if($this->kclass->input['requestMethod']!='post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			if($this->kclass->input['type']==1){
				if($this->kclass->input['price']<=0){
					$e='<li>请输入正确的价格</li>';
				}
				if($this->kclass->input['price1']<0){
					$e='<li>请输入正确的加工费</li>';
				}
			}elseif($this->kclass->input['type']==2){
				if(!$this->kclass->input['itemSelect']){
					$e='<li>请先勾选该物资 已入库的 订单明细 </li>';
				}
			}
		}
		if($e){
			$this->kclass->messager(array(
				'title' => '修改物资',
				'text' => '您在修改物资的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		if($this->kclass->input['type']==1){
			$price=floatval($this->kclass->input['price']);
			$price1=floatval($this->kclass->input['price1']);
			// insert price record
			$this->kclass->DB->query("
				INSERT INTO pricerecord (materialid,versionid,price,price1,created,creator)
				VALUES ('{$this->kclass->input['materialid']}','{$this->kclass->input['versionid']}','{$price}','{$price1}','".TIMENOW."','{$this->kclass->user['userid']}')
			");
			// update the current price 
			$this->kclass->DB->query("UPDATE materialversion SET price={$price},price1={$price1} WHERE versionid={$this->kclass->input['versionid']}");
		}elseif($this->kclass->input['type']==2){
			$count=count($this->kclass->input['itemId']);
			for($i=1;$i<=$count;$i++){
				if($this->kclass->input['itemSelect'][$i]>0 AND $this->kclass->input['itemSelect'][$i]==$this->kclass->input['itemId'][$i]){
					$porderItem=$this->kclass->DB->queryFirst("
						SELECT i.itemid,i.mid,i.materialid,i.versionid,i.quantity,i.price,
							mv.quantity AS oldQuantity,mv.price AS oldPrice
						FROM item AS i
						LEFT JOIN materialversion AS mv ON (mv.versionid=i.versionid)
						WHERE i.killed=0 AND i.itemid={$this->kclass->input['itemId'][$i]}
						LIMIT 0,1
					");

					$price=sprintf("%.4f",($porderItem['oldPrice']*$porderItem['oldQuantity']+$porderItem['price']*$this->kclass->input['itemQuantity'][$i])/($porderItem['oldQuantity']+$this->kclass->input['itemQuantity'][$i]));
					// insert price record
					$this->kclass->DB->query("
						INSERT INTO pricerecord (materialid,versionid,price,created,creator)
						VALUES ('{$this->kclass->input['materialid']}','{$this->kclass->input['versionid']}','{$price}','".TIMENOW."','{$this->kclass->user['userid']}')
					");
					// update the current price 
					$this->kclass->DB->query("UPDATE materialversion SET price={$price} WHERE versionid={$this->kclass->input['versionid']}");
					// update the inbounditem price status
					$inItems=$this->kclass->DB->query("
						SELECT ii.itemid
						FROM inbounditem AS ii
						LEFT JOIN inbound AS i ON (i.inboundid=ii.inboundid)
						WHERE i.killed=0 AND ii.killed=0 AND i.porderid={$porderItem['mid']} AND ii.versionid={$porderItem['versionid']}
					");
					if($this->kclass->DB->numRows()){
						while($inItem=$this->kclass->DB->fetchArray($inItems)){
							$this->kclass->DB->query("UPDATE inbounditem SET ifPrice=1 WHERE itemid={$inItem['itemid']}");
						}
					}
				}
			}
		}
		$this->kclass->messager(array(
			'title' => '修改物资单价',
			'text' => '物资单价 已修改成功!',
			'url' => '/s.php?module=material&action=view&materialid='.$this->kclass->input['materialid'],
			'sec' => 3
		));
	}
	function viewPrice(){
		if($this->kclass->input['materialid']<=0 OR $this->kclass->input['versionid']<=0){
			$e.='<li>数据错误，很抱歉</li>';
		}
		$version=$this->kclass->DB->queryFirst("
			SELECT mv.materialid,mv.versionid,mv.title AS version,mv.itemid,mv.price,mv.price1
			FROM materialversion AS mv
			LEFT JOIN material AS m ON (m.materialid=mv.materialid)
			WHERE mv.materialid={$this->kclass->input['materialid']} AND mv.versionid={$this->kclass->input['versionid']}
			LIMIT 0,1
		");
		if(!$version){
			$e.='<li>不存在该物资版本</li>';
		}
		$materialInfo=$this->kclass->getMaterial(array('materialid'=>$version['materialid'],'itemid'=>$version['itemid']));
		$currentPrice=$this->kclass->DB->queryFirst("
			SELECT pr.recordid,pr.price,pr.price1,pr.created,pr.quantity,i.inboundid,i.inboundno,m.realname AS creator
			FROM pricerecord AS pr
			LEFT JOIN inbound AS i ON (i.inboundid=pr.inboundid)
			LEFT JOIN member AS m ON (m.userid=pr.creator)
			WHERE pr.killed=0 AND materialid={$version['materialid']} AND versionid={$version['versionid']}
			ORDER BY created DESC 
			LIMIT 0,1
		");
		if(!$currentPrice){
			$e.='<li>请先修改单价 点击<a href="/s.php?module=material&action=updatePrice&materialid='.$version['materialid'].'&versionid='.$version['versionid'].'">此处</a> 修改价格</li>';
		}
		if($e){
			$this->kclass->messager(array(
				'title' => '修改物资单价',
				'text' => '您在修改物资单价的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}

		$current='<tr><td>0</td><td class="red bold">'.$currentPrice['price'].'</td><td>'.$currentPrice['price1'].'</td><td><a href="/s.php?module=inbound&action=view&inboundid='.$currentPrice['inboundid'].'">'.$currentPrice['inboundno'].'</a></td><td>'.$currentPrice['quantity'].'</td><td>'.$currentPrice['creator'].'</td><td>'.date('Y-m-d H:i:s',$currentPrice['created']).'</td></tr>';
		$historyPrices=$this->kclass->DB->query("
			SELECT pr.recordid,pr.price,pr.price1,pr.created,pr.quantity,i.inboundid,i.inboundno,m.realname AS creator
			FROM pricerecord AS pr
			LEFT JOIN inbound AS i ON (i.inboundid=pr.inboundid)
			LEFT JOIN member AS m ON (m.userid=pr.creator)
			WHERE pr.killed=0 AND materialid={$version['materialid']} AND versionid={$version['versionid']} AND pr.recordid<>{$currentPrice['recordid']}
			ORDER BY created DESC 
		");
		if($this->kclass->DB->numRows()){
			$i=1;
			$history='<tr class="bold middle"><td colspan=7>历史价格</td></tr>';
			while($historyPrice=$this->kclass->DB->fetchArray($historyPrices)){
				$history.='<tr><td>'.$i.'</td><td class="red bold">'.$historyPrice['price'].'</td><td>'.$historyPrice['price1'].'</td><td><a href="/s.php?module=inbound&action=view&inboundid='.$historyPrice['inboundid'].'">'.$historyPrice['inboundno'].'</a></td><td>'.$historyPrice['quantity'].'</td><td>'.$historyPrice['creator'].'</td><td>'.date('Y-m-d H:i:s',$historyPrice['created']).'</td></tr>';
				$i++;
			}
		}

		$body=<<<EOF
<table style="width:800px;">
<thead><tr><th colspan=7>物资 <a href="{$materialInfo['url']}">{$materialInfo['no']}　{$materialInfo['material']}　{$materialInfo['standard']}</a> 价格记录</th></tr></thead>
<tbody>
<tr><td width="15">ID</td><td>总单价</td><td>素材单价</td><td>相关入库单</td><td>相关数量</td><td>修订人</td><td>修订时间</td></tr>
<tr class="bold middle"><td colspan=7>当前价格</td></tr>
{$current}
{$history}
</tbody>
</table>
EOF;

		$this->kclass->page['title'] .= '查看历史价格';
		$this->kclass->page['onload'] .= 'dc.tabhover()';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - <a href="'.$materialInfo['url'].'">物资详细</a> - 查看历史价格','right' => '<a href="/s.php?module=material&action=view&materialid='.$this->kclass->input['materialid'].'">返回</a>','body'=>$body));
	}

	// 计算半成品成本价。
	function countPrice(){
		$parents=$this->kclass->DB->query("
			SELECT DISTINCT `bom`.parentVersionid,`bom`.materialid,`bom`.parentitemid,mv.title AS version,mv.`change` 
			FROM `bom` 
			LEFT JOIN materialversion AS mv ON (mv.versionid=`bom`.parentVersionid) 
			LEFT JOIN material AS m ON (m.materialid=`bom`.materialid)
			WHERE `bom`.killed=0 
			ORDER BY m.materialno ASC,`bom`.ordering ASC, `bom`.bomid ASC 
		");
		if($this->kclass->DB->numRows()){
			while($parent=$this->kclass->DB->fetchArray($parents)){
				$childBoms=$this->kclass->bomChildList(array('parentid'=>$parent['materialid'],'materialitemid'=>$parent['parentitemid'],'versionid'=>$parent['parentVersionid'],'loopNum'=>1));
				$price=0;
				foreach($childBoms['bom'] AS $childBom){
					if($childBom['ifGeneral']==0){
						$price+=$childBom['price']*$childBom['quantity'];
					}
				}
				$this->kclass->DB->query("UPDATE materialversion SET price={$price} WHERE versionid={$parent['parentVersionid']}");
			}
		}
	}
		// 更新所有子BOM的价格
	function updatePriceByBom(){
		$version=$this->kclass->DB->queryFirst("SELECT versionid,materialid,itemid,price1 FROM materialversion WHERE versionid={$this->kclass->input['versionid']}");
		$children=$this->kclass->bomChildList(array('parentid'=>$version['materialid'],'materialitemid'=>$version['itemid'],'versionid'=>$version['versionid']));
		$bom=array();
		foreach($children['bom'] AS $child){
			$rank[]=$child['rank'];
			$bom[$child['rank']][]=$child;
		}
		$rank=array_unique($rank);
		for($i=count($rank);$i>0;$i--){
			$price=0;
			$rankBoms=$bom[$i];
			foreach($rankBoms AS $rankBom){
				$parentPrice[$rankBom['parentVersionid']][]=array('materialid'=>$rankBom['materialid'],'versionid'=>$rankBom['versionid'],'price'=>$rankBom['price'],'price1'=>$rankBom['price1'],'quantity'=>$rankBom['perQuantity'],'ifPrice'=>$rankBom['ifPrice'],'qualified'=>$rankBom['qualified']);
				$parentVersionid[]=$rankBom['parentVersionid'];
			}
		}
		$parentVersionid=array_unique($parentVersionid);
		foreach($parentVersionid AS $v){
			$price=0;
			foreach($parentPrice[$v] AS $p){
				if($p['ifPrice']==1){
					$price+=$p['price']*$p['quantity']+$p['price1'];
				}
			}
			$price=sprintf("%.4f",$price);
			// insert price record
			/*$this->kclass->DB->query("
				INSERT INTO pricerecord (materialid,versionid,price,quantity,created,creator)
				VALUES ('{$p['materialid']}','{$p['versionid']}','{$price}','{$p['qualified']}','".TIMENOW."','{$this->kclass->user['userid']}')
			");*/
			$this->kclass->DB->query("UPDATE materialversion SET price={$price} WHERE versionid={$v}");
		}
		$this->kclass->messager(array(
			'title' => '手动更新单价',
			'text' => '物资单价 已手动更新成功!',
			'url' => $materialInfo['url'],
			'sec' => 3
		));
	}
	/*// 一键更改所有物料的价格
	function updateAllPrice(){
		$items=$this->kclass->DB->query("
			SELECT mv.versionid 
			FROM materialversion AS mv
			LEFT JOIN material AS m ON (m.materialid=mv.materialid)
			WHERE mv.killed=0 AND m.killed=0 AND m.attrid=8
			ORDER BY mv.versionid ASC
		");
		if($this->kclass->DB->numRows()){
			while($item=$this->kclass->DB->fetchArray($items)){
				$version=$this->kclass->DB->queryFirst("SELECT versionid,materialid,itemid FROM materialversion WHERE versionid={$item['versionid']}");
				$children=$this->kclass->bomChildList(array('parentid'=>$version['materialid'],'materialitemid'=>$version['itemid'],'versionid'=>$version['versionid']));
				$bom=array();
				foreach($children['bom'] AS $child){
					$rank[]=$child['rank'];
					$bom[$child['rank']][]=$child;
				}
				$rank=array_unique($rank);
				for($i=count($rank);$i>0;$i--){
					$price=0;
					$rankBoms=$bom[$i];
					foreach($rankBoms AS $rankBom){
						$parentPrice[$rankBom['parentVersionid']][]=array('materialid'=>$rankBom['materialid'],'versionid'=>$rankBom['versionid'],'price'=>$rankBom['price'],'quantity'=>$rankBom['perQuantity'],'ifPrice'=>$rankBom['ifPrice'],'qualified'=>$rankBom['qualified']);
						$parentVersionid[]=$rankBom['parentVersionid'];
					}
				}
				$parentVersionid=array_unique($parentVersionid);
				foreach($parentVersionid AS $v){
					$price=0;
					foreach($parentPrice[$v] AS $p){
						if($p['ifPrice']==1){
							$price+=sprintf("%.4f",$p['price']*$p['quantity']);
						}
					}

					// insert price record
					$this->kclass->DB->query("
						INSERT INTO pricerecord (materialid,versionid,price,quantity,created,creator)
						VALUES ('{$p['materialid']}','{$p['versionid']}','{$price}','{$p['qualified']}','".TIMENOW."','{$this->kclass->user['userid']}')
					");
					$this->kclass->DB->query("UPDATE materialversion SET price={$price} WHERE versionid={$v}");
				}
			}
		}
	}*/
	
	function updatePriceStatus(){
		$version=$this->kclass->DB->queryFirst("SELECT versionid,materialid,itemid,price FROM materialversion WHERE versionid={$this->kclass->input['versionid']}");
		if(!$version){
			$e.='<li>数据错误，很抱歉</li>';
		}
		if($e){
			$this->kclass->messager(array(
				'title' => '修改物资单价状态',
				'text' => '您在修改物资单价状态的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$materialInfo=$this->kclass->getMaterial(array('materialid'=>$version['materialid'],'itemid'=>$version['itemid']));
		$body=<<<EOF
<form method="post" action="/s.php?module=material&action=doupdatePriceStatus">
<input type="hidden" name="module" value="material">
<input type="hidden" name="action" value="doupdatePriceStatus">
<input type="hidden" name="versionid" value="{$version['versionid']}">
<table style="width:700px">
<thead><tr><th colspan=6>修改产品 【<a href="{$materialInfo['url']}">{$materialInfo['no']}　{$materialInfo['material']}　{$materialInfo['standard']}</a>】 单价状态</th></tr></thead>
<tbody>
<tr><td>当前单价：</td><td>{$version['price']}</td></tr>
<tr><td>状　　态：</td><td><input type="radio" name="priceStatus" value="1" >√ <span class="small">(可用)</span>　<input type="radio" name="priceStatus" value="2" >! <span class="small">(有问题)</span>　<input type="radio" name="priceStatus" value="3" >× <span class="small">(不可用)</span></td></tr>
<tr class="odd center" nohover>
<td colspan=6><input type="submit" id="submitButton" value="  提交  " accesskey="s" onclick="dc.supplier.check(document.supplier)"><input type="reset" value="  复位  "/></td>
</tr>
</tbody>
</table>
</form>
EOF;
		$this->kclass->page['title'] .= '修改物资单价状态';
		$this->kclass->page['onload'] .= 'dc.tabhover()';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - <a href="/s.php?module=material&action=view&materialid='.$this->kclass->input['materialid'].'">查看物资</a> - 修改物资单价状态','right' => '<a href="/s.php?module=material&action=view&materialid='.$this->kclass->input['materialid'].'">返回</a>','body'=>$body));
	}
	
	function doupdatePriceStatus(){
		if($this->kclass->input['requestMethod']!='post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			if(!$this->kclass->input['priceStatus']){
				$e.='<li>请选择单价状态</li>';
			}
		}
		if($e){
			$this->kclass->messager(array(
				'title' => '修改物资单价状态',
				'text' => '您在修改物资单价状态的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$version=$this->kclass->DB->queryFirst("SELECT versionid,materialid,itemid FROM materialversion WHERE versionid={$this->kclass->input['versionid']}");
		$materialInfo=$this->kclass->getMaterial(array('materialid'=>$version['materialid'],'itemid'=>$version['itemid']));
		$this->kclass->DB->query("UPDATE materialversion SET priceStatus='{$this->kclass->input['priceStatus']}' WHERE versionid={$this->kclass->input['versionid']}");
		$this->kclass->messager(array(
			'title' => '修改物资单价状态',
			'text' => '修改物资单价状态 已修改成功!',
			'url' => $materialInfo['url'],
			'sec' => 3
		));
	}
	
	function updateEntrust(){
		if($this->kclass->input['materialid']<=0){
			$e.='<li>数据错误，很抱歉</li>';
		}
		if($e){
			$this->kclass->messager(array(
				'title' => '修改物资单价',
				'text' => '您在修改物资单价的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$versions=$this->kclass->DB->query("SELECT versionid,materialid,itemid FROM materialversion WHERE materialid={$this->kclass->input['materialid']}");
		if($this->kclass->DB->numRows()){
			$i=1;
			while($version=$this->kclass->DB->fetchArray($versions)){
				$item=$this->kclass->DB->queryFirst("SELECT materialentrustid FROM materialentrust WHERE materialid={$version['materialid']} AND versionid={$version['versionid']}");
				$materialInfo=$this->kclass->getMaterial(array('materialid'=>$version['materialid'],'itemid'=>$version['itemid']));
				$itemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').'">
					<td><input type="hidden" name="versionid['.$i.']" value="'.$version['versionid'].'" />
						<input type="hidden" name="itemId['.$i.']" value="'.$item['materialentrustid'].'" />'.$i.'</td>
					<td><a href="'.$materialInfo['url'].'">'.$materialInfo['no'].'</a>　'.$materialInfo['material'].'　'.$materialInfo['standard'].'</td>
					<td>'.$this->kclass->chooserMaterial(array('hName'=>'itemMaterialid['.$i.']','hId'=>'itemMaterialid'.$i,'name'=>'itemMaterial['.$i.']','id'=>'itemMaterial'.$i,'line'=>$i,'showVersion'=>1,'width'=>550,'hasBlank'=>1)).'</td>
					<td><select name="itemVersionid['.$i.']" id="itemVersionid'.$i.'" style="width:150px;"></select></td>
					<td><input type="text" size="12" name="processPrice['.$i.']" /></td>
					<td><input type="text" size="35" name="itemRemark['.$i.']" /></td>
				</tr>';
				$i++;
			}
		}
		$body=<<<EOF
<form method="post" action="/s.php?module=material&action=doupdateEntrust">
<input type="hidden" name="module" value="material">
<input type="hidden" name="action" value="doupdateEntrust">
<input type="hidden" name="materialid" value="{$this->kclass->input['materialid']}">
<table class="hundred">
<thead><tr><th colspan=6>委外加工明细</th></tr></thead>
<tbody>
<tr class="center"><td width="15">ID</td><td>物资</td><td>选择需要提供的物资 <span class="red bold">*</span></td><td>提供的物资版本 <span class="red bold">*</span></td><td>加工费 <span class="red bold">*</span></td><td>备注</td></tr>
{$itemtr}
<tr class="odd center" nohover>
<td colspan=6><input type="submit" id="submitButton" value="  提交  " accesskey="s" onclick="dc.supplier.check(document.supplier)"><input type="reset" value="  复位  "/></td>
</tr>
</tbody>
</table>
</form>
EOF;
		$this->kclass->page['title'] .= '新建或者修改委外加工明细';
		$this->kclass->page['onload'] .= 'dc.tabhover()';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - <a href="/s.php?module=material&action=view&materialid='.$this->kclass->input['materialid'].'">查看物资</a> - 新建或者修改委外加工明细','right' => '<a href="/s.php?module=material&action=view&materialid='.$this->kclass->input['materialid'].'">返回</a>','body'=>$body));
	}
	function doupdateEntrust(){
		if($this->kclass->input['requestMethod']!='post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			$count=count($this->kclass->input['itemVersionid']);
			$hasItem=0;
			for($i=1;$i<=$count;$i++){
				if($this->kclass->input['itemMaterialid'][$i]>0 AND $this->kclass->input['itemVersionid'][$i]>0 AND $this->kclass->input['processPrice'][$i]>0){
					$hasItem=1;
				}
			}
			if($hasItem==0){
				$e.='<li>请填写完整内容</li>';
			}
		}
		if($e){
			$this->kclass->messager(array(
				'title' => '新建或者修改委外加工明细',
				'text' => '您在新建或者修改委外加工明细的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		for($i=1;$i<=$count;$i++){
			if($this->kclass->input['itemMaterialid'][$i]>0 AND $this->kclass->input['itemVersionid'][$i]>0 AND $this->kclass->input['processPrice'][$i]>0){
				if($this->kclass->input['itemId'][$i]>0){
					$this->kclass->DB->query("
						UPDATE materialentrust SET 
							materialid='{$this->kclass->input['materialid']}',
							versionid='{$this->kclass->input['versionid'][$i]}',
							aimMaterialid='{$this->kclass->input['itemMaterialid'][$i]}',
							aimVersionid='{$this->kclass->input['itemVersionid'][$i]}',
							processPrice='{$this->kclass->input['processPrice'][$i]}',
							remark='{$this->kclass->input['itemRemark'][$i]}'
						WHERE materialentrustid='{$this->kclass->input['itemId'][$i]}'
					");
				}else{
					$this->kclass->DB->query("
						INSERT INTO materialentrust (materialid,versionid,aimMaterialid,aimVersionid,processPrice,remark)
						VALUES ('{$this->kclass->input['materialid']}','{$this->kclass->input['versionid'][$i]}','{$this->kclass->input['itemMaterialid'][$i]}','{$this->kclass->input['itemVersionid'][$i]}','{$this->kclass->input['processPrice'][$i]}','{$this->kclass->input['itemRemark'][$i]}')
					");
				}
			}
		}
		$this->kclass->messager(array(
			'title' => '新建或者修改委外加工明细',
			'text' => '委外加工明细 已修改成功!',
			'url' => '/s.php?module=material&action=view&materialid='.$this->kclass->input['materialid'],
			'sec' => 3
		));
	}

	//
	function import(){
		$upload=$this->kclass->upload(array('title'=>'批量导入：'));
$body=<<<EOF
<form method="post" action="/s.php?module=material&action=doimport">
<input type="hidden" name="module" value="material">
<input type="hidden" name="action" value="doimport">
{$upload}
<table class="tlist tsuite" align="center" width="100%">
<tbody>
<tr class="odd center" nohover>
<td colspan=4><input type="submit" id="submitButton" value="  提交  " accesskey="s" onclick="dc.supplier.check(document.supplier)"><input type="reset" value="  复位  "/></td>
</tr>
</tbody>
</table>
</form>
EOF;
		$this->kclass->page['title']=' 批量导入物资';
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - 批量导入物资','right'=>'<a href="/s.php?module=material">返回列表</a>','body'=>$body));
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
					if($this->kclass->input['attachTitle'][$k] != $this->kclass->input['attachOrgTitle'][$k])$cdt="`title`='".$this->kclass->input['attachTitle'][$k]."',";
					if($this->kclass->input['attachOrdering'][$k] != $this->kclass->input['attachOrgOrdering'][$k])$cdt.="`ordering`='".$this->kclass->input['attachOrdering'][$k]."',";
					if($v==1)$cdt.= "`module`='".$this->kclass->input['module']."',";
					if($cdt != ''){
						$this->kclass->DB->query("UPDATE `attachs` SET ".$cdt."`modifier`='".$this->kclass->user['userid']."',`modified`='".TIMENOW."' WHERE `attachid`='".$k."'");
					}
					$fileCount++;
				}elseif($v==2){//删除
					$attach=$this->kclass->DB->queryFirst("SELECT `folder`,`newName`,`image`,`imginfo` FROM `attachs` WHERE `attachid`='".$k."'");
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
								$cell=$worksheet->getCellByColumnAndRow($col,$row);
								$val[$col]=$cell->getValue();
							}
							if($val[0]!=''){
								$material=$this->kclass->DB->queryFirst("SELECT originalMaterialid FROM originalmaterial WHERE killed=0 AND materialno='".$val[0]."' LIMIT 0,1");
								if($material['originalMaterialid']>0){
									$this->kclass->DB->query("
										INSERT INTO originalmaterial
											(materialno,title,standard,unit,origin,qualified,disqualified,quantity,min,criticalNumber,max,remark,created,creator)
										VALUES
											('".$val[0]."','".$val[1]."','".$val[2]."','".$val[3]."','".$val[4]."','".$this->kclass->iif($val[5],$val[5],0)."','".$this->kclass->iif($val[6],$val[6],0)."','".$this->kclass->iif($val[7],$val[7],0)."','".$this->kclass->iif($val[8],$val[8],0)."','".$this->kclass->iif($val[9],$val[9],0)."','".$this->kclass->iif($val[10],$val[10],0)."','".$val[11]."','".TIMENOW."','".$this->kclass->user['userid']."')
									");
								}
							}
						}
					}
				}
			}
		}
		if($this->kclass->input['times']==$total){
			$this->kclass->messager(array(
				'title' => '批量导入物资',
				'text' => '物资批量全部导入成功！',
				'url' => '/s.php?module=material&action=listOriginal',
				'sec' => 2
			));
		}else{
			$this->kclass->messager(array(
				'title' => '批量导入物资',
				'text' => '正在批量导入物资，每次导入10个，请等待自动返回页面...',
				'url' => '/s.php?module=material&action=doimport&times='.$this->kclass->input['times'],
				'sec' => 2
			));
		}
	}
	//
	function listOriginal(){
		$materials=$this->kclass->DB->query("SELECT * FROM originalmaterial ORDER BY originalMaterialid ASC");
		if($this->kclass->DB->numRows()){
			while($material=$this->kclass->DB->fetchArray($materials)){
				$r.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').'">
				<td>'.$material['type'].'</td>
				<td class="middle bold">'.$material['materialno'].'</td>
				<td>'.$material['title'].'</a></td>
				<td>'.$material['standard'].'</td>
				<td>'.$material['unit'].'</td>
				<td>'.$material['qualified'].'</td>
				<td>'.$material['disqualified'].'</td>
				<td>'.$material['quantity'].'</td>
				<td>'.$material['remark'].'</td></tr>';
			}
		}
$body .= <<<EOF
<table>
<thead>
<tr>
<th>类别</th>
<th>物料编码</th>
<th>物料名称</th>
<th>规格</th>
<th>单位</th>
<th>良品数</th>
<th>不良品数</th>
<th>总数</th>
<th>备注</th>
</tr>
</thead>
<tbody>
{$r}
</tr>
</tbody>
</table>
EOF;
	$this->kclass->page['title'] = ' 原始物资列表';
	$this->kclass->page['main'] .= $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 原始物资列表','right' => '<a href="/s.php?module=material&action=exchange">转换到物资列表</a>','body'=>$body));
	}
	//
	function exchange(){
		$abbr=array();
		$cs=$this->kclass->DB->query("SELECT categoryid,title,abbr,childlist FROM category WHERE parentid=19 ORDER BY categoryid ASC");
		if($this->kclass->DB->numRows()){
			while($c=$this->kclass->DB->fetchArray($cs)){
				$ccs=$this->kclass->DB->query("SELECT categoryid,title,abbr FROM category WHERE parentid='".$c['categoryid']."' ORDER BY categoryid ASC");
				if($this->kclass->DB->numRows()){
					while($cc=$this->kclass->DB->fetchArray($ccs)){
						$tccs=$this->kclass->DB->query("SELECT categoryid,title,abbr FROM category WHERE parentid='".$cc['categoryid']."' ORDER BY categoryid ASC");
						if($this->kclass->DB->numRows()){
							while($tcc=$this->kclass->DB->fetchArray($tccs)){
								$abbr[]=array('categoryid'=>$tcc['categoryid'],'abbr'=>$c['abbr'].$cc['abbr'].$tcc['abbr']);
							}
						}else{
							$abbr[]=array('categoryid'=>$cc['categoryid'],'abbr'=>$c['abbr'].$cc['abbr']);
						}
					}
				}else{
					$abbr[]=array('categoryid'=>$c['categoryid'],'abbr'=>$c['abbr']);
				}
			}
		}
		foreach($abbr as $key=>$val){
			if(strlen($val['abbr'])!=3){
				unset($abbr[$key]);
			}
		}
		$this->kclass->input['times']+=1;
		$originalmaterials=$this->kclass->DB->query("SELECT * FROM originalmaterial ORDER BY originalMaterialid ASC");
		$counter=$this->kclass->DB->numRows();
		$total=ceil(($counter)/10);//刷新次数
		$count=$this->kclass->iif($this->kclass->input['times']==$total,$counter,($this->kclass->input['times'])*10);
		$min=($this->kclass->input['times']-1)*10;
		$max=$this->kclass->input['times']*10;
		if($counter){
			$i=0;
			$categoryid='';
			while($originalmaterial=$this->kclass->DB->fetchArray($originalmaterials)){
				if($i>=$min AND $max-$i<=10 AND $max-$i>0){
					$material=$this->kclass->DB->queryFirst("SELECT materialid FROM material WHERE killed=0 AND materialno='".$originalmaterial['materialno']."' LIMIT 0,1");
					if($material['materialid']>0){
						foreach($abbr as $key=>$val){
							if(substr($originalmaterial['materialno'],0,3)==$val['abbr']){
								$categoryid=$val['categoryid'];
							}else{
								if($categoryid==''){
									$categoryid=0;
								}
							}
						}
						$units=$this->kclass->DB->query("SELECT unitid,title FROM unit");
						if($this->kclass->DB->numRows()){
							while($unit=$this->kclass->DB->fetchArray($units)){
								if($originalmaterial['unit']==$unit['title']){
									$originalmaterial['unitid']=$unit['unitid'];
								}
							}
						}
						if($originalmaterial['unitid']==''){
							$originalmaterial['unitid']=5;
						}
						if($originalmaterial['inventoryType']=='是'){
							$inventoryType='must';
						}elseif($originalmaterial['inventoryType']=='否'){
							$inventoryType='choose';
						}
						$materialno=substr($originalmaterial['materialno'],0,3).str_pad(substr($originalmaterial['materialno'],3,6),3,'0',STR_PAD_LEFT);
						// add material
						$this->kclass->DB->query("
							INSERT INTO material
								(materialno,title,standard,categoryid,attrid,unitid,originid,quantity,qualified,disqualified,inventoryType,min,criticalNumber,max,remark,created,creator)
							VALUES
								('".$materialno."','".$originalmaterial['title']."','".$originalmaterial['standard']."','".$categoryid."',6,'".$originalmaterial['unitid']."','".$originalmaterial['origin']."','".$originalmaterial['quantity']."','".$originalmaterial['qualified']."','".$originalmaterial['disqualified']."','".$inventoryType."','".$originalmaterial['min']."','".$originalmaterial['criticalNumber']."','".$originalmaterial['max']."','".$originalmaterial['remark']."','".TIMENOW."','".$this->kclass->user['userid']."')
						");
						$materialid=$this->kclass->DB->insertID();
						// add pmaterial
						$this->kclass->DB->query("
							INSERT INTO pmaterial (materialid,materialno,title,standard,created,creator)
							VALUES ('".$materialid."','".$materialno."','".$originalmaterial['title']."','".$originalmaterial['standard']."','".TIMENOW."','".$this->kclass->user['userid']."')
						");
						// add version of material
						$this->kclass->DB->query("
							INSERT INTO materialversion
								(materialid,title,quantity,qualified,disqualified,created,creator)
							VALUES
								('".$materialid."','01','".$originalmaterial['quantity']."','".$originalmaterial['qualified']."','".$originalmaterial['disqualified']."','".TIMENOW."','".$this->kclass->user['userid']."')
						");
						$versionid=$this->kclass->DB->insertID();
						$time36='';
						$sd=explode('-','13-8-27');
						foreach($sd as $key => $val){
							$time36.=strtoupper(base_convert($val,10,36));
						}

						$time=strtotime('2013-08-27');
						if($originalmaterial['qualified']!=0){
							// add batch of material
							$this->kclass->DB->query("
								INSERT INTO batch (materialid,batchno,supplierid,dateline,quantity,qualified,disqualified,created,creator)
								VALUES ('".$materialid."','".$materialno."-130827',2,'".$time."','".$originalmaterial['quantity']."','".$originalmaterial['qualified']."','".$originalmaterial['disqualified']."','".TIMENOW."','".$this->kclass->user['userid']."')
							");
							$batchid=$this->kclass->DB->insertID();
							// add barcode of material
							$barcode='CN-'.$materialno.'-0000-'.$time36.'-01';
							$this->kclass->DB->query("
								INSERT INTO barcode (batchid,materialid,supplierid,versionid,parentid,barcode,country,materialno,supplierno,arrivalTime,number,version,quantity,statusid,creator,created)
								VALUES ('".$batchid."','".$materialid."',2,'".$versionid."',0,'".$barcode."','CN','".$materialno."','0000','".$time."',0,'01','".$originalmaterial['qualified']."','0','".$this->kclass->user['userid']."','".TIMENOW."')
							");
							$barcodeid=$this->kclass->DB->insertID();
							$this->kclass->DB->query("
								INSERT INTO stockitem
									(stockid,materialid,quantity,batchid,barcodeid,creator,created)
								VALUES
									(1,'".$materialid."','".$originalmaterial['qualified']."','".$batchid."','".$barcodeid."','".$this->kclass->user['userid']."','".TIMENOW."')
							");
						}
					}
				}
				$i++;
			}
		}
		if($this->kclass->input['times']==$total){
			$this->kclass->messager(array(
				'title' => '加入物资列表',
				'text' => '物资加入物资列表成功！',
				'url' => '/s.php?module=material&action=list',
				'sec' => 2
			));
		}else{
			$this->kclass->messager(array(
				'title' => '加入物资列表',
				'text' => '正在批量加入物资列表，每次加入10个，请等待自动返回页面...',
				'url' => '/s.php?module=material&action=exchange&times='.$this->kclass->input['times'],
				'sec' => 2
			));
		}
	}
	//
	function importStock(){
		$upload=$this->kclass->upload(array('title'=>'批量导入：'));
$body=<<<EOF
<form method="post" action="/s.php?module=material&action=doimportStock">
<input type="hidden" name="module" value="material">
<input type="hidden" name="action" value="doimportStock">
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
		$this->kclass->page['title']=' 批量导入物资库存';
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - 批量导入物资库存','right'=>'<a href="/s.php?module=material">返回列表</a>','body'=>$body));
	}
	//
	function doimportStock(){
		require ROOT.'/phpexcel/Classes/PHPExcel.php';
		require ROOT.'/phpexcel/Classes/PHPExcel/IOFactory.php';
		$this->kclass->input['times']+=1;
		$fileCount=0;
		if(count($this->kclass->input['attach'])>0 AND $this->kclass->input['times']==1){
			foreach($this->kclass->input['attach'] as $k => $v){
				if($v==0 OR $v==1){//0:保留，需要检查title与ordering是否更改。1:选择
					$cdt='';
					if($this->kclass->input['attachTitle'][$k] != $this->kclass->input['attachOrgTitle'][$k])$cdt="`title`='".$this->kclass->input['attachTitle'][$k]."',";
					if($this->kclass->input['attachOrdering'][$k] != $this->kclass->input['attachOrgOrdering'][$k])$cdt.="`ordering`='".$this->kclass->input['attachOrdering'][$k]."',";
					if($v==1)$cdt.= "`module`='".$this->kclass->input['module']."',";
					if($cdt != ''){
						$this->kclass->DB->query("UPDATE `attachs` SET ".$cdt."`modifier`='".$this->kclass->user['userid']."',`modified`='".TIMENOW."' WHERE `attachid`='".$k."'");
					}
					$fileCount++;
				}elseif($v==2){//删除
					$attach=$this->kclass->DB->queryFirst("SELECT `folder`,`newName`,`image`,`imginfo` FROM `attachs` WHERE `attachid`='".$k."'");
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
			if($attach=$this->kclass->DB->queryFirst("SELECT COUNT(attachid) AS n,newName,folder,type FROM `attachs` WHERE `module`='".$this->kclass->input['module']."' AND `mid`=0 AND `image`<> 1 ORDER BY created DESC LIMIT 0,1")){
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
								$cell=$worksheet->getCellByColumnAndRow($col,$row);
								$val[$col]=$cell->getValue();
							}
							if($val[0]!=''){
								$this->kclass->DB->query("
									INSERT INTO originalmaterialstock
										(materialno,itemno,quantity,qualified,disqualified,created,creator)
									VALUES
										('".$val[0]."','".$this->kclass->iif($val[1],$val[1],'')."','".$this->kclass->iif($val[2],$val[2],0)."','".$this->kclass->iif($val[3],$val[3],0)."','".$this->kclass->iif($val[4],$val[4],0)."','".TIMENOW."','".$this->kclass->user['userid']."')
								");
							}
						}
					}
				}
			}
		}
		if($this->kclass->input['times']==$total){
			$this->kclass->messager(array(
				'title' => '批量导入物资',
				'text' => '物资批量全部导入成功！',
				'url' => '/s.php?module=material&action=listOriginalStock',
				'sec' => 2
			));
		}else{
			$this->kclass->messager(array(
				'title' => '批量导入物资',
				'text' => '正在批量导入物资，每次导入10个，请等待自动返回页面...',
				'url' => '/s.php?module=material&action=doimportStock&times='.$this->kclass->input['times'],
				'sec' => 2
			));
		}
	}
	//
	function listOriginalStock(){
		// 批次数量清零
		$this->kclass->DB->query("TRUNCATE TABLE batch");
		// 条码数量清零
		$this->kclass->DB->query("TRUNCATE TABLE barcode");
		// 版本数量清零
		$this->kclass->DB->query("UPDATE materialversion SET quantity=0,qualified=0,disqualified=0,planInbound=0,planOutbound=0");
		// 明细数量清零
		$this->kclass->DB->query("UPDATE materialitem SET quantity=0,qualified=0,disqualified=0");
		// 物资数量清零
		$this->kclass->DB->query("UPDATE material SET quantity=0,qualified=0,disqualified=0");

		$materials=$this->kclass->DB->query("SELECT * FROM originalmaterialstock ORDER BY originalMaterialid ASC");
		if($this->kclass->DB->numRows()){
			while($material=$this->kclass->DB->fetchArray($materials)){
				$r.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').'">
				<td class="middle bold">'.$material['materialno'].'</td>
				<td>'.$material['title'].'</a></td>
				<td>'.$material['standard'].'</td>
				<td>'.$material['qualified'].'</td>
				<td>'.$material['disqualified'].'</td>
				<td>'.$material['quantity'].'</td>
				<td>'.$material['remark'].'</td></tr>';
			}
		}
$body .= <<<EOF
<table>
<thead>
<tr>
<th>物料编码</th>
<th>物料名称</th>
<th>规格</th>
<th>良品数</th>
<th>不良品数</th>
<th>总数</th>
<th>备注</th>
</tr>
</thead>
<tbody>
{$r}
</tr>
</tbody>
</table>
EOF;
	$this->kclass->page['title'] = ' 原始物资列表';
	$this->kclass->page['main'] .= $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 原始物资列表','right' => '<a href="/s.php?module=material&action=exchangeStock">转换到物资列表</a>','body'=>$body));
	}
	//
	function exchangeStock(){
		$this->kclass->input['times']+=1;
		$originalmaterials=$this->kclass->DB->query("SELECT * FROM originalmaterialstock ORDER BY originalMaterialid ASC");
		$counter=$this->kclass->DB->numRows();
		$total=ceil(($counter)/10);//刷新次数
		$count=$this->kclass->iif($this->kclass->input['times']==$total,$counter,($this->kclass->input['times'])*10);
		$min=($this->kclass->input['times']-1)*10;
		$max=$this->kclass->input['times']*10;
		if($counter){
			$i=0;
			$categoryid='';
			while($originalmaterial=$this->kclass->DB->fetchArray($originalmaterials)){
				if($i>=$min AND $max-$i<=10 AND $max-$i>0){
					$material=$this->kclass->DB->queryFirst("SELECT materialid FROM material WHERE materialno='".$originalmaterial['materialno']."' LIMIT 0,1");
					// update version of material
					if($originalmaterial['itemno']!=''){
						$materialitem=$this->kclass->DB->queryFirst("SELECT itemid FROM materialitem WHERE itemno='".$originalmaterial['itemno']."'");
						// update material item
						$this->kclass->DB->query("
							UPDATE materialitem SET
								quantity='".$originalmaterial['quantity']."',
								qualified='".$originalmaterial['qualified']."',
								disqualified='".$originalmaterial['disqualified']."'
							WHERE itemid='".$materialitem['itemid']."'
						");
						$no=$originalmaterial['itemno'];
						$materialitemid=$materialitem['itemid'];
					}else{
						$no=$originalmaterial['materialno'];
						$materialitemid=0;
					}
					$version=$this->kclass->DB->queryFirst("SELECT versionid FROM materialversion WHERE materialid='".$material['materialid']."' AND itemid='".$materialitemid."'");
					$versionid=$version['versionid'];
					$this->kclass->DB->query("
						UPDATE materialversion SET
							quantity='".$originalmaterial['quantity']."',
							qualified='".$originalmaterial['qualified']."',
							disqualified='".$originalmaterial['disqualified']."'
						WHERE versionid='".$versionid."'
					");
					// 计算数量
					$materialId[]=$material['materialid'];
					// update material
					$this->kclass->DB->query("
						UPDATE material SET
							quantity='".$originalmaterial['quantity']."',
							qualified='".$originalmaterial['qualified']."',
							disqualified='".$originalmaterial['disqualified']."'
						WHERE materialid='".$material['materialid']."'
					");
					
					$time36='';
					$sd=explode('-','14-02-26');
					foreach($sd as $key => $val){
						$time36.=strtoupper(base_convert($val,10,36));
					}

					$time=strtotime('2014-02-26');

					// add batch of material
					$batchno=$no.'-140226';
					$batch=$this->kclass->DB->queryFirst("SELECT batchid FROM batch WHERE killed=0 AND batchno='".$batchno."' LIMIT 0,1");
					if($batch['batchid']>0){
						$this->kclass->DB->query("
							UPDATE batch SET
								quantity=quantity+'".$originalmaterial['quantity']."',
								qualified=qualified+'".$originalmaterial['qualified']."',
								disqualified=disqualified+'".$originalmaterial['disqualified']."'
							WHERE killed=0 AND batchid='".$batch['batchid']."'
						");
						$batchid=$batch['batchid'];
					}else{
						$this->kclass->DB->query("
							INSERT INTO batch (materialid,materialitemid,batchno,versionid,dateline,quantity,qualified,disqualified,created,creator)
							VALUES ('".$material['materialid']."','".$materialitemid."','".$batchno."','".$versionid."','".$time."','".$originalmaterial['quantity']."','".$originalmaterial['qualified']."','".$originalmaterial['disqualified']."','".TIMENOW."','".$this->kclass->user['userid']."')
						");
						$batchid=$this->kclass->DB->insertID();
					}

					// add barcode of material
					$barcodeno='CN-'.$originalmaterial['materialno'].$this->kclass->iif($originalmaterial['itemno']!='','-'.$no,'').'-0000-'.$time36.'-01';
					$barcode=$this->kclass->DB->queryFirst("SELECT barcodeid FROM barcode WHERE scraped=0 AND barcode='".$barcodeno."' LIMIT 0,1");
					if($barcode['barcodeid']){
						$this->kclass->DB->query("
							UPDATE barcode SET
								quantity=quantity+'".$originalmaterial['qualified']."',
								qualified=qualified+'".$originalmaterial['qualified']."',
								disqualified=disqualified+'".$originalmaterial['disqualified']."'
							WHERE scraped=0 AND barcodeid='".$barcode['barcodeid']."'
						");
						$barcodeid=$barcode['barcodeid'];
					}else{
						$this->kclass->DB->query("
							INSERT INTO barcode (batchid,materialid,supplierid,versionid,parentid,barcode,country,materialno,supplierno,arrivalTime,number,version,quantity,qualified,disqualified,statusid,creator,created)
							VALUES ('".$batchid."','".$material['materialid']."',2,'".$versionid."',0,'".$barcodeno."','CN','".$originalmaterial['materialno']."','0000','".$time."',0,'01','".$originalmaterial['quantity']."','".$originalmaterial['qualified']."','".$originalmaterial['disqualified']."','0','".$this->kclass->user['userid']."','".TIMENOW."')
						");
						$barcodeid=$this->kclass->DB->insertID();
					}
					// 更新出入库明细的批次和条码id
					$this->kclass->DB->query("UPDATE inbounditem SET batchid=".$batchid.",barcodeid=".$barcodeid." WHERE versionid=".$versionid);
					$this->kclass->DB->query("UPDATE outbounditem SET batchid=".$batchid.",barcodeid=".$barcodeid." WHERE preitemid IN (SELECT itemid FROM preoutbounditem WHERE versionid=".$versionid.")");
				}
				$i++;
			}
		}
		if($this->kclass->input['times']==$total){
			// 物资数量
			$materialQuantitys=$this->kclass->DB->query("SELECT materialid,SUM(quantity) AS quantity,SUM(qualified) AS qualified,SUM(disqualified) AS disqualified FROM materialversion WHERE killed=0 GROUP BY materialid");
			if($this->kclass->DB->numRows()){
				while($materialQuantity=$this->kclass->DB->fetchArray($materialQuantitys)){
					$this->kclass->DB->query("
						UPDATE material SET
							quantity='".$materialQuantity['quantity']."',
							qualified='".$materialQuantity['qualified']."',
							disqualified='".$materialQuantity['disqualified']."'
						WHERE materialid='".$materialQuantity['materialid']."'
					");
				}
			}

			$this->kclass->messager(array(
				'title' => '加入物资列表',
				'text' => '物资加入物资列表成功！',
				'url' => '/s.php?module=material&action=list',
				'sec' => 2
			));
		}else{
			$this->kclass->messager(array(
				'title' => '加入物资列表',
				'text' => '正在批量加入物资列表，每次加入10个，请等待自动返回页面...',
				'url' => '/s.php?module=material&action=exchangeStock&times='.$this->kclass->input['times'],
				'sec' => 2
			));
		}
	}
	//
	function importPrice(){
		$upload=$this->kclass->upload(array('title'=>'批量导入：'));
$body=<<<EOF
<form method="post" action="/s.php?module=material&action=doimportPrice">
<input type="hidden" name="module" value="material">
<input type="hidden" name="action" value="doimportPrice">
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
		$this->kclass->page['title']=' 批量导入物资库存';
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - 批量导入物资库存','right'=>'<a href="/s.php?module=material">返回列表</a>','body'=>$body));
	}
	//
	function doimportPrice(){
		require ROOT.'/phpexcel/Classes/PHPExcel.php';
		require ROOT.'/phpexcel/Classes/PHPExcel/IOFactory.php';
		$this->kclass->input['times']+=1;
		$fileCount=0;
		if(count($this->kclass->input['attach'])>0 AND $this->kclass->input['times']==1){
			foreach($this->kclass->input['attach'] as $k => $v){
				if($v==0 OR $v==1){//0:保留，需要检查title与ordering是否更改。1:选择
					$cdt='';
					if($this->kclass->input['attachTitle'][$k] != $this->kclass->input['attachOrgTitle'][$k])$cdt="`title`='".$this->kclass->input['attachTitle'][$k]."',";
					if($this->kclass->input['attachOrdering'][$k] != $this->kclass->input['attachOrgOrdering'][$k])$cdt.="`ordering`='".$this->kclass->input['attachOrdering'][$k]."',";
					if($v==1)$cdt.= "`module`='".$this->kclass->input['module']."',";
					if($cdt != ''){
						$this->kclass->DB->query("UPDATE `attachs` SET ".$cdt."`modifier`='".$this->kclass->user['userid']."',`modified`='".TIMENOW."' WHERE `attachid`='".$k."'");
					}
					$fileCount++;
				}elseif($v==2){//删除
					$attach=$this->kclass->DB->queryFirst("SELECT `folder`,`newName`,`image`,`imginfo` FROM `attachs` WHERE `attachid`='".$k."'");
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
			if($attach=$this->kclass->DB->queryFirst("SELECT COUNT(attachid) AS n,newName,folder,type FROM `attachs` WHERE `module`='".$this->kclass->input['module']."' AND `mid`=0 AND `image`<> 1 ORDER BY created DESC LIMIT 0,1")){
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
								$cell=$worksheet->getCellByColumnAndRow($col,$row);
								$val[$col]=$cell->getValue();
							}
							if($val[0]!=''){
								$this->kclass->DB->query("
									INSERT INTO originalmaterialprice
										(materialno,price,created,creator)
									VALUES
										('".$val[0]."','".$this->kclass->iif($val[1],$val[1],'0')."','".TIMENOW."','".$this->kclass->user['userid']."')
								");
							}
						}
					}
				}
			}
		}
		if($this->kclass->input['times']==$total){
			$this->kclass->messager(array(
				'title' => '批量导入物资',
				'text' => '物资批量全部导入成功！',
				'url' => '/s.php?module=material&action=listOriginalPrice',
				'sec' => 2
			));
		}else{
			$this->kclass->messager(array(
				'title' => '批量导入物资',
				'text' => '正在批量导入物资，每次导入10个，请等待自动返回页面...',
				'url' => '/s.php?module=material&action=doimportPrice&times='.$this->kclass->input['times'],
				'sec' => 2
			));
		}
	}
	//
	function listOriginalPrice(){
		$this->kclass->DB->query("UPDATE materialversion SET price=0");
		$materials=$this->kclass->DB->query("SELECT * FROM originalmaterialprice ORDER BY originalPriceid ASC");
		if($this->kclass->DB->numRows()){
			while($material=$this->kclass->DB->fetchArray($materials)){
				$r.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').'">
				<td class="middle bold">'.$material['materialno'].'</td>
				<td>'.$material['price'].'</a></td></tr>';
			}
		}
$body .= <<<EOF
<table>
<thead>
<tr>
<th>物料编码</th>
<th>价格</th>
</tr>
</thead>
<tbody>
{$r}
</tr>
</tbody>
</table>
EOF;
	$this->kclass->page['title'] = ' 原始物资列表';
	$this->kclass->page['main'] .= $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 原始物资列表','right' => '<a href="/s.php?module=material&action=exchangePrice">转换到物资列表</a>','body'=>$body));
	}
	//
	function exchangePrice(){
		$this->kclass->input['times']+=1;
		$originalmaterials=$this->kclass->DB->query("SELECT * FROM originalmaterialprice ORDER BY originalPriceid ASC");
		$counter=$this->kclass->DB->numRows();
		$total=ceil(($counter)/10);//刷新次数
		$count=$this->kclass->iif($this->kclass->input['times']==$total,$counter,($this->kclass->input['times'])*10);
		$min=($this->kclass->input['times']-1)*10;
		$max=$this->kclass->input['times']*10;
		if($counter){
			$i=0;
			while($originalmaterial=$this->kclass->DB->fetchArray($originalmaterials)){
				if($i>=$min AND $max-$i<=10 AND $max-$i>0){
					$material=$this->kclass->DB->queryFirst("SELECT materialid FROM material WHERE materialno='".$originalmaterial['materialno']."' LIMIT 0,1");
					if($material['materialid']>0){
						$version=$this->kclass->DB->queryFirst("SELECT versionid FROM materialversion WHERE materialid='".$material['materialid']."'");
						$versionid=$version['versionid'];

						$this->kclass->DB->query("UPDATE materialversion SET price={$originalmaterial['price']} WHERE versionid={$versionid}");
						/*$this->kclass->DB->query("
							INSERT INTO pricerecord (materialid,versionid,price,created,creator) 
							VALUES ({$material['materialid']},{$versionid},'{$originalmaterial['price']}','".TIMENOW."',{$this->kclass->user['userid']})
						");*/
					}
				}
				$i++;
			}
		}
		if($this->kclass->input['times']==$total){
			$this->kclass->messager(array(
				'title' => '加入物资列表',
				'text' => '物资加入物资列表成功！',
				'url' => '/s.php?module=material&action=list',
				'sec' => 2
			));
		}else{
			$this->kclass->messager(array(
				'title' => '加入物资列表',
				'text' => '正在批量加入物资列表，每次加入10个，请等待自动返回页面...',
				'url' => '/s.php?module=material&action=exchangePrice&times='.$this->kclass->input['times'],
				'sec' => 2
			));
		}
	}
	/*
	 * 将产品明细转换到物资明细中。
	 */
	function exchangeItem(){
		$versions=$this->kclass->DB->query("
			SELECT mv.*,pi.parentid,pi.attributevalue
			FROM materialversion AS mv 
			LEFT JOIN `productitem_old` AS pi ON (pi.productitemid=mv.productitemid)
			WHERE mv.killed=0 AND pi.killed=0
			ORDER BY mv.versionid ASC
		");
		if($this->kclass->DB->numRows()){
			while($version=$this->kclass->DB->fetchArray($versions)){
				$addValue='';
				if($version['module']=='product'){
					$addValue=$this->kclass->iif($version['attributevalue']==0,'',$version['attributevalue']);
					$related=$this->kclass->DB->queryFirst("
						SELECT mv.*,pi.parentid,pi.attributevalue
						FROM materialversion AS mv 
						LEFT JOIN `productitem_old` AS pi ON (pi.productitemid=mv.productitemid)
						WHERE mv.killed=0 AND pi.killed=0 AND versionid='".$version['relatedid']."' 
						LIMIT 0,1
					");
					$attributevalue=$this->kclass->iif($version['attributevalue']!=0,$related['attributevalue'].','.$addValue,$related['attributevalue']);
				}else{
					$attributevalue=$version['attributevalue'];
				}
				$this->kclass->DB->query("
					INSERT INTO materialitem (materialid,relatedid,itemno,addvalue,attributevalue,quantity,qualified,disqualified,created,creator) 
					VALUES (".$version['materialid'].",0,'".$version['itemno']."','".$addValue."','".$attributevalue."','".$version['quantity']."','".$version['disqualified']."','".$version['disqualified']."','".TIMENOW."','".$this->kclass->user['userid']."')
				");
				$itemid=$this->kclass->DB->insertID();
				$this->kclass->DB->query("UPDATE materialversion SET itemid=".$itemid.",ifProduce=1 WHERE versionid=".$version['versionid']);
			}
		}
	}
	
	function updateVersionMaterialid(){
		$boms=$this->kclass->DB->query("
			SELECT b.bomid,b.versionid,b.parentVersionid,mv.materialid,mv.itemid
			FROM bom AS b
			LEFT JOIN materialversion AS mv ON (b.parentVersionid=mv.versionid)
			ORDER BY bomid ASC
		");
		if($this->kclass->DB->numRows()){
			while($bom=$this->kclass->DB->fetchArray($boms)){
				$child=$this->kclass->DB->queryFirst("SELECT materialid,itemid FROM materialversion WHERE versionid='".$bom['versionid']."' LIMIT 0,1");
				$this->kclass->DB->query("UPDATE bom SET materialid='".$bom['materialid']."',parentitemid='".$bom['itemid']."',childid='".$child['materialid']."',childItemid='".$child['itemid']."' WHERE killed=0 AND bomid='".$bom['bomid']."'");
			}
		}
	}

	/**********************************************private function**************************************************************/
	//
	function _chooserFinishedItem($b){ // 产成品明细
		if(intval($b['materialid'])<=0){
			return false;
		}
		if($b['width'] > 0){
			$b['width'] = ' style="width: '.$b['width'].'px;"';
		}
		$items=$this->kclass->DB->query("SELECT itemid,materialid,itemno FROM materialitem WHERE killed=0 AND materialid='".$b['materialid']."'ORDER BY itemno ASC");
		if($this->kclass->DB->numRows()){
			$r.='<select id="'.$this->kclass->iif($b['id'],$b['id'],$b['name']).'" name="'.$b['name'].'"'.$b['width'].'><option value=0></option>';
			while($item=$this->kclass->DB->fetchArray($items)){
				$materialInfo=$this->kclass->getMaterial(array('materialid'=>$item['materialid'],'itemid'=>$item['itemid']));
				$r.='<option value="'.$item['itemid'].'" ';
				if($b['selected']==$item['itemid']){
					$r.='selected';
				}
				$r.='>'.$item['itemno'].'　'.$materialInfo['material'].'　'.$materialInfo['standard'].'</option>';
			}
			$r.='</select>';
		}

		return $r;
	}
	
	function stopMaterial(){
		if(!$this->kclass->input['materialid'] > 0) $this->kclass->boinkIt('/s.php?module=material');
		//echo $this->kclass->user['userid']; 
		$this->kclass->DB->query("UPDATE `material` SET killer=".$this->kclass->user['userid'].",killed=".TIMENOW." WHERE materialid=".$this->kclass->input['materialid']." ");
	}
}
?>