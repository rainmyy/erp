<?php
//
class test{
	var $kclass;
	var $purchaseType=array(array('entitle'=>'normal','title'=>'普通采购'),array('entitle'=>'entrust','title'=>'加工采购'));
	//
	function autorun(){
		$this->baseurl='<a href="/s.php?">首页</a>';
		switch($this->kclass->input['action']){
			case 'exchange':
				return $this->exchange();
			case 'import':
				return $this->import();
			case 'doimport':
				return $this->doimport();
			break;
		}
	}
	//
	function exchange(){
		/* 入库明细增加orderid和sampleid
		 $items=$this->kclass->DB->query("
			SELECT inboundid,orderid,sampleid
			FROM inbound
			WHERE (orderid<>0 OR sampleid<>0)
			ORDER BY inboundid ASC 
		");
		if($this->kclass->DB->numRows()){
			while($item=$this->kclass->DB->fetchArray($items)){
				$this->kclass->DB->query("
					UPDATE inbounditem SET
						sampleid='".$item['sampleid']."',
						orderid='".$item['orderid']."'
					WHERE inboundid='".$item['inboundid']."'");
			}
		}*/
		/*	// 获得serialnoresult顾客链接中的值
		$reg='/^<a.+>(.+)<\/a>$/';
		$items=$this->kclass->DB->query("
			SELECT resultid,customer
			FROM serialnoresult
			WHERE customer<>''
			ORDER BY resultid ASC
		");
		if($this->kclass->DB->numRows()){
			while($item=$this->kclass->DB->fetchArray($items)){
				$a=preg_match($reg,$item['customer'],$matches);
				$this->kclass->DB->query("UPDATE serialnoresult SET customer='{$matches[1]}' WHERE resultid={$item['resultid']}");
			}
		}*/
		
		/*$this->kclass->DB->query("BEGIN");
		$this->kclass->DB->query("SAVEPOINT test");
		for($i=0;$i<10000;$i++){
			$unit=$this->kclass->DB->queryFirst("SELECT title FROM unit WHERE unitid=1");
			$mysql[$i]=$this->kclass->DB->query("INSERT test (title) VALUES ('{$unit['title']}')");
			$this->kclass->DB->query("INSERT aaa (title,remark) VALUES ('{$unit['title']}','{$unit['title']}')");
		}
		if(count($mysql)<10000){
			$this->kclass->DB->query("ROLLBACK to test");
		}else{
			$this->kclass->DB->query("COMMIT");
		}*/
		/* 获得订单确定时间和修改时间
		$results=$this->kclass->DB->query("SELECT resultid,orderid,sampleid,customerid FROM `serialnoresult` WHERE (orderid>0 OR sampleid>0) AND orderCreated=0");
		if($this->kclass->DB->numRows()){
			while($result=$this->kclass->DB->fetchArray($results)){
				if($result['orderid']>0){
					$order=$this->kclass->DB->queryFirst("SELECT orderid,created,modified FROM `order` WHERE orderid={$result['orderid']}");
					$created=$order['created'];$modified=$order['modified'];
				}elseif($result['sampleid']>0){
					$sample=$this->kclass->DB->queryFirst("SELECT sampleid,created,modified FROM `sample` WHERE sampleid={$result['sampleid']}");
					$created=$sample['created'];$modified=$sample['modified'];
				}
				if($result['customerid']>0){
					$customer=$this->kclass->DB->queryFirst("SELECT created FROM customer WHERE customerid={$result['customerid']}");
					$customerCreated=$customer['created'];
				}
				$this->kclass->DB->query("
					UPDATE serialnoresult SET 
						orderCreated='{$created}',
						orderModified='{$modified}',
						customerCreated='{$customerCreated}'
					WHERE resultid={$result['resultid']}
				");
			}
		}*/
		/*
		$orders=$this->kclass->DB->query("SELECT orderid FROM `order` WHERE killed=0");
		if($this->kclass->DB->numRows()){
			while($order=$this->kclass->DB->fetchArray($orders)){
				// 入库
				$inbound=$this->kclass->DB->queryFirst("SELECT inboundid,ifComplete FROM inbound WHERE orderid={$order['orderid']}");
				if($inbound['inboundid']>0){
					if($inbound['ifComplete']==1){
						$this->kclass->DB->query("UPDATE `order` SET ifInbound=1 WHERE orderid={$order['orderid']}");
					}elseif($inbound['ifComplete']==2){
						$this->kclass->DB->query("UPDATE `order` SET ifInbound=2 WHERE orderid={$order['orderid']}");
					}
				}
				// 出库
				$outbound=$this->kclass->DB->queryFirst("SELECT outboundid,ifComplete FROM outbound WHERE orderid={$order['orderid']}");
				if($outbound['outboundid']>0){
					if($outbound['ifComplete']==1){
						$this->kclass->DB->query("UPDATE `order` SET ifOutbound=1 WHERE orderid={$order['orderid']}");
					}elseif($outbound['ifComplete']==2){
						$this->kclass->DB->query("UPDATE `order` SET ifOutbound=2 WHERE orderid={$order['orderid']}");
					}
				}
			}
		}*/
		
		/*$samples=$this->kclass->DB->query("SELECT sampleid FROM `sample` WHERE killed=0");
		if($this->kclass->DB->numRows()){
			while($sample=$this->kclass->DB->fetchArray($samples)){
				// 入库
				$inbound=$this->kclass->DB->queryFirst("SELECT inboundid,ifComplete FROM inbound WHERE sampleid={$sample['sampleid']}");
				if($inbound['inboundid']>0){
					if($inbound['ifComplete']==1){
						$this->kclass->DB->query("UPDATE `sample` SET ifOutbound=1 WHERE sampleid={$sample['sampleid']}");
					}elseif($inbound['ifComplete']==2){
						$this->kclass->DB->query("UPDATE `sample` SET ifOutbound=2 WHERE sampleid={$sample['sampleid']}");
					}
				}
				// 出库
				$outbound=$this->kclass->DB->queryFirst("SELECT outboundid,ifComplete FROM outbound WHERE sampleid={$sample['sampleid']}");
				if($outbound['outboundid']>0){
					if($outbound['ifComplete']==1){
						$this->kclass->DB->query("UPDATE `sample` SET ifOutbound=1 WHERE sampleid={$sample['sampleid']}");
					}elseif($outbound['ifComplete']==2){
						$this->kclass->DB->query("UPDATE `sample` SET ifOutbound=2 WHERE sampleid={$sample['sampleid']}");
					}
				}
			}
		}*/
		/*// 插入到价格记录中
		$versions=$this->kclass->DB->query("SELECT versionid,materialid,price,price1,qualified FROM materialversion WHERE killed=0 ORDER BY versionid ASC");
		if($this->kclass->DB->numRows()){
			while($version=$this->kclass->DB->fetchArray($versions)){
				$this->kclass->DB->query("
					INSERT INTO pricerecord (materialid,versionid,price,price1,quantity,created,creator)
					VALUES ('{$version['materialid']}','{$version['versionid']}','{$version['price']}','{$version['price1']}','{$version['qualified']}','".TIMENOW."','{$this->kclass->user['userid']}')
				");
			}
		}*/
		
		/*// 修改物资停用或者使用
		$materials=$this->kclass->DB->query("SELECT materialid FROM material WHERE killed=0 AND attrid=6 ORDER BY materialid ASC");
		if($this->kclass->DB->numRows()){
			while($material=$this->kclass->DB->fetchArray($materials)){
				$bom=$this->kclass->DB->queryFirst("SELECT 1 FROM bom WHERE childid={$material['materialid']}");
				if(!$bom){
					$this->kclass->DB->query("UPDATE material SET isUsed=0 WHERE materialid={$material['materialid']}");
				}
			}
		}*/
		/*// 修改自产单价为0
		$versions=$this->kclass->DB->query("
			SELECT versionid 
			FROM materialversion AS mv 
			LEFT JOIN material AS m ON (mv.materialid=m.materialid)
			WHERE m.originid<>2
		");
		if($this->kclass->DB->numRows()){
			while($version=$this->kclass->DB->fetchArray($versions)){
				$this->kclass->DB->query("UPDATE materialversion SET price=0 WHERE versionid={$version['versionid']}");
			}
		}*/
	}
	function import(){
		$upload=$this->kclass->upload(array('title'=>'批量导入：'));
$body=<<<EOF
<form method="post" action="/s.php?module=test&action=doimport">
<input type="hidden" name="module" value="test">
<input type="hidden" name="action" value="doimport">
{$upload}
<table class="tlist tsuite" align="center" width="100%">
<tbody>
<tr class="odd center" nohover>
<td colspan=4><input type="submit" id="submitButton" value="  提交  " ><input type="reset" value="  复位  "/></td>
</tr>
</tbody>
</table>
</form>
EOF;
		$this->kclass->page['title']=' 批量导入物资';
		$this->kclass->page['main']=$this->kclass->skin->dbody(array('title'=>$this->baseurl.=' - 批量导入物资','right'=>'<a href="/s.php?module=material">返回列表</a>','body'=>$body));
	}
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
			if($attach=$this->kclass->DB->queryFirst("SELECT COUNT(attachid) AS n,newName,folder,type FROM `attachs` WHERE `module`='".$this->kclass->input['module']."' AND `mid`=0 AND `image`<> 1 ORDER BY created DESC")){
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
								$materialno=substr($val[0],0,3).str_pad(substr($val[0],3,6),3,'0',STR_PAD_LEFT);
								$material=$this->kclass->DB->queryFirst("SELECT materialid FROM material WHERE materialno='{$materialno}'");
								$materialid=$this->kclass->iif($material['materialid']>0,$material['materialid'],0);
								$materialitem=$this->kclass->DB->queryFirst("SELECT itemid FROM materialitem WHERE itemno='{$materialno}'");
								$materialitemid=$this->kclass->iif($materialitem['itemid']>0,$materialitem['itemid'],0);
								$this->kclass->DB->query("
									INSERT INTO inout_2012
										(materialid,materialitemid,materialno,title,standard,unit,lastQualified,lastDisqualified,inQualified,inDisqualified,in1,in2,in3,in4,in5,in6,in7,in8,in9,in10,in11,in12,out1,out2,out3,out4,out5,out6,out7,out8,out9,out10,out11,out12,sale1,sale2,sale3,sale4,sale5,sale6,sale7,sale8,sale9,sale10,sale11,sale12,outQualified,outDisqualified,saleQualified,otherOutQualified,qualified,disqualified)
									VALUES
										('".$materialid."','".$materialitemid."','".$materialno."','".$val[1]."','".$val[2]."','".$val[3]."','".$this->kclass->iif($val[4]!='',$val[4],0)."','".$this->kclass->iif(floatval($val[5])!='',$val[5],0)."','".$this->kclass->iif(floatval($val[6])!='',$val[6],0)."','".$this->kclass->iif(floatval($val[7])!='',$val[7],0)."','".$this->kclass->iif(floatval($val[8])!='',$val[8],0)."','".$this->kclass->iif(floatval($val[9])!='',$val[9],0)."','".$this->kclass->iif(floatval($val[10])!='',$val[10],0)."','".$this->kclass->iif(floatval($val[11])!='',$val[11],0)."','".$this->kclass->iif(floatval($val[12])!='',$val[12],0)."','".$this->kclass->iif(floatval($val[13])!='',$val[13],0)."','".$this->kclass->iif(floatval($val[14])!='',$val[14],0)."','".$this->kclass->iif(floatval($val[15])!='',$val[15],0)."','".$this->kclass->iif(floatval($val[16])!='',$val[16],0)."','".$this->kclass->iif(floatval($val[17])!='',$val[17],0)."','".$this->kclass->iif(floatval($val[18])!='',$val[18],0)."','".$this->kclass->iif(floatval($val[19])!='',$val[19],0)."','".$this->kclass->iif(floatval($val[20])!='',$val[20],0)."','".$this->kclass->iif(floatval($val[21])!='',$val[21],0)."','".$this->kclass->iif(floatval($val[22])!='',$val[22],0)."','".$this->kclass->iif(floatval($val[23])!='',$val[23],0)."','".$this->kclass->iif(floatval($val[24])!='',$val[24],0)."','".$this->kclass->iif(floatval($val[25])!='',$val[25],0)."','".$this->kclass->iif(floatval($val[26])!='',$val[26],0)."','".$this->kclass->iif(floatval($val[27])!='',$val[27],0)."','".$this->kclass->iif(floatval($val[28])!='',$val[28],0)."','".$this->kclass->iif(floatval($val[29])!='',$val[29],0)."','".$this->kclass->iif(floatval($val[30])!='',$val[30],0)."','".$this->kclass->iif(floatval($val[31])!='',$val[31],0)."','".$this->kclass->iif(floatval($val[32])!='',$val[32],0)."','".$this->kclass->iif(floatval($val[33])!='',$val[33],0)."','".$this->kclass->iif(floatval($val[34])!='',$val[34],0)."','".$this->kclass->iif(floatval($val[35])!='',$val[35],0)."','".$this->kclass->iif(floatval($val[36])!='',$val[36],0)."','".$this->kclass->iif(floatval($val[37])!='',$val[37],0)."','".$this->kclass->iif(floatval($val[38])!='',$val[38],0)."','".$this->kclass->iif(floatval($val[39])!='',$val[39],0)."','".$this->kclass->iif(floatval($val[40])!='',$val[40],0)."','".$this->kclass->iif(floatval($val[41])!='',$val[41],0)."','".$this->kclass->iif(floatval($val[42])!='',$val[42],0)."','".$this->kclass->iif(floatval($val[43])!='',$val[43],0)."','".$this->kclass->iif(floatval($val[44])!='',$val[44],0)."','".$this->kclass->iif(floatval($val[45])!='',$val[45],0)."','".$this->kclass->iif(floatval($val[46])!='',$val[46],0)."','".$this->kclass->iif(floatval($val[47])!='',$val[47],0)."','".$this->kclass->iif(floatval($val[48])!='',$val[48],0)."','".$this->kclass->iif(floatval($val[49])!='',$val[49],0)."')
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
				'url' => '/s.php?',
				'sec' => 2
			));
		}else{
			$this->kclass->messager(array(
				'title' => '批量导入物资',
				'text' => '正在批量导入物资，每次导入10个，请等待自动返回页面...',
				'url' => '/s.php?module=test&action=doimport&times='.$this->kclass->input['times'],
				'sec' => 2
			));
		}
	}
}

?>