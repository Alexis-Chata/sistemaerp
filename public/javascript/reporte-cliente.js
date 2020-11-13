$(document).ready(function(){

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
			
	}});
	$('#btnConsultar').click(function(e){
		if ($('#txtIdCliente').val()!="" && $('#txtCliente').val()!="") {
		e.preventDefault();
		idcliente=$('#txtIdCliente').val();
		tipoCobro=$('#lstTipoCobro').val();
		situacion=$('#lstSituacion').val();
		fechaInicio=$('#fechaInicio').val();
		fechaFinal=$('#fechaFinal').val();

		cargarDetalles(idcliente,tipoCobro,situacion,fechaInicio,fechaFinal);
		}
	});

	$('#btnImprimir').click(function(e){
		e.preventDefault();
		imprSelec('contenedor');
	});



});
function cargarDetalles(idcliente,tipoCobro,situacion,fechaInicio,fechaFinal){
	$.ajax({
		url:'/reporte/letraxCliente',
		type:'post',
		datatype:'html',
		data:{'idcliente':idcliente,'tipoCobro':tipoCobro,'situacion':situacion,'fechaInicio':fechaInicio,'fechaFinal':fechaFinal},
		success:function(resp){
			
			$('#tblcontenedor').html(resp);
		}

	});
}