$(document).ready(function(){
	var listaSubLinea=$('#lstSubLinea').html();


	$('#txtproducto').autocomplete({
		source: "/producto/buscarAutocompleteLimpio/",
		select: function(event, ui){
			console.log(ui.item);
			$('#idProducto').val(ui.item.id);
			$('#codigoProducto').val(ui.item.value);
			$('#rutaImagenProducto').val("/imagenes/productos/" + ui.item.value +"/"+ui.item.imagen);
			$('#txtDescripcion').val(ui.item.tituloProducto);
			cargarAlmacen(ui.item.almacen);
			$('#labelUnidadMedida').html(ui.item.cod_sunat);
		}
	});


	$('#agotados').click(function(e){
		if ($(this).attr('checked')=="checked") {
			
			
			$('#tblConsulta').html('');
			$('#tblConsulta').hide();
			$('#tblEncabezado').hide();
		}
	});

	$('#lstLinea').change(function(){
		idLinea=$(this).val();
		if (idLinea!="") {
			cargarSubLinea(idLinea)
		}else{
			$('#lstSubLinea').html(listaSubLinea);
		}
		
	});
	$('#btnLimpiar').click(function(e){
		e.preventDefault();
		$('#frmConsulta')[0].reset();
		$('#idProducto').val('');
		$('#tblConsulta').html('');
		$('#tblConsulta').hide();
		$('#tblEncabezado').hide();
		$('#agotados').click();
	});

	$('#btnConsultar').click(function(e){
                $(this).attr('disabled','disabled');
                $('#cargando').removeAttr('style');
		e.preventDefault();
		$('#tblConsulta').show();
		$('#tblEncabezado').show();
		encabezado();
                consultavendedores();
	});
        
	$('#btnImprimir').click(function(e){
		e.preventDefault();
		imprSelec('ContenedorImpresion');
	});

});

function cargarSubLinea(idLinea){
	$.ajax({
		url:'/linea/listaSubLinea',
		type:'post',
		dataType:'html',
		data:{'idLinea':idLinea},
		success:function(data){
			
			$('#lstSubLinea').html(data);
		}
	});
}
function consultaAgotados(){
	$.ajax({
		url:'/producto/productosAgotados',
		type:'post',
		dataType:'html',
		data:$('#frmConsulta').serialize(),
		success:function(data){
			
			$('#tblConsulta').html(data);
		}
	});
}
function consultaVendidos(){
	$.ajax({
		url:'/producto/productosVendidos',
		type:'post',
		dataType:'html',
		data:$('#frmConsulta').serialize(),
		success:function(data){
			
			$('#tblConsulta').html(data);
		}
	});
}

function consultavendedores(){
    
    $.ajax({
	url:'/reporte/reportevendedores',
	type:'post',
	dataType:'html',
	data:$('#frmConsulta').serialize(),
	success:function(data){
            $('#tblConsulta').html(data);
            $('#cargando').attr('style','display: none');
            $('#btnConsultar').removeAttr('disabled');
	}
    });
}

function encabezado(){
        switch($('input[name="cbopcion"]:checked').val()){
            case '1':
                $('#lblTitulo').html('<h2>Listado de Vendedores Activos</h2>');
                break;
            case '-1':
                $('#lblTitulo').html('<h2>Listado de Vendedores Inactivos</h2>');
                break;
            case '0':
                $('#lblTitulo').html('<h2>Listado de Vendedores</h2>');
                break;
        } 
}

function cargarAlmacen(idalmacen){
	$.ajax({
		url:'/almacen/buscaAlmacen',
		type:'post',
		dataType:'json',
		data:{'idalmacen':idalmacen},
		success:function(resp){
			$('#labelRazonSocial').html(resp.razsocalm);
			$('#labelRuc').html(resp.rucalm);
		}
	});
}