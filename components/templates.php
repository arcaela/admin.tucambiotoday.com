<template data-usage="search-form">
	<div class="columns is-mobile is-multiline">
	<div class="column is-4-desktop is-full-mobile">
		<div class="control is-expanded">
			<fieldset>
				<div class="switch-toggle switch-candy">
					<input id="week" name="action" type="radio" value="buy">
					<label for="week" onclick="">Comprar</label>
					<input id="month" name="action" type="radio" value="sell">
					<label for="month" onclick="">Vender</label>
					<a></a>
				</div>
			</fieldset>
		</div>
	</div>
	<div class="column is-4-desktop is-6-mobile">
		<div class="control">
			<div class="select w-100">
				<select class="w-100" name="countries" style="text-transform: uppercase;">
					{{#restAPI}} <option value="{{countrie}}">{{name}}</option> {{/restAPI}}
				</select>
			</div>
		</div>
	</div>
	<div class="column is-4-desktop is-6-mobile">
		<div class="field has-addons">
			<div class="control is-expanded">
				<div class="select w-100">
					<select class="w-100" name="currency" style="text-transform: uppercase;"> {{#restAPI}} <option value="{{currency}}" data-countrie="{{countrie}}">{{currency}}</option> {{/restAPI}} </select>
				</div>
			</div>
			<div class="control">
				<button class="button is-link w-100" type="submit">
					<i class="fa fa-sync"></i>
				</button>
			</div>
		</div>
	</div>
</div>	
</template>

<template data-usage="filter-form">
	<div class="is-multiline is-mobile container-filter-form columns m-0">
		<div class="column is-full">
			<div class="control has-icons-left"> <input type="text" class="input" placeholder="Escribe tu busqueda" name="bankname"> <span class="icon is-left"> <i class="fa fa-search"></i>
				</span>
			</div>
		</div>
		<div class="column is-full">
			<div class="control has-icons-left">
				<input type="number" class="input" data-decimals="2" placeholder="Monto a buscar" name="amount" onfocus="$(this).select();">
				<span class="icon is-left">
					<i class="fa fa-sort-numeric-up"></i>
				</span>
			</div>
		</div>
	</div>
</template>

<template data-usage="calculator">
	<div class="box has-text-centered p-3">
		<div class="field has-addons mb-0">
			<div class="control is-expanded">
				<input arc-placeholder="Vitaly" type="number" data-formula="((this/val(.sync_kraken))-1)*100" data-exchange=".__coeficient" class="sync_vitaly not-empty input has-background-white has-text-right bb-radius-0" readonly="">
				<span class="label has-text-link">Vitaly</span>
			</div>
			<div class="control is-expanded">
				<input arc-placeholder="Kraken" type="number" data-formula="((val(.sync_vitaly)/this)-1)*100" data-exchange=".__coeficient" class="sync_kraken not-empty input has-background-white has-text-right bb-radius-0" readonly="" data-formula="this" data-exchange=".__coeficient">
				<span class="label has-text-link">Kraken</span>
			</div>
			<div class="control is-expanded has-icons-right">
				<input type="number" data-decimals="2" class="__coeficient input has-background-white has-text-right bb-radius-0" readonly="">
				<span class="icon is-right">%</span>
			</div>
		</div>
	</div>
	<div class="box has-text-centered p-3">
		<div class="field mt-0 mb-0">
			<div class="control has-icons-right">
				<input type="number" class="has-text-right input bb-radius-0 b-fade-1 calculator_a sync_vitaly" data-formula="(1/val(.calculator_a)*val(.calculator_b))-(1/val(.calculator_a)*val(.calculator_b)*(val(.calculator_task)/100))" data-exchange=".calculator_result" onfocus="$(this).select();">
				<span class="icon is-right _currency_symbol_a _currency_symbol"></span>
			</div>
		</div>
		<div class="field mt-0 mb-0">
			<div class="control has-icons-right">
				<input type="number" class="has-text-right input b-radius-0 bt-0 bb-0 calculator_b" data-formula="(1/val(.calculator_a)*val(.calculator_b))-(1/val(.calculator_a)*val(.calculator_b)*(val(.calculator_task)/100))" data-exchange=".calculator_result" onfocus="$(this).select();">
				<span class="icon is-right _currency_symbol_b"></span>
			</div>
		</div>
		<div class="field mt-0 mb-0 has-addons">
			<div class="control has-icons-right">
				<input type="number" data-decimals="0" class="has-text-right input bt-radius-0 b-fade-1 calculator_task" style="line-height: 39px;" data-formula="(1/val(.calculator_a)*val(.calculator_b))-(1/val(.calculator_a)*val(.calculator_b)*(val(.calculator_task)/100))" data-exchange=".calculator_result" onfocus="$(this).select();">
				<span class="icon is-right">%</span>
			</div>
			<div class="control is-expanded has-icons-right">
				<input type="number" data-decimals="2" data-decimal="," class="has-text-right input bt-radius-0 b-fade-1 calculator_result" style="line-height: 39px;" readonly="" onfocus="$(this).select();">
				<span class="icon is-right">
					<i class="fa fa-calculator"></i>
				</span>
			</div>
		</div>
	</div>
</template>

<template data-usage="ads-item">
	<div ads-item class="column is-4-desktop is-full-mobile pt-0 {{filter-word}}" data-last_seen_minutes="{{last_seen_minutes}}" data-ad_id="{{ad_id}}" data-username="{{username}}" data-currency="{{currency}}" data-country="{{countrycode}}" data-bank_filter="{{bank_filter}}" data-bank_name="{{bank_name}}" data-price="{{price}}" data-min_amount="{{min_amount}}" data-max_amount="{{max_amount}}">
		<textarea class="msg is-hidden">{{msg}}</textarea>
		<div class="box c-default">
			<article class="media">
				<div class="media-content w-100">
					<div class="content">
						<div style="max-width: 100%;max-height: 50px;overflow-x: hidden;white-space: nowrap;" class="c-default">
							<span class="has-text-weight-bold has-text-grey">({{page}}) · {{online_provider}}: </span><span>{{bank_name}}</span>
						</div>
						<div class="c-default">
							<strong><small><a href="https://localbitcoins.com/accounts/profile/{{username}}/" target="_blank">@{{name}}</a></small></strong>
						</div>
						<section>
							<div class="field has-addons">
								<div class="control"><button class="button is-link select-price-target"><i class="fa fa-calculator"></i></button></div>
								<div class="control is-expanded">
									<input type="number" data-decimals="2" class="input has-text-right c-default" value="{{price}}" disabled="">
								</div>
								<div class="control"><button class="input c-default" disabled="">{{currency}}</button></div>
							</div>
							<div class="level">
								<div class="level-left">
									<div class="level-item">{{min_amount_format}} {{currency}}</div>
								</div>
								<div class="level-right">
									<div class="level-item">{{max_amount_format}} {{currency}}</div>
								</div>
							</div>
						</section>
					</div>
					<nav class="level is-mobile">
						<div class="level-left has-text-dark">
							<span class="level-item c-default" is-show="{{require_identification}}"><i class="fas fa-user"></i></span>
							<span class="level-item c-default" is-show="{{is_list}}"><i class="fas fa-list"></i></span>
							<span class="level-item c-default" is-show="{{sms_verification_required}}"><i class="fas fa-mobile-alt"></i></span>
						</div>
						<div class="level-right">
							<span class="level-item c-default has-text-right">{{last_seen}}</span>
						</div>
					</nav>
				</div>
			</article>
		</div>
	</div>
</template>





<template data-usage="modal">
	<div class="modal is-active modal-ads-item">
		<div class="modal-background"></div>
		<div class="modal-card">
			<button class="modal-close"></button>
			<header class="modal-card-head pt-2 pb-2">
				<div class="modal-card-title has-text-weight-bold has-text-grey c-default mb-1">{{bank_name}}</div>
				<input type="hidden" id="value-converter" value="{{price}}">
			</header>
			<section class="modal-card-body has-text-centered b-fade-1 b-0 bb-1 pt-1 pb-1 is-size-3" style="overflow-y: unset;min-height: 60px;">Términos de comercio</section>
			<section class="modal-card-body has-text-justified" style="white-space: pre-line;font-size: 13px;font-family: 'arial';text-transform: lowercase;">{{msg}}</section>
			<div class="box mb-0 b-radius-0 has-background-link">
				<div class="columns">
					<div class="column">
						<div class="field has-addons">
							<div class="control is-expanded">
								<input type="number" class="input has-background-white has-text-right" id="modal_amount_price" data-decimals="2" onkeyup="$('#modal_btc_price').val(parseFloat($(this).val()/$('#value-converter').val()));">
							</div>
							<div class="control is-expanded"><span class="button is-light">{{currency}}</span></div>
						</div>
					</div>
					<div class="column">
						<div class="field has-addons">
							<div class="control is-expanded">
								<input type="number" class="input has-background-white has-text-right" id="modal_btc_price" data-decimals="8" onkeyup="$('#modal_amount_price').val(parseFloat($(this).val()*$('#value-converter').val()));">
							</div>
							<div class="control is-expanded"><span class="button is-light">BTC</span></div>
						</div>
					</div>
				</div>
			</div>
			<footer class="modal-card-foot pt-2 pb-2">
				<a href="https://localbitcoins.com/ad/{{ad_id}}" target="_blank" class="button is-link is-pulled-right">Ir al anuncio</a>
			</footer>
		</div>
	</div>
</template>