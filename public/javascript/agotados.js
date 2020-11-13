$(document).ready(function(){
	$('#liFecha').hide();
	$('.liRangoFecha').hide();
	$('#liProducto').hide();
	$('#mostrarPDF').hide();

	msboxTitle = "Reporte de AGotados";



	$('#lstLinea').change(function(){
		
	});

	$('#btnConsultar').click(function(e){
		e.preventDefault();
		cargaTabla();

		$('#mostrarPDF').show();
		//$('#fecha').val(fecha);
		//$('#fechaInicio').val($('#fechaInicio').val());
		//$('#fechaFinal').val($('#fechaFinal').val());
		//$('#idProducto').val($('#txtIdProducto').val());
		
	});
	$('input[name="rbFiltro"]').change(function(){
		$('#txtFecha').val('');
		$('#txtFechaInicio').val('');
		$('#txtFechaFinal').val('');
		$('#txtCodigoProducto').val('');
		$('#txtIdProducto').val('');
		if(this.value == "1"){
			$('#liFecha').hide();
			$('.liRangoFecha').hide();	
			$('#liProducto').hide();		
		}else if(this.value == "2"){
			$('#liFecha').show();
			$('.liRangoFecha').hide();
			$('#liProducto').hide();
		}else if(this.value == "3"){
			$('#liFecha').hide();
			$('.liRangoFecha').show();
			$('#liProducto').hide();
		}else{
			$('#liFecha').hide();
			$('.liRangoFecha').hide();
			$('#liProducto').show();
			$('#txtCodigoProducto').focus();
		}
	});
});
msboxTitle = "Reporte de AGotados";

/*Carga tabla guia de pedidos*/
function cargaTabla(){
	$('#dataGridReport').data("kendoGrid").dataSource.data("");
	fecha = $('#txtFecha').val();
	fechaInicio = $('#txtFechaInicio').val();
	fechaFinal = $('#txtFechaFinal').val();
	idProducto = $('#txtIdProducto').val();
	$('#fecha').val(fecha);
	$('#fechaInicio').val(fechaInicio);
	$('#fechaFinal').val(fechaFinal);
	$('#idProducto').val(idProducto);

	filtro = $('input[name="rbFiltro"]:checked').val();
	mensaje = "";
	if(filtro == "2"){
		if(fecha == ""){
			mensaje = "Seleccione correctamente la fecha a buscar.";
		}
	}else if(filtro == "3"){
		if(fechaInicio == "" || fechaFinal ==""){
			mensaje = "Seleccione correctamente el rango de fecha a buscar.";
		}
	}else if(filtro == "4"){
		if(idProducto == ""){
			mensaje = "Ingrese correctamente el nombre del producto";
		}
	}else{
		mensaje = "";
	}
	if(mensaje!=""){
		$.msgbox(msboxTitle, mensaje);
		execute();
	}else{
		ruta = "/reporte/agotados/";
		$.post(ruta, {fecha: fecha, fechaInicio: fechaInicio, fechaFinal: fechaFinal, idProducto: idProducto}, function(datos){
			$('#dataGridReport').data("kendoGrid").dataSource.data(datos);
		});
	}
}