$(document).ready(function () {
    $('#liAlmacen').hide();
    $('#liLinea').hide();
    $('#liSubLinea').hide();
    $('#liProducto').hide();
    $('#mostrarPDF').hide();
    msboxTitle = "Productos por Agotar";

    $('#btnProductosxagotarHTML').click(function (e) {
        e.preventDefault();
        cargaTabla(msboxTitle);
        $('#idLinea').val(idLinea);
        $('#idSubLinea').val(idSubLinea);
        $('#idAlmacen').val(idAlmacen);
        $('#idProducto').val(idProducto);
        $('#mostrarPDF').show();

    });

});

function cargaTabla(msboxTitle) {
    idLinea = $('#lstLinea option:selected').val();
    idSubLinea = $('#lstSubLinea option:selected').val();
    idAlmacen = $('#lstAlmacen option:selected').val();
    idProducto = $('#txtIdProducto').val();
    filtro = $('input[name="rbFiltro"]:checked').val();
    mensaje = "";
    if (filtro == "2") {
        if (idAlmacen == "") {
            mensaje = "Seleccione correctamente el almacen";
        }
    } else if (filtro == "3") {
        if (idLinea == "") {
            mensaje = "Seleccione correctamente la Linea";
        }
    } else if (filtro == "4") {
        if (idLinea == "" || idSubLinea == "") {
            mensaje = "Seleccione correctamente la Linea y Sublinea";
        }
    } else if (filtro == "5") {
        if (idProducto == "") {
            mensaje = "Ingrese correctamente el nombre del producto";
        }
    } else {
        mensaje = "";
    }
    if ($('#txtPorcentaje').val() != '') {
        if ($('#txtPorcentaje').val() < 1 || $('#txtPorcentaje').val() > 100) {
            mensaje = "El porcentaje debe estar en el rango de 1 a 100";
        }
    }
    if (mensaje != "") {
        $.msgbox(msboxTitle, mensaje);
        execute();
    } else {
        $("#tblProductosAgotados").html('<tr><td class="center" colspan="9">Cargando...</td></tr>');
        ruta = "/importaciones/productosxagotar_consultar/";
        $.post(ruta, {fechaInicio: $('#fechaInicio').val(), fechaFinal: $('#fechaFinal').val(), procentaje: $('#txtPorcentaje').val(), lstCantidadVeces: $('#lstCantidadVeces').val(), idLinea: idLinea, idSubLinea: idSubLinea, idAlmacen: idAlmacen, idProducto: idProducto}, function (data) {
            $("#tblProductosAgotados").html(data);
        });
    }
}