$(document).ready(function(){
	$('#txtProducto').autocomplete({
		source: "/producto/buscarAutocompleteLimpio/",
		select: function(event, ui){
			$('#idProducto').val(ui.item.id);
			$('#txtDescripcion').val(ui.item.tituloProducto);
			
		}
	});

	$('#btnLimpiar').click(function(e){
		e.preventDefault();
		$('#idProducto').val('');
		$('#txtDescripcion').val('');
		$('#txtProducto').val('');

	});

});