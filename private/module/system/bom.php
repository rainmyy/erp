<?php
//
class bom{
	# Global
	var $kclass;
	var $cacheFile='bom';
	var $cacheExpire=86400;

	/*--------------------*/
	// Auto run function
	/*--------------------*/
	function autoRun(){
		$this->kclass->page['title'] = '物料清单';
		$this->baseurl = '<a href="/s.php?module=home">首页</a> - <a href="/s.php?module=bom">物料清单</a>';
		$this->right = '<a href="/s.php?module=bom&action=add">新建物料清单</a>';

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
			case 'update':
				$this->update();
				break;
			case 'doupdate':
				$this->doupdate();
				break;
			case 'doupdateversion':
				$this->doupdateversion();
				break;
			case 'updateversion':
				$this->updateversion();
				break;
			case 'remove':
				$this->remove();
				break;
			case 'kill':
				$this->kill();
				break;
			case 'doorder':
				$this->doorder();
				break;
			case 'copy':
				return $this->mmcopy();
				break;
			case 'docopy':
				return $this->docopy();
				break;
			case 'updateMany':
				return $this->updateMany();
				break;
			case 'doupdateMany':
				return $this->doupdateMany();
				break;
			case 'showAll':
				return $this->showAll();
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
			case 'export':
				return $this->export();
				break;
			default:
				$this->mmlist();
		}
	}
	//
	function insert(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			if($this->kclass->input['parentid']>0){
				$hasItem=0;
				$j=count($this->kclass->input['itemMaterialid']);
				$materialId=array();
				for($i=1;$i<=$j;$i++){
					if($this->kclass->input['versionid']==$this->kclass->input['itemVersionid'][$i]){
						$e.='<li>不能将一个列项设置为它的子列项的列项！</li>';
					}
					if($this->kclass->input['itemMaterialid'][$i]>0 AND $this->kclass->input['itemVersionid'][$i]>0 AND $this->kclass->input['itemQuantity'][$i]!='' AND $this->kclass->input['itemAttritionRate'][$i]!=''){
						$hasItem=1;
						$material=$this->kclass->DB->queryFirst("SELECT materialid,materialno FROM material WHERE materialid='".$this->kclass->input['itemMaterialid'][$i]."' LIMIT 0,1");
						if(in_array($this->kclass->input['itemMaterialid'][$i],$materialId)){
							$e.='物资 <b>'.$material['materialno'].'</b> 被重复添加，请检查。';
							break;
						}else{
							$materialId[]=$this->kclass->input['itemMaterialid'][$i];
						}
					}
				}
				if($hasItem==0){
					$e.='<li>请填写至少一条明细！</li>';
				}
			}else{
				$e.='<li>请选择物料清单的 父件 </li>';
			}
		}
		if($e){
			$this->kclass->messager(array(
					'title' => '新建物料清单',
					'text' => '您在新建物料清单的过程中有以下错误: <ul>'.$e.'</ul>',
					'url' => 'javascript:history.back()',
					'final' => 1,
					'sec' => 3
				)
			);
		}
		if($this->kclass->input['attrid']==6){
			$sql='SELECT mv.versionid,mv.materialid,mv.itemid,m.unitid,
				m.materialno
			FROM materialversion AS mv
			LEFT JOIN material AS m ON (m.materialid=mv.materialid)
			WHERE mv.killed=0 AND m.killed=0 AND mv.versionid='.$this->kclass->input['versionid'];
		}elseif($this->kclass->input['attrid']==7 OR $this->kclass->input['attrid']==8){
			$sql='SELECT mv.versionid,mv.materialid,mv.itemid,m.unitid,
				m.materialno,mi.itemno
			FROM materialversion AS mv
			LEFT JOIN material AS m ON (m.materialid=mv.materialid)
			LEFT JOIN materialitem AS mi ON (mi.itemid=mv.itemid)
			WHERE mv.killed=0 AND m.killed=0 AND mi.killed=0 AND mv.versionid='.$this->kclass->input['versionid'];
		}
		$parent=$this->kclass->DB->queryFirst($sql);
		for($m=1;$m<=$j;$m++){
			if($this->kclass->input['itemMaterialid'][$m]>0 AND $this->kclass->input['itemVersionid'][$m]>0 AND $this->kclass->input['itemQuantity'][$m]>0 AND $this->kclass->input['itemAttritionRate'][$m]!=''){
				$materialItemid=$this->kclass->iif($this->kclass->input['materialItemid'][$m]>0,$this->kclass->input['materialItemid'][$m],0);		
				$unitid=$this->kclass->iif($this->kclass->input['itemUnitid'][$m]>0,$this->kclass->input['itemUnitid'][$m],0);
				$this->kclass->DB->query("
					INSERT INTO `bom` (`materialid`,`parentVersionid`,`parentitemid`,`childid`,`versionid`,`childItemid`,`quantity`,`childUnitid`,`typeid`,`attritionRate`,`remark`,`creator`,`created`)
					VALUES ('".$parent['materialid']."','".$parent['versionid']."','".$parent['itemid']."','".$this->kclass->input['itemMaterialid'][$m]."','".$this->kclass->input['itemVersionid'][$m]."','".$materialItemid."','".$this->kclass->input['itemQuantity'][$m]."','".$unitid."','".$this->kclass->input['itemTypeid'][$m]."','".$this->kclass->input['itemAttritionRate'][$m]."','".$this->kclass->input['itemRemark'][$m]."','".$this->kclass->user['userid']."','".TIMENOW."')
				");
			}
		}
		$this->kclass->messager(array(
				'title' => '新建物料清单',
			'text' => '物料清单 <b>'.$this->kclass->iif($parent['itemno']!='',$parent['itemno'],$parent['materialno']).'</b> 已新建成功!',
			'url' => '/s.php?module=bom&cache=1#'.$this->kclass->input['versionid'],
				'final' => 1,
				'sec' => 3
		));
	}

	//
	function add(){
		if($this->kclass->input['attrid']==''){
					$attr=$this->kclass->chooserAttr(array('name'=>'attrid','width'=>400,'hasBlank'=>1,'module'=>'material'));
					$body=<<<EOF
<form action="/s.php?module=bom&action=add" name="bom" method="get">
<input type="hidden" name="module" value="bom">
<input type="hidden" name="action" value="add">
<table>
<thead>
<tr>
	<th colspan="2">选择bom单的类型：</th>
</tr>
</thead>
<tbody>
<tr class="odd">
	<td width="150">选择bom单的类型：<span class="bold red">*</span></td>
	<td>{$attr}</td>
</tr>
<tr class="even">
	<td colspan="2" align="center">
		<input type="submit" value="   提交   ">
		<input type="reset" value="   重置   ">
	</td>
</tr>
</tbody>
</table>
</form>
EOF;
				$this->kclass->page['onload'] .= 'dc.tabhover();';
				$this->kclass->page['title'] = ' - 新建物料清单 ';
				$this->kclass->page['main'] .= $this->kclass->skin->dbody(array('title' => $this->baseurl.' - 新建物料清单 - 选择bom单的类型', 'right' => '<a href="/s.php?module=bom">返回列表</a>','body'=>$body));
			}else{
				if($this->kclass->input['versionid']>0){
					$parentVersion=$this->kclass->DB->queryFirst("
						SELECT versionid,materialid,itemid,title AS version
						FROM `materialversion` 
						WHERE killed=0 AND versionid='".$this->kclass->input['versionid']."'
						LIMIT 0,1
					");
					$materialInfo=$this->kclass->getMaterial(array('materialid'=>$parentVersion['materialid'],'itemid'=>$parentVersion['itemid']));
					$attrid=$materialInfo['attrid'];
					$select='<input type="hidden" name="parentid" value="'.$parentVersion['materialid'].'"><input type="hidden" name="versionid" value="'.$parentVersion['versionid'].'"><a href="'.$materialInfo['url'].'">'.$materialInfo['no'].'</a>　'.$materialInfo['material'].'　'.$materialInfo['standard'];
					$version=$parentVersion['version'];
					}else{
					if($this->kclass->input['attrid']==7 OR $this->kclass->input['attrid']==8){
						$select=$this->kclass->chooserMaterialItem(array('name'=>'parentid','hasBlank'=>1,'width'=>450,'showVersion'=>1,'line'=>0,'attrid'=>$this->kclass->input['attrid']));
					}elseif($this->kclass->input['attrid']==6){
						$select=$this->kclass->chooserMaterial(array('hName'=>'parentid','name'=>'materialid','width' =>450,'line'=>0,'showVersion'=>1));
					}
					$attrid=$this->kclass->input['attrid'];
					$version='<select name="versionid" id="itemVersionid0" style="width:250px;"></select>';
				}

				for($i=1;$i<6;$i++){
					$itemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').'">
						<td>'.$this->kclass->chooserMaterial(array('hName'=>'itemMaterialid['.$i.']','hId'=>'itemMaterialid'.$i,'name'=>'itemMaterial['.$i.']','id'=>'itemMaterial'.$i,'width'=>450,'line'=>$i,'showVersion'=>1)).'</td>
						<td><select name="itemVersionid['.$i.']" id="itemVersionid'.$i.'" style="width:80px;"></select></td>
						<td><input type="text" name="itemQuantity['.$i.']" size="5"></td>
						<td>'.$this->kclass->chooserUnit(array('name'=>'itemUnitid['.$i.']','hasBlank'=>1,'width'=>100,'topname'=>'','selectedid'=>$material['unitid'])).'</td>
						<td><input type="text" name="itemAttritionRate['.$i.']" size="6" value="0">‰</td>
						<td><input type="radio" name="itemTypeid['.$i.']" value="0" checked>安装 <input type="radio" name="itemTypeid['.$i.']" value="1">配送</td>
						<td><input type="text" name="itemRemark['.$i.']" style="width:250px"></td>
					</tr>';
				}

$body = <<<EOF
<form action="/s.php?module=bom&action=insert" name="bom" method="post">
<input type="hidden" name="module" value="bom">
<input type="hidden" name="action" value="insert">
<input type="hidden" name="attrid" value="{$attrid}">
<table class="hundred">
<thead>
<tr>
	<th colspan="7">添加物料清单：</th>
</tr>
</thead>
<tbody>
<tr class="even">
	<td width="80">产品/半成品名称：<span class="bold red">*</span></td>
	<td colspan=5 width="600">{$select}</td><td>{$version}</td>
</tr>
<tr class="bold even center middle"><td width="750">物资 <span class="bold red">*</span></td><td width="80">版本 <span class="bold red">*</span></td><td width="60">数量 <span class="bold red">*</span></td><td width="60">单位 </td><td width="80">损耗率 <span class="bold red">*</span></td><td>类型</td><td width="250">备注</td></tr>
{$itemtr}
<tr class="even">
	<td colspan="7" class="gray small">注意事项：<br>①如果所列表格不够，那么在提交保存后再点击“修改”，来增加物料清单物资；<br>②损耗率按照千分比计算。<br>③包装的bom单<br></td>
</tr>
<tr class="odd">
	<td colspan="7" align="center">
		<input type="submit" name="submit" value="   保存   " accesskey="s">
		<input type="reset" value="   重置   ">
	</td>
</tr>
</tbody>
</table>
</form>
EOF;
				$this->kclass->page['onload'] .= 'dc.tabhover();';
				$this->kclass->page['title'] = ' 新建物料清单';
				$this->kclass->page['main'] .= $this->kclass->skin->dbody(array('title' => $this->baseurl.' - 新建物料清单', 'right' => '<a href="/s.php?module=bom">返回列表</a>','body'=>$body));
			}
	}
	//
	function doupdate(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			$j=count($this->kclass->input['itemMaterialid']);
			$hasItem=0;
			$materialId=array();
			for($i=1;$i<=$j;$i++){
				if($this->kclass->input['itemKill'][$i]=='' AND $this->kclass->input['itemVersionid'][$i]>0 AND $this->kclass->input['itemMaterialid'][$i]>0 AND $this->kclass->input['itemQuantity'][$i]!='' AND $this->kclass->input['itemAttritionRate'][$i]!=''){
					$hasItem=1;
					$material=$this->kclass->DB->queryFirst("SELECT materialid,materialno FROM material WHERE materialid='".$this->kclass->input['itemMaterialid'][$i]."' LIMIT 0,1");
					if(in_array($this->kclass->input['itemMaterialid'][$i],$materialId)){
						$e.='物资 <b>'.$material['materialno'].'</b> 被重复添加，请检查。';
						break;
					}else{
						$materialId[]=$this->kclass->input['itemMaterialid'][$i];
					}
				}
			}
			if($hasItem==0){
				$e.='<li>至少填写一种明细</li>';
			}
		}

		if($e){
			$this->kclass->messager(array(
					'title' => '修改物料清单',
					'text' => '您在修改物料清单的过程中有以下错误：<ul>'.$e.'</ul>',
					'url' => 'javascript:history.back()',
					'final' => 1,
					'sec' => 3
				)
			);
		}
		for($m=1;$m<=$j;$m++){
			$childItem='';
			if($this->kclass->input['bomId'][$m]!=''){
				if($this->kclass->input['itemKill'][$m]!='' AND $this->kclass->input['itemKill'][$m]==$this->kclass->input['bomId'][$m]){
					$this->kclass->DB->query("UPDATE `bom` SET killed='".TIMENOW."',killer='".$this->kclass->user['userid']."' WHERE bomid='".$this->kclass->input['bomId'][$m]."'");
				}else{
					if($this->kclass->input['productItemid'][$m]>0){
						$childItem='`childItemid`='.$this->kclass->input['productItemid'][$m].',';
					}
					$childItemid=$this->kclass->iif($this->kclass->input['materialItemid'][$m],$this->kclass->input['materialItemid'][$m],0); 
					$this->kclass->DB->query("
						UPDATE `bom` SET
							`childid`='".$this->kclass->input['itemMaterialid'][$m]."',
							`versionid`='".$this->kclass->input['itemVersionid'][$m]."',
							`childItemid`='".$childItemid."',
							`quantity` = '".$this->kclass->input['itemQuantity'][$m]."',
							`childUnitid` = '".$this->kclass->input['itemUnitid'][$m]."',
							`typeid` = '".$this->kclass->input['itemTypeid'][$m]."',
							`attritionRate`='".$this->kclass->input['itemAttritionRate'][$m]."',
							`remark` = '".$this->kclass->input['itemRemark'][$m]."',
							`modifier` = '".$this->kclass->user['userid']."',
							`modified` = '".TIMENOW."'
						WHERE bomid='".$this->kclass->input['bomId'][$m]."'
					");
				}
			}else{
				if($this->kclass->input['itemMaterialid'][$m]>0 AND $this->kclass->input['itemVersionid'][$m]>0 AND $this->kclass->input['itemQuantity'][$m]!='' AND $this->kclass->input['itemAttritionRate'][$m]!=''){
					$materialItemid=$this->kclass->iif($this->kclass->input['materialItemid'][$m]>0,$this->kclass->input['materialItemid'][$m],0);		
					$unitid=$this->kclass->iif($this->kclass->input['itemUnitid'][$m]>0,$this->kclass->input['itemUnitid'][$m],0);
				
					$this->kclass->DB->query("
						INSERT INTO `bom` (`materialid`,`parentVersionid`,`parentitemid`,`childid`,`versionid`,`childItemid`,`quantity`,`childUnitid`,`typeid`,`attritionRate`,`remark`,`creator`,`created`)
						VALUES ('".$this->kclass->input['materialid']."','".$this->kclass->input['parentVersionid']."','".$this->kclass->input['parentitemid']."','".$this->kclass->input['itemMaterialid'][$m]."','".$this->kclass->input['itemVersionid'][$m]."','".$materialItemid."','".$this->kclass->input['itemQuantity'][$m]."','".$unitid."','".$this->kclass->input['itemTypeid'][$m]."','".$this->kclass->input['itemAttritionRate'][$m]."','".$this->kclass->input['itemRemark'][$m]."','".$this->kclass->user['userid']."','".TIMENOW."')
					");
				}
			}
		}

		$this->kclass->messager(array(
				'title' => '修改物料清单',
				'text' => '物料清单 <b>'.$this->kclass->input['materialno'].'</b> 已成功被修改!',
				'url' => '/s.php?module=bom&cache=1#'.$this->kclass->input['parentVersionid'],
				'final' => 1,
				'sec' => 3
		));
	}

	//
	function update(){
		if($this->kclass->input['parentVersionid']<=0){
			$this->kclass->boinkIt('/s.php?module=bom');
		}
		$parent=$this->kclass->DB->queryFirst("SELECT materialid,itemid,versionid,title AS version FROM `materialversion` WHERE materialid='".$this->kclass->input['materialid']."' AND versionid='".$this->kclass->input['parentVersionid']."' LIMIT 0,1");
		$parentInfo=$this->kclass->getMaterial(array('materialid'=>$parent['materialid'],'itemid'=>$parent['itemid']));
		$material='<a href="'.$parentInfo['url'].'">'.$this->kclass->iif($parentInfo['itemno']!='',$parentInfo['itemno'],$parentInfo['materialno']).'</a>　'.$parentInfo['material'].'　'.$parentInfo['standard'].'　版本：'.$parent['version'];

		$childs=$this->kclass->DB->query("
			SELECT bomid,childid,versionid,childItemid,quantity,childUnitid,typeid,attritionRate,remark
			FROM bom 
			WHERE killed=0 AND materialid=".$parent['materialid']." AND parentitemid=".$parent['itemid']." AND parentVersionid='".$this->kclass->input['parentVersionid']."'
			ORDER BY created ASC
		");
		if($this->kclass->DB->numRows()){
			$i=1;
			while($child=$this->kclass->DB->fetchArray($childs)){
				if($child['typeid']==0){
					$checked1='checked';
					$checked2='';
				}elseif($child['typeid']==1){
					$checked1='';
					$checked2='checked';
				}
				$childInfo=$this->kclass->getMaterial(array('materialid'=>$child['childid'],'itemid'=>$child['childItemid']));
				$itemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').'">
					<td><input type="hidden" name="bomId['.$i.']" value="'.$child['bomid'].'"/>'.$i.'</td>
					<td>'.$this->kclass->chooserMaterial(array('hName'=>'itemMaterialid['.$i.']','hId'=>'itemMaterialid'.$i,'name'=>'itemMaterial['.$i.']','id'=>'itemMaterial'.$i,'width'=>550,'line'=>$i,'showVersion'=>1,'selectedid'=>$child['childid'],'selectItemid'=>$child['childItemid'],'value'=>$this->kclass->iif($childInfo['itemno']!='',$childInfo['itemno'],$childInfo['materialno']).'　'.$childInfo['material'].'　'.$childInfo['standard'])).'</td>
					<td>'.$this->kclass->chooserMaterialVersion(array('name'=>'itemVersionid['.$i.']','id'=>'itemVersionid'.$i,'width'=>150,'selectedid'=>$child['versionid'],'materialid'=>$child['childid'],'itemid'=>$child['childItemid'])).'</td>
					<td><input type="text" name="itemQuantity['.$i.']" size="5" value="'.$child['quantity'].'"></td>
					<td>'.$this->kclass->chooserUnit(array('name'=>'itemUnitid['.$i.']','hasBlank'=>1,'width'=>100,'topname'=>'','selectedid'=>$child['childUnitid'])).'</td>
					<td><input type="text" name="itemAttritionRate['.$i.']" size="6" value="'.$child['attritionRate'].'">‰</td>
					<td><input type="radio" name="itemTypeid['.$i.']" value="0" '.$checked1.'>安装 <input type="radio" name="itemTypeid['.$i.']" value="1" '.$checked2.'>配送</td>
					<td><input type="text" name="itemRemark['.$i.']" style="width:250px" value="'.$child['remark'].'"></td>
					<td><input type="checkbox" name="itemKill['.$i.']" value="'.$child['bomid'].'"></td>
				</tr>';
				$i++;
			}
			$itemtr.='<tr><td colspan=9 class="bold">新增物料清单物资</td></tr>';
		}
		for($k=$i;$k<$i+5;$k++){
			$itemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').'">
				<td>'.$k.'</td>
				<td>'.$this->kclass->chooserMaterial(array('hName'=>'itemMaterialid['.$k.']','hId'=>'itemMaterialid'.$k,'name'=>'itemMaterial['.$k.']','id'=>'itemMaterial'.$k,'width'=>550,'line'=>$k,'showVersion'=>1)).'</td>
				<td><select name="itemVersionid['.$k.']" id="itemVersionid'.$k.'" style="width:150px;"></select></td>
				<td><input type="text" name="itemQuantity['.$k.']" size="5"></td>
				<td>'.$this->kclass->chooserUnit(array('name'=>'itemUnitid['.$k.']','hasBlank'=>1,'width'=>100,'topname'=>'')).'</td>
				<td><input type="text" name="itemAttritionRate['.$k.']" size="6" value="0">‰</td>
				<td><input type="radio" name="itemTypeid['.$k.']" value="0" checked>安装 <input type="radio" name="itemTypeid['.$i.']" value="1">配送</td>
				<td><input type="text" name="itemRemark['.$k.']" style="width:250px"></td><td></td>
			</tr>';
		}
$body = <<<EOF
<form action="/s.php?module=bom&action=doupdate" name="name" method="post">
<input type="hidden" name="module" value="bom">
<input type="hidden" name="action" value="doupdate">
<input type="hidden" name="materialid" value="{$parent['materialid']}">
<input type="hidden" name="parentitemid" value="{$parent['itemid']}">
<input type="hidden" name="parentVersionid" value="{$parent['versionid']}">
<table class="hundred">
<thead>
<tr>
	<th colspan="9">修改物料清单: </th>
</tr>
</thead>
<tbody>
<tr class="even">
	<td colspan=2 width="80" align="center">产品/半成品名称：<span class="bold red">*</span></td>
	<td colspan=9 class="middle">{$material}</td>
</tr>
<tr class="bold even center middle"><td witdh="15">ID</td><td width="750">物资 <span class="bold red">*</span></td><td width="150">版本 <span class="bold red">*</span></td><td width="60">数量 <span class="bold red">*</span></td><td width="60">单位</td><td width="100">损耗率 <span class="bold red">*</span></td><td width="140">类型</td><td width="150">备注</td><td width="20">删</td></tr>
{$itemtr}
<tr class="even">
	<td colspan="9" class="gray small">注意事项：<br>①如果所列表格不够，那么在提交保存后再点击“修改”，来增加物料清单物资；<br>②损耗率按照千分比计算。<br></td>
</tr>
<tr class="odd">
	<td colspan="9" align="center">
		<input type="submit" name="submit" value="   保存   " accesskey="s">
		<input type="reset" value="   重置   ">
	</td>
</tr>
</tbody>
</table>
</form>
EOF;
		$this->kclass->page['onload'] .= 'dc.tabhover();';
		$this->kclass->page['title'] = ' 修改物料清单';
		$this->kclass->page['main'] .= $this->kclass->skin->dbody(array('title' => $this->baseurl.' - 修改物料清单', 'right' => $this->right, 'body'=>$body));
	}

	//
	function kill(){
		if($this->kclass->input['confirm'] == 0){
			$this->kclass->boinkIt('/s.php?module=bom');
		}
		if($this->kclass->input['parentVersionid']<0){
			$this->kclass->boinkIt('/s.php?module=bom');
		}
		if($this->kclass->input['requestMethod'] != 'post'){
			$e='<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}
		if($e){
			$this->kclass->messager(array(
					'title' => '删除物料清单',
					'text' => '您在删除物料清单的过程中有以下错误：<ul>'.$e.'</ul>',
					'url' => 'javascript:history.back()',
					'final' => 1,
					'sec' => 3
				)
			);
		}
			$this->kclass->DB->query("
				UPDATE `bom` SET
					killer='".$this->kclass->user['userid']."',
					killed='".TIMENOW."'
			WHERE materialid='".$this->kclass->input['materialid']."' AND parentVersionid='".$this->kclass->input['parentVersionid']."' AND parentitemid='".$this->kclass->input['parentitemid']."'
			");
		$this->kclass->messager(array(
				'title' => '删除物料清单',
				'text' => '物料清单已成功被删除!',
				'url' => '/s.php?module=bom#&cache=1#'.$this->kclass->input['parentVersionid'],
				'final' => 1,
				'sec' => 3
			)
		);
	}

	//
	function remove(){
		if($this->kclass->input['parentVersionid']<0){
			$this->kclass->boinkIt('/s.php?module=bom');
			}
		$bom=$this->kclass->DB->queryFirst("
			SELECT b.materialid,b.parentitemid,b.parentVersionid,mv.title AS version 
			FROM `bom` AS b
			LEFT JOIN materialversion AS mv ON (mv.versionid=b.parentVersionid) 
			WHERE b.killed=0 AND b.materialid='".$this->kclass->input['materialid']."'AND b.parentVersionid='".$this->kclass->input['parentVersionid']."' 
					LIMIT 0,1
				");
		$materialInfo=$this->kclass->getMaterial(array('materialid'=>$bom['materialid'],'itemid'=>$bom['parentitemid']));
		$material='<a href="'.$materialInfo['url'].'">'.$materialInfo['no'].'</a>　'.$materialInfo['material'].'　'.$materialInfo['standard'].'　版本：'.$bom['version'];
			$body = <<<EOF
<form action="/s.php?module=bom&action=kill" name="name" method="post">
<input type="hidden" name="module" value="bom">
<input type="hidden" name="action" value="kill">
<input type="hidden" name="materialid" value="{$bom['materialid']}">
<input type="hidden" name="parentitemid" value="{$bom['parentitemid']}">
<input type="hidden" name="parentVersionid" value="{$bom['parentVersionid']}">
<table style="width:750px">
<thead>
<tr>
	<th colspan="2">删除物料清单: </th>
</tr>
</thead>
<tbody>
<tr class="even">
<td colspan="2">你确定要删除物资: <b>{$material}的物料清单吗?<br />(注意：此列项下包含的所有子类也会同时被删除！)</td>
</tr>
<tr class="odd">
<td colspan="2" align="center">
<label for="confirm_1"><input type="radio" id="confirm_1" name="confirm" value="1" class="nostyle">是</label>
<label for="confirm_0"><input type="radio" id="confirm_0" name="confirm" value="0" class="nostyle" checked>否</label>
</td>
</tr>
<tr class="even">
	<td colspan="2" align="center">
		<input type="submit" name="submit" value="   保存   " accesskey="s">
		<input type="reset" value="   重置   ">
	</td>
</tr>
</tbody>
</table>
</form>
EOF;
		$this->kclass->page['title'] = ' - 删除物料清单';
		$this->kclass->page['main'] .= $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 删除物料清单', 'right' => $this->right, 'body'=>$body));
	}
	//
	function mmlist(){
		if($this->kclass->input['cache']=='no'){
			$body=$this->_bomList();
		}else{
			if($this->kclass->input['cache']==1){// 如果是添加或者编辑之后跳转，那么更新缓存
				$body=$this->_bomList();
				$this->kclass->updateCache(array('data'=>$body,'filename'=>$this->cacheFile,'time'=>$this->cacheExpire));
			}else{ // 否则，获取缓存文件
				$file=ROOT.'/private/cache/'.$this->cacheFile.'_'.$this->cacheExpire.'.txt';
				if(file_exists($file)){ // 如果存在缓存文件，对缓存文件进行处理
					$lastTime=filemtime($file); // 获得文件的最后修改时间
					if((TIMENOW-$lastTime)>$this->cacheExpire){ // 缓存到期
						$body=$this->_bomList();
						$this->kclass->updateCache(array('data'=>$body,'filename'=>$this->cacheFile,'time'=>$this->cacheExpire));
					}else{
						$body=$this->kclass->getCache(array('filename'=>$this->cacheFile.'_'.$this->cacheExpire));
					}
				}else{// 不存在，则更新缓存
					$body=$this->_bomList();
					$this->kclass->updateCache(array('data'=>$body,'filename'=>$this->cacheFile,'time'=>$this->cacheExpire));
				}
			}
		}

		$this->kclass->page['onload'] .= 'dc.tabhover();dc.tips();dc.flow(\'listtr\')';
		$this->kclass->page['title'] = '物料清单 - 列表';
		$this->kclass->page['main'] .= $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 列表', 'right' => $this->right.'　<a href="/s.php?module=bom&cache=1">更新BOM缓存</a>　<a href="/s.php?module=bom&action=import">导入物料清单</a>', 'body'=>$body));
	}
	//
	function mmcopy(){
		if($this->kclass->input['parentVersionid']<=0){
			$this->kclass->boinkIt('/s.php?module=bom');
		}
		$parent=$this->kclass->DB->queryFirst("SELECT DISTINCT `bom`.parentVersionid,`bom`.materialid,`bom`.parentitemid,mv.title AS version FROM `bom` LEFT JOIN materialversion AS mv ON (mv.versionid=`bom`.parentVersionid) WHERE `bom`.killed=0 AND `bom`.materialid='".$this->kclass->input['materialid']."'AND `bom`.parentVersionid='".$this->kclass->input['parentVersionid']."' LIMIT 0,1");

		$materialInfo=$this->kclass->getMaterial(array('materialid'=>$parent['materialid'],'itemid'=>$parent['parentitemid']));
		$material='<a href="'.$materialInfo['url'].'">'.$materialInfo['no'].'</a>　'.$materialInfo['material'].'　'.$materialInfo['standard'].'　版本：'.$bom['version'];

		$childs=$this->kclass->DB->query("
			SELECT b.bomid,b.quantity,b.childid,b.childItemid,b.versionid,b.childUnitid,b.attritionRate,b.ordering,
				mv.title AS version
			FROM `bom` AS b
			LEFT JOIN `materialversion` AS mv ON (mv.versionid=b.versionid)
			WHERE b.killed=0 AND b.materialid='".$this->kclass->input['materialid']."' AND b.parentVersionid='".$this->kclass->input['parentVersionid']."'
			ORDER BY b.ordering ASC
		");
		if($this->kclass->DB->numRows()){
			$i=1;
			while($child=$this->kclass->DB->fetchArray($childs)){
				$childInfo=$this->kclass->getMaterial(array('materialid'=>$child['childid'],'itemid'=>$child['childItemid']));
				$itemtr.='<tr class="'.$this->kclass->rotateLine().' small">
					<td align="right"><input type="hidden" name="itemId['.$i.']" value="'.$i.'" />'.$i.'</td>
					<td><a href="'.$childInfo['url'].'">'.$childInfo['no'].'</a>　'.$childInfo['material'].'　'.$childInfo['standard'].'</td>
					<td align="center">'.$child['version'].'</td>
					<td align="center">'.$child['quantity'].$this->kclass->iif($child['childUnitid']>0,$child['bomUnit'],$child['unit']).'</td>
					<td>'.$child['attritionRate'].'‰</td>
					<td>'.$child['remark'].'</td>
					<td><input type="checkbox" name="itemSelect['.$i.']" value="'.$child['bomid'].'" /></td></tr>';
				$i++;
			}
		}
		// 目标产品等选择
		$bomParents=$this->kclass->DB->query("SELECT DISTINCT parentVersionid,materialid,parentitemid FROM `bom` WHERE killed=0 AND parentVersionid<>".$this->kclass->input['parentVersionid']." ORDER BY ordering ASC, bomid ASC");
		if($this->kclass->DB->numRows()){
			while($bomParent=$this->kclass->DB->fetchArray($bomParents)){
				$bomParentInfo=$this->kclass->getMaterial(array('materialid'=>$bomParent['materialid'],'itemid'=>$bomParent['parentitemid']));
				$select.='<option value="'.$bomParent['parentVersionid'].'">　'.$bomParentInfo['no'].'　'.$bomParentInfo['material'].'　'.$bomParentInfo['standard'].'　版本：'.$bom['version'].'</option>';
				}
			}
		$body = <<<EOF
<form action="/s.php?module=bom&action=docopy" name="bom" method="post">
<input type="hidden" name="module" value="bom">
<input type="hidden" name="action" value="docopy">
<table class="hundred">
<thead>
<tr>
	<th colspan="8">复制物料清单: </th>
</tr>
</thead>
<tbody>
<tr class="even">
	<td width="80" colspan=2>源产品/半成品/生产物资名称：</td>
	<td colspan=6 class="middle">{$material}</td>
</tr>
<tr class="even">
	<td width="80" colspan=2>目标产品/半成品/生产物资名称：<span class="bold red">*</span></td>
	<td colspan=6><select name="parentVersionid" style="width:450px"><option value="0"> </option>{$select}</select></td>
</tr>
<tr class="bold even center middle"><td width="15">ID</td><td width="750">物资 <span class="bold red">*</span></td><td width="80">版本 <span class="bold red">*</span></td><td width="60">数量 <span class="bold red">*</span></td><td width="80">损耗率 <span class="bold red">*</span></td><td width="250">备注</td><td width="20">选</td></tr>
{$itemtr}
<tr class="even">
	<td colspan="8" class="gray small">注意事项：<br>①如果所列表格不够，那么在提交保存后再点击“修改”，来增加物料清单物资；<br>②损耗率按照千分比计算。<br></td>
</tr>
<tr class="odd">
	<td colspan="8" align="center">
		<input type="submit" name="submit" value="   保存   " accesskey="s">
		<input type="reset" value="   重置   ">
	</td>
</tr>
</tbody>
</table>
</form>
EOF;
		$this->kclass->page['onload'] .= 'dc.tabhover();';
		$this->kclass->page['title'] = ' - 复制物料清单';
		$this->kclass->page['main'] .= $this->kclass->skin->dbody(array('title' => $this->baseurl.' - 复制物料清单', 'right' => $this->right, 'body'=>$body));
	}
	//
	function docopy(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			if($this->kclass->input['parentVersionid']<=0){
				$e.='<li>请选择目标物资</li>';
			}
			$j=count($this->kclass->input['itemId']);
			$hasItem=0;
			for($i=1;$i<=$j;$i++){
				if($this->kclass->input['itemSelect'][$i]>0){
					$hasItem=1;
				}
			}
			if($hasItem=0){
				$e.='<li>请至少选择一条明细！</li>';
			}
			
		}
		if($e){
			$this->kclass->messager(array(
				'title' => '复制物料清单',
				'text' => '您在复制物料清单的过程中有以下错误: <ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'final' => 1,
				'sec' => 3
			));
		}
		
		$parent=$this->kclass->DB->queryFirst("SELECT versionid,materialid,itemid FROM materialversion WHERE killed=0 AND versionid='".$this->kclass->input['parentVersionid']."' LIMIT 0,1");
		
		for($i=1;$i<=$j;$i++){
			if($this->kclass->input['itemSelect'][$i]>0){
				$materialItemid=$this->kclass->iif($this->kclass->input['parentitemid'][$i]>0,$this->kclass->input['parentitemid'][$i],0);
				$child=$this->kclass->DB->queryFirst("SELECT childid,versionid,childItemid,quantity,childUnitid,attritionRate,typeid,remark FROM bom WHERE bomid='".$this->kclass->input['itemSelect'][$i]."'");

				$this->kclass->DB->query("
					INSERT INTO `bom` (`materialid`,`parentVersionid`,`parentitemid`,`childid`,`versionid`,`childItemid`,`quantity`,`childUnitid`,`typeid`,`attritionRate`,`remark`,`creator`,`created`)
					VALUES ('".$parent['materialid']."','".$parent['versionid']."','".$parent['itemid']."','".$child['childid']."','".$child['versionid']."','".$child['childItemid']."','".$child['quantity']."','".$child['childUnitid']."','".$child['typeid']."','".$child['attritionRate']."','".$child['remark']."','".$this->kclass->user['userid']."','".TIMENOW."')
				");
			}
		}
		$this->kclass->messager(array(
			'title' => '复制物料清单',
			'text' => '物料清单 已复制成功!',
			'url' => '/s.php?module=bom&cache=1#'.$this->kclass->input['parentVersionid'],
			'final' => 1,
			'sec' => 3
		));
	}
	// 修改子项 应用于多张bom
	function updateMany(){
		$bom=$this->kclass->DB->queryFirst("SELECT * FROM `bom` WHERE killed=0 AND bomid='".$this->kclass->input['bomid']."'");
		if($this->kclass->input['bomid']<0){
			$e.='<li>数据错误，很抱歉</li>';
		}
		if($e){
			$this->kclass->messager(array(
				'title' => '修改子项，应用于多张物料清单',
				'text' => '您在修改子项的过程中有以下错误: <ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'final' => 1,
				'sec' => 3
			));
		}
		$childInfo=$this->kclass->getMaterial(array('materialid'=>$bom['childid'],'itemid'=>$bom['childItemid']));
		$material=$this->kclass->chooserMaterial(array('hName'=>'materialid','hId'=>'materialid','name'=>'material','id'=>'material','width'=>600,'line'=>'','showVersion'=>1,'selectedid'=>$bom['childid'],'selectItemid'=>$bom['childItemid'],'value'=>$childInfo['materialno'].'　'.$childInfo['material'].'　'.$childInfo['standard'],'showVersion'=>1,'line'=>''));
		$unit=$this->kclass->chooserUnit(array('name'=>'unitid','hasBlank'=>1,'width'=>100,'topname'=>'','selectedid'=>$this->kclass->iif($bom['childUnitid']>0,$bom['childUnitid']>0,$bom['unitid'])));
		$version=$this->kclass->chooserMaterialVersion(array('name'=>'versionid','selectedid'=>$bom['versionid'],'width' =>150,'id'=>'itemVersionid','materialid'=>$bom['childid'],'itemid'=>$bom['childItemid']));

		$parents=$this->kclass->bomParentList(array('childid'=>$bom['childid'],'versionid'=>$bom['versionid'],'itemid'=>$bom['childItemid'],'loopNum'=>1));
		if(!empty($parents)){
			$i=1;
			foreach($parents as $parent){
				$item.='<tr class="odd"><td><input type="hidden" name="itemId['.$i.']" value="'.$i.'">'.$i.'</td><td colspan=6><a href="'.$parent['url'].'">'.$parent['no'].'</a>　'.$parent['title'].'　'.$parent['standard'].'</td><td><input type="checkbox" name="itemSelect['.$i.']" value="'.$parent['bomid'].'"></td></tr>';
				$i++;
			}
		}
		$body = <<<EOF
<form action="/s.php?module=bom&action=doupdateMany" name="bom" method="post">
<input type="hidden" name="module" value="bom">
<input type="hidden" name="action" value="doupdateMany">
<table class="hundred">
<thead>
<tr>
	<th colspan="8">修改子项（应用于多张BOM单）: <a href="{$childInfo['url']}">{$childInfo['no']}</a>　{$childInfo['material']}　{$childInfo['standard']}</a></th>
</tr>
</thead>
<tbody>
<tr class="bold odd center middle"><td></td><td width="750">物资 <span class="bold red">*</span></td><td width="80">版本 <span class="bold red">*</span></td><td width="60">数量 <span class="bold red">*</span></td><td width="60">单位</td><td width="80">损耗率 <span class="bold red">*</span></td><td width="150">备注</td><td></td></tr>
<tr class="even">
<td></td>
<td>{$material}</td>
<td>{$version}</td>
<td><input type="text" name="quantity" size="5" value="{$bom['quantity']}"></td>
<td>{$unit}</td>
<td><input type="text" name="attritionRate" size="6" value="{$bom['attritionRate']}">‰</td>
<td><input type="text" name="remark" value="{$bom['remark']}" size="20"></td><td></td></tr>
<tr class="even">
	<td width="80" colspan=8 class="middle">选择批量修改的父级</td>
</tr>
<tr><td width="15">ID</td><td colspan=6>父级</td><td width="30">选</td></tr>
{$item}
<tr class="even">
	<td colspan="8" align="center">
		<input type="submit" name="submit" value="   保存   " accesskey="s">
		<input type="reset" value="   重置   ">
	</td>
</tr>
</tbody>
</table>
</form>
EOF;
		$this->kclass->page['onload'] .= 'dc.tabhover();';
		$this->kclass->page['title'] = ' 修改物料清单 子项 （应用于多张BOM）';
		$this->kclass->page['main'] .= $this->kclass->skin->dbody(array('title' => $this->baseurl.' - 修改物料清单 子项 （应用于多张BOM）', 'right' => $this->right, 'body'=>$body));
	}
	function doupdateMany(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			$j=count($this->kclass->input['itemId']);
			$hasItem=0;
			for($i=1;$i<$j+1;$i++){
				if($this->kclass->input['itemSelect'][$i]>0){
					$hasItem=1;
				}
			}
			if($hasItem==0){
				$e.='<li>至少填写一种明细</li>';
			}
		}
		if($e){
			$this->kclass->messager(array(
				'title' => '修改物料清单',
				'text' => '您在修改物料清单的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'final' => 1,
				'sec' => 3
			));
		}
		for($i=1;$i<=$j;$i++){
			if($this->kclass->input['itemSelect'][$i]>0){
			$this->kclass->DB->query("
				UPDATE bom SET 
						childid='".$this->kclass->input['materialid']."',
					versionid='".$this->kclass->input['versionid']."',
					quantity='".$this->kclass->input['quantity']."',
					childUnitid='".$this->kclass->input['unitid']."',
					attritionRate='".$this->kclass->input['attritionRate']."',
					remark='".$this->kclass->input['remark']."',
					modified='".TIMENOW."',
					modifier='".$this->kclass->user['userid']."'
				WHERE bomid='".$this->kclass->input['itemSelect'][$i]."'
			");
		}
		}
		$this->kclass->messager(array(
			'title' => '修改 物料清单 子项',
			'text' => '应用于多条BOM的 子项 已修改成功!',
			'url' => '/s.php?module=bom&cache=1',
			'final' => 1,
			'sec' => 3
		));
	}
	function showAll(){
		if(!$this->kclass->input['versionid']){
			$e.='<li>请从正常页面访问</li>';
		}
		$version=$this->kclass->DB->queryFirst("SELECT versionid,materialid,itemid,price FROM materialversion WHERE versionid={$this->kclass->input['versionid']} LIMIT 0,1");
		if(!$version){
			$e.='<li>数据错误，很抱歉</li>';
		}
		if($e){
			$this->kclass->messager(array(
				'title' => '显示所有子集',
				'text' => '您在显示所有子集的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'final' => 1,
				'sec' => 3
			));
		}
		$parent=$this->kclass->getMaterial(array('materialid'=>$version['materialid'],'itemid'=>$version['itemid']));
		$children=$this->kclass->bomChildList(array('parentid'=>$version['materialid'],'versionid'=>$version['versionid'],'materialitemid'=>$version['itemid']));
		if($children['count']){
			$i=1;
			foreach($children['bom'] AS $child){
				$symbol='';
				for($k=2;$k<=$child['rank'];$k++){
					$symbol.='　|';
				}
				$materialInfo=$this->kclass->getMaterial(array('materialid'=>$child['materialid'],'itemid'=>$child['materialitemid']));
				$itemtr.='<tr>
					<td></td>
					<td align="left"><span class="gray bold middle">'.$symbol.'</span>【'.$child['rank'].'.'.$i.'】<a href="'.$materialInfo['url'].'">'.$materialInfo['no'].'</a>　'.$materialInfo['material'].'　'.$materialInfo['standard'].'</td>
					<td align="center">'.$child['version'].'</td>
					<td class="bold '.$this->kclass->iif($child['price']==0,'black','red').'">'.$this->kclass->iif($this->kclass->purviews(array('module'=>'material','action'=>'viewPrice','final'=>1)),$child['price'],'—').'</td>
					<td>'.$child['qualified'].'</td>
					<td align="center">'.$child['quantity'].$child['unit'].'</td>
					<td>'.$child['attritionRate'].'‰</td>
					<td>'.$materialInfo['origin'].'</td>
					<td>'.$materialInfo['cnGeneral'].'</td>
					<td>'.$child['remark'].'</td>
				</tr>';
				$i++;
			}
		}		
			$body= <<<EOF
<div class="title"><a href="{$parent['url']}">{$parent['no']}　{$parent['material']}　{$parent['standard']}</a>　单价：{$version['price']}</div>
<table class="hundred">
<thead>
<tr align="center">
<th width="10"></th>
<th>物资</th>
<th width="30">版本</th>
<th width="60">单价</th>
<th width="30">库存</th>
<th width="60">数量</th>
<th width="60">损耗率</th>
<th width="70">来源</th>
<th width="30">通用</th>
<th width="150">备注</th>
</tr>
</thead>
<tbody id="tbody">
{$itemtr}
</tbody>
</table>
EOF;
		$this->kclass->page['onload'] .= 'dc.tabhover();dc.tips()';
		$this->kclass->page['title'] = '物料清单 - 列表';
		$this->kclass->page['main'] .= $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 列表', 'right' => $this->right.'　<a href="/s.php?module=bom&cache=1">更新BOM缓存</a>　<a href="/s.php?module=bom&action=import">导入物料清单</a>　<a href="/s.php?module=bom&action=export&versionid='.$version['versionid'].'" target="__blank"">导出物料清单</a>', 'body'=>$body));
	}
	//
	function import(){
		$upload=$this->kclass->upload(array('title'=>'批量导入：'));
$body=<<<EOF
<form method="post" action="/s.php?module=bom&action=doimport">
<input type="hidden" name="module" value="bom">
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
		$this->kclass->page['title']=' 批量导入bom单';
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - 批量导入物资', 'right'=>'<a href="/s.php?module=bom">返回列表</a>','body'=>$body));
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
							if($val[0]!=''){
								$this->kclass->DB->query("
									INSERT INTO originalbom
										(module,parent,parentVersion,parentItem,child,childVersion,childItem,quantity,unit,remark,created,creator)
									VALUES
										('".$val[0]."','".$val[1]."','".$val[2]."','".$val[3]."','".$val[4]."','".$val[5]."','".$val[6]."','".$val[7]."','".$val[8]."','".$val[10]."','".TIMENOW."','".$this->kclass->user['userid']."')
								");
							}
						}
					}
				}
			}
		}
		if($this->kclass->input['times']==$total){
			$this->kclass->messager(array(
				'title' => '批量导入bom单',
				'text' => 'bom单批量全部导入成功！',
				'url' => '/s.php?module=bom&action=listOriginal',
				'sec' => 2
			));
		}else{
			$this->kclass->messager(array(
				'title' => '批量导入bom单',
				'text' => '正在批量导入bom单，每次导入10个，请等待自动返回页面...',
				'url' => '/s.php?module=bom&action=doimport&times='.$this->kclass->input['times'],
				'sec' => 2
			));
		}
	}
	//
	function listOriginal(){
		$boms=$this->kclass->DB->query("SELECT * FROM originalbom ORDER BY originalBomid ASC");
		if($this->kclass->DB->numRows()){
			while($bom=$this->kclass->DB->fetchArray($boms)){
				$r.='<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').'">
				<td>'.$bom['parent'].'</td>
				<td>'.$bom['child'].'</td>
				<td>'.$bom['quantity'].'</a></td>
				<td>'.$bom['attritionRate'].'</td>
				<td>'.$bom['remark'].'</td></tr>';
			}
		}
$body .= <<<EOF
<table>
<thead>
<tr>
<th>父物料编码</th>
<th>子物料编码</th>
<th>数量</th>
<th>损耗率</th>
<th>备注</th>
</tr>
</thead>
<tbody>
{$r}
</tr>
</tbody>
</table>
EOF;
	$this->kclass->page['title'] = ' 原始bom单列表';
	$this->kclass->page['main'] .= $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 原始bom单列表', 'right' => '<a href="/s.php?module=bom&action=exchange">转换到bom单列表</a>', 'body'=>$body));
	}
	//
	function exchange(){
		$this->kclass->input['times']+=1;
		$originalBoms=$this->kclass->DB->query("SELECT * FROM originalbom ORDER BY originalBomid ASC");
		$counter=$this->kclass->DB->numRows();
		$total=ceil(($counter)/10);//刷新次数
		$count=$this->kclass->iif($this->kclass->input['times']==$total,$counter,($this->kclass->input['times'])*10);
		$min=($this->kclass->input['times']-1)*10;
		$max=$this->kclass->input['times']*10;
		if($counter){
			$i=0;
			while($originalBom=$this->kclass->DB->fetchArray($originalBoms)){
				if($i>=$min AND $max-$i<=10 AND $max-$i>0){
					// code
					}
				$i++;
			}
		}
		if($this->kclass->input['times']==$total){
			$this->kclass->messager(array(
				'title' => '加入bom单列表',
				'text' => '加入bom单列表成功！',
				'url' => '/s.php?module=bom&action=list',
				'sec' => 2
			));
		}else{
			$this->kclass->messager(array(
				'title' => '加入bom单列表',
				'text' => '正在批量加入bom单列表，每次加入10个，请等待自动返回页面...',
				'url' => '/s.php?module=bom&action=exchange&times='.$this->kclass->input['times'],
				'sec' => 2
			));
		}
	}

/********************* Private Function **********************/
	//
	function _bomList(){
		if($perpage=='')$perpage=50;
		$filterItem=$this->kclass->filterItem(array('module'=>'bom'));
		// 筛选
		$filter=$this->kclass->filter(
			array('module'=>'bom','action'=>'list','url'=>'&cache=no','table'=>'material','serial'=>array(
			array('name'=>'attrid','dname'=>'类　　别','status'=>$filterItem['attr']),
			array('name'=>'materialid','dname'=>'包　成　品','status'=>$filterItem['bmaterial']),
			array('name'=>'materialid','dname'=>'产　成　品','status'=>$filterItem['cmaterial'])
		)));
		if($this->kclass->input['attrid']>0){
			$condition=' AND material.attrid='.$this->kclass->input['attrid'];
		}elseif($this->kclass->input['materialid']>0){
			$condition=' AND material.materialid='.$this->kclass->input['materialid'];
		}
		if($this->kclass->input['p']){
			$pstr=explode(';',$this->kclass->input['p']);
			$count=count($pstr);
			foreach($pstr AS $key=>$val){
				if($key!=$count-1){
					$str.="'".$val."',";
				}else{
					$str.="'".$val."'";
				}
			}
			$condition=" AND (material.materialno IN ({$str}) OR materialitem.itemno IN ({$str}))";
		}elseif($this->kclass->input['c']){
			$cstr=explode(';',$this->kclass->input['c']);
			$count=count($cstr);
			foreach($cstr AS $key=>$val){
				if($key!=$count-1){
					$str.="'".$val."',";
				}else{
					$str.="'".$val."'";
				}
			}
			$childMaterials=$this->kclass->DB->query("
				SELECT b.parentVersionid
				FROM bom AS b
				LEFT JOIN material AS m ON (m.materialid=b.childid)
				LEFT JOIN materialitem AS mi ON (mi.itemid=b.childItemid)
				WHERE m.materialno IN ({$str}) OR mi.itemno IN ({$str})
				ORDER BY b.parentVersionid ASC
			");
			$count1=$this->kclass->DB->numRows();
			if($count1){
				$i=1;
				while($childMaterial=$this->kclass->DB->fetchArray($childMaterials)){
					if($i!=$count1){
						$parentVersionid.="'".$childMaterial['parentVersionid']."',";
					}else{
						$parentVersionid.="'".$childMaterial['parentVersionid']."'";
					}
					$i++;
				}
			}
			
			$condition=" AND `bom`.parentVersionid IN ({$parentVersionid})";
		}
		if($condition!=''){
			$perpage='1000';
		}
		$bomList=$this->kclass->listBom(array('start'=>'','perpage'=>$perpage,'filter'=>$filter['link'],'condition'=>$condition));
		$parentCount=$this->kclass->DB->queryFirst("SELECT COUNT(DISTINCT parentVersionid) AS count FROM `bom` WHERE killed=0 ORDER BY ordering ASC, bomid ASC");
		if($parentCount['count']>$perpage){
			$more='<div class="page"><a href="javascript:dc.tableItem.page('.$perpage.',\'bom\')">查看更多BOM</a></div>';
		}
		$r=$filter['panel'];
$r .= <<<EOF
<form action="/s.php?module=bom&action=list&cache=no" method="get">
<input type="hidden" name="module" value="bom">
<input type="hidden" name="action" value="list">
<input type="hidden" name="cache" value="no">
<table class="hundred">
<thead><tr><th colspan=3>搜索　　<span class="small gray">（注意：多个编号请用英文分号(;)分隔开，搜索时只能选择父编号和子编号中的一种进行搜索）</span></th></tr></thead>
<tbody>
<tr><td>请输入父编号</td><td><input type="text" name="p" value="{$this->kclass->input['p']}" size="200"></td><td><input type="submit" value="查询"></td></tr>
<tr><td>请输入子编号</td><td><input type="text" name="c" value="{$this->kclass->input['c']}" size="200"></td><td><input type="submit" value="查询"></td></tr>
</tbody>
</table>
</form>
<table class="hundred">
EOF;
		if($bomList['tr']){
			$r .= <<<EOF
<thead>
<tr><th width="30">ID</th><th width="700">物资</th><th width="30">版本</th><th width="60">单价</th><th width="40">库存</th><th width="60">数量</th><th width="60">损耗率</th><th width="60">来源</th><th width="30">通用</th><th width="210">备注</th><th width="180">操作</th></tr>
</thead>
<tbody id="tbody">
<tr nohover id="listtr" class="over"><td width="32">ID</td><td width="702">物资</td><td width="32">版本</td><td width="62">单价</td><td width="42">库存</td><td width="62">数量</td><td width="62">损耗率</td><td width="62">来源</td><td width="32">通用</td><td width="212">备注</td><td width="182">操作</td></tr>
{$bomList['tr']}
</tbody>
</table>
{$more}
EOF;
		}else{
			$r .= <<<EOF
<tr>
<td>暂无物料清单.</td>
</tr>
</form>
</table>
EOF;
				}
		return $r;
			}

	//
	function _chooser($c){
		if($c['bomid']==0){
			$r .= '<select name="'.$c['name'].'" style="width: 300">';
			if($c['displaytop']==1){
				$r .= '<option value="0"'.$this->kclass->iif($c['selectedid']==$c['bomid'], ' selected', '').'>'.$c['depth'].$c['topname'].'</option>';
			}
		} else{
			$condition = "bomid='".$c['bomid']."'";
			$bom = $this->kclass->DB->queryFirst("
				SELECT bomid,title
				FROM `bom`
				WHERE $condition
			");
			$r .= '<option value="'.$bom['bomid'].'"'.$this->kclass->iif($c['selectedid']==$c['bomid'], ' selected', '').'>'.$c['depth'].$bom['title'].'</option>';
		}

		$c['depth'] .= "--";

		$ncondition .= "parentid = '".$c['bomid']."'";

		$boms=$this->kclass->DB->query("
			SELECT bomid
			FROM `bom`
			WHERE $ncondition
			ORDER BY ordering
		");
		if($this->kclass->DB->numRows()){
			while($bom = $this->kclass->DB->fetchArray($boms)){
				$r .= $this->kclass->chooserCategory(array(
					'name' => 'bomid',
					'selectedid' => $c['selectedid'],
					'bomid' => $bom['bomid'],
					'depth' => $c['depth'],
					'topname' => $c['topname'],
					'displaytop' => 1,
					'displayid' => $c['displayid'],
					'userid' => $c['userid']
				));
			}
		}
		if($c['bomid'] == 0){
			$r .= '</select>';
		}

		return $r;
	}
	//
	function _getMaterialCost($id){
		if($id<0){
			return false;
		}
		$total='';
		$materials=$this->kclass->DB->query("
			SELECT price
			FROM `item`
			LEFT JOIN `porder` ON (`porder`.porderid=`item`.mid)
			WHERE `item`.killed=0 AND `porder`.ifVerify=1 AND `item`.module='porder' AND `item`.materialid='".$id."'
		");
		if($this->kclass->DB->numRows()){
			while($material=$this->kclass->DB->fetchArray($materials)){
				$total+=$material['price'];
			}
		}
		if($total!=''){
			$item=$this->kclass->DB->queryFirst("
				SELECT COUNT(itemid) AS count
				FROM `item`
				LEFT JOIN `porder` ON (`porder`.porderid=`item`.mid)
				WHERE `item`.killed=0 AND `porder`.ifVerify=1 AND `item`.module='porder' AND `item`.materialid='".$id."'
				LIMIT 0,1
			");
			$r=$total/$item['count'];
		}
		return $this->kclass->iif($r>0,$r,0);
	}
	
	//BOM清表格化导出
	function export(){
		if(!$this->kclass->input['versionid']){
			$e.='<li>请从正常页面访问</li>';
		}
		$version=$this->kclass->DB->queryFirst("SELECT versionid,materialid,itemid,price FROM materialversion WHERE versionid={$this->kclass->input['versionid']} LIMIT 0,1");
		if(!$version){
			$e.='<li>数据错误，很抱歉</li>';
		}
		if($e){
			$this->kclass->messager(array(
				'title' => '显示所有子集',
				'text' => '您在显示所有子集的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'final' => 1,
				'sec' => 3
			));
		}
		$parent=$this->kclass->getMaterial(array('materialid'=>$version['materialid'],'itemid'=>$version['itemid']));
		$children=$this->kclass->bomChildList(array('parentid'=>$version['materialid'],'versionid'=>$version['versionid'],'materialitemid'=>$version['itemid']));
		if($children['count']){
			$i=1;
			foreach($children['bom'] AS $child){
				$symbol='';
				for($k=2;$k<=$child['rank'];$k++){
					$symbol.='　|';
				}
				$materialInfo=$this->kclass->getMaterial(array('materialid'=>$child['materialid'],'itemid'=>$child['materialitemid']));
				$itemtr.='<tr>
					<td></td>
					<td align="left">【'.$child['rank'].'.'.$i.'】</td>
					<td><a href="'.$materialInfo['url'].'">'.$materialInfo['no'].'</a></td>
					<td>　'.$materialInfo['material'].'</td>
					<td>　'.$materialInfo['standard'].'</td>
					<td align="center">'.$child['version'].'</td>
					<td class="bold '.$this->kclass->iif($child['price']==0,'black','red').'">'.$this->kclass->iif($this->kclass->purviews(array('module'=>'material','action'=>'viewPrice','final'=>1)),$child['price'],'—').'</td>
					<td>'.$child['qualified'].'</td>
					<td align="center">'.$child['quantity'].'</td>
					<td align="center">'.$child['unit'].'</td>
					<td>'.$child['attritionRate'].'‰</td>
					<td>'.$materialInfo['origin'].'</td>
					<td>'.$materialInfo['cnGeneral'].'</td>
					<td>'.$child['remark'].'</td>
				</tr>';
				$i++;
			}
		}		
			$body= <<<EOF
<table class="hundred">
<thead class="title" align="left">
<a href="{$parent['url']}">{$parent['no']}　{$parent['material']}　{$parent['standard']}</a>　单价：{$version['price']}
</thead>
<tbody id="tbody">
<tr align="center">
<th width="10"></th>
<th>层级</th>
<th>编号</th>
<th>名称</th>
<th>属性</th>
<th width="30">版本</th>
<th width="75">单价</th>
<th width="60">库存</th>
<th width="45">数量</th>
<th width="30">单位</th>
<th width="60">损耗率</th>
<th width="70">来源</th>
<th width="30">通用</th>
<th width="150">备注</th>
</tr>
{$itemtr}
</tbody>
</table>
EOF;
		$this->kclass->page['onload'] .= 'dc.tabhover();dc.tips()';
		$this->kclass->page['title'] = '物料清单II - 列表';
		$this->kclass->page['main'] .= $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 列表', 'right' => $this->right.'　<a href="/s.php?module=bom&cache=1">更新BOM缓存</a>　<a href="/s.php?module=bom&action=import">导入物料清单</a>', 'body'=>$body));
	}
}
?>