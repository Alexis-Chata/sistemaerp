$(document).ready(function(){
	$('#tablaMostrar').hide();
	var idordenventa=0;
	var contadorA=0;
	if($('#tblOrdenes tbody tr').length ==0){
		$('#divPedidos').hide();
		$('#tablaMostrar').show();
	}
	$('.btnVerDetalle').click(function(e){
		e.preventDefault();
		$('#tblOrdenes tr').removeClass();
		$(this).parents('tr').addClass('active-row');
		var url1 = "/ordenventa/buscarfactura/" + $(this).parents('tr').find('.txtIdOrden').val();
		var url2 = $(this).attr("href");
		idordenventa=$(this).parents('tr').find('.txtIdOrden').val();
		cargaDatosOrden(url1);
		cargaDetalleOrdenVenta(url2);
	});
	
/*Quitar un producto de la Orden*/
	$('body').on('click', '.btnEliminar', function(e){
		e.preventDefault();
		$fila = $(this).parents('tr').hide().find('.txtEstado').val('0');
	});
	$('#btnretornar').click(function(e){
		e.preventDefault();
		if (confirm('¿Esta seguro de retornar a Ventas?')) {
			if (contadorA>0) {
					$(this).attr('disabled','disabled');
			}else{
				retornarVentas(idordenventa);
				$('#btnretornar').hide();
				$('#btnAprobar').hide();
				$('#btnDesaprobar').hide();
			}
			contadorA++;
		}
		
		
	});
	
/*Editar la cantidad de productos de la orden*/
	$('body').on('click', '.btnEditar', function(e){
		e.preventDefault();
		$(this).parents('tr').find('.txtCantidadAprobada').removeAttr('readonly').focus();
	});
	
/*Cambio de datos*/
	$('body').on('blur', '.txtCantidadAprobada, .txtPrecioVenta, .txtDescuento', function(){
		$(this).attr('readonly',true);
		var parentElement = $(this).parents('tr');
		var nextElement = ($(this).is('.txtDescuento'))?$(parentElement.next().find('.txtCantidadAprobada')):$(this).parents('td').next().find('input:text');
		nextElement.removeAttr('readonly').focus();
		calculaTotal(parentElement);
	});
	
/*Estado del orden al enviar el formulario*/
	$('#btnAprobar').click(function(){
		$('#txtEstadoOrden').val(1);
		$('#btnretornar').hide();
		$('#btnAprobar').hide();
		$('#btnDesaprobar').hide();
		if (contadorA>0) {
			$(this).attr('disabled','disabled');
		}
		contadorA++;
	});
	$('#btnDesaprobar').click(function(e){
		if (confirm('¿Esta seguro de Desaprobar la Orden?')) {
			$('#txtEstadoOrden').val(2);
			$('#btnretornar').hide();
			$('#btnAprobar').hide();
			$('#btnDesaprobar').hide();
			if (contadorA>0) {
				$(this).attr('disabled','disabled');
			}
			contadorA++;
		}else{
			e.preventDefault();
		}
		
	});
});

function cargaDetalleOrdenVenta(url){
	$.post(url, function(data){
		$('#tblProductosGuia tbody').html(data);
		
	});
}

function cargaDatosOrden(url){
	$.getJSON(url, function(data){
		$('.important:eq(0)').val(data.codigov+' - '+data.fechaguia);
		$('#txtIdOrden').val(data.idordenventa);
		
		$('#txtCliente').val(data.cliente);
		$('#txtIdCliente').val(data.idcliente);
		$('#txtFechaGuia').val(data.fechaguia);
		$('#txtRucDni').val(data.rucdni);
		$('#txtDireccion').val(data.cdireccion);
		$('#txtTelefono').val(data.ctelefono);
		$('#mventas').val(data.mventas);
		$('#divCondicionPedido').html(data.observaciones);
		$('.inline-block input').exactWidth();
		$('.show:eq(0),.show:eq(1),.show:eq(2)').click();

		cargaPosicion();
		ultimaOrden();
		deudatotal();

		//$('#fsObservaciones ul li:lt(3)').remove();
		if($('#fsObservaciones ul').length){
			$('#fsObservaciones').show();
		}else{
			$('#fsObservaciones').hide();
		}
		var existe = $('#fsObservaciones:contains("Porcentaje Facturacion")').length;
		if(existe){
			$('.gbfacturacion').show();
			$('#txtPorcentajeFacturacion').focus();
		}else{
			$('.gbfacturacion').hide();
		}
	});
}

function calculaTotal(elementParent){
	var cantidadAprobada = parseInt(elementParent.find('.txtCantidadAprobada ').val()).toFixed(2);
	var precioVenta  = parseFloat(elementParent.find('.txtPrecioVenta').val()).toFixed(2);
	var descuento  = parseFloat(elementParent.find('.txtDescuento').val()).toFixed(2);
	var total = parseFloat((cantidadAprobada * precioVenta) - descuento).toFixed(2);
	elementParent.find('.txtTotal').val(total);
	var montoTotal=0;
	$('.txtTotal').each(function(){
		montoTotal += parseFloat($(this).val().replace(',',''));
	});
	var subTotal = (montoTotal - (montoTotal * 0.19)).toFixed(2);
	var igv = (montoTotal * 0.19).toFixed(2);
	var Ttotal = montoTotal.toFixed(2);
	$('#txtSubTotal').val(subTotal);
	$('#txtIgv').val(igv);
	$('#txtTotal').val(Ttotal);
}

function retornarVentas(idordenventa){
	$.ajax({
		url:'/cobranza/retornarVentas',
		type:'post',
		dataType:'json',
		data:{'idordenventa':idordenventa},
		success:function(resp){
			if (resp.verificacion==true) {
				location.reload();
			}
			
		}
	});
}