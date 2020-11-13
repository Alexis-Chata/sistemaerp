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
	$('#vendidos').click(function(e){
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
                consultacostos();
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

function consultacostos(){
    
    $.ajax({
	url:'/reporte/resumendekardex',
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
        $('#lblmes').html($('#INICIOmes option:selected').html() + ' - ' + $('#INICIOanho option:selected').html());
        $('#lblanho').html($('#FINmes option:selected').html() + ' - ' + $('#FINanho option:selected').html());
        $('#lblTitulo').html('<h2>Resumen de Kardex Por Producto</h2>');

        $('#lblFecha').html($('#fechaInicio').val()+' - '+$('#fechaFinal').val());
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