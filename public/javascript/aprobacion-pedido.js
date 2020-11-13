$(document).ready(function(){
	var contadorA=0;
	var contadorD=0;
	$('#tablaMostrar').hide();
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
		$fila = $(this).parents('tr').hide().find('.txtEstado').val('0');
	});
	
/*Editar la cantidad de productos de la orden*/
	$('body').on('click', '.btnEditarItem', function(e){
		e.preventDefault();
		$(this).parents('tr').find('.txtCantidadAprobada').removeAttr('readonly').focus();
		//$(this).parents('tr').find('.txtPrecioAprobado').removeAttr('readonly');
		$(this).parents('tr').find('.txtDescuentoAprobado').removeAttr('readonly');
		//$(this).parents('tr').find('.txtPrecioFinal').removeAttr('readonly');
	});	
	
/*Cambio de datos*/
	$('body').on('blur', '.txtCantidadAprobada, .txtPrecioAprobado', function(){
		$(this).attr('readonly',true);
		var parentElement = $(this).parents('tr');
		cantidadI=parentElement.find('.cantidadInicial').val();
		
		if (verificarStockdisponible(parentElement)==true) {
			calculaTotal(parentElement);
		}else{
			parentElement.find('.txtCantidadAprobada').val(cantidadI);
			alert('No hay suficiente Stock');
		}
		//var nextElement = ($(this).is('.txtPrecioAprobado'))?$(parentElement.next().find('.txtCantidadAprobada')):$(this).parents('td').next().find('input:text');
		//nextElement.removeAttr('readonly').focus();
		//$('.txtPrecioFinal').val(parseFloat($('.txtPrecioAprobado').val())*parseFloat(1-$('.valorunico').val())).toFixed(2);
		
	});

	/*Quitar un producto de la Orden*/
	$('body').on('click', '.btnEliminarItem', function(e){
		e.preventDefault();
		$fila = $(this).parents('tr').hide().find('.txtEstado').val('0');
		padre=$(this).parents('tr');
		restaImporte(padre);
	});
	
	$('body').on('change', '.txtDescuentoAprobado, .txtPrecioFinal', function(){
		//$(this).attr('readonly',true);
		var parentElement = $(this).parents('tr');
		var idDescuento=parentElement.find('.txtDescuentoAprobado').val();
		DescuentoUnico(idDescuento,parentElement);
	});	
	$('#btnEditarOrdenVenta').click(function(e){
		e.preventDefault();
		if ($('#txtIdOrden').val()!="") {
			window.location='/ordenventa/editarordenventa/'+$('#txtIdOrden').val();
		}
	});
/*Estado del orden al enviar el formulario*/
	$('#btnAprobar').click(function(e){
		$('#btnAprobar').hide();
		$('#btnDesaprobar').hide();
		$('#txtEstadoOrden').val(1);
		if (contadorA==1 || contadorD==1) {
			e.preventDefault();
			//alert('No vuelva apretar');
			$(this).attr('disabled','disabled');
		}
		contadorA++;
	});
	$('#btnDesaprobar').click(function(e){
		if (confirm("Esta Seguro que realmente quiere Desaprobar el Pedido")) {
			$('#btnAprobar').hide();
			$('#btnDesaprobar').hide();
			$('#txtEstadoOrden').val(2);
			if (contadorA==1 || contadorD==1) {
				$(this).attr('disabled','disabled');
			}
			contadorD++;
		}else{
			e.preventDefault();
		}
		
	});
});


function cargaPosicion(){
	var idCliente = $('#txtIdCliente').val();
	var ruta = "/cliente/posicionordenventa/"+ idCliente;
	$.post(ruta, function(data){
		$('#clienteposicion').html(data);
		var saldo=parseFloat($('#idsaldo').val());
//		if(saldo<=0){
//			$.msgbox("Alerta al Crear la ORDEN","EL CLIENTE TIENE SALDO S./ " + saldo+ "<br> Verifique el Saldo que necesita ANTES de empezar a registrar su orden.");
//		}

	});

}

function cargaDetalleOrdenVenta(url){
	$.post(url, function(data){
		$('#tblProductosGuia tbody').html(data);
		$('.show:eq(3)').click();
	});
}

function cargaDatosOrden(url){
	$.getJSON(url, function(data){
		console.log(data);
		$('#txtIdOrden').val(data.idordenventa);
		$('#txtCliente').val(data.cliente);
		$('#txtIdCliente').val(data.idcliente);
		$('#txtFechaGuia').val(data.fechaguia);
		$('#txtRucDni').val(data.rucdni);
		$('#txtDireccion').val(data.cdireccion);
		$('#txtTelefono').val(data.ctelefono);
		$('#divCondicionPedido').html(data.observaciones);
		$('.inline-block input').exactWidth();
		cargaPosicion();
		//ultimaOrden();
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
		$('.addicional-informacion:eq(1)').html('(' + data.cliente + ')');
		$('.important:eq(0)').val(data.codigov);
		$('.show:eq(0), .show:eq(1),.show:eq(2)').click();
	});
}

function calculaTotal(elementParent){
    var cantidadAprobada = parseInt(elementParent.find('.txtCantidadAprobada ').val()).toFixed(0);
    var preciofinal=parseFloat(elementParent.find('.txtPrecioFinal').val()).toFixed(2);
    var total = parseFloat(cantidadAprobada * preciofinal).toFixed(2);
    elementParent.find('.txtTotal').val(total); 
	/*
	//alert(cantidadAprobada);
	var precioaprobado=parseFloat(elementParent.find('.txtPrecioAprobado').val()).toFixed(2);
	//alert(precioaprobado);
	var descuentovalorunico=parseFloat(elementParent.find('.valorunico').val()).toFixed(4);
	if (elementParent.find('.valorunico').val()=="") {
		descuentovalorunico=0;
	}
	//$('.txtPrecioFinal').val(parseFloat($('.txtPrecioAprobado').val())*parseFloat(1-$('.valorunico').val())).toFixed(2);
	var precioFinal  = parseFloat(precioaprobado*parseFloat(1-descuentovalorunico));
		elementParent.find('.txtPrecioFinal').val(precioFinal.toFixed(2));
		precioFinal=parseFloat(elementParent.find('.txtPrecioFinal').val()).toFixed(2);
	//var descuento  = parseFloat(elementParent.find('.txtDescuento').val()).toFixed(2);
	//var total = parseFloat((cantidadAprobada * precioVenta) - descuento).toFixed(2);
	*/
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
function cargaDescuento(){
	var ruta = "/descuento/listado/";
	$.post(ruta, function(data){
		$('#lstDescuento').html('<option value="">-- Descuento --' + data);
	});
}

function DescuentoUnico(id,element){
				
		var ruta = "/descuento/valorunico/"+id;

		$.getJSON(ruta, function(data){
			if(id==0){
				var dunico="0.00";
			}else{
				var dunico=data[0]["dunico"];
				var dvalor=data[0]["valor"];

			}
			
			element.find('.valorunico').val(dunico);
			element.find('.textounico').val(dvalor);
			var cantidadAprobada = parseInt(element.find('.txtCantidadAprobada ').val());
			var precioaprobado=parseFloat(element.find('.txtPrecioLista2').val()).toFixed(2);
			var descuentovalorunico=parseFloat(element.find('.valorunico').val()).toFixed(4);
			var precioFinal  = parseFloat(precioaprobado*parseFloat(1-descuentovalorunico));
                        precioFinal = precioFinal.toFixed(2);
			element.find('.txtPrecioAprobado').val(precioFinal);
                        var nuevopreciofinal = precioFinal;
                        if (element.find('.textDescOferta').val() == 0) {
                            element.find('.txtPrecioFinal').val(precioFinal);
                        } else {
                            var preciooferta=parseFloat(element.find('.txtPrecioFinal').val()).toFixed(2);
                            var tempDescOferta = '';
                            var tempTextDescOferta = 0;
                            if (preciooferta*1 > 0 && precioFinal*1 > preciooferta*1) {
                                tempDescOferta = 100 - ((preciooferta*100)/precioFinal);
                                tempTextDescOferta = tempDescOferta.toFixed(2);
                                tempDescOferta = tempDescOferta.toFixed(2) + "%";
                                //element.find('.txtPrecioFinal').val(preciooferta);
                                element.find('.txtDescOferta').html(tempDescOferta);
                                element.find('.textDescOferta').val(tempTextDescOferta);

                                nuevopreciofinal = preciooferta;
                            } else {
                                element.find('.txtPrecioFinal').val(precioFinal);
                                element.find('.txtDescOferta').html('-');
                                element.find('.textDescOferta').val(0);
                            }
                        }
                        
			var total = parseFloat(cantidadAprobada * nuevopreciofinal).toFixed(2);
			element.find('.txtTotal').val(total);
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
		});
				
}
function verificarStockdisponible(padre){
	idproducto=padre.find('.idproducto').val();
	cantidad=parseInt(padre.find('.txtCantidadAprobada').val());
	cantidadInicial=parseInt(padre.find('.cantidadInicial').val());
	ruta="/producto/cantidadStock/"+idproducto;
	var validacion=true;

	$.ajax({
		url:ruta,
		dataType:'json',
		type:'get',
		async: false,
		success:function(respuesta){
			var stock=parseInt(respuesta.stockDisponible);
			nueva=stock+cantidadInicial;
			if(cantidad>nueva){
				validacion=false;
			}else{
				validacion=true;
			}
		},
		error:function(error){

		}
	});
	return validacion;
}

function restaImporte(padre){
	importe=parseFloat(padre.find('.txtTotal').val());
	total=$('#txtTotal').val();

	tTotal=total-importe;
	tSubTotal=tTotal*(1-0.19);
	tIgv=tTotal*0.19;

	$('#txtSubTotal').val(tTotal.toFixed(2));
	$('#txtIgv').val(tIgv.toFixed(2));
	$('#txtTotal').val(tTotal.toFixed(2))


}