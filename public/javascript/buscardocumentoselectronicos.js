$(document).ready(function () {

    $('#btnConsultar').click(function () {
        consultar();
    });
    
    $('#tblComprobantes').on('click', '.classVerDocumento', function () {
        abrirVentana($(this).data('url'));
        return false;
    });

});

function abrirVentana(url) {
    window.open('documentoelectronico/' + url, "Mi pagina", "width=900,height=500,menubar=no,location=no,resizable=no");
}

function consultar() {
    $('#tblComprobantes tbody').html('<tr><td colspan="8">Cargando...</td></tr>');
    $.ajax({
        url: '/facturacion/buscardocumentoselectronicos_consultar',
        type: 'post',
        datatype: 'html',
        data: {
            'txtFechaInicio': $('#txtFechaInicio').val(),
            'txtFechaFin': $('#txtFechaFin').val(),
            'filtroSerie': $('#filtroSerie').val(),
            'folioDesde': $('#folioDesde').val(),
            'folioHasta': $('#folioHasta').val(),
            'filtroComprobante': $('#filtroComprobante').val()
        },
        success: function (resp) {
            $('#tblComprobantes tbody').html(resp);
        }, error: function (error) {
            console.log('error');
        }
    })
}