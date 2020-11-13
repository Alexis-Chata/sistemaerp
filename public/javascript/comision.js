$(document).ready(function(){
	var idvendedor;
	var nombreVendedor;

	$('#txtVendedor').autocomplete({
		source: "/vendedor/autocompletevendedor/",
		select: function(event, ui){
			
			idvendedor=ui.item.id;
			$('#idVendedor').val(idvendedor);
			nombreVendedor=ui.item.label;
			
		}
	});

	$('.btnGrabar').live('click',function(e){
		e.preventDefault();
		padre=$(this).parents('tr');
		idordenventa=padre.find('.idordenventa').val();
		porcentaje=padre.find('.txtcomision').val();
		porcentajeComision(idordenventa,porcentaje);
	});

	$('#btnAceptar').click(function(e){
		e.preventDefault();
		if ($('#txtVendedor').val()!="" && $('#fechaFinal').val()!="" && $('#fechaInicio').val()!="") {
			$('#nombreVendedor').html('Comisiones Pendientes de : '+nombreVendedor);
			fechaInicio=$('#fechaInicial').val();
			fechaFinal=$('#fechaFinal').val();
			cargarOrdenesVenta(idvendedor,fechaInicio,fechaFinal);
			$('#respuesta').html("");
		}else{
			alert("Seleccione un Vendedor o las  Fechas");
		}
		
	});

	$('#comisionar').click(function(e){
		e.preventDefault();
		fechacomision=$('#fechacomision').val();
		if (confirm("¿Esta Seguro de Pagar la Comision?")) {
			if ($('#fechacomision').val()!="") {
				
				$('.idordenventa').each(function(e){
					padre=$(this).parents('tr');
					idOV=padre.find('.idordenventa').val();
					comisionar(idOV,fechacomision);
				});
			}else{
				alert("Ingrese la fecha de comision!");
			}
		}
	});

	$('#btnImprimir').click(function(e){
		$('.btnGrabar').hide();
		$('.txtcomision').css('border','none');
		imprSelec('contenedor');
		$('.btnGrabar').show();
		$('.txtcomision').css('border','1px solid #ccc');
	});
});

function cargarOrdenesVenta(idvendedor,fechaInicio,fechaFinal){
	console.log(fechaInicio);
	console.log(fechaFinal);
	idOrdenVenta=$('#txtIdOrden').val();
	$.ajax({
		url:'/vendedor/listaComisiones',
		type:'post',
		dataType:'html',
		data:{'idvendedor':idvendedor,'fechaInicial':fechaInicio,'fechaFinal':fechaFinal},
		success:function(resp){
			//console.log(resp);
			$('#tblVentas tbody').html(resp);
		}
	});


}
function porcentajeComision(idordenventa,porcentaje){
	console.log(fechaInicio);
	console.log(fechaFinal);
	idOrdenVenta=$('#txtIdOrden').val();
	$.ajax({
		url:'/ordenventa/porcentajeComision',
		type:'post',
		dataType:'html',
		data:{'idordenventa':idordenventa,'porcentaje':porcentaje},
		success:function(resp){
			console.log(resp);

			fechaInicio=$('#fechaInicial').val();
			fechaFinal=$('#fechaFinal').val();
			idvendedor=$('#idVendedor').val();

			cargarOrdenesVenta(idvendedor,fechaInicio,fechaFinal);
			alert('Se grabo Correctamente !');

		}
	});
}
function comisionar(idordenventa,fechacomision){
	
	
	$.ajax({
		url:'/ordenventa/Comisionar',
		type:'post',
		dataType:'html',
		data:{'idordenventa':idordenventa,'fechacomision':fechacomision},
		success:function(resp){
			console.log(resp);
			$('#respuesta').html("Se Pago todas las comisones !").css('color','blue');
			$('.txtcomision').attr('disabled','disabled');
			$('.btnGrabar').attr('disabled','disabled');

		}
	});
}
