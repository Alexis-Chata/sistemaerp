$(document).ready(function(){

	/************************
	Funciones de Movimiento de Productos
	************************/

	$('legend').wrapInner('<span></span>');
	$('form ul li input[type="submit"]').parent().css('text-align','center');
	$('.btnEliminar').click(function(e){
		e.preventDefault();
		$row=$(this).attr('href');
		$.msgbox("Eliminaci&oacute;n de Registro", "&iquest;Est&aacute; seguro de eliminar el registro?",'<a href="' + $row + '">Si</a>');
		execute();
	});
	$("#seleccion").change(function(){
		var id=$("#seleccion option:selected").text();
		var url='/almacen/movstock/'+id;
		window.location=url;
	});
	$("#seleccionrep").change(function(){
		var id=$("#seleccionrep option:selected").text();
		var url='/almacen/movstockrep/'+id;
		window.location=url;
	});
	$('#contenedordetallemovimiento').hide();
	$('.detalledelMovimiento').click(function(e){
		e.preventDefault();
		idcontenedor=$(this).attr('id');
		//alert(idcontenedor);
		$.ajax({
			url:'/movimiento/detalle',
			type:'get',
			data:{'idcontenedor':idcontenedor},
			success:function(resp){
				//console.log(resp);
				$('#contenedordetallemovimiento').hide('Blind');
				$('#tablacontenedor tbody').html(resp);
				$('#contenedordetallemovimiento').show('Blind');
			},
			error:function(error){
				//console.log('error');
			}
		});
	});
	$('.detalleRepuesto').click(function(e){
		e.preventDefault();
		idcontenedor=$(this).attr('id');
		//alert(idcontenedor);
		$.ajax({
			url:'/movimiento/detalleRepuesto',
			type:'get',
			data:{'idcontenedor':idcontenedor},
			success:function(resp){
				//console.log(resp);
				$('#contenedordetallemovimiento').hide('Blind');
				$('#tablacontenedor tbody').html(resp);
				$('#contenedordetallemovimiento').show('Blind');
			},
			error:function(error){
				//console.log('error');
			}
		});
	});
	$("#btnCerrarDetalle").click(function(e){
		e.preventDefault();
		$('#contenedordetallemovimiento').hide('Blind');
	});

	$('#lsttipodocumento').change(function(){
		if ($(this).val()=="") {
			$('#serie').attr('readonly','readonly');
			$('#ndocumento').attr('readonly','readonly');
		}else{
			$('#serie').removeAttr('readonly');
			$('#ndocumento').removeAttr('readonly');
		}
	});
        
	$('.btnEditar').click(function(e){
		e.preventDefault();
		$row=$(this).attr('href');
		$.msgbox("Actualizaci&oacute;n de Registro", "&iquest;Est&aacute; seguro de actualizar el registro?",'<a href="' + $row + '">Si</a>');
		execute();
	});
	$('input[type=text]').blur(function(){
		$(this).val($(this).val().toUpperCase());
	})

	/*Autocompletes*/
    //Autocomplete Producto REPUESTO
    $('#txtCodigoProductoRepuesto').autocomplete({
        minLength: 2,
        source: function (request, response) {
            $.ajax({
                url: "/producto/buscarAutocompleteRepuesto/",
                dataType: "json",
                data: {term: request.term, idlinea: $('#lstLinea option:selected').val()},
                success: function (data) {
                    console.log(data);
                    response(data);
                }
            });
        },
        select: function (event, ui) {
            $('#txtIdProducto').val(ui.item.id);
            $('#txtTituloProducto').val(ui.item.tituloProducto);
            $('#txtCantidadProducto').focus();
            
        }
    });

	//Agregar detalle movimiento
	$('#btnAgregarDetalleMovimiento').click(function(e){
		e.preventDefault();
		var contador = parseInt($('#contador').val()) + 1;
		//var ruta = "/producto/buscar/";
		var ruta = "/producto/buscarxIdProducto/";
		var valorid=$('#txtIdProducto').val();
		var cantidad = $('#txtCantidad').val();
		var $element = $('#txtCantidad');
		var idProducto = $('#txtIdProducto').val();
		var cantidadSalida = parseInt($('#txtCantidad').val());
		if(!$('#lstTipoMovimiento, #txtCodigoProducto, #txtCantidad').valida()){
			return false;
		}else if(!buscaProductoDetalleMovimiento()){
			return false;
		}else if(!existeidProducto()){
			return false;
		}else if(!verificaStockDisponible(cantidadSalida, idProducto, $element)){
			return false;
		}else{
			$('.body:last').show();
			//$('.show:last').attr('title','Ocultar').html('-')
			//esta funcion esta funcionando casi completo 
			//falta revisar consulta en  producto/buscar
			cargaDetalleMovimiento(ruta, cantidad,valorid);
			$('#contador').attr('value', contador);
			$('#txtCodigoProducto').attr('value','').focus();
			$('#txtCantidad').attr('value','');
			$('#error').text('');
		}
	});

	$('#lstTipoMovimiento').attr('value','').focus();

	//Agregar detalle movimiento REPUESTO
	$('#btnAgregarDetalleMovimientoRep').click(function(e){
		e.preventDefault();
		var contador = parseInt($('#contador').val()) + 1;
		//var ruta = "/producto/buscar/";
		var ruta = "/producto/buscarxIdProducto/";
		var valorid=$('#txtIdProducto').val();
		var cantidad = $('#txtCantidad').val();
		var obs = $('#txtObservaciones').val();
		var $element = $('#txtCantidad');
		var idProducto = $('#txtIdProducto').val();
		var cantidadSalida = parseInt($('#txtCantidad').val());
		if(!$('#lstTipoMovimiento, #txtCodigoProductoRepuesto, #txtObservaciones, #txtCantidad').valida()){
			return false;
		}else if(!buscaProductoDetalleMovimientoRep()){
			return false;
		}else if(!existeidProducto()){
			return false;
		}else if(!verificaStockDisponible(cantidadSalida, idProducto, $element)){
			return false;
		}else{
			$('.body:last').show();
			//$('.show:last').attr('title','Ocultar').html('-')
			//esta funcion esta funcionando casi completo 
			//falta revisar consulta en  producto/buscar
			cargaDetalleMovimientoRep(ruta, cantidad, valorid, obs);
			$('#contador').attr('value', contador);
			$('#txtCodigoProductoRepuesto').attr('value','').focus();
			$('#txtCantidad').attr('value','');
			$('#error').text('');
		}
	});

	$('#txtOrdenCompra').autocomplete({
		source: "/ordencompra/autoCompleteAprobados/",
		select: function(event, ui){
			$('#idOrdenCompra').val(ui.item.id);
		}
	});

	//Registrar Movimiento
	$('#frmMovimientoNuevo').validate({
		ignore: '.valida-no-submit'
	});

	$('#frmMovimientoNuevo').submit(function(){
		cantidadDetalle = $('#tblDetalleMovimiento tbody tr').length;
		$('#txtCantidadDetalleMovimiento').attr('value',cantidadDetalle);
		if(!existeDetalleMovimiento()){
			return false;
		}else if(!$('input.required').valida()){
			$('#txtCodigoProducto, #txtCantidad').addClass('required');
			return false;
		}else{
			return true;
		}
	});
	$('#btnRegistrarMovimiento').click(function(e){
		
		if ($('#lsttipodocumento').val()=="") {

			if(($('#tblDetalleMovimiento tbody tr').length)==0){
				e.preventDefault();
				
				$('#error').css({'color':'red'}).html('Agrege al menos una fila');

			}

		}else if($('#lsttipodocumento').val()!="" && $('#serie').val()!="" && $('#ndocumento').val()!=""){
			if(($('#tblDetalleMovimiento tbody tr').length)==0){
				e.preventDefault();
				$('#error').css({'color':'red'}).html('Agrege al menos una fila');

			}
		}else{
			e.preventDefault();
			$('#error').css({'color':'red'}).html('Ingrese la Serie o N° de Documento');
		}
		
	});
	
	//Cargar concepto movimiento
	$('#lstTipoMovimiento').change(function(){
		cargaConceptoMovimiento();
		$('#tblDetalleMovimiento tbody').empty();
	});
	
	//Mostrar el detalle del movimiento
	$('.btnMostrarDetalleMovimiento').click(function(e){
		e.preventDefault();
		var ruta = $(this).attr('href');
		mostrarDetalleMovimiento(ruta);
	});
	
	//Quitar un detalle movimiento
	$('body').on('click','.btnQuitarDetalleMovimientos', function(e){
		e.preventDefault();
		$fila = $(this).parents('tr');
		$fila.remove();
	});
	//Quitar detalle orden compra
	$('body').on('click','.btnQuitaDetalleOrdenCompra', function(e){
			e.preventDefault();
			$fila=$(this).parents('tr');
			$fila.find('.txtEstado').attr('value','0');
			$fila.find('.required').removeClass('required');
			$fila.hide();
	});
	//Editar un detalle movimiento
	$('body').on('click', '.btnEditarDetalleMovimiento', function(e){
			e.preventDefault();
			$(this).parents('tr').find('.txtCantidadDetalle').removeAttr('readonly').focus();
	});
	//Impedir que la cantidad de detalle movimiento quede vacio,cero, o mayor al stock disponible
	//en caso de se una salida.
	$('body').on('blur', '.txtCantidadDetalle', function(){
		cantidadSalida = parseInt(this.value);
		var idProducto = $(this).parents('tr').find('.txtIdProducto').val();
		if(verificaStockDisponible(cantidadSalida, idProducto, $(this))){
			$(this).attr('readonly',true);
		}
	});
	/*Cancelar orden de compra y modificacion*/
	$('#btnCancelar').click(function(e){
		e.preventDefault();
		window.location = '/almacen/movstock';
	});

	/*Cancelar orden de compra y modificacion*/
	$('#btnCancelarRep').click(function(e){
	e.preventDefault();
	window.location = '/almacen/movstockrep';
	});
});

function cargaConceptoMovimiento(){
	idTipoMovimiento = $('#lstTipoMovimiento').val();
	if(idTipoMovimiento != ""){
		var page = "/movimiento/listaConceptoMovimiento/" + idTipoMovimiento;
		$.post(page, function(data){
			$('#lstConceptoMovimiento').html('<option value="">-- Seleccionar uno --' + data);
		});
	}else{
		$('#lstConceptoMovimiento').html('<option value="">-- Seleccionar uno --');
	}
}
function existeDetalleMovimiento(){
	if($('#tblDetalleMovimiento tbody tr').length == 0){
		$.msgbox('Movimiento nuevo','No existe ningun detalle movimiento para registrar');
		execute();
		$('#txtCodigoProducto, #txtCantidad').addClass('required');
		return false;
	}else{
		$('#txtCodigoProducto, #txtCodigoProductoRepuesto, #txtCantidad').removeClass('required');
		return true;
	}
}
function existeProducto(){
	var codigoProducto = $('#txtCodigoProducto').val();
	var existe=1;
	$.ajax({
		async:false,
		url: '/producto/existe/' + codigoProducto,
		type: "POST",
		dataType: "json",
		success: function(data){existe = data.existe;}
		
	});
	if(existe == 0){
		$.msgbox('Detalle movimiento','El producto <strong>' + codigoProducto + "</strong> no existe.")
		execute();
		return false;
	}else{
		return true;
	}
}

function existeidProducto(){
	var IdProducto = $('#txtIdProducto').val();
	var existe=1;
	$.ajax({
		async:false,
		url: '/producto/existe/' + IdProducto,
		type: "POST",
		dataType: "json",
		success: function(data){existe = data.existe;}
		
	});
	if(existe == 0){
		$.msgbox('Detalle movimiento','El producto con <strong>Id=' + codigoProducto + "</strong> no existe.")
		execute();
		return false;
	}else{
		return true;
	}
}

function buscaProductoDetalleMovimiento(){
	var codigoProducto = $.trim($('#txtCodigoProducto').val());
	var existe = $('#tblDetalleMovimiento .codigo:contains("' + codigoProducto + '")').length;
	var x=1;
	if(existe > 0){
		$.msgbox('Movimiento Nuevo','El productoooooo <strong>' + codigoProducto + '</strong> ya esta agregado en el<br>detalle del movimiento.');
		execute();
		x=0;
	}
	return x;
}

function buscaProductoDetalleMovimientoRep(){
	var codigoProducto = $.trim($('#txtCodigoProductoRepuesto').val());
	var existe = $('#tblDetalleMovimiento .codigo:contains("' + codigoProducto + '")').length;
	var x=1;
	if(existe > 0){
		$.msgbox('Movimiento Nuevo','El productoooooo <strong>' + codigoProducto + '</strong> ya esta agregado en el<br>detalle del movimiento.');
		execute();
		x=0;
	}
	return x;
}

//Cargar detalle movimiento
function cargaDetalleMovimiento(ruta, cantidad,valorid){
	var rutaImagen = $('#txtRutaImagen').val();
	var contador = $('#contador').val();
	$.ajax({
		url:ruta,
		dataType:'json',
		data:{'idvalor':valorid},
		success:function(data){
			console.log(data);
			if ((data.foto)=="") {
				rutaCompleta='/public/imagenes/sinFoto.jpg';
			}else{
				rutaCompleta=rutaImagen + data.codigo + '/' + data.foto;
			}

			
			$('#tblDetalleMovimiento tbody').append('<tr>'+
			'<td>'+
				'<input type="hidden" name="Detallemovimiento[' + contador + '][pu]" value="' + data.precio + '">'+
				'<input type="hidden" name="Detallemovimiento[' + contador + '][stockactual]" value="' + data.stockactual + '">'+
				'<input type="hidden" name="Detallemovimiento[' + contador + '][idproducto]" value="' + data.idproducto + '" class="txtIdProducto">'+
				'<input type="hidden" name="Detallemovimiento[' + contador + '][stockdisponibledm]" value="' + data.stockdisponible + '">'+
				'<img src="' + rutaCompleta+ '" width="50" height="40">'+
				'</td>'+
			'<td class="codigo">'+data.codigo+'</td>'+
			'<td>'+data.nompro+'</td>'+
			'<td>'+data.marca+'</td>'+
			'<td><input type="text" name="Detallemovimiento[' + contador + '][cantidad]" value="' + cantidad + '" class="txtCantidadDetalle required numeric" readonly style="width:40px"></td>'+
			'<td><a href="#" class="btnEditarDetalleMovimiento"><img src="/imagenes/editar.gif"></a></td>'+
			'<td><a href="#" class="btnQuitarDetalleMovimientos"><img src="/imagenes/eliminar.gif"></a></td>'+
		'</tr>');
		},
		error:function(){
			console.log('error');
		}
	});
}

//Cargar detalle movimiento REPUESTO
function cargaDetalleMovimientoRep(ruta, cantidad, valorid, obs){
	var rutaImagen = $('#txtRutaImagen').val();
	var contador = $('#contador').val();
	$.ajax({
		url:ruta,
		dataType:'json',
		data:{'idvalor':valorid},
		success:function(data){
			console.log(data);
			if ((data.foto)=="") {
				rutaCompleta='/public/imagenes/sinFoto.jpg';
			}else{
				rutaCompleta=rutaImagen + data.codigo + '/' + data.foto;
			}

			
			$('#tblDetalleMovimiento tbody').append('<tr>'+
			'<td>'+
				'<input type="hidden" name="Detallemovimiento[' + contador + '][pu]" value="' + data.precio + '">'+
				'<input type="hidden" name="Detallemovimiento[' + contador + '][stockactual]" value="' + data.stockactual + '">'+
				'<input type="hidden" name="Detallemovimiento[' + contador + '][idproducto]" value="' + data.idproducto + '" class="txtIdProducto">'+
				'<input type="hidden" name="Detallemovimiento[' + contador + '][stockdisponibledm]" value="' + data.stockdisponible + '">'+
				'<img src="' + rutaCompleta+ '" width="50" height="40">'+
				'</td>'+
			'<td class="codigo">'+data.codigo+'</td>'+
			'<td>'+data.nompro+'</td>'+
			'<td>'+data.marca+'</td>'+
			'<td><input type="text" name="Detallemovimiento[' + contador + '][observacion]" value="' + obs + '" class="txtCantidadDetalle required numeric" readonly></td>'+
			'<td><input type="text" name="Detallemovimiento[' + contador + '][cantidad]" value="' + cantidad + '" class="txtCantidadDetalle required numeric" readonly style="width:40px"></td>'+
			'<td><a href="#" class="btnEditarDetalleMovimiento"><img src="/imagenes/editar.gif"></a></td>'+
			'<td><a href="#" class="btnQuitarDetalleMovimientos"><img src="/imagenes/eliminar.gif"></a></td>'+
		'</tr>');
		},
		error:function(){
			console.log('error');
		}
	});
}

//Cargar el detalle del movimiento
function mostrarDetalleMovimiento(ruta){
	$.post(ruta, function(data){
		$('#tblDetallesMovimiento tbody').html(data).parent().show();
	});
}
//Evitar que se ingrese una cantidad de stock, superior al stock diponible.
//se cambio  por stock actual
function verificaStockDisponible(cantidadSalida, idProducto, element){
	if(cantidadSalida<=0){
		$.msgbox("Movimiento","La cantidad de stock de salida no puede ser menor o igual a cero");
		return false;
	}else{
		var tipoMovimiento = $('#lstTipoMovimiento option:selected').val();
		var ruta = "/producto/cantidadStockFisico/" + idProducto;
		var stockDisponible = 0;
		if(tipoMovimiento == 2){
			$.ajax({
				async: false,
				url: ruta,
				type: "POST",
				dataType: "json",
				success: function(data){stockDisponible = data.stockDisponible}
			});
			if(cantidadSalida > stockDisponible){
				$.msgbox("Movimiento","Cantidad de stock no disponible.<br><strong>Stock Actual: </strong>" + stockDisponible);
				execute();
				$('#msgbox-ok').click(function(){
					element.val('').focus();
				});
				return false;
			}else{
				return true;
			}
		}else{
			return true;
		}
	}
}
//Calcular precio final
function calculaPrecioFinal($row){
	if($row.find('.txtFob').val()>0 & $row.find('.txtCif').val()>0){
		stockActual = parseInt($row.find('.txtStockActual').val());
		precioLista = parseInt($row.find('.txtPrecioLista').val());
		cantidadRecibida = parseInt($row.find('.txtCantidadRecibida').val());
		fob = parseFloat($row.find('.txtFob').val());
		cif = parseFloat($row.find('.txtCif').val());
		precioFinal = Math.round(((stockActual * precioLista) + (cantidadRecibida * (fob + cif)))/(stockActual + cantidadRecibida) * 100)/100;
		$row.find('.txtPrecioFinal').attr('value',precioFinal);
	}
}