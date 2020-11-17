$(document).ready(function(){
	$('#tblStockProducto').hide();
	$('#liAlmacen').hide();
	$('#liLinea').hide();
	$('#liSubLinea').hide();
	$('#liProducto').hide();
	$('#mostrarPDF').hide();
	msboxTitle = "Reporte de Stock de Producto";
	$('#lstLinea').change(function(){
		cargaSubLinea();
	});
	$('#btnConsultar').click(function(e){
		e.preventDefault();
		cargaTabla(msboxTitle);
		$('#idLinea').val(idLinea);
		$('#idSubLinea').val(idSubLinea);
		$('#idAlmacen').val(idAlmacen);
		$('#idProducto').val(idProducto);

	});
	$('#fsLineaSublinea').hide();
	$('input[name="rbFiltro"]').change(function(){
		$('#mostrarPDF').hide();
		$('#tblStockProducto').hide();
		$('#lstAlmacen option').eq(0).attr('selected','selected');
		$('#lstLinea option').eq(0).attr('selected','selected');
		$('#lstSubLinea option').eq(0).attr('selected','selected');
		$('#txtCodigoProducto, #txtCodigoProductoRepuesto, #txtIdProducto').val('');
		if(this.value == "0"){
			$('#liAlmacen').hide();
			$('#liLinea').hide();
			$('#liSubLinea').hide();
			$('#liProducto').hide();
		}else if(this.value == "1"){
			$('#liAlmacen').hide();
			$('#liLinea').hide();
			$('#liSubLinea').hide();
			$('#liProducto').hide();
		}else if(this.value == "2"){
			$('#liAlmacen').show();
			$('#liLinea').hide();
			$('#liSubLinea').hide();
			$('#liProducto').hide();
		}else if(this.value == "3"){
			$('#liAlmacen').hide();
			$('#liLinea').show();
			$('#liSubLinea').hide();
			$('#liProducto').hide();
		}else if(this.value == "4"){
			$('#liAlmacen').hide();
			$('#liLinea').show();
			$('#liSubLinea').show();
			$('#liProducto').hide();
		}else{
			$('#liAlmacen').hide();
			$('#liLinea').hide();
			$('#liSubLinea').hide();
			$('#liProducto').show();
			$('#txtCodigoProducto').focus();
		}
	});
	$('#btnActualizarProd').click(function(e){
 	
            idproducto=$('#txtIdProducto').val();
            stockDisponible=$('#txtStockDisponible').val();
            stockActual=$('#txtStockActual').val();
            $.post('/reporte/updateStock',{idproducto:idproducto,stockDisponible:stockDisponible,stockActual:stockActual},function(data){
                $.msgbox(data);
                $('#txtProductoStock').val('');
                $('#txtStockActual').val('');
            });
            return false;
        });
	$('#txtProductoStock').autocomplete({
			minLength: 2,
			source: function(request, response) {
				$.ajax({
					url: "/producto/buscarautocomplete/",
					dataType: "json",
					data: {term :request.term, idlinea : $('#lstLinea option:selected').val()},
					success: function( data ){
						console.log(data);
						response(data);
					}
				});
			},
			select: function(event, ui) {	
                                $('#txtIdProducto').val(ui.item.id);
				
				
			}
       });
	$('#txtCodigoProducto').keyup(function(){
		if($(this).val()==""){
			$('#txtIdProducto').val('');
		}
	});
	/*Autocompletes*/
    //Autocomplete Producto REPUESTO
    $('#txtCodigoProductoRepuesto').autocomplete({
        minLength: 2,
        source: function (request, response) {
            $.ajax({
                url: "/producto/buscarAutocompleteRepuesto/",
                dataType: "json",
                data: {term: request.term, idlinea: $('#lstLinea option:selected').val()},
                success: function (data) {
                    console.log(data);
                    response(data);
                }
            });
        },
        select: function (event, ui) {
            $('#txtIdProducto').val(ui.item.id);
            $('#txtTituloProducto').val(ui.item.tituloProducto);
            $('#txtCantidadProducto').focus();
            
        }
    });
});
msboxTitle = "Reporte de Stock de Producto";
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

//Carga tabla stock por linea
function cargaTabla(msboxTitle){
	idLinea = $('#lstLinea option:selected').val();
	idSubLinea = $('#lstSubLinea option:selected').val();
	idAlmacen = $('#lstAlmacen option:selected').val();
	idProducto = $('#txtIdProducto').val();
	filtro = $('input[name="rbFiltro"]:checked').val();
	mensaje = "";
	if(filtro == "2"){
		if(idAlmacen == ""){
			mensaje = "Seleccione correctamente el almacen";
		}
	}else if(filtro == "3"){
		if(idLinea == ""){
			mensaje = "Seleccione correctamente la Linea";
		}
	}else if(filtro == "4"){
		if(idLinea == "" || idSubLinea == ""){
			mensaje = "Seleccione correctamente la Linea y Sublinea";
		}
	}else if(filtro == "5"){
		if(idProducto == ""){
			mensaje = "Ingrese correctamente el nombre del producto";
		}
	}else if(filtro == "6"){
		if(idProducto == ""){
			mensaje = "Ingrese correctamente el nombre del repuesto";
		}
	}else{
		mensaje = "";
	}
	if(mensaje!=""){
		$.msgbox(msboxTitle, mensaje);
		execute();
	}else{
		$('#mostrarPDF').show();
		if(filtro == "6" || filtro == "0"){
			ruta = "/reporte/stockproductorepuesto/";
			$.post(ruta, {idProducto: idProducto}, function(data){
				$("#dataGridReport").data("kendoGrid").dataSource.data(data);
			});
		}else{
			ruta = "/reporte/stockproducto/";
			$.post(ruta, {idLinea: idLinea, idSubLinea: idSubLinea, idAlmacen: idAlmacen, idProducto: idProducto}, function(data){
				$("#dataGridReport").data("kendoGrid").dataSource.data(data);
			});
		}

	}
}