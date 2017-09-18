<?php
/* defines */
define ('PIE_NONE',0);
define ('PIE_LEGEND_PCENT',1);
define ('PIE_LEGEND_VALUE',2);
define ('PIE_CHART_VALUE',4);
define ('PIE_CHART_PCENT',8);
define ('LINE_MARK_NONE',0);
define ('LINE_MARK_PLUS',1);
define ('LINE_MARK_X',2);
define ('LINE_MARK_CIRCLE',3);
define ('LINE_MARK_SQUARE',4);
define ('LINE_MARK_DIAMOND',5);
define ('COLS_NO_STACK',0);
define ('COLS_STACKED',1);
class Chart{

	/********************************************************
	*					  Variables						*
	********************************************************/
	var $image;
	var $title;
	var $subtitle;
	var $bgd;
	var $cbgd,$cbgd2;			// chart band colours
	var $txtcol;
	var $white;
	var $black;
	var $gridcol;
	var $width;
	var $height;
	var $cwidth,$cheight;		// chart dimensions
	var $scolors;
	var $lm,$rm,$tm,$bm;		// margins
	var $ytitle;				// y axis settings
	var $ymin = 0;
	var $ymax = 0;
	var $ygrid = false;
	var $ygridint;
	var $xtitle;				// x axis settings
	var $xgrid = false;
	var $xgridint;
	var $xcount;				// count of values in each series
	var $seriescount;
	var $colwidth;
	var $transparent;
	var $ispiechart;

	var $legends;
	var $stackcount;
	var $xlabels;
	var $data;
	var $mark_text;
	var $xmaxima;				// series max values
	var $xminima;
	var $stypes;				// series types
	var $stacked;				// series stacked?
	var $stackbase;
	/********************************************************
	*				   public methods					  *
	********************************************************/
	function Chart ($awidth, $aheight=0) {
		$this->width = $awidth;
		$this->height = $aheight==0 ? floor($awidth / 1.616) : $aheight;
		$this->image = imagecreate($this->width, $this->height);
		$this->bgd = imagecolorallocate($this->image,0xFF,0xFF,0xFF);
		$this->white = imagecolorallocate($this->image,0xFF,0xFF,0xFF);
		$this->cbgd = imagecolorallocate($this->image,0xEE,0xEE,0xEE);
		$this->cbgd2 = imagecolorallocate($this->image,0xDD,0xDD,0xDD);
		$this->txtcol = imagecolorallocate($this->image,0,0,0);
		$this->black = imagecolorallocate($this->image,0,0,0);
		$this->gridcol = imagecolorallocate($this->image,0x66,0x66,0x66);
		$this->scolors = array (
			0 => imagecolorallocate ($this->image,0xFF,0x66,0x66),
			1 => imagecolorallocate ($this->image,0x66,0x66,0xCC),
			2 => imagecolorallocate ($this->image,0x66,0xcc,0x66),
			3 => imagecolorallocate ($this->image,0x99,0x00,0x99),
			4 => imagecolorallocate ($this->image,0xFF,0x99,0x00)
		);
		$this->tm = 50;
		$this->bm = 50;
		$this->lm = 80;
		$this->rm = 20;
		$this->cwidth = $this->width - ($this->lm + $this->rm);
		$this->cheight = $this->height - ($this->tm + $this->bm);
		$this->seriescount = 0;
		$this->xcount = 0;
		$this->legends = array();
		$this->stackcount = array();
		$this->xlabels = array();
		$this->data = array();
		$this->mark_text = array();
		$this->xmaxima = array();				// series max values
		$this->xminima = array();
		$this->stypes = array();				// series types
		$this->stacked = array();				// series stacked?
		$this->stackbase = array();
	}
	function setMargins ($l=0, $t=0, $r=0, $b=0) {
		if ($t>0) $this->tm = $t;
		if ($b>0) $this->bm = $b;
		if ($l>0) $this->lm = $l;
		if ($r>0) $this->rm = $r;
	}
	function setBgColor ($c) {
		$this->bgd = imagecolorallocate($this->image,$c['r'],$c['g'],$c['b']);
		if ($c['trans'])
			imagecolortransparent($this->image,$this->bgd);
		$this->transparent = $c['trans'];
	}
	function setChartBgColor ($r, $g, $b, $asbg=0) {
		$this->cbgd = $asbg ? $this->bgd : imagecolorallocate($this->image,$r,$g,$b);
		$this->cbgd2 = $this->cbgd;
	}
	function setChartBgColor2 ($r, $g, $b) {
		$this->cbgd2 = imagecolorallocate($this->image,$r,$g,$b);
	}
	function setTextColor ($r, $g, $b) {
		$this->txtcol = imagecolorallocate($this->image,$r,$g,$b);
	}
	function setGridColor ($r, $g, $b) {
		$this->gridcol = imagecolorallocate($this->image,$r,$g,$b);
	}
	function setSeriesColor ($n, $r, $g, $b) {
		if ($n<1) $n=1;
		$this->scolors[$n-1] = imagecolorallocate($this->image,$r,$g,$b);
	}
	function setTitle ($t) {
		if ($t['title']) $this->title = $t['title'];
		if ($t['subtitle']) $this->subtitle = $t['subtitle'];
	}
	function setYAxis($y) {
		$this->ytitle = $y['title'];
		$this->ymin = $y['min'];
		$this->ymax = $y['max'];
		$this->ygrid = $y['grid'];
		$this->ygridint = $y['gridint'] < 2 ? 10 : $y['gridint'];
	}
	function setXAxis ($x) {
		$this->xtitle = $x['title'];
		$this->xgrid = $x['grid'];
	}
	function setXLabels ($labs) {
		$this->xlabels = is_array($labs) ? $labs : explode(",",$labs);
		$this->xcount = count($this->xlabels);
	}
	function addDataSeries ($d) {
		$n = $this->seriescount++;
		$this->stypes[$n] = $d['type'];
		$this->stacked[$n] = $d['stacked'];
		$data = is_array($d['vals']) ? $d['vals'] : explode(",",$d['vals']);
		$mark_text = is_array($d['mark_text']) ? $d['mark_text'] : explode(",",$d['mark_text']);
		$this->xmaxima[$n] = max($data);
		$this->xminima[$n] = min($data);
		$dc = count($data);
		if ($this->xcount < $dc) {
			for ($i=$this->xcount,$L = 'A'; $i < $dc; $i++, $L++)
				$this->xlabels[$i] = $L;
			$this->xcount = $dc;
		}
		if ($dc < $this->xcount) array_pad($data,$this->xcount,0);
		$this->data[$n] = $data;
		$this->mark_text[$n] = $mark_text;
		$this->legends[$n] = $d['legend']=='' ? '_' : $d['legend'];
	}
	function drawGraph($filename='') {
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");             // Date in the past
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . "GMT"); // always modified
		header("Cache-Control: no-cache, must-revalidate");           // HTTP/1.1
		header("Pragma: no-cache");                                   // HTTP/1.0
		header("Content-type: image/png");
		$this->_draw();
		if ($filename!=''){
			Imagepng( $this->image, $filename );
		}else{
			Imagepng($this->image);
		}
		imagedestroy( $this->image );
		exit;
	}
	function display($filename='') {
			 $this->drawGraph($filename);
	}

	/********************************************************
	*				   private methods					 *
	********************************************************/
	function _draw() {
		$a = array_keys($this->stypes,'P');
		$this->ispiechart = count($a)>0;
		$this->_calcrmargin();
		imagefilledrectangle($this->image,0,0,$this->width-1,$this->height-1,$this->bgd);
		if (!$this->transparent)
			imagerectangle($this->image,0,0,$this->width-1,$this->height-1,$this->txtcol);
		imagefilledrectangle($this->image, $this->lm, $this->tm, $this->width-$this->rm, $this->height-$this->bm, $this->cbgd);
		$this->stackcount=0;
		for ($s=0; $s<$this->seriescount; $s++) {
			if (($this->stypes[$s] == 'C') && ($this->stacked[$s]==1)) $this->stackcount++;
			$this->stackbase[$s]=0;
		}

		$this->_drawtitles();
		$this->_drawaxes();
		$this->_drawlegends();
		for ($i=0; $i<$this->seriescount; $i++) $this->_plotSeries($i);
	}

	function _drawpieval($i,$alpha,$x,$y,$r) {
		$pietot = 0;
		for ($s=0; $s<$this->seriescount; $s++) {
			if ($this->stypes[$s]=='P') $pietot += array_sum($this->data[$s]);
		}
		$val = array_sum($this->data[$i]);
		$pc = sprintf('%0.1f%%',$val * 100 / $pietot);
		$tx = $x + $r*0.75 * cos(deg2rad($alpha));
		$ty = $y + $r*0.75 * sin(deg2rad($alpha));
		$fw = imagefontwidth(2);
		switch (($this->stacked[$i]>>2) & 0x3) {
		case 1: //plot value
			$tw = strlen($val)*$fw;
			$tx -= $tw/2;
			imagefilledrectangle($this->image,$tx-2,$ty-6,$tx + $tw + 2,$ty+6,$this->white);
			imagestring($this->image,2,$tx,$ty-5,$val,$this->txtcol);
			break;
		case 2: // plot %
		case 3:
			$tw = strlen($pc)*$fw;
			$tx -= $tw/2;
			imagefilledrectangle($this->image,$tx-2,$ty-6,$tx + $tw + 2,$ty+6,$this->white);
			imagestring($this->image,2,$tx,$ty-5,$pc,$this->txtcol);
			break;
		}
	}

	function pielegends() {
		$pietot = 0;
		for ($s=0; $s<$this->seriescount; $s++) {
			if ($this->stypes[$s]=='P') $pietot += array_sum($this->data[$s]);
		}
		$a = array_keys($this->stypes,'P');
		$maxt = $maxv = 0;
		foreach ($a as $pie) {
			$val = array_sum($this->data[$pie]);
			$maxv = max($maxv, strlen($val));
			$txt = $this->legends[$pie];
			$maxt = max($maxt, strlen($txt));
		}
		foreach ($a as $pie) {
			$val = array_sum($this->data[$pie]);
			$pc = $val * 100 / $pietot;
			switch ($this->stacked[$pie] & 0x03) {
			case 1 : $this->legends[$pie] = sprintf("%-{$maxt}s %4.1f%%",$this->legends[$pie],$pc); break;
			case 2 : $this->legends[$pie] = sprintf("%-{$maxt}s %{$maxv}s",$this->legends[$pie],$val); break;
			case 3 : $this->legends[$pie] = sprintf("%-{$maxt}s %{$maxv}s %4.1f%%",$this->legends[$pie],$val,$pc); break;
			}
		}
	}

	function _calcrmargin() {
		if ($this->ispiechart) {
			$this->pielegends();
		}
		$leglen = array();
		foreach($this->legends as $leg) {
			$leglen[] = strlen($leg);
		}
		$maxleglen = max($leglen);
		if ($maxleglen==0) return;
		$legwid = $maxleglen * imagefontwidth(2) + 30;
		$this->rm = max($this->rm, $legwid);
		$this->cwidth = $this->width - $this->lm - $this->rm;
		$this->xgridint = ($this->cwidth / $this->xcount);
		$maxlabwid = $this->_maxlab();
		if ($maxlabwid > $this->xgridint) {
			$this->bm = max($this->bm,$maxlabwid+40);
			$this->cheight = $this->height - $this->tm - $this->bm;
		}
	}

	function _drawlegends() {
		$legx = $this->lm + $this->cwidth + 5;
		$legy = $this->tm;
		if ($this->stackcount>0) {
			$this->legends = array_reverse($this->legends,true);
		}
		foreach ($this->legends as $k=>$leg) {
			if ($leg != '_') {
				switch ($this->stypes[$k]) {
				case 'C':
					if ($this->ispiechart) break;
				case 'P':
					imagefilledrectangle($this->image,$legx,$legy,$legx+15,$legy+15,$this->scolors[$k]);
					imagerectangle($this->image,$legx,$legy,$legx+15,$legy+15,$this->black);
					imagestring($this->image,2,$legx+20,$legy+1,$leg,$this->txtcol);
					break;
				case 'L':
					if ($this->ispiechart) break;
					if ($this->stacked[$k]>=0) {
						imageline($this->image,$legx,$legy+7,$legx+15,$legy+7,$this->scolors[$k]);
						imageline($this->image,$legx,$legy+8,$legx+15,$legy+8,$this->scolors[$k]);
						imageline($this->image,$legx,$legy+9,$legx+15,$legy+9,$this->scolors[$k]);
					}
					$this->_drawmarker($legx+8,$legy+8,$this->stacked[$k], $this->scolors[$k]);
					imagestring($this->image,2,$legx+20,$legy+1,$leg,$this->txtcol);
				}
				$legy += 20;
			}
		}
	}

	function _val2y($v, $base=0) {
		$rv = $v + $base - $this->ymin;
		$ppu = $this->cheight/($this->ymax - $this->ymin);
		return $this->tm + $this->cheight - ($rv * $ppu) ;
	}

	function _drawmarker($x,$y,$m, $c) {
		$fill = $m < 0 ? $c : $this->white;
		$line = $m < 0 ? $c : $this->black;
		switch (abs($m)) {
		case 1:
			$x0 = $x-3; $x1 = $x+3; imageline($this->image,$x0,$y,$x1,$y,$line);
			$y0 = $y-3; $y1 = $y+3; imageline($this->image,$x,$y0,$x,$y1,$line);
			break;
		case 2:
			$x0 = $x-3; $x1 = $x+3;
			$y0 = $y-3; $y1 = $y+3;
			imageline($this->image,$x0,$y0,$x1,$y1,$line);
			imageline($this->image,$x1,$y0,$x0,$y1,$line);
			break;
		case 3:
			$w = $h = 8;
			imagearc($this->image,$x,$y,$w,$h,0,361,$this->black);
			imagefill($this->image,$x,$y,$fill);
			break;
		case 4:
			$x0 = $x-3; $x1 = $x+3;
			$y0 = $y-3; $y1 = $y+3;
			imagefilledrectangle($this->image,$x0,$y0,$x1,$y1,$fill);
			imagerectangle($this->image,$x0,$y0,$x1,$y1,$this->black);
			break;
		case 5:
			$p[] = $x; $p[] = $y-4; $p[] = $x+4; $p[] = $y;
			$p[] = $x; $p[] = $y+4; $p[] = $x-4; $p[] = $y;
			imagefilledpolygon($this->image,$p,4,$fill);
			imagepolygon($this->image,$p,4,$this->black);
			break;
		}
	}

	function _plotseries($i) {
		switch ($this->stypes[$i]) {
		case 'L':
			if ($this->ispiechart) break;
			for ($p=0; $p<$this->xcount; $p++) {
				$pts[$p][0] = $this->lm + $p * $this->xgridint + $this->xgridint/2;
				$pts[$p][1] = $this->_val2y($this->data[$i][$p]);
			}
			for ($p=1; $p<$this->xcount; $p++) {
				if (!isset($this->data[$i][$p])) continue;
				if ($this->stacked[$i]<0) continue;
				imageline($this->image,$pts[$p-1][0],$pts[$p-1][1],$pts[$p][0],$pts[$p][1],$this->scolors[$i]);
				imageline($this->image,$pts[$p-1][0],$pts[$p-1][1]-1,$pts[$p][0],$pts[$p][1]-1,$this->scolors[$i]);
				imageline($this->image,$pts[$p-1][0],$pts[$p-1][1]+1,$pts[$p][0],$pts[$p][1]+1,$this->scolors[$i]);
			}
			if ($this->stacked[$i]!=0) {
				for ($p=0; $p<$this->xcount; $p++) {
					if (!isset($this->data[$i][$p])) continue;
					$this->_drawmarker($pts[$p][0],$pts[$p][1],$this->stacked[$i], $this->scolors[$i]);
//	function _drawmarker($x,$y,$m, $c) {
					if($this->mark_text[$i][$p] > 0){
						imagestring ($this->image,1,$pts[$p][0], $pts[$p][1], $this->mark_text[$i][$p], $this->black);
					}
				}
			}
			break;
		case 'C':
			if ($this->ispiechart) break;
			$stacked = $this->stackcount>0;
			$b = array_keys($this->stypes,'C');
			$colpos = 0;
			if (!$stacked){
				while (list($k,$v)=each($b)) {
					if ($v==$i) break;
					$colpos++;
				}
			}
			for ($p=0; $p<$this->xcount; $p++) {
				$x0 = $this->lm + 5 + $p * $this->xgridint + $colpos * $this->colwidth;
				$x1 = $x0 + $this->colwidth;
				$y0 = $this->_val2y($this->data[$i][$p],$this->stackbase[$p]);
				$y1 = $this->_val2y($this->ymin,$this->stackbase[$p]);
				if ($stacked) $this->stackbase[$p] += $this->data[$i][$p];
				imagefilledrectangle($this->image,$x0,$y0,$x1,$y1,$this->scolors[$i]);
				imagerectangle($this->image,$x0,$y0,$x1,$y1,$this->black);
			}
			break;
		case 'P':
			$x = ($this->lm + $this->cwidth + $this->lm)/2;
			$y = ($this->tm + $this->cheight + $this->tm)/2;
			$w = $h = min($this->cwidth, $this->cheight) - 10;
			$pietot = 0;
			for ($s=0; $s<$this->seriescount; $s++) {
				if ($this->stypes[$s]=='P') $pietot += array_sum($this->data[$s]);
			}
			$alpha = $this->stackbase[0];
			$theta = array_sum($this->data[$i])*360 / $pietot;
			imagefilledarc($this->image,$x,$y,$w,$h,$alpha,$alpha+$theta,$this->scolors[$i],IMG_ARC_PIE);
			$this->_drawpieval($i,$alpha+$theta/2,$x,$y,$w/2);
			$this->stackbase[0] += $theta;
		}
	}

	function _drawtitles() {
		$cw = imagefontwidth(5);
		$l = strlen($this->title);
		$tw = $cw*$l;
		$x = $this->lm + ($this->cwidth - $tw)/2;
		imagestring ($this->image,5,$x, 5, $this->title, $this->txtcol);
		$cw = imagefontwidth(4);
		$l = strlen($this->subtitle);
		$tw = $cw*$l;
		$x = $this->lm + ($this->cwidth - $tw)/2;
		imagestring ($this->image,4,$x, 25, $this->subtitle, $this->txtcol);
	}

	function _drawaxes() {
		if ($this->ispiechart) return;
		$changed = 0;
		$ym = $this->_calcymax();
		if (isset($this->ygridint)) {
			if ($this->ygridint < 2)
				$this->ygridint = ($this->ymax - $this->ymin)/4;
		}
		else $this->ygridint = ($this->ymax - $this->ymin)/4;

		$i =  $this->ygridint;

		if ($this->ymax < $ym ) {
			$this->ymax = floor(($ym + $i)/$i) * $i;
			$changed = 1;
		}
		$yn = min($this->xminima);
		if ($this->ymin > $yn) {
			$this->ymax = floor(($ym - $i)/$i) * $i;
			$changed = 1;
		}

		$this->_drawyaxis();
		$this->_drawxaxis();
	}

	function _drawyaxis() {
		$x0 = $this->lm;
		$y0 = $this->tm + $this->cheight;
		$x1 = $x0;
		$y1 = $this->tm;
		$div = $this->ygridint * $this->cheight / ($this->ymax - $this->ymin);
		$grx = $this->ygrid ? $this->lm + $this->cwidth : $this->lm-3;
		for ($y = $y1,$v=$this->ymax,$i=0; $y < $y0-2; $y += $div, $v -= $this->ygridint, $i++) {
			if ($this->cbgd != $this->cbgd2) {
				$col = ($i%2) ? $this->cbgd : $this->cbgd2;
				imagefilledrectangle($this->image,$x0,$y,$this->lm + $this->cwidth,$y+$div,$col);
			}
			imageline($this->image,$x0,$y,$grx,$y,$this->gridcol);
			$tw = strlen("$v")*imagefontwidth(2);
			imagestring($this->image,2,$x0 - $tw  - 5, $y - 5, $v, $this->txtcol);
		}
		$tw = strlen("$this->ymin")*imagefontwidth(2);
		$y = $this->tm + $this->cheight;
		imagestring($this->image,2,$x0 - $tw  - 5, $y - 6, $this->ymin, $this->txtcol);
		imageline($this->image,$x0,$y0,$x1,$y1,$this->gridcol);
		$tw = strlen($this->ytitle) * imagefontwidth(3);
		$y = ($this->tm + $this->height - $this->bm + $tw)/2;
		$x = 10;
		imagestringup($this->image,3,$x,$y,$this->ytitle,$this->txtcol);
	}

	function _maxlab() {
		$max = 0;
		for ($i=0; $i < $this->xcount; $i++) {
			$v = $this->xlabels[$i];
			$tw = strlen("$v")*imagefontwidth(2);
			$max = max ($max, $tw);
		}
		return $max;
	}

	function _drawxaxis() {
		$maxlabwid = $this->_maxlab();
		$x0 = $this->lm;
		$y0 = $this->tm + $this->cheight;
		$x1 = $x0 + $this->cwidth;
		$y1 = $y0;
		$div = $this->xgridint;
		imageline($this->image,$x0,$y0,$x1,$y1,$this->gridcol);
		$gry = $this->xgrid ? $this->tm : $this->tm + $this->cheight + 3 ;
		for ($x=$x1,$i=$this->xcount-1; $x>$x0+3; $x -= $div, $i--) {
			imageline($this->image,$x,$gry,$x,$y0,$this->gridcol);
			$v = $this->xlabels[$i];
			$tw = strlen("$v")*imagefontwidth(2);
			$th = imagefontheight(2);
			if ($maxlabwid < $div) {
				imagestring($this->image,2,$x - ($div+$tw)/2, $y0 + 5, $v, $this->txtcol);
			}	else {
				imagestringup($this->image,2,$x - ($div+$th)/2, $y0 + 5 + $tw, $v, $this->txtcol);
			}
		}
		$y = $this->height - 30;
		$tw = strlen($this->xtitle)*imagefontwidth(3);
		$x = ($this->lm + $this->cwidth + $this->lm - $tw)/2;
		imagestring($this->image,3,$x,$y,$this->xtitle,$this->txtcol);
	}

	function _calcymax() {

		$b = array_keys($this->stypes,'C');
		if ($this->stackcount == 0) {
			$m = max($this->xmaxima);
			if (count($b) > 0)
				$this->colwidth = ($this->xgridint - 10)/count($b);
			else
				$this->colwidth = 0;
		}
		else {
			$m=0;
			foreach ($b as $v) {
				$m += $this->xmaxima[$v];
				$this->stacked[$v] = 1;
			}
			$this->colwidth = ($this->xgridint - 10);
		}
		return $m;
	}
}
?>