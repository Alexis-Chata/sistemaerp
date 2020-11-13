$(document).on('ready', function(){

	/* Lista de busqueda*/
	$("#seleccion").change(function(){
		var id=$("#seleccion option:selected").text();
		var url='/facturacion/listaDuracion/'+id;
		window.location=url;
	});
	
	$(".detalleOV").hide();

	$(".observar").on('click',function(e){
		e.preventDefault();
		id=$(this).attr('id');
		//alert('Numero de Orden de Venta: '+id);
		ruta="/ordenventa/detalle/"+id;
		$.ajax({
			url:'/ordenventa/detalle/',
			type:'GET',
			data:{'IDOV':id},
			success:function(respuesta){
				//console.log(respuesta);
				$(".detalleOV").hide( 'Blind');
				$("#tablita tbody").html(respuesta);
				
				$(".detalleOV").show( 'Blind');
				
				
			},
			error:function(error){
				//console.log(error);
			}
		});
	});

	

	$("#cierraTabla").click(function(e){		
		$(".detalleOV").hide( 'Blind');
		
	});

});
