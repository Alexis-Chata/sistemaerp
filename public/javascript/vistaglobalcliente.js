$(document).ready(function () {
    // Llamadas a funciones
    var title = "Detalle";
    var title2 = "Letras Protestadas";
    $('#buscarOrdenes').hide();
    $('#nuevabusqueda').hide();
    $('#txtCliente').autocomplete({
        source: "/cliente/autocomplete2/",
        select: function (event, ui) {
            $('#txtIdCliente').val(ui.item.id);
            $('#razonsocial').val(ui.item.label);
            $('#ruc').val(ui.item.rucdni);
            $('#codigo').val(ui.item.codigocliente);
            $('#codantiguo').val(ui.item.codigoantiguo);
            buscaPosicionCliente();
        }});

    
   $('#txtClienteGlobal').autocomplete({
        source: "/cliente/autocomplete2/",
        select: function (event, ui) {
            $('#txtIdCliente').val(ui.item.id);
            $('#razonsocial').val(ui.item.label);
            $('#ruc').val(ui.item.rucdni);
            $('#codigo').val(ui.item.codigocliente);
            $('#codantiguo').val(ui.item.codigoantiguo);
            $('#buscarOrdenes').show();
            $('#nuevabusqueda').show();
            $('#BCliente').html("Cliente: " + ui.item.label);
            $('#RSCliente').html("Cliente: " + ui.item.label);
            $('#RSCliente').hide();
            $('#tblOrdenVenta tbody').html('<div style="margin-top:2px;text-align:center;" ><img src="/imagenes/cargando.gif"></div>');
            cargaOrdenesVenta()
        
            //listaDataLineacredito();
        }});
    
    $('#btnVerDetalle').click(function (e) {
        idcliente = $('#txtIdCliente').val();
        cargaOrdenesVentaDetalles(idcliente);
    });

    $('.btnVerDetalleOrden').live('click', function (e) {
        title = "Detalle de Programacion de Pagos";
        e.preventDefault();
        $('#tblDetalleOrdenCobro thead').html("<tr><th colspan='9'>" + $(this).parents('tr').find('.codigov').val() + "</th></tr>");
        $('#tblOrdenVenta tr').removeClass();
        $(this).parents('tr').addClass('active-row');
        var url = $(this).attr("href");
        cargaDetalleOrdenVenta(url);
    });
    
    $('.btnDetalleProducto').live('click', function (e) {
        e.preventDefault();
        $('#tblDetalleOrdenCobro thead').html("<tr><th colspan='11'>" + $(this).parents('tr').find('.codigov').val() + "</th></tr>");
        title = "Detalle de la Orden (Productos)";
        $('#tblOrdenVenta tr').removeClass();
        $(this).parents('tr').addClass('active-row');
        var url = $(this).attr("href");
        cargaDetalleOrdenVentaProducto(url);
    });


    $('#nuevabusqueda').click(function (e) {
        var url = "/cliente/vistaGlobal/";
        $(location).attr('href', url);
    });

    $('#btnConsultar').click(function (e) {
        if ($('#txtIdCliente').val() != "" && $('#txtClienteGlobal').val() != "") {
            e.preventDefault();
            idcliente = $('#txtIdCliente').val();
            tipoCobro = $('#lstTipoCobro').val();
            situacion = $('#lstSituacion').val();
            fechaInicio = $('#fechaInicio').val();
            fechaFinal = $('#fechaFinal').val();
            $('#RSCliente').show();
            cargarDetalles(idcliente, tipoCobro, situacion, fechaInicio, fechaFinal);
        }
    });

    $('#contenedorModal').css('overflow', 'auto').dialog({
        title: title,
        autoOpen: false,
        modal: true,
        width: 900,
        height: 400,
        resizable: false,
        draggable: true,

    })
    $('#contenedorModal2').css('overflow', 'auto').dialog({
        title: title2,
        autoOpen: false,
        modal: true,
        width: 900,
        height: 350,
        resizable: false,
        draggable: true,

    })
    
    
    $('#btnImprimir').click(function (e) {
        e.preventDefault();
        imprSelec('contenedor');
    });
    $('#btnImprimir2').click(function (e) {
        e.preventDefault();
        imprSelec('contenedorOrdendes');
    });
    $('#btnImprimir3').click(function (e) {
        e.preventDefault();
        imprSelec('contenedorOrdendesDetalles');
    });
    $('#imprimirModal').click(function (e) {
        e.preventDefault();
        imprSelec('modal');
    });
    $('#imprimirModalProtesto').click(function (e) {
        e.preventDefault();
        imprSelec('blockProtestos');
    });
    
    $('#tabs').tabs();

    $('#tblcontenedor').on('click', '.verReferencia', function (e) {
        e.preventDefault();
        var iddoc = $(this).data('iddetalleordencobro');
        title2 = "Letra Protestada";
        var iddoc = $(this).data('iddetalleordencobro');
//        $('#tblcontenedor tr').removeClass();
//        $(this).parents('tr').addClass('active-row');
        $.ajax({
            url: '/reporte/verdocLetraprotestada',
            type: 'post',
            datatype: 'html',
            data: {'iddetalleordencobro': iddoc},
            success: function (resp) {
                $('#blockProtestos').html(resp);
                $('#contenedorModal2').dialog('open');
            }
        });
        return false;
    });
    
});


/*Funciones*/
function cargaOrdenesVenta() {
    var idCliente = $('#txtIdCliente').val();
    var ruta = "/ordenventa/listaOrdenesxIdCliente/" + idCliente;
    $.post(ruta, function (data) {
        $('#tblOrdenVenta tbody').html(data);
        $('#tblDetalleOrdenCobro tbody').html("");
    });
    $('#tblcontenedor').html('');
}
function cargaOrdenesVentaDetalles(id) {
    var ruta = "/ordenventa/listaOrdenesDetallado/" + id;
    $.post(ruta, function (data) {
        $('#tblOrdenVentaDetalle').html(data);
        $('#tblDetalleOrdenCobro tbody').html("");
    });
    $('#tblcontenedor').html('');
}
function cargaDetalleOrdenVenta(url) {
    $.post(url, function (data) {

        $('#tblDetalleOrdenCobro tbody').html(data);
        $('.cancelar').hide();
        $('.pagarparte').hide();
        $('#contenedorModal').dialog('open');
    });
}
function cargaDetalleOrdenVentaProducto(url) {
    $.post(url, function (data) {
        $('#tblDetalleOrdenCobro tbody').html(data);
        $('#contenedorModal').dialog('open');
    });
}
function buscaPosicionCliente() {
    var idCliente = $('#txtIdCliente').val();
    var ruta = "/cliente/detalleposicion/" + idCliente;
    $.post(ruta, function (data) {
        $('#tblDetallePosicion tbody').html(data);
        $('#txtidPosicionCliente').val($('#idposicioncliente').val());
        $('#lineacreditoactual').val($('#lineacreditoactiva').val().replace(',', ''));
    });
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

function cargarDetalles(idcliente, tipoCobro, situacion, fechaInicio, fechaFinal) {
    $.ajax({
        url: '/reporte/letraxCliente',
        type: 'post',
        datatype: 'html',
        data: {'idcliente': idcliente, 'tipoCobro': tipoCobro, 'situacion': situacion, 'fechaInicio': fechaInicio, 'fechaFinal': fechaFinal},
        success: function (resp) {

            $('#tblcontenedor').html(resp);
        }

    });
}

function listaDataLineacredito() {
    var idCliente = $('#txtIdCliente').val();
    var ruta = "/cliente/listaDataLineacredito/" + idCliente;
    $.post(ruta, function (data) {
        $('#seccionLineaCredito').html(data);
    });
}