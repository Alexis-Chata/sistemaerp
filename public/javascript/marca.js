$(document).on('ready', function(){

	$('#btncancelar').on('click', function(e){
		e.preventDefault();
		window.location = '/marca/lista/';
	});

	
	/* Lista de busqueda*/
	$("#seleccion").change(function(){
		var id=$("#seleccion option:selected").text();
		var url='/marca/lista/'+id;
		window.location=url;
	});

});