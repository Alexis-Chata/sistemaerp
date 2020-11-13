$(document).ready(function(){
    var show=1;
    /*********** Autocompletes ************/
    $('#txtCliente').autocomplete({
	source: "/cliente/autocomplete2/",
	select: function(event, ui){
	    $('#idCliente').val(ui.item.id);		
        }
    });

    $('#txtVendedor').autocomplete({
	source: "/vendedor/autocompletevendedor/",
	select: function(event, ui){
	    $('#idVendedor').val(ui.item.id);
	}
    });
    $('#txtProducto').autocomplete({
		source: "/producto/buscarAutocompleteLimpio/",
		select: function(event, ui){
			$('#idProducto').val(ui.item.id);
			$('#txtDescripcion').val(ui.item.tituloProducto);
		}
	});
    /************** Botones **********************/
    $('#btnConsultarHTML').click(function(e){
        e.preventDefault();
        
    });
    $('#btnConsultarPDF').click(function(e){
	var idProducto=$("#idProducto").val();
	if (parseInt(idProducto)==0) {
	    e.preventDefault();
	    alert("Seleccione un Producto");
	}else{
	    $('#frmConsulta').attr('action','/pdf/reporteHistorialVentasxProducto');
	}
        
        
    });
    $('#btnConsultarExcel').click(function(e){
        e.preventDefault();
        
    });
    $('#btnLimpiar').click(function(e){
        e.preventDefault();
        $('#frmConsulta')[0].reset();
        $('#idProducto').val(0);
        $('#idVendedor').val('');
        $('#idCliente').val('');
    });
    
    $('#btnImprimir').click(function(e){
        e.preventDefault();
    });

    $("#btnAvanzado").click(function() {
        if(show==0){
            $("#etiquetaAvanzado").addClass( "ocultar_etiqueta" );
            show=1;
        }else{
            $("#etiquetaAvanzado").removeClass( "ocultar_etiqueta" );
            show=0;
        }
    });
    
    $("#chk1").on("click",function(){
        if ($('#chk1').is(':checked') ) {
           $("#chk2").removeAttr('checked');   
        }
    });
    $("#chk2").on("click",function(){    
        if ($('#chk2').is(':checked') ) {
           $("#chk1").removeAttr('checked');   
        }      
    });
    
    
//   ****** 
    $('#btnExcel').click(function () {
            $('#frmConsulta').attr('action', '/excel/rankingclientesseguncontabilidad');
            $('#frmConsulta').submit();            
    });
    $('#btnPdf').click(function () {
        cmbTipo=$("#cmbTipo").attr("value");
        if(cmbTipo==1){
        $('#frmConsulta').attr('action', '/pdf/rankingclientesseguncontabilidadDetallado');
        }
        if(cmbTipo==2){
        $('#frmConsulta').attr('action', '/pdf/rankingclientesseguncontabilidad');
        }
        
        $('#frmConsulta').submit();
    });
});