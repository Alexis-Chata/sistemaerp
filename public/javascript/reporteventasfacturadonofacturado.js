
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
            $('#frmConsulta').attr('action', '/excel2/ventasfacturadonofacturado1');
            $('#frmConsulta').submit();            
    });
    $('#btnPdf').click(function () {
        $('#frmConsulta').attr('action', '/pdf/ventasfacturadonofacturado1');
        $('#frmConsulta').submit();
    });
});

var txtFechaInicio = $("#txtFechaInicio").val();
$("#txtFechaInicio").bind("keyup keydown change",function(){
  if ( $("#txtFechaInicio").val() != txtFechaInicio ) {
   $('#cmbFiltro').val('0');
   $('#txtFechaEmisionInicio').val('');
   $('#txtFechaEmisionFinal').val('');
  }
});

var txtFechaFinal = $("#txtFechaFinal").val();
$("#txtFechaFinal").bind("keyup keydown change",function(){
  if ( $("#txtFechaFinal").val() != txtFechaFinal ) {
   $('#cmbFiltro').val('0');
   $('#txtFechaEmisionInicio').val('');
   $('#txtFechaEmisionFinal').val('');
  }
});

var txtFechaEmisionInicio = $("#txtFechaEmisionInicio").val();
$("#txtFechaEmisionInicio").bind("keyup keydown change",function(){
  if ( $("#txtFechaEmisionInicio").val() != txtFechaEmisionInicio ) {
   $('#cmbFiltro').val('1');
   $('#txtFechaInicio').val('');
   $('#txtFechaFinal').val('');
  }
});

var txtFechaEmisionFinal = $("#txtFechaEmisionFinal").val();
$("#txtFechaEmisionFinal").bind("keyup keydown change",function(){
  if ( $("#txtFechaEmisionFinal").val() != txtFechaEmisionFinal ) {
   $('#cmbFiltro').val('1');
   $('#txtFechaInicio').val('');
   $('#txtFechaFinal').val('');
  }
});