$(document).ready(function () {

    $('#txtCodigoProductoOferta').autocomplete({
        source: "/producto/buscarAutocomplete/",
        select: function (event, ui) {
            $('#txtIdProducto').val(ui.item.id);
            $('#txtTituloProducto').val(ui.item.tituloProducto);
            verDescripcion2(ui.item.id);
        }
    });

});

function verDescripcion2(idproducto) {
    $.ajax({
            url:'/mantenimiento/gestiondescripcion2_ver',
            type:'post',
            dataType:'json',
            async: false,
            data:{'idproducto':idproducto},
            success:function(resp){
                $('#idDescripcionAuxiliar').val(resp.descripcion2);
                $('#idDescripcionAuxiliar').focus();
            }
    });
}