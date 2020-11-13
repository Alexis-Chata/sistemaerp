$(document).on('ready', function(){
	$('#btncancelar').on('click', function(e){
		e.preventDefault();
		window.location = '/sublinea/listar/';
	});

	//Boton de eliminacion
	$('.btnEliminar').on('click', function(e){
		/*e.preventDefault();
		//var url = $(this).attr('href');
		$.msgbox(msgboxTitleAlmacen, '¿Esta seguro de eliminar el registro?');
		$('#msgbox-ok').click(function(){
			window.location = url;
		});*/
	});

	/* Lista de busqueda*/
	$("#seleccion").change(function(){
		var id=$("#seleccion option:selected").text();
		var url='/sublinea/listar/'+id;
		window.location=url;
	});

	$('.permanente').click(function(){
		//$('textbusqueda').val

	});
});