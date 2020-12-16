function dynamicForm(){
	var debugg = function(){
		return eval($(this)
		.gdt('formula')
		.replace(/this/gi,$.format($(this).is('input,check,textarea,radio') ? $(this).val() : $(this).text()))
		.replace(/\<([a-zA-Z0-9.#_-]+)\>/g,"$.format($('$1').text())")
		.replace(/inner\(([a-zA-Z0-9.#_-]+)\)/g,"$.format($('$1').text())")
		.replace(/\[([a-zA-Z0-9.#_-]+)\]/g,"$('$1').val()")
		.replace(/val\(([a-zA-Z0-9.#_-]+)\)/g,"$('$1').val()"));
	};
	$($(this).gdt('exchange'))
	.format(debugg.call(this))
	.trigger('change');
}


$(document)
.on('change keyup', '[data-formula]', function(event) {
	event.stopPropagation();
	var debugg = function(){
		return eval($(this)
		.gdt('formula')
		.replace(/this/gi,$.format($(this).is('input,check,textarea,radio') ? $(this).val() : $(this).text()))
		.replace(/\<([a-zA-Z0-9.#_-]+)\>/g,"$.format($('$1').text())")
		.replace(/inner\(([a-zA-Z0-9.#_-]+)\)/g,"$.format($('$1').text())")
		.replace(/\[([a-zA-Z0-9.#_-]+)\]/g,"$('$1').val()")
		.replace(/val\(([a-zA-Z0-9.#_-]+)\)/g,"$('$1').val()"));
	};
	$($(this).gdt('exchange'))
	.format(debugg.call(this))
	.trigger('change');
})
.on('click focus', '[numbered]', function(event) {
	event.stopPropagation(),
	$(this).select();
})
.on('focus','input[placeholder]:not(.no-placeholder)',function(event) {
	event.preventDefault();
	if (!$(this).next('.label')[0]) {
		$(this)
		.toggleClass('not-empty',($(this).val().trim().length>0))
		.after('<span class="label">'+$(this).attr('placeholder')+'</span>')
		.attr('arc-placeholder',$(this).attr('placeholder'))
		.removeAttr('placeholder');
	}
})
.on('keyup keydown keypress','[arc-placeholder]', function(event) {
	$(this).toggleClass('not-empty',($(this).val().trim().length>0));
})
.on('click', '[data-toggle="collapse"]', function(event) {
	event.preventDefault();
	let btn_ = $(this);
	let _target = $(btn_.gdt('target',btn_.attr('href')));
	new Promise(async function(s){
		await _target.trigger('arc.collapse.before',[btn_]);
		return s();
	})
	.then(async function(){
		await _target.slideToggle('fast',async function(){
			await _target
			.trigger('arc.collapse.after',[btn_])
			.trigger('arc.collapse',[btn_])
			.toggleClass('is-active', $(this).is(':visible'));
		});
	});

})
.on('click', '.modal-background, .modal-close,.delete,.close', async function(event) {
	$(this).parents('.modal').modal($(this).is('.delete') ? 'remove' : 'hide');
	$(this).parents('.notification').alert(false);
})
.on('click', '[data-toggle="modal"][href],[data-toggle="modal"][data-target]', async function(event) {await  $(($(this).attr('href') ? $(this).attr('href') : $(this).attr('data-target'))).modal('show'); });


$(function() {
	xhrPool = [];
	$.ajaxPrefilter(function(options, originalOptions, jqXHR) {
		filter = (typeof filter !='object') ? [] : filter;
		var filterIndex = filter.length;
		filter[filterIndex] = {
			ajaxSync : jqXHR,
			options : options,
		};
		try {
			filter[filterIndex].options.data = filter[filterIndex].options.type.toLowerCase()=='get' ? jsontourl($.extend(
				true,
				strtojson(filter[filterIndex].options.url),
				strtojson('?'+filter[filterIndex].options.data),
			)) : filter[filterIndex].options.data;
			filter[filterIndex].cacheSettings = $.extend(true, {
				cache:(typeof filter[filterIndex].options.cache=='object') ? true : cn(filter[filterIndex].options.cache,'boolean',false),
				key: cn(filter[filterIndex].options.keyCache,'undefined','cacheHTTP:'+(filter[filterIndex].options.url.split('?')[0].split('#')[0]+'?'+filter[filterIndex].options.data).trim(),'!'),
				expire:60,
			}, isJson(filter[filterIndex].options.cache));
			if (filter[filterIndex].cacheSettings.cache&&(filter[filterIndex].cacheContent=$.cache(filter[filterIndex].cacheSettings.key))) {
				return console.log("Fetch Cache: ",filter[filterIndex].options.url),
				filter[filterIndex].ajaxSync.abort(),
				filter[filterIndex].options.success(filter[filterIndex].cacheContent,'OK',filter[filterIndex].ajaxSync);
			}
			else if(filter[filterIndex].cacheSettings.cache){
				filter[filterIndex].subOptions = typeof filter[filterIndex].options.success != 'function' ? function(){} : filter[filterIndex].options.success;
				filter[filterIndex].options.success = function ($tmp_fn_a,$tmp_fn_b,$tmp_fn_c,$tmp_fn_d) {
					$.cache(filter[filterIndex].cacheSettings.key,$tmp_fn_a,filter[filterIndex].cacheSettings.expire);
					return filter[filterIndex].subOptions($tmp_fn_a,$tmp_fn_b,$tmp_fn_c,$tmp_fn_d);
				};
			}
		} catch(e) {console.error(e);}
	});
	$(document)
	.ajaxSend(function(event, jqXHR, options) {
		xhrPool.push(jqXHR);
	})
	.ajaxComplete(function(event, jqXHR, options) {
		xhrPool = $.grep(xhrPool, function(x) {return x != jqXHR;});
	});
	$.ajaxAbort = function() {
		$.each(xhrPool, function(idx, jqXHR) {
			jqXHR.abort();
		});
	};
	var oldbeforeunload = window.onbeforeunload;
	window.onbeforeunload = function() {
		$.ajaxAbort();
		if(oldbeforeunload) return oldbeforeunload();
	}
});


function cn(basic,type,optional,condition='='){return (condition=='=') ? ((typeof basic==type||basic==type) ? basic : optional ) : ((typeof basic!=type) ? basic : optional ); }
function strtojson(url=undefined) {
  url = (typeof url!='string'||(url.indexOf('?')<0&&url.indexOf('#')<0)) ? '?#' : url;
  var url_data = url.match(/\?([^#]*)/i)[1];          // gets the string between '?' and '#'
  var ar_url_data = url_data.split('&');
  var data_url = {};
  for(var i=0; i<ar_url_data.length; i++) {
    var ar_val = ar_url_data[i].split('=');           // separate name and value from each pair
    if ((typeof ar_val[0]=='string'||typeof ar_val[0] =='number')&&ar_val[0].length>0) data_url[ar_val[0]] = ar_val[1];
  }
  return data_url;
}
function jsontourl(json){
	json = isJson(json);
	return JSON
	.stringify(json)
	.replace(/(\{?\}?\"?)/gi,'')
	.replace(/\:/gi,'=')
	.replace(/\,/gi,'&');
}

function getId(count=1,list=[]) {
	var count = (typeof count=='number') ? parseInt(count) : 1;
	var list = typeof list=='object' ? list : [];
	id = function(list=[]){
		var rand = Math.floor((Math.random() * 895645) + 1);
		return (!document.getElementById(rand)&&list.indexOf(rand)<0) ? rand : id();
	}
	for (var i=0; i < count; i++) {
		list.push(id(list));
	}
	return list.length==1 ? list[0] : list;
}
function ucwords(str) {return (str + '').replace(/^([a-z\u00E0-\u00FC])|\s+([a-z\u00E0-\u00FC])/g, function($1) {return $1.toUpperCase() }) }
function ucfirst(str) {return str.charAt(0).toUpperCase()+str.substring(1).toLowerCase();}
function $_GET(variable) {variable = variable.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]"); var regex = new RegExp("[\\?&]" + variable + "=([^&#]*)"), results = regex.exec(location.search); return results === null ? undefined : decodeURIComponent(results[1].replace(/\+/g, " ")); }
function empty(variable=false){
	return (variable==null||typeof variable=='undefined')||
				(typeof variable=='boolean'&&variable===false)||
					(typeof variable=='object'&&variable.length<1)||
						(typeof variable=='string'&&(variable.trim().length<1))||
							(typeof variable=='number'&&variable<1);
}
function isJson(string='{}',return_value=true){
	try{return $.type(string)=='object' ? string : JSON.parse(string.replace(/(\r\n\t|\n|\r\t+)/gm,"").trim());}
	catch(e){return (return_value) ? {} : false};
}
function __pop(obj){
	if (typeof obj=='object') {
		obj = (typeof obj.filter == 'function') ? obj.filter(function(el) {
			return el;
		}) : Object.keys(obj).forEach((k) => (!obj[k] && obj[k] !== undefined) && delete obj[k]);
	}
	return obj;
}
function setcookie(name=null,value=null,time=false,path=false,domain=false){
	time = typeof time!='number' ? 1 : time;
	return (typeof name=='number'||typeof name=='string') ? (
		(value==null) ? Cookies.get(name) : (
				Cookies.set(name,value,{
					expires:new Date(new Date().getTime()+(parseInt(time)*60*1000)),
					path:typeof path!=='string' ? window.location.pathname : path,
					domain:typeof domain!=='string' ? window.location.host : domain,
				}) ? value : undefined
		)
	) : undefined;
}
function unsetcookie(name=null){Cookies.remove(name);}
function evaluate(json={},print=true){
	try{json.status = typeof json.status!='undefined' ? json.status : 'error'; }
	catch(e){json.status='error';}
	return (json.status=='success');
};





/*##########################################################################*/
/*################################# Plugins ################################*/
/*##########################################################################*/
Number.prototype.cent = String.prototype.cent = function(){
	var str = this.toString();
	var c = str.replace(/\s+/gi,'').match(/(\.|\,)/gi);
	var cent = (c!=null) ? c.slice(-1).toString() : false;
	return !cent ? '' : (
		str.match(new RegExp('\\'+cent,'gi')).length>1 ? '' : cent
	);
};
$.fn.format = $.format = function(num=""){
	ht = function (cg="",$NaN=true) {
		var cg = cn(cg,'number',cn(cg,'string',0));
		var cent = cg.cent();
		var t = parseFloat((cg.toString().trim().substr(0,1)=='-' ? '-' : '')+cg.toString().split((cent!="")?cent:'[]').map(nm=>{
			return nm.replace(/\D+/gi,'');
		}).join('.'));
		return isNaN(t) ? (
			!$NaN ? 0 : t
		) : t;
	};
	prpt = function(arg) {
		return tmp = $.extend(true, {
			cent:',',
			k:'.',
			decimals:0,
		},this.dataset,{pd:','}),
		tmp.cent = (tmp.cent==tmp.k) ? tmp.pd : tmp.cent,
		tmp.decimals = isNaN(tmp.decimals) ? 2 : tmp.decimals,
		tmp;
	};
	nm = ht(num);
	$(this)
	.not(document)
	.filter('[type="number"]')
	.attr('type','text')
	.end()
	.each(function(index, el) {
		try {
			var vl = isNaN(nm) ? ht($(this).is('input,check,textarea,radio') ? $(this).val() : $(this).text(),false) : nm;
			var cf = prpt.call(el);
			$(this)
			.attr('numbered',true)
			.filter('input,check,textarea,radio')
			.number(true,cf.decimals,cf.cent,cf.k)
			.val(vl)
			.end()
			.not('input,check,textarea,radio')
			.number(vl,cf.decimals,cf.cent,cf.k);
		} catch(e) {}
	});
	return $(this).is(document) ? (isNaN(nm)?0:nm) : $(this);
}


sort = function(json={},key=null,order="asc"){
	return json.sort(function(next,current){
		if (next[key]>current[key]) return (typeof key=='string' &&order=='asc') ? 1 : -1;
		if (next[key]<current[key]) return (typeof key=='string' &&order=='asc') ? -1 : 1;
		return 0;
	});
}




/*Modal*/
$.fn.modal = async function(a='toggle',b=null,c=null){
	$modals = $(this).filter('.modal');
	let action = cn(a,'string','toggle').replace(/(\W+)/gi,'').toLowerCase();
	let delay = parseInt(cn(b,'number',cn(c,'number',cn(a,'number','fast'))));
	let callback = cn(c,'function',cn(b,'function',cn(a,'function',function(){})));
	$effects = {
		show:'fadeIn',
		hide:'fadeOut',
		close:'fadeOut',
		remove:'fadeOut',
		toggle:'fadeToggle',
	};
	let basic = function(){
		if (action=='remove') $modals.remove();
		callback.call($modals.trigger('arc.modal.'+action).toggleClass('is-active',true));
	};
	var fn = '$modals.'+$effects[action]+'(delay,basic)';
	await eval(fn);
};
/*Fin de Modal*/

/*SwitchClass*/
$.fn.switchClass = function(classA,classB,type=true){
	if (!type) {return $(this).addClass(classA).removeClass(classB); }
	return $(this).is('.'+classA) ? $(this).addClass(classB).removeClass(classA) : $(this).addClass(classA).removeClass(classB);
}
/*Fin de SwitchClass*/
$.fn.gdt = function(name='',attr=''){
	var a = $(this).attr('data-'+name) ? $(this).attr('data-'+name) : (
		$(this).data(name) ? $(this).data(name) : attr
	);
	$(this).data(name, a).attr('data-'+name,a);
	return a;
};
/*SwitchAttr*/
$.fn.switchAttr = function(attr,valueA='',valueB='',fn=null){
	fn = $.type(fn)=='function' ? fn : function(){};
	attr = attr.toString().toLowerCase();
	$(this).each(function(index, el) {
		var a = $.trim(this.getAttribute(attr)).toLowerCase(),
		vl = (a==valueB) ? valueA : valueB;
		if ($(this).attr(attr,vl)) {fn.call($(this),vl);};
	});
	return $(this);
}
/*Fin de SwitchAttr*/
/*Multi Tab*/
$.fn.tab = function(obj={},action=false){
	$tabsPanel = $(this);
	if (!$tabsPanel[0]) {return;};

	tabsSettings = $.extend({}, {
		target:cn(obj,'string',cn(action,'string','init')).replace(/(\W+)/gi,'').toLowerCase(),
		delay:'fast',
		before:function(tab,tabs,vars){},
		while:function(percent,realPercent,vars){},
		after:function(tab,tabs,vars){},
	}, cn(obj,'object',cn(action,'object',{})),{
		valid:true,
		stop:function(){this.valid = false; },
		start:function(){this.valid = true; },
	});

	tbs = function () {
 		if (!tabsSettings.valid) {return;}
 		tabs = {};
		/*Init*/
		tabs.list = $tabsPanel.children('.arc-tab');
		tabs.active = tabs.list.filter('.arc-tab-active').last().siblings('.arc-tab-active').removeClass('arc-tab-active').end();
		tabs.active = tabs.active[0] ? tabs.active : tabs.list.first().addClass('arc-tab-active');
		/*Tabs List*/
		tabs.refreshtab = tabs.active;
		tabs.init = tabs.active;
		tabs.resettab = tabs.list.first();
		tabs.prevtab = tabs.active.prev('.arc-tab');
		tabs.nexttab = tabs.active.next('.arc-tab');
		tabs.endtab = tabs.list.last();
	}



	new Promise(async function(s,r){
		/*Before slider Tab => Tabs*/
		await tabsSettings.before($tabsPanel);
		return (tabsSettings.valid) ? s() : r();
	})
	.then(tbs)
	.then(function(){
 		if (!tabsSettings.valid) {return;}
		scrolls = {
			target:tabs[tabsSettings.target],
			on:(tabs.active.index()>=0) ? (tabs.active.index()+1) : 1,
			width:$tabsPanel.prop("scrollWidth"),
			height:$tabsPanel.prop("scrollHeight"),
		};
		scrolls.steps = (scrolls.width/tabs.list.length);
		scrolls.to = {
			left:(scrolls.steps*scrolls.target.index()),
			top:scrolls.target.position().top,
		};
		scrolls.percent = {
			x: Math.round10((scrolls.to.left/scrolls.width)*100,0),
			y: Math.round10((scrolls.to.top/scrolls.height)*100,0),
		};
	})
	.then(async function(){
 		if (!tabsSettings.valid) {return;}
		await $tabsPanel.animate({
			scrollLeft:scrolls.to.left,
			scrollTop:scrolls.to.top,
		},{
			duration:tabsSettings.delay,
			progress:function(obj,now){
 				try {
	 				if (!tabsSettings.valid) {$tabsPanel.stop();}
	 				else{
						/*While is Scrolling => number | percent{x,y} | var tabs*/
						tabsSettings.while((now*100),scrolls.percent,tabs);
						scrolls.target
						.css({
							'opacity':now,
							'visibility':'visible',
						})
						.toggleClass('arc-tab-active',(now>=1))
						.siblings('.arc-tab')
						.removeClass('arc-tab-active',(now>=1))
						.css({
							'opacity':(1-now),
							'visibility':(now>=1) ? 'hidden' : 'visible',
						});
	 				} 					
 				} catch(e) {console.error(e); }
			},
		});
	})
	.then(async function(){
 		if (!tabsSettings.valid) {return;}
		/*On Finish Scroll => Active tab | Tab List*/
		await tabsSettings.after.call(scrolls.target);
	})
	.catch(e => {});
	return $tabsPanel;
}
/*Fin de Multi Tab*/
/*Autocomplete*/
$.fn.autocomplete = function(arg={}){
	var params = $.extend(true, {
		url:false,
		list:[],
		template:{
			input:'<input type="text" class="input">',
			item:'<a href="#" class="dropdown-item" value="{{value}}">{{label}}</a>',
		},
		min:3,
		filter:function(obj,__term){return obj;},
	}, isJson(arg));
	__call = {abort:function(){return true;},};
	$(this).each(function() {
		$(this)
		.prepend(
			'<div class="dropdown autocomplete w-100">'
			+ '<div class="dropdown-trigger w-100">'
			+ '		<div class="control has-icons-left has-icons-right search-field">'
			+ 			params.template.input
			+ '			<span class="icon is-medium is-left">'
			+ '				<i class="fa fa-search"></i>'
			+ '			</span>'
			+ '			<span class="icon is-medium is-right">'
			+ '				<i class="fa fa-redo fa-spin"></i>'
			+ '			</span>'
			+ '		</div>'
			+ '	</div>'
			+ '<div class="dropdown-menu w-100">'
			+ '	<div class="dropdown-content b-radius"></div>'
			+ '</div>'
			+'</div>'
		);
	})
	.find('.dropdown-trigger input')
	.on('keyup', function(event) {
		event.stopPropagation();
		__input = $(this);
		if (__input.val().trim().length<params.min) {return;}
		if (typeof __set !== 'undefined'){clearTimeout(__set);}
		if (typeof __call !== 'undefined'){__call.abort();}
		__list = [];
		__set = setTimeout(function(){
			__call = $.ajax({
				url: params.url,
				beforeSend:function(){
					__input
					.parents('.dropdown.autocomplete')
					.find('.dropdown-menu > .dropdown-content')
					.html('<a href="#" class="dropdown-item">Cargando...</a>');
				},
				dataType: 'json',
				data: {term: __input.val()},
				async:false,
			})
			.always(function(e){
				__input
				.parents('.dropdown.autocomplete')
				.find('.dropdown-menu > .dropdown-content').html('');
			});

			try {
				if (evaluate(__call.responseJSON)) {__list = __call.responseJSON.results;}
					$(Mustache.render('{{#list}}'+params.template.item+'{{/list}}', {list:__list.slice(0,5)}))
					.appendTo(__input.parents('.dropdown.autocomplete').find('.dropdown-menu > .dropdown-content'));
			} catch(e) {
				console.log(e);
			}

		},400);
	});
};
/*Fin de Autocomplete*/
/*Local Storage*/
$.cache = function (key=null,value=null,expire=60,callback=null){
	localCache_result = undefined;
	callback = ($.type(expire)=='function'&&$.type(callback)=='null') ? expire : callback;
	expire = $.type(expire)!='number' ? 60 : expire;

	new Promise(function(success){
		cogs = {
			key:(typeof key == 'string') ? key : null,
			content:(typeof value != 'null') ? (
				!isJson(value,false) ? value : isJson(value)
			) : null,
			expire:($.now()+(expire*1000)),
		};
		if (localStorage.getItem(cogs.key)&&JSON.parse(localStorage.getItem(cogs.key)).expire<$.now()) localStorage.removeItem(cogs.key);
	});

	if (cogs&&cogs.key) {
		if (cogs.content) {
			localCache_result = (typeof localStorage.setItem(cogs.key,JSON.stringify(cogs))=='undefined') ? cogs.content : null;
		}
		else if(localStorage.getItem(cogs.key)){
			localCache_result = JSON.parse(localStorage.getItem(cogs.key)).content;
		}
	}
	if ($.type(callback)=='function') {
		new Promise(async function(success,error){
			await callback(localCache_result,cogs);
		});
	}
	return localCache_result;
};
/*Fin de LocalStorage*/
/*$.id*/
$.fn.id = function(){
	var id = getId($(this).length);
	to_r = [];
	for (var i =  0; i<$(this).length; i++) {
		var element = $($(this)[i]);
		to_r[i] = element.attr('id',(element.attr('id')&&element.attr('id').length>0) ? element.attr('id') : (
			(typeof id=='object') ? id[i] : id
		))[0];
	}
	return $(this);
}
/* Fin de $.id */
/*Cambiar URL*/
$.url = function(url=false){
	url = new URL(url,window.location.href);
	url = url.toString();
	return history.pushState({
		path: url
	}, url, url);
}
/*Fin de Cambiar URL*/
$.tagName = function(e) {return e.prop("tagName").toLowerCase();};
$.fn.tagName = function() {return this.prop("tagName").toLowerCase();};
$.hasScrollX = function(e) {return  e = this.get(0), (e.scrollWidth>e.clientWidth);};
$.hasScrollY = function(e) {return  e = this.get(0), (e.scrollHeight>e.clientHeight);};
$.hasScroll = function(e) {
	return {
		vertical: $.hasScrollY(this.get(0)),
		horizontal: $.hasScrollX(this.get(0)),
	};
};
$.fn.scrollBottom = function(px=null,slide=1,afterFn=function(){}) { 
	var body = this.get(0),
		jBody = $(body),
		sH = body.scrollHeight,
		sT = body.scrollTop,
		iH = jBody.innerHeight(),
		sb = (sH - sT - iH);
	if (px==null) {return sb;}
	else if(!isNaN(px)){
		px = (sH - (iH + px));
		jBody.c = afterFn;
		jBody.animate({
			scrollTop:px
		}, slide,function () {
			return jBody.c();
		});
	}
	return $(this);
};
$.fn.percent = function(value=null,max=null,is_percent=null){
	var $ppc = $(this);
	var is_percent = ($.type(max)=='boolean') ? max : ((is_percent=='null') ? false : is_percent);
	var max = $.type(max)!=='number' ? $ppc.gdt('max',100) : parseInt(max);
	var value = ($.type(value)=='object') ? value : {
		value:!isNaN(value) ? value : $ppc.gdt('value',0)
	};
	value = $.extend({}, {
		max:(value.max) ? value.max : max,
		value:(value.percent) ? value.percent : value.value,
		symbol:$ppc.gdt('symbol','%'),
		prefix:$ppc.gdt('prefix',false),
		icon:$ppc.gdt('icon',false),
		iconTemplate:{
			minus:'<i class="fa fa-arrow-down"></i><br>',
			plus:'<i class="fa fa-arrow-up"></i><br>',
		},
	}, value);
	value.negative = (value.value<0);
	value.prefix = (value.icon) ? (
		value.prefix ? value.prefix : (value.negative ? '-' : '')
	) : (value.negative ? '-' : '');
	value.value *= (value.negative) ? -1 : 1;
	value.max *= (value.max<0) ? -1 : 1;
	value.max = (value.value>value.max) ? value.value : value.max;
	value.percent = is_percent ? value.value : ((value.value/value.max)*100);
	value.deg = ((360*(value.percent>360 ? 360 : value.percent))/100);
	if (!$ppc.children('.progress-pie-chart')[0]) {
		$ppc.html('<div class="progress-pie-chart">'
		+ '	<div class="ppc-progress">'
		+ '		<div class="ppc-progress-fill"></div>'
		+ '	</div>'
		+ '	<div class="ppc-percents">'
		+ '		<div class="pcc-percents-wrapper">'
		+ '			<span class="ppc-prefix">'+value.prefix+'</span><span class="ppc-value"></span><span class="ppc-symbol">'+(value.icon ? value.symbol : '')+'</span>'
		+ '		</div>'
		+ '	</div>'
		+ '</div>');
	}
	return $ppc
	.data(value)
	.children('.progress-pie-chart')
	.toggleClass('negative',value.negative)
	.toggleClass('gt-50',(value.percent>50))
	.find('.ppc-progress-fill')
	.animate({deg: value.deg,},{
		duration:500,
		step:function(now){
			$ppc
			.find('.ppc-percents span.ppc-value').text(Math.round10(now/3.6,-2));
			$(this).css({transform: 'rotate('+now+'deg)'});
		},
	}), $(this);
};
/*ARC-LOAD*/
$.load = $.fn.load = function(link,arguments={},typeMethod=null){
	load_vars = (typeof load_vars !='object') ? [] : load_vars;
	var set = load_vars.length;
	load_vars[set] = {
		url:		cn(link,'string',cn(arguments,'string','')),
		type:		cn(typeMethod,'string',cn(arguments,'string','get')),
		container:!$(this).is(document) ? $(this) : false,
		callback:	cn(link,'function',cn(arguments,'function',cn(typeMethod,'function',function(){}))),
		arg:		$.extend(true, {
			render:true,
		}, cn(arguments,'object',cn(typeMethod,'object',{}))),

	};
	try {
		load_vars[set].type = cn(cn(arguments,'object',cn(typeMethod,'object',{type:true})).type,'string',cn(typeMethod,'string',cn(arguments,'string','get')));
	} catch(e) {
		load_vars[set].type = 'get';
	}
	$.ajax($.extend(true, {
		url: load_vars[set].url,
		type: load_vars[set].type,
		async:false,
	}, load_vars[set].arg,{
		beforeSend:function(){
			$('script[bs64url="'+btoa(load_vars[set].url)+'"]').remove();
		},
		error:function(e){console.error("ERROR ON $.load: ",e);},
		success:async function(response,status,xhr){
			var doc = $('<docker>'+response.replace(/\{\{(\w+)\}\}|\%\{(\w+)\}\%/g,function(find){return "{{"+find.substring(2,(find.length-2)).toLowerCase()+"}}";})+'</docker>');
 			var arg = {
				render:cn($.cache('translate'),'object',{}),
				config:{
					title:document.querySelector('title').innerHTML,
					url:window.location.href.split(window.location.origin)[1],
				},
				script:[],
			};
			/*Variables del Renderizado*/
		

			Object.values(doc.find('render')).map(item=>{arg.render = $.extend(true, arg.render, isJson($(item).text())); });
			doc.find('render').remove();
			Object.entries(arg.render).forEach((item)=>{
				arg.render[item[0].toString().toLowerCase()] = item[1];
			});




			/*Depurar HTML*/
			if (load_vars[set].arg.render) {var doc = $('<docker>'+Mustache.render(doc.html(),arg.render)+'</docker>');}

			
			/*Variables de configuracion*/
			Object.values(doc.find('config')).map(item=>{arg.config = $.extend(true, arg.config, isJson($(item).text())); });
			doc.find('config').remove();

			/*Elementos Scripts*/
			doc.find('script').each((index, item)=> {
				attrs = [];
				for (attr in item.attributes) {if (typeof item.attributes[attr]=='object') attrs.push(item.attributes[attr].name+'="'+item.attributes[attr].value+'"'); }
				arg.script.push('<script bs64url="'+btoa(arg.url)+'" script-loadview="'+getId()+'" '+attrs.join(' ')+'>'+$(item).text()+'</script>');
			}).remove();
			$('html title').text(arg.config.title);
			new Promise(function(s){
				$.url(arg.config.url);
				if ($('body').append(arg.script.join(' '))) {
					if(typeof load_vars[set].container=='object') load_vars[set].container.html(doc.html());
					return s();
				}
			})
			.then(function(){
				load_vars[set].callback(load_vars[set].container,status,xhr);
			});
		},
	}));
	return cn(load_vars[set].container,'object',true);
};

/*		Cookies		*/
!function(e){var n=!1;if("function"==typeof define&&define.amd&&(define(e),n=!0),"object"==typeof exports&&(module.exports=e(),n=!0),!n){var o=window.Cookies,t=window.Cookies=e();t.noConflict=function(){return window.Cookies=o,t}}}(function(){function g(){for(var e=0,n={};e<arguments.length;e++){var o=arguments[e];for(var t in o)n[t]=o[t]}return n}return function e(l){function C(e,n,o){var t;if("undefined"!=typeof document){if(1<arguments.length){if("number"==typeof(o=g({path:"/"},C.defaults,o)).expires){var r=new Date;r.setMilliseconds(r.getMilliseconds()+864e5*o.expires),o.expires=r}o.expires=o.expires?o.expires.toUTCString():"";try{t=JSON.stringify(n),/^[\{\[]/.test(t)&&(n=t)}catch(e){}n=l.write?l.write(n,e):encodeURIComponent(String(n)).replace(/%(23|24|26|2B|3A|3C|3E|3D|2F|3F|40|5B|5D|5E|60|7B|7D|7C)/g,decodeURIComponent),e=(e=(e=encodeURIComponent(String(e))).replace(/%(23|24|26|2B|5E|60|7C)/g,decodeURIComponent)).replace(/[\(\)]/g,escape);var i="";for(var c in o)o[c]&&(i+="; "+c,!0!==o[c]&&(i+="="+o[c]));return document.cookie=e+"="+n+i}e||(t={});for(var a=document.cookie?document.cookie.split("; "):[],s=/(%[0-9A-Z]{2})+/g,f=0;f<a.length;f++){var p=a[f].split("="),d=p.slice(1).join("=");this.json||'"'!==d.charAt(0)||(d=d.slice(1,-1));try{var u=p[0].replace(s,decodeURIComponent);if(d=l.read?l.read(d,u):l(d,u)||d.replace(s,decodeURIComponent),this.json)try{d=JSON.parse(d)}catch(e){}if(e===u){t=d;break}e||(t[u]=d)}catch(e){}}return t}}return(C.set=C).get=function(e){return C.call(C,e)},C.getJSON=function(){return C.apply({json:!0},[].slice.call(arguments))},C.defaults={},C.remove=function(e,n){C(e,"",g(n,{expires:-1}))},C.withConverter=e,C}(function(){})});
/*Math.round10*/
$(function(){function decimalAdjust(type,value,exp){if(typeof exp==='undefined'||+exp===0){return Math[type](value)}value=+value;exp=+exp;if(isNaN(value)||!(typeof exp==='number'&&exp%1===0)){return NaN};value=value.toString().split('e');value=Math[type](+(value[0]+'e'+(value[1]?(+value[1]-exp):-exp)));value=value.toString().split('e');return+(value[0]+'e'+(value[1]?(+value[1]+exp):exp))};if(!Math.round10){Math.round10=function(value,exp){return decimalAdjust('round',value,exp)}};if(!Math.floor10){Math.floor10=function(value,exp){return decimalAdjust('floor',value,exp)}}if(!Math.ceil10){Math.ceil10=function(value,exp){return decimalAdjust('ceil',value,exp)}}});
/*		MUSTACHE	*/
(function defineMustache(global,factory){if(typeof exports==="object"&&exports&&typeof exports.nodeName!=="string"){factory(exports)}else if(typeof define==="function"&&define.amd){define(["exports"],factory)}else{global.Mustache={};factory(global.Mustache)}})(this,function mustacheFactory(mustache){var objectToString=Object.prototype.toString;var isArray=Array.isArray||function isArrayPolyfill(object){return objectToString.call(object)==="[object Array]"};function isFunction(object){return typeof object==="function"}function typeStr(obj){return isArray(obj)?"array":typeof obj}function escapeRegExp(string){return string.replace(/[\-\[\]{}()*+?.,\\\^$|#\s]/g,"\\$&")}function hasProperty(obj,propName){return obj!=null&&typeof obj==="object"&&propName in obj}function primitiveHasOwnProperty(primitive,propName){return primitive!=null&&typeof primitive!=="object"&&primitive.hasOwnProperty&&primitive.hasOwnProperty(propName)}var regExpTest=RegExp.prototype.test;function testRegExp(re,string){return regExpTest.call(re,string)}var nonSpaceRe=/\S/;function isWhitespace(string){return!testRegExp(nonSpaceRe,string)}var entityMap={"&":"&amp;","<":"&lt;",">":"&gt;",'"':"&quot;","'":"&#39;","/":"&#x2F;","`":"&#x60;","=":"&#x3D;"};function escapeHtml(string){return String(string).replace(/[&<>"'`=\/]/g,function fromEntityMap(s){return entityMap[s]})}var whiteRe=/\s*/;var spaceRe=/\s+/;var equalsRe=/\s*=/;var curlyRe=/\s*\}/;var tagRe=/#|\^|\/|>|\{|&|=|!/;function parseTemplate(template,tags){if(!template)return[];var sections=[];var tokens=[];var spaces=[];var hasTag=false;var nonSpace=false;function stripSpace(){if(hasTag&&!nonSpace){while(spaces.length)delete tokens[spaces.pop()]}else{spaces=[]}hasTag=false;nonSpace=false}var openingTagRe,closingTagRe,closingCurlyRe;function compileTags(tagsToCompile){if(typeof tagsToCompile==="string")tagsToCompile=tagsToCompile.split(spaceRe,2);if(!isArray(tagsToCompile)||tagsToCompile.length!==2)throw new Error("Invalid tags: "+tagsToCompile);openingTagRe=new RegExp(escapeRegExp(tagsToCompile[0])+"\\s*");closingTagRe=new RegExp("\\s*"+escapeRegExp(tagsToCompile[1]));closingCurlyRe=new RegExp("\\s*"+escapeRegExp("}"+tagsToCompile[1]))}compileTags(tags||mustache.tags);var scanner=new Scanner(template);var start,type,value,chr,token,openSection;while(!scanner.eos()){start=scanner.pos;value=scanner.scanUntil(openingTagRe);if(value){for(var i=0,valueLength=value.length;i<valueLength;++i){chr=value.charAt(i);if(isWhitespace(chr)){spaces.push(tokens.length)}else{nonSpace=true}tokens.push(["text",chr,start,start+1]);start+=1;if(chr==="\n")stripSpace()}}if(!scanner.scan(openingTagRe))break;hasTag=true;type=scanner.scan(tagRe)||"name";scanner.scan(whiteRe);if(type==="="){value=scanner.scanUntil(equalsRe);scanner.scan(equalsRe);scanner.scanUntil(closingTagRe)}else if(type==="{"){value=scanner.scanUntil(closingCurlyRe);scanner.scan(curlyRe);scanner.scanUntil(closingTagRe);type="&"}else{value=scanner.scanUntil(closingTagRe)}if(!scanner.scan(closingTagRe))throw new Error("Unclosed tag at "+scanner.pos);token=[type,value,start,scanner.pos];tokens.push(token);if(type==="#"||type==="^"){sections.push(token)}else if(type==="/"){openSection=sections.pop();if(!openSection)throw new Error('Unopened section "'+value+'" at '+start);if(openSection[1]!==value)throw new Error('Unclosed section "'+openSection[1]+'" at '+start)}else if(type==="name"||type==="{"||type==="&"){nonSpace=true}else if(type==="="){compileTags(value)}}openSection=sections.pop();if(openSection)throw new Error('Unclosed section "'+openSection[1]+'" at '+scanner.pos);return nestTokens(squashTokens(tokens))}function squashTokens(tokens){var squashedTokens=[];var token,lastToken;for(var i=0,numTokens=tokens.length;i<numTokens;++i){token=tokens[i];if(token){if(token[0]==="text"&&lastToken&&lastToken[0]==="text"){lastToken[1]+=token[1];lastToken[3]=token[3]}else{squashedTokens.push(token);lastToken=token}}}return squashedTokens}function nestTokens(tokens){var nestedTokens=[];var collector=nestedTokens;var sections=[];var token,section;for(var i=0,numTokens=tokens.length;i<numTokens;++i){token=tokens[i];switch(token[0]){case"#":case"^":collector.push(token);sections.push(token);collector=token[4]=[];break;case"/":section=sections.pop();section[5]=token[2];collector=sections.length>0?sections[sections.length-1][4]:nestedTokens;break;default:collector.push(token)}}return nestedTokens}function Scanner(string){this.string=string;this.tail=string;this.pos=0}Scanner.prototype.eos=function eos(){return this.tail===""};Scanner.prototype.scan=function scan(re){var match=this.tail.match(re);if(!match||match.index!==0)return"";var string=match[0];this.tail=this.tail.substring(string.length);this.pos+=string.length;return string};Scanner.prototype.scanUntil=function scanUntil(re){var index=this.tail.search(re),match;switch(index){case-1:match=this.tail;this.tail="";break;case 0:match="";break;default:match=this.tail.substring(0,index);this.tail=this.tail.substring(index)}this.pos+=match.length;return match};function Context(view,parentContext){this.view=view;this.cache={".":this.view};this.parent=parentContext}Context.prototype.push=function push(view){return new Context(view,this)};Context.prototype.lookup=function lookup(name){var cache=this.cache;var value;if(cache.hasOwnProperty(name)){value=cache[name]}else{var context=this,intermediateValue,names,index,lookupHit=false;while(context){if(name.indexOf(".")>0){intermediateValue=context.view;names=name.split(".");index=0;while(intermediateValue!=null&&index<names.length){if(index===names.length-1)lookupHit=hasProperty(intermediateValue,names[index])||primitiveHasOwnProperty(intermediateValue,names[index]);intermediateValue=intermediateValue[names[index++]]}}else{intermediateValue=context.view[name];lookupHit=hasProperty(context.view,name)}if(lookupHit){value=intermediateValue;break}context=context.parent}cache[name]=value}if(isFunction(value))value=value.call(this.view);return value};function Writer(){this.cache={}}Writer.prototype.clearCache=function clearCache(){this.cache={}};Writer.prototype.parse=function parse(template,tags){var cache=this.cache;var cacheKey=template+":"+(tags||mustache.tags).join(":");var tokens=cache[cacheKey];if(tokens==null)tokens=cache[cacheKey]=parseTemplate(template,tags);return tokens};Writer.prototype.render=function render(template,view,partials,tags){var tokens=this.parse(template,tags);var context=view instanceof Context?view:new Context(view);return this.renderTokens(tokens,context,partials,template)};Writer.prototype.renderTokens=function renderTokens(tokens,context,partials,originalTemplate){var buffer="";var token,symbol,value;for(var i=0,numTokens=tokens.length;i<numTokens;++i){value=undefined;token=tokens[i];symbol=token[0];if(symbol==="#")value=this.renderSection(token,context,partials,originalTemplate);else if(symbol==="^")value=this.renderInverted(token,context,partials,originalTemplate);else if(symbol===">")value=this.renderPartial(token,context,partials,originalTemplate);else if(symbol==="&")value=this.unescapedValue(token,context);else if(symbol==="name")value=this.escapedValue(token,context);else if(symbol==="text")value=this.rawValue(token);if(value!==undefined)buffer+=value}return buffer};Writer.prototype.renderSection=function renderSection(token,context,partials,originalTemplate){var self=this;var buffer="";var value=context.lookup(token[1]);function subRender(template){return self.render(template,context,partials)}if(!value)return;if(isArray(value)){for(var j=0,valueLength=value.length;j<valueLength;++j){buffer+=this.renderTokens(token[4],context.push(value[j]),partials,originalTemplate)}}else if(typeof value==="object"||typeof value==="string"||typeof value==="number"){buffer+=this.renderTokens(token[4],context.push(value),partials,originalTemplate)}else if(isFunction(value)){if(typeof originalTemplate!=="string")throw new Error("Cannot use higher-order sections without the original template");value=value.call(context.view,originalTemplate.slice(token[3],token[5]),subRender);if(value!=null)buffer+=value}else{buffer+=this.renderTokens(token[4],context,partials,originalTemplate)}return buffer};Writer.prototype.renderInverted=function renderInverted(token,context,partials,originalTemplate){var value=context.lookup(token[1]);if(!value||isArray(value)&&value.length===0)return this.renderTokens(token[4],context,partials,originalTemplate)};Writer.prototype.renderPartial=function renderPartial(token,context,partials){if(!partials)return;var value=isFunction(partials)?partials(token[1]):partials[token[1]];if(value!=null)return this.renderTokens(this.parse(value),context,partials,value)};Writer.prototype.unescapedValue=function unescapedValue(token,context){var value=context.lookup(token[1]);if(value!=null)return value};Writer.prototype.escapedValue=function escapedValue(token,context){var value=context.lookup(token[1]);if(value!=null)return mustache.escape(value)};Writer.prototype.rawValue=function rawValue(token){return token[1]};mustache.name="mustache.js";mustache.version="3.0.0";mustache.tags=["{{","}}"];var defaultWriter=new Writer;mustache.clearCache=function clearCache(){return defaultWriter.clearCache()};mustache.parse=function parse(template,tags){return defaultWriter.parse(template,tags)};mustache.render=function render(template,view,partials,tags){if(typeof template!=="string"){throw new TypeError('Invalid template! Template should be a "string" '+'but "'+typeStr(template)+'" was given as the first '+"argument for mustache#render(template, view, partials)")}return defaultWriter.render(template,view,partials,tags)};mustache.to_html=function to_html(template,view,partials,send){var result=mustache.render(template,view,partials);if(isFunction(send)){send(result)}else{return result}};mustache.escape=escapeHtml;mustache.Scanner=Scanner;mustache.Context=Context;mustache.Writer=Writer;return mustache});
/*		Number	*/
!function(e){"use strict";function t(e,t){if(this.createTextRange){var a=this.createTextRange();a.collapse(!0),a.moveStart("character",e),a.moveEnd("character",t-e),a.select()}else this.setSelectionRange&&(this.focus(),this.setSelectionRange(e,t))}function a(e){var t=this.value.length;if(e="start"==e.toLowerCase()?"Start":"End",document.selection){var a,i,n,l=document.selection.createRange();return(a=l.duplicate()).expand("textedit"),a.setEndPoint("EndToEnd",l),n=(i=a.text.length-l.text.length)+l.text.length,"Start"==e?i:n}return void 0!==this["selection"+e]&&(t=this["selection"+e]),t}var i={codes:{46:127,188:44,109:45,190:46,191:47,192:96,220:92,222:39,221:93,219:91,173:45,187:61,186:59,189:45,110:46},shifts:{96:"~",49:"!",50:"@",51:"#",52:"$",53:"%",54:"^",55:"&",56:"*",57:"(",48:")",45:"_",61:"+",91:"{",93:"}",92:"|",59:":",39:'"',44:"<",46:">",47:"?"}};$.fn.number=function(e,n,l,s){s=void 0===s?",":s,n=void 0===n?0:n;var r="\\u"+("0000"+(l=void 0===l?".":l).charCodeAt(0).toString(16)).slice(-4),h=new RegExp("[^"+r+"0-9]","g"),u=new RegExp(r,"g");return!0===e?this.is("input:text")?this.on({"keydown.format":function(e){var r=$(this),h=r.data("numFormat"),u=e.keyCode?e.keyCode:e.which,o="",c=a.apply(this,["start"]),v=a.apply(this,["end"]),d="",p=!1;if(i.codes.hasOwnProperty(u)&&(u=i.codes[u]),!e.shiftKey&&u>=65&&u<=90?u+=32:!e.shiftKey&&u>=69&&u<=105?u-=48:e.shiftKey&&i.shifts.hasOwnProperty(u)&&(o=i.shifts[u]),""==o&&(o=String.fromCharCode(u)),8!=u&&45!=u&&127!=u&&o!=l&&!o.match(/[0-9]/)){var g=e.keyCode?e.keyCode:e.which;if(46==g||8==g||127==g||9==g||27==g||13==g||(65==g||82==g||80==g||83==g||70==g||72==g||66==g||74==g||84==g||90==g||61==g||173==g||48==g)&&!0===(e.ctrlKey||e.metaKey)||(86==g||67==g||88==g)&&!0===(e.ctrlKey||e.metaKey)||g>=35&&g<=39||g>=112&&g<=123)return;return e.preventDefault(),!1}if(0==c&&v==this.value.length?8==u?(c=v=1,this.value="",h.init=n>0?-1:0,h.c=n>0?-(n+1):0,t.apply(this,[0,0])):o==l?(c=v=1,this.value="0"+l+new Array(n+1).join("0"),h.init=n>0?1:0,h.c=n>0?-(n+1):0):45==u?(c=v=2,this.value="-0"+l+new Array(n+1).join("0"),h.init=n>0?1:0,h.c=n>0?-(n+1):0,t.apply(this,[2,2])):(h.init=n>0?-1:0,h.c=n>0?-n:0):h.c=v-this.value.length,h.isPartialSelection=c!=v,n>0&&o==l&&c==this.value.length-n-1)h.c++,h.init=Math.max(0,h.init),e.preventDefault(),p=this.value.length+h.c;else if(45!=u||0==c&&0!=this.value.indexOf("-"))if(o==l)h.init=Math.max(0,h.init),e.preventDefault();else if(n>0&&127==u&&c==this.value.length-n-1)e.preventDefault();else if(n>0&&8==u&&c==this.value.length-n)e.preventDefault(),h.c--,p=this.value.length+h.c;else if(n>0&&127==u&&c>this.value.length-n-1){if(""===this.value)return;"0"!=this.value.slice(c,c+1)&&(d=this.value.slice(0,c)+"0"+this.value.slice(c+1),r.val(d)),e.preventDefault(),p=this.value.length+h.c}else if(n>0&&8==u&&c>this.value.length-n){if(""===this.value)return;"0"!=this.value.slice(c-1,c)&&(d=this.value.slice(0,c-1)+"0"+this.value.slice(c),r.val(d)),e.preventDefault(),h.c--,p=this.value.length+h.c}else 127==u&&this.value.slice(c,c+1)==s?e.preventDefault():8==u&&this.value.slice(c-1,c)==s?(e.preventDefault(),h.c--,p=this.value.length+h.c):n>0&&c==v&&this.value.length>n+1&&c>this.value.length-n-1&&isFinite(+o)&&!e.metaKey&&!e.ctrlKey&&!e.altKey&&1===o.length&&(d=v===this.value.length?this.value.slice(0,c-1):this.value.slice(0,c)+this.value.slice(c+1),this.value=d,p=c);else e.preventDefault();!1!==p&&t.apply(this,[p,p]),r.data("numFormat",h)},"keyup.format":function(e){var i,l=$(this),s=l.data("numFormat"),r=e.keyCode?e.keyCode:e.which,h=a.apply(this,["start"]),u=a.apply(this,["end"]);0!==h||0!==u||189!==r&&109!==r||(l.val("-"+l.val()),h=1,s.c=1-this.value.length,s.init=1,l.data("numFormat",s),i=this.value.length+s.c,t.apply(this,[i,i])),""===this.value||(r<48||r>57)&&(r<96||r>105)&&8!==r&&46!==r&&110!==r||(l.val(l.val()),n>0&&(s.init<1?(h=this.value.length-n-(s.init<0?1:0),s.c=h-this.value.length,s.init=1,l.data("numFormat",s)):h>this.value.length-n&&8!=r&&(s.c++,l.data("numFormat",s))),46!=r||s.isPartialSelection||(s.c++,l.data("numFormat",s)),i=this.value.length+s.c,t.apply(this,[i,i]))},"paste.format":function(e){var t=$(this),a=e.originalEvent,i=null;return window.clipboardData&&window.clipboardData.getData?i=window.clipboardData.getData("Text"):a.clipboardData&&a.clipboardData.getData&&(i=a.clipboardData.getData("text/plain")),t.val(i),e.preventDefault(),!1}}).each(function(){var e=$(this).data("numFormat",{c:-(n+1),decimals:n,thousands_sep:s,dec_point:l,regex_dec_num:h,regex_dec:u,init:!!this.value.indexOf(".")});""!==this.value&&e.val(e.val())}):this.each(function(){var e=$(this),t=+e.text().replace(h,"").replace(u,".");e.number(isFinite(t)?+t:0,n,l,s)}):this.text($.number.apply(window,arguments))};var n=null,l=null;$.isPlainObject($.valHooks.text)?($.isFunction($.valHooks.text.get)&&(n=$.valHooks.text.get),$.isFunction($.valHooks.text.set)&&(l=$.valHooks.text.set)):$.valHooks.text={},$.valHooks.text.get=function(e){var t,a=$(e).data("numFormat");return a?""===e.value?"":(t=+e.value.replace(a.regex_dec_num,"").replace(a.regex_dec,"."),(0===e.value.indexOf("-")?"-":"")+(isFinite(t)?t:0)):$.isFunction(n)?n(e):void 0},$.valHooks.text.set=function(e,t){var a=$(e).data("numFormat");if(a){var i=$.number(t,a.decimals,a.dec_point,a.thousands_sep);return $.isFunction(l)?l(e,i):e.value=i}return $.isFunction(l)?l(e,t):void 0},$.number=function(e,t,a,i){i=void 0===i?"1000"!==new Number(1e3).toLocaleString()?new Number(1e3).toLocaleString().charAt(1):"":i,a=void 0===a?new Number(.1).toLocaleString().charAt(1):a,t=isFinite(+t)?Math.abs(t):0;var n="\\u"+("0000"+a.charCodeAt(0).toString(16)).slice(-4),l="\\u"+("0000"+i.charCodeAt(0).toString(16)).slice(-4);e=(e+"").replace(".",a).replace(new RegExp(l,"g"),"").replace(new RegExp(n,"g"),".").replace(new RegExp("[^0-9+-Ee.]","g"),"");var s=isFinite(+e)?+e:0,r="";return(r=(t?function(e,t){return""+ +(Math.round((""+e).indexOf("e")>0?e:e+"e+"+t)+"e-"+t)}(s,t):""+Math.round(s)).split("."))[0].length>3&&(r[0]=r[0].replace(/\B(?=(?:\d{3})+(?!\d))/g,i)),(r[1]||"").length<t&&(r[1]=r[1]||"",r[1]+=new Array(t-r[1].length+1).join("0")),r.join(a)}}(jQuery);