$(document).ready(function () {

    $('#btnLimpiar').click(function (e) {
        e.preventDefault();
        limpiar();
    });

    $('#txtCliente').autocomplete({
        source: "/cliente/autocomplete2/",
        select: function (event, ui) {
            $('#idCliente').val(ui.item.id);

        }});

    $('#btnConsultar').click(function (e) {
        e.preventDefault();
        cargaConsulta();
    });
    
    $('#btnDescargarExcel').click(function () {
        $('#Parametros').submit();
    });

});

function limpiar() {
    $('#Parametros')[0].reset();
    $('#idCliente').val('');
}

function cargaConsulta() {
    $.ajax({
        url: '/atencioncliente/seguridad_consultar',
        type: 'post',
        dataType: 'html',
        data: $('#Parametros').serialize(),
        success: function (resp) {
            $('#tblSeguridad tbody').html(resp);
        }
    });
    $('#imprimir').show();
}