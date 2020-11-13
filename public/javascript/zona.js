$(document).on('ready', function(){
	validacion=false;
	$('#btnCancelar').on('click', function(e){
		e.preventDefault();
		window.location = '/zona/lista/';
	});
	$('#seleccion').on('change',function(e){
		var id=$("#seleccion option:selected").text();
		var url='/zona/lista/'+id;
		window.location=url;
	});
	$('#eliminacategoria').on('click',function(e){
		e.preventDefault();
		var id=$('#valorId').attr('value');

		if (confirm("Esta seguro que desea Eliminar la Categoria")) {
		
		window.location='/zona/eliminacategoria/'+id;
		}
		else {
		
		};
		
	});

	$('#lstCategorias').on('change',function(e){
		var id=$("#lstCategorias option:selected").val();
		//alert(id);
		$('#txtBusqueda').attr('value',id);
	});

	$('#editarCategoria').on('click',function(e){
		e.preventDefault();

		var id=id=$("#lstCategorias option:selected").val();
		if (id=="") {
			
		}else{
			var ruta='/zona/editarcategoria/'+id;
			window.location=ruta;
		};
		
	});

	$('#codigoZona').on('keyup',function(e){
	e.preventDefault();
	codigo=$('#codigoZona').attr('value');
	$.ajax({
		url: '/zona/validarCodigoZona/',
		type: 'POST',
		dataType:'json',
		data:{'codigo':codigo},
		success: function(respuesta){
			//console.log(respuesta)
			$('#error').text(respuesta.error);
			validacion=respuesta.validado;
			
			if (validacion==true && codigo!="") {
				$('#error').css({'color':'green','float':'none'});
				
				
			}else{
				$('#error').css({'color':'red'})
			};

		},
		error: function(jqXHR, status, error) {
		//console.log(status)
		//console.log(error)
		}
	});

	$('#btnNuevaZona').on('click',function(e){
		
		if (validacion==false) {
			e.preventDefault();
			
		};
	});

	});
	
});