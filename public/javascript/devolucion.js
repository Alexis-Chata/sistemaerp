$(document).on('ready', function () {
    
    buscarXidOrdenventa($('#idtxtOV').val(), 1);
    $('#blockSubMotivo').hide();
            
    $('#txtOrdenVenta').autocomplete({
        source: "/ordenventa/ListaEstadoGuia/",
        select: function (event, ui) {
            var idOrdenVenta = ui.item.id;
            $('#idtxtOV').val(idOrdenVenta);
            buscarXidOrdenventa(idOrdenVenta, 1);
        }
    });
    
    $('#btnGuardar').click(function () {
        guardar();
    });
    
    $('#cmbfactura > select').change(function () {
        $('.TodasClases').attr('style', 'display:none');
        $('.C' + $(this).val()).removeAttr('style');
    });

    $('#grabarFactura').click(function () {
        $(this).addClass('inhabilitar_elemento');
        if ($('#txtNDevolucion').val().length == 0) {
            guardar();
        }
        if ($('#cmbfactura > select').val() > 0) {
            $.ajax({
                url: '/devolucion/grabaFactura/',
                type: 'post',
                dataType: 'json',
                data: {'idDocumento': $('#cmbfactura > select').val(), 'iddevolucion': $('#txtNDevolucion').val()},
                success: function (resp) {
                    $("#tblDetalles > tbody input").prop('disabled', false);
                    $('#contPE').show();
                    $('#cmbfactura').hide();                    
                    $('#contPE').html('Factura Electronica: <b style="color: red;">' + $('#cmbfactura > select option:selected').text() + '</b>');
                    $('#cmbfactura > select').html('');
                    $('#grabarFactura').removeClass();
                    $("#tblDetalles > tbody .save").attr('class', 'save');
                },
                error: function (a, b, c) {
                    console.log(a);
                    console.log(b);
                    console.log(c);
                }
            });
        } else {
            alert("La Factura Electronica No Ha Sido Cargada.");
        }
    });
    
    $('#idmotivodevolucion').change(function () {
        if($(this).val()>0){
            var idSubMotivoDevolucion=$(this).val();
            $.ajax({
                url: '/devolucion/cambiarSubMotivoDevolucion/',
                type: 'post',
                async: false,
                dataType: 'json',
                data: {
                    'idmotivodevolucion':idSubMotivoDevolucion,
                },
                success: function (resp) {                    
                    if (resp['tamanio'] == 0) {
                        $('#blockSubMotivo').hide();
                    } else {
                        $('#blockSubMotivo').show();
                    }
                    $('#idsubmotivodevolucion').html(resp['motivos']);
                },
                error: function (error) {
                    console.log(error)
                }
            });
        } else {
            $('#idsubmotivodevolucion').html('<option value="0">Seleccione una opcion</option>');
            $('#blockSubMotivo').hide();
        }
         console.log($(this).val());
    });
    
    $('.save').live('click', function (e) {
        e.preventDefault();
        $(this).addClass('inhabilitar_elemento');        
        if ($('#txtNDevolucion').val().length == 0) {
            guardar();
        }        
        var padre = $(this).parents('tr');
        var elemento = $(this);
        var idProducto = padre.find('.modificar').attr('id');
        var cant = padre.find('.modificar').val();
        padre.find('.modificar').val('');
        if (cant != 0) {
            elemento.attr('readonly', 'readonly');
            elemento.css('background-color', 'red');
            $.ajax({
                url: '/devolucion/actualizaDevolucion/',
                type: 'post',
                async: false,
                dataType: 'json',
                data: {
                    'idordenventa': $('#idtxtOV').val(),
                    'iddevolucion': $('#txtNDevolucion').val(), 
                    'idProducto': idProducto, 
                    'cantidad': cant
                },
                success: function (resp) {
                    if (resp['msj'] != 'Aprobado') {
                        alert(resp['msj']);
                    }
                    detalleDevolucionxOV($('#idtxtOV').val());
                },
                error: function (error) {
                    console.log(error)
                }
            });
        } else {
            padre.find('.modificar').focus();
            $(this).attr('class', 'save');
        }
    });
    
    $('.editarPrecio').live('click', function (e) {
        e.preventDefault();
        $('.precioDevolucion').attr('readonly', 'readonly');
        $('.precioDevolucion').css('background-color', 'white');
        padre = $(this).parents('tr');
        padre.find('.precioDevolucion').removeAttr('readonly').css('background-color', '#d2e9fd').focus();
    });
    
    $('.grabarPrecio').live('click', function (e) {
        e.preventDefault();
        $(this).addClass('inhabilitar_elemento');        
        if ($('#txtNDevolucion').val().length == 0) {
            guardar();
        }        
        var padre = $(this).parents('tr');
        var elemento = $(this);
        var idProducto = padre.find('.modificar').attr('id');
        var precio = padre.find('.precioDevolucion').val();
        padre.find('.precioDevolucion').val('');
        if (precio > 0) {
            elemento.attr('readonly', 'readonly');
            elemento.css('background-color', 'red');
            $.ajax({
                url: '/devolucion/cambiaPrecioDevolucion/',
                type: 'post',
                async: false,
                dataType: 'json',
                data: {
                    'idordenventa': $('#idtxtOV').val(),
                    'iddevolucion': $('#txtNDevolucion').val(), 
                    'idProducto': idProducto, 
                    'precio': precio
                },
                success: function (resp) {
                    if (resp['msj'] != 'Aprobado') {
                        alert(resp['msj']);
                    }
                    detalleDevolucionxOV($('#idtxtOV').val());
                },
                error: function (error) {
                    console.log(error)
                }
            });
        } else {
            padre.find('.precioDevolucion').val('');
            padre.find('.precioDevolucion').focus();
            $(this).attr('class', 'grabarPrecio');
        }
    });
    
    $('#btnAprobar').click(function (e) {
        e.preventDefault();
        $(this).attr('disabled', 'disabled');
        $(this).html('Registrando...');        
        if ($('#idtxtOV').val().length > 0 && $('#txtNDevolucion').val().length > 0) {
            if ($('#idmotivodevolucion').val() == 0) {
                alert('Debe elegir un motivo de devolucion.');
                $(this).removeAttr('disabled');
                $(this).html('Registrar');
            } else {
                guardar();
                $.ajax({
                    url: '/devolucion/grabaAprobacion/',
                    type: 'post',
                    async: false,
                    dataType: 'json',
                    data: {
                        'idordenventa': $('#idtxtOV').val(),
                        'iddevolucion': $('#txtNDevolucion').val()
                    },
                    success: function (resp) {
                        if (resp['msj'] != 'Aprobado') {
                            alert(resp['msj']);
                            $('#btnAprobar').removeAttr('disabled');
                            $('#btnAprobar').html('Registrar');
                        } else {
                           window.location = '/devolucion/devolucion'; 
                        }                        
                    }, error: function(a, b, c) {
                        console.log(a);
                        console.log(b);
                        console.log(c);
                    }
                });
            }
        } else {            
            alert('La devolucion no pudo ser registrada.');   
            $(this).removeAttr('disabled');
            $(this).html('Registrar');
        }
    });
    
    $('.verdetalle').click(function (e) {
        e.preventDefault();
        var IDD = $(this).attr('id');
        $.ajax({
            url: '/devolucion/listaDetalleDevolucion',
            type: 'post',
            data: {'IDD': IDD},
            success: function (resp) {
                console.log(resp);
                $('#detalle').hide('Blind');
                $('#mensaje').html(' Devolucion  N°' + IDD);
                $('#tbldetalles tbody').html(resp);
                $('#detalle').show('Blind');
            },
            error: function (error) {
                console.log(error);
            }
        });
        $.ajax({
            url: '/devolucion/encabezadoDevolucion',
            type: 'post',
            data: {'IDD': IDD},
            success: function (resp) {
                console.log(resp);
                $('#tblEncabezado').html(resp);
            },
            error: function (error) {
                console.log(error);
            }
        });
    });
    
    $(".devEliminar").click(function (e) {
        if (!confirm('¿Esta Seguro de Eliminar la Devolucion?')) {
            e.preventDefault();
        }
    });
    
    $('#btncerrar').click(function (e) {
        $('#detalle').hide('Blind');
    });
    
     $(".devregistrada").click(function () {
        $(this).addClass('inhabilitar_elemento');
        if (!confirm('¿Esta Seguro de Confirmar esta Devolucion?')) {
            $(this).attr('class', 'devregistrada');
        } else {
            var iddevolucion = $(this).data('id');
            $.ajax({
                url: '/devolucion/grabaconfirmarPedido/',
                type: 'post',
                async: false,
                dataType: 'json',
                data: {'iddevolucion': iddevolucion },
                success: function (resp) {
                    if (resp['msj'] != 'ok') {
                        alert(resp['msj']);
                    }   
                    window.location = '/devolucion/listadevolucionesAprobadas'; 
                }, error: function(a, b, c) {
                    console.log(a);
                    console.log(b);
                    console.log(c);
                }
            });
        }
        return false;
    });
    
    $('#imprimir').click(function (e) {
        e.preventDefault();
        $('.devregistrada').show();
        $('#imprimir').hide();
        $('#btncerrar').hide();
        $('table tr td, table tr th').css('font-family', 'courier');
        $('body').css('color', 'black');
        imprSelec('detalle');
        $('#imprimir').show();
        $('#btncerrar').show();
        $('table tr td, table tr th').css('font-family', 'Calibri');
    });
    
    $('#cancelarDevolucionAprobada').click(function (e) {
        e.preventDefault();
        window.location = '/devolucion/listadevolucionesAprobadas/';
    });
    
    $('#btnLimpiar').click(function (e) {
        $('#txtClientexIdCliente').val('');
        $('#txtIdCliente').val('');
        $('#txtOrdenVentaxId').val('');
        $('#txtIdOrdenVenta').val('');
        $('#txtSituacion').val('');
        $('#txtFechaRegistroIni').val('');
        $('#txtFechaRegistroFin').val('');
        $('#txtFechaAprobadoIni').val('');
        $('#txtFechaAprobadoFin').val('');
        $('#txtModoConsulta').val('');
        $('#tblDevoluciones tbody').html('');
    });

    $('#btnImprimir').click(function (e) {
        imprSelec('impresion-contenedor');
    });
    
    $('#btnConsultar').click(function (e) {
        ValidarFiltros();
        var idcliente = $('#txtIdCliente').val();
        var idordenventa = $('#txtIdOrdenVenta').val();
        var situacion = $('#txtSituacion').val();
        var fecregini = $('#txtFechaRegistroIni').val();
        var fecregfin = $('#txtFechaRegistroFin').val();
        var fecaprini = $('#txtFechaAprobadoIni').val();
        var fecaprfin = $('#txtFechaAprobadoFin').val();
        var devtotal = $('#txtModoConsulta').val();
        $.ajax({
            url: '/devolucion/DataReporteDevoluciones/',
            type: 'post',
            data: {'idcliente': idcliente, 'idordenventa': idordenventa, 'situacion': situacion, 'fecregini': fecregini, 'fecregfin': fecregfin, 'fecaprini': fecaprini, 'fecaprfin': fecaprfin, 'devtotal': devtotal},
            success: function (resp) {
                console.log(resp)
                $('#tblDevoluciones tbody').html(resp);
            },
            error: function (error) {
                console.log(error)
            }
        });
    });
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    var nada = "";
    var idOV = $('#txtOV').val();
    var idNDevolucion = $('#txtNDevolucion').val();

    
    //txtOV
    $('#txtOV').autocomplete({
        source: "/ordenventa/ListaEstadoGuia/",
        select: function (event, ui) {
            var idOrdenVenta = ui.item.id;
        }
    });

    $(".anular").click(function (e) {/*
        if (!confirm('Se anulará la devolución ' + $(this).data('id') + '. ¿Continuar?')) {
            e.preventDefault();
        }*/
        return false;
    });

    $('#detalle').hide();

    $('#btnAceptar').on('click', function (e) {
        aceptar();
    });
    
    $('#nuevaDevolucion').on('click', function (e) {
        e.preventDefault();
        $('#txtNDevolucion').removeAttr('readonly');
        $('#txtNDevolucion').attr('value', '');
    });


    

    $('#btnCancelar').click(function (e) {
        e.preventDefault();
        window.location = '/devolucion/listadevoluciones/';
    });

    

    $("#seleccion").change(function () {
        var id = $("#seleccion option:selected").text();
        var url = '/devolucion/listarDevolucionTotal/' + id;
        window.location = url;
    });

   
    
    

    function aceptar() {
        idOV = $('#txtOV').val();
        idNDevolucion = $('#txtNDevolucion').val();
        cargaobservaciones(idNDevolucion);
        //$('#tblDetalles tbody').html('');
        $.ajax({
            url: '/devolucion/grabaDetalle/',
            type: 'post',
            dataType: 'json',
            data: {'idOV': idOV, 'idNDevolucion': idNDevolucion},
            success: function (resp) {
                var habilitado = 1;
                //console.log(resp);
                $('#tblDetalles tbody').html(resp['columna']);
                if (resp['electronico'] == 1) {
                    if (resp['editable'] == 1) {
                        $('#contPE').html('Se Detectó Factura Electronica: <b style="color: red;">' + resp['conDeco'] + '</b>');
                    } else {
                        habilitado = 0;
                        $('#cmbfactura').html(resp['facturas']);
                    }
                    $('#colFE').removeAttr('style');
                } else {
                    $('#colFE').attr('style', 'display: none');
                    $('#cmbfactura').html('');
                }
                $('#txtNDevolucion').val(idNDevolucion);
                $('#txtNDevolucion').attr('readonly', 'readonly');

                if (idNDevolucion == "") {
                    $.ajax({
                        url: '/devolucion/obtieneUltimaIdDevolucion/',
                        type: 'post',
                        data: {'idOV': idOV},
                        success: function (resp) {
                            //console.log('resp');
                            $('#txtNDevolucion').val(resp);
                        },
                        error: function (error) {
                            console.log('error');
                        }
                    });
                }
                if (habilitado == 0)
                    $("input").prop('disabled', true);
                else
                    $("input").prop('disabled', false);
            },
            error: function (a, b, c) {
                console.log(a);
                console.log(b);
                console.log(c);
            }
        });
    }  

});


function guardar() {
    if ($('#idtxtOV').val().length > 0) {
        $('#btnGuardar').html('Guardando...');
        $.ajax({
            url: '/devolucion/gestionardevolucion/',
            type: 'post',
            async: false,
            dataType: 'json',
            data: {
                'idOrdenVenta': $('#idtxtOV').val(),
                'iddevolucion': $('#txtNDevolucion').val(),
                'observaciones': $('#observaciones').val(),
                'lstmotivo': $('#idmotivodevolucion').val(),
                'lstsubmotivo': $('#idsubmotivodevolucion').val()
            }, success: function (resp) {
                if (resp['iddevolucion'] > 0) {
                    $('#txtNDevolucion').val(resp['iddevolucion']);
                    $('#blockDevolucion').show();
                    $('#btnGuardar').html('Guardar');
                } else {
                    $('#btnGuardar').html('¡Error!');
                }
            },
            error: function (a, b, c) {
                console.log(a);
                console.log(b);
                console.log(c);
            }
        });
    } else {
        $('#txtOrdenVenta').focus();
    }
}

function buscarXidOrdenventa(idOrdenVenta, banderaDev) {
    if (idOrdenVenta > 0) {
        $.ajax({
            url: '/devolucion/obtieneDatosOV/',
            type: 'post',
            dataType: 'json',
            data: {'idOrdenVenta': idOrdenVenta},
            success: function (resp) {
                $('#txtCliente').val(resp['razonsoscial']);
                $('#txtRUC').val(resp['ruc']);  
                $('#idsubmotivodevolucion').html(resp['motivos']);
                if (resp['iddevolucion'] == '') {
                    $('#txtNDevolucion').val('');
                    $('#observaciones').val('');
                    $('#idmotivodevolucion').val('');
                    $('#idsubmotivodevolucion').val('');
                    
                    $('#blockDevolucion').hide();
                    $('#btnGuardar').html('Aceptar');
                } else {
                    $('#txtNDevolucion').val(resp['iddevolucion']);
                    $('#observaciones').val(resp['observaciones']);
                    $('#idmotivodevolucion').val(resp['idmotivodevolucion']);
                    $('#idsubmotivodevolucion').val(resp['idsubmotivodevolucion']);
                    
                    $('#blockDevolucion').show();
                    $('#btnGuardar').html('Guardar');                
                }
                if (resp['tamanio'] == 0) {
                    $('#blockSubMotivo').hide();
                }else{
                    $('#blockSubMotivo').show();
                }
                if (banderaDev == 1) {
                    detalleDevolucionxOV(idOrdenVenta);
                }            
            },
            error: function (error) {
                console.log('error');
            }
        });
    }    
}

function detalleDevolucionxOV(idOrdenVenta) {
    $.ajax({
        url: '/devolucion/detalleDevolucionxOrdenVenta/',
        type: 'post',
        data: {'idOrdenVenta': idOrdenVenta},
        success: function (resp) {
            var habilitado = 1;
            $('#tblDetalles tbody').html(resp['columna']);
            if (resp['electronico'] == 1) {
                if (resp['editable'] == 1) {
                    $('#contPE').show();
                    $('#cmbfactura').hide();
                    $('#contPE').html('Se Detectó Factura Electronica: <b style="color: red;">' + resp['conDeco'] + '</b>');
                } else {
                    habilitado = 0;
                    $('#contPE').hide();
                    $('#cmbfactura').show();
                    $('#cmbfactura > select').html(resp['facturas']);
                }
                $('#colFE').removeAttr('style');
            } else {
                $('#colFE').attr('style', 'display: none');
                $('#cmbfactura > select').html('');
            }            
            if (habilitado == 0) {
                $("#tblDetalles > tbody input").prop('disabled', true);
                $("#tblDetalles > tbody .save").addClass('inhabilitar_elemento');
            } else {
                $("#tblDetalles > tbody input").prop('disabled', false);
                $("#tblDetalles > tbody .save").attr('class', 'save');
            }
        },
        error: function (error) {
            console.log(error);
        }
    });
}

function ValidarFiltros() {
    var idcliente = $('#txtClientexIdCliente').val();
    var idordenventa = $('#txtOrdenVentaxId').val();
    var situacion = $('#txtSituacion').val();
    var fecregini = $('#txtFechaRegistroIni').val();
    var fecregfin = $('#txtFechaRegistroFin').val();
    var fecaprini = $('#txtFechaAprobadoIni').val();
    var fecaprfin = $('#txtFechaAprobadoFin').val();
    var devtotal = $('#txtModoConsulta').val();
    if (idcliente == '' && idordenventa == '' && situacion == '-1' && fecregini == '' && fecregfin == '' && fecaprini == '' && fecaprfin == '') {
        alert("DEBE INGRESAR UNA CONDICION DE BUSQUEDA");
        exit;
    }
}

