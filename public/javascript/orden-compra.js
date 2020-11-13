$(document).ready(function () {

    $('#btnCambiarProveedor').hide();
    TipoCambio_Actual(2);

    $('#blockColaboradores').on('click', '.EliminarColaborador', function () {
        $(this).remove();
    });
    
    $('.classConsultaPDF').click(function () {
        $('#ididOrdenCompraDirecto').val($(this).data('id'));
        $('#idPDfOcconsulta').submit();
        return false;
    });
    
    $('#txtColaborador').keypress(function () {
        if ($(this).data('estado') == 1) {
            $(this).val('');
            $('#txtColaborador').data('estado', 0);
        }        
    });
    
    $('#txtColaborador').click(function () {
        if ($(this).data('estado') == 1) {
            $(this).val('');
            $('#txtColaborador').data('estado', 0);
        }
    });
    
    $('#txtColaborador').focusout(function () {
        if ($(this).data('estado') == 1) {
            $(this).val('');
            $('#txtColaborador').data('estado', 0);
        }        
    });
    
    $('#txtColaborador').autocomplete({
        source: "/vendedor/autocompletevendedor/",
        select: function (event, ui) {
            if ($('#idColaborado_' + ui.item.id).val() == undefined && $('#idjefelinea').val()!=ui.item.id) {
                var cotenidohtml = '<li class="EliminarColaborador">' +
                                        '<input type="hidden" value="' + ui.item.id + '" id="idColaborado_' + ui.item.id + '" name="idcolaborades[]">' +
                                        '<input type="text" title="Eliminar Colaborador" value="' + ui.item.label + '" size="25" class="inputBorder" readonly="">' +
                                   '</li>';
                $('#blockColaboradores').append(cotenidohtml);
            }
            $('#txtColaborador').data('estado', 1);
        }
    });
    
    $('#txtVendedor').autocomplete({
        source: "/vendedor/autocompletejefedelinea/",
        select: function (event, ui) {
            if ($('#idColaborado_' + ui.item.id).val() == undefined) {
                $('#idjefelinea').val(ui.item.id);
                $('#txtVendedor').attr('disabled', 'disabled');
                $('#btnCambiarJefe').show();
            }            
        }
    });
    
    $('#imprimir').click(function (e) {
        e.preventDefault();
        imprSelec('blockOrdendecompra');
    });
    
    $('#btnCambiarJefe').click(function () {
        $('#txtVendedor').val('');
        $('#idjefelinea').val('');
        $('#txtVendedor').removeAttr('disabled');
        $('#txtVendedor').focus();
        $('#btnCambiarJefe').hide();
        return false;
    });

    $('#cmbMoneda').change(function () {
        var idMoneda = $(this).val();
        if (idMoneda > 1) {
            TipoCambio_Actual(idMoneda);
        } else {
            $('#valorTipoCambio').val("1.00");
            $('#lblVenta').addClass('ocultar');
            $('#valorTipoCambio').hide();
        }
    });

    var msgboxTitle = 'Mantenimiento de Orden De Compra';
    $('#frmConfirmarOrden').hide();
    $('body').on('change', '.chkVerificacion', function () {
        if ($(this).is(':checked')) {
            $cantidadSolicitada = $(this).parents('tr').find('.txtCantidadSolicitada').val();
            $(this).parents('tr').find('.txtCantidadRecibida').attr('readonly', true).val($cantidadSolicitada);
        } else {
            $(this).parents('tr').find('.txtCantidadRecibida').removeAttr('readonly').focus();
        }
    });

    $('#lstOrdenCompra').change(function () {
        cargaDetalleOrdenCompra();
        //esto agrege
        $('.body:gt(0)').show();
        $('.show').attr('title', 'Ocultar').html('-');
    });
    $("#seleccion").change(function () {
        var id = $("#seleccion option:selected").text();
        var url = '/importaciones/ordencompra/' + id;
        window.location = url;
    });
    
    //Generar la orden de compra
    $('#frmOrdenCompraNuevo').submit(function () {
        if (!existeDetalleOrdenCompra()) {
            return false;
        } else if (!$('input.required').valida()) {
            return false;
        } else {
            $('#lstEmpresa, #lstProveedor').removeAttr('disabled');
            return true;
        }
    });

    //Registrar en el sistema la orden de compra (como movimiento).
    $('#frmRegistroOrdenCompra').validate({
        invalidHandler: function (form, validator) {
            var errors = validator.numberOfInvalids();
            if (errors) {
                $.msgbox(msgboxTitle, 'Ingrese todos los datos requeridos correctamente.');
            }
        }
    });

    //Agregar un producto al Detalle de la orden de compra
    $('#btnAgregarDetalleOrdenCompra').click(function (e) {
        e.preventDefault();
        var ruta = "/producto/buscarordencompra/" + $('#txtIdProducto').val();
        var cantidad = $('#txtCantidadProducto').val();
        if (!$('#txtCodigoProductoCompras, #txtCantidadProducto').valida()) {
            return false;
        } else if (!existeProductoCompras("Orden de Compra")) {
            return false;
        } else if (!buscaProductoDetalleOrdenCompra()) {
            return false;
        } else {
            $('#tblDetalleOrdenCompra').show();
            agregaDetalleOrdenCompra(ruta, cantidad);
            var contador = parseInt($('#contador').val()) + 1;
            $('#contador').attr('value', contador);
            $('#txtCodigoProductoCompras').attr('value', '').focus();
            $('#txtCantidadProducto').attr('value', '');
            $('#txtTituloProducto').val('');
        }
    });

    /********************************* Ventana Modal para Proveedor *********************************/
    var razsocProveedor = $('#razsocProveedor'),
            repreProveedor = $('#repreProveedor'),
            percontactoProveedor = $('#percontactoProveedor'),
            direccionProveedor = $('#direccionProveedor'),
            descripcionProveedor = $('#descripcionProveedor'),
            rucProveedor = $('#rucProveedor'),
            emailPrincipalProveedor = $('#emailPrincipalProveedor'),
            emailAltenativoProveedor = $('#emailAltenativoProveedor'),
            paginaProveedor = $('#paginaProveedor'),
            telefonoprincipalProveedor = $('#telefonoprincipalProveedor'),
            telefonoalternativoProveedor = $('#telefonoalternativoProveedor'),
            faxProveedor = $('#faxProveedor'),
            allProveedor = $([]).add(razsocProveedor).add(repreProveedor).add(percontactoProveedor).add(direccionProveedor)
            .add(descripcionProveedor).add(rucProveedor).add(emailPrincipalProveedor).add(emailAltenativoProveedor)
            .add(paginaProveedor).add(telefonoprincipalProveedor).add(telefonoalternativoProveedor).add(faxProveedor),
            tips = $(".validateTips");

    $('#nuevoProveedorModal').dialog({
        autoOpen: false,
        modal: true,
        width: 440,
        height: 'auto',
        buttons: {
            Enviar: function () {
                var bValid = true;
                allProveedor.removeClass("ui-state-error");

                bValid = bValid && checkLength(razsocProveedor, "Razon Social", 3, 100);
                bValid = bValid && checkLength(repreProveedor, "Representante Legal", 0, 200);
                bValid = bValid && checkLength(percontactoProveedor, "Persona Contacto", 0, 200);
                bValid = bValid && checkLength(direccionProveedor, "Direccion", 0, 100);
                bValid = bValid && checkLength(descripcionProveedor, "Descripcion", 0, 150);

                bValid = bValid && checkLength(rucProveedor, "RUC", 0, 11);
                bValid = bValid && checkRegexp(rucProveedor, /^([0-9])+$/, "Solo Puede ingresar digitos.");

                bValid = bValid && checkLength(emailPrincipalProveedor, "E-mail Principal", 0, 100);
                bValid = bValid && checkRegexp(emailPrincipalProveedor, /^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i, "Formato Incorrecto del Correo");

                bValid = bValid && checkLength(emailAltenativoProveedor, "E.mail Alternativo", 0, 200);
                bValid = bValid && checkRegexp(emailAltenativoProveedor, /^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i, "Formato Incorrecto del Correo");

                bValid = bValid && checkLength(paginaProveedor, "Pagina Web", 0, 200);
                bValid = bValid && checkLength(telefonoprincipalProveedor, "Telefono Principal", 0, 11);
                bValid = bValid && checkLength(telefonoalternativoProveedor, "Telefono Alternativo", 0, 11);
                bValid = bValid && checkLength(faxProveedor, "Fax", 0, 12);

                if (bValid) {
                    $.ajax({
                        url: '/proveedor/grabaJason/',
                        type: 'POST',
                        dataType: 'json',
                        data: $('#frmProveedor').serialize(),
                        success: function (respuestaProveedor) {

                            if (respuestaProveedor.valid) {

                                $('#respProveedor').text(respuestaProveedor.resp);
                                $('#respProveedor').css({'color': 'green'});
                                $('#lstProveedor').append("<option value=" + respuestaProveedor.idProveedor + ">" + razsocProveedor.val() + "</option>");
                                allProveedor.val("");

                            } else {
                                $('#respProveedor').text(respuestaProveedor.resp);
                                $('#respProveedor').css({'color': 'red'});
                            }
                        },
                        error: function (jqXHR, status, error) {
                            $('#respProveedor').text("Error al intentar enviar peticion");

                        }
                    });

                }
            },
            Cancelar: function () {
                allProveedor.val("").removeClass("ui-state-error");
                tips.text("Todos los campos son requeridos.");
                $('#respProveedor').text("");
                $(this).dialog("close");
            }
        },
        close: function () {
            allProveedor.val("").removeClass("ui-state-error");
            tips.text("Todos los campos son requeridos.");
            $('#respProveedor').text("");
        }
    });

    $('#agregarProveedor').button().click(function () {
        $('#nuevoProveedorModal').dialog("open");
    });

    /********************************* Validacion General *******************************************/


    function updateTips(t) {
        tips
                .text(t)
                .addClass("ui-state-highlight");
        setTimeout(function () {
            tips.removeClass("ui-state-highlight", 1500);
        }, 500);
    }

    function checkLength(o, n, min, max) {
        if (o.val().length > max) {
            o.addClass("ui-state-error");
            updateTips("El Maximo Tamaño de " + n + " es: " + max + ".");
            return false;
        } else if (o.val().length < min) {
            o.addClass("ui-state-error");
            updateTips("El Minimo Tamaño de " + n + " es: " + min + ".");
            return false;
        } else {
            return true;
        }
    }

    function checklista(lista, nombre) {
        if (lista.val() == 0) {
            lista.addClass("ui-state-error");
            updateTips("No ha seleccionado ningun " + nombre);
            return false;
        } else {
            return true;
        }
    }

    function checkRegexp(o, regexp, n) {
        if (!(regexp.test(o.val()))) {
            o.addClass("ui-state-error");
            updateTips(n);
            return false;
        } else {
            return true;
        }
    }

    /*******/
    /*
    $('#btnRedirecProducto').click(function (e) {
        e.preventDefault();
        window.location = '/producto/nuevo/';
    });*/
    /*******/
//Actualizacion de orden de compra
    $('#frmOrdenCompraActualiza').submit(function () {
        if (!existeDetalleOrdenCompra()) {
            return false;
        } else  {
            $('#txtCodigoProductoCompras, #txtCantidadProducto').removeClass('required');
            if (!$('input.required').valida()) {
                return false;
            } else {
                $('#lstEmpresa').attr("disabled", false);
                return true;
            }
        }
    });

    //Quitar un producto del detalle de orden de compra
    $('body').on('click', '.btnEliminarDetalleOrdenCompra', function (e) {
        e.preventDefault();
        $fila = $(this).parents('tr');
        if ($fila.find('.txtEstado').length) {
            $fila.find('.txtEstado').attr('value', '0');
            $fila.hide();
        } else {
            $fila.remove();
        }
        if ($('#tblDetalleOrdenCompra tbody tr').length == 0) {
            $('#tblDetalleOrdenCompra').addClass('hide');
        }
    });

    //Editar la cantidad del detalle de la orden de compra
    $('body').on('click', '.btnEditarDetalleOrdenCompra', function (e) {
        e.preventDefault();
        $(this).parents('tr').find('.txtCantidadDetalle').removeAttr('readonly').focus();
    });

    //Impedir que la cantidad de detalle orden compra quede vacio,cero, o mayor al stock
    //disponible en caso de se una salida.
    $('body').on('blur', '.txtCantidadDetalle', function () {
        cantidadSalida = parseInt(this.value);
        if (cantidadSalida <= 0) {
            $('.txtCantidadDetalle').focus().attr('value', '');
        } else {
            $(this).attr('readonly', true);
        }
    });
    
    $('#lstProveedor').change(function () {
        if (this.value != '') {
            $(this).attr('disabled', 'disabled');
            $('#btnCambiarProveedor').show();
        }
        var lastRow = $('#tblDetalleOrdenCompra tbody tr:last');
        $('#tblDetalleOrdenCompra tbody').html(lastRow);
        $('#tblDetalleOrdenCompra').addClass('hide');
    });

    //Desactivar los combobox empresa y proveedor
    $('#lstEmpresa').change(function () {
        if (this.value != '') {
            $(this).attr('disabled', 'disabled');
        }        
    });

    //Activar los combobox empresa y proveedor
    $('#btnCambiarEmpresa').click(function (e) {
        e.preventDefault();
        $('#lstEmpresa').removeAttr('disabled');
    });
    $('#btnCambiarProveedor').click(function (e) {
        e.preventDefault();
        $(this).hide();
        $('#lstProveedor').removeAttr('disabled');
    });

    //Verificar si se selecciono correctamente proveedor y empresa
    $('#txtCodigoProductoCompras').focus(function () {
        if ($('#lstEmpresa option:selected').val() == '' || $('#lstProveedor option:selected').val() == '') {
            $.msgbox(msgboxTitle, "Seleccione correctamente la Empresa y Proveedor");
        }
    });

    //Calcular total
    $('body').on('blur', '.txtFob', function () {
        if (this.value > 0) {
            $row = $(this).parents('tr');
            total = parseFloat($row.find('.txtCantidadDetalle').val()) * parseFloat($row.find('.txtFob').val());
            $row.find('.txtTotal').val(total);
            $row.find('.txtPiesas').focus();
        }
    });
    //Calcular carton
    $('body').on('blur', '.txtPiesas', function () {
        if (this.value > 0) {
            $row = $(this).parents('tr');
            total = parseFloat($row.find('.txtCantidadDetalle').val()) / parseFloat($row.find('.txtPiesas').val());
            $row.find('.txtCarton').val(total);
            $row.find('.txtVolumen').focus();
        }
    });
    $('body').on('blur', '.txtVolumen', function () {
        if (this.value > 0) {
            $row = $(this).parents('tr');
            total = parseFloat($row.find('.txtCarton').val()) * parseFloat($row.find('.txtVolumen').val());
            $row.find('.txtCbm').val(total);
        }
    });

    //Mostrar el detalle de la orden de compra
    $('.btnVerDetalleMovimiento').click(function (e) {
        e.preventDefault();
        $('#tblOrdenCompra tr').removeClass();
        $(this).parents('tr').addClass('active-row');
        var ruta = $(this).attr('href');
        $('#tblMostrarEncabezado').show();
        $('#tblDetalleOrdenCompra').show();
        $('#imprimir').show();
        mostrarDetalleOrdenCompra(ruta);
    });
    /*Confirmacion de la orden*/

    $('.btnConfirmaOrden').click(function (e) {
        var frmConfirmarOrden = "";
        e.preventDefault();
        $('#txtIdOrden').val($(this).attr('href'));
        $.msgbox(msgboxTitle1, '<div id="confirmaOrden"></div>', '<a href="#" id="btnConfirmarOrden">Confirmar</a>');
        $('#frmConfirmarOrden').show().appendTo('#confirmaOrden');
        $('#msgbox-cancel, #msgbox-close').click(function (e) {
            e.preventDefault();
            frmConfirmarOrden.hide().appendTo('#contenido');
        });
        execute();
        $('#btnConfirmarOrden').focus();
        frmConfirmarOrden = $('#frmConfirmarOrden');
    });

    $('body').on('click', '#btnConfirmarOrden', function (e) {
        e.preventDefault();
        $('#frmConfirmarOrden').submit();
    });

    $('#frmConfirmarOrden').validate();
    /*Cancelar orden de compra y modificacion*/
    $('#btnCancelar').click(function (e) {
        e.preventDefault();
        window.location = '/importaciones/ordencompra';
    });

    /*Cancelar registro de orden compra*/
    $('#btnCancelarRegistro').click(function (e) {
        e.preventDefault();
        window.location = '/almacen/movstock';
    });
    /*Cambio de datos*/
    $('body').on('blur', '.txtCantidadDetalle', function () {
        var parentElement = $(this).parents('tr');
        calculaTotal(parentElement);
    });
    $('body').on('blur', '.txtFob', function () {
        var parentElement = $(this).parents('tr');
        parentElement.find('.txtPiezas').focus();
        calculaTotal(parentElement);
    });
    $('body').on('blur', '.txtPiezas', function () {
        var parentElement = $(this).parents('tr');
        parentElement.find('.txtVolumen').focus();
        calculaTotal(parentElement);
    });
    $('body').on('blur', '.txtVolumen', function () {
        var parentElement = $(this).parents('tr');
        calculaTotal(parentElement);
    });

    /*Boton de eliminacion*/
    $('.btnEliminar').on('click', function (e) {
        e.preventDefault();
        var url = $(this).attr('href');
        $.msgbox(msgboxTitle, '¿Esta seguro de elimiar el registro?');
        $('#msgbox-ok').click(function () {
            window.location = url;
        });
    });
});

msgboxTitle1 = "Orden de Compra";
//Comprobar si existe una orden de compra
function existeOrdenCompra() {
    var x;
    $.ajax({
        async: false,
        url: "/ordencompra/contarnoregistrado/",
        dataType: "json",
        success: function (data) {
            x = data.cantidad;
        }
    });
    if (x == 0) {
        $.msgbox('Registro de Orden de Compra', 'No existe ninguna orden de compra para registrar');
        return false;
    } else {
        return true;
    }
}
//Cargar el detalle de orden de compra
function cargaDetalleOrdenCompra() {
    idOrdenCompra = $('#lstOrdenCompra option:selected').val();
    $page = "/detalleordencompra/listtable/" + idOrdenCompra;
    if (idOrdenCompra != "") {
        $.post($page, function (data) {
            $('#tblDetalleOrdenCompra tbody').html(data);
        });
    }
}

//Restringir que no se ingresen dos veces un mismo producto al detalle de la orden de compra
function buscaProductoDetalleOrdenCompra() {
    var codigoProducto = $.trim($('#txtCodigoProductoCompras').val());
    var existe = $('#tblDetalleOrdenCompra .codigo:eq("' + codigoProducto + '")').length;
    if (existe > 0) {
        $.msgbox('Orden Compra', 'El producto <strong>' + codigoProducto + '</strong> ya esta agregado en el<br>detalle de la orden de compra.');
        $('#msgbox-ok, #msgbox-cancel, #msgbox-close').click(function () {
            $('#txtCodigoProductoCompras').val('').focus();
        });
        return false;
    } else {
        return true;
    }
}

//Carga el detalle de la orden de compra
function agregaDetalleOrdenCompra(ruta, cantidad) {

    var contador = $('#contador').val();
    var rutaImagen = $('#txtRutaImagen').val();
    $.getJSON(ruta, function (data) {
        if (data.foto == '') {
            rutacompleta = '/public/imagenes/sinFoto.jpg';
        } else {
            rutacompleta = "/imagenes/productos/" + data.codigo + '/' + data.foto;
        }
        $('#tblDetalleOrdenCompra tbody tr:last').before('<tr>' +
                '<td>' +
                '<input type="hidden" name="Detalleordencompra[' + contador + '][idproducto]" value="' + data.idproducto + '">' +
                '<img src="' + rutacompleta + '" width="50" height="40">' +
                '</td>' +
                '<td class="codigo">' + data.codigo + '</td>' +
                '<td class="center">' + data.nompro + '</td>' +
                '<td class="center">' + data.marca + '</td>' +
                '<td class="center">' + data.nomemp + '</td>' +
                '<td><input type="text" name="Detalleordencompra[' + contador + '][cantidadsolicitadaoc]" value="' + cantidad + '" class="txtCantidadDetalle required numeric" readonly style="width:40px"></td>' +
                '<td>' + data.nomum + '</td>' +
                '<td><input type="text" name="Detalleordencompra[' + contador + '][fobdoc]" class="txtFob required text-50 numeric"></td>' +
                '<td class="right"><input type="text" style="width:80px" class="txtTotal required" readonly></td>' +
                '<td><input type="text" name="Detalleordencompra[' + contador + '][piezas]" class="txtPiezas  text-50 numeric"></td>' +
                '<td><input type="text" name="Detalleordencompra[' + contador + '][carton]" class="txtCarton  text-50" readonly></td>' +
                '<td><a href="#" class="btnEditarDetalleOrdenCompra"><img src="/imagenes/editar.gif"></a></td>' +
                '<td><a href="#" class="btnEliminarDetalleOrdenCompra"><img src="/imagenes/eliminar.gif"></a></td>' +
                '</tr>');
        $('#tblDetalleOrdenCompra').removeClass('hide');
        //esto aumente
        $('.body:gt(0)').show();
        $('.show').attr('title', 'Ocultar').html('-');
    });
}

//Verificar si existe al menos un producto en el detalle de la orden de compra antes de registrar
function existeDetalleOrdenCompra() {
    if ($('#idjefelinea').val().length == 0) {
        $('#txtVendedor').focus();
        return false;
    } else if ($('#tblDetalleOrdenCompra >tbody >tr').length == 0) {
        $.msgbox('Orden Compra', 'No existe ningun detalle orden compra para registrar');
        execute();
        return false;
    } else {
        $('#txtCodigoProductoCompras, #txtCantidadProducto').removeClass('required');
        return true;
    }
}

//Mostrar detalle de la orden de compra
function mostrarDetalleOrdenCompra(ruta) {
    $.ajax({
        async: false,
        url: ruta,
        dataType: "json",
        success: function (data) {
            $('#tblMostrarEncabezado thead').html(data.tblEncabezado);
            $('#tblDetalleOrdenCompra tbody').html(data.tblDetalleOrdenCompra).parent().show();
        }
    });
}
/*Confirmar la orden*/
/*function confirmarOrden(){
 var data = $('#frmConfirmarOrden').serialize();
 var url = "/ordencompra/confirmar/";
 $.post(url,data,function())
 }*/

function calculaTotal(elementParent) {
    var cantidad = parseInt(elementParent.find('.txtCantidadDetalle').val());
    var fob = elementParent.find('.txtFob').val();
    var piezas = elementParent.find('.txtPiezas').val();
    var volumen = (elementParent.find('.txtVolumen').val());
    var total = (cantidad != '' && fob != '') ? (cantidad * fob).toFixed(2) : '';
    var carton = (piezas != '') ? (parseInt(cantidad) / parseFloat(piezas)) : '';
    var cbm = (volumen != '' && carton != '') ? (parseFloat(volumen) * parseFloat(carton)) : '';
    elementParent.find('.txtTotal').val(total);    
    //elementParent.find('.txtCbm').val(cbm);
    var montoTotal = 0;
    //var totalCbm = 0;
    $('.txtTotal').each(function () {
        montoTotal += ($(this).val() != '') ? (parseFloat($(this).val().replace(',', '.'))) : 0;

    });/*
    $('.txtCbm').each(function () {
        totalCbm += ($(this).val() != '') ? (parseFloat($(this).val())) : 0;
    });*/
    elementParent.find('.txtCarton').val(carton);
    $('#txtMontoTotal').val(montoTotal.toFixed(2));
    var tipocambiovalor = parseFloat($('#valorTipoCambio').val());
    $('#txtMontoTotalSoles').val(montoTotal.toFixed(2) * tipocambiovalor);
    //$('#txtTotalCbm').val(totalCbm.toFixed(2));
}

function TipoCambio_Actual(idMoneda) {
    var url = "/tipocambio/consulta/" + idMoneda;
    $.getJSON(url, function (data) {
        $('#valorTipoCambio').val(data.venta);
        $('#lblVenta').removeClass('ocultar');
        $('#valorTipoCambio').show();
        $('#valorTipoCambio').focus();
    });
}
