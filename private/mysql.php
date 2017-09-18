<?php
class DBSql{
	var $linkID = 0;
	var $queryID = 0;
	var $record = array();

	function connect($c){
		if($this->linkID == 0){
			$this->linkID=@mysql_pconnect($c['host'],$c['user'],$c['password']);
			if(!$this->linkID){
				$this->halt('数据库连接失败。');
			}
			if($c['db']!=""){
				if(!mysql_select_db($c['db'], $this->linkID)){
					$this->halt('无法连接数据库('.$c['db'].')，请联系管理员。');
				}
			}
		}
	}

	function getError(){
		return mysql_error();
	}

	function getErrorNo(){
		return mysql_errno();
	}

	function query($s){
		$this->queryID = mysql_query($s, $this->linkID);
		if(!$this->queryID){
			$this->halt('无效SQL语句：'.$s);
		}
		return $this->queryID;
	}

	function fetchArray($id=-1, $s=""){
		if($id!=-1){
			$this->queryID=$id;
		}
		if(isset($this->queryID)){
			return mysql_fetch_assoc($this->queryID);
		}else{
			if(!empty($s)){
				$this->halt('无效查询号：('.$this->queryID.') 在这个SQL查询：'.$s);
			}else{
				$this->halt('指定了无效查询号：'.$this->queryID);
			}
		}
	}

	function freeResult($id=-1){
		if($id!=-1){
			$this->queryID=$id;
		}
		return @mysql_free_result($this->queryID);
	}

	function queryFirst($s){
		$queryID = $this->query($s);
		$ra = $this->fetchArray($queryID, $s);
		$this->freeResult($queryID);
		return $ra;
	}

	function dataSeek($p, $id=-1){
		if($id!=-1){
			$this->queryID=$id;
		}
		return mysql_data_seek($this->queryID, $p);
	}

	function numRows($id=-1){
		if($id!=-1){
			$this->queryID=$id;
		}
		return mysql_num_rows($this->queryID);
	}

	function nextRecord(){
		if(!$this->queryID){
			$this->halt('nextRecord() called with no query pending.');
			return 0;
		}
		$this->record = @mysql_fetch_assoc($this->queryID);
		$this->freeResult();
		return $this->record;
	}
	function numFields($queryID=-1){
		if($queryID!=-1){
			$this->queryID=$queryID;
		}
		return mysql_num_fields($this->queryID);
	}

	function queryUnbuffered($s){
		return $this->query($s, 'mysql_unbuffered_query');
	}

	function shutdownQuery($s, $arraykey = 0){
		if(NOSHUTDOWNFUNC){
			return $this->query($s);
		}elseif($arraykey){
			$shutdownqueries[$arraykey] = $s;
		}else{
			$shutdownqueries[] = $s;
		}
	}

	function insertID(){
		return mysql_insert_id($this->linkID);
	}

	function halt($msg){
		$error = mysql_error();
		$errno = mysql_errno();
		$dt = date('Y-m-d H:i:s');
		$s = '<html><head><link rel="stylesheet" type="text/css" href="/public/msg.css"/><title>NWI DB Errors</title></head><body>';
		$t .= '<div id="msg" style="width:774px"><div id="title">数据库错误</div><div id="body">
'.$msg.'
<pre>
错误信息：<b>'.$error.'</b>
错误编号：<b>'.$errno.'</b>
发生时间：<b>'.$dt.'</b>
当前地址：<b>'.URI.'</b>
前页地址：<b>'.REFERER.'</b>
</pre></div></div>
';
		//if(IP == '127.0.0.1'){
			$s .= $t;
		/*}else{
			$s .= '<div id="msg"><div id="title">抱歉，遇到了轻微的错误</div><div id="body" style="font-size:12px">很抱歉，我们的数据库大概发生了轻微的错误，请稍候<a href="javascript:refresh();">刷新</a>尝试。<br/><br/>同时本系统已经将此错误通过邮件发送给了我们的技术人员， 如果问题依然, 您也可以直接联系我们技术部门人员。<br/><br/>我们为本次错误深表歉意，感谢您的支持！'.$dt.'</div><div id="footer">您也可以<a href="javascript: history.back();">点击这里</a>返回之前的页面。</div></div>';
		}*/
		header("HTTP/1.0 200 OK");
		header("HTTP/1.1 200 OK");
		header('Content-type:text/html;charset=utf-8');
		print $s.'</body></html>';
		if(false){
			@mail('albert@n-wisdom.com','NWIERP数据库出现错误！', $t);
		}
		exit;
	}
}
?>