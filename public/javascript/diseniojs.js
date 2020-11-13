$(document).ready(function () {
    
    $('#btnExportarExcel').hide();
  
    $('#btnConsultar').click(function (e) {
        if($('#txtCodigoProducto').val()==''){
            $('#txtCodigoProducto').focus();
        } else {
            cargaTabla();
        }
        e.preventDefault(); 
    });
    
    $('#txtCodigoProducto').keyup(function () {
        if ($(this).val() == "") {
            $('#txtIdProducto').val('');
            $('#btnExportarExcel').hide();
        }
    });
    
    
    
    
});

//Carga tabla stock por linea
function cargaTabla() {
    idProducto = $('#txtIdProducto').val();
    if ($('#txtIdProducto').val() == '') {
        /*No hace nada*/
    } else {
        $('#btnExportarExcel').show();
        ruta = "/disenio/listaDisenio/";
        $.post(ruta, {idProducto: idProducto}, function (data) {
            $("#dataGridReport").data("kendoGrid").dataSource.data(data);
        });
    }
}

