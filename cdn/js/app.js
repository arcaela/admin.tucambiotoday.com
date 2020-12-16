function myApi(){return "2418-8e30-bcb9-86c1-ca21-bc1f-4bb0-654a";}
const apiKey = myApi();


$.fn.websocket = function(params={}){
	var container = $($(this)[0]);
	var attrs = container[0].attributes;
	var arg = {};
	for (attr in attrs) {
		if (!isNaN(attr)&&attrs[attr].name.indexOf('socket-')>=0) arg[attrs[attr].name.replace('socket-','')] = attrs[attr].value;
	}
	var params = $.extend({
		listen:false,
		room:'inv',
		url:[
			"https://blockexplorer.com/",
			"https://insight.bitpay.com/"
		],
	},arg,params);
	var callbacks = {
		'tx':function(data){
		}
	};
	var socket = io(params.url[Math.floor(Math.random() * params.url.length)]);
	socket.on('connect', function() {
	  socket.emit('subscribe', params.room);
	})
	.on(params.listen, function(data) {
		callbacks[params.listen.toLowerCase()](data);
	});	
}


$.chart = function(arg={}){
	_color = function(__set,__hide = 1) {
		return {
			low:'rgba(255, 75, 90,'+__hide+')',
			high:'rgba(255, 155, 1,'+__hide+')',
			open:'rgb(54, 135, 255,'+__hide+')',
			close:'rgb(206, 58, 253,'+__hide+')',
		}[__set.toLowerCase()];
	}
	params = Object.assign({},{
		symbol:false,
		period:'1d',
		__target:false,
		onload:function(){},
		onerror:function(err){},
		callback:function(headers,charts){},
	}, isJson(arg));
	if (!params.symbol||!params.period) {return; }
	__target = $(params.__target).id().attr('id');
	json = $.ajax({
		url: window.location.protocol+'//api.'+window.location.host+'/explorer/chart/',
		data: {
			symbol:params.symbol,
			// noCache:1,
			api_key:apiKey,
			includePrePost:true,
			range:params.period,
		},
		beforeSend:function(){
			params.onload();
		},
		error:params.onerror,
		success:function(json){
			if (evaluate(json)&&$(params.__target)[0]) {
				var ctx = document.getElementById(__target);
				try {
					if ($(ctx).is(':visible')) {
						if (typeof MyChart!=='undefined') {MyChart.destroy();}
						MyChart = new Chart(ctx.getContext('2d'),{
							type: 'time',
							data: {
								type: 'line',
								defaultFontFamily: 'Lato',
								labels: Object.values(json.chart.timestamp).map(function(time){
									return new Date(time*1000);
								}),
								datasets: Object.keys(json.chart.indicators).map(function(dataSet){
									return {
										type: 'line',
										fill: true,
										label: ucfirst(dataSet),
										data: json.chart.indicators[dataSet],
										backgroundColor: _color(dataSet,.05),
										borderColor: _color(dataSet),
										pointStyle: 'circle',
										borderWidth: 2,
										pointRadius: 3,
										pointBorderColor: 'transparent',
										pointBackgroundColor: _color(dataSet),
									};
								})
							},
							options: {
								responsive: true,
								legend: {
									display: true,
									position:'bottom',
									labels: {
										usePointStyle: true,
									},
								},
								tooltips: {
									mode: 'index',
									titleFontSize: 12,
									titleFontFamily: 'sans-serif',
									cornerRadius: 5,
									intersect: false,
								},
								scales: {
									yAxes: [{
										display: true,
										gridLines: {
											display: true,
											drawBorder: true
										},
									}],
									xAxes: [{
										type: 'time',
										display: true,
										distribution: 'series',
										time: {
											tooltipFormat: 'll'
										},
									}],
								},
							}
						});
					}
				} catch (error) {
					console.log(error);
				}
				params.callback(json.chart.headers,json.chart.indicators);
			}
		},
	});
};





$(function(){

	$(document)
	.on('click', '.navbar-burger', function(event) {event.preventDefault(), $($(this).data('target')).toggleClass('is-active'); })
	.on('click', '[data-reveal]', function(event) {
		event.preventDefault(),
		btn = $(this);
		$(btn.gdt('reveal',$.map(btn.parents('.fields').find('[type="password"]').id(),function(e){
			return '#'+e.id;
		}).join(',')))
		.switchAttr('type','text','password',function(state){
			btn.attr('data-toggle-css',(state=='text' ? 'active' : ''));
		});
	})
	.on('keyup keydown change focus blur', '.input', function(event) {
		$(this).toggleClass('not-empty',($(this).val().length>0));
	})
	.on('arc.collapse', '#_crk_', function(event,btn) {
		btn
		.switchClass('bb-radius','b-radius-0',!$(this).is(':visible'))
		.find('.icon-expand')
		.toggleClass('opened',$(this).is(':visible'));
	})
	.on('arc.alert.close', '.cookie_terms', function(event) {
		event.preventDefault(),
		setcookie("cookie_terms",1,86400000);
	});





});