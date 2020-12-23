$(document).ready(function () {
    // Llamadas a funciones
    var click = 0;
    $('.deposito').hide().removeAttr('required');
    $('.cheque').hide().removeAttr('required');
    $('.efectivo').hide().removeAttr('required');
    $('.notacredito').hide().removeAttr('required');

    $('#txtOrdenVenta').autocomplete({
        source: "/ordenventa/PendientesxPagar/",
        select: function (event, ui) {
            $('#txtIdOrden').val(ui.item.id);
            $('#idOrdenVenta').val(ui.item.id);
            buscaOrdenCobro();
            cargaDetalleOrdenCobro();
        }
    });

    $('#txtOrdenVentaxId').autocomplete({
        source: "/ordenventa/PendientesxPagar/",
        select: function (event, ui) {
            $('#txtIdOrdenVenta').val(ui.item.id);
        }
    });

    $('#lstbancocheque').change(function () {
        tipocobro = $('#tipocobro').attr("value");
        nrorecibo = $('#nrorecibo').attr("value");
        nrodoc = $('#nrodoc').attr("value");
        lstbanco = $('#lstbanco').attr("value");
        lstbancocheque = $('#lstbancocheque').attr("value");

        nrooperacion = $('#nrooperacion').attr("value");
//		res=verificarrecibo(nrorecibo);
        condBusqueda = 0;
        if (tipocobro == 1 || tipocobro == 8 || tipocobro == 9 || tipocobro == 11 || tipocobro == 12) {// validar el nrorecibo segun tipocobro
            condBusqueda = 1;
        }
        if (tipocobro == 3 || tipocobro == 4 || tipocobro == 13) {// validar el nrooperacion y el banco segun tipocobro
            condBusqueda = 2;
        }
        if (tipocobro == 2) {// validar el nrodoc y el banco segun tipocobro 
            condBusqueda = 3;
        }
        validaRegistroIngresos(tipocobro, nrorecibo, nrodoc, lstbanco, lstbancocheque, nrooperacion, condBusqueda);
    });
    $('#lstbanco').change(function () {
        tipocobro = $('#tipocobro').attr("value");
        nrorecibo = $('#nrorecibo').attr("value");
        nrodoc = $('#nrodoc').attr("value");
        lstbanco = $('#lstbanco').attr("value");
        lstbancocheque = $('#lstbancocheque').attr("value");

        nrooperacion = $('#nrooperacion').attr("value");
//		res=verificarrecibo(nrorecibo);
        condBusqueda = 0;
        if (tipocobro == 1 || tipocobro == 8 || tipocobro == 9 || tipocobro == 11 || tipocobro == 12) {// validar el nrorecibo segun tipocobro
            condBusqueda = 1;
        }
        if (tipocobro == 3 || tipocobro == 4 || tipocobro == 13) {// validar el nrooperacion y el banco segun tipocobro
            condBusqueda = 2;
        }
        if (tipocobro == 2) {// validar el nrodoc y el banco segun tipocobro 
            condBusqueda = 3;
        }
        validaRegistroIngresos(tipocobro, nrorecibo, nrodoc, lstbanco, lstbancocheque, nrooperacion, condBusqueda);
    });

    $('#tipocobro').change(function () {
        tipocobro = $('#tipocobro').attr("value");
        nrorecibo = $('#nrorecibo').attr("value");
        nrodoc = $('#nrodoc').attr("value");
        lstbanco = $('#lstbanco').attr("value");
        lstbancocheque = $('#lstbancocheque').attr("value");

        nrooperacion = $('#nrooperacion').attr("value");
//		res=verificarrecibo(nrorecibo);
        condBusqueda = 0;
        if (tipocobro == 1 || tipocobro == 3) {
            $('#cmbTipo').show();
        } else {
            $('#cmbTipo').hide();
            $('#cmbTipo').val('');
        }
        if (tipocobro == 1 || tipocobro == 8 || tipocobro == 9 || tipocobro == 11 || tipocobro == 12) {// validar el nrorecibo segun tipocobro
            condBusqueda = 1;
        }
        if (tipocobro == 3 || tipocobro == 4 || tipocobro == 13) {// validar el nrooperacion y el banco segun tipocobro
            condBusqueda = 2;
        }
        if (tipocobro == 2) {// validar el nrodoc y el banco segun tipocobro 
            condBusqueda = 3;
        }

        validaRegistroIngresos(tipocobro, nrorecibo, nrodoc, lstbanco, lstbancocheque, nrooperacion, condBusqueda);
    });

    $('#nrodoc').keyup(function () {
        tipocobro = $('#tipocobro').attr("value");
        nrorecibo = $('#nrorecibo').attr("value");
        nrodoc = $('#nrodoc').attr("value");
        lstbanco = $('#lstbanco').attr("value");
        lstbancocheque = $('#lstbancocheque').attr("value");

        nrooperacion = $('#nrooperacion').attr("value");
//		res=verificarrecibo(nrorecibo);
        condBusqueda = 0;
        if (tipocobro == 1 || tipocobro == 8 || tipocobro == 9 || tipocobro == 11 || tipocobro == 12) {// validar el nrorecibo segun tipocobro
            condBusqueda = 1;
        }
        if (tipocobro == 3 || tipocobro == 4 || tipocobro == 13) {// validar el nrooperacion y el banco segun tipocobro
            condBusqueda = 2;
        }
        if (tipocobro == 2) {// validar el nrodoc y el banco segun tipocobro 
            condBusqueda = 3;
        }
        validaRegistroIngresos(tipocobro, nrorecibo, nrodoc, lstbanco, lstbancocheque, nrooperacion, condBusqueda);
    });

    $('#nrorecibo').keyup(function () {
        tipocobro = $('#tipocobro').attr("value");
        if (tipocobro != 2) {
            nrorecibo = $('#nrorecibo').attr("value");
            nrodoc = $('#nrodoc').attr("value");
            lstbanco = $('#lstbanco').attr("value");
            lstbancocheque = $('#lstbancocheque').attr("value");
            nrooperacion = $('#nrooperacion').attr("value");
//		res=verificarrecibo(nrorecibo);
            condBusqueda = 0;
            if (tipocobro == 1 || tipocobro == 8 || tipocobro == 9 || tipocobro == 11 || tipocobro == 12) {// validar el nrorecibo segun tipocobro
                condBusqueda = 1;
            }
            if (tipocobro == 3 || tipocobro == 4 || tipocobro == 13) {// validar el nrooperacion y el banco segun tipocobro
                condBusqueda = 2;
            }
            if (tipocobro == 2) {// validar el nrodoc y el banco segun tipocobro 
                condBusqueda = 3;
            }
            validaRegistroIngresos(tipocobro, nrorecibo, nrodoc, lstbanco, lstbancocheque, nrooperacion, condBusqueda);
        }
    });
    $('#nrooperacion').keyup(function () {
        tipocobro = $('#tipocobro').attr("value");
        nrorecibo = $('#nrorecibo').attr("value");
        nrodoc = $('#nrodoc').attr("value");
        lstbanco = $('#lstbanco').attr("value");
        lstbancocheque = $('#lstbancocheque').attr("value");

        nrooperacion = $('#nrooperacion').attr("value");
//		res=verificarrecibo(nrorecibo);
        condBusqueda = 0;
        if (tipocobro == 1 || tipocobro == 8 || tipocobro == 9 || tipocobro == 11 || tipocobro == 12) {// validar el nrorecibo segun tipocobro
            condBusqueda = 1;
        }
        if (tipocobro == 3 || tipocobro == 4 || tipocobro == 13) {// validar el nrooperacion y el banco segun tipocobro
            condBusqueda = 2;
        }
        if (tipocobro == 2) {// validar el nrodoc y el banco segun tipocobro 
            condBusqueda = 3;
        }
        validaRegistroIngresos(tipocobro, nrorecibo, nrodoc, lstbanco, lstbancocheque, nrooperacion, condBusqueda);
    });

    $('#lstbanco').change(function () {
        idbanco = $(this).val();
        listaCta_banco(idbanco);
    });
    
    $('#tipocobro').change(function () {
        valor = $(this).val();
        if (valor == 1) {
            $('.notacredito').hide().removeAttr('required');
            $('.deposito').hide().removeAttr('required');
            $('.cheque').hide().removeAttr('required');
            $('.efectivo').show().attr('required', 'required');
            $('#esvalidado').val('1');
        } else if (valor == 2) {
            $('.notacredito').hide().removeAttr('required');
            $('.efectivo').hide().removeAttr('required');
            $('.deposito').hide().removeAttr('required');
            $('.cheque').show().attr('required', 'required');
            $('#esvalidado').val('0');
        } else if (valor == 3 || valor == 4 || valor == 13) {
            $('.notacredito').hide().removeAttr('required');
            $('.efectivo').hide().removeAttr('required');
            $('.cheque').hide().removeAttr('required');
            $('.deposito').show().attr('required', 'required');
            $('#esvalidado').val('1');
        } else if (valor == "") {
            $('.notacredito').hide().attr('required', 'required');
            $('.cheque').hide().attr('required', 'required');
            $('.deposito').hide().attr('required', 'required');
            $('.efectivo').hide().attr('required', 'required');
            $('#esvalidado').val('1');
        } else if (valor == 10) {
            $('.cheque').hide().removeAttr('required');
            $('.deposito').hide().removeAttr('required');
            $('.efectivo').show().attr('required', 'required');
            $('.notacredito').show().attr('required', 'required');
            $('#esvalidado').val('1');
        } else {
            $('.notacredito').hide().removeAttr('required');
            $('.cheque').hide().removeAttr('required');
            $('.deposito').hide().removeAttr('required');
            $('.efectivo').show().attr('required', 'required');
            $('#esvalidado').val('1');
        }
    });

    $('#Registrar').click(function (e) {
        if (click == 1) {
            $(this).attr('disabled', 'disabled');
        } else if ($('#idOrdenVenta').val() == "") {
            e.preventDefault();
            $('#respGeneral').html('Seleccione una Orden de Venta').css('color', 'red');
        } else if ($('#fechapago').val() == "") {
            e.preventDefault();
            $('#respGeneral').html('Ingrese la fecha de Pago').css('color', 'red');
        } else {
            $('#respGeneral').html('');
            click++;
        }
    });
    $('#txtOrdenVenta').focus();
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

function cargaDetalleOrdenCobro() {
    var ordenVenta = $('#txtIdOrden').val();
    if(ordenVenta){
        var ruta = "/ordencobro/buscarDetalleOrdenCobro/" + ordenVenta;
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


function validaRegistroIngresos(tipocobro, nrorecibo, nrodoc, lstbanco, lstbancocheque, nrooperacion, condBusqueda) {
    $('#Registrar').removeAttr('disabled');
    $('#Registrar').css('background', '#0693DE');
    $('#Registrar').css('background-image', '-webkit-linear-gradient(bottom, #71CBFB 0%, #0693DE)');
    $('#respuesta123').html('Codigo Correcto').css('color', 'blue');
    tipocobrotext = $("#tipocobro option:selected").text();
    $.ajax({
        async: false,
        url: '/ingresos/validaRegistroIngresos',
        type: 'get',
        dataType: 'json',
        data: {'tipocobro': tipocobro, 'nrorecibo': nrorecibo, 'nrodoc': nrodoc, 'lstbanco': lstbanco, 'lstbancocheque': lstbancocheque, 'nrooperacion': nrooperacion, 'condBusqueda': condBusqueda},
        success: function (resp) {
            if (resp.duplicado == 1) {

                if (condBusqueda == 1 || condBusqueda == 12) {// validar el nrorecibo segun tipocobro
                    if (tipocobro == 12) {
                        mensaje = "El recibo # " + resp.nrorecibo + " en el tipo de cobro efectivo o efectivo LCE ya existe en la " + resp.codigov;
                    } else {
                        mensaje = "El recibo # " + resp.nrorecibo + " en el tipo de cobro ''" + tipocobrotext + "'' ya existe en la " + resp.codigov;
                    }
                    
                }
                if (condBusqueda == 2) {// validar el nrooperacion y el banco segun tipocobro
                    mensaje = "El deposito # " + resp.nrooperacion + " en el banco " + resp.banco + " ya existe en la " + resp.codigov;
                }
                if (condBusqueda == 3 || condBusqueda == 13) {// validar el nrodoc y el banco segun tipocobro 
                    mensaje = "El cheque # " + resp.nrodoc + " en el banco " + resp.bancocheque + " ya existe en la " + resp.codigov;
                }
                $('#Registrar').attr('disabled', 'disabled');
                $('#Registrar').css('background', 'red');
                $('#respuesta123').html(mensaje).css('color', 'red');
                if (resp.nrorecibo == 0 && resp.nrooperacion == 0 && resp.nrodoc == 0) {
                    $('#Registrar').removeAttr('disabled');
                    $('#Registrar').css('background', '#0693DE');
                    $('#Registrar').css('background-image', '-webkit-linear-gradient(bottom, #71CBFB 0%, #0693DE)');
                    $('#respuesta123').html('Codigo Correcto').css('color', 'blue');
                }
            } else {
                $('#Registrar').removeAttr('disabled');
                $('#Registrar').css('background', '#0693DE');
                $('#Registrar').css('background-image', '-webkit-linear-gradient(bottom, #71CBFB 0%, #0693DE)');
                $('#respuesta123').html('Codigo Correcto').css('color', 'blue');
            }
        }
    });
    if (nrorecibo == "" && nrooperacion == "" && nrodoc == "") {
        $('#respuesta123').empty();
    }
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

function listaCta_banco(idbanco) {
    $.ajax({
        url: '/cta_banco/listaCta_banco',
        type: 'post',
        dataType: 'html',
        data: {'idbanco': idbanco},
        success: function (resp) {
            console.log(resp);
            $('#lstCtaCorriente').html(resp);
        }
    });
}