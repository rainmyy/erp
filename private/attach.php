<?php
// 
class attach {
	var $kclass;
	var $upload;
	var $user_file_path;
	var $pfnum;
	var $pfid;
	var $attachid; //array

	// 
	function ini_upload(){
		if($this->user_file_path == ''){
			$this->user_file_path = '/public/uploads';
		}
		if (@!is_dir($this->user_file_path)) {
			@mkdir($this->user_file_path, 0777);
		}
		require_once("private/upload.php");
		$this->upload = new upload();
		$this->upload->out_file_dir     = $this->user_file_path;
		$this->upload->max_file_size    = $this->kclass->vars['allowed_file_size'];
		$this->upload->make_script_safe = 1;
		$this->upload->allowed_file_ext = explode(',', $this->kclass->vars['allowed_file_ext']);
	}

	// 
	function view($v){
		$condition = "belong = '".$v['belong']."' AND id = '".$v['id']."'";
		if($v['is_image'] == 1){
			$condition .= ' AND is_image = 1';
		}
		$attachs = $this->kclass->DB_site->query("
			SELECT *
			FROM attachment
			WHERE $condition
		");
		if($this->kclass->DB_site->num_rows()){
			$picture .= '<div class="right">';
			while($attach = $this->kclass->DB_site->fetch_array($attachs)){
				if($attach['is_image']){
					$picture .= '<div class="thumb">'.$attach['title'].'<br /><a href="/'.$attach['url'].'" target="_blank"><img src="/'.$attach['thumb_url'].'" width="'.$attach['thumb_width'].'" height="'.$attach['thumb_height'].'"></a></div><br />';
				}
			}
			$picture .= '</div>';
		}else{
			$picture = '';
		}
		return $picture;
	}

	// 
	function kill($attach){
		@unlink($attach['url']);
		if($attach['newname'] != $attach['thumb_name']){
			@unlink($attach['thumb_url']);
		}
		$this->kclass->DB_site->query("
			DELETE FROM `attachment`
			WHERE attachid = '".$attach['attachid']."'
		");
		return true;
	}

	// 
	function remove($r){
		if($r['attachid'] > 0){
			$attach = $this->kclass->DB_site->query_first("
				SELECT *
				FROM `attachment`
				WHERE attachid = '".$attachid."'
			");
			if($attach){
				$this->kill($attach);
			}
		}

		if($r['belong'] != ''){
			$attachs = $this->kclass->DB_site->query("
				SELECT *
				FROM `attachment`
				WHERE belong = '".$r['belong']."' AND id = '".$r['id']."'
			");
			if($this->kclass->DB_site->num_rows()){
				while($attach = $this->kclass->DB_site->fetch_array($attachs)){
					$this->kill($attach);
				}
			}
		}

		return true;
	}

	// 
	function modify_id($m){
		$this->kclass->DB_site->query("
			UPDATE `attachment`
			SET belong = '".$m['belong']."', id = '".$m['id']."'
			WHERE attachid IN (".implode(',', $this->attachid).")
		");
	}

	// 
	function doupload(){
		$this->upload->upload_now = true;
		for($i = 0; $i < $this->pfnum; $i++){
			$out_file_name = TIMENOW.'_'.$i;
			$this->upload->out_file_name = $out_file_name;
			$this->upload->upload_form_field = 'pfile_'.$this->pfid[$i];
			$this->upload->upload_process();
			if($this->upload->is_image AND ($this->upload->image_size[0] > 135 OR $this->upload->image_size[1] > 100)){
				require_once("private/image.php");
				$image = new image();
				
				$image->in_type        = 'file';
				$image->out_type       = 'file';
				$image->in_file_dir    = $this->user_file_path;
				$image->in_file_name   = $this->upload->parsed_file_name;
				$image->desired_width  = $this->kclass->vars['thumb_width'];
				$image->desired_height = $this->kclass->vars['thumb_height'];
				$image->gd_version     = $this->kclass->vars['gd_version'];

				$thumb = $image->generate_thumbnail();
			}else{
				$thumb = array(
					'thumb_location' => $out_file_name,
					'thumb_width' => $this->upload->image_size[0],
					'thumb_height' => $this->upload->image_size[1],
				);
			}

			$this->kclass->DB_site->query("
				INSERT INTO `attachment` ( `title` , `name` , `newname` , `type` , `filesize` , `is_image` , `thumb_name` , `thumb_width` , `thumb_height` , `image_width` , `image_height` , `approved` , `url` , `thumb_url` , `dateline` ) 
				VALUES (
				'".$u['input']['ptext_'.$pfid[$i]]."', '".$this->upload->original_file_name."', '".$out_file_name."', '".$this->upload->real_file_extension."', '".$this->upload->file_size."', '".$this->upload->is_image."', '".$thumb['thumb_location']."', '".$thumb['thumb_width']."', '".$thumb['thumb_height']."', '".$this->upload->image_size[0]."', '".$this->upload->image_size[1]."', '".$this->kclass->iif($this->kclass->vars['image_approved']==1, 0, 1)."', '".$this->upload->saved_upload_name."', '".$this->upload->out_file_dir."/".$thumb['thumb_location']."', '".TIMENOW."'
				)
			");
			$this->attachid[] = $this->kclass->DB_site->insert_id();
		}
		return $this->attachid;
	}

	// 
	function verify(){
		$this->ini_upload();
		// check picture upload file
		for($i = 0; $i < $this->pfnum; $i++){
			$this->upload->upload_form_field = 'pfile_'.$this->pfid[$i];
			$this->upload->upload_process();
		
			if ($this->upload->error_no == 2){
				$errors[] = '图片文件 <b>'.$pfid[$i].'</b> 类型错误（允许的文件格式有 '.$this->kclass->vars['allowed_file_ext'].'）。';
			}
			if($upload->error_no == 3){
				$errors[] = '图片文件 <b>'.$pfid[$i].'</b> 太大（允许的文件大小是 '.$this->kclass->vars['allowed_file_size'].'）。';
			}
		}
		return $errors;
	}

	// 
	function modify_html($m){
		$attachs = $this->kclass->DB_site->query("
			SELECT *
			FROM attachment
			WHERE belong = '".$m['belong']."' AND id = '".$m['id']."'
		");
		if($this->kclass->DB_site->num_rows()){
			$pfile .= '<tr><td valign="top"><b>'.$m['title'].'</b></td><td><table cellpadding="4" cellspacing="0" border="0">';
			while($attach = $this->kclass->DB_site->fetch_array($attachs)){
				$pfile .= '<tr><td><div class="thumb"><a href="/'.$attach['url'].'" target="_blank"><img src="/'.$attach['thumb_url'].'" width="'.$attach['thumb_width'].'" height="'.$attach['thumb_height'].'"></a></div></td><td valign="top">操作: <label for="keep_'.$attach['attachid'].'_1"><input type="radio" class="nostyle" id="keep_'.$attach['attachid'].'_1" name="keep['.$attach['attachid'].']" value="1" checked>保留</label> <label for="keep_'.$attach['attachid'].'_0"><input type="radio" class="nostyle" id="keep_'.$attach['attachid'].'_0" name="keep['.$attach['attachid'].']" value="0">删除</label><br />名称: '.$attach['name'].'<br />大小: '.$this->kclass->size_format($attach['filesize']).'<br />描述: '.$attach['title'].'</td></tr>';
			}
			$pfile .= '</table></td></tr>';
		}
		return $pfile;
	}

	// 
	function add_html($h){
		if($h['fn'] == ''){
			$h['fn'] = 2;
		}
		$size = $this->kclass->size_format($this->kclass->vars['allowed_file_size']);
return <<<EOF
<script type="text/javascript" src="/public/attach.js"></script>
<tr>
	<td valign="top"><b>{$h['title']}</b>: <br />(<a href="javascript: addfile();" id="addfile">+</a> <a href="javascript: subfile();" id="subfile">-</a>)</td>
	<td valign="top">
	<table>
	<tr><td>允许上传的图片类型：{$this->kclass->vars['allowed_file_ext']}，文件需小于：{$size}。</td></tr>
	<tr id="file_1">
		<td>1.<input type="file" name="pfile_1" style="width: 200px;"> 描述:<input type="text" name="ptext_1" style="width: 151px;"></td>
	</tr>
	<tr id="file_2">
		<td>2.<input type="file" name="pfile_2" style="width: 200px;"> 描述:<input type="text" name="ptext_2" style="width: 151px;"></td>
	</tr>
	<tr id="file_3" style="display: none;">
		<td>3.<input type="file" name="pfile_3" style="width: 200px;"> 描述:<input type="text" name="ptext_3" style="width: 151px;"></td>
	</tr>
	<tr id="file_4" style="display: none;">
		<td>4.<input type="file" name="pfile_4" style="width: 200px;"> 描述:<input type="text" name="ptext_4" style="width: 151px;"></td>
	</tr>
	<tr id="file_5" style="display: none;">
		<td>5.<input type="file" name="pfile_5" style="width: 200px;"> 描述:<input type="text" name="ptext_5" style="width: 151px;"></td>
	</tr>
	</table>
	</td>
</tr>
EOF;
	}
}
?>