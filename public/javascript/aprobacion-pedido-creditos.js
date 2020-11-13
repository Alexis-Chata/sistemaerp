$(document).ready(function () {
    var idordenventa = 0;
    $('#tablaMostrar').hide();
    var contadorA = 0;
    if ($('#tblOrdenes tbody tr').length == 0) {
        $('#divPedidos').hide();
        $('#tablaMostrar').show();
    }
    $('#tblProductosGuia tr td:last').hide();
    $('#liCondicionLetra').hide();
    $('#liMontoContado').hide();
    $('#liTipoLetra').hide();
    $('#liCreditoDias').hide();
    $('#liDocumento').hide();
    $('#liMontoCredito').hide();
    $('#liMontoLetras').hide();

    $('.btnVerDetalle').click(function (e) {
        e.preventDefault();
        $('#tblOrdenes tr').removeClass();
        $(this).parents('tr').addClass('active-row');
        var url1 = "/ordenventa/buscarfactura/" + $(this).parents('tr').find('.txtIdOrdenVenta').val();
        var url2 = $(this).attr("href");
        limpiar();
        idordenventa = $(this).parents('tr').find('.txtIdOrdenVenta').val();
        cargaDatosOrden(url1);
        cargaDetalleOrdenVenta(url2);
    });
    
    $('#lstCondicionLetra').change(function () {
        $('#lacliente').html('');
        var condicion = $('#lstCondicionLetra option:selected').text();
        var Condletras = condicion.split("/");
        var cant = Condletras.length - 3;
        if (cant<=0) {
            $('#liLAletra').attr('style', 'display: none');
        } else {
            var selected = "";
            $('#liLAletra').removeAttr('style');
            for (var i = 0; i <= cant; i++) {
                selected = "";
                if (i == cant) selected = " selected";
                $('#lacliente').append('<option value="' + i + '"' + selected +'>' + i + '</option>');
            }
        }
        
    });
    
    $('#btnretornar').click(function (e) {
        e.preventDefault();
        if (confirm('¿Esta seguro de Retornar  el Peido a  ventas?')) {

            if (idordenventa != 0) {
                if (contadorA > 0) {
                    $(this).attr('disabled', 'disabled');
                } else {
                    retornarVentas(idordenventa);
                }
                ocultar();
                contadorA++;

            }
        }


    });
    /*Activando condiciones de pedido*/
    $('#btnFormaPago').click(function (e) {
        e.preventDefault();
        $('#lstFormaPago').attr('disabled', false);
    });
    $('#btnCondicionLetra').click(function (e) {
        e.preventDefault();
        $('#lstCondicionLetra').attr('disabled', false);
    });
    $('#btnTipoLetra').click(function (e) {
        e.preventDefault();
        $('#lstTipoLetra').attr('disabled', false);
    });
    /*Intercambio de forma de pago*/
    $('#chkContado, #chkCredito, #chkLetras').on('change', function () {
        if ($(this).is(':checked')) {
            $(this).val('1');
        } else {
            $(this).val('0');
        }
        var contado = ($('#chkContado').is(':checked')) ? '1' : '0';
        var credito = ($('#chkCredito').is(':checked')) ? '1' : '0';
        var letras = ($('#chkLetras').is(':checked')) ? '1' : '0';

        limpiar();
        NoRequired();
        $('#lstDocumento').attr('required', 'required');
        if (contado == 1 && credito == 1 && letras == 1) {
            Required();
            $('#liCreditoDias,.liLetras').show();
            $('#liMontoContado,#liMontoCredito').show();
            $('#liMontoLetras').show();


        } else if (contado == 0 && credito == 0 && letras == 0) {
            $('#liCreditoDias,.liLetras').hide();
            $('#liMontoContado, #liMontoCredito, #liMontoLetras').hide();
            $('#btnAprobar').attr('disabled', 'disabled').attr('title', 'Esta Deshabilitado escoja un tipo de cobro');
        } else if (contado == 1 && credito == 0 && letras == 0) {
            $('#liCreditoDias,.liLetras').hide();
            $('#liMontoContado, #liMontoCredito, #liMontoLetras').hide();

        } else if (contado == 1 && credito == 1 && letras == 0) {
            $('#liMontoContado,#liCreditoDias').show();
            $('.liLetras').hide();
            $('#liMontoCredito, #liMontoLetras').hide();

        } else if (contado == 1 && credito == 0 && letras == 1) {
            $('#liMontoContado,.liLetras').show();
            $('#liCreditoDias').hide();
            $('#liMontoCredito, #liMontoLetras').hide();

        } else if (contado == 0 && credito == 1 && letras == 1) {
            $('.liLetras,#liCreditoDias').show();
            $('#liMontoContado').hide();
            $('#liMontoCredito, #liMontoLetras').show();

        } else if (contado == 0 && credito == 1 && letras == 0) {
            $('#liCreditoDias').show();
            $('.liLetras').hide();
            $('#liMontoContado,#liMontoCredito, #liMontoLetras').hide();

        } else if (contado == 0 && credito == 0 && letras == 1) {
            $('#liCreditoDias').hide();
            $('.liLetras').show();
            $('#liMontoContado,#liMontoContado,#liMontoCredito, #liMontoLetras').hide();

        }

    });
    /*Estado del orden al enviar el formulario*/
    $('#btnAprobar').click(function (e) {
        //e.preventDefault();

        //console.log($('#observacion').val());
        if (confirm('¿Esta seguro de Aprobar el Pedido?')) {
            if (validarSumaMonto() == true) {
                ocultar();
                if (contadorA > 0) {
                    $(this).attr('disabled', 'disabled');
                } else {
                    generaObservacion();
                    validar();
                    $('#lstFormaPago, #lstCondicionLetra, #lstTipoLetra').attr('disabled', false);
                    $('#txtEstadoOrden').val(1);
                }
                contadorA++;

            } else {
                e.preventDefault();
                alert('Ingrese Correctamente los montos');
            }

        } else {
            e.preventDefault();
        }

    });
    $('#btnDesaprobar').click(function (e) {
        if (confirm('¿Esta seguro de Desaprobar el Pedido?')) {
            if (contadorA > 0) {
                $(this).attr('disabled', 'disabled');
            }
            contadorA++;
            ocultar();
            $('#txtEstadoOrden').val(2);
        } else {
            e.preventDefault();
        }

    });

});

function cargaDetalleOrdenVenta(url) {
    $.post(url, function (data) {
        $('#tblProductosGuia tbody').html(data);

    });
}

function cargaDatosOrden(url) {
    $.getJSON(url, function (data) {
        $('#txtIdOrdenVenta').val(data.idordenventa);
        $('#txtCliente').val(data.cliente);
        $('#txtIdCliente').val(data.idcliente);
        $('#txtFechaGuia').val(data.fechaguia);
        $('#txtRucDni').val(data.rucdni);
        $('#txtDireccion').val(data.cdireccion);
        $('#txtTelefono').val(data.ctelefono);
        $('#divCondicionPedido').html(data.observaciones);
        $('#observacion').val(data.observaciones);
        $('#mensajeVentas').val(data.mventas);
        $('#mensajeCobranzas').val(data.mcobranzas);
        $('#mensajeAlmacen').val(data.malmacen);
        $('.important:eq(0)').val(data.codigov + ' - ' + data.fechaguia);

        $('.show:eq(0)').click();
        $('.show:eq(1)').click();
        $('.show:eq(2)').click();
        $('.show:eq(3)').click();

        $('#liDocumento').show();
        cargaPosicion();
        ultimaOrden();
        deudatotal();
        if ($('#divCondicionPedido:contains("Letras")').length > 0) {
            $('#liCondicionLetra').show();
            $('#liTipoLetra').show();
            $('#chkLetras').attr('checked', true).val('1');
        } else {
            $('#liCondicionLetra').hide();
            $('#liTipoLetra').hide();
            $('#chkLetras').attr('checked', false).val('0');
        }
        if (($('#divCondicionPedido:contains("Letras")').length > 0 || $('#divCondicionPedido:contains("Credito")').length > 0) && $('#divCondicionPedido:contains("Contado")').length > 0) {
            $('#liMontoContado').show();
        } else {
            $('#liMontoContado').hide();
        }
        if ($('#divCondicionPedido:contains("Monto al Credito")').length > 0) {
            $('#liMontoCredito').show();
        } else {
            $('#liMontoCredito').hide();
        }
        if ($('#divCondicionPedido:contains("Monto a Letras")').length > 0) {
            $('#liMontoLetras').show();
        } else {
            $('#liMontoLetras').hide();
        }
        if ($('#divCondicionPedido:contains("Credito")').length > 0) {
            $('#liCreditoDias').show();
            $('#chkCredito').attr('checked', true).val('1');
        } else {
            $('#liCreditoDias').hide();
            $('#chkCredito').attr('checked', false).val('0');
        }
        if ($('#divCondicionPedido:contains("Contado")').length > 0) {
            $('#chkContado').attr('checked', true).val('1');
        } else {
            $('#chkContado').attr('checked', false).val('0');
        }
        $('#lstDocumento option[value="' + data.tipoDocumento + '"]').attr('selected', true);
        $('.inline-block input').exactWidth();
    });
}

function NoRequired() {
    $('#txtCreditoDias').removeAttr('required');
    $('#txtMontoContado').removeAttr('required');
    $('#txtMontoCredito').removeAttr('required');
    $('#txtMontoLetras').removeAttr('required');
    $('#lstCondicionLetra').removeAttr('required');
    $('#lstTipoLetra').removeAttr('required');
    $('#btnAprobar').removeAttr('disabled').attr('title', '');
}
function limpiar() {
    $('#txtCreditoDias').val('');
    $('#txtMontoContado').val('');
    $('#txtMontoCredito').val('');
    $('#txtMontoLetras').val('');
    $('#lstCondicionLetra').val('');
    $('#lstTipoLetra').val('');
}
function Required() {
    $('#txtCreditoDias').attr('required', 'required');
    $('#txtMontoContado').attr('required', 'required');
    $('#txtMontoCredito').attr('required', 'required');
    $('#txtMontoLetras').attr('required', 'required');
    $('#lstCondicionLetra').attr('required', 'required');
    $('#lstTipoLetra').attr('required', 'required');
    $('#lstDocumento').attr('required', 'required');
}

function validar() {
    var cotadoLoad = ($('#chkContado').is(':checked')) ? '1' : '0';
    var creditoLoad = ($('#chkCredito').is(':checked')) ? '1' : '0';
    var letrasLoad = ($('#chkLetras').is(':checked')) ? '1' : '0';
    NoRequired();
    $('#lstDocumento').attr('required', 'required');
    if (cotadoLoad == 1 && creditoLoad == 1 && letrasLoad == 1) {
        Required();

    } else if (cotadoLoad == 0 && creditoLoad == 0 && letrasLoad == 0) {

        $('#btnAprobar').attr('disabled', 'disabled').attr('title', 'Esta Deshabilitado escoja un tipo de cobro');
    } else if (cotadoLoad == 1 && creditoLoad == 0 && letrasLoad == 0) {

    } else if (cotadoLoad == 1 && creditoLoad == 1 && letrasLoad == 0) {

        $('#txtCreditoDias').attr('required', 'required');
        $('#txtMontoContado').attr('required', 'required');
    } else if (cotadoLoad == 1 && creditoLoad == 0 && letrasLoad == 1) {

        $('#txtMontoContado').attr('required', 'required');
        $('#lstCondicionLetra').attr('required', 'required');
        $('#lstTipoLetra').attr('required', 'required');

    } else if (cotadoLoad == 0 && creditoLoad == 1 && letrasLoad == 1) {

        $('#txtCreditoDias').attr('required', 'required');
        $('#txtMontoCredito').attr('required', 'required');
        $('#txtMontoLetras').attr('required', 'required');
        $('#lstCondicionLetra').attr('required', 'required');
        $('#lstTipoLetra').attr('required', 'required');

    } else if (cotadoLoad == 0 && creditoLoad == 1 && letrasLoad == 0) {

        $('#txtCreditoDias').attr('required', 'required');
    } else if (cotadoLoad == 0 && creditoLoad == 0 && letrasLoad == 1) {

        $('#lstCondicionLetra').attr('required', 'required');
        $('#lstTipoLetra').attr('required', 'required');
    }
}
function generaObservacion() {
    if ($(this).is(':checked')) {
        $(this).val('1');
    } else {
        $(this).val('0');
    }
    var contado = ($('#chkContado').is(':checked')) ? '1' : '0';
    var credito = ($('#chkCredito').is(':checked')) ? '1' : '0';
    var letras = ($('#chkLetras').is(':checked')) ? '1' : '0';
    var observaciones = "";


    if (contado == 1 && credito == 1 && letras == 1) {
        observaciones = "<ul>" +
                "<li><strong>Forma Pago: </strong>Contado Credito Letras</li>" +
                "<li><strong>Credito Dias: </strong>" + $('#txtCreditoDias').val() + "</li>" +
                "<li><strong>Condicion Letra: </strong>" + $('#lstCondicionLetra option:selected').html() + "</li>" +
                "<li><strong>Tipo Letra: </strong>" + $('#lstTipoLetra option:selected').html() + "</li>" +
                "<li><strong>Monto Contado: </strong>" + $('#txtMontoContado').val() + "</li>" +
                "<li><strong>Monto Credito: </strong>" + $('#txtMontoCredito').val() + "</li>" +
                "<li><strong>Monto Letra: </strong>" + $('#txtMontoLetras').val() + "</li>" +
                "</ul>";

    } else if (contado == 0 && credito == 0 && letras == 0) {
        observaciones = "";
    } else if (contado == 1 && credito == 0 && letras == 0) {
        observaciones = "<ul>" +
                "<li><strong> Forma Pago: </strong>Contado</li>" +
                "</ul>";
    } else if (contado == 1 && credito == 1 && letras == 0) {
        observaciones = "<ul>" +
                "<li><strong> Forma Pago: </strong>Contado Credito</li>" +
                "<li><strong> Credito Dias: </strong>" + $('#txtCreditoDias').val() + "</li>" +
                "<li><strong> Monto Contado: </strong>" + $('#txtMontoContado').val() + "</li>" +
                "</ul>";
    } else if (contado == 1 && credito == 0 && letras == 1) {
        observaciones = "<ul>" +
                "<li><strong> Forma Pago: </strong>Contado Letras</li>" +
                "<li><strong> Condicion Letra: </strong>" + $('#lstCondicionLetra option:selected').html() + "</li>" +
                "<li><strong> Tipo Letra: </strong>" + $('#lstTipoLetra option:selected').html() + "</li>" +
                "<li><strong> Monto Contado: </strong>" + $('#txtMontoContado').val() + "</li>" +
                "</ul>";
    } else if (contado == 0 && credito == 1 && letras == 1) {
        observaciones = "<ul>" +
                "<li><strong> Forma Pago: </strong>Credito Letras</li>" +
                "<li><strong> Credito Dias: </strong>" + $('#txtCreditoDias').val() + "</li>" +
                "<li><strong> Condicion Letra: </strong>" + $('#lstCondicionLetra option:selected').html() + "</li>" +
                "<li><strong> Tipo Letra: </strong>" + $('#lstTipoLetra option:selected').html() + "</li>" +
                "<li><strong> Monto Credito: </strong>" + $('#txtMontoCredito').val() + "</li>" +
                "<li><strong> Monto Letra: </strong>" + $('#txtMontoLetras').val() + "</li>" +
                "</ul>";
    } else if (contado == 0 && credito == 1 && letras == 0) {
        observaciones = "<ul>" +
                "<li><strong> Forma Pago: </strong>Credito</li>" +
                "<li><strong> Credito Dias: </strong>" + $('#txtCreditoDias').val() + "</li>" +
                "</ul>";
    } else if (contado == 0 && credito == 0 && letras == 1) {
        observaciones = "<ul>" +
                "<li><strong> Forma Pago: </strong>Letras</li>" +
                "<li><strong> Condicion Letra: </strong>" + $('#lstCondicionLetra option:selected').html() + "</li>" +
                "<li><strong> Tipo Letra: </strong>" + $('#lstTipoLetra option:selected').html() + "</li>" +
                "</ul>";
    }
    $('#observacion').val(observaciones);
}

function validarSumaMonto() {
    if ($(this).is(':checked')) {
        $(this).val('1');
    } else {
        $(this).val('0');
    }
    var contado = ($('#chkContado').is(':checked')) ? '1' : '0';
    var credito = ($('#chkCredito').is(':checked')) ? '1' : '0';
    var letras = ($('#chkLetras').is(':checked')) ? '1' : '0';
    var observaciones = "";


    var total = parseFloat($('#importetotal').val());
    var vcontado = 0;
    var vcredito = 0;
    var vletra = 0;
    var opcional = 0;
    var validacion = false;
    var suma = 0;
    if (contado == 1 && credito == 1 && letras == 1) {
        if ($('#txtMontoContado').val() != "") {
            vcontado = parseFloat($('#txtMontoContado').val());
        }
        if ($('#txtMontoCredito').val() != "") {
            vcredito = parseFloat($('#txtMontoCredito').val());
        }
        if ($('#txtMontoLetras').val() != "") {
            vletra = parseFloat($('#txtMontoLetras').val());
        }

    } else if (contado == 0 && credito == 0 && letras == 0) {

    } else if (contado == 1 && credito == 0 && letras == 0) {
        opcional = total;
    } else if (contado == 1 && credito == 1 && letras == 0) {
        if ($('#txtMontoContado').val() != "") {
            vcontado = parseFloat($('#txtMontoContado').val());
        }
        if (vcontado != 0) {
            opcional = total - vcontado;
        }

    } else if (contado == 1 && credito == 0 && letras == 1) {
        if ($('#txtMontoContado').val() != "") {
            vcontado = parseFloat($('#txtMontoContado').val());
        }
        if (vcontado != 0) {
            opcional = total - vcontado;
        }
    } else if (contado == 0 && credito == 1 && letras == 1) {
        if ($('#txtMontoCredito').val() != "") {
            vcredito = parseFloat($('#txtMontoCredito').val());
        }
        if ($('#txtMontoLetras').val() != "") {
            vletra = parseFloat($('#txtMontoLetras').val());
        }
    } else if (contado == 0 && credito == 1 && letras == 0) {
        opcional = total;
    } else if (contado == 0 && credito == 0 && letras == 1) {
        opcional = total;
    }
    suma = vcontado + vcredito + vletra + opcional;
    if (suma == total) {
        validacion = true;
    }
    return validacion;

}

function cargaPosicion() {
    var idCliente = $('#txtIdCliente').val();
    var ruta = "/cliente/posicionordenventa/" + idCliente;
    $.post(ruta, function (data) {
        $('#clienteposicion').html(data);
        var saldo = parseFloat($('#idsaldo').val());
        if (saldo <= 0) {
            $.msgbox("Alerta al Crear la ORDEN", "EL CLIENTE TIENE SALDO S./ " + saldo + "<br> Verifique el Saldo que necesita ANTES de empezar a registrar su orden.");
        }

    });
}

function ultimaOrden() {
    var idCliente = $('#txtIdCliente').val();
    var ruta = "/cliente/datosdeudaOrdenVentas/" + idCliente;
    $.post(ruta, function (data) {
        $('#clienteultimaorden').html(data);
    });
}

function deudatotal() {
    var idCliente = $('#txtIdCliente').val();
    var ruta = "/cliente/datosdeudaTotalOrdenVentas/" + idCliente;
    $.post(ruta, function (data) {
        $('#clientedeudatotal').html(data);
    });
}

function retornarVentas(idordenventa) {
    $.ajax({
        url: '/creditos/retornarVentas',
        type: 'post',
        dataType: 'json',
        data: {'idordenventa': idordenventa},
        success: function (resp) {
            console.log(resp);
            if (resp.verificacion == true) {
                location.reload();
            }

        }
    });
}

function ocultar() {
    $('#btnAprobar').hide();
    $('#btnDesaprobar').hide();
    $('#btnretornar').hide();

}