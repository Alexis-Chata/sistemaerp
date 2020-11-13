	//variables globales para esta vista
	var idCliente;
	var idProducto=0;
	var direccionDespacho;
	var direccionEnvio;
	var redondeo=0;
	var contador=0;
	var click=0;
        
	
$(document).ready(function(){
	redondeo=parseInt($('#redondeo').val());
	idCliente=$('#idCliente').val();
	direccionDespacho=$('#txtDireccionDespacho').val();
	direccionEnvio=$('#txtDireccionEnvio').val();
	contacto=$('#txtContacto').val();
	contador=parseInt($('#contador').val());
	verificarProductosIngresados();
	//variables generales


	cargadireccionDespacho(idCliente);
	cargaDireccionEnvio(idCliente);
	cargaContacto(idCliente);
        
        $('.liDescuento').hide();
	
	/********* Autocomplete ******/
	$('#txtProducto').autocomplete({
		source: "/producto/buscarAutocomplete/",
		select: function(event, ui){
			idProducto=ui.item.id;
			$('#idProducto').val(idProducto);
			$('#txtDescripcion').val(ui.item.tituloProducto);
			
		}
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
	
	$('#txtCliente').autocomplete({
		source: "/cliente/autocomplete2/",
		select: function(event, ui){
			$('#idCliente').val(ui.item.id);
			cargaSucursales(ui.item.id);
			
	}});
	$('#txtVendedor').autocomplete({
		source: "/vendedor/autocompletevendedor/",
		select: function(event, ui){
			$('#idVendedor').val(ui.item.id);
		}
	});
	
	/*************** Botones ***********/
	$('#btnActualizaOrdenVenta').click(function(e){
		e.preventDefault();
		if(confirm('¿Desea realmente Actualizar?')){
			actualizaOrdenVenta();
		}
	});


	$('#btnCambiarMoneda').click(function(e){
		e.preventDefault();
		if(confirm('Se cambiara la moneda para todo el pedido !!!')){
			cambiarmoneda();
		}
	});


	$('#btnResetearOrdenVenta').click(function(e){
		e.preventDefault();
		var idOrdenVenta=$('#idOrdenVenta').val();
		if(confirm('¿Realmente desea RESETEAR la Orden de Venta ::: ESTE PROCESO ES IRREVERSIBLE?')){
			resetearOrdenventa(idOrdenVenta);
		}
	});

	$('#btnSalir').click(function(e){
		e.preventDefault();
		window.location.href='/ventas/guiamadre';
	});
	
	$('.btnEliminar').live('click',function(e){
		e.preventDefault();
		var padre=$(this).parents('tr');
		padre.find('.estadoDetalle').val('0');
		var importe=parseFloat(padre.find('.importe').val()).toFixed(redondeo);
		var total=parseFloat($('#txtTotal').val()).toFixed(redondeo);
		padre.css('display','none');
		var nuevoTotal=total-importe;
		$('#txtTotal').val(nuevoTotal.toFixed(redondeo));
		$('#txtTotalCompleto').val(nuevoTotal.toFixed(redondeo));
	});
	$('.btnEditar').live('click',function(e){
		e.preventDefault();
		padreGeneral=$(this).parents('tr');
		padreGeneral.find('.cantSolicitada').removeAttr('readonly');
//		padreGeneral.find('.precioLista').removeAttr('readonly');
		padreGeneral.find('.cantSolicitada').focus();
	});
	$('.cantSolicitada').live('blur',function(e){
		e.preventDefault();
		var padre=$(this).parents('tr');
		var idDetalleOrdenVenta=parseInt(padre.find('.idDetalleOrdenVenta').val());
		var idProducto=parseInt(padre.find('.idProducto').val());
		var cantidadNueva=parseInt($(this).val());
		
		var cantidadInicial=parseInt(padre.find('.cantidadInicial').val());
		var cantidad=0;
		if(parseInt(idDetalleOrdenVenta)==0){
			cantidad=cantidadNueva;
		}else{
			if(cantidadNueva>cantidadInicial){
				cantidad=cantidadNueva-cantidadInicial;	
			}			
		}
		if($(this).val()==""){
			$(this).val(cantidadInicial);
			alert('La Cantidad no puede ser vacio');
		}else if(verificarStockDisponible(idProducto,cantidad)==true){
			if(idDetalleOrdenVenta==0){
				padre.find('.cantidadInicial').val(cantidadNueva);
			}
			calcularTotal(padre);
			$(this).attr('readonly','readonly');
			padre.find('.precioLista').focus();
		}else{
			$(this).val(cantidadInicial);
			alert('No hay suficiente stock');
		}
		
		
		
	});
        
        $('.txtPrecioOfertado').live('blur',function(e){
		e.preventDefault();
		var padre=$(this).parents('tr');
                calcularTotal(padre);
	});
        
	$('.precioLista').live('blur',function(e){
		e.preventDefault();
		var padre=$(this).parents('tr');
		var valorDescuento=parseFloat(1-parseFloat(padre.find('.descuentoValor').val()));
		var precioLista=parseFloat(padre.find('.precioLista').val()).toFixed(redondeo);
		var precioInicial=parseFloat(padre.find('.precioInicial').val()).toFixed(redondeo);
		if ($(this).val()!="") {
			var nuevoPrecioSolicitado=(precioLista*valorDescuento).toFixed(redondeo);
			padre.find('.precioSolicitado').val(nuevoPrecioSolicitado);
			calcularTotal(padre);
			
		}else{
			var nuevoPrecioSolicitado=(precioInicial*valorDescuento).toFixed(redondeo);
			padre.find('.precioSolicitado').val(nuevoPrecioSolicitado);
			calcularTotal(padre);
			$(this).val(precioInicial);
		}
		$(this).attr('readonly','readonly');
	});
	$('#btnActualizar').click(function(e){
		if(verificarCantidadRegistros()==false){
			e.preventDefault();
			alert('Tiene que tener al menos un registro para guardar los cambios');
		}else if(confirm('¿Desea Guardar los cambios del detalle?')){
			
			if(click==1){
				$('#btnActualizar').attr('disabled','disabled');
			}
			click++;
		}else{
			e.preventDefault();
		}
	});
	$('#btnAgregar').click(function(e){
		e.preventDefault();
		var idProducto=parseInt($('#idProducto').val());
		var cantidad=parseInt($('#txtCantidad').val());
//		var descuentoSolicitado=parseInt($('#lstDescuento').val());
		if($('#txtCantidad').val()==""){
			alert('Ingrese un valor');
//		}else if(cantidad==0){
			alert('Ingrese un valor mayor que cero');
		}else if(idProducto==0){
			alert('Seleccione un Producto');
		}else if($('#lstDescuento').val()==""){
			alert('Seleccione un Descuento');
		}else if(verificarProductosIngresados(idProducto)==false){
			alert('El Producto ya Fue agregado');
			limpiar();
		}else if(verificarStockDisponible(idProducto,cantidad)==false){
			alert('No hay suficiente stock');
		} else if ($('#lstPrecio').val() == 1 && !($('#txtPrecioOferta2').val() > 0)) {
                    $('#txtPrecioOferta2').focus();
                } else if ($('#lstPrecio').val() == 2 && !($('#txtNuevoDescuento').val() >= 0 && $('#txtNuevoDescuento').val() <= 30)) {
                    $('#txtNuevoDescuento').focus();
                } else{
			nuevoProducto(idProducto,cantidad);	
		}
	});
        	
	$('#btnAgregarPercepcion').click(function(e){
		//$(this).attr('disabled','disabled');
		var idOrdenVenta=$('#idOrdenVenta').val();
		var tipoAccion=1;
		var respta=verficaExistenciaPercepcion(idOrdenVenta,tipoAccion);
              
		console.log(respta.validacion);
		if (respta.validacion==false && respta.existe==0) {
			alert('No tiene Facturas creadadas');
		}else if (respta.validacion==false && respta.existe==1) {
			alert('No tiene Facturas creadadas, Pero tiene su Percepcion creada');
		}else if (respta.validacion==false && respta.existe==2) {
			alert('La(s) Factura(s) Electronica(s) Aun No ha(n) Sido Cargada(s).');
		}else if(respta.validacion==true && respta.existe==1){
			alert('Ya Tiene su percepcion creada');
		}
		else if(respta.validacion==true && respta.existe==0){
			cargaProgramacion(respta,tipoAccion);
		}
                
	});
	$('#btnEliminarPercepcion').click(function(e){
		//$(this).attr('disabled','disabled');
		var idOrdenVenta=$('#idOrdenVenta').val();
		var tipoAccion=2;
		var respta=verficaExistenciaPercepcion(idOrdenVenta,tipoAccion);
		
		if (respta.validacion==false && respta.existe==0) {
			alert('No se puede eliminar porque no tiene percepcion');
		}else if(respta.validacion==false && respta.existe==1){
			cargaProgramacion(respta,tipoAccion);
		}
		else if(respta.validacion==true && respta.existe==1){
			alert('No se puede eliminar porque tiene una factura activa');
		}else if(respta.validacion==true && respta.existe==0){
			alert('Tiene una factura activa y no tiene Percepcion ');
		}
	});
	$('.btnAumentarPercepcion').live('click',function(e){
		var idOrdenVenta=$('#idOrdenVenta').val();
		var tipoAccion=1;
		var respta=verficaExistenciaPercepcion(idOrdenVenta,tipoAccion);
		console.log(respta);
		if (respta.validacion==false && respta.existe==0) {
			alert('No tiene Facturas creadadas');
		}else if (respta.validacion==false && respta.existe==1) {
			alert('No tiene Facturas creadadas, Pero tiene su Percepcion creada');
		}else if(respta.validacion==true && respta.existe==1){
			alert('Ya Tiene su percepcion creada');
		}
		else if(respta.validacion==true && respta.existe==0){
			var padre=$(this).parents('tr');
			var idDetalleOrdenCobro=padre.find('.idDetalleOrdenCobro').val();
			var numDoc=padre.find('.numDoc').val();
			var montoPercepcion=$('#percepcion').val();
			var idOrdenGasto=$('#idOrdenGasto').val();
			var datosBusqueda=traerProgramacion(idDetalleOrdenCobro);
			if (datosBusqueda.situacion!="" && datosBusqueda.renovado!=0) {
				alert('Porque favor recargue la pagina los datos estan desactualizados');
			}else{
				aumentarPercepcion(idDetalleOrdenCobro,montoPercepcion,idOrdenGasto,numDoc);
			}
			
		}
			
		
	});
	$('.btnDisminuirPercepcion').live('click',function(e){
                console.log("ENTRO1");
		var idOrdenVenta=$('#idOrdenVenta').val();
		var tipoAccion=2;
		var respta=verficaExistenciaPercepcion(idOrdenVenta,tipoAccion);
		console.log("respuesta: "+respta);
		if (respta.validacion==false && respta.existe==0) {
			alert('No se puede eliminar porque no tiene percepcion');
		}else if(respta.validacion==false && respta.existe==1){
			var padre=$(this).parents('tr');
			var idDetalleOrdenCobro=padre.find('.idDetalleOrdenCobro').val();
			var numDoc=padre.find('.numDoc').val();
			var montoPercepcion=$('#percepcion').val();
			var idOrdenGasto=$('#idOrdenGasto').val();
			var saldoDoc=padre.find('.saldo').val();
			var datosBusqueda=traerProgramacion(idDetalleOrdenCobro);
			//console.log(datosBusqueda);
			var importedoc=datosBusqueda.importedoc;
			var saldodoc=datosBusqueda.saldodoc;
			if (parseFloat(montoPercepcion)>parseFloat(saldoDoc).toFixed(redondeo)) {
				alert('La percepcion es mayor que el saldo');
			}else if(parseFloat(montoPercepcion)>parseFloat(saldodoc).toFixed(redondeo)){
				alert('Porque favor recargue la pagina los datos estan desactualizados');
			}else if(parseFloat(importedoc).toFixed(redondeo)!=parseFloat(saldodoc).toFixed(redondeo)){
				alert('Porque favor recargue la pagina los datos estan desactualizados');
			}else if(parseFloat(montoPercepcion)==parseFloat(saldoDoc).toFixed(redondeo)){
				diminuirPercepcion(idDetalleOrdenCobro,montoPercepcion,idOrdenGasto,numDoc);
			}
			
		}
		else if(respta.validacion==true && respta.existe==1){
			alert('No se puede eliminar porque tiene una factura activa');
		}else if(respta.validacion==true && respta.existe==0){
			alert('Tiene una factura activa y no tiene Percepcion ');
		}else{
                    alert("ultimo");
                }
			
		
	});
	$('#btnNP').live('click',function(e){
		e.preventDefault();
		var idOrdenVenta=$('#idOrdenVenta').val();
		var montoPercepcion=$('#percepcion').val();
		var idOrdenGasto=$('#idOrdenGasto').val();
		creaProgramacionPercepcion(idOrdenVenta,montoPercepcion,idOrdenGasto);	
	});
	
	/************* Listas *******************/
	$('#lstDireccionDespacho').change(function(e){
		if($(this).val()!=""){
			$('#txtDireccionDespacho').val($('#lstDireccionDespacho option:selected').html());
		}else{
			$('#txtDireccionDespacho').val(direccionDespacho);
		}
		
	});
	$('#lstDireccionEnvio').change(function(e){
		if($(this).val()!=""){
			$('#txtDireccionEnvio').val($('#lstDireccionEnvio option:selected').html());
		}else{
			$('#txtDireccionEnvio').val(direccionEnvio);
		}
		
	});
	$('#lstContacto').change(function(e){
		if($(this).val()!=""){
			$('#txtContacto').val($('#lstContacto option:selected').html());
		}else{
			$('#txtContacto').val(direccionEnvio);
		}
		
	});


/*************** Contenedores *****************/
	$('#contenedor').dialog({
		autoOpen:false,
		modal:true,
		width:1000,
		height:600,
		close:function(){
			$('#contenedor').html('');
		}
	});
});
function verficaExistenciaPercepcion(orden,tipoAccion) {
	var retorno=new Object();
 	$.ajax({
		url:'/ordengasto/verificaPercepcion',
		type:'post',
		async: false,
		dataType:'json',
		data:{'orden':orden},
		success:function(respta){
			console.log(respta);
			retorno=respta;
			
		},
		error:function(error){
			console.log('error');
		}
	});
	return retorno;
}
function cargaProgramacion(data,tipoAccion) {
	//console.log(data);
	$.ajax({
		url:'/ordencobro/buscarDetallePercepcion',
		type:'post',
		async: false,
		dataType:'html',
		data:{'idOrdenGasto':data.idOrdenGasto,'montoPercepcion':data.montoPercepcion,'orden':$('#idOrdenVenta').val(),'tipoAccion':tipoAccion},
		success:function(resp){
			console.log(resp);
			$('#contenedor').html('');
			$('#contenedor').html(resp);
			$('#contenedor').dialog("open");
			
		}
	});
}
function aumentarPercepcion(idDetalleOrdenCobro,montoPercepcion,idOrdenGasto,numDoc) {
	//console.log(data);
	$.ajax({
		url:'/ordengasto/aumentarPercepcion',
		type:'post',
		async: false,
		dataType:'json',
		data:{'idDetalleOrdenCobro':idDetalleOrdenCobro,'montoPercepcion':montoPercepcion,'idOrdenGasto':idOrdenGasto,'numDoc':numDoc},
		success:function(resp){
			console.log(resp);
			if (resp.verificacion==true) {
				$('#contenedor').dialog('close');
				verificarCobro();
				alert('Se grabo Correctamente');
                                
			}else{
				alert('Hubo problemas al momento de grabar');
			}
			
			
		}
	});
}

function creaProgramacionPercepcion(idOrdenVenta,montoPercepcion,idOrdenGasto) {
	//console.log(data);
	$.ajax({
		url:'/ordencobro/creaProgramacionPercepcion',
		type:'post',
		async: false,
		dataType:'json',
		data:{'idOrdenVenta':idOrdenVenta,'montoPercepcion':montoPercepcion,'idOrdenGasto':idOrdenGasto},
		success:function(resp){
			//console.log(resp);
			if (resp.verificacion==true) {
				$('#contenedor').dialog('close');
				verificarCobro();
				alert('Se grabo Correctamente');
			}else{
				alert('Hubo problemas al momento de grabar');
			}
			
			
		}
	});
}

function diminuirPercepcion(idDetalleOrdenCobro,montoPercepcion,idOrdenGasto,numDoc) {
	console.log("entrar11")
	$.ajax({
		url:'/ordengasto/disminuirPercepcion',
		type:'post',
		async: false,
		dataType:'json',
		data:{'idDetalleOrdenCobro':idDetalleOrdenCobro,'montoPercepcion':montoPercepcion,'idOrdenGasto':idOrdenGasto,'numDoc':numDoc},
		success:function(resp){
			console.log(resp);
			if (resp.verificacion==true) {
				$('#contenedor').dialog('close');
				verificarCobro();
				alert('Se grabo Correctamente');
			}else{
				alert('Hubo problemas al momento de grabar');
			}
			
			
		}
	});
}
function traerProgramacion(idDetalleOrdenCobro) {
	//console.log(data);
	var retorno=new Object();
	$.ajax({
		url:'/ordencobro/traerProgramacion',
		type:'post',
		async: false,
		dataType:'json',
		data:{'idDetalleOrdenCobro':idDetalleOrdenCobro},
		success:function(resp){
			retorno=resp;			
		}
	});
	return retorno;
}
function calcularTotal(padre){/*
    PrecioDescontado = parseFloat(padre.find('.txtPrecioDescontado').val());
    valorCantidad = parseFloat(padre.find('.txtCantidad').val());
    valorImporte = parseFloat(padre.find('.txtTotal').val());
    valorTotal = parseFloat($('#txtImporteTotal').val());*/

    

    /*
    var tempDescOferta = ''; // REQ29450
    var TempTextDescOferta = 0;
    if (nuevoprecio > 0 && nuevoprecio <= precioLista) {
        //alert('siiiii'); 
        tempDescOferta = 100 - ((nuevoprecio*100)/precioLista);
        TempTextDescOferta = tempDescOferta.toFixed(2);
        if (tempDescOferta == '0.00') {
            tempDescOferta = '-';
        } else {
            tempDescOferta = tempDescOferta.toFixed(2) + "%";
        }
    } else {
        //alert('siiiii j9ojjijij: np_ ' + nuevoPrecio + ' pd: ' + PrecioDescontado); 
        nuevoprecio = precioLista;
        tempDescOferta = '-';
        padre.find('.txtPrecioOfertado').val(nuevoprecio)
        if (bandera == 0) { 
            alert("El precio ofertado debe ser menor al precio lista");
        }
    }
    
    */
    
    
    var cantidad=parseInt(padre.find('.cantSolicitada').val());
    //var nuevoprecio = parseFloat(padre.find('.txtPrecioOfertado').val());
    var precioLista = parseFloat(padre.find('.precioLista').val()).toFixed(redondeo);
    
    var descuentosolicitadoid=0;
    var descuentosolicitadovalor=0;
    var descuentosolicitadotexto=0;

    var TextDescuento = 0;
    var importeOfertado = parseFloat(padre.find('.txtPrecioOfertado').val()).toFixed(redondeo);
//    console.log("IMPORTE OFERTADO: " + importeOfertado);
    if (importeOfertado*1 > precioLista*1) {
        importeOfertado = precioLista;
    } else {
        TextDescuento = 100-((importeOfertado*100)/precioLista);
        //alert('TextDescuento 1: ' + TextDescuento);
        if (TextDescuento <= 30) {
            //alert("si");
        } else {
            TextDescuento = 30;
            //alert("no");
        }
    }
//    console.log("IMPORTE OFERTADO 2: " + importeOfertado);
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
//            alert("descuentosolicitadoid :" + descuentosolicitadoid);
//            alert("descuentosolicitadovalor :" + descuentosolicitadovalor);
//            alert("descuentosolicitadotexto :" + descuentosolicitadotexto);
        }
    });
    
    var tempDescOferta = '';
    var tempTextDescOferta = 0;
    var preciosolicitado=((1-descuentosolicitadovalor)*precioLista).toFixed(2);
    var preciototal = 0;
//    console.log("precio solicitado: " + preciosolicitado);
    if (importeOfertado*1 > 0 && importeOfertado*1 < preciosolicitado*1) {
        tempDescOferta = 100 - ((importeOfertado*100)/preciosolicitado);
        tempTextDescOferta = tempDescOferta.toFixed(2);
        tempDescOferta = tempDescOferta.toFixed(2) + "%";
//        console.log("entre el %");
    } else {
//        console.log("no entre el %");
        importeOfertado = preciosolicitado;
        tempDescOferta = '-';                    
    }
    
//    console.log("IMPORTE OFERTADO 3: " + importeOfertado);
    preciototal = parseFloat(importeOfertado * cantidad).toFixed(2);
//    console.log("precio total 4: " + preciototal);
//    var descuentototal = cantidad * (precioLista - preciosolicitado);
    
    var idProducto = padre.find('.idProducto').val();

    var tipoDescuento=cantidad*(precioLista-preciosolicitado);
 
    $('#textIddescuentosolicitado' + idProducto).val(descuentosolicitadoid);
    $('#textIddescuentosolicitadotexto' + idProducto).val(descuentosolicitadotexto);
    $('#textIddescuentosolicitadovalor' + idProducto).val(descuentosolicitadovalor);
    padre.find('.tipoDescuento').val(tipoDescuento.toFixed(redondeo));

    padre.find('.lblDescuento').html(descuentosolicitadotexto);
    padre.find('.precioSolicitado').val(preciosolicitado);
    padre.find('.textDescOferta').val(tempTextDescOferta);
    padre.find('.txtDescOferta').html(tempDescOferta);
    padre.find('.txtDescuento').val(tipoDescuento);
    var importeaeliminar = parseFloat(padre.find('.importe').val());
    padre.find('.importe').val(preciototal);
    
    var total=parseFloat($('#txtTotal').val()).toFixed(redondeo);

    var nuevoTotal=total - parseFloat(importeaeliminar) + parseFloat(preciototal);
    var nuevoTotal = parseFloat(nuevoTotal);
    $('#txtTotal').val(nuevoTotal.toFixed(redondeo));
    $('#txtTotalCompleto').val(nuevoTotal.toFixed(redondeo));
    

        
    /*
    var importetotaldspseliminacion = 0;
    $('.importe').each(function(e){
        //alert('val; ' + $(this).val());
        importetotaldspseliminacion += parseFloat($(this).val());
       //alert('importedespacho; ' + importetotaldspseliminacion);
    });
    //alert('importedespacho final ' + importetotaldspseliminacion);
    
    $('#txtTotalCompleto').val(importetotaldspseliminacion.toFixed(4));
    $('#txtTotal').val(importetotaldspseliminacion.toFixed(4));*/
    /*
    var fila='<tr>'+

		
                '<td>'+ lblmoneda +' '+
                        '<input type="text" value="' + importeOfertado + '" name="detalle[' + contador + '][precioofertado]"  id="idtxtPrecioOferta' + datos.idproducto + '" class="txtPrecioOfertado text-50"  readonly>'+
                '</td>'+
		'<td style="text-align: right">'+'<input type="hidden" value="' + descuentototal.toFixed(2) + '" name="detalle[' + contador + '][tipodescuento]" class="txtDescuento text-50">'+ 
			'<input style="text-align:right;" type="text" class="importe numeric text-100 " value="'+preciototal+'" readonly>'+
		'</td>'+
		'<td><a class="btnEditar" href="#"><img src="/imagenes/editar.gif"></a></td>'+
		'<td><a class="btnEliminar" href="#"><img src="/imagenes/eliminar.gif"></a></td>'+
		'</tr>';
	
	return fila;	
  
    
    
	
	
	var precioSolicitado=parseFloat(padre.find('.precioSolicitado').val()).toFixed(redondeo);
	var tipoDescuento=(cantidad*(precioLista-precioSolicitado)).toFixed(redondeo);
	var importeInicial=parseFloat(padre.find('.importe').val()).toFixed(redondeo);
	var importeNuevo=cantidad*precioSolicitado;
	var totalInicial=parseFloat($('#txtTotal').val()).toFixed(redondeo);
	var totalNuevo=total=totalInicial-importeInicial+importeNuevo;
	
	padre.find('.tipoDescuento').val(parseFloat(tipoDescuento).toFixed(redondeo));
	padre.find('.importe').val(importeNuevo.toFixed(redondeo));
	$('#txtTotal').val(totalNuevo.toFixed(redondeo));
	$('#txtTotalCompleto').val(totalNuevo.toFixed(redondeo));*/
    
}
function verificarProductosIngresados(idProducto){
	var verificacion=true;
	
	$( ".idProducto" ).each(function( index ) {
		//console.log($(this).val());
		padre=$(this).parents('tr');
		estado=parseInt(padre.find('.estadoDetalle').val());
		if(idProducto==parseInt($(this).val()) && estado==1){
			verificacion=false;
		}
	});
	return verificacion;
	
}
function verificarStockDisponible(idProducto,cantidad){
	var verificacion=true;
	$.ajax({
		url:'/producto/cantidadStock/'+idProducto,
		type:'post',
		dataType:'json',
		async: false,
		success:function(resp){
			//console.log(resp);
			if(resp.stockDisponible<cantidad){
				verificacion=false;
			}
		}
	});
	return verificacion;
}
function verificarCantidadRegistros(){
	var verificacion=true;
	var c=0;
	$( ".idProducto" ).each(function( index ) {
		//console.log($(this).val());
		var padre=$(this).parents('tr');
		var estado=parseInt(padre.find('.estadoDetalle').val());
		if(estado==1){
			c++;
		}
	});
	if(c==0){
		verificacion=false;
	}
	return verificacion;
}
function nuevoProducto(idProducto,cantidad){
	$.ajax({
		url:'/producto/buscar/'+idProducto+'/0',
		type:'post',
		dataType:'json',
		async: false,
		success:function(resp){
			//console.log(resp);
			$('#tblProductos tbody tr:last').before(crearFila(resp,cantidad));
			limpiar();
		}
	});
	
}
function cargaSucursales(idCliente){
	$.ajax({
		url:'/cliente/bucaZonasxCliente',
		type:'post',
		dataType:'html',
		data:{'idCliente':idCliente},
		success:function(resp){
			$('#lstSucursal').html('');
			$('#lstSucursal').html(resp);
			cargadireccionDespacho(idCliente);
			cargaDireccionEnvio(idCliente);
			cargaTransporte(idCliente);
			cargaContacto(idCliente);
			$('#txtDireccionDespacho').val('');
			$('#txtDireccionEnvio').val('');
			direccionDespacho="";
			direccionEnvio="";
			
		}
	});
	
}
function cargadireccionDespacho(idCliente){
	$.ajax({
		url:'/cliente/direccion_despacho',
		type:'post',
		dataType:'html',
		data:{'idcliente':idCliente},
		success:function(resp){
			$('#lstDireccionDespacho').html('');
			$('#lstDireccionDespacho').html(resp);

		}
	});
	
}
function cargaDireccionEnvio(idCliente){
	$.ajax({
		url:'/cliente/direccion_fiscal',
		type:'post',
		dataType:'html',
		data:{'idcliente':idCliente},
		success:function(resp){
			$('#lstDireccionEnvio').html('');
			$('#lstDireccionEnvio').html(resp);

		}
	});
	
}
function cargaContacto(idCliente){
	$.ajax({
		url:'/cliente/contactos',
		type:'post',
		dataType:'html',
		data:{'idcliente':idCliente},
		success:function(resp){
			$('#lstContacto').html('');
			$('#lstContacto').html(resp);

		}
	});
	
}
function cargaTransporte(idCliente){
	$.ajax({
		url:'/facturacion/buscatransporte/'+idCliente,
		type:'post',
		dataType:'html',
		success:function(resp){
			$('#lstTransporte').html('');
			$('#lstTransporte').html(resp);
		}
	});
	
}
function actualizaOrdenVenta(idCliente){
	$.ajax({
		url:'/ordenventa/actualizaOrdenVenta',
		type:'post',
		dataType:'json',
		async: false,
		data:$('#frmOrdenVenta').serialize(),
		success:function(resp){
			console.log(resp);
			if(resp.validacion==true){
				alert('Se grabo correctamente');
			}else{
				alert('Hubo problemas al momento de grabar');
			}
		}
	});
	
}

function cambiarmoneda(){
	idOrdenVenta=$('#idOrdenVenta').val();
	idMoneda=$('#txtMoneda').val();
	$.ajax({
		url:'/ordenventa/cambiarmoneda',
		type:'post',
		dataType:'html',
		data:{'idOrdenVenta':idOrdenVenta,'idMoneda':idMoneda},
		success:function(resp){		
			console.log(resp);
		}
	});
}

function resetearOrdenventa(idOrdenVenta){
	idOrdenVenta=$('#idOrdenVenta').val();
	$.ajax({
		url:'/ordenventa/resetearOrdenventa/',
		type:'post',
		dataType:'html',
		data:{'idOrdenVenta':idOrdenVenta},
		success:function(resp){		
			console.log(resp);
		}
	});

	window.location.href='/ordenventa/editarordenventa/'+idOrdenVenta;
}

function verificarCobro(){
	idOrdenVenta=$('#idOrdenVenta').val();
	$.ajax({
		url:'/ordencobro/verificarCobro',
		type:'post',
		dataType:'html',
		data:{'idOrdenVenta':idOrdenVenta},
		success:function(resp){		
			console.log(resp);
		}
	});
}

function limpiar(){
	$('#txtProducto').val('');
	$('#txtDescripcion').val('');
	$('#idProducto').val(0);
	$('#txtCantidad').val('');
	$('#lstDescuento').val('');
        $('#txtNuevoDescuento').val('');
        $('#txtPrecioOferta2').val('');
        $('#chkVale').prop('checked', false);
}
function crearFila(datos,cantidad){
    
	var validaelijemoneda=$('#txtMoneda').val();
	if(validaelijemoneda=="-1"){
		alert("Antes de registrar productos, debe elegir moneda");
		exit;
	}
	var lblmoneda=$('#lblMoneda').val();
	var tipoCambio=parseFloat($('#txtTipoCambioValor').val()).toFixed(2);

	var descto=1-parseFloat(datos.descuentosolicitado);
	
	if (lblmoneda=="US $") {
		var preciolista=parseFloat(datos.preciolistadolares).toFixed(2);
	}else{
		var preciolista=parseFloat(datos.preciolista).toFixed(2);
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
                    //alert('TextDescuento 1: ' + TextDescuento);
                    if (TextDescuento <= 30) {
                        //alert("si");
                    } else {
                        TextDescuento = 30;
                        //alert("no");
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
        var descuentosolicitadoid=0;
        var descuentosolicitadovalor=0;
        var descuentosolicitadotexto=0;
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
        var precioSolicitado=((1-descuentosolicitadovalor)*preciolista).toFixed(2);
        var preciototal = 0;

        if (importeOfertado > 0 && importeOfertado < precioSolicitado) {
            tempDescOferta = 100 - ((importeOfertado*100)/precioSolicitado);
            tempTextDescOferta = tempDescOferta.toFixed(2);
            tempDescOferta = tempDescOferta.toFixed(2) + "%";
        } else {
            importeOfertado = precioSolicitado;
            tempDescOferta = '-';                    
        }
        
        preciototal=parseFloat(importeOfertado*cantidad).toFixed(2);
        descuentototal=cantidad*(preciolista-precioSolicitado);
        var importeTotal = parseFloat($('#txtTotalCompleto').val()).toFixed(2);
        importeTotal=parseFloat(importeTotal)+parseFloat(preciototal);
                

//	var precioSolicitado=(preciolista*descto).toFixed(redondeo);
	var tipoDescuento=cantidad*(datos.preciolista-precioSolicitado);
	//var importe=parseFloat(precioSolicitado*cantidad).toFixed(redondeo);
//	var total=parseFloat($('#txtTotal').val()).toFixed(redondeo);
//	total=(parseFloat(total)+parseFloat(importe)).toFixed(redondeo);
	contador++;
        
        $('#txtTotalCompleto').val(importeTotal.toFixed(2));
        $('#txtTotal').val(importeTotal.toFixed(2));
        $('#txtPrecioOferta2').val('');
        $('#txtNuevoDescuento').val('');
	
	var fila='<tr>'+
		'<td>'+datos.codigo+
	
			'<input type="hidden" value="0" class="idDetalleOrdenVenta"  name="detalleOV['+contador+'][iddetalleordenventa]">'+
			'<input type="hidden" value="'+datos.idproducto+'" class="idProducto" name="detalle['+contador+'][idproducto]">'+
			'<input type="hidden" value="1" class="estadoDetalle" name="detalle['+contador+'][estado]">'+
                        
			'<input type="hidden" value="'+descuentosolicitadoid+'" id="textIddescuentosolicitado'+datos.idproducto+'" name="detalle['+contador+'][descuentosolicitado]">'+
			'<input type="hidden" value="'+descuentosolicitadotexto+'" id="textIddescuentosolicitadotexto'+datos.idproducto+'" class="descuentoValor" name="detalle['+contador+'][descuentosolicitadotexto]">'+
			'<input type="hidden" value="'+descuentosolicitadovalor+'" id="textIddescuentosolicitadovalor'+datos.idproducto+'" name="detalle['+contador+'][descuentosolicitadovalor]">'+
			'<input type="hidden" value="'+tipoDescuento+'" class="tipoDescuento" name="detalle['+contador+'][tipodescuento]">'+
		'</td>'+
		'<td>'+datos.nompro+'</td>'+
		'<td>'+datos.codigoalmacen+'</td>'+
		'<td>'+
			'<input style="text-align:right;" type="text" value="'+cantidad+'" name="detalle['+contador+'][cantsolicitada]" class="cantSolicitada numeric text-50" readonly="readonly">'+
			'<input type="hidden" class="cantidadInicial" value="'+cantidad+'"  name="producto['+contador+'][cantidadInicial]">'+
		'</td>'+
		'<td><input style="text-align:right;" name="detalle['+contador+'][preciolista]"   type="text" value="'+preciolista+'" class="precioLista numeric text-50" readonly="readonly">'+
			'<input type="hidden" class="precioInicial" value="'+preciolista+'"></td>'+
		'<td id="tdDescuento' + datos.idproducto + '"> ( <b class="lblDescuento">'+descuentosolicitadotexto+' </b>)</td>'+
		'<td>'+
			'<input style="text-align:right;" type="text" value="'+precioSolicitado+'" name="detalle['+contador+'][preciosolicitado]" class="precioSolicitado numeric text-50" readonly="readonly" >'+
			
		'</td>'+
                '<td>' + 
                    '<input type="hidden" value="' + tempTextDescOferta + '" name="detalle[' + contador + '][descuentooferta]" id="textDescOferta' + datos.idproducto + '" class="textDescOferta text-50"  readonly>'+
                    '<label class="txtDescOferta" id="idtxtDescOferta' + datos.idproducto + '">' + tempDescOferta + '</label>' + 
                '</td>' +
                '<td>'+ lblmoneda +' '+
                        '<input type="text" value="' + importeOfertado + '" name="detalle[' + contador + '][precioofertado]"  id="idtxtPrecioOferta' + datos.idproducto + '" class="txtPrecioOfertado text-50" >'+
                '</td>'+
		'<td style="text-align: right">'+'<input type="hidden" value="' + descuentototal.toFixed(2) + '" name="detalle[' + contador + '][tipodescuento]" class="txtDescuento text-50">'+ 
			'<input style="text-align:right;" type="text" class="importe numeric text-100 " value="'+preciototal+'" readonly>'+
		'</td>'+
		'<td><a class="btnEditar" href="#"><img src="/imagenes/editar.gif"></a></td>'+
		'<td><a class="btnEliminar" href="#"><img src="/imagenes/eliminar.gif"></a></td>'+
		'</tr>';
	
	return fila;	
}
