$(document).ready(function(){
	
	var lstZona=$('#lstZona').html();
	var lstRegionCobranza=$('#lstRegionCobranza').html();
	/**************** Botones **********************/
	$('#btnConsultar').click(function(e){
		e.preventDefault();
		//llenarEncabezado();
		//$('#cabecera').show();
		//$('#tblVentas').show();
                //$('#tblVentas').html('<th style="text-align: center;"><img style="width:250px;heigth:100" src="/imagenes/cargando.gif"></th>');
		cargaConsulta();
		console.log($('#Parametros').serialize());
	});
        
	$('#prueba').click(function(e){
		e.preventDefault();
		cargaConsultaPDF();
	});
	$('#btnRanking').click(function(e){
		$('#Parametros').attr('action','/pdf/rankingVendedor');
		
	});
	$('#btnPDF').click(function(e){
		$('#Parametros').attr('action','/pdf/listaReporteVentasXdiaPDF');
		
	});
});

//funciones fuera del evento ready
function cargaRegionCobranza(idpadre){
	$.ajax({
		url:'/zona/listaCategoriaxPadre',
		type:'post',
		async: false,
		dataType:'html',
		data:{'idpadrec':idpadre},
		success:function(resp){
			$('#lstRegionCobranza').html(resp);
		}
	});
}
function cargaZonas(idzona){
	$.ajax({
		url:'/zona/listaZonasxCategoria',
		type:'post',
		async: false,
		dataType:'html',
		data:{'idzona':idzona},
		success:function(resp){
			$('#lstZona').html(resp);
		}
	});
}
function cargaConsulta(){
    $('#fconsulta').html('');
    $('#btnConsultar').attr('disabled', 'disabled');
    $('#contenidoTabla').show();
    $('#tblVentas').html('<tr><td colspan="8"><center><img src="/imagenes/cargando.gif" width="150"></center></td></tr>');
    $.ajax({
        url:'/ventas/listaReporteVentasXdia',
        type:'post',
        dataType:'html',
        data:$('#Parametros').serialize(),
        success:function(resp){
            $('#btnConsultar').removeAttr('disabled');
            $('#fconsulta').html($('#opcmes option:selected').text() + ' - ' + $('#opcanio option:selected').text());
            $('#tblVentas').html(resp);
        }
    });
}

function limpiar(){
	$('#Parametros')[0].reset();
	$('.encabezado').html('');
	$('#idOrdenVenta').val('');
	$('#idCliente').val('');
	$('#idVendedor').val('');
	$('#cabecera').hide();
	$('#tblVentas').hide();
}/*
function llenarEncabezado(){
	$('#lblFechaGuiado').html($('#txtFechaGuiadoInicio').val()+' - '+$('#txtFechaGuiadoFin').val());
	$('#lblFechaAprobado').html($('#txtFechaAprobadoInicio').val()+' - '+$('#txtFechaAprobadoFinal').val());
	$('#lblFechaDespacho').html($('#txtFechaDespachoInicio').val()+' - '+$('#txtFechaDespachoFin').val());
	$('#lblFechaCancelado').html($('#txtFechaCanceladoInicio').val()+' - '+$('#txtFechaCanceladoFin').val());
	$('#lblOrdenVenta').html($('#txtOrdenVenta').val());
	$('#lblCliente').html($('#txtCliente').val());
	$('#lblVendedor').html($('#txtVendedor').val());
	$('#lblZonaGeografica').html($('#lstCategoriaPrincipal option:selected').html());
	$('#lblRegionCobranza').html($('#lstRegionCobranza option:selected').html());
	$('#lblZona').html($('#lstZona option:selected').html());
	$('#lblCondicion').html($('#lstCondicion').val());
}
*/