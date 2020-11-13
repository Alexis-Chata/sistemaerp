$(document).ready(function () {

    if ($('#cuadroutilidad').val() == 1) {
        $('input').attr('disabled', 'disabled');
    }
    
    $('.EditarNompro').click(function () {
        var idproducto = $(this).data('id');
        return false;
    });
    
    $('#btnImprimir').click(function (e) {
        e.preventDefault();
        $('#btnCerrarDetalle').hide();
        $(this).hide();
        $('table tr td, table tr th').css('font-family','courier');
        imprSelec('contenedordetallemovimiento');
        $('table tr td, table tr th').css('font-family','calibri');
        $('#btnCerrarDetalle').show();
        $(this).show();
    });
    
    $('#lstAnio').change(function () {
        var anio = $(this).val();
        $.ajax({
            url: '/ordencompra/ordenesconfirmadasxanio/' + anio,
            success: function (resp) {
                $('#lstValorizados').html(resp);
            }
        });
    });
    
    $('.EditarNompro').click(function () {
        $('#resp').html('');
        var idproducto = $(this).data('id');
        $('#span_' + idproducto).hide();
        $('.inputText[data-id="' + idproducto + '"]').show();
        $('.GuardarNompro[data-id="' + idproducto + '"]').show();
        $('.inputText[data-id="' + idproducto + '"]').focus();
        $(this).hide();
        return false;
    });
    
    $('.inputText').keypress(function (e) {
        if (e.keyCode == 13) {
            var idproducto = $(this).data('id');
            actualizarnompro(idproducto, $(this).val());            
        }
    });
    
    $('.GuardarNompro').click(function () {
        var idproducto = $(this).data('id');
        actualizarnompro(idproducto, $('.inputText[data-id="' + idproducto + '"]').val());
        return false;
    });
    
    $('.neto').blur(function () {
        padre = $(this).parents('tr');
        tipoCambio = parseFloat(padre.find('.tipocambio').val());
        cifVentas = parseFloat(padre.find('.cifVentas').val());
        precioNeto = parseFloat(padre.find('.neto').val());
        preciolista = parseFloat(padre.find('.preciolista').val());
        precionetosoles = parseFloat(precioNeto * tipoCambio).toFixed(2);
        //costoAlmacen=parseFloat(padre.find('.costoAlmacen').val());
        if ($(this).val() != 0 && $(this).val() != "") {
            if (precioNeto < cifVentas) {
                $('#resp').html('Error : El Precio Neto no puede ser menor que el Cif de Ventas ').css('color', 'red');
                padre.find('.preciolista').attr('readonly', 'readonly').css('background', 'skyblue').val('0');
                padre.find('.utilidad').val(0);
                // }else if (neto<cifVentas) {
                // 	$('#resp').html('Error : El Precio Neto no puede ser menor que el Cif de Ventas ').css('color','red');
                // 	padre.find('.preciolista').attr('readonly','readonly').css('background','skyblue').val('0');
                // 	padre.find('.utilidad').val(0);
            } else {
                $('#resp').html('');
                padre.find('.lblNeto').html(precioNeto).css('color', 'blue');
                padre.find('.preciolista').removeAttr('readonly').css('background', 'none');
                padre.find('.netosoles').val(precionetosoles);
                padre.find('.lblPrecioNetoSoles').html(precionetosoles).css('color', 'blue');
                var utilidades;
                utilidades = utilidad(preciolista, cifVentas);
                padre.find('.utilidad').val(utilidades)
                //padre.find('.utilidad').val(0);
                if (verificacion() == true) {
                    $('#btnAceptar').removeAttr('disabled').css('background', '#0693DE');
                } else {
                    $('#btnAceptar').attr('disabled', 'disabled').css('background', 'red');
                }
            }
        } else {
            $('#resp').html('Error : Ingrese un valor ').css('color', 'red');
            padre.find('.preciolista').attr('readonly', 'readonly').css('background', 'skyblue').val('0');
            padre.find('.utilidad').val(0);
        }
    });
    
    $('.preciolista').blur(function () {
        var padre = $(this).parents('tr');
        tipoCambio = parseFloat(padre.find('.tipocambio').val());
        cifVentas = parseFloat(padre.find('.cifVentas').val());
        precioNeto = parseFloat(padre.find('.neto').val());
        preciolista = parseFloat(padre.find('.preciolista').val());
        preciolistasoles = parseFloat(preciolista * tipoCambio).toFixed(2);
        // var preciocosto=parseFloat(padre.find('.fobUnit').val());
        // var preciolista=parseFloat($(this).val());
        if (precioNeto != 0 && precioNeto != "") {
            if (preciolista < precioNeto) {
                $('#resp').html('Error : El Precio Lista no puede ser menor que el Precio Neto ').css('color', 'red');
                padre.find('.utilidad').val(0);
                $('#btnAceptar').attr('disabled', 'disabled').css('background', 'red');
            } else {
                $('#resp').html('');
                var utilidades;
                utilidades = utilidad(preciolista, cifVentas);
                padre.find('.utilidad').val(utilidades);
                padre.find('.lblUtilidad').html(utilidades).css('color', 'blue');
                padre.find('.lblPrecioListaDolares').html(preciolista).css('color', 'blue');
                padre.find('.preciolistasoles').val(preciolistasoles);
                padre.find('.lblPrecioListaSoles').html(preciolistasoles).css('color', 'blue');
                if (verificacion() == true) {
                    $('#btnAceptar').removeAttr('disabled').css('background', '#0693DE');
                } else {
                    $('#btnAceptar').attr('disabled', 'disabled').css('background', 'red');
                }
            }
        } else {
            $('#resp').html('Error : Ingrese un Valor').css('color', 'red');
            padre.find('.utilidad').val(0);
            $('#btnAceptar').attr('disabled', 'disabled').css('background', 'red');
        }
    });
    
    $('#imprimir').click(function (e) {
        e.preventDefault(e);
        $('.EditarNompro').hide();
        $('.GuardarNompro').hide();
        $('#imprimir').hide();
        $('#btnAceptar').hide();
        $('.lblPrecioLista').show();
        $('.preciolista').hide();
        $('.preciolistasoles').hide();
        $('.lblNeto').show();
        $('.neto').hide();
        $('.netosoles').hide();
        $('.lblPrecioListaDolares').show();
        $('.lblPrecioListaSoles').show();
        $('.lblPrecioNetoSoles').show();
        $('.lblPrecioNetoSoles').show();
        $('.lblUtilidad').show();
        $('.utilidad').hide();
        imprSelec('contenedorImpresion');   
        $('.GuardarNompro').removeAttr('style');
        $('.EditarNompro').removeAttr('style');
        $('.lblPrecioLista').hide();
        $('.preciolista').show();
        $('.preciolistasoles').show();
        $('.lblNeto').hide();
        $('.neto').show();
        $('.lblPrecioListaDolares').hide();
        $('.lblPrecioListaSoles').hide();
        $('.lblPrecioNetoSoles').hide();
        $('.netosoles').show();
        $('#imprimir').show();
        $('#btnAceptar').show();
        $('.lblUtilidad').hide();
        $('.utilidad').show();
    });
    $('#lstValorizados').change(function () {
        location = '/ordencompra/cuadroUtilidad/' + $(this).val();
    });
    $('#lstxOrdenCompra').change(function () {
        location = '/ordencompra/utilidadxContainer/' + $(this).val();
    });
    $('.detalledelMovimiento').click(function (e) {
        e.preventDefault();
        idcontenedor = $(this).attr('id');
        //alert(idcontenedor);
        $.ajax({
            url: '/ordencompra/listaDetalle',
            type: 'get',
            data: {'idcontenedor': idcontenedor},
            success: function (resp) {
                //console.log(resp);
                $('#contenedordetallemovimiento').hide('Blind');
                $('#tablacontenedor tbody').html(resp);
                $('#contenedordetallemovimiento').show('Blind');
            },
            error: function (error) {
                //console.log('error');
            }
        });
    });
});

function actualizarnompro(idproducto, nompro) {
    $('#resp').html('');
    $.ajax({
        url: '/ordencompra/actualizarnompro',
        type: 'post',
        async: false,
        dataType:'json',
        data: {'idproducto': idproducto, 'nompro': nompro},
        success: function (resp) {
            if (resp.rspta == 1) {
                $('#span_' + idproducto).html(nompro);    
                $('#span_' + idproducto).show();
                $('.inputText[data-id="' + idproducto + '"]').hide();
                $('.GuardarNompro[data-id="' + idproducto + '"]').hide();
                $('.EditarNompro[data-id="' + idproducto + '"]').removeAttr('style');
            } else {   
                $('#span_' + idproducto).show();
                $('.inputText[data-id="' + idproducto + '"]').hide();
                $('.GuardarNompro[data-id="' + idproducto + '"]').hide();
                $('.EditarNompro[data-id="' + idproducto + '"]').hide();
                $('#resp').html('El producto ha sido vendido, ya no se puede actualizar.').css('color', 'red');
            }
        },
        error: function (error) {
            //console.log('error');
        }
    });
}

function verificacion() {
    var respuesta = true;
    $('.utilidad').each(function () {
        if ($(this).val() > 0) {
            console.log($(this).val());
        } else {
            console.log('false');
            respuesta = false;
        }
    });
    return respuesta;
}

function utilidad(preciolista, cifVentas) {
    var respuesta = 0;
    if (preciolista != 0) {
        respuesta = ((preciolista - cifVentas) / cifVentas) * 100;
    }
    return respuesta.toFixed(2);
}