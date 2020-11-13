$(document).ready(function(){
    	
    $('#txtCliente').css('background-color','skyblue').autocomplete({
	source: "/cliente/autocomplete2/",
	select: function(event, ui){
	    $('#idCliente').val(ui.item.id);
	    
			
    }});
    
    $('#txtVendedor').css('background-color','yellow').autocomplete({
	source: "/vendedor/autocompletevendedor/",
	select: function(event, ui){
	    $('#idVendedor').val(ui.item.id);
	}
    });
    
    $('#txtOrdenVenta').css('background-color','pink').autocomplete({
	source: "/ordenventa/buscarautocompletecompleto/",
	select: function(event, ui){
	    $('#idOrdenVenta').val(ui.item.id);
	}
    });
    
    $('.datepicker').css('background','#3DFF4F');
    $('#btnLimpiar').click(function(e){
        e.preventDefault();
        $("#frmConsulta")[0].reset();
        $('#idCliente').val('');
        $('#idVendedor').val('');
        $('#idOrdenVenta').val('');
        
    });
    $('#btnConsultaHTML').click(function(e){
        e.preventDefault();    
    });
    
    $('#btnConsultaPDF').click(function(e){
            
    });
    
    $('#btnConsultaExcel').click(function(e){
        e.preventDefault();    
    });
    
    $('#btnImprimir').click(function(e){
        e.preventDefault();    
    });
});