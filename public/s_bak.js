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
dc.notification=function(){ // 通知提示
	var time=new Date().getTime();
	setInterval(function(){
		$.getJSON('/s.php?module=ajax&action=notification&t='+time,
			function(r){
				if(r.notice==1){
					if(!$('#notice').children().hasClass('nCount')){
						$('#notice').append('<span class="nCount">'+r.count+'</span>');
						$('#noticeBoard').html(r.panel);
					}
				}else{
					$('.nCount').html('').removeClass('nCount');
					$('#noticeBoard').removeClass('click').html(r.panel);
				}
				$('#notice').attr('title',r.title);
			})
   },60000);
	$(document).click(function(event){
		$('#noticeBoard').removeClass('click');
	});
	$('#notice').click(function(event){
		event.stopPropagation();
		if(!$('#noticeBoard').hasClass('click')){
			$('#noticeBoard').addClass('click');
		}else{
			$('#noticeBoard').removeClass('click');
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
dc.tips=function(){
	$(".tip").tipTip({
		maxWidth: "auto", 
		edgeOffset: 10,
		fadeIn:0,
		fadeOut:0,
		delay:0,
		defaultPosition:"top"
	});
}
dc.listhover=function(){ //
	$('#mmlist li').hover(function(){
		$(this).addClass('over');
	},
	function(){
		$(this).removeClass('over');
	});
}
dc.divhover=function(){ //table每行鼠标滑过换背景色,设置nohover属性则无hover事件
	$('div.myodd').not('[nohover]').hover(function(){
		$(this).addClass('over');
	},function(){
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
	$('div.myeven').not('[nohover]').hover(function(){
		$(this).addClass('over');
	},function(){
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
dc.show=function(){
	$(".plus").toggle(
		function(){
			$(this).removeClass('plus').addClass('minus').parents('thead').siblings('tbody').show();
		},
		function(){
			$(this).removeClass('minus').addClass('plus').parents('thead').siblings('tbody').hide();
		}
	);
	$(".minus").toggle(
		function(){
			$(this).removeClass('minus').addClass('plus').parents('thead').siblings('tbody').hide();
		},
		function(){
			$(this).removeClass('plus').addClass('minus').parents('thead').siblings('tbody').show();
		}
	);
}
dc.checkSubmit=function(e){
	e.submitButton.disabled=true;
	e.submitButton.value = "正在提交";
}
dc.autoComplete=function(arg){
	var ajaxUrl;
	var id=arg.obj.id;
	$.widget("custom.catcomplete",$.ui.autocomplete,{
		_renderMenu: function(ul,items){
			var self = this;
			$.each(items, function(index,item){
				self._renderItem(ul,item);
			});
		}
	});
	if(arg.module=='material'){
		ajaxUrl='/s.php?module=ajax&action=chooserMaterial&line='+arg.line;
	}else if(arg.module=='supplier'){
		ajaxUrl='/s.php?module=ajax&action=chooserSupplier&line='+arg.line;
	}else if(arg.module=='customer'){
		ajaxUrl='/s.php?module=ajax&action=chooserCustomer&line='+arg.line;
	}
	$('#'+id).catcomplete({
		delay:0,
		source:ajaxUrl,
		select:function(event, ui){
			$('#'+id).prev().val(ui.item.id);
			if(ui.item.item!=''){
				$('#'+id).next().remove("");
				$('#'+id).after(ui.item.item);
				if(ui.item.m=='material'){
					$('#materialItemid'+arg.line).val(ui.item.pid);
				}
				if(ui.item.m=='customer'){
					dc.returnCountryAndCustomerStr(ui.item.id,arg.onchange);
				}
			}
			if(arg.s==1){// 是否进行版本选择
				dc.material.version(ui.item.mid,arg.line,ui.item.module);
			}
		}
	});
}
dc.updateNo=function(arg){
	var time=$('#realtime').val();
	var date=new Date(time);
	$.get('/s.php?module=ajax&action=updateNo&m='+arg.id+'&typeid='+arg.typeid+'&t='+date.getTime()/1000,
		function(r){
			$('#'+arg.id).val(r);
		}
	);
}
dc.switchsearch=function(){
	$('#search_menu').draggable({containment: 'document' });
	$('.switchsearch').click(function(){
		if($("#search_menu").is(":hidden")){
			$("#search_menu").fadeIn();
		}else{
			$("#search_menu").fadeOut();
		}
	});
}
dc.tableItem={
	s:0,//s:start
	page:function(p){//翻页 p:perpage
		this.url=window.location.search;
		this.url=this.url.replace("?","&a");
		this.url=this.url.replace("action","maction");
		this.s+=p;
		window.t=$('.page').html();
		$('.page').html("<span class=\"loading\"></span>正在加载...");
		$.get(
			'/s.php?module=ajax&action=tableItemPage'+this.url,
			{'start':this.s,'perpage':p}
			)
		.fail(function() {
			$('.page').html("加载失败！");
		})
		.done(function(r) {
			var d=eval("("+r+")");
			if(d.success==1){
				$('.page').html(window.t);$('#tbody').append(d.nextPage);
			}else{
				$('.page').html("").append(d.html);$('#tbody').append(d.nextPage);
			}
		});
	}
};
dc.listItem={
	s:new Array(),//s:start
	page:function(p,m,w,d){//翻页 p:perpage
		if(typeof(this.s[m])=="undefined")this.s[m]=0;
		this.s[m]+=p;
		this.i+=1;
		window.t=$("#"+m+"page").html();
		$("#"+m+"page").html("<span class=\"loading\"></span>正在加载...");
		$.get(
			'/s.php?module=ajax&action=tableItemPage',
			{'start':this.s[m],'perpage':p,'amodule':m,'mmodule':w,'mid':d}
			)
		.fail(function() {
			$("#"+m+"page").html("加载失败！");
		})
		.done(function(r) {
			var d=eval("("+r+")");
			if(d.success==1){
				$("#"+m+"page").html(window.t);$("#"+m).append(d.nextPage);
			}else{
				$("#"+m+"page").html("").append(d.html);$("#"+m).append(d.nextPage);
			}
		});
	}
};
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
	/*if($(this).has('selected')){
		dc.tabsItem(args);
	}*/
	ts.filter('[disabled]').addClass('disabled');//对disabled属性的标签关闭click事件
	ts.each(function(i){
		$(this).attr('tabid', i).not('[disabled]').click(function(){
			var e=$(this), i=e.attr('tabid');
			var k=e.parent().children('dt.selected').removeClass('selected').attr('tabid');
			e.addClass('selected');
			e.parent().children('dd').eq(k).hide().parent().children('dd').eq(i).show();
			$.cookie('tabs'+e.parent().attr('id'), i, 30);
			dc.tabsItem(args);
		});
		if($(this).hasClass('selected')){
			dc.tabsItem(args);
		}
		ts.eq(args.sid).addClass('selected');
		id.show();
		ps.eq(args.sid).show();
	});
}
dc.tabsItem=function(args){
	var j=$('.selected').attr('ajax');
	if(j!=''){
		var s=$('#'+j).attr('load');
		if(s==0){
			$("#"+j).html("<span class=\"loading\"></span>正在加载...");
			$.get('/s.php?module=ajax&action=tabsItem&tabmodule='+j,args)
			.fail(function(){
				$("#"+j).html("加载失败！");
			})
			.done(function(r){
				var d=eval("("+r+")");
				if(d.success==1){
					$("#"+j).html("").append(d.nextPage);$("#"+j).attr('load','1');
				}else{
					$("#"+j).html("").append(d.html);
				}
			});
		}
	}
};
dc.selectAll=function(){
	$('#selectAll').click(function(){
		if($(this).attr('checked')){
			$('.select').attr('checked','checked');
		}else{
			$('.select').removeAttr('checked');
		}
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
			file_types : "*.jpg;*.jpeg;*.gif;*.png;*.rar;*.zip;*.pdf;*.doc;*.docx;*.xls;*.xlsx;*.pps;*.ppt;*.msg;*.txt;*.mht;*.htm;*.html;*.ai;*.dwg",
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
		$('#regionid').val(e.value);
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
		$('#regionid').val(e.value);
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
	},
	cityChange:function(e){
		$('#regionid').val(e.value);
	}
}
dc.customer={
	check:function(e){
		if(e.title.value.length < 3){
			alert('客户的名称至少是3个英文字符。');
			e.title.focus();
			return false;
		}
		if(e.abbr.value.length < 1){
			alert('请填写客户的 英文简称。');
			e.abbr.focus();
			return false;
		}
		if(e.abbr.value.length > 20){
			alert('客户的英文简称不能超过 20 个英文字符。');
			e.abbr.focus();
			return false;
		}
		if(e.address.value.length < 3){
			alert('客户的英文地址至少是3个英文字符。');
			e.address.focus();
			return false;
		}
		if(e.address.value.length > 255){
			alert('客户的英文地址不能超过 255 个英文字符。');
			e.address.focus();
			return false;
		}
		if(e.regionid.value<=0 || e.regionid.value==''){
			alert('请填写客户的所属 国家。');
			e.countryid.focus();
			return false;
		}
		if(e.linkman.value.length < 2){
			alert('客户的主联系人至少是2个英文字符。');
			e.linkman.focus();
			return false;
		}
		if(e.linkman.value.length > 100){
			alert('客户的主联系人不能超过 100 个英文字符。');
			e.linkman.focus();
			return false;
		}
		if(e.position.value.length < 2){
			alert('客户主联系人的称谓至少是2个英文字符。');
			e.position.focus();
			return false;
		}
		if(e.position.value.length > 50){
			alert('客户的主联系人的称谓不能超过 50 个英文字符。');
			e.position.focus();
			return false;
		}
		if(e.email.value.length < 5){
			alert('客户的邮箱至少5个英文字符。');
			e.email.focus();
			return false;
		}
		if(e.email.value.length > 100){
			alert('客户的邮箱不能超过 100 个英文字符。');
			e.email.focus();
			return false;
		}
		if(e.telephone.value.length < 5){
			alert('客户的电话至少是5个英文字符。');
			e.telephone.focus();
			return false;
		}
		if(e.telephone.value.length > 50){
			alert('客户的电话不能超过 50 个英文字符。');
			e.telephone.focus();
			return false;
		}
	}
}
dc.supplier={
	check:function(e){
		if(e.title.value.length < 3){
			alert('请填写供应商的名称长度至少是3个以上。');
			e.title.focus();
			return false;
		}
		if(e.abbr.value.length < 1){
			alert('请填写供应商的 中文简称。');
			e.abbr.focus();
			return false;
		}
		if(e.abbr.value.length > 10){
			alert('供应商的简称不能超过 10 个英文字符。');
			e.abbr.focus();
			return false;
		}
		if(e.address.value.length < 3){
			alert('请填写供应商的 中文地址。');
			e.address.focus();
			return false;
		}
		if(e.address.value.length > 255){
			alert('供应商的中文地址不能超过 125 个中文字符。');
			e.address.focus();
			return false;
		}
		if(e.countryid.value < 1 && e.provinceid.value < 1 && e.cityid.value < 1){
			alert('请填写供应商的所属 国家。');
			e.countryid.focus();
			return false;
		}
		if(e.linkman.value.length < 2){
			alert('请填写供应商的 主联系人。');
			e.linkman.focus();
			return false;
		}
		if(e.linkman.value.length > 100){
			alert('供应商的主联系人不能超过 50 个中文字符。');
			e.linkman.focus();
			return false;
		}
		if(e.position.value.length < 2){
			alert('请填写供应商主联系人的 称谓。');
			e.position.focus();
			return false;
		}
		if(e.telephone.value.length > 50){
			alert('供应商的主联系人不能超过 25 个中文字符。');
			e.position.focus();
			return false;
		}
		if(e.email.value.length < 2){
			alert('请填写供应商的 邮箱。');
			e.email.focus();
			return false;
		}
		if(e.email.value.length > 150){
			alert('供应商的邮箱不能超过 75 个中文字符。');
			e.email.focus();
			return false;
		}
		if(e.telephone.value.length < 2){
			alert('请填写供应商的 电话。');
			e.telephone.focus();
			return false;
		}
		if(e.telephone.value.length > 50){
			alert('供应商的电话不能超过 25 个中文字符。');
			e.telephone.focus();
			return false;
		}
	}
}

dc.showInquiry = function(id){
	$('#'+id).toggle();
}

/**
 * 包装明细计算器
 * @author mayinghao <mayinghao@wisdom.hk>
 * @param id 行数
 * @param type 计算哪个类型
 * @return none
 */
dc.packingCalculator = function(id,type){
	switch(type){
		case 1:
			if($("#perCarton"+id).val()<=0){
				alert('请输入大于0的整数');
				$("#perCarton"+id).focus();
			}else{
				var value=Math.ceil($("#quantity"+id).val()/$("#perCarton"+id).val());
				$("#totalCartons"+id).val(value);
			}
			break;
		case 2:
			if($("#length"+id).val()<=0){
				alert('请输入大于0的整数');
				$("#length"+id).focus();
			}else if($("#width"+id).val()<=0){
				alert('请输入大于0的整数');
				$("#width"+id).focus();
			}else if($("#height"+id).val()<=0){
				alert('请输入大于0的整数');
				$("#height"+id).focus();
			}else{
				if($("#totalCartons"+id).val()<=0){
					alert('请先输入每箱的数量');
					$("#perCarton"+id).focus();
				}else{
					var value1=$("#length"+id).val()*$("#width"+id).val()*$("#height"+id).val()/1000000;
					value1 = value1.toFixed(3);
					var value2=value1*$("#totalCartons"+id).val();
					value2 = value2.toFixed(3);
					$("#perVolume"+id).val(value1);
					$("#totalVolume"+id).val(value2);
				}
			}
			break;
		case 3:
			if($("#perNetWeight"+id).val()<=0){
				alert('请输入大于0的整数');
				$("#perNetWeight"+id).focus();
			}else{
				var value = $("#perNetWeight"+id).val()*$("#quantity"+id).val();
				$("#perNetWeight"+id).val($("#perNetWeight"+id).val());
				value = value.toFixed(3);
				$("#totalNetWeight"+id).val(value);
				$("#totalWeight"+id).val(value);
			}
			break;
		default:
			break;
	}
}

/**
 * 根据产品ID选择产品的属性
 * @author mayinghao <mayinghao@wisdom.hk>
 * @param id 产品ID
 * @param domId 指定DOM的ID接收返回的html
 * @return html
 */
dc.getProductEntitle = function(id,domId){
	var url='/s.php?module=ajax&action=getProductEntitle';
	$.post(url,{'productitemid':id,'selectName':domId},function(data){
		$("#"+domId).html(data);
	});
}

/**
 * 根据产品ID选择产品的属性
 * @author mayinghao <mayinghao@wisdom.hk>
 * @param id 产品ID
 * @param domId 指定DOM的ID接收返回的html
 * @return html
 */
dc.chooseAttribute = function(id,domId){
	var url='/s.php?module=ajax&action=chooseProductAttribute';
	$.post(url,{'productid':id,'selectName':domId},function(data){
		$("#"+domId).html(data);
	});
}

/**
 * 根据客户ID选择客户的公司简称
 * @author mayinghao <mayinghao@wisdom.hk>
 * @param id 客户ID
 * @param domId 指定DOM的ID接收返回的html
 * @return html
 */
dc.returnCountryAndCustomerStr = function(id,domId){
	var url='/s.php?module=ajax&action=returnStr';
	$.post(url,{'customerid':id,'selectName':domId},function(data){
		if(data == 'idNull'){	
			//alert('没有此客户');
		}else if(data == 'cNull'){
			alert('此客户没有简称,请先到客户模块填写此客户的简称');
		}else{
			$("#"+domId).html(data);
			var yearStr = $("#orderNo").val();
			$("#yearStr").text(yearStr);
			var customerStr = $("#customerStr").text();
			var productStr = $("#productStr").text();
			$("#orderTitle").val(yearStr+customerStr+productStr);
			//alert($("#orderTitle").val());
		}
	});
}

var productStrArr = new Array('1');
dc.returnProductStr = function(id,num){
	//alert(id+"==="+productStrArr[id]+"---"+num);
	var productInfo = $("#tditemProductid"+id+"input").val();
	//alert(productInfo);
	if(productInfo==undefined){
		return;
	}
	productStrArr[id] = "+"+num+productInfo;
	var str = '';
	for(var j=1;j<productStrArr.length;j++){
		if(productStrArr[j]!=null){
			str += productStrArr[j];
		}
	}
	$("#productStr").html(str);
	var yearStr = $("#orderNo").val();
	$("#yearStr").text(yearStr);
	var customerStr = $("#customerStr").text();
	var productStr = $("#productStr").text();
	$("#orderTitle").val(yearStr+customerStr+productStr);
	//alert($("#orderTitle").val());
}
dc.material={
	version:function(id,i,m){
		$.get('/s.php?module=ajax&action=materialVersion&m='+m+'&id='+id,
			function(r){
				$('#itemVersionid'+i).html(r);
			}
		)
	}
}
dc.product={
	selectPid:{},
	showParts:function(obj){
		$.getJSON('/s.php?module=ajax&action=showParts',
			function(r){
				$('#partsPackage').html(r.html);
				$('#partsPackage').dialog({
					width:900,
					min_height:300,
					modal:true,
					close: function(){
						$(this).html();
					}
				});
				dc.product.chooserParts(obj);
			}
		)
	},
	chooserParts:function(obj){
		$("#productList input[type='checkbox']").each(function(){
			$(this).click(function(){
				if(dc.product.selectPid['pid['+this.value+']']==undefined && dc.product.selectPid['pid['+this.value+']']!='false'){
					dc.product.selectPid['pid['+this.value+']']=this.value;
				}else{
					delete(dc.product.selectPid['pid['+this.value+']']);
				}console.log(dc.product.selectPid);
				$.post('/s.php?module=ajax&action=chooserParts&obj='+obj,dc.product.selectPid,
				function(r){
					var d=eval('('+r+')');
					$('#partsList').html(d['html']);
				});
			});
		}); 
	},
	submitParts:function(obj){
		var selectPartIds={};
		$("#partsList input[type='checkbox']").each(function(){
			if($(this).attr('checked')){
				selectPartIds['partId['+this.value+']']=this.value;
			}
		});
		$.post('/s.php?module=ajax&action=submitParts',selectPartIds,
			function(r){
				var d=eval('('+r+')');
				$('#'+obj).after(d['html']);
				$('#partsPackage').remove();
		});
	}
}
dc.barcode={
	addServiceNo:function(id,i){  // i:需要的循环数量
		i=i>1?i:1;
		var vid=$('#'+id+' input[name=versionid]').val(),
				count=parseInt($('#'+id+' input[name=count]').val()),
				startno=$('#'+id+' input[name=startno]').val(),
				endno=parseInt($('#'+id+' .endno').text()),
				quantity=parseInt($('#'+id+' input[name=quantity]').val()),
				oid=$('#'+id+' input[name=orderid]').val(),
				itemid=$('#'+id+' input[name=itemid]').val();
		if(startno==''){
			alert("请输入快速服务代码的起始号");
		}else{
			var lost=quantity; // 每次传值后剩余的数量
			if(count>0){
				lost=quantity-count;
			}
			var per,mod=200; // per:每一次传值数量 mod:固定模值
			lost-=(i-1)*mod;  // 计算剩余数
			per=lost>mod?mod:lost; // 获得每次传值数
			i++;
			$('#'+id+' .status').html("<span class=\"loading\"></span>正在生成...");
			$.post('/s.php?module=ajax&action=addServiceNo',{'vid':vid,'oid':oid,'itemid':itemid,'startno':startno,'quantity':quantity,'per':per},function(r){
				var d=eval('('+r+')');
				$('#'+id+' .info').html(d['info']);
				$('#'+id+' .endno').html(d['endno']);
				if(d['complete']!=0){
					$('#'+id+' .status').html(d['status']);
				}
				if(d['complete']>0 && lost>mod){
					dc.barcode.addServiceNo(id,i);
				}
			});
		}
	},
	addBarcode:function(args){ // id:item的id属性，q:quantity，i:需要的循环数量
		i=args.i>1?args.i:1;
		id=args.id;
		var startno=parseInt($('#'+id+' .startno').text()),
				endno=parseInt($('#'+id+' .endno').text()),
				inid=$('#inbound input[name=inboundid]').val(),
				oid=$('#inbound input[name=orderid]').val(),
				bid=$('#'+id+' input[name=barcodeid]').val(),
				vid=$('#'+id+' input[name=versionid]').val(),
				itemid=$('#'+id+' input[name=itemid]').val();
		$('#'+id+' .status').html("<span class=\"loading\"></span>正在生成...");
		if(endno>0){
			startno=endno;
		}
		var per,mod=50,lost=args.q; // per:每一次传值数量 mod:固定模值 lost:每次传值后剩余的数量
		lost-=(i-1)*mod;  // 计算剩余数
		per=lost>mod?mod:lost; // 获得每次传值数
		i++;
		$('#'+id+' .status').html("<span class=\"loading\"></span>正在生成...");
		$.post('/s.php?module=ajax&action=addBarcode',{'vid':vid,'oid':oid,'bid':bid,'inid':inid,'itemid':itemid,'startno':startno,'quantity':args.q,'per':per},function(r){
			var d=eval('('+r+')');
			$('#'+id+' .info').html(d['info']);
			$('#'+id+' .endno').html(d['endno']);
			if(d['complete']!=0){
				$('#'+id+' .status').html(d['status']);
			}
			if(d['complete']>0 && lost>mod){
				args['i']=i;
				dc.barcode.addBarcode(args);
			}
		});
	}
}
//用户选择标准报价单
function toggle(){
   var show = "";
        show = $("input[name='chooseQuoting']:checked").val();
        switch (show){
            case '1':
                document.getElementById("hidden").style.display="none";
                break;
            case '2':
                document.getElementById("hidden").style.display="block";
                break; 
            default:
                document.getElementById("hidden").style.display="none";
                break;                                                              
        }
}
//
dc.showItem = function(id){
	$('#'+id).toggle();
}
//选择调查问卷类型
function showtype(page,tag){
	var i=1;
	var el;
	while(el=document.getElementById(tag+i)){
		if(i==page)
			el.style.display='block';
		else
			el.style.display='none';
		i++;
	}
}

//新建问卷：选择题型切换选项
function onChange(obj)
{
	var value = obj.value;
	if(value==3)
	{
		obj.parentNode.parentNode.childNodes[7].childNodes[0].style.display="none";
		obj.parentNode.parentNode.childNodes[7].childNodes[1].style.display="inline-block";
	}else{
		obj.parentNode.parentNode.childNodes[7].childNodes[0].style.display="";	
		obj.parentNode.parentNode.childNodes[7].childNodes[1].style.display="none";
	}
}

//单击按钮添加答案选项输入框
function addChooer(obj)
{
	var node=document.createElement("span");
	obj.parentNode.appendChild(node);
	obj.parentNode.lastChild.innerHTML="<input id='newinput' /><button id='subnew' onclick='subNew(this)'>提交</button>";		
}
//提交新建的答案选项
function subNew(obj)
{
	var condition = 3;
	var url = window.location.href;
	var itemid = obj.parentNode.parentNode.childNodes[1].childNodes[0].name;	
	var nchooser = obj.parentNode.childNodes[0].value;
	
	if(confirm("请确认添加内容: "+nchooser))
	{
		$.ajax({    
			  type: 'POST',    
			  url: '/s.php?module=ajax&action=updatequestionnaire', 
			  data: "condition="+condition+"&itemid="+itemid+"&nchooser="+nchooser    
			}); 
	}else{
		return false;
	}
	location.reload(); 
}

//单击按钮更新问卷标题
function upTitle(obj)
{
	var condition = 1;
	var url = window.location.href;
	var qid = url.split("questionnaireid=")[1];
	var ntitle = obj.parentNode.lastChild.value;
	
	if(confirm("请确认修改内容: "+ntitle))
	{
		$.ajax({    
			  type: 'POST',    
			  url: '/s.php?module=ajax&action=updatequestionnaire', 
			  data: "condition="+condition+"&qid="+qid+"&ntitle="+ntitle    
			}); 		
	}
}

//单击按钮更新客户类型
function upRela(obj)
{
	var condition = 2;
	var url = window.location.href;
	var qid = url.split("questionnaireid=")[1];
	var nrelation = obj.parentNode.lastChild.value;
	
	if(confirm("是否确认修改？"))
	{
		$.ajax({    
			  type: 'POST',    
			  url: '/s.php?module=ajax&action=updatequestionnaire', 
			  data: "condition="+condition+"&qid="+qid+"&nrelation="+nrelation    
			}); 		
	}else{
		return false;
	}
}

//单击按钮更新问题
function upQuestion(obj)
{
	var condition = 5;
	var nquestion = obj.parentNode.lastChild.value;
	var itemid = obj.parentNode.lastChild.name;
	if(confirm("请确认修改内容: "+nquestion))
	{

		$.ajax({    
			  type: 'POST',    
			  url: '/s.php?module=ajax&action=updatequestionnaire', 
			  data: "condition="+condition+"&nquestion="+nquestion+"&itemid="+itemid    
			}); 	
	}else{
		return false;
	}

}


