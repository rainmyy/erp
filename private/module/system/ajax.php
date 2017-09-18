<?php
//
class ajax{
	function autoRun(){
//		$this->p = '<pre>'.print_r($this->kclass->input, 1).'</pre>';
		switch($this->kclass->input['action']){
			case 'upload':
				$this->upload();
				break;
			case 'region':
				$this->region();
				break;
			case 'showUploaded':
				$this->showUploaded();
				break;
			case 'materialVersion':
				$this->materialVersion();
				break;
			case 'chooserMaterial':
				$this->chooserMaterial();
				break;
			case 'chooserSupplier':
				$this->chooserSupplier();
				break;
			case 'chooserCustomer':
				$this->chooserCustomer();
				break;
			case 'chooseProductAttribute':
				$this->chooseProductAttribute();
				break;
			case 'tabsItem':
				$this->tabsItem();
				break;
			case 'tableItemPage':
				$this->tableItemPage();
				break;
			case 'bomPage':
				$this->bomPage();
				break;
			case 'getProductEntitle':
				$this->getProductEntitle();
				break;
			case 'findCustomer':
				$this->findCustomer();
				break;
			case 'returnStr':
				$this->returnCountryAndCustomerStr();
				break;
			case 'showParts':
				$this->showParts();
				break;
			case 'chooserParts':
				$this->chooserParts();
				break;
			case 'submitParts':
				$this->submitParts();
				break;
			case 'updateNo':
				$this->updateNo();
				break;
			case 'notification':
				$this->notification();
				break;
			case 'addServiceNo':  // 新增快速服务代码
				$this->addServiceNo();
				break;
			case 'addBarcode':  // 新增流水号条码
				$this->addBarcode();
				break;
			case 'getOrderList':  // 获得订单列表
				$this->getOrderList();
				break;
			case 'getOrderItem':  // 获得订单明细
				$this->getOrderItem();
				break;
			case 'showChildren':  // 显示bomtr
				$this->showChildren();
				break;
			case 'addColumn':  // bom计算器新增一列
				$this->addColumn();
				break;
			case 'getBomChildren':  // 获得bom的所有子节点
				$this->getBomChildren();
				break;
			case 'packingsize':  // 获得bom的所有子节点
				$this->packingsize();
				break;
			case 'addInventoryItem': 
				$this->addInventoryItem();
			default:
				$this->show();
				break;
		}

		$this->printIt();
	}
	//
	function region(){
		if($this->kclass->input['countryid']>0){
			$cs = $this->kclass->DB->query("SELECT regionid, state, enstate, cnum FROM region WHERE countryid='".$this->kclass->input['countryid']."' AND provinceid=0 ORDER BY enstate ASC");
			if($this->kclass->DB->numRows()){
				$this->p = '<select id="provinceid" name="provinceid" onchange="dc.region.city(this);" style="width:115px"><option value="-1"> </option>';
				while($c=$this->kclass->DB->fetchArray($cs)){
					$this->p .= '<option value="'.$c['regionid'].'">'.substr($c['enstate'],0,1).' '.$c['state'].'</option>';
				}
				$this->p .= '</select>';
			}else{
				$this->p = 'false';
			}
		}elseif($this->kclass->input['provinceid']>0){
			$cs = $this->kclass->DB->query("SELECT regionid, city, encity FROM region WHERE provinceid='".$this->kclass->input['provinceid']."' ORDER BY encity ASC");
			if($this->kclass->DB->numRows()){
				$this->p = '<select id="cityid" name="cityid" style="width:115px" onchange="dc.region.cityChange(this)"><option value="-1"> </option>';
				while($c=$this->kclass->DB->fetchArray($cs)){
					$this->p .= '<option value="'.$c['regionid'].'">'.substr($c['encity'],0,1).' '.$c['city'].'</option>';
				}
				$this->p .= '</select>';
			}else{
				$this->p = 'false';
			}
		}else{
			$this->p='';
		}
	}
	//
	function upload(){
		if(isset($_FILES['Filedata']) AND is_uploaded_file($_FILES['Filedata']["tmp_name"]) AND $_FILES['Filedata']["error"] == 0){
			$f['ext'] = strtolower(str_replace(".", "", substr($_FILES['Filedata']['name'], strrpos($_FILES['Filedata']['name'], '.'))));
			$f['filename'] = TIMENOW.str_replace('0.', '', MICRO_TIME);
			$f['fullname'] = $f['filename'].'.'.$f['ext'];
			$f['folder'] = date('Ym', TIMENOW);
			$f['path'] = $this->kclass->vars['attachs'].$f['folder'].'/'.$f['fullname'];
			if(!is_dir($this->kclass->vars['attachs'].$f['folder'])){
				@mkdir($this->kclass->vars['attachs'].$f['folder']);
			}
			move_uploaded_file($_FILES["Filedata"]["tmp_name"], $f['path']);//移动上传文件到附件中
			if(in_array($f['ext'], array('gif','jpeg','jpg','png'))){//判断是否为图片
				$att = getimagesize($f['path']);
				if(is_array($att)){
					$img=array('name'=>$f['folder'].'/'.$f['fullname'], 'width'=>$att[0], 'height'=>$att[1], 'type'=>$att[2]);
					// thumb
					if($img['width'] > $this->kclass->vars['thumb'] OR $img['height']>$this->kclass->vars['thumb']){
						$thumb=$this->_thumb(array_merge($f, $img, array('thumbName'=>'thumb', 'limit'=>$this->kclass->vars['thumb'])));
						$img['thumb'] = $thumb['name'];
						$img['thumbWidth'] = $thumb['width'];
						$img['thumbHeight'] = $thumb['height'];
					}
					// senior
					if($img['width'] > $this->kclass->vars['senior'] OR $img['height']>$this->kclass->vars['senior']){
						$senior=$this->_thumb(array_merge($f, $img, array('thumbName'=>'senior', 'limit'=>$this->kclass->vars['senior'])));
						$img['senior'] = $senior['name'];
						$img['seniorWidth'] = $senior['width'];
						$img['seniorHeight'] = $senior['height'];
					}
					// big
					if($img['width'] > $this->kclass->vars['big'] OR $img['height']>$this->kclass->vars['big']){
						$big=$this->_thumb(array_merge($f, $img, array('thumbName'=>'big', 'limit'=>$this->kclass->vars['big'])));
						$img['big'] = $big['name'];
						$img['bigWidth'] = $big['width'];
						$img['bigHeight'] = $big['height'];
					}
					$image=1;
					$imginfo=addslashes(serialize($img));
				}
			}else{
				$image = 0;
				$imginfo='';
			}
			$this->kclass->DB->query("
				INSERT INTO `attachs` (`tmpid`, `title` ,`orgName` ,`newName` ,`type` ,`size` ,`image` ,`imginfo` ,`folder` ,`ordering` ,`modified` ,`creator` ,`created`)
				VALUES ('".$this->kclass->input['t']."', '".$_FILES['Filedata']['name']."', '".$_FILES['Filedata']['name']."', '".$f['fullname']."', '".$f['ext']."', '".$_FILES['Filedata']['size']."', '".$image."', '".$imginfo."', '".$f['folder']."', '1', '".TIMENOW."', '".$this->kclass->user['userid']."', '".TIMENOW."')
			");
			$attachid = $this->kclass->DB->insertID();
			if($image == 1){
				$timg='<img src="/'.$this->kclass->vars['attachs'].$img['thumb'].'" width="'.$img['thumbWidth'].'" height="'.$img['thumbHeight'].'">';
			}else{
				$timg='<img src="/public/icon/'.$ext['ext'].'" width="128" height="128">';
			}
$this->p = <<<EOF
<div class="small">标题：<input type="text" name="attachTitle[{$attachid}]" value="{$_FILES['Filedata']['name']}" class="small" size="12" style="width:88px" /> 排序：<input type="text" value="1" size="2" style="width:20px" class="small" /> <label for="attachDel{$attachid}"><input type="checkbox" name="attachDel[{$attachid}]" value="{$attachid}" id="attachDel{$attachid}">删除</label></div>
{$timg}
EOF;
			$this->p='true';
		}else{
			header("HTTP/1.1 500 File Upload Error");
			if (isset($_FILES['Filedata']))echo $_FILES['Filedata']["error"];
			exit(0);
		}
	}

	//
	function showUploaded(){
		$this->p = $this->kclass->uploadList(array('module'=>$this->kclass->input['uploadModule'], 'mid'=>$this->kclass->input['uploadMid']));
	}

	//
	function _thumb($t){
		if($t['width'] > $t['limit']){
			$r['width'] = $t['limit'];
			$r['height'] = ceil(($t['height'] * (($r['width'] * 100) / $t['width'])) / 100);
		}elseif($t['height'] > $t['limit']){
			$r['height'] = $t['limit'];
			$r['width'] = ceil(($t['width'] * (($r['height'] * 100) / $t['height'])) / 100);
		}else{
			return array('thumb'=>$t['name'], 'width'=>$t['width'], 'height'=>$t['height']);
		}
		if($t['type'] == 1 AND function_exists('imagecreatefromgif')){ //GIF
			$image = imagecreatefromgif($t['path']);
		}
		if($t['type'] == 2 AND function_exists('imagecreatefromjpeg')){ //JPG
			$image = imagecreatefromjpeg($t['path']);
		}
		if($t['type'] == 3 AND function_exists('imagecreatefrompng')){ //PNG
			$image = imagecreatefrompng($t['path']);
		}
		if($image){//生成thumb
			$thumb = imagecreatetruecolor($r['width'], $r['height']);
			imagecopyresampled($thumb, $image, 0, 0, 0, 0, $r['width'], $r['height'], $t['width'], $t['height']);
		}
		if(function_exists('imagejpeg')){
			$r['name'] = $t['folder']."/".$t['filename'].$t['thumbName'].'.jpg';
			@imagejpeg($thumb, $this->kclass->vars['attachs'].$r['name']);
		}elseif(function_exists('imagepng')){
			$r['name'] = $t['folder']."/".$t['filename'].$t['thumbName'].'.png';
			@imagepng($thumb, $this->kclass->vars['attachs'].$r['name']);
		}
		@chmod($r['thumb'], 0777);
		@imagedestroy($thumb);
		@imagedestroy($image);
		return $r;
	}

	//
	function printIt(){
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // date in the past
		header("Last-modified: " . gmdate("D, d M Y H:i:s") . "GMT"); // always modified
		header("Cache-Control: no-cache, must-revalidate");  // HTTP/1.1
		header("Pragma: no-cache");  // HTTP/1.0
		header("Content-type:text/html;charset=utf-8");
		print $this->p;
		exit;
	}

	function water($w){
//		$w['text'] = 'Product No.: 0101003';

		if($w['type'] == 'jpg'){
			$img = imagecreatefromjpeg($w['file']);
		}elseif($w['type'] == 'gif'){
			$img = imagecreatefromgif($w['file']);
		}elseif($w['type'] == 'png'){
			$img = imagecreatefrompng($w['file']);
		}

		if($w['font_color'] != ''){
			$red = hexdec(substr($w['font_color'], 0, 2));
			$green = hexdec(substr($w['font_color'], 2, 2));
			$blue = hexdec(substr($w['font_color'], 4, 2));
		}else{
			$red = $green = $blue = 0;
		}
		$font_color = imagecolorallocatealpha ($img, $red, $green, $blue, $w['alpha']);
		$w['font'] = realpath($w['font']);
		$text_box = imagettfbbox ($w['font_size'], 0, $w['font'], $w['text']);

		switch($w['position']){
			case 0 : //随机
				$w['xpos'] = rand(0, ($w['width'] - ($text_box[2] - $text_box[6])));
				$w['ypos'] = rand(0, ($w['height'] - ($text_box[3] - $text_box[7])));
				break;
			case 1 : //1为顶端居左
				 $w['xpos'] = 5;
				 $w['ypos'] = abs($text_box[7]) + 5;
				break;
			case 2 : //2为顶端居中
				 $w['xpos'] = ($w['width'] - ($text_box[2] - $text_box[6])) / 2;
				 $w['ypos'] = abs($text_box[7]) + 5;
				break;
			case 3 : //3为顶端居右
				 $w['xpos'] = $w['width'] - ($text_box[2] - $text_box[6]) - 5;
				 $w['ypos'] = abs($text_box[7]) + 5;
				break;
			case 4 : //4为中部居左
				 $w['xpos'] = 5;
				 $w['ypos'] = ($w['height'] - ($text_box[3] - $text_box[7])) / 2;
				break;
			case 5 : //5为中部居中
				 $w['xpos'] = ($w['width'] - ($text_box[2] - $text_box[6])) / 2;
				 $w['ypos'] = ($w['height'] - ($text_box[3] - $text_box[7])) / 2;
				break;
			case 6 : //6为中部居右
				 $w['xpos'] = $w['width'] - ($text_box[2] - $text_box[6]) - 5;
				 $w['ypos'] = ($w['height'] - ($text_box[3] - $text_box[7])) / 2;
				break;
			case 7 : //7为底端居左
				 $w['xpos'] = 5;
				 $w['ypos'] = $w['height'] - ($text_box[3] - $text_box[7]);
				break;
			case 8 : //8为底端居中
				 $w['xpos'] = ($w['width'] - ($text_box[2] - $text_box[6])) / 2;
				 $w['ypos'] = $w['height'] - ($text_box[3] - $text_box[7]);
				break;
			case 9 : //9为底端居右
				 $w['xpos'] = $w['width'] - ($text_box[2] - $text_box[6]) - 5;
				 $w['ypos'] = $w['height'] - ($text_box[3] - $text_box[7]);
				break;
			default: //随机
				 $w['xpos'] = rand(0, ($w['width'] - ($text_box[2] - $text_box[6])));
				 $w['ypos'] = rand(0, ($w['height'] - ($text_box[3] - $text_box[7])));
				break;
		}

		imagettftext ($img, $w['font_size'], 0, $w['xpos'], $w['ypos'], $font_color, $w['font'], $w['text']);

		if($w['type'] == 'jpg'){
			Imagejpeg ($img, $w['file'], 100);
		}elseif($w['type'] == 'gif'){
			Imagegif ($img, $w['file'], 100);
		}elseif($w['type'] == 'png'){
			Imagepng ($img, $w['file'], 100);
		}
	}
	//
	function _image($s, $a, $f, $t){
		$b = imagettfbbox($s, $a, $f, $t);
		$width = abs($b[2] - $b[0]);
		$height = abs($b[7] - $b[1]);

		$img = imagecreatetruecolor($width, $height);

		$bg = imagecolorallocate($img, 0, 0, 0);
		$color = imagecolorallocate($img, 255, 255, 255);

		$x = $b[0] + ($width / 2) - ($b[4] / 2);
		$y = $b[1] + ($height / 2) - ($b[5] / 2);
	//echo '<pre>'.print_r($x.', '.$y, 1).'</pre>';
		imagefilledrectangle($img, 0, 0, $width - 1, $height - 1, $bg);
		imagettftext($img, $s, 0, $x, $y, $color, $f, $t);

		$end= imagecolorat($img, 0, 0);

		for ($j = 0; $j < $height; $j++){
			for ($i = 0; $i < $width; $i++){
				if (isset($blank_left) && $i >= $blank_left){
					break;
				}
				if (imagecolorat($img, $i, $j) !== $end){
					if (!isset($blank_top)){
						$blank_top = $j;
					}
					$blank_left = $i;
					break;
				}
				$end = imagecolorat($img, $i, $j);
			}
		}

		$x -= $blank_left;
		$y -= $blank_top;

		imagedestroy($img);
		return array('width'=>$width, 'height'=>$height, 'x'=>$x, 'y'=>$y);
	}
	//
	function _messager($e){
		$this->p = '<dl><dt style="border-bottom:1px solid #599CD4;font-weight:bold;padding:4px 0">'.$e['title'].'</dt><dd style="padding:4px 0">'.$e['message'].'</dd></dl>';
		$this->print_it();
	}
	//
	function show(){
		$this->p = '';
	}
	//
	function tabsItem(){//id:页面module，tabmodule:用作区分tabs
		$flag=1;
		$year=$this->kclass->input['year'];
		$month=$this->kclass->input['month'];
		if($year!=-100){
			if($month==-100){
				$start=strtotime(date('Y-m-d', mktime(0,0,0,1,1,$year)));
				$end=strtotime(date('Y-m-t', mktime(0,0,0,1,1,$year+1)));
			}else{
				$start=strtotime(date('Y-m-d', mktime(0,0,0,$month,1,$year)));
				$end=strtotime(date('Y-m-t', mktime(0,0,0,$month,1,$year)))+86400;
			}
		}else{
			$flag=0;
		}
		if($this->kclass->input['id']=='material'){
			if($this->kclass->input['tabmodule']=='childBom'){
				$related=$this->kclass->relatedBom(array('materialid'=>$this->kclass->input['materialid'],'itemid'=>$this->kclass->input['itemid'],'show'=>'child'));
			}elseif($this->kclass->input['tabmodule']=='parentBom'){
				$related=$this->kclass->relatedBom(array('materialid'=>$this->kclass->input['materialid'],'itemid'=>$this->kclass->input['itemid'],'show'=>'parent'));
			}elseif($this->kclass->input['tabmodule']=='inoutRecord'){
				if($flag!=0){
					$condition='ior.dateline>='.$start.' AND ior.dateline <='.$end;
				}
				$related=$this->kclass->relatedInoutrecord(array('materialid'=>$this->kclass->input['materialid'],'itemid'=>$this->kclass->input['itemid'],'condition'=>$condition));
			}
		}
		if($this->kclass->input['id']=='requirement'){
			if($this->kclass->input['tabmodule']=='order'){
				$related=$this->kclass->relatedOrder(array('requirementid'=>$this->kclass->input['requirementid'],'panel'=>1));
			}elseif($this->kclass->input['tabmodule']=='sample'){
				$related=$this->kclass->relatedSample(array('requirementid'=>$this->kclass->input['requirementid'],'panel'=>1));
			}elseif($this->kclass->input['tabmodule']=='task'){
				$related=$this->kclass->releatedTasklist(array('requirementid'=>$this->kclass->input['requirementid'],'panel'=>1));
			}elseif($this->kclass->input['tabmodule']=='dispatch'){
				$related=$this->kclass->releatedDispatchlist(array('requirementid'=>$this->kclass->input['requirementid'],'panel'=>1));
			}elseif($this->kclass->input['tabmodule']=='log'){
				$related=$this->kclass->releatedActionLog(array('module'=>'requirement','mid'=>$this->kclass->input['requirementid'],'panel'=>1));
			}
		}
		if($this->kclass->input['id']=='order'){
			if($this->kclass->input['tabmodule']=='requirement'){
				$related=$this->kclass->releatedRequirement(array('orderid'=>$this->kclass->input['orderid'],'panel'=>1));
			}
		}
		if($this->kclass->input['id']=='sample'){
			if($this->kclass->input['tabmodule']=='requirement'){
				$related=$this->kclass->releatedRequirement(array('sampleid'=>$this->kclass->input['sampleid'],'panel'=>1));
			}
		}

		$callback['nextPage']=$related['panel'];
		if($callback['nextPage']){			
			$callback['success']=1;
		}else{
			$callback['success']=0;			
			$callback['html']='无相关数据,请选择时间段再次筛选！';
		}
		print json_encode($callback);exit;
	}
// 
	function tableItemPage(){
		if($this->kclass->input['amodule']=='bom'){
			$totalcount=$this->kclass->DB->queryFirst("SELECT COUNT(DISTINCT parentVersionid) AS count FROM `bom` WHERE killed=0 ORDER BY ordering ASC, bomid ASC");
			$percount=$this->kclass->input['start']+$this->kclass->input['perpage'];
			$bomList=$this->kclass->listBom(array('start'=>$this->kclass->input['start'],'perpage'=>$this->kclass->input['perpage']));
			$r=$bomList['tr'];
		}elseif($this->kclass->input['amodule']=='inbound'){
			$percount=$this->kclass->input['start']+$this->kclass->input['perpage'];
			$inboundList=$this->kclass->listInbound(array('start'=>$this->kclass->input['start'],'perpage'=>$this->kclass->input['perpage'],'condition'=>$condition,'orderby'=>$query['orderby'],'filter'=>$filter['link']));
			$totalcount['count']=$inboundList['counter'];
			$r=$inboundList['tr'];
		}elseif($this->kclass->input['amodule']=='taskitem'){
			if($this->kclass->input['mmodule']=='workcenter')$workcenterid=$this->kclass->input['mid'];
			if($this->kclass->input['mmodule']=='requirement')$requirementid=$this->kclass->input['mid'];
			$percount=$this->kclass->input['start']+$this->kclass->input['perpage'];
			$wctasklist=$this->kclass->releatedTasklist(array('start'=>$this->kclass->input['start'],'perpage'=>$this->kclass->input['perpage'],'workcenterid'=>$workcenterid,'requirementid'=>$requirementid));
			$totalcount['count']=$wctasklist['counter'];
			$r=$wctasklist['panel'];
		}elseif($this->kclass->input['amodule']=='dispatchitem'){
			if($this->kclass->input['mmodule']=='workcenter')$workcenterid=$this->kclass->input['mid'];
			if($this->kclass->input['mmodule']=='task')$taskid=$this->kclass->input['mid'];
			$percount=$this->kclass->input['start']+$this->kclass->input['perpage'];
			$wcdispatchlist=$this->kclass->releatedDispatchlist(array('start'=>$this->kclass->input['start'],'perpage'=>$this->kclass->input['perpage'],'workcenterid'=>$workcenterid,'taskid'=>$taskid));
			$totalcount['count']=$wcdispatchlist['counter'];
			$r=$wcdispatchlist['panel'];
		}elseif($this->kclass->input['amodule']=='productionrecord'){
			if($this->kclass->input['mmodule']=='workcenter')$workcenterid=$this->kclass->input['mid'];
			$percount=$this->kclass->input['start']+$this->kclass->input['perpage'];
			$wcproductionrecord=$this->kclass->releatedProductionrecord(array('start'=>$this->kclass->input['start'],'perpage'=>$this->kclass->input['perpage'],'workcenterid'=>$workcenterid,'taskid'=>$taskid));
			$totalcount['count']=$wcproductionrecord['counter'];
			$r=$wcproductionrecord['panel'];
		}elseif($this->kclass->input['amodule']=='repairrecord'){
			if($this->kclass->input['mmodule']=='workcenter')$workcenterid=$this->kclass->input['mid'];
			$percount=$this->kclass->input['start']+$this->kclass->input['perpage'];
			$wcrepairrecord=$this->kclass->releatedRepairrecord(array('start'=>$this->kclass->input['start'],'perpage'=>$this->kclass->input['perpage'],'workcenterid'=>$workcenterid,'taskid'=>$taskid));
			$totalcount['count']=$wcrepairrecord['counter'];
			$r=$wcrepairrecord['panel'];
		}elseif($this->kclass->input['amodule']=='workcenteritem'){
			if($this->kclass->input['mmodule']=='workcenter')$workcenterid=$this->kclass->input['mid'];
			$percount=$this->kclass->input['start']+$this->kclass->input['perpage'];
			$workcenterItems=$this->kclass->releatedWorkcenterMaterial(array('start'=>$this->kclass->input['start'],'perpage'=>$this->kclass->input['perpage'],'workcenterid'=>$workcenterid,'taskid'=>$taskid));
			$totalcount['count']=$workcenterItems['counter'];
			$r=$workcenterItems['panel'];
		}else{
			$totalcount=$this->kclass->DB->queryFirst("
				SELECT COUNT(".$this->kclass->input['amodule']."id) AS count 
				FROM ".$this->kclass->input['amodule']." 
				WHERE killed=0
				ORDER BY ".$this->kclass->input['amodule']."id ASC 
				LIMIT 0,1
			");
			$percount=$this->kclass->input['start']+$this->kclass->input['perpage'];
			if($this->kclass->input['amodule']=='material'){
				$mList=$this->kclass->listMaterial(array('start'=>$this->kclass->input['start'],'perpage'=>$this->kclass->input['perpage'],'layout'=>$this->kclass->input['layout'],'show'=>$this->kclass->input['show'],'orderby'=>$this->kclass->input['orderby'],'direction'=>$this->kclass->input['direction']));
				$r=$mList['body'];
			}
			if($this->kclass->input['amodule']=='inventory'){
				if($this->kclass->input['stockzero']==''){
					if($this->kclass->input['mtypeid']>0){
						$mCondition = 'AND material.qualified>0';//良品数非零
						$mCondition2 = 'AND m.qualified>0';
						$symbol = 1;
					}else{
						$mCondition = 'AND material.disqualified>0';//不良品数非零
						$mCondition2 = 'AND m.disqualified>0';
						$symbol = -1;
					}
				}elseif($this->kclass->input['stockzero']==1){
					if($this->kclass->input['mtypeid']>0){
						$mCondition = 'AND material.qualified=0';//良品数非零
						$mCondition2 = 'AND m.qualified=0';
						$symbol = 1;
					}else{
						$mCondition = 'AND material.disqualified=0';//不良品数非零
						$mCondition2 = 'AND m.disqualified=0';
						$symbol = -1;
					}			
				}
				//$fieldQuery = ',ivi.materialType,ivi.firstInventory,ivi.checkInventory,ivi.difference';//盘点字段查询
				$tableLink = 'LEFT JOIN (SELECT materialid AS id,materialType,firstInventory,checkInventory,difference FROM `inventoryitem` WHERE inventoryid='.$this->kclass->input['inventoryid'].') AS invi ON (`invi`.id = `material`.materialid)';//盘点明细表与物资表关联
				$condition=$this->kclass->iif($this->kclass->input['show']=='all','1=1',' `material`.killed=0 '.$mCondition.'');
				$mList=$this->kclass->listMaterial(array('module'=>'inventory','start'=>$this->kclass->input['start'],'perpage'=>$this->kclass->input['perpage'],'layout'=>$this->kclass->input['layout'],'show'=>$this->kclass->input['show'],'orderby'=>$this->kclass->input['orderby'],'direction'=>$this->kclass->input['direction'],'mtypeid'=>$this->kclass->input['mtypeid'],'tableLink'=>$tableLink,'symbol'=>$symbol,'condition'=>$condition));
				$r=$mList['body'];
			}
			if($this->kclass->input['amodule']=='requirement'){
				$find=$this->kclass->findRequirement(array('created'=>$this->kclass->input['created'],'quantity'=>$this->kclass->input['quantity'],'dateline'=>$this->kclass->input['dateline'],'mftType'=>$this->kclass->input['mftType'],'modeid'=>$this->kclass->input['modeid'],'materialno'=>$this->kclass->input['materialno'],'materialtitle'=>$this->kclass->input['materialtitle'],'requirementno'=>$this->kclass->input['requirementno'],'orderno'=>$this->kclass->input['orderno'],'sampleno'=>$this->kclass->input['sampleno']));
				$rmList=$this->kclass->listRequirement(array('start'=>$this->kclass->input['start'],'perpage'=>$this->kclass->input['perpage'],'find'=>$find,'layout'=>$this->kclass->input['layout'],'show'=>$this->kclass->input['show'],'orderby'=>$this->kclass->input['orderby'],'direction'=>$this->kclass->input['direction']));
				$r=$rmList['body'];
				$totalcount['count']=$rmList['count'];
			}
			if($this->kclass->input['amodule']=='task'){
				$find=$this->kclass->findTask(array('created'=>$this->kclass->input['created'],'quantity'=>$this->kclass->input['quantity'],'finishquantity'=>$this->kclass->input['finishquantity'],'workdate'=>$this->kclass->input['workdate'],'finishdate'=>$this->kclass->input['finishdate'],'workcenterid'=>$this->kclass->input['workcenterid'],'materialno'=>$this->kclass->input['materialno'],'materialtitle'=>$this->kclass->input['materialtitle'],'taskno'=>$this->kclass->input['taskno']));
				$tkList=$this->kclass->listTask(array('start'=>$this->kclass->input['start'],'perpage'=>$this->kclass->input['perpage'],'find'=>$find,'layout'=>$this->kclass->input['layout'],'show'=>$this->kclass->input['show'],'orderby'=>$this->kclass->input['orderby'],'direction'=>$this->kclass->input['direction']));
				$r=$tkList['body'];
				$totalcount['count']=$tkList['count'];
			}
			if($this->kclass->input['amodule']=='dispatch'){
				$find=$this->kclass->findDispatch(array('created'=>$this->kclass->input['created'],'quantity'=>$this->kclass->input['quantity'],'finishquantity'=>$this->kclass->input['finishquantity'],'workdate'=>$this->kclass->input['workdate'],'finishdate'=>$this->kclass->input['finishdate'],'workcenterid'=>$this->kclass->input['workcenterid'],'materialno'=>$this->kclass->input['materialno'],'materialtitle'=>$this->kclass->input['materialtitle'],'dispatchno'=>$this->kclass->input['dispatchno']));
				$dpList=$this->kclass->listDispatch(array('start'=>$this->kclass->input['start'],'perpage'=>$this->kclass->input['perpage'],'find'=>$find,'layout'=>$this->kclass->input['layout'],'show'=>$this->kclass->input['show'],'orderby'=>$this->kclass->input['orderby'],'direction'=>$this->kclass->input['direction']));
				$r=$dpList['body'];
				$totalcount['count']=$dpList['count'];
			}
		}
		$callback['nextPage']=$r;
		if($totalcount['count']>$percount){			
			$callback['success']=1;
		}else{
			$callback['success']=0;			
			$callback['html']='已经是最后一页了!';
		}
		print json_encode($callback);exit;
	}
	//
	function materialVersion(){
		if($this->kclass->input['id']<=0){
			return false;
		}
		if($this->kclass->input['m']=='material'){
			$condition=" AND mv.materialid='".$this->kclass->input['id']."'";
		}elseif($this->kclass->input['m']=='materialitem'){
			$condition=" AND mv.itemid='".$this->kclass->input['id']."'";
		}elseif($this->kclass->input['m']=='version'){
			$condition=" AND versionid='".$this->kclass->input['id']."'";
		}

		$versions=$this->kclass->DB->query("
			SELECT mv.versionid,mv.title,mv.change,mv.qualified,mv.disqualified,
				u.title AS unit
			FROM materialversion AS mv
			LEFT JOIN material AS m ON (m.materialid=mv.materialid)
			LEFT JOIN unit AS u ON (u.unitid=m.unitid)
			WHERE mv.killed=0 {$condition}
			ORDER BY mv.versionid DESC
		");
		if($this->kclass->DB->numRows()){
			while($version=$this->kclass->DB->fetchArray($versions)){
				$r .= '<option value="'.$version['versionid'].'">'.$version['title'].$this->kclass->iif($version['change']!='',' ('.$version['change'].')','').'　良：'.$version['qualified'].'  '.$version['unit'].'　不良：'.$version['disqualified'].' '.$version['unit'].'</option>';
			}
		}else{
			$r='';
		}
		echo $r;exit;
	}
	//
	function chooserMaterial(){
		$materials=$this->kclass->DB->query("
			SELECT m.materialid AS id,m.materialno,m.title,m.standard
			FROM material AS m
			WHERE m.killed=0 AND m.materialno like '".$this->kclass->input['term']."%'
			ORDER BY m.materialno ASC
		");
		$counter=$this->kclass->DB->numRows();
		if($counter==0){
			$materials=$this->kclass->DB->query("
				SELECT mi.itemid,mi.itemno,mi.attributevalue,m.materialid AS id,m.title
				FROM materialitem AS mi 
				LEFT JOIN material AS m ON (m.materialid=mi.materialid)
				WHERE mi.killed=0 AND m.killed=0 AND mi.itemno like '".$this->kclass->input['term']."%'
				ORDER BY mi.itemno ASC
			");
		}
			while($material=$this->kclass->DB->fetchArray($materials)){
			$materialInfo=$this->kclass->getMaterial(array('materialid'=>$material['id'],'itemid'=>$material['itemid']));
			$callback[]=array('m'=>'material','id'=>$material['id'],'mid'=>$this->kclass->iif($material['itemid']>0,$material['itemid'],$material['id']),'label'=>$this->kclass->iif($material['itemid']>0,$material['itemno'],$material['materialno']).'　'.$material['title'].'　'.$materialInfo['standard'],'module'=>$this->kclass->iif($material['itemid']>0,'materialitem','material'),'pid'=>$material['itemid']);
			}
		print json_encode($callback);exit;
	}
	//
	function chooserSupplier(){
		$supplers=$this->kclass->DB->query("SELECT supplierid AS id,supplierno,title FROM supplier WHERE killed=0 AND field='produce' AND title like '%".$this->kclass->input['term']."%' ORDER BY supplierno ASC");
		if($this->kclass->DB->numRows()){
			while($suppler=$this->kclass->DB->fetchArray($supplers)){
				$callback[]=array('m'=>'supplier','id'=>$suppler['id'],'label'=>$suppler['title'].' ('.$suppler['supplierno'].')');
			}
		}
		print json_encode($callback);exit;
	}
	//
	function chooserCustomer(){
		$customers=$this->kclass->DB->query("SELECT customerid AS id,abbr,title FROM customer WHERE killed=0 AND title like '%".$this->kclass->input['term']."%' ORDER BY title ASC");
		if($this->kclass->DB->numRows()){
			while($customer=$this->kclass->DB->fetchArray($customers)){
				$callback[]=array('m'=>'customer','id'=>$customer['id'],'label'=>$customer['title'],'abbr'=>$customer['abbr']);
			}
		}else{
			$callback[]=array('m'=>'customer','id'=>0);
		}
		print json_encode($callback);exit;
	}
	
	/**
	 * ajax获取产品的英文标题
	 * @param $productid 产品的ID
	 * @return string
	 */
	function getProductEntitle(){
		$productitemid = $this->kclass->input['productitemid'];
		$seletName = $this->kclass->input['selectName'];
		$product = $this->kclass->DB->queryFirst("
			SELECT `product`.title,`product`.entitle
			FROM  `productitem`
			LEFT JOIN `product` ON (`productitem`.productid = `product`.productid)
			WHERE productitemid='".$productitemid."'
		");
		$seletStr = '<input type="hidden" id="'.$seletName.'input" value="'.$product['entitle'].'" />';
		echo $seletStr;
	}
	
	/**
	 * ajax选择产品的属性
	 * @param $productid 产品的ID
	 * @return string
	 */
	function chooseProductAttribute(){
		$productitemid = $this->kclass->input['productid'];
		$seletName = $this->kclass->input['selectName'];

		$product = $this->kclass->DB->queryFirst("
			SELECT `product`.title,`product`.entitle
			FROM  `productitem`
			LEFT JION `product` ON (`productitem`.mid = `product`.productid)
			WHERE productitemid='".$productitemid."'
		");

		$attributes=$this->kclass->DB->query("
			SELECT an.nameid,an.title AS name,av.defaultvalue,
				av.valueid,av.title AS value
			FROM  attributename AS an
			LEFT JOIN attributevalue AS av ON (av.nameid=an.nameid)
			WHERE an.killed=0 AND av.killed=0 AND an.module='product' AND an.mid='".$productid."'
			ORDER BY an.ordering ASC
		");

		$nameId=$title=$defaultValue=$value=$attributeCount=array();
		if($this->kclass->DB->numRows()){
			while($attribute=$this->kclass->DB->fetchArray($attributes)){
				if(in_array($attribute['nameid'],$nameId)){
					$title[$attribute['nameid']]=$attribute['name'];
					$value[$attribute['nameid']][]=$attribute['value'];
					$valueId[$attribute['nameid']][]=$attribute['valueid'];
					$defaultValue[$attribute['nameid']][]=$attribute['defaultvalue'];
					$attributeCount[$attribute['nameid']]++;
				}else{
					$nameId[]=$attribute['nameid'];
					$title[$attribute['nameid']]=$attribute['name'];
					$value[$attribute['nameid']][]=$attribute['value'];
					$valueId[$attribute['nameid']][]=$attribute['valueid'];
					$defaultValue[$attribute['nameid']][]=$attribute['defaultvalue'];
					$attributeCount[$attribute['nameid']]=1;
				}
			}
		}
		//排序数组
		sort($attributeCount);
		//获取列数
		$colNum = count($attributeCount);
		//获取行数
		$rawNum = $attributeCount[$colNum-1];
		$seletStr = '';
		if($nameId){
			foreach ($nameId as $key=>$val){
				$seletStr .= '<select name="'.$seletName.'['.$key.']">';
				$seletStr .= '<option value="">请选择  '.$title[$val].'</option>';
				for($i=0;$i<count($value[$val]);$i++){
					if($defaultValue[$val][$i]==1){
						$seletStr .= '<option value="'.$valueId[$val][$i].'" selected>'.$value[$val][$i].'</option>';
					}else{
						$seletStr .= '<option value="'.$valueId[$val][$i].'">'.$value[$val][$i].'</option>';
					}
				}
				$seletStr .= '</select>';
			}
			$seletStr .= '<input type="hidden" id="'.$seletName.'input" value="'.$product['entitle'].'" />';
		}else{
			$seletStr .= '此产品或配件没有属性';
		}

		echo $seletStr;

	}


	/**
	 * 根据客户名称查找客户(输入框自动提示)
	 * @return json
	 */
	function findCustomer(){
		$input = trim($this->kclass->input['input']);
		$arr = array();
		$customers = $this->kclass->DB->query("
			SELECT title
			FROM  `customer`
			WHERE INSTR(LCASE(`customer`.title),'".(strtolower($input))."')>0
			ORDER BY customerid ASC
		");

		if($this->kclass->DB->numRows()){
			while($customer=$this->kclass->DB->fetchArray($customers)){
				array_push($arr, $customer['title']);
			}
		}

		$input = strtolower($input);
		$len = strlen($input);
		//判断是中文还是英文
		$language = preg_match("/^[".chr(0x80)."-".chr(0xff)."]+$/",$input);
		$result = array();
		$count = 0;
		//如果为英文则截取处理
		if(isset($len) && ($language == 0)){
			for($i=0;$i<count($arr);$i++){
				if(strtolower(substr($arr[$i],0,$len)) == strtolower($input)){
					$count++;
					$result[] = array("id"=>$i,"value"=>$arr[$i]);
				}
			}
		}
		//如果为中文则截取处理
		if(isset($len) && ($language == 1)){

			function msubstr($str, $start=0, $length, $charset="utf-8"){
				if(function_exists("mb_substr"))
					return mb_substr($str, $start, $length, $charset);
				elseif(function_exists('iconv_substr')) {
					return iconv_substr($str,$start,$length,$charset);
				}
				$re['utf-8']   = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
				$re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
				$re['gbk']	  = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
				$re['big5']	  = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
				preg_match_all($re[$charset], $str, $match);
				$slice = join("",array_slice($match[0], $start, $length));
				return $slice;
			}
			//字符串长度
			function abslength($str){
				if(empty($str)){
					return 0;
				}
				if(function_exists('mb_strlen')){
					return mb_strlen($str,'utf-8');
				}else{
					preg_match_all("/./u", $str, $ar);
					return count($ar[0]);
				}
			}
			$cnlen = abslength($input);
			//如果查找到则加入数组
			for($i=0;$i<count($arr);$i++){
				if(msubstr($arr[$i],0,$cnlen) == $input){
					$count++;
					$result[] = array("id"=>$i,"value"=>$arr[$i]);
				}
			}
		}
		//输出json数据
		if(empty($input)){
			echo "";
		}else{
			echo json_encode($result);
		}
	}

	/**
	 * 返回订单号上的国家与客户字符串
	 * @return string
	 */
	function returnCountryAndCustomerStr(){
		$customerid = (int)$this->kclass->input['customerid'];
		if($customerid>0){
			$customer = $this->kclass->DB->queryFirst("
					SELECT c.abbr AS customerAbbr,r.abbr AS regionAbbr,r.countryid
					FROM `customer` as c
					LEFT JOIN region as r ON (c.regionid = r.regionid)
					WHERE c.customerid = ".$customerid."");
			if(empty($customer['customerAbbr'])){
				exit("cNull");
			}
			$countryStr = '';
			if($customer['countryid']>0){
				$country = $this->kclass->DB->queryFirst("
					SELECT `region`.abbr,`region`.countryid
					FROM `region`
					WHERE `region`.countryid = ".$customer['countryid']."");
				if(empty($country['abbr'])){
					exit("rNull");
				}
				$countryStr = $country['abbr'];
			}else{
				if(empty($customer['regionAbbr'])){
					exit("rNull");
				}
				$countryStr = $customer['regionAbbr'];
			}
			echo "-".$countryStr."-".$customer['customerAbbr'];
		}else{
			exit("idNull");
		}
	}
	// 显示出所有产品
	function showParts(){
		$callback['html']='<div style="border:1px solid white "><span>选择产品：</span><ul id="productList">';

		$products = $this->kclass->DB->query("
			SELECT productid, productno, title, entitle
			FROM product
			WHERE killed=0 AND attrid='1' ".$b['condition']."
			ORDER BY productid ASC
		");
		if($this->kclass->DB->numRows()){
			$i=1;
			while($product = $this->kclass->DB->fetchArray($products)){
				$productId=array();
				$part=$this->kclass->DB->queryFirst("SELECT COUNT(productid) AS count FROM product WHERE killed=0 AND attrid=3 AND FIND_IN_SET(".$product['productid'].",parentProduct) LIMIT 0,1");
				if($part['count']>0){
					$callback['html'].='<li style="list-style:none outside none;width:180px;display:inline-block"><input type="checkbox" name="productid['.$i.']" value="'.$product['productid'].'" />'.$product['title'].'</li>';
				}
				$i++;
			}
		}
		$callback['html'].='<li style="list-style:none outside none;width:180px;display:inline-block"><input type="checkbox" name="productid['.$i.']" value="0" />公共配件</li></ul></div><div style="border:1px solid white;margin-top:10px;min-height:300px"><span>选择配件</span><ul id="partsList"></ul></div>';
		print json_encode($callback);exit;
	}
	// 通过产品选择不同配件
	function chooserParts(){
		$productid=$this->kclass->input['pid'];
		$callback='';
		$parts=$this->kclass->DB->query("SELECT productid,title,parentProduct FROM product WHERE killed=0 AND attrid=3 ORDER BY ordering ASC");
		if($this->kclass->DB->numRows()){
			$i=1;
			$callback['success']=1;
			while($part=$this->kclass->DB->fetchArray($parts)){
				$parentId=explode(',',$part['parentProduct']);
				if($productid==array_intersect($productid,$parentId)){
					$callback['html'].='<li style="width:180px;display:inline-block"><input type="checkbox" name="partid['.$i.']" value="'.$part['productid'].'" />'.$part['title'].'</li>';
					$i++;
				}
			}
		}
		if($callback['html']==''){
			$callback['success']=0;
			$callback['html']='';
		}else{
			$callback['html'].='<div class="center"><span class="buttom hand" onclick="dc.product.submitParts(\''.$this->kclass->input['obj'].'\')">确认</span></div>';
		}
		print json_encode($callback);exit;
	}
	// 选择具体的配件 并且将其添加到配件包列表中
	function submitParts(){
		$parts=$this->kclass->DB->query("SELECT productid,title,parentProduct FROM product WHERE killed=0 AND attrid=3 AND productid IN (".implode(',',$this->kclass->input['partId']).") ORDER BY ordering ASC");
		if($this->kclass->DB->numRows()){
			$i=1;
			$callback['success']=1;
			$callback['html'].='<tr class="center"><td width="15">ID</td><td>配件名称</td><td width="100">数量 <span class="red">*</span></td><td width="250">备注</td></tr>';
			while($part=$this->kclass->DB->fetchArray($parts)){
				$callback['html'].='<tr><td>'.$i.'</td><td><input type="hidden" name="partId['.$i.']" value="'.$part['productid'].'"><a href="/s.php?module=product&action=view&productid='.$part['productid'].'">'.$part['title'].'</a></td><td><input type="text" name="itemQuantity['.$i.']" value=""></td><td><input type="text" name="itemRemark['.$i.']" style="width:250px" ></td></tr>';
				$i++;
			}
		}
		if($callback['html']==''){
			$callback['success']=0;
			$callback['html']='';
		}
		print json_encode($callback);exit;
	}
	// 修改编号
	function updateNo(){
		switch($this->kclass->input['m']){
			case 'outboundno':
				$no=$this->kclass->id(array('outbound'=>$this->kclass->input['t'],'typeid'=>$this->kclass->input['typeid']));
				break;
			case 'inboundno':
				$no=$this->kclass->id(array('inbound'=>$this->kclass->input['t'],'typeid'=>$this->kclass->input['typeid']));
				break;
		}

		print $no;exit;
	}
	
	function addServiceNo(){
		$versionid=$this->kclass->input['vid'];
		$orderid=$this->kclass->iif($this->kclass->input['oid'],$this->kclass->input['oid'],0);
		$sampleid=$this->kclass->iif($this->kclass->input['sid'],$this->kclass->input['sid'],0);
		$itemid=$this->kclass->input['itemid'];
		$quantity=intval($this->kclass->input['quantity']);
		$count=intval($this->kclass->input['count']);
		$startno=$this->kclass->input['startno'];
		$callback['complete']=1;
		$serviceno=$this->kclass->DB->queryFirst("SELECT COUNT(*) AS count FROM serviceno WHERE orderid='{$orderid}' AND sampleid={$sampleid} AND versionid='{$versionid}' AND serviceno>={$startno} LIMIT 0,1");
		$endno=$startno+$serviceno['count'];
		$per=$this->kclass->input['per'];
		if($quantity<=0){
			$callback['complete']=0;
			$callback['status']='数量有误，无法生成';
		}
		$exsit=$this->kclass->DB->queryFirst("SELECT 1 FROM serviceno WHERE serviceno={$endno} LIMIT 0,1");
		if($exsit){
			$callback['complete']=0;
			$callback['status']='初始号 已存在。请修改';
		}
		if($callback['complete']!=0){
			$value='';
			$currentno=$this->kclass->iif($endno>$startno,$endno,$startno);
			for($i=1;$i<$per;$i++){
				$value.="('{$currentno}','{$orderid}','{$sampleid}','{$versionid}','{$itemid}','{$this->kclass->user['userid']}','".TIMENOW."'),";
				$callback['r'][]=$currentno;
				$currentno++;
			}
			$value.="('{$currentno}','{$orderid}','{$sampleid}','{$versionid}','{$itemid}','{$this->kclass->user['userid']}','".TIMENOW."');";
			$this->kclass->DB->query("INSERT INTO serviceno (serviceno,orderid,sampleid,versionid,requirementitemid,creator,created) VALUES ".$value);
			if(($serviceno['count']+$i)<$quantity){
				$callback['complete']=1;
			}else{
				$callback['complete']=2;
				$callback['status']='快速服务代码生成完成';
			}
			$callback['endno']=$currentno;
			$callback['info']='共<b>'.($serviceno['count']+$count+$i).'</b>条';
		}
		print json_encode($callback);exit;
	}
	// 
	function addBarcode(){
		$versionid=$this->kclass->input['vid'];
		$orderid=$this->kclass->iif($this->kclass->input['oid'],$this->kclass->input['oid'],0);
		$sampleid=$this->kclass->iif($this->kclass->input['sid'],$this->kclass->input['sid'],0);
		$barcodeid=$this->kclass->input['bid'];
		$inboundid=$this->kclass->input['inid'];
		$itemid=$this->kclass->input['itemid'];
		$quantity=intval($this->kclass->input['quantity']);
		$per=$this->kclass->input['per'];
		$callback['complete']=1;
		$serviceno=$this->kclass->DB->queryFirst("SELECT 1 FROM serviceno WHERE versionid={$versionid} AND orderid={$orderid} AND sampleid={$sampleid} LIMIT 0,1");
		if(!$serviceno){
			$callback['complete']=0;
			$callback['status']='无快速服务代码，无法生成';
		}
		if($callback['complete']!=0){
			$childBarcode=$this->kclass->DB->queryFirst("SELECT COUNT(*) AS count FROM childBarcode WHERE barcodeid={$barcodeid}");
			$barcode=$this->kclass->DB->queryFirst("SELECT barcode,batchid,versionid FROM barcode WHERE barcodeid={$barcodeid} LIMIT 0,1");
			$sb=explode('-',$barcode['barcode']);
			$currentno=$childBarcode['count']+1;
			for($i=1;$i<=$per;$i++){
				if(count($sb)==6){
					$cbarcode=$sb[0].'-'.$sb[1].'-'.$sb[2].'-'.$sb[3].'-'.$sb[4].'-'.str_pad($currentno,4,'0',STR_PAD_LEFT).'-'.$sb[5];
				}elseif(count($sb)==5){
					$cbarcode=$sb[0].'-'.$sb[1].'-'.$sb[2].'-'.$sb[3].'-'.$sb[4].'-'.$sb[5];
				}
				$this->kclass->DB->query("
					INSERT INTO childBarcode (batchid,versionid,barcodeid,inboundid,initemid,title,number,creator,created) 
					VALUES ({$barcode['batchid']},{$barcode['versionid']},{$barcodeid},{$inboundid},{$itemid},'{$cbarcode}',{$currentno},{$this->kclass->user['userid']},'".TIMENOW."')
				");
				$cbarcodeid=$this->kclass->DB->insertID();
				$serviceno=$this->kclass->DB->queryFirst("SELECT servicenoid FROM serviceno WHERE versionid={$versionid} AND orderid={$orderid} AND sampleid={$sampleid} AND childbarcodeid=0 ORDER BY servicenoid ASC LIMIT 0,1");
				if($serviceno['servicenoid']){
					$this->kclass->DB->query("UPDATE serviceno SET childbarcodeid={$cbarcodeid} WHERE servicenoid={$serviceno['servicenoid']}");
				}
				$currentno++;
			}

			if(($childBarcode['count']+$i)<$quantity){
				$callback['complete']=1;
			}else{
				$callback['complete']=2;
				$callback['status']='生成完成';
			}

			$callback['endno']=str_pad($currentno-1,4,'0',STR_PAD_LEFT);
			$callback['info']='共<b>'.($childBarcode['count']+$i-1).'</b>条';
		}
		print json_encode($callback);exit;
	}
	
	function getOrderList(){
		$r='';
		if($this->kclass->input['type']==1){
			$orders=$this->kclass->DB->query("SELECT orderid,orderno,ifOutbound FROM `order` WHERE killed=0 AND type='PO' AND ifInbound=2");
			$r='<select name="sourceOrderid" style="width:200px;" id="sourceOrderid" onchange="dc.order.getItem(this,\'PO\')"><option value=0></option>';
			if($this->kclass->DB->numRows()){
				while($order=$this->kclass->DB->fetchArray($orders)){
					$title='';
					$items=$this->kclass->DB->query("
						SELECT m.title
						FROM item AS i
						LEFT JOIN material AS m ON (m.materialid=i.materialid)
						WHERE i.killed=0 AND i.module='order' AND i.mid='".$order['orderid']."'
						ORDER BY i.itemid ASC
					");
					if($this->kclass->DB->numRows()){
						while($item=$this->kclass->DB->fetchArray($items)){
							if($item['title']){
								$title.='-'.$item['title'];
							}
						}
					}
					if($order['ifOutbound']==2){
						$item=$this->kclass->DB->queryFirst("SELECT SUM(quantity) AS quantity FROM item WHERE killed=0 AND module='order' AND mid='{$order['orderid']}' ");
						$outbound=$this->kclass->DB->queryFirst("SELECT SUM(outQuantity) AS quantity FROM outbounditem AS obi LEFT JOIN outbound AS o ON (o.outboundid=obi.outboundid) WHERE o.killed=0 AND obi.killed=0 AND o.orderid={$order['orderid']} ");
						if($item['quantity']!=$outbound['quantity']){
							$r.='<option value="'.$order['orderid'].'">'.$order['orderno'].$title.'</option>';
						}
					}else{
						$r.='<option value="'.$order['orderid'].'">'.$order['orderno'].$title.'</option>';
					}
				}
			}
			$r.='</select>';
		}elseif($this->kclass->input['type']==2){
			$r='<select name="targetOrderid" style="width:200px;" id="targetOrderid" onchange="dc.order.getItem(this,\'PI\')"><option value=0></option>';
			$orders=$this->kclass->DB->query("SELECT orderid,orderno,orderTitle FROM `order` WHERE killed=0 AND type='PI' AND ifOutbound<>2");
			if($this->kclass->DB->numRows()){
				while($order=$this->kclass->DB->fetchArray($orders)){
					$relatedPO=$this->kclass->DB->queryFirst("SELECT ifOutbound FROM `order` WHERE killed=0 AND type='PO' AND parentid='{$order['orderid']}'");
					if($relatedPO['ifOutbound']!=2){
						$r.='<option value="'.$order['orderid'].'">'.$order['orderno'].$order['orderTitle'].'</option>';
					}
				}
			}
			$r.='</select>';
		}elseif($this->kclass->input['type']==3){
			$r='<select name="sourceOrderid" style="width:200px;" id="sourceOrderid" onchange="dc.order.getItem(this,\'sSample\')"><option value=0></option>';
			$samples=$this->kclass->DB->query("SELECT sampleid,sampleno FROM `sample` WHERE killed=0 AND ifInbound=2");
			if($this->kclass->DB->numRows()){
				while($sample=$this->kclass->DB->fetchArray($samples)){
					$title='';
					$items=$this->kclass->DB->query("
						SELECT m.title 
						FROM requirementitem AS ri 
						LEFT JOIN requirement AS r ON (r.requirementid=ri.requirementid)
						LEFT JOIN material AS m ON (ri.materialid=m.materialid) 
						WHERE ri.killed=0 AND ri.killed=0 AND module='sample' AND mid={$sample['sampleid']}
					");
					if($this->kclass->DB->numRows()){
						while($item=$this->kclass->DB->fetchArray($items)){
							if($item['title']){
								$title.='-'.$item['title'];
							}
						}
					}
					$r.='<option value="'.$sample['sampleid'].'">'.$sample['sampleno'].$title.'</option>';
				}
			}
			$r.='</select>';
		}elseif($this->kclass->input['type']==4){
			$r='<select name="targetOrderid" style="width:200px;" id="targetOrderid" onchange="dc.order.getItem(this,\'tSample\')"><option value=0></option>';
			$samples=$this->kclass->DB->query("SELECT sampleid,sampleno FROM `sample` WHERE killed=0 AND ifOutbound<>2 ");
			if($this->kclass->DB->numRows()){
				while($sample=$this->kclass->DB->fetchArray($samples)){
					$title='';
					$items=$this->kclass->DB->query("
						SELECT p.title 
						FROM item AS i 
						LEFT JOIN product AS p ON (p.productid=i.productid)
						WHERE i.killed=0 AND module='sample' AND mid={$sample['sampleid']}
					");
					if($this->kclass->DB->numRows()){
						while($item=$this->kclass->DB->fetchArray($items)){
							if($item['title']){
								$title.='-'.$item['title'];
							}
						}
					}
					$r.='<option value="'.$sample['sampleid'].'">'.$sample['sampleno'].$title.'</option>';
				}
			}
			$r.='</select>';
		}
		print $r;exit;
	}
	// 
	function getOrderItem(){
		$orderid=$this->kclass->input['id'];
		$type=$this->kclass->input['type'];
		$callback['option']='';
		if($type=='PO'){
			$items=$this->kclass->DB->query("
				SELECT ii.itemid,ii.materialid,ii.materialitemid,ii.quantity,ii.versionid,mv.title AS version 
				FROM `inbounditem` AS ii
				LEFT JOIN `inbound` AS i ON (i.inboundid=ii.inboundid)
				LEFT JOIN materialversion AS mv ON (mv.versionid=ii.versionid)
				WHERE i.killed=0 AND ii.killed=0 ANDi.orderid>0 AND ii.orderid={$orderid}
			");
			if($this->kclass->DB->numRows()){
				while($item=$this->kclass->DB->fetchArray($items)){
					$material=$this->kclass->getMaterial(array('materialid'=>$item['materialid'],'itemid'=>$item['materialitemid']));
					$callback['option'].='<option value="'.$item['itemid'].'">'.$material['no'].'　'.$material['material'].'　'.$material['standard'].'　版本：'.$item['version'].'　已入库数量：'.$item['quantity'].'</option>';
				}
			}
		}elseif($type=='PI'){
			$items=$this->kclass->DB->query("
				SELECT itemid,productid,productitemid,quantity
				FROM item 
				WHERE killed=0 AND module='order' AND mid='{$orderid}'
			");
			if($this->kclass->DB->numRows()){
				while($item=$this->kclass->DB->fetchArray($items)){
					$product=$this->kclass->getProduct(array('productid'=>$item['productid'],'itemid'=>$item['productitemid']));
					$callback['option'].='<option value="'.$item['itemid'].'">'.$product['title'].'　'.$product['standard'].'　需求数量：'.$item['quantity'].'</option>';
				}
			}
		}elseif($type=='sSample'){
			$items=$this->kclass->DB->query("
				SELECT ii.itemid,ii.materialid,ii.materialitemid,ii.quantity,ii.versionid,mv.title AS version 
				FROM `inbounditem` AS ii
				LEFT JOIN `inbound` AS i ON (i.inboundid=ii.inboundid)
				LEFT JOIN materialversion AS mv ON (mv.versionid=ii.versionid)
				WHERE i.killed=0 AND ii.killed=0 AND i.sampleid>0 AND ii.sampleid={$orderid}
			");
			if($this->kclass->DB->numRows()){
				while($item=$this->kclass->DB->fetchArray($items)){
					$material=$this->kclass->getMaterial(array('materialid'=>$item['materialid'],'itemid'=>$item['materialitemid']));
					$callback['option'].='<option value="'.$item['itemid'].'">'.$material['no'].'　'.$material['material'].'　'.$material['standard'].'　版本：'.$item['version'].'　已入库数量：'.$item['quantity'].'</option>';
				}
			}
		}elseif($type=='tSample'){
			$items=$this->kclass->DB->query("
				SELECT itemid,productid,productitemid,quantity
				FROM item 
				WHERE killed=0 AND module='sample' AND mid='{$orderid}'
			");
			if($this->kclass->DB->numRows()){
				while($item=$this->kclass->DB->fetchArray($items)){
					$product=$this->kclass->getProduct(array('productid'=>$item['productid'],'itemid'=>$item['productitemid']));
					$callback['option'].='<option value="'.$item['itemid'].'">'.$product['title'].'　'.$product['standard'].'　需求数量：'.$item['quantity'].'</option>';
				}
			}
		}
		print json_encode($callback);exit;
	}
	function showChildren(){
		$version=$this->kclass->DB->queryFirst("
			SELECT versionid,materialid,itemid
			FROM materialversion
			WHERE versionid={$this->kclass->input['vid']}
			LIMIT 0,1
		");
		$children=$this->kclass->relatedBom(array('materialid'=>$version['materialid'],'itemid'=>$version['itemid'],'rank'=>$this->kclass->input['rank'],'show'=>'child'));
		$callback['child']=$children['tr'];
		print json_encode($callback);exit;
	}
	function addColumn(){
		$materialItems=$this->kclass->DB->query("
			SELECT mv.versionid,mv.materialid,mv.itemid,mi.itemno
			FROM materialversion AS mv
			LEFT JOIN materialitem AS mi ON (mi.itemid=mv.itemid)
			WHERE mv.killed=0 AND mi.killed=0 AND mi.relatedid<>0 AND mv.itemid<>0
			ORDER BY mi.itemno ASC
		");
		if($this->kclass->DB->numRows()){
			while($materialItem=$this->kclass->DB->fetchArray($materialItems)){
				$select.='<option value="'.$materialItem['versionid'].'">'.$materialItem['itemno'].'</option>';
			}
		}
		$callback['select']='<select name="versionid" onchange="dc.bom.getChildren(this)"><option value="0">请选择</option>'.$select.'</select>';
		print json_encode($callback);exit;
	}
	function getBomChildren(){
		$version=$this->kclass->DB->queryFirst("
			SELECT materialid,versionid,itemid
			FROM materialversion 
			WHERE versionid={$this->kclass->input['vid']}
			LIMIT 0,1
		");
		$children=$this->kclass->bomChildList(array('parentid'=>$version['materialid'],'materialitemid'=>$version['itemid'],'versionid'=>$version['versionid']));
		if($children['bom']){
			$callback['success']=1;
			$callback['versionid']=array();
			foreach($children['bom'] AS $child){
				if(in_array($child['versionid'],$callback['versionid'])){
					$callback['quantity'][$child['versionid']]+=$child['quantity'];
				}else{
					$callback['versionid'][]=$child['versionid'];
					$callback['quantity'][$child['versionid']]=$child['quantity'];
				}
			}
		}else{
			$callback['success']=0;
		}
		print json_encode($callback);exit;
	}
	function packingsize(){
		if($this->kclass->input['type']=='chooser'){
			$packingsizes=$this->kclass->DB->query("
				SELECT packingsizeid,title
				FROM packingsize 
				WHERE versionid={$this->kclass->input['id']}
			");
			if($this->kclass->DB->numRows()){
				while($packingsize=$this->kclass->DB->fetchArray($packingsizes)){
					$select='<option value="'.$packingsize['packingsizeid'].'">'.$packingsize['title'].'</option>';
				}
				$param="{'type':'detail','line':{$this->kclass->input['line']},'obj':this}";
				$callback['html']='<select name="packingsizeid['.$this->kclass->input['line'].']" style="width:100px" onchange="dc.material.packingsize('.$param.')"><option value=0>无</option>'.$select.'</select>';
			}else{
				$callback['html']='无规格';
			}
		}elseif($this->kclass->input['type']=='detail'){
			$packingsize=$this->kclass->DB->queryFirst("
				SELECT packingsizeid,materialid,materialitemid,versionid,title,length,width,height,volume,perCarton,perNetWeight,perGrossWeight,netWeight,grossWeight,remark
				FROM packingsize 
				WHERE packingsizeid={$this->kclass->input['id']}
			");
			$callback=array('length'=>$packingsize['length'],'width'=>$packingsize['width'],'height'=>$packingsize['height'],'perCarton'=>$packingsize['perCarton'],'perNetWeight'=>$packingsize['perNetWeight'],'perGrossWeight'=>$packingsize['perGrossWeight'],'netWeight'=>$packingsize['netWeight'],'grossWeight'=>$packingsize['grossWeight'],'remark'=>$packingsize['remark']);
		}
		print json_encode($callback);exit;
	}
	// 
	function notification(){
		if($this->kclass->user['positionid']==1){ // 全权限
			$tradeOrder=$this->_tradeOrder();
			$tradeSample=$this->_tradeSample();
			$requirement=$this->_requirement();
			$task=$this->_task();
			$dispatch=$this->_dispatch();
			$inbound=$this->_inbound();
			$outbound=$this->_outbound();
			$apply=$this->_purchaseApply();
			$purchase=$this->_purchase();
			$porder=$this->_porder();
			$totalCount=$tradeOrder['verfiyPICount']+$tradeOrder['verfiyPOCount']+$tradeOrder['approvePICount']+$tradeOrder['approvePOCount']+$tradeSample['verfiyCount']+$tradeSample['approveCount']+$requirement['lockCount']+$task['lockCount']+$dispatch['verifyCount']+$outbound['confirmCount']+$outbound['verifyCount']+$outbound['unCount']+$outbound['outCount']+$inbound['confirmCount']+$inbound['verifyCount']+$inbound['unCount']+$inbound['outCount']+$apply['verfiyCount']+$apply['approveCount']+$purchase['verfiyCount']+$purchase['approveCount']+$porder['verfiyCount']+$porder['approveCount'];
		}elseif($this->kclass->user['positionid']==7){ // 销售
			$tradeOrder=$this->_tradeOrder();
			$tradeSample=$this->_tradeSample();
			$totalCount=$tradeOrder['verfiyPICount']+$tradeOrder['verfiyPOCount']+$tradeOrder['approvePICount']+$tradeOrder['approvePOCount']+$tradeSample['verfiyCount']+$tradeSample['approveCount'];
		}elseif($this->kclass->user['positionid']==8){ // 采购
			$apply=$this->_purchaseApply();
			$purchase=$this->_purchase();
			$porder=$this->_porder();
			$totalCount=$apply['verfiyCount']+$apply['approveCount']+$purchase['verfiyCount']+$purchase['approveCount']+$porder['verfiyCount']+$porder['approveCount'];
		}elseif($this->kclass->user['positionid']==9){ // 仓库
			$inbound=$this->_inbound();
			$outbound=$this->_outbound();
			$totalCount=$outbound['confirmCount']+$outbound['verifyCount']+$outbound['unCount']+$outbound['outCount']+$inbound['confirmCount']+$inbound['verifyCount']+$inbound['unCount']+$inbound['outCount'];
		}elseif($this->kclass->user['positionid']==12){ // 生产
			$requirement=$this->_requirement();
			$task=$this->_task();
			$dispatch=$this->_dispatch();
			$inbound=$this->_inbound();
			$outbound=$this->_outbound();
			$totalCount=$requirement['lockCount']+$task['lockCount']+$dispatch['verifyCount']+$outbound['confirmCount']+$outbound['verifyCount']+$outbound['unCount']+$outbound['outCount']+$inbound['confirmCount']+$inbound['verifyCount']+$inbound['unCount']+$inbound['outCount'];
		}

		if($tradeOrder['verfiyPICount']>0){// PIorder
			$r.='<li>有 <a href="/s.php?module=order&action=list">'.$tradeOrder['verfiyPICount'].'张</a> 销售订单 [营销]<span class="red blod"> 未审核 </span></li>';
		}
		if($tradeOrder['verfiyPOCount']>0){// POorder
			$r.='<li>有 <a href="/s.php?module=order&action=list">'.$tradeOrder['verfiyPOCount'].'张</a> 采购订单 [营销]<span class="red blod"> 未审核 </span></li>';
		}
		if($tradeOrder['approvePICount']>0){// PIorder
			$r.='<li>有 <a href="/s.php?module=order&action=list">'.$tradeOrder['approvePICount'].'张</a> 销售订单 [营销]<span class="red blod"> 未审批 </span></li>';
		}
		if($tradeOrder['approvePOCount']>0){// POorder
			$r.='<li>有 <a href="/s.php?module=order&action=list">'.$tradeOrder['approvePOCount'].'张</a> 采购订单 [营销]<span class="red blod"> 未审批 </span></li>';
		}
		if($tradeSample['verfiyCount']>0){// sample
			$r.='<li>有 <a href="/s.php?module=sample&action=list">'.$tradeSample['verfiyCount'].'张</a> 样品单<span class="red blod"> 未审核 </span></li>';
		}
		if($tradeSample['approveCount']>0){// sample
			$r.='<li>有 <a href="/s.php?module=sample&action=list">'.$tradeSample['approveCount'].'张</a> 样品单<span class="red blod"> 未审批 </span></li>';
		}
		if($requirement['lockCount']>0){// requirment
			$r.='<li>有 <a href="/s.php?module=requirement&action=list">'.$requirement['lockCount'].'张</a> 需求计划<span class="red blod"> 未锁定 </span></li>';
		}
		if($task['lockCount']>0){// task
			$r.='<li>有 <a href="/s.php?module=task&action=list">'.$task['lockCount'].'张</a> 任务单<span class="red blod"> 未锁定 </span></li>';
		}
		if($dispatch['verifyCount']>0){// dispatch
			$r.='<li>有 <a href="/s.php?module=dispatch&action=list">'.$dispatch['verifyCount'].'张</a> 派工单<span class="red blod"> 未审核 </span></li>';
		}
		if($outbound['confirmCount']>0){// outbound
			$r.='<li>有 <a href="/s.php?module=outbound&action=list">'.$outbound['confirmCount'].'张</a> 出库单<span class="red blod"> 未确认 </span></li>';
		}
		if($outbound['verifyCount']>0){// outbound
			$r.='<li>有 <a href="/s.php?module=outbound&action=list&ifVerify=0">'.$outbound['verifyCount'].'张</a> 出库单<span class="red blod"> 未审核 </span></li>';
		}
		if($outbound['unCount']>0){// outbound
			$r.='<li>有 <a href="/s.php?module=outbound&action=list&ifComplete=0">'.$outbound['unCount'].'张</a> 出库单<span class="red blod"> 待出库 </span></li>';
		}
		if($outbound['outCount']>0){// outbound
			$r.='<li>有 <a href="/s.php?module=outbound&action=list&ifComplete=1">'.$outbound['outCount'].'张</a> 出库单<span class="red blod"> 部分出库 </span></li>';
		}
		if($inbound['confirmCount']>0){// inbound
			$r.='<li>有 <a href="/s.php?module=inbound&action=list">'.$inbound['confirmCount'].'张</a> 入库单<span class="red blod"> 未确认 </span></li>';
		}
		if($inbound['verifyCount']>0){// inbound
			$r.='<li>有 <a href="/s.php?module=inbound&action=list&ifVerify=0">'.$inbound['verifyCount'].'张</a> 入库单<span class="red blod"> 未审核 </span></li>';
		}
		if($inbound['unCount']>0){// inbound
			$r.='<li>有 <a href="/s.php?module=inbound&action=list&ifComplete=0">'.$inbound['unCount'].'张</a> 入库单<span class="red blod"> 待入库 </span></li>';
		}
		if($inbound['inCount']>0){// inbound
			$r.='<li>有 <a href="/s.php?module=inbound&action=list&ifComplete=1">'.$inbound['inCount'].'张</a> 入库单<span class="red blod"> 部分入库 </span></li>';
		}
		if($apply['verfiyCount']>0){// purchase apply
			$r.='<li>有 <a href="/s.php?module=purchaseApply&action=list&ifVerify=0">'.$apply['verfiyCount'].'张</a> 采购申请 [采购]<span class="red blod"> 未审核 </span></li>';
		}
		if($apply['approveCount']>0){// purchase apply
			$r.='<li>有 <a href="/s.php?module=purchaseApply&action=list">'.$apply['approveCount'].'张</a> 采购申请 [采购]<span class="red blod"> 未审批 </span></li>';
		}
		if($purchase['verfiyCount']>0){// purchase
			$r.='<li>有 <a href="/s.php?module=purchase&action=list&ifComplete=1">'.$purchase['verfiyCount'].'张</a> 采购计划 [采购]<span class="red blod"> 未审核 </span></li>';
		}
		if($purchase['approveCount']>0){// purchase
			$r.='<li>有 <a href="/s.php?module=purchase&action=list&ifComplete=1">'.$purchase['approveCount'].'张</a> 采购计划 [采购]<span class="red blod"> 未审批 </span></li>';
		}
		if($porder['verfiyCount']>0){// porder
			$r.='<li>有 <a href="/s.php?module=porder&action=list&ifComplete=1">'.$porder['verfiyCount'].'张</a> 采购订单 [采购]<span class="red blod"> 未审核 </span></li>';
		}
		if($porder['approveCount']>0){// porder
			$r.='<li>有 <a href="/s.php?module=porder&action=list&ifComplete=1">'.$porder['approveCount'].'张</a> 采购订单 [采购]<span class="red blod"> 未审批 </span></li>';
		}

		if($r!=''){
			$callback['notice']=1;
			$callback['title']='有 '.$totalCount.' 条新消息';
			$callback['panel']=$r;
			$callback['count']=$totalCount;
		}else{
			$callback['notice']=0;
			$callback['panel']='';
			$callback['title']='暂无最新消息';
		}
		print json_encode($callback);exit;
	}

	/*************private function****************/
			// 
	function _tradeOrder(){
		$verifyPIOrder=$this->kclass->DB->queryFirst("SELECT COUNT(orderid) AS count FROM `order` WHERE killed=0 AND type='PI' AND ifVerify=0 $user");
		$r['verfiyPICount']=$verifyPIOrder['count'];
		$verifyPOrder=$this->kclass->DB->queryFirst("SELECT COUNT(orderid) AS count FROM `order` WHERE killed=0 AND type='PO' AND ifVerify=0 $user");
		$r['verfiyPOCount']=$verifyPOrder['count'];
		$approvePIOrder=$this->kclass->DB->queryFirst("SELECT COUNT(orderid) AS count FROM `order` WHERE killed=0 AND type='PI' AND ifVerify=1 AND ifApprove=0");
		$r['approvePICount']=$approvePIOrder['count'];
		$approvePOrder=$this->kclass->DB->queryFirst("SELECT COUNT(orderid) AS count FROM `order` WHERE killed=0 AND type='PO' AND ifVerify=1 AND ifApprove=0");
		$r['approvePOCount']=$approvePOrder['count'];

		return $r;
	}
	// 
	function _tradeSample(){
		$verifySample=$this->kclass->DB->queryFirst("SELECT COUNT(sampleid) AS count FROM `sample` WHERE killed=0 AND ifVerify=0");
		$r['verfiyCount']=$verifySample['count'];
		$approveSample=$this->kclass->DB->queryFirst("SELECT COUNT(sampleid) AS count FROM `sample` WHERE killed=0 AND ifVerify=1 AND ifApprove=0");
		$r['approveCount']=$approveSample['count'];

		return $r;
	}
	//
	// 
	function _requirement(){
		$lockRequirement=$this->kclass->DB->queryFirst("SELECT COUNT(requirementid) AS count FROM `requirement` WHERE killed=0 AND statusid=0");
		$r['lockCount']=$lockRequirement['count'];

		return $r;
	}
	// 
	function _task(){
		$lockTask=$this->kclass->DB->queryFirst("SELECT COUNT(taskid) AS count FROM `task` WHERE killed=0 AND statusid=0");
		$r['lockCount']=$lockTask['count'];

		return $r;
	}
	// 
	function _dispatch(){
		$verifyDispatch=$this->kclass->DB->queryFirst("SELECT COUNT(dispatchid) AS count FROM `dispatch` WHERE killed=0 AND ifVerify=0");
		$r['verifyCount']=$verifyDispatch['count'];

		return $r;
	}
	// 
	function _outbound(){
		$confirmOutbound=$this->kclass->DB->queryFirst("SELECT COUNT(outboundid) AS count FROM outbound WHERE killed=0 AND ifConfirm=0");
		$r['confirmCount']=$confirmOutbound['count'];
		$verifyOutbound=$this->kclass->DB->queryFirst("SELECT COUNT(outboundid) AS count FROM outbound WHERE killed=0 AND ifConfirm=1 AND ifVerify=0");
		$r['verifyCount']=$verifyOutbound['count'];
		$unOutbound=$this->kclass->DB->queryFirst("SELECT COUNT(outboundid) AS count FROM outbound WHERE killed=0 AND ifConfirm=1 AND ifVerify=1 AND ifComplete=0");
		$r['unCount']=$unOutbound['count'];
		$outOutbound=$this->kclass->DB->queryFirst("SELECT COUNT(outboundid) AS count FROM outbound WHERE killed=0 AND ifConfirm=1 AND ifVerify=1 AND ifComplete=1");
		$r['outCount']=$outOutbound['count'];
		$allOutbound=$this->kclass->DB->queryFirst("SELECT COUNT(outboundid) AS count FROM outbound WHERE killed=0 AND ifConfirm=1 AND ifVerify=1 AND ifComplete=2");
		$r['allCount']=$allOutbound['count'];
		return $r;
	}
	// 
	function _inbound(){
		$confirmInbound=$this->kclass->DB->queryFirst("SELECT COUNT(inboundid) AS count FROM inbound WHERE killed=0 AND ifConfirm=0");
		$r['confirmCount']=$confirmInbound['count'];
		$verifyInbound=$this->kclass->DB->queryFirst("SELECT COUNT(inboundid) AS count FROM inbound WHERE killed=0 AND ifConfirm=1 AND ifVerify=0");
		$r['verifyCount']=$verifyInbound['count'];
		$unInbound=$this->kclass->DB->queryFirst("SELECT COUNT(inboundid) AS count FROM inbound WHERE killed=0 AND ifConfirm=1 AND ifVerify=1 AND ifComplete=0");
		$r['unCount']=$unInbound['count'];
		$outInbound=$this->kclass->DB->queryFirst("SELECT COUNT(inboundid) AS count FROM inbound WHERE killed=0 AND ifConfirm=1 AND ifVerify=1 AND ifComplete=1");
		$r['inCount']=$outInbound['count'];
		$allInbound=$this->kclass->DB->queryFirst("SELECT COUNT(inboundid) AS count FROM inbound WHERE killed=0 AND ifConfirm=1 AND ifVerify=1 AND ifComplete=2");
		$r['allCount']=$allInbound['count'];
		return $r;
	}
	function _purchaseApply(){
		$verify=$this->kclass->DB->queryFirst("SELECT COUNT(purchaseApplyid) AS count FROM `purchaseapply` WHERE killed=0 AND ifVerify=0");
		$r['verfiyCount']=$verify['count'];
		$approve=$this->kclass->DB->queryFirst("SELECT COUNT(purchaseApplyid) AS count FROM `purchaseapply` WHERE killed=0 AND ifVerify=1 AND ifApprove=0");
		$r['approveCount']=$approve['count'];
		return $r;
	}
	function _purchase(){
		$verify=$this->kclass->DB->queryFirst("SELECT COUNT(purchaseid) AS count FROM `purchase` WHERE killed=0 AND ifVerify=0");
		$r['verfiyCount']=$verify['count'];
		$approve=$this->kclass->DB->queryFirst("SELECT COUNT(purchaseid) AS count FROM `purchase` WHERE killed=0 AND ifVerify=1 AND ifApprove=0");
		$r['approveCount']=$approve['count'];
		return $r;
	}
	function _porder(){
		$verify=$this->kclass->DB->queryFirst("SELECT COUNT(porderid) AS count FROM `porder` WHERE killed=0 AND ifVerify=0");
		$r['verfiyCount']=$verify['count'];
		$approve=$this->kclass->DB->queryFirst("SELECT COUNT(porderid) AS count FROM `porder` WHERE killed=0 AND ifVerify=1 AND ifApprove=0");
		$r['approveCount']=$approve['count'];
		return $r;
	}
	/**
	 *物资盘点明细录入并返回相关数据
	 *@author Huang Jinlong 2015-01-04
	 *@param none
	 *@return none
	 */
	
	function addInventoryItem(){
		//获取传递数据
		$invtype = $this->kclass->input['invtype'];
		$materialid = $this->kclass->input['mid'];
		$materialitemid = $this->kclass->input['itemid'];
		$inventoryid = $this->kclass->input['invid'];
		$materialType = $this->kclass->input['mtype'];
		$inventoryNum = $this->kclass->input['Num'];
		$difference = $this->kclass->input['dif'];
		$stock = $this->kclass->input['stock'];
		//echo $stock;exit;
		$remark = $this->kclass->input['remark'];
		if($invtype == 'f'){//初盘数据录入
			if($materialitemid==''){
				$materialitemid = 0;
			}
			$sql = $this->kclass->DB->query("INSERT INTO inventoryitem (inventoryid,materialType,materialid,materialitemid,stock,firstInventory,difference,creator,created) VALUES({$inventoryid},{$materialType},{$materialid},{$materialitemid},{$stock},{$inventoryNum},{$difference},{$this->kclass->user['userid']},".TIMENOW.")");
		}elseif($invtype == 'c'){//复盘数据录入
			if($materialitemid==''){
				$sql = $this->kclass->DB->query("UPDATE inventoryitem SET checkInventory={$inventoryNum},difference={$difference},modifier={$this->kclass->user['userid']},modified=".TIMENOW." WHERE materialid={$materialid} AND materialType={$materialType} AND inventoryid={$inventoryid}");	
			}else{
				$sql = $this->kclass->DB->query("UPDATE inventoryitem SET checkInventory={$inventoryNum},difference={$difference},modifier={$this->kclass->user['userid']},modified=".TIMENOW." WHERE materialitemid={$materialitemid} AND materialType={$materialType} AND inventoryid={$inventoryid}");			
			}
		}else{
			if($remark!='' AND $materialitemid==''){//备注信息录入
				//判断是否存在该条盘点记录否则无法录入相应的备注信息
				$mids = $this->kclass->DB->query("SELECT materialid FROM inventoryitem WHERE inventoryid={$inventoryid} AND materialType={$materialType}");
				$i = 1;
				while($mid = $this->kclass->DB->fetchArray($mids)){
					$midarr[$i] = $mid['materialid'];
					$i++;
				}
				if(in_array($materialid,$midarr)){
					$sql = $this->kclass->DB->query("UPDATE inventoryitem SET remark='{$remark}',modifier={$this->kclass->user['userid']},modified=".TIMENOW." WHERE materialid={$materialid} AND materialType={$materialType} AND inventoryid={$inventoryid}");	
				}else{				
					echo 1;
				}
			}elseif($remark!='' AND $materialitemid!=''){
				//判断是否存在该条盘点记录否则无法录入相应的备注信息
				$itemids = $this->kclass->DB->query("SELECT materialitemid FROM inventoryitem WHERE inventoryid={$inventoryid} AND materialType={$materialType}");
				$i = 1;
				while($itemid = $this->kclass->DB->fetchArray($itemids)){
					$itemidarr[$i] = $itemid['materialitemid'];
					$i++;
				}
				if(in_array($materialitemid,$itemidarr)){
					$sql = $this->kclass->DB->query("UPDATE inventoryitem SET remark='{$remark}',modifier={$this->kclass->user['userid']},modified=".TIMENOW." WHERE materialitemid={$materialitemid} AND materialType={$materialType} AND inventoryid={$inventoryid}");	
				}else{				
					echo 1;
				}			
			}else{
				echo 2;
			}
		}
	}
}
?>