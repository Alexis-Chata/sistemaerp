$(document).ready(function () {
    
    $('.blockMonto').hide();
    
    $('#cmbMonto').change(function () {
        if ($(this).val() > 0) {
            $('.blockMonto').hide();
        } else {
            $('.blockMonto').show();
            $('#txtMonto').focus();
        }
    });
    
    $('#cmbMoneda').change(function () {
        if ($(this).val() == 1) {
            $('#textMoneda').html('S/');
        } else {
            $('#textMoneda').html('US $.');
        }
    });
    
    $('#btnExcel').click(function () {
        if ($('#cmbMonto').val() == ""&&$('#txtMonto').val().length==0) {
            $('.blockMonto').show();
            $('#txtMonto').focus();
            return false;
        } else {
            $('#frmClientesDestacados').attr('action', '/excel/clientesdestacados');
            $('#frmClientesDestacados').submit();
        }
    });
    
});