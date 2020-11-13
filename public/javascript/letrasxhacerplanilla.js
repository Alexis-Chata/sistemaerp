$(document).ready(function () {

    $('#liNL').hide();

    $('.rbFiltro').change(function () {
        $('#txtBusquedaOV').val('');
        $('#txtBusqueda').val('');
        if ($(this).val() == 1) {
            $('#liNL').hide();
            $('#liOV').show();
        } else {
            $('#liNL').show();
            $('#liOV').hide();
        }
    });

    $('#txtBusquedaOV').autocomplete({
        source: "/ordenventa/busquedaletrasxov/",
        minLength: 2,
        select: function (event, ui) {            
            listadeletras(ui.item.id);            
        }
    })

});

function listadeletras(idordenventa) {
    $('#txtBusquedaOV').attr('disabled', 'disabled');
    $.ajax({
        url: "/ordenventa/listadeletrasxov/",
        type: 'post',
        dataType: 'json',
        data: {'idordenventa': idordenventa},
        success: function (resp) {
            var tamanio = resp.length;
            for (var i = 0; i < tamanio; i++) {
                console.log(resp[i].numeroletra);
                Mostrarplanilla(resp[i].numeroletra, idordenventa);
                valor=1;
                actualizaCampo(resp[i].numeroletra,valor);
            }    
            $('#txtBusquedaOV').val('');
            $('#txtBusquedaOV').removeAttr('disabled'); 
        }
    });
}