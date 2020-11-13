$(document).on('ready', function(){
	validacion=false;
	validacion2=false;
	$('#btnCancelar').on('click', function(e){
		e.preventDefault();
		window.location = '/zona/listacategoria/';
	});
	$('#seleccion').on('change',function(e){
		var id=$("#seleccion option:selected").text();
		var url='/zona/listacategoria/'+id;
		window.location=url;
	});
	$('#eliminacategoria').on('click',function(e){
		e.preventDefault();


		codigo=$('#valorId').attr('value');
		$.ajax({
			url: '/zona/verificarExistenciaHijos/',
			type: 'POST',
			dataType:'json',
			data:{'codigo':codigo},
			success: function(respuesta){
				console.log(respuesta)
				$('#error').text(respuesta.error);
				$('#error').css({ 'color':'red'});
				if (respuesta.validado) {
					window.location='/zona/eliminacategoria/'+codigo;
				};
				

			},
			error: function(jqXHR, status, error) {
			//console.log(status)
			//console.log(error)
			}
		});
		
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
			var ruta='/zona/editarcategoriaprincipal/'+id;
			window.location=ruta;
		};
		
	});

	$('#codigoCategoriaPrincipal').on('keyup',function(e){
	e.preventDefault();
	codigoc=$('#codigoCategoriaPrincipal').attr('value');
	$.ajax({
		url: '/zona/validarCodigoCategoria/',
		type: 'POST',
		dataType:'json',
		data:{'codigoc':codigoc},
		success: function(respuesta){
			//console.log(respuesta)
			$('#error').text(respuesta.error);
			validacion=respuesta.validado;
			
			if (validacion==true && codigoc!="") {
				$('#error').css({'color':'green'});
				
				
			}else{
				$('#error').css({'color':'red'})
			};

		},
		error: function(jqXHR, status, error) {
		//console.log(status)
		//console.log(error)
		}
		});
	});

	$('#codigoCategoria').on('keyup',function(e){
	e.preventDefault();
	codigoc=$('#codigoCategoria').attr('value');
	$.ajax({
		url: '/zona/validarCodigoCategoria/',
		type: 'POST',
		dataType:'json',
		data:{'codigoc':codigoc},
		success: function(respuesta){
			//console.log(respuesta)
			$('#error').text(respuesta.error);
			validacion2=respuesta.validado;
			
			if (validacion2==true && codigoc!="") {
				$('#error').css({'color':'green'});
				
				
			}else{
				$('#error').css({'color':'red'})
			};

		},
		error: function(jqXHR, status, error) {
		//console.log(status)
		//console.log(error)
		}
		});
	});
	$('#btnnuevaCategoriaPrincipal').on('click',function(e){
		
		if (validacion==false) {
			e.preventDefault();
			
		};
	});
	$('#btnnuevaCategoria').on('click',function(e){
		
		if (validacion2==false) {
			e.preventDefault();
			
		};
	});
});