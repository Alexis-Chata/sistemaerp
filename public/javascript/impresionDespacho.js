$(document).ready(function(){

	$('#txtOrdenVenta2').autocomplete({
		source: "/ordenventa/OrdeventaDespachados/",
		select: function(event, ui){
			$('#txtIdOrden').val(ui.item.id);
			idordenventa=ui.item.id;
			buscarDespacho(idordenventa);

		}
	});
	$('#imprimir').click(function(e){
		e.preventDefault(e);
		imprSelec('muestra');
	});
	$('#btnGrabar').click(function(e){
		e.preventDefault();
		
		if ($('#fechaDespacho').val()!="" && $('#idordenventa').val()!="" && $('#idordenventa').val()!=undefined) {

			grabarfechaDespacho();
		}
		
		
	});
});

function buscarDespacho(idordenventa){
	var ruta = "/almacen/despacho/" + idordenventa;
	$.post(ruta, function(data){
		$('#despacho').html(data);	

	});

}
function grabarfechaDespacho(){
	var idordenventa=$('#idordenventa').val();
	$.ajax({
		url:'/ordenventa/grabaFechaDespacho',
		type:'post',
		dataType:'html',
		data:{'fechaDespacho':$('#fechaDespacho').val(),'idordenventa':idordenventa},
		success:function(resp){
			console.log(resp);
			alert('Se grabo Correctamente');
			buscarDespacho(idordenventa);
			$('#fechaDespacho').val('');
		}
	});
}
