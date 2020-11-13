$(document).ready(function () {
    
    $('input[name="rbFiltro"]').change(function(){
        if ($(this).val() == 2) {
            $('.busqueda').attr('style', 'display: none');
            $('.busquedaLista').removeAttr('style');
            $('#XbusquedaLista').focus();
        } else {
            $('.busqueda').removeAttr('style');
            $('.busquedaLista').attr('style', 'display: none');
            $('#txtBusqueda').focus();
        }
    });
    
    $('#txtBusqueda').autocomplete({
        source: "/ordenventa/busquedaletras/",
        select: function (event, ui) {
            Resultado(ui.item.label, $('input[name="rbFiltro"]').val());
        }
    })
    
    $('#btnConsultar').click(function () {
        if ($('#XbusquedaLista').val().length != 0) {
            Resultado($('#XbusquedaLista').val(), $('input[name="rbFiltro"]').val());
        } else {
            $('#XbusquedaLista').focus();
        }
    });
    
    $('#btnLimpiar').click(function () {
        $('#TAcontenido').val('');
    });
    
});

function Resultado(numeroLetra, tipo) {
    $.post('/seguimiento/verOrdenVentaxnroLetra/', {numeroLetra: numeroLetra}, function (data) {
        $('#contResultado').removeAttr('style');
        $('#TAcontenido').val(data.resultado);
        console.log(data.resultado);
        $('#txtBusqueda').val('');
        $('#XbusquedaLista').val('');
    }, 'json');
}

function Mostrarplanilla(numeroLetra) {
    $.post('/reporte/crearplanilla/', {numeroLetra: numeroLetra}, function (data) {
        $('.body').show();
        console.log(data);
        var contador = parseInt($('#contador').val()) + 1;
        $('#contador').attr('value', contador);
        $('#planilla tbody').append("<tr>" +
                "<td>" + data.nombrecli + "<input type='hidden' name='letra[" + contador + "][nombrecli]' value='" + data.nombrecli + "'>" + "</td>" +
                "<td>" + data.apellido1 + "<input type='hidden' name='letra[" + contador + "][apellido1]' value='" + data.apellido1 + "'>" + "</td>" +
                "<td>" + data.apellido2 + "<input type='hidden' name='letra[" + contador + "][apellido2]' value='" + data.apellido2 + "'>" + "</td>" +
                "<td>" + data.doc + "<input type='hidden' name='letra[" + contador + "][doc]' value='" + data.doc + "'>" + "</td>" +
                "<td>" + data.tipodoc + "<input type='hidden' name='letra[" + contador + "][tipodoc]' value='" + data.tipodoc + "'>" + "</td>" +
                "<td>" + data.numeroletra + "<input type='hidden' name='letra[" + contador + "][nombreletra]' value='" + data.numeroletra + "'>" + "</td>" +
                "<td>" + data.fvencimiento + "<input type='hidden' name='letra[" + contador + "][fvencimiento]' value='" + data.fvencimiento + "'>" + "</td>" +
                "<td>" + data.simbolo + ' ' + data.importedoc + "<input type='hidden' name='letra[" + contador + "][simbolo]' value='" + data.simbolo + "'>" + "<input type='hidden' name='letra[" + contador + "][importedoc]' value='" + data.importedoc + "'>" + "</td>" +
                "<td><a href='#' class='btnQuitarDetalleMovimientos' rel='" + data.numeroletra + "'><img src='/imagenes/eliminar.gif'></a></td>" +
                "</tr>"
                );
        $('#txtBusqueda').val('');
    }, 'json');
}

function actualizaCampo(numeroLetra, valor) {
    $.post('/reporte/actualizaCampo/', {numeroLetra: numeroLetra, valor: valor}, function (data) {
        console.log(data);
    }, 'json');
}