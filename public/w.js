dc.login={
	show: function(r){
		if(dc.win.handle['login'] == null){
			dc.win.creat('login', 300, 260);
		}else{
			dc.win.resize('login', 300, 260);
		}
		if(dc.login.handle == null){
			dc.login.handle=document.createElement("div");
			dc.login.handle.id='login';
			dc.login.handle.innerHTML='Loading...';
			dc.ajax.addQueue('GET', 'login', function(){dc.login.handle.innerHTML=dc.ajax.handle.responseText;dc.win.handle['login'].appendChild(dc.login.handle);if(dc.$('loginUsername')!=null){dc.$('winLoginUsername').value=dc.$('loginUsername').value};dc.login.focus()});
		}
		var w=dc.win.show('login');
		return false;
	},
	refresh:function(){
		dc.ajax.addQueue('GET', 'memberzone', function(){dc.$('member').innerHTML=dc.ajax.handle.responseText;});
	},
	logout:function(){
		if(dc.win.handle['login'] != null){
			var s=dc.$("loginSubmit"), r=dc.$("loginReset");
			s.disabled=r.disabled=false;
		}
		dc.ajax.addQueue('GET', 'logout', function(){dc.$('member').innerHTML=dc.ajax.handle.responseText;});
	},
	check:function(){
		var username = dc.$('winLoginUsername');
		var password = dc.$('winLoginPassword');
		var validate = dc.$('winLoginValidate');
		if(username.value.length < 2){
			alert("您输入的 用户名 有误。");
			username.focus();
			return false;
		}
		if(password.value.length < 6 || password.value.length > 16){
			alert("您输入的 密码 有误。");
			password.focus();
			return false;
		}
		if(validate.value.length != 6){
			alert("请输入正确的 验证码。");
			validate.focus();
			return false;
		}
	},
	login:function(){
		var username = dc.$('winLoginUsername');
		var password = dc.$('winLoginPassword');
		var validate = dc.$('winLoginValidate');
		if(username.value.length < 2){
			alert("您输入的 用户名 有误。");
			username.focus();
			return false;
		}
		if(password.value.length < 6 || password.value.length > 16){
			alert("您输入的 密码 有误。");
			password.focus();
			return false;
		}
		if(validate.value.length != 6){
			alert("请输入正确的 验证码。");
			validate.focus();
			return false;
		}
		var p = new Array();
		p.push('username='+username.value, 'password='+password.value, 'validate='+validate.value);
		dc.ajax.addQueue('POST', 'dologin', function(){
			password.value='';
			validate.value='';
			var s=dc.$("loginSubmit"), r=dc.$("loginReset");
			s.disabled=r.disabled=true;
			if(dc.ajax.handle.responseText=='true'){
				dc.win.close('login');
				dc.login.refresh();
			}else{
				alert(dc.ajax.handle.responseText);
				dc.login.changeValidate('winLoginValidateImage');
				s.disabled=r.disabled=false;
			}
		}, p.join('&'));
		var l = window.location;
		if(l.href.indexOf('order') > 0 || l.href.indexOf('lostpwd') > 0){
			window.refresh();
		}
	},
	focus:function(){
		var username = dc.$('winLoginUsername');
		var password = dc.$('winLoginPassword');
		dc.$('winLoginValidate').value = '';
		if(username.value == ""){
			username.focus();
		}else{
			password.focus();
		}
	},
	showValidate:function(e){
		var img=dc.$(e);
		if(img.src == ''){
			img.src="/?module=login&action=validate";
			img.style.display="";
		}
	},
	changeValidate: function(e){
		var img=dc.$(e);
		if(img.src == ''){
			img.src="/?module=login&action=validate";
		}else{
			img.src="/?module=login&action=validate&refresh=" + Math.random();
		}
	}
}
dc.register={
	show: function(){
		if(dc.win.handle['register'] == null){
			dc.win.creat('register', 350, 320);
		}else{
			dc.win.resize('register', 350, 320);
		}
		if(dc.register.handle == null){
			dc.register.handle = document.createElement("div");
			dc.register.handle.id = 'register';
			dc.register.handle.innerHTML = 'Loading...';
			var un=dc.$('regUsername');
			var em=dc.$('regEmail');
			var rd=dc.$('regRedirect');
			var url='';
			if(un != null){
				url='&username='+un.value;
			}
			if(em != null){
				url+='&email='+em.value;
			}
			if(rd != null){
				url+='&redirect='+rd.value;
			}
			dc.ajax.addQueue('GET', 'register'+url, function(){dc.register.handle.innerHTML = dc.ajax.handle.responseText;dc.win.handle['register'].appendChild(dc.register.handle);if(dc.$('regUsername')!=null){dc.$('winRegUsername').value=dc.$('regUsername').value;}if(dc.$('regEmail')!=null){dc.$('winRegEmail').value=dc.$('regEmail').value};dc.register.focus()});
		}
		dc.win.show('register');
		return false;
	},
	check:function(obj){
		var reguUser = "^([a-zA-Z0-9]+[_0-9a-zA-Z]{2,15})$";
		var reguEmail = "^(([0-9a-zA-Z]+)|([0-9a-zA-Z]+[_.0-9a-zA-Z-]*[0-9a-zA-Z]+))@([a-zA-Z0-9-]+[.])+([a-zA-Z]{2,3})$";
		var reUser = new RegExp(reguUser);
		var reEmail = new RegExp(reguEmail);
		var terms = dc.$("winRegTerms");
		var protocol = dc.$("winRegProtocol");
		var username = dc.$("winRegUsername");
		var password = dc.$("winRegPassword");
		var repassword = dc.$("winRegRepassword");
		var email = dc.$("winRegEmail");
		var validate = dc.$("winRegValidate");
		if(protocol.checked == false){
			terms.style.border = "1px solid red";
			alert("注册会员必需同意注册条款内容，请选中复选框。");
			terms.style.border = "0";
			return false;
		}
		if(username.value.length == 0){
			alert("请输入您希望注册的 用户名。");
			username.focus();
			return false;
		}
		if(username.value.search(reUser) == -1) {
			alert("您输入的用户名不正确。注释: 用户名只能使用大小写英文字母、数字以及下划线\"_\"。");
			username.focus();
			return false;
		}
		if(email.value.length == 0){
			alert("请输入您的 电子邮箱。");
			email.focus();
			return false;
		}
		if(email.length > 100){
			alert("电子邮箱错误, 请检查。");
			email.focus();
			return false;
		}
		if(email.value.search(reEmail) == -1){
			alert("电子邮箱错误。");
			email.focus();
			return false;
		}
		if(password.value.length==0){
			alert("输入您的新密码。");
			password.focus();
			return false;
		}
		if(password.value.length < 6 || password.value.length > 16){
			alert("密码字符位数为 6 - 32 位字母、数字和英文符号。");
			password.focus();
			return false;
		}
		if(password.value == username.value) {
				alert("密码不能与您的用户名相同。");
			password.focus();
			return false;
		}
		if(password.value != repassword.value){
			alert("重输的新密码必需和前面输入的相同。");
			repassword.focus();
			return false;
		}
		if(validate.value.length==0){
			alert("请输入图片中的验证码。注释: 验证码为6位数字与字母的组合，且不区分大小写。");
			validate.focus();
			return false;
		}
		if(validate.value.length != 6){
			alert("验证码无效。 注释: 验证码为6位数字与字母的组合，且不区分大小写。");
			validate.focus();
			return false;
		}
	},
	focus:function(){
		dc.$("winRegValidate").value='';
		if(dc.$("winRegUsername").value == ''){
			dc.$("winRegUsername").focus();
		}else if(dc.$("winRegEmail").value == ''){
			dc.$("winRegEmail").focus();
		}else{
			dc.$("winRegPassword").focus();
		}
	}
}

dc.comment = {
	handle: null,
	replyHandle: null,
	t: '',
	i: 0,
	r: 0,
	form:function(t,i,a){
		this.t = t;
		this.i = i;
		if(dc.win.handle['comment'] == null){
			dc.win.creat('comment', 450, 240);
		}else{
			dc.win.resize('comment', 450, 240);
		}
		if(dc.comment.handle == null){
			dc.comment.handle = document.createElement("div");
			dc.comment.handle.id = 'commentForm';
			dc.comment.handle.innerHTML = '正在读取...';
			a=1;
		}
		if(a==1){
			dc.ajax.addQueue('GET', 'comment&type='+t+'&id='+i, function(){dc.comment.handle.innerHTML = dc.ajax.handle.responseText;dc.win.handle['comment'].appendChild(dc.comment.handle)});
		}
		dc.win.show('comment');
	},
	reply:function(t,i,p,a){
		this.t = t;
		this.i = i;
		if(dc.win.handle['commentReply'] == null){
			dc.win.creat('commentReply', 450, 240);
		}else{
			dc.win.resize('commentReply', 450, 240);
		}
		if(dc.comment.replyHandle == null){
			dc.comment.replyHandle = document.createElement("div");
			dc.comment.replyHandle.id = 'commentReplyForm';
			dc.comment.replyHandle.innerHTML = '正在读取...';
			a=1;
		}
		if(a==1 || this.r != p){
			this.r = p;
			dc.ajax.addQueue('GET', 'commentReply&type='+t+'&id='+i+'&p='+p, function(){dc.comment.replyHandle.innerHTML = dc.ajax.handle.responseText;dc.win.handle['commentReply'].appendChild(dc.comment.replyHandle)});
		}
		dc.win.show('commentReply');
	},
	check: function(){
		var cName = dc.$("commentName");
		if(cName.value.length < 2){
			alert("请填写您的名字。");
			cName.focus();
			return false;
		}
		var cText = dc.$("commentText");
		if(cText.value.length < 4){
			alert("评论内容至少需要5个字。");
			cText.focus();
			return false;
		}
		return true;
	},
	post: function(){
		if(this.check()){
			dc.$("commentSubmit").disabled = true;
			dc.$("commentReset").disabled = true;
			var p = new Array();
			p.push('type='+this.t, 'id='+this.i, 'website='+dc.$("commentWebsite").value, 'name='+escape(dc.$("commentName").value), 'ctext='+escape(dc.$("commentText").value));
			dc.ajax.addQueue('POST', 'commentInsert', function(){dc.$("commentText").value='';dc.$("commentSubmit").disabled=false;dc.$("commentReset").disabled=false;dc.comment.handle.innerHTML = dc.ajax.handle.responseText;dc.comment.show(dc.comment.t, dc.comment.i)}, p.join('&'));
		}
		return false;
	},
	replyPost: function(){
		if(this.check()){
			dc.$("commentSubmit").disabled = true;
			dc.$("commentReset").disabled = true;
			var p = new Array();
			p.push('type='+this.t, 'id='+this.i, 'website='+dc.$("commentWebsite").value, 'name='+escape(dc.$("commentName").value), 'ctext='+escape(dc.$("commentText").value), 'parentid='+dc.$('commentParentid').value);
			dc.ajax.addQueue('POST', 'commentInsert', function(){dc.$("commentText").value='';dc.$("commentSubmit").disabled=false;dc.$("commentReset").disabled=false;dc.comment.replyHandle.innerHTML = dc.ajax.handle.responseText;dc.comment.show(dc.comment.t, dc.comment.i)}, p.join('&'));
		}
		return false;
	},
	show:function(t,i){
		if(t!='product' && t!='news'){
			return;
		}
		dc.ajax.addQueue('GET', 'commentList&type='+t+'&id='+i, function(){dc.$('commentList').innerHTML=dc.ajax.handle.responseText;});
	},
	reset:function(){
		dc.$("commentName").value='';
		dc.$("commentWebsite").value='';
		dc.$("commentText").value='';
	}
}

dc.forward = {
	handle: null,
	t: '',
	i: 0,
	form:function(t,i){
		this.t = t;
		this.i = i;
		if(dc.win.handle['forward'] == null){
			dc.win.creat('forward', 550, 370);
		}else{
			dc.win.resize('forward', 550, 370);
		}
		if(dc.forward.handle == null){
			dc.forward.handle = document.createElement("div");
			dc.forward.handle.id = 'forwardForm';
			dc.forward.handle.innerHTML = '正在读取...';
		}
		dc.ajax.addQueue('GET', 'forward&type='+t+'&id='+i, function(){dc.forward.handle.innerHTML = dc.ajax.handle.responseText;dc.win.handle['forward'].appendChild(dc.forward.handle)});
		dc.win.show('forward');
	},
	check: function(){
		var fromEmail = dc.$('fromEmail');
		if(fromEmail.value.length < 7){
			alert("请填写您的邮箱地址。");
			fromEmail.focus();
			return false;
		}
		var toEmail = dc.$('toEmail');
		if(toEmail.value.length < 7){
			alert("请填写您想发往的邮箱地址。");
			toEmail.focus();
			return false;
		}
		var subject = dc.$('subject');
		if(subject.value.length < 4){
			alert("请输入邮件标题。(至少2个字)");
			subject.focus();
			return false;
		}
		var message = dc.$('message');
		if(message.value.length < 2){
			alert("请输入邮件内容。(至少5个字)");
			message.focus();
			return false;
		}
		return true;
	},
	post: function(){
		if(this.check()){
			dc.$("forwardSubmit").disabled = true;
			dc.$("forwardReset").disabled = true;
			var p = new Array();
			p.push('type='+this.t, 'id='+this.i, 'fromName='+escape(dc.$("fromName").value), 'fromEmail='+escape(dc.$("fromEmail").value), 'toName='+escape(dc.$("toName").value), 'toEmail='+escape(dc.$("toEmail").value), 'subject='+escape(dc.$("subject").value), 'message='+escape(dc.$("message").value));
			dc.ajax.addQueue('POST', 'forwardInsert', function(){dc.$("forwardSubmit").disabled=false;dc.$("forwardReset").disabled=false;dc.$("toName").value='';dc.$("toEmail").value='';dc.forward.handle.innerHTML = dc.ajax.handle.responseText;}, p.join('&'));
		}
		return false;
	},
	reset:function(){
		dc.$("toName").value='';
		dc.$("toEmail").value='';
		dc.$("fromName").value='';
		dc.$("fromEmail").value='';
		dc.$("subject").value='';
	}
}
dc.search ={
	quick:function(s){
		if(s.q3.value.length < 2){
			alert("请输入您想搜索的关键字。(内容不能少于2个字符)");
			s.q3.focus();
			return false;
		}
		dc.$('sbtn').disabled=true;
		s.submit();
	},
	all:function(s){
		s.sbtn.disabled=true;
		s.submit();
	}
}
dc.help = function(id, c){
	var pid=document.getElementById('p' + id);
	var cid=document.getElementById('c' + id);
	if(cid.getAttribute("state") == 1){
		cid.className = cid.className.replace(' show', '') + ' hidden';
		cid.setAttribute("state", 0);
		pid.className = 'plus';
//		c.className = c.className.replace(' selected', '');
	}else{
		if(cid.childNodes.length == 0){
			dc.ajax.addQueue('GET', 'help&id=' + id, function(){cid.innerHTML = dc.ajax.handle.responseText});
		}
		cid.className = cid.className.replace(' hidden', '') + ' show';
		cid.setAttribute("state", 1);
		pid.className = 'minus';
//		c.className += ' selected';
	}
};
dc.category = function(id, o, d){
	var pid=document.getElementById('p' + id);
	var cid=document.getElementById('c' + id);
	if(cid.getAttribute("state") == 1){
		cid.className = cid.className.replace(' show', '') + ' hidden';
		cid.setAttribute("state", 0);
		pid.className = 'cplus';
	}else{
		if(cid.childNodes.length == 0){
			dc.ajax.addQueue('GET', 'category&id='+id+'&orderby='+o+'&direction='+d, function(){cid.innerHTML = dc.ajax.handle.responseText});
		}
		cid.className = cid.className.replace(' hidden', '') + ' show';
		cid.setAttribute("state", 1);
		pid.className = 'cminus';
	}
};
dc.clip={
	init:function(id){
		var t = dc.$(id);
		var tp = dc.getElementPos(t);
		var f = '<div id="clipf_'+id+'" style="position:absolute;left:'+tp.x+'px;top:'+tp.y+'px;width:'+t.offsetWidth+'px;height:'+t.offsetHeight+'px;z-index:99">' + this.getHtml(id, t.offsetWidth, t.offsetHeight) + '</div>';
		t.innerHTML += f;
		t.onmouseover=function(){
			var m = dc.$('clip_'+this.id);
			m.setHandCursor(true);
			m.setText(this.getAttribute('rel'));
		};
	},
	callback:function(id, e, a){
		if(e == 'complete'){
			alert("已将本页地址复制到剪切板，您可以直接粘贴在QQ、旺旺、MSN等处。\n\n已复制【"+a+"】");
		}
	},
	getHtml:function(id, w, h){
		w = w>0?w:10;
		h = h>0?h:10;
		var flashvars = 'id=' + id + '&width=' + w + '&height=' + h;

		if(dc.getBrowser() == 'IE') {
			return '<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="'+(location.href.match(/^https/i) ? 'https://' : 'http://')+'download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,0,0" width="'+w+'" height="'+h+'" id="clip_'+id+'" align="middle"><param name="allowScriptAccess" value="always" /><param name="allowFullScreen" value="false" /><param name="movie" value="/public/dcClip.swf" /><param name="loop" value="false" /><param name="menu" value="false" /><param name="quality" value="best" /><param name="bgcolor" value="#ffffff" /><param name="flashvars" value="'+flashvars+'"/><param name="wmode" value="transparent"/></object>';
		}else{
			return '<embed id="clip_'+id+'" src="/public/dcClip.swf" loop="false" menu="false" quality="best" bgcolor="#ffffff" width="'+w+'" height="'+h+'" name="clip_'+id+'" align="middle" allowScriptAccess="always" allowFullScreen="false" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" flashvars="'+flashvars+'" wmode="transparent" />';
		}
	}
}
dc.rating={
	s:function(id,a,t){
		if(t == 2){
			t = 'news'
		}else if(t == 3){
			t = 'comment';
		}else{
			t = 'product';
		}
		dc.ajax.addQueue('GET', 'rating&t='+t+'&id='+id+'&r='+a, function(){dc.$('rating'+id).innerHTML = dc.ajax.handle.responseText;dc.cookies.set(t+id,1,8640000);});
	}
}
dc.favorite={
	s:function(id,t){
		if(t == 2){
			t = 'news'
		}else{
			t = 'product';
		}
		dc.ajax.addQueue('GET', 'favorite&t='+t+'&id='+id, function(){dc.$('favorite'+id).innerHTML = dc.ajax.handle.responseText;});
	}
}
dc.product={
	zoom:new Array(),
	color:function(id){
		var pcolorid = dc.$('pcolorid');
		if(pcolorid.value != id){
			var orgColor = dc.$('color'+pcolorid.value);
			orgColor.className = orgColor.className.replace(' on', '');//去除标记
			dc.$('color'+id).className += ' on'; //添加标记
			dc.$('currentColor').innerHTML = dc.$('color'+id).innerHTML;
			pcolorid.value = id;
			if(this.zoom[id] == undefined){
				dc.ajax.addQueue('GET', 'color&id='+id, function(){
					var pshow = dc.$('pshow');
					if(dc.ajax.handle.responseText==''){
						dc.product.zoom[dc.$('pcolorid').value] = '';
						pshow.innerHTML ='<img src="/public/images/nophoto.gif" width="320" height="320" />';
					}else{
						dc.product.zoom[dc.$('pcolorid').value] = pshow.innerHTML = dc.ajax.handle.responseText;
						DCZoomPlus.refresh();
					}
				});
			}else{
				var pshow = dc.$('pshow');
				if(this.zoom[id] == ''){
					pshow.innerHTML ='<img src="/public/images/nophoto.gif" width="320" height="320" />';
				}else{
					pshow.innerHTML = this.zoom[id];
					DCZoomPlus.refresh();
				}
			}
		}
	},
	size:function(id){
		var psizeid = dc.$('psizeid');
		if(psizeid.value != id){
			var orgSize = dc.$('size'+psizeid.value);
			orgSize.className = orgSize.className.replace(' on', '');//去除标记
			psizeid.value = id;
			var csizeid = dc.$('size'+id);
			dc.$('currentSize').innerHTML = csizeid.innerHTML;
			csizeid.className += ' on'; //添加标记
		}
	}
}
dc.cartPad={
	changeQty:function(id, m){
		var e=dc.$('qty'+id), o = parseInt(e.value);v=isNaN(o)?1:o;
		if(m=='-'){
			v--;
			if(v==0){
				if(confirm('将数量减为 0 意味着删除这个商品，您确定不购买该商品吗？')){
					this.remove(id);
					return;
				}else{
					v=1;
				}
			}
		}
		if(m=='+'){
			v++;
		}
		if(o != v || m=='*'){
			e.value=v;
			dc.ajax.addQueue('GET', 'cartPadQty&id='+id+'&qty='+v, function(){if(dc.ajax.handle.responseText == 'true'){dc.cartPad.total()}});
		}
	},
	total:function(){
		dc.ajax.addQueue('GET', 'cartPadTotal', function(){
			var tr = dc.$('cartTotal');
			var ntd=dc.ajax.handle.responseText.split('|');
			for(var i=1; i<tr.cells.length; i++){
				tr.cells[i].innerHTML=ntd[(i-1)];
			}
		});
	},
	remove:function(id){
		dc.ajax.addQueue('GET', 'cartPadRemove&id='+id, function(){if(dc.ajax.handle.responseText==0){dc.cartPad.empty()}else{dc.$('item'+id).style.display='none';dc.cartPad.total()};});
	},
	empty:function(){
		dc.ajax.addQueue('GET', 'cartPadEmpty', function(){dc.$('cartPad').innerHTML=dc.ajax.handle.responseText;});
	}
}
dc.cart={
	wrap:function(){
		window.onresize = this.show;
		dc.cart.show();
	},
	show:function(){
		var c = dc.$('cart');
		c.style.left = dc.getElementPos(dc.$('wrap')).x + 993 + 'px';
		if(c.style.display != 'block'){
			c.style.display = 'block';
		}
		dc.cart.get('cart');
	},
	add:function(i){
		i = parseInt(i);
		if(i > 0){
			if(dc.$('pcolorid') != null){
				var colorid = dc.$('pcolorid').value;
			}
			if(dc.$('psizeid') != null){
				var sizeid = dc.$('psizeid').value;
			}
			dc.cart.get('cartAdd&id='+i+'&colorid='+colorid+'&sizeid='+sizeid);
		}
	},
	remove:function(i){
		i = parseInt(i);
		if(i > 0){
			dc.cart.get('cartRemove&id='+i);
		}
	},
	empty:function(){
		dc.cart.get('cartEmpty');
	},
	get:function(url){
		dc.ajax.addQueue('GET', url, function(){dc.$('cart').innerHTML = dc.ajax.handle.responseText;});
	}
}
dc.member={
	updateCheck:function(e){
		var reguUser = "^([a-zA-Z]+[_0-9a-zA-Z]{2,15})$";
		var reguEmail = "^(([0-9a-zA-Z]+)|([0-9a-zA-Z]+[_.0-9a-zA-Z-]*[0-9a-zA-Z]+))@([a-zA-Z0-9-]+[.])+([a-zA-Z]{2,3})$";
		var reUser = new RegExp(reguUser);
		var reEmail = new RegExp(reguEmail);
		if(e.realname.value.length < 2) {
			alert("请输入您的真实姓名！\n\n注: 真实名字数不能少于2个字。");
			e.realname.focus();
			return false;
		}
		if(e.email.value.length == 0){
			alert("请输入电子邮箱！");
			e.email.focus();
			return false;
		}
		if(e.email.length > 100){
			alert("您输入的电子邮箱不正确，请检查，以重新输入！");
			e.email.focus();
			return false;
		}
		if(e.email.value.search(reEmail) == -1){
			alert("电子邮箱不正确，请重新输入！");
			e.email.focus();
			return false;
		}
		e.submit.disabled = true;
	},
	pwdCheck:function(e){
		if(e.oldpassword.value.length==0){
			alert("请输入您的 原密码！\n\n(密码位数不能小于 6 位，同时也不能大于 16 位)");
			e.oldpassword.focus();
			return false;
		}
		if(e.oldpassword.value.length<6 || e.oldpassword.value.length>16){
			alert("您输入的 原密码 不符合要求，请重新输入！\n\n(密码位数不能小于 6 位，同时也不能大于 16 位)");
			e.oldpassword.focus();
			return false;
		}
		if(e.password.value.length==0){
			alert("请输入您欲设定的 新密码！\n\n(密码位数不能小于 6 位，同时也不能大于 16 位)");
			e.password.focus();
			return false;
		}
		if(e.password.value.length<6 || e.password.value.length>16){
			alert("您欲设定的新密码不符合要求，请重新输入！\n\n(密码位数不能小于 6 位，同时也不能大于 16 位)");
			e.password.focus();
			return false;
		}
		if(e.repassword.value.length==0){
			alert("请您重复输入您欲设定的 新密码！");
			e.repassword.focus();
			return false;
		}
		if(e.password.value!=e.repassword.value){
			alert("重复输入的 新密码 不正确，请重新输入！\n\n(密码位数不能小于 6 位，同时也不能大于 16 位)");
			e.repassword.focus();
			return false;
		}
		if(e.validate.value.length==0){
			alert("请输入验证码！");
			e.validate.focus();
			return false;
		}
		if(e.validate.value.length != 6){
			alert("您输入的验证码位数不正确！\n\n注: 验证码位数为 6 位。");
			e.validate.focus();
			return false;
		}
		e.submit.disabled = true;
	},
	addressCheck:function(e){
		if(e.pcode.value == ''){
			alert("请选择新地址中的 省.");
			e.pcode.focus();
			return false;
		}
		if(e.ccode.value == ''){
			alert("请选择新地址中的 市.");
			e.ccode.focus();
			return false;
		}
		if(e.address.value == ''){
			alert("请输入您的 详细地址.");
			e.address.focus();
			return false;
		}
		if(e.postalcode.value == ''){
			alert("请输入您的 邮政编码.");
			e.postalcode.focus();
			return false;
		}
		if(e.receiver.value == ''){
			alert("请输入您的 联系人.");
			e.receiver.focus();
			return false;
		}
		if(e.mobile.value == ''){
			alert("请输入您的手机号码，如您填写固定电话请写明区号。");
			e.mobile.focus();
			return false;
		}
		if(e.email.value.length == 0){
			alert("请输入电子邮箱！");
			e.email.focus();
			return false;
		}
		if(e.email.length > 100){
			alert("您输入的电子邮箱不正确，请检查，以重新输入！");
			e.email.focus();
			return false;
		}
		if(e.email.value.search(reEmail) == -1){
			alert("电子邮箱不正确，请重新输入！");
			e.email.focus();
			return false;
		}
		e.submit.disabled = true;
	},
	feedbackCheck:function(e){
		var categoryValue = null;
		if(e.subject.value == ''){
			alert("请输入您要发表的帖子标题。");
			e.subject.focus();
			return false;
		}
		if(e.category.value == 0){
			alert("请选择帖子类型。");
			e.category.focus();
			return false;
		}
		if(e.category.length){
			for(var i = 0; i < e.category.length; i++)
				if(e.category[i].checked)
					categoryValue = e.category[i].value;
		}else if(e.category.checked){
			categoryValue = e.category.value;
		}else if(e.category.value){
			categoryValue = e.category.value;
		}
		if(categoryValue == null){
			alert("请选择帖子的类型。");
			return false;
		}
		if(e.ptext.value.length < 2){
			alert("正文内容不能少于2个字符。");
			e.ptext.focus();
			return false;
		}
		e.submit.disabled = true;
	},
	replyFDCheck:function(e){
		if(e.subject.value == ''){
			alert("请输入您要发表的帖子标题。");
			e.subject.focus();
			return false;
		}
		if(e.ptext.value.length < 2){
			alert("正文内容不能少于2个字符。");
			e.ptext.focus();
			return false;
		}
		e.submit.disabled = true;
	}
};