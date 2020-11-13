$(document).ready(function(){
	var padreGeneral=0;
	var idIngresoGeneral=0;
	
	$('.btnEliminar').click(function(e){
		if (confirm('¿Esta Seguro de Eliminar el cheque?')) {

		}else{
			e.preventDefault();
		}
	});
	$('.btnGrabar').click(function(e){
		e.preventDefault();
		padreGeneral=$(this).parents('tr');
		idIngresoGeneral=padreGeneral.find('.idingresos').val();

		
		$('#contenedorCheques').dialog('open');
		
	});
	$('#btnImprimir').click(function(e){
		e.preventDefault();
		$('.btnGrabar').hide();
		$('.btnEliminar').hide();
		imprSelec('contenedorImpresion');
		$('.btnGrabar').show();
		$('.btnEliminar').show();
	});
	$('#contenedorCheques').dialog({
		autoOpen:false,
		resizable:false,
		modal:true,
		width:550,
		buttons:{
			Aceptar:function(){
				if (confirm('¿Esta Seguro de Validar el cheque?')) {
					nroCuenta=$('#numeroCuenta').val();
					fechaIngreso=$('#fechaIngreso').val();
					nrooperacion=$('#nrooperacion').val();
					if (nroCuenta!='' && fechaIngreso!='' &&  nrooperacion!='') {
						grabarCheque(nroCuenta,fechaIngreso,idIngresoGeneral,nrooperacion);
						padreGeneral.css('display','none');
					}else{
						$('#respuesta').html('Ingrese Correctamente los valores!').css('color','red');
					}
				}
			}
		},
		close:function(){
			$('#numeroCuenta').html('');
			$('#fechaIngreso').val('');
			$('#respuesta').html('');
			$('#nrooperacion').val('');
			//location.reload();
		}

	});
	$('#banco').change(function(){
		idbanco=$(this).val();
		buscaCtaxBanco(idbanco);;
	});


});

function grabarCheque(nroCuenta,fcobro,idingresos,nrooperacion){
	$.ajax({
		url:'/ingresos/aceptarCheque',
		type:'post',
		datatype:'html',
		data:{'nroCuenta':nroCuenta,'fcobro':fcobro,'idingresos':idingresos,'nrooperacion':nrooperacion},
		success:function(resp){
			console.log(resp);
			$('#contenedorCheques').dialog('close');
		}
	});
}
function buscaCtaxBanco(idbanco){
	$.ajax({
		url:'/cta_banco/buscaCtaxBanco',
		type:'post',
		datatype:'html',
		async: false,
		data:{'idbanco':idbanco},
		success:function(resp){
			console.log(resp);
			$('#numeroCuenta').html(resp);
			
		}
	});
}