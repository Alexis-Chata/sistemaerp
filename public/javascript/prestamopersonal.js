$(document).ready(function () {

    $('#btnAnadirVendedor').click(function () {
        var idvendedor = $('#lstVendedor').val();
        if (idvendedor > 0 && $('#idColaborado_' + idvendedor).val() != idvendedor) {
            var div = '<div class="EliminarVendedor">' +
                    '<input type="hidden" value="' + idvendedor + '" id="idColaborado_' + idvendedor + '" name="idVendedores[]">' +
                    '<input type="text" title="Eliminar Vendedor" value="' + $('#lstVendedor option:selected').text() + '" class="inputBorder" readonly="">' +
                    '</div>';
            $('#blockVendedor').append(div);
        } else {
            $('#lstVendedor').focus();
        }
    });
    
    $('#blockVendedor').on('click', '.inputBorder', function () {
        $(this).remove();
    });

});

function cargaConsulta() {
    $.ajax({
        url: '/ventas/listaReporteVentas',
        type: 'post',
        dataType: 'html',
        data: $('#Parametros').serialize(),
        success: function (resp) {
            $('#tblVentas').html(resp);
        }
    });
}