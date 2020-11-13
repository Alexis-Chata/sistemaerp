$(document).ready(function(){
        $('#txtCodigoProductoj').focus();       
        
        /**/
	$('#btnRegistrar').click(function(e){
            
            if (!confirm('¿ ESTÁ SEGURO DE REGISTRAR LA FACTURA?')){
            }else{
                $('#frmGeneracionOrdenVenta').submit();
            }
            e.preventDefault();
	});
        /*Eliminar una fila de los productos ingresados*/
        $('body').on('click', '.btnEliminarProducto', function(e){
		e.preventDefault();
		$fila = $(this).parents('tr');
		var codigo =$fila.find('.codigo').val();
                eliminarValorArray(codigo,codigosProductos);
		$fila.remove();
	});
	
	/*Agregar producto a la guia de pedido*/
	$('#btnAgregarProduco').click(function(e){
		e.preventDefault();
                var idProducto = $('#txtIdProducto').val();
                var codigoProducto = $('#txtCodigoProductoj').val();
                var nombreProducto = $('#txtTituloProducto').val();
                var cantidadProducto = $('#txtCantidadProducto').val();
                var precioInicial = $('#txtPrecioInicial').val();
                
            var val = existeValorArray(idProducto,codigosProductos);

                if(!$('#txtIdProducto , #txtCantidadProducto').valida()){
                    return false;
                }else if(cantidadProducto == 0){
                    alert("La candidad debe ser diferente de 0");	
                    return false;
                }else if(precioInicial == 0 || precioInicial.length == 0){
                    alert("Debe ingresar el precio de costo inicial y debe ser diferente de cero");
                }else if(val){
                    $('#txtCodigoProductoj').val('');
                    $('#txtIdProducto').val('');
                    alert("Ya se registró este producto");	
                    $('#txtCodigoProductoj').focus();
                    return false;
                }else{
                    //console.log(idProducto);
                    var ruta = "/movimiento/verficarSaldoInicial/"+idProducto;
                    
                    $.post(ruta, function(data){
                        console.log(data);
                        if(data!=0){
                            alert("Número ya está registrado,verifique o llame a Sistemas anexo:125x");
                            $('#txtCodigoProductoj').focus();
                        }else{
                            // bacán:
                            codigosProductos.push(idProducto);

                            $('#tblDetalleOrdenVenta tbody tr:last').before('<tr>'+
                                    '<td><input type="hidden" name="txtIdProducto[]" class="codigo" value="'+idProducto+'">' + 
                                            idProducto +
                                    '</td>'+
                                    '<td>' + codigoProducto + '</td>'+
                                    '<td>' + nombreProducto + '</td>'+
                                    '<td><input type="hidden" name="txtCantidadHecha[]" value="' + cantidadProducto + '">' + cantidadProducto + '</td>'+
                                    '<td><input type="hidden" name="txtPrecioInicial[]" value="' + precioInicial + '">' + precioInicial + '</td>'+
                                    '<td><a href="#" class="btnEliminarProducto"><img src="/imagenes/eliminar.gif"></a></td>'+
                            '</tr>');
                            // Si pasa los proyectos se limpia el producto y se pone el cursor en la zona del siguiente producto:
                            $('#txtIdProducto').val('');
                            $('#txtCodigoProductoj').val('');
                            $('#txtTituloProducto').val('');
                            $('#txtCantidadProducto').val('');
                            $('#txtPrecioInicial').val('');
                            $('#txtCodigoProductoj').focus();
                        }
                    });
                }
	});
});
        

var codigosProductos = [];
var buenNumero = false;

function existeValorArray(id,cadena){
    var valor = false;
    cadena.length
    for(i=0;i<cadena.length;i++){
        if(cadena[i]==id){
            valor = true;
            break;
        }
    }
    return valor;
}

function eliminarValorArray(id,cadena){
    for(i=0;i<cadena.length;i++){
        if(cadena[i] == id){
            codigosProductos[i] = 'A';
            break;
        }
    }
    
    mostrarArray(codigosProductos);
}

function mostrarArray(cadena){
    for(i=0;i<cadena.length;i++){
        //console.log(cadena[i]);
    }
}

function verificarNumeroFactura(){
	var idCliente = $('#txtIdCliente').val();
	var ruta = "/cliente/posicionordenventa/"+ idCliente;
	$.post(ruta, function(data){
		$('#clienteposicion').html(data);
		var saldo=parseFloat($('#idsaldo').val());
		if(saldo<=0){
			$.msgbox("Alerta al Crear la ORDEN","EL CLIENTE TIENE SALDO S./ " + saldo+ "<br> Verifique el Saldo que necesita ANTES de empezar a registrar su orden.");
		}
	});
}

function verificaNumero(){
    var numero = $('#txtNumero').val();
    var ruta = "/movimiento/buscaNumeroFactura/"+ numero;
    $.post(ruta, function(data){
        if(data!=0){
            alert("Número ya está registrado,verifique o llame a Sistemas anexo:125");
            $('#txtNumero').focus();
            buenNumero = false;
        }else{
            buenNumero = true;
        }
    });
}