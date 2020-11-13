$(document).ready(function(){
	$('#btnBuscar').click(function(e){
		e.preventDefault();
		
		idInventario=$('#lstInventario').val();
		idProducto=$('#idProducto').val();
		editarInventario(idInventario,idProducto);

	});

	$('#txtInventarioProducto').autocomplete({
		source: "/producto/buscarAutocompleteLimpio/",
		select: function(event, ui){
			$('#idProducto').val(ui.item.id);
			$('#txtDescripcion').val(ui.item.tituloProducto);
		}
	});
	$('.btnGrabar').live('click',function(e){
		e.preventDefault();
		if (confirm('¿Desea realmente Grabar?')) {
			padre=$(this).parents('tr');
			grabarInventario(padre);
		}
	});

	$('.malos,.showroom,.servicio').live('blur',function(){
		padre=$(this).parents('tr');
		malos=parseInt(padre.find('.malos').val());
		servicio=parseInt(padre.find('.servicio').val());
		showroom=parseInt(padre.find('.showroom').val());
		total=malos+servicio+showroom;
		padre.find('.total').val(total);
	});

});

function editarInventario(idInventario,idProducto){
	$.ajax({
		url:'/detalleinventario/cargaDetalleInvetario',
		type:'post',
		dataType:'html',
		async:false,
		data:{'idProducto':idProducto,'idInventario':idInventario},
		success:function(resp){
			//console.log(resp);
			$('#tblDetalle').html('');
			$('#tblDetalle').html(resp);
			$('#idProducto').val(0);
			$('#txtInventarioProducto').val('');
			$('#txtDescripcion').val('');
		}
	});
}

function grabarInventario(padre){
	var idDetalleInventario=padre.find('.id').val();
	var showroom=padre.find('.showroom').val();
	var malos=padre.find('.malos').val();
	var servicio=padre.find('.servicio').val();
	var idBloque=padre.find('.lstBloque').val();
	$.ajax({
		url:'/detalleinventario/actualizaDetalle',
		type:'post',
		dataType:'json',
		async:false,
		data:{'idDetalleInventario':idDetalleInventario,'showroom':showroom,'malos':malos,'servicio':servicio,'idBloque':idBloque},
		success:function(resp){
			console.log(resp);
			if (resp.exito==true) {
				alert('Se Grabo Correctamente');
			}else{
				alert('Hubo un problema y no se pudo grabar');
			}
		}
	});
}