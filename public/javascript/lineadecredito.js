$(document).ready(function() {
    
    $('#idLcredito').change(function () {
        calcularLineas(1);
    });
    
    $('#idLdisponible').change(function () {
        calcularLineas(2);
    });
   
    $('#frmLineaCredito').submit(function () {
        if(!$('#chkContado').prop('checked') && !$('#chkCredito').prop('checked') && !$('#chkLetra').prop('checked')) {
            alert('Debes seleccionar un tipo de condicion');
            return false;
        } else {
            calcularLineas(1);
        }
        return true;
    });
    
});

function calcularLineas(tipo) {
    var lcredito = 0;
    var ldisponible = 0;
    var deuda = parseFloat($('#idLutilizada').val());
    if ($('#idLcredito').val() != '' && tipo == 1) {
        lcredito = parseFloat($('#idLcredito').val());
        ldisponible = lcredito - deuda;
    }
    if ($('#idLdisponible').val() != '' && tipo == 2) {
        ldisponible = parseFloat($('#idLdisponible').val());
        lcredito = ldisponible + deuda;
    }
    $('#idLcredito').val(lcredito.toFixed(2));
    $('#idLdisponible').val(ldisponible.toFixed(2));
}