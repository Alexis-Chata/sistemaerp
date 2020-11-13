$(document).ready(function () {
    $('#tblListaPrecios').hide();
    $('#liAlmacen').hide();
    $('#liLinea').hide();
    $('#liSubLinea').hide();
    $('#liProducto').hide();
    $('#mostrarPDF').hide();
    $('#mostrarExcel').hide();
    msboxTitle = "Reporte de Listado de Precios";
    $('#lstLinea').change(function () {
        cargaSubLinea();
    });

    $('#opcionMoneda').change(function () {
        if ($(this).val() == 1) {
            $('#monedaPDF').val(1);
        } else {
            $('#monedaPDF').val(2);
        }
    });

    $('#mostrarPDF').click(function () {
        $('#form-ver').attr('action', '/pdf/ListaPrecios/');
    });

    $('#mostrarExcel').click(function () {
        $('#form-ver').attr('action', '/excel/listadeprecios/');
    });

    $('#btnConsultar').click(function (e) {
        e.preventDefault();
        cargaTabla(msboxTitle);
        $('#idLinea').val(idLinea);
        $('#idSubLinea').val(idSubLinea);
        $('#idAlmacen').val(idAlmacen);
        $('#idProducto').val(idProducto)
        $('#mostrarPDF').show();
        $('#mostrarExcel').show();
    });

    $('#fsLineaSublinea').hide();
    $('input[name="rbFiltro"]').change(function () {
        $('#tblListaPrecios').hide();
        $('#lstAlmacen option').eq(0).attr('selected', 'selected');
        $('#lstLinea option').eq(0).attr('selected', 'selected');
        $('#lstSubLinea option').eq(0).attr('selected', 'selected');
        $('#txtCodigoProducto, #txtIdProducto').val('');
        if (this.value == "1") {
            $('#liAlmacen').hide();
            $('#liLinea').hide();
            $('#liSubLinea').hide();
            $('#liProducto').hide();

        } else if (this.value == "2") {
            $('#liAlmacen').show();
            $('#liLinea').hide();
            $('#liSubLinea').hide();
            $('#liProducto').hide();
        } else if (this.value == "3") {
            $('#liAlmacen').hide();
            $('#liLinea').show();
            $('#liSubLinea').hide();
            $('#liProducto').hide();
        } else if (this.value == "4") {
            $('#liAlmacen').hide();
            $('#liLinea').show();
            $('#liSubLinea').show();
            $('#liProducto').hide();
        } else {
            $('#liAlmacen').hide();
            $('#liLinea').hide();
            $('#liSubLinea').hide();
            $('#liProducto').show();
            $('#txtCodigoProducto').focus();
        }
    });
    $('#txtCodigoProducto').keyup(function () {
        if ($(this).val() == "") {
            $('#txtIdProducto').val('');
        }
    });
});
msboxTitle = "Reporte de Listado de Precios";
//Cargar listado de sub linea
function cargaSubLinea() {
    idLinea = $('#lstLinea option:selected').val();
    if (idLinea) {
        ruta = "/sublinea/listaroptions/" + idLinea;
        $.post(ruta, function (data) {
            $('#lstSubLinea').html('<option value="">-- Sub Linea --' + data);
        });
    } else {
        $('#lstSubLinea').html('<option value="">-- Sub Linea --');
    }
}
//Carga tabla stock por linea

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
            mensaje = "Seleccione correctamente Linea";
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
    if (mensaje != "") {
        $.msgbox(msboxTitle, mensaje);
        execute();
    } else {
        ruta = "/reporte/listaprecios/";
        $.post(ruta, {idLinea: idLinea, idSubLinea: idSubLinea, idAlmacen: idAlmacen, idProducto: idProducto, opcmoneda: $('#opcionMoneda').val()}, function (data) {
            if ($("#opcionMoneda").val() == 1) {
                $('#precio').text('Precio (S/.)');
            } else {
                $('#precio').text('Precio (U$.)');
            }
            if (data != null) {
                $("#dataGridReport").data("kendoGrid").dataSource.data(data);
            } else {
                alert("Producto agotado, intente con otro.");
            }
        });
    }
}