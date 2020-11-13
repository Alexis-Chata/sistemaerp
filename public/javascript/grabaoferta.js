$(document).ready(function () {

    $('#txtCodigoProductoOferta').autocomplete({
        source: "/producto/buscarAutocomplete/",
        select: function (event, ui) {
            $('#txtIdProducto').val(ui.item.id);
            $('#txtTituloProducto').val(ui.item.tituloProducto);
            listarOfertas(ui.item.id);
        }
    });

});

function listarOfertas(idproducto) {
    var valorVerificacion;
    $.ajax({
            url:'/mantenimiento/verofertas_producto',
            type:'post',
            dataType:'json',
            async: false,
            data:{'idproducto':idproducto},
            success:function(resp){
                $('#spanOfertaSoles').html(resp.preciolista);
                $('#spanOfertaDolares').html(resp.preciolistadolares);
                $('#lstTipoCobro').html(resp.Tipocbro);
                $('#blockOfertas').html(resp.ofertas);
            }
    });
}