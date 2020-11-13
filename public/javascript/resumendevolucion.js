$(document).on('ready', function () {
    
    $('#blockSubMotivo').hide();
    $('#btnLimpiarCliente').hide();
    $('#btnLimpiarOV').hide();
    
    $('#txtOrdenVentaxId').autocomplete({
        source: "/ordenventa/buscarautocompletecompleto/",
        select: function (event, ui) {
            $('#txtIdOrdenVenta').val(ui.item.id);
            $('#txtOrdenVentaxId').attr('disabled', 'disabled');
            $('#btnLimpiarOV').show();
        }
    });
    
    $('#btnLimpiarOV').click(function () {
        $('#txtIdOrdenVenta').val('');
        $('#txtOrdenVentaxId').val('');
        $('#txtOrdenVentaxId').removeAttr('disabled');
        $('#txtOrdenVentaxId').focus();
        $(this).hide();
        return false;
    });

    $('#txtClientexIdCliente').autocomplete({
        source: "/cliente/autocomplete2/",
        select: function (event, ui) {
            $('#txtIdCliente').val(ui.item.id);
            $('#txtClientexIdCliente').attr('disabled', 'disabled');
            $('#btnLimpiarCliente').show();
    }});

    $('#btnLimpiarCliente').click(function () {
        $('#txtIdCliente').val('');
        $('#txtClientexIdCliente').val('');
        $('#txtClientexIdCliente').removeAttr('disabled');
        $('#txtClientexIdCliente').focus();
        $(this).hide();
        return false;
    });
    
    $('#idmotivodevolucion').change(function () {
        if($(this).val()>0){
            var idSubMotivoDevolucion=$(this).val();
            $.ajax({
                url: '/devolucion/cambiarSubMotivoDevolucion/',
                type: 'post',
                async: false,
                dataType: 'json',
                data: {
                    'idmotivodevolucion':idSubMotivoDevolucion,
                },
                success: function (resp) {                    
                    if (resp['tamanio'] == 0) {
                        $('#blockSubMotivo').hide();
                    } else {
                        $('#blockSubMotivo').show();
                    }
                    $('#idsubmotivodevolucion').html(resp['motivos']);
                },
                error: function (error) {
                    console.log(error)
                }
            });
        } else {
            $('#idsubmotivodevolucion').html('<option value="0">Seleccione una opcion</option>');
            $('#blockSubMotivo').hide();
        }
    });
    
    $('#btnDevoluciones').click(function () {
        $('#frmResumenDevolucion').attr('action', '/excel2/devoluciones');
        $('#frmResumenDevolucion').submit();
    });
    
    $('#btnResumen').click(function () {
        $('#frmResumenDevolucion').attr('action', '/excel2/resumendevoluciones');
        $('#frmResumenDevolucion').submit();
    });
    
    $('#btnResumenVendedor').click(function () {
        $('#frmResumenDevolucion').attr('action', '/excel2/resumendevolucionesvendedor');
        $('#frmResumenDevolucion').submit();
    });
    
});