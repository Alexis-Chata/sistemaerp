$(document).ready(function(){
	$('#txtproducto').autocomplete({
		source: "/producto/buscarAutocompleteLimpio/",
		select: function(event, ui){
			$('#idProducto').val(ui.item.id);
			$('#codigoProducto').html(ui.item.value);
			$('#rutaImagenProducto').attr('src',("/imagenes/productos/" + ui.item.value +"/"+ui.item.imagen));
			$('#txtDescripcion').html(ui.item.tituloProducto);
			buscaProducto(ui.item.id);
		}
	});
	$('#imprimir').click(function(e){
		e.preventDefault();
		imprSelec('contenedorImpresion');
	});
});

function buscaProducto(idProducto){
	$.ajax({
		url:'/producto/buscarxIdProducto',
		type:'post',
		dataType:'json',
		data:{'idvalor':idProducto},
		success:function(resp){
			//console.log(resp);
			$('#marca').html(resp.marca);
			$('#preciolista').html('S/. '+resp.preciolista);
			
			$('#stockActual').html(resp.stockactual);
			$('#stockDisponible').html(resp.stockdisponible);
			
		}
	});
}