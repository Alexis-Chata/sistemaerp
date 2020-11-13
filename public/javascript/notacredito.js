$(document).ready(function(){
        actualizarNroNota();
	var iddocumento=0;
	
	var montoFactura=0;
        
        $('.cmbPanel').change(function () {
            if ($(this).val() == 1) {
                $('#bqdFactura').removeAttr('style');
                $('#bqdOv').attr('style', 'display: none');
            } else {
                $('#bqdFactura').attr('style', 'display: none');
                $('#bqdOv').removeAttr('style');
            }
        });
                
        $('#lstTipoDocumento').change(function () {
            console.log(1);
            if ($(this).val() == 1) {
                $('#txtFactura').attr('style', 'display: none');
                $('#txtFacturaElectronica').removeAttr('style');
                $('#blockCargado').removeAttr('style');
                //$('#leTipoFactura').html('F-');
                console.log('electronico');
            } else {
                $('#txtFacturaElectronica').attr('style', 'display: none');
                $('#blockCargado').attr('style', 'display: none');
                $('#txtFactura').removeAttr('style');
                //$('#leTipoFactura').html('');
                console.log('factura');
            }
            $('#blockBtnRegistrar').removeAttr('style');
        });
        
	$('#registrar').click(function(e){
		
		saldoFactura=parseFloat($('#saldoEscondido').val());
		Credito=parseFloat($('#credito').val());
		valor=saldoFactura-Credito;
			
		if (valor>0) {
			
			
		}else{
			e.preventDefault();
			alert('No puede ingresar una Cantidad mayor al saldo de la factura !');
		}
	});
        
        $('#tblfacturas').on('click', '.VerDetalle', function () {
            var iddocumento = $(this).data('id');
            verDetalledeFactura(iddocumento);
            
        })
        
        $('#txtFacturaElectronica').autocomplete({
		source: "/facturacion/autocompletefacturaelectronica/",
		select: function(event, ui){
			$('#txtIdOrden').val(ui.item.idorden);
			iddocumento=ui.item.id;
                        $('#txtIdDocumento').val(iddocumento);
			buscaOrden();
			buscaFactura(iddocumento);
		}
	});
        
	$('#txtFactura').autocomplete({
		source: "/facturacion/autocompletefactura/",
		select: function(event, ui){
			$('#txtIdOrden').val(ui.item.idorden);
			iddocumento=ui.item.id;
                        $('#txtIdDocumento').val(iddocumento);
			buscaOrden();
			buscaFactura(iddocumento);
		}
	});
        
        $('#txtOrdenVenta').autocomplete({
		source: "/ordenventa/ListaGuiaMadreConFactura/",
		select: function(event, ui){
			var idOrdenVenta=ui.item.id;
                        $('#txtIdOrden').val(idOrdenVenta);
			idov=ui.item.id;
			MostrarFacturas(idOrdenVenta);
		}
	});
        
        $('#btnSeleccionar').click(function () {
            iddocumento=$(this).data('iddoc');   
            $('#txtIdDocumento').val(iddocumento);
            buscaOrden();
            buscaFactura(iddocumento);
            actualizarNroNota();
            return false;
        });
        
        $('#txtSerie').change(function () {
            if ($('#lstTipoDocumento').val() == 1) {
                actualizarNroNota();
            }
        });
        
        $('#lstTipoDocumento').change(function () {
            actualizarNroNota();
        });
        
        $('#btnCerrarFactura').click(function () {
            $('#detalle').attr('style', 'display:none');
            $('html,body').animate({
                scrollTop: $("#tblfacturas").offset().top
            }, 500);
        });

});

function actualizarNroNota () {
    var ruta = "/facturacion/actualizarCorrelativos/";
    $.ajax({
            url:ruta,
            type:'post',
            data:{'idguia':0,'tipo':5, 'serie': $('#txtSerie').val()},
            success:function(datos){
                $('#blockCorrelativo').html(datos);
            }, error: function(a, b, c) {
                console.log(a);
                console.log(b);
                console.log(c);
            }
    });
}

function buscaOrden(){
	var ordenVenta = $('#txtIdOrden').val();
	var ruta = "/ordenventa/buscarFacturados/" + ordenVenta;
	//console.log(ruta);
	$.ajax({
		url:ruta,
		type:'get',
		dataType:'json',
		success:function(data){
			//console.log(data);
			$('#txtCliente').val(data.cliente);
			$('#txtRucDni').val(data.rucdni);
			$('#codigo').val(data.codigov);
			$('#importe').val(parseFloat(data.importeov).toFixed(2));
			$('#direccion').val(data.cdireccion);
			$('#ubicacion').val(data.lugar);
			$('#telefono').val(data.ctelefono);
			$('#idcliente').val(data.idcliente);			
		},
		error:function(error){
			console.log('error');
		}
	});
        
        
}

function verDetalledeFactura(documento) {
    $.ajax({
            url:'/documento/verDetalleFactura/' + documento,
            type:'get',
            success:function(resp){
                $('#detalle').removeAttr('style');
                $('#idtxtDocumento').html(documento);
                $('#btnSeleccionar').data('iddoc', documento);
                $('#tbldetalles tbody').html(resp);
                $('html,body').animate({
                    scrollTop: $("#detalle").offset().top
                }, 500);
            },
            error:function(error){
                    console.log('error');
            }
    });
}

function MostrarFacturas(ordenVenta) {
    $.ajax({
            url:'/documento/listafacturas/' + ordenVenta,
            type:'get',
            success:function(resp){
                $('#tblfacturas').removeAttr('style');
                $('#tblfacturas tbody').html(resp);
            },
            error:function(error){
                    console.log('error');
            }
    });
}

function buscaFactura(documento){	
    var ordenVenta = $('#txtIdOrden').val();
    var ruta = "/documento/buscardocumento/" + documento;
    var modo="";
    var montofacturado=0;
    $.ajax({
            url:ruta,
            type:'get',
            dataType:'json',
            success:function(resp){
                console.log(resp); 
                $('#numeroFacturaRelacionado').val(resp.numdoc);
                var txtLetra = "";
                if (resp.electronico==1) txtLetra="F";
                $('#opcSeleccion').html(resp.opcSelecciones);
                $('#numeroFactura').val(txtLetra+completarIzquierda(resp.serie, 3)+'-'+completarIzquierda(resp.numdoc,8));
                $('#porcentajeFacturacion').val(resp.porcentajefactura+'%');
                if (resp.modofactura==1) {
                        modo="Precio";
                } else if(resp.modofactura==2){
                        modo="Cantidad";
                }
                $('#modoFacturacion').val(modo);
                montofacturado=parseFloat(resp.montofacturado);
                //montoFactura=Math.round(parseFloat(resp.montofacturado)*100)/100;
                $('#montoFactura').val(resp.simbolo+' '+montofacturado.toFixed(2));
                $('#montoIGV').val(resp.simbolo+' '+parseFloat(resp.montoigv).toFixed(2));
                if (resp.saldo==null) {
                        montoCredito=0;
                }else{
                        montoCredito=parseFloat(resp.saldo);
                }			
                saldoFactura=montofacturado-montoCredito;
                $('#saldoEscondido').val(saldoFactura);
                $('#saldo').val(resp.simbolo+' '+saldoFactura.toFixed(2));
                $('#credito').focus();
                
                if (resp.electronico == 1) {
                    if (resp.esCargado==1) {
                        $('#blockCargado input').attr('style', 'color: blue; font-weight: 700');
                        $('#blockBtnRegistrar').removeAttr('style');
                        $('#blockCargado input').val('CARGADO');
                    } else {
                        $('#blockCargado input').attr('style', 'color: red; font-weight: 700');
                        $('#blockCargado input').val('SIN CARGAR');
                        $('#blockBtnRegistrar').attr('style', 'display: none');
                    }
                }
                $('html,body').animate({
                    scrollTop: $("#Datos").offset().top
                }, 500);
            },
            error:function(a,b,c){
                    console.log(a);
                    console.log(b);
                    console.log(c);
            }
    });
}