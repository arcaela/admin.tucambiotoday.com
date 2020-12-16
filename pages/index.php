<?php
if (empty($_SESSION['login_key'])||empty(user::accounts()[$_SESSION['login_key']])) {
	header('Location: /login');
	exit();
}
?><!DOCTYPE html>
<html class="has-navbar-fixed-top">
<head>
	<meta tag="meta" charset="utf-8" />
	<meta tag="meta" name="theme-color" content="#00d1b2" />
	<meta tag="meta" http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta tag="meta" name="viewport" content="width=device-width, initial-scale=1" />
	<link rel="icon" href="%{URL}%cdn/img/favicon.png">
	<link rel="icon" href="https://us.v-cdn.net/6024342/uploads/userpics/506/nJE53M65NUC72.png">
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css">
	<link rel="stylesheet" href='%{URL}%cdn/css/app.css'>
	<link rel="stylesheet" href='%{URL}%cdn/css/toggle-switch.css'>
	<title>Explorador de Anuncios</title>
	<style> [class*="is-hidden-"]{display: none !important; }[is-show="false"]{opacity: 0.6;color:#ccc;pointer-events: none;};
		.content::empty{
			background: #000;
		}
		[ads-item].ads-locked{position: relative;}
		[ads-item].ads-locked:after{
			content: ' ';
			position: absolute;
			width: calc(100% - .75rem*2);
			height: calc(100% - .75rem);
			left: .75rem;
			top: 0px;
			background: rgba(196,196,196,.4);
			margin: 0px;
			border-radius: 6px;
			z-index: 1;
		}
	</style>
</head>

<div class="box p-0 pl-4 pr-1 p-fixed c-default is-clipped block_window" style="z-index:9999999999999999;display: none; width: 300px;height: 50px;right: 10px;top: 60px;line-height: 50px;">
	<a class="close p-absolute" style="right:15px;top:0px;">Cancelar</a>
	<span class="mr-2"><i class="fa fa-sync fa-spin"></i></span>
	<span class="text">Cargando</span>
</div>

<body style="background: #f1f1f1;" class="">

	<?php include('./components/navbar.php'); ?>


	<div class="container is-fluid pt-4-desktop p-relative">
		<div class="columns">
			<div class="column is-3 p-fixed-desktop" style="padding: .75rem 1.5rem;">
				<div id="calculator"></div>
				<div class="box mt-2 mb-0 p-0" id="filter-form"></div>
			</div>
			<div class="column is-9 is-offset-3">
				<div class="notification is-bar is-danger d-block has-text-centered bt-radius-1">
					Se informa que dejaremos de ofrecer soporte para esta versión del administrador de Anuncios de <strong>LocalBitcoins</strong>, si desea hacer uso de la nueva versión puede acceder a ella en el siguiente enlace <a href="http://beta.tucambiotoday.com/" target="_beta_tucambiotoday">http://beta.tucambiotoday.com/</a>
				</div>
				<form class="box mb-1 b-radius-0 bb-radius-1" id="search-form" onsubmit="return downloadAds(),false;"></form>
				<div class="columns is-multiline ads-init"></div>
			</div>
		</div>
	</div>








<div class="template-list"></div>
<?php
	compressFiles(array(
		'%{URL}%cdn/js/jquery.min.js',
		'%{URL}%system/assets/js/arc.js',
	));
?>


<script type="text/javascript" async="async">
	const Site = {
		balance:0,
		vitaly:0,
		kraken:0,
	};

	function ntf(text=null,delay=null,timeout=false){
		try {
			text = cn(text,'string',false);
			delay = cn(delay,'number',1);
			if (!text) return $('.block_window').fadeOut(delay),false;
			else
				$('.block_window').find('span.text').html(text).end().fadeIn((!timeout ? delay : 1),function(){
					if(timeout){setTimeout(function(){$('.block_window').fadeOut('fast');},delay);}
				});
		} catch(e) {return false;}
		return true;
	}


	function syncPrices(type='vitaly'){
		$.getJSON('ads',{price:type},function(p){
			Site[type]=p.price;
			var v=parseFloat(Site.vitaly),k=parseFloat(Site.kraken);
			if(v&&k)
				$('.__coeficient').val((((v/k)*100)-100));

			$('.sync_'+type).val(Site[type]);
			$('.localbitcoin_balance_eur').text( parseFloat(Site.balance*Site.vitaly).toFixed(2)+' EUR ' );
			setTimeout(()=>{
				syncPrices(type=='vitaly' ? 'kraken' : 'vitaly');
			},5000);
		});
	}



	function syncBalance(){
		$.getJSON("%{URL}%api/localbitcoin/balance",function(json){
			if (typeof syncSer=='number') {clearTimeout(syncSer);}
			if (evaluate(json)) {
				Site.balance = json.response.total.balance;
				$('.localbitcoin_balance').text(Site.balance+' BTC ');
				$('.localbitcoin_balance_eur').text(parseFloat(Site.balance*Site.vitaly).toFixed(2)+' BTC ');
			}
			syncSer = setTimeout(function(){
				syncBalance();
			},15000);
		});
	}


	function downloadAds(arg = {}) {
		cacheTimeExpire = 240;/*Segundos*/
		if (typeof downloadAds_Ajax!='undefined') {downloadAds_Ajax.abort();}
		data = $.extend(true, {
			page: 1,
			action: $('[type="radio"][name="action"]:checked').val(),
			currency: $('[name="currency"]').val(),
		}, isJson(arg));
		if (cn(data.action, 'string', false)&&cn(data.currency, 'string', false)) {
			downloadAds_Ajax = $.ajax({
				url: '%{URL}%ads',
				data: data,
				dataType:'json',
				cache: {
					cache: true,
					key: 'action_' + data.action + ';currency_' + data.currency + ';page_' + data.page,
					expire: cacheTimeExpire,
				},
				timeout:30000,
				beforeSend: function() {
					ntf("Descargando pagina &nbsp;&nbsp;<b>" + data.page + "</b>");
					$('.ads-init,[ads-content="'+data.action.replace(/(\W+)/gi, '').toLowerCase()+'"]').html('');
					$('.container-filter-form input').val('');
				},
				success:function(json) {
					try {
						if('number'==typeof json.next){
							return data.page = json.next,
							downloadAds(data);
						}
						else{
							ntf(false);
							var ads = {data:[]};
							Object.entries(localStorage).forEach(function(item) {
								url = item[0].toLowerCase();
								Array.prototype.push.apply(ads.data, (((url.indexOf('currency_' + data.currency.toLowerCase()) >= 0) && (url.indexOf('action_' + data.action.toLowerCase()) >= 0)) ? JSON.parse(item[1]).content.ads : undefined ) );
							});
							ads.data = sort(ads.data, 'price', (data.action == 'sell' ? 'desc' : 'asc'));
							var html = '<div class="column is-full"><div class="box pt-1 pb-1 has-text-centered c-default" onselectstart="return false;" oncontextmenu="return false;">Se han encontrado <strong>'+ads.data.length+'</strong> anuncios en <strong>'+Math.ceil(ads.data.length / 50)+'</strong> paginas, utilizando <strong>' + data.currency.toUpperCase() + '</strong></div></div>';
							html += Mustache.render('{{#data}}'+ads_template+'{{/data}}', ads);
							$('.ads-init,[ads-content="' + data.action.replace(/(\W+)/gi, '').toLowerCase() + '"]').html(html);
							$('[ads-item]')
							.not('[data-country="'+$('[name="countries"]').val().toUpperCase()+'"]')
							.toggleClass('is-hidden',true);
							$('[type="number"]').format();
							if (typeof autoRefresh == 'number') {clearTimeout(autoRefresh);}
							autoRefresh = setTimeout(function() {
								downloadAds();
							}, ((cacheTimeExpire+5)*1000));
						}
					} catch(e) {console.error(e);}
				},
				error: function(xhr, textStatus, errorThrown) {
					switch (textStatus) {
						case 'abort':
							if (typeof autoRefresh == 'number') { clearTimeout(autoRefresh); }
							ntf("Cancelado",false,true);
							break;
						default:
							ntf("Reconectando...",true,false);
							console.warn("Retrying Connection to: " + this.url);
							setTimeout(function() {
								downloadAds(data);
							}, 2000);
							return;
							break;
					}
				},
			});
		}
	};




	$(function() {
		$.ajax({url: './components/currencies.json', async: false, cache: {cache: true,expire: 86400,}, success: function(rr) { rest = rr; }, });
		function start_server(fix=false) {
			$('[type="number"]').format();
			syncBalance(fix),syncPrices();
		}


		let action = cn($_GET('action'), 'string', 'buy');
		let countrie = cn($_GET('countries'), 'string', 'AF');
		let currency = cn($_GET('currency'), 'string', 'AFN');

		$('.template-list').load('%{URL}%components/templates.php',function(html){
			$('#calculator').html($('template[data-usage="calculator"]').html());
			$('._currency_symbol_b').val(currency).not('input').html(currency);
			$('#search-form').html(Mustache.render($('template[data-usage="search-form"]').html(), rest)).find('select option[value="' + countrie + '"],select option[value="' + currency + '"],input[value="' + action + '"]').attr({selected:true, checked:true,}).end().html();
			$('#filter-form').html(Mustache.render($('template[data-usage="filter-form"]').html(), rest)).find('select option[value="' + countrie + '"],select option[value="' + currency + '"],input[value="' + action + '"]').attr({selected:true, checked:true,}).end().html();
			start_server(true);
			ads_template = document.querySelector('template[data-usage="ads-item"]').innerHTML;
			Mustache.parse(ads_template);
		},{render:false});

		$(document)
		.on('arc.modal.hide', '.modal-ads-item', function(event) {
			event.preventDefault(), $(this).remove();
		})
		.on('click', '[ads-item]:not(.ads-locked)', function(event) {
			event.preventDefault();
			el = $(this);
			el.data('msg',el.find('.msg').val()),
			$('body').append(Mustache.render($('template[data-usage="modal"').html(),el.data()));
		})
		.on('click', '.select-price-target', function(event) {
			event.stopPropagation();
			trg = $(this).parents('[ads-item]'),
			$('.calculator_b')
			.val(trg.data('price'))
			.trigger('keyup')
			.select();
			$('._currency_symbol_b').html(trg.data('currency'));
		})
		.on('click', '.block_window .close', function(event) {
			event.preventDefault();
			if (typeof downloadAds_Ajax!='undefined') {downloadAds_Ajax.abort();}
			$(this).parents('.block_window').fadeOut(500),
			$(this).find('[type="submit"]').toggleClass('is-loading',false);
		})
		.on('change', 'form#search-form [name]', function(event) {
			event.preventDefault();
			if (typeof downloadAds_Ajax!='undefined') {downloadAds_Ajax.abort();}
			$.url("?"+$(this).parents('form').serialize());
		})
		.on('change', '[name="countries"]', function(event) {
			event.preventDefault(),
			$('option[data-countrie="' + $(this).val() + '"]').attr('selected', true).siblings('option').attr('selected', false);
			$('[ads-item]')
			.toggleClass('is-hidden-countrie',true)
			.filter(function(){
				return (
					($(this).data('country').toLowerCase()==cn($_GET('countries'), 'string', false).toLowerCase())
				);
			})
			.toggleClass('is-hidden-countrie',($(this).val().length > 0 ? true : false));
			$.url("?"+$(this).parents('form').serialize());
		})
		.on('keyup', '[name="bankname"]', function(event) {
			input = $(this);
			v = input.val().replace(/(\W+?)/gi, '').toLowerCase();
			var addCls = (v.length>0);
			$('[ads-item]')
			.removeClass('is-hidden-filter')
			.not('[data-bank_filter*="' + v + '"]')
			.toggleClass('is-hidden-filter', addCls);
		})
		.on('keyup', '[name="amount"]', function(event) {
			event.preventDefault();
			input = $(this);
			v = parseInt(input.val());
			$('[data-ad_id]')
			.addClass('is-hidden-amount')
			.filter((e,item)=>{
				return (parseFloat($(item).data('min_amount'))<=v && parseFloat($(item).data('max_amount'))>=v);
			})
			.removeClass('is-hidden-amount');
		});

	});
	/**********************************************************************************************/
	</script>
</body>

</html>