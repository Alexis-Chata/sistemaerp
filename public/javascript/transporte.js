$(document).ready(function(){
    
    $('.pagTransporte').click(function () {
            var page = $(this).data('pag');
            $('#frmBusquedaTransporte').attr('action', '/transporte/busca/' + page);
            $('#frmBusquedaTransporte').submit();
            return false;
        });
        
	$('#txtTransporte').keyup(function(){
		if(this.value==""){
			$('#frmTransporte input').val('');
		}
	});
	
	$('body').on('click','#btnAgregarTransporte', function(e){
		e.preventDefault();
		var msgboxMessage = "El transporte <strong>" + $('#txtTransporte').val() + "</strong> ya esta asignado al Cliente.";
		agregaTransporte(msgboxMessage);
	});
	
	$('body').on('click','#btnGuardaTransporte', function(e){
		e.preventDefault();
		var msgboxMessage = "El transporte <strong>" + $('#txtTransporte').val() + "</strong> ya existe.";
		agregaTransporte(msgboxMessage);
	});
	$('#frmTransporte').submit(function(){
		if(!$('.required').valida()){
			return false;
		}else{
			return true;
		}
	});
	/*Cancelar el registro de transporte*/
	$('#btnCancelar').on('click',function(e){
		e.preventDefault();
		
		window.location = '/transporte/lista/';
	});

	/*Verificar si ya esta registrado el transpote*/
	$('#txtNombreTransporte').blur(function(){
		existeTransporte();
	});
	
	/*Verifica Ruc*/
	$('#txtRuc').blur(function(){
		existeRucTransporte();
	});

	/* Lista de busqueda*/
	$("#seleccion").change(function(){
		var id=$("#seleccion option:selected").text();
		var url='/transporte/lista/'+id;
		window.location=url;
	});
});
var msgBoxTitle="Transporte";
//Agrega el nuevo transporte ingresado en el formulario
function agregaTransporte(msgboxMessage){
	var idTransporte = $('#txtIdTransporte').val();
	var asignado = 1;
	$('#lstTransporte option').each(function(){
		if(this.value != "" && idTransporte == $(this).val()){
			$("#msgbox, #bgTransparent").hide();
			$.msgbox("Transporte Nuevo", msgboxMessage);
			asignado = 0;
			return false;
		}
	});
	if(asignado == 1){
		var url = '/cliente/agregatransporte/';
		if($('#txtIdCliente').length){
			url += $('#txtIdCliente').val();
		}
		var data = $('#frmTransporte').serialize();
		$.ajax({
			type: 'POST',
			url:	url,
			cache: false,
			data:	data,
			success:	function(dato){
				$('#lstTransporte').html(dato);
				$("#msgbox, #bgTransparent").remove();
			}
		});
	}
}

function existeTransporte(){
	var transporte = $('#txtNombreTransporte').val();
	var ruta = "/transporte/verificarNombre/" + transporte;
	var existe=0;
	$.ajax({
		async:false,
		url: ruta,
		type: "POST",
		dataType: "json",
		success: function(data){existe = data.existe;}
	});
	if(existe == 1){
		$.msgbox(msgBoxTitle,'El transporte <strong>' + transporte + "</strong> ya existe.");
		$('#msgbox-ok, #msgbox-cancel, #msgbox-close').click(function(){
			$('#txtNombreTransporte').val('').focus();
		});
		return false;
	}else{
		return true;
	}
}
function existeRucTransporte(){
	var ruc = $('#txtRuc').val();
	var ruta = "/transporte/verificarRuc/" + ruc;
	var existe=0;
	$.ajax({
		async:false,
		url: ruta,
		type: "POST",
		dataType: "json",
		success: function(data){existe = data.existe;}
	});
	if(existe == 1){
		$.msgbox(msgBoxTitle,'El Ruc <strong>' + ruc + "</strong> pertenece a otro transporte.");
		$('#msgbox-ok, #msgbox-cancel, #msgbox-close').click(function(){
			$('#txtRuc').val('').focus();
		});
		return false;
	}else{
		return true;
	}
}