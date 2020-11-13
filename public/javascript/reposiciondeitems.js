$(document).ready(function () {
    
    $('#txtProducto').autocomplete({
        source: "/producto/buscarAutocompleteLimpio/",
        select: function (event, ui) {
            $('#idProducto').val(ui.item.id);
            $('#txtDescripcion').val(ui.item.tituloProducto);
        }
    });
    
    $('#btnReporteExcel').click(function (e) {
        if ($('#idProducto').val() != '') {
            $('#frmReporte').attr('action', '/excel/reposiciondeitems');
        } else {
            e.preventDefault();
            alert('Ingrese un Producto');
        }
    });
    
    $('#btnConsultar').click(function (e) {
        $(this).attr('disabled', 'disabled');
        $('#BlockBusqueda').html('<img src="/imagenes/cargando.gif" width="400px">');
        $('#imprimir').hide();
        if ($('#idProducto').val() != '') {
            $.ajax({
                url:'/reporte/reposiciondeitems_consultar',
                type:'post',
                datatype:'html',
                data:{'idProducto':$('#idProducto').val()},
                success:function(resp){
                    $('#BlockBusqueda').show();
                    $('#BlockBusqueda').html(resp);
                    $('#btnConsultar').removeAttr('disabled');
                    $('#imprimir').show();
                }
            });
        } else {
            e.preventDefault();
            alert('Ingrese un Producto');
            $('#BlockBusqueda').html('');
            $(this).removeAttr('disabled');
        }
    });
    
    $('#imprimir').click(function(e){
        e.preventDefault(e);
        imprSelec('BlockBusqueda');
    });
    
});
