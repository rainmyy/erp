<?php
// 
class category{
	# Global
	var $kclass;
	var $cache=array();
	var $icache=array();

	/*--------------------*/
	// Auto run function
	/*--------------------*/
	function autoRun(){
		$this->kclass->page['title'] = '类别';
		$this->baseurl = '<a href="/s.php?module=home">首页</a> - <a href="/s.php?module=category">类别</a>';
		$this->right = '<a href="/s.php?module=category&action=add">新建类别</a>';

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
			case 'remove':
				$this->remove();
				break;
			case 'kill':
				$this->kill();
				break;
			case 'doorder':
				$this->doorder();
				break;
			default:
				$this->mmlist();
		}
	}
	
	// 
	function _genealogy(){
		$categorys=$this->kclass->DB->query("SELECT * FROM `category` ORDER BY categoryid ASC");
		if($this->kclass->DB->numRows()){
			while($category=$this->kclass->DB->fetchArray($categorys)){
				$this->cache[$category['categoryid']]=array('categoryid'=>$category['categoryid'], 'parentid'=>$category['parentid'], 'parentlist'=>$category['parentlist'], 'childlist'=>$category['childlist']);
				$this->icache[$category['parentid']][$category['categoryid']]=$category['categoryid'];
			}
//			echo '<pre>'.print_r($this->cache, 1).'</pre>';exit;
			foreach($this->cache as $id=>$category){
				//parent list
				$i=0;
				$curid=$id;
				$this->cache[$id]['parentlist']='';
				while($curid!=-1 AND $i++<1000){
					if($curid){
						$this->cache[$id]['parentlist'].=$curid.',';
						$curid=$this->cache[$curid]['parentid'];
					}
				}
				$this->cache[$id]['parentlist'].='-1';
				//child list
				$this->cache[$id]['childlist']=$id;
				$this->_childList($id, $id);
				$this->cache[$id]['childlist'].=',-1';
			}
			$parentsql = '';
			$childsql = '';
			foreach($this->cache AS $id => $category){
				$parentsql .= "	WHEN $id THEN '$category[parentlist]'";
				$childsql .= "	WHEN $id THEN '$category[childlist]'";
			}
			$this->kclass->DB->query("
				UPDATE category SET
					parentlist = CASE categoryid
					$parentsql
				ELSE parentlist
				END,
					childlist = CASE categoryid
					$childsql
				ELSE childlist
				END
			");
		}else{
			return '';
		}
	}
	// 
	function _childList($mainid,$parentid){
		if(is_array($this->icache[$parentid])){
			foreach($this->icache[$parentid] as $id=>$pid){
				$this->cache[$mainid]['childlist'].=','.$id;
				$this->_childList($mainid,$id);
			}
		}
	}
	// 
	function insert(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$errors[] = '请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。';
		}else{
			if($this->kclass->input['parentid']>0){
				$parent = $this->kclass->DB->queryFirst("
					SELECT categoryid,title,parentlist,rank 
					FROM `category` 
					WHERE categoryid='".$this->kclass->input['parentid']."'
				");
				if($parent){
					$parents = explode(",", $parent['parentlist']);
					$touchon = 0;
					while(list(,$val) = each($parents)){
						if($val==$this->kclass->input['categoryid']){
							$touchon = 1;
						}
					}
				}

				if($touchon){
					$errors[] = '你不能将一个列项设置为它的子列项的列项！';
				}
			}else{
				$parent['rank']=-1;
			}
			if($this->kclass->input['title'] == ''){
				$errors[] = '请填写类别标题。';
			}
			if($this->kclass->input['entitle'] == ''){
				$errors[] = '请填写类别 英文标题。';
			}
		}

		if(count($errors)){
			$error = implode("</li>\n<li>",$errors);
			$error = "<ul>\n<li>".$error."</li></ul>";
			$this->kclass->messager(array(
					'title' => '新建类别',
					'text' => '您在新建类别的过程中有以下错误: '.$error,
					'url' => 'javascript:history.back()',
					'final' => 1,
					'sec' => 3
				)
			);
		}
		$this->kclass->DB->query("
			INSERT INTO `category` 
				(`title` , `parentid` , `entitle` ,  `abbr` ,`description` , `rank` , `publish` , `ordering` , `display` , `creator` , `created`) 
			VALUES 
				( '".$this->kclass->input['title']."', '".$this->kclass->input['parentid']."', '".$this->kclass->input['entitle']."','".$this->kclass->input['abbr']."', '".$this->kclass->input['description']."', '".($parent['rank']+1)."', '".$this->kclass->input['publish']."', '".$this->kclass->input['ordering']."', '".$this->kclass->input['display']."', '".$this->kclass->user['userid']."', '".TIMENOW."')
		");
		$categoryid=$this->kclass->DB->insertID();
		$this->_genealogy($categoryid);

		$this->kclass->messager(array(
				'title' => '新建类别',
				'text' => '类别 <b>'.$this->kclass->input['title'].'</b> 已新建成功!',
				'url' => '/s.php?module=category',
				'final' => 1,
				'sec' => 3
			)
		);
	}

	// 
	function add(){
		$select = $this->kclass->chooserCategory(array(
				'name' => 'parentid', 
				'selectedid' => $this->kclass->input['parentid'],
				'categoryid' => -1, 
				'displaytop' => 1, 
				'hasBlank' => 1, 
				'topname' => '根',
				'showall' => 1,
				'width' =>306
			)	
		);

$body = <<<EOF
<form action="/s.php?module=category&action=insert" name="name" method="post">
<input type="hidden" name="module" value="category">
<input type="hidden" name="action" value="insert">
<table>
<thead>
<tr>
	<th colspan="2">添加类别：</th>
</tr>
</thead>
<tbody>
<tr class="even">
	<td>上级列项：</td>
	<td>{$select}</td>
</tr>
<tr class="odd">
	<td>中文标题：<font color="red">*</font></td>
	<td><input type="text" style="width: 300px;" name="title" value=""></td>
</tr>
<tr class="even">
	<td>英文标题：<font color="red">*</font></td>
	<td><input type="text" style="width: 300px;" name="entitle" value=""></td>
</tr>
<tr class="odd">
	<td>字母缩写：</td>
	<td><input type="text" style="width: 300px;" name="abbr" value=""><br><span class="gray small">是分类的字母缩写，物资分类的物资分类下必须填写</span></td>
</tr>
<tr class="even">
	<td valign="top">描　　述：</td>
	<td><textarea name="description" style="width: 300px;height: 50px;"></textarea></td>
</tr>
<tr class="odd">
	<td>顺　　序：</td>
	<td><input type="text" style="width: 300px;" name="ordering" value="1"></td>
</tr>
<tr class="even">
	<td>系统显示：</td>
	<td>
		<label for="display_1"><input type="radio" id="display_1" name="display" value="1" class="nostyle" checked>显示</label> 
		<label for="display_0"><input type="radio" id="display_0" name="display" value="0" class="nostyle">不显示</label> 
	</td>
</tr>
<tr class="odd">
	<td>网站显示：</td>
	<td>
		<label for="publish_1"><input type="radio" id="publish_1" name="publish" value="1" class="nostyle">显示</label> 
		<label for="publish_0"><input type="radio" id="publish_0" name="publish" value="0" class="nostyle" checked>不显示</label> 
	</td>
</tr>
<tr class="even">
	<td colspan="2" class="gray small">如果选择不显示，那么网站前台所有该类别下的产品页将不显示，即使类别下的产品是“发布”状态。</td>
</tr>
<tr class="odd">
	<td colspan="2" align="center">
		<input type="submit" name="submit" value="   保存   " accesskey="s">
		<input type="reset" value="   重置   ">
	</td>
</tr>
</tbody>
</table>
</form>
EOF;
		$this->kclass->page['onload'] .= 'dc.tabhover();';
		$this->kclass->page['title'] = ' - 新建类别';
		$this->kclass->page['main'] .= $this->kclass->skin->dbody(array('title' => $this->baseurl.' - 新建类别', 'right' => '<a href="/s.php?module=category">返回列表</a>','body'=>$body));
	}

	// 
	function doupdate(){
		if($this->kclass->input['parentid'] == $this->kclass->input['categoryid']){
			$errors[] = '你不能将一个类别自身选做它的父类别！且不能设置一个类为根类。';
		}else{
			if($this->kclass->input['parentid']>0){
				$parent = $this->kclass->DB->queryFirst("
					SELECT categoryid,title,parentlist,rank 
					FROM `category` 
					WHERE categoryid='".$this->kclass->input['parentid']."'
				");
				$parents = explode(",", $parent['parentlist']);
				$touchon = 0;
				while(list(,$val) = each($parents)){
					if($val==$this->kclass->input['categoryid']){
						$touchon = 1;
					}
				}
			}else{
				$parent['rank']=-1;
			}

			if($touchon){
				$errors[] = '你不能将一个列项设置为它的子列项的列项！';
			}
			if($this->kclass->input['title'] == ''){
				$errors[] = '请填写类别标题。';
			}
			if($this->kclass->input['entitle'] == ''){
				$errors[] = '请填写类别 英文标题 标题。';
			}
		}

		if(count($errors)){
			$error = implode("</li>\n<li>",$errors);
			$error = "<ul>\n<li>".$error."</li></ul>";
			$this->kclass->messager(array(
					'title' => '修改类别',
					'text' => '您在修改类别的过程中有以下错误：'.$error,
					'url' => 'javascript:history.back()',
					'final' => 1,
					'sec' => 3
				)
			);
		}
		$this->kclass->DB->query("
			UPDATE `category` 
			SET title = '".$this->kclass->input['title']."', 
				entitle = '".$this->kclass->input['entitle']."', 
				description = '".$this->kclass->input['description']."', 
				abbr = '".$this->kclass->input['abbr']."', 
				ordering = '".$this->kclass->input['ordering']."', 
				parentid = '".$this->kclass->input['parentid']."', 
				rank='".($parent['rank']+1)."', 
				display = '".$this->kclass->input['display']."', 
				publish = '".$this->kclass->input['publish']."', 
				modifier = '".$this->kclass->user['userid']."', 
				modified = '".TIMENOW."' 
			WHERE categoryid='".$this->kclass->input['categoryid']."'
		");
		$this->_genealogy($categoryid);

		$this->kclass->messager(array(
				'title' => '修改类别',
				'text' => '类别 <b>'.$this->kclass->input['title'].'</b> 已成功被修改!',
				'url' => '/s.php?module=category',
				'final' => 1,
				'sec' => 3
		));
	}

	// 
	function update(){
		if($this->kclass->input['categoryid']<=0){
			$this->kclass->boink_it('/s.php?module=category.html');
		}
		$category = $this->kclass->DB->queryFirst("
			SELECT *
			FROM category
			WHERE categoryid='".$this->kclass->input['categoryid']."'
		");
		if($category['display'] == 1){
			$display_1 = ' checked';
		}else{
			$display_0 = ' checked';
		}
		if($category['publish'] == 1){
			$publish_1 = ' checked';
		}else{
			$publish_0 = ' checked';
		}
		$select = $this->kclass->chooserCategory(array(
				'name' => 'parentid', 
				'selectedid' => $category['parentid'],
				'categoryid' => 0, 
				'displaytop' => 1, 
				'hasBlank' => 1, 
				'topname' => '根',
				'showall' => 1,
				'width'=>306
			)	
		);
$body = <<<EOF
<form action="/s.php?module=category&action=doupdate" name="name" method="post">
<input type="hidden" name="module" value="category">
<input type="hidden" name="action" value="doupdate">
<input type="hidden" name="categoryid" value="{$category['categoryid']}">
<table>
<thead>
<tr>
	<th colspan="2">修改类别: </th>
</tr>
</thead>
<tbody>
<tr class="even">
	<td>上级列项：</td>
	<td>{$select}</td>
</tr>
<tr class="odd">
	<td>中文标题：<font color="red">*</font></td>
	<td><input type="text" style="width: 300px;" name="title" value="{$category['title']}"></td>
</tr>
<tr class="even">
	<td>英文标题：<font color="red">*</font></td>
	<td><input type="text" style="width: 300px;" name="entitle" value="{$category['entitle']}"></td>
</tr>
<tr class="odd">
	<td>字母缩写：</td>
	<td><input type="text" style="width: 300px;" name="abbr" value="{$category['abbr']}"><br><span class="gray small">是分类的字母缩写，物资分类的物资分类下必须填写</span></td>
</tr>
<tr class="odd">
	<td valign="top">描　　述：</td>
	<td><textarea name="description" style="width: 300px;height: 50px;">{$category['description']}</textarea></td>
</tr>
<tr class="even">
	<td>顺　　序：</td>
	<td><input type="text" style="width: 300px;" name="ordering" value="{$category['ordering']}"></td>
</tr>
<tr class="odd">
	<td>系统显示：</td>
	<td>
		<label for="display_1"><input type="radio" id="display_1" name="display" value="1" class="nostyle"{$display_1}>显示</label> 
		<label for="display_0"><input type="radio" id="display_0" name="display" value="0" class="nostyle"{$display_0}>不显示</label> 
	</td>
</tr>
<tr class="even">
	<td>网站显示：</td>
	<td>
		<label for="publish_1"><input type="radio" id="publish_1" name="publish" value="1" class="nostyle"{$publish_1}>显示</label> 
		<label for="publish_0"><input type="radio" id="publish_0" name="publish" value="0" class="nostyle"{$publish_0}>不显示</label> 
	</td>
</tr>
<tr class="odd">
	<td colspan="2" class="gray small">如果选择不显示，那么网站前台所有该类别下的产品页将不显示，即使类别下的产品是“发布”状态。</td>
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
		$this->kclass->page['onload'] .= 'dc.tabhover();';
		$this->kclass->page['title'] = ' - 修改类别';
		$this->kclass->page['main'] .= $this->kclass->skin->dbody(array('title' => $this->baseurl.' - 修改类别', 'right' => $this->right, 'body'=>$body));
	}

	// 
	function kill(){
		if($this->kclass->input['confirm'] == 0){
			$this->kclass->boink_it('/s.php?module=category.html');
		}
		if($this->kclass->input['categoryid'] < 0){
			$errors[] = '请从正确的页面进行操作，如果此问题持续存在，请联系网站管理员。';
		}
		if(count($errors)){
			$error = implode("</li>\n<li>",$errors);
			$error = "<ul>\n<li>".$error."</li></ul>";
			$this->kclass->messager(array(
					'title' => '删除类别',
					'text' => '您在删除类别的过程中有以下错误：'.$error,
					'url' => 'javascript:history.back()',
					'final' => 1,
					'sec' => 3
				)
			);
		}

		$category = $this->kclass->DB->queryFirst("
			SELECT title, parentid 
			FROM `category` 
			WHERE categoryid = '".$this->kclass->input['categoryid']."'
		");
		$categorys=$this->kclass->DB->query("
			SELECT categoryid 
			FROM `category` 
			WHERE INSTR(CONCAT(',',parentlist,','), ',".$this->kclass->input['categoryid'].",')>0
		");
		$categorylist = $this->kclass->input['categoryid'];
		if($this->kclass->DB->numRows()){
			while($thiscategory=$this->kclass->DB->fetchArray($categorys)){
				$categorylist .= ",$thiscategory[categoryid]";
			}
		}
		$this->kclass->DB->query("
			DELETE FROM `category` 
			WHERE categoryid IN(".$categorylist.")
		");
		$this->kclass->DB->query("
			UPDATE `category` 
			SET node =(node - 1) 
			WHERE categoryid IN(".$categorylist.")
		");
		$this->kclass->DB->query("
			UPDATE `category` 
			SET node =(node - 1) 
			WHERE categoryid = '".$category['parentid']."'
		");
		$this->kclass->messager(array(
				'title' => '删除类别',
				'text' => '类别 <b>'.$category['title'].'</b> 已成功被删除!',
				'url' => '/s.php?module=category',
				'final' => 1,
				'sec' => 3
			)
		);
	}

	// 
	function remove(){
		$category = $this->kclass->DB->queryFirst("
			SELECT title
			FROM category
			WHERE categoryid='".$this->kclass->input['categoryid']."'
		");
$body = <<<EOF
<form action="/s.php?module=category&action=kill" name="name" method="post">
<input type="hidden" name="module" value="category">
<input type="hidden" name="action" value="kill">
<input type="hidden" name="categoryid" value="{$this->kclass->input['categoryid']}">
<table>
<thead>
<tr>
	<th colspan="2">删除类别: </th>
</tr>
</thead>
<tbody>
<tr class="even">
	<td colspan="2">你确定要删除类别: <b>{$category['title']}</b> 吗?<br />(注意：此列项下包含的所有子类也会同时被删除！)</td>
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
		$this->kclass->page['title'] = ' - 删除类别';
		$this->kclass->page['main'] .= $this->kclass->skin->dbody(array('title' => $this->baseurl.' - 删除类别', 'right' => $this->right,'body'=>$body));
	}

	// 
	function doorder(){
		foreach($this->kclass->input['order'] as $k => $v){
			$this->kclass->DB->query("
				UPDATE category
				SET ordering='".$v."'
				WHERE categoryid='".$k."'
			");
		}
		
		$this->kclass->messager(array(
				'title' => '类别排序',
				'text' => '类别 重新排序 成功!',
				'url' => '/s.php?module=category',
				'final' => 1,
				'sec' => 3
			)
		);
	}

	// 
	function mmlist(){
		$category = $this->_categorylist();
$body = <<<EOF
<form action="/s.php?module=category&action=doorder" name="name" method="post">
<input type="hidden" name="module" value="category">
<input type="hidden" name="action" value="doorder">
<table>
EOF;

if($category){
$body .= <<<EOF
<thead>
<tr>
	<th>ID</th>
	<th width="70">网站发布</th>
	<th width="40">层级</th>
	<th width="250">中文</th>
	<th width="180">英文</th>
	<th width="50">建子类</th>
	<th width="30">排序</th>
	<th width="70">选项</th>
</tr>
</thead>
<tbody>
{$category}
</tr>
<tr>
	<td colspan="10" align="center">
		<input type="submit" name="submit" value="   保存   " accesskey="s">
		<input type="reset" value="   重置   ">
	</td>
</tr>
</tbody>
</table>
</form>
EOF;
}else{
$body .= <<<EOF
<tr>
	<td>暂无类别.</td>
</tr>
</form>
</table>
EOF;
}
		$this->kclass->page['onload'] .= 'dc.tabhover();';
		$this->kclass->page['title'] = '类别 - 列表';
		$this->kclass->page['main'] .= $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 列表', 'right' => $this->right, 'body'=>$body));
	}


/********************* Private Function **********************/
	// 
	function _categoryList($parentid=-1,$rank=''){
		$categorys=$this->kclass->DB->query("
			SELECT * 
			FROM `category` 
			WHERE parentid='".$parentid."'
			ORDER BY ordering ASC, categoryid ASC
		");
		if($this->kclass->DB->numRows()){
			while($category=$this->kclass->DB->fetchArray($categorys)){
				$rank='';
				if($category['rank']>0){
					for($i=0;$i<$category['rank'];$i++){
						$rank.='　　';
					}
				}
				$tr.='<tr class="'.$this->kclass->rotateLine().'"><td align="right">'.$category['categoryid'].'</td><td align="center">'.$this->kclass->iif($category['publish'] == 1, '是', '否').'</td><td align="center">'.$this->kclass->iif($category['rank']>0, $category['rank'], '').'</td><td class="middle bold">' . $rank. '<a href="/s.php?module=product&action=list&categoryid='.$category['categoryid'].'">'.$this->kclass->iif($category['abbr']=='','','('.$category['abbr'].')').$category['title'].'</a></td><td>'.$category['entitle'].'</td><td align="center"><a href="/s.php?module=category&action=add&parentid='.$category['categoryid'].'">添加</a></td><td align="right"><input type="text" name="order['.$category['categoryid'].']" size="1" value="'.$category['ordering'].'"></td><td><a href="/s.php?module=category&action=remove&categoryid='.$category['categoryid'].'" class="small">删除</a> <a href="/s.php?module=category&action=update&categoryid='.$category['categoryid'].'">编辑</a></td></tr>';
				$tr.=$this->_categorylist($category['categoryid'], $rank);
			}
		}
		return $tr;
	}

	// 
	function _chooser($c){
		if($c['categoryid']==0){
			$r .= '<select name="'.$c['name'].'" style="width: 300">';
			if($c['displaytop']==1){
				$r .= '<option value="0"'.$this->kclass->iif($c['selectedid']==$c['categoryid'], ' selected', '').'>'.$c['depth'].$c['topname'].'</option>';
			}
		} else{
			$condition = "categoryid='".$c['categoryid']."'";
			$category = $this->kclass->DB->queryFirst("
				SELECT categoryid,title
				FROM `category`
				WHERE $condition
			");
			$r .= '<option value="'.$category['categoryid'].'"'.$this->kclass->iif($c['selectedid']==$c['categoryid'], ' selected', '').'>'.$c['depth'].$category['title'].'</option>';
		}

		$c['depth'] .= "--";

		$ncondition .= "parentid = '".$c['categoryid']."'";

		$categorys=$this->kclass->DB->query("
			SELECT categoryid 
			FROM `category` 
			WHERE $ncondition
			ORDER BY ordering
		");
		if($this->kclass->DB->numRows()){
			while($category = $this->kclass->DB->fetchArray($categorys)){
				$r .= $this->kclass->chooserCategory(array(
					'name' => 'categoryid', 
					'selectedid' => $c['selectedid'], 
					'categoryid' => $category['categoryid'], 
					'depth' => $c['depth'], 
					'topname' => $c['topname'], 
					'displaytop' => 1, 
					'displayid' => $c['displayid'], 
					'userid' => $c['userid']
				));
			}
		}
		if($c['categoryid'] == 0){
			$r .= '</select>';
		}

		return $r;
	}
}
?>