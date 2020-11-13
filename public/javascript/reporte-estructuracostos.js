$(document).ready(function(){
	var listaSubLinea=$('#lstSubLinea').html();
        
        $('#btnConsultarExcel').click(function(e){
		//alert(44);
		$('#frmConsulta').attr('action','/excel/reporteEstructuradeCostos');	
	});

	$('#txtProducto').autocomplete({
		source: "/producto/buscarAutocompleteLimpio/",
		select: function(event, ui){
			$('#idProducto').val(ui.item.id);
			
		}
	});

	$('#agotados').click(function(e){
		if ($(this).attr('checked')=="checked") {
			
			
			$('#tblConsulta').html('');
			$('#tblConsulta').hide();
			$('#tblEncabezado').hide();
		}
	});
	$('#vendidos').click(function(e){
		if ($(this).attr('checked')=="checked") {
			
			
			$('#tblConsulta').html('');
			$('#tblConsulta').hide();
			$('#tblEncabezado').hide();
		}
	});

	$('#lstLinea').change(function(){
		idLinea=$(this).val();
		if (idLinea!="") {
			cargarSubLinea(idLinea)
		}else{
			$('#lstSubLinea').html(listaSubLinea);
		}
		
	});
	$('#btnLimpiar').click(function(e){
		e.preventDefault();
		$('#frmConsulta')[0].reset();
		$('#idProducto').val('');
		$('#tblConsulta').html('');
		$('#tblConsulta').hide();
		$('#tblEncabezado').hide();
		$('#agotados').click();
	});

	$('#btnConsultar').click(function(e){
		e.preventDefault();
		$('#tblConsulta').show();
		$('#tblEncabezado').show();
		encabezado();
                fecha = $('#anho').val() + "-"+ $('#mes').val();
                consultacostos(fecha);
	});
        
	$('#btnImprimir').click(function(e){
		e.preventDefault();
		imprSelec('ContenedorImpresion');
	});

});

function cargarSubLinea(idLinea){
	$.ajax({
		url:'/linea/listaSubLinea',
		type:'post',
		dataType:'html',
		data:{'idLinea':idLinea},
		success:function(data){
			
			$('#lstSubLinea').html(data);
		}
	});
}
function consultaAgotados(){
	$.ajax({
		url:'/producto/productosAgotados',
		type:'post',
		dataType:'html',
		data:$('#frmConsulta').serialize(),
		success:function(data){
			
			$('#tblConsulta').html(data);
		}
	});
}
function consultaVendidos(){
	$.ajax({
		url:'/producto/productosVendidos',
		type:'post',
		dataType:'html',
		data:$('#frmConsulta').serialize(),
		success:function(data){
			
			$('#tblConsulta').html(data);
		}
	});
}

function consultacostos(fecha){
    $.ajax({
	url:'/reporte/consultacostos',
	type:'post',
	dataType:'html',
	data:$('#frmConsulta').serialize(),
	success:function(data){
            $('#tblConsulta').html(data);
	}
    });
}

function encabezado(){
        $('#lblmes').html($('#mes option:selected').html());
        $('#lblanho').html($('#anho option:selected').html());
        $('#lblTitulo').html('Reporte Estructura de Costos');

        $('#lblFecha').html($('#fechaInicio').val()+' - '+$('#fechaFinal').val());
}