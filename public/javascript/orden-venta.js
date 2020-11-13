$(document).ready(function(){
        var progressCombo = null;
        $('#btnVerMas').on('click',function(){
            if (progressCombo) {
              progressCombo.abort();
            }
            progressCombo = $.ajax({
                type: 'GET',
                url: '/ventas/listadoVendedoresTodos2/',
                data: '',
                dataType: "json",
                success: function (data) {
                    $("#lstVendedor option").remove();
                    $("#lstVendedor").append("<option value=''>-- Seleccionar uno --</option>");
                    $.each(data, function (i, val) {
                        $("#lstVendedor").append("<option value=" + val['idactor'] + ">" + val['nombreconcat'] + "</option>");
                    });
                }
            });
        });
        $('.liDescuento').hide();
	msgboxTitle="Orden de Ventas";
	if($('#lstVendedor option:selected').val()!=""){
		$('#liVendedor').hide();
	}
	$('#btnRegistrar').click(function(e){
		if (!confirm('¿ Esta seguro de crear el pedido?')) {
			e.preventDefault();
		}
	});

	if ($('#codigov').val()!=undefined) {
		alert('Su Orden de Venta Creada es: '+$('#codigov').val());
	}
        $('#contenedorOfertas').dialog({
		autoOpen:false,
		modal:true,
		width:700,
		buttons:{
			Cerrar:function(){
				$('#contenedorOfertas').dialog('close');
			}
		}, close:function(){
		}

	});
        $('#contenedorPrecioreferencial').dialog({
		autoOpen:false,
		modal:true,
		width:600,
		buttons:{
			Cerrar:function(){
				$('#contenedorPrecioreferencial').dialog('close');
			}
		}, close:function(){
		}

	});
	$('#txtObservaciones').hide();
	$('.gbPorcentajeFacturacion').hide();
	$('.gbLetras').hide();
	$('#ul3').hide();
	$('#frmTransporte').hide();
	$('#liModoFacturacion').hide();
	$('#liMontoContado, #liMontoCredito, #liMontoLetras').hide();
	$('#liCreditoDias').hide();
	$('#txtTransporte').keyup(function(){
		if($(this).val() == ""){
			$('#frmTransporte input').val("");
		}
	});
	/* cancelar la orden de venta*/
	$('#btnCalcelar').click(function(e){
		e.preventDefault();


		/*if ($('.btnEditarCantidad').size()>0) {
			$('.btnEditarCantidad').each(function(e){
				padre=$(this).parents('tr');
				idProductoPadre=padre.find('.txtIdProducto').val();
				cantidadPadre=padre.find('.txtCantidad').val();
				agregaStockdisponible(idProductoPadre,cantidadPadre);
				$('#txtImporteTotal').val(0.00);
				padre.remove();

				
			});
		}else{*/
			window.location = '/vendedor/misordenes/';

		//}
		
		

		
	});
        
        $('#lstPrecio').change(function () {
            if ($(this).val() == 1) {
                $('.liDescuento').hide();
                $('.liPrecio').show();
                $('#txtPrecioOferta').focus();
            } else {
                $('.liDescuento').show();
                $('#txtNuevoDescuento').focus();
                $('.liPrecio').hide();
            }
        });
/*Autocomplete Cliente*/
	$('#txtCodigoCliente').autocomplete({
		source: "/cliente/autocompleteClienteZona/",
		minLength: 2,
		select: function(event, ui){
			$('#txtIdCliente').val(ui.item.idcliente);
			$('#txtIdClienteZona').val(ui.item.idclientezona);
			$('#txtRucDni').val(ui.item.rucdni);
			$('#txtDireccion').val(ui.item.direccion);
			$('#txtDistritoCiudad').val(ui.item.distritociudad);
			$('#txtCodCliente').val(ui.item.codigocliente);
			$('#txtCodAntiguo').val(ui.item.codigoantiguo);
			$('#txtTelefono').val(ui.item.telefono);
			//$('#txtAgenciaTransportes').val(ui.item.agenciatransporte);
			$('#txtFaxCelular').val(ui.item.faxcelular);
			$('#txtEmail').val(ui.item.email);
			//$('#txtidclientesucursal').val(ui.item.idclientesucursal);
			$('#txtdireccionenvio').val(ui.item.direccion_fiscal);
			$('#txtdirecciondespacho').val(ui.item.direccion_despacho_contacto);
			$('#txtnombrecontacto').val(ui.item.nombre_contacto);
			$('.inline-block input').exactWidth();
			//$('#txtCodigoCliente').exactWidth();
			cargaTransporte();
			//cargaDescuento();
			cargaPosicion();
		}
	});
	
	/*Agregar producto a la guia de pedido*/
	$('#btnAgregarProduco').click(function(e){
		msgboxTitle="Orden de Ventas";
		e.preventDefault();

		var cantidadProducto=parseInt($('#txtCantidadProducto').val());
		var idProducto = $('#txtIdProducto').val();
		var elemento = $('#txtCantidadProducto');
		var saldo = parseFloat($('#idsaldo').val());
		console.log(cantidadProducto);
		console.log(idProducto);
		console.log(elemento);
		console.log('Saldo es: '+saldo);
		//alert(saldo);
		if(!$('#txtCodigoProducto, #txtCantidadProducto').valida()){
			return false;
		}else if(!existeProducto(msgboxTitle)){
			return false;
		}else if(!buscaProductoDetalleOrdenVenta()){
			return false;
		}else if(!verificaStockDisponible(cantidadProducto, idProducto, elemento)){
			return false;
		} else if ($('#lstPrecio').val() == 1 && !($('#txtPrecioOferta2').val() > 0)) {
                    $('#txtPrecioOferta2').focus();
                } else if ($('#lstPrecio').val() == 2 && !($('#txtNuevoDescuento').val() >= 0 && $('#txtNuevoDescuento').val() <= 30)) {
                    $('#txtNuevoDescuento').focus();
                } else{
			agregaProducto(saldo);
			//disminuyeStockdisponible(idProducto,cantidadProducto);
			//verificaSaldoDisponible(saldo);
			var contador = parseInt($('#txtContador').val()) + 1;
			$('#txtContador').attr('value', contador);
			$('#txtCodigoProducto').val('').focus();
			$('#txtTituloProducto').val('');
			$('#txtCantidadProducto').val('');
                        $('#txtPrecioOferta').val('');
			$('#lstDescuento').val('');
//                        $('#txtPrecioOferta2').val('');
//                        $('#txtNuevoDescuento').val('');
		}
	});
	$('#lstTransporte').change(function(){
		var aaa=$('#txtCodigoCliente').val()+ ' - Saldo: '+ $('#idsaldo').val();
		$('#txtCabeceraCliente').val(aaa);
		console.log();
		if($(this).val() != ''){
			$('.show:eq(0)').click();
			if($('.show:eq(1)').text() != '-'){
				$('.show:eq(1)').click();
			}
		}
	});
	$('#lstVendedor').change(function(){
		if($(this).val() != ''){
			$('.show:eq(1)').click();
			if($('.show:eq(2)').text() != '-'){
				$('.show:eq(2)').click();
			}
		}
	});
	$('#frmGeneracionOrdenVenta').validate({
		rules: {
			modoFacturacion: {
				required: function(element){return $('#txtPorcentajeFacturacion').val() != 100}
			},
			saldoDisponible: {
				required: function(element){return $('#txtSaldoDisponible').val() != '1'}
			},
			condicionLetra: {
				required: '#chkLetras:checked'
			},
			tipoLetra: {
				required: '#chkLetras:checked'
			}
		},
		ignore: '.required-none'
	});

	$('#frmGeneracionOrdenVenta').submit(function(e){
		var creditoDias = $('#txtCreditoDias').val();
		var formaPago = $('#txtFormaPago').val();
		var montoContado = $('#txtMontoContado').val();
		var montoCredito= $('#txtMontoCredito').val();
		var montoLetras= $('#txtMontoLetras').val();
		var condicionLetraText = $.trim($('#lstCondicionLetra option:selected').text());
		var tipoDocumento = $('#lstCondicionLetra option:selected').val();
		var tipoLetraText = $.trim($('#lstTipoLetra option:selected').text());
		var porcentajeFacturacion = $('#txtPorcentajeFacturacion').val();
		var modoFacturacion = $.trim($('#lstModoFacturacion option:selected').text());
		var observacion = '<ul><li><strong>Forma Pago: </strong>' + formaPago+ '</strong>';
		if($('#chkCredito').is(':checked')){
			observacion += "<li><strong>Credito Dias:</strong> " + creditoDias + "</li>";
		}
		if($('#chkLetras').is(':checked')){
			observacion += "<li><strong>Condicion Letra:</strong> " + condicionLetraText + "</li>";
			observacion += "<li><strong>Tipo Letra:</strong> " + tipoLetraText + "</li>";
		}
		if(montoContado != ''){
			observacion += "<li><strong>Monto al Contado:</strong> " + montoContado + "</li>";	
		}
		if(montoCredito != ''){
			observacion += "<li><strong>Monto al Credito:</strong> " + montoCredito + "</li>";	
		}if(montoLetras != ''){
			observacion += "<li><strong>Monto a Letras:</strong> " + montoLetras + "</li>";	
		}
		if(tipoDocumento == 1 &&  porcentajeFacturacion != 100){
			observacion += "<li><strong>Porcentaje Facturacion:</strong> " + porcentajeFacturacion + " %" + "</li>";
			observacion += "<li><strong>Modo Facturacion: </strong>" + modoFacturacion + "</li>";	
		}
		observacion += "</ul>";
		$('#txtObservaciones').val(observacion);
	});
/*Muestra el Cuadro de dialogo luego agrega nuevo transporte para el cliente*/
	$('#btnNuevoTransporte').click(function(e){
		e.preventDefault();
		if($('#txtCodigoCliente').val() == ""){
			$.msgbox(msgboxTitle,'Para agregar un transporte primeramete ingrese el cliente');
			execute();
		}else{
			nuevoTransporte();	
		}
	});
	
/*Cambiar porcentaje de facturacion*/
	$('#btnCambiarPorcentaje').click(function(e){
		e.preventDefault();
		$('#txtPorcentajeFacturacion').attr('readonly', false).val('').focus();
	});
	$('#txtPorcentajeFacturacion').blur(function(){
		$('#txtPorcentajeFacturacion').attr('readonly', true);
		if(this.value == "100" || this.value ==""){
			this.value = 100;
			$('#liModoFacturacion').hide();
		}else{
			$('#liModoFacturacion').show();
		}
	});
	
/*Intercambio de tipo de documento*/	
	$('#lstTipoDocumento').change(function(){
		if($('#lstTipoDocumento option:selected').val()==1){
			$('.gbPorcentajeFacturacion').show();
			$('#txtPorcentajeFacturacion').val('100');
		}else{
			$('.gbPorcentajeFacturacion').hide();
			$('#txtPorcentajeFacturacion').val('100');
			$('#liModoFacturacion').hide();
		}
	});
	
/*Intercambio de forma de pago*/
$('#chkContado, #chkCredito, #chkLetras').on('change', function(){
	$('#lstCondicionLetra option').eq(0).attr('selected','selected');
	$('#lstTipoLetra option').eq(0).attr('selected','selected');
	var element = ($(this).is(':checked'))?'1':'0'
	var contado = ($('#chkContado').is(':checked'))?'1':'0';
	var credito = ($('#chkCredito').is(':checked'))?'1':'0';
	var letras = ($('#chkLetras').is(':checked'))?'1':'0';
	var type = $(this).attr('id');
	var formaPago = $('#txtFormaPago').val();
	if(element == 1){
		if(type == 'chkContado'){
			formaPago += 'Contado ';
		}else if(type == 'chkCredito')	{
			formaPago += 'Credito ';
		}else{
			formaPago += 'Letras ';
		}
	}else{
		if(type == 'chkContado'){
			formaPago = $('#txtFormaPago').val().replace('Contado ', '');
		}else if(type == 'chkCredito')	{
			formaPago = $('#txtFormaPago').val().replace('Credito ', '');
		}else{
			formaPago = $('#txtFormaPago').val().replace('Letras ', '');
		}
	}
	if(contado == 1 && credito == 1 && letras == 1){
		$('#liCreditoDias,.gbLetras,.condicionLetra').show();
		$('#liMontoContado,#liMontoCredito').show();
		$('#liMontoLetras').show();
	}else if(contado == 0 && credito == 0 && letras == 0){
		$('#liCreditoDias,.gbLetras,.condicionLetra').hide();
		$('#liMontoContado, #liMontoCredito, #liMontoLetras').hide();
	}else if(contado == 1 && credito == 0 && letras == 0){
		$('#liCreditoDias,.gbLetras,.condicionLetra').hide();
		$('#liMontoContado, #liMontoCredito, #liMontoLetras').hide();
	}else if(contado == 1 && credito == 1 && letras == 0){
		$('#liMontoContado,#liCreditoDias').show();
		$('.gbLetras,.condicionLetra').hide();
		$('#liMontoCredito, #liMontoLetras').hide();
	}else if(contado == 1 && credito == 0 && letras == 1){
		$('#liMontoContado,.gbLetras,.condicionLetra').show();
		$('#liCreditoDias').hide();
		$('#liMontoCredito, #liMontoLetras').hide();
	}else if(contado == 0 && credito == 1 && letras == 1){
		$('.gbLetras,.condicionLetra,#liCreditoDias').show();
		$('#liMontoContado').hide();
		$('#liMontoCredito, #liMontoLetras').show();
	}else if(contado == 0 && credito == 1 && letras == 0){
		$('#liCreditoDias').show();
		$('.gbLetras,.condicionLetra').hide();
		$('#liMontoContado,#liMontoCredito, #liMontoLetras').hide();
	}else if(contado == 0 && credito == 0 && letras == 1){
		$('#liCreditoDias').hide();
		$('.gbLetras,.condicionLetra').show();
		$('#liMontoContado,#liMontoContado,#liMontoCredito, #liMontoLetras').hide();
	}
	$('#txtFormaPago').val(formaPago);
});
	//Quitar un producto del detalle de la guia de pedido
	$('body').on('click', '.btnEliminarProducto', function(e){
		e.preventDefault();
		$fila = $(this).parents('tr');
		//var total =parseFloat($fila.find('.txtTotal').val());
		//var importeTotal = parseFloat($('#txtImporteTotal').val());
		//$('#txtImporteTotal').val((importeTotal - total).toFixed(4));
		$fila.remove();
		idP=$fila.find('.txtIdProducto').val();
		canP=$fila.find('.txtCantidad').val();
                var importetotaldspseliminacion = 0;
                $('.txtTotal').each(function () {
                    importetotaldspseliminacion += parseFloat($(this).val());
                });
                $('#txtImporteTotal').val(importetotaldspseliminacion.toFixed(4));
		//agregaStockdisponible(idP,canP);
	});
	
	//Editar la cantidad del detalle de la guia de pedido
	$('body').on('click', '.btnEditarCantidad', function(e){
		e.preventDefault();
		//$(this).parents('tr').find('.txtCantidad').removeAttr('readonly').focus();
		$(this).parents('tr').find('.txtPrecioOfertado').removeAttr('readonly').focus();
	});
	//
	$('body').on('click', '.btnEditarPrecio', function(e){
		e.preventDefault();
		$(this).parents('tr').find('.txtPrecio').removeAttr('readonly').focus();
		//$(this).parents('tr').find('.txtCantidad').removeAttr('readonly').focus();
		
	});
        $('#tblOfertas').on('click', '.copiarOfertaPrecio', function () {
            $('#idtxtPrecioOferta' + $(this).data('id')).val($(this).data('precio'));/*
            $('#textIddescuentosolicitado' + $(this).data('id')).val('');
            $('#textIddescuentosolicitadotexto' + $(this).data('id')).val('');
            $('#textIddescuentosolicitadovalor' + $(this).data('id')).val('');
            $('#tdDescuento' + $(this).data('id')).html('()');*/
            padre = $('#idtxtPrecio' + $(this).data('id')).parents('tr');
            cambiarPrecioEdicion(padre, 0);
            $('#contenedorOfertas').dialog('close');
        });

        $('#tblDetalleOrdenVenta').on('click', '.btnOfertaProducto', function () {
            var idproducto = $(this).data('id');
            $.ajax({
		url:'/producto/productosoferta',
		type:'POST',
                data:{'idproducto': idproducto},
		dataType:'json',
		success:function(data){
                    $('#tblOfertas tbody').html(data.ofertas);
                    $('#titleOfertas').html(data.codigo + ' // ' + data.nompro);
                    $('#contenedorOfertas').dialog('open');
                }
            });
            return false;
        });
        
    $('#tblDetalleOrdenVenta').on('click', '.btnadvertencia', function () {
        var idproducto = $(this).data('idproducto');
        $.ajax({
            url:'/ventas/ventasverificarprecio2',
            type:'POST',
            data:{'idproducto': idproducto},
            dataType:'json',
            success:function(data){
                $('#tblPrecioReferencial tbody').html(data.referencia);
                $('#titlePodReferencial').html(data.codigo + ' // ' + data.nompro);
                $('#contenedorPrecioreferencial').dialog('open');
            }
        });
        return false;
    });
    
    $('.txtPrecio').live('blur', function () {
        var preciolista = parseFloat($(this).val()).toFixed(2);
        var preciolistaGuardado = parseFloat($(this).data('pl')).toFixed(2);
        if (preciolista * 1 > preciolistaGuardado * 1) {
            var importeTotal = parseFloat($('#txtImporteTotal').val()).toFixed(2);
            padre = $(this).parents('tr');
            var cantidad = padre.find('.txtCantidad').val();
            var descuentosolicitadoid = 0;
            var descuentosolicitadovalor = 0;
            var descuentosolicitadotexto = 0;
            var preciolistasoles = preciolista;

            var importeOfertado = 0;
            var TextDescuento = 0;
                //alert("entre a lstprecio: [" + $('#txtPrecioOferta2').val() + "] -lenght: " + $('#txtPrecioOferta2').val().length);

            importeOfertado = parseFloat(padre.find('.txtPrecioOfertado').val()).toFixed(2);
            if (importeOfertado * 1 > preciolista * 1) {
                importeOfertado = preciolista;
            } else {
                TextDescuento = 100 - ((importeOfertado * 100) / preciolista);
                if (TextDescuento * 1 <= 30) {
                } else {
                    TextDescuento = 30;
                }
            }
                            
            $.ajax({
                async: false,
                url: "/descuento/gestionar/",
                type: "POST",
                data: {'descuento': TextDescuento},
                dataType: "json",
                success: function (dataDescuento) {/*
                 console.log("aqui estoy para el descuento*******************");
                 console.log(dataDescuento);
                 console.log("------------------------------");*/
                    descuentosolicitadoid = dataDescuento.id;
                    descuentosolicitadovalor = dataDescuento.dunico;
                    descuentosolicitadotexto = dataDescuento.valor;
                    //alert(descuentosolicitadovalor);
                    //alert(descuentosolicitadotexto);
                }
            });

            var tempDescOferta = '';
            var tempTextDescOferta = 0;
            var preciosolicitado = ((1 - descuentosolicitadovalor) * preciolista).toFixed(2);
            var preciototal = 0;

            if (importeOfertado * 1 > 0 && importeOfertado * 1 < preciosolicitado * 1) {
                tempDescOferta = 100 - ((importeOfertado * 100) / preciosolicitado);
                tempTextDescOferta = tempDescOferta.toFixed(2);
                tempDescOferta = tempDescOferta.toFixed(2) + "%";
            } else {
                importeOfertado = preciosolicitado;
                tempDescOferta = '-';
            }
            var idproducto = padre.find('.txtIdProducto').val();
           
            preciototal = parseFloat(importeOfertado * cantidad).toFixed(2);

            descuentototal = cantidad * (preciolista - preciosolicitado);

            importeTotal = parseFloat(importeTotal) + parseFloat(preciototal);
            $('#textIddescuentosolicitado' + idproducto).val(descuentosolicitadoid);
            $('#textIddescuentosolicitadotexto' + idproducto).val(descuentosolicitadotexto);
            $('#textIddescuentosolicitadovalor' + idproducto).val(descuentosolicitadovalor);
            $('#tdDescuento' + idproducto).html(' ( <b>'+descuentosolicitadotexto+' </b>)');
            $('#textDescOferta' + idproducto).val(tempTextDescOferta);
            $('#idtxtDescOferta' + idproducto).html(tempDescOferta);
            $('#' + idproducto).val();
            padre.find('.txtPrecioDescontado').val(preciosolicitado);
            padre.find('.txtPrecioOfertado').val(importeOfertado);
            padre.find('.txtDescuento').val(descuentototal.toFixed(2));
            padre.find('.txtTotal').val(preciototal);
            padre.find('.txtPrecioDescontado').val();
            
            var importetotaldspseliminacion = 0;
            $('.txtTotal').each(function(e){
                //alert('val; ' + $(this).val());
                importetotaldspseliminacion += parseFloat($(this).val());
               //alert('importedespacho; ' + importetotaldspseliminacion);
            });
            //alert('importedespacho final ' + importetotaldspseliminacion);
            $('#txtImporteTotal').val(importetotaldspseliminacion.toFixed(4));
        } else {
            $(this).val(preciolistaGuardado);
        }
    });
	
    $('.txtPrecioOfertado').live('blur', function () {
        padre = $(this).parents('tr');
        revisarPrecioOfertado(padre, 0);
    });
	//Verificar la cantidad de producto al salir de txtCantidad
	$('body').on('blur', '.txtCantidad', function(e){/*
		e.preventDefault();
		var parentElement = $(this).parents('tr');
		var idProducto = parentElement.find('.txtIdProducto').val();
		var cantidadProducto = parseInt(parentElement.find('.txtCantidad').val());
		if($(this).hasClass('txtCantidad')){
			if(!verificaStockDisponible(cantidadProducto, idProducto, $(this))){
				return false;
			}

		}

		var precio = parseFloat(parentElement.find('.txtPrecio').val());
		//creo el nuevo precio descontado
		var txtPrecioDescontado=parseFloat(parentElement.find('.txtPrecioDescontado').val());

		var total = ((cantidadProducto * txtPrecioDescontado)).toFixed(2);
		parentElement.find('.txtTotal').val(total);
		var montoTotal=0;
		$('.txtTotal').each(function(){
			montoTotal += parseFloat($(this).val().replace(',',''));
		});
		$('#txtImporteTotal').val(montoTotal.toFixed(2));

		if($(this).hasClass('txtCantidad')){
			if(!verificaSaldoDisponible()){
					return false;
			}
		}*/

	});

	$(document).keydown(function(e){
	  var code = (e.keyCode ? e.keyCode : e.which);
		  if(code == 116) {
		   e.preventDefault();
		   r=confirm('¿No Recargue la pagina si tiene items agregados?', 'Confirmación', function(r) {
		       if(r)
		        location.reload();
		   });
		  }

	 });


});

msgboxTitle="Orden de Ventas";

function revisarPrecioOfertado (padre, bandera) {
        cambiarPrecioEdicion(padre, bandera);
        var moneda = $('#txtMoneda').val();
        var precio = padre.find('.txtPrecioOfertado').val();
        var idproducto = padre.find('.txtIdProducto').val();
        $.ajax({
            url:'/ventas/ventasverificarprecio',
            type:'POST',
            data:{
                'moneda': moneda,
                'precio': precio,
                'idproducto': idproducto
            },
            dataType:'json',
            success:function(data){
                if (data.rspta == 0) {
                    alert('Producto no disponible');
                } else {
                    if (data.rsptamoneda == 1) {
                        if (data.rspta == 1) {
                            $('#advertencia' + idproducto).html('<a href="#" class="btnadvertencia" data-idproducto="' + idproducto + '"><img src="/imagenes/iconos/advertencia.png"></a>');
                            padre.attr('style', 'background: #ffb6b6');
                            cambiarPrecioEdicion(padre, 0);
                        } else if (data.rspta == 2) {
                            $('#advertencia' + idproducto).html('');
                            padre.removeAttr('style');
                        }
                    } else {
                        alert('Moneda no seleccionada');
                    }
                }
            }
        });
}

//Agrega un producto al detalle de la guia de pedido
function agregaProducto(saldo){
	var validaelijemoneda=$('#txtMoneda').val();
	if(validaelijemoneda=="-1"){
		alert("Antes de registrar productos, debe elegir moneda");
		exit;
	}
	var lblmoneda=$('#lblMoneda').val();
	var tipoCambio=parseFloat($('#txtTipoCambioValor').val()).toFixed(2);
	var contador = $('#txtContador').val();
	var cantidad = parseInt($('#txtCantidadProducto').val());
	var descuento = 0;
	var descuentosolicitado = 0;
	/*if($('#lstDescuento').val()){
		descuentosolicitado = $('#lstDescuento').val();
		//descuentosolicitado = parseFloat($('#lstDescuento').val()).toFixed(4);
	}*/
        
	var importeTotal = parseFloat($('#txtImporteTotal').val()).toFixed(2);
	
	//ruta= "/producto/buscarproducto_venta/" + $('#txtIdProducto').val()+"/"+descuentosolicitado;
	ruta= "/producto/buscarproducto_venta/" + $('#txtIdProducto').val() + '/0';
        console.log(ruta);
	$.ajax({
		url:ruta,
		type:'get',
		dataType:'json',
		success:function(data){
			console.log(data);
		/*var descuentosolicitadoid=descuentosolicitado;
		var descuentosolicitadovalor=data.descuentosolicitado;
		var descuentosolicitadotexto=data.descuentovalor;
                 */
		var descuentosolicitadoid=0;
		var descuentosolicitadovalor=0;
		var descuentosolicitadotexto=0;
                
                
		var preciolistasoles=data.preciolista;
		if (lblmoneda=="US $") {
			var preciolista=parseFloat(data.preciolistadolares).toFixed(2);
		}else{
			var preciolista=parseFloat(data.preciolista).toFixed(2);
		}
                var importeOfertado = 0;
                var TextDescuento = 0;
                if ($('#lstPrecio').val() == 1) {
                    //alert("entre a lstprecio: [" + $('#txtPrecioOferta2').val() + "] -lenght: " + $('#txtPrecioOferta2').val().length);
                    
                    if ($('#txtPrecioOferta2').val() > 0) {                        
                        importeOfertado = parseFloat($('#txtPrecioOferta2').val()).toFixed(2);
                        if($('#chkVale').prop('checked')) {
                            preciolista = importeOfertado;
                        }
                        if (importeOfertado*1 > preciolista*1) {
                            importeOfertado = preciolista;
                        } else {
                            TextDescuento = 100-((importeOfertado*100)/preciolista);
                            if (TextDescuento*1 <= 30) {
                            } else {
                                TextDescuento = 30;
                            }
                        }
                    }
                    //alert('TextDescuento 2: ' + TextDescuento);
                } else {
                    if ($('#txtNuevoDescuento').val() > 0 && $('#txtNuevoDescuento').val() <= 30) {
                        TextDescuento = $('#txtNuevoDescuento').val();                        
                    } else if ($('#txtNuevoDescuento').val() < 0) {
                        TextDescuento = 0;
                    } else if ($('#txtNuevoDescuento').val() > 30) {
                        TextDescuento = 30;
                    }
                }
                $.ajax({
                    async: false,
                    url: "/descuento/gestionar/",
                    type: "POST",
                    data:{'descuento': TextDescuento},
                    dataType: "json",
                    success: function(dataDescuento){/*
                        console.log("aqui estoy para el descuento*******************");
                        console.log(dataDescuento);
                        console.log("------------------------------");*/
                        descuentosolicitadoid=dataDescuento.id;
                        descuentosolicitadovalor=dataDescuento.dunico;
                        descuentosolicitadotexto=dataDescuento.valor;
                        //alert(descuentosolicitadovalor);
                        //alert(descuentosolicitadotexto);
                    }
                });
                
		var tempDescOferta = '';
                var tempTextDescOferta = 0;
		var preciosolicitado=((1-descuentosolicitadovalor)*preciolista).toFixed(2);
                var preciototal = 0;

                if (importeOfertado*1 > 0 && importeOfertado*1 < preciosolicitado*1) {
                    tempDescOferta = 100 - ((importeOfertado*100)/preciosolicitado);
                    tempTextDescOferta = tempDescOferta.toFixed(2);
                    tempDescOferta = tempDescOferta.toFixed(2) + "%";
                } else {
                    importeOfertado = preciosolicitado;
                    tempDescOferta = '-';                    
                }
                
                
                preciototal=parseFloat(importeOfertado*cantidad).toFixed(2);
                
		descuentototal=cantidad*(preciolista-preciosolicitado);

		importeTotal=parseFloat(importeTotal)+parseFloat(preciototal);
                var tempOfertas = '';
                if (data.ofertas != undefined) {
                    tempOfertas = '<a href="#" class="btnOfertaProducto" data-id="' + data.idproducto + '"><img src="/imagenes/iconos/oferta.png"></a>';
                    $('#tblOfertas tbody').html(data.ofertas);
                    $('#titleOfertas').html(data.codigo + ' // ' + data.nompro);
                    $('#contenedorOfertas').dialog('open');
                }
		$('#tblDetalleOrdenVenta tbody tr:last').before('<tr>'+
			'<td class="codigo" id="codigo' + data.idproducto + '" data-on="1">' + 
				data.codigo +
				'<input type="hidden" value="' + data.idproducto + '" name="DetalleOrdenVenta[' + contador + '][idproducto]" class="txtIdProducto">'+
				'<input type="hidden" value="' + descuentosolicitadoid + '" name="DetalleOrdenVenta[' + contador + '][descuentosolicitado]" id="textIddescuentosolicitado' + data.idproducto + '">'+
				'<input type="hidden" value="' + descuentosolicitadotexto + '" name="DetalleOrdenVenta[' + contador + '][descuentosolicitadotexto]" id="textIddescuentosolicitadotexto' + data.idproducto + '">'+
				'<input type="hidden" value="' + descuentosolicitadovalor + '" name="DetalleOrdenVenta[' + contador + '][descuentosolicitadovalor]" class="porTxtDescuento text-50" id="textIddescuentosolicitadovalor' + data.idproducto + '">'+
			'</td>'+
			'<td>' + data.nompro + '</td>'+
			//'<td>' + data.codigoalmacen + '</td>'+
			'<td>' + preciolistasoles + '</td>'+
			'<td>'+
				'<input type="text" value="' + cantidad + '" name="DetalleOrdenVenta[' + contador + '][cantsolicitada]" class="txtCantidad numeric text-50" readonly>'+
			'</td>'+
			'<td>'+ lblmoneda +' <input type="text" value="' + preciolista + '" data-pl="' + preciolista + '" id="idtxtPrecio' + data.idproducto + '" name="DetalleOrdenVenta[' + contador + '][preciolista]" class="txtPrecio numeric text-50"></td>'+
			'<td id="tdDescuento' + data.idproducto + '"> ( <b>'+descuentosolicitadotexto+' </b>)</td>'+
                        '<td>'+ lblmoneda +' '+
				'<input type="text" value="' + preciosolicitado + '" name="DetalleOrdenVenta[' + contador + '][preciosolicitado]" class="txtPrecioDescontado text-50"  readonly>'+
			'</td>'+
                        '<td>' + 
                            '<input type="hidden" value="' + tempTextDescOferta + '" name="DetalleOrdenVenta[' + contador + '][descuentooferta]" id="textDescOferta' + data.idproducto + '" class="textDescOferta text-50"  readonly>'+
                            '<label class="txtDescOferta" id="idtxtDescOferta' + data.idproducto + '">' + tempDescOferta + '</label>' + 
                        '</td>' +
                        '<td>'+ lblmoneda +' '+
				'<input type="text" value="' + importeOfertado + '" name="DetalleOrdenVenta[' + contador + '][precioofertado]"  id="idtxtPrecioOferta' + data.idproducto + '" class="txtPrecioOfertado text-50"  readonly>'+
			'</td>'+
			'<input type="hidden" value="' + descuentototal.toFixed(2) + '" name="DetalleOrdenVenta[' + contador + '][tipodescuento]" class="txtDescuento text-50">'+ 
			'<td class="center"> '+ lblmoneda +' <input type="text" value="' + preciototal + '" class="txtTotal text-100 right" readonly></td>'+
			'<td><a href="#" class="btnEditarCantidad"><img src="/imagenes/editar.gif"></a></td>'+
			'<td><a href="#" class="btnEliminarProducto"><img src="/imagenes/eliminar.gif"></a></td>'+
                        '<td>' + tempOfertas + '</td>'+
                        '<td id="advertencia' + data.idproducto + '"></td>'+
		'</tr>');
		padrenuevo = $('#advertencia' + data.idproducto).parents('tr');
                revisarPrecioOfertado(padrenuevo, 1);
		$('#txtImporteTotal').val(importeTotal.toFixed(2));
                $('#txtPrecioOferta2').val('');
                $('#txtNuevoDescuento').val('');
                $('#chkVale').prop('checked', false);
//		verificaSaldoDisponible();
	},
	error:function(error){
		console.log('error');
	}
	});	
	
}

//Evita que se agrege dos veces un producto al detalle de la guia de pedidos
function buscaProductoDetalleOrdenVenta(){
		//var codigoProducto = $.trim($('#txtCodigoProducto').val());
		var codigoProducto = $('#txtIdProducto').val();
		var existe = $("#codigo"+codigoProducto).data('on');
		if(existe == 1){
			$.msgbox(msgboxTitle,'El producto <strong>' + $('#txtCodigoProducto').val() + '</strong> ya esta agregado en el<br>detalle de la guia de pedido.');
			$('#msgbox-ok, #msgbox-cancel, #msgbox-close').click(function(){
				$('#txtCantidadProducto').val('');
				$('#txtDescuento').val('');
				$('#txtCodigoProducto').val('').focus();
                                $('#txtPrecioOferta2').val('');
                                $('#txtNuevoDescuento').val('');
			});
			return false;
		}else{
			return true;
		}
	}
//Evitar que se ingrese una cantidad de stock, superior al stock diponible.
function verificaStockDisponible(cantidadSalida, idProducto, element){
	//la cantidades  se van aceptar >= que 0 por exigencia de facturacion
	if(cantidadSalida<=0){
		$.msgbox(msgboxTitle,"La cantidad de stock no puede ser menor o igual a cero");
		execute();
		return false;
	}else{
		var ruta = "/producto/cantidadstock/" + idProducto;
		var stockDisponible = 0;
                var stockActual = 0;
                $.ajax({
			async: false,
			url: ruta,
			type: "POST",
			dataType: "json",
			success: function(data){
                            stockDisponible = data.stockDisponible;
                            stockActual = data.stockActual;
                        }
		});
		if(cantidadSalida > stockActual){
                    if ((stockActual*1)<(stockDisponible*1)) {
                        if(cantidadSalida > stockDisponible){
                            $.msgbox(msgboxTitle,"Cantidad de stock no disponible.<br><strong>Stock disponible: </strong>" + stockDisponible);
                            execute();
                            $('#msgbox-ok').click(function(){
                                    element.val('').focus();
                            });
                            return false;
                        }
                        else {
                            $.msgbox(msgboxTitle,"Cantidad de stock no disponible en almacén,<br>pero ya se realizó una orden de compra.<br><br><strong>Stock en almac&eacute;n: </strong>" + stockActual + "<br><strong>Stock disponible: </strong>" + stockDisponible);
                            execute();
                            $('#msgbox-ok').click(function(){
                                    element.val('').focus();
                            });
                            return true;
                        }
                    }
                    else {
			$.msgbox(msgboxTitle,"Cantidad de stock no disponible.<br><strong>Stock disponible: </strong>" + stockDisponible);
			execute();
			$('#msgbox-ok').click(function(){
				element.val('').focus();
			});
			return false;
                    }
		}else{
			return true;
		}
	}
}
function cambiarPrecioEdicion(padre, bandera) {
    
    PrecioDescontado=parseFloat(padre.find('.txtPrecioDescontado').val());
    valorCantidad=parseFloat(padre.find('.txtCantidad').val());
    valorImporte=parseFloat(padre.find('.txtTotal').val());
    valorTotal=parseFloat($('#txtImporteTotal').val());
    
    
    nuevoPrecio=parseFloat(padre.find('.txtPrecioOfertado').val());

    /*
    if (valorDescuento=='') {
            valorDescuento=0;
    }else{
            valorDescuento=parseFloat(padre.find('.porTxtDescuento').val());
    }*/

    /*console.log(valorDescuento);
    console.log(PrecioDescontado);
    console.log(valorCantidad);
    console.log(valorImporte);
    console.log(valorTotal);
    console.log(nuevoPrecio);*/
       //alert(nuevoPrecio); 
    var tempDescOferta = ''; // REQ29450
    var TempTextDescOferta = 0;
    if (nuevoPrecio > 0 && nuevoPrecio <= PrecioDescontado) {
        //alert('siiiii'); 
        tempDescOferta = 100 - ((nuevoPrecio*100)/PrecioDescontado);
        TempTextDescOferta = tempDescOferta.toFixed(2);
        if (tempDescOferta == '0.00') {
            tempDescOferta = '-';
        } else {
            tempDescOferta = tempDescOferta.toFixed(2) + "%";
        }
    } else {
        //alert('siiiii j9ojjijij: np_ ' + nuevoPrecio + ' pd: ' + PrecioDescontado); 
        nuevoPrecio = PrecioDescontado;
        
        tempDescOferta = '-';
        padre.find('.txtPrecioOfertado').val(nuevoPrecio)
        if (bandera == 0) { 
            alert("El precio ofertado debe ser menor al precio solicitado");
        }
        
    }
    
    padre.find('.txtDescOferta').html(tempDescOferta);
    padre.find('.textDescOferta').val(TempTextDescOferta);
    nuevoImporte=nuevoPrecio*valorCantidad;
    //nuevoTotal=valorTotal-valorImporte+nuevoImporte;
    padre.find('.txtTotal').val(nuevoImporte.toFixed(2));
    
    var importetotaldspseliminacion = 0;
    $('.txtTotal').each(function(e){
        //alert('val; ' + $(this).val());
        importetotaldspseliminacion += parseFloat($(this).val());
       //alert('importedespacho; ' + importetotaldspseliminacion);
    });
    //alert('importedespacho final ' + importetotaldspseliminacion);
    $('#txtImporteTotal').val(importetotaldspseliminacion.toFixed(4));
    //$('#txtImporteTotal').val(nuevoTotal.toFixed(2));
    
    
    /*


    nuevoPrecioDescontado=(1-valorDescuento)*nuevoPrecio;
    //console.log('nuevo PrecioDescontado'+nuevoPrecioDescontado);
    
    nuevoTotal=valorTotal-valorImporte+nuevoImporte;

    padre.find('.txtPrecioDescontado').val(nuevoPrecioDescontado.toFixed(2));
    */
    //console.log('nuevo Total'+nuevoTotal);
    
}

function verificaSaldoDisponible(){
	//parseFloat($(this).val().replace(',',''));
	var saldo=parseFloat($('#idsaldo').val().replace(',',''));
	var importeTotal=parseFloat($('#txtImporteTotal').val());
	if(importeTotal>saldo){
		$.msgbox("ALERTA EN LA ORDEN","No tiene saldo suficiente,<br> Puede disminuir la Cantidad de Productos !!!");
		$('#txtSaldoDisponible').val('0');
		return false;
	}else{
		$('#txtSaldoDisponible').val('1');
		return true;
	}
}
/*Carga Transporte del cliente*/
function cargaTransporte(){
	console.log($('#txtIdCliente').val());
	var idCliente = $('#txtIdCliente').val();
	var ruta = "/facturacion/buscatransporte/" + idCliente;
	$.post(ruta, function(data){
		$('#lstTransporte').html('<option value="">-- Transporte --' + data);
	});
}


function cargaDescuento(){
	var ruta = "/descuento/listado/";
	$.post(ruta, function(data){
		$('#lstDescuento').html('<option value="">-- Descuento --' + data);
	});
}


function cargaPosicion(){
	var idCliente = $('#txtIdCliente').val();
	var ruta = "/cliente/posicionordenventa/"+ idCliente;
	$.post(ruta, function(data){
		$('#clienteposicion').html(data);
		var saldo=parseFloat($('#idsaldo').val());
		if(saldo<=0){
			$.msgbox("Alerta al Crear la ORDEN","EL CLIENTE TIENE SALDO S./ " + saldo+ "<br> Verifique el Saldo que necesita ANTES de empezar a registrar su orden.");
		}

	});

}


//Muestra el cuadro de dialogo para agregar un nuevo transporte para el cliente
function nuevoTransporte(){
	$.msgbox("Transporte Nuevos", '<div id="msgboxTransporte"></div>', '<a href="#" id="btnAgregarTransporte">Agregar</a>');
	$('#msgboxTransporte').load('/forms/transporte.phtml', function(){
		$('#txtTransporte').focus();
		execute();
	});
}

function agregaStockdisponible(idproducto,cantidad){
	$.ajax({
		url:'/producto/agregaStockdisponible',
		type:'post',
		datatype:'html',
		data:{'idproducto':idproducto,'cantidad':cantidad},
		success:function(resp){
			console.log(resp);
		}
	});
}
function disminuyeStockdisponible(idproducto,cantidad){
	$.ajax({
		url:'/producto/disminuyeStockdisponible',
		type:'post',
		datatype:'html',
		data:{'idproducto':idproducto,'cantidad':cantidad},
		success:function(resp){
			console.log(resp);
		}
	});
}

function cargaSucursales(){
	var idcliente=$('#idcliente').val();
	$.ajax({
		url:'/cliente/direccion_despacho',
		type:'post',
		dataType:'html',
		async: false,
		data:{'idcliente':idcliente},
		succcess:function(resp){
			$('#lstDirecciones').html(resp);
		}
	});
}