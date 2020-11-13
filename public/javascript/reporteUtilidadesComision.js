$(document).ready(function(){
    $('#txtOrdenVenta').autocomplete({
		source: "/ordenventa/autocompleteCancelados/",
		select: function(event, ui){
			$('#idOrdenVenta').val(ui.item.id);
		}
    });
    
    /********************  Botones ***************************/
    
    $('#btnConsultarHtml').click(function(e){
        e.preventDefault();    
    });
    $('#btnConsultarPDF').click(function(e){
        if($('#idOrdenVenta').val()==0){
            e.preventDefault();
            alert('Debe Ingresar una Orden de Venta');
        }  
    });
    $('#btnConsultarExcel').click(function(e){
        e.preventDefault();    
    });
    $('#btnLimpiar').click(function(e){
        e.preventDefault();
        $('#frmConsulta')[0].reset();
        $('#idOrdenVenta').val(0);
    });
    $('#btnImprimir').click(function(e){
        e.preventDefault();    
    });
});