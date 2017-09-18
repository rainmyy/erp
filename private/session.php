<?php
class session{
	# Global
	var $kclass;
	# IP, AGENT, TIMENOW
	var $session = array();
	var $member = array();

	// Authorise
	function authorise(){
		// INIT
		$this->user = array('userid' => 0, 'username' => 'Guest', 'groupid' => $this->kclass->vars['groupidGuest']);

		// Before we go any lets check the load settings..
		if($this->kclass->vars['loadLimit'] > 0){
			if(file_exists('/proc/loadavg')){
				if($fh = @fopen('/proc/loadavg', 'r')){
					$data = @fread($fh, 6);
					@fclose($fh);
					$load_avg = explode(" ", $data);
					$this->kclass->serverLoad = trim($load_avg[0]);

					if($this->kclass->serverLoad > $this->kclass->vars['loadLimit']){
						$this->kclass->messager(array(
								'title' => 'Server too busy',
								'text' => 'Sorry, the server is too busy to handle your request, please try again in a moment.',
								'url' => '/',
								'final' => 1,
								'sec' => 5
						));
					}
				}
			}else{
				if($serverstats = @exec("uptime")){
					preg_match("/(?:averages)?\: ([0-9\.]+),[\s]+([0-9\.]+),[\s]+([0-9\.]+)/", $serverstats, $load);
					$this->kclass->serverLoad = $load[1];
					if($this->kclass->serverLoad > $this->kclass->vars['loadLimit']){
						$this->kclass->messager(array(
								'title' => 'Server too busy',
								'text' => 'Sorry, the server is too busy to handle your request, please try again in a moment.',
								'url' => '/',
								'final' => 1,
								'sec' => 5
						));
	    		}
				}
			}
		}

		// Are they banned?
		if(is_array($this->kclass->vars['banIP']) and count($this->kclass->vars['banIP'])){
			foreach ($this->kclass->vars['banIP'] as $ip){
				$ip = str_replace('\*', '.*', preg_quote($ip, "/"));

				if(preg_match("/^$ip$/", IP)){
					$this->kclass->messager(array(
							'title' => 'You are banned',
							'text' => 'Sorry, you are not permitted to use this website.',
							'url' => '/',
							'final' => 1,
							'sec' => 5
					));
				}
			}
		}
		// Continue!
		if($this->kclass->input['s']!=''){
			$this->session['id'] = $this->kclass->input['s'];
			$this->getSession();
			$this->session['type'] = 'url';
		}elseif($this->kclass->myGetcookie('sessionID')!=''){
			$this->session['id'] = $this->kclass->myGetcookie('sessionID');
			$this->getSession();
			$this->session['type'] = 'cookie';
		}else{
			$this->session['id'] = 0;
		}
		// Do we have a valid session ID?
		if($this->session['id']){
			// We've checked the IP addy and browser, so we can assume that this is a valid session.
			if($this->session['userid'] > 0){
				// It's a member session, so load the member.
				$this->loadUser();
				// Did we get a member?
				if($this->user['userid'] > 0){
					$this->updateUserSession();
				}else{
					$this->unloadUser();
					$this->updateGuestSession();
				}
			}else{
				$this->updateGuestSession();
			}
		}else{
			$this->createGuestSession();
		}

		// Knock out Google Web Accelerator
		if(strstr(strtolower(@$_SERVER['HTTP_X_MOZ']), 'prefetch') AND $this->user['userid']){
			@header('HTTP/1.1 403 Forbidden');
			print "Prefetching or precaching is not allowed";
			exit();
		}

		// Delete expire session (more than 3 hours 5*60*60)
		if($this->kclass->krand(0, 100) == 50){
			$this->kclass->DB->query("
				DELETE FROM sessions
				WHERE runtime < ".(TIMENOW - 18000)."
			");
		}

		// Set a session ID cookie
		$this->kclass->mySetcookie(array('name'=>'sessionID', 'value'=>$this->session['id']));
		return $this->user;
	}

	// Attempt to load a member
	function loadUser(){
	 	if($this->session['userid'] > 0){
			$this->user = $this->kclass->DB->queryFirst("
				SELECT u.userid, u.groupid, u.username, u.password, u.email,u.telephone,u.mobilephone,u.fax,u.address,u.postalcode,u.lastVisit, u.lastActivity, u.counter, u.killed,
					g.zone, g.title AS `group`
				FROM `user` AS u
				LEFT JOIN `group` AS g ON (g.groupid=u.groupid)
				WHERE userid='".$this->session['userid']."'
				LIMIT 0,1
			");
			switch($this->user['zone']){
				case 'member':
					$sql = "
						SELECT m.memberid, m.positionid, m.realname, m.inherit, m.purview, m.joindate,
							p.title AS position, p.purviews,
							d.departmentid,d.departmentno,d.title AS department
						FROM `member` AS m
						LEFT JOIN `position` AS p ON (p.positionid=m.positionid)
						LEFT JOIN `department` AS d ON (d.departmentid=m.departmentid)
						WHERE m.userid='".$this->user['userid']."'
						LIMIT 0,1
					";
				break;
				case 'customer':
					$sql = "
						SELECT c.customerid, c.rankid, c.title, c.cntitle, c.abbr, c.regionid, c.cover,
							r.entitle AS rank,
							z.encountry AS country, z.abbr AS countryAbbr, z.enstate AS state, z.encity AS city
						FROM `customer` AS c
						LEFT JOIN rank AS r ON (r.rankid=c.rankid)
						LEFT JOIN region AS z ON (z.regionid=c.regionid)
						WHERE c.userid='".$this->user['userid']."'
					";
				break;
				case 'supplier':
					$sql = "
						SELECT s.supplierid, s.rankid, s.title, s.cntitle, s.abbr, s.regionid, s.cover,
							r.entitle AS rank,
							z.encountry AS country, z.abbr AS countryAbbr, z.enstate AS state, z.encity AS city
						FROM `supplier` AS c
						LEFT JOIN rank AS r ON (r.rankid=s.rankid)
						LEFT JOIN region AS z ON (z.regionid=s.regionid)
						WHERE s.userid='".$this->user['userid']."'
					";
				break;
			}
			if($sql!=''){
				$m=$this->kclass->DB->queryFirst($sql);
				if($m){
					$this->user=array_merge($this->user, $m);
				}
			}
		}else{
			// Unless they have a member id, log 'em in as a guest
			$this->unloadUser();
		}
	}

	// Remove the users cookies
	function unloadUser(){
		$this->user = array('userid' => 0, 'username' => 'Guest', 'groupid' => $this->user['groupid']);
	}

	// Updates a current session.
	function updateUserSession(){
		//-----------------------------------------
		// Make sure we have a session id.
		//-----------------------------------------
		if(!$this->session['id']){
			$this->createUserSession();
			return;
		}

		if($this->user['userid'] <= 0 OR $this->session['runtime'] < (TIMENOW - $this->kclass->vars['sessionLifetime'])){
			$this->unloadUser();
			$this->updateGuestSession();
			return;
		}
		$this->kclass->DB->query("
			UPDATE sessions
			SET username='".$this->user['username']."',
				userid='".intval($this->user['userid'])."',
				signtype='".$this->session['type']."',
				runtime = '".TIMENOW."',
				groupid = '".$this->user['groupid']."',
				title = '".$this->user['title']."',
				agent = '".addslashes(AGENT)."',
				location = '".URI."'
			WHERE id='".$this->session['id']."'
		");
		// Synchronise the last visit and activity times if we have some in the member profile
		// Update their last visit times, etc.
		// If there hasn't been a cookie update in 2 hours, we assume that they've gone and come back
		if($this->user['lastVisit'] < (TIMENOW - 7200)){
			// No last visit set, do so now!
			$this->kclass->DB->query("
				UPDATE user
				SET lastVisit=".TIMENOW.", lastActivity=".TIMENOW.", counter = counter+1
				WHERE userid='".$this->user['userid']."'
			");
		}elseif((TIMENOW - $this->user['lastActivity']) > 300){
			// If the last click was longer than 5 mins ago and this is a member Update their profile.
			$this->kclass->DB->query("
				UPDATE user
				SET lastActivity=".TIMENOW."
				WHERE userid='".$this->user['userid']."'
			");
		}
	}

	// Update guest session
	function updateGuestSession(){
		// Make sure we have a session id.
		if(!$this->session['id']){
			$this->createGuestSession();
			return;
		}
		// Update session
		$this->kclass->DB->query("
			UPDATE sessions
			SET userid = '0',
				username = 'Guest',
				groupid = '".$this->user['groupid']."',
				signtype='".$this->session['type']."',
				title = 'Guest',
				runtime = '".TIMENOW."',
				agent = '".addslashes(AGENT)."',
				location = '".URI."'
			WHERE id='".$this->session['id']."'
		");
	}

	// Get a session based on the current session ID
	function getSession(){
		// INIT
		$this->session['id'] = preg_replace("/([^a-zA-Z0-9])/", "", $this->session['id']);
		$condition = "id='".$this->session['id']."'";

		if($this->session['id']){
			if($this->kclass->vars['sessionMatchBrowser'] == 1){
				$condition .= " AND browser='".addslashes(AGENT)."'";
			}

			if($this->kclass->vars['sessionMatchIP'] == 1){
				$condition .= " AND ipaddress='".IP."'";
			}

			$results = $this->kclass->DB->query("
				SELECT *
				FROM sessions
				WHERE ".$condition."
			");
			if($this->kclass->DB->numRows() != 1){
				// Either there is no session, or we have more than one session..
				$this->session['id'] = 0;
				$this->session['userid'] = 0;
				return;
			}else{
				$result = $this->kclass->DB->fetchArray($results);
				if($result['id'] == ""){
					$this->session['id'] = 0;
					unset($result);
					$this->kclass->DB->query("
						DELETE FROM sessions
						WHERE id = '".$sessionID."'
					");
					return;
				}else{
					$this->session = $result;
					unset($result);
					return;
				}
			}
		}
	}

	// Creates a member session.
	function createUserSession(){
		if($this->user['userid']){
			// Remove the defunct sessions
			$this->kclass->DB->query("
				DELETE FROM sessions
				WHERE userid='".$this->user['userid']."'
			");
			$this->session['id']  = md5(uniqid(TIMENOW.' '.MICRO_TIME));

			// Insert the new session
			$this->kclass->DB->query("
				INSERT INTO `sessions` ( `id` , `username` , `userid` , `ipaddress` , `agent` , `runtime` , `signtype` , `location` , `groupid` , `title` )
				VALUES (
				'".$this->session['id']."', '".$this->user['username']."' , '".$this->user['userid']."', '".IP."' , '".addslashes(AGENT)."', '".TIMENOW."' , '".$this->user['username']."' , '".URI."' , '".$this->user['groupid']."' , '".$this->user['title']."'
				)
			");
		}else{
			$this->createGuestSession();
		}
	}

	// Create guest session
	function createGuestSession(){
		// INIT
		$this->session['id']  = md5(uniqid(TIMENOW.' '.MICRO_TIME));
		// Insert the new session
		$this->kclass->DB->query("
			INSERT INTO `sessions` ( `id` , `username` , `userid` , `ipaddress` , `agent` , `runtime` , `signtype` , `location` , `groupid` , `title` )
			VALUES (
			'".$this->session['id']."', 'Guest' , '0', '".IP."' , '".addslashes(AGENT)."', '".TIMENOW."' , '".$this->session['type']."' , '".URI."' , '".$this->user['groupid']."' , 'Guest'
			)
		");
	}
}
?>