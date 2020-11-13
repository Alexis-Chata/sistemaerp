$(document).ready(function(){
	var idvendedor;


	$('#txtVendedor').autocomplete({
		source: "/vendedor/autocompletevendedor/",
		select: function(event, ui){
			
			idvendedor=ui.item.id;
			$('#idVendedor').val(idvendedor);
			cargarFechaComision(idvendedor);
			
		}
	});

	$('#btnAceptar').click(function(e){
		e.preventDefault();
		if ($('#txtVendedor').val()!="" && $('#fechacomision').val()!=0) {
			fechacomision=$('#fechacomision').val();
			console.log(fechacomision);
			$('#nombreVendedor').html($('#txtVendedor').val());
			$('#fecha').html($('#fechacomision option:selected').html());
			cargarPagosComisiones(idvendedor,fechacomision);
		}else{
			alert("Ingrese un vendedor y Seleccione una fecha de Comision !");
		}
		
		
	});

	$('#btnImprimir').click(function(e){
		e.preventDefault();
		$('tr').css('border','1px solid');
		imprSelec('contenedor');
	});
});

function cargarPagosComisiones(idvendedor,fechacomision){
	
	
	$.ajax({
		url:'/vendedor/listaComisionesPagadas',
		type:'post',
		dataType:'html',
		data:{'idvendedor':idvendedor,'fechacomision':fechacomision},
		success:function(resp){
			//console.log(resp);
			$('#tblVentas tbody').html(resp);
		}
	});
}

function cargarFechaComision(idvendedor){

	$.ajax({
		url:'/ordenventa/listaFechaComision',
		type:'post',
		dataType:'html',
		data:{'idvendedor':idvendedor},
		success:function(resp){
			//console.log(resp);
			$('#fechacomision').html(resp);
		}
	});
}

