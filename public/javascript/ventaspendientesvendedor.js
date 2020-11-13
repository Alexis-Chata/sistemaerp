$(document).ready(function(){
	$('#btnExcel').on("click",function(){
		$('#frmPendientesVendedor').attr('action', '/excel/ventaspendientesvendedor');
        $('#frmPendientesVendedor').submit();
	});
    $('#btnLimpiar').on("click",function(e){
		e.preventDefault();
		limpiar();
	});
    function limpiar(){
        $('#frmPendientesVendedor')[0].reset();
          return false; 
    }
});