$(document).ready(function(){
	$('form input:first').focus();
	$('form').on('keypress', function(e){
		if(e == 13){
			return false;
		}
	});
	$('input').on('keypress', function(e){
		if(e.which == 13){
			return false;
		}
	});
	$('body').on('keyup','input.required', function(){
		if($.trim($(this).val()).length >=1){
			$(this).next('span.error').remove();
		}else{
			$(this).next('span.error').remove();
			$(this).after('<span class="error">*</span>');
		}
	});
	$('body').on('change','.required', function(){
		if($.trim($(this).val()).length >=1){
			$(this).next('span.error').remove();
		}else{
			$(this).next('span.error').remove();
			$(this).after('<span class="error">*</span>');
		}
	});
	$('body').on('keydown', 'input.numeric', function(e){
		soloNumero(e);
	});
	$('input.email').keyup(validaEmail);
	/*$('form input[type="submit"]').click(function(){
		validaCampoObligatorio();
		validaEmail();
		validaSelect();
		if($('span.error').length){
			return false;
		}
	});*/
});

/*Validacion de e-mail
-------------------------------------------------------------- */
	function validaEmail(){
		if($.trim($('.email').val()).length >=1){
			if(($('.email').val().indexOf('@', 0) == -1 || $('.email').val().indexOf('.', 0) == -1) && $('.email').siblings('span.error').length==0){
				$('.email').after('<span class="error">La direcci&oacute;n e-mail parece incorrecta</span>');	
			}else{
				$('.email').next('span.error').remove();
			}
		}else{
			$('.email').next('span.error').remove();
		}
	}
	
/*Validacion de numero
-------------------------------------------------------------- */
	function soloNumero(e){
		if (e.keyCode == 110 || e.keyCode == 190 || e.keyCode == 8 || e.keyCode == 9 || (e.keyCode >= 37 && e.keyCode <= 40)){
			
		}else if((e.keyCode < 48 || e.keyCode > 57) && (e.keyCode < 96 || e.keyCode > 105 )) {
			e.preventDefault();
		}
	}
	
/*Validacion de campos obligatorios
-------------------------------------------------------------- */
	function validaCampoObligatorio(){
		var exito = 0;
		var x = 0;
		var y = 0;
		$('input.required').each(function(){
			if(($.trim($(this).val())).length==0){
				if($(this).siblings('span.error').length==0){
					$(this).after('<span class="error">*</span>');
				}
				exito=0;
			}else{
				$(this).next('span.error').remove();
				exito=1;
			}
		});
		return exito;
	}

function validaSelect(){
	/*var $valor = "";
	$('select.required').each(function(){
		$valor += $(this + 'option:selected').val();
	});
	if($valor:contains('0')){
		if($('select.required').siblings('span.error').length==0){
			$('select.required').parent().append('<span class="error">Esta informaci&oacute;n es obligatoria.</span>');
		}
		
	}else{
		$('select.required').siblings('span.error').remove();
	}*/
	$('select.required').each(function(){
		if($(this + 'option:selected').val() == "0"){
			if($(this).siblings('span.error').length==0){
				$(this).parent().append('<span class="error">Esta informaci&oacute;n es obligatoria.</span>');
			}
		}else{
			$(this).siblings('span.error').remove();
		}
	});
	/*if($('select.required option:selected').val()=="0"){
		if($('select.required').parent('li').child('span.error').length==0){
			$('select.required').parent().append('<span class="error">Esta informaci&oacute;n es obligatoria.</span>');
		}
	}else{
		$('select.required').siblings('span.error').remove();
	}*/
}