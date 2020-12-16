<?php if ((defined('COGS')+($_SERVER['REQUEST_METHOD']==='POST'))<1) {exit();}?>
<config>{"title":"TucambioToday | Solicitar Acceso"}</config>

<section class="column is-full arc-tab register-form">
	<div class="field has-addons mb-5">
		<div class="control w-flex">
			<input class="input has-placeholder is-shadowless has-text-black bl-radius-1" autofocus="" type="text" name="token" placeholder="Credencial">
		</div>
	</div>
	<div class="field mb-5">
		<div class="control has-icons-right">
			<input class="input is-shadowless has-text-black" type="password" name="password" placeholder="Clave de acceso">
			<span class="icon is-right c-pointer c-events-auto" data-reveal='[name="password"]' data-toggle-css>
				<i class="fa fa-eye toggle-to-hide"></i>
				<i class="fa fa-eye-slash toggle-to-show"></i>
			</span>
		</div>
	</div>
	<div class="column is-6 is-offset-6 pr-0">
		<button type="submit" class="button login-btn is-info w-100 is-outlined">
			<i class="fa fa-arrow-right"></i>
		</button>								
	</div>
</section>

<script async>
	$(function () {
		/*Document Triggers*/
		$(document)
		.on('click', '.login-btn', function(event) {
			event.preventDefault(),
			form = $(this).parents('#form-loader');
			$('.fields')
			.tab({
				before:function(panel){
					tb = this;
					$.ajax({
						url: '%{URL}%api/user/register',
						data: __pop(Object.entries(form.find('input')).map((item)=>{
							return $(item[1]).is('input') ? item[1].name+'='+item[1].value : null;
						})).join('&'),
						beforeSend:function(){
							$('#form-loader').find('input,[type="submit"]').attr('disabled',true).toggleClass('is-loading',true);
						},
						error:function(er){console.log(er); },
						success:function(resp){
							$('#form-loader').find('input,[type="submit"]').attr('disabled',false).toggleClass('is-loading',false);
							if (evaluate(resp)) {
								window.location.href  = '/';
							}
							else{tb.stop();}
						}
					});
				},
			},'nextTab');
		});
	});
</script>