$(document).ready(function(){
	$('#liModoFacturacion').hide();
	$('.gbfacturacion').hide();
	$('#fsObservaciones').hide();
	var idOV="";
	var vPorcentaje="";
	var vTipo="";
        
        $('#chkInterno').change(function () {
            if($(this).prop('checked') ) {
                $('#letraDoc').html('');
                $('#blockCorrelativo').html('<li><label>Correlativo:</label><input type="text" name="Factura[numdoc]" maxlength="10" size="10" required="required" id="textNFactura"><li>');
            } else {
                $('#lstDoc').change();
                $('#blockCorrelativo').html('<li><label>Correlativo:</label><input type="text" maxlength="10" size="10" required="required" id="textNFactura" disabled=""><li>');
                actualizarCorrelativos();
            }
        });
        
        $('#aRefesh').click(function () {
            actualizarCorrelativos();
            return false;
        });
        
        $('#txtSerie').change(function () {
            actualizarCorrelativos();
        });
        
        $('#lstDoc').change(function () {
            if ($(this).val() == '') {
                $('#txtOrdenVenta').attr('disabled', 'disabled');
                $('#lstEmpresa').attr('disabled', 'disabled');
                $('#letraDoc').html('');
            } else {
                if(!$('#chkInterno').prop('checked')) {
                    if ($(this).val() == 1) {
                        $('#letraDoc').html('F-');
                    } else {
                        $('#letraDoc').html('B-');
                    }
                }
                actualizarCorrelativos();
                $('#txtOrdenVenta').removeAttr('disabled');
                $('#lstEmpresa').removeAttr('disabled');
            }
        });
        
	$('#txtOrdenVenta').autocomplete({
		source: "/ordenventa/autocomplete/",
		select: function(event, ui){
			$('#txtIdOrden').val(ui.item.id);
			buscaOrdenVenta();
			cargaDetalleOrdenVenta();
                        actualizarCorrelativos();
		}
                
	});
	$('#lstModoFacturacion').change(function(){
		idOV=$('#txtIdOrden').val();
		vPorcentaje=$('#txtPorcentajeFacturacion').val();
		vTipo=$('#lstModoFacturacion').val();
		$('#idOV').val(idOV);
		$('#vPorcentaje').val(vPorcentaje);
		$('#vTipo').val(vTipo);
		cambiaCondicionVenta();
	});

	$('#btnFactura').click(function(e){		
		if (idOV=="" || vPorcentaje=="" || vTipo=="" || $('#txtSerie').val()=="" || $('#textNFactura').val()=="" || $('#lstEmpresa').val()=="") {
			e.preventDefault();
		}else{
			
		}
	});
	$('#txtPorcentajeFacturacion').keyup(function(e){
		idOV=$('#txtIdOrden').val();
		vPorcentaje=$('#txtPorcentajeFacturacion').val();
		vTipo=$('#lstModoFacturacion').val();
		vDoc=$('#lstDoc').val();
		console.log(vDoc);
		$('#idOV').val(idOV);
		$('#vPorcentaje').val(vPorcentaje);
		$('#vTipo').val(vTipo);
		if (vPorcentaje==100) {
			console.log('entro');
			$('#lstModoFacturacion').removeAttr('required');
			$('#lstModoFacturacion').attr('disabled','disabled');
			$('#lstModoFacturacion').val(0);
			porcentaje = 100;
			modo = 0;
			idOrden =  $('#txtIdOrden').val();
			if(porcentaje == 100 && porcentaje>0 && modo ==0 && idOrden != ""){
                            var limit = -1;
                            if( $('#chkGenrarDocumento').prop('checked') ) {
                                limit = 1;
                            }
                            var url = "/facturacion/listaProductosGuiaRecuperado/" + idOrden;
                            listaProductosGuiaRecuperado(url, modo, porcentaje, limit);		
			}

		}else{
			$('#lstModoFacturacion').removeAttr('disabled');
			$('#lstModoFacturacion').attr('required','required');
			cambiaCondicionVenta();
		}
		if (vPorcentaje>100 || vPorcentaje==0 ) {
			$('#btnRegistrar').attr('disabled','disabled');
			$('#btnRegistrar').attr('title','El porcentaje es muy Alto');
			$('#btnRegistrar').css('color','red');
			$('#btnRegistrar').val('Deshabilitado');
		}else{
			$('#btnRegistrar').removeAttr('disabled');
			$('#btnRegistrar').removeAttr('title');
			$('#btnRegistrar').css('color','white');
			$('#btnRegistrar').val('Registrar');
		}
		
	});
	
	$('#lstEmpresa').change(function(e){
		valor2=$('#lstDoc').val();
		if (valor2==2) {
			//$('#vPorcentaje').removeAttr('disabled')
			$('#txtPorcentajeFacturacion').val(100);
			//$('#vPorcentaje').attr('disabled','disabled')
		}
	});

	$('#lstDoc').change(function(e){
		cambiarEstadoDocumento();
	});
	$('#btnRegistrar').click(function(e){
            if ($('#txtMsgRucDni').html() == '') {
                if ($('#txtDireccionEnvio').val()!="" && $('txtContacto').val()!="") {
			if (idOV=="" || vPorcentaje=="" || vPorcentaje>100 || vTipo=="" || $('#txtSerie').val()=="" || $('#textNFactura').val()=="" || $('#lstEmpresa').val()=="") {
				
			}else{
				window.location='/facturacion/generafactura/';
			}
		}else{
			e.preventDefault();
			alert('Ingrese el Contacto y Direccion');
		}
            } else {
                e.preventDefault();
                $('#txtRucDni').focus();
                alert('El cliente no tiene todos los datos ingresados de manera correcta');
            }
	});
        
        $('#thGenerarDocumento').on('change', '#chkGenrarDocumento', function () {
            actualizarCorrelativos();
            vPorcentaje=$('#txtPorcentajeFacturacion').val();
            var modo = $('#lstModoFacturacion option:selected').val();
            var limit = -1;
            if( $('#chkGenrarDocumento').prop('checked') ) {
                limit = 1;
            }
            var url = "/facturacion/listaProductosGuiaRecuperado/" + $('#txtIdOrden').val();
            listaProductosGuiaRecuperado(url, modo, vPorcentaje, limit);				
        });
        
        buscaOrdenVenta();
        verificarViene();
        cargaDetalleOrdenVenta();
});

function cambiarEstadoDocumento() {
    valor=$('#lstDoc').val();
    $('#txtSerie').val("");
    $('#textNFactura').val("");
    if (valor==2) {
            $('#lstModoFacturacion').removeAttr('required');
            $('#lstModoFacturacion').attr('disabled','disabled');
            $('#txtPorcentajeFacturacion').val(100);
            $('#lstModoFacturacion').val(0);
            $('#txtPorcentajeFacturacion').attr('disabled','disabled');
            porcentaje =100;
            modo = 0;
            idOrden =  $('#txtIdOrden').val();
            if(porcentaje == 100 && porcentaje>0 && modo ==0 && idOrden != ""){
                var limit = -1;
                if( $('#chkGenrarDocumento').prop('checked') ) {
                    limit = 1;
                }
                    var url = "/facturacion/listaProductosGuiaRecuperado/" + idOrden;
                    listaProductosGuiaRecuperado(url, modo, porcentaje, limit);

            }
    }else{
            $('#txtPorcentajeFacturacion').removeAttr('disabled');
    }
}

function verificarViene() {
    if ($("#xdxd").val() != '') {
        $("#txtCliente").removeAttr('readonly');
        $("#lstDoc").val(2);
        cambiarEstadoDocumento();
    }
}

function buscaOrdenVenta(){
    if ($('#txtIdOrden').val() != '') {
        var ordenVenta = $('#txtIdOrden').val();
	var ruta = "/ordenventa/buscarfactura2/" + ordenVenta;
	$.getJSON(ruta, function(data){
            $('#txtRucDni').removeClass('error');
            $('#txtMsgRucDni').html('');
            $('#txtRucDni').val('');
            if ($('#lstDoc').val() == 1) {
                $('#txtRucDni').val(data.ruc);
                if(data.ruc.length != 11) {
                    $('#txtRucDni').addClass('error');
                    $('#txtMsgRucDni').html('R.U.C. debe tener 11 digitos');
                }
            } else if ($('#lstDoc').val() == 2) {
                $('#txtRucDni').val(data.dni);
                if(data.dni.length != 8) {
                    $('#txtRucDni').addClass('error');
                    $('#txtMsgRucDni').html('DNI debe tener 8 digitos');
                }
            }
		$('#txtCliente').val(data.cliente);
		$('#idcliente').val(data.idcliente);
		$('#txtFechaGuia').val(data.fechaguia);
		$('#txtDireccionEnvio').val(data.direccionfiscal);
		$('#direccionInicial').val(data.direccionfiscal);
		$('#txtContacto').val(data.contacto);
		$('#contactoInicial').val(data.contacto);
		$('.inline-block input').exactWidth();
		if(data.porcentajefacturacion !=0){
			$('#txtPorcentajeFacturacion').val(data.porcentajefacturacion);
			$('#lstModoFacturacion option[value="' + data.modofacturacion + '"]').attr('selected', true);
			//$('.gbfacturacion:last').after(data.observaciones);
			$('.gbfacturacion').show();
		}else{
			$('.gbfacturacion').hide();
		}
		cargaContacto();
		cargaDirecciones()
	});
	$('#lstDireccion').change(function(){
		
		if ($(this).val()!="") {
			$('#txtDireccionEnvio').val($('#lstDireccion option:selected').text()).css('width','350px');
		}else{
			$('#txtDireccionEnvio').val($('#direccionInicial').val());
		}
		
	});
	$('#lstContacto').change(function(){
		
		if ($(this).val()!="") {
			$('#txtContacto').val($('#lstContacto option:selected').text()).css('width','350px');
		}else{
			$('#txtContacto').val($('#contactoInicial').val());
		}
		
	});
    }
}

function cargaDetalleOrdenVenta(){
    if ($('#txtIdOrden').val() != '') {
        var ordenVenta = $('#txtIdOrden').val();
	var ruta = "/facturacion/listaproductosguia/";
        var limit = -1;
        if( $('#chkGenrarDocumento').prop('checked') ) {
            limit = 1;
        }
        $.ajax({
		url:ruta,
		type:'post',
                dataType: "json",
		data:{'idguia':ordenVenta,'tipo':$('#lstDoc').val(), 'serie': $('#letraDoc').val(), 'limite': limit},
		success:function(datos){
                    $('#tblProductosGuia tbody').html(datos['detalleOV']);
                    $('#thGenerarDocumento').html(datos['chkUnion']);
                    if (datos['registrar'] == 1) {
                        $('#btnRegistrar').show();
                    } else {
                        $('#btnRegistrar').hide();
                    }	
		}, error: function(a, b, c) {
                    console.log(a);
                    console.log(b);
                    console.log(c);
                }
	});
    }
}
function cambiaCondicionVenta(){
	var porcentaje = parseInt($('#txtPorcentajeFacturacion').val());
	var modo = $('#lstModoFacturacion option:selected').val();
	var idOrden =  $('#txtIdOrden').val();
	if(porcentaje < 100 && porcentaje>0 && modo != "" && idOrden != ""){
            var limit = -1;
            if( $('#chkGenrarDocumento').prop('checked') ) {
                limit = 1;
            }
            var url = "/facturacion/listaProductosGuiaRecuperado/" + idOrden;
            listaProductosGuiaRecuperado(url, modo, porcentaje, limit);
	}
}
function listaProductosGuiaRecuperado(url, modo, porcentaje, limit) {
    $.ajax({
        url:url,
        type:'post',
        dataType: "json",
        data:{modo: modo, porcentaje: porcentaje, limite: limit},
        success:function(datos){
            $('#tblProductosGuia tbody').html(datos['detalleOV']);
            if (datos['registrar'] == 1) {
                $('#btnRegistrar').show();
            } else {
                $('#btnRegistrar').hide();
            }	
        }, error: function(a, b, c) {
            console.log(a);
            console.log(b);
            console.log(c);
        }
    });
}
function cargaDirecciones(){
	var idcliente=$('#idcliente').val();
	$.ajax({
		url:'/cliente/direccion_fiscal',
		type:'post',
		dataType:'html',
		async: false,
		data:{'idcliente':idcliente},
		success:function(resp){
			//console.log(resp);
			$('#lstDireccion').html(resp);
		},
		error:function(error){
			//console.log('error');
		}
	});

}
function cargaContacto(){
	var idcliente=$('#idcliente').val();
	$.ajax({
		url:'/cliente/contactos',
		type:'post',
		dataType:'html',
		async: false,
		data:{'idcliente':idcliente},
		success:function(resp){
			//console.log(resp);
			$('#lstContacto').html(resp);
		},
		error:function(error){
			//console.log('error');
		}
	});

}

function actualizarCorrelativos () {
    if ($('#lstDoc').val()>0&&$('#txtIdOrden').val()!=''&&!$('#chkInterno').prop('checked')) {
        var ruta = "/facturacion/actualizarCorrelativos/";
        var ordenVenta = $('#txtIdOrden').val();
        var limit = -1;
        if( $('#chkGenrarDocumento').prop('checked') ) {
            limit = 1;
        }
        $.ajax({
                url:ruta,
                type:'post',
                data:{'idguia':ordenVenta,'tipo':$('#lstDoc').val(), 'serie': $('#txtSerie').val(), 'limite': limit},
                success:function(datos){
                    $('#blockCorrelativo').html(datos);
                }, error: function(a, b, c) {
                    console.log(a);
                    console.log(b);
                    console.log(c);
                }
        });
    }
}