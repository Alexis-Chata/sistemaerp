$(document).on('ready', function () {
    
    $("#btnConsultar").click(function () {
        var fechaSeguimiento = $('#fechaSeguimiento').val();
        cargarDetalles(fechaSeguimiento);
    }); 

    $('.btnVerDetalleOrden').live('click',function(e){
        $(this).parents('tr').addClass('active-row');
        var url = $(this).data('urlredireccion');
        cargaDetalleOrdenVenta(url);
    });
    
    $('#txtBusqueda').autocomplete({
        source: "/facturacion/filtro/",
        select: function (event, ui) {
            var idOrdenVenta = ui.item.id;
            valoridordenventa = ui.item.id;
        }
    });
    
    $('#btnImprimir').click( function(e){
        e.preventDefault();
        imprSelec('contenedor');
    });
    
    $(".Observacion").keyup(function (e) {
        if (e.keyCode == 13) {
            var idinput = $(this).data('id');
            var IDOV = $(this).data('idordenventa');
            var txtObservacion = $('#'+idinput).val();
            $.ajax({
                url: '/seguimiento/registrarobservacion',
                type: 'POST',
                dataType:'json',
                data: {'IDOV': IDOV, 'TXTObservacion': txtObservacion},
                success: function (respuesta) {
                   if(parseInt(respuesta.rspta) == -1){
                       $('#'+idinput).val(respuesta.observacion);
                       $('#'+idinput).attr('disabled');
                   } else {
                       if(respuesta.rspta == 1){
                           $('#'+idinput).val(respuesta.observacion);
                           $('#'+idinput).attr('disabled', 'disabled');
                       } else {
                           $('#'+idinput).val('Error de Conexion');
                       }
                   }
                },
                error: function (error) {
                    //console.log(error);
                }
            });
        }
    });
});

function cargarDetalles(fechaSeguimiento) {
    $.ajax({
        url: '/seguimiento/listarOVobservacion',
        type: 'post',
        datatype: 'html',
        data: {'fechaSeguimiento': fechaSeguimiento},
        success: function (resp) {
            $('#tblSeguimiento').html(resp);
        }
    });
}

function cargaDetalleOrdenVenta(url) {
    $.post(url, function (data) {
        $('#cabeceraOV').html(data);
        $('.cancelar').hide();
        $('.pagarparte').hide();
    });
}