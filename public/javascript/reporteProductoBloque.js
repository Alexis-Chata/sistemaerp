$(document).ready(function(){
    existe=0;
    lstInventario=$("#lstInventario").attr("value");
	
    $('#txtProducto').on('keydown',function(){
        $("#infoBloque").empty();
        $("#infoStockActual").empty();
      $('#lstBloques option:first').prop('selected',true);
    });
    
    $('#txtProducto').autocomplete({
        source: "/producto/buscarAutocompleteLimpio/",
        select: function (event, ui) {
            $('#idProducto').val(ui.item.id);
            $('#txtDescripcion').val(ui.item.tituloProducto);
            $.ajax({
                type: 'GET',
                url: '/inventario/stockActualProductoBloques/',
                data: 'txtIdInventario=' + lstInventario + '&txtIdProducto=' + ui.item.id,
                dataType: "json",
                success: function (data) {
                    $.each(data, function (i, val) {
                        $("#infoBloque").html("BLOQUE Y/O ANAQUEL[  " + val['bloque'] + "  ]&nbsp;&nbsp;&nbsp;");
                        $("#infoStockActual").html("STOCK ACTUAL[  " + val['stockactual'] + "  ]&nbsp;&nbsp;&nbsp;");
                        if (val['bloque'] == null) {
                            $("#infoBloque").html("BLOQUE Y/O ANAQUEL[ NO HA SIDO ASIGNADO A NINGUN BLOQUE  ]&nbsp;&nbsp;&nbsp;");
                        }
                    });

                }
            });
        }
	});

	$('#btnLimpiar').click(function(e){
		e.preventDefault();
		$('#idProducto').val('');
		$('#txtDescripcion').val('');
		$('#txtProducto').val('');
	});
    $('#btnReporteImprimirPdf').click(function(e){
			$('#frmReporte').attr('action','/pdf/reporteProductosBloque');
		
		
	});
    $('#btnReporteImprimirExcel').click(function(e){
			$('#frmReporte').attr('action','/excel/reporteProductosBloque');
	});
    

});