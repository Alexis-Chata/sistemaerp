$(document).on('ready', function () {

    $("#seleccion").change(function () {
        var id = $("#seleccion option:selected").text();
        if (id != '') {
            id = '/' + id;
        }
        var url = '/mantenimiento/descripcionproducto' + id + $(this).data('url');
        window.location = url;
    });
    
    $('.eliminarDesc').click(function () {
        if (!confirm('¿Esta seguro de eliminar la descripcion auxiliar?')) {
            return false;
        }
    });
    
});