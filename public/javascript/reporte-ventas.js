$(document).ready(function(){
	$('#liVendedor').hide();
	$('#mostrarPDF').hide();
	msgboxTitle = "Reporte de Ventas";
	$('input[name="rbFiltro"]').change(function(){
		$('#tblOrdenesVenta').hide();
		$('#lstLinea option').eq(0).attr('selected','selected');
		$('#lstVendedor option').eq(0).attr('selected','selected');
		$('#txtFechaInicial').val('');
		$('#txtFechaFinal').val('');
		if(this.value == "1"){
			$('#liLinea').show();
			$('#liVendedor').hide();
		}else if(this.value == "2"){
			$('#liLinea').hide();
			$('#liVendedor').show();
		}else{
			$('#liLinea').hide();
			$('#liVendedor').hide();
		}
	});
	$("#frmbusqueda").validate({
        rules: {
			linea: {
				required: '#rbLinea:checked'
			},
			vendedor: {
				required: '#rbVendedor:checked'
			},
			fechaInicial: {
				required: true
			},
			fechaFinal: {
				required: true
			}
		},
		invalidHandler: function(form, validator) {
		   var errors = validator.numberOfInvalids();
		   if (errors) {
		        $.msgbox(msgboxTitle, 'Ingrese todos los datos requeridos correctamente');
		     }

		},
		errorElement: 'span',
        submitHandler: function(){
			$.getJSON("/reporte/ventas/", $("#frmbusqueda").serialize(),function(data){
				$("#dataGridReport").data("kendoGrid").dataSource.data(data);
	        });
	        
			$('#mostrarPDF').show();
			$('#valorLinea').val($('#lstLinea').val());
			$('#valorVendedor').val($('#lstVendedor').val());
			$('#valorFechaI').val($('#txtFechaInicial').val());
			$('#valorFechaF').val($('#txtFechaFinal').val());
			
	    }
    });
});
msboxTitle = "Reporte de Ventas";
//Cargar listado de sub linea
	function cargaSubLinea(){
		idLinea = $('#lstLinea option:selected').val();
		if(idLinea){
			ruta = "/sublinea/listaroptions/" + idLinea;
			$.post(ruta, function(data){
				$('#lstSubLinea').html('<option value="">-- Sub Linea --' + data);
			});
		}else{
			$('#lstSubLinea').html('<option value="">-- Sub Linea --');
		}
	}