$('document').ready(function(){
    alert('putito');
    $('#aceptar').click(function(e){
       $('#contenedorFinStockImpresion').html('<center><img src="/imagenes/cargando.gif" width="100"></center>');
       e.preventDefault();
       var data = $('#frmStockAnual').serialize();
       $.ajax({
            url:'/reporte/listadoStock',
            type:'post',
            dataType:'html',
            data:data,
            success:function(resp){
                $('#contenedorFinStockImpresion').html(resp);
                console(resp);
            },
            error:function(error){
                alert("mal");
            }
	});
    });
    
    $('#imprimir').click(function(e){
        e.preventDefault();
        imprSelec('contenedorFinStockImpresion');
    });
});


//https://www.youtube.com/watch?v=giyNqQmnDek
//https://www.youtube.com/watch?v=c1xih1BogoU