$(document).ready(function(){
	$('#tblListaPrecios').hide();
	$('#liAlmacen').hide();
	$('#liLinea').hide();
	$('#liSubLinea').hide();
	$('#liProducto').hide();
	$('#mostrarPDF').hide();
        $('#mostrarExcel').hide();
	msboxTitle = "Reporte de Listado de Precios";
	$('#lstLinea').change(function(){
		cargaSubLinea();
	});
        
        $('.opcionMoneda').click(function (){
            if($('input:radio[name=opcmoneda]:checked').val()==1){
                $('#precio').text('Precio (S/.)');
                $('#monedaPDF').val(1);
            } else {
                $('#precio').text('Precio (U$.)');
                $('#monedaPDF').val(2);
            }
        });
        
        $('#mostrarExcel').click(function (){
            $('#form-ver').attr('action','/excel/listagerencial/');
        });
        
	$('#btnConsultar').click(function(e){
		e.preventDefault();
		cargaTabla(msboxTitle);

		$('#idLinea').val(idLinea);
		$('#idSubLinea').val(idSubLinea);
		$('#idAlmacen').val(idAlmacen);
		$('#idProducto').val(idProducto);
                $('#tipoStock').val(tipoStock);
		$('#mostrarPDF').show();
                $('#mostrarExcel').show();
	});
        
	$('#fsLineaSublinea').hide();
	$('input[name="rbFiltro"]').change(function(){
		$('#tblListaPrecios').hide();
		$('#lstAlmacen option').eq(0).attr('selected','selected');
		$('#lstLinea option').eq(0).attr('selected','selected');
		$('#lstSubLinea option').eq(0).attr('selected','selected');
		$('#txtCodigoProducto, #txtIdProducto').val('');
		if(this.value == "1"){
			$('#liAlmacen').hide();
			$('#liLinea').hide();
			$('#liSubLinea').hide();
			$('#liProducto').hide();
			
		}else if(this.value == "2"){
			$('#liAlmacen').show();
			$('#liLinea').hide();
			$('#liSubLinea').hide();
			$('#liProducto').hide();
		}else if(this.value == "3"){
			$('#liAlmacen').hide();
			$('#liLinea').show();
			$('#liSubLinea').hide();
			$('#liProducto').hide();
		}else if(this.value == "4"){
			$('#liAlmacen').hide();
			$('#liLinea').show();
			$('#liSubLinea').show();
			$('#liProducto').hide();
		}else{
			$('#liAlmacen').hide();
			$('#liLinea').hide();
			$('#liSubLinea').hide();
			$('#liProducto').show();
			$('#txtCodigoProducto').focus();
		}
	});
	$('#txtCodigoProducto').keyup(function(){
		if($(this).val()==""){
			$('#txtIdProducto').val('');
		}
	});
});
msboxTitle = "Reporte de Listado de Precios";
//Cargar listado de sub linea
function cargaSubLinea(){
	idLinea = $('#lstLinea option:selected').val();
	if(idLinea){
		ruta = "/sublinea/listaroptions/" + idLinea;
		$.post(ruta, function(data){
			$('#lstSubLinea').html('<option value="">-- Sub Linea --' + data);
		});
	}else{
		$('#lstSubLinea').html('<option value="">-- Sub Linea --');
	}
}
//Carga tabla stock por linea


function cargaTabla(msboxTitle){
	idLinea = $('#lstLinea option:selected').val();
	idSubLinea = $('#lstSubLinea option:selected').val();
	idAlmacen = $('#lstAlmacen option:selected').val();
	idProducto = $('#txtIdProducto').val();
	filtro = $('input[name="rbFiltro"]:checked').val();
        tipoStock = $('#lstStock').val();
	mensaje = "";
	if(filtro == "2"){
		if(idAlmacen == ""){
			mensaje = "Seleccione correctamente el almacen";
		}
	}else if(filtro == "3"){
		if(idLinea == ""){
			mensaje = "Seleccione correctamente Linea";
		}
	}else if(filtro == "4"){
		if(idLinea == "" || idSubLinea == ""){
			mensaje = "Seleccione correctamente la Linea y Sublinea";
		}
	}else if(filtro == "5"){
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
		ruta = "/reporte/listagerencial/";
		$.post(ruta, {idLinea: idLinea, idSubLinea: idSubLinea, idAlmacen: idAlmacen, idProducto: idProducto, opcmoneda: $('input:radio[name=opcmoneda]:checked').val(), tipoStock: tipoStock}, function(data){
			$("#dataGridReport").data("kendoGrid").dataSource.data(data);
		});
	}
}