$(document).on('ready',function(){
        $('#txtBusqueda').autocomplete({
		source: "/ordenventa/autocompleteGuiasSinDocumento/",
		select: function(event, ui){
			$("#formul").submit();
		},
                error: function () {
                    console.log('ocurrio error');
                }
	});

	$("#seleccion").change(function(){
		var id=$("#seleccion option:selected").text();
		var url='/documento/guiassindocumento/'+id;
		window.location=url;
	});
        
        $('#btnCancelar').on('click',function(e){
		e.preventDefault();
		window.location='/documento/guiassindocumento/x';
	});
});