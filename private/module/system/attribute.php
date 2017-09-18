<?php
/**
 * 产品属性的增删改查
 * @author zhourui <zhourui@wisdom.hk>
 * @version 1.0 2013-7-03
 * @copyright New Wisdom Investment Limited
 */
class attribute{
	# Global
	var $kclass;
	function autoRun(){
		$this->baseurl='<a href="/s.php?">首页</a>';
		switch($this->kclass->input['action']){
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
			default:
				$this->mmlist();
		}
	}
	
	
	/**
	 * 添加产品属性
	 * @author creator zhourui 2013-07-03 10:33 modifier zhourui 2013-12-10 15:18
	 * @access public
	 * @param none 没有参数
	 * @return none 没有返回值
	 * @throws none 没有异常
	 */
	function add(){
		if($this->kclass->input['materialid']>0){
			$module='material';
			$mid=$this->kclass->input['materialid'];
		}elseif($this->kclass->input['productid']>0){
			$module='product';
			$mid=$this->kclass->input['productid'];
			$moduleinfo=$this->kclass->DB->queryFirst("SELECT title FROM product WHERE killed=0 AND productid='".$mid."'");
			$moduleTitle=$moduleinfo['title'];
		}elseif($this->kclass->input['barcodeid']){
			$module='barcode';
			$mid=$this->kclass->input['barcodeid'];
		}
		
		$trList='';
		for($i=0;$i<5;$i++){
			$trList .= '<tr class="'.$this->kclass->iif($this->kclass->rotate(),'odd','even').'">';
			$trList .='<td>特有属性名称的属性值'.($i+1).'：</td><td>中文：<input type="text" name="value['.$i.']" style="width:230px;"> 英文：<input type="text" name="envalue['.$i.']" style="width:230px;"></td><td><input type="radio" name="default" value="'.$i.'" '.$this->kclass->iif($i==0,'checked','').'>设为默认</td>';
			$trList .='</tr>';
		}
$body=<<<EOF
<form action="/s.php?module=attribute&action=insert" name="attribute" method="post">
<input type="hidden" name="module" value="attribute" />
<input type="hidden" name="action" value="insert" />
<input type="hidden" name="m" value="{$module}" />
<input type="hidden" name="mid" value="{$mid}" />
<table style="width:100%;">
<thead><tr><th colspan="3">添加 {$moduleTitle} 特有属性信息</th></tr></thead>
<tbody>
<tr class="even">
<td width="180">特有属性名称：<span class="red bold">*</span></td><td>中文：<input type="text" name="title" value="" style="width:230px;" /> 英文：<input type="text" name="entitle" value="" style="width:230px;" /><td width="39%"><input type="radio" name="hidden" value="0" checked>显示 　　<input type="radio" name="hidden" value="1">不显示</td></td>
</tr>
{$trList}
<tr class="even">
<td>排　　　序：</td><td><input type="text" name="ordering" value="1" style="width:230px;"/></td><td></td>
</tr>
<tr class="even">
<td class="small gray" colspan="3">注意事项：确认信息正确无误</td>
</tr>
</tbody>
</table>
<table class="tlist tsuite" align="center" width="100%">
<tbody>
<tr class="even" nohover>
<td class="center" colspan=3><input type="submit" id="submitButton" value="  提交  " accesskey="s"><input type="reset" value="  复位  "/></td>
</tr>
</tbody>
</table>
</form>
EOF;

		$this->kclass->tbline+=4;
		$this->kclass->page['title'].='添加 '.$moduleTitle.' 特有属性';
		$this->kclass->page['onload'].='dc.tabhover();';
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - <a href="javascript:history.back();">产品['.$moduleTitle.']</a> - 添加 '.$moduleTitle.' 特有属性', 'right'=>'<a href="javascript:history.back();">返回</a>','body'=>$body));
	}
	
	/**
	 * 向数据库插入产品数据
	 * @author creator zhourui 2013-07-23 10:33
	 * @access public
	 * @param none 没有参数
	 * @return none 没有返回值
	 * @throws none 没有异常
	 */
	function insert(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e='<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			if($this->kclass->input['title']==''){
				$e.='<li>请输入属性的 中文名称 </li>';
			}
			$count=count($this->kclass->input['value']);
			$hasItem=0;
			for($i=0;$i<$count;$i++){
				if ($this->kclass->input['value'][$i]!='') {
					$hasItem=1;
			}
				}
			}
						
		if($e){
			$this->kclass->messager(array(
				'title' => '新增属性',
				'text' => '您在生成条码的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		
		$this->kclass->DB->query("
			INSERT INTO attributename
				(`module`,`mid`,`title`,`entitle`,`hidden`,`ordering`,`created`,`creator`)
			VALUES
				('".$this->kclass->input['m']."','".$this->kclass->input['mid']."','".$this->kclass->input['title']."','".$this->kclass->input['entitle']."','".$this->kclass->input['hidden']."','".$this->kclass->input['ordering']."','".TIMENOW."','".$this->kclass->user['userid']."')
		");
		$nameid=$this->kclass->DB->insertID();
		$counter=count($this->kclass->input['value']);
		for($i=0;$i<$counter;$i++){
			if($this->kclass->input['value'][$i]!=''){
				$defaultid=$this->kclass->iif($this->kclass->input['default']==$i,1,0);
				$this->kclass->DB->query("
					INSERT INTO attributevalue (`nameid`,`title`,`entitle`,`default`,`created`,`creator`)
				VALUES ('".$nameid."','".$this->kclass->input['value'][$i]."','".$this->kclass->input['envalue'][$i]."','".$defaultid."','".TIMENOW."','".$this->kclass->user['userid']."')
				");
			}
		}
		if($this->kclass->input['m']=='barcode'){
			$barcode=$this->kclass->DB->queryFirst("SELECT parentid FROM barcode WHERE scraped=0 AND barcodeid='".$this->kclass->input['mid']."' LIMIT 0,1");
			$url='/s.php?module='.$this->kclass->input['m'].'&action=view&'.$this->kclass->input['m'].'id='.$barcode['parentid'];
			$this->kclass->DB->query("UPDATE barcode SET statusid=1 WHERE barcodeid='".$this->kclass->input['mid']."'");
		}else{
			$url='/s.php?module='.$this->kclass->input['m'].'&action=view&'.$this->kclass->input['m'].'id='.$this->kclass->input['mid'];
		}
		
		$this->kclass->messager(array(
			'title' => '新增属性',
			'text' => '属性 <b>'.$this->kclass->input['title'].'</b> 已新建成功!返回属性详情页',
			'url' => $url,
			'sec' => 2
		));
	}

	/**
	 * 修改产品属性
	 * @author creator zhourui 2013-07-03 10:33
	 * @access public
	 * @param none 没有参数
	 * @return none 没有返回值
	 * @throws none 没有异常
	 */
	function update(){
		if($this->kclass->input['materialid']>0){
			$module='material';
			$mid=$this->kclass->input['materialid'];
			$moduleinfo=$this->kclass->DB->queryFirst("SELECT title FROM material WHERE killed=0 AND materialid='".$mid."'");
			$moduleTitle=$moduleinfo['title'];
			$condition='killed=0';
		}elseif($this->kclass->input['productid']){
			$module='product';
			$mid=$this->kclass->input['productid'];
			$condition='killed=0';
			$moduleinfo=$this->kclass->DB->queryFirst("SELECT title FROM product WHERE killed=0 AND productid='".$mid."'");
			$moduleTitle=$moduleinfo['title'];
		}elseif($this->kclass->input['barcodeid']){
			$module='barcode';
			$mid=$this->kclass->input['barcodeid'];
			$condition='scraped=0';
		}else{
			$this->kclass->boinkIt(REFERER);
		}

		if(!$moduleinfo){
			$this->kclass->messager(array(
				'title' => '修改属性',
				'text' => '属性 <b>'.$this->kclass->input['title'].'</b> 已新建成功!返回属性详情页',
				'url' => '/s.php?module='.$module.'&action=view&'.$module.'id='.$mid,
				'sec' => 2
			));
		}
		$names=$this->kclass->DB->query("
			SELECT nameid,title,entitle,hidden,ordering
			FROM  attributename 
			WHERE killed=0 AND module='".$module."' AND mid='".$mid."'
			ORDER BY ordering ASC
		");
		if($this->kclass->DB->numRows()){
			$i=1;
			while($name=$this->kclass->DB->fetchArray($names)){
				if($name['hidden']==1){
					$checked1='';$checked2=' checked';
				}elseif($name['hidden']==0){
					$checked1=' checked';$checked2='';
				}
				$itemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').'">
					<td width="100"><input type="hidden" name="nameId['.$i.']" value="'.$name['nameid'].'" ><span class="bold red middle">['.$i.']</span> 属性名称：</td>
					<td width="1000">中文：<input type="text" name="title['.$i.']" value="'.$name['title'].'" style="width:300px" /> 　　英文：<input type="text" name="entitle['.$i.']" value="'.$name['entitle'].'" style="width:300px" />　　排序：<input type="text" name="ordering['.$i.']" value="'.$name['ordering'].'" /></td>
					<td><input type="radio" name="hidden['.$i.']" value="0" '.$checked1.'>显示 　　<input type="radio" name="hidden['.$i.']" value="1" '.$checked2.'>不显示</td>
					<td><input type="checkbox" name="nameKill['.$i.']" value='.$name['nameid'].' />删</td>	
				</tr>';
				$values=$this->kclass->DB->query("
					SELECT valueid,title,entitle,`default`
					FROM  attributevalue 
					WHERE killed=0 AND nameid='".$name['nameid']."'
					ORDER BY created ASC
				");
				if ($this->kclass->DB->numRows()) {
					$j=1;
					while($value=$this->kclass->DB->fetchArray($values)){
						$itemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').' small">
							<td align="right"><input type="hidden" name="valueId['.$i.']['.$j.']" value="'.$value['valueid'].'">属性值['.$j.']：</td>
							<td>中文： <input type="text" name="value['.$i.']['.$j.']" value="'.$value['title'].'" style="width:303px" /> 　　 英文： <input type="text" name="envalue['.$i.']['.$j.']" value="'.$value['entitle'].'" style="width:303px" /></td>
							<td><input type="radio" name="default['.$i.']" value="'.$j.'" '.$this->kclass->iif($value['default']==1,'checked','').'>设为默认</td>
							<td><input type="checkbox" name="valueKill['.$i.']['.$j.']" value='.$value['valueid'].' />删</td>	
						</tr>';
						$j++;
			}
		}
				for($k=$j;$k<$j+2;$k++){
					$itemtr.='<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even').' small">
						<td align="right">属性值['.$k.']：</td>
						<td>中文： <input type="text" name="value['.$i.']['.$k.']" value="" style="width:303px" /> 　　 英文： <input type="text" name="envalue['.$i.']['.$k.']" value="" style="width:303px" /></td>
						<td><input type="radio" name="default['.$i.']" value="'.$k.'" >设为默认</td>
						<td></td>
					</tr>';
				}
				$itemtr.='<tr><td colspan=4></td></tr>';
				$i++;
			}
		}
		
$body=<<<EOF
<form action="/s.php?module=attribute&action=doupdate" name="attribute" method="post">
<input type="hidden" name="module" value="attribute" />
<input type="hidden" name="action" value="doupdate" /> 
<input type="hidden" name="m" value="{$module}" />
<input type="hidden" name="mid" value="{$mid}" />
<table style="width:100%">
<thead><tr><th colspan="19">修改{$moduleTitle}特有属性信息</th></tr></thead>
<tbody>
{$itemtr}
<tr class="odd">
<td class="small gray" colspan="19">注意事项：确认信息正确无误</td>
</tr>
</tbody>
</table>
<table class="tlist tsuite" align="center" width="100%">
<tbody>
<tr class="even" nohover>
<td class="center" coslpan=9><input type="submit" id="submitButton" value="  提交  " accesskey="s"><input type="reset" value="  复位  "/></td>
</tr>
</tbody>
</table>
</form>
EOF;
		$this->kclass->page['title'].='修改属性';
		$this->kclass->page['onload'].='dc.divhover();';
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - <a href="javascript:history.back();">产品['.$moduleTitle.']</a> - 修改 '.$moduleTitle.' 特有属性', 'right'=>'<a href="javascript:history.back();">返回</a>','body'=>$body));
	}

	
	/**
	 * 向数据库修改产品属性
	 * @author creator zhourui 2013-07-03 10:33 
	 * @access public
	 * @param none 没有参数
	 * @return none 没有返回值
	 * @throws none 没有异常
	 */
	function doupdate(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e='<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			$nameCount=count($this->kclass->input['nameId']);
			for($i=1;$i<=$nameCount;$i++){
				if($this->kclass->input['nameKill'][$i]=='' AND $this->kclass->input['title'][$i]==''){
					$e.='<li>请输入第'.$i.'个属性的 中文名称 </li>';
		}
				$valueCount=count($this->kclass->input['value'][$i]);
				$hasValue=0;
				for($j=1;$j<=$valueCount;$j++){
					if($this->kclass->input['value'][$i][$j]!=''){
						if(isset($this->kclass->input['valueKill'][$i][$j])){
							if($this->kclass->input['valueKill'][$i][$j]==''){
								$hasValue=1;	
			}
				}else{
							$hasValue=1;
						}
					}
						}
				if($hasValue==0){
					$e.='<li>请输入第 '.$i.' 个属性的 至少一个 属性值 </li>';
					}
				}
			}
		if($e){
			$this->kclass->messager(array(
				'title' => '修改属性',
				'text' => '您在修改属性的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}

		if($this->kclass->input['m']=='barcode'){
			$barcode=$this->kclass->DB->queryFirst("SELECT parentid FROM barcode WHERE scraped=0 AND barcodeid='".$this->kclass->input['mid']."' LIMIT 0,1");
			$url='/s.php?module='.$this->kclass->input['m'].'&action=view&'.$this->kclass->input['m'].'id='.$barcode['parentid'];
		}else{
			$url='/s.php?module='.$this->kclass->input['m'].'&action=view&'.$this->kclass->input['m'].'id='.$this->kclass->input['mid'];
		}

		for($i=1;$i<=$nameCount;$i++){				
			if($this->kclass->input['nameKill'][$i]>0 AND $this->kclass->input['nameKill'][$i]==$this->kclass->input['nameId'][$i]){
				$this->kclass->DB->query("UPDATE attributename SET killed='".TIMENOW."', killer='".$this->kclass->user['userid']."' WHERE nameid='".$this->kclass->input['nameId'][$i]."'");
			}else{
			if($this->kclass->input['title'][$i]!=''){
				$this->kclass->DB->query("
					UPDATE attributename SET 
						title='".$this->kclass->input['title'][$i]."',
						entitle='".$this->kclass->input['entitle'][$i]."',
							ordering='".$this->kclass->input['ordering'][$i]."',
						hidden='".$this->kclass->input['hidden'][$i]."',
						modified='".TIMENOW."',
						modifier='".$this->kclass->user['userid']."' 
					WHERE nameid='".$this->kclass->input['nameId'][$i]."'
				");
					$valueCount=count($this->kclass->input['value'][$i]);
					for($j=1;$j<=$valueCount;$j++){
						if($this->kclass->input['valueId'][$i][$j]>0){
							if($this->kclass->input['valueKill'][$i][$j]>0 AND $this->kclass->input['valueKill'][$i][$j]==$this->kclass->input['valueId'][$i][$j]){
						$this->kclass->DB->query("UPDATE attributevalue SET killed='".TIMENOW."', killer='".$this->kclass->user['userid']."' WHERE valueid='".$this->kclass->input['valueId'][$i][$j]."'");
					}else{
								if($this->kclass->input['value'][$i][$j]!=''){
									$defaultid=$this->kclass->iif($this->kclass->input['default'][$i]==$j,1,0);
									$this->kclass->DB->query("
										UPDATE attributevalue SET 
											title='".$this->kclass->input['value'][$i][$j]."',
											entitle='".$this->kclass->input['envalue'][$i][$j]."',
											`default`='".$defaultid."',
											modified='".TIMENOW."',
											modifier='".$this->kclass->user['userid']."'
										WHERE valueid='".$this->kclass->input['valueId'][$i][$j]."'
									");
								}
							}
							}else{
							if ($this->kclass->input['value'][$i][$j]!=''){
								$defaultid=$this->kclass->iif($this->kclass->input['default'][$i]==$i,1,0);
								$this->kclass->DB->query("
									INSERT INTO attributevalue (`nameid`,`title`,`entitle`,`default`,`created`,`creator`)
									VALUES ('".$this->kclass->input['nameId'][$i]."','".$this->kclass->input['value'][$i][$j]."','".$this->kclass->input['envalue'][$i][$j]."','".$defaultid."','".TIMENOW."','".$this->kclass->user['userid']."')
								");
							}
						}
					}
				}
			}
		}

		$this->kclass->messager(array(
			'title' => '修改属性',
			'text' => '属性  已修改成功!返回属性详情页',
			'url' => $url,
			'sec' => 2
		));
	}

	/**
	 * 向数据库删除产品属性
	 * @author creator zhourui 2013-07-03 10:33
	 * @access public
	 * @param none 没有参数
	 * @return none 没有返回值
	 * @throws none 没有异常
	 */
	function kill(){
		if($this->kclass->input['m']=='barcode'){
			$barcode=$this->kclass->DB->queryFirst("SELECT parentid FROM barcode WHERE scraped=0 AND barcodeid='".$this->kclass->input['mid']."' LIMIT 0,1");
			$url='/s.php?module='.$this->kclass->input['m'].'&action=view&'.$this->kclass->input['m'].'id='.$barcode['parentid'];
		}else{
			$url='/s.php?module='.$this->kclass->input['m'].'&action=view&'.$this->kclass->input['m'].'id='.$this->kclass->input['mid'];
		}
		if($this->kclass->input['nameid']<=0){
			$this->kclass->boinkIt($url);
		}
		if($this->kclass->input['confirm'] == 0){
		$this->kclass->boinkIt($url);
		}
		if($this->kclass->input['nameid'] < 0){
			$e='<li>请从正确的页面进行操作，如果此问题持续存在，请联系网站管理员。</li>';
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '删除属性',
				'text' => '您在删除属性的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}

		$attribute=$this->kclass->DB->queryFirst("
			SELECT title
			FROM attributename
			WHERE nameid='".$this->kclass->input['nameid']."'
		");
		if($attribute){
			$this->kclass->DB->query("
				UPDATE `attributename`
				SET killed=".TIMENOW.",killer='".$this->kclass->user['userid']."'
				WHERE nameid='".$this->kclass->input['nameid']."'
			");
			$this->kclass->messager(array(
				'title' => '删除属性成功',
				'text' => '属性 <b>'.$attribute['title'].'</b> 已成功被标记为删除!',
				'url' => $url,
				'sec' => 2
			));
		}else{
			$this->kclass->messager(array(
				'title' => '删除属性失败',
				'text' => '您要删除的属性，不存在！',
				'url' => $url,
				'sec' => 3
			));
		}
	}

	/**
	 * 向数据库移除产品属性
	 * @author creator zhourui 2013-07-03 10:33 
	 * @access public
	 * @param none 没有参数
	 * @return none 没有返回值
	 * @throws none 没有异常
	 */
	function remove(){
		if($this->kclass->input['materialid']>0){
			$module='material';
			$mid=$this->kclass->input['materialid'];
		}elseif($this->kclass->input['productid']){
			$module='product';
			$mid=$this->kclass->input['productid'];
		}elseif($this->kclass->input['barcodeid']){
			$module='barcode';
			$mid=$this->kclass->input['barcodeid'];
		}else{
			$this->kclass->boinkIt(REFERER);
		}
		if($this->kclass->input['nameid']<=0){
			$this->kclass->boinkIt(REFERER);
		}
		$attribute=$this->kclass->DB->queryFirst("
			SELECT title
			FROM `attributename`
			WHERE nameid='".$this->kclass->input['nameid']."'
		");
$body=<<<EOF
<form action="/s.php?module=attribute&action=kill" name="attribute" method="post">
<input type="hidden" name="module" value="attribute">
<input type="hidden" name="action" value="kill">
<input type="hidden" name="nameid" value="{$this->kclass->input['nameid']}">
<input type="hidden" name="m" value="{$module}" />
<input type="hidden" name="mid" value="{$mid}" />
<table><thead>
<thead>
<tr>
	<th>删除属性：{$attribute['title']}</th>
</tr>
</thead>
<tbody>
<tr class="even">
	<td class="middle">你确定要删除属性: <span class="big bold">{$attribute['title']}</span> 吗?</td>
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
		$this->kclass->page['title'].=' - 删除 - '.$attribute['title'];
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 删除属性 - '.$attribute['title'], 'body'=>$body));
	}

	
	/**
	 * 恢复产品属性
	 * @author creator zhourui 2013-07-03 10:33
	 * @access public
	 * @param none 没有参数
	 * @return none 没有返回值
	 * @throws none 没有异常
	 */
	function revival(){
		if($this->kclass->input['attributeid']<=0){
			$this->kclass->boinkIt('/s.php?module=attribute');
		}
		if(!$this->kclass->input['rt'] AND $this->kclass->input['confirm'] == 0){
			$this->kclass->boinkIt('/s.php?module=attribute&action=view&attributeid='.$this->kclass->input['attributeid']);
		}
		if($this->kclass->input['attributeid'] < 0){
			$e='<li>请从正确的页面进行操作，如果此问题持续存在，请联系网站管理员。</li>';
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '恢复属性',
				'text' => '您在恢复属性的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		$attribute=$this->kclass->DB->queryFirst("
			SELECT title
			FROM `attribute`
			WHERE attributeid='".$this->kclass->input['attributeid']."'
		");
		if($this->kclass->input['rt']=='view'){
			$rt='&action=view&attributeid='.$this->kclass->input['attributeid'];
		}elseif($this->kclass->input['rt']=='list'){
			$rt='#'.$this->kclass->input['attributeid'];
		}
		if($attribute){
			$this->kclass->DB->query("
				UPDATE `attribute`
				SET killed=0,killer=0
				WHERE attributeid='".$this->kclass->input['attributeid']."'
			");
			$this->kclass->messager(array(
				'title' => '恢复属性成功',
				'text' => '属性 <b>'.$attribute['title'].'</b> 已成功被恢复！',
				'url' => '/s.php?module=attribute'.$rt,
				'sec' => 2
			));
		}else{
			$this->kclass->messager(array(
				'title' => '恢复属性失败',
				'text' => '您要恢复的属性不存在！',
				'url' => '/s.php?module=attribute'.$rt,
				'sec' => 3
			));
		}
	}

	/**
	 * 确认向数据库恢复产品属性
	 * @author creator zhourui 2013-07-03 10:33 
	 * @access public
	 * @param none 没有参数
	 * @return none 没有返回值
	 * @throws none 没有异常
	 */
	function restore(){
		if($this->kclass->input['attributeid']<=0){
			$this->kclass->boinkIt('/s.php?module=attribute');
		}
		$attribute=$this->kclass->DB->queryFirst("
			SELECT title
			FROM `attribute`
			WHERE attributeid='".$this->kclass->input['attributeid']."'
		");
$body=<<<EOF
<form action="/s.php?module=attribute&action=revival" name="attribute" method="post">
<input type="hidden" name="module" value="attribute">
<input type="hidden" name="action" value="revival">
<input type="hidden" name="attributeid" value="{$this->kclass->input['attributeid']}">
<table><thead>
<thead>
<tr>
	<th>恢复属性：{$attribute['title']}</th>
</tr>
</thead>
<tbody>
<tr class="even">
	<td class="middle">你确定要恢复属性: <a href="/s.php?module=attribute&action=view&attributeid={$this->kclass->input['attributeid']}" class="big bold" target="_blank">{$attribute['title']}</a> 吗?</td>
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
		$this->kclass->page['title'].=' - 恢复 - '.$attribute['title'];
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - 恢复属性 - '.$attribute['title'], 'right' => '<a href="/s.php?module=attribute">返回列表</a>', 'body'=>$body));
	}
}
?>