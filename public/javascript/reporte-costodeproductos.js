$(document).on('ready', function(){
   
    $('#idMostrar').click(function () {
        $.ajax({
            url: '/reporte/costodeproductos_mostrar',
            success: function (data) {
                $('#contenidoCosto').html(data);
            }
        });
    });
    
});