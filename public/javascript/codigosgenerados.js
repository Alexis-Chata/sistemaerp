$(document).ready(function () {
    
    listarCodigosgenerados(1);
    
    $('#txtOrdenVenta').autocomplete({
        source: "/ordenventa/buscarautocompletecompleto/",
        select: function (event, ui) {
            $('#txtIdordenventa').val(ui.item.id);
        }
    });
    
    $('#limpiarOv').click(function () {
        $('#txtOrdenVenta').val('');
        $('#txtIdordenventa').val('');
        return false;
    });

    $('#btnFiltrar').click(function () {
        listarCodigosgenerados(1);
    });
    
    $('#tblCodigosVerificacion').on('click', '.classPaginacion', function () {
        listarCodigosgenerados($(this).data('page'));
    });
    
    $('#tblCodigosVerificacion').on('change', '#cmbSeleccion', function () {
        listarCodigosgenerados($(this).val());
    });
    
    $('#tblCodigosVerificacion').on('click', '.btnVerDescripcion', function () {
        if ($(this).data('on') == 0) {
            $('#trDescripcion' + $(this).data('id')).show();
            $(this).data('on', 1);
            $(this).parents('tr').find('td').addClass('classTdFila');
            $(this).parents('tr').find('.classVer').addClass('blockVer');
        } else {
            $('#trDescripcion' + $(this).data('id')).hide();
            $(this).data('on', 0);
            $(this).parents('tr').find('td').removeClass('classTdFila');
            $(this).parents('tr').find('.classVer').removeClass('blockVer');
        }
        return false;
    });
    
    $('#tblCodigosVerificacion').on('click', '.cllasEliminar', function () {
        if (!confirm('¿Esta seguro eliminar el código de verifiación?')) {
            return false;
        }
        return true;
    });
    
});

function listarCodigosgenerados(pagina) {
    var uso = 0;
    var proceso = 0;
    var usado = 0;
    var vencido = 0;
    if ($('#chkUsar').prop('checked')) {
        uso = 1;
    }
    if ($('#chkProceso').prop('checked')) {
        proceso = 1;
    }
    if ($('#chkUsadas').prop('checked')) {
        usado = 1;
    }
    if ($('#chkVencidas').prop('checked')) {
        vencido = 1;
    }
    $('#tblCodigosVerificacion tbody').html('');
    $.ajax({
        url: '/creditos/codigosgenerados',
        type: 'post',
        dataType: 'json',
        data: {
            'chkUsar': uso,
            'chkProceso': proceso,
            'chkUsadas': usado,
            'chkVencidas': vencido,
            'txtFechaInicio': $('#txtFechaInicio').val(),
            'txtFechaFin': $('#txtFechaFin').val(),
            'cmbUsuario': $('#cmbUsuario').val(),
            'cmbModulo': $('#cmbModulo').val(),
            'idordenventa': $('#txtIdordenventa').val(),
            'idMotivo': $('#idMotivo').val(),
            'pagina': pagina
        },
        success: function (resp) {
            $('#tblCodigosVerificacion tbody').html(resp.contenedor);
            $('#tblCodigosVerificacion tfoot').html(resp.paginacion);
        }
    });
}