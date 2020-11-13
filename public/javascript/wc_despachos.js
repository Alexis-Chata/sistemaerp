$(document).ready(function(){
	$('#tablaMostrar').hide();
	if($('#tblOrdenes tbody tr').length ==0){
		$('#divPedidos').hide();
		$('#tablaMostrar').show();
	}
	$('.btnVerDetalle').click(function(e){
		e.preventDefault();
		$('#tblOrdenes tr').removeClass();
		$(this).parents('tr').addClass('active-row');
		//$(#frmdespacharorden).show();
		var url1 = "/ordenventa/buscarfactura/" + $(this).parents('tr').find('.txtIdOrden').val();
		var url2 = $(this).attr("href");
		//alert(url2);
		limpiar();
		cargaDatosOrden(url1);
		cargaDetalleOrdenVenta(url2);
	});
	
/*Quitar un producto de la Orden*/
	$('body').on('click', '.btnEliminar', function(e){
		e.preventDefault();
		$fila = $(this).parents('tr').hide().find('.txtEstado').val('0');
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
	
/*Estado del orden al enviar el formulario

REVISAR

*/
	$('#btnAprobar').click(function(){
		$('#btnAprobar').hide();
		$('#txtDespachado').val(1);
	});
});

function cargaDetalleOrdenVenta(url){
	$.post(url, function(data){
		$('#tblProductosGuia tbody').html(data);
		$('.show:eq(3)').click();
	});
}

function cargaDatosOrden(url){
	$.getJSON(url, function(data){
		$('#txtIdOrden').val(data.idordenventa);
		$('#txtCliente').val(data.cliente);
		$('#txtFechaGuia').val(data.fechaguia);
		$('#txtRucDni').val(data.rucdni);
		$('#txtDireccion').val(data.cdireccion);
		$('#txtTelefono').val(data.ctelefono);
		$('#divCondicionPedido').html(data.observaciones);
		$('.inline-block input').exactWidth();
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
		$('.important:eq(0)').val(data.codigov + ' / ' +data.fechaguia);
		$('.addicional-informacion:eq(1)').html('(' + data.cliente + ')');
		$('.show:eq(0), .show:eq(2)').click();
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
function limpiar(){
	$("#frmdespacharorden")[0].reset();
}