<?php
class country{
	function autoRun(){
		$this->kclass->page['title'] = '国家与币种';
		$this->baseurl = '<a href="/s.php">首页</a> - <a href="/s.php?module=country">国家与币种</a>';
		$this->right = '<a href="/s.php?module=country">列表</a> <a href="/s.php?module=country&action=add">新建</a>';
		switch($this->kclass->input['action']){
			case 'list':
				$this->modify();
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
			default:
				$this->modify();
		}
	}
	// 
	function modify(){
		set_time_limit(200000);
//		$this->_insert();
		$this->_convert();
		exit;
	}
	// 
	function _convert(){
		$countrys = $this->kclass->DB->query("SELECT * FROM country ORDER BY countryid ASC");
		$i=1;
		while($country = $this->kclass->DB->fetchArray($countrys)){
			$r = '';
			$r = $this->kclass->DB->query("
				UPDATE `region`
				SET areacode='".$country['areacode']."', 
					timezone='".$country['timezone']."', 
					currency='".$country['currency']."', 
					encurrency='".$country['encurrency']."', 
					code='".$country['code']."', 
					symbol='".$country['symbol']."'
				WHERE `country`='".$country['title']."'
			");
/*
			$r = $this->kclass->DB->query("
				UPDATE `region`
				SET areacode='".$country['areacode']."'
				WHERE `country`='".$country['title']."' AND countryid=0
			");
*/
			if($r){
				echo $i.'. '.$country['title'].' - <font color="green">成功</font><br />';
			}else{
				echo $i.'. '.$country['title'].' - <font color="green">失败</font><br />';
			}
			$i++;
		}
	}
	// 
	function _update(){
		$xml = simplexml_load_file('private/english.xml');
echo '开始';
		foreach($xml as $k => $v){
			$country = '';
			$country = $v->attributes();
			$cncountry = $this->kclass->DB->queryFirst("SELECT regionid,country FROM `region` WHERE countryid='0' AND provinceid='0' AND abbr='".$country['Code']."'");
			if($cncountry['regionid'] > 0){
				$this->kclass->DB->query("
					UPDATE `region` 
					SET encountry='".addslashes($country['Name'])."' 
					WHERE regionid='".$cncountry['regionid']."'
				");
				echo '<br /><font color="green">Country: 【'.$cncountry['country'].'】'.$cncountry['regionid'].'_'.$country['Name'].'</font>';
			}else{
				echo '<br /><font color="red">Country: xxx_'.$country['Name'].'</font>';//('."SELECT regionid,country FROM `region` WHERE countryid='0' AND provinceid='0' AND abbr='".$country['Code']."'".')';
			}
			$states = $v->children();
			if(count($states) > 1){ // 有省州
				foreach($states as $ke => $va){
					$state = '';
					$state = $va->attributes();
					$cnstate = $this->kclass->DB->queryFirst("SELECT regionid, `state` FROM `region` WHERE countryid='".$cncountry['regionid']."' AND provinceid='0' AND sabbr='".$state['Code']."'");
					if($cnstate['regionid']>0){
						$this->kclass->DB->query("
							UPDATE `region` 
							SET encountry='".addslashes($country['Name'])."', 
								enstate='".addslashes($state['Name'])."' 
							WHERE regionid='".$cnstate['regionid']."'
						");
						echo '<br />　　<font color="green">State: 【'.$cnstate['state'].'】'.$cnstate['regionid'].'_'.$state['Name'].' → '.'</font>';
					}else{
						echo '<br />　　<font color="red">State: '.$cnstate['regionid'].'_'.$state['Name'].' → '.'</font>';//('."SELECT regionid, `state` FROM `region` WHERE countryid='".$cncountry['regionid']."' AND provinceid='0' AND sabbr='".$state['Code']."'".')';
					}
					$citys = $va->children();
					foreach($citys as $key => $val){
						$city = '';
						$city = $val->attributes();
						$cn = $this->kclass->DB->queryFirst("SELECT regionid,city FROM `region` WHERE countryid='".$cncountry['regionid']."' AND provinceid='".$cnstate['regionid']."' AND cabbr='".$city['Code']."'");
						if($cn['regionid']>0){
							$this->kclass->DB->query("
								UPDATE `region` 
								SET encountry='".addslashes($country['Name'])."', 
									enstate='".addslashes($state['Name'])."', 
									encity='".addslashes($city['Name'])."' 
								WHERE regionid='".$cn['regionid']."'
							");
							echo '<br />　　　　<font color="green">City: 【'.$cn['city'].'】'.$cn['regionid'].'_'.$city['Name'].'</font>';
						}else{
							echo '<br />　　　　<font color="red">City: '.$cn['regionid'].'_'.$city['Name'].'</font>';//('."SELECT regionid,city FROM `region` WHERE countryid='".$cncountry['regionid']."' AND provinceid='".$cnstate['regionid']."' AND cabbr='".$city['Code']."'".')';
						}
					}
				}
			}elseif(count($states)==1){
				$citys = $states->children();
				foreach($citys as $key => $val){
					$city = '';
					$city = $val->attributes();
					$cn = $this->kclass->DB->queryFirst("SELECT regionid,`state` FROM `region` WHERE countryid='".$cncountry['regionid']."' AND provinceid='0' AND sabbr='".$city['Code']."'");
					if($cn['regionid']>0){
						$this->kclass->DB->query("
							UPDATE `region` 
							SET encountry='".addslashes($country['Name'])."', 
								enstate='".addslashes($city['Name'])."' 
							WHERE regionid='".$cn['regionid']."'
						");
						echo '<br />　　<font color="green">*City: 【'.$cn['state'].'】'.$cn['regionid'].'_'.$city['Name'].'</font>('."SELECT regionid,`state` FROM `region` WHERE countryid='".$cncountry['regionid']."' AND provinceid='0' AND sabbr='".$city['Code']."'".')';
					}else{
						echo '<br />　　<font color="red">*City: '.$cn['regionid'].'_'.$city['Name'].'</font>';//('."SELECT regionid,`state` FROM `region` WHERE countryid='".$cncountry['regionid']."' AND provinceid='0' AND sabbr='".$city['Code']."'".')';
					}
				}
			}
		}
	}
	// 
	function _insert(){
		$xml = simplexml_load_file('private/chinese.xml');
		foreach($xml as $k => $v){
			$country = '';
			$country = $v->attributes();
			$states = $v->children();
			$pnum=$cnum=0;
			$pnum = count($states);
			$this->kclass->DB->query("
			INSERT INTO `region` (`country` , `encountry` , `abbr` ,`countryid` , `pnum` ,`state` ,`enstate` ,`sabbr` ,`provinceid` ,`city` ,`encity` ,`cabbr` ,`areacode` ,`timezone` ,`currency` ,`code` ,`symbol` ,`remark` ,`ordering` ,`killed` ,`modified` ,`creator` ,`created`)
			VALUES ('".$country['Name']."', '', '".$country['Code']."' , '0', '".$this->kclass->iif($pnum>0, ($pnum-1), 0)."', NULL , NULL , NULL , '0', NULL , NULL , NULL , '', '', '', '', '', '', '0', '0', '".TIMENOW."', '".$this->kclass->user['userid']."', '".TIMENOW."');
			");
			$countryid = $this->kclass->DB->insertID();
			echo '<br />Country: '.$countryid.'_'.$country['Name'].'(pnum: '.$pnum.', cnum:'.$cnum.')';
			if($pnum > 1){ // 有省州
				foreach($states as $ke => $va){
					$state = '';
					$state = $va->attributes();
					$citys = $va->children();
					$cnum=0;
					$cnum = count($citys);
					$this->kclass->DB->query("
					INSERT INTO `region` (`country` , `encountry` , `abbr` ,`countryid` , `pnum` ,`state` ,`enstate` ,`sabbr` ,`provinceid` , `cnum` ,`city` ,`encity` ,`cabbr` ,`areacode` ,`timezone` ,`currency` ,`code` ,`symbol` ,`remark` ,`ordering` ,`killed` ,`modified` ,`creator` ,`created`)
					VALUES ('".$country['Name']."', '', '".$country['Code']."' , '".$countryid."', '".$this->kclass->iif($pnum==0, 0, ($pnum-1))."', '".$state['Name']."' , NULL , '".$state['Code']."', '0', '".$this->kclass->iif($cnum==0, 0, ($cnum-1))."', NULL , NULL , NULL , '', '', '', '', '', '', '0', '0', '".TIMENOW."', '".$this->kclass->user['userid']."', '".TIMENOW."');
					");
					$stateid = $this->kclass->DB->insertID();
					echo '<br />　　State: '.$stateid.'_'.$state['Name'].'(pnum: '.$pnum.', cnum:'.$cnum.')';
					foreach($citys as $key => $val){
						$city = '';
						$city = $val->attributes();
						$this->kclass->DB->query("
						INSERT INTO `region` (`country` , `encountry` , `abbr` ,`countryid` , `pnum` ,`state` ,`enstate` ,`sabbr` ,`provinceid` , `cnum`,`city` ,`encity` ,`cabbr` ,`areacode` ,`timezone` ,`currency` ,`code` ,`symbol` ,`remark` ,`ordering` ,`killed` ,`modified` ,`creator` ,`created`)
						VALUES ('".$country['Name']."', '', '".$country['Code']."' , '".$countryid."', '".($pnum-1)."', '".$state['Name']."' , NULL , '".$state['Code']."', '".$stateid."', '".($cnum-1)."', '".$city['Name']."' , NULL , '".$city['Code']."' , '', '', '', '', '', '', '0', '0', '".TIMENOW."', '".$this->kclass->user['userid']."', '".TIMENOW."');
						");
						echo '<br />　　　　City: '.$this->kclass->DB->insertID().'_'.$city['Name'].'(pnum: '.$pnum.', cnum:'.$cnum.')';
					}
				}
			}elseif($pnum==1){
				$citys = $states->children();
				$cnum=0;
				$cnum = count($citys);
				foreach($citys as $key => $val){
					$city = '';
					$city = $val->attributes();
					$this->kclass->DB->query("
					INSERT INTO `region` (`country` , `encountry` , `abbr` ,`countryid` , `pnum` ,`state` ,`enstate` ,`sabbr` ,`provinceid` , `cnum` ,`city` ,`encity` ,`cabbr` ,`areacode` ,`timezone` ,`currency` ,`code` ,`symbol` ,`remark` ,`ordering` ,`killed` ,`modified` ,`creator` ,`created`)
					VALUES ('".$country['Name']."', '', '".$country['Code']."' , '".$countryid."', '".$this->kclass->iif($cnum==0, 0, ($cnum-1))."', '".$city['Name']."' , NULL , '".$city['Code']."', '0', '0', NULL , NULL , NULL , '', '', '', '', '', '', '0', '0', '".TIMENOW."', '".$this->kclass->user['userid']."', '".TIMENOW."');
					");
					echo '<br />　　*City: '.$this->kclass->DB->insertID().'_'.$city['Name'].'(pnum: '.$pnum.', cnum:'.$cnum.')';
				}
				$this->kclass->DB->query("UPDATE region SET pnum='".$cnum."' WHERE regionid='".$countryid."'");
			}
		}
	}
}
?>