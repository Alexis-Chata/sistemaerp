$(document).ready(function(){
	$('#liFecha').hide();
	$('.liRangoFecha').hide();
	$('#liProveedor').hide();
	msboxTitle = "Reporte de Ordenes de compra";
	$('#btnConsultar').click(function(e){
		e.preventDefault();
		cargaTabla();
	});
	$('input[name="rbFiltro"]').change(function(){
		$('#txtFecha').val('');
		$('#txtFechaInicio').val('');
		$('#txtFechaFinal').val('');
		$('#txtBusqueda').val('');
		$('#txtIdProveedor').val('');
		if(this.value == "1"){
			$('#liProveedor').hide();		
			$('#liFecha').hide();
			$('.liRangoFecha').hide();	
		}else if(this.value == "2"){
			$('#liProveedor').show();
			$('#liFecha').hide();
			$('.liRangoFecha').hide();
			$('#txtCodigoProducto').focus();
		}else if(this.value == "3"){
			$('#liProveedor').hide();
			$('#liFecha').show();
			$('.liRangoFecha').hide();
		}else{
			$('#liProveedor').hide();
			$('#liFecha').hide();
			$('.liRangoFecha').show();
		}
	});
});
msboxTitle = "Reporte de AGotados";

/*Carga tabla guia de pedidos*/
function cargaTabla(){
	var idProveedor = $('#txtIdProveedor').val();
	var fecha = $('#txtFecha').val();
	var fechaInicio = $('#txtFechaInicio').val();
	var fechaFinal = $('#txtFechaFinal').val();
	filtro = $('input[name="rbFiltro"]:checked').val();
	mensaje = "";
	if(filtro == "2"){
		if(idProveedor == ""){
			mensaje = "Ingrese correctamente el nombre del proveedor";
		}
	}else if(filtro == "3"){
		if(fecha == ""){
			mensaje = "Seleccione correctamente la fecha a buscar.";
		}
	}else if(filtro == "4"){
		if(fechaInicio == "" || fechaFinal ==""){
			mensaje = "Seleccione correctamente el rango de fecha a buscar.";
		}
	}else{
		mensaje = "";
	}
	if(mensaje!=""){
		$.msgbox(msboxTitle, mensaje);
	}else{
		ruta = "/reporte/ordencompra/";
		$.post(ruta, {idProveedor: idProveedor, fecha: fecha, fechaInicio: fechaInicio, fechaFinal: fechaFinal}, function(data){
			$("#dataGridReport").data("kendoGrid").dataSource.data(data);
		});
	}
}