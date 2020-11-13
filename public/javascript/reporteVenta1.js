$(document).ready(function(){
	var lstCategoriaPrincipal=$('#lstCategoriaPrincipal').html();
	var lstZonaCobranza=$('#lstZonaCobranza').html();
	var lstZona=$('#lstZona').html();
	//$('.style').customSelect();

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
	/********************** Botones *****************************/
	$('#btnLimpiar').click(function(e){
		e.preventDefault();
		limpiar();
	});
	$('#btnConsultarPorcentaje').click(function(e){
                console.log("plop");
		e.preventDefault();
                if($('#lstCategoriaPrincipal').val() == '' || $('#lstZonaCobranza').val() == ''){
                    alert("DEBE SELECCIONAR UNA CATEGORIA");
                }else{
                    consulta();
                }
	});
	$('#btnImprimir').click(function(e){
		e.preventDefault();
		imprSelec('contenedor');
		
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
function consulta(){
	$.ajax({
		url:'/reporte/reportePorcentajej',
		type:'post',
		async: false,
		dataType:'html',
		data:$('#frmCobranza').serialize(),
		success:function(resp){
			$('#divReport').html('');
			$('#divReport').html(resp);//.show();
		}
	});
}

function limpiar(){
	$('#frmCobranza')[0].reset();
	$('.encabezado').html('');
	//$('#lstCategoriaPrincipal').val('');
	$('.customSelectInner').html('');
	$('#lstCategoriaPrincipal').change();
	$('#idOrdenVenta').val('');
	$('#idCliente').val('');
	$('#idVendedor').val('');
	$('#tblEncabezado').hide();
	$('#tblCuerpo').hide();
	
	
}
function encabezado(){
	$('#tblEncabezado').show();
	if ($('#lstCategoriaPrincipal').val()!="") {
		$('#lblCategoriaPrincipal').html($('#lstCategoriaPrincipal option:selected').html());
	}else{
		$('#lblCategoriaPrincipal').html('Todos');
	}
	if ($('#lstZonaCobranza').val()!="") {
		$('#lblZonaCobranza').html($('#lstZonaCobranza option:selected').html());
	}else{
		$('#lblZonaCobranza').html('Todos');
	}
	if ($('#lstZona').val()!="") {
		$('#lblZona').html($('#lstZona option:selected').html());
	}else{
		$('#lblZona').html('Todos');
	}
	if ($('#lstTipoCobranza').val()!="") {
		$('#lblTipoCobranza').html($('#lstTipoCobranza option:selected').html());
	}else{
		$('#lblTipoCobranza').html('Todos');
	}
	if ($('#lstSituacion').val()!="") {
		$('#lblSituacion').html($('#lstSituacion option:selected').html());
	}else{
		$('#lblSituacion').html('Todos');
	}
	$('#lblVendedor').html($('#txtVendedor').val());
	$('#lblCliente').html($('#txtCliente').val());
	$('#lblOrdenVenta').html($('#txtOrdenVenta').val());
	$('#lblFecha').html($('#txtFechaInicio').val()+' - '+$('#txtFechaFinal').val());
	
}



      google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawChart);
      function drawChart() {

        var data = google.visualization.arrayToDataTable([
          ['Task', 'Hours per Day'],
          ['Work',     11],
          ['Eat',      2],
          ['Commute',  2],
          ['Watch TV', 2],
          ['Sleep',    7]
        ]);

        var options = {
          title: 'My Daily Activities'
        };

        var chart = new google.visualization.PieChart(document.getElementById('piechart'));

        chart.draw(data, options);
      }
