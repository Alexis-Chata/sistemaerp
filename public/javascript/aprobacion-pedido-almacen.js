$(document).ready(function(){
	$('#tablaMostrar').hide();
	var verificacion=true;
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
		cargaDatosOrden(url1);
		cargaDetalleOrdenVenta(url2);
	});
	
/*Quitar un producto de la Orden*/
	$('body').on('click', '.btnEliminar', function(e){
		e.preventDefault();
		if (confirm('¿Esta seguro de eliminar el producto|?')) {
			$fila = $(this).parents('tr').hide().find('.txtEstado').val('0');
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
		var Stock = parseInt(parentElement.find('.Stock').html());
		var cantdespachada = parseInt(parentElement.find('.txtCantidadAprobada').val());

		if (cantdespachada>Stock) {
			alert('La Cantidad Ingresa ha superado el Stock');
			parentElement.find('.txtCantidadAprobada').val(Stock);
			padre.css('background-color','red');
		}else{
			padre.css('background-color','skyblue');
			calculaTotal(parentElement);
		}

	});

	$('.txtCantidadAprobada').on('blur',function(e){
		cantidad=$(this).val();
	});
	
/*Estado del orden al enviar el formulario*/
	$('#btnAprobar').click(function(e){
		$('#btnAprobar').hide();
		$('#btnDesaprobar').hide();
		$('#imprimir').hide();
		$('.txtCantidadAprobada').each(function(){
			padre=$(this).parents('tr');
			cant=$(this).val();
			estado=padre.find('.txtEstado').val();
			stockitem=parseInt(padre.find('.Stock').html());
			console.log(estado);
			if (estado==1) {
				if (cant>stockitem) {
					verificacion=false;
					padre.css('background-color','red');
				}
			}
			console.log(verificacion);
		});
		if (verificacion) {
			$('#txtEstadoOrden').val(1);
			console.log('aprobado');

		}else{
			e.preventDefault();
			console.log('deprobado');
		}
		if (verificacion==true) {
			if (contadorA>0) {
				$(this).attr('disabled','disabled');
			}
			contadorA++;
		}
		verificacion=true;

		
	});
	$('#btnDesaprobar').click(function(e){
		if (confirm('¿Esta seguro de Desaprobar el Pedido?')) {
			$('#btnAprobar').hide();
			$('#btnDesaprobar').hide();
			$('#imprimir').hide();
			$('#txtEstadoOrden').val(2);
			if (contadorA>0) {
				$(this).attr('disabled','disabled');
			}
			contadorA++;
		}else{
			e.preventDefault();
		}
		
	});
	$('#imprimir').click(function(e){
		e.preventDefault();
		$('.btnEditar').hide();
		$('.btnEliminar').hide();
		encabezadoImpresion();
		imprSelec('muestra');
		$('.btnEditar').show();
		$('.btnEliminar').show();
		$('#contenido').html('');
	});
	$('.txtCantidadAprobada').each(function(){
		padre=$(this).parents('tr');
		cant=$(this).val();
		stockitem=parseInt(padre.find('.Stock').html());
		if (cant>stockitem) {
			verificacion=false;
		}else{
			verificacion=true;
			
		}
		console.log(verificacion);
	});
});

function cargaDetalleOrdenVenta(url){
	$.post(url, function(data){
		$('#tblProductosGuia tbody').html(data);
		$('.show:eq(1), .show:eq(3)').click();
	});
}
function encabezadoImpresion(){
	var contenido=	"<table>"+
						"<tr><th>Orden Venta</th><td>"+$('.important:eq(0)').val()+"</td> <th>Telefono</th><td>"+$('#txtTelefono').val()+"</td></tr>"+
						"<tr><th>Cliente</th><td>"+$('#txtCliente').val()+"</td> <th>RUC / DNI</th><td>"+$('#txtRucDni').val()+"</td></tr>"+
						"<tr><th>Dirección</th><td>"+$('#txtDireccion').val()+"</td> <th>Lugar</th><td>"+$('#txtLugar').val()+"</td></tr>"+
					"</table>";
	$('#contenido').html(contenido);				
}
function cargaDatosOrden(url){
	$.getJSON(url, function(data){
		$('#txtIdOrden').val(data.idordenventa);
		$('#txtCliente').val(data.cliente);
		$('#txtFechaGuia').val(data.fechaguia);
		$('#txtRucDni').val(data.rucdni);
		$('#txtDireccion').val(data.cdireccion);
		$('#txtTelefono').val(data.ctelefono);
		$('#txtLugar').val(data.lugar);
		$('#mcobranzas').val(data.mcobranzas);
		$('#divCondicionPedido').html(data.observaciones);
		$('.inline-block input').exactWidth();
		//$('#fsObservaciones ul li:lt(3)').remove();
		$('#txtordenventa').val();
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
		$('.important:eq(0)').val(data.codigov);
		$('.addicional-informacion:eq(1)').html('(' + data.cliente + ')');
		$('.show:eq(0), .show:eq(2)').click();
	});
}

function calculaTotal(elementParent){
	var cantidadAprobada = parseInt(elementParent.find('.txtCantidadAprobada ').val()).toFixed(2);
	//var Stock = parseInt(elementParent.find('.Stock ').html()).toFixed(2);
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