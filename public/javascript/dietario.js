$(document).ready(function () {
    // Llamadas a funciones
    detalleprotesto = $('#detalleprotesto');
    idordenventa = $('#txtIdOrden');
    idcliente = $('#idcliente');
    idcobrador = $('#lstcobrador');
    contenerdorProtestado = $('#contenerdorProtestado');
    contenedorRenovado = $('#contenedorRenovado');
    tipoPago = $('#tipoPago');
    montoporcentaje = $('#montoporcentaje');
    lblDias = $('#lblDias');
    diasVencimiento = $('#diasVencimiento');
    verificadorGlobal = false;
    var claseGlobal;

    minimo = 0;
    maximo = 0;

    contenedorRenovado.hide();
    contenerdorProtestado.hide();
    detalleprotesto.hide();




    fecharenovado = $('#fecharenovado');
    montorenovado = $('#montorenovado');

    padregeneral = "";
    montogeneral = 0;
    formacobrogeneral = "";
    iddetalleordencobrogeneral = "";
    numeroletrageneral = "";
    valorLetra = 0;
    montoadicional = $('#montoadicional');
    fechavencimientogeneral = "";
    
    $('#chkListaIngresos').change(function() {
        if($(this).prop('checked')) {
            $('#tblIngreso').show();
        } else {
            $('#tblIngreso').hide();
        }
    });
    
    $('#tblIngreso').on('change', '.classIngresos', function () {
        var acumulado = parseFloat($('#idBlockAcumulado').html());
        var saldo = parseFloat($(this).data('saldo'));
        var total = parseFloat($('#idBlockSaldo').html());
        if($(this).prop('checked')) {
            if (total > acumulado) {
                acumulado += saldo;
            } else {
                $(this).prop('checked', false);
            }
        } else {
            acumulado -= saldo;
        }
        var diferencia = parseFloat(total - acumulado).toFixed(2);
        if (diferencia > 0) {
            diferencia = "Faltan " + diferencia;
        } else if (diferencia < 0) {
            diferencia = "Sobra " + (diferencia*-1);
        }
        $('#idBlockAcumulado').html(acumulado);
        $('#idBlockDiferencia').html(diferencia);
        return false;
    });
    
    $('#idPandemia').click(function () {
        if ($(this).is(':checked')) {
            $('.classPandemia').val('');
            $('.classPandemia').attr('disabled', 'disabled');
            $('#montorenovado').attr('readonly', 'readonly');
            $('#montorenovado').val('0');
        } else {
            $('.classPandemia').removeAttr('disabled');
            $('#montorenovado').removeAttr('readonly');
        }
    });

    $('#idPandemiaProtesto').click(function () {
        if ($(this).is(':checked')) {
            $('#montoadicional').val('0');
        } else {
            $('#montoadicional').val('');
            $('#montoadicional').focus();
        }
    });


    $('#txtOrdenVenta').autocomplete({
        source: "/ordenventa/PendientesxPagar/",
        select: function (event, ui) {
            $('#txtIdOrden').val(ui.item.id);
            $('#idOrdenVenta').val(ui.item.id);
            buscaOrdenCobro();
            cargaDetalleOrdenCobro();
        }
    });

    $('#txtOrdenVenta2').autocomplete({
        source: "/ordenventa/busquedaletras/",
        select: function (event, ui) {
            $('#txtIdOrden').val(ui.item.id);
            buscaOrdenCobro();
            cargaDetalleOrdenCobro2();
        }
    });

    $(".pagar").live("click", function (e) {
        e.preventDefault();
        elemento = $(this);
        padregeneral = $(this).parents('tr');
        padregeneral.addClass('active-row');
        $('#lstcobrador').val('');
        
        $('#chkListaIngresos').prop('checked', true);
        $('#tblIngreso').show();
        cargaSumaIngresos_seleccion(idordenventa.val());
        $('#contenedorPago').dialog('open');
    });

    $(".protestar").live("click", function (e) {
        e.preventDefault();
        elemento2 = $(this);
        padregeneral = elemento2.parents('tr');
        padregeneral.addClass('active-row');
        montogeneral = padregeneral.find('.importe').html();
        formacobrogeneral = padregeneral.find('.formacobro').val();
        iddetalleordencobrogeneral = padregeneral.find('.iddetalleordencobro').val();
        numeroletrageneral = padregeneral.find('.lblletra').html();
        valorLetra = parseFloat(padregeneral.find('.valorLetra').val());
        $('#contenedorPreguntas').dialog('open');

    });
    $(".renovar").live("click", function (e) {
        e.preventDefault();
        elemento3 = $(this);
        padregeneral = elemento3.parents('tr');
        padregeneral.addClass('active-row');
        montogeneral = padregeneral.find('.importe').html();
        formacobrogeneral = padregeneral.find('.formacobro').val();
        iddetalleordencobrogeneral = padregeneral.find('.iddetalleordencobro').val();
        numeroletrageneral = padregeneral.find('.lblletra').html();
        valorLetra = parseFloat(padregeneral.find('.valorLetra').val());
        //validarRenovado();
        /*if (valorLetra<60) {
         $('#tipoPago').val(2);
         $('#tipoPago').attr('disabled','disabled');
         }else{
         $('#tipoPago').val(1);
         $('#tipoPago').removeAttr('disabled');
         }*/
        $('#montorenovado').val('');
        $('#montorenovado').removeAttr('readonly');
        $('.classPandemia').removeAttr('disabled');
        $('#idPandemia').prop('checked', false);
        contenedorRenovado.dialog('open');

    });

    $('.extornar').live('click', function (e) {
        e.preventDefault();
        elemento4 = $(this);
        claseGlobal = $(this).attr('class');
        padregeneral = elemento4.parents('tr');
        padregeneral.addClass('active-row');
        iddetalleordencobrogeneral = padregeneral.find('.iddetalleordencobro').val();
        valorLetra = parseFloat(padregeneral.find('.valorLetra').val());
        $('#contendorAutorizacion').dialog('open');
    });

    $('.modificar').live('click', function (e) {
        e.preventDefault();

        claseGlobal = $(this).attr('class');
        padregeneral = $(this).parents('tr');
        iddetalleordencobrogeneral = padregeneral.find('.iddetalleordencobro').val();
        valorLetra = parseFloat(padregeneral.find('.valorLetra').val());

        fechavencimientogeneral = padregeneral.find('.fechavencimiento').html();
        console.log(fechavencimientogeneral);
        $('#nuevaFecha').val(fechavencimientogeneral);
        $('#valorModificar').html('S/.' + valorLetra.toFixed(2));
        $('#idModificar').val(iddetalleordencobrogeneral);
        $('#contendorAutorizacion').dialog('open');
    });

    $('#contenerdorProtestado').dialog({
        autoOpen: false,
        modal: true,
        buttons: {
            "A Credito": function () {
                if (confirm('¿Esta Seguro de Convertir a Credito la Letra?')) {
                    if (validarProtestado()) {
                        if (validarMaximoPagoacuenta() == true) {
                            $.ajax({
                                url: '/ingresos/protestar',
                                type: 'post',
                                dataType: 'json',
                                data: {'idordenventa': idordenventa.val(), 'idcliente': idcliente.val(), 'idcobrador': idcobrador.val(), 'monto': montogeneral, 'formacobro': formacobrogeneral, 'iddetalleordencobro': iddetalleordencobrogeneral, 'numeroletra': numeroletrageneral, 'montoadicional': montoadicional.val(), 'dias': lblDias.val(), 'flete': $('#fleteProtestado').val(), 'envioSobre': $('#envioSobreProtestado').val(), 'gastoBancario': $('#gastoBancarioProtestado').val(), 'costoMantenimiento': $('#costoMantenimientoProtestado').val(), 'pagoAcuenta': $('#pagoAcuenta').val()},
                                success: function (resp) {
                                    console.log(resp);
                                    if (resp.errorpandemia == 1) {
                                        alert(resp.msj);
                                    }
                                    padregeneral.find('.situacion').html('Protestado');
                                    padregeneral.find('.pagar').hide();
                                    padregeneral.find('.protestar').hide();
                                    padregeneral.find('.renovar').hide();
                                    cargaDetalleOrdenCobro2();

                                }
                            });
                            $('#contenerdorProtestado').dialog('close');

                            montoadicional.val('');
                            lblDias.val('');
                            $('#respProtestado').html('');
                        } else {
                            $('#respProtestado').html('El monto a cuenta no debe superar el monto de la letra').css('color', 'red');
                        }
                    } else {
                        $('#respProtestado').html('Ingrese Correctamente los Valores').css('color', 'red');
                    }
                }
            },
            "A Nueva Letra": function () {
                if (confirm('¿Esta Seguro de Crear una nueva Letra?')) {
                    if (validarProtestado()) {
                        if (validarMaximoPagoacuenta()) {
                            $.ajax({
                                url: '/ingresos/refinanciar',
                                type: 'post',
                                dataType: 'html',
                                data: {'idordenventa': idordenventa.val(), 'idcliente': idcliente.val(), 'idcobrador': idcobrador.val(), 'monto': montogeneral, 'formacobro': formacobrogeneral, 'iddetalleordencobro': iddetalleordencobrogeneral, 'numeroletra': numeroletrageneral, 'montoadicional': montoadicional.val(), 'dias': lblDias.val(), 'flete': $('#fleteProtestado').val(), 'envioSobre': $('#envioSobreProtestado').val(), 'gastoBancario': $('#gastoBancarioProtestado').val(), 'costoMantenimiento': $('#costoMantenimientoProtestado').val(), 'pagoAcuenta': $('#pagoAcuenta').val()},
                                success: function (resp) {
                                    console.log(resp);
                                    padregeneral.find('.situacion').html('Refinanciado');
                                    padregeneral.find('.pagar').hide();
                                    padregeneral.find('.protestar').hide();
                                    padregeneral.find('.renovar').hide();
                                    cargaDetalleOrdenCobro2();
                                }
                            });
                            $('#contenerdorProtestado').dialog('close');

                            montoadicional.val('');
                            lblDias.val('');
                            $('#respProtestado').html('');
                        } else {
                            $('#respProtestado').html('El monto a cuenta no debe superar el monto de la letra').css('color', 'red');
                        }
                    } else {
                        $('#respProtestado').html('Ingrese Correctamente los Vaores').css('color', 'red');
                    }
                }
            }

        },
        close: function () {

            montoadicional.val('');
            lblDias.val();
            $('#respProtestado').html('');
            $('#fleteProtestado').val('');
            $('#envioSobreProtestado').val('');
            $('#gastoBancarioProtestado').val('');
            $('#costoMantenimientoProtestado').val('');
            $('#pagoAcuenta').val('');
            $('#idPandemiaProtesto').prop('checked', false);
            padregeneral.removeClass();
        }
    })

    $('#CPVariasLetras').dialog({
        autoOpen: false,
        modal: true,
        buttons: {

            "varias letras": function () {
                if (confirm('¿Esta Seguro de Crear las letras?')) {
                    pagoAcuentaVariasLetras = $('#pagoAcuentaVariasLetras').val();
                    if (pagoAcuentaVariasLetras < valorLetra) {

                        montoadicional_2 = $('#montoadicional_2').val();
                        condicionLetra = $('#condicionLetra').val();
                        if (validarVariasLetras()) {
                            $.ajax({
                                url: '/ingresos/variasLetras',
                                type: 'post',
                                dataType: 'html',
                                data: {'idordenventa': idordenventa.val(), 'idcliente': idcliente.val(), 'idcobrador': idcobrador.val(), 'monto': montogeneral, 'formacobro': formacobrogeneral, 'iddetalleordencobro': iddetalleordencobrogeneral, 'numeroletra': numeroletrageneral, 'montoadicional': montoadicional_2, 'idletras': condicionLetra, 'pagoAcuenta': $('#pagoAcuentaVariasLetras').val(), 'flete': $('#fleteVariasLetras').val(), 'envioSobre': $('#envioSobreVariasLetras').val(), 'gastoBancario': $('#gastoBancarioVariasLetras').val(), 'costoMantenimiento': $('#costoMantenimientoVariasLetras').val()},
                                success: function (resp) {

                                    cargaDetalleOrdenCobro2();
                                }
                            });


                            $('#montoadicional_2').val('');
                            $('#condicionLetra').val(1);


                            $('#CPVariasLetras').dialog('close');
                        } else {
                            $('#respVariasLetras').html('Ingrese Correctamente los Valores').css('color', 'red');

                        }
                    } else {
                        $('#respVariasLetras').html('El monto a cuenta no debe superar el monto de la letra').css('color', 'red');
                    }
                }
            }

        },
        close: function () {

            $('#montoadicional_2').val('');
            $('#condicionLetra').val(1);
            $('#fleteVariasLetras').val('');
            $('#envioSobreVariasLetras').val('');
            $('#gastoBancarioVariasLetras').val('');
            $('#costoMantenimientoVariasLetras').val('');
            $('#pagoAcuentaVariasLetras').val('');
            $('#pagoAcuentaVariasLetras').val('');
            $('#respVariasLetras').html('');
            padregeneral.removeClass();
        }
    })

    $('#contenedorPreguntas').dialog({
        autoOpen: false,
        modal: true,
        width: 380,
        buttons: {
            "A credito o una Letra": function () {
                contenerdorProtestado.dialog('open');
                $('#contenedorPreguntas').dialog('close');
            },
            "A varias Letras": function () {
                $('#CPVariasLetras').dialog('open');
                $('#contenedorPreguntas').dialog('close');
            }

        },
        close: function () {

            montoadicional.val('');
            lblDias.val();
            padregeneral.removeClass();
        }
    });

    $('.deshacerPago').live('click', function (e) {
        e.preventDefault();
        claseGlobal = $(this).attr('class');
        padregeneral = $(this).parents('tr');
        padregeneral.addClass('active-row');
        iddetalleordencobrogeneral = padregeneral.find('.iddetalleordencobro').val();
        valorLetra = parseFloat(padregeneral.find('.valorLetra').val());
        console.log(iddetalleordencobrogeneral);
        console.log(claseGlobal);
        $('#contendorAutorizacion').dialog('open');
    });

    $('.anular').live('click', function (e) {
        padregeneral = $(this).parents('tr');
        padregeneral.addClass('active-row');
        iddetalleordencobrogeneral = padregeneral.find('.iddetalleordencobro').val();

        claseGlobal = $(this).attr('class');
        console.log(claseGlobal);

        if (confirm('¿Esta seguro de anular?')) {
            $('#contendorAutorizacion').dialog('open');
        }
        ;
    });

    $('#contenedorModificar').dialog({
        modal: true,
        width: 500,
        autoOpen: false,
        close: function () {
            limpiarModificar();
        }
    });
    $('#contenedorDeshacerPago').dialog({
        modal: true,
        width: 500,
        autoOpen: false,
        buttons: {
            Aceptar: function () {
                console.log(iddetalleordencobrogeneral);
                console.log('deshacerPago');
                var motivo = $('#motivo').val();

                if (motivo != "") {
                    deshacerPago(iddetalleordencobrogeneral, valorLetra, motivo);

                } else {
                    $('#respDeshacerPago').html('Ingrese su Motivo por el cual va Deshacer el pago !').css('color', 'red');
                }

            }
        },
        close: function () {
            padregeneral.removeClass();
            $('#motivo').val('');
            $('#respDeshacerPago').html('');
        }
    });

    $('#prueba').click(function (e) {
        e.preventDefault();
        console.log($('#formularioModificar').serialize());
        if (validaTotalModificar(valorLetra) == true) {
            if (validarVaciosModificar() == true) {
                modificar();
            } else {
                alert('Ingrese las Cantidad con sus respectivos dias ');
            }
        } else {
            alert('La Cantidad asignada no es igual al Total de la Letra');
        }


    });

    $('#cantidadLetras').change(function () {
        cant = $(this).val();
        if (cant == 1) {
            $('#montoLetra').removeAttr('disabled');
            $('#diasMontoLetra').removeAttr('disabled');

            $('#nuevaLetra').attr('disabled', 'disabled').val('');
            $('#montoLetraNueva').attr('disabled', 'disabled').val('');
        } else if (cant == 2) {
            $('#nuevaLetra').removeAttr('disabled');
            $('#montoLetraNueva').removeAttr('disabled');

            $('#montoLetra').attr('disabled', 'disabled').val('');
            $('#diasMontoLetra').attr('disabled', 'disabled').val('');
        } else {
            $('#nuevaLetra').attr('disabled', 'disabled').val('');
            $('#montoLetraNueva').attr('disabled', 'disabled').val('');

            $('#montoLetra').attr('disabled', 'disabled').val('');
            $('#diasMontoLetra').attr('disabled', 'disabled').val('');
        }
    });
    $('#imprimir').click(function (e) {
        e.preventDefault();
        $('.pagar').hide();
        $('.protestar').hide();
        $('.renovar').hide();
        $('.deshacerPago').hide();
        $('.extornar').hide();
        $('#cliente').hide();
        $('#datosingreso').hide();
        imprimircliente();
        $('#imprimirCliente').show();
        $('table tr td, table tr th').css('font-family', 'courier');
        imprSelec('muestra');
        $('table tr td, table tr th').css('font-family', 'calibri');
        $('#cliente').show();
        $('.pagar').show();
        $('.protestar').show();
        $('.renovar').show();
        $('.extornar').show();
        $('#cliente').show();
        $('#imprimirCliente').hide();
        $('#datosingreso').show();
    });

    $('#contenedorPago').dialog({
        autoOpen: false,
        modal: true,
        width: 700,
        buttons: {
            "Pagar": function () {
                nrorecibo = $('#numeroRecibo').val();
                console.log(nrorecibo);
                if ($('#fechaPago').val() != "") {
                    if (nrorecibo != "" || nrorecibo != 0) {
                        if (verificarreciboDietario(nrorecibo) == true) {
                            if (verificarMontoAcumulado() == true) {
                                pagar();
                            } else {
                                $('#respRecibo').html('El monto acumulado de los recibos no es suficiente').css('color', 'red');
                            }
                        } else {
                            $('#respRecibo').html('El Recibo ya fue ingresado').css('color', 'red');
                        }
                    } else {
                        $('#respRecibo').html('Ingrese un valor valido').css('color', 'red');
                    }
                } else {
                    $('#respRecibo').html('Ingrese la fecha de pago').css('color', 'red');
                }
            }
        },
        close: function () {

            $('#numeroRecibo').val('');
            $('#respRecibo').html('');
            $('#fechaPago').val('');
            padregeneral.removeClass();
        }
    })
    $('#contenedorPreguntaExtornado').dialog({
        autoOpen: false,
        modal: true,
        width: 450,
        buttons: {
            "Nuevo Porcentaje o Monto": function () {
                $('#contenedorExtornado').dialog('open');
                $('#contenedorPreguntaExtornado').dialog('close');
            },
            "Por Pago Completo": function () {
                if (confirm('¿Esta seguro de Extornar por Pago ?')) {
                    $('#contenedorPreguntaExtornado').dialog('close');
                    extornaxPago(iddetalleordencobrogeneral);
                    padregeneral.removeClass();
                }

            }

        },
        close: function () {

            montoadicional.val('');
            lblDias.val();

        }
    })
    $('#contenedorExtornado').dialog({
        autoOpen: false,
        modal: true,
        width: 350,
        buttons: {
            "Aceptar": function () {
                if (confirm('¿Esta seguro de Extornar ?')) {
                    if (validarExtornado()) {
                        nuevoTipoPago = $('#nuevoTipoPago').val();
                        nuevomontoporcentaje = $('#nuevoMontoPorcentaje').val();

                        extornaxPorcentajeMonto(iddetalleordencobrogeneral, nuevomontoporcentaje, nuevoTipoPago);
                    } else {
                        alert('Ingrese correctamente los valores !');
                    }
                }

            }

        },
        close: function () {
            $('#nuevoTipoPago').val('');
            $('#nuevoMontoPorcentaje').val('');
            padregeneral.removeClass();

        }
    })
    $('#contendorAutorizacion').dialog({
        autoOpen: false,
        modal: true,
        width: 350,
        buttons: {
            "Aceptar": function () {
                usuario = $('#usuario').val();
                contrasena = $('#contrasena').val();
                if (usuario != "" && contrasena != "") {
                    validado = validarAutorizacion(usuario, contrasena);
                    if (validado == true) {
                        //$('#respVerificacion').html('Usuario Correcto').css('color','green');
                        alert('Usuario Correcto');
                        if (claseGlobal == 'anular') {
                            claseGlobal == '';
                            anular(iddetalleordencobrogeneral);
                            console.log(iddetalleordencobrogeneral);
                            console.log('anulado');
                            $('#contendorAutorizacion').dialog('close');
                        } else if (claseGlobal == 'deshacerPago') {
                            claseGlobal == '';

                            $('#contenedorDeshacerPago').dialog('open');
                            $('#contendorAutorizacion').dialog('close');

                        } else if (claseGlobal == 'extornar') {
                            claseGlobal == '';
                            $('#contenedorPreguntaExtornado').dialog('open');
                            console.log(iddetalleordencobrogeneral);
                            console.log('extornar');
                            $('#contendorAutorizacion').dialog('close');
                        } else if (claseGlobal == 'modificar') {
                            claseGlobal == '';
                            $('#contenedorModificar').dialog('open');
                            console.log(iddetalleordencobrogeneral);
                            console.log('modificar');
                            $('#contendorAutorizacion').dialog('close');
                        }
                        $('#respVerificacion').html('');
                    } else {
                        $('#respVerificacion').html('El Usuario o la Contraseña es Incorrecto').css('color', 'red');
                    }
                } else {
                    $('#respVerificacion').html('Ingrese Usuario y Contraseña').css('color', 'red');
                }
            }

        },
        close: function () {
            $('#usuario').val('');
            $('#contrasena').val('');
            $('#respVerificacion').html('');


        }
    })

    $('#contenedorRenovado').dialog({
        autoOpen: false,
        modal: true,
        buttons: {
            Aceptar: function () {
                if (confirm('¿Esta Seguro de Renovar la Letra?')) {

                    if (validarRenovado()) {
                        $.ajax({
                            url: '/ingresos/renovar',
                            type: 'post',
                            dataType: 'json',
                            data: {'idordenventa': idordenventa.val(), 'idcliente': idcliente.val(), 'idcobrador': idcobrador.val(), 'monto': montogeneral, 'formacobro': formacobrogeneral, 'iddetalleordencobro': iddetalleordencobrogeneral, 'numeroletra': numeroletrageneral, 'fecha': fecharenovado.val(), 'montoadicional': montorenovado.val(), 'montoporcentaje': montoporcentaje.val(), 'tipoPago': tipoPago.val(), 'dias': diasVencimiento.val(), 'flete': $('#fleteRenovado').val(), 'envioSobre': $('#envioSobreRenovado').val(), 'gastoBancario': $('#gastoBancarioRenovado').val(), 'costoMantenimiento': $('#costoMantenimientoRenovado').val()},
                            success: function (resp) {
                                console.log(resp);
                                console.log('pandemia: ' + resp.errorpandemia)
                                if (resp.errorpandemia == 1) {
                                    alert(resp.msj);
                                }
                                padregeneral.find('.situacion').html('Renovado');
                                padregeneral.find('.pagar').hide();
                                padregeneral.find('.protestar').hide();
                                padregeneral.find('.renovar').hide();
                                cargaDetalleOrdenCobro2();
                                verificarCobro();

                            }
                        });
                        $('#contenedorRenovado').dialog('close');
                        montorenovado.val('');
                        fecharenovado.val('');
                        montoporcentaje.val('');
                        diasVencimiento.val('');
                        $('#fleteRenovado').val('');
                        $('#envioSobreRenovado').val('');
                        $('#gastoBancarioRenovado').val('');
                        $('#costoMantenimientoRenovado').val('');

                    } else {
                        alert('Ingrese correctamente Los valores')
                    }
                }

            }
        },
        close: function () {
            montorenovado.val('');
            fecharenovado.val('');
            montoporcentaje.val('');
            diasVencimiento.val('');
            $('#fleteRenovado').val('');
            $('#envioSobreRenovado').val('');
            $('#gastoBancarioRenovado').val('');
            $('#costoMantenimientoRenovado').val('');
            padregeneral.removeClass();
        }
    });
    $('#nrorecibo').keyup(function () {
        nrorecibo = $(this).val();
        res = verificarrecibo(nrorecibo);

    });
    function validarRenovado() {
        console.log(valorLetra);
        valor = $('#tipoPago').val();
        minimo = 0;
        maximo = 0;
        if (valor == 1) {
            minimo = 1;
            maximo = 100;
        } else if (valor == 2) {
            minimo = 1;
            maximo = valorLetra;
        }

        montoR = montorenovado.val();
        diasV = diasVencimiento.val();
        montoP = montoporcentaje.val();


        if (montoR >= 0 && diasV > 0 && montoP >= minimo && montoP <= maximo) {

            return true;
        } else {
            console.log('entro');
            return false;

        }
    }
    function validarExtornado() {

        nuevoValor = $('#nuevoTipoPago').val();
        nuevoMontoP = $('#nuevoMontoPorcentaje').val();
        NuevoMaximo = buscarImporteExtornado(iddetalleordencobrogeneral);
        console.log(NuevoMaximo);
        minimo = 0;
        maximo = 0;
        if (nuevoValor == 1) {
            minimo = 1;
            maximo = 100;
        } else if (nuevoValor == 2) {
            minimo = 1;
            maximo = NuevoMaximo;
            console.log(NuevoMaximo);
        }


        if (nuevoMontoP >= minimo && nuevoMontoP <= maximo) {

            return true;
        } else {
            console.log('entro falso');
            return false;

        }
    }

    function validarProtestado() {
        if ($('#idPandemiaProtesto').is(':checked')) {
            if (montoadicional.val() >= 0 && lblDias.val() > 0) {
                return true;
            } else {
                return false;
            }
        } else {
            if (montoadicional.val() > 0 && lblDias.val() > 0) {
                return true;
            } else {
                return false;
            }
        }
    }

    function validarMaximoPagoacuenta() {
        pagoAcuenta = $('#pagoAcuenta').val();
        if (pagoAcuenta < valorLetra) {
            return true;
        } else {
            return false;
        }
    }
    function validarVariasLetras() {
        if ($('#montoadicional_2').val() > 0 && $('#condicionLetra').val() > 0) {
            return true;
        } else {
            return false;
        }
    }

    function pagar() {
        temporalIngresosIds = "";
        if($('#chkListaIngresos').prop('checked')) {
            $('.classIngresos').each(function () {
                if ($(this).prop('checked')) {
                    temporalIngresosIds += $(this).val() + ';';
                }
            });
            temporalIngresosIds += "-1";
        }
        monto = padregeneral.find('.importe').html();
        formacobro = padregeneral.find('.formacobro').val();
        iddetalleordencobro = padregeneral.find('.iddetalleordencobro').val();
        numeroletra = padregeneral.find('.lblletra').html();
        valorMonto = padregeneral.find('.valorLetra').val();
        var idcobradoraenviar = idcobrador.val();
        var banderapagar = 1;
        if ($('#lstcobrador').val() == '') {
            var idcobradortemp = verificarNumerounico(iddetalleordencobro);
            if (idcobradortemp == 0) {
                banderapagar = 0;
                $('#lstcobrador').focus();
                $('#respRecibo').html('No se puede asignar un cobrador por defecto ya que el cobro a pagar no cuenta con un numero unico válido').css('color', 'red');
            } else {
                idcobradoraenviar = idcobradortemp;
            }
        }
   
        if (banderapagar == 1) {
            if (confirm("¿Esta Seguro de Pagar la Letra?")) {
                $.ajax({
                    url: '/ingresos/pago',
                    type: 'post',
                    dataType: 'html',
                    data: {'idsIngresos': temporalIngresosIds, 'idordenventa': idordenventa.val(), 'idcliente': idcliente.val(), 'idcobrador': idcobradoraenviar, 'monto': monto, 'formacobro': formacobro, 'iddetalleordencobro': iddetalleordencobro, 'numeroletra': numeroletra, 'numerorecibo': $('#numeroRecibo').val(), 'fechapago': $('#fechaPago').val(), 'observacionesrecibo': $('#idobservacionrecibo').val()},
                    success: function (resp) {
                        console.log(resp);
                        
                        padregeneral.find('.situacion').html('Cancelado');
                        padregeneral.find('.pagar').hide();
                        padregeneral.find('.protestar').hide();
                        padregeneral.find('.renovar').hide();

                        verificarCobro();
                        actualizaPago(valorMonto);
                        cargaDetalleOrdenCobro2();
                        $('#contenedorPago').dialog('close');
                    }
                });
            }
        }
        //console.log()		
    }
    $('#txtOrdenVenta2').focus();
});


function buscaOrdenCobro() {
    var ordenVenta = $('#txtIdOrden').val()?$('#txtIdOrden').val():0;
    var ruta = "/ordencobro/buscarxOrdenVenta/" + ordenVenta;
    $.getJSON(ruta, function (data) {
        $('#razonsocial').val(data.razonsocial);
        $('#idcliente').val(data.idcliente);
        $('#ruc').val(data.ruc);
        $('#codigo').val(data.codcliente);
        $('#codantiguo').val(data.codantiguo);
        $('#codigov').val(data.codigov);
        $('.inline-block input').exactWidth();
    });
}

function cargaSumaIngresos_seleccion(ordenVenta) {
    var temporalDOC = padregeneral.find('.iddetalleordencobro').val();
    $('#tblIngreso').html('<tr><td>Cargando...</td></tr>');
    var ruta = "/ingresos/IngresosxOrdenventa_cajabanco/" + ordenVenta;    
    $.ajax({
        url: ruta,
        type: 'post',
        data: {'id': ordenVenta,
               'iddoc': temporalDOC},
        success: function (resp) {
            $('#tblIngreso').html(resp);
        }
    });
}

function cargaDetalleOrdenCobro() {
    var ordenVenta = $('#txtIdOrden').val();
    var ruta = "/ordencobro/buscarDetalleOrdenCobro/" + ordenVenta;
    $.post(ruta, function (data) {
        $('#tblDetalleOrdenCobro tbody').html(data);
    });
}
function cargaDetalleOrdenCobro2() {
    var ordenVenta = $('#txtIdOrden').val();
    if(ordenVenta){
        var ruta = "/ordencobro/buscarDetalleOrdenCobro2/" + ordenVenta;
        $.post(ruta, function (data) {
            $('#tblDetalleOrdenCobro tbody').html(data);
        });
    }else{
        $('#tblDetalleOrdenCobro tbody').html('');
    }
}

function verificarrecibo(nrorecibo) {
    var valorVerificacion;
    $.ajax({
        url: '/ingresos/verificarrecibo',
        type: 'post',
        dataType: 'json',
        data: {'nrorecibo': nrorecibo},
        success: function (resp) {

            if (!resp.verificacion && $('#nrorecibo').val() != '') {
                $('#Registrar').attr('disabled', 'disabled');
                $('#Registrar').css('background', 'red');
                $('#respuesta').html('Numero de Codigo ya Exite').css('color', 'red');
            } else {
                $('#Registrar').removeAttr('disabled');
                $('#Registrar').css('background', '#0693DE');
                $('#Registrar').css('background-image', '-webkit-linear-gradient(bottom, #71CBFB 0%, #0693DE)');
                $('#respuesta').html('Codigo Correcto').css('color', 'blue');
            }
            if ($('#nrorecibo').val() == '') {
                $('#respuesta').html('').css('color', 'blue');
            }
        }
    });
}

function verificarMontoAcumulado () {
    if($('#chkListaIngresos').prop('checked')) {
        var acumulado = parseFloat($('#idBlockAcumulado').html());
        var total = parseFloat($('#idBlockSaldo').html());
        if (total-0.1 > acumulado) {
            return false;
        }
    }
    return true;
}

function verificarreciboDietario(nrorecibo) {
    var valorVerificacion;
    $.ajax({
        url: '/ingresos/verificarrecibo',
        type: 'post',
        dataType: 'json',
        async: false,
        data: {'nrorecibo': nrorecibo},
        success: function (resp) {

            valorVerificacion = resp.verificacion;
        }
    });
    return valorVerificacion;
}

function verificarNumerounico(iddetalleordencobro) {
    var valorIdcobrador = 0;
    $.ajax({
        url: '/ingresos/verificarnumerounico',
        type: 'post',
        dataType: 'json',
        async: false,
        data: {'iddetalleordencobro': iddetalleordencobro},
        success: function (resp) {
            valorIdcobrador = resp.idcobrador;
        }
    });
    return valorIdcobrador;
}


function imprimircliente() {

    $('#imprimirCliente').html(
            '<fieldset>' +
            '<legend>Datos del Cliente</legend>' +
            'Código:<input type="text" value="' + $('#codigo').val() + '"  >' +
            'Razon Social:<input type="text"    size="40" value="' + $('#razonsocial').val() + '">' +
            'Número de RUC:<input type="text"  value="' + $('#ruc').val() + '">' +
            'Codigo Dakkar:<input type="text" value="' + $('#codantiguo').val() + '">' +
            'Nombre del Cobrador:<input type="text" value="CELESTIUM">' +
            '' +
            '</fieldset>'
            );
}

function verificarCobro() {
    idOrdenVenta = $('#txtIdOrden').val();
    $.ajax({
        url: '/ordencobro/verificarCobro',
        type: 'post',
        dataType: 'html',
        data: {'idOrdenVenta': idOrdenVenta},
        success: function (resp) {

            console.log(resp);
        }
    });
}
function actualizaPago(valorMonto) {
    idOrdenVenta = $('#txtIdOrden').val();
    console.log(idOrdenVenta);
    console.log(valorMonto);
    $.ajax({
        url: '/ordenventa/actualizaPago',
        type: 'post',
        dataType: 'html',
        data: {'idordenventa': idOrdenVenta, 'valorMonto': valorMonto},
        success: function (resp) {

            console.log(resp);
        }
    });
}
function descuentaPago(valorMonto) {
    idOrdenVenta = $('#txtIdOrden').val();
    console.log(idOrdenVenta);
    console.log(valorMonto);
    $.ajax({
        url: '/ordenventa/descuentaPago',
        type: 'post',
        dataType: 'html',
        data: {'idordenventa': idOrdenVenta, 'valorMonto': valorMonto},
        success: function (resp) {

            console.log(resp);
        }
    });
}
function extornaxPago(iddetalleordencobro) {
    idorden = $('#txtIdOrden').val();
    $.ajax({
        url: '/ingresos/extornar',
        type: 'post',
        dataType: 'html',
        data: {'iddetalleordencobro': iddetalleordencobro, 'idordenventa': idorden},
        success: function (resp) {

            console.log(resp);
            cargaDetalleOrdenCobro2();
            verificarCobro();
        }
    });
}
function extornaxPorcentajeMonto(iddetalleordencobro, montoporcentaje, tipocobro) {
    idorden = $('#txtIdOrden').val();
    $.ajax({
        url: '/ingresos/extornar',
        type: 'post',
        dataType: 'html',
        data: {'iddetalleordencobro': iddetalleordencobro, 'montoporcentaje': montoporcentaje, 'tipocobro': tipocobro, 'idordenventa': idorden},
        success: function (resp) {

            console.log(resp);
            cargaDetalleOrdenCobro2();
            verificarCobro();
            $('#contenedorExtornado').dialog('close');
        }
    });
}

function buscarImporteExtornado(iddetalleordencobro) {
    var valorNuevo = 0;

    $.ajax({
        url: '/ordencobro/buscarImporteExtornado',
        type: 'post',
        dataType: 'json',
        async: false,
        data: {'iddetalleordencobro': iddetalleordencobro},
        success: function (resp) {

            console.log(resp);
            valorNuevo = resp.importe;
            console.log(valorNuevo);
        },
        error: function (error) {

        }
    });
    return valorNuevo;
}
function deshacerPago(iddetalleordencobro, valorLetra, motivo) {

    $.ajax({
        url: '/ingresos/deshacerPago',
        type: 'post',
        dataType: 'html',
        data: {'iddetalleordencobro': iddetalleordencobro, 'motivo': motivo},
        success: function (resp) {
            console.log(resp);
            $('#contenedorDeshacerPago').dialog('close');
            descuentaPago(valorLetra);
            cargaDetalleOrdenCobro2();
            verificarCobro();
        },
        error: function (error) {

        }
    });

}

function anular(iddetalleordencobro) {
    $.ajax({
        url: '/ingresos/anular',
        type: 'post',
        dataType: 'html',
        data: {'iddetalleordencobro': iddetalleordencobro},
        success: function (resp) {
            console.log(resp);
            cargaDetalleOrdenCobro2();
            verificarCobro();
        },
        error: function (error) {

        }
    });
}

function validarAutorizacion(usuario, contrasena) {
    var retorno = true;
    $.ajax({
        url: '/actor/validaAutorizacion',
        type: 'post',
        async: false,
        dataType: 'json',
        data: {'usuario': usuario, 'contrasena': contrasena},
        success: function (resp) {
            console.log(resp);
            retorno = resp.verificacion;
        },
        error: function (error) {
            console.log('error');
        }
    });
    return retorno;
}
function modificar() {

    $.ajax({
        url: '/ingresos/modificar',
        type: 'post',
        dataType: 'html',
        data: $('#formularioModificar').serialize(),
        success: function (resp) {
            console.log(resp);
            cargaDetalleOrdenCobro2();
            verificarCobro();
            $('#contenedorModificar').dialog('close');
        },
        error: function (error) {
            console.log('error');
        }
    });

}

function validaTotalModificar(valorLetra) {
    valorLe = valorLetra.toFixed(2);
    valorRetorno = true;
    valorMontoLetras = 0;


    if ($('#montoContado').val() != "") {
        valorMontoContado = parseFloat($('#montoContado').val());
    } else {
        valorMontoContado = 0;
    }

    if ($('#montoCredito0').val() != "") {
        valorMontoCredito0 = parseFloat($('#montoCredito0').val());
    } else {
        valorMontoCredito0 = 0;
    }
    if ($('#montoCredito1').val() != "") {
        valorMontoCredito1 = parseFloat($('#montoCredito1').val());
    } else {
        valorMontoCredito1 = 0;
    }
    if ($('#montoCredito2').val() != "") {
        valorMontoCredito2 = parseFloat($('#montoCredito2').val());
    } else {
        valorMontoCredito2 = 0;
    }
    if ($('#montoCredito3').val() != "") {
        valorMontoCredito3 = parseFloat($('#montoCredito3').val());
    } else {
        valorMontoCredito3 = 0;
    }
    if ($('#montoCredito4').val() != "") {
        valorMontoCredito4 = parseFloat($('#montoCredito4').val());
    } else {
        valorMontoCredito4 = 0;
    }

    if ($('#cantidadLetras').val() == 1) {
        if ($('#montoLetra').val() != "") {
            valorMontoLetras = parseFloat($('#montoLetra').val());
        } else {
            valorMontoLetras = 0;
        }


    } else if ($('#cantidadLetras').val() == 2) {
        if ($('#montoLetraNueva').val() != "") {
            valorMontoLetras = parseFloat($('#montoLetraNueva').val());
        } else {
            valorMontoLetras = 0;
        }
    }


    valorTotal = valorMontoContado + valorMontoCredito0 + valorMontoCredito1 + valorMontoCredito2 + valorMontoCredito3 + valorMontoCredito4 + valorMontoLetras;
    console.log(valorTotal);
    if (valorTotal != valorLe) {
        valorRetorno = false;
    }




    return valorRetorno;
}

function limpiarModificar() {
    $('#flete').val('');
    $('#diasFlete').val('');
    $('#envioSobre').val('');
    $('#diasEnvioSobre').val('');
    $('#gastoBancario').val('');
    $('#diasGastoBancario').val('');
    $('#costoMantenimiento').val('');
    $('#diasCostoMantenimiento').val('');
    $('#montoContado').val('');
    $('#montoCredito0').val('');
    $('#diasCredito0').val('');
    $('#montoCredito1').val('');
    $('#diasCredito1').val('');
    $('#montoCredito2').val('');
    $('#diasCredito2').val('');
    $('#montoCredito3').val('');
    $('#diasCredito3').val('');
    $('#montoCredito4').val('');
    $('#diasCredito4').val('');
    $('#montoLetra').val('');
    $('#diasMontoLetra').val('');
    $('#cantidadLetras').val('');
    $('#nuevaLetra').val('');
    $('#valorModificar').html('');
    $('#nuevaFecha').val('');
    $('#idModificar').val('');
    $('#montoLetraNueva').val('');
}
function validarVaciosModificar() {
    var retorno = true;
    if ($('#flete').val() != "" && $('#flete').val() != 0) {
        if ($('#diasFlete').val() != "" && $('#diasFlete').val() != 0) {

        } else {
            retorno = false;
        }
    }
    if ($('#envioSobre').val() != "" && $('#envioSobre').val() != 0) {
        if ($('#diasEnvioSobre').val() != "" && $('#diasEnvioSobre').val() != 0) {

        } else {
            retorno = false;
        }
    }
    if ($('#gastoBancario').val() != "" && $('#gastoBancario').val() != 0) {
        if ($('#diasGastoBancario').val() != "" && $('#diasGastoBancario').val() != 0) {

        } else {
            retorno = false;
        }
    }
    if ($('#costoMantenimiento').val() != "" && $('#costoMantenimiento').val() != 0) {
        if ($('#diasCostoMantenimiento').val() != "" && $('#diasCostoMantenimiento').val() != 0) {

        } else {
            retorno = false;
        }
    }
    if ($('#montoCredito0').val() != "" && $('#montoCredito0').val() != 0) {
        if ($('#diasCredito0').val() != "" && $('#diasCredito0').val() != 0) {

        } else {
            retorno = false;
        }
    }
    if ($('#montoCredito1').val() != "" && $('#montoCredito1').val() != 0) {
        if ($('#diasCredito1').val() != "" && $('#diasCredito1').val() != 0) {

        } else {
            retorno = false;
        }
    }
    if ($('#montoCredito2').val() != "" && $('#montoCredito2').val() != 0) {
        if ($('#diasCredito2').val() != "" && $('#diasCredito2').val() != 0) {

        } else {
            retorno = false;
        }
    }
    if ($('#montoCredito3').val() != "" && $('#montoCredito3').val() != 0) {
        if ($('#diasCredito3').val() != "" && $('#diasCredito3').val() != 0) {

        } else {
            retorno = false;
        }
    }
    if ($('#montoCredito4').val() != "" && $('#montoCredito4').val() != 0) {
        if ($('#diasCredito4').val() != "" && $('#diasCredito4').val() != 0) {

        } else {
            retorno = false;
        }
    }
    if ($('#montoLetra').val() != "" && $('#montoLetra').val() != 0) {
        if ($('#diasMontoLetra').val() != "" && $('#diasMontoLetra').val() != 0) {

        } else {
            retorno = false;
        }
    }
    if ($('#nuevaLetra').val() != "") {
        if ($('#montoLetraNueva').val() != "" && $('#montoLetraNueva').val() != 0) {

        } else {
            retorno = false;
        }
    }

    return retorno;
}