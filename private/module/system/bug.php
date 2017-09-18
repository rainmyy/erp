<?php
//
class bug{
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
			case 'dopending':
				return $this->dopending();
			break;
			case 'pending':
				return $this->pending();
			break;
			case 'doresolve':
				return $this->doresolve();
			break;
			case 'resolve':
				return $this->resolve();
			break;
			case 'doverify':
				return $this->doverify();
			break;
			case 'verify':
				return $this->verify();
			break;
			case 'add':
				return $this->add();
			break;
			case 'doupdate':
				return $this->doupdate();
			break;
			case 'update':
				return $this->update();
			break;
			case 'insert':
				return $this->insert();
			break;
			case 'insertNote':
				return $this->insertNote();
			break;
			case 'viewCounter':
				return $this->viewCounter();
			break;
			case 'viewPro':
				return $this->viewPro();
			break;
			case 'insertPro':
				return $this->insertPro();
			break;
			case 'addPro':
				return $this->addPro();
			break;
			case 'dochangeProStatusStatus':
				return $this->dochangeProStatusStatus();
			break;
			case 'doupdatePro':
				return $this->doupdatePro();
			break;
			case 'updatePro':
				return $this->updatePro();
			break;
			case 'changeProStatus':
				return $this->changeProStatus();
			break;
			default:
				return $this->mmlist();
		}
	}
	//
	function view(){
		$bug=$this->kclass->DB->queryFirst("
				SELECT `bug`.bugid,`bug`.projectid,`bug`.reporterid,`bug`.handlerid,`bug`.priorityid,`bug`.severityid,`bug`.reproducibilityid,
				`bug`.bugstatusid,`bug`.resolutionid,`bug`.osid,`bug`.platformid,`bug`.title,`bug`.description,`bug`.stepstoreproduce,`bug`.remark,`bug`.opinion,
				`bug`.attachs,`bug`.cover,`bug`.images,`bug`.killed,`bug`.modified,`bug`.created, 
				`project`.title AS project,
				`priority`.title AS priority,
				`severity`.title AS severity,
				`reproducibility`.title AS reproducibility,
				`resolution`.title AS resolution,
				`os`.title AS os,
				`platform`.title AS platform,
				`bugstatus`.title AS status,`bugstatus`.entitle AS statusen,`bugstatus`.bgcolor AS bgcolor,
				u.username AS reporter,
				us.username AS handler
			FROM `bug`
			LEFT JOIN `project` ON (`project`.projectid=`bug`.projectid)
			LEFT JOIN `priority` ON (`priority`.priorityid=`bug`.priorityid)
			LEFT JOIN `severity` ON (`severity`.severityid=`bug`.severityid)
			LEFT JOIN `reproducibility` ON (`reproducibility`.reproducibilityid=`bug`.reproducibilityid)
			LEFT JOIN `bugstatus` ON (`bugstatus`.bugstatusid=`bug`.bugstatusid)
			LEFT JOIN `resolution` ON (`resolution`.resolutionid=`bug`.resolutionid)
			LEFT JOIN `os` ON (`os`.osid=`bug`.osid)
			LEFT JOIN `platform` ON (`platform`.platformid=`bug`.platformid)
			LEFT JOIN `user` AS u ON (u.userid=`bug`.reporterid)
			LEFT JOIN `user` AS us ON (us.userid=`bug`.handlerid)
			WHERE `project`.enabled=1 AND `bug`.bugid='".$this->kclass->input['bugid']."'
			LIMIT 0,1
		");
		if($bug){
			$bug['created']=date('Y-m-d H:i',$bug['created']);
			$bug['modified']=date('Y-m-d',$bug['modified']);
			if($bug['modified']=='1970-01-01')$bug['modified']='无';
			if($bug['remark']=='')$bug['remark']='无';
			if($bug['opinion']=='')$bug['opinion']='无';
			if($bug['handler']=='')$bug['handler']='无';
			if($bug['modified']!=0)$modify='，由'.$bug['modifier'].'于'.date('Y-m-d H:i',$bug['modified']).'修改';
			$attach = $this->kclass->getAttachs(array('module'=>'bug','mid'=>$bug['bugid']));
			if($attach!= false){
				$attachs = '<div class="clear">'.$attach.'</div>';
			}else{
				$attachs = '<div class="clear">暂无</div>';
			}
			$bugnotes=$this->kclass->DB->query("
				SELECT `bugnote`.bugnoteid, `bugnote`.reporterid, `bugnote`.remark, `bugnote`.creator, `bugnote`.created, 
				u.username AS reporter
				FROM `bugnote`
				LEFT JOIN `bug` ON (`bug`.bugid=`bugnote`.bugid)
				LEFT JOIN `user` AS u ON (u.userid=`bugnote`.reporterid)
				WHERE `bugnote`.killed=0 AND `bugnote`.bugid=".$this->kclass->input['bugid']."
			");
				$noteList['panel']='<form action="/s.php?module=bug&action=insertNote" name="bug" method="post"><input type="hidden" name="module" value="bug" /><input type="hidden" name="action" value="insertNote" /><input type="hidden" name="bugid" value="'.$bug[bugid].'" /><table class="hundred"><thead><th colspan="4">问题注释</th></thead><tbody>';
				if($noteList['num']=$this->kclass->DB->numRows()){
					$i=1;
					while($bugnote=$this->kclass->DB->fetchArray($bugnotes)){
						$bugnote['created']=date('Y-m-d H:i',$bugnote['created']);
						$noteList['panel'].='<tr class="'.$this->kclass->rotateLine().'">
							<tr class="odd"><td><span class="heavy">'.$i.'楼:</span></td></tr>
							<tr class="even"><td>'.$bugnote['remark'].'<span class=right>由 '.$bugnote['reporter'].' 发表于 '.$bugnote['created'].'</td></tr>';
							$i++;
					}
				}else{
					$noteList['off']='disabled';
				}
				$noteList['panel'].='<tr><td colspan=4 class="center"><textarea style="width:1154px;height:80px" name="bugnote" ></textarea></td></tr><tr><td colspan=4 class="center"><input type="submit" id="submitButton" value="  添加注释  " accesskey="n"></td></tr></tbody></table></form>';

$body=<<<EOF
<table class="hundred">
	<thead><tr><th colspan="4">查看问题详情</th></tr></thead>
	<tbody>
<tr class="odd">
	<td width="80">编号：</td><td width="450"><span class="heavy">NO.{$bug['bugid']}</span></td>
	<td width="85">项目：</td><td>{$bug['project']}</td>
</tr>
<tr class="even">
	<td>报告日期：</td><td>{$bug['created']}</td>
	<td>报告员：</td><td>{$bug['reporter']}</td>
</tr>
<tr><td colspan=4 class="center bold"></td></tr>
<tr class="odd">
	<td>状态：</td><td class="{$bug['bgcolor']}"><span class="bold">{$bug['status']}</span></td>
	<td>严重性：</td><td><span class="bold darkred">{$bug['severity']}</span></td>
</tr>
<tr class="even">
	<td>优先级别：</td><td><span class="bold darkred">{$bug['priority']}</span></td>
	<td>出现频率：</td><td><span class="bold darkred">{$bug['reproducibility']}</span></td>
</tr>
<tr class="odd">
	<td>操作系统：</td><td>{$bug['os']}</td>
	<td>浏览器：</td><td>{$bug['platform']}</td>
</tr>
<tr><td colspan=4 class="center bold"></td></tr>
EOF;
	if($this->kclass->purviews(array('module'=>'bug', 'action'=>'note', 'final'=>1))){
$body.=<<<EOF
<tr class="even">
	<td colspan=1>摘要：</td><td colspan=3>{$bug['title']}</td>
</tr>
EOF;
	}
$body.=<<<EOF
<tr class="odd">
	<td colspan=1>说明：</td><td colspan=3>{$bug['description']}</td>
</tr>
<tr class="even">
	<td colspan=1>重现步骤：</td><td colspan=3>{$bug['stepstoreproduce']}</td>
</tr>
<tr class="odd">
	<td colspan=1>附注：</td><td colspan=3>{$bug['remark']}</td>
</tr>
<tr><td colspan=4 class="center"></td></tr>
</tbody>
</table>
<table class="hundred">
<thead><th colspan="4">相关附件：</th></thead>
<tbody>
</tbody>
</table>
{$attachs}
<table class="hundred">
<thead><tr><th colspan="4">处理情况</th></tr></thead>
<tbody>
	<tr class="odd">
	<td width="80">处理状态：</td><td width="380"><span class="bold">{$bug['resolution']}</span></td>
	<td width="80">处理者：</td><td width="450">{$bug['handler']}</td>
	</tr>
	<tr class="even">
		<td>处理意见：</td><td>{$bug['opinion']}</td>
		<td>处理日期：</td><td>{$bug['modified']}</td>
	</tr>
</table>
{$noteList['panel']}
EOF;
		}else{
			$body='数据错误，很抱歉！';
		}
		if($bug['statusen']=='pending'){
			$handle='<a href="/s.php?module=bug&action=pending&bugid='.$bug[bugid].'">接受问题</a> &nbsp;&nbsp;&nbsp; <a href="/s.php?module=bug&action=update&bugid='.$bug[bugid].'">修改问题 </a>';
		}elseif($bug['statusen']=='resolve'){
			$handle='<a href="/s.php?module=bug&action=resolve&bugid='.$bug[bugid].'">处理问题</a>';
		}elseif($bug['statusen']=='verify'){
			$handle='<a href="/s.php?module=bug&action=verify&bugid='.$bug[bugid].'">验证问题</a>';
		}else{
			$handle='<a href="/s.php?module=bug&action=add">提交新问题</a>';
		}
		$this->kclass->page['title'].='查看问题';
		$this->kclass->page['onload'].='dc.tabhover();';
		$this->kclass->page['main']=$this->kclass->skin->dbody(array(
			'title'=>$this->baseurl.=' - <a href="/s.php?module=bug&action=list">问题列表</a> - 查看问题', 
			'right' => '<span class="small">操作：</span>  '.$handle,
			'body'=>$body));
	}
//
	function dopending(){
		$bugid=$this->kclass->input['bugid'];
		$bug=$this->kclass->DB->queryFirst("SELECT bugstatusid FROM `bug` WHERE `bug`.bugid='".$bugid."'");
		if($this->kclass->input['requestMethod'] != 'post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
		if(!$bug or $bug['bugstatusid']!=1){
			$e = '<li>问题当前状态有误,请从正确的页面进行提交。</li>';
			}
		}
		if($e){
			$this->kclass->messager(array(
				'title' => '接受处理问题',
				'text' => '您在接受处理问题的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		// update Bugstatus To Resolve
		$this->kclass->DB->query("
		UPDATE bug
		SET handlerid='".$this->kclass->user['userid']."',bugstatusid='2',modified='".TIMENOW."' 
		WHERE bugid='".$this->kclass->input['bugid']."'
		");
		$this->kclass->messager(array(
			'title' => '接受处理问题',
			'text' => '处理问题已接受成功!将返回未解决的问题列表页',
			'url' => '/s.php?module=bug&action=list&show=pending',
			'sec' => 2
		));
	}
	//
	function pending(){
		$bugid=$this->kclass->input['bugid'];
		$body=<<<EOF
<form action="/s.php?module=bug&action=dopending" name="pending" method="post">
<input type="hidden" name="module" value="bug" />
<input type="hidden" name="action" value="dopending" />
<input type="hidden" name="bugid" value="{$bugid}" />
<table width="50%">
<thead></tr><th colspan="4">您要接受处理这个问题吗?</th></tr></thead>
<tbody>
<tr class="odd" nohover>
<td align="center" colspan=4><input type="submit" id="submitButton" value="  接受问题  " accesskey="g"></td>
</tr>
</table>
<table class="tlist tsuite" align="center" width="100%">
<tbody>
</tbody>
</table>
</form>
EOF;
		$this->kclass->page['title'].='接受问题';
		$this->kclass->page['onload'].='dc.tabhover();';
		$this->kclass->page['main']=$this->kclass->skin->dbody(array(
			'title'=>$this->baseurl.=' - <a href="/s.php?module=bug&action=list">问题列表</a> - 接受问题', 
			'right' => '',
			'body'=>$body));
	}
	//
	function doresolve(){
		$bugid=$this->kclass->input['bugid'];
		$bug=$this->kclass->DB->queryFirst("SELECT bugstatusid FROM `bug` WHERE `bug`.bugid='".$bugid."'");
		if($this->kclass->input['requestMethod'] != 'post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
		if(!$bug or $bug['bugstatusid']!=2){
			$e = '<li>问题当前状态有误,请从正确的页面进行提交。</li>';
			}
		if($this->kclass->input['opinion']==''){
			$e = '<li>请填写问题处理意见。</li>';
			}
		}
		if($e){
			$this->kclass->messager(array(
				'title' => '提交处理结果',
				'text' => '您在提交处理结果的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		// update Bugresolution
		$this->kclass->DB->query("
		UPDATE bug
		SET handlerid='".$this->kclass->user['userid']."',bugstatusid='3',resolutionid='".$this->kclass->input['resolutionid']."',opinion='".$this->kclass->input['opinion']."',modified='".TIMENOW."' 
		WHERE bugid='".$this->kclass->input['bugid']."'
		");
		$bugid=$this->kclass->input['bugid'];
		$this->kclass->messager(array(
			'title' => '提交处理结果',
			'text' => '处理结果已提交成功!将返回处理中的问题列表页',
			'url' => '/s.php?module=bug&action=list&show=resolve',
			'sec' => 2
		));
	}
	//
	function resolve(){
		$resolution=$this->kclass->chooserResolution(array('name'=>'resolutionid','width'=>150,'hasBlank'=>0,'selectedid'=>1));
		$bugid=$this->kclass->input['bugid'];
		$body=<<<EOF
<form action="/s.php?module=bug&action=doresolve" name="resolve" method="post">
<input type="hidden" name="module" value="bug" />
<input type="hidden" name="action" value="doresolve" />
<input type="hidden" name="bugid" value="{$bugid}" />
<table width="50%">
<thead></tr><th colspan="4">请填写问题处理意见</th></tr></thead>
<tbody>
<tr class="even">
<td colspan=1>处理结果：<span class="red bold">*</span></td><td colspan=3>{$resolution}</td>
</tr>
<tr class="odd">
<td colspan=1>处理意见：<span class="red bold">*</span></td><td colspan=3><textarea style="width:400px;height:100px" name="opinion" ></textarea></td>
</tr>
<tr class="even" nohover>
<td align="center" colspan=4><input type="submit" id="submitButton" value="  提交处理  " accesskey="s"></td>
</tr>
</tbody>
</table>
</form>
EOF;
		$this->kclass->page['title'].='填写处理意见';
		$this->kclass->page['onload'].='dc.tabhover();';
		$this->kclass->page['main']=$this->kclass->skin->dbody(array(
			'title'=>$this->baseurl.=' - <a href="/s.php?module=bug&action=list">问题列表</a> - 填写处理意见', 
			'right' => '',
			'body'=>$body));
	}
	//
	function doverify(){
		$bugid=$this->kclass->input['bugid'];
		$bug=$this->kclass->DB->queryFirst("SELECT bugstatusid FROM `bug` WHERE `bug`.bugid='".$bugid."'");
		if($this->kclass->input['requestMethod'] != 'post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
		if(!$bug or $bug['bugstatusid']!=3){
			$e = '<li>问题当前状态有误,请从正确的页面进行提交。</li>';
			}
		}
		if($this->kclass->input['bugstatus']==''){
			$e = '<li>请选择验证处理结果.</li>';
		}
		if($e){
			$this->kclass->messager(array(
				'title' => '验证处理结果',
				'text' => '您在验证处理结果的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		// update Bugstatus To Resolved
		if($this->kclass->input['bugstatus']=='4'){
		$this->kclass->DB->query("
		UPDATE bug
		SET handlerid='".$this->kclass->user['userid']."',bugstatusid='4',modified='".TIMENOW."' 
		WHERE bugid='".$this->kclass->input['bugid']."'
		");
		}elseif($this->kclass->input['bugstatus']=='1'){
		$this->kclass->DB->query("
		UPDATE bug
		SET handlerid='".$this->kclass->user['userid']."',bugstatusid='1',resolutionid='7',modified='".TIMENOW."' 
		WHERE bugid='".$this->kclass->input['bugid']."'
		");
		}
		$bugid=$this->kclass->input['bugid'];
		$this->kclass->messager(array(
			'title' => '验证处理结果',
			'text' => '处理结果已验证成功!将返回待验证的问题列表页',
			'url' => '/s.php?module=bug&action=list&show=verify',
			'sec' => 2
		));
	}
	//
	function verify(){
		$bugid=$this->kclass->input['bugid'];
		$body=<<<EOF
<form action="/s.php?module=bug&action=doverify" name="verify" method="post">
<input type="hidden" name="module" value="bug" />
<input type="hidden" name="action" value="doverify" />
<input type="hidden" name="bugid" value="{$bugid}" />
<table width="50%">
<thead></tr><th colspan="4">请选择这个问题的验证结果:</th></tr></thead>
<tbody>
<tr class="even center" nohover>
<td>
<label for="c1" class="green"><input type="radio" id="c1" name="bugstatus" value="4">验证通过</label>　　
<label for="c0" class="red"><input type="radio" id="c0" name="bugstatus" value="1">验证不通过</label>
</td>
</tr>
<tr class="odd" nohover>
<td align="center" colspan=4><input type="submit" id="submitButton" value="  提交验证结果  " accesskey="g"></td>
</tr>
</table>
</form>
EOF;
		$this->kclass->page['title'].='提交验证结果';
		$this->kclass->page['onload'].='dc.tabhover();';
		$this->kclass->page['main']=$this->kclass->skin->dbody(array(
			'title'=>$this->baseurl.=' - <a href="/s.php?module=bug&action=list">问题列表</a> - 提交验证结果', 
			'right' => '',
			'body'=>$body));
	}
	//
	function insert(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			if($this->kclass->input['title'] == ''){
				$e .= '<li>请填写问题中的 摘要。</li>';
			}
			if($this->kclass->input['description'] == ''){
				$e .= '<li>请填写问题中的 说明。</li>';
			}
			if($this->kclass->input['stepstoreproduce'] == ''){
				$e .= '<li>请填写问题中的 问题重现步骤。</li>';
			}
		}
		if($e){
			$this->kclass->messager(array(
				'title' => '提交问题',
				'text' => '您在提交问题的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		// insert bug
		$this->kclass->DB->query("
			INSERT INTO `bug`(`projectid`, `reporterid`, `priorityid`, `severityid`, `reproducibilityid`, `bugstatusid`, `osid`, `platformid`, `title`, `description`, `stepstoreproduce`, `remark`,`creator`, `created`) VALUES ( '".$this->kclass->input['projectid']."','".$this->kclass->user['userid']."','".$this->kclass->input['priorityid']."','".$this->kclass->input['severityid']."','".$this->kclass->input['reproducibilityid']."',1,'".$this->kclass->input['osid']."','".$this->kclass->input['platformid']."','".$this->kclass->input['title']."','".$this->kclass->input['description']."','".$this->kclass->input['stepstoreproduce']."','".$this->kclass->input['remark']."','".$this->kclass->user['userid']."','".TIMENOW."')
		");
		$bugid=$this->kclass->DB->insertID();
		$this->kclass->updateAttachs(array('module'=>'bug', 'mid'=>$bugid));
		$this->kclass->messager(array(
			'title' => '提交问题',
			'text' => '问题 <b>'.$bugid.'</b> 已提交成功!返回问题列表页',
			'url' => '/s.php?module=bug&action=view&bugid='.$bugid,
			'sec' => 2
		));
	}
	//
	function add(){
		$project=$this->kclass->chooserProject(array('name'=>'projectid','width'=>200,'hasBlank'=>0,'selectedid'=>1));
		$priority=$this->kclass->chooserPriority(array('name'=>'priorityid','width'=>200,'hasBlank'=>0,'selectedid'=>1));
		$severity=$this->kclass->chooserSeverity(array('name'=>'severityid','width'=>200,'hasBlank'=>0,'selectedid'=>1));
		$reproducibility=$this->kclass->chooserReproducibility(array('name'=>'reproducibilityid','width'=>200,'hasBlank'=>0,'selectedid'=>1));
		$bugstatus=$this->kclass->chooserBugstatus(array('name'=>'bugstatusid','width'=>200,'hasBlank'=>0,'selectedid'=>1));
		$resolution=$this->kclass->chooserResolution(array('name'=>'resolutionid','width'=>200,'hasBlank'=>0,'selectedid'=>1));
		$os=$this->kclass->chooserOs(array('name'=>'osid','width'=>200,'hasBlank'=>0,'selectedid'=>1));
		$platform=$this->kclass->chooserPlatform(array('name'=>'platformid','width'=>200,'hasBlank'=>0,'selectedid'=>1));
		$upload=$this->kclass->upload(array('title'=>'相关附件：'));
$body=<<<EOF
<form action="/s.php?module=bug&action=insert" name="bug" method="post">
<input type="hidden" name="module" value="bug" />
<input type="hidden" name="action" value="insert" />
<table class="hundred">
<thead></tr><th colspan="4">填写问题详情</th></tr></thead>
<tbody>
<tr class="even">
<td>所属项目：<span class="red bold">*</span></td><td>{$project}</td>
</tr>
<tr class="odd">
<td>优先级别：<span class="red bold">*</span></td><td>{$priority}</td>
</tr>
<tr class="even">
<td>严重性：<span class="red bold">*</span></td><td>{$severity}</td>
</tr>
<tr class="odd">
<td>出现频率：<span class="red bold">*</span></td><td>{$reproducibility}</td>
</tr>
<tr class="even">
<td>浏览器：<span class="red bold">*</span></td><td>{$platform}</td>
</tr>
<tr class="odd">
<td>操作系统: <span class="red bold">*</span></td><td>{$os}</td>
<tr class="even">
<td>摘要：<span class="red bold">*</span></td><td><input type="text" size="48" value="" name="title"></td>
</tr>
<tr class="odd">
<td>说明：<span class="red bold">*</span></td><td><textarea style="width:444px;height:120px" name="description" ></textarea></td>
</tr>
<tr class="even">
<td>问题重现步骤：<span class="red bold">*</span></td><td><textarea style="width:444px;height:120px" name="stepstoreproduce" ></textarea></td>
</tr>
<tr class="odd">
<td>附注：</td><td><textarea style="width:444px;height:70px" name="remark" ></textarea></td>
</tr>
</table>
{$upload}
<table class="tlist tsuite" align="center" width="100%">
<tbody>
<tr class="odd" nohover>
<td align="center" colspan=4><input type="submit" id="submitButton" value="  提交报告  " accesskey="s"></td>
</tr>
</tbody>
</table>
</form>
EOF;
		$this->kclass->page['title'].='提交问题';
		$this->kclass->page['onload'].='dc.tabhover();';
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - 提交问题', 'right'=>'<a href="/s.php?module=bug&action=list">查看问题</a>','body'=>$body));
	}
		// 
	function doupdate(){
		$bugid=$this->kclass->input['bugid'];
		$bug=$this->kclass->DB->queryFirst("SELECT bugstatusid FROM `bug` WHERE `bug`.bugid='".$bugid."'");
		if($this->kclass->input['requestMethod'] != 'post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
		if(!$bug or $bug['bugstatusid']!=1){
			$e = '<li>问题当前状态有误,请从正确的页面进行提交。</li>';
			}
			if($this->kclass->input['title'] == ''){
				$e .= '<li>请填写问题中的 摘要。</li>';
			}
			if($this->kclass->input['description'] == ''){
				$e .= '<li>请填写问题中的 说明。</li>';
			}
			if($this->kclass->input['stepstoreproduce'] == ''){
				$e .= '<li>请填写问题中的 问题重现步骤。</li>';
			}
		}
		if($e){
			$this->kclass->messager(array(
				'title' => '修改问题',
				'text' => '您修改问题的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		// update bug
		$bugid=$this->kclass->input['bugid'];
		$this->kclass->DB->query("
			UPDATE `bug`
			SET `projectid`='".$this->kclass->input['projectid']."',
					`reporterid`='".$this->kclass->user['userid']."',
					`priorityid`='".$this->kclass->input['priorityid']."',
					`severityid`='".$this->kclass->input['severityid']."',
					`reproducibilityid`='".$this->kclass->input['reproducibilityid']."',
					`osid`='".$this->kclass->input['osid']."',
					`platformid`='".$this->kclass->input['platformid']."',
					`title`='".$this->kclass->input['title']."',
					`description`='".$this->kclass->input['description']."',
					`stepstoreproduce`='".$this->kclass->input['stepstoreproduce']."',
					`remark`='".$this->kclass->input['remark']."',
					`creator`='".$this->kclass->user['userid']."',
					`created`='".TIMENOW."'
			WHERE `bugid`='".$bugid."'
		");
		$this->kclass->updateAttachs(array('module'=>'bug', 'mid'=>$bugid));
		$this->kclass->messager(array(
			'title' => '修改问题',
			'text' => '问题NO.<b>'.$bugid.'</b> 已修改成功!返回问题列表页',
			'url' => '/s.php?module=bug&action=view&bugid='.$bugid,
			'sec' => 2
		));
	}
	// 
	function update(){
		$bugid=$this->kclass->input['bugid'];
		if($bugid<=0 OR !$bug = $this->kclass->DB->queryFirst("SELECT * FROM `bug` WHERE bugid='".$bugid."'")){
			$this->kclass->boinkIt('/s.php?module=bug&action=list');
		}
		$project=$this->kclass->chooserProject(array('name'=>'projectid','width'=>200,'hasBlank'=>0,'selectedid'=>$bug['projectid']));
		$priority=$this->kclass->chooserPriority(array('name'=>'priorityid','width'=>200,'hasBlank'=>0,'selectedid'=>$bug['priorityid']));
		$severity=$this->kclass->chooserSeverity(array('name'=>'severityid','width'=>200,'hasBlank'=>0,'selectedid'=>$bug['severityid']));
		$reproducibility=$this->kclass->chooserReproducibility(array('name'=>'reproducibilityid','width'=>200,'hasBlank'=>0,'selectedid'=>$bug['reproducibilityid']));
		$bugstatus=$this->kclass->chooserBugstatus(array('name'=>'bugstatusid','width'=>200,'hasBlank'=>0,'selectedid'=>$bug['bugstatusid']));
		$resolution=$this->kclass->chooserResolution(array('name'=>'resolutionid','width'=>200,'hasBlank'=>0,'selectedid'=>$bug['resolutionid']));
		$os=$this->kclass->chooserOs(array('name'=>'osid','width'=>200,'hasBlank'=>0,'selectedid'=>$bug['osid']));
		$platform=$this->kclass->chooserPlatform(array('name'=>'platformid','width'=>200,'hasBlank'=>0,'selectedid'=>$bug['platformid']));
		$upload = $this->kclass->upload(array('title'=>'相关附件：', 'module'=>'bug', 'mid'=>$bug['bugid']));
$body=<<<EOF
<form action="/s.php?module=bug&action=doupdate" name="bug" method="post">
<input type="hidden" name="module" value="bug" />
<input type="hidden" name="action" value="doupdate" />
<input type="hidden" name="bugid" value="{$bugid}" />
<table class="hundred">
<thead></tr><th colspan="4">修改问题 NO.{$bugid}</th></tr></thead>
<tbody>
<tr class="even">
<td>所属项目：<span class="red bold">*</span></td><td>{$project}</td>
</tr>
<tr class="odd">
<td>优先级别：<span class="red bold">*</span></td><td>{$priority}</td>
</tr>
<tr class="even">
<td>严重性：<span class="red bold">*</span></td><td>{$severity}</td>
</tr>
<tr class="odd">
<td>出现频率：<span class="red bold">*</span></td><td>{$reproducibility}</td>
</tr>
<tr class="even">
<td>浏览器：<span class="red bold">*</span></td><td>{$platform}</td>
</tr>
<tr class="odd">
<td>操作系统: <span class="red bold">*</span></td><td>{$os}</td>
<tr class="even">
<td>摘要：<span class="red bold">*</span></td><td><input type="text" size="48" name="title" value="{$bug['title']}"></td>
</tr>
<tr class="odd">
<td>说明：<span class="red bold">*</span></td><td><textarea style="width:444px;height:100px" name="description">{$bug['description']}</textarea></td>
</tr>
<tr class="even">
<td>问题重现步骤：<span class="red bold">*</span></td><td><textarea style="width:444px;height:100px" name="stepstoreproduce">{$bug['stepstoreproduce']}</textarea></td>
</tr>
<tr class="odd">
<td>附注：</td><td><textarea style="width:444px;height:50px" name="remark">{$bug['remark']}</textarea></td>
</tr>
</table>
{$upload}
<table class="tlist tsuite" align="center" width="100%">
<tbody>
<tr class="odd" nohover>
<td align="center" colspan=4><input type="submit" id="submitButton" value="  提交修改  " accesskey="s"></td>
</tr>
</tbody>
</table>
</form>
EOF;
		$this->kclass->page['title'].='修改问题';
		$this->kclass->page['onload'].='dc.tabhover();';
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - <a href="/s.php?module=bug&action=list">查看问题</a> - 修改问题', 'right'=>'<a href="/s.php?module=bug&action=list">返回问题列表</a>','body'=>$body));
	}
	//
	function insertNote(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			if($this->kclass->input['bugnote'] == ''){
				$e .= '<li>添加的问题注释为空,请返回重新添加.</li>';
			}
		}
		if($e){
			$this->kclass->messager(array(
				'title' => '添加问题注释',
				'text' => '您在添加问题注释的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 3
			));
		}
		// insert bugnote
		$this->kclass->DB->query("
			INSERT INTO `bugnote`( `bugid`, `reporterid`, `remark`,`created`) VALUES ( '".$this->kclass->input['bugid']."','".$this->kclass->user['userid']."','".$this->kclass->input['bugnote']."','".TIMENOW."')
		");
		$bugid=$this->kclass->input['bugid'];
		$this->kclass->messager(array(
			'title' => '添加问题注释',
			'text' => '问题注释已提交成功!返回问题详情页',
			'url' => '/s.php?module=bug&action=view&bugid='.$bugid,
			'sec' => 1
		));
	}
	//
	function viewCounter(){
		$projects=$this->kclass->DB->query("
			SELECT `project`.projectid,`project`.title,`project`.remark,
			`prostatus`.title AS prostatus
			FROM `project`
			LEFT JOIN  `prostatus` ON(`prostatus`.prostatusid=`project`.prostatusid)
			WHERE enabled=1
		");
		if($this->kclass->DB->numRows()){
			while($project=$this->kclass->DB->fetchArray($projects)){
				$bugtr=array();
				$bugstatuses=$this->kclass->DB->query("
					SELECT bugstatusid,title,entitle
					FROM `bugstatus`
				");
				if($bugList['num']=$this->kclass->DB->numRows()){
					$bugList['panel'].='<table class="hundred center"><thead><th colspan="6">项目:'.$project['title'].'</th></thead><tbody><tr class="even"><td width="175">简介:</td><td colspan=2>'.$project['remark'].'</td><td width="175" class="grayBg">状态:</td><td colspan=2>'.$project['prostatus'].'</td></tr>';
					$i=1;	
					while($bugstatus=$this->kclass->DB->fetchArray($bugstatuses)){
						$bug=$this->kclass->DB->queryFirst("
							SELECT COUNT(bugid) AS count
							FROM `bug` 
							WHERE bugstatusid='".$bugstatus['bugstatusid']."' AND projectid='".$project['projectid']."'
							LIMIT 0,1
						");
						$bugtr['panelstatus'].='<td width="175"><a href="/s.php?module=bug&action=list&show='.$bugstatus['entitle'].'">'.$bugstatus['title'].'</a></td>';
						$bugtr['panelcount'].='<td>'.$bug['count'].'</td>';
						$bugtr['sum']+=$bug['count'];
						$i++;
					}
					$bugList['panel'].='<tr class="odd" nohover>'.$bugtr['panelstatus'].'<td>合计</td></tr><tr class="even">'.$bugtr['panelcount'].'<td>'.$bugtr['sum'].'</td></tr></tbody></table>';
				}else{
					$bugList['off']='disabled';
				}
			}
		}else{
			$bugList['panel']='暂无项目记录!';
		}		
$body=<<<EOF
{$bugList['panel']}
EOF;
	$this->kclass->page['title'].='问题统计';
	$this->kclass->page['onload'].='dc.tabhover();';
	$this->kclass->page['main']=$this->kclass->skin->dbody(array(
			'title'=>$this->baseurl.=' - 问题统计', 
			'right' => '<a href="/s.php?module=bug&action=add">提交新问题</a>',
			'body'=>$body));
	}
	//
	function viewPro(){
		$projects=$this->kclass->DB->query("
			SELECT `project`.projectid,`project`.title,`project`.remark,`project`.enabled,`project`.created,
			`prostatus`.title AS prostatus
			FROM `project`
			LEFT JOIN  `prostatus` ON(`prostatus`.prostatusid=`project`.prostatusid)
		");
		if($this->kclass->DB->numRows()){
			$proList['panel']='<table><thead><tr><th>项目</th><th>简介</th><th>状态</th><th>启用</th><th align="center">创建日期</th><th align="center" class="small">操作</th></tr></thead><tbody>';
			while($project=$this->kclass->DB->fetchArray($projects)){	
				$proList['panel'] .= '<tr class="'.$this->kclass->iif($this->kclass->rotate(), 'odd', 'even');
				$proList['panel'] .= '"><td class="middle bold"><a name="'.$project['projectid'].'"></a>'.$project['title'].'</td><td class="bold">'.$project['remark'].'</td><td>'.$project['prostatus'].'</td><td align="right" class="small">'.$project['enabled'].'</td><td class="small gray">'.date('Y-m-d', $project['created']).'</td>';
				if($project['enabled']==1){
					$proList['panel'] .= '<td align="right" class="small"><a href="/s.php?module=bug&action=changeProStatus&projectid='.$project['projectid'].'">关闭</a> <a href="/s.php?module=bug&action=updatePro&projectid='.$project['projectid'].'">修改</a></td></tr>';
				}else{
					$proList['panel'] .= '<td align="right" class="small"><a href="/s.php?module=bug&action=changeProStatus&projectid='.$project['projectid'].'">启用</a> <a href="/s.php?module=bug&action=updatePro&projectid='.$project['projectid'].'">修改</a></td></tr>';
				}
			}
			$proList['panel'] .= '</tbody></table>';
		}else{
			$proList['panel'] = '暂无相关记录。';
		}	
$body=<<<EOF
{$proList['panel']}
EOF;
	$this->kclass->page['title'].='项目管理';
	$this->kclass->page['onload'].='dc.tabhover();';
	$this->kclass->page['main']=$this->kclass->skin->dbody(array(
			'title'=>$this->baseurl.=' - 项目管理', 
			'right' => '<a href="/s.php?module=bug&action=addPro">新建项目</a>',
			'body'=>$body));
	}
//
	function insertPro(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			if($this->kclass->input['title'] == ''){
				$e = '<li>请填写项目的 名称。</li>';
			}else{
				if($this->kclass->DB->queryFirst("SELECT projectid FROM `project` WHERE `title`='".$this->kclass->input['title']."'")){
					$e = '<li>您要新建的项目 【'.$this->kclass->input['title'].'】 已经存在。</li>';
				}
			}
			if($this->kclass->input['enabled'] == ''){
				$e .= '<li>请选择是否启用项目。</li>';
			}
			if($this->kclass->input['prostatusid'] == ''){
				$e .= '<li>请选择项目中的 状态。</li>';
			}
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '新建项目',
				'text' => '您在新建项目的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 2
			));
		}
		$this->kclass->DB->query("
			INSERT INTO `project` ( `prostatusid` , `title`, `remark` , `enabled` , `created`)
			VALUES (
			'".$this->kclass->input['prostatusid']."', '".$this->kclass->input['title']."', '".$this->kclass->input['remark']."', '".$this->kclass->input['enabled']."', '".TIMENOW."'
			)
		");
		$this->kclass->messager(array(
			'title' => '新建项目',
			'text' => '项目 <b>'.$this->kclass->input['title'].'</b> 已新建成功!',
			'url' => '/s.php?module=bug&action=viewPro',
			'sec' => 2
		));
	}
//
	function addPro(){
		$prostatus=$this->kclass->chooserProstatus(array('name'=>'prostatusid','width'=>100,'hasBlank'=>0,'selectedid'=>1));
$body = <<<EOF
<form action="/s.php?module=bug&action=insertPro" name="bug" method="post">
<input type="hidden" name="module" value="bug">
<input type="hidden" name="action" value="insertPro">
<table><thead>
<tr>
	<th colspan="4">新建项目:</th>
</tr>
</thead>
<tbody>
<tr class="even">
	<td>名称：<span class="red bold">*</span></td>
	<td><input type="text" style="width:270px" size="20" name="title"></td>
</tr>
<tr class="odd">
	<td>简介：</td>
	<td><input type="text" style="width:270px" size="20" name="remark"></td>
</tr>
<tr class="even">
	<td>状态：<span class="red bold">*</span></td>
	<td>{$prostatus}</td>
</tr>
<tr class="odd">
	<td>启用：<span class="red bold">*</span></td>
	<td>&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="enabled" value="1">是&nbsp;&nbsp;&nbsp;<input type="radio" name="enabled" value="0">否</td>
</tr>
<tr class="even">
	<td align="center" colspan="4">
		<input type="submit" id="submitButton" value="      保存      " accesskey="s">　　
		<input type="reset" value="      复位      ">
	</td>
</tr>
</tbody>
</table>
</form>
EOF;
		$this->kclass->page['onload'] = 'dc.tabhover()';
		$this->kclass->page['title'] .= ' - 新建项目';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . '  - <a href="?module=bug&action=viewPro">项目管理</a> - 新建项目', 'right' => '<a href="?module=bug&action=viewPro">返回列表</a>', 'body'=>$body));
}
//
	function dochangeProStatusStatus(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			if($this->kclass->input['confirm'] == ''){
				$e = '<li>请选择项目的 启用状态。</li>';
			}
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '关闭项目',
				'text' => '您在关闭项目的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 2
			));
		}
		if($this->kclass->input['confirm']=='1'){
			$this->kclass->DB->query("
			UPDATE `project`
			SET enabled = '1'
			WHERE projectid='".$this->kclass->input['projectid']."'
		");
		}elseif($this->kclass->input['confirm']=='0'){
			$this->kclass->DB->query("
			UPDATE `project`
			SET enabled= '0'
			WHERE projectid='".$this->kclass->input['projectid']."'
		");
		}
		$this->kclass->messager(array(
			'title' => '项目启用',
			'text' => '项目启用状态修改成功!',
			'url' => '/s.php?module=bug&action=viewPro',
			'sec' => 2
		));
	}
//
	function changeProStatus(){
	if($this->kclass->input['projectid']<=0){
			$this->kclass->boinkIt('/s.php?module=bug&action=viewPro');
		}
		$project = $this->kclass->DB->queryFirst("
			SELECT title
			FROM `project`
			WHERE projectid='".$this->kclass->input['projectid']."'
		");
$body = <<<EOF
<form action="/s.php?module=bug&action=dochangeProStatusStatus" name="bug" method="post">
<input type="hidden" name="module" value="bug">
<input type="hidden" name="action" value="dochangeProStatusStatus">
<input type="hidden" name="projectid" value="{$this->kclass->input['projectid']}">
<table width="35%"><thead>
<tr>
	<th>请选择项目<span class="big bold">{$project['title']}</span>的启用状态</th>
</tr>
</thead>
<tbody>
<tr class="odd">
	<td align="center">
		<label for="c1" class="red"><input type="radio" id="c1" name="confirm" value="1">启用</label>　　
		<label for="c0" class="green"><input type="radio" id="c0" name="confirm" value="0">关闭</label>
	</td>
</tr>
<tr class="even">
	<td align="center">
		<input type="submit" value="   提交   " accesskey="s">
	</td>
</tr>
</tbody>
</table>
</form>
EOF;
		$this->kclass->page['title'] .= '  项目启用  ';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - <a href="?module=bug&action=viewPro">项目管理</a> - 启用状态', 'right' => '<a href="/s.php?module=bug&action=viewPro">返回列表</a>', 'body'=>$body));
	}
//
	function doupdatePro(){
		if($this->kclass->input['requestMethod'] != 'post'){
			$e = '<li>请从正确的页面进行提交，如此问题持续出现，请联系网站管理员。</li>';
		}else{
			if($this->kclass->input['title'] == ''){
				$e = '<li>请填写项目的 名称。</li>';
			}else{
				if($this->kclass->DB->queryFirst("
						SELECT projectid 
						FROM `project` 
						WHERE title='".$this->kclass->input['title']."' AND projectid !='".$this->kclass->input['projectid']."'
						")){
					$e = '<li>您修改的项目 【'.$this->kclass->input['title'].'】 已经存在。</li>';
				}
			}
			if($this->kclass->input['prostatusid'] == ''){
				$e .= '<li>请选择项目中的 状态。</li>';
			}
		}
		if(isset($e)){
			$this->kclass->messager(array(
				'title' => '修改项目',
				'text' => '您在修改项目的过程中有以下错误：<ul>'.$e.'</ul>',
				'url' => 'javascript:history.back()',
				'sec' => 2
			));
		}
		$this->kclass->DB->query("
			UPDATE `project`
			SET title = '".$this->kclass->input['title']."',  
				  remark= '".$this->kclass->input['remark']."', 
				  prostatusid = '".$this->kclass->input['prostatusid']."'
			WHERE projectid='".$this->kclass->input['projectid']."'
		");
		$this->kclass->messager(array(
			'title' => '修改项目',
			'text' => '项目 <b>'.$this->kclass->input['title'].'</b> 已修改成功!',
			'url' => '/s.php?module=bug&action=viewPro',
			'sec' => 2
		));
	}
//
	function updatePro(){
		$prostatus=$this->kclass->chooserProstatus(array('name'=>'prostatusid','width'=>100,'hasBlank'=>0,'selectedid'=>1));
		if($this->kclass->input['projectid']<=0 OR !$project = $this->kclass->DB->queryFirst("SELECT * FROM `project` WHERE projectid='".$this->kclass->input['projectid']."'")){
			$this->kclass->boinkIt('/s.php?module=bug&action=viewPro');
		}
$body = <<<EOF
<form action="/s.php?module=bug&action=doupdatePro" name="bug" method="post">
<input type="hidden" name="module" value="bug">
<input type="hidden" name="action" value="doupdatePro">
<input type="hidden" name="projectid" value="{$this->kclass->input['projectid']}">
<table><thead>
<tr>
	<th colspan="4">修改项目:<span class="big bold">{$project['title']}</span></th>
</tr>
</thead>
<tbody>
<tr class="even">
	<td>名称：<span class="red bold">*</span></td>
	<td><input type="text" style="width:270px" size="20" name="title" value="{$project['title']}"></td>
</tr>
<tr class="odd">
	<td>简介：</td>
	<td><input type="text" style="width:270px" size="20" name="remark" value="{$project['remark']}"></td>
</tr>
<tr class="even">
	<td>状态：<span class="red bold">*</span></td>
	<td>{$prostatus}</td>
</tr>
<tr class="even">
	<td align="center" colspan="4">
		<input type="submit" id="submitButton" value="      保存      " accesskey="s">　　
		<input type="reset" value="      复位      ">
	</td>
</tr>
</tbody>
</table>
</form>
EOF;
		$this->kclass->page['onload'] = 'dc.tabhover()';
		$this->kclass->page['title'] .= ' 修改项目 ';
		$this->kclass->page['main'] = $this->kclass->skin->dbody(array('title' => $this->baseurl . ' - <a href="?module=bug&action=viewPro">项目管理</a> - 修改项目', 'right' => '<a href="/s.php?module=bug&action=viewPro">返回列表</a>', 'body'=>$body));
	}
	//
	function mmlist(){
		$condition='`bug`.killed=0 AND `project`.enabled=1';
		if($this->kclass->input['show']!=''){
			$condition.=" AND `bugstatus`.entitle='".$this->kclass->input['show']."'";
		}else{
			$condition.=" AND 1=1";
		}
		$orderby=$this->kclass->orderby(array('module'=>'bug','direction'=>'desc','orderby'=>'created', 'default'=>'bugid', 'serial'=>array(array('title'=>'编号', 'field'=>'bugid','word'=>'no'), array('title'=>'时间', 'field'=>'created'),array('title'=>'级别', 'field'=>'priorityid'),array('title'=>'状态', 'field'=>'bugstatusid'),array('title'=>'项目', 'field'=>'projectid')),'appendUrl'=>array('show','page')));
		$bugs=$this->kclass->DB->query("
			SELECT `bug`.bugid,`bug`.projectid,`bug`.reporterid,`bug`.handlerid,`bug`.priorityid,`bug`.severityid,`bug`.bugstatusid,`bug`.title,`bug`.attachs,`bug`.images,`bug`.killed,`bug`.modified,`bug`.created, 
			`project`.title AS project,
			`priority`.title AS priority,
			`severity`.title AS severity,
			`resolution`.title AS resolution,
			`bugstatus`.title AS status,`bugstatus`.entitle AS statusen,`bugstatus`.bgcolor AS bgcolor,
			u.username AS reporter,
			us.username AS handler
			FROM `bug`
			LEFT JOIN `project` ON (`project`.projectid=`bug`.projectid)
			LEFT JOIN `priority` ON (`priority`.priorityid=`bug`.priorityid)
			LEFT JOIN `severity` ON (`severity`.severityid=`bug`.severityid)
			LEFT JOIN `resolution` ON (`resolution`.resolutionid=`bug`. resolutionid)
			LEFT JOIN `bugstatus` ON (`bugstatus`.bugstatusid=`bug`.bugstatusid)
			LEFT JOIN `user` AS u ON (u.userid=`bug`.reporterid)
			LEFT JOIN `user` AS us ON (us.userid=`bug`.handlerid)
			WHERE ".$condition." 
			ORDER BY ".$orderby['sql']." 
		");
		if($this->kclass->DB->numRows()){
			$body='<ul class="mmlist clear" id="mmlist">';
			$i=1;
			while($bug=$this->kclass->DB->fetchArray($bugs)){
				$bug['modified']=date('Y-m-d',$bug['modified']);
				$bug['created']=date('Y-m-d',$bug['created']);
				$body .= '<li title="由 '.$bug['reporter'].' 提交于 '.$bug['created'].$this->kclass->iif($bug['handler']!='', '，'.$bug['handler'].' 处理于 '.$bug['modified'], '').'"'.$this->kclass->iif($i%4==0, ' class="end"', '').'><div class="mmlistt"><span class="right normal">';
				if($bug['statusen']=='pending'){
					$body.='<a href="/s.php?module=bug&action=update&bugid='.$bug['bugid'].'">改</a>&nbsp;&nbsp;';
				}
				if($bug['statusen']!='verified'){
				$body.='<a href="/s.php?module=bug&action='.$bug['statusen'].'&bugid='.$bug['bugid'].'">操作</a>';
				}
				$body.='</span><a href="/s.php?module=bug&action=view&bugid='.$bug['bugid'].'"><span class="middle bold">'.$bug['bugid'].'<span class="small"> <span class="small">('.$bug['project'].')</span></a><br><span class="small  right">'.$bug['statusen'].'</span>'.$this->kclass->iif($bug['attachs']>0, ' <span class="attachFile" title="有'.$bug['attachs'].'个附件。"></span> ', '').$this->kclass->iif($bug['images']>0, ' <span class="attachImage" title="有'.$bug['images'].'个图片。"></span> ', '').'<span class="small gray">提交日期：</span>'.$bug['created'].'</div><div class="mmlistb"><div title="'.$bug['title'].'"><span class="small lowred">摘要：'.$bug['title'].'</span></div><div title="'.$bug['title'].'"><span class="small '.$bug['bgcolor'].' lowred">状态：'.$bug['status'].'</span></div><div title="'.$bug['status'].'"><span class="small lowred">优先级别：'.$bug['priority'].'</span></div><div title="'.$bug['severity'].'"><span class="small lowred">严重性：'.$bug['severity'].'</span></div><div title="'.$bug['resolution'].'"><span class="small lowred">处理状态：'.$bug['resolution'].'</span></div><div class="small clear"><span class=right title="由 '.$bug['reporter'].' 提交于 '.$bug['created'].'">由 '.$bug['reporter'].' 提交于 '.$bug['created'].$this->kclass->iif($bug['handler']!='', '</br>由 '.$bug['handler'].' 处理于 '.$bug['modified'], '').'</span></div></div></li>';
				$i++;
			}
			$body.='</ul>';
		}else{
			$body='暂无相关问题记录';
		}
		$this->kclass->page['title'].='问题列表';
		$this->kclass->page['onload'].='dc.listhover();';
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - 查看问题','right' => '<span class="small">显示方式：</span> <a href="/s.php?module=bug&action=list&show=pending">未解决</a> &nbsp;&nbsp;<a href="/s.php?module=bug&action=list&show=resolve">处理中</a> &nbsp;&nbsp;<a href="/s.php?module=bug&action=list&show=verify">待验证</a> &nbsp;&nbsp;<a href="/s.php?module=bug&action=list&show=verified">已解决</a>&nbsp;&nbsp;<a href="/s.php?module=bug&action=list">显示所有 </a>| <span class="small"> 排序：</span>'.$orderby['link'].'','body'=>$body));
	}
	//
}

?>