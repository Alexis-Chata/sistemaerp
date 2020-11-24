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
    
    $('#tblInformacionComercial').on('click', '.btnEliminarIC', function () {
        if (confirm('¿Esta seguro de eliminar el detalle de la informacion comercial?')) {
            var id = $(this).data('id');
            $.ajax({
                url: '/proveedornacional/informacioncomercial_eliminar',
                type: 'post',
                data: {
                    'ideliminar' :id
                }, success: function (resp) {
                }
            });
            $(this).parents('tr').remove();
        }
        return false;
    });
    
    $('#tblEvaluacionTecnica').on('click', '.btnEliminarET', function () {
        if (confirm('¿Esta seguro de eliminar el detalle de la evaluacion tecnica?')) {
            var id = $(this).data('id');
            $.ajax({
                url: '/proveedornacional/evaluaciontecnica_eliminar',
                type: 'post',
                data: {
                    'ideliminar' :id
                }, success: function (resp) {
                }
            });
            $(this).parents('tr').remove();
        }
        return false;
    });
    
    $('#tblDetalleProductoServicio').on('click', '.btnEliminarDPS', function () {
        if (confirm('¿Esta seguro de eliminar el detalle?')) {
            var id = $(this).data('id');
            $.ajax({
                url: '/proveedornacional/productoservicio_eliminar',
                type: 'post',
                data: {
                    'ideliminar' :id
                }, success: function (resp) {
                }
            });
            $(this).parents('tr').remove();
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
                    $.ajax({
                        url: '/proveedornacional/evaluaciontecnica_guardar',
                        type: 'post',
                        data: {
                            'idProveedorNacional' :$('#idProveedorNacional').val(),
                            'idTextIdET' :$('#idTextIdET').val(),
                            'idProductoET' :$('#idProductoET').val(),
                            'idEvaluadorET' :$('#idEvaluadorET').val(),
                            'idCondicionET' :$('#idCondicionET').val(),
                            'idFechaET' :$('#idFechaET').val(),
                            'idComentariosET' :$('#idComentariosET').val()
                        }, success: function (resp) {                
                            $('#tblEvaluacionTecnica tbody').html(resp);
                        }
                    });                    
                    $('#idTextIdET').val('');
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
                    $.ajax({
                        url: '/proveedornacional/informaciontecnica_guardar',
                        type: 'post',
                        data: {
                            'idProveedorNacional' :$('#idProveedorNacional').val(),
                            'idTextIdIT' :$('#idTextIdIT').val(),
                            'idCertificadoIT' :$('#idCertificadoIT').val(),
                            'idAprobacionIT' :$('#idAprobacionIT').val(),
                            'idFechaIT' :$('#idFechaIT').val(),
                            'idFechaUltimaIT' :$('#idFechaUltimaIT').val()
                        }, success: function (resp) {                
                            $('#tblInformacionTecnica tbody').html(resp);
                        }
                    });
                    $('#idTextIdIT').val('');
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
                if ($('#idPrincipalIC').val().length > 0 && $('#idProveedorNacional').val() > 0) {
                    $.ajax({
                        url: '/proveedornacional/informacioncomercial_guardar',
                        type: 'post',
                        data: {
                            'idTextIdIC' :$('#idTextIdIC').val(),
                            'idProveedorNacional' :$('#idProveedorNacional').val(),
                            'idPrincipalIC' :$('#idPrincipalIC').val(),
                            'idParticipacionIC' :$('#idParticipacionIC').val(),
                            'idAntiguedadIC' :$('#idAntiguedadIC').val()
                        }, success: function (resp) {                
                            $('#tblInformacionComercial tbody').html(resp);
                        }
                    });
                    $('#idTextIdIC').val('');
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
                if ($('#idNombreDetallePS').val().length > 0 && $('#idProveedorNacional').val() > 0) {
                    $.ajax({
                        url: '/proveedornacional/productoservicio_guardar',
                        type: 'post',
                        data: {
                            'idTextIdPS' :$('#idTextIdPS').val(),
                            'idProveedorNacional' :$('#idProveedorNacional').val(),
                            'idNombreDetallePS' :$('#idNombreDetallePS').val()
                        }, success: function (resp) {                
                            $('#tblDetalleProductoServicio tbody').html(resp);
                        }
                    });
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
    
    $('#tblInformacionComercial').on('click', '.btnEeditarIC', function () {
        $('#idPrincipalIC').val($(this).data('cliente'));
        $('#idParticipacionIC').val($(this).data('participacion'));
        $('#idAntiguedadIC').val($(this).data('antiguedad'));
        $('#idTextIdIC').val($(this).data('id'));
        $('#contenedorInformacionGeneral').dialog('open');
        return false;
    });
    
    $('#btnInformacionComercial').click(function () {
        $('#idPrincipalIC').val('');
        $('#idParticipacionIC').val('');
        $('#idAntiguedadIC').val('');
        $('#idTextIdIC').val('');
        $('#contenedorInformacionGeneral').dialog('open');
        return false;
    });

    $('#btnNuevoProductoServicio').click(function () {
        $('#contenedorProductoServicio').dialog('open');
        return false;
    });
    
    $('#tblDetalleProductoServicio').on('click', '.btnEditarDPS', function () {
        $('#idTextIdPS').val($(this).data('id'));
        $('#idNombreDetallePS').val($(this).data('nombre'));
        $('#contenedorDetalleProductoServicio').dialog('open');
        return false;
    });
    
    $('#btnNuevoDetalleProductoServicio').click(function () {
        $('#idTextIdPS').val('');
        $('#idNombreDetallePS').val('');
        $('#contenedorDetalleProductoServicio').dialog('open');
        return false;
    });
    
    $('#tblInformacionTecnica').on('click', '.btnEeditarIT', function () {
        $('#idFechaUltimaIT').val($(this).data('fultimaauditoria'));
        $('#idFechaIT').val($(this).data('fecha'));
        $('#idAprobacionIT').val($(this).data('aprobacionnro'));
        $('#idCertificadoIT').val($(this).data('certificado'));
        $('#idTextIdIT').val($(this).data('id'));
        $('#contenedorInformacionTecnica').dialog('open');
        return false;
    });
    
    $('#btnInformacionTecnica').click(function () {
        $('#idTextIdIT').val('');
        $('#idCertificadoIT').val('');
        $('#idAprobacionIT').val('');
        $('#idFechaIT').val('');
        $('#idFechaUltimaIT').val('');
        $('#contenedorInformacionTecnica').dialog('open');
        return false;
    });
    
    $('#tblEvaluacionTecnica').on('click', '.btnEditarET', function () {
        $('#idProductoET').val($(this).data('productoservicio'));
        $('#idEvaluadorET').val($(this).data('idevaluador'));
        $('#idCondicionET').val($(this).data('condicion'));
        $('#idFechaET').val($(this).data('fecha'));
        $('#idComentariosET').val($(this).data('comentarios'));
        $('#idTextIdET').val($(this).data('id'));
        $('#blockEvaluadorNuevo').hide();
        $('#idTxtNuevoEvaluador').val('');
        $('#blockEvaluadorExiste').show();
        $('#contenedorEvaluacionTecnica').dialog('open');
        return false;
    });
    
    $('#btnEvaluacionTecnica').click(function () {
        $('#idTextIdET').val('');
        $('#idProductoET').val('');
        $('#idEvaluadorET').val('');
        $('#idCondicionET').val('');
        $('#idFechaET').val('');
        $('#idComentariosET').val('');
        $('#blockEvaluadorNuevo').hide();
        $('#idTxtNuevoEvaluador').val('');
        $('#blockEvaluadorExiste').show();
        $('#contenedorEvaluacionTecnica').dialog('open');
        return false;
    });
    
});