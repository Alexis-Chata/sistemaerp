$(document).ready(function(){
	iddetalleordencobrogeneral=0;
	importedocgeneral=0;
	saldodocgeneral=0;
	montoingresado=$('#montoingresado');
	montoasignado=$('#montoasignado');
	saldo=$('#saldo');
	txtIdOrden=$('#txtIdOrden');
	contenedorpago=$('#contenedorpago');
	pagoamortiguar=$('#pagoamortiguar');
	var claseGlobal='';
	var padregeneral='';
	var valorLetra=0;
	var padre=0;
	contador=0;
	
	$('#txtOrdenVenta2').autocomplete({
		source: "/ordenventa/PendientesxPagar2/",
		select: function(event, ui){
			$('#txtIdOrden').val(ui.item.id);
			buscaOrdenCobro();
			cargaDetalleOrdenCobro2();
			cargaSumaIngresos();
		}
	});
        
        $('.anularLetra').live('click', function(e){
            if (confirm('¿Esta seguro que desea anular la letra?')) {
                var idLetra = $(this).data('id');
                var idOrden = $('#txtIdOrden').val();
                $.ajax({
                        url:'/ingresos/anularLetra',
                        data: {"idLetra": idLetra, "idOrden": idOrden},
                        type:'post',
                        dataType: "json",
                        success:function(resp){
                            if (resp['rspta'] == 1) {
                                $('.rowLe'+idLetra).remove();
                            } else {
                                alert('Letra No disponible para esta funcion.');
                            }
                        }
                });
            }
        });

	$('.cancelar').live('click',function(e){
		padre=$(this).parents('tr');
		iddetalleordencobrogeneral=padre.find('.iddetalleordencobro').val();
		importedocgeneral=padre.find('.importedoc').val();
		saldodocgeneral=padre.find('.saldodoc').val();
		montoingresado=$('#montoingresado');
		montoasignado=$('#montoasignado');
		saldo=$('#saldo');
		e.preventDefault();
		padre.addClass('active-row');
		if (verificar()) {
			$('#contenedorCancelado').dialog('open');
			
		}else{
			alert('No hay Ingresos suficientes para Pagar el monto completo');
			padre.removeClass();
		}
		
	});
	$('.pagarparte').live('click',function(e){
		padre=$(this).parents('tr');
		iddetalleordencobrogeneral=padre.find('.iddetalleordencobro').val();
		importedocgeneral=padre.find('.importedoc').val();
		saldodocgeneral=padre.find('.saldodoc').val();
		montoingresado=$('#montoingresado');
		montoasignado=$('#montoasignado');
		saldo=$('#saldo');
		e.preventDefault();
		padre.addClass('active-row');
		contenedorpago.dialog('open');
		//console.log(saldo.val());
		console.log(saldodocgeneral);
		
	});
	contenedorpago.dialog({
		autoOpen:false,
		modal:true,
		buttons:{
			Pagar:function(){
				saldorestante=saldo.val()-pagoamortiguar.val();
				montovalor=pagoamortiguar.val();
				console.log(pagoamortiguar.val());
				//console.log(saldorestante);
				valorrestante=pagoamortiguar.val()-saldodocgeneral;
				console.log(valorrestante);
				if ($('#fechapago').val()!="") {
					if (valorrestante>0) {
						alert('Ha sobrepasado el Monto a pagar');
						pagoamortiguar.val(saldodocgeneral)
					}else if(pagoamortiguar.val()<=0){
						alert('Ingrese una Cantidad Correcta');
					}else if (saldorestante>=0) {
						if (confirm('¿Realmente desea Amortizar?')) {
							$.ajax({
							url:'/ingresos/cancelar',
							type:'post',
							datatype:'html',
							data:{'iddetalleordencobro':iddetalleordencobrogeneral,'idordenventa':txtIdOrden.val(),'saldogeneral':pagoamortiguar.val()},
							success:function(resp){
								//console.log(resp);
								pagoamortiguar.val('');
								contenedorpago.dialog('close');
								verificarCobro();
								actualizaPago(montovalor);
								cargaSumaIngresos();
								cargaDetalleOrdenCobro2();
								
							}

							});
						}
					}else{
						alert('No hay Ingresos suficientes para Descontar');
					}
				}else{
					alert('Ingrese la fecha de pago');
				}
			}
		},
		close:function(){
			pagoamortiguar.val('');
			$('#fechaPago').val('');
			padre.removeClass();
		}
	});
	$('#contenedorDeshacerPago').dialog({
		modal:true,
		width:500,
		autoOpen:false,
		buttons:{
			Aceptar:function(){
				
				var motivo=$('#motivo').val();
				
				if (motivo!="") {
					monoadescontar=valorLetra-saldodocgeneral;
					deshacerPago(iddetalleordencobrogeneral,monoadescontar,motivo);
					console.log(iddetalleordencobrogeneral);
					console.log('deshacerPago');
					
					
				}else{
					$('#respDeshacerPago').html('Ingrese su Motivo por el cual va Deshacer el pago !').css('color','red');
				}

			}
		},
		close:function(){
			padregeneral.removeClass();
			$('#motivo').val('');
			$('#respDeshacerPago').html('');
			padregeneral.removeClass();
		}
	});
	$('#contenedorCancelado').dialog({
		modal:true,
		width:400,
		autoOpen:false,
		buttons:{
			"Pagar":function(){
				if (confirm('¿Realmente desean Pagar el monto completo?')) {
					$.ajax({
						url:'/ingresos/cancelar',
						type:'post',
						datatype:'html',
						data:{'iddetalleordencobro':iddetalleordencobrogeneral,'idordenventa':txtIdOrden.val(),'saldogeneral':saldodocgeneral,'fechapago':$('#fechaPagoContado').val()},
						success:function(resp){
							console.log(resp);
							verificarCobro();
							actualizaPago(saldodocgeneral);
							$('#contenedorCancelado').dialog('close');
							cargaSumaIngresos();
							cargaDetalleOrdenCobro2();
						}
					});
				}
			}
		},
		close:function(){
			$('#fechaPagoContado').val('');
			padre.removeClass();
		}
	});
	$('#contenedorModificar').dialog({
		modal:true,
		width:450,
		autoOpen:false,
		close:function(){
			limpiarModificar();
		}
	});
	$('#contendorAutorizacion').dialog({
		autoOpen:false,
		modal:true,
		width:350,
		buttons:{
			"Aceptar":function(){
				usuario=$('#usuario').val();
				contrasena=$('#contrasena').val();
				if (usuario!="" && contrasena!="") {
					validado=validarAutorizacion(usuario,contrasena);
					if (validado==true) {
						//$('#respVerificacion').html('Usuario Correcto').css('color','green');
							alert('Usuario Correcto');
							if (claseGlobal=='anular') {
								claseGlobal=='';
								anular(iddetalleordencobrogeneral);
								console.log(iddetalleordencobrogeneral);
								console.log('anulado');
								$('#contendorAutorizacion').dialog('close');
							}else if(claseGlobal=='deshacerPago'){
								claseGlobal=='';
								contador++;
								$('#contenedorDeshacerPago').dialog('open');
								$('#contendorAutorizacion').dialog('close');

							}else if(claseGlobal=='modificar'){
								claseGlobal=='';
								$('#contenedorModificar').dialog('open');
								console.log(iddetalleordencobrogeneral);
								console.log('modificar');
								$('#contendorAutorizacion').dialog('close');
							}
						$('#respVerificacion').html('');
					}else{
						$('#respVerificacion').html('El Usuario o la Contraseña es Incorrecto').css('color','red');
					}
				}else{
					$('#respVerificacion').html('Ingrese Usuario y Contraseña').css('color','red');
				}
			}

		},
		close:function(){
			$('#usuario').val('');
			$('#contrasena').val('');
			$('#respVerificacion').html('');
			
			if (contador==0) {
				padregeneral.removeClass();
				contador=0;
			}
		}
	})
	$('#prueba').click(function(e){
		e.preventDefault();
		console.log($('#formularioModificar').serialize());
		if (validaTotalModificar(valorLetra)==true) {
			 if (validarVaciosModificar()==true) {
			 	modificar();
			 }else{
			 	alert('Ingrese las Cantidad con sus respectivos dias ');
			 }
		}else{
			alert('La Cantidad asignada no es igual al Total de la Letra');
		}
	
	});	
	$('.deshacerPago').live('click',function(e){
		e.preventDefault();
		claseGlobal=$(this).attr('class');
		padregeneral=$(this).parents('tr');
		padregeneral.addClass('active-row');
		iddetalleordencobrogeneral=padregeneral.find('.iddetalleordencobro').val();
		valorLetra=parseFloat(padregeneral.find('.valorLetra').val());
		saldodocgeneral=parseFloat(padregeneral.find('.saldodoc').val());
		console.log(iddetalleordencobrogeneral);
		console.log(claseGlobal);
		$('#contendorAutorizacion').dialog('open');
	});
	$('.anular').live('click',function(e){
		padregeneral=$(this).parents('tr');
		iddetalleordencobrogeneral=padregeneral.find('.iddetalleordencobro').val();
		
		claseGlobal=$(this).attr('class');
		console.log(claseGlobal);

		if (confirm('¿Esta seguro de anular?')) {
			$('#contendorAutorizacion').dialog('open');
		};
	});
	$('.modificar').live('click',function(e){
		e.preventDefault();
		
		claseGlobal=$(this).attr('class');
		padregeneral=$(this).parents('tr');
		iddetalleordencobrogeneral=padregeneral.find('.iddetalleordencobro').val();
		valorLetra=parseFloat(padregeneral.find('.valorLetra').val());

		fechavencimientogeneral=padregeneral.find('.fechavencimiento').html();
		console.log(fechavencimientogeneral);
		$('#nuevaFecha').val(fechavencimientogeneral);
		$('#valorModificar').html('S/.'+valorLetra.toFixed(2));
		$('#idModificar').val(iddetalleordencobrogeneral);
		$('#contendorAutorizacion').dialog('open');
	});
	$('#cantidadLetras').change(function(){
		cant=$(this).val();
		if (cant==1) {
			$('#montoLetra').removeAttr('disabled');
			$('#diasMontoLetra').removeAttr('disabled');

			$('#nuevaLetra').attr('disabled','disabled').val('');
			$('#montoLetraNueva').attr('disabled','disabled').val('');
		}else if(cant==2){
			$('#nuevaLetra').removeAttr('disabled');
			$('#montoLetraNueva').removeAttr('disabled');

			$('#montoLetra').attr('disabled','disabled').val('');
			$('#diasMontoLetra').attr('disabled','disabled').val('');
		}else{
			$('#nuevaLetra').attr('disabled','disabled').val('');
			$('#montoLetraNueva').attr('disabled','disabled').val('');

			$('#montoLetra').attr('disabled','disabled').val('');
			$('#diasMontoLetra').attr('disabled','disabled').val('');
		}
	});
	$('#imprimir').click(function(e){
		e.preventDefault();
		$('.cancelar').hide();
		$('.pagarparte').hide();
		$('.deshacerPago').hide();
		$('#cliente').hide();
		
		imprimircliente();
		$('#imprimirCliente').show();
		$('table tr td, table tr th').css('font-family','courier');
		imprSelec('muestra');
		$('table tr td, table tr th').css('font-family','calibri');
		$('#cliente').show();
		$('#imprimirCliente').hide();
		$('.deshacerPago').show();
	});
	
	function verificar(){
		saldorestante=saldo.val()-saldodocgeneral;
		//saldorestante=saldo.val()-saldodocgeneral);
		if (saldorestante>-0.1) {
			return true;
		}else{
			return false;
		}
	}
});
	function buscaOrdenCobro(){
		var ordenVenta = $('#txtIdOrden').val();
		var ruta = "/ordencobro/buscarxOrdenVenta/" + ordenVenta;
		$.getJSON(ruta, function(data){
			$('#razonsocial').val(data.razonsocial);
			$('#idcliente').val(data.idcliente);
			$('#ruc').val(data.ruc);
			$('#codigo').val(data.codcliente);
			$('#codantiguo').val(data.codantiguo);
			$('.inline-block input').exactWidth();

		});
	}

	function cargaDetalleOrdenCobro2(){
		var ordenVenta = $('#txtIdOrden').val();
		var ruta = "/ordencobro/buscarDetalleOrdenCobroPagos/" + ordenVenta;
		$.post(ruta, function(data){
			$('#tblDetalleOrdenCobro tbody').html(data);
			//verificarDiferenciaTotales();
		});
	}
	function cargaSumaIngresos(){
		var ordenVenta = $('#txtIdOrden').val();
		var ruta = "/ingresos/IngresosxOrdenventa/" + ordenVenta;
		$.post(ruta, function(data){
			$('#tblIngreso').html(data);	
		});
	}
	
	function imprimircliente(){

		$('#imprimirCliente').html(
				'<fieldset>'+
					'<legend>Datos del Cliente</legend>'+
					'<h2>Asignacion de Pagos N°: '+$('#txtOrdenVenta2').val()+'</h2>'+
						'Código:<input type="text" value="'+$('#codigo').val()+'"  >'+
						'Razon Social:<input type="text"    size="40" value="'+$('#razonsocial').val()+'">'+ 
						'Número de RUC:<input type="text"  value="'+$('#ruc').val()+'">'+
						'Codigo Dakkar:<input type="text" value="'+$('#codantiguo').val()+'">'+
					''+
				'</fieldset>'
			);
	}
	function verificarCobro(){
		idOrdenVenta=$('#txtIdOrden').val();
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

	function actualizaPago(valorMonto){
		idOrdenVenta=$('#txtIdOrden').val();
		console.log('idordenventa '+idOrdenVenta);
		console.log('valorMonto '+valorMonto+'h');
		$.ajax({
			url:'/ordenventa/actualizaPago',
			type:'post',
			dataType:'html',
			data:{'idordenventa':idOrdenVenta,'valorMonto':valorMonto},
			success:function(resp){
			
				console.log(resp);
			}
		});
	}

	function modificar(){
	
		$.ajax({
			url:'/ingresos/modificar',
			type:'post',
			dataType:'html',
			data:$('#formularioModificar').serialize(),
			success:function(resp){
				console.log(resp);
				cargaDetalleOrdenCobro2();
				verificarCobro();

				$('#contenedorModificar').dialog('close');
			},
			error:function(error){
				console.log('error');
			}
		});
	
	}

function validaTotalModificar(valorLetra){
	valorLe=valorLetra.toFixed(2);
	valorRetorno=true;
	valorMontoLetras=0;
	

	if ($('#montoContado').val()!="") {
		valorMontoContado=parseFloat($('#montoContado').val());
	}else{
		valorMontoContado=0;
	}

	if ($('#montoCredito0').val()!="") {
		valorMontoCredito0=parseFloat($('#montoCredito0').val());
	}else{
		valorMontoCredito0=0;
	}
	if ($('#montoCredito1').val()!="") {
		valorMontoCredito1=parseFloat($('#montoCredito1').val());
	}else{
		valorMontoCredito1=0;
	}
	if ($('#montoCredito2').val()!="") {
		valorMontoCredito2=parseFloat($('#montoCredito2').val());
	}else{
		valorMontoCredito2=0;
	}
	if ($('#montoCredito3').val()!="") {
		valorMontoCredito3=parseFloat($('#montoCredito3').val());
	}else{
		valorMontoCredito3=0;
	}
	if ($('#montoCredito4').val()!="") {
		valorMontoCredito4=parseFloat($('#montoCredito4').val());
	}else{
		valorMontoCredito4=0;
	}
	
	if ($('#cantidadLetras').val()==1) {
		if ($('#montoLetra').val()!="") {
			valorMontoLetras=parseFloat($('#montoLetra').val());
		}else{
			valorMontoLetras=0;
		}
		

	}else if($('#cantidadLetras').val()==2){
		if ($('#montoLetraNueva').val()!="") {
			valorMontoLetras=parseFloat($('#montoLetraNueva').val());
		}else{
			valorMontoLetras=0;
		}
	}


	valorTotal=valorMontoContado+valorMontoCredito0+valorMontoCredito1+valorMontoCredito2+valorMontoCredito3+valorMontoCredito4+valorMontoLetras;
	console.log(valorTotal);
	if (valorTotal!=valorLe) {
		valorRetorno=false;
	}

	


	return valorRetorno;
}

function limpiarModificar(){
	$('#flete').val('');
	$('#diasFlete').val('');
	$('#envioSobre').val('');
	$('#diasEnvioSobre').val('');
	$('#gastoBancario').val('');
	$('#diasGastoBancario').val('');
	$('#costoMantenimiento').val('');
	$('#diasCostoMantenimiento').val('');
	$('#montoContado').val('');
	$('#montoCredito0').val('');
	$('#diasCredito0').val('');
	$('#montoCredito1').val('');
	$('#diasCredito1').val('');
	$('#montoCredito2').val('');
	$('#diasCredito2').val('');
	$('#montoCredito3').val('');
	$('#diasCredito3').val('');
	$('#montoCredito4').val('');
	$('#diasCredito4').val('');
	$('#montoLetra').val('');
	$('#diasMontoLetra').val('');
	$('#cantidadLetras').val('');
	$('#nuevaLetra').val('');
	$('#valorModificar').html('');
	$('#nuevaFecha').val('');
	$('#idModificar').val('');
	$('#montoLetraNueva').val('');
}
function validarVaciosModificar(){
	var retorno=true;
	if ($('#flete').val()!="" && $('#flete').val()!=0) {
		if ($('#diasFlete').val()!="" && $('#diasFlete').val()!=0) {
			
		}else{
			retorno=false;
		}
	}
	if ($('#envioSobre').val()!="" && $('#envioSobre').val()!=0) {
		if ($('#diasEnvioSobre').val()!="" && $('#diasEnvioSobre').val()!=0) {
			
		}else{
			retorno=false;
		}
	}
	if ($('#gastoBancario').val()!="" && $('#gastoBancario').val()!=0) {
		if ($('#diasGastoBancario').val()!="" && $('#diasGastoBancario').val()!=0) {
			
		}else{
			retorno=false;
		}
	}
	if ($('#costoMantenimiento').val()!="" && $('#costoMantenimiento').val()!=0) {
		if ($('#diasCostoMantenimiento').val()!="" && $('#diasCostoMantenimiento').val()!=0) {
			
		}else{
			retorno=false;
		}
	}
	if ($('#montoCredito0').val()!="" && $('#montoCredito0').val()!=0) {
		if ($('#diasCredito0').val()!="" && $('#diasCredito0').val()!=0) {
			
		}else{
			retorno=false;
		}
	}
	if ($('#montoCredito1').val()!="" && $('#montoCredito1').val()!=0) {
		if ($('#diasCredito1').val()!="" && $('#diasCredito1').val()!=0) {
			
		}else{
			retorno=false;
		}
	}
	if ($('#montoCredito2').val()!="" && $('#montoCredito2').val()!=0) {
		if ($('#diasCredito2').val()!="" && $('#diasCredito2').val()!=0) {
			
		}else{
			retorno=false;
		}
	}
	if ($('#montoCredito3').val()!="" && $('#montoCredito3').val()!=0) {
		if ($('#diasCredito3').val()!="" && $('#diasCredito3').val()!=0) {
			
		}else{
			retorno=false;
		}
	}
	if ($('#montoCredito4').val()!="" && $('#montoCredito4').val()!=0) {
		if ($('#diasCredito4').val()!="" && $('#diasCredito4').val()!=0) {
			
		}else{
			retorno=false;
		}
	}
	if ($('#montoLetra').val()!="" && $('#montoLetra').val()!=0) {
		if ($('#diasMontoLetra').val()!="" && $('#diasMontoLetra').val()!=0) {
			
		}else{
			retorno=false;
		}
	}
	if ($('#nuevaLetra').val()!="") {
		if ($('#montoLetraNueva').val()!="" && $('#montoLetraNueva').val()!=0) {
			
		}else{
			retorno=false;
		}
	}

	return retorno;
}
function validarAutorizacion(usuario,contrasena){
	var retorno=true;
	$.ajax({
		url:'/actor/validaAutorizacion',
		type:'post',
		async: false,
		dataType:'json',
		data:{'usuario':usuario,'contrasena':contrasena},
		success:function(resp){
			console.log(resp);
			retorno=resp.verificacion;
		},
		error:function(error){
			console.log('error');
		}
	});
	return retorno;
}
function deshacerPago(iddetalleordencobro,valorLetra,motivo){

	$.ajax({
		url:'/ingresos/deshacerPagoAsignacion',
		type:'post',
		dataType:'html',
		data:{'iddetalleordencobro':iddetalleordencobro,'motivo':motivo},
		success:function(resp){
			console.log(resp);
			$('#contenedorDeshacerPago').dialog('close');
			descuentaPago(valorLetra);
			cargaDetalleOrdenCobro2();
			cargaSumaIngresos()
			verificarCobro();
		},
		error:function(error){

		}
	});
	
}
function descuentaPago(valorMonto){
	idOrdenVenta=$('#txtIdOrden').val();
	console.log(idOrdenVenta);
	console.log(valorMonto);
	$.ajax({
		url:'/ordenventa/descuentaPago',
		type:'post',
		dataType:'html',
		data:{'idordenventa':idOrdenVenta,'valorMonto':valorMonto},
		success:function(resp){
		
			console.log(resp);
		}
	});
}
function anular(iddetalleordencobro){
	$.ajax({
		url:'/ingresos/anular',
		type:'post',
		dataType:'html',
		data:{'iddetalleordencobro':iddetalleordencobro},
		success:function(resp){
			console.log(resp);
			cargaDetalleOrdenCobro2();
			verificarCobro();
		},
		error:function(error){

		}
	});
}
function verificarDiferenciaTotales(){
	var montoProgramacion=parseFloat($('#montoProgramacion').val());
	var montoReal=parseFloat($('#montoReal').val());
	var errorAjuste=parseFloat($('#errorAjuste').val());
	var respuesta=true;
	var msg="";
	if(montoReal-montoProgramacion>errorAjuste){
		respuesta=false;
		msg="Hay un error de:"+(montoReal-montoProgramacion)+" En contra de la empresa por favor corrija este problema sino algunas botones de van a bloquear";
		$('.cancelar').hide();
		$('.pagarparte').hide();
	}else if (montoProgramacion-montoReal>errorAjuste){
		respuesta=false;
		msg="Hay un error de:"+(montoProgramacion-montoReal)+" En contra del cliente  por favor corrija este problema sino algunas botones de van a bloquear";
		$('.cancelar').hide();
		$('.pagarparte').hide();
	}else{
		respuesta=true;
		
	}
	if(respuesta==false){
		alert(msg);
	}
	
}