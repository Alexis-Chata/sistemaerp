$(document).ready(function(){
	var validacion=false;
	$('#liFecha').hide();
	$('.liRangoFecha').hide();
	$('#divDetalleOrdenVenta').hide();
	msboxTitle = "Guia Pedidos del Vendedor";
	var codigoInicial=$('#codigoVendedor').val();
	$('#lstLinea').change(function(){
		cargaSubLinea();
	});
	$('#btnConsultar').click(function(e){
		e.preventDefault();
		cargaTabla();
	});
	$('input[name="rbFiltro"]').change(function(){
		$('#divDetalleOrdenVenta').hide();
		$('#txtFecha').val('');
		$('#txtFechaInicio').val('');
		$('#txtFechaFinal').val('');
		if(this.value == "1"){
			$('#liFecha').hide();
			$('.liRangoFecha').hide();			
		}else if(this.value == "2"){
			$('#liFecha').hide();
			$('.liRangoFecha').hide();
		}else if(this.value == "3"){
			$('#liFecha').hide();
			$('.liRangoFecha').hide();
		}else if(this.value == "4"){
			$('#liFecha').show();
			$('.liRangoFecha').hide();
		}else{
			$('#liFecha').hide();
			$('.liRangoFecha').show();
		}
	});
//Mostrar el detalle de la orden de pedido
	$('.btnDetalleOrdenVenta').click(function(e){
		e.preventDefault();
		$('#tblOrdenVenta tr').removeClass();
		$(this).parents('tr').addClass('active-row');
		var ruta = $(this).attr('href');
		mostrarDetalleOrdenVenta(ruta);
	});

/* Lista de busqueda*/
	$("#seleccion").change(function(){
		var id=$("#seleccion option:selected").text();
		var url='/vendedor/lista/'+id;
		window.location=url;
	});

	/*Cancelar el registro*/
	$('#btnCancelar').on('click',function(e){
		e.preventDefault();
		window.location = '/vendedor/lista/';

	});

	$('#codigoVendedor').on('keyup',function(e){
		e.preventDefault();
		
		if ($(this).val()!=codigoInicial) {
			codigo=$('#codigoVendedor').attr('value');
			$.ajax({
				url: '/vendedor/validarCodigo/',
				type: 'POST',
				async: false,
				dataType:'json',
				data:{'codigo':codigo},
				success: function(respuesta){
					//console.log(respuesta)
					$('#error').text(respuesta.error);
					validacion=respuesta.verificado;
					if(codigo==""){
						validacion=false;
						$('#error').text('');
						$('#error').css({'color':'red'});
						$('#error').text("Codigo Vacio");
					}else if (validacion==true) {
						$('#error').css({'color':'green'});
						//$('#codigoVendedor').attr('readonly','readonly');
						
					}else{
						$('#error').css({'color':'red'})
					};
	
				},
				error: function(jqXHR, status, error) {
				//console.log(status)
				//console.log(error)
				}
			});			
		}else if($(this).val()==""){
			$('#error').css({'color':'red'});
			$('#error').text("Codigo Vacio");
			validacion=false;
		}else if ($(this).val()==codigoInicial && $(this).val()!="" && $(this).val()!="0") {
			$('#error').css({'color':'green'});
			$('#error').text('Codigo Actual');
			validacion=true;
		}
		console.log(codigoInicial);
		console.log(validacion);
	});

	$('#enviar').on('click',function(e){
		
		if (validacion==false) {
			e.preventDefault();
			alert("Codigo Invalido");
		};
	});
	
	$('#enviarActualiza').on('click',function(e){
		
		if (validacion==false) {
			e.preventDefault();
			alert("Codigo Invalido");
		};
	});
	
});
msboxTitle = "Guia Pedidos Vendedor";

/*Carga tabla guia de pedidos*/
function cargaTabla(){
	var fecha = $('#txtFecha').val();
	var fechaInicio = $('#txtFechaInicio').val();
	var fechaFinal = $('#txtFechaFinal').val();
	filtro = $('input[name="rbFiltro"]:checked').val();
	mensaje = "";
	if(filtro == "4"){
		if(fecha == ""){
			mensaje = "Seleccione correctamente la fecha a buscar.";
		}
	}else if(filtro == "5"){
		if(fechaInicio == "" || fechaFinal ==""){
			mensaje = "Seleccione correctamente el rango de fecha a buscar.";
		}
	}else{
		mensaje = "";
	}
	if(mensaje!=""){
		$.msgbox(msboxTitle, mensaje);
	}else{
		ruta = "/ordenventa/listaxvendedor/";
		$.post(ruta, {filtro: filtro, fecha: fecha, fechaInicio: fechaInicio, fechaFinal: fechaFinal}, function(data){
			$('#tblOrdenVenta tbody').html(data);
			$('#tblOrdenVenta').show();
		});
	}
}

//Mostrar detalle de la orden de compra
function mostrarDetalleOrdenVenta(ruta){
	$.post(ruta, function(data){
		$('#divDetalleOrdenVenta').show();
		$('#tblDetalleOrdenVenta tbody').html(data);
		if($('#txtObservacionVentas').val().length){
			$('#liObservacionVentas').show();
		}else{
			$('#liObservacionVentas').hide();
		}
		if($('#txtObservacionCobranzas').val().length){
			$('#liObservacionCobranzas').show();
		}else{
			$('#liObservacionCobranzas').hide();
		}
		if($('#txtObservacionCreditos').val().length){
			$('#liObservacionCreditos').show();
		}else{
			$('#liObservacionCreditos').hide();
		}
	});
}

/*Messagebox
-------------------------------------------------------------- */
	function execute(){
	    var marginTop = "-" + ($("#msgbox").height() / 2) + "px";
	    var marginLeft = "-" + ($("#msgbox").width() / 2) + "px";
	    $("#msgbox").css({"margin-top":marginTop, "margin-left":marginLeft});
	}