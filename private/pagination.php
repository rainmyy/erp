<?php
class Pagination {
	var $output;
	var $file;
	var $pvar = "";
	var $psize;
	var $curr;
	var $varstr;
	var $total_pages;

	/**
	* page set
	*
	* @access public
	* @param int $pagesize The pagesize
	* @param int $total	The toal records count
	* @param int $current  Current page,keep empty will auto read the get variable
	* @return void
	*/
	function set($pagesize=20,$total,$current=false) {
		global $_SERVER,$_GET;
		$this->total_pages = ceil($total/$pagesize);
		if (!$current) {$current = $_GET[$this->pvar];}
		if ($current>$this->total_pages) {$current = $this->total_pages;}
		if ($current<1) {$current = 1;}

		$this->curr  = $current;
		$this->psize = $pagesize;

		if ($this->total_pages > 1) {

			if ($current>10) {
				$this->output.='<a href="'.$this->file.$this->pvar.($current-10).($this->varstr).'.html" title="前10页">&laquo;</a>&nbsp;';
			}
			if ($current>1) {
				$this->output.='<a href="'.$this->file.$this->pvar.($current-1).($this->varstr).'.html" title="前1页">&#8249;</a>&nbsp;';
			}

			$start  = floor($current/10)*10;
			$end	= $start+9;

			if ($start<1)		  $start=1;
			if ($end>$this->total_pages)  $end=$this->total_pages;

			for ($i=$start; $i<=$end; $i++) {
				if ($current==$i) {
					$this->output.='<font color="red">'.$i.'</font>&nbsp;';
				} else {
					$this->output.='<a href="'.$this->file.$this->pvar.$i.$this->varstr.'.html">['.$i.']</a>&nbsp;';
				}
			}

			if ($current<$this->total_pages) {
				$this->output.='<a href="'.$this->file.$this->pvar.($current+1).($this->varstr).'.html" title="后1页">&#8250;</a>&nbsp;';
			}
			if ($this->total_pages>10 && ($this->total_pages-$current)>=10 ) {
				$this->output.='<a href="'.$this->file.$this->pvar.($current+10).($this->varstr).'.html" title="后10页">&raquo;</a>';
			}
		}
	}

	/**
	* passed variable set
	*
	* @access public
	* @param array $data  The parameter u wanta passed,see the example above.
	* @return void
	*/ 
	function setvar($data) {
		foreach ($data as $k=>$v) {
			$this->varstr.='-'.$k.urlencode($v);
		}
	}

	/**
	* Output
	*
	* @access public
	* @param bool $return  Set it true will return a string,otherwish will output automatic
	* @return string
	*/
	function output($return = true) {
		if ($return) {
			return $this->output;
		} else {
			echo $this->output;
		}
	}

	/**
	* Make the limit
	*
	* @access public
	* @return string
	*/
	function limit($arr=0) {
		if ($arr) {
			settype($arr,"array");
			$arr[1] = ($this->curr-1)*$this->psize;
			$arr[2] = $this->psize;
			return $arr;
		} else {
			return (($this->curr-1)*$this->psize).','.$this->psize;
		}
	}

} //End Class
?>