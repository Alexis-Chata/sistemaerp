$(document).ready(function () {

    $('#detalle').hide('Blind');

    $('#txtOrdenVenta').autocomplete({
        source: "/ordenventa/ListaGuiaMadreConDevolucionFactura/",
        select: function (event, ui) {
            var idDevolucion = ui.item.id;
            $('#txtIdDevolucion').val(idDevolucion);
            verDevolucion(idDevolucion);
            $('#lblFacturaElectronica').html('Factura Electronica: ' + ui.item.factura);
        }
    });

    $('#btncerrar').click(function (e) {
        $('#detalle').hide('Blind');
    });

    $('#imprimir').click(function (e) {
        e.preventDefault();
        $('.devregistrada').show();
        $('#imprimir').hide();
        $('#btncerrar').hide();
        $('#btnnotacredito').hide();
        $('table tr td, table tr th').css('font-family', 'courier');
        $('body').css('color', 'black');
        imprSelec('detalle');
        $('#imprimir').show();
        $('#btncerrar').show();
        $('#btnnotacredito').show();
        $('table tr td, table tr th').css('font-family', 'Calibri');
    });
    
    $('#btnEmitir').click(function () {
        $(this).attr('href', '/facturacion/generarnotacreditodevolucion/' + $('#txtIdDevolucion').val());
    });
    
});

function verDevolucion(iddevolucion) {
    var IDD = iddevolucion;
    $.ajax({
        url: '/devolucion/listaDetalleDevolucion',
        type: 'post',
        data: {'IDD': IDD},
        success: function (resp) {
            console.log(resp);
            $('#detalle').hide('Blind');
            $('#mensaje').html(' DEVOLUCIÓN N°: ' + IDD);
            $('#tbldetalles tbody').html(resp);
            $('#detalle').show('Blind');
        },
        error: function (error) {
            console.log(error);
        }
    });
    $.ajax({
        url: '/devolucion/encabezadoDevolucion',
        type: 'post',
        data: {'IDD': IDD},
        success: function (resp) {
            console.log(resp);
            $('#tblEncabezado').html(resp);

        },
        error: function (error) {
            console.log(error);
        }
    });
}