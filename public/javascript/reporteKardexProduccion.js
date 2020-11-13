$(document).ready(function(){
    
   $('#txtProducto').autocomplete({
		source: "/producto/buscarAutocompleteLimpio/",
		select: function(event, ui){
			$('#idProducto').val(ui.item.id);
			$('#txtDescripcion').val(ui.item.tituloProducto);
		}
    });
    
    $('.datepicker').css('background','#3DFF4F');
    $('#btnLimpiar').click(function(e){
        e.preventDefault();
        $("#frmConsulta")[0].reset();
        $('#idProducto').val(0);
        
    });
    $('#btnConsultaHTML').click(function(e){
        e.preventDefault();    
    });
    
    $('#btnConsultaPDF').click(function(e){
            if ($('#idProducto').val()==0) {
                e.preventDefault();
                alert('Seleccione un Producto');
            }
    });
    
    $('#btnConsultaExcel').click(function(e){
        e.preventDefault();    
    });
    
    $('#btnImprimir').click(function(e){
        e.preventDefault();    
    });
});