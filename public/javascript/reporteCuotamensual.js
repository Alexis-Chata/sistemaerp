$(document).ready(function(){
    
    
    $('#txtVendedor').autocomplete({
            source: "/vendedor/autocompletevendedor/",
            select: function(event, ui){
                    $('#idVendedor').val(ui.item.id);
            }
    });
    /**************** Botones **********************/
    $('#btnConsultar').click(function () {
        console.log("BTNCONSULTAR");
        $('#divCuotaMensual').css('display','inline');
        $('#divContenido').html("<center><img src='/public/imagenes/cargando.gif' width='300'></center>");
        var idvendedor = $('#idVendedor').val();
        var moneda = $('#lstMoneda').val();
        var lstSemana = $('#lstSemana').val();
        var lstMes = $('#lstMes').val();
        var lstAnio = $('#lstAnio').val();
        
        var parametros = {
            idvendedor:idvendedor,
            moneda:moneda,
            semana:lstSemana,
            mes:lstMes,
            anio:lstAnio
        };
        
        $.ajax({
            url: "/reporte/listaConsultaCuotaMensual",
            data: parametros,
            type: "POST",
            success: function (datos) {
                $('#divContenido').html(datos);
            }
        });
    });
    
    $('#btnImprimir').live('click',function(e){
            e.preventDefault();
            imprSelec('divContenido');
	});
});