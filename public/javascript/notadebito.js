var nroitem = 0;
var nroId = 1;
$(document).ready(function(){
        actualizarNroNota();
	var iddocumento=0;
	
	var montoFactura=0;
        
        $('#lstMotivoNotaCredito').change(function () {
            $('.Ocultar').attr('style', 'display: none');
            $('.Mostrar').attr('style', 'display: none');
            if ($(this).val()>=1&&$(this).val()<=3) {
                $('#idLiLetras').removeAttr('style');
                $('.Mostrar').removeAttr('style');
            } else if ($(this).val()==4) {
                $('#idLiCheque').removeAttr('style'); 
                $('.Mostrar').removeAttr('style');
            } else if ($(this).val()==5) {
                $('#idLiProducto').removeAttr('style');
                $('.Mostrar').removeAttr('style');
            } else if ($(this).val()==6) {
                $('#idLiAdicional').removeAttr('style');
                $('.Mostrar').removeAttr('style');
            }   
            reiniciarNotaDebito();
        });
        
        $('.cmbPanel').change(function () {
            if ($(this).val() == 1) {
                $('.clFE').removeAttr('style');
                $('#clOV').attr('style', 'display: none');
                $('#bqdOv').attr('style', 'display: none');
            } else {
                $('.clFE').attr('style', 'display: none');
                $('#clOV').removeAttr('style');
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
           
        $('#txtidLiLetras').keypress(function (e) {
            if(e.which == 13) {
                $('#btnanadirregistro').click();
                e.preventDefault();
            }            
        });   
        
        $('#txtidLiProducto').keypress(function (e) {
            if(e.which == 13) {
                $('#btnanadirregistro').click();
                e.preventDefault();
            }            
        }); 
        
        $('#txtidLiCheque').keypress(function (e) {
            if(e.which == 13) {
                $('#btnanadirregistro').click();
                e.preventDefault();
            }            
        }); 
        
        $('#txtidLiAdicional').keypress(function (e) {
            if(e.which == 13) {
                $('#btnanadirregistro').click();
                e.preventDefault();
            }            
        }); 
        
        $('#txtidLiCantidad').keypress(function (e) {
            if(e.which == 13) {
                if ($(this).val().length==0) $(this).val('1');
                $('#btnanadirregistro').click();
                e.preventDefault();
            }            
        }); 
        
        $('#txtidLiPrecio').keypress(function (e) {
            if(e.which == 13) {
                $('#btnanadirregistro').click();
                e.preventDefault();
            }            
        }); 
           
	$('#registrar').click(function(e){
            if ($('#tblLeyenda tbody').html().length==0) {
                e.preventDefault();
		alert('La leyenda de la nota de debito, esta vacia.');
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
    
        $("#txtidLiLetras").autocomplete({
            source: function (request, response) {
                $.ajax({
                    url: "/facturacion/buscarletranotadebito",
                    dataType: "json",
                    data: {
                        term: request.term,
                        codigov:$('#codigo').val()
                    },
                    success: function (data) {
                        response($.map(data, function (item) {
                            return {
                                label: item.label,
                                value: item.value
                            }
                        }));
                    }, select: function(event, ui){
                        $('#btnanadirregistro').click();
                    }
                });
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
            //if ($(this).val() == 1) {
                actualizarNroNota();
            /*} else {
                $('#blockCorrelativo').html('<li><label>Correlativo 1</label><input type="text" name="NotaCredito[numdoc]" maxlength="6" size="6" required></li>');
            }*/            
        });
        
        $('#btnCerrarFactura').click(function () {
            $('#detalle').attr('style', 'display:none');
            $('html,body').animate({
                scrollTop: $("#tblfacturas").offset().top
            }, 500);
        });
        
        $('#btnanadirregistro').click(function () {
            if (verificarCampo() && nroitem<25) {
                if ($('#lstMotivoNotaCredito').val()>=1&&$('#lstMotivoNotaCredito').val()<=3) {
                    anadirCampo($('#txtidLiLetras').val(), $('#txtidLiCantidad').val(), $('#txtidLiPrecio').val());
                } else if ($('#lstMotivoNotaCredito').val()==4) {
                    anadirCampo($('#txtidLiCheque').val(), $('#txtidLiCantidad').val(), $('#txtidLiPrecio').val());
                } else if ($('#lstMotivoNotaCredito').val()==5) {
                    anadirCampo($('#txtidLiProducto').val(), $('#txtidLiCantidad').val(), $('#txtidLiPrecio').val());
                } else if ($('#lstMotivoNotaCredito').val()==6) {
                    anadirCampo($('#txtidLiAdicional').val(), $('#txtidLiCantidad').val(), $('#txtidLiPrecio').val());
                }                 
                verificarCampo();             
            }
        });
        
        $('#tblLeyenda').on('click', '.eliminarCol', function () {
            nroitem--;
            calcularTotal(parseFloat($(this).data('total'))*-1); 
            $('#ItemsTotal').html(nroitem + " de 25 Item's");
            $('#colAgr' + $(this).data('id')).remove();
        });

});

function actualizarNroNota () {
    var ruta = "/facturacion/actualizarCorrelativos/";
    $.ajax({
            url:ruta,
            type:'post',
            data:{'idguia':0,'tipo':6, 'serie': $('#txtSerie').val()},
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
	var ruta = "/documento/buscardocumentosinsaldo/" + documento;
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
			$('#numeroFactura').val(txtLetra+completarIzquierda(resp.serie, 3)+'-'+completarIzquierda(resp.numdoc,8));
			$('#porcentajeFacturacion').val(resp.porcentajefactura+'%');
			if (resp.modofactura==1) {
				modo="Precio";
			}else if(resp.modofactura==2){
				modo="Cantidad";
			}
			$('#modoFacturacion').val(modo);
			montofacturado=parseFloat(resp.montofacturado);
			//montoFactura=Math.round(parseFloat(resp.montofacturado)*100)/100;
			$('#FechEmsion').val(resp.fechadoc);
                        $('#montoFactura').val(resp.simbolo+' '+montofacturado.toFixed(2));
			$('#montoIGV').val(resp.simbolo+' '+parseFloat(resp.montoigv).toFixed(2));
                        $('#montoNeto').val(resp.simbolo+' '+parseFloat(montofacturado-resp.montoigv).toFixed(2));
			$('#lstMotivoNotaCredito').removeAttr('disabled');
                        $('#credito').focus();
                        
                        reiniciarNotaDebito();
                        console.log(resp);
                        if (resp.esImpreso==1 || (resp.electronico == 1 && resp.esCargado == 1)) {
                            $('#blockFactura').removeAttr('style');
                            $('#dxFactura').html('Datos de la Factura');
                            $('#blockbtnRegistrar').attr('style', 'float: right');
                        } else {
                            $('#blockFactura').attr('style', 'background: #fff0f0');
                            $('#dxFactura').html('Factura Sin Cargar');
                            $('#blockbtnRegistrar').attr('style', 'display: none');
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

function verificarCampo() {
    if ($('#lstMotivoNotaCredito').val()>=1&&$('#lstMotivoNotaCredito').val()<=3) {
        if ($('#txtidLiLetras').val().length ==0) {
            $('#txtidLiLetras').focus();
            return false;
        }        
    } else if ($('#lstMotivoNotaCredito').val()==4) {
        if ($('#txtidLiCheque').val().length ==0) {
            $('#txtidLiCheque').focus();
            return false;
        } 
    } else if ($('#lstMotivoNotaCredito').val()==5) {
        if ($('#txtidLiProducto').val().length ==0) {
            $('#txtidLiProducto').focus();
            return false;
        }
    } else if ($('#lstMotivoNotaCredito').val()==6) {
        if ($('#txtidLiAdicional').val().length ==0) {
            $('#txtidLiAdicional').focus();
            return false;
        }
    }
    if ($('#txtidLiPrecio').val().length == 0) {
        $('#txtidLiPrecio').focus();
        return false;
    }
    if ($('#txtidLiCantidad').val().length == 0) $('#txtidLiCantidad').val('1');
    return true;    
}

function anadirCampo(descripcion, cantidad, precio) {
    var precioigv = parseFloat(precio*0.18);
    var total = (parseFloat(precio*cantidad)).toFixed(2);
    var nuevoregistro = '<tr id="colAgr' + nroId + '">';
    nuevoregistro += '<td>' + (nroitem+1) + '</td>';
    nuevoregistro += '<td><input type="hidden" name="Descripciones[]" value="' + descripcion + '">' + descripcion + '</td>';
    nuevoregistro += '<td>NIU</td>';
    nuevoregistro += '<td><input type="hidden" name="Cantidades[]" value="' + cantidad + '">' + cantidad + '</td>';
    nuevoregistro += '<td><input type="hidden" name="Precios[]" value="' + precio + '">' + (precio-precioigv).toFixed(2) + '</td>';
    nuevoregistro += '<td>' + precioigv.toFixed(2) + '</td>';
    nuevoregistro += '<td>' + total + '</td>';
    nuevoregistro += '<td><img src="/imagenes/eliminar.gif" class="eliminarCol" data-id="' + nroId + '" data-total="' + total + '"></td>';
    nuevoregistro += '</tr>';
    nroitem++;
    nroId++;
    $('#ItemsTotal').html(nroitem + " de 25 Item's");
    $('#tblLeyenda tbody').append(nuevoregistro);
    calcularTotal(total);
    reiniciarCampos();   
}

function calcularTotal(valor) {
    var total = parseFloat($('#montoTotal').val()) + parseFloat(valor);
    var igvtotal = parseFloat(total*0.18);
    $('#tblmontoNeto').val((parseFloat(total-igvtotal)).toFixed(2));
    $('#tblmontoIgv').val(igvtotal.toFixed(2));
    $('#montoTotal').val(total.toFixed(2));
}

function reiniciarNotaDebito() {
    $('#tblLeyenda tbody').html('');
    $('#montoTotal').val('0.00');
    $('#tblmontoIgv').val('0.00');
    $('#tblmontoNeto').val('0.00');
    $('#ItemsTotal').html("0 de 25 Item's");
    reiniciarCampos();
    nroitem = 0;
    nroId = 0;
}  

function reiniciarCampos() {
    $('#txtidLiLetras').val('');
    $('#txtidLiProducto').val('');
    $('#txtidLiCheque').val('');
    $('#txtidLiAdicional').val('CARGOS ADMINISTRATIVOS ADICIONALES');
    $('#txtidLiCantidad').val('1');
    $('#txtidLiPrecio').val('');
}