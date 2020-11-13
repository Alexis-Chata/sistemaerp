$(document).ready(function () {

    $('#fdcontenedor').hide();

    $('#txtReferencia').autocomplete({
        source: "/atencioncliente/autocompletecodigost/",
        select: function (event, ui) {
            $('#idRecepcion').val(ui.item.id);

        }
    });

    $('#txtIdTecnico').autocomplete({
        source: "/serviciotecnico/buscaactucompletetecnico/",
        select: function (event, ui) {
            $('#idTecnico').val(ui.item.id);
        }
    });

    $('#txtProducto').autocomplete({
        source: "/producto/buscarAutocompleteLimpio/",
        select: function (event, ui) {
            $('#idProducto').val(ui.item.id);
            $('#txtDescripcion').val(ui.item.tituloProducto);
        }
    });

    $('#btnConsultaExcel').click(function () {
        $('#frmBitacora').attr('action', '/excel/bitacoradeactividades')
    });

    $('#btnImprimir').click(function (e) {
        e.preventDefault();
        $('table tr td, table tr th').css('font-family', 'courier');
        $('body').css('color', 'black');
        imprSelec('contenedor');
        $('table tr td, table tr th').css('font-family', 'Calibri');
    });

    $('#btnConsultar').click(function () {
        $('#contenedor').html('<center><img src="/imagenes/actorfoto/cargandoj.gif"></center>');
        $.ajax({
            url: '/serviciotecnico/bitacoradeactividades',
            type: 'post',
            dataType: 'html',
            data: $('#frmBitacora').serialize(),
            success: function (data) {
                $('#contenedor').html(data);
                $('#fdcontenedor').show();
            }
        });
        return false;
    });

    $('#btnLimpiar').click(function (e) {
        e.preventDefault();
        limpiar();
    });

});

function limpiar() {
    $('#frmBitacora')[0].reset();
    $('#contenedor').html('');
    $('#fdcontenedor').hide();
    $('#idRecepcion').val('');
    $('#idTecnico').val('');
    $('#idProducto').val('');
}