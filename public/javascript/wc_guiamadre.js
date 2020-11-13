$(document).ready(function(){
	var idov=0;
	$('#imprimir').hide();
	$('#btnEditarOrdenVenta').hide();
	$('#OrdenVentaEdicionGlobal').hide();
        $('#btnDescargarOrdenVenta').hide();
	$('.field-set').hide();
        $('#DxFactura').hide();
	$('#block_Factura').hide();
        
        $('#tblDetalleOrdenVenta').on('click', '#editObservaciones', function () {
            if ($('#idThObservaciones').data('on') == 0) {
                $('#idThObservaciones').data('on', 1);
                $('#editObservaciones').hide();
                if ($('#idObservacionesFacturacion').val() == 'Sin Observaciones') {
                    $('#idObservacionesFacturacion').val('');
                }
                $('#idObservacionesFacturacion').show();
                $('#idObservacionesFacturacion').focus();
                $('#guardarObservaciones').show();                
            }
            return false;
        });
        
        $('#tblDetalleOrdenVenta').on('click', '#guardarObservaciones', function () {
            guardarObservacionFacturacion();
        });
        
        $('#tblDetalleOrdenVenta').on('keypress', '#idObservacionesFacturacion', function (e) {
            if (e.which == 13) {
                guardarObservacionFacturacion();
            }
        });
        
	$('#txtOrdenVenta').autocomplete({
		source: "/ordenventa/ListaGuiaMadre/",
		select: function(event, ui){			
			var idOrdenVenta=ui.item.id;
			idov=ui.item.id;                        
			MostrarOrdenVenta(idOrdenVenta);
                        HabilitarBotonDescarga(idOrdenVenta)
			$('#imprimir').show();
			$('#btnEditarOrdenVenta').show();    
			$('.field-set').show();
		}
	});

        $('#btnDescargarOrdenVenta').click(function () {
            $('#imgEstadoDescarga').attr('src', '/imagenes/default-loader.gif');
            $.ajax({
                url: '/documento/generaGuiaMadreTxt',
                type: 'post',
                dataType: "json",
                data: {'idordenventa': idov},
                success: function (datos) {
                    if (datos['rspta'] == 1) {
                        $('#imgEstadoDescarga').attr('src', '/imagenes/check.png');
                    } else {
                        $('#imgEstadoDescarga').attr('src', '/imagenes/iconos/transp.png');
                    }       
                }, error: function (a, b, c) {
                    console.log(a);
                    console.log(b);
                    console.log(c);
                }
            });
        });
        
	$('#txtOrdenVentaEdicionGlobal').autocomplete({
		source: "/ordenventa/ListaGuiaMadre/",
		select: function(event, ui){
			
			var idOrdenVenta=ui.item.id;
			idov=ui.item.id;
			MostrarOrdenVentaEdicionGlobal(idOrdenVenta);
			$('#OrdenVentaEdicionGlobal').show();
			$('.field-set').show();
		}
	});


	
	$('#imprimir').click(function(e){
		$('.ocultador').hide();
		$('.ocultarImpresion').hide();
		guiar(idov);
		imprSelec('muestra');
		$('.ocultador').show();
		$('.ocultarImpresion').show();
	});

	$('#editarPorcentaje').live('click',function(e){
		e.preventDefault();
		
	});
	$('#btnEditarOrdenVenta').live('click',function(e){
		e.preventDefault();
		window.location.href='/ordenventa/editarordenventa/'+idov;
		
	});
	$('.pestaña').click(function(e){
		e.preventDefault();

	});

	$('.ocultador').live('click',function(){
		valorO=$(this).val();
		if ($(this).attr('checked')=="checked") {
			$('.'+valorO).hide();
			padre=$(this).parents('tr').addClass('ocultarImpresion');
			//padre.find('.contenedorOcultador').addClass('ocultarImpresion');
		}else{
			$('.'+valorO).show();
			padre.removeClass('ocultarImpresion');
		}
	});
        
        $('.ocultarComprobante').live('click',function(){
            if ($(this).attr('checked')!="checked") {
                $('#DxFactura').hide();
            } else {
                $('#DxFactura').show();
            }
        });

});

function HabilitarBotonDescarga(idOrdenVenta) {    
    $.ajax({
        url: '/sucursal/habilitarDescarga',
        type: 'post',
        dataType: "json",
        data: {'idordenventa': idOrdenVenta},
        success: function (datos) {            
            $('#imgEstadoDescarga').attr('src', '/imagenes/iconos/transp.png');
            if (datos['idsucursal'] > 0) {
                $('#btnDescargarOrdenVenta').show();
            } else {
                $('#btnDescargarOrdenVenta').hide();
            }
        }, error: function (a, b, c) {
            console.log(a);
            console.log(b);
            console.log(c);
        }
    });
}

function MostrarOrdenVenta(idordenventa){
	var ruta = "/ordenventa/CabeceraGuiaMadre/" + idordenventa;
	$.post(ruta, function(data){
		console.log(data);
		$('#tblDetalleOrdenVenta thead').html(data);
               
	});

	cargaDetalleOrdenVenta(idordenventa);
}

function cargaDetalleOrdenVenta(idordenventa){
	var ruta = "/ordenventa/DetalleGuiaMadre/" + idordenventa;
	$.post(ruta, function(data){
		$('#tblDetalleOrdenVenta2 tbody').html(data);	
	});
	cargaDetalleOrdenCobro(idordenventa);
}

function cargaDetalleOrdenCobro(idordenventa){
	var ruta = "/ordencobro/buscarDetalleOrdenCobroGuia/" + idordenventa;
	$.post(ruta, function(data){
		$('#tblDetalleOrdenCobro tbody').html(data);	
	});
        MostrarFactura(idordenventa);
}

function MostrarFactura(idordenventa) {
        var ruta = "/documento/mostrardocumentoelectronico/" + idordenventa;
        $.ajax({
		url:ruta,
		type:'post',
                dataType: "json",
		data:{'idguia':idordenventa},
		success:function(datos){
                    if (datos['rspta'] == 1) {
                        $('#block_Factura').show();
                        $("#chkFactura").attr('checked', false);
                        $('#DxFactura').hide();
                        $('#fe_serie').val(datos['serie']);
                        $('#fe_correlativo').val(datos['correlativo']);
                        $('#fe_doc').val(datos['tipo']);
                        $('#fe_fecha').val(datos['fecha']);
                        $('#fe_monto').val(datos['facturado']);                        
                    } else {
                        $('#block_Factura').hide();
                    }
		}, error: function(a, b, c) {
                    console.log(a);
                    console.log(b);
                    console.log(c);
                }
	});
        $('#condicionesdeCompra').show();
}

function guiar(idov){
	esguiado=1;
	$.ajax({
		url:'/ordenventa/guiar',
		type:'post',
		datatype:'html',
		data:{'idov':idov,'esguiado':esguiado},
		success:function(resp){
			console.log(resp);
		}
	});
}

function porcentajeComision(idordenventa,porcentaje){
	
	
	$.ajax({
		url:'/ordenventa/porcentajeComision',
		type:'post',
		dataType:'html',
		data:{'idordenventa':idordenventa,'porcentaje':porcentaje},
		success:function(resp){
			console.log(resp);

		}
	});
}


// Edicion Global de Guia Madre
function MostrarOrdenVentaEdicionGlobal(idordenventa){
	var ruta = "/ordenventa/EdicionGlobal/" + idordenventa;
	$.post(ruta, function(data){
		$('#tblOrdenVenta thead').html(data);	
	});
}
function MostrarDetalleOrdenVentaEdicionGlobal(idordenventa){
	var ruta = "/ordenventa/EdicionGlobalDetalle/" + idordenventa;
	$.post(ruta, function(data){
		$('#tblDetalleOrdenVenta thead').html(data);	
	});
}
function MostrarOrdenVentaAprobaciones(idordenventa){
	var ruta = "/ordenventa/EdicionGlobalAprobaciones/" + idordenventa;
	$.post(ruta, function(data){
		$('#tblDetalleOrdenVenta thead').html(data);	
	});
}
function MostrarOrdenVentaCondicionesCredito(idordenventa){
	var ruta = "/ordenventa/EdicionGlobalCondicionesCredito/" + idordenventa;
	$.post(ruta, function(data){
		$('#tblDetalleOrdenVenta thead').html(data);	
	});
}
function MostrarOrdenVentaProgramacionPagos(idordenventa){
	var ruta = "/ordenventa/EdicionGlobalProgramacionPagos/" + idordenventa;
	$.post(ruta, function(data){
		$('#tblDetalleOrdenVenta thead').html(data);	
	});
}

function guardarObservacionFacturacion() {
    var observaciones = 'Sin observaciones';
    if ($('#idObservacionesFacturacion').val() != '') {
        observaciones = $('#idObservacionesFacturacion').val();
    }
    if ($('#idObservacionesFacturacion').data('idguia') > 0) {
        $.ajax({
            url: '/ordenventa/actualizarobservacionesfacturacion',
            type: 'post',
            datatype: 'html',
            data: {'idov': $('#idObservacionesFacturacion').data('idguia'), 'observaciones': observaciones},
            success: function (resp) {
            }
        });
    }
    $('#idThObservaciones').data('on', 0);
    $('#guardarObservaciones').hide();
    $('#idObservacionesFacturacion').hide();
    $('#editObservaciones').html(observaciones + ' <span id="titleObservaciones">[ Editar ]</span>');
    $('#editObservaciones').show();
}

