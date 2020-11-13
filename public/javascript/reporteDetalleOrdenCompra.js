$(document).ready(function(){
   
    $('#txtOrdenCompra').autocomplete({
        source: "/ordencompra/autoCompleteAprobados/",
        select: function(event, ui){
            var id = ui.item.id;
            $.ajax({
                url: "/ordencompra/reporteDetalleOrdenCompra/",
                type: "post",
                data: {txtid: id},
                success: function (data) {
                    $('#tituloOC').html($('#txtOrdenCompra').val());
                    $('#txtOrdenCompra').val('');
                    $('#contenedor').html(data);
                }
            });
        }
    });
    
    $('#imprimir').click(function (e){
        e.preventDefault(e);
	imprSelec('imprimirtabla');
    });
    
});