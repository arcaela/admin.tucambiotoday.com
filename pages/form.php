<?php

if (!empty($_SESSION['login_key'])&&$_GET['form']!='seller') {
	if (!empty(user::accounts()[$_SESSION['login_key']])) {
		header('Location: /');
		exit();
	}
}
?>
<!DOCTYPE html>
<html>
<head>
	<?php
		head(array(
			'%{URL}%cdn/css/app.css',
			"%{URL}%cdn/css/prism.css",
			"%{URL}%cdn/css/intlTelInput.css",
		));
	?>
	<title>Tucambiotoday</title>
	<link rel="icon" href="%{URL}%cdn/img/favicon.png">
</head>
<body style="background:var(--color-facebook)">

<div class="container is-fluid wrapped pt-10-desktop pb-2">
	<div class="column is-8-desktop is-full-mobile is-offset-2-desktop p-0-mobile">
		<div class="box contain m-0 b-fade-1-table b-radius-1-desktop b-radius-0 p-relative b-fade-1-desktop p-0 has-background-none-touch is-shadowless-touch">
			<div class="columns m-0">
				<div class="column is-6-desktop is-full-mobile">
					<div class="column"><figure class="p-0 p-2 m-0 has-text-centered box" style="height: 50px;background:#fff url(%{URL}%cdn/img/logo.png) no-repeat center /auto 40px;"></figure></div>
					<div class="has-background-facebook bt-radius-1 c-default <?php echo $_GET['form']=='seller' ? '' : 'is-hidden'; ?>" style="margin-right: .75rem;margin-left: .75rem;">
						<div class="image has-text-white has-text-centered pt-2 pb-2 bt-radius-1" style="background:rgba(0,0,0,0.05);">Tasa de transacción</div>
						<div class="image has-text-white has-text-centered" style="height: 100px;line-height: 100px;">
							<div class="has-text-centered c-default w-100 is-size-2 has-text-weight-bold" onselectstart="return false;" style="color: #dfdfdf14;background: #ffffff;-webkit-background-clip: text;-moz-background-clip: text;background-clip: text;text-shadow: 0px 0px 1px rgb(255, 255, 255);"><span class="price_sync" data-decimals="0">0</span><input type="hidden" class="price_sync" value="0"> Bs/€</div>
						</div>
					</div>
					<form class="fields arc-tabs columns is-mobile" id="form-loader" style="min-height:250px;"></form>
					<div class="content is-size-7 is-hidden-tablet has-background-white p-1 b-radius-1"> La tasa de transacción se mantendrá vigente durante un periodo no mayor a <b>30 minutos</b>, según nuestra <a href="#">política</a> de transacciones. </div>
				</div>
				<div class="column is-6-desktop is-hidden-mobile p-relative" style="background-image: url(%{URL}%cdn/img/logo.png),url(%{url}%cdn/img/network.png);background-position: center top calc(50% + 80px),center top 50%;background-repeat: no-repeat;background-size: 150px,50%;"></div>
			</div>
		</div>
	</div>
</div>


<div class="container is-fluid">
	<div class="columns is-mobile" style="width: 66.6666666667%;margin-left: calc((100% / 12) * 2);">
		<div class="column has-text-centered is-4 is-offset-4">
			<a href="/" class="has-text-white">
				<img src="%{URL}%cdn/img/favicon.png" class="p-1 b-radius-10" style="background: #fff;box-shadow: #00000047 0px 0px 7px 1px;height: 30px;">
			</a>
		</div>
	</div>
</div>




<?php
	compressFiles(array(
		'%{URL}%cdn/js/jquery.min.js',
		'%{URL}%cdn/js/jquery.number.js',
		'%{URL}%cdn/js/intlTelInput.js',
		'%{URL}%system/assets/js/arc.js',
		'%{URL}%cdn/js/all.js',
		'%{URL}%cdn/js/app.js',
	));
?>
<script async type="application/javascript">
	$(function(){
		$('#form-loader').load('%{URL}%components/form/<?php echo $_GET["form"]; ?>.php',function(){
			$('html,body').scrollBottom(1),
			$('.fields').tab();
		},{
			type:'post',
			cache:{
				cache:false,
				key:'form_<?php echo $_GET["form"]; ?>',
				expire:(60*60),
			},
		});

	});
</script>
</body>
</html>