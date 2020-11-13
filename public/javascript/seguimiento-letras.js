$(document).ready(function(){
	$('#txtNombreCliente').hide();
	$('#tabs').tabs();
	$('#chkFiltro').change(function(){
		if($(this).is(':checked')){
			$('#txtIdGuia').show();
			$('#txtNombreCliente').hide();
			$('#lblFiltro').html('Por Guia');
		}else{
			$('#txtIdGuia').hide();
			$('#txtNombreCliente').show();
			$('#lblFiltro').html('Por Cliente');
		}
	});
	$('#ulTabs li a').click(function(){
		var $elemento = $($(this).attr("href"));
		$("#fsBusquedaLetras").appendTo($elemento);
	});
});