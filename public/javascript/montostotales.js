$(document).ready(function(){

    $('#btnConsultar').click(function () {
        if ($('#lstTipoDocumento').val() == '') {
            $('#lstTipoDocumento').focus();
        } else {
            var electronico = 0;
            var fisico = 0;
            if ($('#idElectronico').prop('checked')) {
                electronico = 1;
            }
            if ($('#idFisico').prop('checked')) {
                fisico = 1;
            }
            $.ajax({
                url: '/documento/montostotales_consultar',
                type: 'post',
                dataType: 'json',
                async: false,
                data:{
                    'txtFechaInicio': $('#txtFechaInicio').val(),
                    'txtFechaFin': $('#txtFechaFin').val(),
                    'lstTipoDocumento': $('#lstTipoDocumento').val(),
                    'idElectronico': electronico,
                    'idFisico': fisico,
                    'txtMoneda': $('#txtMoneda').val()
                },
                success:function(resp){
                    $('#lblFechaInicio').html(resp['fechainicio']);
                    $('#lblFechaFin').html(resp['fechafin']);
                    $('#lblDocumento').html(resp['documento']);
                    $('#lblMoneda').html(resp['moneda']);
                    $('#lblImporteTotal').html(resp['total']);
                }
            });
        }
    });

});