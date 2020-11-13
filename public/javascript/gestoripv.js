$(document).ready(function () {

    $('#msjprd').hide();

    $('#txtproductosimulacro').autocomplete({
        source: "/producto/buscarAutocompleteLimpio/",
        select: function (event, ui) {
            verificarSaldosIniciales(ui.item.id, ui.item.value, ui.item.tituloProducto, 1);
        }
    });

    $('#msjprd').on('click', 'a', function () {
        $('#msjprd').hide();
        $('#msjprd').html('');
        return false;
    });

    $('#cmbTipoMovimiento').change(function () {
        if ($(this).val() == '01') {
            $('#txtTipoMov').html('SALIDA');
        } else {
            $('#txtTipoMov').html('ENTRADA');
        }
    });

    $('#idtxtCantidad').change(function () {
        if ($(this).val() != '' && $('#idtxtCostoUnitario').val() != '') {
            var cantidad = parseFloat($(this).val());
            var costounitario = parseFloat($('#idtxtCostoUnitario').val());
            var total = cantidad * costounitario;
            $('#idtxtTotal').val(total.toFixed(2));
        } else {
            $('#idtxtTotal').val('');
        }
    });

    $('#idtxtCostoUnitario').change(function () {
        if ($(this).val() != '' && $('#idtxtCantidad').val() != '') {
            var costounitario = parseFloat($(this).val());
            var cantidad = parseFloat($('#idtxtCantidad').val());
            var total = cantidad * costounitario;
            $('#idtxtTotal').val(total.toFixed(2));
        } else {
            $('#idtxtTotal').val('');
        }
    });

    $('#btnRegistrar').click(function () {
        if ($('#idProducto').val() == '') {
            $('#txtproductosimulacro').focus();
        } else if ($('#idTxtFechas').val() == '') {
            $('#idTxtFechas').focus();
        } else if ($('#idtxtSerie').val() == '') {
            $('#idtxtSerie').focus();
        } else if ($('#idtxtNumero').val() == '') {
            $('#idtxtNumero').focus();
        } else if ($('#cmbTipoMovimiento').val() == '') {
            $('#cmbTipoMovimiento').focus();
        } else if ($('#idtxtCantidad').val() == '') {
            $('#idtxtCantidad').focus();
        } else if ($('#idtxtCostoUnitario').val() == '') {
            $('#idtxtCostoUnitario').focus();
        } else {
            $.ajax({
                url: '/movimiento/grabaripv',
                type: 'post',
                dataType: 'json',
                data: {
                    'idProducto': $('#idProducto').val(),
                    'TxtFechas': $('#idTxtFechas').val(),
                    'txtTipoDoc': $('#idtxtTipoDoc').val(),
                    'txtSerie': $('#idtxtSerie').val(),
                    'txtNumero': $('#idtxtNumero').val(),
                    'cmbTipoMovimiento': $('#cmbTipoMovimiento').val(),
                    'txtCantidad': $('#idtxtCantidad').val(),
                    'txtCostoUnitario': $('#idtxtCostoUnitario').val()},
                success: function (resp) {
                    $('#idTxtFechas').val('');
                    $('#idtxtSerie').val('');
                    $('#idtxtNumero').val('');
                    $('#cmbTipoMovimiento').val('');
                    $('#idtxtCantidad').val('');
                    $('#idtxtCostoUnitario').val('');
                    $('#idtxtTotal').val('');
                    if (resp.rspta == 1) {
                        listarIPVs($('#idProducto').val());
                    }
                }
            });
        }
    });

    $('#tblIPVsRegistrados').on('click', '.eliminarIPV', function () {
        var idipv = $(this).data('id');
        $(this).parents("tr").remove();
        $.ajax({
            url: '/movimiento/eliminaripv',
            type: 'post',
            data: {'idipv': idipv},
            success: function (resp) {
            }
        });
        return false;
    });

});

function verificarSaldosIniciales(idproducto, codigopa, descripcion, simulacro) {
    $.ajax({
        url: '/movimiento/verificarsaldosiniciales',
        type: 'post',
        dataType: 'json',
        data: {'idproducto': idproducto, 'simulacro': simulacro},
        success: function (resp) {
            $('#periodoInicial').html(resp.periodo);
            if (resp.rspta == 1) {
                $('#idProducto').val(idproducto);
                $('#txtProducto').html($('#txtproductosimulacro').val() + ' // ' + descripcion);
                listarIPVs(idproducto);
                $('#msjprd').hide();
                $('#msjprd').html('');
            } else {
                $('#msjprd').show();
                $('#msjprd').html('<b>Msj:</b> El producto <b>' + codigopa + '</b> // <b> ' + descripcion + '</b> no tiene saldo inicial.<a style="float: right" href="#">x</a>');
            }
            $('#txtproductosimulacro').val('');
        }
    });
}

function listarIPVs(idproducto) {
    $.ajax({
        url: '/movimiento/listarIPVs',
        type: 'post',
        dataType: 'html',
        data: {'idproducto': idproducto},
        success: function (resp) {
            $('#tblIPVsRegistrados tbody').html(resp);
        },
        error: function (error) {
            console.log('error');
        }
    });
}