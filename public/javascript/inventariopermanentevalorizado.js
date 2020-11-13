$(document).ready(function () {

    $('#deshacerPrd').hide();
    $('#msjprd').hide();

    $('#txtproducto').autocomplete({
        source: "/producto/buscarAutocompleteLimpio/",
        select: function (event, ui) {
            verificarSaldosIniciales(ui.item.id, ui.item.value, ui.item.tituloProducto, 0);
        }
    });
    
    $('#txtproductosimulacro').autocomplete({
        source: "/producto/buscarAutocompleteLimpio/",
        select: function (event, ui) {
            verificarSaldosIniciales(ui.item.id, ui.item.value, ui.item.tituloProducto, 1);
        }
    });
    
    $('#periodoInicial').change(function () {
        if ($(this).val() == '') {
            $('#mesFinal').val('');
            $('#mesFinal').attr('disabled', 'disabled');
        } else {            
            $('#mesFinal').removeAttr('disabled');
        }
    });

    $('#msjprd').on('click', 'a', function () {
        $('#msjprd').hide();
        $('#msjprd').html('');
        return false;
    });

    $('#deshacerPrd').click(function () {
        $('#txtproductosimulacro').val('');
        $('#txtproducto').val('');
        $('#idProducto').val('');
        $('#txtDescripcion').val('');
        $('#periodoInicial').html('<option value=""> --- Desde --- </option>');
        $('#txtproductosimulacro').removeAttr('readonly');
        $('#txtproducto').removeAttr('readonly');
        $('#mesFinal').val('');
        $('#mesFinal').attr('disabled', 'disabled');
        $('#deshacerPrd').hide();
        return false;
    });
    
    $('#mesFinal').change(function () {
        $(this).removeClass();
    });
    
    $('#btnGenerar').click(function () {
        if ($(this).html() != 'Generando...') {
            $(this).html('Generando...');
            if ($('#idProducto').val().length == 0) {
                $('#txtproducto').focus();
            } else if ($('#periodoInicial').val() == '') {
                $('#periodoInicial').focus();
            } else if ($('#mesFinal').val() == '') {
                $('#mesFinal').focus();
            } else {
                if ($('#mesFinal').val() > 0) {
                    var periodofinal = $('#periodoInicial').val();
                    var mes = periodofinal.split(":", 2);
                    if ($('#mesFinal').val() >= mes[1] * 1) {
                        $('#frmInventarioValorizado').submit();
                        verificarSaldosIniciales($('#idProducto').val(), '', '', 0);
                    } else {
                        $('#mesFinal').addClass('errorcb');
                        $('#mesFinal').focus();
                    }
                }
            }
        }
        $(this).html('Generar <img style="vertical-align:middle" src="/imagenes/excel.png" width="20">');
    });
    
    $('#btnSimulacro').click(function () {
        if ($(this).html() != 'Generando...') {
            $(this).html('Generando...');
            if ($('#idProducto').val().length == 0) {
                $('#txtproducto').focus();
            } else if ($('#periodoInicial').val() == '') {
                $('#periodoInicial').focus();
            } else if ($('#mesFinal').val() == '') {
                $('#mesFinal').focus();
            } else {
                if ($('#mesFinal').val() > 0) {
                    var periodofinal = $('#periodoInicial').val();
                    var mes = periodofinal.split(":", 2);
                    if ($('#mesFinal').val() >= mes[1] * 1) {
                        $('#frmInventarioValorizado').submit();
                        verificarSaldosIniciales($('#idProducto').val(), '', '', 1);
                    } else {
                        $('#mesFinal').addClass('errorcb');
                        $('#mesFinal').focus();
                    }
                }
            }
        }
        $(this).html('Simulacro <img style="vertical-align:middle" src="/imagenes/excel.png" width="20">');
    });
    
    $('#btnSimulacroHTML').click(function () {
        if ($(this).html() != 'Generando...') {
            $(this).html('Generando...');
            if ($('#idProducto').val().length == 0) {
                $('#txtproducto').focus();
            } else if ($('#periodoInicial').val() == '') {
                $('#periodoInicial').focus();
            } else if ($('#mesFinal').val() == '') {
                $('#mesFinal').focus();
            } else {
                if ($('#mesFinal').val() > 0) {
                    var periodofinal = $('#periodoInicial').val();
                    var mes = periodofinal.split(":", 2);
                    if ($('#mesFinal').val() >= mes[1] * 1) {
                        $('#blockSimulacro').html('<center>Cargando...</center>');
                        $.ajax({
                            url: '/movimiento/simulacroinventariovalorizado',
                            type: 'post',
                            dataType: 'html',
                            data:$('#frmInventarioValorizado').serialize(),
                            success:function(resp){
                                $('#blockSimulacro').html(resp);
                            },
                            error:function(error){
                                    console.log('error');
                            }
                        });
                        verificarSaldosIniciales($('#idProducto').val(), '', '', 1);
                    } else {
                        $('#mesFinal').addClass('errorcb');
                        $('#mesFinal').focus();
                    }
                }
            }
        }
        $(this).html('Consultar <img style="vertical-align:middle" src="/imagenes/consultar.png" width="20">');
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
                $('#txtproducto').attr('readonly', 'readonly');
                $('#txtproductosimulacro').attr('readonly', 'readonly');
                $('#txtDescripcion').val(descripcion);                
                $('#deshacerPrd').show();
                $('#msjprd').hide();
                $('#msjprd').html('');
            } else {
                $('#idProducto').val('');
                $('#txtproducto').val('');
                $('#txtproductosimulacro').val('');
                $('#txtDescripcion').val('');
                $('#mesFinal').val('');
                $('#mesFinal').attr('disabled', 'disabled');
                $('#msjprd').show();
                $('#msjprd').html('<b>Msj:</b> El producto <b>' + codigopa + '</b> // <b> ' + descripcion + '</b> no tiene saldo inicial.<a style="float: right" href="#">x</a>');
            }
        }
    });
}