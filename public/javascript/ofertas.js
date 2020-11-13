$(document).on('ready', function () {

    $("#seleccion").change(function () {
        var id = $("#seleccion option:selected").text();
        if (id != '') {
            id = '/' + id;
        }
        var url = '/mantenimiento/ofertas' + id + $(this).data('url');
        window.location = url;
    });
    
    $('.eliminarOferta').click(function () {
        if (!confirm('¿Esta seguro de eliminar la oferta?')) {
            return false;
        }
    });

});