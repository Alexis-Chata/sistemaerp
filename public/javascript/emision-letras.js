$(document).ready(function(){
	$('#txtOrdenVenta').autocomplete({
		source: "/ordenventa/autocompleteparaletras/",
		select: function(event, ui){
			$('#txtIdOrden').val(ui.item.id);
			$('#txtCondicionLetra').val(ui.item.condicionletra);
			$('#txtTipoLetra').val(ui.item.tipoletra);
			$('#txtCantidadLetras').val(ui.item.cantidadletras);
			/*buscaOrdenVenta();
			cargaLetras();*/
		}
	});
	$('#lstOrdenVenta').change(function(){
		cargaOrdenVenta();
	});
	$('#lstCondicionLetra').change(function(){
		$('#txtCantidadLetras').val($(this).val());
		cargaLetras();
	});
});

function cargaOrdenPago(){
	var ordenVenta = $('#lstOrdenPago option:selected').val();
	var url = "/ordenventa/buscar/" + ordenVenta;
	if(ordenVenta){
		$.post(url, function(data){
			$('#tblOrdenVenta tbody').html(data);
		});
	}
}

function cargaLetras(){
	var condicionLetras = $('#lstCondicionLetra option:selected').val();
	var ordenVenta = $('#lstOrdenPago option:selected').val();
	var url = "/facturacion/generaletras/";
	if(condicionLetras != ""){
		$.post(url,{ordenVenta:ordenVenta,condicionLetras:condicionLetras }, function(data){
			$('#tblLetras tbody').html(data);
		});
	}
}