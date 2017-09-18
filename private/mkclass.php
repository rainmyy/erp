<?php
class fkclass extends kclass{
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
	function confirm($b){
		if($b==0){
			$r='待确认';
		}elseif($b==1){
			$r='已确认';
		}
		return $r;
	}
	//
}
?>