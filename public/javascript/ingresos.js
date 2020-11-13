$(document).ready(function(){
	var idcliente;
	var padregeneral;
	var padresecundario;
	var idordenventageneral;
	var lblordengeneral;
	var saldosecundario;
	var idingresossecundario;
	
	$('#txtCliente').autocomplete({
		source: "/cliente/autocomplete2/",
		select: function(event, ui){
			$('#txtIdCliente').val(ui.item.id);
			$('#razonsocial').val(ui.item.label);
			$('#ruc').val(ui.item.rucdni);
			$('#codigo').val(ui.item.codigocliente);
			$('#codantiguo').val(ui.item.codigoantiguo);
			$('#buscarOrdenes').show();
			$('#nuevabusqueda').show();
			$('#contenedorDetalle').hide();
			idcliente=ui.item.id;
			cargaOrdenVenta(idcliente);
			
	}});
	

	$('.cabecera').live('click',function(e){
		e.preventDefault();
		idordenventa=$(this).attr('id');
		
		if (padregeneral!=undefined) {
			padregeneral.removeClass();
		}
		
		padregeneral=$(this).parents('tr');

		padregeneral.addClass('active-row');
		lblordengeneral=padregeneral.find('.lblorden').html();
		//console.log(lblordengeneral);
		idordenventageneral=padregeneral.find('.idordenventa').val();
		cargaIngresos(idordenventa);
		
	});
	$('.lista').live('click',function(e){
		e.preventDefault();
		padresecundario=$(this).parents('tr');
		padresecundario.addClass('active-row');
		saldosecundario=parseFloat(padresecundario.find('.saldo').val());
		idingresossecundario=parseFloat(padresecundario.find('.idingresos').val());
		$('#montodisponible').html('Monto disponible es: '+saldosecundario);
		//console.log(lblordengeneral);
		$('#titulo').html('Orden Actual :'+lblordengeneral);
		listaOrdenesNoRepitidas(idcliente,idordenventageneral);
		$('#contenedorOrdenes').dialog('open');
	});
	$('.remover').live('click',function(e){
		e.preventDefault();
		padresecundario=$(this).parents('tr');
		padresecundario.addClass('active-row');
		saldosecundario=parseFloat(padresecundario.find('.saldo').val());
		idingresossecundario=parseFloat(padresecundario.find('.idingresos').val());
		$('#montoDisponibleRemover').html('Monto disponible es: '+saldosecundario);
		//console.log(lblordengeneral);
		$('#tituloRemover').html('Orden Actual :'+lblordengeneral);
		
		$('#contenedorRemover').dialog('open');
	});

	$('#contenedorOrdenes').attr('title',lblordengeneral).dialog({
		title:'Liberacion de Saldo',
		autoOpen:false,
		modal:true,
		width:350,
		buttons:{
			aceptar:function(){
				var monto=parseFloat($('#monto').val());
				if (monto>0 && monto<=saldosecundario) {
					if ($('#ordenes').val()!="" && $('#monto').val()!='' ) {
						
						cambiaIngresos($('#nroRecibo').val(),idordenventageneral,idcliente,$('#ordenes').val(),monto,idingresossecundario,$('#observacionesLiberar').val(),$('#observacionesLiberarNuevo').val());
					}else{
						$('#resp').html('No ha ingresado  todos los datos correctamente').css('color','red');
					}
				}else{
					$('#resp').html('Ingrese correctemente El Monto').css('color','red');
				}
			}
		},close:function(){
			$('#titulo').html('');
			$('#resp').html('');
			$('#nroRecibo').val('');
			$('#monto').val('');
			$('#observacionesLiberar').val('');
			padresecundario.removeClass();
		}

	});
	$('#contenedorRemover').attr('title',lblordengeneral).dialog({
		title:'Remover o Restar Ingreso',
		autoOpen:false,
		modal:true,
		width:350,
		buttons:{
			aceptar:function(){
				var monto=parseFloat($('#montoRemover').val());
				var observaciones=$('#observacionesRemover').val();
				if (monto>0 && monto<=saldosecundario) {
					if ($('#observacionesRemover').val()!=""  && $('#montoRemover').val()!='' ) {
						
						removerIngresos(idordenventageneral,idcliente,monto,idingresossecundario,observaciones);
					}else{
						$('#respRemover').html('No ha ingresado  todos los datos correctamente').css('color','red');
					}
				}else{
					$('#respRemover').html('Ingrese correctemente El Monto').css('color','red');
				}
			}
		},close:function(){
			$('#tituloRemover').html('');
			$('#respRemover').html('');
			$('#observacionesRemover').val('');
			$('#montoRemover').val('');
			padresecundario.removeClass();
		}

	});

	
});

function cargaOrdenVenta(idcliente){
	
	$.ajax({
		url:'/ordenventa/OrdenesxIdCliente',
		type:'post',
		datatype:'html',
		data:{'idcliente':idcliente},
		success:function(resp){
			//console.log(resp)

			$('#contenedor').html(resp);
			
		},error:function(erro){
			console.log('error');
		}
	})
}

function cargaIngresos(idordenventa){
	$.ajax({
		url:'/ingresos/ingresosxOrdenventaLista',
		type:'post',
		datatype:'html',
		data:{'idordenventa':idordenventa},
		success:function(resp){
			//console.log(resp)
			$('#contenedorDetalle').html(resp).show();

			
		},error:function(erro){
			console.log('error');
		}
	})
}
function listaOrdenesNoRepitidas(idcliente,idordenventa){
	$.ajax({
		url:'/ordenventa/listaOrdenesNoRepitidas',
		type:'post',
		async: false,
		datatype:'html',
		data:{'idordenventa':idordenventa,'idcliente':idcliente},
		success:function(resp){
			//console.log(resp)
			$('#ordenes').html(resp);

			
		},error:function(erro){
			console.log('error');
		}
	})
}

function verificarrecibo(nrorecibo){
	var valorVerificacion;
	$.ajax({
		url:'/ingresos/verificarrecibo',
		type:'post',
		dataType:'json',
		async: false,
		data:{'nrorecibo':nrorecibo},
		success:function(resp){
		
			valorVerificacion=resp.verificacion;
		}
	});
	return valorVerificacion;
}
function cambiaIngresos(nroRecibo,idordenventa,idcliente,nuevaidordenventa,monto,idingresossecundario,observaciones,observacionesingresonuevo){
	console.log('verificado');
	$.ajax({
		url:'/ingresos/cambiaIngresos',
		type:'post',
		dataType:'html',
		data:{'nroRecibo':nroRecibo,'idingresos':idingresossecundario,'idcliente':idcliente,'nuevaidordenventa':nuevaidordenventa,'monto':monto,'observaciones':observaciones,'observacionesingresonuevo':observacionesingresonuevo},
		success:function(resp){
		 console.log(resp);
		 	$('#contenedorOrdenes').dialog('close');
		 	cargaOrdenVenta(idcliente);
			cargaIngresos(idordenventa);
		}
	});
	
}
function removerIngresos(idordenventa,idcliente,monto,idingresossecundario,observaciones){
	console.log('verificado');
	$.ajax({
		url:'/ingresos/removerIngresos',
		type:'post',
		dataType:'html',
		data:{'idingresos':idingresossecundario,'monto':monto,'observaciones':observaciones},
		success:function(resp){
		 console.log(resp);
		 	$('#contenedorRemover').dialog('close');
		 	cargaOrdenVenta(idcliente);
			cargaIngresos(idordenventa);
		}
	});
	
}