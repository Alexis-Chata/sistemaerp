$(document).ready(function () {
    $('#idImgCargar').hide();
    $('#txtCliente').autocomplete({
        source: "/cliente/autocomplete2/",
        select: function (event, ui) {
            $('#idCliente').val(ui.item.id);
        }
    });
    
    $('#btnConsultar').click(function () {
        $('selector').css('cursor', 'pointer');
        $('#idImgCargar').show();
        $('#btnConsultar').hide();
        var idFecha = $('#txtFecha').val();      
        $.ajax({
            url:"/producto/valorizaxlinea/",
            method:"POST",
            data:{idFecha:idFecha},
            cache:"false",
            success:function(data) {
                $('#idTablaValorizado tbody').html(data);
                $('#idImgCargar').hide();
                $('#btnConsultar').show();
            }
        });
    });
});
