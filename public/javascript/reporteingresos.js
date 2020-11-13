$(document).ready(function () {
    $('#aceptar').click(function (e) {
        e.preventDefault();

        if ($('#simbolo').val() != "") {

            if ($('#monto').val() != "") {
                cargarIngresos();
                encabezadoReporte();
            } else {
                alert('Ingrese un Monto');
                $('#monto').focus();
            }

        } else {
            cargarIngresos();
            encabezadoReporte();
            if ($('#idOrdenVenta').val() != "") {
                $('#tblOrden').show();
            }
        }
    });
    $('#aceptarContabilidad').click(function (e) {
        e.preventDefault();

        if ($('#simbolo').val() != "") {

            if ($('#monto').val() != "") {
                cargarIngresosContabilidad();
                encabezadoReporte();
            } else {
                alert('Ingrese un Monto');
                $('#monto').focus();
            }

        } else {
            cargarIngresosContabilidad();
            encabezadoReporte();
            if ($('#idOrdenVenta').val() != "") {
                $('#tblOrden').show();
            }
        }
    });


    $('#aceptarExcel').click(function (e) {
        e.preventDefault();
        if ($('#simbolo').val() != "") {
            if ($('#monto').val() != "") {
                cargarIngresosExcel();
//				encabezadoReporte();	
            } else {
                alert('Ingrese un Monto');
                $('#monto').focus();
            }
        } else {
            cargarIngresosExcel();
//			encabezadoReporte();
            if ($('#idOrdenVenta').val() != "") {
                $('#tblOrden').show();
            }
        }
    });

    $('#idTipoCobro').change(function () {
        $('#nroRecibo').val('');
        $('#textNroRecibo').html('');
        if ($(this).val() == '1' || $(this).val() == '3') {
            $('#cmbTipo').show();
        } else {
            $('#cmbTipo').val('');
            $('#cmbTipo').hide();
        }
        if ($(this).val() == '') {
            $('#nroRecibo').hide();
            $('#textNroRecibo').hide();
        } else {
            $('#nroRecibo').show();
            $('#textNroRecibo').show();
            if ($(this).val() == '1' || $(this).val() == '8' || $(this).val() == '9' || $(this).val() == '11') {
                $('#textNroRecibo').html('N° Recibo');
            } else if ($(this).val() == '3' || $(this).val() == '4') {
                $('#textNroRecibo').html('N° Deposito');
            } else if ($(this).val() == '2') {
                $('#textNroRecibo').html('N° Cheque');
            } else {
                $('#textNroRecibo').html('N° Recibo');
            }
        }
    });


    $('#imprimir').click(function (e) {
        e.preventDefault();
        imprSelec('contenedorImpresion');
    });

    $('#limpiar').click(function (e) {
        $('#idOrdenVenta').val('');
        $('#idCliente').val('');
        $('#idCobrador').val('');
        $('#tblingresos tbody').html('');
        $('#monto').attr('readonly', 'readonly');
        $('#txtFechaInicio').html('');
        $('#txtFechaFinal').html('');
        $('#lblCliente').html('');
        $('#lblCobrador').html("");
        $('#lblOrdenVenta').html("");
        $('#lblRecibo').html("");
        $('#lblTipoIngreso').html("");
        $('#lblMonto').html('');
        $('#tblOrden').hide();
    });

    $('#simbolo').change(function () {
        if ($(this).val() == "") {
            $('#monto').attr('readonly', 'readonly').val('');
        } else {
            $('#monto').removeAttr('readonly').focus();
        }
    });

    $('#txtCliente').autocomplete({
        source: "/cliente/autocomplete2/",
        select: function (event, ui) {
            $('#idCliente').val(ui.item.id);

        }});

    $('#txtOrdenVenta').autocomplete({
        source: "/ordenventa/PendientesxPagar/",
        select: function (event, ui) {
            $('#idOrdenVenta').val(ui.item.id);
            cargaOrden(ui.item.id);
        }
    });

    $('#txtCobrador').autocomplete({
        source: "/cobrador/autocompletecobrador/",
        select: function (event, ui) {
            $('#idCobrador').val(ui.item.id);
        }
    });

    $('#ranking').click(function () {
        if ($('#fechaInicio').val().length == 0) {
            $('#fechaInicio').focus();
            return false;
        } else if ($('#fechaFinal').val().length == 0) {
            $('#fechaFinal').focus();
            return false;
        } else if ($('#simbolo').val() != '' && $('#monto').val().length == 0) {
            $('#monto').focus();
            return false;
        } else {
            return true;
        }

    });


});

function cargarIngresosExcel() {
    variables = $('#frmingresos').serialize();
    location.href = '/excel/cargaIngresos?' + variables;
}

function cargarIngresosContabilidad() {
    //$('#frmingresos')
    $('#tblingresosContabilidad tbody').html('');
    $('#tblingresos').hide();
    $('#tblingresosContabilidad').show();
    $.ajax({
        url: '/ingresos/cargaIngresosContabilidad',
        type: 'post',
        dataType: 'html',
        data: $('#frmingresos').serialize(),
        success: function (resp) {
            //console.log($('#frmingresos').serialize());
            $('#tblingresosContabilidad tbody').html(resp);
        },
        error: function (error) {

        }
    });
}

function cargarIngresos() {
    //$('#frmingresos')
    $('#tblingresos tbody').html('');
    $('#tblingresos').show();
    $('#tblingresosContabilidad').hide();
    $.ajax({
        url: '/ingresos/cargaIngresos',
        type: 'post',
        dataType: 'html',
        data: $('#frmingresos').serialize(),
        success: function (resp) {
            //console.log($('#frmingresos').serialize());
            $('#tblingresos tbody').html(resp);
        },
        error: function (error) {

        }
    });
}

function encabezadoReporte() {
    if ($('#fechaInicio').val() == "") {
        $('#txtFechaInicio').html('');
    } else {
        $('#txtFechaInicio').html($('#fechaInicio').val());

    }
    if ($('#fechaFinal').val() == "") {
        $('#txtFechaFinal').html('');
    } else {
        $('#txtFechaFinal').html($('#fechaFinal').val());
    }
    if ($('#txtCliente').val() == "") {
        $('#lblCliente').html('Todos');
    } else {
        $('#lblCliente').html($('#txtCliente').val());
    }
    if ($('#txtCobrador').val() == "") {
        $('#lblCobrador').html("Todos");
    } else {
        $('#lblCobrador').html($('#txtCobrador').val());
    }
    if ($('#txtOrdenVenta').val() == "") {
        $('#lblOrdenVenta').html("Todos");
    } else {
        $('#lblOrdenVenta').html($('#txtOrdenVenta').val());
    }
    if ($('#nroRecibo').val() == "") {
        $('#lblRecibo').html("Todos");
    } else {
        $('#lblRecibo').html($('#nroRecibo').val());
    }
    if ($('#idTipoCobro').val() == "") {
        $('#lblTipoIngreso').html("Todos");
        $('#textRecibolbl').html('N° Recibo');
    } else {
        $('#textRecibolbl').html($('#textNroRecibo').html());
        $('#lblTipoIngreso').html($('#idTipoCobro option:selected').html());
    }
    if ($('#simbolo').val() != "") {

        $('#lblMonto').html($('#simbolo option:selected').html() + ' ' + $('#monto').val());

    } else {
        $('#lblMonto').html('Todos');
    }
}

function cargaOrden(idOrdenVenta) {
    $.ajax({
        url: '/ordenventa/cargaOrden',
        type: 'post',
        dataType: 'html',
        data: {'idordenventa': idOrdenVenta},
        success: function (resp) {

            $('#tblOrden tbody').html(resp);
        },
        error: function (error) {

        }
    });

}