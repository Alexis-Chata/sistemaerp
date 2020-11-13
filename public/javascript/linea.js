$(document).on('ready', function(){
	$('#btncancelar').on('click', function(e){
		e.preventDefault();
		window.location = '/linea/lista/';
	});
	/*Boton de eliminacion*/
	$('.btnEliminar').on('click', function(e){

		//e.preventDefault();
		//var url = $(this).attr('href');
		//alert("url");

	/*$.msgBox({
    title: "Are You Sure",
    content: "Would you like a cup of coffee?",
    type: "confirm",
    buttons: [{ value: "Yes" }, { value: "No" }, { value: "Cancel"}],
    success: function (result) {
        if (result == "Yes") {
            alert("One cup of coffee coming right up!");
        }
    }
});*/



	});
	/* Lista de busqueda*/
	$("#seleccion").change(function(){
		var id=$("#seleccion option:selected").text();
		var url='/linea/lista/'+id;
		window.location=url;
	});

	

	$(".eliminaLinea img").on('click',function(e){
		//e.preventDefault();
		//url=$(".eliminaLinea").attr('href');
		//alert(url);
		
	});
});