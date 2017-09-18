<?php
//
class fkclass extends kclass{
	var $tbline=0;
	var $material;
	var $itemType=array(1=>array('title'=>'购买','entitle'=>'charge'),2=>array('title'=>'免费','entitle'=>'free')); // 订单明细的购买方式
	var $month=array(
			array('value'=>-100,'title'=>'全部'),
			array('value'=>1,'title'=>'一月'),
			array('value'=>2,'title'=>'二月'),
			array('value'=>3,'title'=>'三月'),
			array('value'=>4,'title'=>'四月'),
			array('value'=>5,'title'=>'五月'),
			array('value'=>6,'title'=>'六月'),
			array('value'=>7,'title'=>'七月'),
			array('value'=>8,'title'=>'八月'),
			array('value'=>9,'title'=>'九月'),
			array('value'=>10,'title'=>'十月'),
			array('value'=>11,'title'=>'十一月'),
			array('value'=>12,'title'=>'十二月'));
	var $year=array(
			array('value'=>-100,'title'=>'全部'),
			array('value'=>2013,'title'=>'2013年'),
			array('value'=>2014,'title'=>'2014年'),
			array('value'=>2015,'title'=>'2015年'),
			array('value'=>2016,'title'=>'2016年'),
			array('value'=>2017,'title'=>'2017年'),
			array('value'=>2018,'title'=>'2018年'),
			array('value'=>2019,'title'=>'2019年'),
			array('value'=>2020,'title'=>'2020年')
		);
	//
	function purviews($p){
		if(($this->user['groupid']!=1 AND $this->user['groupid']!=3) OR $this->user['killed']>0)$this->messager(array('title' => '无效请求','text' => '您的请求是无效的（无权限访问此内容）。<span class="tiny">ID: 1</span>','url' => '/s.php?module=sign&action=out','sec' => 60));
		if($p['module']=='')$p['module']=$this->input['module'];
		if($p['action']=='')$p['action']=$this->input['action'];
		if($this->user['inherit'] == 1)$purview = $this->user['purviews'];
		else $purview = $this->user['purview'];
		if($purview==100){
			return true;
		}
		if($purview == ''){
			$m = false;
		}else{
			$m = $this->DB->queryFirst("
				SELECT m.moduleid
				FROM `module` AS m
				WHERE m.moduleid IN (".$purview.") AND m.`module`='".$p['module']."' AND INSTR(m.`action`,'".(strtolower($p['action']))."')>0
			");
		}
		if($m){
			return true;
		}else{
			if($p['final'] == 1){
				return false;
			}else{
				$this->messager(array('title' => '无效请求','text' => '您的请求是无效的（无权限访问此内容）。<span class="tiny">ID: 2</span>','url' => (REFERER == '' OR strstr(REFERER, 'module=sign&action=complete'))?'/s.php':'javascript:history.back()','sec' => 5));
			}
		}
	}

	//
	function menu(){
		if($this->user['purviews']==100){
			$condition=' AND 1=1';
		}else{
			$condition=" AND m1.`moduleid` IN (".$this->iif($this->user['inherit']==1,$this->user['purviews'],$this->user['purview']).")";
		}
		$ms = $this->DB->query("
			SELECT m1.menuid, m1.parentid, m1.moduleid, m1.title, m1.icon, m1.`module`, m1.`action`, m1.`addition`,
				m2.title AS parent
			FROM `menu` AS m1
			LEFT JOIN `menu` AS m2 ON (m2.menuid = m1.parentid)
			WHERE m1.killed=0 $condition
			ORDER BY m2.ordering ASC , m1.ordering ASC
		");
		if($this->DB->numRows()){
			$pid = -1;
			$u = '<ul id="menu" class="menu">';
			while($m = $this->DB->fetchArray($ms)){
				if($pid != $m['parentid']){
					if($pid>0)$u .= '</ul></li><li></li>';//if has sub add ul/li tail
					$u .= '<li>'.$this->iif($m['parentid']==0, $m['title'].'</li><li></li>', $this->iif($m['parent']=='个人', ucfirst($this->user['username']),$m['parent']).'<ul>');
					$pid = $m['parentid'];
				}
				if($m['parentid'] > 0){
					$u .= '<li>';
					if($m['icon']!='')$u='<img src="/public/menu/'.$m['icon'].'" width="16" height="16" />';// if icon is true
					if($m['module'] != ''){//if url is true
						$u .= '<a href="/s.php?module='.$m['module'];
						if($m['action'] != '')$u.='&action='.$m['action'];
						if($m['addition']!='')$u.='&'.$m['addition'];
						$u.='">';
					}
					$u .= $m['title'];
					if($m['module'] != '')$u.='</a>';
					$u .= '</li>';
				}
			}
			if($pid > 0)$u .= '</ul></li>';
			$u .= '<li></li><li class="disabled">'.date("H:i", TIMENOW).'</li></ul>';
		}
		return $u;
	}
	//
	function uploadList($u){
		if($u['module'] != '' AND $u['mid'] > 0){
			$condition = " OR (`module`='".$u['module']."' AND `mid`='".$u['mid']."')";
		}
		$as = $this->DB->query("
			SELECT *
			FROM `attachs`
			WHERE (`module`='' AND `mid`=0 AND creator='".$this->user['userid']."')".$condition."
			ORDER BY `mid` DESC, ordering ASC, modified DESC, created DESC
		");
		if($this->DB->numRows()){
			$fileList='<table width="100%"><thead><tr><th>原始文件名</th><th>大小</th><th>上传日期</th><th>标题</th><th>排序</th><th>归属</th><th class="small"><span class="hand" onclick="dc.upload.selectAll(0);" title="设置所有未决的附件为：保留状态">全留</span> | <span class="hand" onclick="dc.upload.selectAll(2);" title="设置所有未决的附件为：删除状态">全删</span> | <span class="hand" onclick="dc.upload.selectAll(1);" title="设置所有未决的附件为：选择状态">全选</span></th></tr></thead><tbody>';
			while($a = $this->DB->fetchArray($as)){
				$fileList.='<tr class="'.($this->rotate()?'odd':'even');
				$c0=$c1=$tick='';
				if($this->input['t']==$a['tmpid'] OR ($a['module']==$u['module'] AND $a['mid']==$u['mid'])){
					if($a['module']==$u['module'] AND $a['mid']==$u['mid']){
						$tick = '<span class="big bold green" title="原有附件">√</span> ';
					}else{
						$fileList.=' gray';
					}
					$c1=' checked';
				}else{
					$c0=' checked';
					$tick = '';
					$fileList.=' gray';
				}
				if($a['image']==1){
					$img = unserialize($a['imginfo']);
					$thumb = '<img src="/public/attachs/'.$img['thumb'].'" height="20" onmouseover="dc.upload.showThumb(this, \''.$img['thumb'].'\')" onmouseout="dc.upload.hiddenThumb()" /> ';
				}else{
					$thumb = '';
				}
				$fileList.='"><td class="middle">'.$thumb.$a['relatedPSampleItem'].'</td><td class="tiny" align="right">'.$this->sizeFormat($a['size']).'</td><td class="tiny gray">'.date('y-m-d H:i:s', $a['created']).'</td><td class="small"><input type="hidden" name="attachOrgTitle['.$a['attachid'].']" value="'.$a['title'].'"><input type="text" name="attachTitle['.$a['attachid'].']" value="'.$a['title'].'" size="30"></td><td class="small"><input type="hidden" name="attachOrgOrdering['.$a['attachid'].']" value="'.$a['ordering'].'"><input type="text" name="attachOrdering['.$a['attachid'].']" value="'.$a['ordering'].'" size="1" style="width:20px"></td><td align="center">'.$tick.'</td><td class="small"><label for="attach['.$a['attachid'].']0" class="gray"><input type="radio" id="attach['.$a['attachid'].']0" name="attach['.$a['attachid'].']" value="0"'.$c0.'>留</label> <label for="attach['.$a['attachid'].']2" class="red"><input type="radio" id="attach['.$a['attachid'].']2" name="attach['.$a['attachid'].']" value="2">删</label> <label for="attach['.$a['attachid'].']1" class="green"><input type="radio" id="attach['.$a['attachid'].']1" name="attach['.$a['attachid'].']" value="1"'.$c1.'>选</label></td></tr>';
			}
			$fileList.='</tbody></table>';
		}else{
			$fileList='还没有未归属的已上传文件。';
		}
		return $fileList;
	}

	//
	function upload($u){
		$t = TIMENOW;
		$this->page['header'] = '<script src="/public/swfupload.js"></script>';
		$this->page['onload'].='dc.upload.init(\''.$this->sess->session['id'].'\', \''.$t.'\');';
		if($u['title']=='')$u['title']='附件管理：';

		$fileList = $this->uploadList(array('module'=>$u['module'], 'mid'=>$u['mid']));
		$this->tbline+=2;
return <<<EOF
	<table class="hundred">
	<thead>
	<tr>
		<th colspan="4">{$u['title']}<input type="hidden" id="uploadModule" name="uploadModule" value="{$u['module']}"><input type="hidden" id="uploadMid" name="uploadMid" value="{$u['mid']}"><input type="hidden" id="timenow" name="t" value="{$t}"></th>
	</tr>
	</thead>
	<tbody>
	<tr class="odd">
		<td align="center" style="border-right:0"><input id="showUploaded" type="button" value="已传附件" title="选择已上传未归属的附件" onclick="dc.upload.showUploaded();" /></td>
		<td align="center" style="border-right:0"><input id="cancelUpload" type="button" value="全部取消" onclick="dc.upload.cancelQueue();" disabled="disabled" /></td>
		<td align="center" style="border-right:0"><input id="startUpload" type="button" value="上传文件" onclick="dc.upload.handle.startUpload();" disabled="disabled" /></td>
		<td align="center"><span id="selectFile"></span></td>
	</tr>
	<tr nohover>
		<td colspan="4"><div id="fileQueue"></div><div id="fileList">{$fileList}</div></td>
	</tr>
	<tr class="even">
		<td colspan="4" class="gray small">上传说明：<ul><li>每次只能上传20个文件；队列累计上限为20，如需要超过这个，保存后，再次进入修改页面继续上传；</li><li>允许的文件类型：jpg, jpeg, png, gif, rar, zip, pdf, doc, docx, xls,xlsx, pps, ppt, txt, mht, htm, html, ai, dwg；</li><li>每个文件大小不能超过10MB；图片文件系统会自动生成缩略图，建议图片首选jpg或jpeg格式；</li><li>“已传附件”是指 一个月内已上传的单还未设定归属的附件；超过1个月未归属的附件系统自动删除。</li></ul></td>
	</tr>
	</tbody>
	</table>
EOF;
	}
//

	//
	function updateAttachs($a){
		if(count($this->input['attach']) > 0){
			$fileCount = 0;
			foreach($this->input['attach'] as $k => $v){
				if($v == 0 OR $v==1){//0:保留，需要检查title与ordering是否更改。1:选择
					$cdt = '';
					if($this->input['attachTitle'][$k] != $this->input['attachOrgTitle'][$k])$cdt = "`title`='".$this->input['attachTitle'][$k]."', ";
					if($this->input['attachOrdering'][$k] != $this->input['attachOrgOrdering'][$k])$cdt .= "`ordering`='".$this->input['attachOrdering'][$k]."', ";
					if($v == 1)$cdt.= "`module`='".$a['module']."', `mid`='".$a['mid']."', ";
					if($cdt != ''){
						$this->DB->query("UPDATE `attachs` SET ".$cdt."`modifier`='".$this->user['userid']."', `modified`='".TIMENOW."' WHERE `attachid`='".$k."'");
					}
					$fileCount++;
				}elseif($v==2){//删除
					$attach = $this->DB->queryFirst("SELECT `folder`, `newName`, `image`, `imginfo` FROM `attachs` WHERE `attachid`='".$k."'");
					if($attach['image'] == 1){
						$img = unserialize($attach['imginfo']);
						@unlink('public/attachs/'.$img['thumb']);
						@unlink('public/attachs/'.$img['senior']);
						@unlink('public/attachs/'.$img['big']);
					}
					@unlink('public/attachs/'.$attach['folder'].'/'.$attach['newName']);
					$this->DB->query("DELETE FROM `attachs` WHERE `attachid`='".$k."'");
				}
			}
			if($fileCount > 0){
				$tmp = '';
				if($attach = $this->DB->queryFirst("SELECT COUNT(attachid) AS n, imginfo FROM `attachs` WHERE `module`='".$a['module']."' AND `mid`='".$a['mid']."' AND `image`=1 ORDER BY ordering ASC")){
					$tmp = ", `images`='".$attach['n']."'";
					if($attach['imginfo']!=''){
						$img=unserialize($attach['imginfo']);
						$tmp .= ", `cover`='/public/attachs/".$img['thumb']."'";
					}
				}
				$this->DB->query("UPDATE `".$a['module']."` SET attachs='".$fileCount."'".$tmp." WHERE `".$a['module']."id`='".$a['mid']."'");
			}
			return true;
		}else{
			return false;
		}
	}
	//
	function getAttachs($g){
		if($g['module']=='' OR !$g['mid']>0)return false;
		$attachs = $this->DB->query("
			SELECT *
			FROM `attachs`
			WHERE `module`='".$g['module']."' AND `mid`='".$g['mid']."'
			ORDER BY `ordering` ASC, `modified` DESC, `created` DESC
		");
		if($this->DB->numRows()){
			$r = '';
			while($attach = $this->DB->fetchArray($attachs)){
				$r .= '<div class="attach">';
				if($attach['image'] == 1){
					$img = unserialize($attach['imginfo']);
					$r .= '<a href="/public/attachs/'.$img['name'].'" target="_blank"><img src="/public/attachs/'.$img['thumb'].'" width="128" height="128"/></a> ';
				}else{
					$r .= '<span class="middle"><a href="/public/attachs/'.$attach['folder'].'/'.$attach['newName'].'" target="_blank" title="从新窗口打开或下载">'.$attach['orgName'].'</a></span><div class="small gray">'.strtoupper($attach['type']).', '.$this->sizeFormat($attach['size']).'</div>';
				}
				$r .= '</div>';
			}
			return $r;
		}else{
			return false;
		}
	}
	//
	function questionlist($b){
    if($b['orderby']==''){
		$b['orderby']='ordering';
	}
	if($b['direction']==''){
		$b['direction']='ASC';
	}
	if($b['action']=='add'){
		$action="WHERE `questionnaire`.relationshipid='".$b['relationshipid']."' AND `questionnaire`.ifkill=0";
		
	}else{
		$action="WHERE 1=1 AND `questionnaire`.ifkill=0";
	}
	$sql=$this->DB->query("SELECT `questionnaire`. * ,`customertype`.title AS ctitle ,`customertype`.entitle AS centitle,m.realname AS mname,b.realname AS bname
	                                 FROM `questionnaire`
									 LEFT JOIN `customertype`				 ON(`customertype`.typeid=`questionnaire`.relationshipid)
									 LEFT JOIN `member`		AS	m      ON(m.memberid=`questionnaire`.creator)
									 LEFT JOIN `member`		AS	b			ON(b.memberid=`questionnaire`.modifier)
									 ".$action."
									 ORDER BY '".$b['orderby']."' '".$b['direction']."'");
   if($b['action']=='list'){
    if($this->DB->numRows()){
		$i=1;
		while($questions=$this->DB->fetchArray($sql)){
			$date=date('Y-m-d',$questions['created']);
			if($questions['modified']!=0){
				$modified=$questions['bname'].'于'.date('Y-m-d',$questions['modified']);
			}else{
				$modified='<span class="red">未修改</span>';
			}
			$body.='
			<tr class="'.$this->rotateLine().'">
			<td>'.$i.'</td>
			<td></td>
			<td><a href="/s.php?module=customer&action=questionview&questionnaireid='.$questions['id'].'">'.$questions['title'].'</a></td>
			<td>'.$questions['ctitle'].'('.$questions['centitle'].')</td>
			<td>'.$date.'</td>
			<td>'.$questions['mname'].'</td>
			<td>'.$modified.'</td>
			<td align="center"><a href="/s.php?module=customer&action=updatequestionnaire&questionnaireid='.$questions['id'].'">修改</a>　｜　<a href="/s.php?module=customer&action=removequestion&questionnaireid='.$questions['id'].'">删除</a></td>
			</tr>
			';
			$i++;
		}
	}
   }elseif($b['action']=='add'){
    if($this->DB->numRows()){
		$i=1;
		while($questions=$this->DB->fetchArray($sql)){
			$body.='<tr class="'.$this->rotateLine().'">
			<td>'.$i.'</td>
			<td></td>
			<td><a href="/s.php?module=customer&action=questionview&questionnaireid='.$questions['id'].'">'.$questions['title'].'</a></td>
			<td>'.$questions['ctitle'].'('.$questions['centitle'].')</td>
			<td>'.$date.'</td>
			<td>'.$questions['mname'].'</td>
			<td><input type="radio" name="questionnaireid" value="'.$questions['id'].'"></td>
			</tr>';
			$i++;
		}
		}else{
			$body="<tr><td colspan='7'><a href>该分类下还没有建立调查问卷，点击新建</href></td></tr>";
		}
	
   }
   return $body;
	}
	//
	function questionchooser($b){
		if($b['questionnaireid']==''){
			$direction='questionnaireid=1';
		}
		if($b['type']==''){
			$type=' AND type=1';
		}else{
			$type='AND type='.$b['type'].'';
		}
	      if($b['customerid']==''){
		     $find=$this->DB->query("SELECT `chooser`.title,`chooser`.typeid ,q.chooseid AS qchooseid
			 FROM   `questionitem`  AS q  
			 LEFT JOIN `chooser`  ON(q.itemid=`chooser`.itemid)
			 WHERE q.itemid='".$b['itemid']."'");
			 if($this->DB->numRows()){
				 while($chooses=$this->DB->fetchArray($find)){
					 if($chooses['qchooseid']==1){
					$choosers.='　<label for radio'.$i.'><input type="radio" name="'.$b['itemid'].'" value="'.$chooses['typeid'].'" id='.$i.' /> '.$chooses['title'].'</label>　';
					 }elseif($chooses['qchooseid']==2){
						 $choosers.='<input type="checkbox" name="'.$b['itemid'].'[]" value="'.$chooses['typeid'].'">'.$chooses['title'].'';
					 }elseif($chooses['qchooseid']==3){
						 $choosers='<textarea rows="4" cols="80" name="'.$b['itemid'].'" >'.$items['choosers'].'</textarea>';
					 }
				 }
			 }   
			}else{
				 $sql=$this->DB->query("SELECT `chooser`.title,`chooser`.typeid ,q.chooseid
		                        FROM  `questionitem` AS q
								LEFT JOIN  chooser   ON(q .itemid=`chooser`.itemid)
		                        WHERE q.questionnaireid='".$b['questionnaireid']."' AND q.itemid='".$b['itemid']."' ");
		if($num=$this->DB->numRows()){
			$i=1;
			while($chooses=$this->DB->fetchArray($sql)){
				$item=$this->DB->query("SELECT c.relationshipid,c.questionnaireid,c.relationshiptypeid ,i.itemid,t.choosers
				                      FROM `customerrelationship` AS c 
									   LEFT JOIN `customerchooser` AS t ON(t.relationshipid=c.relationshipid)
									   LEFT JOIN `questionitem` AS i ON(i.itemid=t.itemid)
								    WHERE c.customerid='".$b['customerid']."' AND i.itemid='".$b['itemid']."' AND c.relationshipid='".$b['relationshipid']."'");
				
					while($items=$this->DB->fetchArray($item)){
				if($b['chooserid']==1){
					if($chooses['typeid']==$items['choosers']){
						$action='checked';
					}else{
                        $action='';
					}
				$choosers.='　<label for '.$b['itemid'].'><input type="radio" name="'.$b['itemid'].'" value="'.$chooses['typeid'].'" id='.$b['itemid'].' '.$action.'/ disabled> '.$chooses['title'].'</label>　';
				}elseif($b['chooserid']==2){
					$array=explode('-',$items['choosers']);
					if(in_array($chooses['typeid'],$array)){
						$action='checked';
					}else{
						$action='';
					}
					$choosers.='　<label for '.$b['itemid'].'><input type="checkbox" id="'.$b['itemid'].'" name="'.$b['itemid'].'[]" value="'.$chooses['typeid'].'"'.$action.' disabled/>'.$chooses['title'].'</label>　';
				}elseif($b['chooserid']==3){
					$choosers='<textarea rows="4" cols="80" name="'.$b['itemid'].'" disabled>'.$items['choosers'].'</textarea>';
				}
				}
			}
				$i++;
			}
         
			}
		return $choosers;
	}
	//
	function typechooser($b){
		if($b['num1']==''){
			$b['num1']==1;
		}
      for($k=1;$k<=5;$k++){
		  $chooser.='选项'.$k.':　<input type="text" name="chooser['.$b['num1'].']['.$k.']" size="8" >';
	  }
	  return $chooser;
	}
	//
	function STquotingorder($b){
       if($b['orderby']==''){
		   $b['orderby']='ordering';
	   }
	   if($b['direction']==''){
		   $b['direction']='ASC';
	   }
	   if($b['action']=='add'){
		   $condition=' ifverify=1';
	   }
	  if($b['action']=='list'){
	   $this->input['QuotingID']=intval($this->input['QuotingID']);
	   if($this->input['QuotingID']>0){
		   $STQuoting=$this->DB->queryFirst("SELECT quotingid FROM `stquoting` WHERE `stquoting`.quotingid='".$this->input['QuotingID']."'");
		   if($STQuoting){
			   $this->boinkIt('/s.php?module=customer&action=STQuotingview&STQuotingid='.$STQuoting['quotingid'].'');
		   }
	   }
	    if($this->input['show']='default'){
		   $condition.='`stquoting`.killed=0 AND `stquoting`.quotingid>2';
	   }else{
		   $condition.='1=1 AND `stquoting`.quotingid>2';
	   }
	   $this->input['Quotingname']=trim($this->input['Quotingname']);
	   $this->input['Quotingtype']=trim($this->input['Quotingtype']);
	   $this->input['Quotingport']=trim($this->input['Quotingport']);
	   $this->input['Quotingcurrency']=trim($this->input['Quotingcurrency']);
	   if($this->input['Quptingname']!=''){
		   $condition="AND INSTR(LCASE(`stquoting`.cntitle),".(strtolower($this->input['Quotingname'])).")>0";
		   $query['Quotingname']=$this->input['Quotingname'];
	   }
	   if($this->input['Quotingtype']!=''){
		   $condition.="AND  `stquoting`.typeid=".$this->input['Quotingtype']."";
		   $query['Quotingtype']=$this->input['Quotingtype'];
	   }
      if($this->input['Quotingport']!=''){
			$condition.="AND `stquoting`.portid=".$thiss->input['Quotingport']."";
			$query['Quotingport']=$this->input['Quotingport'];
	  }
	  if($this->input['Quotingcurrency']!=''){
		  $condition.="AND `stquoting`.currencyid=".$this->input['Quotingcurrency']."";
		  $query['Quotingcurrency']=$this->input['Quotingcurrency'];
	  }
	  
	  if($this->input['ordertype']!='')$query['ordertype']=$thiss->input['ordertype'];
	  if($this->input['direction']!='')$query['direction']=$this->input['direction'];
	  if($this->input['show']!='')$query['show']=$this->kclass->input['show'];
	  $query['layout']=$this->iif($thiss->input['layout']!='',$this->input['layout'],$this->input['layout']='list');
      }
	   $sql=$this->DB->query("SELECT * FROM `stquoting` WHERE ".$condition." ORDER BY ".$b['orderby']."  ".$b['direction']."");
	  
		$body.='<tbody>';
		if($this->DB->numRows()){
			$i=1;
			while($order=$this->DB->fetchArray($sql)){
				if($order['entitle']!=''){
					$title=$order['cntitle'].'('.$order['entitle'].')';
				}else{
					$title=$order['entitle'];
				}
				$item=$this->DB->queryFirst("
					SELECT  p.entitle AS pentitle,p.title AS ptitle, c.title AS ctitle,qt.title AS qttitle,u.realname AS uname,m.realname AS mname
					FROM `stquoting` AS st
					LEFT JOIN `port`					AS	p		ON ( p.portid=st.portid)
					LEFT JOIN `currency`			AS	c		ON (c.currencyid=st.currencyid)
					LEFT JOIN `quotingtype`	AS	qt		ON (qt.typeid=st.typeid)
					LEFT JOIN `member`			AS	u		ON  (u.userid=st.creator)
					LEFT JOIN	`member`		AS	m		ON	(m.userid=st.verifier)
					WHERE st.killed=0 AND st.quotingid='".$order['quotingid']."'
				");
				$doverify=$this->doverify(array('module'=>'customer','action'=>'doverify'));
				if($doverify){
				if($order['ifverify']==0){
					$ifverify='未审核';
					$action="<td class=\"".$this->iif($order['ifverify']==1,'green','red')."\"><a href=\"/s.php?module=customer&action=verify&stquotingid=".$order['quotingid']."\">".$ifverify."</a></td>";
				}elseif($order['ifverify']==1){
					$ifverify='已审核';
					$action="<td class=\"".$this->iif($order['ifverify']==1,'green','red')."\">".$ifverify."</td>";
				}elseif($order['ifverify']==-1){
					$ifverify='审核未通过';
					$action="<td class=\"".$this->iif($order['ifverify']==1,'green','red')."\"><a href=\"/s.php?module=customer&action=verify&stquotingid=".$order['quotingid']."\">".$ifverify."</a></td>";
				}
					}else{
                         if($order['ifverify']==0){
					$ifverify='未审核';
					$action="<td class=\"".$this->iif($order['ifverify']==1,'green','red')."\">".$ifverify."</td>";
				}elseif($order['ifverify']==1){
					$ifverify='已审核';
					$action="<td class=\"".$this->iif($order['ifverify']==1,'green','red')."\">".$ifverify."</td>";
				}elseif($order['ifverify']==-1){
					$ifverify='审核未通过';
					$action="<td class=\"".$this->iif($order['ifverify']==1,'green','red')."\">".$ifverify."</td>";
				}
					}
				if($order['verified']!=0){
					$verifier=$item['mname'].'('.date('Y-i-d H:m:s',$order['verified']).')';
				}else{
					$verifier="未审核";
				}
				if($b['action']=='list'){
				$body.="<tr class='odd'>
				 <td>".$i."</td><td><a href=\"/s.php?module=customer&action=STQuotingview&STQuotingid=".$order['quotingid']."\">".$title."</a></td><td>".$item['qttitle']."</td>".$action."<td>".$item['ptitle']."(".$item['pentitle'].")</a></td><td>".$item['ctitle']."</td>
				 <td>".$item['uname']."(".date('Y-i-d H :m:s',$order['created']).")</td><td class=\"".$this->iif($order['ifverify']==1,'green','red')."\">".$verifier."</td><td><a href=\"/s.php?module=customer&action=STQuotingremove&STQuotingid=".$order['quotingid']."\">删除</a>|<a href='/p.php?module=printer&action=STQuotingprint&STQuotingid=".$order['quotingid']." 'target='_blank'>打印</a></span></td>
				</tr>";
				}elseif($b['action']=='add'){
				$body.="<tr class='odd'>
				 <td>".$i."</td><td>".$title."</td><td>".$item['qttitle']."</td><td>".$item['ptitle']."(".$item['pentitle'].")</td><td>".$item['ctitle']."</td>
				 <td>".$order['creator']."(".date('Y-i-d H :m:s',$order['created']).")</td>
				<td><input type='radio' name='stquoting' value=".$order['quotingid'].">选择</td>
				</tr>";
				}
				$i++;
			}
		}else{
			$body.='<tr><td colspan="9"><a href="/s.php?module=customer&action=STQuoting">创建标准报价单</a></td></tr>';
		}
		$body.='</tbody>';
		return $body;
	}
    //
	function customerItems($s){
		$customernum=$this->DB->query("SELECT customerid FROM `customer` WHERE `customer`.customerid IN(".implode(',',$s['customerid']).")  ");
		$customernums=$this->DB->numRows();
			$pagesize=20;
	  $allpages=intval( $customernums/$pagesize);
		if( $customernums%$pagesize)$allpages++;
		if($s['page']==''){
			$showpage=1;
		}else{
			$showpage=$s['page'];
		}
        $offset=$pagesize*($showpage-1);
		$pagecount=1;
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
			WHERE `customer`.customerid IN (".implode(',',$s['customerid']).") 
			ORDER BY `customer`.customerid LIMIT  ".$offset.",".$pagesize."
		";

       

		$customers = $this->DB->query($sql);
		if($num=$this->DB->numRows()){
			$i=1;
			while($customer=$this->DB->fetchArray($customers)){
                      $tempStr .='<tr>
						<td>'.$this->id(array('customer'=>$customer['customerid'])).'</td>
						<td ></td>
						<td><a href="/s.php?module=customer&action=view&customerid='.$customer['customerid'].'">'.$customer['customerno'].' <span class="normal" title="'.$customer['title'].'">'.$title.'</span></a><br>
						<span class="small">'.$customer['businessTitle'].'</span> '.$this->iif($customer['attachs']>0, ' <span class="attachFile" title="有'.$customer['attachs'].'个附件。"></span> ', '').$this->iif($customer['images']>0, ' <span class="attachImage" title="有'.$customer['images'].'个图片。"></span> ', '').'<span class="normal">'.$this->iif(strlen($customer['cntitle'])>12, mb_substr($customer['cntitle'], 0, 12, 'UTF-8').'...', $customer['cntitle']).'</span></td>
						<td>'.$customer['source'].'</td>
						<td>'.$sourcedate.'</td>
						<td>'.$linkman.' <span class="small">('.$customer['position'].')</span></td>
						<td>'.$email.'</td>
						<td>电话：'.$telephone.$this->iif($customer['mobile']!='', '<br>手机：'.$mobile, '').'</td>
						<td>'.$region.'</td>
						<td class="small">'.$customer['tracker'].'</td>
						<td class="small" title="由 '.$customer['creator'].' 创建于 '.date('Y-m-d H:m', $customer['created']).'。">'.$customer['creator'].'<br>'.date('y-m-d H:m', $customer['created']).'</td>'.$this->iif($customer['modified']>$customer['created'], '<td class="small" title="由 '.$customer['modifier'].' 修改于 '.date('Y-m-d H:m', $customer['modified']).'。">'.$customer['modifier'].'<br>'.date('y-m-d H:m', $customer['modified']).'</td>', '<td>--</td>').'
						<td align="center">'.$link.'</td>
					</tr>';
			}
			
			$tempStr.='共'.$allpages.'页　<a href="/s.php?module=customer&action=list&page=1">首页</a>　';
			if($showpage>1){
				$uppage=$showpage-1;
			$tempStr.='<a href="/s.php?module=customer&action=list&page='.$uppage.'">上一页</a>　';
			}
        while($pagecount<=$allpages){
			  if($pagecount==$showpage){
				   $tempStr.=$showpage.'&nbsp;';
			  }else{
                $tempStr.='<a href="/s.php?module=customer&action=list&page='.$pagecount.'">'.$pagecount.'</a>　';
			  }
			  $pagecount++;
		   }
         if($showpage<$allpages){
		  $downpage=$showpage+1;
		  $tempStr.='<a href="/s.php?module=customer&action=list&page='.$downpage.'">下一页</a>　';
		}
		  $rtn['panel']=$tempStr;
		$rtn['num']=$customernums;
		}else{
			return;
		}
		
		return $rtn;
	}
	//
	function orderby($s){
		if($s['module']=='' OR !is_array($s['serial'])){
			return;
		}
		if($s['action']!=''){
			$action='&action='.$s['action'];
		}
		if($this->input['direction']==''){
			$this->input['direction']=$s['direction'];
		}
		if($this->input['direction']=='asc'){
			$s['direction']='asc';
			$direction='desc';
		}else{
			$s['direction']='desc';
			$direction='asc';
		}
		if($s['start'] AND $s['prepage']){
			$limit=' LIMIT '.$s['start'].','.$s['prepage'];
		}
		if($this->input['orderby']!=''){
			$s['orderby']=$this->input['orderby'];
		}elseif($s['default']!=''){
			$s['orderby']=$s['default'];
		}else{
			$s['orderby']='modified';
		}
		if(is_array($s['appendUrl'])){
			foreach($s['appendUrl'] as $k => $v){
				if($k!='' AND $k!='orderby' AND $k!='direction'){
					$a.='&'.$k.'='.$v;
				}
			}
		}
		foreach($s['serial'] as $k => $v){
			if($v['word']==''){
				$v['word']=$v['field'];
			}
			$r['link'].='<a href="/s.php?module='.$this->iif($s['amodule']!='',$s['amodule'],$s['module']).$action.$a.'&orderby='.$v['word'];
			if($s['orderby']==$v['word'] OR $s['orderby']==$v['field']){
				$r['link'].='&direction='.$direction.'"><span class="bold">'.$v['title'].'<span class="'.$s['direction'].'"> </span>';
				if($s['module']=='psupplier'){
					$r['sql']='`supplier`.'.$v['field'].' '.strtoupper($s['direction']);
				}else{
					$r['sql']='`'.$s['module'].'`.'.$v['field'].' '.strtoupper($s['direction']);
				}
			}else{
				$r['link'].='">'.$v['title'].'';
			}
			$r['link'].='</a>　';
		}
		return $r;
	}
	//
	function filterItem($s){
		// 筛选
		$filterType=$filterDepartment=array();
		$i=0;
		// 审核状态
		$r['verify']=array(array('value'=>-100,'title'=>'全部'),array('value'=>-1,'title'=>'未审核'),array('value'=>0,'title'=>'待审核'),array('value'=>1,'title'=>'已审核'));
		// 审批状态
		$r['approve']=array(array('value'=>-100,'title'=>'全部'),array('value'=>-1,'title'=>'未审批'),array('value'=>0,'title'=>'待审批'),array('value'=>1,'title'=>'已审批'));
		//出库
		if($s['module']=='outbound'){
			// 出库状态
			$r['complete']=array(array('value'=>-100,'title'=>'全部'),array('value'=>0,'title'=>'待出库'),array('value'=>1,'title'=>'部分出库'),array('value'=>2,'title'=>'全部出库'));
			// 出库类型
			$r['outboundType'][$i]['value']=-100;
			$r['outboundType'][$i]['title']='全部';
			$outboundTypes=$this->DB->query("
				SELECT typeid,title
				FROM inorouttype
				WHERE module='outbound'
				ORDER BY typeid
			");
			if($this->DB->numRows()){
				while($outboundType=$this->DB->fetchArray($outboundTypes)){
					$i++;
					$r['outboundType'][$i]['value']=$outboundType['typeid'];
					$r['outboundType'][$i]['title']=$outboundType['title'];
				}
			}
		}elseif($s['module']=='inbound'){
			// 入库状态
			$r['complete']=array(array('value'=>-100,'title'=>'全部'),array('value'=>0,'title'=>'待入库'),array('value'=>1,'title'=>'部分入库'),array('value'=>2,'title'=>'全部入库'));
			// 入库类型
			$r['inboundType'][$i]['value']=-100;
			$r['inboundType'][$i]['title']='全部';
			$inboundTypes=$this->DB->query("
				SELECT typeid,title
				FROM inorouttype
				WHERE module='inbound'
				ORDER BY typeid
			");
			if($this->DB->numRows()){
				while($inboundType=$this->DB->fetchArray($inboundTypes)){
					$i++;
					$r['inboundType'][$i]['value']=$inboundType['typeid'];
					$r['inboundType'][$i]['title']=$inboundType['title'];
				}
			}
		}

		// 部门
		$r['department'][$i]['value']=-100;
		$r['department'][$i]['title']='全部';
		$departments=$this->DB->query("SELECT departmentid,title,parentid FROM department WHERE display=1 ORDER BY departmentid ASC");
		if($this->DB->numRows()){
			while($department=$this->DB->fetchArray($departments)){
				$i++;
				$r['department'][$i]['value']=$department['departmentid'];
				$r['department'][$i]['title']=$department['title'];
				$r['department'][$i]['parentid']=$department['parentid'];
			}
		}
		// 月份
		$r['month']=$this->month;
		// 物资类别
		$r['attr'][$i]['value']=-100;
		$r['attr'][$i]['title']='全部';
		$attrs=$this->DB->query("SELECT attrid,title FROM attr WHERE module='material' ORDER BY attrid ASC");
		if($this->DB->numRows()){
			while($attr=$this->DB->fetchArray($attrs)){
				$i++;
				$r['attr'][$i]['value']=$attr['attrid'];
				$r['attr'][$i]['title']=$attr['title'];
			}
		}
		// 包产品
		$r['bmaterial'][$i]['value']=-100;
		$r['bmaterial'][$i]['title']='全部';
		$materials=$this->DB->query("SELECT materialid,title FROM material WHERE attrid=8");
		if($this->DB->numRows()){
			while($material=$this->DB->fetchArray($materials)){
				$i++;
				$r['bmaterial'][$i]['value']=$material['materialid'];
				$r['bmaterial'][$i]['title']=$material['title'];
			}
		}
		// 产产品
		$r['cmaterial'][$i]['value']=-100;
		$r['cmaterial'][$i]['title']='全部';
		$materials=$this->DB->query("SELECT materialid,title FROM material WHERE attrid=7");
		if($this->DB->numRows()){
			while($material=$this->DB->fetchArray($materials)){
				$i++;
				$r['cmaterial'][$i]['value']=$material['materialid'];
				$r['cmaterial'][$i]['title']=$material['title'];
			}
		}
		
		return $r;
	}
	/**
 	 * 返回筛选html 、url
 	 * @author creator zhourui 2013-11-4 11:30
 	 * @access public
 	 * @param array $b (serial,name,title,module,action)
 	 * @return array $str 返回修订版本信息
 	 * @throws none 没有异常
 	 */
	function filter($s){
		if(empty($s['serial'])){
			return false;
		}
		$item='';$i=1;
		$count=count($s['serial']);
		foreach($s['serial'] as $k=>$v){
			if($this->input[$v['name']]!=''){
				$query[$v['name']]=$this->input[$v['name']];
			}
			if(is_array($query) AND count($query)>0)$queryPart='&'.http_build_query($query);
		}
		if($s['module']=='inventory'){
			if($s['mtypeid']!='' AND $s['inventoryid']!=''){
				$inventoryUrl = '&mtypeid='.$s['mtypeid'].'&inventoryid='.$s['inventoryid'];
			}else{
				$inventoryUrl = '';
			}
			$filterTh = "列表筛选 [<a href='/s.php?module={$s['module']}&action={$s['action']}{$inventoryUrl}'>有库存</a>] - [<a href='/s.php?module={$s['module']}&action={$s['action']}{$inventoryUrl}&stockzero=1'>无库存</a>]";
		}else{		
			$filterTh = "列表筛选 [<a href='/s.php?module={$s['module']}&action={$s['action']}{$inventoryUrl}'>全部</a>]";
		}
		foreach($s['serial'] as $k=>$v){
			$list='';
			if($i==1){
				$item.='<tr>';
			}
			$urlPart=str_replace('&'.$v['name'].'='.$this->input[$v['name']],'',$queryPart);
			foreach($v['status'] as $key => $val){
				if($val['value']==-100){
					if($this->input[$v['name']]!=''){
						$list.='<a href="/s.php?module='.$s['module'].'&action='.$s['action'].$inventoryUrl.$this->iif($s['url']!='',$s['url'],'').$urlPart.'">'.$val['title'].'</a>　';
					}else{
						$list.=$val['title'].'　';
					}
				}elseif($val['value']==$this->input[$v['name']] AND $this->input[$v['name']]!=''){
					$list.=$val['title'].'　';
				}else{
					$list.='<a href="/s.php?module='.$s['module'].'&action='.$s['action'].$inventoryUrl.'&'.$this->iif($s['url']!='',$s['url'].'&','').$v['name'].'='.$val['value'].$urlPart.'">'.$val['title'].'</a>　';
				}
			}
			$item.='<tr><td width="100">'.$v['dname'].'：</td><td>'.$list.'</td></tr>';

			if(isset($this->input[$v['name']]) AND $this->input[$v['name']]!==''){
				if($v['name']=='month' AND $this->input['month']>0){
					$year=date('Y',TIMENOW);
					$first=strtotime(date('Y-m-d', mktime(0,0,0,$this->input['month'],1,$year)));
					$last=strtotime(date('Y-m-t', mktime(0,0,0,$this->input['month'],1,$year)))+86400;
					$r['link'].=' AND `'.$s['table'].'`.created>'.$first.' AND `'.$s['table'].'`.created<='.$last;
				}else{
					$r['link'].=' AND `'.$s['table'].'`.'.$v['name'].'='.$this->input[$v['name']];
				}
			}else{
				$r['link'].='';
			}
			$i++;
		}
		$r['uri']=URI;
		$r['panel']=<<<EOF
<input type="hidden" name="module" value="{$s['module']}">
<input type="hidden" name="action" value="{$s['action']}">
<table class="hundred">
<thead><tr><th colspan=2>{$filterTh}</th></tr></thead>
<tbody>
{$item}
</tbody>
</table>
</form>
EOF;
		return $r;
	}
	// 将 每月某天 转换成下月的1号 $b['time']表示实际时间，$b['day']表示要转换的日期，$b['desc']表示是转换成当月还是下月
	function redefineMonth($b){
		$nowyear=date('Y',$b['time']);
		$nowmonth=date('n',$b['time']);
		$now=date('d',$b['time']);
		$orgstart=strtotime(date('Y-m-d', mktime(0,0,0,$nowmonth,1,$nowyear)));
		$orgend=strtotime(date('Y-m-t', mktime(0,0,0,$nowmonth,1,$nowyear)))+86400;
		if($b['desc']=='next'){
			// 获得月份
			if($now<$b['day']){
				$r['month']=$nowmonth;
				$r['year']=substr($nowyear,2,3);
				// 获得开始时间和结束时间
				$lastMonthDay=date("t",strtotime("last month"));
				$nowMonthDay=date("t",$b['time']);
				$r['start']=$orgstart-($lastMonthDay-$b['day']+1)*24*3600;
				$r['end']=$orgend-($nowMonthDay-$b['day'])*24*3600;
			}else{
				$r['month']=date("n",strtotime("next month"));
				$r['year']=substr($this->iif($nowyear==12,1,$nowyear),2,3);
				// 获得开始时间和结束时间
				$nowMonthDay=date("t",$b['time']);
				$nextMonthDay=date("t",strtotime("next month"));
				$r['start']=$orgstart+($b['day']-1)*24*3600;
				$r['end']=$orgend+$b['day']*24*3600;
			}
		}

		return $r;
	}
	//
	function id($i){
		$start=strtotime(date('Y-m-d',TIMENOW));
		$end=strtotime(date('Y-m-d',$start))+86400;
		if($i['customer']>0){
			$id = 'C'.sprintf("%04d", $i['customer']);
		}
		if($i['supplier']>0){
			$id = sprintf("%04d", $i['supplier']);
		}
		if($i['sample']>0){
			$counter=$this->DB->queryFirst("SELECT COUNT(*) AS count FROM `sample` WHERE noType=0 AND killed=0 AND created>='".$start."' AND created<='".$end."'");
			$id = 'SP'.date('Ymd',$i['sample']).str_pad($counter['count']+1,2,'0',STR_PAD_LEFT);
		}
		if($i['psample']>0){
			$id = 'SP'.date('Ymd',$i['psample']).str_pad($i['number']+1,2,'0',STR_PAD_LEFT);
		}
		if($i['maverickPurchase']>0){
			$id = 'MP'.date('Ymd',$i['maverickPurchase']).str_pad($i['number']+1,2,'0',STR_PAD_LEFT);
		}
		if($i['product']>0){
			$id = 'P'.$i['product'];
		}
		if($i['order']>0){
			$counter=$this->DB->queryFirst("SELECT COUNT(*) AS count FROM `order` WHERE noType=0 AND type='PI' AND  created>='".$start."' AND created<='".$end."'");
			$id = 'PI'.date('Ymd',$i['order']).str_pad($counter['count']+1,2,'0',STR_PAD_LEFT);
		}
		if($i['invoice']>0){
			$id = 'NW'.$i['invoice'];
		}
		if($i['porder']>0){
			$counter=$this->DB->queryFirst("SELECT COUNT(*) AS count FROM `porder` WHERE noType=0 AND created>='".$start."' AND created<='".$end."'");
			$id = 'PO'.date('ymd',$i['porder']).str_pad($counter['count']+1,2,'0',STR_PAD_LEFT);
		}
		if($i['pinvoice']>0){
			$id = 'NW'.date('ymd',$i['pinvoice']).str_pad($i['number']+1,2,'0',STR_PAD_LEFT);
		}
		if($i['inbound']>0){
			$year=date('Y',$i['inbound']);
			$month=date('n',$i['inbound']);
			$start=strtotime(date('Y-m-d', mktime(0,0,0,$month,1,$year)));
			$end=strtotime(date('Y-m-t', mktime(0,0,0,$month,1,$year)))+86400;
			$counter=$this->DB->queryFirst("SELECT COUNT(*) AS count FROM inbound WHERE departmentid='".$this->user['departmentid']."' AND noteTypeid=".$i['typeid']." AND realtime>='".$start."' AND realtime<='".$end."'");
			if(strlen($this->user['departmentno'])==2){//给没有下属部门的部门编号加为01
				$departmentno=$this->user['departmentno'].'01';
			}else{
				$departmentno=$this->user['departmentno'];
			}
			$id=$departmentno.date('ym',$i['inbound']).str_pad($counter['count']+1,4,'0',STR_PAD_LEFT);
		}
		if($i['outbound']>0){
			$year=date('Y',$i['outbound']);
			$month=date('n',$i['outbound']);
			$start=strtotime(date('Y-m-d', mktime(0,0,0,$month,1,$year)));
			$end=strtotime(date('Y-m-t', mktime(0,0,0,$month,1,$year)))+86400;
			$counter=$this->DB->queryFirst("SELECT COUNT(*) AS count FROM outbound WHERE departmentid='".$this->user['departmentid']."' AND noteTypeid=".$i['typeid']." AND realtime>='".$start."' AND realtime<='".$end."'");
			if(strlen($this->user['departmentno'])==2){//给没有下属部门的部门编号加为01
				$departmentno=$this->user['departmentno'].'01';
			}else{
				$departmentno=$this->user['departmentno'];
			}
			$id=$departmentno.date('ym',$i['outbound']).str_pad($counter['count']+1,4,'0',STR_PAD_LEFT);
		}
		if($i['entrust']>0){
			$id='OM'.date('ymd',$i['entrust']).str_pad($i['number']+1,2,'0',STR_PAD_LEFT);
		}
		if($i['batch']>0){
			$id=$i['materialno'].'-'.date('ymd',$i['batch']);
		}
		if($i['purchaseApply']>0){
			$id='PA'.date('ymd',$i['purchaseApply']).str_pad($i['number']+1,2,'0',STR_PAD_LEFT);
		}
		if($i['purchase']>0){
			$id='PM'.date('ymd',$i['purchase']).str_pad($i['number']+1,2,'0',STR_PAD_LEFT);
		}
		if($i['requirement']>0){
			$id='RM'.date('ymd',$i['requirement']).str_pad($i['number']+1,2,'0',STR_PAD_LEFT);
		}
		if($i['dispatch']>0){
			$id='DP'.date('ymd',$i['dispatch']).str_pad($i['number']+1,2,'0',STR_PAD_LEFT);
		}
		if($i['task']>0){
			$id='TK'.date('ymd',$i['task']).str_pad($i['number']+1,2,'0',STR_PAD_LEFT);
		}
		if($i['packinglist']>0){
			$id='PL'.date('ymd',$i['packinglist']).str_pad($i['number']+1,2,'0',STR_PAD_LEFT);
		}
		return $id;
	}
	/*
	*执行页面的分页操作
	*
	*
	*/
	function paginatin(){
	}

	/*
	 * 解析URL，将问号？后面的部分进行数组化,并且与参数所给的action进行对比，判断是否存在
	 * @author creator zhourui 2014-1-18 10:14:08
 	 * @access public
 	 * @param array $arr (url：需要解析的url,module：module值,action：数组值)
 	 * @return string $arr 
	 */
	function queryUrl($d){
		if(!$d['url']){
			return false;
		}
		$urls=parse_url($d['url']);
		$querys=explode('&',$urls['query']);
		foreach($querys AS $query){
			$front=strtok($query,'=');
			$behind=strtok('=');
			$arr[$front]=$behind;
		}
		if($arr['module']==$d['module'] AND in_array($arr['action'],$d['action'])){
			return true;
		}else{
			return false;
		}
	}
	/**
 	 * 更新缓存
 	 * @author creator zhourui 2014-1-16 9:59:00
 	 * @access public
 	 * @param array $arr (data：数组数据,filename：文件前缀名,time:缓存时间(以秒为单位))
 	 * @return string $arr 
 	 */
	function updateCache($d){
		if(!$d['filename']){
			return false;
		}
		if(!$d['time']){
			$d['time']=86400;
		}
		$file='private/cache/'.$d['filename'].'_'.$d['time'].'.txt'; // 缓存文件名为 文件前缀名+当前时间。
		$fp=fopen($file,"w");
		if(!$fp){
			return false;
		}
		//$data=serialize($d['data']);
		if(!flock($fp,LOCK_EX)){//LOCK_NB,排它型锁定    
      return false;   
     }
		fwrite($fp,$d['data']);
		@chmod($file,0777);
		flock($fp, LOCK_UN); //文件解锁
		fclose($fp);
		return true;
	}
	/**
 	 * 读取缓存
 	 * @author creator zhourui 2014-1-16 9:59:00
 	 * @access public
 	 * @param array $arr (filename：文件名)
 	 * @return string $arr 
 	 */
	function getCache($d){
		$dir='private/cache/';
		$files=scandir($dir);
		foreach($files AS $file){
			$filename=basename($file,'.txt');
			if(strstr($filename,$d['filename'])){
				$fp=fopen($dir.$file,'r');
				flock($fp,LOCK_EX); //文件加锁
				$data=fread($fp,filesize($dir.$file));
				flock($fp, LOCK_UN); //文件解锁
				fclose($fp);
				return $data;
			}
		}
	}
	//
	function verify($b){
		if($b==0){
			$r='待审核';
		}elseif($b==-1){
			$r='审核未通过';
		}elseif($b==1){
			$r='审核通过';
		}
		return $r;
	}
	//
	function approve($b){
		if($b==0){
			$r='待审批';
		}elseif($b==-1){
			$r='审批未通过';
		}elseif($b==1){
			$r='审批通过';
		}
		return $r;
	}
	function priceStatus($b){
		if($b==1){
			$r='<span class="green bold">√</span>';
		}elseif($b==2){
			$r='<span class="orange bold">!</span>';
		}elseif($b==3){
			$r='<span class="blue bold">×</span>';
		}
		return $r;
	}
		//
	function confirm($b){
		if($b==0){
			$r='待确认';
		}elseif($b==1){
			$r='已确认';
		}
		return $r;
	}
	//
	function getRealname($id){
		if($id<0){
			return false;
		}
		$user=$this->DB->queryFirst("SELECT realname FROM member WHERE killed=0 AND userid='".$id."'");
		return $user['realname'];
	}
	// 数字金额转换成中文大写金额
	function numberToCNAccount($b){
		$b=floatval(str_replace(',','',$b));
		$cnNumber=array('零','壹','贰','叁','肆','伍','陆','柒','捌','玖');
		$cnMiniUnits=array('圆','角','分');
		$cnUnits=array('拾','佰','仟','万','拾','佰','仟','亿');
		list($b1,$b2)=explode('.',$b,2);
		$b2=array_filter(array($b2[1],$b2[0]));
		$ret=array_merge($b2,array(implode('',$this->mapUnit(str_split($b1),$cnUnits)),''));
		$ret=implode('',array_reverse($this->mapUnit($ret,$cnMiniUnits)));
		$r=str_replace(array_keys($cnNumber),$cnNumber,$ret);
		return $r.'整';
	}
	//给中文金额数字加上单位
	function mapUnit($list,$units){
		$unitCount=count($units);
		$arr=array();
		foreach(array_reverse($list) as $value){
			$count=count($arr);
			$n=$this->iif($value!='0' OR !($count%4),$this->iif($value=='0','',$value).($units[($count-1)%$unitCount]),$this->iif(is_numeric($arr[0][0]),$value,''));
			array_unshift($arr,$n);
		}
		return $arr;
	}
	//
	function mmlist(){

	}
	/*
	*		the bom chlid list
	*		@param array(parentid,quantity,depth,materialitemid,versionid,loopNum)
	*		return array
	*/
	function bomChildList($b){
		$quantity=$this->iif($b['quantity'],$b['quantity'],1);
		$b['netQuantity']=$this->iif($b['netQuantity']>0,$b['netQuantity'],$quantity);
		$condition=$this->iif(intval($b['materialitemid'])>0,' AND parentitemid='.$b['materialitemid'],'');
		$materialid=$productItemid=$versionid=$rs=array();
		$boms=$this->DB->query("
			SELECT b.*,
				mv.title AS version,mv.qualified,mv.planInbound,mv.planOutbound,mv.price,mv.price1,
				pmv.price1 AS parentPrice1
			FROM `bom` AS b
			LEFT JOIN `materialversion` AS mv ON (mv.versionid=b.versionid)
			LEFT JOIN `materialversion` AS pmv ON (pmv.versionid=b.parentVersionid)
			WHERE b.killed=0 AND b.materialid='".$b['parentid']."' AND b.parentVersionid='".$b['versionid']."' $condition
			ORDER BY b.ordering ASC, b.bomid ASC
		");
		$count=$this->DB->numRows();
		if($count){
			if($b['getCount']==1){
				return 1;
			}
			$b['i']++;
			$b['depth'].= "　";
			while($bom=$this->DB->fetchArray($boms)){
				$unit=$this->DB->queryFirst("SELECT title FROM `unit` WHERE killed=0 AND unitid='".$bom['unitid']."'");//单位
				$allquantity=$bom['quantity']*$quantity;//
				$grossQuantity=$bom['quantity']*$b['netQuantity'];//毛需求数量
				$netQuantity=$grossQuantity-$bom['qualified']+$bom['min']+$bom['planOutbound']-$bom['planInbound'];// 净需求量

				$materialInfo=$this->getMaterial(array('materialid'=>$bom['childid'],'itemid'=>$bom['childItemid'],'ifGeneral'=>$b['ifGeneral']));
				//获得mrp物资列表.
				if($bom['ifGeneral']==0 AND $netQuantity>0){
					if(in_array($bom['versionid'],$versionid)){
						$rs[$bom['versionid']]['total']+=$netQuantity;
						$rs[$bom['versionid']]['addQuantity']+=$bom['qualified']-$bom['min'];
					}else{
						$rs[$bom['versionid']]['materialno']=$materialInfo['no'];
						$rs[$bom['versionid']]['title']=$materialInfo['material'];
						$rs[$bom['versionid']]['standard']=$materialInfo['standard'];
						$rs[$bom['versionid']]['total']=$netQuantity;
						$rs[$bom['versionid']]['materialid']=$bom['childid'];
						$rs[$bom['versionid']]['versionid']=$bom['versionid'];
						$rs[$bom['versionid']]['materialitemid']=$bom['childItemid'];
						$rs[$bom['versionid']]['dateline']=$b['dateline'];
						$versionid[]=$bom['versionid'];
					}
				}

					//获得bom列表
				$r[$bom['bomid']]=array(
					'rank'=>$b['i'],
					'materialid'=>$bom['childid'],
					'url'=>$materialInfo['url'],
					'materialno'=>$materialInfo['materialno'],
					'no'=>$materialInfo['no'],
					'title'=>$materialInfo['material'],
					'standard'=>$materialInfo['standard'],
					'qualified'=>$bom['qualified'],
					'min'=>$bom['min'],
					'materialitemid'=>$bom['childItemid'],
					'quantity'=>$allquantity,
					'perQuantity'=>$bom['quantity'],
					'unit'=>$materialInfo['unit'],
					'attritionRate'=>$bom['attritionRate'],
					'netQuantity'=>$netQuantity,
					'planInbound'=>$bom['planInbound'],
					'planOutbound'=>$bom['planOutbound'],
					'price'=>$bom['price'],
					'price1'=>$bom['parentPrice1'],
					'originid'=>$materialInfo['originid'],
					'origin'=>$materialInfo['origin'],
					'parentVersionid'=>$b['versionid'],
					'versionid'=>$bom['versionid'],
					'version'=>$bom['version'],
					'typeid'=>$bom['typeid'],  // 安装还是配送等
					'attrid'=>$materialInfo['attrid'],
					'unitid'=>$this->iif($bom['childUnitid']>0,$bom['childUnitid'],$materialInfo['unitid']),
					'ifGeneral'=>$materialInfo['ifGeneral'],
					'ifPrice'=>$materialInfo['ifPrice'],
						'bomnum'=>$count
				);

				if(($b['loopNum'] AND $b['i']<$b['loopNum']) OR !$b['loopNum']){// 判断循环次数是否跟要求的bom层数相同，相同则跳出递归
					// 获得子bom的数据
					$child=$this->DB->queryFirst("SELECT COUNT(*) AS count FROM `bom` WHERE killed=0 AND materialid='".$bom['childid']."' AND parentVersionid='".$bom['versionid']."' AND parentitemid='".$bom['childItemid']."' ORDER BY created ASC");
					if($child['count']>0){
						// 递归
						$d=$this->bomChildList(array('parentid'=>$bom['childid'],'depth'=>$b['depth'],'i'=>$b['i'],'loopNum'=>$b['loopNum'],'netQuantity'=>$netQuantity,'versionid'=>$bom['versionid'],'quantity'=>$allquantity,'materialitemid'=>$bom['childItemid'],'dateline'=>$b['dateline']));
						if(is_array($d)){
							$r=$r+$d['bom'];// 获得bom数据
							if(!empty($d['mrp'])){
								$rs=$rs+$d['mrp']; // 获得mrp要求的数据
							}
						}
					}
				}
			}
		}//
		if(!empty($r)){
			return array('count'=>$count,'bom'=>$r,'mrp'=>$rs);
		}else{
			return false;
		}
	}
	/*
	*		the bom parent list
	*		@param array(childid,quantity,depth,materialitemid,versionid,loopNum)
	*		return array
	*/
	function bomParentList($b){
		$quantity=$this->iif($b['quantity'],$b['quantity'],1);
		$condition=$this->iif($b['materialitemid']>0,' AND childItemid='.$b['materialitemid'],'');
		$boms=$this->DB->query("
			SELECT b.*,
				mv.title AS version,mv.qualified,mv.planInbound,mv.planOutbound,mv.price
			FROM `bom` AS b
			LEFT JOIN `materialversion` AS mv ON (mv.versionid=b.parentVersionid)
			WHERE b.killed=0 AND b.childid='".$b['childid']."' AND b.versionid='".$b['versionid']."' $condition
			ORDER BY b.ordering ASC,b.bomid ASC
		");
		if($this->DB->numRows()){
			$b['i']++;
			if($b['loopNum']){
				if($b['i']==$b['loopNum']+1)return false;// 判断循环次数是否跟要求的bom层数相同，相同则跳出递归
			}
			while($bom=$this->DB->fetchArray($boms)){
				$materialInfo=$this->getMaterial(array('materialid'=>$bom['materialid'],'itemid'=>$bom['parentitemid']));
				$reQuantity=$quantity*$bom['quantity'];
				$unit=$this->DB->queryFirst("SELECT title FROM `unit` WHERE killed=0 AND unitid='".$bom['unitid']."'");//单位
				//获得bom父信息
				$r[$bom['bomid']]=array(
					'bomid'=>$bom['bomid'],
					'url'=>$materialInfo['url'],
					'materialid'=>$bom['materialid'],
					'materialitemid'=>$bom['parentitemid'],
					'materialno'=>$materialInfo['materialno'],
					'no'=>$materialInfo['no'],
					'title'=>$materialInfo['material'],
					'standard'=>$materialInfo['standard'],
					'qualified'=>$bom['qualified'],
					'price'=>$bom['price'],
					'min'=>$bom['min'],
					'quantity'=>$reQuantity,
					'unit'=>$unit['title'],
					'attritionRate'=>$bom['attritionRate'],
					'versionid'=>$bom['parentVersionid'],
					'version'=>$bom['version']);

				$parent=$this->DB->queryFirst("SELECT COUNT(*) AS count FROM `bom` WHERE killed=0  AND childid='".$bom['mid']."' AND versionid='".$bom['parentVersionid']."' AND childItemid='".$bom['parentitemid']."' ORDER BY created ASC");
				if($parent['count']>0){
					$d=$this->bomParentList(array('childid'=>$bom['mid'],'quantity'=>$reQuantity,'versionid'=>$bom['parentVersionid'],'itemid'=>$bom['parentitemid'],'i'=>$b['i'],'loopNum'=>$b['loopNum']));
					if(is_array($d)){
						$r+=$d;
					}
				}
			}
		}
		return $r;
	}
	/*
	*		the mrp
	*		@param array(materialid,materialitemid,versionid,quantity)
	*		return array
	*/
	function mrpList($b){
		$t=array();
		//计划的物资
		foreach($b['versionid'] AS $versionid){
			if($b['quantity'][$versionid]>0){
				$r=$this->bomChildList(array('parentid'=>$b['materialid'][$versionid],'versionid'=>$versionid,'quantity'=>$b['quantity'][$versionid],'materialitemid'=>$b['materialitemid'][$versionid],'dateline'=>$b['dateline'][$versionid]));
				if(is_array($r['mrp'])){
					$t=array_merge($t,$r['mrp']);
				}
			}
		}

		if($t){
			$versionid=array();
			foreach($t as $val){
				$material=$this->DB->queryFirst("
					SELECT m.materialno,m.title AS material,m.min,m.originid,
						mv.title AS version,mv.qualified,mv.planInbound,mv.planOutbound
					FROM material AS m
					LEFT JOIN materialversion AS mv ON (mv.materialid=m.materialid)
					WHERE m.killed=0 AND m.materialid='".$val['materialid']."' AND mv.versionid='".$val['versionid']."'
					LIMIT 0,1
				");
				if(in_array($val['versionid'],$versionid)){
					$total[$val['versionid']]+=$val['total'];
					$addQuantity[$val['versionid']]+=$material['qualified']-$material['min']-$material['planInbound']+$material['planOutbound'];
				}else{
					$total[$val['versionid']]=$val['total'];
					$materialid[$val['versionid']]=$val['materialid'];
					$materialitemid[$val['versionid']]=$val['materialitemid'];
					$versionid[$val['versionid']]=$val['versionid'];
					$version[$val['versionid']]=$material['version'];
					$dateline[$val['versionid']]=$val['dateline'];
				}
			}
			$arr=array();
			foreach($versionid as $v){
				$netQuantity=$total[$v]+$addQuantity[$v];
				$arr[$v]=array('materialid'=>$materialid[$v],'versionid'=>$v,'version'=>$version[$v],'materialitemid'=>$materialitemid[$v],'netQuantity'=>$netQuantity,'dateline'=>$dateline[$v]);
			}
			return $arr;
		}else{
			return false;
		}
	}
 	/**
 	 * 返回物资信息
 	 * @author creator zhourui 2013-12-12 15:15:19
 	 * @access public
 	 * @param array $arr (materialid,itemid='')
 	 * @return string $arr 
 	 */
	function getMaterial($b){
		if($b['materialid']==''){
			return false;
		}
        $condition=$this->iif($b['ifGeneral']==0,' AND ifGeneral=0','');
		//echo $sql;
		$material=$this->DB->queryFirst("
			SELECT m.materialid,m.materialno,m.title AS material,m.entitle AS enmaterial,m.standard,m.enstandard,m.attrid,m.unitid,m.quantity AS totalQuantity,m.qualified AS totalQualified,m.disqualified AS totalDisqualified,m.originid,m.min,m.ifPrice,m.ifPerBarcode
			FROM material AS m
			LEFT JOIN (SELECT originid,title AS origin FROM `materialorigin`) AS mo ON (mo.originid=m.originid)
			LEFT JOIN (SELECT unitid,title AS unit FROM `unit`) AS u ON (u.unitid=m.unitid)
			WHERE m.killed=0 AND m.materialid=".$b['materialid']." ".$b['condition']."
			LIMIT 0,1
		");
		if(($material['attrid']==7 OR $material['attrid']==8) AND !$b['itemid']){
			return false;
		}
		switch($material['attrid']){
			case 6:
				$sql="SELECT pmaterialid,ifGeneral FROM pmaterial WHERE killed=0 AND materialid=".$b['materialid']." ".$condition." LIMIT 0,1";
				$url='/s.php?module=material&action=view&materialid='.$b['materialid'];
			break;
			case 7:
				$sql="SELECT itemid,itemno,relatedid,attributevalue,quantity AS itemQuantity,qualified AS itemQualified,disqualified AS itemDisqualified FROM materialitem WHERE killed=0 AND itemid=".$b['itemid']." LIMIT 0,1";
				$url='/s.php?module=material&action=view&materialid='.$b['materialid'].'&itemid='.$b['itemid'];
			break;
			case 8:
				$sql="SELECT itemid,itemno,relatedid,attributevalue,quantity AS itemQuantity,qualified AS itemQualified,disqualified AS itemDisqualified FROM materialitem WHERE killed=0 AND itemid=".$b['itemid']." LIMIT 0,1";
				$url='/s.php?module=material&action=view&materialid='.$b['materialid'].'&itemid='.$b['itemid'];
			break;
		}
		if($sql){
			$m=$this->DB->queryFirst($sql);
			if($m){
				$material=array_merge($material,$m);
			}

			if($material['attributevalue']){
				$attributes=$this->DB->query("
					SELECT av.valueid,av.title AS value,
						an.nameid,an.title AS name
					FROM attributevalue AS av
					LEFT JOIN attributename AS an ON (an.nameid=av.nameid)
					WHERE an.killed=0 AND av.killed=0 AND av.valueid IN (".$m['attributevalue'].")
					ORDER BY an.ordering DESC
				");
				if($this->DB->numRows()){
					while($attribute=$this->DB->fetchArray($attributes)){
						$value.=$attribute['value'].'　';
					}
				}
				$material['standard']=$value;
			}
			$material['cnGeneral']=$this->iif($material['ifGeneral']==1,'是','否');
			$material['url']=$url;
		}
		$material['no']=$this->iif($material['itemno']!='',$material['itemno'],$material['materialno']);
		return $material;
	}
		/**
 	 * 返回产品信息
 	 * @author creator zhourui 2013-12-12 15:15:19
 	 * @access public
 	 * @param array $arr (productid,itemid='')
 	 * @return string $arr 
 	 */
	function getProduct($b){
		if($b['productid']==''){
			return false;
		}

		$product=$this->DB->queryFirst("
			SELECT `product`.productid,`product`.productno,`product`.title,`product`.entitle,`product`.materialid,`product`.abbr,`product`.parentProduct,`product`.specifications,`product`.technical,`product`.features,`product`.description,`product`.details,`product`.remark,`product`.display,`product`.modified,`product`.created,
				category.title AS category,
				brand.title AS brand,
				unit.title AS unit,
				packing.title AS packing,
				attr.title AS attr,
				m.realname AS modifier,mem.realname AS creator
			FROM `product`
			LEFT JOIN `category` ON (category.categoryid=product.categoryid)
			LEFT JOIN `brand` ON (brand.brandid=product.brandid)
			LEFT JOIN `unit` ON (unit.unitid=product.unitid)
			LEFT JOIN `packing` ON (packing.packingid=product.packingid)
			LEFT JOIN `attr` ON (attr.attrid=product.attrid)
			LEFT JOIN `member` AS m ON (m.userid=product.modifier)
			LEFT JOIN `member` AS mem ON (mem.userid=product.creator)
			WHERE productid='".$b['productid']."'
		");

		if($b['itemid']>0){
			$selectStr='';
			$productitem=$this->DB->queryFirst("
				SELECT productitemid,itemno,attributevalue,ordering
				FROM `productitem` 
				WHERE killed=0 AND `productid`='".$product['productid']."' AND productitemid='".$b['itemid']."'
				ORDER BY ordering DESC
			");
			if($productitem){
				$attributes=$this->DB->query("
					SELECT av.valueid,av.title AS value, av.entitle AS envalue,
						an.nameid,an.title AS name,an.entitle AS enname
					FROM attributevalue AS av
					LEFT JOIN attributename AS an ON (an.nameid=av.nameid)
					WHERE an.killed=0 AND av.killed=0 AND av.valueid IN (".$productitem['attributevalue'].")
					ORDER BY an.ordering DESC
				");
				if($this->DB->numRows()){
					while($attribute=$this->DB->fetchArray($attributes)){
						if($b['module']=='quoting'){
						$selectStr .= '　'.$attribute['enname'].'：'.'<span class="darkred">'.$attribute['envalue']."</span>";
						}else{
                         $selectStr .= '　'.$attribute['name'].'：'.'<span class="darkred">'.$attribute['value']."</span>";
						}
					}
				}
				$product['standard']=$selectStr;
			}
		}
		return $product;
	}
 	/**
 	 * 根据查询条件返回货运信息
 	 * @author creator mayinghao 2013-07-10 9:15
 	 * @access public
 	 * @param array $arr 查询结果数组
 	 * @return string $str 返回货运表格信息
 	 * @throws none 没有异常
 	 */
 	function getFreightInfo(array $arr){
 		$str = '';
 		if($arr){
 			$freight = $arr;
$str = <<<EOF
<table class="hundred">
<thead>
<tr>
	<th colspan="6">货运信息</th>
</tr>
</thead>
<tbody>
<tr class="odd">
	<td width="8%">快递单号：</td>
	<td width="42%">{$freight['expressNo']}</td>
	<td width="8%">重　　　　量：</td>
	<td >{$freight['weight']}　　　　　　　　箱　数：{$freight['carton']}</td>
</tr>
<tr class="even">
	<td>质　检 OQC：</td>
	<td>{$freight['oqc']}</td>
 	<td>发&nbsp;&nbsp;货&nbsp;&nbsp;&nbsp;确&nbsp;&nbsp;认：</td>
 	<td>{$freight['shippingVerify']}</td>

</tr>
<tr class="odd">
 	<td>提货司机姓名：</td>
	<td>{$freight['driverName']}</td>
	<td>司机电话号码：</td>
	<td>{$freight['driverMobile']}</td>

</tr>
<tr class="even">
 	<td>车　牌　号：</td>
	<td>{$freight['carID']}</td>
	<td>司机身份证号：</td>
	<td>{$freight['driverID']}</td>
</tr>
<tr class="odd">
	<td valign="top">司&nbsp;&nbsp;机&nbsp;&nbsp;&nbsp;确&nbsp;&nbsp;认：</td>
	<td>{$freight['driverVerify']}</td>
	<td valign="top">货&nbsp;&nbsp;运&nbsp;&nbsp;&nbsp;公&nbsp;&nbsp;司：</td>
	<td>{$freight['freightCompany']}</td>
</tr>
</tbody>
</table>
EOF;
 		}
 		return $str;
 	}

 	/**
 	 * 插入修订版本信息
 	 * @author creator mayinghao 2013-07-16 9:15
 	 * @access public
 	 * @param array $b 数组参数
 	 * @return boolean 返回成功或失败
 	 * @throws none 没有异常
 	 */
 	function insertReviseInfo($b){
 		if(isset($b['module']) && isset($b['mid']) && isset($b['revise'])){
 			$num = $this->DB->queryFirst("
 				SELECT count(reviseid) AS num
 				FROM `revise`
 				WHERE module='".$b['module']."' AND mid='".$b['mid']."'
 			");
 			$num['num']=$num['num']+1;
 			$this->DB->query("
	 			INSERT INTO `revise` (module,mid,ptext,pvalue,version,remark,creator,created)
	 			VALUES ('".$b['module']."','".$b['mid']."','".$b['revise']['html']."','".$b['revise']['arr']."','".$num['num']."','".$b['remark']."','".$this->user['userid']."','".TIMENOW."')
 			");
 			return true;
 		}else{
 			return false;
 		}
 	}

 	/**
 	 * 返回修订版本信息（当前版本和历史版本）
 	 * @author creator mayinghao 2013-07-16 15:15
 	 * @access public
 	 * @param array $b 数组参数
 	 * @return array $str 返回修订版本信息
 	 * @throws none 没有异常
 	 */
 	function relatedRevise($b){
 		$str = array();
 		if(isset($b['module']) && isset($b['mid'])){
 			$revises = $this->DB->query("
	 			SELECT `revise`.reviseid,`revise`.ptext,`revise`.version,`revise`.remark,`revise`.created,
					`member`.realname
	 			FROM `revise`
				LEFT JOIN `member` ON (`member`.userid=`revise`.creator)
	 			WHERE `revise`.killed=0 AND `revise`.module='".$b['module']."' AND `revise`.mid='".$b['mid']."'
	 			ORDER BY `revise`.reviseid DESC
 			");
 			$i=0;
			$rtn['num']=$this->DB->numRows();
 			if($rtn['num']){
				$rtn['panel'].='<table class="hundred"><thead><tr><th colspan=5>历史版本</th></tr></thead><tbody><tr class="odd"><td width="15">ID</td><td width="120">版本号</td><td width="400">修改时间</td><td width="200">修订人</td><td>修改原因</td></tr>';
 				/*$rtn['now'] = '<span class="gray small">当前版本：</span>';
 				$str['old'] = '<span class="gray small">历史版本：</span>';*/
 				while($revise=$this->DB->fetchArray($revises)){
					$i++;
					$created=date('Y-m-d H:s:i',$revise['created']);
					$rtn['panel'].='<tr class="'.$this->iif($this->rotate(),'odd','even').'">
						<td>'.$i.'</td>
						<td><a href="/s.php?module='.$b['module'].'&action=revise&reviseid='.$revise['reviseid'].'" target="_blank">R'.$revise['version'].'</a></td>
						<td>'.$created.'</td>
						<td>'.$revise['realname'].'</td>
						<td>'.$revise['remark'].'</td>
					</tr>';
 				}
				$rtn['panel'].='</tbody></table>';
				$rtn['count']='('.$rtn['num'].')';
				$rtn['currencyRevise']='<span class="green normal"> ( R'.($rtn['num']+1).' )</span>';
 			}
			//返回最新的reviseid
			$r=$this->DB->queryFirst("
				SELECT `revise`.reviseid
	 			FROM `revise`
	 			WHERE `revise`.killed=0 AND `revise`.module='".$b['module']."' AND `revise`.mid='".$b['mid']."'
	 			ORDER BY `revise`.reviseid DESC
				LIMIT 0,1
			");
			if(!$r)$r['reviseid']=0;
			$rtn['reviseid']=$r['reviseid'];
 		}else{
 			$rtn['off']=' disabled';
 		}
 		return $rtn;
 	}
//
	/**
 	 * 返回流程列表
 	 * @author creator gaowenfei 2013/11/19 14:45
 	 * @access public
 	 * @param array $b 数组参数
 	 * @return array $str 返回流程列表
 	 * @throws none 没有异常
 	 */
	function listFlow($b){
		if($b['module']!='' AND $b['statusid']!=''){
			$flows=$this->DB->query("
				SELECT *
				FROM flow
				WHERE killed=0 AND module='".$b['module']."'
				ORDER BY ordering ASC
			");
			if($n=$this->DB->numRows()){
				while($flow=$this->DB->fetchArray($flows)){
					if($b['statusid']==$flow['statusid']){
						$rtn.='<li class="active"><span class="label">'.$flow['title'].'</span><span class="arrow" style="red"><span></span></span></li>';
					}else{
						$rtn.='<li><span class="label">'.$flow['title'].'</span><span class="arrow"><span></span></span></li>';
					}
				}
				$rtn='<ul class="form_status">'.$rtn.'</ul>';
			}
			return $rtn;
		}else{
			return false;
		}
	}
//
	/**
 	 * 记录操作日志
 	 * @author creator gaowenfei 2013/11/22 9:24
 	 * @access public
 	 * @param array $b 数组参数
 	 * @return true
 	 * @throws none 没有异常
 	 */
	function actionLog($b){
		if($b['creator']=='')$b['creator']=$this->user['userid'];
		if($b['created']=='')$b['created']=TIMENOW;
		$this->DB->query("
			INSERT INTO `actionlog`
			(`module`, `mid`, `note`, `remark`, `creator`, `created`)
			VALUES
			('".$b['module']."','".$b['mid']."','".$b['note']."','".$b['remark']."','".$b['creator']."','".$b['created']."')
		");
	}
	/**
 	 * 显示操作日志
 	 * @author creator gaowenfei 2013/11/22 9:24
 	 * @access public
 	 * @param array $b 数组参数
 	 * @return true
 	 * @throws none 没有异常
 	 */
	function releatedActionLog($b){
		if(!$b['panel'])$b['panel']=1;
		$logs=$this->DB->query("
			SELECT a.* ,
				u.username AS creator
			FROM `actionlog` AS a
			LEFT JOIN user AS u ON (u.userid=a.creator)
			WHERE a.killed=0 AND a.module='".$b['module']."' AND a.mid='".$b['mid']."'
		");
		$rtn['num']=$this->DB->numRows();
		if($rtn['num'] AND $b['panel']==1){
			$i=1;
			while($log=$this->DB->fetchArray($logs)){
				$itemtr.='<tr class="'.$this->rotateLine(). ' small">
					<td>'.$i.'</td>
					<td>'.date('Y-m-d H:i:s',$log['created']).'</td>
					<td><span class="bold">'.$log['note'].'</span></td>
					<td>'.$log['creator'].'</td>
				</tr>';
				$i++;
			}
			$rtn['panel']=<<<EOF
<table class="hundred">
<thead>
<tr><th width="35">ID</th><th width="120">处理时间</th><th width="400">处理信息</th><th width="100">操作人</th></tr>
</thead>
<tbody>
{$itemtr}
</tbody>
</table>
EOF;
		}
		$rtn['count'] = ' ('.$rtn['num'].')';
		if(!$rtn['num']){
			$rtn['off']='disabled';
		}
		return $rtn;
	}

//
	function listMaterial($b){
		if($b['start']=='')$b['start']=0;
		if($b['perpage']=='')$b['perpage']=500;
		if($b['condition']==''){
			$b['condition']='`material`.killed=0';
		}
		if($b['orderby']!='')$query['orderby']=$b['orderby'];
		if($b['direction']!='')$query['direction']=$b['direction'];
		if($b['show']!='')$query['show']=$b['show'];
		$ob=$this->orderby(array('module'=>'material','direction'=>'asc','orderby'=>'modified', 'default'=>'materialno', 'start'=>$b['start'],'perpage'=>$b['perpage'],'serial'=>array(array('title'=>'名称', 'field'=>'title','word'=>'name'), array('title'=>'编号', 'field'=>'materialno','word'=>'no'), array('title'=>'修改时间', 'field'=>'modified'), array('title'=>'建立时间', 'field'=>'created')),'appendUrl'=>$query));
		if(is_array($query) AND count($query)>0)$queryPart='&'.http_build_query($query);
		$materials=$this->DB->query("
			SELECT * FROM `material` AS material
			LEFT JOIN (SELECT unitid,title AS unit FROM `unit`) AS u ON (u.unitid=`material`.unitid)
			LEFT JOIN (SELECT categoryid,parentlist FROM `category`) AS c ON (c.categoryid=`material`.categoryid)
			LEFT JOIN (SELECT originid,title AS origin FROM `materialorigin`) AS mo ON (mo.originid=`material`.originid)
			LEFT JOIN (SELECT userid,realname AS creator FROM `member`) AS me ON (me.userid=`material`.creator)
			LEFT JOIN (SELECT userid,realname AS modifier FROM `member`) AS mem ON (mem.userid=`material`.modifier)
			{$b['tableLink']}
			WHERE `material`.attrid=6 AND ".$b['condition']." 
			ORDER BY ".$ob['sql']."
			LIMIT ".$b['start'].",".$b['perpage']."
		");

		if($counter=$this->DB->numRows()){
			$i=$b['start']+1;
			while($material=$this->DB->fetchArray($materials)){
				if($material['attrid']==6){
					$pmaterial=$this->DB->queryfirst("
						SELECT pmaterialid,ifGeneral
						FROM pmaterial 
						WHERE materialid='".$material['materialid']."'
						LIMIT 0,1
					");
					if(is_array($pmaterial)){
						$material=array_merge($material,$pmaterial);
					}
				}
				$alarm='';
				$total=$material['quantity'];
				$categoryid=explode(',',$material['parentlist']);
				foreach($categoryid as $key=>$value){
					if($key!=0){
						$category=$this->DB->queryFirst("SELECT title FROM category WHERE categoryid='".$value."' LIMIT 0,1");
						if($category['title'])
							$material['category']=$category['title'].' '.$material['category'];
					}
				}
				if($total<$material['criticalNumber'] AND $total>=$material['min']){
					$alarm='bgYellow';
				}elseif($total<$material['min']){
					$alarm='bgRed';
				}
				$material['created']=date('Y-m-d',$material['created']);
				if($material['killed']>0){
					$link='<a href="/s.php?module=material&action=restore&materialid='.$material['materialid'].'&rt=list">恢复</a>';
				}else{
					$link='<a href="/s.php?module=material&action=update&materialid='.$material['materialid'].'&rt=list">改</a>　<a href="/s.php?module=material&action=remove&materialid='.$material['materialid'].'&rt=list" onclick="return confirm(\'你确定要删除这个物资 '.$material['materialno'].' 吗？\');">删</a>';
				}
				$version=$this->DB->queryFirst("SELECT price FROM materialversion WHERE materialid={$material['materialid']}");
				if($b['layout']=='grid'){//表格
					$materialList.='<li title="由 '.$material['creator'].' 建于 '.$material['created'].$this->iif($material['modifier']!='', '，'.$material['modifier'].' 改于 '.date('Y-m-d',$material['modified']), '').'"'.$this->iif($i%4==0, ' class="end"', '').'>
					<div class="mmlistt">
						<span class="right normal">';
					if($material['killed']>0){
						$materialList.='<a href="/s.php?module=material&action=revival&materialid='.$material['materialid'].'&rt=list" onclick="return confirm(\'你确定要恢复这个物资 '.$material['materialno'].' 吗？\');">恢复</a>';
					}else{
						$materialList.='<a href="/s.php?module=material&action=kill&materialid='.$material['materialid'].'&rt=list" onclick="return confirm(\'你确定要删除这个物资 '.$material['materialno'].' 吗？\');">删</a> <a href="/s.php?module=material&action=update&materialid='.$material['materialid'].'&rt=list">改</a>';
					}
					$materialList.='</span>
					 <a href="/s.php?module=material&action=view&materialid='.$material['materialid'].'">'.$material['materialno'].'</a><br><span class="small gray right">'. $material['created'].'</span>'.$this->iif($material['attachs']>0, ' <span class="attachFile" title="有'.$material['attachs'].'个附件。"></span> ', '').$this->iif($material['images']>0, ' <span class="attachImage" title="有'.$material['images'].'个图片。"></span> ', '').$material['title'].'</div>
				<div class="'.$alarm.' mmlistb">
					<div title="'.$material['standard'].'"><span class="small gray">规格：'.$material['standard'].'</span></div>
					<div title="'.$material['category'].'"><span class="small gray">分类：'.$material['category'].'</span></div>
					<div title="'.$material['quantity'].'"><span class="small gray">总数量：'.$material['quantity'].'</span></div>
					<div title="良品数和不良品数"><span class="small gray">良品数：'.$material['qualified'].'</span>　<span class="small gray">不良品数：'.$material['disqualified'].'</span></div>
					<div title="最小值、最大值、报警值"><span class="small gray">最小值：'.$material['min'].'</span>　<span class="small gray">最大值：'.$material['max'].'</span>　<span class="small gray">报警值：'.$material['criticalNumber'].'</span></div>
					<div title="'.$total.'"><span class="small gray">总库存数：'.$total.'</span></div>
					<div class="small clear"><span class=right title="由 '.$material['creator'].' 建于 '.$material['created'].'">由 '.$material['creator'].' 建于 '.$material['created'].'</span></div></div></li>';
				}else{
					if($version['price']==0){
						$class='black';
					}else{
						$class='red';
					}
					if($b['symbol']==1){
						$mtype = 'qualified';
					}else{
						$mtype = 'disqualified';
					}
					if($b['module']=='inventory'){//物资盘点列表
						if($material['materialType']==$b['symbol']){
							$firstInventory = $this->iif($material['firstInventory']!='',$material['firstInventory'],'+');
							$checkInventory = $this->iif($material['checkInventory']!='',$material['checkInventory'],'');
							$final = $this->iif($material['checkInventory']!='',$material['checkInventory'],$material['firstInventory']);
							$difference = $this->iif($material['difference']!='',$material['difference'],'');
							$remark = $material['invremark'];
						}else{
							$firstInventory = '+';
							$checkInventory = '';
							$final = '';
							$difference = '';
							$remark = '';
						}						
						$firstClass = $this->iif($material['firstInventory']!='','','first');
						if($material['materialType']==$b['symbol'] and $material['checkInventory']=='' and $material['firstInventory']!=''){
							$checkInventory = '+';
							$checkClass = 'check';
						}else{
							$checkClass = '';
						}
						$materialList.='<tr class="'.$this->rotateLine().'">
							<td>'.$i.'</td>
							<td class="mid"><a href="/s.php?module=material&action=view&materialid='.$material['materialid'].'">'.$material['materialno'].'</a></td>
							<td>'.$material['title'].'</td>
							<td>'.$material['origin'].'</td>
							<td title="'.$material['standard'].'"><span class="small">'.$material['standard'].'</span></td>
							<td class="bold '.$class.'">'.$this->iif($this->purviews(array('module'=>'material','action'=>'viewPrice','final'=>1)),$version['price'],'—').'</span></td>
							<td>'.$material['unit'].'</td>
							<td class="bold normal">'.$material[''.$mtype.''].'</td>
							<td class="'.$firstClass.'" align="center">'.$firstInventory.'</td>
							<td class="'.$checkClass.'" align="center">'.$checkInventory.'</td>
							<td class="final">'.$final.'</td>
							<td class="diff">'.$difference.'</td>
							<td align="center"><input type="text" name="remark" class="remark" value="'.$remark.'" /></td>
						</tr>';										
					}else{//物资列表
						$materialList.='<tr class="'.$this->rotateLine().'" id="stopMa'.$i.'">
							<td>'.$i.'</td>
							<td><a href="/s.php?module=material&action=view&materialid='.$material['materialid'].'">'.$material['materialno'].'</a></td>
							<td><span class="small" title="'.$material['title'].'">'.$material['title'].'</span> '.$this->iif($material['attachs']>0, ' <span class="attachFile" title="有'.$material['attachs'].'个附件。"></span> ', '').$this->iif($material['images']>0, ' <span class="attachImage" title="有'.$material['images'].'个图片。"></span> ', '').'</td>
							<td title="'.$material['standard'].'"><span class="small">'.$material['standard'].'</span></td>
							<td class="bold '.$class.'">'.$this->iif($this->purviews(array('module'=>'material','action'=>'viewPrice','final'=>1)),$version['price'],'—').'</span></td>
							<td>'.$this->iif($material['ifGeneral']==1,'是','否').'</td>
							<td class="bold normal">'.$total.'</td>
							<td>'.$material['qualified'].'</td>
							<td>'.$material['disqualified'].'</td>
							<td>'.$material['min'].'</td>
							<td>'.$material['origin'].'</td>
							<td>'.$material['unit'].'</td>
							<td align="center">'.$link.'</td>
							<td><input type="checkbox" name="stop" onchange="stopMaterial('.$material['materialid'].','.$i.')"></td>
						</tr>';
					}
				}
				$i++;
			}
		}else{
			$materialList='<tr><td colspan=13>暂无相关记录</td></tr>';
		}
		if($b['layout']=='grid'){
				$materialList .= '</ul>';
			}else{
				$materialList.='</tbody></table>';
			}
		return array('body'=>$materialList,'counter'=>$counter);
	}
	/**
	 * Bom 的list  分页写法
	 * @author creator zhourui 2013-09-27 12:00
	 * @access public
	 * @param array $b 参数传递 ：start 、 perpage
	 * @return string $str 返回bom的list
	 * @throws none 没有异常
	 */
	function listBom($b){
		if(empty($b['start'])){
			$b['start']=0;
		}
		if(empty($b['perpage'])){
			$b['perpage']=50;
		}
		if($b['condition']){
			$condition=$b['condition'];
		}
		$parents=$this->DB->query("
			SELECT DISTINCT `bom`.parentVersionid,`bom`.materialid,`bom`.parentitemid,mv.title AS version,mv.`change`,mv.price
			FROM `bom` 
			LEFT JOIN materialversion AS mv ON (mv.versionid=`bom`.parentVersionid) 
			LEFT JOIN material ON (material.materialid=`bom`.materialid)
			LEFT JOIN materialitem ON (materialitem.itemid=`bom`.parentitemid)
			WHERE `bom`.killed=0 ".$b['filter'].$condition."
			ORDER BY material.materialno ASC,materialitem.itemno ASC,`bom`.ordering ASC, `bom`.bomid ASC 
			LIMIT ".$b['start'].",".$b['perpage']
		);
		if($this->DB->numRows()){
			$m=$b['start']+1;
			while($parent=$this->DB->fetchArray($parents)){
				$childs=$this->DB->query("
					SELECT b.bomid,b.quantity,b.childid,b.childItemid,b.versionid,b.childUnitid,b.attritionRate,b.ordering,b.typeid,b.remark,
						mv.title AS version,mv.qualified,mv.price
					FROM `bom` AS b
					LEFT JOIN materialversion AS mv ON (mv.versionid=b.versionid)
					LEFT JOIN materialitem ON (materialitem.itemid=b.childItemid)
					LEFT JOIN material ON (material.materialid=b.childid)
					WHERE b.killed=0 AND b.materialid='".$parent['materialid']."' AND parentVersionid='".$parent['parentVersionid']."' 
					ORDER BY material.materialno ASC
				");
				if($this->DB->numRows()){
					$i=1;
					$childItemtr=$parentPrice='';
					while($child=$this->DB->fetchArray($childs)){
						$materialInfo=$this->getMaterial(array('materialid'=>$child['childid'],'itemid'=>$child['childItemid']));
						$addBom='';
						if($materialInfo['originid']==1){
							$nextChild=$this->DB->queryFirst("SELECT COUNT(*) AS count FROM bom WHERE killed=0 AND materialid='".$child['childid']."' AND parentitemid='".$child['childItemid']."' AND parentVersionid='".$child['versionid']."'");
							if($nextChild['count']==0){
								$addBom='　<a href="/s.php?module=bom&action=add&attrid='.$materialInfo['attrid'].'&versionid='.$child['versionid'].'">添加物料清单</a>';
							}
						}
						//$type=$this->iif($child['typeid']==0,'安装','配送');
						$unit=$this->DB->queryFirst("SELECT title FROM unit WHERE unitid=".$this->iif($child['childUnitid']>0,$child['childUnitid'],$materialInfo['unitid'])." LIMIT 0,1");
						if($child['price']==0){
							$class='black';
							$parentPrice='无法计算';
						}else{
							$class='red';
						}
						$childItemtr.='<tr class="'.$this->rotateLine().' small">
							<td align="right">'.$i.'</td>
							<td><a href="'.$materialInfo['url'].'">'.$materialInfo['no'].'</a>　'.$materialInfo['material'].'　'.$materialInfo['standard'].'</td>
							<td align="center">'.$child['version'].'</td>
							<td class="bold '.$class.'">'.$this->iif($this->purviews(array('module'=>'material','action'=>'viewPrice','final'=>1)),$child['price'],'—').'</td>
							<td>'.$child['qualified'].'</td>
							<td align="center">'.$child['quantity'].$unit['title'].'</td>
							<td>'.$child['attritionRate'].'‰</td>
							<td>'.$materialInfo['origin'].'</td>
							<td>'.$materialInfo['cnGeneral'].'</td>
							<td>'.$child['remark'].'</td>
							<td><a href="/s.php?module=bom&action=updateMany&bomid='.$child['bomid'].'" class="small">修改（多张bom）</a>'.$addBom.'</td></tr>';
						$i++;
					}
				}
				$unique=$this->DB->queryFirst("
					SELECT b.created,b.modified,m.realname AS creator,mem.realname AS modifier
					FROM `bom` AS b
					LEFT JOIN member AS m ON (m.userid=b.creator)
					LEFT JOIN member AS mem ON (mem.userid=b.modifier)
					WHERE b.parentVersionid={$parent['parentVersionid']}
				");
				$parentInfo=$this->getMaterial(array('materialid'=>$parent['materialid'],'itemid'=>$parent['parentitemid']));
				$rtn['tr'].='<tr class="'.$this->rotateLine().' small">
					<td class="middle bold"><a name="'.$parent['parentVersionid'].'">['.$m.']</a></td>
					<td class="bold middle"><a href="'.$parentInfo['url'].'">'.$parentInfo['no'].'</a>　'.$parentInfo['material'].'　'.$parentInfo['standard'].'</td>
					<td><a href="#'.$parent['parentVersionid'].'" class="tips" title="'.$parent['change'].'">'.$parent['version'].'</a></td>
					<td class="bold red">'.$this->iif($this->purviews(array('module'=>'material','action'=>'viewPrice','final'=>1)),$this->iif($parentPrice,$parentPrice,$parent['price']),'—').'</td>
					<td></td><td align="center">1</td><td></td>
					<td>'.$parentInfo['origin'].'</td>
					<td>'.$parentInfo['cnGeneral'].'</td>
					<td>由'.$unique['creator'].'于'.date('Y-m-d H:i:s',$unique['created']).'建立，'.$this->iif($unique['modified']>0,'由'.$unique['modifier'].'于'.date('Y-m-d H:i:s',$unique['modified']).'修改','').'</td>
					<td><a href="/s.php?module=bom&action=showAll&versionid='.$parent['parentVersionid'].'" target="__blank">所有BOM</a> <a href="/s.php?module=bom&action=copy&materialid='.$parent['materialid'].'&parentVersionid='.$parent['parentVersionid'].'">子项复制</a> <a href="/s.php?module=bom&action=remove&materialid='.$parent['materialid'].'&parentVersionid='.$parent['parentVersionid'].'" class="small">删除</a> <a href="/s.php?module=bom&action=update&materialid='.$parent['materialid'].'&parentVersionid='.$parent['parentVersionid'].'">编辑</td></tr>'.$childItemtr;
				$m++;
			}
		}

		return $rtn;
	}
	/**
	 * inbound 的list  分页写法
	 * @author creator zhourui 2013/11/16 15:56
	 * @access public
	 * @param array $b 参数传递 ：start,perpage,condition,orderby,inboundType
	 * @return string $str 返回inbound的list
	 * @throws none 没有异常
	 */
	function listInbound($b){
		if($b['start']=='')$b['start']=0;
		if($b['perpage']=='')$b['perpage']=20;
		if($b['condition']=='')$b['condition']='`inbound`.killed=0';
		if($b['orderby']=='')$b['orderby']=' `inbound`.inboundid DESC';
		$count=$this->DB->queryFirst("SELECT COUNT(inboundid) AS counter FROM inbound WHERE ".$b['condition'].$b['filter']);
		$r['counter']=$count['counter'];
		$inbounds=$this->DB->query("
			SELECT `inbound`.inboundid,`inbound`.inboundno,`inbound`.porderid,`inbound`.orderid,`inbound`.sampleid,`inbound`.supplierid,`inbound`.orderno,
					`inbound`.supplier,`inbound`.applicant,`inbound`.arrivalno,`inbound`.typeid,`inbound`.purpose,
					`inbound`.remark,`inbound`.ifConfirm,`inbound`.ifComplete,`inbound`.ifVerify,`inbound`.killed,`inbound`.created,
				d.title AS department,
				t.title AS type,
				wc.title AS workcenter,
				u.username AS creator,
				us.username AS modifier
			FROM `inbound`
			LEFT JOIN `inorouttype` AS t ON (t.typeid=`inbound`.typeid)
			LEFT JOIN `department` AS d ON (d.departmentid=`inbound`.departmentid)
			LEFT JOIN `workcenter` AS wc ON (wc.workcenterid=`inbound`.workcenterid)
			LEFT JOIN `user` AS u ON (u.userid=`inbound`.creator)
			LEFT JOIN `user` AS us ON (us.userid=`inbound`.modifier)
			WHERE ".$b['condition'].$b['filter']."
			ORDER BY ".$b['orderby']."
			LIMIT ".$b['start'].",".$b['perpage']."
		");
		if($this->DB->numRows()){
			if($this->input['layout']=='grid'){
				$r['body']='<ul id="mmlist" class="mmlist clear">';
			}else{
				$r['body']='<table class="hundred mytable tablesorter"><thead><th width="15">ID</th><th width="70">入库状态</th><th width="30">入库单编号</th><th width="70">入库类型</th><th width="100">申请人</th><th width="100">申请部门</th><th width="70">申请时间</th><th width="70">审核状态</th><th width="70"></th></thead><tbody>';
			}
			$i=1;
			while($inbound=$this->DB->fetchArray($inbounds)){
				$alarm='';
				$inbound['created']=date('Y-m-d',$inbound['created']);
				$verify=$this->verify($inbound['ifVerify']);
				$confirm=$this->confirm($inbound['ifConfirm']);
				if($inbound['ifVerify']==0){
					$alarm='bgYellow';
				}elseif($inbound['ifVerify']==-1){
					$alarm='bgRed';
				}
				// 判断入库单入库的状态
				$inStatus='';
				if($inbound['ifComplete']==1){
					$inStatus='部分入库';
				}elseif($inbound['ifComplete']==2){
					$inStatus='入库完成';
				}else{
					$inStatus='等待入库';
				}

				if($inbound['killed']>0){
					$link='<a href="/s.php?module=inbound&action=restore&inboundid='.$inbound['inboundid'].'&rt=list">恢复</a>';
				}else{
					$link=$this->iif($inbound['ifConfirm']==1,$this->iif($inbound['ifVerify']==0,'<a href="/s.php?module=inbound&action=verify&inboundid='.$inbound['inboundid'].'">审</a>',''),'<a href="/s.php?module=inbound&action=confirm&inboundid='.$inbound['inboundid'].'">确</a> <a href="/s.php?module=inbound&action=kill&inboundid='.$inbound['inboundid'].'&rt=list" onclick="return confirm(\'你确定要删除这个物资 '.$inbound['inboundno'].' 吗？\');">删</a>').' <a href="/s.php?module=inbound&action=update&inboundid='.$inbound['inboundid'].'&rt=list">改</a> '.$this->iif($inbound['ifVerify']==1,'<a href="/p.php?action=inboundApply&inboundid='.$inbound['inboundid'].'" target="_blank">印</a>','');
				}
				if($this->input['layout']=='grid'){//表格
					$r['body'].='<li title="由 '.$inbound['creator'].' 建于 '.$inbound['created'].$this->iif($inbound['modifier']!='', '，'.$inbound['modifier'].' 改于 '.date('Y-m-d',$inbound['modified']), '').'"'.$this->iif($i%4==0, ' class="end"', '').'>
					<div class="mmlistt">
						<span class="right normal">';
					if($inbound['killed']>0){
						$r['body'].='<a href="/s.php?module=inbound&action=revival&inboundid='.$inbound['inboundid'].'&rt=list" onclick="return confirm(\'你确定要恢复这个物资 '.$inbound['inboundno'].' 吗？\');">恢复</a>';
					}else{
						$r['body'].='<a href="/s.php?module=inbound&action=kill&inboundid='.$inbound['inboundid'].'&rt=list" onclick="return confirm(\'你确定要删除这个物资 '.$inbound['inboundno'].' 吗？\');">删</a>';
						if($inbound['ifVerify']==0){
							$r['body'].=' <a href="/s.php?module=inbound&action=update&inboundid='.$inbound['inboundid'].'&rt=list">改</a>';
						}
					}
					$r['body'].='</span>
						<a href="/s.php?module=inbound&action=view&inboundid='.$inbound['inboundid'].'">'.$inbound['inboundno'].'</a><br><span class="small gray right">'. $inbound['created'].'</span><span class="normal gray">'.$verify.'</span> '.$verifyOperate.'</div>
					<div class=mmlistb>
						<div title="'.$inbound['applicant'].'"><span class="small gray">申请人：'.$inbound['applicant'].'</span></div>
						<div title="'.$inbound['department'].'"><span class="small gray">申请部门：'.$inbound['department'].'</span></div>
						<div class="small clear"><span class=right title="由 '.$inbound['creator'].' 建于 '.$inbound['created'].'">由 '.$inbound['creator'].' 建于 '.$inbound['created'].'</span></div></div></li>';
				}else{
					$title=$no='';
					if($inbound['orderid']>0){
						$order=$this->DB->queryFirst("SELECT orderid,orderno FROM `order` WHERE orderid=".$inbound['orderid']." LIMIT 0,1");
						$title.='销售订单编号：'.$order['orderno'].'<br>';
						$no='<a class="small" href="/s.php?module=order&action=view&orderid='.$order['orderid'].'">'.$order['orderno'].'</a>';
					}
					if($inbound['porderid']>0 OR $inbound['orderno']){
						$porder=$this->DB->queryFirst("SELECT o.porderid,o.orderno,o.supplierid,s.title AS supplier FROM porder AS o LEFT JOIN supplier AS s ON (s.supplierid=o.supplierid) WHERE o.porderid=".$inbound['porderid']." LIMIT 0,1");
						$title.='采购订单编号：'.$this->iif($inbound['porderid']>0,$porder['orderno'],$inbound['orderno']).'<br>';
						$title.='供应商：'.$this->iif($inbound['supplierid']>0,$porder['supplier'],$inbound['isupplier']).'<br>';
						$no='<a class="small" href="/s.php?module=porder&action=view&porderid='.$porder['orderid'].'">'.$porder['orderno'].'</a>';
					}
					if($inbound['sampleid']>0){
						$sample=$this->DB->queryFirst("SELECT sampleid,sampleno FROM sample WHERE sampleid=".$inbound['sampleid']." LIMIT 0,1");
						$title.='样品单编号：'.$sample['sampleno'].'<br>';
						$no='<a class="small" href="/s.php?module=sample&action=view&sampleid='.$sample['sampleid'].'">'.$sample['sampleno'].'</a>';
					}

					if($inbound['workcenterid']>0)$title.='工作中心：'.$inbound['workcenter'].'<br>';
					if($inbound['purpose']!='')$title.='用途：'.$inbound['purpose'].'<br>';
					if($inbound['remark']!='')$title.='备注：'.$inbound['remark'].'<br>';
					$items=$this->DB->query("
						SELECT pi.*,
							mv.title AS version
						FROM preinbounditem AS pi
						LEFT JOIN `materialversion` AS mv ON (mv.versionid=pi.versionid)
						WHERE pi.killed=0 AND pi.module='inbound' AND pi.mid='".$inbound['inboundid']."'
						ORDER BY pi.created ASC
					");
					if($this->DB->numRows($items)){
						$title.='申请出库物资：<br>';
						while($item=$this->DB->fetchArray($items)){
							$materialInfo=$this->getMaterial(array('materialid'=>$item['materialid'],'itemid'=>$item['materialitemid']));
							$title.='　'.$materialInfo['no'].'　'.$materialInfo['material'].' '.$materialInfo['standard'].'（入库数：'.$this->iif($item['quantity']>0,$item['quantity'],0).'）</br>';
						}
					}

					$r['body'].='<tr class="'.$this->rotateLine().' '.$alarm.'">
						<td>'.$i.'</td>
						<td>'.$inStatus.'</td>
						<td><a class="tip" href="/s.php?module=inbound&action=view&inboundid='.$inbound['inboundid'].'" title="'.$title.'" >'.$inbound['inboundno'].'</a>'.$this->iif($no,'　　('.$no.')','').$this->iif($inbound['attachs']>0, ' <span class="attachFile" title="有'.$inbound['attachs'].'个附件。"></span> ', '').$this->iif($inbound['images']>0, ' <span class="attachImage" title="有'.$inbound['images'].'个图片。"></span> ', '').'</td>
						<td>'.$inbound['type'].'</td>
						<td>'.$inbound['applicant'].'</td>
						<td>'.$inbound['department'].'</td>
						<td>'.$inbound['created'].'</td>
						<td><span class="small">'.$confirm.' , '.$verify.'</span></td>
						<td align="center">'.$link.'</td>
					</tr>';
				}
				$i++;
			}
			if($this->input['layout']=='grid'){
				$r['body'].= '</ul>';
			}else{
				$r['body'].='</tbody></table>';
			}
		}else{
			$r['body']='暂无相关记录';
		}
		return $r;
	}
	/**
	 * outbound 的list  分页写法
	 * @author creator gaowenfei 2013/11/16 15:56
	 * @access public
	 * @param array $b 参数传递 ：start 、 perpage
	 * @return string $str 返回Requirement的list
	 * @throws none 没有异常
	 */
	function listOutbound($b){
		if($b['start']=='')$b['start']=0;
		if($b['perpage']=='')$b['perpage']=20;
		if($b['condition']=='')$b['condition']='`outbound`.killed=0';
		if($b['orderby']=='')$b['orderby']=' `outbound`.outboundid DESC';
		$count=$this->DB->queryFirst("SELECT COUNT(outboundid) AS counter FROM outbound WHERE ".$b['condition'].$b['filter']);
		$r['counter']=$count['counter'];
		$outbounds=$this->DB->query("
			SELECT `outbound`.outboundid,`outbound`.outboundno,`outbound`.applicant,`outbound`.purpose,`outbound`.orderid,`outbound`.sampleid,`outbound`.supplierid,`outbound`.remark,`outbound`.ifVerify,`outbound`.typeid,`outbound`.ifComplete,`outbound`.ifConfirm,`outbound`.created,`outbound`.killed,`outbound`.purpose,`outbound`.remark,
				t.title AS type,
				d.title AS department,
				d.title AS department,
				u.username AS creator,
				us.username AS modifier
			FROM `outbound`
			LEFT JOIN `inorouttype` AS t ON (t.typeid=`outbound`.typeid)
			LEFT JOIN `department` AS d ON (d.departmentid=`outbound`.departmentid)
			LEFT JOIN `user` AS u ON (u.userid=`outbound`.creator)
			LEFT JOIN `user` AS us ON (us.userid=`outbound`.modifier)
			WHERE ".$b['condition'].$b['filter']."
			ORDER BY ".$b['orderby']."
			LIMIT ".$b['start'].",".$b['perpage']."
		");
		if($this->DB->numRows()){
			if($this->input['layout']=='grid'){
				$r['body']='<ul id="mmlist" class="mmlist clear">';
			}else{
				$r['body']='<table class="hundred mytable tablesorter"><thead><th width="15">ID</th><th width="50">出库状态</th><th width="30">出库单编号</th><th width="100">出库类型</th><th width="100">申请人</th><th width="100">申请部门</th><th width="70">申请时间</th><th width="70">状态</th><th width="70"></th></thead><tbody>';
			}
			$i=1;
			while($outbound=$this->DB->fetchArray($outbounds)){
				$alarm='';
				$verify=$this->verify($outbound['ifVerify']);
				$confirm=$this->confirm($outbound['ifConfirm']);
				$outbound['created']=date('Y-m-d',$outbound['created']);
				if($outbound['ifVerify']==0){
					$alarm='bgYellow';
				}elseif($outbound['ifVerify']==-1){
					$alarm='bgRed';
				}
				// 判断出库单出库的状态
				$outStatus='';
				if($outbound['ifComplete']==1){
					$outStatus='部分出库';
				}elseif($outbound['ifComplete']==2){
					$outStatus='出库完成';
				}else{
					$outStatus='等待出库';
				}

				if($outbound['killed']>0){
					$link='<a href="/s.php?module=outbound&action=restore&outboundid='.$outbound['outboundid'].'&rt=list">恢复</a>';
				}else{
					$link=$this->iif($outbound['ifConfirm']==1,$this->iif($outbound['ifVerify']==0,'<a href="/s.php?module=outbound&action=verify&outboundid='.$outbound['outboundid'].'">审</a> <a href="/s.php?module=outbound&action=update&outboundid='.$outbound['outboundid'].'&rt=list">改</a>',''),'<a href="/s.php?module=outbound&action=confirm&outboundid='.$outbound['outboundid'].'">确</a> <a href="/s.php?module=outbound&action=update&outboundid='.$outbound['outboundid'].'&rt=list">改</a> <a href="/s.php?module=outbound&action=kill&outboundid='.$outbound['outboundid'].'&rt=list" onclick="return confirm(\'你确定要删除这个出库单 '.$outbound['outboundno'].' 吗？\');">删</a>').$this->iif($outbound['ifVerify']==1,'<a href="/s.php?module=outbound&action=update&outboundid='.$outbound['outboundid'].'&rt=list">改</a> <a href="/p.php?action=outboundApply&outboundid='.$outbound['outboundid'].'" target="_blank">印</a>','');
				}
				if($this->input['layout']=='grid'){//表格
					$r['body'].='<li title="由 '.$outbound['creator'].' 建于 '.$outbound['created'].$this->iif($outbound['modifier']!='', '，'.$outbound['modifier'].' 改于 '.date('Y-m-d',$outbound['modified']), '').'"'.$this->iif($i%4==0, ' class="end"', '').'>
					<div class="mmlistt">
						<span class="right normal">';
					if($outbound['killed']>0){
						$r['body'].='<a href="/s.php?module=outbound&action=revival&outboundid='.$outbound['outboundid'].'&rt=list" onclick="return confirm(\'你确定要恢复这个出库单 '.$outbound['outboundno'].' 吗？\');">恢复</a>';
					}else{
						$r['body'].='<a href="/s.php?module=outbound&action=kill&outboundid='.$outbound['outboundid'].'&rt=list" onclick="return confirm(\'你确定要删除这个出库单 '.$outbound['outboundno'].' 吗？\');">删</a>';
						if($outbound['ifVerify']==0){
							$r['body'].=' <a href="/s.php?module=outboundApply&action=update&outboundid='.$outbound['outboundid'].'&rt=list">改</a>';
						}
					}
					$r['body'].='</span>
						<a href="/s.php?module=outbound&action=view&outboundid='.$outbound['outboundid'].'">'.$outbound['outboundno'].'</a><br><span class="small gray right">'. $outbound['created'].'</span><span class="normal gray">'.$verify.'</span> '.$verifyOperate.'</div>
					<div class=mmlistb>
						<div><span class="small gray">出库类型：'.$outbound['type'].'</span></div>
						<div title="'.$outbound['orderno'].'"><span class="small gray">申请人：'.$outbound['applicant'].'</span></div>
						<div><span class="small gray">申请部门：'.$outbound['department'].'</span></div>
						<div class="small clear"><span class="right" title="由 '.$outbound['creator'].' 建于 '.$outbound['created'].'">由 '.$outbound['creator'].'  建于 '.$outbound['created'].'</span></div></div></li>';
				}else{
					$title=$no='';
					if($outbound['orderid']>0){
						$order=$this->DB->queryFirst("SELECT orderid,orderno FROM `order` WHERE orderid=".$outbound['orderid']." LIMIT 0,1");
						$title.='订单编号：'.$order['orderno'].'<br>';
						$no='<a class="small gray" href="/s.php?module=order&action=view&orderid='.$order['orderid'].'">'.$order['orderno'].'</a>';
					}
					if($outbound['sampleid']>0){
						$sample=$this->DB->queryFirst("SELECT sampleid,sampleno FROM sample WHERE sampleid=".$outbound['sampleid']." LIMIT 0,1");
						$title.='样品单编号：'.$sample['sampleno'].'<br>';
						$no='<a class="small gray" href="/s.php?module=order&action=view&orderid='.$order['orderid'].'">'.$sample['sampleno'].'</a>';
					}
					if($outbound['purpose']!='')$title.='用途：'.$outbound['purpose'].'<br>';
					if($outbound['remark']!='')$title.='备注：'.$outbound['remark'].'<br>';
					$items=$this->DB->query("
						SELECT pi.*,
							mv.title AS version,mv.qualified AS store1,mv.disqualified AS store2
						FROM preoutbounditem AS pi
						LEFT JOIN `materialversion` AS mv ON (mv.versionid=pi.versionid)
						WHERE pi.killed=0 AND pi.module='outbound' AND pi.mid='".$outbound['outboundid']."'
						ORDER BY pi.itemid ASC
					");
					if($this->DB->numRows($items)){
						$title.='申请出库物资：<br>';
						while($item=$this->DB->fetchArray($items)){
							$materialInfo=$this->getMaterial(array('materialid'=>$item['materialid'],'itemid'=>$item['materialitemid']));
							$store=0;
							if($outbound['typeid']==15 OR $outbound['typeid']==16){
								$d=$this->iif($outbound['typeid']==15,' AND i.orderid='.$this->iif($outbound['orderid']>0,$outbound['orderid'],0),' AND i.sampleid='.$this->iif($outbound['sampleid']>0,$outbound['sampleid'],0));
								$versionQuantity=$this->DB->queryFirst("
									SELECT SUM(ii.quantity) AS quantity
									FROM inbounditem AS ii
									LEFT JOIN preinbounditem AS pii ON (pii.itemid=ii.preitemid)
									LEFT JOIN inbound AS i ON (i.inboundid=ii.inboundid)
									WHERE ii.killed=0 $d AND pii.productid='".$item['productid']."' AND pii.productitemid='".$item['productitemid']."'
									LIMIT 0,1
								");
								$store=$versionQuantity['quantity'];
								$product=$this->getProduct(array('productid'=>$item['productid'],'itemid'=>$item['productitemid']));
								$material='　'.$product['title'].'　'.$product['standard'];
							}else{
								if($item['ifQualified']==1){
									$qualifiedType='良品';
									$store=$item['store1'];
								}elseif($item['ifQualified']==0){
									$qualifiedType='不良品';
									$store=$item['store2'];
								}
								$material='　'.$materialInfo['no'].'　'.$materialInfo['title'].'　'.$materialInfo['standard'];
							}
							$title.=$material.'　（库存：'.$store.'　申请数：'.$this->iif($item['quantity']>0,$item['quantity'],0).$this->iif($item['wastage']>0,'　损耗数：'.$item['wastage'],'').'）</br>';
						}
					}
					$r['body'].='<tr class="'.$this->rotateLine().' '.$alarm.'">
						<td>'.$i.'</td>
						<td>'.$outStatus.'</td>
						<td><a class="tip" title="'.htmlspecialchars($title).'" href="/s.php?module=outbound&action=view&outboundid='.$outbound['outboundid'].'">'.$outbound['outboundno'].$this->iif($no,'　　('.$no.')','').$this->iif($outbound['attachs']>0, ' <span class="attachFile" title="有'.$outbound['attachs'].'个附件。"></span> ', '').$this->iif($outbound['images']>0, ' <span class="attachImage" title="有'.$outbound['images'].'个图片。"></span> ', '').'</a></td>
						<td title="'.$outbound['type'].'">'.$outbound['type'].'</td>
						<td>'.$outbound['applicant'].'</td>
						<td>'.$outbound['department'].'</td>
						<td>'.$outbound['created'].'</td>
						<td><span class="small">'.$confirm.' , '.$verify.'</span></td>

						<td align="center">'.$link.'</td>
					</tr>';
				}
				$i++;
			}
			if($this->input['layout']=='grid'){
				$r['body'].= '</ul>';
			}else{
				$r['body'].='</tbody></table>';
			}
		}else{
			$r['body']='暂无相关记录';
		}
		return $r;
	}
//
	/**
	 * Requirement 的list  分页写法
	 * @author creator gaowenfei 2013/11/4 11:07
	 * @access public
	 * @param array $b 参数传递 ：start 、 perpage
	 * @return string $str 返回Requirement的list
	 * @throws none 没有异常
	 */
	function listRequirement($b){
		if($b['show']=='all'){
			$condition='`requirement`.killed>=0';
		}elseif($b['show']=='unlock'){
			$condition='`requirement`.statusid=1 AND `requirement`.killed=0';
		}elseif($b['show']=='locked'){
			$condition='`requirement`.statusid=2 AND `requirement`.killed=0';
		}elseif($b['show']=='inProduction'){
			$condition='`requirement`.statusid=3 AND `requirement`.killed=0';
		}elseif($b['show']=='Done'){
			$condition='`requirement`.statusid=4 AND `requirement`.killed=0';
		}else{
			$condition='`requirement`.killed=0';
		}
		if($b['start']=='')$b['start']=0;
		if($b['perpage']=='')$b['perpage']=20;
		if($b['condition']=='')$b['condition']='`requirement`.killed=0';
		if($b['orderby']!='')$query['orderby']=$b['orderby'];
		if($b['direction']!='')$query['direction']=$b['direction'];
		if($b['show']!='')$query['show']=$b['show'];
		$ob=$this->orderby(array('module'=>'requirement','direction'=>'desc','orderby'=>'created', 'default'=>'requirementno','start'=>$b['start'],'perpage'=>$b['perpage'], 'serial'=>array( array('title'=>'编号', 'field'=>'requirementno','word'=>'no'), array('title'=>'修改时间', 'field'=>'modified'), array('title'=>'建立时间', 'field'=>'created')),'appendUrl'=>$query));
		$requirements=$this->DB->query("
			SELECT `requirement`.*,
				mt.title AS type,
				msi.title AS situation,
				s.title AS status,
				u.username AS creator,
				us.username AS modifier
			FROM `requirement`
			LEFT JOIN mftstatus AS s ON (s.statusid=`requirement`.statusid)
			LEFT JOIN `mftsituation` AS msi ON (msi.situationid=`requirement`.situationid)
			LEFT JOIN `mfttype` AS mt ON (mt.typeid=`requirement`.typeid)
			LEFT JOIN user AS u ON (u.userid=`requirement`.creator)
			LEFT JOIN user AS us ON (us.userid=`requirement`.modifier)
			WHERE ".$condition." ".$b['find']."
			ORDER BY ".$ob['sql']."
			LIMIT ".$b['start'].",".$b['perpage']."
		");
		if($counter=$this->DB->numRows()){
			$i=$b['start']+1;
			while($requirement=$this->DB->fetchArray($requirements)){
				$orderChange=$this->iif($requirement['orderChange']==0,'',' <span class="small darkred"> ( 订单取消 ) </span>');
				$requirement['created']=date('Y-m-d',$requirement['created']);
				if($requirement['killed']>0){
					$link='<a href="/s.php?module=requirement&action=restore&requirementid='.$requirement['requirementid'].'&rt=list">恢复</a>';
				}else{
					$link=$this->iif($requirement['statusid']==1,'<a href="/s.php?module=requirement&action=update&requirementid='.$requirement['requirementid'].'&rt=list">改</a>','').' <a href="/p.php?action=requirement&requirementid='.$requirement['requirementid'].'" target="_blank">印</a>';
				}
				if($b['layout']=='grid'){//表格
					$requirementList.='<li title="由 '.$requirement['creator'].' 建于 '.$created.$this->iif($requirement['modifier']!='', '，'.$requirement['modifier'].' 改于 '.date('Y-m-d H:i',$requirement['modified']), '').'"'.$this->iif($i%4==0, ' class="end"', '').'>
					<div class="mmlistt '.$this->iif($requirement['statusid']==1, 'bgYellow','').'">
						<span class="right normal">';
					if($requirement['killed']>0){
						$requirementList.='<a href="/s.php?module=requirement&action=revival&requirementid='.$requirement['requirementid'].'&rt=list" onclick="return confirm(\'你确定要恢复这个需求计划单 '.$requirement['requirementno'].' 吗？\');">恢复</a>';
					}else{
						$requirementList.='<a href="/s.php?module=requirement&action=kill&requirementid='.$requirement['requirementid'].'&rt=list" onclick="return confirm(\'你确定要删除这个需求计划单 '.$requirement['requirementno'].' 吗？\');">删</a> '.$this->iif($requirement['ifVerify']==0,'<a href="/s.php?module=requirement&action=update&requirementid='.$requirement['requirementid'].'&rt=list">改</a>','');
					}
					$requirementList.='</span>
						 <a href="/s.php?module=requirement&action=view&requirementid='.$requirement['requirementid'].'">'.$requirement['requirementno'].'</a><span class="small">('.$requirement['status'].')</span><br><span class="small">'.$requirement['requirementid'].'</span><span class="small gray right">'.$requirement['created'].'</span></div>
						<div class="mmlistb">
						<div><span class="small gray">紧急情况：'.$requirement['situation'].'</span></div>
						<div><span class="small gray">计划来源：'.$requirement['type'].'</span>'.$orderChange.'</div>
						<div><span class="small gray">需求计算方式：'.$this->iif($requirement['modeid']==0,'净需求',$this->iif($requirement['modeid']==1,'毛需求','毛需求(BOM)')).'</span></div>
						<div><span class="small gray">计划员：'.$requirement['planner'].'</span></div>
						<div><span class="small gray">备注：'.$requirement['remark'].'</span></div>
						<div class="small clear"><span class=right title="由 '.$prchase['creator'].' 建于 '.$requirement['created'].'">建于 '.$requirement['created'].'</span></div></div></li>';
				}else{
					$requirementList.='<tr class="'.$this->rotateLine().' '.$this->iif($requirement['statusid']==1, 'bgYellow','').'">
						<td>'.$i.'</td>
						<td><a href="/s.php?module=requirement&action=view&requirementid='.$requirement['requirementid'].'">'.$requirement['requirementno'].'</td>
						<td><span class="'.$this->iif($requirement['situationid']==2,'darkred middle',$this->iif($requirement['situationid']==3,'red middle bold','')).'">'.$requirement['situation'].'</span></td>
						<td>'.$requirement['type'].' '.$orderChange.'</td>
						<td>'.$this->iif($requirement['modeid']==0,'净需求',$this->iif($requirement['modeid']==1,'毛需求','毛需求(BOM)')).'</td>
						<td title="'.$requirement['planner'].'">'. $requirement['planner'].'</td>
						<td>'.$requirement['created'].'</td>
						<td>'.$requirement['remark'].'</td>
						<td>'.$requirement['status'].'</td>
						<td align="center">'.$link.'</td>
					</tr>';
				}
				$i++;
			}
			if($b['layout']=='grid'){
					$requirementList .= '</ul>';
				}else{
					$requirementList.='</tbody></table>';
				}
			}else{
				$requirementList='<tr class="center darkred"><td colspan=12>暂无相关记录!</td></tr></tbody></table>';
			}
			$pcount=$this->DB->queryFirst("SELECT COUNT(requirementid) AS count FROM `requirement` WHERE ".$condition." ".$b['find']." ");

		return array('body'=>$requirementList,'counter'=>$counter,'count'=>$pcount['count']);
	}
	//
	/**
	 * task 的list  分页写法
	 * @author creator gaowenfei 2013/11/6 12:01
	 * @access public
	 * @param array $b 参数传递 ：start 、 perpage
	 * @return string $str 返回 task 的 list
	 * @throws none 没有异常
	 */
	function listTask($b){
		if($b['show']=='all'){
			$condition='`task`.killed>=0';
		}elseif($b['show']=='unlock'){
			$condition='`task`.statusid=1 AND `task`.killed=0';
		}elseif($b['show']=='locked'){
			$condition='`task`.statusid=2 AND `task`.killed=0';
		}elseif($b['show']=='inProduction'){
			$condition='`task`.statusid=3 AND `task`.killed=0';
		}elseif($b['show']=='Done'){
			$condition='`task`.statusid=4 AND `task`.killed=0';
		}else{
			$condition='`task`.killed=0';
		}
		if($b['start']=='')$b['start']=0;
		if($b['perpage']=='')$b['perpage']=20;
		if($b['condition']=='')$b['condition']='`task`.killed=0';
		if($b['orderby']!='')$query['orderby']=$b['orderby'];
		if($b['direction']!='')$query['direction']=$b['direction'];
		if($b['show']!='')$query['show']=$b['show'];
		$ob=$this->orderby(array('module'=>'task','direction'=>'desc','orderby'=>'created', 'default'=>'created', 'serial'=>array( array('title'=>'编号', 'field'=>'taskno','word'=>'no'), array('title'=>'修改时间', 'field'=>'modified'), array('title'=>'建立时间', 'field'=>'created')),'appendUrl'=>$query));
		$tasks=$this->DB->query("
			SELECT `task`.*,
				s.title AS status,
				u.username AS creator,
				us.username AS modifier
			FROM `task`
			LEFT JOIN mftstatus AS s ON (s.statusid=`task`.statusid)
			LEFT JOIN user AS u ON (u.userid=`task`.creator)
			LEFT JOIN user AS us ON (us.userid=`task`.modifier)
			WHERE ".$condition." ".$b['find']."
			ORDER BY ".$ob['sql']."
			LIMIT ".$b['start'].",".$b['perpage']."
		");
		if($counter=$this->DB->numRows()){
			$i=$b['start']+1;
		while($task=$this->DB->fetchArray($tasks)){
			$task['created']=date('Y-m-d',$task['created']);
			$addtype=$this->iif($task['addtype']==1,'手工方式','生产计划');
			if($task['killed']>0){
				$link='<a href="/s.php?module=task&action=restore&taskid='.$task['taskid'].'&rt=list">恢复</a>';
			}else{
				$link=$this->iif($task['statusid']==1,'<a href="/s.php?module=task&action=update&taskid='.$task['taskid'].'&rt=list">改</a> <a href="/s.php?module=task&action=lock&taskid='.$task['taskid'].'">锁</a>','').' <a href="/p.php?action=task&taskid='.$task['taskid'].'" target="_blank">印</a>';
			}
			if($this->input['layout']=='grid'){//表格
				$body.='<li title="由 '.$task['creator'].' 建于 '.$created.$this->iif($task['modifier']!='', '，'.$task['modifier'].' 改于 '.date('Y-m-d H:i',$task['modified']), '').'"'.$this->iif($i%4==0, ' class="end"', '').'>
				<div class="mmlistt '.$this->iif($task['statusid']==1, 'bgYellow','').'">
					<span class="right normal">';
				if($task['killed']>0){
					$body.='<a href="/s.php?module=task&action=revival&taskid='.$task['taskid'].'&rt=list" onclick="return confirm(\'你确定要恢复这个生产任务单 '.$task['taskno'].' 吗？\');">恢复</a>';
				}else{
					$body.='<a href="/s.php?module=task&action=kill&taskid='.$task['taskid'].'&rt=list" onclick="return confirm(\'你确定要删除这个生产任务单 '.$task['taskno'].' 吗？\');">删</a> '.$this->iif($task['statusid']==1,'<a href="/s.php?module=task&action=update&taskid='.$task['taskid'].'&rt=list">改</a> <a href="/s.php?module=task&action=lock&taskid='.$task['taskid'].'">锁</a>','');
				}
				$body.='</span>
					<span class="small">'.$i.'</span> <a href="/s.php?module=task&action=view&taskid='.$task['taskid'].'">'.$task['taskno'].'</a><br><span class="small gray right">'.$task['created'].'</span></div>
				<div class="mmlistb">
					<div><span class="small gray">任务员：'.$task['tasker'].'</span></div>
					<div><span class="small gray">状  态：'.$task['status'].'</span></div>
					<div><span class="small gray">录入类型：'.$addtype.'</span></div>
					<div><span class="small gray">备注：'.$task['remark'].'</span></div>
					<div class="small clear"><span class=right title="由 '.$prchase['creator'].' 建于 '.$task['created'].'">建于 '.$task['created'].'</span></div></div></li>';
				}else{
					$body.='<tr class="'.$this->rotateLine().' '.$this->iif($task['statusid']==1, 'bgYellow','').'">
						<td>'.$i.'</td>
						<td><a href="/s.php?module=task&action=view&taskid='.$task['taskid'].'">'.$task['taskno'].'</td>
						<td title="'.$task['tasker'].'">'. $task['tasker'].'</td>
						<td>'.$task['created'].'</td>
						<td>'.$task['remark'].'</td>
						<td>'.$task['status'].'</td>
						<td>'.$addtype.'</td>
						<td align="center">'.$link.'</td>
					</tr>';
				}
				$i++;
			}
			if($b['layout']=='grid'){
					$body .= '</ul>';
				}else{
					$body.='</tbody></table>';
				}
			}else{
				$body='<tr class="center darkred"><td colspan=12>暂无相关记录!</td></tr></tbody></table>';
			}
			$pcount=$this->DB->queryFirst("SELECT COUNT(taskid) AS count FROM `task` WHERE ".$condition." ".$b['find']." ");

		return array('body'=>$body,'counter'=>$counter,'count'=>$pcount['count']);
	}
	/**
	 * dispatch 的list  分页写法
	 * @author creator gaowenfei 2013/11/6 14:15
	 * @access public
	 * @param array $b 参数传递 ：start 、 perpage
	 * @return string $str 返回 dispatch 的 list
	 * @throws none 没有异常
	 */
	function listDispatch($b){
		if($b['show']=='all'){
			$condition='1=1';
		}elseif($b['show']=='unlock'){
			$condition='`dispatch`.statusid=1 AND `dispatch`.killed=0';
		}elseif($b['show']=='locked'){
			$condition='`dispatch`.statusid=2 AND `dispatch`.killed=0';
		}elseif($b['show']=='inProduction'){
			$condition='`dispatch`.statusid=3 AND `dispatch`.killed=0';
		}elseif($b['show']=='Done'){
			$condition='`dispatch`.statusid=4 AND `dispatch`.killed=0';
		}else{
			$condition='`dispatch`.killed=0';
		}
		if($b['start']=='')$b['start']=0;
		if($b['perpage']=='')$b['perpage']=20;
		if($b['condition']=='')$b['condition']='`dispatch`.killed=0';
		if($b['orderby']!='')$query['orderby']=$b['orderby'];
		if($b['direction']!='')$query['direction']=$b['direction'];
		if($b['show']!='')$query['show']=$b['show'];
		$ob=$this->orderby(array('module'=>'dispatch','direction'=>'desc','orderby'=>'`dispatch`.created', 'default'=>'created', 'serial'=>array(array('title'=>'编号', 'field'=>'dispatchno'), array('title'=>'ID', 'field'=>'dispatchid'), array('title'=>'修改时间', 'field'=>'modified'), array('title'=>'建立时间', 'field'=>'created')),'appendUrl'=>$query));
		$departmentid=$this->user['departmentid'];$departments=array(8,9,10);
		$purviews=' AND wc.departmentid='.$departmentid;
		if(in_array($departmentid,$departments))$purviews='';
		$dispatchs=$this->DB->query("
			SELECT `dispatch`.dispatchid,`dispatch`.dispatchno,`dispatch`.statusid,`dispatch`.workcenterid,`dispatch`.applicant,
					`dispatch`.ifVerify,`dispatch`.verifier,`dispatch`.verified,`dispatch`.verifyRemark,`dispatch`.remark,`dispatch`.ordering,`dispatch`.modified,`dispatch`.created,`dispatch`.killed,
					wc.title AS workcenter,
					ms.title AS status,
				u.username AS creator,
				us.username AS modifier
			FROM dispatch
			LEFT JOIN user AS u ON (u.userid=`dispatch`.creator)
			LEFT JOIN user AS us ON (us.userid=`dispatch`.modifier)
			LEFT JOIN mftstatus AS ms ON (ms.statusid=`dispatch`.statusid)
			LEFT JOIN workcenter AS wc ON (wc.workcenterid=`dispatch`.workcenterid)
			WHERE ".$condition." ".$purviews." ".$b['find']."
			ORDER BY ".$ob['sql']."
			LIMIT ".$b['start'].",".$b['perpage']."
		");
		if($counter=$this->DB->numRows()){
			$i=$b['start']+1;
			while($dispatch=$this->DB->fetchArray($dispatchs)){
				$dispatch['created']=date('Y-m-d',$dispatch['created']);
				$verify=$this->verify($dispatch['ifVerify']);
				if($dispatch['killed']>0){
						$link='<a href="/s.php?module=dispatch&action=restore&dispatchid='.$dispatch['dispatchid'].'&rt=list">恢复</a>';
					}else{
						$link=$this->iif($dispatch['statusid']==1,'<a href="/s.php?module=dispatch&action=update&dispatchid='.$dispatch['dispatchid'].'&rt=list">改</a> <a href="/s.php?module=dispatch&action=verify&dispatchid='.$dispatch['dispatchid'].'">审</a>','').' <a href="/p.php?action=dispatch&dispatchid='.$dispatch['dispatchid'].'" target="_blank">印</a>';
					}
				if($this->input['layout']=='grid'){//表格
				$body.='<li title="由 '.$dispatch['creator'].' 建于 '.$created.$this->iif($dispatch['modifier']!='', '，'.$dispatch['modifier'].' 改于 '.date('Y-m-d H:i',$dispatch['modified']), '').'"'.$this->iif($i%4==0, ' class="end"', '').'>
				<div class="mmlistt '.$this->iif($dispatch['ifVerify']==0, 'bgYellow', $this->iif($dispatch['ifVerify']==1, '', 'bgRed')).'">
					<span class="right normal">';
				/*
				if($dispatch['killed']>0){
					$body.='<a href="/s.php?module=dispatch&action=revival&dispatchid='.$dispatch['dispatchid'].'&rt=list" onclick="return confirm(\'你确定要恢复这个派工申请单 '.$dispatch['dispatchno'].' 吗？\');">恢复</a>';
				}else{
					$body.='<a href="/s.php?module=dispatch&action=kill&dispatchid='.$dispatch['dispatchid'].'&rt=list" onclick="return confirm(\'你确定要删除这个派工申请单 '.$dispatch['dispatchno'].' 吗？\');">删</a> '.$this->iif($dispatch['ifVerify']!=1,'<a href="/s.php?module=dispatch&action=update&dispatchid='.$dispatch['dispatchid'].'&rt=list">改</a>','');
				}*/
				$body.='</span>
				<span class="small">'.$dispatch['dispatchid'].'</span> <a href="/s.php?module=dispatch&action=view&dispatchid='.$dispatch['dispatchid'].'">'.$dispatch['dispatchno'].'</a><span class="small '.$this->iif($dispatch['statusid']==2, 'red', $this->iif($dispatch['statusid']==4, 'green', '')).'">('.$dispatch['status'].')</span><br><span class="small gray right">'.$dispatch['created'].'</span>'.$this->iif($dispatch['attachs']>0, ' <span class="attachFile" title="有'.$dispatch['attachs'].'个附件。"></span> ', '').$this->iif($dispatch['images']>0, ' <span class="attachImage" title="有'.$dispatch['images'].'个图片。"></span> ', '').''.$verify.'</div>
				<div class="mmlistb">
				<div><span class="small gray">工作中心：'.$dispatch['workcenter'].'</span></div>
				<div><span class="small gray">状态：'.$dispatch['status'].'</span></div>
				<div><span class="small gray">备注：'.$dispatch['remark'].'</span></div>
				<div><span class="small gray">申请人：'.$dispatch['applicant'].'</span></div>
				<div><span class="small gray">申请时间：'.$dispatch['created'].'</span></div>
				<div class="small clear"><span class=right title="由 '.$dispatch['creator'].' 建于 '.$dispatch['created'].'">由 '.$dispatch['creator'].' 建于 '.$dispatch['created'].'</span></div></div></li>';
				}else{
					$body.='<tr class="'.$this->rotateLine().' '.$this->iif($dispatch['statusid']==1, 'bgYellow','').'">
						<td>'.$i.'</td>
						<td><a href="/s.php?module=dispatch&action=view&dispatchid='.$dispatch['dispatchid'].'">'.$dispatch['dispatchno'].'</td>
						<td title="'.$dispatch['applicant'].'">'. $dispatch['applicant'].'</td>
						<td>'.$dispatch['created'].'</td>
						<td>'.$dispatch['workcenter'].'</td>
						<td>'.$dispatch['status'].'</td>
						<td align="center">'.$link.'</td>
					</tr>';
				}
				$i++;
			}
			if($b['layout']=='grid'){
					$body .= '</ul>';
				}else{
					$body.='</tbody></table>';
				}
			}else{
				$body='<tr class="center darkred"><td colspan=12>暂无相关记录!</td></tr></tbody></table>';
			}
			$pcount=$this->DB->queryFirst("SELECT COUNT(dispatchid) AS count FROM `dispatch` LEFT JOIN workcenter AS wc ON (wc.workcenterid=`dispatch`.workcenterid) WHERE ".$condition." ".$purviews." ".$b['find']." ");

		return array('body'=>$body,'counter'=>$counter,'count'=>$pcount['count']);
	}
	//
	/**
	 * Requirement 的 find 查找
	 * @author creator gaowenfei 2013/11/14 9:31
	 * @access public
	 * @param array $b 参数传递 :requirementitem 的 input
	 * @return string $str 返回 Requirement 的 condition
	 * @throws none 没有异常
	 */
	function findRequirement($b){
		if($b['created']!=''){
			$created=$b['created'];
			$created=str_replace('-','',$created);
			$created=substr($created,2,6);
			$condition .= " AND `requirement`.requirementno LIKE 'RM".$created."%' ";
		}
		if($b['quantity']!=''){
			$quantity=$b['quantity'];
			$rules="AND quantity='".$quantity."'";
			$condition.=$this->findrules(array('rules'=>$rules,'module'=>'requirement'));
		}
		if($b['dateline']!=''){
			$sd=explode('-',$b['dateline']);
			$dateline=mktime(0,0,0,$sd[1],$sd[2],$sd[0]);
			if($dateline=='')$dateline=0;
			$rules="AND dateline='".$dateline."'";
			$condition.=$this->findrules(array('rules'=>$rules,'module'=>'requirement'));
		}
		if($b['mftType']!=''){
			$mftType=$b['mftType'];
			$condition.=" AND `requirement`.typeid='".$mftType."'";
		}
		if($b['modeid']!=''){
			$modeid=$b['modeid'];
			$condition.=" AND `requirement`.modeid='".$modeid."'";
		}
		if($b['materialno']!=''){
			$materialno=$b['materialno'];
			$query="SELECT DISTINCT ri.requirementid
				FROM requirementitem AS ri
				LEFT JOIN material AS m ON (m.materialid=ri.materialid)
				WHERE ri.killed=0 AND m.materialno LIKE '%".$materialno."%' ";
			$condition.=$this->findrules(array('query'=>$query,'module'=>'requirement'));
		}
		if($b['materialtitle']!=''){
			$materialtitle=$b['materialtitle'];
			$query="SELECT DISTINCT ri.requirementid
				FROM requirementitem AS ri
				LEFT JOIN material AS m ON (m.materialid=ri.materialid)
				WHERE ri.killed=0 AND m.title LIKE '%".$materialtitle."%' ";
			$condition.=$this->findrules(array('query'=>$query,'module'=>'requirement'));
		}
		if($b['orderno']!=''){
			$orderno=$b['orderno'];
			$query="SELECT DISTINCT ri.requirementid
				FROM requirementitem AS ri
				LEFT JOIN `order` AS o ON (o.orderid=ri.mid)
				WHERE ri.killed=0 AND ri.module='order' AND o.orderno LIKE '%".$orderno."%' ";
			$condition.=$this->findrules(array('query'=>$query,'module'=>'requirement'));
		}
		if($b['sampleno']!=''){
			$sampleno=$b['sampleno'];
			$query="SELECT DISTINCT ri.requirementid
				FROM requirementitem AS ri
				LEFT JOIN `sample` AS s ON (s.sampleid=ri.mid)
				WHERE ri.killed=0 AND ri.module='sample' AND s.sampleno LIKE '%".$sampleno."%' ";
			$condition.=$this->findrules(array('query'=>$query,'module'=>'requirement'));
		}
		if($b['requirementno']!=''){
			$requirementno=$b['requirementno'];
			$condition.=" AND `requirement`.requirementno LIKE '%".$requirementno."%'";
		}
		return $condition;
	}
	//
		/**
	 * Task 的 find 查找
	 * @author creator gaowenfei 2013/11/14 9:31
	 * @access public
	 * @param array $b 参数传递 :taskitem 的 input
	 * @return string $str 返回 Task 的 condition
	 * @throws none 没有异常
	 */
	function findTask($b){
		if($b['created']!=''){
			$created=$b['created'];
			$created=str_replace('-','',$created);
			$created=substr($created,2,6);
			$condition .= " AND `task`.taskno LIKE 'TK".$created."%' ";
		}
		if($b['quantity']!=''){
			$quantity=$b['quantity'];
			$rules="AND quantity='".$quantity."'";
			$condition.=$this->findrules(array('rules'=>$rules,'module'=>'task'));
		}
		if($b['finishquantity']!=''){
			$finishquantity=$b['finishquantity'];
			$rules="AND finishquantity='".$finishquantity."'";
			$condition.=$this->findrules(array('rules'=>$rules,'module'=>'task'));
		}
		if($b['workdate']!=''){
			$sd=explode('-',$b['workdate']);
			$workdate=mktime(0,0,0,$sd[1],$sd[2],$sd[0]);
			if($workdate=='')$workdate=0;
			$rules="AND workdate='".$workdate."'";
			$condition.=$this->findrules(array('rules'=>$rules,'module'=>'task'));
		}
		if($b['finishdate']!=''){
			$sd=explode('-',$b['finishdate']);
			$finishdate=mktime(0,0,0,$sd[1],$sd[2],$sd[0]);
			if($finishdate=='')$finishdate=0;
			$rules="AND finishdate='".$finishdate."'";
			$condition.=$this->findrules(array('rules'=>$rules,'module'=>'task'));
		}
		if($b['workcenterid']!=''){
			$workcenterid=$b['workcenterid'];
			$rules="AND workcenterid='".$workcenterid."'";
			$condition.=$this->findrules(array('rules'=>$rules,'module'=>'task'));
		}
		if($b['materialno']!=''){
			$materialno=$b['materialno'];
			$query="SELECT DISTINCT ti.taskid
				FROM taskitem AS ti
				LEFT JOIN material AS m ON (m.materialid=ti.materialid)
				WHERE ti.killed=0 AND m.materialno LIKE '%".$materialno."%' ";
			$condition.=$this->findrules(array('query'=>$query,'module'=>'task'));
		}
		if($b['materialtitle']!=''){
			$materialtitle=$b['materialtitle'];
			$query="SELECT DISTINCT ti.taskid
				FROM taskitem AS ti
				LEFT JOIN material AS m ON (m.materialid=ti.materialid)
				WHERE ti.killed=0 AND m.title LIKE '%".$materialtitle."%' ";
			$condition.=$this->findrules(array('query'=>$query,'module'=>'task'));
		}
		if($b['taskno']!=''){
			$taskno=$b['taskno'];
			$condition.=" AND `task`.taskno LIKE '%".$taskno."%'";
		}
		return $condition;
	}
	//
		/**
	 * Dispatch 的 find 查找
	 * @author creator gaowenfei 2013/11/14 9:31
	 * @access public
	 * @param array $b 参数传递 :Dispatchitem 的 input
	 * @return string $str 返回 Dispatch 的 condition
	 * @throws none 没有异常
	 */
	function findDispatch($b){
		if($b['created']!=''){
			$created=$b['created'];
			$created=str_replace('-','',$created);
			$created=substr($created,2,6);
			$condition .= " AND `dispatch`.dispatchno LIKE 'DP".$created."%' ";
		}
		if($b['quantity']!=''){
			$quantity=$b['quantity'];
			$rules="AND quantity='".$quantity."'";
			$condition.=$this->findrules(array('rules'=>$rules,'module'=>'dispatch'));
		}
		if($b['finishquantity']!=''){
			$finishquantity=$b['finishquantity'];
			$rules="AND finishquantity='".$finishquantity."'";
			$condition.=$this->findrules(array('rules'=>$rules,'module'=>'dispatch'));
		}
		if($b['workdate']!=''){
			$sd=explode('-',$b['workdate']);
			$workdate=mktime(0,0,0,$sd[1],$sd[2],$sd[0]);
			if($workdate=='')$workdate=0;
			$rules="AND workdate='".$workdate."'";
			$condition.=$this->findrules(array('rules'=>$rules,'module'=>'dispatch'));
		}
		if($b['finishdate']!=''){
			$finishdate=$b['finishdate'];
			$finishdate=str_replace('-','',$finishdate);
			$finishdate=substr($finishdate,2,6);
			$rules="AND batch LIKE '%".$finishdate."'";
			$condition.=$this->findrules(array('rules'=>$rules,'module'=>'dispatch'));
		}
		if($b['workcenterid']!=''){
			$workcenterid=$b['workcenterid'];
			$rules="AND workcenterid='".$workcenterid."'";
			$condition.=$this->findrules(array('rules'=>$rules,'module'=>'dispatch'));
		}
		if($b['materialno']!=''){
			$materialno=$b['materialno'];
			$query="SELECT DISTINCT di.dispatchid
				FROM dispatchitem AS di
				LEFT JOIN material AS m ON (m.materialid=di.materialid)
				WHERE di.killed=0 AND m.materialno LIKE '%".$materialno."%' ";
			$condition.=$this->findrules(array('query'=>$query,'module'=>'dispatch'));
		}
		if($b['materialtitle']!=''){
			$materialtitle=$b['materialtitle'];
			$query="SELECT DISTINCT di.dispatchid
				FROM dispatchitem AS di
				LEFT JOIN material AS m ON (m.materialid=di.materialid)
				WHERE di.killed=0 AND m.title LIKE '%".$materialtitle."%' ";
			$condition.=$this->findrules(array('query'=>$query,'module'=>'dispatch'));
		}
		if($b['dispatchno']!=''){
			$dispatchno=$b['dispatchno'];
			$condition.=" AND `dispatch`.dispatchno LIKE '%".$dispatchno."%'";
		}
		return $condition;
	}
	/**
	 * Requirement Task Dispatch 的 rules
	 * @author creator gaowenfei 2013/11/14 9:31
	 * @access public
	 * @param array $b 参数传递 :module 的 rules
	 * @return string $str 返回 module 的 condition
	 * @throws none 没有异常
	 */
	function findrules($b){
		if($b['query']!=''){
			$rts=$this->DB->query($b['query']);
		}else{
			$rts=$this->DB->query("
				SELECT DISTINCT ".$b['module']."id
				FROM ".$b['module']."item
				WHERE killed=0 ".$b['rules']."
			");
		}
		if($this->DB->numRows()){
			while($rt=$this->DB->fetchArray($rts)){
				$id[]=$rt[$b['module'].'id'];
			}
			$condition.=" AND `".$b['module']."`.".$b['module']."id IN (".implode(',', $id).")";
		}else{
			$condition.=" AND `".$b['module']."`.".$b['module']."id=0";
		}
		return $condition;
	}
	//
	/**
	 * 选择客户区域
	 * @author creator mayinghao 2013-07-1 9:15
	 * @access public
	 * @param array $arr 查询条件
	 * @return string $str 返回客户区域信息
	 * @throws none 没有异常
	 */
	function chooserArea(array $arr){
		if(!isset($arr['orderby'])){
			$arr['orderby'] = 'areaid';
		}
		if(!isset($arr['direction'])){
			$arr['direction'] = 'ASC';
		}
		if($arr['width'] > 0){
			$arr['width'] = ' style="width: '.$arr['width'].'px;"';
		}
		if(isset($arr['condition'])){
			$arr['condition'] = ' AND '.$arr['condition'];
		}
		$trackers = $this->DB->query("
			SELECT *
			FROM area
			WHERE killed=0 ".$arr['condition']."
			ORDER BY ".$arr['orderby']." ".$arr['direction']."
		");
		$str = '<select id="'.$arr['name'].'" name="'.$arr['name'].'"'.$arr['width'].'>';
		if($this->DB->numRows()){
			if($arr['hasBlank']){
				$str .= '<option value="0">'.$arr['topname'].'</option>';
			}
			while($tracker = $this->DB->fetchArray($trackers)){
				if($arr['selectedid'] == $tracker['areaid']){
					$str .= '<option value="'.$tracker['areaid'].'" selected>'.$tracker['title'].$tracker['entitle'].'</option>';
				}else{
					$str .= '<option value="'.$tracker['areaid'].'">'.$tracker['title'].$tracker['entitle'].'</option>';
				}
			}
		}
		$str .= '</select>';
		return $str;
	}

	/**
	 * 生成跟单人员选择框列表
	 * @author creator mayinghao 2013-07-2 9:00
	 * @access public
	 * @param array $arr 生成选择框参数列表
	 * @return string $str 返回跟单人员选择框字符串
	 * @throws none 没有异常
	 */
	function chooserTracker(array $arr){
		if(!isset($arr['orderby'])){
			$arr['orderby'] = 'userid';
		}
		if(!isset($arr['direction'])){
			$arr['direction'] = 'ASC';
		}
		if($arr['width'] > 0){
			$arr['width'] = ' style="width: '.$arr['width'].'px;"';
		}
		if(isset($arr['condition'])){
			$arr['condition'] = ' AND '.$arr['condition'];
		}
		$trackers = $this->DB->query("
			SELECT *
			FROM member
			WHERE killed=0 ".$arr['condition']."
			ORDER BY ".$arr['orderby']." ".$arr['direction']."
		");
		$str = '<select id="'.$arr['name'].'" name="'.$arr['name'].'"'.$arr['width'].'>';
		if($this->DB->numRows()){
			if($arr['hasBlank']){
				$str .= '<option value="0">'.$arr['topname'].'</option>';
			}
			while($tracker = $this->DB->fetchArray($trackers)){
				if($arr['selectedid'] == $tracker['userid']){
					$str .= '<option value="'.$tracker['userid'].'" selected>'.$tracker['username'].'</option>';
				}else{
					$str .= '<option value="'.$tracker['userid'].'">'.$tracker['username'].'</option>';
				}
			}
		}
		$str .= '</select>';
		return $str;
	}

	/**
	 * 生成所属成品多选择列表
	 * @author creator mayinghao 2013-08-07 14:00
	 * @access public
	 * @param array $b 生成选择框参数列表
	 * @return string $r 返回产品或配件信息选择框字符串
	 * @throws none 没有异常
	 */
	function chooserParentProduct($b){
		$parentids=array();
		if(isset($b['parentProduct'])){
			$parentids = explode(',',$b['parentProduct']);
		}
		$products = $this->DB->query("SELECT productid,title,entitle FROM product WHERE attrid='1' ORDER BY productid ASC");
		$str = '';
		if($this->DB->numRows()){
			while($product = $this->DB->fetchArray($products)){
				if(in_array($product['productid'], $parentids) ){
					$str .= '<input type="checkbox" name="parentProduct[]" value="'.$product['productid'].'" checked/>'.$product['title'].'　';
				}else{
					$str .= '<input type="checkbox" name="parentProduct[]" value="'.$product['productid'].'"/>'.$product['title'].'　';
				}
			}
		}
		return $str;
	}

	/**
	 * 生成产品或配件信息选择框列表
	 * @author creator mayinghao 2013-06-20 9:00
	 * @access public
	 * @param array $b 生成选择框参数列表
	 * @return string $r 返回产品或配件信息选择框字符串
	 * @throws none 没有异常
	 */
	function chooserProduct($b){
		if($b['orderby'] == ''){
			$b['orderby'] = '`product`.productno';
		}
		if($b['direction'] == ''){
			$b['direction'] = 'ASC';
		}
		if($b['width'] > 0){
			$b['width'] = ' style="width: '.$b['width'].'px;"';
		}

		$colArr = array(1=>'成品',2=>'半成品',3=>'配件',4=>'试做样');
		$r = '<select id="'.$b['name'].'" name="'.$b['name'].'"'.$b['width'].' onchange="dc.chooseAttribute(this.value,\'td'.$b['selectName'].'\')">';
		if($b['hasBlank']){
			$r .= '<option value="0">'.$b['topname'].'</option>';
		}
		for($i=1;$i<5;$i++){
			$products = $this->DB->query("
				SELECT productid, productno, title
				FROM product
				WHERE killed=0".$b['condition']."
				AND attrid=".$i."
				ORDER BY ".$b['orderby']." ".$b['direction']."
			");
			if($this->DB->numRows()){
				$r .= '<optgroup label="'.$colArr[$i].'">';
				while($product = $this->DB->fetchArray($products)){
					if($b['selectedid'] == $product['productid']){
						$r .= '<option value="'.$product['productid'].'" selected>'.$product['productno'].' '.$product['title'].'</option>';
					}else{
						$r .= '<option value="'.$product['productid'].'">'.$product['productno'].' '.$product['title'].'</option>';
					}
				}
				$r .= '</optgroup>';
			}

		}
		$r .= '</select>';
		return $r;
	}


	/**
	 * 生成配件选择框列表
	 * @author creator mayinghao 2013-08-07 9:00 modifier zhourui 2013-08-07 12:00
	 * @access public
	 * @param array $b 生成选择框参数列表
	 * @return string $r 返回产品或配件信息选择框字符串
	 * @throws none 没有异常
	 */
		function chooserParts($b){
		if($b['width'] > 0){
			$b['width'] = ' style="width: '.$b['width'].'px;"';
		}
		$products = $this->DB->query("
			SELECT productid, productno, title, entitle
			FROM product
			WHERE killed=0 AND attrid='1' ".$b['condition']."
			ORDER BY productid ASC
		");
		$r = '<select id="'.$b['name'].'" name="'.$b['name'].'"'.$b['width'].$onchange.'>';
		if($b['hasBlank']){
			$r .= '<option value="0">'.$b['topname'].'</option>';
		}
		if($this->DB->numRows()){
			while($product = $this->DB->fetchArray($products)){
				$productId=array();
				$r .= '<optgroup label="'.$product['title'].'">';
				$parts = $this->DB->query("
					SELECT productid, productno,materialid,title,entitle,parentProduct
					FROM product
					WHERE killed=0 AND (attrid='3' OR attrid=5)
					ORDER BY `product`.ordering DESC,`product`.productid ASC
				");
				if($this->DB->numRows()){
					while($part = $this->DB->fetchArray($parts)){
						$productId=explode(',',$part['parentProduct']);
						if(in_array($product['productid'],$productId)){
							$material = $this->DB->queryFirst("SELECT materialno,standard FROM material WHERE materialid='".$part['materialid']."'");
							if($part['productid'] == $b['selectedid']){
								$r .= '<option value="'.$part['productid'].'" selected>'.$part['title'].'　'.$material['standard'].'</option>';
							}else{
								$r .= '<option value="'.$part['productid'].'">'.$part['title'].'　'.$material['standard'].'</option>';
							}
						}
					}
				}

				$r .= '</optgroup>';
			}
		}
		$r .= '<optgroup label="公共配件">';
		$publicParts = $this->DB->query("
			SELECT productid, productno,materialid,title,entitle
			FROM product
			WHERE killed=0 AND attrid='3' AND `product`.parentProduct=0
			ORDER BY  `product`.ordering DESC,`product`.productid ASC
		");

		if($this->DB->numRows()){
			while($publicPart = $this->DB->fetchArray($publicParts)){
				$material = $this->DB->queryFirst("SELECT materialno,standard FROM material WHERE materialid='".$publicPart['materialid']."'");
				if($part['selectedid']>0 AND $part['productid'] == $b['selectedid']){
					$r .= '<option value="'.$publicPart['productid'].'" selected>'.$publicPart['title'].'　'.$material['standard'].'</option>';
				}else{
					$r .= '<option value="'.$publicPart['productid'].'">'.$part['title'].'　'.$material['standard'].'</option>';
				}

			}
		}
		$r .= '</optgroup>';
		$r .= '<optgroup label="配件包">';
		$parts = $this->DB->query("
			SELECT productid, productno,title,entitle
			FROM product
			WHERE killed=0 AND attrid='5' AND `product`.parentProduct=0
			ORDER BY  `product`.ordering DESC,`product`.productid ASC
		");

		if($this->DB->numRows()){
			while($part = $this->DB->fetchArray($parts)){
				if($part['productid'] == $b['selectedid']){
					$r .= '<option value="'.$part['productid'].'" selected>'.$part['title'].'</option>';
				}else{
					$r .= '<option value="'.$part['productid'].'">'.$part['title'].'</option>';
				}

			}
		}
		$r .= '</optgroup>';
		$r .= "</select>";
		return $r;
	}

	/**
	 * 生成产品条目信息选择框列表
	 * @author creator mayinghao 2013-07-23 9:00 modifier zhourui 2013-10-14 21:50
	 * @access public
	 * @param array $b 生成选择框参数列表
	 * @return string $r 返回产品或配件信息选择框字符串
	 * @throws none 没有异常
	 */
	function chooserProductItem($b){
		if($b['orderby'] == ''){
			$b['orderby'] = '`product`.ordering';
		}
		if($b['direction'] == ''){
			$b['direction'] = 'DESC';
		}
		if($b['width'] > 0){
			$b['width'] = ' style="width: '.$b['width'].'px;"';
		}
		if($b['showVersion']){
			$onchange=' onchange="javascript:dc.material.version(this.value,'.$b['line'].',\'product\')"';
		}

		if($b['selectName']){
			$onchange=' onchange="dc.getProductEntitle(this.value,\'td'.$b['selectName'].'\')"';
		}
		//$colArr = array(1=>'成品',2=>'半成品',3=>'配件',4=>'试做样');
		$r = '<select id="'.$b['name'].'" name="'.$b['name'].'"'.$b['width'].$onchange.'>';
		if($b['hasBlank']){
			$r.='<option value="0">'.$b['topname'].'</option>';
		}
		$products = $this->DB->query("
			SELECT productid, productno, title, entitle
			FROM product
			WHERE killed=0 AND attrid='1' ".$b['condition']."
			ORDER BY ".$b['orderby']." ".$b['direction']."
		");
		if($this->DB->numRows()){
			while($product = $this->DB->fetchArray($products)){
				$productitems = $this->DB->query("
					SELECT productitemid,productid,attributevalue,ordering
					FROM `productitem` 
					WHERE killed=0 AND `productid`='".$product['productid']."'
					ORDER BY ordering DESC
				");
				if($this->DB->numRows()){
					$r.='<optgroup class="middle bold" label="'.$product['title'].'">';
					while($productitem=$this->DB->fetchArray($productitems)){
						$select='';
						$attributes=$this->DB->query("
								SELECT av.valueid,av.title AS value,
									an.nameid,an.title AS name
								FROM attributevalue AS av
								LEFT JOIN attributename AS an ON (an.nameid=av.nameid)
							WHERE an.killed=0 AND av.killed=0 AND av.valueid IN (".$productitem['attributevalue'].")
							ORDER BY an.ordering DESC
							");
						if($this->DB->numRows()){
							while($attribute=$this->DB->fetchArray($attributes)){
								$select .= '　'.$attribute['name'].'：'.$attribute['value'].'';
							}
						}
						$r.='<option class="normal" value="'.$productitem['productitemid'].'" ';
						if($productitem['productitemid'] == $b['selectedid']){
							$r.='selected';
						}
						$r.='>'.$select.'</option>';
					}
				$r .= '</optgroup>';
			}
		}
		}
		$r .= '</select>';
		return $r;
	}

	//
	function chooserAttr($b){
		if($b['orderby'] == ''){
			$b['orderby'] = 'attrid';
		}
		if($b['direction'] == ''){
			$b['direction'] = 'ASC';
		}
		if($b['module']!=''){
			$module=' AND module="'.$b['module'].'"';
		}
		$attrs = $this->DB->query("
			SELECT *
			FROM attr
			WHERE killed=0 $module
			ORDER BY ".$b['orderby']." ".$b['direction']."
		");
		if($this->DB->numRows()){
			while($attr = $this->DB->fetchArray($attrs)){
				$r .= '<label for="abbr'.$attr['attrid'].'" title="'.$attr['remark'].'"><input type="radio" id="abbr'.$attr['attrid'].'" name="'.$b['name'].'" value="'.$attr['attrid'].'"'.$this->iif($b['selectedid'] == $attr['attrid'],' checked', '').'>'.$attr['title'].'</label>';
			}
		}

		return $r;
	}
	/**
	 * 选择全部的属性
	 * @author creator zhourui 2013-09-27 12:00
	 * @access public
	 * @param array $b 参数传递 ：start 、 perpage
	 * @return string $str 返回bom的list
	 * @throws none 没有异常
	 */
	function chooserAttribute($b){
		if($b['width']!=''){
			$width=' style="width:'.$b['width'].'"';
		}
		$valueid=array();
		//获取产品属性信息
		$names=$this->DB->query("
			SELECT nameid,title
			FROM  attributename
			WHERE killed=0 AND module='".$b['module']."' AND mid='".$b['mid']."'
			ORDER BY nameid ASC
		");

		$r='';
		if($this->DB->numRows($names)){
			$i=1;
			while($name=$this->DB->fetchArray($names)){
				$values=$this->DB->query("
					SELECT valueid,title,`default`
					FROM attributevalue
					WHERE killed=0 AND nameid='".$name['nameid']."'
					ORDER BY valueid ASC
				");
				if($this->DB->numRows($values)){
					$valueid=explode(',',$b['value']);
					$r.='<select name="'.$b['name'].'['.$i.']" '.$width.'><option value="0">'.$name['title'].'</option>';
					while($value=$this->DB->fetchArray($values)){
						$r.='<option value="'.$value['valueid'].'" ';
						if(in_array($value['valueid'],$valueid)){
							$r.='selected';
						}
						$r.=' >'.$value['title'].'</option>';
					}
					$r.='</select>';
				}
				$i++;
			}
		}else{
			$r='暂无属性';
		}
		return $r;
	}
	//
	function chooserBankDetail($b){
		if($b['orderby'] == ''){
			$b['orderby'] = 'ordering';
		}
		if($b['direction'] == ''){
			$b['direction'] = 'ASC';
		}
		if($b['width'] > 0){
			$b['width'] = ' style="width: '.$b['width'].'px;"';
		}
		if($b['customerid']>0){
			$condition=' customerid='.$b['customerid'];
		}
		if($b['supplierid']>0){
			$condition=' supplierid='.$b['supplierid'];
		}
		$bankDetails=$this->DB->query("
			SELECT *
			FROM bankdetail
			WHERE killed=0 AND ".$condition."
			ORDER BY ".$b['orderby']." ".$b['direction']."
		");
		$r = '<select id="'.$b['name'].'" name="'.$b['name'].'"'.$b['width'].'>';
		if($this->DB->numRows()){
			if($b['hasBlank']){
				$r .= '<option value="0">'.$b['topname'].'</option>';
			}
			while($bankDetail=$this->DB->fetchArray($bankDetails)){
				$r.='<option value="'.$bankDetail['bankDetailid'].'"';
				if($b['selectedid']==$bankDetail['bankDetailid']){
					$r.=' selected';
				}
				$r.='>'.$bankDetail['bank'].'（'.$bankDetail['bankAccountName'].':'.$bankDetail['bankAccount'] .'）</option>';
			}
		}
		$r.='</select>';

		return $r;
	}
	//
	function chooserBrand($b){
		if($b['orderby'] == ''){
			$b['orderby'] = 'ordering';
		}
		if($b['direction'] == ''){
			$b['direction'] = 'ASC';
		}
		if($b['width'] > 0){
			$b['width'] = ' style="width: '.$b['width'].'px;"';
		}

		$brands = $this->DB->query("
			SELECT *
			FROM brand
			WHERE killed=0
			ORDER BY ".$b['orderby']." ".$b['direction']."
		");
		$r = '<select id="'.$b['name'].'" name="'.$b['name'].'"'.$b['width'].'>';
		if($this->DB->numRows()){
			if($b['hasBlank']){
				$r .= '<option value="0">'.$b['topname'].'</option>';
			}
			while($brand = $this->DB->fetchArray($brands)){
				if($b['selectedid'] == $brand['brandid']){
					$r .= '<option value="'.$brand['brandid'].'" selected>'.$brand['title'].'</option>';
				}else{
					$r .= '<option value="'.$brand['brandid'].'">'.$brand['title'].'</option>';
				}
			}
		}
		$r .= '</select>';

		return $r;
	}
	//
	function chooserBatch($b){
		if($b['orderby']==''){
			$b['orderby']='arrivalTime';
		}
		if($b['direction']==''){
			$b['direction']='ASC';
		}
		if($b['width']>0){
			$b['width']=' style="width: '.$b['width'].'px;"';
		}
		if($b['versionid']>0){
			$version=' AND b.versionid like "%,'.$b['versionid'].',%"';
		}
		$batchs=$this->DB->query("
			SELECT b.*
			FROM batch AS b
			WHERE b.killed=0 AND b.materialid='".$b['materialid']."' $version
			ORDER BY ".$b['orderby']." ".$b['direction']."
		");
		$r='<select id="'.$b['name'].'" name="'.$b['name'].'"'.$b['width'].'>';
		if($this->DB->numRows()){
			if($b['hasBlank']){
				$r.= '<option value="0">'.$b['topname'].'</option>';
			}
			while($batch=$this->DB->fetchArray($batchs)){
				$r.='<option value="'.$batch['batchid'].'"';
				if($b['selectedid']==$batch['batchid']){
					$r.=' selected';
				}
				$r.= '>'.$batch['batchno'].' (当前数量为：'.$batch['quantity'].' )</option>';
			}
		}
		$r.= '</select>';

		return $r;
	}
	//
	function chooserBarcode($b){
		if($b['orderby']==''){
			$b['orderby']='arrivalTime';
		}
		if($b['direction']==''){
			$b['direction']='ASC';
		}
		if($b['width']>0){
			$b['width']=' style="width: '.$b['width'].'px;"';
		}
		if($b['condition']==''){
			$b['condition']=' 1=1';
		}
		if($b['ifPerBarcode']==1){
			$b['status']=' 1=1';
		}else{
			if($b['status']==''){
				$b['status']=' 1=1';
			}
		}
		if($b['parentid']==''){
			$b['parentid']=0;
		}
		if($b['versionid']>0){
			$version=' AND versionid='.$b['versionid'];
		}
		$usedId=$usedQuantity=array();
		if($b['ifMinus']==1){// 去掉订单和样品单的条码或数量
			$isUseds=$this->DB->query("
				SELECT inbounditem.barcodeid,SUM(inbounditem.quantity) AS quantity
				FROM inbounditem
				LEFT JOIN inbound ON (inbound.inboundid=inbounditem.inboundid)
				WHERE inbounditem.killed=0 AND inbound.killed=0 AND (inbound.orderid<>0 OR inbound.sampleid<>0) $version
				GROUP BY barcodeid
			");
			if($this->DB->numRows()){
				while($isUsed=$this->DB->fetchArray($isUseds)){
					$outItem=$this->DB->queryFirst("
						SELECT SUM(outbounditem.outQuantity) AS quantity 
						FROM outbounditem
						LEFT JOIN outbound ON (outbound.outboundid=outbounditem.outboundid)
						WHERE outbounditem.killed=0 AND outbound.killed=0 AND outbounditem.barcodeid={$isUsed['barcodeid']}  AND (outbound.orderid<>0 OR outbound.sampleid<>0)
					");
					$usedId[]=$isUsed['barcodeid'];
					$usedQuantity[$isUsed['barcodeid']]=$isUsed['quantity']-$outItem['quantity'];	
				}
			}
		}
		if($b['ifQualified']==1){
			$quantityCondition='AND qualified>0 ';
		}elseif($b['ifQualified']==0){
			$quantityCondition='AND disqualified>0 ';
		}

		$barcodes=$this->DB->query("
			SELECT *
			FROM barcode
			WHERE parentid='".$b['parentid']."' AND ".$b['status']." $version $quantityCondition AND ".$b['condition']."
			ORDER BY ".$b['orderby']." ".$b['direction']."
		");
		$r='<select id="'.$b['name'].'" name="'.$b['name'].'"'.$b['width'].'>';
		if($b['hasBlank']){
			$r.= '<option value="0">'.$b['topname'].'</option>';
		}
		if($this->DB->numRows()){
			while($barcode=$this->DB->fetchArray($barcodes)){
				$barcode['arrivalTime']=date('Y-m-d',$barcode['arrivalTime']);
				if($b['ifMinus']==1){
					if(in_array($barcode['barcodeid'],$usedId)){
						if($barcode['qualified']>$usedQuantity[$barcode['barcodeid']]){
							$r.= '<option value="'.$barcode['barcodeid'].'" ';
							if($b['selectedid']==$barcode['barcodeid']){
								$r.='selected';
							}
							$r.='>'.$barcode['barcode'].' （批次时间：'.$barcode['arrivalTime'].$this->iif($b['ifQualified']==1,' 良：'.($barcode['qualified']-$usedQuantity[$barcode['barcodeid']]),' 不良：'.$barcode['disqualified']).'）';
							$r.='</option>';
						}
					}else{
						$r.= '<option value="'.$barcode['barcodeid'].'" ';
						if($b['selectedid']==$barcode['barcodeid']){
							$r.='selected';
						}
						$r.='>'.$barcode['barcode'].' （批次时间：'.$barcode['arrivalTime'].$this->iif($b['ifQualified']==1,' 良：'.$barcode['qualified'],' 不良：'.$barcode['disqualified']).'）';
						$r.='</option>';
					}
				}else{
					$r.= '<option value="'.$barcode['barcodeid'].'" ';
					if($b['selectedid']==$barcode['barcodeid']){
						$r.='selected';
					}
					$r.='>'.$barcode['barcode'].' （批次时间：'.$barcode['arrivalTime'].$this->iif($b['ifQualified']==1,' 良：'.$barcode['qualified'],' 不良：'.$barcode['disqualified']).'）';
					$r.='</option>';
				}

				/* 暂时保留
				if($b['ifPerBarcode']==1){
					$perBarcodes=$this->DB->query("
						SELECT *
						FROM barcode
						WHERE parentid='".$barcode['barcodeid']."' AND statusid=2
						ORDER BY ".$b['orderby']." ".$b['direction']."
					");
					if($this->DB->numRows()){
						while($perBarcode=$this->DB->fetchArray($perBarcodes)){
							$r.='<option value="'.$barcode['barcodeid'].'">　　'.$perBarcode['barcode'].'</option>';
						}
					}
				}*/
			}
		}
		$r.= '</select>';

		return $r;
	}
	//
	function chooserBusinesstype($b){
		if($b['orderby'] == ''){
			$b['orderby'] = 'ordering';
		}
		if($b['direction'] == ''){
			$b['direction'] = 'ASC';
		}
		if($b['width'] > 0){
			$b['width'] = ' style="width: '.$b['width'].'px;"';
		}

		$businesstypes = $this->DB->query("
			SELECT *
			FROM businesstype
			WHERE killed=0
			ORDER BY ".$b['orderby']." ".$b['direction']."
		");
		$r = '<select id="'.$b['name'].'" name="'.$b['name'].'"'.$b['width'].'>';
		if($this->DB->numRows()){
			if($b['hasBlank']){
				$r .= '<option value="0">'.$b['topname'].'</option>';
			}
			while($businesstype = $this->DB->fetchArray($businesstypes)){
				if($b['selectedid'] == $businesstype['businesstypeid']){
					$r .= '<option value="'.$businesstype['businesstypeid'].'" selected>'.$businesstype['title'].' ('.$businesstype['entitle'].')</option>';
				}else{
					$r .= '<option value="'.$businesstype['businesstypeid'].'">'.$businesstype['title'].' ('.$businesstype['entitle'].')</option>';
				}
			}
		}
		$r .= '</select>';

		return $r;
	}
	//
	function chooserCategory($c){
		if($c['categoryid']>0){
			$condition = "categoryid='".$c['categoryid']."'";
			$category = $this->DB->queryFirst("
				SELECT categoryid, title, entitle, display
				FROM `category`
				WHERE $condition
				ORDER BY ordering ASC
			");
			if($category){
				if($c['showall'] == 1){
					$r .= '<option'.$this->iif($category['display']==0, ' style="color: gray;"', '').' value="'.$category['categoryid'].'"'.$this->iif($c['selectedid']==$c['categoryid'], ' selected', '').'>'.$c['depth'] . $category['title'].$this->iif($category['entitle']!='', ' ('.$category['entitle'].')', '').'</option>';
				}else{
					if($category['display'] == 1){
						$r .= '<option'.$this->iif($category['display']==0, ' style="color: gray;"', '').' value="'.$category['categoryid'].'"'.$this->iif($c['selectedid']==$c['categoryid'], ' selected', '').'>'.$c['depth'].$category['title'].$this->iif($category['entitle']!='', ' ('.$category['entitle'].')', '').'</option>';
					}
				}
			}
			$c['depth'] .= "　　";
		}else{
			$r .= '<select id="'.$c['name'].'" name="'.$c['name'].'"'.$this->iif($c['width'] > 0, ' style="width:'.$c['width'].'px;"', '').$this->iif($c['size'] > 0, ' size="'.$c['size'].'"', '').'>';
			if($c['hasBlank'])$r .= '<option value="-1"'.$this->iif($c['selectedid']==$c['categoryid'], ' selected', '').'>'.$c['topname'].'</option>';
			$c['categoryid'] = -1;
		}
		if($c['parentid']>0){
			$c['categoryid'] = $c['parentid'];
		}
		$pcondition = "parentid = '".$c['categoryid']."'";
		$categorys=$this->DB->query("
			SELECT categoryid
			FROM `category`
			WHERE $pcondition
			ORDER BY ordering ASC
			");
		if($this->DB->numRows()){
			while ($category = $this->DB->fetchArray($categorys)){
				$r.=$this->chooserCategory(array(
					'selectedid' => $c['selectedid'],
					'categoryid' => $category['categoryid'],
					'depth' => $c['depth'],
					'showall' => $c['showall'],
				));
			}
		}
		if($c['categoryid'] == 0){
			$r .= '</select>';
		}

		return $r;
	}
	//
	function chooserCountry($b){
		if($b['orderby'] == ''){
			$b['orderby'] = 'encountry';
		}
		if($b['direction'] == ''){
			$b['direction'] = 'ASC';
		}
		if($b['width'] > 0){
			$b['width'] = ' style="width: '.$b['width'].'px;"';
		}

		$countrys = $this->DB->query("
			SELECT *
			FROM region
			WHERE killed=0 AND countryid=0
			ORDER BY ".$b['orderby']." ".$b['direction']."
		");
		$r = '<select id="'.$b['name'].'" name="'.$b['name'].'"'.$b['width'].'>';
		if($this->DB->numRows()){
			if($b['hasBlank']){
				$r .= '<option value="0">'.$b['topname'].'</option>';
			}
			while($country = $this->DB->fetchArray($countrys)){
				$f = strtoupper(substr($country['encountry'], 0, 1));
				if($b['selectedid'] == $country['regionid']){
					$r .= '<option value="'.$country['regionid'].'" selected>'.$f.' ' . $country['country'] . $this->iif($b['cn'] == 1, '', '('.$country['encountry'].')') . '</option>';
				}else{
					$r .= '<option value="'.$country['regionid'].'"'.$this->iif($country['regionid']==$b['regionid'], ' selected', '').'>'.$f.' '.$country['country'].$this->iif($b['cn'] == 1, '', '('.$country['encountry'].')') . '</option>';
				}
			}
		}
		$r .= '</select>';

		return $r;
	}

	//
	function chooserCurrency($b){
		if($b['orderby'] == ''){
			$b['orderby'] = 'currencyid';
		}
		if($b['direction'] == ''){
			$b['direction'] = 'ASC';
		}
		if($b['width'] > 0){
			$b['width'] = ' style="width: '.$b['width'].'px;"';
		}

		$currencys = $this->DB->query("
			SELECT *
			FROM currency
			WHERE killed=0
			ORDER BY ".$b['orderby']." ".$b['direction']."
		");
		$r = '<select id="'.$b['name'].'" name="'.$b['name'].'"'.$b['width'].'>';
		if($this->DB->numRows()){
			if($b['hasBlank']){
				$r .= '<option value="0">'.$b['topname'].'</option>';
			}
			while($currency = $this->DB->fetchArray($currencys)){
				if($b['selectedid'] == $currency['currencyid']){
					$r .= '<option value="'.$currency['currencyid'].'" selected>'.$currency['title'].'('.$currency['symbol'].')</option>';
				}else{
					$r .= '<option value="'.$currency['currencyid'].'">'.$currency['title'].'('.$currency['symbol'].')</option>';
				}
			}
		}
		$r .= '</select>';

		return $r;
	}

	//
	function chooserCustomer($b){
		if($b['orderby'] == ''){
			$b['orderby'] = 'title';
		}
		if($b['direction'] == ''){
			$b['direction'] = 'ASC';
		}
		if($b['width'] > 0){
			$b['width'] = ' style="width: '.$b['width'].'px;"';
		}

		/*if(isset($b['onchange'])){
			$b['onchange'] = 'onchange="dc.returnCountryAndCustomerStr(this.value,\''.$b['onchange'].'\')"';
		}*/
		if($b['width'] > 0){
			$b['width']=' style="width:'.$b['width'].'px;"';
		}
		if($b['showVersion']==1){
			$b['showVersion']=',1';
		}
		$line=$this->iif($b['line']==='','\'\'',$b['line']);
		$param="{'obj':this,'module':'customer','onchange':'".$b['onchange']."','line':'".$line."'}";
        
		return '<input type="hidden" id="'.$this->iif($b['hId'],$b['hId'],$b['hName']).'" name="'.$b['hName'].'" value="'.$this->iif($b['selectedid'],$b['selectedid'],0).'" />
		<input type="text" name="'.$b['name'].'" id="'.$this->iif($b['id'],$b['id'],$b['name']).'" '.$b['width'].' onfocus="javascript:dc.autoComplete('.$param.')" value="'.$b['value'].'" />';
           
		/*$customers = $this->DB->query("
			SELECT *
			FROM customer
			WHERE killed=0
			ORDER BY ".$b['orderby']." ".$b['direction']."
		");
		$r = '<select id="'.$b['name'].'" name="'.$b['name'].'"'.$b['width'].' '.$b['onchange'].'>';
		if($this->DB->numRows()){
			if($b['hasBlank']){
				$r .= '<option value="0">'.$b['topname'].'</option>';
			}
			while($customer = $this->DB->fetchArray($customers)){
				$title=$this->iif($b['lan']=='cn',$customer['cntitle'],$customer['title']);
				if($b['selectedid'] == $customer['customerid']){
					$r .= '<option value="'.$customer['customerid'].'" selected>'.$title.'</option>';
				}else{
					$r .= '<option value="'.$customer['customerid'].'">'.$title.'</option>';
				}
			}
		}
		$r .= '</select>';

		return $r;*/
	}

	//
	function chooserCustomerSource($b){
		if($b['orderby'] == ''){
			$b['orderby'] = 'ordering';
		}
		if($b['direction'] == ''){
			$b['direction'] = 'ASC';
		}
		if($b['width'] > 0){
			$b['width'] = ' style="width: '.$b['width'].'px;"';
		}

		$customersources = $this->DB->query("
			SELECT *
			FROM customersource
			WHERE killed=0
			ORDER BY ".$b['orderby']." ".$b['direction']."
		");
		$r = '<select id="'.$b['name'].'" name="'.$b['name'].'"'.$b['width'].'>';
		if($this->DB->numRows()){
			if($b['hasBlank']){
				$r .= '<option value="0">'.$b['topname'].'</option>';
			}
			while($customersource = $this->DB->fetchArray($customersources)){
				if($b['selectedid'] == $customersource['customersourceid']){
					$r .= '<option value="'.$customersource['customersourceid'].'" selected>'.$customersource['title'].' ('.$customersource['entitle'].')</option>';
				}else{
					$r .= '<option value="'.$customersource['customersourceid'].'">'.$customersource['title'].' ('.$customersource['entitle'].')</option>';
				}
			}
		}
		$r .= '</select>';

		return $r;
	}
	//
	function chooserCustomerSources($b){
		if($b['orderby'] == ''){
			$b['orderby'] = 'ordering';
		}
		if($b['direction'] == ''){
			$b['direction'] = 'ASC';
		}
		if($b['width'] > 0){
			$b['width'] = ' style="width: '.$b['width'].'px;"';
		}
		if($b['selectedid'] != ''){
			$id = explode(',',$b['selectedid']);
		}
		$customersources = $this->DB->query("
			SELECT *
			FROM customersource
			WHERE killed=0
			ORDER BY ".$b['orderby']." ".$b['direction']."
		");
		if($this->DB->numRows()){
			$i=1;
			while($customersource = $this->DB->fetchArray($customersources)){
				$r .= '<label for="stype'.$customersource['customersourceid'].'"><input type="checkbox" id="stype'.$customersource['customersourceid'].'" name="'.$b['name'].'['.$i.']" value="'.$customersource['customersourceid'].'"';
				if(is_array($id) AND in_array($customersource['customersourceid'], $id)){
					$r .= ' checked';
				}
				$r .= ' />'.$customersource['title'].'</label>	';
				$i++;
				if($i%7==0){
					$r.="<br/>";
				}
			}
		}

		return $r;
	}
	//
	function chooserCustomerDomain($b){
		if($b['orderby'] == ''){
			$b['orderby'] = 'ordering';
		}
		if($b['direction'] == ''){
			$b['direction'] = 'ASC';
		}
		if($b['width'] > 0){
			$b['width'] = ' style="width: '.$b['width'].'px;"';
		}

		$customerdomains = $this->DB->query("
			SELECT *
			FROM customerdomain
			WHERE killed=0
			ORDER BY ".$b['orderby']." ".$b['direction']."
		");
		$r = '<select id="'.$b['name'].'" name="'.$b['name'].'"'.$b['width'].'>';
		if($this->DB->numRows()){
			if($b['hasBlank']){
				$r .= '<option value="0">'.$b['topname'].'</option>';
			}
			while($customerdomain = $this->DB->fetchArray($customerdomains)){
				if($b['selectedid'] == $customerdomain['customerdomainid']){
					$r .= '<option value="'.$customerdomain['customerdomainid'].'" selected>'.$customerdomain['title'].' ('.$customerdomain['entitle'].')</option>';
				}else{
					$r .= '<option value="'.$customerdomain['customerdomainid'].'">'.$customerdomain['title'].' ('.$customerdomain['entitle'].')</option>';
				}
			}
		}
		$r .= '</select>';

		return $r;
	}
	//
	function chooserDeliveryterm($b){
		if($b['orderby'] == ''){
			$b['orderby'] = 'ordering';
		}
		if($b['direction'] == ''){
			$b['direction'] = 'ASC';
		}
		if($b['width'] > 0){
			$b['width'] = ' style="width: '.$b['width'].'px;"';
		}

		$deliverys = $this->DB->query("
			SELECT *
			FROM deliveryterm
			WHERE killed=0
			ORDER BY ".$b['orderby']." ".$b['direction']."
		");
		$r = '<select id="'.$b['name'].'" name="'.$b['name'].'"'.$b['width'].'>';
		if($this->DB->numRows()){
			if($b['hasBlank']){
				$r .= '<option value="0">'.$b['topname'].'</option>';
			}
			while($delivery = $this->DB->fetchArray($deliverys)){
				$r .= '<option value="'.$delivery['deliverytermid'].'"'.$this->iif($b['selectedid'] == $delivery['deliverytermid'], ' selected', '').'>'.$delivery['title'].' ('.$delivery['entitle'].')</option>';
			}
		}
		$r .= '</select>';

		return $r;
	}
	/*
	*		choose department
	*		@param array(orderby,direction,width,name,topname,hasBlank,selectedid)
	*		return html-select
	*/
	function chooserDepartment($c){
		if($c['departmentid']>0){
			$condition = "departmentid='".$c['departmentid']."'";
			$department = $this->DB->queryFirst("
				SELECT *
				FROM department
				WHERE $condition
				ORDER BY ordering ASC
			");
			if($department){
				if($c['showall'] == 1){
					$r .= '<option'.$this->iif($department['display']==0, ' style="color: gray;"', '').' value="'.$department['departmentid'].'"'.$this->iif($c['selectedid']==$c['departmentid'], ' selected', '').'>'.$c['depth'] . $department['title'].$this->iif($department['entitle']!='', ' ('.$department['entitle'].')', '').'</option>';
				}else{
					if($department['display'] == 1){
						$r .= '<option'.$this->iif($department['display']==0, ' style="color: gray;"', '').' value="'.$department['departmentid'].'"'.$this->iif($c['selectedid']==$c['departmentid'], ' selected', '').'>'.$c['depth'].$department['title'].$this->iif($department['entitle']!='', ' ('.$department['entitle'].')', '').'</option>';
					}
				}
			}
			$c['depth'] .= "　　";
		}else{
			$r .= '<select id="'.$c['name'].'" name="'.$c['name'].'"'.$this->iif($c['width'] > 0, ' style="width:'.$c['width'].'px;"', '').$this->iif($c['size'] > 0, ' size="'.$c['size'].'"', '').'>';
			if($c['hasBlank'])$r .= '<option value=""'.$this->iif($c['selectedid']==$c['departmentid'], ' selected', '').'>'.$c['topname'].'</option>';
			$c['departmentid'] = -1;
		}
		if($c['parentid']>0){
			$c['departmentid'] = $c['parentid'];
		}
		$pcondition = "parentid = '".$c['departmentid']."'";
		$departments=$this->DB->query("
			SELECT departmentid
			FROM department
			WHERE $pcondition
			ORDER BY ordering ASC
		");
		if($this->DB->numRows()){
			while ($department = $this->DB->fetchArray($departments)){
				$r .= $this->chooserDepartment(array(
					'selectedid' => $c['selectedid'],
					'departmentid' => $department['departmentid'],
					'depth' => $c['depth'],
					'showall' => $c['showall'],
				));
			}
		}
		if($c['departmentid'] == 0){
			$r .= '</select>';
		}

		return $r;
	}
	// chooser entity type. like material, stock. For attribute
	function chooserEntityType($c){
		if($c['typeid']>0){
			$condition="typeid='".$c['typeid']."'";
			$type=$this->DB->queryFirst("
				SELECT typeid, title, entitle, display
				FROM `entitytype`
				WHERE $condition
				ORDER BY ordering ASC
			");
			if($type){
				if($c['showall'] == 1){
					$r .= '<option'.$this->iif($type['display']==0, ' style="color: gray;"', '').' value="'.$type['typeid'].'"'.$this->iif($c['selectedid']==$c['typeid'], ' selected', '').'>'.$c['depth'] . $type['title'].$this->iif($type['entitle']!='', ' ('.$type['entitle'].')', '').'</option>';
				}else{
					if($type['display'] == 1){
						$r .= '<option'.$this->iif($type['display']==0, ' style="color: gray;"', '').' value="'.$type['typeid'].'"'.$this->iif($c['selectedid']==$c['typeid'], ' selected', '').'>'.$c['depth'].$type['title'].$this->iif($type['entitle']!='', ' ('.$type['entitle'].')', '').'</option>';
					}
				}
			}
			$c['depth'] .= "　　";
		}else{
			$r .= '<select id="'.$c['name'].'" name="'.$c['name'].'"'.$this->iif($c['width'] > 0, ' style="width:'.$c['width'].'px;"', '').$this->iif($c['size'] > 0, ' size="'.$c['size'].'"', '').'>';
			if($c['hasBlank'])$r .= '<option value="0"'.$this->iif($c['selectedid']==$c['typeid'], ' selected', '').'>'.$b['topname'].'</option>';
			$c['typeid'] = 0;
		}
		$pcondition = "parentid = '".$c['typeid']."'";
		$types=$this->DB->query("
			SELECT typeid
			FROM `entitytype`
			WHERE $pcondition
			ORDER BY ordering ASC
		");
		if($this->DB->numRows()){
			while ($type=$this->DB->fetchArray($types)){
				$r.=$this->chooserEntityType(array(
					'selectedid'=>$c['selectedid'],
					'typeid'=>$type['typeid'],
					'depth'=>$c['depth'],
					'showall'=>$c['showall'],
				));
			}
		}
		if($c['typeid']==0){
			$r .= '</select>';
		}

		return $r;
	}
	//
	function chooserFee($b){
		if($b['orderby'] == ''){
			$b['orderby'] = 'ordering';
		}
		if($b['direction'] == ''){
			$b['direction'] = 'ASC';
		}
		if($b['width'] > 0){
			$b['width'] = ' style="width: '.$b['width'].'px;"';
		}

		$deliverys = $this->DB->query("
			SELECT *
			FROM fee
			WHERE killed=0
			ORDER BY ".$b['orderby']." ".$b['direction']."
		");
		$r = '<select id="'.$b['name'].'" name="'.$b['name'].'"'.$b['width'].'>';
		if($this->DB->numRows()){
			if($b['hasBlank']){
				$r .= '<option value="0">'.$b['topname'].'</option>';
			}
			while($delivery = $this->DB->fetchArray($deliverys)){
				$r .= '<option value="'.$delivery['feeid'].'"'.$this->iif($b['selectedid'] == $delivery['feeid'], ' selected', '').'>'.$delivery['title'].' ('.$delivery['entitle'].')</option>';
			}
		}
		$r .= '</select>';

		return $r;
	}
	/*
	*		choose material
	*		@param array(orderby,direction,width,name,topname,hasBlank,selectedid)
	*		return html-select
	*/
	function chooserMaterial($b){
		if($b['width'] > 0){
			$b['width']=' style="width:'.$b['width'].'px;"';
		}

		$line=$this->iif($b['line']==='','',$b['line']);
		$param="{'obj':this,'module':'material','s':'".$b['showVersion']."','line':'".$line."'}";
		return '<input type="hidden" name="materialItemid'.$this->iif($b['line']>0,'['.$b['line'].']','').'" id="materialItemid'.$b['line'].'" value="'.$this->iif($b['selectItemid'],$b['selectItemid'],0).'" /><input type="hidden" id="'.$this->iif($b['hId'],$b['hId'],$b['hName']).'" name="'.$b['hName'].'" value="'.$this->iif($b['selectedid'],$b['selectedid'],0).'" />
		<input type="text" name="'.$b['name'].'" id="'.$this->iif($b['id'],$b['id'],$b['name']).'" '.$b['width'].' onfocus="javascript:dc.autoComplete('.$param.')" value="'.$b['value'].'" />';
	}
	/**
	 * 条目的明细
	 * @author creator zhourui 2013-12-19 16:08:19
	 * @access public
	 * @param array $b 生成选择框参数列表
	 * @return string $r 返回物资明细 即产成品包成品信息选择框字符串
	 * @throws none 没有异常
	 */
	function chooserMaterialItem($b){
		if($b['orderby'] == ''){
			$b['orderby'] = 'mi.ordering';
		}
		if($b['direction'] == ''){
			$b['direction'] = ' ASC';
		}
		if($b['width'] > 0){
			$b['width'] = ' style="width: '.$b['width'].'px;"';
		}
		if($b['showVersion']){
			$onchange=' onchange="javascript:dc.material.version(this.value,'.$b['line'].',\'materialitem\')"';
		}
		if($b['materialid']){
			$b['materialid']=' AND mi.materialid='.$b['materialid'];
		}
		if($b['productid']){
			$b['productid']=' AND mi.productid='.$b['productid'];
		}
		if($b['productitemid']){
			$b['productitemid']=' AND mi.productitemid='.$b['productitemid'];
		}
		if($b['attrid']){
			$b['attrid']=' AND m.attrid='.$b['attrid'];
		}
		$r = '<select id="'.$this->iif($b['id'],$b['id'],$b['name']).'" name="'.$b['name'].'"'.$b['width'].$onchange.'>';
		if($b['hasBlank']){
			$r .= '<option value="0">'.$b['topname'].'</option>';
		}
		$items=$this->DB->query("
			SELECT mi.itemid,mi.itemno,mi.materialid,mi.attributevalue,
				m.materialno,m.title AS material
			FROM `materialitem` AS mi
			LEFT JOIN `material` AS m ON (m.materialid = mi.materialid)
			WHERE m.killed=0 AND mi.killed=0 {$b['materialid']} {$b['productid']} {$b['attrid']} {$b['productitemid']}
			ORDER BY ".$b['orderby'].$b['direction']."
		");
		if($this->DB->numRows()){
			while($item=$this->DB->fetchArray($items)){
				$selectStr=$this->iif($item['itemno']!='',$item['itemno'],$item['materialno']).'　'.$item['material'];
				$valueId=explode(",",$item['attributevalue']);
				foreach($valueId as $key=>$val){
					$attribute=$this->DB->queryFirst("
						SELECT an.nameid,an.title AS name,
							av.valueid,av.title AS value
						FROM  attributename AS an
						LEFT JOIN attributevalue AS av ON (av.nameid=an.nameid)
						WHERE an.killed=0 AND an.hidden=0 AND av.killed=0 AND av.valueid='".$val."'
						ORDER BY an.ordering ASC
					");
					if($attribute['name'] AND $attribute['value']){
						$selectStr.='　'.$attribute['name'].'：'.$attribute['value'];
					}
				}
				$r.='<option value="'.$item['itemid'].'" ';
				if($b['selectedid'] == $item['itemid']){
					$r.=' selected ';
				}
				$r.=' >'.$selectStr.'</option>';
			}
		}
		$r .= '</select>';
		return $r;
	}
	/*
	*		choose origin of material
	*		@param array(orderby,direction,width,name,topname,hasBlank,selectedid)
	*		return html-select
	*/
	function chooserMaterialOrigin($b){
		if($b['orderby']==''){
			$b['orderby']='ordering';
		}
		if($b['direction']==''){
			$b['direction']='ASC';
		}
		if($b['width'] > 0){
			$b['width']=' style="width: '.$b['width'].'px;"';
		}

		$origins=$this->DB->query("
			SELECT originid,title
			FROM materialorigin
			WHERE killed=0
			ORDER BY ".$b['orderby']." ".$b['direction']."
		");
		if($this->DB->numRows()){
			while($origin=$this->DB->fetchArray($origins)){
				$r.='<input type="radio" name="'.$b['name'].'" value="'.$origin['originid'].'"'.$this->iif($b['selectedid']==$origin['originid'], ' checked', '').'>'.$origin['title'];
			}
		}
		
		return $r;
	}
	//
	function chooserMaterialVersion($b){
		if(intval($b['materialid'])<=0){
			return false;
		}else{
			$condition.=' AND mv.materialid='.$b['materialid'].$this->iif($b['itemid']>0,' AND mv.itemid='.$b['itemid'],'');
		}
		if($b['orderby'] == ''){
			$b['orderby'] = 'mv.versionid';
		}
		if($b['direction'] == ''){
			$b['direction'] = 'DESC';
		}
		if($b['width'] > 0){
			$b['width'] = ' style="width: '.$b['width'].'px;"';
		}
		if($b['versionid']>0){
			$condition.=' AND mv.versionid='.$b['versionid'];
		}

		$versions = $this->DB->query("
			SELECT mv.versionid,mv.title,mv.change,mv.qualified,mv.disqualified,
				u.title AS unit
			FROM materialversion AS mv
			LEFT JOIN material AS m ON (m.materialid=mv.materialid)
			LEFT JOIN unit AS u ON (u.unitid=m.unitid)
			WHERE mv.killed=0 ".$condition."
			ORDER BY ".$b['orderby']." ".$b['direction']."
		");
		if($b['input']){
			if($this->DB->numRows()){
				$i=1;
				while($version=$this->DB->fetchArray($versions)){
					$r.= $this->iif($version['change']=='',$version['title'],'<a class="tip" href="#" title="( '.$version['change'].' )">'.$version['title'].$this->iif($version['itemno']!='',' ('.$version['itemno'].') ','').'</a>').'　需求数：<input type="hidden" name="'.$b['name'].'['.$i.']" value="'.$version['versionid'].'"><input type="text" id="'.$this->iif($b['id'],$b['id'],$b['qname']).'" name="'.$b['qname'].'['.$i.']" size="'.$b['size'].'" ';
					if($i==1){
						$r.='value="'.$b['value'].'"';
					}else{
						$r.='value="0"';
					}
					$r.='></input>　良：'.$version['qualified'].' '.$version['unit'].'　不良：'.$version['disqualified'].' '.$version['unit'].'<br>';
					$i++;
				}
			}
		}else{
			$r = '<select id="'.$this->iif($b['id'],$b['id'],$b['name']).'" name="'.$b['name'].'"'.$b['width'].'>';
			if($this->DB->numRows()){
				if($b['hasBlank']){
					$r .= '<option value="0">'.$b['topname'].'</option>';
				}
				while($version=$this->DB->fetchArray($versions)){
					$r.='<option value="'.$version['versionid'].'"';
					if($b['selectedid'] == $version['versionid']){
						$r.=' selected';
					}
					$r.='>'.$version['title'].'　良：'.$version['qualified'].' '.$version['unit'].'　不良：'.$version['disqualified'].' '.$version['unit'].'</option>';
				}
			}
			$r .= '</select>';
		}
		return $r;
	}
	/*
	*		choose type of inbound or outbound
	*		@param array(orderby,direction,width,condition,name,topname,hasBlank,selectedid)
	*		return html-select
	*/
	function chooserInorouttype($b){
		if($b['orderby']==''){
			$b['orderby']='ordering';
		}
		if($b['direction']==''){
			$b['direction']='ASC';
		}
		if($b['width'] > 0){
			$b['width']=' style="width: '.$b['width'].'px;"';
		}

		$module=$this->iif($b['module']," AND module='{$b['module']}'",'');

		$inOrOutTypes=$this->DB->query("
			SELECT *
			FROM inorouttype
			WHERE killed=0 {$module} {$b['condition']}
			ORDER BY ".$b['orderby']." ".$b['direction']."
		");
		$r='<select id="'.$b['name'].'" name="'.$b['name'].'"'.$b['width'].'>';
		if($this->DB->numRows()){
			if($b['hasBlank']){
				$r.='<option value="0">'.$b['topname'].'</option>';
			}
			while($inOrOutType=$this->DB->fetchArray($inOrOutTypes)){
				$r.='<option value="'.$inOrOutType['typeid'].'"'.$this->iif($b['selectedid']==$inOrOutType['typeid'], ' selected', '').'>'.$inOrOutType['title'].'</option>';
			}
		}
		$r.='</select>';

		return $r;
	}
	/*
	*		choose note type of inbound or outbound
	*		@param array(orderby,direction,width,module,name,topname,hasBlank,selectedid)
	*		return html-select
	*/
	function chooserNoteType($b){
		if($b['orderby']==''){
			$b['orderby']='ordering';
		}
		if($b['direction']==''){
			$b['direction']='ASC';
		}
		if($b['width'] > 0){
			$b['width']=' style="width: '.$b['width'].'px;"';
		}
		$module=$this->iif($b['module']," AND module='{$b['module']}'",'');

		$inOrOutTypes=$this->DB->query("
			SELECT *
			FROM notetype
			WHERE killed=0 $module
			ORDER BY ".$b['orderby']." ".$b['direction']."
		");
		$r='<select id="'.$b['name'].'" name="'.$b['name'].'"'.$b['width'].'>';
		if($this->DB->numRows()){
			if($b['hasBlank']){
				$r.='<option value="0">'.$b['topname'].'</option>';
			}
			while($inOrOutType=$this->DB->fetchArray($inOrOutTypes)){
				$r.='<option value="'.$inOrOutType['typeid'].'"'.$this->iif($b['selectedid']==$inOrOutType['typeid'], ' selected', '').'>'.$inOrOutType['title'].'</option>';
			}
		}
		$r.='</select>';

		return $r;
	}
	/**
	 * 订单明细的方式（购买和免费）
	 * @author creator zhourui 2014-1-22 10:07:54
	 * @access public
	 * @param array $b 生成选择框参数列表
	 * @return string $r 返回购买方式的选择框 
	 * @throws none 没有异常
	 */
	function chooserItemType($b){
		if($b['width'] > 0){
			$b['width']=' style="width: '.$b['width'].'px;"';
		}
		$r='<select id="'.$b['name'].'" name="'.$b['name'].'"'.$b['width'].'>';
		if($b['hasBlank']){
			$r.='<option value="0">'.$b['topname'].'</option>';
		}
		foreach($this->itemType AS $key=>$type){
			$r.='<option value="'.$key.'"'.$this->iif($b['selectedid']==$key, ' selected', '').'>'.$type['title'].'</option>';
		}
		$r.='</select>';

		return $r;
	}
	/*
	*		choose order, included order and purchase order
	*		@param array(orderby,direction,width,name,topname,hasBlank,selectedid,module)
	*		return html-select
	*/
	function chooserOrder($b){
		if($b['orderby']==''){
			$b['orderby']='created';
		}
		if($b['direction']==''){
			$b['direction']='DESC';
		}
		if($b['width'] > 0){
			$b['width']=' style="width: '.$b['width'].'px;"';
		}
		if($b['condition'] > 0){
			$condition=$b['condition'];
		}
		if($b['onchange']){
			$onchange=' onchange="'.$b['onchange'].'"';
		}
		$b['type']=$this->iif($b['routeid']>0,' AND o.routeid='.$b['routeid'],'');
		$b['route']=$this->iif($b['route'],' AND o.type="'.$b['route'].'"','');
		$b['customerid']=$this->iif($b['customerid']>0,' AND o.customerid="'.$b['customerid'].'"','');
		if($b['selectedid']>0){
			$selectOrder=$this->DB->queryFirst("
				SELECT `order`.orderid,`order`.orderno,
					`customer`.abbr AS customer
				FROM `order`
				LEFT JOIN `customer` ON (`customer`.customerid=`order`.customerid)
				WHERE `order`.killed=0 AND `order`.orderid='".$b['selectedid']."'
			");
			$selectItems=$this->DB->query("
				SELECT m.title
				FROM item AS i
				LEFT JOIN material AS m ON (m.materialid=i.materialid)
				WHERE i.killed=0 AND i.module='order' AND i.mid='".$selectOrder['orderid']."'
				ORDER BY i.itemid ASC
			");
			if($this->DB->numRows()){
				while($selectItem=$this->DB->fetchArray($selectItems)){
					if($selectItem['title']){
						$selectTitle.='-'.$selectItem['title'];
					}
				}
			}
			$select='<option value="'.$selectOrder['orderid'].'" selected>'.$selectOrder['orderno'].$selectTitle.' </option>';
			$condition.=' AND o.orderid<>'.$selectOrder['orderid'];
		}
		$orders=$this->DB->query("
			SELECT o.*,
				c.title AS customer
			FROM `order` AS o
			LEFT JOIN `customer` AS c ON (c.customerid=o.customerid)
			WHERE o.killed=0 AND o.ifApprove=1 ".$b['type']." ".$b['route']." ".$b['customerid']." $condition
			ORDER BY ".$b['orderby']." ".$b['direction']."
		");
		$r='<select id="'.$b['name'].'" name="'.$b['name'].'"'.$b['width'].' '.$onchange.'>';
		if($this->DB->numRows()){
			if($b['hasBlank']){
				$r.='<option value="0">'.$b['topname'].'</option>'.$select;
			}
			while($order=$this->DB->fetchArray($orders)){
				$title='';
				$items=$this->DB->query("
					SELECT m.title
					FROM item AS i
					LEFT JOIN material AS m ON (m.materialid=i.materialid)
					WHERE i.killed=0 AND i.module='order' AND i.mid='".$order['orderid']."'
					ORDER BY i.itemid ASC
				");
				if($this->DB->numRows()){
					while($item=$this->DB->fetchArray($items)){
						if($item['title']){
							$title.='-'.$item['title'];
						}
					}
				}
				$r.='<option value="'.$order['orderid'].'" >'.$order['orderno'].$title.'</option>';
			}
		}
		$r.='</select>';

		return $r;
	}
	/*
	*		choose order, purchase order
	*		@param array(orderby,direction,width,name,topname,hasBlank,selectedid,module,condition)
	*		return html-select
	*/



	function doverify($s){
       if($s['module']=='' OR !is_array($s)){
		   return;
	   }
	   $direction="WHERE module='".$s['module']."' AND action= '".$s['action']."'  ";
	   $purview=$this->DB->queryFirst("SELECT m.purview AS mpurview,p.purviews AS ppurview
		FROM `member` AS m
		LEFT JOIN `position` AS p ON (p.positionid=m.positionid)
		WHERE userid='".$this->user['userid']."'");
	 $module=$this->DB->queryFirst("SELECT moduleid FROM `module` ".$direction."");
	$module=$module['moduleid'] ;
	 $mpurview=explode(',',$purview['mpurview']);
	 $ppurview=explode(',',$purview['ppurview']);
	 $purviews=array_unique(array_merge($mpurview,$ppurview));
	 if(in_array($module,$purviews)){
		 return 1;
	 }else{
		 return 0;
	 }
	}
	//
	function chooserQuotingtype($b){
	if($b['orderby']==''){
		$b['orderby']='title';
	}
	if($b['direction']==''){
		$b['direction']='ASC';
	}
	if($b['width']>0){
		$b['width']='style="width:'.$b['width'].'px;"';
	}
	$quotingtype=$this->DB->query("
		SELECT * 
		FROM `quotingtype`
		WHERE killed=0
		ORDER BY ".$b['orderby']." ".$b['direction']."
	");
	$r="<select id=".$b['name']." name=".$b['name']." ".$b['width'].">";
	if($this->DB->numRows()){
		if($b['hasBlank']){
			$r .= '<option value="0">'.$b['topname'].'</option>';
		}
		while($type=$this->DB->fetchArray($quotingtype)){
			if($b['selectedid']==$type['typeid']){
				$r.='<option value="'.$type['typeid'].'"selected>'.$type['title'].'</option>';
			}else{
				$r.='<option value="'.$type['typeid'].'">'.$type['title'].'</option>';
			}
		}
	}
	$r.='</select>';

	return $r;
}

	function chooserPOrder($b){
		if($b['orderby']==''){
			$b['orderby']='porderid';
		}
		if($b['direction']==''){
			$b['direction']='ASC';
		}
		if($b['width'] > 0){
			$b['width']=' style="width: '.$b['width'].'px;"';
		}
		if($b['condition']){
			$condition=$b['condition'];
		}
		if($b['selectedid']>0){
			$selectOrder=$this->DB->queryFirst("
				SELECT `porder`.porderid,`porder`.orderno,
					`supplier`.abbr AS supplier
				FROM `porder`
				LEFT JOIN `supplier` ON (`supplier`.supplierid=`porder`.supplierid)
				WHERE `porder`.killed=0 AND `porder`.porderid='".$b['selectedid']."'
			");
			$selectItems=$this->DB->query("
				SELECT m.title
				FROM item AS i
				LEFT JOIN material AS m ON (m.materialid=i.materialid)
				WHERE i.killed=0 AND i.module='porder' AND i.mid='".$selectOrder['porderid']."'
				ORDER BY i.itemid ASC
			");
			if($this->DB->numRows()){
				while($selectItem=$this->DB->fetchArray($selectItems)){
					$selectTitle.='-'.$selectItem['title'];
				}
			}
			$select='<option value="'.$selectOrder['porderid'].'" selected>'.$selectOrder['orderno'].$selectTitle.' ( '.$selectOrder['supplier'].' ) </option>';
		}

		$orders=$this->DB->query("
			SELECT `porder`.porderid,`porder`.orderno,
				`supplier`.abbr AS supplier
			FROM `porder`
			LEFT JOIN `supplier` ON (`supplier`.supplierid=`porder`.supplierid)
			WHERE `porder`.killed=0 AND `porder`.ifVerify=1 AND `porder`.ifApprove=1 {$condition}
			ORDER BY ".$b['orderby']." ".$b['direction']."
		");
		$r='<select id="'.$b['name'].'" name="'.$b['name'].'"'.$b['width'].'>';
		if($this->DB->numRows()){
			if($b['hasBlank']){
				$r.='<option value="0">'.$b['topname'].'</option>'.$select;
			}
			while($order=$this->DB->fetchArray($orders)){
				$title='';
				$items=$this->DB->query("
					SELECT m.title
					FROM item AS i
					LEFT JOIN material AS m ON (m.materialid=i.materialid)
					WHERE i.killed=0 AND i.module='porder' AND i.mid='".$order['porderid']."'
					ORDER BY i.itemid ASC
				");
				if($this->DB->numRows()){
					while($item=$this->DB->fetchArray($items)){
						$title.='-'.$item['title'];
					}
				}
				$r.='<option value="'.$order['porderid'].'">'.$order['orderno'].$title.' ( '.$order['supplier'].' ) </option>';
			}
		}
		$r.='</select>';

		return $r;
	}
	//
	function chooserOrderRoute($b){
		if($b['orderby'] == ''){
			$b['orderby'] = 'ordering';
		}
		if($b['direction'] == ''){
			$b['direction'] = 'ASC';
		}
		$routes = $this->DB->query("
			SELECT *
			FROM `orderroute`
			WHERE killed=0
			ORDER BY ".$b['orderby']." ".$b['direction']."
		");
		if($this->DB->numRows()){
			while($route = $this->DB->fetchArray($routes)){
				$r .= '<label class="bold" for="route'.$route['routeid'].'" title="说明：'.$route['caption'].'"><input type="radio" id="route'.$route['routeid'].'" name="'.$b['name'].'" value="'.$route['routeid'].'"';
				if($route['routeid']==$b['selectedid']){
					$r .= ' checked';
				}
				$r .= ' />'.$route['title'].'<span class="small">('.$route['typelist'].')</label>';
			}
		}
		return $r;
	}

	//
	function chooserPacking($b){
		if($b['orderby'] == ''){
			$b['orderby'] = 'ordering';
		}
		if($b['direction'] == ''){
			$b['direction'] = 'ASC';
		}
		if($b['width'] > 0){
			$b['width'] = ' style="width: '.$b['width'].'px;"';
		}
		if($b['condition'] == ''){
			$b['condition'] = '1=1';
		}
		$packings = $this->DB->query("
			SELECT *
			FROM `packing`
			WHERE killed=0 AND ".$b['condition']."
			ORDER BY ".$b['orderby']." ".$b['direction']."
		");
		$r = '<select id="'.$b['name'].'" name="'.$b['name'].'"'.$b['width'].'>';
		if($this->DB->numRows()){
			if($b['hasBlank']){
				$r .= '<option value="0">'.$b['topname'].'</option>';
			}
			while($packing = $this->DB->fetchArray($packings)){
				if($b['selectedid'] == $packing['packingid']){
					$r .= '<option value="'.$packing['packingid'].'" selected>'.$packing['title'].' ('.$packing['entitle'].')</option>';
				}else{
					$r .= '<option value="'.$packing['packingid'].'">'.$packing['title'].' ('.$packing['entitle'].')</option>';
				}
			}
		}
		$r .= '</select>';

		return $r;
	}

	/**
 	 * 生成包装规格选择框
 	 * @author creator mayinghao 2013-07-23 9:15
 	 * @access public
 	 * @param array $arr 条件数组
 	 * @return string $r 返回包装规格选择框字符串
 	 * @throws none 没有异常
 	 */
	function chooserPackingsize($b){
		if($b['orderby'] == ''){
			$b['orderby'] = 'packingsizeid';
		}
		if($b['direction'] == ''){
			$b['direction'] = 'ASC';
		}
		if($b['width'] > 0){
			$b['width'] = ' style="width: '.$b['width'].'px;"';
		}
		if($b['condition'] == ''){
			$b['condition'] = '1=1';
		}
		$packings = $this->DB->query("
			SELECT *
			FROM `packingsize`
			WHERE killed=0 AND ".$b['condition']."
			ORDER BY ".$b['orderby']." ".$b['direction']."
		");
		$r = '<select id="'.$b['name'].'" name="'.$b['name'].'"'.$b['width'].'>';
		if($this->DB->numRows()){
			if($b['hasBlank']){
				$r .= '<option value="0">'.$b['topname'].'</option>';
			}
			while($packing = $this->DB->fetchArray($packings)){
				if($b['selectedid'] == $packing['packingsizeid']){
					$r .= '<option value="'.$packing['packingsizeid'].'" title="'.$packing['remark'].'" selected>'.$packing['length'].'X'.$packing['width'].'X'.$packing['height'].' ('.$packing['cartonNum'].'pcs)</option>';
				}else{
					$r .= '<option value="'.$packing['packingsizeid'].'" title="'.$packing['remark'].'">'.$packing['length'].'X'.$packing['width'].'X'.$packing['height'].' ('.$packing['cartonNum'].'pcs)</option>';
				}
			}
		}
		$r .= '</select>';

		return $r;
	}
   //
   function chooserPorders($b){
		if($b['orderby']==''){
			$b['orderby']='porderid';
		}
		if($b['direction']==''){
			$b['direction']='ASC';
		}
		if($b['width'] > 0){
			$b['width']=' style="width: '.$b['width'].'px;"';
		}
		if($b['condition']){
			$condition=$b['condition'];
		}
		if($b['module']=='pinvoice'){
			$ifComplete = 1;
		}else{
			$ifComplete = 0;		
		}
		$porders=$this->DB->query("
			SELECT `porder`.porderid,`porder`.orderno,`porder`.purchaseType,
				`supplier`.abbr AS supplier
			FROM `porder` 
			LEFT JOIN `supplier` ON (`supplier`.supplierid=`porder`.supplierid)
			WHERE `porder`.killed=0 AND `porder`.ifVerify=1 AND `porder`.ifApprove=1 AND `porder`.purchaseType='entrust' and ifComplete=".$ifComplete."
			ORDER BY ".$b['orderby']." ".$b['direction']."
		");
		$r='<select id="'.$b['name'].'" name="'.$b['name'].'"'.$b['width'].'>';
		if($this->DB->numRows()){
			if($b['hasBlank']){
				$r.='<option value="0">'.$b['topname'].'</option>';
			}
			$i=0;
			while($porder=$this->DB->fetchArray($porders)){
				$title='';
				$items=$this->DB->query("
					SELECT m.title
					FROM item AS i
					LEFT JOIN material AS m ON (m.materialid=i.materialid)
					WHERE i.killed=0 AND i.module='porder' AND i.mid='".$porder['porderid']."'
					ORDER BY i.itemid ASC
				");
				if($this->DB->numRows()){
					while($item=$this->DB->fetchArray($items)){
						$title.='-'.$item['title'];
					}
				}
				$r.='<option value="'.$porder['porderid'].'">'.$porder['orderno'].$title.' ( '.$porder['supplier'].' ) </option>';
				$i++;
			}
		}
		$r.='</select>';
		return $r;
		
	}
   //
   function chooserPaymentstatus($b){
	   if($b['orderby']==''){
		   $b['orderby']='title ASC ,ordering';
	   }
	   if($b['direction']==''){
		   $b['direction']=' ASC';
	   }
	   if($b['width']>0){
		   $b['width']='style="width:'.$b['width'].'px;"';
	   }
	   if($b['module']!=''){
		   $b['module']='AND module="'.$b['module'].'"';
	   }
		$payments=$this->DB->query("
			SELECT paymentstatusid,title,entitle
			FROM paymentstatus
			WHERE killed=0 ".$b['module']."
			ORDER BY ".$b['orderby']."".$b['direction']."
			");
			$r='<select id="'.$b['name'].'" name="'.$b['name'].'" '.$b['width'].'>';
			if($this->DB->numRows()){
				if($b['hasBlank']){
					$r.='<option value="0">'.$b['topname'].'</option>';
				}
				while($payment=$this->DB->fetchArray($payments)){
					if($payment['entitle']){
						$title=$payment['title'].'('.$payment['entitle'].')';
			      }else{
					  $title=$payment['title'];
				  }
					if($b['selectedid']==$payment['paymentstatusid']){
						$r.='<option value="'.$payment['paymentstatusid'].'" selected>'.$title.'</option>';
					}else{
						$r.='<option value="'.$payment['paymentstatusid'].'">'.$title.'</option>';
					}
				}
			}
			$r.='</select>';
			return $r;
   }
	//
	function chooserPaymentterm($b){
		if($b['orderby'] == ''){
			$b['orderby'] = 'entitle ASC, ordering';
		}
		if($b['direction'] == ''){
			$b['direction'] = 'ASC';
		}
		if($b['width'] > 0){
			$b['width'] = ' style="width: '.$b['width'].'px;"';
		}
		if($b['module']!=''){
			$b['module']=' AND module="'.$b['module'].'"';
		}
		$payments = $this->DB->query("
			SELECT *
			FROM paymentterm
			WHERE killed=0 ".$b['module']."
			ORDER BY ".$b['orderby']." ".$b['direction']."
		");
		$r = '<select id="'.$b['name'].'" name="'.$b['name'].'"'.$b['width'].'>';
		if($this->DB->numRows()){
			if($b['hasBlank']){
				$r .= '<option value="0">'.$b['topname'].'</option>';
			}
			while($payment = $this->DB->fetchArray($payments)){
				if($b['selectedid'] == $payment['paymenttermid']){
					$r .= '<option value="'.$payment['paymenttermid'].'" selected>'.$payment['title'].' ('.$payment['entitle'].')</option>';
				}else{
					$r .= '<option value="'.$payment['paymenttermid'].'">'.$payment['title'].' ('.$payment['entitle'].')</option>';
				}
			}
		}
		$r .= '</select>';

		return $r;
	}
	/* chosser entrust item
	* @param	array	width,orderby,direction,name,selectedid,entrustid
	* @return	select
	*/
	function chooserEntrustItem($b){
		if($b['width'] > 0){
			$b['width']=' style="width: '.$b['width'].'px;"';
		}
		if($b['orderby']==''){
			$b['orderby']='entrustid ';
		}
		if($b['direction']==''){
			$b['direction']='ASC';
		}
		$entrusts=$this->DB->query("
			SELECT entrustid,entrustno,ifVerify
			FROM `entrust`
			WHERE killed=0 AND ifVerify=1
			ORDER BY ".$b['orderby']." ".$b['direction']."
		");
		$r='<select id="'.$b['name'].'" name="'.$b['name'].'"'.$b['width'].'>';
		if($this->DB->numRows()){
			if($b['hasBlank']){
				$r.='<option value="0">'.$b['topname'].'</option>';
			}
			while ($entrust=$this->DB->fetchArray($entrusts)){
				$items=$this->DB->query("
					SELECT `entrustitem`.itemid,`entrustitem`.materialid,`entrustitem`.ifChooser,`entrustitem`.quantity,
						`material`.materialno,`material`.title,`material`.standard
					FROM `entrustitem`
					LEFT JOIN `material` ON (`material`.materialid=`entrustitem`.materialid)
					WHERE `entrustitem`.killed=0 AND `entrustitem`.entrustid='".$entrust['entrustid']."'
					ORDER BY `entrustitem`.itemid ASC
				");
				if($this->DB->numRows()){
					$r.='<option'.$this->iif($entrust['ifVerify']==1,' style="font-weight:bold"','').' value="" >'.$entrust['entrustno'].'</option>';
					while($item=$this->DB->fetchArray($items)){
						$r.='<option '.$this->iif($item['ifChooser']==1,' style="color: gray;"', '').' value="'.$item['itemid'].'"'.$this->iif($b['selectedid']==$item['itemid'], ' selected', '').'>　　'.$item['materialno'].'　'.$item['title'].'　'.$item['standard'].'　　(数量：'.$item['quantity'].')</span></option>';
					}
				}
			}
		}else{
			$r.='<option>暂无采购单</option>';
		}
		$r.='</select>';
		return $r;
	}

	function chooserEntrustMaterial($b){
		if($b['direction'] == ''){
			$b['direction'] = 'ASC';
		}
		if($b['width'] > 0){
			$b['width'] = ' style="width: '.$b['width'].'px;"';
		}	
		if($b['condition'] == ''){
			$b['condition'] = '1=1';
		}
		$materials=$this->DB->query("SELECT materialid,materialno,title,originid FROM `material` WHERE originid=3");
		$r = '<select id="'.$b['name'].'" name="'.$b['name'].'"'.$b['width'].'>';
		if($this->DB->numRows()){
			if($b['hasBlank']==1 and $b['selectedid']==0){
				$r .= '<option value="0">'.$b['topname'].'</option>';
			}
			while($material = $this->DB->fetchArray($materials)){
				$r.='<option value="'.$material['materialid'].'"'.$this->iif($b['selectedid'] == $material['materialid'], ' selected', '').'>'.$material['materialno'].'('.$material['title'].')</option>';
				
			}
			
		}
		$r.='</select>';
		return $r;
	}
	/* chosser item of application for purchase
	* @param	array	width,orderby,direction,name,selectedid,purchaseid
	* @return	select
	*/
	function chooserPurchaseApplyItem($b){
		if($b['width'] > 0){
			$b['width']=' style="width: '.$b['width'].'px;"';
		}
		if($b['orderby']==''){
			$b['orderby']='purchaseApplyid ASC, ordering';
		}
		if($b['direction']==''){
			$b['direction']='ASC';
		}
		$applys=$this->DB->query("
			SELECT purchaseApplyid,applyno,ifVerify
			FROM `purchaseApply`
			WHERE killed=0 AND ifVerify=1 AND ifPlan=1
			ORDER BY ".$b['orderby']." ".$b['direction']."
		");
		$r='<select id="'.$b['name'].'" name="'.$b['name'].'"'.$b['width'].'>';
		if($this->DB->numRows()){
			if($b['hasBlank']){
				$r .= '<option value="0">'.$b['topname'].'</option>';
			}
			if($b['selectedid']){
				$olItem=$this->DB->queryFirst("
					SELECT `preinbounditem`.itemid,`preinbounditem`.materialid,`preinbounditem`.ifChooser,`preinbounditem`.materialno,
							`preinbounditem`.title,`preinbounditem`.standard,`preinbounditem`.quantity,
						`material`.materialno AS newMaterialno,`material`.title AS newTitle,`material`.standard AS newStandard
					FROM `preinbounditem`
					LEFT JOIN `material` ON (`material`.materialid=`preinbounditem`.materialid)
					WHERE `preinbounditem`.itemid='".$b['selectedid']."'
					ORDER BY `preinbounditem`.ordering ASC
				");
				$r.='<option class="bold normal">当前物资</option><option value="'.$olItem['itemid'].'" selected>　　'.$this->iif($olItem['materialid']==0,$olItem['materialno'].'　'.$olItem['title'].'　'.$olItem['standard'],$olItem['newMaterialno'].'　'.$olItem['newTitle'].'　'.$olItem['newStandard']).'　　(数量：'.$olItem['quantity'].')</span></option><option class="bold normal">其他物资</option>';
			}
			while ($apply=$this->DB->fetchArray($applys)){
				$items=$this->DB->query("
					SELECT `preinbounditem`.itemid,`preinbounditem`.materialid,`preinbounditem`.ifChooser,`preinbounditem`.materialno,
							`preinbounditem`.title,`preinbounditem`.standard,`preinbounditem`.quantity,
						`material`.materialno AS newMaterialno,`material`.title AS newTitle,`material`.standard AS newStandard
					FROM `preinbounditem`
					LEFT JOIN `material` ON (`material`.materialid=`preinbounditem`.materialid)
					WHERE `preinbounditem`.killed=0 AND `preinbounditem`.ifChooser=0 AND `preinbounditem`.module='purchaseApply' AND `preinbounditem`.mid='".$apply['purchaseApplyid']."'
					ORDER BY `preinbounditem`.ordering ASC
				");
				if($this->DB->numRows()){
					$r.='<option'.$this->iif($apply['ifVerify']==1, ' style="font-weight:bold"', '').' value="" >'.$apply['applyno'].'</option>';
					while($item=$this->DB->fetchArray($items)){
						$r.='<option value="'.$item['itemid'].'">　　'.$this->iif($item['materialid']==0,$item['materialno'].'　'.$item['title'].'　'.$item['standard'],$item['newMaterialno'].'　'.$item['newTitle'].'　'.$item['newStandard']).'　　(数量：'.$item['quantity'].')</span></option>';
					}
				}
			}
		}else{
			$r.='<option>暂无采购申请单</option>';
		}
		$r.='</select>';
		return $r;
	}
	/* chosser purchase item
	* @param	array	width,orderby,direction,name,selectedid,purchaseid
	* @return	select
	*/
	function chooserPurchaseItem($b){
		if($b['width'] > 0){
			$b['width']=' style="width: '.$b['width'].'px;"';
		}
		if($b['orderby']==''){
			$b['orderby']='purchaseid ASC, ordering';
		}
		if($b['direction']==''){
			$b['direction']='ASC';
		}
		$purchases=$this->DB->query("
			SELECT purchaseid, purchaseno,ifVerify
			FROM `purchase`
			WHERE killed=0 AND ifVerify=1
			ORDER BY ".$b['orderby']." ".$b['direction']."
		");
		$r='<select id="'.$b['name'].'" name="'.$b['name'].'"'.$b['width'].'>';
		if($this->DB->numRows()){
			if($b['hasBlank']){
				$r .= '<option value="0">'.$b['topname'].'</option>';
			}
			if($b['selectedid']){
				$olItem=$this->DB->queryFirst("
					SELECT `purchaseitem`.itemid,`purchaseitem`.materialid,`purchaseitem`.ifChooser,`purchaseitem`.materialno,
							`purchaseitem`.title,`purchaseitem`.standard,`purchaseitem`.quantity,
						`material`.materialno AS newMaterialno,`material`.title AS newTitle,`material`.standard AS newStandard
					FROM `purchaseitem`
					LEFT JOIN `material` ON (`material`.materialid=`purchaseitem`.materialid)
					WHERE `purchaseitem`.itemid='".$b['selectedid']."'
					ORDER BY `purchaseitem`.ordering ASC
				");
				$r.='<option class="bold normal">当前物资</option><option value="'.$olItem['itemid'].'" selected>　　'.$this->iif($olItem['materialid']==0,$olItem['materialno'].'　'.$olItem['title'].'　'.$olItem['standard'],$olItem['newMaterialno'].'　'.$olItem['newTitle'].'　'.$olItem['newStandard']).'　　(数量：'.$olItem['quantity'].')</span></option><option class="bold normal">其他物资</option>';
			}
			while ($purchase=$this->DB->fetchArray($purchases)){
				$items=$this->DB->query("
					SELECT `purchaseitem`.itemid,`purchaseitem`.materialid,`purchaseitem`.ifChooser,`purchaseitem`.quantity,
						`preinbounditem`.materialno,`preinbounditem`.title,`preinbounditem`.standard,
						`material`.materialno AS newMaterialno,`material`.title AS newTitle,`material`.standard AS newStandard
					FROM `purchaseitem`
					LEFT JOIN `preinbounditem` ON (`preinbounditem`.itemid=`purchaseitem`.applyItemid)
					LEFT JOIN `material` ON (`material`.materialid=`purchaseitem`.materialid)
					WHERE `purchaseitem`.killed=0 AND `purchaseitem`.ifChooser=0 AND `purchaseitem`.purchaseid='".$purchase['purchaseid']."'
					ORDER BY `purchaseitem`.ordering ASC
				");
				if($this->DB->numRows()){
					$r.='<option'.$this->iif($purchase['ifVerify']==1,' style="font-weight:bold"','').' value="" >'.$purchase['purchaseno'].'</option>';
					while($item=$this->DB->fetchArray($items)){
						$r.='<option value="'.$item['itemid'].'">　　'.$this->iif($item['materialid']==0,$item['materialno'].'　'.$item['title'].'　'.$item['standard'],$item['newMaterialno'].'　'.$item['newTitle'].'　'.$item['newStandard']).'　　(数量：'.$item['quantity'].')</span></option>';
					}
				}
			}
		}else{
			$r.='<option>暂无采购单</option>';
		}
		$r.='</select>';
		return $r;
	}
	/* chosser purchase
	* @param	array	width,orderby,direction,name,selectedid,purchaseid
	* @return	select
	*/
	function chooserPurchase($b){
		if($b['width'] > 0){
			$b['width']=' style="width: '.$b['width'].'px;"';
		}
		if($b['orderby']==''){
			$b['orderby']='created DESC, purchaseid';
		}
		if($b['direction']==''){
			$b['direction']='ASC';
		}
		if($b['module']!=''){
			$items=$this->DB->query("SELECT DISTINCT purchaseid FROM purchaseitem WHERE killed=0 AND ifChooser=0 AND module='".$b['module']."' ORDER BY created ASC");
			if($this->DB->numRows()){
				while($item=$this->DB->fetchArray($items)){
					$id[]=$item['purchaseid'];
				}
			}
			if(is_Array($id)){
				$condition=" AND purchaseid IN (".implode(',',$id).")";
			}
		}
		$purchases=$this->DB->query("
			SELECT purchaseid, purchaseno
			FROM `purchase`
			WHERE killed=0 AND ifVerify=1 ".$condition."
			ORDER BY ".$b['orderby']." ".$b['direction']."
		");
		$r = '<select id="'.$b['name'].'" name="'.$b['name'].'"'.$b['width'].'>';
		if($this->DB->numRows()){
			if($b['hasBlank']){
				$r.='<option value="0">'.$b['topname'].'</option>';
			}
			while($purchase=$this->DB->fetchArray($purchases)){
				$r.='<option value="'.$purchase['purchaseid'].'"';
				if($b['selectedid'] == $purchase['purchaseid']){
					$r.=' selected';
				}
				$r.='>'.$purchase['purchaseno'].'</option>';
			}
		}
		$r .= '</select>';
		return $r;
	}

	//
	function chooserPInvoiceType($b){
		if($b['orderby']==''){
			$b['orderby']='ordering';
		}
		if($b['direction']==''){
			$b['direction']='ASC';
		}
		if($b['width'] > 0){
			$b['width']=' style="width: '.$b['width'].'px;"';
		}
		if($b['selectedid']==''){
			$b['selectedid']=2;
		}
		$b['condition']=$this->iif($b['condition'],$b['condition'],'');

		$invoiceTypes=$this->DB->query("
			SELECT *
			FROM pinvoicetype
			WHERE killed=0 ".$b['condition']."
			ORDER BY ".$b['orderby']." ".$b['direction']."
		");
		if($this->DB->numRows()){
			while($invoiceType=$this->DB->fetchArray($invoiceTypes)){
				$r.=' <input type="radio" name="'.$b['name'].'" value="'.$invoiceType['pinvoiceTypeid'].'"'.$this->iif($b['selectedid']==$invoiceType['pinvoiceTypeid'], ' checked', '').'> '.$invoiceType['title'];
			}
		}

		return $r;
	}
	//
	function chooserRelationship($b){
		if($b['orderby']==''){
			$b['orderby']='`customertype`.typeid ASC,ordering';
		}
		if($b['direction']==''){
			$b['direction']=' ASC';
		}
		if($b['width']>0){
			$b['width']='style="width:'.$b['width'].'px';
		}
		
		$relationship=$this->DB->query("
			SELECT `customertype`.*
			 FROM `customertype`
			  WHERE `customertype`.killed=0
				ORDER BY ".$b['orderby']."".$b['direction']."
		");
		$r='<select id="'.$b['name'].'"name="'.$b['name'].'"'.$b['width'].'>';
		if($this->DB->numRows()){
			if($b['hasBlank']){
				$r.='<option value="0">'.$b['topname'].'</option>';
			}
			while($relationships=$this->DB->fetchArray($relationship)){
				if($b['selectedid']==$relationships['typeid']){
                 $r.='<option value="'.$relationships['typeid'].'" selected>'.$relationships['title'].'('.$relationships['entitle'].')</option>';
				}else{
					$r.='<option value="'.$relationships['typeid'].'">'.$relationships['title'].'('.$relationships['entitle'].')</option>';
				}
			}
		}
		$r .= '</select>';

		return $r;
	}
	//
	function chooserPort($b){
		if($b['orderby'] == ''){
			$b['orderby'] = '`region`.encountry ASC, ordering';
		}
		if($b['direction'] == ''){
			$b['direction'] = 'ASC';
		}
		if($b['width'] > 0){
			$b['width'] = ' style="width: '.$b['width'].'px;"';
		}

		$ports = $this->DB->query("
			SELECT `port`.*,
				`region`.country, `region`.encountry
			FROM `port`
			LEFT JOIN `region` ON (`region`.regionid=`port`.regionid)
			WHERE `port`.killed=0
			ORDER BY ".$b['orderby']." ".$b['direction']."
		");
		$r = '<select id="'.$b['name'].'" name="'.$b['name'].'"'.$b['width'].'>';
		if($this->DB->numRows()){
			if($b['hasBlank']){
				$r .= '<option value="0">'.$b['topname'].'</option>';
			}
			while($port = $this->DB->fetchArray($ports)){
				if($b['selectedid'] == $port['portid']){
					$r .= '<option value="'.$port['portid'].'" selected>'.substr($port['encountry'],0,1).' '.$port['country'].'：'.$port['title'].'('.$port['entitle'].')</option>';
				}else{
					$r .= '<option value="'.$port['portid'].'">'.substr($port['encountry'],0,1).' '.$port['country'].'：'.$port['title'].'('.$port['entitle'].')</option>';
				}
			}
		}
		$r .= '</select>';

		return $r;
	}
	//
	function chooserRegion($b){
		if($b['regionid']>0){
			$region = $this->DB->queryFirst("SELECT regionid, countryid, provinceid, pnum, cnum FROM `region` WHERE regionid='".$b['regionid']."'");
		}else{
			$b['regionid']=-1;
		}
		if($b['orderby'] == ''){
			$b['orderby'] = 'encountry';
		}
		if($b['direction'] == ''){
			$b['direction'] = 'ASC';
		}
		if($b['width'] > 0){
			$b['width'] = ' style="width: '.$b['width'].'px;"';
		}
		if($b['areaid']>0){
			$b['areaid'] = " AND areaid='".$b['areaid']."'";
		}else{
			$b['areaid'] = "";
		}

		$countrys = $this->DB->query("SELECT regionid, country, encountry, abbr, countryid, pnum FROM region WHERE killed=0 AND countryid=0 ".$b['areaid']." ORDER BY encountry ASC");
		if($this->DB->numRows()){
			$r = '<input type="hidden" id="lastCountryid" value=""><input type="hidden" id="regionid" name="regionid" value="'.$b['regionid'].'"><input type="hidden" id="lastProvinceid" value=""><select id="countryid" name="countryid" '.$b['width'].' onchange="dc.region.province(this);">';
			if($b['hasBlank']){
				$r .= '<option value="0">'.$b['topname'].'</option>';
			}
			$selCountry = '';
			while($country = $this->DB->fetchArray($countrys)){
				$r .= '<option value="'.$country['regionid'].'"';
				if($country['regionid']==$region['countryid'] OR $country['regionid']==$region['regionid']){
					$r .= ' selected';
					$selCountry = $country;
				}
				$r .= '>'.substr($country['encountry'],0,1).' '.$country['country'] .'</option>';

			}
			$r .= '</select>';
		}
		if(!is_Array($selCountry))$selCountry['pnum']=0;
		if($selCountry['pnum']>0){
			$provinces = $this->DB->query("SELECT regionid, state, enstate, provinceid, sabbr, cnum FROM region WHERE killed=0 AND countryid='".$selCountry['regionid']."' AND provinceid=0 ORDER BY enstate ASC");
			if($this->DB->numRows()){
				$r.='<span id="province" style="display:inline"><select id="provinceid" name="provinceid" style="width:115px" onchange="dc.region.city(this);">';
				if($b['hasBlank']){
					$r .= '<option value="-1">'.$b['topname'].'</option>';
				}
				$selProvince='';
				while($province = $this->DB->fetchArray($provinces)){
					$r .= '<option value="'.$province['regionid'].'"';
					if($province['regionid']==$region['regionid'] OR $province['regionid']==$region['provinceid']){
						$r .= ' selected';
						$selProvince = $province;
					}
					$r .= '>'.substr($province['enstate'],0,1).' '.$province['state']. '</option>';
				}
				$r.='</select></span>';
			}
			if(!is_Array($selProvince))$selProvince['cnum']=0;
			if($selProvince['cnum']>0){ //city
				$citys = $this->DB->query("SELECT regionid, city, encity, cabbr FROM region WHERE killed=0 AND countryid='".$selCountry['regionid']."' AND provinceid='".$selProvince['regionid']."' ORDER BY encity ASC");
				if($this->DB->numRows()){
					$r.='<span id="city" style="display:inline"><select id="cityid" name="cityid" style="width:115px">';
					if($b['hasBlank']){
						$r .= '<option value="-1">'.$b['topname'].'</option>';
					}
					while($city = $this->DB->fetchArray($citys)){
						$r .= '<option value="'.$city['regionid'].'"'.$this->iif($city['regionid']==$region['regionid'], ' selected', '').'>'.substr($city['encity'],0,1).' '.$city['city'].'</option>';
					}
					$r.='</select></span>';
				}
			}else{
				$r .= '<span id="city" style="display:none"></span>';
			}
		}else{
			$r.='<span id="province" style="display:none"></span><span id="city" style="display:none"></span>';
		}
		return $r;
	}

	//
	function chooserProvince($b){
		if($b['orderby'] == ''){
			$b['orderby'] = 'provinceid';
		}
		if($b['direction'] == ''){
			$b['direction'] = 'ASC';
		}
		if($b['width'] > 0){
			$b['width'] = ' style="width: '.$b['width'].'px;"';
		}

		$provinces = $this->DB->query("
			SELECT *
			FROM province
			WHERE killed=0
			ORDER BY ".$b['orderby']." ".$b['direction']."
		");
		$r = '<select id="'.$b['name'].'" name="'.$b['name'].'"'.$b['width'].'>';
		if($this->DB->numRows()){
			if($b['hasBlank']){
				$r .= '<option value="0">'.$b['topname'].'</option>';
			}
			while($province = $this->DB->fetchArray($provinces)){
				if($b['selectedid'] == $province['provinceid']){
					$r .= '<option value="'.$province['provinceid'].'" selected>'.$province['title'].'</option>';
				}else{
					$r .= '<option value="'.$province['provinceid'].'">'.$province['title'].'</option>';
				}
			}
		}
		$r .= '</select>';

		return $r;
	}
		//
	function chooserTaxRate($b){
		if($b['width'] > 0){
			$b['width']=' style="width: '.$b['width'].'px;"';
		}
		if($b['selectedid']==''){
			$b['selectedid']==17;
		}
		$taxRate=array(0=>0,1=>3,2=>13,3=>17);
		foreach($taxRate as $key => $val){
			$r.=' <input type="radio" name="'.$b['name'].'" value="'.$val.'"'.$this->iif($b['selectedid']==$val,'checked','').'>'.$val.'%';
		}
		return $r;
	}
	//
	function chooserType($b){

	}
	/*
	*		choose sample, included sample
	*		@param array(orderby,direction,width,name,topname,hasBlank,selectedid,module)
	*		return html-select
	*/
	function chooserSample($b){
		if($b['orderby']==''){
			$b['orderby']='created';
		}
		if($b['direction']==''){
			$b['direction']='DESC';
		}
		if($b['width'] > 0){
			$b['width']=' style="width: '.$b['width'].'px;"';
		}
		$b['customerid']=$this->iif($b['customerid']>0,' AND s.customerid="'.$b['customerid'].'"','');
		if($b['selectedid']>0){
			$selectSample=$this->DB->queryFirst("
				SELECT `sample`.sampleid,`sample`.sampleno,
					`customer`.title AS customer
				FROM `sample`
				LEFT JOIN `customer` ON (`customer`.customerid=`sample`.customerid)
				WHERE `sample`.killed=0 AND `sample`.sampleid='".$b['selectedid']."'
			");
			$selectItems=$this->DB->query("
				SELECT m.title
				FROM item AS i
				LEFT JOIN material AS m ON (m.materialid=i.materialid)
				WHERE i.killed=0 AND i.module='sample' AND i.mid='".$selectSample['sampleid']."'
				ORDER BY i.itemid ASC
			");
			if($this->DB->numRows()){
				while($selectItem=$this->DB->fetchArray($selectItems)){
					$selectTitle.='-'.$selectItem['title'];
				}
			}
			$select='<option value="'.$selectSample['sampleid'].'" selected>'.$selectSample['sampleno'].$selectTitle.' </option>';
		}

		$samples=$this->DB->query("
			SELECT s.*,
				c.title AS customer
			FROM `sample` AS s
			LEFT JOIN `customer` AS c ON (c.customerid=s.customerid)
			WHERE s.killed=0 AND s.ifApprove=1 ".$b['customerid']." $condition
			ORDER BY ".$b['orderby']." ".$b['direction']."
		");
		$r='<select id="'.$b['name'].'" name="'.$b['name'].'"'.$b['width'].'>';
		if($this->DB->numRows()){
			if($b['hasBlank']){
				$r.='<option value="0">'.$b['topname'].'</option>'.$select;
			}
			while($sample=$this->DB->fetchArray($samples)){
				$title='';
				$items=$this->DB->query("
					SELECT m.title
					FROM item AS i
					LEFT JOIN material AS m ON (m.materialid=i.materialid)
					WHERE i.killed=0 AND i.module='sample' AND i.mid='".$sample['sampleid']."'
					ORDER BY i.itemid ASC
				");
				if($this->DB->numRows()){
					while($item=$this->DB->fetchArray($items)){
						$title.='-'.$item['title'];
					}
				}
				$r.='<option value="'.$sample['sampleid'].'" >'.$sample['sampleno'].$title.'</option>';
			}
		}
		$r.='</select>';

		return $r;
	}
	//
	function chooserSampleType($b){
		if($b['orderby'] == ''){
			$b['orderby'] = 'ordering';
		}
		if($b['direction'] == ''){
			$b['direction'] = 'ASC';
		}
		if($b['width'] > 0){
			$b['width'] = ' style="width: '.$b['width'].'px;"';
		}
		if($b['selectedid'] != ''){
			$id = explode(',',$b['selectedid']);
		}
		if($b['module']){
			$module=' AND module="'.$b['module'].'"';
		}
		$sampletypes = $this->DB->query("
			SELECT *
			FROM `sampletype`
			WHERE killed=0 {$module}
			ORDER BY ".$b['orderby']." ".$b['direction']."
		");

		if($this->DB->numRows()){
			$i=1;
			while($sampletype = $this->DB->fetchArray($sampletypes)){
				$r .= '<label for="stype'.$sampletype['typeid'].'" title="('.$sampletype['entitle'].') '.$sampletype['remark'].'"><input type="checkbox" id="stype'.$sampletype['typeid'].'" name="'.$b['name'].'['.$i.']" value="'.$sampletype['typeid'].'"';
				if(is_array($id) AND in_array($sampletype['typeid'], $id)){
					$r .= ' checked';
				}
				$r .= ' />'.$sampletype['title'].'</label>';
				$i++;
			}
		}
		return $r;
	}

	//
	function chooserShipmethod($b){
		if($b['orderby'] == ''){
			$b['orderby'] = 'ordering';
		}
		if($b['direction'] == ''){
			$b['direction'] = 'ASC';
		}
		if($b['width'] > 0){
			$b['width'] = ' style="width: '.$b['width'].'px;"';
		}

		$shipmethods = $this->DB->query("
			SELECT *
			FROM shipmethod
			WHERE killed=0
			ORDER BY ".$b['orderby']." ".$b['direction']."
		");
		$r = '<select id="'.$b['name'].'" name="'.$b['name'].'"'.$b['width'].'>';
		if($this->DB->numRows()){
			if($b['hasBlank']){
				$r .= '<option value="0">'.$b['topname'].'</option>';
			}
			while($shipmethod = $this->DB->fetchArray($shipmethods)){
				if($b['selectedid'] == $shipmethod['shipmethodid']){
					$r .= '<option value="'.$shipmethod['shipmethodid'].'" selected>'.$shipmethod['title'].' ('.$shipmethod['remark'].')</option>';
				}else{
					$r .= '<option value="'.$shipmethod['shipmethodid'].'">'.$shipmethod['title'].' ('.$shipmethod['remark'].')</option>';
				}
			}
		}
		$r .= '</select>';

		return $r;
	}

	//
	function chooserSize($b){
		if($b['orderby'] == ''){
			$b['orderby'] = 'ordering';
		}
		if($b['direction'] == ''){
			$b['direction'] = 'ASC';
		}
		if($b['width'] > 0){
			$b['width'] = ' style="width: '.$b['width'].'px;"';
		}

		$sizes = $this->DB->query("
			SELECT *
			FROM size
			WHERE killed=0
			ORDER BY ".$b['orderby']." ".$b['direction']."
		");
		$r = '<select id="'.$b['name'].'" name="'.$b['name'].'"'.$b['width'].''.$b['js'].'>';
		if($this->DB->numRows()){
			if($b['hasBlank']){
				$r .= '<option value="0">'.$b['topname'].'</option>';
			}
			while($size = $this->DB->fetchArray($sizes)){
				if($b['selectedid'] == $size['sizeid']){
					$r .= '<option value="'.$size['sizeid'].'" selected>'.$size['title'].'</option>';
				}else{
					$r .= '<option value="'.$size['sizeid'].'">'.$size['title'].'</option>';
				}
			}
		}
		$r .= '</select>';

		return $r;
	}

	//
	function chooserSupplier($b){
		if($b['width'] > 0){
			$b['width']=' style="width:'.$b['width'].'px;"';
		}
		if($b['line']!=''){
			$b['line']=','.$b['line'];
		}
		if($b['readonly']==1){
			$readonly=' readonly';
		}
		$param="{'obj':this,'module':'supplier','line':'".$line."'}";
		return '<input type="hidden" id="'.$this->iif($b['hId'],$b['hId'],$b['hName']).'" name="'.$b['hName'].'" value="'.$this->iif($b['selectedid'],$b['selectedid'],0).'" />
		<input type="text" name="'.$b['name'].'" id="'.$this->iif($b['id'],$b['id'],$b['name']).'" '.$b['width'].' onfocus="javascript:dc.autoComplete('.$param.')" value="'.$b['value'].'" '.$readonly .'/>';
	}

	//
	function chooserSupplierSelect($b){
		if($b['field']=='produce'){
			$field=' AND flied="produce"';
		}elseif($b['field']=='trade'){
			$field=' AND flied="trade"';
		}
		if($b['orderby'] == ''){
			$b['orderby'] = 'title';
		}
		if($b['direction'] == ''){
			$b['direction'] = 'ASC';
		}
		if($b['width'] > 0){
			$b['width'] = ' style="width: '.$b['width'].'px;"';
		}

		$suppliers = $this->DB->query("
			SELECT *
			FROM supplier
			WHERE killed=0 ".$filed."
			ORDER BY ".$b['orderby']." ".$b['direction']."
		");
		$r = '<select id="'.$b['name'].'" name="'.$b['name'].'"'.$b['width'].'>';
		if($this->DB->numRows()){
			if($b['hasBlank']){
				$r .= '<option value="0">'.$b['topname'].'</option>';
			}
			while($supplier = $this->DB->fetchArray($suppliers)){
				if($b['selectedid'] == $supplier['supplierid']){
					$r .= '<option value="'.$supplier['supplierid'].'" selected>'.$supplier['title'].'</option>';
				}else{
					$r .= '<option value="'.$supplier['supplierid'].'">'.$supplier['title'].'</option>';
				}
			}
		}
		$r .= '</select>';

		return $r;
	}

	//
	function chooserStock($b){
		if($b['orderby']==''){
			$b['orderby']='stockno';
		}
		if($b['direction']==''){
			$b['direction']='ASC';
		}
		if($b['width'] > 0){
			$b['width']=' style="width: '.$b['width'].'px;"';
		}
		$types=$this->DB->query("SELECT categoryid,title FROM category WHERE parentid=15 ORDER BY categoryid ASC");
		$r='<select id="'.$b['name'].'" name="'.$b['name'].'"'.$b['width'].'>';
		if($this->DB->numRows()){
			while($type=$this->DB->fetchArray($types)){
				$r.='<option>'.$type['title'].'</option>';
				$stocks=$this->DB->query("
					SELECT *
					FROM stock
					WHERE killed=0 AND categoryid='".$type['categoryid']."'
					ORDER BY ".$b['orderby']." ".$b['direction']."
				");
				if($this->DB->numRows()){
					while($stock=$this->DB->fetchArray($stocks)){
						$ifEmpty=$this->iif($stock['ifEmpty']==1,'（已满）','');
						$ifEmptyColor=$this->iif($stock['ifEmpty']==1,'class="gray"','');
						$r.='<option value="'.$stock['stockid'].'" '.$ifEmptyColor;
						if($b['selectedid']==$stock['stockid']){
							$r.='selected';
						}
						$r.=' >　　'.$stock['stockno'].$ifEmpty.'</option>';
						$items=$this->DB->query("
							SELECT si.quantity,si.materialid,
								m.materialno
							FROM stockitem AS si
							LEFT JOIN material AS m ON (m.materialid=si.materialid)
							WHERE si.killed=0 AND si.stockid='".$stock['stockid']."'
						");
						$materialId=$quantity=array();
						if($this->DB->numRows($items)){
							while($item=$this->DB->fetchArray($items)){
								if(in_array($item['materialid'],$materialId)){
									$quantity[$item['materialid']]+=$item['quantity'];
								}else{
									$quantity[$item['materialid']]=$item['quantity'];
									$materialId[]=$item['materialid'];
									$materialno[$item['materialid']]=$item['materialno'];
								}
							}
						}
						foreach($materialId as $key => $val){
							$r.='<option value="'.$stock['stockid'].'" '.$ifEmptyColor.'>　　　　'.$materialno[$val].' ('.$quantity[$val].')</option>';
						}
					}
				}
			}
		}
		$r.='</select>';

		return $r;
	}
	//
	function chooserUnit($b){
		if($b['orderby'] == ''){
			$b['orderby'] = 'ordering';
		}
		if($b['direction'] == ''){
			$b['direction'] = 'ASC';
		}
		if($b['width'] > 0){
			$b['width'] = ' style="width: '.$b['width'].'px;"';
		}

		$units = $this->DB->query("
			SELECT *
			FROM unit
			ORDER BY ".$b['orderby']." ".$b['direction']."
		");
		$r = '<select id="'.$b['name'].'" name="'.$b['name'].'"'.$b['width'].'>';
		if($this->DB->numRows()){
			if($b['hasBlank']){
				$r .= '<option value="0">'.$b['topname'].'</option>';
			}
			while($unit = $this->DB->fetchArray($units)){
				if($b['selectedid'] == $unit['unitid']){
					$r .= '<option value="'.$unit['unitid'].'" selected>'.$unit['title'].' ('.$unit['entitle'].')</option>';
				}else{
					$r .= '<option value="'.$unit['unitid'].'">'.$unit['title'].' ('.$unit['entitle'].')</option>';
				}
			}
		}
		$r .= '</select>';

		return $r;
	}
		//NWI BUG
	function chooserProject($b){
		if($b['orderby']==''){
			$b['orderby']='ordering';
		}
		if($b['direction']==''){
			$b['direction']='ASC';
		}
		if($b['width']>0){
			$b['width']=' style="width: '.$b['width'].'px;"';
		}

		$projects=$this->DB->query("
			SELECT *
			FROM project
			WHERE killed=0 AND enabled=1
			ORDER BY ".$b['orderby']." ".$b['direction']."
		");
		$r='<select id="'.$b['name'].'" name="'.$b['name'].'"'.$b['width'].'>';
		if($this->DB->numRows()){
			if($b['hasBlank']){
				$r.='<option value="0">'.$b['topname'].'</option>';
			}
			while($project=$this->DB->fetchArray($projects)){
				if($b['selectedid']==$project['projectid']){
					$r.='<option value="'.$project['projectid'].'" selected>'.$project['title'].'</option>';
				}else{
					$r.='<option value="'.$project['projectid'].'">'.$project['title'].'</option>';
				}
			}
		}
		$r.='</select>';

		return $r;
	}

	//
	function chooserPriority($b){
			if($b['orderby']==''){
			$b['orderby']='ordering';
		}
		if($b['direction']==''){
			$b['direction']='ASC';
		}
		if($b['width']>0){
			$b['width']=' style="width: '.$b['width'].'px;"';
		}

		$prioritys=$this->DB->query("
			SELECT *
			FROM priority
			WHERE killed=0
			ORDER BY ".$b['orderby']." ".$b['direction']."
		");
		$r='<select id="'.$b['name'].'" name="'.$b['name'].'"'.$b['width'].'>';
		if($this->DB->numRows()){
			if($b['hasBlank']){
				$r.='<option value="0">'.$b['topname'].'</option>';
			}
			while($priority=$this->DB->fetchArray($prioritys)){
				if($b['selectedid']==$priority['priorityid']){
					$r.='<option value="'.$priority['priorityid'].'" selected>'.$priority['title'].'</option>';
				}else{
					$r.='<option value="'.$priority['priorityid'].'">'.$priority['title'].'</option>';
				}
			}
		}
		$r.='</select>';

		return $r;
	}
	//
	function chooserSeverity($b){
			if($b['orderby']==''){
			$b['orderby']='ordering';
		}
		if($b['direction']==''){
			$b['direction']='ASC';
		}
		if($b['width']>0){
			$b['width']=' style="width: '.$b['width'].'px;"';
		}

		$severitys=$this->DB->query("
			SELECT *
			FROM severity
			WHERE killed=0
			ORDER BY ".$b['orderby']." ".$b['direction']."
		");
		$r='<select id="'.$b['name'].'" name="'.$b['name'].'"'.$b['width'].'>';
		if($this->DB->numRows()){
			if($b['hasBlank']){
				$r.='<option value="0">'.$b['topname'].'</option>';
			}
			while($severity=$this->DB->fetchArray($severitys)){
				if($b['selectedid']==$severity['severityid']){
					$r.='<option value="'.$severity['severityid'].'" selected>'.$severity['title'].'</option>';
				}else{
					$r.='<option value="'.$severity['severityid'].'">'.$severity['title'].'</option>';
				}
			}
		}
		$r.='</select>';

		return $r;
	}
	//
	function chooserReproducibility($b){
		if($b['orderby']==''){
			$b['orderby']='ordering';
		}
		if($b['direction']==''){
			$b['direction']='ASC';
		}
		if($b['width']>0){
			$b['width']=' style="width: '.$b['width'].'px;"';
		}

		$reproducibilitys=$this->DB->query("
			SELECT *
			FROM reproducibility
			WHERE killed=0
			ORDER BY ".$b['orderby']." ".$b['direction']."
		");
		$r='<select id="'.$b['name'].'" name="'.$b['name'].'"'.$b['width'].'>';
		if($this->DB->numRows()){
			if($b['hasBlank']){
				$r.='<option value="0">'.$b['topname'].'</option>';
			}
			while($reproducibility=$this->DB->fetchArray($reproducibilitys)){
				if($b['selectedid']==$reproducibility['reproducibilityid']){
					$r.='<option value="'.$reproducibility['reproducibilityid'].'" selected>'.$reproducibility['title'].'</option>';
				}else{
					$r.='<option value="'.$reproducibility['reproducibilityid'].'">'.$reproducibility['title'].'</option>';
				}
			}
		}
		$r.='</select>';

		return $r;
	}
	//
	function chooserBugstatus($b){
		if($b['orderby']==''){
			$b['orderby']='ordering';
		}
		if($b['direction']==''){
			$b['direction']='ASC';
		}
		if($b['width']>0){
			$b['width']=' style="width: '.$b['width'].'px;"';
		}

		$bugstatuses=$this->DB->query("
			SELECT *
			FROM bugstatus
			WHERE killed=0
			ORDER BY ".$b['orderby']." ".$b['direction']."
		");
		$r='<select id="'.$b['name'].'" name="'.$b['name'].'"'.$b['width'].'>';
		if($this->DB->numRows()){
			if($b['hasBlank']){
				$r.='<option value="0">'.$b['topname'].'</option>';
			}
			while($bugstatus=$this->DB->fetchArray($bugstatuses)){
				if($b['selectedid']==$bugstatus['bugstatusid']){
					$r.='<option value="'.$bugstatus['bugstatusid'].'" selected>'.$bugstatus['title'].'</option>';
				}else{
					$r.='<option value="'.$bugstatus['bugstatusid'].'">'.$bugstatus['title'].'</option>';
				}
			}
		}
		$r.='</select>';

		return $r;
	}
	//
	function chooserProstatus($b){
		if($b['orderby']==''){
			$b['orderby']='ordering';
		}
		if($b['direction']==''){
			$b['direction']='ASC';
		}
		if($b['width']>0){
			$b['width']=' style="width: '.$b['width'].'px;"';
		}

		$prostatuses=$this->DB->query("
			SELECT *
			FROM prostatus
			WHERE killed=0
			ORDER BY ".$b['orderby']." ".$b['direction']."
		");
		$r='<select id="'.$b['name'].'" name="'.$b['name'].'"'.$b['width'].'>';
		if($this->DB->numRows()){
			if($b['hasBlank']){
				$r.='<option value="0">'.$b['topname'].'</option>';
			}
			while($prostatus=$this->DB->fetchArray($prostatuses)){
				if($b['selectedid']==$prostatus['prostatusid']){
					$r.='<option value="'.$prostatus['prostatusid'].'" selected>'.$prostatus['title'].'</option>';
				}else{
					$r.='<option value="'.$prostatus['prostatusid'].'">'.$prostatus['title'].'</option>';
				}
			}
		}
		$r.='</select>';

		return $r;
	}
	//
	function chooserResolution($b){
			if($b['orderby']==''){
			$b['orderby']='ordering';
		}
		if($b['direction']==''){
			$b['direction']='ASC';
		}
		if($b['width']>0){
			$b['width']=' style="width: '.$b['width'].'px;"';
		}

		$resolutions=$this->DB->query("
			SELECT *
			FROM resolution
			WHERE killed=0
			ORDER BY ".$b['orderby']." ".$b['direction']."
		");
		$r='<select id="'.$b['name'].'" name="'.$b['name'].'"'.$b['width'].'>';
		if($this->DB->numRows()){
			if($b['hasBlank']){
				$r.='<option value="0">'.$b['topname'].'</option>';
			}
			while($resolution=$this->DB->fetchArray($resolutions)){
				if($b['selectedid']==$resolution['resolutionid']){
					$r.='<option value="'.$resolution['resolutionid'].'" selected>'.$resolution['title'].'</option>';
				}else{
					$r.='<option value="'.$resolution['resolutionid'].'">'.$resolution['title'].'</option>';
				}
			}
		}
		$r.='</select>';

		return $r;
	}
	//
	function chooserOs($b){
		if($b['orderby']==''){
			$b['orderby']='ordering';
		}
		if($b['direction']==''){
			$b['direction']='ASC';
		}
		if($b['width']>0){
			$b['width']=' style="width: '.$b['width'].'px;"';
		}

		$oses=$this->DB->query("
			SELECT *
			FROM os
			WHERE killed=0
			ORDER BY ".$b['orderby']." ".$b['direction']."
		");
		$r='<select id="'.$b['name'].'" name="'.$b['name'].'"'.$b['width'].'>';
		if($this->DB->numRows()){
			if($b['hasBlank']){
				$r.='<option value="0">'.$b['topname'].'</option>';
			}
			while($os=$this->DB->fetchArray($oses)){
				if($b['selectedid']==$os['osid']){
					$r.='<option value="'.$os['osid'].'" selected>'.$os['title'].'</option>';
				}else{
					$r.='<option value="'.$os['osid'].'">'.$os['title'].'</option>';
				}
			}
		}
		$r.='</select>';

		return $r;
	}
	//
	function chooserPlatform($b){
		if($b['orderby']==''){
			$b['orderby']='ordering';
		}
		if($b['direction']==''){
			$b['direction']='ASC';
		}
		if($b['width']>0){
			$b['width']=' style="width: '.$b['width'].'px;"';
		}

		$platforms=$this->DB->query("
			SELECT *
			FROM platform
			WHERE killed=0
			ORDER BY ".$b['orderby']." ".$b['direction']."
		");
		$r='<select id="'.$b['name'].'" name="'.$b['name'].'"'.$b['width'].'>';
		if($this->DB->numRows()){
			if($b['hasBlank']){
				$r.='<option value="0">'.$b['topname'].'</option>';
			}
			while($platform=$this->DB->fetchArray($platforms)){
				if($b['selectedid']==$platform['platformid']){
					$r.='<option value="'.$platform['platformid'].'" selected>'.$platform['title'].'</option>';
				}else{
					$r.='<option value="'.$platform['platformid'].'">'.$platform['title'].'</option>';
				}
			}
		}
		$r.='</select>';

		return $r;
	}
		//NWI PMS
	function chooserMftSituation($b){
		if($b['orderby']==''){
			$b['orderby']='ordering';
		}
		if($b['direction']==''){
			$b['direction']='ASC';
		}
		$mftsituations=$this->DB->query("
			SELECT *
			FROM mftsituation
			WHERE killed=0
			ORDER BY ".$b['orderby']." ".$b['direction']."
		");
		if($this->DB->numRows()){
			while($mftsituation=$this->DB->fetchArray($mftsituations)){
				if($b['selectedid']==$mftsituation['situationid']){
					$r.='<input type="radio" name="'.$b['name'].'" value="'.$mftsituation['situationid'].'" checked>'.$mftsituation['title'].'　';
				}else{
					$r.='<input type="radio" name="'.$b['name'].'" value="'.$mftsituation['situationid'].'">'.$mftsituation['title'].'　';
				}
			}
		}

		return $r;
	}

	//外发加工下拉选项
	function chooserOmorders($b){
		if($b['orderby']==''){
			$b['orderby']='entrustid';
		}
		if($b['direction']==''){
			$b['direction']='ASC';
		}
		if($b['width'] > 0){
			$b['width']=' style="width: '.$b['width'].'px;"';
		}
		if($b['condition']){
			$condition=$b['condition'];
		}
		$entrusts=$this->DB->query("
			SELECT entrustid,entrustno FROM `entrust` WHERE `entrust`.killed=0 AND `entrust`.ifVerify=1 AND `entrust`.ifApprove=1 ORDER BY {$b['orderby']} {$b['direction']}");
		$r='<select id="'.$b['name'].'" name="'.$b['name'].'"'.$b['width'].'>';
		if($this->DB->numRows()){
			if($b['hasBlank']){
				$r.='<option value="0">'.$b['topname'].'</option>';
			}
			$i=0;
			while($entrust=$this->DB->fetchArray($entrusts)){
				$title='';
				$items=$this->DB->query("
					SELECT m.title
					FROM entrustitem AS i
					LEFT JOIN material AS m ON (m.materialid=i.materialid)
					WHERE i.killed=0 AND i.entrustid='".$entrust['entrustid']."'
					ORDER BY i.itemid ASC
				");
				if($this->DB->numRows()){
					while($item=$this->DB->fetchArray($items)){
						$title.='-'.$item['title'];
					}
				}
				$r.='<option value="'.$entrust['entrustid'].'">'.$entrust['entrustno'].$title.'</option>';
				$i++;
			}
		}
		$r.='</select>';
		return $r;
	}
	//
	function chooserMode($b){
		$mode=array(array('id'=>0,'title'=>'净需求'),array('id'=>1,'title'=>'毛需求'),array('id'=>2,'title'=>'毛需求(BOM)'));
		foreach($mode as $key => $val){
			if($val['id']==$b['selectedid']){
				$r.='<input type="radio" name="'.$b['name'].'" value="'.$val['id'].'" checked>'.$val['title'].'　';
			}else{
				$r.='<input type="radio" name="'.$b['name'].'" value="'.$val['id'].'">'.$val['title'].'　';
			}
		}
		return $r;
	}
	//
	function chooserWorkcenter($b){
		if($b['orderby']==''){
			$b['orderby']='ordering';
		}
		if($b['direction']==''){
			$b['direction']='ASC';
		}
		if($b['width']>0){
			$b['width']=' style="width: '.$b['width'].'px;"';
		}

		$workcenters=$this->DB->query("
			SELECT *
			FROM workcenter
			WHERE killed=0
			ORDER BY ".$b['orderby']." ".$b['direction']."
		");
		$r='<select id="'.$b['id'].'" name="'.$b['name'].'"'. $this->iif($b['i']==1,'onkeypress="inputAll(this.id)"','').' '.$b['width'].'>';
		if($this->DB->numRows()){
			if($b['hasBlank']){
				$r.='<option value="" selected>'.$b['topname'].'</option>';
			}
			while($workcenter=$this->DB->fetchArray($workcenters)){
				if($b['selectedid']==$workcenter['workcenterid']){
					$r.='<option value="'.$workcenter['workcenterid'].'" selected>'.$workcenter['title'].'</option>';
				}else{
					$r.='<option value="'.$workcenter['workcenterid'].'">'.$workcenter['title'].'</option>';
				}
			}
		}
		$r.='</select>';

		return $r;
	}
	//
	function chooserDispatch($b){
		if($b['orderby']==''){
			$b['orderby']='ordering';
		}
		if($b['direction']==''){
			$b['direction']='ASC';
		}
		if($b['width']>0){
			$b['width']=' style="width: '.$b['width'].'px;"';
		}

		$dispatchs=$this->DB->query("
			SELECT *
			FROM dispatch
			WHERE killed=0 AND statusid<>4 AND ifVerify=1 AND workcenterid=".$b['workcenterid']."
			ORDER BY ".$b['orderby']." ".$b['direction']."
		");
		$r='<select id="'.$b['name'].'" name="'.$b['name'].'"'.$b['width'].'>';
		if($this->DB->numRows()){
			if($b['hasBlank']){
				$r.='<option value="0">'.$b['topname'].'</option>';
			}
			while($dispatch=$this->DB->fetchArray($dispatchs)){
				if($b['selectedid']==$dispatch['dispatchid']){
					$r.='<option value="'.$dispatch['dispatchid'].'" selected>'.$dispatch['dispatchno'].'</option>';
				}else{
					$r.='<option value="'.$dispatch['dispatchid'].'">'.$dispatch['dispatchno'].'</option>';
				}
			}
		}else{
			return false;
		}
		$r.='</select>';

		return $r;
	}
	//
	function chooserRouting($b){
		if($b['orderby']==''){
			$b['orderby']='ordering';
		}
		if($b['direction']==''){
			$b['direction']='ASC';
		}
		if($b['width']>0){
			$b['width']=' style="width: '.$b['width'].'px;"';
		}

		$routings=$this->DB->query("
			SELECT *
			FROM routing
			WHERE killed=0 AND workcenterid=".$b['workcenterid']."
			ORDER BY ".$b['orderby']." ".$b['direction']."
		");
		$r='<select id="'.$b['name'].'" name="'.$b['name'].'"'.$b['width'].'>';
		if($this->DB->numRows()){
			if($b['hasBlank']){
				$r.='<option value="0">'.$b['topname'].'</option>';
			}
			while($routing=$this->DB->fetchArray($routings)){
				if($b['selectedid']==$routing['routingid']){
					$r.='<option value="'.$routing['routingid'].'" selected>'.$routing['title'].'</option>';
				}else{
					$r.='<option value="'.$routing['routingid'].'">'.$routing['title'].'</option>';
				}
			}
		}
		$r.='</select>';

		return $r;
	}
	// Fee form & list
	function formFee($f){
		$k=1;
		if($f['mid']>0){
			$items = $this->DB->query("SELECT * FROM `feeitem` WHERE killed=0 AND module='".$f['module']."' AND mid='".$f['mid']."' ORDER BY itemid ASC, modified ASC");
			if($this->DB->numRows()){
				$this->tbline+=1;
				while($item = $this->DB->fetchArray($items)){
					$feetr .= '<tr class="'.$this->iif($this->rotate(), 'odd', 'even').'">
					<td><input type="hidden" name="feeItemid['.$k.']" value="'.$item['itemid'].'">'.$this->chooserFee(array('name'=>'feeid['.$k.']', 'hasBlank'=>1, 'width'=>250, 'selectedid'=>$item['feeid'])).'</td>
					<td><input type="text" name="feeCaption['.$k.']" value="'.$item['caption'].'" size="40"></td>
					<td><input type="text" name="feeRemark['.$k.']" value="'.$item['remark'].'" size="30"></td>
					<td><input type="text" name="feeDiscount['.$k.']" value="'.$item['discount'].'" size="3"></td>
					<td><input type="text" name="feeQuantity['.$k.']" value="'.$item['quantity'].'" size="4"></td>
					<td>'.$this->chooserUnit(array('name'=>'feeUnitid['.$k.']', 'hasBlank'=>1, 'width'=>100, 'selectedid'=>$item['unitid'])).'</td>
					<td><input type="text" name="feePrice['.$k.']" value="'.$item['price'].'" size="5"></td>
					<td><input type="checkbox" name="feeKill['.$k.'] value="'.$item['itemid'].'"></td>
					</tr>';
					$k++;
				}
			}
			$feetr.='<tr><td colspan="12">新建费用项目明细</td></tr>';
		}
		for($i = $k; $i < ($k+2); $i++){
			$feetr .= '<tr class="'.$this->iif($this->rotate(), 'odd', 'even').'">
			<td>'.$this->chooserFee(array('name'=>'feeid['.$i.']', 'hasBlank'=>1, 'width'=>250)).'</td>
			<td><input type="text" name="feeCaption['.$i.']" value="'.$fee['caption'].'" size="40"></td>
			<td><input type="text" name="feeRemark['.$i.']" value="'.$fee['remark'].'" size="30"></td>
			<td><input type="text" name="feeDiscount['.$i.']" value="100" size="3"></td>
			<td><input type="text" name="feeQuantity['.$i.']" value="'.$fee['quantity'].'" size="5"></td>
			<td>'.$this->chooserUnit(array('name'=>'feeUnitid['.$i.']', 'hasBlank'=>1, 'width'=>100, 'selectedid'=>$this->input['unitid'])).'</td>
			<td><input type="text" name="feePrice['.$i.']" value="'.$fee['price'].'" size="5"></td>
			<td><input type="checkbox" name="feeKill['.$i.'] "></td>
			</tr>';
		}
return <<<EOF
<table class="hundred">
<thead>
<tr>
	<th colspan="12">费用项目明细表 (可填)</th>
</tr>
</thead>
<tr><td>费用项目<span class="red bold">*</span></td><td>说明</td><td>备注</td><td>折扣%</td><td>数量<span class="red bold">*</span></td><td>单位<span class="red bold">*</span></td><td>单价<span class="red bold">*</span></td><td>删</td></tr>
<tbody class="small">
{$feetr}
<tr>
	<td colspan="8" class="gray small">注：①如行数不够，请保存后再进入修改页面。②至少有“名称”、“数量”、“单位”、“单价”等四个项目，该条明细才会被保存。③折扣值范围“1-100”，不填默认为“100”。</td>
</tr>
</tbody>
</table>
EOF;
	}
	// discount form & list
	function formDiscount($f){
		$k=1;
		if($f['mid']>0){
			$items = $this->DB->query("SELECT * FROM `discount` WHERE killed=0 AND module='".$f['module']."' AND mid='".$f['mid']."' ORDER BY modified ASC, discountid DESC");
			if($this->DB->numRows()){
				$this->tbline+=1;
				while($item = $this->DB->fetchArray($items)){
					$a0=$s0=' checked';
					$a1=$s1='';
					if($item['addition']=='+'){
						$a1=$a0;
						$a0='';
					}
					if($item['sign']=='null'){
						$s1=$s0;
						$s0='';
					}
					$itemtr .= '<tr class="'.$this->iif($this->rotate(), 'odd', 'even').'">
					<td><input type="hidden" name="discountid['.$k.']" value="'.$item['discountid'].'"><input type="text" name="discountReason['.$k.']" value="'.$item['reason'].'" size="40"></td>
					<td><label for="discountAddition'.$k.'0"><input type="radio" name="discountAddition['.$k.']" value="-" id="discountAddition'.$k.'0"'.$a0.'>减去(<span class="big bold">-</span>)</label> <label for="discountAddition'.$k.'1"><input type="radio" name="discountAddition['.$k.']" value="+" id="discountAddition'.$k.'1"'.$a1.'>加上(<span class="middle bold">+</span>)</label></td>
					<td><input type="text" name="discount['.$k.']" value="'.$item['discount'].'" size="10"></td>
					<td><label for="discountSign'.$k.'0"><input type="radio" name="discountSign['.$k.']" value="%" id="discountSign'.$k.'0"'.$s0.'>百分之(<span class="bold">%</span>)</label> <label for="discountSign'.$k.'1"><input type="radio" name="discountSign['.$k.']" value="null" id="discountSign'.$k.'1"'.$s1.'>无</label></td>
					<td><input type="checkbox" name="discountKill['.$k.'] value="'.$item['discountid'].'"></td>
					</tr>';
					$k++;
				}
			}
			$itemtr.='<tr><td colspan="12">新建折扣明细</td></tr>';
		}
		for($i = $k; $i < ($k+2); $i++){
			$itemtr .= '<tr class="'.$this->iif($this->rotate(), 'odd', 'even').'">
			<td><input type="text" name="discountReason['.$i.']" value="'.$item['itemReason'].'" size="40"></td>
			<td><label for="discountAddition'.$i.'0"><input type="radio" name="discountAddition['.$i.']" value="-" id="discountAddition'.$i.'0" checked>减去(<span class="big bold">-</span>)</label> <label for="discountAddition'.$i.'1"><input type="radio" name="discountAddition['.$i.']" value="+" id="discountAddition'.$i.'1">加上(<span class="middle bold">+</span>)</label></td>
			<td><input type="text" name="discount['.$i.']" value="'.$item['Discount'].'" size="10"></td>
			<td><label for="discountSign'.$i.'0"><input type="radio" name="discountSign['.$i.']" value="%" id="discountSign'.$i.'0" checked>百分之(<span class="bold">%</span>)</label> <label for="discountSign'.$i.'1"><input type="radio" name="discountSign['.$i.']" value="null" id="discountSign'.$i.'1">无</label></td>
			<td><input type="checkbox" name="discountKill['.$i.'] "></td>
			</tr>';
		}
return <<<EOF
<table class="hundred">
<thead>
<tr>
	<th colspan="5">折扣 明细表</th>
</tr>
</thead>
<tbody>
<tr><td title="Reason">折扣说明(理由)<span class="red">*</span></td><td title="Addition">加/减</td><td title="">数量<span class="red">*</span></td><td>百分比</td><td>删</td></tr>
{$itemtr}
<tr class="even">
	<td colspan="5" class="gray small">注：①如行数不够，请保存后再进入修改页面。②至少有“折扣说明”、“数量”等两个项目，该折扣明细才会被保存。③如折扣是百分比"%"，数量值范围“0-100”，否则无限制。</td>
</tr>
</tbody>
</table>
EOF;
	}
	// update for Fee
	function updateFee($u){
		if($u['module']=='' OR $u['mid']<=0){
			return false;
		}
		for($i = 1; $i < count($this->input['feeid'])+1; $i++){
			$this->input['feeQuantity'][$i]=intVal($this->input['feeQuantity'][$i]);
			$this->input['feePrice'][$i]=floatVal($this->input['feePrice'][$i]);
			$this->input['feeDiscount'][$i]=intVal($this->input['feeDiscount'][$i]);
			if($this->input['feeDiscount'][$i]<1 OR $this->input['feeDiscount'][$i]>100){
				$this->input['feeDiscount'][$i]=100;
			}
			$amount = $this->input['feeQuantity'][$i] * $this->input['feePrice'][$i];
			if($this->input['feeDiscount'][$i]<100){
				$amount = $amount * $this->input['feeDiscount'][$i] / 100;
			}
			$amount = number_format($amount, 2, '.', '');
			if($this->input['feeItemid'][$i]>0){
				if($this->input['feeKill'][$i]=='on'){
					$this->DB->query("UPDATE `feeitem` SET killer='".$this->user['userid']."', killed=".TIMENOW."  WHERE itemid='".$this->input['feeItemid'][$i]."'");
				}else{
					$this->DB->query("
						UPDATE `feeitem` SET
							`feeid`='".$this->input['feeid'][$i]."',
							`caption`='".$this->input['feeCaption'][$i]."',
							`remark`='".$this->input['feeRemark'][$i]."',
							`discount`='".$this->input['feeDiscount'][$i]."',
							`quantity`='".$this->input['feeQuantity'][$i]."',
							`unitid`='".$this->input['feeUnitid'][$i]."',
							`price`='".$this->input['feePrice'][$i]."',
							`amount`='".$amount."',
							`modified`='".TIMENOW."',
							`modifier`='".$this->user['userid']."'
						WHERE itemid='".$this->input['feeItemid'][$i]."'
					");
					$quantity+=$this->input['feeQuantity'][$i];
				}
			}elseif($this->input['feeid'][$i]>0 AND $this->input['feeQuantity'][$i]!='' AND $this->input['feeUnitid'][$i]>0 AND $this->input['feePrice'][$i]>=0){
				$this->DB->query("
					INSERT INTO `feeitem` (`module`,`mid`,`feeid`,`caption`,`remark`,`quantity`,`discount`,`unitid`,`price`,`amount`,`modified`,`creator`,`created`)
					VALUES (
					'".$u['module']."', '".$u['mid']."', '".$this->input['feeid'][$i]."', '".$this->input['feeCaption'][$i]."', '".$this->input['feeRemark'][$i]."', '".$this->input['feeQuantity'][$i]."', '".$this->input['feeDiscount'][$i]."', '".$this->input['feeUnitid'][$i]."', '".$this->input['feePrice'][$i]."', '".$amount."', '".TIMENOW."', '".$this->user['userid']."', '".TIMENOW."')
				");
				$quantity+=$this->input['feeQuantity'][$i];
			}
		}
		return true;
	}

	// update for discount
	function updateDiscount($u){
		if($u['module']=='' OR $u['mid']<=0){
			return false;
		}
		for($i = 1; $i < count($this->input['discountReason'])+1; $i++){
			$this->input['discount'][$i]=floatVal($this->input['discount'][$i]);
			$this->input['discount'][$i]=trim($this->input['discount'][$i]);
			if($this->input['discountSign'][$i]=='%'){
				if($this->input['discount'][$i]>0 OR $this->input['discount'][$i]<100){
					$this->input['discount'][$i]=intVal($this->input['discount'][$i]);
				}else{
					$this->input['discount'][$i]=0;
				}
			}else{
				$this->input['discount'][$i]=floatVal($this->input['discount'][$i]);
			}
			if($this->input['discountid'][$i]>0){
				if($this->input['discountKill'][$i]=='on'){
					$this->DB->query("UPDATE `discount` SET killer='".$this->user['userid']."', killed=".TIMENOW."  WHERE discountid='".$this->input['discountid'][$i]."'");
				}else{
					$this->DB->query("
						UPDATE `discount` SET
							`reason`='".$this->input['discountReason'][$i]."',
							`addition`='".$this->input['discountAddition'][$i]."',
							`discount`='".$this->input['discount'][$i]."',
							`sign`='".$this->input['discountSign'][$i]."',
							`remark`='".$this->input['discountRemark'][$i]."',
							`modified`='".TIMENOW."',
							`modifier`='".$this->user['userid']."'
						WHERE discountid='".$this->input['discountid'][$i]."'
					");
				}
			}elseif($this->input['discount'][$i]>=0 AND $this->input['discountReason'][$i]!=''){
				$this->DB->query("
					INSERT INTO `discount` (`module`, `mid`, `reason`,`addition`,`discount`,`sign`,`remark`,`modified`,`creator`,`created`)
					VALUES (
					'".$u['module']."', '".$u['mid']."', '".$this->input['discountReason'][$i]."', '".$this->input['discountAddition'][$i]."', '".$this->input['discount'][$i]."', '".$this->input['discountSign'][$i]."',  '".$this->input['discountRemark'][$i]."', '".TIMENOW."', '".$this->user['userid']."', '".TIMENOW."')
				");
			}
		}
		return true;
	}
	function relatedBankDetail($r){
		if($r['supplierid']>0){
			$condition=' AND supplierid='.$r['supplierid'];
		}
		if($r['customerid']>0){
			$condition=' AND customerid='.$r['customerid'];
		}
		$bankDetails=$this->DB->query("
			SELECT bankDetailid,bankAccountName,bank,bankAccount
			FROM bankdetail
			WHERE killed=0 ".$condition."
			ORDER BY ordering,bankDetailid ASC
		");
		if($rtn['num']=$this->DB->numRows()){
			$rtn['panel']='<table class="hundred"><thead><tr><th width="250">户名</th><th width="500">开户行</th><th width="250">银行账号</th></tr></thead>';
			while($bankDetail=$this->DB->fetchArray($bankDetails)){
				$rtn['panel'].='<tr class="'.$this->iif($this->rotate(),'odd','even').'">
					<td>'.$bankDetail['bankAccountName'].'</td>
					<td>'.$bankDetail['bank'].'</td>
					<td>'.$bankDetail['bankAccount'].'</td>
				</tr>';
			}
			$rtn['panel'].='</table>';
			$rtn['count']='('.$rtn['num'].')';
		}else{
			$rtn['off']=' disabled';
		}
		return $rtn;
	}
	/**
	 * child or parent of bom 的list
	 * @author creator zhourui 2013-11-14 12:00
	 * @access public
	 * @param array $b 参数传递 ： materialid, itemid
	 * @return string $str 返回bom的list
	 * @throws none 没有异常
	 */

	 function relatedfeedback($r){
		if($r['customerid']>0){
			$r['condition'] .= " AND `feedback`.customerid='".$r['customerid']."'";
		}
		if($r['orderby'] == ''){
			$r['orderby'] = '`feedback`.modified';
		}
		if($r['direction'] == ''){
			$r['direction'] = 'DESC';
		}
		if($r['limit'] == ''){
			$r['limit'] = '0, 100';
		}
		$feedbacks=$this->DB->query("
			SELECT feedback.*, c.username AS creator  FROM `feedback`
			LEFT JOIN `user` AS c ON (c.userid=`feedback`.creator)
			WHERE `feedback`.killed=0".$r['condition']."
			ORDER BY ".$r['orderby']." ".$r['direction']."
			LIMIT ".$r['limit']."
		");
		$rtn['num'] = $this->DB->numRows();
		if($rtn['num']){
			$rtn['panel']='<table class="hundred"><thead><tr><th>反馈时间</th><th>反馈信息</th><th>备注</th><th>创建人</th><th>创建时间</th><th>操作</th></tr><tbody>';
			while($feedback = $this->DB->fetchArray($feedbacks)){
				if($feedback['customerid']>0){
					$options = '<a href="/s.php?module=customer&action=removeFeedback&feedbackid='.$feedback['feedbackid'].'" onclick="return confirm(\'你确定要删除这张反馈信息单吗？(删除不可恢复！)\')">删除</a>　<a href="/s.php?module=customer&action=updateFeedback&feedbackid='.$feedback['feedbackid'].'">修改</a>　<a href="/p.php?module=customer&action=&inquiryid='.$feedback['inquiryid'].'" target="_blank">打印</a>'; 
				}
				$title = '';
				$created=date('Y-m-d',$feedback['created']);
				$modified=date('Y-m-d',$feedback['modified']);
				if($feedback['dateline']!='')$dateline=date('Y-m-d',$feedback['dateline']);
				if($feedback['content']!='')$content=$feedback['content'];
				$rtn['panel'].='<tr class="'.$this->rotateLine().'">
				<td>'.$dateline.'</td>
				<td>'.$content.'</td>
				<td>'.$feedback['remark'].'</td>
				<td>'.$feedback['creator'].'</td>
				<td>'.$created.'</td>
				<td>'.$options.'</td>
				<tr>';
			}
			$rtn['panel'] .= '</tbody></table>';
			$rtn['count'] = ' ('.$rtn['num'].')';
		}else{
			$rtn['off']=' disabled';
		}
		return $rtn;	
	}
	// 互访
	
	function relatedBom($b){
		// 版本信息
		if(!$b['materialid']){
			return false;
		}
		if($b['itemid']>0){
			$condition=' AND itemid='.$b['itemid'];
		}
		$versions=$this->DB->query("
			SELECT versionid,title,itemid,materialid,qualified,price,price1
			FROM materialversion 
			WHERE killed=0 AND materialid='".$b['materialid']."' $condition
			ORDER BY created DESC
		");
		$hasChild=$hasParent=0;
		if($this->DB->numRows()){
			$i=$j=$k=1;
			while($version=$this->DB->fetchArray($versions)){
				$materialInfo=$this->getMaterial(array('materialid'=>$version['materialid'],'itemid'=>$version['itemid']));
				if($b['show']=='child'){// bom 子项
					$child=$this->bomChildList(array('parentid'=>$version['materialid'],'versionid'=>$version['versionid'],'materialitemid'=>$version['itemid'],'loopNum'=>1));
					if(!empty($child['bom'])){
						$hasChild=1;
						$m=1;
						$rtn['panel'].='<table class="hundred small"><thead><tr><th colspan=12 class="middle bold"><span class="minus"></span><a href="'.$materialInfo['url'].'">'.$materialInfo['no'].'</a>　'.$materialInfo['material'].'　'.$materialInfo['standard'].'　　版本：'.$version['title'].'　　单价：<span class="bold red">'.$this->iif($this->purviews(array('module'=>'material','action'=>'viewPrice','final'=>1)),$version['price'],'—').'</span><span class="small gray">(<a href="/s.php?module=material&action=updatePriceByBom&materialid='.$version['materialid'].'&versionid='.$version['versionid'].'" target="__blank">手动更新单价</a> <a href="/s.php?module=material&action=updatePriceStatus&versionid='.$version['versionid'].'">单价状态</a>)</span><span class="right"><a href="/s.php?module=bom&action=showAll&versionid='.$version['versionid'].'" target="__blank">查看所有BOM</a>　<a href="/s.php?module=bom&action=copy&materialid='.$version['materialid'].'&parentVersionid='.$version['versionid'].'">子项复制</a>　<a href="/s.php?module=bom&action=remove&materialid='.$version['materialid'].'&parentVersionid='.$version['versionid'].'">删除</a>　<a href="/s.php?module=bom&action=update&materialid='.$version['materialid'].'&parentVersionid='.$version['versionid'].'">修改BOM</a></span></th></tr></thead><tbody id="childBom"><tr class="center bold"><td width="50">ID</td><td width="850"  align="left">物料清单物资</td><td width="50"  align="left">版本</td><td width="50">单价</td><td width="50">单位</td><td align="left">库存</td><td width="50">数量</td><td width="80">损耗率</td><td width="60">来源</td><td width="50">通用</td><td width="50">成本</td><td width="180">操作</td></tr>';
						foreach($child['bom'] as $key=>$val){
							$opreate=$showChildren='';
							if($val['originid']!=2){
								$nextChild=$this->DB->queryFirst("SELECT COUNT(bomid) AS count FROM bom WHERE killed=0 AND materialid='".$val['materialid']."' AND parentitemid='".$val['materialitemid']."' AND parentVersionid='".$val['versionid']."'");
								if($nextChild['count']==0){
									$opreate=' <a href="/s.php?module=bom&action=add&attrid='.$val['attrid'].'&versionid='.$val['versionid'].'">添加物料清单</a>';
								}else{
									$showChildren='<span class="hand plus" onclick="javascript:dc.bom.showChildren('.$val['versionid'].')"></span>';
									$opreate=' <a href="/s.php?module=material&action=updatePriceByBom&materialid='.$val['materialid'].'&versionid='.$val['versionid'].'">更新单价</a>';
									
								}
							}							
							$rtn['panel'].='<tr class="'.$this->iif($this->rotate(),'odd','even').'" id="versionid'.$val['versionid'].'" rank="0">
								<td align="right">'.$showChildren.'1.'.$m.'</td>
								<td><a class="normal" href="'.$val['url'].'">'.$val['no'].'</a>　'.$val['title'].'　'.$val['standard'].'</a></td>
								<td>'.$val['version'].'</td>
								<td class="bold '.$this->iif($val['price']==0,'black','red').'">'.$this->iif($this->purviews(array('module'=>'material','action'=>'viewPrice','final'=>1)),$val['price'],'—').'</td>
								<td>'.$val['unit'].'</td>
								<td><span class="bold">'.$val['qualified'].'</span></td>
								<td>'.$val['quantity'].'</td>
								<td>'.$val['attritionRate'].'‰</td>
								<td>'.$this->iif($val['originid']==1,'自产',$this->iif($val['originid']==2,'外购','外发加工')).'</td>
								<td>'.$this->iif($val['ifGeneral']==1,'是','否').'</td>
								<td>'.$this->iif($val['ifPrice']==1,'是','否').'</td>
								<td class="small"><a href="/s.php?module=bom&action=updateMany&bomid='.$key.'">修改（多）</a>'.$opreate.'</td></tr>';

							if($b['rank']>0){
								$symbol='';
								if($b['rank']==1){
									if($child['count']==1){
										if($version['price1']>0){
											$symbol.='　┌';
											$s1='　└';
										}else{
											$symbol.='　[';
										}
									}else{
										if($m==1){
											$symbol='　┌';
										}elseif($m==$child['count']){
											$symbol='　└';
										}else{
											$symbol='　│';
										}
									}
								}else{
									for($k=1;$k<$b['rank'];$k++){
										$symbol.='　│';
									}
									if($child['count']>1){				
										if($m==1){
											$symbol.='　┌';
										}elseif($m==$child['count']){
											$symbol.='　└';
										}else{
											$symbol.='　│';
										}
									}else{
										if($version['price1']>0){
											$s1=$symbol.'　└';
										}else{
											$symbol.='　[';
										}
									}
								}
								
								$rtn['tr'].='<tr class="'.$this->iif($this->rotate(),'odd','even').'" id="versionid'.$val['versionid'].'" rank="'.$b['rank'].'">
									<td align="right">'.$showChildren.'</td>
									<td><span class="gray bold middle">'.$symbol.'</span>【'.($b['rank']+1).'.'.$m.'】<a href="'.$val['url'].'">'.$val['no'].'</a>　'.$val['title'].'　'.$val['standard'].'</a></td>
									<td>'.$val['version'].'</td>
									<td class="bold '.$this->iif($val['price']==0,'black','red').'">'.$this->iif($this->purviews(array('module'=>'material','action'=>'viewPrice','final'=>1)),$val['price'],'—').'</td>
									<td>'.$val['unit'].'</td>
									<td><span class="bold">'.$val['qualified'].'</span></td>
									<td>'.$val['quantity'].'</td>
									<td>'.$val['attritionRate'].'‰</td>
									<td>'.$this->iif($val['originid']==1,'自产',$this->iif($val['originid']==2,'外购','外发加工')).'</td>
									<td>'.$this->iif($val['ifGeneral']==1,'是','否').'</td>
									<td>'.$this->iif($val['ifPrice']==1,'是','否').'</td>
									<td class="small"><a href="/s.php?module=bom&action=updateMany&bomid='.$key.'">修改（多张bom）</a>'.$opreate.'</td>
								</tr>';
							}
							
							$m++;
						}
						if($version['price1']>0){
							$rtn['panel'].='<tr class="'.$this->iif($this->rotate(),'odd','even').'" id="versionid'.$val['versionid'].'" rank="'.$b['rank'].'">
								<td align="right">1.'.$m.'</td>
								<td>外发加工 加工费</td>
								<td></td>
								<td class="bold red">'.$this->iif($this->purviews(array('module'=>'material','action'=>'viewPrice','final'=>1)),$version['price1'],'—').'</td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td></tr>';
							$rtn['tr'].='<tr class="'.$this->iif($this->rotate(),'odd','even').'" id="versionid'.$val['versionid'].'" rank="'.$b['rank'].'">
								<td></td>
								<td><span class="gray bold middle">'.$s1.'</span>【'.($b['rank']+1).'.'.$m.'】外发加工 加工费</td>
								<td></td>
								<td class="bold red">'.$this->iif($this->purviews(array('module'=>'material','action'=>'viewPrice','final'=>1)),$version['price1'],'—').'</td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td></tr>';
							}
						$rtn['panel'].='</tbody></table>';
					}else{
						$rtn['off']=' disabled';
					}
				}elseif($b['show']=='parent'){	// bom父信息
					$parent=$this->bomParentList(array('childid'=>$version['materialid'],'versionid'=>$version['versionid'],'materialitemid'=>$version['itemid']));
					if(!empty($parent)){
						$hasParent=1;
						$rtn['panel'].='<table class="hundred"><thead><tr><th width="30">ID</th><th width="900">父物资</th><th>版本</th><th width="50">单价</th><th width="60">所需数量</th><th width="80">损耗率</th><th width="150">操作</th></tr></thead><tbody>';
						$rtn['panel'].='<tr><td colspan=7 class="middle bold"><a href="'.$materialInfo['url'].'">'.$materialInfo['no'].'</a>　'.$materialInfo['material'].'　'.$materialInfo['standard'].'　　版本：'.$version['title'].'</span></td></tr>';
						$quantitytr.='<table name="quantity" class="hundred"><thead><tr><th colspan=4>物资库存总数</th></tr></thead><tbody><tr><td width="10">ID</td><td>包含<b>'.$materialInfo['no'].'　'.$materialInfo['material'].'　'.$materialInfo['standard'].' </b> 版本： <span class="red bold">'.$version['title'].'</span> 的物资</td><td width="120">该物资数量</td><td width="120">包含数量</td></tr>';
						foreach($parent as $val){
							$rtn['panel'].='<tr class="'.$this->iif($this->rotate(),'odd','even').'" nohover>
								<td>'.$j.'</td>
								<td><a href="'.$val['url'].'">'.$val['no'].'</a>　'.$val['title'].'　'.$val['standard'].'</td>
								<td>'.$val['version'].'</td>
								<td class="bold red">'.$this->iif($this->purviews(array('module'=>'material','action'=>'viewPrice','final'=>1)),$val['price'],'—').'</td>
								<td>'.$val['quantity'].$val['unit'].'</td>
								<td>'.$val['attritionRate'].'‰</td>
								<td><a href="/s.php?module=bom&action=remove&bomid='.$val['bomid'].'" class="small">删除</a>　　<a class="small gray" href="/s.php?module=material&action=updatePriceByBom&materialid='.$val['materialid'].'&versionid='.$val['versionid'].'" target="__blank">(手动更新单价)</a></td></tr>';
								//获得物资库存总数(在库数以及组成配件数)
								$quantity=ceil($val['qualified']*$val['quantity']);
								$quantitytr.='<tr class="'.$this->iif($this->rotate(),'odd','even').'"><td>'.$j.'</td>
									<td><a href="'.$val['url'].'">'.$val['no'].'</a>　'.$val['title'].'　'.$val['standard'].'</td>
									<td>'.$val['qualified'].'</td><td>'.$quantity.'</td></tr>';
								$total+=$quantity;
							$j++;
						}
						$rtn['panel'].='</tbody></table>';
						$quantitytr.='<tr><td>'.($j+1).'</td><td class="bold middle">在库物资</td><td>'.$version['qualified'].'</td><td>'.$version['qualified'].'</td></tr><tr><td colspan=3 class="center bold">库　存　总　计</td><td>'.($total+$version['qualified']).'</td></tr></tbody></table>';
					}else{
						$rtn['off']=' disabled';
					}
				}
			}
		}
		return $rtn;
	}
	//
	function relatedAttribute($b){
		if($b['module']=='' AND $b['mid']==''){
			return false;
		}

		$names=$this->DB->query("
			SELECT nameid,title
			FROM  attributename 
			WHERE killed=0 AND module='".$b['module']."' AND mid='".$b['mid']."'
			ORDER BY ordering DESC
			");
		if($this->DB->numRows($names)){
			$r='<table class="hundred"><thead><tr><th colspan="3">属性<span class="right"><a href="/s.php?module=attribute&action=update&'.$b['module'].'id='.$b['mid'].'">修改属性</a></span></th></tr></thead><tbody><tr class="even bold"><td width="230">属性名称</td><td>属性名称的值</td><td width="150">属性操作</td></tr>';
			while($name=$this->DB->fetchArray($names)){
				$r.='<tr class="'.$this->iif($this->rotate(),'odd','even').'"><td>'.$name['title'].'</td><td>';
				$values=$this->DB->query("
					SELECT valueid,title,`default`
					FROM  attributevalue 
					WHERE killed=0 AND nameid='".$name['nameid']."'
					ORDER BY created ASC
			");
				if($this->DB->numRows($names)){
					while($value=$this->DB->fetchArray($values)){
						$r.='[ ';
						if($value['default']==1){
							$r.='<span class="bold red">'.$value['title'].'</span>';
					}else{
							$r.=$value['title'];
					}
						$r.=' ]　';
					}
				}
				$r.='<td><a href="/s.php?module=attribute&action=remove&'.$b['module'].'id='.$b['mid'].'&nameid='.$name['nameid'].'" onclick="return confirm(\'你确定要删除这个属性 '.$name['title'].' 吗（删除后不可恢复）？\');">删除属性</a></td></tr>';
			}
			$r.='</tbody></table>';
		}

		return $r;
	}
	// 客户
	function relatedCustomer($r){
		$client = $this->DB->queryFirst("SELECT * FROM `customer` WHERE customerid='".$r['id']."'");
		if($client){
			$rtn['link'] = '<a href="/s.php?module=customer&action=view&customerid='.$r['id'].'" target="_blank">'.$client['title'].'</a>';
$rtn['panel'] = <<<EOF
<table class="hundred">
<tr class="odd">
	<td width="80">英文名称：</td><td class="middle">{$rtn['link']}</td>
	<td width="80">简　　称：</td><td class="middle">{$client['abbr']}</td>
</tr>
<tr class="even">
	<td>中文名称：</td><td class="middle">{$client['cntitle']}</td>
	<td>商业类型：</td><td class="middle">{$client['business']}</td>
</tr>
<tr class="odd">
	<td>英文地址：</td><td class="middle">{$client['address']}</td>
	<td>国　　家：</td><td class="middle">{$region}</td>
</tr>
<tr class="even">
	<td>中文地址：</td><td class="middle">{$client['cnaddress']} {$postalcode}</td>
	<td>网　　站：</td><td class="middle">{$client['website']}</td>
</tr>
<tr class="odd">
	<td>主联系人：</td><td class="middle">{$client['linkman']} {$position}</td>
	<td>次联系人：</td><td class="middle">{$client['linkman2']} {$position2}</td>
</tr>
<tr class="even">
	<td>电　　话：</td><td class="middle">{$client['telephone']}</td>
	<td>邮　　箱：</td><td class="middle">{$client['email']}</td>
</tr>
<tr class="odd">
	<td>手　　机：</td><td class="middle">{$client['mobile']}</td>
	<td>传　　真：</td><td class="middle">{$client['fax']}</td>
</tr>
<tr class="even">
	<td valign="top">银行资料：</td><td>{$client['bankdetail']}</td>
	<td valign="top">备　　注：</td><td>{$client['remark']}</td>
</tr>
</table>
EOF;
		}else{
			$rtn['off']=' disabled';
		}
		return $rtn;
	}

	function relatedConsignment($r){
		//echo $r['supplierid'];
		$inbounds=$this->DB->query("
				SELECT i.inboundid,i.inboundno,i.supplierid,i.porderid,
					s.title AS supplier,
					po.orderno AS porder
				FROM `inbound` AS i
				LEFT JOIN `supplier` s ON (s.supplierid=i.supplierid)
				LEFT JOIN `porder` po ON (po.porderid=i.porderid)
				WHERE i.killed=0 AND i.ifVerify=1 AND i.ifComplete=2  AND i.typeid=1 AND i.supplierid='".$r['supplierid']."'
			");	
		if($rtn['num']=$this->DB->numRows()){
			$i=1;
			echo $inbound['inboundid'];
			while($inbound = $this->DB->fetchArray($inbounds)){
				$item="";
				$evaluates=$this->DB->query("SELECT * FROM `consignevaluate` WHERE  inboundid='".$inbound['inboundid']."' ORDER BY dateline DESC  
				");
				if($this->DB->numRows()){
					$i=1;
					$item.='
<tr><th colspan="9">供应商评价记录</th></tr>
<tr><th>编号</th><th>评价日期</th><th>产品质量</th><th>发货速度</th><th>价格评价</th><th>服务</th><th colspan="2">备注</th><th>操作</th></tr>
<tbody>';
					while($evaluate=$this->DB->fetchArray($evaluates)){
						$operation="";
						if($evaluate['ifApprove']==0){
							if($evaluate['ifVerify']==0){ 
								$operation.='<a href="/s.php?module=psupplier&action=verifyconsignevaluate&consignevaluateid='.$evaluate['consignevaluateid'].'">审核</a>　<a href="/s.php?module=psupplier&action=updateconsignevaluate&consignevaluateid='.$evaluate['consignevaluateid'].'">修改</a>';
							}else{
								$operation.='<a href="/s.php?module=psupplier&action=approveconsignevaluate&consignevaluateid='.$evaluate['consignevaluateid'].'">审批</a>　<a href="/s.php?module=psupplier&action=updateconsignevaluate&consignevaluateid='.$evaluate['consignevaluateid'].'">修改</a>';
								}
						}else{
							$operation.=' ';
						}
						if($evaluate['dateline']!=0){
							$dateline=date('Y-m-d',$evaluate['dateline']);
						}
						if($evaluate['quality']==1){
							$quality="合格";
						}else{
							$quality="不合格";
						}
						if($evaluate['delivery']==1){
							$delivery="准时";
						}else{
							$delivery="不准时";
						}
						$item.='<tr class=odd>
							<td>'.$i.'</td>
							<td>'.$dateline.'</td>
							<td>'.$quality.'</td>
							<td>'.$delivery.'</td>
							<td>'.$evaluate['price'].'</td>
							<td>'.$evaluate['service'].'</td>
							<td colspan=2>'.$remark.'</td>
							<td>'.$operation.'</td></tr>';
						$i++;
					}
					$item.="</tbody>";
				}
				$inboundItemtr=''; //获取入库单的入库批次列表
				$inboundItems=$this->DB->query("
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
				if($this->DB->numRows()){
					$i=1;
					$inboundItemtr='<tr class="even center"><td width="25">ID</td><td width="190">条码</td><td width="80">批次编号</td><td width="160">物资</td><td width="40">签收人</td><td width="60">批号时间</td><td width="40">已入数</td><td width="120">入库时间</td><td width="270"></td></tr>';
					while($inboundItem=$this->DB->fetchArray($inboundItems)){
						$in=$this->DB->queryFirst("SELECT SUM(quantity) AS count FROM inbounditem WHERE killed=0 AND itemid='".$inboundItem['itemid']."'");
						$batch=$this->DB->queryFirst("SELECT * FROM batch WHERE killed=0 AND batchid='".$inboundItem['batchid']."'");
						$materialInfo=$this->getMaterial(array('materialid'=>$inboundItem['materialid'],'itemid'=>$inboundItem['materialitemid']));
						if($materialInfo['ifPerBarcode']==1);{
							$status='';
							$startno=$this->DB->queryFirst("
								SELECT number
								FROM childBarcode 
								WHERE versionid={$inboundItem['versionid']} AND inboundid={$inbound['inboundid']} 
								ORDER BY title ASC 
								LIMIT 0,1
							");
							$endno=$this->DB->queryFirst("
								SELECT number
								FROM childBarcode 
								WHERE versionid={$inboundItem['versionid']} AND inboundid={$inbound['inboundid']} 
								ORDER BY title DESC 
								LIMIT 0,1
							");
							$childBarcode=$this->DB->queryFirst("SELECT COUNT(*) AS count FROM childBarcode WHERE versionid={$inboundItem['versionid']} AND inboundid={$inbound['inboundid']} AND barcodeid={$inboundItem['barcodeid']} LIMIT 0,1");
							$info='共<b>'.$this->iif($childBarcode['count'],$childBarcode['count'],0).'</b>条';
							if($childBarcode['count']!=$in['count']){
								$quantity=$in['count']-$childBarcode['count'];
								$status='<a href="javascript:dc.barcode.addBarcode(\'item'.$i.'\')">生成流水条码</a>';
							}else{
									$status='生成完成';
							}
						}
						$inboundItemtr.='<tr class="'.$this->iif($this->rotate(), 'odd', 'even').'" id="item'.$i.'">
							<td><input type="hidden" name="itemid" value="'.$inboundItem['itemid'].'">
								<input type="hidden" name="versionid" value="'.$inboundItem['versionid'].'">
								<input type="hidden" name="quantity" value="'.$quantity.'">
								<input type="hidden" name="barcodeid" value="'.$inboundItem['barcodeid'].'">'.$i.'</td>
							<td><a href="/s.php?module=barcode&action=view&barcodeid='.$inboundItem['barcodeid'].'">'.$inboundItem['barcode'].'</a></td>
							<td><a href="/s.php?module=batch&action=view&batchid='.$inboundItem['batchid'].'">'.$inboundItem['batchno'].'</a></td>
								<td><a href="'.$materialInfo['url'].'">'.$materialInfo['no'].'</a>　'.$materialInfo['material'].'　'.$materialInfo['standard'].'</td>
							<td>'.$inboundItem['creator'].'</td>
							<td>'.date('Y-m-d',$batch['dateline']).'</td>
							<td>'.$this->iif($in['count'],$in['count'],0).'</td>
							<td>'.date('Y-m-d H:i:s',$inboundItem['created']).'</td>
							<td>范围：<em class="startno">'.$this->iif($startno['number'],str_pad($startno['number'],4,'0',STR_PAD_LEFT),'0000').'</em> — <em class="endno">'.$this->iif($endno['number'],str_pad($endno['number'],4,'0',STR_PAD_LEFT),'0000').'</em> <span class="info">'.$info.'</span>　<span class="status">'.$status.'</span></td>
							</tr>';
						$i++;
					}
				}
				$title = '';
				$options = '<a href="/s.php?module=psupplier&action=evaluatesupplier&inboundid='.$inbound['inboundid'].'&supplierid='.$inbound['supplierid'].'")">评价</a>　';
				if($inbound['inboundno']!='')$title.='　<span class="small">入库单号：</span><a href="/s.php?module=inbound&action=view&inboundid='.$inbound['inboundid'].'">'.$inbound['inboundno'].'</a>';
				if($inbound['porderid']!='')$title.='　<span class="small">订单号：</span><a href="/s.php?module=porder&action=view&porderid='.$inbound['porderid'].'">'.$inbound['porder'].'</a>';
				if($inbound['supplier']!='')$title.='　<span class="small">供应商：</span>'.$inbound['supplier'];
				$rtn['panel'].=<<<EOF
<table class="hundred">
<thead>
<tr><th colspan="10"><span class="right">{$options} <span class="plus" onclick="dc.showInquiry('inbound{$inbound['inboundid']}')"></span></span>	<span class="middle bold">{$inquiry['contact']}</span>{$title}</th></tr>
</thead>
<tbody id="inbound{$inbound['inboundid']}" style="display:none">
{$inboundItemtr}
</tbody>
{$item}
</table>
EOF;
				$i++;
			}
			$rtn['panel'].="</tbody>";
			$rtn['count']=' ('.$rtn['num'].')';
		}else{
			$rtn['off']='disabled ';
			
		}
		return $rtn;
	}
	// 供应商
	function relatedSupplier($r){
		$supplier = $this->DB->queryFirst("SELECT * FROM `supplier` WHERE supplierid='".$r['id']."'");
		if($supplier){
			$rtn['link'] = '<a href="/s.php?module=supplier&action=view&supplierid='.$r['id'].'" target="_blank">'.$supplier['title'].'</a>';
$rtn['panel'] = <<<EOF
<table class="hundred">
<tr class="odd">
	<td>中文名称：</td>
	<td class="middle">{$rtn['link']}</td>
	<td width="80">英文名称：</td>
	<td class="middle">{$supplier['entitle']}</td>
</tr>
<tr class="even">
	<td>简　　称：</td>
	<td class="middle">{$supplier['abbr']}</td>
	<td>商业类型：</td>
	<td class="middle">{$supplier['business']}</td>
</tr>
<tr class="odd">
	<td>中文地址：</td>
	<td class="middle">{$supplier['enaddress']} {$postalcode}</td>
	<td>国　　家：</td>
	<td class="middle">{$region}</td>
</tr>
<tr class="even">
	<td>英文地址：</td>
	<td class="middle">{$supplier['address']}</td>
	<td>网　　站：</td>
	<td class="middle">{$supplier['website']}</td>
</tr>
<tr class="odd">
	<td>主联系人：</td>
	<td class="middle">{$supplier['linkman']} {$position}</td>
	<td>次联系人：</td>
	<td class="middle">{$supplier['linkman2']} {$position2}</td>
</tr>
<tr class="even">
	<td>电　　话：</td>
	<td class="middle">{$supplier['telephone']}</td>
	<td>邮　　箱：</td>
	<td class="middle">{$supplier['email']}</td>
</tr>
<tr class="odd">
	<td>手　　机：</td>
	<td class="middle">{$supplier['mobile']}</td>
	<td>传　　真：</td>
	<td class="middle">{$supplier['fax']}</td>
</tr>
<tr class="even">
	<td valign="top">银行资料：</td>
	<td>{$supplier['bankdetail']}</td>
	<td valign="top">备　　注：</td>
	<td>{$supplier['remark']}</td>
</tr>
</table>
EOF;
		}else{
			$rtn['off']=' disabled';
		}
		return $rtn;
	}
	// 产品
	function relatedProduct($r){
		$condition = "`product`.killed=0";
		if($r['productid']>0){
			$condition.=" AND INSTR(LCASE(sample.productid),'".(strtolower($r['productid']))."')>0";
		}
		if($r['categoryid']>0){
			$condition.=" AND `product`.categoryid='".$r['categoryid']."'";
		}
		if($r['attrid']>0){
			$condition.=" AND `product`.attrid='".$r['attrid']."'";
		}
		$products = $this->DB->query("
			SELECT `product`.productid, `product`.title, `product`.entitle, `product`.productno, `product`.cover, `product`.attachs, `product`.images, `product`.modified, `product`.created, `product`.killed,
				category.title AS categoryTitle,
				attr.title AS attr,
				m.username AS mname, c.username AS cname
			FROM `product`
			LEFT JOIN `category` ON (category.categoryid=product.categoryid)
			LEFT JOIN `attr` ON (attr.attrid=product.attrid)
			LEFT JOIN `user` AS m ON (m.userid=`product`.modifier)
			LEFT JOIN `user` AS c ON (c.userid=`product`.creator)
			WHERE ".$condition."
			ORDER BY `product`.productid ASC, `product`.modified DESC, `product`.created DESC
		");
		if($this->DB->numRows()){
			$rtn['panel'] = '<ul id="mmlist" class="mmlist clear">';
			$i=1;
			while($product = $this->DB->fetchArray($products)){
				$created = date('y-m-d H:m', $product['created']);
				$modified = date('Y-m-d H:m', $product['modified']);
				$rtn['panel'] .= '<li title="由 '.$product['creator'].' 建于 '.$created.$this->iif($product['modifier']!='', '，'.$product['modifier'].' 改于 '.$modified, '').'"'.$this->iif($i%3==0, ' class="end"', '').'>
				<div class="mmlistt">
					<span class="right normal"></span>
					<a href="/s.php?module=product&action=view&productid='.$product['productid'].'" target="_blank">'.$product['productno'].' <span class="normal">'.$product['title'].'</span></a><br><span class="small">'.$product['attr'].'</span> '.$this->iif($product['attachs']>0, ' <span class="attachFile" title="有'.$product['attachs'].'个附件。"></span> ', '').$this->iif($product['images']>0, ' <span class="attachImage" title="有'.$product['images'].'个图片。"></span> ', '').'<span class="normal">'.$product['entitle'].'</span></div>
				<div class="mmlistb">
					<div title="'.$product['categoryTitle'].'"><span class="small gray">类别：</span>'.$product['categoryTitle'].'</div>
					'.$this->iif($product['cover']!='', '<div class="center"><img src="'.$product['cover'].'"></div>', '').'
					<div class="small clear"><span class="right" title="由 '.$product['creator'].' 建于 '.$created.'">'.$this->iif($product['modifier']!='', '由 '.$product['modifier'].' 改于 ', '建于 ').$modified.'</span></div>
				</div>
				</li>';
				$i++;
			}
			$rtn['panel'] .= '</ul>';
			$rtn['num'] = '<span class="small">('.$rtn['num'].')</span>';
		}else{
			$rtn['disabled'] = ' disabled';
		}
		return $rtn;
	}

	// 样品
	function relatedSample($r){
		if(!$r['panel'])$r['panel']=1;
		$condition = "`sample`.killed=0";
		if($r['productid']>0){
			$condition.=" AND INSTR(LCASE(sample.productid),'".(strtolower($r['productid']))."')>0";
		}
		if($r['customerid']>0){
			$condition.=" AND `sample`.customerid='".$r['customerid']."'";
		}
		if($r['supplierid']>0){
			$condition.=" AND `sample`.supplierid='".$r['supplierid']."'";
		}
		if($r['requirementid']>0){
			$items = $this->DB->query("
				SELECT DISTINCT ri.mid
				FROM `requirementitem` AS ri
				WHERE ri.killed=0 AND ri.module='sample' AND ri.requirementid='".$r['requirementid']."'");
			if($this->DB->numRows()){
				while($item = $this->DB->fetchArray($items)){
					$id[] = $item['mid'];
				}
				$condition.=" AND `sample`.sampleid IN (".implode(',', $id).")";
			}else{
				$condition.=" AND `sample`.sampleid=0";
			}
		}
		$samples = $this->DB->query("
			SELECT `sample`.sampleid, `sample`.title, `sample`.entitle, `sample`.typeid, `sample`.sampleno, `sample`.attachs, `sample`.cover, `sample`.images, `sample`.modified, `sample`.created, `sample`.killed,
				attr.title AS attr,
				m.username AS modifier, c.username AS creator
			FROM `sample`
			LEFT JOIN `attr` ON (attr.attrid=sample.attrid)
			LEFT JOIN `user` AS m ON (m.userid=`sample`.modifier)
			LEFT JOIN `user` AS c ON (c.userid=`sample`.creator)
			WHERE ".$condition."
			ORDER BY `sample`.sampleid ASC, `sample`.modified DESC, `sample`.created DESC
		");
		$rtn['num'] = $this->DB->numRows();
		if($rtn['num'] AND $r['panel']==1){
			$rtn['panel'] = '<ul id="mmlist" class="mmlist clear">';
			$i=1;
			while($sample = $this->DB->fetchArray($samples)){
				$sampleType='';
				if($sample['typeid']!=''){
					$stypes = $this->DB->query("SELECT typeid, title FROM `sampletype` WHERE typeid IN (".$sample['typeid'].")");
					if($this->DB->numRows()){
						while($stype = $this->DB->fetchArray($stypes)){
							$sampleType .= '<span title="('.$stype['entitle'].') '.$stype['remark'].'" style="padding-right:12px">'.$stype['title'].'</span>';
						}
					}
				}
				$created = date('y-m-d H:m', $sample['created']);
				$modified = date('Y-m-d H:m', $sample['modified']);
				$rtn['panel'] .= '<li title="由 '.$sample['creator'].' 建于 '.$created.$this->iif($sample['modifier']!='', '，'.$sample['modifier'].' 改于 '.$modified, '').'"'.$this->iif($i%3==0, ' class="end"', '').'>
				<div class="mmlistt">
					<span class="right normal">';
				if($sample['killed']>0){
					$rtn['panel'] .= '<a href="/s.php?module=sample&action=restore&sampleid='.$sample['sampleid'].'&rt=list">恢复</a>';
				}else{
					$rtn['panel'] .= '<a href="/s.php?module=sample&action=kill&sampleid='.$sample['sampleid'].'&rt=list" onclick="return confirm(\'你确定要删除这个样品 '.$sample['title'].' 吗？\');">删</a> <a href="/s.php?module=sample&action=update&sampleid='.$sample['sampleid'].'&rt=list">改</a>';
				}
				$rtn['panel'] .= '</span>
					<span class="small">'.$sample['attr'].'</span> <a href="/s.php?module=sample&action=view&sampleid='.$sample['sampleid'].'">'.$sample['title'].'</a><br>'.$this->iif($sample['attachs']>0, ' <span class="attachFile" title="有'.$sample['attachs'].'个附件。"></span> ', '').$this->iif($sample['images']>0, ' <span class="attachImage" title="有'.$sample['images'].'个图片。"></span> ', '').$this->iif($sample['entitle']!='', '<span class="small">'.$sample['entitle'].'</span>', '').'</div>
				<div class="mmlistb">
					<div><span class="small">类型：</span>'.$sampleType.'</div>'.$this->iif($sample['cover']!='', '<div class="center"><img src="'.$sample['cover'].'"></div>', '').'
					<div class="small clear"><span class="right" title="由 '.$sample['creator'].' 建于 '.date('y-m-d H:m', $sample['created']).'">'.$this->iif($sample['modifier']!='', '由 最后'.$sample['modifier'].' 改于 ', '建于 ').date('Y-m-d H:m', $sample['modified']).'</span></div>
				</div>
				</li>';
				$i++;
			}
			$rtn['panel'] .= '</ul>';
			$rtn['count'] = ' ('.$rtn['num'].')';
		}
		if(!$rtn['num']){
		$rtn['off']=' disabled';
		}
		return $rtn;
	}
	// 生产供应商样品明细
	function relatedPSampleItem($o){
		$items = $this->DB->query("
			SELECT si.materialno,si.material,si.standard,si.quantity,si.unitid,si.price,si.amount,si.remark,si.ifValidate,si.validater,si.validated,si.validateResult,
				unit.title AS unit
			FROM `psampleitem` AS si
			LEFT JOIN unit ON (unit.unitid=si.unitid)
			WHERE si.killed=0 AND si.sampleid='".$o['psampleid']."'
			ORDER BY si.ordering,si.modified ASC,si.itemid ASC
		");
		if($this->DB->numRows()){
			$k=1;
			$ifValidate=0;
			while($item = $this->DB->fetchArray($items)){
				if($item['validated']>0){
					$validated=date('Y-m-d H:i:s',$item['validated']);
					$ifValidate=1;$validate='—';
					if($item['ifValidate']==1){
						$validate='合格';
					}elseif($item['ifValidate']==-1){
						$validate='不合格';
					}
				}else{
					$ifValidate=0;
					$validated='等待验证';
				}

				$itemtr .= '<tr class="'.$this->iif($this->rotate(), 'odd', 'even').'">
					<td>'.$k.'</td>
					<td>'.$item['materialno'].'</td>
					<td>'.$item['material'].'</td>
					<td>'.$item['standard'].'</td>
					<td align="right">'.number_format($item['quantity']).' '.$item['unit'].'</td>
					<td align="right">'.number_format($item['price'], 2).'</td>
					<td align="right">'.number_format($item['amount'], 2).'</td>
					<td>'.$item['remark'].'</td>
					<td>'.$validate.'</td>
					<td>'.$item['validateResult'].'</td>		
					<td>'.$item['validater'].'</td>
					<td>'.$validated.'</td>
				</tr>';
				$k++;
				$quantity+=$item['quantity'];
				$total+=$item['amount'];
			}
			$rtn['total'] = $total;
			$total=number_format($total, 2);

$rtn['tr']= <<<EOF
<tr><td width="15">ID</td><td width="40">编号</td><td>名称</td><td>规格</td><td width="100">数量</td><td width="100">单价</td><td width="100">金额</td><td width="200">备注</td><td>是否合格</td><td>验证结果</td><td width="60">验证人</td><td width="145">验证时间</td></tr>	
{$itemtr}
<tr><td colspan="4" align="right">合计：</td><td align="right">{$quantity}</td><td align="right">总价</td><td align="right" class="middle bold">{$total}</td><td></td><td></td><td></td><td></td><td></td></tr>
EOF;
		}
		return $rtn;
	}
	// 样品明细
	function relatedSampleItem($o){
		$items = $this->DB->query("
			SELECT item.*,
				product.title AS product,
				brand.title AS brand,
				packing.title AS packing,
				unit.title AS unit
			FROM `item`
			LEFT JOIN `product` ON (product.productid=item.productid)
			LEFT JOIN packing ON (packing.packingid=item.packingid)
			LEFT JOIN brand ON (brand.brandid=item.brandid)
			LEFT JOIN unit ON (unit.unitid=item.unitid)
			WHERE `item`.killed=0 AND module='sample' AND mid='".$o['sampleid']."'
			AND `item`.productitemid='0' ORDER BY `item`.itemid ASC
		");
		if($this->DB->numRows()){
			$k=1;
			$itemtr = '<tr><td>名称</td><td>型号</td><td>说明</td><td>供应商编号</td><td>客户编号</td><td align="right">折扣%</td><td align="right">数量</td><td align="right">单价</td><td align="right">小计</td></tr>';
			while($item = $this->DB->fetchArray($items)){
				$itemtr .= '<tr class="'.$this->iif($this->rotate(), 'odd', 'even').'"><td class="small">'.$item['title'].'</td><td>'.$item['model'].'</td>
				<td>'.$item['caption'].'</td>
				<td>'.$item['supplierNo'].'</td>
				<td>'.$item['customerNo'].'</td>
				<td align="right">'.$this->iif($item['discount']>0, $item['discount'].'%', '').'</td>
				<td align="right">'.number_format($item['quantity']).' '.$item['unit'].'</td>
				<td align="right">'.number_format($item['price'], 2).'</td>
				<td align="right">'.number_format($item['amount'], 2).'</td>
				</tr>';
				$k++;
				$quantity+=$item['quantity'];
				$total+=$item['amount'];
			}
			$rtn['total'] = $total;
			$total=number_format($total, 2);
$rtn['tr']= <<<EOF
		{$itemtr}
		<tr><td colspan="6" align="right">合计：</td><td align="right">{$quantity}</td><td></td><td align="right" class="middle bold">{$total}</td></tr>
EOF;
		}
		return $rtn;
	}

	// 样品明细
	function relatedPrintSampleItem($o){
		$items = $this->DB->query("
			SELECT item.*,
				product.entitle AS product,
				brand.title AS brand,
				packing.entitle AS packing,
				unit.entitle AS unit
			FROM `item`
			LEFT JOIN `product` ON (product.productid=item.productid)
			LEFT JOIN packing ON (packing.packingid=item.packingid)
			LEFT JOIN brand ON (brand.brandid=item.brandid)
			LEFT JOIN unit ON (unit.unitid=item.unitid)
			WHERE `item`.killed=0 AND module='sample' AND mid='".$o['sampleid']."'
			AND `item`.productid='0' ORDER BY `item`.ordering, `item`.modified ASC, `item`.itemid ASC
		");
		if($this->DB->numRows()){
			$k=1;
			$itemtr ='<table class="hundred"><tbody><tr><td colspan="9" class="middle bold">样品信息</td></tr><tr><td width="100">Samples</td><td width="220">Model</td><td width="126">Special Note</td><td>Supplier Code</td><td>Customer Code</td><td align="right" width="70">Discount</td><td align="right" width="70">Quantity</td><td align="right" width="70">Price</td><td align="right" width="70">Amount</td></tr>';
			while($item = $this->DB->fetchArray($items)){
				$itemtr .= '<tr class="odd"><td class="small">'.$item['title'].'</td><td>'.$item['model'].'</td>
				<td>'.$item['caption'].'</td>
				<td>'.$item['supplierNo'].'</td>
				<td>'.$item['customerNo'].'</td>
				<td align="right">'.$this->iif($item['discount']>0, $item['discount'].'%', '').'</td>
				<td align="right">'.number_format($item['quantity']).' '.$item['unit'].'</td>
				<td align="right">'.number_format($item['price'], 2).'</td>
				<td align="right">'.number_format($item['amount'], 2).'</td>
				</tr>';
				$k++;
				$quantity+=$item['quantity'];
				$total+=$item['amount'];
			}
			$rtn['total'] = $total;
			$total=number_format($total, 2);
			$rtn['tr']= <<<EOF
		{$itemtr}
		<tr><td colspan="6" align="right">Subtotal：</td><td align="right" class="bold">{$quantity}</td><td></td><td align="right" class="middle bold">{$total}</td></tr></tbody></table>
EOF;
		}
		return $rtn;
	}

	// 样品明细
	function relatedPrintEnSampleItem($o){
		$items = $this->DB->query("
			SELECT item.*,
				product.entitle AS product,
				brand.title AS brand,
				packing.entitle AS packing,
				unit.entitle AS unit
			FROM `item`
			LEFT JOIN `product` ON (product.productid=item.productid)
			LEFT JOIN packing ON (packing.packingid=item.packingid)
			LEFT JOIN brand ON (brand.brandid=item.brandid)
			LEFT JOIN unit ON (unit.unitid=item.unitid)
			WHERE `item`.killed=0 AND module='sample' AND mid='".$o['sampleid']."'
			AND `item`.productid='0' ORDER BY `item`.ordering, `item`.modified ASC, `item`.itemid ASC
		");
		if($this->DB->numRows()){
			$k=1;
			$itemtr ='<table class="hundred"><tbody><tr><td colspan="9" class="middle bold">Sample Information</td></tr><tr><td width="100">Samples</td><td width="220">Model</td><td width="126">Special Note</td><td>Supplier Code</td><td>Customer Code</td><td align="right" width="70">Discount</td><td align="right" width="70">Quantity</td><td align="right" width="70">Price</td><td align="right" width="70">Amount</td></tr>';
			while($item = $this->DB->fetchArray($items)){
				$itemtr .= '<tr class="odd"><td class="small">'.$item['title'].'</td><td>'.$item['model'].'</td>
				<td>'.$item['caption'].'</td>
				<td>'.$item['supplierNo'].'</td>
				<td>'.$item['customerNo'].'</td>
				<td align="right">'.$this->iif($item['discount']>0, $item['discount'].'%', '').'</td>
				<td align="right">'.number_format($item['quantity']).' '.$item['unit'].'</td>
				<td align="right">'.number_format($item['price'], 2).'</td>
				<td align="right">'.number_format($item['amount'], 2).'</td>
				</tr>';
				$k++;
				$quantity+=$item['quantity'];
				$total+=$item['amount'];
			}
			$rtn['total'] = $total;
			$total=number_format($total, 2);
			$rtn['tr']= <<<EOF
		{$itemtr}
		<tr><td colspan="6" align="right">Subtotal：</td><td align="right" class="bold">{$quantity}</td><td></td><td align="right" class="middle bold">{$total}</td></tr></tbody></table>
EOF;
		}
		return $rtn;
	}

	// 样品里产品明细
	function relatedSampleProductItem($o){
		$items = $this->DB->query("
			SELECT item.*,
				p.title,
				m.materialid,m.standard,
				brand.title AS brand,
				packing.title AS packing,
				unit.title AS unit
			FROM `item`
			LEFT JOIN `product` AS p ON (p.productid=`item`.productid)
			LEFT JOIN `material` AS m ON (m.materialid=`item`.materialid)
			LEFT JOIN packing ON (packing.packingid=item.packingid)
			LEFT JOIN brand ON (brand.brandid=item.brandid)
			LEFT JOIN unit ON (unit.unitid=item.unitid)
			WHERE `item`.killed=0 AND `item`.module='sample' AND `item`.mid='".$o['sampleid']."'
			ORDER BY `item`.itemid ASC
		");
		if($this->DB->numRows()){
			$k=1;
			$itemtr = '<tr><td width="100">样品名称</td><td>样品特有属性</td><td  width="50">说明</td><td  width="100">品牌</td><td  width="50">包装</td><td  width="45">折扣%</td><td width="30">数量</td><td  align="right" width="50">单价</td><td width="50">小计</td></tr>';
		while($item = $this->DB->fetchArray($items)){
				$selectStr = '';
				if($item['productitemid']>0){
					$valueid=explode(',',$item['productAttributes']);
					foreach($valueid as $key => $val){
						$attribute=$this->DB->queryFirst("
							SELECT av.valueid,av.title AS value,
								an.nameid,an.title AS name
							FROM attributevalue AS av
							LEFT JOIN attributename AS an ON (an.nameid=av.nameid)
							WHERE an.killed=0 AND av.killed=0 AND av.valueid='".$val."'
							ORDER BY an.nameid ASC
						");
						if($attribute['name'] AND $attribute['value']){
							$selectStr .= '　'.$attribute['name'].'：'.'<span class="darkred">'.$attribute['value'].'</span>';
						}
					}
				}else{
					$selectStr = $item['standard'];
				}
				$itemtr .= '<tr class="odd">
				<td><a href="/s.php?module=product&action=view&productid='.$item['productid'].'" target="_blank">'.$item['title'].'</a></td>
				<td>'.$selectStr.'</td>
				<td>'.$item['caption'].'</td>
				<td>'.$item['brand'].'</td>
				<td>'.$item['packing'].'</td>
				<td align="right">'.$this->iif($item['discount']<0,'',$item['discount'].'%').'</td>
				<td align="right">'.number_format($item['quantity']).' '.$item['unit'].'</td>
				<td align="right">'.number_format($item['price'], 2).'</td>
				<td align="right">'.number_format($item['amount'], 2).'</td></tr>';
				$k++;
				$quantity+=$item['quantity'];
				$total+=$item['amount'];
			}
			$rtn['total'] = $total;
			$total=number_format($total, 2);
			$rtn['tr']= <<<EOF
		{$itemtr}
		<tr><td colspan="6" align="right">合计：</td><td align="right">{$quantity}</td><td></td><td align="right" class="middle bold">{$total}</td></tr>
EOF;
		}
		return $rtn;
	}

	// 样品里产品明细
	function relatedPrintEnSampleProductItem($o){
		$items = $this->DB->query("
				SELECT item.*,
					product.entitle AS product,
					brand.title AS brand,
					packing.entitle AS packing,
					unit.entitle AS unit
				FROM `item`
				LEFT JOIN `product` ON (product.productid=item.productid)
				LEFT JOIN packing ON (packing.packingid=item.packingid)
				LEFT JOIN brand ON (brand.brandid=item.brandid)
				LEFT JOIN unit ON (unit.unitid=item.unitid)
				WHERE `item`.killed=0 AND module='sample' AND mid='".$o['sampleid']."'
				AND `item`.productid>0 ORDER BY `item`.ordering, `item`.modified ASC, `item`.itemid ASC
		");
		if($this->DB->numRows()){
			$k=1;
			$itemtr='<table class="hundred"><tbody><tr><td colspan="9" class="middle bold">Sample Information</td></tr><tr><td width="100">Samples</td><td width="220">Description of Samples</td><td  width="126">Special Note</td><td>Brand</td><td>Packaging</td><td align="right" width="70">Discount</td><td align="right" width="70">Quantity</td><td  align="right" width="70">Price</td><td align="right" width="70">Amount</td></tr>';
			while($item = $this->DB->fetchArray($items)){

				$selectStr = '';
				if(!empty($item['productAttributes'])){
					$nameStr = explode("#",$item['productAttributes']);
					$valueStr = explode(",",$nameStr[1]);
					array_pop($valueStr);
					$attributes=$this->DB->query("
						SELECT an.nameid,an.entitle AS name,
							av.valueid,av.entitle AS value
						FROM  attributename AS an
						LEFT JOIN attributevalue AS av ON (av.nameid=an.nameid)
						WHERE an.killed=0 AND av.killed=0 AND an.module='product' AND an.mid='".$nameStr[0]."'
						ORDER BY an.ordering ASC
					");
					$nameId=$title=$value=$attributeCount=array();
					if($this->DB->numRows()){
						while($attribute=$this->DB->fetchArray($attributes)){
							if(in_array($attribute['nameid'],$nameId)){
								$title[$attribute['nameid']]=$attribute['name'];
								$value[$attribute['nameid']][]=$attribute['value'];
								$valueId[$attribute['nameid']][]=$attribute['valueid'];
								$attributeCount[$attribute['nameid']]++;
							}else{
								$nameId[]=$attribute['nameid'];
								$title[$attribute['nameid']]=$attribute['name'];
								$value[$attribute['nameid']][]=$attribute['value'];
								$valueId[$attribute['nameid']][]=$attribute['valueid'];
								$attributeCount[$attribute['nameid']]=1;
							}
						}
					}
					foreach ($nameId as $key=>$val){
						$selectStr .= ''.$title[$val].'：';
						for($n=0;$n<count($value[$val]);$n++){
							if($valueId[$val][$n]==$valueStr[$key]){
								$selectStr .= '<i>'.$value[$val][$n].'</i><br/>';
							}
						}
						$selectStr .= '';
					}
				}else{
					$selectStr .= '';
				}
				$itemtr .= '<tr class="odd">
				<td>'.$item['product'].'</td>
				<td>'.$selectStr.'</td>
				<td>'.$item['caption'].'</td>
				<td>'.$item['brand'].'</td>
				<td>'.$item['packing'].'</td>
				<td align="right">'.$this->iif($item['discount']>0, $item['discount'].'%', '').'</td>
				<td align="right">'.number_format($item['quantity']).' '.$item['unit'].'</td>
				<td align="right">'.number_format($item['price'], 2).'</td>
				<td align="right">'.number_format($item['amount'], 2).'</td></tr>';
				$k++;
				$quantity+=$item['quantity'];
				$total+=$item['amount'];
			}
			$rtn['total'] = $total;
			$total=number_format($total, 2);
			$rtn['tr']= <<<EOF
		{$itemtr}
		<tr><td colspan="6" align="right">Subtotal：</td><td align="right" class="bold">{$quantity}</td><td></td><td align="right" class="middle bold">{$total}</td></tr></tbody></table>
EOF;
		}
		return $rtn;
	}


	function relatedPrintSampleProductItem($o){
		$items = $this->DB->query("
			SELECT item.*,
				productitem.attributevalue,
				productitem.mid AS productId,
				brand.title AS brand,
				packing.title AS packing,
				unit.entitle AS unit
			FROM `item`
			LEFT JOIN `productitem` ON (productitem.productitemid=item.productitemid)
			LEFT JOIN packing ON (packing.packingid=item.packingid)
			LEFT JOIN brand ON (brand.brandid=item.brandid)
			LEFT JOIN unit ON (unit.unitid=item.unitid)
			WHERE `item`.killed=0 AND `item`.module='sample' AND `item`.mid='".$o['sampleid']."'
			ORDER BY itemid ASC, modified ASC
		");
		if($this->DB->numRows()){
			$k=1;
			while($item = $this->DB->fetchArray($items)){
				$selectStr = '';
				if(!empty($item['attributevalue'])){
					$valueStr = explode(",",$item['attributevalue']);

					$product=$this->DB->queryFirst("
						SELECT `product`.title,`product`.entitle,`material`.`materialno`
						FROM  `product`
						LEFT JOIN `material` ON (`material`.`materialid` = `product`.materialid)
						WHERE productid='".$item['productId']."'
					");

					$attributes=$this->DB->query("
						SELECT an.nameid,an.title AS name,
							av.valueid,av.title AS value
						FROM  attributename AS an
						LEFT JOIN attributevalue AS av ON (av.nameid=an.nameid)
						WHERE an.killed=0 AND av.killed=0 AND an.module='product' AND an.mid='".$item['productId']."'
						ORDER BY an.nameid ASC
					");

					$nameId=$title=$value=$attributeCount=array();
					if($this->DB->numRows()){
						while($attribute=$this->DB->fetchArray($attributes)){
							if(in_array($attribute['nameid'],$nameId)){
								$title[$attribute['nameid']]=$attribute['name'];
								$value[$attribute['nameid']][]=$attribute['value'];
								$valueId[$attribute['nameid']][]=$attribute['valueid'];
								$attributeCount[$attribute['nameid']]++;
							}else{
								$nameId[]=$attribute['nameid'];
								$title[$attribute['nameid']]=$attribute['name'];
								$value[$attribute['nameid']][]=$attribute['value'];
								$valueId[$attribute['nameid']][]=$attribute['valueid'];
								$attributeCount[$attribute['nameid']]=1;
							}
						}
					}
					foreach ($nameId as $key=>$val){
						$selectStr .= ''.$title[$val].'：';
						for($n=0;$n<count($value[$val]);$n++){
							if($valueId[$val][$n]==$valueStr[$key]){
								$selectStr .= '<i class="small">'.$value[$val][$n].'</i>';
							}
						}
						$selectStr .= '　';
					}
				}else{
					$product=$this->DB->queryFirst("
						SELECT `product`.title,`product`.entitle,`product`.materialid
						FROM  `product`
						WHERE productid='".$item['productid']."'
					");
					$item['productId'] = $item['productid'];
					$material = $this->DB->queryFirst("
						SELECT `material`.materialid,`material`.materialno,`material`.standard
						FROM  `material`
						WHERE materialid='".$product['materialid']."'
					");
					$product['materialno']=$material['materialno'];
					$selectStr = $material['standard'];
				}

				$itemtr .= '<tr class="odd">
				<td align="center">'.$k.'</td>
				<td align="center">'.$product['materialno'].'</td>
				<td align="center">'.$product['title'].'</td>
				<td>'.$selectStr.'</td>
				<td align="center">'.number_format($item['quantity']).'</td>
				<td align="center">'.$item['unit'].'</td>
				<td></td>
				<td>'.$item['caption'].'</td>
				</tr>';
				$k++;
				$quantity+=$item['quantity'];
				$total+=$item['amount'];
			}
			$total=number_format($total, 2);
			return <<<EOF
		<tr class="bold"><td width="35">序号</td><td width="85" align="center">产品编号</td><td align="center" width="130">产品名称</td><td>规格(电缆线/插头)</td><td align="center" width="65">数量</td><td align="center" width="65">单位</td><td align="center" width="65">方式</td><td align="center" width="220">备注</td></tr>
		{$itemtr}

EOF;
		}
	}


	//
	function relatedPurchase($r){
		if($r['materialid']>0){
			$items=$this->DB->query("SELECT DISTINCT `purchaseid` FROM `purchaseitem` WHERE killed=0 AND materialid='".$r['materialid']."'");
			if($this->DB->numRows()){
				while($item = $this->DB->fetchArray($items)){
					$id[] = $item['purchaseid'];
				}
				$condition.=" AND `purchase`.purchaseid IN (".implode(',', $id).")";
			}else{
				$condition.=" AND `purchase`.purchaseid=0";
			}
		}
		if($r['porderid']>0){
			$items=$this->DB->query("SELECT DISTINCT `purchaseid` FROM `item` WHERE killed=0 AND module='porder' AND mid='".$r['porderid']."'");
			if($this->DB->numRows()){
				while($item = $this->DB->fetchArray($items)){
					$id[] = $item['purchaseid'];
				}
				$condition.=" AND `purchase`.purchaseid IN (".implode(',', $id).")";
			}else{
				$condition.=" AND `purchase`.v=0";
			}
		}
		if($r['orderby'] == ''){
			$r['orderby'] = '`purchase`.modified';
		}
		if($r['direction'] == ''){
			$r['direction'] = 'DESC';
		}
		if($r['limit'] == ''){
			$r['limit'] = '0, 100';
		}
		$purchases=$this->DB->query("
			SELECT `purchase`.purchaseid,`purchase`.purchaseno,`purchase`.applicant,
					`purchase`.amount,`purchase`.ifVerify,`purchase`.modified,`purchase`.created,`purchase`.killed,
				c.title AS currency,
				u.username AS creator,
				us.username AS modifier
			FROM `purchase`
			LEFT JOIN currency AS c ON (c.currencyid=`purchase`.currencyid)
			LEFT JOIN user AS u ON (u.userid=`purchase`.creator)
			LEFT JOIN user AS us ON (us.userid=`purchase`.modifier)
			WHERE `purchase`.killed=0 ".$condition."
			ORDER BY ".$r['orderby'].' '.$r['direction']."
			LIMIT ".$r['limit']."
		");
		$rtn['num']=$this->DB->numRows();
		if($rtn['num']){
			$rtn['panel']='<ul class="mmlist clear" id="mmlist">';
			$i=1;
			while($purchase=$this->DB->fetchArray($purchases)){
				$purchase['created']=date('Y-m-d',$purchase['created']);
					$verify=$this->verify($purchase['ifVerify']);
					$rtn['panel'].='<li title="由 '.$purchase['creator'].' 建于 '.$created.$this->iif($purchase['modifier']!='', '，'.$purchase['modifier'].' 改于 '.date('Y-m-d H:i',$purchase['modified']), '').'"'.$this->iif($i%4==0, ' class="end"', '').'>
					<div class="mmlistt">
						<span class="right normal">';
					if($purchase['killed']>0){
						$rtn['panel'].='<a href="/s.php?module=purchase&action=revival&purchaseid='.$purchase['purchaseid'].'&rt=list" onclick="return confirm(\'你确定要恢复这个采购计划单 '.$purchase['purchaseno'].' 吗？\');">恢复</a>';
					}else{
						$rtn['panel'].='<a href="/s.php?module=purchase&action=kill&purchaseid='.$purchase['purchaseid'].'&rt=list" onclick="return confirm(\'你确定要删除这个采购计划单 '.$purchase['purchaseno'].' 吗？\');">删</a> '.$this->iif($purchase['ifVerify']==0,'<a href="/s.php?module=purchase&action=update&purchaseid='.$purchase['purchaseid'].'&rt=list">改</a>','');
					}
					$rtn['panel'].='</span>
					<span class="small">'.$purchase['attr'].'</span> <a href="/s.php?module=purchase&action=view&purchaseid='.$purchase['purchaseid'].'">'.$purchase['purchaseno'].'</a><br><span class="small gray right">'.$purchase['created'].'</span>'.$this->iif($purchase['attachs']>0, ' <span class="attachFile" title="有'.$purchase['attachs'].'个附件。"></span> ', '').$this->iif($purchase['images']>0, ' <span class="attachImage" title="有'.$purchase['images'].'个图片。"></span> ', '').''.$verify.'</div>
				<div class="mmlistb">
					<div><span class="small gray">采购员：'.$purchase['applicant'].'</span></div>
					<div class="small clear"><span class=right title="由 '.$prchase['creator'].' 建于 '.$purchase['created'].'">建于 '.$purchase['created'].'</span></div></div></li>';
					$i++;
			}
			$rtn['panel'].='</ul>';
			$rtn['count'] = ' ('.$rtn['num'].')';
		}else{
			$rtn['off']=' disabled';
		}

		return $rtn;
	}
	//
	function relatedPurchaseApply($r){
		if($r['materialid']>0){
			$items = $this->DB->query("SELECT DISTINCT `mid` FROM `preinbounditem` WHERE killed=0 AND module='purchaseApply' AND materialid='".$r['materialid']."'");
			if($this->DB->numRows()){
				while($item = $this->DB->fetchArray($items)){
					$id[] = $item['mid'];
				}
				$condition.=" AND `purchaseapply`.purchaseApplyid IN (".implode(',', $id).")";
			}else{
				$condition.=" AND `purchaseapply`.purchaseApplyid=0";
			}
		}
		if($r['purchaseid']>0){
			$items = $this->DB->query("SELECT DISTINCT `mid` FROM `purchaseitem` WHERE killed=0 AND module='purchaseApply' AND purchaseid='".$r['purchaseid']."'");
			if($this->DB->numRows()){
				while($item = $this->DB->fetchArray($items)){
					$id[] = $item['mid'];
				}
				$condition.=" AND `purchaseapply`.purchaseApplyid IN (".implode(',', $id).")";
			}else{
				$condition.=" AND `purchaseapply`.purchaseApplyid=0";
			}
		}
		if($r['requirementid']>0){
			$condition.=" AND `purchaseapply`.requirementid='".$r['requirementid']."'";
		}
		if($r['orderby'] == ''){
			$r['orderby'] = '`purchaseapply`.modified';
		}
		if($r['direction'] == ''){
			$r['direction'] = 'DESC';
		}
		if($r['limit'] == ''){
			$r['limit'] = '0, 100';
		}
		$applys=$this->DB->query("
			SELECT `purchaseapply`.purchaseApplyid,`purchaseapply`.typeid,`purchaseapply`.applyno,`purchaseapply`.applicant,
					`purchaseapply`.ifVerify,`purchaseapply`.modified,`purchaseapply`.created,`purchaseapply`.killed,
				d.title AS department,
				u.username AS creator,
				us.username AS modifier
			FROM `purchaseapply`
			LEFT JOIN department AS d ON (d.departmentid=`purchaseapply`.departmentid)
			LEFT JOIN user AS u ON (u.userid=`purchaseapply`.creator)
			LEFT JOIN user AS us ON (us.userid=`purchaseapply`.modifier)
			WHERE `purchaseapply`.killed=0".$condition."
			ORDER BY ".$r['orderby'].' '.$r['direction']."
			LIMIT ".$r['limit']."
		");
		$rtn['num']=$this->DB->numRows();
		if($rtn['num']){
			$rtn['panel']='<ul class="mmlist clear" id="mmlist">';
			$i=1;
			while($apply=$this->DB->fetchArray($applys)){
				$typeValue='';
				$types=$this->DB->query("SELECT title FROM purchasetype WHERE killed=0 AND typeid IN (".$apply['typeid'].")");
				if($counter=$this->DB->numRows()){
					$i=1;
					while($type=$this->DB->fetchArray($types)){
						if($i<$counter){
							$typeValue.=$type['title'].'、';
						}else{
							$typeValue.=$type['title'];
						}
						$i++;
					}
				}
				$apply['created']=date('Y-m-d',$apply['created']);
				$verify=$this->verify($apply['ifVerify']);
				$rtn['panel'].='<li title="由 '.$apply['creator'].' 建于 '.$apply['created'].$this->iif($apply['modifier']!='','，'.$apply['modifier'].' 改于 '.date('Y-m-d H:i',$apply['modified']),'').'"'.$this->iif($i%4==0, ' class="end"', '').'>
				<div class="mmlistt">
				<a href="/s.php?module=purchaseApply&action=view&purchaseApplyid='.$apply['purchaseApplyid'].'">'.$apply['applyno'].'</a><br><span class="small gray right">'.$apply['created'].'</span>'.$this->iif($apply['attachs']>0, ' <span class="attachFile" title="有'.$apply['attachs'].'个附件。"></span> ', '').$this->iif($apply['images']>0, ' <span class="attachImage" title="有'.$apply['images'].'个图片。"></span> ', '').$verify.'</div>
				<div class="mmlistb"><div><span class="small gray">申请人：</span>'.$apply['applicant'].'</div>
				<div><span class="small gray">申请部门：</span>'.$apply['department'].'</div>
				<div><span class="small gray">采购类型：</span>'.$typeValue.'</div>
				<div class="small clear"><span class=right title="由 '.$apply['creator'].' 建于 '.$apply['created'].'">由 '.$apply['creator'].' 建于 '.$apply['created'].'</span></div></div></li>';
				$i++;
			}
			$rtn['panel'].='</ul>';
			$rtn['count'] = ' ('.$rtn['num'].')';
		}else{
			$rtn['off']=' disabled';
		}

		return $rtn;
	}
	//
	function relatedEntrust($r){
		if($r['materialid']>0){
			$items = $this->DB->query("SELECT DISTINCT `mid` FROM `entrustitem` WHERE killed=0 AND materialid='".$r['materialid']."'");
			if($this->DB->numRows()){
				while($item = $this->DB->fetchArray($items)){
					$id[] = $item['mid'];
				}
				$condition.=" AND `entrust`.entrustid IN (".implode(',', $id).")";
			}else{
				$condition.=" AND `entrust`.entrustid=0";
			}
		}
		if($r['purchaseid']>0){
			$items = $this->DB->query("SELECT DISTINCT `mid` FROM `purchaseitem` WHERE killed=0 AND purchaseid='".$r['purchaseid']."'");
			if($this->DB->numRows()){
				while($item = $this->DB->fetchArray($items)){
					$id[] = $item['mid'];
				}
				$condition.=" AND `entrust`.entrustid IN (".implode(',', $id).")";
			}else{
				$condition.=" AND `entrust`.entrustid=0";
			}
		}
		if($r['requirementid']>0){
			$condition.=" AND `entrust`.requirementid='".$r['requirementid']."'";
		}
		if($r['orderby'] == ''){
			$r['orderby'] = '`entrust`.modified';
		}
		if($r['direction'] == ''){
			$r['direction'] = 'DESC';
		}
		if($r['limit'] == ''){
			$r['limit'] = '0, 100';
		}
		$entrusts=$this->DB->query("
			SELECT `entrust`.entrustid,`entrust`.entrustno,`entrust`.applicant,
					`entrust`.ifVerify,`entrust`.modified,`entrust`.created,`entrust`.killed,
				u.username AS creator,
				us.username AS modifier
			FROM entrust
			LEFT JOIN user AS u ON (u.userid=`entrust`.creator)
			LEFT JOIN user AS us ON (us.userid=`entrust`.modifier)
			WHERE `entrust`.killed=0".$condition."
			ORDER BY ".$r['orderby'].' '.$r['direction']."
			LIMIT ".$r['limit']."
		");
		$rtn['num']=$this->DB->numRows();
		if($rtn['num']){
			$rtn['panel']='<ul class="mmlist clear" id="mmlist">';
			$i=1;
			while($entrust=$this->DB->fetchArray($entrusts)){
				$entrust['created']=date('Y-m-d',$entrust['created']);
				$verify=$this->verify($entrust['ifVerify']);
				$rtn['panel'].='<li title="由 '.$entrust['creator'].' 建于 '.$entrust['created'].$this->iif($entrust['modifier']!='','，'.$entrust['modifier'].' 改于 '.date('Y-m-d H:i',$entrust['modified']),'').'"'.$this->iif($i%4==0, ' class="end"', '').'>
				<div class="mmlistt">
				<a href="/s.php?module=entrust&action=view&entrustid='.$entrust['entrustid'].'">'.$entrust['entrustno'].'</a><br><span class="small gray right">'.$entrust['created'].'</span>'.$this->iif($entrust['attachs']>0, ' <span class="attachFile" title="有'.$entrust['attachs'].'个附件。"></span> ', '').$this->iif($entrust['images']>0, ' <span class="attachImage" title="有'.$entrust['images'].'个图片。"></span> ', '').''.$verify.'</div>
			<div class="mmlistb">
				<div><span class="small gray">申请人：'.$entrust['applicant'].'</span></div>
				<div><span class="small gray">申请时间：'.$entrust['created'].'</span></div>
				<div class="small clear"><span class=right title="由 '.$entrust['creator'].' 建于 '.$entrust['created'].'">建于 '.$entrust['created'].'</span></div></div></li>';
				$i++;
			}
			$rtn['panel'].='</ul>';
			$rtn['count'] = ' ('.$rtn['num'].')';
		}else{
			$rtn['off']=' disabled';
		}

		return $rtn;
	}
	// 询价
	function relatedInquiry($r){
		if($r['productid']>0){
			$items = $this->DB->query("SELECT DISTINCT mid FROM `item` WHERE module='inquiry' AND productid='".$r['productid']."'");
			if($this->DB->numRows()){
				while($item = $this->DB->fetchArray($items)){
					$id[] = $item['mid'];
				}
				if(is_Array($id)){
					$r['condition'] .= " AND `inquiry`.inquiryid IN (".implode(',',$id).")";
				}
			}else{
				return array('num'=>0, 'panel'=>'');
			}
		}
		if($r['customerid']>0){
			$r['condition'] .= " AND `inquiry`.customerid='".$r['customerid']."'";
		}
		if($r['supplierid']>0){
			$r['condition'] .= " AND `inquiry`.supplierid='".$r['supplierid']."'";
		}
		if($r['orderby'] == ''){
			$r['orderby'] = '`inquiry`.modified';
		}
		if($r['direction'] == ''){
			$r['direction'] = 'DESC';
		}
		if($r['limit'] == ''){
			$r['limit'] = '0, 100';
		}
		$inquirys=$this->DB->query("
			SELECT inquiry.*,
				supplier.title AS supplier,
				customer.title AS customer,
				shipmethod.title AS shipmethod,
				port.title AS port,
				paymentterm.title AS paymentterm,
				deliveryterm.title AS deliveryterm,
				currency.title AS currency,
				m.realname AS creator,m.realname AS modifier
			FROM `inquiry`
			LEFT JOIN supplier ON (supplier.supplierid=`inquiry`.supplierid)
			LEFT JOIN customer ON (customer.customerid=`inquiry`.customerid)
			LEFT JOIN currency ON (currency.currencyid=`inquiry`.currencyid)
			LEFT JOIN shipmethod ON (shipmethod.shipmethodid=`inquiry`.shipmethodid)
			LEFT JOIN port ON (port.portid=`inquiry`.portid)
			LEFT JOIN paymentterm ON (paymentterm.paymenttermid=`inquiry`.paymenttermid)
			LEFT JOIN deliveryterm ON (deliveryterm.deliverytermid=`inquiry`.deliverytermid)
			LEFT JOIN member AS m ON (m.userid=`inquiry`.creator)
			LEFT JOIN member AS mem ON (mem.userid=`inquiry`.modifier)
			WHERE `inquiry`.killed=0".$r['condition']."
			ORDER BY ".$r['orderby']." ".$r['direction']."
			LIMIT ".$r['limit']."
		");
		$rtn['num'] = $this->DB->numRows();
		if($rtn['num']){
			while($inquiry = $this->DB->fetchArray($inquirys)){
				if($inquiry['quotingid']>0 AND $inquiry['replied']>0){
					$reply='最后回复时间：'.date('Y-m-d H:i:s', $inquiry['replied']);
					$replyLink='<a href="javascript:nextTabs();" title="查看已经回复的报价单"><span class="small">('.$inquiry['replyTimes'].')</span></a>';
				}else{
					$reply='';
					$replyLink='<span class="small">('.$inquiry['replyTimes'].')</span>';
				}
				if($inquiry['customerid']>0){
					$options = '<a href="/s.php?module=customer&action=removeInquiry&inquiryid='.$inquiry['inquiryid'].'" onclick="return confirm(\'你确定要删除这张询价单吗？(删除不可恢复！)\')">删除</a>　<a href="/s.php?module=customer&action=updateInquiry&inquiryid='.$inquiry['inquiryid'].'">修改</a>　<a href="/s.php?module=customer&action=chooseLanguage&actionid=iq&inquiryid='.$inquiry['inquiryid'].'" target="_blank">打印</a>　<a href="/s.php?module=customer&action=addQuoting&customerid='.$inquiry['customerid'].'&inquiryid='.$inquiry['inquiryid'].'" title="'.$reply.'">回复</a>'.$replyLink;
				}
				if($inquiry['supplierid']>0){
					$options = '<a href="/s.php?module=supplier&action=removeInquiry&inquiryid='.$inquiry['inquiryid'].'" onclick="return confirm(\'你确定要删除这张询价单吗？(删除不可恢复！)\')">删除</a>　<a href="/s.php?module=supplier&action=updateInquiry&inquiryid='.$inquiry['inquiryid'].'">修改</a>　<a href="/p.php?action=supplierInquiry&inquiryid='.$inquiry['inquiryid'].'" target="_blank">打印</a>　<a href="/s.php?module=supplier&action=addQuoting&supplierid='.$inquiry['supplierid'].'&inquiryid='.$inquiry['inquiryid'].'" title="'.$reply.'"><span class="small">供应商</span>回复</a>'.$replyLink;
				}
				$items = $this->DB->query("
					SELECT `item`.*,
						product.title AS product,
						material.standard,
						packing.title AS packing,
						unit.title AS unit
					FROM `item`
					LEFT JOIN product ON (product.productid=`item`.productid)
					LEFT JOIN material ON (material.materialid=`item`.materialid)
					LEFT JOIN packing ON (packing.packingid=`item`.packingid)
					LEFT JOIN unit ON (unit.unitid=`item`.unitid)
					WHERE `item`.killed=0 AND `item`.module='inquiry' AND mid='".$inquiry['inquiryid']."'
					ORDER BY `item`.itemid ASC
				");
				$itemtr='';
				if($itemNum=$this->DB->numRows()){
					$itemtr = '<tr><td colspan="10"><table class="hundred"><thead><tr><th>询价产品</th><th>产品特有属性</th><th>说明</th><th>包装</th><th>折扣</th><th>数量</th></tr></thead><tbody>';
					while($item = $this->DB->fetchArray($items)){
						$selectStr = '';
						$product=$this->getProduct(array('productid'=>$item['productid'],'itemid'=>$item['productitemid']));
						$itemtr .= '<tr class="'.$this->iif($this->rotate(), 'odd', 'even').'">
						<td><a href="/s.php?module=product&action=view&productid='.$item['productid'].'" target="_blank">'.$product['title'].'</a></td>
						<td>'.$this->iif($item['materialid']>0,$item['standard'],$product['standard']).'</td>
						<td>'.$item['caption'].'</td>
						<td>'.$item['packing'].'</td>
						<td>'.$item['discount'].'</td>
						<td>'.$item['quantity'].' '.$item['unit'].'</td>
						</tr>';
					}
					$itemtr .= '</tbody></table></td></tr>';
				}
				$title = '';
				switch($inquiry['inquirytype']){
					case 1:
						$inquirytype="无型号无数量";
						break;
					case 2:
						$inquirytype="无型号有数量";
						break;
					case 3:
						$inquirytype="有型号无数量";
						break;
					case 4:
						$inquirytype="有型号有数量";
						break;
				}
				$dateline=date('Y-m-d',$inquiry['dateline']);
				$created=date('Y-m-d',$inquiry['created']);
				$modified=date('Y-m-d',$inquiry['modified']);
				if($inquiry['currency']!='')$title .= '　<span class="small">币种：</span>'.$inquiry['currency'];
				if($inquiry['dateline']!='')$title .= '　<span class="small">交货日期：</span>'.$dateline;
				if($inquiry['paymentterm']!='')$title .= '　<span class="small">支付条款：</span>'.$inquiry['paymentterm'];
				if($inquiry['shipmethod']!='')$title .= '　<span class="small">运送方式：</span>'.$inquiry['shipmethod'];
				if($inquiry['port']!='')$title .= '　<span class="small">交货地点：</span>'.$inquiry['port'];
				if($inquiry['deliveryterm']!='')$title .= '　<span class="small">交货条款：</span>'.$inquiry['deliveryterm'];
				if($inquiry['inquirytype']!='')$title .= '　<span class="small red">询价单类型：</span>'.$inquirytype;
$rtn['panel'] .= <<<EOF
<table class="hundred">
<thead>
<tr><th colspan="10"><span class="right">{$options} <span class="plus" onclick="dc.showInquiry('inquiry{$inquiry['inquiryid']}')"></span></span>	<span class="normal">[<b>{$itemNum}</b>]</span> <span class="small">联系人：</span><span class="middle bold">{$inquiry['contact']}</span>{$title}</th></tr>
</thead>
<tbody id="inquiry{$inquiry['inquiryid']}" style="display:none">
<tr class="odd">
	<td width="80">运送方式：</td><td class="middle">{$inquiry['shipmethod']}</td>
	<td width="80">交货地点：</td><td class="middle">{$inquiry['port']}</td>
	<td width="80">支付条款：</td><td class="middle">{$inquiry['paymentterm']}</td>
	<td width="80">交货条款：</td><td class="middle">{$inquiry['deliveryterm']}</td>
	<td width="80">交货日期：</td><td>{$dateline}</td>
</tr>
<tr class="even">
	<td>询价内容：</td><td class="middle">{$inquiry['standard']}</td>
	<td>主要材料：</td><td class="middle">{$inquiry['material']}</td>
	<td>工艺说明：</td><td class="middle">{$inquiry['technics']}</td>
	<td>备　　注：</td><td class="middle">{$inquiry['remark']}</td>
	<td>其　　他：</td><td>由 {$inquiry['creator']} 于 {$created} 建立，由 {$inquiry['modifier']} 于 {$modified} 最后修改</td>
</tr>
{$itemtr}
</tbody>
</table>
EOF;
			}
			$rtn['panel'] .= '</tbody></table>';
			$rtn['count'] = ' ('.$rtn['num'].')';
		}else{
			$rtn['off']=' disabled';
		}
		return $rtn;
	}
	// 报价
	function relatedQuoting($r){
		if($r['productid']>0){
			$items = $this->DB->query("SELECT DISTINCT mid FROM `item` WHERE module='quoting' AND productid='".$r['productid']."'");
			if($this->DB->numRows()){
				while($item = $this->DB->fetchArray($items)){
					$id[] = $item['mid'];
				}
				if(is_Array($id)){
					$r['condition'] .= " AND `quoting`.quotingid IN (".implode(',',$id).")";
				}
			}else{
				return array('num'=>0, 'panel'=>'');
			}
		}
		if($r['customerid']>0){
			$r['condition'] .= " AND `quoting`.customerid='".$r['customerid']."'";
		}
		if($r['supplierid']>0){
			$r['condition'] .= " AND `quoting`.supplierid='".$r['supplierid']."'";
		}
		if($r['orderby'] == ''){
			$r['orderby'] = '`quoting`.modified';
		}
		if($r['direction'] == ''){
			$r['direction'] = 'DESC';
		}
		if($r['limit'] == ''){
			$r['limit'] = '0, 100';
		}
		$quotings=$this->DB->query("
			SELECT quoting.*,
				supplier.title AS supplier,
			customer.title AS customer,
				shipmethod.title AS shipmethod,
			port.title AS port,
			paymentterm.title AS paymentterm, paymentterm.entitle AS enpaymentterm,
			deliveryterm.title AS deliveryterm,
			currency.title AS currency,
				m.realname AS creator,m.realname AS modifier,b.realname AS bname
			FROM `quoting`
			LEFT JOIN supplier ON (supplier.supplierid=`quoting`.supplierid)
			LEFT JOIN customer ON (customer.customerid=`quoting`.customerid)
			LEFT JOIN currency ON (currency.currencyid=`quoting`.currencyid)
			LEFT JOIN shipmethod ON (shipmethod.shipmethodid=`quoting`.shipmethodid)
			LEFT JOIN port ON (port.portid=`quoting`.portid)
		LEFT JOIN paymentterm ON (paymentterm.paymenttermid=`quoting`.paymenttermid)
			LEFT JOIN deliveryterm ON (deliveryterm.deliverytermid=`quoting`.deliverytermid)
			LEFT JOIN member AS m ON (m.userid=`quoting`.creator)
		LEFT JOIN member AS mem ON (mem.userid=`quoting`.modifier)
			LEFT JOIN `member` AS b      ON(b.userid=`quoting`.verifier)
			WHERE `quoting`.killed=0".$r['condition']."
			ORDER BY ".$r['orderby']." ".$r['direction']."
			LIMIT ".$r['limit']."
		");
		$rtn['num'] = $this->DB->numRows();
		if($rtn['num']){
			while($quoting = $this->DB->fetchArray($quotings)){
				if($quoting['inquiryid']>0){
					$orgLink='<a href="javascript:previewTabs();" title="查看对应的询价单" class="small">对应询价单</a> ';
				}else{
					$orgLink='';
				}
				if($quoting['customerid']>0){	
				$options = $orgLink.'
				<!--xue 10/28-->
				<a href="./s.php?module=customer&action=chooseLanguage&m=2&quotingid='.$quoting['quotingid'].'&customerid='.$r['customerid'].'&verify='.base64_encode($quoting["ifverify"]).'">发送邮件</a>
				<!--xue 10/28-->			
				<a href="./s.php?module=customer&action=chooseLanguage&quotingid='.$quoting['quotingid'].'" >打印</a>
				<a href="s.php?module=order&action=add&quotingid='.$quoting['quotingid'].'&customerid='.$r['customerid'].'">生成订单</a>';

					$options.= '<a href="/s.php?module=customer&action=removeQuoting&quotingid='.$quoting['quotingid'].'" onclick="return confirm(\'你确定要删除这张报价单吗？(删除不可恢复！)\')">删除</a>　<a href="/s.php?module=customer&action=updateQuoting&quotingid='.$quoting['quotingid'].'">修改</a>　<a href="/s.php?module=customer&action=chooseLanguage&actionid=qt&quotingid='.$quoting['quotingid'].'" >打印</a>    <a href="s.php?module=order&action=add&quotingid='.$quoting['quotingid'].'&customerid='.$r['customerid'].'">生成订单</a>';
				}
				if($quoting['supplierid']>0){
					$options = $orgLink.'<a href="/s.php?module=supplier&action=removeQuoting&quotingid='.$quoting['quotingid'].'" onclick="return confirm(\'你确定要删除这张报价单吗？(删除不可恢复！)\')">删除</a>　<a href="/s.php?module=supplier&action=updateQuoting&quotingid='.$quoting['quotingid'].'">修改</a>　<a href="/p.php?action=quotingprint&actionid=qt&quotingid='.$quoting['quotingid'].'" target="_blank">打印</a>';
				}
				if($quoting['modifier']>0){
					$modified = '，'.$quoting['mname'].'于'.date('y-m-d H:i:s', $quoting['modified']).'最后修改';
				}
                 $doverify=$this->doverify(array('module'=>'customer','action'=>'verify'));
	     if($doverify){
            $action='(<a href="/s.php?module=customer&action=verify&quotingid='.$quoting['quotingid'].'">审核</a>)';
	       }
				if($quoting['ifverify']==0){
					$status='<span class="red">未审核</span>'.$action;
					$statusid=2;
				}elseif($quoting['ifverify']==1){
					$status='<span class="green">审核通过</span>';
					$verifier='（由'.$quoting['bname'].'于'.date('Y-m-d',$quoting['verified']).'审核）';
					$statusid=3;
				}elseif($quoting['ifverify']==-1){
					$status='<span class="red">审核未通过</span>';
					$verifier='由'.$quoting['bname'].'于'.date('Y-m-d',$quoting['verified']).'审核';
					$statusid=3;
				}
				$items = $this->DB->query("
					SELECT `item`.*,
						product.title AS product,
						material.standard,
						packing.title AS packing,
						unit.title AS unit
					FROM `item`
					LEFT JOIN product ON (product.productid=`item`.productid)
					LEFT JOIN material ON (material.materialid=`item`.materialid)
					LEFT JOIN packing ON (packing.packingid=`item`.packingid)
					LEFT JOIN unit ON (unit.unitid=`item`.unitid)
					WHERE `item`.killed=0 AND `item`.module='quoting' AND `item`.mid='".$quoting['quotingid']."'
					ORDER BY itemid ASC
				");
				$itemtr='';
				$total=$quantity=0;
				if($itemNum=$this->DB->numRows()){
					$itemtr = '<tr><td colspan="10"><table class="hundred"><thead><tr><th>询价产品</th><th>产品特有属性</th><th width="400px">说明</th><th>包装</th><th align="right">折扣</th><th align="right">数量</th><th align="right">单价</th><th align="right">小计</th></tr></thead><tbody>';
					while($item = $this->DB->fetchArray($items)){
						if($item['quantity']>0 AND $item['price']>0){
							$amount=$item['quantity']*$item['price'];
							$total+=$amount;
						}
						$product=$this->getProduct(array('productid'=>$item['productid'],'itemid'=>$item['productitemid']));
						$itemtr .= '<tr class="'.$this->iif($this->rotate(), 'odd', 'even').'">
						<td><a href="/s.php?module=product&action=view&productid='.$item['productid'].'" target="_blank">'.$product['title'].'</a></td>
						<td>'.$this->iif($item['materialid']>0,$item['standard'],$product['standard']).'</td>
						<td style="word-break:break-all">'.$item['caption'].'</td>
						<td>'.$item['packing'].'</td>
						<td align="right">'.$item['discount'].'</td>
						<td align="right">'.$item['quantity'].' '.$item['unit'].'</td>
						<td align="right">'.$item['price'].'</td>
						<td align="right">'.number_format($amount,2).'</td>
						</tr>';
						$quantity+=$item['quantity'];
					}
					$itemtr .= '<tr><td colspan="5" align="right">合计：</td><td align="right">'.number_format($quantity).'</td><td></td><td align="right">'.number_format($total, 2).'</td></tr></tbody></table></td></tr>';
				}
				$title = '';
				$dateline=date('Y-m-d',$quoting['dateline']);
				$created=date('Y-m-d',$quoting['created']);
				$modified=date('Y-m-d',$quoting['modified']);
				$flow=$this->listFlow(array('module'=>'STQuoting','statusid'=>$statusid));
				if($quoting['dateline']!='')$title .= '　<span class="small">交货日期：</span>'.$dateline;
				if($quoting['shipmethod']!='')$title .= '　<span class="small">运送方式：</span>'.$quoting['shipmethod'];
				if($quoting['port']!='')$title .= '　<span class="small">交货地点：</span>'.$quoting['port'];
				if($quoting['paymentterm']!='')$title .= '　<span class="small">支付条款：</span>'.$quoting['paymentterm'];
				if($quoting['deliveryterm']!='')$title .= '　<span class="small">交货条款：</span>'.$quoting['deliveryterm'];

$rtn['panel'].= <<<EOF
<table class="hundred">
<thead>
<tr><th colspan="10"><span class="right">{$options} <span class="plus hand" onclick="$('#quoting{$quoting['quotingid']}').toggle('fast');$(this).toggleClass('minus');"></span></span>	<span class="small">联系人：</span><span class="middle bold">{$quoting['contact']}</span>{$title}</th></tr>
</thead>
<tbody id="quoting{$quoting['quotingid']}" style="display:none">
<tr><td colspan="10">报价单状态：{$status}{$verifier}<span class="right">{$flow}</span></td></tr>
<tr class="odd">
	<td width="80">运送方式：</td><td class="middle">{$quoting['shipmethod']}</td>
	<td width="80">交货地点：</td><td class="middle">{$quoting['port']}</td>
	<td width="80">支付条款：</td><td class="middle">{$quoting['paymentterm']}</td>
	<td width="80">交货条款：</td><td class="middle">{$quoting['deliveryterm']}</td>
	<td width="80">交货日期：</td><td>{$dateline}　｜　运费：{$quoting['freight']}</td>
</tr>
<tr class="even">
	<td>规格说明：</td><td class="middle">{$quoting['standrad']}</td>
	<td>主要材料：</td><td class="middle">{$quoting['material']}</td>
	<td>工艺说明：</td><td class="middle">{$quoting['technics']}</td>
	<td>备　　注：</td><td class="middle">{$quoting['remark']}</td>
	<td>其　　他：</td><td>由 {$quoting['creator']} 于 {$created} 建立，由 {$quoting['modifier']} 于 {$modified} 最后修改</td>
</tr>
{$itemtr}
</tbody>
</table>
EOF;
			}
			$rtn['panel'] .= '</tbody></table>';
			$rtn['count'] = ' ('.$rtn['num'].')';
		}else{
			$rtn['off']=' disabled';
		}
		return $rtn;
	}
	// 互访
	function relatedVisit($r){
		if($r['customerid']>0){
			$r['condition'] .= " AND `visit`.customerid='".$r['customerid']."'";
			$module='customer';
		}
		if($r['supplierid']>0){
			$r['condition'] .= " AND `visit`.supplierid='".$r['supplierid']."'";
			$module='supplier';
		}
		if($r['orderby'] == ''){
			$r['orderby'] = '`visit`.modified';
		}
		if($r['direction'] == ''){
			$r['direction'] = 'DESC';
		}
		if($r['limit'] == ''){
			$r['limit'] = '0, 100';
		}
		$visits=$this->DB->query("
			SELECT visit.*,
				mu.username AS modifier,
				cu.username AS creator
			FROM `visit`
			LEFT JOIN user AS cu ON (cu.userid=`visit`.creator)
			LEFT JOIN user AS mu ON (mu.userid=`visit`.modifier)
			WHERE `visit`.killed=0".$r['condition']."
			ORDER BY ".$r['orderby']." ".$r['direction']."
			LIMIT ".$r['limit']."
		");
		$rtn['num'] = $this->DB->numRows();
		if($rtn['num']){
			$rtn['panel']='<table class="hundred"><thead><tr><th>访问时间</th><th>客户方</th><th>我方人员</th><th>地点</th><th>目的</th><th>关键内容</th><th>操作</th></tr><tbody>';
			while($visit = $this->DB->fetchArray($visits)){
				$rtn['panel'].='<tr class="'.$this->rotateLine().'">
				<td><span class="small">开始于</span> '.date('Y-m-d H:i', $visit['start']).'<br><span class="small">结束于</span> '.date('Y-m-d H:i', $visit['end']).'</td>
				<td>'.$visit['theirstaff'].'</td>
				<td>'.$visit['ourstaff'].'</td>
				<td>'.$visit['place'].'</td>
				<td>'.$visit['purpose'].'</td>
				<td>'.$visit['point'].'</td>
				<td><a href="/s.php?module='.$module.'&action=removeVisit&visitid='.$visit['visitid'].'" onclick="return confirm(\'你确定要删除这张询价单吗？(删除不可恢复！)\')">删除</a>　<a href="/s.php?module='.$module.'&action=updateVisit&visitid='.$visit['visitid'].'">修改</a></td></tr>';
			}
			$rtn['panel'] .= '</tbody></table>';
			$rtn['count'] = ' ('.$rtn['num'].')';
		}else{
			$rtn['off']=' disabled';
		}
		return $rtn;
	}
	// 询价
	function relatedPInquiry($r){
		if($r['materialid']>0){
			$items=$this->DB->query("SELECT DISTINCT mid FROM `item` WHERE module='inquiry' AND materialid='".$r['materialid']."'");
			if($this->DB->numRows()){
				while($item=$this->DB->fetchArray($items)){
					$id[]=$item['mid'];
				}
				if(is_Array($id)){
					$r['condition'].=" AND `inquiry`.inquiryid IN (".implode(',',$id).")";
				}
			}else{
				return array('num'=>0, 'panel'=>'','off'=>' disabled');
			}
		}
		if($r['customerid']>0){
			$r['condition'].=" AND `inquiry`.customerid='".$r['customerid']."'";
			$options='<a href="/s.php?module=customer&action=removeInquiry&inquiryid='.$inquiry['inquiryid'].'" onclick="return confirm(\'你确定要删除这张询价单吗？(删除不可恢复！)\')">删除</a>　<a href="/s.php?module=customer&action=updateInquiry&inquiryid='.$inquiry['inquiryid'].'">修改</a>　<a href="/p.php?action=customerInquiry&inquiryid='.$inquiry['inquiryid'].'" target="_blank">打印</a>　<a href="/s.php?module=customer&action=addQuoting&customerid='.$inquiry['customerid'].'&inquiryid='.$inquiry['inquiryid'].'">回复</a>';
		}
		if($r['supplierid']>0){
			$r['condition'].=" AND `inquiry`.supplierid='".$r['supplierid']."'";
			$options='<a href="/s.php?module=psupplier&action=removeInquiry&inquiryid='.$inquiry['inquiryid'].'" onclick="return confirm(\'你确定要删除这张询价单吗？(删除不可恢复！)\')">删除</a>　<a href="/s.php?module=psupplier&action=updateInquiry&inquiryid='.$inquiry['inquiryid'].'">修改</a>　<a href="/p.php?action=supplierInquiry&inquiryid='.$inquiry['inquiryid'].'" target="_blank">打印</a>　<a href="/s.php?module=psupplier&action=addQuoting&supplierid='.$inquiry['supplierid'].'&inquiryid='.$inquiry['inquiryid'].'">回复</a>';
		}
		if($r['orderby']==''){
			$r['orderby']='`inquiry`.modified';
		}
		if($r['direction']==''){
			$r['direction']='DESC';
		}
		if($r['limit']==''){
			$r['limit']='0, 100';
		}
		$inquirys=$this->DB->query("
			SELECT inquiry.*,
				supplier.title AS supplier,
				customer.title AS customer,
				shipmethod.title AS shipmethod,
				port.title AS port,
				paymentterm.title AS paymentterm,paymentterm.entitle AS enpaymentterm,
				deliveryterm.title AS deliveryterm,
				currency.title AS currency
			FROM `inquiry`
			LEFT JOIN supplier ON (supplier.supplierid=`inquiry`.supplierid)
			LEFT JOIN customer ON (customer.customerid=`inquiry`.customerid)
			LEFT JOIN currency ON (currency.currencyid=`inquiry`.currencyid)
			LEFT JOIN shipmethod ON (shipmethod.shipmethodid=`inquiry`.shipmethodid)
			LEFT JOIN port ON (port.portid=`inquiry`.portid)
			LEFT JOIN paymentterm ON (paymentterm.paymenttermid=`inquiry`.paymenttermid)
			LEFT JOIN deliveryterm ON (deliveryterm.deliverytermid=`inquiry`.deliverytermid)
			WHERE `inquiry`.killed=0".$r['condition']."
			ORDER BY ".$r['orderby']." ".$r['direction']."
			LIMIT ".$r['limit']."
		");
		if($rtn['num']=$this->DB->numRows()){
			while($inquiry=$this->DB->fetchArray($inquirys)){
				if($inquiry['quotingid']>0 AND $inquiry['replied']>0){
					$quoting=$this->DB->queryFirst("SELECT quotingid FROM quoting WHERE killed=0 AND inquiryid='".$inquiry['inquiryid']."' ORDER BY created DESC LIMIT 0,1");
					$reply='最后回复时间：'.date('Y-m-d H:i:s', $inquiry['replied']);
					$replyLink='<a href="/s.php?module=psupplier&action=quoting&quotingid='.$quoting['quotingid'].'" title="查看最新回复的报价单"><span class="small">('.$inquiry['replyTimes'].')</span></a>';
				}else{
					$reply='';
					$replyLink='<span class="small">('.$inquiry['replyTimes'].')</span>';
				}
				if($inquiry['customerid']>0){
					$options='<a href="/s.php?module=customer&action=removeInquiry&inquiryid='.$inquiry['inquiryid'].'" onclick="return confirm(\'你确定要删除这张询价单吗？(删除不可恢复！)\')">删除</a>　<a href="/s.php?module=customer&action=updateInquiry&inquiryid='.$inquiry['inquiryid'].'">修改</a>　<a href="/s.php?module=psupplier&action=updateInquiry&options=version&inquiryid='.$inquiry['inquiryid'].'">更新版本</a>　<a href="/p.php?action=customerInquiry&inquiryid='.$inquiry['inquiryid'].'" target="_blank">打印</a>　<a href="/s.php?module=customer&action=addQuoting&customerid='.$inquiry['customerid'].'&inquiryid='.$inquiry['inquiryid'].'" title="'.$reply.'">回复</a>'.$replyLink;
				}
				if($inquiry['supplierid']>0){
					$options='<a href="/s.php?module=psupplier&action=removeInquiry&inquiryid='.$inquiry['inquiryid'].'" onclick="return confirm(\'你确定要删除这张询价单吗？(删除不可恢复！)\')">删除</a>　<a href="/s.php?module=psupplier&action=updateInquiry&inquiryid='.$inquiry['inquiryid'].'">修改</a>　<a href="/s.php?module=psupplier&action=updateInquiry&options=version&inquiryid='.$inquiry['inquiryid'].'">更新版本</a>　<a href="/p.php?action=supplierInquiry&inquiryid='.$inquiry['inquiryid'].'" target="_blank">打印</a>　<a href="/s.php?module=psupplier&action=addQuoting&supplierid='.$inquiry['supplierid'].'&inquiryid='.$inquiry['inquiryid'].'" title="'.$reply.'"><span class="small">供应商</span>回复</a>'.$replyLink;
				}
				$items=$this->DB->query("
					SELECT `item`.*,
						`material`.materialno AS olMaterialno,`material`.title AS olMaterialTitle,`material`.standard AS olMaterialStandard,
						`packing`.title AS packing,
						`unit`.title AS unit
					FROM `item`
					LEFT JOIN `material` ON (`material`.materialid=`item`.materialid)
					LEFT JOIN `packing` ON (`packing`.packingid=`item`.packingid)
					LEFT JOIN `unit` ON (`unit`.unitid=`item`.unitid)
					WHERE `item`.killed=0 AND `item`.module='inquiry' AND `item`.mid='".$inquiry['inquiryid']."'
					ORDER BY itemid ASC
				");
				$itemtr='';
				if($itemNum=$this->DB->numRows()){
					$itemtr='<tr><td colspan="8"><table><thead><tr><th>询价物资编号</th><th>名称</th><th>规格</th><th>包装方式</th><th>包装</th><th>折扣</th><th>数量</th></tr></thead><tbody>';
					while($item=$this->DB->fetchArray($items)){
						$materialno=$this->iif($item['materialid']==0,$item['materialno'],$item['olMaterialno']);
						$materialTitle=$this->iif($item['materialid']==0,$item['materialTitle'],$item['olMaterialTitle']);
						$materialStandard=$this->iif($item['materialid']==0,$item['materialStandard'],$item['olMaterialStandard']);
						$itemtr.='<tr class="'.$this->rotateLine().'">
						<td><a href="/s.php?module=material&action=view&materialid='.$item['materialid'].'" target="_blank">'.$materialno.'</a></td>
						<td>'.$materialTitle.'</td>
						<td>'.$materialStandard.'</td>
						<td>'.$item['package'].'</td>
						<td>'.$item['packing'].'</td>
						<td>'.$item['discount'].'</td>
						<td>'.$item['quantity'].' '.$item['unit'].'</td>
						</tr>';
					}
					$itemtr.='</tbody></table></td></tr>';
				}
				$title='';
				if($inquiry['currency']!='')$title.='　<span class="small">币种：</span>'.$inquiry['currency'];
				if($inquiry['paymentterm']!='')$title.='　<span class="small">支付条款：</span>'.$inquiry['paymentterm'];
				if($inquiry['shipmethod']!='')$title.='　<span class="small">运送方式：</span>'.$inquiry['shipmethod'];
				if($inquiry['port']!='')$title.='　<span class="small">交货地点：</span>'.$inquiry['port'];
				if($inquiry['deliveryterm']!='')$title.='　<span class="small">交货条款：</span>'.$inquiry['deliveryterm'];
$rtn['panel'].=<<<EOF
<table class="hundred">
<thead>
<tr><th colspan="8"><span class="right">{$options} <span class="plus hand"></span></span>	<span class="normal">[<b>{$itemNum}</b>]</span> <span class="small">联系人：</span><span class="middle bold">{$inquiry['contact']}</span>{$title}</th></tr>
</thead>
<tbody id="inquiry{$inquiry['inquiryid']}" style="display:none">
<tr class="odd">
	<td width="80">运送方式：</td><td class="middle">{$inquiry['shipmethod']}</td>
	<td width="80">交货地点：</td><td class="middle">{$inquiry['port']}</td>
	<td width="80">支付条款：</td><td class="middle">{$inquiry['paymentterm']}({$inquiry['enpaymentterm']})</td>
	<td width="80">交货条款：</td><td class="middle">{$inquiry['deliveryterm']}</td>
</tr>
<tr class="even">
	<td>规格说明：</td><td class="middle">{$inquiry['standard']}</td>
	<td>主要材料：</td><td class="middle">{$inquiry['material']}</td>
	<td>工艺说明：</td><td class="middle">{$inquiry['technics']}</td>
	<td>备　　注：</td><td class="middle">{$inquiry['remark']}</td>
</tr>
{$itemtr}
</tbody>
</table>
EOF;
			}
			$rtn['panel'].='</tbody></table>';
			$rtn['count']=' ('.$rtn['num'].')';
		}else{
			$rtn['off']=' disabled';
		}
		return $rtn;
	}
	// 报价
	function relatedPQuoting($r){
		if($r['materialid']>0){
			$items=$this->DB->query("SELECT DISTINCT mid FROM `item` WHERE module='quoting' AND materialid='".$r['materialid']."'");
			if($this->DB->numRows()){
				while($item=$this->DB->fetchArray($items)){
					$id[]=$item['mid'];
				}
				if(is_Array($id)){
					$r['condition'].=" AND `quoting`.quotingid IN (".implode(',',$id).")";
				}
			}else{
				return array('num'=>0,'off'=>'disabled','panel'=>'');
			}
		}
		if($r['customerid']>0){
			$r['condition'].=" AND `quoting`.customerid='".$r['customerid']."'";
			$options='<a href="/s.php?module=customer&action=removeQuoting&quotingid='.$quoting['quotingid'].'" onclick="return confirm(\'你确定要删除这张询价单吗？(删除不可恢复！)\')">删除</a>　<a href="/s.php?module=customer&action=updateQuoting&quotingid='.$quoting['quotingid'].'">修改</a>　<a href="/p.php?action=customerQuoting&quotingid='.$quoting['quotingid'].'" target="_blank">打印</a>　<a href="/s.php?module=customer&action=addQuoting&customerid='.$quoting['customerid'].'&quotingid='.$quoting['quotingid'].'">回复</a>';
		}
		if($r['supplierid']>0){
			$r['condition'].=" AND `quoting`.supplierid='".$r['supplierid']."'";
			$options='<a href="/s.php?module=psupplier&action=removeQuoting&quotingid='.$quoting['quotingid'].'" onclick="return confirm(\'你确定要删除这张询价单吗？(删除不可恢复！)\')">删除</a>　<a href="/s.php?module=psupplier&action=updateQuoting&quotingid='.$quoting['quotingid'].'">修改</a>　<a href="/p.php?action=supplierQuoting&quotingid='.$quoting['quotingid'].'" target="_blank">打印</a>　<a href="/s.php?module=psupplier&action=addQuoting&supplierid='.$quoting['supplierid'].'&quotingid='.$quoting['quotingid'].'">回复</a>';
		}
		if($r['orderby']==''){
			$r['orderby']='`quoting`.modified';
		}
		if($r['direction']==''){
			$r['direction']='DESC';
		}
		if($r['limit']==''){
			$r['limit']='0, 100';
		}
		$quotings=$this->DB->query("
			SELECT quoting.*,
				supplier.title AS supplier,
				customer.title AS customer,
				shipmethod.title AS shipmethod,
				port.title AS port,
				paymentterm.title AS paymentterm,paymentterm.entitle AS enpaymentterm,
				deliveryterm.title AS deliveryterm,
				currency.title AS currency
			FROM `quoting`
			LEFT JOIN supplier ON (supplier.supplierid=`quoting`.supplierid)
			LEFT JOIN customer ON (customer.customerid=`quoting`.customerid)
			LEFT JOIN currency ON (currency.currencyid=`quoting`.currencyid)
			LEFT JOIN shipmethod ON (shipmethod.shipmethodid=`quoting`.shipmethodid)
			LEFT JOIN port ON (port.portid=`quoting`.portid)
			LEFT JOIN paymentterm ON (paymentterm.paymenttermid=`quoting`.paymenttermid)
			LEFT JOIN deliveryterm ON (deliveryterm.deliverytermid=`quoting`.deliverytermid)
			WHERE `quoting`.killed=0".$r['condition']."
			ORDER BY ".$r['orderby']." ".$r['direction']."
			LIMIT ".$r['limit']."
		");
		if($rtn['num']=$this->DB->numRows()){
			while($quoting=$this->DB->fetchArray($quotings)){
				if($quoting['inquiryid']>0){
					$orgLink='<a href="/s.php?module=psupplier&action=inquiry&inquiryid='.$quoting['inquiryid'].'" title="查看对应的询价单" class="small">对应询价单</a> ';
				}else{
					$orgLink='';
				}
				if($quoting['customerid']>0){
					$options=$orgLink.'<a href="/s.php?module=customer&action=removeQuoting&quotingid='.$quoting['quotingid'].'" onclick="return confirm(\'你确定要删除这张报价单吗？(删除不可恢复！)\')">删除</a>　<a href="/s.php?module=customer&action=updateQuoting&quotingid='.$quoting['quotingid'].'">修改</a>　<a href="/p.php?action=customerQuoting&quotingid='.$quoting['quotingid'].'" target="_blank">打印</a>';
				}
				if($quoting['supplierid']>0){
					$options=$orgLink.'<a href="/s.php?module=psupplier&action=removeQuoting&quotingid='.$quoting['quotingid'].'" onclick="return confirm(\'你确定要删除这张报价单吗？(删除不可恢复！)\')">删除</a>　<a href="/s.php?module=psupplier&action=updateQuoting&quotingid='.$quoting['quotingid'].'">修改</a>　<a href="/p.php?action=supplierQuoting&quotingid='.$quoting['quotingid'].'" target="_blank">打印</a>';
				}
				$items=$this->DB->query("
					SELECT `item`.*,
						`material`.materialno AS olMaterialno,`material`.title AS olMaterialTitle,`material`.standard AS olMaterialStandard,
						packing.title AS packing,
						unit.title AS unit
					FROM `item`
					LEFT JOIN `material` ON (`material`.materialid=`item`.materialid)
					LEFT JOIN `packing` ON (`packing`.packingid=`item`.packingid)
					LEFT JOIN `unit` ON (`unit`.unitid=`item`.unitid)
					WHERE `item`.module='quoting' AND `item`.mid='".$quoting['quotingid']."'
					ORDER BY itemid ASC
				");
				$itemtr='';
				if($itemNum=$this->DB->numRows()){
					$itemtr='<tr><td colspan="9"><table><thead><tr><th>询价物资</th><th>物资名称</th><th>规格</th><th>包装方式</th><th>包装</th><th>折扣</th><th>数量</th></tr></thead><tbody>';
					while($item=$this->DB->fetchArray($items)){
						$materialno=$this->iif($item['materialid']==0,$item['materialno'],$item['olMaterialno']);
						$materialTitle=$this->iif($item['materialid']==0,$item['materialTitle'],$item['olMaterialTitle']);
						$materialStandard=$this->iif($item['materialid']==0,$item['materialStandard'],$item['olMaterialStandard']);
						$itemtr.='<tr class="'.$this->rotateLine().'">
						<td><a href="/s.php?module=material&action=view&materialid='.$item['materialid'].'" target="_blank">'.$materialno.'</a></td>
						<td>'.$materialTitle.'</td>
						<td>'.$materialStandard.'</td>
						<td>'.$item['package'].'</td>
						<td>'.$item['packing'].'</td>
						<td>'.$item['discount'].'</td>
						<td>'.$item['quantity'].' '.$item['unit'].'</td>
						</tr>';
					}
					$itemtr.='</tbody></table></td></tr>';
				}
				$title='';
				if($quoting['currency']!='')$title.='　<span class="small">币种：</span>'.$quoting['currency'];
				if($quoting['paymentterm']!='' OR $quoting['enpaymentterm']!='' )$title.='　<span class="small">支付条款：</span>'.$quoting['paymentterm'].'('.$quoting['enpaymentterm'].')';
				if($quoting['shipmethod']!='')$title.='　<span class="small">运送方式：</span>'.$quoting['shipmethod'];
				if($quoting['port']!='')$title.='　<span class="small">交货地点：</span>'.$quoting['port'];
				if($quoting['deliveryterm']!='')$title.='　<span class="small">交货条款：</span>'.$quoting['deliveryterm'];
$rtn['panel'].=<<<EOF
<table class="hundred">
<thead>
<tr><th colspan="8"><span class="right">{$options} <span class="plus hand"></span></span>	<span class="normal">[<b>{$itemNum}</b>]</span> <span class="small">联系人：</span><span class="middle bold">{$quoting['contact']}</span>{$title}</th></tr>
</thead>
<tbody id="quoting{$quoting['quotingid']}" style="display:none">
<tr class="odd">
	<td width="80">运送方式：</td><td class="middle">{$quoting['shipmethod']}</td>
	<td width="80">交货地点：</td><td class="middle">{$quoting['port']}</td>
	<td width="80">支付条款：</td><td class="middle">{$quoting['paymentterm']}({$quoting['enpaymentterm']})</td>
	<td width="80">交货条款：</td><td class="middle">{$quoting['deliveryterm']}</td>
</tr>
<tr class="even">
	<td>规格说明：</td><td class="middle">{$quoting['standard']}</td>
	<td>主要材料：</td><td class="middle">{$quoting['material']}</td>
	<td>工艺说明：</td><td class="middle">{$quoting['technics']}</td>
	<td>备　　注：</td><td class="middle">{$quoting['remark']}</td>
</tr>
{$itemtr}
</tbody>
</table>
EOF;
			}
			$rtn['panel'].='</tbody></table>';
			$rtn['count']=' ('.$rtn['num'].')';
		}else{
			$rtn['off']=' disabled';
		}
		return $rtn;
	}
	// 互访
	function relatedPVisit($r){
		if($r['customerid']>0){
			$r['condition'].=" AND `visit`.customerid='".$r['customerid']."'";
			$module='customer';
		}
		if($r['supplierid']>0){
			$r['condition'].=" AND `visit`.supplierid='".$r['supplierid']."'";
			$module='psupplier';
		}
		if($r['orderby']==''){
			$r['orderby']='`visit`.modified';
		}
		if($r['direction']==''){
			$r['direction']='DESC';
		}
		if($r['limit']==''){
			$r['limit']='0, 100';
		}
		$visits=$this->DB->query("
			SELECT visit.*,
				mu.username AS modifier,
				cu.username AS creator
			FROM `visit`
			LEFT JOIN user AS cu ON (cu.userid=`visit`.creator)
			LEFT JOIN user AS mu ON (mu.userid=`visit`.modifier)
			WHERE `visit`.killed=0".$r['condition']."
			ORDER BY ".$r['orderby']." ".$r['direction']."
			LIMIT ".$r['limit']."
		");
		$rtn['num']=$this->DB->numRows();
		if($rtn['num']){
			$rtn['panel']='<table class="hundred"><thead><tr><th>访问时间</th><th>客户方</th><th>我方人员</th><th>地点</th><th>目的</th><th>关键内容</th><th>操作</th></tr><tbody>';
			while($visit=$this->DB->fetchArray($visits)){
				$rtn['panel'].='<tr class="'.$this->rotateLine().'">
				<td><span class="small">开始于</span> '.date('Y-m-d H:i', $visit['start']).'<br><span class="small">结束于</span> '.date('Y-m-d H:i', $visit['end']).'</td>
				<td>'.$visit['theirstaff'].'</td>
				<td>'.$visit['ourstaff'].'</td>
				<td>'.$visit['place'].'</td>
				<td>'.$visit['purpose'].'</td>
				<td>'.$visit['point'].'</td>
				<td><a href="/s.php?module='.$module.'&action=removeVisit&visitid='.$visit['visitid'].'" onclick="return confirm(\'你确定要删除这张互访单吗？(删除不可恢复！)\')">删除</a>　<a href="/s.php?module='.$module.'&action=updateVisit&visitid='.$visit['visitid'].'">修改</a></td></tr>';
			}
			$rtn['panel'].='</tbody></table>';
			$rtn['count']=' ('.$rtn['num'].')';
		}else{
			$rtn['off']=' disabled';
		}
		return $rtn;
	}
	// 发票
	function relatedPInvoice($r){
		if($r['materialid']>0){
			$items=$this->DB->query("SELECT DISTINCT mid FROM `item` WHERE module='invoice' AND materialid='".$r['materialid']."'");
			if($this->DB->numRows()){
				while($item=$this->DB->fetchArray($items)){
					$id[]=$item['mid'];
				}
				if(is_Array($id)){
					$r['condition'].=" AND `pinvoice`.pinvoiceid IN (".implode(',',$id).")";
				}
			}else{
				return array('num'=>0, 'panel'=>'','off'=>' disabled');
			}
		}
		if($r['customerid']>0){
			$r['condition'].=" AND `pinvoice`.customerid='".$r['customerid']."'";
		}
		if($r['supplierid']>0){
			$r['condition'].= " AND `pinvoice`.supplierid='".$r['supplierid']."'";
		}
		if($r['porderid']>0){
			$r['condition'].= " AND `pinvoice`.porderid='".$r['porderid']."'";
		}
		if($r['orderby']==''){
			$r['orderby']='`pinvoice`.modified';
		}
		if($r['direction']==''){
			$r['direction']='DESC';
		}
		if($r['limit']==''){
			$r['limit']='0, 100';
		}
		$invoices=$this->DB->query("
			SELECT `pinvoice`.pinvoiceid,`pinvoice`.title,`pinvoice`.invoiceno,`pinvoice`.dateOfArrival,
					`pinvoice`.taxRate,`pinvoice`.created,`pinvoice`.killed,`pinvoice`.amount,
				`supplier`.supplierid,`supplier`.title AS supplier,
				`porder`.porderid,`porder`.orderno,
				`currency`.title AS currency,
				u.username AS creator,
				us.username AS modifier
			FROM `pinvoice`
			LEFT JOIN `supplier` ON (`supplier`.supplierid=`pinvoice`.supplierid)
			LEFT JOIN `porder` ON (`porder`.porderid=`pinvoice`.porderid)
			LEFT JOIN `currency` ON (`currency`.currencyid=`pinvoice`.currencyid)
			LEFT JOIN `user` AS u ON (u.userid=`pinvoice`.creator)
			LEFT JOIN `user` AS us ON (us.userid=`pinvoice`.modifier)
			WHERE `pinvoice`.killed=0 ".$r['condition']."
			ORDER BY ".$r['orderby']." ".$r['direction']."
			LIMIT ".$r['limit']."
		");
		$rtn['num']=$this->DB->numRows();
		if($rtn['num']){
			$index=1;
			while($invoice=$this->DB->fetchArray($invoices)){
				$invoice['dateOfArrival']=date('Y-m-d',$invoice['dateOfArrival']);
				$invoice['created']=date('Y-m-d',$invoice['created']);
				$itemtr='';
				$items=$this->DB->query("
					SELECT `item`.itemid,`item`.materialid,`item`.discount,`item`.quantity,`item`.tax,`item`.price,`item`.amount,
						`material`.materialno,`material`.title AS material,`material`.standard,
						`porder`.orderno,
						brand.title AS brand,
						packing.title AS packing,
						unit.title AS unit
					FROM `item`
					LEFT JOIN `material` ON (`material`.materialid=`item`.materialid)
					LEFT JOIN `porder` ON (`porder`.porderid=`item`.porderid)
					LEFT JOIN packing ON (packing.packingid=`item`.packingid)
					LEFT JOIN brand ON (brand.brandid=`item`.brandid)
					LEFT JOIN unit ON (unit.unitid=`item`.unitid)
					WHERE `item`.killed=0 AND `item`.module='pinvoice' AND mid='".$invoice['pinvoiceid']."'
					ORDER BY `item`.ordering, `item`.modified ASC, `item`.itemid ASC
				");
				if($this->DB->numRows()){
					$i=1;
					$itemtr.='<tr class="even center" colspan=10><td width="15">ID</td><td width="30">订单号</td><td width="250">货物或应税劳务名称</td><td width="50">总数/单位</td><td width="50">单价</td><td width="60">总金额</td><td width="50">税率</td><td width="100">税额</td></tr>';
					while($item=$this->DB->fetchArray($items)){
						$itemtr.='<tr class="'.$this->iif($this->rotate(), 'odd', 'even').'">
							<td>'.$i.'</td>
							<td><a href="/s.php?module=porder&action=view&porderid='.$item['porderid'].'" target="_blank">'.$item['orderno'].'</a></td>
							<td>'.$item['materialno'].' <a href="/s.php?module=material&action=view&materialid='.$item['materialid'].'" target="_blank">'.$item['material'].' '.$item['standard'].'</a></td>
							<td align="right">'.$item['quantity'].$item['unit'].'</td>
							<td align="right">'.$item['price'].'</td>
							<td align="right">'.$item['amount'].'</td>
							<td align="right">'.($invoice['taxRate']*100).'%</td>
							<td align="right">'.$item['tax'].'</td></tr>';
					}
				}
				if($invoice['invoiceno']!='')$title.='　<span class="small">发票编号：</span><a href="/s.php?module=pinvoice&action=view&pinvoiceid='.$invoice['pinvoiceid'].'">'.$invoice['invoiceno'].'</a>';
				if($invoice['supplier']!='')$title.='　<span class="small">供应商：</span><a href="/s.php?module='.$supplierM.'&action=view&supplierid='.$invoice['supplierid'].'">'.$invoice['supplier'].'</a>';
				if($invoice['currency']!='')$title.='　<span class="small">币种：</span>'.$invoice['currency'];
				if($invoice['created']!='')$title.='　<span class="small">创建时间：</span>'.$invoice['created'];
				$options=$this->iif($invoice['killed']==0,'<a href="/s.php?module=pinvoice&action=update&pinvoiceid='.$invoice['pinvoiceid'].'">修改</a> <a href="/s.php?module=pinvoice&action=kill&pinvoiceid='.$invoice['pinvoiceid'].'&rt=list" onclick="return confirm(\'你确定要删除这个应付 '.$invoice['orderno'].' 吗？\');">删除</a>','<a href="/s.php?module=pinvoice&action=restorePayment&pinvoiceid='.$invoice['pinvoiceid'].'">恢复</a>');
				$rtn['panel'].=<<<EOF
<table class="hundred">
<thead>
<tr><th colspan="15"><span class="right">{$options} <span class="plus"></span></span>	<span class="normal">[<b>{$index}</b>]</span>{$title}</th></tr>
</thead>
<tbody id="invoice{$invoice['pinvoiceid']}" style="display:none">
{$itemtr}
</tbody>
</table>
EOF;
				$index++;
			}
			$rtn['count']=' ('.$rtn['num'].')';
		}else{
			$rtn['off']=' disabled';
		}
		return $rtn;
	}
	// 发票明细
	function relatedPInvoiceItem($o){
		$items=$this->DB->query("
			SELECT `item`.itemid,`item`.materialid,`item`.discount,`item`.quantity,`item`.tax,`item`.price,`item`.amount,
				`material`.materialno,`material`.title AS material,`material`.standard,
				`porder`.orderno,
				brand.title AS brand,
				packing.title AS packing,
				unit.title AS unit
			FROM `item`
			LEFT JOIN `material` ON (`material`.materialid=`item`.materialid)
			LEFT JOIN `porder` ON (`porder`.porderid=`item`.porderid)
			LEFT JOIN packing ON (packing.packingid=`item`.packingid)
			LEFT JOIN brand ON (brand.brandid=`item`.brandid)
			LEFT JOIN unit ON (unit.unitid=`item`.unitid)
			WHERE `item`.killed=0 AND `item`.module='pinvoice' AND mid='".$o['pinvoiceid']."'
			ORDER BY `item`.ordering, `item`.modified ASC, `item`.itemid ASC
		");
		if($this->DB->numRows()){
			$i=1;
			$r='<tr class="even center" colspan=10><td width="20">ID</td><td width="20">订单号</td><td width="250">货物或应税劳务名称</td><td width="50">数量</td><td width="50">单位</td><td width="50">单价</td><td width="60">金额</td><td width="50">税率</td><td width="100">税额</td></tr>';
			while($item=$this->DB->fetchArray($items)){
				$r.='<tr class="'.$this->iif($this->rotate(), 'odd', 'even').'">
					<td>'.$i.'</td>
					<td><a href="/s.php?module=porder&action=view&porderid='.$item['porderid'].'" target="_blank">'.$item['orderno'].'</a></td>
					<td>'.$item['materialno'].' <a href="/s.php?module=material&action=view&materialid='.$item['materialid'].'" target="_blank">'.$item['material'].' '.$item['standard'].'</a></td>
					<td align="right">'.$item['quantity'].'</td>
					<td>'.$item['unit'].'</td>
					<td align="right">'.$item['price'].'</td>
					<td align="right">'.$item['amount'].'</td>
					<td>'.$invoice['taxRate'].'</td>
					<td align="right">'.$item['tax'].'</td></tr>';
				$i++;
				$quantity+=$item['quantity'];
				$total+=$item['amount'];
				$tax+=$item['tax'];
			}
			$total=number_format($total, 2);
			$r.='<tr><td colspan="6" align="right">合计：</td><td align="right" class="middle bold">'.$total.'</td><td align="right">税额：</td><td align="right" class="middle bold">'.$tax.'</td></tr>';
		}
		return $r;
	}


	// 发票
	function relatedInvoice($r){
		if($r['materialid']>0){
			$items=$this->DB->query("SELECT DISTINCT mid FROM `item` WHERE module='invoice' AND materialid='".$r['materialid']."'");
			if($this->DB->numRows()){
				while($item=$this->DB->fetchArray($items)){
					$id[]=$item['mid'];
				}
				if(is_Array($id)){
					$r['condition'].=" AND `pinvoice`.pinvoiceid IN (".implode(',',$id).")";
				}
			}else{
				return array('num'=>0, 'panel'=>'','off'=>' disabled');
			}
		}
		if($r['customerid']>0){
			$r['condition'].=" AND `invoice`.customerid='".$r['customerid']."'";
		}
		if($r['supplierid']>0){
			$r['condition'].= " AND `invoice`.supplierid='".$r['supplierid']."'";
		}
		if($r['orderid']>0){
			$r['condition'].= " AND `invoice`.orderid='".$r['orderid']."'";
		}
		if($r['orderby']==''){
			$r['orderby']='`invoice`.modified';
		}
		if($r['direction']==''){
			$r['direction']='DESC';
		}
		if($r['limit']==''){
			$r['limit']='0, 100';
		}

		$sql = "
			SELECT `invoice`.invoiceid,`invoice`.title,`invoice`.invoiceno,`invoice`.dateOfArrival,
					`invoice`.created,`invoice`.killed,
				`order`.orderid,`order`.orderno,
				`currency`.title AS currency,
				u.username AS creator,
				us.username AS modifier
			FROM `invoice`
			LEFT JOIN `order` ON (`order`.orderid=`invoice`.orderid)
			LEFT JOIN `currency` ON (`currency`.currencyid=`invoice`.currencyid)
			LEFT JOIN `user` AS u ON (u.userid=`invoice`.creator)
			LEFT JOIN `user` AS us ON (us.userid=`invoice`.modifier)
			WHERE `invoice`.killed=0 ".$r['condition']."
			ORDER BY ".$r['orderby']." ".$r['direction']."
			LIMIT ".$r['limit']."
		";
		//echo $sql;

		$invoices=$this->DB->query($sql);
		$rtn['num']=$this->DB->numRows();
		if($rtn['num']){
			$index=1;
			while($invoice=$this->DB->fetchArray($invoices)){
				$titles='';
				$invoice['dateOfArrival']=date('Y-m-d',$invoice['dateOfArrival']);
				$invoice['created']=date('Y-m-d',$invoice['created']);
				$itemtr='';
				$s = "
					SELECT `item`.itemid,`item`.productid,`item`.productAttributes,`item`.materialid,`item`.discount,`item`.quantity,`item`.tax,`item`.price,`item`.amount,
						`material`.materialno,`material`.title AS material,`material`.standard,
						`order`.orderno,
						product.title AS product,
						brand.title AS brand,
						packing.title AS packing,
						unit.title AS unit
					FROM `item`
					LEFT JOIN `material` ON (`material`.materialid=`item`.materialid)
					LEFT JOIN `product` ON (product.productid=item.productid)
					LEFT JOIN `order` ON (`order`.orderid=`item`.orderid)
					LEFT JOIN packing ON (packing.packingid=`item`.packingid)
					LEFT JOIN brand ON (brand.brandid=`item`.brandid)
					LEFT JOIN unit ON (unit.unitid=`item`.unitid)
					WHERE `item`.killed=0 AND `item`.module='invoice' AND mid='".$invoice['invoiceid']."'
					ORDER BY `item`.ordering, `item`.modified ASC, `item`.itemid ASC
				";
				//echo "<br/><br/>====<br>".$s."<br>===<br>";
				$items=$this->DB->query($s);
				if($this->DB->numRows()){
					$i=1;
					$itemtr.='<tr class="even center" colspan=10><td width="15">ID</td><td width="30">订单号</td><td width="150">产品或配件名称</td><td>产品或配件特有属性</td><td width="50">总数/单位</td><td width="50">单价</td><td width="60">总金额</td><td width="50">税率</td><td width="100">税额</td></tr>';
					while($item=$this->DB->fetchArray($items)){
						$selectStr = '';
						if(!empty($item['productAttributes'])){
							$nameStr = explode("#",$item['productAttributes']);
							$valueStr = explode(",",$nameStr[1]);
							array_pop($valueStr);
							$attributes=$this->DB->query("
								SELECT an.nameid,an.title AS name,
									av.valueid,av.title AS value
								FROM  attributename AS an
								LEFT JOIN attributevalue AS av ON (av.nameid=an.nameid)
								WHERE an.killed=0 AND av.killed=0 AND an.module='product' AND an.mid='".$nameStr[0]."'
								ORDER BY an.ordering ASC
							");
							$nameId=$title=$value=$attributeCount=array();
							if($this->DB->numRows()){
								while($attribute=$this->DB->fetchArray($attributes)){
									if(in_array($attribute['nameid'],$nameId)){
										$title[$attribute['nameid']]=$attribute['name'];
										$value[$attribute['nameid']][]=$attribute['value'];
										$valueId[$attribute['nameid']][]=$attribute['valueid'];
										$attributeCount[$attribute['nameid']]++;
									}else{
										$nameId[]=$attribute['nameid'];
										$title[$attribute['nameid']]=$attribute['name'];
										$value[$attribute['nameid']][]=$attribute['value'];
										$valueId[$attribute['nameid']][]=$attribute['valueid'];
										$attributeCount[$attribute['nameid']]=1;
									}
								}
							}
							foreach ($nameId as $key=>$val){
								$selectStr .= ''.$title[$val].'：';
								for($n=0;$n<count($value[$val]);$n++){
									if($valueId[$val][$n]==$valueStr[$key]){
										$selectStr .= '<span class="darkred">'.$value[$val][$n].'</span>';
									}
								}
								$selectStr .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
							}
						}else{
							$selectStr .= '此产品或配件没有属性';
						}

						$itemtr.='<tr class="'.$this->iif($this->rotate(), 'odd', 'even').'">
							<td>'.$i.'</td>
							<td><a href="/s.php?module=order&action=view&orderid='.$item['orderid'].'" target="_blank">'.$item['orderno'].'</a></td>
							<td><a href="/s.php?module=product&action=view&productid='.$item['productid'].'" target="_blank">'.$item['product'].'</a></td>
							<td>'.$selectStr.'</td>
							<td align="right">'.$item['quantity'].$item['unit'].'</td>
							<td align="right">'.$item['price'].'</td>
							<td align="right">'.$item['amount'].'</td>
							<td align="right">'.($invoice['taxRate']*100).'%</td>
							<td align="right">'.$item['tax'].'</td></tr>';
					}
				}
				if($invoice['invoiceno']!='') $titles.='　<span class="small">发票编号：</span><a href="/s.php?module=invoice&action=view&invoiceid='.$invoice['invoiceid'].'">'.$invoice['invoiceno'].'</a>';
				if($invoice['supplier']!='') $titles.='　<span class="small">供应商：</span><a href="/s.php?module='.$supplierM.'&action=view&supplierid='.$invoice['supplierid'].'">'.$invoice['supplier'].'</a>';
				if($invoice['currency']!='') $titles.='　<span class="small">币种：</span>'.$invoice['currency'];
				if($invoice['created']!='') $titles.='　<span class="small">创建时间：</span>'.$invoice['created'];
				$options=$this->iif($invoice['killed']==0,'<a href="/s.php?module=invoice&action=update&invoiceid='.$invoice['invoiceid'].'">修改</a> <a href="/s.php?module=invoice&action=kill&invoiceid='.$invoice['invoiceid'].'&rt=list" onclick="return confirm(\'你确定要删除这个应付 '.$invoice['orderno'].' 吗？\');">删除</a>','<a href="/s.php?module=pinvoice&action=restorePayment&pinvoiceid='.$invoice['pinvoiceid'].'">恢复</a>');
				$rtn['panel'].=<<<EOF
<table class="hundred">
<thead>
<tr><th colspan="15"><span class="right">{$options} <span class="plus hand" onclick="$('#invoice{$invoice['invoiceid']}').toggle('fast');$(this).toggleClass('minus');"></span></span>	<span class="normal">[<b>{$index}</b>]</span>{$titles}</th></tr>
</thead>
<tbody id="invoice{$invoice['invoiceid']}" style="display:none">
{$itemtr}
</tbody>
</table>
EOF;
				$index++;
			}
			$rtn['count']=' ('.$rtn['num'].')';
		}else{
			$rtn['off']=' disabled';
		}
		return $rtn;
	}


	// 发票明细
	function relatedInvoiceItem($o){
		$items = $this->DB->query("
			SELECT item.*,
				productitem.attributevalue,
				productitem.mid AS productId,
				brand.title AS brand,
				packing.title AS packing,
				unit.title AS unit
			FROM `item`
			LEFT JOIN `productitem` ON (productitem.productitemid=item.productitemid)
			LEFT JOIN packing ON (packing.packingid=item.packingid)
			LEFT JOIN brand ON (brand.brandid=item.brandid)
			LEFT JOIN unit ON (unit.unitid=item.unitid)
			WHERE `item`.killed=0 AND `item`.module='invoice' AND  `item`.mid='".$o['invoiceid']."'
			ORDER BY itemid ASC, modified ASC
		");
		if($this->DB->numRows()){
			$k=1;
			while($item = $this->DB->fetchArray($items)){
				$selectStr = '';
				if(!empty($item['attributevalue'])){
					$valueStr = explode(",",$item['attributevalue']);

					$product=$this->DB->queryFirst("
						SELECT `product`.title,`product`.entitle
						FROM  `product`
						WHERE productid='".$item['productId']."'
					");
					$order=$this->DB->queryFirst("
							SELECT `order`.*
							FROM  `order`
							WHERE orderid='".$item['orderid']."'
						");

					$attributes=$this->DB->query("
						SELECT an.nameid,an.title AS name,
							av.valueid,av.title AS value
						FROM  attributename AS an
						LEFT JOIN attributevalue AS av ON (av.nameid=an.nameid)
						WHERE an.killed=0 AND av.killed=0 AND an.module='product' AND an.mid='".$item['productId']."'
						ORDER BY an.nameid ASC
					");

					$nameId=$title=$value=$attributeCount=array();
					if($this->DB->numRows()){
						while($attribute=$this->DB->fetchArray($attributes)){
							if(in_array($attribute['nameid'],$nameId)){
								$title[$attribute['nameid']]=$attribute['name'];
								$value[$attribute['nameid']][]=$attribute['value'];
								$valueId[$attribute['nameid']][]=$attribute['valueid'];
								$attributeCount[$attribute['nameid']]++;
							}else{
								$nameId[]=$attribute['nameid'];
								$title[$attribute['nameid']]=$attribute['name'];
								$value[$attribute['nameid']][]=$attribute['value'];
								$valueId[$attribute['nameid']][]=$attribute['valueid'];
								$attributeCount[$attribute['nameid']]=1;
							}
						}
					}
					foreach ($nameId as $key=>$val){
						$selectStr .= ''.$title[$val].'：';
						for($n=0;$n<count($value[$val]);$n++){
							if($valueId[$val][$n]==$valueStr[$key]){
								$selectStr .= '<span class="darkred">'.$value[$val][$n].'</span>';
							}
						}
						$selectStr .= '　';
					}
				}else{
					$product=$this->DB->queryFirst("
						SELECT `product`.title,`product`.entitle,`product`.materialid
						FROM  `product`
						WHERE productid='".$item['productid']."'
					");
					$item['productId'] = $item['productid'];
					$material = $this->DB->queryFirst("
						SELECT `material`.materialid,`material`.standard
						FROM  `material`
						WHERE materialid='".$product['materialid']."'
					");
					$selectStr = $material['standard'];
				}

				$itemtr .= '<tr class="'.$this->iif($this->rotate(), 'odd', 'even').'"><td class="small">'.$item['itemid'].'</td>
				<td><a href="/s.php?module=order&action=view&orderid='.$item['orderid'].'" target="_blank">'.$order['orderno'].'</a></td>
				<td><a href="/s.php?module=product&action=view&productid='.$item['productId'].'" target="_blank">'.$product['title'].'</a></td>
				<td>'.$selectStr.'</td>
				<td align="right">'.$this->iif($item['discount']>0, $item['discount'].'%', '').'</td>
				<td align="right">'.number_format($item['quantity']).' '.$item['unit'].'</td>
				<td align="right">'.number_format($item['price'], 2).'</td>
				<td align="right">'.number_format($item['amount'], 2).'</td>
				</tr>';
				$k++;
				$quantity+=$item['quantity'];
				$total+=$item['amount'];
			}
			$rtn['total'] = $total;
			$total=number_format($total, 2);
$rtn['tr']= <<<EOF
		<tr><td>ID</td><td width="110">订单号</td><td width="180">产品</td><td>产品或配件特有属性</td><td align="right">折扣</td><td align="right">数量/单位</td><td align="right">单价</td><td align="right">小计</td></tr>
		{$itemtr}
		<tr><td colspan="5" align="right">合计：</td><td align="right">{$quantity}</td><td></td><td align="right" class="middle bold">{$total}</td></tr>
EOF;
		}
		return $rtn;
	}

	// 发票明细
	function relatedPrintInvoiceItem($o){
		$items = $this->DB->query("
			SELECT item.*,
				productitem.attributevalue,
				productitem.mid AS productId,
				brand.title AS brand,
				packing.entitle AS packing,
				unit.entitle AS unit
			FROM `item`
			LEFT JOIN `productitem` ON (productitem.productitemid=item.productitemid)
			LEFT JOIN packing ON (packing.packingid=item.packingid)
			LEFT JOIN brand ON (brand.brandid=item.brandid)
			LEFT JOIN unit ON (unit.unitid=item.unitid)
			WHERE `item`.killed=0 AND `item`.module='invoice' AND  `item`.mid='".$o['invoiceid']."'
			ORDER BY itemid ASC, modified ASC
		");
		if($this->DB->numRows()){
			$k=1;
			while($item = $this->DB->fetchArray($items)){
				$selectStr = '';
				if(!empty($item['attributevalue'])){
					$valueStr = explode(",",$item['attributevalue']);

					$product=$this->DB->queryFirst("
						SELECT `product`.title,`product`.entitle
						FROM  `product`
						WHERE productid='".$item['productId']."'
					");
					$order=$this->DB->queryFirst("
							SELECT `order`.*
							FROM  `order`
							WHERE orderid='".$item['orderid']."'
						");

					$attributes=$this->DB->query("
						SELECT an.nameid,an.entitle AS name,
							av.valueid,av.entitle AS value
						FROM  attributename AS an
						LEFT JOIN attributevalue AS av ON (av.nameid=an.nameid)
						WHERE an.killed=0 AND av.killed=0 AND an.module='product' AND an.mid='".$item['productId']."'
						ORDER BY an.nameid ASC
					");

					$nameId=$title=$value=$attributeCount=array();
					if($this->DB->numRows()){
						while($attribute=$this->DB->fetchArray($attributes)){
							if(in_array($attribute['nameid'],$nameId)){
								$title[$attribute['nameid']]=$attribute['name'];
								$value[$attribute['nameid']][]=$attribute['value'];
								$valueId[$attribute['nameid']][]=$attribute['valueid'];
								$attributeCount[$attribute['nameid']]++;
							}else{
								$nameId[]=$attribute['nameid'];
								$title[$attribute['nameid']]=$attribute['name'];
								$value[$attribute['nameid']][]=$attribute['value'];
								$valueId[$attribute['nameid']][]=$attribute['valueid'];
								$attributeCount[$attribute['nameid']]=1;
							}
						}
					}
					foreach ($nameId as $key=>$val){
						$selectStr .= ''.$title[$val].'：';
						for($n=0;$n<count($value[$val]);$n++){
							if($valueId[$val][$n]==$valueStr[$key]){
								$selectStr .= '<i class="small">'.$value[$val][$n].'</i>';
							}
						}
						$selectStr .= '　';
					}
				}else{
					$product=$this->DB->queryFirst("
						SELECT `product`.title,`product`.entitle,`product`.materialid
						FROM  `product`
						WHERE productid='".$item['productid']."'
					");
					$item['productId'] = $item['productid'];
					$selectStr = '';
				}
				$itemtr .= '<tr class="odd">
				<td width="110">'.$order['orderno'].'</td>
				<td width="120">'.$product['entitle'].'</td>
				<td>'.$selectStr.'</td>
				<td align="right">'.$this->iif($item['discount']>0, $item['discount'].'%', '').'</td>
				<td align="right">'.number_format($item['quantity']).' '.$item['unit'].'</td>
				<td align="right">'.number_format($item['price'], 2).'</td>
				<td align="right">'.number_format($item['amount'], 2).'</td>
				</tr>';
				$k++;
				$quantity+=$item['quantity'];
				$total+=$item['amount'];
			}
			$rtn['total'] = $total;
			$total=number_format($total, 2);
			$rtn['tr']= <<<EOF
		<table class="printTab">
		<tbody>
		<tr class="bold">
			<td colspan="12">Order Information</td>
		</tr><tr><td>Order No.</td><td>Products or Parts</td><td>Description of Products or Parts</td><td align="right">Discount</td><td align="right">Quantity</td><td align="right">Price</td><td align="right">Amount</td></tr>
		{$itemtr}
		<tr><td colspan="4" align="right">Subtotal：</td><td align="right">{$quantity}</td><td></td><td align="right" class="middle bold">{$total}</td></tr>
		</tbody></table>
EOF;
		}
		return $rtn;
	}

	// 包装清单
	function relatedPackinglist($o){
		if($o['type']){
			$type=' AND type="'.$o['type'].'"';
		}
		if(isset($o['orderid'])){
			$condition=' AND p.mid='.$o['orderid'];
		}
		$packs = $this->DB->query("
			SELECT p.packinglistid, p.packinglistno,p.type,p.module,p.mid,p.customerid,p.cartonCount,p.netWeight,p.grossWeight,p.volume,
				customer.title AS customerTitle, customer.address AS customerAddress, customer.linkman AS customerLinkman, customer.position AS customerPosition, customer.telephone AS customerTelephone, customer.fax AS customerFax,
				m.realname AS modifier,mem.realname AS creator
			FROM `packinglist` AS p
			LEFT JOIN customer ON (customer.customerid=`p`.customerid)
			LEFT JOIN `member` AS m ON (m.userid=p.modifier)
			LEFT JOIN `member` AS mem ON (mem.userid=p.creator)
			WHERE p.killed=0 {$type} AND module='{$o['module']}' {$condition}
		");
		if($this->DB->numRows()){
			while($pl = $this->DB->fetchArray($packs)){
				$itemtr='';
				$items=$this->DB->query("
					SELECT pli.cartonno,pli.length,pli.width,pli.height,pli.volume,pli.perCarton,pli.perNetWeight,pli.perGrossWeight,pli.netWeight,pli.grossWeight,pli.startno,pli.endno,pli.remark,
						mv.versionid,mv.materialid,mv.itemid
					FROM packinglistitem AS pli
					LEFT JOIN materialversion AS mv ON (pli.versionid=mv.versionid)
					WHERE pli.killed=0 AND pli.packinglistid={$pl['packinglistid']}
					ORDER BY cartonno ASC
				");
				if($this->DB->numRows()){
					$k=1;
					$cartonno=array();
					while($item=$this->DB->fetchArray($items)){
						$materialInfo=$this->getMaterial(array('materialid'=>$item['materialid'],'itemid'=>$item['itemid']));
						$itemtr.='<tr class="'.$this->iif($this->rotate(),'odd','even').'">
							<td class="middle gray">'.str_pad($item['cartonno'],2,0,'STR_PAD_LEFT').'</td>
							<td><a href="'.$materialInfo['url'].'">'.$materialInfo['no'].'</a>　'.$materialInfo['material'].'　'.$materialInfo['standard'].'</td>
							<td>'.$item['perCarton'].'</td>							
							<td>'.$item['length'].' * '.$item['width'].' * '.$item['height'].'</td>
							<td>'.$item['perNetWeight'].'</td>
							<td>'.$item['perGrossWeight'].'</td>
							<td>'.$item['netWeight'].'</td>
							<td>'.$item['grossWeight'].'</td>
							<td>'.$item['volume'].'</td>
							<td>'.$item['remark'].'</td>
							<td>'.$item['startno'].' 到 '.$item['endno'].'</td>
						</tr>';
						$totalCount+=$item['perCarton'];
						$perNetWeight+=$item['perNetWeight'];
						$perGrossWeight+=$item['perGrossWeight'];
						$k++;
					}
					$itemtr .= '<tr class="'.$this->iif($this->rotate(), 'odd', 'even').' middle"><td>合计：</td><td>总箱数：'.$pl['cartonCount'].'</td><td>'.$totalCount.'</td><td></td><td align="right">'.$perNetWeight.'</td><td align="right">'.$perGrossWeight.'</td><td align="right">'.$pl['netWeight'].'</td><td align="right">'.$pl['grossWeight'].'</td><td align="right">'.$pl['volume'].'</td><td colspan=2></td></tr>';
				}
				if($pl['module']=='order'){
					$module=$this->DB->queryFirst("
						SELECT orderid,orderno AS no
						FROM `order`
						WHERE orderid={$pl['mid']}
					");
				}elseif($pl['module']=='sample'){
					$module=$this->DB->queryFirst("
						SELECT sampleid,sampleno AS no
						FROM `sample`
						WHERE sampleid={$pl['mid']}
					");
				}
				$status = '';
				$rtn['panel'] .= <<<EOF
<div class="bold darkred big" style="clear:both;"><span class="right normal"><a href="/s.php?module=invoice&action=updatePacking&packinglistid={$pl['packinglistid']}">修改包装清单</a>　<a href="/s.php?module=invoice&action=removePacking&packinglistid={$pl['packinglistid']}&rt=view">删除包装清单</a>　<a href="/p.php?module=printer&action=packinglist&packinglistid={$pl['packinglistid']}" target="_blank">打印PL</a></span></div>
<table class="hundred">
<thead>
<tr>
	<th colspan="6">基本信息 (Basic Information)</th>
</tr>
</thead>
<tbody>
<tr>
	<td width="100">装箱清单号：</td><td width="350">{$pl['packinglistno']}</td>
	<td width="100">相关PO订单：</td><td width="350"><a href="/s.php?module={$pl['module']}&action=view&orderid={$module['orderid']}">{$module['no']}</td>
	<td width="100">客　　户：</td><td><a href="/s.php?module=customer&action=view&customerid={$pl['customerid']}">{$pl['customerTitle']}</a></td>
</tr>
</tbody>
</table>
<table class="hundred">
<thead>
<tr><th colspan="19">包装明细</th></tr>
</thead>
<tbody class="small">
<tr class="center">
	<td width="50">箱号</td>
	<td>产品</td>
	<td>每箱数量/PCS</td>
	<td>外箱规格/CM</td>
	<td>每个净重/KG</td>
	<td>每个毛重/KG</td>
	<td>每箱净重/KG</td>
	<td>每箱毛重/KG</td>
	<td>每箱体积/CBM</td>
	<td>备注</td>
	<td>序列号范围</td>
</tr>
{$itemtr}
</tbody>
</table>
EOF;
			}
		}else{
			$rtn['off']=' disabled';
		}
		return $rtn;
	}
	// 采购订单
	function relatedPOrder($r){
		$condition="`porder`.killed=0";
		if($r['materialid']>0){
			$items=$this->DB->query("SELECT DISTINCT mid FROM `item` WHERE module='porder' AND materialid='".$r['materialid']."'");
			if($this->DB->numRows()){
				while($item=$this->DB->fetchArray($items)){
					$id[]=$item['mid'];
				}
				$condition.=" AND `porder`.porderid IN (".implode(',', $id).")";
			}else{
				$condition.=" AND `porder`.porderid=0";
			}
		}
		if($r['pinvoiceid']>0){
			$items=$this->DB->query("SELECT DISTINCT porderid FROM `item` WHERE module='pinvoice' AND mid='".$r['pinvoiceid']."'");
			if($this->DB->numRows()){
				while($item=$this->DB->fetchArray($items)){
					$id[]=$item['porderid'];
				}
				$condition.=" AND `porder`.porderid IN (".implode(',', $id).")";
			}else{
				$condition.=" AND `porder`.porderid=0";
			}
		}
		if($r['customerid']>0){
			$condition.=" AND `porder`.customerid='".$r['customerid']."'";
		}
		if($r['supplierid']>0){
			$condition.=" AND `porder`.supplierid='".$r['supplierid']."'";
		}
		$orders=$this->DB->query("
			SELECT `porder`.porderid,`porder`.orderno,`porder`.shipmentDate,`porder`.supplierid,`porder`.ifVerify,
					`porder`.paymentDate,`porder`.modified,`porder`.created,`porder`.killed,`porder`.purchaseType,
				`currency`.title AS currency,
				`supplier`.title AS supplier,
				m.username AS modifier, c.username AS creator
			FROM `porder`
			LEFT JOIN `pinvoicetype` AS pi ON (pi.pinvoiceTypeid=`porder`.pinvoiceTypeid)
			LEFT JOIN `supplier` ON (`supplier`.supplierid=`porder`.supplierid)
			LEFT JOIN `currency` ON (`currency`.currencyid=`porder`.currencyid)
			LEFT JOIN `user` AS m ON (m.userid=`porder`.modifier)
			LEFT JOIN `user` AS c ON (c.userid=`porder`.creator)
			WHERE ".$condition." AND `porder`.ifVerify=1
			ORDER BY `porder`.porderid ASC, `porder`.modified DESC, `porder`.created DESC
		");
		$rtn['num']=$this->DB->numRows();
		if($rtn['num']){
			$index=1;
			while($order=$this->DB->fetchArray($orders)){
				$created=date('Y-m-d', $order['created']);
				$itemtr='';
				$items=$this->DB->query("
					SELECT i.itemid,i.mid AS porderid,i.materialid,i.title,i.quantity,i.package,i.price,i.amount,i.discount,i.dateline,i.remark,
						m.materialno,m.title AS material,m.standard,
						u.title AS unit,
						br.title AS brand,
						p.purchaseid,p.purchaseno
					FROM `item` AS i
					LEFT JOIN `purchase` AS p ON (p.purchaseid=i.purchaseid)
					LEFT JOIN `material` AS m ON (m.materialid=i.materialid)
					LEFT JOIN `unit` AS u ON (u.unitid=i.unitid)
					LEFT JOIN `brand` AS br ON (br.brandid=i.brandid)
					WHERE i.killed=0 AND i.module='porder' AND i.mid='".$order['porderid']."'
					ORDER BY itemid ASC
				");
				if($this->DB->numRows()){
					$i=1;
					$amount=$payment=0;
					$itemtr.='<tr><td width="15">ID</td><td width="60">采购计划单号</td><td width="200">物资编号</td><td>品牌</td><td>包装方式</td><td>应到时间</td><td width="60">总数</td><td>折扣</td><td>单价</td><td width="60">已到</td><td>金额</td><td>已付</td><td></td></tr>';
					while($item=$this->DB->fetchArray($items)){
						$tracking=$this->DB->queryFirst("SELECT SUM(quantity) AS count,SUM(amount) AS payment FROM ordertracking WHERE killed=0 AND module='porder' AND itemid='".$item['itemid']."' LIMIT 0,1");
						$itemtr.='<tr class="'.$this->rotateLine(). ' small">
							<td>'.$i.'</td>
							<td><a href="/s.php?module=purchase&action=view&purchaseid='.$item['purchaseid'].'">'.$item['purchaseno'].'</a></td>
							<td title=""><a href="/s.php?module=material&action=view&materialid='.$item['materialid'].'">'.$item['materialno'].'</a><br>
							<span class="small">名称：'.$this->iif(strlen($item['material'])>12,mb_substr($item['material'],0,12, 'UTF-8').'...',$item['material']).'</span><br><span class="small">规格：'.$this->iif(strlen($item['standard'])>12,mb_substr($item['standard'],0,12, 'UTF-8').'...',$item['standard']).'</span></td>
							<td>'.$item['brand'].'</td>
							<td>'.$item['package'].'</td>
							<td>'.date('Y-m-d',$item['dateline']).'</td>
							<td align="right">'.number_format($item['quantity']).' '.$item['unit'].'</td>
							<td align="right">'.$this->iif($item['discount']>0, $item['discount'].'%', '').'</td>
							<td>'.number_format($item['price'],2).'</td>
							<td>'.$this->iif($tracking['count']=='',0,$tracking['count']).$item['unit'].'</td>
							<td>'.$item['amount'].'</td>
							<td>'.$this->iif($tracking['payment'],$tracking['payment'],0).'</td>
							<td><a href="/s.php?module=porderTracking&action=view&itemid='.$item['itemid'].'">查看记录</a><br><a href="/s.php?module=porderTracking&action=addQuantity&itemid='.$item['itemid'].'">新增到货记录</a><br><a href="/s.php?module=accounting&action=addPayment&porderid='.$item['porderid'].'">已付</a></td>
						</tr>';
						$quantity+=$item['quantity'];
						$amount+=$item['amount'];
						$payment+=$tracking['payment'];
						$i++;
					}
				}
				$title='';
				if($order['orderno']!='')$title.='　<span class="small">订单：</span><a href="/s.php?module=porder&action=view&porderid='.$order['porderid'].'">'.$order['orderno'].'</a>('.$this->verify($order['ifVerify']).')';
				if($order['supplier']!='')$title.='　<span class="small">供应商：</span><a href="/s.php?module=psupplier&action=view&supplierid='.$order['supplierid'].'">'.$order['supplier'].'</a>';
				if($amount!='')$title.='　<span class="small">总金额：</span>'.$amount;
				if($payment!='')$title.='　<span class="small">已付：</span>'.$payment;
				if($order['currency']!='')$title.='　<span class="small">币种：</span>'.$order['currency'];
				if($order['created']!='')$title.='　<span class="small">创建时间：</span>'.$created;
				$options=$this->iif($order['killed']==0,$this->iif($order['ifVerify']==0,'<a href="/s.php?module=porder&action=verify&porderid='.$order['porderid'].'">审核</a> ','<a href="/s.php?module=pinvoice&action=add&porderid='.$order['porderid'].'&supplierid='.$order['supplierid'].'&step=2">新增发票</a> ').'<a href="/s.php?module=porder&action=update&porderid='.$order['porderid'].'">修改</a> <a href="/s.php?module=porder&action=kill&porderid='.$order['porderid'].'&rt=list" onclick="return confirm(\'你确定要删除这个应付 '.$order['orderno'].' 吗\');">删除</a>','<a href="/s.php?module=order&action=restorePayment&porderid='.$order['porderid'].'">恢复</a>');
				$rtn['panel'].=<<<EOF
<table class="hundred">
<thead>
<tr><th colspan="15"><span class="right">{$options} <span class="plus"></span></span>	<span class="normal">[<b>{$index}</b>]</span>{$title}</th></tr>
</thead>
<tbody id="order{$order['porderid']}" style="display:none">
{$itemtr}
</tbody>
</table>
EOF;
				$index++;
			}
			$rtn['count']=' ('.$rtn['num'].')';
		}else{
			$rtn['off']=' disabled';
		}
		return $rtn;
	}
	// 采购订单明细
	function relatedPOrderItem($o){
		$order=$this->DB->queryFirst("SELECT porderid,ifVerify FROM `porder` WHERE killed=0 AND porderid='".$o['porderid']."'");
		$items=$this->DB->query("
			SELECT i.itemid,i.mid AS porderid,i.materialid,i.versionid,i.title,i.quantity,i.package,i.price,i.amount,i.discount,i.requirement,i.dateline,i.remark,
				m.materialno,m.title AS material,m.standard,
				u.title AS unit,
				br.title AS brand,
				p.purchaseid,p.purchaseno
			FROM `item` AS i
			LEFT JOIN `purchase` AS p ON (p.purchaseid=i.purchaseid)
			LEFT JOIN `material` AS m ON (m.materialid=i.materialid)
			LEFT JOIN `unit` AS u ON (u.unitid=i.unitid)
			LEFT JOIN `brand` AS br ON (br.brandid=i.brandid)
			WHERE i.killed=0 AND i.module='porder' AND i.mid='".$o['porderid']."'
			ORDER BY itemid ASC
		");
		if($this->DB->numRows()){
			$i=$k=1;
			if($o['hasTable']=='no'){
				$r['tr'].='<table class="hundred"><thead></tr><th colspan="13">订单明细 </th></tr></thead><tbody>';
			}
			if($o['purchaseType']=='normal'){
				$r['tr'].='<table class="hundred small"><thead></tr><th colspan="13">订单明细</th></tr></thead>
				<tbody><tr class="center"><td width="15">ID</td><td width="70">计划单号</td><td>物资编号</td><td>物资名称</td><td>规格</td><td>品牌</td><td>包装方式</td><td align="right">数量/单位</td><td>折扣</td><td>单价</td><td>金额</td><td>到货时间</td><td>备注</td></tr>';
			}elseif($o['purchaseType']=='entrust'){
				$r['tr'].='<table class="hundred small"><thead></tr><th colspan="13">订单明细</th></tr></thead><tbody><tr class="center"><td width="15">ID</td><td width="70">采购单编号</td><td>物资编号</td><td>物资名称</td><td>规格</td><td>品牌</td><td>包装方式</td><td align="right">数量/单位</td><td>折扣</td><td>单价</td><td>金额</td><td>加工要求</td><td>备注</td></tr>';
				$r['entrust'].='<table class="hundred small"><thead></tr><th colspan="12">提供物资明细</th></tr></thead><tbody><tr class="center"><td width="30">ID</td><td>订单物资</td><td>提供的物资</td><td align="right">数量/单位</td><td>单价</td></tr>';
			}
			while($item=$this->DB->fetchArray($items)){
				$total+=$item['amount'];
				$r['tr'].='<tr class="'.$this->rotateLine().'">
					<td>'.$i.'</td>
					<td><a href="/s.php?module=purchase&action=view&purchaseid='.$item['purchaseid'].'">'.$item['purchaseno'].'</a></td>
					<td><a href="/s.php?module=material&action=view&materialid='.$item['materialid'].'">'.$item['materialno'].'</a></td>
					<td>'.$item['material'].'</td>
					<td>'.$item['standard'].'</td>
					<td>'.$item['brand'].'</td>
					<td>'.$item['package'].'</td>
					<td align="right">'.$item['quantity'].' '.$item['unit'].'</td>
					<td align="right">'.$this->iif($item['discount']>0, $item['discount'].'%', '').'</td>
					<td>'.number_format($item['price'],4).'</td>
					<td>'.number_format($item['amount'],2).'</td>
					<td>'.$this->iif($o['purchaseType']=='entrust',$item['requirement'],date('Y-m-d',$item['dateline'])).'</td>
					<td>'.$item['remark'].'</td></tr>';
				$i++;
				$r['array'][]=$item;
				if($o['purchaseType']=='entrust'){
					$children=$this->bomChildList(array('parentid'=>$item['materialid'],'loopNum'=>1,'versionid'=>$item['versionid']));
					if($children['bom']){
						foreach($children['bom'] AS $child){
							$r['entrust'].='<tr class="'.$this->rotateLine().'">
								<td width="30">'.$k.'</td>
								<td><a href="/s.php?module=material&action=view&materialid='.$item['materialid'].'">'.$item['materialno'].'</a>　'.$item['material'].'　'.$item['standard'].'</td>
								<td><a href="'.$child['url'].'">'.$child['no'].'</a>　'.$child['title'].'　'.$child['standard'].'</td>
								<td align="right">'.$child['quantity'].' '.$child['unit'].'</td>
								<td>'.$child['price'].'</td></tr>';
							$k++;
						}
					}
				}
			}
			$total=number_format($total,2);
			$r['total'] = $total;
			$cntotal=$this->numberToCNAccount($total);
			$r['tr'].='<tr class="even"><td colspan="3" align="center">合　　计</td><td colspan="7">人　民　币：　 <span class="bold middle">'.$cntotal.'</span></td><td colspan="3">小写： <span class="bold middle">'.$total.'</span></td></tr>
			<tr class="odd"><td class="small gray" colspan="13">注意事项：可以点击“修改订单”，继续添加所需要购买的物资</td></tr></tbody></table>'.$r['entrust'];
		}

		return $r;
	}
	// 销售订单
	function relatedOrder($r){
		$condition = "`order`.killed=0";
		if(!$r['panel'])$r['panel']=1;
		if($r['type']=='PO'){
			$condition.=" AND `order`.parentid>0 AND `order`.`type`='PO'";
		}elseif($r['type']=='PI'){
			$condition.=" AND `order`.parentid=0 AND `order`.`type`='PI'";
		}
		if($r['productid']>0){
			$items = $this->DB->query("SELECT DISTINCT `orderid` FROM `item` WHERE productid='".$r['productid']."'");
			if($this->DB->numRows()){
				while($item = $this->DB->fetchArray($items)){
					$id[] = $item['orderid'];
				}
				$condition.=" AND `order`.orderid IN (".implode(',', $id).")";
			}else{
				$condition.=" AND `order`.orderid=0";
			}
		}
		if($r['customerid']>0){
			$condition.=" AND `order`.customerid='".$r['customerid']."'";
		}
		if($r['supplierid']>0){
			$condition.=" AND `order`.supplierid='".$r['supplierid']."'";
		}
		if($r['requirementid']>0){
			$items = $this->DB->query("
				SELECT DISTINCT ri.mid
				FROM `requirementitem` AS ri
				WHERE ri.killed=0 AND ri.module='order' AND ri.requirementid='".$r['requirementid']."'");
			if($this->DB->numRows()){
				while($item = $this->DB->fetchArray($items)){
					$id[] = $item['mid'];
				}
				$condition.=" AND `order`.orderid IN (".implode(',', $id).")";
			}else{
				$condition.=" AND `order`.orderid=0";
			}
		}
		$orders = $this->DB->query("
			SELECT `order`.orderid, `order`.orderno, `order`.shipmentDate, `order`.cover, `order`.attachs, `order`.images, `order`.modified, `order`.created, `order`.killed,`order`.ifverify,`order`.ifApprove,`order`.approver,`order`.approved,
				route.title AS routeTitle, route.caption AS routeCaption, `route`.typelist,
				customer.title AS customerTitle,
				m.username AS modifier, c.username AS creator,t.realname AS director,b.realname AS approver
			FROM `order`
			LEFT JOIN orderroute AS route ON (route.routeid=`order`.routeid)
			LEFT JOIN `customer` ON (customer.customerid=`order`.customerid)
			LEFT JOIN `user` AS m ON (m.userid=`order`.modifier)
			LEFT JOIN `user` AS c ON (c.userid=`order`.creator)
			LEFT JOIN `member` AS t ON (t.userid=`order`.director)
			LEFT JOIN `member`	AS b ON (b.userid=`order`.approver)
			WHERE ".$condition."
			ORDER BY `order`.orderid ASC, `order`.modified DESC, `order`.created DESC
		");
		$rtn['num']=$this->DB->numRows();
		 
		if($rtn['num'] AND $r['panel']==1){
			$rtn['panel'].='<table class="hundred"><thead><th>订单ID</th><th>订单编号</th><th>订单状态</th><th>客户</th><th>业务人员</th><th>类型</th><th>交货日期</th><th>创建/修改日期</th><th>创建人</th></thead>';
			//$rtn['panel'] = '<ul id="mmlist" class="mmlist clear">';
			$i=1;
			while($order = $this->DB->fetchArray($orders)){
				$created = date('y-m-d H:m', $order['created']);
				$modified = date('Y-m-d H:m', $order['modified']);
				$status='';
				if($order['ifverify']==0){
					$status = '<span class="darkred">未审核　未审批</span>　（<a href="/s.php?module=order&action=verify&orderid='.$order['orderid'].'">审核</a>）';
				}elseif($order['ifverify']==-1){
					$status = '<span class="red">审核未通过</span>　<span class="darkred">未审批</span>';
				}elseif($order['ifverify']==1 AND $order['ifApprove']==0){
					$status = '<span class="green">审核通过</span>　<span class="darkred">未审批</span>　（<a href="/s.php?module=order&action=approve&orderid='.$order['orderid'].'">审批</a>）';
				}elseif($order['ifverify']==1 AND $order['ifApprove']==-1){
					$status = '<span class="green">审核通过</span>　<span class="red">审批未通过</span>';
				}elseif($order['ifverify']==1 AND $order['ifApprove']==1){
					$status = '<span class="green">审核通过　审批通过</span>';
				}
				 $rtn['panel'].='<tbody><tr class="odd">
				 <td>'.$i.'</td>
				<td> <a href="/s.php?module=order&action=view&orderid='.$order['orderid'].'" target="_blank">'.$order['orderno'].'</a></td>
				<td>'.$status.'</td>
				<td><a href="/s.php?module=customer&action=view&customerid='.$customer['customerid'].'" target="_blank">'.$order['customerTitle'].'</a></td>
				<td>'.$order['director'].'</td>
				<td>'.$order['routeTitle'].'</td>
				<td>'.date('Y-m-d', $order['shipmentDate']).'</td>
				<td>'.$created.'<br/>'.$this->iif($order['modifier']!='', $order['modifier'].' 改于 '.$modified, '').'</td>
				<td>'.$order['creator'].'</td>
				 ';
				//$rtn['panel'] .= '<li title="由 '.$order['creator'].' 建于 '.$created.$this->iif($order['modifier']!='', '，'.$order['modifier'].' 改于 '.$modified, '').'"'.$this->iif($i%3==0, ' class="end"', '').'>
				//<div class="mmlistt">
				//	<span class="right normal"></span>
					//<span class="small">'.$order['attr'].'</span> <a href="/s.php?module=order&action=view&orderid='.$order['orderid'].'" target="_blank">'.$order['orderno'].'</a><span class="small">('.$order['typelist'].')</span><br>'.$this->iif($order['attachs']>0, ' <span class="attachFile" title="有'.$order['attachs'].'个附件。"></span> ', '').$this->iif($order['images']>0, ' <span class="attachImage" title="有'.$order['images'].'个图片。"></span> ', '').'<span class="small gray">交货日期：</span>'.date('Y-m-d', $order['shipmentDate']).'</div>
				//<div class="mmlistb">
				//	<div title="'.$order['routeCaption'].'"><span class="small gray">类型：</span>'.$order['routeTitle'].'</div>
				//	<div title="'.$order['customerTitle'].'"><span class="small gray">客户：</span><a href="/s.php?module=customer&action=view&customerid='.$customer['customerid'].'" target="_blank">'.$order['customerTitle'].'</a></div>
				//	'.$this->iif($order['cover']!='', '<div class="center"><img src="'.$order['cover'].'"></div>', '').'
				//	<div class="small clear"><span class="right" title="由 '.$order['creator'].' 建于 '.date('y-m-d H:m', $order['created']).'">'.$this->iif($order['modifier']!='', '由 '.$order['modifier'].' 改于 ', '建于 ').date('Y-m-d H:m', $order['modified']).'</span></div>
				//</div>
				//</li>';
				$i++;
			}
			//$rtn['panel'] .= '</ul>';
			$rtn['panel'].='</tr>
				 </tbody></table>';
			$rtn['count'] = ' ('.$rtn['num'].')';
		}
		if(!$rtn['num']){
			$rtn['off']=' disabled';
		}
		return $rtn;
	}
	// 订单明细
	function relatedOrderItem($o){
		$items = $this->DB->query("
			SELECT item.*,
				p.title,
				m.materialid,m.standard,
				brand.title AS brand,
				packing.title AS packing,
				unit.title AS unit
			FROM `item`
			LEFT JOIN `product` AS p ON (p.productid=`item`.productid)
			LEFT JOIN `material` AS m ON (m.materialid=`item`.materialid)
			LEFT JOIN packing ON (packing.packingid=item.packingid)
			LEFT JOIN brand ON (brand.brandid=item.brandid)
			LEFT JOIN unit ON (unit.unitid=item.unitid)
			WHERE `item`.killed=0 AND `item`.module='order' AND `item`.mid='".$o['orderid']."'
			ORDER BY ordering ASC,itemid ASC
		");
		if($this->DB->numRows()){
			$k=1;
			while($item = $this->DB->fetchArray($items)){
				$productInfo=$this->getProduct(array('productid'=>$item['productid'],'itemid'=>$item['productitemid']));
				$itemtr .= '<tr class="'.$this->iif($this->rotate(), 'odd', 'even').'">
				<td>'.$k.'</td>
				<td><a href="/s.php?module=product&action=view&productid='.$productInfo['productid'].'" target="_blank">'.$productInfo['title'].'</a></td>
				<td>'.$productInfo['standard'].'</td>
				<td>'.$item['caption'].'</td>
				<td>'.$this->itemType[$item['typeid']]['title'].'</td>
				<td>'.$item['brand'].'</td>
				<td>'.$item['packing'].'</td>
				<td align="right">'.$this->iif($item['discount']>=0, $item['discount'].'%', '').'</td>
				<td align="right">'.number_format($item['quantity']).' '.$item['unit'].'</td>
				<td align="right">'.number_format($item['price'], 2).'</td>
				<td align="right">'.number_format($item['amount'], 2).'</td></tr>';
				$k++;
				$quantity+=$item['quantity'];
				$total+=$item['amount'];
				$rtn['array'][]=$item;
			}
			$rtn['total'] = $total;
			$total=number_format($total, 2);
			$rtn['tr'] = <<<EOF
		<tr><td>ID</td><td>产品或配件</td><td>产品或配件的属性</td><td>说明</td><td>购买方式</td><td>品牌</td><td>包装</td><td align="right">折扣</td><td align="right">数量</td><td align="right">单价</td><td align="right">小计</td></tr>
		{$itemtr}
		<tr><td colspan="8" align="right">合计：</td><td align="right">{$quantity}</td><td></td><td align="right">{$total}</td></tr>
EOF;
			return $rtn;
		}
	}

	//打印订单明细
	function relatedPrintPIOrderItem($o){
		if($o['language']=='c1'){
		
		$items = $this->DB->query("
			SELECT item.*,
				productitem.attributevalue,
				productitem.mid AS productId,
				productitem.productitemid AS pitemid,
				brand.title AS brand,
				packing.title AS packing,
				unit.title AS unit
			FROM `item`
			LEFT JOIN `productitem` ON (productitem.productitemid=item.productitemid)
			LEFT JOIN packing ON (packing.packingid=item.packingid)
			LEFT JOIN brand ON (brand.brandid=item.brandid)
			LEFT JOIN unit ON (unit.unitid=item.unitid)
			WHERE `item`.killed=0 AND `item`.module='order' AND `item`.mid='".$o['orderid']."'
			ORDER BY ordering ASC
		");
		
			
		//title items
		$thPorP = "产品或配件";
		$thDescript = "产品或配件属性";
		$thspNote = "特别说明";
		$thBrand = "品牌";
		$thPackage = "包装";
		$thDiscount = "折扣";
		$thQuantity = "数量";
		$thPrice = "价格";
		$thAmount = "金额";
		$thTotal = "总计：";
		$sqlpart = "title";

	}
	if($o['language']=='c2'){
		
		$items = $this->DB->query("
			SELECT item.*,
				productitem.attributevalue,
				productitem.mid AS productId,
				productitem.productitemid AS pitemid,
				brand.title AS brand,
				packing.entitle AS packing,
				unit.entitle AS unit
			FROM `item`
			LEFT JOIN `productitem` ON (productitem.productitemid=item.productitemid)
			LEFT JOIN packing ON (packing.packingid=item.packingid)
			LEFT JOIN brand ON (brand.brandid=item.brandid)
			LEFT JOIN unit ON (unit.unitid=item.unitid)
			WHERE `item`.killed=0 AND `item`.module='order' AND `item`.mid='".$o['orderid']."'
			ORDER BY ordering ASC
		");
		
			
		//title items
		$thPorP = "Products or Parts";
		$thDescript = "Description of Products or Parts";
		$thspNote = "Special Note";
		$thBrand = "Brand";
		$thPackage = "Packaging";
		$thDiscount = "Discount";
		$thQuantity = "Quantity";
		$thPrice = "Price";
		$thAmount = "Amount";
		$thTotal = "Total：";
		$sqlpart = "entitle";

	}
		if($this->DB->numRows()){
			$k=1;
			while($item = $this->DB->fetchArray($items)){
				$selectStr = '';
				if(!empty($item['attributevalue'])){
				
				//echo $item['attributevalue']."->";///////////////////////////
				
					$valueStr = explode(",",$item['attributevalue']);
					//echo "|->".$valueStr[0]."|->";///////////////////////////
					$product=$this->DB->queryFirst("
						SELECT `product`.{$sqlpart}
						FROM  `product`
						WHERE productid='".$item['pitemid']."'
					");

					$attributes=$this->DB->query("
						SELECT an.nameid,an.{$sqlpart} AS name,
							av.valueid,av.{$sqlpart} AS value
						FROM  attributename AS an
						LEFT JOIN attributevalue AS av ON (av.nameid=an.nameid)
						WHERE an.killed=0 AND av.killed=0 AND an.module='product' AND an.mid='".$item['productid']."'
						ORDER BY an.nameid ASC
					");

					$nameId=$title=$value=$attributeCount=array();
					if($this->DB->numRows()){
						while($attribute=$this->DB->fetchArray($attributes)){
							if(in_array($attribute['nameid'],$nameId)){
								$title[$attribute['nameid']]=$attribute['name'];
								$value[$attribute['nameid']][]=$attribute['value'];
								$valueId[$attribute['nameid']][]=$attribute['valueid'];
								$attributeCount[$attribute['nameid']]++;
							}else{
								$nameId[]=$attribute['nameid'];
								$title[$attribute['nameid']]=$attribute['name'];
								$value[$attribute['nameid']][]=$attribute['value'];
								$valueId[$attribute['nameid']][]=$attribute['valueid'];
								$attributeCount[$attribute['nameid']]=1;
							}
						}
					}
					foreach ($nameId as $key=>$val){
						$selectStr .= ''.$title[$val].'：';
						for($n=0;$n<count($value[$val]);$n++){
							if($valueId[$val][$n]==$valueStr[$key]){
								$selectStr .= '<i>'.$value[$val][$n].'</i>';
							}
						}
						$selectStr .= '　';
					}
				}else{
					$product=$this->DB->queryFirst("
						SELECT `product`.{$sqlpart},`product`.materialid
						FROM  `product`
						WHERE productid='".$item['productid']."'
					");
					$item['productId'] = $item['productid'];
					$material = $this->DB->queryFirst("
						SELECT `material`.materialid,`material`.standard
						FROM  `material`
						WHERE materialid='".$product['materialid']."'
					");

					$selectStr = '';
				}

				$itemtr .= '<tr>
				<td style="padding-left:10px">'.$product[''.$sqlpart.''].'</td>
				<td style="padding-left:10px">'.$selectStr.'</td>				
				<td style="padding-left:10px">'.$item['brand'].'</td>
				<td style="padding-left:10px">'.$item['packing'].'</td>
				<td style="padding-left:10px;text-align:right">'.$this->iif($item['discount']>0, $item['discount'].'%', '').'</td>
				<td style="padding-left:10px;text-align:right">'.number_format($item['quantity']).' '.$item['unit'].'</td>
				<td style="padding-left:10px;text-align:right">'.number_format($item['price'], 2).'</td>
				<td style="padding-left:10px;text-align:right">'.number_format($item['amount'], 2).'</td></tr>';
				$k++;
				$quantity+=$item['quantity'];
				$total+=$item['amount'];
			}
			$total=number_format($total, 2);
			return <<<EOF
		<tr class="even" align="center"><td width="150">{$thPorP}</td><td>{$thDescript}</td><td width="120">{$thBrand}</td><td width="100">{$thPackage}</td><td align="right" width="90">{$thDiscount}</td><td align="right" width="90">{$thQuantity}</td><td align="right" width="90">{$thPrice}</td><td align="right" width="90">{$thAmount}</td></tr>
		{$itemtr}
		<tr><td colspan="5" align="right" class="bold">{$thTotal}</td><td align="right" class="bold">{$quantity}</td><td></td><td align="right" class="bold">{$total}</td></tr>
EOF;
		}
	}

	/**
 	 * 获得销售PO或者样品订单列表以及明细（目前，只有包装组才会显示该列表）
 	 * @author creator zhourui 2014-1-16 9:59:00
 	 * @access public
 	 * @param array $arr (workcenter：工作中心)
 	 * @return string $arr 
 	 */
	function relatedOrderList($d){
		if(!$d['panel'])$d['panel']=1;
		if($d['start']=='')$d['start']=0;
		if($d['perpage']=='')$d['perpage']=10;
		if($d['workcenterid']!=5){
			$rtn['off']=' disabled';
		}
		$orders=$this->DB->query("
			SELECT `order`.orderid, `order`.orderno, `order`.shipmentDate, `order`.customerid,`order`.ifVerify,`order`.ifApprove,`order`.ifChooser,`order`.ifInbound,`order`.ifOutbound
			FROM `order`
			WHERE `order`.killed=0  AND `order`.type='".$d['type']."'
			ORDER BY `order`.orderno DESC
		");
		$count=$this->DB->numRows();
		if($count AND $d['panel']==1){
			$index=$d['start']+1;
			while($order=$this->DB->fetchArray($orders)){
				$itemtr='';
				$items = $this->DB->query("
					SELECT ri.itemid,ri.materialid,ri.materialitemid,ri.versionid,ri.quantity,ri.dateline,
						r.requirementid,r.requirementno,
						mv.title AS version,mv.qualified,mv.disqualified
					FROM `requirementitem` AS ri 
					LEFT JOIN `requirement` AS r ON (r.requirementid=ri.requirementid)
					LEFT JOIN `materialversion` AS mv ON (mv.versionid=ri.versionid)
					WHERE ri.killed=0 AND r.killed=0 AND r.locked<>0 AND ri.module='order' AND ri.mid='".$order['orderid']."'
					ORDER BY ri.itemid ASC
				");
				if($this->DB->numRows()){
					$k=1;
					$itemtr='<tr><td width="15">ID</td><td width="80">需求计划</td><td>物资</td><td width="180">版本</td><td width="100">需求数量</td><td width="70">需求时间</td><td width="370">序列号</td><td width="150">操作</td></tr>';
					while($item=$this->DB->fetchArray($items)){
						$materialInfo=$this->getMaterial(array('materialid'=>$item['materialid'],'itemid'=>$item['materialitemid']));
						if($materialInfo['attrid']==8){
							$serviceno=$this->DB->queryFirst("SELECT COUNT(*) AS count FROM serviceno WHERE orderid='".$order['orderid']."' AND versionid='".$item['versionid']."' AND requirementitemid='".$item['itemid']."' LIMIT 0,1");
							$startno=$this->DB->queryFirst("SELECT serviceno FROM serviceno WHERE orderid='".$order['orderid']."' AND versionid='".$item['versionid']."' AND requirementitemid='".$item['itemid']."' ORDER BY serviceno ASC LIMIT 0,1");
							$endno=$this->DB->queryFirst("SELECT serviceno FROM serviceno WHERE orderid='".$order['orderid']."' AND versionid='".$item['versionid']."' AND requirementitemid='".$item['itemid']."' ORDER BY serviceno DESC LIMIT 0,1");
							$currentno=$this->DB->queryFirst("SELECT serviceno FROM serviceno ORDER BY serviceno DESC limit 0,1");
							$status=$info='';
							if($serviceno['count']==$item['quantity']){
								$status='快速服务代码生成完成';
							}elseif($serviceno['count']<$item['quantity'] AND $serviceno['count']!=0){
								$status='<a href="javascript:dc.barcode.addServiceNo(\'oitem'.$index.'_'.$k.'\')">继续生成快速服务代码</a>';
							}else{
								$status='<a href="javascript:dc.barcode.addServiceNo(\'oitem'.$index.'_'.$k.'\')">生成快速服务代码</a>';
							}
							$info='共<b>'.$this->iif($serviceno['count'],$serviceno['count'],0).'</b>条';
							$notd='<input type="hidden" name="count" value="'.$serviceno['count'].'" >起止号：<input type="text" name="startno" value="'.$this->iif($startno['serviceno'],$startno['serviceno'],$currentno['serviceno']+1).'" size="9" /> — <span class="endno">'.$this->iif($endno['serviceno'],$endno['serviceno'],0).'</span>　<span class="info">'.$info.'</span>　数量：<input type="text" name="requirementCount" size="4" value="'.($item['quantity']-$serviceno['count']).'" />';
							$statustd='<span class="status">'.$status.'</span>';
						}else{
							$notd='无快速服务编号';
							$statustd='';
						}

						$itemtr .= '<tr class="'.$this->iif($this->rotate(), 'odd', 'even').' small" id="oitem'.$index.'_'.$k.'">
						<td><input type="hidden" name="orderid" value="'.$order['orderid'].'">
							<input type="hidden" name="itemid" value="'.$item['itemid'].'">'.$k.'</td>
						<td><a href="/s.php?module=requirement&action=view&requirementid='.$item['requirementid'].'" target="_blank">'.$item['requirementno'].'</a></td>
						<td><a href="'.$materialInfo['url'].'" target="_blank">'.$materialInfo['no'].'</a>　'.$materialInfo['material'].'　'.$materialInfo['standard'].'</td>
						<td><input type="hidden" name="versionid" value="'.$item['versionid'].'">'.$item['version'].'　良品数：'.$item['qualified'].'　不良品数：'.$item['disqualified'].'</td>
						<td><input type="hidden" name="quantity" value="'.$item['quantity'].'">'.$item['quantity'].'</td>
						<td>'.date('Y-m-d',$item['dateline']).'</td>
						<td>'.$notd.'</td>
						<td>'.$statustd.'</td>
						</tr>';
						$k++;
					}
					$title=$op=$status='';
					if($order['orderno'])$title.=' 订单号：<a href="/s.php?module=order&action=view&orderid='.$order['orderid'].'">'.$order['orderno'].'</a>';
					if($order['shipmentDate'])$title.=' 交货日期：'.date('Y-m-d',$order['shipmentDate']);
					// 状态
					if($order['ifVerify']==0){
						$status = '<span class="darkred">未审核　未审批</span>';
					}elseif($order['ifVerify']==-1){
						$status = '<span class="red">审核未通过</span>　<span class="darkred">未审批</span>';
					}elseif($order['ifVerify']==1 AND $order['ifApprove']==0){
						$status = '<span class="green">审核通过</span>　<span class="darkred">未审批</span>';
					}elseif($order['ifVerify']==1 AND $order['ifApprove']==-1){
						$status = '<span class="green">审核通过</span>　<span class="red">审批未通过</span>';
					}elseif($order['ifVerify']==1 AND $order['ifApprove']==1){
						$status = '<span class="green">审核通过　审批通过</span>';
					}
					$status.=$this->iif($order['ifChooser']==1,'　<span class="green">已投产</span>','　<span class="darkred">未投产</span>');
					$inbound=$this->DB->queryFirst("
						SELECT inboundid
						FROM inbound
						WHERE killed=0 AND ifComplete=2 AND orderid='".$order['orderid']."'
					");
					// 入库状态
					if($order['ifInbound']==0){
						$status.='　<span class="green">未入库</span>';
						if($order['ifChooser']==1){
							$op.='　<a href="/s.php?module=inbound&action=add&typeid=7&orderid='.$order['orderid'].'">新增入库</a>'; // 未入库时显示操作，进行入库
						}
					}elseif($order['ifInbound']==1){
						$status.='　<span class="darkred">入库中</span>';
					}elseif($order['ifInbound']==2){
						$status.='　<span class="darkred">已入库</span>';
					}
					// 出库状态
					$outbound=$this->DB->queryFirst("
						SELECT outboundid
						FROM outbound
						WHERE killed=0 AND ifComplete=2 AND orderid='".$order['orderid']."'
					");
					if($order['ifOutbound']==0){
						$status.='　<span class="green">未出库</span>';
					}elseif($order['ifOutbound']==1){
						$status.='　<span class="darkred">出库中</span>';
					}elseif($order['ifOutbound']==2){
						$status.='　<span class="darkred">已出库</span>';
					}
					// 包装清单状态
					$packinglist=$this->DB->queryFirst("SELECT packinglistid FROM packinglist WHERE type='produce' AND module='order' AND mid={$order['orderid']}");
					if($packinglist){
						$op.='<a href="/s.php?module=packinglist&action=view&packinglistid='.$packinglist['packinglistid'].'">查看包装单</a>';
					}else{
						$op.='<a href="/s.php?module=packinglist&action=add&m=order&mid='.$order['orderid'].'">新增包装单</a>';
					}

					$title.=' 状态：'.$status;
					$options='展开 ';
					$rtn['panel'].=<<<EOF
<table class="hundred">
<thead>
<tr><th colspan="15"><span class="right">{$op}　{$options} <span class="minus hand"></span></span>	<span class="normal">[<b>{$index}</b>]</span>{$title}</th></tr>
</thead>
<tbody id="order{$order['orderid']}" style="display:table-row-group">
{$itemtr}
</tbody>
</table>
EOF;
					$index++;
				}
			}
		}
		// 数量
		if($index>1){
			$rtn['counter']=$index-1;
			$rtn['count']=' ('.$rtn['counter'].')';
		}else{
			$rtn['off']=' disabled';
		}
		return $rtn;
	}
	/**
 	 * 获得销售PO或者样品订单列表以及明细（目前，只有包装组才会显示该列表）
 	 * @author creator zhourui 2014-1-16 9:59:00
 	 * @access public
 	 * @param array $arr (workcenter：工作中心)
 	 * @return string $arr 
 	 */
	function relatedSampleList($d){
		if(!$d['panel'])$d['panel']=1;
		if($d['start']=='')$d['start']=0;
		if($d['perpage']=='')$d['perpage']=10;
		if($d['workcenterid']!=5){
			$rtn['off']=' disabled';
		}
		$samples=$this->DB->query("
			SELECT sampleid,sampleno,deliveryDate,customerid,ifVerify,ifApprove,ifChooser,ifInbound,ifOutbound
			FROM `sample`
			WHERE killed=0 
			ORDER BY sampleno DESC
		");
		$count=$this->DB->numRows();
		if($count AND $d['panel']==1){
			$index=$d['start']+1;
			while($sample=$this->DB->fetchArray($samples)){
				$itemtr='';
				$items = $this->DB->query("
					SELECT ri.itemid,ri.materialid,ri.materialitemid,ri.versionid,ri.quantity,ri.dateline,
						r.requirementid,r.requirementno,
						mv.title AS version,mv.qualified,mv.disqualified
					FROM `requirementitem` AS ri 
					LEFT JOIN `requirement` AS r ON (r.requirementid=ri.requirementid)
					LEFT JOIN `materialversion` AS mv ON (mv.versionid=ri.versionid)
					WHERE ri.killed=0 AND r.killed=0 AND r.locked<>0 AND ri.module='sample' AND ri.mid='".$sample['sampleid']."'
					ORDER BY ri.itemid ASC
				");
				if($this->DB->numRows()){
					$k=1;
					$itemtr='<tr><td width="15">ID</td><td width="80">需求计划</td><td>物资</td><td width="180">版本</td><td width="70">需求数量</td><td width="70">需求时间</td><td width="370">序列号</td><td width="150">操作</td></tr>';
					while($item=$this->DB->fetchArray($items)){
						$materialInfo=$this->getMaterial(array('materialid'=>$item['materialid'],'itemid'=>$item['materialitemid']));
						if($materialInfo['attrid']==8){
							$serviceno=$this->DB->queryFirst("SELECT COUNT(*) AS count FROM serviceno WHERE sampleid='".$sample['sampleid']."' AND versionid='".$item['versionid']."' AND requirementitemid='".$item['itemid']."' LIMIT 0,1");
							$startno=$this->DB->queryFirst("SELECT serviceno FROM serviceno WHERE sampleid='".$sample['sampleid']."' AND versionid='".$item['versionid']."' AND requirementitemid='".$item['itemid']."' ORDER BY serviceno ASC LIMIT 0,1");
							$endno=$this->DB->queryFirst("SELECT serviceno FROM serviceno WHERE sampleid='".$sample['sampleid']."' AND versionid='".$item['versionid']."' AND requirementitemid='".$item['itemid']."' ORDER BY serviceno DESC LIMIT 0,1");
							$currencyno=$this->DB->queryFirst("SELECT serviceno FROM serviceno ORDER BY serviceno DESC limit 0,1");
							$status=$info='';
							if($serviceno['count']==$item['quantity']){
								$status='快速服务代码生成完成';
							}elseif($serviceno['count']<$item['quantity'] AND $serviceno['count']!=0){
								$status='<a href="javascript:dc.barcode.addServiceNo(\'sitem'.$index.'_'.$k.'\')">继续生成快速服务代码</a>';
							}else{
								$status='<a href="javascript:dc.barcode.addServiceNo(\'sitem'.$index.'_'.$k.'\')">生成快速服务代码</a>';
							}
							$info='共<b>'.$this->iif($serviceno['count'],$serviceno['count'],0).'</b>条';
							$notd='<input type="hidden" name="count" value="'.$serviceno['count'].'" >起始号：<input type="text" name="startno" value="'.$this->iif($startno['serviceno'],$startno['serviceno'],$currencyno['serviceno']+1).'" size="9" /> — 结束号：<span class="endno">'.$this->iif($endno['serviceno'],$endno['serviceno'],0).'</span>　<span class="info">'.$info.'</span>　数量：<input type="text" name="requirementCount" size="4" value="'.($item['quantity']-$serviceno['count']).'" />';
							$statustd='<span class="status">'.$status.'</span>';
						}else{
							$notd='无快速服务编号';
							$statustd='';
						}
						
						$itemtr .= '<tr class="'.$this->iif($this->rotate(), 'odd', 'even').' small" id="sitem'.$index.'_'.$k.'">
						<td><input type="hidden" name="sampleid" value="'.$sample['sampleid'].'">
							<input type="hidden" name="itemid" value="'.$item['itemid'].'">'.$k.'</td>
						<td><a href="/s.php?module=requirement&action=view&requirementid='.$item['requirementid'].'" target="_blank">'.$item['requirementno'].'</a></td>
						<td><a href="'.$materialInfo['url'].'" target="_blank">'.$materialInfo['no'].'</a>　'.$materialInfo['material'].'　'.$materialInfo['standard'].'</td>
						<td><input type="hidden" name="versionid" value="'.$item['versionid'].'">'.$item['version'].'　良品数：'.$item['qualified'].'　不良品数：'.$item['disqualified'].'</td>
						<td><input type="hidden" name="quantity" value="'.$item['quantity'].'">'.$item['quantity'].'</td>
						<td>'.date('Y-m-d',$item['dateline']).'</td>
						<td>'.$notd.'</td>
						<td>'.$statustd.'</td>
						</tr>';
						$k++;
					}
					$title=$op=$status='';
					if($sample['sampleno'])$title.=' 订单号：<a href="/s.php?module=sample&action=view&sampleid='.$sample['sampleid'].'">'.$sample['sampleno'].'</a>';
					if($sample['deliveryDate'])$title.=' 交货日期：'.date('Y-m-d',$sample['deliveryDate']);
					// 状态
					if($sample['ifVerify']==0){
						$status = '<span class="darkred">未审核　未审批</span>';
					}elseif($samples['ifVerify']==-1){
						$status = '<span class="red">审核未通过</span>　<span class="darkred">未审批</span>';
					}elseif($sample['ifVerify']==1 AND $sample['ifApprove']==0){
						$status = '<span class="green">审核通过</span>　<span class="darkred">未审批</span>';
					}elseif($sample['ifVerify']==1 AND $sample['ifApprove']==-1){
						$status = '<span class="green">审核通过</span>　<span class="red">审批未通过</span>';
					}elseif($sample['ifVerify']==1 AND $sample['ifApprove']==1){
						$status = '<span class="green">审核通过　审批通过</span>';
					}
					$status.=$this->iif($sample['ifChooser']==1,'　<span class="green">已投产</span>','　<span class="darkred">未投产</span>');
					// 入库状态
					$inbound=$this->DB->queryFirst("
						SELECT inboundid
						FROM inbound
						WHERE killed=0 AND ifComplete=2 AND sampleid='".$sample['sampleid']."'
					");
					if($sample['ifInbound']==0){
						$status.='　<span class="green">未入库</span>';
						if($sample['ifChooser']==1){
							$op.='　<a href="/s.php?module=inbound&action=add&typeid=7&sampleid='.$sample['sampleid'].'">新增入库</a>'; // 未入库时显示操作，进行入库
						}
					}elseif($sample['ifInbound']==1){
						$status.=$this->iif($inbound['inboundid'],'　<span class="darkred">已入库</span>','　<span class="darkred">入库中</span>');
					}
					// 出库状态
					$outbound=$this->DB->queryFirst("
						SELECT outboundid
						FROM outbound
						WHERE killed=0 AND ifComplete=2 AND sampleid='".$sample['sampleid']."'
					");
					if($sample['ifOutbound']==0){
						$status.='　<span class="green">未出库</span>';
					}elseif($sample['ifOutbound']==1){
						$status.=$this->iif($outbound['$outboundid'],'　<span class="darkred">出库中</span>','　<span class="darkred">已出库</span>');
					}
					// 包装清单状态
					$packinglist=$this->DB->queryFirst("SELECT packinglistid FROM packinglist WHERE type='produce' AND module='sample' AND mid={$sample['sampleid']}");
					if($packinglist){
						$op.='<a href="/s.php?module=packinglist&action=view&packinglistid='.$packinglist['packinglistid'].'">查看包装单</a>';
					}else{
						$op.='<a href="/s.php?module=packinglist&action=add&m=sample&mid='.$sample['sampleid'].'">新增包装单</a>';
					}
					$title.=' 状态：'.$status;
					$options='展开 ';
					$rtn['panel'].=<<<EOF
<table class="hundred">
<thead>
<tr><th colspan="15"><span class="right">{$op}　{$options} <span class="minus hand"></span></span>	<span class="normal">[<b>{$index}</b>]</span>{$title}</th></tr>
</thead>
<tbody id="order{$sample['orderid']}" style="display:table-row-group">
{$itemtr}
</tbody>
</table>
EOF;
					$index++;
				}
			}
		}
		// 数量
		if($index>1){
			$rtn['counter']=$index-1;
			$rtn['count']=' ('.$rtn['counter'].')';
		}else{
			$rtn['off']=' disabled';
		}
		return $rtn;
	}
	//打印订单明细
	function relatedPrintPOOrderItem($o){
		if($o['language']=='c1'){
			$items = $this->DB->query("
				SELECT item.*,
					productitem.attributevalue,
					productitem.productid AS productId,
					brand.title AS brand,
					packing.title AS packing,
					unit.title AS unit
				FROM `item`
				LEFT JOIN `productitem` ON (productitem.productitemid=item.productitemid)
				LEFT JOIN packing ON (packing.packingid=item.packingid)
				LEFT JOIN brand ON (brand.brandid=item.brandid)
				LEFT JOIN unit ON (unit.unitid=item.unitid)
				WHERE `item`.killed=0 AND `item`.module='order' AND `item`.mid='".$o['orderid']."'
				ORDER BY itemid ASC, modified ASC
			");
			//表格项目名称
			$thIDNo = "序号";
			$thProductNo = "产品编号";
			$thProductName = "产品名称";
			$thSpecification = "规格(电缆线/插头)";
			$thQuantity = "数量";
			$thUnit = "单位";
			$thMethod = "方式";
			$thRemark ="备注";	
			$sqlpart = "title";		
			$mstandard = "standard";

		
		}
		if($o['language']=='c2'){
			$items = $this->DB->query("
				SELECT item.*,
					productitem.attributevalue,
					productitem.productid AS productId,
					brand.title AS brand,
					packing.title AS packing,
					unit.entitle AS unit
				FROM `item`
				LEFT JOIN `productitem` ON (productitem.productitemid=item.productitemid)
				LEFT JOIN packing ON (packing.packingid=item.packingid)
				LEFT JOIN brand ON (brand.brandid=item.brandid)
				LEFT JOIN unit ON (unit.unitid=item.unitid)
				WHERE `item`.killed=0 AND `item`.module='order' AND `item`.mid='".$o['orderid']."'
				ORDER BY itemid ASC, modified ASC
			");
		

			//table th name
			$thIDNo = "ID";
			$thProductNo = "Product NO.";
			$thProductName = "Product Name";
			$thSpecification = "Specifications (cable length, plug)";
			$thQuantity = "Quantity";
			$thUnit = "Unit";
			$thMethod = "Method";
			$thRemark ="Remarks";
			$sqlpart = "entitle";
			$mstandard = "enstandard";
		}
		//echo $sqlpart.">>>>>>>>";
		if($this->DB->numRows()){
			$k=1;
			while($item = $this->DB->fetchArray($items)){
				$selectStr = '';
				if(!empty($item['attributevalue'])){
					$valueStr = explode(",",$item['attributevalue']);

					$product=$this->DB->queryFirst("
						SELECT `product`.{$sqlpart},`material`.materialno
						FROM  `product`
						LEFT JOIN `material` ON (`material`.`materialid` = `product`.materialid)
						WHERE productid='".$item['productId']."'
					");

					$attributes=$this->DB->query("
						SELECT an.nameid,an.{$sqlpart} AS name,
							av.valueid,av.{$sqlpart} AS value
						FROM  attributename AS an
						LEFT JOIN attributevalue AS av ON (av.nameid=an.nameid)
						WHERE an.killed=0 AND av.killed=0 AND an.module='product' AND an.mid='".$item['productId']."'
						ORDER BY an.nameid ASC
					");

					$nameId=$title=$value=$attributeCount=array();
					if($this->DB->numRows()){
						while($attribute=$this->DB->fetchArray($attributes)){
							if(in_array($attribute['nameid'],$nameId)){
								$title[$attribute['nameid']]=$attribute['name'];
								$value[$attribute['nameid']][]=$attribute['value'];
								$valueId[$attribute['nameid']][]=$attribute['valueid'];
								$attributeCount[$attribute['nameid']]++;
							}else{
								$nameId[]=$attribute['nameid'];
								$title[$attribute['nameid']]=$attribute['name'];
								$value[$attribute['nameid']][]=$attribute['value'];
								$valueId[$attribute['nameid']][]=$attribute['valueid'];
								$attributeCount[$attribute['nameid']]=1;
							}
						}
					}
					foreach ($nameId as $key=>$val){
						$selectStr .= ''.$title[$val].'：';
						for($n=0;$n<count($value[$val]);$n++){
							if($valueId[$val][$n]==$valueStr[$key]){
								$selectStr .= '<i class="small">'.$value[$val][$n].'</i>';
							}
						}
						$selectStr .= '　';
					}
				}else{
					$product=$this->DB->queryFirst("
						SELECT `product`.{$sqlpart},`product`.materialid
						FROM  `product`
						WHERE productid='".$item['productid']."'
					");
					$item['productId'] = $item['productid'];
					$material = $this->DB->queryFirst("
						SELECT `material`.materialid,`material`.materialno,`material`.{$mstandard}
						FROM  `material`
						WHERE materialid='".$product['materialid']."'
					");
					$product['materialno']=$material['materialno'];
					$selectStr = $material[$mstandard];

				}

				$itemtr .= '<tr class="odd">
				<td align="center">'.$k.'</td>
				<td align="center">'.$product['materialno'].'</td>
				<td align="center">'.$product[$sqlpart].'</td>
				<td>'.$selectStr.'</td>
				<td align="center">'.number_format($item['quantity']).'</td>
				<td align="center">'.$item['unit'].'</td>
				<td>'.$item['caption'].'</td>
				</tr>';
				$k++;
				$quantity+=$item['quantity'];
				$total+=$item['amount'];
			}
			$total=number_format($total, 2);
			return <<<EOF
		<tr class="bold"><td width="45">{$thIDNo}</td><td width="125" align="center">{$thProductNo}</td><td align="center" width="150">{$thProductName}</td><td>{$thSpecification}</td><td align="center" width="65">{$thQuantity}</td><td align="center" width="65">{$thUnit}</td><td align="center" width="220">{$thRemark}</td></tr>
		{$itemtr}

EOF;
		}
	}
    //打印报表费用明细
	function relatedFees($r){
          if($r['orderid']>0){
			$condition.=" AND `item`.module='order' AND mid='".$r['orderid']."'";
		}
		if($r['porderid']>0){
			$condition.=" AND `item`.module='porder' AND mid='".$r['porderid']."'";
		}
		if($r['invoiceid']>0){
			$condition.=" AND `item`.module='invoice' AND mid='".$r['invoiceid']."'";
		}
		if($r['sampleid']>0){
			$condition.=" AND `item`.module='sample' AND mid='".$r['sampleid']."'";
		}
		if($r['psampleid']>0){
			$condition.=" AND `item`.module='psample' AND mid='".$r['psampleid']."'";
		}
		if($r['maverickpurchaseid']>0){
			$condition.=" AND `item`.module='maverickpurchase' AND mid='".$r['maverickpurchaseid']."'";
		}
        $items = $this->DB->query("
			SELECT item.*,
				fee.title AS fee,
				unit.title AS unit
			FROM `feeitem` AS `item`
			LEFT JOIN `fee` ON (fee.feeid=item.feeid)
			LEFT JOIN unit ON (unit.unitid=item.unitid)
			WHERE `item`.killed=0".$condition."
			ORDER BY `item`.itemid ASC, `item`.modified ASC
		");
		$fees=array();
		if($this->DB->numRows()){
			//$fees['panel']='<div><span class="left"><b>名称</b></span><span class="right"><b>费用</b></span></div>';
			while($item=$this->DB->fetchArray($items)){
				
				$fees['panel'].='<span class="left"><b>'.$item['fee'].'</b></span><span class="right"><b>'.number_format($item['price'], 2).'<b></span><br/><br/>';
				$total+=$item['amount'];
			}
			$fees['panel'].='<span class="right green"><b>额外费用总计：'.$fees['total']=number_format($total, 2).'</b></span>';
		}else{
			$fees['panel']='<b>无额外费用</b>';
		}
		return $fees;
	}
	//配件明细
	function relatedParts($o){
     $item=$this->DB->query("
			SELECT item.*,p.title,p.attrid
			FROM `item`
			LEFT JOIN `product` AS p ON (p.productid=`item`.productid)
			WHERE `item`.killed=0 AND `item`.module='".$o['module']."' AND `item`.mid='".$o['moduleid']."' AND p.attrid=3
	 ");
	 $rtn=array();
	if($this->DB->numRows()){
		while($items=$this->DB->fetchArray($item)){
			$quantity+=$items['quantity'];
			$total+=$items['amount'];
		}
		if($o['module']=='inquiry'){
		$itemstr='<tr>
		                      <td align="center" class="red">配件</td>
							<td align="center">配件合计</td>
							<td align="center" colspan="2">'.$quantity.'</td>
						</tr>';
		}else{
			$itemstr='<tr>
		                      <td align="center" class="red">配件</td>
							<td align="center">配件合计</td>
							<td align="center">'.$quantity.'</td>
							<td align="center"></td>
							<td align="center">'.$total.'</td>
						</tr>';
		}
						$rtn['total']= $total;
	$rtn['quantity']=$quantity;
	$rtn['panel']=$itemstr;
	return $rtn;
	}else{
		return false;
	}
	
	}
	//充电器明细
	function relatedCharger($o){
        $item=$this->DB->query("
				SELECT item.*,
				p.title AS ptitle,p.attrid
			FROM `item`
			LEFT JOIN `product` AS p ON (p.productid=`item`.productid)
			WHERE `item`.killed=0 AND `item`.module='".$o['module']."' AND `item`.mid='".$o['moduleid']."' AND  p.attrid=1 AND p.categoryid IN (1,3,5,6,28)
		");
		 $rtn=array();
	if($num=$this->DB->numRows()){
		$num=$num+1;
		$itemstr='<tr><td rowspan="'.$num.'" align="center" class="red" width="10%">充电设备</td></tr>';
		while($items=$this->DB->fetchArray($item)){
			if($o['module']=='inquiry'){
			$itemstr.='<tr>
		                      <td align="center" >'.$items['ptitle'].'</td>
							<td align="center" colspan="2">'.$items['quantity'].'</td>
						</tr>';
			}else{
				$itemstr.='<tr>
		                      <td align="center" >'.$items['ptitle'].'</td>
							<td align="center">'.$items['quantity'].'</td>
							<td align="center">'.$items['price'].'</td>
							<td align="center">'.$items['amount'].'</td>
						</tr>';
			}
			$quantity+=$items['quantity'];
			$total+=$items['amount'];
		}	
		$rtn['total']= $total;
	$rtn['quantity']=$quantity;
	$rtn['panel']=$itemstr;
	}else{
		return false;
	}
	return $rtn;
	}
	//
	function relatedIproducts($o){
      $items = $this->DB->query("
			SELECT item.*,
				p.title AS ptitle,p.attrid,p.categoryid
			FROM `item`
			LEFT JOIN `product` AS p ON (p.productid=`item`.productid)
			WHERE `item`.killed=0 AND `item`.module= '".$o['module']."' AND `item`.mid='".$o['moduleid']."' AND  p.attrid=1 AND p.categoryid IN (1,4,7,8,29)
			ORDER BY  itemid ASC
		");
		$rtn=array();
		if($num=$this->DB->numRows()){
			$k=1;
			$num=$num+1;
			$partInfo=$this->relatedParts(array('module'=>$o['module'],'moduleid'=>$o['moduleid'])) ;
			$chargerInfo=$this->relatedCharger(array('module'=>$o['module'],'moduleid'=>$o['moduleid']));
         $itemtr='<table frame="void" border="0"  width=100% height=100%><tr><td align="center" class="red" width="20%">类别</td><td class="red" align="center"><b>产品名</b></td><td class="red" align="center" colspan="2" width="20%"><b >数量</b></td></tr>';
			$itemtr.='<tr><td rowspan="'.$num.'" align="center" class="red" >灯</td></tr>';
			while($item = $this->DB->fetchArray($items)){
                $itemtr.='<tr>
				<td align="center">'.$item['ptitle'].'</td>
				<td align="center" colspan="2">'.$item['quantity'].'</td>
				</tr>';
				$k++;
				$quantity+=$item['quantity'];
			}
			$itemtr.=$chargerInfo['panel'];
			$itemtr.=$partInfo['panel'];
			$rtn['total']= $total+$partInfo['total']+$chargerInfo['total'];
			$rtn['quantity']=$quantity;
			$itemtr.='<tr><td align="right" class="red" colspan="2">产品数量合计</td><td align="center" colspan="2">'.$quantity.'</td></tr></table>';
			$rtn['panel']=$itemtr;
		}
    return $rtn;
	}
	//打印订单的产品明细
	function relatedProducts($o){
		$items = $this->DB->query("
			SELECT item.*,
				p.title AS ptitle,p.attrid,p.categoryid
			FROM `item`
			LEFT JOIN `product` AS p ON (p.productid=`item`.productid)
			WHERE `item`.killed=0 AND `item`.module= '".$o['module']."' AND `item`.mid='".$o['moduleid']."' AND  p.attrid=1 AND p.categoryid IN (1,4,7,8,29)
			ORDER BY ordering ASC,itemid ASC
		");
		$rtn=array();
		if($num=$this->DB->numRows()){
			$k=1;
			$num=$num+1;
			$partInfo=$this->relatedParts(array('module'=>$o['module'],'moduleid'=>$o['moduleid'])) ;
			$chargerInfo=$this->relatedCharger(array('module'=>$o['module'],'moduleid'=>$o['moduleid']));
			$itemtr='<tr><td align="center" class="red">类别</td><td class="red" align="center"><b>产品名</b></td><td class="red" align="center"><b>数量</b></td><td class="red" align="center"><b>单价</b></td><td class="red" align="center"><b>总价</b></td></tr>';
			$itemtr.='<tr><td rowspan="'.$num.'" align="center" class="red" width="10%">灯</td></tr>';
			while($item = $this->DB->fetchArray($items)){
				//$productInfo=$this->getProduct(array('productid'=>$item['productid'],'itemid'=>$item['productitemid']));
				$itemtr.='<tr>
				<td align="center">'.$item['ptitle'].'</td>
				<td align="center">'.$item['quantity'].'</td>
				<td align="center">'.$item['price'].'</td>
				<td align="center">'.$item['amount'].'</td>
				</tr>';
				//$itemtr.='<span 　ｗidth="200px"><b>'.$productInfo['title'].'</b></span>　　<span ｗidth="100px"><b>'.number_format($item['quantity']).'</b></span>　　<span ｗidth="100px" ><b>'.number_format($item['price'], 2).'</b></span>　　<span ｗidth="100px"><b>'.number_format($item['amount'], 2).'</b></span><br>';
				//<td align="right">'.number_format($item['quantity']).' '.$item['unit'].'</td>
			//	<td align="right">'.number_format($item['price'], 2).'</td>
			//	<td align="right">'.number_format($item['amount'], 2).'</td></tr>
				$k++;
				$quantity+=$item['quantity'];
				$total+=$item['amount'];	
			}
			$itemtr.=$chargerInfo['panel'];
			$itemtr.=$partInfo['panel'];
			$rtn['total']= $total+$partInfo['total']+$chargerInfo['total'];
			$rtn['quantity']=$quantity;
			$itemtr.='<tr><td align="center" class="red" colspan="2">产品数量合计</td><td align="center" >'.$quantity.'</td><td align="center" class="red">总价合计</td><td align="center">'.$rtn['total'].'</td></tr>';
			$rtn['panel']=$itemtr;
		}else{
			$partInfo=$this->relatedParts(array('orderid'=>$o['orderid'])) ;
			$chargerInfo=$this->relatedCharger(array('orderid'=>$o['orderid']));
			if(isset($partInfo) & isset($chargerInfo)){
			$itemtr='<tr><td align="center" class="red">类别</td><td class="red" align="center"><b>产品名</b></td><td class="red" align="center"><b>数量</b></td><td class="red" align="center"><b>单价</b></td><td class="red" align="center"><b>总价</b></td></tr>';
             $itemtr.=$partInfo['panel'];
			$itemtr.=$chargerInfo['panel'];
			$rtn['total']= $total+$partInfo['total']+$chargerInfo['total'];
			$rtn['quantity']=$quantity;
			$itemtr.='<tr><td></td><td align="center" class="red">灯数量合计</td><td align="center" >'.$quantity.'</td><td align="center" class="red">总价合计</td><td align="center">'.($total+$partInfo['total']+$chargerInfo['total']).'</td></tr>';
			$rtn['panel']=$itemtr;
		}else{
			$rtn['panel']='无产品明细';
		}
		}
		return $rtn;
	}
	//询价单对应的产品明细
   function relatedPrinterinquiryitem($r){
	   if($r['id']=='') return;
	   if($r['module']=='') return;
	  // 
	  //
	  //	
	   $itemstr=$this->DB->query("
				SELECT `item`.quantity,`item`.productid,p.title AS ptitle,p.attrid,p.categoryid
				FROM `item`
				LEFT JOIN `product`   AS  p ON (p.productid=`item`.productid)
				WHERE `item`.killed=0  AND `item`.module='".$r['module']."' AND `item`.mid='".$r['id']."' AND p.attrid=1 AND p.categoryid IN (1,4,7,8,29) 
	   ");
	   if($this->DB->numRows()){
		   $productid=array();
		   while($itemstrs=$this->DB->fetchArray($itemstr)){
			   $quantity+=$itemstrs['quantity'];
			   $productid[]=$itemstrs['productid']; 
		   }
		   $productid=array_unique($productid);
			 $productitem=$this->DB->query("
						SELECT p.productid
						FROM `product`  AS p
						WHERE p.productid IN(".implode(',',$productid).") 
			");
			if($num=$this->DB->numRows()){
				while($productitems=$this->DB->fetchArray($productitem)){
					$products=$this->getproducts(array('productid'=>$productitems['productid'],'module'=>$r['module']));
					$theproduct.=$products['panel'];
				}
			}
	   }
        $rtn['product']=$theproduct;
	   $rtn['quantity']=$quantity;
	   return $rtn;
   }
   //取得询价单报价单订单产品明细
   function getproducts($r){
	   if($r['productid']==''){
			return;
	   }
	   $rtn=array();
		 $productitem=$this->DB->query("
						SELECT p.productid,p.title,item.module,item.module,item.quantity
						FROM `product`  AS p
						LEFT JOIN `item` ON(`item`.productid=p.productid)
						LEFT JOIN `inquiry`  ON(`inquiry`.inquiryid=`item`.mid)
						LEFT JOIN `customer` ON (`customer`.customerid=`inquiry`.customerid)
						WHERE p.productid =".$r['productid']." AND `item`.module='".$r['module']."' 
			");
			if($num=$this->DB->numRows()){
				while($productitems=$this->DB->fetchArray($productitem)){
                 $quantitys+=$productitems['quantity'];
				 $product=$productitems['title'];
				}
				
			}
			$rtn['panel']="产品：". $product."数量：".$quantitys."";
			return $rtn;
   }
	//
	function relatedPrinterquotingitem($r){
		if($r['id']==''){
			return;
		}
       if($r['module']==''){
		   return;
	   } 
        $itemstr=$this->DB->query("
					SELECT `item`.quantity,`item`.price,item.amount,`item`.productid,
									p.title AS ptitle,p.attrid,p.categoryid
					FROM `item`
					LEFT JOIN `product`   AS p ON (p.productid=`item`.productid)
					WHERE `item`.killed=0  AND `item`.module='".$r['module']."' AND `item`.mid='".$r['id']."' AND p.attrid=1 AND p.categoryid IN (1,4,7,8,29)
		");
		if($this->DB->numRows()){
			$partInfo=$this->relatedParts(array('module'=>$r['module'],'moduleid'=>$r['id']));
			$chargerInfo=$this->relatedCharger(array('module'=>$r['module'],'moduleid'=>$r['id']));
			while($itemstrs=$this->DB->fetchArray($itemstr)){
				$quantity+=$itemstrs['quantity'];
				$total+=$itemstrs['amount'];
			}
			$rtn['quantity']=$quantity;
			$rtn['total']=$total+$partInfo['total']+$chargerInfo['total'];
		}else{
			$partInf=$this->relatedParts(array('module'=>'quoting','moduleid'=>$r['id']));
			$chargerInfo=$this->relatedCharger(array('modle'=>'quoting','quotingid'=>$r['id']));
			$rtn['total']=$partInfo['total']+$chargerInfo['total'];
		}
		return $rtn;
	}
	//
 function relatedPrinterorderitem($r){
	 if($r['orderid']==''){
		 return ;
	 }
	 $rtn=array();
	 $itemstr=$this->DB->query("
					SELECT item.quantity,item.price,item.amount ,`item`.productid,
					p.title AS ptitle,p.attrid,p.categoryid
					FROM `item`
					LEFT JOIN `product` AS p ON (p.productid=`item`.productid)
					WHERE `item`.killed=0 AND `item`.module='order' AND `item`.mid='".$r['orderid']."' AND  p.attrid=1 AND p.categoryid IN (1,4,7,8,29) ");
		if($this->DB->numRows()){
			$partInfo=$this->relatedParts(array('module'=>'order','orderid'=>$r['orderid'])) ;
			 $chargerInfo=$this->relatedCharger(array('module'=>'order','orderid'=>$r['orderid']));
			  while($itemstrs=$this->DB->fetchArray($itemstr)){
							$quantity+=$itemstrs['quantity'];
				             $total+=$itemstrs['amount'];
				}
							$rtn['quantity']=$quantity;
							$rtn['total']=$total+$partInfo['total']+$chargerInfo['total'];
			}else{
				$partInfo=$this->relatedParts(array('module'=>'order','orderid'=>$r['orderid'])) ;
			    $chargerInfo=$this->relatedCharger(array('module'=>'order','orderid'=>$r['orderid']));
				 $rtn['total']=$partInfo['total']+$chargerInfo['total'];
					}
		return $rtn;
 }
 //
 function getRegion($r){
	 if($r['countryid']==''){
      return ;
	 }
	 if($r['regionid']==''){
		 return;
	 }
	 $findregion=array();
	$region=$this->DB->query("
	            SELECT r.regionid AS rregionid,s.regionid AS sregionid
				FROM `region`  AS r
				LEFT  JOIN `region`   AS s ON(s.countryid=r.regionid)
				WHERE r.regionid='".$r['countryid']."'
				");
				$num=$this->DB->numRows();
	if($num>0){
		while($regions=$this->DB->fetchArray($region)){
         $findregion[]=$regions['sregionid'];
		}
	}
        $findregion[]=$r['countryid'];
    $findregions=array_uintersect($findregion,$r['regionid'],"strcasecmp") ;
return $findregions;
 }
//
function relatedInquiryregion($r){
 if($r['regionid']==''){
		 return;
	 }
	 if($r['condition']==''){
		 $r['condition']=='';
	 }
	 if($r['killed']==''){
		 $r['killed']=='';
	 }
	 $rtn=array();
	 $inquiry=$this->DB->query("
			SELECT `inquiry`.inquiryid,`inquiry`.customerid,`customer`.regionid,`region`.countryid,`region`.regionid
				FROM `inquiry`
				LEFT JOIN `customer` ON(`customer`.customerid=`inquiry`.customerid)
				LEFT  JOIN `region`		ON(`region`.regionid=`customer`.regionid)
				WHERE  ".$r['condition']." AND  `region`.regionid IN(".implode(',',$r['regionid']).")   ".$r['killed']."  
	 ");
		if($inquirynum=$this->DB->numRows()){
		 while($inquirys=$this->DB->fetchArray($inquiry)){
			
			 $item=$this->relatedPrinterinquiryitem(array('module'=>'inquiry','id'=>$inquirys['inquiryid']));
			 $quantitys+=$item['quantity'];
			 $totals+=$item['total'];
		     $items=$item['product'];
		 }
		 
	 }
	 $rtn['quotingnum']=$inquirynum;
	 $rtn['quantitys']=$quantitys;
	 $rtn['item']= $items;
	 // $rtn['totals']=$totals;
	 // $rtn['region']=$r['regionid'];
		return $rtn;
}

 //国家对应的报价单总数
 function relatedquotingRegion($r){
	 if($r['regionid']==''){
		 return;
	 }
	 if($r['condition']==''){
		 $r['condition']=='';
	 }
	 if($r['killed']==''){
		 $r['killed']=='';
	 }
	 $rtn=array();
	 $quoting=$this->DB->query("
			SELECT `quoting`.quotingid,`quoting`.portid,`quoting`.customerid,`customer`.regionid,`region`.countryid,`region`.regionid
				FROM `quoting`
				LEFT JOIN `customer` ON(`customer`.customerid=`quoting`.customerid)
				LEFT  JOIN `region`		ON(`region`.regionid=`customer`.regionid)
				WHERE  ".$r['condition']." AND  `region`.regionid IN(".implode(',',$r['regionid']).")    
	 ");
	 if($quotingnum=$this->DB->numRows()){
		 while($quotings=$this->DB->fetchArray($quoting)){
			 $item=$this->relatedPrinterquotingitem(array('module'=>'quoting','id'=>$quotings['quotingid']));
			 $quantitys+=$item['quantity'];
			 $totals+=$item['total'];
		 }
	 }
	 $rtn['quotingnum']=$quotingnum;
	 $rtn['quantitys']=$quantitys;
	  $rtn['totals']=$totals;
	  $rtn['region']=$r['regionid'];
		return $rtn;
 }

 //客户对应的询价单总数
 function relatedCustomerinquiry($r){
	  if($r['customerid']==''){
			 return;
		 }
		 if($r['condition']==''){
			 $r['condition']=='  1=1 ';
		 }
		 if($r['killed']==''){
			 $r['killed']=='';
		 }
		 $rtn=array();
		 $inquiry=$this->DB->query("
					SELECT `inquiry`.inquiryid,`inquiry`.customerid,`inquiry`.inquirytype
					FROM `inquiry`
					LEFT JOIN `customer` ON (`customer`.customerid=`inquiry`.customerid)
					LEFT JOIN `region`      ON(`region`.regionid=`customer`.regionid)
					WHERE  ".$r['condition']." AND`customer`.customerid='".$r['customerid']."' 
		 ");
		 $rtn=array();
		 if($inquirynum=$this->DB->numRows()){
			 while($inquirys=$this->DB->fetchArray($inquiry)){
              $item=$this->relatedPrinterinquiryitem(array('module'=>'inquiry','id'=>$inquirys['inquiryid']));
				$quantitys+=$item['quantity'];
			 }
		 }
		 $rtn['inquirynum']=$inquirynum;
		$rtn['quantitys']=$quantitys;
		return $rtn;
 }

 //客户对应的报价单总数
 function relatedCustomerquoting($r){
         if($r['customerid']==''){
			 return;
		 }
		 if($r['condition']==''){
			 $r['condition']==' AND 1=1 ';
		 }
		 if($r['killed']==''){
			 $r['killed']=='';
		 }
		 $rtn=array();
		 $quoting=$this->DB->query("
						SELECT `quoting`.quotingid,`quoting`.customerid,`quoting`.typeid
							FROM `quoting`
							LEFT JOIN `customer` ON (`customer`.customerid=`quoting`.customerid)
							LEFT JOIN `region`      ON(`region`.regionid=`customer`.regionid)
							WHERE ".$r['condition']." AND `customer`.customerid='".$r['customerid']."' 
		                    ");
        if($quotingnum=$this->DB->numRows()){
			while($quotings=$this->DB->fetchArray($quoting)){
				$item=$this->relatedPrinterquotingitem(array('module'=>'quoting','id'=>$quotings['quotingid']));
				$quantitys+=$item['quantity'];
				$totals+=$item['total'];
			}
		}
		$rtn['condition']=$r['condition'];
		$rtn['quotingnum']=$quotingnum;
		$rtn['quantitys']=$quantitys;
		$rtn['totals']=$totals;
		return $rtn;
 }
 //查询询价单下对应的客户
 function relatedInquirycustomer($r){
	 if($r['regionid']==''){
		 return;
	 }
	 if($r['customerid']==''){
		 return;
	 }
	if($r['condition']==''){
		$r['condition']='1=1';
	}
	 $rtn=array();

	 $rtn['regionid']=$r['regionid'];
	 $rtn['customerid']=$r['customerid'];
$customer=$this->DB->query("
				                    SELECT `customer`. title ,`customer`.customerid,`customer`.regionid,`region`.countryid ,`region`.regionid,`region`.countryid
									FROM `customer` 
									LEFT JOIN `region`     ON(`region`.regionid=`customer`.regionid)
									WHERE   `customer` .customerid IN (".implode(',',$r['customerid']).") AND `customer`.regionid IN(".implode(',',$r['regionid']).")");
  if($customernum=$this->DB->numRows()){
	  while($customers=$this->DB->fetchArray($customer)){
          $theinquiry=$this->relatedCustomerinquiry(array('customerid'=>$customers['customerid'],'condition'=>$r['condition']));
             $itemstr.='<tr><td  align="center"><b>'.$customers['title'].'</b></td><td  align="center">'.$theinquiry['inquirynum'].'</td><td align="center">'.$theinquiry['quantitys'].'</td></tr>';
						$i++;
						// $rtn['num']=$theinquiry['inquirynum'];
	  }
  }
 $rtn['num']=$customernum;
  $rtn['panel']=$itemstr;
	return $rtn;
 }
 //
 function relatedquotingCustomeritem($r){
      if($r['customerid']==''){
		  return;
	  }      
	  if($r['condition']!=''){
		 $condition=$r['condition'];
	  }
	              $rtn=array();
				$customer=$this->DB->query("
				                    SELECT `customer`. title ,`customer`.customerid,`customer`.regionid,`region`.countryid ,`region`.regionid,`region`.countryid
									FROM `customer`
									LEFT JOIN `region`     ON(`region`.regionid=`customer`.regionid)
									WHERE   `customer` .customerid IN (".implode(',',$r['customerid']).") AND `customer`.regionid IN(".implode(',',$r['regionid']).") ");
				if($customernum=$this->DB->numRows()){
					//$customerstr='<tr><td class="red" align="center">客户姓名</td><td class="red" align="center">订单数量</td></tr>';
					while($customers=$this->DB->fetchArray($customer)){	
						$theorders=$this->relatedCustomerquoting(array('customerid'=>$customers['customerid'] ,'condition'=>$condition,'killed'=>$killed));
						//$customerstr.='<b>'.$customerorders['title'].'</b>：<b><span class="green">'.$ordernum.'</span></b><br/>';
						$itemstr.='<tr><td  align="center"><b>'.$customers['title'].'</b></td><td align="center">'.$theorders['quotingnum'].'</td><td align="center">'.$theorders['quantitys'].'</td><td align="center">'.$theorders['totals'].'</td></tr>';
						$i++;
					}
				}
				$rtn['regionid']=$r['regionid'];
				$rtn['customerid']=$r['customerid'];
				$rtn['num']=$customernum;
				$rtn['panel']=$itemstr;
        return $rtn;
 }
 //订单对应的客户
 function relatedOrdercustomer($r){
    if($r['customerid']==''){
		 return;
	  }  
	  if($r['regionid']==''){
		 return;
	  }  
	  if($r['condition']!=''){
		 $condition=$r['condition'];
	  }
	 $rtn=array();
	 $customerorder=$this->DB->query("SELECT `customer`. title ,`customer`.customerid FROM `customer`
				WHERE `customer` .customerid IN (".implode(',',$r['customerid']).") AND `customer`.regionid IN(".implode(',',$r['regionid']).") ");
				if($num=$this->DB->numRows()){
					$i=1;
					while($customerorders=$this->DB->fetchArray($customerorder)){
						$theorders=$this->relatedCustomerorder(array('customerid'=>$customerorders['customerid'] ,'condition'=>$condition,'killed'=>$killed));
					//	print_r($theorders);
						//$customerstr.='<b>'.$customerorders['title'].'</b>：<b><span class="green">'.$ordernum.'</span></b><br/>';
						$itemstr.='</tr><td  align="center"><b>'.$customerorders['title'].'</b></td><td  align="center">'.$theorders['ordernum'].'</td><td align="center">'.$theorders['quantitys'].'</td><td align="center">'.$theorders['totals'].'</td><td>'.$theorders['paymentamount'].'</td><td>'.floatval($theorders['totals']-$theorders['paymentamount']).'</td></tr>';
						$i++;
					}
				}
				$rtn['panel']=$itemstr;
				$rtn['num']=$num;
				return $rtn;
 }
	//客户对应的订单总数
	function relatedCustomerorder($r){
		if($r['customerid']==''){
			return;
		}
		if($r['condition']==''){
			$r['condition']=='';
		}
		$rtn=array();
		$order=$this->DB->query("SELECT `order`.orderid,`order`.customerid,`order`.paymentamount,`customer`.regionid,`region`.regionid
								FROM `order` 
								LEFT JOIN `customer` ON(`customer`.customerid=`order`.customerid)
								LEFT JOIN `region` ON(`region`.regionid=`customer`.regionid)
								WHERE    `order`.customerid='".$r['customerid']."' AND`order`.type='PI'  ".$r['condition']." ".$r['killed']." ");
						if($ordernum=$this->DB->numRows()){
							while($orders=$this->DB->fetchArray($order)){
                             $item=$this->relatedPrinterorderitem(array('orderid'=>$orders['orderid']));
							  $quantitys+=$item['quantity'];
								$totals+=$item['total'];
								$paymentamount+=$orders['paymentamount'];
							}
						}
						   $rtn['ordernum']=$ordernum;
							$rtn['quantitys']=$quantitys;
							$rtn['totals']=$totals;
							$rtn['paymentamount']=$paymentamount;
	              return $rtn;
    	}
 
 //国家对应的订单总数
 function relatedOrderregion($r){
	 if($r['regionid']==''){
			return;
		}
		if($r['condition']==''){
			$r['condition']=='';
		}
		//WHERE  ".$r['condition']." AND  `region`.regionid IN(".implode(',',$r['regionid']).")   ".$r['killed']." 
		$order=$this->DB->query("SELECT `order`.orderid ,`order`.paymentamount,`order`.customerid ,`customer`.regionid 
		   FROM `order` 
		   LEFT JOIN `customer` ON(`customer`.customerid=`order`.customerid) 
		   LEFT JOIN `region` ON(`region`.regionid=`customer`.regionid) 
		WHERE `region`.regionid IN(".implode(',',$r['regionid']).") AND`order`.type='PI' ".$r['condition']."");
						if($ordernum=$this->DB->numRows()){
							while($orders=$this->DB->fetchArray($order)){
                             $item=$this->relatedPrinterorderitem(array('orderid'=>$orders['orderid']));
							  $quantitys+=$item['quantity'];
								$totals+=$item['total'];
								$paymentamount+=$orders['paymentamount'];
							}
						}
						   $rtn['ordernum']=$ordernum;
							$rtn['quantitys']=$quantitys;
							$rtn['totals']=$totals;
							$rtn['paymentamount']=$paymentamount;
	              return $rtn;
 }
 //业务员对应的订单总数
 function relatedDirectororder($r){
     if($r['userid']==''){
			return;
		}
		if($r['condition']==''){
			$r['condition']=='';
		}
		$order=$this->DB->query("SELECT `order`.orderid ,`order`.paymentamount, `order`.customerid ,`customer`.regionid
		 FROM `order`
		 LEFT JOIN `customer` ON(`customer`.customerid=`order`.customerid) 
		 LEFT JOIN `region` ON(`region`.regionid=`customer`.regionid) 
		 WHERE `order`.director='".$r['userid']."' AND`order`.type='PI' ".$r['condition']."");
						if($ordernum=$this->DB->numRows()){
							while($orders=$this->DB->fetchArray($order)){
                             $item=$this->relatedPrinterorderitem(array('orderid'=>$orders['orderid']));
							  $quantitys+=$item['quantity'];
								$totals+=$item['total'];
								$paymentamount+=$orders['paymentamount'];
							}
						}
						   $rtn['ordernum']=$ordernum;
							$rtn['quantitys']=$quantitys;
							$rtn['totals']=$totals;
							$rtn['paymentamount']=$paymentamount;
	              return $rtn;
 }
	// 费用项目明细
	function relatedFee($r){
		if($r['orderid']>0){
			$condition.=" AND `item`.module='order' AND mid='".$r['orderid']."'";
		}
		if($r['porderid']>0){
			$condition.=" AND `item`.module='porder' AND mid='".$r['porderid']."'";
		}
		if($r['invoiceid']>0){
			$condition.=" AND `item`.module='invoice' AND mid='".$r['invoiceid']."'";
		}
		if($r['sampleid']>0){
			$condition.=" AND `item`.module='sample' AND mid='".$r['sampleid']."'";
		}
		if($r['psampleid']>0){
			$condition.=" AND `item`.module='psample' AND mid='".$r['psampleid']."'";
		}
		if($r['maverickpurchaseid']>0){
			$condition.=" AND `item`.module='maverickpurchase' AND mid='".$r['maverickpurchaseid']."'";
		}

		$rtn['array']=array();
		$items = $this->DB->query("
			SELECT item.*,
				fee.title AS fee,
				unit.title AS unit
			FROM `feeitem` AS `item`
			LEFT JOIN `fee` ON (fee.feeid=item.feeid)
			LEFT JOIN unit ON (unit.unitid=item.unitid)
			WHERE `item`.killed=0".$condition."
			ORDER BY `item`.itemid ASC, `item`.modified ASC
		");
		if($this->DB->numRows()){
			$k=1;
			while($item = $this->DB->fetchArray($items)){
				$itemtr .= '<tr class="'.$this->iif($this->rotate(), 'odd', 'even').'">
				<td>'.$k.'</td>
				<td>'.$item['fee'].'</td>
				<td>'.$item['caption'].'</td>
				<td>'.$item['remark'].'</td>
				<td align="right">'.$this->iif($item['discount']>0, $item['discount'].'%', '').'</td>
				<td align="right">'.number_format($item['quantity']).' '.$item['unit'].'</td>
				<td align="right">'.number_format($item['price'], 2).'</td>
				<td align="right">'.number_format($item['amount'], 2).'</td></tr>';
				$k++;
				$quantity+=$item['quantity'];
				$total+=$item['amount'];
				$rtn['array'][]=$item;
			}
			$rtn['total'] = $total;
			$total=number_format($total, 2);

			$rtn['tr']= <<<EOF
<tr><td>ID</td><td>费用项目</td><td>说明</td><td>备注</td><td align="right">折扣</td><td align="right">数量</td><td align="right">单价</td><td align="right">小计</td></tr>
{$itemtr}
<tr><td colspan="5" align="right">合计：</td><td align="right">{$quantity}</td><td></td><td align="right" class="bold">{$total}</td></tr>
EOF;
		}else{
			$rtn['tr']='<tr><td class="gray"><i>无费用信息</i></td></tr>';
		}
		return $rtn;
	}

	//打印费用项目明细
	function relatedPrintFee($r){
		if($r['orderid']>0){
			$condition.=" AND `item`.module='order' AND mid='".$r['orderid']."'";
		}
		if($r['porderid']>0){
			$condition.=" AND `item`.module='porder' AND mid='".$r['porderid']."'";
		}
		if($r['invoiceid']>0){
			$condition.=" AND `item`.module='invoice' AND mid='".$r['invoiceid']."'";
		}
		if($r['sampleid']>0){
			$condition.=" AND `item`.module='sample' AND mid='".$r['sampleid']."'";
		}
		$items = $this->DB->query("
			SELECT item.*,
				fee.entitle AS fee,
				unit.entitle AS unit
			FROM `feeitem` AS `item`
			LEFT JOIN `fee` ON (fee.feeid=item.feeid)
			LEFT JOIN unit ON (unit.unitid=item.unitid)
			WHERE `item`.killed=0".$condition."
			ORDER BY `item`.itemid ASC, `item`.modified ASC
		");
		if($this->DB->numRows()){
			$k=1;
			while($item = $this->DB->fetchArray($items)){
				$itemtr .= '<tr class="odd">
				<td>'.$item['fee'].'</td>
				<td>'.$item['caption'].'</td>
				<td>'.$item['remark'].'</td>
				<td align="right">'.$this->iif($item['discount']>0, $item['discount'].'%', '').'</td>
				<td align="right">'.number_format($item['quantity']).' '.$item['unit'].'</td>
				<td align="right">'.number_format($item['price'], 2).'</td>
				<td align="right">'.number_format($item['amount'], 2).'</td></tr>';
				$k++;
				$quantity+=$item['quantity'];
				$total+=$item['amount'];
			}
			$rtn['total'] = $total;
			$total=number_format($total, 2);
			$rtn['tr']= <<<EOF
<table class="hundred">
<tbody>
	<tr>
		<td colspan="11" class="bold middle">Fee Information</td>
	</tr>
	<tr><td>Fee Item</td><td>Special Note</td><td>Remark</td><td align="right">Discont</td><td align="right">Quantity</td><td align="right">Price</td><td align="right">Amount</td></tr>
	{$itemtr}
	<tr><td colspan="4" align="right">Subtotal：</td><td align="right">{$quantity}</td><td></td><td align="right" class="bold">{$total}</td></tr>
</tbody>
</table>
EOF;
		}else{
			$rtn['tr']='';
		}
		return $rtn;
	}
	/**
 	 * 根据查询条件返回折扣信息
 	 * @author creator zhourui 2013-09-28 10:44
 	 * @access public
 	 * @param array $b 数组参数  module,mid,total
 	 * @return string $str 返回货运表格信息
 	 * @throws none 没有异常
 	 */
	function relatedDiscount($b){
		$items = $this->DB->query("
			SELECT *
			FROM `discount`
			WHERE killed=0 AND module='".$b['module']."' AND mid='".$b['mid']."'
			ORDER BY discountid ASC, modified ASC
		");
		if($this->DB->numRows()){
			$k=1;
			while($item = $this->DB->fetchArray($items)){
				if($b['total']>0){
					$amount = 0;
					if($item['sign']=='%'){
						$item['discount'] = intval($item['discount']);
						$amount = ($b['total'] * $item['discount'] / 100);
					}else{
						$amount = $item['discount'];
					}
					if($item['addition']=='+'){
						$total += $amount;
					}else{
						$total -= $amount;
					}
				}
				$itemtr.='<tr class="'.$this->iif($this->rotate(), 'even', 'odd').'">
				<td>'.$item['reason'].'</td>
				<td align="right">'.$item['addition'].''.$item['discount'].''.$this->iif($item['sign']=='%', $item['sign'], '').'</td>
				<td align="right">'.$item['addition'].number_format($amount, 2).'</td>
				</tr>';
				$k++;
				$quantity+=$item['quantity'];
				$total+=$item['amount'];
				$rtn['array'][]=$item;
			}
			$rtn['total'] = $total;
			$total=number_format($total, 2);

			$rtn['tr']= <<<EOF
<tr><td title="Reason">折扣说明(理由)</td><td title="Discount" align="right">折扣</td><td title="Amount" align="right">计算折扣数</td></tr>
{$itemtr}
<tr><td colspan="2" align="right">合计：</td><td align="right" class="bold">{$total}</td></tr>
EOF;
		}else{
			$rtn['tr']='<tr><td class="gray"><i>无费用信息</i></td></tr>';
		}
		return $rtn;
	}
	// 与PI相关的PO/PI订单
	function relatedOtherOrder($o){
		$orders = $this->DB->query("
			SELECT `order`.*,
				route.title AS routeTitle, route.caption AS routeCaption,
				customer.title AS customerTitle, customer.address AS customerAddress, customer.linkman AS customerLinkman, customer.position AS customerPosition,
					customer.telephone AS customerTelephone, customer.fax AS customerFax,
				supplier.title AS supplierTitle, supplier.address AS supplierAddress, supplier.linkman AS supplierLinkman, supplier.position AS supplierPosition,
					supplier.telephone AS supplierTelephone, supplier.fax AS supplierFax,
				shipmethod.title AS shipmethodTitle, shipmethod.entitle AS shipmethodEntitle,
				loading.title AS loadingTitle, loading.entitle AS loadingEntitle,
				loadingCountry.country AS loadingCountryTitle, loadingCountry.encountry AS loadingCountryEntitle,
				discharge.title AS dischargeTitle, discharge.entitle AS dischargeEntitle,
				dischargeCountry.country AS dischargeCountryTitle, dischargeCountry.encountry AS dischargeCountryEntitle,
				paymentterm.title AS paymenttermTitle, paymentterm.entitle AS paymenttermEntitle,
				deliveryterm.title AS deliverytermTitle, deliveryterm.entitle AS deliverytermEntitle,
				currency.title AS currencyTitle, currency.symbol AS currencySymbol, currency.remark AS currencyRemark,
				m.username AS mname,
				c.username AS cname,
				t.username AS tname,
				d.username AS dname,
				me.realname AS verifyName,
				mem.realname AS approveName
			FROM `order`
			LEFT JOIN orderroute AS route ON (route.routeid=`order`.routeid)
			LEFT JOIN customer ON (customer.customerid=`order`.customerid)
			LEFT JOIN supplier ON (supplier.supplierid=`order`.supplierid)
			LEFT JOIN currency ON (currency.currencyid=`order`.currencyid)
			LEFT JOIN shipmethod ON (shipmethod.shipmethodid=`order`.shipmethodid)
			LEFT JOIN port AS loading ON (loading.portid=`order`.loading)
			LEFT JOIN `region` AS loadingCountry ON (loadingCountry.regionid=loading.regionid)
			LEFT JOIN port AS discharge ON (discharge.portid=`order`.discharge)
			LEFT JOIN `region` AS dischargeCountry ON (dischargeCountry.regionid=discharge.regionid)
			LEFT JOIN paymentterm ON (paymentterm.paymenttermid=`order`.paymenttermid)
			LEFT JOIN deliveryterm ON (deliveryterm.deliverytermid=`order`.deliverytermid)
			LEFT JOIN `user` AS m ON (m.userid=order.modifier)
			LEFT JOIN `user` AS c ON (c.userid=order.creator)
			LEFT JOIN `user` AS t ON (t.userid=order.tracker)
			LEFT JOIN `user` AS d ON (d.userid=order.director)
			LEFT JOIN `member` AS me ON (me.userid=order.verifier)
			LEFT JOIN `member` AS mem ON (mem.userid=order.approver)
			WHERE `order`.killed=0 AND `order`.parentid='".$o['parentid']."'
		");
		if($this->DB->numRows()){
			while($order = $this->DB->fetchArray($orders)){
				if($order['modifier']>0){
					$modified = '，'.$order['mname'].' 于 '.date('y-m-d H:i:s', $order['modified']).' 最后修改';
				}
				$created = date('y-m-d H:i:s', $order['created']);
				if($order['ifVerify']==0){
					$statusid=2;
					$status = '<span class="darkred">未审核　未审批</span>　（<a href="/s.php?module=order&action=verify&orderid='.$order['orderid'].'">审核</a>）';
				}elseif($order['ifVerify']==-1){
					$statusid=2;
					$status = '<span class="red">审核未通过</span>　<span class="darkred">未审批</span>';
				}elseif($order['ifVerify']==1 AND $order['ifApprove']==0){
					$statusid=3;
					$status = '<span class="green">审核通过</span>　<span class="darkred">未审批</span>　（<a href="/s.php?module=order&action=approve&orderid='.$order['orderid'].'">审批</a>）';
				}elseif($order['ifVerify']==1 AND $order['ifApprove']==-1){
					$statusid=3;
					$status = '<span class="green">审核通过</span>　<span class="red">审批未通过</span>';
				}elseif($order['ifVerify']==1 AND $order['ifApprove']==1){
					$statusid=4;
					$status = '<span class="green">审核通过　审批通过</span>';
				}
				if($order['type']=='PO'){
					if($order['ifChooser']==1)$statusid=5;
					if($outbound!='')$statusid=6;
					$status.=$this->iif($order['ifChooser']==1,'　<span class="green">已投产</span>','　<span class="darkred">未投产</span>');
					$outbound=$this->DB->queryFirst("
						SELECT outboundid
						FROM outbound
						WHERE killed=0 AND ifComplete=2 AND orderid='".$order['orderid']."'
					");
					$status.=$this->iif($outbound=='','　<span class="darkred">未出库</span>','　<span class="green">已出库</span>');
				}
				if($order['verified']>0){
					$verify='由 '.$order['verifyName'].' 于 '.date('Y-m-d H:i:s', $order['verified']).' 最后审核';
				}
				if($order['approved']>0){
					$approve='，由 '.$order['approveName'].' 于 '.date('Y-m-d H:i:s', $order['approved']).' 最后审批';
				}
				$shipmentDate=date('Y-m-d', $order['shipmentDate']);
				$gray='';
				if($order['type']=='PI'){
					$itemTitle = '客户';
					$rtn['tabs'] .= '<dt>PI (销售订单)</dt>';
					$gray['supplier'] = 'gray';
				}elseif($order['type']=='PO'){
					$itemTitle = '供应商';
					$rtn['tabs'] .= '<dt>PO (采购订单)</dt>';
					$gray['customer'] = 'gray';
				}

				$freight = $this->DB->queryFirst("SELECT * FROM `freight` WHERE module='order' AND mid='".$order['orderid']."'");
				$freightStr = '';
				if($freight){
					$freightStr = '<a href="/s.php?module=freight&action=update&orderid='.$order['orderid'].'">修改货运信息</a>　<a href="/s.php?module=freight&action=kill&orderid='.$order['orderid'].'" onclick="return confirm(\'确定删除货运信息?\');">删除货运信息</a>';
				}else{
					$freight = array();
					$freightStr = '<a href="/s.php?module=freight&action=add&orderid='.$order['orderid'].'">新建货运信息</a>';
				}

				$packingStr = '';
				$packing = $this->DB->queryFirst("SELECT * FROM `packinglist` WHERE module='order' AND mid='".$order['orderid']."'");
				if($packing){
					$packingStr = '<a href="/s.php?module=packinglist&action=updatePacking&orderid='.$order['orderid'].'&packinglistid='.$packing['packinglistid'].'">修改包装箱单</a>';
				}else{
					$packingStr = '<a href="/s.php?module=packinglist&action=addPacking&orderid='.$order['orderid'].'">生成包装箱单</a>';
				}

				$freightInfo = $this->getFreightInfo($freight);
				$revise=$this->relatedRevise(array('module'=>'order','mid'=>$order['orderid']));
				$item = $this->relatedOrderItem(array('orderid'=>$order['orderid'], 'title'=>$itemTitle));
				$flow=$this->listFlow(array('module'=>'poorder','statusid'=>$statusid));

$rtn['panel'] .= <<<EOF
<dd>
<div><span class="small gray">当前PO状态：{$status}</span>　　　　　<span class="gray small">{$verify}{$approve}</span><span class="right normal">{$freightStr}　{$packingStr}　|　<a href="/s.php?module=order&action=update&orderid={$order['orderid']}&rt=view">修改PO</a>　<a href="/s.php?module=order&action=remove&orderid={$order['orderid']}&rt=view">删除</a>　|　<a href="/s.php?module=order&action=chooseLanguage&actionid=po&orderid={$order['orderid']}" target="_blank">打印{$order['type']}</a>　<a href="/s.php?module=order&action=chooseLanguage&actionid=pk&orderid={$order['orderid']}" target="_blank">打印装箱明细</a>　<a href="/s.php?module=order&action=chooseLanguage&actionid=pl&orderid={$order['orderid']}" target="_blank">打印PL</a></span></div>
<div class="bold darkred big" style="clear:both;">{$order['ordertitle']}{$revise['currencyRevise']}　　　　<span class="gray small">业务人员：<span class="bold darkred">{$order['dname']}</span></span>　　　　　<span class="gray small">跟单人员：<span class="bold darkred">{$order['tname']}</span></span>　　　　　<span class="gray small">由 {$order['cname']} 于 {$created} 建立{$modified}</span></div>
<div><span class="right">{$flow}</span></div>
<table class="hundred">
<thead>
<tr>
	<th colspan="4">基本信息 (Basic Information)</th>
</tr>
</thead>
<tbody>
<tr>
	<td width="573" colspan="2" class="bold {$gray['customer']}" align="center">客户 (Buyer)</td>
	<td width="573" colspan="2" class="bold {$gray['supplier']}" align="center">供应商 (Seller)</td>
</tr>
<tr class="odd">
	<td class="{$gray['customer']}" width="70">订单号：</td>
	<td class="{$gray['customer']}">{$order['customerNo']}</td>
	<td width="70" class="{$gray['supplier']}">订单号：</td>
	<td class="{$gray['supplier']}">{$order['supplierNo']}</td>
</tr>
<tr class="odd">
	<td class="{$gray['customer']}">名　称：</td>
	<td class="{$gray['customer']}">{$order['customerTitle']}</td>
	<td class="{$gray['supplier']}">名　称：</td>
	<td class="{$gray['supplier']}">{$order['supplierTitle']}</td>
</tr>
<tr class="odd">
	<td class="{$gray['customer']}">地　址：</td>
	<td class="{$gray['customer']}">{$order['customerAddress']}</td>
	<td class="{$gray['supplier']}">地　址：</td>
	<td class="{$gray['supplier']}">{$order['supplierAddress']}</td>
</tr>
<tr class="odd">
	<td class="{$gray['customer']}">联系人：</td>
	<td class="{$gray['customer']}">{$order['customerLinkman']} <span class="{$gray['supplier']}">{$order['customerPosition']}</span></td>
	<td class="{$gray['supplier']}">联系人：</td>
	<td class="{$gray['supplier']}">{$order['supplierLinkman']} <span class="{$gray['supplier']}">{$order['supplierPosition']}</span></td>
</tr>
<tr class="odd">
	<td class="{$gray['customer']}">电　话：</td>
	<td class="{$gray['customer']}">{$order['customerTelephone']}</td>
	<td class="{$gray['supplier']}">电　话：</td>
	<td class="{$gray['supplier']}">{$order['supplierTelephone']}</td>
</tr>
<tr class="odd">
	<td class="{$gray['customer']}">传　真：</td>
	<td class="{$gray['customer']}">{$order['customerFax']}</td>
	<td class="{$gray['supplier']}">传　真：</td>
	<td class="{$gray['supplier']}">{$order['supplierFax']}</td>
</tr>
</tbody>
</table>
<table class="hundred">
<thead>
<tr>
	<th colspan="4">标准信息 (Critertion Information)</th>
</tr>
</thead>
<tbody>
<tr>
	<td width="70" valign="top" title="Labeling">标签说明：</td>
	<td width="494" class="middle">{$order['labeling']}</td>
	<td width="70" valign="top" title="Packing">包装说明：</td>
	<td class="middle">{$order['packing']}</td>
</tr>
<tr>
	<td valign="top" title="Special">特别说明：</td>
	<td class="middle">{$order['special']}</td>
	<td valign="top" title="Remark">备　　注：</td>
	<td class="middle">{$order['remark']}</td>
</tr>
</tbody>
</table>
<table class="hundred">
<thead>
<tr>
	<th colspan="12">订单明细 (Item Information)</th>
</tr>
</thead>
<tbody>
{$item['tr']}
</tbody>
</table>
{$freightInfo}
{$revise['panel']}
</dd>
EOF;
			}
		}
		return $rtn;
	}
	//
	function relatedOrderTracking($b){
		if($b['porderid']){
			$porder=$this->DB->queryFirst("SELECT ifApprove FROM porder WHERE killed=0 AND porderid='".$b['porderid']."'");
			$condition1=" AND `item`.module='porder' AND `item`.mid=".$b['porderid'];
			$condition2=" AND ot.module='porder' AND ot.mid=".$b['porderid'];
		}elseif($b['supplierid']){
			$orders=$this->DB->query("SELECT porderid FROM porder WHERE killed=0 AND supplierid='".$b['supplierid']."'");
			if($this->DB->numRows()){
				while($order=$this->DB->fetchArray($orders)){
					$id[]=$order['porderid'];
				}
			}else{
				return false;
			}
			if(is_Array($id)){
				$condition1.=" AND `item`.module='porder' AND `item`.mid IN (".implode(',',$id).")";
				$condition2.=" AND ot.module='porder' AND ot.mid IN (".implode(',',$id).")";
			}
		}else{
			return false;
		}
		$items=$this->DB->query("
			SELECT `item`.itemid,`item`.materialid,`item`.quantity AS total,`item`.amount,
				`material`.materialno,`material`.title,`material`.standard,
				`unit`.title AS unit
			FROM `item`
			LEFT JOIN `material` ON (`material`.materialid=`item`.materialid)
			LEFT JOIN `unit` ON (`unit`.unitid=`item`.unitid)
			WHERE `item`.killed=0 ".$condition1."
			ORDER BY `item`.created ASC
		");
		if($this->DB->numRows()){
			$i=1;
			// 新增到货记录 暂保留
			// '.$this->iif($porder['ifApprove']==1,'<span class="right"><a href="/s.php?module=porderTracking&action=add&porderid='.$b['porderid'].'">新增到货记录</a></span>','').'
			$r.='<table class="hundred"><thead><tr><th colspan=11>物资跟踪 </th></tr></thead><tbody><tr class="even bold center"><td>ID</td><td>编号</td><td>名称</td><td>规格</td><td>应到数量</td><td>已到数量</td><td>未到数量</td><td>应付</td><td>已付</td><td>未付</td><td>操作</td></tr>';
			while($item=$this->DB->fetchArray($items)){
				$tracking=$this->DB->queryFirst("SELECT SUM(quantity) AS arrivalCount,SUM(amount) AS amount FROM ordertracking WHERE killed=0 AND itemid='".$item['itemid']."' LIMIT 0,1");
				$r.='<tr class="'.$this->iif($this->rotate(),'odd','even').'">
					<td width="20">'.$i.'</td>
					<td width="50"><a href="/s.php?module=material&action=view&materialid='.$item['materialid'].'">'.$item['materialno'].'</a></td>
					<td>'.$item['title'].'</td>
					<td>'.$item['standard'].'</td>
					<td>'.intval($item['total']).' '.$item['unit'].'</td>
					<td>'.intval($tracking['arrivalCount']).' '.$item['unit'].'</td>
					<td>'.(intval($item['total'])-intval($tracking['arrivalCount'])).' '.$item['unit'].'</td>
					<td>'.number_format($item['amount'],2).'</td>
					<td>'.number_format($tracking['amount'],2).'</td>
					<td>'.number_format($item['amount']-$tracking['amount'],2).'</td>
					<td><a href="/s.php?module=porderTracking&action=view&itemid='.$item['itemid'].'">查看记录</a>
					</tr>';
				$i++;
			}
			$r.='</tbody></table>';
		}
		$tracks=$this->DB->query("SELECT DISTINCT inboundid FROM `ordertracking` WHERE killed=0 ORDER BY created ASC ");
		if($this->DB->numRows()){
			while($track=$this->DB->fetchArray($tracks)){
				$title='';
				$orderTrackings=$this->DB->query("
					SELECT ot.trackingid,ot.mid,ot.itemid,ot.materialid,
							ot.quantity,ot.dateline,ot.remark,
						m.materialno,m.title,m.standard,
						u.title AS unit,
						po.porderid,po.orderno
					FROM `ordertracking` AS ot
					LEFT JOIN `material` AS m ON (m.materialid=ot.materialid)
					LEFT JOIN `unit` AS u ON (u.unitid=ot.unitid)
					LEFT JOIN `porder` AS po ON (po.porderid=ot.mid)
					WHERE ot.paymentid=0 AND ot.killed=0 AND ot.inboundid='".$track['inboundid']."' ".$condition2."
					ORDER BY ot.created ASC
				");
				if($this->DB->numRows()){
					$i=1;
					$inbound=$this->DB->queryFirst("SELECT inboundid,inboundno,arrivalno,created FROM inbound WHERE killed=0 AND inboundid='".$track['inboundid']."'");
					$created=date('Y-m-d H:i:s',$inbound['created']);
					if($inbound['arrivalno']!=''){
						$title='收货单号：'.$inbound['arrivalno'];
					}
					if($inbound['inboundid']!=0){
						$title.='入库申请单：<a href="/s.php?module=inbound&action=view&inboundid='.$inbound['inboundid'].'">'.$inbound['inboundno'].'</a>';
					}
					// 添加入库申请  暂保留
					//<span class="right">'.$this->iif($time['inboundid']==0,'<a href="/s.php?module=pordertracking&action=insertInbound&porderid='.$b['porderid'].'&created='.$time['created'].'">添加入库申请单</a>','').$this->iif($time['inboundid']==0,'　<a href="/s.php?module=porderTracking&action=update&porderid='.$b['porderid'].'&created='.$time['created'].'">修改</a>','').'</span>
					$r.='<table class="hundred"><thead><tr><th colspan=10>于'.$created.' 记录 '.$title.'</th></tr></thead><tbody><tr class="even bold center"><td>ID</td><td>订单编号</td><td>编号</td><td>名称</td><td>规格</td><td>已到数量</td><td>到货时间</td></tr>';
					while($orderTracking=$this->DB->fetchArray($orderTrackings)){
						$r.='<tr class="'.$this->iif($this->rotate(),'odd','even').'">
							<td width="20">'.$i.'</td>
							<td width="50"><a href="/s.php?module=porder&action=view&porderid='.$orderTracking['porderid'].'">'.$orderTracking['orderno'].'</a></td>
							<td width="50"><a href="/s.php?module=material&action=view&materialid='.$orderTracking['materialid'].'">'.$orderTracking['materialno'].'</a></td>
							<td>'.$orderTracking['title'].'</td>
							<td>'.$orderTracking['standard'].'</td>
							<td align="right" width="80">'.number_format($orderTracking['quantity']).' '.$orderTracking['unit'].'</td>
							<td align="right" width="80">'.date('Y-m-d',$orderTracking['dateline']).'</td></tr>';
						$i++;
					}
					$r.='</tbody></table>';
				}
			}
		}

		return $r;
	}
	// 佣金
	function relatedCommission(){
		return array('off'=>' disabled', 'num'=>0);
	}
	// 保证金
	function relatedGuarantee(){
		return array('off'=>' disabled', 'num'=>0);
	}
	// 定金
	function relatedDeposit(){
		return array('off'=>' disabled', 'num'=>0);
	}
	// 应收
	function relatedCreditnote(){
		return array('off'=>' disabled', 'num'=>0);
	}
	// 已收
	function relatedRecipt(){
		return array('off'=>' disabled', 'num'=>0);
	}
	// 应付
	function relatedDebitnote($r){
		if($r['module']=='porder'){
			$t='porder';
			$tid='porderid';
			$supplierM='psupplier';
		}elseif($r['module']=='order'){
			$t='order';
			$tid='orderid';
			$supplierM='supplier';
		}
		/*if($r['materialid']>0){
			$id=array();
			$items=$this->DB->queryFirst("SELECT SUM(amount) AS amount FROM `item` WHERE killed=0 AND module='porder' AND materialid='".$r['materialid']."'");
			if($this->DB->numRows()){
				while($item=$this->DB->fetchArray($items)){
					if(!in_array($item['mid'],$id)){
						$id[]=$item['mid'];
					}
				}
			}else{
				$rtn['off']=' disabled';
				return $rtn;
			}
			if(is_Array($id)){
				$condition=' AND `debitnote`.module="'.$r['module'].'" AND `debitnote`.mid IN ('.implode(',',$id).')';
			}
		}*/
		if($r['mid']>0){
			$order=$this->DB->queryFirst("SELECT o.supplierid,o.orderno,o.ifApprove,s.title AS supplier FROM ".$t." AS o LEFT JOIN supplier AS s ON (o.supplierid=s.supplierid) WHERE o.killed=0 AND o.ifApprove=1 AND ".$tid."='".$r['mid']."'");
			$item=$this->DB->queryFirst("SELECT SUM(amount) AS amount FROM item WHERE killed=0 AND module='".$t."' AND mid='".$r['mid']."'");
			$index=1;
			if($order['ifApprove']==1){
				$rtn['panel']=<<<EOF
<table class="hundred">
<thead>
<tr><th colspan="8"><span class="normal">[<b>1</b>]</span> <span class="small">订单：</span><span class="middle bold"><a href="/s.php?module={$t}&action=view&{$tid}={$r['mid']}">{$order['orderno']}</a></span>　<span class="small">供应商：</span><a href="/s.php?module={$supplierM}&action=view&supplierid={$order['supplierid']}">{$order['supplier']}</a>　<span class="small">应付金额：</span>{$item['amount']}</tr>
</thead></table>
EOF;
			}
			$payments=$this->DB->query("SELECT DISTINCT p.paymentid FROM payment AS p LEFT JOIN ordertracking AS ot ON (ot.paymentid=p.paymentid) WHERE p.killed=0 AND p.supplierid='".$order['supplierid']."' AND ot.killed=0 AND ot.module='".$t."' AND ot.mid='".$r['mid']."' ORDER BY p.created ASC");
			if($this->DB->numRows()){
				$debitnote=$item['amount'];
				while($payment=$this->DB->fetchArray($payments)){
					$payAmount=$this->DB->queryFirst("SELECT SUM(amount) AS amount FROM ordertracking WHERE killed=0 AND module='".$t."' AND mid='".$r['mid']."' AND paymentid='".$payment['paymentid']."' LIMIT 0,1");
					$debitnote-=$payAmount['amount'];
					$index++;
					$title='';
					$debitnote=number_format($debitnote['amount'],4);
					$rtn['panel'].=<<<EOF
<table class="hundred">
<thead>
<tr><th colspan="8"><span class="normal">[<b>{$index}</b>]</span> <span class="small">订单：</span><span class="middle bold"><a href="/s.php?module={$t}&action=view&{$tid}={$r['mid']}">{$order['orderno']}</a></span>　<span class="small">供应商：</span><a href="/s.php?module={$supplierM}&action=view&supplierid={$order['supplierid']}">{$order['supplier']}</a>　<span class="small">应付金额：</span>{$debitnote}　</tr>
</thead></table>
EOF;
				}
			}
			if($rtn['panel']==''){
				$rtn['off']=' disabled';
			}else{
				$rtn['count']=' ('.$index.')';
			}
		}
		if($r['supplierid']>0){
			$supplier=$this->DB->queryFirst("SELECT title FROM supplier WHERE supplierid='".$r['supplierid']."'");
			$index=1;
			$items=$this->DB->query("
				SELECT i.amount
				FROM item AS i
				LEFT JOIN porder AS p ON (p.porderid=i.mid)
				WHERE i.killed=0 AND p.killed=0 AND i.module='porder' AND p.supplierid='".$r['supplierid']."' AND p.ifApprove=1
			");
			if($this->DB->numRows()){
				while($item=$this->DB->fetchArray($items)){
					$amount+=$item['amount'];
				}
			}
			$total=number_format($amount,2);
			$rtn['panel']=<<<EOF
<table class="hundred">
<thead>
<tr><th colspan="8"><span class="normal">[<b>1</b>]</span> 　<span class="small">供应商：</span><a href="/s.php?module=psupplier&action=view&supplierid={$r['supplierid']}">{$supplier['title']}</a>　<span class="small">应付金额：</span>{$total}</tr>
EOF;
			$debitnotes=$this->DB->query("
				SELECT `debitnote`.*,
					`supplier`.supplierid,`supplier`.title AS supplier,
					`currency`.title AS currency
				FROM `debitnote`
				LEFT JOIN `currency` ON (currency.currencyid=debitnote.currencyid)
				LEFT JOIN `supplier` ON (`supplier`.supplierid=`debitnote`.supplierid)
				WHERE `debitnote`.killed=0 AND `debitnote`.supplierid='".$r['supplierid']."'
				ORDER BY created ASC
			");
			if($this->DB->numRows()){
				while($debitnote=$this->DB->fetchArray($debitnotes)){
					$index+=1;
					$title='';
					if($debitnote['title']!='')$title.='　<span class="small">标题：</span>'.$debitnote['title'];
					if($debitnote['supplier']!='')$title.='　<span class="small">供应商：</span><a href="/s.php?module='.$supplierM.'&action=view&supplierid='.$debitnote['supplierid'].'">'.$debitnote['supplier'].'</a>';
					if($debitnote['amount']!='')$title.='　<span class="small">应付金额：</span>'.number_format($debitnote['amount'],4);
					if($debitnote['currency']!='')$title.='　<span class="small">币种：</span>'.$debitnote['currency'];
					$rtn['panel'].=<<<EOF
<table class="hundred">
<thead>
<tr><th colspan="8"><span class="normal">[<b>{$index}</b>]</span> {$title}</th></tr>
</thead>
</table>
EOF;
				}
				$rtn['panel'].='</tbody></table>';
				$rtn['count']=' ('.$index.')';
			}else{
				$rtn['off']=' disabled';
			}
		}

//		return $rtn;
		return array('off'=>' disabled', 'num'=>0);
	}
	// 已付
	function relatedPayment($r){
		if($r['module']=='porder'){
			$t='porder';
			$tid='porderid';
			$supplierM='psupplier';
		}elseif($r['module']=='order'){
			$t='order';
			$tid='orderid';
			$supplierM='supplier';
		}
		if($r['orderby']==''){
			$r['orderby']='`payment`.created';
		}
		if($r['direction']==''){
			$r['direction']='DESC';
		}
		if($r['limit']==''){
			$r['limit']='0, 100';
		}
		$id=array();
		if($r['materialid']>0){
			$items=$this->DB->query("SELECT paymentid FROM `ordertracking` WHERE killed=0 AND materialid='".$r['materialid']."' AND paymentid<>0");
			if($this->DB->numRows()){
				while($item=$this->DB->fetchArray($items)){
					if(!in_array($item['paymentid'],$id)){
						$id[]=$item['paymentid'];
					}
				}
			}else{
				$rtn['off']=' disabled';
				return $rtn;
			}
			/*if(is_Array($id)){
				$condition=' AND INSTR ("'.implode(',',$id).'",`payment`.paymentid)';
			}*/
			$itemCondition=' AND `ordertracking`.materialid='.$r['materialid'];
		}
		if($r['mid']>0){
			$items=$this->DB->query("SELECT paymentid FROM `ordertracking` WHERE killed=0 AND module='".$r['module']."' AND mid='".$r['mid']."' AND paymentid<>0");
			if($this->DB->numRows()){
				while($item=$this->DB->fetchArray($items)){
					if(!in_array($item['paymentid'],$id)){
						$id[]=$item['paymentid'];
					}
				}
			}else{
				$rtn['off']=' disabled';
				return $rtn;
			}
			/*if(is_Array($id)){
				$condition=' AND INSTR ("'.implode(',',$id).'",`payment`.paymentid)';
			}*/
			$itemCondition=' AND `ordertracking`.mid='.$r['mid'];
		}
		if($r['supplierid']>0){
			$items=$this->DB->query("SELECT paymentid FROM payment WHERE killed=0 AND supplierid='".$r['supplierid']."'");
			if($this->DB->numRows()){
				while($item=$this->DB->fetchArray($items)){
					if(!in_array($item['paymentid'],$id)){
						$id[]=$item['paymentid'];
					}
				}
			}else{
				$rtn['off']=' disabled';
				return $rtn;
			}
			//$condition=' AND `payment`.supplierid='.$r['supplierid'];
		}

		$payments=$this->DB->query("
			SELECT `payment`.*,
				`supplier`.supplierid,`supplier`.title AS supplier,
				`currency`.title AS currency
			FROM `payment`
			LEFT JOIN `currency` ON (`currency`.currencyid=`payment`.currencyid)
			LEFT JOIN `supplier` ON (`supplier`.supplierid=`payment`.supplierid)
			WHERE `payment`.killed=0 ".$condition."
			ORDER BY ".$r['orderby']." ".$r['direction']."
			LIMIT ".$r['limit']."
		");
		if($rtn['num']=$this->DB->numRows()){
			$index=0;
			while($payment=$this->DB->fetchArray($payments)){
				if(in_array($payment['paymentid'],$id)){
					$index+=1;
					$title='';
					if($payment['title']!='')$title.='　<span class="small">标题：</span>'.$payment['title'];
					if($payment['supplier']!='')$title.='　<span class="small">供应商：</span><a href="/s.php?module='.$supplierM.'&action=view&supplierid='.$payment['supplierid'].'">'.$payment['supplier'].'</a>';
					if($payment['amount']!='')$title.='　<span class="small">已付金额：</span>'.$payment['amount'];
					if($payment['currency']!='')$title.='　<span class="small">币种：</span>'.$payment['currency'];
					if($payment['dateline']!='')$title.='　<span class="small">付款时间：</span>'.date('Y-m-d',$payment['dateline']);
					if($payment['remark']!='')$title.='　<span class="small">备注：</span>'.$payment['remark'];
					$trackingid=array();
					$items=$this->DB->query("
						SELECT `ordertracking`.*,
							`material`.materialno,`material`.title,`material`.standard,
							m.".$tid." AS orderid,m.orderno,
							`unit`.title AS unit
						FROM `ordertracking`
						LEFT JOIN `material` ON (`material`.materialid=`ordertracking`.materialid)
						LEFT JOIN `unit` ON (`unit`.unitid=`ordertracking`.unitid)
						LEFT JOIN `".$t."` AS m ON (m.".$tid."=`ordertracking`.mid)
						WHERE `ordertracking`.killed=0 AND `ordertracking`.paymentid='".$payment['paymentid']."' ".$itemCondition."
						ORDER BY `ordertracking`.mid ASC,created DESC
					");
					if($this->DB->numRows()){
						$i=1;
						$itemtr='';
						while($item=$this->DB->fetchArray($items)){
							$total+=$item['amount'];
							$itemtr.='<tr class="'.$this->iif($this->rotate(),'even','odd').'">
							<td width="20">'.$i.'</td>
							<td width="100"><a href="/s.php?module='.$t.'&action=view&'.$tid.'='.$item['orderid'].'">'.$item['orderno'].'</a></td>
							<td width="50"><a href="/s.php?module=material&action=view&materialid='.$item['materialid'].'">'.$item['materialno'].'</a></td>
							<td>'.$this->iif(strlen($item['title'])>12, mb_substr($item['title'], 0, 12, 'UTF-8').'...', $item['title']).'</td>
							<td>'.$this->iif(strlen($item['standard'])>12, mb_substr($item['standard'], 0, 12, 'UTF-8').'...', $item['standard']).'</td>
							<td align="right" width="80">'.number_format($item['amount'],2).'</td>
							<td align="right" width="80">'.date('Y-m-d',$item['dateline']).'</td></tr>';
							$trackingid[]=$item['trackingid'];
							$i++;
						}
					}
					if(is_array($trackingid) AND $trackingid){
						$trackingId=implode(',',$trackingid);
						$olItems=$this->DB->query("
							SELECT `ordertracking`.*,
								`material`.materialno,`material`.title,`material`.standard,
								m.".$tid." AS orderid,m.orderno,
								`unit`.title AS unit
							FROM `ordertracking`
							LEFT JOIN `material` ON (`material`.materialid=`ordertracking`.materialid)
							LEFT JOIN `unit` ON (`unit`.unitid=`ordertracking`.unitid)
							LEFT JOIN `".$t."` AS m ON (m.".$tid."=`ordertracking`.mid)
							WHERE `ordertracking`.killed=0 AND `ordertracking`.paymentid='".$payment['paymentid']."' AND `ordertracking`.trackingid NOT IN (".$trackingId.")
							ORDER BY `ordertracking`.mid ASC,created DESC
						");
						if($counter=$this->DB->numRows()){
							$i=1;
							$itemtr.='<tr><td colspan=8>供应商 <span class="bold darkred">'.$payment['supplier'].'</span> 的其他订单下的条目明细 <span class="small gray">(共'.$counter.'个，但没有明细内容的订单不会显示)</span></td></tr>';
							while($olItem=$this->DB->fetchArray($olItems)){
								$total+=$olItem['amount'];
								$itemtr.='<tr class="'.$this->iif($this->rotate(),'even','odd').'">
								<td width="20">'.$i.'</td>
								<td width="100"><a href="/s.php?module='.$t.'&action=view&'.$tid.'='.$olItem['orderid'].'">'.$olItem['orderno'].'</a></td>
								<td width="50"><a href="/s.php?module=material&action=view&materialid='.$olItem['materialid'].'">'.$olItem['materialno'].'</a></td>
								<td>'.$this->iif(strlen($olItem['title'])>12, mb_substr($olItem['title'], 0, 12, 'UTF-8').'...', $olItem['title']).'</td>
								<td>'.$this->iif(strlen($olItem['standard'])>12, mb_substr($olItem['standard'], 0, 12, 'UTF-8').'...', $olItem['standard']).'</td>
								<td align="right" width="80">'.number_format($olItem['amount'],2).'</td>
								<td align="right" width="80">'.date('Y-m-d',$olItem['dateline']).'</td></tr>';
								$amount+=$olItem['amount'];
								$i++;
							}
						}
					}
					$options=$this->iif($index==1 AND $r['supplierid']>0,$this->iif($debitnote['killed']==0,'<a href="/s.php?module=accounting&action=updatePayment&paymentid='.$payment['paymentid'].'">修改</a> <a href="/s.php?module=accounting&action=killPayment&paymentid='.$payment['paymentid'].'&rt=list" onclick="return confirm(\'你确定要删除这个应付 '.$payment['title'].' 吗,删除后无法恢复？\');">删除</a>','<a href="/s.php?module=accounting&action=restorePayment&paymentid='.$payment['paymentid'].'">恢复</a>'),'');
					$rtn['panel'].=<<<EOF
<table class="hundred">
<thead>
<tr><th colspan="8"><span class="right">{$options} <span class="plus"></span></span>	<span class="normal">[<b>{$index}</b>]</span>{$title}</th></tr>
</thead>
<tbody id="payment{$payment['paymentid']}" style="display:none">
<tr class="even bold center"><td>ID</td><td>订单编号</td><td>编号</td><td>名称</td><td>规格</td><td>已付金额</td><td>付款时间</td></tr>{$itemtr}
</tbody>
</table>
EOF;
				}
				$rtn['count']=' ('.$index.')';
			}
		}else{
			$rtn['off']=' disabled';
		}
//		return $rtn;
		return array('off'=>' disabled', 'num'=>0);
	}
	// 索赔
	function relatedClaim($r){
		if($r['module']=='porder'){
			$t='porder';
			$tid='porderid';
		}elseif($r['module']=='order'){
			$t='order';
			$tid='orderid';
		}
		if($r['mid']>0){
			$condition=' AND `claim`.module="'.$r['module'].'" AND `claim`.mid='.$r['mid'];
		}

		if($r['supplierid']>0){
			$condition=' AND `claim`.supplierid='.$r['supplierid'];
		}
		if($r['orderby']==''){
			$r['orderby']='`claim`.created';
		}
		if($r['direction']==''){
			$r['direction']='DESC';
		}
		if($r['limit']==''){
			$r['limit']='0, 100';
		}
		$claims=$this->DB->query("
			SELECT `claim`.*,
				o.orderno,
				`item`.materialid,`item`.productid,
				`currency`.title AS currency,
				u.username AS creator,
				us.username AS modifier
			FROM `claim`
			LEFT JOIN `".$t."` AS o ON (o.".$tid."=`claim`.mid)
			LEFT JOIN `item` ON (`item`.itemid=`claim`.itemid)
			LEFT JOIN `currency` ON (`currency`.currencyid=`claim`.currencyid)
			LEFT JOIN `user` AS u ON (u.userid=`claim`.creator)
			LEFT JOIN `user` AS us ON (us.userid=`claim`.modifier)
			WHERE `claim`.killed=0 ".$condition."
			ORDER BY ".$r['orderby']." ".$r['direction']."
			LIMIT ".$r['limit']."
		");
		$rtn['num']=$this->DB->numRows();
		if($rtn['num']){
			$index=0;
			$rtn['panel']='<table class="hundred"><thead><tr><th>编号</th><th>标题</th><th>索赔物资</th><th>币种</th><th>索赔金额</th><th>原因</th><th>备注</th><th></th></tr></thead><tbody>';
			while($claim=$this->DB->fetchArray($claims)){
				if($t=='porder'){
					$result=$this->DB->queryFirst('SELECT materialid,materialno,title,standard FROM material WHERE materialid='.$claim['materialid']);
					$url='/s.php?module=material&action=view&materialid='.$result['materialid'];
					$td=$result['materialno'].' '.$result['title'].' '.$result['standard'];
				}elseif($t=='order'){
					$result=$this->DB->queryFirst('SELECT productid,productno,title FROM product WHERE materialid='.$claim['materialid']);
					$url='/s.php?module=product&action=view&productid='.$result['productid'];
					$td=$result['productno'].' '.$result['title'];
				}
				$index+=1;
				$claim['created']=date('Y-m-d H:i',$claim['created']);
				$rtn['panel'].='<tr class="'.$this->rotateLine().'">
					<td width="40">'.$index.'</td>
					<td width="250">'.$claim['title'].'</td>
					<td width="350"><a href="'.$url.'">'.$td.'</a></td>
					<td>'.$claim['currency'].'</td>
					<td>'.$claim['amount'].'</td>
					<td>'.$claim['reason'].'</td>
					<td>'.$claim['remark'].'</td>
					<td width="80"><a href="/s.php?module=service&action=killClaim&claimid='.$claim['claimid'].'&rt=list" onclick="return confirm(\'你确定要删除这个索赔单 '.$claim['title'].' 吗,删除后无法恢复？\');">删除</a>　<a href="/s.php?module=service&action=updateClaim&claimid='.$claim['claimid'].'">修改</a></td></tr>';
			}
			$rtn['panel'].='</tbody></table>';
			$rtn['count']=' ('.$rtn['num'].')';
		}else{
			$rtn['off']=' disabled';
		}
		return $rtn;
	}
	// 退货
	function relatedReturn($r){
		if($r['module']=='porder'){
			$t='porder';
			$tid='porderid';
		}elseif($r['module']=='order'){
			$t='order';
			$tid='orderid';
		}
		if($r['mid']>0){
			$condition=' AND `return`.module="'.$r['module'].'" AND `return`.mid='.$r['mid'];
		}

		if($r['supplierid']>0){
			$condition=' AND `return`.supplierid='.$r['supplierid'];
		}
		if($r['orderby']==''){
			$r['orderby']='`return`.created';
		}
		if($r['direction']==''){
			$r['direction']='DESC';
		}
		if($r['limit']==''){
			$r['limit']='0, 100';
		}
		$returns=$this->DB->query("
			SELECT `return`.*,
				o.orderno,
				`item`.materialid,`item`.productid,
				u.username AS creator,
				us.username AS modifier
			FROM `return`
			LEFT JOIN `".$t."` AS o ON (o.".$tid."=`return`.mid)
			LEFT JOIN `item` ON (`item`.itemid=`return`.itemid)
			LEFT JOIN `user` AS u ON (u.userid=`return`.creator)
			LEFT JOIN `user` AS us ON (us.userid=`return`.modifier)
			WHERE `return`.killed=0 ".$condition."
			ORDER BY ".$r['orderby']." ".$r['direction']."
			LIMIT ".$r['limit']."
		");
		$rtn['num']=$this->DB->numRows();
		if($rtn['num']){
			$index=0;
			$rtn['panel']='<table class="hundred"><thead><tr><th>编号</th><th>退货单号</th><th>退货物资</th><th>原因</th><th>备注</th><th>制单时间</th><th></th><th>制单人</th></tr></thead><tbody>';
			while($return=$this->DB->fetchArray($returns)){
				if($t=='porder'){
					$result=$this->DB->queryFirst('SELECT materialid,materialno,title,standard FROM material WHERE materialid='.$return['materialid']);
					$url='/s.php?module=material&action=view&materialid='.$result['materialid'];
					$td=$result['materialno'].' '.$result['title'].' '.$result['standard'];
				}elseif($t=='order'){
					$result=$this->DB->queryFirst('SELECT productid,productno,title FROM product WHERE materialid='.$return['materialid']);
					$url='/s.php?module=product&action=view&productid='.$result['productid'];
					$td=$result['productno'].' '.$result['title'];
				}
				$index+=1;
				$return['created']=date('Y-m-d H:i',$return['created']);
				$rtn['panel'].='<tr class="'.$this->rotateLine().'">
				<td width="40">'.$index.'</td>
				<td width="40">'.$return['returnno'].'</td>
				<td width="350"><a href="'.$url.'">'.$td.'</a></td>
				<td>'.$return['reason'].'</td>
				<td>'.$return['remark'].'</td>
				<td>'.$return['created'].'</td>
				<td>'.$return['creator'].'</td>
				<td width="80"><a href="/s.php?module=service&action=killReturn&returnid='.$return['returnid'].'&rt=list" onclick="return confirm(\'你确定要删除这个退货单 '.$return['returnno'].' 吗,删除后无法恢复？\');">删除</a>　<a href="/s.php?module=service&action=updateReturn&returnid='.$return['returnid'].'">修改</a></td></tr>';
			}
			$rtn['panel'].='</tbody></table>';
			$rtn['count']=' ('.$rtn['num'].')';
		}else{
			$rtn['off']=' disabled';
		}
		return $rtn;
	}
	// 换货
	function relatedExchange(){
		return array('off'=>' disabled', 'num'=>0);
	}
	// 评论、收藏、转发
	function relatedWebsite(){
		return array('off'=>' disabled', 'num'=>0);
	}
	// 客户服务
	function relatedService($r){
		if($r['customerid']>0){
			//$r['condition'].=" AND `quoting`.customerid='".$r['customerid']."'";
		}
	$service=$this->DB->query("
	    SELECT `customer`.*,r.relationshipid,r.relationshiptypeid,r.questionnaireid,t.title AS ttitle,t.entitle AS tentitle,a.country AS acountry,a.encountry AS aencountry,d.title AS domain
		FROM  `customerrelationship`			AS	r	
		LEFT JOIN `customer`     	ON(r.customerid=`customer`.customerid)
		LEFT JOIN `customertype`						AS	t		ON(r.relationshiptypeid=t.typeid)
		 LEFT JOIN `region`									AS	a		ON(a.regionid=`customer`.regionid)
		 LEFT JOIN `customerdomain`				AS	d		ON (d.customerdomainid=`customer`.customerdomainid)
		WHERE `customer`.customerid='".$r['customerid']."'
	");
$rtn['num']=$this->DB->numRows();
if($rtn['num']){
	while($services=$this->DB->fetchArray($service)){
		if($services['cntitle']!=''){
			$title=$services['title'].'('.$services['cntitle'].')';
		}else{
			$title=$services['title'];
		}
	 $item=$this->customerchoosers(array('questionnaireid'=>$services['questionnaireid'],'customerid'=>$r['customerid'],'relationshipid'=>$services['relationshipid']));
$rtn['panel'].=<<<EOF
<table class="hundred">
<thead>
<tr>
<th><span class="right"><span class="plus hand" onclick="$('#service{$services['relationshipid']}').toggle('fast');$(this).toggleClass('minus');"></span></span>客户名称：{$title}　客户关系类型：{$services['ttitle']}({$services['tentitle']})　国家：{$services['acountry']}({$services['aencountry']})　行业：{$services['domain']}　主要联系人：{$services['linkman']}　电话：{$services['telephone']}　</th>
</tr>
</thead>
<tbody id="service{$services['relationshipid']}" style="display:none">
<tr><th>与客户<span class="red">{$title}</span>的客户关系调查表</th></tr>
		{$item}
</tbody>
</table>
EOF;
	}
	$rtn['panel'] .= '</tbody></table>';
	$rtn['count'] = ' ('.$rtn['num'].')';
}else{
	$rtn['off']='disabled';
}
return $rtn;
	}
	//
	function customerchoosers($b){
		if($b['questionnaireid']==''){
			$b['questionnaireid']=1;
		}
     $sql=$this->DB->query("SELECT q.*,`questionitem`.title AS qtitle ,`questionitem`.chooseid AS chooseid,`questionitem`.itemid
	                        FROM `questionnaire` AS q
							LEFT JOIN `questionitem`  ON(`questionitem`.questionnaireid=q.id)
							 WHERE q.id='".$b['questionnaireid']."'");
	if($num=$this->DB->numRows()){
        $i=1;
		while($views=$this->DB->fetchArray($sql)){
       
           $titles=$views['title'];
			$type=$views['ctitle'].'('.$views['centitle'].')';
			$choose=$this->questionchooser(array('questionnaireid'=>$b['questionnaireid'],'chooserid'=>$views['chooseid'],'itemid'=>$views['itemid'],'customerid'=>$b['customerid'],'relationshipid'=>$b['relationshipid']));
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
	return $item;
	}
	
	//
	function relatedComment($g){
		if($g['condition'] == ''){
			$g['condition'] = '1=1';
		}
		if($g['commentby'] == ''){
			$g['commentby'] = 'comment.dateline';
		}
		if($g['direction'] == ''){
			$g['direction'] = 'DESC';
		}
		if($g['limit'] == ''){
			$g['limit'] = '0, 100';
		}

		$comments = $this->DB->query("
			SELECT *
			FROM `comment`
			WHERE ".$g['condition']."
			ORDER BY ".$g['commentby']." ".$g['direction']."
			LIMIT ".$g['limit']."
		");
		$rtn['num'] = $this->DB->numRows();
		if($rtn['num']){
$th = <<<EOF
<form action="/{$this->vars['index_file']}/comment&action=publish"  name="name" method="post">
<input type="hidden" name="action" value="comment">
<input type="hidden" name="do" value="publish">
<input type="hidden" name="rt" value="product">
EOF;
			$rtn['panel'] = '<table cellspacing="5" align="center"><tr><td colspan="2">发布</td><td>用户名</td><td>评论</td><td align="center" width="120">日期</td><td align="center" class="small" width="30">选项</td></tr>';
			$stamp_cache = '';
			$i = 1;
			$commentby = 'dateline';
			while($comment = $this->DB->fetchArray($comments)){
				if($commentby == 'dateline'){
					$stamp_title = $this->get_date_stamp($comment['dateline']);
					if($stamp_title != $stamp_cache){
						$rtn['panel'] .= '<tr><td colspan="6" class="timestamp">'.$stamp_title.'</td></tr>';
						$stamp_cache = $stamp_title;
					}
				}

				$publish = $this->iif($comment['publish']==1, '<font color="green">是</font>', '<font color="red">否</font>');
				$rtn['panel'] .= '<tr class="odd"><td valign="top"><input type="hidden" name="ids[]" value="'.$comment['commentid'].'" valign="top" /><input type="checkbox" name="commentid[]" value="'.$comment['commentid'].'"'.$this->iif($comment['publish'] == 1, ' checked', '').' class="nostyle" /></td><td valign="top">'.$publish.'</td><td valign="top">'.$comment['name'].'</td><td>'.html_entity_decode($comment['ctext']).'</td><td align="center" class="small" valign="top">'.date('Y-m-d H:i:s', $comment['dateline']).'</td><td align="center" class="small" valign="top"><a href="/comment&action=remove&commentid='.$comment['commentid'].'">删</a> <a href="/comment&action=update&commentid='.$comment['commentid'].'" target="_blank">改</a></td></tr>';
				$i++;
				$productid = $comment['productid'];
			}
			$rtn['panel'] .= '<tr><td colspan="6" align="center"><input type="submit" value="   保存   " accesskey="s">		<input type="reset" value="   重置   "></td></tr><input type="hidden" name="productid" value="'.$productid.'"></form></table>';
			$rtn['count'] = ' ('.$rtn['num'].')';
		}else{
			$rtn['off'] = ' disabled';
		}
		return $rtn;
	}
	/*
	*		sth. related with stock
	*		@param array(materialid,stockid,batchdid,orderby,direction,limit)
	*		return html-table
	*/
	function relatedStock($r){
		if($r['materialid']>0){
			$condition.=' AND `stockitem`.materialid='.$r['materialid'];
		}
		if($r['stockid']>0){
			$condition.=' AND `stockitem`.stockid='.$r['stockid'];
		}
		if($r['batchdid']>0){
			$condition.=' AND `stockitem`.batchdid='.$r['batchdid'];
		}
		if($r['orderby']==''){
			$r['orderby']='`stockitem`.modified';
		}
		if($r['direction']==''){
			$r['direction']='DESC';
		}
		if($r['limit']==''){
			$r['limit']='0, 100';
		}
		$stocks=$this->DB->query("
			SELECT stockitem.stockitemid,stockitem.quantity,
				material.materialid,material.title,material.materialno,material.standard,
				stock.stockid,stock.stockno,
				batch.batchid,batch.batchno,
				barcode.barcodeid,barcode.barcode
			FROM `stockitem`
			LEFT JOIN `material` ON (material.materialid=stockitem.materialid)
			LEFT JOIN `stock` ON (stock.stockid=stockitem.stockid)
			LEFT JOIN `batch` ON (batch.batchid=stockitem.batchid)
			LEFT JOIN `barcode` ON (barcode.barcodeid=stockitem.barcodeid)
			WHERE stockitem.quantity<>0 AND stockitem.killed=0 ".$condition."
			ORDER BY ".$r['orderby']." ".$r['direction']."
			LIMIT ".$r['limit']."
		");
		$rtn['num']=$this->DB->numRows();
		if($rtn['num']){
			$i=1;
			$rtn['panel']='<table class="hundred"><thead><tr><th>编号</th><th>库位号</th><th>物资</th><th>批次号</th><th>条码</th><th>当前数量</th></tr></thead><tbody>';
			while($stock=$this->DB->fetchArray($stocks)){
				$rtn['panel'].='<tr class="'.$this->iif($this->rotate(),'odd','even').'">
					<td>'.$i.'</td>
					<td><a href="/s.php?module=stock&action=view&stockid='.$stock['stockid'].'">'.$stock['stockno'].'</a></td>
					<td>'.$stock['materialno'].'　<a href="/s.php?module=material&action=view&materialid='.$stock['materialid'].'">'.$stock['title'].'　'.$stock['standard'].'</a></td>
					<td><a href="/s.php?module=batch&action=view&batchid='.$stock['batchid'].'">'.$stock['batchno'].'</a></td>
					<td><a href="/s.php?module=barcode&action=view&barcodeid='.$stock['barcodeid'].'">'.$stock['barcode'].'</a></td>
					<td>'.$stock['quantity'].'</td></tr>';
				$i++;
			}
			$rtn['panel'].='</tbody></table>';
			$rtn['count']=' ('.$rtn['num'].')';
		}else{
			$rtn['off']=' disabled';
		}
		return $rtn;
	}
	/*
	*		sth. related with barcode
	*		@param array(materialid,batchid,supplierid,orderby,direction,limit)
	*		return html-table
	*/
	function relatedBarcode($r){
		if($r['materialid']>0){
			$condition.=' AND `barcode`.materialid='.$r['materialid'];
		}
		if($r['batchid']>0){
			$condition.=' AND `batch`.batchid='.$r['batchid'];
		}
		if($r['itemid']>0){
			$condition.=' AND mv.itemid='.$r['itemid'];
		}
		if($r['supplierid']>0){
			$r['condition'].=" AND `barcode`.supplierid='".$r['supplierid']."'";
		}
		if($r['orderby']==''){
			$r['orderby']='`barcode`.created';
		}
		if($r['direction']==''){
			$r['direction']='DESC';
		}
		if($r['limit']==''){
			$r['limit']='0, 100';
		}
		$barcodes=$this->DB->query("
			SELECT `barcode`.*,
				`supplier`.supplierno,`supplier`.supplierid,`supplier`.title AS supplier,
				`batch`.batchno,
				mv.itemid
			FROM `barcode`
			LEFT JOIN `supplier` ON (supplier.supplierid=`barcode`.supplierid)
			LEFT JOIN `batch` ON (`batch`.batchid=`barcode`.batchid)
			LEFT JOIN `materialversion` AS mv ON (mv.versionid=`barcode`.versionid)
			WHERE `barcode`.parentid=0 AND `barcode`.quantity<>0 AND scraped=0 ".$condition."
			ORDER BY ".$r['orderby']." ".$r['direction']."
			LIMIT ".$r['limit']."
		");
		$rtn['num']=$this->DB->numRows();
		if($rtn['num']){
			$rtn['panel']='<table class="hundred"><thead><tr><th>序号</th><th>条码</th><th>批次</th><th>供应商</th><th>总数</th><th>良品</th><th>不良品</th><th>收货时间</th></tr></thead><tbody>';
			$i=1;
			while($barcode=$this->DB->fetchArray($barcodes)){
				$barcode['arrivalTime']=date('Y-m-d',$barcode['arrivalTime']);
				$rtn['panel'].='<tr class='.$this->iif($this->rotate(),'odd','even').'><td>'.$i.'</td>
				<td><a href="/s.php?module=barcode&action=view&barcodeid='.$barcode['barcodeid'].'">'.$barcode['barcode'].'</td>
				<td><a href="/s.php?module=batch&action=view&batchid='.$barcode['batchid'].'">'.$barcode['batchno'].'</a></td>
				<td><a href="/s.php?module=supplier&action=view&supplierid='.$barcode['supplierid'].'">'.$barcode['supplier'].'</a></td>
				<td>'.$barcode['quantity'].'</td>
				<td>'.$barcode['qualified'].'</td>
				<td>'.$barcode['disqualified'].'</td>
				<td>'.$barcode['arrivalTime'].'</td></tr>';
				$i++;
			}
			$rtn['panel'].='</tbody></table>';
			$rtn['count']=' ('.$rtn['num'].')';
		}else{
			$rtn['off']=' disabled';
		}
		return $rtn;
	}
	/*
	*		sth. related with batch
	*		@param array(materialid,barcodeid,supplierid,orderby,direction,limit)
	*		return html-table
	*/
	function relatedBatch($r){
		if($r['materialid']>0){
			$condition.=' AND `batch`.materialid='.$r['materialid'];
		}
		if($r['barcodeid']>0){
			$condition.=' AND `batch`.barcodeid='.$r['barcodeid'];
		}
		if($r['itemid']>0){
			$condition.=' AND mv.itemid='.$r['itemid'];
		}
		if($r['supplierid']>0){
			$r['condition'].=" AND `batch`.supplierid='".$r['supplierid']."'";
		}
		if($r['orderby']==''){
			$r['orderby']='`batch`.created';
		}
		if($r['direction']==''){
			$r['direction']='DESC';
		}
		if($r['limit']==''){
			$r['limit']='0, 100';
		}
		$batchs=$this->DB->query("
			SELECT `batch`.*,
				mv.itemid
			FROM `batch`
			LEFT JOIN `materialversion` AS mv ON (mv.versionid=batch.versionid)
			WHERE `batch`.killed=0 AND `batch`.quantity<>0 ".$condition."
			ORDER BY ".$r['orderby']." ".$r['direction']."
			LIMIT ".$r['limit']."
		");
		if($rtn['num']=$this->DB->numRows()){
			$index=1;
			$rtn['panel']='<table class="hundred"><thead><tr><th width="30">ID</th><th>批次</th><th>数量</th><th>良品数</th><th>不良品数</th><th>收货时间</th><th width="100"></th></tr></thead><tbody>';
			while($batch=$this->DB->fetchArray($batchs)){
				if($batch['quantity']==0){
					$operate='<a href="/s.php?module=batch&action-=remove&batchid='.$batch['batchid'].'"></a>';
				}
				$rtn['panel'].='<tr class="'.$this->rotateLine().'">
				<td>'.$index.'</td>
				<td><a href="/s.php?module=batch&action=view&batchid='.$batch['batchid'].'">'.$batch['batchno'].'</a></td>
				<td>'.$batch['quantity'].'</td>
				<td>'.$batch['qualified'].'</td>
				<td>'.$batch['disqualified'].'</td>
				<td>'.date('Y-m-d',$batch['dateline']).'</td>
				<td><a href="/s.php?module=batch&action=kill&batchid='.$batch['batchid'].'">删除</a></td></tr>';
				$index++;
			}
			$rtn['panel'].='</tbody></table>';
			$rtn['count']=' ('.$rtn['num'].')';
		}else{
			$rtn['off']=' disabled';
		}
		return $rtn;
	}
	/*
	*		sth. related with inbound
	*		@param array(materialid,orderby,direction,limit)
	*		return html-table
	*/
	function relatedInbound($r){
		$inboundStatus=array(array('id'=>0,'title'=>'待入库'),array('id'=>1,'title'=>'部分入库'),array('id'=>2,'title'=>'入库完成'));
		if($r['materialid']>0){
			$condition.=' AND pii.materialid='.$r['materialid'];
		}
		if($r['taskid']>0){
			$items=$this->DB->query("
				SELECT itemid
				FROM taskitem
				WHERE taskid='".$r['taskid']."'
			");
			if($this->DB->numRows()){
				while($item=$this->DB->fetchArray($items)){
					$id[]=$item['itemid'];
				}
				$condition.=" AND pii.taskitemid IN (".implode(',', $id).")";
			}else{
				$condition.=" AND pii.taskitemid=0";
			}
		}
		if($r['itemid']>0){ // 物资明细
			$condition.=' AND pii.materialitemid='.$r['itemid'];
		}
		if($r['sampleid']>0){
			$condition.=' AND i.sampleid='.$r['sampleid'];
		}
		if($r['orderid']>0){
			$condition.=' AND i.orderid='.$r['orderid'];
		}
		if($r['orderby']==''){
			$r['orderby']='i.created';
		}
		if($r['direction']==''){
			$r['direction']='DESC';
		}
		if($r['limit']==''){
			$r['limit']='0, 100';
		}
		if($condition==''){
			return false;
		}
		$inbounds=$this->DB->query("
			SELECT DISTINCT i.*
			FROM inbound AS i
			LEFT JOIN preinbounditem AS pii ON (pii.mid=i.inboundid)
			WHERE i.killed=0 AND pii.killed=0 AND pii.module='inbound' ".$condition."
			ORDER BY i.created DESC
		");
		$rtn['num']=$this->DB->numRows();
		if($rtn['num']){
			$index=0;
			while($inbound=$this->DB->fetchArray($inbounds)){
				$inbound['created']=date('Y-m-d H:m',$inbound['created']);
				$confirm=$this->confirm($inbound['ifConfirm']);
				$verify=$this->verify($inbound['ifVerify']);
				$type=$this->DB->query("SELECT title FROM inorouttype WHERE typeid=".$inbound['typeid']." LIMIT 0,1");
				foreach($inboundStatus as $key => $val){
					if($inbound['ifComplete']==$val['id']){
						$status=$val['title'];
					}
				}

				$title='';
				if($inbound['inboundno']!='')$title.='　<span class="small">入库单号：</span><a href="/s.php?module=inbound&action=view&inboundid='.$inbound['inboundid'].'">'.$inbound['inboundno'].'</a>'.' [<span class="gray">'.$status.'</span>]';
				if($inbound['typeid']>0)$title.='　<span class="small">入库类型：</span>'.$type['title'];
				if($inbound['applicant']!='')$title.='　<span class="small">申请人：</span>'.$inbound['applicant'];
				if($inbound['created']!='')$title.='　<span class="small">申请时间：</span>'.$inbound['created'];
				if($inbound['purpose']!='')$title.='　<span class="small">用途：</span>'.$inbound['purpose'];
				if($inbound['remark']!='')$title.='　<span class="small">备注：</span>'.$inbound['remark'];
				$itemtr='';
				$preitems=$this->DB->query("
					SELECT pii.*,
						mv.title AS version
					FROM preinbounditem AS pii
					LEFT JOIN `inbound` AS i ON (i.inboundid=pii.mid)
					LEFT JOIN `material` AS m ON (m.materialid=pii.materialid)
					LEFT JOIN `materialversion` AS mv ON (mv.versionid=pii.versionid)
					WHERE pii.killed=0 AND pii.module='inbound' AND pii.mid='".$inbound['inboundid']."' $condition
					ORDER BY pii.materialitemid DESC,m.materialno ASC
				");
				if($this->DB->numRows()){
					$i=1;
					$itemtr='<tr class="center even bold"><td width="16">ID</td>'.$this->iif($inbound['workcenterid']>0,'<td width="100">任务单号</td>','').'<td width="350" colspan=2>物资</td><td width="60">物资版本</td><td width="90">良品数/单位</td><td width="90">不良品数/单位</td><td width="80">批号时间</td><td width="130">备注</td><td width="60"></td></tr>';
					while($preitem=$this->DB->fetchArray($preitems)){
						$inoperate='';
						$materialInfo=$this->getMaterial(array('materialid'=>$preitem['materialid'],'itemid'=>$preitem['materialitemid']));
						$inboundItem=$this->DB->queryFirst("SELECT SUM(quantity) AS quantity FROM inbounditem WHERE killed=0 AND materialid='".$preitem['materialid']."' AND inboundid='".$inbound['inboundid']."' AND preitemid='".$preitem['itemid']."'");
						if($inbound['killed']==0 AND $inbound['ifConfirm']==1 AND $inbound['ifVerify']==1){
							if($inboundItem['quantity']==$preitem['qualified'] OR ($inboundItem['quantity']==$preitem['disqualified'] AND $preitem['disqualified']>0)){
								$inoperate='入库完成';
							}elseif($inboundItem['quantity'] AND ($inboundItem['quantity']<$preitem['qualified'] OR ($inboundItem['quantity']<$preitem['disqualified'] AND $preitem['disqualified']>0))){
								$inoperate='<a href="/s.php?module=inbound&action=in&inboundid='.$inbound['inboundid'].'&itemid='.$preitem['itemid'].'">继续接收</a>';
							}else{
								$inoperate='<a href="/s.php?module=inbound&action=in&inboundid='.$inbound['inboundid'].'&itemid='.$preitem['itemid'].'">接收</a>';
							}
						}
						$taskitem=$this->DB->queryFirst("
							SELECT d.taskid,d.taskno
							FROM taskitem AS t
							LEFT JOIN task AS d ON (d.taskid=t.taskid)
							WHERE t.killed=0 AND d.killed=0 AND t.itemid='".$preitem['taskitemid']."'
						");
						$itemtr.='<tr class="'.$this->iif($this->rotate(), 'odd', 'even').' small">
							<td>'.$i.'</td>
							'.$this->iif($inbound['workcenterid']>0,'<td><a href="/s.php?module=task&action=view&taskid='.$taskitem['taskid'].'">'.$taskitem['taskno'].'</td>','').'
							<td colspan=2><a href="'.$materialInfo['url'].'">'.$materialInfo['no'].'</a>　'.$materialInfo['material'].'　'.$materialInfo['standard'].'</td>
							<td>'.$preitem['version'].'</td>
							<td>'.$preitem['qualified'].$materialInfo['unit'].'</td>
							<td>'.$preitem['disqualified'].$materialInfo['unit'].'</td>
							<td>'.date('Y-m-d',$preitem['dateline']).'</td>
							<td>'.$preitem['remark'].'</td>
							<td>'.$inoperate.'</td></tr>';
						$i++;
					}
				}
				$items=$this->DB->query("
					SELECT ii.itemid,ii.batchid,ii.barcodeid,ii.materialid,ii.materialitemid,ii.versionid,ii.created,
						bt.batchno,
						bc.barcode,
						mv.title AS version,
						mem.realname AS creator
					FROM inbounditem AS ii
					LEFT JOIN inbound AS i ON (i.inboundid=ii.inboundid)
					LEFT JOIN batch AS bt ON (ii.batchid=bt.batchid)
					LEFT JOIN barcode AS bc ON (bc.barcodeid=ii.barcodeid)
					LEFT JOIN material AS m ON (m.materialid=ii.materialid)
					LEFT JOIN materialversion AS mv ON (mv.versionid=ii.versionid)
					LEFT JOIN preinbounditem AS pii ON (ii.preitemid=pii.itemid)
					LEFT JOIN member AS mem ON (mem.userid=ii.creator)
					WHERE ii.killed=0 AND ii.inboundid='".$inbound['inboundid']."' $condition
					ORDER BY ii.materialitemid DESC,m.materialno ASC
				");
				if($this->DB->numRows()){
					$i=1;
					$itemtr.='<tr><td colspan=16 class="bold middle">已入库明细</td></tr><tr class="even center bold"><td width="15">ID</td>'.$this->iif($inbound['workcenterid']>0,'<td></td>','').'<td width="200">条码</td><td width="80">批次编号</td><td width="80">物资编号</td><td width="100">批号时间</td><td width="60">签收人</td><td width="70">已入库数量</td><td width="150">入库时间</td><td>操作</td></tr>';
					while($item=$this->DB->fetchArray($items)){
						$in=$this->DB->queryFirst("SELECT SUM(quantity) AS count FROM inbounditem WHERE killed=0 AND itemid='".$item['itemid']."'");
						$batch=$this->DB->queryFirst("SELECT * FROM batch WHERE batchid='".$item['batchid']."'");

						$materialInfo=$this->getMaterial(array('materialid'=>$item['materialid'],'itemid'=>$item['materialitemid']));
						$itemtr.='<tr class="'.$this->iif($this->rotate(), 'odd', 'even').' small">
							<td>'.$i.'</td>
							'.$this->iif($inbound['workcenterid']>0,'<td></td>','').'
							<td><a href="/s.php?module=barcode&action=view&barcodeid='.$item['barcodeid'].'">'.$item['barcode'].'</a></td>
							<td><a href="/s.php?module=batch&action=view&batchid='.$item['batchid'].'">'.$item['batchno'].'</a></td>
							<td><a href="'.$materialInfo['url'].'">'.$materialInfo['no'].'</a></td>
							<td>'.date('Y-m-d',$batch['dateline']).'</td>
							<td>'.$item['creator'].'</td>
							<td>'.$this->iif($in['count'],$in['count'],0).$item['unit'].'</td>
							<td>'.date('Y-m-d H:i:s',$item['created']).'</td>
							<td><a target="_blank" href="/s.php?module=inbound&action=cancelIn&type=item&itemid='.$item['itemid'].'" onclick="return confirm(\'你确定要对该条明细 '.$item['no'].' 进行取消入库吗？\');">取消入库</a></td></tr>';
						$i++;
					}
				}
				$index++;
				$options=$confirm.'　'.$verify;
				$rtn['panel'].=<<<EOF
<table class="hundred">
<thead>
<tr><th colspan="16"><span class="right middle bold">{$options} <span class="plus"></span></span>	<span class="normal">[<b>{$index}</b>]</span>{$title}</th></tr>
</thead>
<tbody id="inbound{$inbound['inboundid']}" style="display:none">
{$itemtr}
</tbody>
</table>
EOF;
			}
			$rtn['count']=' ('.$rtn['num'].')';
		}else{
			$rtn['off']=' disabled';
		}
		return $rtn;
	}
	/*
	*		sth. related with outbound
	*		@param array(materialid,orderby,direction,limit)
	*		return html-table
	*/
	function relatedOutbound($r){
		$outboundStatus=array(array('id'=>0,'title'=>'待出库'),array('id'=>1,'title'=>'部分出库'),array('id'=>2,'title'=>'出库完成'));
		if($r['materialid']>0){
			$condition.=' AND poi.materialid='.$r['materialid'];
		}
		if($r['orderid']>0){
			$condition.=' AND o.orderid='.$r['orderid'];
		}
		if($r['sampleid']>0){
			$condition.=' AND o.sampleid='.$r['sampleid'];
		}
		if($r['taskid']>0){
			$items=$this->DB->query("
				SELECT itemid
				FROM taskitem
				WHERE taskid='".$r['taskid']."'
			");
			if($this->DB->numRows()){
				while($item=$this->DB->fetchArray($items)){
					$id[]=$item['itemid'];
				}
				$condition.=" AND poi.taskitemid IN (".implode(',', $id).")";
			}else{
				$condition.=" AND poi.taskitemid=0";
			}
		}
		if($r['itemid']>0){
			$condition.=' AND poi.materialitemid='.$r['itemid'];
		}
		if($r['orderby']==''){
			$r['orderby']='o.created';
		}
		if($r['direction']==''){
			$r['direction']='DESC';
		}
		if($r['limit']==''){
			$r['limit']='0, 100';
		}
		if($condition==''){
			return false;
		}
		$outbounds=$this->DB->query("
			SELECT DISTINCT o.*,t.title AS type
			FROM `outbound` AS o
			LEFT JOIN `inorouttype` AS t ON (t.typeid=o.typeid)
			LEFT JOIN preoutbounditem AS poi ON (poi.mid=o.outboundid)
			WHERE o.killed=0 AND poi.killed=0 ".$condition."
			ORDER BY ".$r['orderby']." ".$r['direction']."
			LIMIT ".$r['limit']."
		");
		$rtn['num']=$this->DB->numRows();
		if($rtn['num']){
			$index=0;
			while($outbound=$this->DB->fetchArray($outbounds)){
				$outbound['created']=date('Y-m-d H:m',$outbound['created']);
				$confirm=$this->confirm($outbound['ifConfirm']);
				$verify=$this->verify($outbound['ifVerify']);
				foreach($outboundStatus as $key => $val){
					if($outbound['ifComplete']==$val['id']){
						$status=$val['title'];
					}
				}
				$title='';
				if($outbound['outboundno']!='')$title.='　<span class="small">出库单号：</span><a href="/s.php?module=outbound&action=view&outboundid='.$outbound['outboundid'].'">'.$outbound['outboundno'].'</a>'.' [<span class="gray">'.$status.'</span>';
				if($outbound['typeid']>0)$title.='　<span class="small">出库类型：</span>'.$outbound['type'];
				if($outbound['applicant']!='')$title.='　<span class="small">申请人：</span>'.$outbound['applicant'];
				if($outbound['created']!='')$title.='　<span class="small">申请时间：</span>'.$outbound['created'];
				if($outbound['purpose']!='')$title.='　<span class="small">用途：</span>'.$outbound['purpose'];
				if($outbound['remark']!='')$title.='　<span class="small">备注：</span>'.$outbound['remark'];
				/* 暂保留
				$stocks=unserialize($outbound['stocks']);
				foreach($stocks as $key=>$val){
					$stock=$this->DB->queryFirst("SELECT stockid,stockno FROM stock WHERE stockid='".$val['stockid']."'");
					$stockLine.='<a href="/s.php?module=stock&action=view&stockid='.$stock['stockid'].'">'.$stock['stockno'].'</a> : '.$val['quantity'].'； ';
				}*/

				$preitems=$this->DB->query("
					SELECT poi.*,
						mv.title AS version,mv.qualified AS store1,mv.disqualified AS store2
					FROM preoutbounditem AS poi
					LEFT JOIN `outbound` AS o ON (o.outboundid=poi.mid)
					LEFT JOIN `materialversion` AS mv ON (mv.versionid=poi.versionid)
					WHERE poi.killed=0 AND poi.module='outbound' AND poi.mid='".$outbound['outboundid']."' $condition
					ORDER BY poi.itemid ASC
				");
				if($this->DB->numRows()){
					$i=1;
					$itemtr='<tr class="center bold"><td width="30">ID</td>'.$this->iif($outbound['workcenterid']>0,'<td width="100">任务单号</td>','').'<td colspan=2>物资</td><td width="30">版本</td><td width="60">是否良品</td><td width="60">库存数量</td><td width="80">数量/单位</td><td width="80">损耗数/单位</td><td width="90">申请出库时间</td><td width="80">已出库数量</td><td width="100">备注</td><td width="150"></td></tr>';
					while($preitem=$this->DB->fetchArray($preitems)){
						$preitem['outTime']=date('Y-m-d',$preitem['outTime']);
						$outOperate='';
						$counter=$this->DB->queryFirst("SELECT SUM(outQuantity) AS count FROM outbounditem WHERE killed=0 AND materialid='".$preitem['materialid']."' AND outboundid='".$outbound['outboundid']."' AND preitemid='".$preitem['itemid']."'");
						$action='out';
						$s='出库';
						if($outbound['ifVerify']==1){
							if($counter['count']==$preitem['quantity']+$preitem['wastage']){
								$outOperate='出库完成';
							}elseif($counter['count']==0){
								$outOperate='<a href="/s.php?module=outbound&action='.$action.'&outboundid='.$outbound['outboundid'].'&itemid='.$preitem['itemid'].'&materialid='.$preitem['materialid'].'">'.$s.'</a>';
							}else{
								$outOperate='<a href="/s.php?module=outbound&action='.$action.'&outboundid='.$outbound['outboundid'].'&itemid='.$preitem['itemid'].'&materialid='.$preitem['materialid'].'">继续出库</a>';
							}
						}
						$store=0;
						if($outbound['typeid']==3 OR $outbound['typeid']==8){
							$d=$this->iif($outbound['typeid']==3,' AND i.orderid='.$this->iif($outbound['orderid']>0,$outbound['orderid'],0),' AND i.sampleid='.$this->iif($outbound['sampleid']>0,$outbound['sampleid'],0));
							$versionQuantity=$this->DB->queryFirst("
								SELECT SUM(ii.quantity) AS quantity
								FROM inbounditem AS ii
								LEFT JOIN preinbounditem AS pii ON (pii.itemid=ii.preitemid)
								LEFT JOIN inbound AS i ON (i.inboundid=ii.inboundid)
								WHERE ii.killed=0 $d AND ii.materialid='".$preitem['materialid']."' AND pii.materialitemid='".$preitem['materialitemid']."'
								LIMIT 0,1
							");
							$store=$versionQuantity['quantity'];
						}else{
							if($preitem['ifQualified']==1){
								$qualifiedType='良品';
								$store=$preitem['store1'];
							}elseif($preitem['ifQualified']==0){
								$qualifiedType='不良品';
								$store=$preitem['store2'];
							}
						}

						$materialInfo=$this->getMaterial(array('materialid'=>$preitem['materialid'],'itemid'=>$preitem['materialitemid']));
						$taskitem=$this->DB->queryFirst("
							SELECT d.taskid,d.taskno
							FROM taskitem AS t
							LEFT JOIN task AS d ON (d.taskid=t.taskid)
							WHERE t.killed=0 AND d.killed=0 AND t.itemid='".$preitem['taskitemid']."'
						");
						$itemtr.='<tr class="'.$this->iif($this->rotate(),'odd','even').' small">
							<td>'.$i.'</td>
							'.$this->iif($outbound['workcenterid']>0,'<td><a href="/s.php?module=task&action=view&taskid='.$taskitem['taskid'].'">'.$taskitem['taskno'].'</td>','').'
							<td colspan=2><a href="'.$materialInfo['url'].'">'.$materialInfo['no'].'</a>　'.$materialInfo['title'].'　'.$materialInfo['standard'].'</td>
							<td>'.$preitem['version'].'</td>
							<td>'.$qualifiedType.'</td>
							<td>'.$store.$preitem['unit'].'</td>
							<td>'.$preitem['quantity'].$materialInfo['unit'].'</td>
							<td>'.$preitem['wastage'].$materialInfo['unit'].'</td>
							<td>'.$preitem['outTime'].'</td>
							<td>'.$this->iif($counter['count'],$counter['count'],0).'</td>
							<td>'.$preitem['remark'].'</td>
							<td>'.$outOperate.'</td></tr>';
						$i++;
					}
				}
				$outboundItems=$this->DB->query("
					SELECT oi.*,
						ba.batchno,
						bc.barcode,
						mem.realname AS creator
					FROM outbounditem AS oi
					LEFT JOIN `outbound` AS o ON (o.outboundid=oi.outboundid)
					LEFT JOIN `preoutbounditem` AS poi ON (poi.itemid=oi.preitemid)
					LEFT JOIN batch AS ba ON (ba.batchid=oi.batchid)
					LEFT JOIN barcode AS bc ON (bc.barcodeid=oi.barcodeid)
					LEFT JOIN member AS mem ON (mem.userid=oi.creator)
					WHERE oi.killed=0 AND oi.outboundid='".$outbound['outboundid']."' $condition
					ORDER BY oi.preitemid ASC
				");
				if($this->DB->numRows()){
					$i=1;
					$itemtr.='<tr><td colspan=13 class="bold middle">已出库明细</td></tr><tr class="even center bold"><td width="30">ID</td>'.$this->iif($outbound['workcenterid']>0,'<td></td>','').'<td colspan=2>物资</td><td width="120">批次编号</td><td width="170">条码</td><td width="70">申请数量</td><td width="60">实际数量</td><td width="70">出库人</td><td width="90">申请出库时间</td><td width="90">实际出库时间</td><td></td><td width="120"></td></tr>';
					while($outbounditem=$this->DB->fetchArray($outboundItems)){
						$outbounditem['applyTime']=date('Y-m-d',$outbounditem['applyTime']);
						$outbounditem['actualTime']=date('Y-m-d',$outbounditem['actualTime']);
						$materialInfo=$this->getMaterial(array('materialid'=>$outbounditem['materialid'],'itemid'=>$outbounditem['materialitemid']));
						$itemtr.='<tr class="'.$this->iif($this->rotate(),'odd','even').' small">
							<td>'.$i.'</td>
							'.$this->iif($outbound['workcenterid']>0,'<td></td>','').'
							<td colspan=2><a href="'.$materialInfo['url'].'">'.$materialInfo['no'].'</a>　'.$materialInfo['material'].'　'.$materialInfo['standard'].'</td>							
							<td><a href="/s.php?module=batch&action=view&batchid='.$outbounditem['batchid'].'">'.$outbounditem['batchno'].'</a></td>
							<td><a href="/s.php?module=barcode&action=view&barcodeid='.$outbounditem['barcodeid'].'">'.$outbounditem['barcode'].'</a></td>
							
							<td>'.$outbounditem['applyQuantity'].$materialInfo['unit'].'</td>
							<td>'.$outbounditem['outQuantity'].$materialInfo['unit'].'</td>
							<td>'.$outbounditem['creator'].'</td>
							<td>'.$outbounditem['applyTime'].'</td>
							<td>'.$outbounditem['actualTime'].'</td><td></td>
							<td><a href="/s.php?module=batch&action=view&batchid='.$outbounditem['batchid'].'">查看详情</a>　<a target="_blank" href="/s.php?module=outbound&action=cancelOut&type=item&itemid='.$outbounditem['itemid'].'" onclick="return confirm(\'你确定要对该条明细 '.$materialInfo['no'].' 进行取消出库吗？\');">取消出库</a></td></tr>';
							$quantity+=$outbounditem['applyQuantity'];
						$i++;
					}
				}
				$index++;
				$options=$confirm.'　'.$verify;
				$rtn['panel'].=<<<EOF
<table class="hundred">
<thead>
<tr><th colspan="13"><span class="right middle bold">{$options} <span class="plus"></span></span>	<span class="normal">[<b>{$index}</b>]</span>{$title}</th></tr>
</thead>
<tbody id="outbound{$outbound['outboundid']}" style="display:none">
{$itemtr}
</tbody>
</table>
EOF;
			}
			$rtn['panel'].='</tbody></table>';
			$rtn['count']=' ('.$rtn['num'].')';
		}else{
			$rtn['off']=' disabled';
		}
		return $rtn;
	}
	// 相关的物料收发卡
	function relatedInoutrecord($b){
		if($b['materialid']<0){
			return false;
		}
		if($b['condition']){
			$condition=' AND '.$b['condition'];
		}
		if($b['itemid']>0){
			$condition.=' AND ior.materialitemid='.$b['itemid'];
		}
		$records=$this->DB->query("
			SELECT ior.*,
				m.materialno,m.title AS material,m.standard,
				mv.title AS version,mv.quantity,mv.qualified AS totalQualified,mv.disqualified AS totalDisqualified
			FROM inoutrecord AS ior
			LEFT JOIN material AS m ON (ior.materialid=m.materialid)
			LEFT JOIN materialversion AS mv ON (mv.versionid=ior.versionid)
			WHERE ior.killed=0 AND ior.materialid='".$b['materialid']."' $condition
			ORDER BY created ASC
		");
		$rtn['num']=$this->DB->numRows();
		if($rtn['num']){
			$rtn['panel']='<table class="hundred"><thead><tr><th width="15">ID</th><th>相关单号</th><th>条码</th><th>批次</th><th>操作人</th><th>入/出</th><th>良品数</th><th>良品结存</th><th>不良品数</th><th>不良品结存</th><th>收发时间</th><tr></thead>';
			$i=1;
			while($record=$this->DB->fetchArray($records)){
				$no='';
				$qualified=$this->iif($record['qualified']>0,$record['addition'].$record['qualified'],0);
				$disqualified=$this->iif($record['disqualified']>0,$record['addition'].$record['disqualified'],0);
				if($record['module']=='inbound'){
					$inItem=$this->DB->queryFirst("
						SELECT ii.inboundid,ii.barcodeid,ii.batchid,
							i.inboundno,i.orderid,i.sampleid,
							bc.barcode,
							ba.batchno,
							m.realname AS creator
						FROM inbounditem AS ii
						LEFT JOIN inbound AS i ON (i.inboundid=ii.inboundid)
						LEFT JOIN barcode AS bc ON (bc.barcodeid=ii.barcodeid)
						LEFT JOIN batch AS ba ON (ba.batchid=ii.batchid)
						LEFT JOIN member AS m ON (m.userid=ii.creator)
						WHERE ii.killed=0 AND ii.itemid='".$record['itemid']."'
						LIMIT 0,1
					");
					if($inItem['orderid']>0){
						$order=$this->DB->queryFirst("SELECT orderid,orderno FROM `order` WHERE orderid=".$inItem['orderid']." LIMIT 0,1");
						$no='<a class="small gray" href="/s.php?module=order&action=view&orderid='.$order['orderid'].'">('.$order['orderno'].')</a>';
					}
					if($inItem['sampleid']>0){
						$sample=$this->DB->queryFirst("SELECT sampleid,sampleno FROM sample WHERE sampleid=".$inItem['sampleid']." LIMIT 0,1");
						$no='<a class="small gray" href="/s.php?module=order&action=view&orderid='.$order['orderid'].'">('.$sample['sampleno'].')</a>';
					}
					$rtn['panel'].='<tr class="odd"><td>'.$i.'</td>
						<td><a href="/s.php?module=inbound&action=view&inboundid='.$inItem['inboundid'].'">'.$inItem['inboundno'].'</a> '.$no.'</td>
						<td><a href="/s.php?module=barcode&action=view&barcodeid='.$inItem['barcodeid'].'">'.$inItem['barcode'].'</a></td>
						<td><a href="/s.php?module=batch&action=view&batchid='.$inItem['batchid'].'">'.$inItem['batchno'].'</a></td>
						<td>'.$inItem['creator'].'</td><td class="bold gray middle">入</td>';
				}elseif($record['module']=='outbound'){
					$outItem=$this->DB->queryFirst("
						SELECT oi.outboundid,oi.barcodeid,oi.batchid,
							o.outboundno,o.orderid,o.sampleid,
							bc.barcode,
							ba.batchno,
							m.realname AS creator
						FROM outbounditem AS oi
						LEFT JOIN outbound AS o ON (o.outboundid=oi.outboundid)
						LEFT JOIN barcode AS bc ON (bc.barcodeid=oi.barcodeid)
						LEFT JOIN batch AS ba ON (ba.batchid=oi.batchid)
						LEFT JOIN member AS m ON (m.userid=oi.creator)
						WHERE oi.killed=0 AND oi.itemid='".$record['itemid']."'
						LIMIT 0,1
					");
					if($outItem['orderid']>0){
						$order=$this->DB->queryFirst("SELECT orderid,orderno FROM `order` WHERE orderid=".$outItem['orderid']." LIMIT 0,1");
						$no='<a class="small gray" href="/s.php?module=order&action=view&orderid='.$order['orderid'].'">('.$order['orderno'].')</a>';
					}
					if($outItem['sampleid']>0){
						$sample=$this->DB->queryFirst("SELECT sampleid,sampleno FROM sample WHERE sampleid=".$outItem['sampleid']." LIMIT 0,1");
						$no='<a class="small gray" href="/s.php?module=order&action=view&orderid='.$order['orderid'].'">('.$sample['sampleno'].')</a>';
					}
					$rtn['panel'].='<tr class="even"><td>'.$i.'</td>
						<td><a href="/s.php?module=outbound&action=view&outboundid='.$outItem['outboundid'].'">'.$outItem['outboundno'].'</a> '.$no.'</td>
						<td><a href="/s.php?module=barcode&action=view&barcodeid='.$outItem['barcodeid'].'">'.$outItem['barcode'].'</a></td>
						<td><a href="/s.php?module=batch&action=view&batchid='.$outItem['batchid'].'">'.$outItem['batchno'].'</a></td>
						<td>'.$outItem['creator'].'</td><td class="bold gray middle">出</td>';
				}elseif($record['module']=='inventory'){
					$inventoryItem=$this->DB->queryFirst("
						SELECT invi.inventoryid,invi.barcodeid,invi.batchid,invi.difference,
							inv.inventoryno,
							bc.barcode,
							ba.batchno,
							m.realname AS creator
						FROM inventoryitem AS invi
						LEFT JOIN inventory AS inv ON (inv.inventoryid=invi.inventoryid)
						LEFT JOIN barcode AS bc ON (bc.barcodeid=invi.barcodeid)
						LEFT JOIN batch AS ba ON (ba.batchid=invi.batchid)
						LEFT JOIN member AS m ON (m.userid=invi.creator)
						WHERE invi.killed=0 AND invi.itemid='".$record['itemid']."'
						LIMIT 0,1					
					");
					if(!is_Array($inventoryItem['difference'])){
						if($inventoryItem['difference']>0){
							$diff = '盈';
						}else{
							$diff = '亏';
						}
					}else{
						if($inventoryItem['difference'][0]+$inventoryItem['difference'][1]){
							$diff = '盈';
						}else{
							$diff = '盈';
						}
					}
					$rtn['panel'].='<tr class="odd"><td>'.$i.'</td>
						<td><a href="/s.php?module=inbound&action=view&inboundid='.$inventoryItem['inventoryid'].'">'.$inventoryItem['inventoryno'].'</td>
						<td><a href="/s.php?module=barcode&action=view&barcodeid='.$inventoryItem['barcodeid'].'">'.$inventoryItem['barcode'].'</a></td>
						<td><a href="/s.php?module=batch&action=view&batchid='.$inventoryItem['batchid'].'">'.$inventoryItem['batchno'].'</a></td>
						<td>'.$inventoryItem['creator'].'</td><td class="bold gray middle">'.$diff.'</td>';				
				}
				
				$rtn['panel'].='<td>'.$qualified.'</td>
					<td class="bold red">'.$record['balance'].'</td>
					<td>'.$disqualified.'</td>
					<td class="bold red">'.$record['disbalance'].'</td>
					<td>'.date('Y-m-d',$record['dateline']).'</td>

				</tr>';
				$i++;
			}
			$rtn['panel'].='</tbody></table>';
			$rtn['count']=' ('.$rtn['num'].')';
		}else{
			$rtn['off']=' disabled';
		}
		return $rtn;
	}
	//
	function relatedFavorite($g){
		if($g['condition'] == ''){
			$g['condition'] = '1=1';
		}
		if($g['favoriteby'] == ''){
			$g['favoriteby'] = 'favorite.dateline';
		}
		if($g['direction'] == ''){
			$g['direction'] = 'DESC';
		}
		if($g['limit'] == ''){
			$g['limit'] = '0, 100';
		}
		$favorites = $this->DB->query("
			SELECT favorite.*,
				user.username
			FROM favorite
			LEFT JOIN user ON (user.userid = favorite.userid)
			WHERE ".$g['condition']."
			ORDER BY ".$g['favoriteby']." ".$g['direction']."
			LIMIT ".$g['limit']."
		");
		$rtn['num'] = $this->DB->numRows();
		if($rtn['num']){
			$rtn['panel'] = '<table cellspacing="5" align="center" width="100%"><tr class="solid bold"><td>ID</td><td>用户</td><td align="right">收藏时间</td><td align="right">选项</td></tr>';
			$favoriteby = 'dateline';
			$stamp_cache = '';
			while($favorite = $this->DB->fetchArray($favorites)){
				if($favoriteby == 'dateline'){
					$stamp_title = $this->get_date_stamp($favorite['dateline']);
					if($stamp_title != $stamp_cache){
						$rtn['panel'] .= '<tr><td colspan="4" class="timestamp">'.$stamp_title.'</td></tr>';
						$stamp_cache = $stamp_title;
					}
				}
				if($favoriteby == 'total'){
					$stamp_title = $this->get_total_stamp($favorite['total']);
					if($stamp_title != $stamp_cache){
						$rtn['panel'] .= '<tr><td colspan="4" class="timestamp">'.$stamp_title.'</td></tr>';
						$stamp_cache = $stamp_title;
					}
				}

				$rtn['panel'] .= '<tr class="product" onmouseover="this.style.backgroundColor=\'white\'" onmouseout="this.style.backgroundColor=\'\'"><td>'.$favorite['favoriteid'].'</td><td class="bold"><a href="/member&action=view&userid='.$favorite['userid'].'" target="_blank">'.$favorite['username'].'</a></td><td align="right">'.date('Y-m-d H:i:s', $favorite['dateline']).'</td><td align="right"><i>无</i></td></tr>';
			}
			$rtn['panel'] .= '</table>';
			$rtn['count'] = ' ('.$rtn['num'].')';
		}else{
			$rtn['off'] = ' disabled';
		}
		return $rtn;
	}

	//
	function relatedForward($g){
		if($g['condition'] == ''){
			$g['condition'] = '';
		}
		if($g['forwardby'] == ''){
			$g['forwardby'] = 'forward.dateline';
		}
		if($g['direction'] == ''){
			$g['direction'] = 'DESC';
		}
		if($g['limit'] == ''){
			$g['limit'] = '0, 100';
		}
		$forwards = $this->DB->query("
			SELECT forward.*,
				user.username
			FROM forward
			LEFT JOIN user ON (user.userid = forward.userid)
			WHERE hidden=0 AND ".$g['condition']."
			ORDER BY ".$g['forwardby']." ".$g['direction']."
			LIMIT ".$g['limit']."
		");
		$rtn['num'] = $this->DB->numRows();
		if($rtn['num']){
			$rtn['panel'] = '<table cellspacing="5" align="center" width="100%"><tr><td>ID</td><td>来自</td><td>收件人</td><td>主题</td><td align="right">转发时间</td><td align="right">选项</td></tr>';
			$forwardby = 'dateline';
			$stamp_cache = '';
			while($forward = $this->DB->fetchArray($forwards)){
				if($forwardby == 'dateline'){
					$stamp_title = $this->get_date_stamp($forward['dateline']);
					if($stamp_title != $stamp_cache){
						$rtn['panel'] .= '<tr><td colspan="6" class="timestamp">'.$stamp_title.'</td></tr>';
						$stamp_cache = $stamp_title;
					}
				}
				if($forwardby == 'total'){
					$stamp_title = $this->get_total_stamp($forward['total']);
					if($stamp_title != $stamp_cache){
						$rtn['panel'] .= '<tr><td colspan="6" class="timestamp">'.$stamp_title.'</td></tr>';
						$stamp_cache = $stamp_title;
					}
				}

				$rtn['panel'] .= '<tr class="odd"><td>'.$forward['forwardid'].'</td><td><a href="mailto: '.$forward['from_email'].'" target="_blank">'.$forward['from_name'].'</a></td><td><a href="mailto: '.$forward['to_email'].'">'.$forward['to_name'].'</a></td><td>'.$forward['subject'].'</td><td align="right">'.date('Y-m-d H:i', $forward['dateline']).'</td><td align="right"><i>无</i></td></tr>';
			}
			$rtn['panel'] .= '</table>';
			$rtn['count'] = ' ('.$rtn['num'].')';
		}else{
			$rtn['off'] = ' disabled';
		}
		return $rtn;
	}
	//
	function relatedMaterial($id){
		if($id<0){
			return false;
		}
		$material=$this->DB->queryFirst("
			SELECT m.*,
				c.parentlist,c.title AS category,
				un.title AS unit,
				b.title AS brand,
				pm.ifGeneral,
				p.title AS packing,
				mo.title AS origin,
				u.username AS creator,
				u.username AS modifier
			FROM material AS m
			LEFT JOIN category AS c ON (c.categoryid=m.categoryid)
			LEFT JOIN unit AS un ON (un.unitid=m.unitid)
			LEFT JOIN brand AS b ON (b.brandid=m.brandid)
			LEFT JOIN pmaterial AS pm ON (pm.materialid=m.materialid)
			LEFT JOIN packing AS p ON (p.packingid=m.packingid)
			LEFT JOIN materialorigin AS mo ON (mo.originid=m.originid)
			LEFT JOIN user AS u ON (u.userid=m.creator)
			LEFT JOIN user AS us ON (us.userid=m.modifier)
			WHERE m.materialid='".$id."'
			LIMIT 0,1
		");
		if($material){
			$categoryid=explode(',',$material['parentlist']);
      foreach($categoryid as $key=>$value){//获得物资分类
        if($value!=-1){
          $category=$this->DB->queryFirst("SELECT title FROM category WHERE categoryid='".$value."' LIMIT 0,1");
          if($category['title'])$material['category']=$category['title'].' '.$material['category'];
        }
      }
      if($material['ifPerBarcode']==1){
        $material['perBarcode']='是';
      }elseif($material['ifPerBarcode']==0){
        $material['perBarcode']='否';
      }
			if($material['inventoryType']=='choose'){
				$invetoryType='抽盘';
			}elseif($material['inventoryType']=='must'){
				$invetoryType='必盘';
			}
			if($material['ifGeneral']==1){
				$ifGeneral='是';
			}else{
				$ifGeneral='否';
			}
			$r.=<<<EOF
<table class="hundred">
<thead><tr><th colspan="4">物资信息</th></tr></thead>
<tbody>
<tr class="odd">
<td width="100">物资编号：</td><td width="450">{$material['materialno']}</td>
<td width="100">物资名称：</td><td width="450">{$material['title']}</td>
</tr>
<tr class="odd">
<td width="100">物资规格：</td><td width="450">{$material['standard']}</td>
<td width="100">物资分类：</td><td width="450">{$material['category']}</td>
</tr>
<tr class="even">
<td>品　　牌：</td><td>{$material['brand']}</td>
<td>物资单位：</td><td>{$material['unit']}</td>
</tr>
<tr class="odd">
<td>物资来源：</td><td>{$material['origin']}　　　　　　　　是否是通用物料：{$ifGeneral}</td>
<td>盘点类型：</td><td>{$invetoryType}</td>
</tr>
<tr class="even">
<td>包　　装：</td><td>{$material['packing']}</td>
<td>总　　数：</td><td>{$material['quantity']}</td>
</tr>
<tr class="odd">
<td>良品数量：</td><td>{$material['qualified']}</td>
<td>不良品数量：</td><td>{$material['disqualified']}</td>
</tr>
<tr class="even">
<td>最大峰值：</td><td>{$material['max']}</td>
<td>最小报警值：</td><td>{$material['min']}</td>
</tr>
<tr class="odd">
<td>临界报警值：</td><td>{$material['criticalNumber']}</td>
<td>允 差 值：</td><td>{$tolerance}</td>
</tr>
<tr class="even">
<td>是否为该物资分配流水号条码：</td><td>{$material['perBarcode']}</td>
<td>备　　注：</td><td>{$material['remark']}</td>
</tr>
</tbody>
</table>
EOF;
		}
		return $r;
	}
	//
	function relatedSupplierMaterial($b){
		if($b['materialid']!=''){
			$condition=' AND sm.materialid='.$b['materialid'];
		}
		if($b['supplierid']!=''){
			$condition=' AND sm.supplierid='.$b['supplierid'];
		}
		$materials=$this->DB->query("
			SELECT sm.*,
				mv.title AS version,
				s.supplierid,s.title AS supplier,
				b.title AS brand
			FROM `suppliermaterial` AS sm
			LEFT JOIN materialversion AS mv ON (mv.versionid=sm.versionid)
			LEFT JOIN `supplier` AS s ON (s.supplierid=sm.supplierid)
			LEFT JOIN `brand` AS b ON (b.brandid=sm.brandid)
			WHERE sm.killed=0 ".$condition."
			ORDER BY suppliermaterialid ASC
		");
		if($this->DB->numRows()){
			$i=1;
			$r='<table class="hundred"><thead>
<tr><th colspan=7>供应商物资</th></tr></thead><tbody><tr><td width="15">ID</td><td>物资</td><td>供应商</td><td>品牌</td><td>单位</td><td>单价</td><td>备注</td></tr>';
			while($material=$this->DB->fetchArray($materials)){
				$materialInfo=$this->getMaterial(array('materialid'=>$material['materialid']));
				$r.='<tr class="'.$this->rotateLine().'">
					<td>'.$i.'</td>
					<td><a href="'.$materialInfo['url'].'">'.$materialInfo['no'].'</a>　'.$materialInfo['material'].'　'.$materialInfo['standard'].'</td>
					<td><a href="/s.php?module=psupplier&action=view&supplierid='.$material['supplierid'].'">'.$material['supplier'].'</a></td>
					<td>'.$material['brand'].'</td>
					<td>'.$materialInfo['unit'].'</td>
					<td>'.$material['price'].'</td>
					<td>'.$material['remark'].'</td>
					</tr>';
				$i++;
			}
			$r.='</tbody></table>';
		}
		return $r;
	}
	//PMS function
	/**
 	 * 获取工作中心关联物资列表
 	 * @author creator gaowenfei
 	 * @access public
 	 * @param array $b 数组参数
 	 * @return true
 	 * @throws none 没有异常
 	 */
	function releatedWorkcenterMaterial($b){
		if($b['start']=='')$b['start']=0;
		if($b['perpage']=='')$b['perpage']=10;
		if($b['materialid']!=''){
			$condition=' AND wci.materialid='.$b['materialid'];
		}
		if($b['workcenterid']!=''){
			$condition=' AND wci.workcenterid='.$b['workcenterid'];
		}
		$items=$this->DB->query("
			SELECT wci.*,
				mv.title AS version,mv.qualified AS mvqualified,mv.planInbound,mv.planOutbound,
				wc.workcenterid,wc.title AS workcenter
			FROM `workcenteritem` AS wci
			LEFT JOIN `materialversion` AS mv ON (mv.versionid=wci.versionid)
			LEFT JOIN `workcenter` AS wc ON (wc.workcenterid=wci.workcenterid)
			LEFT JOIN `materialitem` AS mi ON (mi.itemid=wci.materialitemid)
			WHERE wci.killed=0 ".$condition."
			ORDER BY itemno ASC
			LIMIT ".$b['start'].",".$b['perpage']."
		");
		if($rtn['num']=$this->DB->numRows()){
			$i=$b['start']+1;
			while($item=$this->DB->fetchArray($items)){
				$usable=$item['mvqualified']+$item['planInbound']-$item['planOutbound'];
				$materialInfo=$this->getMaterial(array('materialid'=>$item['materialid'],'itemid'=>$item['materialitemid']));
				$itemtr.='<tr class="'.$this->rotateLine().' small">
					<td>'.$i.'</td>
					<td><a href="'.$materialInfo['url'].'">'.$materialInfo['no'].'</a>　'.$materialInfo['material'].'　'.$materialInfo['standard'].'</td>
					<td>'.$item['version'].'</td>
					<td><a href="/s.php?module=workcenter&action=view&workcenterid='.$item['workcenterid'].'">'.$item['workcenter'].'</a></td>
					<td>'.$item['mvqualified'].'/'.$item['planInbound'].'/'.$item['planOutbound'].' '.$materialInfo['unit'].'</td>
					<td>'.$usable.' '.$materialInfo['unit'].'</td>
					<td><a href="/s.php?module=workcenter&action=update&workcenterid='.$item['workcenterid'].'">修改</a>　
						包装规格 (<a href="/s.php?module=packinglist&action=updatePackingSize&wcitemid='.$item['itemid'].'">修改</a> <a href="/s.php?module=packinglist&action=viewPackingSize&wcitemid='.$item['itemid'].'">查看</a>)</td>
					</tr>';
				$i++;
			}
		}
		$rtn['panel'].=$itemtr;
		//
		$pcount=$this->DB->queryFirst("SELECT COUNT(DISTINCT wci.itemid) AS count FROM `workcenteritem` AS wci WHERE wci.killed=0 ".$condition." ");
		if($pcount['count']>0){
			$rtn['counter']=$pcount['count'];
			$rtn['count']=' ('.$pcount['count'].')';
		}else{
			$rtn['off']=' disabled';
		}

		return $rtn;
	}
	//
	/**
 	 * 获取关联的需求计划单详情
 	 * @author creator gaowenfei
 	 * @access public
 	 * @param array $b 数组参数
 	 * @return true
 	 * @throws none 没有异常
 	 */
	function releatedRequirement($r){
		$condition.="ri.killed=0";
		if($r['orderid']>0){
			$condition.=" AND ri.module='order' AND ri.mid=".$r['orderid'];
		}
		if($r['sampleid']>0){
			$condition.=" AND ri.module='sample' AND ri.mid=".$r['sampleid'];
		}
		if(!$r['panel'])$r['panel']=1;
		$rt=$this->DB->queryFirst("
			SELECT r.*,
				mt.title AS type,
				msi.title AS situation,
				s.title AS status,
				u.username AS creator,
				us.username AS modifier,
				ul.username AS locker
			FROM `requirementitem` AS ri
			LEFT JOIN `requirement` AS r ON (r.requirementid=ri.requirementid)
			LEFT JOIN mftstatus AS s ON (s.statusid=r.statusid)
			LEFT JOIN `mfttype` AS mt ON (mt.typeid=r.typeid)
			LEFT JOIN `mftsituation` AS msi ON (msi.situationid=r.situationid)
			LEFT JOIN user AS u ON (u.userid=r.creator)
			LEFT JOIN user AS us ON (us.userid=r.modifier)
			LEFT JOIN user AS ul ON (ul.userid=r.locker)
			WHERE ".$condition."
			LIMIT 0,1
		");
		if($rt AND $r['panel']==1){
		$rt['created']=date('Y-m-d H:i:s',$rt['created']);
		$created='由 '.$rt['creator'].' 于 '.$rt['created'].' 建立';
		if($rt['modified']!=0)$modify='，由 '.$rt['modifier'].' 于 '.date('Y-m-d H:i:s',$rt['modified']).' 修改';
		if($rt['statusid']==2){
			$pstatus='由 '.$rt['locker'].' 于 '.date('Y-m-d H:i:s',$rt['locked']).' 锁定';
		}
		if($rt['statusid']==1){
			$changeStatus='|　<a href="/s.php?module=requirement&action=update&requirementid='.$rt['requirementid'].'">修改</a>　|　<a href="/s.php?module=requirement&action=lock&requirementid='.$rt['requirementid'].'">锁定</a>';
		}elseif($rt['statusid']!=1){
			$changeStatus='|　<a href="/s.php?module=requirement&action=unlock&requirementid='.$rt['requirementid'].'">解锁</a>　|　<a href="/s.php?module=outbound&action=add&departmentid=8&typeid=3">新建出库单</a>';
		}
		//计划的物资
		$items=$this->DB->query("
			SELECT ri.*,
				mv.title AS version,mv.qualified AS mvqualified,mv.planInbound,mv.planOutbound,
				r.version AS reviseversion
			FROM requirementitem AS ri
			LEFT JOIN `materialversion` AS mv ON (mv.versionid=ri.versionid)
			LEFT JOIN `revise` AS r ON (r.reviseid=ri.reviseid)
			WHERE ri.killed=0 AND ri.requirementid='".$rt['requirementid']."'
			ORDER BY ri.ordering ASC
		");
		if($this->DB->numRows()){
			$i=1;
			$id=array();
			$ifbom='';
			while($item=$this->DB->fetchArray($items)){
				$usable=$item['mvqualified']+$item['planInbound']-$item['planOutbound'];
				$item['dateline']=date('Y-m-d',$item['dateline']);
				$orderrevise=$this->relatedRevise(array('module'=>$item['module'],'mid'=>$item['mid']));
				$orderversion='(R'.($item['reviseversion']+1).')</span>';
				if($item['module']=='order'){
					$order=$this->DB->queryFirst("SELECT orderno,orderid FROM `order` WHERE orderid='".$item['mid']."'");
					$orderno=$this->iif($orderrevise['reviseid']==$item['reviseid'],'<a href="/s.php?module=order&action=view&orderid='.$item['mid'].'">'.$order['orderno'].'</a> <span class="small green">','<a href="/s.php?module=order&action=revise&reviseid='.$orderrevise['reviseid'].'">'.$order['orderno'].'</a> <span class="small darkred">').$orderversion;
				}elseif($item['module']=='sample'){
					$order=$this->DB->queryFirst("SELECT sampleno,sampleid FROM `sample` WHERE sampleid='".$item['mid']."'");
					$orderno=$this->iif($orderrevise['reviseid']==$item['reviseid'],'<a href="/s.php?module=sample&action=view&sampleid='.$item['mid'].'">'.$order['sampleno'].'</a> <span class="small green">','<a href="/s.php?module=sample&action=revise&reviseid='.$orderrevise['reviseid'].'">'.$order['sampleno'].'</a> <span class="small darkred">').$orderversion;
				}elseif($item['module']=='stock'){
					$orderno='库存需求';
				}
				$materialInfo=$this->getMaterial(array('materialid'=>$item['materialid'],'itemid'=>$item['materialitemid']));
				//判断是否存在子BOM
				if($materialInfo['originid']==1){
					$child=$this->bomChildList(array('parentid'=>$item['materialid'],'materialitemid'=>$item['materialitemid'],'versionid'=>$item['versionid'],'loopNum'=>1,'getCount'=>1));
					$ifbom=$this->iif($child==1,'是','否');
					}else{
					$ifbom='-';
				}
				//
				$itemtr.='<tr class="'.$this->iif($this->rotate(),'odd','even').' '.$this->iif($materialInfo['originid']==1,$this->iif($ifbom=='否','bgRed',''),'bgOrange').' small">
					<td>'.$i.'</td>
					<td>'.$orderno.'</td>
					<td><a href="'.$materialInfo['url'].'">'.$materialInfo['no'].'</a></td>
					<td>'.$materialInfo['material'].'</td>
					<td>'.$materialInfo['standard'].'</td>
					<td>'.$item['version'].'</td>
					<td>'.$ifbom.'</td>
					<td>'.$materialInfo['origin'].'</td>
					<td>'.$item['quantity'].'</td>
					<td>'.$item['dateline'].'</td>
					<td>'.$item['remark'].'</td>
					<td>'.$item['mvqualified'].'</td>
					<td>'.$item['planInbound'].'</td>
					<td>'.$item['planOutbound'].'</td>
					<td>'.$item['min'].'</td>
					<td>'.$usable.'</td>
					</tr>';
				$i++;
			}
		}
		$mode=$this->iif($rt['modeid']==0,'净需求',$this->iif($rt['modeid']==1,'毛需求','毛需求(BOM)'));
		$orderChange=$this->iif($rt['orderChange']==0,'',' <span class="small darkred"> ( 订单取消 ) </span>');
		if($rt['statusid']==2){
			$mrp=$this->DB->queryFirst("
					SELECT mrpid
					FROM mrp
					WHERE killed=0 AND ifChooser=0 AND requirementid='".$this->input['requirementid']."'
					LIMIT 0,1
				");
		}
		$orderChangeth=$this->iif($rt['statusid']==1 OR $mrp!='' OR $rt['typeid']==2,'','<span class="right"><a href="/s.php?module=requirement&action=orderChange&requirementid='.$this->input['requirementid'].'">订单变更</a></span>');
		$situation='<span class="'.$this->iif($rt['situationid']==2,'darkred middle',$this->iif($rt['situationid']==3,'red middle bold','')).'">'.$rt['situation'].'</span>';
		$status='<span class="'.$this->iif($rt['statusid']==1,'darkred','green').'">'.$rt['status'].'</span>';
		$revise=$this->relatedRevise(array('module'=>'requirement','mid'=>$rt['requirementid']));
		$right=$this->iif($rt['killed']==0,'<a href="/s.php?module=requirement&action=add">新建需求计划</a>　<a href="/s.php?module=requirement&action=remove&requirementid='.$rt['requirementid'].'&rt=view">删除</a>　'.$changeStatus.'　','<a href="/s.php?module=requirement&action=restore&requirementid='.$rt['requirementid'].'&rt=view">恢复</a>').' |　<a href="/s.php?module=requirement&action=viewMRP">MRP列表</a>';
		$rtn['panel']=<<<EOF
<div><span class="small gray">当前需求计划状态：　{$status}</span>　　　　<span class="gray small">{$pstatus}</span><span class="right normal">{$right}</span></div>
<div class="bold darkred big" style="clear:both;">{$rt['requirementno']}{$revise['currencyRevise']}　　　<span class="gray small">计划人员：<span class="bold darkred">{$rt['planner']}</span></span>　　　　<span class="gray small"> {$created} {$modify}</span></div>
<table class="hundred">
<thead><tr><th colspan="4">需求计划单</th></tr></thead>
<tbody>
<tr class="odd">
<td width="100">编号：</td><td width="475"><a href="/s.php?module=requirement&action=view&requirementid={$rt['requirementid']}" target="_blank">{$rt['requirementno']}</a></td>
<td>需求计算方式：</td><td><span class="bold darkred">{$mode}</span></td>
</tr>
<tr class="even">
<td width="100">计划员：</td><td>{$rt['planner']}</td>
<td width="100">计划来源：</td><td>{$rt['type']}{$orderChange}</td>
</tr>
<tr class="odd">
<td width="100">紧急情况：</td><td>{$situation}</td>
<td>计划时间：</td><td>{$rt['created']}</td>
</tr>
<tr class="even">
<td width="100">备注：</td><td>{$rt['remark']}</td>
<td></td><td></td>
</tr>
</tbody>
</table>
<table class="hundred">
<thead><tr><th colspan="16">需求计划明细{$orderChangeth}</th></tr></thead>
<tbody>
<tr class="center even">
<td width="15">ID</td><td width="140">订单编号</td><td>物资编号</td><td>物资名称</td><td>物资规格</td><td>版本</td><td>存在BOM(子)</td><td>来源</td><td>毛需求数量</td><td>需求交期</td><td>备注</td><td>良品库存</td><td>待入库存</td><td>待出库存</td><td>安全库存</td><td>计划可用</td>
</tr>
{$itemtr}
</tbody>
</table>
{$revise['panel']}
EOF;
		}
		if(!$rt){
			$rtn['off']=' disabled';
		}
		return $rtn;
	}
	//
	//
	/**
 	 * 获取关联的任务单列表
 	 * @author creator gaowenfei
 	 * @access public
 	 * @param array $b 数组参数
 	 * @return true
 	 * @throws none 没有异常
 	 */
	function releatedTasklist($r){
		if(!$r['panel'])$r['panel']=1;
		if($r['start']=='')$r['start']=0;
		if($r['perpage']=='')$r['perpage']=10;
		$condition="t.killed=0 AND t.statusid<>1";
		if($r['workcenterid']>0){
			$items=$this->DB->query("
			SELECT taskid
			FROM `taskitem`
			WHERE killed=0 AND workcenterid='".$r['workcenterid']."'
			");
			if($this->DB->numRows()){
				while($item=$this->DB->fetchArray($items)){
					$id[]=$item['taskid'];
				}
				$condition.=" AND t.taskid IN (".implode(',', $id).")";
			}else{
				$condition.=" AND t.taskid=0";
			}
			$condition2="AND ti.workcenterid=".$r['workcenterid'];
		}
		if($r['requirementid']>0){
			$items=$this->DB->query("
			SELECT taskid
			FROM `taskitem`
			WHERE killed=0 AND requirementid='".$r['requirementid']."'
			");
			if($this->DB->numRows()){
				while($item=$this->DB->fetchArray($items)){
					$id[]=$item['taskid'];
				}
				$condition.=" AND t.taskid IN (".implode(',', $id).")";
			}else{
				$condition.=" AND t.taskid=0";
			}
			$condition2="AND ti.requirementid=".$r['requirementid'];
		}
		$tasks=$this->DB->query("
			SELECT t.*,
				s.title AS status,
				u.username AS creator,
				us.username AS modifier
			FROM `task` AS t
			LEFT JOIN mftstatus AS s ON (s.statusid=t.statusid)
			LEFT JOIN user AS u ON (u.userid=t.creator)
			LEFT JOIN user AS us ON (us.userid=t.modifier)
			WHERE ".$condition."
			ORDER BY t.taskid DESC, t.modified DESC, t.created DESC
			LIMIT ".$r['start'].",".$r['perpage']."
		");
		$rtn['num']=$this->DB->numRows();
		if($rtn['num'] AND $r['panel']==1){
			$index=$r['start']+1;
			while($task=$this->DB->fetchArray($tasks)){
				$created=date('Y-m-d', $task['created']);
				$itemtr='';
				$items=$this->DB->query("
					SELECT ti.*,
						t.taskno,t.addtype,
						s.title AS status,
						mv.title AS version,
						u.username AS creator,
						us.username AS modifier
					FROM `taskitem` AS ti
					LEFT JOIN task AS t ON (t.taskid=ti.taskid)
					LEFT JOIN mftstatus AS s ON (s.statusid=t.statusid)
					LEFT JOIN `materialversion` AS mv ON (mv.versionid=ti.versionid)
					LEFT JOIN user AS u ON (u.userid=ti.creator)
					LEFT JOIN user AS us ON (us.userid=ti.modifier)
					WHERE ti.killed=0 AND ti.taskid=".$task['taskid']." ".$condition2."
					ORDER BY ti.itemid ASC, ti.modified DESC, ti.created DESC
				");
				if($this->DB->numRows()){
					$i=1;
					$itemtr.='<tr><td width="15">ID</td><td width="200">物资</td><td>版本</td><td>需求数量</td><td width="80">投产日期</td><td>完工日期</td><td>派工人数</td><td>工作时数</td><td width="60">申请领料</td><td>已完成数</td><td>未完成数</td><td width="100">备注</td><td width="120">查看</td><td width="120">操作</td></tr>';
					while($item=$this->DB->fetchArray($items)){
						$unfinished=$item['quantity']-$item['finishquantity'];
						if($unfinished<0)$unfinished=0;
						if($r['workcenterid']>0){
							$wc=$this->DB->queryFirst("SELECT outboundtypeid FROM workcenter WHERE workcenterid='".$r['workcenterid']."'");
							if($item['statusid']==4){
								$op='生产完成';
							}elseif($item['statusid']==5){
								$op='指定完工';
							}else{
								$outboundItem=$this->DB->queryFirst("SELECT DISTINCT mid FROM preoutbounditem WHERE killed=0 AND module='outbound' AND taskitemid='".$item['itemid']."' LIMIT 0,1");
								$outbound=$this->DB->queryFirst("SELECT ifComplete FROM outbound WHERE outboundid='".$outboundItem['mid']."'");
								if($item['ifChooser']>0){
									$op='已申请领料';
								}else{
									$op='<a href="/s.php?module=task&action=addOutbound&workcenterid='.$r['workcenterid'].'">申请领料</a>';
								}
								if($outbound['ifComplete']==2 OR $wc['outboundtypeid']==2)$op='<a href="/s.php?module=dispatch&action=add&workcenterid='.$r['workcenterid'].'&taskitemid='.$item['itemid'].'">新增派工单</a>';
							}
						}else{
							$op='无';
						}
						$materialInfo=$this->getMaterial(array('materialid'=>$item['materialid'],'itemid'=>$item['materialitemid']));
						$itemtr.='<tr class="'.$this->rotateLine(). ' small">
							<td>'.$i.'</td>
							<td width="500"><span class="small"><a href="'.$materialInfo['url'].'">'.$materialInfo['no'].'</a> '.$materialInfo['material'].' '.$materialInfo['standard'].'</span></td>
							<td>'.$item['version'].'</td>
							<td><span class="bold">'.number_format($item['quantity']).'</span> '.$item['unit'].'</td>
							<td><span class="bold darkred">'.date('Y-m-d',$item['workdate']).'</span></td>
							<td><span class="bold darkred">'.date('Y-m-d',$item['finishdate']).'</span></td>
							<td><span class="bold darkred">'.$item['dispatchnumber'].' 人</span></td>
							<td><span class="bold darkred">'.$item['workhours'].' 时</span></td>
							<td><span class="bold darkred">'.$this->iif($item['ifChooser']>0,'是','否').'</span> </td>
							<td><span class="bold">'.number_format($item['finishquantity']).'</span> '.$materialInfo['unit'].'</td>
							<td><span class="bold">'.number_format($unfinished).'</span> '.$materialInfo['unit'].'</td>
							<td>'.$item['remark'].'</td>
							<td><a href="/s.php?module=task&action=viewOutboundRecord&taskitemid='.$item['itemid'].'">出库记录</a>　<a href="/s.php?module=task&action=viewInboundRecord&taskitemid='.$item['itemid'].'">入库记录</a></td>
							<td>'.$op.'</td>
						</tr>';
						$i++;
					}
				}
				$title='';
				$addtype=$this->iif($task['addtype']==1,'手工方式','生产计划');
				if($task['taskid']!='')$title.='　<span class="small">任务单：</span><a href="/s.php?module=task&action=view&taskid='.$task['taskid'].'">'.$task['taskno'].'</a>';
				if($task['status']!='')$title.='　<span class="small">状态：</span><span class="'.$this->iif($task['statusid']==1,'red','green').'">'.$task['status'].'</span>';
				if($addtype!='')$title.='　<span class="small">录入类型：</span>'.$addtype.'';
				//if($task['remark']!='')$title.='　<span class="small">备注：</span>'.$task['remark'];
				if($task['creator']!='')$title.='　<span class="small">创建人：</span>'.$task['creator'];
				if($task['created']!='')$title.='　<span class="small">创建时间：</span>'.$created;
				$options='展开 ';
				$rtn['panel'].=<<<EOF
<table class="hundred">
<thead>
<tr><th colspan="15"><span class="right">{$options} <span class="minus"></span></span>	<span class="normal">[<b>{$index}</b>]</span>{$title}</th></tr>
</thead>
<tbody id="task{$task['itemid']}" style="display:table-row-group">
{$itemtr}
</tbody>
</table>
EOF;
				$index++;
			}
		}
		//
		$pcount=$this->DB->queryFirst("SELECT COUNT(t.taskid) AS count FROM `task` AS t WHERE ".$condition." ");
		if($pcount['count']>0){
			$rtn['counter']=$pcount['count'];
			$rtn['count']=' ('.$pcount['count'].')';
		}else{
			$rtn['off']=' disabled';
		}
		return $rtn;
	}
	//
	/**
 	 * 获取关联的派工单列表
 	 * @author creator gaowenfei
 	 * @access public
 	 * @param array $b 数组参数
 	 * @return true
 	 * @throws none 没有异常
 	 */
	function releatedDispatchlist($r){
		if($r['start']=='')$r['start']=0;
		if($r['perpage']=='')$r['perpage']=10;
		if($r['requirementid']>0){// 需求计划中相关
			$id=$itemId=array();
			$items=$this->DB->query("
				SELECT ti.itemid,t.taskid 
				FROM taskitem AS ti 
				LEFT JOIN task AS t ON (t.taskid=ti.taskid) 
				WHERE t.killed=0 AND ti.killed=0 AND requirementid='".$r['requirementid']."'
			");
			if($this->DB->numRows()){
				while($item=$this->DB->fetchArray($items)){
					$id[]=$item['taskid'];
					$itemId[]=$item['itemid'];
				}
			}
			if(is_array($id) AND is_array($itemId)){
				$left=" LEFT JOIN dispatchitem AS di ON (di.dispatchid=d.dispatchid) ";
				$condition.=" AND di.taskitemid IN (".implode(',',$itemId).")";
				$condition2.=" AND di.taskitemid IN (".implode(',',$itemId).")";
			}
			if(empty($id)){
				return false;
			}
		}elseif($r['workcenterid']>0){ //工作中心中相关
			$condition.=" AND d.workcenterid=".$r['workcenterid'];
			$condition2.=" AND di.workcenterid=".$r['workcenterid'];
		}elseif($r['taskid']>0){ // 任务单中相关
			$taskitems=$this->DB->query("
				SELECT workcenterid
				FROM `taskitem`
				WHERE killed=0 AND taskid=".$r['taskid']."
			");
			if($this->DB->numRows()){
				while($taskitem=$this->DB->fetchArray($taskitems)){
					$id[]=$taskitem['workcenterid'];
				}
				$left=" LEFT JOIN dispatchitem AS di ON (di.dispatchid=d.dispatchid) ";
				$condition.=" AND d.workcenterid IN (".implode(',', $id).") AND di.taskid=".$r['taskid'];
				$condition2.=" AND di.workcenterid IN (".implode(',', $id).") AND di.taskid=".$r['taskid'];
			}else{
				$condition.=" AND dispatchid=0";
				$condition2='';
			}
		}else{
			$condition.=" AND dispatchid=0";
			$condition2='';
		}

		$dispatchs=$this->DB->query("
			SELECT DISTINCT d.*,
				s.title AS status,
				u.username AS creator,
				us.username AS modifier
			FROM `dispatch` AS d
			LEFT JOIN mftstatus AS s ON (s.statusid=d.statusid)
			LEFT JOIN user AS u ON (u.userid=d.creator)
			LEFT JOIN user AS us ON (us.userid=d.modifier)
			".$left."
			WHERE d.killed=0 ".$condition."
			ORDER BY d.dispatchid DESC, d.modified DESC, d.created DESC
			LIMIT ".$r['start'].",".$r['perpage']."
		");
		$rtn['num']=$this->DB->numRows();
		if($rtn['num']){
			$index=$r['start']+1;
			while($dispatch=$this->DB->fetchArray($dispatchs)){
				$created=date('Y-m-d', $dispatch['created']);
				$itemtr='';
				$items=$this->DB->query("
					SELECT di.*,
					t.taskno,
					s.title AS status,
					mv.title AS version,
					u.username AS creator,
					us.username AS modifier
				FROM `dispatchitem` AS di
				LEFT JOIN task AS t ON (t.taskid=di.taskid)
				LEFT JOIN mftstatus AS s ON (s.statusid=t.statusid)
				LEFT JOIN `materialversion` AS mv ON (mv.versionid=di.versionid)
				LEFT JOIN user AS u ON (u.userid=di.creator)
				LEFT JOIN user AS us ON (us.userid=di.modifier)
				WHERE di.killed=0 ".$condition2." AND di.dispatchid=".$dispatch['dispatchid']."
				ORDER BY di.itemid ASC, di.modified DESC, di.created DESC
			");
			if($this->DB->numRows()){
				$i=1;
				$itemtr.='<tr><td width="15">ID</td><td width="200">物资</td><td>版本</td><td>需求数量</td><td>备注</td><td width="100">投产日期</td><td>派工人数</td><td>工作时数</td><td>已完成数</td><td>未完成数</td><td>操作</td></tr>';
				while($item=$this->DB->fetchArray($items)){
					$materialInfo=$this->getMaterial(array('materialid'=>$item['materialid'],'itemid'=>$item['materialitemid']));
					if($dispatch['statusid']==1){
						$addQuantity='<a href="/s.php?module=dispatch&action=update&dispatchid='.$dispatch['dispatchid'].'">修改派工单</a>';
					}else{
						$addQuantity=$this->iif($item['statusid']==4 OR $dispatch['statusid']==4,'生产完成','<a href="/s.php?module=dispatch&action=addQuantity&dispatchid='.$dispatch['dispatchid'].'">新增生产记录</a>');
					}
					$unfinished=$item['quantity']-$item['finishquantity'];
					if($unfinished<0)$unfinished=0;
					
					$itemtr.='<tr class="'.$this->rotateLine(). ' small">
						<td>'.$i.'</td>
						<td width="580" class="small"><a href="'.$materialInfo['url'].'">'.$materialInfo['no'].'</a>　'.$materialInfo['title'].'　'.$materialInfo['standard'].'</td>
						<td>'.$item['version'].'</td>
						<td><span class="bold">'.number_format($item['quantity']).'</span> '.$item['unit'].'</td>
						<td>'.$item['remark'].'</td>
						<td><span class="bold darkred">'.date('Y-m-d',$item['workdate']).'</span></td>
						<td><span class="bold darkred">'.$item['dispatchnumber'].'</span> 人</td>
						<td><span class="bold darkred">'.$item['workhours'].'</span> 时</td>
						<td><span class="bold">'.number_format($item['finishquantity']).'</span> '.$item['unit'].'</td>
						<td><span class="bold">'.number_format($unfinished).'</span> '.$item['unit'].'</td>
						<td>'.$addQuantity.'</td>
					</tr>';
					$i++;
				}
			}
			$title='';
			if($dispatch['dispatchid']!='')$title.='　<span class="small">派工单：</span><a href="/s.php?module=dispatch&action=view&dispatchid='.$dispatch['dispatchid'].'" target="_blank">'.$dispatch['dispatchno'].'</a>(<span class="'.$this->iif($dispatch['statusid']==1,'red','green').'">'.$dispatch['status'].'</span>)';
			if($dispatch['workcenterid']!=''){
				$wc=$this->DB->queryFirst("SELECT title FROM workcenter WHERE workcenterid=".$dispatch['workcenterid']."	");
				$title.='　<span class="small">工作中心：</span>'.$wc['title'].'';
			}
			//if($dispatch['remark']!='')$title.='　<span class="small">备注：</span>'.$dispatch['remark'];
			if($dispatch['creator']!='')$title.='　<span class="small">创建人：</span>'.$dispatch['creator'];
			if($dispatch['created']!='')$title.='　<span class="small">创建时间：</span>'.$created;
			if($dispatch['ifVerify']=='0'){
				$title.='　<span class="small">审核状态：</span> <span class="red">等待审核</span>';
			}elseif($dispatch['ifVerify']=='1'){
				$title.='　<span class="small">审核状态：</span> <span class="green">通过审核</span>';
			}elseif($dispatch['ifVerify']=='-1'){
				$title.='　<span class="small">审核状态：</span> 未通过审核';
			}
			$options='展开';
			$rtn['panel'].=<<<EOF
<table class="hundred">
<thead>
<tr><th colspan="15"><span class="right">{$addServiceNo}　{$options} <span class="minus"></span></span>	<span class="normal">[<b>{$index}</b>]</span>{$title}</th></tr>
</thead>
<tbody id="dispatch{$dispatch['itemid']}" style="display:table-row-group">
{$itemtr}
</tbody>
</table>
EOF;
			$index++;
			}
		}
		//
		$pcount=$this->DB->queryFirst("SELECT COUNT(DISTINCT d.dispatchid) AS count FROM `dispatch` AS d ".$left." WHERE d.killed=0 ".$condition." ");
		if($pcount['count']>0){
			$rtn['counter']=$pcount['count'];
			$rtn['count']=' ('.$pcount['count'].')';
		}else{
			$rtn['off']=' disabled';
		}

		return $rtn;
	}
	//
	//
	/**
 	 * 获取关联的生产记录列表
 	 * @author creator gaowenfei
 	 * @access public
 	 * @param array $b 数组参数
 	 * @return true
 	 * @throws none 没有异常
 	 */
	function releatedProductionrecord($r){
		if($r['start']=='')$r['start']=0;
		if($r['perpage']=='')$r['perpage']=10;
			$condition="AND d.killed=0";
		if($r['workcenterid']>0){
			$condition.=" AND d.workcenterid=".$r['workcenterid'];
		}else{
			$condition.=" AND d.dispatchid=0";
		}
		$items=$this->DB->query("
			SELECT di.*,
			d.dispatchno,
			t.taskno,
			s.title AS status,
			mv.title AS version,
			u.username AS creator,
			us.username AS modifier
		FROM `dispatchitem` AS di
		LEFT JOIN task AS t ON (t.taskid=di.taskid)
		LEFT JOIN `dispatch` AS d ON (d.dispatchid=di.dispatchid)
		LEFT JOIN mftstatus AS s ON (s.statusid=t.statusid)
		LEFT JOIN `materialversion` AS mv ON (mv.versionid=di.versionid)
		LEFT JOIN user AS u ON (u.userid=di.creator)
		LEFT JOIN user AS us ON (us.userid=di.modifier)
		WHERE di.killed=0 AND di.finishdate<>0 ".$condition."
		ORDER BY di.finishdate DESC, di.modified DESC, di.created DESC
		LIMIT ".$r['start'].",".$r['perpage']."
		");
		if($rtn['num']=$this->DB->numRows()){
			$i=$r['start']+1;
			while($item=$this->DB->fetchArray($items)){
				$materialInfo=$this->getMaterial(array('materialid'=>$item['materialid'],'itemid'=>$item['materialitemid']));
				$itemtr.='<tr class="'.$this->rotateLine(). ' small">
					<td>'.$i.'</td>
					<td>'.$item['batch'].'</td>
					<td width="90"><span class="small"><a href="/s.php?module=dispatch&action=view&dispatchid='.$item['dispatchid'].'">'.$item['dispatchno'].'</a>
					</span></td>
					<td width="580"><span class="small"><a href="'.$materialInfo['url'].'">'.$materialInfo['no'].'</a>　'.$materialInfo['material'].'　'.$materialInfo['standard'].'</span></td>
					<td>'.$item['version'].'</td>
					<td>'.number_format($item['quantity']).' '.$materialInfo['unit'].'</td>
					<td>'.date('Y-m-d',$item['workdate']).'</td>
					<td>'.number_format($item['finishquantity']).' '.$materialInfo['unit'].'</td>
					<td>'.date('Y-m-d H:i:s',$item['finishdate']).'</td>
					<td>'.$item['finishhours'].' 时</td>
				</tr>';
				$i++;
			}
		}
		$rtn['panel'].=$itemtr;
		//
		$pcount=$this->DB->queryFirst("SELECT COUNT(DISTINCT di.itemid) AS count FROM `dispatchitem` AS di LEFT JOIN `dispatch` AS d ON (d.dispatchid=di.dispatchid) WHERE di.killed=0 AND di.finishdate<>0 ".$condition." ");
		if($pcount['count']>0){
			$rtn['counter']=$pcount['count'];
			$rtn['count']=' ('.$pcount['count'].')';
		}else{
			$rtn['off']=' disabled';
		}

		return $rtn;
	}
	//
	//
	/**
 	 * 获取关联的维修记录列表
 	 * @author creator gaowenfei
 	 * @access public
 	 * @param array $b 数组参数
 	 * @return true
 	 * @throws none 没有异常
 	 */
	function releatedRepairrecord($r){
		if($r['start']=='')$r['start']=0;
		if($r['perpage']=='')$r['perpage']=3;
		$condition="di.killed=0";
		if($r['workcenterid']>0){
			$condition.=" AND di.workcenterid=".$r['workcenterid'];
		}elseif($r['dispatchid']>0){
			$condition.=" AND di.dispatchid=".$r['dispatchid'];
		}else{
			$condition.=" AND di.dispatchid=0";
		}
		$records=$this->DB->query("
			SELECT DISTINCT dri.dispatchitemid
			FROM `repairrecord` AS rr
			LEFT JOIN `dispatchrecorditem` AS dri ON (dri.itemid=rr.recorditemid)
			WHERE rr.killed=0 AND dri.killed=0
			ORDER BY rr.repairid ASC, rr.modified DESC, rr.created DESC
		");
		if($this->DB->numRows()){
			while($record=$this->DB->fetchArray($records)){
				$id[]=$record['dispatchitemid'];
			}
			$condition.=" AND di.itemid IN (".implode(',', $id).")";
		}

		$dispatchs=$this->DB->query("
			SELECT di.*,
				d.dispatchno,
				s.title AS status,
				mv.title AS version,
				u.username AS creator,
				us.username AS modifier
			FROM `dispatchitem` AS di
			LEFT JOIN dispatch AS d ON (d.dispatchid=di.dispatchid)
			LEFT JOIN `materialversion` AS mv ON (mv.versionid=di.versionid)
			LEFT JOIN mftstatus AS s ON (s.statusid=d.statusid)
			LEFT JOIN user AS u ON (u.userid=d.creator)
			LEFT JOIN user AS us ON (us.userid=d.modifier)
			WHERE ".$condition."
			ORDER BY di.itemid DESC, di.modified DESC, di.created DESC
			LIMIT ".$r['start'].",".$r['perpage']."
		");
		$rtn['num']=$this->DB->numRows();
		if($rtn['num']){
			$i=1;$index=$r['start']+1;
			while($dispatch=$this->DB->fetchArray($dispatchs)){
				$created=date('Y-m-d', $dispatch['created']);
				$itemtr='';
				$items=$this->DB->query("
					SELECT rr.*,
					r.title AS routing,
					p.title AS proce,p.ordering AS pordering,
					u.username AS creator,
					us.username AS modifier
				FROM `repairrecord` AS rr
				LEFT JOIN `dispatchrecorditem` AS dri ON (dri.itemid=rr.recorditemid)
				LEFT JOIN routing AS r ON (r.routingid=dri.routingid)
				LEFT JOIN `procedure` AS p ON (p.procedureid=dri.procedureid)
				LEFT JOIN user AS u ON (u.userid=rr.creator)
				LEFT JOIN user AS us ON (us.userid=rr.modifier)
				WHERE rr.killed=0 AND dri.dispatchitemid=".$dispatch['itemid']."
				ORDER BY rr.repairid ASC, rr.modified DESC, rr.created DESC
				");
				if($this->DB->numRows()){
					$itemtr.='<tr><td width="15">ID</td><td>工艺</td><td>工序</td><td>排序</td><td>故障类型</td><td>故障描述</td><td>数量</td><td>送修时间</td><td>完工时间</td><td>处理结果</td><td>创建信息</td><td>操作</td></tr>';
					while($item=$this->DB->fetchArray($items)){
						$type=$this->iif($item['wrongtype']==1,'A: 人为操作问题','B: 物料问题');
						$result=$this->iif($item['repairresult']==1,'A: 合格','B: 报废');
						$itemtr.='<tr class="'.$this->rotateLine(). ' small">
						<td>'.$i.'</td>
						<td>'.$item['routing'].'</td>
						<td>'.$item['proce'].'</td>
						<td>'.$item['pordering'].'</td>
						<td><span class="bold darkred">'.$type.'</span></td>
						<td>'.$item['describe'].'</td>
						<td>'.$item['quantity'].'</td>
						<td>'.date('Y-m-d',$item['repairdate']).'</td>
						<td>'.date('Y-m-d',$item['finishdate']).'</td>
						<td><span class="bold darkred">'.$result.'</span></td>
						<td>由'.$item['creator'].'创建于'.date('Y-m-d',$item['created']).'</td>
						<td><a href="/s.php?module=dispatch&action=viewrepairRecord&recorditemid='.$item['recorditemid'].'"> 查看详情</a></td>
						</tr>';
						$i++;
					}


			$materialInfo=$this->getMaterial(array('materialid'=>$dispatch['materialid'],'itemid'=>$dispatch['materialitemid']));
			$title='';
			if($dispatch['dispatchid']!='')$title.='　<span class="small">派工单：</span><a href="/s.php?module=dispatch&action=view&dispatchid='.$dispatch['dispatchid'].'">'.$dispatch['dispatchno'].'</a>(<span class="'.$this->iif($dispatch['statusid']==1,'red','green').'">'.$dispatch['status'].'</span>)';
			if($dispatch['workcenterid']!=''){
				$wc=$this->DB->queryFirst("SELECT title FROM workcenter WHERE workcenterid=".$dispatch['workcenterid']."	");
				$title.='　<span class="small">工作中心：</span>'.$wc['title'].'';
			}
			if($materialInfo['no']!='')$title.='　<span class="small">生产物资：</span><a href="'.$materialInfo['url'].'">'.$materialInfo['material'].' '.$materialInfo['standard'].'　(版本：'.$dispatch['version'].')';
			if($dispatch['creator']!='')$title.='　<span class="small">创建人：</span>'.$dispatch['creator'];
			if($dispatch['created']!='')$title.='　<span class="small">创建时间：</span>'.$created;

			$options='展开';
			$rtn['panel'].=<<<EOF
<table class="hundred">
<thead>
<tr><th colspan="15"><span class="right">{$options} <span class="minus"></span></span>	<span class="normal">[<b>{$index}</b>]</span>{$title}</th></tr>
</thead>
<tbody id="dispatch{$dispatch['itemid']}" style="display:table-row-group">
{$itemtr}
</tbody>
</table>
EOF;
				$index++;
				}
			}
		}
		//
		$pcount=$this->DB->queryFirst("SELECT COUNT(di.itemid) AS count FROM `dispatchitem` AS di WHERE ".$condition." ");
		if($pcount['count']>0){
			$rtn['counter']=$pcount['count'];
			$rtn['count']=' ('.$pcount['count'].')';
		}else{
			$rtn['off']=' disabled';
		}

		return $rtn;
	}

	/**
	 *采购订单,供应商,供应商评价 列表通用分页
	 *@author HuangJinlong 2014-12-04
	 *access public
	 *@param array $b 数组参数
	 *return $topage 目标页面链接
	 */
	 function commonPaging($b){	 
	 if(!($b['startNum']==='' or $b['pagesize']==='' or $b['count']==='' or $b['module']==='' or $b['action']==='')){	
			$nextStart = $b['startNum']+$b['pagesize'];
			$preStart = $b['startNum']-$b['pagesize'];
			if($b['count']>$b['pagesize'] && $b['count']>$nextStart && $b['startNum']==0){
				$topage = '<a href="/s.php?module='.$b['module'].'&action='.$b['action'].$b['uriParams'].'&startno='.$nextStart.'">后一页</a>';
			}elseif($b['count']>$b['pagesize'] && $b['count']>$nextStart && $b['startNum']>0){
				$topage = '<a href="/s.php?module='.$b['module'].'&action='.$b['action'].$b['uriParams'].'&startno='.$preStart.'">前一页   </a>    <a href="/s.php?module='.$b['module'].'&action='.$b['action'].$b['uriParams'].'&startno='.$nextStart.'"> 后一页</a>';
			}elseif($b['count']>$b['pagesize'] && $b['count']<$nextStart && $b['startNum']>0){
				$topage = '<a href="/s.php?module='.$b['module'].'&action='.$b['action'].$b['uriParams'].'&startno='.$preStart.'">前一页</a>';
			} 
			return $topage;
		}else{
			$topage = '分页过程错误';
			return $topage;
		}
	 }
	/**
	 *供应商列表信息
	 *@author HuangJinlong 2014-12-01
	 *@param array $b 数组参数
	 *@return $conten 返回列表信息
	 */
	 function supplierList($b){
	 //echo $b['startNum'];
			$suppliers=$this->DB->query("
				SELECT `supplier`.supplierid,`supplier`.supplierno,`supplier`.title,`supplier`.abbr,`supplier`.categoryid,`supplier`.mainProduct,				`supplier`.address,`supplier`.mobile,`supplier`.telephone,`supplier`.attachs,`supplier`.images,`supplier`.modified,`supplier`.killed,`supplier`.created,
					m.username AS modifier,
					c.username AS creator
				FROM `supplier`
				LEFT JOIN `user` AS m ON (m.userid=`supplier`.modifier)
				LEFT JOIN `user` AS c ON (c.userid=`supplier`.creator)
				WHERE field='produce' AND ".$b['condition'].$b['search']."
				ORDER BY ".$b['orderby']['sql']." LIMIT ".$b['startNum'].",100
			");
			if($this->DB->numRows()){
				if($b['supplier']['rank']==''){
					if($b['layout']=='grid'){
							$conten .='<ul id="mmlist" class="mmlist clear">';
					}else{
							$conten .='<table class="hundred"><thead><th width="15">ID</th><th width="40">编号</th><th>供应商名称</th><th>简称</th><th width="35">等级</th><th>分类</th><th>主营</th><th>电话</th><th>地址</th><th width="60">选项</th></thead><tbody>';
					}
				}else{
						$conten .='';
				}
					$i=1;				
					while($supplier=$this->DB->fetchArray($suppliers)){
						$neardate=array();
						$supplier['title']=$this->parseConvertValue($supplier['title']);
						if($this->kclass->input['title']!=''){
							$title=$this->highlight($supplier['title'], $this->kclass->input['title']);
						}
							$evaluates=$this->DB->query("
								SELECT * FROM  `evaluate` 
								WHERE supplierid='".$supplier['supplierid']." and killed=0'
							");
							if($this->DB->numRows()){
								$neardate=array();
								while($evaluate=$this->DB->fetchArray($evaluates)){
									$neardate[$evaluate['evaluateid']]=$evaluate['evaluatedate'];						
								}
							}
						if($neardate){
							$near = array_search(max($neardate), $neardate);
						}
						if($b['supplier']['rank']==''){
							if($near){
								$sql=$this->DB->queryFirst("
									SELECT rank FROM  `evaluate` 
									WHERE evaluateid='".$near."' and supplierid='".$supplier['supplierid']."' and killed=0
								");	
								$rank = $sql['rank'];
							}
						}else{
							$rank = $b['supplier']['rank'];
						}
						$linkman=$supplier['linkman'];
						if($b['supplier']['linkman']!=''){
							$linkman=$this->highlight($supplier['linkman'], $b['supplier']['linkman']);
						}
						$email=$this->iif(strlen($supplier['email'])>30, mb_substr($supplier['email'], 0, 30, 'UTF-8').'...', $supplier['email']);
						if($b['supplier']['email']!=''){
							$email=$this->highlight($supplier['email'], $b['supplier']['email']);
						}
						$telephone=$supplier['telephone'];
						$mobile=$supplier['mobile'];
						if($b['supplier']['telephone']!=''){
							$telephone=$this->highlight($telephone, $b['supplier']['telephone']);
							$mobile=$this->highlight($mobile, $b['supplier']['mobile']);
						}
						$region='';
						if($supplier['regionid']>0){
							$region=$supplier['country'].' '.$supplier['state'].' '.$supplier['city'];
						}
						if($supplier['regionid']==$b['supplier']['regionid']){
							$region='<span class="red">'.$region.'</span>';
						}
						$cat='';
						foreach($b['category'] AS $category){
							if($category['id']==$supplier['categoryid']){
								$cat=$category['title'];
							}
						}
						$supplier['created']=date('Y-m-d',$supplier['created']);
						if($supplier['hidden']==0){
							if($supplier['killed']>0){
								$link='<a href="/s.php?module=psupplier&action=restore&supplierid='.$supplier['supplierid'].'&rt=list">恢复</a>';
							}else{
								$link='<a href="/s.php?module=psupplier&action=update&supplierid='.$supplier['supplierid'].'&rt=list">改</a>　<a href="/p.php?action=psupplier&supplierid='.$supplier['supplierid'].'" target="_blank">印</a>';
							}
						}
						
						if($b['layout']=='grid'){//列表
							$conten.='<li title="由 '.$supplier['creator'].' 建于 '.$supplier['created'].$this->iif($supplier['modifier']!='', '，'.$supplier['modifier'].' 改于 '.date('Y-m-d H:i',$supplier['modified']), '').'"'.$this->iif($i%4==0, ' class="end"', '').'>
							<div class="mmlistt">
								<span class="right normal">';
							if($supplier['killed']>0){
								$conten.='<a href="/s.php?module=psupplier&action=revival&supplierid='.$supplier['supplierid'].'&rt=list" onclick="return confirm(\'你确定要恢复这个物资 '.$supplier['supplierno'].' 吗？\');">恢复</a>';
							}else{
								$conten.='<a href="/s.php?module=psupplier&action=kill&supplierid='.$supplier['supplierid'].'&rt=list" onclick="return confirm(\'你确定要删除这个物资 '.$supplier['supplierno'].' 吗？\');">删</a> <a href="/s.php?module=psupplier&action=update&supplierid='.$supplier['supplierid'].'&rt=list">改</a>';
							}
							$conten.='</span>
							<span class="small">'.$supplier['attr'].'</span> <a href="/s.php?module=psupplier&action=view&supplierid='.$supplier['supplierid'].'">'.$supplier['supplierno'].'</a><br><span class="small gray right">'.$supplier['created'].'</span>'.$this->iif($supplier['attachs']>0, ' <span class="attachFile" title="有'.$supplier['attachs'].'个附件。"></span> ', '').$this->iif($supplier['images']>0, ' <span class="attachImage" title="有'.$supplier['images'].'个图片。"></span> ', '').$supplier['title'].'</div>
							<div class="mmlistb">
							<div title="'.$supplier['country'].' '.$supplier['state'].' '.$supplier['city'].'"><span class="small gray">地区：'.$supplier['country'].' '.$supplier['state'].' '.$supplier['city'].'</span></div>
							<div title="'.$supplier['address'].'"><span class="small gray">地址：'.$this->iif(strlen($supplier['address'])>30, mb_substr($supplier['address'], 0, 30, 'UTF-8').'...', $supplier['address']).'</span></div>
							<div><span class="small gray">邮箱：'.$email.'</span></div>
							<div><span class="small gray">电话：'.$telephone.$this->iif($supplier['mobile']!='', '<br>手机：'.$mobile, '').'</span></div>
							<div><span class="small gray">评分：'.$supplier['average'].'</span></div>
							<div class="small clear"><span class=right title="由 '.$supplier['creator'].' 建于 '.$supplier['created'].'">由 '.$supplier['creator'].' 建于 '.$supplier['created'].'</span></div></div></li>';
						}else{
							if($b['id']!=''){
								$id = $b['id'];
							}else{							
								$id = $i;
							}
							$conten .='<tr '.$this->rotateLine().' '.$this->iif($rank=="D",'style="background:#ff9999"','').'>
								<td>'.$id .'</td>
								<td class="middle bold">'.$supplier['supplierno'].'</td>
								<td title="'.$supplier['title'].'"><span class="small">'.$supplier['businessTitle'].'</span> '.$this->iif($supplier['attachs']>0, ' <span class="attachFile" title="有'.$supplier['attachs'].'个附件。"></span> ', '').$this->iif($supplier['images']>0, ' <span class="attachImage" title="有'.$supplier['images'].'个图片。"></span> ', '').'<a href="/s.php?module=psupplier&action=view&supplierid='.$supplier['supplierid'].'">'.$supplier['title'].'</a></td>
								<td>'.$supplier['abbr'].'</td>
								<td>'.$rank.'</td>
								<td>'.$cat.'</td>
								<td>'.$supplier['mainProduct'].'</td>
								<td>电话：'.$telephone.$this->iif($supplier['mobile']!='', '<br>手机：'.$mobile, '').'</td>
								<td>'.$supplier['address'].'</td>
								<td align="center">'.$link.'</td>
							</tr>';
						}
						$i++;
					}
					if($b['supplier']['rank']==''){
						if($b['layout']=='grid'){
							$conten .= '</ul>';
							//列表分页
							if(!($b['keywords']['rank'] or $b['keywords']['categoryid'] or $b['keywords']['mtitle'] or $b['keywords']['supplier'])){
							
							}
						}else{
							$conten .='</tbody></table>';
							//列表分页
							if(!($b['keywords']['rank'] or $b['keywords']['categoryid'] or $b['keywords']['mtitle'] or $b['keywords']['supplier'])){
								$counter = $this->DB->queryFirst("SELECT COUNT(supplierid) FROM `supplier` WHERE `supplier`.killed=0");
								$count = $counter['COUNT(supplierid)'];
								$pagesize = 100;
								$topage=$this->commonPaging(array('module'=>'psupplier','action'=>'list','count'=>$count,'pagesize'=>$pagesize,'startNum'=>$b['startNum']));
								$conten .= '<table width="100%"><tr><td align="center">'.$topage.'</td></tr></table>';
							}
						}
					}else{
							$conten .='';
					}
			}
		return $conten;
	 }
	 //将不连续数组转化为连续数组
	function sortArray($b){
		if(!is_array($b)) return ;
		sort($b);
		$num=count($b);//排序从0开始
		$temp=array();
		for($i=1;$i<=$num;$i++){
			 $temp[$i]=$b[$i-1];//排序从1开始
		}
		return $temp;
	}
}
?>
