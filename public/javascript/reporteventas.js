$(document).ready(function(){
	
	var lstZona=$('#lstZona').html();
	var lstRegionCobranza=$('#lstRegionCobranza').html();
	/**************** Botones **********************/
	$('#btnConsultar').click(function(e){
		e.preventDefault();
		llenarEncabezado();
		$('#cabecera').show();
		$('#tblVentas').show();
                $('#tblVentas').html('<th style="text-align: center;"><img style="width:250px;heigth:100" src="/imagenes/cargando.gif"></th>');
		cargaConsulta();
		console.log($('#Parametros').serialize());
	});
	$('#btnLimpiar').click(function(e){
		e.preventDefault();
		limpiar();
	});
	$('#btnImprimir').click(function(e){
		e.preventDefault();
		imprSelec('imprimir');
		
	});
	$('#prueba').click(function(e){
		e.preventDefault();
		cargaConsultaPDF();
	});
	$('#btnRanking').click(function(e){
		$('#Parametros').attr('action','/pdf/rankingVendedor');
		
	});
        $('#btnEXCEL').click(function () {
            $('#Parametros').attr('action','/excel/reporteventas');
        });
        
	$('#btnPDF').click(function(e){
		$('#Parametros').attr('action','/pdf/reporteventas');	
	});
	/**************  Listas ***********/
	$('#lstCategoriaPrincipal').change(function(){
		idpadre=$(this).val();
		if (idpadre=="") {
			$('#lstRegionCobranza').html(lstRegionCobranza);
			$('#lstRegionCobranza').change();
		}else{
			cargaRegionCobranza(idpadre);
		}

		$('#lstRegionCobranza').change();
	});

	$('#lstRegionCobranza').change(function(){
		idzona=$(this).val();
		console.log(idzona);
		if (idzona=="") {
			$('#lstZona').html(lstZona);
		}else{
			cargaZonas(idzona);
		}
	});

	$('#lstZona').change(function(){

	});

	/******************** Autocomplete ****************************/
	$('#txtOrdenVenta').autocomplete({
		source: "/ordenventa/buscarautocompletecompleto/",
		select: function(event, ui){
			$('#idOrdenVenta').val(ui.item.id);
		}
	});

	$('#txtCliente').autocomplete({
		source: "/cliente/autocomplete2/",
		select: function(event, ui){
			$('#idCliente').val(ui.item.id);
			
	}});

	$('#txtVendedor').autocomplete({
		source: "/vendedor/autocompletevendedor/",
		select: function(event, ui){
			$('#idVendedor').val(ui.item.id);
		}
	});
	/******************** Checkbox************************/
	$('#aprobados').click(function(){

		if ($(this).attr('checked')=="checked") 
		{
			$('#lstCondicion').removeAttr('disabled');
		}else{
			$('#lstCondicion').attr('disabled','disabled');
		}
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
	$.ajax({
		url:'/ventas/listaReporteVentas',
		type:'post',
		dataType:'html',
		data:$('#Parametros').serialize(),
		success:function(resp){
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
}
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
        $('#condVenta').html($('#condVenta option:selected').html());
        $('#lblfiltrocliente').html($('#filtrocliente option:selected').html());
}
