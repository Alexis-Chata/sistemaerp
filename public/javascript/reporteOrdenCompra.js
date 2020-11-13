$(document).ready(function(){
	$('#txtProducto').autocomplete({
		source: "/producto/buscarAutocompleteLimpio/",
		select: function(event, ui){
			$('#idProducto').val(ui.item.id);
			$('#txtDescripcion').val(ui.item.tituloProducto);
		}
	});
	$('#txtOrdenCompra').autocomplete({
		source: "/ordencompra/autoCompleteAprobados/",
		select: function(event, ui){
			$('#idOrdenCompra').val(ui.item.id);
		}
	});
	$('#btnHistorial').click(function(e){
		if($('#idProducto').val()!=""){
			$('#frmReporte').attr('action','/pdf/historialOrdenCompra');
		}else{
			e.preventDefault();
			alert('Ingrese un Producto');
		}
		
	});
	$('#btnReporte').click(function(e){
		if($('#idOrdenCompra').val()!="" || $('#idProducto').val()!=''){
			$('#frmReporte').attr('action','/pdf/reporteOrdenCompra');
		}else{
			e.preventDefault();
			alert('Ingrese un Producto u OrdenCompra');
		}
		
	});
    $('#btnReporteExcel').click(function(e){
		if($('#idOrdenCompra').val()!="" || $('#idProducto').val()!=''){
			$('#frmReporte').attr('action','/excel/reporteOrdenCompra');
		}else{
			e.preventDefault();
			alert('Ingrese un Producto u OrdenCompra');
		}
	});
    
	$('#btnLimpiar').click(function(e){
		e.preventDefault();
		$('#frmReporte')[0].reset();
		$('#idOrdenCompra').val('');
		$('#idProducto').val('');
	});
});
