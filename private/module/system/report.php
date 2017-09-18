<?php
/**
 * 
 * @author mayinghao
 *
 */
class report{
	function autoRun(){
		switch($this->kclass->input['action']){
			case 'list':
				$this->mmlist();
			break;
			case 'bomCalculate':
				$this->bomCalculate();
			break;
			case 'showHistoryInOut':
				$this->showHistoryInOut();
			break;
			default:
				$this->mmlist();
		}
	}
	// 
	function bomCalculate(){
		$materials=$this->kclass->DB->query("
			SELECT mv.versionid,mv.materialid,mv.itemid,mv.qualified,m.min,m.criticalNumber
			FROM materialversion AS mv
			LEFT JOIN material AS m ON (mv.materialid=m.materialid)
			WHERE mv.killed=0 AND m.attrid<>8
			ORDER BY m.attrid DESC,m.materialno ASC
		");
		if($this->kclass->DB->numRows()){	
			while($material=$this->kclass->DB->fetchArray($materials)){
				$materialinfo=$this->kclass->getMaterial(array('materialid'=>$material['materialid'],'itemid'=>$material['itemid']));
				if($materialinfo){
					$materialtr.='<tr id="version'.$material['versionid'].'" class="mtr"><td>'.$materialinfo['no'].'</td><td align="right" class="quantity">'.$material['qualified'].$materialinfo['unit'].'</td><td class="ptotal"></td><td align="right" class="min">'.$materialinfo['min'].'</td><td align="right" class="criticalNumber">'.$material['criticalNumber'].'</td><td class="accountNumber"></td><td><input type="text" name="" size=7 ></td></tr>';
				}
			}
		}
		$show='<a id="s_0" href="javascript:dc.bom.show(0)">全部</a>　<a id="s_1" href="javascript:dc.bom.show(1)">需求明细</a>　<a id="s_2" href="javascript:dc.bom.show(2)">只看红色</a>　<a id="s_3" href="javascript:dc.bom.show(3)">只看黄色</a>';
$this->kclass->page['main'] = <<<EOF
<div class="title">BOM计算器</div>
<table class="left" style="width:500px"><thead><tr><th colspan=2>筛选</th></thead>
<tbody>
	<td>明细显示：</td><td>{$show}</td>
</tbody>
</table>
<div class="clear"></div>
 <table class="left" id="list">
<tr id="first"><td></td><td><a onclick="dc.bom.addColumn()" class="hand small">添加产品计算</a></td><td>计划用量</td><td>最小值</td><td>临界值</td><td>结算数</td><td>自定义数量</td></tr>
<tr id="second"><td width="50">物资编号</td><td width="80">库存</td><td></td><td></td><td></td><td></td><td></td></tr>
{$materialtr}
</table>
EOF;
		$this->kclass->page['pageTitle']='BOM计算器';
		$this->kclass->page['caption'] = 'Customer No.: '.$customer['customerno'].'<br>Created: '.date('Y-m-d', $customer['created']).'. Modified: '.date('Y-m-d', $customer['modified']);
	}
	function showHistoryInOut(){
		if($this->kclass->input['year']=='2010'){
			$table='inout_2010';
			$title='2010年物资收发存累计';
		}elseif($this->kclass->input['year']=='2011'){
			$table='inout_2011';
			$title='2011年物资收发存累计';
		}elseif($this->kclass->input['year']=='2012'){
			$table='inout_2012';
			$title='2012年物资收发存累计';
		}
		if($table){
			$items=$this->kclass->DB->query("SELECT * FROM {$table} ORDER BY materialno ASC");
			if($this->kclass->DB->numRows()){
				$i=1;
				while($item=$this->kclass->DB->fetchArray($items)){
					$itemtr.='<tr><td>'.$i.'</td><td>'.$item['materialno'].'</td><td>'.$item['title'].'</td><td>'.$item['standard'].'</td><td>'.$item['unit'].'</td><td>'.$item['lastQualified'].'</td><td>'.$item['lastDisqualified'].'</td><td>'.$item['inQualified'].'</td><td>'.$item['inDisqualified'].'</td><td>'.$item['in1'].'</td><td>'.$item['in2'].'</td><td>'.$item['in3'].'</td><td>'.$item['in4'].'</td><td>'.$item['in5'].'</td><td>'.$item['in6'].'</td><td>'.$item['in7'].'</td><td>'.$item['in8'].'</td><td>'.$item['in9'].'</td><td>'.$item['in10'].'</td><td>'.$item['in11'].'</td><td>'.$item['in12'].'</td><td>'.$item['out1'].'</td><td>'.$item['out2'].'</td><td>'.$item['out3'].'</td><td>'.$item['out4'].'</td><td>'.$item['out5'].'</td><td>'.$item['out6'].'</td><td>'.$item['out7'].'</td><td>'.$item['out8'].'</td><td>'.$item['out9'].'</td><td>'.$item['out10'].'</td><td>'.$item['out11'].'</td><td>'.$item['out12'].'</td><td>'.$item['sale1'].'</td><td>'.$item['sale2'].'</td><td>'.$item['sale3'].'</td><td>'.$item['sale4'].'</td><td>'.$item['sale5'].'</td><td>'.$item['sale6'].'</td><td>'.$item['sale7'].'</td><td>'.$item['sale8'].'</td><td>'.$item['sale9'].'</td><td>'.$item['sale10'].'</td><td>'.$item['sale11'].'</td><td>'.$item['sale12'].'</td><td>'.$item['outQualified'].'</td><td>'.$item['outDisqualified'].'</td><td>'.$item['saleQualified'].'</td><td>'.$item['otherOutQualified'].'</td><td>'.$item['qualified'].'</td><td>'.$item['disqualified'].'</td></tr>';
					$i++;
				}
			}
		}
		$this->kclass->page['main'] =<<<EOF
<div style="margin-top:30px;font-size:16px"><a href="/r.php?module=report&action=showHistoryInOut&year=2010">2010收发存统计</a>　
	<a href="/r.php?module=report&action=showHistoryInOut&year=2011">2011收发存统计</a>　
	<a href="/r.php?module=report&action=showHistoryInOut&year=2012">2012收发存统计</a>　</div>
<div class="title">{$title}</div>
<table style="width:6000px">
<tbody>
<tr><td width="30">ID</td><td>物资编号</td><td width="150">物资名称</td><td>物资规格</td><td>单位</td><td>去年结存（良品）</td><td>去年结存（不良品）</td><td>入库合计（良品）</td><td>入库合计（不良品）</td><td>收料（1月）</td><td>收料（2月）</td><td>收料（3月）</td><td>收料（4月）</td><td>收料（5月）</td><td>收料（6月）</td><td>收料（7月）</td><td>收料（8月）</td><td>收料（9月）</td><td>收料（10月）</td><td>收料（11月）</td><td>收料（12月）</td><td>发料（1月）</td><td>发料（2月）</td><td>发料（3月）</td><td>发料（4月）</td><td>发料（5月）</td><td>发料（6月）</td><td>发料（7月）</td><td>发料（8月）</td><td>发料（9月）</td><td>发料（10月）</td><td>发料（11月）</td><td>发料（12月）</td><td>销售（1月）</td><td>销售（2月）</td><td>销售（3月）</td><td>销售（4月）</td><td>销售（5月）</td><td>销售（6月）</td><td>销售（7月）</td><td>销售（8月）</td><td>销售（9月）</td><td>销售（10月）</td><td>销售（11月）</td><td>销售（12月）</td><td>发料合计（良品）</td><td>发料合计（不良品）</td><td>销售合计</td><td>非生产合计</td><td>结存（良品）</td><td>结存（不良品）</td></tr>
{$itemtr}
</tbody>
</table>
EOF;
		$this->kclass->page['pageTitle']='BOM计算器';
		$this->kclass->page['caption'] = 'Customer No.: '.$customer['customerno'].'<br>Created: '.date('Y-m-d', $customer['created']).'. Modified: '.date('Y-m-d', $customer['modified']);
	}
}
?>