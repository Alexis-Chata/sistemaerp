$(document).ready(function(){

	$('#imgProducto').hide();
	$('#verImagenes').hide();
	$('#btnActualizarImagen').hide();

	$('#txtproducto').autocomplete({
		source: "/producto/buscarAutocompleteLimpio/",
		select: function(event, ui){
			console.log(ui.item);
			$('#idProducto').val(ui.item.id);
			$('#codigoProducto').val(ui.item.value);
			$('#rutaImagenProducto').val("/imagenes/productos/" + ui.item.value +"/"+ui.item.imagen);
			$('#txtDescripcion').val(ui.item.tituloProducto);
			cargarAlmacen(ui.item.almacen);
			$('#labelUnidadMedida').html(ui.item.cod_sunat);
		}
	});

	$('#btnVerImagen').click(function(e){
		e.preventDefault();
		var imagen=$('#rutaImagenProducto').val();
		cargarImagenes(imagen);
	});

	$('#chkImagenProducto').click(function(e){
		//e.preventDefault();
		$('#imgProducto').show();
		$('#btnActualizarImagen').show();
	});

});

//Funcion que permite cargar la imagen de un producto.
function cargarImagenes(imagen){
	$('#verImagenes').show();
	$('.img').attr('src',imagen);
}

function cargarAlmacen(idalmacen){
	$.ajax({
		url:'/almacen/buscaAlmacen',
		type:'post',
		dataType:'json',
		data:{'idalmacen':idalmacen},
		success:function(resp){
			$('#labelRazonSocial').html(resp.razsocalm);
			$('#labelRuc').html(resp.rucalm);
		}
	});
}