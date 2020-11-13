/*******variables globales en la pagina*******/

$(document).ready(function(e){
    /*********Variables locales*****************/
    var lstCategoriaPrincipal=$('#lstCategoriaPrincipal').html();
    var lstZonaCobranza=$('#lstZonaCobranza').html();
    var lstZona=$('#lstZona').html();
    
    
    /********** Autocompletes ************/
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
    /************* Botones ****************/
    $('#btnLimpiar').click(function(e){
	e.preventDefault();
	limpiar();
    });
    $('#btnConsultarPDF').click(function(e){
	encabezado();
	$('#frmConsulta').attr('action','/pdf/reporteCobranzaxEmpresa')
	
		
    });
    $('#btnConsultarHTML').click(function(e){
	e.preventDefault();
	
		
    });
    $('#btnConsultarExcel').click(function(e){
	e.preventDefault();
	
		
    });
    $('#btnImprimir').click(function(e){
	e.preventDefault();
	imprSelec('contenedor');
		
    });
    /************** listas ***************/
    $('#lstZonaCobranza').change(function(){
	idzona=$(this).val();
		
	if (idzona=="") {
	    $('#lstZona').html(lstZona);
	}else{
	    cargaZonas(idzona);
	}
	$('#lstZona').change();
    });
    $('#lstCategoriaPrincipal').change(function(){
	idpadre=$(this).val();
	if (idpadre=="") {
	    $('#lstZonaCobranza').html(lstZonaCobranza);
	    $('#lstZonaCobranza').change();
	}else{
	    cargaRegionCobranza(idpadre);
	}

	$('#lstZonaCobranza').change();
		
    });
    
    
    
});
function cargaRegionCobranza(idpadre){
	$.ajax({
		url:'/zona/listaCategoriaxPadre',
		type:'post',
		async: false,
		dataType:'html',
		data:{'idpadrec':idpadre},
		success:function(resp){
			$('#lstZonaCobranza').html(resp);
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

function limpiar(){
	$('#frmConsulta')[0].reset();
	$('.encabezado').html('');
	//$('#lstCategoriaPrincipal').val('');
	$('.customSelectInner').html('');
	$('#lstCategoriaPrincipal').change();
	$('#idOrdenVenta').val('');
	$('#idCliente').val('');
	$('#idVendedor').val('');
	$('#tblEncabezado').hide();
	$('#tblCuerpo').hide();
	$('#lblCategoriaPrincipal').val('');
	$('#lblZonaCobranza').val('')
	$('#lblZona').val('');
	$('#lblTipoCobranza').val('');
	$('#lblSituacion').val('');
	$('#lblVendedor').val('');
	$('#lblCliente').val('');
	$('#lblOrdenVenta').val('');
	$('#lblFecha').val('');
}
function encabezado(){
	
	if ($('#lstCategoriaPrincipal').val()!="") {
		$('#lblCategoriaPrincipal').val($('#lstCategoriaPrincipal option:selected').html());
	}else{
		$('#lblCategoriaPrincipal').val('Todos');
	}
	if ($('#lstZonaCobranza').val()!="") {
		$('#lblZonaCobranza').val($('#lstZonaCobranza option:selected').html());
	}else{
		$('#lblZonaCobranza').val('Todos');
	}
	if ($('#lstZona').val()!="") {
		$('#lblZona').val($('#lstZona option:selected').html());
	}else{
		$('#lblZona').val('Todos');
	}
	if ($('#lstTipoCobranza').val()!="") {
		$('#lblTipoCobranza').val($('#lstTipoCobranza option:selected').html());
	}else{
		$('#lblTipoCobranza').val('Todos');
	}
	if ($('#lstSituacion').val()!="") {
		$('#lblSituacion').val($('#lstSituacion option:selected').html());
	}else{
		$('#lblSituacion').val('');
	}
	if ($('#txtVendedor').val()!="") {
	    $('#lblVendedor').val($('#txtVendedor').val());
	}else{
	    $('#lblVendedor').val('Todos');
	}
	
	if ($('#txtCliente').val()!="") {
	    $('#lblCliente').val($('#txtCliente').val());
	}else{
	    $('#lblCliente').val('Todos');
	}
	
	if ($('#txtOrdenVenta').val()!="") {
	    $('#lblOrdenVenta').val($('#txtOrdenVenta').val());
	}else{
	    $('#lblOrdenVenta').val('Todos');
	}
	
	$('#lblFecha').val($('#txtFechaInicio').val()+' - '+$('#txtFechaFinal').val());
	
}
