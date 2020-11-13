$(document).ready(function(){
   
    $('#txtOrdenCompra').autocomplete({
        source: "/ordencompra/autoCompleteAprobados/",
        select: function(event, ui){
            $('#conCab').html("ORDEN DE COMPRA");
            $('#conBody').html('<tr><td colspan="20"><center><img src="/imagenes/cargando.gif" width="100"></center></td></tr>');
            $('#conFoot').html("");
            var id = ui.item.id;
            $.ajax({
                url:'/reporte/ciclodevida_consulta',
                type:'get',
                dataType: "json",
                data:{'idcontenedor':id},
                success:function(resp){
                    $('#conCab').html('<h4>' + $('#txtOrdenCompra').val() + '</h4>');
                    $('#txtOrdenCompra').val('');
                    $('#conCab').html(resp['cabecera']);
                    $('#conBody').html(resp['body']);
                    $('#conFoot').html(resp['foot']);
                },
                error:function(error){
                        //console.log('error');
                }
            });
        }
    });
    
    $('#imprimir').click(function (e){
        e.preventDefault(e);
	imprSelec('imprimirtabla');
    });
    
});