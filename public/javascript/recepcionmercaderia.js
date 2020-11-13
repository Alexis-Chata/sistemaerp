$(document).ready(function () {

    cantidadEditable = "";
    contenedorMotivos = $('#contenedorMotivos');
    contenedorMotivos.hide();
    
    bloqueAgencia();
    listarMotivos();
    
    $('#contenedorMotivos').dialog({
        title: 'Motivo de Recojo',
        autoOpen: false,
        modal: true,
        resizable: true,
        width: 400,
        buttons: {
            "Añadir": function () {
                if ($('#idNombreMotivo').val().length == 0) {
                    $('#idNombreMotivo').focus();
                } else {
                    $.ajax({
                        url: '/motivorecojo/grabamotivo',
                        type: 'post',
                        dataType: 'json',
                        data: {nombremotivo: $('#idNombreMotivo').val()},
                        success: function (resp) {
                            $('#lstMotivo').data('seleccion', resp['nuevoid']);
                            listarMotivos();
                            $('#contenedorMotivos').dialog('close');
                        }
                    });
                }
            }
        }
    });
    
    $('#NuevoMotivo').click(function () {
        $('#contenedorMotivos').dialog('open');
    });    

    $('#txtagencia').autocomplete({
        source: "/atencioncliente/autocompleteagencia/",
        select: function (event, ui) {
            $('#idAgencia').val(ui.item.id);
            $('#direccionAgencia').html(ui.item.direccion);
            $('#serieguia').focus();
        }
    });

    $('#txtCliente').autocomplete({
        source: "/cliente/autocompletexordenventa/",
        select: function (event, ui) {
            if ($('#txtIdCliente').val() != ui.item.idcliente) {
                reiniciarCampos();
            }
            $('#txtIdCliente').val(ui.item.idcliente);
            $('#idCodigo').val(ui.item.codcliente);
            $('#idRazonsocial').val(ui.item.value);
            $('#idRuc').val(ui.item.rucdni);
            $('#idCelular').val(ui.item.celular);
            $('#idDireccion').val(ui.item.direccion);
            $('#idUbicacion').val(ui.item.ubigeo);
            $('#idZona').val(ui.item.zonacategoria);
            $('#idAgencia').val('');
            $('#razonsocial').val('');
            $('#direccionAgencia').html('');
            $('#serieguia').val('');
            $('#correlativoguia').val('');
            if (ui.item.idpadrec == 1) {
                $("#chkHabilitar").prop('checked', false);
                $('.blockAgencia').hide();
            } else {
                $("#chkHabilitar").prop('checked', true);
                $('.blockAgencia').show();
            }
        }
    });

    $("#txtCodigoProducto").autocomplete({
        source: function (request, response) {
            $.ajax({
                url: "/atencioncliente/autocompleteproductoxovxcliente",
                dataType: "json",
                data: {term: request.term, cliente: $('#txtIdCliente').val()},
                success: function (data) {
                    response(data);
                }
            });
        },
        minLength: 2,
        select: function (event, ui) {
            mostrarResultadosBusqueda(ui.item.id, $('#txtIdCliente').val());
        }
    });

    $('#chkHabilitar').change(function () {
        if (!$(this).prop('checked')) {
            $('.blockAgencia').hide();
        } else {
            $('.blockAgencia').show();
        }
    });

    $('#tblResultados').on('keypress', '.txtAnadirProducto', function (e) {
        if (e.keyCode == 13) {
            cantidadEditable = $('.txtAnadirProducto[data-dov="' + $(this).data('dov') + '"]');
            validarCantidad(1);
            e.preventDefault();
        }
    });

    $('#tblResultados').on('change', '.txtAnadirProducto', function () {
        cantidadEditable = $(this);
        validarCantidad(0);
    });

    $('#tblResultados').on('click', '.AnadirProducto', function () {
        cantidadEditable = $('.txtAnadirProducto[data-dov="' + $(this).data('dov') + '"]');
        validarCantidad(1);
    });

    $('#tblDetalleRecepcion').on('click', '.eliminarDRM', function () {
        $(this).parents('tr').remove();
    })

    $('#frmRecepcion').submit(function () {
        $('#Registrar').attr('disabled', 'disabled');
        if ($('#txtIdCliente').val().length == 0) {
            $('#txtCliente').focus();
            $('#Registrar').removeAttr('disabled');
            return false;
        } else if ($.trim($('#tblDetalleRecepcion tbody').html()).length == 0) {
            $('#txtCodigoProducto').focus();
            $('#Registrar').removeAttr('disabled');
            return false;
        } else if ($('#Idimporte').val() < 0 && $('#chkHabilitar').prop('checked')) {
            $('#Idimporte').focus();
            $('#Registrar').removeAttr('disabled');
            return false;
        }
    });
    
    $('#Idimporte').change(function () {
        var importe = parseFloat($(this).val());
        var descuento = parseFloat($('#IdPorcentaje').val());
        importe = importe*(descuento/100);        
        $('#IdDiferencia').val(importe.toFixed(2));
    });
    
    $('#IdPorcentaje').change(function () {
        if ($(this).val() < 0) {
            $(this).val(0);
        } else if ($(this).val() > 100) {
            $(this).val(100);
        }
        if ($('#Idimporte').val().length == 0) {
            $('#Idimporte').val(0);
        }
        var importe = parseFloat($('#Idimporte').val());
        var descuento = parseFloat($('#IdPorcentaje').val());
        importe = importe*(descuento/100);        
        $('#IdDiferencia').val(importe.toFixed(2));
        if ($(this).val() == 100) {
            $('#responsable').val(0);
            $('.DCPblock').hide();
        } else {
            $('.DCPblock').show();
        }
    });
    
    $('#tblDetalleRecepcion').on('click', '.chkGarantia', function () {
        if ($(this).prop('checked')) {
            $('#Garantia' + $(this).data('id')).val(1);
        } else {
            $('#Garantia' + $(this).data('id')).val(0);
        }
    });

});

function listarMotivos() {
    $.ajax({
        url: '/motivorecojo/listarmotivoshtml',
        type: 'post',
        datatype: 'html',
        success: function (resp) {
            $('#lstMotivo').html(resp);
            $('#lstMotivo').val($('#lstMotivo').data('seleccion'));
        }
    });
}

function bloqueAgencia() {
    if ($('#IdPorcentaje').val() == 100) {
        $('.DCPblock').hide();
    } else {
        $('.DCPblock').show();
    }
    if (!$("#chkHabilitar").prop('checked')) {
        $("#chkHabilitar").prop('checked', false);
        $('.blockAgencia').hide();
    } else {
        $("#chkHabilitar").prop('checked', true);
        $('.blockAgencia').show();
    }
}

function validarCantidad(bandera) {
    if ($(cantidadEditable).data('cantidad') < $(cantidadEditable).val() || $(cantidadEditable).val() <= 0 || $(cantidadEditable).val().length == 0) {
        $(cantidadEditable).attr('style', 'color: red;');
        $(cantidadEditable).focus();
    } else {
        $(cantidadEditable).removeAttr('style');
        var iddov = $(cantidadEditable).data('dov');
        var cantidad = $(cantidadEditable).val();
        if (bandera == 1) {
            if ($('#Cant' + iddov).val() == undefined) {
                $.ajax({
                    url: '/atencioncliente/anadirproductoxcliente',
                    type: 'post',
                    datatype: 'html',
                    data: {'iddetalleordenventa': iddov, idcliente: $('#txtIdCliente').val(), 'cantidad': cantidad},
                    success: function (resp) {
                        $('#tblDetalleRecepcion tbody').append(resp);
                    }
                });
            } else {
                $('#Cant' + iddov).val(cantidad);
            }
            $('#rbp' + $(cantidadEditable).data('dov')).remove();
        }
    }
}

function mostrarResultadosBusqueda(idproducto, idcliente) {
    $.ajax({
        url: '/atencioncliente/productosxcliente',
        type: 'post',
        datatype: 'html',
        data: {'idcliente': idcliente, 'idproducto': idproducto},
        success: function (resp) {
            $("#txtCodigoProducto").val('');
            $('#tblResultados tbody').html(resp);
        }
    });
}

function reiniciarCampos() {
    $('#txtCliente').val('');
    $('#txtIdCliente').val('');
    $('#idCodigo').val('');
    $('#idRazonsocial').val('');
    $('#idRuc').val('');
    $('#idCelular').val('');
    $('#idDireccion').val('');
    $('#idUbicacion').val('');
    $('#idZona').val('');
    $('#idAgencia').val('');
    $('#razonsocial').val('');
    $('#direccionAgencia').html('');
    $('#serieguia').val('');
    $('#correlativoguia').val('');
    $("#chkHabilitar").prop('checked', false);
    $('#fechapago').val('');
    $('#lstMotivo').val('');
    $('#txtObservaciones').val('');
    $('#Idimporte').val('');
    $('#IdDiferencia').val('');
    $('#IdPorcentaje').val('100');
    $('#responsable').val('');
    $('.DCPblock').hide();
    $('.blockAgencia').hide();
    $('#txtCodigoProducto').val('');
    $('#tblResultados tbody').html('');
    $('#tblDetalleRecepcion tbody').html('');
}