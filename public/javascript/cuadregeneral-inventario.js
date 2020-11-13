$(document).ready(function () {
    
    $('#idcmbTipo').change(function () {
        if ($(this).val()==1) {
            $('.blocDetalles').removeAttr('style');
        } else {
            $('.blocDetalles').attr('style', 'display: none');
        }            
    });
    
});