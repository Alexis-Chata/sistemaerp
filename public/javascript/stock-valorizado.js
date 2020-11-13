$(document).ready(function(){
	$('#lstLinea').change(function(){
		cargaSubLinea();
	});
	$('#btnConsultar').click(function(e){
		e.preventDefault();
		cargaTabla();
	});
	$('input[name="rbFiltro"]').change(function(){
		if($('input[name="rbFiltro"]:checked').val() == "1"){
			$('#subLinea').show();
		}else{
			$('#subLinea').hide();
		}
	});
});
//Cargar listado de sub linea
function cargaSubLinea(){
	idLinea = $('#lstLinea option:selected').val();
	if(idLinea !=0){
		ruta = "/sublinea/listaroptions/" + idLinea;
		$firsRow = $('#zeroRow');
		$.post(ruta, function(data){
			$('#lstSubLinea').html('<option value="0" id="zeroRow">Seleccione Sub Linea</option>' + data);
		});
	}else{
		$('#lstSubLinea').html('<option value="0" id="zeroRow">Seleccione Sub Linea</option>');
	}
}
//Carga tabla stock por linea
function cargaTabla(){
	idLinea = $('#lstLinea option:selected').val();
	idSubLinea = $('#lstSubLinea option:selected').val();
	filtro = $('input[name="rbFiltro"]:checked').val();
	ruta = "/reporte/reporteStockValorizado/";
	if(filtro == "1"){
		if(idLinea == 0 || idSubLinea == 0){
			$.msgbox('Listado de Stock Valorizado', "Seleccione correctamente la Linea y Sub Linea");
			execute();
		}else{
			$.post(ruta,{sublinea: idSubLinea}, function(data){
				$("#dataGridReport").data("kendoGrid").dataSource.data(data);
			});
		}
			
	}else{
		if(idLinea == 0){
			$.msgbox('Listado de Stock Valorizado', "Seleccione correctamente una linea");
			execute();
		
		}else{
			$.post(ruta,{linea: idLinea,sublinea: idSubLinea}, function(data){
				$("#dataGridReport").data("kendoGrid").dataSource.data(data);
			});
		}
	}
}

/*Messagebox
-------------------------------------------------------------- */
	function execute(){
	    var marginTop = "-" + ($("#msgbox").height() / 2) + "px";
	    var marginLeft = "-" + ($("#msgbox").width() / 2) + "px";
	    $("#msgbox").css({"margin-top":marginTop, "margin-left":marginLeft});
	}