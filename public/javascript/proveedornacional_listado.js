$(document).on('ready', function () {

    $('.eliminarProveedorN').click(function () {
        if (confirm('¿Esta seguro de eliminar el proveedor')) {
            return true;
        }
        return false;
    });
    
    $('.classSituacion').change(function () {
        $(this).parents('tr').find('.classSituacionGuardar').show();
    });
    
    $('.classSituacionGuardar').click(function () {
        var situacion = $(this).parents('tr').find('.classSituacion').val();
        var id = $(this).data('id');
        $.ajax({
            url: '/proveedornacional/proveedornacional_situacion',
            data:{
                'situacion': situacion,
                'idproveedornacional': id
            },
            type: 'POST',
            dataType: 'html',
            success: function (data) {
            }
        });
        
        $(this).hide();
        return false;
    });

});