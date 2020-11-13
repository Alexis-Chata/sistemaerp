//tblmostrarControlCobranzas
$(document).ready(function(){
 // Llamadas a funciones
    var title="Detalle";
 	$('#buscarOrdenes').hide();
 	$('#nuevabusqueda').hide();
//	$('#txtCliente').autocomplete({
//            source: "/cliente/autocomplete2/",
//            select: function(event, ui){
//                    $('#txtIdCliente').val(ui.item.id);
//                    $('#razonsocial').val(ui.item.label);
//                    $('#ruc').val(ui.item.rucdni);
//                    $('#codigo').val(ui.item.codigocliente);
//                    $('#codantiguo').val(ui.item.codigoantiguo);	
//                    buscaPosicionCliente();
//            }
//        });
            
        $('#txtClienteGlobal').autocomplete({
            source: "/cliente/autocomplete2/",
            select: function(event, ui){
               $('#txtIdCliente').val(ui.item.id);
               $('#razonsocial').val(ui.item.label);
               $('#ruc').val(ui.item.rucdni);
               $('#codigo').val(ui.item.codigocliente);
               $('#codantiguo').val(ui.item.codigoantiguo);
               $('#buscarOrdenes').show();
               $('#nuevabusqueda').show();
               $('#BCliente').html("Cliente: "+ui.item.label);
               $('#RSCliente').html("Cliente: "+ui.item.label);
               $('#RSCliente').hide();
               $('#tblControlCobranzas tbody').html('<div style="margin-top:2px;text-align:center;" ><img src="/imagenes/cargando.gif"></div>');
              cargaOrdenesVenta();
            }
         });
        
	$('#btnImprimir').click(function(e){
		e.preventDefault();
		imprSelec('contenedor');
	});
});


/*Funciones*/
function cargaOrdenesVenta(){
	var idCliente= $('#txtIdCliente').val();
	var ruta = "/reporte/listaControlCobranzas/"+ idCliente;
	$.post(ruta, function(data){
            $('#tblControlCobranzas tbody').html(data);
	});
	$('#tblcontenedor').html('');
}
