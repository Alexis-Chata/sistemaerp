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

	$('#btnActualizar').button().click(function(e){
		e.preventDefault();
		//console.log($('#frmProducto').serialize());
		actualizarProducto()
	});
	$('.precioreferencia').bind('click focus',function(){
		$('#respuesta').html('');
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
			$('#precioreferencia01').val(resp.precioreferencia01);
			$('#precioreferencia02').val(resp.precioreferencia02);
			$('#precioreferencia03').val(resp.precioreferencia03);
		}
	});
}
function actualizarProducto(){
	$.ajax({
		url:'/producto/actualizaProductoJson',
		type:'post',
		dataType:'json',
		data:$('#frmProducto').serialize(),
		success:function(resp){
			console.log(resp);
			if (resp.respuesta==true) {
				$('#respuesta').html('El dato se grabo Correctamente').css('color','green');
			}else{
				$('#respuesta').html('Hubo un problema al momento de grabar').css('color','red');
			}
		}
	});
}