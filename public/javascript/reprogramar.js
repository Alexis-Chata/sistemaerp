$(document).ready(function () {
    // Llamadas a funciones
    var idcodigoverificacion = 0;
    detalleprotesto = $('#detalleprotesto');
    idordenventa = $('#txtIdOrden');
    idcliente = $('#idcliente');
    idcobrador = $('#lstcobrador');
    contenerdorProtestado = $('#contenerdorProtestado');
    contenedorRenovado = $('#contenedorRenovado');
    tipoPago = $('#tipoPago');
    montoporcentaje = $('#montoporcentaje');
    lblDias = $('#lblDias');
    diasVencimiento = $('#diasVencimiento');
    verificadorGlobal = false;
    var claseGlobal;
    var title = "Reprogramar";
    var valorRestante = 0;
    minimo = 0;
    maximo = 0;
    valorActividad = false;
    fecharenovado = $('#fecharenovado');
    montorenovado = $('#montorenovado');
    contenedorCargarGasto=$('#contenedorCargarGasto');
    contenedorCargarGasto.hide();

    padregeneral = "";
    montogeneral = 0;
    formacobrogeneral = "";
    iddetalleordencobrogeneral = "";
    numeroletrageneral = "";
    valorLetra = 0;
    valorSaldo = 0;
    montoadicional = $('#montoadicional');
    fechavencimientogeneral = "";

    $(".cargargasto").live("click",function(e){
		e.preventDefault();
		elemento=$(this);
        padregeneral=$(this).parents('tr');
        console.log(padregeneral);
		padregeneral.addClass('active-row');
        $('#lstcobrador').val('');
		$('#contenedorCargarGasto').dialog('open');			
    });
    
    $('#contenedorCargarGasto').dialog({
		modal:true,
		width:500,
		autoOpen:false,
		buttons:{
			"Cargar":function(){
                inputcargargasto=$('#inputcargargasto').val();
                iddetalleordencobro=padregeneral.children('input.iddetalleordencobro').val();
				if ($('#inputcargargasto').val()!="") {
						if (inputcargargasto!=0) {
								$.ajax({
								url:'/ordencobro/cargargasto',
								type:'post',
								dataType:'json',
								data:{'importegasto':inputcargargasto,'iddetalleordencobro':iddetalleordencobro},
								success:function(resp){
                                    console.log(resp);
                                    $('#contenedorCargarGasto').dialog('close');
									cargaDetalleOrdenCobro2();
                                },
                                error: function (error) {
                                    $('#contenedorAnularGasto').dialog('close');
                                    cargaDetalleOrdenCobro2();
                                }
							});
						}else{
								$('#lb_msj').html('Ingrese un valor valido').css('color','red');
						}
				}else{
						$('#lb_msj').html('Ingrese monto de gasto').css('color','red');
				}                                
			}
		},
		close:function(){
			$('#inputcargargasto').val('');
			$('#lb_msj').val('');
			padregeneral.removeClass();
		}
    });
    
    $(".anulargasto").live("click",function(e){
		e.preventDefault();
		elemento=$(this);
        padregeneral=$(this).parents('tr');
        console.log(padregeneral);
		padregeneral.addClass('active-row');
        $('#lstcobrador').val('');
		$('#contenedorAnularGasto').dialog('open');			
    });
    
    $('#contenedorAnularGasto').dialog({
		modal:true,
		width:500,
		autoOpen:false,
		buttons:{
			"Aceptar":function(){
                inputcargargasto=$('#inputcargargasto').val();
                iddetalleordencobro=padregeneral.children('input.iddetalleordencobro').val();
                $.ajax({
                    url:'/ordencobro/anulargasto',
                    type:'post',
                    dataType:'json',
                    data:{'iddetalleordencobro':iddetalleordencobro},
                    success:function(resp){
                        console.log(resp);
                        $('#contenedorAnularGasto').dialog('close');
                        cargaDetalleOrdenCobro2();
                    },
                    error: function (error) {
                        $('#contenedorAnularGasto').dialog('close');
                        cargaDetalleOrdenCobro2();
                    }
                });                               
			}
		},
		close:function(){
			$('#inputcargargasto').val('');
			$('#lb_msj').val('');
			padregeneral.removeClass();
		}
	});

	$(document).on('keydown', 'input[pattern]', function(e){
		var input = $(this);
		var oldVal = input.val();
		var regex = new RegExp(input.attr('pattern'), 'g');
	  
		setTimeout(function(){
		  var newVal = input.val();
		  if(!regex.test(newVal)){
			input.val(oldVal); 
		  }
		}, 0);
	  });


    $('#txtOrdenVenta').autocomplete({
        source: "/ordenventa/PendientesxPagar/",
        select: function (event, ui) {
            $('#txtIdOrden').val(ui.item.id);
            $('#idOrdenVenta').val(ui.item.id);
            buscaOrdenCobro();
            cargaDetalleOrdenCobro2();
            cargartabladocumentos(ui.item.id);
        }
    });

    $('.variarSaldo').live('blur', function (e) {
        e.preventDefault();
        valorIngresado = calcularTotal();
        nuevoValor = valorRestante - valorIngresado;
        if (nuevoValor >= 0) {
            $('#valorRestante').val(nuevoValor.toFixed(2));
            $('#respModificar').html('')
        } else {
            $(this).val(0);
            $('#respModificar').html('El Valor hace que sobrepase el valor del Monto !').css('color', 'red');
        }
    });

    $('.LeAdiconales').live('click', function (e) {
        e.preventDefault();
        var cant = $(this).data('cantidad');
        var seleccion = "";
        for (var i = 0; i <= cant; i++) {
            if (i == cant)
                seleccion = " selected";
            $('#opcCantLetras').append('<option value=' + i + seleccion + '>' + i + '</option>');
        }
        $('#contenedorAdicional').dialog('open');
    });

    $('.modificar').live('click', function (e) {
        e.preventDefault();
        claseGlobal = $(this).attr('class');
        padregeneral = $(this).parents('tr');
        padregeneral.addClass('active-row');
        iddetalleordencobrogeneral = padregeneral.find('.iddetalleordencobro').val();
        valorSaldo = parseFloat(padregeneral.find('.valorSaldo').val());
        SMoneda = $('#SMoneda').val();
        fechavencimientogeneral = padregeneral.find('.fechavencimiento').html();
        console.log(fechavencimientogeneral);
        $('#nuevaFecha').val(fechavencimientogeneral);
        $('#valorModificar').html(SMoneda + valorSaldo.toFixed(2));
        $('.SMoneda').html(SMoneda);
        valorRestante = valorSaldo.toFixed(2);
        $('#valorRestante').val(valorRestante);
        $('#idModificar').val(iddetalleordencobrogeneral);
        $('#contendorAutorizacion').dialog('open');
    });

    $('.anular').live('click', function (e) {
        padregeneral = $(this).parents('tr');
        iddetalleordencobrogeneral = padregeneral.find('.iddetalleordencobro').val();
        padregeneral.addClass('active-row');
        claseGlobal = $(this).attr('class');
        console.log(claseGlobal);
        $('#lstTipoGasto').change();
        $('#contendorAutorizacion').dialog('open');
    });

    $('#contenedorAdicional').dialog({
        title: "Letras Adicional",
        modal: true,
        width: 380,
        autoOpen: false,
        buttons: {
            "Aceptar": function () {
                alert('oli boli');
            }
        },
        close: function () {
            $('#opcCantLetras').html('');
        }
    });

    $('#contenedorModificar').dialog({
        title: title,
        modal: true,
        width: 600,
        autoOpen: false,
        close: function () {
            limpiarModificar();
            if (claseGlobal == "modificar") {
                padregeneral.removeClass();
            }
            valorActividad = false;
            $('#nuevaLetra').attr('disabled', 'disabled').val('');
            $('#montoLetraNueva').attr('disabled', 'disabled').val('');

            $('#montoLetra').attr('disabled', 'disabled').val('');
            $('#diasMontoLetra').attr('disabled', 'disabled').val('');
        }
    });

    $('#prueba').click(function (e) {
        e.preventDefault();
        if (claseGlobal == "modificar") {
            //console.log($('#formularioModificar').serialize());
            if (validaTotalModificar(valorSaldo) == true) {
                if (validarVaciosModificar() == true) {
                    $("#prueba").css("pointer-events", "none");
                    modificar();
                } else {
                    alert('Ingrese las Cantidad con sus respectivos dias ');
                }
            } else {
                alert('La Cantidad asignada no es igual al Total de la Letra');
            }
        } else if (claseGlobal == "reprogramacionTotal") {
            //console.log($('#formularioModificar').serialize());
            montoTotalDeuda = parseFloat($('#totalImporteDeuda').val());
            if (validaTotalModificar(montoTotalDeuda) == true) {
                if (validarVaciosModificar() == true) {
                    $("#prueba").css("pointer-events", "none");
                    reprogramacionTotalDeuda();
                } else {
                    alert('Ingrese las Cantidad con sus respectivos dias ');
                }
            } else {
                alert('La Cantidad asignada no es igual al Total de la Letra');
            }
        }
    });

    $('#cantidadLetras').change(function () {
        cant = $(this).val();
        if (cant == 1) {
            $('#montoLetra').removeAttr('disabled');
            $('#diasMontoLetra').removeAttr('disabled');
            $('#nuevaLetra').attr('disabled', 'disabled').val('');
            $('#montoLetraNueva').attr('disabled', 'disabled').val('');
            $('#montoLetra').val($('#valorRestante').val());
            $('#valorRestante').val(0);
        } else if (cant == 2) {
            $('#nuevaLetra').removeAttr('disabled');
            $('#montoLetraNueva').removeAttr('disabled');

            $('#montoLetra').attr('disabled', 'disabled').val('');
            $('#diasMontoLetra').attr('disabled', 'disabled').val('');
            $('#montoLetraNueva').val($('#valorRestante').val());

            $('#valorRestante').val(0);
        } else {
            $('#nuevaLetra').attr('disabled', 'disabled').val('');
            $('#montoLetraNueva').attr('disabled', 'disabled').val('');

            $('#montoLetra').attr('disabled', 'disabled').val('');
            $('#diasMontoLetra').attr('disabled', 'disabled').val('');
            valorIngresado = calcularTotal();
            nuevoValor = valorRestante - valorIngresado;
            $('#valorRestante').val(nuevoValor.toFixed(2));
        }
    });

    $('.reprogramacionTotal').live('click', function (e) {
        e.preventDefault();
        claseGlobal = $(this).attr('class');
        title = "Reprogramacion Total de la Deuda";
        fechaGiro = $('#fechavencimiento').val();
        console.log(fechaGiro);
        console.log($('#formularioModificar').serialize());
        SMoneda = $('#SMoneda').val();
        $('.SMoneda').html(SMoneda);
        montoTotalDeuda = parseFloat($('#totalImporteDeuda').val());
        //if (parseFloat(montoTotalDeuda).toFixed(2)>0) {
        $('#nuevaFecha').val(fechaGiro);
        $('#valorModificar').html(SMoneda + montoTotalDeuda.toFixed(2));
        valorRestante = montoTotalDeuda.toFixed(2);
        $('#valorRestante').val(valorRestante);
        $('#valorEnvio').val(valorRestante);
        $('#contendorAutorizacion').dialog('open');
        //}else{
        //alert('El Importe de la deuda es cero');
        //}
    });
    
    $('#cbAutorizacion').change(function () {
        if ($(this).val() == 2) {
            $('#blockCodigoVerificacion').hide();
            $('#blockContrasenapersonal').show();
        } else {
            $('#blockCodigoVerificacion').show();
            $('#blockContrasenapersonal').hide();
        }
    });

    $('#contendorAutorizacion').dialog({
        autoOpen: false,
        modal: true,
        width: 350,
        buttons: {
            "Aceptar": function () {
                if ($('#cbAutorizacion').val() == 2) {
                    contrasena = $('#contrasenapersonal').val();
                } else {
                    contrasena = $('#contrasena').val();
                }
                if (contrasena != "") {
                    validado = validarAutorizacion(contrasena, $('#cbAutorizacion').val());
                    if (validado == true) {
                        //$('#respVerificacion').html('Usuario Correcto').css('color','green');
                        valorActividad = true;
                        alert('Código verificacion correcto');
                        if (claseGlobal == 'anular') {
                            claseGlobal == '';
                            $('#tipoGasto').dialog('open');
                            console.log(iddetalleordencobrogeneral);
                            console.log('anulado');
                            $('#contendorAutorizacion').dialog('close');
                        } else if (claseGlobal == 'modificar') {
                            claseGlobal == '';
                            $('#contenedorModificar').dialog('open');
                            console.log(iddetalleordencobrogeneral);
                            console.log('modificar');
                            $('#contendorAutorizacion').dialog('close');

                        } else if (claseGlobal == "reprogramacionTotal") {
                            $('#contenedorModificar').dialog('open');
                            console.log(iddetalleordencobrogeneral);

                            $('#contendorAutorizacion').dialog('close');
                        }
                        $('#respVerificacion').html('');
                    } else {
                        $('#respVerificacion').html('Código de verificación incorrecto.').css('color', 'red');
                    }
                } else {
                    $('#respVerificacion').html('Ingrese código de verificación.').css('color', 'red');
                }
            }
        },
        close: function () {
            $('#usuario').val('');
            $('#contrasena').val('');
            $('#respVerificacion').html('');
            if (valorActividad == false && claseGlobal == 'anular') {
                padregeneral.removeClass();
                claseGlobal = '';
            }
            if (valorActividad == false && claseGlobal == "modificar") {
                padregeneral.removeClass();
            }
        }
    })

    $('#imprimir').click(function (e) {
        e.preventDefault();
        $('.anular').hide();
        $('.modificar').hide();
        $('#cliente').hide();
        $('#datosingreso').hide();
        imprimircliente();
        $('#imprimirCliente').show();
        $('table tr td, table tr th').css('font-family', 'courier');
        imprSelec('muestra');
        $('table tr td, table tr th').css('font-family', 'calibri');
        $('#cliente').show();
        $('.anular').show();
        $('.modificar').show();
        $('#imprimirCliente').hide();
    });

    $('#lstTipoGasto').change(function () {
        var idOrdenVenta = $('#txtIdOrden').val();
        var idTipoGasto = $(this).val();
        var resp = buscarValorGasto(idOrdenVenta, idTipoGasto);
        $('#lblImporteGasto').html('Importe : ' + resp.importe);
        $('#importeGasto').val(resp.importe);
        $('#idOrdenGasto').val(resp.idordengasto);
    });

    $('#tipoGasto').dialog({
        autoOpen: false,
        modal: true,
        width: 350,
        buttons: {
            "Aceptar": function () {
                var datos = traerProgramacion(iddetalleordencobrogeneral);
                var importeGasto = parseFloat($('#importeGasto').val()).toFixed(2);
                var idOrdenGasto = $('#idOrdenGasto').val();
                //console.log('el : '+idOrdenGasto);
                //console.log('importe gasto : '+importeGasto);
                //console.log('importe cobro : '+datos.importedoc);
                var nuevoImporte = parseFloat(importeGasto).toFixed(2) - parseFloat(datos.importedoc).toFixed(2);
                //console.log('importe cobro : '+nuevoImporte);
                if (parseInt(idOrdenGasto) < 0) {
                    alert('Hubo algun error');
                } else if (nuevoImporte >= 0) {

                    console.log('nuevo importe : ' + nuevoImporte);
                    anular(iddetalleordencobrogeneral, idOrdenGasto, nuevoImporte);
                } else {
                    alert('No se pudo grabar debido a que el importe a anular es mayor que el monto del gasto seleccionado');
                }
            }
        },
        close: function () {

            if (claseGlobal == 'anular') {
                padregeneral.removeClass();
                claseGlobal = '';
            }
            if (valorActividad == false && claseGlobal == "modificar") {
                padregeneral.removeClass();
            }
        }
    })

});

function buscarValorGasto(idOrdenVenta, idTipoGasto) {
    var retorno = new Object();
    $.ajax({
        url: '/ordengasto/buscarValorGasto',
        type: 'post',
        dataType: 'json',
        async: false,
        data: {'idOrdenVenta': idOrdenVenta, 'idTipoGasto': idTipoGasto},
        success: function (resp) {
            //console.log(resp);
            retorno = resp;
        }
    });
    return retorno;
}

function buscaOrdenCobro() {
    var ordenVenta = $('#txtIdOrden').val();
    var ruta = "/ordencobro/buscarxOrdenVenta/" + ordenVenta;
    $.getJSON(ruta, function (data) {
        $('#razonsocial').val(data.razonsocial);
        $('#idcliente').val(data.idcliente);
        $('#ruc').val(data.ruc);
        $('#codigo').val(data.codcliente);
        $('#codantiguo').val(data.codantiguo);
        $('#codigov').val(data.codigov);
        $('.inline-block input').exactWidth();
    });
}

function cargaDetalleOrdenCobro2() {
    var ordenVenta = $('#txtIdOrden').val();
    var ruta = "/ordencobro/buscaDetalleOrdencobroReprogramar/" + ordenVenta;
    $.post(ruta, function (data) {
        $('#tblDetalleOrdenCobro tbody').html(data);
    });

}

function imprimircliente() {
    $('#imprimirCliente').html(
            '<fieldset>' +
            '<legend>Datos del Cliente</legend>' +
            'Orden Venta:<input type="text" value="' + $('#txtOrdenVenta').val() + '"  >' +
            'Código:<input type="text" value="' + $('#codigo').val() + '"  >' +
            'Razon Social:<input type="text"    size="40" value="' + $('#razonsocial').val() + '">' +
            'Número de RUC:<input type="text"  value="' + $('#ruc').val() + '">' +
            'Codigo Dakkar:<input type="text" value="' + $('#codantiguo').val() + '">' +
            'Nombre del Cobrador:<input type="text" value="CELESTIUM">' +
            '' +
            '</fieldset>'
            );
}

function verificarCobro() {
    idOrdenVenta = $('#txtIdOrden').val();
    $.ajax({
        url: '/ordencobro/verificarCobro',
        type: 'post',
        dataType: 'html',
        data: {'idOrdenVenta': idOrdenVenta},
        success: function (resp) {

            console.log(resp);
        }
    });
}

function anular(iddetalleordencobro, idOrdenGasto, nuevoImporte) {
    $.ajax({
        url: '/ingresos/anular',
        type: 'post',
        async: false,
        dataType: 'html',
        data: {'iddetalleordencobro': iddetalleordencobro, 'idOrdenGasto': idOrdenGasto, 'nuevoImporte': nuevoImporte},
        success: function (resp) {
            console.log(resp);
            cargaDetalleOrdenCobro2();
            verificarCobro();
            finalizarAutorizacion();
            $('#tipoGasto').dialog('close');
        },
        error: function (error) {

        }
    });
}

function traerProgramacion(idDetalleOrdenCobro) {
    //console.log(data);
    var retorno = new Object();
    $.ajax({
        url: '/ordencobro/traerProgramacion',
        type: 'post',
        async: false,
        dataType: 'json',
        data: {'idDetalleOrdenCobro': idDetalleOrdenCobro},
        success: function (resp) {
            retorno = resp;
        }
    });
    return retorno;
}

function validarAutorizacion(contrasena, tipo) {
    if (contrasena.length > 0) {
        var retorno = true;
        $.ajax({
            url: '/ingresos/reprogramar',
            type: 'post',
            async: false,
            dataType: 'json',
            data: {'tipo': tipo, 'contrasena': contrasena, 'idordenventa': $('#txtIdOrden').val()},
            success: function (resp) {
                console.log(resp);
                retorno = resp.verificacion;
                idcodigoverificacion = resp.idcodigoverificacion;
            },
            error: function (error) {
                console.log('error');
            }
        });
    } else {
        var retorno = false;
    }
    return retorno;
}

function finalizarAutorizacion () {
    if (idcodigoverificacion > 0) {
        $.ajax({
            url: '/creditos/finalizarautorizacion',
            type: 'post',
            async: false,
            dataType: 'json',
            data: {'idautorizacion': idcodigoverificacion},
            success: function (resp) {
            },
            error: function (error) {
                console.log('error');
            }
        });
    }
}

function modificar() {
    $.ajax({
        url: '/ingresos/modificar',
        type: 'post',
        dataType: 'html',
        data: $('#formularioModificar').serialize(),
        success: function (resp) {
            console.log(resp);
            cargaDetalleOrdenCobro2();
            finalizarAutorizacion();
            $('#contenedorModificar').dialog('close');
            $("#prueba").css("pointer-events", "auto");
        },
        error: function (error) {
            console.log('error');
        }
    });
}
function reprogramacionTotalDeuda() {
    $.ajax({
        url: '/ingresos/reprogramacionTotalDeuda',
        type: 'post',
        dataType: 'html',
        data: $('#formularioModificar').serialize(),
        success: function (resp) {
            console.log(resp);
            cargaDetalleOrdenCobro2();
            verificarCobro();
            finalizarAutorizacion();
            $('#contenedorModificar').dialog('close');
            $("#prueba").css("pointer-events", "auto");
        },
        error: function (error) {
            console.log('error');
        }
    });
}

function validaTotalModificar(valorLetra) {
    valorLe = valorLetra.toFixed(2);
    valorRetorno = true;
    valorMontoLetras = 0;

    if ($('#montoContado').val() != "") {
        valorMontoContado = parseFloat($('#montoContado').val());
    } else {
        valorMontoContado = 0;
    }
    if ($('#montoCredito0').val() != "") {
        valorMontoCredito0 = parseFloat($('#montoCredito0').val());
    } else {
        valorMontoCredito0 = 0;
    }
    if ($('#montoCredito1').val() != "") {
        valorMontoCredito1 = parseFloat($('#montoCredito1').val());
    } else {
        valorMontoCredito1 = 0;
    }
    if ($('#montoCredito2').val() != "") {
        valorMontoCredito2 = parseFloat($('#montoCredito2').val());
    } else {
        valorMontoCredito2 = 0;
    }
    if ($('#montoCredito3').val() != "") {
        valorMontoCredito3 = parseFloat($('#montoCredito3').val());
    } else {
        valorMontoCredito3 = 0;
    }
    if ($('#montoCredito4').val() != "") {
        valorMontoCredito4 = parseFloat($('#montoCredito4').val());
    } else {
        valorMontoCredito4 = 0;
    }

    if ($('#cantidadLetras').val() == 1) {
        if ($('#montoLetra').val() != "") {
            valorMontoLetras = parseFloat($('#montoLetra').val());
        } else {
            valorMontoLetras = 0;
        }
    } else if ($('#cantidadLetras').val() == 2) {
        if ($('#montoLetraNueva').val() != "") {
            valorMontoLetras = parseFloat($('#montoLetraNueva').val());
        } else {
            valorMontoLetras = 0;
        }
    }

    valorTotal = valorMontoContado + valorMontoCredito0 + valorMontoCredito1 + valorMontoCredito2 + valorMontoCredito3 + valorMontoCredito4 + valorMontoLetras;
    console.log(valorTotal);
    if (valorTotal.toFixed(2) != valorLe) {
        valorRetorno = false;
    }
    return valorRetorno;
}

function limpiarModificar() {
    $('#flete').val('');
    $('#diasFlete').val('');
    $('#envioSobre').val('');
    $('#diasEnvioSobre').val('');
    $('#gastoBancario').val('');
    $('#diasGastoBancario').val('');
    $('#costoMantenimiento').val('');
    $('#diasCostoMantenimiento').val('');
    $('#montoContado').val('');
    $('#montoCredito0').val('');
    $('#diasCredito0').val('');
    $('#montoCredito1').val('');
    $('#diasCredito1').val('');
    $('#montoCredito2').val('');
    $('#diasCredito2').val('');
    $('#montoCredito3').val('');
    $('#diasCredito3').val('');
    $('#montoCredito4').val('');
    $('#diasCredito4').val('');
    $('#montoLetra').val('');
    $('#diasMontoLetra').val('');
    $('#cantidadLetras').val('');
    $('#nuevaLetra').val('');
    $('#valorModificar').html('');
    $('#nuevaFecha').val('');
    $('#idModificar').val('');
    $('#montoLetraNueva').val('');
}
function validarVaciosModificar() {
    var retorno = true;
    if ($('#flete').val() != "" && $('#flete').val() != 0) {
        if ($('#diasFlete').val() != "" && $('#diasFlete').val() != 0) {

        } else {
            retorno = false;
        }
    }
    if ($('#envioSobre').val() != "" && $('#envioSobre').val() != 0) {
        if ($('#diasEnvioSobre').val() != "" && $('#diasEnvioSobre').val() != 0) {

        } else {
            retorno = false;
        }
    }
    if ($('#gastoBancario').val() != "" && $('#gastoBancario').val() != 0) {
        if ($('#diasGastoBancario').val() != "" && $('#diasGastoBancario').val() != 0) {

        } else {
            retorno = false;
        }
    }
    if ($('#costoMantenimiento').val() != "" && $('#costoMantenimiento').val() != 0) {
        if ($('#diasCostoMantenimiento').val() != "" && $('#diasCostoMantenimiento').val() != 0) {

        } else {
            retorno = false;
        }
    }
    if ($('#montoCredito0').val() != "" && $('#montoCredito0').val() != 0) {
        if ($('#diasCredito0').val() != "" && $('#diasCredito0').val() != 0) {

        } else {
            retorno = false;
        }
    }
    if ($('#montoCredito1').val() != "" && $('#montoCredito1').val() != 0) {
        if ($('#diasCredito1').val() != "" && $('#diasCredito1').val() != 0) {

        } else {
            retorno = false;
        }
    }
    if ($('#montoCredito2').val() != "" && $('#montoCredito2').val() != 0) {
        if ($('#diasCredito2').val() != "" && $('#diasCredito2').val() != 0) {

        } else {
            retorno = false;
        }
    }
    if ($('#montoCredito3').val() != "" && $('#montoCredito3').val() != 0) {
        if ($('#diasCredito3').val() != "" && $('#diasCredito3').val() != 0) {

        } else {
            retorno = false;
        }
    }
    if ($('#montoCredito4').val() != "" && $('#montoCredito4').val() != 0) {
        if ($('#diasCredito4').val() != "" && $('#diasCredito4').val() != 0) {

        } else {
            retorno = false;
        }
    }
    if ($('#montoLetra').val() != "" && $('#montoLetra').val() != 0) {
        if ($('#diasMontoLetra').val() != "" && $('#diasMontoLetra').val() != 0) {

        } else {
            retorno = false;
        }
    }
    if ($('#nuevaLetra').val() != "") {
        if ($('#montoLetraNueva').val() != "" && $('#montoLetraNueva').val() != 0) {

        } else {
            retorno = false;
        }
    }
    return retorno;
}

function calcularTotal() {
    var total = 0;
    $('.variarSaldo').each(function (e) {
        if ($(this).val() == "") {
            total += 0;
        } else {
            total += parseFloat($(this).val());
        }

        console.log(total);
    });
    //console.log(total);
    return total;
}

function cargartabladocumentos(idordenventa) {

    var ruta = "/documento/documentosxordenventa/" + idordenventa;
    $.post(ruta, function (data) {
        $('#tbldocumentos tbody').html(data);

    });
}
