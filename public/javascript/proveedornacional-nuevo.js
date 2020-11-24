$(document).on('ready', function () {
    
    $('#lstTipoProveedor').change(function () {
        if ($(this).val() == 1) {
            $('#lblRucDNI').html('Dni:');
        } else {
            $('#lblRucDNI').html('RUC:');
        }
        return false;
    });
    
    $('#chkContingencia').change(function () {
        if ($(this).prop('checked')) {
            $('#blockContingencia').show();
        } else {
            $('#blockContingencia').hide();
        }
    });
    
    $('.classChkDias').change(function () {
        if ($(this).prop('checked')) {
            $('#txtDiasTP').val('');
            $('.classChkDias').prop('checked', false);
            $(this).prop('checked', true);
        } else {
            $('.classChkDias').prop('checked', false);
        }
    });
    
    $('#txtDiasTP').change(function () {
        if ($(this).val().length > 0) {
            $('.classChkDias').prop('checked', false);
        }
    });
    
    $('#chkDia15TE').change(function () {
        if ($(this).prop('checked')) {
            $('#chkDiaTE').val('');
            $('#chkOtroTE').val('');
        }
    });
    
    $('#chkDiaTE').change(function () {
        if ($(this).val().length > 0) {
            if (parseInt($(this).val()) > 14) {
                $(this).val('14');
            } else if (parseInt($(this).val()) < 1) {
                $(this).val('1');
            }
            $('#chkDia15TE').prop('checked', false);
            $('#chkOtroTE').val('');
        }
    });
    
    $('#chkOtroTE').change(function () {
        if ($(this).val().length > 0) {
            $('#chkDia15TE').prop('checked', false);
            $('#chkDiaTE').val('');
        }
    });
    
    $('#btnNuevoEvaluador').click(function () {
        $('#idTxtNuevoEvaluador').val('');
        $('#blockEvaluadorExiste').hide();
        $('#blockEvaluadorNuevo').show();
        $('#idTxtNuevoEvaluador').focus();
        return false;
    });
    
    $('#btnCerrarEvaluador').click(function () {
        $('#blockEvaluadorNuevo').hide();
        $('#idTxtNuevoEvaluador').val('');
        $('#blockEvaluadorExiste').show();
        return false;
    });
    
    $('#btnGuardarEvaluador').click(function () {
        if ($('#idTxtNuevoEvaluador').val().length > 0) {
            $.ajax({
                url: '/proveedornacional/nuevoevaluador_guardar',
                data:{'txtNuevoEvaluador': $('#idTxtNuevoEvaluador').val()},
                type: 'POST',
                dataType: 'html',
                success: function (data) {
                    $('#idTxtNuevoEvaluador').val('');
                    $('#idEvaluadorET').html(data);
                    $('#blockEvaluadorNuevo').hide();
                    $('#blockEvaluadorExiste').show();
                }
            });
        } else {
            $('#idTxtNuevoEvaluador').focus();
        }
        return false;
    });
    
    
    $('#tblEvaluacionTecnica').on('click', '.btnEliminarET', function () {
        $(this).parents('tr').remove();
        return false;
    });
    
    $('#tblDetalleProductoServicio').on('click', '.btnEliminarDPS', function () {
        $(this).parents('tr').remove();
        return false;
    });
    
    $('#tblInformacionComercial').on('click', '.btnEliminarIC', function () {
        $(this).parents('tr').remove();
        return false;
    });
    
    $('#tblInformacionTecnica').on('click', '.btnEliminarIT', function () {
        $(this).parents('tr').remove();
        var numero = 0;
        $('.classCertificadoIT').each(function () {
            numero ++;
        });
        if (numero == 0) {
            $('#tblInformacionTecnica tbody').html('<tr><td colspan="5">NO PRESENTA</td></tr>');
        }
        return false;
    });
    
    $('#contenedorEvaluacionTecnica').dialog({
        autoOpen: false,
        modal: true,
        width: 400,
        buttons: {
            Cerrar: function () {
                $('#contenedorEvaluacionTecnica').dialog('close');
            },
            Guardar: function () {
                if ($('#idProductoET').val().length > 0) {
                    var bandera = 0;
                    $('.classProductoET').each(function () {
                        var padre = $(this).parents('tr');
                        if ($(this).html() == $('#idProductoET').val() && padre.find('.classEvaluadorET').html() == $('#idEvaluadorET').val() && padre.find('.classCondicionET').html() == $('#idCondicionET').val() && padre.find('.classFechaET').html() == $('#idFechaET').val()) {
                            bandera = 1;
                        }
                    });
                    if (bandera == 0) {
                        var fila = '<tr>' +
                                        '<td class="classProductoET">' + $('#idProductoET').val() + '</td>' +
                                        '<td class="classEvaluadorET">' + $("#idEvaluadorET option:selected").text() + '</td>' +
                                        '<td class="classCondicionET">' + $("#idCondicionET option:selected").text() + '</td>' +
                                        '<td class="classFechaET">' + $('#idFechaET').val() + '</td>' +
                                        '<td class="classComentariosET">' + $('#idComentariosET').val() + '</td>' +
                                        '<td>' + 
                                            '<input type="hidden" name="txtProductoET[]" value="' + $('#idProductoET').val() + '">' + 
                                            '<input type="hidden" name="txtEvaluadorET[]" value="' + $('#idEvaluadorET').val() + '">' + 
                                            '<input type="hidden" name="txtCondicionET[]" value="' + $('#idCondicionET').val() + '">' + 
                                            '<input type="hidden" name="txtFechaET[]" value="' + $('#idFechaET').val() + '">' + 
                                            '<input type="hidden" name="txtComentariosET[]" value="' + $('#idComentariosET').val() + '">' + 
                                            '<a href="#" class="btnEliminarET"><img src=\"/imagenes/eliminar.gif\"></a></td>' +
                                   '</tr>';
                        $('#tblEvaluacionTecnica tbody').append(fila);
                    }
                    $('#idProductoET').val('');
                    $('#idEvaluadorET').val('');
                    $('#idCondicionET').val('');
                    $('#idFechaET').val('');
                    $('#idComentariosET').val('');
                    $('#contenedorEvaluacionTecnica').dialog('close');
                } else {
                     $('#idProductoET').focus();
                }
            }
        }, close: function () {}
    });
    
    $('#contenedorInformacionTecnica').dialog({
        autoOpen: false,
        modal: true,
        width: 330,
        buttons: {
            Cerrar: function () {
                $('#contenedorInformacionTecnica').dialog('close');
            },
            Guardar: function () {
                if ($('#idCertificadoIT').val().length > 0) {
                    var bandera = 0, numero = 0;
                    $('.classCertificadoIT').each(function () {
                        numero++;
                        var padre = $(this).parents('tr');
                        if ($(this).html() == $('#idCertificadoIT').val() && padre.find('.classAprobacionIT').html() == $('#idAprobacionIT').val() && padre.find('.classFechaIT').html() == $('#idFechaIT').val() && padre.find('.classFechaUltimaIT').html() == $('#idFechaUltimaIT').val()) {
                            bandera = 1;
                        }
                    });
                    if (numero == 0) {
                        $('#tblInformacionTecnica tbody').html('');
                    }
                    if (bandera == 0) {
                        var fila = '<tr>' +
                                        '<td class="classCertificadoIT">' + $('#idCertificadoIT').val() + '</td>' +
                                        '<td class="classAprobacionIT">' + $('#idAprobacionIT').val() + '</td>' +
                                        '<td class="classFechaIT">' + $('#idFechaIT').val() + '</td>' +
                                        '<td class="classFechaUltimaIT">' + $('#idFechaUltimaIT').val() + '</td>' +
                                        '<td>' + 
                                            '<input type="hidden" name="txtCertificadoIT[]" value="' + $('#idCertificadoIT').val() + '">' + 
                                            '<input type="hidden" name="txtAprobacionIT[]" value="' + $('#idAprobacionIT').val() + '">' + 
                                            '<input type="hidden" name="txtFechaIT[]" value="' + $('#idFechaIT').val() + '">' + 
                                            '<input type="hidden" name="txtFechaUltimaIT[]" value="' + $('#idFechaUltimaIT').val() + '">' + 
                                            '<a href="#" class="btnEliminarIT"><img src=\"/imagenes/eliminar.gif\"></a></td>' +
                                   '</tr>';
                        $('#tblInformacionTecnica tbody').append(fila);
                    }
                    $('#idCertificadoIT').val('');
                    $('#idAprobacionIT').val('');
                    $('#idFechaIT').val('');
                    $('#idFechaUltimaIT').val('');
                    $('#contenedorInformacionTecnica').dialog('close');
                } else {
                     $('#idPrincipalIC').focus();
                }
            }
        }, close: function () {}
    });
    
    $('#contenedorInformacionGeneral').dialog({
        autoOpen: false,
        modal: true,
        width: 330,
        buttons: {
            Cerrar: function () {
                $('#contenedorInformacionGeneral').dialog('close');
            },
            Guardar: function () {
                if ($('#idPrincipalIC').val().length > 0) {
                    var bandera = 0;
                    $('.classPrincipalIC').each(function () {
                        var padre = $(this).parents('tr');
                        if ($(this).html() == $('#idPrincipalIC').val() && padre.find('.classParticipacionIC').html() == $('#idParticipacionIC').val() && padre.find('.classAntiguedadIC').html() == $('#idAntiguedadIC').val()) {
                            bandera = 1;
                        }
                    });
                    if (bandera == 0) {
                        var fila = '<tr>' +
                                        '<td class="classPrincipalIC">' + $('#idPrincipalIC').val() + '</td>' +
                                        '<td class="classParticipacionIC">' + $('#idParticipacionIC').val() + '</td>' +
                                        '<td class="classAntiguedadIC">' + $('#idAntiguedadIC').val() + '</td>' +
                                        '<td>' + 
                                            '<input type="hidden" name="txtPrincipalesIC[]" value="' + $('#idPrincipalIC').val() + '">' + 
                                            '<input type="hidden" name="txtParticipacionIC[]" value="' + $('#idParticipacionIC').val() + '">' + 
                                            '<input type="hidden" name="txtAntiguedadIC[]" value="' + $('#idAntiguedadIC').val() + '">' + 
                                            '<a href="#" class="btnEliminarIC"><img src=\"/imagenes/eliminar.gif\"></a></td>' +
                                   '</tr>';
                        $('#tblInformacionComercial tbody').append(fila);
                    }
                    $('#idPrincipalIC').val('');
                    $('#idParticipacionIC').val('');
                    $('#idAntiguedadIC').val('');
                    $('#contenedorInformacionGeneral').dialog('close');
                } else {
                     $('#idPrincipalIC').focus();
                }
            }
        }, close: function () {}
    });
    
    $('#contenedorDetalleProductoServicio').dialog({
        autoOpen: false,
        modal: true,
        width: 670,
        buttons: {
            Cerrar: function () {
                $('#contenedorDetalleProductoServicio').dialog('close');
            },
            Guardar: function () {
                if ($('#idNombreDetallePS').val().length > 0) {
                    var bandera = 0;
                    $('.classNombreDetallePS').each(function () {
                        if ($(this).html() == $('#idNombreDetallePS').val()) {
                            bandera = 1;
                        }
                    });
                    if (bandera == 0) {
                        var fila = '<tr>' +
                                        '<td class="classNombreDetallePS">' + $('#idNombreDetallePS').val() + '</td>' +
                                        '<td><input type="hidden" name="txtNombreDPS[]" value="' + $('#idNombreDetallePS').val() + '"><a href="#" class="btnEliminarDPS"><img src=\"/imagenes/eliminar.gif\"></a></td>' +
                                   '</tr>';
                        $('#tblDetalleProductoServicio tbody').append(fila);
                    }
                    $('#idNombreDetallePS').val('');
                    $('#contenedorDetalleProductoServicio').dialog('close');
                } else {
                     $('#idNombreDetallePS').focus();
                }
            }
        }, close: function () {}
    });
    
    $('#contenedorProductoServicio').dialog({
        autoOpen: false,
        modal: true,
        width: 300,
        buttons: {
            Cerrar: function () {
                $('#contenedorProductoServicio').dialog('close');
            },
            Guardar: function () {
                if ($('#idNombrePS').val().length > 0) {
                    $.ajax({
                        url: '/proveedornacional/guardar_productoservicio',
                        data:{'idNombrePS': $('#idNombrePS').val()},
                        type: 'POST',
                        dataType: 'html',
                        success: function (data) {
                            $('#idNombrePS').val('');
                            $('#lstPrductoservicio').html(data);
                            $('#contenedorProductoServicio').dialog('close');
                        }
                    });
                } else {
                     $('#idNombrePS').focus();
                }
            }
        }, close: function () {}
    });
    
    $('#btnInformacionComercial').click(function () {
        $('#contenedorInformacionGeneral').dialog('open');
        return false;
    });

    $('#btnNuevoProductoServicio').click(function () {
        $('#contenedorProductoServicio').dialog('open');
        return false;
    });
    
    $('#btnNuevoDetalleProductoServicio').click(function () {
        $('#contenedorDetalleProductoServicio').dialog('open');
        return false;
    });
    
    $('#btnInformacionTecnica').click(function () {
        $('#contenedorInformacionTecnica').dialog('open');
        return false;
    });
    
    $('#btnEvaluacionTecnica').click(function () {
        $('#blockEvaluadorNuevo').hide();
        $('#idTxtNuevoEvaluador').val('');
        $('#blockEvaluadorExiste').show();
        $('#contenedorEvaluacionTecnica').dialog('open');
        return false;
    });
    
});