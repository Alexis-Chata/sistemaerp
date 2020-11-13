$(document).ready(function () {
    
    $('#btnConsultar').click(function () {
        if ($('#listaordenesVenta').val().length != 0) {
            Resultado($('#listaordenesVenta').val());
        } else {
            $('#listaordenesVenta').focus();
            alert("El campo está vacio");
        }
    });
    
    $('#btnConsultarExcel').click(function(){
        alert("Te digo que está en mantenimiento ... niño rata.");
        
    });
    
});

function Resultado(numeroLetra) {
    $.ajax({
        url:'/seguimiento/verlistavalesxOrdenventa/',
        type:'post',
        dataType:'html',
        data:{'numeroLetra':numeroLetra},
        success:function(resp){
            $('#camporesultado').removeAttr('style');
            $('#contenedorResultadoListaVales').html(resp);
        }
    });
}