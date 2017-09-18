var dc={};
dc.menu = function(id){ //菜单
	if(typeof(id)=="undefined")return false;
	$('#'+id).addClass('menu').show().children()
	.hover(function(){$(this).addClass('hover');}, function(){$(this).removeClass('hover');})
	.each(function(){ //ul>li
		if($(this).has('ul').length==0){
			if(!$(this).hasClass('disabled') && $(this).html()!=''){
				$(this).addClass('simplex');
			}else if($(this).has('a').length==1){ // is href
				$(this).addClass('simplex');
			}else if($(this).html()==''){
				$(this).addClass('separator');
			}
		}else{
			$(this).addClass('polymorph')
			.hover(function(){$(this).addClass('hover').children().fadeIn("fast");}, function(){$(this).removeClass('hover').children().hide();})
			.children()
			.each(function(){ //ul>li
				$(this).addClass('item')
					.children().each(function(){  //ul>li>ul
						$(this).addClass('list').hover(function(){ //ul>li>ul>li
						$(this).addClass('hover');
					},
					function(){
						$(this).removeClass('hover');
					});
				});
			});
		}
	});
}
dc.tabhover=function(){ //table每行鼠标滑过换背景色,设置nohover属性则无hover事件
	$('table tr').not('[nohover]').hover(function(){
		$(this).addClass('over');
	},
	function(){
		$(this).removeClass('over');
	}).each(function(){
		if($(this).children().length>1){
			$(this).children().first().toggle(function(){
				$(this).parent().addClass('pin');
			}, function(){
				$(this).parent().removeClass('pin');
			});
		}
	});
}
dc.listhover=function(){ //table每行鼠标滑过换背景色,设置nohover属性则无hover事件
	$('#mmlist li').hover(function(){
		$(this).addClass('over');
	},
	function(){
		$(this).removeClass('over');
	});
}
dc.tabs=function(args){
	if(typeof(args.id)=="undefined")return false;
	var id = $('#'+args.id), ts=$('#'+args.id+' > dt'), ps=$('#'+args.id+' > dd');
	if(typeof(args.id) == "undefined")return;
	if(typeof(args.sid) == "undefined"){
		args.sid=$.cookie("tabs"+args.id);
		if(args.sid == "")args.sid=0;
	}else
		$.cookie("tabs-"+args.id, args.id);//60 minutes
	if(args.sid > ts.length){
		args.sid=ts.length;
	}
	if(ts.eq(args.sid).attr('disabled')!=undefined){
		args.sid=0;
	}
	ts.filter('[disabled]').addClass('disabled');//对disabled属性的标签关闭click事件
	ts.each(function(i){
		$(this).attr('tabid', i).not('[disabled]').click(function(){
			var e=$(this), i=e.attr('tabid');
			var k=e.parent().children('dt.selected').removeClass('selected').attr('tabid');
			e.addClass('selected');
			e.parent().children('dd').eq(k).hide().parent().children('dd').eq(i).show();
			$.cookie('tabs'+e.parent().attr('id'), i, 30);
		})
		ts.eq(args.sid).addClass('selected');
		id.show();
		ps.eq(args.sid).show();
	});

}
dc.upload={
	handle:null,
	debug:function(ex){
		alert(ex);
	},
	selectAll:function(v){
		$('#fileList :radio[value='+v+']').each(function(){$(this).attr('checked', true);});
	},
	showUploaded:function(){
		$.get('/s.php?module=ajax&action=showUploaded&t='+$('#timenow').val()+'&uploadModule='+$('#uploadModule').val()+'&uploadMid='+$('#uploadMid').val()+'&r=' + Math.random(), function(data){
			$('#fileList').html(data);
	  });
	},
	showThumb:function(e, url){
		var thumb = $('#uploadThumb');
		if(thumb.length==0){
			thumb = $('<div class="uploadThumb" id="uploadThumb"></div>').appendTo('body');
		}
		var o = $(e).offset();
		thumb.css({'top':o.top+28+'px', 'left':o.left+30+'px'}).html('<img src="/public/attachs/'+url+'">').show();
	},
	hiddenThumb:function(){
		var thumb=$('#uploadThumb');
		if(thumb.length==1){
			thumb.hide();
		}
	},
	init:function(id, t){
		this.handle = new SWFUpload({
			upload_url: "/s.php?module=ajax&action=upload",
			post_params: {'s':id, 't':t},
			// File Upload Settings
			file_size_limit : "2MB",
			file_types : "*.jpg;*.jpeg;*.gif;*.png;*.rar;*.zip;*.pdf;*.doc;*.docx;*.xls,xlsx;*.pps;*.ppt;*.msg;*.txt;*.mht;*.htm;*.html;*.ai;*.dwg",
			file_types_description : "所有允许的文件类型",
			file_upload_limit : 20,
			file_queue_limit : 0,
			swfupload_preload_handler : this.preLoad,
			swfupload_load_failed_handler : this.loadFailed,
			file_queued_handler : this.fileQueued,
			file_queue_error_handler : this.fileQueueError,
			file_dialog_complete_handler : this.fileDialogComplete,
			upload_start_handler : this.uploadStart,
			upload_progress_handler : this.uploadProgress,
			upload_error_handler : this.uploadError,
			upload_success_handler : this.uploadSuccess,
			upload_complete_handler : this.uploadComplete,
			// Button Settings
			button_image_url : "/public/images/upload.png",
			button_placeholder_id : "selectFile",
			button_width: 66,
			button_height: 26,
			// Flash Settings
			flash_url : "/public/swfupload.swf",
			flash9_url : "/public/swfupload_fp9.swf",
			// Debug Settings
			debug: false
		});		
	},
	cancelQueue:function(f){
		$('#cancelUpload').attr('disabled', true);
		$('#startUpload').attr('disabled', true);
		$('#submitButton').attr('disabled', false);
		this.handle.stopUpload();
		var stats;
		do{
			stats = this.handle.getStats();
			this.handle.cancelUpload();
		}while(stats.files_queued !== 0);
	},
	preLoad:function(){
		if(!this.support.loading){
			alert("您的Flash Player的版本太低，需要使用 9.028 或 以上的版本。");
			return false;
		}
	},
	loadFailed:function(){
		alert("程序内部加载错误，请联系管理员。");
	},
	fileQueued:function(f){
		try{
			$('#fileQueue').append('<div id="fn'+f.index+'" class="filequeue"><span class="right small hand" onclick="dc.upload.setStatus('+f.index+', \'已取消\');dc.upload.handle.cancelUpload('+f.id+');">取消</span><span id="fs'+f.index+'" class="right small gray" style="margin-right:10px">待上传</span>['+(f.type==''?'　':'<span class="bold small">'+f.type.replace('\.', '').toUpperCase()+'</span>')+'] '+f.name+' <span class="small gray">('+f.size+')</span></div>');
		}catch(ex){
			this.debug(ex);
		}
	},
	setStatus:function(id, h){
		$('#fs'+id).html(h);
	},
	setCancel:function(id){
		if(dc.upload.handle.getStats().files_queued === 0){
		$('#cancelUpload').attr('disabled', true);
		$('#startUpload').attr('disabled', true);
		$('#submitButton').attr('disabled', false);
		}
		setTimeout(function(){$('#fn'+id).hide()}, 2000);
	},
	fileQueueError:function(f, errorCode, message){
		try{
			if(errorCode === SWFUpload.QUEUE_ERROR.QUEUE_LIMIT_EXCEEDED){
				alert("尝试加入队列的文件太多了。\n" + (message === 0 ? "已达上传上限。" : "最多可以再选择" + (message > 1 ? "不超过 " + message + " 个文件。" : "一个文件。")));
				return;
			}
			switch (errorCode){
			case SWFUpload.QUEUE_ERROR.FILE_EXCEEDS_SIZE_LIMIT:
				alert('文件('+f.name+')太大，超过限制尺寸(' + f.size + ')。');
				this.debug("Error Code: File too big, File name: " + f.name + ", File size: " + f.size + ", Message: " + message);
				break;
			case SWFUpload.QUEUE_ERROR.ZERO_BYTE_FILE:
				alert('文件('+f.name+')大小为“0”字节，不允许上传空文件。');
				this.debug("Error Code: Zero byte f, File name: " + f.name + ", File size: " + f.size + ", Message: " + message);
				break;
			case SWFUpload.QUEUE_ERROR.INVALID_FILETYPE:
				alert('文件('+f.name+')类型不允许上传。允许的类型请查看页面中的说明。');
				this.debug("Error Code: Invalid File Type, File name: " + f.name + ", File size: " + f.size + ", Message: " + message);
				break;
			case SWFUpload.QUEUE_ERROR.QUEUE_LIMIT_EXCEEDED:
				alert("您选择了太多的文件，" +  (message > 1 ? "还能再添加" +  message + "个文件。" : "已经达到单次上传队列数量的上限。"));
				break;
			default:
				if(f !== null)alert('未知错误，请联系管理员。');
				this.debug("Error Code: " + errorCode + ", File name: " + f.name + ", File size: " + f.size + ", Message: " + message);
				break;
			}
		}catch(ex){
			this.debug(ex);
		}
	},
	fileDialogComplete:function(numFilesSelected, numFilesQueued){
		try{
			if(dc.upload.handle.getStats().files_queued > 0){
				$('#cancelUpload').attr('disabled', false);
				$('#startUpload').attr('disabled', false);
				$('#submitButton').attr('disabled', true);
			}
			dc.upload.handle.startUpload();
		}catch(ex) {
			this.debug(ex);
		}
	},
	uploadStart:function(f){
		try{
			/* I don't want to do any file validation or anything,  I'll just update the UI and return true to indicate that the upload should start */
			dc.upload.setStatus(f.index, '上传中...');
		}catch(ex){}
		return true;
	},
	uploadProgress:function(f, a, t){
		try{
			// a: bytesLoaded, t: bytesTotal, a/t=percent
			dc.upload.setStatus(f.index, '已上传'+Math.ceil((a / t) * 100)+'%');
		}catch(ex){
			this.debug(ex);
		}
	},
	uploadSuccess:function(f, serverData){
		try{
			dc.upload.setStatus(f.index, '已完成');
			dc.upload.setCancel(f.index);
			if(dc.upload.handle.getStats().files_queued === 0){
				dc.upload.showUploaded();
			}
		}catch(ex){
			this.debug(ex);
		}
	},
	uploadComplete:function(f){
		try{

			/*  I want the next upload to continue automatically so I'll call startUpload here */
			if(dc.upload.handle.getStats().files_queued === 0){
				$('#cancelUpload').attr('disabled', true);
				$('#startUpload').attr('disabled', true);
				$('#submitButton').attr('disabled', false);
			}else{	
				this.startUpload();
			}
		}catch(ex){
			this.debug(ex);
		}
	},
	uploadError:function(f, errorCode, message){
		try{
			switch (errorCode){
			case SWFUpload.UPLOAD_ERROR.HTTP_ERROR:
				dc.upload.setStatus(f.index, "Upload Error: " + message);
				this.debug("Error Code: HTTP Error, File name: " + f.name + ", Message: " + message);
				break;
			case SWFUpload.UPLOAD_ERROR.MISSING_UPLOAD_URL:
				dc.upload.setStatus(f.index, "配置错误");
				this.debug("Error Code: No backend f, File name: " + f.name + ", Message: " + message);
				break;
			case SWFUpload.UPLOAD_ERROR.UPLOAD_FAILED:
				dc.upload.setStatus(f.index, "上传失败");
				this.debug("Error Code: Upload Failed, File name: " + f.name + ", File size: " + f.size + ", Message: " + message);
				break;
			case SWFUpload.UPLOAD_ERROR.IO_ERROR:
				dc.upload.setStatus(f.index, "服务器IO错误");
				this.debug("Error Code: IO Error, File name: " + f.name + ", Message: " + message);
				break;
			case SWFUpload.UPLOAD_ERROR.SECURITY_ERROR:
				dc.upload.setStatus(f.index, "安全错误");
				this.debug("Error Code: Security Error, File name: " + f.name + ", Message: " + message);
				break;
			case SWFUpload.UPLOAD_ERROR.UPLOAD_LIMIT_EXCEEDED:
				dc.upload.setStatus(f.index, "上传超上限");
				this.debug("Error Code: Upload Limit Exceeded, File name: " + f.name + ", File size: " + f.size + ", Message: " + message);
				break;
			case SWFUpload.UPLOAD_ERROR.SPECIFIED_FILE_ID_NOT_FOUND:
				dc.upload.setStatus(f.index, "文件未找到");
				this.debug("Error Code: The f was not found, File name: " + f.name + ", File size: " + f.size + ", Message: " + message);
				break;
			case SWFUpload.UPLOAD_ERROR.FILE_VALIDATION_FAILED:
				dc.upload.setStatus(f.index, "校验失败，跳过");
				this.debug("Error Code: File Validation Failed, File name: " + f.name + ", File size: " + f.size + ", Message: " + message);
				break;
			case SWFUpload.UPLOAD_ERROR.FILE_CANCELLED:
				if(this.getStats().fs_queued === 0){
					$('#cancelUpload').attr('disabled', true);
					$('#startUpload').attr('disabled', true);
					$('#submitButton').attr('disabled', false);
				}
				dc.upload.setStatus(f.index, '已取消');
				dc.upload.setCancel(f.index);
				break;
			case SWFUpload.UPLOAD_ERROR.UPLOAD_STOPPED:
				dc.upload.setStatus(f.index, "已停止");
				break;
			default:
				dc.upload.setStatus(f.index, "Unhandled Error: " + error_code);
				this.debug("Error Code: " + errorCode + ", File name: " + f.name + ", File size: " + f.size + ", Message: " + message);
				break;
			}
		}catch(ex){
			this.debug(ex);
		}
	}
}
dc.region={
	cacheProvince:new Array(),
	cacheCity:new Array(),
	province:function(e){
		var p = $('#province');
		p.hide();
		$('#city').hide();
		$('#lastCountryid').val(e.value);
		$('#provinceid').val(-1);
		$('#cityid').val(-1);
		if(this.cacheProvince[e.value] == undefined && this.cacheProvince[e.value] != 'false'){
			$.get('/s.php?module=ajax&action=region&countryid='+e.value,function(data){
				if(data!='false'){
					$('#province').html(data).show();
				}
				dc.region.cacheProvince[$('#lastCountryid').val()]=data;
			});
		}else if(this.cacheProvince[e.value] != 'false'){
			p.html(this.cacheProvince[e.value]).show();
		}
	},
	city:function(e){
		var c = $('#city');
		c.hide();
		$('#lastProvinceid').val(e.value);
		$('#cityid').val(-1);
		if(this.cacheCity[e.value] == undefined && this.cacheCity[e.value] != 'false'){
			$.get('/s.php?module=ajax&action=region&provinceid='+e.value, function(data){
				if(data!='false'){
					$('#city').html(data).show();
				}
				dc.region.cacheCity[$('#lastProvinceid').val()]=data;
			});
		}else if(this.cacheCity[e.value] != 'false'){
			c.html(this.cacheCity[e.value]).show();
		}
	}
}
dc.home={
	user:function(o){
		if (o.username.value == '' || o.username.value == "用户名称"){
			alert("请输入用户名称。");
			o.username.focus();
			return false;
		}
	}
}
dc.user={
	groupSelect:function(id){
		var g = document.getElementsByName('purviews'+id+'[]');
		for(var i=0; i<g.length; i++){
			if(g[i].type == 'checkbox'){
				if(g[i].checked == true){
					g[i].checked = false;
				}else{
					g[i].checked = true;
				}
			}
		}
	}, 
	allSelect:function(){
		
	}
}