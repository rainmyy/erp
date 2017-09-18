<?php
// 
class mobile{
	# Global
	var $kclass;
	var $output;
	var $outboundType=array(array('id'=>1,'title'=>'领料(BOM)'),array('id'=>2,'title'=>'领料(非BOM)'),array('id'=>3,'title'=>'订单出库'),array('id'=>4,'title'=>'外发加工'),array('id'=>5,'title'=>'退货'),array('id'=>6,'title'=>'补料'),array('id'=>7,'title'=>'补料(BOM)'),array('id'=>8,'title'=>'样品出库'));
	var $inboundType=array(array('id'=>1,'title'=>'进仓'),array('id'=>2,'title'=>'退料(非BOM)'),array('id'=>3,'title'=>'订单入库'),array('id'=>4,'title'=>'收料'),array('id'=>5,'title'=>'退料(BOM)'),array('id'=>6,'title'=>'样品入库'));
	/*--------------------*/
	// Auto run function
	/*--------------------*/
	function autoRun(){
		$this->kclass->page['title'] = '移动ERP系统';
		$this->baseurl = $this->kclass->page['title'];
		switch($this->kclass->input['action']){
			case 'list':
				$this->index();
				break;
			case 'nav':
				$this->nav();
				break;
			case 'order':
				$this->order();
				break;
			case 'sample':
				$this->sample();
				break;
			case 'requirement':
				$this->requirement();
				break;
			case 'task':
				$this->task();
				break;
			case 'dispatch':
				$this->dispatch();
				break;
			case 'outbound':
				$this->outbound();
				break;
			case 'inbound':
				$this->inbound();
				break;
			case 'rank':
				$this->rank();
				break;
			case 'online':
				$this->online();
				break;
			case 'center':
				$this->center();
				break;

			default:
				$this->index();
		}
	}
//
	// 
	function index(){
		if($this->kclass->user['departmentid']>0){
			$body=$this->_pmcIndex();
		}

		$this->kclass->page['main'] .= $body;
	}
		// 
	function nav(){
		if($this->kclass->user['departmentid']>0){
		$orderPO=$this->_orderList(array('type'=>'PO'));
		$sample=$this->_sampleList();
		$requirement=$this->_requirementList();
		$task=$this->_taskList();
		$dispatch=$this->_dispatchList();
		$outbound=$this->_outboundList();
		$inbound=$this->_inboundList();
		$online=$this->_online();
			$body='
<ul data-role="listview" data-count-theme="b">
	<li><a href="/m.php?action=order" data-transition="slide">PO单<span class="ui-li-count">共'.$orderPO['num'].'条</span></a></li>
	<li><a href="/m.php?action=sample" data-transition="slide">样品单<span class="ui-li-count">共'.$sample['num'].'条</span></a></li>
	<li><a href="/m.php?action=requirement" data-transition="slide">需求计划<span class="ui-li-count">共'.$requirement['num'].'条</span></a></li>
	<li><a href="/m.php?action=task" data-transition="slide">任务单<span class="ui-li-count">共'.$task['num'].'条</span></a></li>
	<li><a href="/m.php?action=dispatch" data-transition="slide">派工单<span class="ui-li-count">共'.$dispatch['num'].'条</span></a></li>
	<li><a href="/m.php?action=outbound" data-transition="slide">出库单<span class="ui-li-count">共'.$outbound['num'].'条</span></a></li>
	<li><a href="/m.php?action=inbound" data-transition="slide">入库单<span class="ui-li-count">共'.$inbound['num'].'条</span></a></li>
	<li><a href="/m.php?action=rank" data-transition="slide">操作排行<span class="ui-li-count">共10条</span></a></li>
	<li><a href="/m.php?action=online" data-transition="slide">在线人数<span class="ui-li-count">共'.$online['num'].'人在线</span></a></li>
</ul>
			';
		}

		$this->kclass->page['main'] .= $body;
	}
		// 
	function order(){
		if($this->kclass->user['departmentid']>0){
			$b=$this->_orderList(array('type'=>'PO'));
		}

		$this->kclass->page['main'] .= $b['list'];
	}
		// 
	function sample(){
		if($this->kclass->user['departmentid']>0){
			$b=$this->_sampleList();
		}

		$this->kclass->page['main'] .= $b['list'];
	}
	function requirement(){
		if($this->kclass->user['departmentid']>0){
			$b=$this->_requirementList();
		}

		$this->kclass->page['main'] .= $b['list'];
	}
	function task(){
		if($this->kclass->user['departmentid']>0){
			$b=$this->_taskList();
		}

		$this->kclass->page['main'] .= $b['list'];
	}
	function dispatch(){
		if($this->kclass->user['departmentid']>0){
			$b=$this->_dispatchList();
		}

		$this->kclass->page['main'] .= $b['list'];
	}
	function outbound(){
		if($this->kclass->user['departmentid']>0){
			$b=$this->_outboundList();
		}

		$this->kclass->page['main'] .= $b['list'];
	}
	function inbound(){
		if($this->kclass->user['departmentid']>0){
			$b=$this->_inboundList();
		}

		$this->kclass->page['main'] .= $b['list'];
	}
	function rank(){
		if($this->kclass->user['departmentid']>0){
			$b=$this->_clickRank();
		}

		$this->kclass->page['main'] .= $b;
	}
	function online(){
		if($this->kclass->user['departmentid']>0){
			$b=$this->_online();
		}

		$this->kclass->page['main'] .= $b['item'];
	}
	function center(){
		if($this->kclass->user['departmentid']>0){
			$joindate = date('Y-m-d H:i:s', $this->kclass->user['joindate']);
			$lastVisit = date('Y-m-d H:i:s', $this->kclass->user['lastVisit']);
			$lastActivity = date('Y-m-d H:i:s', $this->kclass->user['lastActivity']);
			$b='<ul data-role="listview" data-inset="true">
    <li data-role="list-divider">'.$this->kclass->user['username'].'</li>
    <li><a href="#">
        <h2>姓名：'.$this->kclass->user['realname'].'</h2>
				<p></p>
        <p><strong>部门：'.$this->kclass->user['department'].'</strong></p>
				<p></p>
				<p>加入时间：'.$joindate.'</p>
				<p></p>
				<p>上次登录：'.$lastVisit.'</p>
				<p></p>
				<p><strong>最后活动：'.$lastActivity.'</strong></p>
    </a></li>
    <li><a href="#">Email '.$this->kclass->user['email'].'</a></li>
    <li><a href="#">Phone '.$this->kclass->user['mobile'].'</a></li>
    <li><a href="#">Address '.$this->kclass->user['address'].'</a></li>
</ul>';
		}

		$this->kclass->page['main'] .= $b;
	}
	// 
	function _pmcIndex(){
		$after5day = TIMENOW + 432000;
		$before5day = TIMENOW - 432000;
		$orderPO=$this->_orderList(array('type'=>'PO'));
		$sample=$this->_sampleList();
		$requirement=$this->_requirementList();
		$task=$this->_taskList();
		$dispatch=$this->_dispatchList();
		$outbound=$this->_outboundList();
		$inbound=$this->_inboundList();
		$rank=$this->_clickRank();
		$online=$this->_online();
		$dd='<br>　　　　<i>试运行进行中...</i><br><br>　　　　<i>11月计划如下：<br><br>　　　　<span class="red">8号</span>打印模块上线，<span class="red">15号</span>系统优化完善。<br><br>　　　　<span class="red">23号</span>流程模块，<span class="red">29号</span>通知模块。</i>';
		//
		$rs=array_merge_recursive($orderPO['item'],$sample['item'],$requirement['item'],$task['item'],$dispatch['item'],$outbound['item'],$inbound['item']);
		arsort($rs['created']);
		$i=1;
		foreach($rs['created'] AS $key=>$val){
			$news.='<li>'.$this->kclass->iif(date('Y-m-d', $rs['created'][$key])==date('Y-m-d', TIMENOW),'<i>new</i>','').' 　['.$i.'] 　<i>'.$rs['creator'][$key].' 　建立了 　<b>'.$rs['mname'][$key].'</b>  　<a href="/s.php?module='.$rs['module'][$key].'&action=view&'.$rs['module'][$key].'id='.$rs['mid'][$key].'"> '.$rs['mno'][$key].'</a></i><i><span class="green"> 　'.date('Y-m-d H:i', $rs['created'][$key]).'</span></i></li>';
			$i++;
			if($i==6)break;
		}
		unset($rs);

$body = <<<EOF
<ul data-role="listview" data-inset="true">
    <li data-role="list-divider">系统公告</li>
    <li data-role="fieldcontain">
			{$dd}
		</li>
</ul>
<ul data-role="listview" data-inset="true">
    <li data-role="list-divider">最近动态</li>
    {$news}
</ul>
EOF;
	return $body;
	}
	// 
	function _orderList($b){
		$after5day = TIMENOW + 432000;
		$before5day = TIMENOW - 432000;
		$rs=array();
		$orders=$this->kclass->DB->query("
			SELECT `order`.orderid, `order`.orderno,`order`.ordertitle, `order`.customerid,`order`.status, `order`.supplierid, `order`.shipmentDate, `order`.cover, `order`.attachs, `order`.images, `order`.modified, `order`.created, `order`.killed,`order`.ifVerify,`order`.ifApprove,`order`.ifChooser,
				route.title AS routeTitle, route.caption AS routeCaption, `route`.typelist,
				customer.title AS customerTitle, shipmethod.title AS shipmethod,
				m.realname AS modifier, c.realname AS creator,t.realname AS tracker,d.realname AS director
			FROM `order`
			LEFT JOIN orderroute AS route ON (route.routeid=`order`.routeid)
			LEFT JOIN `customer` ON (customer.customerid=`order`.customerid)
			LEFT JOIN `shipmethod` ON (shipmethod.shipmethodid=`order`.shipmethodid)
			LEFT JOIN `member` AS m ON (m.userid=`order`.modifier)
			LEFT JOIN `member` AS c ON (c.userid=`order`.creator)
			LEFT JOIN `member` AS t ON (t.userid=`order`.tracker)
			LEFT JOIN `member` AS d ON (d.userid=`order`.director)
			WHERE  `order`.type='".$b['type']."' AND `order`.killed=0
			ORDER BY `order`.created DESC
			LIMIT 5
		");
		if($sum=$this->kclass->DB->numRows()){
			$i=1;
			while($order = $this->kclass->DB->fetchArray($orders)){
				$status='';
				if($order['ifVerify']==0){
					$status = '<span class="darkred">待审核</span>';
				}elseif($order['ifVerify']==-1){
					$status = '<span class="red">审核未通过</span>';
				}elseif($order['ifVerify']==1 AND $order['ifApprove']==0){
					$status = '<span class="darkred">待审批</span>';
				}elseif($order['ifVerify']==1 AND $order['ifApprove']==-1){
					$status = '<span class="red">审批未通过</span>';
				}elseif($order['ifVerify']==1 AND $order['ifApprove']==1){
					$status = '<span class="darkred">待投产</span>';
				}
				if($b['type']=='PO'){
					if($order['ifChooser']==1){
						$status='<span class="">已投产</span>';
					}
					$outbound=$this->kclass->DB->queryFirst("
						SELECT outboundid
						FROM outbound
						WHERE killed=0 AND ifComplete=2 AND orderid='".$order['orderid']."'
					");
					if($outbound){
						$status='<span class="">已出库</span>';
					}
				}
				$rs['module'][]='order';
				$rs['mname'][]='订单(PO)';
				$rs['mno'][]=$order['orderno'];
				$rs['mid'][]=$order['orderid'];
				$rs['creator'][]=$order['creator'];
				$rs['created'][]=$order['created'];
				$itemtr.='<li>'.$this->kclass->iif(date('Y-m-d', $order['created'])==date('Y-m-d', TIMENOW),'<i> new</i>','').'　 '.$i.'　 ['.$order['ordertitle'].']　 '.$order['orderno'].'　 '.$status.'　交货日期：'.date('Y-m-d', $order['shipmentDate']).'　'.$order['creator'].'　'.date('Y-m-d H:i', $order['created']).'</li>';
				$i++;
			}
		}
		if($itemtr)$r='<ul data-role="listview" data-inset="true"><li data-role="list-divider">PO单</li>'.$itemtr.'</ul>';
		$cs=$this->kclass->DB->queryFirst("
			SELECT COUNT(orderid) AS count
			FROM `order`
			WHERE  `order`.type='".$b['type']."' AND `order`.killed=0
		");
		return array('list'=>$r,'item'=>$rs,'num'=>$cs['count']);
	}
	//
	// 
	function _sampleList(){
		$after5day = TIMENOW + 432000;
		$before5day = TIMENOW - 432000;
		$rs=array();
		$samples=$this->kclass->DB->query("
			SELECT `sample`.sampleid, `sample`.title, `sample`.entitle,`sample`.customerid,`sample`.typeid, `sample`.sampleno,`sample`.ifVerify,`sample`.ifApprove,`sample`.ifChooser,  `sample`.attachs,`sample`.deliveryDate, `sample`.cover, `sample`.images, `sample`.modified, `sample`.created, `sample`.killed,
				attr.title AS attr,
				shipmethod.title AS shipmethod,
				s.title AS sampletype,
				m.username AS modifier, c.username AS creator, d.username AS director, t.username AS tracker
			FROM `sample`
			LEFT JOIN `attr` ON (attr.attrid=sample.attrid)
			LEFT JOIN `shipmethod` ON (shipmethod.shipmethodid=sample.shipmethodid)
			LEFT JOIN `sampletype` AS s ON (s.typeid=`sample`.typeid)
			LEFT JOIN `user` AS m ON (m.userid=`sample`.modifier)
			LEFT JOIN `user` AS c ON (c.userid=`sample`.creator)
			LEFT JOIN `user` AS d ON (d.userid=`sample`.director)
			LEFT JOIN `user` AS t ON (t.userid=`sample`.tracker)
			WHERE  `sample`.killed=0
			ORDER BY `sample`.created DESC
			LIMIT 5
		");
		if($sum=$this->kclass->DB->numRows()){
			$i=1;
			while($sample = $this->kclass->DB->fetchArray($samples)){
				$status='';
				if($sample['ifVerify']==0){
					$status = '<span class="darkred">待审核</span>';
				}elseif($sample['ifVerify']==-1){
					$status = '<span class="red">审核未通过</span>';
				}elseif($sample['ifVerify']==1 AND $sample['ifApprove']==0){
					$status = '<span class="darkred">待审批</span>';
				}elseif($sample['ifVerify']==1 AND $sample['ifApprove']==-1){
					$status = '<span class="red">审批未通过</span>';
				}elseif($sample['ifVerify']==1 AND $sample['ifApprove']==1){
					$status = '<span class="darkred">待投产</span>';
				}
				if($sample['ifChooser']==1){
						$status='<span class="">已投产</span>';
					}
				$outbound=$this->kclass->DB->queryFirst("
					SELECT outboundid
					FROM outbound
					WHERE killed=0 AND ifComplete=2 AND sampleid='".$sample['sampleid']."'
				");
				if($outbound){
					$status='<span class="">已出库</span>';
				}
				$rs['module'][]='sample';
				$rs['mname'][]='样品单';
				$rs['mno'][]=$sample['sampleno'];
				$rs['mid'][]=$sample['sampleid'];
				$rs['creator'][]=$sample['creator'];
				$rs['created'][]=$sample['created'];
				$itemtr.='<tr class="'.$this->kclass->rotateLine().'">
					<td>'.$i.''.$this->kclass->iif(date('Y-m-d', $sample['created'])==date('Y-m-d', TIMENOW),'<span class="red tiny" style="vertical-align:top;"> new</span>','').'</td>
					<td><a class="tip" href="/s.php?module=sample&action=view&sampleid='.$sample['sampleid'].'" title="'.$sample['title'].' ('.$sample['sampletype'].')">'.$sample['sampleno'].'</a></td>
					<td>'.$status.'</td>
					<td class="darkred">交货日期：'.date('Y-m-d', $sample['deliveryDate']).'</td>
					<td>'.$sample['creator'].'</td>
					<td class="gray">'.date('Y-m-d H:i', $sample['created']).'</td>
				</tr>';
				$i++;
			}
		}
		if($itemtr)$r='<table class="small">'.$itemtr.'</table>';
		$cs=$this->kclass->DB->queryFirst("
			SELECT COUNT(sampleid) AS count
			FROM `sample`
			WHERE  `sample`.killed=0
		");
		return array('list'=>$r,'item'=>$rs,'num'=>$cs['count']);
	}
	//
	// 
	function _requirementList(){
		$after5day = TIMENOW + 432000;
		$before5day = TIMENOW - 432000;
		$rs=array();
		$requirements=$this->kclass->DB->query("
			SELECT `requirement`.*,
				mt.title AS type,
				msi.title AS situation,
				s.title AS status,
				u.username AS creator,
				us.username AS modifier,
				ul.username AS locker
			FROM `requirement`
			LEFT JOIN mftstatus AS s ON (s.statusid=`requirement`.statusid)
			LEFT JOIN `mftsituation` AS msi ON (msi.situationid=`requirement`.situationid)
			LEFT JOIN `mfttype` AS mt ON (mt.typeid=`requirement`.typeid)
			LEFT JOIN user AS u ON (u.userid=`requirement`.creator)
			LEFT JOIN user AS us ON (us.userid=`requirement`.modifier)
			LEFT JOIN user AS ul ON (ul.userid=`requirement`.locker)
			WHERE  `requirement`.killed=0
			ORDER BY `requirement`.created DESC
			LIMIT 5
		");
		if($sum=$this->kclass->DB->numRows()){
			$i=1;
			while($requirement = $this->kclass->DB->fetchArray($requirements)){
				$rs['module'][]='requirement';
				$rs['mname'][]='需求计划单';
				$rs['mno'][]=$requirement['requirementno'];
				$rs['mid'][]=$requirement['requirementid'];
				$rs['creator'][]=$requirement['creator'];
				$rs['created'][]=$requirement['created'];
				$title=$this->kclass->iif($requirement['statusid']>1,$requirement['locker'].' 锁定于 '.date('Y-m-d H:i:s',$requirement['locked']),'未锁定');;
				$itemtr.='<tr class="'.$this->kclass->rotateLine().'">
						<td>'.$i.''.$this->kclass->iif(date('Y-m-d', $requirement['created'])==date('Y-m-d', TIMENOW),'<span class="red tiny" style="vertical-align:top;"> new</span>','').'</td>
						<td><a class="tip" href="/s.php?module=requirement&action=view&requirementid='.$requirement['requirementid'].'" title="'.$title.'">'.$requirement['requirementno'].'</td>
						<td><span class="'.$this->kclass->iif($requirement['statusid']==1,'darkred','').'">'.$requirement['status'].'</span></td>
						<td><span class="'.$this->kclass->iif($requirement['situationid']==2,'darkred middle',$this->kclass->iif($requirement['situationid']==3,'red middle bold','')).'">'.$requirement['situation'].'</span></td>
						<td>'.$requirement['type'].' '.$this->kclass->iif($requirement['orderChange']==0,'',' <span class="small darkred"> ( 订单取消 ) </span>').'</td>
						<td>'. $requirement['planner'].'</td>
						<td  class="gray">'.date('Y-m-d H:i', $requirement['created']).'</td>
					</tr>';
				$i++;
			}
		}
		if($itemtr)$r='<table class="small">'.$itemtr.'</table>';
		$cs=$this->kclass->DB->queryFirst("
			SELECT COUNT(requirementid) AS count
			FROM `requirement`
			WHERE  `requirement`.killed=0
		");
		return array('list'=>$r,'item'=>$rs,'num'=>$cs['count']);
	}
	// 
	function _taskList(){
		$after5day = TIMENOW + 432000;
		$before5day = TIMENOW - 432000;
		$rs=array();
		$tasks=$this->kclass->DB->query("
			SELECT `task`.*,
				s.title AS status,
				u.username AS creator,
				us.username AS modifier,
				ul.username AS locker
			FROM `task`
			LEFT JOIN mftstatus AS s ON (s.statusid=`task`.statusid)
			LEFT JOIN user AS u ON (u.userid=`task`.creator)
			LEFT JOIN user AS us ON (us.userid=`task`.modifier)
			LEFT JOIN user AS ul ON (ul.userid=`task`.locker)
			WHERE  `task`.killed=0
			ORDER BY `task`.created DESC
			LIMIT 5
		");
		if($sum=$this->kclass->DB->numRows()){
			$i=1;
			while($task = $this->kclass->DB->fetchArray($tasks)){
				$rs['module'][]='task';
				$rs['mname'][]='任务单';
				$rs['mno'][]=$task['taskno'];
				$rs['mid'][]=$task['taskid'];
				$rs['creator'][]=$task['creator'];
				$rs['created'][]=$task['created'];
				$title=$this->kclass->iif($task['statusid']>1,$task['locker'].' 锁定于 '.date('Y-m-d H:i:s',$task['locked']),'未锁定');;
				$itemtr.='<tr class="'.$this->kclass->rotateLine().'">
						<td>'.$i.''.$this->kclass->iif(date('Y-m-d', $task['created'])==date('Y-m-d', TIMENOW),'<span class="red tiny" style="vertical-align:top;"> new</span>','').'</td>
						<td><a class="tip" href="/s.php?module=task&action=view&taskid='.$task['taskid'].'" title="'.$title.'">'.$task['taskno'].'</td>
						<td><span class="'.$this->kclass->iif($task['statusid']==1,'darkred','').'">'.$task['status'].'</span></td>
						<td>'. $task['tasker'].'</td>
						<td  class="gray">'.date('Y-m-d H:i', $task['created']).'</td>
					</tr>';
				$i++;
			}
		}
		if($itemtr)$r='<table class="small">'.$itemtr.'</table>';
		$cs=$this->kclass->DB->queryFirst("
			SELECT COUNT(taskid) AS count
			FROM `task`
			WHERE  `task`.killed=0
		");
		return array('list'=>$r,'item'=>$rs,'num'=>$cs['count']);
	}
	// 
	function _dispatchList(){
		$after5day = TIMENOW + 432000;
		$before5day = TIMENOW - 432000;
		$rs=array();
		$dispatchs=$this->kclass->DB->query("
			SELECT `dispatch`.dispatchid,`dispatch`.dispatchno,`dispatch`.statusid,`dispatch`.workcenterid,`dispatch`.applicant,
				`dispatch`.ifVerify,`dispatch`.verified,`dispatch`.verifyRemark,`dispatch`.remark,`dispatch`.ordering,`dispatch`.modified,`dispatch`.created,`dispatch`.killed,
				wc.title AS workcenter,
				ms.title AS status,
				u.username AS creator,
				us.username AS modifier,
				uv.username AS verifier
			FROM `dispatch`
			LEFT JOIN user AS u ON (u.userid=`dispatch`.creator)
			LEFT JOIN user AS us ON (us.userid=`dispatch`.modifier)
			LEFT JOIN user AS uv ON (uv.userid=`dispatch`.verifier)
			LEFT JOIN mftstatus AS ms ON (ms.statusid=`dispatch`.statusid)
			LEFT JOIN workcenter AS wc ON (wc.workcenterid=`dispatch`.workcenterid)
			WHERE  `dispatch`.killed=0
			ORDER BY `dispatch`.created DESC
			LIMIT 5
		");
		if($sum=$this->kclass->DB->numRows()){
			$i=1;
			while($dispatch = $this->kclass->DB->fetchArray($dispatchs)){
				$rs['module'][]='dispatch';
				$rs['mname'][]='派工单';
				$rs['mno'][]=$dispatch['dispatchno'];
				$rs['mid'][]=$dispatch['dispatchid'];
				$rs['creator'][]=$dispatch['creator'];
				$rs['created'][]=$dispatch['created'];
				$title=$this->kclass->iif($dispatch['ifVerify']==1,'由 '.$dispatch['verifier'].' 于 '.date('Y-n-d H:i:s',$dispatch['verified']).' 审核','待审核');
				$itemtr.='<tr class="'.$this->kclass->rotateLine().'">
						<td>'.$i.''.$this->kclass->iif(date('Y-m-d', $dispatch['created'])==date('Y-m-d', TIMENOW),'<span class="red tiny" style="vertical-align:top;"> new</span>','').'</td>
						<td><a class="tip" href="/s.php?module=dispatch&action=view&dispatchid='.$dispatch['dispatchid'].'" title="'.$title.'">'.$dispatch['dispatchno'].'</td>
						<td><span class="'.$this->kclass->iif($dispatch['statusid']==1,'darkred','').'">'.$dispatch['status'].'</span></td>
						<td>'.$dispatch['workcenter'].'</td>
						<td>'.$dispatch['applicant'].'</td>
						<td  class="gray">'.date('Y-m-d H:i', $dispatch['created']).'</td>
					</tr>';
				$i++;
			}
		}
		if($itemtr)$r='<table>'.$itemtr.'</table>';
		$cs=$this->kclass->DB->queryFirst("
			SELECT COUNT(dispatchid) AS count
			FROM `dispatch`
			WHERE  `dispatch`.killed=0
		");
		return array('list'=>$r,'item'=>$rs,'num'=>$cs['count']);
	}
	// 
	function _outboundList(){
		$after5day = TIMENOW + 432000;
		$before5day = TIMENOW - 432000;
		$rs=array();
		$outbounds=$this->kclass->DB->query("
			SELECT `outbound`.outboundid,`outbound`.outboundno,`outbound`.applicant,`outbound`.ifVerify,`outbound`.typeid,`outbound`.ifComplete,
					`outbound`.ifConfirm,`outbound`.created,`outbound`.killed,
				d.title AS department,
				u.username AS creator,
				us.username AS modifier
			FROM `outbound`
			LEFT JOIN `department` AS d ON (d.departmentid=`outbound`.departmentid)
			LEFT JOIN `user` AS u ON (u.userid=`outbound`.creator)
			LEFT JOIN `user` AS us ON (us.userid=`outbound`.modifier)
			WHERE  `outbound`.killed=0
			ORDER BY `outbound`.created DESC
			LIMIT 5
		");
		if($sum=$this->kclass->DB->numRows()){
			$i=1;
			while($outbound = $this->kclass->DB->fetchArray($outbounds)){
				$rs['module'][]='outbound';
				$rs['mname'][]='出库单';
				$rs['mno'][]=$outbound['outboundno'];
				$rs['mid'][]=$outbound['outboundid'];
				$rs['creator'][]=$outbound['creator'];
				$rs['created'][]=$outbound['created'];
				$outStatus='';
				if($outbound['ifComplete']==1){
					$outStatus='部分出库';
				}elseif($outbound['ifComplete']==2){
					$outStatus='出库完成';
				}else{
					$outStatus='等待出库';
				}
				foreach($this->outboundType as $key => $val){
					if($outbound['typeid']==$val['id']){
						$type=$val['title'];
					}
				}
				$verify=$this->kclass->verify($outbound['ifVerify']);
				$confirm=$this->kclass->confirm($outbound['ifConfirm']);
				$itemtr.='<tr class="'.$this->kclass->rotateLine().'">
						<td>'.$i.''.$this->kclass->iif(date('Y-m-d', $outbound['created'])==date('Y-m-d', TIMENOW),'<span class="red tiny" style="vertical-align:top;"> new</span>','').'</td>
						<td><a class="tip" title="'.$outStatus.' ('.$outbound['department'].')" href="/s.php?module=outbound&action=view&outboundid='.$outbound['outboundid'].'">'.$outbound['outboundno'].'</a></td>
						<td><span class="'.$this->kclass->iif($outbound['ifVerify']==0,'darkred','').'">'.$confirm.','.$verify.'</span></td>
						<td>'.$type.'</td>
						<td>'.$outbound['applicant'].'</td>
						<td  class="gray">'.date('Y-m-d H:i', $outbound['created']).'</td>
					</tr>';
				$i++;
			}
		}
		if($itemtr)$r='<table class="small">'.$itemtr.'</table>';
		$cs=$this->kclass->DB->queryFirst("
			SELECT COUNT(outboundid) AS count
			FROM `outbound`
			WHERE  `outbound`.killed=0
		");
		return array('list'=>$r,'item'=>$rs,'num'=>$cs['count']);
	}
	// 
	function _inboundList(){
		$after5day = TIMENOW + 432000;
		$before5day = TIMENOW - 432000;
		$rs=array();
		$inbounds=$this->kclass->DB->query("
			SELECT `inbound`.inboundid,`inbound`.inboundno,`inbound`.applicant,`inbound`.ifVerify,`inbound`.typeid,`inbound`.ifComplete,
					`inbound`.ifConfirm,`inbound`.created,`inbound`.killed,
				d.title AS department,
				u.username AS creator,
				us.username AS modifier
			FROM `inbound`
			LEFT JOIN `department` AS d ON (d.departmentid=`inbound`.departmentid)
			LEFT JOIN `user` AS u ON (u.userid=`inbound`.creator)
			LEFT JOIN `user` AS us ON (us.userid=`inbound`.modifier)
			WHERE  `inbound`.killed=0
			ORDER BY `inbound`.created DESC
			LIMIT 5
		");
		if($sum=$this->kclass->DB->numRows()){
			$i=1;
			while($inbound = $this->kclass->DB->fetchArray($inbounds)){
				$rs['module'][]='inbound';
				$rs['mname'][]='入库单';
				$rs['mno'][]=$inbound['inboundno'];
				$rs['mid'][]=$inbound['inboundid'];
				$rs['creator'][]=$inbound['creator'];
				$rs['created'][]=$inbound['created'];
				$inStatus='';
				$prein=$this->kclass->DB->queryFirst("
					SELECT SUM(quantity) AS count
					FROM preinbounditem
					WHERE killed=0 AND module='inbound' AND mid='".$inbound['inboundid']."'
					LIMIT 0,1
				");
				$in=$this->kclass->DB->queryFirst("
					SELECT SUM(quantity) AS count
					FROM inbounditem
					WHERE killed=0 AND inboundid='".$inbound['inboundid']."'
					LIMIT 0,1
				");
				if($prein['count']>$in['count'] AND $in['count']>0){
					$inStatus='部分入库';
					$inShow=1;
				}elseif($prein['count']==$in['count']){
					$inStatus='入库完成';
					$inShow=2;
				}elseif(!$in['count']){
					$inStatus='等待入库';
					$inShow=0;
				}
				foreach($this->inboundType as $key => $val){
					if($inbound['typeid']==$val['id']){
						$type=$val['title'];
					}
				}
				$verify=$this->kclass->verify($inbound['ifVerify']);
				$confirm=$this->kclass->confirm($inbound['ifConfirm']);
				$itemtr.='<tr class="'.$this->kclass->rotateLine().'">
						<td>'.$i.''.$this->kclass->iif(date('Y-m-d', $inbound['created'])==date('Y-m-d', TIMENOW),'<span class="red tiny" style="vertical-align:top;"> new</span>','').'</td>
						<td><a class="tip" title="'.$inStatus.' ('.$inbound['department'].')" href="/s.php?module=inbound&action=view&inboundid='.$inbound['inboundid'].'">'.$inbound['inboundno'].'</a></td>
						<td><span class="'.$this->kclass->iif($inbound['ifVerify']==0,'darkred','').'">'.$confirm.','.$verify.'</span></td>
						<td>'.$type.'</td>
						<td>'.$inbound['applicant'].'</td>
						<td  class="gray">'.date('Y-m-d H:i', $inbound['created']).'</td>
					</tr>';
				$i++;
			}
		}
		if($itemtr)$r='<table class="small">'.$itemtr.'</table>';
		$cs=$this->kclass->DB->queryFirst("
			SELECT COUNT(inboundid) AS count
			FROM `inbound`
			WHERE  `inbound`.killed=0
		");
		return array('list'=>$r,'item'=>$rs,'num'=>$cs['count']);
	}
	// 
	// 
	function _clickRank(){
		$beforehour = TIMENOW - 3600;
		$clicks=$this->kclass->DB->query("
			SELECT l.userid,COUNT(l.userid) AS totalclicks,
				u.username,u.lastActivity
			FROM `log` AS l
			LEFT JOIN user AS u ON (u.userid=l.userid)
			WHERE l.area='s' AND l.userid>0
			GROUP BY l.userid
			ORDER BY totalclicks DESC
			LIMIT 10
		");
		$i=1;
		if($this->kclass->DB->numRows()){
			while($click=$this->kclass->DB->fetchArray($clicks)){
				$online=$this->kclass->DB->queryFirst("
					SELECT agent,location
					FROM `sessions`
					WHERE userid='".$click['userid']."' AND runtime>".$beforehour."
					ORDER BY runtime DESC
					LIMIT 1
				");
				if($online)$browser=$this->_getBrowser(array('browser'=>$online['agent']));
				$itemtr.='<tr class="'.$this->kclass->rotateLine(). ' small">
					<td>'.$i.'</td>
					<td>'.$this->kclass->iif($online,'<a href="#" class="tip" title="在线：正在访问['.$online['location'].']　 ('.$browser.')">'.$click['username'].'</a>',$click['username']).'</td>
					<td><span class="gray">'.date('Y-m-d H:i', $click['lastActivity']).'</span></td>
					<td><span class="darkred">'.$click['totalclicks'].'</span> </td>
				</tr>';
				$i++;
			}
		}
		if($itemtr)$r='<table class="small"><tr><td>排行</td><td>用户名</td><td>最近操作</td><td>操作次数</td></tr>'.$itemtr.'</table>';
		return $r;
	}
	//
	// 
	function _online(){
		$beforehour = TIMENOW - 3600;
		$lines=$this->kclass->DB->query("
			SELECT *
			FROM `sessions`
			WHERE userid>0 AND runtime>".$beforehour."
			ORDER BY runtime DESC
			LIMIT 10
		");
		$i=1;
		if($this->kclass->DB->numRows()){
			while($line=$this->kclass->DB->fetchArray($lines)){
				$browser=$this->_getBrowser(array('browser'=>$line['agent']));
				$itemtr.='<tr class="'.$this->kclass->rotateLine(). ' small">
					<td>'.$i.'</td>
					<td><a href="#" class="tip" title="在线：正在访问['.$line['location'].']　 ('.$browser.')">'.$line['username'].'</a></td>
					<td><span class="gray">'.date('Y-m-d H:i', $line['runtime']).'</span></td>
				</tr>';
				$i++;
			}
		}
		if($itemtr)$r='<table class="small"><tr><td>ID</td><td>用户名</td><td>最近操作</td></tr>'.$itemtr.'</table>';
		$rs=$this->kclass->DB->queryFirst("
			SELECT COUNT(id) AS count
			FROM `sessions`
			WHERE userid>0 AND runtime>".$beforehour."
		");
		return array('item'=>$r,'num'=>$rs['count']);
	}
	// 
	function _getBrowser($b){
		$sys = $b['browser'];
		if(stripos($sys, "NetCaptor") > 0){
			 $exp[0] = "NetCaptor";
			 $exp[1] = "";
		}elseif(stripos($sys, "Firefox/") > 0){
			 preg_match("/Firefox\/([^;)]+)+/i", $sys, $f);
			 $exp[0] = "Mozilla Firefox";
			 $exp[1] = $f[1];
		}elseif(stripos($sys, "MAXTHON") > 0){
			 preg_match("/MAXTHON\s+([^;)]+)+/i", $sys, $f);
			 preg_match("/MSIE\s+([^;)]+)+/i", $sys, $ie);
			// $exp = $f[0]." (IE".$ie[1].")";
			 $exp[0] = $f[0]." (IE".$ie[1].")";
			 $exp[1] = $ie[1];
		}elseif(stripos($sys, "MSIE") > 0){
			 preg_match("/MSIE\s+([^;)]+)+/i", $sys, $ie);
			 //$exp = "Internet Explorer ".$ie[1];
			 $exp[0] = "Internet Explorer";
			 $exp[1] = $ie[1];
		}elseif(stripos($sys, "Netscape") > 0){
			 $exp[0] = "Netscape";
			 $exp[1] = "";
		}elseif(stripos($sys, "Opera") > 0){
			 $exp[0] = "Opera";
			 $exp[1] = "";
		}elseif(stripos($sys, "Chrome") > 0){
				$exp[0] = "Chrome";
				$exp[1] = "";
		}else{
			 $exp[0] = "未知浏览器";
			 $exp[1] = "";
		}
		$r=$exp[0].' '.$exp[1];
		return $r;
	}


}
?>