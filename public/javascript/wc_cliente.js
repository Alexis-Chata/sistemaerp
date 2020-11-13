$(document).ready(function(){
 // Llamadas a funciones
 	$('#buscarOrdenes').hide();
 	$('#nuevabusqueda').hide();
	$('#txtCliente').autocomplete({
		source: "/cliente/autocomplete2/",
		select: function(event, ui){
			$('#txtIdCliente').val(ui.item.id);
			$('#razonsocial').val(ui.item.label);
			$('#ruc').val(ui.item.rucdni);
			$('#codigo').val(ui.item.codigocliente);
			$('#codantiguo').val(ui.item.codigoantiguo);	
			buscaPosicionCliente();
		}});

	$('#txtClienteGlobal').autocomplete({
		source: "/cliente/autocomplete2/",
		select: function(event, ui){
			$('#txtIdCliente').val(ui.item.id);
			$('#razonsocial').val(ui.item.label);
			$('#ruc').val(ui.item.rucdni);
			$('#codigo').val(ui.item.codigocliente);
			$('#codantiguo').val(ui.item.codigoantiguo);
			$('#buscarOrdenes').show();
			$('#nuevabusqueda').show();
			cargaOrdenesVenta()
		}});

	$('#txtClientexIdCliente').autocomplete({
		source: "/cliente/autocomplete2/",
		select: function(event, ui){
			$('#txtIdCliente').val(ui.item.id);
		}});			

	$('.btnVerDetalleOrden').live('click',function(e){
		e.preventDefault();
		$('#tblOrdenVenta tr').removeClass();
		$(this).parents('tr').addClass('active-row');
		var url = $(this).attr("href");
		cargaDetalleOrdenVenta(url);
	});
	$('.btnDetalleProducto').live('click',function(e){
		e.preventDefault();
		$('#tblOrdenVenta tr').removeClass();
		$(this).parents('tr').addClass('active-row');
		var url = $(this).attr("href");
		cargaDetalleOrdenVentaProductos(url);
	});

	$('#nuevabusqueda').click(function(e){
		var url="/cliente/vistaGlobal/";
		$(location).attr('href',url);  
	});

});


/*Funciones*/
function cargaOrdenesVenta(){
	var idCliente= $('#txtIdCliente').val();
	var ruta = "/ordenventa/listaOrdenesxIdCliente/"+ idCliente;
	$.post(ruta, function(data){
		$('#tblOrdenVenta tbody').html(data);
		$('#tblDetalleOrdenCobro tbody').html("");

	});
}
function cargaDetalleOrdenVenta(url){
	$.post(url, function(data){
		$('#tblDetalleOrdenCobro tbody').html(data);
			
	});
}
function cargaDetalleOrdenVentaProductos(url){
	$.post(url, function(data){
		$('#tblDetalleOrdenCobro tbody').html(data);
			
	});
}

function buscaPosicionCliente(){
	var idCliente= $('#txtIdCliente').val();
	var ruta = "/cliente/detalleposicion/" + idCliente;
	$.post(ruta, function(data){
		$('#tblDetallePosicion tbody').html(data);
		$('#txtidPosicionCliente').val($('#idposicioncliente').val());
		$('#lineacreditoactual').val($('#lineacreditoactiva').val().replace(',',''));
	});}

function cargaPosicion(){
	var idCliente = $('#txtIdCliente').val();
	var ruta = "/cliente/posicionordenventa/"+ idCliente;
	$.post(ruta, function(data){
		$('#clienteposicion').html(data);
		var saldo=parseFloat($('#idsaldo').val());
		if(saldo<=0){
			$.msgbox("Alerta al Crear la ORDEN","EL CLIENTE TIENE SALDO S./ " + saldo+ "<br> Verifique el Saldo que necesita ANTES de empezar a registrar su orden.");
		}

	});}

function ultimaOrden(){
	var idCliente = $('#txtIdCliente').val();
	var ruta = "/cliente/datosdeudaOrdenVentas/"+ idCliente;
	$.post(ruta, function(data){
		$('#clienteultimaorden').html(data);
	});}

function deudatotal(){
	var idCliente = $('#txtIdCliente').val();
	var ruta = "/cliente/datosdeudaTotalOrdenVentas/"+ idCliente;
	$.post(ruta, function(data){
		$('#clientedeudatotal').html(data);
	});}