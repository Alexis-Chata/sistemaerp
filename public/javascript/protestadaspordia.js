$(document).ready(function () {

    /******************** Autocomplete ****************************/
    $('#txtOrdenVenta').autocomplete({
        source: "/ordenventa/buscarautocompletecompleto/",
        select: function (event, ui) {
            $('#idOrdenVenta').val(ui.item.id);
        }
    });

    $('#txtCliente').autocomplete({
        source: "/cliente/autocomplete2/",
        select: function (event, ui) {
            $('#idCliente').val(ui.item.id);

        }});

    $('#txtVendedor').autocomplete({
        source: "/vendedor/autocompletevendedor/",
        select: function (event, ui) {
            $('#idVendedor').val(ui.item.id);
        }
    });
    /**********************************************************/

    $('#btnLimpiar').click(function (e) {
        e.preventDefault();
        limpiar();
    });
    
    $('#btnEXCEL').click(function () {
        $('#frmProtestadas').attr('action', '/excel2/letrasprotestadasxdia');
        $('#frmProtestadas').submit();
    });

});

function limpiar() {
    $('#frmProtestadas')[0].reset();
    $('#idOrdenVenta').val('');
    $('#idCliente').val('');
    $('#idVendedor').val('');
    $('#lstCategoriaPrincipal').val('');
}