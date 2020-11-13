$(document).ready(function(){
        
        
         
	$('#btnRegistrar').click(function(e){

            var numero = $('#txtNumero').val();
            var ruta = "/movimiento/buscaNumeroFactura/"+ numero;
            $.post(ruta, function(data){
                if(data!=0){
                    alert("Número ya está registrado,verifique o llame a Sistemas anexo:125");
                    $('#txtNumero').focus();
                }else{
                    if (!confirm('¿ ESTÁ SEGURO DE REGISTRAR LA FACTURA?')){
                    }else{
                        $('#frmGeneracionOrdenVenta').submit();
                    }
                }
            });
            e.preventDefault();
	});

	if ($('#codigov').val()!=undefined) {
            alert('Su Orden de Venta Creada es: '+$('#codigov').val());
	}
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
	$('#btnCancelar').click(function(e){
            e.preventDefault();
                window.location = '/importaciones/registroDuas/';
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
			cargaDescuento();
			cargaPosicion();
		}
	});
	
	/*Agregar producto a la guia de pedido*/
	$('#btnAgregarpagoCompra').click(function(e){
		e.preventDefault();
                var codigoReciboDua = $('#txtcodigoReciboDua').val();
                var montoReciboDua = $('#txtmontoReciboDua').val();
                var fechaReciboDua = $('#txtfechaReciboDua').val();
                var lstModalidadPago = $('#lstModalidadPago').val();
                /*modalidad*/
                var modalidad = "";
                if(lstModalidadPago = 1){
                    modalidad = "CONTADO";
                }else{
                    modalidad = "CRÉDTO";
                }
                
//                var data="<tr><td>"+idProducto+"</td><td>"+codigoProducto+"</td><td>"+nombreProducto+"</td><td>"+cantidadProducto+"</td></tr>";
//                $('#contenidoProductos').html(data);
               // var val = existeValorArray(codigoReciboDua,codigosPagosCompra);

                if(!$('#txtcodigoReciboDua , #txtmontoReciboDua , txtfechaReciboDua').valida()){
                    return false;
                }else if(montoReciboDua == 0){
                    alert("El precio no debe ser igual 0");
                    return false;
//                }else if(val){
//                    $('#txtcodigoReciboDua').val('');
//                    alert("Ya se ingreso este código de pago");	
//                    $('#txtcodigoReciboDua').focus();
//                    return false;
                }else{
                    //alert(codigoReciboDua+"-"+montoReciboDua+"-"+fechaReciboDua+"-"+lstModalidadPago);
                    
                    //codigosPagosCompra.push(codigoReciboDua);               
                    $('#tblDetallePagosDua tbody tr:last').after('<tr>'+
                            '<td><input type="hidden" name="codigo[]" class="codigo" value="'+codigoReciboDua+'">' + codigoReciboDua + '</td>'+
                            '<td><input type="hidden" name="fecha[]" value="' + fechaReciboDua + '">' + fechaReciboDua + '</td>'+                            
                            '<td><input type="hidden" name="modalidad[]" value="' + lstModalidadPago + '">' + modalidad + '</td>'+
                            '<td><input type="hidden" name="monto[]" value="' + montoReciboDua + '">' + montoReciboDua + '</td>'+
                            '<td><a href="#" class="btnEliminarPagoCompra"><img src="/imagenes/eliminar.gif"></a></td>'+
                    '</tr>');
                    // Si pasa los proyectos se limpia el producto y se pone el cursor en la zona del siguiente producto:
                    $('#txtcodigoReciboDua').val('');
                    $('#txtmontoReciboDua').val('');
                    $('#txtfechaReciboDua').val('');
                    $('#txtcodigoReciboDua').focus();
                }
	});
        
        //Quitar un producto del detalle de la guia de pedido
	$('body').on('click', '.btnEliminarPagoCompra', function(e){
		e.preventDefault();
		$fila = $(this).parents('tr');
		//var codigo =$fila.find('.codigo').val();
                //eliminarValorArray(codigo,codigosProductos);
		$fila.remove();
                
		
		//agregaStockdisponible(idP,canP);
	});
        

	//Editar la cantidad del detalle de la guia de pedido
	$('body').on('click', '.btnEditarCantidad', function(e){
		e.preventDefault();
		//$(this).parents('tr').find('.txtCantidad').removeAttr('readonly').focus();
		$(this).parents('tr').find('.txtPrecio').removeAttr('readonly').focus();
	});
	//
	$('body').on('click', '.btnEditarPrecio', function(e){
		e.preventDefault();
		$(this).parents('tr').find('.txtPrecio').removeAttr('readonly').focus();
		//$(this).parents('tr').find('.txtCantidad').removeAttr('readonly').focus();
		
	});
	//
	$('.txtPrecio').live('blur',function(){
		padre=$(this).parents('tr');
		valorDescuento=padre.find('.porTxtDescuento').val();
		PrecioDescontado=parseFloat(padre.find('.txtPrecioDescontado').val());
		valorCantidad=parseFloat(padre.find('.txtCantidad').val());
		valorImporte=parseFloat(padre.find('.txtTotal').val());
		valorTotal=parseFloat($('#txtImporteTotal').val());
		nuevoPrecio=parseFloat(padre.find('.txtPrecio').val());

		if (valorDescuento=='') {
			valorDescuento=0;
		}else{
			valorDescuento=parseFloat(padre.find('.porTxtDescuento').val());
		}

		/*console.log(valorDescuento);
		console.log(PrecioDescontado);
		console.log(valorCantidad);
		console.log(valorImporte);
		console.log(valorTotal);
		console.log(nuevoPrecio);*/

		
		nuevoPrecioDescontado=(1-valorDescuento)*nuevoPrecio;
		//console.log('nuevo PrecioDescontado'+nuevoPrecioDescontado);
		nuevoImporte=nuevoPrecioDescontado*valorCantidad;
		nuevoTotal=valorTotal-valorImporte+nuevoImporte;

		padre.find('.txtPrecioDescontado').val(nuevoPrecioDescontado.toFixed(2));
		padre.find('.txtTotal').val(nuevoImporte.toFixed(2));
		//console.log('nuevo Total'+nuevoTotal);
		$('#txtImporteTotal').val(nuevoTotal.toFixed(2));



	});
	//Verificar la cantidad de producto al salir de txtCantidad
	$('body').on('blur', '.txtCantidad', function(e){
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
		}

	});
});

msgboxTitle="Orden de Ventas";

//Agrega un producto al detalle de la guia de pedido
function agregaProducto(saldo){
	
	var importeTotal = parseFloat($('#txtImporteTotal').val()).toFixed(2);
	
	ruta= "/producto/buscar/" + $('#txtIdProducto').val()+"/"+descuentosolicitado;
	console.log(ruta);
	$.ajax({
		url:ruta,
		type:'get',
		dataType:'json',
		success:function(data){
			console.log(data);
		
		var descuentosolicitadoid=descuentosolicitado;
		var descuentosolicitadovalor=data.descuentosolicitado;
		var descuentosolicitadotexto=data.descuentovalor;
		var preciolistasoles=data.preciolista;
		if (lblmoneda=="US $") {
			var preciolista=parseFloat(data.preciolistadolares).toFixed(2);
		}else{
			var preciolista=parseFloat(data.preciolista).toFixed(2);
		};	
		
		var preciosolicitado=((1-descuentosolicitadovalor)*preciolista).toFixed(2);
		var preciototal=parseFloat(preciosolicitado*cantidad).toFixed(2);

		descuentototal=cantidad*(preciolista-preciosolicitado);

		importeTotal=parseFloat(importeTotal)+parseFloat(preciototal);

		$('#tblDetalleOrdenVenta tbody tr:last').before('<tr>'+
			'<td class="codigo">' + 
				data.codigo +
				'<input type="hidden" value="' + data.idproducto + '" name="DetalleOrdenVenta[' + contador + '][idproducto]" class="txtIdProducto">'+
				'<input type="hidden" value="' + descuentosolicitadoid + '" name="DetalleOrdenVenta[' + contador + '][descuentosolicitado]" >'+
				'<input type="hidden" value="' + descuentosolicitadotexto + '" name="DetalleOrdenVenta[' + contador + '][descuentosolicitadotexto]" >'+
				'<input type="hidden" value="' + descuentosolicitadovalor + '" name="DetalleOrdenVenta[' + contador + '][descuentosolicitadovalor]" class="porTxtDescuento text-50">'+
			'</td>'+
			'<td>' + data.nompro + '</td>'+
			//'<td>' + data.codigoalmacen + '</td>'+
			'<td>' + preciolistasoles + '</td>'+
			'<td>'+
				'<input type="text" value="' + cantidad + '" name="DetalleOrdenVenta[' + contador + '][cantsolicitada]" class="txtCantidad numeric text-50" readonly>'+
			'</td>'+
			'<td>'+ lblmoneda +' <input type="text" value="' + preciolista + '" name="DetalleOrdenVenta[' + contador + '][preciolista]" class="txtPrecio numeric text-50" readonly></td>'+
			'<td> ( <b>'+descuentosolicitadotexto+' </b>)</td>'+
			'<td>'+ lblmoneda +' '+
				'<input type="text" value="' + preciosolicitado + '" name="DetalleOrdenVenta[' + contador + '][preciosolicitado]" class="txtPrecioDescontado text-50"  readonly>'+
			'</td>'+						
			'<input type="hidden" value="' + descuentototal.toFixed(2) + '" name="DetalleOrdenVenta[' + contador + '][tipodescuento]" class="txtDescuento text-50">'+ 
			'<td class="center"> '+ lblmoneda +' <input type="text" value="' + preciototal + '" class="txtTotal text-100 right" readonly></td>'+
			'<td><a href="#" class="btnEditarCantidad"><img src="/imagenes/editar.gif"></a></td>'+
			'<td><a href="#" class="btnEliminarProducto"><img src="/imagenes/eliminar.gif"></a></td>'+
		'</tr>');
		
		$('#txtImporteTotal').val(importeTotal.toFixed(2));
		verificaSaldoDisponible();
	},
	error:function(error){
		console.log('error');
	}
	});	
	
}

//Evita que se agrege dos veces un producto al detalle de la guia de pedidos
	function buscaProductoDetalleOrdenVenta(){
		//var codigoProducto = $.trim($('#txtCodigoProducto').val());
		var codigoProducto = $('#txtCodigoProducto').val();
		var existe = $("#tblDetalleOrdenVenta .codigo:contains('"+codigoProducto+"')").length;
		if(existe > 0){
			$.msgbox(msgboxTitle,'El producto <strong>' + codigoProducto + '</strong> ya esta agregado en el<br>detalle de la guia de pedido.');
			$('#msgbox-ok, #msgbox-cancel, #msgbox-close').click(function(){
				$('#txtCantidadProducto').val('');
				$('#txtDescuento').val('');
				$('#txtCodigoProducto').val('').focus();
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
//valores iniciales;
var codigosProductos = [];
var buenNumero = false;

function existeValorArray(id,cadena){
    var valor = false;
    cadena.length
    for(i=0;i<cadena.length;i++){
        if(cadena[i]==id){
            valor = true;
            break;
        }
    }
    return valor;
}

function eliminarValorArray(id,cadena){
    for(i=0;i<cadena.length;i++){
        if(cadena[i] == id){
            codigosProductos[i] = 'A';
            break;
        }
    }
    
    mostrarArray(codigosProductos);
}

function mostrarArray(cadena){
    for(i=0;i<cadena.length;i++){
        console.log(cadena[i]);
    }
}

function verificarNumeroFactura(){
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

function verificaNumero(){
    var numero = $('#txtNumero').val();
    var ruta = "/movimiento/buscaNumeroFactura/"+ numero;
    $.post(ruta, function(data){
        if(data!=0){
            alert("Número ya está registrado,verifique o llame a Sistemas anexo:125");
            $('#txtNumero').focus();
            buenNumero = false;
        }else{
            buenNumero = true;
        }
    });
}
