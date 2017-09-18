<?php
// 
class gskin{
	var $wrapper;
	var $wrapMsg;
	// 
	function dbody($d){
		if($d['class']==''){
			$s = 'dbody';
		}else{
			$s = $d['class'];
		}
		if($d['right'] != ''){
			$r = '<span class="bright">'.$d['right'].'</span>';
		}
		if($d['title'] != ''){
			$t = '<div class="bheader">'.$r.$d['title'].'</div>';
		}
		if($d['body'] != ''){
			$b = '<div class="bbody">'.$d['body'].'</div>';
		}
return <<<EOF
<div class="block">
	{$t}
	{$b}
</div>
EOF;
	}

	// 
	function fieldset($f){
		if($f['class'] != ''){
			$f['ext'] .= ' class="'.$f['class'].'"';
		}else{
			$f['ext'] .= ' class="fieldset"';
		}
		if($f['style'] != ''){
			$f['ext'] .= ' style="'.$f['style'].'"';
		}
return <<<EOF
<fieldset{$f['ext']}>
	<legend class="normal bold">{$f['title']}</legend>
	{$f['content']}
</fieldset>
EOF;
	}
}
?>