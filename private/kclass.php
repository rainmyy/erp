<?php
//
class kclass{
	var $get_magic_quotes = 0;
	var $br2nl = 0;
	var $tbline=0;
	var $member = array();
	var $vars = array();
	var $session = array();
	var $DB;
	var $skin;
	var $redirect;
	var $upload;
	var $page = array();

	// DB Connection
	function initDB(){
		$this->DB=new DBSql;
		$this->DB->connect(array('host'=>$this->vars['host'], 'db'=>$this->vars['db'], 'user'=>$this->vars['dbuser'], 'password'=>$this->vars['dbpassword']));
		$this->DB->query("SET NAMES 'utf8';");
	}

	/* Makes incoming info "safe"
	* Parse _GET _POST data
	* Clean up and unHTML
	* @return	void
	*/
	function parseIncoming(){
		# THIS NEEDS TO BE HERE!
		$this->get_magic_quotes = @get_magic_quotes_gpc();

 		if(is_array($_GET)){
			while(list($k, $v) = each($_GET)){
				if(is_array($_GET[$k])){
					while(list($k2, $v2) = each($_GET[$k])){
						$this->input[$this->parseCleanKey($k)][$this->parseCleanKey($k2)] = $this->parseCleanValue($v2);
					}
				}else{
					$this->input[$this->parseCleanKey($k)] = $this->parseCleanValue($v);
				}
			}
		}
		// Overwrite GET data with post data
		if(is_array($_POST)){
			while(list($k, $v) = each($_POST)){
				if(is_array($_POST[$k])){
					while(list($k2, $v2) = each($_POST[$k])){
						$this->input[$this->parseCleanKey($k)][$this->parseCleanKey($k2)] = $this->parseCleanValue($v2);
					}
				}else{
					$this->input[$this->parseCleanKey($k)] = $this->parseCleanValue($v);
				}
			}
		}

		$this->input['requestMethod'] = strtolower($_SERVER['REQUEST_METHOD']);
//		echo '<pre>'.print_r($this->input, 1).'</pre>';exit;
	}

	/* HTTP redirect with out message screen
	* @param	string	URL to load
	* @return	void
	*/
	function boinkIt($url){
		// Ensure &amp;s are taken care of
		if($url == ''){
			$url = '/';
		}else{
			$url = str_replace( "&amp;", "&", $url );
		}
		if($this->vars['redirect'] == 'location'){
			@header("Location: ".$url);
		}elseif($this->vars['redirect'] == 'refresh'){
			@header("Refresh: 0;url=".$url);
		}elseif($this->vars['header_redirect'] == 'html'){
			echo("<html><head><meta http-equiv='refresh' content='0; url=$url'></head><body></body></html>");
		}
		exit();
	}

	/* Fatal Error
	* @param string Message & Help
	* @return void
	*/
	function fatalError($m='', $h=''){
		echo $m.'<br><br>'.$h;
		exit;
	}

	/* out message screen
	* @param	array	title,text,url,sec,hl
	* @return	void
	*/
	function messager($m){
		if($m['immediacy']){
			$this->boinkIt($m['url']);
		}else {
			if($m['goon']){
				if($m['hl'] == '')$m['hl'] = 'hlRed';
				return array($m['title'], $m['text'], $m['hl']);
			}else {
				$w = str_replace("<%TITLE%>", $m['title'], $this->skin->wrapMsg);
				$w = str_replace("<%TEXT%>", $m['text'], $w);
				$w = str_replace("<%URL%>", $m['url'], $w);
				$w = str_replace("<%SEC%>", $m['sec'], $w);
				$this->export($w);
				exit;
			}
		}
	}
	/* Key Cleaner - ensures no funny business with form elements
	* Clean _GET _POST key
	* @param	string	Key name
	* @return	string	Cleaned key name
	*/
	function parseCleanKey($k){
		if($k<0){
			return "";
		}
		$k = htmlspecialchars(urldecode($k));
		$k = preg_replace("/\.\./"           , ""  , $k);
		$k = preg_replace("/\_\_(.+?)\_\_/"  , ""  , $k);
		$k = preg_replace("/^([\w\.\-\_]+)$/", "$1", $k);
		return $k;
	}

	/* Clean evil tags
	* Clean possible javascipt codes
	* @param	string	Input
	* @return	string	Cleaned Input
	*/
	function cleanEvilTags($t){
		$t = preg_replace("/javascript/i" , "j&#097;v&#097;script", $t);
		$t = preg_replace("/alert/i"      , "&#097;lert"          , $t);
		$t = preg_replace("/about:/i"     , "&#097;bout:"         , $t);
		$t = preg_replace("/onmouseover/i", "&#111;nmouseover"    , $t);
		$t = preg_replace("/onclick/i"    , "&#111;nclick"        , $t);
		$t = preg_replace("/onload/i"     , "&#111;nload"         , $t);
		$t = preg_replace("/onsubmit/i"   , "&#111;nsubmit"       , $t);
		$t = preg_replace("/<body/i"      , "&lt;body"            , $t);
		$t = preg_replace("/<html/i"      , "&lt;html"            , $t);
		$t = preg_replace("/document\./i" , "&#100;ocument."      , $t);
		return $t;
	}

	/* Clean value
	* UnHTML and stripslashes _GET _POST value
	* @param	string	Input
	* @return	string	Cleaned Input
	*/
	function parseCleanValue($v){
		if($v == ""){
			return "";
		}
		$v = str_replace("&#032;", " ", $v);
		$v = str_replace("&"            , "&amp;"         , $v);
		$v = str_replace("<!--"         , "&#60;&#33;--"  , $v);
		$v = str_replace("-->"          , "--&#62;"       , $v);
		$v = preg_replace("/<script/i"  , "&#60;script"   , $v);
		$v = str_replace(">"            , "&gt;"          , $v);
		$v = str_replace("<"            , "&lt;"          , $v);
		$v = str_replace("\""           , "&quot;"        , $v);
		$v = preg_replace("/\n/"        , "<br />"        , $v); // Convert literal newlines
		$v = preg_replace("/\\\$/"      , "&#036;"        , $v);
		$v = preg_replace("/\r/"        , ""              , $v); // Remove literal carriage returns
		$v = str_replace("!"            , "&#33;"         , $v);
		$v = str_replace("'"            , "&#39;"         , $v); // IMPORTANT: It helps to increase sql query safety.
		// Ensure unicode chars are OK
		$v = preg_replace("/&amp;#([0-9]+);/s", "&#\\1;", $v);
		// Strip slashes if not already done so.
		if($this->get_magic_quotes){
			$v = stripslashes($v);
		}
		// Swap user entered backslashes
		$v = preg_replace("/\\\(?!&amp;#|\?#)/", "&#092;", $v);
		return $v;
	}

	/* parse html
	// Converts the doHTML tag
	*/
	function parseConvertValue($t){
		if($this->br2nl){
			$t = str_replace( "<br>" ,"\n" ,$t);
			$t = str_replace( "<br />" ,"\n" ,$t);
			$t = str_replace( "&lt;br /&gt;" ,"\n" ,$t);
		}else{
			$t = str_replace( "\n"   , "<br />", $t );
		}
		$t = str_replace( "&#39;"   , "'", $t );
		$t = str_replace( "&#33;"   , "!", $t );
		$t = str_replace( "&#036;"   , "$", $t );
		$t = str_replace( "&#124;"  , "|", $t );
		$t = str_replace( "&amp;"   , "&", $t );
		$t = str_replace( "&gt;"    , ">", $t );
		$t = str_replace( "&lt;"    , "<", $t );
		$t = str_replace( "&quot;"  , '"', $t );
		return $t;
	}

	/* convert Half shape blank to Full shape blank
	// @param string $s
	// @return string converted
	*/
	function convertBlank($s){
		$l = explode("\n", $s);
		for($i = 0; $i < count($l); $i++){
			$sub = substr($l[$i], 0, 2);
			if($sub == '  '){
				$l[$i] = '　'.substr($l[$i], 2);
			}
		}
		return implode("\n", $l);
	}

	/* Sets a cookie, abstract layer allows us to do some checking, etc
  * Abstract layer allows us to do some checking, etc
	* @param	array	name,value,sticky,expires,path,domain
	* @param	string	Cookie value
	* @param	integer	Is sticky flag
	* @return	void
	*/
	function mySetcookie($c){
		if($c['sticky'] != 1 AND $c['expires'] <= 0){
			$c['expires'] = TIMENOW + $this->vars['sessionLifetime']; //follow session option
		}else{
			if($c['sticky'] == 1){
				$c['expires'] = TIMENOW + 31536000; //365 days
			}elseif($c['expires'] > 0){
				$c['expires'] = TIMENOW + $c['expires'];
			}
		}
		if($c['path'] == ''){
			$c['path'] = '/';
		}
		if($c['domain'] == ''){
			$c['domain'] == $_SERVER['HTTP_HOST'];
		}
		@setcookie($c['name'], $c['value'], $c['expires'], $c['path'], $c['domain']);
	}

	/* Cookies, Get a cookie
	* @param	string	Cookie name
	* @return	mixed
	*/
	function myGetcookie($name){
		if(isset($_COOKIE[$name])){
			return urldecode($_COOKIE[$name]);
		}else{
			return;
		}
	}

	/* krand - rand number
	*param int min
	*param int max
	*param boolean seed 1, 0
	*return int
	*/
	function krand($min, $max, $seed = 1){
		if(!defined('RAND_SEEDED')){
			if($seed == 1){
				$seed = (double) microtime() * 1000000;
			}
			mt_srand($seed);
			define('RAND_SEEDED', true);
		}
		return mt_rand($min, $max);
	}

	/* export - dumpage
	*param string $w: wrapper
	*return void
	*/
	function export($w=''){
		if($this->vars['log'] == 1) $this->ilog();// log

		if($w == ''){
			$w = $this->skin->wrapper;
			$mtime = explode (' ', microtime());// Run Time
			//======= system =======
			if($this->vars['area'] == 's'){
				$this->page['onload'] = 'dc.menu(\'menu\');dc.notification();' . $this->page['onload'];
				$w = str_replace("<%NAVTITLE%>", $this->page['navtitle'], $w);
				$this->page['title'] = $this->vars['titleERP'].' - ' . $this->page['title'];
				$w = str_replace("<%NAV%>", $this->user['userid']>0?$this->menu():'', $w);
			//======= admin =======
			}elseif($this->vars['area'] == 'a'){
				$this->page['title'] = $this->vars['titleERP'].' - ' . $this->page['title'];
				$w = str_replace("<%LEFT%>", $this->skin->navSide(), $w);
				$w = str_replace("<%NAV%>", $this->skin->nav(ucfirst($this->user['username'])), $w);
			//======= Web =======
			}elseif($this->vars['area'] == 'w'){

			}

			$w=str_replace("<%ONLOAD%>", $this->iif($this->page['onload']=='', '', ' onload="'.$this->page['onload'].'"'), $w);
			$w = str_replace("<%HEADER%>", $this->page['header'], $w);
			$w = str_replace("<%TITLE%>", $this->page['title'], $w);
			$w = str_replace("<%LOGO%>", $this->skin->logo(), $w);
			$w = str_replace("<%MAIN%>", $this->page['main'], $w);
			$w = str_replace("<%RUNTIME%>", number_format(($mtime[1] + $mtime[0] - (TIMENOW + MICRO_TIME))*1000, 2).'ms | ', $w);
			$w = str_replace("<%COPYRIGHT%>", COPYRIGHT, $w);
		}
		header("HTTP/1.0 200 OK");
		header("HTTP/1.1 200 OK");
		header('Content-type:text/html;charset=utf-8');
		print $w;
	}

	/* log record
	*param 
	*return null
	*/
	function ilog(){
		if($this->input['action'] != 'mylog'){
			if($this->vars['logSavePage'] == 1) $logSavePage = addslashes(serialize($this->page));
			// user log
			$this->DB->query("
				INSERT INTO `log` (`userid` , `title` , `area` , `module` , `action` , `id` , `ip` , `kinput` , `page` , `dateline`)
				VALUES (
				'".$this->user['userid']."', '".$this->page['title']."', '".$this->vars['area']."', '".$this->input['module']."', '".$this->input['action']."', '".$this->iif($this->input[$this->input['module'].'id'] > 0, $this->input[$this->input['module'].'id'], 0)."', '".IP."', '".serialize($this->input)."', '".$logSavePage."', '".TIMENOW."'
				)
			");
		}
	}

	/* iif
	*param boolean e
	*param string t
	*param string f
	*return string
	*/
	function iif($e, $t, $f){
		if($e){
			return $t;
		}else{
			return $f;
		}
	}

	/* convert text
	*param string buffer
	*return string
	*/
	function convertText($b){
		$b = htmlspecialchars($b);
		$b = str_replace("\011", ' &nbsp;&nbsp;&nbsp;', str_replace('  ', ' &nbsp;', $b));
		$b = ereg_replace("((\015\012)|(\015)|(\012))", '<br />', $b);
		return $b;
	}
	// 
	function highlight($t, $s){
		preg_match_all('/'.$s.'/i',$t,$m);
		$tm = array();
		foreach($m[0] as $v){
			$tm[$v] = '<span class="red">'.$v.'</span>';
		}
		return strtr($t,$tm);
	}
	//
	function getDatestamp($d){
		if(TIMENOW < $d){
			return;
		}
		//year offset
		$y = date('Y', TIMENOW) - date('Y', $d);
		if($y == 0){
			//day offset
			$o = date('z', TIMENOW) - date('z', $d);
			if($o == 0){
				return '今天';
			}elseif($o == 1){
				return '昨天';
			}elseif($o == 2){
				return '前天';
			}elseif($o < 7){
				return $o . ' 天前';
			}elseif($o < 31){
				return (date('W', TIMENOW) - date('W', $d)) . ' 周前';
			}elseif($o < 365){
				return (date('n', TIMENOW) - date('n', $d)) . ' 月前';
			}
		}else{
			return $y . ' 年前';
		}
	}

	//
	function mailer($m){
		require "private/mailer.php";
		$mail = new mailer;
		$mail->From = $this->iif($m['from'] != '', $m['from'], $this->vars['smtpfrom']);
		$mail->FromName = $this->iif($m['from_name'] != '', $m['from_name'], $this->vars['smtpfromname']);
		$mail->Host = $this->vars['smtphost'];
		$mail->Helo = $this->vars['smtphelo'];
		$mail->SMTPAuth = $this->vars['smtpauth'];
		$mail->Username = $this->vars['smtpusername'];
		$mail->Password = $this->vars['smtppassword'];

		$mail->IsHTML(true);
		$mail->AddAddress($m['address'], $m['to']);
		if($m['cc'] != ''){
			$mail->AddCC($m['cc']);
		}
		$mail->Subject = $m['subject'];
		$mail->Body = $this->convertText($m['message']);
		if($mail->Send()){
			return true;
		}else {
			return false;
		}
	}

	/* Convert bytes into kb, mb, etc (Give it a byte to eat and it'll return nice stuff!)
	* @param	integer	size in bytes
	* @return	string	Human size
	* @since	2.0
	*/
	function sizeFormat($b = 0){
		if($b < 1024){
			return $b . 'bytes';
		}elseif($b < 1048576){
			return round($b / 1024 * 100 ) / 100 . 'KB';
		}elseif($b < 130023424){
			return round($b / 1048576 * 100 ) / 100 . 'MB';
		}elseif($b < 133143986176){
			return round($b / 130023424 * 100) / 100 . 'GB';
		}else{
			return round($b / 133143986176 * 100) / 100 . 'TB';
		}
	}

	//
	function folderSize($d){
		$dh = opendir($d);
		$size = 0;
		while($file = readdir($dh)){
			if($file != '.' and $file != '..'){
				$path = $dir."/".$file;
				if(@is_dir($path)){
					$size += $this->folderSize($path);
				} else {
					$size += filesize($path);
				}
			}
		}
		@closedir($dh);
		return $size;
	}
	// $v: string, $s: start, $l: length
	function substrGB($string, $start = 0,$length = 30){
		if (strlen($string) > $length){ 
			for($i=0; $i < $length; $i++){
				if (ord($string[$i]) > 128){
					$i++;
				}
			}
			$string = substr($string,$start,$i); 
		} 
		return $string; 
	}
	//
	function invalid($error = ''){
		$this->messager(array(
				'title' => '无效的请求',
				'text' => '这是一个无效的请求。请返回列表重试，或者联系我们的网站管理人员进行帮助。<br /><br />'.$error,
				'url' => '/'.$this->vars['index_file'],
				'sec' => 5
			)
		);
	}
	// 
	function rotate(){
		if($this->tbline%2 == 0){
			$this->tbline++;
			return true;
		}else{
			$this->tbline++;
			return false;
		}
	}
	// 
	function rotateLine(){
		if($this->rotate()){
			return 'even';
		}else{
			return 'odd';
		}
	}

	// 
	function pagination($p){
		$counter = $this->kclass->DB_site->query_first("SELECT COUNT(".$p['table']."id) AS total FROM ".$p['table']."	WHERE ".$p['condition']);
		$pages = ceil($counter['total']/$p['perpage']);
		if (!$current) {$current = $p['page'];}
		if ($current>$pages) {$current = $pages;}
		if ($current<1) {$current = 1;}

		return (($current-1)*$p['page']).','.$p['page'];
	}
}
?>