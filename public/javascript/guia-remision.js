$(document).ready(function(){
	
	$('#txtOrdenVenta').autocomplete({
		source: "/ordenventa/autocompleteguiaremision/",
		select: function(event, ui){
			$('#txtIdOrden').val(ui.item.id);
			buscaOrdenVenta();
			cargaDetalleOrdenVenta();
			
			//$('#txtRucDni').focus();
		}
	});
	$('#lstDespacho').change(function(){
		
		if ($(this).val()!="") {
			$('#txtDireccionLlegada').val($('#lstDespacho option:selected').text()).css('width','350px');
		}else{
			$('#txtDireccionLlegada').val($('#direccionInicial').val());
		}
		
	});
	$('#grabar').click(function(e){
		if ($('#txtDireccionLlegada').val()!="") {

		}else{
			e.preventDefault();
			alert('Ingrese la Direccion de Despacho');
		}
	})
});

function buscaOrdenVenta(){
	var ordenVenta = $('#txtIdOrden').val();
	var ruta = "/ordenventa/buscarguia/" + ordenVenta;
	$.getJSON(ruta, function(data){
		console.log(data);
		$('#txtCliente').val(data.cliente);
		$('#txtDireccionPartida').val(data.tdireccion);
		$('#txtDireccionLlegada').val(data.direccionllegada);
		$('#txtFechaGuia').val(data.fechaguia);
		$('#txtRucDni').val(data.rucdni);
		$('#txtLugar').val(data.lugar);
		$('#txtTransporte').val(data.trazonsocial);
		$('#txtRucTransporte').val(data.truc);
		$('#txtDireccionTranspote').val(data.tdireccion);
		$('#txtCliente').attr('size',$('#txtCliente').val().length + 2);
		$('.inline-block input').exactWidth();
		$('#idcliente').val(data.idcliente);
		$('#direccionInicial').val(data.direccionllegada);
		cargaDireccionDespacho();
	});
}

function cargaDetalleOrdenVenta(){
	var ordenVenta = $('#txtIdOrden').val();
	var ruta = "/facturacion/listaProductosGuiaRemision/" + ordenVenta;
	$.post(ruta, function(data){
		$('#tblProductosGuia tbody').html(data);
		
	});	
}
function cargaDireccionDespacho(){
	var idcliente=$('#idcliente').val();
	$.ajax({
		url:'/cliente/direccion_despacho',
		type:'post',
		dataType:'html',
		async: false,
		data:{'idcliente':idcliente},
		success:function(resp){
			//console.log(resp);
			$('#lstDespacho').html(resp);
		},
		error:function(error){
			//console.log('error');
		}
	});

}
