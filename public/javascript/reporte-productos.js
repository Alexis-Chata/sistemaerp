$(document).ready(function(){
	var listaSubLinea=$('#lstSubLinea').html();
	
	cargarLinea();
	cargarMarca();
	cargarAlmacen();

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
		encabezado()
		if ($('#agotados').attr('checked')=="checked") {
			
			consultaAgotados();
		}else if($('#vendidos').attr('checked')=="checked"){
			
			consultaVendidos();
		}
		
	});
	$('#btnImprimir').click(function(e){
		e.preventDefault();
		imprSelec('ContenedorImpresion');
	});

});

function cargarLinea(){
	$.ajax({
		url:'/linea/listaLinea',
		type:'post',
		dataType:'html',
		success:function(data){
			$('#lstLinea').html(data);
		}
	});
}
function cargarMarca(){
	$.ajax({
		url:'/marca/listaMarca',
		type:'post',
		dataType:'html',
		success:function(data){
			$('#lstMarca').html(data);
		}
	});
}
function cargarAlmacen(){
	$.ajax({
		url:'/almacen/listaAlmacen',
		type:'post',
		dataType:'html',
		success:function(data){
			$('#lstAlmacen').html(data);
		}
	});
}
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

function encabezado(){
	if ($('#lstLinea').val()!="") {
		$('#lblLinea').html($('#lstLinea option:selected').html());
	}else{
		$('#lblLinea').html('Todo');
	}
	
	if ($('#lstSubLinea').val()!="") {
		$('#lblSubLinea').html($('#lstSubLinea option:selected').html());
	}else{
		$('#lblSubLinea').html('Todo');
	}
	
	if ($('#lstMarca').val()!="") {
		$('#lblMarca').html($('#lstMarca option:selected').html());
	}else{
		$('#lblMarca').html('Todo');
	}

	if ($('#lstAlmacen').val()!="") {
		$('#lblAlmacen').html($('#lstAlmacen option:selected').html());
	}else{
		$('#lblAlmacen').html('Todo');
	}
	
	
	$('#lblProducto').html($('#txtProducto').val());
	if ($('#agotados').attr('checked')=="checked") {
		$('#lblTitulo').html('Reporte de Productos Agotados');
	}else{
		$('#lblTitulo').html('Reporte de Productos Vendidos');
	}

	$('#lblFecha').html($('#fechaInicio').val()+' - '+$('#fechaFinal').val());

	
}