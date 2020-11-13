$(document).ready(function () {
    
    $('#mesDetallado').hide();
    
    $('#txtVendedor').autocomplete({
        source: "/vendedor/autocompletevendedor/",
        select: function (event, ui) {
            $('#idVendedor').val(ui.item.id);
            $('#txtVendedor').attr('disabled', 'disabled');
            $('.cancelarVendedor').removeAttr('style');
        }
    });
    
    $('#lstFormato').change(function () {
        if ($(this).val() == 2) {
            $('.msg-error').hide();
            $('#mesGeneral').hide();
            $('#mesDetallado').show();
        } else {
            $('.msg-error').show();
            $('#mesGeneral').show();
            $('#mesDetallado').hide();
        }
    });
    
    $('.cancelarVendedor').click(function () {
        $('#txtVendedor').val('');
        $('#idVendedor').val('');
        $('#txtVendedor').removeAttr('disabled');
        $('.cancelarVendedor').attr('style', 'display: none');
    });
    
    $('.chkmes').click(function () {
        if ($(this).val() == 13) {
            if (!$(this).prop('checked')) {
                $('.chkmes').prop('checked', false);
            } else {
                $('.chkmes').prop('checked', true);
            }
        }
        $('.msg-error').html('');
    });
    
    $('#btnConsultarExcel').click(function () {
        if ($('#txtVendedor').val().length == 0) {
            $('#txtVendedor').focus();
            return false;
        } else if ($('#lstFormato').val() == '') { 
            $('#lstFormato').focus();
            return false;
        } else if ($('#lstComision').val() == '') { 
            $('#lstComision').focus();
            return false;
        } else {
            if ($('#lstFormato').val() == 1) {
                var bandera = 0;
                $('.chkmes').each(function () {
                    if ($(this).val()!=13) {
                        if ($(this).prop('checked')) {
                            bandera = 1;
                        }
                    }
                }); 
                if (bandera == 0) {
                    $('.msg-error').html('<b>Mensaje:</b> Elige el mes del resumen.');
                    return false;
                } else {
                    $('.msg-error').html('');
                    $('#frmComisionVentas').attr('action', '/excel/comisiondeventas');
                }
            } else {
                $('#frmComisionVentas').attr('action', '/excel/comisiondeventasdetallado');
            }            
        }
    });
    
});