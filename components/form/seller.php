<?php
if (!function_exists('cache_add')) {include(__DIR__.'/../../system/autoload.php'); }
 if ((defined('COGS')+($_SERVER['REQUEST_METHOD']==='POST'))<1) {exit();}?>
<config>{"title":"TuCambioToday | Enviar dinero"}</config>


<section class="column is-full arc-tab pt-0">
	<div class="card b-radius-1 is-shadowless-desktop bt-radius-0">
		<div class="card-content">
			<div class="media">
				<div class="media-content">
					<p class="title is-4">Enviar dinero a Venezuela</p>
					<p class="subtitle is-6">Método cambiario con tasa del día.</p>
				</div>
			</div>
			<div class="field has-addons has-text-info mb-0">
				<div class="control w-flex">
					<input class="input b-0 has-text-right p-0 h-100 is-shadowless is-size-2 has-text-info" type="number" data-decimals="0" id="amount" data-exchange="#feed" data-formula="this>=25&&(this&&this<50)?3:0">
				</div>
				<div class="control w-flex is-size-2 h-auto has-text-weight-bold">€</div>
			</div>
			<div class="content mt-6">
				<div class="columns is-mobile" sstyle="border: solid 1px var(--color-info);">
					<div class="column p-1">Comisión (25€ - 49€)</div>
					<div class="column p-1 has-text-weight-bold has-text-right"><span data-decimals="0" data-k="." id="feed" data-exchange="#subtotal" data-formula="[#amount]>24?[#amount]-this:0">0</span> · €</div>
				</div>
				<div class="columns is-mobile" sstyle="border: solid 1px var(--color-info);">
					<div class="column p-1">Sub Total</div>
					<div class="column p-1 has-text-weight-bold has-text-right"><span id="subtotal" data-exchange="#bonus" data-formula="this>99?100*this:0">0</span> · €</div>
				</div>
				<div class="columns is-mobile" sstyle="border: solid 1px var(--color-info);">
					<div class="column p-1">Bonificación (100€)</div>
					<div class="column p-1 has-text-weight-bold has-text-right"><span id="bonus" data-exchange="#total" data-formula="((<#subtotal>*[input.price_sync])+<#bonus>)">0</span> · Bs</div>
				</div>
				<div class="columns is-mobile" sstyle="border: solid 1px var(--color-info);">
					<div class="column p-1">Total</div>
					<div class="column p-1 has-text-weight-bold has-text-right"><span data-decimals="2" data-k="." id="total">0</span> · Bs</div>
				</div>
			</div>
		</div>
		<div class="columns is-mobile m-0">
			<div class="column">
				<button type="button" class="button next-tab is-info is-fullwidth is-outlined"><i class="fa fa-angle-right" style="margin-top: 3px;"></i></button>
			</div>
		</div>
	</div>
</section>

<section class="column is-full arc-tab pt-0">
	<div class="card b-radius-1 is-shadowless-desktop bt-radius-0">
		<div class="card-content">
			<div class="media">
				<div class="media-content">
					<p class="title is-4">Selección de banco</p>
					<p class="subtitle is-6">Indicanos el banco que dispones a transaccionar</p>
				</div>
			</div>
			<div class="content mt-6" id="content_accounts_data">
				<?php
					$ajax = new ajax('https://coinstant.exchange/widget/banks.php?id=50');
					$ajax->cache(true,'+30 minutes');
					if ($ajax->start()) {
						echo debuggHTML(str_replace(array('<br><br>','Disponible para ingresar en cajero automatico: 999 euros'), array('',''), $ajax->body),'script,a,i');
					}
					else{
						echo "<center> <h3>Disculpe</h3> <h5>Actualmente nuestros proveedores estan fuera de servicio</h5> </center>";
					}
				?>
			</div>
			<div class="is-size-7">Es importante que dispongas del comprobante de pago para proceder con la operación cambiaria.</div>
		</div>

		<div class="columns is-mobile m-0">
			<div class="column is-3 pr-0">
				<button type="button" class="button prev-tab is-info is-fullwidth is-outlined br-radius-0"><i class="fa fa-angle-left" style="margin-top: 3px;"></i></button>
			</div>
			<div class="column pl-0">
				<button type="button" class="button next-tab is-info is-fullwidth is-outlined bl-radius-0 bl-0"><i class="fa fa-angle-right" style="margin-top: 3px;"></i></button>
			</div>
		</div>
	</div>
</section>

<section class="column is-full arc-tab pt-0">
	<div class="card b-radius-1 is-shadowless-desktop bt-radius-0">
		<div class="card-content">
			<div class="media">
				<div class="media-content">
					<p class="title is-4">Tus datos</p>
					<p class="subtitle is-6">Método cambiario con tasa del día.</p>
				</div>
			</div>
			<div class="field has-addons mb-2">
				<div class="control w-flex">
					<input class="input has-placeholder is-shadowless has-text-black bl-radius-1 br-radius-0" type="text" name="name" placeholder="Nombres">
				</div>
				<div class="control w-flex">
					<input class="input has-placeholder is-shadowless has-text-black br-radius-1 bl-radius-0" type="text" name="lastname"  placeholder="Apellidos">
				</div>
			</div>
			<div class="field">
				<div class="control is-expanded mb-5">
					<input class="input" type="text" name="num_dcm" placeholder="Documento de identidad" required="">
				</div>
			</div>
			<div class="field mb-5">
				<div class="control">
					<input class="input phone no-placeholder" type="tel" name="phone">
				</div>
			</div>
		</div>
		<div class="columns is-mobile m-0">
			<div class="column is-3 pr-0">
				<button type="button" class="button prev-tab is-info is-fullwidth is-outlined br-radius-0"><i class="fa fa-angle-left" style="margin-top: 3px;"></i></button>
			</div>
			<div class="column pl-0">
				<button type="button" class="button next-tab is-info is-fullwidth is-outlined bl-radius-0 bl-0"><i class="fa fa-angle-right" style="margin-top: 3px;"></i></button>
			</div>
		</div>
	</div>
</section>





<script async="">
	function priceEur(fix=false){
		$.ajax({
			url: '%{URL}%int',
			data:{task_day:''},
		})
		.always(function(rr) {
			var crcp = parseFloat(rr);
			$('.next-tab,.prev-tab')
			.attr('disabled',isNaN(crcp));
			$('.price_sync').format(crcp);
			setTimeout(function(){
				priceEur();
			},2000);
		});
	}

	$(function () {
		priceEur();

		input = document.querySelector('.phone');
		var iti = window.intlTelInput(input, {
			nationalMode:false,
			separateDialCode:true,
			autoHideDialCode:true,
			formatOnDisplay:false,
			initialCountry: "auto",
			dropdownContainer:document.body,
			utilsScript: "./cdn/js/utils.js",
			geoIpLookup: function(callback) {
				$.getJSON('http://ip-api.com/json/', function(resp, textStatus) {
					var countryCode = (resp && resp.countryCode) ? resp.countryCode : "us";
					callback(countryCode);
				});
			},
		});


		$(document)
		.on('change', '#viewSelector', function(event) {
			event.preventDefault();
			$('#'+$(this).val()+'.banks').show().siblings('.banks').hide(0);
		})
		.on('click', '.next-tab,.prev-tab', function(event) {
			event.preventDefault();
			$('#form-loader').tab($(this).is('.prev-tab') ? 'prevTab' : 'nextTab');
		})
		.on("blusr",input, function(a,b,c) {
			var errorCode = iti.getValidationError();
			$(this)
			.toggleClass('is-danger',(iti.getNumberType()!=1||errorCode>0))
			.not('.is-danger')
			.val(iti.getNumber(1).toString().replace('+'+iti.getSelectedCountryData().dialCode,'').replace(/(\D+)/gi,' ').trim());
		});





	});
</script>